<?php
$this->menu = require dirname(__FILE__) . '/../default/commonMenu.php';

//Yii::app()->clientScript->registerScriptFile('https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.8.2/angular.min.js');
Yii::app()->clientScript->registerScriptFile('https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.21/lodash.min.js');
Yii::app()->clientScript->registerScriptFile('/protected/modules/schema/assets/js/schema.angular.js');

Yii::import('schema.components.*');
Yii::import('begemot.extensions.vault.FileVault');
$link = new CSchemaLink($model->linkType, $model->linkId);

?>

    <h1>Update SchemaLinks <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model' => $model)); ?>

    <script>

        app.service('schemaData', function () {
            this.data = <?=json_encode($link->getData())?>
        });

    </script>


    <div ng-app="schema" ng-controller="update">
        <div ng-repeat="schema in allData ">{{schema.name}}
            <div ng-repeat="field in schema.data">{{field.name}} : {{field.value}}</div>
        </div>
    </div>
<?php



//echo '<pre>';
//print_r();
//echo '<pre>';


