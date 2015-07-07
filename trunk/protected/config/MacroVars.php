<?php 

define('USER_STATUS_NORMAL', 0);	//正常
define('USER_PASSWORD_ERROR', 1);	//密码错误
define('USER_STATUS_INACTIVE', 2);	//未激活
define('USER_STATUS_DELETED', 3);	//已删除
define('USER_STATUS_NO_EXIST', 4);	//用户不存在
define('FAIL_RET', -1);

define('RET_SUC', 0);

define('RET_ERROR', -1);

define('RET_FAILURE', 2);	//已经点过了

define('ERROR_LOGIN_REQUIRE', -1);	//未登录

define('PRE_FIX', 'appgrub_');	//redis前缀

define('DOMAIN_ERROR', 6); //域名错误

define('DOMAIN_TYPE', 7);	//android地址不能为空

define('ERROR_URL', 8);		//产品官网

define('ERROR_CATEGORY', 9);	//所属分类

define('WRONGFULL_URL',10);	//域名

define('ERROR_TITLE', 11);	//产品名称

define('ERROR_EXPLAIN', 12); //描述

define('ERROR_APPURL', 13);	//APP地址

define('ERROR_ANDROID', 14);//安卓地址
