<?php
/**
 * Created by JetBrains PhpStorm.
 * User: user
 * Date: 18.06.13
 * Time: 15:51
 * To change this template use File | Settings | File Templates.
 */
require_once('BaseTemplate.php');
class ThreeTemplate extends BaseTemplate
{

    protected $imageCount = 3;

    protected $config;

    public function __construct($config){
        $this->config = $config;

        if (isset($config['templateFile'])){
            $this->templateFile = $config['templateFile'];
        }

    }

    public function renderTemplate()
    {

        if ($this->templateFile === null) {
            $html='<div class="tidyTemplate">';

            for ($i=0;$i<$this->imageCount;$i++){

                $image = $this->getImage();

                if (isset($image['title'])?$title=$image['title']:$title='');
                if (isset($image['alt'])?$alt=$image['alt']:$alt='');

                $html .= '<a rel="prettyPhoto[post]" href="'.$image['original'].'"><img width="28%" style="margin-left:4%;" src="'.$image[$this->config['imageTag']].'" alt="'.$alt.'" title="'.$title.'" /></a>';
            }

            $html.='</div>';
        } else{

            $images = array();
            for ($i=0;$i<$this->imageCount;$i++){
                $images[] = $this->getImage();
            }

            $html = $this->renderFileTemplate($this->templateFile,array('images'=>$images));
        }

        return $html;
    }

    public function getImageCount(){
        return $this->imageCount;
    }



}