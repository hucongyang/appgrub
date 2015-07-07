<?php 

define("WX_ACCESSTOKEN_URL", 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=[APPID]&secret=[SECRET]');
define("WX_UPLOAD_MEDIA_URL", 'http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token=[TOKEN]&type=[TYPE]');

define("WX_USER_INFO_URL", 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=[TOKEN]&openid=[OPENID]&lang=zh_CN');

define("WX_USER_LIST", 'https://api.weixin.qq.com/cgi-bin/user/get?access_token=[TOKEN]&next_openid=[NEXTID]');


define('WX_GET_QRCODE', 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=[TOKEN]');

define('WX_QRCODE_URL', 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=[TICKET]');

define('WX_MENU_URL', 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=[TOKEN]');

define('WX_AT_URL_MOBILE', 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=[APPID]&secret=[SECRET]&code=[CODE]&grant_type=authorization_code
');

define('WX_USER_INFO_MOBILE', 'https://api.weixin.qq.com/sns/userinfo?access_token=[TOKEN]&openid=[OPENID]&lang=zh_CN');

define('WX_GET_JSAPI_URL','https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=[TOKEN]&type=jsapi');

class WeixinApi{
    
    /**
     * 根据账户ID获得access token 如果过期 再重新获取一下
     */
    static function getAccessToken()
    {
        $token_info = Yii::app()->cache->get('wx_token');
        if($token_info){
            $token = unserialize($token_info);
            return $token['token'];
        }
                

        $url =  str_replace('[APPID]', 'wx1ac3d9fb295a53b1', WX_ACCESSTOKEN_URL);
        $url = str_replace('[SECRET]', 'bc219ed3e76b9da02cf1f82f635f2704', $url);

        $request = Yii::app()->curl->run($url);
        $aResponse = WeixinApi::parseResponse($request, __FILE__, __LINE__);
        
        
        $token = array(
                'token' => $aResponse['access_token'],
                'expire' => time() + $aResponse['expires_in']
        );
        Yii::app()->cache->set('wx_token', serialize($token), $aResponse['expires_in']);

        return $token['token'];
         
    }
    
    
    /**
     * 根据账户ID获得access token 如果过期 再重新获取一下
     * @param $accountID 账号ID
     * @param $filePath  文件绝对路径
     * @return $accsess_token token
     */
    static function uploadWxMedia($accountID, $type, $filePath)
    {
        //允许的类型及大小（KB）
        $access_media = array('image' => 128, 'voice' => 256, 'video' => 1024, 'thumb' => 64) ;
         
        switch(true){
            case !isset($access_media[ $type ]):
                throw new THttpException("media type not allow");
                break;
            case !file_exists($filePath):
                throw new THttpException("file not exists");
                break;
            case (filesize($filePath) >=  $access_media[ $type ] * 1024):
                throw new THttpException("file is too large");
                break;
        }
         
         
        $token = WxApi::getAccessToken($accountID);
        $postData = array('media' => '@'. $filePath);
         
        $url = str_replace('[TOKEN]', $token, WX_UPLOAD_MEDIA_URL);
        $url = str_replace('[TYPE]', $type, $url);
        
        $request = Yii::app()->curl->run( $url, $postData );
        
        $aResponse = WeixinApi::parseResponse($request, __FILE__, __LINE__);
        
         
        $model_media = new UploadMediaFile();
        $model_media->accountid = $accountID;
        $model_media->mediaid = $aResponse['media_id'];
        $model_media->create_time = $aResponse['created_at'];
        $model_media->filepath = $filePath;
        $model_media->type = $type;
        $model_media->expire_time = $aResponse['created_at'] + 3600 * 24 * 3;
        $model_media->save();
        return $model_media->ID;
    }
    
    
    
    /**
     * 获取用户详细信息
     * @param $token access token
     * @param $openid  用户ID
     * @return $aResponse 用户数据
     */
    static function getUserInfo($openid){
        
        $url = str_replace('[TOKEN]', WeixinApi::getAccessToken(), WX_USER_INFO_URL);
        $url = str_replace('[OPENID]', $openid, $url);
        
        
        $request = Yii::app()->curl->run( $url );
        
        return WeixinApi::parseResponse($request, __FILE__, __LINE__);
        ;
        
        
        
    }
    
    
    
    /**
     * 获得用户列表
     * @param $token 账号ID
     * @param $nextID  下一个openid
     * @return $aResponse 用户列表
     */
    static function getAccountFans($token, $nextID=''){
        
        $url = str_replace('[TOKEN]', $token, WX_USER_LIST);
        $url = str_replace('[NEXTID]', $nextID, $url);
    
    
        $request = Yii::app()->curl->run( $url );
        return WeixinApi::parseResponse($request, __FILE__, __LINE__);
        ;
    
    
    }
    
    
    static function getQrCode(){
       
        
        $token = WeixinApi::getAccessToken();
        $url = str_replace('[TOKEN]', $token, WX_GET_QRCODE);
        $post = array(
             "expire_seconds" => 1800, 
             "action_name" => "QR_SCENE", 
             "action_info" => array(
                     "scene" => array("scene_id" => Yii::app()->user->njsid)
              )
        );
        $request = Yii::app()->curl->run( $url, json_encode($post));
        $aResponse = WeixinApi::parseResponse($request, __FILE__, __LINE__);
        
        $status = array(
                'ticket' => $aResponse['ticket'],
                'login' => 1//未登录
        );
        
        Yii::app()->cache->set('qr' . Yii::app()->user->njsid, serialize($status), 1600);
        
        return str_replace('[TICKET]', $aResponse['ticket'], WX_QRCODE_URL);
        
    }
    
    static function setMenu($menu){
        
        $url = str_replace('[TOKEN]', WeixinApi::getAccessToken(), WX_MENU_URL);
                
        $request = Yii::app()->curl->run($url, $menu);
        
        WeixinApi::parseResponse($request, __FILE__, __LINE__);
        
        
    }
    
    static function getResonseUserInfo($code){
        
        $url =  str_replace('[CODE]', $code, WX_AT_URL_MOBILE);
        $url =  str_replace('[APPID]', 'wx1ac3d9fb295a53b1', $url);
        $url = str_replace('[SECRET]', 'bc219ed3e76b9da02cf1f82f635f2704', $url);
        
        $request = Yii::app()->curl->run($url);
        $aResponse = WeixinApi::parseResponse($request, __FILE__, __LINE__); 


        if( isset($_SESSION['from_btn_view']) && $_SESSION['from_btn_view']){
            //不需要用户授权，直接登录
            return WeixinApi::getUserInfo($aResponse['openid']);
        }
        
        
        $url =  str_replace('[TOKEN]', $aResponse['access_token'], WX_USER_INFO_MOBILE);
        $url =  str_replace('[OPENID]', $aResponse['openid'], $url);
        
        $request = Yii::app()->curl->run($url);
        $aResponse = WeixinApi::parseResponse($request, __FILE__, __LINE__);
        
        $aUser = array(
                'nickname' => $aResponse['nickname'],
                'headimgurl' => $aResponse['headimgurl'],
                'unionid' => $aResponse['unionid'],
                'openid' => $aResponse['openid'],
        );
        
        return $aUser;
    }
    
    static function parseResponse($request, $file, $line){
        if($request->getErrors()->hasError){
            Yii::log($file . $line . '  error:' . $request->getErrors()->message, 'error', 'system.api.weixin');
            //throw new THttpException(" request error");
        }
        
        $aResponse = json_decode($request->getBody(), true);
        if( isset($aResponse['errcode']) &&  $aResponse['errcode'] > 0){
            Yii::log($file . $line . ' error : ' . $aResponse['errcode'] . $aResponse['errmsg'], 'error', 'system.api.weixin');
            if( $aResponse['errcode'] == 40001 ) {
                Yii::app()->cache->delete('wx_token');
                
            }
            throw new THttpException("weixin api error");
        }
        
        return $aResponse;
        
    }
    
    static function getJSApiTk(){

        $ticket = Yii::app()->cache->get('wx_jsapi_ticket');

        if($ticket){

            $ticket = unserialize($ticket);
            return $ticket['ticket'];
        }

        $url = str_replace('[TOKEN]', WeixinApi::getAccessToken(), WX_GET_JSAPI_URL);

        $request = Yii::app()->curl->run( $url );
        $aResponse = WeixinApi::parseResponse($request, __FILE__, __LINE__);
        
        $ticket = array(
                'ticket' => $aResponse['ticket'],
                'expire' => time() + $aResponse['expires_in']
        );
        Yii::app()->cache->set('wx_jsapi_ticket', serialize($ticket), $aResponse['expires_in']);
        
        return $ticket['ticket'];
    }
    
}

