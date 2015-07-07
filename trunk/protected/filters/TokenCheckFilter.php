<?php

class TokenCheckFilter extends CFilter
{
    protected function preFilter($filterChain)
    {
        $t = $_GET["timestamp"];
        $k = "0d1bcacc49b6d72738cf550e2bc838f4";
        $token = sha1($k . $t);
        if (isset($_GET['token']) && $_GET['token'] == $token) {
            return true;
        } else {
            return false;
        }
    }
}
