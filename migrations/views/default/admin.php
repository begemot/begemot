
<h1>Миграция базы данных</h1>

<form class="form-search" action="/migrations/default/newMigration" method="GET">
	<label class="control-label" for="inputEmail">Имя файла миграции:</label>
	<input name="filename" type="text" class="input-medium search-query">
	<button type="submit" class="btn">Создать заготовку файла миграции</button>
</form>

<div class="center-block"><?php echo $return?>(Переключатели меняются не сразу, а в течении нескольких секунд)</div>
<table>
	<thead>
		<tr>
			<td>Название</td>
			<td>Описание</td>
			<td>Применено или нет</td>
			<td>Применить или откатить</td>
		</tr>
	</thead>
	<tbody>
	<?php foreach($models as $item): ?>
		<?php $model = new $item; ?>
		<tr>
			<td><?php echo get_class($model)?></td>
			<td><?php echo $model->getDescription();?></td>
			<td><?php echo $model->isConfirmed()?></td>
			<td>
				<?php if ($model->isConfirmed(true) == false): ?>
					<a href='/migrations.html?file=<?php echo get_class($model)?>&go=up' class="btn btn-primary btn-mini">Применить</a>
				<?php elseif($model->isConfirmed(true) == true): ?>
                    <a href='/migrations.html?file=<?php echo get_class($model)?>&go=down'><button type="button" class="btn btn-danger btn-sm">Откатить</button> </a>
				<?php else: ?>
					<a href='/migrations.html?file=<?php echo get_class($model)?>&go=up' class="btn btn-primary btn-mini">Применить</a>
					<a href='/migrations.html?file=<?php echo get_class($model)?>&go=down'><button type="button" class="btn btn-danger btn-sm"></button> >Откатить</a>
				<?php endif ?>
			</td>
		</tr>

	<?php endforeach ?>
	</tbody>
</table>

<a href='?file=all&go=up' class='btn btn-primary btn-medium'>Применить все</a>
