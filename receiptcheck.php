<?php
include 'dbc.php';
page_protect();

$rs_programname = mysql_query("SELECT ProgramName FROM Program WHERE Username = '$_SESSION[Username]'");

$BreadQty = $_SESSION['BreadQtyPage2'];
$AssortedRefrigeratedProductQty = $_SESSION['AssortedRefrigeratedProductQtyPage2'];
$ProduceQty = $_SESSION['ProduceQtyPage2'];
$AssortedMixedDryQty = $_SESSION['AssortedMixedDryQtyPage2'];
$AssortedFrozenQty = $_SESSION['AssortedFrozenQtyPage2'];
$PickupDate = $_SESSION['PickupDatePage2'];
$DonorNumber = $_SESSION['DonorNumberPage2'];
$TotalAmount = $_SESSION['TotalAmountPage2'];
$ProgramNumber = $_SESSION['Username'];
$PickupEntryTime = date("Y-m-d H:i:s",time());

$result = mysql_query("SELECT DonorNumber, DonorName, Address1 FROM Donor WHERE ProgramNumber = '$_SESSION[Username]' AND DonorNumber = '$DonorNumber'");

if($_POST['Submit'] == 'Submit') {
	mysql_query("INSERT INTO PickupHeader (`PickupDate`, `TotalAmount`, `PickupEntryTime`, `ProgramNumber`, `DonorNumber`) VALUES ('$PickupDate', '$TotalAmount', '$PickupEntryTime', '$ProgramNumber', '$DonorNumber')");
									
	mysql_query("INSERT INTO PickupDetail (`LineNumber`,`PickupId`, `Quantity`, `ItemNumber`) VALUES ('1', last_insert_id(), '$BreadQty', '20020'),('2', last_insert_id(), '$AssortedRefrigeratedProductQty', '20039'),('3', last_insert_id(), '$ProduceQty', '25000'),('4', last_insert_id(), '$AssortedMixedDryQty', '90000'),('5', last_insert_id(), '$AssortedFrozenQty', '90030')");
			
	$msg[] = 'Your pickup has been submitted';
}

//Include the header
include('templates/header.php');
?>
    
	<!--Main body content-->
	<div id="main" role="main" class="main">
	<?php while ($row_settings = mysql_fetch_array($rs_programname)) {?>
	<h3 class="titlehdr"><? echo $row_settings['ProgramName']; ?> Pickup Verification</h3><br />
	<?php } ?>
	
	<?php	  
	if(!empty($msg)) {
		echo "<div class=\"msg\">" . $msg[0] . "</div><br />";
	}
	?>
	
	<div class="receiptcheck">
	<p>Hi,<br/><br/>You have an identical pickup in our system. We're just making sure you don't submit the same pickup twice. If you did, indeed, have two identical pickups click the Submit button to verify and complete your submission. If you've entered this information by error, click Cancel and your pickup will not be submitted.</p><br />
	</div>
	
	<form name="OrderForm" method="post" action="receiptcheck.php">
		<label for="PickupDate" class="bolded">Pickup Date: </label><?php echo $PickupDate?>
		<br/><br/>
	
		<label for="DonorNumber" class="bolded">Donor: </label><?php while ($row_settings = mysql_fetch_array($result)) {?><?php echo $row_settings['DonorName'] . ' - '. $row_settings['Address1']; ?><?php } ?>
		<br/><br/>
		
		<label for="BreadQty" class="bolded">Bread: </label><?php echo $BreadQty?> lbs
        <br/><br/>
		
		<label for="AssortedRefrigeratedProductQty" class="bolded">Assorted Refrigerated Product: </label><?php echo $AssortedRefrigeratedProductQty?> lbs
        <br/><br/>
		
        <label for="ProduceQty" class="bolded">Produce: </label><?php echo $ProduceQty?> lbs
        <br/><br/>
		
        <label for="AssortedMixedDryQty" class="bolded">Assorted Mixed Dry: </label><?php echo $AssortedMixedDryQty?> lbs
        <br/><br/>

		<label for="AssortedFrozenQty" class="bolded">Assorted Frozen: </label><?php echo $AssortedFrozenQty?> lbs
        <br/><br/>

		<p align="center">
        <input name="Submit" type="submit" id="Submit" value="Submit" class="awesomeb" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input name="Cancel" type="button"  value="Cancel" onclick="window.location = 'myaccount.php'" class="awesomeb" />
		</p>
	</form>
	
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
	<!-- end scripts-->

	<!--[if lt IE 7 ]>
	<script src="js/libs/dd_belatedpng.js"></script>
	<script>DD_belatedPNG.fix("img, .png_bg"); // Fix any <img> or .png_bg bg-images. Also, please read goo.gl/mZiyb </script>
	<![endif]-->

</body>
</html>
