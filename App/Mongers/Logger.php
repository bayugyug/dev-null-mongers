<?php
/**
|
|  @filename    : 
|
|  @description : 
|
|  @version     : 0.001
|
|  @author      : bayugyug@gmail.com
|
|  @date        : 
|
|
|
|  @modified    :
|  @modified-by :
|  @modified-ver:
|
|              
**/




namespace App\Mongers;




class Logger{
	
	//log types
	const XLOG_INFO  =  1;
	const XLOG_WARN  =  2;
	const XLOG_FATAL =  3;
	const XLOG_ERROR =  4;
	const XLOG_DEBUG =  5;
	
	//class vars
	private static $fh      ;
	public  static $logname ;
	public  static $pid     ;
	
	//constructor
	function __construct($fn)
	{
		self::$logname = $fn;
		self::$pid     = sprintf("%08X",mt_rand());
	}

	function __clone()
	{
	}

	public function __toString()
	{
		return __CLASS__;
	}

	//instance
	public static function log($mod=0 , $msg="")
	{
		//chk filename
		if(null === self::$logname or !strlen(self::$logname))
			self::$logname = sprintf("%s-%s.log",@date('Y-m-d'),__CLASS__);

		//chk handle
		if(null === self::$fh or false === self::$fh)
			self::$fh = @fopen(self::$logname, "a+");

		//chk mode
		switch($mod)
		{
			case self::XLOG_INFO:
			$mode = "INFO";
			break;

			case self::XLOG_WARN:
			$mode = "WARN";
			break;

			case self::XLOG_FATAL:
			$mode = "FATAL";
			break;

			case self::XLOG_ERROR:
			$mode = "ERROR";
			break;

			default:
			$mode = "DEBUG";
			break;
		}
		//fmt
		$dt  = @date("[Y-m-d H:i:s]");
		$fmt = sprintf("%s[%s][%5s] - %s\n", $dt, self::$pid, $mode, $msg);
		@fwrite(self::$fh , "$fmt");
		
		//free
		@fclose(self::$fh);
		self::$fh = null;
	}


	public static function info($msg="")
	{
		self::log(self::XLOG_INFO, $msg) ;
	}

	public static function error($msg="")
	{
		self::log(self::XLOG_ERROR, $msg) ;
	}

	public static function fatal($msg="")
	{
		self::log(self::XLOG_FATAL, $msg) ;
	}

	public static function warn($msg="")
	{
		self::log(self::XLOG_WARN, $msg) ;
	}

	public static function debug($msg="")
	{
		self::log(self::XLOG_DEBUG, $msg) ;
	}
}
?>
