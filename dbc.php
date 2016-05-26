<?php
/************* MYSQL DATABASE SETTINGS *****************
1. MySQL host (localhost or remotehost)
2. MySQL user name with ALL previleges assigned.
3. MySQL password
4. MySQL database name
********************************************************/

define ("DB_HOST","localhost"); // set database host
define ("DB_USER","gsradmin"); // set database user
define ("DB_PASS","gsradmin"); // set database password
define ("DB_NAME","gsr_dist"); // set database name

$link = mysql_connect(DB_HOST, DB_USER, DB_PASS) or die("Couldn't make connection.");
$db = mysql_select_db(DB_NAME, $link) or die("Couldn't select database");

define("COOKIE_TIME_OUT", 10); //specify cookie timeout in days (default is 10 days)
define('SALT_LENGTH', 9); // salt for password

// specify user levels
define ("ADMIN_LEVEL", 5);
define ("PROGRAM_LEVEL", 1);

// recaptcha keys
$publickey = "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";
$privatekey = "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";


/*********** PAGE PROTECT CODE ************
This code protects pages to only logged in users. 
If users have not logged in then it will redirect to login page.
*******************************************/

function page_protect() {
session_start();

global $db;

// secure against Session Hijacking by checking user agent
if (isset($_SESSION['HTTP_USER_AGENT'])) {
	
    if ($_SESSION['HTTP_USER_AGENT'] != md5($_SERVER['HTTP_USER_AGENT'])) {
		logout();
        exit;
    }
}

// before we allow sessions, we need to check authentication key - ckey and ctime stored in database

// if session not set, check for cookies set by Remember me
if (!isset($_SESSION['UserId']) && !isset($_SESSION['Username']) ) {
	
	if(isset($_COOKIE['UserId']) && isset($_COOKIE['user_key'])) {	
		// we double check cookie expiry time against stored in database
		$cookie_UserId  = filter($_COOKIE['UserId']);
		$rs_ctime = mysql_query("SELECT `ckey`,`ctime` FROM `User` WHERE `UserId` = '$cookie_UserId'") or die(mysql_error());
		list($ckey,$ctime) = mysql_fetch_row($rs_ctime);
	
	// coookie expiry
	if( (time() - $ctime) > 60*60*24*COOKIE_TIME_OUT) {
		logout();
	}
		
	 // Security check with untrusted cookies - dont trust value stored in cookie. 		
	 // We also do authentication check of the `ckey` stored in cookie matches that stored in database during login
	 if( !empty($ckey) && is_numeric($_COOKIE['UserId']) && isUserID($_COOKIE['Username']) && $_COOKIE['user_key'] == sha1($ckey)  ) {
	 	  session_regenerate_id(); //against session fixation attacks.
		  $_SESSION['UserId'] = $_COOKIE['UserId'];
		  $_SESSION['Username'] = $_COOKIE['Username'];
		  
		  /* query user level from database instead of storing in cookies */	
		  list($UserAccess) = mysql_fetch_row(mysql_query("SELECT UserAccess FROM User WHERE UserId='$_SESSION[UserId]'"));
		  $_SESSION['UserAccess'] = $UserAccess;
		  $_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT']);
	   } else {
			logout();
	   }
  } else {
		header("Location: login.php");
		exit();
	}
}
}

function filter($data) {
	$data = trim(htmlentities(strip_tags($data)));
	
	if (get_magic_quotes_gpc())
		$data = stripslashes($data);
	
	$data = mysql_real_escape_string($data);
	
	return $data;
}

function EncodeURL($url) {
	$new = strtolower(ereg_replace(' ','_',$url));
	return($new);
}

function DecodeURL($url) {
	$new = ucwords(ereg_replace('_',' ',$url));
	return($new);
}

function ChopStr($str, $len) {
    if (strlen($str) < $len)
        return $str;

    $str = substr($str,0,$len);
    if ($spc_pos = strrpos($str," "))
            $str = substr($str,0,$spc_pos);

    return $str . "...";
}

function isEmail($Email) {
	return preg_match('/^\S+@[\w\d.-]{2,}\.[\w]{2,6}$/iU', $Email) ? TRUE : FALSE;
}

function isUserID($Username) {
	if (preg_match('/^[a-z\d_]{5,20}$/i', $Username)) {
		return true;
	} else {
		return false;
	}
 }	
 
function isURL($url) {
	if (preg_match('/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i', $url)) {
		return true;
	} else {
		return false;
	}
} 

function checkPwd($x,$y) {
	if(empty($x) || empty($y) ) { 
		return false; 
	}
	
	if (strlen($x) < 4 || strlen($y) < 4) { 
		return false; 
	}

	if (strcmp($x,$y) != 0) {
		return false;
	}
	return true;
}

function GenPwd($length = 7) {
	$Password = "";
	$possible = "0123456789bcdfghjkmnpqrstvwxyz"; //no vowels
	$i = 0; 
    
	while ($i < $length) {
		$char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
    
		if (!strstr($Password, $char)) {
			$Password .= $char;
			$i++;
		}
	}
	return $Password;
}

function GenKey($length = 7) {
	$Password = "";
	$possible = "0123456789abcdefghijkmnopqrstuvwxyz"; 
	$i = 0; 
    
	while ($i < $length) {  
		$char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
    
		if (!strstr($Password, $char)) {
			$Password .= $char;
			$i++;
		}
	}
	return $Password;
}

$err = array();
$msg = array();

$host = $_SERVER['HTTP_HOST'];
$host_upper = strtoupper($host);
$login_path = @ereg_replace('admin','',dirname($_SERVER['PHP_SELF']));
$path  = rtrim($login_path, '/\\');

foreach($_POST as $key => $value) {
	$data[$key] = filter($value);
}

// filter GET values
foreach($_GET as $key => $value) {
	$get[$key] = filter($value);
}

function logout() {
global $db;
session_start();

	if(isset($_SESSION['UserId']) || isset($_COOKIE['UserId'])) {
		mysql_query("UPDATE `User` SET `ckey`= '', `ctime`= '' WHERE `UserId` = '$_SESSION[UserId]' OR  `UserId` = '$_COOKIE[UserId]'") or die(mysql_error());
	}			

//Delete the sessions
unset($_SESSION['UserId']);
unset($_SESSION['Username']);
unset($_SESSION['UserAccess']);
unset($_SESSION['HTTP_USER_AGENT']);
session_unset();
session_destroy(); 

//Delete the cookies
setcookie("UserId", '', time()-60*60*24*COOKIE_TIME_OUT, "/");
setcookie("Username", '', time()-60*60*24*COOKIE_TIME_OUT, "/");
setcookie("user_key", '', time()-60*60*24*COOKIE_TIME_OUT, "/");

header("Location: login.php");
}

//Password and salt generation
function PwdHash($pwd, $salt = null) {
    if ($salt === null) {
        $salt = substr(md5(uniqid(rand(), true)), 0, SALT_LENGTH);
    }
    else {
		$salt = substr($salt, 0, SALT_LENGTH);
    }
    return $salt . sha1($pwd . $salt);
}

function checkAdmin() {
	if($_SESSION['UserAccess'] == ADMIN_LEVEL) {
		return 1;
	} else { 
		return 0 ;
	}
}

function checkProgram() {
	if($_SESSION['UserAccess'] == PROGRAM_LEVEL) {
		return 1;
	} else { 
		return 0 ;
	}
}
?>
