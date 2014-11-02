<?php
    
   $this->pageTitle = $item->seo_title; 

?>      

                    <article class="grid_15">
                    	<div class="box">
                        	<div class="padding6">  
                                 <h1>A LITTLE BETTER</h1>
                                <a href="<?php echo Yii::app()->urlManager->createUrl('catalog/site/index');?>">Каталог</a> > 
                                <a href="<?php echo Yii::app()->urlManager->createUrl('catalog/site/categoryView',array('catId'=>$category->id,'title'=>$category->name_t));?>"><?php echo $category->name; ?></a> > <?php echo $item->name; ?>  
                                <h1><?php echo $item->name; ?></h1>
                                <?php echo $item->text; ?>
                            </div>
                        </div>
                    </article>
                    <article class="grid_9" >  
                        <?php  
                            $images = $item->getItemPictures();
                           
                     
       
                            foreach ($images as $image){
                        ?>
                        <figure class="fleft img-right" style="margin-bottom:10px;"><a class="lightbox-image" datagal="prettyPhoto[1]" title="<?php echo isset($image['title'])?$image['title']:'';?>" href="<?php echo $image['original'];?>"><img src="<?php echo $image['verySmall'];?>" alt="<?php echo isset($image['alt'])?$image['alt']:'';?>"></a></figure>
                        <?php }?>
                    </article>