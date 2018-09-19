<?php
/**
 * Created by PhpStorm.
 * User: Николай Козлов
 * Date: 08.11.2017
 * Time: 22:11
 */

class LogViewer
{
    public $logTextArray = ['Нет строк'];
    public $reverse = true;
    private $fileDoesNotExistFlag = false;
    private $lastReadedFile = null;
    private $colorLineRules = [];

    public function LogViewer($logFile)
    {
        $this->lastReadedFile = $logFile;
        if (file_exists($logFile))
            if ($this->reverse) {
                $this->logTextArray = array_reverse(file($logFile));
            }else{
                $this->logTextArray = file($logFile);
            }
        else
            $this->fileDoesNotExistFlag = true;
    }

    public function report()
    {
        if (!$this->fileDoesNotExistFlag) {

            foreach ($this->logTextArray as $lineNum => $line) {
                echo $lineNum . ':';
                if (count($this->colorLineRules) > 0) {
                    $colorFlag = false;
                    foreach ($this->colorLineRules as $rule) {

                        if (preg_match($rule['regExp'], $line, $matches)) {
                            echo '<span style="color:' . $rule['color'] . '">';
                            echo $line;
                            echo '</span>';
                            $colorFlag = true;
                            break;
                        }
                    }
                    if (!$colorFlag) echo $line;
                } else
                    echo $line;
                echo '<br>';
            }
        } else {
            echo 'Файл не найдет!<br>';
            echo $this->lastReadedFile;
        }
    }

    public function addColorLineRule($regExp, $color)
    {
        $this->colorLineRules[] = ['regExp' => $regExp, 'color' => $color];
    }
}