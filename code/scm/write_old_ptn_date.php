<?php

define("DB_HOST","localhost");
define("DB_LOGIN","JackYang");
define("DB_PASSWORD","yankee610");
//define("DB_NAME","scm_test");
define("DB_NAME","scm_test");
# 連上資料庫
Connect_db();

function Connect_db(){
	if(empty($_SESSION["link_db"])){
		$_SESSION["link_db"] = @mysql_connect(DB_HOST, DB_LOGIN, DB_PASSWORD);
		if(!$_SESSION["link_db"]){
			echo ("FATAL:Couldn't connect to db.<br>");
			exit;
		}
	}
	
	$_SESSION["Select_DB"] = @mysql_select_db(DB_NAME, $_SESSION["link_db"]);
	if(!$_SESSION["Select_DB"]){
		echo ("FATAL:Couldn't connect to db.<br>");
		exit;
	}
}

	$sql = "select file_name
			from ord_ptn_file";
	$ord_res= mysql_query($sql);
	
	while($ord_row = mysql_fetch_array($ord_res)){
		$sql = "select ptn_upload
			from s_order
			where order_num = '".$ord_row['file_name']."'";
		$date_res= mysql_query($sql);
		$date_row = mysql_fetch_array($date_res);
		if($date_row){
			echo "update ".$ord_row['file_name'];
			$sql = "update ord_ptn_file set file_date = '".$date_row['ptn_upload']." 00:00:00"."' where file_name='".$ord_row['file_name']."'";
			$update_res= mysql_query($sql);
			echo " OK<BR>";
		}
		
	}
	
echo "<BR>end";exit;
?>