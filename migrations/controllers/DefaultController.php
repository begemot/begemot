<?php

class DefaultController extends Controller
{
    public $layout = 'begemot.views.layouts.bs5clearLayout';

    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(

            array(
                'allow', // allow admin user to perform 'admin' and 'delete' actions
                'actions' => array('index', 'newMigration', 'manage', 'getMigrationsList', 'newMigrationFile', 'upMigration', 'downMigration', 'getAllMigrations'),
                'expression' => 'Yii::app()->user->canDo("")'
            ),
            array(
                'deny',  // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionUpMigration($fileName, $module)
    {
        Yii::import('application.modules.' . $module . '.migrations.' . $fileName);
        $instance = new $fileName();
        $instance->up();
    }

    public function actionDownMigration($fileName, $module)
    {
        Yii::import('application.modules.' . $module . '.migrations.' . $fileName);
        $instance = new $fileName();
        $instance->down();
    }

    public function actionNewMigrationFile($fileName, $module)
    {
        $time = time();

        $migrationsPath = Yii::getPathOfAlias('application.modules.' . $module . '.migrations');

        if (!file_exists($migrationsPath)) {
            mkdir($migrationsPath, 0777, true);
        }

        $maigrationTemplate = Yii::getPathOfAlias('migrations.components.migrationTemplate') . '.php';

        $file = file_get_contents($maigrationTemplate);
        $newClassName = 'm' . time() . '_' . $fileName;
        echo $newFileName = $migrationsPath . '/' . 'm' . time() . '_' . $fileName . '.php';

        $file = str_replace('<class_name>', $newClassName, $file);

        file_put_contents($newFileName, $file);
    }

    public function actionIndex()
    {
        $this->render('migrationsManage');
    }


    public function actionGetAllMigrations()
    {
        Yii::import('modules.components.ModulesManager');
        $modules = ModulesManager::getModulesData();

        $activeModules = array();

        foreach ($modules as $module => $properties) {
            if (!empty($properties['active'])) {
                $activeModules[] = $module;
            }
        }

        $resultMigrationsArray = [];
        foreach ($activeModules as $module) {


            $resultMigrationsArray = array_merge($resultMigrationsArray, $this->getMigrationsList($module));
        }

        echo json_encode($resultMigrationsArray);
    }

    private function getMigrationsList($moduleName)
    {



        $migrationsDir = Yii::getPathOfAlias('application.modules.' . $moduleName . '.migrations');
        //        echo $migrationsDir;
        //        die();
        if (!file_exists($migrationsDir)) {
            mkdir($migrationsDir, 0777);
            $resultList = [];
        } else {
            Yii::import('application.modules.' . $moduleName . '.migrations.*');
            $dirs = glob($migrationsDir . '/*');
            $resultList = [];


            foreach ($dirs as $file) {

                require_once($file);
                $resData = [];

                $migrationFileName = basename($file);
                $className = explode('.', $migrationFileName)[0];

                $resData['className'] = $className;

                $migrationInstance = new $className;

                $resData['description'] = $migrationInstance->getDescription();
                $resData['confirmed'] = $migrationInstance->isConfirmed();

                $resultList[] = $resData;
            }
        }

        return $resultList;
    }

    public function actionGetMigrationsList($moduleName)
    {

        $resultList = $this->getMigrationsList($moduleName);
        echo json_encode($resultList);
    }
}