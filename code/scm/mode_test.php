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
// define("DB_NAME","scm_smpl");
// define("DB_NAME","scm_maintain");
// define("DB_NAME","scm_tmp");
define("DB_NAME","scm_test");
echo date('H:i:s ');

//define("DB_HOST","192.168.1.181");
//define("DB_LOGIN","mode");
//define("DB_PASSWORD","6699");
//define("DB_NAME","dbo");
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
		$_SESSION["link_db"] = mysql_connect(DB_HOST, DB_LOGIN, DB_PASSWORD);
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



$sel_sql="select * from flowslots ";

// echo $sel_sql.'<br>';    
$res = mysql_query($sel_sql);

while( $value = mysql_fetch_array($res) ){
    print($value);
}
exit;

# 修改排產時間
// exit;
// $sel_sql="select * from schedule WHERE fty = 'HJ' ";

// echo $sel_sql.'<br>';    
// $res = mysql_query($sel_sql);

// while( $value = mysql_fetch_array($res) ){

    // $sel_sqls="select MAX(out_date) as m_b , MIN(out_date) as m_m from saw_out_put where `s_id` = '".$value['id']."' ;";
    // echo $sel_sqls.'<br>';   
    // $ress = mysql_query($sel_sqls);
    // $values = mysql_fetch_array($ress);

    // if ( !empty($values['m_b'])) {
    
        // echo $values['m_b'].' ~ '.$values['m_m'].'<br>';
        // $new_sql_str = " update schedule set `rel_ets` = '".$values['m_m']."' , `rel_etf` = '".$values['m_b']."' WHERE `id` = '".$value['id']."' ";
        // mysql_query($new_sql_str);
        // echo $new_sql_str.'<p>';    
    // }
// }


// exit;



# 增加 order_num + wi_id 到 ap_det

// $ord = array();
// $sel_sql="select id,bom_id,mat_cat from ap_det ";
// echo $sel_sql.'<br>';    
// $res = mysql_query($sel_sql);

// while( $value = mysql_fetch_array($res) ){
    
    // if ( $value['mat_cat'] == 'l' ) {
    
        // $sel_sqls="select bom_lots.wi_id , wi.wi_num from bom_lots , wi  where `bom_lots`.`id` = '".$value['bom_id']."' AND bom_lots.wi_id = wi.id  ";
        // echo $sel_sqls.'<br>';   
        // $ress = mysql_query($sel_sqls);
        // $values = mysql_fetch_array($ress);
        
    // } else {
    
        // $sel_sqls="select bom_acc.wi_id , wi.wi_num from bom_acc , wi  where `bom_acc`.`id` = '".$value['bom_id']."' AND bom_acc.wi_id = wi.id  ";
        // echo $sel_sqls.'<br>';   
        // $ress = mysql_query($sel_sqls);
        // $values = mysql_fetch_array($ress);
    
    // }
    
     
	// $new_sql_str = " update ap_det set `order_num` = '".$values['wi_num']."' , `wi_id` = '".$values['wi_id']."' WHERE `id` = '".$value['id']."' ";
	// mysql_query($new_sql_str);
	// echo $new_sql_str.'<p>';    
    
// }



// exit;




# 重新計算原始數量
// $ORDER = 'DDB12-0886';
// $ORDER = 'BLR12-0798';
// $ORDER = 'BBT12-0947';
// $ORDER = 'BCH12-0826';
// $ORDER = 'BLR12-0880';
// $ORDER = 'DLS12-0934';
$ORDER = 'DTH13-1497';


$sql = "SELECT * FROM `s_order` WHERE `order_num` = '".$ORDER."';";
$res = mysql_query($sql);
$ord = mysql_fetch_array($res);

$sql = "SELECT * FROM `size_des` WHERE `id` = '".$ord['size']."';";
$res = mysql_query($sql);
$size = mysql_fetch_array($res);
$size = $size['size'];
$size_arr = explode(',',$size);

$sql = "SELECT * FROM `wi` WHERE `wi_num` = '".$ORDER."' ;";
$res = mysql_query($sql);
$wi = mysql_fetch_array($res);

$sql = "SELECT * FROM `wiqty` WHERE `wi_id` = '".$wi['id']."' ORDER BY `p_id` ASC  ;";
$res = mysql_query($sql);
$qty0 = $qty1 = $qty2 = $qty3 = $wi_id = array();
$qty0_id = $qty1_id = $qty2_id = $qty3_id = $wi_id = array();
$m = 0;
while($tmp = mysql_fetch_array($res)){
	// echo $tmp['id'].' ~ '.$tmp['lots_used_id'].' ~ '.$tmp['color'].' ~ '.$tmp['qty'].' ~ '.$tmp['ap_mark'].'<br>';
	

	$qty_arr = explode(',',$tmp['qty']);
	$qty0[] = array_sum($qty_arr);
	$qty0_id[] = $tmp['id'];
	
	for( $i=0; $i < count($size_arr); $i++ ){
		// echo $m;
		$qty1[$size_arr[$i]] = @$qty1[$size_arr[$i]] + $qty_arr[$i];
		$qty1_id[$size_arr[$i]] = $tmp['id'];
		
		$qty2[$size_arr[$i]][$m] = @$qty2[$size_arr[$i]][$m] + $qty_arr[$i];
		$qty2_id[$size_arr[$i]][$m] = $tmp['id'];
		// echo $size_arr[$i].'<br>';
		// $qty1 = 
	}
	$m++;
	// echo 'QTY = '.$qty.'<br>';
	// echo 'QTY = '.$tmp['qty'].'<br>';
}
echo '<p>';

$qty3 = array_sum($qty1);

// print_r($qty0);
// echo '<p>';
// print_r($qty1);
// echo '<p>';
// print_r($qty2);
// echo '<p>';
// print_r($qty3);
// echo '<p>';
// print_r($size);



// echo '<p>';
// print_r($qty0_id);

// echo '<p>';
// print_r($size_arr);


# 抓主料
$sql = "
SELECT 
`bom_lots`.`id` as `bom_id` , `bom_lots`.`qty` as `bom_qty` , `bom_lots`.`ap_mark` ,
`lots_use`.`est_1` 

FROM 
`bom_lots` LEFT JOIN `lots_use` ON ( `bom_lots`.`lots_used_id` = `lots_use`.`id` ) 

WHERE `bom_lots`.`wi_id` = '".$wi['id']."'
 
ORDER BY `bom_lots`.`id`
;";
// echo $sql.'<p>';	
$res = mysql_query($sql);
$m = 0;
while($tmp = mysql_fetch_array($res)){
	// echo $tmp['bom_id'].' ~ '.$tmp['est_1'].' ~ '.$tmp['ap_mark'].'<br>';
	// echo $tmp['bom_qty'].'<br>';
	
	$qty_arr = explode(',',$tmp['bom_qty']);
	
	$qty_str = '';
	$qty_str2 = '';
	for( $i=0; $i < count($qty_arr); $i++ ){
		$qty_str .= ( $i > 0 )? ',':'';
		$qty_str2 .= ( $i > 0 )? ',':'';
		if(!empty($qty_arr[$i])) {
			// echo $qty0[$i].' * '.$tmp['est_1'].' = '.($qty0[$i]*$tmp['est_1']).' ~ '.$qty_arr[$i].'<br>';
			$qty_str .= ( ($qty0[$i]*$tmp['est_1'])  > 0 ) ? $qty0[$i]*$tmp['est_1'] : '0' ;
			$qty_str2 .= ( !empty($qty0[$i]) ) ? $qty0[$i] : '0' ;
		} else {
			$qty_str .= '0' ;
			$qty_str2 .= '0' ;
		}
		// $qty_id = $qty0_id[$i];
		// $q_sql = "UPDATE `bom_lots` SET `qty_id` = '".$qty0_id[$m]."' WHERE `id` = '".$tmp['bom_id']."' ;";
		// echo $q_sql.$qty0_id[$i].'<br>';		
	}
	// echo $qty_str.'<p>';
	// echo $tmp['bom_id'].' ~ '.$qty_str.'<br>';
	$q_sql = "UPDATE `bom_lots` SET `qty` = '".$qty_str."' , `o_qty` = '".$qty_str2."' WHERE `id` = '".$tmp['bom_id']."' ;";
	echo $q_sql.'<br>';
	$q_res = mysql_query($q_sql);
	$qty_ttl = array_sum(explode(',',$qty_str));
	echo $qty_ttl.'<p>';	
	$m++;
}


echo '<p>';


# 抓副料
$sql = "
SELECT 
`bom_acc`.`id` as `bom_id` , `bom_acc`.`qty` as `bom_qty` , `bom_acc`.`ap_mark` , `bom_acc`.`size` ,
`acc_use`.`est_1` , 
`acc`.`arrange`

FROM 
`bom_acc` LEFT JOIN `acc_use` ON ( `bom_acc`.`acc_used_id` = `acc_use`.`id` ) 
LEFT JOIN `acc` ON ( `acc_use`.`acc_code` = `acc`.`acc_code` ) 

WHERE `bom_acc`.`wi_id` = '".$wi['id']."'
 
ORDER BY `bom_acc`.`id`
;";

$res = mysql_query($sql);
while($tmp = mysql_fetch_array($res)){
	// echo $tmp['bom_id'].' ~ '.$tmp['est_1'].' ~ '.$tmp['arrange'].' ~ '.$tmp['ap_mark'].' ~ '.$tmp['size'].'<br>';
	// echo $tmp['bom_qty'].'<br>';
	// echo $tmp['id'].' ~ '.$tmp['acc_used_id'].' ~ '.$tmp['color'].' ~ '.$tmp['qty'].' ~ '.$tmp['ap_mark'].' ~ '.$tmp['size'].'<br>';
	if ( $tmp['arrange'] == '1' ) {
	
		// ====== 1 ======
		
		// echo $tmp['bom_id'].' ~ '.$tmp['est_1'].' ~ '.$tmp['arrange'].' ~ '.$tmp['ap_mark'].' ~ '.$tmp['size'].'<br>';
		// echo $tmp['bom_qty'].'<br>';
		$qty_str = ( $qty1[$tmp['size']] * $tmp['est_1'] );
		$qty_str2 = $qty1[$tmp['size']];
		// echo $qty_str.'<p>';	
	} elseif ( $tmp['arrange'] == '2' ) {
	
		// ====== 2 ======
		
		// echo $tmp['bom_id'].' ~ '.$tmp['est_1'].' ~ '.$tmp['arrange'].' ~ '.$tmp['ap_mark'].' ~ '.$tmp['size'].'<br>';
		// echo $tmp['bom_qty'].'<br>';
		$qty_arr = explode(',',$tmp['bom_qty']);
		
		$qty_str = '';
		$qty_str2 = '';

		for( $i=0; $i < count($qty_arr); $i++ ){
			$qty_str .= ( $i > 0 )? ',':'';
			$qty_str2 .= ( $i > 0 )? ',':'';
			// echo $qty_arr[$i].' ~ '.$size_arr[$i].' ~ '.$tmp['size'].'<br>';
			if( !empty($qty_arr[$i]) ) {
				// echo $qty0[$i].' * '.$tmp['est_1'].' = '.($qty0[$i]*$tmp['est_1']).' ~ '.$qty_arr[$i].'<br>';
				// echo $qty_arr[$i].'<br>';
				// echo $qty2[$tmp['size']][$i].'<br>';
				$qty_str .= ( ($qty2[$tmp['size']][$i]*$tmp['est_1']) >= 0 ) ? ($qty2[$tmp['size']][$i]*$tmp['est_1']) : '0' ;
				$qty_str2 .= ( !empty($qty2[$tmp['size']][$i]) ) ? ($qty2[$tmp['size']][$i]) : '0' ;
			} else {
				// echo $qty_arr[$i].'<br>';
				$qty_str .= '0' ;
				$qty_str2 .= '0' ;
			}
		}
		// echo $qty_str.'<p>';
	} elseif ( $tmp['arrange'] == '3' ) {
	
		// ====== 3 ======
		
		// echo $tmp['bom_id'].' ~ '.$tmp['est_1'].' ~ '.$tmp['arrange'].' ~ '.$tmp['ap_mark'].' ~ '.$tmp['size'].'<br>';
		// echo $tmp['bom_qty'].'<br>';
		$qty_str = $qty3*$tmp['est_1'];
		$qty_str2 = $qty3;
		// echo $qty_str.'<p>';	
	} else {
		// echo $tmp['bom_id'].' ~ '.$tmp['est_1'].' ~ '.$tmp['arrange'].' ~ '.$tmp['ap_mark'].' ~ '.$tmp['size'].'<br>';
		// echo $tmp['bom_qty'].'<br>';
		$qty_arr = explode(',',$tmp['bom_qty']);
		
		$qty_str = '';
		$qty_str2 = '';
		for( $i=0; $i < count($qty_arr); $i++ ){
			$qty_str .= ( $i > 0 )? ',':'';
			$qty_str2 .= ( $i > 0 )? ',':'';
			if(!empty($qty_arr[$i])) {
				// echo $qty[$i].' * '.$tmp['est_1'].' = '.($qty[$i]*$tmp['est_1']).' ~ '.$qty_arr[$i].'<br>';
				$qty_str .= ( ($qty0[$i]*$tmp['est_1']) >= 0 ) ? ($qty0[$i]*$tmp['est_1']) : '0' ;
				$qty_str2 .= ( !empty($qty0[$i]) ) ? ($qty0[$i]) : '0' ;
			} else {
				$qty_str .= '0' ;
				$qty_str2 .= '0' ;
			}
		}
		// echo $qty_str.'<p>';
	}
	// echo $tmp['bom_id'].' ~ '.$qty_str.'<br>';
	$q_sql = "UPDATE `bom_acc` SET `qty` = '".$qty_str."' , `o_qty` = '".$qty_str2."' WHERE `id` = '".$tmp['bom_id']."' ;";
	// echo $q_sql.'<br>';
	$q_res = mysql_query($q_sql);	
	
	// $qty_ttl = array_sum(explode(',',$qty_str));
	// echo $qty_ttl.'<p>';
}


$ord['id'];




exit;













$sql = "SELECT * FROM `s_order` WHERE `opendate` LIKE '2004%';";
$ores = mysql_query($sql);
while($ord = mysql_fetch_array($ores)){
	// if ( $ord['order_num'] == 'ABK0-288' ){
	echo $ord['order_num'].'<br>';
	$sql = "SELECT * FROM `size_des` WHERE `id` = '".$ord['size']."';";
	$res = mysql_query($sql);
	$size = mysql_fetch_array($res);
	$size = $size['size'];
	$size_arr = explode(',',$size);
// print_r($size_arr);
	$sql = "SELECT * FROM `wi` WHERE `wi_num` = '".$ord['order_num']."';";
	$res = mysql_query($sql);
	$wi = mysql_fetch_array($res);

	$sql = "SELECT * FROM `wiqty` WHERE `wi_id` = '".$wi['id']."' ORDER BY `id` ASC  ;";
	$res = mysql_query($sql);
	$qty0 = $qty1 = $qty2 = $qty3 = $wi_id = array();
	$qty0_id = $qty1_id = $qty2_id = $qty3_id = $wi_id = array();
	$m = 0;
	while($tmp = mysql_fetch_array($res)){
		// echo $tmp['id'].' ~ '.$tmp['lots_used_id'].' ~ '.$tmp['color'].' ~ '.$tmp['qty'].' ~ '.$tmp['ap_mark'].'<br>';
		

		$qty_arr = explode(',',$tmp['qty']);
		$qty0[] = array_sum($qty_arr);
		$qty0_id[] = $tmp['id'];
		
		for( $i=0; $i < count($size_arr); $i++ ){
			// echo $m;
			$qty1[$size_arr[$i]] = @$qty1[$size_arr[$i]] + @$qty_arr[$i];
			$qty1_id[$size_arr[$i]] = $tmp['id'];
			
			$qty2[$size_arr[$i]][$m] = @$qty2[$size_arr[$i]][$m] + @$qty_arr[$i];
			$qty2_id[$size_arr[$i]][$m] = $tmp['id'];
			// echo $size_arr[$i].'<br>';
			// $qty1 = 
		}
		$m++;
		// echo 'QTY = '.$qty.'<br>';
		// echo 'QTY = '.$tmp['qty'].'<br>';
	}
	// echo '<p>';

	$qty3 = array_sum($qty1);

	// print_r($qty0);
	// echo '<p>';
	// print_r($qty1);
	// echo '<p>';
	// print_r($qty2);
	// echo '<p>';
	// print_r($qty3);
	// echo '<p>';
	// print_r($size);



	// echo '<p>';
	// print_r($qty0_id);

	// echo '<p>';
	// print_r($size_arr);


	# 抓主料
	$sql = "
	SELECT 
	`bom_lots`.`id` as `bom_id` , `bom_lots`.`qty` as `bom_qty` , `bom_lots`.`ap_mark` ,
	`lots_use`.`est_1` 

	FROM 
	`bom_lots` LEFT JOIN `lots_use` ON ( `bom_lots`.`lots_used_id` = `lots_use`.`id` ) 

	WHERE `bom_lots`.`wi_id` = '".$wi['id']."' AND `bom_lots`.`dis_ver` = '0' 
	 
	ORDER BY `bom_lots`.`id`
	;";

	$res = mysql_query($sql);
	$m = 0;
	while($tmp = mysql_fetch_array($res)){
		// echo $tmp['bom_id'].' ~ '.$tmp['est_1'].' ~ '.$tmp['ap_mark'].'<br>';
		// echo $tmp['bom_qty'].'<br>';
		
		$qty_arr = explode(',',$tmp['bom_qty']);
		
		$qty_str = '';
		for( $i=0; $i < count($qty_arr); $i++ ){
			$qty_str .= ( $i > 0 )? ',':'';
			if(!empty($qty_arr[$i])) {
				// echo $qty0[$i].' * '.$tmp['est_1'].' = '.($qty0[$i]*$tmp['est_1']).' ~ '.$qty_arr[$i].'<br>';
				// $qty_str .= ( ($qty0[$i]*$tmp['est_1'])  > 0 ) ? $qty0[$i]*$tmp['est_1'] : '0' ;
				$qty_str .= ( ($qty0[$i]*$tmp['est_1'])  > 0 ) ? $qty0[$i] : '0' ;
			} else {
				$qty_str .= '0' ;
			}
			// $qty_id = $qty0_id[$i];
			// $q_sql = "UPDATE `bom_lots` SET `qty_id` = '".$qty0_id[$m]."' WHERE `id` = '".$tmp['bom_id']."' ;";
			// echo $q_sql.$qty0_id[$i].'<br>';		
		}
		// echo $qty_str.'<p>';
		// echo $tmp['bom_id'].' ~ '.$qty_str.'<br>';
		$q_sql = "UPDATE `bom_lots` SET `o_qty` = '".$qty_str."' WHERE `id` = '".$tmp['bom_id']."' ;";
		// echo $q_sql.'<br>';
		$q_res = mysql_query($q_sql);
		// $qty_ttl = array_sum(explode(',',$qty_str));
		// echo $qty_ttl.'<p>';	
		$m++;
	}


	// echo '<p>';


	# 抓副料
	$sql = "
	SELECT 
	`bom_acc`.`id` as `bom_id` , `bom_acc`.`qty` as `bom_qty` , `bom_acc`.`ap_mark` , `bom_acc`.`size` ,
	`acc_use`.`est_1` , 
	`acc`.`arrange`

	FROM 
	`bom_acc` LEFT JOIN `acc_use` ON ( `bom_acc`.`acc_used_id` = `acc_use`.`id` ) 
	LEFT JOIN `acc` ON ( `acc_use`.`acc_code` = `acc`.`acc_code` ) 

	WHERE `bom_acc`.`wi_id` = '".$wi['id']."' AND `bom_acc`.`dis_ver` = '0' 
	 
	ORDER BY `bom_acc`.`id`
	;";

	$res = mysql_query($sql);
	while($tmp = mysql_fetch_array($res)){
		// echo $tmp['bom_id'].' ~ '.$tmp['est_1'].' ~ '.$tmp['arrange'].' ~ '.$tmp['ap_mark'].' ~ '.$tmp['size'].'<br>';
		// echo $tmp['bom_qty'].'<br>';
		// echo $tmp['id'].' ~ '.$tmp['acc_used_id'].' ~ '.$tmp['color'].' ~ '.$tmp['qty'].' ~ '.$tmp['ap_mark'].' ~ '.$tmp['size'].'<br>';
		if ( $tmp['arrange'] == '1' ) {
			// echo $tmp['bom_id'].' ~ '.$tmp['est_1'].' ~ '.$tmp['arrange'].' ~ '.$tmp['ap_mark'].' ~ '.$tmp['size'].'<br>';
			// echo $tmp['bom_qty'].'<br>';
			// $qty_str = ( $qty1[$tmp['size']] * $tmp['est_1'] );
			if( !empty($qty1[$tmp['size']]) )
				$qty_str = ( $qty1[$tmp['size']] );
			else 
				$qty_str = $qty3;
			// echo $qty_str.'<p>';	
		} elseif ( $tmp['arrange'] == '2' ) {
			// echo $tmp['bom_id'].' ~ '.$tmp['est_1'].' ~ '.$tmp['arrange'].' ~ '.$tmp['ap_mark'].' ~ '.$tmp['size'].'<br>';
			// echo $tmp['bom_qty'].'<br>';
			$qty_arr = explode(',',$tmp['bom_qty']);
			
			$qty_str = '';

			for( $i=0; $i < count($qty_arr); $i++ ){
				$qty_str .= ( $i > 0 )? ',':'';
				// echo $qty_arr[$i].' ~ '.$size_arr[$i].' ~ '.$tmp['size'].'<br>';
				if( !empty($qty_arr[$i]) ) {
					// echo $qty0[$i].' * '.$tmp['est_1'].' = '.($qty0[$i]*$tmp['est_1']).' ~ '.$qty_arr[$i].'<br>';
					// echo $qty_arr[$i].'<br>';
					// echo $qty2[$tmp['size']][$i].'<br>';
					// $qty_str .= ( ($qty2[$tmp['size']][$i]*$tmp['est_1']) >= 0 ) ? ($qty2[$tmp['size']][$i]*$tmp['est_1']) : '0' ;
					$qty_str .= ( (@$qty2[$tmp['size']][$i]*@$tmp['est_1']) >= 0 ) ? (@$qty2[$tmp['size']][$i]) : '0' ;
				} else {
					// echo $qty_arr[$i].'<br>';
					$qty_str .= '0' ;
				}
			}
			// echo $qty_str.'<p>';
		} elseif ( $tmp['arrange'] == '3' ) {
			// echo $tmp['bom_id'].' ~ '.$tmp['est_1'].' ~ '.$tmp['arrange'].' ~ '.$tmp['ap_mark'].' ~ '.$tmp['size'].'<br>';
			// echo $tmp['bom_qty'].'<br>';
			// $qty_str = $qty3*$tmp['est_1'];
			$qty_str = $qty3;
			// echo $qty_str.'<p>';	
		} else {
			// echo $tmp['bom_id'].' ~ '.$tmp['est_1'].' ~ '.$tmp['arrange'].' ~ '.$tmp['ap_mark'].' ~ '.$tmp['size'].'<br>';
			// echo $tmp['bom_qty'].'<br>';
			$qty_arr = explode(',',$tmp['bom_qty']);
			
			$qty_str = '';
			for( $i=0; $i < count($qty_arr); $i++ ){
				$qty_str .= ( $i > 0 )? ',':'';
				if(!empty($qty_arr[$i])) {
					// echo $qty[$i].' * '.$tmp['est_1'].' = '.($qty[$i]*$tmp['est_1']).' ~ '.$qty_arr[$i].'<br>';
					// $qty_str .= ( ($qty0[$i]*$tmp['est_1']) >= 0 ) ? ($qty0[$i]*$tmp['est_1']) : '0' ;
					$qty_str .= ( ($qty0[$i]*$tmp['est_1']) >= 0 ) ? ($qty0[$i]) : '0' ;
				} else {
					$qty_str .= '0' ;
				}
			}
			// echo $qty_str.'<p>';
		}
		// echo $tmp['bom_id'].' ~ '.$qty_str.'<br>';
		$q_sql = "UPDATE `bom_acc` SET `o_qty` = '".$qty_str."' WHERE `id` = '".$tmp['bom_id']."' ;";
		// echo $q_sql.'<br>';
		$q_res = mysql_query($q_sql);	
		
		// $qty_ttl = array_sum(explode(',',$qty_str));
		// echo $qty_ttl.'<p>';
	}
// }
}
// $ord['id'];

// print_r($ord);





exit;









# 修改 saw su
$ord = array();
$sel_sql="select order_num,ie1 from s_order where dept='DA'";
$res = mysql_query($sel_sql);
while($value = mysql_fetch_array($res)){
	$ord[$value['order_num']] = $value['ie1'];
}

// print_r($ord);
$sel_sql="select id,ord_num,qty,su from saw_out_put where `ord_num` LIKE 'D%' ";
$res = mysql_query($sel_sql);
while($value = mysql_fetch_array($res)){
		
	$su = ( $value['qty'] * $ord[$value['ord_num']] );
	$new_sql_str = " update saw_out_put set `su` = '".$su."' WHERE `id` = '".$value['id']."' ";
	mysql_query($new_sql_str);
	echo $new_sql_str.'<br>';
}

exit;






// error_reporting(1);



function myutf8_detect_encoding($string, $default = 'UTF-8', $encode = 0, $encode_to = 'UTF-8') { 
	static $list = array('UTF-8', 'ISO-8859-1', 'ASCII', 'windows-1250', 'windows-1251', 'latin1', 'windows-1252', 'windows-1253', 'windows-1254', 'windows-1255', 'windows-1256', 'windows-1257', 'windows-1258', 'ISO-8859-2', 'ISO-8859-3', 'GBK', 'GB2312', 'GB18030', 'MACROMAN', 'ISO-8859-4', 'ISO-8859-5', 'ISO-8859-6', 'ISO-8859-7', 'ISO-8859-8', 'ISO-8859-9', 'ISO-8859-10', 'ISO-8859-11', 'ISO-8859-12', 'ISO-8859-13', 'ISO-8859-14', 'ISO-8859-15', 'ISO-8859-16');

	foreach ($list as $item) {
		$sample = iconv($item, $item, $string);
		if (md5($sample) == md5($string)) {
			if ($encode == 1)
				return iconv($item, $encode_to, $string);
			else
				return $item;
		}
	}

	if ($encode == 1)
		return iconv($encode_to, $encode_to . '//IGNORE', $string);
	else
		return $default;
}
// print_r($_SESSION["link_db"]);
// print_r($_SESSION["Select_DB"]);



$ORDER = 'DDB12-0886';
$ORDER = 'BLR12-0798';
$ORDER = 'BBT12-0947';
$ORDER = 'BCH12-0826';
$ORDER = 'BLR12-0880';




$ORDER = 'DLS12-0934';
$ORDER = 'ABK12-0754';
// $ORDER = 'DLS12-0935';
// $ORDER = 'DLS12-0936';
// $ORDER = 'DLS12-0937';
// $ORDER = 'DLS12-0938';
// $ORDER = 'DLS12-0939';
// $ORDER = 'DLS12-0940';
// $ORDER = 'DLS12-0941';
// $ORDER = 'DLS12-0942';
// $ORDER = 'DLS12-0943';
// $ORDER = 'DLS12-0944';
// $ORDER = 'DLS12-0945';

$sql = "SELECT * FROM `s_order` WHERE `order_num` = '".$ORDER."';";
$res = mysql_query($sql);
$ord = mysql_fetch_array($res);

$sql = "SELECT * FROM `size_des` WHERE `id` = '".$ord['size']."';";
$res = mysql_query($sql);
$size = mysql_fetch_array($res);
$size = $size['size'];
$size_arr = explode(',',$size);

$sql = "SELECT * FROM `wi` WHERE `wi_num` = '".$ORDER."';";
$res = mysql_query($sql);
$wi = mysql_fetch_array($res);

$sql = "SELECT * FROM `wiqty` WHERE `wi_id` = '".$wi['id']."' ORDER BY `id` ASC  ;";
$res = mysql_query($sql);
$qty0 = $qty1 = $qty2 = $qty3 = $wi_id = array();
$qty0_id = $qty1_id = $qty2_id = $qty3_id = $wi_id = array();
$m = 0;
while($tmp = mysql_fetch_array($res)){
	// echo $tmp['id'].' ~ '.$tmp['lots_used_id'].' ~ '.$tmp['color'].' ~ '.$tmp['qty'].' ~ '.$tmp['ap_mark'].'<br>';
	

	$qty_arr = explode(',',$tmp['qty']);
	$qty0[] = array_sum($qty_arr);
	$qty0_id[] = $tmp['id'];
	
	for( $i=0; $i < count($size_arr); $i++ ){
		// echo $m;
		$qty1[$size_arr[$i]] = @$qty1[$size_arr[$i]] + $qty_arr[$i];
		$qty1_id[$size_arr[$i]] = $tmp['id'];
		
		$qty2[$size_arr[$i]][$m] = @$qty2[$size_arr[$i]][$m] + $qty_arr[$i];
		$qty2_id[$size_arr[$i]][$m] = $tmp['id'];
		// echo $size_arr[$i].'<br>';
		// $qty1 = 
	}
	$m++;
	// echo 'QTY = '.$qty.'<br>';
	// echo 'QTY = '.$tmp['qty'].'<br>';
}
echo '<p>';

$qty3 = array_sum($qty1);

// print_r($qty0);
// echo '<p>';
// print_r($qty1);
// echo '<p>';
// print_r($qty2);
// echo '<p>';
// print_r($qty3);
// echo '<p>';
// print_r($size);



// echo '<p>';
// print_r($qty0_id);

// echo '<p>';
// print_r($size_arr);


# 抓主料
$sql = "
SELECT 
`bom_lots`.`id` as `bom_id` , `bom_lots`.`qty` as `bom_qty` , `bom_lots`.`ap_mark` ,
`lots_use`.`est_1` 

FROM 
`bom_lots` LEFT JOIN `lots_use` ON ( `bom_lots`.`lots_used_id` = `lots_use`.`id` ) 

WHERE `bom_lots`.`wi_id` = '".$wi['id']."'
 
ORDER BY `bom_lots`.`id`
;";

$res = mysql_query($sql);
$m = 0;
while($tmp = mysql_fetch_array($res)){
	// echo $tmp['bom_id'].' ~ '.$tmp['est_1'].' ~ '.$tmp['ap_mark'].'<br>';
	// echo $tmp['bom_qty'].'<br>';
	
	$qty_arr = explode(',',$tmp['bom_qty']);
	
	$qty_str = '';
	for( $i=0; $i < count($qty_arr); $i++ ){
		$qty_str .= ( $i > 0 )? ',':'';
		if(!empty($qty_arr[$i])) {
			// echo $qty0[$i].' * '.$tmp['est_1'].' = '.($qty0[$i]*$tmp['est_1']).' ~ '.$qty_arr[$i].'<br>';
			$qty_str .= ( ($qty0[$i]*$tmp['est_1'])  > 0 ) ? $qty0[$i]*$tmp['est_1'] : '0' ;
		} else {
			$qty_str .= '0' ;
		}
		// $qty_id = $qty0_id[$i];
		// $q_sql = "UPDATE `bom_lots` SET `qty_id` = '".$qty0_id[$m]."' WHERE `id` = '".$tmp['bom_id']."' ;";
		// echo $q_sql.$qty0_id[$i].'<br>';		
	}
	// echo $qty_str.'<p>';
	// echo $tmp['bom_id'].' ~ '.$qty_str.'<br>';
	$q_sql = "UPDATE `bom_lots` SET `qty` = '".$qty_str."' WHERE `id` = '".$tmp['bom_id']."' ;";
	echo $q_sql.'<br>';
	$q_res = mysql_query($q_sql);
	// $qty_ttl = array_sum(explode(',',$qty_str));
	// echo $qty_ttl.'<p>';	
	$m++;
}


echo '<p>';


# 抓副料
$sql = "
SELECT 
`bom_acc`.`id` as `bom_id` , `bom_acc`.`qty` as `bom_qty` , `bom_acc`.`ap_mark` , `bom_acc`.`size` ,
`acc_use`.`est_1` , 
`acc`.`arrange`

FROM 
`bom_acc` LEFT JOIN `acc_use` ON ( `bom_acc`.`acc_used_id` = `acc_use`.`id` ) 
LEFT JOIN `acc` ON ( `acc_use`.`acc_code` = `acc`.`acc_code` ) 

WHERE `bom_acc`.`wi_id` = '".$wi['id']."'
 
ORDER BY `bom_acc`.`id`
;";

$res = mysql_query($sql);
while($tmp = mysql_fetch_array($res)){
	// echo $tmp['bom_id'].' ~ '.$tmp['est_1'].' ~ '.$tmp['arrange'].' ~ '.$tmp['ap_mark'].' ~ '.$tmp['size'].'<br>';
	// echo $tmp['bom_qty'].'<br>';
	// echo $tmp['id'].' ~ '.$tmp['acc_used_id'].' ~ '.$tmp['color'].' ~ '.$tmp['qty'].' ~ '.$tmp['ap_mark'].' ~ '.$tmp['size'].'<br>';
	if ( $tmp['arrange'] == '1' ) {
		// echo $tmp['bom_id'].' ~ '.$tmp['est_1'].' ~ '.$tmp['arrange'].' ~ '.$tmp['ap_mark'].' ~ '.$tmp['size'].'<br>';
		// echo $tmp['bom_qty'].'<br>';
		$qty_str = ( $qty1[$tmp['size']] * $tmp['est_1'] );
		// echo $qty_str.'<p>';	
	} elseif ( $tmp['arrange'] == '2' ) {
		// echo $tmp['bom_id'].' ~ '.$tmp['est_1'].' ~ '.$tmp['arrange'].' ~ '.$tmp['ap_mark'].' ~ '.$tmp['size'].'<br>';
		// echo $tmp['bom_qty'].'<br>';
		$qty_arr = explode(',',$tmp['bom_qty']);
		
		$qty_str = '';

		for( $i=0; $i < count($qty_arr); $i++ ){
			$qty_str .= ( $i > 0 )? ',':'';
			// echo $qty_arr[$i].' ~ '.$size_arr[$i].' ~ '.$tmp['size'].'<br>';
			if( !empty($qty_arr[$i]) ) {
				// echo $qty0[$i].' * '.$tmp['est_1'].' = '.($qty0[$i]*$tmp['est_1']).' ~ '.$qty_arr[$i].'<br>';
				// echo $qty_arr[$i].'<br>';
				// echo $qty2[$tmp['size']][$i].'<br>';
				$qty_str .= ( ($qty2[$tmp['size']][$i]*$tmp['est_1']) >= 0 ) ? ($qty2[$tmp['size']][$i]*$tmp['est_1']) : '0' ;
			} else {
				// echo $qty_arr[$i].'<br>';
				$qty_str .= '0' ;
			}
		}
		// echo $qty_str.'<p>';
	} elseif ( $tmp['arrange'] == '3' ) {
		// echo $tmp['bom_id'].' ~ '.$tmp['est_1'].' ~ '.$tmp['arrange'].' ~ '.$tmp['ap_mark'].' ~ '.$tmp['size'].'<br>';
		// echo $tmp['bom_qty'].'<br>';
		$qty_str = $qty3*$tmp['est_1'];
		// echo $qty_str.'<p>';	
	} else {
		// echo $tmp['bom_id'].' ~ '.$tmp['est_1'].' ~ '.$tmp['arrange'].' ~ '.$tmp['ap_mark'].' ~ '.$tmp['size'].'<br>';
		// echo $tmp['bom_qty'].'<br>';
		$qty_arr = explode(',',$tmp['bom_qty']);
		
		$qty_str = '';
		for( $i=0; $i < count($qty_arr); $i++ ){
			$qty_str .= ( $i > 0 )? ',':'';
			if(!empty($qty_arr[$i])) {
				// echo $qty[$i].' * '.$tmp['est_1'].' = '.($qty[$i]*$tmp['est_1']).' ~ '.$qty_arr[$i].'<br>';
				$qty_str .= ( ($qty0[$i]*$tmp['est_1']) >= 0 ) ? ($qty0[$i]*$tmp['est_1']) : '0' ;
			} else {
				$qty_str .= '0' ;
			}
		}
		// echo $qty_str.'<p>';
	}
	// echo $tmp['bom_id'].' ~ '.$qty_str.'<br>';
	$q_sql = "UPDATE `bom_acc` SET `qty` = '".$qty_str."' WHERE `id` = '".$tmp['bom_id']."' ;";
	echo $q_sql.'<br>';
	$q_res = mysql_query($q_sql);	
	
	// $qty_ttl = array_sum(explode(',',$qty_str));
	// echo $qty_ttl.'<p>';
}


$ord['id'];

// print_r($ord);





exit;




# 修改工繳
$sel_sql="select id,order_num,qty,ie_time1,ie_time2,ie1,ie2,su,cm,fty_cm from s_order where factory='CF'";
$res = mysql_query($sel_sql);
while($value = mysql_fetch_array($res)){
		
	echo " ie_time1 = ".$value['ie_time1']." ie_time2 = ".$value['ie_time1']." ie1 = ".$value['ie1']." ie2 = ".$value['ie2']." qty = ".$value['qty']." cm = ".$value['cm']." fty_cm = ".$value['fty_cm'].'<br>'; 
	$ie1 = (!empty($value['ie1']) && $value['ie1'] > 0 )? ( $value['cm'] / 2.45 ) : 0 ;
	$ie2 = (!empty($value['ie2']) && $value['ie2'] > 0 )? ( $value['fty_cm'] / 2.45 ) : 0 ;
	$ie_time1 = (!empty($value['ie_time1']))? $ie1 * 3000 : 0;
	$ie_time2 = (!empty($value['ie_time2']))? $ie2 * 3000 : 0;
	$su = $ie1* $value['qty'];
	
	
	$ie1 = round($ie1, 2);
	$ie2 = round($ie2, 2);
	$ie_time1 = round($ie_time1);
	$ie_time2 = round($ie_time2);
	$su = round($su);

	
	$new_sql_str = " update s_order set `ie_time1` = '".$ie_time1."' , `ie_time2` = '".$ie_time2."' ,
	`ie1` = '".$ie1."' , `ie2` = '".$ie2."' , `su` = '".$su."'
	WHERE `id` = '".$value['id']."'
	";
	mysql_query($new_sql_str);
	echo $new_sql_str.'<br>';

}
exit;




# 修改工繳 order_partial
echo $sel_sql="
select 
s_order.order_num,s_order.ie1,order_partial.ord_num,order_partial.id,order_partial.p_qty,order_partial.p_su , (s_order.ie1 * order_partial.p_qty ) as su

from 
s_order,order_partial 

where 
s_order.factory='CF' AND s_order.order_num = order_partial.ord_num AND s_order.ie1 > 0 ";
$res = mysql_query($sel_sql);
while($value = mysql_fetch_array($res)){
		
	echo " order_num = ".$value['order_num']." ie1 = ".$value['ie1']." ord_num = ".$value['ord_num']." id = ".$value['id']." p_su = ".$value['p_su']." su = ".$value['su'].'<br>'; 
	$new_sql_str = " update order_partial set `p_su` = '".$value['su']."' WHERE `id` = '".$value['id']."'; ";
	mysql_query($new_sql_str);
	echo $new_sql_str.'<br>';

}
exit;





$ndate = '2012-09-27';
$rdate = '2012-09-26';


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










$sql = "SELECT `ap_special`.`ap_num`,`ap_special`.`ord_num`,`ap_special`.`mat_code`,`ap_special`.`color`,`ap_special`.`mat_cat`
FROM `ap`,`ap_special` WHERE `ap`.`status` = '12' AND `ap`.`special` = '2' AND `ap`.`ap_num` = `ap_special`.`ap_num`
;";

$res = mysql_query($sql);
while($tmp = mysql_fetch_array($res)){
	
	$sql2 = "SELECT `wi`.`id` FROM `wi` WHERE `wi`.`wi_num` = '".($tmp['ord_num'])."' ;";
	$res2 = mysql_query($sql2);
	$tmp2 = mysql_fetch_array($res2);
	
	$wi = $tmp2['id'];
		
	if( $tmp['mat_cat'] == 'l' )
	$sql2 = "SELECT `lots_use`.`id` FROM `lots_use` WHERE `lots_use`.`smpl_code` = '".($tmp['ord_num'])."' AND `lots_use`.`lots_code` = '".($tmp['mat_code'])."' ;";
	else 
	$sql2 = "SELECT `acc_use`.`id` FROM `acc_use` WHERE `acc_use`.`smpl_code` = '".($tmp['ord_num'])."' AND `acc_use`.`acc_code` = '".($tmp['mat_code'])."' ;";
	
	$res2 = mysql_query($sql2);
	$tmp2 = mysql_fetch_array($res2);
	
	$acc_use_id = $tmp2['id'];
	
	echo $acc_use_id.' ~ '.$tmp['ap_num'].'<br>';
	if( $tmp['mat_cat'] == 'l' ){
		$sql2 = "SELECT DISTINCT `bom_lots`.`id`
		FROM `bom_lots` 
		WHERE 
		`bom_lots`.`wi_id` = '".($wi)."' AND 
		`bom_lots`.`lots_used_id` = `lots_use`.`id` AND 
		
		`bom_lots`.`ap_mark` = '".($tmp['ap_num'])."' AND 
		
		`wi`.`wi_num` = '".($tmp['ord_num'])."' AND 
		
		
		`lots_use`.`smpl_code` = '".($tmp['ord_num'])."' AND 
		`lots_use`.`lots_code` = '".($tmp['mat_code'])."' AND 
		
		
		`bom_lots`.`color` = '".($tmp['color'])."'
		;";
	} else {
		$sql2 = "SELECT DISTINCT `bom_acc`.`id`
		FROM `bom_acc` , `acc_use` , `s_order` , `acc` , `wi` 
		WHERE `bom_acc`.`ap_mark` = '".($tmp['ap_num'])."' AND 
		
		`wi`.`wi_num` = '".($tmp['ord_num'])."' AND 
		`bom_acc`.`wi_id` = `wi`.`id` AND 
		
		`acc_use`.`smpl_code` = '".($tmp['ord_num'])."' AND 
		`acc_use`.`lots_code` = '".($tmp['mat_code'])."' AND 
		`bom_acc`.`acc_used_id` = `acc_use`.`id` AND 
		
		`bom_acc`.`color` = '".($tmp['color'])."'
		;";
	}

	   
    // $res2 = mysql_query($sql2);
		
    // while($tmp2 = mysql_fetch_array($res2)){
		// echo $tmp2['id'].'<br>';    
        // $sql3 = "UPDATE `remun_det` SET `cost` = '".$tmp2['fty_cm']."' WHERE id = '".$tmp['id']."' ;";
        // $res3 = mysql_query($sql3);
        // echo $tmp['num'].' ~ '.$tmp['ord_num'].' ~ '.$tmp2['fty_cm'].'<br>';
        // echo $sql3.'<br>';    
    // }
}


exit;











echo '<meta http-equiv="Content-Type" content="text/html; charset=utf8">';
// $target = file('data_output(0801_0810).txt');
$target = file('data_output(0811_0816).txt');

foreach ($target as $key => $Tgt){
	if($key > 0){
		$str = explode('	',$Tgt);
		// foreach($str as $p){
			// echo $p.' ';
		// }
		// echo '<br>';
		$q_str = "INSERT INTO `line_balance` SET 
		`workmanship_id` = '".$str[4]."' , 
		`workmanship_emp` = '".$str[3]."' , 
		`workmanship_name_tw` = '".$str[6]."' , 
		`workmanship_name_vn` = '".myutf8_detect_encoding(trim($str[5]), 'windows-1258', 1)."' , 
		`order_num` = '".$str[0]."' , 
		`emp_id` = '".$str[2]."' , 
		`ie` = '".$str[7]."' , 
		`w_date` = '".$str[9]."' , 
		`line` = '".substr($str[1],2)."' , 
		`qty` = '".$str[8]."' ,
		`fty` = 'LY'
		";
		// echo $q_str.'<br>';
		// $res = mysql_query($q_str);
	}
}

// mysql_query("SET NAMES utf8"); 
// $sql = "SELECT * FROM `line_balance`;";
// $res = mysql_query($sql);
// while($tmp = mysql_fetch_array($res)){
  // echo iconv($tmp['workmanship_name_vn'],'windows-1258','big5').','."<br>";
// }


exit;







# 更改異常報告修改工繳

$sql = "SELECT `exceptional`.`ex_num`,`exceptional`.`ord_num`,`exc_static`.`org_rec`
FROM `exceptional`,`exc_static` 
WHERE `exceptional`.`id` = `exc_static`.`exc_id` AND `exceptional`.`status` = '4' AND `exc_static`.`state` = '8'
;";
$res = mysql_query($sql);
while($tmp = mysql_fetch_array($res)){

    // echo $tmp['ex_num'].' ~ '.$tmp['org_rec'].'<br>';
     
    $sql3 = "UPDATE `remun_det` SET `cost` = '".$tmp['org_rec']."' WHERE `ord_num` = '".$tmp['ord_num']."' ;";
    $res3 = mysql_query($sql3);
    echo $sql3.'<br>';    
}




exit;

 
$sql = "SELECT `remun`.`num`,`remun_det`.`ord_num`,`remun_det`.`id`
FROM `remun`,`remun_det` 
WHERE `remun_det`.`rem_id` = `remun`.`id` 
;";
$res = mysql_query($sql);
while($tmp = mysql_fetch_array($res)){

    $sql2 = "SELECT `fty_cm`
    FROM `s_order` 
    WHERE `order_num` = '".($tmp['ord_num'])."' 
    ;";
    $res2 = mysql_query($sql2);
    while($tmp2 = mysql_fetch_array($res2)){
        $sql3 = "UPDATE `remun_det` SET `cost` = '".$tmp2['fty_cm']."' WHERE id = '".$tmp['id']."' ;";
        $res3 = mysql_query($sql3);
        echo $tmp['num'].' ~ '.$tmp['ord_num'].' ~ '.$tmp2['fty_cm'].'<br>';
        echo $sql3.'<br>';    
    }
}


exit;
# RECEIVE_DET 的 apb_mk 重新判斷
$sql = "UPDATE `receive_det` SET `apb_rmk` = '0' ;";
$res = mysql_query($sql);

$sql = "SELECT `apb_det`.`qty`,`apb_det`.`po_id` 
FROM `apb`,`apb_det` 
WHERE `apb`.`rcv_num` = `apb_det`.`rcv_num` AND `apb`.`status` = '2'
;";
$res = mysql_query($sql);
while($tmp = mysql_fetch_array($res)){
    $sql2 = "SELECT `id`,`qty` 
    FROM `receive_det` 
    WHERE `po_id` = '".($tmp['po_id'])."' AND `qty` = '".$tmp['qty']."'
    ;";
    $res2 = mysql_query($sql2);
    $r_qty = 0;
    $id = '';
    while($tmp2 = mysql_fetch_array($res2)){
        if (  $tmp['qty'] == $tmp2['qty'] ) {
            $sql3 = "UPDATE `receive_det` SET `apb_rmk` = '1' WHERE id = '".$tmp2['id']."' ;";
            $res3 = mysql_query($sql3);
            echo $sql3.'<br>';           
        }
    }
}

exit;



# AP 的 apb_mk 重新判斷
$sql = "SELECT `id`,`po_qty`,`po_spare` 
FROM `ap_det` 
;";
$res = mysql_query($sql);
while($tmp = mysql_fetch_array($res)){
    $sql2 = "SELECT `id`,`qty` 
    FROM `receive_det` 
    WHERE `po_id` = '".($tmp['po_spare'])."' 
    ;";
    // echo $sql2.'<br>';
    $res2 = mysql_query($sql2);
    $r_qty = 0;
    $id = '';
    while($tmp2 = mysql_fetch_array($res2)){
        $r_qty += ($tmp2['qty']);
    }
    
    $mk = ( $r_qty >= $tmp['po_qty'] ) ? '1' : '0' ;

    $sql3 = "UPDATE `ap_det` SET `apb_rmk` = '".($mk)."' WHERE id = '".$tmp['id']."';";
    $res3 = mysql_query($sql3);
    echo $sql3.'<br>';    
}

exit;















$sql = "SELECT `id`,`ap_num`,`dm_way` 
FROM `ap` 
WHERE 1 AND `dm_way` 
LIKE '月%'
;";

$res = mysql_query($sql);
while($tmp = mysql_fetch_array($res)){

    $sql2 = "SELECT `po_spare` 
    FROM `ap_special` 
    WHERE `ap_num` = '".($tmp['ap_num'])."' 
    ;";

    $res2 = mysql_query($sql2);
    while($tmp2 = mysql_fetch_array($res2)){
    
        $sql3 = "SELECT `ship_id` FROM `po_ship_det` WHERE `po_id` = '".($tmp2['po_spare'])."' ";
        $res3 = mysql_query($sql3);
        while($tmp3 = mysql_fetch_array($res3)){

            $sql4 = "UPDATE `po_ship` SET `tw_rcv` = '1' WHERE `id` = '".($tmp3['ship_id'])."';";
            $res4 = mysql_query($sql4);
            echo $sql4.'<br>';
            
        }
    }
}
    
exit;

$sql = "SELECT `id`,`ap_num`,`dm_way` 
FROM `ap` 
WHERE 1 AND `dm_way` 
LIKE '月%'
;";

$res = mysql_query($sql);
while($tmp = mysql_fetch_array($res)){

    $sql2 = "SELECT `po_spare` 
    FROM `ap_det` 
    WHERE `ap_num` = '".($tmp['ap_num'])."' 
    ;";

    $res2 = mysql_query($sql2);
    while($tmp2 = mysql_fetch_array($res2)){
    
        $sql3 = "SELECT `ship_id` FROM `po_ship_det` WHERE `po_id` = '".($tmp2['po_spare'])."' ";
        $res3 = mysql_query($sql3);
        while($tmp3 = mysql_fetch_array($res3)){

            $sql4 = "UPDATE `po_ship` SET `tw_rcv` = '1' WHERE `id` = '".($tmp3['ship_id'])."';";
            $res4 = mysql_query($sql4);
            echo $sql4.'<br>';
            
        }
    }
}
    
exit;

$sql = "SELECT `id`,`ap_num`,`dm_way` 
FROM `ap` 
WHERE 1 AND `dm_way` 
LIKE '月%'
;";

$res = mysql_query($sql);
while($tmp = mysql_fetch_array($res)){

    $sql2 = "UPDATE `receive` SET `tw_rcv` = '1' WHERE `po_id` = '".($tmp['id'])."';";
    $sql2 = mysql_query($sql2);
    echo $sql2.($tmp['dm_way']).'<br>';

}
exit;

# 驗收請款 補 ap / apb_rmk FOR before 0 2000 4000 6000 8000

$sql = "SELECT `id`,`ap_num` , `special`
FROM `ap` 
LIMIT 6000 , 2000 
;";

$res = mysql_query($sql);
while($tmp = mysql_fetch_array($res)){

    

    $sql2 = "SELECT `apb_rmk`
    FROM `".(($tmp['special'] == '2')?'ap_special':'ap_det')."` 
    WHERE `ap_num` = '".($tmp['ap_num'])."' 
    ;";

    $res2 = mysql_query($sql2);

    $ks = 0;
    while($tmp2 = mysql_fetch_array($res2)){
        $ct[] = $tmp2;
        if( $tmp2['apb_rmk'] != '1' )$ks++;
    }
    
    $sql3 = "UPDATE `ap` SET `apb_rmk` = '".( ($ks == 0 ) ?'1':'0')."' WHERE id = '".$tmp['id']."';";
    $res3 = mysql_query($sql3);
   
    echo $tmp['ap_num'].' ~ '.$ks.'<br>';
    // echo $sql2.' <br> '.$ks.'<br>';

}

exit;


exit;
# 驗收請款 補 receive
$sql = "SELECT `po_ship`.`num`,`po_ship_det`.`po_id`,`po_ship_det`.`qty`
FROM `po_ship` , `po_ship_det` 
WHERE `po_ship`.`id` = `po_ship_det`.`ship_id`
;";

$res = mysql_query($sql);
while($tmp = mysql_fetch_array($res)){

    $sql2 = "SELECT `receive`.`rcv_num`
    FROM `receive` , `receive_det` 
    WHERE `receive_det`.`po_id` = '".($tmp['po_id'])."'  AND `receive`.`rcv_num` = `receive_det`.`rcv_num` 
    ;";

    $res2 = mysql_query($sql2);
    $ct = array();
    $ks = 0;
    while($tmp2 = mysql_fetch_array($res2)){
        $rcv_num = $tmp2['rcv_num'];
        $sql3 = "UPDATE `receive` SET `ship_num` = '".($tmp['num'])."' WHERE `rcv_num` = '".$rcv_num."';";
        $res3 = mysql_query($sql3);
        echo $sql3.'<br>';
    }
}

exit;

$sql = "SELECT `po_ship`.`num`,`po_ship_det`.`po_id`,`po_ship_det`.`qty`
FROM `po_ship` , `po_ship_det` 
WHERE `po_ship`.`id` = `po_ship_det`.`ship_id`
;";
$res = mysql_query($sql);
$rcv_num = array();
while($tmp = mysql_fetch_array($res)){
    $sql2 = "SELECT `rcv_num`
    FROM `receive_det` 
    WHERE `po_id` = '".($tmp['po_id'])."' AND `qty` = '".($tmp['qty'])."' 
    ;";

    $res2 = mysql_query($sql2);
    
    while($tmp2 = mysql_fetch_array($res2)){
        $rcv_num[$tmp['num']] = $tmp2['rcv_num'];
    }

}


foreach($rcv_num as $key => $val) {
    $sql3 = "UPDATE `receive` SET `ship_num` = '".($key)."' WHERE `rcv_num` = '".$val."';";
    $res3 = mysql_query($sql3);
    echo $sql3.'<br>';
}

exit;

# 驗收請款 補 ap / apb_rmk FOR after
$sql = "SELECT `id`,`ap_num`
FROM `ap` 
LIMIT 6000 , 2000 
;";
$res = mysql_query($sql);
while($tmp = mysql_fetch_array($res)){
    $sql2 = "SELECT `apb_rmk`
    FROM `receive_det` 
    WHERE `ap_num` = '".($tmp['ap_num'])."' 
    ;";

    $res2 = mysql_query($sql2);
    $ct = array();
    $ks = 0;

    while($tmp2 = mysql_fetch_array($res2)){
        $ct[] = $tmp2;
        if( $tmp2['apb_rmk'] == '1' )$ks++;
    }
    
    $ct = count($ct);
    $sql3 = "UPDATE `ap` SET `apb_rmk` = '".( ($ks == $ct && $ks != 0 ) ?'1':'0')."' WHERE id = '".$tmp['id']."';";
    $res3 = mysql_query($sql3);
    echo $sql3.' ~ '.$ks.'='.$ct.'<br>';

}

exit;


# 驗收請款 補 ap_special / apb_rmk
$sql = "SELECT `id`
FROM `ap_special` 
;";
$res = mysql_query($sql);
while($tmp = mysql_fetch_array($res)){
    $sql2 = "SELECT `id`
    FROM `apb_det` 
    WHERE `po_id` = '".($tmp['id'])."' 
    ;";
    $res2 = mysql_query($sql2);
    while($tmp2 = mysql_fetch_array($res2)){
        $sql3 = "UPDATE `ap_special` SET `apb_rmk` = '1' WHERE id = '".$tmp['id']."';";
        $res3 = mysql_query($sql3);
        echo $sql3.'<br>';
    }
}

exit;


# 驗收請款 補 ap_det / apb_rmk
$sql = "SELECT `id`,`po_spare` 
FROM `ap_det` 
;";
$res = mysql_query($sql);
while($tmp = mysql_fetch_array($res)){
    $sql2 = "SELECT `id`
    FROM `apb_det` 
    WHERE `po_id` = '".($tmp['po_spare'])."' 
    ;";
    $res2 = mysql_query($sql2);
    while($tmp2 = mysql_fetch_array($res2)){
        $sql3 = "UPDATE `ap_det` SET `apb_rmk` = '1' WHERE id = '".$tmp['id']."';";
        $res3 = mysql_query($sql3);
        echo $sql3.'<br>';
    }
}

exit;

# 驗收請款 補 apb
$sql = "SELECT `id`,`po_id` 
FROM `apb_det` 
;";
$res = mysql_query($sql);
while($tmp = mysql_fetch_array($res)){
    $sql2 = "SELECT `id`,`rcv_num`,`sup_no`,`ap_num`
    FROM `receive_det` 
    WHERE `po_id` = '".($tmp['po_id'])."' 
    ;";
    $res2 = mysql_query($sql2);
    while($tmp2 = mysql_fetch_array($res2)){
        $sql3 = "UPDATE `apb_det` SET `ap_num` = '".$tmp2['ap_num']."' , `ship_num` = '".$tmp2['sup_no']."', `rcvd_num` = '".$tmp2['rcv_num']."'  WHERE id = '".$tmp['id']."';";
        $res3 = mysql_query($sql3);
        echo $sql3.'<br>';
    }
}

exit;


# 補 驗收請款 receive_det
$sql = "SELECT `id`,`po_id` 
FROM `apb_det` 
;";
$res = mysql_query($sql);
while($tmp = mysql_fetch_array($res)){
    $sql2 = "SELECT `id` 
    FROM `receive_det` 
    WHERE `po_id` = '".($tmp['po_id'])."' 
    ;";
    // echo $sql2.'<br>';
    $res2 = mysql_query($sql2);
    while($tmp2 = mysql_fetch_array($res2)){
        $sql3 = "UPDATE `apb_det` SET `apb_rmk` = '1' WHERE id = '".$tmp2['id']."';";
        $res3 = mysql_query($sql3);
        echo $sql3.'<br>';
    }
}
exit;


# 這個不要用
$sql = "SELECT `id`,`po_id` 
FROM `apb_det` 
;";
$res = mysql_query($sql);
while($tmp = mysql_fetch_array($res)){
    $sql2 = "SELECT `id` 
    FROM `receive_det` 
    WHERE `po_id` = '".($tmp['po_id'])."' 
    ;";
    echo $sql2.'<br>';
    // $res2 = mysql_query($sql2);
    // while($tmp2 = mysql_fetch_array($res2)){
        // $sql3 = "UPDATE `receive_det` SET `apb_rmk` = '1' WHERE id = '".$tmp2['id']."';";
        // $res3 = mysql_query($sql3);
        // echo $sql3.'<br>';
    // }
}

exit;

$sql = "SELECT `id`,`po_id` 
FROM `receive_det` 
;";
$res = mysql_query($sql);
while($tmp = mysql_fetch_array($res)){
    $sql2 = "SELECT `ap`.`sup_code`,`ap_det`.`ap_num` 
    FROM `ap`,`ap_det` 
    WHERE `ap_det`.`po_spare` = '".($tmp['po_id'])."' AND `ap`.`ap_num` = `ap_det`.`ap_num`
    ;";
    $res2 = mysql_query($sql2);
    while($tmp2 = mysql_fetch_array($res2)){
        $sql3 = "UPDATE `receive_det` SET `ap_num` = '".$tmp2['ap_num']."' , `sup_no` = '".$tmp2['sup_code']."'  WHERE id = '".$tmp['id']."';";
        $res3 = mysql_query($sql3);
        echo $sql3.'<br>';
    }
}
exit;



// $ap_num = 'PA12-0634';
// SELECT `ap_det`.`ap_num` , `ap_det`.`po_spare` , `po_ship_det`.`po_id` FROM `ap_det` , `po_ship_det` WHERE `ap_det`.`po_spare` != `po_ship_det`.`po_id` AND `po_ship_det`.`special` = '0' ;
// SELECT `ap_special`.`ap_num`, `ap_det`.`po_spare`  FROM `ap_special`, `po_ship_det` WHERE `ap_special`.`po_spare` = `po_ship_det`.`po_id`  AND `po_ship_det`.`special` = '1' ORDER BY `ap_special`.`ap_num` ASC ;
$sql = "SELECT `po_ship_det`.`ship_id` , `po_ship_det`.`po_id` 
FROM `po_ship_det` 
WHERE `po_ship_det`.`special` = '0' ;";

$res = mysql_query($sql);
// $mo = 1;
while($tmp = mysql_fetch_array($res)){

	// $sqls = "SELECT `ap_special`.`ap_num`,`ap_special`.`po_spare` FROM `ap_special` WHERE `ap_special`.`po_spare` = '".$tmp['po_id']."';";
	$sqls = "SELECT `ap_det`.`ap_num`,`ap_det`.`po_spare` FROM `ap_det` WHERE `ap_det`.`po_spare` = '".$tmp['po_id']."';";
    if($ress = mysql_query($sqls)){

        $total=mysql_num_rows($ress);//取得資料的總數 
        if($total=='0'){
            echo '<br>[ '.$tmp['po_id'].' ] ';
            $sqlss = "DELETE FROM `po_ship_det` WHERE `po_id`= '".$tmp['po_id']."';";
            mysql_query($sqlss);
        } else {
             $ppp = count(explode('|',$tmp['po_id']));
            if( $total != $ppp ) {
                echo '<br>[ '.$tmp['po_id'].' ] ';
            } else {
                // echo '<br>[ '.$ppp.' = ' .$total.' ] ';
            }
            // echo '<br>'.count($ppp);
            // while($tmps = mysql_fetch_array($ress)){
            // }
        }
    }
}


exit;











$ap_num = 'PA12-0634';

$sql = "SELECT `bom_id` , `mat_cat` 
FROM `ap_det` 
WHERE `ap_num` = '".$ap_num."' ;";

$res = mysql_query($sql);
$mo = 1;
while($tmp = mysql_fetch_array($res)){
	// echo '<br>[ '.$tmp['mat_cat'].' ] ';
	// $mo++;
	if ( $tmp['bom_id'] == 'lots' ) 
	$sqls = "UPDATE `lots_acc` SET `ap_mark` = '".$ap_num."' WHERE id = '".$tmp['bom_id']."';";
	else 
	$sqls = "UPDATE `bom_acc` SET `ap_mark` = '".$ap_num."' WHERE id = '".$tmp['bom_id']."';";
	
	// if($ress = mysql_query($sqls)){
		echo '<br>'.$sqls;
	// } else {
		// echo '<br>'.$tmp['bom_id'];
	// }
}












exit;
# 修改 CUST PO
$GLOBALS['config']['root_dir'] = dirname(__FILE__); 
$sql = "SELECT * FROM `s_order` WHERE 1 AND `cust_po` != '' ";
$res = mysql_query($sql);
while($tmp = mysql_fetch_array($res)){

	$atk = '';
	// echo $tmp['order_num'].$tmp['id'].$tmp['cust_po'].'<br>';
	// 檢查 customer po上傳檔
	if(file_exists($GLOBALS['config']['root_dir']."/ord_cust_po/".$tmp['order_num'].".pdf")){
		$atk = "pdf";
	}elseif(file_exists($GLOBALS['config']['root_dir']."/ord_cust_po/".$tmp['order_num'].".rar")){
		$atk = "rar";
	}elseif(file_exists($GLOBALS['config']['root_dir']."/ord_cust_po/".$tmp['order_num'].".zip")){
		$atk = "zip";
	} else {
		$atk = "";
	}	
	$tmp['cust_po'] = str_replace('/','|',$tmp['cust_po']);
	$tmp['cust_po'] = str_replace(',','|',$tmp['cust_po']);
	$tmp['cust_po'] = str_replace('.','|',$tmp['cust_po']);
	
	// echo $tmp['order_num'].'.'.$atk.'<br>';
	$cust_po = explode('|',$tmp['cust_po']);
	$mode = '';
	if( ( count($cust_po) > 1 ) && ( trim($tmp['cust_po']) <> "Array" ) ){
		foreach($cust_po as $k => $v){
			if( $k == '0' ) {
				if( !empty($atk) ) {
					$mode .= $v.'/'.$tmp['order_num'].'.'.$atk;
				} else {
					$mode .= $v.'/';
				}
			} else {
				$mode .= '|'.$v.'/';
			}
		}
	} else {
		if($atk)
			$mode .= $cust_po[0].'/'.$tmp['order_num'].'.'.$atk;
		else 
			$mode .= $cust_po[0].'/';
	}
	
	if ( $mode == 'Array/' ) $mode = '';
	
	$sqls = "UPDATE `s_order` SET `cust_po` = '".$mode."' WHERE id = '".$tmp['id']."'; ";
	echo $sqls.'<br>';
	// mysql_query($sqls);
}
echo 'Ok~';


exit;
# 修改 Schedule ETS - ETF

$sql = "SELECT `ord_num` , min( `out_date` ) AS min , max( `out_date` ) AS max , sum( `qty` ) AS qty , `p_id` , `s_id` 
FROM `saw_out_put` 
WHERE `holiday` != '1' AND `ord_num` != '' AND `s_id` != '' AND saw_fty = 'HJ'
GROUP BY `s_id` 
ORDER BY `min` ASC   ;";

// $sql = "SELECT * FROM saw_out_put WHERE saw_fty = 'LY' ;";
$res = mysql_query($sql);
$mo = 1;
while($tmp = mysql_fetch_array($res)){
	// echo '<br>[ '.$mo.' ] '.$tmp['ord_num'].' / '.$tmp['min'].' - '.$tmp['max'].' / '.$tmp['qty'].' [ '.$tmp['s_id'].' ] ';
	$mo++;
	$sqls = "UPDATE `schedule` SET `rel_ets` = '".$tmp['min']."' , `rel_etf` = '".$tmp['max']."' WHERE id = '".$tmp['s_id']."';";
	$ress = mysql_query($sqls);
	// echo '<br>'.$sqls;
}
echo 'Ok~';


exit;
# 修改 SU
echo '<br>修改 SU';
// exit;
// $sql = "SELECT * FROM `order_partial` WHERE 1 AND `ord_num` LIKE 'DTH1-%' AND `ext_qty` != 0 ;";
$sql = "SELECT * FROM `s_order` WHERE `order_num` LIKE 'DTH1-%' AND `ie_time2` != 0 ;";
$res = mysql_query($sql);
while($tmp = mysql_fetch_array($res)){
	
	$sql2 = "SELECT * FROM `order_partial` WHERE `ord_num` = '".$tmp['order_num']."' ;";
	// echo '<br>'.$sql2;
	// echo '<br>'.$tmp['order_num'];
	$res2 = mysql_query($sql2);
	while($tmp2 = mysql_fetch_array($res2)){
		// print_r($tmp2);
		// echo '<br>'.$tmp2['ext_qty'];
		$sql2 = "UPDATE `order_partial` SET `p_su` = '".$tmp2['p_qty']*$tmp['ie1']."' , `ext_su` = '".$tmp2['ext_qty']*$tmp['ie1']."' WHERE `id` = '".$tmp2['id']."';";
		mysql_query($sql2);
		echo '<br>'.$sql2;
	}

	$sql3 = "SELECT * FROM `schedule` WHERE `ord_num` = '".$tmp['order_num']."' ;";
	$res3 = mysql_query($sql3);
	while($tmp3 = mysql_fetch_array($res3)){
		$sql3 = "UPDATE `schedule` SET `su` = '".$tmp3['qty']*$tmp['ie1']."' WHERE `id` = '".$tmp3['id']."';";
		mysql_query($sql3);
		echo '<br>'.$sql3;
	}

	$sql4 = "SELECT * FROM `saw_out_put` WHERE `ord_num` = '".$tmp['order_num']."' ;";
	$res4 = mysql_query($sql4);
	while($tmp4 = mysql_fetch_array($res4)){
		$sql4 = "UPDATE `saw_out_put` SET `su` = '".$tmp4['qty']*$tmp['ie1']."' WHERE `id` = '".$tmp4['id']."';";
		mysql_query($sql4);
		echo '<br>'.$sql4;
	}
	
	// $sqls = "UPDATE `order_partial` SET `ext_qty` = '' , `ext_su` = '' , `p_ets` = '' , `p_etf` = '' , `ext_period` = '' , `p_qty_done` = '' , `p_qty_shp` = '' , `pdt_status` = '' , `p_shp_date` = '' WHERE `id` = '".$tmp['id']."';";
	// mysql_query($sqls);
	// echo '<br>'.$sqls;
	
}
exit;


#對照表
// 幣別 開始 -----------------------------------------------
$chg_currency = array();
$chg_currency['EUR']		=	'EUR';		#歐元
$chg_currency['GBP']		=	'GBP';		#英鎊
$chg_currency['HKD']		=	'HKD';		#港元
$chg_currency['JPY']		=	'JPY';		#日元
$chg_currency['RMB']		=	'MCY';		#人民幣元
$chg_currency['NTD']		=	'NTD';		#新台幣元
$chg_currency['USD']		=	'USD';		#美元
// 幣別 結束 -----------------------------------------------

// 稅率 沒有
$chg_tariff = array();

// 運送方式 開始 -----------------------------------------------
$chg_ship_way = array();
$chg_ship_way['Express']	=	'A001'; 	#快遞
$chg_ship_way['Air']			=	'A003'; 	#空運
$chg_ship_way['Vessel']		=	'A004'; 	#海運
// 運送方式 結束 -----------------------------------------------

// 交易條件 開始 -----------------------------------------------
$chg_fob = array();
$chg_fob['CNF']		    	=	'CNF'; 		#CNF
$chg_fob['FOB']		    	=	'FOB'; 		#FOB
$chg_fob['FOR']       	=	'FOR'; 		#FOR
$chg_fob['EX-FTY']			=	'EXF'; 		#EX-FTY 不含任何運費，運費由買方吸收
$chg_fob['EX-Works']  	=	'EXW'; 		#EX-Works 不含任何運費，運費買方吸收）在賣方所在地交貨，例如：工廠、倉庫，賣方不負責將貨物裝載於買方所提供的交通工具上，亦不負責辦理貨物出口通關。賣方負擔最小義務的貿易條件。 
// 交易條件 結束 -----------------------------------------------

// 付款條件 開始 -----------------------------------------------
$chg_dm_way = array();
$chg_dm_way['L/C at sight']																		=	'10'; 	#即期信用狀
$chg_dm_way['T/T before shipment']		                				=	'11'; 	#貨前匯款
$chg_dm_way['Check']		                              				=	'12'; 	#支票
$chg_dm_way['Check before shipment']		                  		=	'13'; 	#貨前支票付款
$chg_dm_way['COD']		                                 				=	'14'; 	#cash on delivery 貨到付現金
$chg_dm_way['Bank Draft']		                            			=	'15'; 	#銀行匯票
$chg_dm_way['T/T after shipment']		                  				=	'16'; 	#貨後匯款
$chg_dm_way['30% TT before shipment, 70% TT after shipment']	=	'17'; 	#貨前匯款30% , 貨後匯款 70% 
$chg_dm_way['D/D']		                                 				=	'18'; 	#Draft 票匯
$chg_dm_way['月結30天']		                            				=	'01'; 	#月結30天
$chg_dm_way['月結60天']		                            				=	'03'; 	#月結60天
$chg_dm_way['月結80天']		                            				=	'04'; 	#月結80天
// 付款條件 結束 -----------------------------------------------

// 計量單位 開始 -----------------------------------------------
$chg_po_unit = array();
$chg_po_unit['yd']				=	'YRD'; 	#碼
$chg_po_unit['unit']    	=	'UNT'; 	#台
$chg_po_unit['set']				=	'SET'; 	#套
$chg_po_unit['pc']				=	'PKG'; 	#件
$chg_po_unit['meter']   	=	'MTR'; 	#米
$chg_po_unit['lb']				=	'LBR'; 	#磅
$chg_po_unit['kg']				=	'KGM'; 	#KG
$chg_po_unit['gross']   	=	'GRO'; 	#蘿
$chg_po_unit['ft']				=	'FTQ'; 	#才
$chg_po_unit['dz']				=	'DZN'; 	#打
$chg_po_unit['inch']    	=	'INC'; 	#英吋
$chg_po_unit['cone']    	=	'CON'; 	#1捆
$chg_po_unit['K pcs']  		=	'KPC'; 	#K=1000pcs 
$chg_po_unit['100pcs']  	=	'BPC'; 	#100個


// $sql = "SELECT `ap`.`special`
// FROM `receive`,`receive_det`,`rcv_po_link`,`ap`,`ap_det`,`ap_special` 
// WHERE 
// `receive`.`po_id` = `ap`.`id` AND 
// `receive`.`po_id` = `ap`.`id` AND 
// `receive`.`inv_date` > '2011-05-01' 

// ;";


# 修改 ORDER PARTIAL 初始值
$sql = "SELECT * FROM `order_partial` WHERE 1 AND `ord_num` LIKE 'DTH1-%' AND `ext_qty` != 0 ;";
$res = mysql_query($sql);
while($tmp = mysql_fetch_array($res)){
	$sqls = "UPDATE `order_partial` SET `ext_qty` = '' , `ext_su` = '' , `p_ets` = '' , `p_etf` = '' , `ext_period` = '' , `p_qty_done` = '' , `p_qty_shp` = '' , `pdt_status` = '' , `p_shp_date` = '' WHERE `id` = '".$tmp['id']."';";
	// mysql_query($sqls);
	echo '<br>'.$sqls;
	
}
exit;


# 修改 驗收 金額
// function get_po($id,$sp){
	// if ( $sp == '2' ) {
		// $sql = "SELECT `prics` FROM `ap_special` WHERE `id` = '".$id."' ";
	// } else {
		// $sql = "SELECT `prics` FROM `ap_det` WHERE `id` = '".$id."' ";
	// }

	// $res = mysql_query($sql);
	// $prics = mysql_fetch_row($res); 
	// return $prics[0];
// }

// $sql = "SELECT `receive`.`rcv_num` , `receive_det`.`po_id` as `id` , `rcv_po_link`.`id` as `r_id` , `rcv_po_link`.`qty` , `ap`.`special`
// FROM `receive`,`receive_det`,`rcv_po_link`,`ap`
// WHERE 
// `receive`.`rcv_num` = `receive_det`.`rcv_num` AND 
// `receive`.`po_id` = `ap`.`id` AND 
// `receive_det`.`id` = `rcv_po_link`.`rcv_id` 
// ;";

// $res = mysql_query($sql);
// while($tmp = mysql_fetch_array($res)){
	// $prics = get_po($tmp['id'],$tmp['special']);
	
	// $sqls = "UPDATE `rcv_po_link` SET `amount` = '".$tmp['qty']*$prics."' WHERE `id` = '".$tmp['r_id']."';";
	// mysql_query($sqls);
	// echo '<br>'.$sqls;
	
// }


exit;
# 修改 Schedule ETS - ETF

$sql = "SELECT `ord_num` , min( `out_date` ) AS min , max( `out_date` ) AS max , sum( `qty` ) AS qty , `p_id` , `s_id` 
FROM `saw_out_put` 
WHERE `holiday` != '1' AND `ord_num` != '' AND `s_id` != ''
GROUP BY `s_id` 
ORDER BY `min` ASC   ;";

// $sql = "SELECT * FROM saw_out_put WHERE saw_fty = 'LY' ;";
$res = mysql_query($sql);
$mo = 1;
while($tmp = mysql_fetch_array($res)){
	// echo '<br>[ '.$mo.' ] '.$tmp['ord_num'].' / '.$tmp['min'].' - '.$tmp['max'].' / '.$tmp['qty'].' [ '.$tmp['s_id'].' ] ';
	$mo++;
	$sqls = "UPDATE `schedule` SET `rel_ets` = '".$tmp['min']."' , `rel_etf` = '".$tmp['max']."' WHERE id = '".$tmp['s_id']."';";
	$ress = mysql_query($sqls);
	echo '<br>'.$sqls;
}



exit;

# 修改 S_ID 

$sql = "SELECT schedule.id as sd_id ,saw_out_put.id as out_id,saw_out_put.out_date
FROM saw_out_put, schedule
WHERE schedule.line_id = saw_out_put.line_id AND schedule.p_id = saw_out_put.p_id AND saw_out_put.qty != '0' ;";

// $sql = "SELECT * FROM saw_out_put WHERE saw_fty = 'LY' ;";
$res = mysql_query($sql);
while($tmp = mysql_fetch_array($res)){
	// if(!empty($tmp['s_id']))
	// $mo[$tmp['s_id']][] = $tmp['s_id'];
	$sqls = "UPDATE `saw_out_put` SET s_id = '".$tmp['sd_id']."' WHERE id = '".$tmp['out_id']."';";
	$ress = mysql_query($sqls);
	echo '<br>'.$sqls."\n";
}

// foreach($mo as $key){
	// if( count($key) > 1 ) {
		// print_r($key);
		// echo count($key).'<br>';
	// }
// }


echo 'OK LA~';
exit;




# 工繳異常修改
$sql ="SELECT * FROM `exceptional`,`exc_static` WHERE `exceptional`.`id` = `exc_static`.`exc_id` AND `exceptional`.`status` = 4 AND `exceptional`.`oth_exc` = 1 AND `exceptional`.`exc_date` > '2009-01-01'";
$res = mysql_query($sql);
while($tmp = mysql_fetch_array($res)){
	// echo $tmp['id'].' '.$tmp['ex_num'].' '.$tmp['org_rec'].' '.$tmp['ord_num'].'<br>';
	echo "UPDATE `s_order` SET `fty_cm` = '".$tmp['org_rec']."' WHERE `order_num` = '".$tmp['ord_num']."' <br>";
	// $sqls = "UPDATE `s_order` SET `fty_cm` = '".$tmp['org_rec']."' WHERE `order_num` = '".$tmp['ord_num']."';";
	// $ress = mysql_query($sqls);
}


exit;


require_once("./lib/spreadsheets/Worksheet.php");
require_once("./lib/spreadsheets/Workbook.php");

function HeaderingExcel($filename) {
	header("Content-type: application/vnd.ms-excel");
	header("Content-Disposition: attachment; filename=$filename");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
	header("Pragma: public");
}


// HTTP headers
HeaderingExcel('order.xls');

// Creating a workbook
$workbook = new Workbook("-");

// Creating the first worksheet
$worksheet1 =& $workbook->add_worksheet('order_list');


//欄位名稱用
$formatot =& $workbook->add_format();
$formatot->set_size(10);
$formatot->set_align('left');
$formatot->set_color('block');
$formatot->set_pattern();
$formatot->set_fg_color('white');

// $ary_size = array(12,5,20,8,12,40);
// for ($i=0; $i<sizeof($ary_size); $i++){
	// $worksheet1->set_column(0,$i,$ary_size[$i]);	  
// }

$ary_size = array(10,10,26,8,6);
for ($i=0; $i<sizeof($ary_size); $i++){
	$worksheet1->set_column(0,$i,$ary_size[$i]);	  
} 

// $worksheet1->set_column(0,0,15);
// $worksheet1->set_column(0,1,15);
// $worksheet1->set_column(0,2,15);
// $worksheet1->set_column(0,3,15);
// $worksheet1->set_column(0,4,15);  
// $worksheet1->set_column(0,5,15);  

$i = 0;
// $sql = "SELECT `id`,`wi_num`,`cust` FROM `wi` WHERE `bom_status` = '4' AND ( `bcfm_date` > '2011-01-01' AND `bcfm_date` < '2011-07-04' ) ";
// $sql = "SELECT `id`,``wi_num``,`cust` FROM `wi` WHERE `etd` > '2011-07-05'";

$sql = "SELECT 
`wi`.`id`,
`s_order`.`cust`,
`wi`.`wi_num`,
`s_order`.`style_num`,`s_order`.`etd`,
`size_des`.`size`
FROM `s_order` LEFT JOIN `size_des` ON `s_order`.`size` = `size_des`.`id` LEFT JOIN `wi` ON `s_order`.`order_num` = `wi`.`wi_num` 
WHERE 
`s_order`.`status` >= '4' AND 
`s_order`.`size` = `size_des`.`id` AND 
`s_order`.`etd` >= '2011-07-10'
ORDER BY `wi`.`wi_num` ASC ";
# `s_order`.`etd` opendate > '2011-07-05'";

$res = mysql_query($sql);
while($tmp = mysql_fetch_array($res)){
	// echo $i.':';
	$sql_ordp = "SELECT `id`,`p_etd`,`p_qty` FROM `order_partial` WHERE `order_partial`.`ord_num` = '".$tmp['wi_num']."' ";
	$res_ordp = mysql_query($sql_ordp);
	while($tmp_partial = mysql_fetch_array($res_ordp)){
		$size_arr = explode(',',$tmp['size']);
		
		// echo '#1：'.$tmp['wi_num'].','.$tmp['cust'].','.$tmp['style_num'].','.$tmp_partial['p_qty'].','.$tmp_partial['p_etd'].','.$tmp['size'].'<br>';
		// $worksheet1->write($i,0,$tmp['wi_num'],$formatot);
		// $worksheet1->write($i,1,$tmp['cust'],$formatot);
		// $worksheet1->write($i,2,$tmp['style_num'],$formatot);
		// $worksheet1->write($i,3,$tmp_partial['p_qty'],$formatot);
		// $worksheet1->write($i,4,$tmp_partial['p_etd'],$formatot);
		// $worksheet1->write($i,5,$tmp['size'],$formatot);
		// $i++;
		
		$sql_wiqty = "SELECT `colorway`,`qty` FROM `wiqty` WHERE `wi_id` = '".$tmp['id']."' AND `p_id` = '".$tmp_partial['id']."' ";
		$res_wiqty = mysql_query($sql_wiqty);
		while($tmp_wiqty = mysql_fetch_array($res_wiqty)){
			$colorway_arr = explode(',',$tmp_wiqty['colorway']);
			$qty_arr = explode(',',$tmp_wiqty['qty']);

			foreach($size_arr as $key => $val){
				// # echo '#2-'.$key.'：'.$tmp['wi_num'].','.$tmp_partial['p_etd'].','.$tmp_wiqty['colorway'].','.$val.','.$qty_arr[$key].'<br>';
				$worksheet1->write($i,0,$tmp['wi_num'],$formatot);
				$worksheet1->write($i,1,$tmp_partial['p_etd'],$formatot);
				$worksheet1->write($i,2,$tmp_wiqty['colorway'],$formatot);
				$worksheet1->write($i,3,$val,$formatot);
				$worksheet1->write($i,4,$qty_arr[$key] == NULL?0:$qty_arr[$key],$formatot);
				$i++;			
			}
		}		
	}
	// $i++;

	

	// echo '<p>';
}

$workbook->close();
exit;


// 計量單位 結束 -----------------------------------------------
# 修改 採購 Tolernce 的資料顯示
# 修改多筆預先採購
exit;
$sql = "SELECT `id`,`toler` FROM `ap` ";
$res = mysql_query($sql);
while($tmp = mysql_fetch_array($res)){
	$toler = explode('|',$tmp['toler']);
	# echo $tmp['toler'].'<br>';
	if( count($toler) != 2 ){
		$scql = "UPDATE `ap` set `toler` = '".($tmp['toler'].'|'.$tmp['toler'])."' where `id` = '".($tmp['id'])."'";
		# echo $val.',';
		# echo $scql.'<br>';
		mysql_query($scql);
	}
}
echo '<p><b>(OK)</b>';
exit;





# 修改多筆預先採購
exit;
$sql = "SELECT * FROM `bom_lots` ";
$res = mysql_query($sql);
while($tmp = mysql_fetch_array($res)){
	if($tmp['ap_mark'] && $tmp['ap_mark'] != 'stock'){
		$ap_mark = explode(',',$tmp['ap_mark']);
		foreach($ap_mark as $key => $val){
			$scql = "UPDATE `ap` set `ap_mark` = '".($tmp['ap_mark'])."' where `ap_num` = '".($val)."'";
			// echo $val.',';
			// echo $scql.'<br>';
			mysql_query($scql);
		}
		
	}
}

$sql = "SELECT * FROM `bom_acc` ";
$res = mysql_query($sql);
while($tmp = mysql_fetch_array($res)){
	if($tmp['ap_mark'] && $tmp['ap_mark'] != 'stock'){
		$ap_mark = explode(',',$tmp['ap_mark']);
		foreach($ap_mark as $key => $val){
			$scql = "UPDATE `ap` set `ap_mark` = '".($tmp['ap_mark'])."' where `ap_num` = '".($val)."'";
			// echo $val.',';
			// echo $scql.'<br>';
			mysql_query($scql);
		}
		
	}
}
echo '<p><b>(OK)</b>';
exit;





# 更改權限 csv
exit;

# 6 
$in_no = 6;
$st_no = 0;
$sno = 72;




$user = array();
$sql = "SELECT * FROM `user` ";
$res = mysql_query($sql);
while($tmp = mysql_fetch_array($res)){
	$test = $sstr = '';
	
	if (  $tmp['active'] == 'Y' ) {
		$mo = explode(',',$tmp['perm']);
		for( $i=0; $i < count($mo); $i++ ){
			// $test .= $mo[$i].',';
			if( $i == ( $in_no - 1 ) ) {
				$sstr .=  $mo[$i].','.$st_no.',';
			} else {
				$sstr .=  $mo[$i].',';
			}
		}
		$tmp['perm'] = substr($sstr,0,-1);
		// echo $test.'<br>';
		// echo $sstr.'<br>';
		// exit;
	} else {
		for( $i=0; $i < $sno; $i++ ){
			$sstr .=  '0,';
		}
		$tmp['perm'] = substr($sstr,0,-1);
	}

	// $mo = explode(',',$tmp['perm']);
	// echo count($mo).':'.$tmp['name'].':'.$tmp['perm'].'<p>';
	$user[] = $tmp;
}

foreach( $user as $key => $val ) {
	$sql = "UPDATE `user` set `perm` = '".($val['perm'])."' where `id` = '".($val['id'])."'";
	// echo $sql.'<br>';
	$res = mysql_query($sql);
}

// print_r($user);
// $s_order = array();
// $sql = "SELECT * FROM `s_order` ";
// $res = mysql_query($sql);
// while($tmp = mysql_fetch_array($res)){
  // $s_order[$tmp['order_num']]['etd'] = $tmp['etd'];
// }

// foreach($s_order as $key => $val){
	// if( !empty($wi[$key]) && $wi[$key]['etd'] <> $val['etd'] )
		// echo $key.' - s_order.etd = '.$val['etd'].' - wi.etd = '.$wi[$key]['etd'].'<br>';
// }







/* echo '<p>2010-01-01 以下未使用 Cust：';
$set_date = '2010-01-01';


// $sql = "SELECT `s_order`.`cust` , max( `s_order`.`opendate` ) AS `opendate` , count( `s_order`.`cust` ) AS `count` , `cust`.`cust_f_name` , `cust`.`cust_init_name` 
// FROM `s_order` , `cust` 
// WHERE `s_order`.`cust` = `cust`.`cust_s_name` 
// GROUP BY `s_order`.`cust` 
// ORDER BY `opendate` DESC 
// ";
$sql = "
SELECT `s_order`.`cust` , max( `s_order`.`opendate` ) AS `opendate` , count( `s_order`.`cust` ) AS `count` , `cust`.`cust_f_name` , `cust`.`cust_init_name` 
FROM `s_order` , `cust` 
WHERE `s_order`.`cust` = `cust`.`cust_s_name` 
AND `cust` != 'LU'
AND `cust` != 'BK'
AND `cust` != 'RE'
AND `cust` != 'TH'
AND `cust` != 'HI'
AND `cust` != 'AG'
AND `cust` != 'CH'
AND `cust` != 'MK'
AND `cust` != 'NL'
AND `cust` != 'DO'
AND `cust` != 'BF'
AND `cust` != 'PH'
AND `cust` != 'TM'
AND `cust` != 'TA'
AND `cust` != 'CR'
AND `cust` != 'YG'
AND `cust` != 'MN'
AND `cust` != 'BT'
AND `cust` != 'CT'
AND `cust` != 'SL'
AND `cust` != 'LB'
AND `cust` != 'SR'
AND `cust` != 'GN'
AND `cust` != 'TR'
AND `cust` != 'DB'
AND `cust` != 'NX'
AND `cust` != 'GV'
AND `cust` != 'NT'
AND `cust` != 'HK'
AND `cust` != 'JG'
GROUP BY `s_order`.`cust` 
ORDER BY count( `s_order`.`cust` ) DESC 
";

$sql = "SELECT `s_order`.`cust` , max( `s_order`.`opendate` ) AS `opendate` , count( `s_order`.`cust` ) AS `count` , `cust`.`cust_f_name` , `cust`.`cust_init_name` 
        FROM `s_order` , `cust` 
        WHERE `s_order`.`cust` = `cust`.`cust_s_name` 
        GROUP BY `s_order`.`cust` 
        ORDER BY ( `s_order`.`cust` ) DESC
        "; 

$res = mysql_query($sql);
$mode = 1;
$mode_arr = array();
echo '<table border="0" cellspacing="1" cellpadding="3" cols="0" bgcolor="#666666">';
while($tmp = mysql_fetch_array($res)){
  
  if( $tmp['opendate'] < $set_date ){
    echo '<tr>';
       echo '<td bgcolor="#FFFFFF">'.$mode++.'&nbsp;</td>';
      echo '<td bgcolor="#FFFFFF">'.$tmp['cust'].'&nbsp;</td><td bgcolor="#FFFFFF">'.$tmp['opendate'].'&nbsp;</td><td bgcolor="#FFFFFF">'.$tmp['count'].'&nbsp;</td><td bgcolor="#FFFFFF">'.$tmp['cust_f_name'].'&nbsp;</td><td bgcolor="#FFFFFF">'.$tmp['cust_init_name'].'&nbsp;</td>';
    echo '</tr>';
  }
  $mode_arr[$tmp['cust']] = $tmp['cust'];
}
echo '</table>';

$cust_arr = array();
$sql = "SELECT `cust_s_name`,`cust_init_name` FROM `cust` ";
$res = mysql_query($sql);
while($tmp = mysql_fetch_array($res)){
  $cust_arr[$tmp['cust_s_name']] = $tmp['cust_init_name'];
}

echo '<p>無使用 Cust：';
// print_r($mode_arr);
// print_r($cust_arr);

$mode = 1;
$sn = 1;
echo '<table border="0" cellspacing="1" cellpadding="3" cols="0" bgcolor="#666666">';
  echo '';
    echo '<tr>';
foreach($cust_arr as $key => $val){
  if( empty($mode_arr[$key]) ){
    if ( ($mode == 6) ) {
      $mode = 1;
      echo '</tr><tr bgcolor="#FFFFFF">';
    }
    $mode++;
    echo '<td bgcolor="#FFFFFF">'.$sn++.'.&nbsp;['.$key.']='.$cust_arr[$key].'</td>';
  }
}
  echo '';
  echo '';
echo '</table>'; */




/* # 沒在用的客戶檔
$cust = array();
$sql = "SELECT * FROM `cust` ";
$res = mysql_query($sql);
while($tmp = mysql_fetch_array($res)){
	$cust[] = array( 'id' => $tmp['id'] , 'cust' => $tmp['cust_s_name'] );
}
// print_r($cust);

$s_order = array();
$sql = "SELECT * FROM `s_order` ";
$res = mysql_query($sql);
while($tmp = mysql_fetch_array($res)){
  $s_order[$tmp['cust']] = $tmp['cust'];
}

foreach($cust as $key => $val){
	// echo $val['cust'].'<br>';
	if( empty($s_order[$val['cust']]) ) {
		echo $val['id'].' - '.$val['cust'].'<br>';
	}
} */



/* 
$wi = array();
$sql = "SELECT * FROM `wi` ";
$res = mysql_query($sql);
while($tmp = mysql_fetch_array($res)){
  $wi[$tmp['wi_num']]['etd'] = $tmp['etd'];
}

$s_order = array();
$sql = "SELECT * FROM `s_order` ";
$res = mysql_query($sql);
while($tmp = mysql_fetch_array($res)){
  $s_order[$tmp['order_num']]['etd'] = $tmp['etd'];
}

foreach($s_order as $key => $val){
	if( !empty($wi[$key]) && $wi[$key]['etd'] <> $val['etd'] )
		echo $key.' - s_order.etd = '.$val['etd'].' - wi.etd = '.$wi[$key]['etd'].'<br>';
} */





/*
if(!empty($tb_user)){
	$table = "user";
	$sql = "SELECT id,name,login_id,perm FROM `".$table."` WHERE `active` = 'Y' ";
}

if(!empty($tb_team)){
	$table = "team";
	$sql = "SELECT id,team_name,perm FROM `".$table."` ";
}

if(!empty($sql)){

	$res = mysql_query($sql);
	while($tmp = mysql_fetch_array($res)){
		$pop=$perm='';
		$perm = explode(',',$tmp['perm']);
		foreach($perm as $key => $val)if($key == $ukey)	$perm[$key] = $uval;
		foreach($perm as $key => $val)$pop = $pop.$val.",";
		$pop = substr($pop,0,-1);
		$sql = "update `".($table)."` set perm = '".($pop)."' where id = '".($tmp['id'])."'";
		#echo $sql,'<br>';

		if (mysql_query($sql)){
			$name = ( $table == 'user')? $tmp['name'] : $tmp['team_name'];
			if(empty($name))$name='---無名---';
			echo "[ ".$name." ] 轉換 OK <br>";
		}
	}
}
*/


	// function increceDaysInDate($date,$nDays) 	{ 
    // if( !isset( $nDays ) ) { 	$nDays = 1;     } 
    // $aVet = Explode( "-",$date ); 
    // return date( "Y-m-d",mktime(0,0,0,$aVet[1],$aVet[2]+$nDays,$aVet[0])); 
	// } 


	// $tables = "s_order";
	// $sqls = "SELECT `order_num` FROM `".$tables."` WHERE `etd` LIKE '2009-06%'";
	// $ress = mysql_query($sqls);
	// while($tmps = mysql_fetch_array($ress)){

	// $table = "pdtion";
	// $sql = "SELECT id,mat_etd,m_acc_etd,acc_etd,mat_eta,m_acc_eta,acc_eta FROM `".$table."` WHERE `order_num` = '".$tmps['order_num']."'";
	// $res = mysql_query($sql);
	
		// while($tmp = mysql_fetch_array($res)){
			// $sql = $tsql = '';
			
			// $tsql = (!empty($tmp['mat_etd']) && empty($tmp['mat_eta']))? " `mat_eta` = '".increceDaysInDate($tmp['mat_etd'],10)."' ," : '' ;
			// $tsql .= (!empty($tmp['m_acc_etd']) && empty($tmp['m_acc_eta']))? " `m_acc_eta` = '".increceDaysInDate($tmp['m_acc_etd'],10)."' ," : '' ;
			// $tsql .= (!empty($tmp['acc_etd']) && empty($tmp['acc_eta']))? " `acc_eta` = '".increceDaysInDate($tmp['acc_etd'],10)."' ," : '' ;
			// $tsql = substr($tsql,0,-1);
	
			// if(!empty($tsql))
			// $sql = "update `".($table)."` set ".$tsql." where id = '".($tmp['id'])."'";
	
			// #echo $sql,'<br>';
	
			// if (mysql_query($sql)){
			// #	$name = ( $table == 'user')? $tmp['name'] : $tmp['team_name'];
			// #	if(empty($name))$name='---無名---';
				// echo "[ ".$sql." ] 轉換 OK <br>";
			// }
		// }
	// }




?>