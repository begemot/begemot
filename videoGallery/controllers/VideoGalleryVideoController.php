<?php

class VideoGalleryVideoController extends GxController {
    	public $layout='begemot.views.layouts.column2';
public function filters() {
	return array(
			'accessControl', 
			);
}

public function accessRules() {
	return array(
			array('allow', 
				'actions'=>array('index', 'view'),
                'expression'=>'Yii::app()->user->canDo("VideoGalleryEditor")'
				),
			array('allow', 
				'actions'=>array('minicreate', 'create', 'update', 'admin', 'delete'),
                'expression'=>'Yii::app()->user->canDo("VideoGalleryEditor")'
				),
			array('deny', 
				'users'=>array('*'),
				),
			);
}

	public function actionView($id) {
		$this->render('view', array(
			'model' => $this->loadModel($id, 'VideoGalleryVideo'),
		));
	}

	public function actionCreate() {
		$model = new VideoGalleryVideo;

		$this->performAjaxValidation($model, 'video-gallery-video-form');

		if (isset($_POST['VideoGalleryVideo'])) {
			$model->setAttributes($_POST['VideoGalleryVideo']);

			if ($model->save()) {
				if (Yii::app()->getRequest()->getIsAjaxRequest())
					Yii::app()->end();
				else
					$this->redirect(array('view', 'id' => $model->id));
			}
		}

		$this->render('create', array( 'model' => $model));
	}

	public function actionUpdate($id) {
		$model = $this->loadModel($id, 'VideoGalleryVideo');

		$this->performAjaxValidation($model, 'video-gallery-video-form');

		if (isset($_POST['VideoGalleryVideo'])) {
			$model->setAttributes($_POST['VideoGalleryVideo']);

			$model->save();

		}

		$this->render('update', array(
				'model' => $model,
				));
	}

	public function actionDelete($id) {
		if (Yii::app()->getRequest()->getIsPostRequest()) {
			$this->loadModel($id, 'VideoGalleryVideo')->delete();

			if (!Yii::app()->getRequest()->getIsAjaxRequest())
				$this->redirect(array('admin'));
		} else
			throw new CHttpException(400, Yii::t('app', 'Your request is invalid.'));
	}

	public function actionIndex() {
		$dataProvider = new CActiveDataProvider('VideoGalleryVideo');
		$this->render('index', array(
			'dataProvider' => $dataProvider,
		));
	}

	public function actionAdmin() {
		$model = new VideoGalleryVideo('search');
		$model->unsetAttributes();

		if (isset($_GET['VideoGalleryVideo']))
			$model->setAttributes($_GET['VideoGalleryVideo']);

		$this->render('admin', array(
			'model' => $model,
		));
	}

}