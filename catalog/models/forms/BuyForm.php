<?php
class BuyForm extends CFormModel{

    public $name;
    public $email;
    public $phone;
    public $count;
    public $msg;
    public $model;

    public function rules()
    {

        return array(
                       // array('name','required'),
                        array('phone','phoneOrMail'),
                        array('phone,email, count, msg, model', 'safe'),
                    );


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
        );
    }
}
?>
