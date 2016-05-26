<?php
include 'dbc.php';
page_protect();

//User must have Admin(5) user access to view this page
if(!checkAdmin()) {
	header("Location: login.php");
	exit();
}

if (array_key_exists('Create',$_POST)) {
	
	// Fields that are on form
	$expected = array('DonorNumber','DonorName','Address1','Address2','City','State','ZipCode','FBCProductSource','ProgramNumber');
	// Set required fields
	$required = array('DonorNumber','DonorName','Address1','City','State','ZipCode','FBCProductSource','ProgramNumber');
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
		
		$DonorNumber = $data['DonorNumber'];
		$DonorName = $data['DonorName'];
		$Address1 = $data['Address1'];
		$Address2 = $data['Address2'];
		$City = $data['City'];
		$State = $data['State'];
		$ZipCode = $data['ZipCode'];
		$GroceryStore = $data['GroceryStore'];
		$FoodRescue = $data['FoodRescue'];
		$FoodDrive = $data['FoodDrive'];
		//$FBCProductSource = $data['FBCProductSource'];
		$FBCProductSource = "N/A";
		$ProgramNumber = $data['ProgramNumber'];
		
		mysql_query("INSERT INTO Donor (`DonorNumber`,`DonorName`,`Address1`,`Address2`,
										`City`,`State`,`ZipCode`, `GroceryStore`,
										`FoodRescue`,`FoodDrive`,`FBCProductCategory`,`FBCProductSource`,
										`FBCReason`,`ProgramNumber`) 
								VALUES ('$DonorNumber','$DonorName','$Address1','$Address2',
										'$City','$State','$ZipCode','$GroceryStore',
										'$FoodRescue','$FoodDrive','DONATED','$FBCProductSource',
										'RETGROC','$ProgramNumber')") or die(mysql_error());
		$msg[] = 'A new donor has been created';
		unset($errors);
	}
}

//Include the header:
include('templates/header.php');	
?>

	<div id="main" role="main" class="main">
		<h3 class="titlehdr">Add a New Donor</h3>
		
		<?php
		if(!empty($msg)) {
			echo "<div class=\"msg\">" . $msg[0] . "</div><br />";
		}
		?>

	<form method="post" action="createdonor.php" id="centerformb">
		<div class="cf">
		<div class="itemlabels"><label for="DonorName" class="bolded">Donor Name<sup> *</sup></label></div>
		<div class="rightcolumn"><input name="DonorName" type="text" id="DonorName" required <?php if (isset($errors)) { echo 'value="'.htmlentities($_POST['DonorName']).'"'; } ?>></div><?php if (isset($errors) && in_array('DonorName', $errors)){?><div class="red">Required</div><?php } ?>
		</div>
		
		<div class="cf">
		<div class="itemlabels"><label for="DonorNumber" class="bolded">Donor Number<sup> *</sup></label></div>
		<div class="rightcolumn"><input name="DonorNumber" type="text" id="DonorNumber" maxlength="20" required <?php if (isset($errors)) { echo 'value="'.htmlentities($_POST['DonorNumber']).'"'; } ?>></div><?php if (isset($errors) && in_array('DonorNumber', $errors)){?><div class="red">Required</div><?php } ?>
		</div>
		
		<div class="cf">
		<div class="itemlabels"><label for="GroceryStore" class="bolded">Grocery Store<sup> *</sup></label></div>
		<div class="rightcolumn">
		<select name="GroceryStore" id="GroceryStore" required <?php if (isset($errors)) { echo 'value="'.htmlentities($_POST['GroceryStore']).'"'; } ?>>
			<option value="">-- Select --</option>
			<option value="Yes">Yes</option>
			<option value="No">No</option>
		</select>
		</div><?php if (isset($errors) && in_array('GroceryStore', $errors)){?><div class="red">Required</div><?php } ?>
		</div>
		
		<div class="cf">
		<div class="itemlabels"><label for="FoodRescue" class="bolded">Food Rescue<sup> *</sup></label></div>
		<div class="rightcolumn">
		<select name="FoodRescue" id="FoodRescue" required <?php if (isset($errors)) { echo 'value="'.htmlentities($_POST['FoodRescue']).'"'; } ?>>
			<option value="">-- Select --</option>
			<option value="Yes">Yes</option>
			<option value="No">No</option>
		</select>
		</div><?php if (isset($errors) && in_array('FoodRescue', $errors)){?><div class="red">Required</div><?php } ?>
		</div>
		
		<div class="cf">
		<div class="itemlabels"><label for="FoodDrive" class="bolded">Food Drive<sup> *</sup></label></div>
		<div class="rightcolumn">
		<select name="FoodDrive" id="FoodDrive" required <?php if (isset($errors)) { echo 'value="'.htmlentities($_POST['FoodDrive']).'"'; } ?>>
			<option value="">-- Select --</option>
			<option value="Yes">Yes</option>
			<option value="No">No</option>
		</select>
		</div><?php if (isset($errors) && in_array('FoodDrive', $errors)){?><div class="red">Required</div><?php } ?>
		</div>
		
		<div class="cf">
		<div class="itemlabels"><label for="ProgramNumber" class="bolded">Select an Agency<sup> *</sup></label></div>
		<div class="rightcolumn">
		<?php
		$result = mysql_query("SELECT ProgramNumber, CONCAT(ProgramNumber,' - ',ProgramName) AS Program FROM Program ORDER BY ProgramNumber ASC")
		or die(mysql_error());			
		
		echo '<select name="ProgramNumber" required><option value="">-- Select an Agency --</option>';
		while($row = mysql_fetch_array($result))
		{
		echo '<option value="'.$row['ProgramNumber'].'">'. stripslashes($row['Program']). '</option>';
		}
		echo '</select><br /><br />';
		?>
		</div><?php if (isset($errors) && in_array('ProgramNumber', $errors)){?><div class="red">Required</div><?php } ?>
		</div>

		<div class="cf">
		<div class="itemlabels"><label for="Address1" class="bolded">Address<sup> *</sup></label></div>
		<div class="rightcolumn"><input name="Address1" type="text" id="Address1" size="25" required <?php if (isset($errors)) { echo 'value="'.htmlentities($_POST['Address1']).'"'; } ?> /><br /><input name="Address2" type="text" id="Address2" size="25" <?php if (isset($errors)) { echo 'value="'.htmlentities($_POST['Address2']).'"'; } ?> /></div><?php if (isset($errors) && in_array('Address1', $errors)){?><div class="red">Required</div><?php } ?>
		</div>
		
		<div class="cf">
		<div class="itemlabels"><label for="City" class="bolded">City<sup> *</sup></label></div>
		<div class="rightcolumn"><input name="City" type="text" id="City" size="12" required <?php if (isset($errors)) { echo 'value="'.htmlentities($_POST['City']).'"'; } ?> /></div><?php if (isset($errors) && in_array('City', $errors)){?><div class="red">Required</div><?php } ?>
		</div>
		
		<div class="cf">
		<div class="itemlabels"><label for="State" class="bolded">State<sup> *</sup></label></div>
		<div class="rightcolumn">
		<select name="State" id="State" required <?php if (isset($errors)) { echo 'value="'.htmlentities($_POST['State']).'"'; } ?>>
			<option value="NM">NM</option>
		</select>
		</div><?php if (isset($errors) && in_array('State', $errors)){?><div class="red">Required</div><?php } ?>
		</div>

		<div class="cf">
		<div class="itemlabels"><label for="MailingZipCode" class="bolded">ZIP<sup> *</sup></label></div>
		<div class="rightcolumn"><input name="ZipCode" type="text" id="ZipCode" size="3" maxlength="5" required <?php if (isset($errors)) { echo 'value="'.htmlentities($_POST['ZipCode']).'"'; } ?> /></div><?php if (isset($errors) && in_array('ZipCode', $errors)){?><div class="red">Required</div><?php } ?>
		</div>
		
		<div id="createbutton"><input name="Create" type="submit" id="Create" value="Create" class="awesomeb"></div>
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
