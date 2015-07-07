<?php

class AppHasFiltered extends CActiveRecord
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
        return 'app_has_filtered';
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'Id' => 'ID',
            'Pushid' => 'Push Id',
            'AppId' => 'AppId',
            'SourceId' => 'SourceId',
            'AppName' => 'App Name',
            'MainCategory' => 'Main Category',
            'IconUrl' => 'Icon Url',
            'AppUrl' => 'App地址',
            'CommentCount' => 'Comment Count',
            'ScreenShoot' => 'Screen Shoot',
            'UpdateTime' => 'Update Time',
            'CommitTime' => 'Commit Time',
            'MoveTime' => 'Move Time',
            'OfficialWeb' => '官方网站',
            'Status' => 'Status',
            'AppInfo' => 'App Info',
            'ApkUrl' => 'Apk Url'
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

        );
    }

    /**
     * @return array
     */
    public function scopes()
    {
        return array(

        );
    }
}