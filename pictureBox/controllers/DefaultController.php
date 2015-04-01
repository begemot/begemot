<?php

class DefaultController extends Controller
{


    public function actionIndex()
    {

        $this->render('index');
    }

    public function actionTest()
    {
        $id = 'catalogItem';
        $elemId = 100;
        $pictureId = 2;
        $config = require Yii::getPathOfAlias('application') . '/config/catalog/categoryItemPictureSettings.php';

        $this->renderImageAgain($id, $elemId, $pictureId, $config);
    }

    //Функция пересборки изображений 
    public function renderImageAgain($id, $elemId, $pictureId, $config)
    {

        $filterManager = new FiltersManager(Yii::getPathOfAlias('webroot') . '/files/pictureBox/catalogItem/100/2.jpg', $config);
        $filters = $filterManager->getFilteredImages();

    }

    public function actionAjaxFlipImages($id, $elementId, $pictureid1, $pictureid2)
    {


        $dataFilename = Yii::getPathOfAlias('webroot') . '/' . 'files/pictureBox/' . $id . '/' . $elementId . '/data.php';

        $data = require $dataFilename;
        $images = $data['images'];

        $config = $this->getConfigFromSession($id, $elementId);
        $filters = $config['imageFilters'];

        $dir = Yii::getPathOfAlias('webroot');

        $file1 = $dir . $images[$pictureid1]['original'];
        $file2 = $dir . $images[$pictureid2]['original'];

        $this->flipFiles($file1, $file2);

        //Перекидываем title и alt
        $title1 = (isset($images[$pictureid1]['title']) ? $images[$pictureid1]['title'] : '');
        $alt1 = (isset($images[$pictureid1]['alt']) ? $images[$pictureid1]['alt'] : '');

        $images[$pictureid1]['title'] = isset($images[$pictureid2]['title']) ? $images[$pictureid2]['title'] : '';
        $images[$pictureid1]['alt'] = isset($images[$pictureid2]['alt']) ? $images[$pictureid2]['alt'] : '';

        $images[$pictureid2]['title'] = $title1;
        $images[$pictureid2]['alt'] = $alt1;


        foreach ($filters as $filterName => $filterUselessData) {

            if (isset($images[$pictureid1][$filterName])) {

                $ext = end(explode('.', $images[$pictureid1]['original']));
                $file1 = $dir . '/files/pictureBox/' . $id . '/' . $elementId . '/' . $pictureid1 . '_' . $filterName . '.' . $ext;
                $file2 = $dir . '/files/pictureBox/' . $id . '/' . $elementId . '/' . $pictureid2 . '_' . $filterName . '.' . $ext;

                if (isset($images[$pictureid2][$filterName])) {
                    $tmp = $images[$pictureid2][$filterName];
                    $images[$pictureid2][$filterName] = '/files/pictureBox/' . $id . '/' . $elementId . '/' . $pictureid2 . '_' . $filterName . '.' . $ext;
                    $images[$pictureid1][$filterName] = '/files/pictureBox/' . $id . '/' . $elementId . '/' . $pictureid1 . '_' . $filterName . '.' . $ext;
                } else {
                    $images[$pictureid2][$filterName] = '/files/pictureBox/' . $id . '/' . $elementId . '/' . $pictureid2 . '_' . $filterName . '.' . $ext;
                    unset($images[$pictureid1][$filterName]);
                }
            } else if (isset($images[$pictureid2]['original'])) {
                $ext = end(explode('.', $images[$pictureid2]['original']));
                $file1 = $dir . '/files/pictureBox/' . $id . '/' . $elementId . '/' . $pictureid2 . '_' . $filterName . '.' . $ext;
                $file2 = $dir . '/files/pictureBox/' . $id . '/' . $elementId . '/' . $pictureid1 . '_' . $filterName . '.' . $ext;

                $images[$pictureid1][$filterName] = '/files/pictureBox/' . $id . '/' . $elementId . '/' . $pictureid1 . '_' . $filterName . '.' . $ext;
                unset($images[$pictureid2][$filterName]);
            } else {
                continue;
            }

            $this->flipFiles($file1, $file2);
        }
        $data['images'] = $images;
        PictureBox::crPhpArr($data, $dataFilename);

    }

    public function actionUpload()
    {

        $this->layout = 'pictureBox.views.layouts.ajax';

        $id = $_POST['id'];
        $elementId = $_POST['elementId'];

        $config = unserialize($_POST['config']);
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

        if (!empty($_FILES)) {
            $model = new UploadifyFile;

            $model->uploadifyFile = $uploadedFile = CUploadedFile::getInstanceByName('Filedata');

            if ($model->validate()) {

                Yii::import('application.modules.pictureBox.components.picturebox');
                $file = $model->uploadifyFile;
                $temp = explode('.', $file);
                $imageExt = end($temp);

                $newImageId = $this->addImage($dir, $model->uploadifyFile->name, $imageExt);

                move_uploaded_file($model->uploadifyFile->tempName, $dir . "/" . $newImageId . '.' . $imageExt);

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
                }
            }
        }
    }

    public function actionAjaxLayout($id, $elementId, $imageNumber = 1)
    {


        $this->layout = 'pictureBox.views.layouts.ajax';


        $pictureBoxDir = Yii::getPathOfAlias('webroot') . '/files/pictureBox';
        if (!file_exists($pictureBoxDir)) {
            mkdir($pictureBoxDir, 0777);
        }

        $idDir = Yii::getPathOfAlias('webroot') . '/files/pictureBox/' . $id;

        if (!file_exists($idDir)) {
            mkdir($idDir, 0777);
        }

        $elementIdDir = Yii::getPathOfAlias('webroot') . '/files/pictureBox/' . $id . '/' . $elementId;

        if (!file_exists($elementIdDir)) {

            mkdir($elementIdDir, 0777);
        }


        $dataFile = Yii::getPathOfAlias('webroot') . '/files/pictureBox/' . $id . '/' . $elementId . '/data.php';

        if (file_exists($dataFile)) {

            $data = require($dataFile);
        } else {
            PictureBox::crPhpArr(array('images' => array()), $dataFile);
            $data = array('images' => array());
        }

        $config = $this->getConfigFromSession($id, $elementId);

        $this->render('ajaxLayout', array('elementId' => $elementId, 'id' => $id, 'imageNumber' => $imageNumber, 'data' => $data, 'config' => $config));
    }

    //возвращает новое имя добавленного изображения с
    //с которым его надо сохранить
    private function addImage($dir, $fileName, $fileExt)
    {

        $id = $_POST['id'];
        $elementId = $_POST['elementId'];

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
            'original' => $originalFile,
        );

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
                foreach ($images as $imageKey => $image) {
                    $imagesCounter++;
                    if ($imagesCounter == $pictureId) {

                        if (isset($_REQUEST['title']))
                            $image['title'] = $_REQUEST['title'];

                        if (isset($_REQUEST['alt']))
                            $image['alt'] = $_REQUEST['alt'];

                        $data['images'][$imageKey] = $image;
                        PictureBox::crPhpArr($data, $filesList);
                        break;
                    }
                }
            } else {

                return false;
            }
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
                    unset($data['images'][$pictureId][$filterName]);
                    PictureBox::crPhpArr($data, Yii::getPathOfAlias('webroot') . '/files/pictureBox/' . $id . '/' . $elementId . '/data.php');
                }
            }
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

            $imageExt = end(explode('.', $data['images'][$pictureId]['original']));


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
        $favData = $this->getFavData($id, $elementId);

        $data = $this->getPictureBoxData($id, $elementId);
        $favData[$pictureId] = $data['images'][$pictureId];
        $this->putFavData($id, $elementId, $favData);

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

        $favData = $this->getFavData($id, $elementId);
        if (isset($favData[$pictureId]))
            unset($favData[$pictureId]);

        $this->putFavData($id, $elementId, $favData);
    }

    /**
     * Достаем данные из файла избранных изображений
     *
     * @param type $id Идентификатор хранилища
     * @param type $elementId Идентификатор ячейки хранилища
     */
    static function getFavData($id, $elementId)
    {

        $favFilename = Yii::getPathOfAlias('webroot') . '/files/pictureBox/' . $id . '/' . $elementId . '/favData.php';

        if (!file_exists($favFilename)) {
            $favData = array();
            PictureBox::crPhpArr($favData, $favFilename);
        } else {
            $favData = require $favFilename;
        }

        return $favData;
    }

    static function putFavData($id, $elementId, $favData)
    {

        $favFilename = Yii::getPathOfAlias('webroot') . '/files/pictureBox/' . $id . '/' . $elementId . '/favData.php';
        PictureBox::crPhpArr($favData, $favFilename);
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

        $deleteFilesList = Yii::getPathOfAlias('webroot') . '/' . $data['images'][$pictureId]['original'];

        $images = $data['images'][$pictureId];

        foreach ($images as $image) {

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

    private function flipFiles($file1, $file2)
    {
        if (file_exists($file1)) {
            rename($file1, $file1 . '_tmp');
        } else {

            return;
            throw new Exception(__FILE__ . ' функция flipFiles. Отсутствует первый файл для переименования.');
        }

        if (file_exists($file2)) {
            rename($file2, $file1);
        } else {
            throw new Exception(__FILE__ . ' функция flipFiles. Отсутствует второй файл для переименования.');
        }

        if (file_exists($file1 . '_tmp')) {
            rename($file1 . '_tmp', $file2);
        } else {
            throw new Exception(__FILE__ . ' функция flipFiles. Отсутствует второй файл для переименования.');
        }
    }

}