<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
    public $defaultAction = 'show';
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='/layouts/main';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();

	public $timestamp;
	public $nonceStr = 'JJIOJkfawejaa';
	public $signature;
	public $request_url;
	
	/**
	 * @param $message
	 * @param string $title
	 */
	public function PopMsg($message, $title = '提示')
	{
		$id = rand(1, 999999);
		Yii::app()->user->setFlash($id, array('title' => $title, 'content' => $message));
		//var_dump($a);exit;
	}
	
	public function init()
	{
	    parent::init();
	    Yii::app()->user->opt_id = time() . rand(10000, 99999);
	    
	    $ua = $_SERVER['HTTP_USER_AGENT'];
	    if(stripos($ua, 'Mobile')>0){
	        
	        Yii::app()->theme = "mobile";
	        //$this->setApiSignature();
	        
	    }
        //Yii::app()->theme = "mobile";
        
	}
	
	public function setApiSignature(){
	    $this->timestamp = time();
	     
	    $pathInfo = Yii::app()->request->getPathInfo();
	    $this->request_url = Yii::app()->request->getHostInfo() . ((empty($pathInfo))?'':'/'.$pathInfo);
	     
	    $ticket = WeixinApi::getJSApiTk();
	    
	    $str = sprintf("jsapi_ticket=%s&noncestr=%s&timestamp=%s&url=%s",$ticket,$this->nonceStr,$this->timestamp,$this->request_url);
	    
	    $this->signature = sha1($str);
	    
	}

}