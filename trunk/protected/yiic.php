<?php

// change the following paths if necessary
date_default_timezone_set('Asia/Shanghai');

$env = 'dev';
if ( is_file( dirname(__FILE__).'/../../../online' ) ){
    $env = 'online';
}elseif( is_file( dirname(__FILE__).'/../../../sandbox' ) ){
    $env = 'sandbox';
}

require_once(dirname(__FILE__) . '/../../env/'.$env.'/config.php');
$config=dirname(__FILE__).'/../../env/'.$env.'/console.php';

//宏变量定义目录
require_once(dirname(__FILE__).'/config/MacroVars.php');


define('CONFIG_SET_BASE_PATH', dirname(__FILE__) );
require_once($yiic);
