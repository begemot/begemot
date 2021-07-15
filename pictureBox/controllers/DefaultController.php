<?php

class DefaultController extends Controller
{


    public function actionIndex()
    {

        $this->render('index');
    }

    public function actionTest()
    {
//        Yii::import('begemot.extensions.bootstrap.widgets.TbMenu');
        $this->layout = 'begemot.views.layouts.column1';

//        $id = 'catalogItem';
//        $elemId = 100;
//        $pictureId = 2;
//        $config = require Yii::getPathOfAlias('application') . '/config/catalog/categoryItemPictureSettings.php';
//
//        $this->renderImageAgain($id, $elemId, $pictureId, $config);
        $this->render('test');
    }

    //Функция пересборки изображений
    public function renderImageAgain($id, $elemId, $pictureId, $config)
    {
        $PBox = new PBox($id, $elemId);

        $originalImage = $PBox->getImage($pictureId, 'original');
        $path_info = pathinfo($originalImage);
        $ext = $path_info['extension'];

        $filterManager = new FiltersManager(Yii::getPathOfAlias('webroot') . '/files/pictureBox/' . $id . '/' . $elemId . '/' . $pictureId . '.' . $ext, $config);
        $filters = $filterManager->getFilteredImages();

    }

    //экшн пересборки изображения
    public function actionRenderImageAgain($id, $elemId, $pictureId, $config)
    {

        $this->renderImageAgain($id, $elemId, $pictureId, unserialize($config));
    }

    public function actionAjaxFlipImages($id, $elementId, $pictureid1, $pictureid2)
    {

        $PBox = new PBox($id, $elementId);
        $PBox->swapImages($pictureid1, $pictureid2);

    }

    public function actionNewSortOrder($galleryId, $id)
    {
        $PBox = new PBox($galleryId, $id);
        $PBox->sortArray = $_REQUEST['sort'];
        $PBox->saveToFile();
        return true;
    }

    public function actionUpload()
    {

        $this->layout = 'pictureBox.views.layouts.ajax';

        $id = $_POST['id'];
        $elementId = $_POST['elementId'];

        if (isset ($_POST['mode']) && $_POST['mode'] == 'killEmAll') {
            $this->actionAjaxDeleteAllImages($id, $elementId);
        }

        $config = unserialize($_POST['config']);
        // file_put_contents(Yii::getPathOfAlias('webroot') . '/log.log3', var_export($config, true));

        $dir = Yii::getPathOfAlias('webroot') . '/files/pictureBox';

        if (!file_exists($dir))
            mkdir($dir, 0777);

        $dir = Yii::getPathOfAlias('webroot') . '/files/pictureBox/' . $id . '/';

        if (!file_exists($dir))
            mkdir($dir, 0777);
        $dir = Yii::getPathOfAlias('webroot') . '/files/pictureBox/' . $id . '/' . $elementId . '/';

        if (!file_exists($dir))
            mkdir($dir, 0777);

        if (!empty($_FILES)) {
            $model = new UploadifyFile;

            $model->uploadifyFile = $uploadedFile = CUploadedFile::getInstanceByName('Filedata');

            if ($model->validate()) {

                Yii::import('application.modules.pictureBox.components.picturebox');

                $file = $model->uploadifyFile;
                $temp = explode('.', $file);
                $imageExt = end($temp);

                $newImageId = $this->addImage($dir, $model->uploadifyFile->name, $imageExt, $id, $elementId);

                move_uploaded_file($model->uploadifyFile->tempName, $dir . "/" . $newImageId . '.' . $imageExt);
                //chmod($dir . "/" . $newImageId . '.' . $imageExt, 0777);


                $resultFiltersStack = array();

                foreach ($config['nativeFilters'] as $filterName => $toggle) {
                    if ($toggle && isset($config['imageFilters'][$filterName])) {
                        $resultFiltersStack[$filterName] = $config['imageFilters'][$filterName];
                    }
                }

                $config['imageFilters'] = $resultFiltersStack;

                $filterManager = new FiltersManager($dir . "/" . $newImageId . '.' . $imageExt, $config);
                $filters = $filterManager->getFilteredImages();

                foreach ($filters as $filterName => $filteredImageFile) {
                    $this->addFilteredImage($newImageId, $filterName, '/files/pictureBox/' . $id . '/' . $elementId . '/' . $filteredImageFile, $dir);
                    //chmod(Yii::getPathOfAlias('webroot') . '/files/pictureBox/' . $id . '/' . $elementId . '/' . $filteredImageFile, 0777);
                }

                $this->updateSortData($id, $elementId);
            }
        }
    }

    public function actionUploadArray()
    {
        $images = json_decode($_POST['images']);
        $return = '';

        $hashesMd5 = array();
        $hashesSha1 = array();


        if ($images) {

            $id = 'catalogItem';
            $elementId = $_POST['id'];;

            $catalogItemConfig = require Yii::getPathOfAlias('application') . '/config/catalog/categoryItemPictureSettings.php';

            $config = array_merge_recursive(PictureBox::getDefaultConfig(), $catalogItemConfig);

            file_put_contents(Yii::getPathOfAlias('webroot') . '/log.log3', var_export($config, true));

            $dir = Yii::getPathOfAlias('webroot') . '/files/pictureBox';

            if (!file_exists($dir))
                mkdir($dir, 0777);

            $dir = Yii::getPathOfAlias('webroot') . '/files/pictureBox/' . $id . '/';

            if (!file_exists($dir))
                mkdir($dir, 0777);
            $dir = Yii::getPathOfAlias('webroot') . '/files/pictureBox/' . $id . '/' . $elementId . '/';

            if (!file_exists($dir))
                mkdir($dir, 0777);

            if (file_exists($dir . '/data.php')) {
                $data = require $dir . '/data.php';

                foreach ($data['images'] as $item) {

                    if (isset($item['md5'])) {
                        $hashesMd5[] = $item['md5'];
                    }

                    if (isset($item['sha1'])) {

                        $hashesSha1[] = $item['sha1'];

                    }

                }
            }


            foreach ($images as $image) {

                $hashMd5 = hash_file('md5', $image);
                $hashSha1 = hash_file('sha1', $image);
                if (!in_array($hashMd5, $hashesMd5) & !in_array($hashSha1, $hashesSha1)) {


                    Yii::import('application.modules.pictureBox.components.picturebox');

                    $file = $image;
                    $temp = explode('.', $file);
                    $imageExt = end($temp);

                    $newImageId = $this->addImage($dir, $image, $imageExt, $id, $elementId, $hashMd5, $hashSha1);

                    copy($image, $dir . "/" . $newImageId . '.' . $imageExt);
                    //chmod($dir . "/" . $newImageId . '.' . $imageExt, 0777);


                    $resultFiltersStack = array();

                    foreach ($config['nativeFilters'] as $filterName => $toggle) {
                        if ($toggle && isset($config['imageFilters'][$filterName])) {
                            $resultFiltersStack[$filterName] = $config['imageFilters'][$filterName];
                        }
                    }

                    $config['imageFilters'] = $resultFiltersStack;

                    $filterManager = new FiltersManager($dir . "/" . $newImageId . '.' . $imageExt, $config);
                    $filters = $filterManager->getFilteredImages();

                    foreach ($filters as $filterName => $filteredImageFile) {
                        $this->addFilteredImage($newImageId, $filterName, '/files/pictureBox/' . $id . '/' . $elementId . '/' . $filteredImageFile, $dir);
                        //chmod(Yii::getPathOfAlias('webroot') . '/files/pictureBox/' . $id . '/' . $elementId . '/' . $filteredImageFile, 0777);
                    }

                    $hashesMd5[] = $hashMd5;
                    $hashesSha1[] = $hashSha1;
                }

            }

        } else {
            throw new Exception("Нету изображений", 1);

        }
        $this->updateSortData($id, $elementId);
        echo $return;
        return $return;
    }

    public function actionAjaxLayout($id, $elementId, $imageNumber = 1)
    {


        $this->layout = 'pictureBox.views.layouts.ajax';

        $PBox = new PBox($id, $elementId);

        $data['images'] = $PBox->getSortedImageList('admin');

        $config = $this->getConfigFromSession($id, $elementId);

        $viewFile = 'ajaxLayout';

        if (isset($_REQUEST['theme'])) {
            $viewFile = $_REQUEST['theme'];
        }

        $this->render($viewFile, array('elementId' => $elementId, 'id' => $id, 'imageNumber' => $imageNumber, 'data' => $data, 'config' => $config));
    }

    //возвращает новое имя добавленного изображения с
    //с которым его надо сохранить
    private function addImage($dir, $fileName, $fileExt, $id, $elementId, $md5 = '', $sha1 = '')
    {

        $imageId = $this->getNewImageId($dir);

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

        if ($md5 != "") {
            $data['images'][$imageId]['md5'] = $md5;
        }

        if ($sha1 != "") {
            $data['images'][$imageId]['sha1'] = $sha1;
        }

        PictureBox::crPhpArr($data, $dir . '/data.php');

        return ($imageId);
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

        $data['images'][$imageId][$filterName] = $filteredImageFile;

        PictureBox::crPhpArr($data, $dir . '/data.php');
    }

    private function getNewImageId($dir)
    {

        if (!file_exists($dir . '/lastImageId.php')) {
            PictureBox::crPhpArr(1, $dir . 'lastImageId.php');
            return 0;
        } else {
            $newImageId = require $dir . 'lastImageId.php';
            PictureBox::crPhpArr($newImageId + 1, $dir . 'lastImageId.php');
            return $newImageId;
        }
    }

    public function actionAjaxGetFavArray($elementId, $id, $pictureId = null)
    {
        $this->layout = 'pictureBox.views.layouts.ajax';
        if (Yii::app()->request->isAjaxRequest) {
            $pBox = new PBox($id,$elementId);
//            $favFile = Yii::getPathOfAlias('webroot') . '/files/pictureBox/' . $id . '/' . $elementId . '/favData.php';
//            if (file_exists($favFile)) {
//
//                $favArray = require($favFile);
//
//                if ($favArray == null) $favArray = [];
//
//            } else {
//                $favArray = [];
//            }

            $favArray = $pBox->getFavData();
            echo json_encode($favArray);
        }
    }

    public function actionAjaxGetAltArray($elementId, $id, $pictureId = null)
    {
        $this->layout = 'pictureBox.views.layouts.ajax';
        if (Yii::app()->request->isAjaxRequest) {

            $dataFile = Yii::getPathOfAlias('webroot') . '/files/pictureBox/' . $id . '/' . $elementId . '/data.php';
            $dataArray = require($dataFile);


            $resultArray = [];
            if (isset($dataArray['images'])) {
                $dataArray = $dataArray['images'];


                foreach ($dataArray as $imageKey => $imageArray) {
                    $altArray = [];
                    if (isset($imageArray['title'])) {
                        $altArray['title'] = $imageArray['title'];
                    }

                    if (isset($imageArray['alt'])) {
                        $altArray['alt'] = $imageArray['alt'];
                    }
                    $resultArray[$imageKey] = $altArray;
                }

            }
            echo json_encode($resultArray);
        }
    }

    public function actionAjaxSetTitle()
    {
        $this->layout = 'pictureBox.views.layouts.ajax';
        if (Yii::app()->request->isAjaxRequest) {

            $id = $_REQUEST['id'];
            $elementId = $_REQUEST['elementId'];
            $pictureId = $_REQUEST['pictureId'];
            $filesList = Yii::getPathOfAlias('webroot') . '/files/pictureBox/' . $id . '/' . $elementId . '/data.php';


            if (file_exists($filesList)) {

                $data = require($filesList);
                $images = $data['images'];

                $imagesCounter = 0;


                if (isset($_REQUEST['title']))
                    $data['images'][$pictureId]['title'] = $_REQUEST['title'];

                if (isset($_REQUEST['alt']))
                    $data['images'][$pictureId]['alt'] = $_REQUEST['alt'];


                PictureBox::crPhpArr($data, $filesList);


            } else {

                return false;
            }
        }
    }

    public function actionAjaxDeleteAllImages($id, $elementId)
    {
        if (Yii::app()->request->isAjaxRequest) {
            $PBox = new PBox($id, $elementId);
            $PBox->deleteAll();
            return true;
        }
    }

    public function actionAjaxDeleteImage($id, $elementId, $pictureId)
    {

        $this->layout = 'pictureBox.views.layouts.ajax';
        if (Yii::app()->request->isAjaxRequest) {
            $dataFile = Yii::getPathOfAlias('webroot') . '/files/pictureBox/' . $id . '/' . $elementId . '/data.php';
            $data = require($dataFile);

            $this->actionAjaxDelFav($id, $elementId, $pictureId);

            $this->deleteImageFiles($id, $elementId, $pictureId, $data);

            if (isset($data['images'][$pictureId])) {
                unset($data['images'][$pictureId]);
                PictureBox::crPhpArr($data, $dataFile);
            }

            $this->updateSortData($id, $elementId);
        }
    }

    public function actionAjaxShowHideImage($id, $elementId, $pictureId)
    {

        $this->layout = 'pictureBox.views.layouts.ajax';
        if (Yii::app()->request->isAjaxRequest) {
            $pbox = new PBox($id, $elementId);
            echo $pbox->changeImageShown($pictureId);
        }
    }

    public function actionAjaxDeleteFilteredImage($id, $elementId, $pictureId, $filterName)
    {
        if (Yii::app()->request->isAjaxRequest) {
            $data = require(Yii::getPathOfAlias('webroot') . '/files/pictureBox/' . $id . '/' . $elementId . '/data.php');


            if (isset($data['images'][$pictureId][$filterName])) {
                $fileName = $data['images'][$pictureId][$filterName];
                $fullFilePath = Yii::getPathOfAlias('webroot') . '/' . $fileName;
                //print_t($fullFilePath);
                //die($fullFilePath);
                if (file_exists($fullFilePath)) {
                    unlink($fullFilePath);
                }
                unset($data['images'][$pictureId][$filterName]);
                PictureBox::crPhpArr($data, Yii::getPathOfAlias('webroot') . '/files/pictureBox/' . $id . '/' . $elementId . '/data.php');

            }
            $this->updateSortData($id, $elementId);
        }
    }

    public function actionAjaxFilterOriginalImage($id, $elementId, $pictureId, $filterName)
    {
        if (Yii::app()->request->isAjaxRequest) {

            $data = require(Yii::getPathOfAlias('webroot') . '/files/pictureBox/' . $id . '/' . $elementId . '/data.php');
            $dir = Yii::getPathOfAlias('webroot') . '/files/pictureBox/' . $id . '/' . $elementId;
            $config = $this->getConfigFromSession($id, $elementId);

            $temp = explode('.', $data['images'][$pictureId]['original']);
            $imageExt = end($temp);

            echo $originalImagePath = $dir . "/" . $pictureId . '.' . $imageExt;

            $filter = new ExpandFilter($originalImagePath, $originalImagePath, []);
            $filter->make();
        }
    }


    /**
     *
     *  Аякс-команда, которая создает одно изображение на основе одного фильтра.
     *
     * @param type $id Идентификатор хранилища
     * @param type $elementId Идентификатор ячейки хранилища
     * @param type $pictureId Идентификатор изображения
     * @param type $filterName Имя фильтра. Изначально устанавливается в конфиге
     */
    public function actionAjaxMakeFilteredImage($id, $elementId, $pictureId, $filterName, $x = null, $y = null, $width = null, $height = null)
    {

        if (Yii::app()->request->isAjaxRequest) {

            $data = require(Yii::getPathOfAlias('webroot') . '/files/pictureBox/' . $id . '/' . $elementId . '/data.php');
            $dir = Yii::getPathOfAlias('webroot') . '/files/pictureBox/' . $id . '/' . $elementId;
            $config = $this->getConfigFromSession($id, $elementId);

            $temp = explode('.', $data['images'][$pictureId]['original']);
            $imageExt = end($temp);


            if (isset($config['imageFilters'][$filterName])) {

                $originalImagePath = $dir . "/" . $pictureId . '.' . $imageExt;
                $tmpOriginalImagePath = $originalImagePath . '.tmp';

                if ($x !== null && $width !== null) {
                    $originalPicture = new Imagick($originalImagePath);
                    copy($originalImagePath, $tmpOriginalImagePath);

                    $originalPicture->cropImage($width, $height, $x, $y);
                    $originalPicture->writeImage($originalImagePath);
                    //$originalPicture->writeImage($originalImagePath.'111');

                }

                $filter['imageFilters'][$filterName] = $config['imageFilters'][$filterName];
                $filterManager = new FiltersManager($originalImagePath, $filter);

                $filters = $filterManager->getFilteredImages();


                foreach ($filters as $filterName => $filteredImageFile) {
                    $this->addFilteredImage($pictureId, $filterName, '/files/pictureBox/' . $id . '/' . $elementId . '/' . $filteredImageFile, $dir);
                }

                if ($x !== null && $width !== null) {
                    copy($tmpOriginalImagePath, $originalImagePath);
                    unlink($tmpOriginalImagePath);
                }


            }
        }
    }

    /**
     *
     * Аякс команда добавления конкретного изображения в избранное
     *
     * @param type $id Идентификатор хранилища
     * @param type $elementId Идентификатор ячейки хранилища
     * @param type $pictureId Идентификатор изображения
     */
    public function actionAjaxAddFav($id, $elementId, $pictureId)
    {
        $pBox = new PBox($id, $elementId);
        $pBox->addPictureToFav($pictureId);

//        $favData = $this->getFavData($id, $elementId);
//
//        $data = $this->getPictureBoxData($id, $elementId);
//        $favData[$pictureId] = $data['images'][$pictureId];
//        $this->putFavData($id, $elementId, $favData);

    }

    /**
     *
     * Аякс команда удаления конкретного изображения в избранное
     *
     * @param type $id Идентификатор хранилища
     * @param type $elementId Идентификатор ячейки хранилища
     * @param type $pictureId Идентификатор конкретного изображения
     */
    public function actionAjaxDelFav($id, $elementId, $pictureId)
    {
        $pBox = new PBox($id, $elementId);

        if (isset($pBox->favPictures[$pictureId]))
            unset($pBox->favPictures[$pictureId]);

        $pBox->saveToFile();
    }

    /**
     * Достаем данные из файла избранных изображений
     *
     * @param type $id Идентификатор хранилища
     * @param type $elementId Идентификатор ячейки хранилища
     */
    static function getFavData($id, $elementId)
    {

        $pBox = new PBox($id, $elementId);

        return $pBox->getFavData();
    }



    /**
     * Достаем данные из ячейки хранилища
     *
     * @param type $id Идентификатор хранилища
     * @param type $elementId Идентификатор ячейки хранилища
     */
    private function getPictureBoxData($id, $elementId)
    {

        if (!file_exists(Yii::getPathOfAlias('webroot') . '/files/pictureBox'))
            mkdir(Yii::getPathOfAlias('webroot') . '/files/pictureBox');

        if (!file_exists(Yii::getPathOfAlias('webroot') . '/files/pictureBox/' . $id))
            mkdir(Yii::getPathOfAlias('webroot') . '/files/pictureBox/' . $id);

        if (!file_exists(Yii::getPathOfAlias('webroot') . '/files/pictureBox'))
            mkdir(Yii::getPathOfAlias('webroot') . '/files/pictureBox/' . $id . '/' . $elementId);


        $dataFilename = Yii::getPathOfAlias('webroot') . '/files/pictureBox/' . $id . '/' . $elementId . '/data.php';

        if (!file_exists($dataFilename)) {

            $data = array();
            $data['images'] = array();
            $data['filters'] = array();

            PictureBox::crPhpArr($data, $dataFilename);
        } else {
            $data = require $dataFilename;
        }

        return $data;
    }

    /**
     *
     * Физическое удаление основного файла и всех его фильтрованных копий.
     *
     * @param type $id Идентификатор хранилища
     * @param type $elementId Идентификатор ячейки хранилища
     * @param type $pictureId Идентификатор изображения
     * @param type $data Массив всех изображений
     */
    function deleteImageFiles($id, $elementId, $pictureId, $data)
    {
        if (isset($data['images'][$pictureId]['original'])){

            $deleteFilesList = Yii::getPathOfAlias('webroot') . '/' . $data['images'][$pictureId]['original'];
            if (file_exists($deleteFilesList))
                unlink($deleteFilesList);
        }

        $images = $data['images'][$pictureId];

        foreach ($images as $key => $image) {
            if ($key == 'params') continue;
            $fileFullName = Yii::getPathOfAlias('webroot') . '/' . $image;

            if (file_exists($fileFullName) && !is_dir($fileFullName)) {
                unlink($fileFullName);
            }
        }
    }

    /**
     * Забираем из сессии данные о выставленных фильтрах. Фильтры выставляются
     * при вызове виджета, а все аякс-команды находятся в контроллере. Т.к. фильтров
     * может быть произвольное количество, то передавать такие сложные данные get
     * или post запросом сложновато. Поэтому данные передаются через сессии.
     *
     * @return type Конфиг фильтров, который передается виджету
     */
    public function getConfigFromSession($id, $elementId)
    {


        session_start();

        if (isset($_SESSION['pictureBox'][$id . '_' . $elementId])) {
            return $_SESSION['pictureBox'][$id . '_' . $elementId];
        } else {
            return 'Config in session not exist!';
        }
    }

    static function updateSortData($id, $elementId)
    {
        $pBox = new PBox($id,$elementId);
        $pBox->updateSortData();
        //TODO::
    }

    private function flipFiles($file1, $file2)
    {
        echo 'Меняем файлы:<br>';
        echo $file1 . '<br>';
        echo $file2 . '<br>';

        if (!file_exists($file1) && !file_exists($file2)) {
            echo 'Оба файла не существует. Нечего менять местами' . '<br>';
            return;
        }

        if (file_exists($file1)) {
            echo 'Первый файл существует. Переименовываем его во временный ' . $file1 . '_tmp' . '<br>';
            rename($file1, $file1 . '_tmp');

            if (file_exists($file2)) {
                rename($file2, $file1);
                rename($file1 . '_tmp', $file2);
            } else {
                rename($file1 . '_tmp', $file2);
            }

        } else {
            echo 'Первый файл НЕ существует.' . '<br>';
            if (file_exists($file2)) {
                echo 'Второй файл существует, поэтому просто.' . '<br>';
                rename($file2, $file1);
                return;
            }

        }


    }

}