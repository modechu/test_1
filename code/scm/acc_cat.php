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




switch ($PHP_action) {



#++++++++++++++    SAMPLE ORDER +++++++++++++  2007/09/11  +++++++++++++++++
#		 job 73    副料類別 
#++++++++++++++++++++++++++++++++++++++++++++  2007/09/11  +++++++++++++++++

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "acc_cat":			job 73
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	  case "acc_cat":
		check_authority('051',"view");
		
		if (!$op = $acc->search_cat()) {
			$op['msg']= $acc->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$op['msg'] = $acc->msg->get(2);
 
		page_display($op,'051', $TPL_ACC_CAT);   	    
	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_acc_cat_add":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_acc_cat_add":

		check_authority('054',"add");
		$acc_key = $acc->get_max_key();
		$acc_key++;
		$parm = array(	"acc_key"			=>	$acc_key,
										"name"				=>	$PHP_name,
					);

		$f1 = $acc->add_cat($parm);

	if (!$op = $acc->search_cat()) {
			$op['msg']= $acc->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$op['msg'] = $acc->msg->get(2);
 		$op['msg'][] = 'successfully add accessory category : '.$PHP_name;
		page_display($op,'051', $TPL_ACC_CAT);    	    

	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "acc_cat_update":			job 73
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "acc_cat_update":

		check_authority('051',"edit");
		$op['acc_cat'] = $acc->get_cat($PHP_id);
//		$op['msg'][] = "Up :".$op['dept']['dept_code'];
		page_display($op,'051', $TPL_ACC_CAT_UPDATE);   	    

		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_add":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_acc_cat_updat":

		check_authority('051',"edit");

		$argv = array(	"id"		=> $PHP_id,
										"name"	=> $PHP_name,
						);
		if (!$acc->update_cat($argv)) {
			$op['msg'] = $dept->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;	    
		}

	if (!$op = $acc->search_cat()) {
			$op['msg']= $acc->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$op['msg'] = $acc->msg->get(2);
 		$op['msg'][] = 'successfully Edit accessory category : '.$PHP_name;
		page_display($op,'051', $TPL_ACC_CAT);    	    
		
	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "chg_unit_ajax":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "chg_unit_ajax":   
	
		$unit_change[0]['S'] = 'meter';
		$unit_change[0]['B'] = 'yd';
		$unit_change[0]['R'] = '1.09361';
		$unit_change[1]['S'] = 'kg';
		$unit_change[1]['B'] = 'lb';
		$unit_change[1]['R'] = '2.20462';
		$unit_change[2]['S'] = 'pc';
		$unit_change[2]['B'] = 'gross';
		$unit_change[2]['R'] = '144';

		$unit_change[3]['S'] = 'pc';
		$unit_change[3]['B'] = 'dz';
		$unit_change[3]['R'] = '12';

		$unit_change[4]['S'] = 'dz';
		$unit_change[4]['B'] = 'gross';
		$unit_change[4]['R'] = '12';

		$unit_change[5]['S'] = 'inch';
		$unit_change[5]['B'] = 'ft';
		$unit_change[5]['R'] = '12';

		$unit_change[6]['S'] = 'inch';
		$unit_change[6]['B'] = 'yd';
		$unit_change[6]['R'] = '39.37';
		
		$unit_change[5]['S'] = 'inch';
		$unit_change[5]['B'] = 'meter';
		$unit_change[5]['R'] = '36';

		$unit_change[7]['S'] = 'ft';
		$unit_change[7]['B'] = 'yd';
		$unit_change[7]['R'] = '3.28084';

		$unit_change[7]['S'] = 'ft';
		$unit_change[7]['B'] = 'meter';
		$unit_change[7]['R'] = '3';

		
		$chg = 0;
	
		$qty=$PHP_qty;
		for ($i=0; $i<sizeof($unit_change); $i++)
		{
			if ($unit_change[$i]['S'] == $PHP_ounit && $unit_change[$i]['B'] == $PHP_nunit)
			{
				$qty= $qty / $unit_change[$i]['R'];
				$chg = 1;
				break;
			}
			if ($unit_change[$i]['B'] == $PHP_ounit && $unit_change[$i]['S'] == $PHP_nunit)
			{
				$qty= $qty * $unit_change[$i]['R'];
				$chg = 1;
				break;
			}
		}
		if ($PHP_ounit == $PHP_nunit) $chg = 1;
//		$qty = number_format($qty, 2);
//		echo $qty;
			if ($chg == 1)
			{
				printf('%.2f',$qty);	
			}else{
				echo "no|".$PHP_ounit."|".$PHP_nunit;
			}
			
break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "count_range_ajax":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "count_range_ajax":
		$qty=$PHP_qty;
		$id = explode('|',$PHP_det_id);
		for ($i=0; $i< sizeof($id); $i++)
		{
			$f1=$apply->update_fields_id('range',$PHP_range, $id[$i], $PHP_table);
			$f1=$apply->update_fields_id('po_unit',$PHP_unit, $id[$i], $PHP_table);
		}	
		$tmp_rag = $qty * ($PHP_range / 100);
		$qty = $qty + $tmp_rag;
	//	$qty = number_format($qty, 2);
	//	echo $qty;
		printf('%.2f',$qty);
break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "add_po_qty":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "add_po_qty":
		$id = explode('|',$PHP_det_id);
		$s_total=0;
		$tmp=0;
		for ($i=0; $i< sizeof($id); $i++)		//取得每項的請購量,並加總
		{
			$where_str = "id = ".$id[$i];
			$row[$i]=$po->get_det_field('ap_qty',$PHP_table,$where_str);
			$s_total = $s_total + $row[$i]['ap_qty'];
		}		
		for ($i=0; $i< (sizeof($id)-1); $i++)		//計算每項採購量(平分)
		{
			$po_qty[$i] = $PHP_qty * ($row[$i]['ap_qty'] / $s_total);
			$tmp = $tmp + $po_qty[$i];
		}	
		$tmp_qty = $PHP_qty - $tmp;
		$po_qty[$i] = $tmp_qty;
		
		
		for ($i=0; $i< sizeof($id); $i++)		//加入DB
		{
			$f1=$apply->update_fields_id('po_qty',$po_qty[$i], $id[$i], $PHP_table);
			$f1=$apply->update_fields_id('po_spare',$PHP_det_id, $id[$i], $PHP_table);	
			$f1=$apply->update_fields_id('prics',$PHP_price, $id[$i], $PHP_table);	
			$f1=$apply->update_fields_id('po_eta',$PHP_eta, $id[$i], $PHP_table);	
		}	
		
			$parm = array (	'field_name'	=>	'price1',
											'field_value'	=>	$PHP_price,
											'code'				=>	$PHP_mat_code
										);
		//計算總金額
//		$pa_num = "PA".substr($PHP_po_num,2);						
		$where = "AND ap.ap_num = '".$PHP_ap_num."'";
		$total=$po->count_totoal_price($PHP_table,$where);		
		$f1=$apply->update_fields('po_total', $total, $PHP_ap_num);	//儲存總金額
		$f1=$apply->update_fields('po_user',$GLOBALS['SCACHE']['ADMIN']['login_id'], $PHP_ap_num); //記入申請人
		$f1=$apply->update_fields('po_date',$TODAY, $PHP_ap_num); //記錄申請日

		$po_num=str_replace("A", "O",$PHP_ap_num);
		$message = "Successfully add P/O Detial : ".$po_num."record。";
		$log->log_add(0,"54A",$message);
		
		
		if ($PHP_mat_cat =='l')$f1 = $lots->update_field_code($parm);	//上傳主料單價
		if ($PHP_mat_cat =='a')$f1 = $acc->update_field_code($parm);	//上傳副料單價
		$message = "Successfully add qty for P/O # :".$po_num."| PO Q'ty : ".$PHP_qty."&nbsp;".$PHP_unit."&nbsp;&nbsp;&nbsp; Range  : ".$PHP_range."% &nbsp;&nbsp;&nbsp; Price : @ $".$PHP_price;
		$message = $message."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=button size=30 class=select_e value='EDIT' onclick=\"po_edit_p2('$PHP_id')\";>|$PHP_eta";
		echo $message;
break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "add_po_toler":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "add_po_toler":

		$f1=$apply->update_fields('toler', $PHP_toler, $PHP_po_num);	//儲存總金額		

		$PHP_po_num=str_replace("A", "O",$PHP_po_num);
		
		$message = "Successful modification  Tolerance for P/O # :".$PHP_po_num;
		echo $message;
break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_view":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "po_view":
		check_authority(5,4,"view");	

	$log_where = "AND item <> 'special'";	
	$op = $po->get($PHP_aply_num,$log_where);
	if (isset($op['ap_det']))
	{
				$op['ap_det2']=$po->grout_ap($op['ap_det']);
	}
	
	if ($op['ap']['arv_area'])
	{
		$op['ap']['ship_name'] = $SHIP_TO[$op['ap']['arv_area']]['Name'];
		$op['ap']['ship_addr'] = $SHIP_TO[$op['ap']['arv_area']]['Addr'];
		$op['ap']['ship_tel']  = $SHIP_TO[$op['ap']['arv_area']]['TEL'];
		$op['ap']['ship_fax']  = $SHIP_TO[$op['ap']['arv_area']]['FAX'];
		$op['ap']['ship_attn'] = $SHIP_TO[$op['ap']['arv_area']]['Attn'];
	}
	
	if (isset($PHP_SCH_num))
	{
		$back_str="&PHP_dept_code=".$PHP_dept_code."&PHP_SCH_num=".$PHP_SCH_num."&PHP_SCH_fty=".$PHP_SCH_fty."&PHP_SCH_cust=".$PHP_SCH_cust."&PHP_SCH_supl=".$PHP_SCH_supl."&PHP_sr_startno=".$PHP_sr_startno;
	}else{
		$back_str="&PHP_dept_code=&PHP_SCH_num=&PHP_SCH_fty=&PHP_SCH_cust=&PHP_SCH_supl=&PHP_sr_startno=";
	}
	$op['back_str']=$back_str;
	
	$op['rmk_item'] = $arry2->select($PO_ITEM,'','PHP_item','select','');

	$ary=array('A','B','C','D','E','F','G','H','I');
	for ($i=0; $i<sizeof($ary); $i++)
	{
		if ($op['ap']['usance'] == $ary[$i])	$op['ap']['usance']=$usance[$i];
	}
				
	for ($i=0; $i< 4; $i++)
	{
		if ($op['ap']['dm_way'] == $dm_way[1][$i]) $op['ap']['dm_way']=$dm_way[0][$i];
	}

	
	if (isset($PHP_message)) $op['msg'][]=$PHP_message;
	if (!isset($PHP_rev)) $PHP_rev='';
	if ($op['ap']['po_rev'] > 0 || $op['ap']['po_apv_user']) $op['ap']['po_rev'] =$op['ap']['po_rev']+1;
	
	if ($PHP_rev == 1)
	{
		page_display($op, 5, 4, $TPL_PO_SHOW_REV);
	}else{
		page_display($op, 5, 4, $TPL_PO_SHOW);			    	    
	}
		break;
		

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_apply_logs_add":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "do_apply_logs_add":
//	check_authority(5,1,"view");	
	
	
	$parm= array(	'ap_num'		=>	$PHP_aply_num,
								'item'			=>	$PHP_item,
								'des'				=>	$PHP_des,
								'user'			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
								'k_date'		=>	date('Y-m-d')
				);
	$f1=$apply->add_log($parm);
	
	if ($f1)
	{
		$po_num=str_replace("A", "O",$PHP_aply_num);
		$message="Successfully add log :".$po_num;
		$op['msg'][]=$message;
//		$log->log_add(0,"54E",$message);
	}else{
		$op['msg']= $apply->msg->get(2);
		$message = $op['msg'][0];
	}	
	$redir_str = "po.php?PHP_action=po_view&PHP_aply_num=".$PHP_aply_num."&PHP_message=".$message.$PHP_back_str;
	redirect_page($redir_str);


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_edit":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "po_edit":
		check_authority(5,4,"edit");	

		//echo $PHP_aply_num;
	$op = $po->get($PHP_aply_num);
	if (isset($op['ap_det']))
	{
		$op['ap_det2'][0] = $op['ap_det'][0];
		$op['ap_det2'][0]['orders'][0] = $op['ap_det'][0]['ord_num'];
		$op['ap_det2'][0]['ids'] = $op['ap_det'][0]['id'];
		if ($op['ap_det'][0]['po_unit']){$units = $op['ap_det'][0]['po_unit'];}else{$units = $op['ap_det'][0]['unit'];}
		$op['ap_det2'][0]['Unit_select'] = $po->get_unit_group($units,$op['ap_det'][0]['id']);

		$k=1;
		for ($i=1; $i<sizeof($op['ap_det']); $i++)
		{
			$mk=0;	$order_add=0;
			$x=1;
			for ($j=0; $j< sizeof($op['ap_det2']); $j++)
			{
				if ($op['ap_det2'][$j]['mat_code'] == $op['ap_det'][$i]['mat_code'] && $op['ap_det2'][$j]['color'] == $op['ap_det'][$i]['color'] && $op['ap_det2'][$j]['unit'] == $op['ap_det'][$i]['unit'] && $op['ap_det2'][$j]['eta'] == $op['ap_det'][$i]['eta'])
				{
					$op['ap_det2'][$j]['ap_qty'] = $op['ap_det'][$i]['ap_qty'] +$op['ap_det2'][$j]['ap_qty'];
					$op['ap_det2'][$j]['po_qty'] = $op['ap_det'][$i]['po_qty'] +$op['ap_det2'][$j]['po_qty'];
					for ($k =0; $k < sizeof($op['ap_det2'][$j]['orders']); $k++)
					{
						if ($op['ap_det2'][$j]['orders'][$k] == $op['ap_det'][$i]['ord_num'])
						{
							$order_add =1;
							break;
						}
					}
					if ($order_add == 0)	$op['ap_det2'][$j]['orders'][] = $op['ap_det'][$i]['ord_num'];
					$op['ap_det2'][$j]['ids'] = $op['ap_det2'][$j]['ids']."|".$op['ap_det'][$i]['id'];
					$x++;
					$mk = 1;
				}
			}
			
			if ($mk == 0)
			{
				$op['ap_det2'][$k] = $op['ap_det'][$i];
				$op['ap_det2'][$k]['orders'][0] = $op['ap_det'][$i]['ord_num'];
				$op['ap_det2'][$k]['ids'] = $op['ap_det'][$i]['id'];
				if ($op['ap_det'][$i]['po_unit']){$units = $op['ap_det'][$i]['po_unit'];}else{$units = $op['ap_det'][$i]['unit'];}
				$op['ap_det2'][$i]['Unit_select'] = $po->get_unit_group($units,$op['ap_det'][$i]['id']);

				$k++;
			}
		}
	}
	if (isset($op['ap_spec']))
	{
		for ($i=0; $i<sizeof($op['ap_spec']); $i++)
		{
				if ($op['ap_spec'][$i]['po_unit']){$units = $op['ap_spec'][$i]['po_unit'];}else{$units = $op['ap_spec'][$i]['unit'];}
				$op['ap_spec'][$i]['Unit_select'] = $po->get_unit_group($units,"e".$op['ap_spec'][$i]['id']);
		}
	}

	if ($op['ap']['arv_area'])
	{
		$op['ap']['ship_name'] = $SHIP_TO[$op['ap']['arv_area']]['Name'];
		$op['ap']['ship_addr'] = $SHIP_TO[$op['ap']['arv_area']]['Addr'];
		$op['ap']['ship_tel']  = $SHIP_TO[$op['ap']['arv_area']]['TEL'];
		$op['ap']['ship_fax']  = $SHIP_TO[$op['ap']['arv_area']]['FAX'];
		$op['ap']['ship_attn'] = $SHIP_TO[$op['ap']['arv_area']]['Attn'];
	}

		$back_str="?PHP_action=po_search".$PHP_back_str;
		$op['back_str']=$back_str;
		$op['edit'] = 1;
//	if (isset($PHP_message)) $op['msg'][]=$PHP_message;

		page_display($op, 5, 4, $TPL_PO_ADD);			    	    
		break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_revise":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "po_revise":
		check_authority(5,4,"edit");	

	$f1=$apply->update_2fields('po_apv_user', 'po_apv_date','','0000-00-00', $PHP_id);
	$f1=$apply->update_2fields('po_cfm_user', 'po_cfm_date','','0000-00-00', $PHP_id);
	$f1=$apply->update_2fields('po_sub_date', 'po_sub_date','','0000-00-00', $PHP_id);
	$f1=$apply->update_fields('status',6, $PHP_aply_num);
	$f1=$apply->update_fields('po_rev',($PHP_rev_time+1), $PHP_aply_num);		

	$op = $po->get($PHP_aply_num);
	if (isset($op['ap_det']))
	{
		$op['ap_det2'][0] = $op['ap_det'][0];
		$op['ap_det2'][0]['orders'][0] = $op['ap_det'][0]['ord_num'];
		$op['ap_det2'][0]['ids'] = $op['ap_det'][0]['id'];
		if ($op['ap_det'][0]['po_unit']){$units = $op['ap_det'][0]['po_unit'];}else{$units = $op['ap_det'][0]['unit'];}
		$op['ap_det2'][0]['Unit_select'] = $po->get_unit_group($units,$op['ap_det'][0]['id']);

		$k=1;
		for ($i=1; $i<sizeof($op['ap_det']); $i++)
		{
			$mk=0;
			$x=1;	$order_add=0;
			for ($j=0; $j< sizeof($op['ap_det2']); $j++)
			{
				if ($op['ap_det2'][$j]['mat_code'] == $op['ap_det'][$i]['mat_code'] && $op['ap_det2'][$j]['color'] == $op['ap_det'][$i]['color'] && $op['ap_det2'][$j]['unit'] == $op['ap_det'][$i]['unit'] && $op['ap_det2'][$j]['eta'] == $op['ap_det'][$i]['eta'])
				{
					$op['ap_det2'][$j]['ap_qty'] = $op['ap_det'][$i]['ap_qty'] +$op['ap_det2'][$j]['ap_qty'];
					$op['ap_det2'][$j]['po_qty'] = $op['ap_det'][$i]['po_qty'] +$op['ap_det2'][$j]['po_qty'];
					for ($k =0; $k < sizeof($op['ap_det2'][$j]['orders']); $k++)
					{
						if ($op['ap_det2'][$j]['orders'][$k] == $op['ap_det'][$i]['ord_num'])
						{
							$order_add =1;
							break;
						}
					}
					if ($order_add == 0)	$op['ap_det2'][$j]['orders'][] = $op['ap_det'][$i]['ord_num'];
					$op['ap_det2'][$j]['ids'] = $op['ap_det2'][$j]['ids']."|".$op['ap_det'][$i]['id'];
					$x++;
					$mk = 1;
				}
			}
			
			if ($mk == 0)
			{
				$op['ap_det2'][$k] = $op['ap_det'][$i];
				$op['ap_det2'][$k]['orders'][0] = $op['ap_det'][$i]['ord_num'];
				$op['ap_det2'][$k]['ids'] = $op['ap_det'][$i]['id'];
				if ($op['ap_det'][$i]['po_unit']){$units = $op['ap_det'][$i]['po_unit'];}else{$units = $op['ap_det'][$i]['unit'];}
				$op['ap_det2'][$i]['Unit_select'] = $po->get_unit_group($units,$op['ap_det'][$i]['id']);

				$k++;
			}
		}
	}
	if (isset($op['ap_spec']))
	{
		for ($i=0; $i<sizeof($op['ap_spec']); $i++)
		{
				if ($op['ap_spec'][$i]['po_unit']){$units = $op['ap_spec'][$i]['po_unit'];}else{$units = $op['ap_spec'][$i]['unit'];}
				$op['ap_spec'][$i]['Unit_select'] = $po->get_unit_group($units,"e".$op['ap_spec'][$i]['id']);
		}
	}
	$tmp_num = substr($PHP_aply_num,1);
	$po_num = "PO".$tmp_num;
	$f1=$apply->update_fields('po_num',$po_num, $PHP_aply_num);
	$f1=$apply->update_fields('status','6', $PHP_aply_num);

	if ($op['ap']['arv_area'])
	{
		$op['ap']['ship_name'] = $SHIP_TO[$op['ap']['arv_area']]['Name'];
		$op['ap']['ship_addr'] = $SHIP_TO[$op['ap']['arv_area']]['Addr'];
		$op['ap']['ship_tel']  = $SHIP_TO[$op['ap']['arv_area']]['TEL'];
		$op['ap']['ship_fax']  = $SHIP_TO[$op['ap']['arv_area']]['FAX'];
		$op['ap']['ship_attn'] = $SHIP_TO[$op['ap']['arv_area']]['Attn'];
	}

//		$back_str="?PHP_action=po_search".$PHP_back_str;
//		$op['back_str']=$back_str;
		$op['revise'] = 1;
//	if (isset($PHP_message)) $op['msg'][]=$PHP_message;

		page_display($op, 5, 4, $TPL_PO_ADD);			    	    
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_search":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "po_search":
	check_authority(5,4,"view");	

	if (!$op = $po->search(1, $PHP_dept_code)) {  
				$op['msg']= $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

		for ($i=0; $i<sizeof($op['apply']); $i++)
		{
			if ($op['apply'][$i]['special'] == 2){$table = 'ap_special';}else{$table = 'ap_det';}
			$where_str = "ap_num ='".$op['apply'][$i]['ap_num']."' GROUP BY ap_num";
			
			$row = $po->get_det_field('min(po_eta) as eta',$table,$where_str);
			$op['apply'][$i]['eta'] = $row['eta'];
		}
			


	$back_str="&PHP_dept_code=".$PHP_dept_code."&PHP_SCH_num=".$PHP_SCH_num."&PHP_SCH_fty=".$PHP_SCH_fty."&PHP_SCH_cust=".$PHP_SCH_cust."&PHP_SCH_supl=".$PHP_SCH_supl."&PHP_sr_startno=".$PHP_sr_startno;
	$op['back_str']=$back_str;

	if (!$PHP_dept_code) {   // 如果 不是 呈辦業務部門進入時...
		$op['manager_flag'] = 1;
	}
	$op['dept_id'] = $PHP_dept_code;
	$op['msg']= $apply->msg->get(2);
	page_display($op, 5, 4, $TPL_PO_LIST);
	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_submit":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "po_submit":
	check_authority(5,4,"edit");	
	
	$tmp_num = substr($PHP_aply_num,2);
	$po_num = "PO".$tmp_num;
	$f1=$apply->update_fields('po_num',$po_num, $PHP_aply_num);
//	$f1=$apply->update_fields('status','6', $PHP_aply_num);
	$PHP_po_num = $po_num;

	$f1=$apply->update_2fields('status', 'po_sub_date', '8', $TODAY, $PHP_id);
	$f1=$apply->update_fields('po_sub_user',$GLOBALS['SCACHE']['ADMIN']['login_id'], $PHP_aply_num);
		if ($f1)
	{
		$message="Successfully submit PO :".$PHP_po_num;
		$op['msg'][]=$message;
		$log->log_add(0,"54E",$message);
	}else{
		$op['msg']= $apply->msg->get(2);
		$message=$op['msg'][0];
	}	
		
	$redir_str = "po.php?PHP_action=po_view&PHP_aply_num=".$PHP_aply_num."&PHP_message=".$message;
	redirect_page($redir_str);
	break;
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_cfm_search":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "po_cfm_search":
	check_authority(5,5,"view");	

	if (!$op = $po->search_cfm()) {  
				$op['msg']= $po->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
	$op['msg']= $apply->msg->get(2);
	page_display($op, 5, 5, $TPL_PO_CFM_LIST);
	break;	



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_cfm_view":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "po_cfm_view":
		check_authority(5,5,"view");	

	$log_where = "AND item <> 'special'";	
	$op = $po->get($PHP_aply_num, $log_where);
	if (isset($op['ap_det']))
	{
				$op['ap_det2']=$po->grout_ap($op['ap_det']);
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


	if ($op['ap']['arv_area'])
	{
		$op['ap']['ship_name'] = $SHIP_TO[$op['ap']['arv_area']]['Name'];
		$op['ap']['ship_addr'] = $SHIP_TO[$op['ap']['arv_area']]['Addr'];
		$op['ap']['ship_tel']  = $SHIP_TO[$op['ap']['arv_area']]['TEL'];
		$op['ap']['ship_fax']  = $SHIP_TO[$op['ap']['arv_area']]['FAX'];
		$op['ap']['ship_attn'] = $SHIP_TO[$op['ap']['arv_area']]['Attn'];
	}
	
	$back_str="&PHP_sr_startno=".$PHP_sr_startno;
	$op['back_str']=$back_str;
	if (isset($PHP_message)) $op['msg'][]=$PHP_message;
	if ($op['ap']['po_rev'] > 0 || $op['ap']['po_apv_user']) $op['ap']['po_rev'] =$op['ap']['po_rev']+1;

		page_display($op, 5, 5, $TPL_PO_SHOW_CFM);			    	    
		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_po_cfm":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "do_po_cfm":
	check_authority(5,5,"add");	
	$f1=$apply->update_2fields('status', 'po_cfm_date', '10', $TODAY, $PHP_id);
	$f1=$apply->update_fields('po_cfm_user',$GLOBALS['SCACHE']['ADMIN']['login_id'], $PHP_aply_num);
		if ($f1)
	{
		$message="Successfully CFM PO :".$PHP_aply_num;
		$op['msg'][]=$message;
		$log->log_add(0,"54E",$message);
	}else{
		$op['msg']= $apply->msg->get(2);
		$message=$op['msg'][0];
	}	
	$print_str = $message."|<small>".$GLOBALS['SCACHE']['ADMIN']['login_id']." / ".$TODAY."</small>";
	echo $print_str;
//	$redir_str = "po.php?PHP_action=po_cfm_view&PHP_aply_num=".$PHP_aply_num."&PHP_message=".$message.$PHP_back_str;
//	redirect_page($redir_str);
	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "reject_po_cfm":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "reject_po_cfm":
	check_authority(5,5,"add");	
	
	$f1=$apply->update_2fields('status', 'au_date', '13', $TODAY, $PHP_id);
	$f1=$apply->update_fields('au_user',$GLOBALS['SCACHE']['ADMIN']['login_id'], $PHP_aply_num);
	
	$parm= array(	'ap_num'		=>	$PHP_aply_num,
								'item'			=>	'PO REJ-CFM',
								'des'				=>	$PHP_detail,
								'user'			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
								'k_date'		=>	date('Y-m-d')
				);
	$f1=$apply->add_log($parm);
	
	if ($f1)
	{
		$message="Successfully reject PO :".$PHP_aply_num;
		$op['msg'][]=$message;
//		$log->log_add(0,"54E",$message);
	}else{
		$op['msg']= $apply->msg->get(2);
		$message = $op['msg'][0];
	}	
	$redir_str = "po.php?PHP_action=po_cfm_view&PHP_aply_num=".$PHP_aply_num."&PHP_message=".$message.$PHP_back_str;
	redirect_page($redir_str);
	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_apv_search":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "po_apv_search":
	check_authority(5,6,"view");	

	if (!$op = $po->search_apv()) {  
				$op['msg']= $po->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
	$op['msg']= $po->msg->get(2);
	page_display($op, 5, 6, $TPL_PO_APV_LIST);
	break;	



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_apv_view":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "po_apv_view":
		check_authority(5,6,"edit");	

	$log_where = "AND item <> 'special'";	
	$op = $po->get($PHP_aply_num, $log_where);
	if (isset($op['ap_det']))
	{
				$op['ap_det2']=$po->grout_ap($op['ap_det']);
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

	if ($op['ap']['arv_area'])
	{
		$op['ap']['ship_name'] = $SHIP_TO[$op['ap']['arv_area']]['Name'];
		$op['ap']['ship_addr'] = $SHIP_TO[$op['ap']['arv_area']]['Addr'];
		$op['ap']['ship_tel']  = $SHIP_TO[$op['ap']['arv_area']]['TEL'];
		$op['ap']['ship_fax']  = $SHIP_TO[$op['ap']['arv_area']]['FAX'];
		$op['ap']['ship_attn'] = $SHIP_TO[$op['ap']['arv_area']]['Attn'];
	}
	
	$back_str="&PHP_sr_startno=".$PHP_sr_startno;
	$op['back_str']=$back_str;
	if (isset($PHP_message)) $op['msg'][]=$PHP_message;
	if ($op['ap']['po_rev'] > 0 || $op['ap']['po_apv_user']) $op['ap']['po_rev'] =$op['ap']['po_rev']+1;

		page_display($op, 5, 6, $TPL_PO_SHOW_APV);			    	    
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_po_apv":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "do_po_apv":
	check_authority(5,6,"add");	
	$f1=$apply->update_2fields('status', 'po_apv_date', '12', $TODAY, $PHP_id);
	$f1=$apply->update_fields('po_apv_user',$GLOBALS['SCACHE']['ADMIN']['login_id'], $PHP_aply_num);
		if ($f1)
	{
		$message="Successfully APV PO :".$PHP_aply_num;
		$op['msg'][]=$message;
		$log->log_add(0,"54E",$message);
	}else{
		$op['msg']= $apply->msg->get(2);
		$message=$op['msg'][0];
	}	
	
	$print_str = 	$message."|<small>".$GLOBALS['SCACHE']['ADMIN']['login_id']." / ".$TODAY."</small>";
	echo $print_str;
		
//	$redir_str = "po.php?PHP_action=po_apv_view&PHP_aply_num=".$PHP_aply_num."&PHP_message=".$message.$PHP_back_str;
//	redirect_page($redir_str);
	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "reject_po_apv":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "reject_po_apv":
	check_authority(5,6,"add");	
	
	$f1=$apply->update_2fields('status', 'au_date', '13', $TODAY, $PHP_id);
	$f1=$apply->update_fields('au_user',$GLOBALS['SCACHE']['ADMIN']['login_id'], $PHP_aply_num);
	
	$parm= array(	'ap_num'		=>	$PHP_aply_num,
								'item'			=>	'PO REJ-APV',
								'des'				=>	$PHP_detail,
								'user'			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
								'k_date'		=>	date('Y-m-d')
				);
	$f1=$apply->add_log($parm);
	
	if ($f1)
	{
		$message="Successfully reject PO :".$PHP_aply_num;
		$op['msg'][]=$message;
//		$log->log_add(0,"54E",$message);
	}else{
		$op['msg']= $apply->msg->get(2);
		$message = $op['msg'][0];
	}	
	$redir_str = "po.php?PHP_action=po_apv_view&PHP_aply_num=".$PHP_aply_num."&PHP_message=".$message.$PHP_back_str;
	redirect_page($redir_str);
	break;



/*
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_print":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "po_print":   //  .......
	check_authority(5,1,"add");	
	$where_str="AND (item like '%other%' OR item like '%special%')";		
	$op = $po->get($PHP_aply_num,$where_str);
	//取得user name
	$po_user=$user->get(0,$op['ap']['po_apv_user']);
	if ($po_user['name'])$op['ap']['po_apv_user'] = $po_user['name'];
	$po_user=$user->get(0,$op['ap']['po_cfm_user']);
	if ($po_user['name'])$op['ap']['po_cfm_user'] = $po_user['name'];
	$po_user=$user->get(0,$op['ap']['po_sub_user']);
	if ($po_user['name'])$op['ap']['po_sub_user'] = $po_user['name'];
	$po_user=$user->get(0,$op['ap']['submit_user']);
	if ($po_user['name'])$op['ap']['submit_user'] = $po_user['name'];
	
	$where_str = " WHERE dept_code = '".$op['ap']['dept']."'";
	$dept_name=$dept->get_fields('dept_name',$where_str);
	if (isset($op['ap_det']))
	{
		$op['ap_det2'][0] = $op['ap_det'][0];
		$op['ap_det2'][0]['orders'] = $op['ap_det'][0]['ord_num'];
		if ($op['ap_det2'][0]['mat_cat'] == 'l')$mat_cat="Fabric";
		if ($op['ap_det2'][0]['mat_cat'] == 'a')$mat_cat="Accessory";		
		$k=1;
		for ($i=1; $i<sizeof($op['ap_det']); $i++)
		{
			$mk=0;
			for ($j=0; $j< sizeof($op['ap_det2']); $j++)
			{
				if ($op['ap_det2'][$j]['mat_code'] == $op['ap_det'][$i]['mat_code'] && $op['ap_det2'][$j]['color'] == $op['ap_det'][$i]['color'] && $op['ap_det2'][$j]['unit'] == $op['ap_det'][$i]['unit'] && $op['ap_det2'][$j]['eta'] == $op['ap_det'][$i]['eta'])
				{
					$op['ap_det2'][$j]['ap_qty'] = $op['ap_det'][$i]['ap_qty'] +$op['ap_det2'][$j]['ap_qty'];
					$op['ap_det2'][$j]['po_qty'] = $op['ap_det'][$i]['po_qty'] +$op['ap_det2'][$j]['po_qty'];
					if (!strstr($op['ap_det2'][$j]['orders'],$op['ap_det'][$i]['ord_num']))
							$op['ap_det2'][$j]['orders'] = $op['ap_det2'][$j]['orders']."|".$op['ap_det'][$i]['ord_num'];
					$mk = 1;
				}
			}
			
			if ($mk == 0)
			{
				$op['ap_det2'][$k] = $op['ap_det'][$i];
				$op['ap_det2'][$k]['orders'] = $op['ap_det'][$i]['ord_num'];
				$k++;
			}
		}
	}else{
		if ($op['ap_spec'][0]['mat_cat'] == 'l')$mat_cat="Fabric";
		if ($op['ap_spec'][0]['mat_cat'] == 'a')$mat_cat="Accessory";		

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
//---------------------------------- 資料庫作業結束 ------------------------------

include_once($config['root_dir']."/lib/class.pdf_po.php");

if ($op['ap']['special'] == 0) 
{
	$print_title="Regular Purchase Order";
}else{
	$print_title="Special Purchase Order";
}

$print_title2 = "VER.".($op['ap']['po_rev']+1)."   for   ".$mat_cat;
$creator = $op['ap']['ap_user'];
$mark = $op['ap']['po_num'];
$pdf=new PDF_po('L','mm','A4');
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
$parm = array (	'po_num'		=>	$op['ap']['po_num'],
								'supplier'	=>	$op['ap']['s_name'],
								'dept'			=>	$dept_name[0],
								'ap_date'		=>	$op['ap']['po_apv_date'],
								'ap_user'		=>	$op['ap']['ap_user'],
								'currency'	=>	$op['ap']['currency'],
								);
$pdf->hend_title($parm);
$pdf->ln();$pdf->ln();


	if ($op['ap']['arv_area'])
	{
		$op['ap']['ship_name'] = $SHIP_TO[$op['ap']['arv_area']]['Name'];
		$op['ap']['ship_addr'] = $SHIP_TO[$op['ap']['arv_area']]['Addr'];
		$op['ap']['ship_tel']  = $SHIP_TO[$op['ap']['arv_area']]['TEL'];
		$op['ap']['ship_fax']  = $SHIP_TO[$op['ap']['arv_area']]['FAX'];
		$op['ap']['ship_attn'] = $SHIP_TO[$op['ap']['arv_area']]['Attn'];
	}

$pdf->SetFont('Arial','B',10);
$pdf->Cell(20,5,'SHIP TO : ',0,0,'L');
$pdf->cell(100,5,$op['ap']['ship_name'],0,0,'L');
$pdf->SetFont('Arial','',9);
$pdf->Cell(25,5,'RCVER ATTN : ',0,0,'L');
$pdf->cell(20,5,$op['ap']['ship_attn'],0,0,'L');
$pdf->Cell(23,5,'RCVER TEL : ',0,0,'L');
$pdf->cell(35,5,$op['ap']['ship_tel'],0,0,'L');
$pdf->Cell(23,5,'RCVER FAX : ',0,0,'L');
$pdf->cell(25,5,$op['ap']['ship_fax'],0,0,'L');
$pdf->ln();
$pdf->Cell(28,5,'RCVER ADDR : ',0,0,'L');
$pdf->cell(250,5,$op['ap']['ship_addr'],0,0,'L');
$pdf->ln();
$pdf->ln();
$pdf->po_title();
$pdf->ln();



$x=0;$total=0;
if (isset($op['ap_det2']))
{
	for ($i=0; $i< sizeof($op['ap_det2']); $i++)
	{		
		$op['ap_det2'][$i]['prics'] = NUMBER_FORMAT($op['ap_det2'][$i]['prics'],2);
		$op['ap_det2'][$i]['po_qty'] = NUMBER_FORMAT($op['ap_det2'][$i]['po_qty'],2);
		$op['ap_det2'][$i]['amount'] = NUMBER_FORMAT($op['ap_det2'][$i]['amount'],2);
		$ords= explode('|',$op['ap_det2'][$i]['orders']);		
		$total = $total + ($op['ap_det2'][$i]['prics']*$op['ap_det2'][$i]['po_qty']);
		$ord_cut = sizeof($ords);
		$x = $x+$ord_cut;
		if ($x > 17)
		{
			$pdf->Open();
			$pdf->AddPage();
			$pdf->hend_title($parm);
			$pdf->ln();$pdf->ln();$pdf->ln();
			$pdf->po_title();
			$pdf->ln();			
			$x=0;
		}
		
		if ($ord_cut > 1)
		{
			$pdf->po_det_mix($op['ap_det2'][$i],$ords);
		}else{
			$pdf->po_det($op['ap_det2'][$i]);
		}
		$pdf->ln();
		
	}
}

if (isset($op['ap_spec']))
{
	for ($i=0; $i< sizeof($op['ap_spec']); $i++)
	{
		$x++;
		$total = $total + ($op['ap_spec'][$i]['prics']*$op['ap_spec'][$i]['po_qty']);
		$op['ap_spec'][$i]['prics'] = NUMBER_FORMAT($op['ap_spec'][$i]['prics'],2);
		$op['ap_spec'][$i]['po_qty'] = NUMBER_FORMAT($op['ap_spec'][$i]['po_qty'],2);
		$op['ap_spec'][$i]['amount'] = NUMBER_FORMAT($op['ap_spec'][$i]['amount'],2);

		if ($x > 17)
		{
			$pdf->Open();
			$pdf->AddPage();
			$pdf->hend_title($parm);
			$pdf->ln();$pdf->ln();$pdf->ln();
			$pdf->po_title();
			$pdf->ln();			
			$x=0;
		}
		
		$pdf->po_det($op['ap_spec'][$i]);
		$pdf->ln();		
	}
}
$x=$x+2;
		if ($x > 17)
		{
			$pdf->Open();
			$pdf->AddPage();
			$pdf->hend_title($parm);
			$pdf->ln();$pdf->ln();$pdf->ln();				
			$x=0;
		}
$total = NUMBER_FORMAT($total,2);
$pdf->Cell(18,5,'Re-Mark','TRL',0,'C');
$pdf->Cell(242,5,'Total Cost : '.$op['ap']['currency'].' $'.$total,'T',0,'R');
$pdf->Cell(20,5,'','TR',0,'C');
$pdf->ln();
$pdf->Cell(18,5,'','RL',0,'C');
$pdf->Cell(242,5,'Delivery : '.$op['ap']['usance'].' days   Payment : '.$op['ap']['dm_way'],0,0,'L');
$pdf->Cell(20,5,'','R',0,'C');
$pdf->ln();
for ($i=0; $i< sizeof($op['apply_log']); $i++)
{
	$x++;
		if ($x > 16)
		{
			$pdf->Cell(18,5,'','RLB',0,'C');
			$pdf->Cell(242,5,'','B',0,'L');
			$pdf->Cell(20,5,'','BR',0,'C');
			$pdf->Open();
			$pdf->AddPage();
			$pdf->hend_title($parm);
			$pdf->ln();$pdf->ln();$pdf->ln();	
			$pdf->Cell(18,5,'Logs','TRL',0,'C');
			$pdf->Cell(242,5,'','T',0,'C');
			$pdf->Cell(20,5,'','TR',0,'C');
			$pdf->ln();	
			$x=0;
		}
	$pdf->Cell(18,5,'','RL',0,'C');
	$pdf->Cell(242,5,$op['apply_log'][$i]['des'],0,0,'L');
	$pdf->Cell(20,5,'','R',0,'C');
	$pdf->ln();
}
	$pdf->Cell(18,5,'','RLB',0,'C');
	$pdf->Cell(242,5,'','B',0,'L');
	$pdf->Cell(20,5,'','BR',0,'C');

	$pdf->ln();
	$pdf->SetFont('Big5','',10);
	$pdf->Cell(40,5,' ','0',0,'C');
	$pdf->Cell(40,5,'',0,0,'L');
	
//	$pdf->SetFont('Big5','B',10);
	$pdf->Cell(50,5,'APPROVAL : '.$op['ap']['po_apv_user'],'0',0,'C');	//PO Approval
	$pdf->Cell(50,5,'CONFIRM :'.$op['ap']['po_cfm_user'],0,0,'L');//PO Confirm
	$pdf->Cell(50,5,'PO :'.$op['ap']['po_sub_user'],0,0,'L');//PO Submit
	
	$pdf->Cell(50,5,'PA :'.$op['ap']['submit_user'].' ('.$op['ap']['submit_date'].')',0,0,'L');//PA submit
	

$name=$op['ap']['po_num'].'_po.pdf';
$pdf->Output($name,'D');
break;	
*/

//=======================================================
    case "po_bom_det":

		check_authority(5,4,"view");
		//-------------------- 將 製造令 show out ------------------
		//  wi 主檔
	if (isset($PHP_id))
	{
		if(!$op['wi'] = $wi->get($PHP_id)){    //取出該筆 製造令記錄
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}
	}else{	
		if(!$op['wi']  = $wi->get(0,$PHP_bom_num)){    //取出該筆 製造令記錄
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}
	}
		//  smpl 樣本檔
		if(!$op['order'] = $order->get($op['wi']['smpl_id'])){
					$op['msg']= $order->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}
		//-----------------------------------------------------------------

		// 相片的URL決定 ------------------------------------------------------
				$style_dir	= "./picture/";  
				$no_img		= "./images/graydot.gif";
			if(file_exists($style_dir.$op['wi']['style_code'].".jpg")){
				$op['wi']['pic_url'] = $style_dir.$op['wi']['style_code'].".jpg";
			} else {
				$op['wi']['pic_url'] = $no_img;
			}
		//  取出  BOM  主料記錄 --------------------------------------------------------------
		 $op['bom_lots_NONE']= '';
	 	 $op['bom_lots'] = $po->get_lots_det($op['wi']['id']);  //取出該筆 bom 內ALL主料記錄
		 $num_bom_lots = count($op['bom_lots']);
		 if (!$num_bom_lots){	$op['bom_lots_NONE'] = "1";		}

		 if (isset($PHP_po_num))
		 { 
		 	for ($i =0; $i< sizeof($op['bom_lots']); $i++)
		 	{
		 			if ($op['bom_lots'][$i]['po_num'] == $PHP_po_num) $op['bom_lots'][$i]['ln_mk'] = 1;
		 			if ($op['bom_lots'][$i]['spec_ap'])
		 			{
		 				for ($k =0; $k< sizeof($op['bom_lots'][$i]['spec_ap']); $k++)
		 				{
		 					if ($op['bom_lots'][$i]['spec_ap'][$k]['po_num'] == $PHP_po_num) $op['bom_lots'][$i]['ln_mk'] = 1;
		 				}
		 			}
		 	}
		 }

		//  取出  BOM  副料記錄 --------------------------------------------------------------
		$op['bom_acc_NONE']= '';
		$op['bom_acc'] = $po->get_acc_det($op['wi']['id']);  //取出該筆 bom 內ALL副料記錄
		$num_bom_acc = count($op['bom_acc']);
		if (!$num_bom_acc){	$op['bom_acc_NONE'] = "1";		}

		 if (isset($PHP_po_num))
		 {
		 	for ($i =0; $i< sizeof($op['bom_acc']); $i++)
		 	{
		 			if ($op['bom_acc'][$i]['po_num'] == $PHP_po_num) $op['bom_acc'][$i]['ln_mk'] = 1;
		 			if ($op['bom_acc'][$i]['spec_ap'])
		 			{
		 				for ($k =0; $k< sizeof($op['bom_acc'][$i]['spec_ap']); $k++)
		 				{
		 					if ($op['bom_acc'][$i]['spec_ap'][$k]['po_num'] == $PHP_po_num) $op['bom_acc'][$i]['ln_mk'] = 1;
		 				}
		 			}
		 	}
		 }

		//  取出  BOM  加購記錄 --------------------------------------------------------------
		$op['ext_mat_NONE']= '';
		$op['ext_mat'] = $po->get_ext_ap($op['wi']['wi_num']);  //取出該筆 bom 內ALL加購記錄
		$num_ext_mat = count($op['ext_mat']);
		if (!$num_ext_mat){	$op['ext_mat_NONE'] = "1";		}

		 if (isset($PHP_po_num))
		 {
		 	for ($i =0; $i< sizeof($op['ext_mat']); $i++)
		 	{
		 			if ($op['ext_mat'][$i]['po_num'] == $PHP_po_num) $op['ext_mat'][$i]['ln_mk'] = 1;
		 	}
		 }		
		
		page_display($op, 5,4, $TPL_BOM_PO_VIEW);
		break;


//-------------------------------------------------------------------------


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_print_doc":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "po_print_doc":   //  .......
	check_authority(5,4,"add");	
	$where_str="AND (item like '%Packing%' OR item like '%Remark%')";		
	$op = $po->get($PHP_aply_num,$where_str);
	//取得user name
	
	if (isset($op['ap_det']))
	{
		
		$op['ap_det2'][0] = $op['ap_det'][0];
		$op['ap_det2'][0]['orders'] = $op['ap_det'][0]['ord_num'];
		if ($op['ap_det2'][0]['mat_cat'] == 'l')$mat_cat="Fabric";
		if ($op['ap_det2'][0]['mat_cat'] == 'a')$mat_cat="Accessory";		
		$k=1;
		for ($i=1; $i<sizeof($op['ap_det']); $i++)
		{
			$mk=0;
			for ($j=0; $j< sizeof($op['ap_det2']); $j++)
			{
				if ($op['ap_det2'][$j]['mat_code'] == $op['ap_det'][$i]['mat_code'] && $op['ap_det2'][$j]['color'] == $op['ap_det'][$i]['color'] && $op['ap_det2'][$j]['unit'] == $op['ap_det'][$i]['unit'] && $op['ap_det2'][$j]['eta'] == $op['ap_det'][$i]['eta'])
				{
					$op['ap_det2'][$j]['ap_qty'] = $op['ap_det'][$i]['ap_qty'] +$op['ap_det2'][$j]['ap_qty'];
					$op['ap_det2'][$j]['po_qty'] = $op['ap_det'][$i]['po_qty'] +$op['ap_det2'][$j]['po_qty'];
					if (!strstr($op['ap_det2'][$j]['orders'],$op['ap_det'][$i]['ord_num']))
							$op['ap_det2'][$j]['orders'] = $op['ap_det2'][$j]['orders']."|".$op['ap_det'][$i]['ord_num'];
					$mk = 1;
				}
			}
			
			if ($mk == 0)
			{
				$op['ap_det2'][$k] = $op['ap_det'][$i];
				$op['ap_det2'][$k]['orders'] = $op['ap_det'][$i]['ord_num'];
				$k++;
			}
		}
	}else{
		if ($op['ap_spec'][0]['mat_cat'] == 'l')$mat_cat="Fabric";
		if ($op['ap_spec'][0]['mat_cat'] == 'a')$mat_cat="Accessory";		

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

	if ($op['ap']['po_rev'] > 0 || $op['ap']['po_apv_user']) $op['ap']['po_rev'] =$op['ap']['po_rev']+1;

	if( $op['ap']['po_rev'] < 10)	 $op['ap']['po_rev'] = "00".$op['ap']['po_rev'];
	if( $op['ap']['po_rev'] > 10 && $op['ap']['po_rev'] < 100)	 $op['ap']['po_rev'] = "0".$op['ap']['po_rev'];
	
//---------------------------------- 資料庫作業結束 ------------------------------

include_once($config['root_dir']."/lib/class.pdf_po_doc.php");

$print_title2="PURCHASE ORDER";
$creator = $op['ap']['ap_user'];
$mark = $op['ap']['po_num'];

$pdf=new PDF_po_doc('P','mm','A4');
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();
$pdf->SetFont('Arial','B',10);
//$Y = $pdf->GetY();
//$pdf->SetXY(10,$Y+10);
$pdf->cell(20, 5,"P.O. NO.",0,0,'L');
$pdf->cell(145, 5,$op['ap']['po_num'],0,0,'L');
$pdf->SetFont('Arial','',10);
$pdf->cell(40, 5,"Ver      ".$op['ap']['po_rev'],0,0,'L');
$pdf->ln();

$pdf->SetFont('Arial','',10);
$pdf->cell(165, 5,'',0,0,'L');
$pdf->cell(40, 5,"Date     ".date('d-M-y'),0,0,'L');
$pdf->ln();
$pdf->supl_show($op['ap']);
$pdf->ln();
$pdf->SetFont('Arial','',10);
$pdf->cell(205, 5,'We Would like to reconfirm with you the following qty for bulk use..',0,0,'L');
$pdf->ln();
/*
$pdf->SetFont('Arial','B',10);
$pdf->cell(20, 5,'SHELL',0,0,'L');
$pdf->cell(100, 5,'',0,0,'L');
$pdf->cell(80, 5,'FOB Shan Tao by BOA',0,0,'L');
$pdf->ln();
*/
$x=1;
$pdf->SetFont('Arial','',9);
$pdf->mater_title();
$pdf->ln();

$pri_tal = 0;
$qty_tal = 0;
$eta = '0000-00-00';
//$toler = 0;

	if ($op['ap']['arv_area'])
	{
		$op['ap']['ship_name'] = $SHIP_TO[$op['ap']['arv_area']]['Name'];
		$op['ap']['ship_addr'] = $SHIP_TO[$op['ap']['arv_area']]['Addr'];
		$op['ap']['ship_tel']  = $SHIP_TO[$op['ap']['arv_area']]['TEL'];
		$op['ap']['ship_fax']  = $SHIP_TO[$op['ap']['arv_area']]['FAX'];
		$op['ap']['ship_attn'] = $SHIP_TO[$op['ap']['arv_area']]['Attn'];
	}
$mk_order = '';
if (isset($op['ap_det2']))
{
	for ($i=0; $i< sizeof($op['ap_det2']); $i++)
	{
		if (!strstr($mk_order,$op['ap_det'][$i]['ord_num']))$mk_order = $mk_order.$op['ap_det'][$i]['ord_num'].",";			
		$pri_tal = $pri_tal + ($op['ap_det2'][$i]['prics']*$op['ap_det2'][$i]['po_qty']);
		$qty_tal = $qty_tal + $op['ap_det2'][$i]['po_qty'];
		if ($eta < $op['ap_det2'][$i]['eta']) $eta = $op['ap_det2'][$i]['eta'];
	//	if ($toler < $op['ap_det2'][$i]['range']) $toler = $op['ap_det2'][$i]['range'];
		if ($x > 30)
		{
			$pdf->AddPage();
			$pdf->mater_title();
			$pdf->ln();
			$x=0;
		}	
		$bk_ll = $pdf->mater($op['ap_det2'][$i],$op['ap']['currency']);
		$x=$x+$bk_ll;
	}
}
if (isset($op['ap_spec']))
{
	for ($i=0; $i< sizeof($op['ap_spec']); $i++)
	{		
		$mk_order = $op['ap_spec'][0]['ord_num'].",";			
		$pri_tal = $pri_tal + ($op['ap_spec'][$i]['prics']*$op['ap_spec'][$i]['po_qty']);
		$qty_tal = $qty_tal + $op['ap_spec'][$i]['po_qty'];
		if ($eta < $op['ap_spec'][$i]['eta']) $eta = $op['ap_spec'][$i]['eta'];
	//	if ($toler < $op['ap_spec'][$i]['range']) $toler = $op['ap_spec'][$i]['range'];
		if ($x > 30)
		{
			$pdf->AddPage();
			$pdf->mater_title();
			$pdf->ln();
			$x=0;		
		}			
		$bk_ll = $pdf->mater($op['ap_spec'][$i],$op['ap']['currency']);	
		$x=$x+$bk_ll;
	}
}
$mk_order = substr($mk_order,0,-1);
//echo $mk_order;
//Total
$x++;
if ($x > 30)
{
	$pdf->AddPage();
	$x=0;		
}
$pri_tal = NUMBER_FORMAT($pri_tal,2);
$qty_tal = NUMBER_FORMAT($qty_tal,2);
$pdf->cell(130,5,'TOTAL',1,0,'R');
$pdf->cell(20,5,$qty_tal,1,0,'R');
$pdf->cell(45,5,$op['ap']['currency']."$ ".$pri_tal,1,0,'R');
$pdf->ln();

//Delivery
$x++;
if ($x > 30)
{
	$pdf->AddPage();
	$x=0;		
}
$pdf->SetFont('Arial','',10);
$pdf->cell(20, 5,"Delivery    : ",0,0,'L');
$pdf->cell(145, 5,$eta,0,0,'L');
$pdf->ln();

//Less(量的增減範圍)
$x++;
if ($x > 30)
{
	$pdf->AddPage();
	$x=0;		
}
$pdf->SetFont('Arial','',10);
$pdf->cell(20, 5,"Tolerance : ",0,0,'L');
$pdf->cell(145, 5,"+-".$op['ap']['toler']."% in quantity allowable",0,0,'L');
$pdf->ln();

//Payment(付款方式)
$x++;
if ($x > 30)
{
	$pdf->AddPage();
	$x=0;		
}
$pdf->SetFont('Arial','',10);
$pdf->cell(20, 5,"Payment   : ",0,0,'L');
$pdf->cell(145, 5,$op['ap']['dm_way'],0,0,'L');
$pdf->ln();

//Bill To(帳單送達地)
$x=$x+3;
if ($x > 30)
{
	$pdf->AddPage();
	$x=0;		
}
$pdf->SetFont('Arial','',10);
$pdf->cell(20, 5,"BILL TO    : ",0,0,'L');
$pdf->SetFont('Arial','B',10);
$pdf->cell(145, 5,'CARNIVAL INDUSTRIAL CORPORATION',0,0,'L');
$pdf->ln();
$pdf->SetFont('Arial','',8);
$pdf->cell(20, 5," ",0,0,'L');
$pdf->cell(145, 5,'7TH FL., NO.25, JEN-AI RD., SEC.4, TAIPEI, TAIWAN.',0,0,'L');
$pdf->ln();
$pdf->SetFont('Arial','',8);
$pdf->cell(20, 5," ",0,0,'L');
$pdf->cell(145, 5,'TEL:8862-27113171',0,0,'L');
$pdf->ln();

//SHIP To(貨物送達地)
$x=$x+3;
if ($x > 30)
{
	$pdf->AddPage();
	$x=0;		
}
$pdf->SetFont('Arial','',10);
$pdf->cell(20, 5,"SHIP TO   : ",0,0,'L');
$pdf->SetFont('Arial','B',10);
$pdf->cell(145, 5,$op['ap']['ship_name'],0,0,'L');
$pdf->ln();
$pdf->SetFont('Arial','',8);
$pdf->cell(20, 5," ",0,0,'L');
$pdf->cell(165, 5,$op['ap']['ship_addr'],0,0,'L');
$pdf->ln();
$pdf->SetFont('Arial','',8);
$pdf->cell(20, 5," ",0,0,'L');
$pdf->cell(145, 5,"TEL:".$op['ap']['ship_tel'],0,0,'L');
$pdf->ln();
$pdf->SetFont('Arial','',8);
$pdf->cell(20, 5," ",0,0,'L');
$pdf->cell(145, 5,"FAX:".$op['ap']['ship_fax'],0,0,'L');
$pdf->ln();
$pdf->SetFont('Arial','',8);
$pdf->cell(20, 5," ",0,0,'L');
$pdf->cell(145, 5,"Attn:".$op['ap']['ship_attn'],0,0,'L');
$pdf->ln();

//Packing
$ln=$pdf->mk_show($op['apply_log'],'Packing');
if ($ln == 0)$pdf->ln();
$x=$x+$ln;
if ($x > 30)
{
	$pdf->AddPage();
	$x=0;		
}

//Remark
$ln=$pdf->mk_show($op['apply_log'],'Remark',$mk_order);
$x=$x+$ln;
if ($x > 30)
{
	$pdf->AddPage();
	$x=0;		
}

//主管簽名
$pdf->SetY(240);
$pdf->SetFont('Arial','',10);
$pdf->cell(120, 5,'',0,0,'L');
$pdf->cell(70, 5,'Confirmed by',0,0,'L');
$pdf->ln();
$pdf->SetFont('Arial','B',10);
$pdf->cell(90, 5,$op['ap']['supl_f_name'],0,0,'L');
$pdf->cell(30, 5,'',0,0,'L');
$pdf->cell(70, 5,'Carnival Industrial Corporation',0,0,'L');
$pdf->ln();
$pdf->ln();
$pdf->ln();
$pdf->cell(90, 5,'','B',0,'L');
$pdf->cell(30, 5,'',0,0,'L');
$pdf->cell(70, 5,'','B',0,'L');
$name=$op['ap']['po_num'].'_po_doc.pdf';
$pdf->Output($name,'D');
break;	









}   // end case ---------

?>
