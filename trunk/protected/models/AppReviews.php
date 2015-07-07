<?php

/**
 * This is the model class for table "appReview".
 *
 * The followings are the available columns in table 'notice':
 * @property string $Id
 * @property string $Pid
 * @property integer $AppId
 * @property string $Title
 * @property integer $AuthorId
 * @property string $UserRating
 * @property string $Content
 * @property string $UpdateTime
 * @property string $AuthorName
 * @property string $Status
 */
//表示发布的APP一天内被评论
define('APP_COMMENT_IN_ONE_DAY', 24*3600);
//表示发布的APP三天内被评论
define('APP_COMMENT_IN_THREE_DAYS', 3*24*3600);
//表示发布的APP一周内被评论
define('APP_COMMENT_IN_ONE_WEEK', 7*24*3600);
//APP发布一天内被评论，mostcomment所加的分数
define('APP_COMMENT_IN_ONE_DAY_MOSTCOMMENT_POINT', 5);
//APP发布三天内被评论，mostcomment所加的分数
define('APP_COMMENT_IN_THREE_DAYS_MOSTCOMMENT_POINT', 4);
//APP发布一周内被评论，mostcomment所加的分数
define('APP_COMMENT_IN_ONE_WEEK_MOSTCOMMENT_POINT', 3);
//APP发布超过一周内被评论，mostcomment所加的分数
define('APP_COMMENT_OVER_ONE_WEEK_MOSTCOMMENT_POINT', 2);
//APP被评论，sort所加的分数
define('APP_COMMENT_SORT_POINT', 2);

class AppReviews extends CActiveRecord
{
    public function init()
    {
        $this->attachEventHandler('onAfterSave', array($this, 'appComment'));
    }

    public function appComment()
    {
        if (!$this->isNewRecord) {
           return;
        }
        $userKey = 'user_' . $this->AuthorId;
        $userRedisInfo = CommonFunc::getRedis($userKey);
        if(!isset($userRedisInfo['comment'])) {
            $userRedisInfo['comment'] = array();
        }
        if (!isset($userRedisInfo['comment'][$this->AppId])) {
            $userRedisInfo['comment'][$this->AppId] = $this->AppId;
            CommonFunc::setRedis($userKey, 'comment', $userRedisInfo['comment']);
            $deltaT = abs(time() - strtotime($this->appWhichReply->UpdateTime));
            $updateTime = explode(' ', $this->appWhichReply->UpdateTime);
            $dateObj = date_diff(date_create($updateTime[0]), date_create(date('Y-m-d')));
            $intervalDays = $dateObj->days + 1;
            if ($deltaT > 0 && $deltaT <= APP_COMMENT_IN_ONE_DAY) {
                $mostCommentPoint = APP_COMMENT_IN_ONE_DAY_MOSTCOMMENT_POINT;
            } elseif ($deltaT > APP_COMMENT_IN_ONE_DAY && $deltaT <= APP_COMMENT_IN_THREE_DAYS) {
                $mostCommentPoint = APP_COMMENT_IN_THREE_DAYS_MOSTCOMMENT_POINT;
            } elseif ($deltaT > APP_COMMENT_IN_THREE_DAYS && $deltaT <= APP_COMMENT_IN_ONE_WEEK) {
                $mostCommentPoint = APP_COMMENT_IN_ONE_WEEK_MOSTCOMMENT_POINT;
            } elseif ($deltaT > APP_COMMENT_IN_ONE_WEEK) {
                $mostCommentPoint = APP_COMMENT_OVER_ONE_WEEK_MOSTCOMMENT_POINT;
            }
            $this->appWhichReply->MostComment += $mostCommentPoint;
            $this->appWhichReply->FastUp += round($mostCommentPoint * 1.0 / $intervalDays, 2);
            $this->appWhichReply->Sort += APP_COMMENT_SORT_POINT;
            $this->appWhichReply->save();
        }
        $noticeUserArray = array();
        if (empty($this->Pid)) {//评论APP
            if ($this->appWhichReply->CommitUserId != $this->AuthorId) {
                $noticeUserArray[] = array('type' => 1, 'userId' => $this->appWhichReply->CommitUserId);
            }
        } else {
            $noticeUserArray[] = array('type' => 2, 'userId' => $this->ToAuthorId);
        }
        preg_match_all("/@(.*?)\s/i", $this->Content, $matches);
        if (!empty($matches[1])) {
            $atUserArray = array_unique($matches[1]);
            foreach ($atUserArray as $atWho) {
                $user = User::model()->findByAttributes(array('UserName' => htmlspecialchars_decode($atWho)));
                $targetUserID = '';
                if ($user instanceof User) {
                    $targetUserID = $user->ID;
                }
                if ($targetUserID != $this->AuthorId && !empty($targetUserID)) {
                    $noticeUserArray[] = array('type' => 3, 'userId' => $targetUserID);
                }
            }
        }
        foreach ($noticeUserArray as $notice) {
            Notice::createNotice(
                $notice['type'],
                $notice['userId'],
                '',
                $this->appWhichReply->Id,
                $this->Id
            );
        }
    }
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'app_reviews';
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'author_icon'   => array(self::BELONGS_TO, 'User', 'AuthorId'),
            'toAuthor'       => array(self::BELONGS_TO, 'User', 'ToAuthorId'),
            'appWhichReply' => array(self::BELONGS_TO, 'AppInfoList', 'AppId'),
            'replyAuthorID' => array(self::BELONGS_TO, 'AppReviews', 'Pid'),
            'replyUser' => array(self::BELONGS_TO, 'User', 'AuthorId'),
        );
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return AppReviews the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    static function comment($appID,User $user, $content, $pid = 0, $toAuthorID = 0)
    {
        $userID = $user->ID;
        $reviews = new AppReviews();
        $reviews->Content = $content;
        $reviews->AppId = $appID;
        $reviews->AuthorId = $userID;
        $reviews->Pid = $pid;
        $reviews->ToAuthorId = $toAuthorID;
        $reviews->Status = 0;
        $reviews->UpdateTime = date('Y-m-d H:i:s');
        if ($reviews->save()) {
            return $reviews->Id;
        } else {
            throw new CDbException('系统繁忙，请稍后再试');
        }
    }

    static function filterComment($comment)
    {
        if (! empty($comment)) {
            $faceIconArray = FaceIcon::$faceIcon;
            preg_match_all("/\[(.*)\]/U", $comment, $matches);
            preg_match_all("/@(\S+)\s?/i", $comment, $matchesATag);
            $replaceSpacial = array();
            $replaceAtWho = array();
            foreach ($matchesATag[1] as $atWho) {
                $user = User::model()->findByAttributes(array('UserName' => $atWho));
                if ($user instanceof User) {
                    $replaceSpacial[] = '@' . htmlspecialchars($atWho);
                    $replaceAtWho[] = "<a target='_blank' href='/user/myzone?memberid={$user->ID}'>@" . htmlspecialchars($atWho) . " </a>";
                }
            }
            $comment = htmlspecialchars($comment);
            $comment = str_replace($replaceSpacial, $replaceAtWho, $comment);
            foreach ($matches[1] as $title) {
                foreach ($faceIconArray['hotFace'] as $key => $value) {
                    if ($title === $key) {
                        $comment = str_replace("[$title]", "<img title='$title' src='$value'>", $comment);
                        break;
                    }
                }
                foreach ($faceIconArray['faceIcons'] as $value) {
                    foreach ($value as $key => $imgPath) {
                        if ($title === $key) {
                            $comment = str_replace("[$title]", "<img title='$title' src='$imgPath'>", $comment);
                            break;
                        }
                    }
                }
            }
            return $comment;
        }
    }
}
