    <!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="ru" />



    <base href="<?=$_SERVER['HTTP_HOST']?>"/>
        
	<title>Begemot Content - система доставки контента</title>
</head>

<body>
<div class="container-fluid" style="margin-top:50px;">

<?php

$begemotMaenuArray = array(
    //array('label'=>'About', 'url'=>array('/site/page', 'view'=>'about')),


);

$configPath = Yii::getPathOfAlias('webroot.protected.config');
$localMenuFile = $configPath.'/adminLocalMenu.php';
if (file_exists($localMenuFile)){
    $localMenu = require($localMenuFile);
    array_unshift($begemotMaenuArray,$localMenu);
}

?>

        
<?php $this->widget('bootstrap.widgets.TbNavbar', array(
    'type'=>'inverse', // null or 'inverse'
    'brand'=>'Begemot Content',
    'brandUrl'=>'/begemot',
    'collapse'=>true, // requires bootstrap-responsive.css
    'fluid' => true,
    'items'=>array(
        array(
            'class'=>'bootstrap.widgets.TbMenu',
            'items'=>$begemotMaenuArray,
        ),
    ),
)); ?>
      
        


	<?php echo $content; ?>





</div><!-- page -->

