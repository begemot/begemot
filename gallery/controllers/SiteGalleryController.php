<?php

class SiteGalleryController extends Controller
{


    public function actionIndex()
    {

        Yii::import('gallery.GalleryModule');
        $this->layout = GalleryModule::$galleryLayout;
        $this->render('index', array(
            'allGall' => Gallery::model()->published()->findAll(array('order' => '`order`')),

        ));

    }

    public function actionViewGallery($id)
    {
        
        $filedata = Yii::getPathOfAlias('webroot') . '/files/pictureBox/gallery/' . $id . '/data.php';
        if (file_exists($filedata)) {
            $images = require($filedata);
            $images1 = $images['images'];
        }

        $gallery = Gallery::model()->findByPk($id);

        $this->layout = GalleryModule::$galleryLayout;
        $this->render('viewGallery', array(
                'images' => $images1,
                'gallery' => $gallery
            )

        );

    }
}