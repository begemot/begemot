<?php
class CropResizeUpdateFilter extends BaseFilter{
       
    public function make (){
        
    	$width = $this->param['width'];
    	$height = $this->param['height'];

        $im = new Imagick($this->fileName);

		// get the current image dimensions
		$geo = $im->getImageGeometry();

		if($geo['width'] > $geo['height']){
		    
		    $newWidth = $geo['width'] / ($geo['height'] / $height);
		    $newHeight = $height;
		}else{
		    $newWidth = $width;
		    $newHeight = $get['height'] / ($geo['width'] / $width);
		}
		$im->resizeImage($newWidth,$newHeight, 2, 0.9, true);


		if($geo['width'] > $geo['height']){
			$im->cropImage(
		    	$width, 
		    	$height,
		    	($newWidth / 2 - $width / 2),
				0
		    );
		}
		else {
			$im->cropImage(
		    	$width, 
		    	$height,
		    	0,
		    	($newHeight / 2 - $height / 2)
		    );
		}
	    

		// thumbnail the image

		//$im->ThumbnailImage($width,$height,true);

        //$im->cropThumbnailImage($this->param['width'],$this->param['height']);
        $im->writeImage($this->newFileName);
        $im->clear();
        $im->destroy();  
        
    }
    
}
?>

