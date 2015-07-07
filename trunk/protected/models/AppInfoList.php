<?php

/**
 * This is the model class for table "app_info_list".
 *
 * The followings are the available columns in table 'app_info_list':
 * @property string $Id
 * @property string $AppId
 * @property integer $SourceId
 * @property string $AppName
 * @property integer $MainCategory
 * @property string $CommitUserId
 * @property string $Remarks
 * @property string $IconUrl
 * @property string $IosUrl
 * @property integer $Up
 * @property integer $Rank
 * @property integer $CommentCount
 * @property string $ScreenShoot
 * @property string $UpdateTime
 * @property string $AndroidUrl
 * @property string $AppPlatform
 * @property string $OfficialWeb
 * @property string $Status
 * @property string $MostComment
 * @property string $FastUp
 * @property string $Sort
 */
//表示发布的APP一天内被点赞
define('APP_UP_IN_ONE_DAY', 24*3600);
//表示发布的APP三天内被点赞
define('APP_UP_IN_THREE_DAYS', 3*24*3600);
//表示发布的APP一周内被点赞
define('APP_UP_IN_ONE_WEEK', 7*24*3600);
//APP发布一天内被点赞，fastup所加的分数
define('APP_UP_IN_ONE_DAY_POINT', 5);
//APP发布三天内被点赞，fastup所加的分数
define('APP_UP_IN_THREE_DAYS_POINT', 4);
//APP发布一周内被点赞，fastup所加的分数
define('APP_UP_IN_ONE_WEEK_POINT', 3);
//APP发布超过一周内被点赞，fastup所加的分数
define('APP_UP_OVER_ONE_WEEK_POINT', 2);

//APP发布一天内被点赞，sort所加的分数
define('APP_UP_IN_ONE_DAY_SORT_POINT', 3);
//APP发布三天内被点赞，sort所加的分数
define('APP_UP_IN_THREE_DAYS_SORT_POINT', 2);
//APP发布一周内被点赞，sort所加的分数
define('APP_UP_IN_ONE_WEEK_SORT_POINT', 2);
//APP发布超过一周内被点赞，sort所加的分数
define('APP_UP_OVER_ONE_WEEK_SORT_POINT', 1);


class AppInfoList extends CActiveRecord
{
    public $maxSort;
    private $oldRecord = null;
    public function init()
    {
        $this->attachEventHandler('onAfterFind', array($this, 'setOldRecord'));
        $this->attachEventHandler('onBeforeSave', array($this, 'appLike'));
    }

    public function setOldRecord()
    {
        $this->oldRecord = $this->attributes;
        return $this->oldRecord;
    }

    public function getOldRecord()
    {
        return $this->oldRecord;
    }

    //点赞是，给fastUp和sort字段加上相应的值
    public function appLike()
    {
        $oldRecord = $this->oldRecord;
        if ($this->Up != $oldRecord['Up']) {
            $deltaT = abs(time() - strtotime($this->UpdateTime));
            $updateTime = explode(' ', $this->UpdateTime);
            $dateObj = date_diff(date_create($updateTime[0]), date_create(date('Y-m-d')));
            $intervalDays = $dateObj->days + 1;
            if ($deltaT > 0 && $deltaT <= APP_UP_IN_ONE_DAY) {
                $upPoint = APP_UP_IN_ONE_DAY_POINT;
                $sortPoint = APP_UP_IN_ONE_DAY_SORT_POINT;
            } elseif ($deltaT > APP_UP_IN_ONE_DAY && $deltaT <= APP_UP_IN_THREE_DAYS) {
                $upPoint = APP_UP_IN_THREE_DAYS_POINT;
                $sortPoint = APP_UP_IN_THREE_DAYS_SORT_POINT;
            } elseif ($deltaT > APP_UP_IN_THREE_DAYS && $deltaT <= APP_UP_IN_ONE_WEEK) {
                $upPoint = APP_UP_IN_ONE_WEEK_POINT;
                $sortPoint = APP_UP_IN_ONE_WEEK_SORT_POINT;
            } else {
                $upPoint = APP_UP_OVER_ONE_WEEK_POINT;
                $sortPoint = APP_UP_OVER_ONE_WEEK_SORT_POINT;
            }
            if ($this->Up > $oldRecord['Up'] ) {
                $this->FastUp += round($upPoint * 1.0 / $intervalDays, 2);
                $this->Sort += $sortPoint;
            } else{
                $this->FastUp -= round($upPoint * 1.0 / $intervalDays, 2);
                $this->Sort -= $sortPoint;
            }
            return $this;
        }
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'app_info_list';
    }

    public function scopes()
    {
        return array(
            'published' => array(
                'condition' => 'Status = 0'
            )
        );
    }
    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('SourceId, MainCategory, Up, Rank, CommentCount', 'numerical', 'integerOnly' => true, 'message' => '{attribute}只能是数字'),
            array('AppId, CommitUserId', 'length', 'max'=>20, 'tooLong' => '{attribute}最大长度为{max}'),
            array('AppName', 'length', 'max'=>128, 'tooLong' => '{attribute}最大长度为{max}'),
            array('Remarks', 'length', 'max'=>1024, 'tooLong' => '{attribute}最大长度为{max}'),
            array('IconUrl', 'length', 'max'=>256, 'tooLong' => '{attribute}最大长度为{max}'),
            array('CommitTime', 'required', 'message' => '{attribute}不能为空'),
            array('OfficialWeb, AppUrl', 'regexUrl'),
        );
    }
    /**
     * validate IosUrl, AndroidUrl, Official
     */
    public function regexUrl($attribute, $params)
    {
        switch ($attribute) {
            case 'OfficialWeb' :
                if (!empty($this->OfficialWeb)) {
                    //"(^(http[s]?:\\/\\/(www\\.)?|ftp:\\/\\/(www\\.)?|www\\.){1}([0-9A-Za-z-\\.@:%_\+~#=]+)+((\\.[a-zA-Z]{2,3})+)(/(.)*)?(\\?(.)*)?$)";
                    $regex = "(^(http|ftp|https):\/\/[\w\-_]+(\.[\w\-_]+)+([\w\-\.,@?^=%&amp;:/~\+#]*[\w\-\@?^=%&amp;/~\+#])?$)";
                    if (!preg_match($regex, $this->OfficialWeb)) {
                        throw new THttpException("App官方网站地址格式有误");
                        //$this->addError('OfficialWeb', $attribute."地址错误");
                    }
                }
                break;
            case 'AppUrl' :
                if (!empty($this->AppUrl)) {
                    $appHost = parse_url($this->AppUrl);
                    if (isset($appHost['host'])) {
                        $appMarketArray = Source::getSourceDomains();
                        if (! in_array($appHost['host'], $appMarketArray)) {
                            throw new THttpException("App链接有误");
                            //$this->addError('AppUrl', $attribute."地址错误");
                        }
                    } else {
                        throw new THttpException($attribute."App链接有误");
                        //$this->addError('AppUrl', $attribute."地址错误");
                    }
                }
                break;
        }
    }
    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'reply_count' => array(self::STAT, 'AppReviews', 'AppId', 'condition'=>'Status=0'),
            'replies'   => array(self::HAS_MANY, 'AppReviews', 'AppId', 'condition'=>'Status=0'),
            'link_info' => array(self::HAS_MANY, 'LinkInfo', 'linkid'),
            'link_user' => array(self::BELONGS_TO, 'User', 'CommitUserId'),
            'pushListObj' => array(self::BELONGS_TO, 'AppPushList', 'PushId'),
            'operationSystem' => array(self::BELONGS_TO, 'Source', 'SourceId')
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'Id' => 'ID',
            'AppId' => 'AppID',
            'SourceId' => 'Source',
            'AppName' => 'App Name',
            'MainCategory' => 'Main Category',
            'CommitUserId' => 'Commit User',
            'Remarks' => 'Remarks',
            'IconUrl' => 'Icon Url',
            'AppUrl' => 'App链接',
            'OfficialWeb' => 'App官方网站',
            'Up' => '赞',
            'Rank' => 'Rank',
            'CommentCount' => 'Comment Count',
            'ScreenShoot' => 'Screen Shoot',
            'UpdateTime' => 'Update Time',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.
        $criteria=new CDbCriteria;
        $criteria->compare('Id',$this->Id,true);
        $criteria->compare('AppId',$this->AppId,true);
        $criteria->compare('SourceId',$this->SourceId);
        $criteria->compare('AppName',$this->AppName,true);
        $criteria->compare('MainCategory',$this->MainCategory);
        $criteria->compare('CommitUserId',$this->CommitUserId,true);
        $criteria->compare('Remarks',$this->Remarks,true);
        $criteria->compare('IconUrl',$this->IconUrl,true);
        $criteria->compare('AppUrl',$this->AppUrl,true);
        $criteria->compare('Score',$this->Score);
        $criteria->compare('Rank',$this->Rank);
        $criteria->compare('CommentCount',$this->CommentCount);
        $criteria->compare('ScreenShoot',$this->ScreenShoot,true);
        $criteria->compare('UpdateTime',$this->UpdateTime,true);
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function getMaxSort()
    {
        $criteria = new CDbCriteria;
        $criteria->select='MAX(Sort) as maxSort';
        $max = self::model()->find($criteria);
        return $max['maxSort'];
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return AppInfoList the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    static function getMaxId(){
        $criteria = new CDbCriteria();
        $criteria->order = 'Id desc';
        $criteria->limit = 1;
        $appInfo = AppInfoList::model()->published()->find($criteria);
        return $appInfo ? $appInfo->Id : 0; 
    }

    static function getData($order = 1, $type = null, $search = '', $category = '', $offset = 0, $limit = 25, $maxid = 0)
    {
        //app的排序
        switch ($order) {
            case 1:
                $order = 'Sort desc';
                break;
            case 2:
                $order = 'FastUp desc';
                break;
            case 3:
                $order = 'MostComment desc';
                break;
            case 4:
                $order = 'Up desc';
                break;
            default:
                $order = 'Sort desc';
                break;
        }
        $criteria = new CDbCriteria();
        $criteria->order = $order;
        //app类型：ios or android
        if ($type) {
            if ($type == 1) {
                $criteria->addCondition("SourceId = 1");
            } elseif ($type == 2) {
                $criteria->addCondition("SourceId > 1");
            }
        }
        //搜索条件
        if (! empty($search)) {
            $condition = implode(
                ',',
                array_map(
                    function ($category) {
                        return $category->ID;
                    },
                    Category::model()->findAll(
                        array(
                            "select" => "ID",
                            'condition' => 'Name like :search',
                            'params' => array(':search' => "%" . $search . "%")
                        )
                    )
                )
            );
            $where = "AppName like '%" . addslashes($search) ."%'";
            if (!empty($condition)) {
                $where .= " or MainCategory in (" . $condition . ")";
            }
            $criteria->addCondition($where);
        }
        //app的分类
        if ( !empty($category) ) {
            $categoryModel = new Category();
            $categoryCondition = $categoryModel->getCategoryCondition($category);
            if($categoryCondition){
                $criteria->addCondition("MainCategory " . $categoryCondition);
            }
        }
        if ($maxid) {
            $criteria->addCondition("Id <= :maxid");
            $criteria->params[':maxid'] = $maxid;
        }
        $data = array();
        $data['pageCount'] = ceil(( AppInfoList::model()->published()->count($criteria)) / $limit);
        $criteria->offset = $offset;
        $criteria->limit = $limit;
        $appsInfo = AppInfoList::model()->published()->findAll($criteria);
        $userID = Yii::app()->user->id;
        $aData = self::parseData($appsInfo, $userID);
        $data['data'] = $aData;
        return $data;
    }

    static function parseData($apps, $userId) {
        $aData = array();
        $userFavorite = CommonFunc::getRedis('user_' . $userId, 'favorite');
        foreach ($apps as $row) {
            $_t = array();
            $_t['Id'] = $row->Id;
            $_t['OS'] = $row->operationSystem->OS ? $row->operationSystem->OS : '';
            if( empty($row->Remarks) ){
                $_t['Remarks'] = strlen($row->AppInfo) > 144 ? mb_substr(strip_tags($row->AppInfo), 0, 144, 'utf-8') . '...' : strip_tags($row->AppInfo);
            }else{
                $_t['Remarks'] = strlen($row->Remarks) > 144 ? mb_substr(strip_tags($row->Remarks), 0, 144, 'utf-8') . '...' : strip_tags($row->Remarks);
            }
            $_t['AppName'] = htmlspecialchars($row->AppName);
            $_t['AppUrl'] = $row->AppUrl;
            $_t['IconUrl'] = $row->IconUrl;
            $_t['CommentCount'] = $row->reply_count;
            $_t['CommitTime'] = self::getPeriod($row->CommitTime);
            $_t['AppInfo'] = strlen($row->AppInfo) > 144 ? mb_substr(strip_tags($row->AppInfo), 0, 144, 'utf-8') .'...' : strip_tags($row->AppInfo);
            $_t['commitUser'] = $row->link_user->ID;
            $_t['Status'] = $row->Status;
            if ($row->link_user) {
                $_t['username'] = htmlspecialchars($row->link_user->UserName);
                $_t['userurl'] = $row->link_user->Icon;
            } else {
                $_t['username'] = '';
                $_t['userurl'] = '';
            }
            //赞的总数redis
            $_t['isUpped'] = false;
            $_t['hasFavorited'] = false;
            if (!empty($userFavorite)) {
                $_t['hasFavorited'] = isset($userFavorite[$row->Id]) ? 1 : 0;
            }
            $count_value = CommonFunc::getRedis('link_'.$row->Id);
            if(!empty($count_value)){
                $count = 0;
                if (isset($count_value['count'])) {
                    $count = $count_value['count'];
                }
                $_t['count'] = $count;
                if ($userId && isset($count_value['user'][$userId])) {
                    $_t['isUpped'] = true;
                }
            } else {
                $_t['count'] = 0;
            }
            $aData[] = $_t;
        }
        return $aData;
    }

    static function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    static function getPeriod($time)
    {
        if (self::validateDate($time)) {
            $deltaTime = time() - strtotime($time);
            if ($deltaTime < 60) {
                $period = '刚刚';
            } elseif ($deltaTime >= 60 && $deltaTime < 3600) {
                $period = floor($deltaTime / 60).'分钟前';
            } elseif ($deltaTime >= 3600 && $deltaTime < 86400) {
                $period = floor($deltaTime / 3600).'小时前';
            } elseif ($deltaTime >= 86400 && $deltaTime < 2592000) {
                $period = floor($deltaTime / 86400).'天前';
            } elseif ($deltaTime >= 2592000 && $deltaTime < 15552000) {
                $period = floor($deltaTime / 2592000).'个月前';
            } else {
                $period = date('Y-m-d',strtotime($time));
            }
        } else {
            $period = '';
        }
        return $period;
    }

    static function getQRCode($appID, $path, $type)
    {
        if (!empty($appID)) {
            if ($type == 1) {
                $codeName = 'share_';
            } else if ($type == 2) {
                $codeName = 'download_';
            }
            $filename = Yii::app()->basePath . "/../../upload/qrcode/" . $codeName . $appID . ".png";
            if(!file_exists($filename))
            {
                $serverName = $_SERVER['SERVER_NAME'];
                if(stripos($_SERVER['SERVER_NAME'], 'www') === false) {
                    $serverName = 'www.' . $serverName;
                }
                $url = 'http://' . $serverName . $path . $appID;
                QRcode::png($url, $filename, "L", 3, 0);
            }
            header('Content-type: image/png');
            return file_get_contents($filename);
        }
    }

    static function up(AppInfoList $app, User $user, $isUpped)
    {
        if (!is_bool($isUpped)) {
            throw new ErrorException('Argument 3 passed to ' . __CLASS__ . '::' . __FUNCTION__ . '() that must be a boolean');
        }
        $appID = $app->Id;
        $userID = $user->ID;
        $linkKey = 'link_' . $appID;
        $userKey = 'user_' . $userID;
        $datetime = date('Y-m-d H:i:s');
        $appArray = CommonFunc::getRedis($linkKey);
        if (empty($appArray)) {
            $appArray = array('user' => array(), 'count' => 0);
        }
        $memberArray = CommonFunc::getRedis($userKey, 'like');
        if ($isUpped) {
            if (isset($appArray['user'][$userID])) {
                throw new ErrorException('你已经赞过该App');
            }
            if (! isset($appArray['count'])) {
                $appArray['count'] = 0;
            }
            $appArray['count'] += 1;
            $appArray['user'][$userID] = array('ID' => $userID, 'time' => $datetime, 'status' => $user->Status);
            CommonFunc::setRedis($linkKey, '', $appArray);
            $memberArray[$appID] = $appID;
            CommonFunc::setRedis($userKey, 'like', $memberArray);
            $app->Up +=  1;
        } else {
            if (! isset($appArray['user'][$userID])) {
                throw new ErrorException('取消赞失败');
            }
            if (isset($appArray['user'][$userID])) {
                $appArray['count'] -= 1;
                unset($appArray['user'][$userID]);
                CommonFunc::setRedis($linkKey, '', $appArray);
                $app->Up -= 1;
            }
            if (isset($memberArray[$appID])) {
                unset($memberArray[$appID]);
                CommonFunc::setRedis($userKey, 'like', $memberArray);
            }
        }
        if ($app->save()) {
            return true;
        } else {
            throw new CDbException($app->getErrors());
        }
    }

    static function getLikedPeople(array $arr, $start, $length = 50)
    {
        $likedPeople = array_slice($arr, $start, $length);
        $likedPeopleArray = array();
        foreach($likedPeople as $key => $value) {
            $upUserRedis = CommonFunc::getRedis('user_' . $value['ID']);
            if(!isset($upUserRedis['userHeadUrl']) || !isset($upUserRedis['userName'])) {
                $userModal = User::model()->findByPk($value['ID']);
            }
            $likedPeopleArray[] = array(
                'userId' => $value['ID'],
                'userurl' => (!isset($upUserRedis['userHeadUrl']) || $upUserRedis['userHeadUrl'] === '') ? $userModal->Icon : $upUserRedis['userHeadUrl'],
                'username' => (!isset($upUserRedis['userName']) || $upUserRedis['userName'] === '') ? htmlspecialchars($userModal->UserName) : htmlspecialchars($upUserRedis['userName']),
            );
        }
        return $likedPeopleArray;
    }

    static function getInteractionApp($memberID, $appIds)
    {
        $apps = array();
        if (! empty($appIds)) {
            $appsModel = self::getAppsByIds($appIds);
            $publicApps = array();
            foreach ($appsModel as $app) {
                if ($app->Status == 0) {
                    $publicApps[$app->Id] = $app;
                }
            }
            $interactionApps = array();
            $interactionOrder = array_reverse($appIds);
            foreach($interactionOrder as $interactionAppId => $id){
                if (isset($publicApps[$id])) {
                    $interactionApps[] = $publicApps[$id];
                }
            }
            $apps = AppInfoList::parseData($interactionApps, $memberID);
        }
        return $apps;
    }

    static function getAppsByIds($Ids, $status = 0) {
        $conditions = "Id in (".implode(',', $Ids).")";
        if($status !== '') {
            $conditions .= " and Status=" .$status;
        }
        return AppInfoList::model()->published()->findAll(
            array('condition' => $conditions)
        );
    }
}
