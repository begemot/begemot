<div id="comments" class="start-wrap cf">
    <div class="start">
        <?php $this->render('webroot.themes.' . Yii::app()->theme->name . '.ECommentsWidgetComments', array('comments' => $comments));?>
        <?php
            $this->widget('comments.widgets.ECommentsFormWidget', array(
            'model' => $this->model,
            ));
        ?>

    </div>
</div>

<?php return;?>

<div class="comment-widget" id="<?php echo $this->id?>">
<h3><?php echo Yii::t('CommentsModule.msg', 'Comments');?></h3>
<?php
    $this->render('webroot.themes.' . Yii::app()->theme->name . '.ECommentsWidgetComments', array('comments' => $comments));
    if($this->showPopupForm === true)
    {
        if($this->registeredOnly === false || Yii::app()->user->isGuest === false)
        {
            echo "<div id=\"addCommentDialog-$this->id\">";
                $this->widget('comments.widgets.ECommentsFormWidget', array(
                    'model' => $this->model,
                ));
            echo "</div>";
        }
    }
    if($this->registeredOnly === false || Yii::app()->user->isGuest === false)
    {
        echo CHtml::link(Yii::t('CommentsModule.msg', 'Add comment'), '#', array('rel'=>0, 'class'=>'add-comment'));
    }
    else
    {
        echo '<strong>'.Yii::t('CommentsModule.msg', 'You cannot add a new comment').'</strong>';
    }
?>
</div>
