<?php

/*
 * 主页控制器
 */

class AppController extends Controller{

    public function actionIndex()
    {
        $this->pageTitle = 'App哥伦部 - 发现好玩的App';
        $ua = $_SERVER['HTTP_USER_AGENT'];
        $categoryModel = new Category();
        $systemCategory = $categoryModel->getCategory();
        if(stripos($ua, 'Mobile') > 0){
            $isFollow = 0;
            $userId = Yii::app()->user->id;
            if($userId) {
                $aUser = User::model()->findByPk($userId);
                if($aUser){
                    $isFollow = $aUser->IsFollow;
                }
            }
            $this->render('app', array(
                'userId' => $userId,
                'isFollow' => $isFollow,
                'systemCategory' => $systemCategory,
                ));
            return;
        }
        $order = isset($_GET['order']) ? $_GET['order'] : 1;
        $order = CommonFunc::checkIntParam($order, 4, '');
        $type = isset($_GET['type']) ? $_GET['type'] : '';
        $type = CommonFunc::checkIntParam($type, 2, '');
        $search = isset($_GET['search']) ? $_GET['search'] : '';
        $category = isset($_GET['category']) ? $_GET['category'] : '';
        $category = CommonFunc::checkIntParam($category, Category::getMaxCategory(), '');
        if(!isset($systemCategory[$category])) {
            $category = 0;
        }
        $maxId = AppInfoList::getMaxId();
        $appsInfo = AppInfoList::getData($order, $type, $search, $category);
        $this->render(
            'app',
            array(
                'data' => $appsInfo['data'],
                'pagecount' => $appsInfo['pageCount'],
                'maxid' => $maxId,
                'order' => $order,
                'category' => $category,
                'type'  => $type,
                'search'  => $search,
                'systemCategory' => $systemCategory,
            )
        );
    }

    public function actionList()
    {
        $order = isset($_POST['order']) ? $_POST['order'] : 1;
        $order = CommonFunc::checkIntParam($order, 4, 1);
        $type = isset($_POST['type']) ? $_POST['type'] : '';
        $type = CommonFunc::checkIntParam($type, 2, '');
        $search = isset($_POST['search']) ? $_POST['search'] : '';
        $category = isset($_POST['category']) ? $_POST['category'] : '';
        $category = CommonFunc::checkIntParam($category, Category::getMaxCategory(), '');
        $maxId = isset($_POST['maxid']) ? $_POST['maxid'] : 0;
        $page = isset($_POST['page']) ? $_POST['page'] : 0;
        if(!$maxId){
            $maxId = AppInfoList::getMaxId();
        }
        $appsInfo = AppInfoList::getData($order, $type, $search, $category, $_POST['page'] * 25, 25, $maxId);
        echo new ReturnInfo(RET_SUC, array('list'=>$appsInfo['data'], 'maxid'=>$maxId, 'pageCount' => $appsInfo['pageCount']));
    }
}