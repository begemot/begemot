<?php

/**
 *  Базовый класс для сбора данных с других сайтов.
 *
 * Основное преимущество в том, что парсит не все и сразу, а делит процесс на этапы.
 * Обычные парсеры на больших сайтах могут отъедать ресурсы, подвешивать сервер и
 * могут не успеть выполниться за установленный в php параметр выполнения
 * времени скрипта. Основной упор на полный контроль и мониторинг процесса парсинга.
 * Удобство и скорость создания новых парсеров для сайта с инеграцией в админку - цель.
 *
 * Задача оптимального расходования ресурсов сервера и временных ресурсов не цель данной библиотеки.
 *
 * Этот класс парсит нужное количество страниц за один запуск. Что позволяет тонко настраивать и контролировать процесс
 * сбора информации. Пишется сценарий сбора информации и класс запускается до тех пор, пока процесс не завершиться.
 *
 * Основные цели написания библиотеки:
 * - устойчивость процесса
 * - не убивать сервер одним долгим и ресурсоемким процессом сбора, в случае большого количества страниц
 * - простота создания новых парсеров данных, путем написания сценариев
 * - управление. Возможность поставить на паузу или вернуться к процессу через какое-то количество
 * времени
 *
 *
 * Работа по сценарию распределена на два этапа. Навигация и сбор данных
 *
 * Навигация - это сбор ссылок на страницы которые надо будет обработать на
 * следующем запуске парсера. В сценарии указывается в какой части страницы могут
 * быть ссылки для определенных сценариев. Эти ссылки собираются в список
 * и генерируются новые задачи в план выполнения с пометкой по какому сценарию надо
 * их обработать.
 *
 */

Yii::import('TaskManager');

class CWebParser
{
    public $urlArray = array();
    public $filteredUrlArray = [];
    public $urlFilterArray = array();
    //регулярные выражения для предобработки урл, например удаление не нужных параметров трекеров
    public $urlReplaceArray = array();
    /**
     * @var int Сколько задач обрабатывается за один запуск процесса
     */
    public $tasksPerExecute = 5;

    /**
     * @var int Количество данных процесса, которые остаются в базе и на диске. Остальное удаляется.
     */
    public $processForStore = 5;

    /**
     * Нужно установить. По указанному хосту фильтруются внешние ссылки.
     */
    public $host = null;

    /**
     * Номер процесса сборки данных. Основной параметр по которому
     * все данные в таблицах группируются. Что бы можно было работать
     * в рамках одного процесса. И вернуться к выполнению любого старого
     * незавершенного.
     */
    private $processId = null;

    /**
     * Параметр необходим, что бы принципиально разделять данные сбора.
     * Например когда нужно разделить парсеры по модулям и что бы у каждого
     * модуля были свои процессы.
     */
    private $parserName = null;
    private $parseScenario = null;

    private $checkMime = false;
    private $mimeArray = [];

    /**
     * @var array массив параметров ссылки, который будет удален из текста ссылки
     */
    private $urlParamFiltersArray = [];

    public $isInterface = false;

    public $doneTasks = [];

    public function getProcessId()
    {
        return $this->processId;
    }

    /**
     * @var TaskManager Экземпляр класса управляющего задачами для текущего процесса
     */
    public $taskManager = null;

    public function __construct($parserName, $host, $scenario, $processId)
    {
        $this->log('==================================================================================================');
        $this->log('==================================================================================================');
        $this->log('Запускаем парсер ' . $parserName . ' хост: ' . $host . ' Id процесса:' . $processId);
        Yii::import('begemot.extensions.parser.models.*');

        $dir = Yii::getPathOfAlias('begemot.extensions.parser');
        require_once($dir . '/phpQuery-onefile.php');
        $this->parserName = $parserName;
        $this->host = $host;
        $this->setScenario($scenario);
        $this->processId = $processId;

        $this->taskManager = new TaskManager($processId);
        $this->deleteAllData();

    }

    /**
     * Удаляем старые данные, перед тем как начнем новый процесс.
     */
    private function deleteAllData()
    {
        //удаляем файлы

        WebParserProcess::model()->findAll();
        $filesDir = Yii::getPathOfAlias('webroot') . '/files/webParser/*';

        $dirsArray = glob($filesDir);


        $dirsForDelete = [];

        foreach ($dirsArray as $dir) {
            $dirsForDelete[filemtime($dir)] = $dir;
        }

        ksort($dirsForDelete);
        reset($dirsForDelete);


        $countOfDirs = count($dirsArray);
        $dirsI = 0;

        foreach ($dirsForDelete as $dir) {
            if ($countOfDirs - $dirsI > $this->processForStore) {

                CFileHelper::removeDirectory($dir);
            }
            $dirsI++;
        }


        //удаляем данные из бд
        $dbDataForClear = [
            [
                'dbName' => 'webParser',
                'dbCol' => 'id'
            ],
            [
                'dbName' => 'webParserData',
                'dbCol' => 'processId'
            ],
            [
                'dbName' => 'webParserDownload',
                'dbCol' => 'processId'
            ],
            [
                'dbName' => 'webParserPage',
                'dbCol' => 'procId'
            ],
            [
                'dbName' => 'webParserScenarioTask',
                'dbCol' => 'processId'
            ],
            [
                'dbName' => 'webParserUrl',
                'dbCol' => 'procId'
            ],
        ];

        foreach ($dbDataForClear as $dbData) {
            //Удаляем из таблица webParser
            $sql = "SELECT distinct(" . ($dbData['dbCol']) . ") FROM " . ($dbData['dbName']) . " order by " . ($dbData['dbCol']) . ";";
            $connection = Yii::app()->db;
            $command = $connection->createCommand($sql);
            $rows = $command->queryAll();

            $countOfRows = count($rows);
            $rowsI = 0;

            foreach ($rows as $row) {
                if ($countOfRows - $rowsI > $this->processForStore) {

                    $sql = "DELETE FROM " . ($dbData['dbName']) . " where " . ($dbData['dbCol']) . "=" . ($row[$dbData['dbCol']]) . ";";
                    $command = $connection->createCommand($sql);
                    $command->execute();
                }
                $rowsI++;
            }
        }

        $this->logVar($rows);


    }

    /**
     * Собственно главный процесс, который надо выполнять многократно
     * с определенным сценарием.
     */
    public function parse()
    {
        $processId = $this->getProcessId();


        $status = null;
        if (is_null($processId)) {

            //Если не интерфейсный режим, то в переменную вернет id нового процесса
            $status = $this->startNewParseProccess();

        } else {
            $this->processId = $processId;
            $status = $this->continueParseProccess($this->parseScenario);
            $this->log('Заканчиваем функцию parse(), кол-во активных задач:' . $this->taskManager->getActiveTaskCount());
        }


        return $status;
    }

    public function getProcessStatus()
    {

        $processId = $this->processId;

        $webParserProcess = WebParserProcess::model()->findByPk($processId);
        if (!is_null($webParserProcess))
            return $webParserProcess->status;
        else
            return 'Процесса не существует!';

    }

    /**
     *
     * Продолжаем процесс сбора данных с удаленного сайта
     *
     * @param $parseScenario array Массив сценариев на базе которого каждый раз принимается решение что делать сейчас и что делать дальше
     *
     */

    private function continueParseProccess($parseScenario)
    {

        $processId = $this->processId;

        $webParserProcess = WebParserProcess::model()->findByPk($processId);
        if ($webParserProcess->status == 'created') {

            $webParserProcess->status = 'proccessing';
            $webParserProcess->save();
        }

        //Начинаем обработку очередной страницы по сценарию сбора информации

        //Для начала узнаем есть ли существующие задания для сценарием в БД
        //Для нашего id процесса и со статусом new
        if (ScenarioTask::isExistSomeTask($this->processId)) {

            //Задачи есть. Выполняем что можем за один запуск
            $this->doSomeTasks();
        } else {
            /*
             * Задач нет. Это может означать:
             *
             * - Запустились первый раз и значит нужно пройтись по сценариям в поиске
             * параметра startUrl. Проверяем наличие startUrl в спарсенных страницах. Если их нет,
             * значит запускаемся первый раз. Начинаем процесс.
             * - Если все ссылки startUrl уже есть в таблице страниц. Процесс закончен.
             * Все страницы обработаны в соответствии со всеми сценариями. Завершаем процесс.
             *
             */

            //Вытаскиваем все startUrl для проверки
            $startUrlArray = array();
            foreach ($parseScenario as $scenarioName => $scenarioItem) {
                if (isset($scenarioItem['startUrl'])) {
                    $startTaskItem = array();
                    $startTaskItem['scenarioItemName'] = $scenarioName;
                    $startTaskItem['url'] = $this->normalizeUrl($this->removeHostFromUrl($scenarioItem['startUrl']));
                    $startTaskItem['target_type'] = WebParserDataEnums::TASK_TARGET_DATA_TYPE_URL;
                    $startTaskItem['task_type'] = $scenarioItem['type'];
                    $startUrlArray[] = $startTaskItem;
                }
            }

            /*
             * Проверяем были ли обработаны эти стартовые страницы раньше
             * Если нет, то ставим их в план на обработку
             */

            //Флаг отвечает за наличие добавленных задач в план. По нему принимаем решение
            //завершаем процесс или нет

            $weHaveNewTasks = false;
            $taskManager = $this->taskManager;

            foreach ($startUrlArray as $startTask) {


                $target_type = WebParserDataEnums::TASK_TARGET_DATA_TYPE_URL;

                $WebParserUrl = $this->getUrlObject($startTask['url']);

                $targetId = $WebParserUrl->id;
                //TODO на забыть добавить всем проверкам на существование задания тип задания
                if (!$this->isTaskExist($targetId, $target_type, $startTask['scenarioItemName'])) {

                    $this->log('Добавляем стартовое задание:');
                    $this->log('$targetId:' . $targetId . ' $target_type:' . $target_type . ' $startTask["scenarioItemName"]:' . $startTask['scenarioItemName']);

                    $taskManager->createTask($startTask['task_type'], $targetId, $target_type, $startTask['scenarioItemName']);
                    $weHaveNewTasks = true;
                }

            }

            if (!$weHaveNewTasks) {
                /**
                 * Процесс парсинга закончен. Все что парсер мог сделать - он сделал.
                 */
                $this->clearOldData();

                $webParserProcess->status = 'done';
                $webParserProcess->save();
            }

        }

    }

    public function getUrlObject($url)
    {

        if (!($webParserUrl = WebParserUrl::model()->find('`url`=:url and `procId`=' . $this->processId, array(':url' => $url)))) {
            $webParserUrl = new WebParserUrl();

            $webParserUrl->procId = $this->processId;
            $webParserUrl->url = $url;


            if ($webParserUrl->save()) {
                $webParserUrl->id = Yii::app()->db->getLastInsertId();
            }
        }


        return $webParserUrl;
    }


    /**
     * Процедура запускается когда ясно что есть задачи
     * в плане и их нужно выполнять.
     *
     * По сути основная логика именно разбора кода страницы по сценарию и формирования следующих
     * заданий происходит тут.
     */
    private function doSomeTasks()
    {
        $tasks = ScenarioTask::model()->findAll(
            array(
                'limit' => $this->tasksPerExecute,
                'condition' => 'processId=:processId and taskStatus="new"',
                'params' => array(
                    ':processId' => $this->processId
                )
            )
        );
        $i = 0;
        foreach ($tasks as $task) {
            $i++;
            $this->log('Выполняем ' . $i . ' в очереди! Лимит:' . $this->tasksPerExecute);
            if ($i > $this->tasksPerExecute) {
                $this->log('ВЫПОЛНИЛИ ДОСТАТОЧНО! ПРЕВЫШЕН ЛИМИТ ВЫПОЛНЕНИЯ ЗАДАНИЙ ЗА ОДИН ЗАПУСК!');
                break;
            }
            $this->doTask($task);
        }
    }

    public function addMime($mime)
    {
        $this->mimeArray[$mime] = 1;
        $this->checkMimeOn();
    }

    /**
     *
     * @param $task ScenarioTask
     */
    private function doTask($task)
    {
        //Действуем от типа задачи

        $this->log('Выполняем задачу  ' . ($task->id) . ' сценарий: ' . ($task->scenarioName));
        $this->log(' тип:' . ($task->taskType));
        $scenarioItem = $this->getScenarioItem($task->scenarioName);
        $taskManager = $this->taskManager;


//        if ($task->taskType == WebParserDataEnums::TASK_TYPE_START_NAVIGATION) {
//            Yii::log('Определили тип задачи  ' .WebParserDataEnums::TASK_TYPE_START_NAVIGATION , 'trace', 'webParser');
//
//            //Данный тип задач обрабатываем так. Проверяем код ответа http. Проверяем mime,
//            //Если все норм - грузим страницу и сохраняем в виде WebParserPage
//            $url = $task->getTargetData();
//
//            if ($data = $this->checkRemotePage($url)) {
//
//                if ($data['mime'] == 'text/html' && $data['httpCode'] == 200) {
//
//                    $webParserPage = $this->parsePage($url);
//
//
//                    if (!$this->isTaskExist(WebParserDataEnums::TASK_TARGET_DATA_TYPE_WEBPAGE, $webParserPage->id, $task->scenarioName)) {
//
//                        $this->taskManager->createTask(
//                            WebParserDataEnums::TASK_TYPE_PROCESS_URL,
//                            $webParserPage->id,
//                            WebParserDataEnums::TASK_TARGET_DATA_TYPE_WEBPAGE,
//                            $task->scenarioName);
//                    }
//                }
//            }
//
//        }

        //Тип задачи "паук". Собираем все со всех ссылок.
        if ($task->taskType == WebParserDataEnums::TASK_TYPE_CRAWLER) {

            $this->log('Определили тип задачи  ' . WebParserDataEnums::TASK_TYPE_CRAWLER);
            $this->log('Определили тип обрабатываемых данных:' . $task->target_type);

            $pageContent = $this->getContentByTask($task);
            if (!$pageContent) {
                $this->logError('ОШИБКА! Контент страницы пуст!');
            }
            $doc = phpQuery::newDocument($pageContent);
            phpQuery::selectDocument($doc);

            //достаем все ссылки из контента
            $urlArray = $this->getAllUrlFromContent($doc);

            $taskTargetUrl = $task->getUrl();

            foreach ($urlArray as $url => $data) {

                /*
                 * Запоминаем ссылки. Которые нашли.
                 */
                $link = WebParserLink::model()->findByAttributes([
                    'sourceUrl' => $taskTargetUrl,
                    'url' => $url,
                    'procId' => $this->processId
                ]);
                if (!$link) {
                    $link = new WebParserLink();
                    $link->url = $url;
                    $link->sourceUrl = $taskTargetUrl;
                    $link->procId = $this->processId;
                    $link->anchor = $data;
                    $link->save();
                }


                $target_type = WebParserDataEnums::TASK_TARGET_DATA_TYPE_URL;

                $WebParserUrl = $this->getUrlObject($url);

                $targetId = $WebParserUrl->id;

                $scenarioTaskName = $task->scenarioName;

                if (!$this->isTaskExist($targetId, $target_type, $scenarioTaskName)) {

                    $targetScenarioItem = $this->getScenarioItem($scenarioTaskName);

                    $taskManager->createTask($targetScenarioItem['type'], $targetId, $target_type, $scenarioTaskName);

                }

                //Тип цели нам известен, выставляем его сразу
                $target_type = WebParserDataEnums::TASK_TARGET_DATA_TYPE_URL;
            }

        }

        /*
         * Отличается от CRAWLER тем, что можно определить области откуда берем ссылки.
         * например только из меню или подвала и т.д. А CRAWLER собирает все.
         */
        if ($task->taskType == WebParserDataEnums::TASK_TYPE_PROCESS_URL) {

            $this->log('Определили тип задачи  ' . WebParserDataEnums::TASK_TYPE_PROCESS_URL);
            $this->log('Определили тип обрабатываемых данных:' . $task->target_type);


            $pageContent = $this->getContentByTask($task);
            if (!$pageContent) {
                $this->logError('ОШИБКА! Контент страницы пуст!');
            }

            $doc = phpQuery::newDocument($pageContent);
            phpQuery::selectDocument($doc);

            $taskTargetUrl = $task->getUrl();

            if (isset($scenarioItem['parser_rules']) && is_array($scenarioItem['parser_rules'])) {
                $this->log('Начали перебор $scenarioItem[\'parser_rules\']');
                foreach ($scenarioItem['parser_rules'] as $scenarioTaskName => $navigationRule) {


                    $searchHrefsDocumentPart = pq($navigationRule);

                    //Перебираем все части кода которые нашли по правилу сценария
                    $this->log('Правило:' . $navigationRule);
                    $this->log('Вернуло массив данных с количеством элементов:' . count($searchHrefsDocumentPart));
                    foreach ($searchHrefsDocumentPart as $navigationPart) {
                        //Создаем ScenarioTask для каждого найденного урл

                        $urlArray = $this->getAllUrlFromContent($navigationPart);
                        $this->log('Ищем url в полученных данных. Нашли:' . count($urlArray));
                        foreach ($urlArray as $url => $data) {

                            /*
                                 * Запоминаем ссылки. Которые нашли.
                                 */
                            $link = WebParserLink::model()->findByAttributes([
                                'sourceUrl' => $taskTargetUrl,
                                'url' => $url,
                                'procId' => $this->processId
                            ]);
                            if (!$link) {
                                $link = new WebParserLink();
                                $link->url = $url;
                                $link->sourceUrl = $taskTargetUrl;
                                $link->procId = $this->processId;
                                $link->anchor = $data;
                                $link->save();
                            }


                            $target_type = WebParserDataEnums::TASK_TARGET_DATA_TYPE_URL;

                            $WebParserUrl = $this->getUrlObject($url);

                            $targetId = $WebParserUrl->id;

                            if (!$this->isTaskExist($targetId, $target_type, $scenarioTaskName)) {

                                $targetScenarioItem = $this->getScenarioItem($scenarioTaskName);

                                $taskManager->createTask($targetScenarioItem['type'], $targetId, $target_type, $scenarioTaskName);

                            }

                            //Тип цели нам известен, выставляем его сразу
                            $target_type = WebParserDataEnums::TASK_TARGET_DATA_TYPE_URL;


                        }
                    }


                }
            }
        }

        if ($task->taskType == WebParserDataEnums::TASK_TYPE_DATA) {

            $this->log('Определили тип задачи  ' . WebParserDataEnums::TASK_TYPE_DATA);
            $this->log('Определили тип цели ' . $task->target_type);


            /**
             * Начинаем сбор данных по набору фильтров.
             * Если фильтр начинается с "@", то он процедурный. То есть это
             * не css путь, а некий стандартный модификатор. Например @url - это фильтр,
             * который в качестве данных просит вернуть url страницы с которой ведется работа. И т.д.
             *
             * В общем все что нужно обрабатывать нестандартно помечаем символом @
             */
            // $this->logVar($scenarioItem);

            //Тут вытаскиваем данные из которых будем парсить
            $this->log('Запускаем $this->getPageContentByTask в doTask()' . $task->target_type);
            // $pageContent = $this->getPageContentByTask($task);

            //phpQuery::newDocument($pageContent);

            if (isset($scenarioItem['dataFields']) && is_array($scenarioItem['dataFields'])) {
                $this->log('Все правила сборки данных $scenarioItem[\'dataFields\']: ');
                $this->logVar($scenarioItem['dataFields']);
                $this->log('$task->target_type=' . $task->target_type);
                if ($task->target_type == WebParserDataEnums::TASK_TARGET_DATA_TYPE_DATA) {
                    $targetDataObject = $this->getTargetFromTask($task);
                }


                foreach ($scenarioItem['dataFields'] as $fieldName => $fieldFilter) {

                    $downloadImageFlag = false;
                    $leftTableExtractFlag = false;

                    //Перебираем все фильтры. Пропускаем процедурный, ибо он
                    //применяется ко всем фильтрам
                    if ($fieldName === '@') continue;
                    $this->log('Обрабатываем поле ' . $fieldName . ' с фильтром ' . $fieldFilter);
                    //Вытащили данные. Значений можем получить много, либо одно

                    if (strlen($fieldFilter) > 0 && $fieldFilter{0} == '@') {
                        $filterArray = explode(' ', $fieldFilter);

                        if ($filterArray[0] == '@download') {
                            $downloadImageFlag = true;
                        }

                        if ($filterArray[0] == '@leftTableExtract') {
                            $leftTableExtractFlag = true;
                        }

                        unset($filterArray[0]);
                        $fieldFilter = implode(' ', $filterArray);
                    }

                    $pqData = $this->executeFilter($fieldFilter, $task);

                    /*
                     *Разбираем таблицу на несколько по первому столбцу и каждой колонке
                     */
                    if (isset($leftTableExtractFlag) && $leftTableExtractFlag == true) {
                        require_once(dirname(__FILE__) . '/HTMLTablesHelper.php');
                        $this->log('leftTableExtractFlag включен');

//                        $this->log(print_r($task->getUrl(),true));
                        if (count($pqData) == 0) continue;
                        $pqData = leftTableExtract($pqData[0]);

                    }

                    //$this->logVar($pqData);
                    //Если получили одно значение, преобразовываем его в массив для простоты обработки
                    if (!is_array($pqData)) $pqData = array($pqData);
                    $this->log('Вытащили ' . count($pqData) . ' полей');


                    //Всегда обрабатывается массив значений, в простейшем случае это массив с одним элементом
                    foreach ($pqData as $planeData) {

                        // Проверяем что мы доставли. Если это с флагом @download
                        // то создаем задачу на скачивание, иначе просто создаем
                        // элемент данных в БД
                        if (isset($downloadImageFlag) && $downloadImageFlag == true) {
                            /*
                             * Создаем задачу на скачку изображение
                             * и создаем terget на скачку с id данных
                             * к которым надо изображение после прикрепить
                             */

                            //Сначала декодируем символы %## и проверяем url мы вытащили или нет

                            $planeData = urldecode($planeData);

                            if (!(bool)parse_url($planeData)) {
                                $this->logError('Не является ссылкой  ' . $planeData);
                                continue;
                            }


                            $this->log('Создаем объек загрузки.');
                            $downloadModel = new WebParserDownload();
                            $downloadModel->processId = $this->processId;
                            $downloadModel->fileUrl = $planeData;

                            if (isset($scenarioItem['dataFields']['@'][WebParserDataEnums::DATA_ID_ARRAY_KEY])) {
                                $filterResult = $this->executeFilter($scenarioItem['dataFields']['@'][WebParserDataEnums::DATA_ID_ARRAY_KEY], $task);
                                if (is_array($filterResult))
                                    $downloadModel->fieldId = array_shift($filterResult);
                                else
                                    $downloadModel->fieldId = $filterResult;
                            }
                            $downloadModel->save();
                            $targetType = WebParserDataEnums::TASK_TARGET_DATA_TYPE_DOWNLOAD;
                            if (!$this->isTaskExist($downloadModel->id, $targetType, null)) {
                                $taskType = WebParserDataEnums::TASK_TYPE_DOWNLOAD;
                                $taskManager->createTask($taskType, $downloadModel->id, $targetType, null);

                            }

                            continue;
                        }

                        $dataFieldModel = new WebParserData();

                        $dataFieldModel->sourcePageUrl = $task->getUrl();


                        if (isset($targetDataObject)) {
                            $dataFieldModel->parentDataId = $targetDataObject->id;

                        }

                        $dataFieldModel->processId = $this->processId;

                        $dataFieldModel->fieldName = $fieldName;
                        $this->log('Фильтр: ');
                        $this->logVar($fieldFilter);


                        $dataFieldModel->fieldData = $planeData;


                        /*
                         * Если имена фильтров начинались с @, то это массовые фильтры.
                         * Применяются ко всем данным в текущем Task
                         */
                        if (isset($scenarioItem['dataFields']['@'])) {
                            foreach ($scenarioItem['dataFields']['@'] as $massFilterKey => $massFilter) {
                                switch ($massFilterKey) {
                                    case WebParserDataEnums::DATA_ID_ARRAY_KEY :
                                        $filterResult = $this->executeFilter($massFilter, $task);
                                        if (is_array($filterResult))
                                            $dataFieldModel->fieldId = array_shift($filterResult);
                                        else
                                            $dataFieldModel->fieldId = $filterResult;
                                        break;
                                    case WebParserDataEnums::DATA_PARENT_ID_ARRAY_KEY :
                                        $filterResult = $this->executeFilter($massFilter, $task);
                                        if (is_array($filterResult))
                                            $dataFieldModel->fieldParentId = array_shift($filterResult);
                                        else
                                            $dataFieldModel->fieldParentId = $filterResult;
                                        break;
                                    case WebParserDataEnums::DATA_GROUP_ID_ARRAY_KEY :
                                        $this->log('Инструкция DATA_GROUP_ID_ARRAY_KEY! ' . $massFilter);
                                        $filterResult = $this->executeFilter($massFilter, $task);
                                        if (is_array($filterResult)) {
                                            $this->log('Нашли результатов: ' . count($filterResult));
                                            $dataFieldModel->fieldGroupId = array_shift($filterResult);
                                        } else
                                            $dataFieldModel->fieldGroupId = $filterResult;
                                        break;
                                    case WebParserDataEnums::DATA_MODIF_OF_ID_ARRAY_KEY :
                                        $filterResult = $this->executeFilter($massFilter, $task);
                                        if (is_array($filterResult))
                                            $dataFieldModel->fieldModifId = array_shift($filterResult);
                                        else
                                            $dataFieldModel->fieldModifId = $filterResult;
                                        break;
                                }
                            }
                        }

                        if ($dataFieldModel->save()) {

                            $this->log('Сохранили данные! ' . $dataFieldModel->fieldName);
                        } else {
                            $this->logError('Не сохранили данные! Имя поля:' . $dataFieldModel->fieldName . ' fieldId:');

                            $this->logVar($dataFieldModel->fieldId);
                            $this->logVar($dataFieldModel->getErrors());
                        }
                        /*
                         * Данные мы вытащили и сохранили. Теперь нужно уточнить
                         * нужно ли эти данные разбирать повторно.
                         *
                         * Эти правила записываем в сценарий с ключом parse_data_rules
                         *
                         * В этом элементе пишем массив. Ключ должен совпадать с fieldName
                         *
                         * Значение, это сценарий в который это дело передаем.
                         *
                         */
                        if (isset($scenarioItem['parse_data_rules']) && is_array($scenarioItem['parse_data_rules'])) {


                            foreach ($scenarioItem['parse_data_rules'] as $fieldName => $scenarioTaskNameArray) {
                                $this->log('parse_data_rules:');
                                $this->logVar($scenarioItem['parse_data_rules']);
                                if (!is_array($scenarioTaskNameArray)) $scenarioTaskNameArray = [$scenarioTaskNameArray];


                                foreach ($scenarioTaskNameArray as $scenarioTaskName) {
                                    $this->log('Проверяем нужно ли создавать задачу на разбор данных: ' . $scenarioTaskName);

                                    $this->log('Проверяем ' . $dataFieldModel->fieldName . ' = ' . $fieldName);
                                    if ($dataFieldModel->fieldName == $fieldName) {
                                        //Это спарсеное значение нужно передать дальше на обработку другому
                                        //сценарию.
                                        $this->log('Равно! Создаем задачу на разбор данных: ' . $scenarioTaskName);

                                        $targetScenarioItem = $this->getScenarioItem($scenarioTaskName);

                                        $taskType = $targetScenarioItem['type'];
                                        $targetType = WebParserDataEnums::TASK_TARGET_DATA_TYPE_DATA;

                                        if (!$this->isTaskExist($dataFieldModel->id, $targetType, $scenarioTaskName)) {

                                            $taskManager->createTask($taskType, $dataFieldModel->id, $targetType, $scenarioTaskName);

                                        }

                                    } else {
                                        $this->log('Не равно!');
                                    }
                                }
                            }

                        }

                    }


                }

            }


        }

        /*
         * Задачи по скачиванию файлов
         */
        if ($task->taskType == WebParserDataEnums::TASK_TYPE_DOWNLOAD) {
            $this->log('Определили тип задачи  ' . WebParserDataEnums::TASK_TYPE_DOWNLOAD);
            $this->log('Определили тип цели  ' . $task->target_type);

            $targetDownloadObject = $this->getTargetFromTask($task);
            if (!is_null($targetDownloadObject)) {
                $this->log('id  ' . $targetDownloadObject->id);


                //Проверяем и создаем директории
                $fieldIdDir = null;
                $dirArray = [
                    'rootDir' => 'files/webParser/',
                    'processDir' => 'files/webParser/process_' . $this->processId . '/',
                    'fieldIdDir' => 'files/webParser/process_' . $this->processId . '/item_' . $targetDownloadObject->id . '/',

                ];

                $this->checkAndCreateDirs($dirArray);


                extract($dirArray);
                $targetDownloadObject->file = $this->downloadFile($targetDownloadObject->fileUrl, $fieldIdDir);
                // $this->log('Ломаем'.$file);
                $targetDownloadObject->save();

            } else {
                $this->logError(__LINE__ . ' $targetDownloadObject - NULL');
            }
        }


        $task->completeTask();
        $this->doneTasks[] = $task;

    }

    /**
     * Все просто. Скачиваем файл.
     *
     * @param $fileUrl Что скачиваем.
     * @param $dir Куда сохраняем.
     */
    private function downloadFile($fileUrl, $dir)
    {
        if (is_null($dir)) {
            $this->logError('function downloadFile входящий параметр с директорией NULL');
            return;
        }

        $urlHost = parse_url($fileUrl, PHP_URL_HOST);

        if (is_null($urlHost)) {
            $fileUrl = $this->host . '/' . $fileUrl;
        }

        $fileNameWithPath = parse_url($fileUrl, PHP_URL_PATH);
        $file = Yii::getPathOfAlias('webroot') . '/' . $dir . basename($fileNameWithPath);
        // открываем файл, на сервере, на запись

        if (!file_exists($file)) {
            $dest_file = @fopen($file, "w");

            // открываем cURL-сессию
            $resource = curl_init();
            //$this->log('Ломаем!!!!' . $fileUrl . ' ' . $dir);
            // устанавливаем опцию удаленного файла
            curl_setopt($resource, CURLOPT_URL, $fileUrl);

            // устанавливаем место на сервере, куда будет скопирован удаленной файл
            curl_setopt($resource, CURLOPT_FILE, $dest_file);

            // заголовки нам не нужны
            curl_setopt($resource, CURLOPT_HEADER, 0);

            // выполняем операцию
            curl_exec($resource);

            // закрываем cURL-сессию
            curl_close($resource);

            // закрываем файл
            fclose($dest_file);
        }
        if (file_exists($file)) {
            $this->log('Файл скачался успешно!');
        } else {
            $this->logError('После скачивания файл не сохранился и на диске его нет.');
        }
        return $file;

    }

    /**
     * Проверяет массив директорий и создает их если нет
     */

    private function checkAndCreateDirs($dirsArray)
    {

        $baseDir = Yii::getPathOfAlias('webroot');

        foreach ($dirsArray as $dir) {

            $dirName = $baseDir . '/' . $dir;
            if (!file_exists($dirName)) {
                mkdir($dirName);
            }
        }

    }

    public function checkMimeOn()
    {
        $this->checkMime = true;
    }

    private function getContentByTask($task)
    {
        $this->log('Достаем контент из задачи с id:' . $task->id);
        $this->log('Достаем контент из задачи с типом:' . $task->target_type);
        $targetType = $task->target_type;
        if ($targetType == WebParserDataEnums::TASK_TARGET_DATA_TYPE_WEBPAGE) {
            $pageContent = $task->getTargetData();
        } elseif ($targetType == WebParserDataEnums::TASK_TARGET_DATA_TYPE_URL) {
            $pageUrl = $task->getTargetData();
            $this->log('Вытащили url:' . $pageUrl);
            $pageContent = $this->getDocument($pageUrl)->content;
        } elseif ($targetType == WebParserDataEnums::TASK_TARGET_DATA_TYPE_DATA) {
            $webParserData = WebParserData::model()->findByPk($task->target_id);
            $pageContent = $webParserData->fieldData;
            //$this->log('Вытащили data:' . $pageContent);

        }

        return $pageContent;
    }

    /**
     * @param $url адрес страницы
     * @return int
     */

    private function getDocumentId($url)
    {

        $webParserPage = $this->getDocument($url);

        return $webParserPage->id;
    }

    /**
     * Ищем документ с таким адресом. Если документа нет, то парсим его. Как итог возвращаем id
     *
     * @param $url адрес страницы документа
     */

    private function getDocument($url)
    {

        $this->log('Проверяем на редиректы удаленную страницу.');
        $urlForSearch = $this->getRedirectedUrl($url);

        $this->log('Начали поиск в базе url ' . $url . '');
        $webParserPage = WebParserPage::model()->find('url =:urlForSearch and `procId` = ' . $this->processId, [':urlForSearch' => $urlForSearch]);
        $this->log('Закончили поиск в базе url ' . $url . '');


        if (!$webParserPage) {
            $this->log('В базе url ' . $url . ' нет, парсим страницу.');
            $webParserPage = $this->parsePage($url);
        }

        return $webParserPage;
    }

    private function executeFilter($filter, $task)
    {

        $this->log('Зашли в executeFilter и выполняем фильтр:' . $filter);

        if (strstr($filter, '+')) {
            $this->log('В фильтре найден знак конкатенации "+". ' . $filter);

            /*
             * В случае фильтра с конкатенацией фильтр разбивается на несколько фильтров
             * по разделителю "+". Каждый фильтр будет выполнен. С каждого результата будет взят
             * первый элемент результата, если фильтр вернул больше одного совпадения. Все данные от
             * каждого фильтра объединяются в одну строку.
             */

            $concatFilters = explode('+', $filter);

            $dataForConcat = [];

            foreach ($concatFilters as $concatFilter) {
                $concatFilter = trim($concatFilter);

                $filterResults = $this->executeFilter($concatFilter, $task);
                if (is_array($filterResults)) {
                    $this->log('Фильтр процедурный ' . print_r($filterResults, true));
                    if (count($filterResults) == 0) {
                        $this->log('Нет совпадений! Выходим. ');
                        return '';
                    }
                    $dataForConcat[] = $filterResults[0];
                } else {
                    $dataForConcat[] = $filterResults;
                }

            }

            return implode('', $dataForConcat);
        }

        if (strlen($filter) > 0 && $filter{0} == "!") {
            return str_replace('!', '', $filter);
        }

        if (strlen($filter) > 0 && $filter{0} == "@") {
            //Процедурный фильтр
            $this->log('Фильтр процедурный.');
            if ($filter == WebParserDataEnums::DATA_FILTER_URL) {
                $this->log('Фильтр процедурный ' . WebParserDataEnums::DATA_FILTER_URL);
                return $task->getUrl();
            }

            if ($filter == WebParserDataEnums::DATA_FILTER_HOLE_CONTENT) {
                $this->log('Фильтр процедурный ' . WebParserDataEnums::DATA_FILTER_HOLE_CONTENT);
                return $this->getContentByTask($task);
            }

        }
        $this->log('Фильтр НЕ процедурный.');
        $content = $this->getContentByTask($task);

        $returnArray = [];

        $doc = phpQuery::newDocumentHTML($content);

        /*
         * Фильтр делиться на левую и правую часть символом "|"
         * в правой части модификаторы и команды
         */
        $filterCommand = '';
        if (strstr($filter, '|')) {
            $filterParts = explode('|', $filter);
            $filter = $filterParts[0];
            $filterCommand = $filterParts[1];
        }


        foreach ($doc->find($filter) as $resultElement) {
            $resultElement = pq($resultElement);

            if (strstr($filterCommand, 'val')) {
                $returnArray[] = $resultElement->val();
            } elseif (strstr($filterCommand, 'price')) {
                $returnArray[] = preg_replace('/[^0-9,]/', '', $resultElement->text());
            } elseif (strstr($filterCommand, 'text')) {
                $returnArray[] = $resultElement->text();
            } elseif (strstr($filterCommand, 'href')) {
                $returnArray[] = $resultElement->attr('href');
            } else {

                $returnArray[] = $resultElement->html();
            }


        }
        /*
           * Комманда указания номера элемента с конца, который нужно взять.
           * Указывается в конце комманды фильтру в виде #1
           */
        if (strstr($filterCommand, '#')) {
            $this->log('Нашли указание на предпоследний элемент. ');
            $lastElemNumber = explode('#', $filterCommand);
            $lastElemNumber = $lastElemNumber[1];
            $this->log('Оставляем элемент с конца под номером: ' . $lastElemNumber);
            $ewsultCount = count($returnArray);
            foreach ($returnArray as $id => $data) {
                if ($ewsultCount - $id != $lastElemNumber) {
                    unset($returnArray[$id]);
                }
            }
        }

        return $returnArray;

    }


    private function startNewParseProccess()
    {

        $this->log('Номер процесса парсера не передали. Создаем.');
        $webParserProcess = new WebParserProcess();
        $webParserProcess->date = time();
        $webParserProcess->status = 'created';
        $webParserProcess->name = $this->parserName;

        $webParserProcess->save();

        $webParserProcess = WebParserProcess::model()->findByPk(Yii::app()->db->getLastInsertId());

        $this->log('Создаем с номером - ' . $webParserProcess->id);

        /*
         * Если парсер загружен в интерфейсном режиме,
         * то делаем редирект на ту же страницу с номером процесса.
         * Далее можно на странице, где организован парсер
         * выводить данные. Каждая перезагрузка страницы будет запуском итерации парсера.
         *
         * Если же режим не интерфейсный, то есть парсер будет запускаться фоном где-то в консоли или
         * еще где, то редиректа не нужно. Просто возвращаем id нового процесса как параметр. Окружение
         * уже само должно решить куда сохранять данный id, что бы при следующем запуске продолжить
         * этот процесс.
         */
        if ($this->isInterface) {
            $controller = Yii::app()->getController();
            $redirectUrl = $controller->createUrl('', array('processId' => $webParserProcess->id) + $_GET);
            $this->log('Редирект на страницу- ' . $redirectUrl);

            $controller->redirect($redirectUrl);
        } else {

            return $webParserProcess->id;
        }

    }

    /**
     *
     * Возвращаем конечный урл, если был редирект. Или тот же самый, если редиректа не было.
     *
     * @param $url проверяемый урл
     */
    private function getRedirectedUrl($url)
    {

        $fullPageUrl = 'http://' . $this->host . $url;

        $ch = curl_init($fullPageUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        curl_exec($ch);
        $lastUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

        return $lastUrl;
    }

    /**
     * Проверка страницы на код ответа и mime.
     *
     *
     * @param $webParserPage WebParserPage
     * @return bool
     */
    private function checkRemotePage($url)
    {
        $url = trim($url);
        $webParserPage = new WebParserPage();

        $webParserPage->procId = $this->processId;

        $fullPageUrl = 'http://' . $this->host . $url;
        $this->log('Начали базовые проверки удаленной страницы: ' . $fullPageUrl);

        $ch = curl_init($fullPageUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        curl_exec($ch);

        $mime = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        $mime = explode(';', $mime);
        $mime = $mime[0];
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $lastUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        $webParserPage->url = $lastUrl;

        //if ($mime != 'text/html') {

        $codeFlag = true;
        $mimeFlag = true;

        if ($this->checkMime) {
            $this->logVar($mime);
            $this->logVar($this->mimeArray);
            if (isset($this->mimeArray[trim($mime)])) {
                $this->logError('mime не равно text/html, а равен:' . $mime . ' Фильтруем страниц! Не сохраняем согласно фильтрам МИМЕ');
                $mimeFlag = false;
            }
        }

        if ($httpCode != 200) {
            $this->log('http код не равен 200, а равен:' . $httpCode);
            $codeFlag = false;
        }

        if (!$codeFlag or !$mimeFlag) {

            $webParserPage->mime = $mime;
            $webParserPage->http_code = $httpCode;
            $this->log('Эту страницу не парсим! mime: ' . $mime . ' HTTP Code: ' . $httpCode);

            if (!$webParserPage->save()) {
                die('Ошибка сохранения модели в protected/modules/begemot/extensions/parser/CWebParser.php');
            }
            return false;
        }

        $returnData = [];
        $returnData['mime'] = $mime;
        $returnData['httpCode'] = $httpCode;


        return $returnData;
    }


    /**
     * Парсим страницу. Сохраняем в базу в соответствии с моделью. Сохраняем контент, код http
     * на случай ошибки и mime тип.
     *
     * @param $url string ссылка на страницу. относительная
     * @return WebParserPage спасенная страница возвращается в виде экземпляра модели WebParserPage
     */
    public function parsePage($url)
    {
        $url = trim($url);
        $webParserPage = new WebParserPage();

        $fullPageUrl = 'http://' . $this->host . $url;
        $this->log('Полный url страницы:' . $fullPageUrl);
        $webParserPage->url = $url;


        $webParserPage->procId = $this->processId;

        if ($this->checkRemotePage($webParserPage->url)) {
            $this->log('Проверили удаленную страницу. Все ок! Парсим. ');

//            $microtimeStart = microtime();
            // $this->log('Начинаем профилирование.');

            // $this->log('Инициализация CURL - ' . (microtime() - $microtimeStart));

            $data = self::getRemotePageContent($fullPageUrl);

            $webParserPage->url = $data['lastUrl'];
            $webParserPage->content = $data['content'];
            $webParserPage->mime = $data['mime'];
            $webParserPage->http_code = $data['httpCode'];
//            $this->log('Mime: ' . $mime . ' HTTP Code: ' . $httpCode);

            if (!$webParserPage->save()) {
                die('Ошибка сохранения модели в protected/modules/begemot/extensions/parser/CWebParser.php');
            }
            $this->log('Сохранили в базу данные ');
        }

        return $webParserPage;
    }

    /**
     * Скачиваем и возвращаем коды ответа и тип mime страницы.
     * Отдельная статическая функция, что бы можно было это использовать в
     * других местах движка, где нужно разово забрать удаленную страницу.
     */
    public static function getRemotePageContent($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

        $data = curl_exec($ch);

        $mime = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        $mime = explode(';', $mime);
        $mime = $mime[0];
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $lastUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);


        if (!$data) {
//            $this->logError('Ошибка CURL:');
//            $this->logVar(curl_error($ch));

        }
        // $this->log('Выполнили curl_exec - ' . (microtime() - $microtimeStart));
        curl_close($ch);
        return [
            'content'=>$data,
            'mime'=>$mime,
            'lastUrl'=>$lastUrl,
            'httpCode'=>$httpCode
        ];

    }

    /**
     *
     * Вытаскивает все ссылки со страницы. Убирает те, которые попадают под фильтры.
     *
     * @param $pageContent html код
     *
     */
    public function getAllUrlFromContent($сontent)
    {

        $urlArray = array();
        foreach (pq($сontent)->find('a') as $a) {
            $a = pq($a);
            $urlArray[$a->attr('href')] = $a->html();
        }


        $urlArray = $this->filterOtherHostUrl($urlArray);
        $normalizedUrlArray = $this->normalizeUrlArray($urlArray);

        $urlArray = array_unique($normalizedUrlArray, SORT_STRING);


        return $this->filterUrlArray($urlArray);

    }

    public function addUrlFilter($regExpFilter)
    {
        $this->urlFilterArray[] = $regExpFilter;
    }

    /**
     * @param $paramName Добавляем параметр, который нужно удалить из урл
     */
    public function addUrlParamFilter($paramName)
    {
        $this->urlParamFiltersArray[] = $paramName;
    }

    public function addUrlReplacer($regExpFilter, $resultStr, $filterName)
    {
        $this->urlReplaceArray[$filterName] = [$regExpFilter, $resultStr];
    }

    public function filterUrlArray($urlArray)
    {


        $resultArray = [];
        $filteredArray = array_filter($urlArray, array(get_class($this), 'regExpChecker'),ARRAY_FILTER_USE_KEY);

//        $resultArray = [];
//        foreach ($filteredArray as $ulr) {
//            foreach ($this->urlReplaceArray as $filter) {
//                $string = $ulr;
//                $pattern = $filter[0];
//                $replacement = $filter[1];
//                $resultArray[] = preg_replace($pattern, $replacement, $string);
//
//            }
//        }

        foreach ($filteredArray as $urlKey => $url) {


            $url = htmlspecialchars_decode($url);


            $urlParams = parse_url($url);


            $paramsNames = $this->urlParamFiltersArray;
            $resultUrl = '';
            if (isset($urlParams['scheme'])) $resultUrl .= $urlParams['scheme'];
            if (isset($urlParams['host'])) $resultUrl .= $urlParams['host'];

            $resultUrl .= $urlParams['path'];
            if (isset($urlParams['query']))
                if (strlen($urlParams['query']) > 0 && $urlParams['query'] !== '?') {
                    $params = explode('&', $urlParams['query']);

                    foreach ($params as $paramId => $param) {
                        $singleParamDetails = explode('=', $param);
                        echo $singleParamDetails[0];

                        if (in_array($singleParamDetails[0], $paramsNames)) {
                            unset($params[$paramId]);
                        }
                    }
                    asort($params);
                    if (count($params) > 0) {
                        $resultUrl .= '?';
                        $resultUrl .= implode('&', $params);
                    }
                }


            $resultArray[$urlKey] = $resultUrl;
        }

        return $resultArray;
    }


    /**
     * Функция убирает внешние ссылки из массива всех ссылок
     */
    private function filterOtherHostUrl($urlArray)
    {

        foreach ($urlArray as $key => $url) {
            $url_data = parse_url($url);

            if (isset($url_data['host'])) {
                if ($url_data['host'] != $this->host && $url_data['host'] != 'www.' . $this->host) {
                    unset ($urlArray[$key]);
                } else {
                    $urlArray[$key] = $this->removeHostFromUrl($url);
                }
            }

        }

        return $urlArray;

    }

    private function normalizeUrlArray($urlArray)
    {

        foreach ($urlArray as $key => $url) {
            $urlArray[$key] = $this->normalizeUrl($url);
        }

        return $urlArray;
    }

    /**
     * Функция нормализации урл. Все работы по приведению всех урл к общему виду делаем тут.
     *
     * @param $url
     * @return string
     */
    public function normalizeUrl($url)
    {

        //$url = strtolower($url);
        $url = trim($url);
        if (!preg_match('#^/#', $url)) {
            $url = '/' . $url;
        }

        return $url;
    }

    /**
     *
     * Проверяем есть в базе код страницы с указанным адресомм
     *
     * @param $url адрес страницы
     * @param null $webPage в эту переменную возвращает код из базы
     * @return bool true - значит url в базе нет, false - уже парсили и код есть в базе и скопирован в переменную &$webPage
     */
//    protected function isUrlUniq($url, &$webPage = null)
//    {
//        $WebParserPages = WebParserPage::model()->findAll(
//            array(
//                'condition' => 'url_hash=:url_hash and procId=:processId',
//                'params' => array(
//                    ':url_hash' => md5($url),
//                    ':processId' => $this->processId
//                )
//            )
//        );
//
//        if (count($WebParserPages) !== 0) {
//            foreach ($WebParserPages as $WebParserPage) {
//                if ($WebParserPage->url === $url) {
//
//                    if (!is_null($webPage)) {
//                        $webPage = $WebParserPage;
//                    }
//
//                    return false;
//                }
//            }
//        }
//
//        return true;
//
//    }

    private function isTaskExist($target_id, $target_type, $scenarioItemName = null)
    {
        return ScenarioTask::isExistTask($target_id, $target_type, $scenarioItemName, $this->processId);
    }

    private function getTargetFromTask($task)
    {
        if ($task->target_type == WebParserDataEnums::TASK_TARGET_DATA_TYPE_DATA) {
            $targetDataObject = WebParserData::model()->findByPk($task->target_id);
            return $targetDataObject;
        } elseif ($task->target_type == WebParserDataEnums::TASK_TARGET_DATA_TYPE_DOWNLOAD) {
            $targetDownloadObject = WebParserDownload::model()->findByPk($task->target_id);
            return $targetDownloadObject;
        }
    }

    private function regExpChecker($var)
    {

        foreach ($this->urlFilterArray as $urlFilter) {
            if (preg_match($urlFilter, $var)) {
                return false;
            }
        }
        return true;
    }


    /**
     * @param $scenarioInput Массив с описанием сценария работы для парсера
     */
    private function setScenario($scenarioInput)
    {
        $scenarioArray = array();
        foreach ($scenarioInput as $scenarioName => $scenarioItem) {


            /*
             * Ищем и кладем отдельно фильтры с процедурными именами
             */
            if (isset($scenarioItem['dataFields'])) {

                foreach ($scenarioItem['dataFields'] as $key => $scenarioFilter) {

                    if ($key{0} == '@') {
                        if (!isset($scenarioItem['dataFields']['@'])) {
                            $scenarioItem['dataFields']['@'] = array();
                        }

                        $scenarioItem['dataFields']['@'][$key] = $scenarioFilter;
                        unset ($scenarioItem['dataFields'][$key]);

                    }
                }
            }

            $scenarioArray[$scenarioName] = $scenarioItem;

        }

        $this->parseScenario = $scenarioArray;

    }

    private function getScenarioItem($name)
    {


        if (!is_null($name))
            $result = $this->parseScenario[$name];
        else
            $result = null;
        return $result;
    }

    private function removeHostFromUrl($url)
    {
        $url_data = parse_url($url);
        print_r($url_data);

        $url =

            (isset($url_data['path']) ? $url_data['path'] : '') .
            (isset($url_data['query']) ? '?' . $url_data['query'] : '') .
            (isset($url_data['fragment']) ? '#' . $url_data['fragment'] : '');

        return $url;

    }

    /**
     * Очищаем все старые процессы. Оставляем только последние 5
     */
    private function clearOldData()
    {
        $sql = "DELETE FROM webParser WHERE id<" . ($this->processId - 5);
        Yii::app()->db->createCommand($sql)->execute();

        $sql = "DELETE FROM webParserData WHERE processId<" . ($this->processId - 5);
        Yii::app()->db->createCommand($sql)->execute();

        $sql = "DELETE FROM webParserPage WHERE procId<" . ($this->processId - 5);
        Yii::app()->db->createCommand($sql)->execute();

        $sql = "DELETE FROM webParserScenarioTask WHERE processId<" . ($this->processId - 5);
        Yii::app()->db->createCommand($sql)->execute();

        $sql = "DELETE FROM webParserUrl WHERE procId<" . ($this->processId - 5);
        Yii::app()->db->createCommand($sql)->execute();
    }

    private function log($message)
    {
        Yii::log('    ' . $message, 'trace', 'webParser');
        Yii::getLogger()->flush(true);
    }

    private function logError($message)
    {
        Yii::log('!!!! ERROR !!!! ' . $message, 'trace', 'webParser');
        Yii::getLogger()->flush(true);
    }

    private function logVar($var)
    {
        Yii::log('
' . var_export($var, true), 'trace', 'webParser');
        Yii::getLogger()->flush(true);
    }


    public function getAllTaksCount()
    {
        $processId = $this->getProcessId();
        return Yii::app()->db->createCommand()
            ->select('count(id)')
            ->from('webParserScenarioTask')
            ->where('processId=:processId', array(':processId' => $processId))
            ->queryScalar();
    }

    public function getAllNewTaksCount()
    {
        $processId = $this->getProcessId();
        return Yii::app()->db->createCommand()
            ->select('count(id)')
            ->from('webParserScenarioTask')
            ->where('processId=:processId and taskStatus=:taskStatus', array(':processId' => $processId, ':taskStatus' => 'new'))
            ->queryScalar();
    }
}