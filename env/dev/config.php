<?php
// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);
define('SANDBOX_FLAG', false);

define('YII_ENABLE_ERROR_HANDLER', false);



//upload路径
define('CONFIG_UPLOAD_BASE_PATH', dirname(__FILE__) . '/../../upload');

define('CONFIG_SET_RUNTIME_PATH', dirname(__FILE__) . '/../../runtime');

//创意上传路径
define("FILE_CREATIVE_UPLOAD_PATH", CONFIG_UPLOAD_BASE_PATH . '/../../creative');


//logs路径
define('CONFIG_LOGS_PATH', dirname(__FILE__) . '/../../logs');


//用于清除js,css缓存
define('CONFIG_VER', '2014-06-26');



//上传素材的域名，在linux下需要配置，开发机和生产机的域名不一样 需要这里配置  windows下不需要配置
define("PIC_URL","http://pic.optaim.com/"); //我这里是在本地是示意，配的是生产环境的
define("SOHU_CDN", "http://images.sohu.com/optaim/material/");
define("OPT_CDN", "http://cdn.optaim.com/material/video/");
define('SOURCE_MZ',106);
define('SOURCE_LM',107);

$yii=dirname(__FILE__).'/../../../yii/yii-1.1.7.r3135/framework/yii.php';

$yiic=dirname(__FILE__).'/../../../yii/yii-1.1.7.r3135/framework/yiic.php';
