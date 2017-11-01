<?php

class LastVideos extends CWidget {
    
    public $limit = 2;
    
    public function run(){
        Yii::import('videoGallery.models.VideoGalleryVideo');
        $dataProvider = new CActiveDataProvider(
            'VideoGalleryVideo',
            array(
                'criteria' => array(
                    'order' => 'id desc',
                    'limit'=>$this->limit,
                    'condition'=>'top=1'
                )
            )
        );
        $dataProvider->pagination=false;
        $this->render('lastVideo',array('videos'=>$dataProvider->getData()));
        
    }
    
}

?>
