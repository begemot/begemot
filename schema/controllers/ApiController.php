<?php
Yii::import('schema.components.ApiFunctions');
class ApiController extends Controller
{
	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl'
		);
	}

	public function accessRules()
	{
		return array(
			array(
				'allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions' => array('schemaLinks', 'getSchemaData', 'geLineSchemaData'),
				'expression' => 'Yii::app()->user->canDo()'
			),
			array(
				'deny', // deny all users
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
	public function actionGeLineSchemaData($linkType, $linkId)
	{
		$data = ApiFunctions::getLineSchemaData($linkType, $linkId);

		// Выводим результат в формате JSON
		header('Content-Type: application/json');
		echo CJSON::encode($data);
		Yii::app()->end();
	}
}