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

    public $theme = 'tiles';

    public function init() {
        if (!file_exists(Yii::app()->basePath . '/../files/pictureBox/')) {
            mkDir(Yii::app()->basePath . '/../files/pictureBox/',777);
        }
    }

    public function run() {

        $this->config = array_merge_recursive(PictureBox::getDefaultConfig(),$this->config);

        if (session_id()=='')
            session_start();
        $this->divId=$this->id.'_'.$this->elementId;
        $this->config['divId'] = $this->divId;


        $_SESSION['pictureBox'][$this->divId] = $this->config;


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
                        'filter' => 'CropResize',
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

    public function actionUploadOnlyOneImage($id, $elementId, $image, $temporaryDir = false)
    {

        $id = $id;
        $elementId = $elementId;

        if (isset ($_POST['mode']) && $_POST['mode']=='killEmAll'){
            $this->actionAjaxDeleteAllImages($id,$elementId);
        }

        $catalogItemConfig = require Yii::getPathOfAlias('application') . '/config/picture/' . $id . 'Picture.php';

        $config = array_merge_recursive(PictureBox::getDefaultConfig(), $catalogItemConfig);

        $dir = Yii::getPathOfAlias('webroot') . '/files/pictureBox';

        if (!file_exists($dir))
            mkdir($dir, 0777);

        $dir = Yii::getPathOfAlias('webroot') . '/files/pictureBox/' . $id . '/';

        if (!file_exists($dir))
            mkdir($dir, 0777);
        $dir = Yii::getPathOfAlias('webroot') . '/files/pictureBox/' . $id . '/' . $elementId . '/';

        if (!file_exists($dir))
            mkdir($dir, 0777);



        if(file_exists($dir . "data.php")){
            PictureBox::crPhpArr(require($dir . 'data.php'), $dir . '/dataTest.php');
            $this->deleteAll($id, $elementId);
        }

        $temp = explode('.', $image);
        $imageExt = end($temp);

        $newImageId = 1;
        $newImagePath = $dir . "1." . $imageExt;

        if($image != $newImagePath){
            copy($image, $newImagePath);

        }


        $resultFiltersStack = array();

        foreach ($config['nativeFilters'] as $filterName => $toggle) {
            if ($toggle && isset($config['imageFilters'][$filterName])) {
                $resultFiltersStack[$filterName] = $config['imageFilters'][$filterName];
            }
        }

        $config['imageFilters'] = $resultFiltersStack;

        Yii::import('application.modules.pictureBox.components.FiltersManager');
        Yii::import('application.modules.pictureBox.components.filters.*');
        $filterManager = new FiltersManager($dir . "/" . $newImageId . '.' . $imageExt, $config);
        $filters = $filterManager->getFilteredImages();


        $images = [];

        foreach ($filters as $filterName => $filteredImageFile) {
            $images[$filterName] = $this->addFilteredImage($newImageId, $filterName, '/files/pictureBox/' . $id . '/' . $elementId . '/' . $filteredImageFile, $dir);
            //PictureBox::crPhpArr($filteredImageFile, $dir . '/dataTest' . $filteredImageFile . '.php');

            
            //chmod(Yii::getPathOfAlias('webroot') . '/files/pictureBox/' . $id . '/' . $elementId . '/' . $filteredImageFile, 0777);
        }

        PictureBox::crPhpArr(['images' => ['1' => $images], 'filters' => []], $dir . 'data.php');

        //$this->updateSortData($id, $elementId);
    }

    static function deleteAll($id, $elementId){
        Yii::import('application.modules.pictureBox.components.PBox');
        $PBox = new PBox($id,$elementId);
        $PBox->deleteAll();
        return true;

    }

    private function addFilteredImage($imageId, $filterName, $filteredImageFile, $dir)
    {

        if (!file_exists($dir . '/data.php')) {
            PictureBox::crPhpArr(array(), $dir . '/data.php');
            $data = array();
            $data['images'] = array();
            $data['filters'] = array();
        } else {
            $data = require $dir . '/data.php';
        }

        //PictureBox::crPhpArr($filteredImageFile, $dir . 'dataTest' . $filterName . '.php');

        $data['images'][$imageId][$filterName] = $filteredImageFile;

        //PictureBox::crPhpArr($data, $dir . '/data.php');
        
        return $data['images'][$imageId][$filterName];
    }

    //возвращает новое имя добавленного изображения с
    //с которым его надо сохранить
    private function addImage($dir, $fileName, $fileExt, $id, $elementId)
    {

        $imageId = 1;

        if (!file_exists($dir . '/data.php')) {
            PictureBox::crPhpArr(array(), $dir . '/data.php');
            $data = array();
            $data['images'] = array();
            $data['filters'] = array();
        } else {
            $data = require $dir . '/data.php';
        }



        $originalFile = '/files/pictureBox/' . $id . '/' . $elementId . '/' . $imageId . '.' . $fileExt;

        $data['images'][$imageId] = array(
            'original' => $originalFile
        );

        PictureBox::crPhpArr($data, $dir . '/data.php');

        return ($imageId);
    }

    protected function renderContent() {

        if ($this->theme=='default'){
            $theme = 'pictureBox.components.view.pictureBoxView';
        } else {
            $theme = 'pictureBox.components.view.'.$this->theme;
        }

        $this->render($theme,array('id'=>$this->id,'elementId'=>$this->elementId,'config'=>$this->config));

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