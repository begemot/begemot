<?php
Yii::import('pictureBox.components.PBox');

?>

<div id="promoWidget">
    <nav class="tab-nav"  id="per-slide-template">

    </nav>

    <div class='btn cycle-prev' id="leftBtn"></div>
    <div>
        <?php foreach ($promos as $promo):?>
             <div class="slide" data-cycle-pager-template="<a href=#><?php echo $promo->promo->title;?></a>">
                 <div id="contentImg">
                    <?php
                        /** @var PBox $PBox */
                        $PBox = new PBox('catalogPromo',$promo->promoId);
                        echo $PBox->getFirstImageHtml('standart');
                    ?>
                 </div>
                 <div id="content">
                     <h2><?php echo $promo->promo->title?></h2>
                     <?php echo $promo->promo->text?>
                 </div>
             </div>
        <?php endforeach;?>

    </div>
    <div class='btn' id="rightBtn"></div>


</div>


