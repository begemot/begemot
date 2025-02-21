<?php

class ApiController extends Controller
{
    public function filters()
    {
        return array(
            'accessControl',
            'postOnly + delete', // we only allow deletion via POST request
        );
    }

    public function accessRules()
    {
        return array(
            array('allow',  // allow all users to perform these actions
                'actions' => array('index', 'create', 'update', 'delete','EntityLinks','AddEntityLink','DeleteEntityLink'),
                'users' => array('*'),
            ),
            array('deny',  // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionIndex()
    {
        $videos = VideoGalleryVideo::model()->findAll();
        $videoList = [];
    
        foreach ($videos as $video) {
            Yii::import('application.modules.pictureBox.components.PBox');
            $pbox = new PBox('videoGallery', $video->id);
            $image = $pbox->getFirstImage('main'); // Получаем изображение
    
            $videoList[] = [
                'id' => $video->id,
                'title' => $video->title,
                'thumbnail' => $image, // Добавляем ссылку на изображение
            ];
        }
    
        echo CJSON::encode($videoList);
    }
    

    public function actionCreate()
    {
        $video = new VideoGalleryVideo;

        $rawData = file_get_contents('php://input');
        $postData = json_decode($rawData, true);

        if (isset($postData['VideoGalleryVideo'])) {
            $video->attributes = $postData['VideoGalleryVideo'];
            // Automatically set additional fields
            $video->pub_date = time();
            $video->create_time = time();
            $video->update_time = time();
            $video->published = 1; // assuming you want to automatically set it to published
            $video->authorId = 1; // set this to the correct author ID

            if ($video->save()) {
                echo CJSON::encode(array('status' => 'success', 'data' => $video));
            } else {
                echo CJSON::encode(array('status' => 'failure', 'errors' => $video->errors));
            }
        } else {
            echo CJSON::encode(array('status' => 'failure', 'message' => 'Invalid request data'));
        }
    }

    public function actionUpdate($id)
    {
        $video = $this->loadModel($id);

        $rawData = file_get_contents('php://input');
        $postData = json_decode($rawData, true);

        if (isset($postData['VideoGalleryVideo'])) {
            $video->attributes = $postData['VideoGalleryVideo'];
            // Automatically set update_time
            $video->update_time = time();

            if ($video->save()) {
                echo CJSON::encode(array('status' => 'success', 'data' => $video));
            } else {
                echo CJSON::encode(array('status' => 'failure', 'errors' => $video->errors));
            }
        } else {
            echo CJSON::encode(array('status' => 'failure', 'message' => 'Invalid request data'));
        }
    }

    public function actionDelete($id)
    {
        if ($this->loadModel($id)->delete()) {
            echo CJSON::encode(array('status' => 'success'));
        } else {
            echo CJSON::encode(array('status' => 'failure'));
        }
    }

    protected function loadModel($id)
    {
        $model = VideoGalleryVideo::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }
    public function actionEntityLinks()
    {
        $modelName = Yii::app()->request->getParam('modelName');
        $modelId = Yii::app()->request->getParam('modelId');

        $criteria = new CDbCriteria();
        $criteria->compare('entity_model', $modelName);
        $criteria->compare('entity_id', $modelId);

        $entityLinks = VideoEntityLink::model()->findAll($criteria);
        echo CJSON::encode($entityLinks);
    }

    public function actionAddEntityLink()
    {
        $entityLink = new VideoEntityLink;

        $rawData = file_get_contents('php://input');
        $postData = json_decode($rawData, true);
        print_r($postData);
        if (isset($postData['VideoEntityLink'])) {
            $entityLink->attributes = $postData['VideoEntityLink'];
            if ($entityLink->save()) {
                echo CJSON::encode(array('status' => 'success', 'data' => $entityLink));
            } else {
                echo CJSON::encode(array('status' => 'failure', 'errors' => $entityLink->errors));
            }
        } else {
            echo CJSON::encode(array('status' => 'failure', 'message' => 'Invalid request data'));
        }
    }

    public function actionDeleteEntityLink($id)
    {
        if (VideoEntityLink::model()->findByPk($id)->delete()) {
            echo CJSON::encode(array('status' => 'success'));
        } else {
            echo CJSON::encode(array('status' => 'failure'));
        }
    }

}
