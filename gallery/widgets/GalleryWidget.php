<?php

class GalleryWidget extends CWidget
{

    public $limit = 5;
    public $galleryId = null;
    public $view = 'gallery';

    public function run()
    {
        Yii::import('gallery.models.Gallery');
        echo $this->limit;
        $criteria = [

            'order' => 'id desc',
            'limit' => $this->limit,

        ];

        if ($this->galleryId !== null) {
            $criteria['condition']='id = '.$this->galleryId;
        }

        $dataProvider = new CActiveDataProvider(
            'Gallery',
            array(
                'criteria' =>
                    $criteria

            )
        );
        $dataProvider->pagination = false;



        $this->render($this->view, array('gallery' => $dataProvider->getData(),'limit'=>$this->limit));

    }

}

?>
