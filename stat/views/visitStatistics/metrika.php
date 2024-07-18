<?php
/* @var $this VisitStatisticsController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs = array(
    'Метрика',
);

$menuPath = Yii::getPathOfAlias('stat.views');

$this->menu = require_once($menuPath.DIRECTORY_SEPARATOR.'commonMenu.php');
?>

<h1>Метрика</h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
    'dataProvider'=>$dataProvider,
    'columns'=>array(
        'id',
        'domain',
        'counter_id',
        array(
            'class'=>'CButtonColumn',
        ),
    ),
)); ?>
