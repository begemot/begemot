<?php

class SiteController extends Controller
{
    public $defaultAction = 'review';

    public function actionReview()
    {
        $model = new Reviews;
        if (isset($_POST['Reviews'])) {
            $model->attributes = $_POST['Reviews'];
            if ($model->validate() && $model->save()) {
                $this->render('index');
            }
        } else {
            $this->redirect(array('admin'));
        }
    }

    public function actionReviewIndex()
    {
        $this->layout = '//layouts/clear';

        $formErrors=false;
        $formSend=false;
        $errorsArray = [];

        if (isset($_POST['formSend'])) {

            $formSend=true;

            $review = new Reviews();
            $review->name = isset($_POST['name']) ? $_POST['name'] : null;
            $review->general = isset($_POST['general']) ? $_POST['general'] : null;

            if ($review->save()) {
                $formErrors=false;
            } else {
                $formErrors=true;
                $errorsArray = $review->errors;
            }
        }

        $this->render('index',['errors'=>$errorsArray,'sendFlag'=>$formSend,'errorsFlag'=>$formErrors]);
    }

}