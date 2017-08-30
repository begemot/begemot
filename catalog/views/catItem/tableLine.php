<tr data-item-id="<?= $itemId ?>">
    <td><a class="fancybox" rel="<?= $itemId ?>" href="<?= $originalImage ?>"><img width="50" src="<?= $adminImage ?>"/></a>
    </td>
    <td><?= $name; ?></td>
    <td><input type="checkbox" <?= ($isBase ? "checked" : "") ?>/></td>
    <td><input data-item-id="<?=$itemId?>"  class="ajaxOptionPrice" type="text" value="<?=$price?>"/></td>
    <td width="150">
        <?php
        $optionsMessage = 'нет опций';
        $optionsCount = count($options);
        if ($optionsCount > 0) $optionsMessage = 'есть ' . $optionsCount;
        ?>

        <a class="btn btn-success .btn-mini btn-add-option-relation"><?= $optionsMessage ?> </a>
    </td>
    <td width="150">
        <?php
        $optionsMessage = 'нет опций';
//        $optionsCount = count($conflict);
//        if ($optionsCount > 0) $optionsMessage = 'есть ' . $optionsCount;
        ?>

        <a class="btn btn-success .btn-mini btn-add-option-conflict"><?= $optionsMessage ?> </a>
    </td>
    <td><a class="btn btn-danger removeOption">Открепить</a></td>
</tr>