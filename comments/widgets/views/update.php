<?php 
/**
 * @var Comment model
 */
?>

<div div class="popup__block zoom-anim-dialog popup-dialog addComment-block" style='max-width: 300px'>

    <h3>Изменения комментария</h3>


  <?php

    $id = (isset($id)) ? $this->id . "-" . $id : $this->id;
    $appendTo = "#comment-" . $newComment->comment_id; 

    $form=$this->beginWidget('UActiveForm', array(
        'action'=>Yii::app()->urlManager->createUrl('/comments/comment/update', array('id' => $newComment->comment_id)),
        'id'=>$id,
        //'htmlOptions'=>array('class'=>'art-comm-mess'),
        'enableClientValidation' => true,
        'clientOptions'=>array(
            'validateOnSubmit'=>true,
            'afterValidate' => 'js: function(form, data, hasError) { 
                if(!hasError) {
                    elem = $("' . $appendTo . '").parent();
                    $.ajax({
                        type: "POST",
                        dataType: "html",
                        url: form.attr("action"),
                        data: form.serialize(),
                        success: function(data){
                            //alert("Ваше сообщение отправлено.");
                            $(".mfp-close").trigger("click");
                            elem.html(data);
                            elem.fadeIn();
                            elem.find(".commentsOpen-button").trigger("click");
                            $(".addComment-block TEXTAREA").val("");
                        },
                        error: function (data) {
                            console.log("An error occurred.");
                            console.log(data);
                        },
                        beforeSend: function(){
                            elem.fadeOut();
                        }
                    });
                }
                else return false;
            }',
        ),


    )); ?>
    <?php echo $form->errorSummary($newComment); ?>
    <img class="addComment__img" src="<?php echo Yii::app()->user->miniAvatar ?>" alt="">
    <?php
    echo $form->hiddenField($newComment, 'owner_name');
    echo $form->hiddenField($newComment, 'owner_id');
    echo $form->hiddenField($newComment, 'parent_comment_id', array('class'=>'parent_comment_id'));
    ?>
    <?php if(Yii::app()->user->isGuest == true):?>
    <div class="row">
        <?php echo $form->labelEx($newComment, 'user_name'); ?>
        <?php echo $form->textField($newComment,'user_name', array('size'=>40)); ?>
        <?php echo $form->error($newComment,'user_name'); ?>
    </div>
    <div class="row">
        <?php echo $form->labelEx($newComment, 'user_email'); ?>
        <?php echo $form->textField($newComment,'user_email', array('size'=>40)); ?>
        <?php echo $form->error($newComment,'user_email'); ?>
    </div>
    <?php endif; ?>

    <div class="row">
        <?php echo $form->textArea($newComment, 'comment_text', array('rows' => 4, 'class'=>'form-control')); ?>
        <?php echo $form->error($newComment, 'comment_text'); ?>
    </div>
    <?php

        echo CHtml::submitButton('Обработать', array('class' => 'btn btn-border submitCommentForm', 'id' => 'submit-' . $id));
    ?>

   
    <?php $this->endWidget();?>
</div>
<?php return false ?>

