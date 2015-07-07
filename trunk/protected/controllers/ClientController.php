<?php
class ClientController extends Controller{

    public function actionDown(){
        
        throw new THttpException("", 1102);
        
        
    }
    
}