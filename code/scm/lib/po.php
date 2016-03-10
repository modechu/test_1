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

/*
include_once($config['root_dir']."/lib/class.notify.php");
$notify = new NOTIFY();
if (!$notify->init($mysql,"log")) { print "error!! cannot initialize database for LOG class"; exit; }
*/



switch ($PHP_action) {



#++++++++++++++    SAMPLE ORDER +++++++++++++  2007/09/11  +++++++++++++++++
#		 job 54    請購記錄 
#++++++++++++++++++++++++++++++++++++++++++++  2007/09/11  +++++++++++++++++
#		case "po":										job 54		採購新增/search畫面
#		case "po_search_bom":					job 54		新增採購--搜尋要採購的訂單
#		case "po_bom_view":						job 54		新增採購--檢視要採購的訂單(BOM表)
#		case "po_add":								job 54		新增採購內容
#		case "chg_unit_ajax":					job 54		新增採購--改變採購單位
#		case "count_range_ajax":			job 54		新增採購--計算採購增加量(範圍)
#		case "add_po_qty":						job 54		新增採購--儲存採購量
#		case "po_view":								job 54		採購內容檢視
#		case "do_apply_logs_add":			job 54		Log檔記錄
#		case "po_edit":								job 54		採購修改
#		case "po_revise":							job 54		採購Revise
#		case "po_search":							job 54		採購列表搜尋
#		case "po_submit":							job 54		採購submit
#		case "po_cfm_search":					job 54		採購列表搜尋--需CFM項目
#		case "do_po_cfm":							job 54		採購Confrim
#		case "reject_po_cfm":					job 54		採購reject (CFM時)
#		case "po_apv_search":					job 54		採購列表搜尋--需APV項目
#		case "po_apv_view":						job 54		採購apv畫面檢視
#		case "do_po_apv":							job 54		採購approval
#		case "reject_po_apv":					job 54		採購reject


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apply_edit":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

    case "apply_edit":
		check_authority(5,1,"edit");		
	$where_str="AND( item like '%special%')";		
	$op = $apply->get($PHP_aply_num,$where_str);

				
//組合畫面List
	if (isset($op['ap_det']))
	{
			$op['ap_det2'] = $apply->group_ap($op['ap_det']);	//組同請購明細
	}
	
	
	if ($op['ap']['arv_area'])
	{
		$op['ap']['ship_name'] = $SHIP_TO[$op['ap']['arv_area']]['Name'];
		$op['ap']['ship_addr'] = $SHIP_TO[$op['ap']['arv_area']]['Addr'];
		$op['ap']['ship_tel']  = $SHIP_TO[$op['ap']['arv_area']]['TEL'];
		$op['ap']['ship_fax']  = $SHIP_TO[$op['ap']['arv_area']]['FAX'];
		$op['ap']['ship_attn'] = $SHIP_TO[$op['ap']['arv_area']]['Attn'];
	}

		$op['CURRENCY_select'] = $arry2->select($CURRENCY,$op['ap']['currency'],'PHP_CURRENCY','select','');
		$op['FACTORY_select'] = $arry2->select($SHIP,$op['ap']['arv_area'],'PHP_FACTORY','select',"ship_show(this)");
		for ($i=0; $i< 4; $i++)
		{
			if ($op['ap']['dm_way'] == $dm_way[1][$i]) $op['ap']['dm_way']=$dm_way[0][$i];
		}

		$op['dm_way_select'] = $arry2->select($dm_way[0],$op['ap']['dm_way'],"PHP_dm_way","select","");


		$op['back_str']=$PHP_back_str;
		
		page_display($op, 5, 1, $TPL_APLY_EDIT);			    	    
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "mat_edit":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "mat_edit":
//		check_authority(5,1,"edit");
		$id = explode('|',$PHP_id_comp);
		$s_total=0;
		$tmp=0;
		if ($PHP_special == 2){$PHP_table = 'ap_special';}else{$PHP_table = 'ap_det';}
		for ($i=0; $i< sizeof($id); $i++)		//取得每項的請購量,並加總
		{
			$where_str = "id = ".$id[$i];
			$row[$i]=$po->get_det_field('ap_qty',$PHP_table,$where_str);
			$s_total = $s_total + $row[$i]['ap_qty'];
		}		
		for ($i=0; $i< (sizeof($id)-1); $i++)		//計算每項採購量(平分)
		{
			$avg_qty[$i] = $PHP_qty * ($row[$i]['ap_qty'] / $s_total);
			$avg_qty[$i] = number_format($avg_qty[$i],2,'.','');
			$tmp = $tmp + $avg_qty[$i];
		}	
		$tmp_qty = $PHP_qty - $tmp;
		$avg_qty[$i] = $tmp_qty;
		$avg_qty[$i] = number_format($avg_qty[$i],2,'.','');
		
		
		for ($i=0; $i< sizeof($id); $i++)		//加入DB
		{
			$f1=$apply->update_fields_id('ap_qty',$avg_qty[$i], $id[$i], $PHP_table);
			$f1=$apply->update_fields_id('eta',$PHP_eta, $id[$i], $PHP_table);
		}	
		$i--;
		$po_spare =$po->get_det_field('po_spare',$PHP_table,$where_str);
		$spares = explode('|',$po_spare['po_spare']);
		for($i=0; $i< sizeof($spares); $i++)	
		{
			$f1=$apply->update_fields_id('po_qty',0, $spares[$i], $PHP_table);
			$f1=$apply->update_fields_id('po_unit','', $spares[$i], $PHP_table);
		}
		
		$tmp_num = substr($PHP_ap_num,2);
		$po_num = "PO".$tmp_num;
		
		$message = "Successfully add P/O detial : ".$po_num."record。";
		$log->log_add(0,"51E",$message);		
		
		$message = "Successfully Edit P/O : ".$po_num." record";
		echo $message;
break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_apply_edit":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "do_apply_edit":
		check_authority(5,1,"edit");	
/*
		$apply->update_2fields('currency','arv_area',$PHP_CURRENCY,$PHP_FACTORY,$PHP_id);
		$apply->update_fields_id('dm_way',$PHP_dmway,$PHP_id);	//Update 幣別
	
		$parm = array( 'field_name'		=>	'dm_way',
									 'field_value'	=>	$PHP_dmway,
									 'id'						=>	$PHP_sup_id
									 );
		$supl->update_field($parm);
	
		$parm_log = array(	'ap_num'	=>	$PHP_ap_num,
												'item'		=>	'special',
												'des'			=>	$PHP_des,
												'user'		=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
												'k_date'	=>	$TODAY,
								);
		if ($PHP_des <> $PHP_old_des)
		{
			$f1=$apply->add_log($parm_log);		
		}

	  if (!$PHP_FACTORY)	//若無運送地(ID)時,記錄運送地詳細資料
	  {
	  	$apply->update_fields_id('ship_addr',$PHP_saddr,$PHP_id);		//運送地址
			$apply->update_fields_id('ship_name',$PHP_sname,$PHP_id);		//運送地公司名
			$apply->update_fields_id('ship_fax',$PHP_sfax,$PHP_id);			//運送地傳真
			$apply->update_fields_id('ship_tel',$PHP_stel,$PHP_id);			//運送地電話
			$apply->update_fields_id('ship_attn',$PHP_sattn,$PHP_id);	  //運送地聯絡人
	  }  


		$log_where = "AND item <> 'special'";	
		$op = $apply->get($PHP_ap_num,$log_where);
		
		
		if (isset($op['ap_det']))
		{
			$op['ap_det2'] = $apply->group_ap($op['ap_det']);	//組同請購明細
		}

		$msg = "Successfully update P/A : ".$PHP_ap_num." main record";
		$op['msg'][]=$msg;
//Ship TO
	if ($op['ap']['arv_area'])
	{
		$op['ap']['ship_name'] = $SHIP_TO[$op['ap']['arv_area']]['Name'];
		$op['ap']['ship_addr'] = $SHIP_TO[$op['ap']['arv_area']]['Addr'];
		$op['ap']['ship_tel']  = $SHIP_TO[$op['ap']['arv_area']]['TEL'];
		$op['ap']['ship_fax']  = $SHIP_TO[$op['ap']['arv_area']]['FAX'];
		$op['ap']['ship_attn'] = $SHIP_TO[$op['ap']['arv_area']]['Attn'];
	}

//Payment
	for ($i=0; $i< 4; $i++)
	{
		if ($op['ap']['dm_way'] == $dm_way[1][$i]) $op['ap']['dm_way']=$dm_way[0][$i];
	}

//		$op['back_str']=$PHP_back_str;
	*/
		$f1=$apply->update_fields_id('special',$PHP_new_special,$PHP_id);	 //記錄申請日

			if (isset($PHP_SCH_num))
			{
				$back_str="?PHP_action=apply_search&PHP_dept_code=".$PHP_dept_code."&PHP_SCH_num=".$PHP_SCH_num."&PHP_SCH_fty=".$PHP_SCH_fty."&PHP_SCH_cust=".$PHP_SCH_cust."&PHP_SCH_supl=".$PHP_SCH_supl."&PHP_sr_startno=".$PHP_sr_startno;				
			}else{
				$back_str="?PHP_action=apply_search&PHP_dept_code=&PHP_SCH_num&PHP_SCH_fty=&PHP_SCH_cust=&PHP_SCH_supl=&PHP_sr_startno=";
			}
			$op['back_str']=$back_str;
		if ($op['ap']['revise'] == 0 && $op['ap']['apv_user']) $op['ap']['revise'] =$op['ap']['revise']+1;
	
		$redir_str = "po.php?PHP_action=po_revise&PHP_aply_num=".$PHP_ap_num."&PHP_revise=1&PHP_id=".$op['ap']['id']."&PHP_new_special=".$PHP_new_special;
		redirect_page($redir_str);

		if ($PHP_revise == 1)
		{
			page_display($op, 5, 1, $TPL_APLY_SHOW_REV);
		}else{
			page_display($op, 5, 1, $TPL_APLY_SHOW);			    	    
		}		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apply_revise":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

    case "apply_revise":
		check_authority(5,1,"edit");		
	$f1=$apply->update_2fields('apv_user', 'apv_date','','0000-00-00', $PHP_id);
	$f1=$apply->update_2fields('cfm_user', 'cfm_date','','0000-00-00', $PHP_id);
	$f1=$apply->update_2fields('submit_user', 'submit_date','','0000-00-00', $PHP_id);

	$f1=$apply->update_2fields('po_apv_user', 'po_apv_date','','0000-00-00', $PHP_id);
	$f1=$apply->update_2fields('po_cfm_user', 'po_cfm_date','','0000-00-00', $PHP_id);
	$f1=$apply->update_2fields('po_sub_date', 'po_sub_date','','0000-00-00', $PHP_id);


	$f1=$apply->update_fields('status',6, $PHP_aply_num);
	$f1=$apply->update_fields('revise',($PHP_rev_time+1), $PHP_aply_num);		

	$where_str="AND( item like '%special%')";		
	$op = $apply->get($PHP_aply_num,$where_str);
				
//組合畫面List
		if (isset($op['ap_det']))
		{
			$op['ap_det2'] = $apply->group_ap($op['ap_det']);	//組同請購明細
		}

	if ($op['ap']['arv_area'])
	{
		$op['ap']['ship_name'] = $SHIP_TO[$op['ap']['arv_area']]['Name'];
		$op['ap']['ship_addr'] = $SHIP_TO[$op['ap']['arv_area']]['Addr'];
		$op['ap']['ship_tel']  = $SHIP_TO[$op['ap']['arv_area']]['TEL'];
		$op['ap']['ship_fax']  = $SHIP_TO[$op['ap']['arv_area']]['FAX'];
		$op['ap']['ship_attn'] = $SHIP_TO[$op['ap']['arv_area']]['Attn'];
	}
		
		$op['CURRENCY_select'] = $arry2->select($CURRENCY,$op['ap']['currency'],'PHP_CURRENCY','select','');
		$op['FACTORY_select'] = $arry2->select($FACTORY,$op['ap']['arv_area'],'PHP_FACTORY','select','');
		for ($i=0; $i< 4; $i++)
		{
			if ($op['ap']['dm_way'] == $dm_way[1][$i]) $op['ap']['dm_way']=$dm_way[0][$i];
		}

		$op['dm_way_select'] = $arry2->select($dm_way[0],$op['ap']['dm_way'],"PHP_dm_way","select","");

		$op['back_str']='';
		$op['revise']=1;
		page_display($op, 5, 1, $TPL_APLY_EDIT);			    	    
		break;

















#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

    case "po":
		check_authority(5,1,"view");
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
		$op['factory_select'] = $arry2->select($SHIP,'','PHP_SCH_fty','select','');
		$op['cust_sch_select'] =  $arry2->select($cust_value,'','PHP_SCH_cust','select','',$cust_def_vue); 


		$op['manager_flag'] = $manager;
		$op['manager_v_flag'] = $manager_v;
		$op['dept_id'] = $dept_id;
		$op['dept_ary'] = $dept_ary;

		$op['msg'] = $order->msg->get(2);
		  	
		page_display($op, 5, 1, $TPL_PO);			    	    
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_search_bom":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "po_search_bom":   //  先找出製造令.......

		check_authority(5,1,"view");
		
			if (!$op = $bom->search_pa()) {
				$op['msg']= $wi->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			// op 被清除了 需再判斷一次 
			for ($i=0; $i< sizeof($op['wi']); $i++)
			{
				$op['wi'][$i]['acc_ap'] = $po->chk_po_ok($op['wi'][$i]['id'], 'bom_acc','a');
				$op['wi'][$i]['lots_ap'] = $po->chk_po_ok($op['wi'][$i]['id'], 'bom_lots','l');
			}
			
			

		$op['msg']= $bom->msg->get(2);				
		$back_str="&PHP_dept_code=".$PHP_dept_code."&PHP_cust=".$PHP_cust."&PHP_num=".$PHP_num;
		$op['back_str']=$back_str;
		
		page_display($op, 5,1, $TPL_PO_BOM_LIST);
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_bom_view":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "po_bom_view":

		check_authority(5,1,"view");
		//-------------------- 將 製造令 show out ------------------
		//  wi 主檔
		if(!$op['wi'] = $wi->get($PHP_id)){    //取出該筆 製造令記錄
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}
		//  smpl 樣本檔
		if(!$op['smpl'] = $order->get($op['wi']['smpl_id'])){
					$op['msg']= $order->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}
		//-------------------- wi_qty 數量檔 -------------------------------
			$where_str = " WHERE wi_id='".$op['wi']['id']."' ";
		$T_wiqty = $wiqty->search(1,$where_str);

		$num_colors = count($T_wiqty);
		$op['num_colors'] = $num_colors;
		// 取出 size_scale ----------------------------------------------
		$size_A = $size_des->get($op['smpl']['size']);
		$op['smpl']['size_scale']=$size_A['size_scale'];
			//????????????????????????????????????? 注意 有時沒有 size type 資料時??????
		$sizing = explode(",", $size_A['size']);

			// 做出 size table-------------------------------------------
		$reply = show_breakdown($T_wiqty,$sizing,$size_A['base_size']);
		$op['show_breakdown'] = $reply['html'];
		$op['total'] = $reply['total'];   // 總件數
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

		//  取出  BOM  副料記錄 --------------------------------------------------------------
		$op['bom_acc_NONE']= '';
		$op['bom_acc'] = $po->get_acc_det($op['wi']['id']);  //取出該筆 bom 內ALL副料記錄
		$num_bom_acc = count($op['bom_acc']);
		if (!$num_bom_acc){	$op['bom_acc_NONE'] = "1";		}

		//  取出  BOM  加購記錄 --------------------------------------------------------------
		$op['ext_mat_NONE']= '';
		$op['ext_mat'] = $po->get_ext_ap($op['wi']['wi_num']);  //取出該筆 bom 內ALL加購記錄
		$num_ext_mat = count($op['ext_mat']);
		if (!$num_ext_mat){	$op['ext_mat_NONE'] = "1";		}

		$back_str="?PHP_action=po_search_bom&PHP_dept_code=".$PHP_dept_code."&PHP_cust=".$PHP_cust."&PHP_num=".$PHP_num."&PHP_sr_startno=".$PHP_sr_startno;
		$op['back_str']=$back_str;

			page_display($op, 5,1, $TPL_PO_BOM_VIEW);

		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_add":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "po_add":
		check_authority(5,1,"add");	

		//echo $PHP_aply_num;
	$op = $po->get($PHP_aply_num);
	
	if (isset($op['ap_det']))
	{
		$op['ap_det2'][0] = $op['ap_det'][0];
		$op['ap_det2'][0]['orders'][0] = $op['ap_det'][0]['ord_num'];
		$op['ap_det2'][0]['ids'] = $op['ap_det'][0]['id'];
		if ($op['ap_det'][0]['po_unit']){$units = $op['ap_det'][0]['po_unit'];}else{$units = $op['ap_det'][0]['unit'];}
		if ($op['ap_det'][0]['prc_unit']){$prc_units = $op['ap_det'][0]['prc_unit'];}else{$prc_units = $op['ap_det'][0]['unit'];}

		$op['ap_det2'][0]['Unit_select'] = $po->get_unit_group($units,$op['ap_det'][0]['id']);
		$op['ap_det2'][0]['prc_unit_select'] = $po->get_unit_group($prc_units,$op['ap_det'][0]['id'],'PHP_prc_unit');


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
					for ($z =0; $z < sizeof($op['ap_det2'][$j]['orders']); $z++)
					{
						if ($op['ap_det2'][$j]['orders'][$z] == $op['ap_det'][$i]['ord_num'])
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

				//if ($op['ap_det'][$i]['mat_cat'] == 'a') $op['ap_det2'][$k]['Unit_select'] = $arry2->select($ACC_PRICE_UNIT,$units,'PHP_unit'.$op['ap_det'][$i]['id'],'select','chg_unit('.$op['ap_det'][$i]['id'].')');
				//if ($op['ap_det'][$i]['mat_cat'] == 'l') $op['ap_det2'][$k]['Unit_select'] = $arry2->select($LOTS_PRICE_UNIT,$units,'PHP_unit'.$op['ap_det'][$i]['id'],'select','chg_unit('.$op['ap_det'][$i]['id'].')');
				$op['ap_det2'][$k]['Unit_select'] = $po->get_unit_group($units,$op['ap_det'][$i]['id']);
				$op['ap_det2'][$k]['prc_unit_select'] = $po->get_unit_group($prc_units,$op['ap_det'][$i]['id'],'PHP_prc_unit');

				$k++;
			}
		}
	}
	if (isset($op['ap_spec']))
	{
		for ($i=0; $i<sizeof($op['ap_spec']); $i++)
		{
				if ($op['ap_spec'][$i]['po_unit']){$units = $op['ap_spec'][$i]['po_unit'];}else{$units = $op['ap_spec'][$i]['unit'];}
				if ($op['ap_spec'][$i]['prc_unit']){$prc_units = $op['ap_spec'][$i]['prc_unit'];}else{$prc_units = $op['ap_spec'][$i]['unit'];}

				$op['ap_spec'][$i]['Unit_select'] = $po->get_unit_group($units,"e".$op['ap_spec'][$i]['id']);
				$op['ap_spec'][$i]['prc_unit_select'] = $po->get_unit_group($prc_units,"e".$op['ap_spec'][$i]['id'],'PHP_prc_unit');

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


		$op['CURRENCY_select'] = $arry2->select($CURRENCY,$op['ap']['currency'],'PHP_CURRENCY','select','');
		$op['FACTORY_select'] = $arry2->select($SHIP,$op['ap']['arv_area'],'PHP_FACTORY','select',"ship_show(this)");
		for ($i=0; $i< 4; $i++)
		{
			if ($op['ap']['dm_way'] == $dm_way[1][$i]) $op['ap']['dm_way']=$dm_way[0][$i];
		}
		$op['dm_way_select'] = $arry2->select($dm_way[0],$op['ap']['dm_way'],"PHP_dm_way","select","");
		$op['FOB_select'] = $arry2->select($TRADE_TERM,$op['ap']['fob'],'PHP_FOB','select','');

	
		if (isset($PHP_wi_id))
		{
			$back_str="?PHP_action=po_bom_view&PHP_id=".$PHP_wi_id."&PHP_dept_code=".$PHP_dept_code."&PHP_cust=".$PHP_cust."&PHP_num=".$PHP_num."&PHP_sr_startno=".$PHP_sr_startno;
			$op['back_str']=$back_str;
			$op['add_back'] ="po_bom_view";
		}
//	if (isset($PHP_message)) $op['msg'][]=$PHP_message;

		page_display($op, 5, 1, $TPL_PO_ADD);			    	    
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "chg_unit_ajax":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "chg_unit_ajax":   

		$qty=$PHP_qty;
		$rtn_qty = change_unit_qty($PHP_ounit,$PHP_nunit,$qty);		
		if ($PHP_ounit == $PHP_nunit) $rtn_qty = $qty;
			if ($rtn_qty)
			{
				printf('%.2f',$rtn_qty);	
			}else{
				echo "no|".$PHP_ounit."|".$PHP_nunit."|".$qty;
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
		$tmp_amt = 0;
		$rtn_price = change_unit_price($PHP_prc_unit,$PHP_unit,$PHP_price);
		for ($i=0; $i< sizeof($id); $i++)		//取得每項的請購量,並加總
		{
			$where_str = "id = ".$id[$i];
			$row[$i]=$po->get_det_field('ap_qty',$PHP_table,$where_str);
			$s_total = $s_total + $row[$i]['ap_qty'];
		}		
		for ($i=0; $i< (sizeof($id)-1); $i++)		//計算每項採購量(平分)
		{
			$po_qty[$i] = $PHP_qty * ($row[$i]['ap_qty'] / $s_total);
			$po_qty[$i] = number_format($po_qty[$i],2,'.','');
			$po_amt[$i] = $po_qty[$i] * $rtn_price;
			$po_amt[$i] = number_format($po_amt[$i],2,'.','');
			$tmp = $tmp + $po_qty[$i];
			$tmp_amt += $po_amt[$i];
		}	
		$tmp_qty = $PHP_qty - $tmp;
		$po_qty[$i] = $tmp_qty;
		$po_qty[$i] = number_format($po_qty[$i],2,'.','');
		$po_amt[$i] = $po_qty[$i] * $rtn_price;
		$po_amt[$i] = number_format($po_amt[$i],2,'.','');
		$tmp_amt += $po_amt[$i];
		
		
		for ($i=0; $i< sizeof($id); $i++)		//加入DB
		{
			$f1=$apply->update_fields_id('po_qty',$po_qty[$i], $id[$i], $PHP_table);
			$f1=$apply->update_fields_id('po_spare',$PHP_det_id, $id[$i], $PHP_table);	
			$f1=$apply->update_fields_id('prics',$PHP_price, $id[$i], $PHP_table);	
			$f1=$apply->update_fields_id('po_eta',$PHP_eta, $id[$i], $PHP_table);	
			
			$f1=$apply->update_fields_id('prc_unit',$PHP_prc_unit, $id[$i], $PHP_table);
			$f1=$apply->update_fields_id('amount',$po_amt[$i], $id[$i], $PHP_table);
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
		$message = "Successfully add qty for P/O # :".$po_num."| PO Q'ty : ".$PHP_qty."&nbsp;".$PHP_unit."&nbsp;&nbsp;&nbsp; Range  : ".$PHP_range."% &nbsp;&nbsp;&nbsp; Price : @ $".$PHP_price."/".$PHP_prc_unit;
		$message = $message."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=button size=30 class=select_e value='EDIT' onclick=\"po_edit_p2('$PHP_id')\";>|$PHP_eta|$tmp_amt";
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
#	case "add_po_toler":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "add_po_dear":

		$f1=$apply->update_fields('dear', $PHP_dear, $PHP_po_num);	//儲存總金額		

		$PHP_po_num=str_replace("A", "O",$PHP_po_num);
		
		$message = "Successful modification  Deal Amount for P/O # :".$PHP_po_num;
		echo $message;
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "add_po_toler":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "add_oth_cost":

		$parm = array( "ap_num"	=>	$PHP_po_num,
									 "item"		=>	$PHP_item,
									 "cost"		=>	$PHP_cost
									 );
		$f1=$po->add_other_cost($parm);	//儲存總金額		

		$PHP_po_num=str_replace("A", "O",$PHP_po_num);
		
		$message = "Successful ADD others item for P/O # :".$PHP_po_num;
		$oth_show = "<b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Others&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Describe : ".$PHP_item." &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Price : ".$PHP_cost."</b>";
		$oth_botton = "<input type='image' src='images/del.gif' height=20 onclick=\"del_cost('".$f1."','$PHP_cost','$PHP_po_num',this)\">";	


		echo $message."|".$oth_show."|".$oth_botton."|".$PHP_cost;
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "add_po_toler":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "oth_cost_del":

		$f1=$po->del_cost($PHP_cost_id);	//儲存總金額		

//		$PHP_po_num=str_replace("A", "O",$PHP_po_num);
		
		$message = "Successful Delete others item for P/O # :".$PHP_po_num;
		echo $message;
break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_view":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "po_view":
		check_authority(5,1,"view");	
 //echo $PHP_aply_num;
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
		$back_str="?PHP_action=po_search&PHP_dept_code=".$PHP_dept_code."&PHP_SCH_num=".$PHP_SCH_num."&PHP_SCH_fty=".$PHP_SCH_fty."&PHP_SCH_cust=".$PHP_SCH_cust."&PHP_SCH_supl=".$PHP_SCH_supl."&PHP_sr_startno=".$PHP_sr_startno;
	}else if(isset($PHP_add_back) && $PHP_add_back){
		if (isset($op['ap_det2'][0]['ord_num']))
		{
			$wi_id = $wi->get_field($op['ap_det2'][0]['ord_num'],'id');
		}else{
			$wi_id = $wi->get_field($op['ap_spec'][0]['ord_num'],'id');
		}
		$back_str="?PHP_action=po_bom_view&PHP_id=".$wi_id['id']."&PHP_dept_code=".$op['ap']['dept']."&PHP_cust=".$op['ap']['cust']."&PHP_num=&PHP_sr_startno=";
	}else{
		$back_str="?PHP_action=po_search&PHP_dept_code=&PHP_SCH_num=&PHP_SCH_fty=&PHP_SCH_cust=&PHP_SCH_supl=&PHP_sr_startno=";
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
	if ($op['ap']['revise'] == 0 && $op['ap']['po_apv_user']) $op['ap']['revise'] =$op['ap']['revise']+1;
	
	if ($PHP_rev == 1)
	{
		page_display($op, 5, 1, $TPL_PO_SHOW_REV);
	}else{
		page_display($op, 5, 1, $TPL_PO_SHOW);			    	    
	}
		break;
		

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_apply_logs_add":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "do_apply_logs_add":
//	check_authority(5,1,"view");	
	$po_num=str_replace("A", "O",$PHP_aply_num);
	if(!$PHP_des)
	{
		$message="Please Input Log Description On :".$po_num;	
		$redir_str = "po.php?PHP_action=po_view&PHP_aply_num=".$PHP_aply_num."&PHP_message=".$message."&PHP_back=".$PHP_back_str;
		redirect_page($redir_str);
	}
	if(!$PHP_item)
	{
		$message="Please Input Log Item On :".$po_num;	
		$redir_str = "po.php?PHP_action=po_view&PHP_aply_num=".$PHP_aply_num."&PHP_message=".$message."&PHP_back=".$PHP_back_str;
		redirect_page($redir_str);		
	}
	if(strstr($PHP_des,'&#'))	$PHP_des = $ch_cov->check_cov($PHP_des);
	
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
	$redir_str = "po.php?PHP_action=po_view&PHP_aply_num=".$PHP_aply_num."&PHP_message=".$message."&PHP_back=".$PHP_back_str;
	redirect_page($redir_str);


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_edit":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "po_edit":
		check_authority(5,1,"edit");	

		//echo $PHP_aply_num;
	$op = $po->get($PHP_aply_num);
	if (isset($op['ap_det']))
	{
		$op['ap_det2'][0] = $op['ap_det'][0];
		$op['ap_det2'][0]['orders'][0] = $op['ap_det'][0]['ord_num'];
		$op['ap_det2'][0]['ids'] = $op['ap_det'][0]['id'];
		if ($op['ap_det'][0]['po_unit']){$units = $op['ap_det'][0]['po_unit'];}else{$units = $op['ap_det'][0]['unit'];}
		if ($op['ap_det'][0]['prc_unit']){$prc_units = $op['ap_det'][0]['prc_unit'];}else{$prc_units = $op['ap_det'][0]['unit'];}

		$op['ap_det2'][0]['Unit_select'] = $po->get_unit_group($units,$op['ap_det'][0]['id']);
		$op['ap_det2'][0]['prc_unit_select'] = $po->get_unit_group($prc_units,$op['ap_det'][0]['id'],'PHP_prc_unit');

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
					for ($z =0; $z < sizeof($op['ap_det2'][$j]['orders']); $z++)
					{
						if ($op['ap_det2'][$j]['orders'][$z] == $op['ap_det'][$i]['ord_num'])
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
				$op['ap_det2'][$k]['Unit_select'] = $po->get_unit_group($units,$op['ap_det'][$i]['id']);
				$op['ap_det2'][$k]['prc_unit_select'] = $po->get_unit_group($prc_units,$op['ap_det'][$i]['id'],'PHP_prc_unit');

				$k++;
			}
		}
	}
	if (isset($op['ap_spec']))
	{
		for ($i=0; $i<sizeof($op['ap_spec']); $i++)
		{
				if ($op['ap_spec'][$i]['po_unit']){$units = $op['ap_spec'][$i]['po_unit'];}else{$units = $op['ap_spec'][$i]['unit'];}
				if ($op['ap_spec'][$i]['prc_unit']){$prc_units = $op['ap_spec'][$i]['prc_unit'];}else{$prc_units = $op['ap_spec'][$i]['unit'];}

				$op['ap_spec'][$i]['Unit_select'] = $po->get_unit_group($units,"e".$op['ap_spec'][$i]['id']);
				$op['ap_spec'][$i]['prc_unit_select'] = $po->get_unit_group($prc_units,"e".$op['ap_spec'][$i]['id'],'PHP_prc_unit');

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


		$op['CURRENCY_select'] = $arry2->select($CURRENCY,$op['ap']['currency'],'PHP_CURRENCY','select','');
		$op['FACTORY_select'] = $arry2->select($SHIP,$op['ap']['arv_area'],'PHP_FACTORY','select',"ship_show(this)");
		for ($i=0; $i< 4; $i++)
		{
			if ($op['ap']['dm_way'] == $dm_way[1][$i]) $op['ap']['dm_way']=$dm_way[0][$i];
		}

		$op['dm_way_select'] = $arry2->select($dm_way[0],$op['ap']['dm_way'],"PHP_dm_way","select","");
		$op['ap']['special'] = $PHP_new_special;
		$op['FOB_select'] = $arry2->select($TRADE_TERM,$op['ap']['fob'],'PHP_FOB','select','');

		$back_str="?PHP_action=po_search".$PHP_back_str;
		$op['back_str']=$back_str;
		$op['edit'] = 1;
//	if (isset($PHP_message)) $op['msg'][]=$PHP_message;

		page_display($op, 5, 1, $TPL_PO_ADD);			    	    
		break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_revise":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "po_revise":
		check_authority(5,1,"edit");	

	$f1=$apply->update_2fields('po_apv_user', 'po_apv_date','','0000-00-00', $PHP_id);
	$f1=$apply->update_2fields('po_cfm_user', 'po_cfm_date','','0000-00-00', $PHP_id);
	$f1=$apply->update_2fields('po_sub_date', 'po_sub_date','','0000-00-00', $PHP_id);
	$f1=$apply->update_fields('status',6, $PHP_aply_num);
//	$f1=$apply->update_fields('po_rev',($PHP_rev_time+1), $PHP_aply_num);		

	$op = $po->get($PHP_aply_num);
	if (isset($op['ap_det']))
	{
		$op['ap_det2'][0] = $op['ap_det'][0];
		$op['ap_det2'][0]['orders'][0] = $op['ap_det'][0]['ord_num'];
		$op['ap_det2'][0]['ids'] = $op['ap_det'][0]['id'];
		if ($op['ap_det'][0]['po_unit']){$units = $op['ap_det'][0]['po_unit'];}else{$units = $op['ap_det'][0]['unit'];}
		if ($op['ap_det'][0]['prc_unit']){$prc_units = $op['ap_det'][0]['prc_unit'];}else{$prc_units = $op['ap_det'][0]['unit'];}

		$op['ap_det2'][0]['Unit_select'] = $po->get_unit_group($units,$op['ap_det'][0]['id']);
		$op['ap_det2'][0]['prc_unit_select'] = $po->get_unit_group($prc_units,$op['ap_det'][0]['id'],'PHP_prc_unit');


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
					for ($z =0; $z < sizeof($op['ap_det2'][$j]['orders']); $z++)
					{
						if ($op['ap_det2'][$j]['orders'][$z] == $op['ap_det'][$i]['ord_num'])
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
				$op['ap_det2'][$k]['Unit_select'] = $po->get_unit_group($units,$op['ap_det'][$i]['id']);
				$op['ap_det2'][$k]['prc_unit_select'] = $po->get_unit_group($prc_units,$op['ap_det'][$i]['id'],'PHP_prc_unit');

				$k++;
			}
		}
	}
	if (isset($op['ap_spec']))
	{
		for ($i=0; $i<sizeof($op['ap_spec']); $i++)
		{
				if ($op['ap_spec'][$i]['po_unit']){$units = $op['ap_spec'][$i]['po_unit'];}else{$units = $op['ap_spec'][$i]['unit'];}
				if ($op['ap_spec'][$i]['prc_unit']){$prc_units = $op['ap_spec'][$i]['prc_unit'];}else{$prc_units = $op['ap_spec'][$i]['unit'];}

				$op['ap_spec'][$i]['Unit_select'] = $po->get_unit_group($units,"e".$op['ap_spec'][$i]['id']);
				$op['ap_spec'][$i]['prc_unit_select'] = $po->get_unit_group($prc_units,"e".$op['ap_spec'][$i]['id'],'PHP_prc_unit');

		}
	}
	$tmp_num = substr($PHP_aply_num,2);
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
		$op['CURRENCY_select'] = $arry2->select($CURRENCY,$op['ap']['currency'],'PHP_CURRENCY','select','');
		$op['FACTORY_select'] = $arry2->select($SHIP,$op['ap']['arv_area'],'PHP_FACTORY','select',"ship_show(this)");
		for ($i=0; $i< 4; $i++)
		{
			if ($op['ap']['dm_way'] == $dm_way[1][$i]) $op['ap']['dm_way']=$dm_way[0][$i];
		}

		$op['dm_way_select'] = $arry2->select($dm_way[0],$op['ap']['dm_way'],"PHP_dm_way","select","");
		$op['ap']['special'] = $PHP_new_special;
		$op['FOB_select'] = $arry2->select($TRADE_TERM,$op['ap']['fob'],'PHP_FOB','select','');

//		$back_str="?PHP_action=po_search".$PHP_back_str;
//		$op['back_str']=$back_str;
		$op['revise'] = 1;
//	if (isset($PHP_message)) $op['msg'][]=$PHP_message;


		page_display($op, 5, 1, $TPL_PO_ADD);			    	    
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_search":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "po_search":
	check_authority(5,1,"view");	

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
	$op['msg']= $po->msg->get(2);
	page_display($op, 5, 1, $TPL_PO_LIST);
	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_submit":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "po_submit":
	check_authority(5,1,"edit");	


	if(isset($PHP_CURRENCY))
	{
		if(strstr($PHP_des,'&#'))	$PHP_des = $ch_cov->check_cov($PHP_des);

		$apply->update_2fields('currency','arv_area',$PHP_CURRENCY,$PHP_FACTORY,$PHP_id);
		
		
		$apply->update_fields_id('dm_way',$PHP_dmway,$PHP_id);	//Update 幣別

		$parm = array( 'field_name'		=>	'dm_way',
									 'field_value'	=>	$PHP_dmway,
									 'id'						=>	$PHP_sup_id
									 );
		$supl->update_field($parm);
	
		$parm_log = array(	'ap_num'	=>	$PHP_aply_num,
												'item'		=>	'special',
												'des'			=>	$PHP_des,
												'user'		=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
												'k_date'	=>	$TODAY,
								);
		if ($PHP_des <> $PHP_old_des && $PHP_des <> 'no_des')
		{
			$f1=$apply->add_log($parm_log);		
		}

	  if (!$PHP_FACTORY)	//若無運送地(ID)時,記錄運送地詳細資料
	  {
	  	$apply->update_fields_id('ship_addr',$PHP_saddr,$PHP_id);		//運送地址
			$apply->update_fields_id('ship_name',$PHP_sname,$PHP_id);		//運送地公司名
			$apply->update_fields_id('ship_fax',$PHP_sfax,$PHP_id);			//運送地傳真
			$apply->update_fields_id('ship_tel',$PHP_stel,$PHP_id);			//運送地電話
			$apply->update_fields_id('ship_attn',$PHP_sattn,$PHP_id);	  //運送地聯絡人
	  }  
	}



	$tmp_num = substr($PHP_aply_num,2);
	$po_num = "PO".$tmp_num;
	$f1=$apply->update_fields('po_num',$po_num, $PHP_aply_num);
//	$f1=$apply->update_fields('status','6', $PHP_aply_num);
	$PHP_po_num = $po_num;

	$f1=$apply->update_2fields('status', 'po_sub_date', '8', $TODAY, $PHP_id);
	$f1=$apply->update_2fields('fob', 'fob_area', $PHP_fob, $PHP_fob_area, $PHP_id); // Price term
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

	$redir_str = "po.php?PHP_action=po_view&PHP_aply_num=".$PHP_aply_num."&PHP_message=".$message."&PHP_add_back=".$PHP_add_back;
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
	$op['msg']= $po->msg->get(2);

//080725message增加		
	$note=$notify->search($GLOBALS['SCACHE']['ADMIN']['login_id'], "`read`=0");
	$op['max_notify'] = $note['max_no'];


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
	if ($op['ap']['revise'] == 0 && $op['ap']['po_apv_user']) $op['ap']['revise'] =$op['ap']['revise']+1;

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
		$message="Successfully CFM PO ";
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
	
	if(strstr($PHP_detail,'&#'))	$PHP_detail = $ch_cov->check_cov($PHP_detail);
	
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
		$message="Successfully reject PO :".$op['ap']['po_num'];
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

//080725message增加		
	$note=$notify->search($GLOBALS['SCACHE']['ADMIN']['login_id'], "`read`=0");
	$op['max_notify'] = $note['max_no'];

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
	if ($op['ap']['revise'] == 0 && $op['ap']['po_apv_user']) $op['ap']['revise'] =$op['ap']['revise']+1;

		page_display($op, 5, 6, $TPL_PO_SHOW_APV);			    	    
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_po_apv":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "do_po_apv":
	check_authority(5,6,"add");	
	$f1=$apply->update_2fields('status', 'po_apv_date', '12', $TODAY, $PHP_id);
	$f1=$apply->update_fields('po_apv_user',$GLOBALS['SCACHE']['ADMIN']['login_id'], $PHP_aply_num);
	
	$op = $apply->get($PHP_aply_num);	
	if ($op['ap']['special'] < 2)
	{
		for ($i=0; $i<sizeof($op['ap_det']); $i++)
		{
			if ($op['ap_det'][$i]['mat_cat'] == 'l'){
				$eta_date = $apply->get_fab_eta($op['ap_det'][$i]['ord_num']);
			}else{
				$eta_date = $apply->get_acc_eta($op['ap_det'][$i]['ord_num'],$op['ap_det'][$i]['bom_id']);
			}
		}
	}else{
		for ($i=0; $i<sizeof($op['ap_spec']); $i++)
		{
			if ($op['ap_spec'][$i]['mat_cat'] == 'l'){
				$eta_date = $apply->get_fab_eta($op['ap_spec'][$i]['ord_num']);
			}else{
				$eta_date = $apply->get_acc_eta($op['ap_spec'][$i]['ord_num'],'','acc_etd');
			}
		}	
	}	
		
	
	if ($f1)
	{
		$message="Successfully APV PO :".$op['ap']['po_num'];
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
	
	if(strstr($PHP_detail,'&#'))	$PHP_detail = $ch_cov->check_cov($PHP_detail);
	
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
		$message="Successfully reject PO :".$op['ap']['po_num'];
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

		check_authority(5,1,"view");
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
	if($op['wi']['bcfm_date'] == '0000-00-00 00:00:00'){    //取出該筆 製造令記錄
					$op['msg'][] = 'NOTIC : <b>WI & BOM</b> not built yet or unavailable!';
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}
		//  smpl 樣本檔
		if(!$op['order'] = $order->get($op['wi']['smpl_id'])){
					$op['msg']= $order->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}
		$size_data=$size_des->get($op['order']['size']);		
		$op['order']['size']=$size_data['size_scale'];
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

		//  取出  BOM  預先採購記錄 --------------------------------------------------------------
		$op['pp_mat_NONE']= '';
		$op['pp_mat'] = $po->get_unadd_pp($op['wi']['wi_num']);  //取出該筆 bom 內ALL加購記錄
		$num_ext_mat = count($op['pp_mat']);
		if (!$num_ext_mat){	$op['pp_mat_NONE'] = "1";}

		 if (isset($PHP_po_num))
		 {
		 	for ($i =0; $i< sizeof($op['ext_mat']); $i++)
		 	{
		 			if ($op['ext_mat'][$i]['po_num'] == $PHP_po_num) $op['ext_mat'][$i]['ln_mk'] = 1;
		 	}
		 }		


		//  取出  BOM  Disable計錄中己採購主料者 --------------------------------------------------------------
		$op['dis_lots_NONE']= '';
		$op['dis_lots'] = $bom->get_dis_lots($op['wi']['id']);  //取出該筆 bom 內記錄
		$num_lots = count($op['dis_lots']);
		if (!$num_lots){	$op['dis_lots_NONE'] = "1";}
		 if (isset($PHP_po_num))
		 {
		 	for ($i =0; $i< sizeof($op['dis_lots']); $i++)
		 	{
		 			if ($op['dis_lots'][$i]['po_num'] == $PHP_po_num) $op['dis_lots'][$i]['ln_mk'] = 1;
		 	}
		 }	


		//  取出  BOM  Disable計錄中己採購副料者 --------------------------------------------------------------
		$op['dis_acc_NONE']= '';
		$op['dis_acc'] = $bom->get_dis_acc($op['wi']['id']);  //取出該筆 bom 內記錄
		$num_acc = count($op['dis_acc']);
		if (!$num_acc){	$op['dis_acc_NONE'] = "1";}
		 if (isset($PHP_po_num))
		 {
		 	for ($i =0; $i< sizeof($op['dis_acc']); $i++)
		 	{
		 			if ($op['dis_acc'][$i]['po_num'] == $PHP_po_num) $op['dis_acc'][$i]['ln_mk'] = 1;
		 	}
		 }
		 
		
		page_display($op, 5,1, $TPL_BOM_PO_VIEW);
		break;


//-------------------------------------------------------------------------


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_print_doc":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "po_print_doc":   //  .......
	check_authority(5,1,"view");	
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
					$op['ap_det2'][$j]['amount'] = $op['ap_det'][$i]['amount'] +$op['ap_det2'][$j]['amount'];

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

	if ($op['ap']['revise'] == 0 && $op['ap']['po_apv_user']) $op['ap']['revise'] =$op['ap']['revise']+1;

	if( $op['ap']['revise'] < 10)	 $op['ap']['revise'] = "00".$op['ap']['revise'];
	if( $op['ap']['revise'] > 10 && $op['ap']['revise'] < 100)	 $op['ap']['revise'] = "0".$op['ap']['revise'];
	
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
$pdf->cell(40, 5,"Ver      ".$op['ap']['revise'],0,0,'L');
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
		$bk_ll = $pdf->mater($op['ap_det2'][$i],$op['ap']['currency'],$op['ap']['fob'],$op['ap']['fob_area']);
		$x=$x+$bk_ll;
	}
}
if (isset($op['ap_det']))
{
	for ($i=0; $i< sizeof($op['ap_det']); $i++)
	{
		if (!strstr($mk_order,$op['ap_det'][$i]['ord_num']))
		{
			$mk_order = $mk_order.$op['ap_det'][$i]['ord_num'].",";		
		}
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
		$bk_ll = $pdf->mater($op['ap_spec'][$i],$op['ap']['currency'],$op['ap']['fob'],$op['ap']['fob_area']);	
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

	for ($i=0; $i< sizeof($op['ap_oth_cost']); $i++)
	{		
		if ($x > 30)
		{
			$pdf->AddPage();
			$pdf->mater_title();
			$pdf->ln();
			$x=0;		
		}			
		$bk_ll = $pdf->oth_cost($op['ap_oth_cost'][$i],$op['ap']['currency']);	
		$x++;
	}


$pri_tal = NUMBER_FORMAT($pri_tal,2);
$qty_tal = NUMBER_FORMAT($qty_tal,2);
$op['ap']['dear'] = NUMBER_FORMAT($op['ap']['dear'],2);
$op['ap']['po_total'] = NUMBER_FORMAT($op['ap']['po_total'],2);
$pdf->cell(130,5,'TOTAL',1,0,'R');
$pdf->cell(19,5,$qty_tal,1,0,'R');
$pdf->cell(46,5,$op['ap']['currency']."$ ".$op['ap']['po_total'],1,0,'R');
$pdf->ln();
$x++;
//支付總金額
$pdf->cell(130,5,'Deal Amount',1,0,'R');
$pdf->cell(19,5,'',1,0,'R');
$pdf->cell(46,5,$op['ap']['currency']."$ ".$op['ap']['dear'],1,0,'R');
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
$pdf->SetFont('BIG5','',10);
$pdf->cell(145, 5,$op['ap']['dm_way'],0,0,'L');
$pdf->SetFont('Arial','',10);
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
$pdf->SetY(250);
$pdf->SetFont('Arial','',10);
$pdf->cell(120, 5,'',0,0,'L');
$pdf->cell(70, 5,'Confirmed by',0,0,'L');
$pdf->ln();
$pdf->SetFont('BIG5','B',10);
$pdf->cell(90, 5,$op['ap']['supl_f_name'],0,0,'L');
$pdf->cell(30, 5,'',0,0,'L');
$pdf->cell(70, 5,'Carnival Inudstrial Corporation',0,0,'L');
$pdf->ln();
$pdf->ln();
$pdf->ln();
$pdf->cell(90, 5,'','B',0,'L');
$pdf->cell(30, 5,'',0,0,'L');
$pdf->cell(70, 5,'','B',0,'L');
$name=$op['ap']['po_num'].'_po_doc.pdf';
$pdf->Output($name,'D');
break;	





#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_view":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "po_rcv_view":
		check_authority(5,1,"view");	
	$log_where = "AND item <> 'special'";	
	$op = $po->get($PHP_aply_num,$log_where);
	if (isset($op['ap_det']))
	{
		$op['ap_det2']=$po->grout_ap($op['ap_det']);
		for ($i=0; $i<sizeof($op['ap_det2']); $i++)
		{
			if($op['ap_det2'][$i]['mat_code'] == $PHP_mat_code) $op['ap_det2'][$i]['ln_mk'] ='1';
		}
	
	}else{
		for ($i=0; $i<sizeof($op['ap_spec']); $i++)
		{
			if($op['ap_spec'][$i]['mat_code'] == $PHP_mat_code) $op['ap_spec'][$i]['ln_mk'] ='1';
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

	
	if ($op['ap']['revise'] == 0 && $op['ap']['po_apv_user']) $op['ap']['revise'] =$op['ap']['revise']+1;
	
		$layout->assign($op);
		$layout->display($TPL_PO_SHOW);		    	    

		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apply_search_pp":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "apply_search_pp":   //  先找出製造令.......

		check_authority(5,1,"view");

			if (!$op = $order->search_apvd_ord()) {
				$op['msg']= $wi->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

		$op['msg']= $bom->msg->get(2);				
		$back_str="&PHP_dept_code=".$PHP_dept_code."&PHP_cust=".$PHP_cust."&PHP_num=".$PHP_num;
		if ($GLOBALS['SCACHE']['ADMIN']['dept'] == 'SA') 
		{
			$op['close_flag'] = 1;
		}
		
		$op['back_str']=$back_str;
		page_display($op, 5,1, $TPL_APLY_PP_LIST);
		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apply_pp_add":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "apply_pp_add":
		check_authority(5,1,"add");		
		
			if(!$ord = $order->get(0,$PHP_ord_num)){    //取出該筆 製造令記錄 ID
						$op['msg']= $wi->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
			}
			
			$parm_madd= array(  'cust'  			=>	$ord['cust'],
													'dept'				=>	$ord['dept'],
													'sup_code'		=>	'',
													'ap_user'			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
													'ap_date'			=>	$TODAY,
													'ap_special'	=>	2,
													);
		$op['ap'] = $parm_madd;
		
		$back_str="&PHP_dept_code=".$PHP_dept_code."&PHP_cust=".$PHP_cust."&PHP_num=".$PHP_num."&PHP_sr_startno=".$PHP_sr;
		$op['back_str'] = $back_str;
		$op['wi_num'] = $PHP_ord_num;
		$op['item'] = $PHP_item;
		$op['CURRENCY_select'] = $arry2->select($CURRENCY,'','PHP_CURRENCY','select','');
		$op['FACTORY_select'] = $arry2->select($SHIP,'','PHP_FACTORY','select',"ship_show(this)");
		$op['ACC_select'] = $arry2->select($ACC_PRICE_UNIT,'','PHP_unit','select','');
		$op['FAB_select'] = $arry2->select($LOTS_PRICE_UNIT,'','PHP_unit','select','');

		$op['dm_way_select'] = $arry2->select($dm_way[0],'',"PHP_dm_way","select","");

		$op['revise'] = $PHP_revise;
		page_display($op, 5, 1, $TPL_APLY_PP_ADD);			    	    
		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apply_pp_det_add":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "apply_pp_det_add":
		check_authority(5,1,"add");		
		
		if(strstr($PHP_use_for,'&#'))	$PHP_use_for = $ch_cov->check_cov($PHP_use_for);
		

		if ($PHP_ap_num =='')  //若沒有pa編號時,先建立請購單(母)
		{			
			if(!$ord = $order->get(0,$PHP_wi_num)){    //取出該筆 製造令記錄 ID
						$op['msg']= $wi->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
			}			
			$parm_madd= array(  'cust'  			=>	$ord['cust'],
													'dept'				=>	$ord['dept'],
													'sup_code'		=>	$PHP_vendor,
													'ap_user'			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
													'ap_date'			=>	$TODAY,
													'ap_special'	=>	2,
													);
			$head="PA".date('y')."-";	//A+日期+部門=請購單開頭
			$parm_madd['ap_num']=$apply->get_no($head,'ap_num','ap');	//取得請購的最後編號
			$new_id = $apply->add($parm_madd);	//新增AP資料庫
			$message = "Successfully appended Purchase : ".$parm_madd['ap_num'];
//			$op['msg'][] = $message;
			$log->log_add(0,"51A",$message);
			$PHP_ap_num = $parm_madd['ap_num'];

			$tmp_num = substr($parm_madd['ap_num'],2);
			$po_num = "PO".$tmp_num;
			$f1=$apply->update_fields('po_num',$po_num, $PHP_ap_num);


		}

		
		if ($PHP_item == 'lots')
		{
			$parm_mat = array (	'field_name'	=>	'vendor1',
													'field_value'	=>	$PHP_vendor,
													'code'				=>	$PHP_mat_code
												);
			$lots->update_field_code($parm_mat);

			$parm_mat = array (	'field_name'	=>	'price1',
													'field_value'	=>	$PHP_price,
													'code'				=>	$PHP_mat_code
												);
			$lots->update_field_code($parm_mat);

	  	$mat_cat='l';
		}else{
			$mat_cat='a';		
			$parm_mat = array (	'field_name'	=>	'vendor1',
													'field_value'	=>	$PHP_vendor,
													'code'				=>	$PHP_mat_code
												);
			$acc->update_field_code($parm_mat);

			$parm_mat = array (	'field_name'	=>	'price1',
													'field_value'	=>	$PHP_price,
													'code'				=>	$PHP_mat_code
												);
			$acc->update_field_code($parm_mat);
		
		}
		
		
		$parm_dadd = array( 'ap_num'		=>	$PHP_ap_num,
												'mat_code'	=>	$PHP_mat_code,
												'ord_num'		=>	$PHP_wi_num,
												'color'			=>	$PHP_color,
												'eta'				=>	$PHP_eta,
												'qty'				=>	$PHP_qty,
												'unit'			=>	$PHP_unit,
												'mat_cat'		=>	$mat_cat,
												'use_for'		=>	$PHP_use_for,
								);
		$f1 = $apply->add_pp($parm_dadd);	//新增AP資料庫

		$op = $apply->get($PHP_ap_num);
		$message = "Successfully appended Purchase detial for Order : ".$PHP_wi_num;
		$op['msg'][] = $message;
		$log->log_add(0,"54A",$message);
		
		$op['wi_num'] = $PHP_wi_num;
		$op['item'] = $PHP_item;
		$op['CURRENCY_select'] = $arry2->select($CURRENCY,$op['ap']['currency'],'PHP_CURRENCY','select','');
		$op['FACTORY_select'] = $arry2->select($SHIP,$op['ap']['arv_area'],'PHP_FACTORY','select',"ship_show(this)");
		$op['ACC_select'] = $arry2->select($ACC_PRICE_UNIT,'','PHP_unit','select','');
		$op['FAB_select'] = $arry2->select($LOTS_PRICE_UNIT,'','PHP_unit','select','');
		for ($i=0; $i< 4; $i++)
		{
			if ($op['ap']['dm_way'] == $dm_way[1][$i]) $op['ap']['dm_way']=$dm_way[0][$i];
		}

		$op['dm_way_select'] = $arry2->select($dm_way[0],$op['ap']['dm_way'],"PHP_dm_way","select","");

		$op['revise'] = $PHP_revise;

			if (isset($PHP_SCH_num))
			{
				$back_str="&PHP_dept_code=".$PHP_dept_code."&PHP_SCH_num=".$PHP_SCH_num."&PHP_SCH_fty=".$PHP_SCH_fty."&PHP_SCH_cust=".$PHP_SCH_cust."&PHP_SCH_supl=".$PHP_SCH_supl."&PHP_sr_startno=".$PHP_sr_startno;
				$op['back_str']=$back_str;
			}

	if ($op['ap']['arv_area'])
	{
		$op['ap']['ship_name'] = $SHIP_TO[$op['ap']['arv_area']]['Name'];
		$op['ap']['ship_addr'] = $SHIP_TO[$op['ap']['arv_area']]['Addr'];
		$op['ap']['ship_tel']  = $SHIP_TO[$op['ap']['arv_area']]['TEL'];
		$op['ap']['ship_fax']  = $SHIP_TO[$op['ap']['arv_area']]['FAX'];
		$op['ap']['ship_attn'] = $SHIP_TO[$op['ap']['arv_area']]['Attn'];
	}

		page_display($op, 5, 1, $TPL_APLY_PP_ADD);			    	    
		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_apply_pp_add":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "do_apply_pp_add":
		check_authority(5,1,"add");	
/*
if(strstr($PHP_des,'&#'))	$PHP_des = $ch_cov->check_cov($PHP_des);

		$apply->update_2fields('currency','arv_area',$PHP_CURRENCY,$PHP_FACTORY,$PHP_id);
		$apply->update_fields_id('dm_way',$PHP_dmway,$PHP_id);	//Update 幣別
	
		$parm = array( 'field_name'		=>	'dm_way',
									 'field_value'	=>	$PHP_dmway,
									 'id'						=>	$PHP_sup_id
									 );
		$supl->update_field($parm);
	
		$parm_log = array(	'ap_num'	=>	$PHP_ap_num,
												'item'		=>	'special',
												'des'			=>	$PHP_des,
												'user'		=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
												'k_date'	=>	$TODAY,
								);
		if ($PHP_des <> $PHP_old_des)
		{
			$f1=$apply->add_log($parm_log);		
		}

	  if (!$PHP_FACTORY)	//若無運送地(ID)時,記錄運送地詳細資料
	  {
	  	$apply->update_fields_id('ship_addr',$PHP_saddr,$PHP_id);		//運送地址
			$apply->update_fields_id('ship_name',$PHP_sname,$PHP_id);		//運送地公司名
			$apply->update_fields_id('ship_fax',$PHP_sfax,$PHP_id);			//運送地傳真
			$apply->update_fields_id('ship_tel',$PHP_stel,$PHP_id);			//運送地電話
			$apply->update_fields_id('ship_attn',$PHP_sattn,$PHP_id);	  //運送地聯絡人
	  }  
		$log_where = "AND item <> 'special'";	
		$op = $apply->get($PHP_ap_num,$log_where);
		
		
		if (isset($op['ap_det']))
		{
			$op['ap_det2'] = $apply->group_ap($op['ap_det']);	//組同請購明細
		}

		$msg = "Successfully update P/A : ".$PHP_ap_num." main record";
		$op['msg'][]=$msg;
		$log->log_add(0,"51A",$msg);
//Ship TO
	if ($op['ap']['arv_area'])
	{
		$op['ap']['ship_name'] = $SHIP_TO[$op['ap']['arv_area']]['Name'];
		$op['ap']['ship_addr'] = $SHIP_TO[$op['ap']['arv_area']]['Addr'];
		$op['ap']['ship_tel']  = $SHIP_TO[$op['ap']['arv_area']]['TEL'];
		$op['ap']['ship_fax']  = $SHIP_TO[$op['ap']['arv_area']]['FAX'];
		$op['ap']['ship_attn'] = $SHIP_TO[$op['ap']['arv_area']]['Attn'];
	}

//Payment
	for ($i=0; $i< 4; $i++)
	{
		if ($op['ap']['dm_way'] == $dm_way[1][$i]) $op['ap']['dm_way']=$dm_way[0][$i];
	}
*/
			if (isset($PHP_SCH_num))
			{
				$back_str="?PHP_action=apply_search&PHP_dept_code=".$PHP_dept_code."&PHP_SCH_num=".$PHP_SCH_num."&PHP_SCH_fty=".$PHP_SCH_fty."&PHP_SCH_cust=".$PHP_SCH_cust."&PHP_SCH_supl=".$PHP_SCH_supl."&PHP_sr_startno=".$PHP_sr_startno;				
			}else{
				if (isset($op['ap_det2'][0]['ord_num']))
				{
					$wi_id = $wi->get_field($op['ap_det2'][0]['ord_num'],'id');
					$back = "apply_wi_view";
				}else{
					$wi_id = $wi->get_field($op['ap_spec'][0]['ord_num'],'id');
					$back = "apply_ext_bom_view";
				}
				$back_str="?PHP_action=".$back."&PHP_sr=&PHP_dept_code=".$op['ap']['dept']."&PHP_cust=".$op['ap']['cust']."&PHP_num=&PHP_full=&PHP_id=".$wi_id['id'];
				$op['add_back'] = $back;
			}
			$op['back_str']=$back_str;
		if ($op['ap']['revise'] == 0 && $op['ap']['apv_user']) $op['ap']['revise'] =$op['ap']['revise']+1;

	$redirect_str = 'po.php?PHP_action=po_add&PHP_aply_num='.$PHP_ap_num;
	redirect_page($redirect_str);

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_apply_del":		job 51
# 刪除未送出採購單
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "do_apply_del":
		check_authority(5,1,"add");	
		$f1 = $apply->del_pa($PHP_ap_num);
		$redirect_str = 'index2.php?PHP_action=apply';
		if(isset($PHP_back))$redirect_str='po.php?PHP_action=po_search&PHP_dept_code='.$PHP_dept_code."&PHP_SCH_num=".$PHP_SCH_num."&PHP_SCH_fty=".$PHP_SCH_fyt."&PHP_SCH_cust=".$PHP_SCH_cust."&PHP_SCH_supl=".$PHP_SCH_supl."&PHP_sr_startno=".$PHP_sr_starton;
		redirect_page($redirect_str);
		


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_po_apv_del":			job 51
# 刪除己核可採購單
# 需填寫異常報告
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "do_po_apv_del":
		check_authority(5,1,"add");	
		$rec = $po->get($PHP_ap_num);
		$ord_rec = array();
		if(isset($rec['ap_det']))
		{
			for($i=0; $i<sizeof($rec['ap_det']); $i++)
			{
				$mk = 0;
				for($j=0; $j<sizeof($ord_rec); $j++)
				{
					if($ord_rec[$j] == $rec['ap_det'][$i]['ord_num'])
					{
						$mk = 1;
						break;
					}
				}
				if($mk == 0) $ord_rec[] = $rec['ap_det'][$i]['ord_num'];

			}
		}

		if(isset($rec['ap_spec']))
		{
			for($i=0; $i<sizeof($rec['ap_spec']); $i++)
			{
				$mk = 0;
				for($j=0; $j<sizeof($ord_rec); $j++)
				{
					if($ord_rec[$j] == $rec['ap_spec'][$i]['ord_num']) $mk = 1;
				}
				if($mk == 0) $ord_rec[] = $rec['ap_spec'][$i]['ord_num'];
			}
		}
		$ord_num = '';
		for($j=0; $j<sizeof($ord_rec); $j++) $ord_num .= $ord_rec[$j].",";
		$ord_num = substr($ord_num,0,-1);
		if($rec['ap']['dear'] == 0) $rec['ap']['dear'] = $rec['ap']['po_total'];
		$rec['ap']['dear'] = number_format($rec['ap']['dear'],2,'.',',');
//		$f1 = $apply->del_pa($PHP_ap_num);

			$op['ap_num'] = $PHP_ap_num;
			
			$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];   // 判定進入身份的指標
			// 取出選項資料 及傳入之參數
			$op['msg'][]= "Please write Exceptional for PO : [".$rec['ap']['po_num']."]";
			$op['cust_id'] = $rec['ap']['cust'];
			$op['dept_id']	 = $user_dept;
			$op['date'] = $TODAY;
			$op['exc']['ord_num'] = $ord_num;
			$op['rec']['dear'] = $rec['ap']['currency']."$".$rec['ap']['dear'];
			
			$op['rec']['reason'] = "採購單 [ ".$rec['ap']['po_num']." ] 刪除,影響採購總金額 : ".$rec['ap']['currency']."$".$rec['ap']['dear']
														 .chr(13).chr(10)."影響訂單 : ".$ord_num;
			// 2005/07/30 加入 由 menu進入新增後的 back page 只向 全部列表
			page_display($op, 10, 5,$TPL_EXC_ADD);		
			break;		
	



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_ann":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "po_ann":
		check_authority(5,2,"view");
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
		$op['factory_select'] = $arry2->select($SHIP,'','PHP_SCH_fty','select','');
		$op['cust_sch_select'] =  $arry2->select($cust_value,'','PHP_SCH_cust','select','',$cust_def_vue); 


		$op['manager_flag'] = $manager;
		$op['manager_v_flag'] = $manager_v;
		$op['dept_id'] = $dept_id;
		$op['dept_ary'] = $dept_ary;

		$op['msg'] = $order->msg->get(2);

	$note=$notify->search($GLOBALS['SCACHE']['ADMIN']['login_id'], "`read`=0");
	$op['max_notify'] = $note['max_no'];

		  	
		page_display($op, 5, 2, $TPL_PO_ANN);			    	    
		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_search":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "po_ann_search":
	check_authority(5,2,"view");	

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
	$op['msg']= $po->msg->get(2);
	if(isset($PHP_msg))$op['msg'][] = $PHP_msg;
	page_display($op, 5, 2, $TPL_PO_ANN_LIST);
	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_po_ann_add":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "do_po_ann_add":
	check_authority(5,2,"view");	

	if($PHP_org_ann)
	{
		$ann_num = $PHP_org_ann.",".$PHP_ann_no;
	}else{
		$ann_num = $PHP_ann_no;
	}

	$f1=$apply->update_fields_id('ann_num',$ann_num, $PHP_id, 'ap');
	$msg = "Successfully add WA-ANN Number on PO".$PHP_po_num;
	$redirect_str = 'po.php?PHP_action=po_ann_search'.$PHP_back_str."&PHP_msg=".$msg;
	redirect_page($redirect_str);
	
	break;
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_po_ann_add":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "po_ann_del":
	check_authority(5,2,"view");	

	$f1=$apply->update_fields_id('ann_num','', $PHP_id, 'ap');
	$msg = "Successfully Delete WA-ANN Number on PO".$PHP_po_num;

	$back_str="&PHP_dept_code=".$PHP_dept_code."&PHP_SCH_num=".$PHP_SCH_num."&PHP_SCH_fty=".$PHP_SCH_fty."&PHP_SCH_cust=".$PHP_SCH_cust."&PHP_SCH_supl=".$PHP_SCH_supl."&PHP_sr_startno=".$PHP_sr_startno;

	$redirect_str = 'po.php?PHP_action=po_ann_search'.$back_str."&PHP_msg=".$msg;
	redirect_page($redirect_str);
	
	break;	




#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "pa_print":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "pa_print":   //  .......
	check_authority(5,1,"view");	
	$where_str="AND( item like '%PA-Rmk%' OR item like '%special%')";		
	$op = $po->get($PHP_aply_num,$where_str);

	//取得user name
/*	
	$po_user=$user->get(0,$op['ap']['apv_user']);
	if ($po_user['name'])$op['ap']['apv_user'] = $po_user['name'];
	$po_user=$user->get(0,$op['ap']['cfm_user']);
	if ($po_user['name'])$op['ap']['cfm_user'] = $po_user['name'];
	$po_user=$user->get(0,$op['ap']['submit_user']);
	if ($po_user['name'])$op['ap']['submit_user'] = $po_user['name'];
*/
	$mat_cat='';
	$where_str = " WHERE dept_code = '".$op['ap']['dept']."'";
//	echo $where_str;
	$dept_name=$dept->get_fields('dept_name',$where_str);
	if (isset($op['ap_det']))
	{
		$op['ap_det2'][0] = $op['ap_det'][0];
		$op['ap_det2'][0]['orders'] = $op['ap_det'][0]['ord_num'];
		if ($op['ap_det2'][0]['mat_cat'] == 'l')	$mat_cat="Fabric";
		if ($op['ap_det2'][0]['mat_cat'] == 'a')	$mat_cat="Accessory";		
		$k=1;
		for ($i=1; $i<sizeof($op['ap_det']); $i++)
		{
			$mk=0;
			for ($j=0; $j< sizeof($op['ap_det2']); $j++)
			{
				if ($op['ap_det2'][$j]['mat_code'] == $op['ap_det'][$i]['mat_code'] && $op['ap_det2'][$j]['color'] == $op['ap_det'][$i]['color'] && $op['ap_det2'][$j]['unit'] == $op['ap_det'][$i]['unit'] && $op['ap_det2'][$j]['eta'] == $op['ap_det'][$i]['eta'])
				{
					$op['ap_det2'][$j]['po_qty'] = $op['ap_det'][$i]['po_qty'] +$op['ap_det2'][$j]['po_qty'];
					$op['ap_det2'][$j]['amount'] = $op['ap_det'][$i]['amount'] +$op['ap_det2'][$j]['amount'];
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
		if ($op['ap_spec'][0]['mat_cat'] == 'l')	$mat_cat="Fabric";
		if ($op['ap_spec'][0]['mat_cat'] == 'a')	$mat_cat="Accessory";		
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
	$print_title="Regular Purchase Application";
}else{
	$print_title="Special Purchase Application";
}

$print_title2 = "VER.".($op['ap']['revise']+1)."   for   ".$mat_cat;
$creator = $op['ap']['ap_user'];
$mark = $op['ap']['ap_num'];
$pdf=new PDF_po('L','mm','A4');
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
$parm = array (	'po_num'		=>	$op['ap']['po_num'],
							'supplier'		=>	$op['ap']['s_name'],
							'dept'				=>	$dept_name[0],
							'ap_date'			=>	$op['ap']['po_apv_date'],
							'ap_user'			=>	$op['ap']['ap_user'],
							'currency'		=>	$op['ap']['currency'],
							'ann_num'		=>	$op['ap']['ann_num'],
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
//		$total = $total + ($op['ap_det2'][$i]['amount']);
		$op['ap_det2'][$i]['price1'] = NUMBER_FORMAT($op['ap_det2'][$i]['price1'],2);
		$op['ap_det2'][$i]['prics'] = NUMBER_FORMAT($op['ap_det2'][$i]['prics'],2);
		$op['ap_det2'][$i]['ap_qty'] = NUMBER_FORMAT($op['ap_det2'][$i]['po_qty'],2);
		$op['ap_det2'][$i]['amount'] = NUMBER_FORMAT($op['ap_det2'][$i]['amount'],2);
		
		$ords= explode('|',$op['ap_det2'][$i]['orders']);		
		
		$ord_cut = sizeof($ords);
		$x = $x+$ord_cut;
		if ($x > 20)
		{
			$pdf->AddPage();
			$pdf->hend_title($parm);
			$pdf->ln();$pdf->ln();$pdf->ln();
			$pdf->po_title();
			$pdf->ln();			
			$x=0;
		}
		
		if ($ord_cut > 1)
		{
			$pdf->pa_det_mix($op['ap_det2'][$i],$ords);
		}else{
			$pdf->pa_det($op['ap_det2'][$i]);
		}
		$pdf->ln();
		
	}
}

if (isset($op['ap_spec']))
{
	for ($i=0; $i< sizeof($op['ap_spec']); $i++)
	{
		$x++;
//		$total = $total + ($op['ap_spec'][$i]['price1']*$op['ap_spec'][$i]['ap_qty']);
		$op['ap_spec'][$i]['price1'] = NUMBER_FORMAT($op['ap_spec'][$i]['price1'],2);
		$op['ap_spec'][$i]['prics'] = NUMBER_FORMAT($op['ap_spec'][$i]['prics'],2);
		$op['ap_spec'][$i]['ap_qty'] = NUMBER_FORMAT($op['ap_spec'][$i]['po_qty'],2);
		$op['ap_spec'][$i]['amount'] = NUMBER_FORMAT($op['ap_spec'][$i]['amount'],2);
		if ($x > 20)
		{
			$pdf->AddPage();
			$pdf->hend_title($parm);
			$pdf->ln();$pdf->ln();$pdf->ln();
			$pdf->po_title();
			$pdf->ln();			
			$x=0;
		}
		
		$pdf->pa_det($op['ap_spec'][$i]);
		$pdf->ln();		
	}
}
$x=$x+2;
		if ($x > 20)
		{
			$pdf->AddPage();
			$pdf->hend_title($parm);
			$pdf->ln();$pdf->ln();$pdf->ln();				
			$x=0;
		}

	for ($i=0; $i< sizeof($op['ap_oth_cost']); $i++)
	{
		$x++;
		$op['ap_oth_cost'][$i]['cost'] = NUMBER_FORMAT($op['ap_oth_cost'][$i]['cost'],2);
		if ($x > 20)
		{
			$pdf->AddPage();
			$pdf->hend_title($parm);
			$pdf->ln();$pdf->ln();$pdf->ln();
			$pdf->po_title();
			$pdf->ln();			
			$x=0;
		}		
		$pdf->oth_cost($op['ap_oth_cost'][$i]);
		$pdf->ln();		
	}
		
		
//$total = NUMBER_FORMAT($total,2);
$op['ap']['dear'] = NUMBER_FORMAT($op['ap']['dear'],2);
$op['ap']['po_total'] = NUMBER_FORMAT($op['ap']['po_total'],2);
$pdf->Cell(18,5,'Re-Mark','TRL',0,'C');
$pdf->Cell(242,5,'Total : '.$op['ap']['currency'].' $'.$op['ap']['po_total'],'T',0,'R');
$pdf->Cell(20,5,'','TR',0,'C');
$pdf->ln();
$pdf->Cell(18,5,'','RL',0,'C');
$pdf->Cell(242,5,'Deal Amount : '.$op['ap']['currency'].' $'.$op['ap']['dear'],0,0,'R');
$pdf->Cell(20,5,'','R',0,'C');
$pdf->ln();
$pdf->Cell(18,5,'','RL',0,'C');
$pdf->SetFont('BIG5','',8);
$pdf->Cell(242,5,'Delivery : '.$op['ap']['usance'].' days   Payment : '.$op['ap']['dm_way'],0,0,'L');
$pdf->Cell(20,5,'','R',0,'C');
$pdf->ln();

for ($i=0; $i< sizeof($op['apply_log']); $i++)
{
	$x++;
		if ($x > 18)
		{
			$pdf->Cell(18,5,'','RLB',0,'C');
			$pdf->Cell(242,5,'','B',0,'L');
			$pdf->Cell(20,5,'','BR',0,'C');
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
	$pdf->SetFont('Big5','',10);
	$pdf->Cell(242,5,$op['apply_log'][$i]['des'],0,0,'L');
	
	$pdf->Cell(20,5,'','R',0,'C');
	$pdf->ln();
}
	$pdf->Cell(18,5,'','RLB',0,'C');
	$pdf->Cell(242,5,'','B',0,'L');
	$pdf->Cell(20,5,'','BR',0,'C');

	$pdf->ln();
	$pdf->SetFont('Big5','',10);
	$pdf->Cell(50,5,' ','0',0,'C');
	$pdf->Cell(50,5,'',0,0,'L');
	$pdf->Cell(50,5,'',0,0,'L');//PO Submit

	$pdf->Cell(50,5,'APPROVAL : '.$op['ap']['po_apv_user'],'0',0,'C');	//PO Approval
	$pdf->Cell(50,5,'CONFIRM :'.$op['ap']['po_cfm_user'],0,0,'L');//PO Confirm
	
	$pdf->Cell(50,5,'PO :'.$op['ap']['po_user'],0,0,'L');//PA submit
	

$name=$op['ap']['ap_num'].'_pa.pdf';
//echo "<meta http-equiv='refresh' content='50000; url=close2.html'>";
$pdf->Output($name,'D');


break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "ord_bom_match":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

    case "ord_bom_match":
		check_authority(5,1,"add");		
		
			if(!$ord = $wi->get($PHP_wi_id)){    //取出該筆 製造令記錄 ID
						$op['msg']= $wi->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
			}
			$parm = array('ap_num'		=>	$PHP_ap_num,
										'ap_det_id'	=>	$PHP_ap_det_id,
										'bom_id'		=>	$PHP_bom_id
										);
										
			if ($PHP_item == 'lots')
			{
				$chg_rec=$bom->get_old_lots($parm);				
			}else{
				$chg_rec=$bom->get_old_acc($parm);
			}
//		echo "diff".$chg_rec['dif_qty']."<br>";


		if($chg_rec['dif_qty'] >= 0)
		{
			$message = "Success chage PO  : [".$chg_rec['po_num']."] to Material : [".$chg_rec['mat_code']."]";

			$redirect_str ="index2.php?PHP_action=apply_wi_view&other=other&PHP_sr".$PHP_sr_startno."&PHP_dept_code=".$PHP_dept_code."&PHP_cust=".$PHP_cust."&PHP_num=".$PHP_num."&PHP_id=".$PHP_wi_id."&PHP_full=".$PHP_full."&PHP_msg=".$message;	
			redirect_page($redirect_str);			
		}
		if($chg_rec['dif_qty'] < 0)
		{

			$chg_rec['po_qty'] = change_unit_qty($chg_rec['po_unit'],$chg_rec['unit'],$chg_rec['po_qty']);
			$chg_rec['po_qty'] = number_format($chg_rec['po_qty'],2,'.','');
			if($chg_rec['prc_unit']){$org_unit = $chg_rec['prc_unit'];}else{$org_unit = $chg_rec['po_unit'];}

			$chg_rec['prics'] = change_unit_price($org_unit,$chg_rec['unit'],$chg_rec['prics']);
			$chg_rec['prics'] = number_format($chg_rec['prics'],2,'.','');
			$chg_rec['po_amount'] = $chg_rec['amount'] ;
			$chg_rec['bom_amount'] = $chg_rec['new_qty'] * $chg_rec['prics'];
			$more_qty = $chg_rec['po_qty'] - $chg_rec['new_qty'];  //計算差異數量
			
			$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];   // 判定進入身份的指標
			// 取出選項資料 及傳入之參數
			$op['msg'][]= "Please write Exceptional for Order : [".$ord['wi_num']."]";
			$op['cust_id'] = $ord['cust'];
			$op['dept_id']	 = $user_dept;
			$op['date'] = $TODAY;
			$op['exc']['ord_num'] = $ord['wi_num'];
			$op['rec'] = $chg_rec;
			$op['rec']['reason'] = $chg_rec['mat_code']."因BOM修改後造成採購單 [".$chg_rec['po_num']."] 過量採購".chr(13).chr(10)."超額量為 : ".$more_qty.
														 " 單價 : ".$chg_rec['prics']." 差異總金額 : ".number_format(($chg_rec['prics'] * $more_qty),2,'.',',');
			// 2005/07/30 加入 由 menu進入新增後的 back page 只向 全部列表
			page_display($op, 10, 5,$TPL_EXC_ADD);		
			break;		
		}		






}   // end case ---------

?>
