<h1>Статьи</h1>


<?php
foreach ($models as $model){
    echo '<img src="/circle.gif" alt="">';
    echo'<a class="posts" href="'.Yii::app()->createUrl('post/site/view',['title'=>$model->title_t,'id'=>$model->id]).'">'.$model->title.'</a>';
    echo '<br>';
}