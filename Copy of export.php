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
//Code to generate the Ceres GSR Import
if($_REQUEST['Type']=='Ceres'){
$valuesa = mysql_query("SELECT ph.PickupId, p.ProgramName, p.ProgramNumber, d.DonorName, ph.DonorNumber, PickupDate, ph.PickupType, ph.TotalAmount AS 'Total', 
					   SUM(CASE WHEN pd.LineNumber = 1 THEN pd.Quantity ELSE 0 END) as 'Assorted Bakery',
					   SUM(CASE WHEN pd.LineNumber = 2 THEN pd.Quantity ELSE 0 END) as 'Assorted Dairy',
					   SUM(CASE WHEN pd.LineNumber = 3 THEN pd.Quantity ELSE 0 END) as 'Assorted Produce',
					   SUM(CASE WHEN pd.LineNumber = 4 THEN pd.Quantity ELSE 0 END) as 'Assorted Meat',
					   SUM(CASE WHEN pd.LineNumber = 5 THEN pd.Quantity ELSE 0 END) as 'Misc. & Non-Foods',
					   SUM(CASE WHEN pd.LineNumber = 6 THEN pd.Quantity ELSE 0 END) as 'Trash',
					   SUM(CASE WHEN pd.LineNumber = 7 THEN pd.Quantity ELSE 0 END) as 'Assorted Grocery',
					   CASE WHEN ph.PickupType = 3 THEN ph.TotalAmount ELSE 0 END as 'Food Drive',
					   CASE WHEN ph.PickupType = 2 THEN ph.TotalAmount ELSE 0 END as 'Food Rescue'
					   FROM PickupDetail pd, PickupHeader ph, Program p, Donor d
					   WHERE ph.PickupId = pd.PickupId AND ph.ProgramNumber = p.ProgramNumber AND ph.DonorNumber = d.DonorNumber AND (PickupDate BETWEEN '$exStartDate' AND '$exEndDate')
					   GROUP BY ph.PickupId");
					   
$valuesd = mysql_query("SELECT ph.PickupId, p.ProgramName, p.ProgramNumber, d.DonorName, ph.DonorNumber, PickupDate, ph.PickupType, ph.TotalAmount AS 'Total', 
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
					   GROUP BY ph.PickupId");
					   
$sumsd = mysql_query("SELECT SUM(CASE WHEN LineNumber = 1 THEN pd.Quantity ELSE 0 END) as 'Assorted Bakery Total',
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
							WHERE ph.PickupId = pd.PickupId AND (PickupDate BETWEEN '$exStartDate' AND '$exEndDate')");	
							
$sumsa = mysql_query("SELECT SUM(CASE WHEN LineNumber = 1 THEN pd.Quantity ELSE 0 END) as 'Assorted Bakery Total',
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
							WHERE ph.PickupId = pd.PickupId AND (PickupDate BETWEEN '$exStartDate' AND '$exEndDate')");								   
					   
$PostingDate = date('m/d/Y');
$file = 'Ceres_'.$exStartDate.'_'.$exEndDate;
$csv_output .="Roadrunner Food Bank,,,,,,,Loction:,ABQ,,,,,,,,,,\n";
$csv_output .="Grocery Rescue Import,,,,,,,Posting Date:,$PostingDate,,,AutoRelease:,Y,,,,,,FBC Prod. Source/FBC Reason Code\n";
$csv_output .=",,,Description,Assorted Bakery,Assorted Dairy,Assorted Produce,Assorted Meat,Assorted Grocery,Misc. & Non-Foods,Trash,Food Drive,Food Rescue\n";
$csv_output .="Donor ID, Donor Name,Posting,Item #,FR599998,FR150003,FR350000,FR489900,FR000001,FR001000,FR000002,FD000001,FR000003\n";
$csv_output .=",,Date,Line Total,";

while ($rowsd = mysql_fetch_row($sumsd)) {
	$csv_output .= $rowsd[0].",";
	$csv_output .= $rowsd[1].",";
	$csv_output .= $rowsd[2].",";
	$csv_output .= $rowsd[3].",";
	$csv_output .= $rowsd[6].",";
	$csv_output .= $rowsd[4].",";
	$csv_output .= $rowsd[5].",";
	$csv_output .= $rowsd[7].",";
	$csv_output .= $rowsd[8].",";
	$csv_output .= "\n";
}
						
while ($rowd = mysql_fetch_row($valuesd)) {
	if($rowd[6] == 1){
		$csv_output .= $rowd[4].",";
		$csv_output .= $rowd[3].",";
		$csv_output .= $rowd[5].",";
		$csv_output .= $rowd[7].",";
		$csv_output .= $rowd[8].",";
		$csv_output .= $rowd[9].",";
		$csv_output .= $rowd[10].",";
		$csv_output .= $rowd[11].",";
		$csv_output .= $rowd[14].",";
		$csv_output .= $rowd[12].",";
		$csv_output .= $rowd[13].",";
		$csv_output .= $rowd[15].",";
		$csv_output .= $rowd[16].",";
		$csv_output .= "\n";
	} else {
		$csv_output .= $rowd[17].",";
		$csv_output .= $rowd[3].",";
		$csv_output .= $rowd[5].",";
		$csv_output .= $rowd[7].",";
		$csv_output .= $rowd[8].",";
		$csv_output .= $rowd[9].",";
		$csv_output .= $rowd[10].",";
		$csv_output .= $rowd[11].",";
		$csv_output .= $rowd[14].",";
		$csv_output .= $rowd[12].",";
		$csv_output .= $rowd[13].",";
		$csv_output .= $rowd[15].",";
		$csv_output .= $rowd[16].",";
		$csv_output .= "\n";
	}
}

$csv_output .=",,,Description,Assorted Bakery,Assorted Dairy,Assorted Produce,Assorted Meat,Assorted Grocery,Misc. & Non-Foods,Trash,Food Drive,Food Rescue\n";
$csv_output .="Agency Number, Agency Name,Posting,Item #,FR599998,FR150003,FR350000,FR489900,FR000001,FR001000,FR000002,FD000001,FR000003\n";
$csv_output .=",,Date,Line Total,";

while ($rowsa = mysql_fetch_row($sumsa)) {
	$csv_output .= $rowsa[0].",";
	$csv_output .= $rowsa[1].",";
	$csv_output .= $rowsa[2].",";
	$csv_output .= $rowsa[3].",";
	$csv_output .= $rowsa[6].",";
	$csv_output .= $rowsa[4].",";
	$csv_output .= $rowsa[5].",";
	$csv_output .= $rowsa[7].",";
	$csv_output .= $rowsa[8].",";
	$csv_output .= "\n";
}

while ($rowa = mysql_fetch_row($valuesa)) {
		$csv_output .= $rowa[2].",";
		$csv_output .= $rowa[1].",";
		$csv_output .= $rowa[5].",";
		$csv_output .= $rowa[7].",";
		$csv_output .= $rowa[8].",";
		$csv_output .= $rowa[9].",";
		$csv_output .= $rowa[10].",";
		$csv_output .= $rowa[11].",";
		$csv_output .= $rowa[14].",";
		$csv_output .= $rowa[12].",";
		$csv_output .= $rowa[13].",";
		$csv_output .= $rowa[15].",";
		$csv_output .= $rowa[16].",";
		$csv_output .= "\n";
}

$filename = $file."_".date("Y-m-d_H-i",time());
ob_end_clean();
header("Content-type: application/vnd.ms-excel");
header("Content-disposition: csv" . date("Y-m-d") . ".csv");
header("Content-disposition: filename=".$file.".csv");
print $csv_output;
exit;
	
} elseif ($_REQUEST['Type']=="Agency") {

//Code to generate the Agency Report
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
					   WHERE ph.PickupId = pd.PickupId AND ph.ProgramNumber = p.ProgramNumber AND ph.DonorNumber = d.DonorNumber AND (PickupDate BETWEEN '$exStartDate' AND '$exEndDate') AND ph.ProgramNumber = '".$_REQUEST['Agency']."'
					   GROUP BY ph.PickupId";
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
					   WHERE ph.PickupId = pd.PickupId AND ph.ProgramNumber = p.ProgramNumber AND ph.DonorNumber = d.DonorNumber AND (PickupDate BETWEEN '$exStartDate' AND '$exEndDate')
					   GROUP BY ph.PickupId";
}

$result = mysql_query($AgencySQL);

$csv_output .="Type,Agency No,Agency Name,Donor No,Donor Name,Pickup Date,Assorted Bakery,Assorted Dairy,Assorted Produce,Assorted Meat,Assorted Grocery,Misc. & Non-Foods,Trash,Total\n";

while ($rows = mysql_fetch_row($result)){
	$csv_output .= $rows[6].",";
	$csv_output .= $rows[2].",";
	$csv_output .= $rows[1].",";
	$csv_output .= $rows[4].",";
	$csv_output .= $rows[3].",";
	$csv_output .= $rows[5].",";
	$csv_output .= $rows[8].",";
	$csv_output .= $rows[9].",";
	$csv_output .= $rows[10].",";
	$csv_output .= $rows[11].",";
	$csv_output .= $rows[14].",";
	$csv_output .= $rows[12].",";
	$csv_output .= $rows[13].",";
	$csv_output .= $rows[7].",";
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