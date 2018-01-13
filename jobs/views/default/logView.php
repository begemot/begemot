<?php
require(dirname(__FILE__).'/mainMenu.php');


Yii::import('begemot.extensions.LogViewer');
echo $logFile = Yii::getPathOfAlias('webroot.protected.runtime').'/cronLog.log';
$logViewer = new LogViewer($logFile);
//$logViewer->addColorLineRule('/.*Запускаем парсер.*/ui','red');
//$logViewer->addColorLineRule('/.*Обрабатываем поле.*/ui','blue');

?>
    <h1>Журнал запуска планировщика задач</h1>
<?=$logViewer->report()?>