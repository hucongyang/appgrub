<?php

class AppPushListDetail extends CActiveRecord {

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
        return 'app_push_list_detail';
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'Id' => 'ID',
            'AppId' => 'AppID',
            'ApkName' => 'Android安装包名称',
            'DownLoadNum' => '下载量',
            'CommentNum' => '评论量',
            'Date' => '日期'
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
            'app_push_list' => array(self::BELONGS_TO, 'app_push_list', 'Id'),
        );
    }
}