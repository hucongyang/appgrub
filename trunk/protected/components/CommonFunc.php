<?php
Class CommonFunc {
    static function encodeURIComponent($str) {
        $revert = array('%21'=>'!', '%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')');
        return strtr(rawurlencode($str), $revert);
    }

    static function getProjectEnv() {
        $env = 'dev';
        if (is_file(Yii::app()->basePath . '/../../../online')){
            $env = 'online';
        }else if(is_file(Yii::app()->basePath . '/../../../sandbox')){
            $env = 'sandbox';
        }
        return $env;
    }

    static function setRedis($key, $type, $value) {

        $info = Yii::app()->cache->get($key);
        if(!$info){
            $aInfo = array();
        }else{
            $aInfo = unserialize($info);
        }
        if($type){
            $aInfo[$type] = $value;
        } else {
            $aInfo = $value;
        }
        Yii::app()->cache->set($key, serialize($aInfo));
        return true;
    }


    static function getRedis($key, $type = '') {
        $info = Yii::app()->cache->get($key);
        if(!$info){
            return array();
        }
        $aInfo = unserialize($info);
        return $type ? (isset($aInfo[$type]) ? $aInfo[$type] : array()) : $aInfo;
    }
    
    
    /**
     * 检测微信用户是否在这个广告主的账户下
     * @param $openID 用户的微信原始ID
     * @return void
     */
    static function checkUser($openID, $aUser = array())
    {
        if(empty($openID)){
            $openID = $aUser['openid'];
            $_aUser  = WeixinApi::getUserInfo($openID);
            $aUser['subscribe'] = $_aUser['subscribe'];
        }else{
            $aUser  = WeixinApi::getUserInfo($openID);
        }
        //用 unionid取数据，和微信登录统一用户
        $unionID = $aUser['unionid'];
    
        $user = User::model()->findByAttributes(array('unionid' => $unionID));
        if($user){
            if($aUser['nickname'] !== '' && $user->NickName == $user->UserName && !User::checkUserName($aUser['nickname'])){
                $user->UserName = $aUser['nickname'];
    
            }
            $user->NickName = $aUser['nickname'];
            $user->Icon = $aUser['headimgurl'];
            $user->IsFollow = $aUser['subscribe'];
            //微信登录的用户，这个字段为空
            $user->Openid = $openID;
    
            if($user->save()) {
                //更新redis
                CommonFunc::setRedis('user_'.$user->ID, 'userHeadUrl', $aUser['headimgurl']);
                CommonFunc::setRedis('user_'.$user->ID, 'userName', $user->UserName);
                return $user->ID;
    
            }else{
                Yii::log(__FILE__ . __LINE__ . 'insert fans error', 'error', 'system.api.weixin');
            }
    
        }else{
    
            $model_user = new User();
            if($aUser['nickname'] === '') {
                $aUser['nickname'] = 'name_' . time();
            }
            $model_user->Account = $aUser['nickname'];
            $model_user->NickName = $aUser['nickname'];
            $model_user->UserName = $aUser['nickname'];
            if(User::checkUserName($aUser['nickname'])){
                $model_user->UserName = $aUser['nickname'].'_'.time();
            }
            $model_user->Openid = $openID;
            $model_user->Icon = $aUser['headimgurl'];
            $model_user->unionid = $aUser['unionid'];
            $model_user->CreateTime = date('Y-m-d H:i:s');
            $model_user->Status = 0;
            $model_user->LastLoginTime = date('Y-m-d H:i:s');
            $model_user->IsFollow = $aUser['subscribe'];
            
            
            if($model_user->save()){
                CommonFunc::setRedis('user_'.$model_user->ID, 'userHeadUrl', $aUser['headimgurl']);
                CommonFunc::setRedis('user_'.$model_user->ID, 'userName', $model_user->UserName);
    
                return $model_user->ID;
            }else{
                Yii::log(__FILE__ . __LINE__ . 'insert fans error', 'error', 'system.api.weixin');
            }
        }
    
        Yii::app()->end();
    
    }

    static function isWeiXin(){
        return (stripos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false);
    }

    static function isMobile(){
        return (stripos($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false);
    }

    static function isAndroid(){
        return (stripos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false);
    }

    static function isIOS(){
        return (stripos($_SERVER['HTTP_USER_AGENT'], 'iPhone') !== false) || (stripos($_SERVER['HTTP_USER_AGENT'], 'iPad') !== false);
    }

    static function transUrl($url){
        if(self::isMobile()){
            if(strpos($url, 'anzhi') !== false) {
                $url = str_replace(array('www', 'soft'), array('m', 'info'), $url);
            }
        }
        return $url;
    }
    
    static function checkIntParam($param, $max, $default = 0){
        if(!is_numeric($param)){
            return $default;
        }
        $param = (int) $param;
        if($param < 0 || $param > $max) {
            return $default;
        }
        return $param;
    }
}