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



class PortLocker{
	
	//vars
	private $_Port     = 34567;
	private $_Sock;
	private $_Res;
	
	//new
	function  PortLocker($port=34567)
	{
		//init
		$this->_Port    = (! $port ) ?  (34567) : ($port);
		$this->_Sock    = null;
		$this->_Res     = null;
	}

	/**
	*
	*  @lock
	*
	*  @description
	*      - lock the port
	*
	*  @parameters
	*      - 
	*
	*  @return
	*      - true/false
	*              
	*/
	public function  lock( )
	{ 
		// create socket
		$this->_Sock = @socket_create(AF_INET, SOCK_STREAM, 0); 
		$bind        = @socket_bind($this->_Sock, '127.0.0.1', $this->_Port) ;
		
		// Start listening for connections 
		@socket_listen($this->_Sock); 
		$res = (! $this->_Sock  or ! $bind ) ? (false) : (true);
		//give it back
	   	return $res;
	}


	/**
	*
	*  @unlock
	*
	*  @description
	*      - unlock the port
	*
	*  @parameters
	*      - 
	*
	*  @return
	*      -  
	*              
	*/
	public function  unlock( )
	{
	
		//free
		@socket_shutdown($this->_Sock, 2);
		@socket_close($this->_Sock);
		
 	} 
	
}
?>
