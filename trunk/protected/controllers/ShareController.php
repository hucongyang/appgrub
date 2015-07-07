<?php
/*
 * 分享产品控制器
 */
class ShareController extends Controller
{
    public function filters()
    {
        return array(
            array(
                'application.filters.LoginCheckFilter'
            )
        );
    }
    
    public function actionIndex()
    {
        $this->pageTitle = 'App哥伦部 - 分享新App';
        if(!CommonFunc::isMobile()){
            throw new THttpException('', 404);
        }
        $mobile_type = CommonFunc::isAndroid() ? 'android' : 'ios';
        $this->render('share', array('mobile_type' => $mobile_type));
    }
    
    public function actionAdd()
    {
        $commitUserID = Yii::app()->user->id;
        if (empty($commitUserID)) {
            echo new ReturnInfo(RET_SUC, array('code' => -1, 'msg' => '请登录后再分享App'));
            Yii::app()->end();
        }
        if (! Yii::app()->request->isPostRequest){
            echo new ReturnInfo(RET_SUC, array('code' => -1, 'msg' => '数据请求方式错误'));
            Yii::app()->end();
        }
        $appUrl = trim(Yii::app()->request->getParam('appUrl', ''));
        if (empty($appUrl)) {
            echo new ReturnInfo(RET_SUC, array('code' => -1, 'msg' => 'App链接不能为空'));
            Yii::app()->end();
        }
        $appHost = parse_url($appUrl);
        $domains = Source::getSourceDomains();
        if(!isset($appHost['host']) || !in_array($appHost['host'], $domains)){
            echo new ReturnInfo(RET_SUC, array('code' => -1, 'msg' => 'App链接有误,请参考填写规则'));
            Yii::app()->end();
        }
        $userInfo = array();
        $userInfo = unserialize(Yii::app()->cache->get('user_' . $commitUserID));
        $md5AppUrl = md5($appUrl);
        if(isset($userInfo['share_list']) && in_array($md5AppUrl, $userInfo['share_list'])) {
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
        $link->CommitTime = date('Y-m-d H:i:s');
        $link->OfficialWeb = empty($officialUrl) ? '' : $officialUrl;
        $link->Status = 1;
        $link->Sort = $link->model()->getMaxSort() + 1;
        if ($link->save()) {
            if(!isset($userInfo['share_list']) || empty($userInfo['share_list'])) {
                $userInfo['share_list'] = array();
            }
            $userInfo['share_list'][] = $md5AppUrl;
            Yii::app()->cache->set('user_' . $commitUserID, serialize($userInfo));
            echo new ReturnInfo(RET_SUC, 0);
        } else {
            echo new ReturnInfo(RET_SUC, array('code' => -1, 'msg' => $link->getErrors()));
        }
    }

    public function actionGetSource()
    {
        echo new ReturnInfo(RET_SUC, Source::getSourceDomains());
    }
}
