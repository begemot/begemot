<?php
require(dirname(__FILE__).'/../menu.php');


Yii::import('begemot.extensions.LogViewer');
echo $logFile = Yii::getPathOfAlias('webroot.protected.runtime').'/webParser.log';
$logViewer = new LogViewer($logFile);
$logViewer->reverse = true;
$logViewer->addColorLineRule('/.*Запускаем парсер.*/ui','red');
$logViewer->addColorLineRule('/.*Обрабатываем поле.*/ui','blue');
$logViewer->addColorLineRule('/.*\d+\/\d+\/\d+.*/ui','green');
?>
<h1>Просмотр логов и данных таблиц webParser</h1>
<?=$logViewer->report()?>