<?php 
include 'dbc.php';
page_protect();

if(!checkAdmin()) {
	header("Location: login.php");
	exit();
}

if ($_POST['ProgramDonorSearch'] == 'Search') {
	
	$ProgramNumber = $data['ProgramNumber'];
	$cond = ""; // initialize the variable and set it blank
			
	if ($_POST['ProgramNumber']) {
		$cond = "AND p.ProgramNumber = '$ProgramNumber'";
	}
	
	$programdonorquery = mysql_query("SELECT p.ProgramNumber, ProgramName, DonorNumber, DonorName FROM Program p, Donor d WHERE p.ProgramNumber = d.ProgramNumber $cond") or die(mysql_error());
}

//Include the header
include('templates/header.php');
?>

<!--Main body content-->
<div id="main" role="main" class="main">
	<h3 class="titlehdr">Search Agencies and Donors</h3>
	<form name="SearchForm" action="programdonorsearch.php" method="post" class="SearchForm noprint">
		
		<div class="cf">
		<div class="searchlabels"><label for="Program" class="bolded">Agency:&nbsp;&nbsp;</label></div>
		<div class="rightcolumn">
		<?php
			$result = mysql_query("SELECT ProgramNumber, CONCAT(ProgramNumber,' - ',ProgramName) AS Program FROM Program ORDER BY ProgramNumber ASC") or die(mysql_error());			
			
			echo '<select name="ProgramNumber"><option value="">-- Select an Agency --</option>';
			while($row = mysql_fetch_array($result))
			{
			echo '<option value="'.$row['ProgramNumber'].'">'. stripslashes($row['Program']). '</option>';
			}
			echo '</select><br/>';
		?>
		</div>
		</div>
		
		<input name="ProgramDonorSearch" type="submit" id="ProgramDonorSearch" value="Search" class="awesomed">
	</form>
	
		<?php
		echo "<table id='box-table-a'>
		<thead>
		<tr>
		<th scope='col'>Agency No.</th>
		<th scope='col'>Ageny Name</th>
		<th scope='col'>Donor No.</th>
		<th scope='col'>Donor Name</th>
		</tr>
		</thead>";

		while($row = mysql_fetch_array($programdonorquery)) {
		echo "<tbody><tr align='center'>";
		echo "<td>" . $row['ProgramNumber'] . "</td>";
		echo "<td>" . $row['ProgramName'] . "</td>";
		echo "<td>" . $row['DonorNumber'] . "</td>";
		echo "<td>" . $row['DonorName'] . "</td>";
		echo "</tr></tbody>";
		}
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
	<!-- end scripts-->

	<!--[if lt IE 7 ]>
	<script src="js/libs/dd_belatedpng.js"></script>
	<script>DD_belatedPNG.fix("img, .png_bg"); // Fix any <img> or .png_bg bg-images. Also, please read goo.gl/mZiyb </script>
	<![endif]-->

</body>
</html>
