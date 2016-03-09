<?php
session_start();
session_register	('ord_parm');
session_register	('add_sch');
session_register	('sch_parm');
##################  2004/11/10  ########################
#			monitor.php  ￥Dμ{|!
#		for Carnival SCM [Sample]  management
#			Jack Yang     2004/11/10
#
#  index2.PHP --->shp_out
#  class.shipping.php
#  cost_analysis.php
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

$op = array();

session_register	('FTY_CM');
$para_cm = $para->get(0,'hj-cm');
$FTY_CM['HJ'] = $para_cm['set_value'];
$para_cm = $para->get(0,'ly-cm');
$FTY_CM['LY'] = $para_cm['set_value'];
$FTY_CM['SC'] = 0;

session_register	('CUST_DEL');
$para_cm = $para->get(0,'cust_del');
$CUST_DEL = $para_cm['set_value'] ;
// echo $PHP_action;
switch ($PHP_action) {
//=======================================================


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "packing":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "packing":
 	check_authority('032',"view");
 		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
 		
		$where_str=" ORDER BY cust_s_name"; //依cust_s_name排序
		$cust_def = $cust->get_fields('cust_init_name',$where_str);//取出客戶簡稱
		$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//取出客戶代號
		for ($i=0; $i< sizeof($cust_def); $i++)
			$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//以 [客戶代號-客戶簡稱] 呈現
		$op['cust_select'] =  $arry2->select($cust_value,'','PHP_cust','select','',$cust_def_vue); 
		
		if($user_dept == 'HJ' || $user_dept == 'LY')
		{
			$op['fty'] = "<B>".$user_dept."</B> <input type=hidden name='PHP_fty' value='$user_dept'>";
		}else{
			$op['fty'] = $arry2->select($FACTORY,'','PHP_fty','select','');
		}
		
	 if(isset($PHP_msg)) $op['msg'][] = $PHP_msg;
		
	page_display($op, '032', $TPL_SHIPDOC);
	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "shipdoc":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "shipdoc":
 	check_authority('034',"view");
 		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
 		
		$where_str=" ORDER BY cust_s_name"; //依cust_s_name排序
		$cust_def = $cust->get_fields('cust_init_name',$where_str);//取出客戶簡稱
		$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//取出客戶代號
		for ($i=0; $i< sizeof($cust_def); $i++)
			$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//以 [客戶代號-客戶簡稱] 呈現
		$op['cust_select'] =  $arry2->select($cust_value,'','PHP_cust','select','',$cust_def_vue); 
		
		if($user_dept == 'HJ' || $user_dept == 'LY')
		{
			$op['fty'] = "<B>".$user_dept."</B> <input type=hidden name='PHP_fty' value='$user_dept'>";
		}else{
			$op['fty'] = $arry2->select($FACTORY,'','PHP_fty','select','');
		}

		$op['year'] = $arry2->select($YEAR_WORK,date('Y'),'PHP_year','select','');  	
		$op['mon'] = $arry2->select($MONTH_WORK,'','PHP_month','select','');  	

		
	 if(isset($PHP_msg)) $op['msg'][] = $PHP_msg;
		
	page_display($op, '034', $TPL_SHIPDOC_INV);
	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "order_shiped_search":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "order_shiped_search":
 	check_authority('032',"add");
		if(isset($PHP_fty))
		{
			
			$add_sch  = array();
			$ord_parm = array();
			$add_sch = array(
												'PHP_fty'					=>	$PHP_fty,
												'PHP_cust'				=>	$PHP_cust,
												'PHP_num'					=>	$PHP_num,
												'PHP_action'			=>	$PHP_action,
												'PHP_sr_startno'	=>	$PHP_sr_startno
												);
			}else{
				if(isset($PHP_sr_startno))$add_sch ['PHP_sr_startno'] = $PHP_sr_startno;
			}		
		

		$op = $ship_doc->add_search(1,$ord_parm);		
		$op['ord_select'] = $ord_parm;
		$op['span'] = sizeof($op['ord_select']);
//		$op['back_str'] = "&PHP_fty=".$PHP_fty."&PHP_cust=".$PHP_cust."&PHP_num=".$PHP_num;
	page_display($op, '032', $TPL_SHIPDOC_ADD_LIST);
	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "order_add":	  //增加多筆訂單
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "order_add":
 	check_authority('032',"add");

		$parm = array('id'		=>	$PHP_id,
									'num'		=>	$PHP_ord,
									'mks'		=>	$PHP_mks,
									'p_id'	=>	$PHP_p_id,
									'sort'	=>	$PHP_ord
								 );
		$add_sch['PHP_cust'] = $PHP_cust;
		$ord_parm[] = $parm;
		
		$op = $ship_doc->add_search(1,$ord_parm);
		$tmp = $op['ord'];
		
		$op['ord_select'] = $ord_parm;
		
		$op['span'] = sizeof($op['ord_select']);
//		$op['back_str'] = "&PHP_fty=".$PHP_fty."&PHP_cust=".$PHP_cust."&PHP_num=".$PHP_num;
	page_display($op, '032', $TPL_SHIPDOC_ADD_LIST);
	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "order_del":	  //刪除選擇訂單
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "order_del":
 	check_authority('032',"add");

		$parm = array();
		for($i=0; $i<sizeof($ord_parm); $i++)
		{
			if($ord_parm[$i]['id'] != $PHP_id) $parm[] = $ord_parm[$i];
		}
	
							 
		$ord_parm = $parm;
		
		$op = $ship_doc->add_search(1,$ord_parm);
		$op['ord_select'] = $ord_parm;
		
		$op['span'] = sizeof($op['ord_select']);

	page_display($op, '032', $TPL_SHIPDOC_ADD_LIST);
	break;


	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "packet_add":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "packet_add":
 	check_authority('032',"add");
		$csgn = array();

		$ord_parm = bubble_sort_s($ord_parm);
		$op = $ship_doc->get_order_by_add($ord_parm);
/*		
		if(sizeof($op['color']))
		{
			$op['color_select'] = $arry2->select($op['color'],'','PHP_color','select','');
		}
*/	
		for($i=0; $i<sizeof($op['assort']); $i++) 
		{
			$op['assort'][$i]= $ship_doc->count_size_qty($op['ord'][$i]['order_num'], $op['assort'][$i]);
		}
		

//		if(isset($PHP_fty))$op['back_str'] = "&PHP_fty=".$PHP_fty."&PHP_cust=".$PHP_cust."&PHP_num=".$PHP_num."&PHP_sr_startno=".$PHP_sr_startno;
		
//echo $PHP_id;

		if(isset($PHP_from))
		{
			$shp_rec = $ship_doc->get($PHP_id);
			$op['shp'] = $shp_rec['shp'];
			$op['shp_det'] = $shp_rec['shp_det'];
		}else{
			$op['shp']['id']='x';
		}
		
		if(isset($PHP_msg))	$op['msg'][] = $PHP_msg;

		$where_str = "WHERE cust='".$op['ord'][0]['cust']."'";
		$csgn = $cust->get_csge_fields('s_name',$where_str);
		if(!$csgn)$csgn=array('');
		$op['csgn_select'] = $arry2->select($csgn,'','PHP_consignee','select','');
		
		if(!isset($PHP_from))$PHP_from = 'add';
		if($PHP_from == 'edit')
		{
			page_display($op, '032', $TPL_SHIPDOC_PACK_EDIT);
		}else{
			page_display($op, '032', $TPL_SHIPDOC_PACK_ADD);
		}

//	page_display($op, '032', $TPL_SHIPDOC_PACK_ADD);
	break;	


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "packet_bar_add":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "packet_bar_add":
 	check_authority('032',"add");
		$csgn = array();
		
		$ord_parm = bubble_sort_s($ord_parm);
		$op = $ship_doc->get_order_by_add($ord_parm);

		for($i=0; $i<sizeof($op['assort']); $i++) 
		{
			$op['assort'][$i]= $ship_doc->count_size_qty($op['ord'][$i]['order_num'], $op['assort'][$i]);
			
		}

		if(isset($PHP_from))
		{
			$shp_rec = $ship_doc->get($PHP_id);
		
			$op['shp'] = $shp_rec['shp'];
			$op['shp_det'] = $shp_rec['shp_det'];

		}else{
			$op['shp']['id']='x';
		}
		
		if(isset($PHP_msg))	$op['msg'][] = $PHP_msg;



		$where_str = "WHERE cust='".$op['ord'][0]['cust']."'";
		$csgn = $cust->get_csge_fields('s_name',$where_str);
		if(!$csgn)$csgn=array('');
		$op['csgn_select'] = $arry2->select($csgn,'','PHP_consignee','select','');
		
		if(!isset($PHP_from))$PHP_from = 'add';
		if($PHP_from == 'edit')
		{
			page_display($op, '032', $TPL_SHIPDOC_PACK_BAR_EDIT);
		}else{
			page_display($op, '032', $TPL_SHIPDOC_PACK_BAR_ADD);
		}

	break;	



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "order_shiped_search":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "packet_inbox_add":
 	check_authority('032',"add");
		$csgn = array();

		$ord_parm = bubble_sort_s($ord_parm);
		$op = $ship_doc->get_order_by_add($ord_parm,1);
		
/*		
		$op = $ship_doc->get_order($PHP_id);
		if(sizeof($op['color']))
		{
			$op['color_select'] = $arry2->select($op['color'],'','PHP_color','select','');
		}
*/	
		for($i=0; $i<sizeof($op['assort']); $i++) $op['assort'][$i]= $ship_doc->count_size_qty($op['ord'][$i]['order_num'], $op['assort'][$i]);
		
//		$op['back_str'] = "&PHP_fty=".$PHP_fty."&PHP_cust=".$PHP_cust."&PHP_num=".$PHP_num."&PHP_sr_startno=".$PHP_sr_startno;
		if(isset($PHP_from))
		{
			$shp_rec = $ship_doc->get($PHP_id);
		
			$op['shp'] = $shp_rec['shp'];
			$op['shp_det'] = $shp_rec['shp_det'];

		}else{
			$op['shp']['id']='x';
		}
		
		if(isset($PHP_msg))	$op['msg'][] = $PHP_msg;



		$where_str = "WHERE cust='".$op['ord'][0]['cust']."'";
		$csgn = $cust->get_csge_fields('s_name',$where_str);
		if(!$csgn)$csgn=array('');
		$op['csgn_select'] = $arry2->select($csgn,'','PHP_consignee','select','');
		
		if(!isset($PHP_from))$PHP_from = 'add';
		if($PHP_from == 'edit')
		{
			page_display($op, '032', $TPL_SHIPDOC_PACK_INBOX_EDIT);
		}else{
			page_display($op, '032', $TPL_SHIPDOC_PACK_INBOX_ADD);
		}

	
	break;		
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_packet_add":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "do_packet_add":
 	check_authority('032',"add");
	
		if(!$PHP_id || $PHP_id == 0)
		{
			$PHP_id = $ship_doc->add_main($PHP_ord_cust, $PHP_fty);
		}		
		if($PHP_inv)	$ship_doc->update_field_main('inv_num',$PHP_inv,$PHP_id);
		if($PHP_unit)	$ship_doc->update_field_main('unit',$PHP_unit,$PHP_id);
		
		if(!$PHP_ctn2)$PHP_ctn2 = $PHP_ctn1;
		$PHP_cnum = $PHP_ctn2 - $PHP_ctn1 + 1;
		$mk = 0;
		foreach($PHP_qty as $ord_key => $ord_val)
		{
			foreach($PHP_qty[$ord_key] as $po_key => $po_val)
			{
				foreach($PHP_qty[$ord_key][$po_key] as $clr_key => $clr_val)
				{
					if($PHP_qty[$ord_key][$po_key][$clr_key]['s_ttl'] > 0)
					{
						$size_qty ='';
						$parm = array();
						foreach($PHP_qty[$ord_key][$po_key][$clr_key] as $sz_key => $sz_val)
						{
							$size_qty .= $sz_val.',';
						}
						$tmp_size = explode(',',$size_qty);
						$size_qty = '';		
						$ttl_size_qty = 0;			
						for($i=0; $i < (sizeof($tmp_size)-2); $i++)$ttl_size_qty+=$tmp_size[$i];	
						for($i=0; $i < (sizeof($tmp_size)-2); $i++)$size_qty .= $tmp_size[$i].',';
						$size_qty = substr($size_qty,0,-1);
						
						$ship_ord_id = $ship_doc->add_ord($PHP_id, $po_key, $PHP_ord_num[$ord_key]);
						if($mk == 0)
						{
							$parm = array(
									'ship_id'			=>	$PHP_id,				'ord_num'			=> 	$PHP_ord_num[$ord_key],
									'size_qty'		=>	$size_qty,			'color'				=>	$PHP_color_id[$ord_key][$po_key][$clr_key],
									'ctn_l'				=>	$PHP_l,					'nnw'					=>	$PHP_nnw[$ord_key][$po_key][$clr_key],				
									'ctn_w'				=>	$PHP_w,					'nw'					=>	$PHP_nw[$ord_key][$po_key][$clr_key],
									'ctn_h'				=>	$PHP_h,					'gw'					=>	$PHP_gw[$ord_key][$po_key][$clr_key],				
									'qty'					=>	$PHP_ctn_qty,		'cnum'				=>	$PHP_cnum,											
									'size_box'		=>	'',							'ppk'					=>	$PHP_ppk[$ord_key][$po_key][$clr_key],
									'ship_ord_id'	=>	$ship_ord_id,		'ctn_id'			=>	$PHP_ctn_id[$ord_key][$po_key][$clr_key],	
									'f_mk'				=>	1,							'ctn' 				=>	$PHP_ctn1.'-'.$PHP_ctn2,
									'ucc'					=>	$PHP_ucc[$ord_key][$po_key][$clr_key],
									's_qty'				=>	$PHP_qty[$ord_key][$po_key][$clr_key]['s_ttl'],
									's_cnum'			=>	$PHP_cnum,				'ctn_mk'			=>	'',
									'fit'					=>	$PHP_fit[$ord_key][$po_key][$clr_key],
									);
						}else{
							$parm = array(
									'ship_id'			=>	$PHP_id,			'ord_num'			=> 	$PHP_ord_num[$ord_key],
									'size_qty'		=>	$size_qty,		'color'				=>	$PHP_color_id[$ord_key][$po_key][$clr_key],
									'ctn_l'				=>	$PHP_l,				'nnw'					=>	$PHP_nnw[$ord_key][$po_key][$clr_key],				
									'ctn_w'				=>	$PHP_w,				'nw'					=>	$PHP_nw[$ord_key][$po_key][$clr_key],
									'ctn_h'				=>	$PHP_h,				'gw'					=>	$PHP_gw[$ord_key][$po_key][$clr_key],													
									'qty'					=>	'',						'cnum'				=>	'',					
									'size_box'		=>	'',						'ppk'					=>	$PHP_ppk[$ord_key][$po_key][$clr_key],
									'ship_ord_id'	=>	$ship_ord_id,	'ctn_id'			=>	$PHP_ctn_id[$ord_key][$po_key][$clr_key],
									'f_mk'				=>	2,						'ctn' 				=>	$PHP_ctn1.'-'.$PHP_ctn2,
									'ucc'					=>	$PHP_ucc[$ord_key][$po_key][$clr_key],
									's_qty'				=>	$PHP_qty[$ord_key][$po_key][$clr_key]['s_ttl'],
									's_cnum'			=>	$PHP_cnum,			'ctn_mk'			=>	'',
									'fit'					=>	$PHP_fit[$ord_key][$po_key][$clr_key],
									);						
						
						}
						$mk++;
						$f1 = $ship_doc->add_det($parm);
						$mesg = "Successfully add CTN : $PHP_ctn1 ~ $PHP_ctn2 ON $PHP_ord_num[$ord_key]";
					}
				}
			}
		}
		
		
		
/*
		foreach ($PHP_qty as $key => $value)
		{
			$size_qty .= $value.',';			
		}		
		$size_qty = substr($size_qty,0,-1);
		

		$ship_ord_id = $ship_doc->add_ord($PHP_id, $PHP_cust_po, $PHP_ord_num);
		
		
				
		if($PHP_color)
		{
			if(!$PHP_ctn2)$PHP_ctn2 = $PHP_ctn1;
			$PHP_cnum = $PHP_ctn2 - $PHP_ctn1 + 1;
			$parm = array(
									'ship_id'		=>	$PHP_id,
									'ord_num'		=> 	$PHP_ord_num,
									'size_qty'	=>	$size_qty,
									'color'			=>	$PHP_color,
									'nnw'				=>	$PHP_nnw,
									'nw'				=>	$PHP_nw,
									'gw'				=>	$PHP_gw,
									'qty'				=>	$PHP_ctn_qty,
									'ctn_l'			=>	$PHP_l,
									'ctn_w'			=>	$PHP_w,
									'ctn_h'			=>	$PHP_h,
									'cnum'			=>	$PHP_cnum,
									'size_box'	=>	'',
									'ppk'				=>	$PHP_ppk,
									'ctn_id'		=>	$PHP_ctn_id,
									'ship_ord_id'	=>	$ship_ord_id,
									);

			$parm['ctn'] = $PHP_ctn1.'-'.$PHP_ctn2;
			$f1 = $ship_doc->add_det($parm);

			$mesg = "Successfully add CTN : $PHP_ctn1 ~ $PHP_ctn2 ON $PHP_ord_num";
		}else{
			$mesg = "Please select color first";
		}
*/		
		
		$redir_str = 'shipdoc.php?PHP_action=packet_add&PHP_id='.$PHP_id."&PHP_msg=".$mesg."&PHP_from=".$PHP_from;
		redirect_page($redir_str);
					
/*
		$op = $ship_doc->get_order_by_add($ord_parm);

		for($i=0; $i<sizeof($op['assort']); $i++) $op['assort'][$i]= $ship_doc->count_size_qty($op['ord'][$i]['order_num'], $op['assort'][$i]);
		
		
		$shp_rec = $ship_doc->get($PHP_id);
		$op['shp'] = $shp_rec['shp'];
		$op['shp_det'] = $shp_rec['shp_det'];
		$op['msg'][] = $mesg;

		$where_str = "WHERE cust='".$op['ord'][0]['cust']."'";
		$csgn = $cust->get_csge_fields('s_name',$where_str);
		if(!$csgn)$csgn=array('');
		$op['csgn_select'] = $arry2->select($csgn,$op['shp']['consignee'],'PHP_consignee','select','');

		
		if(isset($PHP_back_str))
		{
			$op['back_str'] = $PHP_back_str;
			if(isset($PHP_cfm))
			{
				page_display($op, '033', $TPL_SHIPDOC_PACK_EDIT_CFM);
			}else{			
				page_display($op, '032', $TPL_SHIPDOC_PACK_EDIT);
			}
		}else{
			page_display($op, '032', $TPL_SHIPDOC_PACK_ADD);
		}
		break;		
*/	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_packet_bar_add":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "do_packet_bar_add":
 	check_authority('032',"add");
	
		if(!$PHP_id || $PHP_id == 0)
		{
			$PHP_id = $ship_doc->add_main($PHP_ord_cust, $PHP_fty);
		}		
		if($PHP_inv)	$ship_doc->update_field_main('inv_num',$PHP_inv,$PHP_id);
		if($PHP_unit)	$ship_doc->update_field_main('unit',$PHP_unit,$PHP_id);
		$ship_doc->update_field_main('bar_mk',1,$PHP_id);
		if(!$PHP_ctn2)$PHP_ctn2 = $PHP_ctn1;
		$PHP_cnum = $PHP_ctn2 - $PHP_ctn1 + 1;
		$mk = 0;
		foreach($PHP_qty as $ord_key => $ord_val)
		{
			foreach($PHP_qty[$ord_key] as $po_key => $po_val)
			{
				foreach($PHP_qty[$ord_key][$po_key] as $clr_key => $clr_val)
				{
					if($PHP_qty[$ord_key][$po_key][$clr_key]['s_ttl'] > 0)
					{
						$size_qty ='';
						$parm = array();
						foreach($PHP_qty[$ord_key][$po_key][$clr_key] as $sz_key => $sz_val)
						{
							$size_qty .= $sz_val.',';
						}
						$tmp_size = explode(',',$size_qty);
						$size_qty = '';		
						$ttl_size_qty = 0;			
						for($i=0; $i < (sizeof($tmp_size)-2); $i++)$ttl_size_qty+=$tmp_size[$i];	
						for($i=0; $i < (sizeof($tmp_size)-2); $i++)$size_qty .= $tmp_size[$i].',';
						$size_qty = substr($size_qty,0,-1);
						
						$ship_ord_id = $ship_doc->add_ord($PHP_id, $po_key, $PHP_ord_num[$ord_key]);
						if($mk == 0)
						{
							$parm = array(
									'ship_id'			=>	$PHP_id,				'ord_num'			=> 	$PHP_ord_num[$ord_key],
									'size_qty'		=>	$size_qty,			'color'				=>	$PHP_color_id[$ord_key][$po_key][$clr_key],
									'ctn_l'				=>	$PHP_l,					'nnw'					=>	$PHP_nnw[$ord_key][$po_key][$clr_key],				
									'ctn_w'				=>	$PHP_w,					'nw'					=>	$PHP_nw[$ord_key][$po_key][$clr_key],
									'ctn_h'				=>	$PHP_h,					'gw'					=>	$PHP_gw[$ord_key][$po_key][$clr_key],				
									'qty'					=>	$PHP_ctn_qty,		'cnum'				=>	$PHP_cnum,											
									'size_box'		=>	'',							'ppk'					=>	$PHP_ppk[$ord_key][$po_key][$clr_key],
									'ship_ord_id'	=>	$ship_ord_id,		'ctn_id'			=>	$PHP_ctn_id[$ord_key][$po_key][$clr_key],	
									'f_mk'				=>	1,							'ctn' 				=>	$PHP_ctn1.'-'.$PHP_ctn2,
									'ucc'					=>	$PHP_ucc[$ord_key][$po_key][$clr_key],
									's_qty'				=>	$PHP_qty[$ord_key][$po_key][$clr_key]['s_ttl'],
									's_cnum'			=>	$PHP_cnum,			'ctn_mk'			=>	$PHP_ctn_mk,
									);
						}else{
							$parm = array(
									'ship_id'			=>	$PHP_id,			'ord_num'			=> 	$PHP_ord_num[$ord_key],
									'size_qty'		=>	$size_qty,		'color'				=>	$PHP_color_id[$ord_key][$po_key][$clr_key],
									'ctn_l'				=>	$PHP_l,				'nnw'					=>	$PHP_nnw[$ord_key][$po_key][$clr_key],				
									'ctn_w'				=>	$PHP_w,				'nw'					=>	$PHP_nw[$ord_key][$po_key][$clr_key],
									'ctn_h'				=>	$PHP_h,				'gw'					=>	$PHP_gw[$ord_key][$po_key][$clr_key],													
									'qty'					=>	'',						'cnum'				=>	'',					
									'size_box'		=>	'',						'ppk'					=>	$PHP_ppk[$ord_key][$po_key][$clr_key],
									'ship_ord_id'	=>	$ship_ord_id,	'ctn_id'			=>	$PHP_ctn_id[$ord_key][$po_key][$clr_key],
									'f_mk'				=>	2,						'ctn' 				=>	$PHP_ctn1.'-'.$PHP_ctn2,
									'ucc'					=>	$PHP_ucc[$ord_key][$po_key][$clr_key],
									's_qty'				=>	$PHP_qty[$ord_key][$po_key][$clr_key]['s_ttl'],
									's_cnum'		=>	$PHP_cnum,			'ctn_mk'			=>	$PHP_ctn_mk,
									'fit'					=>	$PHP_fit[$ord_key][$po_key][$clr_key],
									);						
						
						}
						$mk++;
						$f1 = $ship_doc->add_det($parm);
						$mesg = "Successfully add CTN : $PHP_ctn1 ~ $PHP_ctn2 ON $PHP_ord_num[$ord_key]";
					}
				}
			}
		}
		
		$redir_str = 'shipdoc.php?PHP_action=packet_bar_add&PHP_id='.$PHP_id."&PHP_msg=".$mesg."&PHP_from=".$PHP_from;
		redirect_page($redir_str);
					
	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "order_shiped_search":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "do_packet_inbox_add":
 	check_authority('032',"add");
		$size_qty = $size_box  = '';


		if(!$PHP_id || $PHP_id == 0)
		{
			$PHP_id = $ship_doc->add_main($PHP_ord_cust, $PHP_fty);
		}		
		if($PHP_inv)	$ship_doc->update_field_main('inv_num',$PHP_inv,$PHP_id);
		if($PHP_unit)	$ship_doc->update_field_main('unit',$PHP_unit,$PHP_id);

		$ship_doc->update_field_main('inner_box',1,$PHP_id);


		if(!$PHP_ctn2)$PHP_ctn2 = $PHP_ctn1;
		$PHP_cnum = $PHP_ctn2 - $PHP_ctn1 + 1;
		$mk = 0;
		foreach($PHP_ibox as $ord_key => $ord_val)
		{
			foreach($PHP_ibox[$ord_key] as $po_key => $po_val)
			{
				foreach($PHP_ibox[$ord_key][$po_key] as $clr_key => $clr_val)
				{
					if($PHP_qty[$ord_key][$po_key][$clr_key]['s_ttl'] > 0)
					{
						$size_qty = $size_box ='';
						$parm = array();
						foreach($PHP_ibox[$ord_key][$po_key][$clr_key] as $sz_key => $sz_val)
						{
							$size_box .= $sz_val.',';
							$size_qty .= $PHP_qty[$ord_key][$po_key][$clr_key][$sz_key].',';
						}
						$size_qty = substr($size_qty,0,-1);
						$size_box = substr($size_box,0,-1);
						
						$ship_ord_id = $ship_doc->add_ord($PHP_id, $po_key, $PHP_ord_num[$ord_key]);
						if($mk == 0)
						{
							$parm = array(
									'ship_id'			=>	$PHP_id,			'ord_num'			=> 	$PHP_ord_num[$ord_key],
									'size_qty'		=>	$size_qty,		'color'				=>	$PHP_color_id[$ord_key][$po_key][$clr_key],
									'nnw'					=>	$PHP_nnw[$ord_key][$po_key][$clr_key],			
									'nw'					=>	$PHP_nw[$ord_key][$po_key][$clr_key],
									'gw'					=>	$PHP_gw[$ord_key][$po_key][$clr_key],			
									'qty'					=>	$PHP_ctn_qty,
									'ctn_l'				=>	$PHP_l,				'ctn_w'				=>	$PHP_w,
									'ctn_h'				=>	$PHP_h,				'cnum'				=>	$PHP_cnum,
									'size_box'		=>	$size_box,		'ppk'					=>	$PHP_ppk[$ord_key][$po_key][$clr_key],
									'ship_ord_id'	=>	$ship_ord_id,	'ctn_id'			=>	$PHP_ctn_id[$ord_key][$po_key][$clr_key],	
									'f_mk'				=>	1,						'ctn' 				=>	$PHP_ctn1.'-'.$PHP_ctn2,
									'ucc'					=>	$PHP_ucc[$ord_key][$po_key][$clr_key],	
									's_qty'				=>	$PHP_qty[$ord_key][$po_key][$clr_key]['s_ttl'],
									's_cnum'		=>	$PHP_cnum,			'ctn_mk'			=>	'',
									);
						}else{
							$parm = array(
									'ship_id'			=>	$PHP_id,			'ord_num'			=> 	$PHP_ord_num[$ord_key],
									'size_qty'		=>	$size_qty,		'color'				=>	$PHP_color_id[$ord_key][$po_key][$clr_key],
									'nnw'					=>	$PHP_nnw[$ord_key][$po_key][$clr_key],			
									'nw'					=>	$PHP_nw[$ord_key][$po_key][$clr_key],
									'gw'					=>	$PHP_gw[$ord_key][$po_key][$clr_key],			
									'qty'					=>	'',
									'ctn_l'				=>	$PHP_l,				'ctn_w'				=>	$PHP_w,
									'ctn_h'				=>	$PHP_h,				'cnum'				=>	'',
									'size_box'		=>	$size_box,		'ppk'					=>	$PHP_ppk[$ord_key][$po_key][$clr_key],
									'ship_ord_id'	=>	$ship_ord_id,	'ctn_id'			=>	$PHP_ctn_id[$ord_key][$po_key][$clr_key],	
									'f_mk'				=>	2,						'ctn' 				=>	$PHP_ctn1.'-'.$PHP_ctn2,
									'ucc'					=>	$PHP_ucc[$ord_key][$po_key][$clr_key],	
									's_qty'				=>	$PHP_qty[$ord_key][$po_key][$clr_key]['s_ttl'],
									's_cnum'		=>	$PHP_cnum,			'ctn_mk'			=>	'',
									'fit'					=>	$PHP_fit[$ord_key][$po_key][$clr_key],
									);						
						
						}
						$mk++;
						$f1 = $ship_doc->add_det($parm);
						$mesg = "Successfully add CTN : $PHP_ctn1 ~ $PHP_ctn2 ON $PHP_ord_num[$ord_key]";
					}
				}
			}
		}


		$redir_str = 'shipdoc.php?PHP_action=packet_inbox_add&PHP_id='.$PHP_id."&PHP_msg=".$mesg."&PHP_from=".$PHP_from;
		redirect_page($redir_str);






		
/*		
		
		$op = $ship_doc->get_order($PHP_ord_id);
		if(sizeof($op['color']))
		{
			$op['color_select'] = $arry2->select($op['color'],'','PHP_color','select','');
		}
		$op['assort'] = $ship_doc->count_size_qty($op['ord']['order_num'], $op['assort']);
		
		
		$shp_rec = $ship_doc->get($PHP_id);
		$op['shp'] = $shp_rec['shp'];
		$op['shp_det'] = $shp_rec['shp_det'];
		$op['msg'][] = $mesg;

		$where_str = "WHERE cust='".$op['ord']['cust']."'";
		$csgn = $cust->get_csge_fields('s_name',$where_str);
		if(!$csgn)$csgn=array('');
		$op['csgn_select'] = $arry2->select($csgn,$op['shp']['consignee'],'PHP_consignee','select','');

		
		if(isset($PHP_back_str))
		{
			$op['back_str'] = $PHP_back_str;
			if(isset($PHP_cfm))
			{
				page_display($op, '033', $TPL_SHIPDOC_PACK_EDIT_INBOX_CFM);
			}else{			
				page_display($op, '032', $TPL_SHIPDOC_PACK_INBOX_EDIT);
			}
		}else{
			page_display($op, '032', $TPL_SHIPDOC_PACK_INBOX_ADD);
		}
		break;			
*/	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_packet_edit":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "do_packet_edit":
 	check_authority('032',"add");
		
//		$PHP_ctn_qty = 0;
		$mk = 0;
		for($i=0; $i<sizeof($PHP_ord_id); $i++)
		{
			foreach ($PHP_qty[$PHP_ord_id[$i]] as $ln_key => $ln_value)
			{
				$size_qty = '';
				if($PHP_qty[$PHP_ord_id[$i]][$ln_key]['s_ttl'] > 0)
				{					
					for($k=0; $k < $PHP_y_ln[$i]; $k++)
					{
						 $size_qty .= $PHP_qty[$PHP_ord_id[$i]][$ln_key][$k].",";
//						if($sz_key != 's_ttl') $PHP_ctn_qty += $sz_value;
					}
					$size_qty = substr($size_qty,0,-1);
					$PHP_cnum = $PHP_e_ctn - $PHP_s_ctn + 1;
					if($mk == 0)
					{
						$parm = array(
									'size_qty'	=>	$size_qty,			'id'		=>	$PHP_shp_id[$PHP_ord_id[$i]][$ln_key],		
									'size_box'	=>	'',							'nnw'		=>	$PHP_nnw[$PHP_ord_id[$i]][$ln_key],
									'nw'				=>	$PHP_nw[$PHP_ord_id[$i]][$ln_key],				
									'gw'				=>	$PHP_gw[$PHP_ord_id[$i]][$ln_key],
									'qty'				=>	$PHP_ctn_qty,		'ctn_l'	=>	$PHP_l,
									'ctn_w'			=>	$PHP_w,					'ctn_h'	=>	$PHP_h,
									'cnum'			=> 	$PHP_cnum,			'ppk'		=>	$PHP_ppk[$PHP_ord_id[$i]][$ln_key],
									'f_mk'			=>	1,							'ctn'		=> 	$PHP_s_ctn.'-'.$PHP_e_ctn,							
									'ctn_id'		=>	$PHP_ctn_id[$PHP_ord_id[$i]][$ln_key],
									'ucc'				=>	$PHP_ucc[$PHP_ord_id[$i]][$ln_key],
									's_qty'			=>	$PHP_qty[$PHP_ord_id[$i]][$ln_key]['s_ttl'],
									's_cnum'		=>	$PHP_cnum,			'ctn_mk'	=>	'',
									'fit'				=>	$PHP_fit[$PHP_ord_id[$i]][$ln_key],
										);
					}else{
						$parm = array(
									'size_qty'	=>	$size_qty,			'id'		=>	$PHP_shp_id[$PHP_ord_id[$i]][$ln_key],		
									'size_box'	=>	'',							'nnw'		=>	$PHP_nnw[$PHP_ord_id[$i]][$ln_key],
									'nw'				=>	$PHP_nw[$PHP_ord_id[$i]][$ln_key],				
									'gw'				=>	$PHP_gw[$PHP_ord_id[$i]][$ln_key],
									'qty'				=>	'',							'ctn_l'	=>	$PHP_l,
									'ctn_w'			=>	$PHP_w,					'ctn_h'	=>	$PHP_h,
									'cnum'			=> 	'',							'ppk'		=>	$PHP_ppk[$PHP_ord_id[$i]][$ln_key],
									'f_mk'			=>	2	,							'ctn'		=> 	$PHP_s_ctn.'-'.$PHP_e_ctn,							
									'ctn_id'		=>	$PHP_ctn_id[$PHP_ord_id[$i]][$ln_key],
									'ucc'				=>	$PHP_ucc[$PHP_ord_id[$i]][$ln_key],
									's_qty'			=>	$PHP_qty[$PHP_ord_id[$i]][$ln_key]['s_ttl'],
									's_cnum'		=>	$PHP_cnum,			'ctn_mk'	=>	'',
									'fit'				=>	$PHP_fit[$PHP_ord_id[$i]][$ln_key],
										);
					}
					$mk = 1;
					$f1 = $ship_doc->edit_pack_det($parm);
				}else{
					$ship_doc->del_pack_det($PHP_shp_id[$PHP_ord_id[$i]][$ln_key]);
				}
			}			
		}
		$mesg = "success update carton:[ ".$PHP_s_ctn.'-'.$PHP_e_ctn." ] ON P/L :[ ".$PHP_inv_num." ]";

		$redir_str = 'shipdoc.php?PHP_action=packet_add&PHP_id='.$PHP_id."&PHP_msg=".$mesg."&PHP_from=".$PHP_from;
		redirect_page($redir_str);
		
/*		
		foreach ($PHP_qty as $key => $value)
		{
			$size_qty .= $value.',';
			$PHP_ctn_qty += $value;
		}		
		$size_qty = substr($size_qty,0,-1);
		$PHP_cnum = $PHP_e_ctn - $PHP_s_ctn + 1;
	
		$parm = array(
									'id'				=>	$PHP_shp_id,
									'size_qty'	=>	$size_qty,
									'size_box'	=>	'',
									'nnw'				=>	$PHP_nnw,
									'nw'				=>	$PHP_nw,
									'gw'				=>	$PHP_gw,
									'qty'				=>	$PHP_ctn_qty,
									'ctn_l'			=>	$PHP_l,
									'ctn_w'			=>	$PHP_w,
									'ctn_h'			=>	$PHP_h,
									'cnum'			=> 	$PHP_cnum,
									'ppk'				=>	$PHP_ppk,
									'ctn_id'		=>	$PHP_ctn_id
									);
		$parm['ctn'] = $PHP_s_ctn.'-'.$PHP_e_ctn;									
		$f1 = $ship_doc->edit_pack_det($parm);
		
		
		
		$op = $ship_doc->get_order('',$PHP_ord_num);
		if(sizeof($op['color']))
		{
			$op['color_select'] = $arry2->select($op['color'],'','PHP_color','select','');
		}
		$op['assort'] = $ship_doc->count_size_qty($op['ord']['order_num'], $op['assort']);
		
		
		$shp_rec = $ship_doc->get($PHP_id);
		$op['shp'] = $shp_rec['shp'];
		$op['shp_det'] = $shp_rec['shp_det'];
	
		$where_str = "WHERE cust='".$op['ord']['cust']."'";
		$csgn = $cust->get_csge_fields('s_name',$where_str);
		if(!$csgn)$csgn=array('');
		$op['csgn_select'] = $arry2->select($csgn,$op['shp']['consignee'],'PHP_consignee','select','');
	
		
		if(isset($PHP_back_str))
		{
			$op['back_str'] = $PHP_back_str;
			if(isset($PHP_cfm))
			{
				page_display($op, '033', $TPL_SHIPDOC_PACK_EDIT_CFM);
			}else{
				page_display($op, '032', $TPL_SHIPDOC_PACK_EDIT);
			}
		}else{
			page_display($op, '032', $TPL_SHIPDOC_PACK_ADD);
		}
	break;		
*/	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_packet_bar_edit":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "do_packet_bar_edit":
 	check_authority('032',"add");
		
		$mk = 0;
		for($i=0; $i<sizeof($PHP_ord_id); $i++)
		{
			foreach ($PHP_qty[$PHP_ord_id[$i]] as $ln_key => $ln_value)
			{
				$size_qty = '';
				if($PHP_qty[$PHP_ord_id[$i]][$ln_key]['s_ttl'] > 0)
				{					
					for($k=0; $k < $PHP_y_ln[$i]; $k++)
					{
						 $size_qty .= $PHP_qty[$PHP_ord_id[$i]][$ln_key][$k].",";
					}
					$size_qty = substr($size_qty,0,-1);
					$PHP_cnum = $PHP_e_ctn - $PHP_s_ctn + 1;
					if($mk == 0)
					{
						$parm = array(
									'size_qty'	=>	$size_qty,			'id'			=>	$PHP_shp_id[$PHP_ord_id[$i]][$ln_key],		
									'size_box'	=>	'',							'nnw'			=>	$PHP_nnw[$PHP_ord_id[$i]][$ln_key],
									'nw'				=>	$PHP_nw[$PHP_ord_id[$i]][$ln_key],				
									'gw'				=>	$PHP_gw[$PHP_ord_id[$i]][$ln_key],
									'qty'				=>	$PHP_ctn_qty,		'ctn_l'		=>	$PHP_l,
									'ctn_w'			=>	$PHP_w,					'ctn_h'		=>	$PHP_h,
									'cnum'			=> 	$PHP_cnum,			'ppk'			=>	$PHP_ppk[$PHP_ord_id[$i]][$ln_key],
									'f_mk'			=>	1,							'ctn'			=> 	$PHP_s_ctn.'-'.$PHP_e_ctn,							
									'ctn_id'		=>	$PHP_ctn_id[$PHP_ord_id[$i]][$ln_key],
									'ucc'				=>	$PHP_ucc[$PHP_ord_id[$i]][$ln_key],
									's_qty'			=>	$PHP_qty[$PHP_ord_id[$i]][$ln_key]['s_ttl'],
									's_cnum'		=>	$PHP_cnum,			'ctn_mk'	=>	$PHP_ctn_mk,
									'fit'				=>	$PHP_fit[$PHP_ord_id[$i]][$ln_key],
										);
					}else{
						$parm = array(
									'size_qty'	=>	$size_qty,			'id'			=>	$PHP_shp_id[$PHP_ord_id[$i]][$ln_key],		
									'size_box'	=>	'',							'nnw'			=>	$PHP_nnw[$PHP_ord_id[$i]][$ln_key],
									'nw'				=>	$PHP_nw[$PHP_ord_id[$i]][$ln_key],				
									'gw'				=>	$PHP_gw[$PHP_ord_id[$i]][$ln_key],
									'qty'				=>	'',							'ctn_l'		=>	$PHP_l,
									'ctn_w'			=>	$PHP_w,					'ctn_h'		=>	$PHP_h,
									'cnum'			=> 	'',							'ppk'			=>	$PHP_ppk[$PHP_ord_id[$i]][$ln_key],
									'f_mk'			=>	2	,							'ctn'			=> 	$PHP_s_ctn.'-'.$PHP_e_ctn,							
									'ctn_id'		=>	$PHP_ctn_id[$PHP_ord_id[$i]][$ln_key],
									'ucc'				=>	$PHP_ucc[$PHP_ord_id[$i]][$ln_key],
									's_qty'			=>	$PHP_qty[$PHP_ord_id[$i]][$ln_key]['s_ttl'],
									's_cnum'		=>	$PHP_cnum,			'ctn_mk'	=>	$PHP_ctn_mk,
									'fit'				=>	$PHP_fit[$PHP_ord_id[$i]][$ln_key],
										);
					}
					$mk = 1;
					$f1 = $ship_doc->edit_pack_det($parm);
				}else{
					$ship_doc->del_pack_det($PHP_shp_id[$PHP_ord_id[$i]][$ln_key]);
				}
			}			
		}
		$mesg = "success update carton:[ ".$PHP_s_ctn.'-'.$PHP_e_ctn." ] ON P/L :[ ".$PHP_inv_num." ]";

		$redir_str = 'shipdoc.php?PHP_action=packet_bar_add&PHP_id='.$PHP_id."&PHP_msg=".$mesg."&PHP_from=".$PHP_from;
		redirect_page($redir_str);		


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_packet_edit":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "do_packet_inbox_edit":
 	check_authority('032',"add");
		$size_qty = $size_box ='';
//		$PHP_ctn_qty = 0;
		$size_box = '';
		

		$mk = 0;
		for($i=0; $i<sizeof($PHP_ord_id); $i++)
		{
			foreach ($PHP_qty[$PHP_ord_id[$i]] as $ln_key => $ln_value)
			{
				$size_box = $size_qty = '';
				if($PHP_qty[$PHP_ord_id[$i]][$ln_key]['s_ttl'] > 0)
				{					
					for($k=0; $k < $PHP_y_ln[$i]; $k++)
					{
						 $size_qty .= $PHP_qty[$PHP_ord_id[$i]][$ln_key][$k].",";
						 $size_box .= $PHP_ibox[$PHP_ord_id[$i]][$ln_key][$k].",";
//						if($sz_key != 's_ttl') $PHP_ctn_qty += $sz_value;
					}
					$size_qty = substr($size_qty,0,-1);
					$size_box = substr($size_box,0,-1);
					$PHP_cnum = $PHP_e_ctn - $PHP_s_ctn + 1;
					if($mk == 0)
					{
						$parm = array(
									'size_qty'	=>	$size_qty,			'id'		=>	$PHP_shp_id[$PHP_ord_id[$i]][$ln_key],		
									'size_box'	=>	$size_box,			'nnw'		=>	$PHP_nnw[$PHP_ord_id[$i]][$ln_key],
									'nw'				=>	$PHP_nw[$PHP_ord_id[$i]][$ln_key],				
									'gw'				=>	$PHP_gw[$PHP_ord_id[$i]][$ln_key],
									'qty'				=>	$PHP_ctn_qty,		'ctn_l'	=>	$PHP_l,
									'ctn_w'			=>	$PHP_w,					'ctn_h'	=>	$PHP_h,
									'cnum'			=> 	$PHP_cnum,			'ppk'		=>	$PHP_ppk[$PHP_ord_id[$i]][$ln_key],
									'f_mk'			=>	1,							'ctn'		=> 	$PHP_s_ctn.'-'.$PHP_e_ctn,							
									'ctn_id'		=>	$PHP_ctn_id[$PHP_ord_id[$i]][$ln_key],
									'ucc'				=>	$PHP_ucc[$PHP_ord_id[$i]][$ln_key],
									's_qty'			=>	$PHP_qty[$PHP_ord_id[$i]][$ln_key]['s_ttl'],
									's_cnum'		=>	$PHP_cnum,			'ctn_mk'	=>	'',
									'fit'				=>	$PHP_fit[$PHP_ord_id[$i]][$ln_key],
										);
					}else{
						$parm = array(
									'size_qty'	=>	$size_qty,			'id'		=>	$PHP_shp_id[$PHP_ord_id[$i]][$ln_key],		
									'size_box'	=>	$size_box,			'nnw'		=>	$PHP_nnw[$PHP_ord_id[$i]][$ln_key],
									'nw'				=>	$PHP_nw[$PHP_ord_id[$i]][$ln_key],				
									'gw'				=>	$PHP_gw[$PHP_ord_id[$i]][$ln_key],
									'qty'				=>	'',							'ctn_l'	=>	$PHP_l,
									'ctn_w'			=>	$PHP_w,					'ctn_h'	=>	$PHP_h,
									'cnum'			=> 	'',							'ppk'		=>	$PHP_ppk[$PHP_ord_id[$i]][$ln_key],
									'f_mk'			=>	2	,							'ctn'		=> 	$PHP_s_ctn.'-'.$PHP_e_ctn,							
									'ctn_id'		=>	$PHP_ctn_id[$PHP_ord_id[$i]][$ln_key],
									'ucc'				=>	$PHP_ucc[$PHP_ord_id[$i]][$ln_key],
									's_qty'			=>	$PHP_qty[$PHP_ord_id[$i]][$ln_key]['s_ttl'],
									's_cnum'		=>	$PHP_cnum,			'ctn_mk'	=>	'',
									'fit'				=>	$PHP_fit[$PHP_ord_id[$i]][$ln_key],
										);
					}
					$mk = 1;
					$f1 = $ship_doc->edit_pack_det($parm);
				}else{
					$ship_doc->del_pack_det($PHP_shp_id[$PHP_ord_id[$i]][$ln_key]);
				}
			}			
		}
		$mesg = "success update carton:[ ".$PHP_s_ctn.'-'.$PHP_e_ctn." ] ON P/L :[ ".$PHP_inv_num." ]";

		$redir_str = 'shipdoc.php?PHP_action=packet_inbox_add&PHP_id='.$PHP_id."&PHP_msg=".$mesg."&PHP_from=".$PHP_from;
		redirect_page($redir_str);


/*
		foreach ($PHP_qty as $key => $value)
		{
			$size_qty .= $value.',';
			$size_box .= $PHP_ibox[$key].',';
			$PHP_ctn_qty += ($value * $PHP_ibox[$key]);
			
		}		
		$size_qty = substr($size_qty,0,-1);
		$size_box = substr($size_box,0,-1);
		
		$PHP_cnum = $PHP_e_ctn - $PHP_s_ctn + 1;
	
		$parm = array(
									'id'				=>	$PHP_shp_id,
									'size_qty'	=>	$size_qty,
									'size_box'	=>	$size_box,
									'nnw'				=>	$PHP_nnw,
									'nw'				=>	$PHP_nw,
									'gw'				=>	$PHP_gw,
									'qty'				=>	$PHP_ctn_qty,
									'ctn_l'			=>	$PHP_l,
									'ctn_w'			=>	$PHP_w,
									'ctn_h'			=>	$PHP_h,
									'cnum'			=> 	$PHP_cnum,
									'ppk'				=>	$PHP_ppk,
									'ctn_id'		=>	$PHP_ctn_id
									);
		$parm['ctn'] = $PHP_s_ctn.'-'.$PHP_e_ctn;									
		$f1 = $ship_doc->edit_pack_det($parm);
		
		
		
		
		
		
		$op = $ship_doc->get_order('',$PHP_ord_num);
		if(sizeof($op['color']))
		{
			$op['color_select'] = $arry2->select($op['color'],'','PHP_color','select','');
		}
		$op['assort'] = $ship_doc->count_size_qty($op['ord']['order_num'], $op['assort']);
		
		
		$shp_rec = $ship_doc->get($PHP_id);
		$op['shp'] = $shp_rec['shp'];
		$op['shp_det'] = $shp_rec['shp_det'];
	
		$where_str = "WHERE cust='".$op['ord']['cust']."'";
		$csgn = $cust->get_csge_fields('s_name',$where_str);
		if(!$csgn)$csgn=array('');
		$op['csgn_select'] = $arry2->select($csgn,$op['shp']['consignee'],'PHP_consignee','select','');
	
		
		if(isset($PHP_back_str))
		{
			$op['back_str'] = $PHP_back_str;
			if(isset($PHP_cfm))
			{
				page_display($op, '033', $TPL_SHIPDOC_PACK_INBOX_EDIT_CFM);
			}else{
				page_display($op, '032', $TPL_SHIPDOC_PACK_INBOX_EDIT);
			}
		}else{
			page_display($op, '032', $TPL_SHIPDOC_PACK_INBOX_ADD);
		}
	break;			
	*/
		
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "ship_pack_del_ajx":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "ship_pack_del_ajx":
 	check_authority('032',"add");
 		$ship_det = $ship_doc->get_pack_det($PHP_id);
 		for( $i=0; $i<sizeof($ship_det); $i++)
 		{
 			$f1 = $ship_doc->del_pack_det($ship_det[$i]['id']);

 		}
 		
	
		$mesg = "success DELETE carton: [".$ship_det[0]['cnt'].']';

		if($PHP_inbox == 1){$PHP_action = 'packet_inbox_add';}else{$PHP_action = 'packet_add';}
		if($f1 == 2)
		{
		 $redir_str = 'shipdoc.php?PHP_action='.$PHP_action.'&PHP_id='.$ship_det[0]['ship_id']."&PHP_msg=".$mesg;
		}else{
			$redir_str = 'shipdoc.php?PHP_action='.$PHP_action.'&PHP_id='.$ship_det[0]['ship_id']."&PHP_from=".$PHP_from."&PHP_msg=".$mesg;
		}
		redirect_page($redir_str);
 		
	/*	
		$size_qty = explode(',',$ship_det[0]['size_qty']);
		
		$ttl = 0;
		$msg = "Successfully delete CTN :".$PHP_ctn." ON Order# : ".$PHP_ord_num;
		echo $f1."|".$msg."|";
		echo $ship_det[0]['ord_num']."|".$ship_det[0]['color']."|";
		for($i=0; $i<sizeof($size_qty); $i++)
		{
			echo NUMBER_FORMAT($size_qty[$i]*$ship_det[0]['cnum'],0,'','')."|";
			$ttl +=NUMBER_FORMAT($size_qty[$i]*$ship_det[0]['cnum'],0,'','');
		}
		echo $ttl;
	break;				
*/	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "other_order_search":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "other_order_search":
 	check_authority('032',"add");
 			
		$op['fty'] = "<B>".$PHP_fty."</B> <input type=hidden name='PHP_fty' value='$PHP_fty'>";
		$op['cust_select'] = "<B>".$PHP_cust."</B> <input type=hidden name='PHP_cust' value='$PHP_cust'>";
		
		
	page_display($op, '032', $TPL_SHIPDOC_ADD_SUB_LIST);
	break;	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "order_shiped_search":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "order_shiped_sub_search":
 	check_authority('032',"add");
		
//		$op = $ship_doc->add_search(1);
		$op['back_str'] = "&PHP_fty=".$PHP_fty."&PHP_cust=".$PHP_cust."&PHP_num=".$PHP_num;
		$op['fty'] = "<B>".$PHP_fty."</B> <input type=hidden name='PHP_fty' value='$PHP_fty'>";
		$op['cust_select'] = "<B>".$PHP_cust."</B> <input type=hidden name='PHP_cust' value='$PHP_cust'>";

	page_display($op, '032', $TPL_SHIPDOC_ADD_SUB_LIST);
	break;
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "packet_order_chg":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "packet_order_chg":
 	check_authority('032',"add");
		
		$op = $ship_doc->get_order($PHP_ord_id);
		if(sizeof($op['color']))
		{
			$op['color_select'] = $arry2->select($op['color'],'','PHP_color','select','');
		}
		$op['assort'] = $ship_doc->count_size_qty($op['ord']['order_num'], $op['assort']);
		
		
		$shp_rec = $ship_doc->get($PHP_id);
		$op['shp'] = $shp_rec['shp'];
		$op['shp_det'] = $shp_rec['shp_det'];

		$where_str = "WHERE cust='".$op['ord']['cust']."'";
		$csgn = $cust->get_csge_fields('s_name',$where_str);
		if(!$csgn)$csgn=array('');
		$op['csgn_select'] = $arry2->select($csgn,$op['shp']['consignee'],'PHP_consignee','select','');

		if($op['shp']['inner_box'] == 0)
		{
			if(isset($PHP_sr_startno))
			{
			
				$op['back_str'] = "&SCH_inv=".$SCH_inv."&SCH_date_str=".$SCH_date_str."&SCH_date_end=".$SCH_date_end."&SCH_des=".$SCH_des."&PHP_fty=".$PHP_fty."&PHP_cust=".$PHP_cust."&PHP_sr_startno=".$PHP_sr_startno."&SCH_ord=".$SCH_ord;
				if(isset($PHP_cfm))
				{
					page_display($op, '033', $TPL_SHIPDOC_PACK_EDIT_CFM);
				}else{
					page_display($op, '032', $TPL_SHIPDOC_PACK_EDIT);
				}
			}else{
				page_display($op, '032', $TPL_SHIPDOC_PACK_ADD);
			}
		}else{
			if(isset($PHP_sr_startno))
			{
				$op['back_str'] = "&SCH_inv=".$SCH_inv."&SCH_date_str=".$SCH_date_str."&SCH_date_end=".$SCH_date_end."&SCH_des=".$SCH_des."&PHP_fty=".$PHP_fty."&PHP_cust=".$PHP_cust."&PHP_sr_startno=".$PHP_sr_startno."&SCH_ord=".$SCH_ord;
				if(isset($PHP_cfm))
				{
					page_display($op, '033', $TPL_SHIPDOC_PACK_INBOX_EDIT_CFM);
				}else{
					page_display($op, '032', $TPL_SHIPDOC_PACK_INBOX_EDIT);
				}
			}else{
				page_display($op, '032', $TPL_SHIPDOC_PACK_INBOX_ADD);
			}		
		}
	break;		
	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "shipdoc_search":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "shipdoc_search":
 	check_authority('034',"view");
		if(isset($SCH_inv))
		{
			$sch_parm = array();
			$sch_parm = array(
												'SCH_inv'					=>	$SCH_inv,
												'SCH_date_str'		=>	$SCH_date_str,
												'SCH_date_end'		=>	$SCH_date_end,
												'SCH_des'					=>	$SCH_des,
												'PHP_fty'					=>	$PHP_fty,
												'PHP_cust'				=>	$PHP_cust,
												'SCH_ord'					=>	$SCH_ord,
												'PHP_action'			=>	$PHP_action,
												'PHP_sr_startno'	=>	$PHP_sr_startno
												);
			}else{
				if(isset($PHP_sr_startno))$sch_parm ['PHP_sr_startno'] = $PHP_sr_startno;
			}
		$op = $ship_doc->search(1);
		for($i=0; $i<sizeof($op['ship_doc']); $i++)
		{
			$op['ship_doc'][$i]['ord_num'] = $ship_doc->get_ship_ord('ord_num', $op['ship_doc'][$i]['id']);
			$op['ship_doc'][$i]['cust_po'] = $ship_doc->get_ship_ord('cust_po', $op['ship_doc'][$i]['id']);
		}
		
//		$op['back_str'] = "&SCH_inv=".$SCH_inv."&SCH_date_str=".$SCH_date_str."&SCH_date_end=".$SCH_date_end."&SCH_des=".$SCH_des."&PHP_fty=".$PHP_fty."&PHP_cust=".$PHP_cust."&SCH_ord=".$SCH_ord;
		if(isset($PHP_msg))$op['msg'][]=$PHP_msg;
	page_display($op, '034', $TPL_SHIPDOC_INV_LIST);
	break;	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "order_shiped_search":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "packing_search":
 	check_authority('032',"view");

		if(isset($SCH_inv))
		{
			$sch_parm = array();
			$sch_parm = array(
												'SCH_inv'					=>	$SCH_inv,
												'SCH_date_str'		=>	$SCH_date_str,
												'SCH_date_end'		=>	$SCH_date_end,
												'SCH_des'					=>	$SCH_des,
												'PHP_fty'					=>	$PHP_fty,
												'PHP_cust'				=>	$PHP_cust,
												'SCH_ord'					=>	$SCH_ord,
												'PHP_action'			=>	$PHP_action,
												'PHP_sr_startno'	=>	$PHP_sr_startno
												);
			}else{
				if(isset($PHP_sr_startno))$add_sch ['sr_startno'] = $PHP_sr_startno;
			}				
		
		$op = $ship_doc->search(1);
		for($i=0; $i<sizeof($op['ship_doc']); $i++)
		{
			$op['ship_doc'][$i]['ord_num'] = $ship_doc->get_ship_ord('ord_num', $op['ship_doc'][$i]['id']);
			$op['ship_doc'][$i]['cust_po'] = $ship_doc->get_ship_ord('cust_po', $op['ship_doc'][$i]['id']);
		}
		
//		$op['back_str'] = "&SCH_inv=".$SCH_inv."&SCH_date_str=".$SCH_date_str."&SCH_date_end=".$SCH_date_end."&SCH_des=".$SCH_des."&PHP_fty=".$PHP_fty."&PHP_cust=".$PHP_cust."&SCH_ord=".$SCH_ord;
		if(isset($PHP_msg))$op['msg'][]=$PHP_msg;
	page_display($op, '032', $TPL_SHIPDOC_LIST);
	break;	
	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_packet_add_show":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "do_packet_add_show":
 	check_authority('032',"add");
 	
 	
/* 	
		if(!$PHP_inv)
		{
			$op = $ship_doc->get_order($PHP_ord_id);
			if(sizeof($op['color']))
			{
				$op['color_select'] = $arry2->select($op['color'],'','PHP_color','select','');
			}
			$op['assort'] = $ship_doc->count_size_qty($op['ord']['order_num'], $op['assort']);
		
		
			$shp_rec = $ship_doc->get($PHP_id);
			$op['shp'] = $shp_rec['shp'];
			$op['shp_det'] = $shp_rec['shp_det'];

			$where_str = "WHERE cust='".$op['ord']['cust']."'";
			$csgn = $cust->get_csge_fields('s_name',$where_str);
			if(!$csgn)$csgn=array('');
			$op['csgn_select'] = $arry2->select($csgn,$op['shp']['consignee'],'PHP_consignee','select','');



			$op['msg'][] = "please input invoice or ship date first.";
			if($PHP_inner_box == 1)
			{
				page_display($op, '032', $TPL_SHIPDOC_PACK_INBOX_ADD);
			}else{
				page_display($op, '032', $TPL_SHIPDOC_PACK_ADD);
			}
			break;

		}
		
		
		if($PHP_inv)	$ship_doc->update_field_main('inv_num',$PHP_inv,$PHP_id);

		if($PHP_ship_date)$ship_doc->update_field_main('ship_date',$PHP_ship_date,$PHP_id);
		if($PHP_cust)			$ship_doc->update_field_main('cust',$PHP_cust,$PHP_id);
		if($PHP_ship_term)$ship_doc->update_field_main('ship_term',$PHP_ship_term,$PHP_id);
		if($PHP_ship_via)	$ship_doc->update_field_main('ship_via',$PHP_ship_via,$PHP_id);
		if($PHP_ship_from)$ship_doc->update_field_main('ship_from',$PHP_ship_from,$PHP_id);
		if($PHP_ship_to)	$ship_doc->update_field_main('ship_to',$PHP_ship_to,$PHP_id);
		if($PHP_des)			$ship_doc->update_field_main('des',$PHP_des,$PHP_id);
		if($PHP_pack)			$ship_doc->update_field_main('pack',$PHP_pack,$PHP_id);
		$PHP_vendor = $PHP_vendor."|".$PHP_vendor2;
		if($PHP_vendor)		$ship_doc->update_field_main('vendor',$PHP_vendor,$PHP_id);
		if($PHP_consignee)$ship_doc->update_field_main('consignee',$PHP_consignee,$PHP_id);

		if($PHP_fnl_name)$ship_doc->update_field_main('fnl_name',$PHP_fnl_name,$PHP_id);
		if($PHP_fnl_addr)$ship_doc->update_field_main('fnl_addr',$PHP_fnl_addr,$PHP_id);
		if($PHP_fnl_style)$ship_doc->update_field_main('fnl_style',$PHP_fnl_style,$PHP_id);
		if($PHP_fnl_po)$ship_doc->update_field_main('fnl_po',$PHP_fnl_po,$PHP_id);
		if($PHP_del_due)$ship_doc->update_field_main('del_due',$PHP_del_due,$PHP_id);
		if($PHP_fnl_des)$ship_doc->update_field_main('fnl_des',$PHP_fnl_des,$PHP_id);
		if($PHP_fnl_dept)$ship_doc->update_field_main('fnl_dept',$PHP_fnl_dept,$PHP_id);
		if($PHP_dept_des)$ship_doc->update_field_main('dept_des',$PHP_dept_des,$PHP_id);
		if($PHP_ctn_num)$ship_doc->update_field_main('ctn_num',$PHP_ctn_num,$PHP_id);
		if($PHP_seal)$ship_doc->update_field_main('seal',$PHP_seal,$PHP_id);
		$ship_doc->update_field_main('que1',$PHP_que1,$PHP_id);
		$ship_doc->update_field_main('que2',$PHP_que2,$PHP_id);
		$ship_doc->update_field_main('que3',$PHP_que3,$PHP_id);
		$ship_doc->update_field_main('que4',$PHP_que4,$PHP_id);
		$ship_doc->update_field_main('que5',$PHP_que5,$PHP_id);		
*/		

	if($PHP_unit)	$ship_doc->update_field_main('unit',$PHP_unit,$PHP_id);
	$sch_parm = array();
	$sch_parm = array(
										'SCH_inv'					=>	'',			'SCH_date_str'		=>	'',
										'SCH_date_end'		=>	'',			'SCH_des'					=>	'',
										'PHP_fty'					=>	'',			'PHP_cust'				=>	'',
										'SCH_ord'					=>	'',			'PHP_action'			=>	'',
										'PHP_sr_startno'	=>	''
										);
		
		$mesg = "Successfully append packet list on INV# : ".$PHP_inv;
		$shp_rec = $ship_doc->get_pack($PHP_id);
		$op['shp'] = $shp_rec['shp'];
		$op['shp_det'] = $shp_rec['shp_det'];
		$op['amt'] = $shp_rec['amt'];
		$op['shp_log'] = $shp_rec['shp_log'];
		$op['msg'][] = $mesg;

			if($PHP_inner_box == 1)
			{
				page_display($op, '032', $TPL_SHIPDOC_PACK_INBOX_VIEW);
			}else{
				page_display($op, '032', $TPL_SHIPDOC_PACK_VIEW);
			}		
	
	break;		
		
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_packet_add_show":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "shipdoc_p_view":
 	check_authority('032',"view");

		$op = $ship_doc->get_pack($PHP_id);
/*		
		$op['shp'] = $shp_rec['shp'];
		$op['shp_det'] = $shp_rec['shp_det'];
		$op['amt'] = $shp_rec['amt'];
		$op['shp_log'] = $shp_rec['shp_log'];
		$op['break'] = $shp_rec['break'];
*/		
//if(isset($SCH_inv))		$op['back_str'] = "&SCH_inv=".$SCH_inv."&SCH_date_str=".$SCH_date_str."&SCH_date_end=".$SCH_date_end."&SCH_des=".$SCH_des."&PHP_fty=".$PHP_fty."&PHP_cust=".$PHP_cust."&PHP_sr_startno=".$PHP_sr_startno."&SCH_ord=".$SCH_ord;
	if(isset($PHP_msg)) $op['msg'][] = $PHP_msg;
	if($op['shp']['inner_box'] == 1)
	{
		page_display($op, '032', $TPL_SHIPDOC_PACK_INBOX_VIEW);
	}else if($op['shp']['bar_mk'] == 1){
		page_display($op, '032', $TPL_SHIPDOC_PACK_BAR_VIEW);
	}else{
		page_display($op, '032', $TPL_SHIPDOC_PACK_VIEW);
	}
	break;				
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "shipdoc_pack_edit":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "shipdoc_pack_edit":
 	check_authority('032',"add");
			
		$ord_parm = array();	
		$shp_rec = $ship_doc->get($PHP_id);
		$ord_parm =	$ship_doc->get_pl_ord($PHP_id);		
		$op = $ship_doc->get_order_by_add($ord_parm,$shp_rec['shp']['inner_box']);
		for($i=0; $i<sizeof($op['assort']); $i++) $op['assort'][$i]= $ship_doc->count_size_qty($op['ord'][$i]['order_num'], $op['assort'][$i]);
		
//		$shp_rec = $ship_doc->get($PHP_id);
		$op['shp'] = $shp_rec['shp'];
		$op['shp_det'] = $shp_rec['shp_det'];
		
/*		
		$jj = sizeof($shp_rec['shp_det']) - 1;
		$op = $ship_doc->get_order('',$shp_rec['shp_det'][$jj]['ord_num']);
		if(sizeof($op['color']))
		{
			$op['color_select'] = $arry2->select($op['color'],'','PHP_color','select','');
		}
		$op['assort'] = $ship_doc->count_size_qty($op['ord']['order_num'], $op['assort']);
*/		
		
		
		$where_str = "WHERE cust='".$op['shp']['ord_cust']."'";
		$csgn = $cust->get_csge_fields('s_name',$where_str);
		if(!$csgn)$csgn=array('');
		$op['csgn_select'] = $arry2->select($csgn,$op['shp']['consignee'],'PHP_consignee','select','');
//		$op['back_str'] = $PHP_back_str;
		$op['action'] = 'shipdoc_search';
		
	if($op['shp']['inner_box'] == 1)
	{
		page_display($op, '032', $TPL_SHIPDOC_PACK_INBOX_EDIT);
	}else if($op['shp']['bar_mk'] == 1){
		page_display($op, '032', $TPL_SHIPDOC_PACK_BAR_EDIT);
	}else{
		page_display($op, '032', $TPL_SHIPDOC_PACK_EDIT);
	}
	
	break;		
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_packet_edit_show":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "do_packet_edit_show":
 	check_authority('032',"add");

		if($PHP_unit)	$ship_doc->update_field_main('unit',$PHP_unit,$PHP_id);
		
		$mesg = "Successfully Update packet list on INV# : ".$PHP_inv;
		$op = $ship_doc->get_pack($PHP_id);

		$op['msg'][] = $mesg;
//		$op['back_str'] = $PHP_back_str;
		
	if($op['shp']['inner_box'] == 1)
	{
		page_display($op, '032', $TPL_SHIPDOC_PACK_INBOX_VIEW);
	}else if($op['shp']['bar_mk'] == 1){
		page_display($op, '032', $TPL_SHIPDOC_PACK_BAR_VIEW);
	}else{
		page_display($op, '032', $TPL_SHIPDOC_PACK_VIEW);
	}
	break;	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "shipdoc_i_view":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "shipdoc_i_view":
 	check_authority('034',"view");

		$op = $ship_doc->get_inv($PHP_id);
		
		for($i=0; $i<sizeof($op['shp_charge']); $i++)
		{
			$op['amt']['amount'] -= $op['shp_charge'][$i]['charge'];
		}
		$op['amt']['eng_amount'] = engilsh_math($op['amt']['amount'])." ONLY";	
		
/*		
		$op['shp'] = $shp_rec['shp'];
		$op['shp_det'] = $shp_rec['shp_det'];
		$op['amt'] = $shp_rec['amt'];
*/
		$op['shp']['fty_det'] = str_replace( chr(13).chr(10), "<br>",  $op['shp']['fty_det'] );
		$op['shp']['ship_det'] = str_replace( chr(13).chr(10), "<br>", $op['shp']['ship_det'] );
		$op['shp']['benf_det'] = str_replace( chr(13).chr(10), "<br>", $op['shp']['benf_det'] );
		$op['shp']['ship_mark'] = str_replace( chr(13).chr(10), "<br>", $op['shp']['ship_mark'] );
		$op['shp']['others'] = str_replace( chr(13).chr(10), "<br>", $op['shp']['others'] );
		$op['shp']['fab_des'] = str_replace( chr(13).chr(10), "<br>", $op['shp']['fab_des'] );
		$op['shp']['nty_part'] = str_replace( chr(13).chr(10), "<br>", $op['shp']['nty_part'] );
		$op['shp']['stmt'] = str_replace( chr(13).chr(10), "<br>", $op['shp']['stmt'] );
		$op['shp']['comm'] = str_replace( chr(13).chr(10), "<br>", $op['shp']['comm'] );


		if(isset($PHP_msg)) $op['msg'][] = $PHP_msg;
/*
		if(isset($PHP_back_str))
		{
			$op['back_str'] = $PHP_back_str;
		}else{
			$op['back_str'] = "&SCH_inv=".$SCH_inv."&SCH_date_str=".$SCH_date_str."&SCH_date_end=".$SCH_date_end."&SCH_des=".$SCH_des."&PHP_fty=".$PHP_fty."&PHP_cust=".$PHP_cust."&PHP_sr_startno=".$PHP_sr_startno."&SCH_ord=".$SCH_ord;
		}
*/		
	page_display($op, '034', $TPL_SHIPDOC_INV_VIEW);
	break;					
	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "shipdoc_i_edit":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "shipdoc_i_edit":
 	check_authority('034',"view");
		$sort = " s_order.order_num, shipdoc_ord.cust_po ";
		$shp_rec = $ship_doc->get_inv($PHP_id, $sort);
		$op['shp'] = $shp_rec['shp'];
		$op['shp_det'] = $shp_rec['shp_det'];
		$op['shp_charge'] =$shp_rec['shp_charge'];
		
		if(!$op['shp']['fty_det'])
		{
			$op['shp']['fty_det'] .= $SHIP_TO[$op['shp']['fty']]['Name'].chr(13).chr(10);
			$op['shp']['fty_det'] .= $SHIP_TO[$op['shp']['fty']]['Addr'].chr(13).chr(10);
			$op['shp']['fty_det'] .= "TEL : ".$SHIP_TO[$op['shp']['fty']]['TEL'].chr(13).chr(10);
			$op['shp']['fty_det'] .= "FAX : ".$SHIP_TO[$op['shp']['fty']]['FAX'].chr(13).chr(10);
		}
		
		if(!$op['shp']['ship_det'])
		{
			$op['shp']['ship_det'] .= $SHIP_TO[$op['shp']['fty']]['Name'].chr(13).chr(10);
			$op['shp']['ship_det'] .= $SHIP_TO[$op['shp']['fty']]['Addr'].chr(13).chr(10);
			$op['shp']['ship_det'] .= "TEL : ".$SHIP_TO[$op['shp']['fty']]['TEL'].chr(13).chr(10);
			$op['shp']['ship_det'] .= "FAX : ".$SHIP_TO[$op['shp']['fty']]['FAX'].chr(13).chr(10);
		}		

		$op['shp']['mid_select'] = $arry2->select($MID ,$op['shp']['mid_no'],"PHP_mid_no","select"); 
		$op['shp']['country_select'] = $arry2->select($SHIP_COUNTRY ,$op['shp']['country_org'],"PHP_country_org","select");  

		foreach($CONTAINER as $key => $value)
		{
			$conter[] = $key;
			$conter_name[] = $CONTAINER[$key]['name'];
		}
		$op['shp']['conter_select'] = $arry2->select($conter_name ,$op['shp']['conter'],"PHP_conter","select",'',$conter);  
		$op['shp']['constor_select'] = $arry2->select($conter_name ,$op['shp']['constor'],"PHP_constor","select",'',$conter);  

		
/*		
		if(isset($PHP_back_str))
		{
			$op['back_str'] = $PHP_back_str;
		}else{
			$op['back_str'] = "&SCH_inv=".$SCH_inv."&SCH_date_str=".$SCH_date_str."&SCH_date_end=".$SCH_date_end."&SCH_des=".$SCH_des."&PHP_fty=".$PHP_fty."&PHP_cust=".$PHP_cust."&PHP_sr_startno=".$PHP_sr_startno."&SCH_ord=".$SCH_ord;
		}
*/
		$where_str = "WHERE cust='".$op['shp']['ord_cust']."'";
		$csgn = $cust->get_csge_fields('s_name',$where_str);
		if(!$csgn)$csgn=array('');
		$op['csgn_select'] = $arry2->select($csgn,$op['shp']['consignee'],'PHP_consignee','select','');

		for($i=0; $i<sizeof($SHIP_PO_TYPE); $i++) $po_key[] = $i+1;
		for($i=0; $i<sizeof($SHIP_FACILLITY); $i++) $fact_key[] = $i+1;
		for($i=0; $i<sizeof($SHIP_MODE); $i++) $mode_key[] = $i+1;
		for($i=0; $i<sizeof($SHIP_METHOD); $i++) $method_key[] = $i+1;

//		$op['shp']['po_type_select'] = $arry2->select($SHIP_PO_TYPE ,$op['shp']['po_type'],"PHP_po_type","select",'',$po_key);  
//		$op['shp']['facility_select'] = $arry2->select($SHIP_FACILLITY ,$op['shp']['facility'],"PHP_facility","select",'',$fact_key);  
//		$op['shp']['mode_select'] = $arry2->select($SHIP_MODE ,$op['shp']['mode'],"PHP_mode","select",'',$mode_key);  
//		$op['shp']['method_select'] = $arry2->select($SHIP_METHOD ,$op['shp']['mode'],"PHP_method","select",'',$method_key);  

		if(isset($PHP_msg))$op['msg'][] = $PHP_msg;
	page_display($op, '034', $TPL_SHIPDOC_INV_EDIT);
	break;	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "ship_charge_add":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "ship_charge_add":
    
    $f1 = $ship_doc->add_charge($PHP_id,$PHP_charge,$PHP_chg_des);
    
    $msg = "SUCCESSFULLY ADD Other Charge";

  	$edit_btn =	"<input type=button onClick=\"edit_charge('".$f1."')\" value=Update  class=select_e style=\"width=50%\"> ";
	  $del_tbn  =	"<input type='image' src='images/del.png' onclick=\"del_ajx('".$f1."',this)\"> ";
  	$charge =	"<input type='text' name='PHP_up_chg_".$f1."' size=10 value='".$PHP_charge."' class=select>";
  	$des = "<input type='text' name='PHP_up_des_".$f1."' size=105 value='".$PHP_chg_des."' class=select>";


    echo $msg."|".$charge."|".$des."|".$edit_btn.$del_tbn."|";
 		exit;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "ship_charge_edit":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "ship_charge_edit":
    
    $f1 = $ship_doc->edit_charge($PHP_id,$PHP_charge,$PHP_chg_des);    
    $msg = "SUCCESSFULLY Update Other Charge";
    echo $msg."|";
		exit;
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "ship_charge_del":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "ship_charge_del":
    
    $f1 = $ship_doc->del_charge($PHP_id);    
    $msg = "SUCCESSFULLY Delete Other Charge";
    echo $msg."|";
    exit;	
	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "other_ord_field_add":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "other_ord_field_add":
 	check_authority('034',"edit");
 	
 	$parm = array('field'		=>	$PHP_field,
 								'values'	=>	$PHP_values,
 								'id'			=>	$PHP_id,
 								'shp_id'	=>	$PHP_ship_id,
 								);
 	$f1 = $ship_doc->add_ord_field($parm);

 	$message = "SUCCESS APPEND OTHER FIELD ON :".$PHP_po;
 	$fd = "<font color=navy><B>".$PHP_field." : </B>".$PHP_values."</font>";
 	$btn = "<input type='image' src='images/del.png'   onclick=del_ord_field('".$PHP_tb."','".$f1."',this)>";
 	echo $message.$btn."|".$fd.$btn;
 	exit;
 	//$rstr = "shipdoc.php?PHP_action=shipdoc_i_edit&PHP_id=".$PHP_ship_id."&PHP_msg=".$message;
 	//redirect_page($rstr);
 	
 	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "other_ord_field_del":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "other_ord_field_del":
    
    $f1 = $ship_doc->del_ord_field($PHP_id);    
    $msg = "SUCCESSFULLY Delete Other field by customer po";
    echo $msg."|";
    exit;	 	
 	
 	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "other_field_add":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "other_field_add":
 	check_authority('034',"edit");
 	
 	$parm = array('field'		=>	$PHP_field,
 								'values'	=>	$PHP_values,
 								'id'			=>	$PHP_id,
 								);
 	$f1 = $ship_doc->add_other_field($parm);

 	$message = "SUCCESS APPEND OTHER FIELD";
 	$fd = "<font color=navy><B>".$PHP_field." : </B>".$PHP_values."</font>";
 	$btn = "<input type='image' src='images/del.png'   onclick=del_oth_field('".$f1."',this)>";
 	echo $message."|".$fd."|".$btn;
exit;
// 	$rstr = "shipdoc.php?PHP_action=shipdoc_i_edit&PHP_id=".$PHP_id."&PHP_msg=".$message;
// 	redirect_page($rstr); 	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "other_field_del":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "other_field_del":
    
    $f1 = $ship_doc->del_other_field($PHP_id);    
    $msg = "SUCCESSFULLY Delete Other field by customer po";
    echo $msg."|";
    exit;	
    	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_shipdoc_i_edit":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "do_shipdoc_i_edit":
 	check_authority('034',"view");
 	
 		if(!$ship_doc->check_inv($PHP_inv_num,$PHP_id))
 		{
 			$message = "invoice number is exist, please check again";
 			$rstr = "shipdoc.php?PHP_action=shipdoc_i_edit&PHP_id=".$PHP_id."&PHP_msg=".$message.$PHP_back_str;
 			redirect_page($rstr);
 		}
 	
 	
 	
 		$PHP_ship_addr = $PHP_ship_addr1."|".$PHP_ship_addr2;
 		$PHP_bill_addr = $PHP_bill_addr1."|".$PHP_bill_addr2;
 		if($PHP_consignee2)$PHP_consignee = $PHP_consignee2;
 		$parm = array(
 										'ship_term'		=>	$PHP_ship_term,
 										'ship_via'		=>	$PHP_ship_via,
 										'ship_from'		=>	$PHP_ship_from,
 										'ship_to'			=>	$PHP_ship_to,
 										'payment'			=>	$PHP_payment,
 										'des'					=>	$PHP_des,
 										'fv'					=>	$PHP_fv,
 										'fv_etd'			=>	$PHP_fv_etd,
 										'fv_eta'			=>	$PHP_fv_eta,
 										'mv'					=>	$PHP_mv,
 										'mv_etd'			=>	$PHP_mv_etd,
 										'mv_eta'			=>	$PHP_mv_eta,
 										'lc_no'				=>	$PHP_lc_no,
 										'lc_bank'			=>	$PHP_lc_bank,
 										'consignee'		=>	$PHP_consignee,
 										'ship_addr'		=>	$PHP_ship_addr,
 										'bill_addr'		=>	$PHP_bill_addr,
 										'fty_det'			=>	$PHP_fty_det,
 										'ship_det'		=>	$PHP_ship_det,
 										'benf_det'		=>	$PHP_benf_det,
 										'inv_num'			=>	$PHP_inv_num,
 										'ship_date'		=>	$PHP_ship_date,
 										'mid_no'			=>	$PHP_mid_no,
 										'country_org'	=>	$PHP_country_org,
 										'ship_mark'		=>	$PHP_ship_mark,
 										'others'			=>	$PHP_others,
 										'ship_by'			=>	$PHP_ship_by,
 										'carrier'			=>	$PHP_carrier,
 										'fab_des'			=>	$PHP_fab_des,
 										'dis_port'		=>	$PHP_dis_port,
 										'lc_date'			=>	$PHP_lc_date,
 										'cbm'					=>	$PHP_cbm,
 										
 										'nty_part'			=>	$PHP_nty_part,
 										'stmt'					=>	$PHP_stmt,
 										'comm'					=>	$PHP_comm,
 										'conter'				=>	$PHP_conter,
 										'constor'				=>	$PHP_constor,
 										 										
 										'id'						=>	$PHP_id
 									);
 
 									
 		$f1 = $ship_doc->edit_inv($parm);
 		
 		
 //原packing 修改項目		
		if($PHP_pack)			$ship_doc->update_field_main('pack',$PHP_pack,$PHP_id);
		$PHP_vendor = $PHP_vendor."|".$PHP_vendor2;
		if($PHP_vendor)		$ship_doc->update_field_main('vendor',$PHP_vendor,$PHP_id);
 		
 		
 		
		foreach($PHP_quota as $key => $value)
		{

			$f1=$order->update_field_num('ship_quota', $PHP_quota[$key],$key);  
			$f1=$order->update_field_num('quota_unit', $PHP_quota_unit[$key],$key);  
		}

		foreach($PHP_style_num as $key => $value)
		{
			$f1=$ship_doc->update_field_det('style_num',$PHP_style_num[$key],0,$key); 		
			$f1=$ship_doc->update_field_det('content',$PHP_content[$key],0,$key); 
			$f1=$ship_doc->update_field_det('des',$PHP_ord_des[$key],0,$key);
			$f1=$ship_doc->update_field_det('ship_fob',$PHP_uprice[$key],0,$key);		
			$f1=$ship_doc->update_field_det('hts_cat',$PHP_hts_cat[$key],0,$key); 
		}			
	
		$op = $ship_doc->get_inv($PHP_id);
		$op['shp']['fty_det'] = str_replace( chr(13).chr(10), "<br>",  $op['shp']['fty_det'] );
		$op['shp']['ship_det'] = str_replace( chr(13).chr(10), "<br>", $op['shp']['ship_det'] );
		$op['shp']['benf_det'] = str_replace( chr(13).chr(10), "<br>", $op['shp']['benf_det'] );
		$op['shp']['ship_mark'] = str_replace( chr(13).chr(10), "<br>", $op['shp']['ship_mark'] );
		$op['shp']['others'] = str_replace( chr(13).chr(10), "<br>", $op['shp']['others'] );
		$op['shp']['fab_des'] = str_replace( chr(13).chr(10), "<br>", $op['shp']['fab_des'] );
		$op['shp']['nty_part'] = str_replace( chr(13).chr(10), "<br>", $op['shp']['nty_part'] );
		$op['shp']['stmt'] = str_replace( chr(13).chr(10), "<br>", $op['shp']['stmt'] );
		$op['shp']['comm'] = str_replace( chr(13).chr(10), "<br>", $op['shp']['comm'] );



		$mesg = "SUCCESS EDIT INVOICE ON  [".$op['shp']['inv_num']."]";
		$op['back_str'] = $PHP_back_str;
		$op['msg'][] = $mesg;
		
	page_display($op, '034', $TPL_SHIPDOC_INV_VIEW);
	break;						
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "order_shiped_search":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "shipdoc_submit":
 	check_authority('034',"add");
//		$shp_fld = $ship_doc->get_apvd($PHP_id);

		$ship_doc->update_field_main('status',6,$PHP_id);
		$ship_doc->update_field_main('inv_sub_user',$GLOBALS['SCACHE']['ADMIN']['login_id'],$PHP_id);
		$ship_doc->update_field_main('inv_sub_date',$TODAY,$PHP_id);

		

		$mesg = "SUCCESS submit shipping document on inv : [".$PHP_inv_num."]";
		$log->log_add(0,"411S",$mesg);
		
		$redirect_str ="shipdoc.php?PHP_action=shipdoc_search".$PHP_back_str."&PHP_msg=".$mesg;
		redirect_page($redirect_str);





#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "order_shiped_search":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "packing_submit":
 	check_authority('032',"add");
		$shp_fld = $ship_doc->get_apvd($PHP_id);

		$over_ord = array();
		$mk = 0;
//判斷出貨是否有超量		
//		for($i = 0; $i<sizeof($shp_fld); $i++)
//		{
//			$tmp_qty = $shp_fld[$i]['rem_qty'] - $shp_fld[$i]['qty_shp'];
//			if($tmp_qty < 0)
//			{
//				$over_ord[] = $shp_fld[$i]['ord_num'];
//				$mk = 1;
//			}			
//		}
		$ship_doc->update_field_main('status',2,$PHP_id);
		$ship_doc->update_field_main('submit_user',$GLOBALS['SCACHE']['ADMIN']['login_id'],$PHP_id);
		$ship_doc->update_field_main('submit_date',$TODAY,$PHP_id);

//		for($i = 0; $i<sizeof($shp_fld); $i++)
//		{
//			$parm = array(	
//											"p_id"			=>	$shp_fld[$i]['p_id'],
//											"ord_num"		=>  $shp_fld[$i]['ord_num'],
//											"qty_shp"		=>  $shp_fld[$i]['qty_shp'],
//											"shp_date"	=>  $shp_fld[$i]['ship_date'],
//											"remain"		=>	$shp_fld[$i]['rem_qty'],
//				);			
//				$F = $order->add_shipping($parm,1);
//
//			//寫入shipping
////			$F = $shipping->add($parm);
//
//			// *********  寫入 pdtion->qty_shp, shp_date    *********************
//			
//			
//			//取得cpapcity for shipping
////			$T=$capaci->get($shp_fld[$i]['factory'], $THIS_YEAR,'shipping');
////			//加入capacity
////			$ky_mon = substr($THIS_MON,4,2);
////			$F = $capaci->update_su($shp_fld[$i]['factory'], $THIS_YEAR, $ky_mon, 'shipping', $shp_fld[$i]['qty_shp']);
////			$F = $capaci->update_su($shp_fld[$i]['factory'], $THIS_YEAR, $ky_mon, "shp_fob", $shp_fld[$i]['fob']);
//		}



		$mesg = "SUCCESS submit shipping document -- packing  on inv : [".$PHP_inv_num."]";
		$log->log_add(0,"48S",$mesg);
		
		$redirect_str ="shipdoc.php?PHP_action=packing_search&PHP_msg=".$mesg;

		redirect_page($redirect_str);



		
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "cfm_shipdoc":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "cfm_shipdoc":
 	check_authority('035',"add");
 		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
 		
		$where_str=" ORDER BY cust_s_name"; //依cust_s_name排序
		$cust_def = $cust->get_fields('cust_init_name',$where_str);//取出客戶簡稱
		$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//取出客戶代號
		for ($i=0; $i< sizeof($cust_def); $i++)
			$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//以 [客戶代號-客戶簡稱] 呈現
		$op['cust_select'] =  $arry2->select($cust_value,'','PHP_cust','select','',$cust_def_vue); 
		
		if($user_dept == 'HJ' || $user_dept == 'LY')
		{
			$op['fty'] = "<B>".$user_dept."</B> <input type=hidden name='PHP_fty' value='$user_dept'>";
		}else{
			$op['fty'] = $arry2->select($FACTORY,'','PHP_fty','select','');
		}
		
		
		
	page_display($op, '035', $TPL_SHIPDOC_INV_CFM);
	break;
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "cfm_packing":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "cfm_packing":
 	check_authority('033',"add");
 		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
 		
		$where_str=" ORDER BY cust_s_name"; //依cust_s_name排序
		$cust_def = $cust->get_fields('cust_init_name',$where_str);//取出客戶簡稱
		$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//取出客戶代號
		for ($i=0; $i< sizeof($cust_def); $i++)
			$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//以 [客戶代號-客戶簡稱] 呈現
		$op['cust_select'] =  $arry2->select($cust_value,'','PHP_cust','select','',$cust_def_vue); 
		
		if($user_dept == 'HJ' || $user_dept == 'LY')
		{
			$op['fty'] = "<B>".$user_dept."</B> <input type=hidden name='PHP_fty' value='$user_dept'>";
		}else{
			$op['fty'] = $arry2->select($FACTORY,'','PHP_fty','select','');
		}
		
		
		
	page_display($op, '033', $TPL_SHIPDOC_CFM);
	break;	
	
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "order_shiped_search":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "cfm_packing_search":
 	check_authority('033',"view");
		
		if(isset($SCH_inv))
		{
			$sch_parm = array();
			$sch_parm = array(
												'SCH_inv'					=>	$SCH_inv,
												'SCH_date_str'		=>	$SCH_date_str,
												'SCH_date_end'		=>	$SCH_date_end,
												'SCH_des'					=>	$SCH_des,
												'PHP_fty'					=>	$PHP_fty,
												'PHP_cust'				=>	$PHP_cust,
												'SCH_ord'					=>	$SCH_ord,
												'PHP_action'			=>	$PHP_action,
												'PHP_sr_startno'	=>	$PHP_sr_startno
												);
			}else{
				if(isset($PHP_sr_startno))$add_sch ['sr_startno'] = $PHP_sr_startno;
			}		
		$op = $ship_doc->search(2);
		for($i=0; $i<sizeof($op['ship_doc']); $i++)
		{
			$op['ship_doc'][$i]['ord_num'] = $ship_doc->get_ship_ord('ord_num', $op['ship_doc'][$i]['id']);
			$op['ship_doc'][$i]['cust_po'] = $ship_doc->get_ship_ord('cust_po', $op['ship_doc'][$i]['id']);
		}
//		$op['back_str'] = "&SCH_inv=".$SCH_inv."&SCH_date_str=".$SCH_date_str."&SCH_date_end=".$SCH_date_end."&SCH_des=".$SCH_des."&PHP_fty=".$PHP_fty."&PHP_cust=".$PHP_cust."&SCH_ord=".$SCH_ord;
		if(isset($PHP_msg))$op['msg'][]=$PHP_msg;
	page_display($op, '033', $TPL_SHIPDOC_LIST_CFM);
	break;			
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "cfm_shipdoc_search":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "cfm_shipdoc_search":
 	check_authority('035',"view");

		if(isset($SCH_inv))
		{
			$sch_parm = array();
			$sch_parm = array(
												'SCH_inv'					=>	$SCH_inv,
												'SCH_date_str'		=>	$SCH_date_str,
												'SCH_date_end'		=>	$SCH_date_end,
												'SCH_des'					=>	$SCH_des,
												'PHP_fty'					=>	$PHP_fty,
												'PHP_cust'				=>	$PHP_cust,
												'SCH_ord'					=>	$SCH_ord,
												'PHP_action'			=>	$PHP_action,
												'PHP_sr_startno'	=>	$PHP_sr_startno
												);
			}else{
				if(isset($PHP_sr_startno))$add_sch ['sr_startno'] = $PHP_sr_startno;
			}		
		$op = $ship_doc->search(6);
		for($i=0; $i<sizeof($op['ship_doc']); $i++)
		{
			$op['ship_doc'][$i]['ord_num'] = $ship_doc->get_ship_ord('ord_num', $op['ship_doc'][$i]['id']);
			$op['ship_doc'][$i]['cust_po'] = $ship_doc->get_ship_ord('cust_po', $op['ship_doc'][$i]['id']);
		}
//		$op['back_str'] = "&SCH_inv=".$SCH_inv."&SCH_date_str=".$SCH_date_str."&SCH_date_end=".$SCH_date_end."&SCH_des=".$SCH_des."&PHP_fty=".$PHP_fty."&PHP_cust=".$PHP_cust."&SCH_ord=".$SCH_ord;
		if(isset($PHP_msg))$op['msg'][]=$PHP_msg;
	page_display($op, '035', $TPL_SHIPDOC_INV_LIST_CFM);
	break;		
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "cfm_shipdoc_p_view":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "cfm_shipdoc_p_view":
 	check_authority('033',"view");

		$op = $ship_doc->get_pack($PHP_id);
/*		
		$op['shp'] = $shp_rec['shp'];
		$op['shp_det'] = $shp_rec['shp_det'];
		$op['amt'] = $shp_rec['amt'];
		$op['break'] = $shp_rec['break'];
		
		$op['back_str'] = "&SCH_inv=".$SCH_inv."&SCH_date_str=".$SCH_date_str."&SCH_date_end=".$SCH_date_end."&SCH_des=".$SCH_des."&PHP_fty=".$PHP_fty."&PHP_cust=".$PHP_cust."&PHP_sr_startno=".$PHP_sr_startno."&SCH_ord=".$SCH_ord;
*/
	if($op['shp']['inner_box'] == 1)
	{
		page_display($op, '033', $TPL_SHIPDOC_PACK_INBOX_VIEW_CFM);
	}elseif($op['shp']['bar_mk'] == 1){
		page_display($op, '033', $TPL_SHIPDOC_PACK_BAR_VIEW_CFM);
	}else{
		page_display($op, '033', $TPL_SHIPDOC_PACK_VIEW_CFM);
	}		
break;				
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "cfm_shipdoc_pack_edit":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "cfm_shipdoc_pack_edit":
 	check_authority('033',"add");
			
		$shp_rec = $ship_doc->get($PHP_id);
		$jj = sizeof($shp_rec['shp_det']) - 1;
		$op = $ship_doc->get_order('',$shp_rec['shp_det'][$jj]['ord_num']);
		if(sizeof($op['color']))
		{
			$op['color_select'] = $arry2->select($op['color'],'','PHP_color','select','');
		}
		$op['assort'] = $ship_doc->count_size_qty($op['ord']['order_num'], $op['assort']);
		
		
		
		$op['shp'] = $shp_rec['shp'];
		$op['shp_det'] = $shp_rec['shp_det'];

		$where_str = "WHERE cust='".$op['shp']['ord_cust']."'";
		$csgn = $cust->get_csge_fields('s_name',$where_str);
		if(!$csgn)$csgn=array('');
		$op['csgn_select'] = $arry2->select($csgn,$op['shp']['consignee'],'PHP_consignee','select','');


//		$op['back_str'] = $PHP_back_str;
		$op['action'] = 'shipdoc_search';
	page_display($op, '033', $TPL_SHIPDOC_PACK_EDIT_CFM);
	break;		
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_cfm_packet_edit_show":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "do_cfm_packet_edit_show":
 	check_authority('033',"add");

		if($PHP_inv)	$ship_doc->update_field_main('inv_num',$PHP_inv,$PHP_id);
		if($PHP_ship_date)$ship_doc->update_field_main('ship_date',$PHP_ship_date,$PHP_id);
		if($PHP_cust)			$ship_doc->update_field_main('cust',$PHP_cust,$PHP_id);
		if($PHP_ship_term)$ship_doc->update_field_main('ship_term',$PHP_ship_term,$PHP_id);
		if($PHP_ship_via)	$ship_doc->update_field_main('ship_via',$PHP_ship_via,$PHP_id);
		if($PHP_ship_from)$ship_doc->update_field_main('ship_from',$PHP_ship_from,$PHP_id);
		if($PHP_ship_to)	$ship_doc->update_field_main('ship_to',$PHP_ship_to,$PHP_id);
		if($PHP_des)			$ship_doc->update_field_main('des',$PHP_des,$PHP_id);
		if($PHP_pack)			$ship_doc->update_field_main('pack',$PHP_pack,$PHP_id);
		if($PHP_vendor)		$ship_doc->update_field_main('vendor',$PHP_vendor,$PHP_id);
		if($PHP_consignee)$ship_doc->update_field_main('consignee',$PHP_consignee,$PHP_id);


		
		$mesg = "Successfully Update packet list on INV# : ".$PHP_inv;
		$op = $ship_doc->get_pack($PHP_id);
/*		
		$op['shp'] = $shp_rec['shp'];
		$op['shp_det'] = $shp_rec['shp_det'];
		$op['amt'] = $shp_rec['amt'];
*/
		$op['msg'][] = $mesg;
//		$op['back_str'] = $PHP_back_str;

	if($op['shp']['inner_box'] == 1)
	{
		page_display($op, '033', $TPL_SHIPDOC_PACK_INBOX_VIEW_CFM);
	}elseif($op['shp']['bar_mk'] == 1){
		page_display($op, '033', $TPL_SHIPDOC_PACK_BAR_VIEW_CFM);
	}else{
		page_display($op, '033', $TPL_SHIPDOC_PACK_VIEW_CFM);
	}

	break;		
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "shipdoc_i_view":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "cfm_shipdoc_i_view":
 	check_authority('035',"view");

		$op = $ship_doc->get_inv($PHP_id);
/*		
		$op['shp'] = $shp_rec['shp'];
		$op['shp_det'] = $shp_rec['shp_det'];
		$op['amt'] = $shp_rec['amt'];
*/
		$op['shp']['fty_det'] = str_replace( chr(13).chr(10), "<br>",  $op['shp']['fty_det'] );
		$op['shp']['ship_det'] = str_replace( chr(13).chr(10), "<br>", $op['shp']['ship_det'] );
		$op['shp']['benf_det'] = str_replace( chr(13).chr(10), "<br>", $op['shp']['benf_det'] );
		$op['shp']['ship_mark'] = str_replace( chr(13).chr(10), "<br>", $op['shp']['ship_mark'] );
		$op['shp']['others'] = str_replace( chr(13).chr(10), "<br>", $op['shp']['others'] );
		$op['shp']['fab_des'] = str_replace( chr(13).chr(10), "<br>", $op['shp']['fab_des'] );
		$op['shp']['nty_part'] = str_replace( chr(13).chr(10), "<br>", $op['shp']['nty_part'] );
		$op['shp']['stmt'] = str_replace( chr(13).chr(10), "<br>", $op['shp']['stmt'] );
		$op['shp']['comm'] = str_replace( chr(13).chr(10), "<br>", $op['shp']['comm'] );
/*
		if(isset($PHP_back_str))
		{
			$op['back_str'] = $PHP_back_str;
		}else{
			$op['back_str'] = "&SCH_inv=".$SCH_inv."&SCH_date_str=".$SCH_date_str."&SCH_date_end=".$SCH_date_end."&SCH_des=".$SCH_des."&PHP_fty=".$PHP_fty."&PHP_cust=".$PHP_cust."&PHP_sr_startno=".$PHP_sr_startno."&SCH_ord=".$SCH_ord;
		}
*/		
	page_display($op, '035', $TPL_SHIPDOC_INV_VIEW_CFM);
	break;					
	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "cfm_shipdoc_i_edit":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "cfm_shipdoc_i_edit":
 	check_authority('035',"view");

		$shp_rec = $ship_doc->get_inv($PHP_id);
		$op['shp'] = $shp_rec['shp'];
		$op['shp_det'] = $shp_rec['shp_det'];
		$op['shp_charge'] =$shp_rec['shp_charge'];
		
		if(!$op['shp']['fty_det'])
		{
			$op['shp']['fty_det'] .= $SHIP_TO[$op['shp']['fty']]['Name'].chr(13).chr(10);
			$op['shp']['fty_det'] .= $SHIP_TO[$op['shp']['fty']]['Addr'].chr(13).chr(10);
			$op['shp']['fty_det'] .= "TEL : ".$SHIP_TO[$op['shp']['fty']]['TEL'].chr(13).chr(10);
			$op['shp']['fty_det'] .= "FAX : ".$SHIP_TO[$op['shp']['fty']]['FAX'].chr(13).chr(10);
		}
		
		if(!$op['shp']['ship_det'])
		{
			$op['shp']['ship_det'] .= $SHIP_TO[$op['shp']['fty']]['Name'].chr(13).chr(10);
			$op['shp']['ship_det'] .= $SHIP_TO[$op['shp']['fty']]['Addr'].chr(13).chr(10);
			$op['shp']['ship_det'] .= "TEL : ".$SHIP_TO[$op['shp']['fty']]['TEL'].chr(13).chr(10);
			$op['shp']['ship_det'] .= "FAX : ".$SHIP_TO[$op['shp']['fty']]['FAX'].chr(13).chr(10);
		}		
//		$op['back_str'] = $PHP_back_str;

		$where_str = "WHERE cust='".$op['shp']['ord_cust']."'";
		$csgn = $cust->get_csge_fields('s_name',$where_str);
		if(!$csgn)$csgn=array('');
		$op['csgn_select'] = $arry2->select($csgn,$op['shp']['consignee'],'PHP_consignee','select','');

		$op['shp']['mid_select'] = $arry2->select($MID ,$op['shp']['mid_no'],"PHP_mid_no","select"); 
		$op['shp']['country_select'] = $arry2->select($SHIP_COUNTRY ,$op['shp']['country_org'],"PHP_country_org","select");  


		foreach($CONTAINER as $key => $value)
		{
			$conter[] = $key;
			$conter_name[] = $CONTAINER[$key]['name'];
		}
		$op['shp']['conter_select'] = $arry2->select($conter_name ,$op['shp']['conter'],"PHP_conter","select",'',$conter);  
		$op['shp']['constor_select'] = $arry2->select($conter_name ,$op['shp']['constor'],"PHP_constor","select",'',$conter);  

		for($i=0; $i<sizeof($SHIP_PO_TYPE); $i++) $po_key[] = $i+1;
		for($i=0; $i<sizeof($SHIP_FACILLITY); $i++) $fact_key[] = $i+1;
		for($i=0; $i<sizeof($SHIP_MODE); $i++) $mode_key[] = $i+1;
		for($i=0; $i<sizeof($SHIP_METHOD); $i++) $method_key[] = $i+1;


		if(isset($PHP_msg)) $op['msg'][] = $PHP_msg;
	page_display($op, '035', $TPL_SHIPDOC_INV_EDIT_CFM);
	break;	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_cfm_shipdoc_i_edit":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "do_cfm_shipdoc_i_edit":
 	check_authority('035',"view");


 		if(!$ship_doc->check_inv($PHP_inv_num,$PHP_id))
 		{
 			$message = "invoice number is exist, please check again";
 			$rstr = "shipdoc.php?PHP_action=cfm_shipdoc_i_edit&PHP_id=".$PHP_id."&PHP_msg=".$message;
 			redirect_page($rstr);
 		}


 		$PHP_ship_addr = $PHP_ship_addr1."|".$PHP_ship_addr2;
 		$PHP_bill_addr = $PHP_bill_addr1."|".$PHP_bill_addr2;
 		if($PHP_consignee2)$PHP_consignee = $PHP_consignee2;
 		$parm = array(
 										'ship_term'		=>	$PHP_ship_term,
 										'ship_via'		=>	$PHP_ship_via,
 										'ship_from'		=>	$PHP_ship_from,
 										'ship_to'			=>	$PHP_ship_to,
 										'payment'			=>	$PHP_payment,
 										'mark_no'			=>	$PHP_mark_no,
 										'des'					=>	$PHP_des,
 										'fv'					=>	$PHP_fv,
 										'fv_etd'			=>	$PHP_fv_etd,
 										'fv_eta'			=>	$PHP_fv_eta,
 										'mv'					=>	$PHP_mv,
 										'mv_etd'			=>	$PHP_mv_etd,
 										'mv_eta'			=>	$PHP_mv_eta,
 										'lc_no'				=>	$PHP_lc_no,
 										'lc_bank'			=>	$PHP_lc_bank,
 										'consignee'		=>	$PHP_consignee,
 										'ship_addr'		=>	$PHP_ship_addr,
 										'bill_addr'		=>	$PHP_bill_addr,
 										'fty_det'			=>	$PHP_fty_det,
 										'ship_det'		=>	$PHP_ship_det,
 										'benf_det'		=>	$PHP_benf_det,
 										'inv_num'			=>	$PHP_inv_num,
 										'ship_date'		=>	$PHP_ship_date,
 										'mid_no'			=>	$PHP_mid_no,
 										'country_org'	=>	$PHP_country_org,
 										'ship_mark'		=>	$PHP_ship_mark,
 										'others'			=>	$PHP_others,
 										'ship_by'			=>	$PHP_ship_by,
 										'carrier'			=>	$PHP_carrier,
 										'fab_des'			=>	$PHP_fab_des,
 										'dis_port'		=>	$PHP_dis_port,
 										'lc_date'			=>	$PHP_lc_date,
 										'cbm'					=>	$PHP_cbm,

 										'nty_part'			=>	$PHP_nty_part,
 										'stmt'					=>	$PHP_stmt,
 										'comm'					=>	$PHP_comm,
 										'conter'				=>	$PHP_conter,
 										'constor'				=>	$PHP_constor,
 										'conter_num'		=>	$PHP_conter_num,
 										'freight_date'	=>	$PHP_freight_date,
 										'lod_date'			=>	$PHP_lod_date,
 										'dischg_date'		=>	$PHP_dischg_date,
 										'voyage'				=>	$PHP_voyage,
 										'freight_rcp'		=>	$PHP_freight_rcp,
 										'fty_ship_date'	=>	$PHP_fty_ship_date,
 										'facility_code'	=>	$PHP_facility_code,
 										'facility_name'	=>	$PHP_facility_name,
 										'hbl'						=>	$PHP_hbl,
 										'po_type'				=>	$PHP_po_type,
 										'facility'			=>	$PHP_facility,
 										'mode'					=>	$PHP_mode,	
 										'method'				=>	$PHP_method,
 										
 										'id'					=>	$PHP_id
 									);
 									
 		$f1 = $ship_doc->edit_inv($parm);
 		
 		
 //原packing 修改項目		
		if($PHP_pack)			$ship_doc->update_field_main('pack',$PHP_pack,$PHP_id);
		$PHP_vendor = $PHP_vendor."|".$PHP_vendor2;
		if($PHP_vendor)		$ship_doc->update_field_main('vendor',$PHP_vendor,$PHP_id);

		if($PHP_fnl_name)$ship_doc->update_field_main('fnl_name',$PHP_fnl_name,$PHP_id);
		if($PHP_fnl_addr)$ship_doc->update_field_main('fnl_addr',$PHP_fnl_addr,$PHP_id);
		if($PHP_fnl_style)$ship_doc->update_field_main('fnl_style',$PHP_fnl_style,$PHP_id);
		if($PHP_fnl_po)$ship_doc->update_field_main('fnl_po',$PHP_fnl_po,$PHP_id);
		if($PHP_del_due)$ship_doc->update_field_main('del_due',$PHP_del_due,$PHP_id);
		if($PHP_fnl_des)$ship_doc->update_field_main('fnl_des',$PHP_fnl_des,$PHP_id);
		if($PHP_fnl_dept)$ship_doc->update_field_main('fnl_dept',$PHP_fnl_dept,$PHP_id);
		if($PHP_dept_des)$ship_doc->update_field_main('dept_des',$PHP_dept_des,$PHP_id);
		if($PHP_ctn_num)$ship_doc->update_field_main('ctn_num',$PHP_ctn_num,$PHP_id);
		if($PHP_seal)$ship_doc->update_field_main('seal',$PHP_seal,$PHP_id);
		$ship_doc->update_field_main('que1',$PHP_que1,$PHP_id);
		$ship_doc->update_field_main('que2',$PHP_que2,$PHP_id);
		$ship_doc->update_field_main('que3',$PHP_que3,$PHP_id);
		$ship_doc->update_field_main('que4',$PHP_que4,$PHP_id);
		$ship_doc->update_field_main('que5',$PHP_que5,$PHP_id);
 		
 		
 		
 		
		foreach($PHP_content as $key => $value)
		{
			$value = str_replace("'","\'",$value);
			$PHP_ord_des[$key] = str_replace("'","\'",$PHP_ord_des[$key]);
			$f1=$order->update_field_num('content', $value,$key); 
//			$f1=$order->update_field_num('cust_po', $PHP_cust_po[$key],$key);
			$f1=$order->update_field_num('des', $PHP_ord_des[$key],$key); 
			$f1=$order->update_field_num('ship_quota', $PHP_quota[$key],$key);  
			$f1=$order->update_field_num('quota_unit', $PHP_quota_unit[$key],$key);  
			$f1=$order->update_field_num('style_num', $PHP_style_num[$key],$key); 
			$f1=$order->update_field_num('ship_fob', $PHP_uprice[$key],$key); 
			
			if($PHP_uprice[$key] <> $PHP_old_fob[$key])
			{
					// ---- 寫入 order log檔  ----
					$argv2['order_num']	=	$key;
					$argv2['user']			= $GLOBALS['SCACHE']['ADMIN']['login_id'];
					$argv2['des']				= "Shipping FOB change : [ US$".$PHP_old_fob[$key]." => US$".$PHP_uprice[$key]."] ON ".$TODAY;

					$order_log->add($argv2);
			}

		}
		
		foreach($PHP_hts_cat as $key => $value)
		{
			$f1=$ship_doc->update_field_ord('hts_cat', $PHP_hts_cat[$key],$key); 
			$f1=$ship_doc->update_field_ord('mat_ft',  $PHP_mat_ft[$key],$key); 
			$f1=$ship_doc->update_field_ord('ship_ft', $PHP_ship_ft[$key],$key); 
			$f1=$ship_doc->update_field_ord('belt_co', $PHP_belt_co[$key],$key); 
			$f1=$ship_doc->update_field_ord('hanger',  $PHP_hanger[$key],$key); 
			$f1=$ship_doc->update_field_ord('belt',    $PHP_belt[$key],$key); 			
			$f1=$ship_doc->update_field_ord('compt',    $PHP_compt[$key],$key); 			
			$f1=$ship_doc->update_field_ord('case_num',    $PHP_case_num[$key],$key); 			

		}		
		
		$op = $ship_doc->get_inv($PHP_id);
/*		
		$op['shp'] = $shp_rec['shp'];
		$op['shp_det'] = $shp_rec['shp_det'];
		$op['amt'] = $shp_rec['amt'];
*/		
		$op['shp']['fty_det'] = str_replace( chr(13).chr(10), "<br>",  $op['shp']['fty_det'] );
		$op['shp']['ship_det'] = str_replace( chr(13).chr(10), "<br>", $op['shp']['ship_det'] );
		$op['shp']['benf_det'] = str_replace( chr(13).chr(10), "<br>", $op['shp']['benf_det'] );
		$op['shp']['ship_mark'] = str_replace( chr(13).chr(10), "<br>", $op['shp']['ship_mark'] );
		$op['shp']['others'] = str_replace( chr(13).chr(10), "<br>", $op['shp']['others'] );
		$op['shp']['fab_des'] = str_replace( chr(13).chr(10), "<br>", $op['shp']['fab_des'] );
		$op['shp']['nty_part'] = str_replace( chr(13).chr(10), "<br>", $op['shp']['nty_part'] );
		$op['shp']['stmt'] = str_replace( chr(13).chr(10), "<br>", $op['shp']['stmt'] );
		$op['shp']['comm'] = str_replace( chr(13).chr(10), "<br>", $op['shp']['comm'] );

		$mesg = "SUCCESS EDIT INVOICE ON  [".$op['shp']['inv_num']."]";
//		$op['back_str'] = $PHP_back_str;
		$op['msg'][] = $mesg;
		
	page_display($op, '035', $TPL_SHIPDOC_INV_VIEW_CFM);
	break;						
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "order_shiped_search":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "do_shipdoc_cfm":
 	check_authority('035',"add");

		$ship_doc->update_field_main('status',8,$PHP_id);
		$ship_doc->update_field_main('inv_cfm_user',$GLOBALS['SCACHE']['ADMIN']['login_id'],$PHP_id);
		$ship_doc->update_field_main('inv_cfm_date',$TODAY,$PHP_id);
		$ship_doc->update_order_fob($PHP_id);
		$mesg = "SUCCESS submit shipping document on inv : [".$PHP_inv_num."]";
		$log->log_add(0,"411S",$mesg);
		
//		$shp_fld = $ship_doc->get_apvd($PHP_id);
//		for($i = 0; $i<sizeof($shp_fld); $i++)
//		{
//			$parm = array(	
//											"p_id"			=>	$shp_fld[$i]['p_id'],
//											"ord_num"		=>  $shp_fld[$i]['ord_num'],
//											"qty_shp"		=>  $shp_fld[$i]['qty_shp'],
//											"shp_date"	=>  $shp_fld[$i]['ship_date'],
//											"remain"		=>	$shp_fld[$i]['rem_qty'],
//				);			
//				$F = $order->add_shipping($parm,1);
//		}		
		
		$redirect_str ="shipdoc.php?PHP_action=cfm_shipdoc_search&PHP_msg=".$mesg;
		redirect_page($redirect_str);	
		
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "order_shiped_search":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "do_packing_cfm":
 	check_authority('033',"add");
		$ship_doc->update_field_main('status',4,$PHP_id);
		$ship_doc->update_field_main('cfm_user',$GLOBALS['SCACHE']['ADMIN']['login_id'],$PHP_id);
		$ship_doc->update_field_main('cfm_date',$TODAY,$PHP_id);
		
		$mesg = "SUCCESS submit shipping document -- packing on inv : [".$PHP_inv_num."]";
		$log->log_add(0,"48S",$mesg);
		
		$redirect_str ="shipdoc.php?PHP_action=cfm_packing_search&PHP_msg=".$mesg;

		redirect_page($redirect_str);	
		
		
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "reject_shipdoc_cfm": 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "reject_shipdoc_cfm":
 		check_authority('035',"add");
		
		$ship_doc->update_field_main('status',5,$PHP_id);
		
		$parm['ship_id'] = $PHP_id;
		$parm['des'] = $PHP_detail;
		$ship_doc->add_log($parm);
		
		$mesg = "SUCCESS Reject shipping document on inv : [".$PHP_inv_num."]";
		$log->log_add(0,"411RT",$mesg);
		
		$redirect_str ="shipdoc.php?PHP_action=cfm_shipdoc_search&PHP_msg=".$mesg;

		redirect_page($redirect_str);	
			
		
		
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "reject_packing_cfm": 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "reject_packing_cfm":
 		check_authority('032',"add");
/*
		for($i = 0; $i<sizeof($shp_fld); $i++)
		{
			$parm = array(	"pd_id"			=>  $shp_fld[$i]['pdt_id'],
											"qty_shp"		=>  $shp_fld[$i]['qty_shp']*-1,
											"shp_date"	=>	$shp_fld[$i]['ship_date'],
											'id'				=>	$shp_fld[$i]['ord_id'],
											'factory'		=>	$shp_fld[$i]['factory'],
				);			
			if($shp_fld[$i]['finish'] && $shp_fld[$i]['finish']<>'0000-00-00' ){		// 當訂單己Finish狀態才會變成Shipped否則維持在production
				$order->update_field('status', 10, $shp_fld[$i]['ord_id']);
			}
			//寫入shipping
			$F = $shipping->del_f_shipdoc($shp_fld[$i]['ord_num'],$PHP_submit_date) ;

			// *********  寫入 pdtion->qty_shp, shp_date    *********************
			$F = $order->add_shipping($parm);
			
			//取得cpapcity for shipping
			$sub_date = explode('-',$PHP_submit_date);
			$T=$capaci->get($shp_fld[$i]['factory'], $sub_date[0],'shipping');
			//加入capacity
			$F = $capaci->update_su($shp_fld[$i]['factory'], $sub_date[0], $sub_date[1], 'shipping', ($shp_fld[$i]['qty_shp']*-1));
			$F = $capaci->update_su($shp_fld[$i]['factory'], $sub_date[0], $sub_date[1], "shp_fob", ($shp_fld[$i]['fob']*-1));
		}
*/


		
		$ship_doc->update_field_main('status',1,$PHP_id);
		
		$parm['ship_id'] = $PHP_id;
		$parm['des'] = $PHP_detail;
		$ship_doc->add_log($parm);


//		$shp_fld = $ship_doc->get_apvd($PHP_id);
//		for($i = 0; $i<sizeof($shp_fld); $i++)
//		{
//			$parm = array(	
//											"p_id"			=>	$shp_fld[$i]['p_id'],
//											"ord_num"		=>  $shp_fld[$i]['ord_num'],
//											"qty_shp"		=>  $shp_fld[$i]['qty_shp'],
//											"shp_date"	=>  $shp_fld[$i]['ship_date'],
//											"remain"		=>	$shp_fld[$i]['rem_qty'],
//				);			
//				$F = $order->add_shipping($parm,1);
//		}

		
		$mesg = "SUCCESS Reject shipping document -- packing on inv : [".$PHP_inv_num."]";
		$log->log_add(0,"48RT",$mesg);
		
		$redirect_str ="shipdoc.php?PHP_action=cfm_packing_search&PHP_msg=".$mesg;

		redirect_page($redirect_str);	
		
		
		
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "order_shiped_search":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "apv_shipdoc_search":
 	check_authority('032',"view");
		
		$op = $ship_doc->search(3);
		if(isset($PHP_msg))$op['msg'][]=$PHP_msg;
	page_display($op, '032', $TPL_SHIPDOC_LIST_APV);
	break;		
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_packet_add_show":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "apv_shipdoc_p_view":
 	check_authority('033',"view");

		$shp_rec = $ship_doc->get_pack($PHP_id);
		$op['shp'] = $shp_rec['shp'];
		$op['shp_det'] = $shp_rec['shp_det'];
		$op['amt'] = $shp_rec['amt'];
		$op['back_str'] = "&PHP_sr_startno=".$PHP_sr_startno;
		
	page_display($op, '033', $TPL_SHIPDOC_PACK_VIEW_APV);
	break;				
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "shipdoc_i_view":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "apv_shipdoc_i_view":
 	check_authority('032',"view");

		$op = $ship_doc->get_inv($PHP_id);
/*		
		$op['shp'] = $shp_rec['shp'];
		$op['shp_det'] = $shp_rec['shp_det'];
		$op['amt'] = $shp_rec['amt'];
*/		
		$op['shp']['fty_det'] = str_replace( chr(13).chr(10), "<br>",  $op['shp']['fty_det'] );
		$op['shp']['ship_det'] = str_replace( chr(13).chr(10), "<br>", $op['shp']['ship_det'] );
		$op['shp']['benf_det'] = str_replace( chr(13).chr(10), "<br>", $op['shp']['benf_det'] );
		$op['shp']['fab_des'] = str_replace( chr(13).chr(10), "<br>", $op['shp']['fab_des'] );
		$op['shp']['nty_part'] = str_replace( chr(13).chr(10), "<br>", $op['shp']['nty_part'] );
		$op['shp']['stmt'] = str_replace( chr(13).chr(10), "<br>", $op['shp']['stmt'] );
		$op['shp']['comm'] = str_replace( chr(13).chr(10), "<br>", $op['shp']['comm'] );

		$op['back_str'] = "&PHP_sr_startno=".$PHP_sr_startno;

		
	page_display($op, '032', $TPL_SHIPDOC_INV_VIEW_APV);
	break;						
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "do_shipdoc_apv": 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
/*
    case "do_shipdoc_apv":
 		check_authority('032',"add");
		$shp_fld = $ship_doc->get_apvd($PHP_id);
		
		$ship_doc->update_field_main('status',6,$PHP_id);
		$ship_doc->update_field_main('apv_user',$GLOBALS['SCACHE']['ADMIN']['login_id'],$PHP_id);
		$ship_doc->update_field_main('apv_date',$TODAY,$PHP_id);


		
		for($i = 0; $i<sizeof($shp_fld); $i++)
		{
			$parm = array(	"pd_id"			=>  $shp_fld[$i]['pdt_id'],
											"ord_num"		=>  $shp_fld[$i]['ord_num'],
											"qty_shp"		=>  $shp_fld[$i]['qty_shp'],
											"shp_date"	=>  $TODAY,
											"remain"		=>	$shp_fld[$i]['rem_qty'],
											"acm"				=>	$shp_fld[$i]['acm'],
											"cm"				=>	$shp_fld[$i]['cm'],
											'id'				=>	$shp_fld[$i]['ord_id'],
											'factory'		=>	$shp_fld[$i]['factory'],
											'su_shp'		=>	$shp_fld[$i]['su_shp'],
											'su_shp'		=>	$shp_fld[$i]['su_shp'],
				);			
			if($shp_fld['finish'] && $shp_fld['finish']<>'0000-00-00' ){		// 當訂單己Finish狀態才會變成Shipped否則維持在production
				$order->update_field('status', 12, $parm['id']);
			}
			//寫入shipping
			$F = $shipping->add($parm);

			// *********  寫入 pdtion->qty_shp, shp_date    *********************
			$F = $order->add_shipping($parm);
			
			//取得cpapcity for shipping
			$T=$capaci->get($shp_fld[$i]['factory'], $THIS_YEAR,'shipping');
			//加入capacity
			$ky_mon = substr($THIS_MON,4,2);
			$F = $capaci->update_su($shp_fld[$i]['factory'], $THIS_YEAR, $ky_mon, 'shipping', $shp_fld[$i]['qty_shp']);
			$F = $capaci->update_su($shp_fld[$i]['factory'], $THIS_YEAR, $ky_mon, "shp_fob", $shp_fld[$i]['fob']);
		}
		
		$mesg = "SUCCESS Approval shipping document on inv : [".$PHP_inv_num."]";
		$log->log_add(0,"48AV",$mesg);
		
		$redirect_str ="shipdoc.php?PHP_action=apv_shipdoc_search".$PHP_back_str."&PHP_msg=".$mesg;

		redirect_page($redirect_str);	
			
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 case "reject_shipdoc_apv": 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "reject_shipdoc_apv":
 		check_authority('032',"add");
		$shp_fld = $ship_doc->get_apvd($PHP_id);
		
		$ship_doc->update_field_main('status',5,$PHP_id);
		
		$parm['ship_id'] = $PHP_id;
		$parm['des'] = $PHP_detail;
		$ship_doc->add_log($parm);
		
		$mesg = "SUCCESS Reject shipping document on inv : [".$PHP_inv_num."]";
		$log->log_add(0,"48RT",$mesg);
		
		$redirect_str ="shipdoc.php?PHP_action=apv_shipdoc_search".$PHP_back_str."&PHP_msg=".$mesg;

		redirect_page($redirect_str);	
*/	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "revise_ship":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "revise_ship":
 	check_authority('034',"edit");

		$shp_fld = $ship_doc->get_apvd($PHP_id);

	

		$ship_doc->update_field_main('inv_ver',($PHP_ver+1),$PHP_id);
		$ship_doc->update_field_main('inv_rev_date',$TODAY,$PHP_id);
		$ship_doc->update_field_main('inv_rev_user',$GLOBALS['SCACHE']['ADMIN']['login_id'],$PHP_id);
		$ship_doc->update_field_main('status',4,$PHP_id);

		$mesg = "SUCCESS Revise shipping document on inv : [".$PHP_inv_num."]";
		$log->log_add(0,"410R",$mesg);
		
		$back_str = "&SCH_inv=".$SCH_inv."&SCH_date_str=".$SCH_date_str."&SCH_date_end=".$SCH_date_end."&SCH_des=".$SCH_des."&PHP_fty=".$PHP_fty."&PHP_cust=".$PHP_cust."&PHP_sr_startno=".$PHP_sr_startno."&SCH_ord=".$SCH_ord;

		$redirect_str ="shipdoc.php?PHP_action=shipdoc_search".$back_str."&PHP_msg=".$mesg;
		redirect_page($redirect_str);
		
	
	break;			
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "revise_packing":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "revise_packing":
 	check_authority('032',"edit");

/*
		for($i = 0; $i<sizeof($shp_fld); $i++)
		{
			$parm = array(	"pd_id"			=>  $shp_fld[$i]['pdt_id'],
											"qty_shp"		=>  $shp_fld[$i]['qty_shp']*-1,
											"shp_date"	=>	$shp_fld[$i]['ship_date'],
											'id'				=>	$shp_fld[$i]['ord_id'],
											'factory'		=>	$shp_fld[$i]['factory'],
				);			
			if($shp_fld[$i]['finish'] && $shp_fld[$i]['finish']<>'0000-00-00' ){		// 當訂單己Finish狀態才會變成Shipped否則維持在production
				$order->update_field('status', 10, $shp_fld[$i]['ord_id']);
			}
			//寫入shipping
			$F = $shipping->del_f_shipdoc($shp_fld[$i]['ord_num'],$PHP_submit_date) ;

			// *********  寫入 pdtion->qty_shp, shp_date    *********************
			$F = $order->add_shipping($parm);
			
			//取得cpapcity for shipping
			$sub_date = explode('-',$PHP_submit_date);
			$T=$capaci->get($shp_fld[$i]['factory'], $sub_date[0],'shipping');
			//加入capacity
			$F = $capaci->update_su($shp_fld[$i]['factory'], $sub_date[0], $sub_date[1], 'shipping', ($shp_fld[$i]['qty_shp']*-1));
			$F = $capaci->update_su($shp_fld[$i]['factory'], $sub_date[0], $sub_date[1], "shp_fob", ($shp_fld[$i]['fob']*-1));
		}
*/

		$ship_doc->update_field_main('ver',($PHP_ver+1),$PHP_id);
		$ship_doc->update_field_main('rev_date',$TODAY,$PHP_id);
		$ship_doc->update_field_main('rev_user',$GLOBALS['SCACHE']['ADMIN']['login_id'],$PHP_id);
		$ship_doc->update_field_main('status',0,$PHP_id);

//		$shp_fld = $ship_doc->get_apvd($PHP_id);
//		for($i = 0; $i<sizeof($shp_fld); $i++)
//		{
//			$parm = array(	
//											"p_id"			=>	$shp_fld[$i]['p_id'],
//											"ord_num"		=>  $shp_fld[$i]['ord_num'],
//											"qty_shp"		=>  $shp_fld[$i]['qty_shp'],
//											"shp_date"	=>  $shp_fld[$i]['ship_date'],
//											"remain"		=>	$shp_fld[$i]['rem_qty'],
//				);			
//				$F = $order->add_shipping($parm,1);
//		}

		$mesg = "SUCCESS Revise shipping document -- packing on inv : [".$PHP_inv_num."]";
		$log->log_add(0,"48R",$mesg);
		
		$back_str = "&SCH_inv=".$SCH_inv."&SCH_date_str=".$SCH_date_str."&SCH_date_end=".$SCH_date_end."&SCH_des=".$SCH_des."&PHP_fty=".$PHP_fty."&PHP_cust=".$PHP_cust."&PHP_sr_startno=".$PHP_sr_startno."&SCH_ord=".$SCH_ord;

		$redirect_str ="shipdoc.php?PHP_action=packing_search".$back_str."&PHP_msg=".$mesg;
		redirect_page($redirect_str);
		
	
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
																		
		if ($_FILES['PHP_pttn']['size'] < 2072864 && $_FILES['PHP_pttn']['size'] > 0)
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
			
			$A = $fils->get_name_id('shipdoc_file');
			$pttn_name = $PHP_num."_".$A.".".$ext;  // 組合檔名
			$parm['file_name'] = $pttn_name;
			
			$str_long=strlen($pttn_name);
			$upload = new Upload;
			
			$upload->setMaxSize(2072864);
			$upload->uploadFile(dirname($PHP_SELF).'/shipdoc_file/', 'other', 16, $pttn_name );
			$upload->setMaxSize(2072864);
			if (!$upload){
				$op['msg'][] = $upload;
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			
			if (!$A = $fils->upload_shipdoc_file($parm)){
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



		$redir_str = 'shipdoc.php?PHP_action='.$PHP_html.'&PHP_id='.$PHP_id.$PHP_back_str."&PHP_msg=".$message;
		redirect_page($redir_str);

/*			
		$op = $ship_doc->get_pack($PHP_id);

		$op['back_str'] = $PHP_back_str;		
		$op['msg'][] = $message;
		
		if($op['shp']['inner_box'] == 1)
		{
			page_display($op, '032', $TPL_SHIPDOC_PACK_INBOX_VIEW);
		}else{
			page_display($op, '032', $TPL_SHIPDOC_PACK_VIEW);
		}		
	
*/	
		break;	
	
			
	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "ship_pack_print":			
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "ship_pack_print":   //  .......
	check_authority('032',"view");	
		$shp_rec = $ship_doc->get_pack($PHP_id);

$j = $k = -1;
$x = $y =0;
$content = array();

for($i=0; $i<sizeof($shp_rec['shp_det']); $i++)
{
	$con_mk = 0;
	$shp_rec['shp_det'][$i]['ord_row'] = 0;
	$shp_rec['shp_det'][$i]['color_row'] =	0;
//取得不同的訂單content	於一陣列中
	for($ct=0; $ct<sizeof($content); $ct++)
	{
		if($content[$ct] == $shp_rec['shp_det'][$i]['content']) 
		{
			$con_mk = 1;
			break;
		}		
	}
	
	if($con_mk == 0)$content[] = $shp_rec['shp_det'][$i]['content'];

	if (isset($shp_rec['shp_det'][$i]['tb_mk']) && $shp_rec['shp_det'][$i]['tb_mk'] == 1)
	{
		$style_num = $color = '';		
	}
	
	if($style_num <> $shp_rec['shp_det'][$i]['style_num']) //整理sytle_num的顯示
	{
		$style_num = $shp_rec['shp_det'][$i]['style_num'];
		if($j > -1)
		{
			$shp_rec['shp_det'][$j]['ord_row'] = $x;
			if(strlen($shp_rec['shp_det'][$j]['style_num']) > 18 && $x > 1)
			{
				$tmp = $shp_rec['shp_det'][$j]['style_num'];
				$shp_rec['shp_det'][$j]['style_num'] = substr($tmp,0,18);
				$shp_rec['shp_det'][($j+1)]['style_num'] = substr($tmp,18);
			}
		}
		$x = 0;
		$j = $i;
		$color = '';
	}else{
		$shp_rec['shp_det'][$i]['style_num'] = '';
	}	
	
	if($color <> $shp_rec['shp_det'][$i]['color']) //整理color的顯示
	{
		$color = $shp_rec['shp_det'][$i]['color'];
		if($k > -1)
		{
			$shp_rec['shp_det'][$k]['color_row'] = $y;
			if(strlen($shp_rec['shp_det'][$k]['color']) > 18 && $y > 1)
			{
				$tmp = $shp_rec['shp_det'][$k]['color'];
				$shp_rec['shp_det'][$k]['color'] = substr($tmp,0,18);
				$shp_rec['shp_det'][($k+1)]['color'] = substr($tmp,18);
			}
		}
		$y = 0;
		$k = $i;
	}else{
		$shp_rec['shp_det'][$i]['color'] = '';	
	}	
	$x++;
	$y++;	

}
if($j > -1)
{

	$shp_rec['shp_det'][$j]['ord_row'] = $x;
	if(strlen($shp_rec['shp_det'][$j]['style_num']) > 18 && $x > 1)
	{
		$tmp = $shp_rec['shp_det'][$j]['style_num'];
		$shp_rec['shp_det'][$j]['style_num'] = substr($tmp,0,18);
		$shp_rec['shp_det'][($j+1)]['style_num'] = substr($tmp,18);

		
	}
}
if($k > -1)
{
	$shp_rec['shp_det'][$k]['color_row'] = $x;
	if(strlen($shp_rec['shp_det'][$k]['color']) > 18 && $y > 1)
	{
		$tmp = $shp_rec['shp_det'][$k]['color'];
		$shp_rec['shp_det'][$k]['color'] = substr($tmp,0,18);
		$shp_rec['shp_det'][($k+1)]['color'] = substr($tmp,18);
	}
}

//將不同的content轉成字串
$shp_rec['shp']['ord_content'] ='';
for($i=0; $i< sizeof($content); $i++)
{
	$shp_rec['shp']['ord_content'].=$content[$i]."/";
}
$shp_rec['shp']['ord_content'] = substr($shp_rec['shp']['ord_content'],0,-1);

//---------------------------------- 資料庫作業結束 ------------------------------

include_once($config['root_dir']."/lib/class.pdf_shp_pack.php");
$pg_mk = 0;
session_register	('main_rec');
$main_rec = array (	'fty'			=>	$shp_rec['shp']['fty'],
										'inv_num'	=>	$shp_rec['shp']['inv_num'],
										'des'			=>	$shp_rec['shp']['des'],
										'date'		=>	$shp_rec['shp']['fv_etd'],
										'style'		=>	$shp_rec['shp']['ord_style'],
										'content'	=>	$shp_rec['shp']['ord_content']
										);

$print_title="DETAILED PACKING LIST";

$print_title1 = "our pdt. code : ".$shp_rec['shp']['ord_num'];
$print_title2 = "VER.".($shp_rec['shp']['ver']+1);
$creator = $shp_rec['shp']['submit_user'];
$mark = $shp_rec['shp']['inv_num'];
$fty = $shp_rec['shp']['fty'];


$pdf = new PDF_shp_pack('L','mm','A4');
//$pdf->SetAutoPageBreak('on',10);
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();

$gd_mk = 0;

$ctn_num = $style = $color = '';

for($i=0; $i<sizeof($shp_rec['shp_det']); $i++) //列印packing list
{

	if($pg_mk > 29) //換頁
	{
		$pdf->AddPage();
		if(!(isset($shp_rec['shp_det'][$i]['tb_mk']) && $shp_rec['shp_det'][$i]['tb_mk'] == 1))
		{
			$pdf->ship_title($shp_rec['shp_det'][$i]['size']);
			$pg_mk = 3;
		}
		$pg_mk = 0;
	}

	if($i == 0) //初始化總計
	{

		$gd_tal = array();
		for($j=0; $j<sizeof($shp_rec['shp_det'][$i]['size_qty']); $j++) $gd_tal['size'][] = 0;
		$gd_tal['gw'] =$gd_tal['nw'] =$gd_tal['nnw'] = $gd_tal['qty'] = $gd_tal['ctn'] = 0;
	}		
	
	if($ctn_num != '' && $ctn_num <> $shp_rec['shp_det'][$i]['cnt'] && $style <> $shp_rec['shp_det'][$i]['style_num'] && $shp_rec['shp_det'][$i]['style_num'] <> ''  && $color <> $shp_rec['shp_det'][$i]['color'] && $shp_rec['shp_det'][$i]['color'] <> '')
	{
		$style = $shp_rec['shp_det'][$i]['style_num'];
		$color = $shp_rec['shp_det'][$i]['color'];
		$ctn_num = $shp_rec['shp_det'][$i]['cnt'];
		
		if($i > 0)
		{
			$pdf->sub_total($sub_tal); //小計列印
			$pg_mk++;
		}		
		$sub_tal = array(); //小計初始化
		for($j=0; $j<sizeof($shp_rec['shp_det'][$i]['size_qty']); $j++) $sub_tal['size'][] = 0;
		$sub_tal['gw'] =$sub_tal['nw'] =$sub_tal['nnw'] = $sub_tal['qty'] = $sub_tal['ctn'] = 0;
	}

	if (isset($shp_rec['shp_det'][$i]['tb_mk']) && $shp_rec['shp_det'][$i]['tb_mk'] == 1)
	{
		$pdf->ship_title($shp_rec['shp_det'][$i]['size']); //packing list表頭
		$pg_mk+=3;
//		if($i > 0)$pdf->sub_total($sub_tal);
		$sub_tal = array(); //小計初始化
		for($j=0; $j<sizeof($shp_rec['shp_det'][$i]['size_qty']); $j++) $sub_tal['size'][] = 0;
		$sub_tal['gw'] =$sub_tal['nw'] =$sub_tal['nnw'] = $sub_tal['qty'] = $sub_tal['ctn'] = 0;
		$ctn_num = $style = $color = '';
		$gd_mk++;
	}
	

	if(strlen($shp_rec['shp_det'][$i]['color']) > 18 || strlen($shp_rec['shp_det'][$i]['style_num']) > 18)
	{
		$pdf->ship_txt_mix($shp_rec['shp_det'][$i],$shp_rec['shp']['inner_box']); //color or style > 18 works, print 二行
		$pg_mk+=2;
	}else{

		$pdf->ship_txt($shp_rec['shp_det'][$i],$shp_rec['shp']['inner_box']); //print packing list
		$pg_mk++;
	}





	//小計
	$ctn = explode('-',$shp_rec['shp_det'][$i]['cnt']);
	for($j=0; $j<sizeof($shp_rec['shp_det'][$i]['size_qty']); $j++) 
	{
		if($shp_rec['shp']['inner_box'] == 0)$shp_rec['shp_det'][$i]['size_box'][$j] = 1;
		$sub_tal['size'][$j] += $shp_rec['shp_det'][$i]['size_qty'][$j] * $shp_rec['shp_det'][$i]['size_box'][$j] * ($ctn[1] - $ctn[0] + 1);		
	}
	
	$sub_tal['ctn'] +=  $shp_rec['shp_det'][$i]['cnum'];
	$sub_tal['qty'] += $shp_rec['shp_det'][$i]['qty'] * ($ctn[1] - $ctn[0] + 1);
	$sub_tal['nnw'] += $shp_rec['shp_det'][$i]['nnw'] * $shp_rec['shp_det'][$i]['cnum'];
	$sub_tal['nw'] += $shp_rec['shp_det'][$i]['nw'] * $shp_rec['shp_det'][$i]['cnum'];
	$sub_tal['gw'] += $shp_rec['shp_det'][$i]['gw'] * $shp_rec['shp_det'][$i]['cnum'];


	//總計
	$ctn = explode('-',$shp_rec['shp_det'][$i]['cnt']);
	if($gd_mk < 2) for($j=0; $j<sizeof($shp_rec['shp_det'][$i]['size_qty']); $j++) $gd_tal['size'][$j] += $shp_rec['shp_det'][$i]['size_qty'][$j] * ($ctn[1] - $ctn[0] + 1);
	$gd_tal['ctn'] += $shp_rec['shp_det'][$i]['cnum'];
	$gd_tal['qty'] += $shp_rec['shp_det'][$i]['qty'] * ($ctn[1] - $ctn[0] + 1);
	$gd_tal['nnw'] += $shp_rec['shp_det'][$i]['nnw'] * $shp_rec['shp_det'][$i]['cnum'];
	$gd_tal['nw'] += $shp_rec['shp_det'][$i]['nw'] * $shp_rec['shp_det'][$i]['cnum'];
	$gd_tal['gw'] += $shp_rec['shp_det'][$i]['gw'] * $shp_rec['shp_det'][$i]['cnum'];	
}


if($gd_mk > 1)//如果size breakdown有一種以上時,總計不要計算size breakdown
{
	$gd_tal['size'] = array();
	for($j=0; $j<sizeof($shp_rec['shp_det'][($i-1)]['size_qty']); $j++) $gd_tal['size'][$j] = '';
}
//小計+總計
if($pg_mk > 29)
{
	$pdf->AddPage();
	$pdf->ship_title($shp_rec['shp_det'][($i-1)]['size']);
	$pg_mk = 0;
}

$pdf->sub_total($sub_tal);
$pdf->gd_total($gd_tal);
$pg_mk+=2;

//COLOR/SIZE BREAKDOWN 
if($pg_mk > 29)
{
	$pdf->AddPage();
	$pg_mk = 0;
}
$pdf->ln();
$pdf->SetFont('Arial','B',10);
$pdf->SetX(8);
$pdf->cell(280,7,'COLOR/SIZE BREAKDOWN :',0,0,'L');
$pg_mk+=2;
$pdf->ln();

for($i=0; $i<sizeof($shp_rec['break']); $i++)
{
	if($pg_mk > 29)
	{
		$pdf->AddPage();
		if(!(isset($shp_rec['break'][$i]['tb_mk']) && ($shp_rec['break'][$i]['tb_mk'] == 1 || $shp_rec['break'][$i]['tb_mk'] == 2 || $shp_rec['break'][$i]['tb_mk'] == 5)))
			$pdf->break_title($shp_rec['break'][$i]['size']);// COLOR/SIZE BREAKDOWN title
		$pg_mk = 1;
	}

	if(isset($shp_rec['break'][$i]['tb_mk']) && ($shp_rec['break'][$i]['tb_mk'] == 1 ))
	{
		$pdf->break_total($shp_rec['break'][$i]);// COLOR/SIZE BREAKDOWN小計
		$pg_mk++;		
	}

	if(isset($shp_rec['break'][$i]['tb_mk']) && ($shp_rec['break'][$i]['tb_mk'] == 1 || $shp_rec['break'][$i]['tb_mk'] == 2 || $shp_rec['break'][$i]['tb_mk'] == 5))
	{
		$pdf->break_title($shp_rec['break'][$i]['size']);// COLOR/SIZE BREAKDOWN title
		$pg_mk++;
		
	}
	$pdf->break_txt($shp_rec['break'][$i]);// COLOR/SIZE BREAKDOWN 細項
	$pg_mk++;
	if(isset($shp_rec['break'][$i]['tb_mk']) && ( $shp_rec['break'][$i]['tb_mk'] == 3 || $shp_rec['break'][$i]['tb_mk'] == 5))
	{
		$pdf->break_total($shp_rec['break'][$i]);// COLOR/SIZE BREAKDOWN小計
		$pg_mk++;
	}	
}

if($pg_mk > 13)
{
	$pdf->AddPage();
	$pg_mk = 0;
}
$yy = $pdf->GetY();
$pdf->SetY(($yy+5));
$pdf->end_ship($gd_tal,$shp_rec['shp']['fty']); //匯總資料

$name=$shp_rec['shp']['inv_num'].'_pack.pdf';
$pdf->Output($name,'D');


break;	



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "ship_pack_print":			
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "ship_pack_bar_print":   //  .......
	check_authority('032',"view");	
		$shp_rec = $ship_doc->get_pack($PHP_id);

$j = $k = -1;
$x = $y =0;
$content = array();

for($i=0; $i<sizeof($shp_rec['shp_det']); $i++)
{
	$con_mk = 0;
	$shp_rec['shp_det'][$i]['ord_row'] = 0;
	$shp_rec['shp_det'][$i]['color_row'] =	0;
//取得不同的訂單content	於一陣列中
	for($ct=0; $ct<sizeof($content); $ct++)
	{
		if($content[$ct] == $shp_rec['shp_det'][$i]['content']) 
		{
			$con_mk = 1;
			break;
		}		
	}
	
	if($con_mk == 0)$content[] = $shp_rec['shp_det'][$i]['content'];

	if (isset($shp_rec['shp_det'][$i]['tb_mk']) && $shp_rec['shp_det'][$i]['tb_mk'] == 1)
	{
		$style_num = $color = '';		
	}
	
	if($style_num <> $shp_rec['shp_det'][$i]['style_num']) //整理sytle_num的顯示
	{
		$style_num = $shp_rec['shp_det'][$i]['style_num'];
		if($j > -1)
		{
			$shp_rec['shp_det'][$j]['ord_row'] = $x;
			if(strlen($shp_rec['shp_det'][$j]['style_num']) > 18 && $x > 1)
			{
				$tmp = $shp_rec['shp_det'][$j]['style_num'];
				$shp_rec['shp_det'][$j]['style_num'] = substr($tmp,0,18);
				$shp_rec['shp_det'][($j+1)]['style_num'] = substr($tmp,18);
			}
		}
		$x = 0;
		$j = $i;
		$color = '';
	}else{
		$shp_rec['shp_det'][$i]['style_num'] = '';
	}	
	
	if($color <> $shp_rec['shp_det'][$i]['color']) //整理color的顯示
	{
		$color = $shp_rec['shp_det'][$i]['color'];
		if($k > -1)
		{
			$shp_rec['shp_det'][$k]['color_row'] = $y;
			if(strlen($shp_rec['shp_det'][$k]['color']) > 18 && $y > 1)
			{
				$tmp = $shp_rec['shp_det'][$k]['color'];
				$shp_rec['shp_det'][$k]['color'] = substr($tmp,0,18);
				$shp_rec['shp_det'][($k+1)]['color'] = substr($tmp,18);
			}
		}
		$y = 0;
		$k = $i;
	}else{
		$shp_rec['shp_det'][$i]['color'] = '';	
	}	
	$x++;
	$y++;	

}
if($j > -1)
{

	$shp_rec['shp_det'][$j]['ord_row'] = $x;
	if(strlen($shp_rec['shp_det'][$j]['style_num']) > 18 && $x > 1)
	{
		$tmp = $shp_rec['shp_det'][$j]['style_num'];
		$shp_rec['shp_det'][$j]['style_num'] = substr($tmp,0,18);
		$shp_rec['shp_det'][($j+1)]['style_num'] = substr($tmp,18);

		
	}
}
if($k > -1)
{
	$shp_rec['shp_det'][$k]['color_row'] = $x;
	if(strlen($shp_rec['shp_det'][$k]['color']) > 18 && $y > 1)
	{
		$tmp = $shp_rec['shp_det'][$k]['color'];
		$shp_rec['shp_det'][$k]['color'] = substr($tmp,0,18);
		$shp_rec['shp_det'][($k+1)]['color'] = substr($tmp,18);
	}
}

//將不同的content轉成字串
$shp_rec['shp']['ord_content'] ='';
for($i=0; $i< sizeof($content); $i++)
{
	$shp_rec['shp']['ord_content'].=$content[$i]."/";
}
$shp_rec['shp']['ord_content'] = substr($shp_rec['shp']['ord_content'],0,-1);

//---------------------------------- 資料庫作業結束 ------------------------------

include_once($config['root_dir']."/lib/class.pdf_shp_pack_bar.php");
$pg_mk = 0;
session_register	('main_rec');
$main_rec = array (	'fty'			=>	$shp_rec['shp']['fty'],
										'inv_num'	=>	$shp_rec['shp']['inv_num'],
										'des'			=>	$shp_rec['shp']['des'],
										'date'		=>	$shp_rec['shp']['fv_etd'],
										'style'		=>	$shp_rec['shp']['ord_style'],
										'content'	=>	$shp_rec['shp']['ord_content']
										);

$print_title="DETAILED PACKING LIST";

$print_title1 = "our pdt. code : ".$shp_rec['shp']['ord_num'];
$print_title2 = "VER.".($shp_rec['shp']['ver']+1);
$creator = $shp_rec['shp']['submit_user'];
$mark = $shp_rec['shp']['inv_num'];
$fty = $shp_rec['shp']['fty'];


$pdf = new PDF_shp_pack_bar('L','mm','A4');
//$pdf->SetAutoPageBreak('on',10);
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();

$gd_mk = 0;

$ctn_num = $style = $color = '';

for($i=0; $i<sizeof($shp_rec['shp_det']); $i++) //列印packing list
{

	if($pg_mk > 29) //換頁
	{
		$pdf->AddPage();
		if(!(isset($shp_rec['shp_det'][$i]['tb_mk']) && $shp_rec['shp_det'][$i]['tb_mk'] == 1))
		{
			$pdf->ship_title($shp_rec['shp_det'][$i]['size']);
			$pg_mk = 3;
		}
		$pg_mk = 0;
	}

	if($i == 0) //初始化總計
	{

		$gd_tal = array();
		for($j=0; $j<sizeof($shp_rec['shp_det'][$i]['size_qty']); $j++) $gd_tal['size'][] = 0;
		$gd_tal['gw'] =$gd_tal['nw'] =$gd_tal['nnw'] = $gd_tal['qty'] = $gd_tal['ctn'] = 0;
	}		
	
	if($ctn_num != '' && $ctn_num <> $shp_rec['shp_det'][$i]['cnt'] && $style <> $shp_rec['shp_det'][$i]['style_num'] && $shp_rec['shp_det'][$i]['style_num'] <> ''  && $color <> $shp_rec['shp_det'][$i]['color'] && $shp_rec['shp_det'][$i]['color'] <> '')
	{
		$style = $shp_rec['shp_det'][$i]['style_num'];
		$color = $shp_rec['shp_det'][$i]['color'];
		$ctn_num = $shp_rec['shp_det'][$i]['cnt'];
		
		if($i > 0)
		{
			$pdf->sub_total($sub_tal); //小計列印
			$pg_mk++;
		}		
		$sub_tal = array(); //小計初始化
		for($j=0; $j<sizeof($shp_rec['shp_det'][$i]['size_qty']); $j++) $sub_tal['size'][] = 0;
		$sub_tal['gw'] =$sub_tal['nw'] =$sub_tal['nnw'] = $sub_tal['qty'] = $sub_tal['ctn'] = 0;
	}

	if (isset($shp_rec['shp_det'][$i]['tb_mk']) && $shp_rec['shp_det'][$i]['tb_mk'] == 1)
	{
		$pdf->ship_title($shp_rec['shp_det'][$i]['size']); //packing list表頭
		$pg_mk+=3;
//		if($i > 0)$pdf->sub_total($sub_tal);
		$sub_tal = array(); //小計初始化
		for($j=0; $j<sizeof($shp_rec['shp_det'][$i]['size_qty']); $j++) $sub_tal['size'][] = 0;
		$sub_tal['gw'] =$sub_tal['nw'] =$sub_tal['nnw'] = $sub_tal['qty'] = $sub_tal['ctn'] = 0;
		$ctn_num = $style = $color = '';
		$gd_mk++;
	}
	

	if(strlen($shp_rec['shp_det'][$i]['color']) > 22 || strlen($shp_rec['shp_det'][$i]['style_num']) > 22)
	{
		$pdf->ship_txt_mix($shp_rec['shp_det'][$i],$shp_rec['shp']['inner_box']); //color or style > 18 works, print 二行
		$pg_mk+=2;
	}else{

		$pdf->ship_txt($shp_rec['shp_det'][$i],$shp_rec['shp']['inner_box']); //print packing list
		$pg_mk++;
	}





	//小計
	$ctn = explode('-',$shp_rec['shp_det'][$i]['cnt']);
	for($j=0; $j<sizeof($shp_rec['shp_det'][$i]['size_qty']); $j++) 
	{
		if($shp_rec['shp']['inner_box'] == 0)$shp_rec['shp_det'][$i]['size_box'][$j] = 1;
		$sub_tal['size'][$j] += $shp_rec['shp_det'][$i]['size_qty'][$j] * $shp_rec['shp_det'][$i]['size_box'][$j] * ($ctn[1] - $ctn[0] + 1);		
	}
	
	$sub_tal['ctn'] +=  $shp_rec['shp_det'][$i]['cnum'];
	$sub_tal['qty'] += $shp_rec['shp_det'][$i]['qty'] * ($ctn[1] - $ctn[0] + 1);
	$sub_tal['nnw'] += $shp_rec['shp_det'][$i]['nnw'] * $shp_rec['shp_det'][$i]['cnum'];
	$sub_tal['nw'] += $shp_rec['shp_det'][$i]['nw'] * $shp_rec['shp_det'][$i]['cnum'];
	$sub_tal['gw'] += $shp_rec['shp_det'][$i]['gw'] * $shp_rec['shp_det'][$i]['cnum'];


	//總計
	$ctn = explode('-',$shp_rec['shp_det'][$i]['cnt']);
	if($gd_mk < 2) for($j=0; $j<sizeof($shp_rec['shp_det'][$i]['size_qty']); $j++) $gd_tal['size'][$j] += $shp_rec['shp_det'][$i]['size_qty'][$j] * ($ctn[1] - $ctn[0] + 1);
	$gd_tal['ctn'] += $shp_rec['shp_det'][$i]['cnum'];
	$gd_tal['qty'] += $shp_rec['shp_det'][$i]['qty'] * ($ctn[1] - $ctn[0] + 1);
	$gd_tal['nnw'] += $shp_rec['shp_det'][$i]['nnw'] * $shp_rec['shp_det'][$i]['cnum'];
	$gd_tal['nw'] += $shp_rec['shp_det'][$i]['nw'] * $shp_rec['shp_det'][$i]['cnum'];
	$gd_tal['gw'] += $shp_rec['shp_det'][$i]['gw'] * $shp_rec['shp_det'][$i]['cnum'];	
}


if($gd_mk > 1)//如果size breakdown有一種以上時,總計不要計算size breakdown
{
	$gd_tal['size'] = array();
	for($j=0; $j<sizeof($shp_rec['shp_det'][($i-1)]['size_qty']); $j++) $gd_tal['size'][$j] = '';
}
//小計+總計
if($pg_mk > 29)
{
	$pdf->AddPage();
	$pdf->ship_title($shp_rec['shp_det'][($i-1)]['size']);
	$pg_mk = 0;
}

$pdf->sub_total($sub_tal);
$pdf->gd_total($gd_tal);
$pg_mk+=2;

//COLOR/SIZE BREAKDOWN 
if($pg_mk > 29)
{
	$pdf->AddPage();
	$pg_mk = 0;
}
$pdf->ln();
$pdf->SetFont('Arial','B',10);
$pdf->SetX(8);
$pdf->cell(280,7,'COLOR/SIZE BREAKDOWN :',0,0,'L');
$pg_mk+=2;
$pdf->ln();

for($i=0; $i<sizeof($shp_rec['break']); $i++)
{
	if($pg_mk > 29)
	{
		$pdf->AddPage();
		if(!(isset($shp_rec['break'][$i]['tb_mk']) && ($shp_rec['break'][$i]['tb_mk'] == 1 || $shp_rec['break'][$i]['tb_mk'] == 2 || $shp_rec['break'][$i]['tb_mk'] == 5)))
			$pdf->break_title($shp_rec['break'][$i]['size']);// COLOR/SIZE BREAKDOWN title
		$pg_mk = 1;
	}

	if(isset($shp_rec['break'][$i]['tb_mk']) && ($shp_rec['break'][$i]['tb_mk'] == 1 ))
	{
		$pdf->break_total($shp_rec['break'][$i]);// COLOR/SIZE BREAKDOWN小計
		$pg_mk++;		
	}

	if(isset($shp_rec['break'][$i]['tb_mk']) && ($shp_rec['break'][$i]['tb_mk'] == 1 || $shp_rec['break'][$i]['tb_mk'] == 2 || $shp_rec['break'][$i]['tb_mk'] == 5))
	{
		$pdf->break_title($shp_rec['break'][$i]['size']);// COLOR/SIZE BREAKDOWN title
		$pg_mk++;
		
	}
	$pdf->break_txt($shp_rec['break'][$i]);// COLOR/SIZE BREAKDOWN 細項
	$pg_mk++;
	if(isset($shp_rec['break'][$i]['tb_mk']) && ( $shp_rec['break'][$i]['tb_mk'] == 3 || $shp_rec['break'][$i]['tb_mk'] == 5))
	{
		$pdf->break_total($shp_rec['break'][$i]);// COLOR/SIZE BREAKDOWN小計
		$pg_mk++;
	}	
}

if($pg_mk > 13)
{
	$pdf->AddPage();
	$pg_mk = 0;
}
$yy = $pdf->GetY();
$pdf->SetY(($yy+5));
$pdf->end_ship($gd_tal,$shp_rec['shp']['fty']); //匯總資料

$name=$shp_rec['shp']['inv_num'].'_pack.pdf';
$pdf->Output($name,'D');


break;	



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "ship_pack_ab_print":			
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "ship_pack_ab_print":   //  .......
	check_authority('032',"view");	
		$shp_rec = $ship_doc->get_pack($PHP_id);
		$shp_rec['shp']['que1'] = $ship_doc->get_other_value($shp_rec['shp']['id'],'HANGING GARMENTS');
		$shp_rec['shp']['que2'] = $ship_doc->get_other_value($shp_rec['shp']['id'],'BOXED GARMENT ON HANGER');
		$shp_rec['shp']['que3'] = $ship_doc->get_other_value($shp_rec['shp']['id'],'LOOSE CARTONS');
		$shp_rec['shp']['que4'] = $ship_doc->get_other_value($shp_rec['shp']['id'],'PALLETISED CARTONS');
		$shp_rec['shp']['que5'] = $ship_doc->get_other_value($shp_rec['shp']['id'],'ACCESSORIES');
		$shp_rec['shp']['ctn_num'] = $ship_doc->get_other_value($shp_rec['shp']['id'],'CONTAINER NUMBER');
		$shp_rec['shp']['seal'] = $ship_doc->get_other_value($shp_rec['shp']['id'],'SEAL NUMBER');
		$shp_rec['shp']['fnl_dept'] = $ship_doc->get_other_value($shp_rec['shp']['id'],'DEPT NUMBER');
		$shp_rec['shp']['dept_des'] = $ship_doc->get_other_value($shp_rec['shp']['id'],'DEPT DESCRIPTION');
		$shp_rec['shp']['dept_des'] = $ship_doc->get_other_value($shp_rec['shp']['id'],'DEPT DESCRIPTION');
		$shp_rec['shp']['fnl_des'] = $ship_doc->get_other_value($shp_rec['shp']['id'],'DESCRIPTION');
		$shp_rec['shp']['del_due'] = $ship_doc->get_other_value($shp_rec['shp']['id'],'DEL. DUE');
		$shp_rec['shp']['fnl_po'] = $ship_doc->get_other_value($shp_rec['shp']['id'],'TU PURCHASE ORDER');
		$shp_rec['shp']['fnl_style'] = $ship_doc->get_other_value($shp_rec['shp']['id'],'ITEM STYLE NUMBER');
		$shp_rec['shp']['fnl_name'] = $ship_doc->get_other_value($shp_rec['shp']['id'],'FROM');
		$shp_rec['shp']['fnl_addr'] = $ship_doc->get_other_value($shp_rec['shp']['id'],'TO');
		
$j = $k = -1;
$x = $y =0;
$content = array();

for($i=0; $i<sizeof($shp_rec['shp_det']); $i++)
{
	$con_mk = 0;
	$shp_rec['shp_det'][$i]['ord_row'] = 0;
	$shp_rec['shp_det'][$i]['color_row'] =	0;
//取得不同的訂單content	於一陣列中
	for($ct=0; $ct<sizeof($content); $ct++)
	{
		if($content[$ct] == $shp_rec['shp_det'][$i]['content']) 
		{
			$con_mk = 1;
			break;
		}		
	}
	
	if($con_mk == 0)$content[] = $shp_rec['shp_det'][$i]['content'];

	if (isset($shp_rec['shp_det'][$i]['tb_mk']) && $shp_rec['shp_det'][$i]['tb_mk'] == 1)
	{
		$style_num = $color = '';		
	}
	
	if($style_num <> $shp_rec['shp_det'][$i]['style_num']) //整理sytle_num的顯示
	{
		$style_num = $shp_rec['shp_det'][$i]['style_num'];
		if($j > -1)
		{
			$shp_rec['shp_det'][$j]['ord_row'] = $x;
			if(strlen($shp_rec['shp_det'][$j]['style_num']) > 18 && $x > 1)
			{
				$tmp = $shp_rec['shp_det'][$j]['style_num'];
				$shp_rec['shp_det'][$j]['style_num'] = substr($tmp,0,18);
				$shp_rec['shp_det'][($j+1)]['style_num'] = substr($tmp,18);
			}
		}
		$x = 0;
		$j = $i;
		$color = '';
	}else{
		$shp_rec['shp_det'][$i]['style_num'] = '';
	}	
	
	if($color <> $shp_rec['shp_det'][$i]['color']) //整理color的顯示
	{
		$color = $shp_rec['shp_det'][$i]['color'];
		if($k > -1)
		{
			$shp_rec['shp_det'][$k]['color_row'] = $y;
			if(strlen($shp_rec['shp_det'][$k]['color']) > 18 && $y > 1)
			{
				$tmp = $shp_rec['shp_det'][$k]['color'];
				$shp_rec['shp_det'][$k]['color'] = substr($tmp,0,18);
				$shp_rec['shp_det'][($k+1)]['color'] = substr($tmp,18);
			}
		}
		$y = 0;
		$k = $i;
	}else{
		$shp_rec['shp_det'][$i]['color'] = '';	
	}	
	$x++;
	$y++;	

}
if($j > -1)
{

	$shp_rec['shp_det'][$j]['ord_row'] = $x;
	if(strlen($shp_rec['shp_det'][$j]['style_num']) > 18 && $x > 1)
	{
		$tmp = $shp_rec['shp_det'][$j]['style_num'];
		$shp_rec['shp_det'][$j]['style_num'] = substr($tmp,0,18);
		$shp_rec['shp_det'][($j+1)]['style_num'] = substr($tmp,18);

		
	}
}
if($k > -1)
{
	$shp_rec['shp_det'][$k]['color_row'] = $x;
	if(strlen($shp_rec['shp_det'][$k]['color']) > 18 && $y > 1)
	{
		$tmp = $shp_rec['shp_det'][$k]['color'];
		$shp_rec['shp_det'][$k]['color'] = substr($tmp,0,18);
		$shp_rec['shp_det'][($k+1)]['color'] = substr($tmp,18);
	}
}

//將不同的content轉成字串
$shp_rec['shp']['ord_content'] ='';
for($i=0; $i< sizeof($content); $i++)
{
	$shp_rec['shp']['ord_content'].=$content[$i]."/";
}
$shp_rec['shp']['ord_content'] = substr($shp_rec['shp']['ord_content'],0,-1);

//---------------------------------- 資料庫作業結束 ------------------------------

include_once($config['root_dir']."/lib/class.pdf_shp_pack_ab.php");
$pg_mk = 0;
session_register	('main_rec');
$main_rec = array (	'fty'			=>	$shp_rec['shp']['fty'],
										'inv_num'	=>	$shp_rec['shp']['inv_num'],
										'des'			=>	$shp_rec['shp']['des'],
										'date'		=>	$shp_rec['shp']['fv_etd'],
										'style'		=>	$shp_rec['shp']['ord_style'],
										'content'	=>	$shp_rec['shp']['ord_content']
										);

$print_title="DETAILED PACKING LIST";

$print_title1 = "our pdt. code : ".$shp_rec['shp']['ord_num'];
$print_title2 = "VER.".($shp_rec['shp']['ver']+1);
$creator = $shp_rec['shp']['submit_user'];
$mark = $shp_rec['shp']['inv_num'];
$fty = $shp_rec['shp']['fty'];


$pdf = new PDF_shp_pack('L','mm','A4');
//$pdf->SetAutoPageBreak('on',10);
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();

$gd_mk = 0;

$ctn_num = $style = $color = '';

for($i=0; $i<sizeof($shp_rec['shp_det']); $i++) //列印packing list
{

	if($pg_mk > 29) //換頁
	{
		$pdf->AddPage();
		if(!(isset($shp_rec['shp_det'][$i]['tb_mk']) && $shp_rec['shp_det'][$i]['tb_mk'] == 1))
		{
			$pdf->ship_title($shp_rec['shp_det'][$i]['size']);
			$pg_mk = 3;
		}
		$pg_mk = 0;
	}

	if($i == 0) //初始化總計
	{

		$gd_tal = array();
		for($j=0; $j<sizeof($shp_rec['shp_det'][$i]['size_qty']); $j++) $gd_tal['size'][] = 0;
		$gd_tal['gw'] =$gd_tal['nw'] =$gd_tal['nnw'] = $gd_tal['qty'] = $gd_tal['ctn'] = 0;
	}		
	
	if($ctn_num != '' && $ctn_num <> $shp_rec['shp_det'][$i]['cnt'] && $style <> $shp_rec['shp_det'][$i]['style_num'] && $shp_rec['shp_det'][$i]['style_num'] <> ''  && $color <> $shp_rec['shp_det'][$i]['color'] && $shp_rec['shp_det'][$i]['color'] <> '')
	{
		$style = $shp_rec['shp_det'][$i]['style_num'];
		$color = $shp_rec['shp_det'][$i]['color'];
		$ctn_num = $shp_rec['shp_det'][$i]['cnt'];
		
		if($i > 0)
		{
			$pdf->sub_total($sub_tal); //小計列印
			$pg_mk++;
		}		
		$sub_tal = array(); //小計初始化
		for($j=0; $j<sizeof($shp_rec['shp_det'][$i]['size_qty']); $j++) $sub_tal['size'][] = 0;
		$sub_tal['gw'] =$sub_tal['nw'] =$sub_tal['nnw'] = $sub_tal['qty'] = $sub_tal['ctn'] = 0;
	}

	if (isset($shp_rec['shp_det'][$i]['tb_mk']) && $shp_rec['shp_det'][$i]['tb_mk'] == 1)
	{
		$pdf->ship_title($shp_rec['shp_det'][$i]['size']); //packing list表頭
		$pg_mk+=3;
//		if($i > 0)$pdf->sub_total($sub_tal);
		$sub_tal = array(); //小計初始化
		for($j=0; $j<sizeof($shp_rec['shp_det'][$i]['size_qty']); $j++) $sub_tal['size'][] = 0;
		$sub_tal['gw'] =$sub_tal['nw'] =$sub_tal['nnw'] = $sub_tal['qty'] = $sub_tal['ctn'] = 0;
		$ctn_num = $style = $color = '';
		$gd_mk++;
	}
	

	if(strlen($shp_rec['shp_det'][$i]['color']) > 18 || strlen($shp_rec['shp_det'][$i]['style_num']) > 18)
	{
		$pdf->ship_txt_mix($shp_rec['shp_det'][$i],$shp_rec['shp']['inner_box']); //color or style > 18 works, print 二行
		$pg_mk+=2;
	}else{

		$pdf->ship_txt($shp_rec['shp_det'][$i],$shp_rec['shp']['inner_box']); //print packing list
		$pg_mk++;
	}





	//小計
	$ctn = explode('-',$shp_rec['shp_det'][$i]['cnt']);
	for($j=0; $j<sizeof($shp_rec['shp_det'][$i]['size_qty']); $j++) 
	{
		if($shp_rec['shp']['inner_box'] == 0)$shp_rec['shp_det'][$i]['size_box'][$j] = 1;
		$sub_tal['size'][$j] += $shp_rec['shp_det'][$i]['size_qty'][$j] * $shp_rec['shp_det'][$i]['size_box'][$j] * ($ctn[1] - $ctn[0] + 1);		
	}
	
	$sub_tal['ctn'] +=  $shp_rec['shp_det'][$i]['cnum'];
	$sub_tal['qty'] += $shp_rec['shp_det'][$i]['qty'] * ($ctn[1] - $ctn[0] + 1);
	$sub_tal['nnw'] += $shp_rec['shp_det'][$i]['nnw'] * $shp_rec['shp_det'][$i]['cnum'];
	$sub_tal['nw'] += $shp_rec['shp_det'][$i]['nw'] * $shp_rec['shp_det'][$i]['cnum'];
	$sub_tal['gw'] += $shp_rec['shp_det'][$i]['gw'] * $shp_rec['shp_det'][$i]['cnum'];


	//總計
	$ctn = explode('-',$shp_rec['shp_det'][$i]['cnt']);
	if($gd_mk < 2) for($j=0; $j<sizeof($shp_rec['shp_det'][$i]['size_qty']); $j++) $gd_tal['size'][$j] += $shp_rec['shp_det'][$i]['size_qty'][$j] * ($ctn[1] - $ctn[0] + 1);
	$gd_tal['ctn'] += $shp_rec['shp_det'][$i]['cnum'];
	$gd_tal['qty'] += $shp_rec['shp_det'][$i]['qty'] * ($ctn[1] - $ctn[0] + 1);
	$gd_tal['nnw'] += $shp_rec['shp_det'][$i]['nnw'] * $shp_rec['shp_det'][$i]['cnum'];
	$gd_tal['nw'] += $shp_rec['shp_det'][$i]['nw'] * $shp_rec['shp_det'][$i]['cnum'];
	$gd_tal['gw'] += $shp_rec['shp_det'][$i]['gw'] * $shp_rec['shp_det'][$i]['cnum'];	
}


if($gd_mk > 1)//如果size breakdown有一種以上時,總計不要計算size breakdown
{
	$gd_tal['size'] = array();
	for($j=0; $j<sizeof($shp_rec['shp_det'][($i-1)]['size_qty']); $j++) $gd_tal['size'][$j] = '';
}
//小計+總計
if($pg_mk > 29)
{
	$pdf->AddPage();
	$pdf->ship_title($shp_rec['shp_det'][($i-1)]['size']);
	$pg_mk = 0;
}

$pdf->sub_total($sub_tal);
$pdf->gd_total($gd_tal);
$pg_mk+=2;

//COLOR/SIZE BREAKDOWN 
if($pg_mk > 29)
{
	$pdf->AddPage();
	$pg_mk = 0;
}
$pdf->ln();
$pdf->SetFont('Arial','B',10);
$pdf->SetX(8);
$pdf->cell(280,7,'COLOR/SIZE BREAKDOWN :',0,0,'L');
$pg_mk+=2;
$pdf->ln();

for($i=0; $i<sizeof($shp_rec['break']); $i++)
{

	if($pg_mk > 29)
	{
		$pdf->AddPage();
		if(!(isset($shp_rec['break'][$i]['tb_mk']) && ($shp_rec['break'][$i]['tb_mk'] == 1 || $shp_rec['break'][$i]['tb_mk'] == 2 || $shp_rec['break'][$i]['tb_mk'] == 5)))
			$pdf->break_title($shp_rec['break'][$i]['size']);// COLOR/SIZE BREAKDOWN title
		$pg_mk = 1;
	}

	if(isset($shp_rec['break'][$i]['tb_mk']) && ($shp_rec['break'][$i]['tb_mk'] == 1 ))
	{
		$pdf->break_total($shp_rec['break'][$i]);// COLOR/SIZE BREAKDOWN小計
		$pg_mk++;		
	}

	if(isset($shp_rec['break'][$i]['tb_mk']) && ($shp_rec['break'][$i]['tb_mk'] == 1 || $shp_rec['break'][$i]['tb_mk'] == 2 || $shp_rec['break'][$i]['tb_mk'] == 5))
	{
		$pdf->break_title($shp_rec['break'][$i]['size']);// COLOR/SIZE BREAKDOWN title
		$pg_mk++;
		
	}
	$pdf->break_txt($shp_rec['break'][$i]);// COLOR/SIZE BREAKDOWN 細項
	$pg_mk++;
	if(isset($shp_rec['break'][$i]['tb_mk']) && ( $shp_rec['break'][$i]['tb_mk'] == 3 || $shp_rec['break'][$i]['tb_mk'] == 5))
	{
		$pdf->break_total($shp_rec['break'][$i]);// COLOR/SIZE BREAKDOWN小計
		$pg_mk++;
	}	
}

if($pg_mk > 21)
{
	$pdf->AddPage();
	$pg_mk = 0;
}
$yy = $pdf->GetY();
$pdf->SetY(($yy+5));
$pdf->end_ship($gd_tal,$shp_rec['shp']['fty']); //匯總資料

$name=$shp_rec['shp']['inv_num'].'_pack.pdf';
$pdf->Output($name,'D');


break;	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "ship_inv_print":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "ship_inv_print":
 	check_authority('034',"view");

		$shp_rec = $ship_doc->get_inv($PHP_id);
/*
		$op['shp'] = $shp_rec['shp'];
		$op['shp_det'] = $shp_rec['shp_det'];
		$op['amt'] = $shp_rec['amt'];
		$op['shp']['fty_det'] = str_replace( chr(13).chr(10), "<br>",  $op['shp']['fty_det'] );
		$op['shp']['ship_det'] = str_replace( chr(13).chr(10), "<br>", $op['shp']['ship_det'] );
		$op['shp']['benf_det'] = str_replace( chr(13).chr(10), "<br>", $op['shp']['benf_det'] );
		$op['shp']['ship_mark'] = str_replace( chr(13).chr(10), "<br>", $op['shp']['ship_mark'] );
		$op['shp']['others'] = str_replace( chr(13).chr(10), "<br>", $op['shp']['others'] );
*/
//---------------------------------- 資料庫作業結束 ------------------------------

include_once($config['root_dir']."/lib/class.pdf_ship_inv.php");
session_register	('main_rec');
$main_rec = array (	'fty'					=>	$shp_rec['shp']['fty'],
										'inv_num'			=>	$shp_rec['shp']['inv_num'],
										'bill_addr1'	=>	$shp_rec['shp']['bill_addr1'],
										'bill_addr2'	=>	$shp_rec['shp']['bill_addr2'],
										'ship_date'		=>	$shp_rec['shp']['fv_etd'],
										'date'				=>	$shp_rec['shp']['submit_date'],
										'ship_by'			=>	$shp_rec['shp']['ship_by'],
										'via'					=>	$shp_rec['shp']['ship_via'],
										'ship_from'		=>	$shp_rec['shp']['ship_from'],
										'ship_to'			=>	$shp_rec['shp']['ship_to'],
										'payment'			=>	$shp_rec['shp']['payment'],
										'bank'				=>	$shp_rec['shp']['lc_bank'],
										'country_org'	=>	$shp_rec['shp']['country_org'],
										'lc_no'				=>	$shp_rec['shp']['lc_no'],
										'lc_date'			=>	$shp_rec['shp']['lc_date'],
										);

$print_title="INVOICE";

$print_title1  = "our pdt. code : ".$shp_rec['shp']['ord_num'];
$print_title2 = "VER.".($shp_rec['shp']['ver']+1);
$creator = $shp_rec['shp']['submit_user'];
$mark = $shp_rec['shp']['inv_num'];
$fty = $shp_rec['shp']['fty'];


$pdf = new PDF_shidoc_inv('P','mm','A4');
//$pdf->SetAutoPageBreak('on',10);
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();

$pg_mk = 2;//TOTAL 53 ROW
$pdf->item_title($shp_rec['shp']['ship_term']);
for($i=0; $i<sizeof($shp_rec['shp_det']); $i++)
{
	$pg_mk+=4;
	if($pg_mk > 51)
	{
		$pdf->AddPage();
		$pdf->item_title($shp_rec['shp']['ship_term']);
		$pg_mk = 2;
	}
	$pdf->item_txt($shp_rec['shp_det'][$i]);
	
}



$pg_mk+=4;
if($pg_mk > 51)
{
	$pdf->AddPage();
	$pdf->item_title($shp_rec['shp']['ship_term']);
	$pg_mk = 2;
}
$pdf->item_total($shp_rec['amt'],$shp_rec['shp_charge']);


if($shp_rec['shp']['others'])
{
	
	$txt = explode( chr(13).chr(10), $shp_rec['shp']['others'] );
	$pg_mk+=(sizeof($txt)+1);
	if($pg_mk > 51)
	{
		$pdf->AddPage();
		$pg_mk = 0;
	}
	$pdf->inv_txt('',$txt);
}
if($shp_rec['shp']['fty_det'])
{
	$txt = explode( chr(13).chr(10), $shp_rec['shp']['fty_det'] );
	if($shp_rec['shp']['mid_no'])$txt[] = "M.I.D. NO.: ".$shp_rec['shp']['mid_no'];
	if($shp_rec['shp']['country_org'])$txt[] = "COUNTRY OF ORIGIN: ".$shp_rec['shp']['country_org'];
	$pg_mk+=(sizeof($txt)+3);
	if($pg_mk > 51)
	{
		$pdf->AddPage();
		$pg_mk = 0;
	}
	$pdf->inv_txt('MANUFACTURER NAME & ADDRESS :',$txt);
}


if($shp_rec['shp']['benf_det'])
{
	$txt = explode( chr(13).chr(10), $shp_rec['shp']['benf_det'] );
	$pg_mk+=(sizeof($txt)+1);
	if($pg_mk > 51)
	{
		$pdf->AddPage();
		$pg_mk = 0;
	}
	$pdf->inv_txt('BENEFICIARY ACCOUNT DETAIL AS BELOW :',$txt);
}

$pg_mk+=5;

if($pg_mk > 51)
{
	$pdf->AddPage();
	$pg_mk = 0;
}

$pdf->pack_txt($shp_rec['pack']);

if($shp_rec['shp']['ship_mark'])
{
	$txt = explode( chr(13).chr(10), $shp_rec['shp']['ship_mark'] );
	$pg_mk+=(sizeof($txt)+1);
	if($pg_mk > 51)
	{
		$pdf->AddPage();
		$pg_mk = 0;
	}
	$pdf->inv_txt('SHIPPING MARK :',$txt);
}
$name=$shp_rec['shp']['inv_num'].'_inv.pdf';
$pdf->Output($name,'D');


break;	


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "inv_ab_print":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "inv_ab_print":
 	check_authority('034',"view");

		$shp_rec = $ship_doc->get_inv($PHP_id);
		$shp_rec['shp']['invoice_of'] = $ship_doc->get_other_value($PHP_id,'INVOICE OF');
		$shp_rec['shp']['tuc_id'] = $ship_doc->get_other_value($PHP_id,'TUC ID');
//---------------------------------- 資料庫作業結束 ------------------------------

include_once($config['root_dir']."/lib/class.pdf_ship_inv_ab.php");
session_register	('main_rec');
$main_rec = array (	'fty'					=>	$shp_rec['shp']['fty'],
										'inv_num'			=>	$shp_rec['shp']['inv_num'],
										'bill_addr1'	=>	$shp_rec['shp']['bill_addr1'],
										'bill_addr2'	=>	$shp_rec['shp']['bill_addr2'],
										'ship_date'		=>	$shp_rec['shp']['fv_etd'],
										'date'				=>	$shp_rec['shp']['submit_date'],
										'ship_by'			=>	$shp_rec['shp']['ship_by'],
										'via'					=>	$shp_rec['shp']['ship_via'],
										'ship_from'		=>	$shp_rec['shp']['ship_from'],
										'ship_to'			=>	$shp_rec['shp']['ship_to'],
										'payment'			=>	$shp_rec['shp']['payment'],
										'bank'				=>	$shp_rec['shp']['lc_bank'],
										'country_org'	=>	$shp_rec['shp']['country_org'],
										'lc_no'				=>	$shp_rec['shp']['lc_no'],
										'lc_date'			=>	$shp_rec['shp']['lc_date'],
										'invoice_of'	=>	$shp_rec['shp']['invoice_of'],
										'tuc_id'			=>	$shp_rec['shp']['tuc_id'],
										);

$print_title="INVOICE";

$print_title1  = "our pdt. code : ".$shp_rec['shp']['ord_num'];
$print_title2 = "VER.".($shp_rec['shp']['ver']+1);
$creator = $shp_rec['shp']['submit_user'];
$mark = $shp_rec['shp']['inv_num'];
$fty = $shp_rec['shp']['fty'];


$pdf = new PDF_shidoc_inv_ab('P','mm','A4');
//$pdf->SetAutoPageBreak('on',10);
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();

$pg_mk = 2;//TOTAL 53 ROW
$pdf->item_title($shp_rec['shp']['ship_term'],$shp_rec['shp']['des'],$shp_rec['shp']['tuc_id']);
for($i=0; $i<sizeof($shp_rec['shp_det']); $i++)
{
	$pg_mk+=5;
	if($pg_mk > 51)
	{
		$pdf->AddPage();
		$pdf->item_title($shp_rec['shp']['ship_term'],$shp_rec['shp']['des'],$shp_rec['shp']['tuc_id']);
		$pg_mk = 2;
	}
	$shp_rec['shp_det'][$i]['line'] = $ship_doc->get_other_ord_value($shp_rec['shp_det'][$i]['ship_ord_id'],'LINE');
	$pdf->item_txt($shp_rec['shp_det'][$i]);
	
}



$pg_mk+=4;
if($pg_mk > 51)
{
	$pdf->AddPage();
	$pdf->item_title($shp_rec['shp']['ship_term']);
	$pg_mk = 2;
}
$pdf->item_total($shp_rec['amt'],$shp_rec['shp_charge']);


if($shp_rec['shp']['others'])
{	
	if($shp_rec['shp']['mid_no'])$txt[0] = "M.I.D. NO.: ".$shp_rec['shp']['mid_no'];
	if($shp_rec['shp']['country_org'])$txt[1] = "COUNTRY OF ORIGIN: ".$shp_rec['shp']['country_org'];

	$tmp = explode( chr(13).chr(10), $shp_rec['shp']['others'] );
	for($m=0; $m<sizeof($tmp); $m++)$txt[] = $tmp[$m];
	$pg_mk+=(sizeof($txt)+3);
	if($pg_mk > 51)
	{
		$pdf->AddPage();
		$pg_mk = 0;
	}
	$pdf->inv_txt('',$txt);
}
if($shp_rec['shp']['fty_det'])
{
	$txt = explode( chr(13).chr(10), $shp_rec['shp']['fty_det'] );
	$pg_mk+=(sizeof($txt)+1);
	if($pg_mk > 51)
	{
		$pdf->AddPage();
		$pg_mk = 0;
	}
	$pdf->inv_txt('MANUFACTURER NAME & ADDRESS :',$txt);
}


if($shp_rec['shp']['ship_mark'])
{
	$txt = explode( chr(13).chr(10), $shp_rec['shp']['ship_mark'] );
	$pg_mk+=(sizeof($txt)+1);
	if($pg_mk > 51)
	{
		$pdf->AddPage();
		$pg_mk = 0;
	}
	$pdf->inv_txt('SHIPPING MARK :',$txt);
}
$name=$shp_rec['shp']['inv_num'].'_inv_AB.pdf';
$pdf->Output($name,'D');


break;	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "shipdoc_colorway_edit":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "shipdoc_colorway_edit":
 	check_authority('032',"add");
		$size_qty = '';
		$PHP_ctn_qty = 0;
		$size_box = '';
		
		foreach($PHP_old_color as $key => $value)
		{
			if($PHP_old_color[$key] <> $PHP_new_color[$key])
			{
				$ship_doc->change_color_name($key,$PHP_new_color[$key]);
			}
			
			$wiqty->update_field('item', $PHP_item[$key], $key);
		}
		foreach($PHP_nnw as $key => $value)
		{
			$id = $key;
			$nnw_str = '';
			foreach($PHP_nnw[$key] as $sub_key => $sub_value)
			{
				$nnw_str .=$sub_value.',';				
			}
			$nnw_str = substr($nnw_str,0,-1);
			$wiqty->update_field('nnw', $nnw_str, $id);

		}
		
		
		$mesg = "success update size/colorway on ORDER:[ ".$PHP_ord_num." ]";
		$log->log_add(0,"48A",$mesg);
		if($PHP_inbox == 1)
		{
			$redir_str = 'shipdoc.php?PHP_action=packet_inbox_add&PHP_id='.$PHP_id."&PHP_msg=".$mesg."&PHP_from=".$PHP_from;			
		}else if(isset($PHP_bar_mk) && $PHP_bar_mk == 1){
			$redir_str = 'shipdoc.php?PHP_action=packet_bar_add&PHP_id='.$PHP_id."&PHP_msg=".$mesg."&PHP_from=".$PHP_from;			
		}else{
			$redir_str = 'shipdoc.php?PHP_action=packet_add&PHP_id='.$PHP_id."&PHP_msg=".$mesg."&PHP_from=".$PHP_from;
		}
		redirect_page($redir_str);
		
/*		
		$op = $ship_doc->get_order('',$PHP_ord_num);
		if(sizeof($op['color']))
		{
			$op['color_select'] = $arry2->select($op['color'],'','PHP_color','select','');
		}
		$op['assort'] = $ship_doc->count_size_qty($op['ord']['order_num'], $op['assort']);
		
		if($PHP_id)		
		{

			$shp_rec = $ship_doc->get($PHP_id);
			$op['shp'] = $shp_rec['shp'];
			$op['shp_det'] = $shp_rec['shp_det'];
		}else{
			$op['shp']['consignee'] = '';
			$op['shp']['id']=0;
		}
		$where_str = "WHERE cust='".$op['ord']['cust']."'";
		$csgn = $cust->get_csge_fields('s_name',$where_str);
		if(!$csgn)$csgn=array('');
		$op['csgn_select'] = $arry2->select($csgn,$op['shp']['consignee'],'PHP_consignee','select','');


		if($PHP_inbox == 1)
		{
			if(isset($PHP_back_str))
			{

				$op['back_str'] = $PHP_back_str;
				if(isset($PHP_cfm))
				{
					page_display($op, '033', $TPL_SHIPDOC_PACK_INBOX_EDIT_CFM);
				}else{

					page_display($op, '032', $TPL_SHIPDOC_PACK_INBOX_EDIT);
				}
			}else{
				page_display($op, '032', $TPL_SHIPDOC_PACK_INBOX_ADD);
			}
		}else{
			if(isset($PHP_back_str))
			{
				$op['back_str'] = $PHP_back_str;
				if(isset($PHP_cfm))
				{
					page_display($op, '033', $TPL_SHIPDOC_PACK_EDIT_CFM);
				}else{
					page_display($op, '032', $TPL_SHIPDOC_PACK_EDIT);
				}
			}else{
				page_display($op, '032', $TPL_SHIPDOC_PACK_ADD);
			}
		}
	break;
*/

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "ship_bill_print":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "ship_bill_print":
 	check_authority('032',"view");

		$shp_rec = $ship_doc->get_inv($PHP_id);

include_once($config['root_dir']."/lib/class.pdf_ship_bill.php");
session_register	('main_rec');


$print_title="BILL DETAILS";


$creator = $shp_rec['shp']['submit_user'];
$mark = $shp_rec['shp']['inv_num'];
$fty = $shp_rec['shp']['fty'];


$pdf = new PDF_shidoc_bill('P','mm','A4');
//$pdf->SetAutoPageBreak('on',10);
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();

$pg_mk = 2;//TOTAL 53 ROW

$pdf->base_data($shp_rec['shp']);

for($i=0; $i<sizeof($shp_rec['shp_det']); $i++)
{
	$pdf->item_txt($shp_rec['shp_det'][$i],$i);	
}

$parm['qty'] = $shp_rec['amt']['qty'];
$parm['ctn'] = $shp_rec['pack']['ctn'];
$parm['gw']  = $shp_rec['pack']['gw'];
$parm['cbm'] = $shp_rec['shp']['cbm'];
$parm['ship_date'] = $shp_rec['shp']['fv_etd'];
$parm['ctn_num'] = $shp_rec['shp']['ctn_num'];
$parm['seal'] = $shp_rec['shp']['seal'];

$pdf->item_total($parm);
$name=$shp_rec['shp']['inv_num'].'_bill.pdf';
$pdf->Output($name,'D');
break;	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "ship_ab2_print":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "ship_ab2_print":
 	check_authority('032',"view");

		$shp_rec = $ship_doc->get_pack($PHP_id);
		$shp_rec['shp']['que1'] = $ship_doc->get_other_value($shp_rec['shp']['id'],'HANGING GARMENTS');
		$shp_rec['shp']['que2'] = $ship_doc->get_other_value($shp_rec['shp']['id'],'BOXED GARMENT ON HANGER');
		$shp_rec['shp']['que3'] = $ship_doc->get_other_value($shp_rec['shp']['id'],'LOOSE CARTONS');
		$shp_rec['shp']['que4'] = $ship_doc->get_other_value($shp_rec['shp']['id'],'PALLETISED CARTONS');
		$shp_rec['shp']['que5'] = $ship_doc->get_other_value($shp_rec['shp']['id'],'ACCESSORIES');
		$shp_rec['shp']['ctn_num'] = $ship_doc->get_other_value($shp_rec['shp']['id'],'CONTAINER NUMBER');
		$shp_rec['shp']['seal'] = $ship_doc->get_other_value($shp_rec['shp']['id'],'SEAL NUMBER');
		$shp_rec['shp']['fnl_dept'] = $ship_doc->get_other_value($shp_rec['shp']['id'],'DEPT NUMBER');
		$shp_rec['shp']['dept_des'] = $ship_doc->get_other_value($shp_rec['shp']['id'],'DEPT DESCRIPTION');
		$shp_rec['shp']['dept_des'] = $ship_doc->get_other_value($shp_rec['shp']['id'],'DEPT DESCRIPTION');
		$shp_rec['shp']['fnl_des'] = $ship_doc->get_other_value($shp_rec['shp']['id'],'DESCRIPTION');
		$shp_rec['shp']['del_due'] = $ship_doc->get_other_value($shp_rec['shp']['id'],'DEL. DUE');
		$shp_rec['shp']['fnl_po'] = $ship_doc->get_other_value($shp_rec['shp']['id'],'TU PURCHASE ORDER');
		$shp_rec['shp']['fnl_style'] = $ship_doc->get_other_value($shp_rec['shp']['id'],'ITEM STYLE NUMBER');
		$shp_rec['shp']['fnl_name'] = $ship_doc->get_other_value($shp_rec['shp']['id'],'TU NAME');
		$shp_rec['shp']['fnl_addr'] = $ship_doc->get_other_value($shp_rec['shp']['id'],'TU ADDR');
		
include_once($config['root_dir']."/lib/class.pdf_ship_ab2.php");
session_register	('main_rec');


$print_title="BILL DETAILS";
$print_title1  = "our pdt. code : ".$shp_rec['shp']['ord_num'];

$creator = $shp_rec['shp']['submit_user'];
$mark = $shp_rec['shp']['inv_num'];
$fty = $shp_rec['shp']['fty'];
session_register	('foot');
$foot = array('po'	=>	$shp_rec['shp']['fnl_po'],
							'inv_num'	=>	$shp_rec['shp']['inv_num'],
							'cust'		=>	$shp_rec['shp']['cust_name'],
							'ver'			=>	$shp_rec['shp']['ver'],
							);
$po = $shp_rec['shp']['fnl_po'];
$inv_num = $shp_rec['shp']['inv_num'];
$cust = $shp_rec['shp']['cust_name'];

$pdf = new PDF_shidoc_ab2('P','mm','A4');
//$pdf->SetAutoPageBreak('on',10);
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();
$pdf->base_data($shp_rec['shp']);
$x=21; // max=56
$ctn='';
$pdf->item_title($shp_rec['shp']['unit']);
$shp_rpt = array();
$j=-1;
$z=0;

for($i=0; $i<sizeof($shp_rec['shp_det']); $i++)
{
	if($ctn <> $shp_rec['shp_det'][$i]['cnt'])
	{
		$j++;
		$z=0;
		$shp_rpt[$j] = $shp_rec['shp_det'][$i];
		$ctn = $shp_rec['shp_det'][$i]['cnt'];
	}	

	$color= explode('-',$shp_rec['shp_det'][$i]['color']);
	if(!isset($color[1]))$color= explode('_',$shp_rec['shp_det'][$i]['color']);
	if(!isset($color[1]))$color[1] = '';	
	for($k=0; $k<sizeof($shp_rec['shp_det'][$i]['size']); $k++)
	{		
		if($shp_rec['shp_det'][$i]['size_qty'][$k] > 0)
		{
			$shp_rpt[$j]['rpt'][$z]['qty'] = $shp_rec['shp_det'][$i]['size_qty'][$k];
			$shp_rpt[$j]['rpt'][$z]['box'] = $shp_rec['shp_det'][$i]['size_box'][$k];
			$shp_rpt[$j]['rpt'][$z]['size'] = $shp_rec['shp_det'][$i]['size'][$k]." ".$shp_rec['shp_det'][$i]['item'];
			$shp_rpt[$j]['rpt'][$z]['sort'] = $shp_rec['shp_det'][$i]['size'][$k].$shp_rec['shp_det'][$i]['item'];

			$z++;
		}
	}
}
$ctn_count = 0;
for($i=0; $i<sizeof($shp_rpt); $i++)
{
	
		$x= $x+sizeof($shp_rpt[$i]['rpt']);
		if($x > 56)
		{
			$x = 0;
			$pdf->AddPage();
			$pdf->ln();
			$pdf->ln();
			$pdf->item_title($shp_rec['shp']['unit']);

		}
		$ctn_count += ($shp_rpt[$i]['e_cnt'] - $shp_rpt[$i]['s_cnt'] + 1);
		$shp_rpt[$i]['rpt'] = bubble_sort_s($shp_rpt[$i]['rpt']);
		$pdf->item_txt($shp_rpt[$i]);
}
$pdf->item_total($shp_rec['amt'],$ctn_count);

$name=$shp_rec['shp']['inv_num'].'_tu2.pdf';
$pdf->Output($name,'D');
break;	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "ship_ab1_print":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "ship_ab1_print":
 	check_authority('032',"view");

		$shp_rec = $ship_doc->get_pack($PHP_id);
		$shp_rec['shp']['que1'] = $ship_doc->get_other_value($shp_rec['shp']['id'],'HANGING GARMENTS');
		$shp_rec['shp']['que2'] = $ship_doc->get_other_value($shp_rec['shp']['id'],'BOXED GARMENT ON HANGER');
		$shp_rec['shp']['que3'] = $ship_doc->get_other_value($shp_rec['shp']['id'],'LOOSE CARTONS');
		$shp_rec['shp']['que4'] = $ship_doc->get_other_value($shp_rec['shp']['id'],'PALLETISED CARTONS');
		$shp_rec['shp']['que5'] = $ship_doc->get_other_value($shp_rec['shp']['id'],'ACCESSORIES');
		$shp_rec['shp']['ctn_num'] = $ship_doc->get_other_value($shp_rec['shp']['id'],'CONTAINER NUMBER');
		$shp_rec['shp']['seal'] = $ship_doc->get_other_value($shp_rec['shp']['id'],'SEAL NUMBER');
		$shp_rec['shp']['fnl_dept'] = $ship_doc->get_other_value($shp_rec['shp']['id'],'DEPT NUMBER');
		$shp_rec['shp']['dept_des'] = $ship_doc->get_other_value($shp_rec['shp']['id'],'DEPT DESCRIPTION');
		$shp_rec['shp']['dept_des'] = $ship_doc->get_other_value($shp_rec['shp']['id'],'DEPT DESCRIPTION');
		$shp_rec['shp']['fnl_des'] = $ship_doc->get_other_value($shp_rec['shp']['id'],'DESCRIPTION');
		$shp_rec['shp']['del_due'] = $ship_doc->get_other_value($shp_rec['shp']['id'],'DEL. DUE');
		$shp_rec['shp']['fnl_po'] = $ship_doc->get_other_value($shp_rec['shp']['id'],'TU PURCHASE ORDER');
		$shp_rec['shp']['fnl_style'] = $ship_doc->get_other_value($shp_rec['shp']['id'],'ITEM STYLE NUMBER');
		$shp_rec['shp']['fnl_name'] = $ship_doc->get_other_value($shp_rec['shp']['id'],'TU NAME');
		$shp_rec['shp']['fnl_addr'] = $ship_doc->get_other_value($shp_rec['shp']['id'],'TU ADDR');

		
include_once($config['root_dir']."/lib/class.pdf_ship_ab1.php");
session_register	('main_rec');


$print_title1  = "our pdt. code : ".$shp_rec['shp']['ord_num'];
$creator = $shp_rec['shp']['submit_user'];
$mark = $shp_rec['shp']['inv_num'];
$fty = $shp_rec['shp']['fty'];
session_register	('foot');
$foot = array('po'	=>	$shp_rec['shp']['fnl_po'],
							'inv_num'	=>	$shp_rec['shp']['inv_num'],
							'cust'		=>	$shp_rec['shp']['cust_name'],
							'ver'			=>	$shp_rec['shp']['ver'],
							);
$po = $shp_rec['shp']['fnl_po'];
$inv_num = $shp_rec['shp']['inv_num'];
$cust = $shp_rec['shp']['cust_name'];

$pdf = new PDF_shidoc_ab1('P','mm','A4');
//$pdf->SetAutoPageBreak('on',10);
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();
$pdf->base_data($shp_rec['shp']);
$x=21; // max=43
$ctn='';
$ctn_count = 0;
$pdf->item_title($shp_rec['shp']['unit']);
$shp_rpt = array();
for($i=0; $i<sizeof($shp_rec['shp_det']); $i++)
{
//	$color= explode('-',$shp_rec['shp_det'][$i]['color']);
//	if(!isset($color[1]))$color= explode('_',$shp_rec['shp_det'][$i]['color']);
//	if(!isset($color[1]))$color[1] = '-';	
	$color[1] = $shp_rec['shp_det'][$i]['item'];
	if(!isset($shp_rpt[$color[1]]))$shp_rpt[$color[1]] = 0;
	$shp_rpt[$color[1]] += ($shp_rec['shp_det'][$i]['s_qty'] * ($shp_rec['shp_det'][$i]['e_cnt'] - $shp_rec['shp_det'][$i]['s_cnt'] + 1));
	$pdf->item_txt($shp_rec['shp_det'][$i]);
	if($ctn != $shp_rec['shp_det'][$i]['cnt'])
	{
		$ctn_count += $shp_rec['shp_det'][$i]['e_cnt'] - $shp_rec['shp_det'][$i]['s_cnt'] + 1;
	}
}
$pdf->sum_title();
$ttl=0;
foreach($shp_rpt as $key => $value)
{
	$ttl += $value;
	$pdf->sum_txt($key,$value);
}
$pdf->sum_txt('Total',$ttl);

$name=$shp_rec['shp']['inv_num'].'_tu1.pdf';
$pdf->Output($name,'D');
break;	


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "pack_mn_print":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "pack_mn_print":
 	check_authority('032',"view");

		$shp_rec = $ship_doc->get_pack($PHP_id,'shipdoc_ord.cust_po, s_order.style_num, shipdoc_qty.color',1);
		
		$shp_rec['shp']['fnl_po'] = $ship_doc->get_other_value($shp_rec['shp']['id'],'TU PURCHASE ORDER');

		$cust_po = array();
		$po_mk = 0;
		$shp_rec['shp']['cust_po'] = '';
		for($i=0; $i<sizeof($shp_rec['shp_det']); $i++)
		{
			$shp_rec['shp_det'][$i]['sort'] = $shp_rec['shp_det'][$i]['cust_po'].'_'.$shp_rec['shp_det'][$i]['sort'];
			for($j=0; $j<sizeof($cust_po); $j++)
			{
				if($shp_rec['shp_det'][$i]['cust_po'] == $cust_po[$j])$po_mk = 1;
			}
			if($po_mk == 0)
			{
				$shp_rec['shp']['cust_po'] = $shp_rec['shp_det'][$i]['cust_po']."/";
				$cust_po[] = $shp_rec['shp_det'][$i]['cust_po'];
			}
			
		}
		$shp_rec['shp']['cust_po'] = substr($shp_rec['shp']['cust_po'],0,-1);
		$shp_rec['shp_det'] =  bubble_sort_s($shp_rec['shp_det']);



include_once($config['root_dir']."/lib/class.pdf_shp_pack_mn.php");
session_register	('main_rec');

$main_rec['method'] = $shp_rec['shp']['method'];
$print_title1  = "our pdt. code : ".$shp_rec['shp']['ord_num'];
$creator = $shp_rec['shp']['submit_user'];
$mark = $shp_rec['shp']['inv_num'];
$fty = $shp_rec['shp']['fty'];
session_register	('foot');
$foot = array('po'			=>	$shp_rec['shp']['cust_po'],
							'inv_num'	=>	$shp_rec['shp']['inv_num'],
							'cust'		=>	$shp_rec['shp']['cust_name'],
							'ver'			=>	$shp_rec['shp']['ver'],
							);
//$po = $shp_rec['shp']['fnl_po'];
$inv_num = $shp_rec['shp']['inv_num'];
$cust = $shp_rec['shp']['cust_name'];

$pdf = new PDF_shp_pack_mn('L','mm','A4');
//$pdf->SetAutoPageBreak('on',10);
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();
$parm =array(
							'cust_po'		=>	$shp_rec['shp']['cust_po'],
							'vendor'		=>	$shp_rec['shp']['vendor_name'],
							'carrier'		=>	$shp_rec['shp']['carrier'],
							'ship_date'	=>	$shp_rec['shp']['fv_etd']							
						 );
$pdf->hend_title($parm);
$pdf->item_title();
$x = $pdf->item_txt($shp_rec['shp_det'],$shp_rec['shp']['cbm']);
$x_area = $pdf->GetX();
$pdf->break_item($shp_rec['break'],$shp_rec['shp']['ship_mark'],$x);

$name=$shp_rec['shp']['inv_num'].'_mnp.pdf';
$pdf->Output($name,'D');
break;	


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "shipdoc_i_view":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "inv_mn_print":
 	check_authority('034',"view");

		$shp_rec = $ship_doc->get_inv($PHP_id);
//---------------------------------- 資料庫作業結束 ------------------------------

include_once($config['root_dir']."/lib/class.pdf_ship_inv_mn.php");
session_register	('main_rec');
$main_rec = array (	'fty'					=>	$shp_rec['shp']['fty'],
										'inv_num'			=>	$shp_rec['shp']['inv_num'],
										'bill_addr1'	=>	$shp_rec['shp']['bill_addr1'],
										'bill_addr2'	=>	$shp_rec['shp']['bill_addr2'],
										'ship_date'		=>	$shp_rec['shp']['fv_etd'],
										'date'				=>	$shp_rec['shp']['submit_date'],
										'ship_by'			=>	$shp_rec['shp']['ship_by'],
										'via'					=>	$shp_rec['shp']['ship_via'],
										'ship_from'		=>	$shp_rec['shp']['ship_from'],
										'ship_to'			=>	$shp_rec['shp']['ship_to'],
										'payment'			=>	$shp_rec['shp']['payment'],
										'bank'				=>	$shp_rec['shp']['lc_bank'],
										'des'					=>	$shp_rec['shp']['des'],
										'lc_no'				=>	$shp_rec['shp']['lc_no'],
										'lc_date'			=>	$shp_rec['shp']['lc_date'],										
										);

$print_title="INVOICE";

$print_title1  = "our pdt. code : ".$shp_rec['shp']['ord_num'];
$print_title2 = "VER.".($shp_rec['shp']['ver']+1);
$creator = $shp_rec['shp']['submit_user'];
$mark = $shp_rec['shp']['inv_num'];
$fty = $shp_rec['shp']['fty'];


$pdf = new PDF_shidoc_inv_mn('P','mm','A4');
//$pdf->SetAutoPageBreak('on',10);
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();

$pg_mk = 2;//TOTAL 53 ROW
$pdf->item_title($shp_rec['shp']['ship_term']);
$cust_po ='';
$con_mk =  0;
for($i=0; $i<sizeof($shp_rec['shp_det']); $i++)
{
	
	if($pg_mk > 51)
	{
		$pdf->AddPage();
		$pdf->item_title($shp_rec['shp']['ship_term']);
		$pg_mk = 2;
	}
	if($cust_po != $shp_rec['shp_det'][$i]['cust_po'] )
	{
		$cust_po = $shp_rec['shp_det'][$i]['cust_po'] ;
		$pdf->item_txt($shp_rec['shp_det'][$i],1);
		$pg_mk+=4;
	}else{
		$pdf->item_txt($shp_rec['shp_det'][$i],0);
		$pg_mk++;
	}
	
}

$pg_mk+=4;
if($pg_mk > 51)
{
	$pdf->AddPage();
	$pdf->item_title($shp_rec['shp']['ship_term']);
	$pg_mk = 2;
}
$pdf->item_total($shp_rec['amt']);
$txt = array();
	if($shp_rec['shp']['mid_no'])$txt[] = "M.I.D. NO.: ".$shp_rec['shp']['mid_no'];
	if($shp_rec['shp']['country_org'])$txt[] = "COUNTRY OF ORIGIN: ".$shp_rec['shp']['country_org'];
	$pg_mk += 2;
	if($pg_mk > 51)
	{
		$pdf->AddPage();
		$pg_mk = 0;
	}
	$pdf->inv_txt('',$txt);


if($shp_rec['shp']['others'])
{
	
	$txt = explode( chr(13).chr(10), $shp_rec['shp']['others'] );
	$pg_mk+=(sizeof($txt)+1);
	if($pg_mk > 51)
	{
		$pdf->AddPage();
		$pg_mk = 0;
	}
	$pdf->inv_txt('',$txt);
}

if($shp_rec['shp']['fty_det'])
{
	$txt = explode( chr(13).chr(10), $shp_rec['shp']['fty_det'] );
	$pg_mk+=(sizeof($txt)+3);
	if($pg_mk > 51)
	{
		$pdf->AddPage();
		$pg_mk = 0;
	}
	$pdf->inv_txt('MANUFACTURER NAME & ADDRESS :',$txt);
}






if($shp_rec['shp']['benf_det'])
{
	$txt = explode( chr(13).chr(10), $shp_rec['shp']['benf_det'] );
	$pg_mk+=(sizeof($txt)+1);
	if($pg_mk > 51)
	{
		$pdf->AddPage();
		$pg_mk = 0;
	}
	$pdf->inv_txt('BENEFICIARY ACCOUNT DETAIL AS BELOW :',$txt);
}

$pg_mk+=5;

if($pg_mk > 51)
{
	$pdf->AddPage();
	$pg_mk = 0;
}

$pdf->pack_txt($shp_rec['pack']);

if($shp_rec['shp']['ship_mark'])
{
	$txt = explode( chr(13).chr(10), $shp_rec['shp']['ship_mark'] );
	$pg_mk+=(sizeof($txt)+1);
	if($pg_mk > 51)
	{
		$pdf->AddPage();
		$pg_mk = 0;
	}
	$pdf->inv_txt('SHIPPING MARK :',$txt);
}
$name=$shp_rec['shp']['inv_num'].'_inv_mn.pdf';
$pdf->Output($name,'D');


break;	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "shipdoc_i_view":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "inv_ph_print":
 	check_authority('034',"view");

		$shp_rec = $ship_doc->get_inv($PHP_id);
//---------------------------------- 資料庫作業結束 ------------------------------

include_once($config['root_dir']."/lib/class.pdf_ship_inv_ph.php");
session_register	('main_rec');
$main_rec = array (	'fty'					=>	$shp_rec['shp']['fty'],
										'inv_num'			=>	$shp_rec['shp']['inv_num'],
										'bill_addr1'	=>	$shp_rec['shp']['bill_addr1'],
										'bill_addr2'	=>	$shp_rec['shp']['bill_addr2'],
										'ship_date'		=>	$shp_rec['shp']['fv_etd'],
										'date'				=>	$shp_rec['shp']['submit_date'],
										'ship_by'			=>	$shp_rec['shp']['ship_by'],
										'via'					=>	$shp_rec['shp']['ship_via'],
										'ship_from'		=>	$shp_rec['shp']['ship_from'],
										'ship_to'			=>	$shp_rec['shp']['ship_to'],
										'payment'			=>	$shp_rec['shp']['payment'],
										'bank'				=>	$shp_rec['shp']['lc_bank'],
										'des'					=>	$shp_rec['shp']['des'],
										'lc_no'				=>	$shp_rec['shp']['lc_no'],
										'lc_date'			=>	$shp_rec['shp']['lc_date'],										
										);

$print_title="INVOICE";

$print_title1  = "our pdt. code : ".$shp_rec['shp']['ord_num'];
$print_title2 = "VER.".($shp_rec['shp']['ver']+1);
$creator = $shp_rec['shp']['submit_user'];
$mark = $shp_rec['shp']['inv_num'];
$fty = $shp_rec['shp']['fty'];


$pdf = new PDF_shidoc_inv_ph('P','mm','A4');
//$pdf->SetAutoPageBreak('on',10);
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();

$pg_mk = 2;//TOTAL 53 ROW
$pdf->item_title($shp_rec['shp']['ship_term']);
$cust_po ='';
$con_mk =  0;
for($i=0; $i<sizeof($shp_rec['shp_det']); $i++)
{
	
	if($pg_mk > 51)
	{
		$pdf->AddPage();
		$pdf->item_title($shp_rec['shp']['ship_term']);
		$pg_mk = 2;
	}
	if($cust_po != $shp_rec['shp_det'][$i]['cust_po'] )
	{
		$cust_po = $shp_rec['shp_det'][$i]['cust_po'] ;
		$pdf->item_txt($shp_rec['shp_det'][$i],1);
		$pg_mk+=4;
	}else{
		$pdf->item_txt($shp_rec['shp_det'][$i],0);
		$pg_mk++;
	}
	
}

$pg_mk+=4;
if($pg_mk > 51)
{
	$pdf->AddPage();
	$pdf->item_title($shp_rec['shp']['ship_term']);
	$pg_mk = 2;
}
$pdf->item_total($shp_rec['amt']);
$txt = array();
	$txt[] = '';
	if($shp_rec['shp']['country_org'])$txt[] = "COUNTRY OF ORIGIN: ".$shp_rec['shp']['country_org'];
	if($shp_rec['shp']['mid_no'])$txt[] = "M.I.D. NO.: ".$shp_rec['shp']['mid_no'];

	$pg_mk += 3;
	if($pg_mk > 51)
	{
		$pdf->AddPage();
		$pg_mk = 0;
	}
	$pdf->inv_txt('',$txt);


if($shp_rec['shp']['others'])
{
	
	$txt = explode( chr(13).chr(10), $shp_rec['shp']['others'] );
	$pg_mk+=(sizeof($txt)+1);
	if($pg_mk > 51)
	{
		$pdf->AddPage();
		$pg_mk = 0;
	}
	$pdf->inv_txt('',$txt);
}

if($shp_rec['shp']['fty_det'])
{
	$txt = explode( chr(13).chr(10), $shp_rec['shp']['fty_det'] );
	$pg_mk+=(sizeof($txt)+3);
	if($pg_mk > 51)
	{
		$pdf->AddPage();
		$pg_mk = 0;
	}
	$pdf->inv_txt('MANUFACTURER NAME & ADDRESS :',$txt);
}






if($shp_rec['shp']['benf_det'])
{
	$txt = explode( chr(13).chr(10), $shp_rec['shp']['benf_det'] );
	$pg_mk+=(sizeof($txt)+1);
	if($pg_mk > 51)
	{
		$pdf->AddPage();
		$pg_mk = 0;
	}
	$pdf->inv_txt('BENEFICIARY ACCOUNT DETAIL AS BELOW :',$txt);
}

$pg_mk+=5;

if($pg_mk > 51)
{
	$pdf->AddPage();
	$pg_mk = 0;
}

$pdf->pack_txt($shp_rec['pack']);

if($shp_rec['shp']['ship_mark'])
{
	$txt = explode( chr(13).chr(10), $shp_rec['shp']['ship_mark'] );
	$pg_mk+=(sizeof($txt)+1);
	if($pg_mk > 51)
	{
		$pdf->AddPage();
		$pg_mk = 0;
	}
	$pdf->inv_txt('SHIPPING MARK :',$txt);
}
$name=$shp_rec['shp']['inv_num'].'_inv_ph.pdf';
$pdf->Output($name,'D');

break;	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "pack_db_print":			
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "pack_db_print":   //  .......
	check_authority('032',"view");	
		$shp_rec = $ship_doc->get_pack($PHP_id);

$j = $k = -1;
$x = $y =0;
$content = array();
$measure = array();

for($i=0; $i<sizeof($shp_rec['shp_det']); $i++)
{
	$con_mk = 0;
	$shp_rec['shp_det'][$i]['ord_row'] = 0;
	$shp_rec['shp_det'][$i]['color_row'] =	0;
//取得不同的訂單content	於一陣列中
	for($ct=0; $ct<sizeof($content); $ct++)
	{
		if($content[$ct] == $shp_rec['shp_det'][$i]['content']) 
		{
			$con_mk = 1;
			break;
		}		
	}
	if($con_mk == 0)$content[] = $shp_rec['shp_det'][$i]['content'];

	$ctn_size = $shp_rec['shp_det'][$i]['ctn_l']."*".$shp_rec['shp_det'][$i]['ctn_w']."*".$shp_rec['shp_det'][$i]['ctn_h'];
	$mea_mk = 0;
	for($ct=0; $ct<sizeof($measure); $ct++)
	{
		if($measure[$ct] == $ctn_size) 
		{
			$mea_mk = 1;
			break;
		}		
	}
	if($mea_mk == 0)$measure[] = $ctn_size;	
		
	

	if (isset($shp_rec['shp_det'][$i]['tb_mk']) && $shp_rec['shp_det'][$i]['tb_mk'] == 1)
	{
		$style_num = $color = '';		
	}
	
	if($style_num <> $shp_rec['shp_det'][$i]['style_num']) //整理sytle_num的顯示
	{
		$style_num = $shp_rec['shp_det'][$i]['style_num'];
		if($j > -1)
		{
			$shp_rec['shp_det'][$j]['ord_row'] = $x;
			if(strlen($shp_rec['shp_det'][$j]['style_num']) > 18 && $x > 1)
			{
				$tmp = $shp_rec['shp_det'][$j]['style_num'];
				$shp_rec['shp_det'][$j]['style_num'] = substr($tmp,0,18);
				$shp_rec['shp_det'][($j+1)]['style_num'] = substr($tmp,18);
			}
		}
		$x = 0;
		$j = $i;
		$color = '';
	}else{
		$shp_rec['shp_det'][$i]['style_num'] = '';
	}	
	
	if($color <> $shp_rec['shp_det'][$i]['color']) //整理color的顯示
	{
		$color = $shp_rec['shp_det'][$i]['color'];
		if($k > -1)
		{
			$shp_rec['shp_det'][$k]['color_row'] = $y;
			if(strlen($shp_rec['shp_det'][$k]['color']) > 18 && $y > 1)
			{
				$tmp = $shp_rec['shp_det'][$k]['color'];
				$shp_rec['shp_det'][$k]['color'] = substr($tmp,0,18);
				$shp_rec['shp_det'][($k+1)]['color'] = substr($tmp,18);
			}
		}
		$y = 0;
		$k = $i;
	}else{
		$shp_rec['shp_det'][$i]['color'] = '';	
	}	
	$x++;
	$y++;	

}
if($j > -1)
{

	$shp_rec['shp_det'][$j]['ord_row'] = $x;
	if(strlen($shp_rec['shp_det'][$j]['style_num']) > 18 && $x > 1)
	{
		$tmp = $shp_rec['shp_det'][$j]['style_num'];
		$shp_rec['shp_det'][$j]['style_num'] = substr($tmp,0,18);
		$shp_rec['shp_det'][($j+1)]['style_num'] = substr($tmp,18);

		
	}
}
if($k > -1)
{
	$shp_rec['shp_det'][$k]['color_row'] = $x;
	if(strlen($shp_rec['shp_det'][$k]['color']) > 18 && $y > 1)
	{
		$tmp = $shp_rec['shp_det'][$k]['color'];
		$shp_rec['shp_det'][$k]['color'] = substr($tmp,0,18);
		$shp_rec['shp_det'][($k+1)]['color'] = substr($tmp,18);
	}
}

//將不同的content轉成字串
$shp_rec['shp']['ord_content'] ='';
for($i=0; $i< sizeof($content); $i++)
{
	$shp_rec['shp']['ord_content'].=$content[$i]."/";
}
$shp_rec['shp']['ord_content'] = substr($shp_rec['shp']['ord_content'],0,-1);

//---------------------------------- 資料庫作業結束 ------------------------------

include_once($config['root_dir']."/lib/class.pdf_shp_pack_db.php");
session_register	('main_rec');
$main_rec = array (	'fty'							=>	$shp_rec['shp']['fty'],
										'inv_num'					=>	$shp_rec['shp']['inv_num'],
										'des'							=>	$shp_rec['shp']['des'],
										'content'					=>	$shp_rec['shp']['ord_content'],
										'consignee'				=>	$shp_rec['shp']['consignee_name'],
										'consignee_addr'	=>	$shp_rec['shp']['consignee_addr'],
										'date'						=>	$shp_rec['shp']['submit_date'],
										'lc_no'						=>	$shp_rec['shp']['lc_no'],
										'lc_date'					=>	$shp_rec['shp']['lc_date'],
										'pack'						=>	$shp_rec['shp']['pack'],										
										);

$print_title="DETAILED PACKING LIST";

$print_title1  = "our pdt. code : ".$shp_rec['shp']['ord_num'];
$print_title2 = "VER.".($shp_rec['shp']['ver']+1);
$creator = $shp_rec['shp']['submit_user'];
$mark = $shp_rec['shp']['inv_num'];
$fty = $shp_rec['shp']['fty'];


$pdf = new PDF_shp_pack_db('L','mm','A4');
//$pdf->SetAutoPageBreak('on',10);
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();

$gd_mk = 0;
$pg_mk = 0;
$style = $color = '';
for($i=0; $i<sizeof($shp_rec['shp_det']); $i++) //列印packing list
{
//	if($i > 0) $pg_mk++;
	if (isset($shp_rec['shp_det'][$i]['tb_mk']) && $shp_rec['shp_det'][$i]['tb_mk'] == 1) $pg_mk+=3;
	if(strlen($shp_rec['shp_det'][$i]['color']) > 10 || strlen($shp_rec['shp_det'][$i]['style_num']) > 10)
	{
		$pg_mk+=2;
	}else{
		$pg_mk ++;
	}
		
	if($pg_mk > 31) //換頁
	{
		$pdf->AddPage();
		if(!(isset($shp_rec['shp_det'][$i]['tb_mk']) && $shp_rec['shp_det'][$i]['tb_mk'] == 1))
		{
			$pdf->ship_title($shp_rec['shp_det'][$i]['size'],$shp_rec['shp']['unit']);
			$pg_mk = 3;
		}
		$pg_mk = 0;
	}	

	if($i == 0) //初始化總計
	{

		$gd_tal = array();
		for($j=0; $j<sizeof($shp_rec['shp_det'][$i]['size_qty']); $j++) $gd_tal['size'][] = 0;
		$gd_tal['gw'] =$gd_tal['nw'] =$gd_tal['nnw'] = $gd_tal['qty'] = $gd_tal['ctn'] = 0;
	}
	
	if($style <> $shp_rec['shp_det'][$i]['style_num'] && $shp_rec['shp_det'][$i]['style_num'] <> ''  && $color <> $shp_rec['shp_det'][$i]['color'] && $shp_rec['shp_det'][$i]['color'] <> '')
	{
		$style = $shp_rec['shp_det'][$i]['style_num'];
		$color = $shp_rec['shp_det'][$i]['color'];
		if($i > 0)
		{
//			$pdf->sub_total($sub_tal); //小計列印
//			$pg_mk++;
		}		
		$sub_tal = array(); //小計初始化
		for($j=0; $j<sizeof($shp_rec['shp_det'][$i]['size_qty']); $j++) $sub_tal['size'][] = 0;
		$sub_tal['gw'] =$sub_tal['nw'] =$sub_tal['nnw'] = $sub_tal['qty'] = $sub_tal['ctn'] = 0;
	}

	if (isset($shp_rec['shp_det'][$i]['tb_mk']) && $shp_rec['shp_det'][$i]['tb_mk'] == 1)
	{
		$pdf->ship_title($shp_rec['shp_det'][$i]['size'],$shp_rec['shp']['unit']); //packing list表頭
//		$pg_mk+=3;
//		if($i > 0)$pdf->sub_total($sub_tal);
		$sub_tal = array(); //小計初始化
		for($j=0; $j<sizeof($shp_rec['shp_det'][$i]['size_qty']); $j++) $sub_tal['size'][] = 0;
		$sub_tal['gw'] =$sub_tal['nw'] =$sub_tal['nnw'] = $sub_tal['qty'] = $sub_tal['ctn'] = 0;
		$style = $color = '';
		$gd_mk++;
	}

	
	if(strlen($shp_rec['shp_det'][$i]['color']) > 10 || strlen($shp_rec['shp_det'][$i]['style_num']) > 10)
	{
		$pdf->ship_txt_mix($shp_rec['shp_det'][$i],$shp_rec['shp']['inner_box']); //color or style > 18 works, print 二行
//		$pg_mk+=2;
	}else{

		$pdf->ship_txt($shp_rec['shp_det'][$i],$shp_rec['shp']['inner_box']); //print packing list
//		$pg_mk++;
	}
	
	//小計
	$ctn = explode('-',$shp_rec['shp_det'][$i]['cnt']);
	for($j=0; $j<sizeof($shp_rec['shp_det'][$i]['size_qty']); $j++) 
	{
		if($shp_rec['shp']['inner_box'] == 0)$shp_rec['shp_det'][$i]['size_box'][$j] = 1;
		$sub_tal['size'][$j] += $shp_rec['shp_det'][$i]['size_qty'][$j] * $shp_rec['shp_det'][$i]['size_box'][$j] * ($ctn[1] - $ctn[0] + 1);		
	}
	$sub_tal['ctn'] += $shp_rec['shp_det'][$i]['cnum'];
	$sub_tal['qty'] += $shp_rec['shp_det'][$i]['qty'] * ($ctn[1] - $ctn[0] + 1);
	$sub_tal['nnw'] += $shp_rec['shp_det'][$i]['nnw'];
	$sub_tal['nw'] += $shp_rec['shp_det'][$i]['nw'];
	$sub_tal['gw'] += $shp_rec['shp_det'][$i]['gw'];

	//總計
	$ctn = explode('-',$shp_rec['shp_det'][$i]['cnt']);
	if($gd_mk < 2) for($j=0; $j<sizeof($shp_rec['shp_det'][$i]['size_qty']); $j++) $gd_tal['size'][$j] += $shp_rec['shp_det'][$i]['size_qty'][$j] * ($ctn[1] - $ctn[0] + 1);
	$gd_tal['ctn'] += $shp_rec['shp_det'][$i]['cnum'];
	$gd_tal['qty'] += $shp_rec['shp_det'][$i]['qty'] * ($ctn[1] - $ctn[0] + 1);
	$gd_tal['nnw'] += $shp_rec['shp_det'][$i]['nnw'];
	$gd_tal['nw'] += $shp_rec['shp_det'][$i]['nw'];
	$gd_tal['gw'] += $shp_rec['shp_det'][$i]['gw'];

}
if($gd_mk > 1)//如果size breakdown有一種以上時,總計不要計算size breakdown
{
	$gd_tal['size'] = array();
	for($j=0; $j<sizeof($shp_rec['shp_det'][($i-1)]['size_qty']); $j++) $gd_tal['size'][$j] = '';
}
//小計+總計
$pg_mk+=1;
if($pg_mk > 31)
{
	$pdf->AddPage();
	$pdf->ship_title($shp_rec['shp_det'][($i-1)]['size'],$shp_rec['shp']['unit']);
	$pg_mk = 0;
}

//$pdf->sub_total($sub_tal);
$pdf->gd_total($gd_tal);


//DESCRIPTION OF MERCHANDISE
	$pg_mk+=3;
	if($pg_mk > 31) //換頁
	{
		$pdf->AddPage();
		$pg_mk = 0;
	}	
$pdf->SetFont('Arial','',10);	
$yy = $pdf->GetY();
$pdf->SetY(($yy+3));
$pdf->cell(270,5,'DESCRIPTION OF MERCHANDISE : '.$shp_rec['shp']['pack']);
$pdf->ln();


//COLOR/SIZE BREAKDOWN 
if($pg_mk > 31)
{
	$pdf->AddPage();
	$pg_mk = 0;
}
$pdf->ln();

$pg_mk = $pg_mk+3+sizeof($measure);
if($pg_mk > 31)
{
	$pdf->AddPage();
	$pg_mk = 0;
}
$yy = $pdf->GetY();
$pdf->SetY(($yy+5));
$pdf->end_ship($gd_tal,$measure,$shp_rec['shp']['fty']); //匯總資料


$pdf->SetFont('Arial','B',10);
$pdf->SetX(8);
$pdf->cell(280,7,'COLOR/SIZE BREAKDOWN :',0,0,'L');
$pg_mk+=2;
$pdf->ln();

for($i=0; $i<sizeof($shp_rec['break']); $i++)
{
	if($pg_mk > 29)
	{
		$pdf->AddPage();
		if(!(isset($shp_rec['break'][$i]['tb_mk']) && ($shp_rec['break'][$i]['tb_mk'] == 1 || $shp_rec['break'][$i]['tb_mk'] == 2 || $shp_rec['break'][$i]['tb_mk'] == 5)))
			$pdf->break_title($shp_rec['break'][$i]['size']);// COLOR/SIZE BREAKDOWN title
		$pg_mk = 1;
	}

	if(isset($shp_rec['break'][$i]['tb_mk']) && ($shp_rec['break'][$i]['tb_mk'] == 1 ))
	{
		$pdf->break_total($shp_rec['break'][$i]);// COLOR/SIZE BREAKDOWN小計
		$pg_mk++;		
	}

	if(isset($shp_rec['break'][$i]['tb_mk']) && ($shp_rec['break'][$i]['tb_mk'] == 1 || $shp_rec['break'][$i]['tb_mk'] == 2 || $shp_rec['break'][$i]['tb_mk'] == 5))
	{
		$pdf->break_title($shp_rec['break'][$i]['size']);// COLOR/SIZE BREAKDOWN title
		$pg_mk++;
		
	}
	$pdf->break_txt($shp_rec['break'][$i]);// COLOR/SIZE BREAKDOWN 細項
	$pg_mk++;
	if(isset($shp_rec['break'][$i]['tb_mk']) && ( $shp_rec['break'][$i]['tb_mk'] == 3 || $shp_rec['break'][$i]['tb_mk'] == 5))
	{
		$pdf->break_total($shp_rec['break'][$i]);// COLOR/SIZE BREAKDOWN小計
		$pg_mk++;
	}	
}



$name=$shp_rec['shp']['inv_num'].'_pack_db.pdf';
$pdf->Output($name,'D');


break;	


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "shipdoc_i_view":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "inv_db_print":
 	check_authority('034',"view");

		$shp_rec = $ship_doc->get_inv($PHP_id,"s_order.style_num");

//---------------------------------- 資料庫作業結束 ------------------------------

include_once($config['root_dir']."/lib/class.pdf_ship_inv_db.php");
session_register	('main_rec');
$main_rec = array (	'fty'							=>	$shp_rec['shp']['fty'],
										'inv_num'					=>	$shp_rec['shp']['inv_num'],
										'date'						=>	$shp_rec['shp']['submit_date'],
										'consignee_name'	=>	$shp_rec['shp']['consignee_name'],
										'consignee_addr'	=>	$shp_rec['shp']['consignee_addr'],
										'ship_from'				=>	$shp_rec['shp']['ship_from'],
										'ship_to'					=>	$shp_rec['shp']['ship_to'],
										'payment'					=>	$shp_rec['shp']['payment'],
										'lc_no'						=>	$shp_rec['shp']['lc_no'],
										'lc_date'					=>	$shp_rec['shp']['lc_date'],
										'bank'						=>	$shp_rec['shp']['lc_bank'],							
										'fv'							=>	$shp_rec['shp']['fv'],							
										'fv_etd'					=>	$shp_rec['shp']['fv_etd'],							
										'fv_eta'					=>	$shp_rec['shp']['fv_eta'],							
										
										'ship_date'		=>	$shp_rec['shp']['fv_etd'],
										'ship_by'			=>	$shp_rec['shp']['ship_by'],
										'via'					=>	$shp_rec['shp']['ship_via'],
										);

$print_title="COMMERCIAL INVOICE";

$print_title1  = "our pdt. code : ".$shp_rec['shp']['ord_num'];
$print_title2 = "VER.".($shp_rec['shp']['ver']+1);
$creator = $shp_rec['shp']['submit_user'];
$mark = $shp_rec['shp']['inv_num'];
$fty = $shp_rec['shp']['fty'];


$pdf = new PDF_shidoc_inv_db('P','mm','A4');
//$pdf->SetAutoPageBreak('on',10);
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();






$pg_mk = 5;//TOTAL 35 ROW
$item_parm = array('ship_term'	=>	$shp_rec['shp']['ship_term'],
									 'des'				=>	$shp_rec['shp']['des'],
									 'fabric'			=>	$shp_rec['shp']['fab_des'],
									 'fty'				=>	$shp_rec['shp']['fty'],
									 );
$pdf->item_title($item_parm);
$style_num = '';
$ship_mk = 0;



for($i=0; $i<sizeof($shp_rec['shp_det']); $i++)
{
	$style_mk =0;
	if($style_num != $shp_rec['shp_det'][$i]['style_num'])
	{
		$style_mk = 1;
		$style_num = $shp_rec['shp_det'][$i]['style_num'];
		$pg_mk+=3;
	}
	$pg_mk+=1;

	if($pg_mk > 34)
	{
		$pdf->item_button();
		$pdf->AddPage();		
		$pdf->item_title($item_parm);
		$pg_mk = 5;
	}
	$ship_mk = $pdf->item_txt($shp_rec['shp_det'][$i],$shp_rec['shp']['fty'],$style_mk,$ship_mk);
	
}

$pg_mk+=7;
if($pg_mk > 34)
{
	$pdf->item_button();
	$pdf->AddPage();
	$pdf->item_title($item_parm);
	$pg_mk = 5;
}
$pdf->item_wt($shp_rec['pack']);


$pg_mk+=6;
if($pg_mk > 34)
{
	$pdf->item_button();
	$pdf->AddPage();
	$pdf->item_title($item_parm);
	$pg_mk = 5;
}
$pdf->item_total($shp_rec['amt'],$shp_rec['shp']['country_org'],$shp_rec['pack']['ctn']);


if($shp_rec['shp']['others'])
{
	
	$txt = explode( chr(13).chr(10), $shp_rec['shp']['others'] );
	$pg_mk+=(sizeof($txt)+1);
	if($pg_mk > 34)
	{
		$pdf->AddPage();
		$pg_mk = 0;
	}
	$pdf->inv_txt('',$txt,7);
}
if($shp_rec['shp']['fty_det'])
{
	$txt = explode( chr(13).chr(10), $shp_rec['shp']['fty_det'] );
	if($shp_rec['shp']['mid_no'])$txt[] = "M.I.D. NO.: ".$shp_rec['shp']['mid_no'];
//	if($shp_rec['shp']['country_org'])$txt[] = "COUNTRY OF ORIGIN: ".$shp_rec['shp']['country_org'];
	$pg_mk+=(sizeof($txt)+2);
	if($pg_mk > 34)
	{
		$pdf->AddPage();
		$pg_mk = 0;
	}
	$pdf->inv_txt('MANUFACTURER NAME & ADDRESS :',$txt);
}

if($shp_rec['shp']['ship_det'])
{
	$txt = explode( chr(13).chr(10), $shp_rec['shp']['ship_det'] );
	$pg_mk+=(sizeof($txt)+1);
	if($pg_mk > 34)
	{
		$pdf->AddPage();
		$pg_mk = 0;
	}
	$pdf->inv_txt('SHIPPERS ADDRESS : ',$txt);
}


if($shp_rec['shp']['benf_det'])
{
	$txt = explode( chr(13).chr(10), $shp_rec['shp']['benf_det'] );
	$pg_mk+=(sizeof($txt)+1);
	if($pg_mk > 34)
	{
		$pdf->AddPage();
		$pg_mk = 0;
	}
	$pdf->inv_txt('BENEFICIARY ACCOUNT DETAIL AS BELOW :',$txt);
}


if($shp_rec['shp']['ship_mark'])
{
	$txt = explode( chr(13).chr(10), $shp_rec['shp']['ship_mark'] );
	$pg_mk+=(sizeof($txt)+1);
	if($pg_mk > 34)
	{
		$pdf->AddPage();
		$pg_mk = 0;
	}
	$pdf->inv_txt('SHIPPING MARK :',$txt);
}
$name=$shp_rec['shp']['inv_num'].'_db_inv.pdf';
$pdf->Output($name,'D');


break;	


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "shipdoc_i_view":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "inv_jg_print":
 	check_authority('034',"view");

		$shp_rec = $ship_doc->get_inv($PHP_id);

//---------------------------------- 資料庫作業結束 ------------------------------

include_once($config['root_dir']."/lib/class.pdf_ship_inv_jg.php");
session_register	('main_rec');


$print_title="COMMERCIAL INVOICE";

$inv = explode('-',$shp_rec['shp']['inv_num']);
$print_title1  = "our pdt. code : ".$shp_rec['shp']['ord_num'];
$print_title2 = $inv[0]."  V.".($shp_rec['shp']['ver']+1);
$creator = $shp_rec['shp']['submit_user'];
$mark = $shp_rec['shp']['inv_num'];
$fty = $shp_rec['shp']['fty'];


$pdf = new PDF_shidoc_inv_jg('P','mm','A4');
//$pdf->SetAutoPageBreak('on',10);
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();
$pdf->hend_title($shp_rec['shp']);

$pg_mk = 18;//TOTAL 53 ROW

$pdf->SetX(5);
$pdf->Cell(203, 4, 'Description:','TLR',1,'L',0);

for($i=0; $i<sizeof($shp_rec['shp_det']); $i++)
{
	if(isset($shp_rec['shp_det'][$i]['tb_mk']) && $shp_rec['shp_det'][$i]['tb_mk'] == 1)
	{
		$pdf->SetX(5);
		$pdf->Cell(83, 4, $shp_rec['shp_det'][$i]['cust_po'],'L',0,'L',0);		
		$pdf->Cell(120, 4, $shp_rec['shp_det'][$i]['content'],'R',1,'L',0);		
		$pg_mk++;
	}	
}
$pdf->SetX(5);
$pdf->Cell(203, 1, '','BLR',1,'L',0);


$pg_mk +=2;
$pdf->item_title();
for($i=0; $i<sizeof($shp_rec['shp_det']); $i++)
{
	$pg_mk++;
	if($pg_mk > 51)
	{
		$pdf->AddPage();
		$pdf->item_title();
		$pg_mk = 2;
	}
	$pdf->item_txt($shp_rec['shp_det'][$i]);
	
}

$pg_mk+=2;
if($pg_mk > 51)
{
	$pdf->AddPage();
	$pdf->item_title();
	$pg_mk = 2;
}
$pdf->item_total($shp_rec['amt']);


if($shp_rec['shp']['fty_det'])
{
//	if($shp_rec['shp']['mid_no'])$txt[] = "MANUFACTURER NAME & ADDRESS : ".$shp_rec['shp']['mid_no'];
	$txt = explode( chr(13).chr(10), $shp_rec['shp']['fty_det'] );
	
	$pg_mk+=(sizeof($txt)+1);
	if($pg_mk > 51)
	{
		$pdf->AddPage();
		$pg_mk = 0;
	}
	$pdf->inv_txt("MANUFACTURER NAME & ADDRESS : ".$shp_rec['shp']['mid_no'],$txt);
}


if($shp_rec['shp']['stmt'])
{
	$txt = explode( chr(13).chr(10), $shp_rec['shp']['stmt'] );
	$pg_mk+=(sizeof($txt)+1);
	if($pg_mk > 51)
	{
		$pdf->AddPage();
		$pg_mk = 0;
	}
	$pdf->inv_txt('Statments :',$txt);
}


$name=$shp_rec['shp']['inv_num'].'_inv.pdf';
$pdf->Output($name,'D');


break;	




#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "pack_jg_print":			
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "pack_jg_print":   //  .......
		check_authority('032',"view");	
		$shp_rec = $ship_doc->get_pack($PHP_id);

//---------------------------------- 資料庫作業結束 ------------------------------

include_once($config['root_dir']."/lib/class.pdf_shp_pack_jg1.php");

session_register	('main_rec');


$print_title="DETAILED PACKING LIST";

$print_title1  = "our pdt. code : ".$shp_rec['shp']['ord_num'];
$print_title2 = $shp_rec['shp']['inv_num']."       VER.".($shp_rec['shp']['ver']+1);
$creator = $shp_rec['shp']['submit_user'];
$mark = $shp_rec['shp']['inv_num'];
$fty = $shp_rec['shp']['fty'];


$pdf = new PDF_shp_pack_jg1();
$pdf->FPDF('L','mm','A4');
//$pdf->SetAutoPageBreak('on',10);
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();

$pdf->main_txt("Agent",$PACK_COVER[0],"Manufacturer Name & Address(Origin Factory)",$PACK_COVER[$shp_rec['shp']['fty']]);
$pdf->main_txt("Seller Name & Address(Vendor)",$PACK_COVER['CA'],'Invoicing Party',$PACK_COVER['CA']);
$container = $consolidator = $PACK_COVER[0];
if($shp_rec['shp']['conter'])$container = $CONTAINER[$shp_rec['shp']['conter']];
if($shp_rec['shp']['constor'])$consolidator = $CONTAINER[$shp_rec['shp']['constor']];
$pdf->main_txt("Container Stuffing Location",$container,'Consolidator Name & Address(Stuffer)',$consolidator);


$name=$shp_rec['shp']['inv_num'].'_pack.pdf';
$pdf->Output($name,'D');


break;	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "pack_jg_print":			
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "pack_jg2_print":   //  .......
		check_authority('032',"view");	
		$shp_rec = $ship_doc->get_inv($PHP_id);

//---------------------------------- 資料庫作業結束 ------------------------------

include_once($config['root_dir']."/lib/class.pdf_ship_pack_jg2.php");

session_register	('main_rec');


$print_title="DETAILED PACKING LIST";

$print_title1  = "our pdt. code : ".$shp_rec['shp']['ord_num'];
$print_title2 = $shp_rec['shp']['inv_num']."       VER.".($shp_rec['shp']['ver']+1);
$creator = $shp_rec['shp']['submit_user'];
$mark = $shp_rec['shp']['inv_num'];
$fty = $shp_rec['shp']['fty'];


$pdf = new PDF_shp_pack_jg2();
$pdf->FPDF('P','mm','A4');
//$pdf->SetAutoPageBreak('on',10);
$pdf->AddBig5Font();
$pdf->Open();
$pdf->SetMargins(3,5);
$pdf->AddPage();

$pdf->hend_title($shp_rec['shp']);

$i=0;
		$ord_rec 	= array('item'		=>	'',
											'po'			=>	'',
											'gw'			=>	'',
											'nw'			=>	'',
											'color'		=>	'',
											'compt'		=>	'',
											'mat_ft'	=>	'',
											'ship_ft'	=>	'',
											'qty'			=>	'',
											'fob'			=>	'',
											'fob_co'	=>	'',
											'hanger'	=>	'',
											'belt'		=>	'',
											'belt_co'	=>	'',
											);

for($j=0; $j<2; $j++)
{
	if(isset($shp_rec['shp_det'][$i]))
	{
		$ord_rec1 = array('item'		=>	$shp_rec['shp_det'][$i]['item'],
											'po'			=>	$shp_rec['shp_det'][$i]['cust_po'],
											'gw'			=>	$shp_rec['shp_det'][$i]['gw'],
											'nw'			=>	$shp_rec['shp_det'][$i]['nw']." KGS",
											'color'		=>	$shp_rec['shp_det'][$i]['color'],
											'compt'		=>	$shp_rec['shp_det'][$i]['compt'],
											'mat_ft'	=>	$shp_rec['shp_det'][$i]['mat_ft'],
											'ship_ft'	=>	$shp_rec['shp_det'][$i]['ship_ft'],
											'qty'			=>	$shp_rec['shp_det'][$i]['bk_qty']." PCS",
											'fob'			=>	"$".$shp_rec['shp_det'][$i]['ship_fob'],
											'fob_co'	=>	$shp_rec['shp']['country_org'],
											'hanger'	=>	"$".$shp_rec['shp_det'][$i]['hanger'],
											'belt'		=>	"$".$shp_rec['shp_det'][$i]['belt'],
											'belt_co'	=>	$shp_rec['shp_det'][$i]['belt_co'],
											);
	}else{
		$ord_rec1 = $ord_rec;	
	}
	$i++;
	if(isset($shp_rec['shp_det'][$i]))
	{
		$ord_rec2 = array('item'		=>	$shp_rec['shp_det'][$i]['item'],
											'po'			=>	$shp_rec['shp_det'][$i]['cust_po'],
											'gw'			=>	$shp_rec['shp_det'][$i]['gw'],
											'nw'			=>	$shp_rec['shp_det'][$i]['nw']." KGS",
											'color'		=>	$shp_rec['shp_det'][$i]['color'],
											'compt'		=>	$shp_rec['shp_det'][$i]['compt'],
											'mat_ft'	=>	$shp_rec['shp_det'][$i]['mat_ft'],
											'ship_ft'	=>	$shp_rec['shp_det'][$i]['ship_ft'],
											'qty'			=>	$shp_rec['shp_det'][$i]['bk_qty']." PCS",
											'fob'			=>	"$".$shp_rec['shp_det'][$i]['ship_fob'],
											'fob_co'	=>	$shp_rec['shp']['country_org'],
											'hanger'	=>	"$".$shp_rec['shp_det'][$i]['hanger'],
											'belt'		=>	"$".$shp_rec['shp_det'][$i]['belt'],
											'belt_co'	=>	$shp_rec['shp_det'][$i]['belt_co'],
											);
	}else{
		$ord_rec2 = $ord_rec;	
	}	
	
	$pdf->ord_rec($ord_rec1,$ord_rec2);	
	$i++;
}

while($i<sizeof($shp_rec['shp_det']))
{
	$pdf->AddPage();
	for($j=0; $j<3;  $j++)
	{
		if(isset($shp_rec['shp_det'][$i]))
		{
			$ord_rec1 = array('item'		=>	$shp_rec['shp_det'][$i]['item'],
												'po'			=>	$shp_rec['shp_det'][$i]['cust_po'],
												'gw'			=>	$shp_rec['shp_det'][$i]['gw'],
												'nw'			=>	$shp_rec['shp_det'][$i]['nw']." KGS",
												'color'		=>	$shp_rec['shp_det'][$i]['color'],
												'compt'		=>	$shp_rec['shp_det'][$i]['compt'],
												'mat_ft'	=>	$shp_rec['shp_det'][$i]['mat_ft'],
												'ship_ft'	=>	$shp_rec['shp_det'][$i]['ship_ft'],
												'qty'			=>	$shp_rec['shp_det'][$i]['bk_qty']." PCS",
												'fob'			=>	"$".$shp_rec['shp_det'][$i]['ship_fob'],
												'fob_co'	=>	$shp_rec['shp']['country_org'],
												'hanger'	=>	"$".$shp_rec['shp_det'][$i]['hanger'],
												'belt'		=>	"$".$shp_rec['shp_det'][$i]['belt'],
												'belt_co'	=>	$shp_rec['shp_det'][$i]['belt_co'],
											);
		}else{
			$ord_rec1 = $ord_rec;	
		}
		$i++;
		if(isset($shp_rec['shp_det'][$i]))
		{
			$ord_rec2 = array('item'		=>	$shp_rec['shp_det'][$i]['item'],
												'po'			=>	$shp_rec['shp_det'][$i]['cust_po'],
												'gw'			=>	$shp_rec['shp_det'][$i]['gw'],
												'nw'			=>	$shp_rec['shp_det'][$i]['nw']." KGS",
												'color'		=>	$shp_rec['shp_det'][$i]['color'],
												'compt'		=>	$shp_rec['shp_det'][$i]['compt'],
												'mat_ft'	=>	$shp_rec['shp_det'][$i]['mat_ft'],
												'ship_ft'	=>	$shp_rec['shp_det'][$i]['ship_ft'],
												'qty'			=>	$shp_rec['shp_det'][$i]['bk_qty']." PCS",
												'fob'			=>	"$".$shp_rec['shp_det'][$i]['ship_fob'],
												'fob_co'	=>	$shp_rec['shp']['country_org'],
												'hanger'	=>	"$".$shp_rec['shp_det'][$i]['hanger'],
												'belt'		=>	"$".$shp_rec['shp_det'][$i]['belt'],
												'belt_co'	=>	$shp_rec['shp_det'][$i]['belt_co'],
												);
		}else{
			$ord_rec2 = $ord_rec;	
		}	
	
		$pdf->ord_rec($ord_rec1,$ord_rec2,$j);	
		$i++;
	}

}

$name=$shp_rec['shp']['inv_num'].'_pack_jgord.pdf';
$pdf->Output($name,'D');


break;	


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "pack_jg_print":			
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "pack_jg3_print":   //  .......
		check_authority('032',"view");	
		$shp_rec = $ship_doc->get_pack($PHP_id,' size_des.size, s_order.cust_po, shipdoc_qty.cnt',1);
		for($i=0; $i<sizeof($shp_rec['shp_det']); $i++)$shp_rec['shp_det'][$i]['sort'] = $shp_rec['shp_det'][$i]['cust_po']."_".$shp_rec['shp_det'][$i]['cnt'];
		$shp_rec['shp_det'] =  bubble_sort_s($shp_rec['shp_det']);
//---------------------------------- 資料庫作業結束 ------------------------------

include_once($config['root_dir']."/lib/class.pdf_shp_pack_jg3.php");

session_register	('main_rec');
$main_rec = array('inv_num'	=>	$shp_rec['shp']['inv_num'],
									'po_type'	=>	$shp_rec['shp']['po_type'],
									'date'		=>	$shp_rec['shp']['submit_date'],
									'method'	=>	$shp_rec['shp']['method'],
									'unit'		=>	$shp_rec['shp']['unit'],
									);


$print_title="DETAILED PACKING LIST";

$print_title1  = "our pdt. code : ".$shp_rec['shp']['ord_num'];
$print_title2 = $shp_rec['shp']['inv_num']."       VER.".($shp_rec['shp']['ver']+1);
$creator = $shp_rec['shp']['submit_user'];
$mark = $shp_rec['shp']['inv_num'];
$fty = $shp_rec['shp']['fty'];


$pdf = new  PDF_shp_pack_jg3('L','mm','A4');
$pdf->AddBig5Font();
$pdf->SetMargins(3,5);
$pdf->Open();
$l_mk = 0;
$size_des = array();
for($i=0; $i<sizeof($shp_rec['shp_det']); $i++)
{
	if(isset($shp_rec['shp_det'][$i]['tb_mk']) && $shp_rec['shp_det'][$i]['tb_mk'] == 1)
	{
		if($l_mk == 1)$pdf->ship_txt_end($size_des);
		$main_rec['size'] = $shp_rec['shp_det'][$i]['size'];
		$pdf->AddPage();
	}
	$pdf->ship_txt($shp_rec['shp_det'][$i],$shp_rec['shp']['inner_box']);
	$size_des = $shp_rec['shp_det'][$i]['size'];
	$l_mk = 1;
}
//echo $size_des;
$pdf->ship_txt_end($size_des);


/*
$pdf->main_txt("Agent",$PACK_COVER[0],"Manufacturer Name & Address(Origin Factory)",$PACK_COVER[$shp_rec['shp']['fty']]);
$pdf->main_txt("Seller Name & Address(Vendor)",$PACK_COVER['CA'],'Invoicing Party',$PACK_COVER['CA']);
$container = $consolidator = $PACK_COVER[0];
if($shp_rec['shp']['conter'])$container = $CONTAINER[$shp_rec['shp']['conter']];
if($shp_rec['shp']['constor'])$consolidator = $CONTAINER[$shp_rec['shp']['constor']];
$pdf->main_txt("Container Stuffing Location",$container,'Consolidator Name & Address(Stuffer)',$consolidator);
*/

$name=$shp_rec['shp']['inv_num'].'_pack.pdf';
$pdf->Output($name,'D');


break;	


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "pack_ct_print":			
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "pack_ct_print":   //  .......
	check_authority('032',"view");	
		$sort = "s_order.cust_po, shipdoc_qty.cnt, shipdoc_qty.f_mk";
		$shp_rec = $ship_doc->get_pack($PHP_id,$sort);
		$ord_mk = '';
		for($i=0; $i<sizeof($shp_rec['shp_det']); $i++)
		{
			
			if(!isset($style[$shp_rec['shp_det'][$i]['cust_po']] ) )$style[$shp_rec['shp_det'][$i]['cust_po']] = '';
			if(!isset($content[$shp_rec['shp_det'][$i]['cust_po']] ) )$content[$shp_rec['shp_det'][$i]['cust_po']] = '';
			if(!isset($c_id[$shp_rec['shp_det'][$i]['cnt']] ) )$c_id[$shp_rec['shp_det'][$i]['cnt']] = '';

			if(!strstr($style[$shp_rec['shp_det'][$i]['cust_po']],$shp_rec['shp_det'][$i]['style_num']))$style[$shp_rec['shp_det'][$i]['cust_po']] .= $shp_rec['shp_det'][$i]['style_num'].'/';
			if($shp_rec['shp_det'][$i]['content'] && !strstr($content[$shp_rec['shp_det'][$i]['cust_po']],$shp_rec['shp_det'][$i]['content']))$content[$shp_rec['shp_det'][$i]['cust_po']] .= $shp_rec['shp_det'][$i]['content'].'|';
			if($shp_rec['shp_det'][$i]['ctn_id'] && !strstr($c_id[$shp_rec['shp_det'][$i]['cnt']],$shp_rec['shp_det'][$i]['ctn_id']))$c_id[$shp_rec['shp_det'][$i]['cnt']] .= $shp_rec['shp_det'][$i]['ctn_id'].'|';
		
			if($ord_mk != $shp_rec['shp_det'][$i]['style_num'])
			{
				$ord_mk = $shp_rec['shp_det'][$i]['style_num'];
				$ord_rec[] = $ship_doc->get_order($shp_rec['shp_det'][$i]['ord_id']);
			}
			
		}


//---------------------------------- 資料庫作業結束 ------------------------------

include_once($config['root_dir']."/lib/class.pdf_shp_pack_ct.php");
session_register	('main_rec');
$main_rec = array (	'fty'				=>	$shp_rec['shp']['fty'],
										'inv_num'		=>	$shp_rec['shp']['inv_num'],
										'inv_date'	=>	$shp_rec['shp']['submit_date'],
										'pack'			=>	$shp_rec['shp']['pack'],
										'ship_to1'	=>	$shp_rec['shp']['ship_addr1'],
										'ship_addr'	=>	$shp_rec['shp']['ship_addr2'],
										'ship_via'	=>	$shp_rec['shp']['ship_via'],
										'ship_from'	=>	$shp_rec['shp']['ship_from'],
										'ship_to'		=>	$shp_rec['shp']['ship_to'],
										'date'			=>	$shp_rec['shp']['fv_etd'],
										);

$print_title="PACKING / WEIGHT LIST";

$print_title1  = "our pdt. code : ".$shp_rec['shp']['ord_num'];
$print_title2 = $shp_rec['shp']['inv_num']." ver.".($shp_rec['shp']['ver']+1);
$creator = $shp_rec['shp']['submit_user'];
$mark = $shp_rec['shp']['inv_num'];
$fty = $shp_rec['shp']['fty'];


$pdf = new PDF_shp_pack_ct('P','mm','A4');
$pdf->SetAutoPageBreak('on',10);
$pdf->SetMargins(3,5);
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();

$gd_mk = 0;
$pg_mk = 0;
$po = '';
$pdf->ship_title($shp_rec['shp']['pack']);
$i_mk = 0;
for($i=0; $i<sizeof($shp_rec['shp_det']); $i++) //列印packing list
{
	$shp_rec['shp_det'][$i]['fab_content'] = $ship_doc->get_other_ord_value($shp_rec['shp_det'][$i]['ship_ord_id'],'FABRIC CONTENT');
	$ctn_str = $shp_rec['shp_det'][$i]['ctn_l']."x".$shp_rec['shp_det'][$i]['ctn_w']."x".$shp_rec['shp_det'][$i]['ctn_h'];
	
	if(!isset($ctn_dim[$ctn_str]))$ctn_dim[$ctn_str] = 0;
	$ctn_dim[$ctn_str] += $shp_rec['shp_det'][$i]['cnum'];
/*	
	if($shp_rec['shp_det'][$i]['f_mk'] == 1)
	{
		if($i > 0)
		{
			$pg_mk++;
			if($pg_mk > 47)
			{
				$pdf->AddPage();
				$pdf->ship_title($shp_rec['shp']['pack']);
				$pg_mk = 0;
			}
			$pdf->ship_sub_total($shp_rec['shp_det'][$i_mk]);			
		}
		$i_mk = $i;		
	}
*/
//echo $pg_mk."<BR>";
	if($po != $shp_rec['shp_det'][$i]['cust_po'])
	{
		$tmp =explode('|',$content[$shp_rec['shp_det'][$i]['cust_po']]);
		$pg_mk += (sizeof($tmp) + 1);
		if($shp_rec['shp_det'][$i]['fab_content'])$pg_mk++;
		if($pg_mk > 47)
		{
			$pdf->AddPage();
			$pdf->ship_title($shp_rec['shp']['pack']);
			$pg_mk = 0;
		}
		$parm = array('cust_po'	=>	$shp_rec['shp_det'][$i]['cust_po'],
									'style'		=>	substr($style[$shp_rec['shp_det'][$i]['cust_po']],0,-1),
									'content'	=>	substr($content[$shp_rec['shp_det'][$i]['cust_po']],0,-1),
									'fab_content'	=>	$shp_rec['shp_det'][$i]['fab_content'],
									);
		$po = $shp_rec['shp_det'][$i]['cust_po'];
		$pdf->ship_ord_det($parm);
	}
	if($shp_rec['shp_det'][$i]['f_mk'] == 1)
	{
		$pg_mk += 3;
		$tmp = explode('|',$c_id[$shp_rec['shp_det'][$i]['cnt']]);
		$pg_mk += sizeof($tmp);

//		if( $shp_rec['shp_det'][$i]['f_mk'] == 1)$pg_mk++;
		if($pg_mk > 47)
		{
			$pdf->AddPage();
			$pdf->ship_title($shp_rec['shp']['pack']);
			$pg_mk = 0;

		}
//echo $c_id[$shp_rec['shp_det'][$i]['cnt']]."<BR>";
		$pdf->ship_txt($shp_rec['shp_det'][$i],$c_id[$shp_rec['shp_det'][$i]['cnt']]);
	 }	
}
/*
$pg_mk++;
if($pg_mk > 47)
{
	$pdf->AddPage();
	$pdf->ship_title($shp_rec['shp']['pack']);
	$pg_mk = 0;
}
$pdf->ship_sub_total($shp_rec['shp_det'][$i_mk]);
*/


$pg_mk += 3;
if($pg_mk > 47)
{
	$pdf->AddPage();
	$pdf->ship_title($shp_rec['shp']['pack']);
	$pg_mk = 0;
}
$pdf->ship_total($shp_rec['amt']);

$pg_mk +=(sizeof($ord_rec)*2);
if($pg_mk > 47)
{
	$pdf->AddPage();
	$pg_mk = 0;
}
$pdf->Cell(15,4,'',0,0,'R'); 
$pdf->Cell(185,4,'LINE WEIGHT KG PER PC',0,0,'L'); 

$size = '';
//各尺碼單件重量
for($i=0; $i<sizeof($ord_rec); $i++)
{
	$nnw= array();
	for($j=0; $j<sizeof($ord_rec[$i]['assort']); $j++)
	{
		for($k=0; $k<sizeof($ord_rec[$i]['assort'][$j]['nnw']); $k++)
		{
			if(!isset($nnw[$k]))$nnw[$k] = 0;
			$nnw[$k] += $ord_rec[$i]['assort'][$j]['nnw'][$k];
		}
	}
	if($size != $ord_rec[$i]['ord']['size']) 
	{
		$pdf->Cell(15,4,'',0,0,'R'); 
		$pdf->Cell(15,4,'STYLE#/SIZE',0,0,'L'); 
		for($s=0; $s<sizeof($ord_rec[$i]['size']); $s++) $pdf->Cell(12,4,$ord_rec[$i]['size'][$s],0,0,'R'); 
		$pdf->ln();
		$size = $ord_rec[$i]['ord']['size'];
	}
		$pdf->Cell(15,4,'',0,0,'R'); 
		$pdf->Cell(15,4,$ord_rec[$i]['ord']['style_num'],0,0,'L'); 
		for($s=0; $s<sizeof($nnw); $s++) $pdf->Cell(12,4,NUMBER_FORMAT($nnw[$s],3),0,0,'R'); 
		$pdf->ln();
	
}

$pg_mk +=3;
if($pg_mk > 45)
{
	$pdf->AddPage();
	$pg_mk = 0;
}

$pdf->weight($shp_rec['amt']);  // total wieght

//各種大小箱子箱數

$pg_mk +=sizeof($ctn_dim);
if($pg_mk > 45)
{
	$pdf->AddPage();
	$pg_mk = 0;
}
$i=0;
foreach($ctn_dim as $key => $value)
{
	$title = '';
	if($i==0) $title = 'CARTON DIMENSION LIST';
	$pdf->Cell(50,4,$title,0,0,'C');
	$pdf->Cell(100,4,$key." inch X ".$value." CTNS",0,1,'L');
	$i++;
}


$pg_mk+=2;
$pdf->ln();

for($i=0; $i<sizeof($shp_rec['break']); $i++)
{
	if($pg_mk > 45)
	{
		$pdf->AddPage();
		if(!(isset($shp_rec['break'][$i]['tb_mk']) && ($shp_rec['break'][$i]['tb_mk'] == 1 || $shp_rec['break'][$i]['tb_mk'] == 2 || $shp_rec['break'][$i]['tb_mk'] == 5)))
			$pdf->break_title($shp_rec['break'][$i]['size']);// COLOR/SIZE BREAKDOWN title
		$pg_mk = 1;
	}

	if(isset($shp_rec['break'][$i]['tb_mk']) && ($shp_rec['break'][$i]['tb_mk'] == 1 ))
	{
		$pdf->break_total($shp_rec['break'][$i]);// COLOR/SIZE BREAKDOWN小計
		$pg_mk++;		
	}

	if(isset($shp_rec['break'][$i]['tb_mk']) && ($shp_rec['break'][$i]['tb_mk'] == 1 || $shp_rec['break'][$i]['tb_mk'] == 2 || $shp_rec['break'][$i]['tb_mk'] == 5))
	{
		$pdf->break_title($shp_rec['break'][$i]['size']);// COLOR/SIZE BREAKDOWN title
		$pg_mk++;
		
	}
	$pdf->break_txt($shp_rec['break'][$i]);// COLOR/SIZE BREAKDOWN 細項
	$pg_mk++;
	if(isset($shp_rec['break'][$i]['tb_mk']) && ( $shp_rec['break'][$i]['tb_mk'] == 3 || $shp_rec['break'][$i]['tb_mk'] == 5))
	{
		$pdf->break_total($shp_rec['break'][$i]);// COLOR/SIZE BREAKDOWN小計
		$pg_mk++;
	}	
}

$name=$shp_rec['shp']['inv_num'].'_pack_ct.pdf';
$pdf->Output($name,'D');


break;	



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "pack_slip_ct_print":			
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "pack_slip_ct_print":   //  .......
	check_authority('032',"view");	
		$sort = "s_order.cust_po, shipdoc_qty.cnt,shipdoc_qty.f_mk";
		$shp_rec = $ship_doc->get_pack($PHP_id,$sort);
		$ord_mk = '';
		for($i=0; $i<sizeof($shp_rec['shp_det']); $i++)
		{
			if(!isset($style[$shp_rec['shp_det'][$i]['cust_po']] ) )$style[$shp_rec['shp_det'][$i]['cust_po']] = '';
			if(!isset($content[$shp_rec['shp_det'][$i]['cust_po']] ) )$content[$shp_rec['shp_det'][$i]['cust_po']] = '';
			if(!isset($c_id[$shp_rec['shp_det'][$i]['cnt']] ) )$c_id[$shp_rec['shp_det'][$i]['cnt']] = '';

			if(!strstr($style[$shp_rec['shp_det'][$i]['cust_po']],$shp_rec['shp_det'][$i]['style_num']))$style[$shp_rec['shp_det'][$i]['cust_po']] .= $shp_rec['shp_det'][$i]['style_num'].'/';
			if($shp_rec['shp_det'][$i]['content'] && !strstr($content[$shp_rec['shp_det'][$i]['cust_po']],$shp_rec['shp_det'][$i]['content']))$content[$shp_rec['shp_det'][$i]['cust_po']] .= $shp_rec['shp_det'][$i]['content'].'|';
			if($shp_rec['shp_det'][$i]['ctn_id'] && !strstr($c_id[$shp_rec['shp_det'][$i]['cnt']],$shp_rec['shp_det'][$i]['ctn_id']))$c_id[$shp_rec['shp_det'][$i]['cnt']] .= $shp_rec['shp_det'][$i]['ctn_id'].'|';
			if($ord_mk != $shp_rec['shp_det'][$i]['style_num'])
			{
				$ord_mk = $shp_rec['shp_det'][$i]['style_num'];
				$ord_rec[] = $ship_doc->get_order($shp_rec['shp_det'][$i]['ord_id']);
			}
			
		}


//---------------------------------- 資料庫作業結束 ------------------------------

include_once($config['root_dir']."/lib/class.pdf_shp_slip_ct.php");
session_register	('main_rec');
$main_rec = array (	'fty'				=>	$shp_rec['shp']['fty'],
										'inv_num'		=>	$shp_rec['shp']['inv_num'],										
										'bill_to'		=>	$shp_rec['shp']['bill_addr1'],
										'bill_addr'	=>	$shp_rec['shp']['bill_addr2'],
										'ship_from'	=>	$shp_rec['shp']['ship_from'],
										'ship_to'		=>	$shp_rec['shp']['ship_to'],
										);

$print_title="PACKING SLIP";

$print_title1  = "our pdt. code : ".$shp_rec['shp']['ord_num'];
$print_title2 = $shp_rec['shp']['inv_num']." ver.".($shp_rec['shp']['ver']+1);
$creator = $shp_rec['shp']['submit_user'];
$mark = $shp_rec['shp']['inv_num'];
$fty = $shp_rec['shp']['fty'];


$pdf = new PDF_shp_slip_ct('P','mm','A4');
$pdf->SetAutoPageBreak('on',10);
$pdf->SetMargins(3,10);
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();


$pg_mk = 0;
$po = '';
for($i=0; $i<sizeof($shp_rec['shp_det']); $i++) //列印packing list
{
	$po_mk = 0;

	if($po != $shp_rec['shp_det'][$i]['cust_po'])
	{
		$tmp =explode('|',$content[$shp_rec['shp_det'][$i]['cust_po']]);

		$parm = array('cust_po'	=>	$shp_rec['shp_det'][$i]['cust_po'],
									'style'		=>	substr($style[$shp_rec['shp_det'][$i]['cust_po']],0,-1),
									'content'	=>	substr($content[$shp_rec['shp_det'][$i]['cust_po']],0,-1),
									);
		$po = $shp_rec['shp_det'][$i]['cust_po'];
		
		$pdf->ship_ord_det($parm,$i,$shp_rec['shp']['conter_num']);
		$po_mk = 1;
	}

		if($po_mk == 0)$shp_rec['shp_det'][$i]['cust_po']='';
		$pdf->ship_txt($shp_rec['shp_det'][$i]);	





}

$pdf->ship_total($shp_rec['amt']);


$name=$shp_rec['shp']['inv_num'].'_pack_ct.pdf';
$pdf->Output($name,'D');


break;	


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "inv_ct_print":			
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "inv_ct_print":   //  .......
	check_authority('032',"view");	
		$sort = "s_order.style_num, shipdoc_qty.cnt";
		$shp_rec = $ship_doc->get_pack($PHP_id,$sort);
		$ord_mk = '';
		$content = array();
		for($i=0; $i<sizeof($shp_rec['shp_det']); $i++)
		{
			if(!isset($style[$shp_rec['shp_det'][$i]['cust_po']] ) )$style[$shp_rec['shp_det'][$i]['cust_po']] = '';
			if(!isset($content[$shp_rec['shp_det'][$i]['cust_po']] ) )$content[$shp_rec['shp_det'][$i]['cust_po']] = '';
			if($style[$shp_rec['shp_det'][$i]['cust_po']] && !strstr($style[$shp_rec['shp_det'][$i]['cust_po']],$shp_rec['shp_det'][$i]['style_num']))$style[$shp_rec['shp_det'][$i]['cust_po']] .= $shp_rec['shp_det'][$i]['style_num'].'/';
			if($shp_rec['shp_det'][$i]['content'] && !strstr($content[$shp_rec['shp_det'][$i]['cust_po']],$shp_rec['shp_det'][$i]['content']))$content[$shp_rec['shp_det'][$i]['cust_po']] .= $shp_rec['shp_det'][$i]['content'].'|';
			if($ord_mk != $shp_rec['shp_det'][$i]['style_num'])
			{
				$ord_mk = $shp_rec['shp_det'][$i]['style_num'];
				$ord_rec[] = $ship_doc->get_order($shp_rec['shp_det'][$i]['ord_id']);
			}
			
		}


//---------------------------------- 資料庫作業結束 ------------------------------

include_once($config['root_dir']."/lib/class.pdf_shp_inv_ct.php");
session_register	('main_rec');
$eta = '';
if($shp_rec['shp']['fv_eta'])$eta = $shp_rec['shp']['fv_eta'];
if($shp_rec['shp']['mv_eta'])$eta = $shp_rec['shp']['mv_eta'];
$shp_rec['shp']['invoce_of']=$ship_doc->get_other_value($shp_rec['shp']['id'],'INVOICE OF');

$main_rec = array (	'fty'				=>	$shp_rec['shp']['fty'],
										'inv_num'		=>	$shp_rec['shp']['inv_num'],
										'inv_date'	=>	$shp_rec['shp']['submit_date'],
										'pack'			=>	$shp_rec['shp']['invoce_of'],
										'bill_to'		=>	$shp_rec['shp']['bill_addr1'],
										'bill_addr'	=>	$shp_rec['shp']['bill_addr2'],
										'ship_via'	=>	$shp_rec['shp']['ship_via'],
										'ship_from'	=>	$shp_rec['shp']['ship_from'],
										'ship_to'		=>	$shp_rec['shp']['ship_to'],
										'date'			=>	$shp_rec['shp']['fv_etd'],
										'lc_num'		=>	$shp_rec['shp']['lc_no'],
										'lc_date'		=>	$shp_rec['shp']['lc_date'],
										'eta'				=>	$eta,
										);

$print_title="COMMERCIAL INVOICE";

$print_title1  = "our pdt. code : ".$shp_rec['shp']['ord_num'];
$print_title2 = $shp_rec['shp']['inv_num']." ver.".($shp_rec['shp']['ver']+1);
$creator = $shp_rec['shp']['submit_user'];
$mark = $shp_rec['shp']['inv_num'];
$fty = $shp_rec['shp']['fty'];


$pdf = new PDF_shp_inv_ct('P','mm','A4');
$pdf->SetAutoPageBreak('on',10);
$pdf->SetMargins(3,5);
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();

$gd_mk = 0;
$pg_mk = 0;
$po = '';
		if($pg_mk > 47)
		{
			$pdf->AddPage();
			$pdf->ship_title($shp_rec['shp']['ship_term']);
			$pg_mk = 0;

		}
$pdf->ship_title($shp_rec['shp']['ship_term']);
$y = $pdf->GetY();
$ship_mark = array('ship_to'		=> $shp_rec['shp']['ship_addr1'],
									 'ship_mark'	=>	$shp_rec['shp']['ship_mark'],
									 'ship_addr'	=>	$shp_rec['shp']['ship_addr2'],
									 'ship_det'		=>	$shp_rec['shp']['ship_det'],
									);
$pdf->ship_mark($ship_mark);
$pdf->SetY($y);
$style='';
$s_qty=$s_amt=$t_qty=$t_amt=0;
$style_count=0;
for($i=0; $i<sizeof($shp_rec['shp_det']); $i++)
{
	$shp_rec['shp_det'][$i]['case_num']=$ship_doc->get_other_ord_value($shp_rec['shp_det'][$i]['ship_ord_id'],'CASE');
	if($style != $shp_rec['shp_det'][$i]['style_num'])
	{
		$style_count++;
		$pg_mk += 3;
		if($style<>'')$pg_mk++;
		if($pg_mk > 47)
		{
			$pdf->AddPage();
			$pdf->ship_title($shp_rec['shp']['pack']);
			$pg_mk = 0;

		}
		$parm = array('cust_po'	=>	$shp_rec['shp_det'][$i]['cust_po'],
									'content'	=>	$shp_rec['shp_det'][$i]['content'],
									'style'		=>	$shp_rec['shp_det'][$i]['style_num'],
									'case'		=>	$shp_rec['shp_det'][$i]['case_num'],
									);
		if($style<>'')
		{
			$pdf->ship_total('SUBTOTAL',$s_qty,$s_amt);
			
		}


		$style = $shp_rec['shp_det'][$i]['style_num'];
		$pdf->ship_ord_det($parm);
		$s_qty=$s_amt=0;
	}
	$s_qty+=($shp_rec['shp_det'][$i]['s_qty']*$shp_rec['shp_det'][$i]['s_cnum']);
	$t_qty+=($shp_rec['shp_det'][$i]['s_qty']*$shp_rec['shp_det'][$i]['s_cnum']);
	$s_amt+=($shp_rec['shp_det'][$i]['s_qty']*$shp_rec['shp_det'][$i]['s_cnum']*$shp_rec['shp_det'][$i]['ship_fob']);
	$t_amt+=($shp_rec['shp_det'][$i]['s_qty']*$shp_rec['shp_det'][$i]['s_cnum']*$shp_rec['shp_det'][$i]['ship_fob']);
	
		$pg_mk += 1;
		if($pg_mk > 47)
		{
			$pdf->AddPage();
			$pdf->ship_title($shp_rec['shp']['pack']);
			$pg_mk = 0;

		}
		$pdf->ship_txt($shp_rec['shp_det'][$i]);
	
}
$pdf->ship_total('SUBTOTAL',$s_qty,$s_amt);
$pdf->ship_total('GRAND TOTAL',$t_qty,$t_amt);

$pdf->SetX(120);
$pdf->Cell(30,5,NUMBER_FORMAT(($t_qty/12),2),0,0,'R');
$pdf->Cell(5,5,'DOZEN',0,1,'L');

//$pdf->SetX(60);
//$pdf->Cell(60,5,'LESS PRICE TICKET CHARGES',0,0,'L');
//$pdf->Cell(30,5,NUMBER_FORMAT($t_qty,0),0,0,'R');
//$pdf->Cell(5,5,'PCS',0,0,'C');
//$pdf->Cell(20,5,'US$0.026',0,0,'R'); //quantity
//$pdf->Cell(25,5,'US$'.NUMBER_FORMAT(($t_qty*0.026),2),0,1,'R'); //quantity
//
//
//
//$pdf->SetX(60);
//$pdf->Cell(115,5,'LESS DAMAGE CHARGES',0,0,'L');
//$pdf->Cell(25,5,'US$'.NUMBER_FORMAT(($style_count*150),2),0,1,'R'); //quantity
//
//$t_amt -= ($style_count*150);



$oth_charge = 0;

for($i=0; $i<sizeof($shp_rec['shp_charge']); $i++)
{
	$pdf->SetX(60);
	$pdf->Cell(115, 5,"LESS : ".$shp_rec['shp_charge'][$i]['chg_des'],0,0,'L',0);
	$pdf->Cell(25, 5,"-US$".NUMBER_FORMAT($shp_rec['shp_charge'][$i]['charge'],2),0,1,'R',0);					
	$oth_charge +=  $shp_rec['shp_charge'][$i]['charge'];
}




//$g_total = $t_amt - ($t_qty*0.026) - ($style_count*150) - $oth_charge;
$g_total = $t_amt  - $oth_charge;
$pdf->SetX(60);
$pdf->Cell(115,5,'GRAND TOTAL AMOUNT','T',0,'L');
$pdf->Cell(25,5,'US$'.NUMBER_FORMAT($g_total,2),'T',1,'R'); //quantity

$pdf->ln();
$pdf->SetX(60);
$pdf->Cell(22,5,'TOTAL : PACK IN',0,0,'L');
$pdf->Cell(20,5,$shp_rec['amt']['cnum'],'B',0,'R'); //quantity
$pdf->Cell(20,5,'CARTONS',0,1,'L'); //quantity

$pdf->Cell(200,5,'SAY TOTAL U.S DOLLERS'.engilsh_math($g_total).'ONLY',0,1,'L'); //quantity

$pdf->det_txt($shp_rec['shp']['fab_des']);
$pdf->det_txt($shp_rec['shp']['others']);
$pdf->ln();
$pdf->SetFont('Arial','B',8);
$pdf->Cell(200,5,'MANUFACTURER:',0,1,'L'); //quantity
$pdf->det_txt($shp_rec['shp']['fty_det']);

$pdf->end_ship($shp_rec['amt'],$shp_rec['shp']['fty']);
$name=$shp_rec['shp']['inv_num'].'_inv_ct.pdf';
$pdf->Output($name,'D');


break;	



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "pack_th_print":			
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "pack_th_print":   //  .......
	check_authority('032',"view");	
		$sort = "shipdoc_qty.cnt";
		$shp_rec = $ship_doc->get_pack($PHP_id,$sort);
		$ord_mk = '';
		$shp_rec['shp']['customer_po'] = $ship_doc->get_other_value($PHP_id,'Customer PO No.');
		$shp_rec['shp']['season_division'] = $ship_doc->get_other_value($PHP_id,'Season/Division');
		$shp_rec['shp']['fabric_content'] = $ship_doc->get_other_value($PHP_id,'Fabric content');
		$shp_rec['shp']['company_code'] = $ship_doc->get_other_value($PHP_id,'Company Code');
		$shp_rec['shp']['style_des'] = $ship_doc->get_other_value($PHP_id,'Style description');
		 
		$po_num = $style_num = '';
		for($i=0; $i<sizeof($shp_rec['shp_det']); $i++)
		{
			if(!strstr($po_num,$shp_rec['shp_det'][$i]['cust_po']))$po_num .= $shp_rec['shp_det'][$i]['cust_po'].',';
			if(!strstr($style_num,$shp_rec['shp_det'][$i]['style_num']))$style_num .= $shp_rec['shp_det'][$i]['style_num'].',';
		}
		$po_num = substr($po_num,0,-1);
		$style_num = substr($style_num,0,-1);
		
		$parm = array('fty_name' 				=>	$SHIP_TO[$shp_rec['shp']['fty']]['Name'],
									'city_name'				=>	$SHIP_TO[$shp_rec['shp']['fty']]['Addr'],
									'country_org'			=>	$shp_rec['shp']['country_org'],
									'ship_date'				=>	$shp_rec['shp']['fv_etd'],
									'qty'							=>	$shp_rec['amt']['qty'],
									'inv_num'					=>	$shp_rec['shp']['inv_num'],
									'cbm'							=>	$shp_rec['shp']['cbm'],
									'cnum'						=>	$shp_rec['amt']['cnum'],
									'ship_to'					=>	$shp_rec['shp']['ship_to'],
									'ship_by'					=>	$shp_rec['shp']['mode_value'],
									'gw'							=>	$shp_rec['amt']['gw'],
									'nw'							=>	$shp_rec['amt']['nw'],
									'po_num'					=> 	$po_num,
									'pack'						=>	$shp_rec['shp']['pack'],	
									'customer_po'			=>	$shp_rec['shp']['customer_po'],	
									'style_num'				=>	$style_num,	
									'season_division'	=>	$shp_rec['shp']['season_division'],
									'des'							=>	$shp_rec['shp']['style_des'],
									'fabric_content'	=>	$shp_rec['shp']['fabric_content'],
									'company_code'		=>	$shp_rec['shp']['company_code'],
									);
									
$mark = $shp_rec['shp']['inv_num'];					
$name=$shp_rec['shp']['inv_num'].'_pack_th.pdf';				
//---------------------------------- 資料庫作業結束 ------------------------------

include_once($config['root_dir']."/lib/class.pdf_ship_pack_th.php");



$pdf = new PDF_shidoc_pack_th('P','mm','A4');
$pdf->SetAutoPageBreak('on',10);
$pdf->SetMargins(3,5);
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();

$pdf->hend_title($parm);
$pdf->item_title();

$x= 21;

for($i=0; $i<sizeof($shp_rec['shp_det']); $i++)
{
	$size_qty = array();
	$k=0;
	for($j=0; $j<sizeof($shp_rec['shp_det'][$i]['size_qty']); $j++)
	{
		if($shp_rec['shp_det'][$i]['size_qty'][$j] > 0)
		{
			$size_qty[$k]['size'] = $shp_rec['shp_det'][$i]['size'][$j];
			$size_qty[$k]['qty'] = $shp_rec['shp_det'][$i]['size_qty'][$j];
			$k++;
		}
	}
	$x += $k;
	if($x >= 64)
	{
		$pdf->AddPage();
		$pdf->item_title();
		$x=0;
	}
	$pdf->item($shp_rec['shp_det'][$i],$size_qty);
	
}

	if($x >= 60)
	{
		$pdf->AddPage();
		$x=0;
	}
	$pdf->assort_title();

  for($i=0; $i<sizeof($shp_rec['break']); $i++)
  {
  	$x += (sizeof($shp_rec['break'][$i]['size'])+1);
		if($x >= 64)
		{
			$pdf->AddPage();
			$pdf->assort_title();
			$x=0;
		}
  	$pdf->assort($shp_rec['break'][$i]);
  }

	if($x >= 62)
	{
		$pdf->AddPage();
		$pdf->assort_title();
		$x=0;
	}	
	$pdf->ttl($shp_rec['amt']['qty']);
	
$name=$shp_rec['shp']['inv_num'].'_pack_th.pdf';
$pdf->Output($name,'D');


break;	

















//====================================================
case "ship_excel_print":
	check_authority('032',"view");
	$shp_pack = $ship_doc->get_pack($PHP_id);
	$shp_inv = $ship_doc->get_inv($PHP_id);
	$size = 0;
	for($i=0; $i<sizeof($shp_pack['shp_det']); $i++)
	{
		if(sizeof($shp_pack['shp_det'][$i]['size']) > $size) $size = sizeof($shp_pack['shp_det'][$i]['size']);
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
	  HeaderingExcel('shipdoc.xls');
	 
	  // Creating a workbook
	  $workbook = new Workbook("-");

	  // Creating the first worksheet
	  $worksheet1 =& $workbook->add_worksheet('Shipping document');


	// 寫入 title
	  
	  $rr =& $workbook->add_format(); //置右
	  $rr->set_size(10);
	  $rr->set_align('right');
	  $rr->set_num_format(3);

	  $cc =& $workbook->add_format();  //置中
	  $cc->set_align('center');
	  
	  $f6 =& $workbook->add_format();  //灰底白字置右
	  $f6->set_size(10);
	  $f6->set_color('white');
	  $f6->set_pattern(1);
	  $f6->set_align('right');
	  $f6->set_num_format(3);
	  $f6->set_fg_color('grey');


	  $f8 =& $workbook->add_format();  //黑底
	  $f8->set_color(62);	
	  $f8->set_pattern(1);  
	  $f8->set_fg_color('black');


	  $f9 =& $workbook->add_format();  //灰底白字置中
	  $f9->set_size(10);
	  $f9->set_color('white');
	  $f9->set_pattern(1);
	  $f9->set_align('center');
	  $f9->set_num_format(3);
	  $f9->set_fg_color('grey');

	  
	  for($i=0; $i<5; $i++)$clumn[] = 16; //style# ~ carton id
	  for($i=0; $i<= $size; $i++)$clumn[] = 10; //ppk ~ size
	  for($i=0; $i<7; $i++)$clumn[] = 16; //qty ~ ctn size
		for($i=sizeof($clumn); $i<18; $i++)$clumn[] = 16;
	  for ($i=0; $i< sizeof($clumn); $i++) $worksheet1->set_column(0,$i,$clumn[$i]);
	

//	  for ($i=0; $i< sizeof($clumn); $i++) $worksheet1->write_string(0,$i,'');


	  $worksheet1->write_string(0,0,'Shipping Document Text File     '.date('Y-m-d'));
	  $worksheet1->merge_cells(0,0,0,(sizeof($clumn)-1)); 

	  $worksheet1->write_string(1,0,'Cust : ',$f6);
	  $worksheet1->write_string(1,1,$shp_inv['shp']['ord_cust']);	
	  $worksheet1->write_string(1,3,'FTY : ',$f6);
	  $worksheet1->write_string(1,4,$shp_inv['shp']['fty']);
		
	  $worksheet1->write_string(2,0,'Invoice : ',$f6);
	  $worksheet1->write_string(2,1,$shp_inv['shp']['inv_num']);
	  $worksheet1->write_string(2,3,'Inv. date : ',$f6);
	  $worksheet1->write_string(2,4,$shp_inv['shp']['submit_date']);	
	  $worksheet1->write_string(2,6,'Ship date : ',$f6);
	  $worksheet1->write_string(2,7,$shp_inv['shp']['ship_date']);	
		
	  $worksheet1->write_string(3,0,'SHIP TERM : ',$f6);
	  $worksheet1->write_string(3,1,$shp_inv['shp']['ship_term']);
	  $worksheet1->write_string(3,3,'SHIP VIA : ',$f6);
	  $worksheet1->write_string(3,4,$shp_inv['shp']['ship_via']);	
	  $worksheet1->write_string(3,6,'SHIP BY : ',$f6);
	  $worksheet1->write_string(3,7,$shp_inv['shp']['ship_by']);	
	
	  $worksheet1->write_string(4,0,'FROM : ',$f6);
	  $worksheet1->write_string(4,1,$shp_inv['shp']['ship_from']);	
	  $worksheet1->write_string(4,6,'TO : ',$f6);
	  $worksheet1->write_string(4,7,$shp_inv['shp']['ship_to']);	

	  $worksheet1->write_string(5,0,'Payment : ',$f6);
	  $worksheet1->write_string(5,1,$shp_inv['shp']['payment']);
	  $worksheet1->write_string(5,3,'Marks & NO. : ',$f6);
	  $worksheet1->write_string(5,4,$shp_inv['shp']['mark_no']);	
	  $worksheet1->write_string(5,6,'Description : ',$f6);
	  $worksheet1->write_string(5,7,$shp_inv['shp']['des']);	

	  $worksheet1->write_string(6,0,'FV : ',$f6);
	  $worksheet1->write_string(6,1,$shp_inv['shp']['fv']);
	  $worksheet1->write_string(6,3,'ETD : ',$f6);
	  $worksheet1->write_string(6,4,$shp_inv['shp']['fv_etd']);	
	  $worksheet1->write_string(6,6,'ETA ',$f6);
	  $worksheet1->write_string(6,7,$shp_inv['shp']['fv_eta']);	
	
	  $worksheet1->write_string(6,0,'MV : ',$f6);
	  $worksheet1->write_string(6,1,$shp_inv['shp']['mv']);
	  $worksheet1->write_string(6,3,'ETD : ',$f6);
	  $worksheet1->write_string(6,4,$shp_inv['shp']['mv_etd']);	
	  $worksheet1->write_string(6,6,'ETA ',$f6);
	  $worksheet1->write_string(6,7,$shp_inv['shp']['mv_eta']);	
	
	  $worksheet1->write_string(7,0,'L/C NO : ',$f6);
	  $worksheet1->write_string(7,1,$shp_inv['shp']['lc_no']);
	  $worksheet1->write_string(7,3,'Date : ',$f6);
	  $worksheet1->write_string(7,4,$shp_inv['shp']['lc_date']);	
	  $worksheet1->write_string(7,6,'L/C BANK  ',$f6);
	  $worksheet1->write_string(7,7,$shp_inv['shp']['lc_bank']);	

	  $worksheet1->write_string(8,0,'CONSIGNEE : ',$f6);
	  $worksheet1->write_string(8,1,$shp_inv['shp']['consignee_name']);
	  $worksheet1->write_string(8,3,$shp_inv['shp']['consignee_addr']);

	  $worksheet1->write_string(9,0,'SHIP TO : ',$f6);
	  $worksheet1->write_string(9,1,$shp_inv['shp']['ship_addr1']);
	  $worksheet1->write_string(9,3,$shp_inv['shp']['ship_addr2']);

	  $worksheet1->write_string(10,0,'BILL TO : ',$f6);
	  $worksheet1->write_string(10,1,$shp_inv['shp']['bill_addr1']);
	  $worksheet1->write_string(10,3,$shp_inv['shp']['bill_addr2']);

	  $worksheet1->write_string(11,0,'M.I.D NO : ',$f6);
	  $worksheet1->write_string(11,1,$shp_inv['shp']['mid_no']);	
	  $worksheet1->write_string(11,6,'Country of Org. ',$f6);
	  $worksheet1->write_string(11,7,$shp_inv['shp']['country_org']);	

	  $worksheet1->write_string(12,0,'Carrier : ',$f6);
	  $worksheet1->write_string(12,1,$shp_inv['shp']['carrier']);	
	  $worksheet1->write_string(12,6,'DISCHARGE PORT : ',$f6);
	  $worksheet1->write_string(12,7,$shp_inv['shp']['dis_port']);	

	  $worksheet1->write_string(13,0,'Carrier : ',$f6);
	  $worksheet1->write_string(13,1,$shp_inv['shp']['carrier']);	
	  $worksheet1->write_string(13,6,'DISCHARGE PORT : ',$f6);
	  $worksheet1->write_string(13,7,$shp_inv['shp']['dis_port']);	

	  if(!isset($shp_inv['shp']['conter_det.name']))$shp_inv['shp']['conter_det.name']='';
	  if(!isset($shp_inv['shp']['constor_det.name']))$shp_inv['shp']['constor_det.name']='';
	  $worksheet1->write_string(13,0,'Container : ',$f6);
	  $worksheet1->write_string(13,1,$shp_inv['shp']['conter_det.name']);	
	  $worksheet1->write_string(13,6,'Consolidator : ',$f6);
	  $worksheet1->write_string(13,7,$shp_inv['shp']['constor_det.name']);	

	  $worksheet1->write_string(14,0,'CBM (m) : ',$f6);
	  $worksheet1->write_string(14,1,$shp_inv['shp']['cbm']);

	  $worksheet1->write_string(14,3,'Customer :  ',$f6);
	  $worksheet1->write_string(14,4,$shp_inv['shp']['cust']);	
	  $worksheet1->write_string(14,6,'PACKAGING :  ',$f6);
	  $worksheet1->write_string(14,7,$shp_inv['shp']['pack']);	

	  $worksheet1->write_string(15,0,'Vendor : ',$f6);
	  $worksheet1->write_string(15,1,$shp_inv['shp']['vendor_name']);
	  $worksheet1->write_string(15,3,$shp_inv['shp']['vendor_addr']);
		
//		$worksheet1->set_row(20, 2);
//		$worksheet1->write_string(16,0,'',$f8);
//	  $worksheet1->merge_cells(16,0,20,(sizeof($clumn)-1)); 


	  

		
		$ll = 22;
		
		for($j=0; $j<sizeof($shp_inv['shp']['oth_field']); $j++)
		{
			$worksheet1->write_string($ll,0,'OTHERS FIELD',$f6);
			$worksheet1->write_string($ll,1,$shp_inv['shp']['oth_field'][$j]['f_name']." : ".$shp_inv['shp']['oth_field'][$j]['f_values']);
			$worksheet1->merge_cells($ll,1,$ll,10);
			$ll++;
		}
		$ll++;

	  //結束基本資料
	  
	  //pakcing list
	  

		for($i=0; $i<sizeof($shp_pack['shp_det']); $i++)
		{
	  	//表頭
	  	if(isset($shp_pack['shp_det'][$i]['tb_mk']) && $shp_pack['shp_det'][$i]['tb_mk'] == 1)
	  	{
	  		$size = sizeof($shp_pack['shp_det'][$i]['size']);
	  		for($j=0; $j<($size+6); $j++) $worksheet1->write_string($ll,$j,'',$f9); //style# ~ carton id			
				$worksheet1->write_string($ll,$j,'QTY',$f9);
				for($k=1; $k<=3; $k++) $worksheet1->write_string($ll,($j+$k),'',$f9);
				$j += $k;
				$worksheet1->write_string($ll,$j,'',$f9);
				$worksheet1->merge_cells($ll,$j,$ll,(sizeof($clumn)-1)); 
				$ll++;
				
	  		for($j=0; $j<6; $j++) $worksheet1->write_string($ll,$j,'',$f9); //style# ~ carton id
	  		$worksheet1->write_string($ll,$j,'Size',$f9);		
	  		$worksheet1->merge_cells($ll,$j,$ll,($j+$size)); 	
	  		$j = $j+$size;
				$worksheet1->write_string($ll,$j,'In PCS.',$f9);
				$j++;
				$worksheet1->write_string($ll,$j,'N.N.W.',$f9);
				$j++;
				$worksheet1->write_string($ll,$j,'N.N.',$f9);
				$j++;
				$worksheet1->write_string($ll,$j,'G.N.',$f9);
				$j++;
				$worksheet1->write_string($ll,$j,'CTN SIZE (cm)',$f9);
				$worksheet1->merge_cells($ll,$j,$ll,(sizeof($clumn)-1)); 				
				$ll++;
				
			 	$worksheet1->write_string($ll,0,'style#',$f9);
			 	$worksheet1->write_string($ll,1,'P.O.#',$f9);
			 	$worksheet1->write_string($ll,2,'C/NO.',$f9);
			 	$worksheet1->write_string($ll,3,'COLOR',$f9);
			 	$worksheet1->write_string($ll,4,'ctn. ID',$f9);
			 	$worksheet1->write_string($ll,5,'ppk',$f9);
	  		for($j=0; $j< $size; $j++) $worksheet1->write_string($ll,($j+6),$shp_pack['shp_det'][$i]['size'][$j],$f9); 
				$j+= 6;
				$worksheet1->write_string($ll,$j,'PER CTN.',$f9);
				$j++;
				$worksheet1->write_string($ll,$j,'IN KGS. PER CTN.',$f9);
				$worksheet1->merge_cells($ll,$j,$ll,($j+2)); 
				$j+=3;
				$worksheet1->write_string($ll,$j,'L',$f9);
				$j++;
				$worksheet1->write_string($ll,$j,'W',$f9);
				$j++;
				$worksheet1->write_string($ll,$j,'H',$f9);
				$ll++;				
			}
			//pakcing list明細
		 	$worksheet1->write_string($ll,0,$shp_pack['shp_det'][$i]['style_num']);
		 	$worksheet1->write_string($ll,1,$shp_pack['shp_det'][$i]['cust_po']);
		 	$worksheet1->write_string($ll,2,$shp_pack['shp_det'][$i]['cnt']);
		 	$worksheet1->write_string($ll,3,$shp_pack['shp_det'][$i]['color']);
		 	$worksheet1->write_string($ll,4,$shp_pack['shp_det'][$i]['ctn_id']);
		 	$worksheet1->write_string($ll,5,$shp_pack['shp_det'][$i]['ppk']);
		 	if($shp_pack['shp']['inner_box'] == 0)
		 	{
  			for($j=0; $j< $size; $j++) $worksheet1->write_string($ll,($j+6),$shp_pack['shp_det'][$i]['size_qty'][$j],$cc); 
  		}else{
  			for($j=0; $j< $size; $j++) $worksheet1->write_string($ll,($j+6),$shp_pack['shp_det'][$i]['size_box'][$j],$cc);   		
  		}
			$j+= 6;
			$worksheet1->write_string($ll,$j,$shp_pack['shp_det'][$i]['qty'],$rr);
			$j++;
			$worksheet1->write_string($ll,$j,$shp_pack['shp_det'][$i]['nnw'],$rr);
			$j++;
			$worksheet1->write_string($ll,$j,$shp_pack['shp_det'][$i]['nw'],$rr);
			$j++;
			$worksheet1->write_string($ll,$j,$shp_pack['shp_det'][$i]['gw'],$rr);
			$j++;
			$worksheet1->write_string($ll,$j,$shp_pack['shp_det'][$i]['ctn_l'],$rr);
			$j++;
			$worksheet1->write_string($ll,$j,$shp_pack['shp_det'][$i]['ctn_w'],$rr);
			$j++;
			$worksheet1->write_string($ll,$j,$shp_pack['shp_det'][$i]['ctn_h'],$rr);
			$ll++;			
		}
		//packing list總計
		$worksheet1->set_row($ll, 2);
		$worksheet1->write_string($ll,0,'',$f8);
	  $worksheet1->merge_cells($ll,0,$ll,(sizeof($clumn)-1)); 
	  $ll++;
		$worksheet1->write_string($ll,0,'GARND TOTAL',$rr);
	  $worksheet1->merge_cells($ll,0,$ll,($size+5)); 
		$j = $size+6;
		$worksheet1->write_string($ll,$j,$shp_pack['amt']['qty'],$rr);
		$j++;
		$worksheet1->write_string($ll,$j,$shp_pack['amt']['nnw'],$rr);
		$j++;
		$worksheet1->write_string($ll,$j,$shp_pack['amt']['nw'],$rr);
		$j++;
		$worksheet1->write_string($ll,$j,$shp_pack['amt']['gw'],$rr);
		$j++;
		$worksheet1->write_string($ll,$j,'');
	  $worksheet1->merge_cells($ll,$j,$ll,($j+2)); 		
	  $ll+=2;
	  
	  //訂單各尺碼出口匯總
		for($i=0; $i<sizeof($shp_pack['break']); $i++)
		{
				$size = $shp_pack['break'][$i]['size_ct'];
			//訂單各尺碼出口匯總 表尾
			if(isset($shp_pack['break'][$i]['tb_mk']) && ($shp_pack['break'][$i]['tb_mk'] == 1))
			{
				$size = $shp_pack['break'][$i]['size_ct'];
				$worksheet1->write_string($ll,0,'GRAND TOTAL',$rr);
				$worksheet1->merge_cells($ll,0,$ll,2);
				for($j=0; $j<$size; $j++)$worksheet1->write_string($ll,($j+3),$shp_pack['break'][$i]['g_size_qty'][$j],$rr);
	 			$worksheet1->write_string($ll,($size+3),$shp_pack['break'][$i]['g_qty'],$rr);
				$ll+=2;

	 		}
	 		
			//訂單各尺碼出口匯總 表頭
			if(isset($shp_pack['break'][$i]['tb_mk']) && ($shp_pack['break'][$i]['tb_mk'] == 1 || $shp_pack['break'][$i]['tb_mk'] == 2 || $shp_pack['break'][$i]['tb_mk'] == 5))
			{
				for($j=0; $j<3; $j++)$worksheet1->write_string($ll,$j,'',$f9);
				$worksheet1->write_string($ll,3,'SIZE AND QUANTITIES',$f9);			
				$worksheet1->merge_cells($ll,3,$ll,($size+2)); 		
				$worksheet1->write_string($ll,($size+3),'',$f9);
				$ll++;
				
	 			$worksheet1->write_string($ll,0,'style#',$f9);
	 			$worksheet1->write_string($ll,1,'P.O.#',$f9);
	 			$worksheet1->write_string($ll,2,'COLOR',$f9);
				for($j=0; $j<$size; $j++)$worksheet1->write_string($ll,($j+3),$shp_pack['break'][$i]['size'][$j],$f9);
	 			$worksheet1->write_string($ll,($size+3),'TOTAL',$f9);
	 			$ll++;
	 		}
	 		
			//訂單各尺碼出口匯總 內容
	 		$worksheet1->write_string($ll,0,$shp_pack['break'][$i]['style_num']);
	 		$worksheet1->write_string($ll,1,$shp_pack['break'][$i]['po_num']);
	 		$worksheet1->write_string($ll,2,$shp_pack['break'][$i]['color']);
			for($j=0; $j<$size; $j++)$worksheet1->write_string($ll,($j+3),$shp_pack['break'][$i]['qty'][$j],$rr);
	 		$worksheet1->write_string($ll,($size+3),$shp_pack['break'][$i]['ttl_qty'],$rr);
	 		$ll++;
	 		
			//訂單各尺碼出口匯總 表尾
			if(isset($shp_pack['break'][$i]['tb_mk']) && ($shp_pack['break'][$i]['tb_mk'] == 3 || $shp_pack['break'][$i]['tb_mk'] == 5))
			{
				$size = $shp_pack['break'][$i]['size_ct'];
				$worksheet1->write_string($ll,0,'GRAND TOTAL',$rr);
				$worksheet1->merge_cells($ll,0,$ll,2);
				for($j=0; $j<$size; $j++)$worksheet1->write_string($ll,($j+3),$shp_pack['break'][$i]['g_size_qty'][$j],$rr);
	 			$worksheet1->write_string($ll,($size+3),$shp_pack['break'][$i]['g_qty'],$rr);
				$ll+=2;
	 		}
	 		
	 		
	  }
		
		//invoice 中訂單明細
		$worksheet1->write_string($ll,0,'Content#',$f9);
		$worksheet1->merge_cells($ll,0,$ll,2);
		$worksheet1->write_string($ll,3,'Style#',$f9);
		$worksheet1->write_string($ll,4,'P.O.#',$f9);
		$worksheet1->write_string($ll,5,'COLOR',$f9);
		$worksheet1->write_string($ll,6,'Description',$f9);
		$worksheet1->write_string($ll,7,'HTS CODE/CAT#',$f9);
		$worksheet1->write_string($ll,8,'QTY',$f9);
		$worksheet1->write_string($ll,9,'PRICE',$f9);
		$worksheet1->write_string($ll,10,'AMOUNT',$f9);
		$ll++;
		
		for($i=0; $i<sizeof($shp_inv['shp_det']); $i++)
		{
			if(isset($shp_inv['shp_det'][$i]['tb_mk']) && $shp_inv['shp_det'][$i]['tb_mk'] == 1)
			{
				for($j=0; $j<sizeof($shp_inv['shp_det'][$i]['oth_field']); $j++)
				{
					$worksheet1->write_string($ll,0,'OTHERS FIELD',$f6);
					$worksheet1->write_string($ll,1,$shp_inv['shp_det'][$i]['oth_field'][$j]['f_name']." : ".$shp_inv['shp_det'][$i]['oth_field'][$j]['f_values']);
					$worksheet1->merge_cells($ll,1,$ll,10);
					$ll++;
				}
			}
			$worksheet1->write_string($ll,0,$shp_inv['shp_det'][$i]['content']);
			$worksheet1->merge_cells($ll,0,$ll,2);
			$worksheet1->write_string($ll,3,$shp_inv['shp_det'][$i]['style_num']);
			$worksheet1->write_string($ll,4,$shp_inv['shp_det'][$i]['cust_po']);
			$worksheet1->write_string($ll,5,$shp_inv['shp_det'][$i]['color']);
			$worksheet1->write_string($ll,6,$shp_inv['shp_det'][$i]['des']);
			$worksheet1->write_string($ll,7,$shp_inv['shp_det'][$i]['hts_cat']);
			$worksheet1->write_string($ll,8,$shp_inv['shp_det'][$i]['bk_qty'],$rr);
			$worksheet1->write_string($ll,9,'$ '.$shp_inv['shp_det'][$i]['ship_fob'],$rr);
			$worksheet1->write_string($ll,10,'US$ '.$shp_inv['shp_det'][$i]['amount'],$rr);
			$ll++;			
		}
		

		$worksheet1->set_row($ll, 2);
		$worksheet1->write_string($ll,0,'',$f8);
	  $worksheet1->merge_cells($ll,0,$ll,17); 
		$ll++;

		$worksheet1->write_string($ll,0,'TOTAL :');
		$worksheet1->merge_cells($ll,0,$ll,14);
		$worksheet1->write_string($ll,15,$shp_inv['amt']['qty'],$rr);
		$worksheet1->write_string($ll,16,'',$rr);
		$worksheet1->write_string($ll,17,'US$ '.$shp_inv['amt']['amount'],$rr);
		$ll++;			
		$worksheet1->write_string($ll,0,'SAY US DOLLARS '.$shp_inv['amt']['eng_amount']);
	  $worksheet1->merge_cells($ll,0,$ll,17); 
		$ll+=2;
		
		if($shp_inv['shp']['fab_des'])
		{
			$worksheet1->write_string($ll,0,'Fabric Description :',$f9);		
			$worksheet1->merge_cells($ll,0,$ll,10);
			$ll++;
			$txt = explode( chr(13).chr(10), $shp_inv['shp']['fab_des'] );	
			for($i=0; $i<sizeof($txt); $i++)
			{
				$worksheet1->write_string($ll,0,$txt[$i]);		
				$ll++;
			}
			$ll++;
		}
		
		if($shp_inv['shp']['fty_det'])
		{
			$worksheet1->write_string($ll,0,'MANUFACTURER NAME & ADDRESS :',$f9);	
			$worksheet1->merge_cells($ll,0,$ll,10);	
			$ll++;
			$txt = explode( chr(13).chr(10), $shp_inv['shp']['fty_det'] );	
			for($i=0; $i<sizeof($txt); $i++)
			{
				$worksheet1->write_string($ll,0,$txt[$i]);		
				$ll++;
			}
			$ll++;
		}		

		if($shp_inv['shp']['ship_det'])
		{
			$worksheet1->write_string($ll,0,'SHIPPER FULL NAME & ADDRESS :',$f9);	
			$worksheet1->merge_cells($ll,0,$ll,10);	
			$ll++;
			$txt = explode( chr(13).chr(10), $shp_inv['shp']['ship_det'] );	
			for($i=0; $i<sizeof($txt); $i++)
			{
				$worksheet1->write_string($ll,0,$txt[$i]);		
				$ll++;
			}
			$ll++;
		}	  
		if($shp_inv['shp']['benf_det'])
		{
			$worksheet1->write_string($ll,0,'BENEFICIARY ACCOUNT DETAIL AS BELOW :',$f9);	
			$worksheet1->merge_cells($ll,0,$ll,10);	
			$ll++;
			$txt = explode( chr(13).chr(10), $shp_inv['shp']['benf_det'] );	
			for($i=0; $i<sizeof($txt); $i++)
			{
				$worksheet1->write_string($ll,0,$txt[$i]);		
				$ll++;
			}
			$ll++;
		}	  
		if($shp_inv['shp']['ship_mark'])
		{
			$worksheet1->write_string($ll,0,'SHIPPING MARK :',$f9);	
			$worksheet1->merge_cells($ll,0,$ll,10);	
			$ll++;
			$txt = explode( chr(13).chr(10), $shp_inv['shp']['ship_mark'] );	
			for($i=0; $i<sizeof($txt); $i++)
			{
				$worksheet1->write_string($ll,0,$txt[$i]);		
				$ll++;
			}
			$ll++;
		}	
		if($shp_inv['shp']['nty_part'])
		{
			$worksheet1->write_string($ll,0,'Notify Party :',$f9);	
			$worksheet1->merge_cells($ll,0,$ll,10);	
			$ll++;
			$txt = explode( chr(13).chr(10), $shp_inv['shp']['nty_part'] );	
			for($i=0; $i<sizeof($txt); $i++)
			{
				$worksheet1->write_string($ll,0,$txt[$i]);		
				$ll++;
			}
			$ll++;
		}	
		if($shp_inv['shp']['stmt'])
		{
			$worksheet1->write_string($ll,0,'Statments :',$f9);	
			$worksheet1->merge_cells($ll,0,$ll,10);	
			$ll++;
			$txt = explode( chr(13).chr(10), $shp_inv['shp']['stmt'] );	
			for($i=0; $i<sizeof($txt); $i++)
			{
				$worksheet1->write_string($ll,0,$txt[$i]);		
				$ll++;
			}
			$ll++;
		}	 
		if($shp_inv['shp']['comm'])
		{
			$worksheet1->write_string($ll,0,'Comments :',$f9);	
			$worksheet1->merge_cells($ll,0,$ll,10);	
			$ll++;
			$txt = explode( chr(13).chr(10), $shp_inv['shp']['comm'] );	
			for($i=0; $i<sizeof($txt); $i++)
			{
				$worksheet1->write_string($ll,0,$txt[$i]);		
				$ll++;
			}
			$ll++;
		}		
		if($shp_inv['shp']['others'])
		{
			$worksheet1->write_string($ll,0,'OTHERS :',$f9);	
			$worksheet1->merge_cells($ll,0,$ll,10);	
			$ll++;
			$txt = explode( chr(13).chr(10), $shp_inv['shp']['others'] );	
			for($i=0; $i<sizeof($txt); $i++)
			{
				$worksheet1->write_string($ll,0,$txt[$i]);		
				$ll++;
			}
			$ll++;
		}		 

  	  $workbook->close();
	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "inv_th_print":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "inv_th_print":
 	check_authority('034',"view");
		$shp_rec = $ship_doc->get_inv($PHP_id);
		$shp_rec['shp']['invoice_of'] = $ship_doc->get_other_value($PHP_id,'INVOICE OF');
		$shp_rec['shp']['buying_agent'] = $ship_doc->get_other_value($PHP_id,'BUYING AGENT');
//---------------------------------- 資料庫作業結束 ------------------------------

include_once($config['root_dir']."/lib/class.pdf_ship_inv_th.php");
session_register	('main_rec');
$main_rec = array (	'fty'							=>	$shp_rec['shp']['fty'],
										'inv_num'					=>	$shp_rec['shp']['inv_num'],
										'date'						=>	$shp_rec['shp']['submit_date'],
										'invoice_of'			=>	$shp_rec['shp']['invoice_of'],
										'ship_by'					=>	$shp_rec['shp']['ship_by'],										
										'buying_agent'		=>	$shp_rec['shp']['buying_agent'],
										'consignee_name'	=>	$shp_rec['shp']['consignee_name'],
										'consignee_addr'	=>	$shp_rec['shp']['consignee_addr'],
										'ship_via'				=>	$shp_rec['shp']['ship_via'],
										'ship_date'				=>	$shp_rec['shp']['fv_etd'],
										'ship_from'				=>	$shp_rec['shp']['ship_from'],
										'ship_to'					=>	$shp_rec['shp']['ship_to'],
										'country_org'			=>	$shp_rec['shp']['country_org'],
										'payment'					=>	$shp_rec['shp']['payment'],
										);

$print_title="INVOICE";

$print_title1  = "our pdt. code : ".$shp_rec['shp']['ord_num'];
//$print_title2 = "VER.".($shp_rec['shp']['ver']+1);
$creator = $shp_rec['shp']['submit_user'];
$mark = $shp_rec['shp']['inv_num'];
$fty = $shp_rec['shp']['fty'];
$print_title2=$shp_rec['shp']['inv_num'].'_inv_th.pdf';


$pdf = new PDF_shidoc_inv_th('P','mm','A4');
//$pdf->SetAutoPageBreak('on',10);
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();

$pg_mk = 2;//TOTAL 40 ROW
$pdf->item_title($shp_rec['shp']['ship_term'],$shp_rec['shp']['des']);
$hanger_ttl = 0;
for($i=0; $i<sizeof($shp_rec['shp_det']); $i++)
{
	
	if(isset($shp_rec['shp_det'][$i]['tb_mk']) && $shp_rec['shp_det'][$i]['tb_mk'] == 1)
	{
		$parm = array();
		$pg_mk+=10;
		if($pg_mk > 40)
		{
			$pdf->AddPage();
			$pdf->item_title($shp_rec['shp']['ship_term'],$shp_rec['shp']['des']);
			$pg_mk = 2;
		}		
		$parm['DIVISION_CODE'] = $ship_doc->get_other_ord_value($shp_rec['shp_det'][$i]['ship_ord_id'],'DIVISION_CODE');
		$parm['SEASON_CODE'] = $ship_doc->get_other_ord_value($shp_rec['shp_det'][$i]['ship_ord_id'],'SEASON CODE');
		$parm['LICENCEE_CODE'] = $ship_doc->get_other_ord_value($shp_rec['shp_det'][$i]['ship_ord_id'],'LICENCEE CODE');
		$parm['LF_PM_NO'] = $ship_doc->get_other_ord_value($shp_rec['shp_det'][$i]['ship_ord_id'],'LF PM NO.');
		$parm['STYLE_NAME'] = $ship_doc->get_other_ord_value($shp_rec['shp_det'][$i]['ship_ord_id'],'STYLE NAME');
		$parm['GENDER'] = $ship_doc->get_other_ord_value($shp_rec['shp_det'][$i]['ship_ord_id'],'GENDER');
		$parm['PO'] = $shp_rec['shp_det'][$i]['cust_po'];
		$parm['CONTENT'] = $shp_rec['shp_det'][$i]['content'];
		$parm['BUYING_AGENT'] = $shp_rec['shp']['buying_agent'];
		$pdf->item_hend($parm);
	}
	$pg_mk++;
	if($pg_mk > 40)
	{
		$pdf->AddPage();
		$pdf->item_title($shp_rec['shp']['ship_term'],$shp_rec['shp']['des']);
		$pg_mk = 2;
	}
	$shp_rec['shp_det'][$i]['hanger'] = $ship_doc->get_other_ord_value($shp_rec['shp_det'][$i]['ship_ord_id'],'HANGER');
	$shp_rec['shp_det'][$i]['hanger_uprice'] = $ship_doc->get_other_ord_value($shp_rec['shp_det'][$i]['ship_ord_id'],'HANGER UPRICE');
	$hanger_ttl += ($shp_rec['shp_det'][$i]['hanger_uprice'] * $shp_rec['shp_det'][$i]['bk_qty']);
	$pdf->item_txt($shp_rec['shp_det'][$i]);
	
}



$pg_mk+=5;
if($pg_mk > 40)
{
	$pdf->AddPage();
	$pdf->item_title($shp_rec['shp']['ship_term'],$shp_rec['shp']['des']);
	$pg_mk = 2;
}

$pdf->item_total($shp_rec['amt'],$shp_rec['shp_charge'],$shp_rec['shp_det'][($i-1)]['des'],$hanger_ttl);


if($shp_rec['shp']['others'])
{
	
	$txt = explode( chr(13).chr(10), $shp_rec['shp']['others'] );
	$pg_mk+=(sizeof($txt));
	if($pg_mk > 40)
	{
		$pdf->AddPage();
		$pg_mk = 0;
	}
	$pdf->inv_txt('',$txt);
}


if($shp_rec['shp']['fty_det'])
{
	$txt = explode( chr(13).chr(10), $shp_rec['shp']['fty_det'] );
	if($shp_rec['shp']['mid_no'])$txt[] = "M.I.D. NO.: ".$shp_rec['shp']['mid_no'];
	$pg_mk+=(sizeof($txt)+2);
	if($pg_mk > 40)
	{
		$pdf->AddPage();
		$pg_mk = 0;
	}
	$pdf->inv_txt('MANUFACTURER NAME & ADDRESS :',$txt);
}


if($shp_rec['shp']['benf_det'])
{
	$txt = explode( chr(13).chr(10), $shp_rec['shp']['benf_det'] );
	$pg_mk+=(sizeof($txt)+1);
	if($pg_mk > 40)
	{
		$pdf->AddPage();
		$pg_mk = 0;
	}
	$pdf->inv_txt('BENEFICIARY ACCOUNT DETAIL AS BELOW :',$txt);
}



if($shp_rec['shp']['ship_mark'])
{
	$txt = explode( chr(13).chr(10), $shp_rec['shp']['ship_mark'] );
	$pg_mk+=(sizeof($txt)+1);
	if($pg_mk > 40)
	{
		$pdf->AddPage();
		$pg_mk = 0;
	}
	$pdf->inv_txt('SHIPPING MARK :',$txt);
}

$name=$shp_rec['shp']['inv_num'].'_inv_th.pdf';
$pdf->Output($name,'D');


break;	


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "shipdoc_i_check":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "shipdoc_i_check":
 	check_authority('034',"view");
 	$op['id'] = $PHP_id;

	page_display($op, '034', $TPL_SHIPDOC_INV_EDIT_CHECK);
	break;	


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "shipdoc_i_edit":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "shipdoc_i_m_edit":
 	check_authority('034',"view");
		$sort = " s_order.order_num, shipdoc_ord.cust_po , shipdoc_qty.color";
		$op = $ship_doc->get_inv($PHP_id, $sort);
/*		
		$op['shp'] = $shp_rec['shp'];
		$op['shp_det'] = $shp_rec['shp_det'];
		$op['shp_charge'] =$shp_rec['shp_charge'];
		$op['shp_charge'] =$shp_rec['shp_charge'];
*/	
		if(!$op['shp']['fty_det'])
		{
			$op['shp']['fty_det'] .= $SHIP_TO[$op['shp']['fty']]['Name'].chr(13).chr(10);
			$op['shp']['fty_det'] .= $SHIP_TO[$op['shp']['fty']]['Addr'].chr(13).chr(10);
			$op['shp']['fty_det'] .= "TEL : ".$SHIP_TO[$op['shp']['fty']]['TEL'].chr(13).chr(10);
			$op['shp']['fty_det'] .= "FAX : ".$SHIP_TO[$op['shp']['fty']]['FAX'].chr(13).chr(10);
		}
		
		if(!$op['shp']['ship_det'])
		{
			$op['shp']['ship_det'] .= $SHIP_TO[$op['shp']['fty']]['Name'].chr(13).chr(10);
			$op['shp']['ship_det'] .= $SHIP_TO[$op['shp']['fty']]['Addr'].chr(13).chr(10);
			$op['shp']['ship_det'] .= "TEL : ".$SHIP_TO[$op['shp']['fty']]['TEL'].chr(13).chr(10);
			$op['shp']['ship_det'] .= "FAX : ".$SHIP_TO[$op['shp']['fty']]['FAX'].chr(13).chr(10);
		}		

		$op['shp']['mid_select'] = $arry2->select($MID ,$op['shp']['mid_no'],"PHP_mid_no","select"); 
		$op['shp']['country_select'] = $arry2->select($SHIP_COUNTRY ,$op['shp']['country_org'],"PHP_country_org","select");  

		foreach($CONTAINER as $key => $value)
		{
			$conter[] = $key;
			$conter_name[] = $CONTAINER[$key]['name'];
		}
		$op['shp']['conter_select'] = $arry2->select($conter_name ,$op['shp']['conter'],"PHP_conter","select",'',$conter);  
		$op['shp']['constor_select'] = $arry2->select($conter_name ,$op['shp']['constor'],"PHP_constor","select",'',$conter);  


		$where_str = "WHERE cust='".$op['shp']['ord_cust']."'";
		$csgn = $cust->get_csge_fields('s_name',$where_str);
		if(!$csgn)$csgn=array('');
		$op['csgn_select'] = $arry2->select($csgn,$op['shp']['consignee'],'PHP_consignee','select','');

		for($i=0; $i<sizeof($SHIP_PO_TYPE); $i++) $po_key[] = $i+1;
		for($i=0; $i<sizeof($SHIP_FACILLITY); $i++) $fact_key[] = $i+1;
		for($i=0; $i<sizeof($SHIP_MODE); $i++) $mode_key[] = $i+1;
		for($i=0; $i<sizeof($SHIP_METHOD); $i++) $method_key[] = $i+1;



		if(isset($PHP_msg))$op['msg'][] = $PHP_msg;
	page_display($op, '034', $TPL_SHIPDOC_INV_EDIT_MULTI);
	break;	





#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_shipdoc_i_edit":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "do_shipdoc_i_m_edit":
 	check_authority('034',"view");
 	
 		if(!$ship_doc->check_inv($PHP_inv_num,$PHP_id))
 		{
 			$message = "invoice number is exist, please check again";
 			$rstr = "shipdoc.php?PHP_action=shipdoc_i_edit&PHP_id=".$PHP_id."&PHP_msg=".$message.$PHP_back_str;
 			redirect_page($rstr);
 		}
 	
 	
 	
 		$PHP_ship_addr = $PHP_ship_addr1."|".$PHP_ship_addr2;
 		$PHP_bill_addr = $PHP_bill_addr1."|".$PHP_bill_addr2;
 		if($PHP_consignee2)$PHP_consignee = $PHP_consignee2;
 		$parm = array(
 										'ship_term'		=>	$PHP_ship_term,
 										'ship_via'		=>	$PHP_ship_via,
 										'ship_from'		=>	$PHP_ship_from,
 										'ship_to'			=>	$PHP_ship_to,
 										'payment'			=>	$PHP_payment,
 										'des'					=>	$PHP_des,
 										'fv'					=>	$PHP_fv,
 										'fv_etd'			=>	$PHP_fv_etd,
 										'fv_eta'			=>	$PHP_fv_eta,
 										'mv'					=>	$PHP_mv,
 										'mv_etd'			=>	$PHP_mv_etd,
 										'mv_eta'			=>	$PHP_mv_eta,
 										'lc_no'				=>	$PHP_lc_no,
 										'lc_bank'			=>	$PHP_lc_bank,
 										'consignee'		=>	$PHP_consignee,
 										'ship_addr'		=>	$PHP_ship_addr,
 										'bill_addr'		=>	$PHP_bill_addr,
 										'fty_det'			=>	$PHP_fty_det,
 										'ship_det'		=>	$PHP_ship_det,
 										'benf_det'		=>	$PHP_benf_det,
 										'inv_num'			=>	$PHP_inv_num,
 										'ship_date'		=>	$PHP_ship_date,
 										'mid_no'			=>	$PHP_mid_no,
 										'country_org'	=>	$PHP_country_org,
 										'ship_mark'		=>	$PHP_ship_mark,
 										'others'			=>	$PHP_others,
 										'ship_by'			=>	$PHP_ship_by,
 										'carrier'			=>	$PHP_carrier,
 										'fab_des'			=>	$PHP_fab_des,
 										'dis_port'		=>	$PHP_dis_port,
 										'lc_date'			=>	$PHP_lc_date,
 										'cbm'					=>	$PHP_cbm,
 										
 										'nty_part'			=>	$PHP_nty_part,
 										'stmt'					=>	$PHP_stmt,
 										'comm'					=>	$PHP_comm,
 										'conter'				=>	$PHP_conter,
 										'constor'				=>	$PHP_constor,
 										 										
 										'id'						=>	$PHP_id
 									);
 
 									
 		$f1 = $ship_doc->edit_inv($parm);
 		
 		
 //原packing 修改項目		
		if($PHP_pack)			$ship_doc->update_field_main('pack',$PHP_pack,$PHP_id);
		$PHP_vendor = $PHP_vendor."|".$PHP_vendor2;
		if($PHP_vendor)		$ship_doc->update_field_main('vendor',$PHP_vendor,$PHP_id);

 		
 		
		foreach($PHP_quota_unit as $key => $value)
		{
			$f1=$order->update_field_num('ship_quota', $PHP_quota[$key],$key);  
			$f1=$order->update_field_num('quota_unit', $PHP_quota_unit[$key],$key);  
		}			
	
		foreach($PHP_style_num as $key => $value)
		{
			$f1=$ship_doc->update_field_det('style_num',$PHP_style_num[$key],$key); 		
			$f1=$ship_doc->update_field_det('content',$PHP_content[$key],$key); 
			$f1=$ship_doc->update_field_det('des',$PHP_ord_des[$key],$key);
			$f1=$ship_doc->update_field_det('ship_fob',$PHP_uprice[$key],$key);		
			$f1=$ship_doc->update_field_det('hts_cat',$PHP_hts_cat[$key],$key); 
		}		
		
		
		
		$op = $ship_doc->get_inv($PHP_id);
		$op['shp']['fty_det'] = str_replace( chr(13).chr(10), "<br>",  $op['shp']['fty_det'] );
		$op['shp']['ship_det'] = str_replace( chr(13).chr(10), "<br>", $op['shp']['ship_det'] );
		$op['shp']['benf_det'] = str_replace( chr(13).chr(10), "<br>", $op['shp']['benf_det'] );
		$op['shp']['ship_mark'] = str_replace( chr(13).chr(10), "<br>", $op['shp']['ship_mark'] );
		$op['shp']['others'] = str_replace( chr(13).chr(10), "<br>", $op['shp']['others'] );
		$op['shp']['fab_des'] = str_replace( chr(13).chr(10), "<br>", $op['shp']['fab_des'] );
		$op['shp']['nty_part'] = str_replace( chr(13).chr(10), "<br>", $op['shp']['nty_part'] );
		$op['shp']['stmt'] = str_replace( chr(13).chr(10), "<br>", $op['shp']['stmt'] );
		$op['shp']['comm'] = str_replace( chr(13).chr(10), "<br>", $op['shp']['comm'] );



		$mesg = "SUCCESS EDIT INVOICE ON  [".$op['shp']['inv_num']."]";
		$op['back_str'] = $PHP_back_str;
		$op['msg'][] = $mesg;
		
	page_display($op, '034', $TPL_SHIPDOC_INV_VIEW);
	break;	
	



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "pack_re_print":			
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "pack_re_print":   //  .......
	check_authority('032',"view");	
		$shp_rec = $ship_doc->get_pack($PHP_id);
		$shp_rec['shp']['TLC'] = $ship_doc->get_other_value($PHP_id,'TLC');
		$shp_rec['shp']['TT'] = $ship_doc->get_other_value($PHP_id,'TT');
		$shp_rec['shp']['secondary_mark'] = $ship_doc->get_other_value($PHP_id,'SECONDARY MARK');
		$shp_rec['shp']['side_mark'] = $ship_doc->get_other_value($PHP_id,'SIDE MARK');
$j = $k = -1;
$x = $y =0;
$content = array();
		for($i=0; $i<sizeof($shp_rec['shp_det']); $i++)
		{
			$key = $shp_rec['shp_det'][$i]['cust_po']."_".$shp_rec['shp_det'][$i]['color']."_".$shp_rec['shp_det'][$i]['fit'];			
			$size_key = $shp_rec['shp_det'][$i]['size_csv'];
			if(!isset($break[$key]))
			{
				$break[$key]['cust_po'] = $shp_rec['shp_det'][$i]['cust_po'];
				$break[$key]['color'] = $shp_rec['shp_det'][$i]['color'];
				$break[$key]['fit'] = $shp_rec['shp_det'][$i]['fit'];
				$break[$key]['size_csv'] = $shp_rec['shp_det'][$i]['size_csv'];
				$break[$key]['size'] = $shp_rec['shp_det'][$i]['size'];
				for($j=0; $j<sizeof($break[$key]['size']); $j++)$break[$key]['size_qty'][] = 0;
				$break[$key]['qty'] = 0;
			}
			if(!isset($break_sum[$size_key]))
			{
				for($j=0; $j<sizeof($shp_rec['shp_det'][$i]['size']); $j++)$break_sum[$size_key]['size_qty'][] = 0;
				$break_sum[$size_key]['qty'] = 0;
			}
			
			
			$size_qty = $shp_rec['shp_det'][$i]['size_qty'];
			for($j=0; $j<sizeof($size_qty); $j++)
			{
				$break[$key]['size_qty'][$j] += ($size_qty[$j] * $shp_rec['shp_det'][$i]['s_cnum']);
				$break[$key]['qty'] += ($size_qty[$j] * $shp_rec['shp_det'][$i]['s_cnum']);
				$break_sum[$size_key]['size_qty'][$j] +=($size_qty[$j] * $shp_rec['shp_det'][$i]['s_cnum']);
				$break_sum[$size_key]['qty'] +=($size_qty[$j] * $shp_rec['shp_det'][$i]['s_cnum']);
			}			
		
		}			
		
//---------------------------------- 資料庫作業結束 ------------------------------

include_once($config['root_dir']."/lib/class.pdf_shp_pack_re.php");
$pg_mk = 0;
session_register	('main_rec');
$main_rec = array (	'fty'							=>	$shp_rec['shp']['fty'],
										'inv_num'					=>	$shp_rec['shp']['inv_num'],
										'date'						=>	$shp_rec['shp']['fv_etd'],
										'consignee_name'	=>	$shp_rec['shp']['consignee_name'],
										'consignee_addr'	=>	$shp_rec['shp']['consignee_addr'],
										'TT'							=>	$shp_rec['shp']['TT'],
										'TLC'							=>	$shp_rec['shp']['TLC'],
										'ship_term'				=>	$shp_rec['shp']['ship_term'],
										'ship_from'				=>	$shp_rec['shp']['ship_from'],
										'ship_to'					=>	$shp_rec['shp']['ship_to'],
										'country_org'			=>	$shp_rec['shp']['country_org'],		
										'des'							=>	$shp_rec['shp']['det_des']	
										);

$print_title="PACKING LIST / WEIGHT LIST";
$creator = $shp_rec['shp']['submit_user'];
$mark = $shp_rec['shp']['inv_num'];
$fty = $shp_rec['shp']['fty'];


$pdf = new PDF_shp_pack_RE('L','mm','A4');
//$pdf->SetAutoPageBreak('on',10);
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();

$pdf->hend_title($main_rec);
$pg_mk = 7+sizeof($shp_rec['shp']['det_des']);
$po_mk = '';

for($i=0; $i<sizeof($shp_rec['shp_det']); $i++) //列印packing list
{
	$pg_mk ++;
	if($po_mk != $shp_rec['shp_det'][$i]['cust_po'] || $pg_mk > 33)
	{
		if($pg_mk > 33) //換頁
		{
			$pdf->AddPage();
			$pg_mk = 0;
		}		
		$pdf->ship_title($shp_rec['shp_det'][$i]['size'],$shp_rec['shp_det'][$i]['cust_po'],$shp_rec['shp']['unit']);
		$po_mk = $shp_rec['shp_det'][$i]['cust_po'];
		$pg_mk += 3;
	}
	$style_num = '';
	
	if($i > 0 && $shp_rec['shp_det'][$i]['style_num'] <> $shp_rec['shp_det'][($i-1)]['style_num'])
	{
		$style_num = $shp_rec['shp_det'][$i]['style_num'];
	}
	$pdf->ship_txt($shp_rec['shp_det'][$i],$style_num ); //print packing list
	
}
	$pg_mk += 7;
	$shp_rec['amt']['cbm'] = $shp_rec['shp']['cbm'];
	$shp_rec['amt']['unit'] = $shp_rec['shp']['unit'];
	$pdf->gd_total($shp_rec['amt']); //print packing list total



	$i=0;
	$size_csv = '';$po_num = '';
	foreach($break as $key => $value)
	{
		$pg_mk++;
		if($size_csv != $break[$key]['size_csv'] || $pg_mk > 33)
		{			
			if($pg_mk > 33) //換頁
			{
				$pdf->AddPage();
				$pg_mk = 0;
			}else if($i > 0){
				$pdf->break_total($break_sum[$break[$key]['size_csv']]);
				$pg_mk++;
			}			
			$pdf->break_title($break[$key]['size']);
			$size_csv = $break[$key]['size_csv'];			
			$pg_mk+=2;
		}
	
		if($po_num != $break[$key]['cust_po'])
		{
			$pdf->break_txt($break[$key],$break[$key]['cust_po']);
		}else{
			$pdf->break_txt($break[$key],'');
		}
		$po_num = $break[$key]['cust_po'];
		
		$i++;
	}
	$pg_mk++;
	if($pg_mk > 33) //換頁
	{
		$pdf->AddPage();
		$pdf->break_title($break[$key]['size']);
		$pg_mk = 3;
	}	
	$pdf->break_total($break_sum[$break[$key]['size_csv']]);
	
	$shp_rec['shp']['ship_mark'] = explode( chr(13).chr(10), $shp_rec['shp']['ship_mark']);
	$shp_rec['shp']['secondary_mark'] = explode( '<br>', $shp_rec['shp']['secondary_mark']);
	$shp_rec['shp']['side_mark'] = explode( '<br>', $shp_rec['shp']['side_mark']);
	$mark_size = sizeof($shp_rec['shp']['ship_mark']);
	if(sizeof($shp_rec['shp']['secondary_mark']) > $mark_size) $mark_size=sizeof($shp_rec['shp']['secondary_mark']);
	if(sizeof($shp_rec['shp']['side_mark']) > $mark_size) $mark_size=sizeof($shp_rec['shp']['side_mark']);
	$pg_mk += ($mark_size +3);
	if($pg_mk > 33) //換頁
	{
		$pdf->AddPage();
		$pdf->break_title($break[$key]['size']);
	}	
	$pdf->ship_mark($shp_rec['shp']['ship_mark'],$shp_rec['shp']['secondary_mark'],$shp_rec['shp']['side_mark'],$mark_size);
	

$name=$shp_rec['shp']['inv_num'].'_re_pack.pdf';
$pdf->Output($name,'D');


break;	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_ship_rmk":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "db_update":
		$mons=array('JAN' => '01','FEB' => '02','MAR' => '03','APL' => '04','MAY' => '05',
								'JUN' => '06','JLY' => '07','AUG' => '07','SEP' => '08','OCT' => '10',
								'NOV' => '11','DEC' => '12');

			$q_str = "SELECT fv_etd, id FROM shipdoc ORDER BY id";														

		$q_result = $mysql->query($q_str);		
		$rec = array();
		while($row = $mysql->fetch($q_result)) $rec[] = $row;    
    for($i=0; $i<sizeof($rec); $i++)
    {
    	if($rec[$i]['fv_etd'])
    	{
    		$tmp = explode('.',$rec[$i]['fv_etd']);
    		$tmp[0] = trim($tmp[0]);
    		$mm = $mons[$tmp[0]];
    		$yy_dd = explode(',',$tmp[1]);
    		$etd = trim($yy_dd[1]).'-'.$mm.'-'.trim($yy_dd[0]);
				$q_str =  "UPDATE shipdoc SET fv_etd='".$etd."'  WHERE id = '".$rec[$i]['id']."'";
				echo $rec[$i]['fv_etd'].'==>'.$q_str."<BR>";
				$q_result = $mysql->query($q_str);
			}
    };
    break;
    
    
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_ship_rmk":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "shipdoc_mm_rpt":
		if(!$PHP_year)$PHP_year=date('Y');
		if(!$PHP_month)$PHP_month=date('m');
		if(!$PHP_fty)
		{
			$message = "Please select factory first!";
			$redir_str='shipdoc.php?PHP_action=shipdoc&PHP_msg='.$message;
			redirect_page($redir_str);			
		}
		$rec = $ship_doc->get_mm_rpt(($PHP_year.'-'.$PHP_month),$PHP_fty);

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
	  HeaderingExcel('ord_pdt_tal.xls');
	 
	  // Creating a workbook
	  $workbook = new Workbook("-");

	  // Creating the first worksheet
	  $worksheet1 =& $workbook->add_worksheet('report');

		$now = $GLOBALS['THIS_TIME'];

	// 寫入 title

	  $formatot =& $workbook->add_format();
	  $formatot->set_size(10);
	  $formatot->set_align('center');
	  $formatot->set_color('white');
	  $formatot->set_pattern(1);
	  $formatot->set_fg_color('navy');
	  $formatot->set_num_format(4);
	  
	  $f3 =& $workbook->add_format(); //置右
	  $f3->set_size(10);
	  $f3->set_align('right');
	  $f3->set_num_format(4);
	  
	  $f5 =& $workbook->add_format();  //灰底白字置中
	  $f5->set_size(10);
	  $f5->set_align('center');
	  $f5->set_color('white');
	  $f5->set_pattern(1);
	  $f5->set_fg_color('grey');
	  
	  $f6 =& $workbook->add_format();  //灰底白字置右
	  $f6->set_size(10);
	  $f6->set_color('white');
	  $f6->set_pattern(1);
	  $f6->set_align('right');
	  $f6->set_fg_color('grey');
	  $f6->set_num_format(4);

	  $f7 =& $workbook->add_format();  //綠底白字置中
	  $f7->set_size(10);
	  $f7->set_align('center');
	  $f7->set_color('white');
	  $f7->set_pattern(1);
	  $f7->set_fg_color('green');
	  
	  $f8 =& $workbook->add_format();  //綠底白字置右
	  $f8->set_size(10);
	  $f8->set_color('white');
	  $f8->set_pattern(1);
	  $f8->set_align('right');
	  $f8->set_fg_color('green');	 
	  $f8->set_num_format(4); 
//title	  
	  $clumn = array(     25,     15,    15,        15,      10,       9,         9,      10,          10,        10,       10,        15,       11,       13,         13);
	  $title = array('Buyer','SCM NO','STYLE NO','PO NO','Descript','SHIP QTY','U/PRICE','AMOUNT','Price Trick','Damage', 'Other','Invoice NO', 'ETD','CMT PRICE','TOTAL CMT');
	  for ($i=0; $i< sizeof($clumn); $i++) $worksheet1->set_column(0,$i,$clumn[$i]);
	  $worksheet1->write_string(0,1,'Summary Monthly Report For THE MONTH of '.$PHP_year.'-'.$PHP_month);
	  $worksheet1->write_string(0,10,$THIS_TIME);
	  for ($i=0; $i< sizeof($title); $i++) $worksheet1->write_string(1,$i,$title[$i],$formatot);

//內容
		$j=2;
		foreach($rec as $f_key => $f_value)
		{
			foreach($rec[$f_key]['inv'] as $key => $value)
			{
				
				$rpt = $rec[$f_key]['inv'][$key]['det'];
				for($i=0; $i<sizeof($rpt); $i++)
				{
						$worksheet1->write_string($j,0,$rpt[$i]['cust_name']);
						$worksheet1->write_string($j,1,$rpt[$i]['ord_num']);
						$worksheet1->write_string($j,2,$rpt[$i]['style_num']);
						$worksheet1->write_string($j,3,$rpt[$i]['cust_po']);
						$worksheet1->write_string($j,4,$rpt[$i]['style']);
						$worksheet1->write_number($j,5,$rpt[$i]['qty'],$f3);
						$worksheet1->write_number($j,6,$rpt[$i]['ship_fob'],$f3);
						$worksheet1->write_number($j,7,$rpt[$i]['amount'],$f3);
						$worksheet1->write_string($j,8,'');
						$worksheet1->write_string($j,9,'');
						$worksheet1->write_string($j,10,'');
						$worksheet1->write_string($j,11,$rpt[$i]['inv_num']);
						$worksheet1->write_string($j,12,$rpt[$i]['fv_etd']);
						$worksheet1->write_number($j,13,$rpt[$i]['fty_cm'],$f3);
						$worksheet1->write_number($j,14,$rpt[$i]['cm_amt'],$f3);
						$j++;
				}
				$worksheet1->write_string($j,0,'Invoice Summary',$f5);
				$worksheet1->write_string($j,1,'',$f5);
				$worksheet1->write_string($j,2,'',$f5);
				$worksheet1->write_string($j,3,'',$f5);	
				$worksheet1->write_string($j,4,'',$f5);			
				$worksheet1->write_number($j,5,$rec[$f_key]['inv'][$key]['qty'],$f6);
				$worksheet1->write_string($j,6,'',$f5);
				$worksheet1->write_number($j,7,$rec[$f_key]['inv'][$key]['amount'],$f6);
				$worksheet1->write_number($j,8,$rec[$f_key]['inv'][$key]['ticket_fee'],$f6);
				$worksheet1->write_number($j,9,$rec[$f_key]['inv'][$key]['damage_fee'],$f6);
				$worksheet1->write_number($j,10,$rec[$f_key]['inv'][$key]['other_fee'],$f6);
				$worksheet1->write_string($j,11,'',$f5);
				$worksheet1->write_string($j,12,'',$f5);
				$worksheet1->write_string($j,13,'',$f5);
				$worksheet1->write_string($j,14,'',$f5);
				$j++;				
			}
				$worksheet1->write_string($j,0,'Total',$f7);
				$worksheet1->write_string($j,1,'',$f7);
				$worksheet1->write_string($j,2,'',$f7);
				$worksheet1->write_string($j,3,'',$f7);	
				$worksheet1->write_string($j,4,'',$f7);			
				$worksheet1->write_number($j,5,$rec[$f_key]['qty'],$f8);
				$worksheet1->write_string($j,6,'',$f7);
				$worksheet1->write_number($j,7,$rec[$f_key]['amount'],$f8);
				$worksheet1->write_string($j,8,'',$f7);
				$worksheet1->write_string($j,9,'',$f7);
				$worksheet1->write_string($j,10,'',$f7);
				$worksheet1->write_string($j,11,'',$f7);
				$worksheet1->write_string($j,12,'',$f7);
				$worksheet1->write_string($j,13,'',$f7);
				$worksheet1->write_number($j,14,$rec[$f_key]['cm_amt'],$f8);				
				$j++;	
				$j++;		
		}

  $workbook->close();
	break; 

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "tiptop_mm_rpt":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "tiptop_mm_rpt":

		if(!$PHP_year)$PHP_year=date('Y');
		if(!$PHP_month)$PHP_month=date('m');
		if(!$PHP_fty)
		{
			$message = "Please select factory first!";
			$redir_str='shipdoc.php?PHP_action=shipdoc&PHP_msg='.$message;
			redirect_page($redir_str);			
		}
		$rec = $ship_doc->get_mm_rpt_tiptop(($PHP_year.'-'.$PHP_month),$PHP_fty);

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
	  HeaderingExcel($PHP_fty.'_'.$PHP_year.'_'.$PHP_month.'_ord_pdt_tal.xls');
	 
	  // Creating a workbook
	  $workbook = new Workbook("-");

	  // Creating the first worksheet
	  $worksheet1 =& $workbook->add_worksheet('report');

		$now = $GLOBALS['THIS_TIME'];

	// 寫入 title

	  $formatot =& $workbook->add_format();
	  $formatot->set_size(10);
	  $formatot->set_align('center');
	  $formatot->set_color('white');
	  $formatot->set_pattern(1);
	  $formatot->set_fg_color('navy');
	  $formatot->set_num_format(4);
	  
	  $f3 =& $workbook->add_format(); //置右
	  $f3->set_size(10);
	  $f3->set_align('right');
	  $f3->set_num_format(4);
	  
	  $f5 =& $workbook->add_format();  //灰底白字置中
	  $f5->set_size(10);
	  $f5->set_align('center');
	  $f5->set_color('white');
	  $f5->set_pattern(1);
	  $f5->set_fg_color('grey');
	  
	  $f6 =& $workbook->add_format();  //灰底白字置右
	  $f6->set_size(10);
	  $f6->set_color('white');
	  $f6->set_pattern(1);
	  $f6->set_align('right');
	  $f6->set_fg_color('grey');
	  $f6->set_num_format(4);

	  $f7 =& $workbook->add_format();  //綠底白字置中
	  $f7->set_size(10);
	  $f7->set_align('center');
	  $f7->set_color('white');
	  $f7->set_pattern(1);
	  $f7->set_fg_color('green');
	  
	  $f8 =& $workbook->add_format();  //綠底白字置右
	  $f8->set_size(10);
	  $f8->set_color('white');
	  $f8->set_pattern(1);
	  $f8->set_align('right');
	  $f8->set_fg_color('green');	 
	  $f8->set_num_format(4); 
//title	  
	  $clumn = array(     6,        10,         9,         9,            12,         9,       7,         9,         9,        15,      5,     5,      6,            12,         9,        10,        10,     5,     8,     8,        10,            10,        11,          12);
	  $title = array('單別','出貨日期','帳款客戶','內外銷別','收款客戶編號','客戶簡稱','發票別','科目分類','人員編號','部門編號', '稅別','幣別', '匯率','收款條件編號','已收金額','收款銀行','訂單單號','項次','品牌','商品','品名規格','實際出貨數量','原幣金額','原出貨單號');
	  for ($i=0; $i< sizeof($clumn); $i++) $worksheet1->set_column(0,$i,$clumn[$i]);
	  $worksheet1->write_string(0,1,'Tiptop 匯出資料 '.$PHP_year.'-'.$PHP_month);
	  $worksheet1->write_string(0,10,$THIS_TIME);
	  for ($i=0; $i< sizeof($title); $i++) $worksheet1->write_string(1,$i,$title[$i],$formatot);

//內容
		$j=2;
		foreach($rec as $f_key => $f_value)
		{
			foreach($rec[$f_key]['inv'] as $key => $value)
			{
				
				$rpt = $rec[$f_key]['inv'][$key]['det'];
				for($i=0; $i<sizeof($rpt); $i++)
				{
						$dept_v = $order->get_field_value('dept','',$rpt[$i]['ord_num'],'s_order');
						$chief = $dept->get_fields("chief"," where dept_code='$dept_v'");
						$emp_id = $user->get_fields("emp_id"," where login_id='$chief[0]'");
						$dept_v = $apb->get_apb_dept($dept_v);
						
						$worksheet1->write_string($j,0,'CR41');
						$worksheet1->write_string($j,1,$rpt[$i]['ship_date']);
						$worksheet1->write_string($j,2,$rpt[$i]['uni_no']);
						$worksheet1->write_number($j,3,'2');
						$worksheet1->write_string($j,4,'');	//$rpt[$i]['cust_init_name']
						$worksheet1->write_string($j,5,'');	//$rpt[$i]['cust_s_name']
						$worksheet1->write_number($j,6,'99');
						$worksheet1->write_number($j,7,'11');
						$worksheet1->write_number($j,8,$emp_id[0]);
						$worksheet1->write_string($j,9,$dept_v);
						$worksheet1->write_string($j,10,'S299');
						$worksheet1->write_string($j,11,'USD');
						$worksheet1->write_number($j,12,$rate->get_rate('USD',$rpt[$i]['ship_date']),$f3);
						$worksheet1->write_string($j,13,'CR0003');
						$worksheet1->write_string($j,14,'0');
						$worksheet1->write_number($j,15,'1509'); // 華南 1509 台銀 ?
						$worksheet1->write_string($j,16,'');	//$rpt[$i]['ord_num']
						$worksheet1->write_number($j,17,$i+1);
						$worksheet1->write_string($j,18,$dept_v);
						$worksheet1->write_string($j,19,'',$f3);
						$worksheet1->write_string($j,20,'成衣');
						$worksheet1->write_number($j,21,$rpt[$i]['qty'],$f3);
						$worksheet1->write_number($j,22,$rpt[$i]['ttl_amt'],$f3);
						$worksheet1->write_string($j,23,$rpt[$i]['inv_num']);
						$j++;
				}
			}
		}

	$workbook->close();
	break;
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_ship_rmk":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "do_fv_etd_edit":
	  $ship_doc->update_field_main('fv_etd',$PHP_fv_etd,$PHP_id);

		$message = "Success update FV ETD!";
		$redir_str='shipdoc.php?PHP_action=shipdoc_p_view&PHP_id='.$PHP_id.'&PHP_msg='.$message;
		redirect_page($redir_str);			
		 
  break;  
  
//-------------------------------------------------------------------------

}   // end case ---------

?>
