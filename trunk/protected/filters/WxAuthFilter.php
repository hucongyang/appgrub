<?php 

class WxAuthFilter extends CFilter
{
    protected function preFilter($filterChain)
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        
        
        $token = "Jj234jkKijwtuzm45239";

        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );
        
        
        if( $tmpStr == $signature ){
            if( isset( $_GET['echostr'] ) ){
                echo $_GET['echostr'];
                Yii::app()->end();
            }
            return true;
        }else{
            Yii::log('weixin auth error', 'error', 'system.api.weixin');
            return false;
        }
        
    }
}
