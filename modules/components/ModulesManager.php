<?php


class ModulesManager
{
    const DEFAULT_MODULES = [
        'begemot' => '',
        'migrations' => '',
        'modules' => '',
        'pages' => '',
        'pictureBox' => '',
        'RolesImport' => '',
        'user' => '',
        'srbac' => '',
        'pictureBox' => '',
        'settings' => '',
        'schema' => ''

    ];

    public static function isModuleByDefault($module)
    {
        return isset(self::DEFAULT_MODULES[$module]);
    }


    public static function getModulesData()
    {
        // Yii::import('begemot.extensions.vault.FileVault');
        $dir = dirname(__FILE__);
        $vaultClassPath = $dir . '/../../begemot/extensions/vault/FileVault.php';

        require_once $vaultClassPath;
        $vaultPath = $dir . '/../../../../files/modules_data';

        if (!file_exists($vaultPath))
            mkdir($vaultPath, 0777, true);

        $modulesDataVault = new FileVault($vaultPath);

        $modulesList = self::getModulesList();

        $data = $modulesDataVault->getCollection();

        foreach ($modulesList as $moduleName) {
            if (!isset($data[$moduleName]))
                $data[$moduleName] = [];

            if (self::isModuleByDefault($moduleName)) {
                $data[$moduleName]['active'] = true;
                $data[$moduleName]['default'] = true;
            } else {
                $data[$moduleName]['default'] = false;
                if (!isset($data[$moduleName]['active'])) {
                    $data[$moduleName]['active'] = false;
                }
            }
        }

        self::saveModulesData($data);
        return $data;
    }

    public static function saveModulesData($data)
    {

        $dir = dirname(__FILE__);
        $vaultClassPath = $dir . '/../../begemot/extensions/vault/FileVault.php';

        require_once $vaultClassPath;

        $dir = __DIR__;
        $vaultPath = $baseWebDir = $dir . '/../../../../files/modules_data';
        //$vaultPath = Yii::getPathOfAlias('webroot.files.modules_data');
        if (!file_exists($vaultPath))
            mkdir($vaultPath, 0777, true);

        $modulesDataVault = new FileVault($vaultPath);

        $modulesDataVault->pushCollection($data);
    }

    public static function getModulesList()
    {
        //        $modulesPath = Yii::getPathOfAlias('application.modules');
        // die($modulesPath);
        $modulesPath = dirname(__FILE__) . '/../../';
        $modulesDirs = glob($modulesPath . '/*', GLOB_ONLYDIR);
        $modulesList = [];
        foreach ($modulesDirs as $moduleDir) {

            $modulesList[] = basename($moduleDir);
        }
        return $modulesList;
    }

    public static function isModuleActive($moduleName)
    {
        $data = self::getModulesData();
        if (isset($data[$moduleName]['active'])) {
            return $data[$moduleName]['active'];
        } else return false;
    }
}