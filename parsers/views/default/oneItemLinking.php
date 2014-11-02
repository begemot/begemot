<tr>
	<td><?php echo $item->fromId?></td>
	<td><img src="<?php echo $item->item->getItemMainPicture("innerSmall")?>"></td>
	<td class='name'><?php echo $item->linking->name ?></td>
	<td><?php echo $item->item->name ?></td>
	<td><input type='button' value='Удалить связь' data-id='<?php echo $item->id?>' class='deleteLinking'></td>
</tr>