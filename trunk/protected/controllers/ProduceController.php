<?php
/**
 * 评论控制器
 * @author admin
 */
class ProduceController extends Controller
{
    public function filters()
    {
        return array(
            array(
                'application.filters.LoginCheckFilter + Comment,ReplyComment,Like,Dislike,Favorite,Unfavorite',
            )
        );
    }

    public function actionIndex()
    {
        $id = Yii::app()->getRequest()->getQuery('id'); 
        $app = AppInfoList::model()->published()->findByPk($id);
        if (! $app instanceof AppInfoList) {
            throw new THttpException('查看的App审核中或不存在');
        }
        $this->pageTitle = 'App哥伦部 - 《' . $app->AppName . '》';
        $aInfo = array();
        //app_info_list表的基本信息
        $aInfo['Id'] = $app->Id;
        $aInfo['AppName'] = $app->AppName;
        $aInfo['Remarks'] = htmlspecialchars($app->Remarks);
        $aInfo['IconUrl'] = $app->IconUrl;
        $aInfo['CommentCount'] = $app->reply_count;
        $aInfo['CommitUserId'] = $app->CommitUserId;
        $aInfo['AppUrl'] = CommonFunc::transUrl($app->AppUrl);
        $aInfo['CommitTime'] = AppInfoList::getPeriod($app->CommitTime);
        $aInfo['AppInfo'] = $app->AppInfo;
        $aInfo['SourceId'] = $app->SourceId;
        $aInfo['markName'] = Source::getSourceName($app->SourceId);
        $aInfo['pushListObj'] = isset($app->pushListObj) && $app->pushListObj->FileSize ? $app->pushListObj->FileSize : "0MB";
        //app_info_list赞的总数
        $appID = $app->Id;
        $count_key = 'link_'.$appID;
        $memberArray = CommonFunc::getRedis('user_' . Yii::app()->user->id);
        $aInfo['hasFavorited'] = false;
        $aInfo['isUpped'] = false;
        if (!empty($memberArray)) {
            $aInfo['hasFavorited'] = isset($memberArray['favorite'][$appID]) ? 1 : 0;
            if (!isset($memberArray['like'])) {
                $memberArray['like'] = array();
            }
            $aInfo['isUpped'] = in_array($app->Id, $memberArray['like']) ? true : false;
        }
        $countArray = CommonFunc::getRedis($count_key);
        if (!empty($countArray)) {
            if (!isset($countArray['count'])) {
                $countArray['count'] = 0;
            }
            if (!isset($countArray['user'])) {
                $countArray['user'] = array();
            }
            $aInfo['count'] = $countArray['count'];
            $aInfo['p_user'] = AppInfoList::getLikedPeople($countArray['user'], 0);
        } else {
            $aInfo['count'] = 0;
            $aInfo['p_user'] = array();
        }
        //发信息的人
        $user = $app->link_user;
        if (!empty($user)) {
            $aInfo['username'] = htmlspecialchars($user->UserName);
            if(!empty($user['Icon'])){
                $aInfo['userurl'] = $user->Icon;
            }else{
                $aInfo['userurl'] = '';
            }
        } else {
            $aInfo['username'] = '';
            $aInfo['userurl'] = '';
        }
        //信息的轮播图片（link_info表）
        $aInfo['imgurl'] = array();
        if (!empty($app->VideoUrl)) {
            $aInfo['videoUrl'] = $app->VideoUrl;
        }
        if (!empty($app->ScreenShoot)) {
            $aInfo['imgurl'] = explode(',', $app->ScreenShoot);
        }
        $aReply = array();
        $replies = AppReviews::model()->with('author_icon')->together()->findAll(
            array(
                'select'=> array('Id', 'Pid', 'Content', 'UpdateTime', 'AuthorId', 'ToAuthorId'),
                'order' => 't.Pid asc, t.UpdateTime desc',
                'condition' => 'AppId = :AppId',
                'params' => array(':AppId' => $id)
            )
        );
        if (!empty($replies)) {
            foreach ($replies as $single_reply) {
                $toAuthorNanme = '';
                if ($single_reply->ToAuthorId) {
                    $toAuthorNanme = htmlspecialchars($single_reply->toAuthor->UserName);
                }
                $replay_info = array(
                    'Id'            => $single_reply->Id,
                    'Content'       => $single_reply->Content,
                    'Pid'           => $single_reply->Pid,
                    'AuthorName'    => htmlspecialchars($single_reply->replyUser->UserName),
                    'UpdateTime'    => AppInfoList::getPeriod($single_reply->UpdateTime),
                    'AuthorIcon'    => $single_reply->author_icon->Icon,
                    'AuthorId'      => $single_reply->AuthorId,
                    'ToAuthorID'    => $single_reply->ToAuthorId,
                    'ToAuthorName'  => $toAuthorNanme,
                );
                if ($single_reply->Pid != 0) {
                    if(!isset($aReply[$single_reply->Pid])) {
                        $aReply[$single_reply->Pid] = array(
                            'children' => array($replay_info)
                        );
                    } else {
                        if(!isset($aReply[$single_reply->Pid]['children'])){
                            $aReply[$single_reply->Pid]['children'] = array();
                        }
                        $aReply[$single_reply->Pid]['children'][] = $replay_info;
                    }
                } else {
                    if(!isset($aReply[$single_reply->Id])) {
                        $aReply[$single_reply->Id] = $replay_info;
                        $aReply[$single_reply->Id]['children'] = array();
                    } else {
                        $aReply[$single_reply->Id] = array_merge($aReply[$single_reply->Id], $replay_info);
                    }
                }
            }
        }
        foreach ($aReply as $key => $value) {
            if(!isset($value['Id']) || empty($value['Id'])){
                unset($aReply[$key]);
            }
        }
        $aInfo['replies'] = $aReply;
        $this->render('detail', array('data' => $aInfo));
    }
    //回复评论
    public function actionComment()
    {
        $appID = Yii::app()->request->getParam('appID');
        $content = Yii::app()->request->getParam('content');
        $replayId = Yii::app()->request->getParam('replayId', 0);

        if (! Yii::app()->request->isPostRequest
            || empty($content)
            || empty($appID)
        ) {
            throw new THttpException('评论失败');
        }
        $app = AppInfoList::model()->findByPk($appID);
        if (! ($app instanceof AppInfoList)) {
            throw new THttpException('评论失败');
        }
        //被回复的评论
        $pid = 0;
        $toAuthorID = 0;
        if ($replayId) {
            $replay = AppReviews::model()->findByPk($replayId);
            if (! ($replay instanceof AppReviews)) {
                throw new THttpException('评论失败');
            }
            if($replay->Pid) {
                $pid = $replay->Pid;
            } else {
                $pid = $replay->Id;
            }
            if($replay->AuthorId) {
                $toAuthorID = $replay->AuthorId;
            }
        }
        
        if ($toAuthorID) {
            $toAuthor = User::model()->findByPk($toAuthorID);
            if (! ($toAuthor instanceof User)) {
                throw new THttpException('评论失败');
            }
        }
        $userID = Yii::app()->user->id;
        $user = User::model()->findByPk($userID);
        if (! ($user instanceof User) || $userID == $toAuthorID) {
            throw new THttpException('评论失败');
        }
        $content = AppReviews::filterComment($content);
        $id = AppReviews::comment($app->Id, $user, $content, $pid, $toAuthorID);
        if ($id) {
            $return = array();
            $return['id'] = $id;
            $return['pid'] = $pid;
            $return['authorID'] = $userID;
            $return['content'] = $content;
            $return['username'] = htmlspecialchars($user->UserName);
            $return['authorIcon'] = $user->Icon;
            $return['toAuthorID'] = $toAuthorID;
            $return['toAuthorUserName'] = $toAuthorID ?  htmlspecialchars($toAuthor->UserName) : '';
            echo new ReturnInfo(RET_SUC, $return);
        }
    }

    public function actionDeleteReply(){
        $appID = Yii::app()->request->getParam('appID');
        $replyID = Yii::app()->request->getParam('replyID');
        if (! Yii::app()->request->isPostRequest
            || empty($replyID)
            || empty($appID)
        ) {
            throw new THttpException('删除失败');
        }
        $app = AppInfoList::model()->findByPk($appID);
        if (! ($app instanceof AppInfoList)) {
            throw new THttpException('删除失败');
        }
        //被回复的评论
        $reply = AppReviews::model()->findByPk($replyID);
        if (! ($reply instanceof AppReviews)) {
            throw new THttpException('评论不存在或已被删除');
        }
        $userID = Yii::app()->user->id;
        $user = User::model()->findByPk($userID);
        if (! ($user instanceof User)) {
            throw new THttpException('删除失败');
        }
        if($userID != $reply->AuthorId || $appID != $reply->AppId) {
            throw new THttpException('删除失败');
        }

        $deleteReplyNum = $reply->delete();
        if (!$deleteReplyNum) {
            throw new THttpException('删除失败');
        }
        $deleteChildrenReplyNum = 0;
        //如果是父评论，则删除其子评论
        if($reply->Pid == 0) {
            $deleteChildrenReplyNum = AppReviews::model()->deleteAll('Pid=:pid', array(':pid' => $reply->Id));
        }
        
        echo new ReturnInfo(RET_SUC, array('num' => $deleteReplyNum + $deleteChildrenReplyNum));
    }

    public function actionGetQRCode()
    {
        $appID = Yii::app()->getRequest()->getQuery('id');
        $app = AppInfoList::model()->published()->findByPk($appID);
        if (! $app instanceof AppInfoList) {
            throw new ErrorException('该App不存在');
        }
        echo AppInfoList::getQRCode($appID, '/produce/index/', 1);
    }

    public function actionLike()
    {
        $appID = Yii::app()->request->getParam('id');
        if (empty($appID)) {
            throw new THttpException('操作失败');
        }
        $app = AppInfoList::model()->findByPk($appID);
        if (empty($app) || $app->Status > 0) {
            throw new THttpException('操作失败');
        }
        $userID = Yii::app()->user->id;
        $user = User::model()->findByPk($userID);
        if (empty($user)) {
            throw new THttpException('操作失败');
        }
        try {
            if (AppInfoList::up($app, $user, true)) {
                echo new ReturnInfo(RET_SUC, 'success');
            }
        } catch (Exception $e) {
            
            throw new THttpException( $e->getMessage() );
        }
    }

    public function actionDislike()
    {
        $appID = Yii::app()->request->getParam('id');
        if (empty($appID)) {
            throw new THttpException('操作失败');
        }
        $app = AppInfoList::model()->findByPk($appID);
        if (empty($app) || $app->Status > 0) {
            throw new THttpException('操作失败');
        }
        $userID = Yii::app()->user->id;
        $user = User::model()->findByPk($userID);
        if (empty($user)) {
            throw new THttpException('操作失败');
        }
        try {
            if (AppInfoList::up($app, $user, false)) {
                echo new ReturnInfo(RET_SUC, 'success');
            }
        } catch (Exception $e) {
            throw new THttpException($e->getMessage());
        }
    }

    public function actionGetDownloadQRCode()
    {
        echo AppInfoList::getQRCode(Yii::app()->getRequest()->getQuery('id'), '/produce/findScan/', 2);
    }

    public function actionFindScan()
    {
        $appID = Yii::app()->getRequest()->getQuery('id');
        if (! empty($appID)) {
            $app = AppInfoList::model()->findByPk($appID);
            if ($app instanceof AppInfoList && $app->Status == 0) {
                $appSource = strtolower(Source::getSourceOS($app->SourceId));
                if (CommonFunc::isIOS() && $appSource != 'ios') {
                    throw new THttpException("对不起，您访问的链接不是IOS系统的App");
                } else if (CommonFunc::isAndroid() && $appSource != 'android') {
                    throw new THttpException("对不起，您访问的链接不是Android系统的App");
                }
                $url = $app->AppUrl;
                if (empty($url)) {
                    throw new THttpException("您访问的URL不存在");
                }
                if(CommonFunc::isWeiXin() && $appSource == 'android'){
                    echo '<div style="position:fixed;left:0;right:0;top:0;bottom:0;background-color:#000;opacity:0.85;"><img style="float:right:width:300px" src="/img/download_notice_android.png"/></div>';
                    Yii::app()->end();
                }
                $this->redirect($url);
            }
        }
    }

    public function actionFavorite()
    {
        $appID = $_POST['id'];
        if (empty($appID)) {
            throw new THttpException('操作失败');
        }
        $app = AppInfoList::model()->findByPk($appID);
        if (empty($app) || $app->Status > 0) {
            throw new THttpException('操作失败');
        }
        $userID = Yii::app()->user->id;
        $user = User::model()->findByPk($userID);
        if (empty($user)) {
            throw new THttpException('操作失败');
        }
        try {
            if (Favorite::favoriteBoolean($appID, $userID, true)) {
                echo new ReturnInfo(RET_SUC, 'success');
            }
        } catch (Exception $e) {
            throw new THttpException($e->getMessage());
        }
    }

    public function actionUnfavorite()
    {
        $appID = $_POST['id'];
        if (empty($appID)) {
            throw new THttpException('操作失败');
        }
        $app = AppInfoList::model()->findByPk($appID);
        if (empty($app) || $app->Status > 0) {
            throw new THttpException('操作失败');
        }
        $userID = Yii::app()->user->id;
        $user = User::model()->findByPk($userID);
        if (empty($user)) {
            throw new THttpException('操作失败');
        }
        try {
            if (Favorite::favoriteBoolean($appID, $userID, false)) {
                echo new ReturnInfo(RET_SUC, 'success');
            }
        } catch (Exception $e) {
            throw new THttpException('操作失败');
        }
    }

    public function actionGetLikedPeople()
    {
        if (isset($_POST['appID']) && isset($_POST['start'])) {
            $userArray = CommonFunc::getRedis('link_' . $_POST['appID'], 'user');
            if (! empty($userArray)) {
                echo new ReturnInfo(RET_SUC, AppInfoList::getLikedPeople($userArray, $_POST['start']));
                return;
            }
        }
        echo new ReturnInfo(-1, '网络连接超时，请稍后重试！');
        return;
    }

    public function actionSearchUser()
    {
        $appID = Yii::app()->request->getParam('appID');
        $app = AppInfoList::model()->findByPk($appID);
        if (!$app instanceof AppInfoList) {
            throw new THttpException("操作错误");
        }
        $array = AppReviews::model()->findAll(
            array(
                'select'    => array('AuthorId'),
                'distinct'  => true,
                'condition' => 'AppId = :AppId',
                'params'    => array(':AppId' => $appID)
            )
        );
        $userArray = array();
        foreach ($array as $row) {
            $userArray[] = array(
                'encode_name' => htmlspecialchars($row->author_icon->UserName),
                'icon' => $row->author_icon->Icon,
                'name' => $row->author_icon->UserName
            );
        }
        echo new ReturnInfo(RET_SUC, $userArray);
    }

    public function actionGetFaceIcon()
    {
        echo new ReturnInfo(RET_SUC, FaceIcon::$faceIcon);
    }
}
