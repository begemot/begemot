<?php
class BuyForm extends CFormModel{

    public $name;
    public $email;
    public $phone;
    public $count;
    public $msg;
    public $model;
    public $verifyCode;

    public function rules()
    {
        $returnArray = array(
           // array('name','required'),
            array('phone','phoneOrMail'),
            array('phone, count, msg, model', 'safe'),
        );


        if (Yii::app()->controller->module->capcha) {
           array_push($returnArray, array('verifyCode','captcha'));
        }

        return $returnArray;


    }

    public function phoneOrMail($attribute,$params){
        if ( trim($this->phone)=='' && trim($this->email)==''){
            $this->addError('phone','Нужно указать телефон или электронную почту!');
            return false;
        }
        return true;
    }

    public function attributeLabels()
    {
        return array(
            'name' => 'Имя',
            'count' => 'Количество',
            'phone' => 'Телефон',
            'eMail' => 'e-mail',
            'msg' => 'Сообщение',
            'verifyCode' => 'Код проверки'
        );
    }
}
?>
