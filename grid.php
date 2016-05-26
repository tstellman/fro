<?php
ob_start();

function loadGrid($startDate, $endDate, $AgencyNoOption, $DonorNoOption, $PickupType, $PickupTypeOption, $initAgencyNo) {

if (!empty($AgencyNoOption)){
	$AgencyNo = "AND ph.ProgramNumber = '" . $AgencyNoOption . "'";
}
if (!empty($DonorNoOption)){
	$DonorNo = "AND ph.DonorNumber = '" . $DonorNoOption . "'";
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
	
		
?>


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
		echo "<tbody><tr>";
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
		echo "<td align='left'>" . $row['ProgramName'] . "</td>";
		echo "<td align='left'>" . $row['DonorName'] . "</td>";
		echo "<td align='left'>" . $row['PickupDate'] . "</td>";
		echo "<td align='right'>" . $row['Assorted Bakery'] . "</td>";
		echo "<td align='right'>" . $row['Assorted Dairy'] . "</td>";
		echo "<td align='right'>" . $row['Assorted Produce'] . "</td>";
		echo "<td align='right'>" . $row['Assorted Meat'] . "</td>";
		echo "<td align='right'>" . $row['Assorted Grocery'] . "</td>";
		echo "<td align='right'>" . $row['Misc. & Non-Foods'] . "</td>";
		echo "<td align='right'>" . $row['Trash'] . "</td>";
		echo "<td align='right'><strong>" . $row['Total'] . "</strong></td>";
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