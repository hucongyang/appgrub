<?php
class CategoryController extends Controller
{
    public function filters()
    {
        return array(
            array('application.filters.TokenCheckFilter')
        );
    }

    public function actionList()
    {
        $categoryModel = new Category();
        $systemCategory = $categoryModel->getCategory();
        echo new ReturnInfo(RET_SUC, $systemCategory);
    }
}
