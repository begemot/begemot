<?php 



$menu = array(
	array('label' => 'Все парсеры', 'url' => array('/parsers/default/index')),

    array('label' => 'Все связи', 'url' => array('/parsers/default/linking')),


    array('label' => 'Cоздать новую связанную категорию', 'url'=>array('/parsers/parsersCategoryConnection/create')),
	array('label' => 'Все связанные категории', 'url'=>array('/parsers/parsersCategoryConnection/admin')),
    array('label' => 'Отладочная информация'),
    array('label' => 'Логи', 'url'=>array('/parsers/default/logView')),
    array('label' => 'Процессы', 'url'=>array('/parsers/data/webParserProcess')),
    array('label' => 'Задачи', 'url'=>array('/parsers/data/webParserScenarioTask')),

);

$this->menu= $menu;
 ?>