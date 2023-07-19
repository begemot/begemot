<?php

class Page extends CInputWidget{

    public $page;
    public $view = null;
    public $baseContent = null;


    
    public function run(){

        parent::run();

        $pageContentPath = Yii::getPathOfAlias('webroot.files.pages').'/'.$this->page.'.php';

        if (file_exists($pageContentPath)){

            if ($this->view !== null){
                //$pageContentPath = Yii::getPathOfAlias('webroot.files.pages.'.$this->page).'.php';

                $fileContent = file_get_contents($pageContentPath);

                $this->render($this->view,array('content'=>$fileContent));

            } else{

                $this->render('webroot.files.pages.'.$this->page);
            }
        } else {
            file_put_contents ($pageContentPath,$this->baseContent);
        }
        return;
       
    }
}
?>
