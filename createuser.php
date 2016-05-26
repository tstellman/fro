<?php 
include 'dbc.php';
page_protect();

//User must have Admin(5) user access to view this page
if(!checkAdmin()) {
	header("Location: login.php");
	exit();
}

if (array_key_exists('doSubmit', $_POST)) {

	// Fields that are on form
	$expected = array('Username', 'FirstName', 'LastName', 'Email', 'UserAccess');
	// Set required fields
	$required = array('Username', 'Email', 'UserAccess');
	// Initialize array for errors
	$errors = array();

	foreach ($_POST as $field => $value) {
		// Assign to $temp and trim spaces if not array 
		$temp = is_array($value) ? $value : trim($value);
		// If field is empty and required, tag onto $errors array 
		if (empty($temp) && in_array($field, $required)) {
			array_push($errors, $field);
		}
	}
	
	//If good to go
	if (empty($errors)) {
		
		$Username = $data['Username'];
		$FirstName = $data['FirstName'];
		$LastName = $data['LastName'];
		$Email = $data['Email'];

		if(!empty($_POST['Password'])) {
			$pwd = $data['Password'];	
			$hash = PwdHash($data['Password']);
		}
		else {
			$pwd = 'change12';
			$hash = PwdHash($pwd);
		}
	 
		mysql_query("INSERT INTO User (`Username`,`Password`,`FirstName`,`LastName`,`email`,`UserAccess`) VALUES ('$Username','$hash','$FirstName','$LastName','$Email','5')");
			
$message = 
"You are now registered to use Roadrunner's Grocery Store Recovery System. You may reset your password by navigating to the Account Details page once logged in.\n
Username: $Username
Password: $pwd

Login Link: gsr.rrfb.org


Questions? Contact us: gsr.rrfb.org/contact.php

Thank You

Administrator
$host_upper
______________________________________________________
THIS IS AN AUTOMATED RESPONSE. 
***DO NOT RESPOND TO THIS EMAIL***
";

			mail($Email, "Login Details", $message,
			"From: \"Member Registration\" <auto-reply@$host>\r\n" .
			 "X-Mailer: PHP/" . phpversion()); 

		$msg[] = 'A new administrative user has been created'; 
		unset($errors);
	}
}

//Include the header
include('templates/header.php');
?>

	<body><!--Main body content-->
	<div id="main" role="main" class="main">
		<h3 class="titlehdr">Add a New Administrator</h3>
		<?php
		if(!empty($msg)) {
			echo "<div class=\"msg\">" . $msg[0] . "</div><br/>";
		}
		?>

		<form method="post" action="createuser.php" id="centerform">
			<div class="cf">
			<div class="itemlabels"><label for="Username" class="bolded">Username<sup> *</sup></label></div>
			<div class="rightcolumn"><input name="Username" type="text" id="Username" size="25" required <?php if (isset($errors)) { echo 'value="'.htmlentities($_POST['Username']).'"'; } ?> /></div><?php if (isset($errors) && in_array('Username', $errors)){?><div class="red">Required</div><?php } ?>
			</div>
			
			<div class="cf">
			<div class="itemlabels"><label for="FirstName" class="bolded">First Name</label></div>
			<div class="rightcolumn"><input name="FirstName" type="text" id="FirstName" size="25"/></div>
			</div>
			
			<div class="cf">
			<div class="itemlabels"><label for="LastName" class="bolded">Last Name</label></div>
			<div class="rightcolumn"><input name="LastName" type="text" id="LastName" size="25"/></div>
			</div>
			
			<div class="cf">
			<div class="itemlabels"><label for="Email" class="bolded">Email<sup> *</sup></label></div>
			<div class="rightcolumn"><input name="Email" type="email" id="Email" required size="25" <?php if (isset($errors)) { echo 'value="'.htmlentities($_POST['Email']).'"'; } ?> /></div><?php if (isset($errors) && in_array('Email', $errors)){?><div class="red">Required</div><?php } ?>
			</div>
			
			<div id="createbutton"><input name="doSubmit" type="submit" id="doSubmit" value="Create" class="awesomeb"></div>
		</form>
	</div><!--end of main-->
    
	<!--Include the footer-->
	<?php
	include('templates/footer.html');
	?>
	
</div> <!--end of #container-->

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
