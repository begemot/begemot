<?php
Yii::import('schema.components.ApiFunctions');
class ApiController extends Controller
{
	/**
	 * Разрешает запросы только через POST.
	 */
	public function filters()
	{
		return array(
			'accessControl', // Контроль доступа
		);
	}

	/**
	 * Правила доступа к API.
	 */
	public function accessRules()
	{
		return array(
			array(
				'allow',
				'actions' => array('updateField', 'schemaLinks', 'getSchemaData', 'geLineSchemaData'), // Добавили новый метод
				'users' => array('@'), // Только авторизованные пользователи
			),
			array(
				'deny',
				'users' => array('*'),
			),
		);
	}
	/**
	 * Action to return the list of SchemaLinks models in JSON format
	 */
	public function actionSchemaLinks($except = null)
	{
		// Create a criteria to exclude the specified linkType
		$criteria = new CDbCriteria();
		if ($except !== null && $except !== 'null') {
			$criteria->addNotInCondition('linkType', explode(',', $except));
		}

		// Fetch records from SchemaLinks model based on the criteria
		$schemaLinks = SchemaLinks::model()->findAll($criteria);

		// Transform records to an array for JSON output, keeping only the required fields
		$result = [];
		foreach ($schemaLinks as $link) {
			$result[] = [
				'id' => $link->id,
				'name' => $link->name,
				'linkType' => $link->linkType,
				'linkId' => $link->linkId,
				'schemaId' => $link->schemaId
				// Add other fields if necessary
			];
		}

		// Output the result as JSON
		header('Content-Type: application/json');
		echo CJSON::encode($result);
		Yii::app()->end();
	}
	public function actionGetSchemaData($linkType, $linkId)
	{
		$data = ApiFunctions::getSchemaData($linkType, $linkId);

		// Выводим результат в формате JSON
		header('Content-Type: application/json');
		echo CJSON::encode($data);
		Yii::app()->end();
	}

	/**
	 * Обновляет поле в зависимости от его типа.
	 * Ожидает POST-запрос с параметрами:
	 * - schemaId (ID схемы)
	 * - fieldId (ID поля)
	 * - value (новое значение)
	 * - linkType (тип связи)
	 * - groupId (ID группы, ранее linkId)
	 */
	public function actionUpdateField()
	{
		Yii::import('schema.models.types.*');
		if (!Yii::app()->request->isPostRequest) {
			echo json_encode(['success' => false, 'message' => 'Метод не поддерживается']);
			Yii::app()->end();
		}

		$schemaId = Yii::app()->request->getPost('schemaId');
		$fieldId = Yii::app()->request->getPost('fieldId');
		$newValue = Yii::app()->request->getPost('value');
		$linkType = Yii::app()->request->getPost('linkType');
		$groupId = Yii::app()->request->getPost('groupId'); // Было linkId, теперь groupId

		// Находим запись в SchemaData
		$schemaData = SchemaData::model()->findByAttributes([
			'schemaId' => $schemaId,
			'fieldId' => $fieldId,
			'linkType' => $linkType,
			'groupId' => $groupId // Было linkId, теперь groupId
		]);

		if (!$schemaData) {
			echo json_encode(['success' => false, 'message' => 'Запись в SchemaData не найдена']);
			Yii::app()->end();
		}

		// Определяем таблицу по типу
		$fieldType = $schemaData->fieldType;
		$valueId = $schemaData->valueId;

		if (!$fieldType || !$valueId) {
			echo json_encode(['success' => false, 'message' => 'Тип данных или valueId не определен']);
			Yii::app()->end();
		}

		// Определяем имя модели
		$modelClass = 'SchmType' . ucfirst($fieldType);

		if (!class_exists($modelClass)) {
			echo json_encode(['success' => false, 'message' => 'Модель для данного типа не найдена']);
			Yii::app()->end();
		}

		// Ищем значение в таблице типа
		$valueModel = $modelClass::model()->findByPk($valueId);

		if (!$valueModel) {
			echo json_encode(['success' => false, 'message' => 'Запись в таблице типа не найдена']);
			Yii::app()->end();
		}

		// Обновляем значение
		$valueModel->value = $newValue;
		if ($valueModel->save()) {
			echo json_encode(['success' => true, 'message' => 'Значение успешно обновлено']);
		} else {
			echo json_encode(['success' => false, 'message' => 'Ошибка при сохранении']);
		}
	}



	/**
	 * Получает данные схемы по её ID.
	 * Ожидает GET-запрос с параметром:
	 * - schemaId (ID схемы)
	 */
	public function actionGetSchema()
	{
		$schemaId = Yii::app()->request->getQuery('schemaId');

		if (!$schemaId) {
			echo json_encode(['success' => false, 'message' => 'Не указан ID схемы']);
			Yii::app()->end();
		}

		$schema = Schema::model()->findByPk($schemaId);

		if ($schema) {
			echo json_encode(['success' => true, 'data' => $schema->attributes]);
		} else {
			echo json_encode(['success' => false, 'message' => 'Схема не найдена']);
		}
	}

	/**
	 * Принимает массив данных и обновляет несколько полей сразу.
	 * Ожидает POST-запрос с параметрами:
	 * - fields (массив объектов {schemaId, fieldId, value, linkType, linkId})
	 */
	public function actionUpdateMultipleFields()
	{
		if (Yii::app()->request->isPostRequest) {
			$fields = Yii::app()->request->getPost('fields');

			if (!is_array($fields) || empty($fields)) {
				echo json_encode(['success' => false, 'message' => 'Некорректные данные']);
				Yii::app()->end();
			}

			$errors = [];
			foreach ($fields as $field) {
				$model = SchemaData::model()->findByAttributes([
					'schema_id' => $field['schemaId'],
					'field_id' => $field['fieldId'],
					'link_type' => $field['linkType'],
					'link_id' => $field['linkId']
				]);

				if ($model) {
					$model->value = $field['value'];
					if (!$model->save()) {
						$errors[] = "Ошибка при обновлении поля ID: {$field['fieldId']}";
					}
				} else {
					$errors[] = "Поле ID: {$field['fieldId']} не найдено";
				}
			}

			if (empty($errors)) {
				echo json_encode(['success' => true, 'message' => 'Все данные успешно обновлены']);
			} else {
				echo json_encode(['success' => false, 'message' => 'Некоторые поля не обновлены', 'errors' => $errors]);
			}
		}
	}
}