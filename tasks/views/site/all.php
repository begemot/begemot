<main>
	<div class="container">
	
		<div class="sort-container">
		    <h1>Все задания</h1>
		    <div class="sort-block">
		        <span class="sort-text">Сортировать по:</span>
		        <?php 
		        	$priceOrder =  $popularOrder = $dateOrder = 'a'; 
		        	if ($sort == 'date' && $order == 'a') {
					    $dateOrder = 'd';
					} else if ($sort == 'price' && $order == 'a') {
					    $priceOrder = 'd';
					} else if ($sort == 'popular' && $order == 'a') {
					    $popularOrder = 'd';
					}
		        ?>
		        <a class="<?php if($sort == 'date') echo "active" ?>" href="<?php echo Yii::app()->createUrl($this->route, array('sort' => 'date', 'order' => $dateOrder))
		        ?>">дате <i class="fa fa-angle-<?php if($dateOrder == "a") echo "down"; else echo "up"?>" aria-hidden="true"></i></a>
		        <a class="<?php if($sort == 'price') echo "active" ?>" href="<?php echo Yii::app()->createUrl($this->route, array('sort' => 'price', 'order' => $priceOrder))?>">сумме <i class="fa fa-angle-<?php if($priceOrder == "a") echo "down"; else echo "up"?>" aria-hidden="true"></i></a>
		        <a class="<?php if($sort == 'popular') echo "active" ?>" href="<?php echo Yii::app()->createUrl($this->route, array('sort' => 'popular', 'order' => $popularOrder))?>">популярности <i class="fa fa-angle-<?php if($popularOrder == "a") echo "down"; else echo "up"?>" aria-hidden="true"></i></a>
		    </div>
		</div>
		<?php foreach ($tasks as $key => $taskHolder): ?>
			<?php if (count($taskHolder['data'])): ?>
				<div class="showall-container">
				    <h2><?php echo $taskHolder['title']?></h2>
				    <?php if (count($taskHolder['data']) > 6): ?>
					    <a class="showall" href="<?php echo Yii::app()->createUrl("/tasks/site/category", array('id' => $taskHolder['id']))?>">Показать все</a>
					<?php endif ?>
				</div>
				
				<div class="task-container">
					<?php $i=0; ?>
					<?php foreach ($taskHolder['data'] as $key => $task): ?>
					<?php if($i == 6) break; ?>
						<div class="task-block">
					        <img src="<?php echo $task->getItemMainPicture('mini')?>" alt="">
					        <div class="task-block__bg"></div>
					        <div class="task-block__descr">
					            <p><?php echo $task->title?></p>
					            <div class="price">
					            <?php if ($task->price): ?>
					            	<img class="icon-money" src="/img/icon-money.svg" onerror="this.onerror=null; this.src='img/icon-money.png'" alt=""><?php echo $task->price?> р.
					            <?php endif ?>
					                <span class="likes like-<?php echo $task->id?>"><?php echo $task->likes?></span>
					                <img class="icon-like" src="/img/icon-like.svg" onerror="this.onerror=null; this.src='img/icon-like.png'" alt="">
					            </div>
					        </div>
					        <div class="task-block__overlay">
					            <?php if ($task->done): ?>
                                    <a class="btn btn-border open-popup-link" data-to="#popup" href="<?php echo Yii::app()->createUrl("/tasks/site/view", array('itemName' => $task->title_t, 'id' => $task->id, 'catId' => $taskHolder['id']))?>">Посмотреть видео</a>
                                <?php else: ?>
                                    <a class="btn btn-border open-popup-link" data-to="#popup" href="<?php echo Yii::app()->createUrl('/payment/form', array('task_id' => $task->id))?>">Задонатить</a>
                                <?php endif ?>
					            <a class="btn" href="<?php echo Yii::app()->createUrl("/tasks/site/view", array('itemName' => $task->title_t, 'id' => $task->id, 'catId' => $taskHolder['id']))?>">Подробнее</a>
					            <div class="price">
					            	<?php if ($task->price): ?>
					            		<img class="icon-money" src="/img/icon-money.svg" onerror="this.onerror=null; this.src='img/icon-money.png'" alt=""><?php echo $task->price?> р.
					            	<?php endif?>
					                <?php $likeClass = (Yii::app()->user->isGuest) ? "" : "likes-increase" ?>
                                    <div class="<?php echo $likeClass ?>" data-id='<?php echo $task->id?>' data-option='1' data-elem='.like-<?php echo $task->id?>'>
					                    <span class="likes like-<?php echo $task->id?>"><?php echo $task->likes?></span>
						                <img class="icon-like" src="/img/icon-like.svg" onerror="this.onerror=null; this.src='img/icon-like.png'" alt="">
						                
					                </div>
					            </div>
					        </div>
					    </div>
					    <?php $i++ ?>
					<?php endforeach ?>
					
					<?php if (count($taskHolder['data']) > 6): ?>
						<div class="showmore-container">
					        <a class="showmore btn btn-border" href="<?php echo Yii::app()->createUrl("/tasks/site/category", array('id' => $taskHolder['id']))?>">Показать еще</a>
					    </div>
					<?php endif ?>
				    
				</div>

			<?php endif ?>
		<?php endforeach ?>

	</div>
</main>

<?php return  ?>
<?php echo Yii::app()->createUrl("/tasks/site/view", array('itemName' => $task->title_t, 'id' => $task->id, 'catId' => $taskHolder['id']))?>