<?php
class ApplistController extends Controller
{
    public function filters()
    {
        return array(
            array('application.filters.TokenCheckFilter')
        );
    }

    public function actionIndex()
    {
        echo $this->getData(1);
    }

    public function actionFastUp()
    {
        echo $this->getData(2);
    }

    public function actionMostComment()
    {
        echo $this->getData(3);
    }

    public function actionMostUp()
    {
        echo $this->getData(4);
    }
    //app详情
    public function actionAppDetail()
    {
        $appID = Yii::app()->getRequest()->getQuery('appid');
        if (empty($appID)) {
            echo new ReturnInfo(RET_ERROR, 'Argument appid passed to ' . __CLASS__ . '::' . __FUNCTION__ . '() that can not be empty.');
            Yii::app()->end();
        }
        $appInfoObj = AppInfoList::model()->findByPk($appID);
        if (empty($appInfoObj)) {
            echo new ReturnInfo(RET_ERROR, 'Argument appid passed to ' . __CLASS__ . '::' . __FUNCTION__ . '() that can not find a record.');
            Yii::app()->end();
        }
        $appInfoArray = array(
            'id' => $appInfoObj->Id,
            'appName' => $appInfoObj->AppName,
            'remarks' => $appInfoObj->Remarks,
            'commentCount' => $appInfoObj->reply_count,
            'commitUserId' => $appInfoObj->CommitUserId,
            'CommitTime' => AppInfoList::getPeriod($appInfoObj->CommitTime),
            'markName' => Source::getSourceName($appInfoObj->SourceId),
            'pushListObj' =>  isset($appInfoObj->pushListObj) && $appInfoObj->pushListObj->FileSize ? $appInfoObj->pushListObj->FileSize : "0MB",
            'iconUrl' => $appInfoObj->IconUrl,
            'appSource' => $appInfoObj->SourceId,
            'appUrl' => $appInfoObj->AppUrl,
            'appInfo' => $appInfoObj->AppInfo,
            'up' => $appInfoObj->Up,
        );
        $appID = $appInfoObj->Id;
        $count_key = 'link_'.$appID;
        $memberArray = CommonFunc::getRedis('user_' . Yii::app()->user->id);
        $appInfoArray['hasFavorited'] = false;
        $appInfoArray['isUpped'] = false;
        if (!empty($memberArray)) {
            $appInfoArray['hasFavorited'] = isset($memberArray['favorite'][$appID]) ? 1 : 0;
            if (!isset($memberArray['like'])) {
                $memberArray['like'] = array();
            }
            $appInfoArray['isUpped'] = in_array($appInfoObj->Id, $memberArray['like']) ? true : false;
        }
        $countArray = CommonFunc::getRedis($count_key);
        if (!empty($countArray)) {
            if (!isset($countArray['count'])) {
                $countArray['count'] = 0;
            }
            if (!isset($countArray['user'])) {
                $countArray['user'] = array();
            }
            $appInfoArray['count'] = $countArray['count'];
            $appInfoArray['p_user'] = AppInfoList::getLikedPeople($countArray['user'], 0);
        } else {
            $appInfoArray['count'] = 0;
            $appInfoArray['p_user'] = array();
        }
        //发信息的人
        $user = $appInfoObj->link_user;
        if (!empty($user)) {
            $appInfoArray['username'] = htmlspecialchars($user->UserName);
            if(!empty($user['Icon'])){
                $appInfoArray['userurl'] = $user->Icon;
            }else{
                $appInfoArray['userurl'] = '';
            }
        } else {
            $appInfoArray['username'] = '';
            $appInfoArray['userurl'] = '';
        }
        //信息的轮播图片（link_info表）
        $appInfoArray['imgurl'] = array();
        if (!empty($appInfoObj->VideoUrl)) {
            $appInfoArray['videoUrl'] = $appInfoObj->VideoUrl;
        }
        if (!empty($appInfoObj->ScreenShoot)) {
            $appInfoArray['imgurl'] = explode(',', $appInfoObj->ScreenShoot);
        }
        echo new ReturnInfo(RET_SUC, $appInfoArray);
    }
    //获取该APP的评论
    public function actionGetAppComment()
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
        $aReply = array();
        $replies = AppReviews::model()->with('author_icon')->together()->findAll(
            array(
                'select'=> array('Id', 'Pid', 'Content', 'UpdateTime', 'AuthorId', 'ToAuthorId'),
                'order' => 't.Pid asc, t.UpdateTime desc',
                'condition' => 'AppId = :AppId',
                'params' => array(':AppId' => $appID)
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
        echo new ReturnInfo(RET_SUC, $aReply);
    }

    public function actionGetMaxId() {
        echo new ReturnInfo(RET_SUC, AppInfoList::getMaxId());
    }

    public function getData($order)
    {
        $offset = isset($_GET['offset']) ? $_GET['offset'] : 0;
        $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
        if (!is_numeric($offset) || !is_numeric($limit)) {
            return new ReturnInfo(RET_ERROR, 'offset or limit parameter error');
        }
        $offset = (int) $offset;
        $limit = (int) $limit;
        if ($offset < 0 || $limit < 0) {
            return new ReturnInfo(RET_ERROR, 'offset or limit parameter error');
        }

        $maxId = isset($_GET['maxid']) ? $_GET['maxid'] : 0;
        if (!is_numeric($maxId)) {
            return new ReturnInfo(RET_ERROR, 'maxid parameter error');
        }
        $maxId = (int) $maxId;
        $type = isset($_GET['type']) ? $_GET['type'] : 0;
        $type = CommonFunc::checkIntParam($type, 2, '');
        $search = isset($_GET['search']) ? $_GET['search'] : '';
        $category = isset($_GET['category']) ? $_GET['category'] : '';
        $category = CommonFunc::checkIntParam($category, Category::getMaxCategory(), '');
        $appsInfo = AppInfoList::getData($order, $type, $search, $category, $offset * $limit, $limit, $maxId);
        return new ReturnInfo(
            RET_SUC,
            array(
                'offset' => $offset,
                'data' => $appsInfo['data']
            )
        );
    }
}
