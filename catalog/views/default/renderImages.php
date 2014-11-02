<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$this->menu = require dirname(__FILE__) . '/../catItem/commonMenu.php';
?>

<h2>Пересобираем изображения каталога</h2>
<h3>Процесс пересборки<?php echo ($progress!=0?'( '.$progress.'% )':'');?></h3>
<?php $this->widget('bootstrap.widgets.TbProgress', array(
    'type'=>'danger', // 'info', 'success' or 'danger'
    'percent'=>$progress, // the progress
    'striped'=>true,
    'animated'=>true,
)); ?>

<?php

    $this->widget('bootstrap.widgets.TbButton', array(
        'label' => 'Запустить',
        'type' => 'primary', // null, 'primary', 'info', 'success', 'warning', 'danger' or 'inverse'
        'size' => 'small', // null, 'large', 'small' or 'mini'
        'url' => '/catalog/default/renderImages/action/start',
  
    ));
    
    $this->widget('bootstrap.widgets.TbButton', array(
        'label' => 'Пауза',
        'type' => 'warning', // null, 'primary', 'info', 'success', 'warning', 'danger' or 'inverse'
        'size' => 'small', // null, 'large', 'small' or 'mini'
        'url' => '/catalog/default/renderImages/action/stop',
    ));    
    
    $this->widget('bootstrap.widgets.TbButton', array(
        'label' => 'Удалить',
        'type' => 'danger', // null, 'primary', 'info', 'success', 'warning', 'danger' or 'inverse'
        'size' => 'small', // null, 'large', 'small' or 'mini'
        'url' => '/catalog/default/renderImages/action/stop',
    ));
?>



<?php if ($action=='complete'){?>
<?php
Yii::app()->user->setFlash('success', '<strong>Все прошло успешно!</strong> Изображения были успешно пересобраны.');
?>
<?php }?>
<hr>
<?php $this->widget('bootstrap.widgets.TbAlert', array(
        'block'=>true, // display a larger alert block?
        'fade'=>true, // use transitions?
        'closeText'=>'&times;', // close link text - if set to false, no close link is displayed
        'alerts'=>array( // configurations per alert type
            'success'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
        ),
    ));
?>
<?php if ($action=='start'||$action=='continue'){?>
<script>
    location.replace("/catalog/default/renderImages/action/continue"); 
</script>
<?php }?>