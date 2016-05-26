<?php 
include 'dbc.php';
page_protect();

if(!checkAdmin()) {
	header("Location: login.php");
	exit();
}

$page_limit = 20; 

if($_POST['doDeleteOrder'] == 'Delete') {

if(!empty($_POST['u'])) {
	foreach ($_POST['u'] as $uid) {
		$PickupId = filter($uid);
		mysql_query("DELETE FROM PickupDetail WHERE PickupId = '$PickupId'");
		mysql_query("DELETE FROM PickupHeader WHERE PickupId = '$PickupId'");
	}
}
$ret = $_SERVER['PHP_SELF'] . '?'.$_POST['query_str'];;
 
header("Location: $ret");
exit();
}

//Include the header
include('templates/header.php');
?>

	<div id="main" role="main" class="main">
		<h3 class="titlehdr">Modify Existing Pickups</h3>
	  
		<?php 
		if(!empty($msg)) {
			echo $msg[0];
		}
		?>
          
		  <form name="form1" method="get" action="modifypickups.php">
              <label for="doSearch" class="bolded">Program Number: </label>
			  <input name="q" type="text" id="q" size="20">&nbsp;<input name="doSearch" type="submit" id="doSearch2" value="Search" class="awesomed">
          </form>
	  
        <?php {
	  $cond = '';
	  
	  if($get['q'] == '') {
	  $sql = "SELECT oh.PickupId, DonorNumber, PickupDate, oh.ProgramNumber, ProgramName,
				GROUP_CONCAT(if(LineNumber = 1, Quantity, NULL)) AS 'Bread',
				GROUP_CONCAT(if(LineNumber = 2, Quantity, NULL)) AS 'AssortedRefrigeratedProduct',
				GROUP_CONCAT(if(LineNumber = 3, Quantity, NULL)) AS 'Produce',
				GROUP_CONCAT(if(LineNumber = 4, Quantity, NULL)) AS 'AssortedMixedDry',
				GROUP_CONCAT(if(LineNumber = 5, Quantity, NULL)) AS 'AssortedFrozen'
			FROM PickupDetail od, PickupHeader oh, Program p
			WHERE od.PickupId = oh.PickupId AND oh.ProgramNumber = p.ProgramNumber
			GROUP BY oh.PickupId
			Order BY PickupDate DESC";
	  }
	  else {
	  $sql = "SELECT oh.PickupId, DonorNumber, PickupDate, oh.ProgramNumber, ProgramName,
				GROUP_CONCAT(if(LineNumber = 1, Quantity, NULL)) AS 'Bread',
				GROUP_CONCAT(if(LineNumber = 2, Quantity, NULL)) AS 'AssortedRefrigeratedProduct',
				GROUP_CONCAT(if(LineNumber = 3, Quantity, NULL)) AS 'Produce',
				GROUP_CONCAT(if(LineNumber = 4, Quantity, NULL)) AS 'AssortedMixedDry',
				GROUP_CONCAT(if(LineNumber = 5, Quantity, NULL)) AS 'AssortedFrozen'
			FROM PickupDetail od, PickupHeader oh, Program p
			WHERE oh.ProgramNumber = '$get[q]' AND oh.ProgramNumber = p.ProgramNumber AND od.PickupId = oh.PickupId
			GROUP BY oh.PickupId
			Order BY PickupDate DESC";
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
		echo "<a href=\"modifypickups.php?$qstr&page=$page_no\">$page_no</a> ";
		$i++;
		}
		echo "</div>";
		}  
		?>
		</p>
		
		<form name="searchform" action="modifypickups.php" method="post">
        <table align="center" cellpadding="2" cellspacing="0" id="modifyprogramtable">
          <tr bgcolor="#FF4C50">
            <td width="5%"><div align="center"><strong><font color="white">&#9745;</font></strong></div></td>
            <td width="20%"><div align="center"><strong><font color="white">Pickup Date</font></strong></div></td>
            <td width="20%"><div align="center"><strong><font color="white">Program #</font></strong></div></td>
            <td width="45%"><strong><font color="white">Program Name</font></strong></td>
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
            <td><div align="center"><input name="u[]" type="checkbox" value="<?php echo $rrows['PickupId']; ?>" id="u[]"></div></td>
            <td><div align="center"><?php echo $rrows['PickupDate']; ?></div></td>
			<td><div align="center"><?php echo $rrows['ProgramNumber'];?></div></td>
			<td><?php echo $rrows['ProgramName']; ?></td>
			<td><div align="center"><font size="2"><a href="javascript:void(0);" onclick='$("#editorder<?php echo $rrows['PickupId'];?>").show("slow");'>Edit</a></font></div></td>
          </tr>
          <tr> 
            <td colspan="5">
			
			<div style="display:none;font: normal 12px arial; padding:5px 5px 10px 10px; background: #FF4C50" id="editorder<?php echo $rrows['PickupId']; ?>">
			<input type="hidden" name="PickupId<?php echo $rrows['PickupId']; ?>" id="PickupId<?php echo $rrows['PickupId']; ?>" value="<?php echo $rrows['PickupId']; ?>">
			<input type="hidden" name="ProgramNumber<?php echo $rrows['PickupId']; ?>" id="ProgramNumber<?php echo $rrows['PickupId']; ?>" value="<?php echo $rrows['ProgramNumber']; ?>">
			
			<div id="usercount"><a href="javascript:void(0);" onclick='$("#editorder<?php echo $rrows['PickupId'];?>").hide();'><img src="img/close-button.png"></a></div>
			
			<div class="cf">
			<div class="modifylabels150">Pickup Date:</div>
			<div class="rightcolumn"><input name="PickupDate<?php echo $rrows['PickupId']; ?>" id="PickupDate<?php echo $rrows['PickupId']; ?>" type="text" size="8" maxlength="10" value="<?php echo $rrows['PickupDate']; ?>" readonly ></div>
			</div>
			
			<div class="cf">
			<div class="modifylabels150">Donor #:</div>
			<div class="rightcolumn"><input name="DonorNumber<?php echo $rrows['PickupId']; ?>" id="DonorNumber<?php echo $rrows['PickupId']; ?>" type="text" size="8" value="<?php echo $rrows['DonorNumber']; ?>" ></div>
			</div>
			
			<HR class="white">
			
			<div class="cf">
			<div class="modifylabels150">Bread:</div>
			<div class="rightcolumn"><input id="BreadQuantity<?php echo $rrows['PickupId']; ?>" name="BreadQuantity<?php echo $rrows['PickupId']; ?>" type="text" size="1" maxlength="3" value="<?php echo $rrows['Bread']; ?>" ></div>
			</div>
			
			<div class="cf">
			<div class="modifylabels150">Assorted Refrigerated:</div>
			<div class="rightcolumn"><input id="AssortedRefrigeratedProductQuantity<?php echo $rrows['PickupId']; ?>" name="AssortedRefrigeratedProductQuantity<?php echo $rrows['PickupId']; ?>" type="text" size="1" maxlength="3" value="<?php echo $rrows['AssortedRefrigeratedProduct']; ?>" ></div>
			</div>
			
			<div class="cf">
			<div class="modifylabels150">Produce:</div>
			<div class="rightcolumn"><input id="ProduceQuantity<?php echo $rrows['PickupId']; ?>" name="ProduceQuantity<?php echo $rrows['PickupId']; ?>" type="text" size="1" maxlength="3" value="<?php echo $rrows['Produce']; ?>" ></div>
			</div>
			
			<div class="cf">
			<div class="modifylabels150">Assorted Mixed Dry:</div>
			<div class="rightcolumn"><input id="AssortedMixedDryQuantity<?php echo $rrows['PickupId']; ?>" name="AssortedMixedDryQuantity<?php echo $rrows['PickupId']; ?>" type="text" size="1" maxlength="3" value="<?php echo $rrows['AssortedMixedDry']; ?>" ></div>
			</div>
			
			<div class="cf">
			<div class="modifylabels150">Assorted Frozen:</div>
			<div class="rightcolumn"><input id="AssortedFrozenQuantity<?php echo $rrows['PickupId']; ?>" name="AssortedFrozenQuantity<?php echo $rrows['PickupId']; ?>" type="text" size="1" maxlength="3" value="<?php echo $rrows['AssortedFrozen']; ?>" ></div>
			</div>
			
			<p align="center"><input name="doSave" type="button" id="doSave" value="Save" class="awesomed" onclick='$.get("do.php",{ cmd: "editorder", PickupId:$("input#PickupId<?php echo $rrows['PickupId']; ?>").val(),PickupDate:$("input#PickupDate<?php echo $rrows['PickupId']; ?>").val(),DonorNumber:$("input#DonorNumber<?php echo $rrows['PickupId']; ?>").val(),BreadQuantity:$("input#BreadQuantity<?php echo $rrows['PickupId']; ?>").val(),AssortedRefrigeratedProductQuantity:$("input#AssortedRefrigeratedProductQuantity<?php echo $rrows['PickupId']; ?>").val(),ProduceQuantity:$("input#ProduceQuantity<?php echo $rrows['PickupId']; ?>").val(),AssortedMixedDryQuantity:$("input#AssortedMixedDryQuantity<?php echo $rrows['PickupId']; ?>").val(),AssortedFrozenQuantity:$("input#AssortedFrozenQuantity<?php echo $rrows['PickupId']; ?>").val(),ProgramNumber:$("input#ProgramNumber<?php echo $rrows['PickupId']; ?>").val() } ,function(data){ $("#msg<?php echo $rrows['PickupId']; ?>").html(data); });'><div style="color:white" id="msg<?php echo $rrows['PickupId']; ?>" name="msg<?php echo $rrows['PickupId']; ?>"></div></p>
		  </div>
		  
		  </td>
          </tr>
          <?php } ?>
        </table>
		<br />
          <input name="doDeleteOrder" type="submit" id="doDeleteOrder" value="Delete" class="awesomed">
          <input name="query_str" type="hidden" id="query_str" value="<?php echo $_SERVER['QUERY_STRING']; ?>">
		  <br />  
      </form>
	  
	  <?php } ?>
	 
	</div>
    
	<!--Include the footer-->
	<?php
	include('templates/footer.html');
	?>
	
</div> <!--! end of #container -->

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
