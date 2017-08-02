<?php

class VarsModule extends CWebModule
{

    public static $arrayForSite = null;

    public function init()
    {
        // this method is called when the module is being created
        // you may place code here to customize the module or the application
        // import the module-level models and components
        $this->setImport(array(
            'vars.models.*',
            'vars.components.*',
        ));
    }

    public function beforeControllerAction($controller, $action)
    {

        self::checkDataFile();
        $component=Yii::createComponent(array(

            'class'=>'begemot.extensions.bootstrap.components.Bootstrap'

        ));
        Yii::app()->setComponent('bootstrap',$component);

        return true;
    }

    public static function getDataFilePath()
    {
        return Yii::getPathOfAlias('webroot') . '/files/vars.data';
    }

    public static function getData()
    {
        return require self::getDataFilePath();
    }

    public static function setData($data)
    {

        $dataFile = VarsModule::getDataFilePath();

        self::checkDataFile();

        file_put_contents($dataFile, '<?php return ' . var_export($data, true) . '?>');
    }

    public static function getVar($varName,$silent=false)
    {

        self::checkDataFile();

        if (self::$arrayForSite === null) {
            $data = self::getData();

            $siteArray = array();

            foreach ($data as $var) {
                $siteArray[$var['varname']] = $var['vardata'];
            }

            self::$arrayForSite = $siteArray;

        }
        $resultVarData = null;

        if (isset(self::$arrayForSite[$varName])) {
            $resultVarData = self::$arrayForSite[$varName];
        } else {
            if (!$silent)
                $resultVarData = 'Переменная - ' . $varName;
        }

        return $resultVarData;
    }

    public static function checkDataFile()
    {
        $dataFile = VarsModule::getDataFilePath();

        if (!file_exists($dataFile)) {
            $fp = fopen($dataFile, "w");
            fwrite($fp, '<?php return array();?>');
            fclose($fp);
        }
    }
}
