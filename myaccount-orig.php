<?php
include 'dbc.php';
include 'grid.php';
page_protect();

$rs_programname = mysql_query("SELECT ProgramName FROM Program WHERE Username = '$_SESSION[Username]'");

$sunday_this_week = date ('F j', strtotime ( date ( 'Y' ) . 'W' . date ( 'W' ) . '0' ) );  //The last number is the num of the weekday. 0 being sunday.
$saturday_this_week = date ('F j', strtotime ( date ( 'Y' ) . 'W' . date ( 'W' ) . '6' ) );  //The last number is the num of the weekday. 6 being saturday.
$week = $sunday_this_week ." - ". $saturday_this_week;

$ReceiptsPoundsQuery = mysql_query("SELECT COUNT(PickupId) AS TotalReceipts, SUM(TotalAmount) AS TotalPounds FROM PickupHeader WHERE YEARWEEK(PickupEntryTime) = YEARWEEK(CURRENT_DATE)");
$ReceiptsPoundsSelect = mysql_fetch_array($ReceiptsPoundsQuery);
$Receipts = $ReceiptsPoundsSelect['TotalReceipts'];
$Pounds = $ReceiptsPoundsSelect['TotalPounds'];

// query to display table with current week's records
$result = mysql_query("SELECT ph.PickupId, p.ProgramName, d.DonorName, PickupDate,
							GROUP_CONCAT(if(LineNumber = 1, Quantity, NULL)) AS 'Bread',
							GROUP_CONCAT(if(LineNumber = 2, Quantity, NULL)) AS 'Assorted Refrigerated Product',
							GROUP_CONCAT(if(LineNumber = 3, Quantity, NULL)) AS 'Produce',
							GROUP_CONCAT(if(LineNumber = 4, Quantity, NULL)) AS 'Assorted Mixed Dry',
							GROUP_CONCAT(if(LineNumber = 5, Quantity, NULL)) AS 'Assorted Frozen',
						ph.TotalAmount AS 'Total'
						FROM PickupDetail pd, PickupHeader ph, Program p, Donor d
						WHERE ph.PickupId = pd.PickupId AND ph.ProgramNumber = p.ProgramNumber AND ph.DonorNumber = d.DonorNumber AND YEARWEEK(PickupEntryTime) = YEARWEEK(CURRENT_DATE)
						GROUP BY ph.PickupId
						ORDER BY PickupDate DESC");
						
$BreadQuery = mysql_query("SELECT SUM(Quantity) AS 'Bread' FROM PickupDetail pd, PickupHeader ph WHERE ph.PickupId = pd.PickupId AND LineNumber = 1 AND YEARWEEK(PickupEntryTime) = YEARWEEK(CURRENT_DATE)");
$BreadSelect = mysql_fetch_array($BreadQuery);
$Bread = $BreadSelect['Bread'];

$AsstdRefrigQuery = mysql_query("SELECT SUM(Quantity) AS 'AsstdRefrig' FROM PickupDetail pd, PickupHeader ph WHERE ph.PickupId = pd.PickupId AND LineNumber = 2 AND YEARWEEK(PickupEntryTime) = YEARWEEK(CURRENT_DATE)");
$AsstdRefrigSelect = mysql_fetch_array($AsstdRefrigQuery);
$AsstdRefrig = $AsstdRefrigSelect['AsstdRefrig'];

$ProduceQuery = mysql_query("SELECT SUM(Quantity) AS 'Produce' FROM PickupDetail pd, PickupHeader ph WHERE ph.PickupId = pd.PickupId AND LineNumber = 3 AND YEARWEEK(PickupEntryTime) = YEARWEEK(CURRENT_DATE)");
$ProduceSelect = mysql_fetch_array($ProduceQuery);
$Produce = $ProduceSelect['Produce'];

$AsstdMixedDryQuery = mysql_query("SELECT SUM(Quantity) AS 'AsstdMixedDry' FROM PickupDetail pd, PickupHeader ph WHERE ph.PickupId = pd.PickupId AND LineNumber = 4 AND YEARWEEK(PickupEntryTime) = YEARWEEK(CURRENT_DATE)");
$AsstdMixedDrySelect = mysql_fetch_array($AsstdMixedDryQuery);
$AsstdMixedDry = $AsstdMixedDrySelect['AsstdMixedDry'];

$AsstdFrozenQuery = mysql_query("SELECT SUM(Quantity) AS 'AsstdFrozen' FROM PickupDetail pd, PickupHeader ph WHERE ph.PickupId = pd.PickupId AND LineNumber = 5 AND YEARWEEK(PickupEntryTime) = YEARWEEK(CURRENT_DATE)");
$AsstdFrozenSelect = mysql_fetch_array($AsstdFrozenQuery);
$AsstdFrozen = $AsstdFrozenSelect['AsstdFrozen'];

// if pickup is submitted
if (array_key_exists('Submit',$_POST)) {
	
	// Fields that are on form
	$expected = array('PickupDate', 'DonorNumber', 'BreadQty', 'AssortedRefrigeratedProductQty', 'ProduceQty', 'AssortedMixedDryQty', 'AssortedFrozenQty');
	// Set required fields
	$required = array('PickupDate', 'DonorNumber',);
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
		$ProgramNumber = $_SESSION['Username'];
		$TotalAmount = $BreadQty + $AssortedRefrigeratedProductQty + $ProduceQty + $AssortedMixedDryQty + $AssortedFrozenQty;
		$PickupEntryTime = date("Y-m-d H:i:s",time());
		
		// Select donor information based off user selection in drop down
		$sql = mysql_query("SELECT * FROM Donor WHERE ProgramNumber = '$_SESSION[Username]' AND DonorNumber = '$DonorNumber'");
		$row_settings = mysql_fetch_array($sql);
		$FBCProductCategory = $row_settings['FBCProductCategory'];
		$FBCProductSource = $row_settings['FBCProductSource'];
		$FBCReason = $row_settings['FBCReason'];
		
		//Block Program from submitting a receipt of 0 Qty
		if($BreadQty == 0 && $AssortedRefrigeratedProductQty == 0 && $ProduceQty == 0  && $AssortedMixedDryQty == 0 && $AssortedFrozenQty == 0 ) {
			die("You cannot submit a pickup with 0 Quantity. Please go <a href='myaccount.php'>back</a> and try again.");
		}
		
		$block_order = mysql_query("SELECT oh.PickupId, oh.ProgramNumber, oh.DonorNumber, PickupDate,
										GROUP_CONCAT(if(LineNumber = 1, Quantity = $BreadQty, NULL)) AS 'Bread',
										GROUP_CONCAT(if(LineNumber = 2, Quantity = $AssortedRefrigeratedProductQty, NULL)) AS 'Assorted Refrigerated Product',
										GROUP_CONCAT(if(LineNumber = 3, Quantity = $ProduceQty, NULL)) AS 'Produce',
										GROUP_CONCAT(if(LineNumber = 4, Quantity = $AssortedMixedDryQty, NULL)) AS 'Assorted Mixed Dry',
										GROUP_CONCAT(if(LineNumber = 5, Quantity = $AssortedFrozenQty, NULL)) AS 'Assorted Frozen'
									FROM PickupDetail od, PickupHeader oh
									WHERE od.PickupId = oh.PickupId AND ProgramNumber = '$ProgramNumber' AND DonorNumber = '$DonorNumber' AND PickupDate = '$PickupDate' GROUP BY oh.PickupId ORDER BY PickupDate DESC");
									
		$block_variables = mysql_fetch_array($block_order);
		$Bread2 = $block_variables['Bread'];
		$AssortedRefrigeratedProduct2 = $block_variables['Assorted Refrigerated Product'];
		$Produce2 = $block_variables['Produce'];
		$AssortedMixedDry2 = $block_variables['Assorted Mixed Dry'];
		$AssortedFrozen2 = $block_variables['Assorted Frozen'];
		
		//Set Session Variables for next page
		$_SESSION['BreadQtyPage2'] = $BreadQty;
		$_SESSION['AssortedRefrigeratedProductQtyPage2'] = $AssortedRefrigeratedProductQty;
		$_SESSION['ProduceQtyPage2'] = $ProduceQty;
		$_SESSION['AssortedMixedDryQtyPage2'] = $AssortedMixedDryQty;
		$_SESSION['AssortedFrozenQtyPage2'] = $AssortedFrozenQty;
		$_SESSION['PickupDatePage2'] = $PickupDate;
		$_SESSION['DonorNumberPage2'] = $DonorNumber;
		$_SESSION['TotalAmountPage2'] = $TotalAmount;

		//Redirects to receiptcheck.php page if matching record is found
		if($Bread2 == 1 && $AssortedRefrigeratedProduct2 == 1 && $Produce2 == 1 && $AssortedMixedDry2 == 1 && $AssortedFrozen2 == 1) {
			/* Redirect browser */
			echo '<META HTTP-EQUIV="Refresh" Content="0; URL=receiptcheck.php">';
			exit;
		} else { }
				
		mysql_query("INSERT INTO PickupHeader (`PickupDate`, `TotalAmount`, `PickupEntryTime`, `ProgramNumber`, `DonorNumber`) VALUES ('$PickupDate', '$TotalAmount', '$PickupEntryTime', '$ProgramNumber', '$DonorNumber')");					
		
		mysql_query("INSERT INTO PickupDetail (`LineNumber`,`PickupId`, `Quantity`, `ItemNumber`) VALUES ('1', last_insert_id(), '$BreadQty', '20020'),('2', last_insert_id(), '$AssortedRefrigeratedProductQty', '20039'),('3', last_insert_id(), '$ProduceQty', '25000'),('4', last_insert_id(), '$AssortedMixedDryQty', '90000'),('5', last_insert_id(), '$AssortedFrozenQty', '90030')");
		
		$msg[] = 'Your pickup has been submitted';
		unset($errors);
	}
}

//Include the header
include('templates/header.php');
?>
    
	<!--Main body content-->
	<div id="main" role="main" class="main">
	
		<!-- Display the following if Admin user -->
		<?php if (isset($_SESSION['UserId'])) {?><?php }if (checkAdmin()) {?>
		<h3 class="titlehdr">Receipt history for week of <?php echo $week;?></h3>
		
		<?php
		echo "<table id='box-table-a'>
		<thead>
		<tr>
		<th scope='col'>Program</th>
		<th scope='col'>Donor</th>
		<th scope='col'>Pickup Date</th>
		<th scope='col'>Bread</th>
		<th scope='col'>Asstd Refrig</th>
		<th scope='col'>Produce</th>
		<th scope='col'>Asstd Mixed Dry</th>
		<th scope='col'>Asstd Frozen</th>
		<th scope='col'>Total</th>
		</tr>
		</thead>";

		while($row = mysql_fetch_array($result)) {
		echo "<tbody><tr align='center'>";
		echo "<td>" . $row['ProgramName'] . "</td>";
		echo "<td>" . $row['DonorName'] . "</td>";
		echo "<td>" . $row['PickupDate'] . "</td>";
		echo "<td>" . $row['Bread'] . "</td>";
		echo "<td>" . $row['Assorted Refrigerated Product'] . "</td>";
		echo "<td>" . $row['Produce'] . "</td>";
		echo "<td>" . $row['Assorted Mixed Dry'] . "</td>";
		echo "<td>" . $row['Assorted Frozen'] . "</td>";
		echo "<td><strong>" . $row['Total'] . "</strong></td>";
		echo "</tr></tbody>";
		}
		echo "<tbody><tr align='center'>";
		echo "<td align='right' colspan=3><strong>Total Pickups: " . $Receipts . "</strong></td>";
		echo "<td><strong>" . $Bread . "</strong></td>";
		echo "<td><strong>" . $AsstdRefrig . "</strong></td>";
		echo "<td><strong>" . $Produce . "</strong></td>";
		echo "<td><strong>" . $AsstdMixedDry . "</strong></td>";
		echo "<td><strong>" . $AsstdFrozen . "</strong></td>";
		echo "<td><strong>" . $Pounds . "</strong></td>";
		echo "</tr></tbody>";
		echo "</table>";
		
		loadGrid();
		?>
	
		<?php } ?><!-- End of Admin view -->
	
		<!-- Display the following if Program -->
		<?php if (isset($_SESSION['UserId'])) {?><?php }if (checkProgram()) {?>
		<?php while ($row_settings = mysql_fetch_array($rs_programname)) {?>
		<h3 class="titlehdr">Welcome <?php echo $row_settings['ProgramName']; ?><?php echo "test"; ?></h3>
		<?php } ?>
		
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
			echo "<div class=\"msg\">" . $msg[0] . "</div><br/>";
		}
		?>
	
		<form name="OrderForm" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
			<div class="cf">
			<div class="itemlabels"><label for="PickupDate" class="bolded">Pickup Date <sup>*</sup></label></div>
			<div class="rightcolumn"><input name="PickupDate" type="text" id="PickupDate" size="8" readonly <?php if (isset($errors)) { echo 'value="'.htmlentities($_POST['PickupDate']).'"'; } ?>></div><?php if (isset($errors) && in_array('PickupDate', $errors)){?><div class="red">Required</div><?php } ?>
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
			<div class="itemlabels"><label for="AssortedRefrigeratedProduceQty" class="bolded">Assorted Refrigerated Product</label></div>
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
		<?php } ?> <!-- End of Program view -->
	
	
	</div> <!-- end of #main -->
    
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
	// temp vars used below; show current month up to current day, unless first 2 days of month then allow selection of previous month back 3 days
	var currentTime = new Date() 
	if (currentTime.getDate()<=0) {
		var minDate = new Date(currentTime.getFullYear() -1, currentTime.getMonth() -0, -2);
	} else {
		var minDate = new Date(currentTime.getFullYear() -1, currentTime.getMonth());
	}
	$( ".pickDate" ).datepicker({
	dateFormat: 'yy-mm-dd',
	minDate: minDate, 
	maxDate: '+0D' 
	});
	</script>
	
	<!-- Set textbox value to 0 if empty -->
	<script>
	function setDefaults(){
		if(document.OrderForm.BreadQty.value==""){
	        document.OrderForm.BreadQty.value = "0";
	    }
		
		if(document.OrderForm.AssortedRefrigeratedProductQty.value==""){
	        document.OrderForm.AssortedRefrigeratedProductQty.value = "0";
	    }
		
		if(document.OrderForm.ProduceQty.value==""){
	        document.OrderForm.ProduceQty.value = "0";
	    }
	
	    if(document.OrderForm.AssortedFrozenQty.value==""){
	        document.OrderForm.AssortedFrozenQty.value = "0";
	    }
		
		if(document.OrderForm.AssortedMixedDryQty.value==""){
	        document.OrderForm.AssortedMixedDryQty.value = "0";
	    }
	}
	</script>
	
	<!--[if lt IE 7 ]>
	<script src="js/libs/dd_belatedpng.js"></script>
	<script>DD_belatedPNG.fix("img, .png_bg"); // Fix any <img> or .png_bg bg-images. Also, please read goo.gl/mZiyb </script>
	<![endif]-->

</body>
</html>
