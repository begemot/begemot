<?php

class ConfigBuilder
{
    const BASE_CONFIG = '/begemot/config.php';


    public static function buildConfig($userConfig)
    {
        $dir = dirname(__FILE__);
        $webroot = $dir . '/../../../../';
        $protected = $dir . '/../../../';
        $modules = $dir . '/../../';

        //die($dir.'/../..'.self::BASE_CONFIG);
        require_once($dir . '/ModulesManager.php');

        $data = ModulesManager::getModulesData();

        $baseConfig = require($dir . '/../..' . self::BASE_CONFIG);
        foreach ($data as $moduleName => $moduleData) {
            $moduleConfigPath = $modules .$moduleName. '/config.php';
            if ($moduleName == 'begemot') continue;

            if(file_exists($moduleConfigPath)) {

                if ($moduleData['active'] == 1) {
                    
                    $moduleConfig = require_once $moduleConfigPath;

                    $baseConfig = array_replace($baseConfig, $moduleConfig);
                }
            }
        }


        return array_replace($baseConfig, $userConfig);
    }

}