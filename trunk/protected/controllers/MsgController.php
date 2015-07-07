<?php 
/**
 * 通知消息控制器
 */
class MsgController extends Controller
{
    public function filters()
    {
        return array(
            array(
                'application.filters.LoginCheckFilter',
            )
        );
    }

    public function actionIndex()
    {
        $this->pageTitle = 'App哥伦部 - 通知';
        $noticeModel = Notice::model();
        $targetUserid = Yii::app()->user->id;
        $msgs = $noticeModel->findAll(
            array(
                'condition' => 'targetUserid =:targetUserid AND readFlag = 1',
                'order' => 'createTime',
                'params' => array(':targetUserid' => $targetUserid)
            )
        );
        $noticeInfo = array();
        foreach ($msgs as $msg) {
            if ($msg->type == 0) {
                $noticeInfo[] = array(
                    'type' => $msg->type,
                    'msg'  => $msg->msg,
                    'createTime' => $msg->createTime,
                );
            } else {
                $appInfo = AppInfoList::model()->find(
                    array(
                        'select' => 'Id, AppName',
                        'condition' => 'Id = :appId',
                        'params' => array(':appId' => $msg->appId)
                    )
                );
                $authorId = $msg->reviews->AuthorId;
                $noticeInfo[] = array(
                    'type' => $msg->type,
                    'msg'  => $msg->reviews->Content,
                    'createTime' => $msg->createTime,
                    'appName' => $appInfo->AppName,
                    'appID' => $appInfo->Id,
                    'authorName' => htmlspecialchars(CommonFunc::getRedis('user_'. $authorId, 'userName')),
                    'authorID' => $authorId
                );
            }
        }
        $noticeModel->updateAll(array('readFlag' => '0'), 'targetUserid = :targetUserid', array(':targetUserid' => $targetUserid));
        $this->render('msg', array('msg' => $noticeInfo));
    }

    public function actionNoticeCount()
    {
        if (Yii::app()->request->isAjaxRequest) {
            echo Notice::Model()->count("targetUserid=:targetUserId AND readFlag = 1", array(":targetUserId" => Yii::app()->user->id));
        }
    }
}
