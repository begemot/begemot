<?php
class PageUrlRule extends CBaseUrlRule
{
    public $connectionID = 'db';
 
    public function createUrl($manager,$route,$params,$ampersand)
    {
        if ($route==='site/page')
        {
            if (isset($params['view']))
                return $params['view'].'.html';

        }
        return false;  // не применяем данное правило
    }
 
    public function parseUrl($manager,$request,$pathInfo,$rawPathInfo)
    {
        if (preg_match('%^([A-Za-z0-9\-\_]+)$%', $pathInfo, $matches))
        {
            // Проверяем $matches[1] и $matches[3] на предмет
            // соответствия производителю и модели в БД.
            // Если соответствуют, выставляем $_GET['manufacturer'] и/или $_GET['model']
            // и возвращаем строку с маршрутом 'car/index'.


            $filename = Yii::getPathOfAlias('webroot.files.pages').'/'.$matches[1].'.php';
            if (file_exists($filename)){
                return 'site/page/view/'.$matches[1];
            } else {
                return false;
            }
                
        }
        return false;  // не применяем данное правило
    }
}
?>