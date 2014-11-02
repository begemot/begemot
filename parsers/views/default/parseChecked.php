<?php 
$this->menu = array(
    $this->menu = array(
    array('label' => 'Все парсеры', 'url' => array('/parsers/default/index')),
    array('label' => 'Все связи', 'url' => array('/parsers/default/linking')),
    array('label' => 'Задания по расписанию', 'url' => array('/parsers/default/cron')),
    ));

 ?>





<?php if ($items): ?>
	<form action="/parsers/default/doChecked" method='post'>

		<table>
			<thead>
				<tr>
					<td><input type="checkbox" class='checkAll'></td>
					<td>Изображение</td>
					<td>Название</td>
					<td>Новая цена</td>
					<td>Наличие</td>
					<td>Обновить</td>
				</tr>
			</thead>
			<tbody>

			<?php foreach($items as $item): ?>
				<tr class='<?php echo $item->id?>'>
					<td><input type="checkbox" name="item[<?php echo $item->item->id ?>][id]" value='<?php echo $item->item->id ?>'></td>
					<td><img src="<?php echo $item->item->getItemMainPicture("innerSmall")?>"></td>
					<td class='name'><?php echo $item->item->name?></td>
					<td><input type='text' value='<?php echo $item->linking->price?>' name='item[<?php echo $item->item->id ?>][price]' class='price input-small'><br/>(было <?php echo $item->item->price ?>)</td>
					<td><input type='text' value='<?php echo $item->linking->quantity?>' name='item[<?php echo $item->item->id ?>][quantity]' class='quantity input-small'><br/>(было <?php echo $item->item->quantity ?>)</td>
					<td><button type='button' class='updatePrice' data-id='<?php echo $item->item->id ?>'>Обновить цену и наличие</button></td>
				</tr>
					
			<?php endforeach ?>
			</tbody>
		</table>

		<input type="hidden" name='url' value='<?php echo $_SERVER['REQUEST_URI']?>'>
		<input type='submit' class='btn btn-primary btn-medium' value='Применить все'>

	</form>
<?php else: ?>
	Ничего не надо обновлять
<?php endif ?>

<script>
	$(document).on("click", ".updatePrice", function(e){

		var button = $(this);
		var params = {'id': $(this).attr("data-id"), 'price': $(this).parents("TR").find(".price").val(), 'quantity': $(this).parents("TR").find(".quantity").val()};


		$.post('/parsers/default/updateCard', params, function(data){
			if (data == '1') {
				button.parents("TR").fadeOut('1000');
				setTimeout(function(){
					button.parents("TR").remove();
				}, 1000)
				
			};
			
		})

	})


	$(document).on("click", '.checkAll', function(){
		var checkboxes = $(this).parents("TABLE").find("INPUT[type='checkbox']");

		if ($(this).attr("checked")) {
			checkboxes.attr("checked", true);
		} else checkboxes.attr("checked", false);
	})
</script>