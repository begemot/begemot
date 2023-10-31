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

                    $baseConfig = self::mergeArray($baseConfig, $moduleConfig);
                }
            }
        }


        return self::mergeArray($baseConfig, $userConfig);
    }

    public static function mergeArray($a,$b)
    {
        $args=func_get_args();
        $res=array_shift($args);
        while(!empty($args))
        {
            $next=array_shift($args);
            foreach($next as $k => $v)
            {
                if(is_integer($k))
                    isset($res[$k]) ? $res[]=$v : $res[$k]=$v;
                elseif(is_array($v) && isset($res[$k]) && is_array($res[$k]))
                    $res[$k]=self::mergeArray($res[$k],$v);
                else
                    $res[$k]=$v;
            }
        }
        return $res;
    }
}