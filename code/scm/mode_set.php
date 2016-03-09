<?php
// phpinfo();
// exit;
# 暫無轉檔用途 ， 純粹備份轉檔程式用
// session_start();
// mb_internal_encoding("big5");

// echo '<meta http-equiv="Content-Type" content="text/html; charset=big5">';
define("DB_HOST","localhost");
define("DB_LOGIN","root");
define("DB_PASSWORD","");
// define("DB_LOGIN","ie");
// define("DB_PASSWORD","carnival");
define("DB_NAME","scm_smpl");
// define("DB_NAME","scm_maintain");
// define("DB_NAME","scm_tmp");
// define("DB_NAME","scm_test");
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

// $sql = "SELECT * FROM `po_ship_det` WHERE `po_id` != '' AND `ap_num` = '' ;";
// $res = mysql_query($sql);
// while($tmp = mysql_fetch_array($res)){
    // echo $tmp['id'].'<br>';
    // $q_str = "SELECT ap_num,mat_cat,mat_id,color,size FROM ap_det WHERE `po_spare` = '".$tmp['po_id']."';";
     // echo $q_str.'<br>';
    // $ress = mysql_query($q_str);
    // $tmpp = mysql_fetch_array($ress);
    // if( $tmpp['ap_num'] != '' ){
        // $q_sql = "UPDATE `po_ship_det` SET `ap_num` = '".$tmpp['ap_num']."',`mat_cat` = '".$tmpp['mat_cat']."',`mat_id` = '".$tmpp['mat_id']."',`color` = '".$tmpp['color']."',`size` = '".$tmpp['size']."' WHERE `id` = '".$tmp['id']."' ;";
        // mysql_query($q_sql);
        // echo $q_sql.'<br>';
    // }
// }
// exit;



// # 修改 CF 訂單狀態錯誤 重複執行無關緊要

# ORDER,ETD,etd,Cust,QTY,FOB,SHIP Q'ty,SHIP FOB,短裝件數,短出FOB金額
// $sql = "SELECT 
// `s_order`.`order_num` as `ORDER` , 
// date_format(`s_order`.`etd`,'%Y_%m') as `ETD` , 
// `s_order`.`etd` , 
// `cust`.`cust_f_name` as `Cust` , 
// `s_order`.`qty` , 
// `s_order`.`uprice` as `FOB` , 
// SUM(`shipping_doc_qty`.`ttl_qty`) as `SHIP_Qty`,
// `shipping_doc_qty`.`ship_fob`

// FROM `s_order` LEFT JOIN `cust` ON ( `s_order`.`cust` = `cust`.`cust_s_name` ) , `shipping_doc_qty`

// WHERE 

// `s_order`.`factory` = 'LY' AND 
// `s_order`.`order_num` = `shipping_doc_qty`.`ord_num` AND 
// `s_order`.`etd` BETWEEN '2015-01-01' AND '2015-08-31' 


// GROUP By `shipping_doc_qty`.`ord_num` 
// ORDER By `s_order`.`etd`";
// $res = mysql_query($sql);
// echo 'ORDER,ETD,etd,Cust,QTY,FOB,SHIP Q\'ty,SHIP FOB,短裝件數,短出FOB金額'.'<br>';
// while($tmp = mysql_fetch_array($res)){
    // if( $tmp['qty'] > $tmp['SHIP_Qty'] ) {
        // echo $tmp['ORDER'].','.$tmp['ETD'].','.$tmp['etd'].','.$tmp['Cust'].','.$tmp['qty'].','.$tmp['FOB'].','.$tmp['SHIP_Qty'].','.$tmp['ship_fob'].','.($tmp['qty']-$tmp['SHIP_Qty']).','.(($tmp['qty']-$tmp['SHIP_Qty'])*$tmp['ship_fob']).'<br>';
    // }
// }
// exit;


# 修改 CF 訂單狀態錯誤 重複執行無關緊要
// $sql = "SELECT * FROM `s_order` WHERE `factory` = 'CF' AND `status` = '4' ;";
// $sql = "SELECT * FROM `s_order` WHERE `factory` = 'LY' AND `status` = '4' ;";
// $sql = "SELECT * FROM `s_order` WHERE `factory` = 'HJ' AND `status` = '4' ;";
// $res = mysql_query($sql);
// while($tmp = mysql_fetch_array($res)){
    // $q_str = "SELECT sum(saw_out_put.qty) as qty FROM saw_out_put WHERE `ord_num` = '".$tmp['order_num']."';";
    // $ress = mysql_query($q_str);
    // $tmpp = mysql_fetch_array($ress);
    // if( $tmpp['qty'] > 0 ){
        // $q_sql = "UPDATE `s_order` SET `status` = '8' WHERE `order_num` = '".$tmp['order_num']."' ;";
        // mysql_query($q_sql);
        // echo $q_sql.'<br>';
    // }
// }
// exit;



// #修改訂單狀態錯誤 20150525 
// $sql = "SELECT * FROM `s_order` WHERE `status` = '4' ;";
// $res = mysql_query($sql);
// while($tmp = mysql_fetch_array($res)){
    // $q_str = "SELECT sum(saw_out_put.qty) as qty FROM saw_out_put WHERE `ord_num` = '".$tmp['order_num']."';";
    // $ress = mysql_query($q_str);
    // $tmpp = mysql_fetch_array($ress);
    // if( $tmpp['qty'] > 0 ){
        // $q_sql = "UPDATE `s_order` SET `status` = '8' WHERE `order_num` = '".$tmp['order_num']."' ;";
        // mysql_query($q_sql);
        // echo $q_sql.'<br>';
    // }
// }
// exit;





# 重新計算原始數量 訂單
// $ORDER = 'DDB12-0886';// $ORDER = 'BLR12-0798';// $ORDER = 'BBT12-0947';// $ORDER = 'BCH12-0826';// $ORDER = 'BLR12-0880';// $ORDER = 'DLS12-0934';// $ORDER = 'BLR13-1477';
// $ORDER = 'BLR13-1478';// $ORDER = 'BLR13-1477';// $ORDER = 'BLR13-1487';// $ORDER = 'ACT13-1366';// $ORDER = 'ABK13-1580';// $ORDER = 'ARE13-1628';// $ORDER = 'ARE13-1629';
// $ORDER = 'ARE13-1630';// $ORDER = 'ABK13-1584';// $ORDER = 'DTH13-1673';// $ORDER = 'DTH13-1706';// $ORDER = 'BCH13-1729';// $ORDER = 'ARE14-0138';// $ORDER = 'ARE14-0109';
// $ORDER = 'ARE14-0111';// $ORDER = 'BCH14-0053';// $ORDER = 'BCH14-0062';// $ORDER = 'BCH14-0064';// $ORDER = 'BCH14-0065';// $ORDER = 'ARE14-0115';// $ORDER = 'ACB14-0162';
// $ORDER = 'BCH14-0070';// $ORDER = 'BCH14-0068';// $ORDER = 'BBT14-0329';// $ORDER = 'ALR14-0492';// $ORDER = 'ALR14-0492';// $ORDER = 'JNI14-0477';// $ORDER = 'JNI14-0482';
// $ORDER = 'JNI14-0481';// $ORDER = 'JNI14-0483';// $ORDER = 'JNI14-0485';// $ORDER = 'JNI14-0476';// $ORDER = 'JNI14-0483';// $ORDER = 'JNI14-0479';// $ORDER = 'DTH14-0732';
// $ORDER = 'DTH14-0733';// $ORDER = 'ACB14-0737';// $ORDER = 'ACB14-0653';// $ORDER = 'DTH14-0719';// $ORDER = 'DTH14-0720';// $ORDER = 'ACB14-0652';// $ORDER = 'ARE14-0782';
// $ORDER = 'ABK14-0704';// $ORDER = 'ACB14-0748';// $ORDER = 'ACB14-0749';// $ORDER = 'DTH14-0756';// $ORDER = 'ALR14-0806';// $ORDER = 'ALR14-0710';// $ORDER = 'ALR14-0711';
// $ORDER = 'ALR14-0805';// $ORDER = 'ACB14-0752';// $ORDER = 'ALR14-0865';// $ORDER = 'JNI14-0481';// $ORDER = 'JNI14-0477';// $ORDER = 'JNI14-0794';// $ORDER = 'JNI14-0796';
// $ORDER = 'JNI14-0796';// $ORDER = 'JAE14-0913';// $ORDER = 'ARE14-0999';// $ORDER = 'JNI14-1110';// $ORDER = 'JNI14-1112';// $ORDER = 'JNI14-1113';// $ORDER = 'ACH15-0041';
// $ORDER = 'ACH15-0042';// $ORDER = 'DTH14-1177';// $ORDER = 'ALR15-0078';// $ORDER = 'ALR15-0334';// $ORDER = 'ACH15-0039';// $ORDER = 'ALR15-0054';// $ORDER = 'ACH15-0365';
// $ORDER = 'ALR15-0325';// $ORDER = 'DTH15-0438';// $ORDER = 'DTH15-0439';// $ORDER = 'DTH15-0381';// $ORDER = 'ACB15-0188';// $ORDER = 'ALR15-0466';// $ORDER = 'ALR15-0637';
// $ORDER = 'ALR15-0638';// $ORDER = 'ALR15-0639';// $ORDER = 'ALR15-0635';// $ORDER = 'DTH15-0564';
// $ORDER = 'ACH15-0780';

echo '修改BOM表配比數量，確保暫停'; exit; 
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


$sql = "SELECT * FROM `order_partial` WHERE `ord_num` = '".$ORDER."' ORDER BY `p_etd` ASC  ;";
$resp = mysql_query($sql);

$qty0 = $qty1 = $qty2 = $qty3 = $wi_id = array();
$qty0_id = $qty1_id = $qty2_id = $qty3_id = $wi_id = array();
$m = 0;

//echo $sql.'<br>';
while($tmpp = mysql_fetch_array($resp)){


	$sql = "SELECT * FROM `wiqty` WHERE `wi_id` = '".$wi['id']."' AND `p_id` = '".$tmpp['id']."' ORDER BY `id` ASC   ;";
	$res = mysql_query($sql);

	//echo $sql.'<br>';
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

	// echo $tmp['id'].'<br>';
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
// exit;


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
		// echo $qty3.' ~ '.$tmp['est_1'].'<br>';
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
				// echo $qty_str.'<br>';
				// echo $qty_str2.'<br>';
			} else {
				$qty_str .= '0' ;
				$qty_str2 .= '0' ;
			}
		}
		// echo $qty_str.'<p>';
	}
	// echo '['.$tmp['arrange'].']' . $tmp['bom_id'].' ~ '.$qty_str.'<br>';
	$q_sql = "UPDATE `bom_acc` SET `qty` = '".$qty_str."' , `o_qty` = '".$qty_str2."' WHERE `id` = '".$tmp['bom_id']."' ;";
	echo $q_sql.'<br>';
	$q_res = mysql_query($q_sql);	
	
	// $qty_ttl = array_sum(explode(',',$qty_str));
	// echo $qty_ttl.'<p>';
}

$ord['id'];

exit;

# 更改訂單組別

// $order_array = array(
    // 'JAE14-0892',
    // 'JAE14-0913',
    // 'JAE14-0914',
    // 'JAE14-0890',
    // 'JAE14-0891',
    // 'JAE14-0889',
    // 'JAE14-0915',
    // 'JAE14-0893',
    // 'JAE14-0894'
// );

$order_array = array(
    'JAE15-0311',
    'JAE15-0310'
);

// $order_array = array(
    // 'AAE14-0892',
    // 'AAE14-0913',
    // 'AAE14-0914',
    // 'AAE14-0890',
    // 'AAE14-0891',
    // 'AAE14-0889',
    // 'AAE14-0915',
    // 'AAE14-0893',
    // 'AAE14-0894'
// );

foreach( $order_array as $key => $order ){

    $new_order = str_replace(substr($order,0,3),'L'.substr($order,1,2),$order);

    # acc_use.smpl_code
    $sql = "SELECT * FROM `acc_use` WHERE `smpl_code` = '".$order."' ;";
    $resp = mysql_query($sql);
    #echo $sql.'<br>';
    while($str = mysql_fetch_array($resp)){

        // echo $str['id'].' - '.str_replace(substr($str['smpl_code'],0,3),'A'.substr($str['smpl_code'],1,2),$str['smpl_code']).' - '.$str['smpl_code'].'<br>';
        $q_sql = "UPDATE `acc_use` SET `smpl_code` = '".str_replace(substr($str['smpl_code'],0,3),'L'.substr($str['smpl_code'],1,2),$str['smpl_code'])."' WHERE `id` = '".$str['id']."' ;";
        mysql_query($q_sql);
        echo $q_sql.'<br>';
        
    }
    
    # cutting_out.ord_num
    $sql = "SELECT * FROM `cutting_out` WHERE `ord_num` = '".$order."' ;";
    $resp = mysql_query($sql);
    while($str = mysql_fetch_array($resp)){
        $q_sql = "UPDATE `cutting_out` SET `ord_num` = '".str_replace(substr($str['ord_num'],0,3),'L'.substr($str['ord_num'],1,2),$str['ord_num'])."' WHERE `id` = '".$str['id']."' ;";
        mysql_query($q_sql);
        echo $q_sql.'<br>';
    }

    # log.description
    $sql = "SELECT * FROM `log` WHERE `description` LIKE '%".$order."%' ;";
    $resp = mysql_query($sql);
    while($str = mysql_fetch_array($resp)){
        $q_sql = "UPDATE `log` SET `description` = '".addslashes(str_replace($order,$new_order,$str['description']))."' WHERE `id` = '".$str['id']."' ;";
        mysql_query($q_sql);
        echo $q_sql.'<br>';
    }

    # lots_use.smpl_code
    $sql = "SELECT * FROM `lots_use` WHERE `smpl_code` LIKE '%".$order."%' ;";
    $resp = mysql_query($sql);
    while($str = mysql_fetch_array($resp)){
        $q_sql = "UPDATE `lots_use` SET `smpl_code` = '".str_replace($order,$new_order,$str['smpl_code'])."' WHERE `id` = '".$str['id']."' ;";
        mysql_query($q_sql);
        echo $q_sql.'<br>';
    }

    # order_log.order_num
    $sql = "SELECT * FROM `order_log` WHERE `order_num` LIKE '%".$order."%' ;";
    $resp = mysql_query($sql);
    while($str = mysql_fetch_array($resp)){
        $q_sql = "UPDATE `order_log` SET `order_num` = '".str_replace($order,$new_order,$str['order_num'])."' WHERE `id` = '".$str['id']."' ;";
        mysql_query($q_sql);
        echo $q_sql.'<br>';
    }

    # order_partial.ord_num
    $sql = "SELECT * FROM `order_partial` WHERE `ord_num` LIKE '%".$order."%' ;";
    $resp = mysql_query($sql);
    while($str = mysql_fetch_array($resp)){
        $q_sql = "UPDATE `order_partial` SET `ord_num` = '".str_replace($order,$new_order,$str['ord_num'])."' WHERE `id` = '".$str['id']."' ;";
        mysql_query($q_sql);
        echo $q_sql.'<br>';
    }

    # pdt_finish.order_num
    $sql = "SELECT * FROM `pdt_finish` WHERE `order_num` LIKE '%".$order."%' ;";
    $resp = mysql_query($sql);
    while($str = mysql_fetch_array($resp)){
        $q_sql = "UPDATE `pdt_finish` SET `order_num` = '".str_replace($order,$new_order,$str['order_num'])."' WHERE `id` = '".$str['id']."' ;";
        mysql_query($q_sql);
        echo $q_sql.'<br>';
    }

    # saw_out_put.ord_num
    $sql = "SELECT * FROM `saw_out_put` WHERE `ord_num` LIKE '%".$order."%' ;";
    $resp = mysql_query($sql);
    while($str = mysql_fetch_array($resp)){
        $q_sql = "UPDATE `saw_out_put` SET `ord_num` = '".str_replace($order,$new_order,$str['ord_num'])."' WHERE `id` = '".$str['id']."' ;";
        mysql_query($q_sql);
        echo $q_sql.'<br>';
    }

    # schedule.ord_num
    $sql = "SELECT * FROM `schedule` WHERE `ord_num` LIKE '%".$order."%' ;";
    $resp = mysql_query($sql);
    while($str = mysql_fetch_array($resp)){
        $q_sql = "UPDATE `schedule` SET `ord_num` = '".str_replace($order,$new_order,$str['ord_num'])."' WHERE `id` = '".$str['id']."' ;";
        mysql_query($q_sql);
        echo $q_sql.'<br>';
    }

    # shipping.ord_num
    $sql = "SELECT * FROM `shipping` WHERE `ord_num` LIKE '%".$order."%' ;";
    $resp = mysql_query($sql);
    while($str = mysql_fetch_array($resp)){
        $q_sql = "UPDATE `shipping` SET `ord_num` = '".str_replace($order,$new_order,$str['ord_num'])."' WHERE `id` = '".$str['id']."' ;";
        mysql_query($q_sql);
        echo $q_sql.'<br>';
    }

    # pdtion.order_num
    $sql = "SELECT * FROM `pdtion` WHERE `order_num` LIKE '%".$order."%' ;";
    $resp = mysql_query($sql);
    while($str = mysql_fetch_array($resp)){
        $q_sql = "UPDATE `pdtion` SET `order_num` = '".str_replace($order,$new_order,$str['order_num'])."' WHERE `id` = '".$str['id']."' ;";
        mysql_query($q_sql);
        echo $q_sql.'<br>';
    }

    # s_order.order_num,combine,dept = J1
    $sql = "SELECT * FROM `s_order` WHERE `order_num` LIKE '%".$order."%' OR `marked` LIKE '%".$order."%' ;";
    $resp = mysql_query($sql);
    while($str = mysql_fetch_array($resp)){
        $q_sql = "UPDATE `s_order` SET 
        `order_num` = '".str_replace($order,$new_order,$str['order_num'])."' ,
        `marked` = '".str_replace($order,$new_order,$str['order_num'])."' ,
        `combine` = '".str_replace($order,$new_order,$str['combine'])."' ,
        `dept` = 'LY' 
        WHERE `id` = '".$str['id']."' ;";
        mysql_query($q_sql);
        echo $q_sql.'<br>';
    }

    # smpl_ord.orders,dept = J1
    $sql = "SELECT * FROM `smpl_ord` WHERE `orders` LIKE '%".$order."%' ;";
    $resp = mysql_query($sql);
    while($str = mysql_fetch_array($resp)){
        $q_sql = "UPDATE `smpl_ord` SET 
        `orders` = '".str_replace($order,$new_order,$str['orders'])."' ,
        `dept` = 'LY' 
        WHERE `id` = '".$str['id']."' ;";
        mysql_query($q_sql);
        echo $q_sql.'<br>';
    }

    # wi.wi_num,style_code,dept = J1
    $sql = "SELECT * FROM `wi` WHERE `wi_num` LIKE '%".$order."%' ;";
    $resp = mysql_query($sql);
    while($str = mysql_fetch_array($resp)){
        $q_sql = "UPDATE `wi` SET 
        `wi_num` = '".str_replace($order,$new_order,$str['wi_num'])."' ,
        `style_code` = '".str_replace($order,$new_order,$str['style_code'])."' ,
        `dept` = 'LY' 
        WHERE `id` = '".$str['id']."' ;";
        mysql_query($q_sql);
        echo $q_sql.'<br>';
    }

} 


exit; 














# 修改排產時間
// exit;
/* $sel_sql="select * from schedule WHERE fty = 'HJ' ;";
// $sel_sql="select * from schedule WHERE fty = 'LY' ;";
// $sel_sql="select * from schedule WHERE fty = 'CF' ;";

echo $sel_sql.'<br>';
$res = mysql_query($sel_sql);

// $s1_date = '2000-01-01';
// $s2_date = '2011-12-31';

// $s1_date = '2012-01-01';
// $s2_date = '2012-12-31';

// $s1_date = '2013-01-01';
// $s2_date = '2013-12-31';

// $s1_date = '2014-01-01';
// $s2_date = '2014-12-25';

// $s1_date = '2014-12-25';
// $s2_date = '2015-01-01';

// $s1_date = '2015-01-01';
// $s2_date = '2015-01-05';

// $s1_date = '2015-01-05';
// $s2_date = '2015-01-10';

// $s1_date = '2015-01-10';
// $s2_date = '2015-01-15';

// $s1_date = '2015-01-15';
// $s2_date = '2015-01-20';

// $s1_date = '2015-01-21';
// $s2_date = '2015-01-31';

// $s1_date = '2015-02-01';
// $s2_date = '2015-02-10';

// $s1_date = '2015-02-11';
// $s2_date = '2015-02-20';

// $s1_date = '2015-02-21';
// $s2_date = '2015-02-31';

$s1_date = '2015-03-01';
$s2_date = '2015-03-10';

while( $value = mysql_fetch_array($res) ){

    $sel_sqls="select MAX(`saw_out_put`.`out_date`) as `m_b` , MIN(`saw_out_put`.`out_date`) as `m_m` , `s_order`.`etd` 
    from `saw_out_put` , `s_order` , `schedule` 
    where `schedule`.`ord_num` = `s_order`.`order_num` AND `saw_out_put`.`ord_num` = `s_order`.`order_num` AND `saw_out_put`.`s_id` = '".$value['id']."' 
    group by `s_order`.`id`
    ;";
    
    #echo $sel_sqls.'<br>';   
    $ress = mysql_query($sel_sqls);
    $values = mysql_fetch_array($ress);

    if ( !empty($values['m_b'])) {
        #echo $values['m_b'].' ~ '.$values['m_m'].'<br>';
        $new_sql_str = " update schedule set `ets` = '".$values['m_m']."' , `etf` = '".$values['m_b']."' , `status` = '2' WHERE `id` = '".$value['id']."' ";
        mysql_query($new_sql_str);
        #echo $new_sql_str.'<p>';    
    } else {
    
        $sel_sqls="select `s_order`.`etd` 
        from `s_order`
        where `s_order`.`order_num` = '".$value['ord_num']."' 
        ;";
        #echo $sel_sqls.'<br>';   
        $ress = mysql_query($sel_sqls);
        $values = mysql_fetch_array($ress);
        #echo $values['etd'].'<br>';
        if ( !empty($values['etd']) && $values['etd'] >= $s1_date && $values['etd'] <= $s2_date ) {
            $new_sql_str = " update schedule set `ets` = '".$s1_date."' , `etf` = '".$s2_date."' , `status` = '2' WHERE `id` = '".$value['id']."' ";
            mysql_query($new_sql_str);
            echo $new_sql_str.'<p>';   
        }
    }
}

echo '<br>OK!!~';
exit; */


# 修改 pdtion 欄位 ext_qty , ext_su 計算錯誤 
// echo '<br>修改 pdtion 欄位 ext_qty , ext_su 計算錯誤<br>';
// exit;

// $sql = "SELECT * FROM `s_order` WHERE `etd` LIKE '2015%' ;";
// $res = mysql_query($sql);
// while($tmp = mysql_fetch_array($res)){
    // $num = $tmp['order_num'];

    // $q_str = "SELECT 
    // sum(`ext_qty`) as `ext_qty` , 
    // sum(`ext_su`) as `ext_su` 
    // FROM `order_partial`
    // WHERE `ord_num` = '".$num."' 
    // GROUP BY `ord_num` 
    // ;";
     // echo $q_str.'<br>';
    // $q_results = mysql_query($q_str);
    // $row = mysql_fetch_array($q_results);

    // $ext_qty = $row['ext_qty'];
    // $ext_su = $row['ext_su'];

    // $q_str = "UPDATE 
    // `pdtion` SET 
    // `ext_qty` = '".$ext_qty."' , 
    // `ext_su` = '".$ext_su."' 
    // WHERE `order_num` = '".$num."' 
    // ;";
    // mysql_query($q_str);
    // echo $q_str.'<br>';

// }
// exit;




// # Kathy 抓資料
// exit;

// $sel_sql="
// SELECT `s_order`.`order_num`,`s_order`.`qty`,`s_order`.`su`,date_format(`s_order`.`etd`,'%Y-%m') as `date` ,
// `s_order`.`ie1` , `s_order`.`ie2` , `s_order`.`uprice` 
// FROM `s_order` 
// WHERE 
// `s_order`.`factory` = 'LY' AND 
// `s_order`.`status` >= '4' AND 
// `s_order`.`status` != '5' AND 
// `s_order`.`status` != '13' AND 
// `s_order`.`etd` BETWEEN '2013-01-01' AND '2014-12-31' 
// ORDER BY `date` ASC 
// ";

// echo $sel_sql.'<br>';    
// $res = mysql_query($sel_sql);
// $m=0;
// echo 'order_num,FOB@,Amount,qty,su,ETD,calc. IE,Final IE,Output Qty,Output SU,Ship qty,Ship SU,Ship Date<br>';
// while( $str = mysql_fetch_array($res) ){
    // $m++;
    // $sel_sqls = "SELECT sum(`shipping_doc_qty`.`ttl_qty`) as `qty` , `shipping_doc`.`ship_date` FROM `shipping_doc` , `shipping_doc_qty`  WHERE `shipping_doc_qty`.`ord_num` = '".$str['order_num']."' AND `shipping_doc_qty`.`s_id` = `shipping_doc`.`id` GROUP By `shipping_doc_qty`.`s_id`;";
    // #echo $sel_sqls.'<br>';   
    // $ress = mysql_query($sel_sqls);
    // $values = mysql_fetch_array($ress);
    // $shipping_qty = $values['qty'];
    // $ship_date = $values['ship_date'];

    // $new_sql_str = "SELECT sum(saw_out_put.qty) as qty FROM saw_out_put WHERE `ord_num` = '".$str['order_num']."';";
    // $ress = mysql_query($new_sql_str);
    // $values = mysql_fetch_array($ress);
    // $out_put_qty = $values['qty'];
    // $ie = $str['ie2'] > 0 ? $str['ie2'] : $str['ie1'] ;
    // echo $str['order_num'].','.$str['uprice'].','.($str['uprice']*$str['qty']).','.$str['qty'].','.$str['su'].','.$str['date'].','.$str['ie1'].','.$str['ie2'].','.$out_put_qty.','.$ie*$out_put_qty.','.$shipping_qty.','.$ie*$shipping_qty.','.$ship_date.'<br>';
// }

// exit;

// # 修改裁減數量
// exit;

// $sel_sql="select ord_num from cutting_out WHERE p_id = '' GROUP by ord_num";

// echo $sel_sql.'<br>';    
// $res = mysql_query($sel_sql);
// $m=0;
// while( $str = mysql_fetch_array($res) ){
    // $m++;
    // $sel_sqls="select id from order_partial where ord_num = '".$str['ord_num']."' AND `mks` = 'A' ;";
    // echo $sel_sqls.'<br>';   
    // $ress = mysql_query($sel_sqls);
    // $values = mysql_fetch_array($ress);

    // $new_sql_str = " update cutting_out set `p_id` = '".$values['id']."' WHERE `ord_num` = '".$str['ord_num']."' ";
    // mysql_query($new_sql_str);
    // echo $m.'. '.$new_sql_str.'<p>';    
// }


// exit;



# 修改Partial裁減數量加總
// exit;
// $sel_sql="select p_id , sum(qty) as qty , sum(su) as su , ord_num
// from cutting_out 
// GROUP by cutting_out.ord_num
// ";

// echo $sel_sql.'<br>';    
// $res = mysql_query($sel_sql);
// $m=0;
// while( $str = mysql_fetch_array($res) ){
    // $m++;
    // $sel_sqls="select s_order.ie1 , s_order.ie2 from s_order where order_num = '".$str['ord_num']."' ;";
    // echo $sel_sqls.'<br>';   
    // $ress = mysql_query($sel_sqls);
    // $values = mysql_fetch_array($ress);
    
    // $ie = ( $values['ie2'] > 0 ) ? $values['ie2'] : $values['ie1'] ;
    

    // #$new_sql_str = " update order_partial set `cut_qty` = '".$str['qty']."' , `cut_su` = '".$str['su']."'  , `cut_su` = '".($ie*$str['qty'])."' WHERE `id` = '".$str['p_id']."' ";
    // $new_sql_str = " update order_partial set `cut_qty` = '".$str['qty']."'  , `cut_su` = '".($ie*$str['qty'])."' WHERE `id` = '".$str['p_id']."' ";
    // mysql_query($new_sql_str);
    // echo $m.'. '.$new_sql_str.'<p>';    
// }


// exit;


# 修改 pdtion 欄位 ext_qty , ext_su 計算錯誤 
// echo '<br>修改 pdtion 欄位 ext_qty , ext_su 計算錯誤<br>';
// exit;

// $sql = "SELECT * FROM `s_order` WHERE `etd` LIKE '2015%' ;";
// $res = mysql_query($sql);
// while($tmp = mysql_fetch_array($res)){
    // $num = $tmp['order_num'];

    // $q_str = "SELECT 
    // sum(`ext_qty`) as `ext_qty` , 
    // sum(`ext_su`) as `ext_su` 
    // FROM `order_partial`
    // WHERE `ord_num` = '".$num."' 
    // GROUP BY `ord_num` 
    // ;";
     // echo $q_str.'<br>';
    // $q_results = mysql_query($q_str);
    // $row = mysql_fetch_array($q_results);

    // $ext_qty = $row['ext_qty'];
    // $ext_su = $row['ext_su'];

    // $q_str = "UPDATE 
    // `pdtion` SET 
    // `ext_qty` = '".$ext_qty."' , 
    // `ext_su` = '".$ext_su."' 
    // WHERE `order_num` = '".$num."' 
    // ;";
    // mysql_query($q_str);
    // echo $q_str.'<br>';

// }
// exit;




// # 修改排產時間
// exit;
// $sel_sql="select * from schedule WHERE fty = 'CF' ";

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

//exit;

// $sel_sql="select `order_num` , `ie1` , `ie2` from `s_order` WHERE `etd` between '2005-01-01' AND '2015-12-31';";
// echo $sel_sql.'<br>';    
// $res = mysql_query($sel_sql);

// $ie = array();
// while( $value = mysql_fetch_array($res) ){
    // $ie[$value['order_num']] = $value['ie2'] > 0 ? $value['ie2'] : $value['ie1'] ;
// }

// $sel_sql="select `id` , `ord_num` , `su` , `qty` , `work_qty` , `over_qty` from `saw_out_put` WHERE `holiday` = '0' AND `out_date` between '2011-01-01' AND '2015-12-31';";
// echo $sel_sql.'<br>';    
// $res = mysql_query($sel_sql);
// while( $value = mysql_fetch_array($res) ){

    // if( $value['su'] != ( number_format($value['qty']*$ie[$value['ord_num']],'','','') ) ){
        // echo $value['id'] . ' = ' . $value['ord_num'].' [ ' . $value['su'] . ' != ' .number_format($value['qty']*$ie[$value['ord_num']],'','','') . ' ] ' . $value['qty'] . ' * ' .$ie[$value['ord_num']].'~'.$value['ord_num'].'<br>'; 
    // }
    
    // if( $value['su'] != ( number_format( ($value['work_qty'] + $value['over_qty'] )*$ie[$value['ord_num']],'','','') ) ){
        // echo $value['id'] . ' = ' . $value['ord_num'].' [ ' . $value['su'] . ' != ' .number_format($value['qty']*$ie[$value['ord_num']],'','','') . ' ] ' . $value['qty'] . ' * ' .$ie[$value['ord_num']].'~'.$value['ord_num'].'<br>'; 
        // echo $value['work_qty'] . ' = ' .number_format($value['work_qty']*$ie[$value['ord_num']],'','','') . '  ~  ' . $value['over_qty'] . ' = ' .number_format($value['over_qty']*$ie[$value['ord_num']],'','','').'~'.$ie[$value['ord_num']].'~'.$value['ord_num'].'<br>'; 
        // echo $value['su'] . ' = ' .number_format(($value['over_qty']+$value['work_qty'])*$ie[$value['ord_num']],'','','') . '  ~  ' . $value['over_qty'] . ' = ' .number_format($value['over_qty']*$ie[$value['ord_num']],'','','').'~'.$ie[$value['ord_num']].'~'.$value['ord_num'].'<br>'; 
        // echo $value['su'] . ' = ' .(number_format($value['work_qty']*$ie[$value['ord_num']],'','','')+number_format(($value['over_qty'])*$ie[$value['ord_num']],'','','')) . '  ~  ' . $value['over_qty'] . ' = ' .number_format($value['over_qty']*$ie[$value['ord_num']],'','','').'~'.$ie[$value['ord_num']].'~'.$value['ord_num'].'<br>'; 
    // }
    
    
    // if( $value['qty'] != ( $value['work_qty'] + $value['over_qty'] ) ){
        // echo $value['id'] . ' = ' . $value['ord_num'].' [ ' . $value['qty'] . ' != ' . $value['work_qty'] . ' + ' .$ie[$value['over_qty']].'~'.$value['ord_num'].'<br>'; 
    // }

    // if ( !empty($ie[$value['ord_num']])) {
        // $new_sql_str = " update `saw_out_put` set `su` = '".number_format($value['qty']*$ie[$value['ord_num']],'','','')."'  WHERE `id` = '".$value['id']."' ". $value['qty'] . ' * ' .$ie[$value['ord_num']].'~'.$value['ord_num'];
        // $new_sql_str = " update `saw_out_put` set `su` = '".number_format($value['qty']*$ie[$value['ord_num']],'','','')."'  WHERE `id` = '".$value['id']."' ";
        // mysql_query($new_sql_str);
        // echo $new_sql_str.'<p>';
    // }

// }

// DELETE FROM `saw_out_put` WHERE `qty` = '0' OR `su`  = '0'

//exit;

// $sel_sql="select `ord_num` , sum(`p_qty_done`) as `qty_done` from `order_partial` WHERE `p_qty_done` = '0' GROUP BY `ord_num` ;";
// $sel_sql="select `order_num` from `pdtion` WHERE `ets` between '2014-07-01' AND '2014-09-31';";
// echo $sel_sql.'<br>';    
// $res = mysql_query($sel_sql);

// while( $value = mysql_fetch_array($res) ){

    // $sel_sql="select `ord_num` , sum(`p_qty_done`) as `qty_done` from `order_partial` WHERE  `ord_num` = '".$value['order_num']."' GROUP BY `ord_num` ;";
    // echo $sel_sql.'<br>';   
    // $ress = mysql_query($sel_sql);
    // $values = mysql_fetch_array($ress);

    // if ( !empty($values['qty_done'])) {

        // $new_sql_str = " update `pdtion` set `qty_done` = '".$values['qty_done']."'  WHERE `order_num` = '".$values['ord_num']."' ";
        // mysql_query($new_sql_str);
        // echo $new_sql_str.'<p>';    
    // }
// }
 
// exit;


// $sel_sql="select * from schedule WHERE fty = 'CF' ";

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




// # 修改廠別錯誤
// $sel_sql="SELECT * FROM `pdt_saw_line` WHERE `del_mk` = '0' ; ";
// echo $sel_sql.'<br>';    
// $re = mysql_query($sel_sql);
// while( $val = mysql_fetch_array($re) ){

    // $new_sql_str = " update `saw_out_put` set `saw_fty` = '".$val['fty']."' WHERE `line_id` = '".$val['id']."' ";
    // mysql_query($new_sql_str);
    // echo $new_sql_str.'<br>';    
    
// }
// exit;


# 修改產出紀錄所缺少的編制人數紀錄
// $sel_sql="SELECT * FROM `pdt_saw_line` ";
// echo $sel_sql.'<br>';    
// $re = mysql_query($sel_sql);
// while( $val = mysql_fetch_array($re) ){

    // $new_sql_str = " update `saw_out_put` set `attendence` = '".$val['worker']."' WHERE `line_id` = '".$val['id']."' ";
    // mysql_query($new_sql_str);
    // echo $new_sql_str.'<br>';    
    
// }
// exit;









// $sel_sql="select * from `s_order` ;";
// echo $sel_sql.'<br>';    
// $res = mysql_query($sel_sql);
// while( $value = mysql_fetch_array($res) ){
    // print_r($value);
// }

// exit;

# 修改 APB 補新增狀態
// T/T before shipment => 1,2               = 2
// T/T before shipment 驗收 => 3,4          = 4
// T/T after shipment => 5,6
// L/C at sight => 7,8
// 月結 (工廠) => 9,10                      = 2  ( receive.tw_rcv = 0 )
// 月結 (台灣) => 11,12                     = 2  ( receive.tw_rcv = 1 )
 // before|% 貨前貨後 的 貨前 13,14
 // %|after 貨前貨後 的 貨後 15,16
 
 // exit;
 
// $sel_sql="select * from `apb`";
// echo $sel_sql.'<br>';    
// $res = mysql_query($sel_sql);
// while( $value = mysql_fetch_array($res) ){

    // if ( $value['payment'] == 'T/T after shipment' ) {
        // $status = $value['status'] == 0 ? 5 : 6;
    // } else if ( $value['payment'] == 'L/C at sight' ) {
        // $status = $value['status'] == 0 ? 7 : 8;
    // } else if ( $value['payment'] == '月結' ) {
        // $sel_sqls="SELECT `rcvd_num` FROM `apb_det` WHERE `rcv_num` = '".$value['rcv_num']."' LIMIT 0 , 1 ";
        // $ress = mysql_query($sel_sqls);
        // $values = mysql_fetch_array($ress);
        // echo $sel_sqls.'<br>';  
        // $sel_sqls="select `tw_rcv` from `receive` where `rcv_num` = '".$values['rcvd_num']."' ;";
        // $ress = mysql_query($sel_sqls);
        // $values = mysql_fetch_array($ress);
        // echo $sel_sqls.'<br>';
        // if ( $values[0]['tw_rcv'] == 0 ) {
            // $status = $value['status'] == 0 ? 9 : 10;
        // } else {
            // $status = $value['status'] == 0 ? 11 : 12;
        // }
        
    // } else if ( $value['payment'] == 'before|%' ) {
        // $status = $value['status'] == 0 ? 13 : 14;
    // } else if ( $value['payment'] == '%|after' ) {
        // $status = $value['status'] == 0 ? 15 : 16;
    // } else {
        // $status = $value['status'];
    // }

    // $new_sql_str = " update `apb` set `status` = '".$status."' WHERE `id` = '".$value['id']."' ";

    // mysql_query($new_sql_str);
    // echo $new_sql_str.'<p>';  
    
// }


// exit;


// # 修改 APB 補新增資料 mat_id + size
// $sel_sql="select * from `apb_det` WHERE `mat_id` = '0' ";
// echo $sel_sql.'<br>';    
// $res = mysql_query($sel_sql);
// while( $value = mysql_fetch_array($res) ){
    // $sel_sqls="select `mat_id` , `size` from `ap_det` where `po_spare` = '".$value['po_id']."' ;";
    // $ress = mysql_query($sel_sqls);
    // $values = mysql_fetch_array($ress);
      
    // $new_sql_str = " update `apb_det` set `mat_id` = '".$values['mat_id']."' , `size` = '".$values['size']."' WHERE `id` = '".$value['id']."' ";
// if(!empty($values['size']))
    // echo $sel_sqls.'<br>';
    // mysql_query($new_sql_str);
    // echo $new_sql_str.'<p>';  
    
// }


// exit;

# 塞入 AP_DET.MAT_ID.USED_ID.COLOR.SIZE     mat_id  used_id  color  size 
# 增加 order_num + wi_id 到 ap_det 

// $sel_sql="select * from `ap_det` WHERE `mat_id` = '0' ";
// echo $sel_sql.'<br>';    
// $res = mysql_query($sel_sql);
// while( $value = mysql_fetch_array($res) ){
    // if( $value['mat_cat'] == 'l' ) {
        // $sel_sqls="select `lots`.`id` as `mat_id` , `lots_use`.`id` as `used_id`, `bom_lots`.`color` as `color`, `bom_lots`.`size` as `size` from `bom_lots`,`lots_use`,`lots` where `bom_lots`.`lots_used_id` = `lots_use`.`id` AND `lots_use`.`lots_code` = `lots`.`lots_code` AND `bom_lots`.`id` = '".$value['bom_id']."' ;";
        // $ress = mysql_query($sel_sqls);
        // $values = mysql_fetch_array($ress);

        
        
        // $new_sql_str = " update `ap_det` set `mat_id` = '".$values['mat_id']."' , `used_id` = '".$values['used_id']."' , `color` = '".$values['color']."' , `size` = '".$values['size']."' WHERE `id` = '".$value['id']."' ";
    // } else {
        // $sel_sqls="select `acc`.`id` as `mat_id` , `acc_use`.`id` as `used_id`, `bom_acc`.`color` as `color`, `bom_acc`.`size` as `size` from `bom_acc`,`acc_use`,`acc` where `bom_acc`.`acc_used_id` = `acc_use`.`id` AND `acc_use`.`acc_code` = `acc`.`acc_code` AND `bom_acc`.`id` = '".$value['bom_id']."' ;";
        // $ress = mysql_query($sel_sqls);
        // $values = mysql_fetch_array($ress);
        
        
        // $new_sql_str = " update `ap_det` set `mat_id` = '".$values['mat_id']."' , `used_id` = '".$values['used_id']."' , `color` = '".$values['color']."' , `size` = '".$values['size']."' WHERE `id` = '".$value['id']."' ";
    // }
    // echo $sel_sqls.'<br>';
    // mysql_query($new_sql_str);
    // echo $new_sql_str.'<p>';  
    
// }


// exit;





# 增加 order_num + wi_id 到 ap_det

// $ord = array();
// $sel_sql="select id,bom_id,mat_cat from ap_det WHERE `order_num` = '' ";
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



// echo "Close";exit;





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


    
    
    
    
    
    
    
# 修正 PDTION

exit;

function countDays ($begdate,$enddate) {

	if( ($begdate=='0000-00-00') || ($enddate=='0000-00-00') || !$begdate || !$enddate ){
		return 0;
	}
    // return round(abs(strtotime($begdate)-strtotime($enddate))/86400);
    return round((strtotime($enddate)-strtotime($begdate))/86400);
}
function distri_month_su($T_su, $s_date, $f_date, $fty='', $cat='', $mode=0,$add_check=1) {

    if($mode==1) { $factor = -1; } else { $factor = 1; }  // 加入 正負值 視需要-- 減或加 capaci
    $div = array();
    $distribute ='';   // 做為csv 變數

    list($s_year,$s_mon,$s_day) = split("-",$s_date);  // 開始日
    list($f_year,$f_mon,$f_day) = split("-",$f_date);  // 結束日
    
    $days =	countDays($s_date,$f_date);
    // $T_su = $su;		// 總 su 數
    if ($days==0)$days=1;
    $day_su = $T_su/$days;		// 每日產出 --(偏小)

    // 計算總共有幾個月份?
    $y = $f_year - $s_year;
    $m = 12*$y + (12-$s_mon+1) - (12-$f_mon);

    $su_year = $s_year;	// 年份的計數器:: 開始設訂為起頭年
    $su_mon = $s_mon;	// 月份的計數器:: 開始設定為起頭月

    $divered_su =0;	// 已經統計的su數 做為最後月之減項

    for ($i=0; $i<$m; $i++){
        if($su_mon >12){     // 計數器預到年底時
            $su_year = $su_year+1;
            $su_mon = 1;
        }

        $mon = sprintf("%04d%02d", $su_year, $su_mon);   // 月份的標記

        // 計算每月的天數 ---- 將 su 分配進入
        if($s_mon==$f_mon){   // 如果開始和最後是同月份時-----
            $d = $f_day - $s_day ;
            $su = $T_su;
        }else{
            if ($i==0){  // 第一個月
                $d = getDaysInMonth($su_mon,$su_year)- intval($s_day);
                $su = intval($day_su * $d);
            } elseif($i==$m-1){  // 最後一個月
                $d = intval($f_day);
                $su = $T_su - $divered_su;
            } else{
                $d = getDaysInMonth($su_mon,$su_year);
                $su = intval($day_su * $d);
            }
        }

        $divered_su = $divered_su + $su; 
        $su_mon = $su_mon+1;
        $tmp_m = $mon;
        $div[$tmp_m] = $su;   // 置入 array 

        # #####============ 加入 capacity ->    #########################

        $su_m = substr($mon,4);
        $su = $su * $factor; 	// 加入正負值 2005/11/21

        $distribute = $distribute.','.$mon.$su;
    }

    $distribute = substr($distribute,1);  // 去除開頭的',' 符號

    // 傳回的參數為一個 csv 如: 2005071200,200508850,
	return $distribute;

} // end func
function getDaysInMonth($month,$year)	{
    $daysInMonth = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
   
   if ($month < 1 || $month > 12)     return 0;
   $d = $daysInMonth[$month - 1];
   if ($month == 2){
      if ($year%4 == 0){
          if ($year%100 == 0){
             if ($year%400 == 0){
                $d = 29; 
             }
          } else {
               $d = 29;
          }
      }
   }
   return $d;
}

// include_once("/lib/class.order.php");

# 修改 pdtion 欄位 ext_qty , ext_su 計算錯誤 
// echo '<br>修改 pdtion 欄位 ext_qty , ext_su 計算錯誤<br>';
// exit;

$sql = "SELECT * FROM `s_order` WHERE `etd` LIKE '2015-%' AND factory = 'CF' AND
`status` > '4' AND
`status` != '-1' AND
`status` != '5' AND
`status` != '13' AND
`status` != '14' 
;";
 // AND `order_num` = 'ALR14-1099' 
$res = mysql_query($sql);
while($tmp = mysql_fetch_array($res)){
    $order_num = $tmp['order_num'];

    $q_str = "SELECT 
    min(`ets`) as `ext_ets` , max(`etf`) as `ext_etf` , min(`rel_ets`) as `rel_ets` , max(`rel_etf`) as `rel_etf` , sum(`qty`) as `ext_qty`  , sum(`su`) as `ext_su` , min(`pre_ets`) as `pre_ets` , max(`pre_etf`) as `pre_etf`
    FROM `schedule`
    WHERE `ord_num` = '".$order_num."' 
    GROUP BY `ord_num` 
    ;";

    $q_results = mysql_query($q_str);
    if( $row = mysql_fetch_array($q_results) ) {
        $pre_ets = $row['pre_ets'];
        $pre_etf = $row['pre_etf'];
        $rel_ets = $row['rel_ets'];
        $rel_etf = $row['rel_etf'];
        $ext_ets = $row['ext_ets'] != '0000-00-00'?$row['ext_ets']:$rel_ets;
        $ext_etf = $row['ext_etf'] != '0000-00-00'?$row['ext_etf']:$rel_etf;
        // $rel_ets = $rel_ets != '0000-00-00'?$rel_ets:$ext_ets;
        // $rel_etf = $rel_etf != '0000-00-00'?$rel_etf:$ext_etf;
        $ext_qty = $row['ext_qty'];
        $ext_su =  $row['ext_su'];
        $ext_period = countDays($ext_ets,$ext_etf);
    } else {
        $pre_ets = $pre_etf = $ext_ets = $ext_etf = $ext_qty = $ext_su = $ext_period = NULL;
    }

    $q_str = "SELECT 
    min(`p_etp`) as `ets` , max(`p_etd`) as `etf` , min(`p_ets`) as `rel_ets` , max(`p_etf`) as `rel_etf` , sum(`ext_su`) as `ext_su` 
    FROM `order_partial`
    WHERE `ord_num` = '".$order_num."'  
    GROUP BY `ord_num` 
    ;";
    // echo $q_str.'<br>';
    $q_results = mysql_query($q_str);
    if( $row = mysql_fetch_array($q_results) ) {
        // $rel_ets = $row['rel_ets'];
        // $rel_etf = $row['rel_etf'];
        $ets = $row['ets'] != '0000-00-00'?$row['ets']:$rel_ets;
        $etf = $row['etf'] != '0000-00-00'?$row['etf']:$rel_etf;
        $fty_su = distri_month_su($row['ext_su'],$rel_ets,$rel_etf);
    } else {
        $rel_ets = $rel_etf = $fty_su = NULL;
    }
    
    $q_str = "SELECT 
    sum(`work_qty`)+sum(`over_qty`) as `qty_done` 
    FROM `saw_out_put`
    WHERE `ord_num` = '".$order_num."' 
    GROUP BY `ord_num` 
    ;";

    $q_results = mysql_query($q_str);
    if( $row = mysql_fetch_array($q_results) ) {
        $qty_done = $row['qty_done'];
    } else {
        $qty_done = NULL;
    }

    $q_str = "SELECT 
    sum(`qty`) as `qty` 
    FROM `shipping`
    WHERE `ord_num` = '".$order_num."' 
    GROUP BY `ord_num` 
    ;";

    $q_results = mysql_query($q_str);
    if( $row = mysql_fetch_array($q_results) ) {
        $qty_shp = $row['qty'];
    } else {
        $qty_shp = NULL;
    }

    $q_str = "UPDATE 
    `pdtion` SET 
    `fty_su`        = '".$fty_su."' , 
    `ets`           = '".$ets."' , 
    `etf`           = '".$etf."' , 
    `pre_ets`       = '".$pre_ets."' , 
    `pre_etf`       = '".$pre_etf."' , 
    `rel_ets`       = '".$rel_ets."' , 
    `rel_etf`       = '".$rel_etf."' , 
    `qty_done`      = '".$qty_done."' , 
    `qty_shp`       = '".$qty_shp."' , 
    `ext_ets`       = '".$ext_ets."' , 
    `ext_etf`       = '".$ext_etf."' , 
    `ext_period`    = '".$ext_period."' , 
    `ext_qty`       = '".$ext_qty."' , 
    `ext_su`        = '".$ext_su."' 
    WHERE `order_num` = '".$order_num."' 
    ;";

    echo $q_str.'<br>';
    mysql_query($q_str);
    
$q_str = "
SELECT `s_order`.`qty` , `s_order`.`status` , `pdtion`.`qty_shp` , `pdtion`.`qty_done` , `pdtion`.`ext_qty` 
FROM `s_order`,`pdtion` 
WHERE `s_order`.`order_num` = `pdtion`.`order_num` AND `pdtion`.`order_num` = '".$order_num."' 
;";

if (!$q_result = mysql_query($q_str)) {
    return false;    
}
// echo $q_str.'<br>';
if( $row = mysql_fetch_array($q_result) ) {
    
    if( $row['qty_shp'] > 0 ) {
        $status = 12 ;
    } else if ( $row['qty_done'] >= $row['qty'] ) {
        $status = 10;
    } else if ( $row['qty_done'] > 0 ) {
        $status = 8;
    } else if ( $row['ext_qty'] >= $row['qty'] ) {
        $status = 7;
    } else if ( $row['ext_qty'] > 0 ) {
        $status = 6;
    } else if ( $row['status'] == '-1' && $row['status'] == '5' && $row['status'] == '13' && $row['status'] == '14' ) {
        $status = $row['status'];
    } else {
        $status = $row['status'];
    }

    $q_str =  "UPDATE `s_order` SET `status` = '".$status."' WHERE `order_num` = '".$order_num."' ;";
    mysql_query($q_str);
    echo $q_str.'<p>';

}
    

}
exit;    


?>