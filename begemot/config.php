<?php
return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '../..',

    'name' => 'My Web Application',
    // preloading 'log' component
    'preload' => array('log'),
    // autoloading model and component classes
    'layout' => 'clearNoAnimate',
    'import' => array(
        'application.models.*',
        'application.components.*',
        'application.modules.begemot.extensions.giix.components.*',
        'application.modules.begemot.components.NestedDynaTree.*',
        'application.modules.begemot.extensions.crontab.*',
        'application.modules.begemot.extForBaseClasses.*',
        'application.modules.catalog.models.CatItem',
        'application.modules.post.models.Posts',
        'application.modules.user.models.*',
        'application.modules.user.components.*',
        'application.modules.vars.*',
        'application.modules.jobs.components.*', // components for jobs
        'application.jobs.*',
        'application.extensions.editable.bootstrap-editable.*'
    ),
    'language' => 'ru',

    'modules' => array(
        'pictureBox',
        'begemot',
        'catalog',
        'jobs',
        'contentTask',
        'company',
        'pages' => [
            'tidyConfig' => array(
                'Three' => array(
                    'imageTag' => 'innerBig',
                    'templateFile' => 'application.config.tidyThreeTamplate',
                ),
            )
        ],
        'parsers',
        'elfinder',
        'gallery',
        'post',
        'seo',
        'migrations',
        'RolesImport',
        'priceList',
        'faq',
        'reviews',
        'slider',
        'srbac' => array(
            'userclass' => 'User', //default: User
            'userid' => 'id', //default: userid
            'username' => 'username', //default:username
            'delimeter' => '@', //default:-
            'debug' => true, //default :false
            'pageSize' => 10, // default : 15
            'superUser' => 'admin', //default: Authorizer
            'css' => 'srbac.css', //default: srbac.css
            //  'layout'=>
            //  'application.views.layouts.main', //default: application.views.layouts.main,//must be an existing alias
            'notAuthorizedView' => 'srbac.views.authitem.unauthorized', // default:

            'alwaysAllowed' => array(
                'SiteLogin', 'SiteLogout', 'SiteIndex', 'SiteAdmin',
                'SiteError', 'SiteContact'),
            'userActions' => array('Show', 'View', 'List'), //default: array()
            'listBoxNumberOfLines' => 15, //default : 10
            'imagesPath' => 'srbac.images', // default: srbac.images
            'imagesPack' => 'noia', //default: noia
            'iconText' => true, // default : false
            'header' => 'srbac.views.authitem.header', //default : srbac.views.authitem.header,
            //must be an existing alias
            'footer' => 'srbac.views.authitem.footer', //default: srbac.views.authitem.footer,
            //must be an existing alias
            'showHeader' => true, // default: false
            'showFooter' => true, // default: false
            'alwaysAllowedPath' => 'srbac.components', // default: srbac.components
            // must be an existing alias
        ),
        'videoGallery',
        'vars',
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
            'recoveryUrl' => array('/user/recovery/recovery'),
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
        'comments' => array(
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
                'isSuperuser' => 'Yii::app()->user->checkAccess("moderate")',
                //order direction for comments
                'orderComments' => 'DESC',
            ),
            //the models for commenting
            'commentableModels' => array(
                //model with individual settings
                'Posts' => array(

                    'registeredOnly' => false,
                    'useCaptcha' => false,
                    'allowSubcommenting' => false,
                    //config for create link to view model page(page with comments)
                    'pageUrl' => array(
                        'route' => 'admin/citys/view',
                        'data' => array('id' => 'id'),
                    ),
                ),
                'CatItem' => array(

                    'registeredOnly' => false,
                    'useCaptcha' => false,
                    'allowSubcommenting' => false,
                    //config for create link to view model page(page with comments)
                    'pageUrl' => array(
                        'route' => 'admin/citys/view',
                        'data' => array('item' => 'id'),
                    ),
                ),
            ),

//            'userConfig'=>array(
//                'class' => 'User',
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
            'returnUrl' => array('/begemot'),
        ),

        // uncomment the following to enable URLs in path-format

        'urlManager' => array(

            'urlFormat' => 'path',
            'showScriptName' => false,
            'caseSensitive' => true,
            'urlSuffix' => '.html',
            'rules' => array(


                '/admin' => '/begemot',
                //модуль gallery
                '/photo' => array('gallery/siteGallery/index'),
                '/photo/<title:[\w-]+>_<id:[\w-]+>' => array('gallery/siteGallery/viewGallery'),
                '/contact' => '/site/contact/',
                //модуль pages
                array('class' => 'application.modules.pages.components.PageUrlRule'),
                //модуль catalog
                '/catalog' => '/catalog/site/',
                'catalog/<title:[\w-]+>_<catId:\d+>' => 'catalog/site/RCategoryView',
                'catalog/<catName:[\w-]+>_<catId:\d+>' => 'catalog/site/RCategoryView',
                'catalog/<title:[\w-]+>_<catId:\d+>/<itemName:[\w-\"\'\.\"]+>_<item:\d+>' => 'catalog/site/itemView',
                'promo_<promoId:\d+>' => 'catalog/site/promoView',
                //модуль post
                'allPosts' => 'post/site/allPosts',
//                'posts' => 'post/site/tagIndex',
                'posts/<title:[\w-\.]+>_<id:\d+>' => 'post/site/tagIndex',
                'postsView/<title:[\w-_.]+>_<id:\d+>' => 'post/site/view',

                'company/<title:[\w-]+>_<departId:\d+>' => 'company/site/depart',
                'employee/<title:[\w-]+>_<empId:\d+>' => 'company/site/emp',
                'employes' => 'company/site/employes',

                'priceList' => 'priceList/site/index',

                'reviews' => 'reviews/site/reviewIndex',

                'index' => '/',
                'contacts' => '/site/contact',
                'katalog' => '/site/ModelsAndPrices',
                'moto' => '/catalog/site/RCategoryView/catId/72/V_nalichii',
                'video' => '/site/video',
                'api/<module:\w+>/<controller:\w+>' => ['<module>/<controller>/REST.GET', 'verb' => 'GET'],
                'api/<module:\w+>/<controller:\w+>/<id:\w*>' => ['<module>/<controller>/REST.GET', 'verb' => 'GET'],
                'api/<module:\w+>/<controller:\w+>/<id:\w*>/<param1:\w*>' => ['<module>/<controller>/REST.GET', 'verb' => 'GET'],
                'api/<module:\w+>/<controller:\w+>/<id:\w*>/<param1:\w*>/<param2:\w*>' => ['<module>/<controller>/REST.GET', 'verb' => 'GET'],
                ['<module>/<controller>/REST.PUT', 'pattern' => 'api/<module:\w+>/<controller:\w+>/<id:\w*>', 'verb' => 'PUT'],
                ['<module>/<controller>/REST.PUT', 'pattern' => 'api/<module:\w+>/<controller:\w+>/<id:\w*>/<param1:\w*>', 'verb' => 'PUT'],
                ['<module>/<controller>/REST.PUT', 'pattern' => 'api/<module:\w+>/<controller:\w*>/<id:\w*>/<param1:\w*>/<param2:\w*>', 'verb' => 'PUT'],
                ['<module>/<controller>/REST.DELETE', 'pattern' => 'api/<module:\w+>/<controller:\w+>/<id:\w*>', 'verb' => 'DELETE'],
                ['<module>/<controller>/REST.DELETE', 'pattern' => 'api/<module:\w+>/<controller:\w+>/<id:\w*>/<param1:\w*>', 'verb' => 'DELETE'],
                ['<module>/<controller>/REST.DELETE', 'pattern' => 'api/<module:\w+>/<controller:\w+>/<id:\w*>/<param1:\w*>/<param2:\w*>', 'verb' => 'DELETE'],
                ['<module>/<controller>/REST.POST', 'pattern' => 'api/<module:\w+>/<controller:\w+>', 'verb' => 'POST'],
                ['<module>/<controller>/REST.POST', 'pattern' => 'api/<module:\w+>/<controller:\w+>/<id:\w+>', 'verb' => 'POST'],
                ['<module>/<controller>/REST.POST', 'pattern' => 'api/<module:\w+>/<controller:\w+>/<id:\w*>/<param1:\w*>', 'verb' => 'POST'],
                ['<module>/<controller>/REST.POST', 'pattern' => 'api/<module:\w+>/<controller:\w+>/<id:\w*>/<param1:\w*>/<param2:\w*>', 'verb' => 'POST'],
                ['<module>/<controller>/REST.OPTIONS', 'pattern' => 'api/<module:\w+>/<controller:\w+>', 'verb' => 'OPTIONS'],
                ['<module>/<controller>/REST.OPTIONS', 'pattern' => 'api/<module:\w+>/<controller:\w+>/<id:\w+>', 'verb' => 'OPTIONS'],
                ['<module>/<controller>/REST.OPTIONS', 'pattern' => 'api/<module:\w+>/<controller:\w+>/<id:\w*>/<param1:\w*>', 'verb' => 'OPTIONS'],
                ['<module>/<controller>/REST.OPTIONS', 'pattern' => 'api/<module:\w+>/<controller:\w+>/<id:\w*>/<param1:\w*>/<param2:\w*>', 'verb' => 'OPTIONS'],


//                '<controller:\w+>/<id:\d+>' => '<controller>/view',
//                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
//                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',


            )

        ),



//        'errorHandler' => array(
//            // use 'site/error' action to display errors
//            'errorAction' => 'site/error',
//        ),
        'authManager' => array(
            // Path to SDbAuthManager in srbac module if you want to use case insensitive
            //access checking (or CDbAuthManager for case sensitive access checking)
            'class' => 'application.modules.srbac.components.SDbAuthManager',
            // The database component used
            'connectionID' => 'db',
            // The itemTable name (default:authitem)
            'itemTable' => 'authItem',
            // The assignmentTable name (default:authassignment)
            'assignmentTable' => 'authAssignment',
            // The itemChildTable name (default:authitemchild)
            'itemChildTable' => 'authItemChild',
        ),
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'error, warning',
                ),
                array(
                    'class' => 'CFileLogRoute',
                    'logFile' => 'cronLog.log',
                    'maxLogFiles' => 2,
                    'categories' => 'cron'
                ),
                array(
                    'class' => 'CFileLogRoute',
                    'logFile' => 'webParser.log',
                    'maxLogFiles' => 2,
                    'categories' => 'webParser'
                ),

            ),
        ),
//        'request' => array(
//                        'baseUrl' => 'http://www.buggy-motor.ru',
//                    ),
        'cache' => array(
            'class' => 'system.caching.CFileCache',
        ),

    ),
    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
    'params' => array(
        // this is used in contact page
        'adminEmail' => 'scott2to@gmail.com',
        'RestfullYii' => [
            'req.auth.user'=>function($application_id, $username, $password) {
                return true;
            },
        ]
    ),
);
