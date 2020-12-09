<?php
class BaseFilter{
    
    protected $fileName = null;
    protected $newFileName = null;
    protected $param = null;
    
    static public $resultExt = 'jpg';
    
    public function __construct($_fileName,$_newFileName,$_param){
        
        $this->fileName = $_fileName;
        $this->newFileName = $_newFileName;
        $this->param = $_param;
    }
    
    public function make (){
        

    }
    
}
?>
