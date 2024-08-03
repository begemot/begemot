<?php


$this->menu = require(dirname(__FILE__).'/../commonMenu.php');
?>

<h1><?php echo 'Update' ; ?></h1>

<?php
$this->renderPartial('_form', array(
		'model' => $model));
?>