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

        if (!file_exists($vaultPath)) {
            mkdir($vaultPath, 0777, true);
        }
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

        if (isset($_SESSION['fileValut'][$fileName])) {
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

        //        $this->startSession();
        //
        //        if (isset($_SESSION['fileValut'][$fileName]))
        //            return $_SESSION['fileValut'][$fileName];


        if (file_exists($fileName)) {
            $data =  require($fileName);
            $_SESSION['fileValut'][$fileName] = $data;
            if (is_array($data)) {

                return $data;
            } else return [];
        } else {
            return [];
        }
    }

    public function setVar($name, $value)
    {

        $this->startSession();
        $varFileName = $this->dataPath . '/' . $name . '.php';


        $_SESSION['fileValut'][$varFileName] = $value;

        if ($this->crPhpArrayFile($varFileName, $value)) {
            return true;
        }
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

        $file = $fileName;
        $data = $code;

        $fp = fopen($file, 'w');

        if (flock($fp, LOCK_EX)) { // acquire an exclusive lock
            fwrite($fp, $data);
            fflush($fp); // flush output before releasing the lock
            flock($fp, LOCK_UN); // release the lock
            fclose($fp);

            return true;
        } else {
            fclose($fp);
            return false;
        }



        // if (file_put_contents($fileName, $code)) {
        // return true;
        // } else {
        // return false;
        // }
    }

    private function startSession()
    {
        if (!headers_sent() && PHP_SESSION_ACTIVE != session_status()) {
            session_start();
        }
    }
}
