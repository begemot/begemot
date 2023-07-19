<?php
class Image2BoxFilter extends BaseFilter{
       
    public function make (){
        $width = $this->param['width'];
        $height = $this->param['height'];

        /* Instanciate and read the image in */
        $im = new Imagick($this->fileName);

        /* Fit the image into $width x $height box
          The third parameter fits the image into a "bounding box" */
        $im->adaptiveResizeImage($width, $height, true);

        /* Create a canvas with the desired color */
        $canvas = new Imagick();
        $canvas->newImage($width, $height, 'none', 'png');

        /* Get the image geometry */
        $geometry = $im->getImageGeometry();

        /* The overlay x and y coordinates */
        $x = ( $width - $geometry['width'] ) / 2;
        $y = ( $height - $geometry['height'] ) / 2;

        /* Composite on the canvas  */
        $canvas->compositeImage($im, imagick::COMPOSITE_OVER, $x, $y);
        
        $canvas->writeImage($this->newFileName);
        
    }
    
}
?>
