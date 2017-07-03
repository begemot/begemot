<?php
class CropResizeUpdateFilter extends BaseFilter{
       
    public function make (){
        
    	$width = $this->param['width'];
    	$height = $this->param['height'];

        $im = new Imagick($this->fileName);

		// get the current image dimensions
		$geo = $im->getImageGeometry();

	    $im->cropImage(
	    	$width, 
	    	$height,
	    	round($geo['width'] /2) - $width / 2,
			round($geo['height'] /2) - $height / 2
	    );

		// thumbnail the image

		//$im->ThumbnailImage($width,$height,true);

        //$im->cropThumbnailImage($this->param['width'],$this->param['height']);
        $im->writeImage($this->newFileName);
        $im->clear();
        $im->destroy();  
        
    }
    
}
?>
