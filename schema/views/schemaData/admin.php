<?php
/* @var $this SchemaLinksController */
/* @var $model SchemaLinks */



$this->menu = require dirname(__FILE__) . '/../default/commonMenu.php';

?>
<h1>SchemaData</h1>
<div class="table-responsive">
	<table class="table table-striped table-hover">
		<thead class="table-dark">
			<tr>
				<th scope="col">ID группы</th>
				<th scope="col">Название</th>
				<th scope="col">Тип</th>
				<th scope="col">ID схемы</th>
				<th scope="col">Действия</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($data['data'] as $item): ?>
				<tr>
					<td><?php echo CHtml::encode($item['groupId']); ?></td>
					<td><?php echo isset($item['fields']['Название']['value']) ? CHtml::encode($item['fields']['Название']['value']) : ''; ?></td>
					<td><?php echo CHtml::encode($item['linkType']); ?></td>
					<td><?php echo CHtml::encode($item['schemaId']); ?></td>
					<td>
						<a href="<?php echo $this->createUrl('update', ['id' => (string)$item['_id']]); ?>"
							class="btn btn-sm btn-primary"
							title="Редактировать"
							data-bs-toggle="tooltip"
							data-bs-placement="top">
							<i class="bi bi-pencil me-1"></i> Редактировать
						</a>

					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>

<nav aria-label="Page navigation">
	<?php $this->widget('CLinkPager', [
		'pages' => new CPagination($data['pagination']['totalItems']),
		'pageSize' => $data['pagination']['perPage'],
		'currentPage' => $data['pagination']['currentPage'] - 1,
		'htmlOptions' => ['class' => 'pagination justify-content-center'],
		'header' => '',
		'firstPageLabel' => '&laquo;&laquo;',
		'lastPageLabel' => '&raquo;&raquo;',
		'prevPageLabel' => '&laquo;',
		'nextPageLabel' => '&raquo;',
		'maxButtonCount' => 5,
		'cssFile' => false,
	]); ?>
</nav>

<style>
	/* Стили для совместимости Yii Pagination с Bootstrap 5 */
	.pagination a,
	.pagination span {
		position: relative;
		display: block;
		padding: 0.5rem 0.75rem;
		margin-left: -1px;
		line-height: 1.25;
		color: #0d6efd;
		background-color: #fff;
		border: 1px solid #dee2e6;
		text-decoration: none;
	}

	.pagination a:hover {
		z-index: 2;
		color: #0a58ca;
		background-color: #e9ecef;
		border-color: #dee2e6;
	}

	.pagination .selected span {
		z-index: 3;
		color: #fff;
		background-color: #0d6efd;
		border-color: #0d6efd;
	}

	.pagination .disabled span {
		color: #6c757d;
		pointer-events: none;
		background-color: #fff;
		border-color: #dee2e6;
	}
</style>