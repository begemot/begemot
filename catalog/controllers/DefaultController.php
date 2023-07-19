<?php

class DefaultController extends Controller {

    public $layout = 'begemot.views.layouts.column2';

    public function actionIndex() {
        $this->render('index');
    }
    public function actionTest() {

        $queueId = 'parser';
        $queue = new CLongTaskQueue($queueId);

        $activeTask = '';


    }
    public function actionRenderImages($action = 'none') {

        Yii::import('begemot.extensions.CLongTaskQueue');

        $queueId = 'catalog';
        $queue = new CLongTaskQueue($queueId);

        $activeTask = '';

        $webroot = Yii::getPathOfAlias('webroot');

        $progress = 0;

        if ($action == 'start') {
            if (!$queue->isQueueExist()) {
                $data = CatItem::model()->findAll();

                $taskArray = array();

                foreach ($data as $dataItem) {

                    $itemDataFile = $webroot . '/files/pictureBox/catalogItem/' . $dataItem->id . '/data.php';
                    $data = require ($itemDataFile);
                    foreach ($data['images'] as $image) {
                        if (isset($image['original'])){
                            $taskArray[] = $image['original'];
                        } else{
                            throw new Exception ($dataItem->id.' - отсутствует индекс original');
                        }
                    }
                }

                $queue->startNewQueue($taskArray);
            }
        }
        
        if ($action == 'continue') {
            if ($queue->isQueueExist()) {
                Yii::import('pictureBox.components.FiltersManager');
                Yii::import('pictureBox.components.filters.*');

                $activeTask = $queue->getNewActiveTask();
                $dir = dirname($activeTask);
                $fullDir = Yii::getPathOfAlias('webroot').$dir;

                $fileName = basename($activeTask);

                $fileNameArray = explode('.',$fileName);

                $imageId = $fileNameArray[0];

                $dataFilePath = $fullDir.'/data.php';


                if (file_exists($dataFilePath)){
                    $data = require($dataFilePath);

                    if (isset($data['images'][$imageId])) {

                        $images = $data['images'][$imageId];

                        foreach ($images as $image){
                            if (!preg_match('#\d+\.(\w+)#',basename($image))){
                                if (file_exists($fullDir.'/'.basename($image))){
                                    unlink($fullDir.'/'.basename($image));
                                }
                            }
                        }
                    }
                }


                $config = require Yii::getPathOfAlias('application') . '/config/catalog/categoryItemPictureSettings.php';

                $config['nativeFilters']['admin'] = true;
                $config['filtersTitles']['admin'] = 'Системный';
                $config['imageFilters']['admin'] = array(
                    0 => array(
                        'filter' => 'CropResize',
                        'param' => array(
                            'width' => 298,
                            'height' => 198,
                        ),
                    ),
                );
               // die($activeTask);
                //$this->renderImageAgain($id, $elemId, $pictureId, $config);

                $filterManager = new FiltersManager($webroot . $activeTask, $config);
                $filters = $filterManager->getFilteredImages();

                $resultImageArray = array();

                $resultImageArray['original']= $activeTask;

                foreach ($filters as $filterId=>$filteredFileName){
                    $resultImageArray[$filterId] = $dir.'/'.$filteredFileName;
                }

                if (is_array($data)){
                    $data['images'][$imageId] = $resultImageArray;
                    Yii::import('application.modules.pictureBox.components.PictureBox');
                    PictureBox::crPhpArr($data, $fullDir . '/data.php');
                }

                $favDataFile = $fullDir.'/favData.php';

                if (file_exists($favDataFile)){
                    $favData = require($favDataFile);
                    if (is_array($favData)) {

                        $favKeys = array_keys($favData);

                        $newFavData = array();

                        foreach ($favKeys as $favKey){
                            $newFavData[$favKey] = $data['images'][$favKey];
                        }
                        PictureBox::crPhpArr($newFavData, $favDataFile);
                    }
                }

                if ($queue->activeTaskCompleted()){
                    $progress = $queue->getProgress();
                } else{
                    $action='complete';
                }
            }
        }

        $this->render('renderImages', array(
            'activeTask' => $activeTask,
            'action' => $action,
            'progress' => $progress,
                )
        );
    }

    //Функция пересборки изображений 
    public function renderImageAgain($id, $elemId, $pictureId, $config) {
        
    }

}