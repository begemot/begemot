<table class='tableToSearch'>
	<thead>
		<tr>
			<td>Артикул</td>
			<td>Название</td>
			<td>Цена</td>
			<td>Соединить с</td>
		</tr>
		<tr>
			<td><input type="text" class='search input-small' data-selector='.id' placeholder='Поиск'></td>
			<td><input type="text" class='search input-small' data-selector='.name' placeholder='Поиск'></td>
			<td></td>
			<td></td>	
		</tr>
	</thead>
	<tbody>
		
	<?php $number = 0; ?>
	<?php foreach($itemList as $item): ?>
		<tr class='item-<?php echo $number ?>'>
			<td class='id'><?php echo $item->id?></td>
			<td class='name' data-id='<?php echo $item->id?>' data-price='<?php echo $item->price?>'><?php echo $item->name?></td>
			<td><?php echo $item->price?></td>
			<td>
				<form action="/parsers/default/syncCard" data-removeAfter='.item-<?php echo $number ?>' class='ajaxSubmit'>
					<input type="hidden" name='ParsersLinking[fromId]' id='name' value='<?php echo $item->id?>'><br/>
			      	<input type="hidden" name='ParsersLinking[toId]' id='itemId' value='<?php echo $itemId?>'>
			      	<input type="hidden" name='ParsersLinking[filename]' id='itemId' value='<?php echo $item->filename?>' >
					<button type='submit' class='compositeRightNow btn btn-primary'>Привязать к карточке</button></td>
				</form>
			</td>
		</tr>
		<?php $number++; ?>
	<?php endforeach ?>
	</tbody>
</table>