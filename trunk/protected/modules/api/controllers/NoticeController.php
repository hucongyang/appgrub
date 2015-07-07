<?php
class NoticeController extends Controller
{
    public $apiUser;
    public function filters()
    {
        return array(
            array('application.filters.ApiCheckFilter - ShareResultNotice'),
            array('application.filters.TokenCheckFilter'),
        );
    }
    //个人通知页面
    public function actionIndex()
    {
        $noticeModel = Notice::model();
        $msg = $noticeModel->findAll(
            array(
                'condition' => 'targetUserid =:targetUserid AND readFlag = 1',
                'order' => 'createTime',
                'params' => array(':targetUserid' => $this->apiUser->ID)
            )
        );
        echo new ReturnInfo(RET_SUC, array('data' => $msg));
        $noticeModel->updateAll(
            array('readFlag' => '0'),
            'targetUserid = :targetUserid',
            array(':targetUserid' => $this->apiUser->ID)
        );
    }
    //显示通知数量
    public function actionNoticeCount()
    {
        echo new ReturnInfo(
            RET_SUC,
            array(
                'data' => Notice::Model()->count(
                    "targetUserid=:targetUserId AND readFlag = 1",
                    array("targetUserId" => $this->apiUser->ID)
                )
            )
        );
    }

    public function actionShareResultNotice()
    {
        $appID = Yii::app()->request->getParam('appID');
        $app = AppInfoList::model()->findByPk($appID);
        if (! $app instanceof AppInfoList) {
            echo new ReturnInfo(RET_ERROR, 'app id error');
            Yii::app()->end();
        }
        $user = User::model()->findByPk($app->CommitUserId);
        if (! $user instanceof User || $user->Status != 0) {
            echo new ReturnInfo(RET_ERROR, 'user id or user status error');
            Yii::app()->end();
        }
        $message = '';
        if ($app->Status == 0) {
            $message = '您分享的<a href="/produce/index/'.$appID.'">App《'.htmlspecialchars($app->AppName).'》</a>已经通过审核';
        } else if ($app->Status == 2) {
            $message = '您分享的App(<a href="'.rawurlencode($app->AppUrl).'" target="_blank">'.htmlspecialchars($app->AppUrl).'</a>)，没有通过审核';
        } else {
            echo new ReturnInfo(RET_ERROR, 'app status error');
            Yii::app()->end();
        }
        $notice = new Notice();
        $notice->type = 0;
        $notice->targetUserid = $app->CommitUserId;
        $notice->msg = $message;
        $notice->createTime = date('Y-m-d H:i:s');
        $notice->appId = $appID;
        if ($notice->save()) {
            echo new ReturnInfo(RET_SUC, true);
        } else {
            echo new ReturnInfo(RET_ERROR, $notice->getErrors());
        }
    }
}