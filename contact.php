<?php
$msg = array();

$host = $_SERVER['HTTP_HOST'];
$host_upper = strtoupper($host);
$login_path = @ereg_replace('admin','',dirname($_SERVER['PHP_SELF']));
$path = rtrim($login_path, '/\\');

if (array_key_exists('Submit',$_POST)) {

	$ProgramNumber = $_POST['ProgramNumber'];
	$FullName = $_POST['FullName'];
	$Email = $_POST['Email'];
	$Phone = $_POST['Phone'];
	$Comments = $_POST['Comments'];

    // Edit the two lines below as required
    $email_to = "techsupport@harvestersgsr.org";
    $email_subject = "Tech Issues with Harvesters Grocery Store Recovery System";

	// Fields that are on form
	$expected = array('ProgramNumber','FullName','Email','Phone','Comments');
	// Set required fields
	$required = array('ProgramNumber','FullName','Email','Comments');
	// Initialize array for errors
	$errors = array();
	
	foreach ($_POST as $field => $value){
		// Assign to $temp and trim spaces if not array 
		$temp = is_array($value) ? $value : trim($value);
		// If field is empty and required, tag onto $errors array 
		if (empty($temp) && in_array($field, $required)) {
			array_push($errors, $field); 
		}
	}
	  
	//If good to go
	if (empty($errors)){
	
		$email_message = "Form details below.\n\n";
		 
		function clean_string($string) {
		  $bad = array("content-type","bcc:","to:","cc:","href");
		  return str_replace($bad,"",$string);
		}
		 
		$email_message .= "Program Number: ".clean_string($ProgramNumber)."\n";
		$email_message .= "Full Name: ".clean_string($FullName)."\n";
		$email_message .= "Email: ".clean_string($Email)."\n";
		$email_message .= "Telephone: ".clean_string($Phone)."\n";
		$email_message .= "Comments: ".clean_string($Comments)."\n";
		 
	// create email headers
	$headers = "From: \"Grocery Store Recovery Contact\" <no-reply@$host>\r\n".
	"Reply-to: \"Grocery Store Recovery Contact\" <no-reply@$host>\r\n" .
	'X-Mailer: PHP/' . phpversion();
	@mail($email_to, $email_subject, $email_message, $headers);
	
	$msg[] = 'Your message has been submitted';
			unset($errors);
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
	
	<title>Contact Us</title>
	<meta name="description" content="">
	<meta name="author" content="">
	
	<!-- Mobile viewport optimized: j.mp/bplateviewport -->
	<meta name="viewport" content="width=900" />
	
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
		<nav id="topNav">
			<ul>
			<li class="last"><a></font></a></li>
			</ul>
		</nav>
    </header>
    
	<!--Main body content-->
	<div id="main" role="main" class="main">
    <h3 class="titlehdr">Contact Us</h3>
    
		<?php
		if(!empty($msg)) {
			echo "<div class=\"msg\">" . $msg[0] . "</div><br />";
		}
		?>
		
		<form name="contactform" method="post" action="contact.php">
			<div class="cf">
			<div class="contactlabels"><label for="ProgramNumber" class="bolded">Username <sup>*</sup></label></div>
			<div class="rightcolumn"><input type="text" name="ProgramNumber" id="ProgramNumber" maxlength="5" size="3" required  <?php if (isset($errors)) { echo 'value="'.htmlentities($_POST['ProgramNumber']).'"'; } ?>></div><?php if (isset($errors) && in_array('ProgramNumber', $errors)){?><div class="red">Required</div><?php } ?>
			</div>
			
			<div class="cf">
			<div class="contactlabels"><label for="FullName" class="bolded">Full Name <sup>*</sup></label></div>
			<div class="rightcolumn"><input type="text" name="FullName" id="FullName" maxlength="50" size="25" required  <?php if (isset($errors)) { echo 'value="'.htmlentities($_POST['FullName']).'"'; } ?>></div><?php if (isset($errors) && in_array('FullName', $errors)){?><div class="red">Required</div><?php } ?>
			</div>
			
			<div class="cf">
			<div class="contactlabels"><label for="Email" class="bolded">Email Address <sup>*</sup></label></div>
			<div class="rightcolumn"><input type="email" name="Email" id="Email" maxlength="80" size="25" required  <?php if (isset($errors)) { echo 'value="'.htmlentities($_POST['Email']).'"'; } ?>></div><?php if (isset($errors) && in_array('Email', $errors)){?><div class="red">Required</div><?php } ?>
			</div>
			
			<div class="cf">
			<div class="contactlabels"><label for="Phone" class="bolded">Phone Number</label></div>
			<div class="rightcolumn"><input type="tel" name="Phone" id="Phone" maxlength="20" size="25"></div>
			</div>
			
			<div class="cf">
			<div class="contactlabels"><label for="Comments" class="bolded">Comments <sup>*</sup></label></div>
			<div class="rightcolumn"><textarea name="Comments" id="Comments" class="contact" required><?php if (isset($errors)) { echo ''.htmlentities($_POST['Comments']).''; } ?></textarea></div><?php if (isset($errors) && in_array('Comments', $errors)){?><div class="red">Required</div><?php } ?>
			</div>
				
			<p align="center"><input type="submit" name="Submit" id="Submit" value="Submit" class="awesomed"></p>
		</form>
    </div>
    
	<!--Include the footer:-->
	<?php
	include('templates/footer.html');
	?>
  
</div> <!--! end of #container -->

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
