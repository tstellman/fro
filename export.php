<?php
ob_start();
include 'dbc.php';

if(!empty($_REQUEST['StartDate'])){
	$exStartDate = $_REQUEST['StartDate'];	
} else {
	$exStartDate = date('Y-m-d', strtotime('first day of this month'));
}

if(!empty($_REQUEST['EndDate'])){
	$exEndDate = $_REQUEST['EndDate'];
} else {
	$exEndDate = date('Y-m-d');
}

function nz0($test){
	if($test == 0){
		return '';
	} else {
		return $test;
	}
}

if($_REQUEST['Type']=='CeresNP'){
//Code to generate the Ceres Non-Produce GSR Import
$valuesSQL = "SELECT ph.PickupId, p.ProgramName, p.ProgramNumber, d.DonorName, ph.DonorNumber, PickupDate, ph.PickupType, ph.TotalAmount AS 'Total', 
					   SUM(CASE WHEN pd.LineNumber = 1 THEN pd.Quantity ELSE 0 END) as 'Assorted Bakery',
					   SUM(CASE WHEN pd.LineNumber = 2 THEN pd.Quantity ELSE 0 END) as 'Assorted Dairy',
					   SUM(CASE WHEN pd.LineNumber = 3 THEN pd.Quantity ELSE 0 END) as 'Assorted Produce',
					   SUM(CASE WHEN pd.LineNumber = 4 THEN pd.Quantity ELSE 0 END) as 'Assorted Meat',
					   SUM(CASE WHEN pd.LineNumber = 5 THEN pd.Quantity ELSE 0 END) as 'Misc. & Non-Foods',
					   SUM(CASE WHEN pd.LineNumber = 6 THEN pd.Quantity ELSE 0 END) as 'Trash',
					   SUM(CASE WHEN pd.LineNumber = 7 THEN pd.Quantity ELSE 0 END) as 'Assorted Grocery',
					   CASE WHEN ph.PickupType = 3 THEN ph.TotalAmount ELSE 0 END as 'Food Drive',
					   CASE WHEN ph.PickupType = 2 THEN ph.TotalAmount ELSE 0 END as 'Food Rescue',
					   p.County
					   FROM PickupDetail pd, PickupHeader ph, Program p, Donor d
					   WHERE ph.PickupId = pd.PickupId AND ph.ProgramNumber = p.ProgramNumber AND ph.DonorNumber = d.DonorNumber AND (PickupDate BETWEEN '$exStartDate' AND '$exEndDate')
					   GROUP BY ph.PickupId
					   ORDER BY PickupDate ASC";
					   
$sumsSQL = "SELECT SUM(CASE WHEN LineNumber = 1 THEN pd.Quantity ELSE 0 END) as 'Assorted Bakery Total',
	   			   SUM(CASE WHEN pd.LineNumber = 2 THEN pd.Quantity ELSE 0 END) as 'Assorted Dairy Total',
				   SUM(CASE WHEN pd.LineNumber = 3 THEN pd.Quantity ELSE 0 END) as 'Assorted Produce Total',
				   SUM(CASE WHEN pd.LineNumber = 4 THEN pd.Quantity ELSE 0 END) as 'Assorted Meat Total',
				   SUM(CASE WHEN pd.LineNumber = 5 THEN pd.Quantity ELSE 0 END) as 'Misc. & Non-Foods Total',
				   SUM(CASE WHEN pd.LineNumber = 6 THEN pd.Quantity ELSE 0 END) as 'Trash Total',
			       SUM(CASE WHEN pd.LineNumber = 7 THEN pd.Quantity ELSE 0 END) as 'Assorted Grocery Total',
				   pt.FoodDriveTotal,
				   pt.FoodRescueTotal
				   FROM PickupDetail pd, 
						PickupHeader ph,
						(SELECT SUM(CASE WHEN ph.PickupType = 3 THEN ph.TotalAmount ELSE 0 END) as 'FoodDriveTotal', 
								SUM(CASE WHEN ph.PickupType = 2 THEN ph.TotalAmount ELSE 0 END) as 'FoodRescueTotal'
						 FROM PickupHeader ph
						 WHERE PickupDate BETWEEN '$exStartDate' AND '$exEndDate') as pt
				   WHERE ph.PickupId = pd.PickupId AND (PickupDate BETWEEN '$exStartDate' AND '$exEndDate')";	
							
$PostingDate = date('m/d/Y');

$csv_output .="Roadrunner Food Bank,,,,,,,Loction:,ABQ,,,,,,,,,,\n";
$csv_output .="Grocery Rescue Import,,,,,,,Posting Date:,$PostingDate,,,AutoRelease:,Y,,,,,,FBC Prod. Source/FBC Reason Code\n";
$csv_output .=",,,Description,Assorted Bakery,Assorted Dairy,Assorted Produce,Assorted Meat,Assorted Grocery,Misc. & Non-Foods,Trash,Food Drive,Food Rescue\n";
$csv_output .="Donor ID, Donor Name,Posting,Item #,FR599998,FR150003,FR350000,FR489900,FR000001,FR001000,FR000002,FD000001,FR000003\n";
$csv_output .=",,Date,Line Total,";

$sums = mysql_query($sumsSQL);

while ($row = mysql_fetch_row($sums)) {
	$csv_output .= nz0($row[0]).",";
	$csv_output .= nz0($row[1]).",";
	$csv_output .= ",";
	$csv_output .= nz0($row[3]).",";
	$csv_output .= nz0($row[6]).",";
	$csv_output .= nz0($row[4]).",";
	$csv_output .= nz0($row[5]).",";
	$csv_output .= nz0($row[7]).",";
	$csv_output .= nz0($row[8]).",";
	$csv_output .= "\n";
}

$values = mysql_query($valuesSQL);
						
while ($row = mysql_fetch_row($values)) {
	if(($row[7]-$row[10]>0)){
		if($row[6] == 1){
			$csv_output .= $row[4].",";
			$csv_output .= $row[3].",";
			$csv_output .= $row[5].",";
			$csv_output .= nz0(($row[7]-$row[10])).",";
			$csv_output .= nz0($row[8]).",";
			$csv_output .= nz0($row[9]).",";
			$csv_output .= ",";
			$csv_output .= nz0($row[11]).",";
			$csv_output .= nz0($row[14]).",";
			$csv_output .= nz0($row[12]).",";
			$csv_output .= nz0($row[13]).",";
			$csv_output .= nz0($row[15]).",";
			$csv_output .= nz0($row[16]).",";
			$csv_output .= "\n";
		} else {
			$csv_output .= $row[17].",";
			$csv_output .= $row[3].",";
			$csv_output .= $row[5].",";
			$csv_output .= nz0(($row[7]-$row[10])).",";
			$csv_output .= nz0($row[8]).",";
			$csv_output .= nz0($row[9]).",";
			$csv_output .= ",";
			$csv_output .= nz0($row[11]).",";
			$csv_output .= nz0($row[14]).",";
			$csv_output .= nz0($row[12]).",";
			$csv_output .= nz0($row[13]).",";
			$csv_output .= nz0($row[15]).",";
			$csv_output .= nz0($row[16]).",";
			$csv_output .= "\n";
		}
	}
}

$csv_output .="\n";
$csv_output .=",,,Description,Assorted Bakery,Assorted Dairy,Assorted Produce,Assorted Meat,Assorted Grocery,Misc. & Non-Foods,Trash,Food Drive,Food Rescue\n";
$csv_output .="Agency Number, Agency Name,Posting,Item #,FR599998,FR150003,FR350000,FR489900,FR000001,FR001000,FR000002,FD000001,FR000003\n";
$csv_output .=",,Date,Line Total,";

$sums = mysql_query($sumsSQL);

while ($row = mysql_fetch_row($sums)) {
	$csv_output .= nz0($row[0]).",";
	$csv_output .= nz0($row[1]).",";
	$csv_output .= ",";
	$csv_output .= nz0($row[3]).",";
	$csv_output .= nz0($row[6]).",";
	$csv_output .= nz0($row[4]).",";
	$csv_output .= nz0($row[5]).",";
	$csv_output .= nz0($row[7]).",";
	$csv_output .= nz0($row[8]).",";
	$csv_output .= "\n";
}

$values = mysql_query($valuesSQL);

while ($row = mysql_fetch_row($values)) {
	if(($row[7]-$row[10]>0)){
		$csv_output .= $row[2].",";
		$csv_output .= $row[1].",";
		$csv_output .= $row[5].",";
		$csv_output .= nz0(($row[7]-$row[10])).",";
		$csv_output .= nz0($row[8]).",";
		$csv_output .= nz0($row[9]).",";
		$csv_output .= ",";
		$csv_output .= nz0($row[11]).",";
		$csv_output .= nz0($row[14]).",";
		$csv_output .= nz0($row[12]).",";
		$csv_output .= nz0($row[13]).",";
		$csv_output .= nz0($row[15]).",";
		$csv_output .= nz0($row[16]).",";
		$csv_output .= "\n";
	}
}

ob_end_clean();
$file = 'CeresNonProduce_'.$exStartDate.'_'.$exEndDate;
header("Content-type: application/vnd.ms-excel");
header("Content-disposition: csv" . date("Y-m-d") . ".csv");
header("Content-disposition: filename=".$file.".csv");
print $csv_output;
exit;

} elseif ($_REQUEST['Type']=="CeresP") {
//Code to generate the Ceres Produce GSR Import
$valuesSQL = "SELECT ph.PickupId, p.ProgramName, p.ProgramNumber, d.DonorName, ph.DonorNumber, PickupDate, ph.PickupType, ph.TotalAmount AS 'Total', 
					   SUM(CASE WHEN pd.LineNumber = 1 THEN pd.Quantity ELSE 0 END) as 'Assorted Bakery',
					   SUM(CASE WHEN pd.LineNumber = 2 THEN pd.Quantity ELSE 0 END) as 'Assorted Dairy',
					   SUM(CASE WHEN pd.LineNumber = 3 THEN pd.Quantity ELSE 0 END) as 'Assorted Produce',
					   SUM(CASE WHEN pd.LineNumber = 4 THEN pd.Quantity ELSE 0 END) as 'Assorted Meat',
					   SUM(CASE WHEN pd.LineNumber = 5 THEN pd.Quantity ELSE 0 END) as 'Misc. & Non-Foods',
					   SUM(CASE WHEN pd.LineNumber = 6 THEN pd.Quantity ELSE 0 END) as 'Trash',
					   SUM(CASE WHEN pd.LineNumber = 7 THEN pd.Quantity ELSE 0 END) as 'Assorted Grocery',
					   CASE WHEN ph.PickupType = 3 THEN ph.TotalAmount ELSE 0 END as 'Food Drive',
					   CASE WHEN ph.PickupType = 2 THEN ph.TotalAmount ELSE 0 END as 'Food Rescue',
					   p.County
					   FROM PickupDetail pd, PickupHeader ph, Program p, Donor d
					   WHERE ph.PickupId = pd.PickupId AND ph.ProgramNumber = p.ProgramNumber AND ph.DonorNumber = d.DonorNumber AND (PickupDate BETWEEN '$exStartDate' AND '$exEndDate')
					   GROUP BY ph.PickupId
					   ORDER BY PickupDate ASC";
					   
$sumsSQL = "SELECT SUM(CASE WHEN LineNumber = 1 THEN pd.Quantity ELSE 0 END) as 'Assorted Bakery Total',
	   			   SUM(CASE WHEN pd.LineNumber = 2 THEN pd.Quantity ELSE 0 END) as 'Assorted Dairy Total',
				   SUM(CASE WHEN pd.LineNumber = 3 THEN pd.Quantity ELSE 0 END) as 'Assorted Produce Total',
				   SUM(CASE WHEN pd.LineNumber = 4 THEN pd.Quantity ELSE 0 END) as 'Assorted Meat Total',
				   SUM(CASE WHEN pd.LineNumber = 5 THEN pd.Quantity ELSE 0 END) as 'Misc. & Non-Foods Total',
				   SUM(CASE WHEN pd.LineNumber = 6 THEN pd.Quantity ELSE 0 END) as 'Trash Total',
			       SUM(CASE WHEN pd.LineNumber = 7 THEN pd.Quantity ELSE 0 END) as 'Assorted Grocery Total',
				   pt.FoodDriveTotal,
				   pt.FoodRescueTotal
				   FROM PickupDetail pd, 
						PickupHeader ph,
						(SELECT SUM(CASE WHEN ph.PickupType = 3 THEN ph.TotalAmount ELSE 0 END) as 'FoodDriveTotal', 
								SUM(CASE WHEN ph.PickupType = 2 THEN ph.TotalAmount ELSE 0 END) as 'FoodRescueTotal'
						 FROM PickupHeader ph
						 WHERE PickupDate BETWEEN '$exStartDate' AND '$exEndDate') as pt
				   WHERE ph.PickupId = pd.PickupId AND (PickupDate BETWEEN '$exStartDate' AND '$exEndDate')";	
							
$PostingDate = date('m/d/Y');

$csv_output .="Roadrunner Food Bank,,,,,,,Loction:,ABQ,,,,,,,,,,\n";
$csv_output .="Grocery Rescue Import,,,,,,,Posting Date:,$PostingDate,,,AutoRelease:,Y,,,,,,FBC Prod. Source/FBC Reason Code\n";
$csv_output .=",,,Description,Assorted Bakery,Assorted Dairy,Assorted Produce,Assorted Meat,Assorted Grocery,Misc. & Non-Foods,Trash,Food Drive,Food Rescue\n";
$csv_output .="Donor ID, Donor Name,Posting,Item #,FR599998,FR150003,FR350000,FR489900,FR000001,FR001000,FR000002,FD000001,FR000003\n";
$csv_output .=",,Date,Line Total,";

$sums = mysql_query($sumsSQL);

while ($row = mysql_fetch_row($sums)) {
	$csv_output .= ",";
	$csv_output .= ",";
	$csv_output .= $row[2].",";
	$csv_output .= ",";
	$csv_output .= ",";
	$csv_output .= ",";
	$csv_output .= ",";
	$csv_output .= ",";
	$csv_output .= ",";
	$csv_output .= "\n";
}

$values = mysql_query($valuesSQL);
						
while ($row = mysql_fetch_row($values)) {
	if($row[10]>0){
		if($row[6] == 1){
			$csv_output .= $row[4].",";
			$csv_output .= $row[3].",";
			$csv_output .= $row[5].",";
			$csv_output .= $row[10].",";
			$csv_output .= ",";
			$csv_output .= ",";
			$csv_output .= $row[10].",";
			$csv_output .= ",";
			$csv_output .= ",";
			$csv_output .= ",";
			$csv_output .= ",";
			$csv_output .= ",";
			$csv_output .= ",";
			$csv_output .= "\n";
		} else {
			$csv_output .= $row[17].",";
			$csv_output .= $row[3].",";
			$csv_output .= $row[5].",";
			$csv_output .= $row[10].",";
			$csv_output .= ",";
			$csv_output .= ",";
			$csv_output .= $row[10].",";
			$csv_output .= ",";
			$csv_output .= ",";
			$csv_output .= ",";
			$csv_output .= ",";
			$csv_output .= ",";
			$csv_output .= ",";
			$csv_output .= "\n";
		}
	}
}

$csv_output .="\n";
$csv_output .=",,,Description,Assorted Bakery,Assorted Dairy,Assorted Produce,Assorted Meat,Assorted Grocery,Misc. & Non-Foods,Trash,Food Drive,Food Rescue\n";
$csv_output .="Agency Number, Agency Name,Posting,Item #,FR599998,FR150003,FR350000,FR489900,FR000001,FR001000,FR000002,FD000001,FR000003\n";
$csv_output .=",,Date,Line Total,";

$sums = mysql_query($sumsSQL);

while ($row = mysql_fetch_row($sums)) {
	$csv_output .= ",";
	$csv_output .= ",";
	$csv_output .= $row[2].",";
	$csv_output .= ",";
	$csv_output .= ",";
	$csv_output .= ",";
	$csv_output .= ",";
	$csv_output .= ",";
	$csv_output .= ",";
	$csv_output .= "\n";
}

$values = mysql_query($valuesSQL);

while ($row = mysql_fetch_row($values)) {
	if($row[10]>0){
		$csv_output .= $row[2].",";
		$csv_output .= $row[1].",";
		$csv_output .= $row[5].",";
		$csv_output .= $row[10].",";
		$csv_output .= ",";
		$csv_output .= ",";
		$csv_output .= $row[10].",";
		$csv_output .= ",";
		$csv_output .= ",";
		$csv_output .= ",";
		$csv_output .= ",";
		$csv_output .= ",";
		$csv_output .= ",";
		$csv_output .= "\n";
	}
}

ob_end_clean();
$file = 'CeresNonProduce_'.$exStartDate.'_'.$exEndDate;
header("Content-type: application/vnd.ms-excel");
header("Content-disposition: csv" . date("Y-m-d") . ".csv");
header("Content-disposition: filename=".$file.".csv");
print $csv_output;
exit;
	
} elseif ($_REQUEST['Type']=="Agency") {

//Code to generate the Agency Report

if(!empty($_REQUEST['AgencyNo'])){
	$exAgencyNo = " AND ph.ProgramNumber = '".$_REQUEST['AgencyNo']."'";
} else {
	$exAgencyNo = "";
}

if(!empty($_REQUEST['DonorNo'])){
	$exDonorNo = " AND ph.DonorNumber = '".$_REQUEST['DonorNo']."'";
} else {
	$exDonorNo = "";
}

if(!empty($_REQUEST['PickupType'])){
	switch($_REQUEST['PickupType']){
		case "Grocery Store" :
			$exPickupType = " AND ph.PickupType = 1";
			break;
		case "Food Rescue" :
			$exPickupType = " AND ph.PickupType = 2";
			break;
		case "Food Drive" :
			$exPickupType = " AND ph.PickupType = 3";
			break;
		}
} else {
	$exPickupType = "";
}

if(!empty($_REQUEST['Agency'])){
	$AgencySQL = "SELECT ph.PickupId, p.ProgramName, p.ProgramNumber, d.DonorName, ph.DonorNumber, PickupDate, ph.PickupType, ph.TotalAmount AS 'Total', 
					   SUM(CASE WHEN pd.LineNumber = 1 THEN pd.Quantity ELSE 0 END) as 'Assorted Bakery',
					   SUM(CASE WHEN pd.LineNumber = 2 THEN pd.Quantity ELSE 0 END) as 'Assorted Dairy',
					   SUM(CASE WHEN pd.LineNumber = 3 THEN pd.Quantity ELSE 0 END) as 'Assorted Produce',
					   SUM(CASE WHEN pd.LineNumber = 4 THEN pd.Quantity ELSE 0 END) as 'Assorted Meat',
					   SUM(CASE WHEN pd.LineNumber = 5 THEN pd.Quantity ELSE 0 END) as 'Misc. & Non-Foods',
					   SUM(CASE WHEN pd.LineNumber = 6 THEN pd.Quantity ELSE 0 END) as 'Trash',
					   SUM(CASE WHEN pd.LineNumber = 7 THEN pd.Quantity ELSE 0 END) as 'Assorted Grocery'
					   FROM PickupDetail pd, PickupHeader ph, Program p, Donor d
					   WHERE ph.PickupId = pd.PickupId AND ph.ProgramNumber = p.ProgramNumber AND ph.DonorNumber = d.DonorNumber AND (PickupDate BETWEEN '$exStartDate' AND '$exEndDate') AND ph.ProgramNumber = '".$_REQUEST['Agency']."' $exDonorNo $exPickupType
					   GROUP BY ph.PickupId
					   ORDER BY PickupDate DESC";
} else {
	$AgencySQL = "SELECT ph.PickupId, p.ProgramName, p.ProgramNumber, d.DonorName, ph.DonorNumber, PickupDate, ph.PickupType, ph.TotalAmount AS 'Total', 
					   SUM(CASE WHEN pd.LineNumber = 1 THEN pd.Quantity ELSE 0 END) as 'Assorted Bakery',
					   SUM(CASE WHEN pd.LineNumber = 2 THEN pd.Quantity ELSE 0 END) as 'Assorted Dairy',
					   SUM(CASE WHEN pd.LineNumber = 3 THEN pd.Quantity ELSE 0 END) as 'Assorted Produce',
					   SUM(CASE WHEN pd.LineNumber = 4 THEN pd.Quantity ELSE 0 END) as 'Assorted Meat',
					   SUM(CASE WHEN pd.LineNumber = 5 THEN pd.Quantity ELSE 0 END) as 'Misc. & Non-Foods',
					   SUM(CASE WHEN pd.LineNumber = 6 THEN pd.Quantity ELSE 0 END) as 'Trash',
					   SUM(CASE WHEN pd.LineNumber = 7 THEN pd.Quantity ELSE 0 END) as 'Assorted Grocery'
					   FROM PickupDetail pd, PickupHeader ph, Program p, Donor d
					   WHERE ph.PickupId = pd.PickupId AND ph.ProgramNumber = p.ProgramNumber AND ph.DonorNumber = d.DonorNumber AND (PickupDate BETWEEN '$exStartDate' AND '$exEndDate') $exAgencyNo $exDonorNo $exPickupType
					   GROUP BY ph.PickupId
					   ORDER BY PickupDate DESC";
}

$result = mysql_query($AgencySQL);

$csv_output .="Type,Agency No,Agency Name,Donor No,Donor Name,Pickup Date,Assorted Bakery,Assorted Dairy,Assorted Produce,Assorted Meat,Assorted Grocery,Misc. & Non-Foods,Trash,Total\n";

while ($row = mysql_fetch_row($result)){
	switch ($row[6]) {
		case 1 :
			$csv_row_type = "Grocery Store";
			break;
		case 2 :
			$csv_row_type = "Food Rescue";
			break;
		case 3 :
			$csv_row_type = "Food Drive";
			break;
	}
	$csv_output .= $csv_row_type.",";
	$csv_output .= $row[2].",";
	$csv_output .= $row[1].",";
	$csv_output .= $row[4].",";
	$csv_output .= $row[3].",";
	$csv_output .= $row[5].",";
	$csv_output .= $row[8].",";
	$csv_output .= $row[9].",";
	$csv_output .= $row[10].",";
	$csv_output .= $row[11].",";
	$csv_output .= $row[14].",";
	$csv_output .= $row[12].",";
	$csv_output .= $row[13].",";
	$csv_output .= $row[7].",";
	$csv_output .= "\n";	
}
	
$file = 'Agency_'.$exStartDate.'_'.$exEndDate; 
ob_end_clean();
header("Content-type: application/vnd.ms-excel");
header("Content-disposition: csv" . date("Y-m-d") . ".csv");
header("Content-disposition: filename=".$file.".csv");
print $csv_output;
exit;
}
?>