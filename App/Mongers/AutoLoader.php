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




final class AutoLoader{
	

	public function __toString()
	{
		return __CLASS__;
	}

	
	public static function autoload($className)
    {
		$clsnm     = @split("::",$classname);
		$thisName  = $clsnm[1];
        $thisClass = str_replace(__NAMESPACE__.'\\', '', __CLASS__);
		$thisClass = str_replace(__NAMESPACE__.'\\', '', $thisName);
        $baseDir   = __DIR__;

        if (substr($baseDir, -strlen($thisClass)) === $thisClass) {
            $baseDir = substr($baseDir, 0, -strlen($thisClass));
        }

        $className = ltrim($className, '\\');
        $fileName  = $baseDir;
        $namespace = '';
        if ($lastNsPos = strripos($className, '\\')) {
            $namespace = substr($className, 0, $lastNsPos);
            $className = substr($className, $lastNsPos + 1);
            $fileName  .= str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
        }
        $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

        if (file_exists($fileName)) {
            require $fileName;
        }
    }


    public static function registerAutoloader($classname)
    {
        spl_autoload_register(__NAMESPACE__ . sprintf("\\%s::autoload",$classname));
    }


}
?>
