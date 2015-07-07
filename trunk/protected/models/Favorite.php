<?php
/**
 * This is the model class for table "notice".
 *
 * The followings are the available columns in table 'notice':
 * @property integer $id
 * @property integer $userID
 * @property integer $appID
 * @property string $time
 */
class Favorite extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'favorite';
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

    static function favoriteBoolean($appID, $userID, $isFavorite)
    {
        if (!is_bool($isFavorite)) {
            throw new ErrorException('Argument 3 passed to ' . __CLASS__ . '::' . __FUNCTION__ . '() that must be a boolean');
        }
        $linkKey = 'link_' . $appID;
        $userKey = 'user_' . $userID;
        $datetime = date('Y-m-d H:i:s');
        //哪些人收藏了app
        $appArray = CommonFunc::getRedis($linkKey, 'appFavorite');
        $memberArray = CommonFunc::getRedis($userKey, 'favorite');
        if ($isFavorite) {
            if (isset($memberArray[$appID])) {
                throw new ErrorException('你已经收藏过该App');
            }
            $appArray[$userID] = array('time' => $datetime);
            CommonFunc::setRedis($linkKey, 'appFavorite', $appArray);
            $memberArray[$appID] = array('appID' => $appID, 'time' => $datetime);
            CommonFunc::setRedis($userKey, 'favorite', $memberArray);
        } else {
            if (!isset($memberArray[$appID])) {
                throw new ErrorException('取消收藏失败');
            }
            if (isset($appArray[$userID])) {
                unset($appArray[$userID]);
                CommonFunc::setRedis($linkKey, 'appFavorite', $appArray);
            }
            if (isset($memberArray[$appID])) {
                unset($memberArray[$appID]);
                CommonFunc::setRedis($userKey, 'favorite', $memberArray);
            }
        }
        return true;
    }
}
