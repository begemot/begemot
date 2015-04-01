<?php

class PictureBox extends CWidget {

    //Идентификатор множестка
    //например:books

    public $id = null;
    //Идентификатор элемента множества
    //например:1,2,3,4 и т.д. 
    public $elementId = null;
    public $config = array();
    
    public $divId = 'pictureBox';

    public function init() {
        if (!file_exists(Yii::app()->basePath . '/../files/pictureBox/')) {
            mkDir(Yii::app()->basePath . '/../files/pictureBox/',777);
        }
    }

    public function run() {
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
                        'filter' => 'CropResize',
                        'param' => array(
                            'width' => 298,
                            'height' => 198,
                        ),
                    ),
                ),
            )
        );
        $this->config = array_merge_recursive($defaultConfig,$this->config);

        if (session_id()=='')
            session_start();
        
        $_SESSION['pictureBox']=array($this->id.'_'.$this->elementId=>$this->config);
        
        
        $this->renderContent();
    }

    protected function renderContent() {
        $this->render('pictureBox.components.view.pictureBoxView',array('id'=>$this->id,'elementId'=>$this->elementId,'config'=>$this->config));   
        
    }

    static public function crPhpArr($array, $file) {
        
      
        $code = "<?php
  return
 " . var_export($array, true) . ";
?>";
          file_put_contents($file, $code);


    }

}

?>