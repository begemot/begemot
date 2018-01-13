<?php

require(dirname(__FILE__).'/../menu.php');


?>

    <h1>Manage Web Parsers</h1>




<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'id'=>'web-parser-grid',
    'dataProvider'=>$model->search(),
    'filter'=>$model,
    'columns'=>array(
        'id',
        'date',
        'report_text',
        'processTime',
        'pagesProcessed',
        'status',

    ),
)); ?>