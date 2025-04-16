<?php

class SchemaModule extends CWebModule
{
    public function init()
    {
        $this->setImport(array(
            'schema.models.*',
            'schema.components.*',
        ));
        Yii::import('webroot.protected.modules.schema.components.MysqlToMongo');

        MysqlToMongo::migrate();
        //тут делаем миграцию



    }

    public function beforeControllerAction($controller, $action)
    {

        // Массив исключений
        $exclusions = [
            'manage' => ['*'], // Все действия контроллера 'site'
            'schemaLinks' => ['update'], // Все действия контроллера 'site'
            'schemaGroup' => ['*'],
            'schemaData' => ['*'],
            'default' => ['*']

        ];

        // Проверка исключений
        $controllerId = $controller->id;
        $actionId = $action->id;

        if (isset($exclusions[$controllerId])) {
            if (in_array('*', $exclusions[$controllerId]) || in_array($actionId, $exclusions[$controllerId])) {
                return true; // Исключение, не подключаем Bootstrap
            }
        }


        if ($controller->id != 'site') {
            $component = Yii::createComponent(array(

                'class' => 'begemot.extensions.bootstrap.components.Bootstrap'

            ));
            Yii::app()->setComponent('bootstrap', $component);
        }

        return true;
    }
}
