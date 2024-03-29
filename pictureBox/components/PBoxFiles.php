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

Yii::import('pictureBox.components.PictureBox');
Yii::import('begemot.extensions.vault.FileVault');

class PBoxFiles
{

    public $pictures;
    public $favPictures;


    public $dataFile = null;
    public $favDataFile = null;

    public $sortArray = null;

    protected $galleryId;
    protected $id;

    protected $count;

    public $vault = null;


//    public function oldDataFormatCheckAndConvert($dir)
//    {
//        $dir = $dir . '/';
//
//        if (file_exists($dir . 'data.php')) {
//            $data = require($dir . 'data.php');
//
//            if (isset($data['images'])) {
//                $this->saveImages($data['images']);
//            }
//            if (isset($data['filters'])) {
//                $this->saveFilters($data['filters']);
//            }
//            $file = $dir . 'data.php';
//            copy($file, $file . '_old');
//            unlink($file);
//        }
//
//        if (file_exists($dir . 'lastImageId.php')) {
//            $file = $dir . 'lastImageId.php';
//            $lastId = require($file);
//            $this->saveLastImageId($lastId);
//            copy($file, $file . '_old');
//            unlink($file);
//        }
//
//        if (file_exists($dir . 'sort.php')) {
//            $file = $dir . 'sort.php';
//            $sort = require($file);
//            $this->saveSortData($sort);
//            copy($file, $file . '_old');
//            unlink($file);
//        }
//
//        if (file_exists($dir . 'favData.php')) {
//            $file = $dir . 'favData.php';
//            $favData = require($file);
//            $this->saveFavData($favData);
//            copy($file, $file . '_old');
//            unlink($file);
//        }
//    }

    private function saveImages($images)
    {
        $this->vault->pushCollection($images);
    }

    public function getImages()
    {
        return $this->vault->getCollection();
    }

    private function saveFilters($filters)
    {
        $this->vault->pushCollection($filters, 'filters');
    }

    public function getFilters()
    {
        return $this->vault->getCollection('filters');
    }

    private function saveLastImageId($id)
    {
        $this->vault->setVar('lastImageId', $id);
    }

    public function getLastImageId()
    {
        return $this->vault->getVar('lastImageId');
    }

    public function saveSortData($sortData)
    {

        $this->vault->pushCollection($sortData, 'sortData');
    }

    public function getSortData()
    {
        $data = $this->vault->getCollection('sortData');
        if (!is_array($data))
            return [];
        else
            return $data;
    }

    private function saveFavData($favData)
    {

        $this->vault->pushCollection($favData, '$favData');
    }

    public function getFavData()
    {
        return $this->vault->getCollection('$favData');
    }

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


        $dataFile = $elementIdDir ;

        $this->vault = new FileVault($dataFile);

        $this->dataFile = $dataFile;
//        $this->oldDataFormatCheckAndConvert($elementIdDir);

//        $this->favDataFile = $favDatafile = $pictureBoxDir . '/' . $galleryId . '/' . $id . '/favData.php';

        $this->pictures = $this->getImages();
        $this->favPictures = $this->getFavData();

        $this->galleryId = $galleryId;
        $this->id = $id;


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
            $array = $this->getSortedImageList();


        } else {
            $array = $this->favPictures;
        }

        if (is_array($array) && count($array) > 0) {

            $keys = array_keys($array);
            $id = $keys[0];


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

    public function getSortedImageList($withOrderData = false)
    {
        Yii::import("pictureBox.controllers.DefaultController");
        Yii::import("pictureBox.components.PictureBox");
        $sortArray = $this->getSortArray();

        $images = $this->pictures;

        $imagesWithSort = [];
        $i = 0;
        foreach ($sortArray as $key => $value) {
            $imagesWithSort[$key] = $this->pictures[$key];
            if ($withOrderData) {
                $imagesWithSort[$key]['order'] = $i;
                $i++;
            }
        }


        return $imagesWithSort;
    }

    public function getSortArray()
    {
        if ($this->sortArray == null) {

            $this->sortArray = $this->getSortData();
            if (count($this->sortArray) == 0) {

                $this->updateSortData();
            }
        }
        return $this->sortArray;
    }

    public function updateSortData()
    {
        $id = $this->galleryId;
        $elementId = $this->id;

        $maxSortPosition = 0;

        $sortData = $this->sortArray;
        //Определяем максимальный индекс сортировки
        if (!is_array($sortData)) $sortData = [];
        foreach ($sortData as $sortIndex) {
            if ($maxSortPosition < $sortIndex) $maxSortPosition = $sortIndex;
        }

        $images = $this->pictures;

        /*
           Добавляем изображения, которые не отсортированы
            то есть чьих id нет в массиве $sortData
        */
        if (!is_array($images)) $images = [];
        foreach ($images as $key => $image) {
            if (!isset($sortData[$key])) {
                $maxSortPosition++;
                $sortData[$key] = $maxSortPosition;
            }
        }

        /*
         *  теперь в обратную сторону. смотрим, что бы
         * все id изображений существовали в массиве $sortData,
         * если нет, это значит изображение удалили и из сортировки его тоже надо удалить
         */

        foreach ($sortData as $key => $sortKey) {

            if (!isset($images[$key])) unset ($sortData[$key]);

        }

        $this->saveSortData($sortData);
    }

//    public function saveSortArray()
//    {
//        $pictureBoxDir = Yii::getPathOfAlias('webroot') . '/files/pictureBox/';
//        $elemDir = $pictureBoxDir . '/' . $this->galleryId . '/' . $this->id . '/';
//        $sortFile = $elemDir . 'sort.php';
//
//        PictureBox::crPhpArr($this->sortArray, $sortFile);
//    }

    public function saveToFile()
    {


        $data = array();
        $data['images'] = $this->pictures;

//        PictureBox::crPhpArr($data, $this->dataFile);
        $this->saveImages($this->pictures);
        $this->saveSortData($this->sortArray);
        $this->saveFavData($this->favPictures);

        //   $this->SQLiteVault->pushCollection($data['images']);
        //s  $this->SQLiteVault->pushCollection($this->favPictures, 'favData');
//        $data = $this->favPictures;

//        PictureBox::crPhpArr($data, $this->favDataFile);

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
        $sortArrray = $this->sortArray;

        $tmpElement = $sortArrray[$pictureid1];
        $sortArrray[$pictureid1] = $sortArrray[$pictureid2];
        $sortArrray[$pictureid2] = $tmpElement;

        $this->sortArray = $sortArrray;

        $this->saveSortData();
    }

    public function changeImageShown($pictureid)
    {


        if (isset($this->pictures[$pictureid]['params']['show'])) {
            if ($this->pictures[$pictureid]['params']['show'] == true) {
                $this->pictures[$pictureid]['params']['show'] = false;
            } else {
                $this->pictures[$pictureid]['params']['show'] = true;
            }

        } else {
            $this->pictures[$pictureid]['params']['show'] = false;
        }
        $this->saveToFile();

        return $this->pictures[$pictureid]['params']['show'];
    }

    public function deleteAll()
    {

        $dir = dirname($this->dataFile);

        foreach (glob($dir . '/*.*') as $filename) {
            unlink($filename);
        }
    }

    /**
     * Если имена файлов в data содержат неверные директории, эта функция должна обновить все пути
     */
    public function filesBasePathChange()
    {
        foreach ($this->pictures as $imageId => $picture) {
            foreach ($picture as $filterId => $file) {
                if ($filterId != 'title' and $filterId != 'title' and $filterId != 'params') {

                    $newFile = '/files/pictureBox/' . $this->galleryId . '/' . $this->id . '/' . basename($file);
                    $this->pictures[$imageId][$filterId] = $newFile;

                    $this->saveToFile();

                }
            }
        }

    }

    public function addPictureToFav($pictureId)
    {
        $this->favPictures[$pictureId] = $this->pictures[$pictureId];
        $this->saveToFile();
    }


    public function setImagesRenderRules($config)
    {
        $this->vault->setVar('imagesConfig', $config);
    }

    public function getImagesRenderRules()
    {
        $config = $this->vault->getVar('imagesConfig');

        return $config;
    }

    /**
     * Загрузить можно любой файл по абсолютному пути на сервере. Загрузит оригинал, нарежет, сохранит.
     *
     * @param $fileArray массив файлов для загрузки и нарезки превьюшек
     */
    public function upload($fileArray)
    {


        $temp = explode('.', $fileArray['name']);
        $imageExt = end($temp);


        $tmpname = $fileArray['tmp_name'];

        $imageId = $this->getLastImageId() + 1;
        $this->saveLastImageId($imageId);


        $basePath = Yii::getPathOfAlias('webroot');
        $filesPath = $basePath . '/files/pictureBox/' . $this->galleryId . '/' . $this->id . '/';
        $webFilePath = '/files/pictureBox/' . $this->galleryId . '/' . $this->id . '/';
        $newOriginalFile = $filesPath . $imageId . '.' . $imageExt;
        $webOriginalFile = $webFilePath . $imageId . '.' . $imageExt;
        move_uploaded_file($tmpname, $newOriginalFile);

        $filters = $this->getImagesRenderRules();


        $resultFiltersStack = array();


        foreach ($filters['nativeFilters'] as $filterName => $toggle) {
            if ($toggle && isset($filters['imageFilters'][$filterName])) {
                $resultFiltersStack[$filterName] = $filters['imageFilters'][$filterName];
            }
        }

        $config['imageFilters'] = $resultFiltersStack;

        $filterManager = new FiltersManager($newOriginalFile, $config);
        $filters = $filterManager->getFilteredImages();
        $pictureForAdd = [];
        $pictureForAdd['original'] = $webOriginalFile;
        foreach ($filters as $filterName => $filteredImageFile) {

            $pictureForAdd[$filterName] = $webFilePath . $filteredImageFile;
        }

        $this->pictures[$imageId] = $pictureForAdd;
        $this->saveToFile();
        $this->updateSortData();
    }

    private function getNewImageId()
    {
        $newImageId = $this->getLastImageId() + 1;
        $this->saveLastImageId($newImageId);
        return $newImageId;
    }

    public function deleteImage($imageId){
        foreach($this->pictures[$imageId] as $image){
            $dir = Yii::getPathOfAlias('webroot');
            $file = $dir.$image;
            print_r($file);
            echo '%%%';
            if (file_exists($file)){
                unlink($file);
            }
        }
        unset($this->pictures[$imageId]);
        print_r($this->pictures);
        if ($this->saveToFile()) return;
    }


}

?>
