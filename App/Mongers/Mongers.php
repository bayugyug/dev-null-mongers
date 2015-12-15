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

class Mongers {
	 //attr
	 protected $attr = array();
	 
	 //magic setter
	 function __set($k, $v) 
	 { 
		$this->attr[$k]=$v; 
	 }
	 
	 //magic getter
	 function __get($k) 
	 { 
		return $this->attr[$k]($this); 
	 }
}
?>
