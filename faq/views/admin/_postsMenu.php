<?php

$this->menu=array(
    array('label'=>Yii::t('FaqModule.faq','Create Faq'), 'url'=>array('admin/create')),
    array('label'=>Yii::t('FaqModule.faq','Manage Cat'), 'url'=>array('cats/')),
    array('label'=>'Добавить раздел', 'url'=>array('cats/create')),
    array('label'=>Yii::t('FaqModule.faq','Categs')),
    array('label'=>Yii::t('FaqModule.faq','Moderation') . Faq::getCount(), 'url'=>array('admin/index'),'itemOptions'=>array('style'=>'font-weight:bold;')),
);

$cats = FaqCats::model()->findAll();

foreach($cats as $cat){
   $this->menu[] = array('label'=>$cat->name, 'url'=>array('index', 'cid'=>$cat->id));
}
?>
