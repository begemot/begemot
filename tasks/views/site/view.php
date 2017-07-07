<?php
$this->pageTitle=Yii::app()->name . ' - Задание "' . $model->title . '"';
$this->breadcrumbs = array(
	$model->label(2) => array('index'),
	GxHtml::valueEx($model),
); ?>

<div class="container containerHeader">
    <h1 class="pad-bot40"><?php echo $model->title?></h1>
    <?php 
        $array = Yii::app()->authManager->getRoles(Yii::app()->user->id);
        reset($array);
        $first_key = key($array);
    ?>
    <?php if ($first_key == 'admin joystarter'): ?>
        <button class="btn btn-filled deleteOnConfirm" href="<?php echo Yii::app()->createUrl("/tasks/site/delete", array('id' => $model->id))?>" data-remove=".taskHeader, main, .deleteOnConfirm">Удалить задание<i class= aria-hidden="true"></i></button>
        <br/>
        <br/>
    <?php endif ?>
</div>
<section class="taskHeader">
    <div class="img-block">
        <img class="fullscreen" src="<?php echo $model->getItemMainPicture('main')?>" alt="">
        <div class="taskHeader__overlay">
            <div class="taskHeader__info">
                <img class="user__img" src="<?php echo Image::getProfileImage($model->user_id)?>" alt="">
                <div class="taskHeader_ownner">
                    <h5><?php echo $model->user->profile->Name ?> <?php echo $model->user->profile->Lastname ?></h5>
                    <p><i class="fa fa-calendar-minus-o"></i><?php echo date('d.m.Y', $model->create_time)?></p>
                </div>
                <div class="likes-block">
                    <?php $taskLikeOrDislike = (Yii::app()->user->isGuest) ? "" : "taskLikeOrDislike" ?>
                    <span class="likes-block__dislike <?php echo $taskLikeOrDislike?>" data-id='<?php echo $model->id?>' data-option='0'><?php echo $model->dislikes?></span>
                    <span class="likes-block__like <?php echo $taskLikeOrDislike?>" data-id='<?php echo $model->id?>' data-option='1'><?php echo $model->likes?></span>
                </div>
            </div>
        </div>
    </div>
    <div class="taskHeader__buttons">

        <?php if ($getWhoWillDoExists = $model->getWhoWillDo()): //estj ispolnitelj?>
            <?php if ($model->willDoId != 0): ?>
                <?php if ($model->donated >= $model->price && $model->done == 1): ?>
                    <p class="taskHeader__buttons_title">выполнено</p>
                <?php else: ?>
                    <p class="taskHeader__buttons_title">выполняется</p>
                <?php endif ?>

                <div class="contractor-block">
                    <img src="<?php echo Image::getProfileImage($model->willDoId);?>" alt="">
                    <h5><?php echo ModelHelper::getFullName($model->willDoId);?></h5>
                    <p>исполнитель</p>
                </div>

            <?php else: ?>
                <p class="taskHeader__buttons_title">сбор денег
                    <a class="showDonats" href="#donatq">
                        <span class="tablet-text">История донатов</span>
                        <span class="mobile-text">донаты</span> 
                        <i class="fa fa-long-arrow-right" aria-hidden="true"></i>
                    </a>
                </p>

            <?php endif; ?>

            
        <?php endif; //estj ispolnitelj?>

        <?php if ($model->price > 0): ?>
            <p class="price"><?php echo $model->price?> р.</p>
        <?php endif ?>
        


        
        
        <?php if ($getWhoWillDoExists && $model->willDoId == 0): //estj ispolnitelj?>
            <p>Собрано <?php echo $model->donated ?> руб. / <?php echo $model->donatedCount ?> донатеров</p>
            <?php if ($model->price > 0): ?>
                <div class="slider-block">
                    <div class="slider">
                        <div class="slider-range" style="width:<?php echo $model->getDonatedPercent() ?>%"></div>
                    </div>
                   
                    <span class="slider-text"><?php echo $model->getDonatedPercent() ?> %</span>
                </div>
            <?php endif ?>
             <a class="btn btn-border open-popup-link" data-to="#popup" href="<?php echo Yii::app()->createUrl('/payment/form', array('task_id' => $model->id))?>">Задонатить</a>
             <a class="subscribe open-popup-link" data-to="#popup" href="<?php echo Yii::app()->createUrl('/tasks/site/subscribe', array('task_id' => $model->id))?>">Подписаться на рассылку</a>
        <?php endif ?>


       
       <?php if ($model->willDoId == 0): ?>
           <a class="btn btn-filled open-popup-link" data-to="#popup" href="<?php echo Yii::app()->createUrl('/tasks/site/willDo', array('task_id' => $model->id))?>">Хочу выполнить</a>
       <?php endif ?>

       <br/><br/>

        <?php if (!$getWhoWillDoExists): ?>
            <a class="subscribe2 open-popup-link" data-to="#popup" href="<?php echo Yii::app()->createUrl('/tasks/site/subscribe', array('task_id' => $model->id))?>">Подписаться на рассылку</a>
        <?php endif ?>
    </div>
</section>

<main class="hasSidebar">
    <?php if (false): ?>
        <div class="share-block">
            <span>Поделиться</span>
            <!-- Put this script tag to the <head> of your page -->
            <script type="text/javascript" src="http://vk.com/js/api/share.js?94" charset="windows-1251"></script>

            <!-- Put this script tag to the place, where the Share button will be -->
            <script type="text/javascript"><!--
            document.write(VK.Share.button(false,{type: "round", text: 'Сохранить'}));
            --></script>

            <a class="twitter-share-button" href="https://twitter.com/intent/tweet?text=#<?php echo $model->title?>"><i class="fa fa-twitter" aria-hidden="true"></i>90</a>
            <div class="fb-share-button" data-href="http://joystarter.ru" data-layout="button_count" data-size="small" data-mobile-iframe="true"><a class="fb-xfbml-parse-ignore" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo Yii::app()->createUrl(Yii::app()->controller->getId().'/'.Yii::app()->controller->getAction()->getId() , $_GET );?>%2F&amp;src=sdkpreparse">Share</a></div>

            <!-- <a href=""><i class="fa fa-instagram" aria-hidden="true"></i>75</a> -->

        </div>
    <?php endif ?>
    
    <div class="share-block">
        <span>Поделиться</span>
        <a href="http://vk.com/share.php?url=<?php echo $_SERVER['REQUEST_URI'] ?>"><i class="fa fa-vk" aria-hidden="true"></i></a>
        <a href="https://twitter.com/intent/tweet?text=<?php echo $model->title?>"><i class="fa fa-twitter" aria-hidden="true"></i></a>
        <a href="http://www.facebook.com/share.php?u=<?php echo $_SERVER['REQUEST_URI'] ?>&title=<?php echo $model->title?>"><i class="fa fa-facebook" aria-hidden="true"></i></a>

    </div>

    <h2>Описание</h2>
    <p><?php echo $model->text ?></p>

    <div class="canDoTask-container">
        <h3><span>Могут выполнить<sup><?php echo count($model->user_tasks);?></sup></span></h3>
        <?php if (count($model->user_tasks)): ?>
            <?php foreach ($model->user_tasks as $key => $user_task): ?>
                <div class="canDoTask-block">
                    <img class="canDoTask__img" src="<?php echo Image::getProfileImage($user_task->user_id);?>" alt="">
                    <h5><?php echo ModelHelper::getFullName($user_task->user_id);?></h5>
                    <p class="canDoTask__conditions">Выполню за: <span class="red-text"><?php echo $user_task->price?> р.</span></p>
                    <p><?php echo $user_task->text?></p>
                    <p class="vote-block">
                        <span class='voteCount'><?php echo $user_task->likes?></span> голосов
                        <?php if (!Yii::app()->user->isGuest): ?>
                            <?php if (!$user_task->isVoted()): ?>
                                <a href="#" class="btn btn-border taskToUserLike" data-id='<?php echo $user_task->id?>'>Проголосовать</a>    
                            <?php endif ?>
                        <?php endif ?>
                    </p>

                    <?php if ($user_task->video_link != ""): ?>
                        <div class="canDoTask__video">
                            <iframe src="https://www.youtube.com/embed/<?php echo $user_task->video_link?>" frameborder="0" allowfullscreen></iframe>
                            <!-- <div class="overlay"></div>
                            <i class="fa fa-play-circle-o play2" aria-hidden="true"></i> -->
                        </div>
                    <?php endif ?>
                    
                </div>
            <?php endforeach ?>
        <?php endif ?>
    </div>

    <?php $this->widget('comments.widgets.ECommentsListWidget', array(
    	'model' => $model,
	)); ?>

    <aside class="sidebar">
        <?php if (isset($model->payments)): ?>
            <h3 id='donatq'><span>Донаты<sup><?php echo count($model->payments)?></sup></span></h3>

            <?php foreach ($model->payments as $key => $payment): ?>
                <?php $hideClass = ($key > 9) ? 'hide' : ''; ?>
                <div class="donater <?php echo $hideClass?>">
                    <img src="<?php echo Image::getProfileImage($payment->user_id);?>" alt="">
                    <h5><?php echo ModelHelper::getFullName($payment->user_id)?></h5>
                    <p><i class="fa fa-calendar-minus-o"></i><?php echo date('d.m.Y', strtotime($payment->paid_at))?></p>
                    <div class="price"><?php echo $payment->amount?> р.</div>
                </div>
            <?php endforeach ?>
            
            <?php if (count($model->payments) > 9): ?>
                <a class="showall showHidden" data-elem='.donater.hide' href="#"><span>показать все</span></a>
            <?php endif ?>
            
        <?php else: ?>
            <h3><span>Донатов пока нету</span></h3>
        <?php endif ?>
        
    </aside>
</main>
