<?php



class MassController extends Controller
{
    public function init() {
        parent::init();
        // Ваш код инициализации
        $path = Yii::getPathOfAlias('catalog.views.catItem.commonMenu');
        $this->menu = require $path.'.php';
    }
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations

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

            array(
                'allow', // allow admin user to perform 'admin' and 'delete' actions


                'actions' => array(
                    'titleAlt'
                ),


                'expression' => 'Yii::app()->user->canDo("*")'
            ),
            array(
                'deny',  // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionTitleAlt(){
        $this->layout = 'begemot.views.layouts.bs5clearLayout';
        $this->render('titleAlt');
    }
}
