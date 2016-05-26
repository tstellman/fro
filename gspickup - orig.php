<?php 
include 'dbc.php';
page_protect();

// User must have Admin(5) user access to view this page
/*if(!checkAdmin()) {
	header("Location: login.php");
	exit();
}*/

// If button is clicked
if (array_key_exists('Submit',$_POST)) {
	
	// Fields that are on form
	$expected = array('PickupDate', 'ProgramNumber', 'DonorNumber', 'BreadQty', 'AssortedRefrigeratedProductQty', 'ProduceQty', 'AssortedMixedDryQty', 'AssortedFrozenQty');
	// Set required fields
	$required = array('PickupDate', 'ProgramNumber', 'DonorNumber');
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
		$PickupDate = $data['PickupDate'];
		$BreadQty = $data['BreadQty'];
		$AssortedRefrigeratedProductQty = $data['AssortedRefrigeratedProductQty'];
		$ProduceQty = $data['ProduceQty'];
		$AssortedMixedDryQty = $data['AssortedMixedDryQty'];
		$AssortedFrozenQty = $data['AssortedFrozenQty'];
		if (isset($_SESSION['UserId'])) {
			if (checkAdmin()) {
				$ProgramNumber = $data['ProgramNumber'];
			}elseif (checkProgram()) {
				$ProgramNumber = $_SESSION['Username'];
			}
		}
		$TotalAmount = $BreadQty + $AssortedRefrigeratedProductQty + $ProduceQty + $AssortedMixedDryQty + $AssortedFrozenQty;
		/*$TotalAmount = $data['TotalQty'];*/
		$PickupEntryTime = date("Y-m-d H:i:s",time());
		
		//Block Program from submitting a pickup of 0 Qty
		if($BreadQty == 0 && $AssortedRefrigeratedProductQty == 0 && $ProduceQty == 0  && $AssortedMixedDryQty == 0 && $AssortedFrozenQty == 0) {
			die("You cannot submit a pickup with 0 Quantity. Please go <a href='fdpickup.php'>back</a> and try again.");
		}

		$block_order = mysql_query("SELECT oh.PickupId, oh.ProgramNumber, oh.DonorNumber, PickupDate,
										GROUP_CONCAT(if(LineNumber = 1, Quantity = $BreadQty, NULL)) AS 'Bread',
										GROUP_CONCAT(if(LineNumber = 2, Quantity = $AssortedRefrigeratedProductQty, NULL)) AS 'Assorted Refrigerated Product',
										GROUP_CONCAT(if(LineNumber = 3, Quantity = $ProduceQty, NULL)) AS 'Produce',
										GROUP_CONCAT(if(LineNumber = 4, Quantity = $AssortedMixedDryQty, NULL)) AS 'Assorted Mixed Dry',
										GROUP_CONCAT(if(LineNumber = 5, Quantity = $AssortedFrozenQty, NULL)) AS 'Assorted Frozen'
									FROM PickupDetail od, PickupHeader oh
									WHERE od.PickupId = oh.PickupId AND ProgramNumber = '$ProgramNumber' AND DonorNumber = '$DonorNumber' AND PickupDate = '$PickupDate'
									GROUP BY oh.PickupId
									ORDER BY PickupDate DESC");
									
		$block_variables = mysql_fetch_array($block_order);
		$Bread2 = $block_variables['Bread'];
		$AssortedRefrigeratedProduct2 = $block_variables['Assorted Refrigerated Product'];
		$Produce2 = $block_variables['Produce'];
		$AssortedMixedDry2 = $block_variables['Assorted Mixed Dry'];
		$AssortedFrozen2 = $block_variables['Assorted Frozen'];
				
		mysql_query("INSERT INTO PickupHeader (`PickupDate`, `TotalAmount`, `PickupEntryTime`, `ProgramNumber`, `DonorNumber`, `PickupType`) VALUES ('$PickupDate', '$TotalAmount', '$PickupEntryTime', '$ProgramNumber', '$DonorNumber', '1')");
		mysql_query("INSERT INTO PickupDetail (`LineNumber`,`PickupId`, `Quantity`, `ItemNumber`) VALUES ('1', last_insert_id(), '$BreadQty', '20020'),('2', last_insert_id(), '$AssortedRefrigeratedProductQty', '20039'),('3', last_insert_id(), '$ProduceQty', '25000'),('4', last_insert_id(), '$AssortedMixedDryQty', '90000'),('5', last_insert_id(), '$AssortedFrozenQty', '90030')");
		
		$msg[] = 'Pickup has been submitted'; 
		unset($errors);
	}
}

//Include the header
include('templates/header.php');
?>

	<div id="main" role="main" class="main">
	<h3 class="titlehdr">Create a New Grocery Store Pickup for <?php echo $_SESSION['Username']?></h3>
		<?php
		//Displays error and confirmation messages
		if(!empty($err)) {
			echo "<div class=\"msg\">";
		foreach ($err as $e) {
			echo "$e <br/>";
		}
			echo "</div>";	
		}
			  
		if(!empty($msg)) {
			echo "<div class=\"msg\">" . $msg[0] . "</div><br />";
		}
		?>
		
		<form name="OrderForm" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
			<div class="cf">
			<div class="itemlabels"><label for="PickupDate" class="bolded">Pickup Date <sup>*</sup></label></div>
			<div class="rightcolumn"><input name="PickupDate" type="text" id="PickupDate" size="8" readonly <?php if (isset($errors)) { echo 'value="'.htmlentities($_POST['PickupDate']).'"'; } ?>></div><?php if (isset($errors) && in_array('PickupDate', $errors)){?><div class="red">Required</div><?php } ?>
			</div>
			
			<div class="cf">
			<div class="itemlabels"><label for="ProgramNumber" class="bolded">Program <sup>*</sup></label></div>
			<div class="rightcolumn">
			<select name="ProgramNumber" class="ProgramNumber" required>
				<option value="">-- Select Program --</option>
				<?php
				$sql=mysql_query("SELECT ProgramNumber, ProgramName FROM Program");
				while($row=mysql_fetch_array($sql)) {
					$ProgramNumber=$row['ProgramNumber'];
					$ProgramName=$row['ProgramName'];
					echo '<option value="'.$ProgramNumber.'">'.$ProgramNumber.' - '.$ProgramName.'</option>';
				} 
				?>
			</select>
			</div><?php if (isset($errors) && in_array('ProgramNumber', $errors)){?><div class="red">Required</div><?php } ?>
			</div>
			
			<div class="cf">
			<div class="itemlabels"><label for="DonorNumber" class="bolded">Donor <sup>*</sup></label></div>
			<div class="rightcolumn">
			<?php
			// only display donors associated with specific program
			$result = mysql_query("SELECT DonorNumber, DonorName, Address1 FROM Donor WHERE ProgramNumber = '$_SESSION[Username]'");
			
			echo '<select name="DonorNumber"><option value="">-- Select a Donor --</option>';
			while($row = mysql_fetch_array($result)) {
				echo '<option value="'.$row['DonorNumber'].'">'. stripslashes($row['DonorName']). ' - '. stripslashes($row['Address1']). '</option>';
			}
			echo '</select>';
			?>
			</div><?php if (isset($errors) && in_array('DonorNumber', $errors)){?><div class="red">Required</div><?php } ?>
			</div>
		
			<div class="cf">
			<div class="itemlabels"><label for="BreadQty" class="bolded">Bread</label></div>
			<div class="rightcolumn"><input name="BreadQty" type="text" id="BreadQty" size="2" maxlength="4" <?php if (isset($errors)) { echo 'value="'.htmlentities($_POST['BreadQty']).'"'; } ?> /> lbs</div>
			</div>
			
			<div class="cf">
			<div class="itemlabels"><label for="AssortedRefrigeratedProductQty" class="bolded">Assorted Refrigerated Product</label></div>
			<div class="rightcolumn"><input name="AssortedRefrigeratedProductQty" type="text" id="AssortedRefrigeratedProductQty" size="2" maxlength="4" <?php if (isset($errors)) { echo 'value="'.htmlentities($_POST['AssortedRefrigeratedProductQty']).'"'; } ?> /> lbs</div>
			</div>
			
			<div class="cf">
			<div class="itemlabels"><label for="ProduceQty" class="bolded">Produce</label></div>
			<div class="rightcolumn"><input name="ProduceQty" type="text" id="ProduceQty" size="2" maxlength="4" <?php if (isset($errors)) { echo 'value="'.htmlentities($_POST['ProduceQty']).'"'; } ?> /> lbs</div>
			</div>
			
			<div class="cf">
			<div class="itemlabels"><label for="AssortedMixedDryQty" class="bolded">Assorted Mixed Dry</label></div>
			<div class="rightcolumn"><input name="AssortedMixedDryQty" type="text" id="AssortedMixedDryQty" size="2" maxlength="4" <?php if (isset($errors)) { echo 'value="'.htmlentities($_POST['AssortedMixedDryQty']).'"'; } ?> /> lbs</div>
			</div>
			
			<div class="cf">
			<div class="itemlabels"><label for="AssortedFrozenQty" class="bolded">Assorted Frozen</label></div>
			<div class="rightcolumn"><input name="AssortedFrozenQty" type="text" id="AssortedFrozenQty" size="2" maxlength="4" <?php if (isset($errors)) { echo 'value="'.htmlentities($_POST['AssortedFrozenQty']).'"'; } ?> /> lbs</div>
			</div>
					
			<p align="center"><input name="Submit" type="submit" id="Submit" value="Submit" class="awesomeb" onclick="setDefaults(); return confirm('If the quantities shown below are correct, click OK to submit your receipt.\n' + 'If the quantities are incorrect, click Cancel and adjust them.\n' + '\n' + 'Bread: ' + (document.getElementById('BreadQty').value) + 'lbs' + '\n' + 'Assorted Refrigerated Product: ' + (document.getElementById('AssortedRefrigeratedProductQty').value) + 'lbs' + '\n' + 'Produce: ' + (document.getElementById('ProduceQty').value) + 'lbs' + '\n' + 'Assorted Mixed Dry: ' + (document.getElementById('AssortedMixedDryQty').value) + 'lbs' + '\n' + 'Assorted Frozen: ' + (document.getElementById('AssortedFrozenQty').value) + 'lbs');"></p>
		</form>
	</div><!-- end of #main -->
    
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
	<script src="js/jquery.js"></script>
	<script src="js/jquery-ui-1.8.16.custom.min.js"></script>
	<!-- end scripts-->
  
	<!-- calendar script -->
	<script type="text/javaScript">
	// temp vars used below
	var currentTime = new Date() 
	if (currentTime.getDate()<=2) {
		var minDate = new Date(currentTime.getFullYear(), currentTime.getMonth() -0, -2);
	} else {
		var minDate = new Date(currentTime.getFullYear(), currentTime.getMonth());
	}
	$( "#PickupDate" ).datepicker({ 
	dateFormat: 'yy-mm-dd',
	minDate: minDate, 
	maxDate: '+0D' 
	});
	</script>
	
	<!-- Set textbox value to 0 if empty -->
	<script>
	function setDefaults(){
		if(document.CreatePickup.BreadQty.value==""){
			document.CreatePickup.BreadQty.value = "0";
		}
		
		if(document.CreatePickup.AssortedRefrigeratedProductQty.value==""){
			document.CreatePickup.AssortedRefrigeratedProductQty.value = "0";
		}
		
		if(document.CreatePickup.ProduceQty.value==""){
			document.CreatePickup.ProduceQty.value = "0";
		}

		if(document.CreatePickup.AssortedFrozenQty.value==""){
			document.CreatePickup.AssortedFrozenQty.value = "0";
		}
		
		if(document.CreatePickup.AssortedMixedDryQty.value==""){
			document.CreatePickup.AssortedMixedDryQty.value = "0";
		}
	}
	</script> 
	<!--[if lt IE 7 ]>
    <script src="js/libs/dd_belatedpng.js"></script>
    <script>DD_belatedPNG.fix("img, .png_bg"); // Fix any <img> or .png_bg bg-images. Also, please read goo.gl/mZiyb </script>
	<![endif]-->

</body>
</html>
