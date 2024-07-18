<?php

class Metrika extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'metrika';
    }

    public function rules()
    {
        return array(
            array('domain, counter_id', 'required'),
            array('domain', 'length', 'max' => 255),
            array('counter_id', 'numerical', 'integerOnly' => true),
            // The following rule is used by search().
            array('id, domain, counter_id', 'safe', 'on' => 'search'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'domain' => 'Домен',
            'counter_id' => 'ID счетчика',
        );
    }

    // Используем базу данных commonDb
    public function getDbConnection()
    {
        return Yii::app()->commonDb;
    }

    // Метод для получения HTML-кода метрики
    public function getMetrikaCode()
    {
        return "<!-- Yandex.Metrika counter -->
<script type=\"text/javascript\" >
    (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
    m[i].l=1*new Date();
    k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
    (window, document, \"script\", \"https://mc.yandex.ru/metrika/tag.js\", \"ym\");

    ym({$this->counter_id}, \"init\", {
        clickmap:true,
        trackLinks:true,
        accurateTrackBounce:true,
        webvisor:true
    });
</script>
<noscript><div><img src=\"https://mc.yandex.ru/watch/{$this->counter_id}\" style=\"position:absolute; left:-9999px;\" alt=\"\" /></div></noscript>
<!-- /Yandex.Metrika counter -->";
    }

    public static function checkAndDisplayMetrika()
    {
        $domain = parse_url(Yii::app()->request->getHostInfo(), PHP_URL_HOST);
        $metrika = self::model()->findByAttributes(array('domain' => $domain));
        if ($metrika) {
            return $metrika->getMetrikaCode();
        } else {
            return '<div class="text-danger">Метрика не подключена</div>';
        }
    }
    
}
