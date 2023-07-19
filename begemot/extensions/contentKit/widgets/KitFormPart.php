<?php
/**
 * Created by JetBrains PhpStorm.
 * User: anton
 * Date: 4/2/13
 * Time: 3:55 PM
 * To change this template use File | Settings | File Templates.
 */

class KitFormPart extends CWidget {

    public $form;
    public $model;


    public function run(){

        $form = $this->form;
        $model = $this->model;

         if($model->hasAttribute('published')){
            echo $form->labelEx($model,'published');
             echo $form->checkBoxRow($model,'published',array());
         }
         if($model->hasAttribute('top'))
            echo $form->checkBoxRow($model,'top',array());


    }

}
