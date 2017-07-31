<div style="float:left;width:100%">
    <img data-is-filtered-image="1" class="ladybug_ant" pb-id="<?= $id ?>" pb-element-id="<?= $elementId ?>"
         pb-picture-id="<?= $pictureId ?>" style="float:left;" filter-height="<?= $filterHeight ?>"
         filter-width="<?= $filterWidth ?>"
         image-filter="<?= $imageKey ?>" src="<?= $image['original'] ?>"/>
    <div style="position: relative;width:<?= $filterWidth ?>px;height:<?= $filterHeight ?>px;overflow: hidden;">
        <img style="float:left;position: relative;max-width: none;" class="original"
             src="<?= $subImage . '?' . rand(1, 1000) ?>"/>
    </div>

</div>