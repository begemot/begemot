<?php
class ParseBase{

    public $dir = '';


    public function __construct()
    {
        $this->dir = Yii::app()->basePath . '/config/';
    }

    public function addJob($filename, $period = 86400)
    {
        $this->time = time();

        //every day = 86 400 sec
        //every week = 604 800 sec
        //2 days in a week = 302 400 sec
        $arr = array(
            $filename => array(
                'period' => $period,
                'lastExecuted' => '0',
            )
        );

        $all = (array) $this->getListJob();

        $arr = array_merge($all, $arr);

        $this->writeFile($arr);

    }

    public function removeJob($filename)
    {
        $all = (array) $this->getListJob();

        if ($all[$filename]) {
            unset($all[$filename]);

            $this->writeFile($all);
        }

    }

    public function timeToString($time)
    {

        $string = '';

        if ($time == 86400) {
            $string = 'Каждый день';
        } else if ($time == 604800){
            $string = 'Раз в неделю';
        } else if ($time == 302400){
            $string = 'Два раза в неделю';
        } else{
            $string = 'Не известно';
        }

        return $string;
    }

    private function writeFile($arrayToWrite)
    {
        $tempFile = fopen($this->dir . 'cronConfig.php', 'w');

        fwrite($tempFile, json_encode($arrayToWrite));

        fclose($tempFile);
    }

    public function getListJob()
    {

        $array = array();

        $files  = scandir($this->dir);

        $tempFile = fopen($this->dir . 'cronConfig.php', 'r');

        if (array_search('cronConfig.php', $files)) {
            $json = json_decode(file_get_contents($this->dir . 'cronConfig.php'));

            $array = $json;


        }

        fclose($tempFile);


        return $array;
    }

    public function runAll()
    {
        $all = $this->getListJob();
        $save = array();

        if ($all) {
            foreach ($all as $key => $item) {

                $item = (array) $item;
                if (($item['lastExecuted'] + $item['period']) < time()) {
                    $this->runJob($key);

                    $item['lastExecuted'] = mktime(0, 0, 0, date('n'), date('j'));


                    echo "run" . time() .  " - " . $key .  " - " . $item['lastExecuted'];
                }
                else echo 'no run' . time();

                $save[$key] = $item;
            }

            print_r($save);


            $this->writeFile($save);
        }

    }

    private function runJob($filename)
    {
        $websiteName = 'rosvezdehod.ru';

        $json = file_get_contents('http://'. $websiteName . "/parsers/" . $filename . "?newDate");
        $json = json_decode($json);

        ParsersStock::model()->deleteAll(array('condition' => "`filename`='" . $json->name . "'"));

        $length = count($json->items);

        foreach ($json->items as $itemParsed) {
            $new = new ParsersStock;
            $itemParsed = (array)$itemParsed;
            $itemParsed['filename'] = $json->name;
            $itemParsed['name'] = substr($itemParsed['name'], 0, 99);

            if (ParsersLinking::model()->find(array(
                'condition'=>'fromId=:fromId',
                'params'=>array(':fromId'=>$itemParsed['id'])))
            ) {
                $itemParsed['linked'] = 1;
            }

            $new->attributes = $itemParsed;

            $new->save();
        }

        $items = ParsersLinking::model()->findAllByAttributes(array('filename' => $filename), array('order' => 'id ASC'));

        if (!$items) {

            $to = Yii::app()->params['adminEmail'];

            $subject = "Задание не удалось выполнить($filename)";

            $headers = "From: susan@example.com\r\n";
            $headers .= "Reply-To: susan@example.com\r\n";
            $headers .= "CC: susan@example.com\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

            $message = "Не удалось найти карточек для парсера $filename";

            mail($to, $subject, $message, $headers);

            echo 'no changes';
            return false;
        }

        $changed = array();
        foreach ($items as $item) {
            if ($item->linking->price != $item->item->price || $item->linking->quantity != $item->item->quantity) {

                $changed[] = array(
                    'name' => $item->item->name,
                    'oldPrice' => $item->item->price,
                    'newPrice' => $item->linking->price,
                    'oldQuantity' => $item->item->quantity,
                    'newQuantity' => $item->linking->quantity,
                );
                $item->item->price = $item->linking->price;
                $item->item->quantity = $item->linking->quantity;
                $item->item->save();
            }
        }

        $to = Yii::app()->params['adminEmail'];



        $headers = "From: susan@example.com\r\n";
        $headers .= "Reply-To: susan@example.com\r\n";
        $headers .= "CC: susan@example.com\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

        $message = '<html><body>';
        $message .= '<h1>test</h1>';
        if ($changed) {
            $message .= '<table>';
            $message .= '<thead><tr><td>Название</td><td>Старая цена</td><td>Новая цена</td><td>Старое наличие</td><td>Новое наличие</td></tr></thead>';

            foreach ($changed as $item) {
                $message .= "<tr>
					<td>{$item['name']}</td>
					<td>{$item['oldPrice']}</td>
					<td>{$item['newPrice']}</td>
					<td>{$item['oldQuantity']}</td>
					<td>{$item['newQuantity']}</td>
				</tr>";
            }

            $message .= '</table>';
        } else{
            $message .= 'Нечего не поменялось';
        }
        $message .= '</body></html>';

        $subject = "Изменилось " . count($changed) . " карточек";

        if (mail($to, $subject, $message, $headers)) {
            echo Yii::app()->params['adminEmail'];
        } else{
            echo "no message";
        }
    }
}
