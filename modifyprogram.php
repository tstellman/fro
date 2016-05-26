<?php 
include 'dbc.php';
page_protect();

if(!checkAdmin()) {
	header("Location: login.php");
	exit();
}

$page_limit = 100; 

if($_POST['doBan'] == 'Inactive') {

if(!empty($_POST['u'])) {
	foreach ($_POST['u'] as $uid) {
		$ProgramNumber = filter($uid);
		mysql_query("UPDATE User SET banned = '1' WHERE Username = '$ProgramNumber'");
	}
}

$ret = $_SERVER['PHP_SELF'] . '?'.$_POST['query_str'];;
 
header("Location: $ret");
exit();
}

if($_POST['doUnban'] == 'Active') {

if(!empty($_POST['u'])) {
	foreach ($_POST['u'] as $uid) {
		$ProgramNumber = filter($uid);
		mysql_query("UPDATE User SET banned = '0' WHERE Username = '$ProgramNumber'");
	}
}

$ret = $_SERVER['PHP_SELF'] . '?'.$_POST['query_str'];;
 
header("Location: $ret");
exit();
}

$rs_all = mysql_query("SELECT COUNT(*) AS total_all FROM Program") or die(mysql_error());
list($all) = mysql_fetch_row($rs_all);

//Include the header
include('templates/header.php');
?>

	<div id="main" role="main" class="main">
		<h3 class="titlehdr">Modify Existing Agencies</h3>
          
		<div id = "usercount">
		<strong>Total Agencies: </strong><?php echo $all;?>
		</div>
	  
		<?php 
		if(!empty($msg)) {
			echo $msg[0];
		}
		?>
          
		<form name="form1" method="get" action="modifyprogram.php">
            <label for="doSearch" class="bolded">Agency Number: </label>
			<input name="q" type="text" id="q" size="20">&nbsp;<input name="doSearch" type="submit" id="doSearch2" value="Search" class="awesomed">
        </form>
	  
        <?php {
		$cond = '';
	  
		if($get['q'] == '') {
			$sql = "SELECT * FROM Program, User u WHERE ProgramNumber = u.Username";
		}
		else {
			$sql = "SELECT *, (SELECT banned FROM User u WHERE u.Username = '$get[q]') AS banned FROM Program, User us WHERE ProgramNumber = '$get[q]' AND us.Username = ProgramNumber";
		}
	  
		$rs_total = mysql_query($sql) or die(mysql_error());
		$total = mysql_num_rows($rs_total);
	  
		if (!isset($_GET['page']) )
			{ $start=0; } else
			{ $start = ($_GET['page'] - 1) * $page_limit; }
	  
		$rs_results = mysql_query($sql . " limit $start,$page_limit") or die(mysql_error());
		$total_pages = ceil($total/$page_limit);
	  
		?><br/>

		<p align="right">
        <?php
		// outputting the pages
		if ($total > $page_limit)
		{
		echo "<div><strong>Pages:</strong> ";
		$i = 0;
		while ($i < $page_limit)
		{
		$page_no = $i+1;
		$qstr = ereg_replace("&page=[0-9]+","",$_SERVER['QUERY_STRING']);
		echo "<a href=\"modifyprogram.php?$qstr&page=$page_no\">$page_no</a> ";
		$i++;
		}
		echo "</div>";
		}  
		?>
		</p>
		
		<form name="searchform" action="modifyprogram.php" method="post">
        <table align="center" id="modifyprogramtable">
          <tr bgcolor="#FF4C50"> 
            <td width="5%"><div align="center"><strong><font color="white">&#9745;</font></strong></div></td>
            <td width="20%"><div align="center"><strong><font color="white">Agency No.</font></strong></div></td>
            <td width="45%"><strong><font color="white">Agency Name</strong></font></td>
            <td width="20%"><div align="center"><strong><font color="white">Status</strong></div></font></td>
            <td width="10%"><div align="center">&nbsp;</div></td>
          </tr>
          <tr> 
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <?php while ($rrows = mysql_fetch_array($rs_results)) {?>
          <tr>
            <td><div align="center"><input name="u[]" type="checkbox" value="<?php echo $rrows['ProgramNumber']; ?>" id="u[]"></div></td>
            <td><div align="left"><?php echo $rrows['ProgramNumber'];?></div></td>
            <td><?php echo $rrows['ProgramName']; ?></td>
            <td><div align="center"><span id="ban<?php echo $rrows['UserId']; ?>"><?php if($rrows['banned'] == '0') { echo "Active"; } else {echo "Inactive"; }?></div></span></td>
            <td><div align="center"><font size="2"><a href="javascript:void(0);" onclick='$("#editprogram<?php echo $rrows['ProgramNumber'];?>").show("slow");'>Edit</a></font></div></td>
          </tr>
          <tr>
            <td colspan="5">
			
			<div style="display:none;font: normal 12px arial; padding:5px 5px 10px 10px; background: #FF4C50" id="editprogram<?php echo $rrows['ProgramNumber']; ?>">
			<input type="hidden" name="id<?php echo $rrows['ProgramNumber']; ?>" id="id<?php echo $rrows['ProgramNumber']; ?>" value="<?php echo $rrows['ProgramNumber']; ?>">
			
			<div id="usercount"><a href="javascript:void(0);" onclick='$("#editprogram<?php echo $rrows['ProgramNumber'];?>").hide();'><img src="img/close-button.png"></a></div>
			
			<div class="cf">
			<div class="modifylabels150">Agency Name:</div>
			<div class="rightcolumn"><input name="ProgramName<?php echo $rrows['ProgramNumber']; ?>" id="ProgramName<?php echo $rrows['ProgramNumber']; ?>" type="text" size="27" value="<?php echo $rrows['ProgramName']; ?>" ></div>
			</div>
			
			<div class="cf">
			<div class="modifylabels150">Agency Number:</div>
			<div class="rightcolumn"><input name="ProgramNumber<?php echo $rrows['ProgramNumber']; ?>" id="ProgramNumber<?php echo $rrows['ProgramNumber']; ?>" type="text" size="5" readonly value="<?php echo $rrows['ProgramNumber']; ?>" ></div>
			</div>
			
			<div class="cf">
			<div class="modifylabels150">Agency County:</div>
			<div class="rightcolumn"><input name="ProgramCounty<?php echo $rrows['ProgramNumber']; ?>" id="ProgramCounty<?php echo $rrows['ProgramNumber']; ?>" type="text" size="5" value="<?php echo $rrows['County']; ?>" ></div>
			</div>
			
			<HR class="white">
			
			<div class="cf">
			<div class="modifylabels150">First Name:</div>
			<div class="rightcolumn"><input name="FirstName<?php echo $rrows['ProgramNumber']; ?>" id="FirstName<?php echo $rrows['ProgramNumber']; ?>" type="text" size="23" value="<?php echo $rrows['FirstName']; ?>" ></div>
			</div>
			
			<div class="cf">
			<div class="modifylabels150">Last Name:</div>
			<div class="rightcolumn"><input name="LastName<?php echo $rrows['ProgramNumber']; ?>" id="LastName<?php echo $rrows['ProgramNumber']; ?>" type="text" size="23" value="<?php echo $rrows['LastName']; ?>" ></div>
			</div>
			
			<div class="cf">
			<div class="modifylabels150">Email:</div>
			<div class="rightcolumn"><input name="Email<?php echo $rrows['ProgramNumber']; ?>" id="Email<?php echo $rrows['ProgramNumber']; ?>" type="text" size="23" value="<?php echo $rrows['Email']; ?>" >			</div>
			</div>
			
			<div class="cf">
			<div class="modifylabels150">Reset Password:</div>
			<div class="rightcolumn"><input name="Password<?php echo $rrows['ProgramNumber']; ?>" id="Password<?php echo $rrows['ProgramNumber']; ?>" type="text" size="23" value="" ></div>
			</div>
			
			<p align="center"><input name="doSave" type="button" id="doSave" value="Save" class="awesomed" onclick='$.get("do.php",{ cmd: "editprogram",ProgramName:$("input#ProgramName<?php echo $rrows['ProgramNumber']; ?>").val(),ProgramNumber:$("input#ProgramNumber<?php echo $rrows['ProgramNumber']; ?>").val(),ProgramCounty:$("input#ProgramCounty<?php echo $rrows['ProgramNumber']; ?>").val(),FirstName:$("input#FirstName<?php echo $rrows['ProgramNumber']; ?>").val(),LastName:$("input#LastName<?php echo $rrows['ProgramNumber']; ?>").val(),Email:$("input#Email<?php echo $rrows['ProgramNumber']; ?>").val(),Password:$("input#Password<?php echo $rrows['ProgramNumber']; ?>").val() } ,function(data){ $("#msg<?php echo $rrows['ProgramNumber']; ?>").html(data); });'><br /><div style="color:white" id="msg<?php echo $rrows['ProgramNumber']; ?>" name="msg<?php echo $rrows['ProgramNumber']; ?>"></div></p>
		  </div>
		  
		  </td>
          </tr>
          <?php } ?>
        </table>
	    <br/>
          <div id="modifyuserbuttons">
		  <input name="doBan" type="submit" id="doBan" value="Inactive" class="awesomed">
          <input name="doUnban" type="submit" id="doUnban" value="Active" class="awesomed">
          <input name="query_str" type="hidden" id="query_str" value="<?php echo $_SERVER['QUERY_STRING']; ?>">
		  </div>
      </form>
	  
	  <?php } ?>
	 
	</div>
    
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
