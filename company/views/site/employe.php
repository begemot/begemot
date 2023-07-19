<?php

Yii::import('company.models.*');


$emps = CompanyEmployee::model()->findByPK();

?>
<h1>Врачи и сотрудники клиники Нуклеомед</h1>
<?php foreach ($emps as $emp):
    Yii::import('pictureBox.components.PBox');
    $pBox = new PBox('companyEmployee', $emp->id);
    $keyEmpHaveImage = true;
    if (count($pBox->pictures)!=0){
        $image = $pBox->getFirstImage('forDepart');
        $imageOriginal = $pBox->getFirstImage('original');
    } else {
        $keyEmpHaveImage = false;
    }

    ?>
    <div style="float:left;margin-bottom:30px;width:100%;">
        <?php if ($keyEmpHaveImage):?>
            <a href="<?=$imageOriginal?>"><img class="text_pic_a" title="<?= $emp->name; ?>" src="<?= $image ?>" alt="<?= $emp->name; ?>"></a>
        <?php endif; ?>
        <h2><?= $emp->name; ?></h2>

        <p><span style="font-size: small;"><?= $emp->text;?><br><a class="more"
                                                                   href="<?=Yii::app()->createUrl('company/site/emp',['title'=>$emp->nameT,'empId'=>$emp->id]);?>">подробнее</a></span>
        </p>
    </div>


<?php endforeach; ?>
