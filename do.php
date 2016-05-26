<?php 
include 'dbc.php';
session_start();

if(!checkAdmin()) {
header("Location: login.php");
exit();
}

$ret = $_SERVER['HTTP_REFERER'];

foreach($_GET as $key => $value) {
	$get[$key] = filter($value);
}

//edit user
if($get['cmd'] == 'edit') {
	mysql_query("UPDATE User SET `Username` = '$get[Username]', `Email` = '$get[Email]' WHERE `UserId` = '$get[UserId]'") or die(mysql_error());
	//header("Location: $ret"); 

	if(!empty($get['pass'])) {
		$hash = PwdHash($get['pass']);
		mysql_query("UPDATE User SET `Password` = '$hash' WHERE `UserId` = '$get[UserId]'") or die(mysql_error());
	}

echo "Changes Done!";
exit();
}

//edit program
if($get['cmd'] == 'editprogram') {
	mysql_query("UPDATE Program, User u SET `ProgramName`  = '$get[ProgramName]', `County` = '$get[ProgramCounty]', `FirstName`  = '$get[FirstName]', `LastName`  = '$get[LastName]', `Email`  = '$get[Email]' WHERE ProgramNumber = '$get[ProgramNumber]' AND u.Username = ProgramNumber") or die(mysql_error());
	//header("Location: $ret");

	if(!empty($get['Password'])) {
		$hash = PwdHash($get['Password']);
		mysql_query("UPDATE User SET `Password` = '$hash' WHERE `Username` = '$get[ProgramNumber]'") or die(mysql_error());
	}

echo "Changes Done!";
exit();
}

//edit donor
if($get['cmd'] == 'editdonor') {
	mysql_query("UPDATE Donor SET `DonorName`  = '$get[DonorName]', `Address1`  = '$get[Address1]', `Address2`  = '$get[Address2]', `City`  = '$get[City]', `State`  = '$get[State]', `ZipCode`  = '$get[ZipCode]', `GroceryStore`  = '$get[GroceryStore]', `FoodRescue`  = '$get[FoodRescue]', `FoodDrive`  = '$get[FoodDrive]', `ProgramNumber` = '$get[ProgramNumber]' WHERE DonorNumber = '$get[DonorNumber]'") or die("Donor could not be updated. No such program exists. Make sure you've entered a valid program number and try again.");
	//header("Location: $ret");

echo "Changes Done!";
exit();
}

//edit order
if($get['cmd'] == 'editorder') {
	mysql_query("UPDATE PickupDetail SET `Quantity` = '$get[BreadQuantity]' WHERE PickupId = '$get[PickupId]' AND LineNumber = '1'") or die(mysql_error());

	mysql_query("UPDATE PickupDetail SET `Quantity` = '$get[AssortedRefrigeratedProductQuantity]' WHERE PickupId = '$get[PickupId]' AND LineNumber = '2'") or die(mysql_error());

	mysql_query("UPDATE PickupDetail SET `Quantity` = '$get[ProduceQuantity]' WHERE PickupId = '$get[PickupId]' AND LineNumber = '3'") or die(mysql_error());

	mysql_query("UPDATE PickupDetail SET `Quantity` = '$get[AssortedMixedDryQuantity]' WHERE PickupId = '$get[PickupId]' AND LineNumber = '4'") or die(mysql_error());

	mysql_query("UPDATE PickupDetail SET `Quantity` = '$get[AssortedFrozenQuantity]' WHERE PickupId = '$get[PickupId]' AND LineNumber = '5'") or die(mysql_error());

	$TotalAmount = $get[BreadQuantity] + $get[AssortedRefrigeratedProductQuantity] + $get[ProduceQuantity] + $get[AssortedMixedDryQuantity] + $get[AssortedFrozenQuantity];

	mysql_query("UPDATE PickupHeader oh SET `PickupDate` = '$get[PickupDate]', `DonorNumber` = (SELECT DonorNumber FROM Donor WHERE `DonorNumber` = '$get[DonorNumber]' AND `ProgramNumber` = '$get[ProgramNumber]'), `TotalAmount` = '$TotalAmount' WHERE PickupId = '$get[PickupId]'") or die("Order could not be updated. Make sure you are entering the correct donor number and try again.") or die(mysql_error());
	//header("Location: $ret");

echo "Changes Done!";
exit();
}
?>
