<?php
/**
 * extends CWebUser to change the login url and give other properties to the user object
 * now every user has a group, a groupName, accessRules and a home
 * @author Nicola Puddu
 * @package userGroups
 *
 */
define('ROLE_ID_FRONT_ACCOUNT', 20);
define('ROLE_ID_FRONT_MANAGER', 21);
define('ROLE_ID_FRONT_OPT', 22);
define('ROLE_ID_FRONT_READ', 23);

define('ROLE_ID_FRONT_SYS',1);
define('ROLE_ID_FRONT_AG',4);
define('ROLE_ID_FRONT_CUSTOM',3);
define('ROLE_ID_FRONT_DEV_R',5);
define('ROLE_ID_FRONT_DEV_RW',6);


class WebUser extends CWebUser
{
	/**
	 * @var array containing the url of the login page
	 */
	public $loginUrl = '/user/login';
    public $opt_id;
    public $logNotice;
    public $beforeSaveData;

    public $allowAutoLogin=true;
    
    
	public function afterLogin($fromCookie)
	{
	    if($fromCookie){
	        Yii::app()->user = &$this;
	    }
        //本地测试id
//	    $oUser = User::model()->findByPk(6);
//        var_dump($fromCookie);
//        echo 'ID:';
//        var_dump($this);exit;
	    $oUser = User::model()->findByPk($this->id);
	    
	    $this->username = $oUser->UserName;

	    $this->userurl = $oUser->Icon;
	    
        $this->openid = $oUser->Openid;
        
        $this->lastLoginTime = $oUser->LastLoginTime;

        $oUser->LastLoginTime = date('Y-m-d H:i', time());

        $oUser->save();

	}

		
	public function getNjsid()
	{
	    return $this->getState('__njsid');
	}
	
	public function setNjsid($value)
	{
	    return $this->setState('__njsid', $value);
	
	}
	
	
    public function getOpenid()
    {
        return $this->getState('__openid');
    }
    
    public function setOpenid($value)
    {
        return $this->setState('__openid', $value);
        
    }
    
	public function getQrcodeid()
	{
	    return $this->getState('__qrcodeid');
	}
	
	public function setQrcodeid($value)
	{
	    $this->setState('__qrcodeid',$value);
	}
	
	
    public function getUsername()
    {
        return $this->getState('__username');
    }
	
	public function setUsername($value)
	{
		$this->setState('__username',$value);	
	}
		
	public function getUserurl()
	{
		return $this->getState('__userurl');
	}
	
	public function setUserurl($value)
	{
		$this->setState('__userurl',$value);
	}
	
	public function setLastLoginTime($value)
    {
        $this->setState('__lastLoginTime',$value);
    }

    public function getLastLoginTime()
    {
        return $this->getState('__lastLoginTime');
    }
	/**
	 * action performed when to access a specific page the login is required
	 */
	public function loginRequired()
	{
	    $app=Yii::app();
	    $request=$app->getRequest();
	
	    if(!$request->getIsAjaxRequest())
	        $this->setReturnUrl($request->getUrl());
	
	    if(($url=$this->loginUrl)!==null)
	    {
	        if(is_array($url))
	        {
	            $route=isset($url[0]) ? $url[0] : $app->defaultController;
	            $url=$app->createUrl($route,array_splice($url,1));
	        }
	        
	        if ($request->getIsAjaxRequest()){
	        	$url .= '?_isAjax=1';
	        	echo new ReturnInfo(ERROR_LOGIN_REQUIRE, '请登录后再做此操作');
	        	Yii::app()->end();
	        }else{
	        	$request->redirect($url);
	        }
	    }
	    else
	        throw new CHttpException(EXCEPTION_ILLEGAL_OPERATION, Yii::t('userGroupsModule.general','Login Required'));
	}
}
