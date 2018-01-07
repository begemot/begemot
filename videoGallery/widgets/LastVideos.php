<?php

class LastVideos extends CWidget
{

    public $limit = 2;
    public $galleryId = null;

    public function run()
    {
        Yii::import('videoGallery.models.VideoGalleryVideo');

        $criteria = [

            'order' => 'id desc',
            'limit' => $this->limit,
            'condition' => 'top=1',
        ];

        if ($this->galleryId !== null) {
            $criteria['condition']='gallery_id = '.$this->galleryId;
        }

        $dataProvider = new CActiveDataProvider(
            'VideoGalleryVideo',
            array(
                'criteria' =>
                    $criteria

            )
        );
        $dataProvider->pagination = false;
        $this->render('lastVideo', array('videos' => $dataProvider->getData()));

    }

}

?>
