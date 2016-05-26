<?php 
include 'dbc.php';
include('Mail.php');
include('Mail/mime.php');

if ($_POST['doReset']=='Reset') {

	if(!isEmail($data['Email'])) {
		$err[] = 'Not a valid email address'; 
	}

	$Email = $data['Email'];
	$Username = $data['Username'];

	//check if user is valid
	$rs_check = mysql_query("SELECT UserId FROM User WHERE Email = '$Email' AND Username = '$Username'") or die (mysql_error());
	$num = mysql_num_rows($rs_check);

	//Match row found with more than 1 results - the user is authenticated
	if ( $num <= 0 ) {
		$err[] = 'No such account exists';
	}

	if(empty($err)) {
		//Generate random string for password reset code
		$SelectPwCodeQuery = mysql_query("SELECT substring(MD5(RAND()), -8) AS PasswordResetCode") or die(mysql_error());
		$SelectPwCode = mysql_fetch_array($SelectPwCodeQuery);
		$PasswordResetCode = $SelectPwCode['PasswordResetCode'];

		//Update pw reset code
		$setpwcode = mysql_query("UPDATE User SET PasswordResetCode = '$PasswordResetCode' WHERE Username = '$Username' AND Email = '$Email'") or die (mysql_error());

		$mail = new Mail_mime("\n");
		$mail->setHTMLBody("<html><body><p align='center'><img src='img/logotrans.png' alt='Harvesters - The Commnity Food Network'/></p>It was requested that your Harvesters Grocery Store Recovery System password be reset. To confirm, click the link below and you will be forwarded to a page to reset your password.<br/><br/><a href='www.harvestersgsr.org/pwreset.php?Code=$PasswordResetCode&Username=$Username&Email=$Email'>Reset Password</a><br/><br/>Thank you,<br/><br/>Administrator<br/>___________________________________________________<br/>THIS IS AN AUTOMATED RESPONSE<br/>DO NOT RESPOND TO THIS EMAIL</body></html>");
		$mail->addHTMLImage('img/logotrans.png', 'image/png');
		$send = Mail::factory('mail');
		$body = $mail->get();
		$hdrs = $mail->headers(array("From" => "Harvesters Grocery Store Recovery System <auto-reply@$host>", 
		'Subject' => 'Reset Password'));
		$send->send($Email, $hdrs, $body);						 
						 
		$msg[] = 'Check your email to confirm and complete your request';
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
	
	<title>Forgot Password</title>
	<meta name="description" content="">
	<meta name="author" content="">
	
	<!-- Mobile viewport optimized: j.mp/bplateviewport -->
	<meta name="viewport" content="width=900" />
	
	<!-- Icons for Browser Address Bar -->
	<link rel="shortcut icon" href="img/favicon.ico" >
	<link rel="icon" type="image/gif" href="img/animated_favicon1.gif">
	
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
		<nav id="topNav">
			<ul>
			<li class="last"><a></font></a></li>
			</ul>
		</nav>
	</header>
    
	<!--Main body content-->
	<div id="main" role="main" class="main">
	<h3 class="titlehdr">Forgot Password</h3>

		<?php
		//Displays error and confirmation messages
		if(!empty($err)) {
			echo "<div class=\"msg\">";
		foreach ($err as $e) {
			echo "* $e <br>";
		}
			echo "</div>";	
		}
	  
		if(!empty($msg)) {
			echo "<div class=\"msg\">" . $msg[0] . "</div><br/>";
		}
		?>
      
		<p>To reset your password, enter the username and email address associated with your account.</p><br/>
	 
		<form action="forgot.php" method="post" name="PasswordReset" id="PasswordReset">
			<div class="cf">
			<div class="labelswidth100"><label for="Username" class="bolded">Username:</label></div>
			<div class="rightcolumn"><input name="Username" type="text" id="Username" maxlength="15" size="10"></div>
			</div>
		
			<div class="cf">
			<div class="labelswidth100"><label for="Email" class="bolded">Email:</label></div>
			<div class="rightcolumn"><input name="Email" type="email" id="Email" size="25"></div>
			</div>
        
			<p align="center"><input name="doReset" type="submit" id="doLogin3" value="Reset" class="awesomed"></p>
		</form>
	  
	</div> <!--end #main-->
    
	<!--Include the footer-->
	<?php
	include('templates/footer.html');
	?>
	
</div> <!--end #container-->

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
