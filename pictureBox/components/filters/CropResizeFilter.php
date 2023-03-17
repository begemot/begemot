<?php
class CropResizeFilter extends BaseFilter{
       
    public function make (){

        if ($this->checkImageformat($this->fileName)) {

            $im = new Imagick($this->fileName);
        } else return false;


        $im->resizeImage ($this->param['width'],$this->param['height'],2,0.9);
        $im->writeImage($this->newFileName);
        $im->clear();
        $im->destroy();  
        
    }
    
}
?>
