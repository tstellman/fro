<?php 
include 'dbc.php';
page_protect();

if(!checkAdmin()) {
	header("Location: login.php");
	exit();
}

$page_limit = 100; 

$rs_all = mysql_query("SELECT COUNT(*) AS total_all FROM Donor") or die(mysql_error());

list($all) = mysql_fetch_row($rs_all);

if($_POST['doDeleteOrder'] == 'Delete') {

if(!empty($_POST['u'])) {
	foreach ($_POST['u'] as $uid) {
		$DonorNumber = filter($uid);
		mysql_query("DELETE FROM Donor WHERE DonorNumber='$DonorNumber'") or die(mysql_error());
	}
 }
$ret = $_SERVER['PHP_SELF'] . '?'.$_POST['query_str'];;
 
header("Location: $ret");
exit();
}

//Include the header:
include('templates/header.php');
?>

	<body><!--Main body content-->
	<div id="main" role="main" class="main">
		<h3 class="titlehdr">Modify Existing Donors</h3>
          
		<div id = "usercount">
		<strong>Total Donors:</strong> <?php echo $all;?>
		</div>
	  
		<?php 
		if(!empty($msg)) {
		echo $msg[0];
		}
		?>
          
		<form name="form1" method="get" action="modifydonor.php">
			<label for="doSearch" class="bolded">Donor Number: </label>
            <input name="q" type="text" id="q" size="20">&nbsp;<input name="doSearch" type="submit" id="doSearch2" value="Search" class="awesomed">
		</form>
	  
        <?php {
		$cond = '';
	  
		if($get['q'] == '') {
			$sql = "SELECT * FROM Donor" or die(mysql_error());
		}
		
		else {
			$sql = "SELECT * FROM Donor WHERE DonorNumber = '$_REQUEST[q]'" or die(mysql_error());
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
		while ($i < $total_pages)
		{
		$page_no = $i+1;
		$qstr = ereg_replace("&page=[0-9]+","",$_SERVER['QUERY_STRING']);
		echo "<a href=\"modifydonor.php?$qstr&page=$page_no\">$page_no</a> ";
		$i++;
		}
		echo "</div>";
		}  ?>
		</p>
		
		<form name="searchform" action="modifydonor.php" method="post">
        <table align="center" cellpadding="2" cellspacing="0" id="modifyusertable">
          <tr bgcolor="#FF4C50"> 
            <td width="5%"><div align="center"><strong><font color="white">&#9745;</font></strong></div></td>
            <td width="25%"><div align="center"><strong><font color="white">Donor #</font></strong></div></td>
            <td width="50%"><strong><font color="white">Donor Name</strong></font></td>
            <td width="20%"><div align="center">&nbsp;</div></td>
          </tr>
          <tr> 
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <?php while ($rrows = mysql_fetch_array($rs_results)) {?>
          <tr>
            <td><div align="center"><input name="u[]" type="checkbox" value="<?php echo $rrows['DonorNumber']; ?>" id="u[]"></div></td>
            <td><div align="left"><?php echo $rrows['DonorNumber'];?></div></td>
            <td><?php echo $rrows['DonorName']; ?></td>
            <td><div align="center"><font size="2"><a href="javascript:void(0);" onclick='$("#editdonor<?php echo $rrows['DonorNumber'];?>").show("slow");'>Edit</a></font></div></td>
          </tr>
          <tr>
            <td colspan="4">
			
			<div style="display:none;font: normal 12px arial; padding:5px 5px 10px 10px; background: #FF4C50" id="editdonor<?php echo $rrows['DonorNumber']; ?>">
			<input type="hidden" name="id<?php echo $rrows['DonorNumber']; ?>" id="id<?php echo $rrows['DonorNumber']; ?>" value="<?php echo $rrows['DonorNumber']; ?>">
			<div id="usercount"><a href="javascript:void(0);" onclick='$("#editdonor<?php echo $rrows['DonorNumber'];?>").hide();'><img src="img/close-button.png"></a></div>
			
			<div class="cf">
			<div class="modifylabels150">Donor Name:</div>
			<div class="rightcolumn"><input name="DonorName<?php echo $rrows['DonorNumber']; ?>" id="DonorName<?php echo $rrows['DonorNumber']; ?>" type="text" size="25" value="<?php echo $rrows['DonorName']; ?>" ></div>
			</div>
			
			<div class="cf">
			<div class="modifylabels150">Donor Number:</div>
			<div class="rightcolumn"><input name="DonorNumber<?php echo $rrows['DonorNumber']; ?>" id="DonorNumber<?php echo $rrows['DonorNumber']; ?>" type="text" size="6" readonly value="<?php echo $rrows['DonorNumber']; ?>" ></div>
			</div>
			
			<div class="cf">
			<div class="modifylabels150">Agency:</div>
			<div class="rightcolumn"><input name="ProgramNumber<?php echo $rrows['DonorNumber']; ?>" id="ProgramNumber<?php echo $rrows['DonorNumber']; ?>" type="text" size="3" maxlength="20" value="<?php echo $rrows['ProgramNumber']; ?>" ></div>
			</div>
			
			<div class="cf">
			<div class="modifylabels150">Grocery Store:</div>
			<div class="rightcolumn"><input name="GroceryStore<?php echo $rrows['DonorNumber']; ?>" id="GroceryStore<?php echo $rrows['DonorNumber']; ?>" type="text" size="3" maxlength="5" value="<?php echo $rrows['GroceryStore']; ?>" ></div>
			</div>
			
			<div class="cf">
			<div class="modifylabels150">Food Rescue:</div>
			<div class="rightcolumn"><input name="FoodRescue<?php echo $rrows['DonorNumber']; ?>" id="FoodRescue<?php echo $rrows['DonorNumber']; ?>" type="text" size="3" maxlength="5" value="<?php echo $rrows['FoodRescue']; ?>" ></div>
			</div>
			
			<div class="cf">
			<div class="modifylabels150">Food Drive:</div>
			<div class="rightcolumn"><input name="FoodDrive<?php echo $rrows['DonorNumber']; ?>" id="FoodDrive<?php echo $rrows['DonorNumber']; ?>" type="text" size="3" maxlength="5" value="<?php echo $rrows['FoodDrive']; ?>" ></div>
			</div>
			
			<HR class="white">
			
			<div class="cf">
			<div class="modifylabels150">Address:</div>
			<div class="rightcolumn"><input name="Address1<?php echo $rrows['DonorNumber']; ?>" id="Address1<?php echo $rrows['DonorNumber']; ?>" type="text" size="25" value="<?php echo $rrows['Address1']; ?>" ><br /><input name="Address2<?php echo $rrows['DonorNumber']; ?>" id="Address2<?php echo $rrows['DonorNumber']; ?>" type="text" size="25" value="<?php echo $rrows['Address2']; ?>" ></div>
			</div>
			
			<div class="cf">
			<div class="modifylabels150">City:</div>
			<div class="rightcolumn"><input name="City<?php echo $rrows['DonorNumber']; ?>" id="City<?php echo $rrows['DonorNumber']; ?>" type="text" size="20" value="<?php echo $rrows['City']; ?>" ></div>
			</div>
			
			<div class="cf">
			<div class="modifylabels150">State:</div>
			<div class="rightcolumn"><input name="State<?php echo $rrows['DonorNumber']; ?>" id="State<?php echo $rrows['DonorNumber']; ?>" type="text" size="1" maxlength="2" value="<?php echo $rrows['State']; ?>" ></div>
			</div>
			
			<div class="cf">
			<div class="modifylabels150">Zip:</div>
			<div class="rightcolumn"><input name="ZipCode<?php echo $rrows['DonorNumber']; ?>" id="ZipCode<?php echo $rrows['DonorNumber']; ?>" type="text" size="3" maxlength="5" value="<?php echo $rrows['ZipCode']; ?>" ></div>
			</div>
			
			<p align="center"><input name="doSave" type="button" id="doSave" value="Save" class="awesomed" onclick='$.get("do.php",{ cmd: "editdonor",DonorName:$("input#DonorName<?php echo $rrows['DonorNumber']; ?>").val(),DonorNumber:$("input#DonorNumber<?php echo $rrows['DonorNumber']; ?>").val(),Address1:$("input#Address1<?php echo $rrows['DonorNumber']; ?>").val(),Address2:$("input#Address2<?php echo $rrows['DonorNumber']; ?>").val(),City:$("input#City<?php echo $rrows['DonorNumber']; ?>").val(),State:$("input#State<?php echo $rrows['DonorNumber']; ?>").val(),ZipCode:$("input#ZipCode<?php echo $rrows['DonorNumber']; ?>").val(),GroceryStore:$("input#GroceryStore<?php echo $rrows['DonorNumber']; ?>").val(),FoodRescue:$("input#FoodRescue<?php echo $rrows['DonorNumber']; ?>").val(),FoodDrive:$("input#FoodDrive<?php echo $rrows['DonorNumber']; ?>").val(),ProgramNumber:$("input#ProgramNumber<?php echo $rrows['DonorNumber']; ?>").val() },function(data){ $("#msg<?php echo $rrows['DonorNumber']; ?>").html(data); });'><div style="color:white" id="msg<?php echo $rrows['DonorNumber']; ?>" name="msg<?php echo $rrows['DonorNumber']; ?>"></div></p>
			
		  </div>
		  
		  </td>
          </tr>
          <?php } ?>
        </table>
		  <br />
			<input name="doDeleteOrder" type="submit" id="doDeleteOrder" value="Delete" class="awesomed">
			<input name="query_str" type="hidden" id="query_str" value="<?php echo $_SERVER['QUERY_STRING']; ?>">
      </form>
	  
	  <?php } ?>
	 
	</div>
    
	<!--Include the footer:-->
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
