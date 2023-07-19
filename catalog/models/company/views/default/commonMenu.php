<?php

$menuPart1 =

    array(
        array('label' => 'Навигация'),
        array('label' => 'Структурные подразделения', 'url' => array('/company/companyDepart/admin')),
        array('label' => 'Сотрудники', 'url' => array('/company/companyEmployee/admin')),


    );


$departs = CompanyDepart::model()->findAll();
$departMenu =[];
foreach ($departs as $depart){
    $departMenuItem=[];
    $departMenuItem['label']=$depart->name;
    $departMenuItem['url'] = array('/company/empToDep/adminForDepart','depId'=>$depart->id);

    $departMenu[]=$departMenuItem;
}

$menuPart2 = array(
     array('label' => 'Управление'),
        array('label' => 'Добавить подразделение', 'url' => array('/company/companyDepart/create')),
        array('label' => 'Добавить сотрудника', 'url' => array('/company/companyEmployee/create')),
    array('label'=>'Порядок вывода сотрудников по разделам'),

);

return array_merge($menuPart1, $menuPart2,$departMenu);
?>
