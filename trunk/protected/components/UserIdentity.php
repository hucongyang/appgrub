<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
	public $id;
	/**
	 * Authenticates a user.
	 * The example implementation makes sure if the username and password
	 * are both 'demo'.
	 * In practical applications, this should be changed to authenticate
	 * against some persistent user identity storage (e.g. database).
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate()
	{
		//var_dump($this);exit;
		$user = User::model()->findByAttributes(array('Openid'=>$this->username));
		//var_dump($user);die;
		//die(var_dump($user));
		if (!empty($user) && $user->Status == 0) {
            $this->errorCode =  Constant::USER_STATUS_NORMAL;
			$this->id = $user->ID;
			$this->username = $user->UserName;
		}else{
			$this->errorCode = Constant::USER_STATUS_NO_EXIST;
		}
	}
	
	public function getId()
	{
		return $this->id;
	}
}
