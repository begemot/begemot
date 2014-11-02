<?php 
$this->menu = array(
    array('label' => 'Все парсеры', 'url' => array('/parsers/default/index')),
    array('label' => 'Все связи', 'url' => array('/parsers/default/linking')),
);
 ?>



<h1>Все связи</h1>

<?php if ($items): ?>
	<table>
		<thead>
			<tr>
				<td>id</td>
				<td>Имя</td>
				<td>Обновить</td>
			</tr>
		</thead>
		<tbody>

		<?php foreach($items as $item): ?>
			<tr>
				<td><?php echo $item->fromId?></td>
				<td class='name'><?php echo $item->item->name ?></td>
				<td><input type='button' value='Удалить связь' data-id='<?php echo $item->id?>' class='deteleLinking'></td>
			</tr>
		<?php endforeach ?>
		</tbody>
	</table>

	<input type='submit' class='btn btn-primary btn-medium' value='Применить все'>
<?php else: ?>
	Ничего не надо обновлять
<?php endif ?>

<script>
	$(document).on("click", ".deteleLinking", function(e){

		var link = $(this);
		var id = $(this).attr("data-id");


		$.get('/parsers/default/deleteLinking/id/' + id, function(data){
			if (data == '1') {
				link.parents("TR").fadeOut('1000');
	
				if ($("." + id).attr("class") != "") {
					$(".countChanged").html(parseInt($(".countChanged").html()) - 1);
					$("." + id).remove();
				};
				setTimeout(function(){
					link.parents("TR").remove();
				}, 1000)
				
			};
			
		})

	})
</script>