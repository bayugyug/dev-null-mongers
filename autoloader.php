<?php
/*
|--------------------------------------------------------------------------
| AutoLoader
|--------------------------------------------------------------------------
|
|
*/

define('BASE_PATH', realpath(dirname(__FILE__)));

function __AUTO_LOADER__($class)
{
    $filename = BASE_PATH . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
	//chk
	if(@file_exists($filename))
	{
		require_once($filename);
	}
}

//do some magic
@spl_autoload_register('__AUTO_LOADER__');

