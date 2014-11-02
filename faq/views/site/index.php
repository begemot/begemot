<?php
/* @var $this SiteController */
/* @var $model ContactForm */
/* @var $form CActiveForm */


$this->pageTitle = 'Контакты. Продажа багги в Москве.';
$this->breadcrumbs = array(
    'Contact',
);
?>
<div class="contacts">
    <div class="main_container">

        <span class="titles">Вопросы</span>
         
        <div class="divide"></div>
<?php if (!Yii::app()->user->hasFlash('contact')): ?>
            <h3>FAQ:</h3>


         <?php foreach($answers as $answer): ?>
         
            <h1>Вопрос: <?php echo $answer->question; ?></h1>
            <br />
            <h2>Ответ: <?php echo $answer->answer; ?></h2>
            <br />
         
         <?php endforeach; ?>
         
<?php endif; ?>
        <div class="contacts_form">

            <h3>Задать вопрос</h3>

            <?php if (Yii::app()->user->hasFlash('contact')): ?>

                <div class="flash-success">
                    <?php echo Yii::app()->user->getFlash('contact'); ?>
                </div>

            <?php else: ?>

                <div class="form_container">

                    <?php $form = $this->beginWidget('CActiveForm', array(
                        'id' => 'contact-form',
                        'enableClientValidation' => true,
                        'clientOptions' => array(
                            'validateOnSubmit' => true,
                        ),
                    )); ?>
                    <?php echo $form->errorSummary($model); ?>

                    <div>
                        <?php echo $form->textField($model, 'name', array('placeholder' => 'Вас зовут')); ?>
                        <?php echo $form->error($model, 'name'); ?>
                        <div class="form_info">

                            <i>Ваше имя</i>
                        </div>
                    </div>                  


                    <div>
                        <?php echo $form->textField($model, 'email', array('placeholder' => 'E-mail')); ?>
                        <?php echo $form->error($model, 'email'); ?>

                        <div class="form_info">

                            <i>Ваша электронная почта</i>
                            <p class="e_mail">Вы получите ответ на <br>электронную почту.</p>
                        </div>
                    </div>
                    
                    <div>
                        <?php echo $form->textField($model, 'phone', array('placeholder' => 'Телефон')); ?>
                        <?php echo $form->error($model, 'phone'); ?>
                        <div class="form_info">

                            <i>Номер телефона</i>
                        </div>
                    </div>
                    <div class="txt_line">
                        <?php echo $form->textArea($model, 'question', array('rows' => 6, 'cols' => 50, 'placeholder' => 'Текст сообщения')); ?>
                        <?php echo $form->error($model, 'question'); ?>
                                                            
                        <div class="form_info">

                            <i>Текст сообщения</i>
                        </div>
                    </div>
                    
                    <?php echo CHtml::submitButton('Отправить'); ?>

                    <?php $this->endWidget(); ?>

                </div><!-- form -->

            <?php endif; ?>

        </div>
    </div>
</div>
