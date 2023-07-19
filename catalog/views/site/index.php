<?php
    echo $this->layout;
    $this->pageTitle = 'Заголовок каталога';
?>

    <h1>Аквариумы</h1>
    <div class="wrapper pad-top2">

        <?php foreach ($categories as $category){ ?>
        <?php
            $catHref = Yii::app()->urlManager->createUrl('catalog/site/categoryView',array('catId'=>$category->id,'title'=>$category->name_t));
        ?>

        <div class="col-1" style="margin-bottom: 20px;height:330px;">
            <figure><a   title="" href="<?php echo $catHref;?>"><img src="<?php echo $category->getCatMainPicture('small');?>" alt=""></a></figure>
            <div class="title4"><a href="<?php echo $catHref;?>"><?php echo $category->name;?></a></div>
            <?php echo mb_strcut($category->text, 0,150,'UTF-8').'...';?>
            <div>
            <a style="margin-top:10px;" href="<?php echo $catHref;?>" class="link1 link1-top">подробно</a>
            </div>
        </div>
        <?php }; ?>

    </div>