<?php
/**
 * This is the model class for table "notice".
 *
 * The followings are the available columns in table 'notice':
 * @property string $ID
 * @property string $type
 * @property integer $targetUserid
 * @property string $msg
 * @property integer $createTime
 * @property string $readFlag
 * @property string $appId
 * @property string $reviewId
 */
class Notice extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'notice';
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'reviews' => array(self::BELONGS_TO, 'AppReviews', 'reviewId'),
            'user' => array(self::HAS_ONE, 'User', 'targetUserid'),
            'appRelation' => array(self::BELONGS_TO, 'AppInfoList', 'AppId')
        );
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Logmsg the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    static function createNotice($type, $targetUserID, $content = '', $appID, $reviewID)
    {
        $targetUser = User::model()->findByPk($targetUserID);
        if ($targetUser && $targetUser->Status != -1) {
            $notice = new Notice();
            $notice->type = $type;
            $notice->targetUserid = $targetUserID;
            if (!empty($content)) {
                $content = mb_substr($content, 0, 128, 'utf-8');
            }
            $notice->msg = $content;
            $notice->createTime = date('Y-m-d H:i:s');
            $notice->appId = $appID;
            $notice->reviewId = $reviewID;
            if (!$notice->save()) {
                throw new CDbException($notice->getErrors());
            }
            return $notice->ID;
        }
        return false;
    }
}
