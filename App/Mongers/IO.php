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



final class IO{
	
	public static function write($fname,$contents='',$mode = 'a')
	{
		//ret val
		$is_ok = false;
		
		try
		{
			//mode of fopen
			$mode  = @preg_match("/^(a|append)$/i", $mode) ? ('a') :  ('w');

			//open it
			$fh = @fopen($fname, $mode);
			if($fh)
			{
				@fwrite($fh, $contents);
				@fclose($fh); 
				$is_ok  = true;
			}
			
			//give it back ;-)
			return $is_ok;	
		}
		catch(Exception $e)
		{
			return null;
		}
	}
}
?>
