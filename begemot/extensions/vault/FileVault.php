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

        if ($this->crPhpArrayFile($fileName,$collection)){
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
        if (file_exists($fileName)) {
            return require($fileName);
        } else {
            return [];
        }
    }

    public function setVar($name, $value)
    {

        $varFileName = $this->dataPath.'/'.$name.'.php';
        $this->crPhpArrayFile($varFileName,$value);
    }

    public function getVar($name)
    {
        $varFileName = $this->dataPath.'/'.$name.'.php';
        if (file_exists($varFileName)){

            return require $varFileName;
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
        if (file_put_contents($fileName, $code)){
            return true;
        } else {
            return false;
        }

    }


}