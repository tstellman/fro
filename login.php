<?php 
include 'dbc.php';

if ($_POST['doLogin']=='Login') {
	
	$Username = $data['Username'];
	$Pass = $data['Password'];
	$AgencyNo = $data['Username'];

	if (strpos($Email,'@') === false) {
		$user_cond = "Username='$Username'";
	} else {
		$user_cond = "Email='$Username'";
	}

	$result = mysql_query("SELECT `UserId`,`Username`,`Password`,`UserAccess` FROM User WHERE $user_cond AND `banned` = '0'") or die (mysql_error());
	$num = mysql_num_rows($result);

	// Match row found with more than 1 results  - the user is authenticated. 
    if ( $num > 0 ) {
	
		list($UserId,$Username,$Password,$UserAccess) = mysql_fetch_row($result);
	 
		//check against salt
		if ($Password === PwdHash($Pass,substr($Password,0,9))) {
			
			if(empty($err)) {
				
				// this sets session and logs user in  
				session_start();
				session_regenerate_id (true); //prevent against session fixation attacks.

				// this sets variables in the session 
				$_SESSION['UserId'] = $UserId;
				$_SESSION['Username'] = $Username;
				$_SESSION['AgencyNo'] = $Username;
				$_SESSION['UserAccess'] = $UserAccess;
				$_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT']);
			
				// update the timestamp and key for cookie
				$stamp = time();
				$ckey = GenKey();
				mysql_query("UPDATE User SET `ctime` = '$stamp', `ckey` = '$ckey' WHERE UserId = '$UserId'") or die(mysql_error());
			
				//set a cookie
				if(isset($_POST['remember'])) {
					setcookie("UserId", $_SESSION['UserId'], time()+60*60*24*COOKIE_TIME_OUT, "/");
					setcookie("user_key", sha1($ckey), time()+60*60*24*COOKIE_TIME_OUT, "/");
					setcookie("Username",$_SESSION['Username'], time()+60*60*24*COOKIE_TIME_OUT, "/");
				}
				
				header("Location: myaccount.php");
			}
		} else {
			$err[] = 'Invalid Password. Make sure your password is correct and try again.';
		}
	} else {
		$err[] = 'Invalid Username. No such user exists';
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
	
	<title>Roadrunner Food Bank's Food Rescue Online</title>
	
	<!-- Mobile viewport optimized: j.mp/bplateviewport -->
	<meta name="viewport" content="width=900" />
	
	<!-- Icons for Browser Address Bar -->
	<link rel="shortcut icon" href="img/favicon.ico" >
	<link rel="icon" type="image/gif" href="img/animated_favicon1.gif" >
	
	<!-- CSS: implied media="all" -->
	<link rel="stylesheet" href="css/style.css?v=2">
	
	<!--[if IE 7]>
	<link rel="stylesheet" href="css/ie.css">
	<link rel="stylesheet" href="css/ie7.css">
	<![endif]-->
	
	<noscript>
	<!--[if IE]>
	<link rel="stylesheet" href="css/ie.css">
	<![endif]-->
	</noscript>
	
	<!-- Uncomment if you are specifically targeting less enabled mobile browsers
	<link rel="stylesheet" media="handheld" href="css/handheld.css?v=2">  -->
	
	<!-- All JavaScript at the bottom, except for Modernizr which enables HTML5 elements & feature detects -->
	<script src="js/libs/modernizr-1.7.min.js"></script>
	
	<!-- BEGIN Navigation bar CSS - This is where the magic happens -->
	<link rel="stylesheet" href="css/nav.css">
	<!-- END Navigation bar CSS -->
</head>

<body>
<div id="container" class="container">
	<img id="harvLogo" src="img/GSRFrontLogo.png" class="noprint" alt="Harvesters Grocery Store Recovery System">
	
	<header>
		<nav id="topNav">
			<ul>
			<li class="last"><a></font></a></li>
			</ul>
		</nav>
	</header>
    
	<!--Main body content-->
	<div id="main" role="main" class="main">
	
		<?php
		//Displays error and confirmation messages
		if(!empty($err)) {
			echo "<div class=\"msg\">";
		foreach ($err as $e) {
			echo "$e <br>";
		}
			echo "</div><br />";	
		}  
		?>
      
		<form action="login.php" method="post" name="logForm" id="logForm" class="loginForm" >
			<label for="Username" class="bolded">Username <sup>*</sup></label><br />
			<input name="Username" type="text" id="Username" class="loginTextbox" placeholder="Username" title="Enter your username" required autofocus><br /><br />
			<label for="Password" class="bolded">Password <sup>*</sup></label><br />
			<input name="Password" type="password" id="Password" class="loginTextbox" placeholder="Password" title="Enter your password" required /><br /><br />
			<input name="doLogin" type="submit" id="doLogin3" value="Login" class="awesome" title="Click here to login" /><br />
			<a href="forgot.php">Forgot Password</a><br /><br />
			<p class="small goldtext">Have an issue? Contact tech support: <a href="contact.php">Contact us!</a></p>
		</form>
		<div style="clear: both;"></div>
		
	</div> <!-- end of #main -->
	
	<!--Include the footer:-->
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
