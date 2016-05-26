<?php 
include 'dbc.php';
include('Mail.php');
include('Mail/mime.php');
page_protect();

$user_settings = mysql_query("SELECT * FROM User WHERE UserId = '$_SESSION[UserId]'");
$row_settings = mysql_fetch_array($user_settings);
$email = $row_settings['Email'];
$firstname = $row_settings['FirstName'];
$lastname = $row_settings['LastName'];
$Username = $_SESSION['Username'];

//Reset Password
if($_POST['doUpdate'] == 'Update') {

	$rs_pwd = mysql_query("SELECT Password FROM User WHERE UserId = '$_SESSION[UserId]'");
	list($old) = mysql_fetch_row($rs_pwd);
	$old_salt = substr($old,0,9);

	if ($_POST['pwd_new1'] != $_POST['pwd_new2']) {
		$err[] = ('Passwords didn\'t match. Try again.');
	} else if ($old === PwdHash($data['pwd_old'],$old_salt)) {
		$newsha1 = PwdHash($data['pwd_new1']);
		mysql_query("UPDATE User SET Password = '$newsha1' WHERE UserId = '$_SESSION[UserId]'");
		$msg[] = 'Your password has been updated.';
	
	$mail = new Mail_mime("\n");
	$mail->setHTMLBody("<html><body><p align='center'><img src='img/logotrans.png' alt='Harvesters - The Commnity Food Network'/></p>$Username,<br/><br/>Your password for Harvesters Grocery Store Recovery System has been reset. If you did not make this change, please <a href='www.harvestersgsr.org/contact.php'>contact us</a>.<br/><br/>Thank you,<br/><br/>Administrator<br/>___________________________________________________<br/>THIS IS AN AUTOMATED RESPONSE<br/>DO NOT RESPOND TO THIS EMAIL</body></html>");
	$mail->addHTMLImage('img/logotrans.png', 'image/png');
	$send = Mail::factory('mail');
	$body = $mail->get();
	$hdrs = $mail->headers(array("From" => "Harvesters Grocery Store Recovery System <auto-reply@$host>", 
	'Subject' => 'Login Details'));
	$send->send($email, $hdrs, $body);
	
		} else {
			$err[] = 'Your old password is invalid. Try again with the correct password.';
		}
}

//Include the header:
include('templates/header.php');

?>
	<!--Main body content-->
	<div id="main" role="main" class="main">  	
	<h3 class="titlehdr">My Account Details</h3>
		
		<?php	
		if(!empty($err)) {
			echo "<div class=\"msg\">";
		foreach ($err as $e) {
			echo "$e <br>";
		}
			echo "</div><br />";	
		}
	   
		if(!empty($msg)) {
			echo "<div class=\"msg\">" . $msg[0] . "</div><br />";
		}
		?>
		
		<label for="FirstName LastName" class="bolded">Name:</label>
		<?php echo $firstname; ?>&nbsp;<?php echo $lastname; ?>
		<br/><br/>
		
		<label for="Email" class="bolded">Email:</label>
		<?php echo $email; ?>
		<br/><br/>
		
		<h3 class="titlehdr">Reset Password</h3>
		<form name="pform" id="pform" method="post" action="">
			<div class="cf">
			<div class="passwordlabels"><label for="pwd_old" class="bolded">Old Password:</label></div>
			<div class="rightcolumn"><input name="pwd_old" type="password" class="required password"  id="pwd_old"></div>
			</div>
			
			<div class="cf">
			<div class="passwordlabels"><label for="pwd_new1" class="bolded">New Password:</label></div>
			<div class="rightcolumn"><input name="pwd_new1" type="password" id="pwd_new1" class="required password"></div>
			</div>
			
			<div class="cf">
			<div class="passwordlabels"><label for="pwd_new2" class="bolded">Retype Password:</label></div>
			<div class="rightcolumn"><input name="pwd_new2" type="password" id="pwd_new2" class="required password"></div>
			</div>
			
			<p align="center"><input name="doUpdate" type="submit" id="doUpdate" value="Update" class="awesomed"></p>
		</form>
    </div> <!--end of #main-->
    
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

</body>
</html>
