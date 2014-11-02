<?php
/**
 * Created by JetBrains PhpStorm.
 * User: alittlebitmore
 * Date: 2/10/14
 * Time: 5:02 PM
 * To change this template use File | Settings | File Templates.
 */

class KitPopupPart extends CWidget {

    public $defaultText;
    public $header;
    public $text;


    public function run(){

        $defaultText = $this->defaultText;
        $header = $this->header;
        $text = $this->text;

        echo CHtml::Link($defaultText, null, array(
             'class' => 'ipopover',
             'data-trigger' => 'hover',
             'data-title' => $header,
             'data-content' => $text,
             'html' => true
      ));

    }

}