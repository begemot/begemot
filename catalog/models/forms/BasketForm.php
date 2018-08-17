<?php
class BasketForm extends CFormModel{

    public $name;
    public $email;
    public $phone;
    public $msg;
    public $shipment;
    public $order;

    public function rules()
    {
        $returnArray = array(
           // array('name','required'),

            array('phone,shipment,name','required'),
            ['order','orderCheck'],
            array('phone, email, msg, shipment', 'safe'),
        );

//        if (Yii::app()->controller->module->capcha) {
//            array_push($returnArray, array('verifyCode', 'required'));
//           array_push($returnArray, array('verifyCode', 'CaptchaExtendedValidator', 'allowEmpty'=>false));
//        }

        return $returnArray;
        
    }





    public function attributeLabels()
    {
        return array(
            'name' => 'Имя',
            'count' => 'Количество',
            'phone' => 'Телефон',
            'eMail' => 'e-mail',
            'msg' => 'Сообщение',
            'shipment'=>'Доставка'

        );
    }

    public function orderCheck($attribute,$params){

        if (!is_array($this->$attribute) || count ($this->$attribute)<1){
            $this->addError($attribute, 'Прежде чем оформлять заказ необходимо добавить в корзину товар!');
        }
    }
}
?>
