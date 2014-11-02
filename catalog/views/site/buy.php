<?php

    Yii::import('pictureBox.components.PBox');
    $pBox = new PBox('catalogItem',$id);


?>

<section id="content">
    <div class="container_24">
        <div class="wrapper">
            <article class="grid_14">
                <h5>магазин</h5>
                <?php $form=$this->beginWidget('CActiveForm',array('id'=>'contact-form')); ?>
                    <?php echo $form->errorSummary($buyFormModel); ?>
                    <fieldset>
                        <label class="name">
                            <input name="BuyForm[name]" type="text" value="<?php echo ($buyFormModel->name==''?'Имя:':$buyFormModel->name);?>">

                        </label>
                        <label class="email">
                            <input name="BuyForm[email]" type="text" value="E-mail:">

                        </label>
                        <label class="phone">
                            <input name="BuyForm[phone]" type="text" value="Телефон:">

                        </label>
                        <label class="kol-bo">
                            <input name="BuyForm[count]" type="text" value="Колличество:">

                        </label>
                        <label class="message">
                            <textarea name="BuyForm[msg]">Сообщение:</textarea>

                        </label>
                        <?php if (Yii::app()->controller->module->capcha): ?>
                            <label class="capcha">
                                <div id="capcha">
                                    <?php $this->widget('CCaptcha', array('buttonLabel' => 'Новый код'))?>
                                </div>   
                                
                                <?php echo CHtml::activeTextField($buyFormModel, 'verifyCode', array('placeholder'=> 'Код проверки:'))?>
                            </label>
                        <?php endif ?>
                        
                        <div class="success" style="display: none;">Представленная форма контакта!<br>
                            <strong>Мы скоро Вам ответим.</strong>
                        </div>
                        <div class="buttons2">
                            <a href="javascript:;" data-type="reset" class="button" onClick="document.getElementById('contact-form').reset();"><span>сбросить</span></a>
                            <a href="javascript:;" data-type="submit" class="button green" onClick="document.getElementById('contact-form').submit();">отправить</a>
                        </div>
                        <input name="BuyForm[model]" type="hidden" value="<?php echo $item->name;?>">
                    </fieldset>

                <?php $this->endWidget(); ?>
            </article>
            <article class="grid_9 prefix_1">
               <span class="map_wrapper">
                  <img src="<?php  echo $pBox->getImage(0,'main');?>" class="textImg" alt="">
               </span>
                <h1><?php  echo $item->name;?></h1>
                <div class="kol_vo">
<!--                    <span>Количество:</span>-->
<!--                    <a href="javascript:;" class="button green">–</a>-->
<!--                    <span class="kol">1 <b>шт.</b></span>-->
<!--                    <a href="javascript:;" class="button green">+</a>-->
                    <p class="prise">OT <?php  echo number_format($item->price, 0, ',', ' ');?></p>
                </div>
            </article>
        </div>
    </div>
</section>