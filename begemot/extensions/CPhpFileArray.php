<?php

/**
 * Created by PhpStorm.
 * User: Николай Козлов
 * Date: 26.11.2014
 * Time: 17:49
 */
class CPhpFileArray
{
    public static function crPhpArray($array, $file)
    {


        $code = "<?php
  return
 " . var_export($array, true) . ";
?>";
        file_put_contents($file, $code);


    }
} 