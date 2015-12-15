<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

use  App\Mongers as Mongers;
use  App\Mongers\Mongers    as Container;
use  App\Mongers\Logger     as Xlog;
use  App\Mongers\Helper     as Helper;
use  App\Mongers\DbMySql    as DbMySql;
use  App\Mongers\PortLocker  as PortLocker;
use  App\Mongers\StatusCodes as SCodes;

echo "test......";

//auto-load
include_once('autoloader.php');


//try it
$logf= sprintf("log/%s-logger.log",@date('Y-m-d'));
$log = new Xlog($logf);
$log->info("test");

$uid = Helper::get_uuid('TEST-UID');
$log->debug($uid);

if(1)
{
	$enc = Helper::encrypt('TEST-UID');
	$dex = Helper::decrypt($enc);

	$log->debug("encrypted> $enc");
	$log->debug("decrypted> $dex");

}

//db
$DBOPTS['dbhost'] = "localhost";
$DBOPTS['dbuser'] = "libmgmt";
$DBOPTS['dbpass'] = "Libmgmt";
$DBOPTS['dbname'] = "libmgmt";

$Db = new DbMySql($DBOPTS);
$Db->dbh();
$log->debug("db-connected.....");
$Db->close();
$log->debug("db-disconnected.....");
//lock
$port = 3947;
$Lock = new PortLocker($port);
$Lock->lock();
$log->debug("locked> $port");


phpinfo();

$c = new Container();
 
// parameters
$c->mailer_class    = function () { return 'Zend_Mail'; };
$c->mailer_username = function () { return 'fabien'; };
$c->mailer_password = function () { return 'myPass'; };
$dmp = @var_export($c,1);
$log->debug("OBJ> $dmp");


$sret = Helper::mail_send('noreply@dev-apps.mongers.com', 'bayugyug@gmail.com','RE: test mail-sender', 'Aguy da bis !!!');
$log->debug("SEND-MAIL> $sret;". Helper::$SERVER); 

echo SCodes::REST_RESP_201;

//free
$Lock->unlock();
$log->debug("unlocked> $port");

