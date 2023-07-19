<?php
return [
    'import'=>[
        'application.modules.user.models.*',
        'application.modules.user.components.*',
    ],
    'modules' => [
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
    ],
    'components' => [
        'user' => array(
            // enable cookie-based authentication
            'class' => 'application.modules.user.components.WebUser',
            'allowAutoLogin' => true,
            'loginUrl' => array('/user/login'),
            'returnUrl' => array('/begemot'),
        ),
    ]
];