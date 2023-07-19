<?php

$this->pageTitle = 'Акция'
?>
<section class="main" style="padding-top: 50px;">
    <div class="container">
        <div class="block-caption-main mb-40">


            <?php
            $pBox = new PBox('catalogPromo', $model->id);
            $image = $pBox->getFirstImage('main');

            ?>
            <div class="row align-items-center">
                <div class="item-shares col-md-6 col-md-offset-3">
                    <div class="img-wrapper"><img src="<?= $image ?>" alt=""></div>
                    <div class="block-description">

                    </div>
                </div>
            </div>
            <br>
            <h2 class="caption-main"><?= $model->title; ?></h2>
            <?= $model->text; ?>
        </div>

    </div>
</section>
