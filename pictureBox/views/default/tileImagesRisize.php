<?php

$pBox = new PBox($id, $elementId);
$pictureId = $_REQUEST['pictureId'];
$image = $pBox->pictures[$pictureId];
//print_r($config);
?>

<?php
if (isset($image['title'])) unset ($image['title']);
if (isset($image['alt'])) unset ($image['alt']);
//if (isset($image['admin'])) unset ($image['admin']);


//$images =


?>
<div class="tabbable">
    <ul class="nav nav-tabs">
        <?php
        $index = 0;
        foreach ($config['imageFilters'] as $imageKey => $subImage):
            $continueKey = true;


            foreach ($subImage as $filter) {

                if (isset($filter['param']['width'])) {
                    $continueKey = false;
                    $filterWidth = $filter['param']['width'];
                    $filterHeight = $filter['param']['height'];
                    continue;
                }
            }

            if ($continueKey) continue;
            $index++;
            ?>
            <li data-image-filter="<?=$imageKey?>" class="<?= ($index == 1 ? 'active' : '') ?>"><a href="#<?= $index ?>"
                                                                data-toggle="tab"><?= $imageKey ?></a></li>
        <?php endforeach; ?>
    </ul>
    <div class="tab-content">


        <?php
        $index = 0;
        foreach ($config['imageFilters'] as $imageKey => $subImage):
            if ($imageKey == 'original') continue;
            /*
                 * Выводим только те картинки у которых
                 * хотя бы в одном филтре есть width и height
                 */
            $continueKey = true;


            foreach ($subImage as $filter) {

                if (isset($filter['param']['width'])) {
                    $continueKey = false;
                    $filterWidth = $filter['param']['width'];
                    $filterHeight = $filter['param']['height'];
                    continue;
                }
            }

            if ($continueKey) continue;


            $index++;


            ?>
            <div class="tab-pane <?=($index==1?'active':'');?>" id="<?= $index ?>">
                <?php


                ?>
                <?php if (isset($image[$imageKey])): ?>
                    <?php
                    $params = [
                        'id' => $id,
                        'imageKey' => $imageKey,
                        'elementId' => $elementId,
                        'pictureId' => $pictureId,
                        'imageKey' => $imageKey,
                        'filterHeight' => $filterHeight,
                        'filterWidth' => $filterWidth,
                        'image' => $image
                    ];
                    $this->renderPartial('existingImageLine', $params);
                    ?>
                <?php else: ?>
                    <?php
                    $params = [
                        'id' => $id,
                        'imageKey' => $imageKey,
                        'elementId' => $elementId,
                        'pictureId' => $pictureId,
                        'imageKey' => $imageKey
                    ];
                    $this->renderPartial('notExistingImageLine', $params);
                    ?>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

    </div>
</div>
<br/><br/><br/><br/>
