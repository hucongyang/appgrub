<?php

class AppPushList extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return AppPushList the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'app_push_list';
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'Id' => 'ID',
            'AppId' => 'AppId',
            'SourceId' => 'SourceId',
            'AppName' => 'App Name',
            'MainCategory' => 'Main Category',
            'CommitUserId' => 'Commit User',
            'Remarks' => 'Remarks',
            'IconUrl' => 'Icon Url',
            'IosUrl' => 'AppStore地址',
            'ScreenShoot' => 'Screen Shoot',
            'UpdateTime' => 'Update Time',
            'AppPlatform' => 'App Platform',
            'AndroidUrl' => '安卓地址',
            'OfficialWeb' => '官方网站',
            'AppInfo' => 'App Info',
            'ApkName' => 'Apk Name',
            'ApkUrl' => 'Apk Url',
            'ProcessDate' => 'Process Date',
            'Status' => 'Status',
            'FileSize' => 'FileSize',
            'PusherId' => 'PusherId'
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'reply_count' => array(self::STAT, 'AppPushListReviews', 'PushId'),
            'replies' => array(self::HAS_MANY, 'AppPushListReviews', 'PushId'),
            'category' => array(self::BELONGS_TO, 'Category', 'MainCategory'),
            'list_detail' => array(self::HAS_MANY, 'AppPushListDetail', 'PushId'),
            'source' => array(self::BELONGS_TO, 'Source', 'SourceId'),
        );
    }

    /**
     * @return array
     */
    public function scopes()
    {
        return array(
            'published' => array(
                'condition' => 'Status = 0'
            )
        );
    }
}