<?php

// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
return array(
    'basePath'=>CONFIG_SET_BASE_PATH,
    'name'=>'My Console Application',
    'id' => 'appgrub_ol',
    // preloading 'log' component
    'preload'=>array('log'),

    
    'import'=>array(
            'application.components.*',
            'application.models.*',
    ),
    
    // application components
    'components'=>array(
        'db'=>array(
                'connectionString' => 'mysql:host=10.4.9.10;dbname=appgrub',
                'emulatePrepare' => true,
                'username' => 'appgrub',
                'password' => 'ag@opt.ca33',
                'charset' => 'utf8',
                'enableParamLogging' => true,
        ),
         
        // uncomment the following to use a MySQL database
        /*
        'db'=>array(
            'connectionString' => 'mysql:host=;dbname=testdrive',
            'emulatePrepare' => true,
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
        ),
        */
        'log'=>array(
            'class'=>'CLogRouter',
            'routes'=>array(
                array(
                    'class'=>'CFileLogRoute',
                    'levels'=>'error, warning',
                ),
            ),
        ),
        'curl' =>array(
                'class' => 'application.extensions.curl.Curl',
        ),
        
        //配置redis缓存
        'cache'=>array(
                'class'=>'ext.redis.CRedisCache',
                'servers'=>array(
                        array(
                                'host'=>'10.4.3.201',
                                'port'=>6379,
                        ),
                ),
        ),
        
    ),
    'timeZone' => 'PRC',
);
