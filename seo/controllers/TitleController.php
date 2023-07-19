 <?php

class TitleController extends Controller
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
				'actions'=>array('index','newPage','delete','update'),
                'expression'=>'Yii::app()->user->canDo("Meta")'
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}       
        
	public function actionIndex()
	{

        $test = new PagesList();

        $test->loadPagesList();

        //$test->addPage('111','222','333','444');
        //$test->deletePage(12);
        //$test->updatePage(13,111,222,333);
        //print_r($test->pages);
        //$test->savePagesList();



        $pagesArray = array();

        foreach($test->pages as $id=>$page){
            $pageItem=array();
            $pageItem['id']=$id;
            $pageItem['url']=$page['url'];
            $pagesArray[] = $pageItem;
        }


        $gridDataProvider = new CArrayDataProvider($pagesArray);
        $gridDataProvider->pagination = array('pageSize'=>20);

        $this->render('index',array('data'=>$gridDataProvider));
	}
        
    public function actionNewPage()
    {
        $model=new NewPage();

        if(isset($_POST['ajax']) && $_POST['ajax']==='new-page-test-form')
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }


        if(isset($_POST['NewPage']))
        {
            $model->attributes=$_POST['NewPage'];
            if($model->validate())
            {
                $pagesList = new PagesList();
                $pagesList->addPage($model->url,$model->title,$model->keywords,$model->description);

                $this->redirect('index');
            }
        }
        $this->render('newPage',array('model'=>$model));
    }
        
	public function actionDelete($id)
	{
        $pagesList = new PagesList();
        $pagesList->deletePage($id);
	}        
        
	public function actionUpdate($id)
	{

        $model=new NewPage('update');

        if(isset($_POST['NewPage']))
        {
            $model->attributes=$_POST['NewPage'];
            if($model->validate())
            {
                $pagesList = new PagesList();
                $pagesList->updatePage($model->id,$model->url,$model->title,$model->keywords,$model->description);

                //$this->redirect('/seo/title/index');
            }
        } else{
            $pagesList = new PagesList();

            $page = $pagesList->pages[$id];

            $model->id = $id;
            $model->url = $page['url'];
            $model->title = $page['title'];
            $model->keywords = $page['keywords'];
            $model->description = $page['description'];
        }



        $this->render('update',array('model'=>$model));
	}

}