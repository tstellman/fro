<?php 
include 'dbc.php';
include 'grid.php';
page_protect();

//Include the header
include('templates/header.php');
?>

	<!--Main body content-->
	<div id="main" role="main" class="main">
	<?php loadGrid($_REQUEST['t'],$_REQUEST['sd'],$_REQUEST['ed']); ?>
	</div>
	<!--end of #main-->

<!--Include the footer-->
<?php
include('templates/footer.html');
?>
	
</div> <!--end of #container-->

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
	
	<!-- calendar script -->
	<script type="text/javaScript">
	// temp vars used below; show current month up to current day, unless first 2 days of month then allow selection of previous month back 3 days
	var currentTime = new Date() 
	if (currentTime.getDate()<=0) {
		var minDate = new Date(currentTime.getFullYear() -1, currentTime.getMonth() -0, -2);
	} else {
		var minDate = new Date(currentTime.getFullYear() -1, currentTime.getMonth());
	}
	$( ".pickDate" ).datepicker({
	dateFormat: 'yy-mm-dd',
	minDate: minDate, 
	maxDate: '+0D' 
	});
	</script>
	
	<!--[if lt IE 7 ]>
	<script src="js/libs/dd_belatedpng.js"></script>
	<script>DD_belatedPNG.fix("img, .png_bg"); // Fix any <img> or .png_bg bg-images. Also, please read goo.gl/mZiyb </script>
	<![endif]-->

</body>
</html>
