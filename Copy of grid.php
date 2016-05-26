<?php
ob_start();
function loadGrid($yes) {
$startDate = date ('Y-m-d', strtotime ( date ( 'Y' ) . 'W' . date ( 'W' ) . '0' ) );
$endDate = date ('Y-m-d', strtotime ( date ( 'Y' ) . 'W' . date ( 'W' ) . '6' ) );

if (checkAdmin()) {
	$initAgencyNo = "";	
} else {
	$initAgencyNo = "AND ph.ProgramNumber = '" . $_SESSION['Username'] . "'";
}
$AgencyNo = "";
$AgencyNoOption = "";

$DonorNo = "";
$DonorNoOption = "";

$PickupType = "";
$PickupTypeOption = "";

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
} else {

}
	$sunday_this_week = date ('F j', strtotime ( date ( 'Y' ) . 'W' . date ( 'W' ) . '0' ) );  //The last number is the num of the weekday. 0 being sunday.
	$saturday_this_week = date ('F j', strtotime ( date ( 'Y' ) . 'W' . date ( 'W' ) . '6' ) );  //The last number is the num of the weekday. 6 being saturday.
	$week = $sunday_this_week ." - ". $saturday_this_week;

	$ReceiptsPoundsQuery = mysql_query("SELECT COUNT(PickupId) AS TotalReceipts, SUM(TotalAmount) AS TotalPounds 
									    FROM PickupHeader ph
									    WHERE (PickupDate BETWEEN '".$startDate."' AND '".$endDate."') ". $initAgencyNo . $AgencyNo . $DonorNo . $PickupType);
	$ReceiptsPoundsSelect = mysql_fetch_array($ReceiptsPoundsQuery);
	$Receipts = $ReceiptsPoundsSelect['TotalReceipts'];
	$Pounds = $ReceiptsPoundsSelect['TotalPounds'];

	// query to display table with current week's records
	$result = mysql_query("SELECT ph.PickupId, p.ProgramName, d.DonorName, PickupDate, ph.PickupType,
							GROUP_CONCAT(if(LineNumber = 1, Quantity, NULL)) AS 'Assorted Bakery',
							GROUP_CONCAT(if(LineNumber = 2, Quantity, NULL)) AS 'Assorted Dairy',
							GROUP_CONCAT(if(LineNumber = 3, Quantity, NULL)) AS 'Assorted Produce',
							GROUP_CONCAT(if(LineNumber = 4, Quantity, NULL)) AS 'Assorted Meat',
							GROUP_CONCAT(if(LineNumber = 5, Quantity, NULL)) AS 'Misc. & Non-Foods',
							GROUP_CONCAT(if(LineNumber = 6, Quantity, NULL)) AS 'Trash',
							GROUP_CONCAT(if(LineNumber = 7, Quantity, NULL)) AS 'Assorted Grocery',
						ph.TotalAmount AS 'Total'
						FROM PickupDetail pd, PickupHeader ph, Program p, Donor d
						WHERE ph.PickupId = pd.PickupId AND ph.ProgramNumber = p.ProgramNumber AND ph.DonorNumber = d.DonorNumber AND (PickupDate BETWEEN '".$startDate."' AND '".$endDate."') " . $initAgencyNo . $AgencyNo . $DonorNo . $PickupType ."
						GROUP BY ph.PickupId
						ORDER BY PickupDate DESC");
						
	$BakeryQuery = mysql_query("SELECT SUM(Quantity) AS 'Bakery' FROM PickupDetail pd, PickupHeader ph WHERE ph.PickupId = pd.PickupId AND LineNumber = 1 AND (PickupDate BETWEEN '".$startDate."' AND '".$endDate."')". $initAgencyNo . $AgencyNo . $DonorNo . $PickupType);
	$BakerySelect = mysql_fetch_array($BakeryQuery);
	$Bakery = $BakerySelect['Bakery'];

	$DairyQuery = mysql_query("SELECT SUM(Quantity) AS 'Dairy' FROM PickupDetail pd, PickupHeader ph WHERE ph.PickupId = pd.PickupId AND LineNumber = 2 AND (PickupDate BETWEEN '".$startDate."' AND '".$endDate."')". $initAgencyNo . $AgencyNo . $DonorNo . $PickupType);
	$DairySelect = mysql_fetch_array($DairyQuery);
	$Dairy = $DairySelect['Dairy'];

	$ProduceQuery = mysql_query("SELECT SUM(Quantity) AS 'Produce' FROM PickupDetail pd, PickupHeader ph WHERE ph.PickupId = pd.PickupId AND LineNumber = 3 AND (PickupDate BETWEEN '".$startDate."' AND '".$endDate."')". $initAgencyNo . $AgencyNo . $DonorNo . $PickupType);
	$ProduceSelect = mysql_fetch_array($ProduceQuery);
	$Produce = $ProduceSelect['Produce'];

	$MeatQuery = mysql_query("SELECT SUM(Quantity) AS 'Meat' FROM PickupDetail pd, PickupHeader ph WHERE ph.PickupId = pd.PickupId AND LineNumber = 4 AND (PickupDate BETWEEN '".$startDate."' AND '".$endDate."')". $initAgencyNo . $AgencyNo . $DonorNo . $PickupType);
	$MeatSelect = mysql_fetch_array($MeatQuery);
	$Meat = $MeatSelect['Meat'];

	$MiscQuery = mysql_query("SELECT SUM(Quantity) AS 'Misc' FROM PickupDetail pd, PickupHeader ph WHERE ph.PickupId = pd.PickupId AND LineNumber = 5 AND (PickupDate BETWEEN '".$startDate."' AND '".$endDate."')". $initAgencyNo . $AgencyNo . $DonorNo . $PickupType);
	$MiscSelect = mysql_fetch_array($MiscQuery);
	$Misc = $MiscSelect['Misc'];
	
	$TrashQuery = mysql_query("SELECT SUM(Quantity) AS 'Trash' FROM PickupDetail pd, PickupHeader ph WHERE ph.PickupId = pd.PickupId AND LineNumber = 6 AND (PickupDate BETWEEN '".$startDate."' AND '".$endDate."')". $initAgencyNo . $AgencyNo . $DonorNo . $PickupType);
	$TrashSelect = mysql_fetch_array($TrashQuery);
	$Trash = $TrashSelect['Trash'];
	
	$GroceryQuery = mysql_query("SELECT SUM(Quantity) AS 'Grocery' FROM PickupDetail pd, PickupHeader ph WHERE ph.PickupId = pd.PickupId AND LineNumber = 7 AND (PickupDate BETWEEN '".$startDate."' AND '".$endDate."')". $initAgencyNo . $AgencyNo . $DonorNo . $PickupType);
	$GrocerySelect = mysql_fetch_array($GroceryQuery);
	$Grocery = $GrocerySelect['Grocery'];
	
	if (checkAdmin()||checkProgram()){
	if(!empty($yes)){
		
		// file name for download
  		$filename = "website_data_" . date('Ymd') . ".csv";

  		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Type: application/vnd.ms-excel");

		$flag = false;
  		while($row = mysql_fetch_row($result)) {
    		if(!$flag) {
      			// display field/column names as first row
      			echo implode("\t", array_keys($row)) . "\n";
      			$flag = true;
    		}
    	echo implode("\t", array_values($row)) . "\n";
  		}

  exit;
	}
}
	
?>
<h3 class="titlehdr">Receipt History</h3>
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
		</tr>
		<tr>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td>
				<input type="submit" name="submit" size="8" value="Filter" class="awesomeb" />
				<input type="button" name="export" size="8" value="Export" class="awesomeb" onclick="location.href='?e=yes';" />
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
		<th scope='col'>Type</th>
		<th scope='col'>Agency</th>
		<th scope='col'>Donor</th>
		<th scope='col'>Pickup Date</th>
		<th scope='col'>Bakery</th>
		<th scope='col'>Dairy</th>
		<th scope='col'>Produce</th>
		<th scope='col'>Meat</th>
		<th scope='col'>Grocery</th>
		<th scope='col'>Misc.</th>
		<th scope='col'>Trash</th>
		<th scope='col'>Total</th>
		</tr>
		</thead>";

		while($row = mysql_fetch_array($result)) {
		echo "<tbody><tr align='center'>";
			switch($row['PickupType']) {
				case 1:
					echo "<td>Grocery Store</td>";
					break;
				case 2:
					echo "<td>Food Rescue</td>";
					break;
				case 3:
					echo "<td>Food Drive</td>";
					break;
			}
		echo "<td>" . $row['ProgramName'] . "</td>";
		echo "<td>" . $row['DonorName'] . "</td>";
		echo "<td>" . $row['PickupDate'] . "</td>";
		echo "<td>" . $row['Assorted Bakery'] . "</td>";
		echo "<td>" . $row['Assorted Dairy'] . "</td>";
		echo "<td>" . $row['Assorted Produce'] . "</td>";
		echo "<td>" . $row['Assorted Meat'] . "</td>";
		echo "<td>" . $row['Assorted Grocery'] . "</td>";
		echo "<td>" . $row['Misc. & Non-Foods'] . "</td>";
		echo "<td>" . $row['Trash'] . "</td>";
		echo "<td><strong>" . $row['Total'] . "</strong></td>";
		echo "</tr></tbody>";
		}
		echo "<tbody><tr align='center'>";
		echo "<td align='right' colspan=4><strong>Total Pickups: " . $Receipts . "</strong></td>";
		echo "<td><strong>" . $Bakery . "</strong></td>";
		echo "<td><strong>" . $Dairy . "</strong></td>";
		echo "<td><strong>" . $Produce . "</strong></td>";
		echo "<td><strong>" . $Meat . "</strong></td>";
		echo "<td><strong>" . $Grocery . "</strong></td>";
		echo "<td><strong>" . $Misc . "</strong></td>";
		echo "<td><strong>" . $Trash . "</strong></td>";
		echo "<td><strong>" . $Pounds . "</strong></td>";
		echo "</tr></tbody>";
		echo "</table>";
	
}
?>