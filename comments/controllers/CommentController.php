<?php
/**
* Comment controller class file.
*
* @author Dmitry Zasjadko <segoddnja@gmail.com>
* @link https://github.com/segoddnja/ECommentable
* @version 1.0
* @package Comments module
* 
*/
class CommentController extends Controller
{
    public $defaultAction = 'admin';
    
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
    public $layout='begemot.views.layouts.column1';
    public $adminMode = false;
    public $registeredOnly = true;
    public $useCaptcha = false;
    public $postCommentAction = 'comments/comment/postComment';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
            'ajaxOnly + postComment, approve, likeOrDislike',
		);
	}

    protected function beforeAction($action){
        if(Yii::app()->request->isAjaxRequest){
            Yii::app()->clientScript->scriptMap['jquery.js'] = false;
            Yii::app()->clientScript->scriptMap['*.css'] = false;
        }

        return true;
    }
        
        /**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',
                'actions'=>array('delete', 'update', 'ajaxSubmit'),
                'users'=>array('*')
			),
			array('allow',
				'actions'=>array('admin', 'delete', 'approve', 'likeOrDislike'),
                'expression'=>'Yii::app()->user->canDo("")'
			),
            
            array('deny',
                'actions'=>array('admin', 'approve'),
                'users'=>array('*')
            ),
		);
	}

	/**
	 * Deletes a particular model.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		// we only allow deletion via POST request
        $result = array('deletedID' => $id);
        $comment = $this->loadModel($id);
        if($comment->setDeleted()){
            $result['code'] = 'success';

            /*$model=Comment::model()->findByPk($comment->owner_id);
            $model->comments--;
            if($model->comments < 0) $model->comments = 0;
            $model->save();*/
        }
        else 
            $result['code'] = 'fail';

        echo CJSON::encode($result);
	}


        
    /**
	 * Approves a particular model.
	 * @param integer $id the ID of the model to be approve
	 */
	public function actionApprove($id)
	{
		// we only allow deletion via POST request
        $result = array('approvedID' => $id);
        if($this->loadModel($id)->setApproved())
            $result['code'] = 'success';
        else 
            $result['code'] = 'fail';
        echo CJSON::encode($result);
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
        $model=new Comment('search');

        $model->unsetAttributes();  // clear any default values
        if(isset($_GET['Comment']))
			$model->attributes=$_GET['Comment'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

    public function actionUpdate($id){
        $this->layout = '//layouts/modal';
        $model = $this->loadModel($id, 'Comment');


        if (Yii::app()->user->isGuest) {
            throw new Exception("Ошибка запроса", 1);
        }
        if(!Yii::app()->user->canDo("") && $model->creator_id != Yii::app()->user->id){
            throw new Exception("Ошибка запроса", 1);
        }

        $sub_comments = Comment::model()->findByAttributes(array('parent_comment_id' => $id));

        if(count($sub_comments) > 0){
            throw new Exception("Ошибка запроса", 1);
        }

        if(isset($_POST['ajax'])){
            echo UActiveForm::validate(array($model));
            Yii::app()->end();
        }

        if (isset($_POST['Comment'])) {
            $model->setAttributes($_POST['Comment']);

            if($model->validate()){
                
                if ($model->save()) {

                    if (Yii::app()->getRequest()->isAjaxRequest){
                        if(isset(Yii::app()->theme) && Yii::app()->theme->name != ""){
                            $path = 'webroot.themes.' . Yii::app()->theme->name . '.views.ECommentsListWidget.ECommentsWidgetCommentsAjax';
                        }
                        else $path = 'application.modules.comments.widgets.views.ECommentsWidgetCommentsAjax';

                        $adminMode = (Yii::app()->user->canDo("")) ? true : false;

                        $comments = array();
                        $comments[] = $model;
                        $this->renderPartial($path,array('comments'=>$comments, $adminMode => $adminMode), false, true);
                    }
                    else{
                        $task = Tasks::model()->findByPk($model->owner_id);
                        $this->redirect(array("/tasks/site/view", 'itemName' => $task->title_t, 'id' => $task->id, '#' => 'comment-' . $model->comment_id));
                    }
                }

                
            }
            else{
                if(isset($_POST['ajax'])){
                    echo $model->getErrors();
                    Yii::app()->end();
                }
            }
        }
        else{

            if(isset(Yii::app()->theme) && Yii::app()->theme->name != ""){
                $path = 'webroot.themes.' . Yii::app()->theme->name . '.views.ECommentsListWidget.update';
            }
            else $path = 'application.modules.comments.widgets.views.update';

            if(Yii::app()->user->canDo("") || ($model->creator_id == Yii::app()->user->id && count($sub_comments) == 0)){
                if(Yii::app()->request->isAjaxRequest){
                    $this->renderPartial($path,array('newComment'=>$model, 'edit' => true), false, true);

                } else{
                    $this->render($path, array(
                        'newComment' => $model,
                        'edit' => true,
                    ));
                }
            } else throw new CHttpException(400, Yii::t('app', 'Нету доступа'));
        }
    }

    public function actionAjaxSubmit()
    {
        if(isset($_POST['Comment']))
        {
            $comment = new Comment();
            $comment->attributes = $_POST['Comment'];


            if (!$comment->validate()){
                echo CActiveForm::validate( array( $comment));
                
                Yii::app()->end();
            }
            else{

                if($comment->save()){
                    // $model=Comment::model();
                    // $transaction=$model->dbConnection->beginTransaction();
                    // try
                    // {

                    //     $model=$model->findByPk($comment->owner_id);

                    //     if(isset($model->comments)){
                    //         $model->comments++;

                    //         if($model->save())
                    //             $transaction->commit();
                    //         else
                    //             $transaction->rollback();
                    //     }
                        
                    // }
                    // catch(Exception $e)
                    // {
                    //     $transaction->rollback();
                    //     throw $e;
                    //     Yii::log($e, 3, 'transaction_error');
                    // }

                    if(!Yii::app()->request->isAjaxRequest){
                        $task = Tasks::model()->findByPk($comment->owner_id);
                        $this->redirect(array("/tasks/site/view",'itemName' => $task->title_t, 'id' => $task->id));
                    }

                    $comments[] = $comment;
                    $newComment = new Comment();
                    $newComment->owner_name = Yii::app()->user->name . " " . Yii::app()->user->lastName;
                    $newComment->owner_id = Yii::app()->user->id;


                    if(isset(Yii::app()->theme) && Yii::app()->theme->name != ""){
                        $path = 'webroot.themes.' . Yii::app()->theme->name . '.views.ECommentsListWidget.ECommentsWidgetCommentsAjax';
                    }
                    else $path = 'application.modules.comments.widgets.views.ECommentsWidgetCommentsAjax';

                    if(Yii::app()->request->isAjaxRequest){
                        $this->renderPartial($path, array('comments' => $comments, 'newComment' => $newComment), false, true);
                    }
                    Yii::app()->end();
                }

                
            }
        }



    }

    public function actionPostComment()
    {

        if(isset($_POST['Comment']) && Yii::app()->request->isAjaxRequest)
        {
            $comment = new Comment();
            $comment->attributes = $_POST['Comment'];

            if (!$comment->validate()){
                echo CActiveForm::validate( array( $comment));
                
                Yii::app()->end();
            }

            //return true;

            if($comment->save())
            {
                $result['code'] = 'success';
                $this->beginClip("list");
                    $this->widget('comments.widgets.ECommentsListWidget', array(
                        'model' => $comment->ownerModel,
                        'showPopupForm' => false,
                    ));
                $this->endClip();
                $this->beginClip('form');
                    $this->widget('comments.widgets.ECommentsFormWidget', array(
                        'model' => $comment->ownerModel,
                    ));
                $this->endClip();
                $result['list'] = $this->clips['list'];
            }
            else 
            {
                $result['code'] = 'fail';
                $this->beginClip('form');
                    $this->widget('comments.widgets.ECommentsFormWidget', array(
                        'model' => $comment->ownerModel,
                        'validatedComment' => $comment,
                    ));
                $this->endClip();
            }
            $result['form'] = $this->clips['form'];


            //echo CJSON::encode($result);
        }
    }

    public function actionTest()
    {
        echo "ok";
    }

    public function actionLikeOrDislike($id, $doLikeOrDislike){

        if (Yii::app()->user->id != null) {
            $needToDowngrade = false;

            if(CommentsLikesAndDislikes::model()->deleteAllByAttributes(array('user_id' => Yii::app()->user->id, 'comment_id' => $id))){
                $needToDowngrade = true;
            }
            $addNewOne = new CommentsLikesAndDislikes(); 
            $addNewOne->user_id = Yii::app()->user->id;
            $addNewOne->comment_id = $id;
            $addNewOne->like_or_dislike = intval($doLikeOrDislike);
            if($addNewOne->save()){

                $model=Comments::model();
                $transaction=$model->dbConnection->beginTransaction();
                try
                {
                    $comment=$model->findByPk($id);
                    if(intval($doLikeOrDislike)){
                        $comment->likes++;
                        if($needToDowngrade) $comment->dislikes--;
                    } else {
                        $comment->dislikes++;
                        if($needToDowngrade) $comment->likes--;
                    }

                    if($comment->likes < 0) $comment->likes = 0;
                    if($comment->dislikes < 0) $comment->dislikes = 0;
                    
                    if($comment->save())
                        $transaction->commit();
                    else
                        $transaction->rollback();
                }
                catch(Exception $e)
                {
                    $transaction->rollback();
                    Yii::log($e, 3, 'transaction_error');
                }
            }


            $comment = Comment::model()->with('likes', 'dislikes')->findByPk($id);

            echo json_encode(array('success' => true, 'likes' => $comment->likes, 'dislikes' => $comment->dislikes));
            return true;
        }

        return false;
    }

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Comment::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
}
