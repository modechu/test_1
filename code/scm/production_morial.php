<?php
// phpinfo();
// exit;
# �ȵL���ɥγ~ �A �º�ƥ����ɵ{����
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


# ��鷺�e USER / TEAM
#$tb_user = true;
#$tb_team = true;

# �]�w�v�� ukey = �ĴX�U�}�C uval = ��諸��
#$ukey = '6';
#$uval = '1';

# �s�W��Ʈw
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
`saw_out_put`.`out_date` as `�Ͳ����`,
`saw_out_put`.`saw_line` as `�Z�էO`,
`saw_out_put`.`ord_num` as `�q�渹`,
`order_partial`.`p_etd` as `ETD`,
`saw_out_put`.`qty` as `�Ͳ����`,
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

    if( substr($tmp['�Z�էO'],0,1) <> "A" && $tmp['�Z�էO'] <> "contractor" && $tmp['�Ͳ����'] > 0 ) {
				// echo $tmp['�Z�էO']."<br>";
        $tmp['�Z�էO'] = 'BL'.str_pad($tmp['�Z�էO'],2,'0',STR_PAD_LEFT);
        
        // echo $tmp['�Ͳ����'].','.$tmp['�Z�էO'].','.$tmp['�q�渹'].','.$num[$tmp['ETD']].','.$tmp['�Ͳ����']."<br>";
        echo $rdate.','.$tmp['�Z�էO'].','.$tmp['�q�渹'].','.$tmp['ETD'].','.$tmp['�Ͳ����']."<br>";
        $DaTa .= $rdate.','.$tmp['�Z�էO'].','.$tmp['�q�渹'].','.$tmp['ETD'].','.$tmp['�Ͳ����']."\n";

    }

}

$file = fopen('saw_out_put'.$rdate.'.csv','w');
fputs($file,$DaTa);
fclose($file);

exit;
?>