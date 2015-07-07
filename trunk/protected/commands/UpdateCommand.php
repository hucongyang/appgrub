<?php

class UpdateCommand extends CConsoleCommand
{

    private $_log = null;

    public function __construct($name, $runner) {
        parent::__construct($name, $runner);
        if ($this->_log == null) {
            $this->_log = new CronFileLogRoute('command_update');
        }
    }
    public function actionUpdateFastUp(){
        $this->_log->setLogFile('fastup.log');
        $sql = 'UPDATE app_info_list set FastUp = round(FastUp * ((DATEDIFF(curdate(), LEFT(UpdateTime, 10)) + 1) * 1.0 / (DATEDIFF(curdate(), LEFT(UpdateTime, 10)) + 1)), 2)';
        $result = Yii::app()->db->createCommand($sql)->execute();
        $result_str = ($result>=0) ? '成功' : '失败';
        $this->_log->log(date('Y-m-d') . '更新FastUp字段' . $result_str . '。');
    }
}
