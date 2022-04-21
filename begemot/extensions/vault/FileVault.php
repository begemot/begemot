<?php
/**
 * Created by PhpStorm.
 * User: Николай Козлов
 * Date: 12.03.2021
 * Time: 14:18
 */

require(__DIR__ . '/Vault.php');

class FileVault implements Vault
{

    private $dataPath;

    public function __construct($vaultPath)
    {
        $this->dataPath = $vaultPath;
    }

    public function pushCollection($collection, $tag = 'default')
    {
        $collectionFileName = '';

        if ($tag == 'default') {
            $fileName = $this->dataPath . '/data_default.php';
        } else {
            $fileName = $this->dataPath . '/data_' . $tag . '.php';
        }

        $this->startSession();

        if (isset($_SESSION['fileValut'][$fileName])){
            $_SESSION['fileValut'][$fileName] = $collection;
            session_commit();
        }


        if ($this->crPhpArrayFile($fileName, $collection)) {
            return true;
        } else {
            return false;
        }
    }

    public function getCollection($tag = 'default')
    {
        if ($tag == 'default') {
            $fileName = $this->dataPath . '/data_default.php';
        } else {
            $fileName = $this->dataPath . '/data_' . $tag . '.php';
        }
        if (!file_exists($fileName)) $_SESSION['fileValut'][$fileName] = [];

        $this->startSession();

        if (isset($_SESSION['fileValut'][$fileName]))
            return $_SESSION['fileValut'][$fileName];


        if (file_exists($fileName)) {
            $data =  require($fileName);
            $_SESSION['fileValut'][$fileName] = $data;
            return $data;
        } else {
            return [];
        }
    }

    public function setVar($name, $value)
    {

        $this->startSession();
        $varFileName = $this->dataPath . '/' . $name . '.php';


        $_SESSION['fileValut'][$varFileName] = $value;

        $this->crPhpArrayFile($varFileName, $value);
    }

    public function getVar($name)
    {
        $this->startSession();
        $varFileName = $this->dataPath . '/' . $name . '.php';

        if (!file_exists($varFileName)) $_SESSION['fileValut'][$varFileName] = false;

        if (isset($_SESSION['fileValut'][$varFileName]))
            return $_SESSION['fileValut'][$varFileName];

        if (file_exists($varFileName)) {

            $data = require $varFileName;
            $_SESSION['fileValut'][$varFileName] = $data;
            return $_SESSION['fileValut'][$varFileName];
        } else {
            return false;
        }
    }

    private function crPhpArrayFile($fileName, $data)
    {

        $code = "<?php
  return
 " . var_export($data, true) . ";
?>";
        if (file_put_contents($fileName, $code)) {
            return true;
        } else {
            return false;
        }

    }

    private function startSession()
    {
       if (PHP_SESSION_ACTIVE != session_status()){
           session_start();
       }

    }

}