<?php 
class JobManager extends CApplicationComponent{

	private $dir = '';


	public function __construct()
	{
		$this->dir = Yii::app()->basePath . '/config/';

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

			return $this->timeToString($all[$jobIndex]['period']);
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

        //every day = 86 400 sec
        //every week = 604 800 sec
        //2 days in a week = 302 400 sec

		$filename = $parameters['filename'];
        $parameters['lastExecuted'] = 0;
        $parameters['executable'] = true;

        unset($parameters['filename']);

        $arr = array(
	        $filename => $parameters
        );

        $all = (array) $this->getListCronJob();

        $arr = array_merge($all, $arr);

        $this->saveConfigFile($arr);

        return 1;
        
	}

	public function changeTime($jobIndex, $time = 86400, $hour = '1')
	{
		$all = (array) $this->getListCronJob();

		if (array_key_exists($jobIndex, $all)) {

			$all[$jobIndex]->time = $time;
			$all[$jobIndex]->hour = $hour;

			$this->saveConfigFile((array) $all);

			return $this::timeToString($time);
		} else{
			return "error: нету такого индекса в заданиях";
		}
	}

	public function turnOff($jobIndex)
	{
		$all = (array) $this->getListCronJob();

		if (array_key_exists($jobIndex, $all)) {
			$all[$jobIndex]->executable = false;

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
			$all[$jobIndex]->executable = true;

			$this->saveConfigFile($all);

			return "Сохранено";
		} else{
			return "error: нету такого индекса в заданиях";
		}
	}

	

	public function removeTask($filename)
	{
		$all = (array) $this->getListCronJob();

		if ($all[$filename]) {
			unset($all[$filename]);

	        $this->saveConfigFile($all);
		}

    }

	public static function timeToString($time)
	{

		$string = '';

		if ($time == 86400) {
			$string = 'Каждый день';
		} else if ($time == 604800){
			$string = 'Раз в неделю';
		} else if ($time == 302400){
			$string = 'Два раза в неделю';
		} else if ($time < 86400){
			$string = $time / 3600;
		} else{
			$string = 'Не известно';
		}


		return $string;
	}

	private function saveConfigFile($arrayToWrite)
	{
		$tempFile = fopen($this->dir . 'cronConfig.php', 'w');

        fwrite($tempFile, json_encode($arrayToWrite)); 

        fclose($tempFile); 
	}

	public function getListCronJob()
	{

		$array = array();

		$files  = scandir($this->dir);

		if (array_search('cronConfig.php', $files)) {
		    $json = json_decode(file_get_contents($this->dir . 'cronConfig.php'));
		    
		    $array = $json;

		    
		}
		

		return (array) $array;
	}

	public function changeTimeOfLastExecuted($name, $time = 0)
	{

		$array = array();
		$directory = dirname(Yii::app()->basePath) . "/parsers/history/";

		$files  = scandir($directory);

		

		if (array_search('time.txt', $files)) {
			$tempFile = fopen($directory . 'time.txt', 'c');

		    $json = json_decode(file_get_contents($directory . 'time.txt'));
		    //print_r($files);
		    $array = (array) $json;

		    if(is_array($array)){
		    	if (array_key_exists($name, $array)) {
			    	$array[$name] = $time;
			    }

			    fwrite($tempFile, json_encode($array)); 
		    }
		    

		    fclose($tempFile); 
		}
		
		return true;
	}

	public function isTaskSettedUp($name)
	{	
		return array_key_exists($name, $this->getListCronJob());
	}

	public function runAll()
	{


		foreach(glob(Yii::app()->basePath . "/modules/*", GLOB_ONLYDIR) as $path) {    

            if(file_exists($path)){
                if(file_exists($path . "/jobs")){
                    Yii::import('application.modules.' . basename($path) . ".jobs.*");
                }
            }
            
        } 

		$all = $this->getListCronJob();
		$save = array();

		if ($all) {
			foreach ($all as $filename => $item) {

				$item = (array) $item;

				if ($item['executable'] == true) {
						
					if (($item['lastExecuted'] + $item['time'] + $item['hour'] - 60) < time()) {
						
						$classItem = new $filename;
						//$classItem->runJob($filename);
						$classItem->runJob();
						$this->changeTimeOfLastExecuted($filename, time());

						$item['lastExecuted'] = (int)mktime(0, 0, 0, date('n'), date('j'));
						
						echo "run" . time() .  " - " . $filename .  " - " . $item['lastExecuted'];
					}
					else echo 'no run' . time();
				}
				

				$save[$filename] = $item;
			}

			print_r($save);


			$this->saveConfigFile($save);
		}
		

	}

	private function runJob()
	{
		return true;
	}
}