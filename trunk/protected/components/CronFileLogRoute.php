<?php
/**
 * @class: CronFileLogRoute
 * @data: 2011-8-25
 * @author: Jeremy
 */

class CronFileLogRoute extends CComponent
{
	/**
	 * @var integer number of log messages
	 */
	private $_maxFileSize=10240; // in KB
	/**
	 * @var integer number of log files used for rotation
	 */
	private $_maxLogFiles=10;
	/**
	 * @var string directory storing log files
	 */
	private $_logPath='';
	/**
	 * @var string log file name
	 */
	private $_logFile='cron.log';

	public function __construct($logPath = null, $logFile = null){
		$this->init($logPath, $logFile);
	}

	/**
	 * Initializes the route.
	 * This method is invoked after the route is created by the route manager.
	 */
	public function init($logPath, $logFile)
	{
		if($logPath){
			$this->setLogPath($logPath);
		}
		if($logFile){
			$this->setLogFile($logFile);
		}
		if($this->getLogPath()===null)
			$this->setLogPath(Yii::app()->getRuntimePath());
	}

	/**
	 * @return string directory storing log files. Defaults to application runtime path.
	 */
	public function getLogPath()
	{
		return $this->_logPath;
	}

	/**
	 * @param string $value directory for storing log files.
	 * @throws CException if the path is invalid
	 */
	public function setLogPath($value)
	{
	    $logpath = CONFIG_LOGS_PATH . DIRECTORY_SEPARATOR . Date('Ym');
	    if(!is_dir($logpath)){
	    	if(!mkdir($logpath , 0777)){
	            throw new CException(Yii::t('yii','CFileLogRoute.logPath "{path}" does not point to a valid directory. Make sure the directory exists and is writable by the Web server process.',
	                                        array('{path}'=>$value)));
	        }
			chmod($logpath, 0777);
	    }
	    if($value !== '' && $value !== null){
	    	$logpath  .= DIRECTORY_SEPARATOR . $value;
		    if(!is_dir($logpath)){
		    	if(!mkdir($logpath , 0777)){
		            throw new CException(Yii::t('yii','CFileLogRoute.logPath "{path}" does not point to a valid directory. Make sure the directory exists and is writable by the Web server process.',
		                                        array('{path}'=>$value)));
		        }
				chmod($logpath, 0777);
		    }
		}
	    
	    $this->_logPath = $logpath;
	    
		if($this->_logPath===false || !is_dir($this->_logPath) || !is_writable($this->_logPath))
			throw new CException(Yii::t('yii','CFileLogRoute.logPath "{path}" does not point to a valid directory. Make sure the directory exists and is writable by the Web server process.',
				array('{path}'=>$value)));
	}

	/**
	 * @return string log file name. Defaults to 'application.log'.
	 */
	public function getLogFile()
	{
		return $this->_logFile;
	}

	/**
	 * @param string $value log file name
	 */
	public function setLogFile($value)
	{
		$this->_logFile=$value;
	}

	/**
	 * @return integer maximum log file size in kilo-bytes (KB). Defaults to 1024 (1MB).
	 */
	public function getMaxFileSize()
	{
		return $this->_maxFileSize;
	}

	/**
	 * @param integer $value maximum log file size in kilo-bytes (KB).
	 */
	public function setMaxFileSize($value)
	{
		if(($this->_maxFileSize=(int)$value)<1)
			$this->_maxFileSize=1;
	}

	/**
	 * @return integer number of files used for rotation. Defaults to 5.
	 */
	public function getMaxLogFiles()
	{
		return $this->_maxLogFiles;
	}

	/**
	 * @param integer $value number of files used for rotation.
	 */
	public function setMaxLogFiles($value)
	{
		if(($this->_maxLogFiles=(int)$value)<1)
			$this->_maxLogFiles=1;
	}

	/**
	 * Saves log messages in files.
	 * @param array $logs list of log messages
	 */
	protected function processLogs($message,$level,$category,$time)
	{
		$logFile=$this->getLogPath().DIRECTORY_SEPARATOR.$this->getLogFile();
		if(@filesize($logFile)>$this->getMaxFileSize()*1024)
			$this->rotateFiles();
		$fp=@fopen($logFile,'a');
		@flock($fp,LOCK_EX);
		@fwrite($fp,$this->formatLogMessage($message,$level,$category,$time));
		@flock($fp,LOCK_UN);
		@fclose($fp);
	}

	/**
	 * Rotates log files.
	 */
	protected function rotateFiles()
	{
		$file=$this->getLogPath().DIRECTORY_SEPARATOR.$this->getLogFile();
		$max=$this->getMaxLogFiles();
		for($i=$max;$i>0;--$i)
		{
			$rotateFile=$file.'.'.$i;
			if(is_file($rotateFile))
			{
				// suppress errors because it's possible multiple processes enter into this section
				if($i===$max)
					@unlink($rotateFile);
				else
					@rename($rotateFile,$file.'.'.($i+1));
			}
		}
		if(is_file($file))
			@rename($file,$file.'.1'); // suppress errors because it's possible multiple processes enter into this section
	}
	
	protected function formatLogMessage($message,$level,$category,$time)
	{
		$msg = @date('Y/m/d H:i:s',$time)." [$level] [$category] $message\n";
		if(defined("MEDIAV_GAILEO_TESTCASE") && MEDIAV_GAILEO_TESTCASE === TRUE)
            echo $msg;
		return $msg;
	}
	
	
	//记录日志
	public function log($message,$level='info',$category='application')
	{
		$this->processLogs($message,$level,$category,microtime(true));
	}
	
	
}
