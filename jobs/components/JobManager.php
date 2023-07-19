<?php 


class JobManager extends CApplicationComponent{

	private $dir = '';


	public function __construct()
	{

		$logMessage = 'JobManager __construct - зашли в конструктор ';
		Yii::log($logMessage,'trace','cron');

		if ( ! is_writable(dirname(Yii::app()->request->scriptFile) . "/files/parsersData/")) {
			$logMessage = 'JobManager __construct -  '.dirname(Yii::app()->request->scriptFile) . "/files/parsersData/ нету прв для записи";
			Yii::log($logMessage,'trace','cron');
			throw new Exception(dirname(Yii::app()->request->scriptFile) . "/files/parsersData/ нету прв для записи" , 503);
		}

		//$this->dir = dirname(Yii::app()->request->scriptFile) . "/files/parsersData/";
		$this->dir = Yii::getPathOfAlias('webroot') . "/files/parsersData/";

		foreach(glob(Yii::app()->basePath . "/modules/*", GLOB_ONLYDIR) as $path) {    

            if(file_exists($path)){
                if(file_exists($path . "/jobs")){
                    Yii::import('application.modules.' . basename($path) . ".jobs.*");
                }
            }
            
        }
	}

	public function getPeriodOfItem($jobIndex)
	{
		$all = (array) $this->getListCronJob();

		if (array_key_exists($jobIndex, $all)) {
			$arr = '';
			$arr .= (isset($all[$jobIndex]['min'])) ? $all[$jobIndex]['min'] . "  " : "";
			$arr .= (isset($all[$jobIndex]['hour'])) ? $all[$jobIndex]['hour'] . "  " : "";
			$arr .= (isset($all[$jobIndex]['day'])) ? $all[$jobIndex]['day'] . "  " : "";
			$arr .= (isset($all[$jobIndex]['month'])) ? $all[$jobIndex]['month'] . "  " : "";
			$arr .= (isset($all[$jobIndex]['dayWeek'])) ? $all[$jobIndex]['dayWeek'] . "  " : "";

			return $arr;
		}
		else{
			return "ошибка";
		}
	}

	public function getAllJobs()
	{
		$itemClassesList = array();

		if($this->getListOfAllJobs()){
			foreach ($this->getListOfAllJobs() as $item) {
				$itemName = basename($item);

				$name = str_replace('.php', '', $itemName);
				$itemClassesList[] = $name;
			}
		}
		

		return $itemClassesList;
	}

	public static function getListOfAllJobs()
	{	
		$arrayOfJobs = array();
		if (glob(Yii::app()->basePath . "/jobs/*.php")) {
			foreach(glob(Yii::app()->basePath . "/jobs/*.php") as $path) {	
				$arrayOfJobs[] = $path;
			}
		}

		if (glob(Yii::app()->basePath . "/modules/*/jobs/*.php")) {
			foreach(glob(Yii::app()->basePath . "/modules/*/jobs/*.php") as $path) {	
				$arrayOfJobs[] = $path;
			}
		}
		
		return $arrayOfJobs;
	}

	public function newTask($parameters)
	{
        $logMessage = 'JobManager newTask - создаем задачу '.var_export($parameters,true);
        Yii::log($logMessage,'trace','cron');


		$parameters['lastExecuted'] = 0;
        //$parameters['lastExecuted'] = $yesterday + $parameters['time'] + $parameters['hour'] + $parameters['minutes'];
        $parameters['executable'] = true;

        $arrayIndex = $parameters['filename'] . " - " . time() ;
        $parameters['jobIndex'] = $arrayIndex;
        $arr = array(
	        $arrayIndex => $parameters
        );

        $all = (array) $this->getListCronJob();

        $arr = array_merge($all, $arr);

        $logMessage = 'Результат слияния массивов: '.var_export($arr,true);
        Yii::log($logMessage,'trace','cron');

        $this->saveConfigFile($arr);

        return 1;
        
	}

	public function changeTime($parameters)
	{
		$all = (array) $this->getListCronJob();
		$jobIndex = $parameters['name'];
		unset($parameters['name']);

		if (array_key_exists($jobIndex, $all)) {

			unset($all[$jobIndex]['min']);
			unset($all[$jobIndex]['hour']);
			unset($all[$jobIndex]['day']);
			unset($all[$jobIndex]['month']);
			unset($all[$jobIndex]['dayWeek']);
			//Присвоение всех параметров к уже существующим и создание новых если их не было.
			foreach ($parameters as $key => $param) {
				$all[$jobIndex][$key] = $param;
			}

			$this->saveConfigFile((array) $all);

			return $this->getPeriodOfItem($jobIndex);
		} else{
			return "error: нету такого индекса в заданиях";
		}
	}

	public function turnOff($jobIndex)
	{
		$all = (array) $this->getListCronJob();

		if (array_key_exists($jobIndex, $all)) {
			$all[$jobIndex]['executable'] = false;

			$this->saveConfigFile($all);

			return "Сохранено";
		} else{
			return "error: нету такого индекса в заданиях";
		}
	}

	public function turnOn($jobIndex)
	{
		$all = (array) $this->getListCronJob();

		if (array_key_exists($jobIndex, $all)) {
			$all[$jobIndex]['executable'] = true;

			$this->saveConfigFile($all);

			return "Сохранено";
		} else{
			return "error: нету такого индекса в заданиях";
		}
	}

	

	public function removeTask($filename)
	{
        $logMessage = 'JobManager removeTask - удаляем задачу '.$filename;
        Yii::log($logMessage,'trace','cron');

		$all = $this->getListCronJob();

		if (isset($all[$filename])) {
			unset($all[$filename]);

	        $this->saveConfigFile($all);
		}

		print_r($all);

    }

	public function saveConfigFile($arrayToWrite)
	{
//        $logMessage = 'Пишем в файл данные, путь: '.$this->dir . 'cronConfig.php';
//        Yii::log($logMessage,'trace','cron');
//        $logMessage = 'данные: '.var_export($arrayToWrite,true);
//        Yii::log($logMessage,'trace','cron');
		PictureBox::crPhpArr($arrayToWrite, $this->dir . 'cronConfig.php');
	}

	public function getListCronJob()
	{

		$array = array();

		$files  = scandir($this->dir);


		if (array_search('cronConfig.php', $files)) {

		    $array = require($this->dir . 'cronConfig.php');	    
		} 
		

		return $array;
	}

	public function changeTimeOfLastExecuted($name, $time = 0)
	{

        $dir    = Yii::app()->basePath . "/../files/parsersData/";

        if (!file_exists($dir)) {
            mkdir($dir, 0777);
        }
        $files  = scandir($dir);

        $arr = array($name => $time);

        if (array_search('time.txt', $files)) {
            $array = file_get_contents($dir . 'time.txt');
            if (is_array($array)) {
                $arr = array_merge($array, $arr);
            }

        }

        PictureBox::crPhpArr($arr, Yii::app()->basePath . "/../files/parsersData/time.txt");


		
		return true;
	}

	public function isTaskSettedUp($name)
	{
		$logMessage = 'function isTaskSettedUp - проверяем '.$name;
		Yii::log($logMessage,'trace','cron');
		return array_key_exists($name, $this->getListCronJob());
	}

	public function runAll()
	{

		$logMessage = 'JobManager runAll - начинаем проверку задач на запуск.';
		Yii::log($logMessage,'trace','cron');

		foreach(glob(Yii::app()->basePath . "/modules/*", GLOB_ONLYDIR) as $path) {    

            if(file_exists($path)){
                if(file_exists($path . "/jobs")){
                    Yii::import('application.modules.' . basename($path) . ".jobs.*");
                }
            }
            
        } 

        Yii::import('application.modules.parsers.components.*');
        Yii::import('application.modules.pictureBox.components.*');

		$all = $this->getListCronJob();
		$save = array();

		if ($all) {
			foreach ($all as $filename => $item) {

				$item = (array) $item;

				if ($item['executable'] == true) {
					if (isset($item['type'])&&$item['type']=='manual'){
						$logMessage = 'JobManager runAll - Проверяем задачу '.$filename.' Текущая метка:'.time();
						Yii::log($logMessage,'trace','cron');
						$logMessage = 'JobManager runAll - Пропускаем. Задача для ручного режима!';
						Yii::log($logMessage,'trace','cron');
						continue;
					}
                    $logMessage = 'JobManager runAll - Проверяем задачу '.$filename.' Текущая метка:'.time();
                    Yii::log($logMessage,'trace','cron');

                    if ($this->checkForStart($item)) {
						
						$className = $item['filename'];
						$classItem = new $className;
						//$classItem->runJob($filename);

                        $logMessage = 'JobManager runAll - запускаем '.$filename;
                        Yii::log($logMessage,'trace','cron');

						$classItem->runJob($item);
						$this->changeTimeOfLastExecuted($filename, time());

                        $tmpJobList = $this->getListCronJob();
                        if (isset($tmpJobList[$filename])){
                            $tmpJobList[$filename]['lastExecuted'] = time();
                            $this->saveConfigFile($tmpJobList);
                        }
						
						echo "Запуск задачи " . $filename .  " в " . time() . "\n";
					}
					else{
                        $logMessage = 'Не запускаем '.$filename;
                        Yii::log($logMessage,'trace','cron');
                        echo 'Не запустилась задача ' . $filename . "\n";
                    };
				}
				

				$save[$filename] = $item;
			}




			//$this->saveConfigFile($save);
		}
		

	}

	private function runJob()
	{
		return true;
	}
	/**
	 * 2-5 - проверит попадание в интервал от 2 до 5 включительно
	 * 1,2-10/3,7 - проверит на равенство 1,
	 * попадание в интервал от 2 до 10 с шагом 3
	 * то есть 2,5,8
	 * и проверит на равенство 7
	 * * - звездочка эмениться на полный интервал, для минут от 0 до 59 и т.д.
	 *
	 * Аналог записей crontab. Так же как и в ctontab при указании и дня недели и дня
	 * месяца возьмет будет проверять пересечение по логическому or. Все остальные
	 * по логическому and.
	 */
	public function checkForStart($cronItem){
		//print_r($cronItem);
		//Правильные интервалы значений в соответствии с date()
		$minInterval = '0-59'; // 'i' - минута
		$hourInterval = '0-23';// 'H' - час
		$dayInterval = '1-31';// 'd' - день
		$monthInterval = '1-12';//'m' - число дня месяца
		$dayWeekInterval = '0-6';// 'w' - порядковый номер дня недеи, 0 - воскресенье

		//Если чего то нет, то заменяем на общий интервал
		if (!isset($cronItem['min']) || $cronItem['min']==0){$cronItem['min']=$minInterval;}
		if (!isset($cronItem['hour']) || $cronItem['hour']==0){$cronItem['hour']=$hourInterval;}
		if (!isset($cronItem['day']) || $cronItem['day']==0){$cronItem['day']=$dayInterval;}
		if (!isset($cronItem['month']) || $cronItem['month']==0){$cronItem['month']=$monthInterval;}
		if (!isset($cronItem['dayWeek']) || $cronItem['dayWeek']==0){$cronItem['dayWeek']=$dayWeekInterval;}

		//Для удобства все звездочки заменяем на правильные интервалы
		$cronItem['min'] = str_replace ('*',$minInterval,$cronItem['min']);
		$cronItem['hour'] = str_replace ('*',$hourInterval,$cronItem['hour']);
		$cronItem['day'] = str_replace ('*',$dayInterval,$cronItem['day']);
		$cronItem['month'] = str_replace ('*',$monthInterval,$cronItem['month']);
		$cronItem['dayWeek'] = str_replace ('*',$dayWeekInterval,$cronItem['dayWeek']);

		//Собираем текущие значения. Минуту, час, день, месяц и день недели
		$actTime = time();
		$actMin = date('i',$actTime);
		$actHour = date('H',$actTime);
		$actDay = date('d',$actTime);
		$actMonth = date('m',$actTime);
		$actDayWeek = date('w',$actTime);


        $resultMinArray = $this->parseIntervalString($cronItem['min']);
        $resultHourArray = $this->parseIntervalString($cronItem['hour']);
        $resultDayArray = $this->parseIntervalString($cronItem['day']);
        $resultMonthArray = $this->parseIntervalString($cronItem['month']);
        $resultDayWeekArray = $this->parseIntervalString($cronItem['dayWeek']);



        $minFlag = false;
        if (array_search($actMin,$resultMinArray)!==false){
            $minFlag=true;

        }

        $hourFlag = false;
        if (array_search($actHour,$resultHourArray)!==false){
            $hourFlag=true;
        }

        $dayFlag = false;
        if (array_search($actDay,$resultDayArray)!==false){
            $dayFlag=true;
        }

        $monthFlag = false;
        if (array_search($actMonth,$resultMonthArray)!==false){
            $monthFlag=true;
        }

        $dayWeekFlag = false;
        if (array_search($actDayWeek,$resultDayWeekArray)!==false){
            $dayWeekFlag=true;
        }

        //Дни счетаем из дня недели и дня месяца. Эти флаги надо обрабатывать отдельно
        $commonDaysFlag = false;

        if (($cronItem['day']===$dayInterval) or ($cronItem['dayWeek']===$dayWeekInterval)){
          //Если один из интервалов дня *, то делаем and
            $commonDaysFlag = ($dayWeekFlag and $dayFlag);
        } else {
            //Если оба интервала дня отличны от базового, то делаем or
            $commonDaysFlag = ($dayWeekFlag or $dayFlag);
        }

        return $minFlag and $hourFlag and $commonDaysFlag and $monthFlag;

	}

	/**
     *
     * Преобразуем конечный интервал в массив отдельных чисел
     * для последующей простой проверки попадания в массив текущего
     * значения.
     *
	 * @param $interval Интервал вида x-y
	 * @param null $step Шаг который пишется через "/"
	 */
	private function processInterval($interval,$step){
        $intervalArray = explode('-',$interval);

        $resultIntervalArray = [];

        $start = $intervalArray[0];
        $end = $intervalArray[1];

        for ($i=$start;$i<=$end;){
            $resultIntervalArray[]=$i;
            $i=$i+$step;
        }


        return $resultIntervalArray;

	}

    /*
     * анализируем строку что бы получить массив всех конкретных значений
     *
     * если исходная строка 0,1,4-11/3
     *
     * то выходной массив должен получиться
     *
     * array(
     * 	0,1,4,7,10
     * )
     *
     * а потом просто перебором проверяем на равенство текущему значению.
     * По идее так проще будет отлаживать если что пойдет не так.
     */

    private function parseIntervalString($intervalStr){
        $resultDigitsArray = array();

        //разбиваем по запятым на набор интервалов
        $intervalArray = explode (',',$intervalStr);
       // print_r($intervalArray);
        //Набиваем этот массив всеми конкретными значениями которые найдем

        foreach ($intervalArray as $key => $intervalOfArray){
            //Проверяем на наличие "-"
            if(strstr($intervalOfArray,'-')){
                //есть дефис, значит интервал вида x-y/z, но шага / может и не быть
                //проверяем на наличие /

                $step = 1;
                //Разбиваем по /, если будет элемент с индексом 1, то это шаг
                $currentIntervalArray = explode('/',$intervalOfArray);
                if (isset($currentIntervalArray[1])){
                    $step = $currentIntervalArray[1];
                }


                $resultDigitsArray = array_merge($resultDigitsArray,$this->processInterval($currentIntervalArray[0],$step));
            } else{
                //если "-" нет, то просто проверяем на равенство
                $resultDigitsArray[] = $intervalOfArray;
            }
        }

        return array_merge($resultDigitsArray);
    }

}