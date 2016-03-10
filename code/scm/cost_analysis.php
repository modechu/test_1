<?php
session_start();
session_register	('SCACHE');
session_register	('PAGE');
session_register	('authority');
session_register	('where_str');
session_register	('parm');
session_register	('PHP_ses_etd');
session_register	('PHP_unstatus');
##################  2004/11/10  ########################
#			monitor.php  ¥Dµ{¦¡
#		for Carnival SCM [Sample]  management
#			Jack Yang     2004/11/10
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

require_once "config.php";
require_once "config.admin.php";
include_once($config['root_dir']."/lib/class.monitor.php");
$PHP_SELF = $_SERVER['PHP_SELF'];

$perm = $GLOBALS['power'];


require_once "init.object.php";
$monitor = new MONITOR();
if (!$monitor->init($mysql,"log")) { print "error!! cannot initialize database for LOG class"; exit; }

//工繳取得
session_register	('FTY_CM');
$para_cm = $para->get(0,'hj-cm');
$FTY_CM['HJ'] = $para_cm['set_value'];
$para_cm = $para->get(0,'ly-cm');
$FTY_CM['LY'] = $para_cm['set_value'];
$FTY_CM['SC'] = 0;


$op = array();

// echo $PHP_action.'<br>';
switch ($PHP_action) {
//=======================================================
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "cost_analysis":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "cost_analysis":
		check_authority('058',"view");
				// creat cust combo box
		$op['year_1'] = $arry2->select($YEAR_WORK,date('Y'),'SCH_year','select','');  	
		$op['month'] = $arry2->select($MONTH_WORK,'','SCH_month','select','');  	

		$where_str="order by cust_s_name"; //依cust_s_name排序
		$cust_def = $cust->get_fields('cust_init_name',$where_str);
		$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//取出客戶代號
		for ($i=0; $i< sizeof($cust_def); $i++) $cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//以 [客戶代號-客戶簡稱] 呈現

		$op['cust_select'] =  $arry2->select($cust_value,'','SCH_cust','select','',$cust_def_vue); 
		$op['fty_select'] =  $arry2->select($FACTORY,'','SCH_fty','select'); 



//080725message增加		
	$note=$notify->search($GLOBALS['SCACHE']['ADMIN']['login_id'], "`read`=0");
	$op['max_notify'] = $note['max_no'];
	
			page_display($op,'058', $TPL_ORD_COST_SEARCH);    	    
		break;
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "ord_cost_list":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "ord_cost_list":
		check_authority('058',"view");
				// creat cust combo box
		if(!isset($SCH_status))$SCH_status ='';		
		if(!isset($SCH_ship))
		{
			$SCH_ship = '';
			if(!$SCH_year && $SCH_month)	$SCH_year = date('Y');
			if($SCH_year) $SCH_ship = $SCH_year."-".$SCH_month;
		}
		$op = $order->search_shiped();
		
		for($i=0; $i<sizeof($op['ord']); $i++)
		{	

			$op['order'][$i] = $op['ord'][$i];
			$op['order'][$i]['status'] = get_ord_status($op['ord'][$i]['status']);
			$op['order'][$i]['cust_name'] = $op['ord'][$i]['cust_name'];

			$Cost = $bom->get_cost($op['ord'][$i]['wi_id'],$op['ord'][$i]['qty'],$op['ord'][$i]['bcfm_date']);

			$ops['USD'] = $GLOBALS['rate']->get_rate('USD',$Cost['rate_date']);
			$ops['HKD'] = $GLOBALS['rate']->get_rate('HKD',$Cost['rate_date']);
			$ops['EUR'] = $GLOBALS['rate']->get_rate('EUR',$Cost['rate_date']);

			if( !empty($Cost['lots_po_ttl']) ) $ops['po_check'] = 1;
			if( !empty($Cost['lots_rcv_ttl']) ) $ops['rcv_check'] = 1;

			# Fabric cost per unit  $Cost['rate_date']
			$ops['ord']['lots'] = ( ( $op['ord'][$i]['mat_useage'] * $op['ord'][$i]['mat_u_cost'] ) + $op['ord'][$i]['fusible'] + $op['ord'][$i]['interline'] ) * $ops['USD'];
			$ops['rcv']['lots'] = $Cost['lots_rcv_ttl'] / $op['ord'][$i]['qty'] ;

			# Accessory cost per unit 
			$ops['ord']['acc'] = $op['ord'][$i]['acc_u_cost'] * $ops['USD'];
			$ops['rcv']['acc'] = $Cost['acc_rcv_ttl'] / $op['ord'][$i]['qty'] ;

			# Sub. Total 
			$ops['ord']['sub_tal'] = $ops['ord']['lots'] + $ops['ord']['acc'];
			$ops['rcv']['sub_tal'] = $ops['rcv']['lots'] + $ops['rcv']['acc'];

			# Other treatment 
			$ops['ord']['other_tremt'] = ( $op['ord'][$i]['emb'] + $op['ord'][$i]['wash'] + $op['ord'][$i]['oth'] ) * $ops['USD'];
			$ops['rcv']['other_tremt'] = ( $op['ord'][$i]['rel_cm_cost'] * $ops['USD'] ) / $op['ord'][$i]['qty'] ;

			# Others
			$ops['ord']['other'] = ( $op['ord'][$i]['quota_fee'] + $op['ord'][$i]['comm_fee'] ) * $ops['USD'];

			# C.M.
			$op['ord'][$i]['fty_cm'] *= $ops['USD'];
			$op['ord'][$i]['cm'] *= $ops['USD'];

			# Est. Total Sales of this order
			$ship_fob = $op['ord'][$i]['ship_fob'];
			if(!isset($op['ord'][$i]['ship_fob']) || $op['ord'][$i]['ship_fob'] == 0)$op['ord'][$i]['ship_fob'] = $op['ord'][$i]['uprice'];
			$op['ord'][$i]['sales'] = $op['ord'][$i]['uprice'] * $op['ord'][$i]['qty'] * $ops['USD'];
			$ops['rcv']['sales'] = $op['ord'][$i]['ship_fob'] * $op['ord'][$i]['qty'] * $ops['USD'];


			# 如果沒有 工廠工繳時
			if( $op['ord'][$i]['fty_cm'] == 0 )
			{
				if($op['ord'][$i]['ie2'] == 0 ) {
					$op['ord'][$i]['fty_cm'] = $op['ord'][$i]['ie1'] * $FTY_CM[$op['ord'][$i]['factory']];
				} else {
					$op['ord'][$i]['fty_cm'] = $op['ord'][$i]['ie2'] * $FTY_CM[$op['ord'][$i]['factory']];
				}
			}

			# Est. Total Cost of good sold 
			$ops['ord']['grand_cost']	= ( $ops['ord']['sub_tal'] + $ops['ord']['other_tremt'] + $ops['ord']['other'] + $op['ord'][$i]['cm'] ) * $op['ord'][$i]['qty'];
			$ops['rcv']['grand_cost']	= ( $ops['rcv']['sub_tal'] + $ops['rcv']['other_tremt'] + $ops['ord']['other'] + $op['ord'][$i]['fty_cm'] ) * $op['ord'][$i]['qty'];

			# G.M.
			$ops['ord']['gm'] 	= $op['ord'][$i]['sales'] - $ops['ord']['grand_cost'];
			$ops['rcv']['gm']	= $ops['rcv']['sales'] - $ops['rcv']['grand_cost'];

			# different G.M. rate (%) 和訂單	gm rate 差
			$ops['ord']['gm_rate'] = ( $ops['ord']['gm'] / $op['ord'][$i]['sales'] ) * 100 ;

			if ($op['ord'][$i]['sales']) {
				$op['order'][$i]['rcv_gmr'] = ( $ops['rcv']['gm']	/ $ops['rcv']['sales'] ) * 100;
			} else {
				$op['order'][$i]['rcv_gmr'] = '';			
			}
			
			$op['order'][$i]['gm_rate'] = $ops['ord']['gm_rate'];
			$op['order'][$i]['order_cost'] = $ac_costs = $order->get_ac_cost($op['ord'][$i]['order_num']);
			if(!$ac_costs)
			{
				//true
				$op['order'][$i]['cost_rate']=0;
			}
			else
			{
				//false
				$op['order'][$i]['cost_rate']=(($op['order'][$i]['order_cost']['Total_sales'] - (($op['order'][$i]['order_cost']['acc']+$op['order'][$i]['order_cost']['fab']+$op['order'][$i]['order_cost']['special']+$op['order'][$i]['order_cost']['cm'])*$op['order'][$i]['order_cost']['qty']))/$op['order'][$i]['order_cost']['Total_sales'])*100;
			}


		
			// $ord['order'] = $order->get($op['ord'][$i]['id']);  //取出該筆記錄 rcv_gmr
			// $ord = $order->orgainzation_ord($ord);
			// $op['order'][$i] = $ord['order'];
			// $op['order'][$i]['status'] = get_ord_status($ord['order']['status']);
			// $op['order'][$i]['cust_name'] = $op['ord'][$i]['cust_name'];
			// if($op['order'][$i]['rel_cm_cost'] == 0) $op['order'][$i]['rel_cm_cost'] = $cost->add_ord_cm_cost($op['order'][$i]['order_num']);	
			// if(($op['order'][$i]['rel_mat_cost'] > 0 || $op['order'][$i]['mat_u_cost'] == 0) && $op['order'][$i]['rel_acc_cost'] > 0)
			// {
			

				// $rcv_other =  NUMBER_FORMAT((( $op['order'][$i]['quota_fee'] + $op['order'][$i]['comm_fee'] ) ),2);
		
				// if( !isset($op['order'][$i]['ship_fob'] ) || $op['order'][$i]['ship_fob'] == 0 ) $op['order'][$i]['ship_fob'] = $op['order'][$i]['uprice'];
				// if($op['order'][$i]['fty_cm'] == 0) $op['order'][$i]['fty_cm'] = $op['order'][$i]['ie1'] * $FTY_CM[$op['order'][$i]['factory']];
	
				// $rcv_grand_cost	= $op['order'][$i]['rel_mat_cost'] + $op['order'][$i]['rel_acc_cost'] + $op['order'][$i]['rel_cm_cost'] + ( $rcv_other + $op['order'][$i]['fty_cm'] ) * $op['order'][$i]['qty'] + $op['order'][$i]['smpl_fee'];
	
				// $rcv_gm = ( $op['order'][$i]['ship_fob'] * $op['order'][$i]['qty'] ) - $rcv_grand_cost;
				
				// $op['order'][$i]['rcv_gmr'] = ( $rcv_gm / (($op['order'][$i]['ship_fob'] *  $op['order'][$i]['qty']))) * 100;		
				
			// }
		}

		$op['msg'] = $order->msg->get(2);
		$op['back_str'] = "&SCH_ship=".$SCH_ship."&SCH_ord=".$SCH_ord."&SCH_cust=".$SCH_cust."&SCH_status=".$SCH_status."&SCH_fty=".$SCH_fty;
		page_display($op,'058', $TPL_ORD_COST_LIST);    	    
		break;		
		
		

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "ord_cost_show":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "ord_cost_show3":
check_authority('065',"view");

$ord['order'] = $order->get($PHP_id);  //取出該筆記錄
$wi_rec = $wi->get(0, $ord['order']['order_num']); //取出wi記錄
$acc_ap = $lots_ap = 0;
$op['po_check'] = $op['rcv_check'] = 0;
$ord = $order->orgainzation_ord($ord);

$lots_ap = $bom->get_aply_quck($wi_rec['id'], 'bom_lots');	
$acc_ap = $bom->get_aply_quck($wi_rec['id'], 'bom_acc');	

if($ord['order']['po_mat_cost'] == 0)
{		
	if($lots_ap > 0)$ord['order']['po_mat_cost'] = $po->add_lots_cost($ord['order']['order_num'],$wi_rec['id']);							
}
if($ord['order']['po_acc_cost'] == 0)
{		
	if($acc_ap > 0)$ord['order']['po_acc_cost'] = $po->add_acc_cost($ord['order']['order_num'],$wi_rec['id']);							
}	

if($acc_ap == 2 && $lots_ap == 2) $op['po_check'] = 1;
if($op['po_check'] == 1) $op['rcv_check'] = $receive->check_rcvd($ord['order']['order_num']);	

if($ord['order']['rel_mat_cost'] == 0 && $op['rcv_check'] > 0)
{
	// $mat_date = $order->get_field_value('mat_shp','',$ord['order']['order_num'],'pdtion');
	// if($mat_date && $mat_date <> '0000-00-00')
	$ord['order']['rel_mat_cost'] = $receive->add_ord_cost($ord['order']['order_num'],'l');
}

if($ord['order']['rel_acc_cost'] == 0 && $op['rcv_check'] > 0)
{
	// $macc_date = $order->get_field_value('m_acc_shp','',$ord['order']['order_num'],'pdtion');
	// $acc_date = $order->get_field_value('acc_shp','',$ord['order']['order_num'],'pdtion');
	// $acc_etd = $order->get_field_value('acc_etd','',$ord['order']['order_num'],'pdtion');
	// if(($macc_date && $macc_date <> '0000-00-00') && (!$acc_etd || ($acc_date && $acc_date <> '0000-00-00')) )			
	$ord['order']['rel_acc_cost'] = $receive->add_ord_cost($ord['order']['order_num'],'a');
}

if($ord['order']['rel_cm_cost'] == 0) $ord['order']['rel_cm_cost'] = $cost->add_ord_cm_cost($ord['order']['order_num']);


$acc_cost = $receive->get_bom_acc_cost($ord['order']['order_num'],$wi_rec['id']);
$lots_cost = $receive->get_bom_lots_cost($ord['order']['order_num'],$wi_rec['id']);

# 沒有用到
// $op['fty_source_chk'] = $order->check_sourceing($ord['order']['order_num'],1);
// $op['cust_source_chk'] = $order->check_sourceing($ord['order']['order_num'],2);


//	$acc_ap = $bom->get_aply($wi_rec['id'], 'bom_acc');
//	$lots_ap = $bom->get_aply($wi_rec['id'], 'bom_lots');


if($acc_ap == 2 && $lots_ap == 2) $op['po_check'] = 1;
// echo $acc_ap."===>".$lots_ap;
if($op['po_check'] == 1) $op['rcv_check'] = $receive->check_rcvd($ord['order']['order_num']);

// Fabric cost per unit 
$op['ord']['lots'] = ( $ord['order']['mat_u_cost'] * $ord['order']['mat_useage'] + $ord['order']['fusible'] + $ord['order']['interline'] ) * $cury_d['USD']['NTD'];
$op['bom']['lots'] = NUMBER_FORMAT(($lots_cost['bom'] / $ord['order']['qty']),2);
$op['po']['lots'] = NUMBER_FORMAT(($ord['order']['po_mat_cost'] * $cury_d['USD']['NTD'] / $ord['order']['qty']),2);
$op['rcv']['lots'] = NUMBER_FORMAT(($ord['order']['rel_mat_cost'] * $cury_d['USD']['NTD'] / $ord['order']['qty']),2);

// Accessory cost per unit 
$op['ord']['acc'] = $ord['order']['acc_u_cost'] * $cury_d['USD']['NTD'];
$op['bom']['acc'] = NUMBER_FORMAT(($acc_cost['bom'] / $ord['order']['qty']),2);
$op['po']['acc'] = NUMBER_FORMAT(($ord['order']['po_acc_cost'] * $cury_d['USD']['NTD'] / $ord['order']['qty']),2);
$op['rcv']['acc'] = NUMBER_FORMAT(($ord['order']['rel_acc_cost'] * $cury_d['USD']['NTD'] / $ord['order']['qty']),2);

// Sub. Total 
$op['ord']['sub_tal'] = $op['ord']['lots'] + $op['ord']['acc'];
$op['bom']['sub_tal'] = $op['bom']['lots'] + $op['bom']['acc'];
$op['po']['sub_tal']  = $op['po']['lots'] + $op['po']['acc'];
$op['rcv']['sub_tal'] = $op['rcv']['lots'] + $op['rcv']['acc'];

// Other treatment 
$op['ord']['other_tremt'] = NUMBER_FORMAT((( $ord['order']['emb'] + $ord['order']['wash'] + $ord['order']['oth'] ) *$cury_d['USD']['NTD']),2);
$op['rcv']['other_tremt'] = NUMBER_FORMAT(($ord['order']['rel_cm_cost'] * $cury_d['USD']['NTD'] / $ord['order']['qty']),2);

// Others
$op['ord']['other'] =  NUMBER_FORMAT((( $ord['order']['quota_fee'] + $ord['order']['comm_fee'] ) * $cury_d['USD']['NTD']),2);

if(!isset($ord['order']['ship_fob']) || $ord['order']['ship_fob'] == 0)$ord['order']['ship_fob'] = $ord['order']['uprice'];
$ord['order']['sales'] = $ord['order']['sales'] * $cury_d['USD']['NTD'];
$op['rcv']['sales'] = $ord['order']['ship_fob'] *  $ord['order']['qty'] * $cury_d['USD']['NTD'];

$ord['order']['smpl_fee']*= $cury_d['USD']['NTD'];

if($ord['order']['fty_cm'] == 0)
{
	$ord['order']['fty_cm'] = $ord['order']['ie1'] * $FTY_CM[$ord['order']['factory']];		
}

// C.M.
$ord['order']['fty_cm'] *= $cury_d['USD']['NTD'];
$ord['order']['fty_cm'] = NUMBER_FORMAT($ord['order']['fty_cm'],2);
$ord['order']['cm'] *= $cury_d['USD']['NTD'];

// Est. Total Cost of good sold 
$op['ord']['grand_cost']	= $ord['order']['grand_cost'] * $cury_d['USD']['NTD'];
$op['bom']['grand_cost']	= ( $op['bom']['lots']+$op['bom']['acc']+$op['ord']['other_tremt'] + $op['ord']['other'] + $ord['order']['fty_cm'])*$ord['order']['qty'] +$ord['order']['smpl_fee'];
$op['po']['grand_cost']	= ( $op['po']['lots']+$op['po']['acc']+$op['ord']['other_tremt'] + $op['ord']['other'] + $ord['order']['fty_cm'])*$ord['order']['qty'] +$ord['order']['smpl_fee'];
$op['rcv']['grand_cost']	= ( $op['rcv']['lots'] + $op['rcv']['acc'] + $op['rcv']['other_tremt'] + $op['ord']['other'] + $ord['order']['fty_cm'] ) * $ord['order']['qty'] + $ord['order']['smpl_fee'];

// G.M.
$op['ord']['gm'] = $ord['order']['gm'] * $cury_d['USD']['NTD'];
$op['bom']['gm'] = $ord['order']['sales'] - $op['bom']['grand_cost'];
$op['po']['gm'] = $ord['order']['sales'] - $op['po']['grand_cost'];
$op['rcv']['gm'] = $op['rcv']['sales'] - $op['rcv']['grand_cost'];

// different G.M. rate (%) 和訂單	gm rate 差
$op['ord']['gm_rate'] = ($op['ord']['gm'] /($ord['order']['sales'] ))*100 ;

if ($ord['order']['sales']) {
	$op['bom']['gm_rate'] = ($op['bom']['gm']/ ($ord['order']['sales']))*100;
	$op['po']['gm_rate'] = ($op['po']['gm']/ ($ord['order']['sales']))*100;
	$op['rcv']['gm_rate'] = ($op['rcv']['gm']/ ($op['rcv']['sales']))*100;
} else {
	$op['po']['gm_rate'] = 0;
	$op['bom']['gm_rate'] = 0;
	$op['rcv']['gm_rate'] = 0;			
}

$op['bom']['dif_gm'] = $op['bom']['gm_rate'] - $op['ord']['gm_rate'];
$op['po']['dif_gm'] = $op['po']['gm_rate'] - $op['ord']['gm_rate'];
$op['rcv']['dif_gm'] = $op['rcv']['gm_rate'] - $op['ord']['gm_rate'];

$op['order'] = $ord['order'];

page_display($op, '065', $TPL_ORD_COST_VIEW);
break;





#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "Remun":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "ord_cost_show3":
 	check_authority('065',"view");
	$ord['order'] = $order->get($PHP_id);  //取出該筆記錄
	$wi_rec = $wi->get(0, $ord['order']['order_num']); //取出wi記錄

	$ord = $order->orgainzation_ord($ord);

	$acc_cost = $receive->get_order_acc_cost($ord['order']['order_num'],$wi_rec['id']);
	$lots_cost = $receive->get_order_lots_cost($ord['order']['order_num'],$wi_rec['id']);

	$op['fty_source_chk'] = $order->check_sourceing($ord['order']['order_num'],1);
	$op['cust_source_chk'] = $order->check_sourceing($ord['order']['order_num'],2);
	
	$op['po_check'] = $op['rcv_check'] = 0;
	$acc_ap = $bom->get_aply($wi_rec['id'], 'bom_acc');
	$lots_ap = $bom->get_aply($wi_rec['id'], 'bom_lots');
	
	if($acc_ap == 1 && $lots_ap == 1) $op['po_check'] = 1;

	if($op['po_check'] == 1) $op['rcv_check'] = $receive->check_rcvd($ord['order']['order_num']);
	
	$op['ord']['lots'] = ($ord['order']['mat_u_cost'] * $ord['order']['mat_useage'] + $ord['order']['fusible'] + $ord['order']['interline']) *$cury_d['USD']['NTD'];
	$op['bom']['lots'] = NUMBER_FORMAT(($lots_cost['bom'] / $ord['order']['qty']),2);
	$op['po']['lots'] = NUMBER_FORMAT(($lots_cost['po'] / $ord['order']['qty']),2);
	$op['rcv']['lots'] = NUMBER_FORMAT(($lots_cost['rcv'] / $ord['order']['qty']),2);

	$op['ord']['acc'] = $ord['order']['acc_u_cost'] *$cury_d['USD']['NTD'];
	$op['bom']['acc'] = NUMBER_FORMAT(($acc_cost['bom'] / $ord['order']['qty']),2);
	$op['po']['acc'] = NUMBER_FORMAT(($acc_cost['po'] / $ord['order']['qty']),2);
	$op['rcv']['acc'] = NUMBER_FORMAT(($acc_cost['rcv'] / $ord['order']['qty']),2);
	
	$op['ord']['sub_tal'] = $op['ord']['lots'] +$op['ord']['acc'];
	$op['bom']['sub_tal'] = $op['bom']['lots'] + $op['bom']['acc'];
	$op['po']['sub_tal']  = $op['po']['lots'] + $op['po']['acc'];
	$op['rcv']['sub_tal'] = $op['rcv']['lots'] + $op['rcv']['acc'];
	
	$op['ord']['other_tremt'] = NUMBER_FORMAT((( $ord['order']['emb'] + $ord['order']['wash'] + $ord['order']['oth'] ) *$cury_d['USD']['NTD']),2);
	$op['ord']['other'] =  NUMBER_FORMAT((( $ord['order']['quota_fee'] + $ord['order']['comm_fee'] ) *$cury_d['USD']['NTD']),2);
		
	$ord['order']['sales'] = $ord['order']['sales'] * $cury_d['USD']['NTD'];
	$ord['order']['smpl_fee']*= $cury_d['USD']['NTD'];
	if($ord['order']['fty_cm'] == 0)
	{
		$ord['order']['fty_cm'] = $ord['order']['ie1'] * $FTY_CM[$ord['order']['factory']];		
	}
	$ord['order']['fty_cm'] *= $cury_d['USD']['NTD'];
	$ord['order']['fty_cm'] = NUMBER_FORMAT($ord['order']['fty_cm'],2);
	$ord['order']['cm'] *= $cury_d['USD']['NTD'];
	
	$op['ord']['grand_cost']	= $ord['order']['grand_cost'] *$cury_d['USD']['NTD'];
	$op['bom']['grand_cost']	= ($op['bom']['lots']+$op['bom']['acc']+$op['ord']['other_tremt'] + $op['ord']['other'] + $ord['order']['fty_cm'])*$ord['order']['qty'] +$ord['order']['smpl_fee'];
	$op['po']['grand_cost']	=   ($op['po']['lots']+$op['po']['acc']+$op['ord']['other_tremt'] + $op['ord']['other'] + $ord['order']['fty_cm'])*$ord['order']['qty'] +$ord['order']['smpl_fee'];
	$op['rcv']['grand_cost']	= ($op['rcv']['lots']+$op['rcv']['acc']+$op['ord']['other_tremt'] + $op['ord']['other'] + $ord['order']['fty_cm'])*$ord['order']['qty'] +$ord['order']['smpl_fee'];
	
	$op['ord']['gm'] = $ord['order']['gm'] *$cury_d['USD']['NTD'];
	$op['bom']['gm'] = $ord['order']['sales']  - $op['bom']['grand_cost'];
	$op['po']['gm'] = $ord['order']['sales']  - $op['po']['grand_cost'];
	$op['rcv']['gm'] = $ord['order']['sales'] - $op['rcv']['grand_cost'];

	$op['ord']['gm_rate'] = ($op['ord']['gm'] /($ord['order']['sales'] ))*100 ;
	if ($ord['order']['sales']){
		$op['bom']['gm_rate'] = ($op['bom']['gm']/ ($ord['order']['sales']))*100;
		$op['po']['gm_rate'] = ($op['po']['gm']/ ($ord['order']['sales']))*100;
		$op['rcv']['gm_rate'] = ($op['rcv']['gm']/ ($ord['order']['sales']))*100;
	}else{
		$op['po']['gm_rate'] = 0;
		$op['bom']['gm_rate'] = 0;
		$op['rcv']['gm_rate'] = 0;			
	}
//和訂單	gm rate 差
	$op['bom']['dif_gm'] = $op['bom']['gm_rate'] - $op['ord']['gm_rate'];
	$op['po']['dif_gm'] = $op['po']['gm_rate'] - $op['ord']['gm_rate'];
	$op['rcv']['dif_gm'] = $op['rcv']['gm_rate'] - $op['ord']['gm_rate'];
	
	$op['order'] = $ord['order'];
	

	
	page_display($op, '065', $TPL_ORD_COST_VIEW);
	break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "ord_cost_show":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ 
case "ord_cost_show":
check_authority('065',"view");
$op['status']=$_GET['revise'];
$op['PHP_id']=$_GET['PHP_id'];
$ord['order'] = $ords = $order->get_cost($PHP_id);
$op['ord_num'] = $ords['order_num'];

$ac_cost['order'] = $ac_costs = $order->get_ac_cost($ords['order_num']);
if($ac_costs['rate'] <=0)
{
	$ac_costs['rate']=0;
}
$Cost = $bom->get_cost($ords['wi_id'],$ords['qty'],$ords['bcfm_date'],$ords['opendate']);

$op['ship'] = $ship = $shipping_doc->get_cost($ords['order_num']);
// print_r($op['ship']);
$op['USD'] = $GLOBALS['rate']->get_rate('USD',$Cost['rate_date']);
$op['HKD'] = $GLOBALS['rate']->get_rate('HKD',$Cost['rate_date']);
$op['EUR'] = $GLOBALS['rate']->get_rate('EUR',$Cost['rate_date']);

$op['bom_lots_check'] = $Cost['ck_bom_lots'] == $Cost['ck_bom_lots_qty'] ? 1 : 0 ;
$op['bom_acc_check'] = $Cost['ck_bom_acc'] == $Cost['ck_bom_acc_qty'] ? 1 : 0 ;
$op['po_lots_check'] = $Cost['ck_bom_lots'] == $Cost['ck_po_lots_qty'] ? 1 : 0 ;
$op['po_acc_check'] = $Cost['ck_bom_acc'] == $Cost['ck_po_acc_qty'] ? 1 : 0 ;
if( !empty($Cost['lots_po_ttl']) ) $op['po_check'] = 1;
if( !empty($Cost['lots_rcv_ttl']) ) $op['rcv_check'] = 1;
if( !empty($ship['ttl_qty']) ) $op['ship_check'] = 1;

// Fabric cost per unit  $Cost['rate_date']
//取出 handling fee 的值 ( LY 才需要)
// if($ords['factory'] == 'LY'){
	// $handling_row = $order->get_fields("set_value", " where set_name='handling_fee'","para_set");
	// $handling_fee = $handling_row[0];
// }else{
	// $handling_fee = 0;
// }
$op['ord']['lots'] = ( ( $ords['mat_useage'] * $ords['mat_u_cost'] ) + $ords['fusible'] + $ords['interline'] + $ords['handling_fee'] ) * $op['USD'];
$op['bom']['lots'] = $Cost['lots_bom_ttl'] / $ords['qty'] ;
$op['po']['lots'] = $Cost['lots_po_ttl'] / $ords['qty'] ;
$op['rcv']['lots'] = $Cost['lots_rcv_ttl'] / $ords['qty'] ;

// Accessory cost per unit 
$op['ord']['acc'] = $ords['acc_u_cost'] * $op['USD'];
$op['bom']['acc'] = $Cost['acc_bom_ttl'] / $ords['qty'] ;
$op['po']['acc'] = $Cost['acc_po_ttl'] / $ords['qty'] ;
$op['rcv']['acc'] = $Cost['acc_rcv_ttl'] / $ords['qty'] ;

// Sub. Total 
$op['ord']['sub_tal'] = $op['ord']['lots'] + $op['ord']['acc'];
$op['bom']['sub_tal'] = $op['bom']['lots'] + $op['bom']['acc'];
$op['po']['sub_tal']  = $op['po']['lots'] + $op['po']['acc'];
$op['rcv']['sub_tal'] = $op['rcv']['lots'] + $op['rcv']['acc'];
$op['output']['sub_tal'] =$ac_costs['fab']+$ac_costs['acc'];
// Other treatment 
$op['ord']['other_tremt'] = ( $ords['emb'] + $ords['wash'] + $ords['oth'] ) * $op['USD'];
$op['rcv']['other_tremt'] = ( $ords['rel_cm_cost'] * $op['USD'] ) / $ords['qty'] ;

// Others
$op['ord']['other'] = ( $ords['quota_fee'] + $ords['comm_fee'] ) * $op['USD'];

// C.M.
$ords['fty_cm'] *= $op['USD'];
$ords['cm'] *= $op['USD'];

// Est. Total Sales of this order
if(!isset($ords['ship_fob']) || $ords['ship_fob'] == 0)$ords['ship_fob'] = $ords['uprice'];
$ords['sales'] = $ords['uprice'] * $ords['qty'] * $op['USD'];
$op['rcv']['sales'] = $ords['ship_fob'] * $ords['qty'] * $op['USD'];
//$op['output']['sales'] =$ac_costs['fob'] * $ac_costs['qty'] * $ac_costs['rate'];


# 如果沒有 工廠工繳時
if( $ords['fty_cm'] == 0 )
{
	if($ords['ie2'] == 0 ) {
		$ords['fty_cm'] = $ords['ie1'] * $FTY_CM[$ords['factory']]*= $op['USD'];
	} else {
		$ords['fty_cm'] = $ords['ie2'] * $FTY_CM[$ords['factory']]*= $op['USD'];
	}
}

// Est. Total Cost of good sold 
$op['ord']['grand_cost']	= ( $op['ord']['sub_tal'] + $op['ord']['other_tremt'] + $op['ord']['other'] + $ords['cm'] ) * $ords['qty'];
$op['bom']['grand_cost']	= ( $op['bom']['sub_tal'] + $op['ord']['other_tremt'] + $op['ord']['other'] + $ords['fty_cm'] ) * $ords['qty'];
$op['po']['grand_cost']		= ( $op['po']['sub_tal'] + $op['ord']['other_tremt'] + $op['ord']['other'] + $ords['fty_cm'] ) * $ords['qty'];
$op['rcv']['grand_cost']	= ( $op['rcv']['sub_tal'] + $op['rcv']['other_tremt'] + $op['ord']['other'] + $ords['fty_cm'] ) * $ords['qty'];
$op['output']['grand_cost'] = ( $op['output']['sub_tal'] + $ac_costs['special'] + $ac_costs['cm'] ) * $ac_costs['qty'];
// G.M.
$op['ord']['gm'] 	= $ords['sales'] - $op['ord']['grand_cost'];
$op['bom']['gm'] 	= $ords['sales'] - $op['bom']['grand_cost'];
$op['po']['gm'] 	= $ords['sales'] - $op['po']['grand_cost'];
$op['rcv']['gm']	= $op['rcv']['sales'] - $op['rcv']['grand_cost'];
$op['output']['gm']	= $ac_costs['Total_sales'] - $op['output']['grand_cost'];
// different G.M. rate (%) 和訂單	gm rate 差
$op['ord']['gm_rate'] = ( $op['ord']['gm'] / $ords['sales'] ) * 100 ;

if ($ords['sales']) {
	$op['bom']['gm_rate']	= ( $op['bom']['gm']	/ $ords['sales'] ) * 100;
	$op['po']['gm_rate'] 	= ( $op['po']['gm']		/ $ords['sales'] ) * 100;
	$op['rcv']['gm_rate']	= ( $op['rcv']['gm']	/ $op['rcv']['sales'] ) * 100;
	$op['output']['gm_rate']	= ( $op['output']['gm']	/ $ac_costs['Total_sales']) * 100;
} else {
	$op['po']['gm_rate'] = 0;
	$op['bom']['gm_rate'] = 0;
	$op['rcv']['gm_rate'] = 0;		
	$op['output']['gm_rate'] = 0;		
}

$op['bom']['dif_gm']	= $op['bom']['gm_rate']	- $op['ord']['gm_rate'];
$op['po']['dif_gm']		= $op['po']['gm_rate']	- $op['ord']['gm_rate'];
$op['rcv']['dif_gm']	= $op['rcv']['gm_rate']	- $op['ord']['gm_rate'];
$op['output']['dif_gm']	= $op['output']['gm_rate']	- $op['ord']['gm_rate'];

$op['order'] = $ords;
$ac_costs['remark'] = trim($ac_costs['remark']);
$op['ac_cost'] =$ac_costs;
$op['lots_stock'] = $Cost['lots_stock'];
$op['acc_stock'] = $Cost['acc_stock'];

page_display($op, '065', $TPL_ORD_COST_VIEW);
break;
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "cost_value_insert":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "cost_value_insert":

if($db_exist=='i')
{
	$q_str="INSERT INTO ac_cost(";
	
	$q_str1="order_num";
	$q_str2=") VALUES('".$order_num."'";
	$q_str3=")";
	
	if($output_qty!='')
	{
		$q_str1.=",qty";
		$q_str2.=",".$output_qty;
	}
	if($output_fob!='')
	{
		$q_str1.=",fob";
		$q_str2.=",".$output_fob;
	}
	if($cost_fab!='')
	{
		$q_str1.=",fab";
		$q_str2.=",".$cost_fab;
	}
	if($cost_acc!='')
	{
		$q_str1.=",acc";
		$q_str2.=",".$cost_acc;
	}
	if($cost_spec!='')
	{
		$q_str1.=",special";
		$q_str2.=",".$cost_spec;
	}
	if($cost_cm!='')
	{
		$q_str1.=",cm";
		$q_str2.=",".$cost_cm;
	}
	if($cost_other!='')
	{
		$q_str1.=",other";
		$q_str2.=",".$cost_other;
	}
	if($us_rate!='')
	{
		$q_str1.=",rate";
		$q_str2.=",".$us_rate;
	}
	if($desc!='')
	{
		$q_str1.=",remark";
		$q_str2.=",'".$desc."'";
	}
	if($Total_sales!='')
	{
		$q_str1.=",Total_sales";
		$q_str2.=",".$Total_sales;
	}
	$q_str=$q_str.$q_str1.$q_str2.$q_str3;
	//echo $q_str;
	//exit;
	$order->creat_ac_cost($q_str);
	
	
}
else
{
	$q_index=1;
	$q_str="UPDATE ac_cost SET ";
	$q_str1="";
	$q_where=" where order_num='".$order_num."'";
	if($output_qty=='')
	{
		$q_str1="qty=0";
		($output_fob=='')? $q_str1.=",fob=0":$q_str1.=",fob=".$output_fob;
		($cost_fab=='')?$q_str1.=",fab=0":$q_str1.=",fab=".$cost_fab;
		($cost_acc=='')?$q_str1.=",acc=0":$q_str1.=",acc=".$cost_acc;
		($cost_spec=='')?$q_str1.=",special=0":$q_str1.=",special=".$cost_spec;
		($cost_cm=='')?$q_str1.=",cm=0":$q_str1.=",cm=".$cost_cm;
		($cost_other=='')?$q_str1.=",other=0":$q_str1.=",other=".$cost_other;
		($us_rate=='')?$q_str1.=",rate=0":$q_str1.=",rate=".$us_rate;
		($desc=='')?$q_str1.=",remark=''":$q_str1.=",remark='".$desc."'";
		($Total_sales=='')?$q_str1.=",Total_sales=0":$q_str1.=",Total_sales=".$Total_sales;
		
	}
	else
	{
		$q_str1="qty=".$output_qty;
		($output_fob=='')? $q_str1.=",fob=0":$q_str1.=",fob=".$output_fob;
		($cost_fab=='')?$q_str1.=",fab=0":$q_str1.=",fab=".$cost_fab;
		($cost_acc=='')?$q_str1.=",acc=0":$q_str1.=",acc=".$cost_acc;
		($cost_spec=='')?$q_str1.=",special=0":$q_str1.=",special=".$cost_spec;
		($cost_cm=='')?$q_str1.=",cm=0":$q_str1.=",cm=".$cost_cm;
		($cost_other=='')?$q_str1.=",other=0":$q_str1.=",other=".$cost_other;
		($us_rate=='')?$q_str1.=",rate=0":$q_str1.=",rate=".$us_rate;
		($desc=='')?$q_str1.=",remark=''":$q_str1.=",remark='".$desc."'";
		($Total_sales=='')?$q_str1.=",Total_sales=0":$q_str1.=",Total_sales=".$Total_sales;
	}
	
	$q_str=$q_str.$q_str1.$q_where;

	$order->creat_ac_cost($q_str);
	
	
}
$redir_str = "cost_analysis.php?PHP_action=ord_cost_show&PHP_id=".$PHP_id."&revise=0";
redirect_page($redir_str); 
//-------------------------------------------------------------------------

}   // end case ---------

?>
