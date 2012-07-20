<?php

Yii::setPathOfAlias('lib', realpath(dirname(__FILE__) . '/../../lib'));
Yii::setPathOfAlias('bootstrap', dirname(__FILE__) . '/../../lib/bootstrap');

$params = require('params.php');
$config = array(
    'onBeginRequest' => array('ModuleUrlManager', 'collectRules'),
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => $params['appName'],
    'language' => 'ru',
    'preload' => array('log'),
    'import' => array(
        'application.models.*',
        'application.models.forms.*',
        'application.components.*',
        'application.helpers.*',
    ),
    'modules' => array(
        // uncomment the following to enable the Gii tool

//		'gii'=>array(
//			'class'=>'system.gii.GiiModule',
//			'password'=>'123',
//			// If removed, Gii defaults to localhost only. Edit carefully to taste.
//			'ipFilters'=>array('192.168.*.*','::1', '127.0.0.1', '87.251.171.14'),
//		),
    ),
    // application components
    'components' => array(
        'session' => array(
            'class' => 'CDbHttpSession',
            'connectionID' => 'db',
            'autoCreateSessionTable' => false,
            'sessionTableName' => 'yii_session',
            //'sessionName' => 'Session',
            //'cookieMode' => 'none',
        ),
        'user' => array(
            // enable cookie-based authentication
            'allowAutoLogin' => true,
            'loginUrl' => array('site/login'),
        ),
        'clientScript' => array(
            'scriptMap' => array(
                'jquery.js' => '/js/jquery-1.8.3.js',
                'jquery.min.js' => '/js/jquery-1.8.3.min.js',
            )
        ),
        'urlManager' => array(
            'urlFormat' => 'path',
            'urlSuffix' => '/',
            'showScriptName' => false,
        ),
        'db' => array(
            'connectionString' => 'mysql:host=' . $params['dbHost'] . ';dbname=' . $params['dbName'],
            'emulatePrepare' => true,
            'username' => $params['dbLogin'],
            'password' => $params['dbPassword'],
            'charset' => 'utf8',
            'enableProfiling' => YII_DEBUG,
            'enableParamLogging' => YII_DEBUG,
        ),
        'authManager' => array(
            'class' => 'CDbAuthManager',
            'connectionID' => 'db',
        ),
        'fs' => array(
            'class' => 'FileSystem',
            'nestedFolders' => 1,
        ),
        'viewRenderer' => array(
            'class' => 'lib.twig-renderer.ETwigViewRenderer',
            'twigPathAlias' => 'lib.twig.lib.Twig',
            'options' => array(
                'autoescape' => true,
            ),
            'functions' => array(
                'widget' => array(
                    0 => 'TwigFunctions::widget',
                    1 => array('is_safe' => array('html')),
                ),
                'const' => 'TwigFunctions::constGet',
                'static' => 'TwigFunctions::staticCall',
                'settings' => array(
                    0 => 'TwigFunctions::settings',
                    1 => array('is_safe' => array('all')),
                ),
            ),
        ),
        'bootstrap' => array(
            'class' => 'lib.bootstrap.components.Bootstrap',
            //'responsiveCss' => true,
        ),
        'errorHandler' => array(
            'errorAction' => 'site/error',
        ),
        'image' => array(
            'class' => 'ext.image.CImageComponent',
            'driver' => $params['imageDriver'],
        ),
        'log' => array(
            'class'=>'CLogRouter',

            //Comment above and uncomment bellow, to disable debug toolbar
            'routes' => array(
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'error, warning',
                ),
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'info',
                    'logFile' => 'info.log'
                ),
            ),
        ),
        'format' => array(
            'class' => 'application.components.Formatter',
        ),
    ),
    'params' => array_merge(
        $params,
        array(
            'md5Salt' => 'ThisIsMymd5Salt(*&^%$#',
        )
    ),
);
if(YII_DEBUG)
    $config['components']['log']['routes']=array(
        array(
            'class'=>'ext.yii-debug-toolbar.YiiDebugToolbarRoute',
            'ipFilters'=>array('*'),
        ));
return $config;