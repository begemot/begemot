<?php
Yii::import('pictureBox.components.PBox');

class ApiController extends Controller
{

    public function actionGetData($galleryId, $id, $subGallery='default')
    {

        $pBox = new PBox($galleryId, $id,$subGallery);

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

            if (!isset ($images[$key]['params']['show'])) {
                $images[$key]['params']['show'] = true;
            }

            if (isset ($favData[$key])) {
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

            foreach($images[$key] as $imageKey=>$image){
                if ($imageKey!='id' &&
                    $imageKey!='order' &&
                    $imageKey!='show'&&
                    $imageKey!='params'&&
                    $imageKey!='title'&&
                    $imageKey!='alt'
                ){
                    $images[$key][$imageKey] = $image.'?'.rand(1,100000);
                }
            }

        }

        $allData = [
            'images' => $images,
            'favData' => $pBox->getFavData(),
            'lastImageId' => $pBox->getLastImageId(),
            'sortedData' => $pBox->getSortArray(),
            'config' => $config,
            'subGalleryList'=>$subGalleryList
        ];


        echo json_encode($allData);

    }

    public function actionObectSave($galleryId, $id,$subGallery='default')
    {

        $pBox = new PBox($galleryId, $id,$subGallery);

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
                unset ($favData[$image['id']]['fav']);
            }

            unset($images[$image['id']]['id']);


        }

        print_r($postdata['deleted']);
        if (isset($postdata['deleted'])) {
            foreach ($postdata['deleted'] as $item) {
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
        $subGallery='default';
        if (isset($_REQUEST['subGallery']))
            $subGallery=$_REQUEST['subGallery'];

        $this->layout = 'pictureBox.views.layouts.ajax';

        $id = $_POST['galleryId'];
        $elementId = $_POST['id'];
        $lastImageId = $_POST['lastId'];
        $pbox = new PBox($id, $elementId,$subGallery);

        $config = $pbox->filters;


        if (!empty($_FILES)) {

            $addedImages = [];
            foreach ($_FILES as $fileArray) {

                $addedImages[] = $pbox->upload($fileArray, $lastImageId);

            }

            echo json_encode($addedImages);
        }
    }


    public function actionGetLastItemId($galleryId, $id)
    {
        $pbox = new PBox($galleryId, $id);

        $lastId = $pbox->getLastImageId();

        echo json_encode($lastId);

    }


    public function actionSavePreviewImage($gallery, $id, $imageId, $filterName,$subGallery='default')
    {

        if (isset($_FILES['croppedImage'])) {

            $pbox = new PBox($gallery, $id,$subGallery);
            $imagick = new Imagick($_FILES['croppedImage']['tmp_name']);
            $images = $pbox->getImages();
            if (isset($images[$imageId])) {

                $filters = $pbox->filters['imageFilters'];

                if (isset($filters[$filterName])){

                    if (isset($filters[$filterName][0]['param']['width'])){

                        $width = $filters[$filterName][0]['param']['width'];
                        $height = $filters[$filterName][0]['param']['height'];

                        $originalImageName = $images[$imageId]['original'];
                        $path_info = pathinfo($originalImageName);

                        $ext = strtolower($path_info['extension']);
                        $ext = explode('?',$ext);
                        $ext = array_shift($ext);

                        $imagick->setImageFormat($ext);

                        $baseDir = Yii::getPathOfAlias('webroot');
                        $filename = $baseDir.$images[$imageId][$filterName];
                        $filename =              explode('?',$filename);
                        $filename = array_shift($filename);
                        print_r($filename);
                        $imagick->resizeImage($width,$height,imagick::FILTER_BOX ,1);

                        $imagick->writeImage($filename);
                    }
                }



            }
        }
    }
}