<?php
class RusDate{
    private static  $days = array(
        '0'=>'Воскресенье',
        '1'=>'Понедельник',
        '2'=>'Вторник',
        '3'=>'Среда',
        '4'=>'Четверг',
        '5'=>'Пятница',
        '6'=>'Суббота',
    );
    
    private static  $month = array (
        '0'=>'Января',
        '1'=>'Февраля',
        '2'=>'Марта',
        '3'=>'Апреля',
        '4'=>'Мая',
        '5'=>'Июня',
        '6'=>'Июля',
        '7'=>'Августа',
        '8'=>'Сентября',
        '9'=>'Октября',
        '10'=>'Ноября',
        '11'=>'Декабря',
                
    );
    
    private static  $monthName = array (
        '0'=>'Январь',
        '1'=>'Февраль',
        '2'=>'Март',
        '3'=>'Апрель',
        '4'=>'Май',
        '5'=>'Июнь',
        '6'=>'Июль',
        '7'=>'Август',
        '8'=>'Сентябрь',
        '9'=>'Октябрь',
        '10'=>'Ноябрь',
        '11'=>'Декабрь',
                
    );
    
    private  static  $shortMonthName = array (
        '0'=>'Янв',
        '1'=>'Фев',
        '2'=>'Мар',
        '3'=>'Апр',
        '4'=>'Май',
        '5'=>'Июн',
        '6'=>'Июл',
        '7'=>'Авг',
        '8'=>'Сен',
        '9'=>'Окть',
        '10'=>'Ноя',
        '11'=>'Дек',
                
    );    
    
    
    public static function getWeekDay($day){
        return self::$days[$day];
    }
    
     public static  function getMonth($month){
        return self::$month[$month-1];
         
    }   
     public static  function getShortMonth($month){
        return self::$shortMonthName[$month-1];
    }   
    
     public static  function getMonthName($month){
        return self::$monthName[$month-1];
    }   
    
}	
?>