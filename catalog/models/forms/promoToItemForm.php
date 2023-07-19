<?php
return array(
    'title'=>'Привязать акцию к позиции каталога',
   
    'elements'=>array(
        'targetId'=>array(
            'type'=>'text',

            'label'=>'Введите ID позиции',
        ),
        'promoId'=>array(
            'type'=>'hidden',

        ),
        'type'=>array(
            'type'=>'hidden',

        ),
    ),
 
    'buttons'=>array(
        'promoToItemSubmit'=>array(
            'type'=>'submit',
            'label'=>'Прикрепить',
        ),
    ),
); 
?>
