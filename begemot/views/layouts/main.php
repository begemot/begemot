    <!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="ru" />


        
    
        
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body>
<div class="container-fluid" style="margin-top:50px;">



        
<?php $this->widget('bootstrap.widgets.TbNavbar', array(
    'type'=>'inverse', // null or 'inverse'
    'brand'=>'Begemot',
    'brandUrl'=>'/begemot',
    'collapse'=>true, // requires bootstrap-responsive.css
    'fluid' => true,
    'items'=>array(
        array(
            'class'=>'bootstrap.widgets.TbMenu',
            'items'=>array(
                        //array('label'=>'About', 'url'=>array('/site/page', 'view'=>'about')),
                        array('label'=>'Система', 'url'=>array(''),
                            'items'=>array(
                                array('label'=>'Пользователи', 'url'=>array('/user/admin'),'visible'=>Yii::app()->hasModule('user')),
                                array('label'=>'Разрешения', 'url'=>array('/srbac'),'visible'=>Yii::app()->hasModule('user')),
                                array('label'=>'Ипорт ролей', 'url'=>array('/RolesImport'),'visible'=>Yii::app()->hasModule('user')),
                              ),
                        ),
                        array('label'=>'Контент', 'url'=>array(''),
                            'items'=>array(
                                array('label'=>'Каталог', 'url'=>array('/catalog/catItem'),'visible'=>Yii::app()->hasModule('catalog')),
                                array('label'=>'Статьи', 'url'=>array('/post/default/admin'),'visible'=>Yii::app()->hasModule('post')),
                                array('label'=>'HTML', 'url'=>array('/pages'),'visible'=>Yii::app()->hasModule('pages')),
                                array('label'=>'Vars', 'url'=>array('/vars'),'visible'=>Yii::app()->hasModule('vars')),
                                array('label'=>'Слайдер', 'url'=>array('/slider/admin'),'visible'=>Yii::app()->hasModule('slider')),
                                array('label'=>'Структура компании', 'url'=>array('/company/companyDepart/admin'),'visible'=>Yii::app()->hasModule('slider')),

                            ),
                        ),

                        array('label'=>'Другие',
                            'items'=>array(
                                array('label'=>'Миграция', 'url'=>array('/migrations'),'visible'=>Yii::app()->hasModule('migrations')),
                                array('label'=>'Парсеры', 'url'=>array('/parsers'),'visible'=>Yii::app()->hasModule('parsers')),
                                array('label'=>'Кронтаб', 'url'=>array('/jobs'),'visible'=>Yii::app()->hasModule('jobs')),
                            ),
                        ),
                        
                        array('label'=>'Файлы', 'url'=>array('/elfinder'),'visible'=>Yii::app()->hasModule('elfinder')),
                        array('label'=>'Сообщения',
                            'items'=>array(
                                array('label'=>'CallBack', 'url'=>array('/callback/callback/index'),'visible'=>Yii::app()->hasModule('callback')),
                                array('label'=>'Коменты', 'url'=>array('/comments'),'visible'=>Yii::app()->hasModule('comments')),
                                array('label'=>'FAQ', 'url'=>array('/faq/admin'),'visible'=>Yii::app()->hasModule('faq')),
                                array('label'=>'Отзывы', 'url'=>array('/reviews/admin'),'visible'=>Yii::app()->hasModule('reviews')),
                            ),
                        ),
                        array('label'=>'Галлерея',
                            'items'=>array(
                                array('url'=>array('/gallery'),'visible'=>Yii::app()->hasModule('gallery'),'label'=>'Фото'),
                                array('url'=>array('/videoGallery/videoGalleryVideo/admin'),'label'=>'Видео'),
                                
                            ),
                        ),   
                
                        array('label'=>'SEO',
                            'items'=>array (
                                ['url'=>array('/seo/seoPages/admin'),'visible'=>Yii::app()->hasModule('seo'),'label'=>'Анализ страниц'],
                                ['url'=>array('/seo/title/index'),'visible'=>Yii::app()->hasModule('seo'),'label'=>'Редактор мета-тегов']
                            )
                        ),
                        array('label'=>'Login', 'url'=>array('/begemot'), 'visible'=>Yii::app()->user->isGuest),
                        array('label'=>'Logout ('.Yii::app()->user->name.')', 'url'=>array('/begemot/default/logout'), 'visible'=>!Yii::app()->user->isGuest)
                ),
        ),
    ),
)); ?>
      
        


	<?php echo $content; ?>





</div><!-- page -->

