<?php

$options = CatItemsToItems::model()->findAllByAttributes(['itemId' => $paentItemId]);
//
$connectedOptionsIdsArray = [];
foreach ($alreadyConnetedOptions as $alreadyConnetedOptions){
    $connectedOptionsIdsArray[$alreadyConnetedOptions->toItemId]='1';
}

?>

<table class="table">
    <thead>
    <th>Изображение</th>
    <th>Название</th>
    <th></th>
    </thead>
    <?php foreach ($options as $item): ?>
        <tr data-item-id="<?=$item->toItem->id?>">
            <td><img width="50" src="<?=$item->toItem->getItemMainPicture('original')?>"/></td>
            <td><?=$item->toItem->name?></td>
            <td><input type="checkbox" <?php if (isset($connectedOptionsIdsArray[$item->toItem->id])) echo "checked"; ?> /></td>
        </tr>
    <?php endforeach; ?>
</table>