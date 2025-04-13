<?php

class CropResizeUpdateFilter extends BaseFilter
{

    public function make()
    {

        $width = $this->param['width'];
        $height = $this->param['height'];

        if ($this->checkImageformat($this->fileName)) {

            $im = new Imagick($this->fileName);
        } else return false;



        // get the current image dimensions
        $geo = $im->getImageGeometry();

        // crop the image
        if (($geo['width'] / $width) < ($geo['height'] / $height)) {
            $im->cropImage((int)$geo['width'], (int)floor($height * $geo['width'] / $width), 0, (int)(($geo['height'] - ($height * $geo['width'] / $width)) / 2));
        } else {
            $im->cropImage((int)ceil($width * $geo['height'] / $height), (int)$geo['height'], (int)(($geo['width'] - ($width * $geo['height'] / $height)) / 2), 0);
        }
        // thumbnail the image

        $im->ThumbnailImage($width, $height, true);

        //$im->cropThumbnailImage($this->param['width'],$this->param['height']);
        $im->writeImage($this->newFileName);
        $im->clear();
        $im->destroy();
    }
}
