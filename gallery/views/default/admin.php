<?php

$this->menu = require dirname(__FILE__) . '/commonMenu.php';


?>

<h1>Управление фотогаллереями</h1>



<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'id' => 'gallery-grid',
    'dataProvider' => $model->search(),
    'filter' => $model,
    'columns' => array(

        'name',

        array(
            'class' => 'bootstrap.widgets.TbButtonColumn',
        ),
        array(
            'class' => 'begemot.extensions.order.gridView.CBOrderColumn',
            "header" => "порядок",
        ),
    ),
)); ?>
