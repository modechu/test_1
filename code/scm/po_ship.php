<?php

session_start();
session_register	('SCACHE');
session_register	('PAGE');
session_register	('authority');
session_register	('where_str');
session_register	('parm');
session_register	('PHP_ses_etd');
session_register	('PHP_unstatus');
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

require_once "config.php";
require_once "config.admin.php";

$PHP_SELF = $_SERVER['PHP_SELF'];
$perm = $GLOBALS['power'];

require_once "init.object.php";

$op = array();
$ex_cmpy = array('DHL','FedEx');

// if ( $_SESSION['SCACHE']['ADMIN']['dept'] != 'SA' ) {
	// page_display($op, '000','sorry.html');
	// exit;
// }

// echo $PHP_action.'<br>';

switch ($PHP_action) {
#++++++++++++++    SAMPLE ORDER +++++++++++++  2007/09/11  +++++++++++++++++
#		 job 54    請購記錄 
#++++++++++++++++++++++++++++++++++++++++++++  2007/09/11  +++++++++++++++++
#		case "po_ship":										job 53		採購出口--搜尋畫面
#		case "po_ship_search":						job 53		採購出口--搜尋列表
#
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_ship":			job 53
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "po_ship":
check_authority('039',"view");

$op['org'] = $arry2->select($SHIP2,'','SCH_org','select'); 
$op['dist'] = $arry2->select($DIST,'','SCH_dist','select');
$op['ship_way'] = $arry2->select($PO_SHIP,'','SCH_way','select');

$note=$notify->search($GLOBALS['SCACHE']['ADMIN']['login_id'], "`read`=0");
$op['max_notify'] = $note['max_no'];
    
page_display($op, '039', $TPL_PO_SHIP);			    	    
break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "ship_add":			job 53
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "ship_add":
check_authority('039',"add");

$op['org'] = $arry2->select($SHIP2,'','PHP_org','select'); 
$op['dist'] = $arry2->select($DIST,'','PHP_dist','select');
$op['ship_way'] = $arry2->select($PO_SHIP,'','PHP_ship_way','select','ex_select(this)');
$op['ex_cmpy'] = $arry2->select($ex_cmpy,'','PHP_ex_cmpy','select');
$op['today']	= date('Ymd');

page_display($op, '039', $TPL_PO_SHIP_ADD);			    	    
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_search":			job 53
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "po_search":
check_authority('039',"view");

$where_str="order by cust_s_name"; //依cust_s_name排序
$cust_def = $cust->get_fields('cust_init_name',$where_str);
$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//取出客戶代號
for ($i=0; $i< sizeof($cust_def); $i++)
{
    $cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//以 [客戶代號-客戶簡稱] 呈現
}

$op['factory_select'] = $arry2->select($SHIP,'','PHP_SCH_fty','select','');
$op['cust_sch_select'] =  $arry2->select($cust_value,'','PHP_SCH_cust','select','',$cust_def_vue); 

$op['msg'] = $order->msg->get(2);

page_display($op, '039', $TPL_PO_SHIP_ADD_LIST);			    	    
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_search":			job 53
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_po_search":
check_authority('039',"view");

if(!isset($SCH_ord))$SCH_ord='';
if($SCH_ord == '')
{
    $op = $po->search(3);
}else{
    $op = $po->search_ord_po();
}	


$where_str="order by cust_s_name"; //依cust_s_name排序
$cust_def = $cust->get_fields('cust_init_name',$where_str);
$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//取出客戶代號
for ($i=0; $i< sizeof($cust_def); $i++)
{
    $cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//以 [客戶代號-客戶簡稱] 呈現
}

$op['factory_select'] = $arry2->select($SHIP,'','PHP_SCH_fty','select','');
$op['cust_sch_select'] =  $arry2->select($cust_value,'','PHP_SCH_cust','select','',$cust_def_vue); 

$op['msg'] = $order->msg->get(2);

$back_str="&PHP_SCH_num=".$PHP_SCH_num."&PHP_SCH_fty=".$PHP_SCH_fty."&PHP_SCH_cust=".$PHP_SCH_cust."&PHP_SCH_supl=".$PHP_SCH_supl."&PHP_sr_startno=".$PHP_sr_startno."&SCH_ord=".$SCH_ord;
$op['back_str']=$back_str;

page_display($op, '039', $TPL_PO_SHIP_ADD_LIST);			    	    
break;		



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_add_view":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "po_add_view":
check_authority('039',"add");	

$log_where = "AND item <> 'special'";	
$op = $po->get($PHP_ap_num,$log_where);

if (isset($op['ap_det'])) $op['ap_det2']=$po->grout_ap($op['ap_det']);


if (isset($PHP_SCH_num))
{
    $op['back_str']="&PHP_SCH_num=".$PHP_SCH_num."&PHP_SCH_fty=".$PHP_SCH_fty."&PHP_SCH_cust=".$PHP_SCH_cust."&PHP_SCH_supl=".$PHP_SCH_supl."&PHP_sr_startno=".$PHP_sr_startno."&SCH_ord=".$SCH_ord;
}



$ary=array('A','B','C','D','E','F','G','H','I');
for ($i=0; $i<sizeof($ary); $i++)
{
    if ($op['ap']['usance'] == $ary[$i])	$op['ap']['usance']=$usance[$i];
}
            
for ($i=0; $i< 4; $i++)
{
    if ($op['ap']['dm_way'] == $dm_way[1][$i]) $op['ap']['dm_way']=$dm_way[0][$i];
}

# 重組 TT payment
$re_dm_way = re_dm_way($op['ap']['dm_way']);
$op['ap']['dm_way'] = $re_dm_way[1];

if (isset($PHP_message)) $op['msg'][]=$PHP_message;
if (!isset($PHP_rev)) $PHP_rev='';

$det_size = sizeof($op['ap_det2']);
for($i=0;$i<$det_size;$i++){
    $op['ap_det2'][$i]['remain_qty'] = $op['ap_det2'][$i]['po_qty'] - $op['ap_det2'][$i]['ship_qty_total'];
    if ($op['ap_det2'][$i]['remain_qty'] < 0) $op['ap_det2'][$i]['remain_qty'] = 0;
}

page_display($op, '039', $TPL_PO_SHIP_ADD_SHOW);
break;


	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_ship_add":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_ship_add":
check_authority('039',"add");	

if(!$PHP_id) $PHP_id = $poship->add_main();	//沒有任何ship_det時，新增ship記錄
$parm = array(
    'ship_id'   => 	$PHP_id,
    'inv_num'	=>	$PHP_supl_inv,
    'po_id'		=>	$PHP_spare,
    'ap_num'    =>	$PHP_ap_num,
    'mat_cat'   =>	$PHP_mat_cat,
    'mat_id'    =>	$PHP_mat_id,
    'color'     =>	$PHP_color,
    'size'      =>	$PHP_size,
    'qty'		=>	$PHP_qty,
    'special'	=>	$PHP_special
);

$f1 = $poship->add_det($parm);
$op = $poship->get($PHP_id);

$op['tmp_inv'] = $PHP_supl_inv;
$op['org'] = $arry2->select($SHIP2,$op['po_ship']['org'],'PHP_org','select'); 
$op['dist'] = $arry2->select($DIST,$op['po_ship']['dist'],'PHP_dist','select');
$op['ship_way'] = $arry2->select($PO_SHIP,$op['po_ship']['ship_way'],'PHP_ship_way','select','ex_select(this)');
$op['ex_cmpy'] = $arry2->select($ex_cmpy,$op['po_ship']['ex_cmpy'],'PHP_ex_cmpy','select');
$op['today'] = date('Y-m-d');

page_display($op, '039', $TPL_PO_SHIP_ADD);
break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "del_det_ajax":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "del_det_ajax":
check_authority('039',"add");	

$f1 = $poship->del_det($PHP_id,$PHP_special);	
			
echo "Successfully delete record";
break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "ship_submit":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "ship_submit":
check_authority('039',"add");

$PHP_tw_rcv = (!empty($_POST['PHP_tw_rcv'])?$_POST['PHP_tw_rcv']:(!empty($_GET['PHP_tw_rcv'])?$_GET['PHP_tw_rcv']:''));
$PHP_qty = (!empty($_POST['PHP_qty'])?$_POST['PHP_qty']:(!empty($_GET['PHP_qty'])?$_GET['PHP_qty']:''));

#因為有些會沒有invoice 所以先拿掉
/* foreach($PHP_inv_num as $val){ //判斷invoice是否都有輸入
    if($val==''){
        $op = $poship->get($PHP_id);
        
        $op['msg'][] = "Please input Invoice !!!";
        $op['tmp_inv'] = $op['ship'][sizeof($op['ship'])-1]['inv_num'] ;
        $op['org'] = $arry2->select($SHIP2,$op['po_ship']['org'],'PHP_org','select'); 
        $op['dist'] = $arry2->select($DIST,$op['po_ship']['dist'],'PHP_dist','select');
        $op['ship_way'] = $arry2->select($PO_SHIP,$op['po_ship']['ship_way'],'PHP_ship_way','select','ex_select(this)');
        $op['ex_cmpy'] = $arry2->select($ex_cmpy,$op['po_ship']['ex_cmpy'],'PHP_ex_cmpy','select');
        $op['today']	= date('Y-m-d');

        page_display($op, '039', $TPL_PO_SHIP_ADD);
        break 2;
    }
} */

if(!isset($PHP_qty))
{
    $SCH_f_date = $SCH_e_date = $SCH_org = $SCH_dist = $SCH_way = $PHP_sr_startno ='';
    $op = $poship->search();
    $op['back_str'] = "&SCH_f_date=".$SCH_f_date."&SCH_e_date=".$SCH_e_date."&SCH_org=".$SCH_org."&SCH_dist=".$SCH_dist."&SCH_way=".$SCH_way."&PHP_sr_startno=".$PHP_sr_startno;				  	
    $op['msg'][]= "ship# [".$PHP_num."] was deleted";
    page_display($op, '039', $TPL_PO_SHIP_LIST);		
    break;	    	    
}

if(!$PHP_org)$PHP_org = $PHP_org2;
if(!$PHP_org || !$PHP_dist || !$PHP_ship_way || !$PHP_ship_date || !$PHP_ship_eta)
{
    $op = $poship->get($PHP_id);	
    $op['org'] = $arry2->select($SHIP2,$op['po_ship']['org'],'PHP_org','select'); 
    $op['dist'] = $arry2->select($DIST,$op['po_ship']['dist'],'PHP_dist','select');
    $op['ship_way'] = $arry2->select($PO_SHIP,$op['po_ship']['ship_way'],'PHP_ship_way','select','ex_select(this)');
    $op['ex_cmpy'] = $arry2->select($ex_cmpy,$op['po_ship']['ex_cmpy'],'PHP_ex_cmpy','select');

    $op['today']	= date('Y-m-d');
    $op['msg'][] = "Please Check input Item";
    page_display($op, '039', $TPL_PO_SHIP_ADD);
    break;
}


if($PHP_ship_way != 'Express') $PHP_ex_cmpy = '';
$parm = array(
    'ship_inv'	=>	$PHP_ship_inv,
    'org'		=> 	$PHP_org,
    'dist'		=>	$PHP_dist,
    'ship_way'	=>	$PHP_ship_way,
    'ex_cmpy'	=>	$PHP_ex_cmpy,
    'ship_date'	=>	$PHP_ship_date,
    'ship_eta'	=>	$PHP_ship_eta,
    'carrier'	=>	$PHP_carrier,
    'ver'		=>	($PHP_ver+1),
    'status'	=>	'2',
    'tw_rcv'	=>	$PHP_tw_rcv,
    'id'		=>	$PHP_id
);
$f1 = $poship->edit_main($parm);				

foreach( $PHP_qty as $key => $value) {
    $parm = array(
        'inv_num'	=>	$PHP_inv_num[$key],
        'qty'		=>	$PHP_qty[$key],
        'id'		=>	$key,
        'po_id'		=> 	$PHP_po_id[$key],
        'special'	=> 	$PHP_special[$key],
        'ship_way'	=>	$PHP_ship_way,								
        'ship_date'	=>	$PHP_ship_date,
        'ship_eta'	=>	$PHP_ship_eta
    );
    $f1 = $poship->edit_det($parm);										
    for($i=0; $i<sizeof($PHP_ord[$key]); $i++) {
        if($PHP_mat_cat[$key] == 'l') {
            $order->update_pdtfld_num('mat_rcv', $PHP_ship_eta, $PHP_ord[$key][$i]);
            $order->update_pdtfld_num('mat_ship_way', $PHP_ship_way, $PHP_ord[$key][$i]);
        } else {
            if($PHP_mat_cat[$key][$i] == '1') {
                $order->update_pdtfld_num('m_acc_rcv', $PHP_ship_eta, $PHP_ord[$key][$i]);
                $order->update_pdtfld_num('m_acc_ship_way', $PHP_ship_way, $PHP_ord[$key][$i]);					
            } else {
                $order->update_pdtfld_num('acc_rcv', $PHP_ship_eta, $PHP_ord[$key][$i]);
                $order->update_pdtfld_num('acc_ship_way', $PHP_ship_way, $PHP_ord[$key][$i]);						
            }
        }
    }
}

$op = $poship->get($PHP_id);	
$wi_id = array();
foreach($op['ship'] as $key => $val){
    $wi_id[$val['wi_id']] = $val['mat_cat'];
}
if($wi_id){
    foreach($wi_id as $key => $val){
        # RESET PO STATUS FOR PDTION
        $GLOBALS['order']->reset_pdtion($val,$key);
    }
}
//傳送PO Ship 完成報告
/*
$messg  = '<a href="po_ship.php?PHP_action=ship_view&PHP_id='.$PHP_id.'&PHP_sr_startno=0&SCH_f_date=&SCH_e_date=&SCH_org=&SCH_dist=&SCH_way=">'.$op['po_ship']['num'].'</a>';
$contxt = $poship->create_html($op);
$notify->system_msg_send_with_mail('5-3-S',$PHP_dist,$op['po_ship']['dist'],$messg,$contxt);
*/
$message = "Successfully submit SHIP : ".$op['po_ship']['num'];
$log->log_add(0,"039A",$message);

if($PHP_back_str)
{
    $op['back_str'] = $PHP_back_str;
}else{
    $op['back_str'] = "&SCH_f_date=&SCH_e_date=&SCH_org=&SCH_dist=&SCH_way=&PHP_sr_startno=0&SCH_carrier=";
}

page_display($op, '039', $TPL_PO_SHIP_SHOW);
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_ship_search":			job 53
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "po_ship_search":
check_authority('039',"view");

if(isset($SCH_org2) && $SCH_org2)$SCH_org = $SCH_org2;
$op = $poship->search();

$op['back_str'] = "&SCH_f_date=".$SCH_f_date."&SCH_e_date=".$SCH_e_date."&SCH_org=".$SCH_org."&SCH_dist=".$SCH_dist."&SCH_way=".$SCH_way."&PHP_sr_startno=".$PHP_sr_startno."&SCH_bl_num=".$SCH_bl_num."&SCH_inv=".$SCH_inv."&SCH_ship_num=".$SCH_ship_num."&SCH_shipper=".$SCH_shipper;

page_display($op, '039', $TPL_PO_SHIP_LIST);			    	    
break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "ship_view":			job 53
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "ship_view":
check_authority('039',"view");

$op = $poship->get($PHP_id);
$op['back_str'] = "&SCH_f_date=".$SCH_f_date."&SCH_e_date=".$SCH_e_date."&SCH_org=".$SCH_org."&SCH_dist=".$SCH_dist."&SCH_way=".$SCH_way."&PHP_sr_startno=".$PHP_sr_startno."&SCH_bl_num=".$SCH_bl_num."&SCH_inv=".$SCH_inv."&SCH_ship_num=".$SCH_ship_num."&SCH_shipper=".$SCH_shipper;
if(!empty($PHP_resize)) $op['resize'] = 1;

page_display($op, '039', $TPL_PO_SHIP_SHOW);			    	    
break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "unlock_apb":			job 53
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "unlock_apb":
check_authority('039',"add");

$poship->update_field( 'apb_rmk' , 0 , ' id = '.$PHP_ship_id.'' , 'po_ship_det');
$op = $poship->get($PHP_id);
$op['back_str'] = "&SCH_f_date=".$SCH_f_date."&SCH_e_date=".$SCH_e_date."&SCH_org=".$SCH_org."&SCH_dist=".$SCH_dist."&SCH_way=".$SCH_way."&PHP_sr_startno=".$PHP_sr_startno."&SCH_bl_num=".$SCH_bl_num."&SCH_inv=".$SCH_inv."&SCH_ship_num=".$SCH_ship_num."&SCH_shipper=".$SCH_shipper;
if(!empty($PHP_resize)) $op['resize'] = 1;
$message = "Successfully unlock_apb SHIP : ".$PHP_ship_inv;
$log->log_add(0,"039A",$message);

page_display($op, '039', $TPL_PO_SHIP_SHOW);			    	    
break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "lock_apb":			job 53
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "lock_apb":
check_authority('039',"add");

$poship->update_field( 'apb_rmk' , 1 , ' id = '.$PHP_ship_id.'' , 'po_ship_det');
$op = $poship->get($PHP_id);
$op['back_str'] = "&SCH_f_date=".$SCH_f_date."&SCH_e_date=".$SCH_e_date."&SCH_org=".$SCH_org."&SCH_dist=".$SCH_dist."&SCH_way=".$SCH_way."&PHP_sr_startno=".$PHP_sr_startno."&SCH_bl_num=".$SCH_bl_num."&SCH_inv=".$SCH_inv."&SCH_ship_num=".$SCH_ship_num."&SCH_shipper=".$SCH_shipper;
if(!empty($PHP_resize)) $op['resize'] = 1;
$message = "Successfully lock_apb SHIP : ".$PHP_ship_inv;
$log->log_add(0,"039A",$message);

page_display($op, '039', $TPL_PO_SHIP_SHOW);			    	    
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "ship_edit":			job 53
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "ship_edit":
check_authority('039',"add");

$op = $poship->get($PHP_id);
$op['org'] = $arry2->select($SHIP2,$op['po_ship']['org'],'PHP_org','select'); 
$op['dist'] = $arry2->select($DIST,$op['po_ship']['dist'],'PHP_dist','select');
$op['ship_way'] = $arry2->select($PO_SHIP,$op['po_ship']['ship_way'],'PHP_ship_way','select','ex_select(this)');
$op['ex_cmpy'] = $arry2->select($ex_cmpy,$op['po_ship']['ex_cmpy'],'PHP_ex_cmpy','select');

$op['back_str'] = $PHP_back_str;
// print_r($op);
page_display($op, '039', $TPL_PO_SHIP_ADD);			    	    
break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_revise":			job 53
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "po_revise":
check_authority('039',"add");

$poship->update_field_id('status','0',$PHP_id);
$op = $poship->get($PHP_id);
$op['org'] = $arry2->select($SHIP2,$op['po_ship']['org'],'PHP_org','select'); 
$op['dist'] = $arry2->select($DIST,$op['po_ship']['dist'],'PHP_dist','select');
$op['ship_way'] = $arry2->select($PO_SHIP,$op['po_ship']['ship_way'],'PHP_ship_way','select','ex_select(this)');
$op['ex_cmpy'] = $arry2->select($ex_cmpy,$op['po_ship']['ex_cmpy'],'PHP_ex_cmpy','select');
$op['back_str'] = "&SCH_f_date=".$SCH_f_date."&SCH_e_date=".$SCH_e_date."&SCH_org=".$SCH_org."&SCH_dist=".$SCH_dist."&SCH_way=".$SCH_way."&PHP_sr_startno=".$PHP_sr_startno."&SCH_carrier=".$SCH_carrier;

$wi_id = array();
foreach($op['ship'] as $key => $val){
    $wi_id[$val['wi_id']] = $val['mat_cat'];
}
if($wi_id){
    foreach($wi_id as $key => $val){
        # RESET PO STATUS FOR PDTION
        $GLOBALS['order']->reset_pdtion($val,$key);
    }
}

page_display($op, '039', $TPL_PO_SHIP_ADD);			    	    
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "ship_print":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "ship_print":
check_authority('037',"view");

$op = $poship->get($PHP_id);

//---------------------------------- 資料庫作業結束 ------------------------------

include_once($config['root_dir']."/lib/class.pdf_po_ship.php");

if($op['po_ship']['dist'] == 'HJ') $print_title = "湖嘉 廠 -- 出 貨 明 細 表 [".$op['po_ship']['num']."]";
if($op['po_ship']['dist'] == 'LY') $print_title = "立元 廠 -- 出 貨 明 細 表 [".$op['po_ship']['num']."]";


# 輸出頁面設定
$_SESSION['PDF']['w'] = $tmp_full_w = 275;	# 預設可用寬度
$_SESSION['PDF']['h'] = 200;								# 預設可用高度
$_SESSION['PDF']['lh'] = 5;

$print_title2 = "VER.".$op['po_ship']['ver']."  出貨日期 : ".$op['po_ship']['ship_date']." BY : ".$op['po_ship']['ship_way'];
$creator = $op['po_ship']['shipper'];
$mark = $op['po_ship']['num'];
$pdf=new PDF_po_ship('L','mm','A4');
$pdf->SetAutoPageBreak('on',10);
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();
$x = 0;
$pdf->titles($op['po_ship']);
$cno=0;$tmp_ctn='';$ttl_nw=$ttl_gw=0;
for($i=0; $i<sizeof($op['ship']); $i++)
{
	if($op['ship'][$i]['ctn'] != $tmp_ctn)
	{
		$tmp_ctn = $op['ship'][$i]['ctn'];
		$cno ++;
	}
	$op['ship'][$i]['cno'] = $cno;
	if(sizeof($op['ship'][$i]['ord_num']) == 1)
	{
		$x++;
		if ($x >= 26)
		{
			$pdf->AddPage();
			$pdf->titles($op['po_ship']);					
			$x=0;
		}
		$pdf->singl_det($op['ship'][$i]);		
	}else{
		$x+=sizeof($op['ship'][$i]['ord_num']);
		
		if ($x >= 26)
		{
			$pdf->AddPage();
			$pdf->titles($op['po_ship']);					
			$x=0;
		}
		$pdf->mix_det($op['ship'][$i]);		
	}
	$ttl_gw += $op['ship'][$i]['gw'];
	$ttl_nw += $op['ship'][$i]['nw'];
}
if ($x > 24)
{
	$pdf->AddPage();
	$pdf->titles($op['po_ship']);					
	$x=0;
}
$pdf->det_sum($ttl_nw,$ttl_gw,$op['ship'][($i-1)]['cno']);



$name=$op['po_ship']['num'].'_ship.pdf';

$pdf->Output($name,'D');


break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		job 24A sample pattern upload
#		case "upload_smpl_pattern":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "upload_file":
		check_authority('039',"add");
		
		$filename = $_FILES['PHP_pttn']['name'];		
		$ext =  strtolower(preg_replace("/.*\.([^.]+)$/","\\1", $filename));
		
		$f_check = 0;
		for ($i=0; $i<sizeof( $GLOBALS['VALID_TYPES']); $i++)
		{
			if ($GLOBALS['VALID_TYPES'][$i] == $ext)
			{
				$f_check = 1;
				break;
			}
		}		
		
		if ($filename && $PHP_desp)
		{
		$upload = new Upload;																
		if ($_FILES['PHP_pttn']['size'] < $upload->max_file_size && $_FILES['PHP_pttn']['size'] > 0)
		{
		if ($f_check == 1){   // 上傳檔的副檔名為有效時 -----

			// upload pattern file to server
			$today = $GLOBALS['TODAY'];
			$user_name =  $GLOBALS['SCACHE']['ADMIN']['name'];
			$parm = array(	"id"					=>  $PHP_id,
											"file_des"		=>	$PHP_desp,
											"file_user"		=>	$user_name,
											"file_date"		=>	$today
			);
			
			$A = $fils->get_name_id('po_ship_file');
			$pttn_name = $PHP_num."_".$A.".".$ext;  // 組合檔名
			$parm['file_name'] = $pttn_name;
			
			$str_long=strlen($pttn_name);

			//$upload->uploadFile(dirname($PHP_SELF).'/po_ship_file/', 'other', 16, $pttn_name );
			$upload->uploadFile(dirname(__FILE__).'\po_ship_file\\', 'other', 16, $pttn_name );
			if (!$upload){
				$op['msg'][] = $upload;
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			
			if (!$A = $fils->upload_po_ship_file($parm)){
				$op['msg'] = $fils->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}			
			$message = "UPLOAD file of : ".$PHP_num;
			$log->log_add(0,"53E",$message);
		} else {  // 上傳檔的副檔名  是  exe 時 -----

			$message = "upload file is incorrect format ! Please re-send.";
		}
		}else{  //上傳檔名重覆時
			$message = "Upload file is too big";
		}
	
		}else{
			$message="You don't pick any file or add file descript.";
		}	
			
		$op = $poship->get($PHP_id);
		$op['back_str'] = $PHP_back_str;		
		$op['msg'][] = $message;
		page_display($op, '039', $TPL_PO_SHIP_SHOW);			    	    
		break;


//-------------------------------------------------------------------------------------
//			 job 101X    訂單 記錄轉成 excel 檔
//-------------------------------------------------------------------------------------
case "ship_excel":

$op = $poship->get($PHP_id);


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
  HeaderingExcel($op['po_ship']['num'].'_ship.xls');
 
  // Creating a workbook
  $workbook = new Workbook("-");

  // Creating the first worksheet
  $worksheet1 =& $workbook->add_worksheet('SHIP');

    $now = $GLOBALS['THIS_TIME'];

// 寫入 title

  // Format for the headings
  $formatot =& $workbook->add_format();
  $formatot->set_size(10);
  $formatot->set_align('center');
  $formatot->set_color('white');
  $formatot->set_pattern();
  $formatot->set_fg_color('navy');

  $f2 =& $workbook->add_format();
  $f2->set_size(10);
  $f2->set_align('center');
  $f2->set_color('navy');
  $f2->set_pattern();
  $f2->set_fg_color('white');
  $ary = array(5,5,15,40,10,15,10,10,10,10);
  for ($i=0; $i<sizeof($ary); $i++)
  {
    $worksheet1->set_column(0,$i,$ary[$i]);
  }

  $worksheet1->write_string(0,1,"SHIP List");
  $worksheet1->write(0,3,$now);
    $worksheet1->write(0,5,"Ver. : ".$op['po_ship']['ver']);
  $worksheet1->write_string(1,1,"SHIP From :".$op['po_ship']['org']." To :".$op['po_ship']['dist']);
  $worksheet1->write_string(2,1,"Carrier# :".$op['po_ship']['carrier']);
  $worksheet1->write_string(3,1,"SHIP Date :".$op['po_ship']['ship_date']." BY :".$op['po_ship']['ship_way']);
  $worksheet1->write_string(4,1,"ETA :".$op['po_ship']['ship_eta']);



  $ary = array("CTN","C/NO","Material#","GOODS","ORDER#","@Price","Q'TY","N.W.(kg)","G.W.(kg)","PO#");
  for ($i=0; $i<sizeof($ary); $i++)
  {
     $worksheet1->write_string(5,$i,$ary[$i],$formatot);
  }       

  // Format for the numbers
  $fr =& $workbook->add_format(); //置右
  $fr->set_size(10);
  $fr->set_align('right');


  $fl =& $workbook->add_format(); //置左
  $fl->set_size(10);
  $fl->set_align('left');

  
  $fc =& $workbook->add_format(); //置右
  $fc->set_size(10);
  $fc->set_align('center');
  
  $fb =& $workbook->add_format();  //灰底白字置右
  $fb->set_pattern(1);
  $fb->set_align('right');
  $fb->set_fg_color('black');
  $fb->set_size(1);
  
  
    
    $tmp_ctn	=	'';
    $cno = 0;
    $j = 0;
    $ttl_gw = $ttl_nw = 0;
for ($i=0; $i < sizeof($op['ship']); $i++){
    $order_num = '';
    if($op['ship'][$i]['ctn'] != $tmp_ctn)
    {
        $tmp_ctn = $op['ship'][$i]['ctn'];
        $cno ++;
    }
     if($op['ship'][$i]['nw'] <= 0)$op['ship'][$i]['nw'] = '';
     if($op['ship'][$i]['gw'] <= 0)$op['ship'][$i]['gw'] = '';
    
    $ttl_gw += $op['ship'][$i]['gw'];
    $ttl_nw += $op['ship'][$i]['nw'];

    $op['ship'][$i]['mat_des']='';
    if($op['ship'][$i]['color'])$op['ship'][$i]['mat_des'] .= $op['ship'][$i]['color']." ; ";
    if($op['ship'][$i]['con1'])$op['ship'][$i]['mat_des'] .= $op['ship'][$i]['con1']." ; ";
    if($op['ship'][$i]['con2'])$op['ship'][$i]['mat_des'] .= $op['ship'][$i]['con2']." ; ";
    $op['ship'][$i]['mat_des'] = substr($op['ship'][$i]['mat_des'],0,-2);

    
  $worksheet1->write($j+6,0,$cno);
  $worksheet1->write($j+6,1,$op['ship'][$i]['ctn']);
  $worksheet1->write($j+6,2,$op['ship'][$i]['mat_code']);
  $worksheet1->write($j+6,3,$op['ship'][$i]['mat_name']." : [".$op['ship'][$i]['mat_des']." ]");  		
  $worksheet1->write($j+6,5,$op['ship'][$i]['currency']."$".$op['ship'][$i]['prics']."/".$op['ship'][$i]['prc_unit'],$fr);
  $worksheet1->write($j+6,6,$op['ship'][$i]['qty']." ".$op['ship'][$i]['po_unit'],$fr);
 
  $worksheet1->write($j+6,7,$op['ship'][$i]['nw'],$fr);
  $worksheet1->write($j+6,8,$op['ship'][$i]['gw'],$fr);
  $worksheet1->write($j+6,9,$op['ship'][$i]['po_num']);
  if($op['ship'][$i]['special'] == 0)
    {		
        for($k=0; $k<sizeof($op['ship'][$i]['ord_num']); $k++)
        {
            $worksheet1->write($j+6,4,$op['ship'][$i]['ord_num'][$k]);
            $j++;
        }
    }else{
        $worksheet1->write($j+6,4,$op['ship'][$i]['ord_num']);	
        $j++;
    }

    $worksheet1->set_Row($j+6,2);
    for($k=0; $k<sizeof($ary); $k++) 
    {	 		
        $worksheet1->write($j+6,$k,'',$fb);			
    }
    $j++;
}

$worksheet1->write($j+6,0,$cno);
$worksheet1->write($j+6,7,$ttl_nw,$fr);
$worksheet1->write($j+6,8,$ttl_gw,$fr);


$workbook->close();


break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "get_ship_det":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "get_ship_det":
check_authority('039',"view");

$op['ship'] = $poship->get_by_po($PHP_po_id, $PHP_special);
$op['mat_num'] = $PHP_mat;
$op['po_num'] = $PHP_po_num;
$op['unit'] = $PHP_unit;

page_display($op, '039', $TPL_PO_SHIPD_SHOW);			    	    
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "carrier_edit":			job 53
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "carrier_edit":
	check_authority('039',"view");
	$poship->update_field_id('`carrier`',$PHP_carrier,$PHP_id);;
	$msg = "Successfully Update Carrier# on :[".$PHP_num."]";			    	    
	$bttn = '</b> <span style="cursor:hand" onClick="car_edit.style.display=\'block\';car_view.style.display=\'none\'">&nbsp;&nbsp;<img src="images/Add.gif" alt="Edit ETD" border=0 valign="bottom"> </span>';
	echo $msg."|<b>".$PHP_carrier.$bttn;
break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "ship_del":			job 53
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "ship_del":
check_authority('039',"del");

$t = $poship->ship_del($PHP_ship_id);
if($t) $log->log_add(0,"039D","delete ship id:$PHP_ship_id");
redirect_page($PHP_SELF."?PHP_action=po_ship");

break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "file_del":			job 53
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "file_del":
	check_authority('039',"add");
	$t = $poship->file_del($PHP_file_id);
	if($t) $log->log_add(0,"039D","delete ship file on ship id:$PHP_ship_id");
	redirect_page($PHP_SELF."?PHP_action=ship_view&PHP_id=".$PHP_ship_id);
break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "ship_set":			job 53
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "ship_set":
	check_authority('039',"add");
	
	if($PHP_mode == 1){
		$parm = array("ship_set_user" => $GLOBALS['SCACHE']['ADMIN']['login_id'], "ship_set_date" => $GLOBALS['TODAY']);
	}else{
		$parm = array("ship_set_user" => '', "ship_set_date" => '');
	}
	$t = $poship->ship_set($PHP_id, $parm);
	// if($t) $log->log_add(0,"039A","delete ship file on ship id:$PHP_ship_id");
	echo $t;
break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "ship_finish":			job 53
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "ship_finish":
	check_authority('039',"view");
	
	if($PHP_mode == 1){
		$parm = array("ship_finish_user" => $GLOBALS['SCACHE']['ADMIN']['login_id'], "ship_finish_date" => $GLOBALS['TODAY']);
	}else{
		$parm = array("ship_finish_user" => '', "ship_finish_date" => '');
	}
	$t = $poship->ship_finish($PHP_id, $parm);
	// if($t) $log->log_add(0,"039A","delete ship file on ship id:$PHP_ship_id");
	echo $t;
break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "get_old_ship_det":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "get_old_ship_det":

check_authority('039',"view");

$op['ship'] = $poship->get_by_bom_id($_GET['PHP_bom_id'], $_GET['PHP_mat_cat']);

page_display($op, '039', "po_shipd_show2.html");			    	    
break;



}   // end case ---------

?>
