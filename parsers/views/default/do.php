<?php 
Yii::app()->clientScript->registerCssFile(
    Yii::app()->assetManager->publish(Yii::app()->getModule('parsers')->basePath . '/assets/parsers.css')
);
Yii::app()->clientScript->registerScriptFile(
    Yii::app()->assetManager->publish(Yii::app()->getModule('parsers')->basePath . '/assets/jquery.quicksearch.js'), CClientScript::POS_HEAD
);


$this->menu = array(
    array('label' => 'Все парсеры', 'url' => array('/parsers/default/index')),
    array('label' => 'Все связи', 'url' => array('/parsers/default/linking')),
);
 ?>



<h1>Парсер "<?php echo $filename?>"</h1>

<?php //print_r($itemList['combinedAndChanged']) ?>




<!-- Nav tabs -->
<ul class="nav nav-tabs">
  <li class="active"><a href="#toDo" data-toggle="tab">Изменились (<span class='countChanged'><?php echo count($itemList['combinedAndChanged'])?></span>)</a></li>
  <li><a href="#new" data-toggle="tab">Новые</a></li>
  <li><a href="#combined" data-toggle="tab">Все связи</a></li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
  <div class="tab-pane active" id="toDo">
  	
  		<form action="/parsers/default/doChecked" method='post'>

			<table>
				<thead>
					<tr>
						<td><input type="checkbox" class='checkAll'></td>
						<td>Изображение</td>
						<td>Артикул</td>
						<td>Название</td>
						<td>Старая цена</td>
						<td>Новая цена</td>
						<td>Наличие</td>
						<td>Обновить</td>
					</tr>
				</thead>
				<tbody>
				<?php if ($itemList['combinedAndChanged']): ?>
					<?php foreach($itemList['combinedAndChanged'] as $item): ?>
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
							
					<?php endforeach ?>
				<?php endif ?>
				</tbody>
			</table>

			<input type="hidden" name='url' value='<?php echo $_SERVER['REQUEST_URI']?>'>
			<input type='submit' class='btn btn-primary btn-medium' value='Применить выделенные'>

		</form>
  </div>
  <div class="tab-pane" id="new">
  	
  	<table class='tableToSearch'>
		<thead>
			<tr>
				<td>Артикул</td>
				<td>Название</td>
				<td>Цена</td>
				<td>Соединить с</td>
				<td>Добавить как новый</td>
			</tr>
			<tr>
				<td><input type="text" class='search input-small' data-selector='.id' placeholder='Поиск'></td>
				<td><input type="text" class='search input-small' data-selector='.name' placeholder='Поиск'></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
		</thead>
		<tbody>
			
		<?php $number = 0; ?>
		<?php foreach($itemList['notCombined'] as $item): ?>
			<tr class='item-<?php echo $number ?>'>
				<td class='id'><?php echo $item->id?></td>
				<td class='name' data-id='<?php echo $item->id?>' data-price='<?php echo $item->price?>'><?php echo $item->name?></td>
				<td><?php echo $item->price?></td>
				<td><button type='button'  data-filename='<?php echo $item->filename?>' class='composite btn btn-info' name='<?php echo $item->name?>'>Объединить с ...</button>
					<?php if ($item->findedByArticle()): ?>
					<form action="/parsers/default/syncCard" data-removeAfter='.item-<?php echo $number ?>' class='ajaxSubmit'>
						<input type="hidden" name='ParsersLinking[fromId]' id='name' value='<?php echo $item->id?>'><br/>
				      	<input type="hidden" name='ParsersLinking[toId]' id='itemId' value='<?php echo $item->findedByArticle()?>' >
				      	<input type="hidden" name='ParsersLinking[filename]' id='itemId' value='<?php echo $item->filename?>' >
						<button type='submit' class='compositeRightNow btn btn-primary' data-id='<?php echo $item->findedByArticle()?>'>Привязать сразу по артиклю к (<a style='color:white;text-decoration:underline' href='<?php echo $this->createUrl("/catalog/catItem/update", array('id' => $item->findedByArticle() )) ?>'><?php echo $item->findedByArticle() ?></a>)</button></td>
					</form> 
						
					<?php endif ?>
					
				<td><button type='button' class='addAsNew' data-filename='<?php echo $item->filename?>' data-id='<?php echo $item->id?>' price='<?php echo $item->price?>' name='<?php echo $item->name?>' text='<?php echo $item->text?>'>Добавить как новый</button></td>
			</tr>
			<?php $number++; ?>
		<?php endforeach ?>
		</tbody>
	</table>
  </div>
  <div class="tab-pane" id="combined">
	<table>
		<thead>
			<tr>
				<td>Id</td>
				<td>Изображение</td>
				<td>Название</td>
				<td>Связан с</td>
				<td>Удалить связь</td>
			</tr>
		</thead>
		<tbody>
		<?php if ($itemList['combined']): ?>
			<?php foreach($itemList['combined'] as $item): ?>
				<tr>
					<td><?php echo $item->fromId?></td>
					<td><img src="<?php echo $item->item->getItemMainPicture("innerSmall")?>"></td>
					<td class='name'><?php echo $item->linking->name ?></td>
					<td><?php echo $item->item->name ?></td>
					<td><input type='button' value='Удалить связь' data-id='<?php echo $item->id?>' class='deleteLinking'></td>
				</tr>
					
			<?php endforeach ?>
		<?php endif ?>
		
		</tbody>
	</table>
  </div>
</div>






	


<div class="modal fade" style='display:none'>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Связать элемент с ...</h4>
      </div>
      <form action="/parsers/default/syncCard" method='post' class='ajaxSubmit' data-hideafter='.modal' data-removeafter=''>
	      <div class="modal-body">
	      	<div id="error"></div>
	      	Нажмите на название карточки когда выберите в списке.<br/>
	      	<input type="text" id="search" placeholder='Поиск'>
	      	<ul id="search_in_items">
	      		<?php foreach ($allItems as $item): ?>
	        		<li data-itemid='<?php echo $item->id ?>'><?php echo $item->name ?> (<?php echo $item->id ?>) <a href='<?php echo $this->createUrl("/catalog/catItem/update", array('id' => $item->id )) ?>'>Просмотреть</a></li>
	       		<?php endforeach ?>
	      	</ul>
			
			
	      	<input type="hidden" name='ParsersLinking[filename]' id='filename' required >
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default closeModal" data-dismiss="modal">Закрыть</button>
	        <button class='syncCard btn btn-primary' type='submit'>Соединить карточку (<span class='cardName'></span>)</button>
	        <br/>
	        <div class="bottom" style='margin-top: 10px;'>
	        	<div style='margin:auto'>Сохранится для id: <input type="text" name='ParsersLinking[fromId]' id='name' class='required' required></div>
	      		<div style='margin:auto'>Привяжет к ID карточки: <input type="text" name='ParsersLinking[toId]' id='itemIdModal' required ></div>
	        </div>
	        
	      </div>

      </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>

	$(document).on('change', ".search", function(){
		$(this).quicksearch('.tableToSearch tbody tr', {
		    selector: $(this).attr("data-selector")
		});
	})
	$(document).on("click", ".addAsNew", function(){
		var button = $(this);
		var params = {'CatItem': {'name': $(this).attr("name"), 'price': $(this).attr("price"), 'text': $(this).attr("text")}, 'returnId': true};


		$.post('/catalog/catItem/create', params, function(data){
			button.parents("TR").find(".composite").html("Уже обьединено");
			button.parents("TR").find("BUTTON").attr("disabled", true);
			button.parent().html("<a href='/catalog/catItem/update/id/" + data +"' target='_blank'>Редактировать</a>");
			var toId = data;

			$.post('/parsers/default/syncCard', {'ParsersLinking': {'fromId': button.attr('data-id'), 'toId': toId, 'filename': button.attr('data-filename')}}, function(data){
				data = $.parseJSON(data);
				console.log(data);
				if (data.code) {

					if (data.toUpdate != "") {
						$("#toDo").find("TBODY").append(data.toUpdate);
						$(".countChanged").html(parseInt($(".countChanged").html()) + 1);
					}

					if (data.toAllLinks != "") {
						$("#combined").find("TBODY").append(data.toAllLinks);
					}
					console.log(data);
				}
			});
		})

	})

	$(document).on("click", ".deleteLinking", function(e){

		var link = $(this);
		var id = $(this).attr("data-id");


		$.get('/parsers/default/deleteLinking/id/' + id, function(data){
			if (data == '1') {
				link.parents("TR").fadeOut('1000');

				if ($("." + id).attr("class") != "") {
					if (parseInt($(".countChanged").html() >= 0)) {
						$(".countChanged").html(parseInt($(".countChanged").html()) - 1);
					}
					
					$("." + id).remove();
				};
				setTimeout(function(){
					link.parents("TR").remove();
				}, 1000)
				
			} else{
				alert("error");
			}
			
		})

	})

	$(document).on("click", ".composite", function(){
		var name = $(this).parents("TR").find(".name").html();
		var id = $(this).parents("TR").find(".name").attr("data-id");
		var className = $(this).parents("TR").attr('class');
		var price = $(this).parents("TR").find(".name").attr("data-price");
		var filename = $(this).attr("data-filename");
		$("#search").val(name);
		$("#search").quicksearch('UL#search_in_items li').search(name);
		$(".error").hide();

		

		$("#search_in_items LI").each(function(){
			if ($(this).css("display") != "none") {
				$(this).addClass('active');
				$("#itemIdModal").val($(this).attr("data-itemid"));
				return false;
			}
			
		})
		
		$(".modal").show().addClass("in");
		$(".modal FORM").attr("data-removeafter", '.' + className);
		$(".modal FORM").find(".cardName").html(name);
		$(".modal FORM").find("#name").val(id);
		$(".modal FORM").find("#filename").val(filename);
	})

	$(document).on("click", "#search_in_items LI", function(){
		$(this).siblings().removeClass("active");
		$(this).addClass("active");
	
		$("#itemIdModal").val($(this).attr("data-itemid"));
		
	})

	$(document).on("click", ".checkAll", function(){
		if($(this).attr("checked")){
			$(this).parents("TABLE").find("input[type='checkbox']").attr("checked", true);
		} 
		else $(this).parents("TABLE").find("input[type='checkbox']").attr("checked", false);
	})


	$(document).on("submit", ".ajaxSubmit", function(e){
		e.preventDefault();
		var form = $(this);
		var hideAfter = $(this).attr("data-hideafter");
		var removeAfter = $(this).attr("data-removeAfter");
		var status = true;
		$(this).find(".required").removeClass("form_error");

		$(this).find(".required").each(function(){
			if ($(this).val() == "") {status = false}
				$(this).addClass("form_error");
		})
		if (!status) return false;

		$.post(form.attr("action"), form.serialize(), function(data){
			data = $.parseJSON(data);
			if (data.code) {
				$(hideAfter).removeClass('in').hide();
				$(removeAfter).fadeOut(1000);
				setTimeout(function(){
					$(removeAfter).remove();
				}, 1000)

				if (data.toUpdate != "") {
					$("#toDo").find("TBODY").append(data.toUpdate);
					$(".countChanged").html(parseInt($(".countChanged").html()) + 1);
				}

				if (data.toAllLinks != "") {
					$("#combined").find("TBODY").append(data.toAllLinks);
				}
				console.log(data);
			} else{
				$("#error").html(data.html);
				$("#error").fadeIn();
				$('.modal-body').scrollTo(0);
			}
		})
	})

	$(document).on("click", ".closeModal", function(e){
		$(".modal").removeClass('in').hide();
	})

	$(".close").click(function(){
		$(".modal").removeClass("in").addClass("out");
	})

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

	

	

	$('input#search').quicksearch('UL#search_in_items li');


$.fn.scrollTo = function( target, options, callback ){
  if(typeof options == 'function' && arguments.length == 2){ callback = options; options = target; }
  var settings = $.extend({
    scrollTarget  : target,
    offsetTop     : 50,
    duration      : 500,
    easing        : 'swing'
  }, options);
  return this.each(function(){
    var scrollPane = $(this);
    var scrollTarget = (typeof settings.scrollTarget == "number") ? settings.scrollTarget : $(settings.scrollTarget);
    var scrollY = (typeof scrollTarget == "number") ? scrollTarget : scrollTarget.offset().top + scrollPane.scrollTop() - parseInt(settings.offsetTop);
    scrollPane.animate({scrollTop : scrollY }, parseInt(settings.duration), settings.easing, function(){
      if (typeof callback == 'function') { callback.call(this); }
    });
  });
}

</script>