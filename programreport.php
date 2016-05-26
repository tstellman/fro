<?php 
include 'dbc.php';
include 'grid.php';
include 'chart.php';
page_protect();

//Include the header
include('templates/header.php');

/*$startDate = "";
$endDate = "";

$AgencyNo = "";
$AgencyNoOption = "";

$DonorNo = "";
$DonorNoOption = "";

$PickupType = "";
$PickupTypeOption = "";*/

$startDate = date ('Y-m-d', strtotime ( date ( 'Y' ) . 'W' . date ( 'W' ) . '0' ) );
$endDate = date ('Y-m-d', strtotime ( date ( 'Y' ) . 'W' . date ( 'W' ) . '6' ) );

if(!empty($_REQUEST['sd'])){
	$startDate = $_REQUEST['sd'];
}
if(!empty($_REQUEST['ed'])){
	$endDate = $_REQUEST['ed'];
}
if(!empty($_REQUEST['an'])){
	$AgencyNoOption = $_REQUEST['an'];
}
if(!empty($_REQUEST['dn'])){
	$DonorNoOption = $_REQUEST['dn'];
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if(!empty($_POST["startDate"])){
		$startDate = $_POST["startDate"];
	} else {
		$startDate = date ('Y-m-d', strtotime('first day of this month'));
		#$startDate = date ('Y-m-d', strtotime ( date ( 'Y' ) . 'W' . date ( 'W' ) . '0' ) );
	}
	if(!empty($_POST["endDate"])) {
		$endDate = $_POST["endDate"];
	} else {
		$endDate = date ('Y-m-d');
		#$endDate = date ('Y-m-d', strtotime ( date ( 'Y' ) . 'W' . date ( 'W' ) . '6' ) );
	}
	if(!empty($_POST["AgencyNo"])){
		$AgencyNo = "AND ph.ProgramNumber = '" . $_POST['AgencyNo'] . "'";
		$AgencyNoOption = $_POST['AgencyNo'];
	} else {
		$AgencyNo = "";
		$AgencyNoOption = "";
	}
	if(!empty($_POST["DonorNo"])){
		$DonorNo = "AND ph.DonorNumber = '" . $_POST['DonorNo'] . "'";
		$DonorNoOption = $_POST['DonorNo'];
	} else {
		$DonorNo = "";
		$DonorNoOption = "";
	}
	
	if(!empty($_POST["PickupType"])){
		switch ($_POST["PickupType"]) {
			case "Grocery Store":
				$PickupType = "AND ph.PickupType = 1";
				$PickupTypeOption = $_POST["PickupType"]; 
				break;
			case "Food Rescue":
				$PickupType = "AND ph.PickupType = 2";
				$PickupTypeOption = $_POST["PickupType"];
				break;
			case "Food Drive":
				$PickupType = "AND ph.PickupType = 3";
				$PickupTypeOption = $_POST["PickupType"];
				break;
		}	
	} else {
		$PickupType = "";
		$PickupTypeOption = "";
	}
	
	if (checkAdmin()) {
		$initAgencyNo = "";	
	} else {
		$initAgencyNo = "AND ph.ProgramNumber = '" . $_SESSION['Username'] . "'";
	}
}
?>

	<!--Main body content-->
<div id="main" role="main" class="main">
<h3 class="titlehdr">Receipt History</h3>

	
<?php loadChart($_REQUEST[t], $startDate, $endDate, $AgencyNoOption, $DonorNoOption, $PickupType, $PickupTypeOption, $initAgencyNo); ?>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
	<table align="center">
		<thead>
			<tr>
				<th>Start Date</th>
				<th>End Date</th>
				<th>Pickup Type</th>
				<?php if (isset($_SESSION['UserId'])) {?><?php }if (checkAdmin()) {?>
				<th>Agency No.</th>
				<?php } ?>
				<th>Donor No.</th>
			</tr>
		</thead>
		
		<tr>
			<td><input type="text" name="startDate" id="startDate" class="pickDate" size="8"  value="<?php echo $startDate; ?>" /></td>
			<td><input type="text" name="endDate" size="8" class="pickDate" value="<?php echo $endDate; ?>" /></td>
			<td>
				<select name="PickupType" class="PickupType" style="width:100px;">
					<option value="<?php echo $PickupTypeOption; ?>"><?php echo $PickupTypeOption; ?></option>
					<option value=""></option>
					<option value="Grocery Store">Grocery Store</option>
					<option value="Food Rescue">Food Rescue</option>
					<option value="Food Drive">Food Drive</option>
				</select>
			</td>
			<?php if (isset($_SESSION['UserId'])) {?><?php }if (checkAdmin()) {?>
			<td>
				<select name="AgencyNo" class="AgencyNo" style="width: 100px;" >
				<option value="<?php echo $AgencyNoOption; ?>"><?php echo $AgencyNoOption; ?></option>
				<option value=""></option>
				<?php
				$sql=mysql_query("SELECT ProgramNumber, ProgramName FROM Program");
				while($row=mysql_fetch_array($sql)) {
					$ProgramNumber=$row['ProgramNumber'];
					$ProgramName=$row['ProgramName'];
					echo '<option value="'.$ProgramNumber.'">'.$ProgramNumber.'</option>';
				} 
				?>
				</select>
			</td>
			<?php } ?>
			<td>
				<select name="DonorNo" class="DonorNo" style="width: 100px;">
				<option value="<?php echo $DonorNoOption; ?>"><?php echo $DonorNoOption; ?></option>
				<option value=""></option>
				<?php
				if (checkAdmin()) {
					$queryAgency = "";
				} else {
					$queryAgency = "WHERE ProgramNumber='".$_SESSION["Username"]."'";
				}
				$sql=mysql_query("SELECT DonorNumber, DonorName FROM Donor ".$queryAgency);
				while($row=mysql_fetch_array($sql)) {
					$DonorNumber=$row['DonorNumber'];
					$DonorName=$row['DonorName'];
					echo '<option value="'.$DonorNumber.'">'.$DonorNumber.'</option>';
				}
				?>
				</select>
			</td>
			<td><input type="submit" name="submit" size="8" value="Filter" class="awesomeb" />
				<?php 
				if(checkAdmin()) {
					$location = "export.php?Type=Agency&StartDate=$startDate&EndDate=$endDate&AgencyNo=".$_POST['AgencyNo']."&DonorNo=".$_POST['DonorNo']."&PickupType=".$_POST['PickupType'];	
				} else {
					$location = "export.php?Type=Agency&StartDate=$startDate&EndDate=$endDate&Agency=".$_SESSION['Username']."&DonorNo=".$_POST['DonorNo']."&PickupType=".$_POST['PickupType'];	
				}
				 
				?>
			</td>
			<td><input type="button" name="export" size="8" value="Export" class="awesomeb" onclick="location.href='<?php echo $location ?>';" /></td>
		</tr>
	</table>
</form>

<?php loadGrid($startDate, $endDate, $AgencyNoOption, $DonorNoOption, $PickupType, $PickupTypeOption, $initAgencyNo); ?>
	</div>
	<!--end of #main-->

<!--Include the footer-->
<?php
include('templates/footer.html');
?>
	
</div> <!--end of #container-->

<!-- JavaScript at the bottom for fast page loading -->
	<script src='fusioncharts/js/fusioncharts.js'></script>
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
	
	<!--[if lt IE 7 ]>
	<script src="js/libs/dd_belatedpng.js"></script>
	<script>DD_belatedPNG.fix("img, .png_bg"); // Fix any <img> or .png_bg bg-images. Also, please read goo.gl/mZiyb </script>
	<![endif]-->

</body>
</html>
