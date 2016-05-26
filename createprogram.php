<?php
include 'dbc.php';
include('Mail.php');
include('Mail/mime.php');
page_protect();

//User must have Admin(5) user access to view this page
if(!checkAdmin()) {
	header("Location: login.php");
	exit();
}

if (array_key_exists('Create',$_POST)) {
	
	// Fields that are on form
	$expected = array('ProgramNumber','ProgramName','FirstName','LastName','Email');
	// Set required fields
	$required = array('ProgramNumber','ProgramName','Email');
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
		
		$ProgramNumber = $data['ProgramNumber'];
		$FirstName = $data['FirstName'];
		$LastName = $data['LastName'];
		$Email = $data['Email'];
		$ProgramName = $data['ProgramName'];
		$ProgramCounty = $data['ProgramCounty'];
		$pwd = 'grocery';
		$hash = PwdHash($pwd);
	
		//Creates the User
		mysql_query("INSERT INTO User (`Username`,`Password`,`FirstName`,`LastName`,`Email`,`UserAccess`) VALUES ('$ProgramNumber','$hash','$FirstName','$LastName','$Email','1')");

		//Creates the Program
		mysql_query("INSERT INTO Program (`ProgramNumber`,`ProgramName`,`Username`, `County`) VALUES ('$ProgramNumber','$ProgramName','$ProgramNumber','$ProgramCounty')");

		$mail = new Mail_mime("\n");
		$mail->setHTMLBody("<html>
							<body>
							<p align='center'>
								<img src='img/logotrans.png' alt='Roadrunner Food Bank'/></p>
								You are now a registered user on Food Rescue Online. As a reminder, you may reset your password from the Account Details page once logged in.
								<br/><br/>
								Username: $ProgramNumber<br/>
								Password: $pwd<br/><br/><a href='http://$host$path/login.php'>Click Here to Login</a><br/><br/>Have any questions? <a href='http://$host$path/contact.php'>Contact us</a><br/><br/>Thank You,<br/><br/>Administrator<br/>____________________________________<br/>THIS IS AN AUTOMATED RESPONSE<br/>DO NOT RESPOND TO THIS EMAIL</body></html>");
		$mail->addHTMLImage('img/logotrans.png', 'image/png');
		$send = Mail::factory('mail');
		$body = $mail->get();
		$hdrs = $mail->headers(array("From" => "Roadrunner Food Bank <info@$host>", 
		'Subject' => 'User Registration'));
		$send->send($Email, $hdrs, $body);
	
	// Confirmation message if program is created successfully
	$msg[] = 'Program successfully created'; 
	unset($errors);
	}
}

//Include the header:
include('templates/header.php');	
?>

<body><!--Main body content-->

	<div id="main" role="main" class="main">
		<h3 class="titlehdr">Add a New Agency</h3>
		
		<?php	  
		if(!empty($msg)) {
			echo "<div class=\"msg\">" . $msg[0] . "</div><br />";
		}
		?>
		
	<p><u>Reminder</u>: Agencies are created with default password "change12"</p><br />
	<form method="post" action="createprogram.php" id="centerform">
		<div class="cf">
		<div class="itemlabels"><label for="ProgramName" class="bolded">Agency Name<sup> *</sup></label></div>
		<div class="rightcolumn"><input name="ProgramName" type="text" id="ProgramName" size="25" required <?php if (isset($errors)) { echo 'value="'.htmlentities($_POST['ProgramName']).'"'; } ?>></div><?php if (isset($errors) && in_array('ProgramName', $errors)){?><div class="red">Required</div><?php } ?>
		</div>
		
		<div class="cf">
		<div class="itemlabels"><label for="ProgramNumber" class="bolded">Agency Number/Username<sup> *</sup></label></div>
		<div class="rightcolumn">
		<input name="ProgramNumber" type="text" id="ProgramNumber" size="25" maxlength="20" required <?php if (isset($errors)) { echo 'value="'.htmlentities($_POST['ProgramNumber']).'"'; } ?>></div><?php if (isset($errors) && in_array('ProgramNumber', $errors)){?><div class="red">Required</div><?php } ?>
		</div>
		
		<div class="cf">
		<div class="itemlabels"><label for="ProgramCounty" class="bolded">County<sup> *</sup></label></div>
		<div class="rightcolumn">
		<select name="ProgramCounty" id="ProgramCounty" required <?php if (isset($errors)) { echo 'value="'.htmlentities($_POST['ProgramCounty']).'"'; } ?>>
			<option value="">-- Select --</option>
			<option value="COMBER">Bernalillo</option>
			<option value="COMCHA">Chavez</option>
			<option value="COMCUR">Curry</option>
			<option value="COMDON">Dona Ana</option>
			<option value="COMEDD">Eddy</option>
			<option value="COMGRANT">Grant</option>
			<option value="COMHIDA">Hidalgo</option>
			<option value="COMLEA">Lea</option>
			<option value="COMLIN">Lincoln</option>
			<option value="COMLUN">Luna</option>
			<option value="COMMCK">McKinley</option>
			<option value="COMOTE">Otero</option>
			<option value="COMSANJ">San Juan</option>
			<option value="COMSAN">Sandoval</option>
			<option value="COMSF">Santa Fe</option>
			<option value="COMSIE">Sierra</option>
			<option value="COMSOC">Socorro</option>
			<option value="COMTORR">Torrance</option>
			<option value="COMVAL">Valencia</option>
		</select>
		</div><?php if (isset($errors) && in_array('ProgramCounty', $errors)){?><div class="red">Required</div><?php } ?>
		</div>
		
		<div class="cf">
		<div class="itemlabels"><label for="FirstName" class="bolded">First Name<sup> *</sup></label></div>
		<div class="rightcolumn"><input name="FirstName" type="text" id="FirstName" size="25" required></div>
		</div>
		
		<div class="cf">
		<div class="itemlabels"><label for="LastName" class="bolded">Last Name<sup> *</sup></label></div>
		<div class="rightcolumn"><input name="LastName" type="text" id="LastName" size="25" required></div>
		</div>
		
		<div class="cf">
		<div class="itemlabels"><label for="Email" class="bolded">Email<sup> *</sup></label></div>
		<div class="rightcolumn"><input name="Email" type="email" id="Email" size="25" required <?php if (isset($errors)) { echo 'value="'.htmlentities($_POST['Email']).'"'; } ?>></div><?php if (isset($errors) && in_array('Email', $errors)){?><div class="red">Required</div><?php } ?>
		</div>
		
		<div id="createbutton"><input name="Create" type="submit" id="Create" value="Create" class="awesomeb"></div>
	</form>
	
	</div><!--end of #main-->
    
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
