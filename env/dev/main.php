<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
    'basePath'=> CONFIG_SET_BASE_PATH,
    'name'=>'My Web Application',
    'id' => 'appgrub',
    'runtimePath' => CONFIG_SET_RUNTIME_PATH,
    // preloading 'log' component
    'preload'=>array('log'),

    // autoloading model and component classes
    'import'=>array(
        'application.models.*',
        'application.components.*',
        'application.modules.srbac.controllers.SBaseController',
        'application.extensions.*',
        'application.extensions.phpqrcode.*',
    ),

    'modules'=>array(
        // uncomment the following to enable the Gii tool
        'admin',
        'api',
        'gii'=>array(
            'class'=>'system.gii.GiiModule',
            'password'=>'123',
            // If removed, Gii defaults to localhost only. Edit carefully to taste.
            'ipFilters'=>array('127.0.0.1','::1'),
        ),
        
    ),

    // application components
    'components'=>array(
        'user'=>array(
              'class' => 'WebUser',
              'stateKeyPrefix'=>'appgrub_',
            ),  
            
        // uncomment the following to enable URLs in path-format
        
        'urlManager'=>array(
            'urlFormat'=>'path',
            'showScriptName'=>false,//注意false不要用引号括上
            'rules'=>array(
                '<controller:\w+>/<id:\d+>'=>'<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
                '<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
            ),
        ),
        
        'curl' =>array(
            'class' => 'application.extensions.curl.Curl',
        ),

      
        // uncomment the following to use a MySQL database
        
        'db'=>array(
            'connectionString' => 'mysql:host=localhost;dbname=appgrub',
            'emulatePrepare' => true,
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
            'enableParamLogging' => true,
        ),
        
        'errorHandler'=>array(
            // use 'site/error' action to display errors
            'errorAction'=>'error/error',
        ),
     'log'=>array(
            'class'=>'CLogRouter',
            'routes'=>array(
                array(
                    'class'=>'CFileLogRoute',
                    'logPath'=>CONFIG_LOGS_PATH,
                    'levels'=>'error, warning, trace, profile, info',
                    'categories'=>'system.*',   
                ),
                array(
                        'class'=>'CFileLogRoute',
                        'logPath'=>CONFIG_LOGS_PATH,
                        'levels'=>'error, warning, trace, profile, info',
                        'categories'=>'exception.*',
                ),
                array(
                        'class'=>'CFileLogRoute',
                        'logPath'=>CONFIG_LOGS_PATH,
                        'levels'=>'error',
                        'categories'=>'system.*',
                        'logFile'=>'error.log',
                ),
                array(
                        'class'=>'CFileLogRoute',
                        'logPath'=>CONFIG_LOGS_PATH,
                        'levels'=>'error,info,trace',
                        'categories'=>'system.db.*',
                        'logFile'=>'db.log',
                ),                   
                array(
                        'class'=>'CFileLogRoute',
                        'logPath'=>CONFIG_LOGS_PATH,
                        'levels'=>'error,info,trace',
                        'categories'=>'system.api.weixin',
                        'logFile'=>'weixin.log',
                ),
                
            ),
        ),
            

        //配置redis缓存
        'cache'=>array(
        'class'=>'ext.redis.CRedisCache',
        'servers'=>array(
            array(
                    'host'=>'127.0.0.1',
                    'port'=>6379,
                ),
            ),
        ),
        //配置PRedisCacheHttpSession
//         'session' => array(
//             'class' => 'ext.PRedisCacheHttpSession.PRedisCacheHttpSession',
//             'database' => 9, 
//         ),
    ),

    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
    'params'=>array(
        // this is used in contact page
        'adminEmail'=>'webmaster@example.com',
    ),

    //默认的控制器和方法
    'name'=>'list',
    'defaultController'=>'app',

    // 'urlManager'=>array(
    //     'urlFormat'=>'path',
    //     'showScriptName'=>false,
    //     ),
    'timeZone' => 'PRC',


);