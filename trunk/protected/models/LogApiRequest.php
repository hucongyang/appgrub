<?php

/**
 * This is the model class for table "advertiser".
 *
 * The followings are the available columns in table 'advertiser':

 */
class LogApiRequest extends CActiveRecord
{
    
    /**
     * Returns the static model of the specified AR class.
     * @return Advertiser the static model class
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
        return 'log_api_request';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
        );
    }


    
    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
        );
    }
        
}