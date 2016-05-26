<?php
include 'dbc.php';
page_protect();

if (array_key_exists('Submit',$_POST)) {

	$ProgramNumber = $_SESSION['Username'];
	$FullName = $_POST['FullName'];
	$Email = $_POST['Email'];
	$Phone = $_POST['Phone'];
	$Comments = $_POST['Comments'];

    // EDIT THE 2 LINES BELOW AS REQUIRED
    $email_to = "troy@rrfb.org";
    $email_subject = "Food Rescue Online Program Support";

	// Fields that are on form
	$expected = array('FullName','Email','Phone','Comments');
	// Set required fields
	$required = array('FullName','Email','Comments');
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
	$headers = "From: \"Food Rescue Online Contact\" <no-reply@$host>\r\n".
	"Reply-to: \"Food Rescue Online Contact\" <no-reply@$host>\r\n" .
	'X-Mailer: PHP/' . phpversion();
	@mail($email_to, $email_subject, $email_message, $headers);
	
	$msg[] = 'Thanks, your message has been submitted';
	unset($errors);
	}
}

//Include the header:
include('templates/header.php');
?>
    
	<!--Main body content-->
	<div id="main" role="main" class="main">
    <h3 class="titlehdr">Agency Support Contact Page</h3>
		
		<?php	   
		if(!empty($msg)) {
			echo "<div class=\"msg\">" . $msg[0] . "</div><br />";
		}
		?>
		
		<form name="contactform" method="post" action="contactprogsupport.php">
			<div class="cf">
			<div class="contactlabels"><label for="FullName" class="bolded">Full Name <sup>*</sup></label></div>
			<div class="rightcolumn"><input type="text" name="FullName" maxlength="50" size="25" required  <?php if (isset($errors)) { echo 'value="'.htmlentities($_POST['FullName']).'"'; } ?>></div><?php if (isset($errors) && in_array('FullName', $errors)){?><div class="red">Required</div><?php } ?>
			</div>
			
			<div class="cf">
			<div class="contactlabels"><label for="Email" class="bolded">Email Address <sup>*</sup></label></div>
			<div class="rightcolumn"><input type="email" name="Email" maxlength="80" size="25" required  <?php if (isset($errors)) { echo 'value="'.htmlentities($_POST['Email']).'"'; } ?>></div><?php if (isset($errors) && in_array('Email', $errors)){?><div class="red">Required</div><?php } ?>
			</div>
			
			<div class="cf">
			<div class="contactlabels"><label for="Phone" class="bolded">Phone Number</label></div>
			<div class="rightcolumn"><input type="tel" name="Phone" maxlength="20" size="25"></div>
			</div>
			
			<div class="cf">
			<div class="contactlabels"><label for="Comments" class="bolded">Comments <sup>*</sup></label></div>
			<div class="rightcolumn"><textarea name="Comments" class="contact" required><?php if (isset($errors)) { echo ''.htmlentities($_POST['Comments']).''; } ?></textarea></div><?php if (isset($errors) && in_array('Comments', $errors)){?><div class="red">Required</div><?php } ?>
			</div>
					
			<p align="center"><input type="submit" name="Submit" id="Submit" value="Submit" class="awesomed"></p>
		</form>
	
	</div> <!--end of #main-->
    
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
