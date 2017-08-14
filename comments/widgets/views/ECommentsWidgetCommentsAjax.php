<?php if(count($comments) > 0):?>
    <?php foreach($comments as $key => $comment):?>
        <?php $hideClass = ($key > 9) ? 'hide' : ''; ?>
        <div class="comment-block <?php echo $hideClass?>" id='comment-<?php echo $comment->comment_id?>'>
            <img class="comment-block__img" src="<?php echo $comment->getProfileImage();?>" alt="">
            <h5><?php echo $comment->userName;?></h5><span class="comment-block__date"><?php echo Yii::app()->dateFormatter->formatDateTime($comment->create_time, 'short', null);?></span>
            <?php if (false): ?>
                <?php if($this->adminMode === true):?>
                    <div class="admin-panel">
                        <!--<?php if($comment->status === null || $comment->status == Comment::STATUS_NOT_APPROWED) echo CHtml::link(Yii::t('CommentsModule.msg', 'approve'), Yii::app()->urlManager->createUrl(
                            CommentsModule::APPROVE_ACTION_ROUTE, array('id'=>$comment->comment_id)
                        ), array('class'=>'approve'));?> -->

                    </div>
                <?php endif; ?>
            <?php endif ?>
            
            <?php if (Yii::app()->user->isGuest === false): ?>
                <?php if ($this->adminMode === true || $comment->creator_id === Yii::app()->user->id): ?>
                    <br/>
                    <a href="<?php echo Yii::app()->urlManager->createUrl(
                        CommentsModule::UPDATE_ACTION_ROUTE, array('id'=>$comment->comment_id)
                    )?>"  data-to='#popup' class='open-popup-link'>Редактировать</a>
                    <a href="<?php echo Yii::app()->urlManager->createUrl(
                        CommentsModule::DELETE_ACTION_ROUTE, array('id'=>$comment->comment_id)
                    )?>" class="comment_delete">Удалить</a>
                    <!--<?php echo CHtml::link(Yii::t('CommentsModule.msg', 'delete'), Yii::app()->urlManager->createUrl(
                        CommentsModule::DELETE_ACTION_ROUTE, array('id'=>$comment->comment_id)
                    ), array('class'=>'delete'));?> -->
                <?php endif ?>
            <?php endif ?>
            <p><?php echo CHtml::encode($comment->comment_text);?></p>
            
            <div class="comment-block__dislike commentLikeOrDislike" data-id='<?php echo $comment->comment_id?>' data-option='0'><?php echo $comment->dislikes?></div>
            <div class="comment-block__like commentLikeOrDislike" data-id='<?php echo $comment->comment_id?>' data-option='1'><?php echo $comment->likes?></div>
        </div>

    <?php endforeach;?>

<?php else:?>
    <p><?php echo Yii::t('CommentsModule.msg', 'No comments');?></p>
<?php endif; ?>

<?php return false ?>
    <ul class="comments-list">
        <?php foreach($comments as $comment):?>
            <li id="comment-<?php echo $comment->comment_id; ?>">
                <div class="comment-header">
                    <?php echo $comment->userName;?>
                    <?php echo Yii::app()->dateFormatter->formatDateTime($comment->create_time);?>
                </div>
               
                <div>
                    <?php echo CHtml::encode($comment->comment_text);?>
                </div>
                <?php if(count($comment->childs) > 0 && $this->allowSubcommenting === true) $this->render('ECommentsWidgetComments', array('comments' => $comment->childs));?>
                <?php
                    if($this->allowSubcommenting === true && ($this->registeredOnly === false || Yii::app()->user->isGuest === false))
                    {

                        echo CHtml::link(Yii::t('CommentsModule.msg', 'Add comment'), '#', array('rel'=>$comment->comment_id, 'class'=>'add-comment'));
                    }
                ?>
            </li>
        <?php endforeach;?>
    </ul>