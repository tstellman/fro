<?php 
include 'dbc.php';
include('Mail.php');
include('Mail/mime.php');

$Username = $_GET['Username'];
$Email = $_GET['Email'];
$PasswordResetCode = $_GET['Code'];

//check if code and user is valid
$rs_check = mysql_query("SELECT UserId FROM User WHERE Email = '$Email' AND Username = '$Username' AND PasswordResetCode = '$PasswordResetCode'");
$num = mysql_num_rows($rs_check);

// Match row found with more than 1 results - the user is authenticated
if ( $num <= 0 ) {
	$err[] = 'Error - No such account exists';
}

if($_POST['doUpdate'] == 'Update') {

	if ($_POST['pwd_new1'] != $_POST['pwd_new2']) {
		$err[] = ('Passwords didn\'t match');
	}

	if(empty($err)) {
		
		$newsha1 = PwdHash($data['pwd_new1']);
		mysql_query("UPDATE User SET Password = '$newsha1' WHERE Email = '$Email' AND Username = '$Username' AND PasswordResetCode = '$PasswordResetCode'");
	
		mysql_query("UPDATE User Set PasswordResetCode = '' WHERE Email = '$Email' AND Username = '$Username'");
		
		$mail = new Mail_mime("\n");
		$mail->setHTMLBody("<html><body><p align='center'><img src='img/logotrans.png' alt='Harvesters - The Commnity Food Network'/></p>$Username,<br/><br/>Your password for Harvesters Grocery Store Recovery System has been reset. If you did not make this change, please <a href='www.harvestersgsr.org/contact.php'>contact us</a>.<br/><br/>Thank you,<br/><br/>Administrator<br/>___________________________________________________<br/>THIS IS AN AUTOMATED RESPONSE<br/>DO NOT RESPOND TO THIS EMAIL</body></html>");
		$mail->addHTMLImage('img/logotrans.png', 'image/png');
		$send = Mail::factory('mail');
		$body = $mail->get();
		$hdrs = $mail->headers(array("From" => "Harvesters Grocery Store Recovery System <auto-reply@$host>", 
		'Subject' => 'Login Details'));
		$send->send($Email, $hdrs, $body);	
				 
	$Pass = $data['pwd_new1'];
		
	$result = mysql_query("SELECT `UserId`,`Username`,`Password`,`UserAccess` FROM User WHERE Username = '$Username' AND `banned` = '0'"); 
	$num = mysql_num_rows($result);

	// Match row found with more than 1 results  - the user is authenticated. 
    if ( $num > 0 ) {
	
	list($UserId,$Username,$Password,$UserAccess) = mysql_fetch_row($result);
	 
	//check against salt
	if ($Password === PwdHash($Pass,substr($Password,0,9))) {
	if(empty($err)){

		// this sets session and logs user in  
		session_start();
		session_regenerate_id (true); //prevent against session fixation attacks.

		// this sets variables in the session 
		$_SESSION['UserId'] = $UserId;
		$_SESSION['Username'] = $Username;
		$_SESSION['UserAccess'] = $UserAccess;
		$_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT']);
		
		//update the timestamp and key for cookie
		$stamp = time();
		$ckey = GenKey();
		mysql_query("UPDATE User SET `ctime` = '$stamp', `ckey` = '$ckey' WHERE UserId = '$UserId'") or die(mysql_error());
		
		//set a cookie 
		
	   if(isset($_POST['remember'])){
				  setcookie("UserId", $_SESSION['UserId'], time()+60*60*24*COOKIE_TIME_OUT, "/");
				  setcookie("user_key", sha1($ckey), time()+60*60*24*COOKIE_TIME_OUT, "/");
				  setcookie("Username",$_SESSION['Username'], time()+60*60*24*COOKIE_TIME_OUT, "/");
				   }
		  header("Location: myaccount.php");
		 }
		}
		else {
			$err[] = 'Invalid Password. Please make sure your password is correct and try again.';
		}
	} else {
		$err[] = 'Invalid login. No such user exists';
	  }
	
	
	}
}
?>

<!doctype html>
<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if lt IE 7 ]> <html class="no-js ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]>    <html class="no-js ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]>    <html class="no-js ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
  <meta charset="utf-8">

  <!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame
       Remove this if you use the .htaccess -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

  <title>Password Reset</title>
  <meta name="description" content="">
  <meta name="author" content="">

  <!-- Mobile viewport optimized: j.mp/bplateviewport -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Icons for Browser Address Bar -->
  <link rel="shortcut icon" href="img/favicon.ico" >
  <link rel="icon" type="image/gif" href="img/animated_favicon1.gif" >

  <!-- CSS: implied media="all" -->
  <link rel="stylesheet" href="css/style.css?v=2">

  <!-- Uncomment if you are specifically targeting less enabled mobile browsers
  <link rel="stylesheet" media="handheld" href="css/handheld.css?v=2">  -->

  <!-- All JavaScript at the bottom, except for Modernizr which enables HTML5 elements & feature detects -->
  <script src="js/libs/modernizr-1.7.min.js"></script>
  
  <!-- BEGIN Navigation bar CSS - This is where the magic happens -->
  <link rel="stylesheet" href="css/nav.css">
  <!-- END Navigation bar CSS -->
  
  <!--[if IE 7]>
  <link rel="stylesheet" href="css/ie.css">
  <link rel="stylesheet" href="css/ie7.css">
  <![endif]-->
	
  <noscript>
  <!--[if IE]>
  <link rel="stylesheet" href="css/ie.css">
  <![endif]-->
  </noscript>

</head>

<body>

  <div id="container" class="container">
    <a href="login.php"><img id="harvLogo" src="img/GSRLogo.png" class="noprint" alt="Harvesters Grocery Store Recovery System"/></a>
	<header>
	<!-- BEGIN Dark navigation bar -->
		<nav id="topNav">
			<ul>
			<li class="last"><a></font></a></li>
			</ul>
		</nav>
	<!-- END Dark navigation bar -->
	</header>
    
	<!--Main body content-->
	<div id="main" role="main" class="main">
	<h3 class="titlehdr">Reset Password</h3>

		<p> 
		<?php
		/************** ERROR MESSAGES **************
		This code is to show error messages 
		*********************************************/
		if(!empty($err)) {
			echo "<div class=\"msg\">";
		foreach ($err as $e) {
			echo "* $e <br/>";
	    }
			echo "</div><br/>";	
		}
		
		if(!empty($msg)) {
			echo "<div class=\"msg\">" . $msg[0] . "</div><br/>";
		}
		/******************* END *******************/
		?>
		</p>
		
		<form name="pform" id="pform" method="post" action="">
			
			<div class="labelswidth160">
			<label for="pwd_new1" class="bolded">New Password:</label>
			</div>
			
			<div class="rightcolumn">
			<input name="pwd_new1" type="password" id="pwd_new1" class="required password"><br /><br />
			</div>
			
			<div style="clear: both;"></div>
			
			<div class="labelswidth160">
			<label for="pwd_new2" class="bolded">Retype Password:</label>
			</div>
			
			<div class="rightcolumn">
			<input name="pwd_new2" type="password" id="pwd_new2" class="required password"><br />
			</div>
			
			<div style="clear: both;"></div>
			
			<p align="center"><br />
			<input name="doUpdate" type="submit" id="doUpdate" value="Update" class="awesomeb">
			</p>
		</form>
	  
	</div>
    
	<!--Include the footer-->
	<?php
	include('templates/footer.html');
	?>
	
</div> <!-- end of #container -->

	<!-- JavaScript at the bottom for fast page loading -->
	
	<!-- Grab Google CDN's jQuery, with a protocol relative URL; fall back to local if necessary -->
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.js"></script>
	<script>window.jQuery || document.write("<script src='js/libs/jquery-1.5.1.min.js'>\x3C/script>")</script>
	
	<!-- scripts concatenated and minified via ant build script-->
	<script src="js/plugins.js"></script>
	<script src="js/script.js"></script>
	<!-- end scripts-->
	
	<!--[if lt IE 7 ]>
	<script src="js/libs/dd_belatedpng.js"></script>
	<script>DD_belatedPNG.fix("img, .png_bg"); // Fix any <img> or .png_bg bg-images. Also, please read goo.gl/mZiyb </script>
	<![endif]-->

</body>
</html>
