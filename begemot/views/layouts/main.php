    <!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="ru" />


        
    
        
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body>
<div class="container" style="margin-top:50px;">



        
<?php $this->widget('bootstrap.widgets.TbNavbar', array(
    'type'=>'inverse', // null or 'inverse'
    'brand'=>'Begemot',
    'brandUrl'=>'/begemot',
    'collapse'=>true, // requires bootstrap-responsive.css
    'items'=>array(
        array(
            'class'=>'bootstrap.widgets.TbMenu',
            'items'=>array(
                        //array('label'=>'About', 'url'=>array('/site/page', 'view'=>'about')),
                        array('label'=>'Система', 'url'=>array(''),
                            'items'=>array(
                                array('label'=>'Пользователи', 'url'=>array('/user'),'visible'=>Yii::app()->hasModule('user')),
                                array('label'=>'Разрешения', 'url'=>array('/srbac'),'visible'=>Yii::app()->hasModule('user')),
                                array('label'=>'Ипорт ролей', 'url'=>array('/RolesImport'),'visible'=>Yii::app()->hasModule('user')),
                              ),
                        ),
                        array('label'=>'Контет', 'url'=>array(''),
                            'items'=>array(
                                array('label'=>'Каталог', 'url'=>array('/catalog/catItem'),'visible'=>Yii::app()->hasModule('catalog')),
                                array('label'=>'Статьи', 'url'=>array('/post/default/admin'),'visible'=>Yii::app()->hasModule('post')),
                                array('label'=>'HTML', 'url'=>array('/pages'),'visible'=>Yii::app()->hasModule('pages')),
                                array('label'=>'Переменные', 'url'=>array('/vars'),'visible'=>Yii::app()->hasModule('vars')),
                            ),
                        ),


                        array('label'=>'Миграция', 'url'=>array('/migrations'),'visible'=>Yii::app()->hasModule('migrations')),
                        array('label'=>'Файлы', 'url'=>array('/elfinder'),'visible'=>Yii::app()->hasModule('elfinder')),
                        array('label'=>'Сообщения',
                            'items'=>array(
                                array('label'=>'CallBack', 'url'=>array('/callback/callback/index'),'visible'=>Yii::app()->hasModule('callback')),
                                array('label'=>'Коменты', 'url'=>array('/comments'),'visible'=>Yii::app()->hasModule('comments')),
                            ),
                        ),
                        array('label'=>'Галлерея',
                            'items'=>array(
                                array('url'=>array('/gallery'),'visible'=>Yii::app()->hasModule('gallery'),'label'=>'Фото'),
                                array('url'=>array('/videoGallery/videoGalleryVideo/admin'),'label'=>'Видео'),
                                
                            ),
                        ),   
                
                        array('label'=>'SEO', 'url'=>array('/seo/seoPages/admin'),'visible'=>Yii::app()->hasModule('seo')),
                        array('label'=>'Login', 'url'=>array('/begemot'), 'visible'=>Yii::app()->user->isGuest),
                        array('label'=>'Logout ('.Yii::app()->user->name.')', 'url'=>array('/begemot/default/logout'), 'visible'=>!Yii::app()->user->isGuest)
                ),
        ),
    ),
)); ?>
      
        


	<?php echo $content; ?>





</div><!-- page -->

