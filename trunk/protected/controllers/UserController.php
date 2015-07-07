<?php

class UserController extends Controller{

    public $hidden_weixin = null;
    public function filters()
    {
        return array(
            array(
                'application.filters.LoginCheckFilter + Save, MyFavorite',
            )
        );
    }
    /**
     * This is the action to handle external exceptions.
     */
    public function actionError()
    {
        if($error=Yii::app()->errorHandler->error)
        {
            if(Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('error', $error);
        }
    }

    /**
     * Displays the login page
     */
    public function actionLogin()
    {
        $this->pageTitle = 'App哥伦部 - 登录';
        if (Yii::app()->user->id) {
            $this->redirect("/");
        }
        $this->hidden_weixin = 1;
        $this->render('login');
    }

    public function actionCheckLogin()
    {
        if (YII_DEBUG) {
            $identity = new UserIdentity('ohdH2s8zSzWpPbx1tY5BW1MaiLlg','');
            $identity->authenticate();
            $user = Yii::app()->user;
            $user->login($identity, 3600*24*30);
            echo new ReturnInfo(0, '登陆成功');
            Yii::app()->end();
        }

        $qrcode = Yii::app()->user->njsid;
        if(!empty($qrcode)){
            $status = Yii::app()->cache->get('qr'.$qrcode);
            if($status){
                $aStatus = unserialize($status);
                if($aStatus['login'] == 2){
                    $identity = new UserIdentity($aStatus['openID'],'');
                    $identity->authenticate();
                    $user = Yii::app()->user;
                    $user->login($identity, 3600*24*30);
                    echo new ReturnInfo(0, '登陆成功');
                    Yii::app()->end();
                }
            }
        }
        echo new ReturnInfo(-1, '登陆失败');
    }
    /**
     * Logs out the current user and redirect to homepage.
     */
    public function actionLogout()
    {
        Yii::app()->user->logout();
        
        $this->redirect("/");
    }
    
    public function accessRules()
    {
        return array(
            array(
                'allow',
                'action'=>array('captcha'),
                'users'=>array('*'),
            ),
        );
    }

    //我的主页
    public function actionMyZone ()
    {
        $this->pageTitle = 'App哥伦部 - 个人主页';
        //用户主页
        $itsMe = false;
        $memberID = Yii::app()->getRequest()->getQuery('memberid');//别的会员主页
        $condition = 'CommitUserId = :CommitUserId and Status = 0';
        if (empty($memberID)) {
            if (Yii::app()->user->isGuest) {
                Yii::app()->user->loginRequired();
            }
            $memberID = Yii::app()->user->id;
            $itsMe = true;
        }
        if ($memberID ==  Yii::app()->user->id) {
            $itsMe = true;
            $condition = 'CommitUserId = :CommitUserId';
        }
        $member = User::model()->findByPk($memberID);
        $memberApps = AppInfoList::model()->findAll(
            array(
                'select' => array('Id', 'Sort', 'Up'),
                'condition' => $condition,
                'params' =>  array(':CommitUserId' => $memberID)
            )
        );
        $upCount = 0;
        foreach ($memberApps as $app) {
            $upCount += $app->Up;
        }
        $userKey = 'user_' . $memberID;
        $userArray = Yii::app()->cache->get($userKey);
        $memberArray = empty($userArray) ? array() : unserialize($userArray);
        $interactedApp = 0;
        if (isset($memberArray['like'])) {
            $interactedApp += count($memberArray['like']);
        }
        if (isset($memberArray['comment'])) {
            $interactedApp += count($memberArray['comment']);
        }
        if(empty($member->UserName)) {
            $UserName = $member->NickName;
        }else{
            $UserName = $member->UserName;
        }
        $this->render(
            'myzone',
            array(
                'data' => array(
                    'amI' => $itsMe,//是否是当前登陆者的zone
                    'appCount' => count($memberApps),
                    'appUp'    => $upCount,
                    'interactedApp' => $interactedApp,
                    'member'  => array(
                        'memberID' => $member->ID,
                        'memberName' => htmlspecialchars($UserName),
                        'email' => $member->Email,
                        'icon' => $member->Icon
                    )
                )
            )
        );
    }
    //我的app列表
    public function actionMyAppList()
    {
        $this->pageTitle = 'App哥伦部 - 我分享的App';
        $memberID = Yii::app()->getRequest()->getQuery('memberid');
        $condition = 'CommitUserId = :CommitUserId';
        if (empty($memberID)) {
            $memberID = Yii::app()->user->id;
        }
        if ($memberID !=  Yii::app()->user->id) {
            $condition .= ' and Status = 0';
        }
        $member = User::model()->findByPk($memberID);
        if (! $member instanceof User) {
            throw new THttpException("操作错误");
        }
        $this->render(
            'myapplist',
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

    public function actionInteractionList()
    {
        $this->pageTitle = 'App哥伦部 - 互动过的App';
        $memberID = Yii::app()->getRequest()->getQuery('memberid');
        if (empty($memberID)) {
            $memberID = Yii::app()->user->id;
        }
        $member = User::model()->findByPk($memberID);
        if (! $member instanceof User) {
            throw new THttpException("链接有误或用户不存在");
        }
        $type = Yii::app()->request->getParam('type', 1);
        if ($type != 1 && $type != 2) {
            throw new THttpException("", 404);
        }
        $interactionType = $type == 1 ? 'like' : 'comment';
        $userKey = 'user_' . $memberID;
        $this->render(
            'interactionlist',
            array(
                'apps' => AppInfoList::getInteractionApp($memberID, CommonFunc::getRedis($userKey, $interactionType)),
                'type' => $type,
                'memberID' => $memberID
            )
        );
    }

    public function actionMobileInteraction()
    {
        $this->pageTitle = 'App哥伦部 - 互动过的App';
        $memberID = Yii::app()->request->getParam('memberid');
        $type = Yii::app()->request->getParam('type', 1);
        if (empty($memberID)) {
            $memberID = Yii::app()->user->id;
        }
        $member = User::model()->findByPk($memberID);
        if (! $member instanceof User) {
            throw new THttpException("操作错误");
        }
        $interactionType = $type == 1 ? 'like' : 'comment';
        $userKey = 'user_' . $memberID;
        $interactionAppIds = CommonFunc::getRedis($userKey, $interactionType);
        echo new ReturnInfo(RET_SUC, AppInfoList::getInteractionApp($memberID, $interactionAppIds));
    }
    
    public function actionQrcode()
    {
        $this->redirect(WeixinApi::getQrCode());
    }
    
    public function actionWxlogin()
    {
        if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') === false ) {
            
            throw new THttpException('', 1103);
            return;
        }
        $sessionid = session_id();
        $ref = parse_url(Yii::app()->request->urlReferrer);
        $response_url = rawurlencode("http://www.appgrub.com/user/response?bk=" . $ref['path']);
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx1ac3d9fb295a53b1&redirect_uri={$response_url}&response_type=code&scope=snsapi_userinfo&state={$sessionid}#wechat_redirect";
        $this->redirect($url);
    }
    
    public function actionResponse()
    {
        if(isset($_GET["code"])){
            //response后 sessionid会被修改，这里重新赋值
            session_id($_GET["state"]);
            $code = $_GET["code"];
            $aUser = WeixinApi::getResonseUserInfo($code);
            
            CommonFunc::checkUser('', $aUser);

            //login
            //表示从微信登录,在 framework里判断这个变量不重新生成sessionid
            $_SESSION['from_wx'] = 1;
            $identity = new UserIdentity($aUser['openid'],'');
            $identity->authenticate();
            $user = Yii::app()->user;
            $user->login($identity, 3600*24*30);
            
            $this->redirect( $_GET['bk'] );
        }
    }

    public function actionCheckUsername(){
        if(!isset($_POST['username']) || $_POST['username'] === '' ) {
            echo new ReturnInfo(RET_SUC, array('code'=>-1, 'msg'=>'昵称不能为空'));
            return ;
        }
        $username = $_POST['username'];
        $length = mb_strlen($username, 'UTF8');
        if($length>127){
            echo new ReturnInfo(RET_SUC, array('code'=>-1, 'msg'=>'昵称长度过长'));
            return ;
        }
        $criteria = new CDbCriteria();
        $criteria->condition = 'Username = :username and ID !=' . Yii::app()->user->id;
        $criteria->params = array(':username' => $username);
        $user = User::model()->find($criteria);
        if($user){
            echo new ReturnInfo(RET_SUC, array('code'=>-1, 'msg'=>'昵称已存在'));
            return ;
        }else{
            echo new ReturnInfo(RET_SUC, array('code'=>0, 'msg'=>'昵称可用'));
            return ;
        }
    }

    public function actionCheckEmail(){

        if(isset($_POST['email']) || $_POST['email'] !== '' ) {
            $email = $_POST['email'];
            if(!preg_match("/^[0-9a-zA-Z]+@(([0-9a-zA-Z]+)[.])+[a-z]{2,4}$/i", $email )){
                echo new ReturnInfo(RET_SUC, array('code'=>-1, 'msg'=>'邮箱格式不正确'));
                return ;
            }

            $criteria = new CDbCriteria();
            $criteria->condition = 'Email = :email and ID !=' . Yii::app()->user->id;
            $criteria->params = array(':email' => $email);
            $user = User::model()->find($criteria);
            if($user){
                echo new ReturnInfo(RET_SUC, array('code'=>-1, 'msg'=>'邮箱已存在'));
                return ;
            }else{
                echo new ReturnInfo(RET_SUC, array('code'=>0, 'msg'=>'邮箱可用'));
                return ;
            }
        }

        echo new ReturnInfo(RET_SUC, array('code'=>0, 'msg'=>''));
        return;
    }

    /**
     *用户设置页面
     */
    public function actionSave()
    {
        if(!isset($_POST['username']) || $_POST['username'] === '' ) {
            echo new ReturnInfo(RET_SUC, array('code'=>-1, 'msg'=>'昵称不能为空', 'type'=>'username'));
            return ;
        }
        $username = $_POST['username'];
        $length = mb_strlen($username, 'UTF8');
        if($length>127){
            echo new ReturnInfo(RET_SUC, array('code'=>-1, 'msg'=>'昵称长度过长', 'type'=>'username'));
            return ;
        }
        $criteria = new CDbCriteria();
        $criteria->condition = 'Username = :username and ID !=' . Yii::app()->user->id;
        $criteria->params = array(':username' => $username);
        $user = User::model()->find($criteria);
        if($user){
            echo new ReturnInfo(RET_SUC, array('code'=>-1, 'msg'=>'昵称已存在', 'type'=>'username'));
            return ;
        }

        $email = '';
        if(!isset($_POST['email']) || $_POST['email'] !== '' ) {
            $email = $_POST['email'];
            if(!preg_match("/^[0-9a-zA-Z]+@(([0-9a-zA-Z]+)[.])+[a-z]{2,4}$/i", $email )){
                echo new ReturnInfo(RET_SUC, array('code'=>-1, 'msg'=>'邮箱格式不正确'));
                return ;
            }

            $criteria = new CDbCriteria();
            $criteria->condition = 'Email = :email and ID !=' . Yii::app()->user->id;
            $criteria->params = array(':email' => $email);
            $user = User::model()->find($criteria);
            if($user){
                echo new ReturnInfo(RET_SUC, array('code'=>-1, 'msg'=>'邮箱已存在'));
                return ;
            }
        }

        $userId = Yii::app()->user->id;
        $editUser = User::model()->findByPk($userId);
        if (!$editUser) {
            throw new THttpException('保存失败');
        }

        $editUser->UserName = $username;
        $editUser->Email = $email;
        if(!$editUser->save()){
            throw new THttpException('保存失败');
        }
        CommonFunc::setRedis('user_'.$userId, 'userName', $editUser->UserName);
        echo new ReturnInfo(RET_SUC, array('code'=>0, 'msg'=>'保存成功'));
    }

    public function actionMyFavorite()
    {
        $this->pageTitle = 'App哥伦部 - 我收藏的App';
        $userID = Yii::app()->user->id;
        if (! empty($userID)) {
            $userKey = 'user_' . $userID;
            $interactionApp = CommonFunc::getRedis($userKey, 'favorite');
            $interactionAppIds = array_keys($interactionApp);
            $data = AppInfoList::getInteractionApp($userID, $interactionAppIds);
            $this->render('myfavorite', array('data' => $data));
        } else {
            throw new THttpException('请登陆后再查看');
        }
    }
}
