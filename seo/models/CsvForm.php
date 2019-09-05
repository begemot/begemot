<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of csvForm
 *
 * @author Антон
 */
class CsvForm extends CFormModel {
    //put your code here
    public $csv;
    
    public function rules()
    {
            // NOTE: you should only define rules for those attributes that
            // will receive user inputs.
            return array(
                    array('csv', 'safe'),
            );
    }

}

?>
