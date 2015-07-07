<?php
/*
 *
 * ret_code
 * :
 * >=0
 * 为正常
 * -1
 * 针对用户提交内容的验证错误信息；要求把所有的验证逻辑判断一次返回；
 * 错误信息会直接使用alert显示在页面上
 * 特殊错误类型：
 * -2
 * 错误会传递给JS中CALLBACK函数处理
 * -99
 * 网络连接超时，请稍后再试!
 * -100
 * 权限验证已过期或者您还没有登录，请点击“确定”按钮重新登录!
 */
class ReturnInfo
{
	public $ret_code;
	public $ret_msg;
	public $error_code;

	function __construct($ret_code = 0 , $ret_msg = '', $error_code = 0)
	{
		$this->ret_code = $ret_code;
		$this->ret_msg = $ret_msg;
		$this->error_code = $error_code;
	}

	public function set_info($ret_code , $ret_msg = '', $error_code = 0)
	{
		$this->ret_code = $ret_code;
		$this->ret_msg = $ret_msg;
		$this->error_code = $error_code;
	}

	public function getMessage()
	{
		return $this->ret_msg;
	}

	public function getCode()
	{
		return $this->ret_code;
	}

	public function __toString()
	{
		return json_encode($this);
	}

}
?>
