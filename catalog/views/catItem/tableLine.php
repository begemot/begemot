<tr data-item-id="<?=$itemId?>">
    <td><a class="fancybox" rel="<?=$itemId?>" href="<?= $originalImage ?>"><img width="50" src="<?= $adminImage ?>" /></a></td>
    <td><?= $name; ?></td>
    <td><input type="checkbox" <?=($isBase?"checked":"")?>/></td>
    <td>


        <a class="btn btn-success .btn-mini btn-add-option-relation"><span class="icon-plus icon-white"></span></a>
    </td>
    <td><a class="btn btn-danger removeOption">Открепить</a></td>
</tr>