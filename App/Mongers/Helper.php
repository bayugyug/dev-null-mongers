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


define('HELPER_ENC_METHOD','aes-128-cbc');
define('HELPER_ENC_IV',    md5(sprintf("%s-%s",HELPER_ENC_METHOD,'#!@pp/m0ng3rs/r3st4pi/h3lp3r/^0123455@')));
define('HELPER_ENC_PASS',  md5(sprintf("%s-%s",HELPER_ENC_METHOD,'#!@pp/m0ng3rs/r3st4pi/h3lp3r/^9876543$')));


final class Helper{

	//more vars
    private static $connectionTimeout;
    private static $timeout;

	static $CRLF     = "\r\n";
	static $SERVER   = "dev-apps.mongers.com";

	/**
	*
	*  @get_uuid
	*
	*  @description
	*      - calculate the uniq id
	*        
	*
	*  @parameters
	*      - prefix
	*
	*  @return
	*      - uniq-id
	*              
	*/
	public function get_uuid($pfx='')
	{

			//fmt
			//sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
		    $s1        = uniqid(rand(), true);
			$s2        = uniqid(rand(), true);
			$tok1      = hash('sha1',$s1);
			$tok2      = hash('sha1',$s2);
			$tok3      = md5($s1);
			$tok4      = md5($s2);
			$tok5      = md5($tok1.$tok2);
			$ref_id    = sprintf("%s-%s-%s-%s-%s-%s",
							@strtoupper(trim($pfx)),
							substr($tok1,0,8),
							substr($tok2,-4 ),
							substr($tok3,0,4),
							substr($tok4,-4 ),
							substr($tok5,0,12));
			//give it back ;-)
			return $ref_id;
	}


	/**
	*
	*  @get_rand_str
	*
	*  @description
	*      - generate random string
	*
	*  @parameters
	*      - max
	*
	*  @return
	*      - uniq-id
	*              
	*/
	public function get_rand_str($more=5)
	{

		//lits of chars
		$bfr    = 'abcdefghijklmnopqrstuvwxyz0123456789';
		
		//sanity
		if( $more >= @strlen($bfr) )
			$more  = @strlen($bfr) - 1;
		
		//get buffer
		$ref_id = @str_shuffle( substr(@str_shuffle($bfr), 0, $more-1 ) );
		
		//give it back ;-)
		return $ref_id;
	}
	
	
	public function send($param=array())
	{
		$url     = trim($param['url']);
		$data    = $param['data'];
		$mode    = $param['method'];
		
		//default
		if(!strlen($mode))
			$mode = 'POST';
			
		//options	
		$opts    = array(
					'http' => array(
						'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
						'method'  => $mode,
						'content' => @http_build_query($data),
					),
			   );
		//send it
		$context  = @stream_context_create($opts);
		$result   = @file_get_contents($url, false, $context);
		
		//give it back
		return $result;
	}
	
	//curl
	public function sendcurl($url,$method,$data=null,$hdrs=array(),$hdr_json=false)
	{

		//method-GET
        if ($method === 'GET' && !empty($data)) {
            $url .= '?'.http_build_query($data);
        }

		//init
        $rest = curl_init();
		curl_setopt($rest, CURLOPT_URL, $url);
        curl_setopt($rest, CURLOPT_RETURNTRANSFER, 1);
		
		//set HEADERS
		$headers   = $hdrs;
		$headers[] = 'Expect: ';
		
		//method-POST
        if ($method === 'POST') {
			if($hdr_json)
				$headers[] = 'Content-Type: application/json';
            curl_setopt($rest, CURLOPT_POST, 1);
            curl_setopt($rest, CURLOPT_POSTFIELDS, $data);
        }
		//method-PUT
        if ($method === 'PUT') {
            if($hdr_json)
				$headers[] = 'Content-Type: application/json';
            curl_setopt($rest, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($rest, CURLOPT_POSTFIELDS, $data);
        }
		//method-DELETE
        if ($method === 'DELETE') {
            curl_setopt($rest, CURLOPT_CUSTOMREQUEST, $method);
        }
		
		//headers
        curl_setopt($rest, CURLOPT_HTTPHEADER, $headers);

		
		//settings
        if (!is_null(self::$connectionTimeout)) {
            curl_setopt($rest, CURLOPT_CONNECTTIMEOUT, self::$connectionTimeout);
        }
        if (!is_null(self::$timeout)) {
            curl_setopt($rest, CURLOPT_TIMEOUT, self::$timeout);
        }

		//run
        $response    = curl_exec($rest);
        $status      = curl_getinfo($rest, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($rest, CURLINFO_CONTENT_TYPE);
		$errmsg      = null;
        if (curl_errno($rest)) 
		{
            $errmsg = sprintf("ERROR [ %d ] => %s", curl_errno($rest), curl_error($rest));
        }
		
		//free
        @curl_close($rest);
		
		//sanity check
        $decoded = json_decode($response, true);
        if (isset($decoded['error'])) 
		{
			$errmsg = sprintf("ERROR [ %d ] => %s", $decoded['code'], $decoded['error']);
        }

		//give it back
        return array(
			'status'      => $status,
			'decoded'     => $decoded,
			'contentType' => $contentType,
			'errmsg'      => $errmsg,
			'response'    => $response,
		);
    }	
	
	//unzip to a dest dir
    public function unzip($filename, $extract_to_dir)
    {
        $zip = new ZipArchive;
        $res = $zip->open($filename);
        
        if ($res === TRUE) 
        {
          $result = $zip->extractTo($extract_to_dir);
          $zip->close();
          
          if(!$result) 
			  return false;
        } 
        else 
        {
          return false;
        }
		
        //good
        return true;
    }	
	
	
	
		
	 
	/**
	*
	*  @ucs2_utf8_conv
	*
	*  @description
	*      - convert hex to utf8 chars
	*
	*  @parameters
	*      - hex
	*
	*  @return
	*      - result
	*              
	*/
	public function ucs2_utf8_conv($hex='')
	{
		 
		
		//--------
		//
		// @sample: 00480065006C006C006F0020007B00200057006F0072006C0064002062C900200021 
		//          will be converted to Hello  World 
		//          <chinese character here> !
		//--------
		
		//pack-it
		$hex  = @pack('H*', $hex);                                                                                                                           
		$res  = @iconv('UCS-2', 'UTF-8', $hex);                                                                                                           
		 
		 //give it back ;-)
		 return $res;
		 
	}


	/**
	*
	*  @ucs2be_utf8_conv
	*
	*  @description
	*      - convert hex to utf8 chars
	*
	*  @parameters
	*      - hex
	*
	*  @return
	*      - result
	*              
	*/
	public function ucs2be_utf8_conv($hex='')
	{
		 
		
		//--------
		//
		// @sample: 00480065006C006C006F0020007B00200057006F0072006C0064002062C900200021 
		//          will be converted to Hello  World 
		//          <chinese character here> !
		//--------
		
		//pack-it
		$hex  = @pack('H*', $hex);                                                                                                                           
		$res  = @iconv('UCS-2BE', 'UTF-8', $hex);                                                                                                           
	 
		 
		 //give it back ;-)
		 return $res;
		 
	}


	/**
	*
	*  @utf8_ucs2be_convert
	*
	*  @description
	*      - convert utf8 to hex
	*
	*  @parameters
	*      - utf8
	*
	*  @return
	*      - result
	*              
	*/
	public function utf8_ucs2be_convert($utf8='')
	{
		 
		//convert it
		$res = @iconv('UTF-8', 'UCS-2BE', $utf8);
		//give it back ;-)
		return $res;
	}


	/**
	*
	*  @utf8_ucs2_convert
	*
	*  @description
	*      - convert utf8 to hex
	*
	*  @parameters
	*      - utf8
	*
	*  @return
	*      - result
	*              
	*/
	public function utf8_ucs2_convert($utf8='')
	{
		 
		//convert it
		$res = @iconv('UTF-8', 'UCS-2', $utf8);
	
		//give it back ;-)
		return $res;
		 
	}

	//encrypt
	public  function encrypt($word='')
	{
		
		//give it back
		return  @base64_encode(@openssl_encrypt(
					@base64_encode($word),       
					HELPER_ENC_METHOD, 
					HELPER_ENC_PASS, 
					false, 
					HELPER_ENC_IV ) );
	}
	
	//decrypt
	public  function decrypt($word='')
	{
		//give it back
		return rtrim( @base64_decode( @openssl_decrypt(
				@base64_decode($word), 
				HELPER_ENC_METHOD, 
				HELPER_ENC_PASS, 
				false, 
				HELPER_ENC_IV ) ), "\0" );
	
	}
	
	//read a file
	function io_read_utf8($fn) 
	{ 
		//slurp the entire file -> string ;-)
		$content = @file_get_contents($fn); 
		
		//utf-8 string
		return  @mb_convert_encoding($content, 'UTF-8', 
				@mb_detect_encoding( $content, 'UTF-8, ISO-8859-1', true)); 
	} 

	
	//url check
	public function regex_url_chk($url='')
	{

		//VALID URL Pattern
		$url_pat= '@(https?://[\w\d:#\@%/;$()~_?\+-=\\\.&]*)@ix';
		//chk it
		if(@preg_match($url_pat , $url, $matches) )
		{
			
			//save
			$actual    = trim($matches[1]);
			
			//give it back
			return array('status' => true, 'url' => $actual, 'raw' => $url);

		}
		
		//give it back ;-)
		return array('status' => false, 'url' => $actual, 'raw' => $url);
		
	}
	
	//email check
	public function regex_email_chk($raw='')
	{

		//VALID EMAIL Pattern
		$mail_pat = "/^([A-Z0-9._%-]+)@([A-Z0-9][A-Z0-9.-]{0,255}[A-Z0-9]\.[A-Z]{2,6})$/i";

		//give it back ;-)
		return ( @preg_match($mail_pat , $raw, $matches) ) ? (true) : (false);
	}

	//mime-type
	public function get_content_mtype($filename) 
	{
		//manual
		$mime_types = array(

				'txt' => 'text/plain',
				'csv' => 'text/csv',
				'htm' => 'text/html',
				'html'=> 'text/html',
				'php' => 'text/html',
				'css' => 'text/css',
				'js'  => 'application/javascript',
				'json'=> 'application/json',
				'xml' => 'application/xml',
				'swf' => 'application/x-shockwave-flash',
				'flv' => 'video/x-flv',

				// images
				'png' => 'image/png',
				'jpe' => 'image/jpeg',
				'jpeg'=> 'image/jpeg',
				'jpg' => 'image/jpeg',
				'gif' => 'image/gif',
				'bmp' => 'image/bmp',
				'ico' => 'image/vnd.microsoft.icon',
				'tiff'=> 'image/tiff',
				'tif' => 'image/tiff',
				'svg' => 'image/svg+xml',
				'svgz'=> 'image/svg+xml',

				// archives
				'zip' => 'application/zip',
				'rar' => 'application/x-rar-compressed',
				'exe' => 'application/x-msdownload',
				'msi' => 'application/x-msdownload',
				'cab' => 'application/vnd.ms-cab-compressed',

				// audio/video
				'mp3' => 'audio/mpeg',
				'qt'  => 'video/quicktime',
				'mov' => 'video/quicktime',

				// adobe
				'pdf' => 'application/pdf',
				'psd' => 'image/vnd.adobe.photoshop',
				'ai'  => 'application/postscript',
				'eps' => 'application/postscript',
				'ps'  => 'application/postscript',

				// ms office
				'doc' => 'application/msword',
				'rtf' => 'application/rtf',
				'xls' => 'application/vnd.ms-excel',
				'ppt' => 'application/vnd.ms-powerpoint',

				// open office
				'odt' => 'application/vnd.oasis.opendocument.text',
				'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
			);

			//ext
			$parts = @pathinfo($filename);
			$ext   = strtolower(trim($parts['extension']));
			if (@array_key_exists($ext, $mime_types)) 
			{
			   $mime = $mime_types[$ext];
			}
			elseif (@function_exists('finfo_open')) 
			{
				$finfo    = @finfo_open(FILEINFO_MIME);
				$mimetype = @finfo_file($finfo, $filename);
				@finfo_close($finfo);
				$mime     = $mimetype;
			}
			else 
			{
				$mime = 'application/octet-stream';
			}
			//give it back
			return $mime ;
	}//get mime
	
	//send email
	public function mail_send($email_from='', $email_to='', $subject='', $msg='')
	{
		//fmt
		$token      = md5(uniqid(rand(), true));
		$email_to   = trim($email_to);
		$crlf       = self::$CRLF;
		$svr        = self::$SERVER;
		
		// To send HTML mail, the Content-type header must be set
		$headers  = "MIME-Version: 1.0". $crlf;
		$headers .= "Content-type: text/html; charset=iso-8859-1". $crlf;
		$headers .= "To: $email_to". $crlf;
		$headers .= "From: $email_from" . $crlf;
		$headers .= "Reply-To: $email_from" . $crlf;
		$headers .= "Return-Path: $email_from" . $crlf;
		$headers .= "Message-ID: <$token@$svr>" . $crlf;
		$headers .= "X-Mailer: DevMongersMailer v". phpversion() . $crlf;
		
		//normal transport
		$ret      = @mail($email_to, $subject, $msg, $headers); 
		
		//give it back ;-)
		return $ret;
		
	}
	
}
?>