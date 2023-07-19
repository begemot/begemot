<?php 
require(dirname(__FILE__).'/../menu.php');
 ?>



<h1>Все связи</h1>
<ul class="nav nav-tabs buttons" role="tablist">
	<?php foreach ($buttons as $button): ?>
		<li role="presentation" class="active"><a href='#<?php echo $button?>' data-class='.<?php echo $button?>'><?php echo $button?></a></li>
	<?php endforeach ?>
</ul>

<?php if ($items): ?>
	<table class='items'>
		<thead>
			<tr>
				<td>id</td>
				<td>Имя</td>
				<td>Обновить</td>
			</tr>
		</thead>
		<tbody>

		<?php foreach($items as $item): ?>
			<tr class='<?php echo preg_replace('/\./', '', $item->filename)?>'>
				<td><?php echo $item->fromId?></td>
				<td class='name'><?php echo $item->item->name ?></td>
				<td><input type='button' value='Удалить связь' data-id='<?php echo $item->id?>' class='deteleLinking'></td>
			</tr>
		<?php endforeach ?>
		</tbody>
	</table>

<?php else: ?>
	Нету связей
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

	$(function(){
		$(".items TR").hide();
		$(".<?php echo $buttons[0] ?>").show();
	})

	$(".buttons A").click(function(){
		$(".items TR").hide();
		$($(this).data("class")).show();
	})
</script>