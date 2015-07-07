<?php

class AppController extends Controller {

   public function filters()
   {
       return array(
           array(
               'application.filters.AdminCheckFilter',
           )
       );
   }

    /**
     *App 列表页面
     */
    public function actionShow() {
        $this->render('show');
    }

    public function actionList() {
        $os = Yii::app()->request->getParam('os', '');
        $order = Yii::app()->request->getParam('order', 'DownLoadNum');
        $where = 'b.Status = 0';
        if($os) {
            $where .= ' AND c.OS = "' . addslashes($os) . '"';
        }
        $list = Yii::app()->db->createCommand()
              ->select('a.PushId, b.IconUrl, b.AppName, b.PusherId, d.Name as MainCategory, ifnull(b.FileSize, "") as FileSize, c.OS, b.ProcessDate, max(a.CommentNum) as CommentNum, max(a.DownLoadNum) as DownLoadNum')
              ->from('app_push_list_detail a')
              ->join('app_push_list b', 'a.PushId = b.Id')
              ->join('source c', 'b.SourceId = c.ID')
              ->join('category d', 'b.MainCategory = d.ID')
              ->group('a.PushId')
              ->where($where)
              ->order($order . ' desc')
              ->queryAll();

        $models = Source::model()->findAll();
        $aPuhser = array();
        foreach($models as $m){
            $aPuhser[ $m->ID ] = $m->ChnName;
        }
        $aPuhser[ 0 ] = '';

        $new_list = array();
        foreach($list as $row){
            $row['ChnName'] = $aPuhser[ intval($row['PusherId']) ];
            $new_list[] = $row;
        }

        echo new ReturnInfo(0, $new_list);
    }

    /**
     *App详情页面
     */
    public function actionDetail()
    {
        $id = Yii::app()->getRequest()->getQuery('id');
        $link = AppPushList::model()->findByPk($id);
        $aInfo = array();
        //app_push_list表的基本信息
        $aInfo['Id'] = $link->Id;
        $aInfo['AppName'] = $link->AppName;
        $aInfo['IconUrl'] = $link->IconUrl;
        $aInfo['SourceId'] = $link->SourceId;
        $aInfo['AppUrl'] = $link->AppUrl;
        $aInfo['AppInfo'] = $link->AppInfo;
        $aInfo['ProcessDate'] = $link->ProcessDate;
        $aInfo['FileSize'] = $link->FileSize;
        $aInfo['ChnName'] = $link->source->ChnName;
        //App所属类型
        $category = $link->category;
        if(!empty($category)) {
            $aInfo['CategoryName'] = $category->Name;
        }
        else{
            $aInfo['CategoryName'] = '';
        }

        //信息的轮播图片（link_info表）
        $aInfo['imgurl'] = array();
        if (!empty($link->ScreenShoot)) {
            foreach (explode(',', $link->ScreenShoot) as $linkinfo) {
                $_i = array();
                $_i['imgurl'] = $linkinfo;
                $aInfo['imgurl'][] = $_i;
            }
        }
        //App的下载总量/评论总量
        $criteria = new CDbCriteria();
        $criteria->select = 'PushId, MAX(DownLoadNum) as DownLoadNum, MAX(CommentNum) as CommentNum';
        $criteria->condition = 'PushId = :Id';
        $criteria->params = array(':Id' => $id);
        $models_num = AppPushListDetail::model()->find($criteria);
        if(!empty($models_num->DownLoadNum)){
            $aInfo['DownLoadNum'] = $models_num->DownLoadNum;
        }else{
            $aInfo['DownLoadNum'] = 0;
        }
        if(!empty($models_num->CommentNum)) {
            $aInfo['CommentNum'] = $models_num->CommentNum;
        }else{
            $aInfo['CommentNum'] = 0;
        }
        //App评论内容
        $aReply = array();
        $replies = AppPushListReviews::model()->findAll(
            array(
                'select' => array('Id', 'PushId', 'Title', 'Content', 'UpdateTime'),
                'condition' => 'PushId = :Id',
                'order' => 't.UpdateTime desc',
                'params' => array(':Id' => $id)
            )
        );
        if(!empty($replies)) {
            foreach($replies as $reply) {
                $aReply[] = array(
                    'Id' => $reply->Id,
                    'PushId' => $reply->PushId,
                    'Title' => $reply->Title,
                    'Content' => $reply->Content,
                    'UpdateTime' => $reply->UpdateTime
                );
            }
        }
        //App 下载量/评论量 分析
        $criteria2 = new CDbCriteria();
        $criteria2->select = 'PushId, DownLoadNum, CommentNum, Date';
        $criteria2->condition = 'PushId = :Id';
        $criteria2->params = array(':Id' => $id);
        $criteria2->order = 'Date';
        $models_date  = AppPushListDetail::model()->findAll($criteria2);
        $num = array();
        foreach($models_date as $row) {
            $num['Date'][] = $row->Date;
            $num['DownLoadNum'][] = intval($row->DownLoadNum);
            $num['CommentNum'][] = intval($row->CommentNum);
        }
        if(!empty($num['Date'])){
            $Date_json = json_encode($num['Date']);
        }else{
            $Date_json = 0;
        }
        if(!empty($num['DownLoadNum'])){
            $DownLoadNum_json = json_encode($num['DownLoadNum']);
        }else{
            $DownLoadNum_json = 0;
        }
        if(!empty($num['CommentNum'])){
            $CommentNum_json = json_encode($num['CommentNum']);
        }else{
            $CommentNum_json = 0;
        }

        $this->render('detail', array('data' => $aInfo,
            'Date_json' => $Date_json,
            'DownLoadNum_json' => $DownLoadNum_json,
            'CommentNum_json' => $CommentNum_json,
            'aReply' => $aReply));
    }

    /**
     * 删除App操作
     * @throws THttpException
     */
    public function actionDelete()
    {
        if(!isset($_POST['id']) || empty($_POST['id'])){
            throw new THttpException('勾选项不能为空');
        }
        $id = $_POST['id'];
        if(!is_array($id)){
            $id = array($id);
        }
        try {
            foreach($id as $row) {
                $models_app = AppPushList::model()->findByPk($row);
                if(!($models_app instanceof AppPushList)) {
                    throw new THttpException('操作失败');
                }
                $models_app->Status = '-1';
                if(!$models_app->save()) {
                    throw new Exception();
                }
                AppPushListReviews::model()->deleteAll('PushId=' . $row);
                AppPushListDetail::model()->find('PushId=' . $row)->delete();
            }
            echo new ReturnInfo(RET_SUC, '删除成功');
        }catch (Exception $e) {
            throw new THttpException('操作失败');
        }
    }

    /**
     * 添加App操作
     * @throws THttpException
     */
    public function actionAdd()
    {
        if(!isset($_POST['id']) || empty($_POST['id'])){
            throw new THttpException('勾选项不能为空');
        }
        $id = $_POST['id'];
        if(!is_array($id)){
            $id = array($id);
        }
        try {
            foreach($id as $row) {
                $models_app = AppPushList::model()->findByPk($row);
                if(!($models_app instanceof AppPushList)) {
                    throw new THttpException('操作失败');
                }
                $models_filtered = new AppHasFiltered();
                $models_filtered->PushId                = $models_app->Id;
                $models_filtered->AppId                 = $models_app->AppId;
                $models_filtered->SourceId              = $models_app->SourceId;
                $models_filtered->AppName               = $models_app->AppName;
                $models_filtered->MainCategory          = $models_app->MainCategory ;
                $models_filtered->IconUrl               = $models_app->IconUrl;
                $models_filtered->AppUrl                = $models_app->AppUrl;
                $models_filtered->ScreenShoot           = $models_app->ScreenShoot;
                $models_filtered->VideoUrl              = $models_app->VideoUrl;
                $models_filtered->MoveTime              = new CDbExpression('NOW()');
                $models_filtered->OfficialWeb           = $models_app->OfficialWeb;
                $models_filtered->Status                = 1;
                $models_filtered->AppInfo               = $models_app->AppInfo;
                $models_filtered->ApkUrl                = $models_app->ApkUrl;

                $models_app->Status = 1;

                $transaction=Yii::app()->db->beginTransaction();
                try {
                    if(!$models_filtered->save() || !$models_app->save()){
                        throw new Exception();
                    }
                    $transaction->commit();
                } catch(Exception $e) {
                    $transaction->rollBack();
                }
            }
            echo new ReturnInfo(RET_SUC, '添加成功');
        }
        catch (Exception $e) {
            throw new THttpException('操作失败');
        }
    }

    public function actionEditComment(){
        if(!isset($_POST['id']) || empty($_POST['id'])){
            throw new THttpException('评论ID为空！');
        }
        if(!isset($_POST['content']) || $_POST['content'] === ''){
            throw new THttpException('评论内容为空！');
        }

        $comment = AppPushListReviews::model()->findByPk($_POST['id']);
        if(!$comment) {
            throw new THttpException('评论不存在！');
        }
        $comment->Content = $_POST['content'];
        if(!$comment->save()){
            throw new THttpException('编辑评论失败！');
        }
        echo new ReturnInfo(0, '保存成功！');
        return;
    }

    public function actionDeleteComment(){
        if(!isset($_POST['id']) || empty($_POST['id'])){
            throw new THttpException('评论ID为空！');
        }
        $id = $_POST['id'];
        if (!is_array($id)) {
            $id = array($id);
        }
        try{
            $criteria =new CDbCriteria; 
            $criteria->addInCondition('Id', $id); 
            AppPushListReviews::model()->deleteAll($criteria);
            echo new ReturnInfo(RET_SUC, '删除评论成功');
        }
        catch (Exception $e) {
            throw new THttpException('操作失败');
        }
    }

    public function actionEditAppInfo(){
        if(!isset($_POST['app_id']) || empty($_POST['app_id'])) {
            throw new THttpException('应用介绍ID为空！');
        }
        if(!isset($_POST['content']) || $_POST['content'] === ''){
            throw new THttpException('应用介绍内容为空！');
        }

        $app_info = AppPushList::model()->findByPk($_POST['app_id']);
        if(!$app_info) {
            throw new THttpException('应用介绍不存在！');
        }
        $app_info->AppInfo = $_POST['content'];
        if(!$app_info->save()){
            throw new THttpException('编辑应用介绍失败！');
        }
        echo new ReturnInfo(0, '保存成功！');
        return;
    }

    public function actionEditCategory()
    {
        $this->render('editcategory');
    }

    public function actionAppInfoList()
    {
        $limit = Yii::app()->request->getParam('limit');
        $offset = Yii::app()->request->getParam('offset');
        $os = Yii::app()->request->getParam('os');
        $appName = Yii::app()->request->getParam('app_name');
        $mainCategory = Yii::app()->request->getParam('main_category');
        $subcategory = Yii::app()->request->getParam('subcategory');
        if (! (is_numeric($limit) && is_numeric($offset))) {
            $limit = 10;
            $offset = 0;
        }
        $where = 'a.Status = 0';
        if ($appName) {
            $where .= ' AND a.AppName like "%'.$appName.'%"';
        }
        if($os) {
            $where .= ' AND s.OS = "' . addslashes($os) . '"';
        }

        if ($mainCategory&& !$subcategory) {
            $categoryModel = new Category();
            $where .= ' AND a.MainCategory ' . $categoryModel->getCategoryCondition($mainCategory);
        }
        if ($subcategory && $mainCategory) {
            $where .= ' AND a.MainCategory = ' . $subcategory;
        }
        $list = Yii::app()->db->createCommand()
            ->select('a.Id, a.IconUrl, a.AppName, c.ID as categoryID, c.Name as subcategory, s.OS, a.UpdateTime')
            ->from('app_info_list a')
            ->join('source s', 'a.SourceId = s.ID')
            ->join('category c', 'a.MainCategory = c.ID')
            ->where($where)
            ->order('Id' . ' desc')
            ->limit($limit, $offset)
            ->queryAll();
        $data = array();
        $categoryModel = new Category();
        foreach ($list as $row) {
            $row['mainCategory'] = $categoryModel->getMainCategory($row['categoryID']);
            $data[] = $row;
        }
        echo new ReturnInfo(0, $data);
    }

    public function actionGetMainCategory()
    {
        $category = Category::model()->getCategory();
        unset($category[0]);
        echo new ReturnInfo(0, $category);
    }

    public function actionGetSubcategory()
    {
        $mainCategory = Yii::app()->request->getParam('main_category');
        $category = CommonFunc::checkIntParam($mainCategory, Category::getMaxCategory(), '');
        $categoryModel = new Category();
        $systemCategory = $categoryModel->category;
        if ($category) {
            if(! isset($systemCategory[$category])) {
                throw new THttpException('分类有误！');
            }
        }
        $categoryCondition = $categoryModel->getCategoryCondition($category);
        $command = Yii::app()->db->createCommand();
        $command->select('ID,Name');
        $command->from('category');
        $command->where('ID '.$categoryCondition);
        echo new ReturnInfo(0, $command->queryAll());
    }

    public function actionAlertAppInfoListCategory()
    {
        $mainCategory = Yii::app()->request->getParam('main_category');
        $subcategory = Yii::app()->request->getParam('subcategory');
        $mainCategory = CommonFunc::checkIntParam($mainCategory, Category::getMaxCategory(), '');
        $categoryModel = new Category();
        $systemCategory = $categoryModel->category;
        if(! isset($systemCategory[$mainCategory])) {
            throw new THttpException('一级分类有误');
        }
        if (! in_array($subcategory, explode(',', $systemCategory[$mainCategory]['value']))) {
            throw new THttpException('子分类有误');
        }
        $app = AppInfoList::model()->findByPk(Yii::app()->request->getParam('appID'));
        if (! $app instanceof AppInfoList) {
            throw new THttpException('应用ID有误');
        }
        $app->MainCategory = $subcategory;
        if ($app->save()) {
            echo new ReturnInfo(0, 1);
        } else {
            throw new THttpException('修改失败');
        }
    }

    public function actionGetTotalPage()
    {
        $where = 'a.Status = 0';
        $os = Yii::app()->request->getParam('os');
        $appName = Yii::app()->request->getParam('app_name');
        $mainCategory = Yii::app()->request->getParam('main_category');
        if ($appName) {
            $where .= ' AND a.AppName like "%'.$appName.'%"';
        };
        if($os) {
            $where .= ' AND s.OS = "' . addslashes($os) . '"';
        }
        if ($mainCategory != -1) {
            $categoryModel = new Category();
            $where .= ' AND a.MainCategory ' . $categoryModel->getCategoryCondition($mainCategory);
        }
        $count = Yii::app()->db->createCommand()
            ->select('count(a.Id) as count')
            ->from('app_info_list a')
            ->join('source s', 'a.SourceId = s.ID')
            ->join('category c', 'a.MainCategory = c.ID')
            ->where($where)
            ->queryRow();
        echo new ReturnInfo(0, $count['count']);
    }
}