<?php
return array(
    'title'=>'Привязать скидку к разделу каталога',
   
    'elements'=>array(
        'targetId'=>array(
            'type'=>'dropdownlist',
            'items'=>CHtml::listData(CatCategory::model()->findAll(),'id','name'),
            'prompt'=>'Выберите значение:',
            'label'=>'Раздел каталога:',
        ),
        'discountId'=>array(
            'type'=>'hidden',

        ),
        'type'=>array(
            'type'=>'hidden',

        ),
    ),
 
    'buttons'=>array(
        'discountToCatSubmit'=>array(
            'type'=>'submit',
            'label'=>'Прикрепить',
        ),
    ),
); 
?>
