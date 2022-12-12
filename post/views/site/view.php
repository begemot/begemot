<?php
die('123');
$this->pageTitle = $model->title_seo;

$imagesData = Yii::app()->basePath . '/../files/pictureBox/posts/' . $model->id . '/data.php';

$image = '';
$firstImage = '';
$imagesOutput = '';

if (file_exists($imagesData)) {
    $imagesArray = require($imagesData);
    $imagesArray = $imagesArray['images'];
    $firstImage = array_shift($imagesArray);

    if (count($imagesArray)>0){
        foreach($imagesArray as $image){
            $imagesOutput.='<a href="'.$image['original'].'" rel="prettyPhoto[gallery1]"><img src="'.$image['inner_small'].'" /></a>';
        }
    }
        
}


    $tagsOutput = '';
    $tags = PostsTags::model()->findAll();
    foreach ($tags as $tag){

        $tagsOutput.='
                <li>
                    '.CHtml::link($tag->tag_name,array('/post/site/tagIndex','id'=>$tag->id,'title'=>$tag->tag_name_t)).'
                </li>';
    }


?>

<div id="content">
		<div class="container_12">
<!--			<div class="grid_3 left">-->
<!--				<ul class="menu">-->
<!--                                        --><?php //echo '<li>'.CHtml::link('Все',array('/posts')).'</li>'; ?>
<!--					--><?php //echo $tagsOutput; ?>
<!--				</ul>-->
<!--			</div>-->
			
			<div class="grid_8 right">
				<div class="top_content">
					<h1><?php echo $model->title;?></h1>
                                        <?php if ($firstImage!='') {?>
					<div class="img gallery">
						<a href="<?php echo $firstImage['original']; ?>" rel="prettyPhoto[gallery1]">
						<span></span>
						<img src="<?php echo $firstImage['inner_big']; ?>" /></a>
						<div class="small_img">
                                                    <?php echo $imagesOutput;?>
                                                </div>
						<pУвеличить фото</p>
					</div>
                                        <?php }?>
					<div class="text">
						<?php echo $model->text; ?>
					</div>
				</div>
			</div>
		</div>
	</div><!-- #content-->


<?php return;?>
<div class="wrapper">	<div id="content" class="grid_9 alpha right">

        <div class="right-indent">



            <article class="single-post">
                <h1><?php echo $model->title;?></h1>
                <figure class="featured-thumbnail large"><span class="img-wrap"><span class="f-thumb-wrap"><img width="619" height="388" src="<?php echo $image['bigInner']; ?>" class="attachment-post-thumbnail-xl wp-post-image" alt="<?php echo $image['alt']; ?>" title="<?php echo $image['title']; ?>" /></span></span></figure>                
                <div class="indent-right">
<?php echo $model->text; ?>
                </div><!--.post-content-->
            </article>




        </div>
    </div><!--#content-->
    <aside id="sidebar" class="grid_3 omega">
        <div id="categories-2" class="widget"><h3>Разделы</h3>		<ul>

<?php 
    $output = '';
    $tags = PostsTags::model()->findAll();
    foreach ($tags as $tag){
        
        $output.='
                <li class="cat-item cat-item-6">
                    '.CHtml::link($tag->tag_name,array('posts/tagIndex','id'=>$tag->id,'title'=>$tag->tag_name_t)).'
                  <!--  <a href="/posts/index/tagId/'.$tag->id.'" >'.$tag->tag_name.'</a>!-->
                </li>            
';
    }
    echo $output;
?>                
                
                


            </ul>
        </div></aside><!--sidebar-->						</div> 


