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

?>

<?php foreach ($config['imageFilters'] as $imageKey => $subImage): ?>
    <?php


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


    ?>
    <?php if (isset($image[$imageKey])): ?>
        <?php
        $params = [
            'id'=>$id,
            'imageKey'=>$imageKey,
            'elementId'=> $elementId,
            'pictureId'=>$pictureId,
            'imageKey'=>$imageKey,
            'filterHeight'=>$filterHeight,
            'filterWidth'=>$filterWidth,
            'image'=>$image
        ];
        $this->renderPartial('existingImageLine',$params);
        ?>
    <?php else: ?>
        <?php
            $params = [
                'id'=>$id,
                'imageKey'=>$imageKey,
                'elementId'=> $elementId,
                'pictureId'=>$pictureId,
                'imageKey'=>$imageKey
            ];
            $this->renderPartial('notExistingImageLine',$params);
        ?>
    <?php endif; ?>
<?php endforeach; ?>

<br/><br/><br/><br/>
