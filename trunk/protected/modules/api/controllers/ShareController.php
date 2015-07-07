<?php
class ShareController extends Controller
{
    public $apiUser;
    public function filters()
    {
        return array(
            array('application.filters.ApiCheckFilter'),
            array('application.filters.TokenCheckFilter'),
        );
    }

    public function actionAddApp()
    {
        $commitUserID = $this->apiUser->ID;
        if (empty($commitUserID)) {
            echo new ReturnInfo(RET_SUC, array('code' => -1, 'msg' => '请登录后再分享App'));
            Yii::app()->end();
        }
        if (! Yii::app()->request->isPostRequest){
            echo new ReturnInfo(RET_SUC, array('code' => -1, 'msg' => '数据请求方式错误'));
            Yii::app()->end();
        }
        $appUrl = Yii::app()->request->getParam('appUrl');
        if (empty($appUrl)) {
            echo new ReturnInfo(RET_SUC, array('code' => -1, 'msg' => 'App链接不能为空'));
            Yii::app()->end();
        }
        $app = AppInfoList::model()->findByAttributes(array('CommitUserId' => $commitUserID, 'AppUrl' => $appUrl));
        if ($app instanceof AppInfoList) {
            echo new ReturnInfo(RET_SUC, array('code' => -1, 'msg' => '您已经提交过该App了'));
            Yii::app()->end();
        }
        $explain = Yii::app()->request->getParam('explain');
        $officialUrl = Yii::app()->request->getParam('url');
        $link = new AppInfoList();
        $link->SourceId = Source::getSourceByDomain($appUrl);
        $link->CommitUserId = $commitUserID;
        $link->Remarks = empty($explain) ? '' : $explain;
        $link->AppUrl = $appUrl;
        $link->CommitTime = date('Y-m-d H:i:s',time());
        $link->OfficialWeb = empty($officialUrl) ? '' : $officialUrl;
        $link->Status = 1;
        $link->Sort = $link->model()->getMaxSort() + 1;
        if ($link->save()) {
            echo new ReturnInfo(RET_SUC, 0);
        } else {
            echo new ReturnInfo(RET_SUC, array('code' => -1, 'msg' => $link->getErrors()));
        }
    }
}
