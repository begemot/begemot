<?php
/* @var $this ContentTaskController */
/* @var $model ContentTask */

$this->breadcrumbs = array(
    'Content Tasks' => array('index'),
    'Manage',
);

$this->menu = array(
    array('label' => 'List ContentTask', 'url' => array('index')),
    array('label' => 'Create ContentTask', 'url' => array('create')),
);


?>

<h1>Manage Content Tasks</h1>


<?php $this->widget('begemot.extensions.bootstrap.widgets.TbGridView', array(
    'id' => 'content-task-grid',
    'dataProvider' => $model->search(),
    'filter' => $model,
    'columns' => array(
        'id',

        [
            'class' => 'begemot.extensions.bootstrap.widgets.TbEditableColumn',
            'name' => 'name',
            'editable' => [
                'url' => $this->createUrl('/contentTask/contentTask/editableSaver'),]
        ],

        'type',

        array(
            'class' => 'begemot.extensions.bootstrap.widgets.TbButtonColumn',
            'template' => '{update}{delete}'
        ),
    ),
)); ?>
