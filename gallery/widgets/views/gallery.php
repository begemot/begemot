<?php
Yii::import('pictureBox.components.PBox');
$pBox = new PBox('gallery',$gallery[0]->id);

$pictures = $pBox->getSortedImageList();

?>
<ul class="list-gallery clearfix">

    <?php foreach ($pictures as $imageArray):?>
        <?php
        if (!isset($imageArray['miniGallery'])) continue;
        $title = '';
        $alt = '';
        if (isset($imageArray['alt'])) {
            $alt = $imageArray['alt'];
        }
        if (isset($imageArray['title'])) {
            $title = $imageArray['title'];
        }
        ?>
        <li class="item col-md-4 col-sm-4 col-xs-6">
            <a href="<?= $imageArray['original'] ?>" class="box image-link">
                <div class="img-wrapper"><img src="<?= $imageArray['miniGallery'] ?>" alt="<?= $alt ?>" title="<?= $title ?>"></div>
                <div class="description">
                    <span class="icon-plus"><i class="fas fa-plus"></i></span>

                    <p class="title"><?= $alt ?></p>
                    <span class="category"><?= $title ?></span>
                </div>
            </a>
        </li>
    <?php endforeach;?>


</ul>
