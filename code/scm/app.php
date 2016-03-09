<?
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

$tmp = $para->get(0,'po_accept');
$po_accept_qty = increceDaysInDate($TODAY,$tmp['set_value']);
// echo $PHP_action;
switch ($PHP_action) {
#++++++++++++++    Acounts Payable +++++++++++++  2011/05/17  +++++++++++++++++
#		 job 54    請購記錄 
#++++++++++++++++++++++++++++++++++++++++++++  2011/05/17  +++++++++++++++++

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "ap":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "ap":
check_authority(5,4,"view");

// echo $days=date("t",strtotime(date("Y-m-d")));
$op['year'] = $arry2->select($YEAR_WORK,date("Y"),'PHP_year','select','');
$op['month'] = $arry2->select($MONTH_WORK,date("m"),'PHP_month','select','');

page_display($op, 5, 4, $TPL_AP);			    	    
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "ap_list":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "ap_list":
check_authority(5,4,"view");

// echo $PHP_year.'-'.$PHP_month;
if ( !empty($PHP_status) ){
	$_SESSION['parm'] = array(
	'PHP_rcv_date'	=>	$PHP_year.'-'.$PHP_month,
	'PHP_dept'			=>	$PHP_rcvd,
	'PHP_sup_code'	=>	$PHP_ship,
	'PHP_ap_user'		=>	$PHP_ord,
	'PHP_action'		=>	$PHP_action
	);
}
$_SESSION['parm']['PHP_sr_startno'] = ( !empty($PHP_sr_startno) ) ? $PHP_sr_startno : 1 ;
$op = $ap->search();
// print_r($op);
// $op

page_display($op, 5, 4, $TPL_AP_LIST);			    	    
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "ap_add":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "ap_add":
check_authority(5,4,"view");


$op = $receive->get($PHP_rcv_num);

// echo $days=date("t",strtotime(date("Y-m-d")));
$op['year'] = $arry2->select($YEAR_WORK,date("Y"),'PHP_year','select','');
$op['month'] = $arry2->select($MONTH_WORK,date("m"),'PHP_month','select','');

page_display($op, 5, 4, $TPL_AP_ADD);			    	    
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apply_search_bom":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++










case "apply":
		check_authority(5,4,"view");
		$where_str = $manager = $manager_v = $dept_id = '';
		$dept_ary = array();
		$sales_dept_ary = get_sales_dept(); // 取出 業務的部門 [不含K0] ------
		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];   // 判定進入身份的指標
		
		$op['factory'] = $arry2->select($FACTORY,'','PHP_factory','select','');
		
		for ($i=0; $i<count($sales_dept_ary);$i++){
			if($user_dept == $sales_dept_ary[$i]){

				// 如果是業務部進入 則dept_code 指定該業務部---
				$dept_id = $sales_dept_ary[$i];  
			}
		}

		if (!$dept_id) {    // 當不是業務部人[也不含 K0 的人 ]進入時
			$manager = 1;
			$manager_v = 1;
			//業務部門 select選單
			$dept_ary = $arry2->select($sales_dept_ary,"","PHP_dept_code","select","");  
		} else {
			$where_str = " WHERE dept = '".$dept_id."' ";			
		}

		$where_str=$where_str."order by cust_s_name"; //依cust_s_name排序
		if(!$cust_def = $cust->get_fields('cust_init_name',$where_str)){;  //取出客戶簡稱
			$op['msg'][] = "sorry! there is no any customer record in your team, please add customer first!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//取出客戶代號
		for ($i=0; $i< sizeof($cust_def); $i++)
		{
			$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//以 [客戶代號-客戶簡稱] 呈現
		}
		$op['cust_select'] =  $arry2->select($cust_value,'','PHP_cust','select','',$cust_def_vue); 

		$op['manager_flag'] = $manager;
		$op['manager_v_flag'] = $manager_v;
		$op['dept_id'] = $dept_id;
		$op['dept_ary'] = $dept_ary;

		$op['msg'] = $order->msg->get(2);
		  	
		page_display($op, 5, 4, $TPL_APLY);			    	    
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apply_search_bom":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "apply_search_bom":   //  先找出製造令.......

		check_authority(5,4,"view");
		
			if (!$op = $bom->search_cfm()) {
				$op['msg']= $wi->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			// op 被清除了 需再判斷一次 
			for ($i=0; $i< sizeof($op['wi']); $i++)
			{
				$op['wi'][$i]['acc_ap'] = $bom->get_aply($op['wi'][$i]['id'], 'bom_acc');
				$op['wi'][$i]['lots_ap'] = $bom->get_aply($op['wi'][$i]['id'], 'bom_lots');
			}
			
			

		$op['msg']= $bom->msg->get(2);				
		$back_str="&PHP_dept_code=".$PHP_dept_code."&PHP_cust=".$PHP_cust."&PHP_num=".$PHP_num;
		$op['back_str']=$back_str;

		page_display($op, 3,5, $TPL_APLY_BOM_LIST);
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apply_add_search":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "apply_add_search":   //  先找出製造令.......

		check_authority(5,4,"view");
			
		$op['dept_id'] = $PHP_dept;
		$op['cust'] = $PHP_cust;
		page_display($op, 3,5, $TPL_APLY_ADD_SCH);
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_apply_add_search":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_apply_add_search":   //  先找出製造令.......

		check_authority(5,4,"view");

			if (!$op = $bom->search_cfm()) {
				$op['msg']= $wi->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			// op 被清除了 需再判斷一次 
			for ($i=0; $i< sizeof($op['wi']); $i++)
			{
				$op['wi'][$i]['acc_ap'] = $bom->get_aply($op['wi'][$i]['id'], 'bom_acc');
				$op['wi'][$i]['lots_ap'] = $bom->get_aply($op['wi'][$i]['id'], 'bom_lots');
			}
			
			

		$op['msg']= $bom->msg->get(2);				
		$back_str="&PHP_dept_code=".$PHP_dept_code."&PHP_cust=".$PHP_cust."&PHP_num=".$PHP_num;
		$op['back_str']=$back_str;

		page_display($op, 3,5, $TPL_APLY_ADD_SCH);
		break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apply_wi_view":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "apply_wi_view":

		check_authority(3,1,"view");

		// 將 製造令 完整show out ------
		//  wi 主檔

			if(!$op = $wi->get_all($PHP_id)){    //取出該筆 製造令記錄 ID
						$op['msg']= $wi->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
			}

//--------------------------------------------------------------------------
		//  wi_qty 數量檔
		$where_str = " WHERE wi_id='".$op['wi']['id']."' ";
		$T_wiqty = $wiqty->search(1,$where_str);

		$num_colors = count($T_wiqty);

		// 取出 size_scale ----------------------------------------------
		$size_A = $size_des->get($op['smpl']['size']);
		$op['smpl']['size_scale']=$size_A['size_scale'];
			//????????????????????????????????????? 注意 有時沒有 size type 資料時??????
		$sizing = explode(",", $size_A['size']);

			// 做出 size table-------------------------------------------
		$reply = show_breakdown($T_wiqty,$sizing,$size_A['base_size']);
		$op['show_breakdown'] = $reply['html'];
		$op['total'] = $reply['total'];
		//-----------------------------------------------------------------

		
			$op['msg'] = $wi->msg->get(2);
		if (isset($PHP_item))
		{
			$back_str="?PHP_action=do_apply_add_search&PHP_sr_startno".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_cust=".$PHP_cust."&PHP_num=".$PHP_num;
			$op['back_str']=$back_str;
			page_display($op, 3, 5, $TPL_APLY_VIEW_WI_SUB);		
		}else{
			$back_str="?PHP_action=apply_search_bom&PHP_sr_startno".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_cust=".$PHP_cust."&PHP_num=".$PHP_num;
			$op['back_str']=$back_str;
			page_display($op, 3, 5, $TPL_APLY_VIEW_WI);		
		}
		break;
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apply_add":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

    case "apply_add":
		check_authority(5,4,"add");		
		
			if(!$ord = $wi->get($PHP_wi_id)){    //取出該筆 製造令記錄 ID
						$op['msg']= $wi->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
			}
			
			$parm_madd= array(  'cust'  		=>	$ord['cust'],
													'dept'			=>	$ord['dept'],
													'sup_code'	=>	$PHP_vendor,
													'ap_user'		=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
													'ap_date'		=>	$TODAY,
													);

		$head="A".date('y')."-";	//A+日期+部門=請購單開頭
		$parm_madd['ap_num']=$apply->get_no($head,'ap_num','ap');	//取得請購的最後編號
		$new_id = $apply->add($parm_madd);	//新增AP資料庫
		
		
		if ($PHP_item == 'lots')
		{
			$parm_mat = array (	'field_name'	=>	'vendor1',
													'field_value'	=>	$PHP_vendor,
													'code'				=>	$PHP_mat_code
												);
			$lots->update_field_code($parm_mat);
			
			$where_str = "WHERE wi_id =". $PHP_wi_id ." AND lots_used_id =".$PHP_mat_id;
			$mat=$bom->search('bom_lots', $where_str);

			$fd='bom_lots';	
			$mat_cat='l';		
		}else{
			$parm_mat = array (	'field_name'	=>	'vendor1',
													'field_value'	=>	$PHP_vendor,
													'code'				=>	$PHP_mat_code
												);
			$acc->update_field_code($parm_mat);
			
			$where_str = "WHERE wi_id =". $PHP_wi_id ." AND acc_used_id =".$PHP_mat_id;
			$mat=$bom->search('bom_acc', $where_str);		
			
			$fd='bom_acc';
			$mat_cat='a';				
		}
		for ($i=0; $i<sizeof($mat); $i++)
		{
			$tmp_qty=explode(',',$mat[$i]['qty']);
			$qty=0;
			for ($j=0; $j<sizeof($tmp_qty); $j++)
			{
				$qty=$qty+$tmp_qty[$j];
			}
	//		echo $mat[$i]['id'];
			$parm_dadd = array( 'ap_num'	=>	$parm_madd['ap_num'],
													'mat_id'	=>	$mat[$i]['id'],
													'eta'			=>	$PHP_eta,
													'qty'			=>	$qty,
													'unit'		=>	$PHP_unit,
													'mat_cat'	=>	$mat_cat,
													);

			$parm_mat = array($mat[$i]['id'],'ap_mark',$parm_madd['ap_num'],$fd);
			$f1 = $apply->add_det($parm_dadd);	//新增AP資料庫
			$f2 = $bom->update_field($parm_mat);			
		}


	
		$op = $apply->get($parm_madd['ap_num']);
		
		$message = "success to append AP : ".$parm_madd['ap_num'];
		$op['msg'][] = $message;
		$log->log_add(0,"54A",$message);

		
//		$op['COUNTRY_select']=$arry2->select($COUNTRY,'','PHP_buy_area','select','');
		$op['CURRENCY_select'] = $arry2->select($CURRENCY,'','PHP_CURRENCY','select','');
		$op['FACTORY_select'] = $arry2->select($FACTORY,'','PHP_FACTORY','select','');
		page_display($op, 5, 4, $TPL_APLY_ADD);			    	    
		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apply_add_det":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

    case "apply_add_det":
		check_authority(5,4,"add");		
		
			if(!$ord = $wi->get($PHP_wi_id)){    //取出該筆 製造令記錄 ID
						$op['msg']= $wi->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
			}
					
		if ($PHP_item == 'lots')
		{
			$parm_mat = array (	'field_name'	=>	'vendor1',
													'field_value'	=>	$PHP_vendor,
													'code'				=>	$PHP_mat_code
												);
			$lots->update_field_code($parm_mat);
			$where_str = "WHERE wi_id =". $PHP_wi_id ." AND lots_used_id =".$PHP_mat_id;
			$mat=$bom->search('bom_lots', $where_str);
			$fd='bom_lots';	
			$mat_cat='l';		
		}else{
			$parm_mat = array (	'field_name'	=>	'vendor1',
													'field_value'	=>	$PHP_vendor,
													'code'				=>	$PHP_mat_code
												);
			$acc->update_field_code($parm_mat);
			$where_str = "WHERE wi_id =". $PHP_wi_id ." AND acc_used_id =".$PHP_mat_id;
			$mat=$bom->search('bom_acc', $where_str);		
			$fd='bom_acc';
			$mat_cat='a';				
		}
		for ($i=0; $i<sizeof($mat); $i++)
		{
			$tmp_qty=explode(',',$mat[$i]['qty']);
			$qty=0;
			for ($j=0; $j<sizeof($tmp_qty); $j++)
			{
				$qty=$qty+$tmp_qty[$j];
			}
	//		echo $mat[$i]['id'];
			$parm_dadd = array( 'ap_num'	=>	$PHP_ap_num,
													'mat_id'	=>	$mat[$i]['id'],
													'eta'			=>	$PHP_eta,
													'qty'			=>	$qty,
													'unit'		=>	$PHP_unit,
													'mat_cat'	=>	$mat_cat,
													);

			$parm_mat = array($mat[$i]['id'],'ap_mark',$PHP_ap_num,$fd);
			$f1 = $apply->add_det($parm_dadd);	//新增AP資料庫
			$f2 = $bom->update_field($parm_mat);			
		}
		
		$op = $apply->get($PHP_ap_num);
		$msg = "Success to add ap : ".$PHP_ap_num." detial";
		$op['msg'][]=$msg;

		$log->log_add(0,"54A",$msg);

		
//		$op['COUNTRY_select']=$arry2->select($COUNTRY,'','PHP_buy_area','select','');
		$op['CURRENCY_select'] = $arry2->select($CURRENCY,'','PHP_CURRENCY','select','');
		$op['FACTORY_select'] = $arry2->select($FACTORY,'','PHP_FACTORY','select','');
		page_display($op, 5, 4, $TPL_APLY_ADD);			    	    
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "del_ap_det_ajax":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "del_ap_det_ajax":
		check_authority(5,4,"add");	
		$f1=$apply->mat_del($PHP_id);

		if ($PHP_mat_cat == 'l') 
		{
			$parm_del = array($PHP_mat_id, 'ap_mark','','bom_lots');
			$bom->update_field($parm_del);
		}else{
			$parm_del = array($PHP_mat_id, 'ap_mark','','bom_acc');
			$bom->update_field($parm_del);
		}
		$msg = "Success to delete ap : ".$PHP_ap_num." mat : ".$PHP_mat_code." color & cat: ".$PHP_color;
		$log->log_add(0,"54A",$msg);

		echo $msg;
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_apply_add":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "do_apply_add":
		check_authority(5,4,"add");	

		$apply->update_2fields('currency','arv_area',$PHP_CURRENCY,$PHP_FACTORY,$PHP_id);
		
		$op = $apply->get($PHP_ap_num);
		$op['ap_det2'][0] = $op['ap_det'][0];
		$k=1;
		for ($i=1; $i<sizeof($op['ap_det']); $i++)
		{
			$mk=0;
			for ($j=0; $j< sizeof($op['ap_det2']); $j++)
			{
				if ($op['ap_det2'][$j]['mat_code'] == $op['ap_det'][$i]['mat_code'] && $op['ap_det2'][$j]['color'] == $op['ap_det'][$i]['color'] && $op['ap_det2'][$j]['unit'] == $op['ap_det'][$i]['unit'] && $op['ap_det2'][$j]['eta'] == $op['ap_det'][$i]['eta'])
				{
					$op['ap_det2'][$j]['ap_qty'] = $op['ap_det'][$i]['ap_qty'] +$op['ap_det2'][$j]['ap_qty'];
					$mk = 1;
				}
			}
			
			if ($mk == 0)
			{
				$op['ap_det2'][$k] = $op['ap_det'][$i];
				$k++;
			}
		}
		$msg = "Success to add ap : ".$PHP_ap_num;
		$op['msg'][]=$msg;

		page_display($op, 5, 4, $TPL_APLY_SHOW);			    	    
		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_apply_add":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "apply_search":
	check_authority(5,4,"view");	

	if (!$op = $apply->search(1, $PHP_dept_code)) {  
				$op['msg']= $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
	$back_str="&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_fab=".$PHP_fab."&PHP_acc=".$PHP_acc."&PHP_supl=".$PHP_supl."&PHP_sr_startno=".$PHP_sr_startno;
	$op['back_str']=$back_str;

	if (!$PHP_dept_code) {   // 如果 不是 呈辦業務部門進入時...
		$op['manager_flag'] = 1;
	}
	$op['dept_id'] = $PHP_dept_code;
	$op['msg']= $apply->msg->get(2);
	page_display($op, 5, 4, $TPL_APLY_LIST);
	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apply_view":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "apply_view":
	check_authority(5,4,"view");	


	$op=$apply->get($PHP_aply_num);
	$back_str="&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_fab=".$PHP_fab."&PHP_acc=".$PHP_acc."&PHP_supl=".$PHP_supl;
	$op['back_str']=$back_str;		
	$op['msg']= $apply->msg->get(2);
	page_display($op, 5, 4, $TPL_APLY_SHOW);			    	    
	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apply_view":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "apply_edit":
	check_authority(5,4,"edit");	


	$op=$apply->get($PHP_aply_num);
	if (!$bom=$apply->bom_edit_search($op['apply']['sup_code'],$PHP_aply_num,$op['apply']['dept'])){  // 當找不到物料資料時......
		$op['msg'][] = "sorry! selected supplier doesn't have any bom record yet!";
		 $layout->assign($op);
		 $layout->display($TPL_ERROR);		    	    
		break;
	}
	$op['bom']=$bom;
	$op['item']=$bom[0]['sup_cat'];
	$op['COUNTRY_select']=$arry2->select($COUNTRY,$op['apply']['sup_area'],'PHP_buy_area','select','');
	$op['CURRENCY_select'] = $arry2->select($CURRENCY,$op['apply']['currency'],'PHP_CURRENCY','select','');
	$op['FACTORY_select'] = $arry2->select($FACTORY,$op['apply']['fty'],'PHP_FACTORY','select','');
	$op['back_str']=$PHP_back_str;		
	$op['msg']= $apply->msg->get(2);
	page_display($op, 5, 4, $TPL_APLY_EDIT);			    	    
	break;
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apply_view":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "apply_revise":
	check_authority(5,4,"edit");	


	$op=$apply->get($PHP_aply_num);
	$revise=$op['apply']['revise']+1;
	$apply->update_2fields('status', 'revise', '0',$revise , $op['apply']['id']);
	if (!$bom=$apply->bom_edit_search($op['apply']['sup_code'],$PHP_aply_num,$op['apply']['dept'])){  // 當找不到物料資料時......
		$op['msg'][] = "sorry! selected supplier doesn't have any bom record yet!";
		 $layout->assign($op);
		 $layout->display($TPL_ERROR);		    	    
		break;
	}
	$op['bom']=$bom;
	$op['item']=$bom[0]['sup_cat'];
	$op['apply']['revise']=$revise;
	$op['COUNTRY_select']=$arry2->select($COUNTRY,$op['apply']['sup_area'],'PHP_buy_area','select','');
	$op['CURRENCY_select'] = $arry2->select($CURRENCY,$op['apply']['currency'],'PHP_CURRENCY','select','');
	$op['FACTORY_select'] = $arry2->select($FACTORY,$op['apply']['fty'],'PHP_FACTORY','select','');
	$back_str="&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_fab=".$PHP_fab."&PHP_acc=".$PHP_acc."&PHP_supl=".$PHP_supl;
	$op['back_str']=$back_str;		
	$op['msg']= $apply->msg->get(2);
	page_display($op, 5, 4, $TPL_APLY_REVISE);			    	    
	break;
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apply_view":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "do_apply_edit_del":  
	check_authority(5,4,"edit");	
	$parm=array(	'code'		=>	$PHP_code,
					'color'		=>	$PHP_color,
					'qty'		=>	$PHP_qty,
					'item'		=>	$PHP_item,
					'aply_num'	=>	$PHP_aply_num,
					'bom_id'	=>	$PHP_bom_id,
					'unit'		=>	$PHP_unit
					);
	$f1=$apply->mat_del($parm);

	
	$op=$apply->get($PHP_aply_num);
	if (!$bom=$apply->bom_edit_search($op['apply']['sup_code'],$PHP_aply_num,$op['apply']['dept'])){  // 當找不到物料資料時......
		$op['msg'][] = "sorry! selected supplier doesn't have any bom record yet!";
		 $layout->assign($op);
		 $layout->display($TPL_ERROR);		    	    
		break;
	}
	if ($f1)
	{
		$message="success to delete materiel :".$PHP_code."on :".$PHP_aply_num;
		$op['msg'][]=$message;
		$log->log_add(0,"54E",$message);
	}else{
		$op['msg']= $apply->msg->get(2);
	}
	$op['bom']=$bom;
	$op['item']=$bom[0]['sup_cat'];
	$op['COUNTRY_select']=$arry2->select($COUNTRY,$op['apply']['sup_area'],'PHP_buy_area','select','');
	$op['CURRENCY_select'] = $arry2->select($CURRENCY,$op['apply']['currency'],'PHP_CURRENCY','select','');
	$op['FACTORY_select'] = $arry2->select($FACTORY,$op['apply']['fty'],'PHP_FACTORY','select','');
	$back_str="&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_fab=".$PHP_fab."&PHP_acc=".$PHP_acc."&PHP_supl=".$PHP_supl;
	$op['back_str']=$back_str;		
		
	if (isset($PHP_revise))
	{
		page_display($op, 5, 4, $TPL_APLY_REVISE);	
	}else{
		page_display($op, 5, 4, $TPL_APLY_EDIT);			    	    
	}
	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apply_view":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "do_apply_edit_add":
	check_authority(5,4,"edit");	
	$parm=array(	'code'		=>	$PHP_code,
					'color'		=>	$PHP_color,
					'qty'		=>	$PHP_qty,
					'item'		=>	$PHP_item,
					'aply_num'	=>	$PHP_aply_num,
					'bom_id'	=>	$PHP_bom_id,
					'unit'		=>	$PHP_unit
					);
	$f1=$apply->mat_add($parm);

	
	$op=$apply->get($PHP_aply_num);
	if (!$bom=$apply->bom_edit_search($op['apply']['sup_code'],$PHP_aply_num,$op['apply']['dept'])){  // 當找不到物料資料時......
		$op['msg'][] = "sorry! selected supplier doesn't have any bom record yet!";
		 $layout->assign($op);
		 $layout->display($TPL_ERROR);		    	    
		break;
	}
	if ($f1)
	{
		$message="success to Add materiel :".$PHP_code."on :".$PHP_aply_num;
		$op['msg'][]=$message;
		$log->log_add(0,"54E",$message);
	}else{
		$op['msg']= $apply->msg->get(2);
	}
	$op['bom']=$bom;
	$op['item']=$bom[0]['sup_cat'];
	$op['COUNTRY_select']=$arry2->select($COUNTRY,$op['apply']['sup_area'],'PHP_buy_area','select','');
	$op['CURRENCY_select'] = $arry2->select($CURRENCY,$op['apply']['currency'],'PHP_CURRENCY','select','');
	$op['FACTORY_select'] = $arry2->select($FACTORY,$op['apply']['fty'],'PHP_FACTORY','select','');
	$back_str="&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_fab=".$PHP_fab."&PHP_acc=".$PHP_acc."&PHP_supl=".$PHP_supl;
	$op['back_str']=$back_str;		
		
	
	if (isset($PHP_revise))
	{
		page_display($op, 5, 4, $TPL_APLY_REVISE);	
	}else{
		page_display($op, 5, 4, $TPL_APLY_EDIT);			    	    
	}
	break;
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apply_view":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "do_apply_edit":
	check_authority(5,4,"view");	
	for ($i=0;  $i< sizeof($PHP_mat_id); $i++)
	{
		$t_name='PHP_eta'.$i;
		$PHP_eta[$i]=$$t_name;			
	}	
	$parm=array(	'sup_area'		=>	$PHP_buy_area,
					'currency'		=>	$PHP_CURRENCY,
					'aply_num'		=>	$PHP_aply_num,
					'fty'			=>	$PHP_FACTORY,
					'eta'			=>	$PHP_eta,
					'mat_id'		=>	$PHP_mat_id
					);
	$f1=$apply->edit($parm);
	
	$op=$apply->get($PHP_aply_num);
	if ($f1)
	{
		$message="success to update apply :".$PHP_aply_num;
		$op['msg'][]=$message;
		$log->log_add(0,"54E",$message);
	}else{
		$op['msg']= $apply->msg->get(2);
	}	
	$op['back_str']=$PHP_back_str;
	if (isset($PHP_revise))
	{
		page_display($op, 5, 4, $TPL_APLY_REVISE_SHOW);	
	}else{
		page_display($op, 5, 4, $TPL_APLY_SHOW);			    	    
	}		
				    	    
	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apply_submit":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "apply_submit":
	check_authority(5,4,"view");	
	
	$f1=$apply->update_2fields('status', 'submit_date', '2', date('Y-m-d'), $PHP_id);
	
	$op=$apply->get($PHP_aply_num);
	if ($f1)
	{
		$message="success to submit apply :".$PHP_aply_num;
		$op['msg'][]=$message;
		$log->log_add(0,"54E",$message);
	}else{
		$op['msg']= $apply->msg->get(2);
	}	
	$op['back_str']=$PHP_back_str;		
	page_display($op, 5, 4, $TPL_APLY_SHOW);			    	    
	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apply_submit":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "do_apply_logs_add":
	check_authority(5,4,"view");	
	
	
	$parm= array(	'ap_num'		=>	$PHP_aply_num,
								'item'			=>	'other',
								'des'				=>	$PHP_des,
								'user'			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
								'k_date'		=>	date('Y-m-d')
				);
	$f1=$apply->add_log($parm);
	
	$op=$apply->get($PHP_aply_num);
	if ($f1)
	{
		$message="success to add log :".$PHP_aply_num;
		$op['msg'][]=$message;
//		$log->log_add(0,"54E",$message);
	}else{
		$op['msg']= $apply->msg->get(2);
	}	
	$op['back_str']=$PHP_back_str;		
	page_display($op, 5, 4, $TPL_APLY_SHOW);			    	    
	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_apply_add":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "apply_apv_search":
	check_authority(5,4,"view");	

	if (!$op = $apply->apv_search()) {  
				$op['msg']= $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}


	$op['msg']= $apply->msg->get(2);
	page_display($op, 5, 4, $TPL_APLY_APV_LIST);
	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apply_view":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "apply_apv_view":
	check_authority(5,4,"view");	


	$op=$apply->get($PHP_aply_num);
	
	$op['now_pp']=$PHP_sr_startno;		
	$op['msg']= $apply->msg->get(2);
	page_display($op, 5, 4, $TPL_APLY_APV_SHOW);			    	    
	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_apply_add":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "reject_apply_apv":
	check_authority(5,4,"view");	

	$f1=$apply->update_2fields('status', 'submit_date', '5', '0000-00-00', $PHP_id);
	$parm= array(	'aply_num'		=>	$PHP_aply_num,
					'des'			=>	" APV Rejeckt apply for:".$PHP_detail,
					'user'			=>	$GLOBALS['SCACHE']['ADMIN']['name'],
					'k_date'		=>	date('Y-m-d')
				);
	$f1=$apply->add_log($parm);
	if (!$op = $apply->apv_search()) {  
				$op['msg']= $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}


	$op['msg']= $apply->msg->get(2);
	page_display($op, 5, 4, $TPL_APLY_APV_LIST);
	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_apply_add":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "do_apply_apv":
	check_authority(5,4,"view");	

	$parm = array ( 'id'		=>	$PHP_id,
					'apv_user'	=>	$GLOBALS['SCACHE']['ADMIN']['name'],
					'apv_date'	=>	date('Y-m-d'),
					'status'	=>	4,
					'sup_code'	=>	$PHP_sup_code,
					'aply_num'	=>	$PHP_aply_num
					);
	$apply->apply_apv($parm);
	if (!$op = $apply->apv_search()) {  
				$op['msg']= $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}


	$op['msg']= $apply->msg->get(2);
	page_display($op, 5, 4, $TPL_APLY_APV_LIST);
	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apply":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "purch":
		check_authority(5,5,"view");
		$where_str = $manager = $manager_v = $dept_id = '';
		$dept_ary = array();
		$sales_dept_ary = get_sales_dept(); // 取出 業務的部門 [不含K0] ------
		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];   // 判定進入身份的指標
		
		$op['factory'] = $arry2->select($FACTORY,'','PHP_factory','select','');
		
		for ($i=0; $i<count($sales_dept_ary);$i++){
			if($user_dept == $sales_dept_ary[$i]){

				// 如果是業務部進入 則dept_code 指定該業務部---
				$dept_id = $sales_dept_ary[$i];  
			}
		}

		if (!$dept_id) {    // 當不是業務部人[也不含 K0 的人 ]進入時			
			$manager_v = 1;
		} 


		$op['manager_v_flag'] = $manager_v;
		$op['dept_id'] = $dept_id;		

		$op['msg'] = $order->msg->get(2);
		  	
		page_display($op, 5, 5, $TPL_PURCH);			    	    
		break;





#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_apply_add":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "purch_search":
	check_authority(5,5,"view");	

	if (!$op = $apply->purchase_search()) {  
				$op['msg']= $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

	$back_str="&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_fab=".$PHP_fab."&PHP_acc=".$PHP_acc."&PHP_supl=".$PHP_supl."&PHP_sr_startno=".$PHP_sr_startno;
	$op['back_str']=$back_str;

	if (!$PHP_dept_code) {   // 如果 不是 呈辦業務部門進入時...
		$op['manager_flag'] = 1;
	}
	$op['dept_id'] = $PHP_dept_code;

	$op['msg']= $apply->msg->get(2);
	page_display($op, 5, 5, $TPL_PURCH_LIST);
	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "purch_view":			job 55
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "purch_view":
	check_authority(5,5,"view");	


	$op=$apply->get($PHP_aply_num);
	for ($i=0; $i<sizeof($op['apply_det']); $i++)
	{
			$op['apply_det'][$i]['s_total']=get_currency($op['apply_det'][$i]['s_total']);
	}
	$back_str="&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_fab=".$PHP_fab."&PHP_acc=".$PHP_acc."&PHP_supl=".$PHP_supl;
	$op['back_str']=$back_str;		
	
	$op['msg']= $apply->msg->get(2);
	page_display($op, 5, 5, $TPL_PURCH_SHOW);			    	    
	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "purch_edit":			job 55
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "purch_edit":
	check_authority(5,5,"view");	


	$op=$apply->get($PHP_aply_num);
	
	$supl=$supl->get_sname($op['apply']['sup_code']);
	for ($i=0; $i<sizeof($op['apply_det']); $i++)
	{
			$op['apply_det'][$i]['cost']=$apply->get_fab_cost($op['apply_det'][$i]['aply_mat'],$op['apply']['sup_code'],$supl['supl_cat']);
			$op['apply_det'][$i]['j']=$i;
	}
	$op['back_str']=$PHP_back_str;		
	$op['COUNTRY_select']=$arry2->select($COUNTRY,$op['apply']['sup_area'],'PHP_buy_area','select','');
	$op['CURRENCY_select'] = $arry2->select($CURRENCY,$op['apply']['currency'],'PHP_CURRENCY','select','');
	$op['FACTORY_select'] = $arry2->select($FACTORY,$op['apply']['fty'],'PHP_fty','select','');
	$op['msg']= $apply->msg->get(2);
	page_display($op, 5, 5, $TPL_PURCH_EDIT);			    	    
	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "purch_edit":			job 55
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "do_purch_edit":
	check_authority(5,5,"view");	
	$parm=array(	'aply_id'		=>	$PHP_aply_id,
					'pur_qty'		=>	$PHP_pur_qty,
					'pur_cost'		=>	$PHP_pur_cost,
					'sup_area'		=>	$PHP_buy_area,
					'currency'		=>	$PHP_CURRENCY,
					'fty'			=>	$PHP_fty,
					'aply_num'		=>	$PHP_aply_num
				);

	$f1=$apply->edit($parm);
	for ($i=0; $i<sizeof($parm['aply_id']); $i++)
	{
		$f1=$apply->update_2fields('mat_cost', 'pur_qty', $PHP_pur_cost[$i], $PHP_pur_qty[$i], $PHP_aply_id[$i],'apply_det');
	}
	$op=$apply->get($PHP_aply_num);
	

	for ($i=0; $i<sizeof($op['apply_det']); $i++)
	{
			$op['apply_det'][$i]['s_total']=get_currency($op['apply_det'][$i]['s_total']);
	}
	$op['back_str']=$PHP_back_str;		
	$op['msg']= $apply->msg->get(2);
	if (isset($PHP_revise))
	{
		page_display($op, 5, 5, $TPL_PURCH_REVISE_SHOW);	
	}else{
		page_display($op, 5, 5, $TPL_PURCH_SHOW);			    	    
	}				    	    
	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apply_submit":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "purch_submit":
	check_authority(5,4,"view");	
	
	$f1=$apply->update_2fields('status', 'pur_submit', '6', date('Y-m-d'), $PHP_id);
	
	$op=$apply->get($PHP_aply_num);
	if ($f1)
	{
		$message="success to submit purchase :".$PHP_aply_num;
		$op['msg'][]=$message;
		$log->log_add(0,"55E",$message);
	}else{
		$op['msg']= $apply->msg->get(2);
	}	
	$op['back_str']=$PHP_back_str;		
	page_display($op, 5, 5, $TPL_PURCH_SHOW);			    	    
	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_apply_add":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "purch_apv_search":
	check_authority(5,6,"view");	

	if (!$op = $apply->purch_apv_search()) {  
				$op['msg']= $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}


	$op['msg']= $apply->msg->get(2);
	page_display($op, 5, 6, $TPL_PURCH_APV_LIST);
	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "purch_apv_view":			job 56
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "purch_apv_view":
	check_authority(5,6,"view");	


	$op=$apply->get($PHP_aply_num);
	
	$op['now_pp']=$PHP_sr_startno;		
	$op['msg']= $apply->msg->get(2);
	page_display($op, 5, 6, $TPL_PURCH_APV_SHOW);			    	    
	break;
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "reject_purch_apv":			job 56
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "reject_purch_apv":
	check_authority(5,4,"add");	

	$f1=$apply->update_2fields('status', 'pur_submit', '9', '0000-00-00', $PHP_id);
	$parm= array(	'aply_num'		=>	$PHP_aply_num,
					'des'			=>	" APV Rejeckt purchase	 for:".$PHP_detail,
					'user'			=>	$GLOBALS['SCACHE']['ADMIN']['name'],
					'k_date'		=>	date('Y-m-d')
				);
	$f1=$apply->add_log($parm);
	if (!$op = $apply->apv_search()) {  
				$op['msg']= $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}


	$op['msg']= $apply->msg->get(2);
	page_display($op, 5, 6, $TPL_APLY_APV_LIST);
	break;
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_apply_add":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "do_purch_apv":
	check_authority(5,4,"add");	

	$parm = array ( 'id'		=>	$PHP_id,
					'apv_user'	=>	$GLOBALS['SCACHE']['ADMIN']['name'],
					'apv_date'	=>	date('Y-m-d'),
					'status'	=>	8
					);
	$apply->purch_apv($parm);
	if (!$op = $apply->apv_search()) {  
				$op['msg']= $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}


	$op['msg']= $apply->msg->get(2);
	page_display($op, 5, 4, $TPL_APLY_APV_LIST);
	break;	
	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apply_view":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "purch_revise":
	check_authority(5,4,"edit");	


	$op=$apply->get($PHP_aply_num);
	
	$revise=$op['apply']['revise']+1;
	$apply->update_2fields('status', 'pur_revise', '4',$revise , $op['apply']['id']);
	if (!$bom=$apply->bom_edit_search($op['apply']['sup_code'],$PHP_aply_num,$op['apply']['dept'])){  // 當找不到物料資料時......
		$op['msg'][] = "sorry! selected supplier doesn't have any bom record yet!";
		 $layout->assign($op);
		 $layout->display($TPL_ERROR);		    	    
		break;
	}
	
	$supl=$supl->get_sname($op['apply']['sup_code']);
	for ($i=0; $i<sizeof($op['apply_det']); $i++)
		{
			$op['apply_det'][$i]['cost']=$apply->get_fab_cost($op['apply_det'][$i]['aply_mat'],$op['apply']['sup_code'],$supl['supl_cat']);
			$op['apply_det'][$i]['j']=$i;
		}
	
	$back_str="&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_fab=".$PHP_fab."&PHP_acc=".$PHP_acc."&PHP_supl=".$PHP_supl;
	$op['back_str']=$back_str;		
	$op['apply']['revise']=$revise;
	$op['COUNTRY_select']=$arry2->select($COUNTRY,$op['apply']['sup_area'],'PHP_buy_area','select','');
	$op['CURRENCY_select'] = $arry2->select($CURRENCY,$op['apply']['currency'],'PHP_CURRENCY','select','');
	$op['FACTORY_select'] = $arry2->select($FACTORY,$op['apply']['fty'],'PHP_fty','select','');

	$op['msg']= $apply->msg->get(2);
	page_display($op, 5, 4, $TPL_PURCH_REVISE);    	    
	break;
	
}
?>