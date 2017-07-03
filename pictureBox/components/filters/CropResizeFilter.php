<?php
class CropResizeFilter extends BaseFilter{
       
    public function make(){
        
        $im = new Imagick($this->fileName);
        $im->resizeImage($this->param['width'],$this->param['height'],2,0.9);
        $im->writeImage($this->newFileName);
        $im->clear();
        $im->destroy();  
        
    }
    
}
?>
