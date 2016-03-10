<?php
// phpinfo();
// exit;
# 暫無轉檔用途 ， 純粹備份轉檔程式用
// session_start();
// mb_internal_encoding("big5");

// echo '<meta http-equiv="Content-Type" content="text/html; charset=big5">';
define("DB_HOST","localhost");
define("DB_LOGIN","JackYang");
define("DB_PASSWORD","yankee610");
 define("DB_NAME","scm_smpl");
// define("DB_NAME","scm_maintain");
// define("DB_NAME","scm_tmp");
//define("DB_NAME","scm_test");
/*--------------\
\***************/


# 更改內容 USER / TEAM
#$tb_user = true;
#$tb_team = true;

# 設定權限 ukey = 第幾各陣列 uval = 更改的值
#$ukey = '6';
#$uval = '1';

# 連上資料庫
###############################################################################################################
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
###############################################################################################################
$DB = Connect_db();

$ndate = '2012-12-25';
$rdate = '2012-12-24';


$num = array (
'A' => 1 ,
'B' => 2 ,
'C' => 3 ,
'D' => 4 ,
'E' => 5 ,
'F' => 6 ,
'G' => 7 ,
'H' => 8 ,
'I' => 9 ,
'J' => 10 ,
'K' => 11 ,
'L' => 12 ,
'M' => 13 ,
'N' => 14 ,
'O' => 15 ,
'P' => 16 ,
'Q' => 17 ,
'R' => 18 ,
'S' => 19 ,
'T' => 20 ,
'U' => 21 ,
'V' => 22 ,
'W' => 23 ,
'X' => 24 ,
'Y' => 25 ,
'Z' => 26 
);
// BTH1-246 and BTH1-242


$sql = "
SELECT 
`saw_out_put`.`out_date` as `生產日期`,
`saw_out_put`.`saw_line` as `班組別`,
`saw_out_put`.`ord_num` as `訂單號`,
`order_partial`.`p_etd` as `ETD`,
`saw_out_put`.`qty` as `生產件數`,
`order_partial`.`p_etp`,
`order_partial`.`p_etd`
,`order_partial`.`id`
FROM `saw_out_put`,`order_partial` 

WHERE 
`saw_out_put`.`p_id` = `order_partial`.`id` AND 
`saw_out_put`.`out_date` = '".$ndate."' AND 
`saw_out_put`.`saw_fty` = 'LY'
;";

// `saw_out_put`.`ord_num` = 'BTH1-246' AND 
// ( `saw_out_put`.`out_date` between '2012-05-11' AND '2012-05-20' ) AND 
$res = mysql_query($sql);

$DaTa = '';

while($tmp = mysql_fetch_array($res)){

    if( substr($tmp['班組別'],0,1) <> "A" && $tmp['班組別'] <> "contractor" && $tmp['生產件數'] > 0 ) {
				// echo $tmp['班組別']."<br>";
        $tmp['班組別'] = 'BL'.str_pad($tmp['班組別'],2,'0',STR_PAD_LEFT);
        
        // echo $tmp['生產日期'].','.$tmp['班組別'].','.$tmp['訂單號'].','.$num[$tmp['ETD']].','.$tmp['生產件數']."<br>";
        echo $rdate.','.$tmp['班組別'].','.$tmp['訂單號'].','.$tmp['ETD'].','.$tmp['生產件數']."<br>";
        $DaTa .= $rdate.','.$tmp['班組別'].','.$tmp['訂單號'].','.$tmp['ETD'].','.$tmp['生產件數']."\n";

    }

}

$file = fopen('saw_out_put'.$rdate.'.csv','w');
fputs($file,$DaTa);
fclose($file);

exit;
?>