<?php
return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '../..',


    //'theme' => 'custom',
    // preloading 'log' component
    'preload' => array('log'),
    // autoloading model and component classes
    'import' => array(
        'application.models.*',
        'application.components.*',
        'application.modules.begemot.components.NestedDynaTree.*',
        'application.modules.begemot.extensions.giix.*',
        'application.modules.begemot.extensions.giix.components.*',
        'application.modules.begemot.extensions.crontab.*',
        'application.modules.catalog.models.CatItem',
        'application.modules.post.models.Posts',
        'application.modules.user.models.*',
        'application.modules.user.components.*',
        'application.modules.vars.*',
    ),
    'language' => 'ru',
    'modules' => array(
        'pictureBox',
        'begemot',
        'catalog',
        'pages',
        'parsers',
        'elfinder',
        'gallery',
        'post',
        'seo',
        'migrations',
        'RolesImport',
        'srbac' => array(
            'userclass'=>'User', //default: User
            'userid'=>'id', //default: userid
            'username'=>'username', //default:username
            'delimeter'=>'@', //default:-
            'debug'=>true, //default :false
            'pageSize'=>10, // default : 15
            'superUser' =>'admin', //default: Authorizer
            'css'=>'srbac.css', //default: srbac.css
            //  'layout'=>
            //  'application.views.layouts.main', //default: application.views.layouts.main,//must be an existing alias
            'notAuthorizedView'=> 'srbac.views.authitem.unauthorized', // default:

            'alwaysAllowed'=>array(
                'SiteLogin','SiteLogout','SiteIndex','SiteAdmin',
                'SiteError', 'SiteContact'),
            'userActions'=>array('Show','View','List'), //default: array()
            'listBoxNumberOfLines' => 15, //default : 10
            'imagesPath' => 'srbac.images', // default: srbac.images
            'imagesPack'=>'noia', //default: noia
            'iconText'=>true, // default : false
            'header'=>'srbac.views.authitem.header', //default : srbac.views.authitem.header,
            //must be an existing alias
            'footer'=>'srbac.views.authitem.footer', //default: srbac.views.authitem.footer,
            //must be an existing alias
            'showHeader'=>true, // default: false
            'showFooter'=>true, // default: false
            'alwaysAllowedPath'=>'srbac.components', // default: srbac.components
            // must be an existing alias
        ),
        'videoGallery',

        'user' => array(
            # encrypting method (php hash function)
            'hash' => 'md5',
            # send activation email
            'sendActivationMail' => true,
            # allow access for non-activated users
            'loginNotActiv' => false,
            # activate user on registration (only sendActivationMail = false)
            'activeAfterRegister' => false,
            # automatically login from registration
            'autoLogin' => true,
            # registration path
            'registrationUrl' => array('/user/registration'),
            # recovery password path
            'recoveryUrl' => array('/user/recovery'),
            # login form path
            'loginUrl' => array('/user/login'),
            # page after login
            'returnUrl' => array('/admin'),
            # page after login
            'logoutUrl' => array('/user/logout'),
            'tableUsers' => 'users',
            'tableProfiles' => 'profiles',
            'tableProfileFields' => 'profiles_fields',
        ),
        'comments' =>    array(
            //you may override default config for all connecting models
            'defaultModelConfig' => array(
                //only registered users can post comments
                'registeredOnly' => false,
                'useCaptcha' => false,
                //allow comment tree
                'allowSubcommenting' => true,
                //display comments after moderation
                'premoderate' => true,
                //action for postig comment
                'postCommentAction' => 'comments/comment/postComment',
                //super user condition(display comment list in admin view and automoderate comments)
                'isSuperuser'=>'Yii::app()->user->checkAccess("moderate")',
                //order direction for comments
                'orderComments'=>'DESC',
            ),
            //the models for commenting
            'commentableModels'=>array(
                //model with individual settings
                'Posts'=>array(

                    'registeredOnly'=>false,
                    'useCaptcha'=>false,
                    'allowSubcommenting'=>true,
                    //config for create link to view model page(page with comments)
                    'pageUrl'=>array(
                        'route'=>'admin/citys/view',
                        'data'=>array('id'=>'id'),
                    ),
                ),
            ),

            //config for user models, which is used in application
//            'userConfig'=>array(
//                'class'=>'User',
//                'nameProperty'=>'username',
//                'emailProperty'=>'email',
//            ),

        ),
    ),
    // application components
    'components' => array(
        'user' => array(
            // enable cookie-based authentication
            'class' => 'application.modules.user.components.WebUser',
            'allowAutoLogin' => true,
            'loginUrl' => array('/user/login'),
            'returnUrl'=>array('/begemot'),
        ),
        'bootstrap' => array(
            'class' => 'begemot.extensions.bootstrap.components.Bootstrap', // assuming you extracted bootstrap under extensions
        ),
        // uncomment the following to enable URLs in path-format

        'urlManager' => array(
            'urlFormat' => 'path',
            'showScriptName' => false,
            'urlSuffix' => '.html',
            'rules' => array(
                '/admin' => '/begemot',
                //модуль gallery
                '/photo' => array('gallery/siteGallery/index'),
                '/photo/<title:[\w-]+>_<id:[\w-]+>' => array('gallery/siteGallery/viewGallery'),
                //модуль pages
                array('class' => 'pages.components.PageUrlRule'),
                //модуль catalog
                'catalog' => 'catalog/site/index',
                'catalog/<title:[\w-]+>_<catId:\d+>' => 'catalog/site/categoryView',
                'catalog/<title:[\w-]+>_<catId:\d+>/<itemName:[\w-]+>_<item:\d+>' => 'catalog/site/itemView',
                //модуль post
                'posts' => 'post/site/tagIndex',
                'posts/<title:\w+>_<id:\d+>' => 'post/site/tagIndex',
                'postsView/<title:[\w-]+>_<id:\d+>' => 'post/site/view',

                'contact' => 'site/contact'
            )
        ),

        'errorHandler' => array(
            // use 'site/error' action to display errors
            'errorAction' => 'site/error',
        ),
        'authManager' => array(
// Path to SDbAuthManager in srbac module if you want to use case insensitive
//access checking (or CDbAuthManager for case sensitive access checking)
            'class' => 'application.modules.srbac.components.SDbAuthManager',
// The database component used
            'connectionID' => 'db',
// The itemTable name (default:authitem)
//            'itemTable' => 'items',
//// The assignmentTable name (default:authassignment)
//            'assignmentTable' => 'assignments',
//// The itemChildTable name (default:authitemchild)
//            'itemChildTable' => 'itemchildren',
        ),

//        'request' => array(
//                        'baseUrl' => 'http://www.buggy-motor.ru',
//                    ),
        /*'cache' => array(
            'class' => 'system.caching.CDbCache',
        ),*/

    ),
    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
    'params' => array(
        // this is used in contact page
        'adminEmail' => 'scott2to@gmail.com',
    ),
);