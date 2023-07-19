<?php
$this->breadcrumbs=array(
	'Callbacks',
);

$this->menu=array(
    array('label'=>'List Callback','url'=>array('index')),
     array('label'=>'Manage Callback','url'=>array('admin')),
);
?>

<h1>Callbacks</h1>

<table>
<?php //$this->widget('bootstrap.widgets.TbListView',array(
//	'dataProvider'=>$dataProvider,
//	'itemView'=>'_view',
//));
foreach($dataProvider->getData() as $lineData){
	echo '<tr>';

	echo '<td>'.$lineData['id'].'</td>';
	echo '<td>'.$lineData['title'].'</td>';
	echo '<td>'.$lineData['date'].'</td>';
	echo '<td>'.$lineData['text'].'</td>';
	echo '</tr>';
}

?>
</table>