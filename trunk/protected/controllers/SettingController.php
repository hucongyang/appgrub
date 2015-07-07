<?php

class SettingController extends Controller{

	public function actionIndex(){
		
		
		if(!empty($_POST)){
			
			$id = Yii::app()->user->getId();
			
			$username = $_POST['username'];
			$userdialog = $_POST['userdialog'];
			
			$model_user = User::model()->findByPk($id);
			
			$model_user->id = $id;
			$model_user->username =$username;
			$model_user->userdialog = $userdialog;
			
			if($model_user->save()){
				echo new ReturnInfo(RET_SUC, 'success');
			}else{
				echo new ReturnInfo(RET_ERROR, '入库失败');
			}
		
			$this->render('setting');
		}else{
			
			$this->render('setting');
		}
		
	}

	
	
}