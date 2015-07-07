<?php

/**
 * This is the model class for table "category".
 *
 * The followings are the available columns in table 'category':
 * @property string $id
 * @property string $name
 * @property integer $AppType
 */
class Category extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */

    const MAX_CATEGORY = 10;

    public $category = array(
        1 => array(
            'name' => '游戏',
            'value' => '3014, 3015, 3016, 3019, 3020, 3021, 3024, 3056, 3057, 3069, 6014, 6024, 6025, 6026, 6029, 6030, 6031, 6034, 6035, 6038, 6039, 6040, 6064, 6065, 6066, 6067, 6068, 6069, 6070, 6071, 6072, 6092, 6093, 6094, 6095, 6096, 6097, 6098, 6099, 6100, 6101, 6111, 6112, 6113, 6114, 6115, 6115, 6116, 6133, 6137, 6143, 6146, 6168, 6169, 6175, 6193, 6194, 6220, 6230',
            'enable' => true,
        ),
        2 => array(
            'name' => '生活',
            'value' => '3047, 3048, 3049, 3050, 6001, 6012, 6015, 6023, 6033, 6037, 6045, 6054, 6057, 6058, 6081, 6083, 6084, 6085, 6086, 6102, 6110, 6121, 6126, 6128, 6134, 6147, 6150, 6161, 6166, 6177, 6179, 6185, 6188, 6196, 6197, 6198, 6201, 6205, 6207, 6208, 6210, 6209, 6214, 6219, 6221',
            'enable' => true,
        ),
        3 => array(
            'name' => '学习',
            'value' => '3054, 3055, 6017, 6018, 6021, 6022, 6046, 6053, 6073, 6090, 6091, 6104, 6122, 6131, 6138, 6139, 6153, 6171, 6181, 6186, 6190, 6191, 6211',
            'enable' => true,
        ),
        4 => array(
            'name' => '健康',
            'value' => '6013, 6020, 6060, 6117, 6136, 6148, 6195',
            'enable' => true,
        ),
        5 => array(
            'name' => '图像',
            'value' => '3046, 6008, 6041, 6049, 6052, 6082, 6118, 6123, 6142, 6183, 6204, 6215, 6225, 6229',
            'enable' => true,
        ),
        6 => array(
            'name' => '音乐',
            'value' => '3043, 6011, 6044, 6079, 6125, 6149, 6158, 6176, 6187, 6189, 6192, 6222',
            'enable' => true,
        ),
        7 => array(
            'name' => '旅行',
            'value' => '6003, 6032, 6036, 6056, 6106, 6120, 6127, 6129, 6141, 6206, 6227, 6231',
            'enable' => true,
        ),
        8 => array(
            'name' => '社交',
            'value' => '3051, 3052, 6005, 6042, 6048, 6063, 6087, 6088, 6119, 6135, 6151, 6182, 6184, 6203, 6223, 6226, 6228',
            'enable' => true,
        ),
        9 => array(
            'name' => '母婴',
            'value' => '6057, 6110, 6130',
            'enable' => true,
        ),
        10 => array(
            'name' => '其他',
            'value' => '3039, 3040, 3041, 3042, 3044, 3045, 3053, 6000, 6002, 6004, 6007, 6009, 6010, 6016, 6027, 6028, 6043, 6047, 6050, 6051, 6055, 6057, 6059, 6061, 6062, 6074, 6075, 6076, 6077, 6078, 6080, 6089, 6103, 6105, 6107, 6108, 6109, 6124, 6132, 6140, 6144, 6145, 6152, 6154, 6155, 6156, 6157, 6159, 6160, 6162, 6163, 6164, 6165, 6167, 6170, 6172, 6173, 6174, 6178, 6180, 6199, 6202, 6212, 6213, 6224',
            'enable' => true,
        ),
    );

	public function tableName()
	{
		return 'category';
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
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Category the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    static function getMaxCategory() {
        return self::MAX_CATEGORY;
    }

    public function getCategory($enable = true) {
        $category = array(
            0 => '全部'
        );
        $systemCategory = $this->category;
        foreach ($systemCategory as $key => $value) {
            if ($enable) {
                if ($value['enable']) {
                    $category[$key] = $value['name']; 
                }
            } else {
                $category[$key] = $value['name'];
            }
        }
        return $category;
    }

	public function getCategoryCondition($category) {
		$categoryCondition = '';
        $systemCategory = $this->category;
        if ($category && isset($systemCategory[$category]) && $systemCategory[$category]['enable']) {
            $categoryCondition = 'in (' . $systemCategory[$category]['value'] . ')';
        }
        return $categoryCondition;
	}

    public function getMainCategory($subcategory)
    {
        foreach ($this->category as $categoryArray) {
            if (in_array($subcategory, explode(',', $categoryArray['value']))) {
                return $categoryArray['name'];
            }
        }
    }
}
