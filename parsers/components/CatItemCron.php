<?php 
class CatItemCron extends CrontabBase{


	private function runJob($filename)
	{
		parent::rundJob(); 
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
