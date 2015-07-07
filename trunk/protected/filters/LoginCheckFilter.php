<?php 

class LoginCheckFilter extends CFilter
{
    protected function preFilter($filterChain)
    {
        if(Yii::app()->user->isGuest){
            Yii::app()->user->loginRequired();
        }
        return true;
    }
}
