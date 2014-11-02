<?php 
$this->menu = array(
    array('label' => 'Все парсеры', 'url' => array('/parsers/default/index')),
    array('label' => 'Все связи', 'url' => array('/parsers/default/linking')),
);
 ?>
<h1>Парсеры</h1>

<form action="/parsers/default/parseChecked" method='get'>
	<table>
		<thead>
			<tr>
				<td>Название файла</td>
				<td>Дата парсинга</td>
				<td>Применять?</td>
				<td>Применить</td>
			</tr>
		</thead>
		<tbody>
		<?php foreach($fileListOfDirectory as $item): ?>
			<tr>
				<td><?php echo $item['name']?></td>
				<td class='date'><?php echo date("d.m.Y H:i", $item['time']) ?></td>
				<td><input type="checkbox" value='<?php echo $item['name']?>' name='parse[]'/></td>
				
				<td><input type='button' class='parseNew' data-file='<?php echo $item['className']?>' value='Спарсить новые данные'></td>
				<td><a href='<?php echo $this->createUrl("/parsers/default/do", array('file' => $item['name'])) ?>' class="btn btn-info btn-mini">Работать с текущими данными</a></td>
				
			</tr>
				
		<?php endforeach ?>
		</tbody>
	</table>

	<input type='submit' class='btn btn-primary btn-medium' value='Применить выделенные'>

</form>


<script>
	$(document).on("click", ".parseNew", function(){
		var button = $(this);
		var params = {'CatItem': {'name': $(this).attr("name"), 'price': $(this).attr("price"), 'text': $(this).attr("text")}, 'returnId': true};


		$.get('/parsers/default/parseNew/className/' + $(this).attr("data-file") + '/', function(data){
			if (data != "") {
				button.val("Спарсенно");
				button.parents("TR").find(".date").html(data);
			};
			
		})

	})
</script>
