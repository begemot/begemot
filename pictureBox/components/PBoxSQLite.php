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
Yii::import('begemot.extensions.SQLiteVault');

class PBoxSQLite
{

    public $pictures;
    public $favPictures;


    public $dataFile = null;
    public $favDataFile = null;

    public $sortArray = null;

    protected $galleryId;
    protected $id;

    protected $count;

    protected $SQLiteVault = null;

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
        }

        if (file_exists($dir . 'lastImageId.php')) {
            $file = $dir . 'lastImageId.php';
            $lastId = require($file);
            $this->saveLastImageId($lastId);
            copy($file, $file . '_old');
        }

        if (file_exists($dir . 'sort.php')) {
            $file = $dir . 'sort.php';
            $sort = require($file);
            $this->saveSortData($sort);
            copy($file, $file . '_old');
        }

        if (file_exists($dir . 'favData.php')) {
            $file = $dir . 'favData.php';
            $favData = require($file);
            $this->saveFavData($favData);
            copy($file, $file . '_old');
        }
    }

    private function saveImages($images)
    {
        $this->SQLiteVault->pushCollection($images);
    }

    public function getImages()
    {
        return $this->SQLiteVault->getCollection();
    }

    private function saveFilters($filters)
    {
        $this->SQLiteVault->pushCollection($filters, 'filters');
    }

    public function getFilters()
    {
        return $this->SQLiteVault->getCollection('filters');
    }

    private function saveLastImageId($id)
    {
        $this->SQLiteVault->setVar('lastImageId', $id);
    }

    public function getLastImageId()
    {
        return $this->SQLiteVault->getVar('lastImageId');
    }

    private function saveSortData($sortData)
    {
        $this->SQLiteVault->pushCollection( $sortData,'sortData');
    }

    public function getSortData()
    {
        return $this->SQLiteVault->getCollection('sortData');
    }

    private function saveFavData($favData)
    {
        $this->SQLiteVault->pushCollection('$favData', $favData);
    }

    public function getFavData()
    {
        return $this->SQLiteVault->getCollection('$favData');
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


        $dataFile = $elementIdDir . '/SQLite.db';

        $this->SQLiteVault = new SQLiteVault($dataFile);

        $this->dataFile = $dataFile;
        $this->oldDataFormatCheckAndConvert($elementIdDir);

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

            foreach ($imagesWithSort as $key => $value) {
                if (!is_array($value)) {
                    unset($imagesWithSort[$key]);
                }

                if (isset($value['params']['show']) && !$value['params']['show']) unset($imagesWithSort[$key]);
            }
        }
        return $imagesWithSort;
    }

    public function getSortArray()
    {
        if ($this->sortArray == null) {

            $this->sortArray = $this->getSortData();
            if (count($this->sortArray) < count($this->pictures)) {
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

        $sortData = $this->sortArray();
        //Определяем максимальный индекс сортировки
        foreach ($sortData as $sortIndex) {
            if ($maxSortPosition < $sortIndex) $maxSortPosition = $sortIndex;
        }

        $images = $this->pictures;

        /*
           Добавляем изображения, которые не отсортированы
            то есть чьих id нет в массиве $sortData
        */

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

}

?>
