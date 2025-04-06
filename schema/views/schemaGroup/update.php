<?php
//$groupId
Yii::import('schema.components.MysqlToMongo');

if (!MysqlToMongo::checkCollection('schemaField')) {
    MysqlToMongo::syncSchemaFields();
}

$schemaGroupCollection = Yii::app()->mongoDb->getCollection('schemaGroup');

$query = ['schemaGroup' => (int)$groupId];
$res = $schemaGroupCollection->findOne($query);
$queryParams = [];

Yii::import('schema.mongoModels.MngSchemaFieldModel');
foreach ($res->params->getArrayCopy() as $key => $value) {
    $mongoFieldModel = MngSchemaFieldModel::findById((int)$key);

    $queryParams['fields.' . $mongoFieldModel->name . '.value'] = $value;
}

$pipeline = [
    ['$match' => $queryParams],
    ['$group' => [
        '_id' => null,
        'groupIds' => ['$addToSet' => '$groupId']
    ]]
];

$schemaDataCollection = Yii::app()->mongoDb->getCollection('schemaData');
$mongoResult = $schemaDataCollection->aggregate($pipeline)->toArray();

print_r($mongoResult[0]->getArrayCopy()['groupIds']->getArrayCopy());





$picturesConfig = array();
Yii::import('catalog.CatalogModule');
$configFile = Yii::getPathOfAlias(CatalogModule::CAT_CONFIG_FILE_ALIAS) . '.php';
if (file_exists($configFile)) {

    $picturesConfig = require($configFile);

    $this->widget(
        'application.modules.pictureBox.components.PictureBoxFiles',
        array(
            'id' => 'schemaData',
            'elementId' => $groupId,
            'config' => $picturesConfig,
            'theme' => 'angularTilesBs5'
        )
    );
} else {
    Yii::app()->user->setFlash('error', 'Отсутствует конфигурационный файл:' . $configFile);
}
