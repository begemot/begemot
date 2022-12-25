<?php
return [
    'modules' => [
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
    ],
    'components'=>[
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
    ]

];