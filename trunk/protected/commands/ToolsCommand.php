<?php

class ToolsCommand extends CConsoleCommand
{
    public function actionForgeUp($up = 10, $clear = false){
        
        $aUser = array();
        $aUserID = array();
        $models_user = User::model()->findAllByAttributes(array('Status' => '-1'));
        
        foreach($models_user as $m){
            $aUser[] = array(
                    'ID' => $m->ID,
                    'userurl' => $m->Icon,
                    'username' => $m->UserName
            );
            $aUserID[ $m->ID ] = 1;
        }
        
        $models = AppInfoList::model()->findAll();
        
        $iUserCount = count($aUser);
        

        
        foreach($models as $m){
            
            
            $k = 'link_' . $m->Id;
            $v = Yii::app()->cache->get($k);
            
            if( $v == null || $clear){
                $aRedis = array(
                        'count' => 0,
                        'user' => array()
                );
                $m->Up = 0;
            }else{
                $aRedis = unserialize($v);
            }
            
            if( isset($aUserID[$m->CommitUserId]) ){
                //系统分享的应用
                $c = rand(rand(0,$m->reply_count+rand(1,$m->reply_count)), $up*2 + $m->reply_count * 3);
            }else{
                $c = rand(rand(0,$m->reply_count+rand(1,$m->reply_count)), $up + $m->reply_count * 2);
            }
            
            //开始伪造赞
            for($i=0; $i<$c; $i++){
                $index = rand(1, $iUserCount-1);
                if( isset($aRedis['user'][ $aUser[$index]['ID'] ]) ) continue;
                $aRedis['count'] = $m->Up + 1;
                $aRedis['user'][ $aUser[$index]['ID'] ] = $aUser[$index];
                $m->Up = $aRedis['count'];
                //在循环里save，可以触发其他sort字段一起更新
                $m->save();
            }
            
            Yii::app()->cache->set($k, serialize($aRedis) );
            
        }
        
    }
    
    public function actionForgeComitTime(){
        $period = array('week','days','hours');
        
        $aUserID = array();
        $models_user = User::model()->findAllByAttributes(array('Status' => '-1'));
        
        foreach($models_user as $m){
            $aUserID[ $m->ID ] = 1;
        }
        
        $models = AppInfoList::model()->findAll();
        
        $aTime = array();
        foreach($models as $m){
            $t = array();
            for($i=0; $i<3; $i++){
                $j = rand($i*$i, 3+$i*5);
                $t[] = "-$j " . $period[$i];
            }
            $aTime[] = date("Y-m-d H:i:s", strtotime(implode(' ', $t)) );
        }
        sort($aTime);
        $iTime = 0;
        foreach($models as $m){
            if( isset($aUserID[$m->CommitUserId]) ){
                //系统分享的
                $m->CommitTime = $aTime[$iTime];
                $m->Sort = $iTime;
                $m->save();
                $iTime++;
            }
            
        }
        
    
    }
    
    public function actionGKey($key){
        echo Yii::app()->cache->get($key) . "\n";
        
    }
    
    public function actionSKey($k, $v){
        Yii::app()->cache->set($k, $v);
    }
    
    
    public function actionSetMenu(){
        $menu = '
{
     "button":[
     {
          "name":"最新应用",
          "type":"click",
          "key":"APP_NEW_LIST"
      },
      {
          "name":"进入网站",
          "type":"view",
          "url":"http://appgrub.com"
      },
      {
          "type":"view",
          "name":"下载客户端",
          "url":"http://appgrub.com/client/down"
     }]
 }
';
        WeixinApi::setMenu($menu);
    }
    
    
    public function actionCurl(){
        $request = Yii::app()->curl->run("http://out.zhe800.com/ju/deal/LEDyang_946752");
        var_dump($request);
    }
    
    public function actionFlushRedis(){
        $aUser = User::model()->findAll();
        
        foreach($aUser as $m){
            Yii::app()->cache->delete( 'user_' . $m->ID );
        }
        
        $aLink = AppInfoList::model()->findAll();
        foreach($aLink as $m){
            Yii::app()->cache->delete( 'link_' . $m->Id);
        }
    }
    
    public function actionUpdateUserInfo(){
        $aUser = User::model()->findAll();
        foreach($aUser as $m){
            CommonFunc::setRedis('user_'.$m->ID, 'userHeadUrl', $m->Icon);
            CommonFunc::setRedis('user_'.$m->ID, 'userName', $m->UserName);
        }
    }

    public function actionUpdateAppUp(){
        $apps = AppInfoList::model()->findAll();
        foreach($apps as $app) {
            $appUp = CommonFunc::getRedis('link_' . $app->Id, 'count');
            if (empty($appUp)){
                continue;
            }
            $app->Up = $appUp;
            $app->save();
        }
    }
}
