<?php
/**
 * Created by PhpStorm.
 * User: Николай Козлов
 * Date: 12.03.2021
 * Time: 14:10
 */

interface Vault
{

    public function __construct($vaultPath);

    public function pushCollection($collection,$tag = 'default');
    public function getCollection($tag = 'default');

    public function setVar($name, $value);
    public function getVar($name);

}