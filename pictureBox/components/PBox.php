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
Yii::import('pictureBox.components.FiltersManager');
Yii::import('begemot.extensions.vault.FileVault');

class PBox
{

    public $pictures;
    public $favPictures;

    public $webDataFile = null;
    public $dataFile = null;
    public $favDataFile = null;

    public $sortArray = null;

    protected $galleryId;
    protected $id;

    protected $count;

    public $vault = null;
    public $filters = null;
    public $subGalleriesList = null;


    public function __construct($galleryId, $id, $subGallery = 'default')
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


        if ($subGallery == 'default') {

            $dataFile = $elementIdDir;
            $this->webDataFile = '/files/pictureBox/' . $galleryId . '/' . $id;
        } else {
            $dataFile = $elementIdDir . '/' . $subGallery;
            if (!file_exists($dataFile)) {
                mkdir($dataFile, 0777);
            }
            $this->webDataFile = '/files/pictureBox/' . $galleryId . '/' . $id . '/' . $subGallery;
        }


        $this->vault = new FileVault($dataFile);

        $this->dataFile = $dataFile;

        $this->oldDataFormatCheckAndConvert($elementIdDir);

        //        $this->favDataFile = $favDatafile = $pictureBoxDir . '/' . $galleryId . '/' . $id . '/favData.php';

        $this->pictures = $this->getImages();
        $this->favPictures = $this->getFavData();

        $this->galleryId = $galleryId;
        $this->id = $id;
        $subGallerySearchPath = '';
        if ($subGallery != 'default') {
            $tmpPBox = new PBox($galleryId, $id);
            $this->filters = $tmpPBox->getImagesRenderRules();
            $subGallerySearchPath = $dataFile . '/../*';
        } else {

            $this->filters = $this->getImagesRenderRules();
            $subGallerySearchPath = $dataFile . '/*';
        }

        $files = glob($subGallerySearchPath, GLOB_ONLYDIR);
        foreach ($files as $key => $file) {
            $files[$key] = basename($file);
        }
        $this->subGalleriesList = $files;
    }




    /**
     * @param $dir
     *
     * Переделывает данные старого формата в новый
     */
    public function oldDataFormatCheckAndConvert($dir)
    {
        $dir = $dir . '/';

        if (file_exists($dir . 'data.php')) {
            $data = require($dir . 'data.php');

            if (isset($data['images'])) {
                $this->saveImages($data['images']);
            }
            if (isset($data['filters'])) {
                $this->saveFilters($data['filters']);
            }
            $file = $dir . 'data.php';
            copy($file, $file . '_old');
            unlink($file);
        }
    }

    public function saveImages($images)
    {
        if ($this->vault->pushCollection($images)) {
            return true;
        } else {
            return false;
        }
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

    public function saveFavData($favData)
    {

        $this->vault->pushCollection($favData, 'favData');
    }

    public function getFavData()
    {
        return $this->vault->getCollection('favData');
    }

    public function saveSubGalleryList()
    {

        $this->vault->pushCollection($this->subGalleriesList, 'subGalleryList');
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

    public function getFirstImage($tag = 'original')
    {

        if (count($this->favPictures) == 0) {

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

            $images = $this->pictures[$pictureId]; //$data['images'][$pictureId];


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
        $sortArray = $this->getSortArray();

        $images = $this->pictures;
        //        print_r($images);
        //        print_r($sortArray);
        $imagesWithSort = [];
        $i = 0;

        foreach ($sortArray as $key => $value) {
            if (isset($this->pictures[$key])) {
                $imagesWithSort[$key] = $this->pictures[$key];
            } else {
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

            if (!isset($images[$key])) unset($sortData[$key]);
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

        $this->saveImages($this->pictures);
        $this->saveSortData($this->sortArray);
        $this->saveFavData($this->favPictures);
    }

    public function copyToAnotherId($idOfCopy)
    {
        $destDir = Yii::getPathOfAlias('webroot') . '/files/pictureBox/' . $this->galleryId . '/' . $idOfCopy;

        if (!file_exists($destDir)) mkdir($destDir);
        $files = glob($this->dataFile . '/*');

        foreach ($files as $file) {

            $file1 = $file;

            $file2 = $destDir . '/' . basename($file);

            copy($file1, $file2);
        }

        $this->correctPathsInFiles($destDir, $idOfCopy);
    }

    private function correctPathsInFiles($dir, $newId)
    {
        $searchStr = $this->galleryId . '/' . $this->id;
        $replaceStr = $this->galleryId . '/' . $newId;
        $file = $dir . '/data_default.php';
        if (file_exists($file)) {
            $this->replaceInfile($file, $searchStr, $replaceStr);
        }

        $file = $dir . '/data_favData.php';
        if (file_exists($file)) {
            $this->replaceInfile($file, $searchStr, $replaceStr);
        }
    }

    private function replaceInfile($file, $strToReplace, $str)
    {
        $file = $file;
        $content = file_get_contents($file);
        $newContent = str_replace($strToReplace, $str, $content);
        if (!file_put_contents($file, $newContent)) {
            throw new Exception('Не удалось сохранить файл');
        }
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

        if ($this->saveImages($this->pictures)) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteAll()
    {

        $dir = $this->dataFile;

        foreach (glob($dir . '/*', GLOB_ONLYDIR) as $filename) {
            //echo $filename;
            $this->deleteFilesInDir($filename);
            rmdir($filename);
        }

        $this->deleteFilesInDir($dir);
        rmdir($dir);
    }

    private function deleteFilesInDir($dir)
    {

        foreach (glob($dir . '/*') as $filename) {
            if (!is_dir($filename))
                if (!unlink($filename)) {
                    throw new Exception('file is not deleted');
                }
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
     *
     *
     * @param $fileArray массив файла, стандартного формата при загрузки файлов POST
     *  должны быть след. параметры
     *  $fileArray['name'] - имя файла оригинала с расширением test.jpg
     *  $fileArray['tmp_name'] - полное имя файла, который физически есть на сервере
     *
     * @param null $lastImageIdParam добавляет в "коробку" изображение с id равным $lastImageIdParam+1
     */
    public function upload($fileArray, $lastImageIdParam = null, $isPostLoaded = true)
    {

        $temp = explode('.', $fileArray['name']);
        $imageExt = end($temp);


        $tmpname = $fileArray['tmp_name'];


        if (is_null($lastImageIdParam)) {
            $imageId = $this->getLastImageId() + 1;
        } else {

            $imageId = $lastImageIdParam;
        }

        $this->saveLastImageId($imageId);


        $basePath = Yii::getPathOfAlias('webroot');
        //$filesPath = $basePath . '/files/pictureBox/' . $this->galleryId . '/' . $this->id . '/';
        $filesPath = $this->dataFile;
        $webFilePath = $this->webDataFile;
        $newOriginalFile = $filesPath . '/' . $imageId . '.' . $imageExt;
        $webOriginalFile = $webFilePath . '/' . $imageId . '.' . $imageExt;

        if ($isPostLoaded) {

            move_uploaded_file($tmpname, $newOriginalFile);
        } else {
            copy($tmpname, $newOriginalFile);
        }


        $resultFiltersStack = array();

        $filters = $this->filters;
        //        if (is_bool($filters)) {
        //            echo '<pre>';
        //            echo 123;
        //            print_r($filters);
        //            echo '<pre>';
        //            die();
        //        }


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

            $pictureForAdd[$filterName] = $webFilePath . '/' . $filteredImageFile;
        }

        $this->pictures[$imageId] = $pictureForAdd;
        $this->saveToFile();
        $this->updateSortData();
        $pictureForAdd['id'] = $imageId;
        return $pictureForAdd;
    }

    /**
     * @param $fileNameOrUrl
     * @param null $lastImageIdParam
     *
     * Добавляет в коробку изображение, которое уже физически есть на сервере и не обязательно было загружено по HTTP.
     * Либо по url на удаленном сервере в публичном доступе
     */
    public function addImagefile($fileNameOrUrl, $lastImageIdParam = null)
    {


        $fileArray = [];
        $fileArray['name'] = basename($fileNameOrUrl);
        $fileArray['tmp_name'] = $fileNameOrUrl;
        return $this->upload($fileArray, $lastImageIdParam, false);
    }

    public function addImagesFromArray()
    {
    }


    private function getNewImageId()
    {
        $newImageId = $this->getLastImageId() + 1;
        $this->saveLastImageId($newImageId);
        return $newImageId;
    }

    public function deleteImage($imageId)
    {
        foreach ($this->pictures[$imageId] as $key => $image) {
            if ($key == 'params') continue;
            $dir = Yii::getPathOfAlias('webroot');


            $file = $dir . $image;

            if (file_exists($file)) {
                unlink($file);
            }
        }
        unset($this->pictures[$imageId]);

        if ($this->saveImages($this->pictures)) {
            return true;
        } else {
            return false;
        }
    }

    public static function boxFilesExist($galleryId, $id)
    {

        $elementIdDir = Yii::getPathOfAlias('webroot') . '/files/pictureBox/' . $galleryId . '/' . $id;
        if (file_exists($elementIdDir)) {
            return true;
        } else {
            return false;
        }
    }

    public function getFirstImageOrCreate($subImageName, $params, $filterName = 'SmartResize')
    {
        $webroot = Yii::getPathOfAlias('webroot');
        $galleryId = $this->galleryId;
        $id = $this->id;

        $image = $this->getFirstImage();


        // Extract the directory, filename, and extension from the original image path
        $directory = dirname($image);
        $filename = pathinfo($image, PATHINFO_FILENAME);
        $extension = pathinfo($image, PATHINFO_EXTENSION);

        // Concatenate the parts to form the new image path
        $resultImage = $directory . '/' . $filename . '_' . $subImageName . '.' . $extension;
        if (!file_exists($webroot.'/'.$resultImage)){
            if ($image) {

                $config = [
                    'nativeFilters' => array(
                        $subImageName => true,
    
                    ),
                    'filtersTitles' => array(
                        $subImageName => 'getFirstImageOrCreate',
                    ),
                    'imageFilters' => array(
    
                        $subImageName => array(
                            0 => array(
                                'filter' => $filterName,
                                'param' => $params,
                            ),
                        ),
    
                    ),
    
                ];
    
                if (strpos($image, '?') !== false) {
                    $clean_url = explode('?', $image)[0];
                } else {
                    $clean_url = $image;
                }
    
                $filterManager = new FiltersManager($this->dataFile . DIRECTORY_SEPARATOR . basename($clean_url), $config);
                $filters = $filterManager->getFilteredImages();
                
                return $this->webDataFile . DIRECTORY_SEPARATOR . array_shift($filters);
            } else {
                return 'noImage.png';
            }
        } else {
            return $resultImage;
        }

    }
}
