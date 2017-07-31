<?php

$params =  ['itemId' => $paentItemId];


$options = CatItemsToItems::model()->findAllByAttributes(
    $params
   );
//
$connectedOptionsIdsArray = [];
foreach ($alreadyConnetedOptions as $alreadyConnetedOptions){
    $connectedOptionsIdsArray[$alreadyConnetedOptions->toItemId]='1';
}

$checkedOptions = [];
$uncheckedOptions = [];

foreach ($options as $option){
    if (isset($connectedOptionsIdsArray[$option->toItem->id])){

        $checkedOptions[]=$option;
    } else {
        $uncheckedOptions[]=$option;
    }
}
?>

<table class="table">
    <thead>
    <th>Изображение</th>
    <th>Название</th>
    <th></th>
    </thead>
    <?php foreach ($checkedOptions as $item): ?>
        <tr data-item-id="<?=$item->toItem->id?>">
            <td><img width="50" src="<?=$item->toItem->getItemMainPicture('original')?>"/></td>
            <td><?=$item->toItem->name?></td>
            <td><input type="checkbox" <?php if (isset($connectedOptionsIdsArray[$item->toItem->id])) echo "checked"; ?> /></td>
        </tr>
    <?php endforeach; ?>
    <?php

    foreach ($uncheckedOptions as $item): ?>
        <?php
        $optionMarker = '';
        if ($_REQUEST['selectedOptionId']==$item->toItem->id) $optionMarker = 'getIt';
        ?>
        <tr data-item-id="<?=$item->toItem->id?>" class="<?=$optionMarker?>">
            <td><img width="50" src="<?=$item->toItem->getItemMainPicture('original')?>"/></td>
            <td><?=$item->toItem->name?></td>
            <td><input type="checkbox" <?php if (isset($connectedOptionsIdsArray[$item->toItem->id])) echo "checked"; ?> /></td>
        </tr>
    <?php endforeach; ?>
</table>