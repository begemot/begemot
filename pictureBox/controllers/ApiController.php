<?php

/**
 * @var Yii
 */

Yii::import('pictureBox.components.PBox');

class ApiController extends Controller
{

    public function actionGetData($galleryId, $id, $subGallery = 'default')
    {


        $pBox = new PBox($galleryId, $id, $subGallery);

        $images = $pBox->getSortedImageList();

        $favData = $pBox->getFavData();
        $lastImageId = $pBox->getLastImageId();
        $sortedData = $pBox->getSortArray();
        $config = $pBox->filters;

        $subGalleryList = $pBox->subGalleriesList;

        foreach ($images as $key => $imageData) {
            $images[$key]['id'] = $key;
            if (isset($sortedData[$key])) {
                $images[$key]['order'] = $sortedData[$key];
            }

            if (!isset($images[$key]['params']['show'])) {
                $images[$key]['params']['show'] = true;
            }

            if (isset($favData[$key])) {
                $images[$key]['params']['fav'] = true;
            } else {
                $images[$key]['params']['fav'] = false;
            }
            $images[$key]['params']['deleted'] = false;

            if (!isset($images[$key]['title'])) {
                $images[$key]['title'] = '';
            }
            if (!isset($images[$key]['alt'])) {
                $images[$key]['alt'] = '';
            }

            foreach ($images[$key] as $imageKey => $image) {
                if (
                    $imageKey != 'id' &&
                    $imageKey != 'order' &&
                    $imageKey != 'show' &&
                    $imageKey != 'params' &&
                    $imageKey != 'title' &&
                    $imageKey != 'alt'
                ) {
                    $images[$key][$imageKey] = $image;
                }
            }
        }

        $allData = [
            'images' => $images,
            'favData' => $pBox->getFavData(),
            'lastImageId' => $pBox->getLastImageId(),
            'sortedData' => $pBox->getSortArray(),
            'config' => $config,
            'subGalleryList' => $subGalleryList
        ];


        echo json_encode($allData, JSON_NUMERIC_CHECK);
    }

    public function actionObectSave($galleryId, $id, $subGallery = 'default')
    {

        $pBox = new PBox($galleryId, $id, $subGallery);

        $postdata = json_decode(file_get_contents("php://input"), true);

        $sortData = [];
        $images = [];
        $favData = [];
        foreach ($postdata['images'] as $order => $image) {
            $sortData[$image['id']] = $order;
            unset($image['order']);

            $images[$image['id']] = $image;

            if ($image['params']['fav']) {
                $favData[$image['id']] = $images[$image['id']];
                unset($favData[$image['id']]['fav']);
            }

            unset($images[$image['id']]['id']);
        }


        if (isset($postdata['deleted'])) {

            foreach ($postdata['deleted'] as $item) {
                if (!is_array($item)) continue;
                $webroot = Yii::getPathOfAlias('webroot');
                foreach ($item as $key => $imageFile) {
                    if (is_array($imageFile)) continue;
                    if ($key == 'title') continue;
                    if ($key == 'alt') continue;
                    //                    echo $webroot.$imageFile;
                    if (file_exists($webroot . $imageFile)) {

                        unlink($webroot . $imageFile);
                    }
                }
            }
        }

        $pBox->saveSortData($sortData);
        $pBox->saveImages($images);
        $pBox->saveFavData($favData);
    }

    public function actionGetImages($galleryId, $id)
    {

        $pBox = new PBox($galleryId, $id);

        $images = $pBox->getSortedImageList(true);

        echo json_encode($images);
    }

    public function actionGetGalleries($galleryId, $id)
    {

        $configDirectoryPath = Yii::getPathOfAlias('webroot.files.pictureBoxConfig') . DIRECTORY_SEPARATOR . $galleryId;

        // Путь к файлу конфигурации
        $filePath = $configDirectoryPath . DIRECTORY_SEPARATOR . 'config.json';
        $resultList = ['default' => ['title' => 'Основная']];
        if (file_exists($filePath)) {
            // Read the file contents
            $fileContents = file_get_contents($filePath);

            // Decode the JSON to an associative array
            $config = json_decode($fileContents, true);

            // Check if JSON decoding was successful
            if (json_last_error() === JSON_ERROR_NONE) {
                // Successfully retrieved the configuration
                // You can now use $config array as needed
                if (isset($config['subGalleries'])) {
                    $resultList = array_merge($resultList, $config['subGalleries']);
                }
            } else {
                // Handle JSON decode error
                $pBox = new PBox($galleryId, $id);
                $wr = Yii::getPathOfAlias('webroot');
                $filelist = glob($wr . '/' . $pBox->webDataFile . '/*', GLOB_ONLYDIR);


                foreach ($filelist as $dir) {
                    $resultList[] = basename($dir);
                }
            }
        } else {
            // Handle the case where the file does not exist
            throw new Exception('Configuration file does not exist: ' . $filePath);
        }




        echo (json_encode($resultList));
    }
    //    public function actionImageDelete($galleryId, $id, $imageId)
    //    {
    //
    //        $pBox = new PBox($galleryId, $id);
    //        if ($pBox->deleteImage($imageId)) {
    //            echo json_encode($pBox->pictures);
    //        } else {
    //
    //            throw new Exception('Error of sve images to file');
    //        }
    //
    //
    //    }

    public function actionImageShownChange($galleryId, $id, $imageId)
    {

        $pBox = new PBox($galleryId, $id);

        if ($pBox->changeImageShown($imageId)) {
            echo json_encode($pBox->pictures);
        } else {

            throw new Exception('Error of save images to file');
        }
    }

    public function actionGetSort($galleryId, $id)
    {

        $pBox = new PBox($galleryId, $id);


        $sort = $pBox->getSortData();

        echo json_encode($sort);
    }

    public function actionSetOrder($galleryId, $id)
    {


        $postdata = json_decode(file_get_contents("php://input"));


        $sort = array_combine(array_keys($postdata->images), array_column($postdata->images, 'id'));
        $sort = array_flip($sort);


        $pbox = new PBox($galleryId, $id);
        $pbox->sortArray = $sort;
        $pbox->saveToFile();
    }

    public function actionUpload()
    {
        $subGallery = 'default';
        if (isset($_REQUEST['subGallery']))
            $subGallery = $_REQUEST['subGallery'];

        $this->layout = 'pictureBox.views.layouts.ajax';

        $id = $_POST['galleryId'];
        $elementId = $_POST['id'];
        $lastImageId = $_POST['lastId'];
        $pbox = new PBox($id, $elementId, $subGallery);

        $config = $pbox->filters;


        if (!empty($_FILES)) {

            $addedImages = [];
            foreach ($_FILES as $fileArray) {

                $addedImages[] = $pbox->upload($fileArray, $lastImageId);
            }

            echo json_encode($addedImages, JSON_NUMERIC_CHECK);
        }
    }


    public function actionGetLastItemId($galleryId, $id)
    {
        $pbox = new PBox($galleryId, $id);

        $lastId = $pbox->getLastImageId();

        echo json_encode($lastId);
    }


    public function actionSavePreviewImage($gallery, $id, $imageId, $filterName, $subGallery = 'default')
    {

        if (isset($_FILES['croppedImage'])) {

            $pbox = new PBox($gallery, $id, $subGallery);
            $imagick = new Imagick($_FILES['croppedImage']['tmp_name']);
            $images = $pbox->getImages();


            if (isset($images[$imageId])) {




                if (isset($pbox->filters[$filterName][0]['param']['width'])) {
                    $width = $pbox->filters[$filterName][0]['param']['width'];
                    $height = $pbox->filters[$filterName][0]['param']['height'];
                } else {
                    $width = $_REQUEST['width'];
                    $height = $_REQUEST['height'];
                }


                $originalImageName = $images[$imageId]['original'];
                $path_info = pathinfo($originalImageName);

                $ext = strtolower($path_info['extension']);
                $ext = explode('?', $ext);
                $ext = array_shift($ext);

                $imagick->setImageFormat($ext);

                $baseDir = Yii::getPathOfAlias('webroot') . '/files/pictureBox/' . $gallery . '/' . $id . '/';
                $baseWebDir = '/files/pictureBox/' . $gallery . '/' . $id . '/';
                $filename = $baseDir . $imageId . '_' . $filterName . '.' . $ext;
                $webFileName = $baseWebDir . $imageId . '_' . $filterName . '.' . $ext;
                //                        print_r($filename);
                //                        $imagick->resizeImage($width,$height,imagick::FILTER_BLACKMAN,0.8);

                $imagick->resizeImage($width, $height, Imagick::FILTER_BLACKMAN, 0.8);
                $imagick->setImageBackgroundColor('white');
                $imagick->extentImage($width, $height, 0, 0);


                $imagick->writeImage($filename);
                $pbox->pictures[$imageId][$filterName] = $webFileName;
                //$pbox->pictures[]=123123;
                $pbox->saveToFile();
            } else throw new Exception('$images[' . $imageId . '] - не существует');
        } else throw new Exception('$_FILES[\'croppedImage\'] - не существует');
    }

    public function actionGetAllIds($gallery)
    {

        $path = Yii::getPathOfAlias('webroot.files.pictureBox');
        $path = $path . DIRECTORY_SEPARATOR . $gallery;
        $directories = array_filter(glob($path . '/*'), 'is_dir');
        $numericDirectories = array();

        foreach ($directories as $dir) {
            $dirName = basename($dir);
            if (ctype_digit($dirName)) {
                $numericDirectories[] = (int) $dirName;
            }
        }

        rsort($numericDirectories);

        echo json_encode($numericDirectories);
    }

    public function actionUpdateAlt($gallery, $id, $imageId, $alt)
    {
        $pbox = new PBox($gallery, $id);
        $pbox->setAlt($imageId,$alt);
        $pbox->saveToFile();
    }
    
    public function actionUpdateTitle($gallery, $id, $imageId, $title)
    {
        $pbox = new PBox($gallery, $id);
        $pbox->setTitle($imageId,$title);
        $pbox->saveToFile();
    }

    public function actionTitleAlrChangeMass($gallery, $id, $imageId, $title)
    {
        $postdata = json_decode(file_get_contents("php://input"));
        $pbox = new PBox($gallery, $id);
        $pbox->setTitle($imageId,$title);
        $pbox->saveToFile();
    }
}
