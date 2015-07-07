<?php

define('WX_DEBUG', false);

define('RULE_TYPE_MATCH', 1);
define('RULE_TYPE_CONTAIN', 2);
define('RULE_TYPE_REGULAR', 3);
define('RULE_TYPE_TAKE', 4);



class WxController extends Controller
{
    var $request_id;
    var $userName;
    var $accountName;
    var $userID;
    var $openID;
    var $unionid;
    
    public function filters()
    {
        if(WX_DEBUG){
            return array(
            );
        }else{
            return array(
                    array(
                            'application.filters.WxAuthFilter - response',
                    )
            );
            
        }
        
    }
    
    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    
    
    public $defaultAction = 'receive';
    
    
    public function init()
    {
        parent::init();
    }

    /**
     * 收到用户发来的消息处理
     * @param 无
     * @return void
     */
    public function actionReceive()
    {
        
        
        //记录原始的请求数据
        if(WX_DEBUG){
            $message = "<xml><ToUserName><![CDATA[gh_f419197c3cc8]]></ToUserName>
<FromUserName><![CDATA[ohdH2s23MGn5EaWIVqT9B979dN5c]]></FromUserName>
<CreateTime>1423985317</CreateTime>
<MsgType><![CDATA[event]]></MsgType>
<Event><![CDATA[subscribe]]></Event>
<EventKey><![CDATA[qrscene_67537530]]></EventKey>
<Ticket><![CDATA[gQGv7zoAAAAAAAAAASxodHRwOi8vd2VpeGluLnFxLmNvbS9xL2ZFalEtdExrQ1hGdzZqcnRLbVpCAAIEZUbgVAMECAcAAA==]]></Ticket>
</xml>";
            
            $message = "<xml><ToUserName><![CDATA[gh_f419197c3cc8]]></ToUserName>
<FromUserName><![CDATA[ohdH2s23MGn5EaWIVqT9B979dN5c]]></FromUserName>
<CreateTime>1427362112</CreateTime>
<MsgType><![CDATA[event]]></MsgType>
<Event><![CDATA[CLICK]]></Event>
<EventKey><![CDATA[APP_NEW_LIST]]></EventKey>
</xml>";
            
        }else{
            $message = $GLOBALS["HTTP_RAW_POST_DATA"];
        }
        
        $model_log = new LogApiRequest();
        $model_log->opt_id = Yii::app()->user->opt_id;
        $model_log->request_time = date("Y-m-d H:i:s");
        $model_log->data = $message;
        $model_log->save();
        
        if (!empty($message)){
            $obj = simplexml_load_string($message, 'SimpleXMLElement', LIBXML_NOCDATA);
            if($obj instanceof SimpleXMLElement) {

                $this->doProcess($obj);
            }
        }
        
    }
    
    /**
     * 通过用户传递的信息进行处理
     * @param $obj 微信穿过来的XML数据
     * @return void
     */
    public function doProcess($obj){
        
        $this->userName = strval($obj->FromUserName);
        $this->accountName = strval($obj->ToUserName);
        $this->openID = strval($obj->FromUserName);
        
        //检查用户是否已经在这个账户下，如果没有则插入到库里,返回库里的ID
        $this->userID = CommonFunc::checkUser($this->openID);
        
        //判断用户发过来的信息类型
        //不同的类型放到不同的数据表里
        switch(strtolower($obj->MsgType)){
            case 'text':
                $this->processResponse($obj->Content, 'text');
                
                break;
            case 'image':
                break;
            case 'link':
                break;
            case 'voice':
                break;
            case 'location':
                break;
            case 'event':
                $event = strtolower($obj->Event);
                if( $event == 'click' || $event == 'view'){
                    //自定义菜单的事件太多，需要单独存放，以后有了自定义菜单完善这里
                    $model = new MsgReceiveEventMenu();
                }else{
                    //
                    $model = new MsgReceiveEvent();
                }
                $model->type = $event;
                if( isset($obj->EventKey) ){
                    $_a = explode('_', $obj->EventKey);
                    if(count($_a) > 1){
                        $model->eventKey = intval($_a[1]);
                    }else{
                        $model->eventKey = $obj->EventKey;
                    }
                    
                }

                $model->opt_id = Yii::app()->user->opt_id;
                $model->userID = $this->userID;
                $model->createTime = date('Y-m-d H:i:s', intval($obj->CreateTime));
                
                
                $model->save();
                
                //对用户做响应
                $this->processResponse($model, strtolower($obj->MsgType));
                
                
                break;
            default:
                return;
            
        }
               
        
    }
    
    
    
    /**
     * 根据用户发来的信息进行相应的处理
     * @param $model 接收的消息数据
     * @param $type 消息类型
     * @return void
     */
    public function processResponse($model_rev,$type)
    {

        
        switch($type){
            case 'event':
                if($model_rev->type == 'subscribe' || $model_rev->type == 'scan'){
                    if($model_rev->eventKey >= 1000000 && $model_rev->eventKey <= 4294967296){
                        //通过扫码登录
                        $this->userLogin($model_rev->eventKey);
                    }else{
                        //如果只是关注
                        if($model_rev->type == 'subscribe' ){
                            $this->sendMsg('欢迎关注App哥伦部,点击菜单栏中的 <a href="http://www.appgrub.com">进入网站</a> 随时随地发现好玩的App！也可访问官方网站: www.appgrub.com!');
                        }
                    }
                }
                
                
                if($model_rev->type == 'click'){
                    $this->sendAppList($model_rev->eventKey);
                    
                }
                break;
                
            case 'text':
                
                $this->sendAppList('SEARCH', $model_rev);
                
                
                
                break;
        }
        
    }

    public function userLogin($key)
    {
        $status = Yii::app()->cache->get('qr' . $key);
        if(!$status){
            $this->sendMsg('登录失败，请刷新页面后重试');
        }else{
            $status = unserialize($status);
            $status['login'] = 2;
            $status['openID'] = $this->openID;
            Yii::app()->cache->set('qr' . $key, serialize($status), 10);
            $this->sendMsg('您已登录成功');
            file_get_contents('http://appgrub.com:443?qrcode='.$key.'&msg=1');
        }
    }
    
    public function sendAppList($type, $txt){
        switch($type) {
            case 'APP_NEW_LIST':
                $appsInfo = AppInfoList::getData(0, 0, '', 0, 0, 10);
                $title = "App哥伦部最新应用";
                break;
            case 'SEARCH':
                $appsInfo = AppInfoList::getData(0, 0, $txt, 0, 0, 10);
                $title = "App搜索结果";
                break;
        }
        
        
        $aData = array();
        foreach($appsInfo['data'] as $m){
            $_ = "[强]" . $m['count'];
            $_ .= ' <a href="http://appgrub.com/produce/index/'.$m['Id'].'">' . $m['AppName'] . "</a>\n";
        
            if( !empty($m['Remarks']) ){
                $content = strip_tags($m['Remarks']);
            }else{
                $content = strip_tags($m['AppInfo']);
            }
            $_ .= mb_substr($content,0, 30, "utf-8");
            $aData[] = $_;
        }
        
        if( count($aData) == 0){
            $msg = '暂无符合条件的App, 请点击 <a href="http://www.appgrub.com">进入网站</a> 发现更多App';
        }else{
            $msg = implode("\n\n", $aData);
        }
        $this->sendMsg( $title . "\n-------------------------\n". $msg);
        break;
        
    }
    /**
     * 向用户发送信息
     * @param $content 发送内容
     * @param $type 发送类型
     * @return void
     */
    public function sendMsg($content, $type = 'text')
    {
        switch($type){
            case 'text':
                $textTpl = "<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[text]]></MsgType>
                            <Content><![CDATA[%s]]></Content>
                            <FuncFlag>0</FuncFlag>
                    </xml>";
                $resultStr = sprintf($textTpl, $this->userName, $this->accountName, time(), $content);
                break;
        }
        echo $resultStr;
        
        //备份记录
        $model = new LogApiResponse();
        $model->opt_id = Yii::app()->user->opt_id;
        $model->userID = $this->userID;
        $model->response_time = date('Y-m-d H:i:s');
        $model->response_type = $type;
        $model->data = 'login';
        $model->save();
        
    }
    
        
    public function actionTest(){
        
        return;
        $request = Yii::app()->curl->run("http://clk.optaim.com/event.ng/Type=click&FlightID=201309&TargetID=sohu&Values=4960caa5,d64a93f7,0bd7d879,040f2666&AdID=1023788");
        var_dump($request->getInfo());
        
        $at = WxApi::getAccessToken(1);
        
        
        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=$at";
        $data = '
{
     "button":[
     {
          "name":"车型选择",
          "sub_button":[
           {
                "type":"view",
                "name":"全部车型",
                "url":"http://weixin.optaim.com/tpl/allcars.html"
            },
            {
                "type":"view",
                "name":"尊选二手车",
                "url":"http://weixin.optaim.com/tpl/scar.html"
            },
            {
                "type":"view",
                "name":"照片展示",
                "url":"http://weixin.optaim.com/tpl/pics.html"
            }
            ]
      },
      {
           "name":"在线服务",
          "sub_button":[
           {
                "type":"view",
                "name":"车主关怀",
                "url":"http://weixin.optaim.com/tpl/care.html"
            },
            {
                "type":"view",
                "name":"预约保养",
                "url":"http://weixin.optaim.com/tpl/baoyang.html"
            },
            {
                "type":"click",
                "name":"预约试驾",
                "key":"14_1_6"
            }]
      },
      {
           "type":"view",
            "name":"进入官网",
            "url":"http://weixin.optaim.com/tpl/auto.html"
     }]
 }
';

        $a = Yii::app()->curl->run($url, $data);
        
        var_dump($a->getBody());
        /*
        $abc = array('app_id'=>'1922874','app_secret'=>'40eae2780e80243bf0324b4557ee2fd8');
        $request = Yii::app()->curl->run("http://adp.optaim.com/api/auth/access_token", $abc);
    
        $date = $request->getBody();
        $a = json_decode($date, true);

        
        $abc = array('date'=>'2014-04-08','access_token'=>'4bf4ea6f23ef1df5df7f18878237752e9dac199ca42dced5c446ccf25eb5919a4ee19d228e7c844e26390a35b25d19d1789013c60cd2f6508f39413025782322');
        $request = Yii::app()->curl->run("http://adp.optaim.com/api/report/hour", $abc);
        var_dump($request->getBody());
    */

    }
    
}