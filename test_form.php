<!DOCTYPE HTML>
<html>
<body>
<?php

$text1 = "";
$t1Err = "";
$text2 = "";
$t2Err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   if (empty($_POST["text1"])) {
     $t1Err = "Text1 is required";
   } else {
     $text1 = $_POST["text1"];
   }
}

?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
	Text 1: <input type="text" name="text1" value="<?php echo $text1; ?>" /><?php echo $t1Err; ?><br />
	Text 2: <input type="text" name="text2" value="<?php echo $text2; ?>" /><?php echo $t2Err; ?><br />
	<input type="submit" name="submit" value="submit" />
</form>
<?php
echo "<h2>PHP Output</h2>";
echo $text1;
echo $text2;
?>
</body>
</html>


