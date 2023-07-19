<?php
class ResizeFilter extends BaseFilter{
       
    public function make (){
        //echo $this->fileName;
        $im = new Imagick($this->fileName);
        $im->resizeImage($this->param['width'],$this->param['width'],Imagick::FILTER_BLACKMAN,0);
        $im->writeImage($this->newFileName);
        $im->clear();
        $im->destroy();  
        
    }
    
}
?>
