<?php
class WebServer extends WebSocketServer {
    //protected $maxBufferSize = 1048576; //1MB... overkill for an echo server, but potentially plausible for other applications.

    protected function process ($user, $message) {
        
        $status = Yii::app()->cache->get('qr'.$message);
        if($status){
            $aStatus = unserialize($status);
            if($aStatus['login'] == 2){
                $auth_token = md5( 'login_token' . time() . $message);
                $msg = "2|" . $auth_token;
                Yii::app()->cache->delete('qr'.$message);
                
                //安全起见，换一个key
                Yii::app()->cache->set($auth_token, $status, 30);
                Yii::log(__FILE__ . __LINE__ . 'wbsocket command token:' . $auth_token . ';status:'.$status, 'trace', 'system.api.weixin');
                
                $this->send($user, $msg);
                $this->disconnect($user->socket);
            }else{
                $this->send($user, 1);
            }
            
        }else{
            $this->disconnect($user->socket);
        }
    }

    protected function connected ($user) {
        $this->send($user, '1');
    }

    protected function closed ($user) {
        // Do nothing: This is where cleanup would go, in case the user had any sort of
        // open files or other objects associated with them.  This runs after the socket
        // has been closed, so there is no need to clean up the socket itself here.
    }
    
    
    public function loginCheck(){
        
    }
}


class WbSocketCommand extends CConsoleCommand{
    
    public function actionRun(){
        
        $server = new WebServer("0.0.0.0","443");
        
        try {
            $server->run();
            
        }
        catch (Exception $e) {
            $server->stdout($e->getMessage());
        }
        
    }
    
    public function actionTest(){
        $i = rand(1000000, 4294967296);
        $status = Yii::app()->cache->get('qr' . $i);
        while( $status ){
            $i = rand(1000000, 4294967296);
            $status = Yii::app()->cache->get('qr' . $i);
        }
        //二维码展示阶段，还没有被扫描
        Yii::app()->cache->set('qr' . $i, 1);
    }
}
