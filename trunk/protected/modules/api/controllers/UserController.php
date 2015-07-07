<?php
class UserController extends Controller
{
    public $apiUser;
    public function filters()
    {
        return array(
            array('application.filters.ApiCheckFilter - login'),
            array('application.filters.TokenCheckFilter'),
        );
    }
    //我的主页
    public function actionIndex()
    {
        $amI = false;
        $memberID = Yii::app()->getRequest()->getQuery('userid');//别的会员主页
        if (empty($memberID)) {
            $memberID = $this->apiUser->ID;
            $amI = true;
        }
        if ($memberID == $this->apiUser->ID) {
            $amI = true;
        }
        $member = User::model()->findByPk($memberID);
        if (! $member instanceof User) {
            echo new ReturnInfo(RET_ERROR, 'Argument userid passed to ' . __CLASS__ . '::' . __FUNCTION__ . '() that can not find a record.');
            Yii::app()->end();
        }
        $command = Yii::app()->db->createCommand();
        $command->select('count(Id) as countApp, sum(Up) as totalUp');
        $command->from('app_info_list');
        $command->where('CommitUserId = :CommitUserId', array(':CommitUserId' => $memberID));
        $result = $command->queryRow();
        if (empty($result)) {
            $result = array('countApp' => 0, 'totalUp' => 0);
        }
        $userKey = 'user_' . $this->apiUser->ID;
        $memberArray = CommonFunc::getRedis($userKey);
        $interactedApp = 0;
        if (isset($memberArray['like'])) {
            $interactedApp += count($memberArray['like']);
        }
        if (isset($memberArray['comment'])) {
            $interactedApp += count($memberArray['comment']);
        }
        echo new ReturnInfo(
            RET_SUC,
            array(
                'data' => array(
                    'amI' => $amI,//是否是当前登陆者的zone
                    'app_count'  => $result['countApp'],
                    'app_up'     => $result['totalUp'],
                    'interacted_app' => $interactedApp,
                    'last_login' => Yii::app()->user->lastLoginTime,
                    'member'  => array(
                        'memberID' => $member->ID,
                        'memberName' => $member->UserName,
                        'icon' => $member->Icon
                    )
                )
            )
        );
    }

    public function actionMyApp()
    {
        $memberID = Yii::app()->getRequest()->getQuery('userid');
        $condition = 'CommitUserId = :CommitUserId';
        if (empty($memberID)) {
            $memberID = Yii::app()->user->id;
        }
        if ($memberID !=  Yii::app()->user->id) {
            $condition .= ' and Status = 0';
        }
        $member = User::model()->findByPk($memberID);
        if (! $member instanceof User) {
            echo new ReturnInfo(RET_ERROR, '操作错误');
            Yii::app()->end();
        }
        echo new ReturnInfo(
            RET_SUC,
            array(
                'data' => AppInfoList::parseData(
                    AppInfoList::model()->findAll(
                        array(
                            'condition' => $condition,
                            'order' => 'Id desc',
                            'params' =>  array(':CommitUserId' => $memberID)
                        )
                    ), $memberID
                )
            )
        );
    }

    //点赞
    public function actionLike()
    {
        $appID = Yii::app()->getRequest()->getQuery('appid');
        if (empty($appID)) {
            echo new ReturnInfo(RET_ERROR, 'Argument appid passed to ' . __CLASS__ . '::' . __FUNCTION__ . '() that can not be empty.');
            Yii::app()->end();
        }
        $app = AppInfoList::model()->findByPk($appID);
        if (empty($app)) {
            echo new ReturnInfo(RET_ERROR, 'Argument appid passed to ' . __CLASS__ . '::' . __FUNCTION__ . '() that can not find a record.');
            Yii::app()->end();
        }
        try {
            AppInfoList::up($app, $this->apiUser, true);
            echo new ReturnInfo(RET_SUC, 1);
        } catch (Exception $e) {
            echo new ReturnInfo(RET_ERROR, $e->getMessage());
        }
    }

    //取消赞
    public function actionDislike()
    {
        $appID = Yii::app()->getRequest()->getQuery('appid');
        if (empty($appID)) {
            echo new ReturnInfo(RET_ERROR, 'Argument appid passed to ' . __CLASS__ . '::' . __FUNCTION__ . '() that can not be empty.');
            Yii::app()->end();
        }
        $app = AppInfoList::model()->findByPk($appID);
        if (empty($app)) {
            echo new ReturnInfo(RET_ERROR, 'Argument appid passed to ' . __CLASS__ . '::' . __FUNCTION__ . '() that can not find a record.');
            Yii::app()->end();
        }
        try {
            AppInfoList::up($app, $this->apiUser, false);
            echo new ReturnInfo(RET_SUC, 1);
        } catch (Exception $e) {
            echo new ReturnInfo(RET_ERROR, $e->getMessage());
        }
    }
    //收藏
    public function actionFavorite()
    {
        $appID = Yii::app()->getRequest()->getQuery('appid');
        if (empty($appID)) {
            echo new ReturnInfo(RET_ERROR, 'Argument appid passed to ' . __CLASS__ . '::' . __FUNCTION__ . '() that can not be empty.');
            Yii::app()->end();
        }
        $app = AppInfoList::model()->findByPk($appID);
        if (empty($app)) {
            echo new ReturnInfo(RET_ERROR, 'Argument appid passed to ' . __CLASS__ . '::' . __FUNCTION__ . '() that can not find a record.');
            Yii::app()->end();
        }
        try {
            Favorite::favoriteBoolean($appID, $this->apiUser->ID, true);
            echo new ReturnInfo(RET_SUC, 1);
        } catch (Exception $e) {
            echo new ReturnInfo(RET_ERROR, $e->getMessage());
        }
    }
    //取消收藏
    public function actionUnfavorite()
    {
        $appID = Yii::app()->getRequest()->getQuery('appid');
        if (empty($appID)) {
            echo new ReturnInfo(RET_ERROR, 'Argument appid passed to ' . __CLASS__ . '::' . __FUNCTION__ . '() that can not be empty.');
            Yii::app()->end();
        }
        $app = AppInfoList::model()->findByPk($appID);
        if (empty($app)) {
            echo new ReturnInfo(RET_ERROR, 'Argument appid passed to ' . __CLASS__ . '::' . __FUNCTION__ . '() that can not find a record.');
            Yii::app()->end();
        }
        try {
            Favorite::favoriteBoolean($appID, $this->apiUser->ID, false);
            echo new ReturnInfo(RET_SUC, 1);
        } catch (Exception $e) {
            echo new ReturnInfo(RET_ERROR, $e->getMessage());
        }
    }

    public function actionAddComment()
    {
        if (! Yii::app()->request->isPostRequest) {
            echo new ReturnInfo(RET_ERROR, 'Data request error,post please.');
            Yii::app()->end();
        }
        $content  = Yii::app()->getRequest()->getQuery('content');
        if (empty($content)) {
            echo new ReturnInfo(RET_ERROR, 'Argument content passed to ' . __CLASS__ . '::' . __FUNCTION__ . '() that can not be empty.');
            Yii::app()->end();
        }
        $appID = Yii::app()->getRequest()->getQuery('appID');
        if (empty($appID)) {
            echo new ReturnInfo(RET_ERROR, 'Argument appID passed to ' . __CLASS__ . '::' . __FUNCTION__ . '() that can not be empty.');
            Yii::app()->end();
        }
        $app = AppInfoList::model()->findByPk($appID);
        if (! ($app instanceof AppInfoList)) {
            echo new ReturnInfo(RET_ERROR, 'Argument appID passed to ' . __CLASS__ . '::' . __FUNCTION__ . '() that can not find a record.');
            Yii::app()->end();
        }
        try {
            AppReviews::comment($app->Id, $this->apiUser, $content);
            $return = array();
            $return['content'] = $content;
            $return['username'] = $this->apiUser->UserName;
            echo new ReturnInfo(RET_SUC, $return);
        } catch (Exception $e) {
            echo new ReturnInfo(RET_ERROR, $e->getMessage());
        }
    }

    public function actionReplyComment()
    {
        if (! Yii::app()->request->isPostRequest) {
            echo new ReturnInfo(RET_ERROR, 'Data request error,post please.');
            Yii::app()->end();
        }
        $content  = Yii::app()->getRequest()->getQuery('content');
        if (empty($content)) {
            echo new ReturnInfo(RET_ERROR, 'Argument content passed to ' . __CLASS__ . '::' . __FUNCTION__ . '() that can not be empty.');
            Yii::app()->end();
        }
        $appID = Yii::app()->getRequest()->getQuery('appID');
        if (empty($appID)) {
            echo new ReturnInfo(RET_ERROR, 'Argument appID passed to ' . __CLASS__ . '::' . __FUNCTION__ . '() that can not be empty.');
            Yii::app()->end();
        }
        $pid = Yii::app()->getRequest()->getQuery('pid');
        if (empty($pid)) {
            echo new ReturnInfo(RET_ERROR, 'Argument pid passed to ' . __CLASS__ . '::' . __FUNCTION__ . '() that can not be empty.');
            Yii::app()->end();
        }
        $app = AppInfoList::model()->findByPk($appID);
        if (! ($app instanceof AppInfoList)) {
            echo new ReturnInfo(RET_ERROR, 'Argument appID passed to ' . __CLASS__ . '::' . __FUNCTION__ . '() that can not find a record.');
            Yii::app()->end();
        }
        $reply = AppReviews::model()->findByPk($pid);
        if (!($reply instanceof AppReviews)) {
            echo new ReturnInfo(RET_ERROR, 'Argument pid passed to ' . __CLASS__ . '::' . __FUNCTION__ . '() that can not find a record.');
            Yii::app()->end();
        }
        try {
            AppReviews::comment($app->Id, $this->apiUser, $content, $pid);
            $return = array();
            $return['content'] = $content;
            $return['username'] = $this->apiUser->UserName;
            echo new ReturnInfo(RET_SUC, $return);
        } catch (Exception $e) {
            echo new ReturnInfo(RET_ERROR, $e->getMessage());
        }
    }
    
    public function actionLogin()
    {
        $uid = Yii::app()->getRequest()->getQuery('unionid');
        $nickname = urldecode(Yii::app()->getRequest()->getQuery('nickname'));
        $headurl = urldecode(Yii::app()->getRequest()->getQuery('headimgurl'));
        if(empty($uid) || empty($nickname) || empty($headurl) ){
            echo new ReturnInfo(RET_ERROR, "param error");
            Yii::app()->end();
        }
        $user = User::model()->findByAttributes(array('unionid' => $uid));
        if ($user) {
            $user->UserName = $nickname;
            $user->Icon = $headurl;
            $user->save();
        } else {
            $model_user = new User();
            $model_user->Account = $nickname;
            $model_user->UserName = $nickname;
            $model_user->Icon = $headurl;
            $model_user->unionid = $uid;
            $model_user->CreateTime = date('Y-m-d H:i:s');
            $model_user->Status = 0;
            if(!$model_user->save()){
                echo new ReturnInfo(RET_ERROR, 'login error');
                Yii::app()->end();
            }
        }
        echo new ReturnInfo(RET_SUC, 'login_success');
    }
    
    public function actionInteractionApp()
    {
        $memberID = Yii::app()->getRequest()->getQuery('memberid');
        if (empty($memberID)) {
            $memberID = Yii::app()->user->id;
        }
        $member = User::model()->findByPk($memberID);
        if (! $member instanceof User) {
            echo new ReturnInfo(RET_ERROR, "链接有误或用户不存在");
            Yii::app()->end();
        }
        $type = Yii::app()->request->getParam('type', 1);
        if ($type != 1 && $type != 2) {
            echo new ReturnInfo(RET_ERROR, "参数错误");
            Yii::app()->end();
        }
        $interactionType = $type == 1 ? 'like' : 'comment';
        $userKey = 'user_' . $memberID;
        echo new ReturnInfo(
            RET_SUC,
            array(
                'apps' => AppInfoList::getInteractionApp($memberID, CommonFunc::getRedis($userKey, $interactionType)),
                'type' => $type,
                'memberID' => $memberID
            )
        );
    }

    public function actionMyFavorite()
    {
        $userID = Yii::app()->getRequest()->getQuery('memberid');//$this->apiUser->ID;
        if (! empty($userID)) {
            $userKey = 'user_' . $userID;
            $interactionApp = CommonFunc::getRedis($userKey, 'favorite');
            $interactionAppIds = array_keys($interactionApp);
            $data = AppInfoList::getInteractionApp($userID, $interactionAppIds);
            echo new ReturnInfo(RET_SUC, $data);
        } else {
            echo new ReturnInfo(RET_ERROR, '请登陆后再查看');
        }
    }
}
