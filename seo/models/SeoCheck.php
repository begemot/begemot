<?php

class SeoCheck extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seo_textru';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array(
			    'id,text_unique,count_chars_with_space,count_chars_without_space,count_words,water_percent,spam_percent,mixed_words,request_date,error_code','numerical','integerOnly'=>true),
			array('userkey, uid, clear_text,error_desc', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
//			array('id, url, href, anchor, type', 'safe', 'on'=>'search'),
		);
	}



	public function sendCheckRequest($content){

	    $siteName = $_SERVER['SERVER_NAME'];

        $postQuery = array();
        $postQuery['text'] = $content;

        $postQuery['userkey'] = "e3e31a36332a5a00d05da3342e81383a";
        // домены разделяются пробелами либо запятыми. Данный параметр является необязательным.
        $postQuery['exceptdomain'] = $siteName;
        // Раскомментируйте следующую строку, если вы хотите, чтобы результаты проверки текста были по-умолчанию доступны всем пользователям
        //$postQuery['visible'] = "vis_on";
        // Раскомментируйте следующую строку, если вы не хотите сохранять результаты проверки текста в своём архиве проверок
        //$postQuery['copying'] = "noadd";
        // Указывать параметр callback необязательно
         $postQuery['callback'] = 'https://'.$siteName.'/seo/checkService/textRuCallBack';

        $postQuery = http_build_query($postQuery, '', '&');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://api.text.ru/post');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postQuery);
        $json = curl_exec($ch);
        $errno = curl_errno($ch);

        // если произошла ошибка
        if (!$errno)
        {
            echo 'ошибки нет';

            $resAdd = json_decode($json);

            if (isset($resAdd->text_uid))
            {
                $text_uid = $resAdd->text_uid;
                $this->uid = $text_uid;
                $this->request_date = time();
                if (!$this->save()){
                    echo ' Ошибка сохранения!';
                    return false;
                } else {
                    echo 'uid:'.$this->uid;
                    return $this->uid;
                }

            }
            else
            {
                $error_code = $resAdd->error_code;
                $error_desc = $resAdd->error_desc;
                $this->request_date = time();
                $this->error_code = $error_code;
                $this->error_desc = $error_desc;

                if (!$this->save()){
                    echo 'Была ошибка! Ошибка сохранения!';
                }
            }
        }
        else
        {
            $errmsg = curl_error($ch);
        }

        curl_close($ch);
    }

    public function getCheckResult(){
        $postQuery = array();
        $postQuery['uid'] = $this->uid;

        $model = $this->findByAttributes(['uid'=>$this->uid]);
        if ($model){
            $this->isNewRecord = false;
            $this->id = $model->id;
        }
        $postQuery['userkey'] = "e3e31a36332a5a00d05da3342e81383a";
//        $postQuery['userkey'] = "e3e31123a36332a5a00123d05da3342e81383a";
        // Раскомментируйте следующую строку, если вы хотите получить более детальную информацию в результатах проверки текста на уникальность
        $postQuery['jsonvisible'] = "detail";

        $postQuery = http_build_query($postQuery, '', '&');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://api.text.ru/post');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postQuery);
        $json = curl_exec($ch);
        $data = json_decode($json,JSON_UNESCAPED_UNICODE);
        $seoData = json_decode($data['seo_check']);
        $errno = curl_errno($ch);



        if (!$errno)
        {
            $resCheck = json_decode($json);
            if (isset($resCheck->text_unique))
            {
                $text_unique = $resCheck->text_unique;
                $resultJson = json_decode($resCheck->result_json);

//                print_r($resultJson);

                $this->userkey = "e3e31a36332a5a00d05da3342e81383a";
                $this->text_unique = intval ($text_unique);

                $this->clear_text = $resultJson->clear_text;

                $this->count_chars_with_space = $seoData->count_chars_with_space;
                $this->count_chars_without_space = $seoData->count_chars_without_space;
                $this->count_words = $seoData->count_words;
                $this->water_percent = $seoData->water_percent;
                $this->spam_percent = $seoData->spam_percent;
                $this->request_date = time();

                //очищаем ошибку
                $this->error_code = '';
                $this->error_desc = '';

                return $this->save();
            }
            else
            {
                $error_code = $resCheck->error_code;
                $error_desc = $resCheck->error_desc;
                $this->request_date = time();
                $this->error_code = $error_code;
                $this->error_desc = $error_desc;

                $this->save();
                return false;
            }
        }
        else
        {
            $errmsg = curl_error($ch);
        }

        curl_close($ch);
    }


	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SeoLinks the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
