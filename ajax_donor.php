<?php
include 'dbc.php';

//get the donor list for the select agency
if($_POST['ProgramNumber']) {
	$ProgramNumber=$data['ProgramNumber'];
	$sql=mysql_query("SELECT DonorNumber, DonorName, Address1 FROM Donor WHERE ProgramNumber = '$ProgramNumber'") or die(mysql_error());

	while($row=mysql_fetch_array($sql)) {
		$DonorNumber=$row['DonorNumber'];
		$DonorName=$row['DonorName'];
		$Address1=$row['Address1'];
		echo '<option value="'.$DonorNumber.'">'.$DonorNumber.' - '.$DonorName.' - '.$Address1.'</option>';
	}
}
?>
