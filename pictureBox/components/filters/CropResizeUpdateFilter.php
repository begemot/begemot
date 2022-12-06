<?php
class CropResizeUpdateFilter extends BaseFilter{
       
    public function make (){

    	$width = $this->param['width'];
    	$height = $this->param['height'];

        $im = new Imagick($this->fileName);

		// get the current image dimensions
		$geo = $im->getImageGeometry();

		// crop the image
		if(($geo['width']/$width) < ($geo['height']/$height))
		{
		    $im->cropImage($geo['width'], floor($height*$geo['width']/$width), 0, (($geo['height']-($height*$geo['width']/$width))/2));
		}
		else
		{
		    $im->cropImage(ceil($width*$geo['height']/$height), $geo['height'], (($geo['width']-($width*$geo['height']/$height))/2), 0);
		}
		// thumbnail the image

		$im->ThumbnailImage($width,$height,true);

        //$im->cropThumbnailImage($this->param['width'],$this->param['height']);
        $im->writeImage($this->newFileName);
        $im->clear();
        $im->destroy();  
        
    }
    
}
?>
