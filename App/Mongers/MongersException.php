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


use Exception;

class MongersException extends Exception
{
    /**
     *
     * @param string     $message  Message for the Exception.
     * @param int        $code     Error code.
     * @param \Exception $previous Previous Exception.
     */
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
?>
