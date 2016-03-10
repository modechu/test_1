<?php
session_start();
session_register	('SCACHE');
session_register	('PAGE');
session_register	('authority');
session_register	('where_str');
session_register	('parm');
session_register	('PHP_ses_etd');
session_register	('PHP_unstatus');
// error_reporting(1);
##################  2004/11/10  ########################
#			index.php  主程式
#		for Carnival SCM [Sample]  management
#			Jack Yang     2004/11/10
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

require_once "config.php";
require_once "config.admin.php";
$PHP_SELF = $_SERVER['PHP_SELF'];
$perm = $GLOBALS['power'];
require_once "init.object.php";
$op = array();

$TPL_APB_QTY_SHOW = "apb_qty_show.html";
$TPL_PO_BEFORE_APB_LIST = "po_before_apb_list.html";
$TPL_PO_BEFORE_APB_ADD = "po_before_apb_add.html";
$TPL_PO_RCV_APB_SHOW = "po_rcv_apb_show.html";
$TPL_PO_RCV_APB_EDIT = "po_rcv_apb_edit.html";
$TPL_PO_RCV_LIST = "po_rcv_list.html";
$TPL_PO_RCV_ADD = "po_rcv_add.html";
$TPL_APB_AFTER_LIST = "apb_after_list.html";
$TPL_APB_AFTER_ADD = "apb_after_add.html";
$TPL_APB_AFTER_SHOW = "apb_after_show.html";
$TPL_APB_AFTER_EDIT = "apb_after_edit.html";
$TPL_APB_LC_LIST = "apb_lc_list.html";
$TPL_APB_LC_ADD = "apb_lc_add.html";
$TPL_APB_LC_SHOW = "apb_lc_show.html";
$TPL_APB_LC_EDIT = "apb_lc_edit.html";
$TPL_APB_MONTH_FTY_LIST = "apb_month_fty_list.html";
$TPL_APB_MONTH_FTY_ADD = "apb_month_fty_add.html";
$TPL_APB_MONTH_FTY_SHOW = "apb_month_fty_show.html";
$TPL_APB_MONTH_FTY_EDIT = "apb_month_fty_edit.html";
$TPL_APB_MONTH_TW_LIST = "apb_month_tw_list.html";
$TPL_APB_MONTH_TW_ADD = "apb_month_tw_add.html";
$TPL_APB_MONTH_TW_SHOW = "apb_month_tw_show.html";
$TPL_APB_MONTH_TW_EDIT = "apb_month_tw_edit.html";
$TPL_APB_BEFORE_AFTER_LIST = "apb_before_after_list.html";
$TPL_APB_BEFORE_AFTER_ADD = "apb_before_after_add.html";
$TPL_APB_BEFORE_AFTER_SHOW = "apb_before_after_show.html";
$TPL_APB_BEFORE_AFTER_EDIT = "apb_before_after_edit.html";
$TPL_APB_AFTER_RCV_LIST = "apb_after_rcv_list.html";
$TPL_APB_AFTER_RCV_ADD = "apb_after_rcv_add.html";
$TPL_APB_PAY_SHOW = "apb_pay_show.html";
$TPL_APB_AFTER_RCV_SHOW = "apb_after_rcv_show.html";
$TPL_APB_AFTER_RCV_EDIT = "apb_after_rcv_edit.html";

#
#
$AUTH = '040';
// echo $PHP_action.'<br>';
switch ($PHP_action) {
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "APB":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "apb":
check_authority($AUTH,"view");

$op['d_date'] = date("d");

if( $op['d_date'] < 26 ) {
    $payment = array(
        "T/T before shipment",
        "T/T after shipment",
        "Input T/T of shipment separately the BEFORE & AFTER",
        "L/C at sight",
        "月結"
    );
} else {
	$payment = array("T/T before shipment");
}

$op['payment']= $arry2->select($payment,$payment[0],"PHP_payment","select","chk(this.value,".date('d').")");
$dept=$GLOBALS['SCACHE']['ADMIN']['dept'];

$op['manager_flag']=1;
$where_str ='';
$where_str=" where status = 12 AND rcv_rmk = 0";
for ($i=0; $i<sizeof($SHIP); $i++) {
	if ($dept==$SHIP[$i]) {
		$op['manager_flag']=0;
		$op['fty']= $dept;
	}
}

$where_str = "ORDER BY supl_s_name";
$sup=$supl->get_fields('vndr_no',$where_str);
$sup_name=$supl->get_fields('supl_s_name',$where_str);

if(!$sup)$sup=array('');
if(!$sup_name) $sup_name=array();
$op['sup_no'] = $arry2->select($sup_name,"","PHP_sup","select","",$sup);
$op['sup_supl'] = $arry2->select($sup_name,"","SCH_supl","select","",$sup);

if (isset($PHP_msg))$op['msg'][]=$PHP_msg;
$op['rcvd'] = $apb->get_apb_check_list();

$note=$notify->search($GLOBALS['SCACHE']['ADMIN']['login_id'], "`read`=0");
$op['max_notify'] = $note['max_no'];

page_display($op, $AUTH, $TPL_APB);
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "APB": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "rcvd_search":
check_authority($AUTH,"view");

$op = $apb->search(1);

$op['back_str'] = "&SCH_num=".$SCH_num."&SCH_supl=".$SCH_supl."&SCH_po=".$SCH_po."&SCH_ship=".$SCH_ship;
$op['msg'] = $apb->msg->get(2);
if(isset($PHP_msg)) $op['msg'][] = $PHP_msg;
page_display($op, $AUTH, $TPL_APB_LIST);
break;				



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "rcvd_view":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "rcvd_view":
check_authority($AUTH,"view");

//$op = $apb->show_apb($PHP_rcv_num);
$op = $apb->show_apb($PHP_rcv_num,'view');
// print_r($op);
if (isset($PHP_msg))$op['msg'][]=$PHP_msg;
if(isset($PHP_sr_startno))$op['back_str'] = "&SCH_num=".$SCH_num."&SCH_supl=".$SCH_supl."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_sr_startno=".$PHP_sr_startno;
$op['ck'] = (!empty($_GET['ck']))? $_GET['ck'] : '';
$op['date'] = $TODAY;
// echo substr($op['apb']['rcv_date'],0,7).' = '.substr($TODAY,0,7);
$op['ckdate'] = substr($op['apb']['rcv_date'],0,7) < substr($TODAY,0,7) ? '1' : '0' ;

if(!empty($PHP_resize)) $op['resize'] = 1;
page_display($op, $AUTH, $TPL_APB_SHOW);

break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "rcvd_after_view":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "rcvd_after_view":
check_authority($AUTH,"view");

$op = $apb->show_apb3($PHP_rcv_num);

$toler = explode("|",$op['po'][0]['dm_way']);
$op['apb']['payment'] = $toler[0]."% TT before shipment, ".$toler[1]."% TT after shipment";

if (isset($PHP_msg))$op['msg'][]=$PHP_msg;
if(isset($PHP_sr_startno))$op['back_str'] = "&SCH_num=".$SCH_num."&SCH_supl=".$SCH_supl."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_sr_startno=".$PHP_sr_startno;
$op['ck'] = (!empty($_GET['ck']))? $_GET['ck'] : '';
$op['date'] = $TODAY;
// echo substr($op['apb']['rcv_date'],0,7).' = '.substr($TODAY,0,7);
$op['ckdate'] = substr($op['apb']['rcv_date'],0,7) < substr($TODAY,0,7) ? '1' : '0' ;
if(!empty($PHP_resize)) $op['resize'] = 1;

//print_r($op);
page_display($op, $AUTH, $TPL_APB_AFTER_SHOW);

break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "rcvd_add_search":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "rcvd_add_search":
check_authority($AUTH,"add");

# T/T before shipment       請款        1,2
# T/T before shipment       驗收        3,4
# T/T after shipment        驗收付款    5,6
# Input T/T of the BEFORE   驗收付款    7,8
# Input T/T of the AFTER    驗收付款    9,10
# L/C at sight              請款        11,12 先進行驗收，請款放二階
# L/C at sight              驗收        13,14 直接無條件驗收
# 月結                      驗收付款    15,16

# 請款 = 無 SHIP 先行請款
# 驗收 = 驗收已請款
# 驗收付款 = 已驗收但未申請付款


switch ($PHP_payment) {

case 'T/T before shipment':
if($PHP_po_before_rcv){
    echo 'T/T before shipment 請款    1,2<br>';
    $op = $apb->search_po_rcv($PHP_payment);
} else {
    echo 'T/T before shipment 驗收    3,4<br>';
    $op = $apb->search_tt_before($PHP_payment);
}
break;

case 'T/T after shipment':
echo 'T/T after shipment 驗收付款  5,6<br>';
$op = $apb->search_after_rcv($PHP_payment);
break;

case 'Input T/T of shipment separately the BEFORE & AFTER':
if($PHP_before_after=="before"){
    echo 'Input T/T of shipment separately the BEFORE 驗收付款    7,8<br>';
    $op = $apb->search_po_before('%|%');
} else {
    echo 'Input T/T of shipment separately the AFTER 驗收付款    9,10<br>';
    $op = $apb->search_before_after_rcv();
}
break;

case 'L/C at sight':
echo 'L/C at sight 驗收  13,14<br>';
$op = $apb->search_lc_rcv($PHP_payment);
break;

case '月結':
echo '月結 驗收付款  15,16<br>';
$op = $apb->search_month_fty_rcv($PHP_payment);
break;

}

$op['back_str'] = "&PHP_ship_inv=".$PHP_ship_inv."&PHP_po=".$PHP_po."&PHP_sup=".$PHP_sup."&PHP_ord=".$PHP_ord."&PHP_payment=".urlencode($PHP_payment);


switch ($PHP_payment) {

case 'T/T before shipment':
if($PHP_po_before_rcv){
    // echo 'T/T before shipment 請款    1,2<br>';
    page_display($op, $AUTH, $TPL_PO_RCV_LIST);
} else {
    // echo 'T/T before shipment 驗收    3,4<br>';
    page_display($op, $AUTH, $TPL_APB_PO_BEFORE_LIST);
}
break;

case 'T/T after shipment':
// echo 'T/T after shipment 驗收付款  5,6<br>';
page_display($op, $AUTH, $TPL_PO_RCV_LIST);
break;

case 'Input T/T of shipment separately the BEFORE & AFTER':
if($PHP_before_after=="before"){
    // echo 'Input T/T of shipment separately the BEFORE 驗收付款    7,8<br>';
} else {
    // echo 'Input T/T of shipment separately the AFTER 驗收付款    9,10<br>';
}
break;

case 'L/C at sight':
// echo 'L/C at sight 驗收  13,14<br>';
break;

case '月結':
// echo '月結 驗收付款  15,16<br>';
break;

}



print_r($_POST);
exit;
break;

if ($PHP_before_after=="before"){ # 貨前貨後 的 貨前 狀態13,14
	$op = $apb->search_po_before('%|%');
} elseif($PHP_po_before_rcv) {	# 請款單 的 驗收
	$op = $apb->search_po_rcv($PHP_payment);
} else {
	if($PHP_payment == 'T/T before shipment'){	# 請款單
		$op = $apb->search_tt_before($PHP_payment);
	}elseif($PHP_payment == '月結' and $PHP_tw_rcv){	# 月結(台灣驗收)
		$op = $apb->search_month_tw_rcv();
	}elseif($PHP_payment == '月結' and !$PHP_tw_rcv){	# 月結(工廠驗收)
		$op = $apb->search_month_fty_rcv($PHP_payment);
	}elseif($PHP_payment == 'T/T after shipment'){	# T/T after shipment
		$op = $apb->search_after_rcv($PHP_payment);
	}elseif($PHP_payment == 'L/C at sight'){		# L/C at sight
		$op = $apb->search_lc_rcv($PHP_payment);
	}else{	# 貨前貨後 的 貨後 狀態15,16
		$op = $apb->search_before_after_rcv();
	}
}



$op['payment'] = $PHP_payment;
$op['tw_rcv'] = $PHP_tw_rcv;

if ( $PHP_payment == 'T/T before shipment' and !$PHP_po_before_rcv){
	# 請款單 狀態 1,2
	$op['back_str'] = "&PHP_po=".$PHP_po."&PHP_ship=".$PHP_ship."&PHP_ship2=".$PHP_ship2."&PHP_sup=".$PHP_sup."&PHP_payment=".$PHP_payment."&PHP_ord=".$PHP_ord."&PHP_b_a=".$PHP_b_a."&PHP_payment=".urlencode($PHP_payment);
	page_display($op, $AUTH, $TPL_APB_PO_BEFORE_LIST);
}elseif ( $PHP_po_before_rcv){
	# 請款單 的 驗收 狀態 3,4
	$op['back_str'] = "&PHP_po=".$PHP_po."&PHP_sup=".$PHP_sup."&PHP_payment=".urlencode($PHP_payment);
	page_display($op, $AUTH, $TPL_PO_RCV_LIST);
}elseif( $PHP_payment == 'T/T after shipment' ){
	# T/T after shipment 狀態5,6
	$op['back_str'] = "&PHP_po=".$PHP_po."&PHP_sup=".$PHP_sup."&PHP_payment=".urlencode($PHP_payment);
	page_display($op, $AUTH, $TPL_APB_AFTER_LIST);
}elseif( $PHP_payment == 'L/C at sight' ){
	# L/C at sight 狀態7,8
	$op['back_str'] = "&PHP_po=".$PHP_po."&PHP_sup=".$PHP_sup."&PHP_payment=".urlencode($PHP_payment);
	page_display($op, $AUTH, $TPL_APB_LC_LIST);
}elseif( $PHP_payment == '月結' and !$PHP_tw_rcv ){
	# 月結(工廠驗收) 狀態 9,10
	$op['back_str'] = "&PHP_po=".$PHP_po."&PHP_tw_rcv=".$PHP_tw_rcv."&PHP_sup=".$PHP_sup."&PHP_payment=".urlencode($PHP_payment);
	page_display($op, $AUTH, $TPL_APB_MONTH_FTY_LIST);
}elseif( $PHP_payment == '月結' and $PHP_tw_rcv ){
	# 月結(台灣驗收) 狀態 11,12
	$op['back_str'] = "&PHP_po=".$PHP_po."&PHP_tw_rcv=".$PHP_tw_rcv."&PHP_sup=".$PHP_sup."&PHP_payment=".urlencode($PHP_payment);
	page_display($op, $AUTH, $TPL_APB_MONTH_TW_LIST);
}elseif($PHP_before_after=="before"){
	# 貨前貨後 的 貨前 狀態13,14
	$op['back_str'] = "&PHP_po=".$PHP_po."&PHP_sup=".$PHP_sup."&PHP_payment=%|%&PHP_payment=".urlencode($PHP_payment)."&PHP_ord=".$PHP_ord."&PHP_before_after=".$PHP_before_after;
	page_display($op, $AUTH, $TPL_APB_BEFORE_AFTER_LIST);
}else{
	# 貨前貨後 的 貨後 狀態15,16
	$op['back_str'] = "&PHP_ship_inv=".$PHP_ship_inv."&PHP_po=".$PHP_po."&PHP_sup=".$PHP_sup."&PHP_ord=".$PHP_ord."&PHP_payment=".urlencode($PHP_payment);
	page_display($op, $AUTH, $TPL_APB_AFTER_RCV_LIST);
}

break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		Rate Jquery
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "check_rate":

if($PHP_cury != 'NTD'){
	echo $rate->get_rate($PHP_cury,$PHP_rcv_date);
}else{
	echo '1';
}

break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "rcvd_add":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "rcvd_add":
check_authority($AUTH,"view");

if(substr($PHP_payment,0,4)=="月結")
	$op = $apb->get_tw_rcvd($PHP_sup_no,"月結",$PHP_tw_rcv);
else
	$op = $apb->get_rcvd($PHP_sup_no,$PHP_payment);
$op['currency'] =  $arry2->select($CURRENCY,$op['rcv'][0]['rcv_det'][0]['po']['currency'],'PHP_select_currency','select','change_currency()');	//幣別下拉式
$op['rcv_num'] = "AP".date('y')."-xxxx";
$op['date'] = $TODAY;
$op['dept'] = $GLOBALS['SCACHE']['ADMIN']['dept'];
$op['user'] = $GLOBALS['SCACHE']['ADMIN']['login_id'];
$op['dm_way'] = $PHP_payment;
$op['supl'] = $supl->get('',$PHP_sup_no);

$dates = $TODAY;
$op['back_str'] = "&PHP_po=".$PHP_po."&PHP_ship=".$PHP_ship."&PHP_ship2=".$PHP_ship2."&PHP_sup=".$PHP_sup."&PHP_sr_startno=".$PHP_sr_startno."&PHP_ord=".$PHP_ord;

page_display($op, $AUTH, $TPL_APB_ADD);

break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "rcvd_after_add":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "rcvd_after_add":
check_authority($AUTH,"view");

$op = $apb->get_rcvd_after($PHP_sup_no, $PHP_payment);

$toler = explode("|",$op['ap'][0]['dm_way']);
//顯示用
$op['dm_way']  = $toler[0]."% TT before shipment, ".$toler[1]."% TT after shipment";
//寫入資料庫用
$op['payment']  = "%|after";

$ck =0;
$cy = $op['ap'][0]['currency'];
$sz = sizeof($op['ap'])-1;
for($i=1;$i<=$sz;$i++){
	if( $op['ap'][$i]['currency'] <> $cy ){
		$ck =1;
		break;
	}
	
}

if( $ck ){
	$message = "以下 P/O 的幣別不同!!";
	$op['msg'][]= $message;
	$op['currency'] =  $arry2->select($CURRENCY,'','PHP_currency','select','change_currency()');	//幣別下拉式
}else{
	$op['currency'] =  $arry2->select($CURRENCY,$op['ap'][0]['currency'],'PHP_currency','select','change_currency()');	//幣別下拉式
}

$op['rcv_num'] = "AP".date('y')."-xxxx";
$op['date'] = $TODAY;
$op['dept'] = $GLOBALS['SCACHE']['ADMIN']['dept'];
$op['user'] = $GLOBALS['SCACHE']['ADMIN']['login_id'];
$op['supl'] = $supl->get('',$PHP_sup_no);

$dates = $TODAY;
$op['back_str'] = "&PHP_po=".$PHP_po."&PHP_ship=".$PHP_ship."&PHP_ship2=".$PHP_ship2."&PHP_sup=".$PHP_sup."&PHP_sr_startno=".$PHP_sr_startno."&PHP_ord=".$PHP_ord;
page_display($op, $AUTH, $TPL_APB_AFTER_ADD);

break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_po_rcv_add": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_po_rcv_add":
check_authority($AUTH,"add");

$parm = array (
"sup_no"			=>	$PHP_sup_no,
"rcv_date"			=>	$TODAY,
"rcv_user"			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
"inv_num"			=>	$PHP_inv_num,
"foreign_inv_num"	=>	$PHP_foreign_inv_num,
"inv_date"			=>	$PHP_inv_date,
"inv_price"			=>	$PHP_inv_price,
"dept"				=>	$PHP_dept,
"discount"			=>	$PHP_discount,
"off_duty"			=>	$PHP_off_duty,
"vat"				=>	$PHP_vat,
"currency"			=>	$PHP_select_currency,
"status"			=>	3,
"payment"			=>	$PHP_payment
);

$head="AP".date('y')."-";	//RP+日期=驗收單開頭
$parm['rcv_num'] = $apply->get_no($head,'rcv_num','apb');	//取得驗收的最後編號

$f1 = $apb->add($parm);

if (isset($PHP_ship_num))
{
	foreach ($PHP_ship_num as $ship_num)
	{
		if(isset($PHP_checkbox[$ship_num]))
		{
			foreach($PHP_mark[$ship_num] as $i => $mark)
			{
				if($PHP_apb_qty[$ship_num][$i] > 0){
					$parm_det = array(
					"ship_num"		=>	$ship_num,
					"inv_num"		=>	$PHP_rcv_inv[$ship_num][$i],
					"rcv_num"		=>	$parm['rcv_num'],
					"mat_code"		=>	$PHP_mat_code[$ship_num][$i],
					"qty"			=>	$PHP_apb_qty[$ship_num][$i],
					"uprices"		=>	$PHP_uprices[$ship_num][$i],
					"uprice"		=>	$PHP_uprice[$ship_num][$i],
					"color"			=>	$PHP_color[$ship_num][$i],
					"foc"			=>	$PHP_foc[$ship_num][$i],
					"ap_num"		=>	$PHP_ap_num[$ship_num][$i],
					"mat_id"		=>	$PHP_mat_id[$ship_num][$i],
					"size"			=>	$PHP_size[$ship_num][$i]
					);

					$det_id = $apb->add_det($parm_det);
					# 備註apb_num
					$apb->update_field('apb_num', $parm['rcv_num'], "type='i' and po_num='".str_replace("PA","PO",$PHP_ap_num[$ship_num][$i])."' and mat_id=".$PHP_mat_id[$ship_num][$i]." and color='".$PHP_color[$ship_num][$i]."' and size='".$PHP_size[$ship_num][$i]."' and ship_id='".$ship_num."'", "stock_inventory");
					
					$input_rate = $PHP_rate[$ship_num][$i];
					
					foreach($PHP_link_qty[$ship_num][$i] as $ord => $link_qty){
						$link_parm = array(
										"po_id"		=>	$PHP_po_id[$ship_num][$i][$ord],
										"rcv_id"	=>	$det_id,
										"qty"		=>	$link_qty,
										"currency"	=>	$PHP_select_currency,
										"amount"	=>	$PHP_uprice[$ship_num][$i] * $link_qty,
										"rate"		=>	$input_rate,
										"ord_num"	=>	$ord
										);
							$f3=$apb->add_link($link_parm);
					}
					
					# 超量備註
					/* if($PHP_reason[$i])
					{
						$parm= array(
						'rcv_num'		=>	$parm['rcv_num'],
						'item'			=>	'Overbalance.',
						'des'				=>	$PHP_reason[$i],
						'user'			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
						'k_date'		=>	$TODAY
						);
						$f4=$apb->add_log($parm);
					} */
				}
			}
		}
	}
}


#加入新增的apb_oth_cost
if(isset($PHP_des)){
	foreach($PHP_des as $k1 => $v1){
		foreach($v1 as $k2 => $v2){
			$oth_parm = array(
							"ap_num"	=>	str_replace("PO","PA",$k1),
							"item"		=>	$PHP_des[$k1][$k2],
							"cost"		=>	$PHP_cost[$k1][$k2],
							"apb_num"	=>	$parm['rcv_num'],
							"payment_status"	=>	4
							);
			$f0 = $apb->oth_cost_add($oth_parm);
		}
	}
}

$message = "successfully append Apb On : ".$parm['rcv_num'];
$op['msg'][]= $message;
$log->log_add(0,"040A",$message);

redirect_page($PHP_SELF."?PHP_action=po_rcv_view&PHP_rcv_num=".$parm['rcv_num']."&SCH_num=&SCH_supl=&SCH_fab=&SCH_acc=&PHP_sr_startno=1");
break;		

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "po_rcv_apb_edit": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "po_rcv_apb_edit":
check_authority($AUTH,"view");

$op = $apb->get_po_rcv_apb($PHP_rcv_num);

$op['date'] = $TODAY;
$op['back_str'] = "&SCH_num=".$SCH_num."&SCH_supl=".$SCH_supl."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_sr_startno=".$PHP_sr_startno;
$op['currency'] =  $arry2->select($CURRENCY,$op['apb']['currency'],'PHP_select_currency','select','change_currency()');	//幣別下拉式

$dates = $TODAY;
page_display($op, $AUTH, $TPL_PO_RCV_APB_EDIT);
break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "do_po_rcv_apb_edit": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_po_rcv_apb_edit":
check_authority($AUTH,"view");				

$parm = array (	
"rcv_date"			=>	$TODAY,
"rcv_user"			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
"inv_num"			=>	$PHP_inv_num,
"inv_date"			=>	$PHP_inv_date,
"foreign_inv_num"	=>	$PHP_foreign_inv_num,
"inv_price"			=>	$PHP_inv_price,
"discount"			=>	$PHP_discount,
"off_duty"			=>	$PHP_off_duty,
"vat"				=>	$PHP_vat,
"currency"			=>	$PHP_select_currency,
"rcv_num"			=>	$PHP_apb_num
);

$f1 = $apb->edit($parm);

	foreach($PHP_det_id as $ship_num => $val){
		if (array_key_exists($ship_num, $PHP_checkbox)){ # 更新
			foreach($val as $i => $det_id){
				if(array_key_exists($i, $PHP_mark[$ship_num])){
					$f1 = $apb->update_field_id('qty',$PHP_apb_qty[$ship_num][$i],$det_id,'apb_det');
					$f1 = $apb->update_field_id('foc',$PHP_foc[$ship_num][$i],$det_id,'apb_det');
					$f1 = $apb->update_field_id('uprice',$PHP_uprice[$ship_num][$i],$det_id,'apb_det');
					$f1 = $apb->update_field_id('uprices',$PHP_uprices[$ship_num][$i],$det_id,'apb_det');
					$f1 = $apb->update_field('currency',$PHP_select_currency,"rcv_id='".$det_id."'",'apb_po_link');
					$f1 = $apb->update_field('rate',$PHP_rate[$ship_num][$i],"rcv_id='".$det_id."'",'apb_po_link');
				
					foreach ($PHP_link_qty[$ship_num][$i] as $link_id => $link_qty) {
						$f1 = $apb->update_field_id("qty", $link_qty, $link_id, "apb_po_link");
					}
				}else{	//刪除
					$f1 = $apb->del_link($det_id);
					$f1 = $apb->del_det($det_id);
					$f1 = $apb->update_field("apb_num", "", "ship_inv='".$ship_num."' and po_num='".$PHP_po_num[$ship_num][$i]."' and mat_id='".$PHP_mat_id[$ship_num][$i]."' and color='".$PHP_color[$ship_num][$i]."' and size='".$PHP_size[$ship_num][$i]."'", "stock_inventory");
				}
			}
		}else{ # 刪除
			foreach($val as $i => $det_id){
				$f1 = $apb->del_link($det_id);
				$f1 = $apb->del_det($det_id);
				$f1 = $apb->update_field("apb_num", "", "ship_inv='".$ship_num."' and po_num='".$PHP_po_num[$ship_num][$i]."' and mat_id='".$PHP_mat_id[$ship_num][$i]."' and color='".$PHP_color[$ship_num][$i]."' and size='".$PHP_size[$ship_num][$i]."'", "stock_inventory");
			}
		}
		$f2 = $apb->chk_del_full($PHP_apb_num);
	}

if($f2){
	$message = "Delete APB : ".$PHP_rcv_num;
	$log->log_add(0,"040D",$message);
	$message = "Delete APB [".$PHP_rcv_num."]";
	$redir_str = "apb.php?PHP_action=rcvd_search".$PHP_back_str."&PHP_msg=".$message;
	redirect_page($redir_str);
}else{
	#修改apb_oth_cost
	foreach($PHP_oth_item as $id => $val){
		$apb->update_field("item", $val, "id=".$id." and apb_num='".$PHP_apb_num."'", "apb_oth_cost");
		$apb->update_field("cost", $PHP_oth_cost[$id], "id=".$id." and apb_num='".$PHP_apb_num."'", "apb_oth_cost");
	}

	#加入新增的apb_oth_cost
	if(isset($PHP_des)){
		foreach($PHP_des as $po_num => $v1){
			foreach($v1 as $k2 => $val){
				$parm = array(
							"ap_num"			=>	str_replace("PO","PA",$po_num),
							"item"				=>	$val,
							"cost"				=>	$PHP_cost[$po_num][$k2],
							"apb_num"			=>	$PHP_apb_num,
							"payment_status"	=>	4
						);
				$f0 = $apb->oth_cost_add($parm);
			}
		}
	}
}

$op = $apb->get_po_rcv_apb($PHP_apb_num);
$op['back_str'] = $PHP_back_str;
$message = "Successfully Edit Apb : ".$PHP_apb_num;
$log->log_add(0,"040E",$message);
$op['msg'][]=$message;
$op['ckdate'] = substr($op['apb']['rcv_date'],0,7) < substr($TODAY,0,7) ? '1' : '0' ;
page_display($op, $AUTH, $TPL_PO_RCV_APB_SHOW);

break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "rcvd_edit": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "rcvd_edit":
check_authority($AUTH,"view");

// $op = $apb->get_apb($PHP_rcv_num);
$op = $apb->show_apb($PHP_rcv_num);
$op['date'] = $TODAY;
$op['back_str'] = "&SCH_num=".$SCH_num."&SCH_supl=".$SCH_supl."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_sr_startno=".$PHP_sr_startno;
$op['currency'] =  $arry2->select($CURRENCY,$op['apb']['currency'],'PHP_select_currency','select','change_currency()');	//幣別下拉式

$dates = $TODAY;

page_display($op, $AUTH, $TPL_APB_EDIT);
break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "rcvd_after_edit": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "rcvd_after_edit":
check_authority($AUTH,"view");

$op = $apb->show_apb3($PHP_rcv_num);

foreach($op['po'] as $key_po => $val_po)
{
	$tmpap = array();
	$tmpap =$apb->get_array("distinct rcv_num","apb_det"," ap_num = '".$val_po['ap_num']."' AND rcvd_num <>  ''"); 
	$before_price = $apb->get_det_field("cost","apb_oth_cost","ap_num='".$val_po['ap_num']."' and item='before|%'");
	foreach($tmpap as $key_tmpap => $val_tmpap)
	{
		$op['po'][$key_po]['all_ap_num'][$key_tmpap] = $val_tmpap['rcv_num'];
	}
		
	foreach($op['po'][$key_po]['all_ap_num'] as $key_ap => $val_ap)
	{
		$ap_info = $apb->get_array("ship_num,rcvd_num,rcv_num,po_id,qty,uprice,ap_num","apb_det"," ap_num = '".$val_po['ap_num']."' AND rcv_num = '".$val_ap."' AND rcvd_num <>  ''");
		$oth_cost = $apb->get_det_field("cost","apb_oth_cost","ap_num='".$val_po['ap_num']."' and apb_num='".$val_ap."'");
		$total = 0;
		foreach($ap_info as $key_getTotal => $val_getTotal)
		{
			if($val_getTotal['rcv_num'] <> $op['apb']['rcv_num'])
			{
				$qty = $val_getTotal['qty'];
				$uprice = $val_getTotal['uprice'];
				$total += (round($qty,2) * round($uprice,2)) ;
			}
		}
		if($oth_cost['cost'] < $total )
		{
			if(($total - $oth_cost['cost']) <= $before_price['cost'] )
			{
				$before_price['cost'] = round($before_price['cost'],2) - (round($total,2) - round($oth_cost['cost'],2));
			}
			else
			{
				$before_price['cost'] = 0;
			}
		}
	}
	$op['po'][$key_po]['before_price'] = $before_price['cost'] ;
}

$toler = explode("|",$op['po'][0]['dm_way']);
$op['apb']['payment'] = $toler[0]."% TT before shipment, ".$toler[1]."% TT after shipment";

$op['date'] = $TODAY;
$op['currency'] = $arry2->select($CURRENCY,$op['apb']['currency'],'PHP_select_currency','select','change_currency()');	//幣別下拉式
$op['back_str'] = "&SCH_num=".$SCH_num."&SCH_supl=".$SCH_supl."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_sr_startno=".$PHP_sr_startno;

$dates = $TODAY;
page_display($op, $AUTH, $TPL_APB_AFTER_EDIT);
break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "do_rcvd_after_edit": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_rcvd_after_edit":

check_authority($AUTH,"edit");				

$parm = array (	
"rcv_date"			=>	$TODAY,
"rcv_user"			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
"inv_num"			=>	$PHP_inv_num,
"foreign_inv_num"	=>	$PHP_foreign_inv_num,
"inv_date"			=>	$PHP_inv_date,
"inv_price"			=>	$PHP_inv_price,
"discount"			=>	$PHP_discount,
"off_duty"			=>	$PHP_off_duty,
"vat"				=>	$PHP_vat,
"currency"			=>	$PHP_select_currency,
"rcv_num"			=>	$PHP_rcv_num
);

$f1 = $apb->edit($parm);

foreach($PHP_ap_num as $po_num => $ap_num){
	if (array_key_exists($po_num, $PHP_checkbox)){
		$PHP_special[$po_num] == 2 ? $tbl="ap_special" : $tbl="ap_det";
		foreach($PHP_apb_qty[$po_num] as $key=>$qty){
			//function update_field($field, $value, $where_str, $table='apb')
			$f1 = $apb->update_field("qty", $qty, "rcv_num='".$PHP_rcv_num."' and po_id='".$PHP_po_id[$po_num][$key]."'", "apb_det");
			$f1 = $apb->update_field("uprice", $PHP_uprice[$po_num][$key], "rcv_num='".$PHP_rcv_num."' and po_id='".$PHP_po_id[$po_num][$key]."'", "apb_det");
			
			$id = explode('|',$PHP_po_id[$po_num][$key]);
			$size_id = sizeof($id);
			$m_size = $size_id - 1;
			$ttl_po_qty = 0;
			for($k=0;$k<$size_id;$k++){
				$po_qty_row = $apb->get_det_field('id, po_qty', $tbl, "id=".$id[$k]);
				if($k <> $m_size){
					$link_qty[$k]['qty'] = number_format($po_qty_row['po_qty'] / $PHP_po_qty[$po_num][$key] * $qty,2,'','');
					$ttl_po_qty += $link_qty[$k]['qty'];
					$link_qty[$k]['po_id'] = $id[$k];
				}else{
					$link_qty[$k]['qty'] = $qty - $ttl_po_qty;
					$link_qty[$k]['po_id'] = $id[$k];
				}
			}
			
			for($m=0;$m<sizeof($link_qty);$m++){
				$apb->update_link_qty($PHP_det_id[$po_num][$key], $link_qty[$m]['po_id'], $link_qty[$m]['qty']);
			}
			$apb->update_field("cost", $PHP_checkbox[$po_num], "apb_num='".$PHP_rcv_num."' and item='%|after'", "apb_oth_cost");
		}
	}else{
		foreach($PHP_det_id[$po_num] as $key=>$id){
			$apb->del_link($id);
			$apb->del_det($id);
			$apb->update_field("apb_rmk", 0, "po_id='".$PHP_po_id[$po_num][$key]."' and ap_num='".$PHP_ap_num[$po_num]."'", "receive_det");
		}
		$apb->del_apb_cost("apb_num='".$PHP_rcv_num."' and item='%|after'", "apb_oth_cost");
	}
}		

$f2 = $apb->chk_del_full($PHP_rcv_num);
if($f2)
{
	$message = "Delete APB : ".$PHP_rcv_num;
	$log->log_add(0,"040D",$message);
	$message = "Delete APB [".$PHP_rcv_num."]";
	$redir_str = "apb.php?PHP_action=rcvd_search".$PHP_back_str."&PHP_msg=".$message;
	redirect_page($redir_str);
}

foreach($PHP_cost_id as $po_num => $id){
	$f1 = $apb->update_field("item", $PHP_oth_item[$po_num], "id=".$id, "apb_oth_cost");
	$f1 = $apb->update_field("cost", $PHP_oth_cost[$po_num], "id=".$id, "apb_oth_cost");
}

$op = $apb->show_apb3($PHP_rcv_num);
$op['back_str'] = $PHP_back_str;
$message = "Successfully Edit Apb : ".$PHP_rcv_num;
$log->log_add(0,"040E",$message);
$op['msg'][]=$message;

page_display($op, $AUTH, $TPL_APB_AFTER_SHOW);

break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "do_rcvd_edit": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_rcvd_edit":
//print_r($GLOBALS);exit;
check_authority($AUTH,"view");				
/* if($PHP_del_mk == 1)
{
	$message = "Delete Receive [".$PHP_rcv_num."]";
	$redir_str = "apb.php?PHP_action=rcvd_search".$PHP_back_str."&PHP_msg=".$message;
	redirect_page($redir_str);
} */

$parm = array (	
"rcv_date"			=>	$TODAY,
"rcv_user"			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
"inv_num"			=>	$PHP_inv_num,
"inv_date"			=>	$PHP_inv_date,
"foreign_inv_num"	=>	$PHP_foreign_inv_num,
"inv_price"			=>	$PHP_inv_price,
"discount"			=>	$PHP_discount,
"off_duty"			=>	$PHP_off_duty,
"vat"				=>	$PHP_vat,
"currency"			=>	$PHP_select_currency,
"rcv_num"			=>	$PHP_rcv_num
);

$f1 = $apb->edit($parm);

	foreach($PHP_det_id as $rcv_num => $val){
		if (array_key_exists($rcv_num, $PHP_checkbox)){	//更新
			foreach($val as $i => $det_id){
				if(array_key_exists($i, $PHP_select_po_ids[$rcv_num])){
					$f1 = $apb->update_field_id('qty',$PHP_rcv_qty[$rcv_num][$i],$det_id,'apb_det');
					$f1 = $apb->update_field_id('foc',$PHP_foc[$rcv_num][$i],$det_id,'apb_det');
					$f1 = $apb->update_field_id('uprice',$PHP_uprice[$rcv_num][$i],$det_id,'apb_det');
					$f1 = $apb->update_field_id('uprices',$PHP_uprices[$rcv_num][$i],$det_id,'apb_det');
					$f1 = $apb->update_field('currency',$PHP_select_currency,"rcv_id='".$det_id."'",'apb_po_link');
					$f1 = $apb->update_field('rate',$PHP_rate[$rcv_num][$i],"rcv_id='".$det_id."'",'apb_po_link');
				
					if ($PHP_special[$rcv_num][$i] < 2)
					{
						$id = explode('|',$PHP_select_po_ids[$rcv_num][$i]);								
						$s_total=0;
						$tmp=0;
						$M_sizeof = sizeof($id);
						$rowM=$rows=array();
						for ($j=0; $j< $M_sizeof; $j++)		//取得每項的請購量,並加總
						{
							// 以工廠的驗收作平均
							$where_str = "id = ".$id[$j];
							$row[$j]=$po->get_det_field('rcv_qty','ap_det',$where_str);
							$s_total += $row[$j]['rcv_qty'];
							if( $row[$j][0] < 0 ) $row[$j][0] = 1;

							// 0=副料總採購量,1=個別購量,2=ID;
							$rowM[$j]=array($PHP_rcv_qty[$rcv_num][$i],$row[$j][0],$id[$j]);
							$rows[$j]=$row[$j][0];
						}
						
						# 因為數量太小在最後計算會造成負數的產生，所以將數量排序由最小的數量開始計算
						asort($rows);

						$M=0;
						$MCt = $M_sizeof-1;
						foreach ($rows as $kay => $v2) { //計算每項採購量(平分)
							if( $M <> $MCt ) {
								$rcv_qty = $rowM[$kay][1] * ($rowM[$kay][0] / $s_total);
								$rcv_qty = number_format($rcv_qty,2,'.','');
								$tmp = $tmp + $rcv_qty;
							} else {
								$tmp_qty = $rowM[$kay][0] - $tmp;
								$rcv_qty = number_format($tmp_qty,2,'.','');		
							}
							
							$f1 = $apb->update_link_qty($det_id,$rowM[$kay][2],$rcv_qty);
							$f1 = $apb->update_field('amount',$rcv_qty * $PHP_uprice[$rcv_num][$i],"rcv_id='".$det_id."'",'apb_po_link');
							$f1 = $apb->update_apb_qty('apb_qty',$rcv_qty,$rowM[$kay][2],'ap_det');	
							$M++;
						}
						
					} else {
						$f1 = $apb->update_link_qty($val[$i],$PHP_select_po_ids[$rcv_num][$i],$PHP_rcv_qty[$rcv_num][$i]);
						$f1 = $apb->update_apb_qty('apb_qty',$PHP_rcv_qty[$rcv_num][$i],$PHP_select_po_ids[$rcv_num][$i],'ap_special');	
					}
				}else{	//刪除
					$PHP_special[$rcv_num][$i] == 2 ? $ap_table = "ap_special" : $ap_table = "ap_det";
					
						if($ap_table == 'ap_det')
						{
							$qty_fd = $apb->get_link_field('po_id, qty','apb_po_link','rcv_id ='.$det_id);
							$f1 = $apb->del_link($det_id);
							for ($j=0; $j< sizeof($qty_fd); $j++)		//加入DB
							{
								$f1=$apb->update_apb_qty('apb_qty',($qty_fd[$j]['qty']*-1), $qty_fd[$j]['po_id'],'ap_det');						
							}
							if(substr($PHP_mat_code[$rcv_num][$i],0,1) == 'F')
							{
								$ord_rec = $apb->get_ord_rec($qty_fd,'l','ap_det');
								# 要重新review $apb->back_ord_rcvd
								$f1 = $apb->back_ord_rcvd('l',$ord_rec);
							} else {
								$ord_rec = $apb->get_ord_rec($qty_fd,'a','ap_det');			
								$f1 = $apb->back_ord_rcvd('l',$ord_rec);
							}
						} else {
							$f1 = $apb->del_link($det_id);
							
								$f1 = $apb->update_apb_qty('apb_qty',($PHP_qty*-1),$det_id,'ap_special');	
								$qty_fd[0]['po_id'] = $det_id;
								if(substr($PHP_mat_code[$rcv_num][$i],0,1) == 'F')
								{
									$ord_rec = $apb->get_ord_rec($qty_fd,'l','ap_special');						
									$f1 = $apb->back_ord_rcvd('l',$ord_rec);
								} else {
									$ord_rec = $apb->get_ord_rec($qty_fd,'a','ap_special');			
									$f1 = $apb->back_ord_rcvd('l',$ord_rec);
								}
							
						}
						
						$f1 = $apb->del_det($det_id);
						$f1 = $apb->update_field("apb_rmk", 0, "rcv_num='".$rcv_num."' and po_id='".$PHP_po_ids[$rcv_num][$i]."'", "receive_det");
					
				}
			}
		}
		else{
			foreach($val as $i => $det_id){
				$PHP_special[$rcv_num][$i] == 2 ? $ap_table = "ap_special" : $ap_table = "ap_det";
					
						if($ap_table == 'ap_det')
						{
							$qty_fd = $apb->get_link_field('po_id, qty','apb_po_link','rcv_id ='.$det_id);
							$f1 = $apb->del_link($det_id);
							for ($j=0; $j< sizeof($qty_fd); $j++)		//加入DB
							{
								$f1=$apb->update_apb_qty('apb_qty',($qty_fd[$j]['qty']*-1), $qty_fd[$j]['po_id'],'ap_det');						
							}
							if(substr($PHP_mat_code[$rcv_num][$i],0,1) == 'F')
							{
								$ord_rec = $apb->get_ord_rec($qty_fd,'l','ap_det');
								# 要重新review $apb->back_ord_rcvd
								$f1 = $apb->back_ord_rcvd('l',$ord_rec);
							} else {
								$ord_rec = $apb->get_ord_rec($qty_fd,'a','ap_det');			
								$f1 = $apb->back_ord_rcvd('l',$ord_rec);
							}
						} else {
							$f1 = $apb->del_link($det_id);
							
								$f1 = $apb->update_apb_qty('apb_qty',($PHP_qty*-1),$det_id,'ap_special');	
								$qty_fd[0]['po_id'] = $det_id;
								if(substr($PHP_mat_code[$rcv_num][$i],0,1) == 'F')
								{
									$ord_rec = $apb->get_ord_rec($qty_fd,'l','ap_special');						
									$f1 = $apb->back_ord_rcvd('l',$ord_rec);
								} else {
									$ord_rec = $apb->get_ord_rec($qty_fd,'a','ap_special');			
									$f1 = $apb->back_ord_rcvd('l',$ord_rec);
								}
							
						}
						
						$f1 = $apb->del_det($det_id);
						$f1 = $apb->update_field("apb_rmk", 0, "rcv_num='".$rcv_num."' and po_id='".$PHP_po_ids[$rcv_num][$i]."'", "receive_det");
					
			}
		}
		
		$f2 = $apb->chk_del_full($PHP_rcv_num);
	}

if($f2)
{
	$message = "Delete APB : ".$PHP_rcv_num;
	$log->log_add(0,"040D",$message);
	$message = "Delete APB [".$PHP_rcv_num."]";
	$redir_str = "apb.php?PHP_action=rcvd_search".$PHP_back_str."&PHP_msg=".$message;
	redirect_page($redir_str);
}

#修改原有的apb_oth_cost
foreach($PHP_oth_item as $id => $val){
	$apb->update_field("item", $val, "id=".$id." and apb_num='".$PHP_rcv_num."'", "apb_oth_cost");
	$apb->update_field("cost", $PHP_oth_cost[$id], "id=".$id." and apb_num='".$PHP_rcv_num."'", "apb_oth_cost");
}

#新增apb_oth_cost
foreach($PHP_des as $ap_num => $val){
	foreach($val as $key => $val2){
		$apb->oth_cost_add($ap_num, $PHP_des[$ap_num][$key], $PHP_cost[$ap_num][$key], $PHP_rcv_num);
	}
}

$op = $apb->show_apb($PHP_rcv_num);
$op['back_str'] = $PHP_back_str;
$message = "Successfully Edit Apb : ".$PHP_rcv_num;
$log->log_add(0,"040E",$message);
$op['msg'][]=$message;

if ($PHP_revise == 1) {
	page_display($op, $AUTH, $TPL_APB_REVISE);
} else {
	redirect_page($PHP_SELF."?PHP_action=rcvd_view&PHP_rcv_num=".$parm['rcv_num']."&SCH_num=&SCH_supl=&SCH_fab=&SCH_acc=&PHP_sr_startno=1&PHP_msg=".$message);
}
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "del_det_ajax": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "del_det_ajax":

$ap_table = $apb->get_table_name($PHP_id);

if($ap_table == 'ap_det')
{
	$qty_fd = $apb->get_link_field('po_id, qty','apb_po_link','rcv_id ='.$PHP_id);
	$f1 = $apb->del_link($PHP_id);
	for ($j=0; $j< sizeof($qty_fd); $j++)		//加入DB
	{
		$f1=$apb->update_apb_qty('apb_qty',($qty_fd[$j]['qty']*-1), $qty_fd[$j]['po_id'],'ap_det');						
	}
	if(substr($PHP_mat_code,0,1) == 'F')
	{
		$ord_rec = $apb->get_ord_rec($qty_fd,'l','ap_det');						
		$f1 = $apb->back_ord_rcvd('l',$ord_rec);
	} else {
		$ord_rec = $apb->get_ord_rec($qty_fd,'a','ap_det');			
		$f1 = $apb->back_ord_rcvd('l',$ord_rec);
	}
} else {
	$f1 = $apb->del_link($PHP_id);
	$pid = explode('|',$PHP_po_id);
	foreach($pid as $key){
		$PHP_po_id = $key;
		$f1 = $apb->update_apb_qty('apb_qty',($PHP_qty*-1),$PHP_po_id,'ap_special');	
		$qty_fd[0]['po_id'] = $PHP_po_id;
		if(substr($PHP_mat_code,0,1) == 'F')
		{
			$ord_rec = $apb->get_ord_rec($qty_fd,'l','ap_special');						
			$f1 = $apb->back_ord_rcvd('l',$ord_rec);
		} else {
			$ord_rec = $apb->get_ord_rec($qty_fd,'a','ap_special');			
			$f1 = $apb->back_ord_rcvd('l',$ord_rec);
		}
	}
}
$f1 = $apb->del_det($PHP_id);
$f2 = $apb->chk_del_full($PHP_rcv_num);
$message = "Success delete material #:".$PHP_mat_code."On APB #:".$PHP_rcv_num;
$log->log_add(0,"54E",$message);

echo $message."|".$f2;
exit;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "del_apb_cost":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "del_apb_cost":
	$f1 = $apb->del_cost($PHP_id);
	echo $f1;
break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "update_ap_cost":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "update_ap_cost":
	$f1 = $apb->update_ap_cost($PHP_id);
	echo $f1;
break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "tt_before_submit": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "tt_before_submit":
check_authority($AUTH,"edit");

$f1 = $apb->update_field_id('rcv_sub_user',$GLOBALS['SCACHE']['ADMIN']['login_id'],$PHP_id,'apb');
$f1 = $apb->update_field_id('rcv_sub_date',$TODAY,$PHP_id,'apb');
$f1 = $apb->update_field_id('status',2,$PHP_id,'apb');

if ($PHP_ver == 0)$apb->update_field_id('version',1,$PHP_id,'apb');
# $f1 = $apb->check_po_rcvd($PHP_rcv_num);

$op = $apb->get_po_before($PHP_rcv_num);

$op['back_str'] = $PHP_back_str;
$op['ckdate'] = substr($op['apb']['rcv_date'],0,7) < substr($TODAY,0,7) ? '1' : '0' ;

$message = "Successfully Submitted APB # : ".$PHP_rcv_num;
$op['msg'][]="Successfully Submitted APB # : ".$PHP_rcv_num;
$log->log_add(0,"040E",$message);

page_display($op, $AUTH, $TPL_APB_PO_BEFORE_SHOW);

break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_rcv_apb_submit": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "po_rcv_apb_submit":
check_authority($AUTH,"edit");

$f1 = $apb->update_field_id('rcv_sub_user',$GLOBALS['SCACHE']['ADMIN']['login_id'],$PHP_id,'apb');
$f1 = $apb->update_field_id('rcv_sub_date',$TODAY,$PHP_id,'apb');
$f1 = $apb->update_field_id('status',4,$PHP_id,'apb');

if ($PHP_ver == 0)$apb->update_field_id('version',1,$PHP_id,'apb');
# $f1 = $apb->check_po_rcvd($PHP_rcv_num);

$op = $apb->get_po_rcv_apb($PHP_apb_num);

$op['back_str'] = $PHP_back_str;
$op['ckdate'] = substr($op['apb']['rcv_date'],0,7) < substr($TODAY,0,7) ? '1' : '0' ;

$message = "Successfully Submitted APB # : ".$PHP_apb_num;
$op['msg'][]="Successfully Submitted APB # : ".$PHP_apb_num;
$log->log_add(0,"040E",$message);

page_display($op, $AUTH, $TPL_PO_RCV_APB_SHOW);

break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "po_close": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "po_close":
check_authority($AUTH,"add");				
$f1 = $apb->po_close($PHP_po_num,$PHP_special);
//		$op['back_str'] = "&SCH_num=".$SCH_num."&SCH_supl=".$SCH_supl."&SCH_fab=".$SCH_fab."&SCH_acc".$SCH_fab;

$message = "Successfully Close PO # : ".$PHP_po_num;
//		$op['msg'][]="Successfully Submitted APB # : ".$PHP_rcv_num;
$log->log_add(0,"040A",$message);
echo $message;
//		page_display($op, $AUTH, $TPL_APB_SHOW);
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "rcvd_revise": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "rcvd_revise":
check_authority($AUTH,"view");

$f1 = $apb->update_field_id('rcv_sub_user','',$PHP_id,'apb');
$f1 = $apb->update_field_id('rcv_sub_date','',$PHP_id,'apb');
$f1 = $apb->update_field_id('status',0,$PHP_id,'apb');
$f1 = $apb->update_field_id('version',($PHP_ver+1),$PHP_id,'apb');

$op = $apb->show_apb($PHP_rcv_num);

$op['date'] = $TODAY;
//$op['currency'] =  $arry2->select($CURRENCY,$op['apb']['currency'],'PHP_currency','select','rate()');	//稅率狀態下拉式
$op['back_str'] = "&SCH_num=".$SCH_num."&SCH_supl=".$SCH_supl."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_sr_startno=".$PHP_sr_startno;

$op['revise'] = 1;
$msg = "APB  #:".$PHP_rcv_num." Revised.";
$log->log_add(0,"040V",$msg);
$op['msg'][] = $msg;

if($op['apb']['payment'] == "T/T before shipment" or $op['apb']['payment'] == "before|%" or $op['apb']['payment'] == "L/C at sight")
	redirect_page($PHP_SELF."?PHP_action=po_before_edit&PHP_rcv_num=".$PHP_rcv_num);
elseif($op['apb']['payment'] == "%|after")
	redirect_page($PHP_SELF."?PHP_action=rcvd_after_edit&PHP_rcv_num=".$PHP_rcv_num);
elseif($op['apb']['payment'] == "T/T after shipment" or $op['apb']['payment'] == "月結")
	redirect_page($PHP_SELF."?PHP_action=rcvd_edit&PHP_rcv_num=".$PHP_rcv_num);
else
	page_display($op, $AUTH, $TPL_APB_EDIT);
break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "rcvd_log_add": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "rcvd_log_add":
check_authority($AUTH,"view");	

if(strstr($PHP_des,'&#'))	$PHP_des = $ch_cov->check_cov($PHP_des);

$parm= array(
'rcv_num'		=>	$PHP_rcv_num,
'item'			=>	'RCV-Rmk.',
'des'			=>	$PHP_des,
'user'			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
'k_date'		=>	date('Y-m-d')
);

$f1=$apb->add_log($parm);

if ($f1) {
	$message="Successfully add log :".$PHP_rcv_num;
	$op['msg'][]=$message;
} else {
	$op['msg']= $apply->msg->get(2);
}	

$op = $apb->show_apb($PHP_rcv_num);
$op['back_str'] = $PHP_back_str;
$op['date'] = $TODAY;

page_display($op, $AUTH, $TPL_APB_SHOW);
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "po_before_apb_print":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "po_before_apb_print":
check_authority($AUTH,"view");

$op = $apb->get_po_rcv_apb($PHP_rcv_num);

$submit_user=$user->get(0,$op['apb']['rcv_sub_user']);
if ($submit_user['name'])$op['apb']['rcv_sub_user'] = $submit_user['name'];
$create_user=$user->get(0,$op['apb']['rcv_user']);
if ($create_user['name'])$op['apb']['rcv_user'] = $create_user['name'];

//請購單位
$where_str = " WHERE dept_code = '".$op['ap'][0]['dept']."'";
$po_dept_name = $dept->get_fields('dept_name',$where_str);

# 將同張 P/O# 的 ord_num 歸類
$po_ary = array();

foreach($op['apb_det'] as $key1 => $val1){
	foreach($val1["det"] as $key2 => $val2){
		$po_ary[$val2['po_num']]['po_num'] = $val2['po_num'];
		foreach($val2['order'] as $key3=>$val3){
            if (!in_array($val3,$po_ary[$val2['po_num']]['ord']))
                $po_ary[$val2['po_num']]['ord'][] = $val3;
		}
	}
}

#請購單位
$dt = $apb->get_dept_code(substr($op['apb_det'][0]['det'][0]['order'][0],0,1));
$where_str = " WHERE dept_code = '".$dt."'";
$dept_name = $dept->get_fields('dept_name',$where_str);

$ary_title = array (
'apb_num'			=>	$op['apb']['rcv_num'],
'apb_dept'	        =>	mb_substr($dept_name[0],0,3,"big5")=="貿易業" ? "貿易業務" : $dept_name[0],	#申請單位
'dept_code'	        =>	$apb->get_apb_dept($dt),            #單位代號
'apb_date'	        =>	$TODAY,			                    #申請日期
'supplier'	        =>	$op['apb']['f_name'],				#受款人
'uni_no'	        =>	$op['apb']['uni_no'],				#統一編號
'inv_num'	        =>	$op['apb']['inv_num'],				#統一發票
'inv_date'	        =>	$op['apb']['inv_date'],				#發票日期
'rcv_user'          =>  $op['apb']['rcv_user'],				#經辦
'rcv_user_id'       =>  $submit_user['emp_id'],				#經辦工號
'foreign_inv_num'   =>	$op['apb']['foreign_inv_num'],      #INVOICE #
);

include_once($config['root_dir']."/lib/class.pdf_apb.php");

$print_title = "驗 收 單";
$print_title2 = "VER.".$op['apb']['version'];
$creator = $op['apb']['rcv_user'];
$mark = $op['apb']['rcv_num'];

$pdf=new PDF_rcvd('L','mm','A4');
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();
$pdf->SetAutoPageBreak(1);
$pdf->hend_title($ary_title);

$pdf->SetFont('Big5','',10);

$pdf->ln();
$pdf->SetFont('big5','B',8);
$pdf->Cell(8,7,'項目',1,0,'C');
$pdf->Cell(20,7,'編號',1,0,'C');
$pdf->Cell(90,7,'品名 / 規格',1,0,'C');
$pdf->Cell(45,7,'顏色',1,0,'C');
$pdf->Cell(20,7,'請購單號碼',1,0,'C');
$pdf->Cell(10,7,'單位',1,0,'C');
$pdf->Cell(15,7,'請購數量',1,0,'C');
$pdf->Cell(15,7,'FOC',1,0,'C');
$pdf->Cell(15,7,'請款數量',1,0,'C');
$pdf->Cell(15,7,'單價',1,0,'C');
$pdf->Cell(25,7,'實付總價',1,0,'C');
$pdf->ln();

$no = '1' ;
$uprice = $qty = $foc = $po_qty = $amount = 0;
$pdf->SetFont('Big5','',10);
foreach($op['apb_det'] as $key => $val) {
	foreach($val["det"] as $key2 => $val2) {
        if($no != 1)$pdf->ln();
		if ( ( $pdf->getY() + 6 ) >= 190 )$pdf->AddPage();

		if (substr($val2['mat_code'],0,1) =='A') {
			$val2['sname'] = $acc->get( '',$val2['mat_code']);
			$note = $val2['sname']['des'];
		} else {
			$val2['sname'] = $lots->get( '',$val2['mat_code']);
			$note = $val2['sname']['comp'];
		}
		
        $height = 6;
		
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(8,$height,$no,1,0,'C');
		$pdf->Cell(20,$height,$val2['mat_code'],1,0,'C');
		$pdf->SetFont('big5','',8);

		$o_y = $pdf->getY();
		$pdf->Cell(90,$height,substr($val2['sname'][2]." / ".($note),0,55),1,0,'L');
		
		$pdf->setY($o_y);
		$pdf->setX(128);
		$pdf->SetFont('big5','',6);
		$pdf->Cell(45,$height,$val2['color'],1,0,'C');
		$pdf->SetFont('Arial','',8);
		
		$pdf->Cell(20,$height,$val2['po_num'],1,0,'C');
		$pdf->Cell(10,$height,$val2['po']['po_unit'],1,0,'C');
		$pdf->Cell(15,$height,number_format($val2['po']['po_qty'], 2, '.',','),1,0,'R');
		//$pdf->Cell(13,$height,$val['ord_num'][0]['receive_time'],1,0,'C');
		$pdf->Cell(15,$height,$val2['foc'],1,0,'R');
		$pdf->Cell(15,$height,number_format($val2['qty'], 2, '.',','),1,0,'R');
		$pdf->Cell(15,$height,number_format($val2['uprice'], 2, '.',','),1,0,'R');
		if ($no==1)
			$pdf->Cell(25,$height,$op['apb']['currency']." $".number_format($val2['uprice']*$val2['qty'], 2, '.',','),1,0,'R');
		else
			$pdf->Cell(25,$height,number_format($val2['uprice']*$val2['qty'], 2, '.',','),1,0,'R');
		$no++;
        
		$amount += number_format($val2['uprice']*$val2['qty'], 2, '.','');
        $qty += $val2['qty'];
        $uprice += $val2['uprice'];
        $foc += $val2['foc'];
        $po_qty += $val2['po_qty'];
        
	}
}

$oth_count = 0;
for($j=0;$j<sizeof($op['apb_oth_cost']);$j++){
	$pdf->ln();
	$pdf->SetFont('Arial','',8);
	$pdf->Cell(8,$height,$no,1,0,'C');
	$pdf->SetFont('big5','',8);
	$pdf->Cell(20,$height,'其他費用',1,0,'C');
	$o_y = $pdf->getY();
	$pdf->Cell(225,$height,$op['apb_oth_cost'][$j]['item'],1,0,'L');
	$pdf->SetFont('Arial','',8);
	$pdf->Cell(25,$height,number_format($op['apb_oth_cost'][$j]['cost'], 2, '.',','),1,0,'R');    
	
	$oth_count += $op['apb_oth_cost'][$j]['cost'];
	$no++;
}

for($i=0;$i<sizeof($op['ap']);$i++){
    for($j=0;$j<sizeof($op['ap'][$i]['ap_oth_cost']);$j++){
        if($op['ap'][$i]['ap_oth_cost'][$j]){
            $pdf->ln();
            $pdf->SetFont('Arial','',8);
            $pdf->Cell(8,$height,$no,1,0,'C');
            $pdf->SetFont('big5','',8);
            $pdf->Cell(20,$height,'其他費用',1,0,'C');
            $o_y = $pdf->getY();
            $pdf->Cell(225,$height,$op['ap'][$i]['ap_oth_cost'][$j]['item'],1,0,'L');
            $pdf->SetFont('Arial','',8);
            $pdf->Cell(25,$height,number_format($op['ap'][$i]['ap_oth_cost'][$j]['cost'], 2, '.',','),1,0,'R');    

            $oth_count += $op['ap'][$i]['ap_oth_cost'][$j]['cost'];
            $no++;
        }
	}
}



$pdf->ln();
# 粗線
$y=$pdf->GetY();
$pdf->SetLineWidth(0.5);
$pdf->Line(10,$pdf->GetY(),288,$pdf->GetY());
$pdf->SetLineWidth(0.1);
$pdf->SetY($y);

$pdf->SetFont('big5','B',8);
$pdf->Cell(208,6,'合 計',1,0,'R');
$pdf->SetFont('Arial','B',8);

$pdf->Cell(15,6,number_format($foc, 2, '.',','),1,0,'R');
$pdf->Cell(15,6,number_format($qty, 2, '.',','),1,0,'R');
$pdf->Cell(15,6,number_format($uprice/($no-1), 2, '.',','),1,0,'R');

if($op['apb']['currency']=='NTD')
	$pdf->Cell(25,6,number_format(round($amount+$oth_count), 0, '',','),1,0,'R');
else
	$pdf->Cell(25,6,number_format($amount+$oth_count, 2, '.',','),1,0,'R');

$pdf->ln();
$pdf->SetFont('big5','B',8);
$pdf->Cell(223,6,'',0,0,'C');
$pdf->Cell(15,6,'營業稅',1,0,'C');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(40,6,number_format($op['apb']['vat'], 2, '.',','),1,0,'R');
$pdf->ln();

$pdf->SetFont('big5','B',8);
$pdf->Cell(223,6,'',0,0,'L');
$pdf->Cell(15,6,'折讓',1,0,'C');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(40,6,($op['apb']['discount'] != 0)?'-'.number_format($op['apb']['discount'], 2, '.',','):'0.00',1,0,'R');
$pdf->ln();

$pdf->SetFont('big5','B',8);
$pdf->Cell(223,6,'',0,0,'L');
$pdf->Cell(15,6,'折稅',1,0,'C');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(40,6,($op['apb']['off_duty'] != 0)?'-'.number_format($op['apb']['off_duty'], 2, '.',','):'0.00',1,0,'R');
$pdf->ln();

$pdf->SetFont('big5','B',8);
$pdf->Cell(223,6,'',0,0,'L');
$pdf->Cell(15,6,'調整稅額',1,0,'C');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(40,6,$op['apb']['adjust_amt'],1,0,'R');
$pdf->ln();

$pdf->SetFont('big5','B',8);
$pdf->Cell(223,6,'',0,0,'L');
$pdf->Cell(15,6,'總合計',1,0,'C');
$pdf->SetFont('Arial','',10);

if($op['apb']['currency']=='NTD')
	$pdf->Cell(40,6,$op['apb']['currency']." $".number_format(round($op['apb']['inv_price']+$op['apb']['adjust_amt']), 0, '',','),1,0,'R');
else
	$pdf->Cell(40,6,$op['apb']['currency']." $".number_format($op['apb']['inv_price']+$op['apb']['adjust_amt'], 2, '.',','),1,0,'R');

$pdf->ln();

if ( ( $pdf->getY() + 50 ) >= 190 )$pdf->AddPage();
######################################################
$pdf->ln();
$pdf->SetFont('big5','B',8);
$pdf->Cell(278,7,'備      註',1,0,'L');
$pdf->SetFont('big5','',8);
$pdf->ln();

$pdf->Cell(278,7,'付款方式：'.$op['apb']['payment'],'LR','TLR','L');
$pdf->ln();

foreach($po_ary as $key => $val){
	$pdf->Cell(15,5,$val['po_num']."： ",'L',0,'L');
	//$pdf->Cell(20,5," [ ".$val['pay_price']." ] ",0,0,'L');
	$str_ord = ' ';
	foreach($val['ord'] as $ordnum){
		$str_ord.=$ordnum."、";
	}
	$str_ord = substr($str_ord,0,-1);
	$pdf->Cell(263,5,$str_ord,'R',0,'L');
	$pdf->ln();
}

for($x=0;$x<sizeof($op['rcv_log']);$x++){
	if ( $pdf->getY() >= 170 )//換頁
		$pdf->AddPage();
	$pdf->Cell(278,5,$op['rcv_log'][$x]['des'],'LR',0,'L');
	$pdf->ln();
}

$y=$pdf->GetY();
//$pdf->ln();
$pdf->SetLineWidth(0.5);
$pdf->Line(10,$pdf->GetY(),288,$pdf->GetY());
$pdf->SetLineWidth(0.1);
$pdf->SetY($y);

//$pdf->ln();

$ch_price = $apb->num2chinese(number_format($op['apb']['inv_price']+$op['apb']['adjust_amt'],2,'.',''));
$pdf->SetFont('big5','B',10);
//$pdf->Cell(278,7,"新台幣 / 美金：".$ch_price[9]." \t仟\t".$ch_price[8]." \t佰\t".$ch_price[7]." \t拾\t".$ch_price[6]." \t萬\t".$ch_price[5]." \t仟\t"
//						.$ch_price[4]." \t佰\t".$ch_price[3]." \t拾\t".$ch_price[2]." \t元\t".$ch_price[1]." \t角\t".$ch_price[0]." \t分\t",1,0,'L');

$pdf->SetFont('big5','B',8);
$pdf->Cell(15,7,$op['apb']['currency']."：",'LTB',0,'L');
$pdf->Cell(17,7,$ch_price[9],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"仟",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[8],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"佰",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[7],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"拾",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[6],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"萬",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[5],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"仟",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[4],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"佰",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[3],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"拾",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[2],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"元",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[1],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"角",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[0],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(12,7,"分 整",'TBR',0,'C');

$pdf->ln();
$pdf->ln();

$pdf->SetFont('big5','B',10);
$pdf->Cell(118,7,"核准：",0,0,'R');
$pdf->Cell(21,7,"",0,0,'L');
$pdf->Cell(21,7,"覆核：",0,0,'R');
$pdf->Cell(21,7,"",0,0,'L');
$pdf->Cell(21,7,"主管：",0,0,'R');
$pdf->Cell(21,7,"",0,0,'L');
$pdf->Cell(21,7,"經辦：",0,0,'R');
$pdf->Cell(21,7,$op['apb']['rcv_user']." ".$submit_user['emp_id'],0,0,'L');

$pdf->SetFont('Arial','',8);
$pdf->SetX();
$pdf->SetY(195);
$pdf->Cell(280,7,"Print ".$GLOBALS['THIS_TIME'],0,0,'R');


$pdf->SetY($oy);
$pdf->SetFont('big5','B',8);
for($s=0;$s<sizeof($op['apb_log']);$s++){
	$pdf->SetX(20);
	$pdf->MultiCell(230,6,($s+1).".".$op['apb_log'][$s]['des'],0,'L',0);
}

$name=$PHP_rcv_num.'.pdf';
$pdf->Output($name,'D');

page_display($op, $AUTH, $TPL_APB_SHOW);
break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "apb_month_print": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "apb_month_print":
check_authority($AUTH,"view");

// $op = $apb->get_month_print($PHP_rcv_num);
//$op = $apb->show_apb($PHP_rcv_num);
$op = $apb->show_apb($PHP_rcv_num,'view');


#將同張P/O#的金額加總及歸類ord_num
$po_ary = array();
foreach($op['apb_det'] as $key1 => $val1){
	foreach($val1['det'] as $key2 => $val2){
		$po_ary[$val2['po']['po_num']]['po_num'] = $val2['po']['po_num'];
		foreach ($val2['order'] as $nn => $ord_val){
			if (!in_array($ord_val,$po_ary[$val2['po']['po_num']]['ord_num']))
				$po_ary[$val2['po']['po_num']]['ord_num'][] = $ord_val;
		}
		$po_ary[$val2['po']['po_num']]['pay_price'] += $val2['price'];
	}
}

$submit_user=$user->get(0,$op['apb']['rcv_sub_user']);
if ($submit_user['name'])$op['apb']['rcv_sub_user'] = $submit_user['name'];
$create_user=$user->get(0,$op['apb']['rcv_user']);
if ($create_user['name'])$op['apb']['rcv_user'] = $create_user['name'];

#請購單位
$dt = $apb->get_dept_code(substr($op['apb_det'][0]['det'][0]['order'][0],0,1));
$where_str = " WHERE dept_code = '".$dt."'";
$dept_name = $dept->get_fields('dept_name',$where_str);

$ary_title = array (
'apb_num'			=>	$PHP_rcv_num,
'apb_dept'	        =>	mb_substr($dept_name[0],0,3,"big5")=="貿易業" ? "貿易業務" : $dept_name[0],	#申請單位
'dept_code'	        =>	$apb->get_apb_dept($dt),            #單位代號
'apb_date'	        =>	$TODAY,			                    #申請日期
'supplier'	        =>	$op['apb']['f_name'],				#受款人
'uni_no'	        =>	$op['apb']['uni_no'],				#統一編號
'inv_num'	        =>	$op['apb']['inv_num'],				#統一發票
'inv_date'	        =>	$op['apb']['inv_date'],				#發票日期
'rcv_user'          =>  $op['apb']['rcv_user'],				#經辦
'rcv_user_id'       =>  $create_user['emp_id'],				#經辦工號
'foreign_inv_num'   =>	$op['apb']['foreign_inv_num'],      #INVOICE #
);

include_once($config['root_dir']."/lib/class.pdf_apb.php");

$print_title="驗收付款單";
$print_title2 = "VER.".$op['apb']['version']."   for   ".$mat_cat;
$creator = $op['apb']['rcv_user'];
$mark = $op['apb']['rcv_num'];

$pdf=new PDF_rcvd('L','mm','A4');
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();
$pdf->SetAutoPageBreak(1);



$pdf->ln();
$pdf->SetFont('big5','B',8);
$pdf->Cell(8,7,'項目',1,0,'C');
$pdf->Cell(20,7,'編號',1,0,'C');
$pdf->Cell(75,7,'品名 / 規格',1,0,'C');
$pdf->Cell(45,7,'顏色',1,0,'C');
$pdf->Cell(20,7,'請購單號碼',1,0,'C');
$pdf->Cell(10,7,'單位',1,0,'C');
$pdf->Cell(15,7,'請購數量',1,0,'C');
$pdf->Cell(15,7,'驗收數量',1,0,'C');
$pdf->Cell(15,7,'FOC',1,0,'C');
$pdf->Cell(15,7,'請款數量',1,0,'C');
$pdf->Cell(15,7,'單價',1,0,'C');
$pdf->Cell(25,7,'實付總價',1,0,'C');
$pdf->ln();

$no = '1' ;
$uprice = $qty = $foc = $po_qty = $rcv_qty = $amount = 0;
$pdf->SetFont('Big5','',10);
foreach($op['apb_det'] as $key => $val) {
	//$amount += $val['amount'];
	foreach($val['det'] as $key2 => $det) {
		if($no != 1)$pdf->ln();
		if ( ( $pdf->getY() + 6 ) >= 190 )$pdf->AddPage();

		if (substr($det['mat_code'],0,1) =='A') {
			$det['sname'] = $acc->get( '',$det['mat_code']);
			$note = $det['sname']['des'];
		} else {
			$det['sname'] = $lots->get( '',$det['mat_code']);
			$note = $det['sname']['comp'];
		}
		
		//$note=($val['mat_cat'] == '主料' ? $val['sname']['comp'] : $val['sname']['des']);
		// $cd_len_row = ceil(intval($pdf->GetStringWidth($note))/66); //判斷要斷成幾列 
		// $cd_len_row = ( $cd_len_row == 0 ) ? 1 : $cd_len_row;
		// $height = $cd_len_row * 6; //設定MultiCell的高度
        $height = 6;
		
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(8,$height,$no,1,0,'C');
		$pdf->Cell(20,$height,$det['mat_code'],1,0,'C');
		$pdf->SetFont('big5','',8);

		$o_y = $pdf->getY();
		$pdf->Cell(75,6,substr($det['sname'][2]." / ".($note),0,60),1,0,'L');
		
		//$pdf->setY($o_y);
		//$pdf->setX(128);
        
		$pdf->SetFont('big5','',6);
		$pdf->Cell(45,$height,$det['color'],1,0,'C');
		$pdf->SetFont('Arial','',8);
		
		$pdf->Cell(20,$height,$det['po']['po_num'],1,0,'C');
		$pdf->Cell(10,$height,$det['po']['po_unit'],1,0,'C');
		$pdf->Cell(15,$height,number_format($det['po']['po_qty'], 2, '.',','),1,0,'R');
		$pdf->Cell(15,$height,number_format($det['rcv_qty'], 2, '.',','),1,0,'R');
		$pdf->Cell(15,$height,$det['foc'],1,0,'R');
		$pdf->Cell(15,$height,number_format($det['qty'], 2, '.',','),1,0,'R');
		$pdf->Cell(15,$height,number_format($det['uprice'], 2, '.',','),1,0,'R');
        
		$amount += number_format($det['uprice']*$det['qty'], 2, '.','');
        $qty += $det['qty'];
		$rcv_qty += $det['rcv_qty'];
        $uprice += $det['uprice'];
        $foc += $det['foc'];
        $po_qty += $det['po']['po_qty'];
        
		if ($no==1)
			$pdf->Cell(25,$height,$op['apb']['currency']." $".number_format($det['uprice']*$det['qty'], 2, '.',','),1,0,'R');
		else
			$pdf->Cell(25,$height,number_format($det['uprice']*$det['qty'], 2, '.',','),1,0,'R');
		$no++;
	}
}

$oth_count = 0;
for($i=0;$i<sizeof($op['apb_oth_cost']);$i++){
        $pdf->ln();
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(8,$height,$no,1,0,'C');
        $pdf->SetFont('big5','',8);
        $pdf->Cell(20,$height,'其他費用',1,0,'C');
        $o_y = $pdf->getY();
        $pdf->Cell(225,$height,$op['apb_oth_cost'][$i]['item'],1,0,'L');
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(25,$height,number_format($op['apb_oth_cost'][$i]['cost'], 2, '.',','),1,0,'R');    

        $oth_count += $op['apb_oth_cost'][$i]['cost'];
        $no++;
}

for($i=0;$i<sizeof($op['ap_oth_cost']);$i++){

    $pdf->ln();
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(8,$height,$no,1,0,'C');
    $pdf->SetFont('big5','',8);
    $pdf->Cell(20,$height,'其他費用',1,0,'C');
    $o_y = $pdf->getY();
    $pdf->Cell(225,$height,$op['ap_oth_cost'][$i]['item'],1,0,'L');
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(25,$height,number_format($op['ap_oth_cost'][$i]['cost'], 2, '.',','),1,0,'R');    

    $oth_count += $op['ap_oth_cost'][$i]['cost'];
    $no++;
}

$pdf->ln();
# 粗線
$y=$pdf->GetY();
$pdf->SetLineWidth(0.5);
$pdf->Line(10,$pdf->GetY(),288,$pdf->GetY());
$pdf->SetLineWidth(0.1);
$pdf->SetY($y);

$pdf->SetFont('big5','B',8);
$pdf->Cell(193,6,'合 計',1,0,'R');
$pdf->SetFont('Arial','B',8);

$pdf->Cell(15,6,number_format($rcv_qty, 2, '.',','),1,0,'R');
$pdf->Cell(15,6,number_format($foc, 2, '.',','),1,0,'R');
$pdf->Cell(15,6,number_format($qty, 2, '.',','),1,0,'R');
$pdf->Cell(15,6,number_format($uprice/($no-1), 2, '.',','),1,0,'R');

if($op['apb']['currency']=='NTD')
	$pdf->Cell(25,6,number_format(round($amount+$oth_count), 0, '',','),1,0,'R');
else
	$pdf->Cell(25,6,number_format($amount+$oth_count, 2, '.',','),1,0,'R');

$pdf->ln();
$pdf->SetFont('big5','B',8);
$pdf->Cell(223,6,'',0,0,'C');
$pdf->Cell(15,6,'營業稅',1,0,'C');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(40,6,number_format($op['apb']['vat'], 2, '.',','),1,0,'R');
$pdf->ln();

$pdf->SetFont('big5','B',8);
$pdf->Cell(223,6,'',0,0,'L');
$pdf->Cell(15,6,'折讓',1,0,'C');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(40,6,($op['apb']['discount'] != 0)?'-'.number_format($op['apb']['discount'], 2, '.',','):'0.00',1,0,'R');
$pdf->ln();

$pdf->SetFont('big5','B',8);
$pdf->Cell(223,6,'',0,0,'L');
$pdf->Cell(15,6,'折稅',1,0,'C');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(40,6,($op['apb']['off_duty'] != 0)?'-'.number_format($op['apb']['off_duty'], 2, '.',','):'0.00',1,0,'R');
$pdf->ln();

$pdf->SetFont('big5','B',8);
$pdf->Cell(223,6,'',0,0,'L');
$pdf->Cell(15,6,'調整稅額',1,0,'C');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(40,6,$op['apb']['adjust_amt'],1,0,'R');
$pdf->ln();

$pdf->SetFont('big5','B',8);
$pdf->Cell(223,6,'',0,0,'L');
$pdf->Cell(15,6,'總合計',1,0,'C');
$pdf->SetFont('Arial','',10);

if($op['apb']['currency']=='NTD')
	$pdf->Cell(40,6,$op['apb']['currency']." $".number_format(round($op['apb']['inv_price'] + $op['apb']['adjust_amt']), 0, '',','),1,0,'R');
else
	$pdf->Cell(40,6,$op['apb']['currency']." $".number_format($op['apb']['inv_price'] + $op['apb']['adjust_amt'], 2, '.',','),1,0,'R');

$pdf->ln();

if ( $pdf->getY() >= 160 ) //換頁
	$pdf->AddPage();

$pdf->ln();
$pdf->SetFont('big5','B',8);
$pdf->Cell(278,7,'備      註',1,0,'L');
$pdf->SetFont('big5','',8);
$pdf->ln();

$pdf->Cell(278,7,'付款方式：'.$op['apb']['payment'],'LR','TLR','L');
$pdf->ln();

foreach($po_ary as $key => $val){
	if ( $pdf->getY() >= 160 ) //換頁
		$pdf->AddPage();
	$pdf->Cell(15,5,$val['po_num']."： ",'L',0,'L');
	//$pdf->Cell(20,5," [ ".$val['pay_price']." ] ",0,0,'L');
	$str_ord = ' ';
	foreach($val['ord_num'] as $ordnum){
		$str_ord.=$ordnum."、";
	}
	$str_ord = substr($str_ord,0,-1);
	$pdf->Cell(263,5,$str_ord,'R',0,'L');
	$pdf->ln();
}

for($x=0;$x<sizeof($op['rcv_log']);$x++){
	if ( $pdf->getY() >= 170 )//換頁
		$pdf->AddPage();
	$pdf->Cell(278,5,$op['rcv_log'][$x]['des'],'LR',0,'L');
	$pdf->ln();
}

$y=$pdf->GetY();
$pdf->SetLineWidth(0.5);
$pdf->Line(10,$pdf->GetY(),288,$pdf->GetY());
$pdf->SetLineWidth(0.1);
$pdf->SetY($y);

//$pdf->ln();
$pdf->SetFont('big5','B',10);

$ch_price = $apb->num2chinese(number_format($op['apb']['inv_price'] + $op['apb']['adjust_amt'],2,'.',''));
//$pdf->Cell(278,7,"新台幣 / 美金：".$ch_price[9]." \t仟\t".$ch_price[8]." \t佰\t".$ch_price[7]." \t拾\t".$ch_price[6]." \t萬\t".$ch_price[5]." \t仟\t"
//						.$ch_price[4]." \t佰\t".$ch_price[3]." \t拾\t".$ch_price[2]." \t元\t".$ch_price[1]." \t角\t".$ch_price[0]." \t分\t",1,0,'L');
$pdf->SetFont('big5','B',8);
$pdf->Cell(15,7,$op['apb']['currency']."：",'LTB',0,'L');
$pdf->Cell(17,7,$ch_price[9],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"仟",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[8],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"佰",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[7],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"拾",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[6],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"萬",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[5],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"仟",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[4],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"佰",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[3],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"拾",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[2],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"元",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[1],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"角",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[0],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(12,7,"分 整",'TBR',0,'C');
$pdf->ln();

$pdf->ln();

$pdf->SetFont('big5','B',10);
$pdf->Cell(118,7,"核准：",0,0,'R');
$pdf->Cell(21,7,"",0,0,'L');
$pdf->Cell(21,7,"覆核：",0,0,'R');
$pdf->Cell(21,7,"",0,0,'L');
$pdf->Cell(21,7,"主管：",0,0,'R');
$pdf->Cell(21,7,"",0,0,'L');
$pdf->Cell(21,7,"經辦：",0,0,'R');
$pdf->Cell(21,7,$op['apb']['rcv_user']." ( ".$create_user['emp_id'].' ) ',0,0,'L');

$pdf->SetFont('Arial','',8);
$pdf->SetX();
$pdf->SetY(195);
$pdf->Cell(280,7,$pdf->getY()."Print ".$GLOBALS['THIS_TIME'],0,0,'R');


$pdf->SetY($oy);
$pdf->SetFont('big5','B',8);
for($s=0;$s<sizeof($op['apb_log']);$s++){
	$pdf->SetX(20);
	$pdf->MultiCell(230,6,($s+1).".".$op['apb_log'][$s]['des'],0,'L',0);
}

$name=$PHP_rcv_num.'.pdf';
$pdf->Output($name,'D');

page_display($op, $AUTH, $TPL_APB_SHOW);
break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "apb_rcvd_after_print": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "apb_rcvd_after_print":
check_authority($AUTH,"view");

$op = $apb->show_apb($PHP_rcv_num);

#將同張P/O#的金額加總及歸類ord_num
$po_ary = array();
foreach($op['apb_det'] as $key1 => $val1){
	foreach($val1['det'] as $key2 => $val2){
		$po_ary[$val2['po']['po_num']]['po_num'] = $val2['po']['po_num'];
		foreach ($val2['order'] as $nn => $ord_val){
			if (!in_array($ord_val,$po_ary[$val2['po']['po_num']]['ord_num']))
				$po_ary[$val2['po']['po_num']]['ord_num'][] = $ord_val;
		}
		$po_ary[$val2['po']['po_num']]['pay_price'] += $val2['price'];
	}
}

$submit_user=$user->get(0,$op['apb']['rcv_sub_user']);
if ($submit_user['name'])$op['apb']['rcv_sub_user'] = $submit_user['name'];
$create_user=$user->get(0,$op['apb']['rcv_user']);
if ($create_user['name'])$op['apb']['rcv_user'] = $create_user['name'];

#請購單位
$dt = $apb->get_dept_code(substr($op['apb_det'][0]['det'][0]['order'][0],0,1));
$where_str = " WHERE dept_code = '".$dt."'";
$dept_name = $dept->get_fields('dept_name',$where_str);

$ary_title = array (
'apb_num'	=>	$PHP_rcv_num,
'apb_dept'	=>	mb_substr($dept_name[0],0,3,"big5")=="貿易業" ? "貿易業務" : $dept_name[0],	#申請單位
'dept_code'	=>	$apb->get_apb_dept($dt),				#單位代號
'apb_date'	=>	$TODAY,			#申請日期
'supplier'	=>	$op['apb']['f_name'],				#受款人
'uni_no'	=>	$op['apb']['uni_no'],				#統一編號
'inv_date'	=>	$op['apb']['inv_date'],				#發票日期
'inv_num'	=>	$op['apb']['inv_num'],				#統一發票
'rcv_user'  =>  $op['apb']['rcv_user'],				#經辦
'rcv_user_id'  =>  $create_user['emp_id'],           #經辦工號
'foreign_inv_num'	=>	$op['apb']['foreign_inv_num']	#INVOICE
);

include_once($config['root_dir']."/lib/class.pdf_apb.php");

if($op['apb']['payment']=="L/C at sight")
	$print_title="驗收單";
else
	$print_title="驗收付款單";
$print_title2 = "VER.".$op['apb']['version']."   for   ".$mat_cat;
$creator = $op['apb']['rcv_user'];
$mark = $op['apb']['rcv_num'];

$pdf=new PDF_rcvd('L','mm','A4');
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();
$pdf->SetAutoPageBreak(1);

$pdf->ln();
$pdf->SetFont('big5','B',8);
$pdf->Cell(8,7,'項目',1,0,'C');
$pdf->Cell(20,7,'編號',1,0,'C');
$pdf->Cell(75,7,'品名 / 規格',1,0,'C');
$pdf->Cell(45,7,'顏色',1,0,'C');
$pdf->Cell(20,7,'請購單號碼',1,0,'C');
$pdf->Cell(10,7,'單位',1,0,'C');
$pdf->Cell(15,7,'請購數量',1,0,'C');
$pdf->Cell(15,7,'驗收數量',1,0,'C');
$pdf->Cell(15,7,'FOC',1,0,'C');
$pdf->Cell(15,7,'請款數量',1,0,'C');
$pdf->Cell(15,7,'單價',1,0,'C');
$pdf->Cell(25,7,'實付總價',1,0,'C');
$pdf->ln();

$no = '1' ;
$amount = 0;

foreach($op['apb_det'] as $key => $val) {
	//$amount += $val['amount'];
	foreach($val['det'] as $key2 => $det) {
		if($no != 1)$pdf->ln();
		if ( ( $pdf->getY() + 6 ) >= 190 )$pdf->AddPage();

		if (substr($det['mat_code'],0,1) =='A') {
			$det['sname'] = $acc->get( '',$det['mat_code']);
			$note = $det['sname']['des'];
		} else {
			$det['sname'] = $lots->get( '',$det['mat_code']);
			$note = $det['sname']['comp'];
		}
		
		$height = 7;
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(8,$height,$no,1,0,'C');
		$pdf->Cell(20,$height,$det['mat_code'],1,0,'C');
		$pdf->SetFont('big5','',8);

		//$o_y = $pdf->getY();
		$pdf->Cell(75,$height,substr($det['sname'][2],0,40),1,0,'C');
		
		$pdf->SetFont('big5','',6);
		$pdf->Cell(45,$height,$det['color'],1,0,'C');
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(20,$height,$det['po']['po_num'],1,0,'C');
		$pdf->Cell(10,$height,$det['po']['po_unit'],1,0,'C');
		$pdf->Cell(15,$height,number_format($det['po']['po_qty'], 2, '.',','),1,0,'R');
		$pdf->Cell(15,$height,number_format($det['rcv_qty'], 2, '.',','),1,0,'R');
		$pdf->Cell(15,$height,$det['foc'],1,0,'R');
		$pdf->Cell(15,$height,number_format($det['qty'], 2, '.',','),1,0,'R');
		$pdf->Cell(15,$height,number_format($det['uprice'], 2, '.',','),1,0,'R');
		$amount += number_format($det['uprice']*$det['qty'], 2, '.','');
		if ($no==1)
			$pdf->Cell(25,$height,$op['apb']['currency']." $".number_format($det['uprice']*$det['qty'], 2, '.',','),1,0,'R');
		else
			$pdf->Cell(25,$height,number_format($det['uprice']*$det['qty'], 2, '.',','),1,0,'R');
		$no++;
		
		$qty += $det['qty'];
		$rcv_qty += $det['rcv_qty'];
        $uprice += $det['uprice'];
        $foc += $det['foc'];
        $po_qty += $det['po']['po_qty'];
	}
}

$oth_count = 0;
for($i=0;$i<sizeof($op['apb_oth_cost']);$i++){
    $pdf->ln();
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(8,$height,$no,1,0,'C');
    $pdf->SetFont('big5','',8);
    $pdf->Cell(20,$height,'其他費用',1,0,'C');
    $o_y = $pdf->getY();
    $pdf->Cell(225,$height,$op['apb_oth_cost'][$i]['item'],1,0,'L');
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(25,$height,number_format($op['apb_oth_cost'][$i]['cost'], 2, '.',','),1,0,'R');    
    
    $oth_count += $op['apb_oth_cost'][$i]['cost'];
    $no++;
}

for($i=0;$i<sizeof($op['ap_oth_cost']);$i++){

    $pdf->ln();
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(8,$height,$no,1,0,'C');
    $pdf->SetFont('big5','',8);
    $pdf->Cell(20,$height,'其他費用',1,0,'C');
    $o_y = $pdf->getY();
    $pdf->Cell(225,$height,$op['ap_oth_cost'][$i]['item'],1,0,'L');
    $pdf->SetFont('Arial','',8);
    $pdf->Cell(25,$height,number_format($op['ap_oth_cost'][$i]['cost'], 2, '.',','),1,0,'R');    

    $oth_count += $op['ap_oth_cost'][$i]['cost'];
    $no++;
}

$pdf->ln();
# 粗線
$y=$pdf->GetY();
$pdf->SetLineWidth(0.5);
$pdf->Line(10,$pdf->GetY(),288,$pdf->GetY());
$pdf->SetLineWidth(0.1);
$pdf->SetY($y);

$pdf->SetFont('big5','B',8);
$pdf->Cell(193,6,'合 計',1,0,'R');
$pdf->SetFont('Arial','B',8);

$pdf->Cell(15,6,number_format($rcv_qty, 2, '.',','),1,0,'R');
$pdf->Cell(15,6,number_format($foc, 2, '.',','),1,0,'R');
$pdf->Cell(15,6,number_format($qty, 2, '.',','),1,0,'R');
$pdf->Cell(15,6,number_format($uprice/($no-1), 2, '.',','),1,0,'R');

if($op['apb']['currency']=='NTD')
	$pdf->Cell(25,6,number_format(round($amount+$oth_count), 0, '',','),1,0,'R');
else
	$pdf->Cell(25,6,number_format($amount+$oth_count, 2, '.',','),1,0,'R');

$pdf->ln();
$pdf->SetFont('big5','B',8);
$pdf->Cell(223,6,'',0,0,'C');
$pdf->Cell(15,6,'營業稅',1,0,'C');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(40,6,number_format($op['apb']['vat'], 2, '.',','),1,0,'R');
$pdf->ln();

$pdf->SetFont('big5','B',8);
$pdf->Cell(223,6,'',0,0,'L');
$pdf->Cell(15,6,'折讓',1,0,'C');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(40,6,($op['apb']['discount'] != 0)?'-'.number_format($op['apb']['discount'], 2, '.',','):'0.00',1,0,'R');
$pdf->ln();

$pdf->SetFont('big5','B',8);
$pdf->Cell(223,6,'',0,0,'L');
$pdf->Cell(15,6,'折稅',1,0,'C');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(40,6,($op['apb']['off_duty'] != 0)?'-'.number_format($op['apb']['off_duty'], 2, '.',','):'0.00',1,0,'R');
$pdf->ln();

$pdf->SetFont('big5','B',8);
$pdf->Cell(223,6,'',0,0,'L');
$pdf->Cell(15,6,'調整稅額',1,0,'C');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(40,6,$op['apb']['adjust_amt'],1,0,'R');
$pdf->ln();

$pdf->SetFont('big5','B',8);
$pdf->Cell(223,6,'',0,0,'L');
$pdf->Cell(15,6,'總合計',1,0,'C');
$pdf->SetFont('Arial','',10);
if($op['apb']['currency']=="NTD")
	$pdf->Cell(40,6,$op['apb']['currency']." $".number_format($op['apb']['inv_price']+$op['apb']['adjust_amt'],0,'',','),1,0,'R');
else
	$pdf->Cell(40,6,$op['apb']['currency']." $".number_format($op['apb']['inv_price']+$op['apb']['adjust_amt'],2,'.',','),1,0,'R');
$pdf->ln();

if ( $pdf->getY() >= 160 )//換頁
	$pdf->AddPage();

$pdf->ln();
$pdf->SetFont('big5','',8);
$pdf->Cell(278,7,'備      註',1,0,'L');
$pdf->ln();

$pdf->Cell(278,7,'付款方式：'.$op['apb']['payment'],'LR','TLR','L');
$pdf->ln();

foreach($po_ary as $key => $val){
	if ( $pdf->getY() >= 160 )//換頁
		$pdf->AddPage();
	$pdf->Cell(15,5,$val['po_num']."： ",'L',0,'L');
	//$pdf->Cell(20,5," [ ".$val['pay_price']." ] ",0,0,'L');
	$str_ord = ' ';
	foreach($val['ord_num'] as $ordnum){
		$str_ord.=$ordnum."、";
	}
	$str_ord = substr($str_ord,0,-1);
	$pdf->Cell(263,5,$str_ord,'R',0,'L');
	$pdf->ln();
}

for($x=0;$x<sizeof($op['rcv_log']);$x++){
	if ( $pdf->getY() >= 170 )//換頁
		$pdf->AddPage();
	$pdf->Cell(278,5,$op['rcv_log'][$x]['des'],'LR',0,'L');
	$pdf->ln();
}

$y=$pdf->GetY();
//$pdf->ln();
$pdf->SetLineWidth(0.5);
$pdf->Line(10,$pdf->GetY(),288,$pdf->GetY());
$pdf->SetLineWidth(0.1);
$pdf->SetY($y);



//$pdf->ln();
$pdf->SetFont('big5','B',10);
$ch_price = $apb->num2chinese(number_format($op['apb']['inv_price']+$op['apb']['adjust_amt'],2,'.',''));
//$pdf->Cell(278,7,"新台幣 / 美金：".$ch_price[9]." \t仟\t".$ch_price[8]." \t佰\t".$ch_price[7]." \t拾\t".$ch_price[6]." \t萬\t".$ch_price[5]." \t仟\t"
//						.$ch_price[4]." \t佰\t".$ch_price[3]." \t拾\t".$ch_price[2]." \t元\t".$ch_price[1]." \t角\t".$ch_price[0]." \t分\t",1,0,'L');

$pdf->SetFont('big5','B',8);
$pdf->Cell(15,7,$op['apb']['currency']."：",'LTB',0,'L');
$pdf->Cell(17,7,$ch_price[9],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"仟",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[8],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"佰",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[7],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"拾",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[6],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"萬",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[5],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"仟",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[4],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"佰",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[3],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"拾",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[2],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"元",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[1],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"角",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[0],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(12,7,"分 整",'TBR',0,'C');

$pdf->SetFont('big5','B',8);
$pdf->ln();

$pdf->ln();

$pdf->SetFont('big5','B',10);
$pdf->Cell(118,7,"核准：",0,0,'R');
$pdf->Cell(21,7,"",0,0,'L');
$pdf->Cell(21,7,"覆核：",0,0,'R');
$pdf->Cell(21,7,"",0,0,'L');
$pdf->Cell(21,7,"主管：",0,0,'R');
$pdf->Cell(21,7,"",0,0,'L');
$pdf->Cell(21,7,"經辦：",0,0,'R');
$pdf->Cell(21,7,$op['apb']['rcv_user']." ".$create_user['emp_id'],0,0,'L');

$pdf->SetFont('Arial','',8);
$pdf->SetX();
$pdf->SetY(195);
$pdf->Cell(280,7,"Print ".$GLOBALS['THIS_TIME'],0,0,'R');


$pdf->SetY($oy);
$pdf->SetFont('big5','B',8);
for($s=0;$s<sizeof($op['apb_log']);$s++){
	$pdf->SetX(20);
	$pdf->MultiCell(230,6,($s+1).".".$op['apb_log'][$s]['des'],0,'L',0);
}

$name=$PHP_rcv_num.'.pdf';
$pdf->Output($name,'D');

page_display($op, $AUTH, $TPL_APB_SHOW);
break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "apb_before_print":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "apb_before_print":
check_authority($AUTH,"view");

$op = $apb->get_po_before($PHP_rcv_num);
//if($op['apb']['payment']=='before|%') $op['apb']['payment'] = $op['ap'][0]['dm_way'];

$submit_user=$user->get(0,$op['apb']['rcv_sub_user']);
if ($submit_user['name'])$op['apb']['rcv_sub_user'] = $submit_user['name'];
$create_user=$user->get(0,$op['apb']['rcv_user']);
if ($create_user['name'])$op['apb']['rcv_user'] = $create_user['name'];

//請購單位
$where_str = " WHERE dept_code = '".$op['ap'][0]['dept']."'";
$po_dept_name = $dept->get_fields('dept_name',$where_str);

# 將同張 P/O# 的 ord_num 歸類
$po_ary = array();

foreach($op['ap'] as $key1 => $val1){
	$po_ary[$val1['po_num']]['po_num'] = $val1['po_num'];
	//$po_ary[$val1['po_num']]['po_num']['ord'] = array();
	foreach($val1["ap_det"] as $key2 => $val2){
		foreach($val2['orders'] as $key3=>$val3){
            if (!in_array($val3,$po_ary[$val1['po_num']]['ord']))
                $po_ary[$val1['po_num']]['ord'][] = $val3;
		}
	}
}

#請購單位
$dt = $apb->get_dept_code(substr($op['ap'][0]['ord_num'][0],0,1));
$where_str = " WHERE dept_code = '".$dt."'";
$dept_name = $dept->get_fields('dept_name',$where_str);

$ary_title = array (
'apb_num'			=>	$op['apb']['rcv_num'],
'apb_dept'	        =>	mb_substr($dept_name[0],0,3,"big5")=="貿易業" ? "貿易業務" : $dept_name[0],	#申請單位
'dept_code'	        =>	$apb->get_apb_dept($dt),            #單位代號
'apb_date'	        =>	$TODAY,			                    #申請日期
'supplier'	        =>	$op['apb']['f_name'],				#受款人
'uni_no'	        =>	$op['apb']['uni_no'],				#統一編號
'inv_num'	        =>	$op['apb']['inv_num'],				#統一發票
'inv_date'	        =>	$op['apb']['inv_date'],				#發票日期
'rcv_user'          =>  $op['apb']['rcv_user'],				#經辦
'rcv_user_id'       =>  $submit_user['emp_id'],				#經辦工號
'foreign_inv_num'   =>	$op['apb']['foreign_inv_num'],      #INVOICE #
);

include_once($config['root_dir']."/lib/class.pdf_apb.php");

if($op['apb']['payment'] == "月結")
	$print_title="借 款 單";
else
	$print_title="請 款 單";
$print_title2 = "VER.".$op['apb']['version'];
$creator = $op['apb']['rcv_user'];
$mark = $op['apb']['rcv_num'];

$pdf=new PDF_rcvd('L','mm','A4');
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();
$pdf->SetAutoPageBreak(1);
$pdf->hend_title($ary_title);

$pdf->SetFont('Big5','',10);

$pdf->ln();
$pdf->SetFont('big5','B',8);
$pdf->Cell(8,7,'項目',1,0,'C');
$pdf->Cell(20,7,'編號',1,0,'C');
$pdf->Cell(90,7,'品名 / 規格',1,0,'C');
$pdf->Cell(45,7,'顏色',1,0,'C');
$pdf->Cell(20,7,'請購單號碼',1,0,'C');
$pdf->Cell(10,7,'單位',1,0,'C');
$pdf->Cell(15,7,'請購數量',1,0,'C');
$pdf->Cell(15,7,'FOC',1,0,'C');
$pdf->Cell(15,7,'請款數量',1,0,'C');
$pdf->Cell(15,7,'單價',1,0,'C');
$pdf->Cell(25,7,'實付總價',1,0,'C');
$pdf->ln();

$no = '1' ;
$uprice = $qty = $foc = $po_qty = $amount = 0;
$pdf->SetFont('Big5','',10);
$height = 6;
foreach($op['ap'] as $key => $val) {
	foreach($val["ap_det"] as $key2 => $val2) {
        //$amount += $val2['amount'];
		if($no != 1)$pdf->ln();
		if ( ( $pdf->getY() + 6 ) >= 190 )$pdf->AddPage();

		if (substr($val2['mat_code'],0,1) =='A') {
			$val2['sname'] = $acc->get( '',$val2['mat_code']);
			$note = $val2['sname']['des'];
		} else {
			$val2['sname'] = $lots->get( '',$val2['mat_code']);
			$note = $val2['sname']['comp'];
		}
		
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(8,$height,$no,1,0,'C');
		$pdf->Cell(20,$height,$val2['mat_code'],1,0,'C');
		$pdf->SetFont('big5','',8);

		$o_y = $pdf->getY();
		$pdf->Cell(90,$height,substr($val2['sname'][2]." / ".($note),0,55),1,0,'L');
		
		$pdf->setY($o_y);
		$pdf->setX(128);
		$pdf->SetFont('big5','',6);
		$pdf->Cell(45,$height,$val2['color'],1,0,'C');
		$pdf->SetFont('Arial','',8);
		
		$pdf->Cell(20,$height,$val['po_num'],1,0,'C');
		$pdf->Cell(10,$height,$val2['po_unit'],1,0,'C');
		$pdf->Cell(15,$height,number_format($val2['po_qty'], 2, '.',','),1,0,'R');
		//$pdf->Cell(13,$height,$val['ord_num'][0]['receive_time'],1,0,'C');
		$pdf->Cell(15,$height,$val2['foc'],1,0,'R');
		$pdf->Cell(15,$height,number_format($val2['qty'], 2, '.',','),1,0,'R');
		$pdf->Cell(15,$height,number_format($val2['uprice'], 2, '.',','),1,0,'R');
		if ($no==1)
			$pdf->Cell(25,$height,$op['apb']['currency']." $".number_format($val2['uprice']*$val2['qty'], 2, '.',','),1,0,'R');
		else
			$pdf->Cell(25,$height,number_format($val2['uprice']*$val2['qty'], 2, '.',','),1,0,'R');
		$no++;
        
		$amount += number_format($val2['uprice']*$val2['qty'], 2, '.','');
        $qty += $val2['qty'];
        $uprice += $val2['uprice'];
        $foc += $val2['foc'];
        $po_qty += $val2['po_qty'];
        
	}
}

$oth_count = 0;
for($i=0;$i<sizeof($op['apb_oth_cost']);$i++){
    if($op['apb_oth_cost'][$i]){
        $pdf->ln();
        // $pdf->SetFont('Arial','',8);
        // $pdf->Cell(8,$height,$no,1,0,'C');
        // $pdf->SetFont('big5','',8);
        // $pdf->Cell(20,$height,'其他費用'.$op['apb_oth_cost'][$i]['ap_num'],1,0,'C');
        // $o_y = $pdf->getY();
        // $pdf->Cell(225,$height,$op['apb_oth_cost'][$i]['item'],1,0,'L');
        // $pdf->SetFont('Arial','',8);
        // $pdf->Cell(25,$height,number_format($op['apb_oth_cost'][$i]['cost'], 2, '.',','),1,0,'R');    
        $oth_count += $op['apb_oth_cost'][$i]['cost'];
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(8,$height,$no,1,0,'C');
        $pdf->SetFont('big5','',8);
		$pdf->Cell(20,$height,'其他費用',1,0,'C');

		$o_y = $pdf->getY();
		$pdf->Cell(90,$height,$op['apb_oth_cost'][$i]['item'],1,0,'L');
		
		$pdf->setY($o_y);
		$pdf->setX(128);
		$pdf->SetFont('big5','',6);
		$pdf->Cell(45,$height,$val2['color'],1,0,'C');
		$pdf->SetFont('Arial','',8);
		
		$pdf->Cell(20,$height,$op['apb_oth_cost'][$i]['ap_num'],1,0,'C');
		$pdf->Cell(10,$height,'',1,0,'C');
		$pdf->Cell(15,$height,'',1,0,'R');
		$pdf->Cell(15,$height,'',1,0,'R');
		$pdf->Cell(15,$height,'',1,0,'R');
		$pdf->Cell(15,$height,'',1,0,'R');
        $pdf->Cell(25,$height,number_format($op['apb_oth_cost'][$i]['cost'], 2, '.',','),1,0,'R');
        $no++;
    }
}

for($i=0;$i<sizeof($op['ap_oth_cost']);$i++){
    if($op['ap_oth_cost'][$i]){
        $pdf->ln();
        // $pdf->SetFont('Arial','',8);
        // $pdf->Cell(8,$height,$no,1,0,'C');
        // $pdf->SetFont('big5','',8);
        // $pdf->Cell(20,$height,'其他費用'.$op['ap_oth_cost'][$i]['ap_num'],1,0,'C');
        // $o_y = $pdf->getY();
        // $pdf->Cell(225,$height,$op['ap_oth_cost'][$i]['item'],1,0,'L');
        // $pdf->SetFont('Arial','',8);
        // $pdf->Cell(25,$height,number_format($op['ap_oth_cost'][$i]['cost'], 2, '.',','),1,0,'R');    
        $oth_count += $op['ap_oth_cost'][$i]['cost'];
        
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(8,$height,$no,1,0,'C');
        $pdf->SetFont('big5','',8);
		$pdf->Cell(20,$height,'其他費用',1,0,'C');

		$o_y = $pdf->getY();
		$pdf->Cell(90,$height,$op['ap_oth_cost'][$i]['item'],1,0,'L');
		
		$pdf->setY($o_y);
		$pdf->setX(128);
		$pdf->SetFont('big5','',6);
		$pdf->Cell(45,$height,$val2['color'],1,0,'C');
		$pdf->SetFont('Arial','',8);
		
		$pdf->Cell(20,$height,$op['ap_oth_cost'][$i]['ap_num'],1,0,'C');
		$pdf->Cell(10,$height,'',1,0,'C');
		$pdf->Cell(15,$height,'',1,0,'R');
		$pdf->Cell(15,$height,'',1,0,'R');
		$pdf->Cell(15,$height,'',1,0,'R');
		$pdf->Cell(15,$height,'',1,0,'R');
        $pdf->Cell(25,$height,number_format($op['ap_oth_cost'][$i]['cost'], 2, '.',','),1,0,'R');
        $no++;
    }
}




// for($i=0;$i<sizeof($op['ap']);$i++){
    // for($j=0;$j<sizeof($op['ap'][$i]['apb_oth_cost']);$j++){
        // if($op['ap'][$i]['apb_oth_cost'][$j]){
            // $pdf->ln();
            // $pdf->SetFont('Arial','',8);
            // $pdf->Cell(8,$height,$no,1,0,'C');
            // $pdf->SetFont('big5','',8);
            // $pdf->Cell(20,$height,'其他費用',1,0,'C');
            // $o_y = $pdf->getY();
            // $pdf->Cell(225,$height,$op['ap'][$i]['apb_oth_cost'][$j]['item'],1,0,'L');
            // $pdf->SetFont('Arial','',8);
            // $pdf->Cell(25,$height,number_format($op['ap'][$i]['apb_oth_cost'][$j]['cost'], 2, '.',','),1,0,'R');    

            // $oth_count += $op['ap'][$i]['apb_oth_cost'][$j]['cost'];
            // $no++;
        // }
	// }
// }

// for($i=0;$i<sizeof($op['ap']);$i++){
    // for($j=0;$j<sizeof($op['ap'][$i]['ap_oth_cost']);$j++){
        // if($op['ap'][$i]['ap_oth_cost'][$j]){
            // $pdf->ln();
            // $pdf->SetFont('Arial','',8);
            // $pdf->Cell(8,$height,$no,1,0,'C');
            // $pdf->SetFont('big5','',8);
            // $pdf->Cell(20,$height,'其他費用',1,0,'C');
            // $o_y = $pdf->getY();
            // $pdf->Cell(225,$height,$op['ap'][$i]['ap_oth_cost'][$j]['item'],1,0,'L');
            // $pdf->SetFont('Arial','',8);
            // $pdf->Cell(25,$height,number_format($op['ap'][$i]['ap_oth_cost'][$j]['cost'], 2, '.',','),1,0,'R');    

            // $oth_count += $op['ap'][$i]['ap_oth_cost'][$j]['cost'];
            // $no++;
        // }
	// }
// }


$pdf->ln();
# 粗線
$y=$pdf->GetY();
$pdf->SetLineWidth(0.5);
$pdf->Line(10,$pdf->GetY(),288,$pdf->GetY());
$pdf->SetLineWidth(0.1);
$pdf->SetY($y);

$pdf->SetFont('big5','B',8);
$pdf->Cell(208,6,'合 計',1,0,'R');
$pdf->SetFont('Arial','B',8);

$pdf->Cell(15,6,number_format($foc, 2, '.',','),1,0,'R');
$pdf->Cell(15,6,number_format($qty, 2, '.',','),1,0,'R');
$pdf->Cell(15,6,number_format($uprice/($no-1), 2, '.',','),1,0,'R');

if($op['apb']['currency']=='NTD')
	$pdf->Cell(25,6,number_format(round($amount+$oth_count), 0, '',','),1,0,'R');
else
	$pdf->Cell(25,6,number_format($amount+$oth_count, 2, '.',','),1,0,'R');

$pdf->ln();
$pdf->SetFont('big5','B',8);
$pdf->Cell(223,6,'',0,0,'C');
$pdf->Cell(15,6,'營業稅',1,0,'C');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(40,6,number_format($op['apb']['vat'], 2, '.',','),1,0,'R');
$pdf->ln();

$pdf->SetFont('big5','B',8);
$pdf->Cell(223,6,'',0,0,'L');
$pdf->Cell(15,6,'折讓',1,0,'C');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(40,6,($op['apb']['discount'] != 0)?'-'.number_format($op['apb']['discount'], 2, '.',','):'0.00',1,0,'R');
$pdf->ln();

$pdf->SetFont('big5','B',8);
$pdf->Cell(223,6,'',0,0,'L');
$pdf->Cell(15,6,'折稅',1,0,'C');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(40,6,($op['apb']['off_duty'] != 0)?'-'.number_format($op['apb']['off_duty'], 2, '.',','):'0.00',1,0,'R');
$pdf->ln();

$pdf->SetFont('big5','B',8);
$pdf->Cell(223,6,'',0,0,'L');
$pdf->Cell(15,6,'調整稅額',1,0,'C');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(40,6,$op['apb']['adjust_amt'],1,0,'R');
$pdf->ln();

$pdf->SetFont('big5','B',8);
$pdf->Cell(223,6,'',0,0,'L');
$pdf->Cell(15,6,'總合計',1,0,'C');
$pdf->SetFont('Arial','',10);

if($op['apb']['currency']=='NTD')
	$pdf->Cell(40,6,$op['apb']['currency']." $".number_format(round($op['apb']['inv_price']+$op['apb']['adjust_amt']), 0, '',','),1,0,'R');
else
	$pdf->Cell(40,6,$op['apb']['currency']." $".number_format($op['apb']['inv_price']+$op['apb']['adjust_amt'], 2, '.',','),1,0,'R');

$pdf->ln();

if ( ( $pdf->getY() + 50 ) >= 190 )$pdf->AddPage();
######################################################
$pdf->ln();
$pdf->SetFont('big5','B',8);
$pdf->Cell(278,7,'備      註',1,0,'L');
$pdf->SetFont('big5','',8);
$pdf->ln();

$pdf->Cell(278,7,'付款方式：'.$op['apb']['payment'],'LR','TLR','L');
$pdf->ln();

foreach($po_ary as $key => $val){
	$pdf->Cell(15,5,$val['po_num']."： ",'L',0,'L');
	//$pdf->Cell(20,5," [ ".$val['pay_price']." ] ",0,0,'L');
	$str_ord = ' ';
	foreach($val['ord'] as $ordnum){
		$str_ord.=$ordnum."、";
	}
	$str_ord = substr($str_ord,0,-1);
	$pdf->Cell(263,5,$str_ord,'R',0,'L');
	$pdf->ln();
}

for($x=0;$x<sizeof($op['rcv_log']);$x++){
	if ( $pdf->getY() >= 170 )//換頁
		$pdf->AddPage();
	$pdf->Cell(278,5,$op['rcv_log'][$x]['des'],'LR',0,'L');
	$pdf->ln();
}

$y=$pdf->GetY();
//$pdf->ln();
$pdf->SetLineWidth(0.5);
$pdf->Line(10,$pdf->GetY(),288,$pdf->GetY());
$pdf->SetLineWidth(0.1);
$pdf->SetY($y);

//$pdf->ln();

$ch_price = $apb->num2chinese(number_format($op['apb']['inv_price']+$op['apb']['adjust_amt'],2,'.',''));
$pdf->SetFont('big5','B',10);
//$pdf->Cell(278,7,"新台幣 / 美金：".$ch_price[9]." \t仟\t".$ch_price[8]." \t佰\t".$ch_price[7]." \t拾\t".$ch_price[6]." \t萬\t".$ch_price[5]." \t仟\t"
//						.$ch_price[4]." \t佰\t".$ch_price[3]." \t拾\t".$ch_price[2]." \t元\t".$ch_price[1]." \t角\t".$ch_price[0]." \t分\t",1,0,'L');

$pdf->SetFont('big5','B',8);
$pdf->Cell(15,7,$op['apb']['currency']."：",'LTB',0,'L');
$pdf->Cell(17,7,$ch_price[9],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"仟",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[8],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"佰",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[7],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"拾",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[6],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"萬",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[5],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"仟",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[4],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"佰",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[3],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"拾",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[2],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"元",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[1],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"角",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[0],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(12,7,"分 整",'TBR',0,'C');

$pdf->ln();
$pdf->ln();

$pdf->SetFont('big5','B',10);
$pdf->Cell(118,7,"核准：",0,0,'R');
$pdf->Cell(21,7,"",0,0,'L');
$pdf->Cell(21,7,"覆核：",0,0,'R');
$pdf->Cell(21,7,"",0,0,'L');
$pdf->Cell(21,7,"主管：",0,0,'R');
$pdf->Cell(21,7,"",0,0,'L');
$pdf->Cell(21,7,"經辦：",0,0,'R');
$pdf->Cell(21,7,$op['apb']['rcv_user']." ".$submit_user['emp_id'],0,0,'L');

$pdf->SetFont('Arial','',8);
$pdf->SetX();
$pdf->SetY(195);
$pdf->Cell(280,7,"Print ".$GLOBALS['THIS_TIME'],0,0,'R');


$pdf->SetY($oy);
$pdf->SetFont('big5','B',8);
for($s=0;$s<sizeof($op['apb_log']);$s++){
	$pdf->SetX(20);
	$pdf->MultiCell(230,6,($s+1).".".$op['apb_log'][$s]['des'],0,'L',0);
}

$name=$PHP_rcv_num.'.pdf';
$pdf->Output($name,'D');

page_display($op, $AUTH, $TPL_APB_SHOW);
break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "apb_after_rcv_print":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "apb_after_rcv_print":
check_authority($AUTH,"view");

$op = $apb->get_rcv_after_apb($PHP_rcv_num);

$toler = explode("|",$op['apb_det'][0]['det'][0]['po']['dm_way']);
$op['apb']['payment'] = $toler[0]."% TT before shipment, ".$toler[1]."% TT after shipment";

#將同張P/O#的金額加總及歸類ord_num
$po_ary = array();
$pa_num_ary = array();
$ttl_before_price = 0;
foreach($op['apb_det'] as $key1 => $val1){
	foreach($val1['det'] as $key2 => $val2){
		$po_ary[$val2['po_num']]['po_num'] = $val2['po_num'];
		foreach ($val2['order'] as $nn => $ord_val){
			if (!in_array($ord_val,$po_ary[$val2['po_num']]['ord_num']))
				$po_ary[$val2['po_num']]['ord_num'][] = $ord_val;
		}
		$pa_num_ary[$val2['po_num']] = $val2['ap_num'];
	}
}
$pa_num_ary = array_unique($pa_num_ary);

foreach($pa_num_ary as $key => $val){
	$po_ary[$key]['before_price'] = $apb->get_payment_cost($val,14);
	//$po_ary[$key]['after_price'] = $apb->get_payment_cost($val, 16);
	$after_apb_row = $apb->get_det_field("after_apb","ap","ap_num = '".$val."'");
	$po_ary[$key]['after_apb'] = $after_apb_row['after_apb'];
	if($po_ary[$key]['after_apb']){
		$po_ary[$key]['before_price_remain'] = 0;
	}else{
		$po_ary[$key]['before_price_remain'] = $po_ary[$key]['before_price'];
	}
	$po_ary[$key]['after_price'] = $apb->get_cost4pdf($val, $po_ary[$key]['after_apb']);
	foreach($po_ary[$key]['after_price'] as $after_k => $after_v){
		if($after_v['apb_num'] == $PHP_rcv_num && $after_v['cost'] <> $after_v['cost2'])
			$ttl_before_price += $po_ary[$key]['before_price'];
	}
}

$submit_user=$user->get(0,$op['apb']['rcv_sub_user']);
if ($submit_user['name'])$op['apb']['rcv_sub_user'] = $submit_user['name'];
$create_user=$user->get(0,$op['apb']['rcv_user']);
if ($create_user['name'])$op['apb']['rcv_user'] = $create_user['name'];

#請購單位
$dt = $apb->get_dept_code(substr($op['apb_det'][0]['det'][0]['order'][0],0,1));
$where_str = " WHERE dept_code = '".$dt."'";
$dept_name = $dept->get_fields('dept_name',$where_str);

$ary_title = array (
'apb_num'	=>	$PHP_rcv_num,
'apb_dept'	=>	mb_substr($dept_name[0],0,3,"big5")=="貿易業" ? "貿易業務" : $dept_name[0],	#申請單位
'dept_code'	=>	$apb->get_apb_dept($dt),				#單位代號
'apb_date'	=>	$TODAY,			#申請日期
'supplier'	=>	$op['apb']['f_name'],				#受款人
'uni_no'	=>	$op['apb']['uni_no'],				#統一編號
'inv_date'	=>	$op['apb']['inv_date'],				#發票日期
'inv_num'	=>	$op['apb']['inv_num'],				#統一發票
'rcv_user'  =>  $op['apb']['rcv_user'],				#經辦
'rcv_user_id'  =>  $create_user['emp_id'],          #經辦工號
'foreign_inv_num'	=>	$op['apb']['foreign_inv_num']	#INVOICE
);

include_once($config['root_dir']."/lib/class.pdf_apb.php");

if($op['apb']['payment']=="L/C at sight")
	$print_title="驗收單";
else
	$print_title="驗收付款單";
$print_title2 = "VER.".$op['apb']['version']."   for   ".$mat_cat;
$creator = $op['apb']['rcv_user'];
$mark = $op['apb']['rcv_num'];

$pdf=new PDF_rcvd('L','mm','A4');
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();
$pdf->SetAutoPageBreak(1);

$pdf->ln();
$pdf->SetFont('big5','B',8);
$pdf->Cell(8,7,'項目',1,0,'C');
$pdf->Cell(20,7,'編號',1,0,'C');
$pdf->Cell(75,7,'品名 / 規格',1,0,'C');
$pdf->Cell(45,7,'顏色',1,0,'C');
$pdf->Cell(20,7,'請購單號碼',1,0,'C');
$pdf->Cell(10,7,'單位',1,0,'C');
$pdf->Cell(15,7,'請購數量',1,0,'C');
$pdf->Cell(15,7,'驗收數量',1,0,'C');
$pdf->Cell(15,7,'FOC',1,0,'C');
$pdf->Cell(15,7,'請款數量',1,0,'C');
$pdf->Cell(15,7,'單價',1,0,'C');
$pdf->Cell(25,7,'實付總價',1,0,'C');
$pdf->ln();

$no = '1' ;
$amount = 0;

foreach($op['apb_det'] as $key => $val) {
	foreach($val['det'] as $key2 => $det) {
		if($no != 1)$pdf->ln();
		if ( ( $pdf->getY() + 6 ) >= 190 )$pdf->AddPage();

		if (substr($det['mat_code'],0,1) =='A') {
			$det['sname'] = $acc->get( '',$det['mat_code']);
			$note = $det['sname']['des'];
		} else {
			$det['sname'] = $lots->get( '',$det['mat_code']);
			$note = $det['sname']['comp'];
		}
		
		$height = 7;
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(8,$height,$no,1,0,'C');
		$pdf->Cell(20,$height,$det['mat_code'],1,0,'C');
		$pdf->SetFont('big5','',8);

		//$o_y = $pdf->getY();
		$pdf->Cell(75,$height,substr($det['sname'][2],0,40),1,0,'C');
		
		$pdf->SetFont('big5','',6);
		$pdf->Cell(45,$height,$det['color'],1,0,'C');
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(20,$height,$det['po_num'],1,0,'C');
		$pdf->Cell(10,$height,$det['po']['po_unit'],1,0,'C');
		$pdf->Cell(15,$height,number_format($det['po']['po_qty'], 2, '.',','),1,0,'R');
		$pdf->Cell(15,$height,number_format($det['rcv_qty'], 2, '.',','),1,0,'R');
		$pdf->Cell(15,$height,$det['foc'],1,0,'R');
		$pdf->Cell(15,$height,number_format($det['qty'], 2, '.',','),1,0,'R');
		$pdf->Cell(15,$height,number_format($det['uprice'], 2, '.',','),1,0,'R');
		$amount += number_format($det['uprice']*$det['qty'], 2, '.','');
		if ($no==1)
			$pdf->Cell(25,$height,$op['apb']['currency']." $".number_format($det['uprice']*$det['qty'], 2, '.',','),1,0,'R');
		else
			$pdf->Cell(25,$height,number_format($det['uprice']*$det['qty'], 2, '.',','),1,0,'R');
		$no++;
		
		$qty += $det['qty'];
		$rcv_qty += $det['rcv_qty'];
        $uprice += $det['uprice'];
        $foc += $det['foc'];
        $po_qty += $det['po']['po_qty'];
	}
}

$oth_count = 0;
foreach($op['apb_det'] as $key => $val) {
	if(isset($val['ap_oth_cost'])){
		foreach($val['ap_oth_cost'] as $key2 => $val2) {
			$pdf->ln();
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(8,$height,$no,1,0,'C');
			$pdf->SetFont('big5','',8);
			$pdf->Cell(20,$height,'其他費用',1,0,'C');
			$o_y = $pdf->getY();
			$pdf->Cell(225,$height,$val2['item'],1,0,'L');
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(25,$height,number_format($val2['cost'], 2, '.',','),1,0,'R');    
			
			$oth_count += $val2['cost'];
			$no++;
		}
	}
}
foreach($op['apb_det'] as $key => $val) {
	if(isset($val['apb_oth_cost'])){
		foreach($val['apb_oth_cost'] as $key2 => $val2) {
			$pdf->ln();
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(8,$height,$no,1,0,'C');
			$pdf->SetFont('big5','',8);
			$pdf->Cell(20,$height,'其他費用',1,0,'C');
			$o_y = $pdf->getY();
			$pdf->Cell(225,$height,$val2['item'],1,0,'L');
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(25,$height,number_format($val2['cost'], 2, '.',','),1,0,'R');    
			
			$oth_count += $val2['cost'];
			$no++;
		}
	}
}

$pdf->ln();
# 粗線
$y=$pdf->GetY();
$pdf->SetLineWidth(0.5);
$pdf->Line(10,$pdf->GetY(),288,$pdf->GetY());
$pdf->SetLineWidth(0.1);
$pdf->SetY($y);

$pdf->SetFont('big5','B',8);
$pdf->Cell(193,6,'合 計',1,0,'R');
$pdf->SetFont('Arial','B',8);

$pdf->Cell(15,6,number_format($rcv_qty, 2, '.',','),1,0,'R');
$pdf->Cell(15,6,number_format($foc, 2, '.',','),1,0,'R');
$pdf->Cell(15,6,number_format($qty, 2, '.',','),1,0,'R');
$pdf->Cell(15,6,number_format($uprice/($no-1), 2, '.',','),1,0,'R');

if($op['apb']['currency']=='NTD'){
	$pay_amt = number_format(round($amount+$oth_count), 0, '','');
	$pdf->Cell(25,6,$pay_amt ,1,0,'R');
}else{
	$pay_amt = number_format($amount+$oth_count, 2, '.','');
	$pdf->Cell(25,6,$pay_amt ,1,0,'R');
}
$pdf->ln();
$pdf->SetFont('big5','B',8);
$pdf->Cell(223,6,'',0,0,'C');
$pdf->Cell(15,6,'營業稅',1,0,'C');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(40,6,number_format($op['apb']['vat'], 2, '.',','),1,0,'R');
$pdf->ln();

$pdf->SetFont('big5','B',8);
$pdf->Cell(223,6,'',0,0,'L');
$pdf->Cell(15,6,'折讓',1,0,'C');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(40,6,($op['apb']['discount'] != 0)?'-'.number_format($op['apb']['discount'], 2, '.',','):'0.00',1,0,'R');
$pdf->ln();

$pdf->SetFont('big5','B',8);
$pdf->Cell(223,6,'',0,0,'L');
$pdf->Cell(15,6,'折稅',1,0,'C');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(40,6,($op['apb']['off_duty'] != 0)?'-'.number_format($op['apb']['off_duty'], 2, '.',','):'0.00',1,0,'R');
$pdf->ln();

$pdf->SetFont('big5','B',8);
$pdf->Cell(223,6,'',0,0,'L');
$pdf->Cell(15,6,'調整稅額',1,0,'C');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(40,6,$op['apb']['adjust_amt'],1,0,'R');
$pdf->ln();

$pdf->SetFont('big5','B',8);
$pdf->Cell(223,6,'',0,0,'L');
$pdf->Cell(15,6,'貨前已付',1,0,'C');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(40,6,$ttl_before_price,1,0,'R');
$pdf->ln();

$pdf->SetFont('big5','B',8);
$pdf->Cell(223,6,'',0,0,'L');
$pdf->Cell(15,6,'本次應付',1,0,'C');
$pdf->SetFont('Arial','',10);
if($op['apb']['currency']=="NTD"){
	$final_amt = $pay_amt+$op['apb']['adjust_amt']-$ttl_before_price;
	$pdf->Cell(40,6,$op['apb']['currency']." $".number_format($final_amt,0,'',','),1,0,'R');
}else{
	$final_amt = $pay_amt+$op['apb']['adjust_amt']-$ttl_before_price;
	$pdf->Cell(40,6,$op['apb']['currency']." $".number_format($final_amt,2,'.',','),1,0,'R');
}
$pdf->ln();

if ( $pdf->getY() >= 160 )//換頁
	$pdf->AddPage();

$pdf->ln();
$pdf->SetFont('big5','',8);
$pdf->Cell(278,7,'備      註',1,0,'L');
$pdf->ln();

$pdf->Cell(278,7,'付款方式：'.$op['apb']['payment'],'LR','TLR','L');
$pdf->ln();
foreach($po_ary as $key => $val){
	if ( $pdf->getY() >= 160 ){//換頁
		$pdf->AddPage();
	}
	if(is_array($val)){
		$pdf->Cell(15,5,$val['po_num']."： ",'L',0,'L');
		$str_ord = ' ';
		foreach($val['ord_num'] as $ordnum){
			$str_ord.=$ordnum."、";
		}
		$str_ord = substr($str_ord,0,-1);
		$pdf->Cell(263,5,$str_ord,'R',0,'L');
		$pdf->ln();
	}
}

foreach($po_ary as $key => $val){
	if ( $pdf->getY() >= 160 )//換頁
		$pdf->AddPage();
	$pdf->Cell(15,5,$val['po_num'],'L',0,'');
	$pdf->Cell(40,5," 訂金 : ".$val['before_price']." (剩".$val['before_price_remain'].")",'',0,'L');
	$cal_w = 15+40;
	foreach($val['after_price'] as $key2 => $val2){
		$pdf->Cell(25,5," 貨後 :( ".$val2['apb_num']." )",'',0,'L');
		$pdf->Cell(22,5," 應付 : ".$val2['cost2'],'',0,'L');
		$pdf->Cell(22,5," 實付 : ".$val2['cost'],'',0,'L');
		$cal_w += 25+22+22;
	}
	
	$pdf->Cell(278-$cal_w,5," ",'R',0,'R');
	$pdf->ln();
}

for($x=0;$x<sizeof($op['rcv_log']);$x++){
	if ( $pdf->getY() >= 170 )//換頁
		$pdf->AddPage();
	$pdf->Cell(278,5,$op['rcv_log'][$x]['des'],'LR',0,'L');
	$pdf->ln();
}

$y=$pdf->GetY();
//$pdf->ln();
$pdf->SetLineWidth(0.5);
$pdf->Line(10,$pdf->GetY(),288,$pdf->GetY());
$pdf->SetLineWidth(0.1);
$pdf->SetY($y);



//$pdf->ln();
$pdf->SetFont('big5','B',10);
$ch_price = $apb->num2chinese(number_format($final_amt,2,'.',''));

$pdf->SetFont('big5','B',8);
$pdf->Cell(15,7,$op['apb']['currency']."：",'LTB',0,'L');
$pdf->Cell(17,7,$ch_price[9],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"仟",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[8],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"佰",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[7],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"拾",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[6],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"萬",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[5],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"仟",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[4],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"佰",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[3],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"拾",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[2],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"元",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[1],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"角",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[0],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(12,7,"分 整",'TBR',0,'C');

$pdf->SetFont('big5','B',8);
$pdf->ln();

$pdf->ln();

$pdf->SetFont('big5','B',10);
$pdf->Cell(118,7,"核准：",0,0,'R');
$pdf->Cell(21,7,"",0,0,'L');
$pdf->Cell(21,7,"覆核：",0,0,'R');
$pdf->Cell(21,7,"",0,0,'L');
$pdf->Cell(21,7,"主管：",0,0,'R');
$pdf->Cell(21,7,"",0,0,'L');
$pdf->Cell(21,7,"經辦：",0,0,'R');
$pdf->Cell(21,7,$op['apb']['rcv_user']." ".$create_user['emp_id'],0,0,'L');

$pdf->SetFont('Arial','',8);
$pdf->SetX();
$pdf->SetY(195);
$pdf->Cell(280,7,"Print ".$GLOBALS['THIS_TIME'],0,0,'R');


$pdf->SetY($oy);
$pdf->SetFont('big5','B',8);
for($s=0;$s<sizeof($op['apb_log']);$s++){
	$pdf->SetX(20);
	$pdf->MultiCell(230,6,($s+1).".".$op['apb_log'][$s]['des'],0,'L',0);
}

$name=$PHP_rcv_num.'.pdf';
$pdf->Output($name,'D');

page_display($op, $AUTH, $TPL_APB_SHOW);
break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "rcvd_print": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "rcvd_print":
check_authority($AUTH,"view");

$op = $apb->get($PHP_rcv_num);
$submit_user=$user->get(0,$op['rcv']['rcv_sub_user']);
if ($submit_user['name'])$op['rcv']['rcv_sub_user'] = $submit_user['name'];
$create_user=$user->get(0,$op['rcv']['rcv_user']);
if ($create_user['name'])$op['rcv']['rcv_user'] = $create_user['name'];

if (substr($op['rcv_det'][0]['mat_code'],0,1) =='A') {$mat_cat='Accessory';}else{$mat_cat='Fabric';}

$where_str = " WHERE dept_code = '".$op['rcv']['dept']."'";

$dept_name=$dept->get_fields('dept_name',$where_str);
if (!isset($dept_name[0]))$dept_name[0] = $op['rcv']['dept'];
//---------------------------------- 資料庫作業結束 ------------------------------

include_once($config['root_dir']."/lib/class.pdf_rcvd.php");

$print_title="驗收付款單";
$print_title2 = "VER.".$op['rcv']['version']."   for   ".$mat_cat;
$creator = $op['rcv']['rcv_user'];
$mark = $op['rcv']['rcv_num'];

$pdf=new PDF_rcvd('L','mm','A4');
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();
$pdf->SetAutoPageBreak(1);
$pdf->SetFont('Arial','B',14);

$ary_title = array (	'rcv_num'		=>	$op['rcv']['rcv_num'],
							'sup_code'		=>	$op['rcv']['sup_no'],
							'sup_name'		=>	$op['rcv']['s_name'],
							'dept'				=>	$dept_name[0],
							'sub_date'		=>	$op['rcv']['rcv_sub_date'],
							'sub_user'		=>	$op['rcv']['rcv_sub_user'],
							'inv_nun'			=>	$op['rcv']['inv_num'],
				);
$pdf->hend_title($ary_title);

//列印王安採購單碼
$pdf->ln();
$pdf->SetFont('Arial','',10);
$pdf->setX(13);
$pdf->Cell(150,5,"(".$op['rcv']['ann_num'].")",0,0,'L');
$pdf->ln();
 $pdf->SetFont('Arial','B',14);




$pdf->rcv_title();
$pdf->ln();
$total =0;$x=0;$po_total=0;$rcv_total=0;
//$cur='';$cur_mk=0;
$unt='';$unt_mk=0;


for ($i=0; $i< sizeof($op['rcv_det']); $i++)
{
/*
	if ($op['rcv']['currency'] <> $cur)	//確認Currency是否都相同
	{
		if($cur<>"") $cur_mk=1;
		$cur = $op['rcv']['currency'];
	}
*/	
	if ($op['rcv_det'][$i]['unit'] <> $unt) //確認Unit是否都相同
	{
		if($unt<>"") $unt_mk=1;
		$unt = $op['rcv_det'][$i]['unit'];
	}		

	if (substr($op['rcv_det'][0]['mat_code'],0,1) =='A')
	{
		$where_str = " WHERE acc_code = '".$op['rcv_det'][$i]['mat_code']."'";
		$mat_name = $acc->get_fields('acc_name',$where_str);
	}else{
		$where_str = " WHERE lots_code = '".$op['rcv_det'][$i]['mat_code']."'";
		$mat_name = $lots->get_fields('lots_name',$where_str);
	}
	$rcv_det_ary = array(	'ord_num'		=>	$op['rcv_det'][$i]['ord_num'][0],
												'mat_code'	=>	$op['rcv_det'][$i]['mat_code'],
												'mat_name'	=>	$mat_name[0],
												'color'			=>	$op['rcv_det'][$i]['color'],
												'po_num'		=>	$op['rcv']['po_num'],
												'po_qty'		=>	NUMBER_FORMAT($op['rcv_det'][$i]['po']['po_qty'],2),
												'eta'				=>	$op['rcv_det'][$i]['po']['eta'],
												'price'			=>	NUMBER_FORMAT($op['rcv_det'][$i]['price'],2),
												'currency'	=>	$op['rcv']['currency'],
												'qty'				=>	NUMBER_FORMAT($op['rcv_det'][$i]['qty'],2),
												'unit'			=>	$op['rcv_det'][$i]['unit'],
											);

	$rcv_det_ary['amount'] = $op['rcv_det'][$i]['qty'] * $op['rcv_det'][$i]['price'];
	$total +=$rcv_det_ary['amount'];
	$po_total+=$op['rcv_det'][$i]['po']['po_qty'];
	$rcv_total+=$op['rcv_det'][$i]['qty'];
	$rcv_det_ary['amount'] = NUMBER_FORMAT($rcv_det_ary['amount'],2);
//	$x+=sizeof($op['rcv_det'][$i]['ord_num']);

	if (sizeof($op['rcv_det'][$i]['ord_num']) > 1)
	{		
		$pdf->rcv_det_mix($rcv_det_ary,$op['rcv_det'][$i]['ord_num']);
	}else{
		$pdf->rcv_det($rcv_det_ary);
	}
}
$pdf->Cell(166,5,'Sub-Total  :  ',1,0,'R');
if ($unt_mk == 1)
{
	$pdf->Cell(50,5,'',1,0,'R');
	$pdf->Cell(53,5,'',1,0,'R');
}else{
	$pdf->Cell(50,5,NUMBER_FORMAT($po_total,2)." ".$op['rcv_det'][0]['unit'],1,0,'R');
	$pdf->Cell(47,5,NUMBER_FORMAT($rcv_total,2)." ".$op['rcv_det'][0]['unit'],1,0,'R');
}
//$pdf->Cell(46,5,$op['rcv']['currency']."$".NUMBER_FORMAT($total,2),1,0,'R'); //金額加總的小計(稅前)
$pdf->ln();
$pdf->SetFont('Big5','',10);
$v_cost =0;$rmk0='';$rmk1='';
$v_cost = $total * ($vant / 100);



if (isset($op['rcv_log'][0]['des']))$rmk0 = $op['rcv_log'][0]['des'];
//$pdf->Cell(14,5,'Remark','RTL',0,'C');
//$pdf->Cell(249,5,$rmk0,'RTL',0,'L');
//$pdf->Cell(20,5,'Vat. : ',1,0,'R');
//$pdf->Cell(46,5,$op['rcv']['currency']."$".NUMBER_FORMAT($v_cost,2),1,0,'R'); //稅金

		$len = mb_strlen($rmk0,'big-5');
		
		if($len >= 90)
		{
			$tmp1 = mb_substr($rmk0,0,90,'big-5'); 
			$tmp2 = mb_substr($rmk0,90,$len,'big-5');
			$pdf->Cell(14,5,'Remark','RTL',0,'C');
			$pdf->Cell(249,5,$tmp1,'RTL',1,'L');
			$pdf->Cell(14,5,'','RL',0,'C');
			$pdf->Cell(249,5,$tmp2,'RL',1,'L');
		}else{
			$pdf->Cell(14,5,'Remark','RTL',0,'C');
			$pdf->Cell(249,5,$rmk0,'RTL',1,'L');
		}

$pdf->SetFont('big5','',10);

//$pdf->ln();

$total +=$v_cost;
//if (isset($op['rcv_log'][1]['des']))$rmk1 = $op['rcv_log'][1]['des'];
//$pdf->Cell(14,5,'','RL',0,'C');
//$pdf->Cell(249,5,$rmk1,'RL',0,'L');
//$pdf->SetFont('big5','',10);
//$pdf->Cell(20,5,'Total : ',1,0,'R');
//$pdf->Cell(46,5,$op['rcv']['currency']."$".NUMBER_FORMAT($total,2),1,0,'R'); //稅後總計


if (isset($op['rcv_log']))
{
	for ($i=1; $i<sizeof($op['rcv_log']); $i++)
	{
		$len = mb_strlen($op['rcv_log'][$i]['des'],'big-5');
		
		if($len >= 90)
		{
			$tmp1 = mb_substr($op['rcv_log'][$i]['des'],0,90,'big-5'); 
			$tmp2 = mb_substr($op['rcv_log'][$i]['des'],90,$len,'big-5');
			$pdf->Cell(14,5,'','RL',0,'C');
			$pdf->Cell(249,5,$tmp1,'RL',0,'L');
			$pdf->ln();
			$pdf->Cell(14,5,'','RL',0,'C');
			$pdf->Cell(249,5,$tmp2,'RL',0,'L');
			$pdf->ln();			
		}else{
		  $pdf->Cell(14,5,'','RL',0,'C');
			$pdf->Cell(249,5,$op['rcv_log'][$i]['des'],'RL',0,'L');
			$pdf->ln();
		}
	}
}
$pdf->Cell(14,0,'','RLB',0,'C');
$pdf->Cell(249,0,'','RLB',0,'C');
$pdf->ln();

$pdf->ln();
$pdf->SetFont('Big5','',10);
$pdf->Cell(10,5,' ','0',0,'C');
$pdf->Cell(40,5,'',0,0,'L');

$pdf->Cell(60,5,'APPROVAL : ','0',0,'C');	//PO Approval

$pdf->Cell(60,5,'CONFIRM :',0,0,'L');//PO Confirm
$pdf->Cell(50,5,'Bursary :',0,0,'L');//PO Submit
	
$pdf->Cell(60,5,'Receive :'.$op['rcv']['rcv_sub_user'],0,0,'L');//PA submit


$name=$op['rcv']['rcv_num'].'_rcvd.pdf';
$pdf->Output($name,'D');
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "recv_qty_show": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "recv_qty_show":
check_authority($AUTH,"view");

$op['rcvd']=$apb->get_inventory_qty("i", $PHP_po_num, $PHP_mat_id, $PHP_color, $PHP_size);

page_display($op, $AUTH, "apb_incoming_det_show.html");
break;	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "apb_qty_show": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "apb_qty_show":
check_authority($AUTH,"view");

$op['rcvd']=$apb->get_apb_det($PHP_id,$PHP_ap_num);
$op['unit'] = $PHP_unit;

page_display($op, $AUTH, $TPL_APB_QTY_SHOW);
break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "rcv_rpt": JOB31 驗收報表主頁
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "rcv_rpt":
check_authority($AUTH,"view");		

$op['fty']= $arry2->select($SHIP,"","PHP_ship","select");
$op['year']= $arry2->select($YEAR_WORK,date('Y'),"PHP_year","select");
$op['month']= $arry2->select($MONTH_WORK,"","PHP_month","select");

page_display($op, $AUTH, $TPL_APBD_RPT);
break;	


	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "rcvd_rpt_used": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "rcvd_rpt_used":
 
		check_authority($AUTH,"view");	
		if(!$PHP_str && (!$PHP_year || !$PHP_month)) 
		{
			$op['msg'][] ="Please Select Year, Month or choice Start Date.";
			$op['fty']= $arry2->select($SHIP,"","PHP_ship","select");
			$op['year']= $arry2->select($YEAR_WORK,date('Y'),"PHP_year","select");
			$op['month']= $arry2->select($MONTH_WORK,"","PHP_month","select");
			page_display($op, $AUTH, $TPL_APBD_RPT);
			break;
		}
		if(!isset($PHP_cat1))
		{
			$op['msg'][] ="Please Select Fabric or Accessory.";
			$op['fty']= $arry2->select($SHIP,"","PHP_ship","select");
			$op['year']= $arry2->select($YEAR_WORK,date('Y'),"PHP_year","select");
			$op['month']= $arry2->select($MONTH_WORK,"","PHP_month","select");
			page_display($op, $AUTH, $TPL_APBD_RPT);
			break;
		}
					
		if(!$PHP_str)
		{
			$e_day = getDaysInMonth($PHP_month,$PHP_year);
			$PHP_str = $PHP_year."-".$PHP_month."-01";
			$PHP_fsh = $PHP_year."-".$PHP_month."-".$e_day;
		}else{
			if(!$PHP_fsh) $PHP_fsh = $TODAY;
		}
		
		$op['title2'] = "起 ".$PHP_str." 迄 ".$PHP_fsh;
		if ($PHP_cat1=="F"){$mat = "主料";}else{$mat = "副料";}
		if ($PHP_ship){$PHP_ship2 = $PHP_ship;}else{$PHP_ship2 = 'HJ & LY';}
		$op['title1'] = "[".$PHP_ship2."] ".$mat." 彙總報表";
		$parm = array("str"		=>	$PHP_str,
									"end"		=>	$PHP_fsh,
									"ship"	=>	$PHP_ship,
									"cat"		=>	$PHP_cat1,
									);
		$rcv = $apb->search_rpt_used($parm);
		$rpt = array();
		$tmp_mat = '';
		$j=-1;
		$total_qty=$total_cost=0;
		
	if($rcv)
	{
		for ($i=0; $i<sizeof($rcv); $i++)
		{
			if($tmp_mat <> $rcv[$i]['mat_code'])
			{
				if($j > -1)
				{
					$rpt[$j]['avg_price'] = number_format(($rpt[$j]['cost'] / $rpt[$j]['qty']),2,'.','');
					$rpt[$j]['qty'] = number_format($rpt[$j]['qty'],2,'.','');
				}
				$j++;
				$rpt[$j] =$rcv[$i];
				$rpt[$j]['qty'] = $rcv[$i]['rcv_qty'];
				$tmp_mat = $rcv[$i]['mat_code'];								
			}else{				
				$rpt[$j]['cost']+=$rcv[$i]['cost'];
				$rpt[$j]['qty']+=$rcv[$i]['rcv_qty'];
			}		
			$total_qty+=$rcv[$i]['rcv_qty'];
			$total_cost+=$rcv[$i]['cost'];
		}
		$rpt[$j]['avg_price'] = number_format(($rpt[$j]['cost'] / $rpt[$j]['qty']),2,'.','');
		$rpt[$j]['qty'] = number_format($rpt[$j]['qty'],2,'.','');
	}
		$op['rpt'] = $rpt;
		$op['sch'] = $parm;
		$op['total_qty'] = $total_qty;
		$op['total_cost'] = $total_cost;
		
		if(!isset($PHP_pdf) && !isset($PHP_excel))
		{
			page_display($op, $AUTH, $TPL_APB_RPT_USED);
			break;			
		}elseif(isset($PHP_pdf)){
			include_once($config['root_dir']."/lib/class.pdf_rpt.php");

			$print_title = $op['title1'];
			$print_title2 = $op['title2'];
			$creator = $GLOBALS['SCACHE']['ADMIN']['name']." [ ".$GLOBALS['THIS_TIME']." ]";
			$mark = $PHP_ship2;
			$pdf=new PDF_rpt('P','mm','A4');
			$pdf->AddBig5Font();
			$pdf->Open();
			$pdf->AddPage();
			$pdf->SetFont('Arial','B',14);
			// [表匡] 訂單基本資料
			$title_ary = array ('物料 #','物料名稱','驗收量','單價','成本');
			$title_size = array (30,70,30,30,30);
			$rpt_ail = array ('c','L','R','R','R');

			$pdf->rpt_title($title_ary,$title_size);
			$cg = 0;
			for($i=0; $i<sizeof($rpt); $i++)
			{
				if ($cg > 34)
				{
					$pdf->AddPage();		
					$pdf->rpt_title($title_ary,$title_size);
					$cg = 0;
				}
				$cg++;
				$rpt[$i]['qty'] = number_format($rpt[$i]['qty'],2,'.',',');
				$rpt[$i]['cost'] = number_format($rpt[$i]['cost'],2,'.',',');
				$rpt[$i]['avg_price'] = number_format($rpt[$i]['avg_price'],2,'.',',');
				$rpt_data = array($rpt[$i]['mat_code'],$rpt[$i]['mat_name'],$rpt[$i]['qty'].' '.$rpt[$i]['unit'],
										$rpt[$i]['avg_price'],$rpt[$i]['cost']);
				$pdf->rpt_data($rpt_data,$title_size,$rpt_ail);
			}

			$total_qty = number_format($total_qty,2,'.',',');
			$total_cost = number_format($total_cost,2,'.',',');
			$rpt_data = array('TOTAL','',$total_qty,'',$total_cost);
			$pdf->rpt_total($rpt_data,$title_size,$rpt_ail);


			$name=$PHP_ship.'rpt_used.pdf';
			$pdf->Output($name,'D');
			break;	
		}else{
			require_once($config['root_dir']."/lib/spreadsheets/Worksheet.php");
			require_once($config['root_dir']."/lib/spreadsheets/Workbook.php");

	  	function HeaderingExcel($filename) {
			  header("Content-type: application/vnd.ms-excel");
			  header("Content-Disposition: attachment; filename=$filename" );
			  header("Expires: 0");
			  header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
			  header("Pragma: public");
		 	}
	 		 // HTTP headers
	 		 HeaderingExcel('order.apved.xls');
	 
	 		 // Creating a workbook
	 		 $workbook = new Workbook("-");

	  		// Creating the first worksheet
	 		 $worksheet1 =& $workbook->add_worksheet('order list');

			 $now = $GLOBALS['THIS_TIME'];		
			// 寫入 title

	 		 // Format for the headings
			$formatot =& $workbook->add_format();
	  	$formatot->set_size(10);
	  	$formatot->set_align('center');
	  	$formatot->set_color('white');
	  	$formatot->set_pattern();
	  	$formatot->set_fg_color('navy');
	  	
	 		$f3 =& $workbook->add_format(); //置右數字
	  	$f3->set_size(10);
	  	$f3->set_align('right');
	  	$f3->set_num_format(3);

	 		$f4 =& $workbook->add_format();  //灰底白字置右數字
	  	$f4->set_size(10);
	  	$f4->set_color('white');
	  	$f4->set_pattern(1);
	 	  $f4->set_align('right');
	 	  $f4->set_num_format(3);
	  	$f4->set_fg_color('grey');
  		$f4->set_num_format(3);

	 		$f5 =& $workbook->add_format();  //灰底白字置中
	  	$f5->set_size(10);
	  	$f5->set_color('white');
	  	$f5->set_pattern(1);
	 	  $f5->set_align('center');
	 	  $f5->set_num_format(3);
	  	$f5->set_fg_color('grey');

	  	$worksheet1->set_column(0,0,15);
	  	$worksheet1->set_column(0,1,30);
	  	$worksheet1->set_column(0,2,15);
	  	$worksheet1->set_column(0,3,15);
	  	$worksheet1->set_column(0,4,15);  


	  $worksheet1->write_string(0,1,$op['title1']." [ ".$op['title2']."]");
	  $worksheet1->write(1,1,"printed:".$GLOBALS['SCACHE']['ADMIN']['name']." [ ".$GLOBALS['THIS_TIME']." ]");

	  $worksheet1->write_string(2,0,"物料#",$formatot);
	  $worksheet1->write_string(2,1,"物料名稱",$formatot);
	  $worksheet1->write_string(2,2,"驗收量",$formatot);
	  $worksheet1->write_string(2,3,"單價",$formatot);
	  $worksheet1->write_string(2,4,"成本",$formatot);
	  	

	for ($i=0; $i<sizeof($rpt); $i++){
	  $worksheet1->write($i+3,0,$rpt[$i]['mat_code']);
	  $worksheet1->write($i+3,1,$rpt[$i]['mat_name']);
	  $worksheet1->write($i+3,2,$rpt[$i]['qty']." ".$rpt[$i]['unit'],$f3);
	  $worksheet1->write_number($i+3,3,$rpt[$i]['avg_price'],$f3);
	  $worksheet1->write_number($i+3,4,$rpt[$i]['cost'],$f3);
	
	}
  $worksheet1->write($i+3,0,'Total',$f5);
  $worksheet1->write($i+3,1,'',$f5);
  $worksheet1->write_number($i+3,2,$total_qty,$f4);
  $worksheet1->write_number($i+3,3,'',$f4);
  $worksheet1->write_number($i+3,4,$total_cost,$f4);

  $workbook->close();

	break;
}



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "rcvd_rpt_used": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "rcvd_rpt_dtl":
check_authority($AUTH,"view");
	
		if(!$PHP_str && (!$PHP_year || !$PHP_month)) 
		{
			$op['msg'][] ="Please Select Year, Month or choice Start Date.";
			$op['fty']= $arry2->select($SHIP,"","PHP_ship","select");
			$op['year']= $arry2->select($YEAR_WORK,date('Y'),"PHP_year","select");
			$op['month']= $arry2->select($MONTH_WORK,"","PHP_month","select");
			page_display($op, $AUTH, $TPL_APBD_RPT);
			break;
		}
		if(!isset($PHP_cat1))
		{
			$op['msg'][] ="Please Select Fabric or Accessory.";
			$op['fty']= $arry2->select($SHIP,"","PHP_ship","select");
			$op['year']= $arry2->select($YEAR_WORK,date('Y'),"PHP_year","select");
			$op['month']= $arry2->select($MONTH_WORK,"","PHP_month","select");
			page_display($op, $AUTH, $TPL_APBD_RPT);
			break;
		}
					
		if(!$PHP_str)
		{
			$e_day = getDaysInMonth($PHP_month,$PHP_year);
			$PHP_str = $PHP_year."-".$PHP_month."-01";
			$PHP_fsh = $PHP_year."-".$PHP_month."-".$e_day;
		}else{
			if(!$PHP_fsh) $PHP_fsh = $TODAY;
		}
		
		$op['title2'] = "起 ".$PHP_str." 迄 ".$PHP_fsh;
		if ($PHP_cat1=="F"){$mat = "主料";}else{$mat = "副料";}
		if ($PHP_ship){$PHP_ship2 = $PHP_ship;}else{$PHP_ship2 = 'HJ & LY';}
		$op['title1'] = "[".$PHP_ship2."] ".$mat." 驗收列表 ";
		
		if(!isset($PHP_price))$PHP_price = 0;
		$parm = array("str"		=>	$PHP_str,
									"end"		=>	$PHP_fsh,
									"ship"	=>	$PHP_ship,
									"cat"		=>	$PHP_cat1,
									"ord"		=>	$PHP_ord,
									"price"	=>	$PHP_price
									);
		$rcv = $apb->search_rpt_dtl($parm);
		$rpt = array();		
		$j=0;
		$sub_cost=$sub_qty=$total_qty=$total_cost=0;
		$ary_unit= array();
	if($rcv)
	{
	
		$rcv = bubble_sort($rcv);
		$tmp_ord = $rcv[0]['ord_num'];
		$tmp_unit = '';
		$x = 0;
		$ary_qty[0] = 0;
	  $ary_unit[0] = $rcv[0]['unit'];					

		for ($i=0; $i<sizeof($rcv); $i++)
		{			
			if($tmp_ord <> $rcv[$i]['ord_num'])
			{		
				$rpt[$j]['ord_num'] = 'Sub-Total';
				$rpt[$j]['rcv_qty'] = '';
			  for($k=0; $k<sizeof($ary_unit); $k++)
			  {
					$rpt[$j]['rcv_qty'] .= NUMBER_FORMAT($ary_qty[$k],2,'.',',').$ary_unit[$k]."+";
  			}
				$rpt[$j]['rcv_qty'] = substr($rpt[$j]['rcv_qty'],0,-1);
		//		$rpt[$j]['rcv_qty'] = $sub_qty;
				
				$rpt[$j]['cost'] = $sub_cost;
				$rpt[$j]['ln_mk'] = 1;
//				$sub_qty=$sub_cost=0;	
				$j++;
				$tmp_ord = $rcv[$i]['ord_num'];									
			}
			
//			$total_qty+=$rcv[$i]['rcv_qty'];
			$total_cost+=$rcv[$i]['cost'];				
			
			$tmp_x = -1;
			for($k=0; $k<sizeof($ary_unit); $k++)
			{
				if($ary_unit[$k] ==$rcv[$i]['unit']) 
				{
					$tmp_x = $k;
					break;
				}					
			}
			
			if($tmp_x > -1)
			{
				$ary_qty[$tmp_x]+= $rcv[$i]['rcv_qty'];	
									
			}else{
				$x++;
				$ary_qty[$x] = $rcv[$i]['rcv_qty'];
	 			$ary_unit[$x] = $rcv[$i]['unit'];						
			} // end if($tmp_j > -1)
	
		
			
			if(isset($rcv[$i]['price']))$rcv[$i]['price'] = number_format($rcv[$i]['price'],2,'.','');
			if(isset($rcv[$i]['qty']))  $rcv[$i]['rcv_qty'] = number_format($rcv[$i]['rcv_qty'],2,'.','');
			if(isset($rcv[$i]['qty']))  $rcv[$i]['cost'] = number_format($rcv[$i]['cost'],2,'.','');			
			$rpt[$j] = $rcv[$i];	
			$rpt[$j]['ln_mk'] = 0;		
//			$sub_qty +=$rcv[$i]['rcv_qty'];
			$sub_cost+=$rcv[$i]['cost'];
			$j++;
			
		}
	}
/*		
		$rpt[$j]['ord_num'] = 'sub_total';
		$rpt[$j]['qty'] = $sub_qty;
		$rpt[$j]['cost'] = $sub_cost;
		$rpt[$j]['ln_mk'] = 1;
*/		
		$op['rpt'] = $rpt;
		$op['sch'] = $parm;
		$op['total_qty'] = $total_qty;
		$op['total_cost'] = $total_cost;

		$op['total_qty'] = '';
	  for($k=0; $k<sizeof($ary_unit); $k++)
	  {
	  	
			$op['total_qty'] .= NUMBER_FORMAT($ary_qty[$k],2,'.',',').$ary_unit[$k]."+";
		}
		$op['total_qty'] = substr($op['total_qty'],0,-1);
		
		
		if(!isset($PHP_pdf) && !isset($PHP_excel))
		{
			page_display($op, $AUTH, $TPL_APB_RPT_DTL);
			break;					
		}else if(isset($PHP_pdf)){
			include_once($config['root_dir']."/lib/class.pdf_rpt.php");

			$print_title = $op['title1'];
			$print_title2 = $op['title2'];
			$creator = $GLOBALS['SCACHE']['ADMIN']['name']." [ ".$GLOBALS['THIS_TIME']." ]";
			$mark = $PHP_ship2;
			$pdf=new PDF_rpt('P','mm','A4');
			$pdf->AddBig5Font();
			$pdf->Open();
			$pdf->AddPage();
			$pdf->SetFont('Arial','B',14);
			// [表匡] 訂單基本資料
			if($PHP_price == 1)
			{
				$title_ary = array ('訂單 #','驗收單 #','驗收日','收料單位','驗收部門','物料#','驗收量','單價','成本');
				$title_size = array (20,20,20,15,15,25,25,25,25);
				$rpt_ail = array ('C','C','C','C','C','C','R','R','R');
				$total_size = array(60,80,50);
				$total_ail = array ('R','R','R');
			}else{			
				$title_ary = array ('訂單 #','驗收單 #','驗收日','收料單位','驗收部門','物料 #','驗收量');
				$title_size = array (25,25,30,20,20,35,35);
				$rpt_ail = array ('C','C','C','C','C','C','R');
				$total_size = array(80,110);
				$total_ail = array ('R','R','C');
			}
			$pdf->rpt_title($title_ary,$title_size);
			$cg = 0;
			for($i=0; $i<sizeof($rpt); $i++)
			{
				if ($cg > 34)
				{
					$pdf->AddPage();		
					$pdf->rpt_title($title_ary,$title_size);
					$cg = 0;
				}
				$cg++;
				$rpt[$i]['qty'] = number_format($rpt[$i]['rcv_qty'],2,'.',',');
				$rpt[$i]['cost'] = number_format($rpt[$i]['cost'],2,'.',',');
				if($rpt[$i]['ln_mk'] == 1)
				{
					if($PHP_price == 1)
					{
						$rpt_data = array($rpt[$i]['ord_num'],$rpt[$i]['rcv_qty'],$rpt[$i]['cost']);
					}else{
						$rpt_data = array($rpt[$i]['ord_num'],$rpt[$i]['rcv_qty']);
					}
					$pdf->rpt_total($rpt_data,$total_size,$total_ail);					
				}else{
					
					if($PHP_price == 1)
					{
						if(isset($rpt[$i]['price']))$rpt[$i]['price'] = number_format($rpt[$i]['price'],2,'.',',');
						$rpt_data = array($rpt[$i]['ord_num'],$rpt[$i]['rcv_num'],$rpt[$i]['rcv_sub_date'],
															$rpt[$i]['ship'],$rpt[$i]['dept'],$rpt[$i]['mat_code'],$rpt[$i]['rcv_qty'].' '.$rpt[$i]['unit'],
															$rpt[$i]['price'],$rpt[$i]['cost']);
					}else{
						$rpt_data = array($rpt[$i]['ord_num'],$rpt[$i]['rcv_num'],$rpt[$i]['rcv_sub_date'],
															$rpt[$i]['ship'],$rpt[$i]['dept'],$rpt[$i]['mat_code'],$rpt[$i]['rcv_qty'].' '.$rpt[$i]['unit']
															);
					}

					$pdf->rpt_data($rpt_data,$title_size,$rpt_ail);
				}
			}

			$total_qty = number_format($total_qty,2,'.',',');
			$total_cost = number_format($total_cost,2,'.',',');
			if($PHP_price == 1)
			{
				$rpt_data = array('TOTAL',$op['total_qty'],$total_cost);
			}else{
				$rpt_data = array('TOTAL',$op['total_qty']);
			}
			$pdf->rpt_total($rpt_data,$total_size,$total_ail);


			$name=$PHP_ship.'rpt_list.pdf';
			$pdf->Output($name,'D');
			break;				
		}else{
			require_once($config['root_dir']."/lib/spreadsheets/Worksheet.php");
			require_once($config['root_dir']."/lib/spreadsheets/Workbook.php");

	  	function HeaderingExcel($filename) {
			  header("Content-type: application/vnd.ms-excel");
			  header("Content-Disposition: attachment; filename=$filename" );
			  header("Expires: 0");
			  header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
			  header("Pragma: public");
		 	}
	 		 // HTTP headers
	 		 HeaderingExcel('rcvd_detial.xls');
	 
	 		 // Creating a workbook
	 		 $workbook = new Workbook("-");

	  		// Creating the first worksheet
	 		 $worksheet1 =& $workbook->add_worksheet('Receive list');

			 $now = $GLOBALS['THIS_TIME'];		
			// 寫入 title

	 		 // Format for the headings
			$formatot =& $workbook->add_format();
	  	$formatot->set_size(10);
	  	$formatot->set_align('center');
	  	$formatot->set_color('white');
	  	$formatot->set_pattern();
	  	$formatot->set_fg_color('navy');
	  	
	 		$f3 =& $workbook->add_format(); //置右數字有小數點
	  	$f3->set_size(10);
	  	$f3->set_align('right');
	  	$f3->set_num_format(4);

	 		$f2 =& $workbook->add_format(); //置右數字整數
	  	$f2->set_size(10);
	  	$f2->set_align('right');
	  	$f2->set_num_format(3);


	 		$f4 =& $workbook->add_format();  //灰底白字置右數字有小數點二位
	  	$f4->set_size(10);
	  	$f4->set_color('white');
	  	$f4->set_pattern(1);
	 	  $f4->set_align('right');
	 	  $f4->set_num_format(4);
	  	$f4->set_fg_color('grey');

	 		$f6 =& $workbook->add_format();  //灰底白字置右數字整數
	  	$f6->set_size(10);
	  	$f6->set_color('white');
	  	$f6->set_pattern(1);
	 	  $f6->set_align('right');
	 	  $f6->set_num_format(4);
	  	$f6->set_fg_color('grey');


	 		$f5 =& $workbook->add_format();  //灰底白字置中
	  	$f5->set_size(10);
	  	$f5->set_color('white');
	  	$f5->set_pattern(1);
	 	  $f5->set_align('center');
	  	$f5->set_fg_color('grey');

	  	$worksheet1->set_column(0,0,10);
	  	$worksheet1->set_column(0,1,10);	  	
	  	$worksheet1->set_column(0,2,12);
	  	$worksheet1->set_column(0,3,10);
	  	$worksheet1->set_column(0,4,8);  
	  	$worksheet1->set_column(0,5,15);
	  	$worksheet1->set_column(0,6,15);
	  	$worksheet1->set_column(0,7,15);
	  	$worksheet1->set_column(0,8,15);


	  $worksheet1->write_string(0,1,$op['title1']." [ ".$op['title2']."]");
	  $worksheet1->write(1,1,"printed:".$GLOBALS['SCACHE']['ADMIN']['name']." [ ".$GLOBALS['THIS_TIME']." ]");

	  $worksheet1->write_string(2,0,"訂單#",$formatot);
	  $worksheet1->write_string(2,1,"驗收單#",$formatot);
	  $worksheet1->write_string(2,2,"驗收日",$formatot);
	  $worksheet1->write_string(2,3,"收料單位",$formatot);
	  $worksheet1->write_string(2,4,"驗收部門",$formatot);
	  $worksheet1->write_string(2,5,"物料#",$formatot);
	  $worksheet1->write_string(2,6,"驗收量",$formatot);
  	if ($PHP_price == 1)
  	{
	  	$worksheet1->write_string(2,7,"單價",$formatot);
	  	$worksheet1->write_string(2,8,"成本",$formatot);
	  }
	  	

	for ($i=0; $i<sizeof($rpt); $i++){
		if($rpt[$i]['ln_mk'] == 1)
		{
	  	$worksheet1->write($i+3,0,$rpt[$i]['ord_num'],$f5);
		  for($j=1; $j < 6; $j++) $worksheet1->write($i+3,$j,'',$f5);
	  	$worksheet1->write_string($i+3,6,$rpt[$i]['rcv_qty'],$f6);
	  	if ($PHP_price == 1)
	  	{
	  		$worksheet1->write_string($i+3,7,'',$f5);
	  		$worksheet1->write_number($i+3,8,$rpt[$i]['cost'],$f4);
			}
		}else{
	  	$worksheet1->write($i+3,0,$rpt[$i]['ord_num']);
	  	$worksheet1->write($i+3,1,$rpt[$i]['rcv_num']);
	  	$worksheet1->write($i+3,2,$rpt[$i]['rcv_sub_date']);
	  	$worksheet1->write($i+3,3,$rpt[$i]['ship']);
	  	$worksheet1->write($i+3,4,$rpt[$i]['dept']);
	  	$worksheet1->write($i+3,5,$rpt[$i]['mat_code']);
	  	$worksheet1->write($i+3,6,$rpt[$i]['rcv_qty']." ".$rpt[$i]['unit'],$f2);
	  	if ($PHP_price == 1)
	  	{
	  		$worksheet1->write_number($i+3,7,$rpt[$i]['price'],$f3);
	  		$worksheet1->write_number($i+3,8,$rpt[$i]['cost'],$f3);
	  	}
		}
	}
  $worksheet1->write($i+3,0,'Total',$f5);
  for($j=1; $j < 6; $j++) $worksheet1->write($i+3,$j,'',$f5);
  $worksheet1->write_string($i+3,6,$op['total_qty'],$f6);
 	if ($PHP_price == 1)
 	{
  	$worksheet1->write_number($i+3,7,'',$f4);
  	$worksheet1->write_number($i+3,8,$total_cost,$f4);
	}
  $workbook->close();

	break;
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "rev_po_det": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "rev_po_det":
check_authority($AUTH,"view");	

$op['rcvd'] = $apb->get_apb_po_det($PHP_apb_po_id,$PHP_special,$PHP_ord_num);

page_display($op, $AUTH, $TPL_RCV_PO_SHOW );
break;


case "rev_bom_pre_apb_det":
check_authority($AUTH,"view");	

$op['apb_det'] = $apb->get_bom_pre_apb_det($_GET['PHP_po_nums'], $_GET['PHP_mat_cat'], $_GET['PHP_mat_id'], $_GET['PHP_color'], $_GET['PHP_size'], $PHP_ord_num);

page_display($op, $AUTH, "bom_status_apb_det_show.html" );
break;


case "rcv_bom_apb_det":
check_authority($AUTH,"view");	

$op['apb_det'] = $apb->get_bom_apb_det($_GET['PHP_po_nums'], $_GET['PHP_mat_cat'], $_GET['PHP_mat_id'], $_GET['PHP_color'], $_GET['PHP_size'], $PHP_ord_num);

page_display($op, $AUTH, "bom_status_apb_det_show.html" );
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "add_ord_tmp": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "add_ord_tmp":
check_authority($AUTH,"view");

$apb->add_order();

break;					



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		job 24A sample pattern upload
#		case "upload_smpl_pattern":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "upload_file":
check_authority($AUTH,"add");

$filename = $_FILES['PHP_pttn']['name'];		
$ext =  strtolower(preg_replace("/.*\.([^.]+)$/","\\1", $filename));

if(strstr($PHP_desp,'&#'))
{
	$PHP_desp = $ch_cov->check_cov($PHP_desp);
}			

$f_check = 0;
for ($i=0; $i<sizeof( $GLOBALS['VALID_TYPES']); $i++)
{
	if ($GLOBALS['VALID_TYPES'][$i] == $ext)
	{
		echo $f_check = 1;
		
		// break;
	}
}		

if ($filename && $PHP_desp)
{
	if ($_FILES['PHP_pttn']['size'] < 2072864 && $_FILES['PHP_pttn']['size'] > 0)	{
		if ($f_check == 1) {   // 上傳檔的副檔名為有效時 -----

			// upload pattern file to server
			$today = $GLOBALS['TODAY'];
			$user_name =  $GLOBALS['SCACHE']['ADMIN']['name'];
			$parm = array(
			"id"					=>  $PHP_id,
			"file_des"		=>	$PHP_desp,
			"file_user"		=>	$user_name,
			"file_date"		=>	$today
			);
			
			$A = $fils->get_name_id('apb_file');
			$pttn_name = $PHP_num."_".$A.".".$ext;  // 組合檔名
			$parm['file_name'] = $pttn_name;
			
			$str_long=strlen($pttn_name);
			$upload = new Upload;
			print_r($_FILES);
			$upload->setMaxSize(2072864);
			$upload->uploadFile(dirname($PHP_SELF).'/apb_file/', 'other', 16, $pttn_name );
			$upload->setMaxSize(2072864);
			if (!$upload){
				$op['msg'][] = $upload;
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			
			if (!$A = $fils->upload_apb_file($parm)){
				$op['msg'] = $fils->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}			
			$message = "UPLOAD file of : ".$PHP_num;
			$log->log_add(0,"040A",$message);
		} else {  // 上傳檔的副檔名  是  exe 時 -----
			$message = "upload file is incorrect format ! Please re-send.";
		}
	} else {  //上傳檔名重覆時
		$message = "Upload file is too big";
	}
} else {
	$message="You don't pick any file or add file descript.";
}	
	
$op = $apb->get($PHP_num);
echo $PHP_num;
$op['back_str'] = $PHP_back_str;		

$op['msg'][] = $message;
page_display($op, $AUTH, $TPL_APB_SHOW);
break;	



//=======================================================
case "do_file_del":
check_authority($AUTH,"edit");	

$f1 = $fils->del_file($PHP_talbe,$PHP_id);
if (!$f1) {
	$op['msg'] = $fils->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}

if(file_exists($GLOBALS['config']['root_dir']."/apb_file/".$PHP_file_name)){
	unlink("./apb_file/".$PHP_file_name);
}

$op = $apb->get($PHP_rcv_num);
if(isset($PHP_sr_startno))$op['back_str'] = "&SCH_num=".$SCH_num."&SCH_supl=".$SCH_supl."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_sr_startno=".$PHP_sr_startno;
page_display($op, $AUTH, $TPL_APB_SHOW);
break;



//-------------------------------------------------------------------------
case "apb2excel":

if($groupby==5){ // 國內 TIPTOP 匯出資料 Domestic 

    $q_str = "select distinct `rcv_num`,`inv_num`,`status` 
                from `apb` 
                where (`rcv_date` between '$PHP_begin_date' and '$PHP_end_date') AND `apb`.`inv_num` != '' group by `inv_num` ORDER BY `rcv_date` ASC ";
    
    $e = $apb->get_rcv_num($q_str);
    $s_size = sizeof($e['status']);
    for($i=0;$i<$s_size;$i++){
        if ($e['status'][$i] % 2 == 1 ){
            echo "<script>history.go(-1);alert('查詢期間($PHP_begin_date ~ $PHP_end_date)有尚未Submit之驗收單！');</script>";
            exit;
        }
    }
    
    $op = array();
    $op = $apb->apb_tiptop_domestic($e,$PHP_begin_date,$PHP_end_date);

    require_once($config['root_dir']."/lib/spreadsheets/Worksheet.php");
    require_once($config['root_dir']."/lib/spreadsheets/Workbook.php");

    function HeaderingExcel($filename) {
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=$filename");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
        header("Pragma: public");
    }

    // HTTP headers
    HeaderingExcel('TIPTOP-Domestic -'.$PHP_begin_date.'-'.$PHP_end_date.'.xls');
 
    // Creating a workbook
    $workbook = new Workbook("-");

    // Creating the first worksheet
    $worksheet1 =& $workbook->add_worksheet('apb list');

    $now = $GLOBALS['THIS_TIME'];

    // 寫入 title

    //欄位名稱用
    $formatot =& $workbook->add_format();
    $formatot->set_size(10);
    $formatot->set_align('center');
    $formatot->set_color('white');
    $formatot->set_pattern();
    $formatot->set_fg_color('navy');
    
    //(給 稅額 用) 可刪掉	2011/12/19
    $formatot2 =& $workbook->add_format();
    $formatot2->set_size(10);
    $formatot2->set_align('center');
    $formatot2->set_color('white');
    $formatot2->set_pattern();
    $formatot2->set_fg_color('gray');
    
    //表頭用
    $title_style =& $workbook->add_format();
    $title_style->set_size(12);
    $title_style->set_align('center');
    $title_style->set_color('block');
    $title_style->set_pattern();
    $title_style->set_fg_color('white');
    
    //小計數量用
    $f1 =& $workbook->add_format();
    $f1->set_size(10);
    $f1->set_align('right');
    $f1->set_color('block');
    $f1->set_pattern();
    $f1->set_fg_color('yellow');
  
    //主副料小計用
    $f2 =& $workbook->add_format();
    $f2->set_size(10);
    $f2->set_align('right');
    $f2->set_color('block');
    $f2->set_pattern();
    $f2->set_fg_color('orange');
    
    //國內外小計用
    $f3 =& $workbook->add_format();
    $f3->set_size(10);
    $f3->set_align('right');
    $f3->set_color('white');
    $f3->set_pattern();
    $f3->set_fg_color('navy');
    
    //總合計用
    $f4 =& $workbook->add_format();
    $f4->set_size(10);
    $f4->set_align('right');
    $f4->set_color('white');
    $f4->set_pattern();
    $f4->set_fg_color('red');
    
    $ary_size = array(6,10,9,11,10,10,4,6,5,8,8,8,8,4,4,6,14,10,14,14,6);
    
    for ($i=0; $i<sizeof($ary_size); $i++){
        $worksheet1->set_column(0,$i,$ary_size[$i]);	  
    } 
  
    $ary = array("單別"	,"帳款日期"	,"廠別編號"	,"發票號碼"	,"發票日期"	,"付款方式"	,"幣別"	,"匯率"	,"稅別"	,"請款人員"	,"部門"	,"帳款類別"	,"廠別"	,"項次"	,"品名"	,"數量"	,"原幣金額(未稅)"	,"原入庫號"	,"未稅金額(原幣)"	,"未稅金額(本幣)");
    for ($i=0; $i<sizeof($ary); $i++) $worksheet1->write_string(0,$i,$ary[$i],$formatot);	

    //給 "稅額" 用 可刪掉
    $worksheet1->write_string(0,$i,"稅額",$formatot2);
    
    // Format for the numbers
    $formatnum =& $workbook->add_format();
    $formatnum->set_size(10);
    $formatnum->set_align('center');
    $formatnum->set_color('black');
    $formatnum->set_fg_color('B8D7D8');
    $data = array();
    
    $p=0;
    $y=substr($PHP_begin_date,0,4);
    $m=substr($PHP_begin_date,5,2);
    $maxday=getDaysInMonth($m,$y);
    
    if($op <> 0){
        $i=0;//列數用
        for($j=0;$j<sizeof($op);$j++){
            $worksheet1->write($i+1,0,$op[$j]['inv_num']!=NULL?"CP11":"CP12");
            $worksheet1->write($i+1,1,str_replace("-","/",$op[$j]['rcv_date']));
            $worksheet1->write($i+1,2,$op[$j]['uni_no'],0,1);
            $worksheet1->write($i+1,3,$op[$j]['inv_num']);
            $worksheet1->write($i+1,4,str_replace("-","/",$op[$j]['inv_date']));
            $worksheet1->write($i+1,5,$apb->get_apb_pay($op[$j]['dm_way']));
            // $worksheet1->write($i+1,6,$op[$j]['currency']);
            $worksheet1->write($i+1,6,"NTD");
            // $rates = $rate->get_rate($op[$j]['currency'],$op[$j]['rcv_date']);
            // $worksheet1->write($i+1,7,($rates==0)?"1":$rates);
            $worksheet1->write($i+1,7,"1");
            $worksheet1->write($i+1,8,$op[$j]['inv_num']!=NULL?"T305":"T000");
            $where_str = " WHERE login_id = '".$op[$j]['rcv_sub_user']."'";
            $rcv_dept_name=$user->get_fields('emp_id',$where_str);
            $worksheet1->write($i+1,9,$apb->get_apb_dept($rcv_dept_name[0]));
            //$SHIP = $apb->get_apb_dept($op[$j]['ship']);
            //$worksheet1->write($i+1,10,( $SHIP == 'AEJ0000') ? "AHF0000" : "AHF1000" );
			$worksheet1->write($i+1,10,$apb->get_apb_dept($op[$j]['ord_dept']));
            $worksheet1->write($i+1,11,substr($op[$j]['mat_code'],0,1)=='A'?"61B":"61A");
            //$worksheet1->write($i+1,12,$SHIP);		
			$worksheet1->write($i+1,12,$apb->get_apb_dept($op[$j]['ord_fty']));		
            $worksheet1->write($i+1,13,"1");
            $worksheet1->write($i+1,14,substr($op[$j]['mat_code'],0,1)=='A'?"副料":"主料");
            $worksheet1->write($i+1,15,$op[$j]['sum_qty']);

            $ds = round($op[$j]['sum_uprice'],0);
            $vs = round($op[$j]['sum_vat'],0);
            $worksheet1->write($i+1,16,$ds-$vs);
            $worksheet1->write($i+1,17,$op[$j]['rcv_num']);
            $worksheet1->write($i+1,18,$ds-$vs);
            $worksheet1->write($i+1,19,$ds-$vs);
            $worksheet1->write($i+1,20,$vs);
            $i++;
        }
    } else { //無資料
        $worksheet1->write(5,0,'no data',$formatot);
    }
    $workbook->close();

} elseif( $groupby == 7 ) { // 國內 ( by 物料 )
    
    $q_str = "select distinct `rcv_num`,`inv_num`,`status` 
                from `apb` 
                where (`rcv_date` between '$PHP_begin_date' and '$PHP_end_date') AND `apb`.`inv_num` != '' group by `inv_num` ORDER BY `inv_num` ASC ";
    
    $e = $apb->get_rcv_num($q_str);
    $s_size = sizeof($e['status']);
    for($i=0;$i<$s_size;$i++){
        if ($e['status'][$i] % 2 == 1){
            echo "<script>history.go(-1);alert('查詢期間($PHP_begin_date ~ $PHP_end_date)有尚未Submit之驗收單！');</script>";
            exit;
        }
    }
    
    $op = array();
    $op = $apb->apb_tiptop_domestic($e,$PHP_begin_date,$PHP_end_date);
	
	
	# 重新排序
	$mo = array();
	$mos = array();
	foreach($op as $k => $v){
		$mo[$v['uni_no']][] = $v;
		$mos[$v['uni_no']] = $v['uni_no'];
	}

	asort($mos);

	$op = array();
	foreach($mos as $key){
		foreach($mo[$key] as $k => $v){
			$op[] = $v;
		}
	}
	
	require_once($config['root_dir']."/lib/spreadsheets/Worksheet.php");
    require_once($config['root_dir']."/lib/spreadsheets/Workbook.php");

    function HeaderingExcel($filename) {
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=$filename");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
        header("Pragma: public");
    }

    // HTTP headers
    HeaderingExcel('TIPTOP-Domestic -'.$PHP_begin_date.'-'.$PHP_end_date.'.xls');
 
    // Creating a workbook
    $workbook = new Workbook("-");

    // Creating the first worksheet
    $worksheet1 =& $workbook->add_worksheet('apb list');

    $now = $GLOBALS['THIS_TIME'];

    // 寫入 title

    //欄位名稱用
    $formatot =& $workbook->add_format();
    $formatot->set_size(10);
    $formatot->set_align('center');
    $formatot->set_color('white');
    $formatot->set_pattern();
    $formatot->set_fg_color('navy');
    
    //(給 稅額 用) 可刪掉	2011/12/19
    $formatot2 =& $workbook->add_format();
    $formatot2->set_size(10);
    $formatot2->set_align('center');
    $formatot2->set_color('white');
    $formatot2->set_pattern();
    $formatot2->set_fg_color('gray');
    
    //表頭用
    $title_style =& $workbook->add_format();
    $title_style->set_size(12);
    $title_style->set_align('center');
    $title_style->set_color('block');
    $title_style->set_pattern();
    $title_style->set_fg_color('white');
    
    //小計數量用
    $f1 =& $workbook->add_format();
    $f1->set_size(10);
    $f1->set_align('right');
    $f1->set_color('block');
    $f1->set_pattern();
    $f1->set_fg_color('yellow');
  
    //主副料小計用
    $f2 =& $workbook->add_format();
    $f2->set_size(10);
    $f2->set_align('right');
    $f2->set_color('block');
    $f2->set_pattern();
    $f2->set_fg_color('orange');
    
    //國內外小計用
    $f3 =& $workbook->add_format();
    $f3->set_size(10);
    $f3->set_align('right');
    $f3->set_color('white');
    $f3->set_pattern();
    $f3->set_fg_color('navy');
    
    //總合計用
    $f4 =& $workbook->add_format();
    $f4->set_size(10);
    $f4->set_align('right');
    $f4->set_color('white');
    $f4->set_pattern();
    $f4->set_fg_color('red');
    
    $ary_size = array(8,9,9,15,12,9,8,7,7,9,9,9,9);
    
    for ($i=0; $i<sizeof($ary_size); $i++){
        $worksheet1->set_column(0,$i,$ary_size[$i]);	  
    } 

    $ary = array("借方名稱"	,"廠別代碼"	,"統一編號"	,"對象簡稱"	,"發票號碼"	,"發票金額"	,"稅額"	,"折讓金" ,"折讓稅"	,"部門代碼"	,"部門名稱"	,"廠別名稱"	,"處理年月"	);
    for ($i=0; $i<sizeof($ary); $i++) $worksheet1->write_string(0,$i,$ary[$i],$formatot);	

    // Format for the numbers
    $formatnum =& $workbook->add_format();
    $formatnum->set_size(10);
    $formatnum->set_align('center');
    $formatnum->set_color('black');
    $formatnum->set_fg_color('B8D7D8');
    $data = array();
    
    $p=0;
    $y=substr($PHP_begin_date,0,4);
    $m=substr($PHP_begin_date,5,2);
    $maxday=getDaysInMonth($m,$y);
    
    if($op <> 0){
		$i=0;//列數用
		$t_price = $t_vat = $t_discount = $t_off_duty = 0; // 總合計用
		
		# 主料 - 貿易一部
		$s_price = $s_vat = $s_discount = $s_off_duty = 0; // 小計用
		$g_price = $g_vat = $g_discount = $g_off_duty = 0; // 合計用
        for($j=0;$j<sizeof($op);$j++){
			if ( substr($op[$j]['mat_code'],0,1) == 'F' && $op[$j]['ord_dept'] == 'DA' ) {
				$m=0;
				$SHIP = $apb->get_apb_dept($op[$j]['ord_fty']);
				$worksheet1->write($i+1,$m++,substr($op[$j]['mat_code'],0,1)=='A'?"副料":"主料");		#借方名稱
				$worksheet1->write($i+1,$m++,$SHIP);													#廠別代碼
				$worksheet1->write($i+1,$m++,$op[$j]['uni_no'],0,1);									#統一編號
				$worksheet1->write($i+1,$m++,$op[$j]['s_name']);										#對象簡稱
				$worksheet1->write($i+1,$m++,$op[$j]['inv_num']);										#發票號碼			
				$price = $op[$j]['inv_price']-$op[$j]['vat']+$op[$j]['discount']+$op[$j]['off_duty'];
				$worksheet1->write($i+1,$m++,$price);													#發票金額
				$worksheet1->write($i+1,$m++,$op[$j]['vat']);											#稅額
				$worksheet1->write($i+1,$m++,$op[$j]['discount']);										#折讓金
				$worksheet1->write($i+1,$m++,$op[$j]['off_duty']);										#折讓稅
				$worksheet1->write($i+1,$m++,( $SHIP == 'AEJ0000') ? "AHF0000" : "AHF1000" );			#部門代碼
				$worksheet1->write($i+1,$m++,$op[$j]['ord_dept']=='DA'?"貿易一部":"貿易二部");			#部門名稱
				$worksheet1->write($i+1,$m++,$op[$j]['ship']=='LY'?"立元製衣":"嘉菲廠");				#廠別名稱
				$worksheet1->write($i+1,$m++,str_replace("-","/",substr($op[$j]['rcv_date'],0,7)));		#處理年月
				
				$s_price += $price;
				$s_vat += $op[$j]['vat'];
				$s_discount += $op[$j]['discount'];
				$s_off_duty += $op[$j]['off_duty'];
				$i++;
			}
        }
		
		$g_price += $s_price;
		$g_vat += $s_vat;
		$g_discount += $s_discount;
		$g_off_duty += $s_off_duty;
		
		#主料 - 貿易一部 - 小計
		$m=0;
		$worksheet1->write($i+1,$m++,"小計",$f1);
		$worksheet1->write($i+1,$m++,"",$f1);
		$worksheet1->write($i+1,$m++,"",$f1);
		$worksheet1->write($i+1,$m++,"",$f1);
		$worksheet1->write($i+1,$m++,"",$f1);	
		$worksheet1->write($i+1,$m++,$s_price,$f1);		#發票金額
		$worksheet1->write($i+1,$m++,$s_vat,$f1);		#稅額
		$worksheet1->write($i+1,$m++,$s_discount,$f1);	#折讓金
		$worksheet1->write($i+1,$m++,$s_off_duty,$f1);	#折讓稅
		$worksheet1->write($i+1,$m++,"",$f1);
		$worksheet1->write($i+1,$m++,"",$f1);
		$worksheet1->write($i+1,$m++,"",$f1);
		$worksheet1->write($i+1,$m++,"",$f1);
		$i++;
		
		# 主料 - 貿易二部
		$s_price = $s_vat = $s_discount = $s_off_duty = 0; // 小計用
        for($j=0;$j<sizeof($op);$j++){
			if ( substr($op[$j]['mat_code'],0,1) == 'F' && $op[$j]['ord_dept'] != 'DA' ) {
				$m=0;
				$SHIP = $apb->get_apb_dept($op[$j]['ord_fty']);
				$worksheet1->write($i+1,$m++,substr($op[$j]['mat_code'],0,1)=='A'?"副料":"主料");		#借方名稱
				$worksheet1->write($i+1,$m++,$SHIP);													#廠別代碼
				$worksheet1->write($i+1,$m++,$op[$j]['uni_no'],0,1);									#統一編號
				$worksheet1->write($i+1,$m++,$op[$j]['s_name']);										#對象簡稱
				$worksheet1->write($i+1,$m++,$op[$j]['inv_num']);										#發票號碼			
				$price = $op[$j]['inv_price']-$op[$j]['vat']+$op[$j]['discount']+$op[$j]['off_duty'];
				$worksheet1->write($i+1,$m++,$price);													#發票金額
				$worksheet1->write($i+1,$m++,$op[$j]['vat']);											#稅額
				$worksheet1->write($i+1,$m++,$op[$j]['discount']);										#折讓金
				$worksheet1->write($i+1,$m++,$op[$j]['off_duty']);										#折讓稅
				$worksheet1->write($i+1,$m++,( $SHIP == 'AEJ0000') ? "AHF0000" : "AHF1000" );			#部門代碼
				$worksheet1->write($i+1,$m++,$op[$j]['ord_dept']=='DA'?"貿易一部":"貿易二部");			#部門名稱
				$worksheet1->write($i+1,$m++,$op[$j]['ship']=='LY'?"立元製衣":"嘉菲廠");				#廠別名稱
				$worksheet1->write($i+1,$m++,str_replace("-","/",substr($op[$j]['rcv_date'],0,7)));		#處理年月
				
				$s_price += $price;
				$s_vat += $op[$j]['vat'];
				$s_discount += $op[$j]['discount'];
				$s_off_duty += $op[$j]['off_duty'];
				$i++;
			}
        }
		
		
		$g_price += $s_price;
		$g_vat += $s_vat;
		$g_discount += $s_discount;
		$g_off_duty += $s_off_duty;
		
		$t_price += $g_price;
		$t_vat += $g_vat;
		$t_discount += $g_discount;
		$t_off_duty += $g_off_duty;
		
		#主料 - 貿易二部 - 小計
		$m=0;
		$worksheet1->write($i+1,$m++,"小計",$f1);
		$worksheet1->write($i+1,$m++,"",$f1);
		$worksheet1->write($i+1,$m++,"",$f1);
		$worksheet1->write($i+1,$m++,"",$f1);
		$worksheet1->write($i+1,$m++,"",$f1);	
		$worksheet1->write($i+1,$m++,$s_price,$f1);		#發票金額
		$worksheet1->write($i+1,$m++,$s_vat,$f1);		#稅額
		$worksheet1->write($i+1,$m++,$s_discount,$f1);	#折讓金
		$worksheet1->write($i+1,$m++,$s_off_duty,$f1);	#折讓稅
		$worksheet1->write($i+1,$m++,"",$f1);
		$worksheet1->write($i+1,$m++,"",$f1);
		$worksheet1->write($i+1,$m++,"",$f1);
		$worksheet1->write($i+1,$m++,"",$f1);
		$i++;
		
		#主料 - 貿易二部 - 合計
		$m=0;
		$worksheet1->write($i+1,$m++,"合計",$f2);
		$worksheet1->write($i+1,$m++,"",$f2);
		$worksheet1->write($i+1,$m++,"",$f2);
		$worksheet1->write($i+1,$m++,"",$f2);
		$worksheet1->write($i+1,$m++,"",$f2);	
		$worksheet1->write($i+1,$m++,$g_price,$f2);		#發票金額
		$worksheet1->write($i+1,$m++,$g_vat,$f2);		#稅額
		$worksheet1->write($i+1,$m++,$g_discount,$f2);	#折讓金
		$worksheet1->write($i+1,$m++,$g_off_duty,$f2);	#折讓稅
		$worksheet1->write($i+1,$m++,"",$f2);
		$worksheet1->write($i+1,$m++,"",$f2);
		$worksheet1->write($i+1,$m++,"",$f2);
		$worksheet1->write($i+1,$m++,"",$f2);
		$i++;
		
		
		# 副料 - 貿易一部
		$s_price = $s_vat = $s_discount = $s_off_duty = 0; // 小計用
		$g_price = $g_vat = $g_discount = $g_off_duty = 0; // 合計用
        for($j=0;$j<sizeof($op);$j++){
			if ( substr($op[$j]['mat_code'],0,1) == 'A' && $op[$j]['ord_dept'] == 'DA' ) {
				$m=0;
				$SHIP = $apb->get_apb_dept($op[$j]['ord_fty']);
				$worksheet1->write($i+1,$m++,substr($op[$j]['mat_code'],0,1)=='A'?"副料":"主料");		#借方名稱
				$worksheet1->write($i+1,$m++,$SHIP);													#廠別代碼
				$worksheet1->write($i+1,$m++,$op[$j]['uni_no'],0,1);									#統一編號
				$worksheet1->write($i+1,$m++,$op[$j]['s_name']);										#對象簡稱
				$worksheet1->write($i+1,$m++,$op[$j]['inv_num']);										#發票號碼			
				$price = $op[$j]['inv_price']-$op[$j]['vat']+$op[$j]['discount']+$op[$j]['off_duty'];
				$worksheet1->write($i+1,$m++,$price);													#發票金額
				$worksheet1->write($i+1,$m++,$op[$j]['vat']);											#稅額
				$worksheet1->write($i+1,$m++,$op[$j]['discount']);										#折讓金
				$worksheet1->write($i+1,$m++,$op[$j]['off_duty']);										#折讓稅
				$worksheet1->write($i+1,$m++,( $SHIP == 'AEJ0000') ? "AHF0000" : "AHF1000" );			#部門代碼
				$worksheet1->write($i+1,$m++,$op[$j]['ord_dept']=='DA'?"貿易一部":"貿易二部");			#部門名稱
				$worksheet1->write($i+1,$m++,$op[$j]['ship']=='LY'?"立元製衣":"嘉菲廠");				#廠別名稱
				$worksheet1->write($i+1,$m++,str_replace("-","/",substr($op[$j]['rcv_date'],0,7)));		#處理年月
				
				$s_price += $price;
				$s_vat += $op[$j]['vat'];
				$s_discount += $op[$j]['discount'];
				$s_off_duty += $op[$j]['off_duty'];
				$i++;
			}
        }
		
		$g_price += $s_price;
		$g_vat += $s_vat;
		$g_discount += $s_discount;
		$g_off_duty += $s_off_duty;
		
		#副料 - 貿易一部 - 小計
		$m=0;
		$worksheet1->write($i+1,$m++,"小計",$f1);
		$worksheet1->write($i+1,$m++,"",$f1);
		$worksheet1->write($i+1,$m++,"",$f1);
		$worksheet1->write($i+1,$m++,"",$f1);
		$worksheet1->write($i+1,$m++,"",$f1);	
		$worksheet1->write($i+1,$m++,$s_price,$f1);		#發票金額
		$worksheet1->write($i+1,$m++,$s_vat,$f1);		#稅額
		$worksheet1->write($i+1,$m++,$s_discount,$f1);	#折讓金
		$worksheet1->write($i+1,$m++,$s_off_duty,$f1);	#折讓稅
		$worksheet1->write($i+1,$m++,"",$f1);
		$worksheet1->write($i+1,$m++,"",$f1);
		$worksheet1->write($i+1,$m++,"",$f1);
		$worksheet1->write($i+1,$m++,"",$f1);
		$i++;
		
		# 副料 - 貿易二部
		$s_price = $s_vat = $s_discount = $s_off_duty = 0; // 小計用
        for($j=0;$j<sizeof($op);$j++){
			if ( substr($op[$j]['mat_code'],0,1) == 'A' && $op[$j]['ord_dept'] != 'DA' ) {
				$m=0;
				$SHIP = $apb->get_apb_dept($op[$j]['ord_fty']);
				$worksheet1->write($i+1,$m++,substr($op[$j]['mat_code'],0,1)=='A'?"副料":"主料");		#借方名稱
				$worksheet1->write($i+1,$m++,$SHIP);													#廠別代碼
				$worksheet1->write($i+1,$m++,$op[$j]['uni_no'],0,1);									#統一編號
				$worksheet1->write($i+1,$m++,$op[$j]['s_name']);										#對象簡稱
				$worksheet1->write($i+1,$m++,$op[$j]['inv_num']);										#發票號碼			
				$price = $op[$j]['inv_price']-$op[$j]['vat']+$op[$j]['discount']+$op[$j]['off_duty'];
				$worksheet1->write($i+1,$m++,$price);													#發票金額
				$worksheet1->write($i+1,$m++,$op[$j]['vat']);											#稅額
				$worksheet1->write($i+1,$m++,$op[$j]['discount']);										#折讓金
				$worksheet1->write($i+1,$m++,$op[$j]['off_duty']);										#折讓稅
				$worksheet1->write($i+1,$m++,( $SHIP == 'AEJ0000') ? "AHF0000" : "AHF1000" );			#部門代碼
				$worksheet1->write($i+1,$m++,$op[$j]['ord_dept']=='DA'?"貿易一部":"貿易二部");			#部門名稱
				$worksheet1->write($i+1,$m++,$op[$j]['ship']=='LY'?"立元製衣":"嘉菲廠");				#廠別名稱
				$worksheet1->write($i+1,$m++,str_replace("-","/",substr($op[$j]['rcv_date'],0,7)));		#處理年月
				
				$s_price += $price;
				$s_vat += $op[$j]['vat'];
				$s_discount += $op[$j]['discount'];
				$s_off_duty += $op[$j]['off_duty'];
				$i++;
			}
        }
		
		
		$g_price += $s_price;
		$g_vat += $s_vat;
		$g_discount += $s_discount;
		$g_off_duty += $s_off_duty;
		
		$t_price += $g_price;
		$t_vat += $g_vat;
		$t_discount += $g_discount;
		$t_off_duty += $g_off_duty;		
		
		#副料 - 貿易二部 - 小計
		$m=0;
		$worksheet1->write($i+1,$m++,"小計",$f1);
		$worksheet1->write($i+1,$m++,"",$f1);
		$worksheet1->write($i+1,$m++,"",$f1);
		$worksheet1->write($i+1,$m++,"",$f1);
		$worksheet1->write($i+1,$m++,"",$f1);	
		$worksheet1->write($i+1,$m++,$s_price,$f1);		#發票金額
		$worksheet1->write($i+1,$m++,$s_vat,$f1);		#稅額
		$worksheet1->write($i+1,$m++,$s_discount,$f1);	#折讓金
		$worksheet1->write($i+1,$m++,$s_off_duty,$f1);	#折讓稅
		$worksheet1->write($i+1,$m++,"",$f1);
		$worksheet1->write($i+1,$m++,"",$f1);
		$worksheet1->write($i+1,$m++,"",$f1);
		$worksheet1->write($i+1,$m++,"",$f1);
		$i++;
		
		#副料 - 貿易二部 - 合計
		$m=0;
		$worksheet1->write($i+1,$m++,"合計",$f2);
		$worksheet1->write($i+1,$m++,"",$f2);
		$worksheet1->write($i+1,$m++,"",$f2);
		$worksheet1->write($i+1,$m++,"",$f2);
		$worksheet1->write($i+1,$m++,"",$f2);	
		$worksheet1->write($i+1,$m++,$g_price,$f2);		#發票金額
		$worksheet1->write($i+1,$m++,$g_vat,$f2);		#稅額
		$worksheet1->write($i+1,$m++,$g_discount,$f2);	#折讓金
		$worksheet1->write($i+1,$m++,$g_off_duty,$f2);	#折讓稅
		$worksheet1->write($i+1,$m++,"",$f2);
		$worksheet1->write($i+1,$m++,"",$f2);
		$worksheet1->write($i+1,$m++,"",$f2);
		$worksheet1->write($i+1,$m++,"",$f2);
		$i++;
		
		
		# 總合計
		$m=0;
		$worksheet1->write($i+1,$m++,"總合計",$f4);
		$worksheet1->write($i+1,$m++,"",$f4);
		$worksheet1->write($i+1,$m++,"",$f4);
		$worksheet1->write($i+1,$m++,"",$f4);
		$worksheet1->write($i+1,$m++,"",$f4);	
		$worksheet1->write($i+1,$m++,$t_price,$f4);		#發票金額
		$worksheet1->write($i+1,$m++,$t_vat,$f4);		#稅額
		$worksheet1->write($i+1,$m++,$t_discount,$f4);	#折讓金
		$worksheet1->write($i+1,$m++,$t_off_duty,$f4);	#折讓稅
		$worksheet1->write($i+1,$m++,"",$f4);
		$worksheet1->write($i+1,$m++,"",$f4);
		$worksheet1->write($i+1,$m++,"",$f4);
		$worksheet1->write($i+1,$m++,"",$f4);
		$i++;
		

    } else { //無資料
        $worksheet1->write(5,0,'no data',$formatot);
    }
    $workbook->close();

} elseif( $groupby == 8 ) { // 國內 ( by 客人 )
    
    $q_str = "select distinct `rcv_num`,`inv_num`,`status` 
                from `apb` 
                where (`rcv_date` between '$PHP_begin_date' and '$PHP_end_date') AND `apb`.`inv_num` != '' group by `inv_num` ORDER BY `inv_num` ASC ";
    
    $e = $apb->get_rcv_num($q_str);
    $s_size = sizeof($e['status']);
    for($i=0;$i<$s_size;$i++){
        if ($e['status'][$i] % 2 == 1){
            echo "<script>history.go(-1);alert('查詢期間($PHP_begin_date ~ $PHP_end_date)有尚未Submit之驗收單！');</script>";
            exit;
        }
    }
    
    $op = array();
    $op = $apb->apb_tiptop_domestic($e,$PHP_begin_date,$PHP_end_date);

	# 重新排序
	$mo = array();
	$mos = array();
	foreach($op as $k => $v){
		$mo[$v['uni_no']][] = $v;
		$mos[$v['uni_no']] = $v['uni_no'];
	}

	asort($mos);

	$op = array();
	foreach($mos as $key){
		foreach($mo[$key] as $k => $v){
			$op[] = $v;
		}
	}
	
    require_once($config['root_dir']."/lib/spreadsheets/Worksheet.php");
    require_once($config['root_dir']."/lib/spreadsheets/Workbook.php");

    function HeaderingExcel($filename) {
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=$filename");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
        header("Pragma: public");
    }

    // HTTP headers
    HeaderingExcel('TIPTOP-Domestic -'.$PHP_begin_date.'-'.$PHP_end_date.'.xls');
 
    // Creating a workbook
    $workbook = new Workbook("-");

    // Creating the first worksheet
    $worksheet1 =& $workbook->add_worksheet('apb list');

    $now = $GLOBALS['THIS_TIME'];

    // 寫入 title

    //欄位名稱用
    $formatot =& $workbook->add_format();
    $formatot->set_size(10);
    $formatot->set_align('center');
    $formatot->set_color('white');
    $formatot->set_pattern();
    $formatot->set_fg_color('navy');
    
    //(給 稅額 用) 可刪掉	2011/12/19
    $formatot2 =& $workbook->add_format();
    $formatot2->set_size(10);
    $formatot2->set_align('center');
    $formatot2->set_color('white');
    $formatot2->set_pattern();
    $formatot2->set_fg_color('gray');
    
    //表頭用
    $title_style =& $workbook->add_format();
    $title_style->set_size(12);
    $title_style->set_align('center');
    $title_style->set_color('block');
    $title_style->set_pattern();
    $title_style->set_fg_color('white');
    
    //小計數量用
    $f1 =& $workbook->add_format();
    $f1->set_size(10);
    $f1->set_align('right');
    $f1->set_color('block');
    $f1->set_pattern();
    $f1->set_fg_color('yellow');
  
    //主副料小計用
    $f2 =& $workbook->add_format();
    $f2->set_size(10);
    $f2->set_align('right');
    $f2->set_color('block');
    $f2->set_pattern();
    $f2->set_fg_color('orange');
    
    //國內外小計用
    $f3 =& $workbook->add_format();
    $f3->set_size(10);
    $f3->set_align('right');
    $f3->set_color('white');
    $f3->set_pattern();
    $f3->set_fg_color('navy');
    
    //總合計用
    $f4 =& $workbook->add_format();
    $f4->set_size(10);
    $f4->set_align('right');
    $f4->set_color('white');
    $f4->set_pattern();
    $f4->set_fg_color('red');
    
    $ary_size = array(15,9,9,12,9,8,7,7,9,9,9,8,9);
    
    for ($i=0; $i<sizeof($ary_size); $i++){
        $worksheet1->set_column(0,$i,$ary_size[$i]);	  
    } 

    $ary = array("對象簡稱"	,"統一編號"	,"發票日期"	,"發票號碼"	,"發票金額"	,"稅額"	,"折讓金" ,"折讓稅"	,"部門名稱"	,"票期"	,"到期日" ,"借方名稱" ,"處理年月" );
    for ($i=0; $i<sizeof($ary); $i++) $worksheet1->write_string(0,$i,$ary[$i],$formatot);	

    // Format for the numbers
    $formatnum =& $workbook->add_format();
    $formatnum->set_size(10);
    $formatnum->set_align('center');
    $formatnum->set_color('black');
    $formatnum->set_fg_color('B8D7D8');
    $data = array();
    
    $p=0;
    $y=substr($PHP_begin_date,0,4);
    $m=substr($PHP_begin_date,5,2);
    $maxday=getDaysInMonth($m,$y);
    
    if($op <> 0){
		$i=0;//列數用
		$t_price = $t_vat = $t_discount = $t_off_duty = 0; // 總合計用
		
		# 主料 - 貿易一部
		$s_price = $s_vat = $s_discount = $s_off_duty = 0; // 小計用
		$g_price = $g_vat = $g_discount = $g_off_duty = 0; // 合計用
		
		$uni_no = '';
        for($j=0;$j<sizeof($op);$j++){
		
			if( empty($uni_no) ) $uni_no = $op[$j]['uni_no'];
			

			if( $uni_no != $op[$j]['uni_no'] ) {
			
				# 小計
				$m=0;
				$worksheet1->write($i+1,$m++,"小計",$f1);
				$worksheet1->write($i+1,$m++,"",$f1);
				$worksheet1->write($i+1,$m++,"",$f1);
				$worksheet1->write($i+1,$m++,"",$f1);
				$worksheet1->write($i+1,$m++,$s_price,$f1);		#發票金額
				$worksheet1->write($i+1,$m++,$s_vat,$f1);		#稅額
				$worksheet1->write($i+1,$m++,$s_discount,$f1);	#折讓金
				$worksheet1->write($i+1,$m++,$s_off_duty,$f1);	#折讓稅
				$worksheet1->write($i+1,$m++,"",$f1);
				$worksheet1->write($i+1,$m++,"",$f1);
				$worksheet1->write($i+1,$m++,"",$f1);
				$worksheet1->write($i+1,$m++,"",$f1);
				$worksheet1->write($i+1,$m++,"",$f1);
				$i++;
				
		
				$g_price += $s_price;
				$g_vat += $s_vat;
				$g_discount += $s_discount;
				$g_off_duty += $s_off_duty;
				
				$s_price = $s_vat = $s_discount = $s_off_duty = 0; // 小計用
				
			}	
			
			
			$m=0;
			$SHIP = $apb->get_apb_dept($op[$j]['ord_fty']);
			$worksheet1->write($i+1,$m++,$op[$j]['s_name']);										#對象簡稱
			$worksheet1->write($i+1,$m++,$op[$j]['uni_no'],0,1);									#統一編號
			$worksheet1->write($i+1,$m++,$op[$j]['inv_date']);										#發票日期
			$worksheet1->write($i+1,$m++,$op[$j]['inv_num']);										#發票號碼				

			$price = $op[$j]['inv_price']-$op[$j]['vat']+$op[$j]['discount']+$op[$j]['off_duty'];
			$worksheet1->write($i+1,$m++,$price);													#發票金額
			$worksheet1->write($i+1,$m++,$op[$j]['vat']);											#稅額
			$worksheet1->write($i+1,$m++,$op[$j]['discount']);										#折讓金
			$worksheet1->write($i+1,$m++,$op[$j]['off_duty']);										#折讓稅
			$worksheet1->write($i+1,$m++,$op[$j]['ord_dept']=='DA'?"貿易一部":"貿易二部");			#部門名稱
			$worksheet1->write($i+1,$m++,is_numeric(substr($op[$j]['dm_way'],4,2))?substr($op[$j]['dm_way'],4,2):$op[$j]['dm_way']);	#票期
			$worksheet1->write($i+1,$m++,is_numeric(substr($op[$j]['dm_way'],4,2))?$apb->get_apb_dm_way(substr($op[$j]['dm_way'],4,2),substr($op[$j]['rcv_date'],0,7)):"");	#到期日
			$worksheet1->write($i+1,$m++,substr($op[$j]['mat_code'],0,1)=='A'?"副料":"主料");		#借方名稱
			$worksheet1->write($i+1,$m++,str_replace("-","/",substr($op[$j]['rcv_date'],0,7)));		#處理年月
			
			$s_price += $price;
			$s_vat += $op[$j]['vat'];
			$s_discount += $op[$j]['discount'];
			$s_off_duty += $op[$j]['off_duty'];
			
			$uni_no = $op[$j]['uni_no'];
			
			$i++;
        }

		# 小計
		$m=0;
		$worksheet1->write($i+1,$m++,"小計",$f1);
		$worksheet1->write($i+1,$m++,"",$f1);
		$worksheet1->write($i+1,$m++,"",$f1);
		$worksheet1->write($i+1,$m++,"",$f1);
		$worksheet1->write($i+1,$m++,$s_price,$f1);		#發票金額
		$worksheet1->write($i+1,$m++,$s_vat,$f1);		#稅額
		$worksheet1->write($i+1,$m++,$s_discount,$f1);	#折讓金
		$worksheet1->write($i+1,$m++,$s_off_duty,$f1);	#折讓稅
		$worksheet1->write($i+1,$m++,"",$f1);
		$worksheet1->write($i+1,$m++,"",$f1);
		$worksheet1->write($i+1,$m++,"",$f1);
		$worksheet1->write($i+1,$m++,"",$f1);
		$worksheet1->write($i+1,$m++,"",$f1);
		$g_price += $s_price;
		$g_vat += $s_vat;
		$g_discount += $s_discount;
		$g_off_duty += $s_off_duty;
		
		# 合計
		$i++;
		$m=0;
		$worksheet1->write($i+1,$m++,"合計",$f2);
		$worksheet1->write($i+1,$m++,"",$f2);
		$worksheet1->write($i+1,$m++,"",$f2);
		$worksheet1->write($i+1,$m++,"",$f2);
		$worksheet1->write($i+1,$m++,$g_price,$f2);		#發票金額
		$worksheet1->write($i+1,$m++,$g_vat,$f2);		#稅額
		$worksheet1->write($i+1,$m++,$g_discount,$f2);	#折讓金
		$worksheet1->write($i+1,$m++,$g_off_duty,$f2);	#折讓稅
		$worksheet1->write($i+1,$m++,"",$f2);		
		$worksheet1->write($i+1,$m++,"",$f2);
		$worksheet1->write($i+1,$m++,"",$f2);
		$worksheet1->write($i+1,$m++,"",$f2);
		$worksheet1->write($i+1,$m++,"",$f2);
		
    } else { //無資料
        $worksheet1->write(5,0,'no data',$formatot);
    }
    $workbook->close();

} elseif( $groupby == 6 ) { // 國外 TIPTOP 匯出資料 Foreign
    
    $q_str = "select distinct `rcv_num`,`inv_num`,`status`
                from `apb` 
                where (`rcv_date` between '$PHP_begin_date' and '$PHP_end_date') AND `apb`.`inv_num` = '' ORDER BY `rcv_date` ASC";
    
    $e = $apb->get_rcv_num($q_str);
    $s_size = sizeof($e['status']);
    for($i=0;$i<$s_size;$i++){
        if ($e['status'][$i] % 2 == 1){
            echo "<script>history.go(-1);alert('查詢期間($PHP_begin_date ~ $PHP_end_date)有尚未Submit之驗收單！');</script>";
            exit;
        }
    }
    $op = array();
    // for($i=0;$i<sizeof($e['rcv_num']);$i++){
    $op = $apb->apb_tiptop_foreign($e);
    // }

    // print_r($op);
    // exit;
    
    require_once($config['root_dir']."/lib/spreadsheets/Worksheet.php");
    require_once($config['root_dir']."/lib/spreadsheets/Workbook.php");

    function HeaderingExcel($filename) {
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=$filename");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
        header("Pragma: public");
    }

    // HTTP headers
    HeaderingExcel('TIPTOP-Foreign-'.$PHP_begin_date.'-'.$PHP_end_date.'.xls');
 
    // Creating a workbook
    $workbook = new Workbook("-");

    // Creating the first worksheet
    $worksheet1 =& $workbook->add_worksheet('apb list');

    $now = $GLOBALS['THIS_TIME'];

    // 寫入 title

    //欄位名稱用
    $formatot =& $workbook->add_format();
    $formatot->set_size(10);
    $formatot->set_align('center');
    $formatot->set_color('white');
    $formatot->set_pattern();
    $formatot->set_fg_color('navy');
    
    //表頭用
    $title_style =& $workbook->add_format();
    $title_style->set_size(12);
    $title_style->set_align('center');
    $title_style->set_color('block');
    $title_style->set_pattern();
    $title_style->set_fg_color('white');
    
    //小計數量用
    $f1 =& $workbook->add_format();
    $f1->set_size(10);
    $f1->set_align('right');
    $f1->set_color('block');
    $f1->set_pattern();
    $f1->set_fg_color('yellow');
  
    //主副料小計用
    $f2 =& $workbook->add_format();
    $f2->set_size(10);
    $f2->set_align('right');
    $f2->set_color('block');
    $f2->set_pattern();
    $f2->set_fg_color('orange');
    
    //國內外小計用
    $f3 =& $workbook->add_format();
    $f3->set_size(10);
    $f3->set_align('right');
    $f3->set_color('white');
    $f3->set_pattern();
    $f3->set_fg_color('navy');
    
    //總合計用
    $f4 =& $workbook->add_format();
    $f4->set_size(10);
    $f4->set_align('right');
    $f4->set_color('white');
    $f4->set_pattern();
    $f4->set_fg_color('red');
    
    // $ary_size = array(12,9,9,10,10,10,8,8,4,6,8,4,10,8,4,6,10,8,10);
    $ary_size = array(6,10,9,11,10,10,4,6,5,8,8,8,8,4,4,6,14,10,10,10,14,14);
    
    for ($i=0; $i<sizeof($ary_size); $i++){
        $worksheet1->set_column(0,$i,$ary_size[$i]);	  
    } 
  

    //$worksheet1->write_string(0,1,"apb List");
    // $worksheet1->write(0,7,"TipTop 國內外匯出資料報表",$title_style);
    // $worksheet1->write(1,16,"報表期間：$PHP_begin_date ~ $PHP_end_date",$title_style);

    // $ary = array("發票號碼","部門別","廠別部門","發票日期","付款起算","供應商","驗收金額","營業稅","折讓","課稅別","扣抵代號","票期","格式代號","建檔日期","借方","貸方","銷售人統","處理年月","領款方式");
    // $ary = array("0"	 	,"1"				,"2"				,"3"				,"4"				,"5"				,"6"		,"7"		,"8"		,"9"				,"10"		,"11"	    ,"11"	  ,"11",		  ,"12"		,"13"		,"14"		,"15"		,"16"							,"17"				,"18"							,"19");
    $ary = array("單別"	,"帳款日期"	,"廠別編號"	,"發票號碼"	,"發票日期"	,"付款方式"	,"幣別"	,"匯率"	,"稅別"	,"請款人員"	,"部門"	,"帳款類別"	,"廠別"	,"項次"	,"品名"	,"數量"	,"原幣金額(未稅)"	,"原入庫號"	,"採購單號","訂單號碼", "未稅金額(原幣)"	,"未稅金額(本幣)");

    for ($i=0; $i<sizeof($ary); $i++) $worksheet1->write_string(0,$i,$ary[$i],$formatot);	

    // Format for the numbers
    $formatnum =& $workbook->add_format();
    $formatnum->set_size(10);
    $formatnum->set_align('center');
    $formatnum->set_color('black');
    $formatnum->set_fg_color('B8D7D8');
    $data = array();
    
    $p=0;
    $y=substr($PHP_begin_date,0,4);
    $m=substr($PHP_begin_date,5,2);
    $maxday=getDaysInMonth($m,$y);
    
    if($op <> 0){
        $i=0;//列數用
        for($j=0;$j<sizeof($op);$j++){
            $worksheet1->write($i+1,0,$op[$j]['inv_num']!=NULL?"CP11":"CP12");
            $worksheet1->write($i+1,1,str_replace("-","/",$op[$j]['rcv_date']));
            // $worksheet1->write($i+3,2,$op[$j]['sup_no']." ".$op[$j]['uni_no']);
            $worksheet1->write($i+1,2,$op[$j]['uni_no'],0,1);
            $worksheet1->write($i+1,3,$op[$j]['inv_num']);
            $worksheet1->write($i+1,4,str_replace("-","/",$op[$j]['inv_date']));
            $worksheet1->write($i+1,5,$apb->get_apb_pay($op[$j]['dm_way']));
            $worksheet1->write($i+1,6,$op[$j]['currency']);
            $rates = $rate->get_rate($op[$j]['currency'],$op[$j]['rcv_date']);
            $worksheet1->write($i+1,7,($rates==0)?"1":$rates);
            $worksheet1->write($i+1,8,$op[$j]['inv_num']!=NULL?"T325":"T000");
            $where_str = " WHERE login_id = '".$op[$j]['rcv_sub_user']."'";
            $rcv_dept_name=$user->get_fields('emp_id',$where_str);
            $worksheet1->write($i+1,9,$apb->get_apb_dept($rcv_dept_name[0]));						
            $worksheet1->write($i+1,10,"AHF0000");
            $worksheet1->write($i+1,11,substr($op[$j]['mat_code'],0,1)=='A'?"61B":"61A");
            $worksheet1->write($i+1,12,$apb->get_apb_dept($op[$j]['ship']));		
            $worksheet1->write($i+1,13,"1");
            // $worksheet1->write($i+1,14," ".$op[$j]['mat_name']);
            $worksheet1->write($i+1,14,substr($op[$j]['mat_code'],0,1)=='A'?"副料":"主料");
            $worksheet1->write($i+1,15,$op[$j]['sum_qty']);
            // $ds = ( $op[$j]['qty'] * $op[$j]['uprices'] );
            
            // $d = ( $op[$j]['qty'] * $op[$j]['uprice'] );
            $d = ( $op[$j]['inv_price'] - $op[$j]['vat'] );
            $d = round($d,0);
            $ds = $op[$j]['inv_num']!=NULL ? $d : round($op[$j]['sum_uprice'], 2) ;
            $worksheet1->write($i+1,16,$ds);
            $worksheet1->write($i+1,17,$op[$j]['rcv_num']);
            $worksheet1->write($i+1,18,$op[$j]['po_num']);
            $worksheet1->write($i+1,19,$op[$j]['ord_num']);
            $worksheet1->write($i+1,20,$ds);
            $worksheet1->write($i+1,21,$d);
            $i++;
        }
    } else { //無資料
        $worksheet1->write(5,0,'no data',$formatot);
    }
    $workbook->close();

} else if($groupby==4) { //國內匯出資料
    
    $q_str = "select distinct `inv_num`,`status`
                from `apb`
                where `inv_num` !='' and (rcv_date between '$PHP_begin_date' and '$PHP_end_date') and status in(3,4,5,6,7,8,9,10,11,12,15,16)";
    
    $e = $apb->get_rcv_num($q_str);
    $s_size = sizeof($e['status']);
    for($i=0;$i<$s_size;$i++){
        if ($e['status'][$i] % 2 == 1){
            echo "<script>history.go(-1);alert('查詢期間($PHP_begin_date ~ $PHP_end_date)有尚未Submit之驗收單！');</script>";
            exit;
        }
    }
    
    for($i=0;$i<sizeof($e['inv_num']);$i++){
        $op[$i] = $apb->apb_local($e['inv_num'][$i]);
    }
    
    require_once($config['root_dir']."/lib/spreadsheets/Worksheet.php");
    require_once($config['root_dir']."/lib/spreadsheets/Workbook.php");

    function HeaderingExcel($filename) {
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=$filename");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
        header("Pragma: public");
    }

    // HTTP headers
    HeaderingExcel('apb.xls');
 
    // Creating a workbook
    $workbook = new Workbook("-");

    // Creating the first worksheet
    $worksheet1 =& $workbook->add_worksheet('apb list');

    $now = $GLOBALS['THIS_TIME'];

    // 寫入 title

    //欄位名稱用
    $formatot =& $workbook->add_format();
    $formatot->set_size(10);
    $formatot->set_align('center');
    $formatot->set_color('white');
    $formatot->set_pattern();
    $formatot->set_fg_color('navy');
    
    //表頭用
    $title_style =& $workbook->add_format();
    $title_style->set_size(12);
    $title_style->set_align('center');
    $title_style->set_color('block');
    $title_style->set_pattern();
    $title_style->set_fg_color('white');
    
    //小計數量用
    $f1 =& $workbook->add_format();
    $f1->set_size(10);
    $f1->set_align('right');
    $f1->set_color('block');
    $f1->set_pattern();
    $f1->set_fg_color('yellow');
  
    //主副料小計用
    $f2 =& $workbook->add_format();
    $f2->set_size(10);
    $f2->set_align('right');
    $f2->set_color('block');
    $f2->set_pattern();
    $f2->set_fg_color('orange');
    
    //國內外小計用
    $f3 =& $workbook->add_format();
    $f3->set_size(10);
    $f3->set_align('right');
    $f3->set_color('white');
    $f3->set_pattern();
    $f3->set_fg_color('navy');
    
    //總合計用
    $f4 =& $workbook->add_format();
    $f4->set_size(10);
    $f4->set_align('right');
    $f4->set_color('white');
    $f4->set_pattern();
    $f4->set_fg_color('red');

    $ary_size = array(12,12,9,9,10,10,10,8,8,4,6,8,4,10,8,4,6,10,8,10);

    for ($i=0; $i<sizeof($ary_size); $i++){
        $worksheet1->set_column(0,$i,$ary_size[$i]);	  
    } 

    //$worksheet1->write_string(0,1,"apb List");
    $worksheet1->write(0,7,"國內匯出資料報表",$title_style);
    $worksheet1->write(1,16,"報表期間：$PHP_begin_date ~ $PHP_end_date",$title_style);

    $ary = array("發票號碼","驗收單號","部門別","廠別部門","發票日期","付款起算","供應商","驗收金額","營業稅","折讓","課稅別","扣抵代號","票期","格式代號","建檔日期","借方","貸方","銷售人統","處理年月","領款方式");

    for ($i=0; $i<sizeof($ary); $i++){
         $worksheet1->write_string(2,$i,$ary[$i],$formatot);
    }

    // Format for the numbers
    $formatnum =& $workbook->add_format();
    $formatnum->set_size(10);
    $formatnum->set_align('center');
    $formatnum->set_color('black');
    $formatnum->set_fg_color('B8D7D8');
    $data = array();

    $p=0;
    $y=substr($PHP_begin_date,0,4);
    $m=substr($PHP_begin_date,5,2);
    $maxday=getDaysInMonth($m,$y);

    if($op <> 0){
        $i=0;//列數用
        for($j=0;$j<sizeof($op);$j++){
            $worksheet1->write($i+3,0,$op[$j]['apb']['inv_num']);
			$worksheet1->write($i+3,1,$op[$j]['apb']['rcv_num']);
            $worksheet1->write($i+3,2,$apb->get_apb_dept($op[$j]['apb']['ord_dept']));
            $where_str = " WHERE login_id = '".$op[$j]['apb']['receive_user']."'";
            $rcv_dept_name=$user->get_fields('dept',$where_str);

            //$worksheet1->write($i+3,2,$apb->get_apb_dept($rcv_dept_name[0]));
            $worksheet1->write($i+3,3,$apb->get_apb_dept($op[$j]['apb']['ord_fty']));
            $worksheet1->write($i+3,4,str_replace("-","/",$op[$j]['apb']['inv_date']));
            $worksheet1->write($i+3,5,$y."/".$m."/".$maxday);
            $worksheet1->write($i+3,6,$op[$j]['apb']['uni_no'],0,1);
            $worksheet1->write($i+3,7,$op[$j]['apb']['sum_inv_price']-$op[$j]['apb']['sum_vat']+$op[$j]['apb']['sum_discount']+$op[$j]['apb']['sum_off_duty']);
            $worksheet1->write($i+3,8,$op[$j]['apb']['sum_vat']);
            $worksheet1->write($i+3,9,$op[$j]['apb']['sum_discount']+$op[$j]['apb']['sum_off_duty']);
            $worksheet1->write($i+3,10,"1");
            $worksheet1->write($i+3,11,"1");
            $p = is_numeric(mb_substr($op[$j]['apb']['dm_way'],2,2,"utf-8"))?mb_substr($op[$j]['apb']['dm_way'],2,2,"utf-8"):"30";
            //$p = mb_substr($op[$j]['apb']['dm_way'],2,2,"utf-8");
            $worksheet1->write($i+3,12,$p);
            $worksheet1->write($i+3,13,"21");
            $worksheet1->write($i+3,14,str_replace("-","/",$op[$j]['apb']['rcv_date']));
            $worksheet1->write($i+3,15,substr($op[$j]['apb']['mat_code'],0,1)=='A'?"1217":"1215");
            $worksheet1->write($i+3,16,"214401");
            $worksheet1->write($i+3,17,$op[$j]['apb']['uni_no'],0,1);
            $worksheet1->write($i+3,18,substr($op[$j]['apb']['rcv_date'],0,4).substr($op[$j]['apb']['rcv_date'],5,2));
            $worksheet1->write($i+3,19,"1");
            $i++;
        }

    }else{ //無資料
        $worksheet1->write(5,0,'no data',$formatot);
    }

    $workbook->close();
} elseif ( $groupby==3 ){ //國外

    $q_str = "select distinct rcv_num,status,inv_num
                from apb
                where inv_num = '' and (rcv_date between '$PHP_begin_date' and '$PHP_end_date') and status in(3,4,5,6,7,8,9,10,11,12,15,16)";
    
    $e = $apb->get_rcv_num($q_str);
    $s_size = sizeof($e['status']);
    for($i=0;$i<$s_size;$i++){
        if ($e['status'][$i] == 0 or $e['status'][$i] == 3){
            echo "<script>history.go(-1);alert('查詢期間($PHP_begin_date ~ $PHP_end_date)有尚未Submit之驗收單！');</script>";
            exit;
        }
    }

    for($i=0;$i<sizeof($e['rcv_num']);$i++){
        $row[$i] = $apb->get_apb2($e['rcv_num'][$i]);
    }
	
	for($i=0;$i<sizeof($e['rcv_num']);$i++){
        $apb_oth_cost['apb_oth_cost'][$i] = $apb->get_apb_oth_cost($e['rcv_num'][$i]);
		$apb_oth_cost['ap_oth_cost'][$i] = $apb->get_ap_oth_cost($e['rcv_num'][$i]);
    }

    require_once($config['root_dir']."/lib/spreadsheets/Worksheet.php");
    require_once($config['root_dir']."/lib/spreadsheets/Workbook.php");

    function HeaderingExcel($filename) {
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=$filename");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
        header("Pragma: public");
    }

    // HTTP headers
    HeaderingExcel('apb.xls');
 
    // Creating a workbook
    $workbook = new Workbook("-");

    // Creating the first worksheet
    $worksheet1 =& $workbook->add_worksheet('apb list');

    $now = $GLOBALS['THIS_TIME'];

    // 寫入 title

    //欄位名稱用
    $formatot =& $workbook->add_format();
    $formatot->set_size(10);
    $formatot->set_align('center');
    $formatot->set_color('white');
    $formatot->set_pattern();
    $formatot->set_fg_color('navy');
    
    //表頭用
    $title_style =& $workbook->add_format();
    $title_style->set_size(12);
    $title_style->set_align('center');
    $title_style->set_color('block');
    $title_style->set_pattern();
    $title_style->set_fg_color('white');
    
    //小計數量用
    $f1 =& $workbook->add_format();
    $f1->set_size(10);
    $f1->set_align('right');
    $f1->set_color('block');
    $f1->set_pattern();
    $f1->set_fg_color('yellow');
  
    //主副料小計用
    $f2 =& $workbook->add_format();
    $f2->set_size(10);
    $f2->set_align('right');
    $f2->set_color('block');
    $f2->set_pattern();
    $f2->set_fg_color('orange');
    
    //國內外小計用
    $f3 =& $workbook->add_format();
    $f3->set_size(10);
    $f3->set_align('right');
    $f3->set_color('white');
    $f3->set_pattern();
    $f3->set_fg_color('navy');
    
    //總合計用
    $f4 =& $workbook->add_format();
    $f4->set_size(10);
    $f4->set_align('right');
    $f4->set_color('white');
    $f4->set_pattern();
    $f4->set_fg_color('red');
    
    $ary_size = array(4,10,10,12,20,10,10,8,10,8,20,5,10,5,8,5,8,10,10);
    
    for ($i=0; $i<sizeof($ary_size); $i++){
        $worksheet1->set_column(0,$i,$ary_size[$i]);	  
    } 
  

    //$worksheet1->write_string(0,1,"apb List");
    $worksheet1->write(0,7,"驗收報表",$title_style);
    $worksheet1->write(1,7,"驗收資料-by國外",$title_style);
    $worksheet1->write(1,16,"報表期間：$PHP_begin_date ~ $PHP_end_date",$title_style);

    $ary = array("廠別","主料(副料)","訂單號碼","料號","品名","驗收日","驗收單號","廠商代碼","統一編號","國內(外)","廠商名稱","單位","驗收數量","幣別","原幣單價","匯率","驗收單價","折讓","驗收金額","請購單號");

    for ($i=0; $i<sizeof($ary); $i++){
         $worksheet1->write_string(2,$i,$ary[$i],$formatot);
    }	
 

    // Format for the numbers
    $formatnum =& $workbook->add_format();
    $formatnum->set_size(10);
    $formatnum->set_align('center');
    $formatnum->set_color('black');
    $formatnum->set_fg_color('B8D7D8');
   
	#為了依訂單號碼做排序，把所有資料列放在同一陣列
    $data = array();
	$ord_ary = array();
    $m=0;
	$r_size = sizeof($row);
	for($i=0;$i<$r_size;$i++){
        $d_size = sizeof($row[$i]['apb_det']);
		$dc_amount = 0;
		for($j=0;$j<$d_size;$j++){
			$ord_ary[] = $row[$i]['apb_det'][$j]['ord_num'];
			if($row[$i]['apb_det'][$j]['currency'] <> "NTD"){
				$row[$i]['apb_det'][$j]['rate'] = $rate->get_rate($row[$i]['apb_det'][$j]['currency'], $row[$i]['apb_det'][$j]['inv_date'] <> '0000-00-00' ? $row[$i]['apb_det'][$j]['inv_date'] : $row[$i]['apb_det'][$j]['rcv_date']);
			}else{
				$row[$i]['apb_det'][$j]['rate'] = 1;
			}
			
			// $row[$i]['apb_det'][$j]['rate_uprice'] = number_format($row[$i]['apb_det'][$j]['uprices'] * $row[$i]['apb_det'][$j]['rate'],5,'','');;
			if($row[$i]['apb_det'][$j]['po_currency'] == "NTD" and $row[$i]['apb_det'][$j]['currency'] <> "NTD"){
				$row[$i]['apb_det'][$j]['rate_uprice'] = $row[$i]['apb_det'][$j]['uprice'];
			}else{
				$row[$i]['apb_det'][$j]['rate_uprice'] = number_format($row[$i]['apb_det'][$j]['uprices'] * $row[$i]['apb_det'][$j]['rate'],5,'','');;
			}
			
			$row[$i]['apb_det'][$j]['amount'] = number_format($row[$i]['apb_det'][$j]['link_qty'] * $row[$i]['apb_det'][$j]['rate_uprice'], 2, '', '');
			if($j < $d_size-1){
				if( $row[$i]['apb_det'][$j]['rcv_num'] == "AP14-0040"){
					// echo $row[$i]['apb_det'][$j]['amount']." / ".$row[$i]['apb_det'][$j]['inv_price']." * ".$row[$i]['apb_det'][$j]['apb_discount'];
					// exit;
				}
				$row[$i]['apb_det'][$j]['discount'] = number_format((float)($row[$i]['apb_det'][$j]['amount']/($row[$i]['apb_det'][$j]['inv_price']*$row[$i]['apb_det'][$j]['rate_uprice']))*$row[$i]['apb_det'][$j]['apb_discount'],0,'.','');
				$dc_amount += $row[$i]['apb_det'][$j]['discount'];
			}else{
				$row[$i]['apb_det'][$j]['discount'] = $row[$i]['apb_det'][$j]['apb_discount'] - $dc_amount;
			}
			
			$data[$m] = $row[$i]['apb_det'][$j];
			$m++;
        }
    }
	
	/* foreach($row as $key1 => $val1){
        foreach($val1 as $key2 => $val2){
            foreach($val2 as $key3 => $val3){
                $data[] = $val3;
                $ord_ary[] = $val3['ord_num'];
            }
        }
    } */

    array_multisort($ord_ary, SORT_ASC, $data);
    
    if(isset($data)){
        $total=0;
        $f_a = array("F"=>"主料","A"=>"副料");
        
        $i=0;
        foreach($f_a as $f_a_key => $f_a_val){
            $f_a_sum=$s_sum=0;
            foreach($data as $key => $val){
                if(substr($val['mat_code'],0,1) == $f_a_key){
                    if($f_a_key=="F")
                        $val['sname'] = $lots->get( '' , $val['mat_code']);
                    else
                        $val['sname'] = $acc->get( '', $val['mat_code']);
					
					$ord_rec = $apb->get_det_field("factory","s_order","order_num='".$val['ord_num']."'");
                    $worksheet1->write($i+3,0,$ord_rec['factory']);
                    $worksheet1->write($i+3,1,substr($val['mat_code'],0,1)=="F"?"主料":"副料");
                    $worksheet1->write($i+3,2,$val['ord_num']);
                    $worksheet1->write($i+3,3,$val['mat_code']);
                    $worksheet1->write($i+3,4,$val['sname'][2]);
                    $worksheet1->write($i+3,5,$val['rcv_date']);
                    $worksheet1->write($i+3,6,$val['rcv_num']);
                    $worksheet1->write($i+3,7,$val['sup_no']);
                    $worksheet1->write($i+3,8,$val['uni_no'],0,1);
                    $worksheet1->write($i+3,9,"國外");
                    $worksheet1->write($i+3,10,$val['s_name']);
                    $worksheet1->write($i+3,11,$val['po_unit']);
                    $worksheet1->write($i+3,12,$val['link_qty']);
                    $worksheet1->write($i+3,13,$val['currency']);
                    $worksheet1->write($i+3,14,$val['uprice']);
                    $worksheet1->write($i+3,15,$val['rate']);
					$worksheet1->write($i+3,16,$val['rate_uprice']);
					$worksheet1->write($i+3,17,$val['discount']);
					$price = $val['amount'] - $val['discount'];
					$worksheet1->write($i+3,18,$price);
					$worksheet1->write($i+3,19,str_replace("A","O",$val['ap_num']));
					
                    $f_a_sum += $price; //加總
                    $i++;
                }
            }
            $s_sum+=$f_a_sum;
            if($f_a_sum>0){ // "主料" 或 "副料" 的總和大於0才顯示小計欄位
                $worksheet1->write($i+3,17,$f_a_val."小計",$f1);
                $worksheet1->write($i+3,18,number_format($s_sum,0,'.',','),$f1);
                $i++;
            }
            $total+=$s_sum;
        }
        
		$data_size = sizeof($data);
		foreach($apb_oth_cost as $k1 => $v1){
			foreach($v1 as $k2 => $v2){
				foreach($v2 as $cost_k => $cost_v){
					$ord_ary = array();
					$ord_str = '';
					for($p=0;$p<$data_size;$p++){
						if($data[$p]['rcv_num']==$cost_v['apb_num']){
							$row_num = $p; //記錄要抓哪筆資料
							$ord_ary[]=$data[$p]['ord_num'];
							//break;
						}
					}
					$ord_ary = array_unique($ord_ary);
					foreach($ord_ary as $ord_val)
						$ord_str.=$ord_val."、";
					$ord_str = substr($ord_str,0,-1);
					$worksheet1->write($i+3,2,$ord_str);
					$worksheet1->write($i+3,5,$data[$row_num]['rcv_date']);
					$worksheet1->write($i+3,6,$data[$row_num]['rcv_num']);
					$worksheet1->write($i+3,7,$data[$row_num]['sup_no']);
					$worksheet1->write($i+3,8,$data[$row_num]['uni_no'],0,1);
					$worksheet1->write($i+3,9,"國外");
					$worksheet1->write($i+3,10,$data[$row_num]['s_name']);
					$worksheet1->write($i+3,13,$data[$row_num]['currency']);
					$worksheet1->write($i+3,15,$data[$row_num]['rate']);
					
					$cost = $cost_v['cost'] * $rate->get_rate($data[$row_num]['currency'],$data[$row_num]['inv_date'] <> '0000-00-00' ? $data[$row_num]['inv_date'] : $data[$row_num]['rcv_date']);
					$cost = number_format($cost,2,'','');
					$worksheet1->write($i+3,18,$cost);
					
					$i++;
				}
			}
		}
        if($s_sum>0){ // "國內" 或 "國外" 的總和大於0才顯示小計欄位
            $worksheet1->write($i+3,17,"國外小計",$f2);
            $worksheet1->write($i+3,18,number_format($total,0,'.',','),$f2);
            $i++;
        }
        
        $worksheet1->write($i+3,17,"驗收小計",$f3);
        $worksheet1->write($i+3,18,number_format($total,0,'.',','),$f3);
        $worksheet1->write($i+4,19,"列印日期：".$now,$title_style);		
        
    }else{ //無資料
        $worksheet1->write(5,0,'no data',$formatot);
    }
    $workbook->close();
} elseif ($groupby==2) { //國內
    $q_str = "select distinct rcv_num,status
                from apb
                where inv_num !='' and (rcv_date between '$PHP_begin_date' and '$PHP_end_date') and status in(3,4,5,6,7,8,9,10,11,12,15,16)";
    

    $e = $apb->get_rcv_num($q_str);
    $s_size = sizeof($e['status']);
    for($i=0;$i<$s_size;$i++){
        if ($e['status'][$i] % 2 == 1){
            echo "<script>history.go(-1);alert('查詢期間($PHP_begin_date ~ $PHP_end_date)有尚未Submit之驗收單！');</script>";
            exit;
        }
    }

    for($i=0;$i<sizeof($e['rcv_num']);$i++){
        $row[$i] = $apb->get_apb2($e['rcv_num'][$i]);
	}
	
	for($i=0;$i<sizeof($e['rcv_num']);$i++){
        $apb_oth_cost['apb_oth_cost'][$i] = $apb->get_apb_oth_cost($e['rcv_num'][$i]);
		$apb_oth_cost['ap_oth_cost'][$i] = $apb->get_ap_oth_cost($e['rcv_num'][$i]);
    }
	
	#分攤discount
	for($i=0;$i<sizeof($row);$i++){
		$dc_amount=0;
		for($j=0;$j<sizeof($row[$i]['apb_det'])-1;$j++){
			$row[$i]['apb_det'][$j]['discount'] = number_format((float)($row[$i]['apb_det'][$j]['amount']/$row[$i]['apb_det'][$j]['inv_price'])*$row[$i]['apb_det'][$j]['apb_discount'],0,'.','');
			$dc_amount += $row[$i]['apb_det'][$j]['discount'];
		}
		$row[$i]['apb_det'][$j]['discount'] = $row[$i]['apb_det'][$j]['apb_discount'] - $dc_amount;
	}
				
    require_once($config['root_dir']."/lib/spreadsheets/Worksheet.php");
    require_once($config['root_dir']."/lib/spreadsheets/Workbook.php");

    function HeaderingExcel($filename) {
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=$filename");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
        header("Pragma: public");
    }

    // HTTP headers
    HeaderingExcel('apb.xls');
 
    // Creating a workbook
    $workbook = new Workbook("-");

    // Creating the first worksheet
    $worksheet1 =& $workbook->add_worksheet('apb list');

    $now = $GLOBALS['THIS_TIME'];

    // 寫入 title

    //欄位名稱用
    $formatot =& $workbook->add_format();
    $formatot->set_size(10);
    $formatot->set_align('center');
    $formatot->set_color('white');
    $formatot->set_pattern();
    $formatot->set_fg_color('navy');
    
    //表頭用
    $title_style =& $workbook->add_format();
    $title_style->set_size(12);
    $title_style->set_align('center');
    $title_style->set_color('block');
    $title_style->set_pattern();
    $title_style->set_fg_color('white');
    
    //小計數量用
    $f1 =& $workbook->add_format();
    $f1->set_size(10);
    $f1->set_align('right');
    $f1->set_color('block');
    $f1->set_pattern();
    $f1->set_fg_color('yellow');
  
    //主副料小計用
    $f2 =& $workbook->add_format();
    $f2->set_size(10);
    $f2->set_align('right');
    $f2->set_color('block');
    $f2->set_pattern();
    $f2->set_fg_color('orange');
    
    //國內外小計用
    $f3 =& $workbook->add_format();
    $f3->set_size(10);
    $f3->set_align('right');
    $f3->set_color('white');
    $f3->set_pattern();
    $f3->set_fg_color('navy');
    
    //總合計用
    $f4 =& $workbook->add_format();
    $f4->set_size(10);
    $f4->set_align('right');
    $f4->set_color('white');
    $f4->set_pattern();
    $f4->set_fg_color('red');
    
    $ary_size = array(4,10,10,12,20,10,10,8,10,8,20,5,10,5,8,5,8,10,10,10);
    
    for ($i=0; $i<sizeof($ary_size); $i++){
        $worksheet1->set_column(0,$i,$ary_size[$i]);	  
    } 
  

    //$worksheet1->write_string(0,1,"apb List");
    $worksheet1->write(0,7,"驗收報表",$title_style);
    $worksheet1->write(1,7,"驗收資料-by國內",$title_style);
    $worksheet1->write(1,16,"報表期間：$PHP_begin_date ~ $PHP_end_date",$title_style);

    $ary = array("廠別","主料(副料)","訂單號碼","料號","品名","驗收日","驗收單號","廠商代碼","統一編號","國內(外)","廠商名稱","單位","驗收數量","幣別","原幣單價","匯率","驗收單價","折讓","驗收金額","請購單號");

    for ($i=0; $i<sizeof($ary); $i++){
         $worksheet1->write_string(2,$i,$ary[$i],$formatot);
    }	
 

    // Format for the numbers
    $formatnum =& $workbook->add_format();
    $formatnum->set_size(10);
    $formatnum->set_align('center');
    $formatnum->set_color('black');
    $formatnum->set_fg_color('B8D7D8');
    
    $data = array();
    $ord_ary = array();
    
    #為了依訂單號碼做排序，把所有資料列放在同一陣列
    $data = array();
    foreach($row as $key1 => $val1){
        foreach($val1 as $key2 => $val2){
            foreach($val2 as $key3 => $val3){
                $data[] = $val3;
                $ord_ary[] = $val3['ord_num'];
            }
        }
    }

    array_multisort($ord_ary, SORT_ASC, $data);
    
    if(isset($data)){
        $total=0;
        $f_a = array("F"=>"主料","A"=>"副料");
        
        $i=0;
        foreach($f_a as $f_a_key => $f_a_val){
            $f_a_sum=$s_sum=$dc_amount=0;
            foreach($data as $key => $val){
                if(substr($val['mat_code'],0,1) == $f_a_key){
                    if($f_a_key=="F")
                        $val['sname'] = $lots->get( '' , $val['mat_code']);
                    else
                        $val['sname'] = $acc->get( '', $val['mat_code']);
					
					$ord_rec = $apb->get_det_field("factory","s_order","order_num='".$val['ord_num']."'");
                    $worksheet1->write($i+3,0,$ord_rec['factory']);
					//$Abc=substr($val['mat_code'],0,1) ;
					//echo $Abc;
					//exit;
					if(substr($val['mat_code'],0,1)=="F")
					{
						$worksheet1->write($i+3,1,"主料");
					}
					else
					{
						$worksheet1->write($i+3,1,"副料");
					}
                    //$worksheet1->write($i+3,1,$val['mat_code']);
                    $worksheet1->write($i+3,2,$val['ord_num']);
                    $worksheet1->write($i+3,3,$val['mat_code']);
                    $worksheet1->write($i+3,4,$val['sname'][2]);
                    $worksheet1->write($i+3,5,$val['rcv_date']);
                    $worksheet1->write($i+3,6,$val['rcv_num']);
                    $worksheet1->write($i+3,7,$val['sup_no']);
                    $worksheet1->write($i+3,8,$val['uni_no'],0,1);
                    $worksheet1->write($i+3,9,"國內");
                    $worksheet1->write($i+3,10,$val['s_name']);
                    $worksheet1->write($i+3,11,$val['po_unit']);
                    $worksheet1->write($i+3,12,$val['link_qty']);
                    $worksheet1->write($i+3,13,$val['currency']);
                    $worksheet1->write($i+3,14,$val['uprices']);
                    $worksheet1->write($i+3,15,$val['rate']);
                    $worksheet1->write($i+3,16,$val['uprice']);
                    $worksheet1->write($i+3,17,$val['discount']);
                    $worksheet1->write($i+3,18,number_format($val['uprice']*$val['link_qty'],2,'','')-$val['discount']);
                    $worksheet1->write($i+3,19,str_replace("A","O",$val['ap_num']));
                    $f_a_sum+=number_format($val['uprice']*$val['link_qty'],2,'','')-$val['discount']; //加總
                    $i++;
                }
            }
            $s_sum+=$f_a_sum;
            if($f_a_sum>0){ // "主料" 或 "副料" 的總和大於0才顯示小計欄位
                $worksheet1->write($i+3,17,$f_a_val."小計",$f1);
                $worksheet1->write($i+3,18,number_format($s_sum,0,'.',','),$f1);
                $i++;
            }
            $total+=$s_sum;
        }
        
		$data_size = sizeof($data);
		foreach($apb_oth_cost as $k1 => $v1){
			foreach($v1 as $k2 => $v2){
				foreach($v2 as $cost_k => $cost_v){
					$ord_ary = array();
					$ord_str = '';
					for($p=0;$p<$data_size;$p++){
						if($data[$p]['rcv_num']==$cost_v['apb_num']){
							$row_num = $p; //記錄要抓哪筆資料
							$ord_ary[]=$data[$p]['ord_num'];
						}
					}
					$ord_ary = array_unique($ord_ary);
					foreach($ord_ary as $ord_val)
						$ord_str.=$ord_val."、";
					$ord_str = substr($ord_str,0,-1);
					$worksheet1->write($i+3,2,$ord_str);
					$worksheet1->write($i+3,5,$data[$row_num]['rcv_date']);
					$worksheet1->write($i+3,6,$data[$row_num]['rcv_num']);
					$worksheet1->write($i+3,7,$data[$row_num]['sup_no']);
					$worksheet1->write($i+3,8,$data[$row_num]['uni_no'],0,1);
					$worksheet1->write($i+3,9,"國內");
					$worksheet1->write($i+3,10,$data[$row_num]['s_name']);
					$worksheet1->write($i+3,13,$data[$row_num]['currency']);
					$worksheet1->write($i+3,18,$cost_v['cost']);
					$total+=$cost_v['cost'];
					$i++;
				}
			}
		}
        if($s_sum>0){ // "國內" 或 "國外" 的總和大於0才顯示小計欄位
            $worksheet1->write($i+3,17,"國內小計",$f2);
            $worksheet1->write($i+3,18,number_format($total,0,'.',','),$f2);
            $i++;
        }
        
        $worksheet1->write($i+3,17,"驗收小計",$f3);
        $worksheet1->write($i+3,18,number_format($total,0,'.',','),$f3);
        $worksheet1->write($i+4,18,"列印日期：".$now,$title_style);		
        
    }else{ //無資料
        $worksheet1->write(5,0,'no data',$formatot);
    }
    $workbook->close();
}else{//廠別
    $q_str = "select distinct rcv_num,status,inv_num
                from apb
                where (rcv_date between '$PHP_begin_date' and '$PHP_end_date') and (payment not like '%before%' or status > 2)";
    
    $e = $apb->get_rcv_num($q_str);
    $s_size = sizeof($e['status']);
    for($i=0;$i<$s_size;$i++){
        if ($e['status'][$i] == 0 or $e['status'][$i] == 3){
            echo "<script>history.go(-1);alert('查詢期間($PHP_begin_date ~ $PHP_end_date)有尚未Submit之驗收單！');</script>";
            exit;
        }
    }
	$rcv_size = sizeof($e['rcv_num']);
    for($i=0;$i<$rcv_size;$i++){
        $row[$i] = $apb->get_apb2($e['rcv_num'][$i]);
	}
    
	for($i=0;$i<sizeof($e['rcv_num']);$i++){
        $apb_oth_cost['apb_oth_cost'][$i] = $apb->get_apb_oth_cost($e['rcv_num'][$i]);
		$apb_oth_cost['ap_oth_cost'][$i] = $apb->get_ap_oth_cost($e['rcv_num'][$i]);
    }
	    
    require_once($config['root_dir']."/lib/spreadsheets/Worksheet.php");
    require_once($config['root_dir']."/lib/spreadsheets/Workbook.php");

    function HeaderingExcel($filename) {
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=$filename" );
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
        header("Pragma: public");
    }

    // HTTP headers
    HeaderingExcel('apb.xls');
 
    // Creating a workbook
    $workbook = new Workbook("-");

    // Creating the first worksheet
    $worksheet1 =& $workbook->add_worksheet('apb list');
    
    $now = $GLOBALS['THIS_TIME'];

    // 寫入 title

    // Format for the headings
    //欄位名稱用
    $formatot =& $workbook->add_format();
    $formatot->set_size(10);
    $formatot->set_align('center');
    $formatot->set_color('white');
    $formatot->set_pattern();
    $formatot->set_fg_color('navy');
    
    //表頭用
    $title_style =& $workbook->add_format();
    $title_style->set_size(12);
    $title_style->set_align('center');
    $title_style->set_color('block');
    $title_style->set_pattern();
    $title_style->set_fg_color('white');
    
    //小計數量用
    $f1 =& $workbook->add_format();
    $f1->set_size(10);
    $f1->set_align('right');
    $f1->set_color('block');
    $f1->set_pattern();
    $f1->set_fg_color('yellow');
  
    //主副料小計用
    $f2 =& $workbook->add_format();
    $f2->set_size(10);
    $f2->set_align('right');
    $f2->set_color('block');
    $f2->set_pattern();
    $f2->set_fg_color('orange');
    
    //工廠小計用
    $f3 =& $workbook->add_format();
    $f3->set_size(10);
    $f3->set_align('right');
    $f3->set_color('white');
    $f3->set_pattern();
    $f3->set_fg_color('navy');
    
    //總合計用
    $f4 =& $workbook->add_format();
    $f4->set_size(10);
    $f4->set_align('right');
    $f4->set_color('white');
    $f4->set_pattern();
    $f4->set_fg_color('red');
    
    $ary_size = array(4,10,10,12,20,10,10,8,10,8,20,5,10,5,8,5,8,10,10,10);
    
    for ($i=0; $i<sizeof($ary_size); $i++){
        $worksheet1->set_column(0,$i,$ary_size[$i]);	  
    } 
  

    //$worksheet1->write_string(0,1,"apb List");
    $worksheet1->write(0,6,"驗收報表",$title_style);
    $worksheet1->write(1,6,"驗收資料-by廠別",$title_style);
    $worksheet1->write(1,16,"報表期間：$PHP_begin_date ~ $PHP_end_date",$title_style);

    $ary = array("廠別","主料(副料)","訂單號碼","料號","品名","驗收日","驗收單號","廠商代碼","統一編號","國內(外)","廠商名稱","單位","驗收數量","幣別","原價單價","匯率","驗收單價","折讓","驗收金額","請購單號");

    for ($i=0; $i<sizeof($ary); $i++){
         $worksheet1->write_string(2,$i,$ary[$i],$formatot);
    }	
 
    // Format for the numbers
    $formatnum =& $workbook->add_format();
    $formatnum->set_size(10);
    $formatnum->set_align('center');
    $formatnum->set_color('black');
    $formatnum->set_fg_color('B8D7D8');
    $data = array();
    $ord_ary = array();
    
    
    if(sizeof($row) <> 0){
        $i=0;//列數用
            foreach($row as $key => $val){ //將所有資料放入$data中，後續處理用
					$count_ord=$dc_amount=$sum_rcv_qty=0;
                    for($d=0;$d<sizeof($val['apb_det']);$d++){//先合計驗收數量,分攤折讓用
                        for($e=0;$e<sizeof($val['apb_det']);$e++){
                            $sum_rcv_qty+=$val['apb_det'][$d]['ord_num'][$e]['po_link_qty'];
                            $count_ord+=1;
                        }
                    }
				$dc_amount=0;
                for($j=0;$j<sizeof($val['apb_det']);$j++){
                    
                    
                        $ord_ary[] = $val['apb_det'][$j]['ord_num'];
						$ord_rec = $apb->get_det_field("factory","s_order","order_num='".$val['apb_det'][$j]['ord_num']."'");
                        $data[$i]['ship'] = $ord_rec['factory'];
                        $data[$i]['f_a'] = substr($val['apb_det'][$j]['mat_code'],0,1)=="F"?"主料":"副料";
                        $data[$i]['ord'] = $val['apb_det'][$j]['ord_num'];
                        $data[$i]['mat_code'] = $val['apb_det'][$j]['mat_code'];
                            if (substr($val['apb_det'][$j]['mat_code'],0,1) =='A') {
                                $val['apb_det'][$j]['sname'] = $acc->get( '', $val['apb_det'][$j]['mat_code']);
                            } else {
                                $val['apb_det'][$j]['sname'] = $lots->get( '' , $val['apb_det'][$j]['mat_code']);
                            }
                        $data[$i]['sname'] = $val['apb_det'][$j]['sname'][2];
                        $data[$i]['rcv_date'] = $val['apb_det'][$j]['rcv_date'];
						$data[$i]['inv_date'] = $val['apb_det'][$j]['inv_date'];
                        $data[$i]['rcv_num'] = $val['apb_det'][$j]['rcv_num'];
                        $data[$i]['sup_no'] = '';
                        $data[$i]['uni_no'] = $val['apb_det'][$j]['uni_no'];
                        $data[$i]['local_foreign'] = $val['apb_det'][$j]['apb_inv_num']==""?"國外":"國內";
                        $data[$i]['s_name'] = $val['apb_det'][$j]['s_name'];
                        $data[$i]['unit'] = $val['apb_det'][$j]['po_unit'];
                        $data[$i]['qty'] = $val['apb_det'][$j]['link_qty'];
                        //$data[$i]['currency'] = $data[$i]['local_foreign']=="國內"?"NTD":$val['apb_det'][$j]['currency'];
						$data[$i]['currency'] = $val['apb_det'][$j]['currency'];
                        $data[$i]['uprice'] = $val['apb_det'][$j]['uprice'];
                        $data[$i]['rate'] = $data[$i]['currency']=="NTD"? 1 : $rate->get_rate($val['apb_det'][$j]['currency'],$val['apb_det'][$j]['inv_date'] <> '0000-00-00' ? $val['apb_det'][$j]['inv_date'] : $val['apb_det'][$j]['rcv_date']);
                        
						if($data[$i]['local_foreign']=="國內"){
							$data[$i]['uprice'] = $val['apb_det'][$j]['uprice'];
						}else{
							if($val['apb_det'][$j]['po_currency'] == "NTD" and $val['apb_det'][$j]['currency'] <> "NTD"){
								$data[$i]['uprice'] = $data[$i]['uprice'];
							}else{
								$data[$i]['uprice'] = number_format($val['apb_det'][$j]['uprice'] * $data[$i]['rate'],5,'','');
							}
						}
						 
						
						
						
						if($j<sizeof($val['apb_det'])-1){
							$data[$i]['discount'] = number_format((float)($val['apb_det'][$j]['amount']/$val['apb_det'][$j]['inv_price'])*$val['apb_det'][$j]['apb_discount'],0,'.','');
							$dc_amount += $data[$i]['discount'];
						}else{
							$data[$i]['discount'] = $val['apb_det'][$j]['apb_discount'] - $dc_amount;
						}
                        $data[$i]['rcv_cost'] = number_format($data[$i]['uprice']*$val['apb_det'][$j]['link_qty'],2,'.','');
                        $data[$i]['po_num'] = str_replace("PA","PO",$val['apb_det'][$j]['ap_num']);
                        
                        $i++;
                }
						
            }
            
            array_multisort($ord_ary, SORT_ASC, $data);

            $i=$kind=$total=0;
            //先將第一筆放入$result中，比對用
            $result[$kind][$i]['ship']=$data[$i]['ship'];
            $result[$kind][$i]['f_a']=$data[$i]['f_a'];
            $result[$kind][$i]['ord']=$data[$i]['ord'];
            $result[$kind][$i]['mat_code']=$data[$i]['mat_code'];
            $result[$kind][$i]['sname']=$data[$i]['sname'];
            $result[$kind][$i]['rcv_date']=$data[$i]['rcv_date'];
			$result[$kind][$i]['inv_date']=$data[$i]['inv_date'];
            $result[$kind][$i]['rcv_num']=$data[$i]['rcv_num'];
            $result[$kind][$i]['sup_no']=$data[$i]['sup_no'];
            $result[$kind][$i]['uni_no']=$data[$i]['uni_no'];
            $result[$kind][$i]['local_foreign']=$data[$i]['local_foreign'];
            $result[$kind][$i]['s_name']=$data[$i]['s_name'];
            $result[$kind][$i]['unit']=$data[$i]['unit'];
            $result[$kind][$i]['qty']=$data[$i]['qty'];
            $result[$kind][$i]['currency']=$data[$i]['currency'];
            $result[$kind][$i]['uprices']=$data[$i]['uprices'];
            $result[$kind][$i]['rate']=$data[$i]['rate'];
            $result[$kind][$i]['uprice']=$data[$i]['uprice'];
            $result[$kind][$i]['discount']=$data[$i]['discount'];
            $result[$kind][$i]['rcv_cost']=$data[$i]['rcv_cost'];
            $result[$kind][$i]['po_num']=$data[$i]['po_num'];

            $a=1;;
            for($x=1;$x<sizeof($data);$x++){ //第一筆在上面，所以從1開始
                for($y=0;$y<=sizeof($result);$y++){
                    $kind_result_rows = sizeof($result[$y]);
                    for($z=0;$z<$kind_result_rows;$z++){
                        if(($data[$x]['ship']==$result[$y][$z]['ship']) and ($data[$x]['f_a']==$result[$y][$z]['f_a']) and ($data[$x]['ord']==$result[$y][$z]['ord'])){
                            
                            $i=$kind_result_rows;
                            
                            //$worksheet1->write($tmp,0,"第".$a."筆yes加入result[".$y."][".$i."]..".$data[$x]['ship']."=".$result[$y][$z]['ship']." ".$data[$x]['f_a']."=".$result[$y][$z]['f_a']." ".$data[$x]['ord']."=".$result[$y][$z]['ord']);
                            //$tmp++;
                            $result[$y][$i]['ship']=$data[$x]['ship'];
                            $result[$y][$i]['f_a']=$data[$x]['f_a'];
                            $result[$y][$i]['ord']=$data[$x]['ord'];
                            $result[$y][$i]['mat_code']=$data[$x]['mat_code'];
                            $result[$y][$i]['sname']=$data[$x]['sname'];
                            $result[$y][$i]['rcv_date']=$data[$x]['rcv_date'];
							$result[$y][$i]['inv_date']=$data[$x]['inv_date'];
                            $result[$y][$i]['rcv_num']=$data[$x]['rcv_num'];
                            $result[$y][$i]['sup_no']=$data[$x]['sup_no'];
                            $result[$y][$i]['uni_no']=$data[$x]['uni_no'];
                            $result[$y][$i]['local_foreign']=$data[$x]['local_foreign'];
                            $result[$y][$i]['s_name']=$data[$x]['s_name'];
                            $result[$y][$i]['unit']=$data[$x]['unit'];
                            $result[$y][$i]['qty']=$data[$x]['qty'];
                            $result[$y][$i]['currency']=$data[$x]['currency'];
                            $result[$y][$i]['uprices']=$data[$x]['uprices'];
                            $result[$y][$i]['rate']=$data[$x]['rate'];
                            $result[$y][$i]['uprice']=$data[$x]['uprice'];
                            $result[$y][$i]['discount']=$data[$x]['discount'];
                            $result[$y][$i]['rcv_cost']=$data[$x]['rcv_cost'];
                            $result[$y][$i]['po_num']=$data[$x]['po_num'];
                            break 2;//找到相同廠別 主副料 訂單號碼時 跳離2個迴圈
                        }elseif($y==$kind and $z==($kind_result_rows-1)){ //沒找到時 新增另外一筆分類記錄
                            $kind=$y+1;
                            //$worksheet1->write($tmp,0,"第".$a."筆no加入result[".$kind."][0]..".$data[$x]['rcv_dept']."=".$result[$y][$z]['ship']." ".$data[$x]['f_a']."=".$result[$y][$z]['f_a']." ".$data[$x]['ord']."=".$result[$y][$z]['ord']);
                            //$tmp++;
                            $result[$kind][0]['ship']=$data[$x]['ship'];
                            $result[$kind][0]['f_a']=$data[$x]['f_a'];
                            $result[$kind][0]['ord']=$data[$x]['ord'];
                            $result[$kind][0]['mat_code']=$data[$x]['mat_code'];
                            $result[$kind][0]['sname']=$data[$x]['sname'];
                            $result[$kind][0]['rcv_date']=$data[$x]['rcv_date'];
							$result[$kind][0]['inv_date']=$data[$x]['inv_date'];
                            $result[$kind][0]['rcv_num']=$data[$x]['rcv_num'];
                            $result[$kind][0]['sup_no']=$data[$x]['sup_no'];
                            $result[$kind][0]['uni_no']=$data[$x]['uni_no'];
                            $result[$kind][0]['local_foreign']=$data[$x]['local_foreign'];
                            $result[$kind][0]['s_name']=$data[$x]['s_name'];
                            $result[$kind][0]['unit']=$data[$x]['unit'];
                            $result[$kind][0]['qty']=$data[$x]['qty'];
                            $result[$kind][0]['currency']=$data[$x]['currency'];
                            $result[$kind][0]['uprices']=$data[$x]['uprices'];
                            $result[$kind][0]['rate']=$data[$x]['rate'];
                            $result[$kind][0]['uprice']=$data[$x]['uprice'];
                            $result[$kind][0]['discount']=$data[$x]['discount'];
                            $result[$kind][0]['rcv_cost']=$data[$x]['rcv_cost'];
                            $result[$kind][0]['po_num']=$data[$x]['po_num'];
                            break 2;//沒找到時 跳離2個迴圈
                        }
                    }
                }
                $a++;
            }
                
                //print_r($result);
                
                //計算廠別數目
                ##############################################
                $fty_arr=array($result[0][0]['ship']);
                $mat_arr=array("主料","副料");
                for($m=0;$m<sizeof($result);$m++){
                    $hit=false;
                    for($n=0;$n<sizeof($fty_arr);$n++){
                        if($result[$m][0]['ship']==$fty_arr[$n]) $hit=true;
                    }
                    if ($hit==false) $fty_arr[]=$result[$m][0]['ship'];
                }
                //計算廠別結束

                $i=0;
                $s_sum=$mat_sum=$fty_sum=$total=0;
            
            //處理所有記錄 分別輸出
            for($m=0;$m<sizeof($fty_arr);$m++){
                $fty_sum=0;
                for($n=0;$n<sizeof($mat_arr);$n++){	
                    $mat_sum=0;
                    for($x=0;$x<sizeof($result);$x++){
                        $s_sum=0;
                        for($y=0;$y<sizeof($result[$x]);$y++){
                            if($result[$x][$y]['ship']==$fty_arr[$m] and $result[$x][$y]['f_a']==$mat_arr[$n]){//add
                                $worksheet1->write($i+3,0,$result[$x][$y]['ship']);
                                $worksheet1->write($i+3,1,$result[$x][$y]['f_a']);
                                $worksheet1->write($i+3,2,$result[$x][$y]['ord']);
                                $worksheet1->write($i+3,3,$result[$x][$y]['mat_code']);
                                $worksheet1->write($i+3,4,$result[$x][$y]['sname']);
                                $worksheet1->write($i+3,5,$result[$x][$y]['rcv_date']);
                                $worksheet1->write($i+3,6,$result[$x][$y]['rcv_num']);
                                $worksheet1->write($i+3,7,$result[$x][$y]['sup_no']);
                                $worksheet1->write($i+3,8,$result[$x][$y]['uni_no'],0,1);
                                $worksheet1->write($i+3,9,$result[$x][$y]['local_foreign']);
                                $worksheet1->write($i+3,10,$result[$x][$y]['s_name']);
                                $worksheet1->write($i+3,11,$result[$x][$y]['unit']);
                                $worksheet1->write($i+3,12,$result[$x][$y]['qty']);
                                $worksheet1->write($i+3,13,$result[$x][$y]['currency']);
                                $worksheet1->write($i+3,14,$result[$x][$y]['uprices']);
                                $worksheet1->write($i+3,15,$result[$x][$y]['rate']);
                                $worksheet1->write($i+3,16,$result[$x][$y]['uprice']);
                                $worksheet1->write($i+3,17,$result[$x][$y]['discount']);
                                $worksheet1->write($i+3,18,$result[$x][$y]['rcv_cost']-$result[$x][$y]['discount']);
                                $worksheet1->write($i+3,19,$result[$x][$y]['po_num']);
                                $s_sum+=$result[$x][$y]['rcv_cost']-$result[$x][$y]['discount'];
                                $i++;
                                if($y==(sizeof($result[$x])-1)){
                                    $worksheet1->write($i+3,17,"小計",$f1);
                                    $worksheet1->write($i+3,18,number_format($s_sum,0,'.',','),$f1);
                                    $i++;
                                    $mat_sum+=$s_sum;
                                }
                            }
                        }
                    }
                    if($mat_sum>0){
                        $worksheet1->write($i+3,17,$mat_arr[$n]."小計",$f2);
                        $worksheet1->write($i+3,18,number_format($mat_sum,0,'.',','),$f2);
                        $i++;
                    }
                    $fty_sum+=$mat_sum;
                }
                $worksheet1->write($i+3,17,$fty_arr[$m]."小計",$f3);
                $worksheet1->write($i+3,18,number_format($fty_sum,0,'.',','),$f3);
                $i++;
                $total+=$fty_sum;
            }
			
			$result_size = sizeof($result);
			foreach($apb_oth_cost as $k1 => $v1){
				foreach($v1 as $k2 => $v2){
					foreach($v2 as $cost_k => $cost_v){
						$ord_ary = array();
						$ord_str = '';
						
						for($p=0;$p<$result_size;$p++){	//直接比對資料
							$result_size2 = sizeof($result[$p]);
							$row_num = -1;
							for($q=0;$q<$result_size2;$q++){
								if($result[$p][$q]['rcv_num']==$cost_v['apb_num']){
									$row_num = $p; //記錄要抓哪筆資料
									$row_num2 = $q;
									$ord_ary[]=$result[$p][$q]['ord'];
									break 2;
								}
							}
						}
						
						if($row_num <> -1){
							$ord_ary = array_unique($ord_ary);
							foreach($ord_ary as $ord_val)
								$ord_str.=$ord_val."、";
							$ord_str = substr($ord_str,0,-1);
							$worksheet1->write($i+3,3,$ord_str);
							$worksheet1->write($i+3,5,$result[$row_num][$row_num2]['rcv_date']);
							$worksheet1->write($i+3,6,$result[$row_num][$row_num2]['rcv_num']);
							$worksheet1->write($i+3,7,$result[$row_num][$row_num2]['sup_no']);
							$worksheet1->write($i+3,8,$result[$row_num][$row_num2]['uni_no']);
							$worksheet1->write($i+3,9,$result[$row_num][$row_num2]['local_foreign']);
							$worksheet1->write($i+3,10,$result[$row_num][$row_num2]['s_name']);
							$worksheet1->write($i+3,13,$result[$row_num][$row_num2]['currency']);
							$worksheet1->write($i+3,15,$result[$row_num][$row_num2]['rate']);
							if($result[$row_num][$row_num2]['currency'] == "NTD")
								$cost = $cost_v['cost'];
							else
								$cost = $cost_v['cost'] * $rate->get_rate($result[$row_num][$row_num2]['currency'],$result[$row_num][$row_num2]['inv_date'] <> '0000-00-00' ? $result[$row_num][$row_num2]['inv_date'] : $result[$row_num][$row_num2]['rcv_date']);
							$cost = number_format($cost,2,'','');
							$worksheet1->write($i+3,18,number_format($cost,2,'',''));
							if($result[$row_num][$row_num2]['local_foreign'] == "國內")
								$total+=$cost;
							$i++;
						}else{
							$worksheet1->write($i+3,6,$cost_v['apb_num']);
							$apb_row = $apb->get_det_field("inv_num, inv_date, rcv_date","apb","rcv_num='".$cost_v['apb_num']."'");
							if($apb_row['inv_num']<>''){
								$worksheet1->write($i+3,5,$apb_row['rcv_date']);
								$worksheet1->write($i+3,9,"國內");
								$worksheet1->write($i+3,13,"NTD");
								$worksheet1->write($i+3,15,1);
								$worksheet1->write($i+3,18,$cost_v['cost']);
								$total += $cost_v['cost'];
							}
							$i++;
						}
					}
				}
			}
            $i++;
            $worksheet1->write($i+3,17,"驗收小計",$f4);
            $worksheet1->write($i+3,18,number_format($total,0,'.',','),$f4);
            $worksheet1->write($i+4,18,"列印日期：".$now,$title_style);
            
    }else{ //無資料
        $worksheet1->write(5,0,'no data',$title_style);
    }
    $workbook->close();
}
	
break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->case "sup_code_search"
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "sup_code_search":
check_authority($AUTH,"view");

if($PHP_payment == "T/T before shipment")
    $op = $apb->search_po($PHP_sup_no, $PHP_payment);
else
    $op = $apb->search_po($PHP_sup_no, "%|%");

$ap_size = sizeof($op['ap']);
for($i=1;$i<$ap_size;$i++){
	if($op['ap'][$i]['currency'] <> $op['ap'][$i-1]['currency']){
		$op['msg'][] = "以下 PO 幣別不同!!";
	}
}

$op['rcv_num'] = "AP".date('y')."-xxxx";
$op['date'] = $TODAY;
$op['dept'] = $GLOBALS['SCACHE']['ADMIN']['dept'];
$op['user'] = $GLOBALS['SCACHE']['ADMIN']['login_id'];

# 前面顯示
$op['payment'] = $payment;
# 傳遞值
$op['dm_way'] = $PHP_payment;

$op['supl'] = $supl->get('',$op['ap'][0]['sup_code']);
$op['back_str'] = "&SCH_num=".$SCH_num."&SCH_supl=".$SCH_supl."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc;
$op['back_str'] = "&PHP_po=".$PHP_po."&PHP_ship=".$PHP_ship."&PHP_ship2=".$PHP_ship2."&PHP_sup=".$PHP_sup."&PHP_payment=".$PHP_payment."&PHP_ord=".$PHP_ord."&PHP_b_a=".$PHP_b_a."&payment=".urlencode($PHP_payment);
$op['currency'] = $arry2->select($CURRENCY,$op['ap'][0]['currency'],'PHP_select_currency','select','change_currency()');	//幣別下拉式
//$op['msg'] = $apb->msg->get(2);

if(isset($PHP_msg)) $op['msg'][] = $PHP_msg;

page_display($op, $AUTH, $TPL_APB_PO_BEFORE_ADD);
break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->case "po_rcv_add"
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "po_rcv_add":
check_authority($AUTH,"view");

$op = $apb->search_po_inventory($PHP_sup_no);

$rcv_size = sizeof($op['rcv']);
$chk_cury = $op['rcv'][0]['rcv_det'][0]['currency'];
for($i=0;$i<$rcv_size;$i++){
	$det_size = sizeof($op['rcv'][$i]['rcv_det']);
	for($j=0;$j<$det_size;$j++){
		if($op['rcv'][$i]['rcv_det'][$j]['currency'] <> $chk_cury){
			$op['msg'][] = "以下 PO 幣別不同!!";
			break 2;
		}
	}
}

$op['rcv_num'] = "AP".date('y')."-xxxx";
$op['date'] = $TODAY;
$op['dept'] = $GLOBALS['SCACHE']['ADMIN']['dept'];
$op['user'] = $GLOBALS['SCACHE']['ADMIN']['login_id'];

# 前面顯示
$op['payment'] = $payment;
# 傳遞值
$op['dm_way'] = $PHP_payment;

$op['supl'] = $apb->get_det_field("vndr_no,supl_s_name,supl_f_name,uni_no","supl","vndr_no='".$PHP_sup_no."'");
$op['back_str'] = "&PHP_po=".$PHP_po."&PHP_ship=".$PHP_ship."&PHP_ship2=".$PHP_ship2."&PHP_sup=".$PHP_sup."&PHP_payment=".$PHP_payment."&PHP_ord=".$PHP_ord."&PHP_b_a=".$PHP_b_a."&payment=".urlencode($PHP_payment);
$op['currency'] = $arry2->select($CURRENCY,$op['rcv'][0]['rcv_det'][0]['currency'],'PHP_select_currency','select','change_currency()');	//幣別下拉式
//$op['msg'] = $apb->msg->get(2);

page_display($op, $AUTH, $TPL_PO_RCV_ADD);
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_po_before_add"
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_po_before_add":

check_authority($AUTH,"view");

$parm = array (
"sup_no"			=>	$PHP_sup_no,
"rcv_date"			=>	$TODAY,
"rcv_user"			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
"inv_num"			=>	$PHP_inv_num,
"foreign_inv_num"	=>	$PHP_foreign_inv_num,
"inv_date"			=>	$PHP_inv_date,
"inv_price"			=>	$PHP_inv_price,
"dept"				=>	$PHP_dept,
"discount"			=>	$PHP_discount,
"off_duty"			=>	$PHP_off_duty,
"vat"				=>	$PHP_vat,
"currency"			=>	$PHP_select_currency,
"status"			=>	1,
"payment"			=>	$PHP_payment <> "T/T before shipment" ? $PHP_payment="before|%" : $PHP_payment
);

$head="AP".date('y')."-";	//RP+日期=驗收單開頭
$parm['rcv_num'] = $apply->get_no($head,'rcv_num','apb');	//取得驗收的最後編號

$f1 = $apb->add($parm);

	$link_row = array();
	foreach ($PHP_po_num as $po_num)
	{
		if(isset($PHP_checkbox[$po_num]))
		{
			foreach($PHP_qty[$po_num] as $i => $qty)
			{
				if($PHP_qty[$po_num][$i] > 0){
					$parm_det = array(
					"ship_num"		=>	'',
					"rcvd_num"		=>	'',
					"inv_num"		=>	'',
					"rcv_num"		=>	$parm['rcv_num'],
					"mat_code"		=>	$PHP_mat_code[$po_num][$i],
					"qty"			=>	$PHP_qty[$po_num][$i],
					"uprices"		=>	$PHP_uprices[$po_num][$i],
					"uprice"		=>	$PHP_uprice[$po_num][$i],
					"color"			=>	$PHP_color[$po_num][$i],
					"foc"			=>	$PHP_foc[$po_num][$i],
					"ap_num"		=>	$PHP_ap_num[$po_num],
					"mat_cat"		=>	$PHP_mat_cat[$po_num][$i],
					"mat_id"		=>	$PHP_mat_id[$po_num][$i],
					"size"			=>	$PHP_size[$po_num][$i]
					);

					$det_id = $apb->add_det($parm_det);
					
					#備註apb_rmk
					$where_str = "ap_num='".$PHP_ap_num[$po_num]."' and mat_id=".$PHP_mat_id[$po_num][$i]." and color='".$PHP_color[$po_num][$i]."' and size='".$PHP_size[$po_num][$i]."'";
					$apb->update_field('apb_rmk', '1', $where_str, "ap_det");
					
					$input_rate = $PHP_rate[$po_num][$i];
					
					#分攤數量
					$tmp_qty = 0;
					$ap_det_row = $apb->get_same_mat($PHP_ap_num[$po_num],$PHP_mat_id[$po_num][$i],$PHP_color[$po_num][$i],$PHP_size[$po_num][$i],"order by po_qty asc");
					
					$ttl_po_qty_row = $apb->get_det_field("sum(po_qty) as po_qty","ap_det",$where_str);
					$ttl_po_qty = $ttl_po_qty_row['po_qty'];
					$percent = number_format($PHP_qty[$po_num][$i] / $ttl_po_qty,5,'.','');
					
					$link_row = array();
					for ($j=0; $j< sizeof($ap_det_row)-1; $j++)
					{
						$link_row[$j]['mat_cat'] = $ap_det_row[$j]['mat_cat'];
						$link_row[$j]['po_id'] = $ap_det_row[$j]['id'];
						$link_row[$j]['rcv_id'] = $det_id;
						$link_row[$j]['qty'] = number_format($ap_det_row[$j]['po_qty'] * $percent,2,'.','');
						$tmp_qty += $link_row[$j]['qty'];
						$link_row[$j]['currency'] = $PHP_select_currency;
						$link_row[$j]['amount'] = number_format($PHP_uprice[$po_num][$i] * $link_row[$j]['qty'],2,'.','');
						$link_row[$j]['rate'] = $PHP_rate[$po_num][$i];
						$link_row[$j]['ord_num'] = $apb->get_ord_num($link_row[$j]['po_id'],$link_row[$j]['mat_cat']);
					}
						#最後一筆
						$link_row[$j]['mat_cat'] = $ap_det_row[$j]['mat_cat'];
						$link_row[$j]['po_id'] = $ap_det_row[$j]['id'];
						$link_row[$j]['rcv_id'] = $det_id;
						$link_row[$j]['qty'] = $PHP_qty[$po_num][$i] - $tmp_qty;
						$link_row[$j]['currency'] = $PHP_select_currency;
						$link_row[$j]['amount'] = number_format($PHP_uprice[$po_num][$i] * $link_row[$j]['qty'],2,'.','');
						$link_row[$j]['rate'] = $PHP_rate[$po_num][$i];
						$link_row[$j]['ord_num'] = $apb->get_ord_num($link_row[$j]['po_id'],$link_row[$j]['mat_cat']);
					
					#寫入apb_po_link
					for ($j=0; $j< sizeof($link_row); $j++)
					{
						$f3 = $apb->add_link($link_row[$j]);
					}
				}
			}
			#記錄 PO 已付金額
			$i_parm = array("ap_num"	=>	$PHP_ap_num[$po_num],
						  "item"	=>	"",
						  "cost"	=>	$PHP_price[$po_num],
						  "apb_num"	=>	$parm['rcv_num'],
						  "payment_status"	=>	"2"
						  );
			$f1 = $apb->oth_cost_add($i_parm);
			#判斷 ap_det 是否都已付款,是的話就將 ap 備註
			$f1 = $apb->check_full_det_rmk($PHP_ap_num[$po_num]);
			
			# 標註ap_oth_cost
			foreach($ap_oth_cost_id[$po_num] as $key=>$val){
				$f1 = $apb->update_field("apb_num", $parm['rcv_num'], "id=".$val, 'ap_oth_cost');
			}
		}
	}

# 加入apb的其他費用(原採購單可能沒有加入的費用)
foreach ($PHP_des as $po_num=>$val)
{
	if($val){
		$i_parm = array("ap_num"	=>	$PHP_ap_num[$po_num],
						  "item"	=>	$val,
						  "cost"	=>	$PHP_cost[$po_num],
						  "apb_num"	=>	$parm['rcv_num'],
						  "payment_status"	=>	2
						  );
		$f1 = $apb->oth_cost_add($i_parm);
	}
}



$message = "successfully append Apb On : ".$parm['rcv_num'];
$op['msg'][]= $message;
$log->log_add(0,"040A",$message);

redirect_page($PHP_SELF."?PHP_action=po_before_view&PHP_rcv_num=".$parm['rcv_num']."&SCH_num=&SCH_supl=&SCH_fab=&SCH_acc=&PHP_sr_startno=1");

break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_before_view: T/T before shipment
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "po_before_view":
check_authority($AUTH,"view");

$op = $apb->get_po_before($PHP_rcv_num);

if($op['apb']['payment']=='before|%'){
	$op['apb']['payment2'] = $op['ap'][0]['dm_way'];
}else

if (isset($PHP_msg))$op['msg'][]=$PHP_msg;
if(isset($PHP_sr_startno))
	$op['back_str'] = "&PHP_payment=".$op['apb']['payment']."&SCH_num=".$SCH_num."&SCH_supl=".$SCH_supl."&SCH_po=".$SCH_po."&SCH_ship=".$SCH_ship."&PHP_sr_startno=".$PHP_sr_startno;
$op['ck'] = (!empty($_GET['ck']))? $_GET['ck'] : '';

$op['date'] = $TODAY;
$op['submit_ckdate'] = substr($op['apb']['rcv_date'],0,7) < substr($TODAY,0,7) ? '0' : '1' ;
$op['revise_ckdate'] = $op['apb']['rcv_date'] == $TODAY ? '1' : '0' ;
if(!$op['revise_ckdate'] and $op['apb']['status'] == 2){
	$op['msg'][] = "APB僅當天可Revise!";
}

if(!empty($PHP_resize)) $op['resize'] = 1;

page_display($op, $AUTH, $TPL_APB_PO_BEFORE_SHOW);
break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_before_edit: T/T before shipment
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "po_before_edit":
check_authority($AUTH,"view");

$op = $apb->get_po_before($PHP_rcv_num);

if (isset($PHP_msg))$op['msg'][]=$PHP_msg;
if(isset($PHP_sr_startno))$op['back_str'] = "&SCH_num=".$SCH_num."&PHP_sr_startno=".$PHP_sr_startno;
$op['ck'] = (!empty($_GET['ck']))? $_GET['ck'] : '';
$op['date'] = $TODAY;
$op['ckdate'] = substr($op['apb']['rcv_date'],0,7) < substr($TODAY,0,7) ? '1' : '0' ;
$op['currency'] =  $arry2->select($CURRENCY,$op['apb']['currency'],'PHP_select_currency','select','change_currency()');

page_display($op, $AUTH, $TPL_APB_PO_BEFORE_EDIT);
break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_po_before_edit:
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_po_before_edit":
check_authority($AUTH,"view");

	foreach($PHP_ap_num as $po_num=>$ap_num){
		if (!array_key_exists($po_num, $PHP_checkbox)){
			# 刪除 apb_oth_cost 中的記錄
			$f1 = $apb->del_apb_cost("apb_num='".$PHP_rcv_num."' and ap_num='".$ap_num."'", "apb_oth_cost");
			# 將 ap_oth_cost 裡 ap_num 的 apb_num 清空
			foreach($ap_oth_cost_id[$po_num] as $ai){
				$f1 = $apb->update_field("apb_num", "", "id=$ai", "ap_oth_cost");
			}
			
			foreach($PHP_det_id[$po_num] as $key=>$det_id){
				# 刪除 apb_po_link 中的記錄
				$f1 = $apb->del_link($det_id);
				# 刪除 apb_det 中的記錄
				$f1 = $apb->del_det($det_id);
			}
			# 修改 ap_det 中的 apb_rmk
			$f1 = $apb->update_field("apb_rmk", 0, "ap_num = '".$ap_num."'", "ap_det");
			$f1 = $apb->chk_del_full($PHP_rcv_num);
		}else{	//修改
			foreach($PHP_qty[$po_num] as $key=>$apb_qty){
				if($apb_qty > 0){
					#修改 apb_det 資料
					$f1 = $apb->update_field("qty", $apb_qty, "rcv_num='".$PHP_rcv_num."' and ap_num='".$PHP_ap_num[$po_num]."' and mat_id=".$PHP_mat_id[$po_num][$key]." and color='".$PHP_color[$po_num][$key]."' and size='".$PHP_size[$po_num][$key]."'", "apb_det");
					$f1 = $apb->update_field("foc", $PHP_foc[$po_num][$key], "rcv_num='".$PHP_rcv_num."' and ap_num='".$PHP_ap_num[$po_num]."' and mat_id=".$PHP_mat_id[$po_num][$key]." and color='".$PHP_color[$po_num][$key]."' and size='".$PHP_size[$po_num][$key]."'", "apb_det");
					$f1 = $apb->update_field("uprice", $PHP_uprice[$po_num][$key], "rcv_num='".$PHP_rcv_num."' and ap_num='".$PHP_ap_num[$po_num]."' and mat_id=".$PHP_mat_id[$po_num][$key]." and color='".$PHP_color[$po_num][$key]."' and size='".$PHP_size[$po_num][$key]."'", "apb_det");
					$f1 = $apb->update_field("uprices", $PHP_uprices[$po_num][$key], "rcv_num='".$PHP_rcv_num."' and ap_num='".$PHP_ap_num[$po_num]."' and mat_id=".$PHP_mat_id[$po_num][$key]." and color='".$PHP_color[$po_num][$key]."' and size='".$PHP_size[$po_num][$key]."'", "apb_det");
					
					#判斷是否標註 apb_rmk
					$toler = explode("|",$PHP_toler[$po_num]);
					$apbed_qty = $apb->get_apbed_qty($PHP_ap_num[$po_num], $PHP_mat_id[$po_num][$key], $PHP_color[$po_num][$key], $PHP_size[$po_num][$key], "0,2", "");
					
					if( $apbed_qty * (1-$toler[1]/100) >= $PHP_po_qty[$po_num][$key] )
						$apb->update_field('apb_rmk', '1', "ap_num='".$PHP_ap_num[$po_num]."' and mat_id='".$PHP_mat_id[$po_num][$key]."' and color='".$PHP_color[$po_num][$key]."' and size='".$PHP_size[$po_num][$key]."'", "ap_det");
					else
						$apb->update_field('apb_rmk', '0', "ap_num='".$PHP_ap_num[$po_num]."' and mat_id='".$PHP_mat_id[$po_num][$key]."' and color='".$PHP_color[$po_num][$key]."' and size='".$PHP_size[$po_num][$key]."'", "ap_det");
					
					# 取po_qty 並分攤數量
					$po_row = $apb->get_same_mat($PHP_ap_num[$po_num],$PHP_mat_id[$po_num][$key],$PHP_color[$po_num][$key],$PHP_size[$po_num][$key],"order by po_qty asc");
					$po_row_size = sizeof($po_row);
					$ttl_po_qty = $sum_qty = 0;
					
					for($i=0;$i<$po_row_size;$i++){
						$ttl_po_qty += $po_row[$i]['po_qty'];
					}
					
					$percent = number_format($apb_qty / $ttl_po_qty,5,'.','');
					for($i=0;$i<$po_row_size-1;$i++){
						$po_row[$i]['link_qty'] = number_format($percent * $po_row[$i]['po_qty'],2,'.','');
						$sum_qty += $po_row[$i]['link_qty'];
					}
					$po_row[$i]['link_qty'] = $apb_qty - $sum_qty;
					
					# update apb_po_link
					for ($j=0;$j<$po_row_size;$j++){
						$f3 = $apb->update_field("qty", $po_row[$j]['link_qty'], "rcv_id=".$PHP_det_id[$po_num][$key]." and po_id=".$po_row[$j]['id'], "apb_po_link");
						$f3 = $apb->update_field("rate", $PHP_rate[$po_num][$key], "rcv_id=".$PHP_det_id[$po_num][$key]." and po_id=".$po_row[$j]['id'], "apb_po_link");
						$f3 = $apb->update_field("currency", $PHP_select_currency, "rcv_id=".$PHP_det_id[$po_num][$key]." and po_id=".$po_row[$j]['id'], "apb_po_link");
						$f3 = $apb->update_field("amount", $po_row[$j]['link_qty'] * $PHP_uprice[$po_num][$key], "rcv_id=".$PHP_det_id[$po_num][$key]." and po_id=".$po_row[$j]['id'], "apb_po_link");
					}
				}else{
					#刪除 apb_po_link 中的記錄
					$f1 = $apb->del_link($PHP_det_id[$po_num][$key]);
					#刪除 apb_det 中的記錄
					$f1 = $apb->del_det($PHP_det_id[$po_num][$key]);
					#修改 ap_det 中的 apb_rmk
					$f1 = $apb->update_field("apb_rmk", 0, "ap_num='".$PHP_ap_num[$po_num]."' and mat_id=".$PHP_mat_id[$po_num][$key]." and color='".$PHP_color[$po_num][$key]."' and size='".$PHP_size[$po_num][$key]."'", "ap_det");
				}
			}
			#修改PO已付款金額
			$f1 = $apb->update_field('cost', $PHP_price[$po_num], "ap_num='".$PHP_ap_num[$po_num]."' and apb_num='".$PHP_rcv_num."'", "apb_oth_cost");
			
			#判斷ap_oth_cost是否被取消
			foreach($ap_oth_cost_id[$po_num] as $ai_key=>$ai_val){
				if (!array_key_exists($ai_key, $ap_oth_cost)){
					$f1 = $apb->update_field("apb_num", "", "id=$ai_key", "ap_oth_cost");
				}
			}
		}
		$f1 = $apb->chk_det_rmk($ap_num, $PHP_special[$po_num]);
	}

$f1 = $apb->update_field_id('inv_num', $PHP_inv_num, $PHP_id, 'apb');
$f1 = $apb->update_field_id('foreign_inv_num', $PHP_foreign_inv_num, $PHP_id, 'apb');
$f1 = $apb->update_field_id('inv_date', $PHP_inv_date, $PHP_id, 'apb');
$f1 = $apb->update_field_id('currency', $PHP_select_currency, $PHP_id, 'apb');
$f1 = $apb->update_field_id('inv_price', $PHP_inv_price, $PHP_id, 'apb');
$f1 = $apb->update_field_id('vat', $PHP_vat, $PHP_id, 'apb');
$f1 = $apb->update_field_id('off_duty', $PHP_off_duty, $PHP_id, 'apb');
$f1 = $apb->update_field_id('discount', $PHP_discount, $PHP_id, 'apb');

foreach($PHP_item as $id => $val){
	$f1 = $apb->update_field_id('item', $val, $id, 'apb_oth_cost');
	$f1 = $apb->update_field_id('cost', $PHP_cost[$id], $id, 'apb_oth_cost');
}

$f2 = $apb->chk_del_full($PHP_rcv_num);
if($f2)
{
	$message = "Delete APB : ".$PHP_rcv_num;
	$log->log_add(0,"040D",$message);
	$message = "Delete APB [".$PHP_rcv_num."]";
	$redir_str = "apb.php?PHP_action=apb&PHP_msg=".$message;
	redirect_page($redir_str);
}

$op = $apb->get_po_before($PHP_rcv_num);
$op['back_str'] = $PHP_back_str;
$message = "Successfully Edit Apb : ".$PHP_rcv_num;
$log->log_add(0,"040E",$message);
$op['msg'][]=$message;

redirect_page($PHP_SELF."?PHP_action=po_before_view&PHP_rcv_num=".$PHP_rcv_num."&SCH_num=&SCH_supl=&SCH_fab=&SCH_acc=&PHP_sr_startno=1&PHP_msg=".$message);

break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "po_before_revise":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "po_before_revise":
check_authority($AUTH,"edit");

$f1 = $apb->update_field_id('rcv_sub_user','',$PHP_id,'apb');
$f1 = $apb->update_field_id('rcv_sub_date','',$PHP_id,'apb');
$f1 = $apb->update_field_id('status',1,$PHP_id,'apb');
$f1 = $apb->update_field_id('version',($PHP_ver+1),$PHP_id,'apb');

$msg = "請款單 ".$PHP_rcv_num." Revised.";
$log->log_add(0,"040V",$msg);

redirect_page($PHP_SELF."?PHP_action=po_before_edit&PHP_rcv_num=".$PHP_rcv_num."&PHP_msg=".$msg);

break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "adjust_edit:
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "adjust_edit":
	echo $apb->adjust_edit($PHP_adjust, $PHP_apb_num);
break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->case "po_before_apb_add"
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "po_before_apb_add":
check_authority($AUTH,"view");

$op = $apb->get_po_rcvd($PHP_sup_no, $PHP_payment);

$op['rcv_num'] = "AP".date('y')."-xxxx";
$op['date'] = $TODAY;
$op['dept'] = $GLOBALS['SCACHE']['ADMIN']['dept'];
$op['user'] = $GLOBALS['SCACHE']['ADMIN']['login_id'];

# 前面顯示
$op['payment'] = $payment;
# 傳遞值
$op['dm_way'] = $PHP_payment;

$op['supl'] = $supl->get('',$op['rcv'][0]['rcv_det'][0]['sup_no']);
$op['back_str'] = "&SCH_num=".$SCH_num."&SCH_supl=".$SCH_supl."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc;
$op['back_str'] = "&PHP_po=".$PHP_po."&PHP_ship=".$PHP_ship."&PHP_ship2=".$PHP_ship2."&PHP_sup=".$PHP_sup."&PHP_payment=".$PHP_payment."&PHP_ord=".$PHP_ord."&PHP_b_a=".$PHP_b_a."&payment=".urlencode($PHP_payment);
$op['currency'] = $arry2->select($CURRENCY,$op['rcv'][0]['rcv_det'][0]['po']['currency'],'PHP_select_currency','select','change_currency()');	//幣別下拉式
$op['msg'] = $apb->msg->get(2);

if(isset($PHP_msg)) $op['msg'][] = $PHP_msg;

page_display($op, $AUTH, $TPL_PO_BEFORE_APB_ADD);
break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_po_before_apb_add":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_po_before_apb_add":
check_authority($AUTH,"view");

$parm = array (
"sup_no"			=>	$PHP_sup_no,
"rcv_date"			=>	$TODAY,
"rcv_user"			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
"inv_num"			=>	$PHP_inv_num,
"foreign_inv_num"	=>	$PHP_foreign_inv_num,
"inv_date"			=>	$PHP_inv_date,
"inv_price"			=>	$PHP_inv_price,
"dept"				=>	$PHP_dept,
"discount"			=>	$PHP_discount,
"off_duty"			=>	$PHP_off_duty,
"vat"				=>	$PHP_vat,
"currency"			=>	$PHP_select_currency,
"payment"			=>	$PHP_payment
);

$head="AP".date('y')."-";	//RP+日期=驗收單開頭
$parm['rcv_num'] = $apply->get_no($head,'rcv_num','apb');	//取得驗收的最後編號

$f1 = $apb->add($parm);
if($f1) $apb->update_field("status", "3", "rcv_num='".$parm['rcv_num']."'", 'apb');
if (isset($PHP_rcv_num))
{
	foreach ($PHP_rcv_num as $rcv_value)
	{
		if(isset($PHP_checkbox[$rcv_value]))
		{
			foreach($PHP_ids[$rcv_value] as $i => $po_id)
			{
				if($PHP_rcv_qty[$rcv_value][$i] >= 0){
					$cury_str = "PHP_currency".$rcv_value.$i;	//組合變數名稱用
					$parm_det = array(
					"ship_num"		=>	$PHP_ship_num[$rcv_value],
					"rcvd_num"		=>	$rcv_value,
					"inv_num"		=>	$PHP_rcv_inv[$rcv_value][$i],
					"rcv_num"		=>	$parm['rcv_num'],
					"po_id"			=>	$po_id,
					"mat_code"		=>	$PHP_mat_code[$rcv_value][$i],
					"qty"			=>	$PHP_apb_qty[$rcv_value][$i],
					"uprices"		=>	$PHP_uprices[$rcv_value][$i],
					"uprice"		=>	$PHP_uprice[$rcv_value][$i],
					"color"			=>	$PHP_color[$rcv_value][$i],
					"foc"			=>	$PHP_foc[$rcv_value][$i],
					"ap_num"		=>	$PHP_ap_num[$rcv_value][$i]
					);

					$f1 = $apb->add_det($parm_det);
					$apb->update_field('apb_rmk', '1', "rcv_num='".$rcv_value."' and po_id='".$po_id."'", "receive_det");
					
					$input_rate = $PHP_rate[$rcv_value][$i];
					$$cury_str == "NTD" ? $u_p = $PHP_uprice[$rcv_value][$i] : $u_p = $PHP_uprices[$rcv_value][$i];
					$id = explode('|',$po_id);
					$s_total=0;
					$tmp=0;
					$PHP_special[$rcv_value][$i] == 2 ? $tbl="ap_special" : $tbl="ap_det";
					for ($j=0; $j< sizeof($id); $j++)		//取得每項的請購量,並加總
					{
						$row[$j]=$apb->get_det_field("po_qty",$tbl,"id=".$id[$j]);
						$s_total = $s_total + $row[$j]['po_qty'];
					}		

					for ($j=0; $j< (sizeof($id)-1); $j++)		//計算每項採購量(平分)
					{
						$rcv_qty[$j] = $PHP_apb_qty[$rcv_value][$i] * ($row[$j]['po_qty'] / $s_total);
						$rcv_qty[$j] = number_format($rcv_qty[$j],0,'.','');
						$tmp = $tmp + $rcv_qty[$j];
					}

					$tmp_qty =  $PHP_apb_qty[$rcv_value][$i] - $tmp;
					$rcv_qty[$j] = $tmp_qty;
					$rcv_qty[$j] = number_format($rcv_qty[$j],2,'.','');

					for ($j=0; $j< sizeof($id); $j++) //加入DB
					{
						$special = ( $PHP_special[$rcv_value][$i] == 2 ) ? 1 : 0 ;
						$ord_num = $po->get_ord_num($id[$j],$special,$PHP_mat_code[$rcv_value][$i]);		
						$f3=$apb->add_link($f1,$id[$j],$rcv_qty[$j],$input_rate,$u_p,$ord_num,$PHP_select_currency);
						
						# 重新寫入 apb_qty 至 ap ， 將來必須要拿掉，浪費資料庫
						$PHP_special[$rcv_value][$i] == 2 ? $table = 'ap_special' : $table = 'ap_det';
						$f2=$apb->update_apb_qty('apb_qty',$rcv_qty[$j], $id[$j],$table);	
					}

					# 超量備註
					if($PHP_reason[$i])
					{
						$parm= array(
						'rcv_num'		=>	$parm['rcv_num'],
						'item'			=>	'Overbalance.',
						'des'				=>	$PHP_reason[$i],
						'user'			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
						'k_date'		=>	$TODAY
						);
						$f4=$apb->add_log($parm);
					}
				}
			}
		}
	}
}

foreach($oth_cost_id as $id=>$cost){
	$apb->update_field("apb_num", $parm['rcv_num'], "id=".$id, "ap_oth_cost");
}

#加入新增的apb_oth_cost
if(isset($PHP_des)){
	foreach($PHP_des as $k1 => $v1){
		foreach($v1 as $k2 => $v2){
			$f0 = $apb->oth_cost_add(str_replace("PO","PA",$k1), $PHP_des[$k1][$k2] , $PHP_cost[$k1][$k2], $parm['rcv_num']);
		}
	}
}

$message = "successfully append Apb On : ".$parm['rcv_num'];
$op['msg'][]= $message;
$log->log_add(0,"040A",$message);

redirect_page($PHP_SELF."?PHP_action=po_before_apb_view&PHP_rcv_num=".$parm['rcv_num']."&SCH_num=&SCH_supl=&SCH_fab=&SCH_acc=&PHP_sr_startno=1");
break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_before_apb_view":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "po_before_apb_view":

check_authority($AUTH,"view");

$op = $apb->show_apb($PHP_rcv_num);

if (isset($PHP_msg))$op['msg'][]=$PHP_msg;
if(isset($PHP_sr_startno))$op['back_str'] = "&SCH_num=".$SCH_num."&SCH_supl=".$SCH_supl."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_sr_startno=".$PHP_sr_startno;
$op['ck'] = (!empty($_GET['ck']))? $_GET['ck'] : '';
$op['date'] = $TODAY;
// echo substr($op['apb']['rcv_date'],0,7).' = '.substr($TODAY,0,7);
$op['ckdate'] = substr($op['apb']['rcv_date'],0,7) < substr($TODAY,0,7) ? '1' : '0' ;
if(!empty($PHP_resize)) $op['resize'] = 1;
page_display($op, $AUTH, $TPL_PO_BEFORE_APB_SHOW);

break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "po_before_apb_edit": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "po_before_apb_edit":
check_authority($AUTH,"view");

$op = $apb->get_po_before_apb($PHP_rcv_num);

$op['date'] = $TODAY;
$op['back_str'] = "&SCH_num=".$SCH_num."&SCH_supl=".$SCH_supl."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_sr_startno=".$PHP_sr_startno;
$op['currency'] =  $arry2->select($CURRENCY,$op['apb']['currency'],'PHP_select_currency','select','change_currency()');	//幣別下拉式

$dates = $TODAY;
page_display($op, $AUTH, $TPL_PO_BEFORE_APB_EDIT);
break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "do_po_before_apb_edit": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_po_before_apb_edit":

check_authority($AUTH,"view");				
/* if($PHP_del_mk == 1)
{
	$message = "Delete Receive [".$PHP_rcv_num."]";
	$redir_str = "apb.php?PHP_action=rcvd_search".$PHP_back_str."&PHP_msg=".$message;
	redirect_page($redir_str);
} */

$parm = array (	
"rcv_date"			=>	$TODAY,
"rcv_user"			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
"inv_num"			=>	$PHP_inv_num,
"inv_date"			=>	$PHP_inv_date,
"foreign_inv_num"	=>	$PHP_foreign_inv_num,
"inv_price"			=>	$PHP_inv_price,
"discount"			=>	$PHP_discount,
"off_duty"			=>	$PHP_off_duty,
"vat"				=>	$PHP_vat,
"currency"			=>	$PHP_select_currency,
"rcv_num"			=>	$PHP_rcv_num
);

$f1 = $apb->edit($parm);

	foreach($PHP_det_id as $rcv_num => $val){
		if (array_key_exists($rcv_num, $PHP_checkbox)){	//更新
			foreach($val as $i => $det_id){
				if(array_key_exists($i, $PHP_select_po_ids[$rcv_num])){
					$f1 = $apb->update_field_id('qty',$PHP_rcv_qty[$rcv_num][$i],$det_id,'apb_det');
					$f1 = $apb->update_field_id('foc',$PHP_foc[$rcv_num][$i],$det_id,'apb_det');
					$f1 = $apb->update_field_id('uprice',$PHP_uprice[$rcv_num][$i],$det_id,'apb_det');
					$f1 = $apb->update_field_id('uprices',$PHP_uprices[$rcv_num][$i],$det_id,'apb_det');
					$f1 = $apb->update_field('currency',$PHP_select_currency,"rcv_id='".$det_id."'",'apb_po_link');
					$f1 = $apb->update_field('rate',$PHP_rate[$rcv_num][$i],"rcv_id='".$det_id."'",'apb_po_link');
				
					if ($PHP_special[$rcv_num][$i] < 2)
					{
						$id = explode('|',$PHP_select_po_ids[$rcv_num][$i]);								
						$s_total=0;
						$tmp=0;
						$M_sizeof = sizeof($id);
						$rowM=$rows=array();
						for ($j=0; $j< $M_sizeof; $j++)		//取得每項的請購量,並加總
						{
							// 以工廠的驗收作平均
							$where_str = "id = ".$id[$j];
							$row[$j]=$po->get_det_field('rcv_qty','ap_det',$where_str);
							$s_total += $row[$j]['rcv_qty'];
							if( $row[$j][0] < 0 ) $row[$j][0] = 1;

							// 0=副料總採購量,1=個別購量,2=ID;
							$rowM[$j]=array($PHP_rcv_qty[$rcv_num][$i],$row[$j][0],$id[$j]);
							$rows[$j]=$row[$j][0];
						}
						
						# 因為數量太小在最後計算會造成負數的產生，所以將數量排序由最小的數量開始計算
						asort($rows);

						$M=0;
						$MCt = $M_sizeof-1;
						foreach ($rows as $kay => $v2) { //計算每項採購量(平分)
							if( $M <> $MCt ) {
								$rcv_qty = $rowM[$kay][1] * ($rowM[$kay][0] / $s_total);
								$rcv_qty = number_format($rcv_qty,2,'.','');
								$tmp = $tmp + $rcv_qty;
							} else {
								$tmp_qty = $rowM[$kay][0] - $tmp;
								$rcv_qty = number_format($tmp_qty,2,'.','');		
							}
							
							$f1 = $apb->update_link_qty($det_id,$rowM[$kay][2],$rcv_qty);
							$f1 = $apb->update_field('amount',$rcv_qty * $PHP_uprice[$rcv_num][$i],"rcv_id='".$det_id."'",'apb_po_link');
							$f1 = $apb->update_apb_qty('apb_qty',$rcv_qty,$rowM[$kay][2],'ap_det');	
							$M++;
						}
						
					} else {
						$f1 = $apb->update_link_qty($val[$i],$PHP_select_po_ids[$rcv_num][$i],$PHP_rcv_qty[$rcv_num][$i]);
						$f1 = $apb->update_apb_qty('apb_qty',$PHP_rcv_qty[$rcv_num][$i],$PHP_select_po_ids[$rcv_num][$i],'ap_special');	
					}
				}else{	//刪除
					$PHP_special[$rcv_num][$i] == 2 ? $ap_table = "ap_special" : $ap_table = "ap_det";
					
						if($ap_table == 'ap_det')
						{
							$qty_fd = $apb->get_link_field('po_id, qty','apb_po_link','rcv_id ='.$det_id);
							$f1 = $apb->del_link($det_id);
							for ($j=0; $j< sizeof($qty_fd); $j++)		//加入DB
							{
								$f1=$apb->update_apb_qty('apb_qty',($qty_fd[$j]['qty']*-1), $qty_fd[$j]['po_id'],'ap_det');						
							}
							if(substr($PHP_mat_code[$rcv_num][$i],0,1) == 'F')
							{
								$ord_rec = $apb->get_ord_rec($qty_fd,'l','ap_det');
								# 要重新review $apb->back_ord_rcvd
								$f1 = $apb->back_ord_rcvd('l',$ord_rec);
							} else {
								$ord_rec = $apb->get_ord_rec($qty_fd,'a','ap_det');			
								$f1 = $apb->back_ord_rcvd('l',$ord_rec);
							}
						} else {
							$f1 = $apb->del_link($det_id);
							
								$f1 = $apb->update_apb_qty('apb_qty',($PHP_qty*-1),$det_id,'ap_special');	
								$qty_fd[0]['po_id'] = $det_id;
								if(substr($PHP_mat_code[$rcv_num][$i],0,1) == 'F')
								{
									$ord_rec = $apb->get_ord_rec($qty_fd,'l','ap_special');						
									$f1 = $apb->back_ord_rcvd('l',$ord_rec);
								} else {
									$ord_rec = $apb->get_ord_rec($qty_fd,'a','ap_special');			
									$f1 = $apb->back_ord_rcvd('l',$ord_rec);
								}
							
						}
						
						$f1 = $apb->del_det($det_id);
						$f1 = $apb->update_field("apb_rmk", 0, "rcv_num='".$rcv_num."' and po_id='".$PHP_po_ids[$rcv_num][$i]."'", "receive_det");
					
				}
			}
		}
		else{
			foreach($val as $i => $det_id){
				$PHP_special[$rcv_num][$i] == 2 ? $ap_table = "ap_special" : $ap_table = "ap_det";
					
						if($ap_table == 'ap_det')
						{
							$qty_fd = $apb->get_link_field('po_id, qty','apb_po_link','rcv_id ='.$det_id);
							$f1 = $apb->del_link($det_id);
							for ($j=0; $j< sizeof($qty_fd); $j++)		//加入DB
							{
								$f1=$apb->update_apb_qty('apb_qty',($qty_fd[$j]['qty']*-1), $qty_fd[$j]['po_id'],'ap_det');						
							}
							if(substr($PHP_mat_code[$rcv_num][$i],0,1) == 'F')
							{
								$ord_rec = $apb->get_ord_rec($qty_fd,'l','ap_det');
								# 要重新review $apb->back_ord_rcvd
								$f1 = $apb->back_ord_rcvd('l',$ord_rec);
							} else {
								$ord_rec = $apb->get_ord_rec($qty_fd,'a','ap_det');			
								$f1 = $apb->back_ord_rcvd('l',$ord_rec);
							}
						} else {
							$f1 = $apb->del_link($det_id);
							
								$f1 = $apb->update_apb_qty('apb_qty',($PHP_qty*-1),$det_id,'ap_special');	
								$qty_fd[0]['po_id'] = $det_id;
								if(substr($PHP_mat_code[$rcv_num][$i],0,1) == 'F')
								{
									$ord_rec = $apb->get_ord_rec($qty_fd,'l','ap_special');						
									$f1 = $apb->back_ord_rcvd('l',$ord_rec);
								} else {
									$ord_rec = $apb->get_ord_rec($qty_fd,'a','ap_special');			
									$f1 = $apb->back_ord_rcvd('l',$ord_rec);
								}
							
						}
						
						$f1 = $apb->del_det($det_id);
						$f1 = $apb->update_field("apb_rmk", 0, "rcv_num='".$rcv_num."' and po_id='".$PHP_po_ids[$rcv_num][$i]."'", "receive_det");
					
			}
		}
		
		$f2 = $apb->chk_del_full($PHP_rcv_num);
	}


#修改apb_oth_cost
foreach($PHP_oth_item as $id => $val){
	$apb->update_field("item", $val, "id=".$id." and apb_num='".$PHP_rcv_num."'", "apb_oth_cost");
	$apb->update_field("cost", $PHP_oth_cost[$id], "id=".$id." and apb_num='".$PHP_rcv_num."'", "apb_oth_cost");
}

#加入新增的apb_oth_cost
if(isset($PHP_des)){
	foreach($PHP_des as $k1 => $v1){
		foreach($v1 as $k2 => $v2){
			$f0 = $apb->oth_cost_add(str_replace("PO","PA",$k1), $PHP_des[$k1][$k2] , $PHP_cost[$k1][$k2], $parm['rcv_num']);
		}
	}
}

if($f2)
{
	$message = "Delete APB : ".$PHP_rcv_num;
	$log->log_add(0,"040D",$message);
	$message = "Delete APB [".$PHP_rcv_num."]";
	$redir_str = "apb.php?PHP_action=rcvd_search".$PHP_back_str."&PHP_msg=".$message;
	redirect_page($redir_str);
}

$op = $apb->show_apb($PHP_rcv_num);
$op['back_str'] = $PHP_back_str;
$message = "Successfully Edit Apb : ".$PHP_rcv_num;
$log->log_add(0,"040E",$message);
$op['msg'][]=$message;

redirect_page($PHP_SELF."?PHP_action=po_before_apb_view&PHP_rcv_num=".$parm['rcv_num']."&SCH_num=&SCH_supl=&SCH_fab=&SCH_acc=&PHP_sr_startno=1&PHP_msg=".$message);

break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "po_before_apb_submit": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "po_before_apb_submit":
check_authority($AUTH,"edit");

$f1 = $apb->update_field_id('rcv_sub_user',$GLOBALS['SCACHE']['ADMIN']['login_id'],$PHP_id,'apb');
$f1 = $apb->update_field_id('rcv_sub_date',$TODAY,$PHP_id,'apb');
$f1 = $apb->update_field_id('status',4,$PHP_id,'apb');

//$f1 = $apb->change_currency($PHP_rcv_num);
//$f1 = $apb->check_ord_rcvd($PHP_rcv_num,$PHP_ord_num,$PHP_mat_cat);

if ($PHP_ver == 0)$apb->update_field_id('version',1,$PHP_id,'apb');
# $f1 = $apb->check_po_rcvd($PHP_rcv_num);

$op = $apb->show_apb($PHP_rcv_num);
	
$op['back_str'] = $PHP_back_str;
$op['ckdate'] = substr($op['apb']['rcv_date'],0,7) < substr($TODAY,0,7) ? '1' : '0' ;

$message = "Successfully Submitted APB # : ".$PHP_rcv_num;
$op['msg'][]="Successfully Submitted APB # : ".$PHP_rcv_num;
$log->log_add(0,"040E",$message);

page_display($op, $AUTH, $TPL_PO_BEFORE_APB_SHOW);

break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "po_before_apb_revise": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "po_before_apb_revise":
check_authority($AUTH,"view");

$f1 = $apb->update_field_id('rcv_sub_user','',$PHP_id,'apb');
$f1 = $apb->update_field_id('rcv_sub_date','',$PHP_id,'apb');
$f1 = $apb->update_field_id('status',3,$PHP_id,'apb');
$f1 = $apb->update_field_id('version',($PHP_ver+1),$PHP_id,'apb');

$op = $apb->get_po_before_apb($PHP_rcv_num);

$op['date'] = $TODAY;
$op['currency'] =  $arry2->select($CURRENCY,$op['apb']['currency'],'PHP_select_currency','select','change_currency()');	//稅率狀態下拉式
$op['back_str'] = "&SCH_num=".$SCH_num."&SCH_supl=".$SCH_supl."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_sr_startno=".$PHP_sr_startno;

$op['revise'] = 1;
$msg = "驗收單  #:".$PHP_rcv_num." Revised.";
$log->log_add(0,"040V",$msg);
$op['msg'][] = $msg;

page_display($op, $AUTH, $TPL_PO_BEFORE_APB_EDIT);

break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "po_rcv_apb_revise": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "po_rcv_apb_revise":
check_authority($AUTH,"view");

$f1 = $apb->update_field_id('rcv_sub_user','',$PHP_id,'apb');
$f1 = $apb->update_field_id('rcv_sub_date','',$PHP_id,'apb');
$f1 = $apb->update_field_id('status',3,$PHP_id,'apb');
$f1 = $apb->update_field_id('version',($PHP_ver+1),$PHP_id,'apb');

$op = $apb->get_po_rcv_apb($PHP_apb_num);

$op['date'] = $TODAY;
$op['currency'] =  $arry2->select($CURRENCY,$op['apb']['currency'],'PHP_select_currency','select','change_currency()');	//稅率狀態下拉式
$op['back_str'] = "&SCH_num=".$SCH_num."&SCH_supl=".$SCH_supl."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_sr_startno=".$PHP_sr_startno;

$msg = "驗收單  #:".$PHP_apb_num." Revised.";
$log->log_add(0,"040V",$msg);
$op['msg'][] = $msg;

page_display($op, $AUTH, $TPL_PO_RCV_APB_EDIT);

break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "po_rcv_view":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "po_rcv_view":
check_authority($AUTH,"view");

$op = $apb->get_po_rcv_apb($PHP_rcv_num);

if (isset($PHP_msg))$op['msg'][]=$PHP_msg;
if(isset($PHP_sr_startno))$op['back_str'] = "&SCH_num=".$SCH_num."&SCH_supl=".$SCH_supl."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_sr_startno=".$PHP_sr_startno;
$op['ck'] = (!empty($_GET['ck']))? $_GET['ck'] : '';
$op['date'] = $TODAY;

$op['date'] = $TODAY;
$op['submit_ckdate'] = substr($op['apb']['rcv_date'],0,7) < substr($TODAY,0,7) ? '0' : '1' ;
$op['revise_ckdate'] = $op['apb']['rcv_date'] == $TODAY ? '1' : '0' ;
if(!$op['revise_ckdate'] and $op['apb']['status'] == 4){
	$op['msg'][] = "APB僅當天可Revise!";
}

if(!empty($PHP_resize)) $op['resize'] = 1;
page_display($op, $AUTH, $TPL_PO_RCV_APB_SHOW);

break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->case "apb_after_add"
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "apb_after_add":
check_authority($AUTH,"view");

$op = $apb->search_after_inventory($PHP_sup_no);

$po_ary = array();
foreach($op['rcv'] as $key1 => $val1){
	foreach($val1['rcv_det'] as $key2 => $val2){
		$po_ary[] = $val2['ap_num'];
	}
}
$po_ary = array_unique($po_ary);
$op['ap_oth_cost'] = array();
foreach($po_ary as $key=>$ap_num){
	$tmp_cost = $apb->get_link_field("*","ap_oth_cost","ap_num = '".$ap_num."' and apb_num = ''");
	if($tmp_cost)
		$op['ap_oth_cost'] = $tmp_cost;
}

$rcv_size = sizeof($op['rcv']);
$chk_cury = $op['rcv'][0]['rcv_det'][0]['currency'];
for($i=0;$i<$rcv_size;$i++){
	$det_size = sizeof($op['rcv'][$i]['rcv_det']);
	for($j=0;$j<$det_size;$j++){
		if($op['rcv'][$i]['rcv_det'][$j]['currency'] <> $chk_cury){
			$op['msg'][] = "以下 PO 幣別不同!!";
			break 2;
		}
	}
}

$op['rcv_num'] = "AP".date('y')."-xxxx";
$op['date'] = $TODAY;
$op['dept'] = $GLOBALS['SCACHE']['ADMIN']['dept'];
$op['user'] = $GLOBALS['SCACHE']['ADMIN']['login_id'];

# 前面顯示
$op['payment'] = $payment;
# 傳遞值
$op['dm_way'] = $PHP_payment;

$op['supl'] = $apb->get_det_field("vndr_no,supl_s_name,supl_f_name,uni_no","supl","vndr_no='".$PHP_sup_no."'");
$op['back_str'] = "&PHP_po=".$PHP_po."&PHP_ship=".$PHP_ship."&PHP_ship2=".$PHP_ship2."&PHP_sup=".$PHP_sup."&PHP_payment=".$PHP_payment."&PHP_ord=".$PHP_ord."&PHP_b_a=".$PHP_b_a."&payment=".urlencode($PHP_payment);
$op['currency'] = $arry2->select($CURRENCY,$op['rcv'][0]['rcv_det'][0]['currency'],'PHP_select_currency','select','change_currency()');	//幣別下拉式
//$op['msg'] = $apb->msg->get(2);

page_display($op, $AUTH, $TPL_APB_AFTER_ADD);
break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_apb_after_add": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_apb_after_add":
check_authority($AUTH,"add");

$parm = array (
"sup_no"			=>	$PHP_sup_no,
"rcv_date"			=>	$TODAY,
"rcv_user"			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
"inv_num"			=>	$PHP_inv_num,
"foreign_inv_num"	=>	$PHP_foreign_inv_num,
"inv_date"			=>	$PHP_inv_date,
"inv_price"			=>	$PHP_inv_price,
"dept"				=>	$PHP_dept,
"discount"			=>	$PHP_discount,
"off_duty"			=>	$PHP_off_duty,
"vat"				=>	$PHP_vat,
"currency"			=>	$PHP_select_currency,
"status"			=>	5,
"payment"			=>	$PHP_payment
);

$head="AP".date('y')."-";	//RP+日期=驗收單開頭
$parm['rcv_num'] = $apply->get_no($head,'rcv_num','apb');	//取得驗收的最後編號

$f1 = $apb->add($parm);

if (isset($PHP_ship_num))
{
	foreach ($PHP_ship_num as $ship_num)
	{
		if(isset($PHP_checkbox[$ship_num]))
		{
			foreach($PHP_mark[$ship_num] as $i => $mark)
			{
				if($PHP_apb_qty[$ship_num][$i] > 0){
					$parm_det = array(
					"ship_num"		=>	$ship_num,
					"inv_num"		=>	$PHP_rcv_inv[$ship_num][$i],
					"rcv_num"		=>	$parm['rcv_num'],
					"mat_code"		=>	$PHP_mat_code[$ship_num][$i],
					"qty"			=>	$PHP_apb_qty[$ship_num][$i],
					"uprices"		=>	$PHP_uprices[$ship_num][$i],
					"uprice"		=>	$PHP_uprice[$ship_num][$i],
					"color"			=>	$PHP_color[$ship_num][$i],
					"foc"			=>	$PHP_foc[$ship_num][$i],
					"ap_num"		=>	$PHP_ap_num[$ship_num][$i],
					"mat_id"		=>	$PHP_mat_id[$ship_num][$i],
					"size"			=>	$PHP_size[$ship_num][$i]
					);

					$det_id = $apb->add_det($parm_det);
					# 備註apb_num
					$apb->update_field('apb_num', $parm['rcv_num'], "type='i' and po_num='".str_replace("PA","PO",$PHP_ap_num[$ship_num][$i])."' and mat_id=".$PHP_mat_id[$ship_num][$i]." and color='".$PHP_color[$ship_num][$i]."' and size='".$PHP_size[$ship_num][$i]."' and ship_id='".$ship_num."'", "stock_inventory");
					
					$input_rate = $PHP_rate[$ship_num][$i];
					
					foreach($PHP_link_qty[$ship_num][$i] as $ord => $link_qty){
						$link_parm = array(
										"po_id"		=>	$PHP_po_id[$ship_num][$i][$ord],
										"rcv_id"	=>	$det_id,
										"qty"		=>	$link_qty,
										"currency"	=>	$PHP_select_currency,
										"amount"	=>	$PHP_uprice[$ship_num][$i] * $link_qty,
										"rate"		=>	$input_rate,
										"ord_num"	=>	$ord
										);
							$f3=$apb->add_link($link_parm);
					}
					
					# 超量備註
					/* if($PHP_reason[$i])
					{
						$parm= array(
						'rcv_num'		=>	$parm['rcv_num'],
						'item'			=>	'Overbalance.',
						'des'				=>	$PHP_reason[$i],
						'user'			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
						'k_date'		=>	$TODAY
						);
						$f4=$apb->add_log($parm);
					} */
				}
			}
		}
	}
}

# 更新ap_oth_cost
foreach($oth_cost_id as $id => $val){
	$apb->update_field_id("apb_num", $parm['rcv_num'], $id, "ap_oth_cost");
}

# 加入新增的apb_oth_cost
if(isset($PHP_des)){
	foreach($PHP_des as $k1 => $v1){
		foreach($v1 as $k2 => $v2){
			$oth_parm = array(
							"ap_num"	=>	str_replace("PO","PA",$k1),
							"item"		=>	$PHP_des[$k1][$k2],
							"cost"		=>	$PHP_cost[$k1][$k2],
							"apb_num"	=>	$parm['rcv_num'],
							"payment_status"	=>	6
							);
			$f0 = $apb->oth_cost_add($oth_parm);
		}
	}
}

$message = "successfully append Apb On : ".$parm['rcv_num'];
$op['msg'][]= $message;
$log->log_add(0,"040A",$message);

redirect_page($PHP_SELF."?PHP_action=apb_after_view&PHP_rcv_num=".$parm['rcv_num']."&SCH_num=&SCH_supl=&SCH_fab=&SCH_acc=&PHP_sr_startno=1");
break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "apb_after_view":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "apb_after_view":

check_authority($AUTH,"view");

$op = $apb->get_after_apb($PHP_rcv_num);

if (isset($PHP_msg))$op['msg'][]=$PHP_msg;
if(isset($PHP_sr_startno))$op['back_str'] = "&SCH_num=".$SCH_num."&SCH_supl=".$SCH_supl."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_sr_startno=".$PHP_sr_startno;
$op['ck'] = (!empty($_GET['ck']))? $_GET['ck'] : '';

$op['date'] = $TODAY;
$op['submit_ckdate'] = substr($op['apb']['rcv_date'],0,7) < substr($TODAY,0,7) ? '0' : '1' ;
$op['revise_ckdate'] = $op['apb']['rcv_date'] == $TODAY ? '1' : '0' ;
if(!$op['revise_ckdate'] and $op['apb']['status'] == 6){
	$op['msg'][] = "APB僅當天可Revise!";
}

if(!empty($PHP_resize)) $op['resize'] = 1;
page_display($op, $AUTH, $TPL_APB_AFTER_SHOW);

break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apb_after_edit":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "apb_after_edit":
check_authority($AUTH,"view");

$op = $apb->get_after_apb($PHP_rcv_num);

$op['date'] = $TODAY;
$op['back_str'] = "&SCH_num=".$SCH_num."&SCH_supl=".$SCH_supl."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_sr_startno=".$PHP_sr_startno;
$op['currency'] =  $arry2->select($CURRENCY,$op['apb']['currency'],'PHP_select_currency','select','change_currency()');	//幣別下拉式

$dates = $TODAY;
page_display($op, $AUTH, $TPL_APB_AFTER_EDIT);
break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_apb_after_edit":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_apb_after_edit":
check_authority($AUTH,"add");				

$parm = array (	
"rcv_date"			=>	$TODAY,
"rcv_user"			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
"inv_num"			=>	$PHP_inv_num,
"inv_date"			=>	$PHP_inv_date,
"foreign_inv_num"	=>	$PHP_foreign_inv_num,
"inv_price"			=>	$PHP_inv_price,
"discount"			=>	$PHP_discount,
"off_duty"			=>	$PHP_off_duty,
"vat"				=>	$PHP_vat,
"currency"			=>	$PHP_select_currency,
"rcv_num"			=>	$PHP_apb_num
);

$f1 = $apb->edit($parm);

	foreach($PHP_det_id as $ship_num => $val){
		if (array_key_exists($ship_num, $PHP_checkbox)){ # 更新
			foreach($val as $i => $det_id){
				if(array_key_exists($i, $PHP_mark[$ship_num])){
					$f1 = $apb->update_field_id('qty',$PHP_apb_qty[$ship_num][$i],$det_id,'apb_det');
					$f1 = $apb->update_field_id('foc',$PHP_foc[$ship_num][$i],$det_id,'apb_det');
					$f1 = $apb->update_field_id('uprice',$PHP_uprice[$ship_num][$i],$det_id,'apb_det');
					$f1 = $apb->update_field_id('uprices',$PHP_uprices[$ship_num][$i],$det_id,'apb_det');
					$f1 = $apb->update_field('currency',$PHP_select_currency,"rcv_id='".$det_id."'",'apb_po_link');
					$f1 = $apb->update_field('rate',$PHP_rate[$ship_num][$i],"rcv_id='".$det_id."'",'apb_po_link');
				
					foreach ($PHP_link_qty[$ship_num][$i] as $link_id => $link_qty) {
						$f1 = $apb->update_field_id("qty", $link_qty, $link_id, "apb_po_link");
					}
				}else{	//刪除
					$f1 = $apb->del_link($det_id);
					$f1 = $apb->del_det($det_id);
					$f1 = $apb->update_field("apb_num", "", "ship_inv='".$ship_num."' and po_num='".$PHP_po_num[$ship_num][$i]."' and mat_id='".$PHP_mat_id[$ship_num][$i]."' and color='".$PHP_color[$ship_num][$i]."' and size='".$PHP_size[$ship_num][$i]."'", "stock_inventory");
				}
			}
		}else{ # 刪除
			foreach($val as $i => $det_id){
				$f1 = $apb->del_link($det_id);
				$f1 = $apb->del_det($det_id);
				$f1 = $apb->update_field("apb_num", "", "ship_inv='".$ship_num."' and po_num='".$PHP_po_num[$ship_num][$i]."' and mat_id='".$PHP_mat_id[$ship_num][$i]."' and color='".$PHP_color[$ship_num][$i]."' and size='".$PHP_size[$ship_num][$i]."'", "stock_inventory");
			}
		}
		$f2 = $apb->chk_del_full($PHP_apb_num);
	}

if($f2){
	$message = "Delete APB : ".$PHP_rcv_num;
	$log->log_add(0,"040D",$message);
	$message = "Delete APB [".$PHP_rcv_num."]";
	$redir_str = "apb.php?PHP_action=rcvd_search".$PHP_back_str."&PHP_msg=".$message;
	redirect_page($redir_str);
}else{
	#修改apb_oth_cost
	foreach($PHP_oth_item as $id => $val){
		$apb->update_field("item", $val, "id=".$id." and apb_num='".$PHP_apb_num."'", "apb_oth_cost");
		$apb->update_field("cost", $PHP_oth_cost[$id], "id=".$id." and apb_num='".$PHP_apb_num."'", "apb_oth_cost");
	}

	#加入新增的apb_oth_cost
	if(isset($PHP_des)){
		foreach($PHP_des as $po_num => $v1){
			foreach($v1 as $k2 => $val){
				$parm = array(
							"ap_num"			=>	str_replace("PO","PA",$po_num),
							"item"				=>	$val,
							"cost"				=>	$PHP_cost[$po_num][$k2],
							"apb_num"			=>	$PHP_apb_num,
							"payment_status"	=>	6
						);
				$f0 = $apb->oth_cost_add($parm);
			}
		}
	}
}

$op = $apb->get_after_apb($PHP_apb_num);
$op['back_str'] = $PHP_back_str;
$message = "Successfully Edit Apb : ".$PHP_apb_num;
$log->log_add(0,"040E",$message);
$op['msg'][]=$message;
$op['ckdate'] = substr($op['apb']['rcv_date'],0,7) < substr($TODAY,0,7) ? '1' : '0' ;
page_display($op, $AUTH, $TPL_APB_AFTER_SHOW);

break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apb_after_submit":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "apb_after_submit":
check_authority($AUTH,"edit");

$f1 = $apb->update_field_id('rcv_sub_user',$GLOBALS['SCACHE']['ADMIN']['login_id'],$PHP_id,'apb');
$f1 = $apb->update_field_id('rcv_sub_date',$TODAY,$PHP_id,'apb');
$f1 = $apb->update_field_id('status',6,$PHP_id,'apb');

if ($PHP_ver == 0)$apb->update_field_id('version',1,$PHP_id,'apb');

$op = $apb->get_after_apb($PHP_apb_num);

$op['back_str'] = $PHP_back_str;
$op['ckdate'] = substr($op['apb']['rcv_date'],0,7) < substr($TODAY,0,7) ? '1' : '0' ;

$message = "Successfully Submitted APB # : ".$PHP_apb_num;
$op['msg'][]="Successfully Submitted APB # : ".$PHP_apb_num;
$log->log_add(0,"040E",$message);

page_display($op, $AUTH, $TPL_APB_AFTER_SHOW);

break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "apb_after_revise":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "apb_after_revise":
check_authority($AUTH,"view");

$f1 = $apb->update_field_id('rcv_sub_user','',$PHP_id,'apb');
$f1 = $apb->update_field_id('rcv_sub_date','',$PHP_id,'apb');
$f1 = $apb->update_field_id('status',5,$PHP_id,'apb');
$f1 = $apb->update_field_id('version',($PHP_ver+1),$PHP_id,'apb');

$op = $apb->get_after_apb($PHP_apb_num);

$op['date'] = $TODAY;
$op['currency'] =  $arry2->select($CURRENCY,$op['apb']['currency'],'PHP_select_currency','select','change_currency()');	//稅率狀態下拉式
$op['back_str'] = "&SCH_num=".$SCH_num."&SCH_supl=".$SCH_supl."&PHP_sr_startno=".$PHP_sr_startno;

$msg = "驗收單  #:".$PHP_apb_num." Revised.";
$log->log_add(0,"040V",$msg);
$op['msg'][] = $msg;

page_display($op, $AUTH, $TPL_APB_AFTER_EDIT);

break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->case "apb_lc_add"
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "apb_lc_add":
check_authority($AUTH,"view");

$op = $apb->search_lc_inventory($PHP_sup_no);

$po_ary = array();
foreach($op['rcv'] as $key1 => $val1){
	foreach($val1['rcv_det'] as $key2 => $val2){
		$po_ary[] = $val2['ap_num'];
	}
}
$po_ary = array_unique($po_ary);
$op['ap_oth_cost'] = array();
foreach($po_ary as $key=>$ap_num){
	$tmp_cost = $apb->get_link_field("*","ap_oth_cost","ap_num = '".$ap_num."' and apb_num = ''");
	if($tmp_cost)
		$op['ap_oth_cost'] = $tmp_cost;
}

$rcv_size = sizeof($op['rcv']);
$chk_cury = $op['rcv'][0]['rcv_det'][0]['currency'];
for($i=0;$i<$rcv_size;$i++){
	$det_size = sizeof($op['rcv'][$i]['rcv_det']);
	for($j=0;$j<$det_size;$j++){
		if($op['rcv'][$i]['rcv_det'][$j]['currency'] <> $chk_cury){
			$op['msg'][] = "以下 PO 幣別不同!!";
			break 2;
		}
	}
}

$op['rcv_num'] = "AP".date('y')."-xxxx";
$op['date'] = $TODAY;
$op['dept'] = $GLOBALS['SCACHE']['ADMIN']['dept'];
$op['user'] = $GLOBALS['SCACHE']['ADMIN']['login_id'];

# 前面顯示
$op['payment'] = $payment;
# 傳遞值
$op['dm_way'] = $PHP_payment;

$op['supl'] = $apb->get_det_field("vndr_no,supl_s_name,supl_f_name,uni_no","supl","vndr_no='".$PHP_sup_no."'");
$op['back_str'] = "&PHP_po=".$PHP_po."&PHP_ship=".$PHP_ship."&PHP_ship2=".$PHP_ship2."&PHP_sup=".$PHP_sup."&PHP_payment=".$PHP_payment."&PHP_ord=".$PHP_ord."&PHP_b_a=".$PHP_b_a."&payment=".urlencode($PHP_payment);
$op['currency'] = $arry2->select($CURRENCY,$op['rcv'][0]['rcv_det'][0]['currency'],'PHP_select_currency','select','change_currency()');	//幣別下拉式
//$op['msg'] = $apb->msg->get(2);

page_display($op, $AUTH, $TPL_APB_LC_ADD);
break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_apb_lc_add":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_apb_lc_add":
check_authority($AUTH,"add");

$parm = array (
"sup_no"			=>	$PHP_sup_no,
"rcv_date"			=>	$TODAY,
"rcv_user"			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
"inv_num"			=>	$PHP_inv_num,
"foreign_inv_num"	=>	$PHP_foreign_inv_num,
"inv_date"			=>	$PHP_inv_date,
"inv_price"			=>	$PHP_inv_price,
"dept"				=>	$PHP_dept,
"discount"			=>	$PHP_discount,
"off_duty"			=>	$PHP_off_duty,
"vat"				=>	$PHP_vat,
"currency"			=>	$PHP_select_currency,
"status"			=>	7,
"payment"			=>	$PHP_payment
);

$head="AP".date('y')."-";	//RP+日期=驗收單開頭
$parm['rcv_num'] = $apply->get_no($head,'rcv_num','apb');	//取得驗收的最後編號

$f1 = $apb->add($parm);

if (isset($PHP_ship_num))
{
	foreach ($PHP_ship_num as $ship_num)
	{
		if(isset($PHP_checkbox[$ship_num]))
		{
			foreach($PHP_mark[$ship_num] as $i => $mark)
			{
				if($PHP_apb_qty[$ship_num][$i] > 0){
					$parm_det = array(
					"ship_num"		=>	$ship_num,
					"inv_num"		=>	$PHP_rcv_inv[$ship_num][$i],
					"rcv_num"		=>	$parm['rcv_num'],
					"mat_code"		=>	$PHP_mat_code[$ship_num][$i],
					"qty"			=>	$PHP_apb_qty[$ship_num][$i],
					"uprices"		=>	$PHP_uprices[$ship_num][$i],
					"uprice"		=>	$PHP_uprice[$ship_num][$i],
					"color"			=>	$PHP_color[$ship_num][$i],
					"foc"			=>	$PHP_foc[$ship_num][$i],
					"ap_num"		=>	$PHP_ap_num[$ship_num][$i],
					"mat_id"		=>	$PHP_mat_id[$ship_num][$i],
					"size"			=>	$PHP_size[$ship_num][$i]
					);

					$det_id = $apb->add_det($parm_det);
					# 備註apb_num
					$apb->update_field('apb_num', $parm['rcv_num'], "type='i' and po_num='".str_replace("PA","PO",$PHP_ap_num[$ship_num][$i])."' and mat_id=".$PHP_mat_id[$ship_num][$i]." and color='".$PHP_color[$ship_num][$i]."' and size='".$PHP_size[$ship_num][$i]."' and ship_id'".$PHP_ship_num[$ship_num]."'", "stock_inventory");
					
					$input_rate = $PHP_rate[$ship_num][$i];
					
					foreach($PHP_link_qty[$ship_num][$i] as $ord => $link_qty){
						$link_parm = array(
										"po_id"		=>	$PHP_po_id[$ship_num][$i][$ord],
										"rcv_id"	=>	$det_id,
										"qty"		=>	$link_qty,
										"currency"	=>	$PHP_select_currency,
										"amount"	=>	$PHP_uprice[$ship_num][$i] * $link_qty,
										"rate"		=>	$input_rate,
										"ord_num"	=>	$ord
										);
							$f3=$apb->add_link($link_parm);
					}
					
					# 超量備註
					/* if($PHP_reason[$i])
					{
						$parm= array(
						'rcv_num'		=>	$parm['rcv_num'],
						'item'			=>	'Overbalance.',
						'des'				=>	$PHP_reason[$i],
						'user'			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
						'k_date'		=>	$TODAY
						);
						$f4=$apb->add_log($parm);
					} */
				}
			}
		}
	}
}

# 更新ap_oth_cost
foreach($oth_cost_id as $id => $val){
	$apb->update_field_id("apb_num", $parm['rcv_num'], $id, "ap_oth_cost");
}

# 加入新增的apb_oth_cost
if(isset($PHP_des)){
	foreach($PHP_des as $k1 => $v1){
		foreach($v1 as $k2 => $v2){
			$oth_parm = array(
							"ap_num"	=>	str_replace("PO","PA",$k1),
							"item"		=>	$PHP_des[$k1][$k2],
							"cost"		=>	$PHP_cost[$k1][$k2],
							"apb_num"	=>	$parm['rcv_num'],
							"payment_status"	=>	8
							);
			$f0 = $apb->oth_cost_add($oth_parm);
		}
	}
}

$message = "successfully append Apb On : ".$parm['rcv_num'];
$op['msg'][]= $message;
$log->log_add(0,"040A",$message);

redirect_page($PHP_SELF."?PHP_action=apb_lc_view&PHP_rcv_num=".$parm['rcv_num']."&SCH_num=&SCH_supl=&SCH_fab=&SCH_acc=&PHP_sr_startno=1");
break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "apb_lc_view":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "apb_lc_view":
check_authority($AUTH,"view");

$op = $apb->get_lc_apb($PHP_rcv_num);

if (isset($PHP_msg))$op['msg'][]=$PHP_msg;
if(isset($PHP_sr_startno))$op['back_str'] = "&SCH_num=".$SCH_num."&SCH_supl=".$SCH_supl."&PHP_sr_startno=".$PHP_sr_startno;
$op['ck'] = (!empty($_GET['ck']))? $_GET['ck'] : '';

$op['date'] = $TODAY;
$op['submit_ckdate'] = substr($op['apb']['rcv_date'],0,7) < substr($TODAY,0,7) ? '0' : '1' ;
$op['revise_ckdate'] = $op['apb']['rcv_date'] == $TODAY ? '1' : '0' ;
if(!$op['revise_ckdate'] and $op['apb']['status'] == 8){
	$op['msg'][] = "APB僅當天可Revise!";
}

if(!empty($PHP_resize)) $op['resize'] = 1;
page_display($op, $AUTH, $TPL_APB_LC_SHOW);

break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "apb_lc_edit":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "apb_lc_edit":
check_authority($AUTH,"view");

$op = $apb->get_lc_apb($PHP_rcv_num);

$op['date'] = $TODAY;
$op['back_str'] = "&SCH_num=".$SCH_num."&SCH_supl=".$SCH_supl."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_sr_startno=".$PHP_sr_startno;
$op['currency'] =  $arry2->select($CURRENCY,$op['apb']['currency'],'PHP_select_currency','select','change_currency()');	//幣別下拉式

$dates = $TODAY;
page_display($op, $AUTH, $TPL_APB_LC_EDIT);
break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_apb_lc_edit":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_apb_lc_edit":
check_authority($AUTH,"view");				

$parm = array (	
"rcv_date"			=>	$TODAY,
"rcv_user"			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
"inv_num"			=>	$PHP_inv_num,
"inv_date"			=>	$PHP_inv_date,
"foreign_inv_num"	=>	$PHP_foreign_inv_num,
"inv_price"			=>	$PHP_inv_price,
"discount"			=>	$PHP_discount,
"off_duty"			=>	$PHP_off_duty,
"vat"				=>	$PHP_vat,
"currency"			=>	$PHP_select_currency,
"rcv_num"			=>	$PHP_apb_num
);

$f1 = $apb->edit($parm);

	foreach($PHP_det_id as $ship_num => $val){
		if (array_key_exists($ship_num, $PHP_checkbox)){ # 更新
			foreach($val as $i => $det_id){
				if(array_key_exists($i, $PHP_mark[$ship_num])){
					$f1 = $apb->update_field_id('qty',$PHP_apb_qty[$ship_num][$i],$det_id,'apb_det');
					$f1 = $apb->update_field_id('foc',$PHP_foc[$ship_num][$i],$det_id,'apb_det');
					$f1 = $apb->update_field_id('uprice',$PHP_uprice[$ship_num][$i],$det_id,'apb_det');
					$f1 = $apb->update_field_id('uprices',$PHP_uprices[$ship_num][$i],$det_id,'apb_det');
					$f1 = $apb->update_field('currency',$PHP_select_currency,"rcv_id='".$det_id."'",'apb_po_link');
					$f1 = $apb->update_field('rate',$PHP_rate[$ship_num][$i],"rcv_id='".$det_id."'",'apb_po_link');
				
					foreach ($PHP_link_qty[$ship_num][$i] as $link_id => $link_qty) {
						$f1 = $apb->update_field_id("qty", $link_qty, $link_id, "apb_po_link");
					}
				}else{	//刪除
					$f1 = $apb->del_link($det_id);
					$f1 = $apb->del_det($det_id);
					$f1 = $apb->update_field("apb_num", "", "ship_inv='".$ship_num."' and po_num='".$PHP_po_num[$ship_num][$i]."' and mat_id='".$PHP_mat_id[$ship_num][$i]."' and color='".$PHP_color[$ship_num][$i]."' and size='".$PHP_size[$ship_num][$i]."'", "stock_inventory");
				}
			}
		}else{ # 刪除
			foreach($val as $i => $det_id){
				$f1 = $apb->del_link($det_id);
				$f1 = $apb->del_det($det_id);
				$f1 = $apb->update_field("apb_num", "", "ship_inv='".$ship_num."' and po_num='".$PHP_po_num[$ship_num][$i]."' and mat_id='".$PHP_mat_id[$ship_num][$i]."' and color='".$PHP_color[$ship_num][$i]."' and size='".$PHP_size[$ship_num][$i]."'", "stock_inventory");
			}
		}
		$f2 = $apb->chk_del_full($PHP_apb_num);
	}

if($f2){
	$message = "Delete APB : ".$PHP_rcv_num;
	$log->log_add(0,"040D",$message);
	$message = "Delete APB [".$PHP_rcv_num."]";
	$redir_str = "apb.php?PHP_action=rcvd_search".$PHP_back_str."&PHP_msg=".$message;
	redirect_page($redir_str);
}else{
	#修改apb_oth_cost
	foreach($PHP_oth_item as $id => $val){
		$apb->update_field("item", $val, "id=".$id." and apb_num='".$PHP_apb_num."'", "apb_oth_cost");
		$apb->update_field("cost", $PHP_oth_cost[$id], "id=".$id." and apb_num='".$PHP_apb_num."'", "apb_oth_cost");
	}

	#加入新增的apb_oth_cost
	if(isset($PHP_des)){
		foreach($PHP_des as $po_num => $v1){
			foreach($v1 as $k2 => $val){
				$parm = array(
							"ap_num"			=>	str_replace("PO","PA",$po_num),
							"item"				=>	$val,
							"cost"				=>	$PHP_cost[$po_num][$k2],
							"apb_num"			=>	$PHP_apb_num,
							"payment_status"	=>	8
						);
				$f0 = $apb->oth_cost_add($parm);
			}
		}
	}
}

$op = $apb->get_lc_apb($PHP_apb_num);
$op['back_str'] = $PHP_back_str;
$message = "Successfully Edit Apb : ".$PHP_apb_num;
$log->log_add(0,"040E",$message);
$op['msg'][]=$message;
$op['ckdate'] = substr($op['apb']['rcv_date'],0,7) < substr($TODAY,0,7) ? '1' : '0' ;
page_display($op, $AUTH, $TPL_APB_LC_SHOW);

break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apb_lc_submit":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "apb_lc_submit":
check_authority($AUTH,"edit");

$f1 = $apb->update_field_id('rcv_sub_user',$GLOBALS['SCACHE']['ADMIN']['login_id'],$PHP_id,'apb');
$f1 = $apb->update_field_id('rcv_sub_date',$TODAY,$PHP_id,'apb');
$f1 = $apb->update_field_id('status',8,$PHP_id,'apb');

if ($PHP_ver == 0)$apb->update_field_id('version',1,$PHP_id,'apb');

$op = $apb->get_lc_apb($PHP_apb_num);

$op['back_str'] = $PHP_back_str;
$op['ckdate'] = substr($op['apb']['rcv_date'],0,7) < substr($TODAY,0,7) ? '1' : '0' ;

$message = "Successfully Submitted APB # : ".$PHP_apb_num;
$op['msg'][]="Successfully Submitted APB # : ".$PHP_apb_num;
$log->log_add(0,"040E",$message);

page_display($op, $AUTH, $TPL_APB_LC_SHOW);

break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "apb_lc_revise": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "apb_lc_revise":
check_authority($AUTH,"view");

$f1 = $apb->update_field_id('rcv_sub_user','',$PHP_id,'apb');
$f1 = $apb->update_field_id('rcv_sub_date','',$PHP_id,'apb');
$f1 = $apb->update_field_id('status',7,$PHP_id,'apb');
$f1 = $apb->update_field_id('version',($PHP_ver+1),$PHP_id,'apb');

$op = $apb->get_lc_apb($PHP_apb_num);

$op['date'] = $TODAY;
$op['currency'] =  $arry2->select($CURRENCY,$op['apb']['currency'],'PHP_select_currency','select','change_currency()');	//稅率狀態下拉式
$op['back_str'] = "&SCH_num=".$SCH_num."&SCH_supl=".$SCH_supl."&PHP_sr_startno=".$PHP_sr_startno;

$msg = "驗收單  #:".$PHP_apb_num." Revised.";
$log->log_add(0,"040V",$msg);
$op['msg'][] = $msg;

page_display($op, $AUTH, $TPL_APB_LC_EDIT);

break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->case "apb_month_fty_add"
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "apb_month_fty_add":
check_authority($AUTH,"view");

$op = $apb->search_month_fty_inventory($PHP_sup_no);

$po_ary = array();
foreach($op['rcv'] as $key1 => $val1){
	foreach($val1['rcv_det'] as $key2 => $val2){
		$po_ary[] = $val2['ap_num'];
	}
}
$po_ary = array_unique($po_ary);
$op['ap_oth_cost'] = array();
foreach($po_ary as $key=>$ap_num){
	$tmp_cost = $apb->get_link_field("*","ap_oth_cost","ap_num = '".$ap_num."' and apb_num = ''");
	if($tmp_cost)
		$op['ap_oth_cost'] = $tmp_cost;
}
$rcv_size = sizeof($op['rcv']);
$chk_cury = $op['rcv'][0]['rcv_det'][0]['currency'];
for($i=0;$i<$rcv_size;$i++){
	$det_size = sizeof($op['rcv'][$i]['rcv_det']);
	for($j=0;$j<$det_size;$j++){
		if($op['rcv'][$i]['rcv_det'][$j]['currency'] <> $chk_cury){
			$op['msg'][] = "以下 PO 幣別不同!!";
			break 2;
		}
	}
}

$op['rcv_num'] = "AP".date('y')."-xxxx";
$op['date'] = $TODAY;
$op['dept'] = $GLOBALS['SCACHE']['ADMIN']['dept'];
$op['user'] = $GLOBALS['SCACHE']['ADMIN']['login_id'];

# 前面顯示
$op['payment'] = $payment;
# 傳遞值
$op['dm_way'] = $PHP_payment;

$op['supl'] = $apb->get_det_field("vndr_no,supl_s_name,supl_f_name,uni_no","supl","vndr_no='".$PHP_sup_no."'");
$op['back_str'] = "&PHP_po=".$PHP_po."&PHP_ship=".$PHP_ship."&PHP_ship2=".$PHP_ship2."&PHP_sup=".$PHP_sup."&PHP_payment=".$PHP_payment."&PHP_ord=".$PHP_ord."&PHP_b_a=".$PHP_b_a."&payment=".urlencode($PHP_payment);
$op['currency'] = $arry2->select($CURRENCY,$op['rcv'][0]['rcv_det'][0]['currency'],'PHP_select_currency','select','change_currency()');	//幣別下拉式
//$op['msg'] = $apb->msg->get(2);

page_display($op, $AUTH, $TPL_APB_MONTH_FTY_ADD);
break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_apb_month_fty_add": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_apb_month_fty_add":
check_authority($AUTH,"add");

$parm = array (
"sup_no"			=>	$PHP_sup_no,
"rcv_date"			=>	$TODAY,
"rcv_user"			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
"inv_num"			=>	$PHP_inv_num,
"foreign_inv_num"	=>	$PHP_foreign_inv_num,
"inv_date"			=>	$PHP_inv_date,
"inv_price"			=>	$PHP_inv_price,
"dept"				=>	$PHP_dept,
"discount"			=>	$PHP_discount,
"off_duty"			=>	$PHP_off_duty,
"vat"				=>	$PHP_vat,
"currency"			=>	$PHP_select_currency,
"status"			=>	9,
"payment"			=>	$PHP_payment
);

$head="AP".date('y')."-";	//RP+日期=驗收單開頭
$parm['rcv_num'] = $apply->get_no($head,'rcv_num','apb');	//取得驗收的最後編號

$f1 = $apb->add($parm);

if (isset($PHP_ship_num))
{
	foreach ($PHP_ship_num as $ship_num)
	{
		if(isset($PHP_checkbox[$ship_num]))
		{
			foreach($PHP_mark[$ship_num] as $i => $mark)
			{
				if($PHP_apb_qty[$ship_num][$i] > 0){
					$parm_det = array(
					"ship_num"		=>	$ship_num,
					"inv_num"		=>	$PHP_rcv_inv[$ship_num][$i],
					"rcv_num"		=>	$parm['rcv_num'],
					"mat_code"		=>	$PHP_mat_code[$ship_num][$i],
					"qty"			=>	$PHP_apb_qty[$ship_num][$i],
					"uprices"		=>	$PHP_uprices[$ship_num][$i],
					"uprice"		=>	$PHP_uprice[$ship_num][$i],
					"color"			=>	$PHP_color[$ship_num][$i],
					"foc"			=>	$PHP_foc[$ship_num][$i],
					"ap_num"		=>	$PHP_ap_num[$ship_num][$i],
					"mat_id"		=>	$PHP_mat_id[$ship_num][$i],
					"size"			=>	$PHP_size[$ship_num][$i]
					);

					$det_id = $apb->add_det($parm_det);
					# 備註apb_num
					$apb->update_field('apb_num', $parm['rcv_num'], "type='i' and po_num='".str_replace("PA","PO",$PHP_ap_num[$ship_num][$i])."' and mat_id=".$PHP_mat_id[$ship_num][$i]." and color='".$PHP_color[$ship_num][$i]."' and size='".$PHP_size[$ship_num][$i]."' and ship_id='".$ship_num."'", "stock_inventory");
					
					$input_rate = $PHP_rate[$ship_num][$i];
					
					foreach($PHP_link_qty[$ship_num][$i] as $ord => $link_qty){
						$link_parm = array(
										"po_id"		=>	$PHP_po_id[$ship_num][$i][$ord],
										"rcv_id"	=>	$det_id,
										"qty"		=>	$link_qty,
										"currency"	=>	$PHP_select_currency,
										"amount"	=>	$PHP_uprice[$ship_num][$i] * $link_qty,
										"rate"		=>	$input_rate,
										"ord_num"	=>	$ord
										);
							$f3=$apb->add_link($link_parm);
					}
					
					# 超量備註
					/* if($PHP_reason[$i])
					{
						$parm= array(
						'rcv_num'		=>	$parm['rcv_num'],
						'item'			=>	'Overbalance.',
						'des'				=>	$PHP_reason[$i],
						'user'			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
						'k_date'		=>	$TODAY
						);
						$f4=$apb->add_log($parm);
					} */
				}
			}
		}
	}
}

# 更新ap_oth_cost
foreach($oth_cost_id as $id => $val){
	$apb->update_field_id("apb_num", $parm['rcv_num'], $id, "ap_oth_cost");
}
# 加入新增的apb_oth_cost
if(isset($PHP_des)){
	foreach($PHP_des as $k1 => $v1){
		foreach($v1 as $k2 => $v2){
			$oth_parm = array(
							"ap_num"	=>	str_replace("PO","PA",$k1),
							"item"		=>	$PHP_des[$k1][$k2],
							"cost"		=>	$PHP_cost[$k1][$k2],
							"apb_num"	=>	$parm['rcv_num'],
							"payment_status"	=>	10
							);
			$f0 = $apb->oth_cost_add($oth_parm);
		}
	}
}

$message = "successfully append Apb On : ".$parm['rcv_num'];
$op['msg'][]= $message;
$log->log_add(0,"040A",$message);

redirect_page($PHP_SELF."?PHP_action=apb_month_fty_view&PHP_rcv_num=".$parm['rcv_num']."&SCH_num=&SCH_supl=&SCH_fab=&SCH_acc=&PHP_sr_startno=1");
break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "apb_month_fty_view":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "apb_month_fty_view":

check_authority($AUTH,"view");

$op = $apb->get_month_fty_apb($PHP_rcv_num);

if (isset($PHP_msg))$op['msg'][]=$PHP_msg;
if(isset($PHP_sr_startno))$op['back_str'] = "&SCH_num=".$SCH_num."&SCH_supl=".$SCH_supl."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_sr_startno=".$PHP_sr_startno;
$op['ck'] = (!empty($_GET['ck']))? $_GET['ck'] : '';

$op['date'] = $TODAY;
$op['submit_ckdate'] = substr($op['apb']['rcv_date'],0,7) < substr($TODAY,0,7) ? '0' : '1' ;
$op['revise_ckdate'] = $op['apb']['rcv_date'] == $TODAY ? '1' : '0' ;
if(!$op['revise_ckdate'] and $op['apb']['status'] == 10){
	$op['msg'][] = "APB僅當天可Revise!";
}

if(!empty($PHP_resize)) $op['resize'] = 1;
page_display($op, $AUTH, $TPL_APB_MONTH_FTY_SHOW);

break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apb_month_fty_edit":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "apb_month_fty_edit":
check_authority($AUTH,"view");

$op = $apb->get_month_fty_apb($PHP_rcv_num);

$op['date'] = $TODAY;
$op['back_str'] = "&SCH_num=".$SCH_num."&SCH_supl=".$SCH_supl."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_sr_startno=".$PHP_sr_startno;
$op['currency'] =  $arry2->select($CURRENCY,$op['apb']['currency'],'PHP_select_currency','select','change_currency()');	//幣別下拉式

$dates = $TODAY;
page_display($op, $AUTH, $TPL_APB_MONTH_FTY_EDIT);
break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_apb_month_fty_edit":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_apb_month_fty_edit":
check_authority($AUTH,"view");				

$parm = array (	
"rcv_date"			=>	$TODAY,
"rcv_user"			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
"inv_num"			=>	$PHP_inv_num,
"inv_date"			=>	$PHP_inv_date,
"foreign_inv_num"	=>	$PHP_foreign_inv_num,
"inv_price"			=>	$PHP_inv_price,
"discount"			=>	$PHP_discount,
"off_duty"			=>	$PHP_off_duty,
"vat"				=>	$PHP_vat,
"currency"			=>	$PHP_select_currency,
"rcv_num"			=>	$PHP_apb_num
);

$f1 = $apb->edit($parm);

	foreach($PHP_det_id as $ship_num => $val){
		if (array_key_exists($ship_num, $PHP_checkbox)){ # 更新
			foreach($val as $i => $det_id){
				if(array_key_exists($i, $PHP_mark[$ship_num])){
					$f1 = $apb->update_field_id('qty',$PHP_apb_qty[$ship_num][$i],$det_id,'apb_det');
					$f1 = $apb->update_field_id('foc',$PHP_foc[$ship_num][$i],$det_id,'apb_det');
					$f1 = $apb->update_field_id('uprice',$PHP_uprice[$ship_num][$i],$det_id,'apb_det');
					$f1 = $apb->update_field_id('uprices',$PHP_uprices[$ship_num][$i],$det_id,'apb_det');
					$f1 = $apb->update_field('currency',$PHP_select_currency,"rcv_id='".$det_id."'",'apb_po_link');
					$f1 = $apb->update_field('rate',$PHP_rate[$ship_num][$i],"rcv_id='".$det_id."'",'apb_po_link');
				
					foreach ($PHP_link_qty[$ship_num][$i] as $link_id => $link_qty) {
						$f1 = $apb->update_field_id("qty", $link_qty, $link_id, "apb_po_link");
					}
				}else{	//刪除
					$f1 = $apb->del_link($det_id);
					$f1 = $apb->del_det($det_id);
					$f1 = $apb->update_field("apb_num", "", "ship_inv='".$ship_num."' and po_num='".$PHP_po_num[$ship_num][$i]."' and mat_id='".$PHP_mat_id[$ship_num][$i]."' and color='".$PHP_color[$ship_num][$i]."' and size='".$PHP_size[$ship_num][$i]."'", "stock_inventory");
				}
			}
		}else{ # 刪除
			foreach($val as $i => $det_id){
				$f1 = $apb->del_link($det_id);
				$f1 = $apb->del_det($det_id);
				$f1 = $apb->update_field("apb_num", "", "ship_inv='".$ship_num."' and po_num='".$PHP_po_num[$ship_num][$i]."' and mat_id='".$PHP_mat_id[$ship_num][$i]."' and color='".$PHP_color[$ship_num][$i]."' and size='".$PHP_size[$ship_num][$i]."'", "stock_inventory");
			}
		}
		$f2 = $apb->chk_del_full($PHP_apb_num);
	}

if($f2){
	$message = "Delete APB : ".$PHP_rcv_num;
	$log->log_add(0,"040D",$message);
	$message = "Delete APB [".$PHP_rcv_num."]";
	$redir_str = "apb.php?PHP_action=rcvd_search".$PHP_back_str."&PHP_msg=".$message;
	redirect_page($redir_str);
}else{
	#修改apb_oth_cost
	foreach($PHP_oth_item as $id => $val){
		$apb->update_field("item", $val, "id=".$id." and apb_num='".$PHP_apb_num."'", "apb_oth_cost");
		$apb->update_field("cost", $PHP_oth_cost[$id], "id=".$id." and apb_num='".$PHP_apb_num."'", "apb_oth_cost");
	}

	#加入新增的apb_oth_cost
	if(isset($PHP_des)){
		foreach($PHP_des as $po_num => $v1){
			foreach($v1 as $k2 => $val){
				$parm = array(
							"ap_num"			=>	str_replace("PO","PA",$po_num),
							"item"				=>	$val,
							"cost"				=>	$PHP_cost[$po_num][$k2],
							"apb_num"			=>	$PHP_apb_num,
							"payment_status"	=>	10
						);
				$f0 = $apb->oth_cost_add($parm);
			}
		}
	}
}

$op = $apb->get_month_fty_apb($PHP_apb_num);
$op['back_str'] = $PHP_back_str;
$message = "Successfully Edit Apb : ".$PHP_apb_num;
$log->log_add(0,"040E",$message);
$op['msg'][]=$message;
$op['ckdate'] = substr($op['apb']['rcv_date'],0,7) < substr($TODAY,0,7) ? '1' : '0' ;
page_display($op, $AUTH, $TPL_APB_MONTH_FTY_SHOW);

break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apb_month_fty_submit":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "apb_month_fty_submit":
check_authority($AUTH,"edit");

$f1 = $apb->update_field_id('rcv_sub_user',$GLOBALS['SCACHE']['ADMIN']['login_id'],$PHP_id,'apb');
$f1 = $apb->update_field_id('rcv_sub_date',$TODAY,$PHP_id,'apb');
$f1 = $apb->update_field_id('status',10,$PHP_id,'apb');

if ($PHP_ver == 0)$apb->update_field_id('version',1,$PHP_id,'apb');

$op = $apb->get_month_fty_apb($PHP_apb_num);

$op['back_str'] = $PHP_back_str;
$op['ckdate'] = substr($op['apb']['rcv_date'],0,7) < substr($TODAY,0,7) ? '1' : '0' ;

$message = "Successfully Submitted APB # : ".$PHP_apb_num;
$op['msg'][]="Successfully Submitted APB # : ".$PHP_apb_num;
$log->log_add(0,"040E",$message);

page_display($op, $AUTH, $TPL_APB_MONTH_FTY_SHOW);

break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "apb_month_fty_revise":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "apb_month_fty_revise":
check_authority($AUTH,"view");

$f1 = $apb->update_field_id('rcv_sub_user','',$PHP_id,'apb');
$f1 = $apb->update_field_id('rcv_sub_date','',$PHP_id,'apb');
$f1 = $apb->update_field_id('status',9,$PHP_id,'apb');
$f1 = $apb->update_field_id('version',($PHP_ver+1),$PHP_id,'apb');

$op = $apb->get_month_fty_apb($PHP_apb_num);

$op['date'] = $TODAY;
$op['currency'] =  $arry2->select($CURRENCY,$op['apb']['currency'],'PHP_select_currency','select','change_currency()');	//稅率狀態下拉式
$op['back_str'] = "&SCH_num=".$SCH_num."&SCH_supl=".$SCH_supl."&PHP_sr_startno=".$PHP_sr_startno;

$msg = "驗收單  #:".$PHP_apb_num." Revised.";
$log->log_add(0,"040V",$msg);
$op['msg'][] = $msg;

page_display($op, $AUTH, $TPL_APB_MONTH_FTY_EDIT);

break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apb_month_tw_add":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "apb_month_tw_add":
check_authority($AUTH,"add");

$op = $apb->search_month_tw_det($PHP_sup_no,"月結");

$rcv_size = sizeof($op['rcv']);
$chk_cury = $op['rcv'][0]['rcv_det'][0]['currency'];
for($i=0;$i<$rcv_size;$i++){
	$det_size = sizeof($op['rcv'][$i]['rcv_det']);
	for($j=0;$j<$det_size;$j++){
		if($op['rcv'][$i]['rcv_det'][$j]['currency'] <> $chk_cury){
			$op['msg'][] = "以下 PO 幣別不同!!";
			break 2;
		}
	}
}
$op['currency'] =  $arry2->select($CURRENCY,$op['rcv'][0]['rcv_det'][0]['currency'],'PHP_select_currency','select','change_currency()');	//幣別下拉式
$op['rcv_num'] = "AP".date('y')."-xxxx";
$op['date'] = $TODAY;
$op['dept'] = $GLOBALS['SCACHE']['ADMIN']['dept'];
$op['user'] = $GLOBALS['SCACHE']['ADMIN']['login_id'];
$op['dm_way'] = $PHP_payment;
$op['supl'] = $supl->get('',$PHP_sup_no);

$dates = $TODAY;
$op['back_str'] = "&PHP_po=".$PHP_po."&PHP_tw_rcv=".$PHP_tw_rcv."&PHP_payment=".urlencode($PHP_payment)."&PHP_ship=".$PHP_ship."&PHP_ship2=".$PHP_ship2."&PHP_sup=".$PHP_sup."&PHP_sr_startno=".$PHP_sr_startno."&PHP_ord=".$PHP_ord;

page_display($op, $AUTH, $TPL_APB_MONTH_TW_ADD);

break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_apb_month_tw_add": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_apb_month_tw_add":
check_authority($AUTH,"add");

$parm = array (
"sup_no"			=>	$PHP_sup_no,
"rcv_date"			=>	$TODAY,
"rcv_user"			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
"inv_num"			=>	$PHP_inv_num,
"foreign_inv_num"	=>	$PHP_foreign_inv_num,
"inv_date"			=>	$PHP_inv_date,
"inv_price"			=>	$PHP_inv_price,
"dept"				=>	$PHP_dept,
"discount"			=>	$PHP_discount,
"off_duty"			=>	$PHP_off_duty,
"vat"				=>	$PHP_vat,
"currency"			=>	$PHP_select_currency,
"status"			=>	11,
"payment"			=>	$PHP_payment
);

$head="AP".date('y')."-";	//RP+日期=驗收單開頭
$parm['rcv_num'] = $apply->get_no($head,'rcv_num','apb');	//取得驗收的最後編號

$f1 = $apb->add($parm);

if (isset($PHP_ship_num))
{
	foreach ($PHP_ship_num as $rcv_value)
	{
		if(isset($PHP_checkbox[$rcv_value]))
		{
			foreach($PHP_mark[$rcv_value] as $i => $non_val)
			{
				if($PHP_apb_qty[$rcv_value][$i] > 0){
					$parm_det = array(
					"ship_num"		=>	$PHP_ship_num[$rcv_value],
					"rcvd_num"		=>	$rcv_value,
					"receive_det_id"=>	$PHP_rcv_det_id[$rcv_value][$i],
					"inv_num"		=>	$PHP_rcv_inv[$rcv_value][$i],
					"rcv_num"		=>	$parm['rcv_num'],
					"mat_code"		=>	$PHP_mat_code[$rcv_value][$i],
					"mat_id"		=>	$PHP_mat_id[$rcv_value][$i],
					"color"			=>	$PHP_color[$rcv_value][$i],
					"qty"			=>	$PHP_apb_qty[$rcv_value][$i],
					"uprices"		=>	$PHP_uprices[$rcv_value][$i],
					"uprice"		=>	$PHP_uprice[$rcv_value][$i],
					"mat_code"		=>	$PHP_mat_code[$rcv_value][$i],
					"size"			=>	$PHP_size[$rcv_value][$i],
					"foc"			=>	$PHP_foc[$rcv_value][$i],
					"ap_num"		=>	$PHP_ap_num[$rcv_value][$i]
					);

					$det_id = $apb->add_det($parm_det);
					
					#$toler = $PHP_toler[$rcv_value][$i];
					#$apbed_row = $apb->get_apbed_qty($PHP_ap_num[$rcv_value][$i],$PHP_mat_id[$rcv_value][$i],$PHP_color[$rcv_value][$i],$PHP_size[$rcv_value][$i],"11,12","");
					#斷是否該標註apb_rmk
					#$tf1 = $PHP_rcv_qty[$rcv_value][$i] >= ($PHP_po_qty[$rcv_value][$i] * (1-$toler[1]*0.01));
					#$tf2 = $apb_row['qty'] >= ($PHP_po_qty[$rcv_value][$i] * (1-$toler[1]*0.01));
					#$tf3 = $PHP_rcv_qty[$rcv_value][$i] <= ($PHP_po_qty[$rcv_value][$i] * (1-$toler[1]*0.01));
					#$tf4 = $apb_row['qty'] >= $PHP_rcv_qty[$rcv_value][$i];
					#if( ($tf1 and $tf2) or ($tf3 and $tf4) )
                    
                    $apbed_qty = $apb->get_apbed_tw_qty($PHP_ap_num[$rcv_value][$i],$PHP_rcv_det_id[$rcv_value][$i],$PHP_mat_id[$rcv_value][$i],$PHP_color[$rcv_value][$i],$PHP_size[$rcv_value][$i],"");
					$apb->update_field("apb_qty",$apbed_qty, " id = '".$PHP_rcv_det_id[$rcv_value][$i]."' " , "po_ship_det");
					$apb->update_field('apb_rmk', '1', " id = '".$PHP_rcv_det_id[$rcv_value][$i]."' ", "po_ship_det");
					
					$input_rate = $PHP_rate[$rcv_value][$i];
					$s_total=0;
					$tmp=0;
					
					foreach($PHP_link_qty[$rcv_value][$i] as $ord => $link_qty){
						$link_parm = array(
                            "po_id"		=>	$PHP_po_id[$rcv_value][$i][$ord],
                            "rcv_id"	=>	$det_id,
                            "qty"		=>	$link_qty,
                            "currency"	=>	$PHP_select_currency,
                            "amount"	=>	$PHP_uprice[$rcv_value][$i] * $link_qty,
                            "rate"		=>	$input_rate,
                            "ord_num"	=>	$ord
                        );
						$f3=$apb->add_link($link_parm);
						#$f2=$apb->update_apb_qty('apb_qty',$rcv_qty[$j], $id[$j],$table);	
					}

					# 超量備註
					/* if($PHP_reason[$i])
					{
						$parm= array(
						'rcv_num'		=>	$parm['rcv_num'],
						'item'			=>	'Overbalance.',
						'des'				=>	$PHP_reason[$i],
						'user'			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
						'k_date'		=>	$TODAY
						);
						$f4=$apb->add_log($parm);
					} */
				}
			}
		}
	}
}

#標註ap_oth_cost
foreach($oth_cost_id as $val){
	$f1 = $apb->update_field("apb_num", $parm['rcv_num'], "id=$val", 'ap_oth_cost');
}

#加入新增的apb_oth_cost
if(isset($PHP_des)){
	foreach($PHP_des as $po_num => $v1){
		foreach($v1 as $k2 => $v2){
			$oth_parm = array(
                "ap_num"			=>	str_replace("PO ","PA",$po_num),
                "item"				=>	$PHP_des[$po_num][$k2],
                "cost"				=>	$PHP_cost[$po_num][$k2],
                "apb_num"			=>	$parm['rcv_num'],
                "payment_status"	=>	12
            );
			$f0 = $apb->oth_cost_add($oth_parm);
		}
	}
}

$message = "successfully append Apb On : ".$parm['rcv_num'];
$op['msg'][]= $message;
$log->log_add(0,"040A",$message);

redirect_page($PHP_SELF."?PHP_action=apb_month_tw_view&PHP_rcv_num=".$parm['rcv_num']."&SCH_num=&SCH_supl=&PHP_sr_startno=1");
break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apb_month_tw_view":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "apb_month_tw_view":

check_authority($AUTH,"view");

$op = $apb->get_month_tw_apb($PHP_rcv_num);

if (isset($PHP_msg))$op['msg'][]=$PHP_msg;
if(isset($PHP_sr_startno))$op['back_str'] = "&SCH_num=".$SCH_num."&SCH_supl=".$SCH_supl."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_sr_startno=".$PHP_sr_startno;
$op['ck'] = (!empty($_GET['ck']))? $_GET['ck'] : '';

$op['date'] = $TODAY;
$op['submit_ckdate'] = substr($op['apb']['rcv_date'],0,7) < substr($TODAY,0,7) ? '0' : '1' ;
$op['revise_ckdate'] = $op['apb']['rcv_date'] == $TODAY ? '1' : '0' ;
if(!$op['revise_ckdate'] and $op['apb']['status'] == 12){
	$op['msg'][] = "APB僅當天可Revise!";
}

if(!empty($PHP_resize)) $op['resize'] = 1;
page_display($op, $AUTH, $TPL_APB_MONTH_TW_SHOW);

break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apb_month_tw_edit":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "apb_month_tw_edit":
check_authority($AUTH,"view");

$op = $apb->get_month_tw_apb($PHP_rcv_num);

$op['date'] = $TODAY;
$op['back_str'] = "&SCH_num=".$SCH_num."&SCH_supl=".$SCH_supl."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_sr_startno=".$PHP_sr_startno;
$op['currency'] =  $arry2->select($CURRENCY,$op['apb']['currency'],'PHP_select_currency','select','change_currency()');	//幣別下拉式

$dates = $TODAY;
page_display($op, $AUTH, $TPL_APB_MONTH_TW_EDIT);
break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_apb_month_tw_edit":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_apb_month_tw_edit":
check_authority($AUTH,"view");				

$parm = array (	
"rcv_date"			=>	$TODAY,
"rcv_user"			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
"inv_num"			=>	$PHP_inv_num,
"inv_date"			=>	$PHP_inv_date,
"foreign_inv_num"	=>	$PHP_foreign_inv_num,
"inv_price"			=>	$PHP_inv_price,
"discount"			=>	$PHP_discount,
"off_duty"			=>	$PHP_off_duty,
"vat"				=>	$PHP_vat,
"currency"			=>	$PHP_select_currency,
"rcv_num"			=>	$PHP_apb_num
);

$f1 = $apb->edit($parm);
print_r($_POST);
	foreach($PHP_det_id as $rcvd_num => $val){
		if (array_key_exists($rcvd_num, $PHP_checkbox)){ # 更新
			foreach($val as $i => $det_id){
				if(array_key_exists($i, $PHP_mark[$rcvd_num])){
					$f1 = $apb->update_field_id('qty',$PHP_apb_qty[$rcvd_num][$i],$det_id,'apb_det');
					$f1 = $apb->update_field_id('foc',$PHP_foc[$rcvd_num][$i],$det_id,'apb_det');
					$f1 = $apb->update_field_id('uprice',$PHP_uprice[$rcvd_num][$i],$det_id,'apb_det');
					$f1 = $apb->update_field_id('uprices',$PHP_uprices[$rcvd_num][$i],$det_id,'apb_det');
					$f1 = $apb->update_field('currency',$PHP_select_currency,"rcv_id='".$det_id."'",'apb_po_link');
					$f1 = $apb->update_field('rate',$PHP_rate[$rcvd_num][$i],"rcv_id='".$det_id."'",'apb_po_link');
				
					foreach ($PHP_link_qty[$rcvd_num][$i] as $link_id => $link_qty) {
						$f1 = $apb->update_field_id("qty", $link_qty, $link_id, "apb_po_link");
					}
				}else{	//刪除
					$f1 = $apb->del_link($det_id);
					$f1 = $apb->del_det($det_id);
                    $apbed_qty = $apb->get_apbed_tw_qty(str_replace("PO","PA",$PHP_po_num[$rcvd_num][$i]),$PHP_receive_det_id[$rcvd_num][$i],$PHP_mat_id[$rcvd_num][$i],$PHP_color[$rcvd_num][$i],$PHP_size[$rcvd_num][$i],"");
                    $apbed_qty = $apbed_qty > 0 ? $apbed_qty : 0 ;
					$f1 = $apb->update_field("apb_qty",$apbed_qty, " ap_num='".str_replace("PO","PA",$PHP_po_num[$rcvd_num][$i])."' and mat_id='".$PHP_mat_id[$rcvd_num][$i]."' and color='".$PHP_color[$rcvd_num][$i]."' and size='".$PHP_size[$rcvd_num][$i]."'", "po_ship_det");
				}
			}
		}else{ # 刪除
			foreach($val as $i => $det_id){
				$f1 = $apb->del_link($det_id);
				$f1 = $apb->del_det($det_id);
                $apbed_qty = $apb->get_apbed_tw_qty(str_replace("PO","PA",$PHP_po_num[$rcvd_num][$i]),$PHP_receive_det_id[$rcvd_num][$i],$PHP_mat_id[$rcvd_num][$i],$PHP_color[$rcvd_num][$i],$PHP_size[$rcvd_num][$i],"");
                $apbed_qty = $apbed_qty > 0 ? $apbed_qty : 0 ;
                $f1 = $apb->update_field("apb_qty",$apbed_qty, " ap_num='".str_replace("PO","PA",$PHP_po_num[$rcvd_num][$i])."' and mat_id='".$PHP_mat_id[$rcvd_num][$i]."' and color='".$PHP_color[$rcvd_num][$i]."' and size='".$PHP_size[$rcvd_num][$i]."'", "po_ship_det");
			}
		}
		$f2 = $apb->chk_del_full($PHP_apb_num);
	}

if($f2){
	$message = "Delete APB : ".$PHP_rcv_num;
	$log->log_add(0,"040D",$message);
	$message = "Delete APB [".$PHP_apb_num."]";
	$redir_str = "apb.php?PHP_action=rcvd_search".$PHP_back_str."&PHP_msg=".$message;
	redirect_page($redir_str);
}else{
	#修改apb_oth_cost
	foreach($PHP_oth_item as $id => $val){
		$apb->update_field("item", $val, "id=".$id." and apb_num='".$PHP_apb_num."'", "apb_oth_cost");
		$apb->update_field("cost", $PHP_oth_cost[$id], "id=".$id." and apb_num='".$PHP_apb_num."'", "apb_oth_cost");
	}

	#加入新增的apb_oth_cost
	if(isset($PHP_des)){
		foreach($PHP_des as $po_num => $v1){
			foreach($v1 as $k2 => $val){
				$parm = array(
							"ap_num"			=>	str_replace("PO","PA",$po_num),
							"item"				=>	$val,
							"cost"				=>	$PHP_cost[$po_num][$k2],
							"apb_num"			=>	$PHP_apb_num,
							"payment_status"	=>	12
						);
				$f0 = $apb->oth_cost_add($parm);
			}
		}
	}
}

$op = $apb->get_month_tw_apb($PHP_apb_num);
$op['back_str'] = $PHP_back_str;
$message = "Successfully Edit Apb : ".$PHP_apb_num;
$log->log_add(0,"040E",$message);
$op['msg'][]=$message;
$op['ckdate'] = substr($op['apb']['rcv_date'],0,7) < substr($TODAY,0,7) ? '1' : '0' ;
page_display($op, $AUTH, $TPL_APB_MONTH_TW_SHOW);

break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apb_month_tw_submit":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "apb_month_tw_submit":
check_authority($AUTH,"edit");

$f1 = $apb->update_field_id('rcv_sub_user',$GLOBALS['SCACHE']['ADMIN']['login_id'],$PHP_id,'apb');
$f1 = $apb->update_field_id('rcv_sub_date',$TODAY,$PHP_id,'apb');
$f1 = $apb->update_field_id('status',12,$PHP_id,'apb');

if ($PHP_ver == 0)$apb->update_field_id('version',1,$PHP_id,'apb');

$op = $apb->get_month_tw_apb($PHP_apb_num);

$op['back_str'] = $PHP_back_str;
$op['ckdate'] = substr($op['apb']['rcv_date'],0,7) < substr($TODAY,0,7) ? '1' : '0' ;

$message = "Successfully Submitted APB # : ".$PHP_apb_num;
$op['msg'][]="Successfully Submitted APB # : ".$PHP_apb_num;
$log->log_add(0,"040E",$message);

page_display($op, $AUTH, $TPL_APB_MONTH_TW_SHOW);

break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "apb_month_tw_revise":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "apb_month_tw_revise":
check_authority($AUTH,"view");

$f1 = $apb->update_field_id('rcv_sub_user','',$PHP_id,'apb');
$f1 = $apb->update_field_id('rcv_sub_date','',$PHP_id,'apb');
$f1 = $apb->update_field_id('status',11,$PHP_id,'apb');
$f1 = $apb->update_field_id('version',($PHP_ver+1),$PHP_id,'apb');

$op = $apb->get_month_tw_apb($PHP_apb_num);

$op['date'] = $TODAY;
$op['currency'] =  $arry2->select($CURRENCY,$op['apb']['currency'],'PHP_select_currency','select','change_currency()');	//稅率狀態下拉式
$op['back_str'] = "&SCH_num=".$SCH_num."&SCH_supl=".$SCH_supl."&PHP_sr_startno=".$PHP_sr_startno;

$msg = "驗收單  #:".$PHP_apb_num." Revised.";
$log->log_add(0,"040V",$msg);
$op['msg'][] = $msg;

page_display($op, $AUTH, $TPL_APB_MONTH_TW_EDIT);

break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->case "apb_before_after_search"
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "apb_before_after_add":
check_authority($AUTH,"view");

$op = $apb->search_before_after_po($PHP_sup_no, "%|%");

$ap_size = sizeof($op['ap']);
for($i=1;$i<$ap_size;$i++){
	if($op['ap'][$i]['currency'] <> $op['ap'][$i-1]['currency']){
		$op['msg'][] = "以下 PO 幣別不同!!";
	}
}

$op['rcv_num'] = "AP".date('y')."-xxxx";
$op['date'] = $TODAY;
$op['dept'] = $GLOBALS['SCACHE']['ADMIN']['dept'];
$op['user'] = $GLOBALS['SCACHE']['ADMIN']['login_id'];

# 前面顯示
$op['payment'] = $payment;
# 傳遞值
$op['dm_way'] = $PHP_payment;

$op['supl'] = $supl->get('',$PHP_sup_no);
$op['back_str'] = "&SCH_num=".$SCH_num."&SCH_supl=".$SCH_supl."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc;
$op['back_str'] = "&PHP_po=".$PHP_po."&PHP_ship=".$PHP_ship."&PHP_ship2=".$PHP_ship2."&PHP_sup=".$PHP_sup."&PHP_payment=".$PHP_payment."&PHP_ord=".$PHP_ord."&PHP_b_a=".$PHP_b_a."&payment=".urlencode($PHP_payment);
$op['currency'] = $arry2->select($CURRENCY,$op['ap'][0]['currency'],'PHP_select_currency','select','change_currency()');	//幣別下拉式
if(isset($PHP_msg)) $op['msg'][] = $PHP_msg;

page_display($op, $AUTH, $TPL_APB_BEFORE_AFTER_ADD);
break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_apb_before_after_add"
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_apb_before_after_add":
check_authority($AUTH,"add");

$parm = array (
"sup_no"			=>	$PHP_sup_no,
"rcv_date"			=>	$TODAY,
"rcv_user"			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
"inv_num"			=>	$PHP_inv_num,
"foreign_inv_num"	=>	$PHP_foreign_inv_num,
"inv_date"			=>	$PHP_inv_date,
"inv_price"			=>	$PHP_inv_price,
"dept"				=>	$PHP_dept,
"discount"			=>	$PHP_discount,
"off_duty"			=>	$PHP_off_duty,
"vat"				=>	$PHP_vat,
"currency"			=>	$PHP_select_currency,
"status"			=>	13,
"payment"			=>	""
);

$head="AP".date('y')."-";	//RP+日期=驗收單開頭
$parm['rcv_num'] = $apply->get_no($head,'rcv_num','apb');	//取得驗收的最後編號

$f1 = $apb->add($parm);

	$link_row = array();
	foreach ($PHP_po_num as $po_num)
	{
		if(isset($PHP_checkbox[$po_num]))
		{
			foreach($PHP_qty[$po_num] as $i => $qty)
			{
				if($PHP_qty[$po_num][$i] > 0){
					$parm_det = array(
					"ship_num"		=>	'',
					"rcvd_num"		=>	'',
					"inv_num"		=>	'',
					"rcv_num"		=>	$parm['rcv_num'],
					"mat_code"		=>	$PHP_mat_code[$po_num][$i],
					"qty"			=>	$PHP_qty[$po_num][$i],
					"uprices"		=>	$PHP_uprices[$po_num][$i],
					"uprice"		=>	$PHP_uprice[$po_num][$i],
					"color"			=>	$PHP_color[$po_num][$i],
					"foc"			=>	$PHP_foc[$po_num][$i],
					"ap_num"		=>	$PHP_ap_num[$po_num],
					"mat_id"		=>	$PHP_mat_id[$po_num][$i],
					"size"			=>	$PHP_size[$po_num][$i]
					);

					$det_id = $apb->add_det($parm_det);
					
					#備註apb_rmk
					$where_str = "ap_num='".$PHP_ap_num[$po_num]."' and mat_id=".$PHP_mat_id[$po_num][$i]." and color='".$PHP_color[$po_num][$i]."' and size='".$PHP_size[$po_num][$i]."'";
					$apb->update_field('apb_rmk', '1', $where_str, "ap_det");
					
					$input_rate = $PHP_rate[$po_num][$i];
					
					#分攤數量
					$tmp_qty = 0;
					$ap_det_row = $apb->get_same_mat($PHP_ap_num[$po_num],$PHP_mat_id[$po_num][$i],$PHP_color[$po_num][$i],$PHP_size[$po_num][$i],"order by po_qty asc");
					
					//$ttl_po_qty_row = $apb->get_det_field("sum(po_qty) as po_qty","ap_det",$where_str);
					//$ttl_po_qty = $ttl_po_qty_row['po_qty'];
					
					$percent = number_format($PHP_qty[$po_num][$i] / $PHP_po_qty[$po_num][$i],5,'.','');
					
					$link_row = array();
					for ($j=0; $j< sizeof($ap_det_row)-1; $j++)
					{
						$link_row[$j]['mat_cat'] = $ap_det_row[$j]['mat_cat'];
						$link_row[$j]['po_id'] = $ap_det_row[$j]['id'];
						$link_row[$j]['rcv_id'] = $det_id;
						$link_row[$j]['qty'] = number_format($ap_det_row[$j]['po_qty'] * $percent,2,'.','');
						$tmp_qty += $link_row[$j]['qty'];
						$link_row[$j]['currency'] = $PHP_select_currency;
						$link_row[$j]['amount'] = number_format($PHP_uprice[$po_num][$i] * $link_row[$j]['qty'],2,'.','');
						$link_row[$j]['rate'] = $PHP_rate[$po_num][$i];
						$link_row[$j]['ord_num'] = $apb->get_ord_num($link_row[$j]['po_id'],$link_row[$j]['mat_cat']);
					}
						#最後一筆
						$link_row[$j]['mat_cat'] = $ap_det_row[$j]['mat_cat'];
						$link_row[$j]['po_id'] = $ap_det_row[$j]['id'];
						$link_row[$j]['rcv_id'] = $det_id;
						$link_row[$j]['qty'] = $PHP_qty[$po_num][$i] - $tmp_qty;
						$link_row[$j]['currency'] = $PHP_select_currency;
						$link_row[$j]['amount'] = number_format($PHP_uprice[$po_num][$i] * $link_row[$j]['qty'],2,'.','');
						$link_row[$j]['rate'] = $PHP_rate[$po_num][$i];
						$link_row[$j]['ord_num'] = $apb->get_ord_num($link_row[$j]['po_id'],$link_row[$j]['mat_cat']);
					
					#寫入apb_po_link
					for ($j=0; $j< sizeof($link_row); $j++)
					{
						$f3 = $apb->add_link($link_row[$j]);
					}
				}
			}
			#記錄 PO 已付金額
			$i_parm = array("ap_num"	=>	$PHP_ap_num[$po_num],
						  "item"		=>	"",
						  "cost"		=>	$PHP_price[$po_num],
						  "apb_num"		=>	$parm['rcv_num'],
						  "payment_status"	=>	"14"
						  );
			$f1 = $apb->oth_cost_add($i_parm);
			#判斷 ap_det 是否都已付款,是的話就將 ap 備註
			$f1 = $apb->check_full_det_rmk($PHP_ap_num[$po_num]);
			
			# 標註ap_oth_cost
			foreach($ap_oth_cost_id[$po_num] as $key=>$val){
				$f1 = $apb->update_field("apb_num", $parm['rcv_num'], "id=".$val, 'ap_oth_cost');
			}
		}
	}

# 加入apb的其他費用(原採購單可能沒有加入的費用)
foreach ($PHP_des as $po_num=>$val)
{
	if($val){
		$i_parm = array("ap_num"	=>	$PHP_ap_num[$po_num],
						  "item"	=>	$val,
						  "cost"	=>	$PHP_cost[$po_num],
						  "apb_num"	=>	$parm['rcv_num'],
						  "payment_status"	=>	14
						  );
		$f1 = $apb->oth_cost_add($i_parm);
	}
}


$message = "successfully append Apb On : ".$parm['rcv_num'];

$log->log_add(0,"040A",$message);

redirect_page($PHP_SELF."?PHP_action=apb_before_after_view&PHP_rcv_num=".$parm['rcv_num']."&PHP_msg=".$message."&PHP_sr_startno=1");

break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apb_before_after_view: 貨前貨後 的 貨前
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "apb_before_after_view":
check_authority($AUTH,"view");

$op = $apb->get_before_after_apb($PHP_rcv_num);

$pay = explode("|", $op['ap'][0]['dm_way']);
$op['apb']['payment'] = $pay[0]."% TT before, ".$pay[1]."% TT after";

if (isset($PHP_msg))$op['msg'][]=$PHP_msg;
if(isset($PHP_sr_startno))$op['back_str'] = "&PHP_payment=".$op['apb']['payment']."&SCH_num=".$SCH_num."&SCH_supl=".$SCH_supl."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_sr_startno=".$PHP_sr_startno;

$op['date'] = $TODAY;
$op['submit_ckdate'] = substr($op['apb']['rcv_date'],0,7) < substr($TODAY,0,7) ? '0' : '1' ;
$op['revise_ckdate'] = $op['apb']['rcv_date'] == $TODAY ? '1' : '0' ;
if(!$op['revise_ckdate'] and $op['apb']['status'] == 14){
	$op['msg'][] = "APB僅當天可Revise!";
}

if(!empty($PHP_resize)) $op['resize'] = 1;
page_display($op, $AUTH, $TPL_APB_BEFORE_AFTER_SHOW);

break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apb_before_after_edit:	貨前貨後 的 貨前
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "apb_before_after_edit":
check_authority($AUTH,"edit");

$op = $apb->get_before_after_apb($PHP_rcv_num);

if (isset($PHP_msg))$op['msg'][]=$PHP_msg;
if(isset($PHP_sr_startno))$op['back_str'] = "&SCH_num=".$SCH_num."&SCH_supl=".$SCH_supl."&PHP_sr_startno=".$PHP_sr_startno;

$op['date'] = $TODAY;
$op['ckdate'] = substr($op['apb']['rcv_date'],0,7) < substr($TODAY,0,7) ? '1' : '0' ;
$op['currency'] =  $arry2->select($CURRENCY,$op['apb']['currency'],'PHP_select_currency','select','change_currency()');

$pay = explode("|", $op['ap'][0]['dm_way']);
$op['apb']['payment'] = $pay[0]."% TT before, ".$pay[1]."% TT after";

page_display($op, $AUTH, $TPL_APB_BEFORE_AFTER_EDIT);
break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_apb_before_after_edit:
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_apb_before_after_edit":
check_authority($AUTH,"edit");

	foreach($PHP_ap_num as $po_num=>$ap_num){
		if (!array_key_exists($po_num, $PHP_checkbox)){
			# 刪除 apb_oth_cost 中的記錄
			$f1 = $apb->del_apb_cost("apb_num='".$PHP_rcv_num."' and ap_num='".$ap_num."'", "apb_oth_cost");
			# 將 ap_oth_cost 裡 ap_num 的 apb_num 清空
			foreach($ap_oth_cost_id[$po_num] as $ai){
				$f1 = $apb->update_field("apb_num", "", "id=$ai", "ap_oth_cost");
			}
			
			foreach($PHP_det_id[$po_num] as $key=>$det_id){
				# 刪除 apb_po_link 中的記錄
				$f1 = $apb->del_link($det_id);
				# 刪除 apb_det 中的記錄
				$f1 = $apb->del_det($det_id);
			}
			# 修改 ap_det 中的 apb_rmk
			$f1 = $apb->update_field("apb_rmk", 0, "ap_num = '".$ap_num."'", "ap_det");
			$f1 = $apb->chk_del_full($PHP_rcv_num);
		}else{	//修改
			foreach($PHP_qty[$po_num] as $key=>$apb_qty){
				if($apb_qty > 0){
					# 修改 apb_det 資料
					$f1 = $apb->update_field("qty", $apb_qty, "rcv_num='".$PHP_rcv_num."' and ap_num='".$PHP_ap_num[$po_num]."' and mat_id=".$PHP_mat_id[$po_num][$key]." and color='".$PHP_color[$po_num][$key]."' and size='".$PHP_size[$po_num][$key]."'", "apb_det");
					$f1 = $apb->update_field("foc", $PHP_foc[$po_num][$key], "rcv_num='".$PHP_rcv_num."' and ap_num='".$PHP_ap_num[$po_num]."' and mat_id=".$PHP_mat_id[$po_num][$key]." and color='".$PHP_color[$po_num][$key]."' and size='".$PHP_size[$po_num][$key]."'", "apb_det");
					$f1 = $apb->update_field("uprice", $PHP_uprice[$po_num][$key], "rcv_num='".$PHP_rcv_num."' and ap_num='".$PHP_ap_num[$po_num]."' and mat_id=".$PHP_mat_id[$po_num][$key]." and color='".$PHP_color[$po_num][$key]."' and size='".$PHP_size[$po_num][$key]."'", "apb_det");
					$f1 = $apb->update_field("uprices", $PHP_uprices[$po_num][$key], "rcv_num='".$PHP_rcv_num."' and ap_num='".$PHP_ap_num[$po_num]."' and mat_id=".$PHP_mat_id[$po_num][$key]." and color='".$PHP_color[$po_num][$key]."' and size='".$PHP_size[$po_num][$key]."'", "apb_det");
					
					# 判斷是否標註 apb_rmk
					$toler = explode("|",$PHP_toler[$po_num]);
					//$apbed_qty = $apb->get_apbed_qty($PHP_ap_num[$po_num], $PHP_mat_id[$po_num][$key], $PHP_color[$po_num][$key], $PHP_size[$po_num][$key], "0,2", "");
					//if( $apbed_qty * (1-$toler[1]/100) >= $PHP_po_qty[$po_num][$key] )
						$apb->update_field('apb_rmk', '1', "ap_num='".$PHP_ap_num[$po_num]."' and mat_id='".$PHP_mat_id[$po_num][$key]."' and color='".$PHP_color[$po_num][$key]."' and size='".$PHP_size[$po_num][$key]."'", "ap_det");
					//else
						//$apb->update_field('apb_rmk', '0', "ap_num='".$PHP_ap_num[$po_num]."' and mat_id='".$PHP_mat_id[$po_num][$key]."' and color='".$PHP_color[$po_num][$key]."' and size='".$PHP_size[$po_num][$key]."'", "ap_det");
					
					# 取po_qty 並分攤數量
					$po_row = $apb->get_same_mat($PHP_ap_num[$po_num],$PHP_mat_id[$po_num][$key],$PHP_color[$po_num][$key],$PHP_size[$po_num][$key],"order by po_qty asc");
					$po_row_size = sizeof($po_row);
					$ttl_po_qty = $sum_qty = 0;
					
					for($i=0;$i<$po_row_size;$i++){
						$ttl_po_qty += $po_row[$i]['po_qty'];
					}
					
					$percent = number_format($apb_qty / $ttl_po_qty,5,'.','');
					for($i=0;$i<$po_row_size-1;$i++){
						$po_row[$i]['link_qty'] = number_format($percent * $po_row[$i]['po_qty'],2,'.','');
						$sum_qty += $po_row[$i]['link_qty'];
					}
					$po_row[$i]['link_qty'] = $apb_qty - $sum_qty;
					
					# update apb_po_link
					for ($j=0;$j<$po_row_size;$j++){
						$f3 = $apb->update_field("qty", $po_row[$j]['link_qty'], "rcv_id=".$PHP_det_id[$po_num][$key]." and po_id=".$po_row[$j]['id'], "apb_po_link");
						$f3 = $apb->update_field("rate", $PHP_rate[$po_num][$key], "rcv_id=".$PHP_det_id[$po_num][$key]." and po_id=".$po_row[$j]['id'], "apb_po_link");
						$f3 = $apb->update_field("currency", $PHP_select_currency, "rcv_id=".$PHP_det_id[$po_num][$key]." and po_id=".$po_row[$j]['id'], "apb_po_link");
						$f3 = $apb->update_field("amount", $po_row[$j]['link_qty'] * $PHP_uprice[$po_num][$key], "rcv_id=".$PHP_det_id[$po_num][$key]." and po_id=".$po_row[$j]['id'], "apb_po_link");
					}
				}else{
					#刪除 apb_po_link 中的記錄
					$f1 = $apb->del_link($PHP_det_id[$po_num][$key]);
					#刪除 apb_det 中的記錄
					$f1 = $apb->del_det($PHP_det_id[$po_num][$key]);
					#修改 ap_det 中的 apb_rmk
					$f1 = $apb->update_field("apb_rmk", 0, "ap_num='".$PHP_ap_num[$po_num]."' and mat_id=".$PHP_mat_id[$po_num][$key]." and color='".$PHP_color[$po_num][$key]."' and size='".$PHP_size[$po_num][$key]."'", "ap_det");
				}
			}
			#修改PO已付款金額
			$f1 = $apb->update_field('cost', $PHP_price[$po_num], "ap_num='".$PHP_ap_num[$po_num]."' and apb_num='".$PHP_rcv_num."'", "apb_oth_cost");
			
			#判斷ap_oth_cost是否被取消
			foreach($ap_oth_cost_id[$po_num] as $ai_key=>$ai_val){
				if (!array_key_exists($ai_key, $ap_oth_cost)){
					$f1 = $apb->update_field("apb_num", "", "id=$ai_key", "ap_oth_cost");
				}
			}
		}
		$f1 = $apb->chk_det_rmk($ap_num);
	}

$f2 = $apb->chk_del_full($PHP_rcv_num);
if($f2)
{
	$message = "Delete APB : ".$PHP_rcv_num;
	$log->log_add(0,"040D",$message);
	$redir_str = "apb.php?PHP_action=apb&PHP_msg=".$message;
	redirect_page($redir_str);
}else{
	$f1 = $apb->update_field_id('inv_num', $PHP_inv_num, $PHP_id, 'apb');
	$f1 = $apb->update_field_id('foreign_inv_num', $PHP_foreign_inv_num, $PHP_id, 'apb');
	$f1 = $apb->update_field_id('inv_date', $PHP_inv_date, $PHP_id, 'apb');
	$f1 = $apb->update_field_id('currency', $PHP_select_currency, $PHP_id, 'apb');
	$f1 = $apb->update_field_id('inv_price', $PHP_inv_price, $PHP_id, 'apb');
	$f1 = $apb->update_field_id('vat', $PHP_vat, $PHP_id, 'apb');
	$f1 = $apb->update_field_id('off_duty', $PHP_off_duty, $PHP_id, 'apb');
	$f1 = $apb->update_field_id('discount', $PHP_discount, $PHP_id, 'apb');

	foreach($PHP_item as $id => $val){
		$f1 = $apb->update_field_id('item', $val, $id, 'apb_oth_cost');
		$f1 = $apb->update_field_id('cost', $PHP_cost[$id], $id, 'apb_oth_cost');
	}
}

$op['back_str'] = $PHP_back_str;
$message = "Successfully Edit Apb : ".$PHP_rcv_num;
$log->log_add(0,"040E",$message);
$op['msg'][]=$message;

redirect_page($PHP_SELF."?PHP_action=apb_before_after_view&PHP_rcv_num=".$PHP_rcv_num."&PHP_sr_startno=1&PHP_msg=".$message);

break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apb_before_after_submit": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "apb_before_after_submit":
check_authority($AUTH,"edit");

$f1 = $apb->update_field_id('rcv_sub_user',$GLOBALS['SCACHE']['ADMIN']['login_id'],$PHP_id,'apb');
$f1 = $apb->update_field_id('rcv_sub_date',$TODAY,$PHP_id,'apb');
$f1 = $apb->update_field_id('status',14,$PHP_id,'apb');

if ($PHP_ver == 0)$apb->update_field_id('version',1,$PHP_id,'apb');

$op = $apb->get_before_after_apb($PHP_rcv_num);

$op['back_str'] = $PHP_back_str;
$op['ckdate'] = substr($op['apb']['rcv_date'],0,7) < substr($TODAY,0,7) ? '1' : '0' ;

$message = "Successfully Submitted APB # : ".$PHP_rcv_num;
$op['msg'][]="Successfully Submitted APB # : ".$PHP_rcv_num;
$log->log_add(0,"040E",$message);

page_display($op, $AUTH, $TPL_APB_BEFORE_AFTER_SHOW);

break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "apb_before_after_revise":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "apb_before_after_revise":
check_authority($AUTH,"edit");

$f1 = $apb->update_field_id('rcv_sub_user','',$PHP_id,'apb');
$f1 = $apb->update_field_id('rcv_sub_date','',$PHP_id,'apb');
$f1 = $apb->update_field_id('status',13,$PHP_id,'apb');
$f1 = $apb->update_field_id('version',($PHP_ver+1),$PHP_id,'apb');

$op = $apb->get_before_after_apb($PHP_rcv_num);

$op['date'] = $TODAY;
$op['ckdate'] = substr($op['apb']['rcv_date'],0,7) < substr($TODAY,0,7) ? '1' : '0' ;
$op['currency'] =  $arry2->select($CURRENCY,$op['apb']['currency'],'PHP_select_currency','select','change_currency()');

$msg = "驗收單  #:".$PHP_rcv_num." Revised.";
$log->log_add(0,"040V",$msg);
$op['msg'][] = $msg;

page_display($op, $AUTH, $TPL_APB_BEFORE_AFTER_EDIT);

break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apb_after_rcv_add":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "apb_after_rcv_add":
check_authority($AUTH,"view");

$op = $apb->get_before_after_rcv($PHP_sup_no, "%|%");

$toler = explode("|",$op['ap'][0]['dm_way']);
//顯示用
$op['dm_way']  = $toler[0]."% TT before shipment, ".$toler[1]."% TT after shipment";
//寫入資料庫用
$op['payment']  = "%|after";

$op['currency'] =  $arry2->select($CURRENCY,$op['ap'][0]['currency'],'PHP_currency','select','change_currency()');	//幣別下拉式

$op['rcv_num'] = "AP".date('y')."-xxxx";
$op['date'] = $TODAY;
$op['dept'] = $GLOBALS['SCACHE']['ADMIN']['dept'];
$op['user'] = $GLOBALS['SCACHE']['ADMIN']['login_id'];
$op['supl'] = $supl->get('',$PHP_sup_no);

$dates = $TODAY;
$op['back_str'] = "&PHP_po=".$PHP_po."&PHP_ship=".$PHP_ship."&PHP_sup=".$PHP_sup."&PHP_sr_startno=".$PHP_sr_startno;
page_display($op, $AUTH, $TPL_APB_AFTER_RCV_ADD);

break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "apb_payemnt_show":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "apb_payemnt_show":
check_authority($AUTH,"view");

$op['apb'] = $apb->get_apb_payment($PHP_ap_num,$PHP_status);

page_display($op, $AUTH, $TPL_APB_PAY_SHOW);
break;	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_apb_after_rcv_add": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_apb_after_rcv_add":
check_authority($AUTH,"add");

if (!isset($PHP_inv_price)) {
	$message = $op['msg'][] = "Please input Receiving Q'TY";
	redirect_page($PHP_SELF."?PHP_action=rcvd_add&PHP_aply_num=".$PHP_aply_num."&PHP_msg=".$message);
	break;
}

$parm = array (
"sup_no"			=>	$PHP_sup_no,
"rcv_date"			=>	$TODAY,
"rcv_user"			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
"inv_num"			=>	$PHP_inv_num,
"foreign_inv_num"	=>	$PHP_foreign_inv_num,
"inv_date"			=>	$PHP_inv_date,
"inv_price"			=>	$PHP_inv_price,
"dept"				=>	$PHP_dept,
"discount"			=>	$PHP_discount,
"off_duty"			=>	$PHP_off_duty,
"vat"				=>	$PHP_vat,
"currency"			=>	$PHP_currency,
"status"			=>	15,
"payment"			=>	$PHP_payment
);

$head="AP".date('y')."-";	//RP+日期=驗收單開頭
$parm['rcv_num'] = $apply->get_no($head,'rcv_num','apb');	//取得驗收的最後編號

$f1 = $apb->add($parm);

if (isset($PHP_checkbox))
{
	foreach ($PHP_checkbox as $po_num => $val)
	{
		$oth_parm = array("ap_num"=>$PHP_ap_num[$po_num],"item"=>'',"cost"=>$PHP_price[$po_num],"apb_num"=>$parm['rcv_num'],"payment_status"=>16);
		$apb->oth_cost_add($oth_parm);
		foreach($PHP_mat_code[$po_num] as $k1 => $v1)
		{
			if($PHP_apb_qty[$po_num][$k1] > 0){
				$parm_det = array(
					"ship_num"		=>	$PHP_ship_num[$po_num][$k1],
					"rcvd_num"		=>	'',
					"inv_num"		=>	$PHP_rcv_inv[$po_num][$k1],
					"rcv_num"		=>	$parm['rcv_num'],
					"mat_code"		=>	$v1,
					"mat_id"		=>	$PHP_mat_id[$po_num][$k1],
					"color"			=>	$PHP_color[$po_num][$k1],
					"size"			=>	$PHP_size[$po_num][$k1],
					"qty"			=>	$PHP_apb_qty[$po_num][$k1],
					"uprices"		=>	$PHP_uprices[$po_num][$k1],
					"uprice"		=>	$PHP_uprice[$po_num][$k1],
					"foc"			=>	$PHP_foc[$po_num][$k1],
					"ap_num"		=>	$PHP_ap_num[$po_num]
					);

					$det_id = $apb->add_det($parm_det);
					$apb->update_field('apb_num', $parm['rcv_num'], "type='i' and po_num='".$po_num."' and mat_id='".$PHP_mat_id[$po_num][$k1]."' and color='".$PHP_color[$po_num][$k1]."' and size='".$PHP_size[$po_num][$k1]."'", "stock_inventory");
					
					foreach($PHP_link_qty[$po_num][$k1] as $ord_num => $link_qty)
					{
						$link_parm = array(
										"po_id"		=>	$PHP_po_id[$po_num][$k1][$ord_num],
										"rcv_id"	=>	$det_id,
										"qty"		=>	$link_qty,
										"currency"	=>	$PHP_currency,
										"amount"	=>	number_format($link_qty*$PHP_uprice[$po_num][$k1],2,'',''),
										"rate"		=>	$PHP_rate[$po_num][$k1],
										"ord_num"	=>	$ord_num
									);
						$f3=$apb->add_link($link_parm);
					}
			}
		}
		# 標註ap_oth_cost
		foreach($PHP_ap_oth_cost_id[$po_num] as $oth_id => $val){
			$f1 = $apb->update_field("apb_num", $parm['rcv_num'], "id=$val", 'ap_oth_cost');
		}
		
		# 標註ap的after_apb
		if($after_apb_flag[$po_num] == 1){
			$apb->update_field('after_apb', $parm['rcv_num'], "po_num = '".$po_num."'", "ap");
		}
	}
}

#加入新增的apb_oth_cost
if(isset($PHP_des)){
	foreach($PHP_des as $po_num => $val){
		if($v1)
			$oth_cost_parm = array(
								"ap_num"	=>	str_replace("PO","PA",$po_num),
								"item"		=>	$val,
								"cost"		=>	$PHP_cost[$po_num],
								"apb_num"	=>	$parm['rcv_num'],
								"payment_status"	=>	16
							);
			$f0 = $apb->oth_cost_add($oth_cost_parm);
	}
}

$message = "successfully append APB : ".$parm['rcv_num'];
$op['msg'][]= $message;
$log->log_add(0,"040A",$message);

redirect_page($PHP_SELF."?PHP_action=apb_after_rcv_view&PHP_rcv_num=".$parm['rcv_num']."&PHP_sr_startno=1");
break;	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apb_after_rcv_view":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "apb_after_rcv_view":

check_authority($AUTH,"view");

$op = $apb->get_rcv_after_apb($PHP_rcv_num);

if (isset($PHP_msg))$op['msg'][]=$PHP_msg;
if(isset($PHP_sr_startno))$op['back_str'] = "&SCH_num=".$SCH_num."&SCH_supl=".$SCH_supl."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_sr_startno=".$PHP_sr_startno;
$op['ck'] = (!empty($_GET['ck']))? $_GET['ck'] : '';

$op['date'] = $TODAY;
$op['submit_ckdate'] = substr($op['apb']['rcv_date'],0,7) < substr($TODAY,0,7) ? '0' : '1' ;
$op['revise_ckdate'] = $op['apb']['rcv_date'] == $TODAY ? '1' : '0' ;
if(!$op['revise_ckdate'] and $op['apb']['status'] == 16){
	$op['msg'][] = "APB僅當天可Revise!";
}

if(!empty($PHP_resize)) $op['resize'] = 1;
page_display($op, $AUTH, $TPL_APB_AFTER_RCV_SHOW);

break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "apb_after_rcv_edit": JOB31
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "apb_after_rcv_edit":
check_authority($AUTH,"edit");

$op = $apb->get_rcv_after_apb($PHP_rcv_num);

/* foreach($op['po'] as $key_po => $val_po)
{
	$tmpap = array();
	$tmpap =$apb->get_array("distinct rcv_num","apb_det"," ap_num = '".$val_po['ap_num']."' AND rcvd_num <>  ''"); 
	$before_price = $apb->get_det_field("cost","apb_oth_cost","ap_num='".$val_po['ap_num']."' and item='before|%'");
	foreach($tmpap as $key_tmpap => $val_tmpap)
	{
		$op['po'][$key_po]['all_ap_num'][$key_tmpap] = $val_tmpap['rcv_num'];
	}
		
	foreach($op['po'][$key_po]['all_ap_num'] as $key_ap => $val_ap)
	{
		$ap_info = $apb->get_array("ship_num,rcvd_num,rcv_num,po_id,qty,uprice,ap_num","apb_det"," ap_num = '".$val_po['ap_num']."' AND rcv_num = '".$val_ap."' AND rcvd_num <>  ''");
		$oth_cost = $apb->get_det_field("cost","apb_oth_cost","ap_num='".$val_po['ap_num']."' and apb_num='".$val_ap."'");
		$total = 0;
		foreach($ap_info as $key_getTotal => $val_getTotal)
		{
			if($val_getTotal['rcv_num'] <> $op['apb']['rcv_num'])
			{
				$qty = $val_getTotal['qty'];
				$uprice = $val_getTotal['uprice'];
				$total += (round($qty,2) * round($uprice,2)) ;
			}
		}
		if($oth_cost['cost'] < $total )
		{
			if(($total - $oth_cost['cost']) <= $before_price['cost'] )
			{
				$before_price['cost'] = round($before_price['cost'],2) - (round($total,2) - round($oth_cost['cost'],2));
			}
			else
			{
				$before_price['cost'] = 0;
			}
		}
	}
	$op['po'][$key_po]['before_price'] = $before_price['cost'] ;
} */

$toler = explode("|",$op['po'][0]['dm_way']);
$op['apb']['payment'] = $toler[0]."% TT before shipment, ".$toler[1]."% TT after shipment";

$op['date'] = $TODAY;
$op['currency'] = $arry2->select($CURRENCY,$op['apb']['currency'],'PHP_select_currency','select','change_currency()');	//幣別下拉式
$op['back_str'] = "&SCH_num=".$SCH_num."&SCH_supl=".$SCH_supl."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_sr_startno=".$PHP_sr_startno;

$dates = $TODAY;
page_display($op, $AUTH, $TPL_APB_AFTER_RCV_EDIT);
break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_apb_after_rcv_edit":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_apb_after_rcv_edit":

check_authority($AUTH,"add");				

$parm = array (	
"rcv_date"			=>	$TODAY,
"rcv_user"			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
"inv_num"			=>	$PHP_inv_num,
"inv_date"			=>	$PHP_inv_date,
"foreign_inv_num"	=>	$PHP_foreign_inv_num,
"inv_price"			=>	$PHP_inv_price,
"discount"			=>	$PHP_discount,
"off_duty"			=>	$PHP_off_duty,
"vat"				=>	$PHP_vat,
"currency"			=>	$PHP_select_currency,
"rcv_num"			=>	$PHP_apb_num
);

$f1 = $apb->edit($parm);

	foreach($PHP_det_id as $po_num => $val){
		if (array_key_exists($po_num, $PHP_checkbox)){ # 更新
			foreach($val as $i => $det_id){
				if(array_key_exists($i, $PHP_mark[$po_num])){
					$f1 = $apb->update_field_id('qty',$PHP_apb_qty[$po_num][$i],$det_id,'apb_det');
					$f1 = $apb->update_field_id('foc',$PHP_foc[$po_num][$i],$det_id,'apb_det');
					$f1 = $apb->update_field_id('uprice',$PHP_uprice[$po_num][$i],$det_id,'apb_det');
					$f1 = $apb->update_field_id('uprices',$PHP_uprices[$po_num][$i],$det_id,'apb_det');
					$f1 = $apb->update_field('currency',$PHP_select_currency,"rcv_id='".$det_id."'",'apb_po_link');
					$f1 = $apb->update_field('rate',$PHP_rate[$po_num][$i],"rcv_id='".$det_id."'",'apb_po_link');
				
					foreach ($PHP_link_qty[$po_num][$i] as $link_id => $link_qty) {
						$f1 = $apb->update_field_id("qty", $link_qty, $link_id, "apb_po_link");
					}
				}else{	//刪除
					$f1 = $apb->del_link($det_id);
					$f1 = $apb->del_det($det_id);
					$f1 = $apb->update_field("apb_num", "", "type = 'i' and po_num='".$po_num."' and mat_id='".$PHP_mat_id[$po_num][$i]."' and color='".$PHP_color[$po_num][$i]."' and size='".$PHP_size[$po_num][$i]."'", "stock_inventory");
				}
			}
			$apb->update_field("cost", $PHP_checkbox[$po_num], "ap_num='".str_replace("PO","PA",$po_num)."' and apb_num='".$PHP_apb_num."'", "apb_oth_cost");
			if(!$PHP_after_apb_flag[$po_num])
				$apb->chk_after_apb($po_num, $PHP_apb_num, "ap");
		}else{ # 刪除
			foreach($val as $i => $det_id){
				$f1 = $apb->del_link($det_id);
				$f1 = $apb->del_det($det_id);
				$f1 = $apb->update_field("apb_num", "", "type='i' and po_num='".$po_num."' and mat_id='".$PHP_mat_id[$po_num][$i]."' and color='".$PHP_color[$po_num][$i]."' and size='".$PHP_size[$po_num][$i]."'", "stock_inventory");
			}
			$apb->del_apb_cost("apb_num='".$PHP_apb_num."' and ap_num='".str_replace("PO","PA",$po_num)."'", "apb_oth_cost");
			$apb->chk_after_apb($po_num, $PHP_apb_num, "ap");
		}
		$f2 = $apb->chk_del_full($PHP_apb_num);
	}

if($f2){
	$message = "Delete APB : ".$PHP_apb_num;
	$log->log_add(0,"040D",$message);
	$redir_str = "apb.php?PHP_action=rcvd_search".$PHP_back_str."&PHP_msg=".$message;
	redirect_page($redir_str);
}else{
	#修改apb_oth_cost
	foreach($PHP_oth_cost as $id => $cost){
		if($cost > 0){
			$apb->update_field("item", $PHP_oth_item[$id], "id=".$id." and apb_num='".$PHP_apb_num."'", "apb_oth_cost");
			$apb->update_field("cost", $cost, "id=".$id." and apb_num='".$PHP_apb_num."'", "apb_oth_cost");
		}
	}

	#加入新增的apb_oth_cost
	if(isset($PHP_des)){
		foreach($PHP_cost as $po_num => $v1){
			foreach($v1 as $k2 => $cost){
				if($cost > 0){
					$parm = array(
								"ap_num"			=>	str_replace("PO","PA",$po_num),
								"item"				=>	$PHP_des[$po_num][$k2],
								"cost"				=>	$cost,
								"apb_num"			=>	$PHP_apb_num,
								"payment_status"	=>	16
							);
					$f0 = $apb->oth_cost_add($parm);
				}
			}
		}
	}
}

$op = $apb->get_rcv_after_apb($PHP_apb_num);
$op['back_str'] = $PHP_back_str;
$message = "Successfully Edit Apb : ".$PHP_apb_num;
$log->log_add(0,"040E",$message);
$op['msg'][]=$message;
$op['submit_ckdate'] = substr($op['apb']['rcv_date'],0,7) == substr($TODAY,0,7) ? '1' : '0' ;

page_display($op, $AUTH, $TPL_APB_AFTER_RCV_SHOW);

break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apb_after_rcv_submit":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "apb_after_rcv_submit":
check_authority($AUTH,"edit");

$f1 = $apb->update_field_id('rcv_sub_user',$GLOBALS['SCACHE']['ADMIN']['login_id'],$PHP_id,'apb');
$f1 = $apb->update_field_id('rcv_sub_date',$TODAY,$PHP_id,'apb');
$f1 = $apb->update_field_id('status',16,$PHP_id,'apb');

if ($PHP_ver == 0)$apb->update_field_id('version',1,$PHP_id,'apb');

$op = $apb->get_rcv_after_apb($PHP_apb_num);

$op['back_str'] = $PHP_back_str;
$op['ckdate'] = substr($op['apb']['rcv_date'],0,7) < substr($TODAY,0,7) ? '1' : '0' ;

$message = "Successfully Submitted APB # : ".$PHP_apb_num;
$op['msg'][] = $message;
$log->log_add(0,"040E",$message);

page_display($op, $AUTH, $TPL_APB_AFTER_RCV_SHOW);

break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "apb_after_rcv_revise":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "apb_after_rcv_revise":
check_authority($AUTH,"edit");

$f1 = $apb->update_field_id('rcv_sub_user','',$PHP_id,'apb');
$f1 = $apb->update_field_id('rcv_sub_date','',$PHP_id,'apb');
$f1 = $apb->update_field_id('status',15,$PHP_id,'apb');
$f1 = $apb->update_field_id('version',($PHP_ver+1),$PHP_id,'apb');

$op = $apb->get_rcv_after_apb($PHP_apb_num);

$op['date'] = $TODAY;
$op['currency'] =  $arry2->select($CURRENCY,$op['apb']['currency'],'PHP_select_currency','select','change_currency()');	//稅率狀態下拉式
$op['back_str'] = "&SCH_num=".$SCH_num."&SCH_supl=".$SCH_supl."&PHP_sr_startno=".$PHP_sr_startno;

$msg = "驗收付款單 #:".$PHP_apb_num." Revised.";
$log->log_add(0,"040V",$msg);
$op['msg'][] = $msg;

page_display($op, $AUTH, $TPL_APB_AFTER_RCV_EDIT);


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "apb_after_print":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "apb_after_print":
check_authority($AUTH,"view");

$op = $apb->get_after_apb($PHP_rcv_num);

print_r($op);
exit;

$submit_user=$user->get(0,$op['apb']['rcv_sub_user']);
if ($submit_user['name'])$op['apb']['rcv_sub_user'] = $submit_user['name'];
$create_user=$user->get(0,$op['apb']['rcv_user']);
if ($create_user['name'])$op['apb']['rcv_user'] = $create_user['name'];

//請購單位
$where_str = " WHERE dept_code = '".$op['ap'][0]['dept']."'";
$po_dept_name = $dept->get_fields('dept_name',$where_str);

# 將同張 P/O# 的 ord_num 歸類
$po_ary = array();

foreach($op['apb_det'] as $key1 => $val1){
	foreach($val1["det"] as $key2 => $val2){
		$po_ary[$val2['po_num']]['po_num'] = $val2['po_num'];
		foreach($val2['order'] as $key3=>$val3){
            if (!in_array($val3,$po_ary[$val2['po_num']]['ord']))
                $po_ary[$val2['po_num']]['ord'][] = $val3;
		}
	}
}

#請購單位
$dt = $apb->get_dept_code(substr($op['apb_det'][0]['det'][0]['order'][0],0,1));
$where_str = " WHERE dept_code = '".$dt."'";
$dept_name = $dept->get_fields('dept_name',$where_str);

$ary_title = array (
'apb_num'			=>	$op['apb']['rcv_num'],
'apb_dept'	        =>	mb_substr($dept_name[0],0,3,"big5")=="貿易業" ? "貿易業務" : $dept_name[0],	#申請單位
'dept_code'	        =>	$apb->get_apb_dept($dt),            #單位代號
'apb_date'	        =>	$TODAY,			                    #申請日期
'supplier'	        =>	$op['apb']['f_name'],				#受款人
'uni_no'	        =>	$op['apb']['uni_no'],				#統一編號
'inv_num'	        =>	$op['apb']['inv_num'],				#統一發票
'inv_date'	        =>	$op['apb']['inv_date'],				#發票日期
'rcv_user'          =>  $op['apb']['rcv_user'],				#經辦
'rcv_user_id'       =>  $submit_user['emp_id'],				#經辦工號
'foreign_inv_num'   =>	$op['apb']['foreign_inv_num'],      #INVOICE #
);

include_once($config['root_dir']."/lib/class.pdf_apb.php");

$print_title = "驗 收 付 款 單";
$print_title2 = "VER.".$op['apb']['version'];
$creator = $op['apb']['rcv_user'];
$mark = $op['apb']['rcv_num'];

$pdf=new PDF_rcvd('L','mm','A4');
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();
$pdf->SetAutoPageBreak(1);
$pdf->hend_title($ary_title);

$pdf->SetFont('Big5','',10);

$pdf->ln();
$pdf->SetFont('big5','B',8);
$pdf->Cell(8,7,'項目',1,0,'C');
$pdf->Cell(20,7,'編號',1,0,'C');
$pdf->Cell(90,7,'品名 / 規格',1,0,'C');
$pdf->Cell(45,7,'顏色',1,0,'C');
$pdf->Cell(20,7,'請購單號碼',1,0,'C');
$pdf->Cell(10,7,'單位',1,0,'C');
$pdf->Cell(15,7,'請購數量',1,0,'C');
$pdf->Cell(15,7,'FOC',1,0,'C');
$pdf->Cell(15,7,'請款數量',1,0,'C');
$pdf->Cell(15,7,'單價',1,0,'C');
$pdf->Cell(25,7,'實付總價',1,0,'C');
$pdf->ln();

$no = '1' ;
$uprice = $qty = $foc = $po_qty = $amount = 0;
$pdf->SetFont('Big5','',10);
foreach($op['apb_det'] as $key => $val) {
	foreach($val["det"] as $key2 => $val2) {
        if($no != 1)$pdf->ln();
		if ( ( $pdf->getY() + 6 ) >= 190 )$pdf->AddPage();

		if (substr($val2['mat_code'],0,1) =='A') {
			$val2['sname'] = $acc->get( '',$val2['mat_code']);
			$note = $val2['sname']['des'];
		} else {
			$val2['sname'] = $lots->get( '',$val2['mat_code']);
			$note = $val2['sname']['comp'];
		}
		
        $height = 6;
		
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(8,$height,$no,1,0,'C');
		$pdf->Cell(20,$height,$val2['mat_code'],1,0,'C');
		$pdf->SetFont('big5','',8);

		$o_y = $pdf->getY();
		$pdf->Cell(90,$height,substr($val2['sname'][2]." / ".($note),0,55),1,0,'L');
		
		$pdf->setY($o_y);
		$pdf->setX(128);
		$pdf->SetFont('big5','',6);
		$pdf->Cell(45,$height,$val2['color'],1,0,'C');
		$pdf->SetFont('Arial','',8);
		
		$pdf->Cell(20,$height,$val2['po_num'],1,0,'C');
		$pdf->Cell(10,$height,$val2['po']['po_unit'],1,0,'C');
		$pdf->Cell(15,$height,number_format($val2['po']['po_qty'], 2, '.',','),1,0,'R');
		//$pdf->Cell(13,$height,$val['ord_num'][0]['receive_time'],1,0,'C');
		$pdf->Cell(15,$height,$val2['foc'],1,0,'R');
		$pdf->Cell(15,$height,number_format($val2['qty'], 2, '.',','),1,0,'R');
		$pdf->Cell(15,$height,number_format($val2['uprice'], 2, '.',','),1,0,'R');
		if ($no==1)
			$pdf->Cell(25,$height,$op['apb']['currency']." $".number_format($val2['uprice']*$val2['qty'], 2, '.',','),1,0,'R');
		else
			$pdf->Cell(25,$height,number_format($val2['uprice']*$val2['qty'], 2, '.',','),1,0,'R');
		$no++;
        
		$amount += number_format($val2['uprice']*$val2['qty'], 2, '.','');
        $qty += $val2['qty'];
        $uprice += $val2['uprice'];
        $foc += $val2['foc'];
        $po_qty += $val2['po_qty'];
        
	}
}

$oth_count = 0;
for($j=0;$j<sizeof($op['apb_oth_cost']);$j++){
	$pdf->ln();
	$pdf->SetFont('Arial','',8);
	$pdf->Cell(8,$height,$no,1,0,'C');
	$pdf->SetFont('big5','',8);
	$pdf->Cell(20,$height,'其他費用',1,0,'C');
	$o_y = $pdf->getY();
	$pdf->Cell(225,$height,$op['apb_oth_cost'][$j]['item'],1,0,'L');
	$pdf->SetFont('Arial','',8);
	$pdf->Cell(25,$height,number_format($op['apb_oth_cost'][$j]['cost'], 2, '.',','),1,0,'R');    
	
	$oth_count += $op['apb_oth_cost'][$j]['cost'];
	$no++;
}

for($j=0;$j<sizeof($op['ap_oth_cost']);$j++){
	$pdf->ln();
	$pdf->SetFont('Arial','',8);
	$pdf->Cell(8,$height,$no,1,0,'C');
	$pdf->SetFont('big5','',8);
	$pdf->Cell(20,$height,'其他費用',1,0,'C');
	$o_y = $pdf->getY();
	$pdf->Cell(225,$height,$op['ap_oth_cost'][$j]['item'],1,0,'L');
	$pdf->SetFont('Arial','',8);
	$pdf->Cell(25,$height,number_format($op['ap_oth_cost'][$j]['cost'], 2, '.',','),1,0,'R');    
	
	$oth_count += $op['ap_oth_cost'][$j]['cost'];
	$no++;
}



$pdf->ln();
# 粗線
$y=$pdf->GetY();
$pdf->SetLineWidth(0.5);
$pdf->Line(10,$pdf->GetY(),288,$pdf->GetY());
$pdf->SetLineWidth(0.1);
$pdf->SetY($y);

$pdf->SetFont('big5','B',8);
$pdf->Cell(208,6,'合 計',1,0,'R');
$pdf->SetFont('Arial','B',8);

$pdf->Cell(15,6,number_format($foc, 2, '.',','),1,0,'R');
$pdf->Cell(15,6,number_format($qty, 2, '.',','),1,0,'R');
$pdf->Cell(15,6,number_format($uprice/($no-1), 2, '.',','),1,0,'R');

if($op['apb']['currency']=='NTD')
	$pdf->Cell(25,6,number_format(round($amount+$oth_count), 0, '',','),1,0,'R');
else
	$pdf->Cell(25,6,number_format($amount+$oth_count, 2, '.',','),1,0,'R');

$pdf->ln();
$pdf->SetFont('big5','B',8);
$pdf->Cell(223,6,'',0,0,'C');
$pdf->Cell(15,6,'營業稅',1,0,'C');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(40,6,number_format($op['apb']['vat'], 2, '.',','),1,0,'R');
$pdf->ln();

$pdf->SetFont('big5','B',8);
$pdf->Cell(223,6,'',0,0,'L');
$pdf->Cell(15,6,'折讓',1,0,'C');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(40,6,($op['apb']['discount'] != 0)?'-'.number_format($op['apb']['discount'], 2, '.',','):'0.00',1,0,'R');
$pdf->ln();

$pdf->SetFont('big5','B',8);
$pdf->Cell(223,6,'',0,0,'L');
$pdf->Cell(15,6,'折稅',1,0,'C');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(40,6,($op['apb']['off_duty'] != 0)?'-'.number_format($op['apb']['off_duty'], 2, '.',','):'0.00',1,0,'R');
$pdf->ln();

$pdf->SetFont('big5','B',8);
$pdf->Cell(223,6,'',0,0,'L');
$pdf->Cell(15,6,'調整稅額',1,0,'C');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(40,6,$op['apb']['adjust_amt'],1,0,'R');
$pdf->ln();

$pdf->SetFont('big5','B',8);
$pdf->Cell(223,6,'',0,0,'L');
$pdf->Cell(15,6,'總合計',1,0,'C');
$pdf->SetFont('Arial','',10);

if($op['apb']['currency']=='NTD')
	$pdf->Cell(40,6,$op['apb']['currency']." $".number_format(round($op['apb']['inv_price']+$op['apb']['adjust_amt']), 0, '',','),1,0,'R');
else
	$pdf->Cell(40,6,$op['apb']['currency']." $".number_format($op['apb']['inv_price']+$op['apb']['adjust_amt'], 2, '.',','),1,0,'R');

$pdf->ln();

if ( ( $pdf->getY() + 50 ) >= 190 )$pdf->AddPage();
######################################################
$pdf->ln();
$pdf->SetFont('big5','B',8);
$pdf->Cell(278,7,'備      註',1,0,'L');
$pdf->SetFont('big5','',8);
$pdf->ln();

$pdf->Cell(278,7,'付款方式：'.$op['apb']['payment'],'LR','TLR','L');
$pdf->ln();

foreach($po_ary as $key => $val){
	$pdf->Cell(15,5,$val['po_num']."： ",'L',0,'L');
	// $pdf->Cell(20,5," [ ".$val['pay_price']." ] ",0,0,'L');
	$str_ord = ' ';
	foreach($val['ord'] as $ordnum){
		$str_ord.=$ordnum."、";
	}
	$str_ord = substr($str_ord,0,-1);
	$pdf->Cell(263,5,$str_ord,'R',0,'L');
	$pdf->ln();
}

for($x=0;$x<sizeof($op['rcv_log']);$x++){
	if ( $pdf->getY() >= 170 )//換頁
		$pdf->AddPage();
	$pdf->Cell(278,5,$op['rcv_log'][$x]['des'],'LR',0,'L');
	$pdf->ln();
}

$y=$pdf->GetY();
//$pdf->ln();
$pdf->SetLineWidth(0.5);
$pdf->Line(10,$pdf->GetY(),288,$pdf->GetY());
$pdf->SetLineWidth(0.1);
$pdf->SetY($y);

//$pdf->ln();

$ch_price = $apb->num2chinese(number_format($op['apb']['inv_price']+$op['apb']['adjust_amt'],2,'.',''));
$pdf->SetFont('big5','B',10);
//$pdf->Cell(278,7,"新台幣 / 美金：".$ch_price[9]." \t仟\t".$ch_price[8]." \t佰\t".$ch_price[7]." \t拾\t".$ch_price[6]." \t萬\t".$ch_price[5]." \t仟\t"
//						.$ch_price[4]." \t佰\t".$ch_price[3]." \t拾\t".$ch_price[2]." \t元\t".$ch_price[1]." \t角\t".$ch_price[0]." \t分\t",1,0,'L');

$pdf->SetFont('big5','B',8);
$pdf->Cell(15,7,$op['apb']['currency']."：",'LTB',0,'L');
$pdf->Cell(17,7,$ch_price[9],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"仟",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[8],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"佰",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[7],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"拾",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[6],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"萬",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[5],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"仟",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[4],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"佰",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[3],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"拾",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[2],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"元",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[1],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"角",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[0],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(12,7,"分 整",'TBR',0,'C');

$pdf->ln();
$pdf->ln();

$pdf->SetFont('big5','B',10);
$pdf->Cell(118,7,"核准：",0,0,'R');
$pdf->Cell(21,7,"",0,0,'L');
$pdf->Cell(21,7,"覆核：",0,0,'R');
$pdf->Cell(21,7,"",0,0,'L');
$pdf->Cell(21,7,"主管：",0,0,'R');
$pdf->Cell(21,7,"",0,0,'L');
$pdf->Cell(21,7,"經辦：",0,0,'R');
$pdf->Cell(21,7,$op['apb']['rcv_user']." ".$submit_user['emp_id'],0,0,'L');

$pdf->SetFont('Arial','',8);
$pdf->SetX();
$pdf->SetY(195);
$pdf->Cell(280,7,"Print ".$GLOBALS['THIS_TIME'],0,0,'R');


$pdf->SetY($oy);
$pdf->SetFont('big5','B',8);
for($s=0;$s<sizeof($op['apb_log']);$s++){
	$pdf->SetX(20);
	$pdf->MultiCell(230,6,($s+1).".".$op['apb_log'][$s]['des'],0,'L',0);
}

$name=$PHP_rcv_num.'.pdf';
$pdf->Output($name,'D');

page_display($op, $AUTH, $TPL_APB_SHOW);
break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "apb_lc_print":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "apb_lc_print":
check_authority($AUTH,"view");

$op = $apb->get_lc_apb($PHP_rcv_num);

$submit_user=$user->get(0,$op['apb']['rcv_sub_user']);
if ($submit_user['name'])$op['apb']['rcv_sub_user'] = $submit_user['name'];
$create_user=$user->get(0,$op['apb']['rcv_user']);
if ($create_user['name'])$op['apb']['rcv_user'] = $create_user['name'];

//請購單位
$where_str = " WHERE dept_code = '".$op['ap'][0]['dept']."'";
$po_dept_name = $dept->get_fields('dept_name',$where_str);

# 將同張 P/O# 的 ord_num 歸類
$po_ary = array();

foreach($op['apb_det'] as $key1 => $val1){
	foreach($val1["det"] as $key2 => $val2){
		$po_ary[$val2['po_num']]['po_num'] = $val2['po_num'];
		foreach($val2['order'] as $key3=>$val3){
            if (!in_array($val3,$po_ary[$val2['po_num']]['ord']))
                $po_ary[$val2['po_num']]['ord'][] = $val3;
		}
	}
}

#請購單位
$dt = $apb->get_dept_code(substr($op['apb_det'][0]['det'][0]['order'][0],0,1));
$where_str = " WHERE dept_code = '".$dt."'";
$dept_name = $dept->get_fields('dept_name',$where_str);

$ary_title = array (
'apb_num'			=>	$op['apb']['rcv_num'],
'apb_dept'	        =>	mb_substr($dept_name[0],0,3,"big5")=="貿易業" ? "貿易業務" : $dept_name[0],	#申請單位
'dept_code'	        =>	$apb->get_apb_dept($dt),            #單位代號
'apb_date'	        =>	$TODAY,			                    #申請日期
'supplier'	        =>	$op['apb']['f_name'],				#受款人
'uni_no'	        =>	$op['apb']['uni_no'],				#統一編號
'inv_num'	        =>	$op['apb']['inv_num'],				#統一發票
'inv_date'	        =>	$op['apb']['inv_date'],				#發票日期
'rcv_user'          =>  $op['apb']['rcv_user'],				#經辦
'rcv_user_id'       =>  $submit_user['emp_id'],				#經辦工號
'foreign_inv_num'   =>	$op['apb']['foreign_inv_num'],      #INVOICE #
);

include_once($config['root_dir']."/lib/class.pdf_apb.php");

$print_title = "驗 收 付 款 單";
$print_title2 = "VER.".$op['apb']['version'];
$creator = $op['apb']['rcv_user'];
$mark = $op['apb']['rcv_num'];

$pdf=new PDF_rcvd('L','mm','A4');
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();
$pdf->SetAutoPageBreak(1);
$pdf->hend_title($ary_title);

$pdf->SetFont('Big5','',10);

$pdf->ln();
$pdf->SetFont('big5','B',8);
$pdf->Cell(8,7,'項目',1,0,'C');
$pdf->Cell(20,7,'編號',1,0,'C');
$pdf->Cell(90,7,'品名 / 規格',1,0,'C');
$pdf->Cell(45,7,'顏色',1,0,'C');
$pdf->Cell(20,7,'請購單號碼',1,0,'C');
$pdf->Cell(10,7,'單位',1,0,'C');
$pdf->Cell(15,7,'請購數量',1,0,'C');
$pdf->Cell(15,7,'FOC',1,0,'C');
$pdf->Cell(15,7,'請款數量',1,0,'C');
$pdf->Cell(15,7,'單價',1,0,'C');
$pdf->Cell(25,7,'實付總價',1,0,'C');
$pdf->ln();

$no = '1' ;
$uprice = $qty = $foc = $po_qty = $amount = 0;
$pdf->SetFont('Big5','',10);
foreach($op['apb_det'] as $key => $val) {
	foreach($val["det"] as $key2 => $val2) {
        if($no != 1)$pdf->ln();
		if ( ( $pdf->getY() + 6 ) >= 190 )$pdf->AddPage();

		if (substr($val2['mat_code'],0,1) =='A') {
			$val2['sname'] = $acc->get( '',$val2['mat_code']);
			$note = $val2['sname']['des'];
		} else {
			$val2['sname'] = $lots->get( '',$val2['mat_code']);
			$note = $val2['sname']['comp'];
		}
		
        $height = 6;
		
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(8,$height,$no,1,0,'C');
		$pdf->Cell(20,$height,$val2['mat_code'],1,0,'C');
		$pdf->SetFont('big5','',8);

		$o_y = $pdf->getY();
		$pdf->Cell(90,$height,substr($val2['sname'][2]." / ".($note),0,55),1,0,'L');
		
		$pdf->setY($o_y);
		$pdf->setX(128);
		$pdf->SetFont('big5','',6);
		$pdf->Cell(45,$height,$val2['color'],1,0,'C');
		$pdf->SetFont('Arial','',8);
		
		$pdf->Cell(20,$height,$val2['po_num'],1,0,'C');
		$pdf->Cell(10,$height,$val2['po']['po_unit'],1,0,'C');
		$pdf->Cell(15,$height,number_format($val2['po']['po_qty'], 2, '.',','),1,0,'R');
		//$pdf->Cell(13,$height,$val['ord_num'][0]['receive_time'],1,0,'C');
		$pdf->Cell(15,$height,$val2['foc'],1,0,'R');
		$pdf->Cell(15,$height,number_format($val2['qty'], 2, '.',','),1,0,'R');
		$pdf->Cell(15,$height,number_format($val2['uprice'], 2, '.',','),1,0,'R');
		if ($no==1)
			$pdf->Cell(25,$height,$op['apb']['currency']." $".number_format($val2['uprice']*$val2['qty'], 2, '.',','),1,0,'R');
		else
			$pdf->Cell(25,$height,number_format($val2['uprice']*$val2['qty'], 2, '.',','),1,0,'R');
		$no++;
        
		$amount += number_format($val2['uprice']*$val2['qty'], 2, '.','');
        $qty += $val2['qty'];
        $uprice += $val2['uprice'];
        $foc += $val2['foc'];
        $po_qty += $val2['po_qty'];
        
	}
}

$oth_count = 0;
for($j=0;$j<sizeof($op['apb_oth_cost']);$j++){
	$pdf->ln();
	$pdf->SetFont('Arial','',8);
	$pdf->Cell(8,$height,$no,1,0,'C');
	$pdf->SetFont('big5','',8);
	$pdf->Cell(20,$height,'其他費用',1,0,'C');
	$o_y = $pdf->getY();
	$pdf->Cell(225,$height,$op['apb_oth_cost'][$j]['item'],1,0,'L');
	$pdf->SetFont('Arial','',8);
	$pdf->Cell(25,$height,number_format($op['apb_oth_cost'][$j]['cost'], 2, '.',','),1,0,'R');    
	
	$oth_count += $op['apb_oth_cost'][$j]['cost'];
	$no++;
}

for($j=0;$j<sizeof($op['ap_oth_cost']);$j++){
	$pdf->ln();
	$pdf->SetFont('Arial','',8);
	$pdf->Cell(8,$height,$no,1,0,'C');
	$pdf->SetFont('big5','',8);
	$pdf->Cell(20,$height,'其他費用',1,0,'C');
	$o_y = $pdf->getY();
	$pdf->Cell(225,$height,$op['ap_oth_cost'][$j]['item'],1,0,'L');
	$pdf->SetFont('Arial','',8);
	$pdf->Cell(25,$height,number_format($op['ap_oth_cost'][$j]['cost'], 2, '.',','),1,0,'R');    
	
	$oth_count += $op['ap_oth_cost'][$j]['cost'];
	$no++;
}



$pdf->ln();
# 粗線
$y=$pdf->GetY();
$pdf->SetLineWidth(0.5);
$pdf->Line(10,$pdf->GetY(),288,$pdf->GetY());
$pdf->SetLineWidth(0.1);
$pdf->SetY($y);

$pdf->SetFont('big5','B',8);
$pdf->Cell(208,6,'合 計',1,0,'R');
$pdf->SetFont('Arial','B',8);

$pdf->Cell(15,6,number_format($foc, 2, '.',','),1,0,'R');
$pdf->Cell(15,6,number_format($qty, 2, '.',','),1,0,'R');
$pdf->Cell(15,6,number_format($uprice/($no-1), 2, '.',','),1,0,'R');

if($op['apb']['currency']=='NTD')
	$pdf->Cell(25,6,number_format(round($amount+$oth_count), 0, '',','),1,0,'R');
else
	$pdf->Cell(25,6,number_format($amount+$oth_count, 2, '.',','),1,0,'R');

$pdf->ln();
$pdf->SetFont('big5','B',8);
$pdf->Cell(223,6,'',0,0,'C');
$pdf->Cell(15,6,'營業稅',1,0,'C');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(40,6,number_format($op['apb']['vat'], 2, '.',','),1,0,'R');
$pdf->ln();

$pdf->SetFont('big5','B',8);
$pdf->Cell(223,6,'',0,0,'L');
$pdf->Cell(15,6,'折讓',1,0,'C');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(40,6,($op['apb']['discount'] != 0)?'-'.number_format($op['apb']['discount'], 2, '.',','):'0.00',1,0,'R');
$pdf->ln();

$pdf->SetFont('big5','B',8);
$pdf->Cell(223,6,'',0,0,'L');
$pdf->Cell(15,6,'折稅',1,0,'C');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(40,6,($op['apb']['off_duty'] != 0)?'-'.number_format($op['apb']['off_duty'], 2, '.',','):'0.00',1,0,'R');
$pdf->ln();

$pdf->SetFont('big5','B',8);
$pdf->Cell(223,6,'',0,0,'L');
$pdf->Cell(15,6,'調整稅額',1,0,'C');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(40,6,$op['apb']['adjust_amt'],1,0,'R');
$pdf->ln();

$pdf->SetFont('big5','B',8);
$pdf->Cell(223,6,'',0,0,'L');
$pdf->Cell(15,6,'總合計',1,0,'C');
$pdf->SetFont('Arial','',10);

if($op['apb']['currency']=='NTD')
	$pdf->Cell(40,6,$op['apb']['currency']." $".number_format(round($op['apb']['inv_price']+$op['apb']['adjust_amt']), 0, '',','),1,0,'R');
else
	$pdf->Cell(40,6,$op['apb']['currency']." $".number_format($op['apb']['inv_price']+$op['apb']['adjust_amt'], 2, '.',','),1,0,'R');

$pdf->ln();

if ( ( $pdf->getY() + 50 ) >= 190 )$pdf->AddPage();
######################################################
$pdf->ln();
$pdf->SetFont('big5','B',8);
$pdf->Cell(278,7,'備      註',1,0,'L');
$pdf->SetFont('big5','',8);
$pdf->ln();

$pdf->Cell(278,7,'付款方式：'.$op['apb']['payment'],'LR','TLR','L');
$pdf->ln();

foreach($po_ary as $key => $val){
	$pdf->Cell(15,5,$val['po_num']."： ",'L',0,'L');
	//$pdf->Cell(20,5," [ ".$val['pay_price']." ] ",0,0,'L');
	$str_ord = ' ';
	foreach($val['ord'] as $ordnum){
		$str_ord.=$ordnum."、";
	}
	$str_ord = substr($str_ord,0,-1);
	$pdf->Cell(263,5,$str_ord,'R',0,'L');
	$pdf->ln();
}

for($x=0;$x<sizeof($op['rcv_log']);$x++){
	if ( $pdf->getY() >= 170 )//換頁
		$pdf->AddPage();
	$pdf->Cell(278,5,$op['rcv_log'][$x]['des'],'LR',0,'L');
	$pdf->ln();
}

$y=$pdf->GetY();
//$pdf->ln();
$pdf->SetLineWidth(0.5);
$pdf->Line(10,$pdf->GetY(),288,$pdf->GetY());
$pdf->SetLineWidth(0.1);
$pdf->SetY($y);

//$pdf->ln();

$ch_price = $apb->num2chinese(number_format($op['apb']['inv_price']+$op['apb']['adjust_amt'],2,'.',''));
$pdf->SetFont('big5','B',10);
//$pdf->Cell(278,7,"新台幣 / 美金：".$ch_price[9]." \t仟\t".$ch_price[8]." \t佰\t".$ch_price[7]." \t拾\t".$ch_price[6]." \t萬\t".$ch_price[5]." \t仟\t"
//						.$ch_price[4]." \t佰\t".$ch_price[3]." \t拾\t".$ch_price[2]." \t元\t".$ch_price[1]." \t角\t".$ch_price[0]." \t分\t",1,0,'L');

$pdf->SetFont('big5','B',8);
$pdf->Cell(15,7,$op['apb']['currency']."：",'LTB',0,'L');
$pdf->Cell(17,7,$ch_price[9],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"仟",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[8],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"佰",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[7],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"拾",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[6],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"萬",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[5],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"仟",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[4],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"佰",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[3],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"拾",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[2],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"元",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[1],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"角",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[0],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(12,7,"分 整",'TBR',0,'C');

$pdf->ln();
$pdf->ln();

$pdf->SetFont('big5','B',10);
$pdf->Cell(118,7,"核准：",0,0,'R');
$pdf->Cell(21,7,"",0,0,'L');
$pdf->Cell(21,7,"覆核：",0,0,'R');
$pdf->Cell(21,7,"",0,0,'L');
$pdf->Cell(21,7,"主管：",0,0,'R');
$pdf->Cell(21,7,"",0,0,'L');
$pdf->Cell(21,7,"經辦：",0,0,'R');
$pdf->Cell(21,7,$op['apb']['rcv_user']." ".$submit_user['emp_id'],0,0,'L');

$pdf->SetFont('Arial','',8);
$pdf->SetX();
$pdf->SetY(195);
$pdf->Cell(280,7,"Print ".$GLOBALS['THIS_TIME'],0,0,'R');


$pdf->SetY($oy);
$pdf->SetFont('big5','B',8);
for($s=0;$s<sizeof($op['apb_log']);$s++){
	$pdf->SetX(20);
	$pdf->MultiCell(230,6,($s+1).".".$op['apb_log'][$s]['des'],0,'L',0);
}

$name=$PHP_rcv_num.'.pdf';
$pdf->Output($name,'D');

page_display($op, $AUTH, $TPL_APB_SHOW);
break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "apb_month_tw_print":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "apb_month_tw_print":
check_authority($AUTH,"view");

$op = $apb->get_month_tw_apb($PHP_rcv_num);

$submit_user=$user->get(0,$op['apb']['rcv_sub_user']);
if ($submit_user['name'])$op['apb']['rcv_sub_user'] = $submit_user['name'];
$create_user=$user->get(0,$op['apb']['rcv_user']);
if ($create_user['name'])$op['apb']['rcv_user'] = $create_user['name'];

//請購單位
$where_str = " WHERE dept_code = '".$op['ap'][0]['dept']."'";
$po_dept_name = $dept->get_fields('dept_name',$where_str);

# 將同張 P/O# 的 ord_num 歸類
$po_ary = array();

foreach($op['apb_det'] as $key1 => $val1){
	foreach($val1["det"] as $key2 => $val2){
		$po_ary[$val2['po_num']]['po_num'] = $val2['po_num'];
		foreach($val2['order'] as $key3=>$val3){
            if (!in_array($val3,$po_ary[$val2['po_num']]['ord']))
                $po_ary[$val2['po_num']]['ord'][] = $val3;
		}
	}
}

#請購單位
$dt = $apb->get_dept_code(substr($op['apb_det'][0]['det'][0]['order'][0],0,1));
$where_str = " WHERE dept_code = '".$dt."'";
$dept_name = $dept->get_fields('dept_name',$where_str);

$ary_title = array (
'apb_num'			=>	$op['apb']['rcv_num'],
'apb_dept'	        =>	mb_substr($dept_name[0],0,3,"big5")=="貿易業" ? "貿易業務" : $dept_name[0],	#申請單位
'dept_code'	        =>	$apb->get_apb_dept($dt),            #單位代號
'apb_date'	        =>	$TODAY,			                    #申請日期
'supplier'	        =>	$op['apb']['f_name'],				#受款人
'uni_no'	        =>	$op['apb']['uni_no'],				#統一編號
'inv_num'	        =>	$op['apb']['inv_num'],				#統一發票
'inv_date'	        =>	$op['apb']['inv_date'],				#發票日期
'rcv_user'          =>  $op['apb']['rcv_user'],				#經辦
'rcv_user_id'       =>  $submit_user['emp_id'],				#經辦工號
'foreign_inv_num'   =>	$op['apb']['foreign_inv_num'],      #INVOICE #
);

include_once($config['root_dir']."/lib/class.pdf_apb.php");

$print_title = "驗 收 付 款 單";
$print_title2 = "VER.".$op['apb']['version'];
$creator = $op['apb']['rcv_user'];
$mark = $op['apb']['rcv_num'];

$pdf=new PDF_rcvd('L','mm','A4');
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();
$pdf->SetAutoPageBreak(1);
$pdf->hend_title($ary_title);

$pdf->SetFont('Big5','',10);

$pdf->ln();
$pdf->SetFont('big5','B',8);
$pdf->Cell(8,7,'項目',1,0,'C');
$pdf->Cell(20,7,'編號',1,0,'C');
$pdf->Cell(90,7,'品名 / 規格',1,0,'C');
$pdf->Cell(45,7,'顏色',1,0,'C');
$pdf->Cell(20,7,'請購單號碼',1,0,'C');
$pdf->Cell(10,7,'單位',1,0,'C');
$pdf->Cell(15,7,'請購數量',1,0,'C');
$pdf->Cell(15,7,'FOC',1,0,'C');
$pdf->Cell(15,7,'請款數量',1,0,'C');
$pdf->Cell(15,7,'單價',1,0,'C');
$pdf->Cell(25,7,'實付總價',1,0,'C');
$pdf->ln();

$no = '1' ;
$uprice = $qty = $foc = $po_qty = $amount = 0;
$pdf->SetFont('Big5','',10);
foreach($op['apb_det'] as $key => $val) {
	foreach($val["det"] as $key2 => $val2) {
        if($no != 1)$pdf->ln();
		if ( ( $pdf->getY() + 6 ) >= 190 )$pdf->AddPage();

		if (substr($val2['mat_code'],0,1) =='A') {
			$val2['sname'] = $acc->get( '',$val2['mat_code']);
			$note = $val2['sname']['des'];
		} else {
			$val2['sname'] = $lots->get( '',$val2['mat_code']);
			$note = $val2['sname']['comp'];
		}
		
        $height = 6;
		
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(8,$height,$no,1,0,'C');
		$pdf->Cell(20,$height,$val2['mat_code'],1,0,'C');
		$pdf->SetFont('big5','',8);

		$o_y = $pdf->getY();
		$pdf->Cell(90,$height,substr($val2['sname'][2]." / ".($note),0,55),1,0,'L');
		
		$pdf->setY($o_y);
		$pdf->setX(128);
		$pdf->SetFont('big5','',6);
		$pdf->Cell(45,$height,$val2['color'],1,0,'C');
		$pdf->SetFont('Arial','',8);
		
		$pdf->Cell(20,$height,$val2['po_num'],1,0,'C');
		$pdf->Cell(10,$height,$val2['po']['po_unit'],1,0,'C');
		$pdf->Cell(15,$height,number_format($val2['po']['po_qty'], 2, '.',','),1,0,'R');
		//$pdf->Cell(13,$height,$val['ord_num'][0]['receive_time'],1,0,'C');
		$pdf->Cell(15,$height,$val2['foc'],1,0,'R');
		$pdf->Cell(15,$height,number_format($val2['qty'], 2, '.',','),1,0,'R');
		$pdf->Cell(15,$height,number_format($val2['uprice'], 2, '.',','),1,0,'R');
		if ($no==1)
			$pdf->Cell(25,$height,$op['apb']['currency']." $".number_format($val2['uprice']*$val2['qty'], 2, '.',','),1,0,'R');
		else
			$pdf->Cell(25,$height,number_format($val2['uprice']*$val2['qty'], 2, '.',','),1,0,'R');
		$no++;
        
		$amount += number_format($val2['uprice']*$val2['qty'], 2, '.','');
        $qty += $val2['qty'];
        $uprice += $val2['uprice'];
        $foc += $val2['foc'];
        $po_qty += $val2['po_qty'];
        
	}
}

$oth_count = 0;
for($j=0;$j<sizeof($op['apb_oth_cost']);$j++){
	$pdf->ln();
	$pdf->SetFont('Arial','',8);
	$pdf->Cell(8,$height,$no,1,0,'C');
	$pdf->SetFont('big5','',8);
	$pdf->Cell(20,$height,'其他費用',1,0,'C');
	$o_y = $pdf->getY();
	$pdf->Cell(225,$height,$op['apb_oth_cost'][$j]['item'],1,0,'L');
	$pdf->SetFont('Arial','',8);
	$pdf->Cell(25,$height,number_format($op['apb_oth_cost'][$j]['cost'], 2, '.',','),1,0,'R');    
	
	$oth_count += $op['apb_oth_cost'][$j]['cost'];
	$no++;
}

for($j=0;$j<sizeof($op['ap_oth_cost']);$j++){
	$pdf->ln();
	$pdf->SetFont('Arial','',8);
	$pdf->Cell(8,$height,$no,1,0,'C');
	$pdf->SetFont('big5','',8);
	$pdf->Cell(20,$height,'其他費用',1,0,'C');
	$o_y = $pdf->getY();
	$pdf->Cell(225,$height,$op['ap_oth_cost'][$j]['item'],1,0,'L');
	$pdf->SetFont('Arial','',8);
	$pdf->Cell(25,$height,number_format($op['ap_oth_cost'][$j]['cost'], 2, '.',','),1,0,'R');    
	
	$oth_count += $op['ap_oth_cost'][$j]['cost'];
	$no++;
}



$pdf->ln();
# 粗線
$y=$pdf->GetY();
$pdf->SetLineWidth(0.5);
$pdf->Line(10,$pdf->GetY(),288,$pdf->GetY());
$pdf->SetLineWidth(0.1);
$pdf->SetY($y);

$pdf->SetFont('big5','B',8);
$pdf->Cell(208,6,'合 計',1,0,'R');
$pdf->SetFont('Arial','B',8);

$pdf->Cell(15,6,number_format($foc, 2, '.',','),1,0,'R');
$pdf->Cell(15,6,number_format($qty, 2, '.',','),1,0,'R');
$pdf->Cell(15,6,number_format($uprice/($no-1), 2, '.',','),1,0,'R');

if($op['apb']['currency']=='NTD')
	$pdf->Cell(25,6,number_format(round($amount+$oth_count), 0, '',','),1,0,'R');
else
	$pdf->Cell(25,6,number_format($amount+$oth_count, 2, '.',','),1,0,'R');

$pdf->ln();
$pdf->SetFont('big5','B',8);
$pdf->Cell(223,6,'',0,0,'C');
$pdf->Cell(15,6,'營業稅',1,0,'C');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(40,6,number_format($op['apb']['vat'], 2, '.',','),1,0,'R');
$pdf->ln();

$pdf->SetFont('big5','B',8);
$pdf->Cell(223,6,'',0,0,'L');
$pdf->Cell(15,6,'折讓',1,0,'C');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(40,6,($op['apb']['discount'] != 0)?'-'.number_format($op['apb']['discount'], 2, '.',','):'0.00',1,0,'R');
$pdf->ln();

$pdf->SetFont('big5','B',8);
$pdf->Cell(223,6,'',0,0,'L');
$pdf->Cell(15,6,'折稅',1,0,'C');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(40,6,($op['apb']['off_duty'] != 0)?'-'.number_format($op['apb']['off_duty'], 2, '.',','):'0.00',1,0,'R');
$pdf->ln();

$pdf->SetFont('big5','B',8);
$pdf->Cell(223,6,'',0,0,'L');
$pdf->Cell(15,6,'調整稅額',1,0,'C');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(40,6,$op['apb']['adjust_amt'],1,0,'R');
$pdf->ln();

$pdf->SetFont('big5','B',8);
$pdf->Cell(223,6,'',0,0,'L');
$pdf->Cell(15,6,'總合計',1,0,'C');
$pdf->SetFont('Arial','',10);

if($op['apb']['currency']=='NTD')
	$pdf->Cell(40,6,$op['apb']['currency']." $".number_format(round($op['apb']['inv_price']+$op['apb']['adjust_amt']), 0, '',','),1,0,'R');
else
	$pdf->Cell(40,6,$op['apb']['currency']." $".number_format($op['apb']['inv_price']+$op['apb']['adjust_amt'], 2, '.',','),1,0,'R');

$pdf->ln();

if ( ( $pdf->getY() + 50 ) >= 190 )$pdf->AddPage();
######################################################
$pdf->ln();
$pdf->SetFont('big5','B',8);
$pdf->Cell(278,7,'備      註',1,0,'L');
$pdf->SetFont('big5','',8);
$pdf->ln();

$pdf->Cell(278,7,'付款方式：'.$op['apb']['payment'],'LR','TLR','L');
$pdf->ln();

foreach($po_ary as $key => $val){
	$pdf->Cell(15,5,$val['po_num']."： ",'L',0,'L');
	//$pdf->Cell(20,5," [ ".$val['pay_price']." ] ",0,0,'L');
	$str_ord = ' ';
	foreach($val['ord'] as $ordnum){
		$str_ord.=$ordnum."、";
	}
	$str_ord = substr($str_ord,0,-1);
	$pdf->Cell(263,5,$str_ord,'R',0,'L');
	$pdf->ln();
}

for($x=0;$x<sizeof($op['rcv_log']);$x++){
	if ( $pdf->getY() >= 170 )//換頁
		$pdf->AddPage();
	$pdf->Cell(278,5,$op['rcv_log'][$x]['des'],'LR',0,'L');
	$pdf->ln();
}

$y=$pdf->GetY();
//$pdf->ln();
$pdf->SetLineWidth(0.5);
$pdf->Line(10,$pdf->GetY(),288,$pdf->GetY());
$pdf->SetLineWidth(0.1);
$pdf->SetY($y);

//$pdf->ln();

$ch_price = $apb->num2chinese(number_format($op['apb']['inv_price']+$op['apb']['adjust_amt'],2,'.',''));
$pdf->SetFont('big5','B',10);
//$pdf->Cell(278,7,"新台幣 / 美金：".$ch_price[9]." \t仟\t".$ch_price[8]." \t佰\t".$ch_price[7]." \t拾\t".$ch_price[6]." \t萬\t".$ch_price[5]." \t仟\t"
//						.$ch_price[4]." \t佰\t".$ch_price[3]." \t拾\t".$ch_price[2]." \t元\t".$ch_price[1]." \t角\t".$ch_price[0]." \t分\t",1,0,'L');

$pdf->SetFont('big5','B',8);
$pdf->Cell(15,7,$op['apb']['currency']."：",'LTB',0,'L');
$pdf->Cell(17,7,$ch_price[9],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"仟",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[8],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"佰",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[7],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"拾",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[6],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"萬",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[5],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"仟",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[4],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"佰",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[3],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"拾",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[2],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"元",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[1],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"角",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[0],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(12,7,"分 整",'TBR',0,'C');

$pdf->ln();
$pdf->ln();

$pdf->SetFont('big5','B',10);
$pdf->Cell(118,7,"核准：",0,0,'R');
$pdf->Cell(21,7,"",0,0,'L');
$pdf->Cell(21,7,"覆核：",0,0,'R');
$pdf->Cell(21,7,"",0,0,'L');
$pdf->Cell(21,7,"主管：",0,0,'R');
$pdf->Cell(21,7,"",0,0,'L');
$pdf->Cell(21,7,"經辦：",0,0,'R');
$pdf->Cell(21,7,$op['apb']['rcv_user']." ".$submit_user['emp_id'],0,0,'L');

$pdf->SetFont('Arial','',8);
$pdf->SetX();
$pdf->SetY(195);
$pdf->Cell(280,7,"Print ".$GLOBALS['THIS_TIME'],0,0,'R');


$pdf->SetY($oy);
$pdf->SetFont('big5','B',8);
for($s=0;$s<sizeof($op['apb_log']);$s++){
	$pdf->SetX(20);
	$pdf->MultiCell(230,6,($s+1).".".$op['apb_log'][$s]['des'],0,'L',0);
}

$name=$PHP_rcv_num.'.pdf';
$pdf->Output($name,'D');

page_display($op, $AUTH, $TPL_APB_SHOW);
break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "apb_month_fty_print":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "apb_month_fty_print":
check_authority($AUTH,"view");

$op = $apb->get_month_fty_apb($PHP_rcv_num);

$submit_user=$user->get(0,$op['apb']['rcv_sub_user']);
if ($submit_user['name'])$op['apb']['rcv_sub_user'] = $submit_user['name'];
$create_user=$user->get(0,$op['apb']['rcv_user']);
if ($create_user['name'])$op['apb']['rcv_user'] = $create_user['name'];

//請購單位
$where_str = " WHERE dept_code = '".$op['ap'][0]['dept']."'";
$po_dept_name = $dept->get_fields('dept_name',$where_str);

# 將同張 P/O# 的 ord_num 歸類
$po_ary = array();

foreach($op['apb_det'] as $key1 => $val1){
	foreach($val1["det"] as $key2 => $val2){
		$po_ary[$val2['po_num']]['po_num'] = $val2['po_num'];
		foreach($val2['order'] as $key3=>$val3){
            if (!in_array($val3,$po_ary[$val2['po_num']]['ord']))
                $po_ary[$val2['po_num']]['ord'][] = $val3;
		}
	}
}

#請購單位
$dt = $apb->get_dept_code(substr($op['apb_det'][0]['det'][0]['order'][0],0,1));
$where_str = " WHERE dept_code = '".$dt."'";
$dept_name = $dept->get_fields('dept_name',$where_str);

$ary_title = array (
'apb_num'			=>	$op['apb']['rcv_num'],
'apb_dept'	        =>	mb_substr($dept_name[0],0,3,"big5")=="貿易業" ? "貿易業務" : $dept_name[0],	#申請單位
'dept_code'	        =>	$apb->get_apb_dept($dt),            #單位代號
'apb_date'	        =>	$TODAY,			                    #申請日期
'supplier'	        =>	$op['apb']['f_name'],				#受款人
'uni_no'	        =>	$op['apb']['uni_no'],				#統一編號
'inv_num'	        =>	$op['apb']['inv_num'],				#統一發票
'inv_date'	        =>	$op['apb']['inv_date'],				#發票日期
'rcv_user'          =>  $op['apb']['rcv_user'],				#經辦
'rcv_user_id'       =>  $submit_user['emp_id'],				#經辦工號
'foreign_inv_num'   =>	$op['apb']['foreign_inv_num'],      #INVOICE #
);

include_once($config['root_dir']."/lib/class.pdf_apb.php");

$print_title = "驗 收 付 款 單";
$print_title2 = "VER.".$op['apb']['version'];
$creator = $op['apb']['rcv_user'];
$mark = $op['apb']['rcv_num'];

$pdf=new PDF_rcvd('L','mm','A4');
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();
$pdf->SetAutoPageBreak(1);
$pdf->hend_title($ary_title);

$pdf->SetFont('Big5','',10);

$pdf->ln();
$pdf->SetFont('big5','B',8);
$pdf->Cell(8,7,'項目',1,0,'C');
$pdf->Cell(20,7,'編號',1,0,'C');
$pdf->Cell(90,7,'品名 / 規格',1,0,'C');
$pdf->Cell(45,7,'顏色',1,0,'C');
$pdf->Cell(20,7,'請購單號碼',1,0,'C');
$pdf->Cell(10,7,'單位',1,0,'C');
$pdf->Cell(15,7,'請購數量',1,0,'C');
$pdf->Cell(15,7,'FOC',1,0,'C');
$pdf->Cell(15,7,'請款數量',1,0,'C');
$pdf->Cell(15,7,'單價',1,0,'C');
$pdf->Cell(25,7,'實付總價',1,0,'C');
$pdf->ln();

$no = '1' ;
$uprice = $qty = $foc = $po_qty = $amount = 0;
$pdf->SetFont('Big5','',10);
foreach($op['apb_det'] as $key => $val) {
	foreach($val["det"] as $key2 => $val2) {
        if($no != 1)$pdf->ln();
		if ( ( $pdf->getY() + 6 ) >= 190 )$pdf->AddPage();

		if (substr($val2['mat_code'],0,1) =='A') {
			$val2['sname'] = $acc->get( '',$val2['mat_code']);
			$note = $val2['sname']['des'];
		} else {
			$val2['sname'] = $lots->get( '',$val2['mat_code']);
			$note = $val2['sname']['comp'];
		}
		
        $height = 6;
		
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(8,$height,$no,1,0,'C');
		$pdf->Cell(20,$height,$val2['mat_code'],1,0,'C');
		$pdf->SetFont('big5','',8);

		$o_y = $pdf->getY();
		$pdf->Cell(90,$height,substr($val2['sname'][2]." / ".($note),0,55),1,0,'L');
		
		$pdf->setY($o_y);
		$pdf->setX(128);
		$pdf->SetFont('big5','',6);
		$pdf->Cell(45,$height,$val2['color'],1,0,'C');
		$pdf->SetFont('Arial','',8);
		
		$pdf->Cell(20,$height,$val2['po_num'],1,0,'C');
		$pdf->Cell(10,$height,$val2['po']['po_unit'],1,0,'C');
		$pdf->Cell(15,$height,number_format($val2['po']['po_qty'], 2, '.',','),1,0,'R');
		//$pdf->Cell(13,$height,$val['ord_num'][0]['receive_time'],1,0,'C');
		$pdf->Cell(15,$height,$val2['foc'],1,0,'R');
		$pdf->Cell(15,$height,number_format($val2['qty'], 2, '.',','),1,0,'R');
		$pdf->Cell(15,$height,number_format($val2['uprice'], 2, '.',','),1,0,'R');
		if ($no==1)
			$pdf->Cell(25,$height,$op['apb']['currency']." $".number_format($val2['uprice']*$val2['qty'], 2, '.',','),1,0,'R');
		else
			$pdf->Cell(25,$height,number_format($val2['uprice']*$val2['qty'], 2, '.',','),1,0,'R');
		$no++;
        
		$amount += number_format($val2['uprice']*$val2['qty'], 2, '.','');
        $qty += $val2['qty'];
        $uprice += $val2['uprice'];
        $foc += $val2['foc'];
        $po_qty += $val2['po_qty'];
        
	}
}

$oth_count = 0;
for($j=0;$j<sizeof($op['apb_oth_cost']);$j++){
	$pdf->ln();
	$pdf->SetFont('Arial','',8);
	$pdf->Cell(8,$height,$no,1,0,'C');
	$pdf->SetFont('big5','',8);
	$pdf->Cell(20,$height,'其他費用',1,0,'C');
	$o_y = $pdf->getY();
	$pdf->Cell(225,$height,$op['apb_oth_cost'][$j]['item'],1,0,'L');
	$pdf->SetFont('Arial','',8);
	$pdf->Cell(25,$height,number_format($op['apb_oth_cost'][$j]['cost'], 2, '.',','),1,0,'R');    
	
	$oth_count += $op['apb_oth_cost'][$j]['cost'];
	$no++;
}

for($j=0;$j<sizeof($op['ap_oth_cost']);$j++){
	$pdf->ln();
	$pdf->SetFont('Arial','',8);
	$pdf->Cell(8,$height,$no,1,0,'C');
	$pdf->SetFont('big5','',8);
	$pdf->Cell(20,$height,'其他費用',1,0,'C');
	$o_y = $pdf->getY();
	$pdf->Cell(225,$height,$op['ap_oth_cost'][$j]['item'],1,0,'L');
	$pdf->SetFont('Arial','',8);
	$pdf->Cell(25,$height,number_format($op['ap_oth_cost'][$j]['cost'], 2, '.',','),1,0,'R');    
	
	$oth_count += $op['ap_oth_cost'][$j]['cost'];
	$no++;
}



$pdf->ln();
# 粗線
$y=$pdf->GetY();
$pdf->SetLineWidth(0.5);
$pdf->Line(10,$pdf->GetY(),288,$pdf->GetY());
$pdf->SetLineWidth(0.1);
$pdf->SetY($y);

$pdf->SetFont('big5','B',8);
$pdf->Cell(208,6,'合 計',1,0,'R');
$pdf->SetFont('Arial','B',8);

$pdf->Cell(15,6,number_format($foc, 2, '.',','),1,0,'R');
$pdf->Cell(15,6,number_format($qty, 2, '.',','),1,0,'R');
$pdf->Cell(15,6,number_format($uprice/($no-1), 2, '.',','),1,0,'R');

if($op['apb']['currency']=='NTD')
	$pdf->Cell(25,6,number_format(round($amount+$oth_count), 0, '',','),1,0,'R');
else
	$pdf->Cell(25,6,number_format($amount+$oth_count, 2, '.',','),1,0,'R');

$pdf->ln();
$pdf->SetFont('big5','B',8);
$pdf->Cell(223,6,'',0,0,'C');
$pdf->Cell(15,6,'營業稅',1,0,'C');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(40,6,number_format($op['apb']['vat'], 2, '.',','),1,0,'R');
$pdf->ln();

$pdf->SetFont('big5','B',8);
$pdf->Cell(223,6,'',0,0,'L');
$pdf->Cell(15,6,'折讓',1,0,'C');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(40,6,($op['apb']['discount'] != 0)?'-'.number_format($op['apb']['discount'], 2, '.',','):'0.00',1,0,'R');
$pdf->ln();

$pdf->SetFont('big5','B',8);
$pdf->Cell(223,6,'',0,0,'L');
$pdf->Cell(15,6,'折稅',1,0,'C');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(40,6,($op['apb']['off_duty'] != 0)?'-'.number_format($op['apb']['off_duty'], 2, '.',','):'0.00',1,0,'R');
$pdf->ln();

$pdf->SetFont('big5','B',8);
$pdf->Cell(223,6,'',0,0,'L');
$pdf->Cell(15,6,'調整稅額',1,0,'C');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(40,6,$op['apb']['adjust_amt'],1,0,'R');
$pdf->ln();

$pdf->SetFont('big5','B',8);
$pdf->Cell(223,6,'',0,0,'L');
$pdf->Cell(15,6,'總合計',1,0,'C');
$pdf->SetFont('Arial','',10);

if($op['apb']['currency']=='NTD')
	$pdf->Cell(40,6,$op['apb']['currency']." $".number_format(round($op['apb']['inv_price']+$op['apb']['adjust_amt']), 0, '',','),1,0,'R');
else
	$pdf->Cell(40,6,$op['apb']['currency']." $".number_format($op['apb']['inv_price']+$op['apb']['adjust_amt'], 2, '.',','),1,0,'R');

$pdf->ln();

if ( ( $pdf->getY() + 50 ) >= 190 )$pdf->AddPage();
######################################################
$pdf->ln();
$pdf->SetFont('big5','B',8);
$pdf->Cell(278,7,'備      註',1,0,'L');
$pdf->SetFont('big5','',8);
$pdf->ln();

$pdf->Cell(278,7,'付款方式：'.$op['apb']['payment'],'LR','TLR','L');
$pdf->ln();

foreach($po_ary as $key => $val){
	$pdf->Cell(15,5,$val['po_num']."： ",'L',0,'L');
	//$pdf->Cell(20,5," [ ".$val['pay_price']." ] ",0,0,'L');
	$str_ord = ' ';
	foreach($val['ord'] as $ordnum){
		$str_ord.=$ordnum."、";
	}
	$str_ord = substr($str_ord,0,-1);
	$pdf->Cell(263,5,$str_ord,'R',0,'L');
	$pdf->ln();
}

for($x=0;$x<sizeof($op['rcv_log']);$x++){
	if ( $pdf->getY() >= 170 )//換頁
		$pdf->AddPage();
	$pdf->Cell(278,5,$op['rcv_log'][$x]['des'],'LR',0,'L');
	$pdf->ln();
}

$y=$pdf->GetY();
//$pdf->ln();
$pdf->SetLineWidth(0.5);
$pdf->Line(10,$pdf->GetY(),288,$pdf->GetY());
$pdf->SetLineWidth(0.1);
$pdf->SetY($y);

//$pdf->ln();

$ch_price = $apb->num2chinese(number_format($op['apb']['inv_price']+$op['apb']['adjust_amt'],2,'.',''));
$pdf->SetFont('big5','B',10);
//$pdf->Cell(278,7,"新台幣 / 美金：".$ch_price[9]." \t仟\t".$ch_price[8]." \t佰\t".$ch_price[7]." \t拾\t".$ch_price[6]." \t萬\t".$ch_price[5]." \t仟\t"
//						.$ch_price[4]." \t佰\t".$ch_price[3]." \t拾\t".$ch_price[2]." \t元\t".$ch_price[1]." \t角\t".$ch_price[0]." \t分\t",1,0,'L');

$pdf->SetFont('big5','B',8);
$pdf->Cell(15,7,$op['apb']['currency']."：",'LTB',0,'L');
$pdf->Cell(17,7,$ch_price[9],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"仟",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[8],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"佰",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[7],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"拾",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[6],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"萬",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[5],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"仟",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[4],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"佰",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[3],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"拾",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[2],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"元",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[1],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"角",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[0],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(12,7,"分 整",'TBR',0,'C');

$pdf->ln();
$pdf->ln();

$pdf->SetFont('big5','B',10);
$pdf->Cell(118,7,"核准：",0,0,'R');
$pdf->Cell(21,7,"",0,0,'L');
$pdf->Cell(21,7,"覆核：",0,0,'R');
$pdf->Cell(21,7,"",0,0,'L');
$pdf->Cell(21,7,"主管：",0,0,'R');
$pdf->Cell(21,7,"",0,0,'L');
$pdf->Cell(21,7,"經辦：",0,0,'R');
$pdf->Cell(21,7,$op['apb']['rcv_user']." ".$submit_user['emp_id'],0,0,'L');

$pdf->SetFont('Arial','',8);
$pdf->SetX();
$pdf->SetY(195);
$pdf->Cell(280,7,"Print ".$GLOBALS['THIS_TIME'],0,0,'R');


$pdf->SetY($oy);
$pdf->SetFont('big5','B',8);
for($s=0;$s<sizeof($op['apb_log']);$s++){
	$pdf->SetX(20);
	$pdf->MultiCell(230,6,($s+1).".".$op['apb_log'][$s]['des'],0,'L',0);
}

$name=$PHP_rcv_num.'.pdf';
$pdf->Output($name,'D');

page_display($op, $AUTH, $TPL_APB_SHOW);
break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "apb_before_after_print":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "apb_before_after_print":
check_authority($AUTH,"view");

$op = $apb->get_before_after_apb($PHP_rcv_num);

$submit_user=$user->get(0,$op['apb']['rcv_sub_user']);
if ($submit_user['name'])$op['apb']['rcv_sub_user'] = $submit_user['name'];
$create_user=$user->get(0,$op['apb']['rcv_user']);
if ($create_user['name'])$op['apb']['rcv_user'] = $create_user['name'];

//請購單位
$where_str = " WHERE dept_code = '".$op['ap'][0]['dept']."'";
$po_dept_name = $dept->get_fields('dept_name',$where_str);

# 將同張 P/O# 的 ord_num 歸類
$po_ary = array();

foreach($op['ap'] as $key1 => $val1){
	$po_ary[$val1['po_num']]['po_num'] = $val1['po_num'];
	foreach($val1["ap_det"] as $key2 => $val2){
		foreach($val2['orders'] as $key3=>$val3){
            if (!in_array($val3,$po_ary[$val1['po_num']]['ord']))
                $po_ary[$val1['po_num']]['ord'][] = $val3;
		}
	}
}

#請購單位
$dt = $apb->get_dept_code(substr($op['apb_det'][0]['det'][0]['order'][0],0,1));
$where_str = " WHERE dept_code = '".$dt."'";
$dept_name = $dept->get_fields('dept_name',$where_str);

$ary_title = array (
'apb_num'			=>	$op['apb']['rcv_num'],
'apb_dept'	        =>	mb_substr($dept_name[0],0,3,"big5")=="貿易業" ? "貿易業務" : $dept_name[0],	#申請單位
'dept_code'	        =>	$apb->get_apb_dept($dt),            #單位代號
'apb_date'	        =>	$TODAY,			                    #申請日期
'supplier'	        =>	$op['apb']['f_name'],				#受款人
'uni_no'	        =>	$op['apb']['uni_no'],				#統一編號
'inv_num'	        =>	$op['apb']['inv_num'],				#統一發票
'inv_date'	        =>	$op['apb']['inv_date'],				#發票日期
'rcv_user'          =>  $op['apb']['rcv_user'],				#經辦
'rcv_user_id'       =>  $submit_user['emp_id'],				#經辦工號
'foreign_inv_num'   =>	$op['apb']['foreign_inv_num'],      #INVOICE #
);

include_once($config['root_dir']."/lib/class.pdf_apb.php");

$print_title = "請 款 單";
$print_title2 = "VER.".$op['apb']['version'];
$creator = $op['apb']['rcv_user'];
$mark = $op['apb']['rcv_num'];

$pdf=new PDF_rcvd('L','mm','A4');
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();
$pdf->SetAutoPageBreak(1);
$pdf->hend_title($ary_title);

$pdf->SetFont('Big5','',10);

$pdf->ln();
$pdf->SetFont('big5','B',8);
$pdf->Cell(8,7,'項目',1,0,'C');
$pdf->Cell(20,7,'編號',1,0,'C');
$pdf->Cell(90,7,'品名 / 規格',1,0,'C');
$pdf->Cell(45,7,'顏色',1,0,'C');
$pdf->Cell(20,7,'請購單號碼',1,0,'C');
$pdf->Cell(10,7,'單位',1,0,'C');
$pdf->Cell(15,7,'請購數量',1,0,'C');
$pdf->Cell(15,7,'FOC',1,0,'C');
$pdf->Cell(15,7,'請款數量',1,0,'C');
$pdf->Cell(15,7,'單價',1,0,'C');
$pdf->Cell(25,7,'實付總價',1,0,'C');
$pdf->ln();

$no = '1' ;
$uprice = $qty = $foc = $po_qty = $amount = 0;
$pdf->SetFont('Big5','',10);
foreach($op['ap'] as $key => $val) {
	foreach($val["ap_det"] as $key2 => $val2) {
        if($no != 1)$pdf->ln();
		if ( ( $pdf->getY() + 6 ) >= 190 )$pdf->AddPage();

		if (substr($val2['mat_code'],0,1) =='A') {
			$val2['sname'] = $acc->get( '',$val2['mat_code']);
			$note = $val2['sname']['des'];
		} else {
			$val2['sname'] = $lots->get( '',$val2['mat_code']);
			$note = $val2['sname']['comp'];
		}
		
        $height = 6;
		
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(8,$height,$no,1,0,'C');
		$pdf->Cell(20,$height,$val2['mat_code'],1,0,'C');
		$pdf->SetFont('big5','',8);

		$o_y = $pdf->getY();
		$pdf->Cell(90,$height,substr($val2['sname'][2]." / ".($note),0,55),1,0,'L');
		
		$pdf->setY($o_y);
		$pdf->setX(128);
		$pdf->SetFont('big5','',6);
		$pdf->Cell(45,$height,$val2['color'],1,0,'C');
		$pdf->SetFont('Arial','',8);
		
		$pdf->Cell(20,$height,str_replace("PA","PO",$val2['ap_num']),1,0,'C');
		$pdf->Cell(10,$height,$val2['po_unit'],1,0,'C');
		$pdf->Cell(15,$height,number_format($val2['po_qty'], 2, '.',','),1,0,'R');
		//$pdf->Cell(13,$height,$val['ord_num'][0]['receive_time'],1,0,'C');
		$pdf->Cell(15,$height,$val2['foc'],1,0,'R');
		$pdf->Cell(15,$height,number_format($val2['qty'], 2, '.',','),1,0,'R');
		$pdf->Cell(15,$height,number_format($val2['uprice'], 2, '.',','),1,0,'R');
		if ($no==1)
			$pdf->Cell(25,$height,$op['apb']['currency']." $".number_format($val2['uprice']*$val2['qty'], 2, '.',','),1,0,'R');
		else
			$pdf->Cell(25,$height,number_format($val2['uprice']*$val2['qty'], 2, '.',','),1,0,'R');
		$no++;
        
		$amount += number_format($val2['uprice']*$val2['qty'], 2, '.','');
        $qty += $val2['qty'];
        $uprice += $val2['uprice'];
        $foc += $val2['foc'];
        $po_qty += $val2['po_qty'];
        
	}
}

$oth_count = 0;
foreach($op['ap'] as $key => $val){
	if($val['apb_oth_cost']){
		foreach($val['apb_oth_cost'] as $key2 => $val2){
			$pdf->ln();
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(8,$height,$no,1,0,'C');
			$pdf->SetFont('big5','',8);
			$pdf->Cell(20,$height,'其他費用',1,0,'C');
			$o_y = $pdf->getY();
			$pdf->Cell(225,$height,$val2['item'],1,0,'L');
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(25,$height,number_format($val2['cost'], 2, '.',','),1,0,'R');    
			
			$oth_count += $val2['cost'];
			$no++;
		}
	}
}

for($i=0;$i<sizeof($op['ap']);$i++){
    for($j=0;$j<sizeof($op['ap'][$i]['ap_oth_cost']);$j++){
        if($op['ap'][$i]['ap_oth_cost'][$j]){
            $pdf->ln();
            $pdf->SetFont('Arial','',8);
            $pdf->Cell(8,$height,$no,1,0,'C');
            $pdf->SetFont('big5','',8);
            $pdf->Cell(20,$height,'其他費用',1,0,'C');
            $o_y = $pdf->getY();
            $pdf->Cell(225,$height,$op['ap'][$i]['ap_oth_cost'][$j]['item'],1,0,'L');
            $pdf->SetFont('Arial','',8);
            $pdf->Cell(25,$height,number_format($op['ap'][$i]['ap_oth_cost'][$j]['cost'], 2, '.',','),1,0,'R');    

            $oth_count += $op['ap'][$i]['ap_oth_cost'][$j]['cost'];
            $no++;
        }
	}
}



$pdf->ln();
# 粗線
$y=$pdf->GetY();
$pdf->SetLineWidth(0.5);
$pdf->Line(10,$pdf->GetY(),288,$pdf->GetY());
$pdf->SetLineWidth(0.1);
$pdf->SetY($y);

$pdf->SetFont('big5','B',8);
$pdf->Cell(208,6,'合 計',1,0,'R');
$pdf->SetFont('Arial','B',8);

$pdf->Cell(15,6,number_format($foc, 2, '.',','),1,0,'R');
$pdf->Cell(15,6,number_format($qty, 2, '.',','),1,0,'R');
$pdf->Cell(15,6,number_format($uprice/($no-1), 2, '.',','),1,0,'R');

if($op['apb']['currency']=='NTD')
	$pdf->Cell(25,6,number_format(round($amount+$oth_count), 0, '',','),1,0,'R');
else
	$pdf->Cell(25,6,number_format($amount+$oth_count, 2, '.',','),1,0,'R');

$pdf->ln();
$pdf->SetFont('big5','B',8);
$pdf->Cell(223,6,'',0,0,'C');
$pdf->Cell(15,6,'營業稅',1,0,'C');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(40,6,number_format($op['apb']['vat'], 2, '.',','),1,0,'R');
$pdf->ln();

$pdf->SetFont('big5','B',8);
$pdf->Cell(223,6,'',0,0,'L');
$pdf->Cell(15,6,'折讓',1,0,'C');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(40,6,($op['apb']['discount'] != 0)?'-'.number_format($op['apb']['discount'], 2, '.',','):'0.00',1,0,'R');
$pdf->ln();

$pdf->SetFont('big5','B',8);
$pdf->Cell(223,6,'',0,0,'L');
$pdf->Cell(15,6,'折稅',1,0,'C');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(40,6,($op['apb']['off_duty'] != 0)?'-'.number_format($op['apb']['off_duty'], 2, '.',','):'0.00',1,0,'R');
$pdf->ln();

$pdf->SetFont('big5','B',8);
$pdf->Cell(223,6,'',0,0,'L');
$pdf->Cell(15,6,'調整稅額',1,0,'C');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(40,6,$op['apb']['adjust_amt'],1,0,'R');
$pdf->ln();

$pdf->SetFont('big5','B',8);
$pdf->Cell(223,6,'',0,0,'L');
$pdf->Cell(15,6,'總合計',1,0,'C');
$pdf->SetFont('Arial','',10);

if($op['apb']['currency']=='NTD')
	$pdf->Cell(40,6,$op['apb']['currency']." $".number_format(round($op['apb']['inv_price']+$op['apb']['adjust_amt']), 0, '',','),1,0,'R');
else
	$pdf->Cell(40,6,$op['apb']['currency']." $".number_format($op['apb']['inv_price']+$op['apb']['adjust_amt'], 2, '.',','),1,0,'R');

$pdf->ln();

if ( ( $pdf->getY() + 50 ) >= 190 )$pdf->AddPage();
######################################################
$pdf->ln();
$pdf->SetFont('big5','B',8);
$pdf->Cell(278,7,'備      註',1,0,'L');
$pdf->SetFont('big5','',8);
$pdf->ln();

$pdf->Cell(278,7,'付款方式：'.$op['apb']['payment'],'LR','TLR','L');
$pdf->ln();

foreach($po_ary as $key => $val){
	$pdf->Cell(15,5,$val['po_num']."： ",'L',0,'L');
	//$pdf->Cell(20,5," [ ".$val['pay_price']." ] ",0,0,'L');
	$str_ord = ' ';
	foreach($val['ord'] as $ordnum){
		$str_ord.=$ordnum."、";
	}
	$str_ord = substr($str_ord,0,-1);
	$pdf->Cell(263,5,$str_ord,'R',0,'L');
	$pdf->ln();
}

for($x=0;$x<sizeof($op['rcv_log']);$x++){
	if ( $pdf->getY() >= 170 )//換頁
		$pdf->AddPage();
	$pdf->Cell(278,5,$op['rcv_log'][$x]['des'],'LR',0,'L');
	$pdf->ln();
}

$y=$pdf->GetY();
//$pdf->ln();
$pdf->SetLineWidth(0.5);
$pdf->Line(10,$pdf->GetY(),288,$pdf->GetY());
$pdf->SetLineWidth(0.1);
$pdf->SetY($y);

//$pdf->ln();

$ch_price = $apb->num2chinese(number_format($op['apb']['inv_price']+$op['apb']['adjust_amt'],2,'.',''));
$pdf->SetFont('big5','B',10);
//$pdf->Cell(278,7,"新台幣 / 美金：".$ch_price[9]." \t仟\t".$ch_price[8]." \t佰\t".$ch_price[7]." \t拾\t".$ch_price[6]." \t萬\t".$ch_price[5]." \t仟\t"
//						.$ch_price[4]." \t佰\t".$ch_price[3]." \t拾\t".$ch_price[2]." \t元\t".$ch_price[1]." \t角\t".$ch_price[0]." \t分\t",1,0,'L');

$pdf->SetFont('big5','B',8);
$pdf->Cell(15,7,$op['apb']['currency']."：",'LTB',0,'L');
$pdf->Cell(17,7,$ch_price[9],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"仟",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[8],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"佰",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[7],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"拾",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[6],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"萬",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[5],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"仟",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[4],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"佰",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[3],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"拾",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[2],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"元",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[1],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(9,7,"角",'TB',0,'C');
$pdf->SetFont('big5','B',11);
$pdf->Cell(17,7,$ch_price[0],'TB',0,'C');
$pdf->SetFont('big5','B',8);
$pdf->Cell(12,7,"分 整",'TBR',0,'C');

$pdf->ln();
$pdf->ln();

$pdf->SetFont('big5','B',10);
$pdf->Cell(118,7,"核准：",0,0,'R');
$pdf->Cell(21,7,"",0,0,'L');
$pdf->Cell(21,7,"覆核：",0,0,'R');
$pdf->Cell(21,7,"",0,0,'L');
$pdf->Cell(21,7,"主管：",0,0,'R');
$pdf->Cell(21,7,"",0,0,'L');
$pdf->Cell(21,7,"經辦：",0,0,'R');
$pdf->Cell(21,7,$op['apb']['rcv_user']." ".$submit_user['emp_id'],0,0,'L');

$pdf->SetFont('Arial','',8);
$pdf->SetX();
$pdf->SetY(195);
$pdf->Cell(280,7,"Print ".$GLOBALS['THIS_TIME'],0,0,'R');


$pdf->SetY($oy);
$pdf->SetFont('big5','B',8);
for($s=0;$s<sizeof($op['apb_log']);$s++){
	$pdf->SetX(20);
	$pdf->MultiCell(230,6,($s+1).".".$op['apb_log'][$s]['des'],0,'L',0);
}

$name=$PHP_rcv_num.'.pdf';
$pdf->Output($name,'D');

page_display($op, $AUTH, $TPL_APB_SHOW);
break;


case "show_un_rcv_po":
	check_authority($AUTH,"view");
	
	$where_str = "order by cust_s_name";
	$cust_def = $cust->get_fields('cust_init_name',$where_str);
	$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);
	
	for ($i=0; $i< sizeof($cust_def); $i++){
		$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//以 [客戶代號-客戶簡稱] 呈現
	}
	
	if($PHP_search_flag){
		$op['ap'] = $apb->search_un_rcv_po($_POST);
		$op['login_id'] = $_POST['PHP_po_user'];
		$op['cust_select'] =  $arry2->select($cust_value,$_POST['PHP_cust'],'PHP_cust','select','check_add(this)',$cust_def_vue); 
		$op['begin_date'] = $_POST['PHP_begin_date'];
		$op['end_date'] = $_POST['PHP_end_date'];
	}else{
		$op['login_id'] = $GLOBALS['SCACHE']['ADMIN']['login_id']; 
		$op['cust_select'] =  $arry2->select($cust_value,'','PHP_cust','select','check_add(this)',$cust_def_vue); 
	}
	
	
	
	page_display($op, $AUTH, "un_rcv_po_show.html");
break;

// end case ---------
}

?>
