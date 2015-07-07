<?php
class Source extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'source';
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array();
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

    static function getSourceDomains()
    {
        $sourceArray = array();
        foreach (self::model()->findAll() as $source) {
            $sourceArray = array_merge($sourceArray, explode(',', $source->Domains));
        }
        return $sourceArray;
    }

    static function getSourceName($sourceID)
    {
        $sourceObj = self::model()->findByPk($sourceID);
        if ($sourceObj instanceof Source) {
            return $sourceObj->ChnName;
        }
    }

    static function getSourceOS($sourceID)
    {
        $sourceObj = self::model()->findByPk($sourceID);
        if ($sourceObj instanceof Source) {
            return $sourceObj->OS;
        }
    }

    static function getSourceByDomain($Domain)
    {
        $appHost = parse_url($Domain);
        if (isset($appHost['host'])) {
            $criteria = new CDbCriteria;
            $criteria->addSearchCondition('Domains', $appHost['host']);
            $source = Source::model()->find($criteria);
            if($source){
                return $source->ID;
            }
        } else {
            echo new ReturnInfo(RET_SUC, array('code' => -1, 'msg' => 'App链接有误,请参考填写规则'));
            Yii::app()->end();
        }
    }
}
