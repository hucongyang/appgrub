<?php

class ErrorController extends Controller
{
    /**
     * Declares class-based actions.
     */
    //public $layout='//layouts/exception';
    public $aErrorMsg = array(
            '1000' => array('title' => '出错啦~', 'message' => 'Opps!!!服务器压力有点大!'),
            '200' => array('title' => '出错啦~', 'message' => ''),
            '404' => array('title' => '404 NOT FOUND', 'message' => '哎呀...您访问的页面不存在'),
            '1101' => array('title' => '', 'message' => ''),
            '1102' => array('title' => '非常抱歉~~~', 'message' => '工程师忙成狗了，客户端还在玩命研发中...'),
            '1103' => array('title' => '出错啦~', 'message' => '请关注微信公众号"appgrub"后,在对话框中点击进入网站完成登录!'),
        );

    public function filters()
    {
        return array();
    }

    /**
     * This is the action to handle external exceptions.
     */
    public function actionError()
    {
        $this->pageTitle = 'App哥伦部 - 发现好玩的App';
        if($error=Yii::app()->errorHandler->error)
        {
            if($error['code'] != 404 && !isset($this->aErrorMsg[$error['errorCode']])){
                Yii::log(' code:' . $error['code'] . ' errorCode:' . $error['errorCode'] . ' error : ' . $error['file'] .":". $error['line'] .":". $error['message'], 'error', 'system');
            }
            if(Yii::app()->request->getIsAjaxRequest()){
                $ret = new ReturnInfo(FAIL_RET, Yii::t('exceptions', $error['message']), intval($error['errorCode']));
                echo json_encode($ret);   
            }else{
                if(empty($error['errorCode'])){
                    if(isset($this->aErrorMsg[$error['code']])){
                        if(empty($this->aErrorMsg[$error['code']]['message'])) {
                            $this->aErrorMsg[$error['code']]['message'] = $error['message'];
                        }
                        $errorMsg = $this->aErrorMsg[$error['code']];
                    }else{
                        $errorMsg = $this->aErrorMsg['1000'];
                    }
                }else{
                    if(isset($this->aErrorMsg[$error['errorCode']]) && $this->aErrorMsg[$error['errorCode']]['message'] != '') {
                        $errorMsg = $this->aErrorMsg[$error['errorCode']];
                    } else {
                        $errorMsg = $this->aErrorMsg['1000'];
                    }
                }
                $this->render('error', $errorMsg);
            }
        } 
    }
}