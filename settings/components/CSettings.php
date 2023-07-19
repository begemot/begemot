<?php

Yii::import('begemot.extensions.vault.FileVault');
class CSettings
{
    private $settingsId;
    public $settings;
    private $settingsFileVault;

    private $settingsFilesBaseDir = '/files/settings/';

    public function __construct($settingsId)
    {
        $this->settingsId = $settingsId;
        $this->loadSettings();
    }

//    private function getSettingsFile()
//    {
//        return Yii::getPathOfAlias('application.modules.' . $this->moduleName . '.settings.php');
//    }

    private function loadSettings()
    {

            $this->settingsFileVault = new FileVault(Yii::getPathOfAlias('webroot').$this->settingsFilesBaseDir.'/'.$this->settingsId);
            $this->settings = $this->settingsFileVault->getCollection();
    }

    public function getSettingDigit($paramName)
    {
        return isset($this->settings[$paramName]) ? (int)$this->settings[$paramName] : 0;
    }

    public function getSettingBoolean($paramName)
    {
        return isset($this->settings[$paramName]) ? (bool)$this->settings[$paramName] : false;
    }

    public function getSettingArray($paramName)
    {
        return isset($this->settings[$paramName]) ? (array)$this->settings[$paramName] : [];
    }

    public function getSettingString($paramName)
    {
        return isset($this->settings[$paramName]) ? (string)$this->settings[$paramName] : '';
    }

    public function setSettingDigit($paramName, $value)
    {
        $this->settings[$paramName] = (int)$value;
        $this->saveSettings();
    }
    public function setSettingBoolean($paramName, $value)
    {
        $this->settings[$paramName] = (bool)$value;
        $this->saveSettings();
    }
    public function setSettingArray($paramName, $value)
    {
        $this->settings[$paramName] = (array)$value;
        $this->saveSettings();
    }

    public function setSettingString($paramName, $value)
    {
        $this->settings[$paramName] = (string)$value;
        $this->saveSettings();
    }

    private function saveSettings()
    {
        $this->settingsFileVault->pushCollection($this->settings);
    }
}
