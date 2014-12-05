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
        $file = require(Yii::app()->basePath . "/../files/parsersData/" . $this->name . '.data');
        return $file;
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
        PictureBox::crPhpArr($arrayToWrite, Yii::app()->basePath . "/../files/parsersData/" . $this->name . '.data');
    }

    public function saveTime()
    {   

        $this->time = time();
        $dir    = Yii::app()->basePath . "/../files/parsersData/";

        if (!file_exists($dir)) {
            mkdir($dir, 0777);

        }
        $files  = scandir($dir);

        $arr = array($this->name => $this->time);

        if (array_search('time.txt', $files)) {
            $array = file_get_contents($dir . 'time.txt');
            if (is_array($array)) {
                $arr = array_merge($array, $arr);
            }
            
        }

        $this->saveParserData($arr);


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