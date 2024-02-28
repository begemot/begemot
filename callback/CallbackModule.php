    <?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;
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

        if ($sendMail){


            $mail = new PHPMailer(true);

            try {
                //Server settings
//                $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
                $mail->isSMTP();                                            //Send using SMTP
                $mail->Host = 'smtp.yandex.com';                     //Set the SMTP server to send through
                $mail->SMTPAuth = true;                                   //Enable SMTP authentication
                $mail->Username = 'info@transautogroup.ru';                     //SMTP username
                $mail->Password = 'fyAMILMOOooL';                               //SMTP password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
                $mail->Port = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
                $mail->CharSet = PHPMailer::CHARSET_UTF8;

                //Recipients
                $mail->setFrom('info@transautogroup.ru', 'TAG Robot');
                $mails = Yii::app()->getModule('callback')->mails;

                foreach ($mails as $mailitem)
                 $mail->addAddress($mailitem, '');     //Add a recipient
//            $mail->addAddress('ellen@example.com');               //Name is optional
                //   $mail->addReplyTo('info@example.com', 'Information');
//           / $mail->addCC('cc@example.com');
//            $mail->addBCC('bcc@example.com');

                //Attachments
//            $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
//            $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

                //Content
                $mail->isHTML(true);                                  //Set email format to HTML
                $mail->Subject = $title;
                $mail->Body = $text;
                $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

                $mail->send();
                echo 'Message has been sent';
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
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
