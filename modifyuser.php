<?php 
include 'dbc.php';
page_protect();

if(!checkAdmin()) {
	header("Location: login.php");
	exit();
}

$page_limit = 10;

if($_POST['doDelete'] == 'Delete') {

if(!empty($_POST['u'])) {
	foreach ($_POST['u'] as $uid) {
		$Id = filter($uid);
		mysql_query("DELETE FROM User WHERE UserId='$Id'");
	}
}
$ret = $_SERVER['PHP_SELF'] . '?'.$_POST['query_str'];;
 
header("Location: $ret");
exit();
}

$rs_all = mysql_query("SELECT COUNT(*) AS total_all FROM User WHERE NOT `UserAccess` = '1' ") or die(mysql_error());					   
list($all) = mysql_fetch_row($rs_all);

//Include the header
include('templates/header.php');
?>

	<div id="main" role="main" class="main">
	<h3 class="titlehdr">Modify Existing Administrators</h3>
          
		<div id = "usercount">
		<strong>Total Admins:</strong> <?php echo $all;?>
		</div>
	  
		<?php 
		if(!empty($msg)) {
		echo $msg[0];
		}
		?>
          
		<form name="form1" method="get" action="modifyuser.php">
			<label for="doSearch" class="bolded">Username/Email: </label>
			<input name="q" type="text" id="q" size="20">&nbsp;<input name="doSearch" type="submit" id="doSearch2" value="Search" class="awesomed">
		</form>
	  
        <?php {
		$cond = '';
	  
		if($get['q'] == '') {
			$sql = "SELECT * FROM User $cond WHERE NOT `UserAccess` = '1' ";
		}
		else {
			$sql = "SELECT * FROM User WHERE `Email` = '$get[q]' AND `UserAccess` <> '1' OR `Username`='$get[q]' AND `UserAccess` <> '1' ";
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
		echo "<a href=\"modifyuser.php?$qstr&page=$page_no\">$page_no</a> ";
		$i++;
		}
		echo "</div>";
		}  
		?>
		</p>
		
		<form name "searchform" action="modifyuser.php" method="post">
        <table align="center" cellpadding="2" cellspacing="0" id="modifyusertable">
          <tr bgcolor="#FF4C50"> 
            <td width="5%"><div align="center"><strong><font color="white">&#9745;</font></strong></div></td>
            <td width="25%"><div align="center"><strong><font color="white">Username</font></strong></div></td>
            <td width="45%"><strong><font color="white">Email</strong></font></td>
            <td width="25%"><div align="center">&nbsp;</div></td>
          </tr>
          <tr> 
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <?php while ($rrows = mysql_fetch_array($rs_results)) {?>
          <tr> 
            <td><div align="center"><input name="u[]" type="checkbox" value="<?php echo $rrows['UserId']; ?>" id="u[]"></div></td>
            <td><div align="center"><?php echo $rrows['Username'];?></div></td>
            <td><?php echo $rrows['Email']; ?></td>
            <td><div align="center"><font size="2"><a href="javascript:void(0);" onclick='$("#edit<?php echo $rrows['UserId'];?>").show("slow");'>Edit</a></font></div></td>
          </tr>
          <tr> 
            <td colspan="4">
			
			<div style="display:none;font: normal 12px arial; padding:5px 5px 10px 10px;  background: #FF4C50" id="edit<?php echo $rrows['UserId']; ?>">
				<input type="hidden" name="id<?php echo $rrows['UserId']; ?>" id="id<?php echo $rrows['UserId']; ?>" value="<?php echo $rrows['UserId']; ?>">
				<div id="usercount"><a href="javascript:void(0);" onclick='$("#edit<?php echo $rrows['UserId'];?>").hide();'><img src="img/close-button.png"></a></div>			
				
				<div class="cf">
				<div class="modifylabels150">User Name:</div>
				<div class="rightcolumn"><input name="Username<?php echo $rrows['UserId']; ?>" id="Username<?php echo $rrows['UserId']; ?>" type="text" size="8" value="<?php echo $rrows['Username']; ?>" ></div>
				</div>
				
				<div class="cf">
				<div class="modifylabels150">User Email:</div>
				<div class="rightcolumn"><input id="Email<?php echo $rrows['UserId']; ?>" name="Email<?php echo $rrows['UserId']; ?>" type="text" size="25" value="<?php echo $rrows['Email']; ?>" ></div>
				</div>
				
				<div class="cf">
				<div class="modifylabels150">Reset Password:</div>
				<div class="rightcolumn"><input id="pass<?php echo $rrows['UserId']; ?>" name="pass<?php echo $rrows['UserId']; ?>" type="text" size="10" value="" ></div>
				</div>
				
				<p align="center"><input name="doSave" type="button" id="doSave" value="Save" class="awesomed" onclick='$.get("do.php",{ cmd: "edit", pass:$("input#pass<?php echo $rrows['UserId']; ?>").val(),UserAccess:$("input#UserAccess<?php echo $rrows['UserId']; ?>").val(),Email:$("input#Email<?php echo $rrows['UserId']; ?>").val(),Phone:$("input#Phone<?php echo $rrows['UserId']; ?>").val(),Username: $("input#Username<?php echo $rrows['UserId']; ?>").val(),UserId: $("input#id<?php echo $rrows['UserId']; ?>").val() } ,function(data){ $("#msg<?php echo $rrows['UserId']; ?>").html(data); });'><div style="color:white" id="msg<?php echo $rrows['UserId']; ?>" name="msg<?php echo $rrows['UserId']; ?>"></div></p>
			</div>
		  
		  </td>
          </tr>
          <?php } ?>
        </table>
	    <br />
          <div id = "modifyuserbuttons">
          <input name="doDelete" type="submit" id="doDelete" value="Delete" class="awesomed">
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
