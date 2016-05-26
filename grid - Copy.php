<?php
function loadGrid() {
$startDate = date ('Y-m-d', strtotime ( date ( 'Y' ) . 'W' . date ( 'W' ) . '0' ) );
$endDate = date ('Y-m-d', strtotime ( date ( 'Y' ) . 'W' . date ( 'W' ) . '6' ) );
$AgencyNo = "";
$DonorNo = "";
$AgencyNoOption = "";
$DonorNoOption = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if(!empty($_POST["startDate"])){
		$startDate = $_POST["startDate"];
	} else {
		$startDate = date ('Y-m-d', strtotime ( date ( 'Y' ) . 'W' . date ( 'W' ) . '0' ) );
	}
	if(!empty($_POST["endDate"])) {
		$endDate = $_POST["endDate"];
	} else {
		$endDate = date ('Y-m-d', strtotime ( date ( 'Y' ) . 'W' . date ( 'W' ) . '6' ) );
	}
	if(!empty($_POST["AgencyNo"])){
		$AgencyNo = "AND p.ProgramNumber = '" . $_POST['AgencyNo'] . "'";
		$AgencyNoOption = $_POST['AgencyNo'];
	} else {
		$AgencyNo = "";
		$AgencyNoOption = "";
	}
	if(!empty($_POST["DonorNo"])){
		$DonorNo = "AND d.DonorNumber = '" . $_POST['DonorNo'] . "'";
		$DonorNoOption = $_POST['DonorNo'];
	} else {
		$DonorNo = "";
		$DonorNoOption = "";
	}
} else {

}
	$sunday_this_week = date ('F j', strtotime ( date ( 'Y' ) . 'W' . date ( 'W' ) . '0' ) );  //The last number is the num of the weekday. 0 being sunday.
	$saturday_this_week = date ('F j', strtotime ( date ( 'Y' ) . 'W' . date ( 'W' ) . '6' ) );  //The last number is the num of the weekday. 6 being saturday.
	$week = $sunday_this_week ." - ". $saturday_this_week;

	$ReceiptsPoundsQuery = mysql_query("SELECT COUNT(PickupId) AS TotalReceipts, SUM(TotalAmount) AS TotalPounds FROM PickupHeader WHERE (PickupDate BETWEEN '".$startDate."' AND '".$endDate."')");
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
						WHERE ph.PickupId = pd.PickupId AND ph.ProgramNumber = p.ProgramNumber AND ph.DonorNumber = d.DonorNumber AND (PickupDate BETWEEN '".$startDate."' AND '".$endDate."') " . $AgencyNo . $DonorNo . "
						GROUP BY ph.PickupId
						ORDER BY PickupDate DESC");
						
	$BreadQuery = mysql_query("SELECT SUM(Quantity) AS 'Bread' FROM PickupDetail pd, PickupHeader ph WHERE ph.PickupId = pd.PickupId AND LineNumber = 1 AND (PickupDate BETWEEN '".$startDate."' AND '".$endDate."')");
	$BreadSelect = mysql_fetch_array($BreadQuery);
	$Bread = $BreadSelect['Bread'];

	$AsstdRefrigQuery = mysql_query("SELECT SUM(Quantity) AS 'AsstdRefrig' FROM PickupDetail pd, PickupHeader ph WHERE ph.PickupId = pd.PickupId AND LineNumber = 2 AND (PickupDate BETWEEN '".$startDate."' AND '".$endDate."')");
	$AsstdRefrigSelect = mysql_fetch_array($AsstdRefrigQuery);
	$AsstdRefrig = $AsstdRefrigSelect['AsstdRefrig'];

	$ProduceQuery = mysql_query("SELECT SUM(Quantity) AS 'Produce' FROM PickupDetail pd, PickupHeader ph WHERE ph.PickupId = pd.PickupId AND LineNumber = 3 AND (PickupDate BETWEEN '".$startDate."' AND '".$endDate."')");
	$ProduceSelect = mysql_fetch_array($ProduceQuery);
	$Produce = $ProduceSelect['Produce'];

	$AsstdMixedDryQuery = mysql_query("SELECT SUM(Quantity) AS 'AsstdMixedDry' FROM PickupDetail pd, PickupHeader ph WHERE ph.PickupId = pd.PickupId AND LineNumber = 4 AND (PickupDate BETWEEN '".$startDate."' AND '".$endDate."')");
	$AsstdMixedDrySelect = mysql_fetch_array($AsstdMixedDryQuery);
	$AsstdMixedDry = $AsstdMixedDrySelect['AsstdMixedDry'];

	$AsstdFrozenQuery = mysql_query("SELECT SUM(Quantity) AS 'AsstdFrozen' FROM PickupDetail pd, PickupHeader ph WHERE ph.PickupId = pd.PickupId AND LineNumber = 5 AND (PickupDate BETWEEN '".$startDate."' AND '".$endDate."')");
	$AsstdFrozenSelect = mysql_fetch_array($AsstdFrozenQuery);
	$AsstdFrozen = $AsstdFrozenSelect['AsstdFrozen'];
?>
<br />
<br />
<h3 class="titlehdr">Receipt history</h3>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
	<table align="center">
		<thead>
			<tr>
				<th>Start Date</th>
				<th>End Date</th>
				<th>Agency No.</th>
				<th>Donor No.</th>
			</tr>
		</thead>
		
		<tr>
			<td><input type="text" name="startDate" id="startDate" class="pickDate" size="8"  value="<?php echo $startDate; ?>" /></td>
			<td><input type="text" name="endDate" size="8" class="pickDate" value="<?php echo $endDate; ?>" /></td>
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
			<td>
				<select name="DonorNo" class="DonorNo" style="width: 100px;">
				<option value="<?php echo $DonorNoOption; ?>"><?php echo $DonorNoOption; ?></option>
				<option value=""></option>
				<?php
				$sql=mysql_query("SELECT DonorNumber, DonorName FROM Donor");
				while($row=mysql_fetch_array($sql)) {
					$DonorNumber=$row['DonorNumber'];
					$DonorName=$row['DonorName'];
					echo '<option value="'.$DonorNumber.'">'.$DonorNumber.'</option>';
				} 
				?>
				</select>
			</td>
		</tr>
		<tr>
			<td></td>
			<td></td>
			<td></td>
			<td>
				<input type="submit" name="submit" size="8" value="Filter" class="awesomeb" />
				<input type="submit" name="submit" size="8" value="Export" class="awesomeb" />
			</td>
			<!--<td><?php echo $AgencyNo; ?></td>-->
			<!--<td><?php echo $DonorNo; ?></td>-->
		</tr>
	</table>
</form>



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
	
}
?>