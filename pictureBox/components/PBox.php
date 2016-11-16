<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PBox
 *
 * @author Антон
 */
class PBox
{

    public $pictures;
    public $favPictures;


    public $dataFile = null;
    public $favDataFile = null;

    public $sortArray = null;

    protected $galleryId;
    protected $id;

    protected $count;

    public function __construct($galleryId, $id)
    {


        $pictureBoxDir = Yii::getPathOfAlias('webroot') . '/files/pictureBox';
        if (!file_exists($pictureBoxDir)) {
            mkdir($pictureBoxDir, 0777);
        }

        $idDir = Yii::getPathOfAlias('webroot') . '/files/pictureBox/' . $galleryId;
        if (!file_exists($idDir)) {
            mkdir($idDir, 0777);
        }

        $elementIdDir = Yii::getPathOfAlias('webroot') . '/files/pictureBox/' . $galleryId . '/' . $id;
        if (!file_exists($elementIdDir)) {
            mkdir($elementIdDir, 0777);
        }

        $dataFile = $elementIdDir . '/data.php';

        $this->dataFile = $dataFile;

        $this->favDataFile = $favDatafile = $pictureBoxDir . $galleryId . '/' . $id . '/favData.php';

        if (file_exists($dataFile)) {
            $array = require($dataFile);
            $this->pictures = $array['images'];
            $this->galleryId = $galleryId;
            $this->id = $id;

            if (file_exists($favDatafile)) {

                $array = require($favDatafile);
                if (count($array) > 0) {
                    $this->favPictures = require($favDatafile);
                }

            }

        }
    }

    static public function getTitleFromArray($image)
    {
        if (isset($image['title'])) {
            return $image['title'];
        } else {
            return '';
        }
    }

    static public function getAltFromArray($image)
    {
        if (isset($image['alt'])) {
            return $image['alt'];
        } else {
            return '';
        }
    }


    public function getImage($id, $tag)
    {
        if (isset($this->pictures[$id][$tag])) {
            return $this->pictures[$id][$tag];
        } else {
            return false;
        }
    }

    public function getImageHtml($id, $tag, $htmlOptions = array())
    {

        $src = $this->getImage($id, $tag);

        $alt = $this->getAlt($id);
        $title = $this->getTitle($id);

        return CHtml::image($src, $alt, array_merge(array('title' => $title), $htmlOptions));
        return '<img src="' . $src . '" alt="' . $alt . '" title="' . $title . '"/>';
    }

    public function getFirstImageHtml($tag, $htmlOptions = array())
    {

        if (is_null($this->favPictures)) {
            $array = $this->pictures;
        } else {
            $array = $this->favPictures;
        }
        if (is_array($array)) {
            $id = key($array);

            return $this->getImageHtml($id, $tag, $htmlOptions);
        } else {
            return '<img src=""/>';
        }

    }

    public function getFirstImage($tag)
    {

        if (is_null($this->favPictures)) {
            $array = $this->pictures;
        } else {
            $array = $this->favPictures;
        }
        if (is_array($array)) {
            $id = key($array);

            return $this->getImage($id, $tag);
        } else {
            return '';
        }

    }

    public function getImageCount()
    {
        return count($this->pictures);
    }

    public function getTitle($id)
    {
        if (isset($this->pictures[$id]['title'])) {
            return $this->pictures[$id]['title'];
        } else {
            return false;
        }
    }

    public function getAlt($id)
    {
        if (isset($this->pictures[$id]['alt'])) {
            return $this->pictures[$id]['alt'];
        } else {
            return false;
        }
    }

    /**
     *
     * Физическое удаление основного файла и всех его фильтрованных копий.
     *
     * @param type $id Идентификатор хранилища
     * @param type $elementId Идентификатор ячейки хранилища
     * @param type $pictureId Идентификатор изображения
     * @param type $data Массив всех изображений
     */
    public function deleteImageFiles($pictureId)
    {

        if (isset($this->pictures[$pictureId])) {

            $images = $this->pictures[$pictureId];//$data['images'][$pictureId];


            foreach ($images as $image) {

                $fileFullName = Yii::app()->basePath . '/../' . $image;

                if (file_exists($fileFullName)) {
                    unlink($fileFullName);
                }
            }


            unset($this->pictures[$pictureId]);

            if (isset($this->favPictures[$pictureId])) {
                unset($this->favPictures[$pictureId]);
            }
        }

    }

    public function getSortedImageList()
    {
        Yii::import("pictureBox.controllers.DefaultController");
        Yii::import("pictureBox.components.PictureBox");
        $sortArray = array_flip($this->getSortArray());
        ksort($sortArray);

        $images = $this->pictures;


        $imagesWithSort = [];
        if (is_array($images)) {


            $imagesWithSort = array_replace(array_fill_keys($sortArray, ''), $images);

            foreach ($imagesWithSort as $key=>$value){
                if (!is_array($value)){
                    unset($imagesWithSort[$key]);
                }
            }
        }
        return $imagesWithSort;
    }

    public function getSortArray()
    {
        if ($this->sortArray == null) {
            $pictureBoxDir = Yii::getPathOfAlias('webroot') . '/files/pictureBox/';
            $elemDir = $pictureBoxDir . '/' . $this->galleryId . '/' . $this->id . '/';
            $sortFile = $elemDir . 'sort.php';
            if (!file_exists($sortFile)) {
                DefaultController::updateSortData($this->galleryId, $this->id);
            }
            $this->sortArray = require($sortFile);
        }

        return $this->sortArray;

    }

    public function saveSortArray()
    {
        $pictureBoxDir = Yii::getPathOfAlias('webroot') . '/files/pictureBox/';
        $elemDir = $pictureBoxDir . '/' . $this->galleryId . '/' . $this->id . '/';
        $sortFile = $elemDir . 'sort.php';

        PictureBox::crPhpArr($this->sortArray, $sortFile);
    }

    public function saveToFile()
    {

        $data = array();
        $data['images'] = $this->pictures;

        PictureBox::crPhpArr($data, $this->dataFile);

        $data = $this->favPictures;

        PictureBox::crPhpArr($data, $this->favDataFile);

    }

    public function copyAllOriginalImages($destDir)
    {
        $imagesArray = $this->pictures;

        $webRoot = Yii::getPathOfAlias('webroot');

        foreach ($imagesArray as $imageArray) {
            if (isset($imageArray['original'])) {
                echo $file1 = $webRoot . $imageArray['original'];
                echo $file2 = $destDir . '/' . basename($imageArray['original']);
                copy($file1, $file2);
            }
        }

    }

    public function swapImages($pictureid1, $pictureid2)
    {
        $sortArrray = $this->getSortArray();

        $tmpElement = $sortArrray[$pictureid1];
        $sortArrray[$pictureid1] = $sortArrray[$pictureid2];
        $sortArrray[$pictureid2] = $tmpElement;

        $this->sortArray = $sortArrray;

        $this->saveSortArray();
    }

    public function deleteAll()
    {

        $dir = dirname($this->dataFile);

        foreach (glob($dir . '/*.*') as $filename) {
            unlink($filename);
        }
    }
}

?>
