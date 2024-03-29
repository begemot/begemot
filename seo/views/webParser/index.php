<?php


?>


    <h2>Сбор контента всех страниц сайта</h2>
    <p>
        Используется для дальнейшего анализа.
    </p>

<?php
$this->menu = require dirname(__FILE__) . '/../commonMenu.php';
//echo $processId;


Yii::import('begemot.extensions.parser.*');
Yii::import('begemot.extensions.parser.models.*');

echo $site_name = $_SERVER['HTTP_HOST'];

Yii::log('    ЗАШЛИ В ОТОБРАЖЕНИЕ!', 'trace', 'webParser');

$parseScenario = [
    'allPages' => [

        'startUrl' => '/',

        'type' => WebParserDataEnums::TASK_TYPE_CRAWLER,

    ],


];

$webParser = new CWebParser('seoParser', $site_name, $parseScenario, $processId);
echo '
<p>
ID процесса:' . $webParser->getProcessId() . ',  
всего задач:' . $webParser->getAllTaksCount() . ',  
не обработано:' . $webParser->getAllNewTaksCount() . '

</p>
';

$webParser->addUrlFilter('#mailto#i');
$webParser->addUrlFilter('#catalog/site/buy/itemId#i');
$webParser->addUrlFilter('#\##i');

$webParser->addMime('image/jpeg');
$webParser->addMime('image/png');
$webParser->addMime('image/gif');
$webParser->tasksPerExecute = 10;
$webParser->isInterface = true;

$webParser->parse();



//$pageContent = $webParser->getPageContent('http://www.buggy-motor.ru/catalog/buggy_79.html');
//$webParser->getAllUrlFromPage($pageContent);
//echo  md5('123', true);

//echo 'Количество активных задач:'. $webParser->taskManager->getActiveTaskCount().'!!';

echo '<br>';
//foreach ($webParser->doneTasks as $doneTask){
//    echo $doneTask->id.'<br>';
//}
//echo '<pre>';
//print_r($webParser->filteredUrlArray);
//echo '</pre>';

Yii::log('    проверяем закончен ли процесс!', 'trace', 'webParser');
//echo $webParser->getProcessStatus();
if ($webParser->getProcessStatus() != 'done') {
    Yii::app()->db->createCommand()->truncateTable('seo_pages');
    Yii::app()->db->createCommand()->truncateTable('seo_links');
    Yii::app()->db->createCommand()->truncateTable('seo_tags');
    echo '<script>location.reload();</script>';
} else {

    $pages = WebParserPage::model()->findAll(
        [
            'condition' => 'procId=:procId and export=0',
            'limit' => 50,
            'params' => array(
                ':procId' => $processId)
        ]
    );

    $urls = WebParserLink::model()->findAll(
        [
            'condition' => 'procId=:procId and export=0',
            'limit' => 50,
            'params' => array(
                ':procId' => $processId)
        ]
    );
    if ($pages) {

        //экспорт страниц
        echo "Парсер закончил работать. Идет обработка данных!";
        echo "Импортируем страницы!";
        try {
            $i=0;
            foreach ($pages as $page) {
                echo $i++.' ';
                if ($page->mime == 'text/html') {

                    $seoPages = new SeoPages();
                    $seoPages->url = $page->url;

                    $seoPages->content = $page->content;

                    $seoPages->status = $page->http_code;

                    $seoPages->contentHash = $page->content_hash;
                    echo 123;
                    $seoPages->mime = $page->mime;

                    if ($seoPages->save()) {
                        $page->export = 1;
                        $page->save();
                    }
                } else {
                    $page->export = 1;
                    $page->save();
                }

            }
        } catch(Exception $e) {
           throw new Exception('Ошибка сохранения страниц!');
        }


        echo '<script>location.reload();</script>';
    } else if ($urls) {

        //экспорт ссылок
        echo "Парсер закончил работать. Идет обработка данных!";
        echo "Импортируем ссылки!";
        foreach ($urls as $url) {
            $seoLink = new SeoLinks();
            $seoLink->url = $url->sourceUrl;
            $seoLink->href = $url->url;
            $seoLink->anchor = $url->anchor;
            if ($seoLink->save()){
                $url->export = 1;
                $url->save();
            }
        }
        echo '<script>location.reload();</script>';
    } else {
        echo "Закончили!";
    }


}




