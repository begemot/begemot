<?php

class SchemaModule extends CWebModule
{
    public function init()
    {
        // this method is called when the module is being created
        // you may place code here to customize the module or the application

        // import the module-level models and components
        $this->setImport(array(
            'schema.models.*',
            'schema.components.*',
        ));
    }

    public function beforeControllerAction($controller, $action)
    {

        // Массив исключений
        $exclusions = [
            'manage' => ['*'], // Все действия контроллера 'site'

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