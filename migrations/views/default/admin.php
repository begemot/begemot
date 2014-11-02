
<h1>Миграция базы данных</h1>
<?php echo $time?>
<div class="center-block"><?php echo $return?></div>
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
				<?php if (is_bool($model->isConfirmed(true)) && !$model->isConfirmed(true)): ?>
					<a href='?file=<?php echo get_class($model)?>&go=up' class="btn btn-primary btn-mini">Применить</a>
				<?php elseif(is_bool($model->isConfirmed(true)) && $model->isConfirmed(false)): ?>
					<a href='?file=<?php echo get_class($model)?>&go=down' class="btn btn-warning btn-mini">Откатить</a>
				<?php else: ?>
					<a href='?file=<?php echo get_class($model)?>&go=up' class="btn btn-primary btn-mini">Применить</a>
					<a href='?file=<?php echo get_class($model)?>&go=down' class="btn btn-warning btn-mini">Откатить</a>
				<?php endif ?>
			</td>
		</tr>
			
	<?php endforeach ?>
	</tbody>
</table>

<a href='?file=all&go=up' class='btn btn-primary btn-medium'>Применить все</a>
