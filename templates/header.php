<!doctype html>
<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if lt IE 7 ]> <html class="no-js ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]>    <html class="no-js ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]>    <html class="no-js ie8" lang="en"> <![endif]-->
<head>
  <meta charset="utf-8">

  <!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame
       Remove this if you use the .htaccess -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

  <title>Roadrunner Food Bank's Food Rescue Online</title>
  <meta name="description" content="Roadrunner Food Bank">
  <meta name="author" content="Roadrunner Food Bank">

  <!-- Mobile viewport optimized: j.mp/bplateviewport -->
  <meta name="viewport" content="width=900" />

  <!-- Icons for Browser Address Bar -->
   <!--<link rel="shortcut icon" href="img/favicon.ico" >-->
   <!--<link rel="icon" type="image/gif" href="img/animated_favicon1.gif" >-->

  <!-- CSS: implied media="all" -->
  <link rel="stylesheet" type="text/css" href="css/style.css?v=2" >
  <link rel="stylesheet" type="text/css" href="css/jquery-ui-1.8.16.custom.css" >
    
  <!--[if IE 7]>
  <link rel="stylesheet" href="css/ie.css">
  <link rel="stylesheet" href="css/ie7.css">
  <![endif]-->

  <noscript>
  <!--[if IE]>
  <link rel="stylesheet" href="css/ie.css">
  <![endif]-->
  </noscript>

  <!-- Uncomment if you are specifically targeting less enabled mobile browsers
  <link rel="stylesheet" media="handheld" href="css/handheld.css?v=2">  -->

  <!-- All JavaScript at the bottom, except for Modernizr which enables HTML5 elements & feature detects -->
  <script src="js/libs/modernizr-1.7.min.js"></script>
  
  <!-- BEGIN Navigation bar CSS - This is where the magic happens -->
  <link rel="stylesheet" href="css/nav.css">
  <!-- END Navigation bar CSS -->
 
<style> 
  div.ui-datepicker{
	font-size:12px;
	}
</style>
</head>

<body>	
<div id="container" class="container">
	<div class="printheader"><img src="img/logotrans.png"></div>
    <img id="harvLogo" src="img/GSRLogo.png" class="noprint" alt="Roadrunner Food Bank's Grocery Store Recovery System">
	<header>
	<!-- BEGIN Dark navigation bar -->
		<nav id="topNav" class="noprint">
            <ul>
				<li><a href="myaccount.php"><font color="#FFFFFF">Home</font></a></li>
				<li><a href="#"><font color="#FFFFFF">Create Pickup</font></a>
					<ul>
						<li><a href="gspickup.php"><font color="#FFFFFF">Grocery Store</font></a></li>
						<li><a href="frpickup.php"><font color="#FFFFFF">Food Rescue</font></a></li>
						<li><a href="fdpickup.php"><font color="#FFFFFF">Food Drive</font></a></li>
					</ul>
				</li>
				<li><a href="programreport.php"><font color="#FFFFFF">Reporting</font></a></li>
				<?php if (isset($_SESSION['UserId'])) {?><?php }if (checkAdmin()) {?>
				<li><a href="#"><font color="#FFFFFF">Administration</font></a>
					<ul>
					<li><a href="createprogram.php"><font color="#FFFFFF">Create an Agency</font></a></li>
					<li><a href="modifyprogram.php"><font color="#FFFFFF">Modify an Agency</font></a></li>
					<li><a href="createdonor.php"><font color="#FFFFFF">Create a Donor</font></a></li>
					<li><a href="modifydonor.php"><font color="#FFFFFF">Modify a Donor</font></a></li>
					<li><a href="createuser.php"><font color="#FFFFFF">Create an Admin</font></a></li>
					<li class="last"><a href="modifyuser.php"><font color="#FFFFFF">Modify an Admin</font></a></li>
					<li class="last"><a href="programdonorsearch.php"><font color="#FFFFFF">Agency/Donor Search</font></a></li>
					<?php
						$startDate = date ('Y-m-d', strtotime('first day of last month'));
						$endDate = date ('Y-m-d', strtotime('last day of last month'));
						$locationNP = "export.php?Type=CeresNP&StartDate=$startDate&EndDate=$endDate";
						$locationP = "export.php?Type=CeresP&StartDate=$startDate&EndDate=$endDate";
					?>
					<li class="last" onclick="location.href='<?php echo $locationNP ?>';"><a href="#"><font color="#FFFFFF">Ceres Export - Non Produce</font></a></li>
					<li class="last" onclick="location.href='<?php echo $locationP ?>';"><a href="#"><font color="#FFFFFF">Ceres Export - Produce</font></a></li>
					</ul>
				</li>
				<?php } ?>
				<!--<li><a href="#"><font color="#FFFFFF">Pickups</font></a>
					<ul>
					<li><a href="createpickups.php"><font color="#FFFFFF">Create</font></a></li>
					<li><a href="modifypickups.php"><font color="#FFFFFF">Modify</font></a></li>
					<li class="last"><a href="searchpickups.php"><font color="#FFFFFF">Search</font></a></li>
					</ul>
				</li>-->
				<li><a href="mysettings.php"><font color="#FFFFFF">My Account</font></a></li>
				<?php if (isset($_SESSION['UserId'])) {?><?php }if (checkProgram()) {?>
				<li><a href="#"><font color="#FFFFFF">Contact Us</font></a>
					<ul>
					<li><a href="contacttechsupport.php"><font color="#FFFFFF">Tech Support</font></a></li>
					<li class="last"><a href="contactprogsupport.php"><font color="#FFFFFF">Agency Support</font></a></li>
					</ul>
				</li>
			<?php } ?>
			<li class="last"><a href="logout.php"><font color="#FFFFFF">Logout</font></a></li>
          </ul>
        </nav>
		<!-- END Dark navigation bar -->
	</header><!-- END Header -->
