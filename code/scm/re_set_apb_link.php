<?php

# 暫無轉檔用途 ， 純粹備份轉檔程式用
// session_start();
// mb_internal_encoding("big5");

// echo '<meta http-equiv="Content-Type" content="text/html; charset=big5">';
define("DB_HOST","localhost");
define("DB_LOGIN","JackYang");
define("DB_PASSWORD","yankee610");
define("DB_NAME","scm_smpl");

# 連上資料庫
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
    return $_SESSION["link_db"];
}

$DB = Connect_db();

$sql = "SELECT * from apb where rcv_date between '2012-06-01' and '2012-06-13';";
$result = mysql_query($sql);
while($apb_row = mysql_fetch_array($result)){
    if( $apb_row['currency'] <> "NTD" ){
		$total_price = 0;
		$q_str = "select *
					from apb_det
					where rcv_num='".$apb_row['rcv_num']."'";
		$det_result =  mysql_query($q_str);
		while($det_row = mysql_fetch_array($det_result)){
			$total_price += number_format($det_row['qty'] * $det_row['uprices'], 2, '', '');
			$q_str = "select * from apb_po_link
						where rcv_id=".$det_row['id'];
			$link_result =  mysql_query($q_str);
			while($link_row = mysql_fetch_array($link_result)){
				$q_str = "update apb_po_link set amount=".number_format($link_row['qty']*$det_row['uprices'], 2, '', '')." where id=".$link_row['id'];
				$link_update =  mysql_query($q_str);
			}
			$q_str = "update apb set inv_price=".number_format($total_price, 2, '', '')." where id=".$apb_row['id'];
			$link_update =  mysql_query($q_str);
		}
	}
}
echo "end";



?>