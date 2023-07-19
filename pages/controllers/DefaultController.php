 <?php

class DefaultController extends Controller
{
	public $layout='begemot.views.layouts.column2';
        
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}
        
	public function accessRules()
	{
		return array(

			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('index','newFile','delete','update','tidyItemText'),
                'expression'=>'Yii::app()->user->canDo("HTML")'
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}       
        
	public function actionIndex()
	{
            $dataPath = $this->getDataDir();

            $fileHelper = new CFileHelper();
            $files = $fileHelper->findFiles('./files/pages',array('exclude'=>array('data','filesId.php','pagesList.php')));
            $filesArray=array();
            foreach($files as $id=>$filePath){
                $fileItem=array();
                $fileItem['id']=$id;
                $fileItem['filePath']=$filePath;
                $filesArray[] = $fileItem;
            }
           
            
            $gridDataProvider = new CArrayDataProvider($filesArray);
            $gridDataProvider->pagination = array('pageSize'=>20);
            
            $this->render('index',array('data'=>$gridDataProvider));
	}
        
        public function actionNewFile()
        {
            $model=new NewFile();

            if(isset($_POST['ajax']) && $_POST['ajax']==='new-file-test-form')
            {
                echo CActiveForm::validate($model);
                Yii::app()->end();
            }
            

            if(isset($_POST['NewFile']))
            {                
                $model->attributes=$_POST['NewFile'];
                if($model->validate())
                {

                    $dataPath = $this->getDataDir();

                    $webroot = Yii::getPathOfAlias('webroot');
                    $file = fopen($dataPath.'/'.$model->filename.'.php', 'w');
                    fclose($file);

                    
                    $this->redirect('/pages');
                    return;
                }
            }
            $this->render('newFile',array('model'=>$model));
        }   
        
	public function actionDelete($file)
	{
            $file = str_replace("*","/",$file);
      
            unlink($file.'.php');
	}        
        
	public function actionUpdate($file)
	{

            $model=new updateForm($file.'.php');


            if(isset($_POST['ajax']) && $_POST['ajax']==='update-form-update-form')
            {
                echo CActiveForm::validate($model);
                Yii::app()->end();
            }
            

            if(isset($_POST['updateForm'])){

                $model->attributes=$_POST['updateForm'];
                    if($model->validate())
                    {
                        $model->saveFile();
                        
                    }
            }
            $this->render('update',array('model'=>$model,'file'=>$file));
	}

    private function getDataDir(){

        $dataPath = Yii::getPathOfAlias('webroot.files.pages');


        if (!file_exists($dataPath)){
            mkdir($dataPath,0777);
        }

        return $dataPath;
    }

    public function actionTidyItemText($file)
    {

        $filesIndexPath =  Yii::getPathOfAlias('webroot').'/files/pages/pagesList.php';
        if (file_exists($filesIndexPath)){
            $pagesFilesList = require $filesIndexPath;
        } else {
            $pagesFilesList = array();
            crPhpArr($pagesFilesList,$filesIndexPath);
        }

        if (isset($pagesFilesList[$file])){
            $fileId = $pagesFilesList[$file];
        } else {
            $idHtmlFile = Yii::getPathOfAlias('webroot').'/files/pages/filesId.php';
            $fileId = getFileId($idHtmlFile);
            $pagesFilesList[$file] = $fileId;

            crPhpArr($pagesFilesList,$filesIndexPath);

        }

        $htmlFile = Yii::getPathOfAlias('webroot').'/'.str_replace('*', '/', $file).'.php';

        $text = file_get_contents($htmlFile);

        Yii::import('application.modules.pictureBox.components.PBox');

        $pbox = new PBox('htmlPage', $fileId);

        $images = $pbox->pictures;
//        print_r($pbox->pictures);
        //return;


        Yii::import('application.modules.begemot.components.tidy.TidyBuilder');

       // $this->module->tidyleadImage != 0 ? $leadImage = 1 : $leadImage = 0;

        $tidy = new TidyBuilder ($text, $this->module->tidyConfig, $images);

        $resultText = $tidy->renderText();

        file_put_contents($htmlFile,$resultText);
//        print_r($resultText);
        $this->redirect(array('/pages/default/update/file/'.$file));
//        $this->redirect(array('/pages', 'id' => $model->id,));
    }


}