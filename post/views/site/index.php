123<?php

$tagsOutput = '';
$tags = PostsTags::model()->findAll();

foreach ($tags as $tag) {

    $tagsOutput.='
                <li >
                    ' . CHtml::link($tag->tag_name, array('/post/site/tagIndex', 'id' => $tag->id, 'title' => $tag->tag_name_t)) . '
                 
                </li>            
';
}

$output = '';
Yii::import('begemot.extensions.RusDate');
foreach ($models as $model):

    $imagesData = Yii::app()->basePath . '/../files/pictureBox/posts/' . $model->id . '/data.php';
    $image = '';

    if (file_exists($imagesData)) {
        $imagesArray = require($imagesData);
        $image = array_shift(array_shift($imagesArray));

        if (is_array($image) && (count($image)) > 0)
            $imgHtml = '                    <div class="img">
                                                    
                                                    ' . CHtml::link('<span></span><img src="' . $image['inner_big'] . '" />', array('/post/site/view', 'id' => $model->id, 'title' => $model->title_t)) . '
                                    
                                            </div>';
        else
            $imgHtml = '';
    } else {
        $imgHtml = '';
    }

    $output .= '
   
        <br />
         ' . $imgHtml . '
        <h5 style="text-transform: none;">' 
            .CHtml::link($model->title, array('/post/site/view', 'id' => $model->id, 'title' => $model->title_t)) 
        .'</h5>
<p>' . mb_substr(html_entity_decode(strip_tags($model->text), ENT_COMPAT, 'UTF-8'), 0, 255, 'UTF-8') . '...</p>

' . CHtml::link('весь текст', array('/post/site/view', 'id' => $model->id, 'title' => $model->title_t), array('class' => 'button blue')) . '     

';

endforeach;

if ($tag_id!=null){
   $tagName = PostsTags::getTagName($tag_id);
} else{
    $tagName='Все статьи портала';
}
?>

<section id="content">
    <div class="container_24">
        <div class="wrapper m_bot4">
            <article class="grid_8">
                <ul class="list1 m_bot3">
                    <?php
                    echo '<li>' . CHtml::link('Все', array('/post/site/tagIndex')) . '</li>';
                    echo $tagsOutput;
                    ?>
                </ul>
            </article>
            <article class="grid_15 prefix_1">
                <h3><?php echo $tagName;?></h3>
                <div class="paginator">
                    <ul>                   
                    <?php
                    $this->widget('CLinkPager', array(
                        'pages'=>$pages,
                        'nextPageLabel'=>'туда',
                        'prevPageLabel'=>'сюда',
                        'lastPageLabel'=>'последняя',
                        'firstPageLabel'=>'первая',
                        'header'=>'',
                        'htmlOptions'=>array(
                            'class'=>'',
                        ),

                    ));
                    ?>
                    </ul>
                </div>
                <br />
                <?php echo $output; ?>
                <div class="paginator">
                    <ul>                   
                    <?php
                    $this->widget('CLinkPager', array(
                        'pages'=>$pages,
                        'nextPageLabel'=>'туда',
                        'prevPageLabel'=>'сюда',
                        'lastPageLabel'=>'последняя',
                        'firstPageLabel'=>'первая',
                        'header'=>'',
                        'htmlOptions'=>array(
                            'class'=>'',
                        ),

                    ));
                    ?>
                    </ul>
                </div>
            </article>
        </div>
</section>

<?php
return;
?>  


<div id="content">
    <div class="container_12">
        <div class="grid_3 left">

            <ul class="menu">

<?php
echo '<li>' . CHtml::link('Все', array('/post/site/index')) . '</li>';
echo $tagsOutput;
?>

            </ul>
        </div>

        <div class="grid_8 right">


            <div id="articles">
                <h3>Статьи</h3>
<?php echo $output; ?>

            </div>



        </div>
    </div>
</div><!-- #content-->



<?php
