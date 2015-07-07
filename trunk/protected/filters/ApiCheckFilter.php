<?php

class ApiCheckFilter extends CFilter
{
    protected function preFilter($filterChain)
    {
        if (isset($_GET['unionid']) ) {
            $user = User::model()->find(
                'unionid = :unionid',
                array(
                    ':unionid'  => $_GET['unionid']
                )
            );
            $filterChain->controller->apiUser = $user;
            return $user instanceof User ? true : false;
        } else {
            return false;
        }
    }
}
