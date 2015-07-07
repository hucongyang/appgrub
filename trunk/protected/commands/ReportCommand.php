<?php

class ReportCommand extends CConsoleCommand
{
    public function actionUseractive(){
        $yesterday = date("Y-m-d",strtotime("-1 day"));
        
        $yesterdaybf = date("Y-m-d",strtotime("-2 day"));
        
        $model_ybf = ReportUserActive::model()->findByAttributes(array('date' => $yesterdaybf));
        $count = User::model()->count("Status = 0");
        if($model_ybf){
            $new = $count - $model_ybf->user_all;
        }else{
            $new = 0;
        }
        
        $active = User::model()->count( "Status = 0 and CreateTime < '$yesterday' and LastLoginTime>='$yesterday'" );
        
        
        $m = ReportUserActive::model()->findByAttributes(array('date' => $yesterday));
        if(!$m){
            $m = new ReportUserActive();
        }
        
        
        $m->date = $yesterday;
        $m->user_all = $count;
        $m->user_new = $new;
        $m->user_active = $active;
        $m->save();
        
    }
    
    
}
