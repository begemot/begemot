<tr class='<?php echo $item->id?>'>
	<td><input type="checkbox" name="item[<?php echo $item->item->id ?>][id]" value='<?php echo $item->item->id ?>'></td>
	<td><img src="<?php echo $item->item->getItemMainPicture("innerSmall")?>"></td>
	<td><?php echo $item->fromId?></td>
	<td class='name'><?php echo $item->item->name?></td>
	<td><?php echo $item->item->price?></td>
	<td><input type='text' value='<?php echo $item->linking->price?>' name='item[<?php echo $item->item->id ?>][price]' class='price input-small'></td>
	<td><input type='text' value='<?php echo $item->linking->quantity?>' name='item[<?php echo $item->item->id ?>][quantity]' class='quantity input-small'></td>
	<td><button type='button' class='updatePrice' data-id='<?php echo $item->item->id ?>'>Обновить цену и наличие</button></td>
</tr>