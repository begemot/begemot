    <?php

class CallbackModule extends CWebModule
{
    public $mails=array();



    public $smtpUsername; //адрес своего почтового ящика.
    public $smtpPort;
    public $smtpHost; //сервер для отправки почты
    public $smtpPwd; // пароль
    public $smtpDebug=false; //Если Вы хотите видеть сообщения ошибок, укажите true вместо false
    public $smtpCharset; //кодировка сообщений. (windows-1251 или utf-8, итд)
    public $smtpFrom; //Ваше имя - или имя Вашего сайта. Будет показывать при прочтении в поле "От кого"

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

    /**
     * Функция сбора всевозможных сообщений или событий, которые должен зафиксировать сайт.
     * Это могут быть сообщения с форм контактов, форм обратных звонков или заказы в каталоге.
     *
     * Основная задача модуля уведомление администрации сайта о каких либо событиях. Все события фиксируются
     * в админ панели модуля. Так же можно настроить уведомление по электронной почте несколькими методами.
     *
     * Методы отправки эл. почты.
     * - php посредством стандартной функции mail(метод устарел, письма с большой вероятностью могут не доходить)
     * - smtp посредством smtp-протокола
     *
     * @param $title заголовок сообщения
     * @param $text текст
     * @param string $group группа, что бы фильтровать сообщения с разных форм
     * @param bool $sendMail флаг отправки сообщения по эл. почте
     * @param string $sendMethod методы отправки эл. почты
     */
    public static function addMessage($title,$text,$group='',$sendMail=false,$sendMethod='php'){
        Yii::import('application.modules.callback.models.Callback');
        echo 'Попали!';
        if ($sendMail){


            if ($sendMethod=='php'){
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
            } elseif ($sendMethod=='smtp'){

                Yii::import('begemot.extensions.CSmtpMailSender');
                $smtpSender=new CSmtpMailSender();

                $module = Yii::app()->getModule('callback');

                $smtpSender->username = $module->smtpUsername;
                $smtpSender->port = $module->smtpPort;
                $smtpSender->host = $module->smtpHost;
                $smtpSender->pwd = $module->smtpPwd;
                $smtpSender->debug = $module->smtpDebug;
                $smtpSender->charset = $module->smtpCharset;
                $smtpSender->from = $module->smtpFrom;

                $mails = Yii::app()->getModule('callback')->mails;

                foreach ($mails as $mail) {
                    $smtpSender->smtpmail('Sales@profhobby.ru', $mail, $title, $text);
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
