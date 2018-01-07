    <?php

class CallbackModule extends CWebModule
{
    public $mails=array();

	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'callback.models.*',
			'callback.components.*',
		));
	}

    public function beforeControllerAction($controller, $action) {
        if ($controller->id != 'site') {
            $component=Yii::createComponent(array(

                'class'=>'begemot.extensions.bootstrap.components.Bootstrap'

            ));
            Yii::app()->setComponent('bootstrap',$component);
        }
        return true;
    }

    public static function addMessage($title,$text,$group='',$sendMail=false){
        Yii::import('application.modules.callback.models.Callback');

        if ($sendMail){
            $mails = Yii::app()->getModule('callback')->mails;

            if (count($mails)>0){

                $headers='From: '.$group.' '.$_SERVER['SERVER_NAME'].' <'.$_SERVER['SERVER_NAME']. ">\r\n" .
                    "MIME-Version: 1.0\r\n".
                    "Content-type: text/html; charset=UTF-8";

                $subject=$title;
                foreach ($mails as $mail){

                    mail($mail, $subject, $text,$headers);
                }
            }
        }

        $msg = new  Callback();
        $msg->title = $title;
        $msg->text = $text;
        $msg->group = $group;
        $msg->date = date('Y-m-d H:i:s',time());
        $msg->save();





    }


}
