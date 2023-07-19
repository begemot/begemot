<?php
return array(
    'title'=>'Привязать акцию к разделу каталога',
   
    'elements'=>array(
        'targetId'=>array(
            'type'=>'dropdownlist',
            'items'=>CHtml::listData(CatCategory::model()->findAll(),'id','name'),
            'prompt'=>'Выберите значение:',
            'label'=>'Раздел каталога:',
        ),
        'promoId'=>array(
            'type'=>'hidden',

        ),
        'type'=>array(
            'type'=>'hidden',

        ),
    ),
 
    'buttons'=>array(
        'promoToCatSubmit'=>array(
            'type'=>'submit',
            'label'=>'Прикрепить',
        ),
    ),
); 
?>
