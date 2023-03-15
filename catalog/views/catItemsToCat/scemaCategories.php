<?php
$this->widget('bootstrap.widgets.TbGridView', array(
    'id' => 'test-grid',
    'dataProvider' => $dataProvider,




    'type' => 'striped bordered condensed',
    'columns' => array(
        'id','name'
    ),
));
