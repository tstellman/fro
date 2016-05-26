<?php

function openDB() {
	global $mysql;
	if (!$link) $link= mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if (!$link) {
		return 0;
	} elseif (!mysql_select_db(DB_NAME, $link)) {
		return 0;
	} else {
		return $link;
	}
}

function sqlQuery($sql){
	global $link;
	if (!$link) $link=openDB();
	if (!$link) { return ; } else {
		$trs = mysql_query($sql, $link);
		return $trs;
	}
}

function md5pw($pw){
	$outpw= md5('aid!MATRIX#'.$pw.'--besT.eVr--');
	return $outpw;
}

function checkUser($u, $p, $prog){
	$p = md5pw($p);
	$rs = sqlQuery("SELECT uid FROM pa_accounts WHERE username = '$u' AND password = '$p' AND description = '$prog' LIMIT 1");
	if ($rs){
		$r = @mysql_fetch_assoc($rs);
		if ($r['uid']>0){
			return true;
		} else return false;
	} else return false;
}

function isAuth(){
	return $_SESSION['auth_session'];
}