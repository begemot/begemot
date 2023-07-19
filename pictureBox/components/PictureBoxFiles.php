<?php

Yii::import('pictureBox.components.PBox');
class PictureBoxFiles extends CWidget {

    //Идентификатор множестка
    //например:books

    public $id = null;
    //Идентификатор элемента множества
    //например:1,2,3,4 и т.д. 
    public $elementId = null;
    public $config = array();

    public $divId = '';

    public $theme = 'tiles';

    public function init() {
        if (!file_exists(Yii::app()->basePath . '/../files/pictureBox/')) {
            mkDir(Yii::app()->basePath . '/../files/pictureBox/',777);
        }
    }

    public function run() {

        $this->config = array_merge_recursive(self::getDefaultConfig(),$this->config);

        $pBox = new PBox($this->id,$this->elementId);
        $pBox->setImagesRenderRules($this->config);

        $this->renderContent();
    }

    public static function getDefaultConfig(){
        $defaultConfig = array(

            'nativeFilters'=>array(
                'admin' =>true,
            ),
            'filtersTitles'=>array(
                'admin' =>'Системный',

            ),
            'imageFilters' => array(
                'admin' => array(
                    0 => array(
                        'filter' => 'CropResizeUpdate',
                        'param' => array(
                            'width' => 298,
                            'height' => 198,
                        ),
                    ),
                ),
            )
        );

        return $defaultConfig;
    }




    protected function renderContent() {
          $theme  =   'pictureBox.components.view.angularTiles';
//        if ($this->theme=='default'){
//            $theme = 'pictureBox.components.view.pictureBoxView';
//        } else {
//            $theme = 'pictureBox.components.view.'.$this->theme;
//        }

        $this->render($theme,array('id'=>$this->id,'elementId'=>$this->elementId,'config'=>$this->config));

    }
}
?>