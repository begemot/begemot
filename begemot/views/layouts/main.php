    <!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="ru" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.8.2/angular.min.js"></script>


    <base href="<?=$_SERVER['HTTP_HOST']?>"/>
        
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body>
<div class="container-fluid" style="margin-top:50px;">

<?php

$begemotMenuArray = require_once (dirname(__FILE__).'/../menu.php');

$configPath = Yii::getPathOfAlias('webroot.protected.config');
$localMenuFile = $configPath.'/adminLocalMenu.php';
if (file_exists($localMenuFile)){
    $localMenu = require($localMenuFile);
    array_unshift($begemotMenuArray,$localMenu);
}

?>


<?php $this->widget('bootstrap.widgets.TbNavbar', array(
    'type'=>'inverse', // null or 'inverse'
    'brand'=>'Begemot',
    'brandUrl'=>'/begemot',
    'collapse'=>true, // requires bootstrap-responsive.css
    'fluid' => true,
    'items'=>array(
        array(
            'class'=>'bootstrap.widgets.TbMenu',

            'items'=>$begemotMenuArray,

        ),
    ),
)); ?>

        


	<?php

    echo $content;

    ?>





</div><!-- page -->

