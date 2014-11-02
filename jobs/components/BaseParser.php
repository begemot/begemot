<?php
/* Для того чтобы экземпляр класса рассмативался как файл для парсинга данных, его название должно заканчиваться на ParserJob.php (Пример названия: ArgoParserJob.php) */
class BaseParser extends BaseJob{
	protected $items = array();
    protected $time = 0;


	public function addItem($id, $price, $name, $text, $quantity)
    {

        $name = str_replace('&quot;',"\"",  $name);
        $text = str_replace('&quot;', "'",$text);

        $this->items[] = array(
            'id' => $id,
            'price' => $price,
            'name' => $name,
            'text' => $text,
            'quantity' => $quantity,
        );

    }

    public function runJob()
    {
        return true;
    }

    public function getLastParsedData()
    {   
        $file = file_get_contents(Yii::app()->basePath . "/../files/parsersData/" . $this->name . '.data');
        return json_decode($file);
    }


    // Передалать в save to file.
    public function saveToFile()
    {  
        $this->saveTime();

        $arr = array_merge(array('name' => $this->name), array('items' => $this->items));
    	
        $this->saveParserData($arr);
    }

    public function saveParserData($arrayToWrite)
    {
        $tempFile = fopen(Yii::app()->basePath . "/../files/parsersData/" . $this->name . '.data', 'w');

        fwrite($tempFile, json_encode($arrayToWrite)); 

        fclose($tempFile); 
    }

    public function saveTime()
    {   

        $this->time = time();
        $dir    = Yii::app()->basePath . "/../files/parsersData/";

        $files  = scandir($dir);

        $arr = array($this->name => $this->time);

        $tempFile = fopen($dir . 'time.txt', 'w');

        if (array_search('time.txt', $files)) {
            $json = json_decode(file_get_contents($dir . 'time.txt'));
            if (is_array($json)) {
                $arr = array_merge($json, $arr);
            }
            
        }

        fwrite($tempFile, json_encode($arr)); 

        fclose($tempFile); 


    }

    public function getTime(){
        if($this->time != 0){
            return $this->time;
        } else return "Нету времени";
    }

    public function setName($name)
    {
        $this->name = $name;
    }
}