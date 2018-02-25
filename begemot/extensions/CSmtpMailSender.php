<?php
/**
 * Created by PhpStorm.
 * User: Николай Козлов
 * Date: 25.02.2018
 * Time: 15:24
 */

class CSmtpMailSender
{
    public $username; //адрес своего почтового ящика.
    public $port;
    public $host; //сервер для отправки почты
    public $pwd; // пароль
    public $debug; //Если Вы хотите видеть сообщения ошибок, укажите true вместо false
    public $charset; //кодировка сообщений. (windows-1251 или utf-8, итд)
    public $from; //Ваше имя - или имя Вашего сайта. Будет показывать при прочтении в поле "От кого"


    public function smtpmail($to='', $mail_to, $subject, $message, $headers='') {

        $SEND =	"Date: ".date("D, d M Y H:i:s") . " UT\r\n";
        $SEND .= 'Subject: =?'.$this->charset.'?B?'.base64_encode($subject)."=?=\r\n";
        if ($headers) $SEND .= $headers."\r\n\r\n";
        else
        {
            $SEND .= "Reply-To: ".$this->username."\r\n";
            $SEND .= "To: \"=?".$this->charset."?B?".base64_encode($to)."=?=\" <$mail_to>\r\n";
            $SEND .= "MIME-Version: 1.0\r\n";
            $SEND .= "Content-Type: text/html; charset=\"".$this->charset."\"\r\n";
            $SEND .= "Content-Transfer-Encoding: 8bit\r\n";
            $SEND .= "From: \"=?".$this->charset."?B?".base64_encode($this->from)."=?=\" <".$this->username.">\r\n";
            $SEND .= "X-Priority: 3\r\n\r\n";
        }
        $SEND .=  $message."\r\n";
        if( !$socket = fsockopen($this->host, $this->port, $errno, $errstr, 30) ) {
            if ($this->debug) echo $errno."<br>".$errstr;
            return false;
        }

        if (!$this->server_parse($socket, "220", __LINE__)) return false;

        fputs($socket, "HELO " . $this->host . "\r\n");
        if (!$this->server_parse($socket, "250", __LINE__)) {
            if ($this->debug) echo '<p>Не могу отправить HELO!</p>';
            fclose($socket);
            return false;
        }
        fputs($socket, "AUTH LOGIN\r\n");
        if (!$this->server_parse($socket, "334", __LINE__)) {
            if ($this->debug) echo '<p>Не могу найти ответ на запрос авторизаци.</p>';
            fclose($socket);
            return false;
        }
        fputs($socket, base64_encode($this->username) . "\r\n");
        if (!$this->server_parse($socket, "334", __LINE__)) {
            if ($this->_debug) echo '<p>Логин авторизации не был принят сервером!</p>';
            fclose($socket);
            return false;
        }
        fputs($socket, base64_encode($this->pwd) . "\r\n");
        if (!$this->server_parse($socket, "235", __LINE__)) {
            if ($this->debug) echo '<p>Пароль не был принят сервером как верный! Ошибка авторизации!</p>';
            fclose($socket);
            return false;
        }
        fputs($socket, "MAIL FROM: <".$this->username.">\r\n");
        if (!$this->server_parse($socket, "250", __LINE__)) {
            if ($this->debug) echo '<p>Не могу отправить комманду MAIL FROM: </p>';
            fclose($socket);
            return false;
        }
        fputs($socket, "RCPT TO: <" . $mail_to . ">\r\n");

        if (!$this->server_parse($socket, "250", __LINE__)) {
            if ($this->debug) echo '<p>Не могу отправить комманду RCPT TO: </p>';
            fclose($socket);
            return false;
        }
        fputs($socket, "DATA\r\n");

        if (!$this->server_parse($socket, "354", __LINE__)) {
            if ($this->debug) echo '<p>Не могу отправить комманду DATA</p>';
            fclose($socket);
            return false;
        }
        fputs($socket, $SEND."\r\n.\r\n");

        if (!$this->server_parse($socket, "250", __LINE__)) {
            if ($this->debug) echo '<p>Не смог отправить тело письма. Письмо не было отправленно!</p>';
            fclose($socket);
            return false;
        }
        fputs($socket, "QUIT\r\n");
        fclose($socket);
        return TRUE;
    }

    public function server_parse($socket, $response, $line = __LINE__) {
        $server_response = '';
        while (@substr($server_response, 3, 1) != ' ') {
            if (!($server_response = fgets($socket, 256))) {
                if ($this->debug) echo "<p>Проблемы с отправкой почты!</p>$response<br>$line<br>";
                return false;
            }
        }
        if (!(substr($server_response, 0, 3) == $response)) {
            if ($this->debug) echo "<p>Проблемы с отправкой почты!</p>$response<br>$line<br>";
            return false;
        }
        return true;
    }

}