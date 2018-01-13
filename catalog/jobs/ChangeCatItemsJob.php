<?php

class ChangeCatItemsJob extends BaseJob
{

    protected $name = "ChangeCatItemsJob";
    protected $description = "description";

    public function getParameters()
    {

        return array(
            'file' => array('type' => 'input', 'name' => 'Название файла'),
            'naming' => array('type' => 'select', 'options' => array('1', '2', '3'), 'name' => 'Имя вообще')
        );
    }


    public function runJob($parameters = null)
    {
        $logMessage = 'Зашли!';
        Yii::log($logMessage, 'trace', 'cron');
        //if ( ! array_key_exists('parsers', Yii::app()->getModules())){ return false;};

        Yii::import('application.modules.parsers.models.*');

        $arrayOfJobs = array();

        foreach (glob(dirname(Yii::app()->request->scriptFile) . "/files/parsersData/*.data") as $file) {

            Yii::log("Старт сбора данных с " . $file, 'trace', 'catItemJob');
            $websiteName = Yii::app()->params['adminEmail'];
//            echo $file.'
//            ';
            $json = require($file);

            $filename = $json['name'];

            ParsersStock::model()->deleteAll(array('condition' => "`filename`='" . $filename . "'"));
            ParsersOtherFields::model()->deleteAll(array('condition' => "`filename`='" . $filename . "'"));

            $length = count($json['items']);

            foreach ($json['items'] as $itemParsed) {

                $new = new ParsersStock;
                $itemParsed = (array)$itemParsed;
                $itemParsed['filename'] = $filename;
                $itemParsed['name'] = substr($itemParsed['name'], 0, 255);

                if (ParsersLinking::model()->find(array(
                    'condition' => 'fromId=:fromId',
                    'params' => array(':fromId' => $itemParsed['id'])))
                ) {
                    $itemParsed['linked'] = 1;
                }

                if (isset($json['images'][$itemParsed['id']])) {

                    $hashes = [];
                    $images = [];
                    foreach ($json['images'][$itemParsed['id']] as $image) {

                        if (file_exists($image)) {
                            $hash = hash_file('md5', $image);
                            if (!in_array($hash, $hashes)) {
                                $images[] = $image;
                                $hashes[] = $hash;
                            }
                        }

                    }
                    $itemParsed['images'] = json_encode($images);
                }

                if (isset($json['childs'][$itemParsed['id']])) {

                    $itemParsed['parents'] = json_encode($json['childs'][$itemParsed['id']]);
                }

                if (isset($json['groups'][$itemParsed['id']])) {

                    $itemParsed['groups'] = json_encode($json['groups'][$itemParsed['id']]);
                }
//                echo 'Массив модификаций';
//                print_r($json['modifs']);
                if (isset($json['modifs'][$itemParsed['id']])) {
//                    echo 'Есть совпадение:';
                    print_r($json['modifs'][$itemParsed['id']]);
                }

//                echo 'ИД:';
                print_r($itemParsed);
                if (isset($json['modifs'][$itemParsed['id']])) {
                    $itemParsed['modifs'] = json_encode($json['modifs'][$itemParsed['id']]);
                    Yii::log("Модификации: " . $itemParsed['modifs'], 'trace', 'cron');
                }

                $new->attributes = $itemParsed;

                if (!$new->save()) {
                    Yii::log("Ошибка сохранения:", 'trace', 'cron');
                    Yii::log(print_r($new->getErrors(), true), 'trace', 'cron');
                } else {
                    Yii::log("Сохранил в базу запись с ID: " . $itemParsed['id'], 'trace', 'cron');

                    Yii::log("Проверяем есть ли дополнительные поля ", 'trace', 'cron');
                    if (isset($itemParsed['anotherParams'])) {

                        Yii::log("Есть " . count($itemParsed['anotherParams']) . " полей.", 'trace', 'cron');

                        $lastInsertId = $new->id;

                        foreach ($itemParsed['anotherParams'] as $name => $otherParamValue) {

                            $parsersOtherFields = new ParsersOtherFields();

                            $parsersOtherFields->name = $name;
                            $parsersOtherFields->value = $otherParamValue;
                            $parsersOtherFields->parsers_stock_id = $lastInsertId;
                            $parsersOtherFields->filename = $filename;
                            Yii::log("Пытаемся сохранить!", 'trace', 'cron');
                            if (!$parsersOtherFields->save()) {
                                Yii::log("Ошибка сохранения!", 'trace', 'cron');

                            } else {
                                Yii::log("Сохранилось!", 'trace', 'cron');
                            }

                        }

                    }


                }
            }

            $items = ParsersLinking::model()->findAllByAttributes(array('filename' => $filename), array('order' => 'id ASC'));

            if (!$items) {

//		      $to = Yii::app()->params['adminEmail'];
//		      if(Yii::app()->params['programmerEmail']){
//		      	$to = " ," . Yii::app()->params['programmerEmail'];
//		      }
//
//		      $subject = "Задание не удалось выполнить($filename)";
//
//		      $headers = "From: susan@example.com\r\n";
//		      $headers .= "Reply-To: susan@example.com\r\n";
//		      $headers .= "CC: susan@example.com\r\n";
//		      $headers .= "MIME-Version: 1.0\r\n";
//		      $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
//
//		      $message = "Не удалось найти карточек для парсера $filename";
//
//		      mail($to, $subject, $message, $headers);
//
//		      echo 'no changes';
                //return true;

                //exit();
            }

            $changed = array();
            Yii::import('application.modules.catalog.models.CatItemsToItems');
            $logMessage = 'Перебираем все спарсенные элементы и обновляем те что уже имеют привязку:';
            Yii::log($logMessage, 'trace', 'cron');
            foreach ($items as $item) {

                if (isset($item->linking) && isset($item->item)) {
                    $logMessage = $item->linking->name;
                    Yii::log($logMessage, 'trace', 'cron');

                    //Обновляем опции
                    $changedParents = '';

                    if (isset($item->linking->parents)) {
                        $parents = json_decode($item->linking->parents);
                        foreach ($parents as $parent) {

                            $parentModel = ParsersLinking::model()->find(array(
                                'condition' => 'fromId=:fromId',
                                'params' => array(':fromId' => $parent)
                            ));

                            $parentId = $parentModel->toId;
                            'Ищем связи по itemId в CatItemsToItems по:' . $parentId;
                            $relations = CatItemsToItems::model()->find(array(
                                'condition' => 'itemId=:parentId and toItemId=:toItemId',
                                'params' => array(':parentId' => $parentId, ':toItemId' => $item->item->id)));
                            'надено:' . count($relations);
                            if (count($relations) == 0) {
                                'Создаем связь!';
                                $currentItemId = $item->item->id;

                                $CatItemsToItems = new CatItemsToItems();

                                $CatItemsToItems->itemId = $parentId;
                                $CatItemsToItems->toItemId = $currentItemId;

                                $CatItemsToItems->save();

                                $changedParents .= $item->item->name . ", ";
                            }

                        }
                    }

                    //Обновляем цену
                    if ($item->linking->price != $item->item->price || $item->linking->quantity != $item->item->quantity) {

                        $changed[] = array(
                            'name' => $item->item->name,
                            'oldPrice' => $item->item->price,
                            'newPrice' => $item->linking->price,
                            'oldQuantity' => $item->item->quantity,
                            'newQuantity' => $item->linking->quantity,
                            'changedParents' => $changedParents
                        );
                        $item->item->price = $item->linking->price;
                        $item->item->quantity = $item->linking->quantity;
                        $item->item->save();
                    }

                    //Обновляем доп. поля
                    $attr = [
                        'parsers_stock_id' => $item->fromId
                    ];

                    $otherFields = ParsersOtherFields::model()->findAllByAttributes($attr);

                    if (is_array($otherFields) && count($otherFields) > 0) {
                        foreach ($otherFields as $otherField) {
                            $columnsArrray = $item->item->tableSchema->columnNames;
                            $columnsArrray = array_flip($columnsArrray);

                            if (isset($columnsArrray[$otherField->name])) {
                                $otherFieldName = $otherField->name;
                                $item->item->$otherFieldName = $otherField->value;
                                $item->item->save();
                            }
                        }
                    }


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
                $message .= '<thead><tr><td>Название</td><td>Старая цена</td><td>Новая цена</td><td>Старое наличие</td><td>Новое наличие</td><td>Эта карточка добавилась как опция для</td></tr></thead>';

                foreach ($changed as $item) {
                    $message .= "<tr>
		          <td>{$item['name']}</td>
		          <td>{$item['oldPrice']}</td>
		          <td>{$item['newPrice']}</td>
		          <td>{$item['oldQuantity']}</td>
		          <td>{$item['newQuantity']}</td>
		          <td>{$item['changedParents']}</td>
		        </tr>";
                }

                $message .= '</table>';
            } else {
                $message .= 'Нечего не поменялось';
            }
            $message .= '</body></html>';

            $subject = "Изменилось " . count($changed) . " карточек";

            if (mail($to, $subject, $message, $headers)) {
                //echo Yii::app()->params['adminEmail'];
            } else {
                //echo "no message";
            }
        }

        return true;

    }

}
