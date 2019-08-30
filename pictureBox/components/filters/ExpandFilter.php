<?php
class ExpandFilter extends BaseFilter{
       
    public function make (){
        
        $im = new Imagick($this->fileName);

         $width = round($im->getImageWidth()*0.1);
         $height =  round( $im->getImageHeight()*0.1);

        $pixel = $im->getImageBackgroundColor();
        $im->borderImage($pixel, $width, $height);
        $im->writeImage($this->newFileName);
        $im->clear();
        $im->destroy();




    }
    
}
?>
