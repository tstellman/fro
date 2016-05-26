<?php 
include 'dbc.php';
page_protect();

if(!checkAdmin()) {
	header("Location: login.php");
	exit();
}

if ($data['DateSearch'] == 'Search') {
	
	$cond = ""; // initialize the variable and set it blank

if ($data['StartDate']) {
	$cond = "PickupDate = '$_REQUEST[StartDate]'";
}

if ($data['StartDate'] && $data['EndDate']) {
	$cond = "PickupDate BETWEEN '$_REQUEST[StartDate]' AND '$_REQUEST[EndDate]'";
}
	
if ($data['ProgramNumber']) {
	$cond = "ph.ProgramNumber = '$_REQUEST[ProgramNumber]'";
}

if ($data['StartDate'] && $data['ProgramNumber']) {
	$cond = "PickupDate = '$_REQUEST[StartDate]' AND ph.ProgramNumber = '$_REQUEST[ProgramNumber]'";
}

if ($data['StartDate'] && $data['EndDate'] && $data['ProgramNumber']) {
	$cond = "PickupDate BETWEEN '$_REQUEST[StartDate]' AND '$_REQUEST[EndDate]' AND ph.ProgramNumber = '$_REQUEST[ProgramNumber]'";
}

$searchpickups = mysql_query("SELECT ph.PickupId, p.ProgramName, d.DonorName, PickupDate,
								GROUP_CONCAT(if(LineNumber = 1, Quantity, NULL)) AS 'Bread',
								GROUP_CONCAT(if(LineNumber = 2, Quantity, NULL)) AS 'Assorted Refrigerated Product',
								GROUP_CONCAT(if(LineNumber = 3, Quantity, NULL)) AS 'Produce',
								GROUP_CONCAT(if(LineNumber = 4, Quantity, NULL)) AS 'Assorted Mixed Dry',
								GROUP_CONCAT(if(LineNumber = 5, Quantity, NULL)) AS 'Assorted Frozen',
							ph.TotalAmount AS 'Total'
							FROM PickupDetail pd, PickupHeader ph, Program p, Donor d
							WHERE ph.PickupId = pd.PickupId AND ph.ProgramNumber = p.ProgramNumber AND ph.DonorNumber = d.DonorNumber AND $cond
							GROUP BY ph.PickupId
							ORDER BY PickupDate DESC");
						
$ReceiptsPoundsQuery = mysql_query("SELECT COUNT(PickupId) AS TotalReceipts, SUM(TotalAmount) AS TotalPounds FROM PickupHeader ph WHERE $cond");
$ReceiptsPoundsSelect = mysql_fetch_array($ReceiptsPoundsQuery);
$Receipts = $ReceiptsPoundsSelect['TotalReceipts'];
$Pounds = $ReceiptsPoundsSelect['TotalPounds'];

$BreadQuery = mysql_query("SELECT SUM(Quantity) AS 'Bread' FROM PickupDetail pd, PickupHeader ph WHERE ph.PickupId = pd.PickupId AND LineNumber = 1 AND $cond");
$BreadSelect = mysql_fetch_array($BreadQuery);
$Bread = $BreadSelect['Bread'];

$AsstdRefrigQuery = mysql_query("SELECT SUM(Quantity) AS 'AsstdRefrig' FROM PickupDetail pd, PickupHeader ph WHERE ph.PickupId = pd.PickupId AND LineNumber = 2 AND $cond");
$AsstdRefrigSelect = mysql_fetch_array($AsstdRefrigQuery);
$AsstdRefrig = $AsstdRefrigSelect['AsstdRefrig'];

$ProduceQuery = mysql_query("SELECT SUM(Quantity) AS 'Produce' FROM PickupDetail pd, PickupHeader ph WHERE ph.PickupId = pd.PickupId AND LineNumber = 3 AND $cond");
$ProduceSelect = mysql_fetch_array($ProduceQuery);
$Produce = $ProduceSelect['Produce'];

$AsstdMixedDryQuery = mysql_query("SELECT SUM(Quantity) AS 'AsstdMixedDry' FROM PickupDetail pd, PickupHeader ph WHERE ph.PickupId = pd.PickupId AND LineNumber = 4 AND $cond");
$AsstdMixedDrySelect = mysql_fetch_array($AsstdMixedDryQuery);
$AsstdMixedDry = $AsstdMixedDrySelect['AsstdMixedDry'];

$AsstdFrozenQuery = mysql_query("SELECT SUM(Quantity) AS 'AsstdFrozen' FROM PickupDetail pd, PickupHeader ph WHERE ph.PickupId = pd.PickupId AND LineNumber = 5 AND $cond");
$AsstdFrozenSelect = mysql_fetch_array($AsstdFrozenQuery);
$AsstdFrozen = $AsstdFrozenSelect['AsstdFrozen'];
}

//Include the header
include('templates/header.php');
?>

	<!--Main body content-->
	<div id="main" role="main" class="main">
	<h3 class="titlehdr">Search Pickups</h3>
	
		<form name="SearchForm" action="searchpickups.php" method="post" class="SearchForm noprint">
			<div class="cf">
			<div class="searchlabels"><label for="StartDate" class="bolded">Date:</label></div>
			<div class="rightcolumn"><input name="StartDate" type="text" id="StartDate" size="7" readonly> - <input name="EndDate" id="EndDate" type="text" size="7" readonly></div>
			</div>
			
			<div class="cf">
			<div class="searchlabels"><label for="Program" class="bolded">Program:&nbsp;&nbsp;</label></div>
			<div class="rightcolumn">
				<?php
				$result = mysql_query("SELECT ProgramNumber, CONCAT(ProgramNumber,' - ',ProgramName) AS Program FROM Program ORDER BY ProgramNumber ASC") or die(mysql_error());
			
				echo '<select name="ProgramNumber"><option value=""></option>';
				while($row = mysql_fetch_array($result)) {
				echo '<option value="'.$row['ProgramNumber'].'">'. stripslashes($row['Program']). '</option>';
				}
				echo '</select>';
				?>
			</div>
			</div>
			
			<input name="DateSearch" type="submit" id="DateSearch" value="Search" class="awesomed">
		</form><br/>
	
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

		//display the search results
		while($row = mysql_fetch_array($searchpickups)) {
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
		?>
	
</div><!--end of #main-->

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
	  
	<script type="text/javaScript">
	$(document).ready(function() {
		$('#StartDate').datepicker({ dateFormat: 'yy-mm-dd' });
		$('#EndDate').datepicker({ dateFormat: 'yy-mm-dd' });
	});
	</script>

	<!--[if lt IE 7 ]>
	<script src="js/libs/dd_belatedpng.js"></script>
	<script>DD_belatedPNG.fix("img, .png_bg"); // Fix any <img> or .png_bg bg-images. Also, please read goo.gl/mZiyb </script>
	<![endif]-->

</body>
</html>
