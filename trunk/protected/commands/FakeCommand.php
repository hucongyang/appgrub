<?php

class FakeCommand extends CConsoleCommand
{
    //配置参数
    const SHARE_NUM_MIN = 5;        //每天最少分享App个数
    const SHARE_NUM_MAX = 15;       //每天最多分享App个数
    const LIKE_NUM_MIN = 200;       //每天最少赞数
    const LIKE_NUM_MAX = 300;       //每天最多赞数
    const COMMENT_NUM_MIN = 200;    //每天最少评论数
    const COMMENT_NUM_MAX = 300;    //每天最多评论数

    private $_log = null;

    public function __construct($name, $runner) {
        parent::__construct($name, $runner);
        if ($this->_log == null) {
            $this->_log = new CronFileLogRoute('command_fake');
        }
    }

    private function _isFake($min, $max) {
        $fake_num = rand($min, $max);
        //780是从10点到23点的分钟数
        $fake_rand = rand(1, 780);
        return ($fake_rand >= (390 - $fake_num / 2) && $fake_rand <= (390 + $fake_num / 2));
    }

    private function _getRandomUserId($filterUser = array()) {
        $selectSql = 'SELECT ID FROM user WHERE Status = -1 ';
        if (!empty($filterUser)) {
            $selectSql .= 'AND ID NOT IN (' . implode(', ', $filterUser) . ') ';
        }
        $selectSql .= 'ORDER BY RAND() LIMIT 1';
        $userId = Yii::app()->db->createCommand($selectSql)->queryScalar();
        if ($userId) {
            $lastLoginTime = date('Y-m-d H:i:s', time() - 300);         //设置登陆时间为5分钟前
            $updateSql = 'UPDATE user SET LastLoginTime = "' . $lastLoginTime . '" WHERE ID='.$userId;
            Yii::app()->db->createCommand($updateSql)->execute();
        }
        return $userId;
    }

    private function _getRandomApp() {
        $twoWeeksAgo = date('Y-m-d', strtotime("-5 day"));
        $recentApps = AppInfoList::model()->findAll('CommitTime > "' . $twoWeeksAgo . '" and Status = 0');
        $chance = array();
        $appScoreInfo = array();
        $appScore = array();
        $appRealLikeMax = 0;
        $appRealCommentMax = 0;
        $appDateMax = 0;
        $weight = array(
            'like' => 30,
            'comment' => 30,
            'date' => 40
            );
        foreach($recentApps as $app) {
            $appRealLike = count($this->_getLikedRealUser($app->Id));
            if ($appRealLike > $appRealLikeMax) {
                $appRealLikeMax = $appRealLike;
            }
            $appRealComment = count($this->_getCommentedUser($app->Id, 0));
            if ($appRealComment > $appRealCommentMax) {
                $appRealCommentMax = $appRealComment;
            }
            $commitDateArr = explode(' ', $app->CommitTime);
            $commitDate = $commitDateArr[0];
            $days = (strtotime(date('Y-m-d')) - strtotime($commitDate) / 86400);
            if($days < 1) {
                $dateScore = 50;
            } else if ($days < 3) {
                $dateScore = 30;
            } else if ($days < 7) {
                $dateScore = 5;
            } else {
                $dateScore = 1;
            }
            if ($dateScore > $appDateMax) {
                $appDateMax = $dateScore;
            }
            $app_detail = AppPushListDetail::model()->find(array(
                'condition' => 'PushId=:PushId',
                'params' => array(':PushId' => $app->PushId),
                'order' => 'Date desc',
                'limit' => 1
                ));
            $appScoreInfo['app_' . $app->Id] = array(
                'id' => $app->Id,
                'like' => $appRealLike,
                'comment' => $appRealComment,
                'date' => $dateScore,
                'dayscore' => $app_detail ? $app_detail->Score : 0
                );
        }
        foreach($appScoreInfo as $key=>$value) {
            $score = round(($this->_devide($value['like'], $appRealLikeMax) * $weight['like'] + $this->_devide($value['comment'], $appRealCommentMax) * $weight['comment'] + $this->_devide($value['date'], $appDateMax) * $weight['date'])*0.7 + $value['dayscore'] * 0.3);
            if($score > 0) {
                $chance = array_merge($chance, array_fill(count($chance), $score, $value['id']));
            }
        }
        return $chance ? AppInfoList::model()->findByPk($chance[array_rand($chance)]) : '';
    }

    private function _devide($divisor, $dividend){
        return $dividend ? $divisor * 1.0 / $dividend : 0;
    }

    private function _getLikedFakeUser($appId) {
        $likedFakeUser = array();
        $likedUser = unserialize(Yii::app()->cache->get('link_' . $appId));
        if ($likedUser) {
            foreach ($likedUser as $key=>$value) {
                if (isset($value['status']) && $value['status'] == -1) {
                    $likedFakeUser[] = $key;
                }
            }
        }
        return $likedFakeUser;
    }

    private function _getLikedRealUser($appId) {
        $likedRealUser = array();
        $likedUser = CommonFunc::getRedis('link_' . $appId);
        if ($likedUser) {
            foreach ($likedUser as $key=>$value) {
                if (!isset($value['status']) || $value['status'] == 0) {
                    $likedRealUser[] = $key;
                }
            }
        }
        return $likedRealUser;
    }

    private function _getCommentedUser($appId, $status = -1) {
        $sql = 'SELECT DISTINCT(a.AuthorId) AuthorId FROM app_reviews AS a INNER JOIN user AS b on a.AuthorId = b.ID WHERE a.AppId = ' . $appId . ' AND b.Status = ' . $status;
        $userIds = Yii::app()->db->createCommand($sql)->queryAll();
        $commentedFakeUser = array();
        if ($userIds) {
            foreach ($userIds as $key => $value) {
                $commentedFakeUser[] = $value['AuthorId'];
            }
        }
        return $commentedFakeUser;
    }

    private function _getRandomComment($pushId) {
        $commentModel = AppPushListReviews::model()->findAll(array(
            'condition' => 'Status=0 and Used=0 and PushId=:PushId and Content !=""',
            'params' => array(':PushId'=>$pushId),
            'order' => new CDbExpression('RAND()'),
            'limit' => 1
            ));
        if (!$commentModel || !$commentModel[0]) {
            return false;
        }
        return $commentModel[0];
    }

    public function actionFakeShare() {
        $this->_log->setLogFile('share.log');
        if ($this->_isFake(self::SHARE_NUM_MIN, self::SHARE_NUM_MAX)) {
            $hasFilteredModel = AppHasFiltered::model()->findAll(array(
                'condition' => 'Status=1',
                'order' => new CDbExpression('RAND()'),
                'limit' => 1
                ));
            if (!$hasFilteredModel || !$hasFilteredModel[0]) {
                return ;
            }
            $userId = $this->_getRandomUserId();
            if (!$userId) {
                return ;
            }
            $hasFiltered = $hasFilteredModel[0];
            $date = date('Y-m-d H:i:s');

            $appInfoModel = new AppInfoList();
            $appInfoModel->PushId               = $hasFiltered->PushId;
            $appInfoModel->AppId                = $hasFiltered->AppId;
            $appInfoModel->SourceId             = $hasFiltered->SourceId;
            $appInfoModel->AppName              = $hasFiltered->AppName;
            $appInfoModel->MainCategory         = $hasFiltered->MainCategory;
            $appInfoModel->CommitUserId         = $userId;
            $appInfoModel->IconUrl              = $hasFiltered->IconUrl;
            $appInfoModel->AppUrl               = $hasFiltered->AppUrl;
            $appInfoModel->ScreenShoot          = $hasFiltered->ScreenShoot;
            $appInfoModel->VideoUrl             = $hasFiltered->VideoUrl;
            $appInfoModel->UpdateTime           = $date;
            $appInfoModel->CommitTime           = $date;
            $appInfoModel->OfficialWeb          = $hasFiltered->OfficialWeb;
            $appInfoModel->AppInfo              = $hasFiltered->AppInfo;
            $appInfoModel->ApkUrl               = $hasFiltered->ApkUrl;
            $appInfoModel->Sort                 = $appInfoModel->model()->getMaxSort() + 1;
            $appInfoModel->ShareType         = 1;

            $hasFiltered->Status = 0;
            
            $transaction=Yii::app()->db->beginTransaction();
            try {
                if (!$appInfoModel->save() || !$hasFiltered->save()) {
                    throw new Exception();
                }
                $this->_log->log('userId#' . $userId . '#于#' . $date .'#分享App PushId#' . $hasFiltered->PushId . '#');
                $transaction->commit();
            } catch(Exception $e) {
                $transaction->rollBack();
            }
        }
    }

    public function actionFakeLike() {
        $this->_log->setLogFile('like.log');
        if ($this->_isFake(self::LIKE_NUM_MIN, self::LIKE_NUM_MAX)) {
            $likeApp = $this->_getRandomApp();
            if (!$likeApp) {
                return;
            }
            $date = date('Y-m-d H:i:s');
            $likedFakeUser = $this->_getLikedFakeUser($likeApp->Id);
            $userId = $this->_getRandomUserId($likedFakeUser);
            $user = User::model()->findByPk($userId);
            try {
                $result = AppInfoList::up($likeApp, $user, true);
                if ($result) {
                    $this->_log->log('userId#' . $userId . '#于#' . $date .'#赞了App Id#' . $likeApp->Id . '#');
                }
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
    }

    public function actionFakeComment() {
        $this->_log->setLogFile('comment.log');
        if ($this->_isFake(self::COMMENT_NUM_MIN, self::COMMENT_NUM_MAX)) {
            $commentApp = $this->_getRandomApp();
            if (!$commentApp) {
                return;
            }
            $commentedFakeUser = $this->_getCommentedUser($commentApp->Id);
            $userId = $this->_getRandomUserId($commentedFakeUser);
            $user = User::model()->findByPk($userId);
            $comment = $this->_getRandomComment($commentApp->PushId);
            if (!$comment) {
                return;
            }
            $transaction=Yii::app()->db->beginTransaction();
            try {
                $date = date('Y-m-d H:i:s');
                $result = AppReviews::comment($commentApp->Id, $user, $comment->Content);
                $comment->Used = 1;
                if (!$comment->save() || !$result) {
                    throw new Exception();
                }
                $this->_log->log('userId#' . $userId . '#于#' . $date .'#评论App PushId#' . $commentApp->PushId . '#，评论ID为#' . $comment->Id . '#评论内容为#' . $comment->Content . '#');
                $transaction->commit();
            } catch(Exception $e) {
                $transaction->rollBack();
            }

        }
    }

    public function actionUploadSiteMap(){
        $yesterday = date('Y-m-d', strtotime("-1 day"));
        $today = date('Y-m-d');
        $yesterdayApps = AppInfoList::model()->findAll('CommitTime > "' . $yesterday . '" and CommitTime < "' . $today . '" and Status = 0');
        foreach ($yesterdayApps as $app) {
            $this->baiduSiteMap($app->Id);
        }
    }

    public function actionManualUpload($appIds) {
        $appIdArr = explode(',', $appIds);
        foreach($appIdArr as $appId) {
            echo $this->baiduSiteMap($appId) ? 1 : 0;
        }   
    }

    private function baiduSiteMap($appId){
        if(!$appId){
            return;
        }
        $urls = array(
            'http://www.appgrub.com/produce/index/' . $appId,
        );
        $api = 'http://data.zz.baidu.com/urls?site=www.appgrub.com&token=klRYMRnf3Na5fNnQ';
        $ch = curl_init();
        $options =  array(
            CURLOPT_URL => $api,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => implode("\n", $urls),
            CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
        );
        curl_setopt_array($ch, $options);
        $result = json_decode(curl_exec($ch));
        if($result->success == 1) {
            $this->_log->setLogFile('sitemap_success.log');
            $this->_log->log('推送appId：' . $appId . ' 成功。推送baidu sitemap链接：' . $urls[0]);
        } else {
            $this->_log->setLogFile('sitemap_fail.log');
            $this->_log->log('推送appId：' . $appId . ' 失败。');
        }
        return true;
    }
}
