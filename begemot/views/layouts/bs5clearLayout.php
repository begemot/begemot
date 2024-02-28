<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <base href="<?= $_SERVER['HTTP_HOST'] ?>"/>

    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body>

<?php Yii::app()->clientScript->registerScriptFile('/bower_components/angular/angular.min.js'); ?>
<?php //Yii::app()->clientScript->registerScriptFile('/bower_components/bootstrap/dist/js/bootstrap.min.js');?>
<?php Yii::app()->clientScript->registerScriptFile('/bower_components/bootstrap/dist/js/bootstrap.bundle.min.js', 2); ?>
<?php Yii::app()->clientScript->registerCssFile('/bower_components/bootstrap/dist/css/bootstrap.min.css'); ?>

<?php

$path = Yii::getPathOfAlias('begemot.views.default');
$this->renderFile($path . '/bs5topMenu.php', ['menu' => ['123123123']]);
//$this->renderPartial('begemot/default/bs5topMenu');
?>


<div class="container-fluid">
    <div class="row flex-nowrap">
        <div class="col-auto col-md-3 col-xl-2 px-sm-2 px-0 ">
            <div class="d-flex flex-column align-items-center align-items-sm-start px-3 pt-2  min-vh-100">
                    <h5><?= Yii::app()->controller->module->id; ?></h5>



                <?php
                    $path = Yii::getPathOfAlias('begemot.views.default');
                    $this->renderFile($path . '/leftMenu.php');
                ?>



                <!--                <div class="dropdown pb-4">-->
                <!--                    <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">-->
                <!--                        <img src="https://github.com/mdo.png" alt="hugenerd" width="30" height="30" class="rounded-circle">-->
                <!--                        <span class="d-none d-sm-inline mx-1">loser</span>-->
                <!--                    </a>-->
                <!--                    <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">-->
                <!--                        <li><a class="dropdown-item" href="#">New project...</a></li>-->
                <!--                        <li><a class="dropdown-item" href="#">Settings</a></li>-->
                <!--                        <li><a class="dropdown-item" href="#">Profile</a></li>-->
                <!--                        <li>-->
                <!--                            <hr class="dropdown-divider">-->
                <!--                        </li>-->
                <!--                        <li><a class="dropdown-item" href="#">Sign out</a></li>-->
                <!--                    </ul>-->
                <!--                </div>-->
            </div>
        </div>
        <div class="col py-3">
            <?php echo $content; ?>
        </div>
    </div>
</div>
</body>