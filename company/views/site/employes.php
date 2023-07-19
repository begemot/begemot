123123<?php

Yii::import('company.models.*');


$departs = CompanyDepart::model()->findAll(['order' => '`order`']);

?>
<h1>Врачи и сотрудники клиники Нуклеомед</h1>
<?php foreach ($departs as $depart):

    $departEmps = $depart->emps;
    if (count($departEmps)==0) continue;

    ?>
    <h2><?= $depart->name ?></h2>
    <?php
    foreach ($departEmps as $departEmp):?>
        <h3><?= $departEmp->name; ?></h3>


    <?php endforeach; ?>


<?php endforeach; ?>
