<div>
    <h2><?= $imageKey ?></h2>[<a class="deleteFilteredImage" href="javascript:;">удалить</a>]
    <div style="float:left;width:100%">
        <img data-is-filtered-image="1" class="ladybug_ant" pb-id="<?= $id ?>" pb-element-id="<?= $elementId ?>"
             pb-picture-id="<?= $pictureId ?>" style="float:left;max-width:500px;" filter-height="<?= $filterHeight ?>"
             filter-width="<?= $filterWidth ?>"
             image-filter="<?= $imageKey ?>" src="<?= $image['original']. '?' . rand(1, 1000) ?> ?>"/>
        <div
            style="position: relative;width:<?= $filterWidth ?>px;height:<?= $filterHeight ?>px;overflow: hidden;">
            <img style="float:left;position: relative;max-width: none;" class="original"
                 src="<?= $image[$imageKey] . '?' . rand(1, 1000) ?>"/>
        </div>

    </div>
</div>