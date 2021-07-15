<?php

class PictureBoxModule extends CWebModule {

    public function init() {
        // this method is called when the module is being created
        // you may place code here to customize the module or the application
        // import the module-level models and components
        $this->setImport(array(
            'pictureBox.models.*',
            'pictureBox.components.*',
            'pictureBox.components.filters.*',
            'pictureBox.controllers.*',
        ));
        Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.pictureBox.assets'));
    }

}
