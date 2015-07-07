<?php

class AdminCheckFilter extends CFilter
{
    protected function preFilter($filterChain)
    {
        if(Yii::app()->user->isGuest){
            Yii::app()->user->loginRequired();
        }else {
            $aOpenid = array(
                'ohdH2s23MGn5EaWIVqT9B979dN5c',
                'ohdH2s5ustufKzGUUBvZW1KGQFB4',
                'ohdH2s0pdej67O8EKZ-kfdqqVQMA',
                'ohdH2s0EimJdDekgeu4TN8EExgXE',
                'ohdH2s8zSzWpPbx1tY5BW1MaiLlg'
                );
            $openId = Yii::app()->user->openid;
            return in_array($openId, $aOpenid);
        }
    }
}