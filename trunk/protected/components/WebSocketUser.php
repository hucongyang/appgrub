<?php

class WebSocketUser {

  public $socket;
  public $id;
  public $headers = array();
  public $handshake = false;

  public $handlingPartialPacket = false;
  public $partialBuffer = "";

  public $sendingContinuous = false;
  public $partialMessage = "";
  
  public $hasSentClose = false;

  function __construct($socket) {
    $i = rand(1000000, 4294967296);
    $status = Yii::app()->cache->get('qr' . $i);    
    while( $status ){
        $i = rand(1000000, 4294967296);
        $status = Yii::app()->cache->get('qr' . $i);
    }
    //二维码展示阶段，还没有被扫描
    Yii::app()->cache->set('qr' . $i, 1);
    
    $this->id = $i;
    $this->socket = $socket;
  }
}