<?php
/**
 * Created by JetBrains PhpStorm.
 * User: anton
 * Date: 09.06.13
 * Time: 11:48
 * To change this template use File | Settings | File Templates.
 */

class MetaLoader {
    private static $title;
    private static $kewords;
    private static $description;
    private static $relCanonical;

    public static $wasLoaded = false;

    public static function getTitle(){

        self::loadData();

        if (self::$title==null){
            return Yii::app()->controller->pageTitle;
        } else {
            return self::$title;
        }

    }

    public static function getKeywords(){

        self::loadData();
        return self::$kewords;
    }

    public static function getDescription(){

        self::loadData();
        return self::$description;
    }

    /**
     *  Для задания канонической страницы для текущей странице. Вызываем эту функцию и устанавливаем ссылку на
     * каноническую страницу, если нужно.
     *
     *
     * @param $value
     */
    public static function setRelCanonical($value){


        self::$relCanonical = $value;
    }

    public static function getRelCanonical(){

        return self::$relCanonical;
    }

    public static function loadData(){
        if (!self::$wasLoaded){
            $filePath = Yii::getPathOfAlias('webroot.files.meta').'/data.php';

            if (file_exists($filePath)){

                $data = require ($filePath);
                $currentUri = $_SERVER['REQUEST_URI'];


                foreach ($data['pages'] as $page){
                    if (isset($page['url'])){
                        if ($page['url']==$currentUri){

                            self::$title = (isset($page['title'])?$page['title']:null);
                            self::$kewords = isset($page['keywords'])?$page['keywords']:null;
                            self::$description = isset($page['description'])?$page['description']:null;
                            self::$relCanonical = isset($page['relCanonical'])?$page['relCanonical']:null;

                            self::$wasLoaded = true;

                            break;
                        }
                    }
                }

            }
        }
    }





}