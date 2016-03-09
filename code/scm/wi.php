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
#			index.php  主程式
#		for Carnival SCM [Sample]  management
#			Jack Yang     2004/11/10
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//		job 39  [生產製造][生產產能]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

require_once "config.php";
require_once "config.admin.php";

$PHP_SELF = $_SERVER['PHP_SELF'];

$perm = $GLOBALS['power'];

require_once "init.object.php";

$op = array();
$max_file_size = 3145728; //3 Mb

session_register	('sch_parm');
session_register	('partial_id');
// echo $PHP_action;
switch ($PHP_action) {
//=======================================================
//-------------------------------------------------------------------------------------
//			 job 31  製作令
//-------------------------------------------------------------------------------------
case "wi":
check_authority("019","view");

$where_str = '';		 

// creat cust combo box	 取出 客戶代號
$where_str=$where_str."order by cust_s_name";
if(!$cust_def = $cust->get_fields('cust_init_name',$where_str)){;  
    $op['msg'][] = "sorry! there is no any customer record in your team, please add customer first!";
    $layout->assign($op);
    $layout->display($TPL_ERROR);  		    
    break;
}

if(!$cust_def_vue = $cust->get_fields('cust_s_name',$where_str)){;  
    $op['msg'][] = "sorry! there is no any customer record in your team, please add customer first!";
    $layout->assign($op);
    $layout->display($TPL_ERROR);  		    
    break;
}

for ($i=0; $i< sizeof($cust_def); $i++){
    $cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];
}

$op['cust_select'] =  $arry2->select($cust_value,'','PHP_cust','select','',$cust_def_vue);
$op['factory_select'] = $arry2->select(get_factory_group(),'','PHP_sch_fty','select','');
$op['dept_select'] = $arry2->select(get_dept_group(),"","PHP_dept_code","select","");

$op['msg'] = $wi->msg->get(2);

//080725message增加		
$note=$notify->search($GLOBALS['SCACHE']['ADMIN']['login_id'], "`read`=0");
$op['max_notify'] = $note['max_no'];

page_display($op, "019", $TPL_WI_SEARCH);	    	    
break;



//=======================================================
case "wi_search":
check_authority("019","view");

if (!isset($PHP_un_fhs)){$PHP_un_fhs='';}

if(isset($PHP_num))
{
	$_SESSION['sch_parm'] = array();
	$_SESSION['sch_parm'] = array(	
		"PHP_dept_code"		=>  $PHP_dept_code,		"PHP_num"			=>  $PHP_num,
		"PHP_cust"			=>	$PHP_cust,			"PHP_etdstr"		=>	$PHP_etdstr,
		"PHP_etdfsh"		=>	$PHP_etdfsh,		"PHP_sch_fty"		=>	$PHP_sch_fty,
		"PHP_un_fhs"		=>	$PHP_un_fhs,					
		"PHP_sr_startno"	=>	$PHP_sr_startno,	"PHP_action"		=>	$PHP_action,		
		);			
}else{
    if(isset($PHP_sr_startno))$_SESSION['sch_parm']['PHP_sr_startno'] = $PHP_sr_startno;
}


if (!$op = $order->mater_search(1,$_SESSION['sch_parm']['PHP_dept_code'])) {
	$op['msg']= $order->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}


for ($i=0; $i<sizeof($op['sorder']); $i++)
{
	if(file_exists($GLOBALS['config']['root_dir']."/picture/".$op['sorder'][$i]['order_num'].".jpg")){
		$op['sorder'][$i]['main_pic'] = "./picture/".$op['sorder'][$i]['order_num'].".jpg";
	} else {
		$op['sorder'][$i]['main_pic'] = "./images/graydot.gif";
	}
	
	if ($op['sorder'][$i]['wi_id'])
	{
		$where_str = "WHERE wi_id = '".$op['sorder'][$i]['wi_id']."'";
		$op['sorder'][$i]['ti'] = $ti->get_fields('id',$where_str);
		$op['sorder'][$i]['bom_lots'] = $smpl_bom->get_fields_lots('id',$where_str);
		$op['sorder'][$i]['bom_acc'] =  $smpl_bom->get_fields_acc('id',$where_str);
	}
	
	$op['sorder'][$i]['acc_ap'] = $bom->get_aply($op['sorder'][$i]['wi_id'], 'bom_acc');
	$op['sorder'][$i]['lots_ap'] = $bom->get_aply($op['sorder'][$i]['wi_id'], 'bom_lots');
	
}

$op['msg']= $order->msg->get(2);

page_display($op, "019", $TPL_WI);
break;



//=======================================================
    case "wi_add":

		check_authority("019","add");
			if ($PHP_size > 0)
			{
				$size_scale=$size_des->get_fields('size_scale',"where id='$PHP_size'");				
				$PHP_size_scale=$size_scale[0];
			}
			
			$_SESSION['partial_id'] = $PHP_p_id;	//order_partial的ID
			
			// 取出選項資料 及傳入之參數
			$op['wi']['cust'] = $PHP_customer;							
			$op['smpl']['size_scale'] = $PHP_size_scale;
			$op['smpl']['size'] = $PHP_size;
			$op['smpl']['style_code'] = $PHP_style_code;
			$op['smpl']['cust_ref'] = $PHP_cust_ref;
			$op['smpl']['id'] = $PHP_smpl_id;
			$op['smpl']['etd'] = $PHP_etd;
			$op['smpl']['unit'] = $PHP_unit;				
			$op['wi']['dept']	 = $PHP_dept;
			// pre  製造令編號....
			$op['wi']['wi_precode'] = $PHP_style_code;


			// 取出選項資料 及傳入之參數
			

			page_display($op, "019", $TPL_WI_ADD);	    	    
		break;
		
//=======================================================
	case "do_wi_add_1":

		check_authority("019","add");
		// 取出年碼... 以輸入時之年為年碼[兩位]
		$dt = decode_date(1);
		$size_id=$PHP_Size_id;
		if ($PHP_Size_add==1)
		{
			if ($PHP_Size_id==0)
			{
				$PHP_Size_item = strtoupper($PHP_Size_item);
				$PHP_Size = strtoupper($PHP_Size);
				$PHP_base_size = strtoupper($PHP_base_size);
				$parm=array(	'cust'				=>	$PHP_cust,
											'dept'				=>	$PHP_dept_code,
											'size'				=>	$PHP_Size_item,
											'size_scale'	=>	$PHP_Size,
											'base_size'		=>	$PHP_base_size,
											'check'				=>	1,
				);
				$size_id=$size_des->add($parm);				
			}
			$f1 = $order->update_field('size', $size_id, $PHP_smpl_id);
		}

		$parm = array(	"dept"				=>	$PHP_dept_code,
										"cust"				=>	$PHP_cust,
										"cust_ref"		=>	$PHP_cust_ref,
										"smpl_id"			=>	$PHP_smpl_id,

										"etd"					=>	$PHP_etd,						
										"unit"				=>	$PHP_unit,

										"size_scale"	=>	$size_id,	// 不存入 wi
						

										"style_code"	=>	$PHP_style_code,
										"creator"			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
										"open_date"		=>	$dt['date_str'],
										"version"			=>	"1"
								);

		// check 寫入 wi 檔	資料		
			$op['wi'] = $parm;
		if (!$f1 = $wi->check($parm)) {  // 沒有成功輸入資料時
			$op['msg'] = $wi->msg->get(2);
			
			// 抓入 視窗的樣本參數 ----
			$op['smpl']['size_scale'] = $PHP_Size;
			$op['smpl']['size'] = $size_id;
			$op['smpl']['style_code'] = $parm['style_code'];
			$op['smpl']['cust_ref'] = $parm['cust_ref'];
			$op['smpl']['id'] = $parm['smpl_id'];
			$op['smpl']['etd'] = $parm['etd'];
			$op['smpl']['unit'] = $PHP_unit;
			$op['smpl']['status'] = $PHP_status;
			// 設定 re_adding 標桿---
			$op['re_adding'] = "1";
			// 相片的URL決定  ---------------------------------------------
				$style_dir	= "./picture/";  
				$no_img		= "./images/graydot.gif";
			if(file_exists($style_dir.$op['wi']['style_code'].".jpg")){
				$op['pic_link'] = $style_dir.$op['wi']['style_code'].".jpg";
			} else {
				$op['pic_link'] = $no_img;
			}

			// 列出 新增 製作令 選入樣本的視窗 ---------------------------------------------
					
				// 取出年碼...
				$dt = decode_date(1);
				$year_code = substr($dt['year'],-2);
				// pre  製造令編號....
				$op['wi']['wi_precode'] = $parm['style_code'];

					// 選項的 combo 設定
				$op['unit'] = $arry2->select($APPAREL_UNIT,$parm['unit'],'PHP_unit','select','');  	
				// creat sample type combo box
				$sample_def = $smpl_type->get_fields('smpl_type');   // 取出 樣本類別
			page_display($op, 3, 1, $TPL_WI_ADD);	    	    
			break;		
		}   // end check
		
		// 編製 製作令 號碼----------------------------------------------------------------
		//  編製 制作令號碼 也同時更新dept檔內的num值[csv]
		$parm['wi_num'] = $PHP_style_code; 

	
		
		$f1 = $wi->add($parm);
	
		if (!$f1) {  // 沒有成功輸入資料時

			$op['msg'] = $wi->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);
			break;

		}   // end if (!$F1)---------  結束判斷 ------------------------------------
		$message = "Done ADD [".$parm['wi_num']."] W / I；Please add colorway / breakdown and materiel consump.";
		$log->log_add(0,"31A",$message);
		// 將 wis [樣本製令]編號 寫入 樣本檔內[ smpl table ] NOTE: 主要是讓該款 樣本不能再做任何製令[需再複製]
				if (!$order->update_field("marked",$parm['wi_num'],$parm['smpl_id'])) {
					$op['msg']= $order->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
				}
				if (!$order->update_field("m_status",'1',$parm['smpl_id'])) {
					$op['msg']= $order->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
				}

		$back_str = "wi.php?&PHP_action=wi_edit&PHP_id=".$f1."&PHP_style_code=".$parm['wi_num']."&PHP_msg=".$message;			
		redirect_page($back_str);
		break;		

//=======================================================
	case "do_wi_copy":

		check_authority("019","edit");
		$_SESSION['partial_id'] = $PHP_p_id;	//order_partial的ID
//由Sample order複製[製造令]		
		$f1=$wi->copy_wi($PHP_wi_num,$PHP_smpl_id,$PHP_new_num,$PHP_etd,$PHP_ord_cust,$PHP_dept,$GLOBALS['SCACHE']['ADMIN']['login_id'],$dt['date_str']);
		
		if (!$f1)
		{  
			$op['msg']= $wi->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
// 將 wis [樣本製令]編號 寫入 樣本檔內[ smpl table ] NOTE: 主要是讓該款 樣本不能再做任何製令[需再複製]
		if (!$order->update_field("marked",$PHP_new_num,$PHP_smpl_id)) {
			$op['msg']= $order->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		if (!$order->update_field("m_status",'1',$PHP_smpl_id)) {
			$op['msg']= $order->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		
		$back_str = "wi.php?&PHP_action=wi_edit&PHP_id=".$f1."&PHP_style_code=".$PHP_new_num;					
		redirect_page($back_str);
		break;		

//=======================================================
	case "search_ord_wi_copy":
	$_SESSION['partial_id'] = $PHP_p_id;	//order_partial的ID
	if($PHP_ord_cust == 'GV') 
	{
		$ord_rec = $order->get('',$PHP_wi_num);

		if(isset($ord_rec['carry_ord']) && sizeof($ord_rec['carry_ord']) > 0)
		{
			for($i=0; $i<sizeof($ord_rec['carry_ord']); $i++)
			{
				$wi_rec = $wi->get('',$ord_rec['carry_ord'][$i]['ord_org']);
			 	$op['wi'][$i]['id'.$i]=$wi_rec['id'];
			 	$op['wi'][$i]['wi_num'.$i]=$wi_rec['wi_num'];	
			//	$op['wi'][]=$ord_rec['carry_ord'][$i];
			}
		}else{
			$op['wi']=$wi->copy_search_all();
		}
	}else{
	 $op['wi']=$wi->copy_search($PHP_ord_cust, $PHP_dept);                                                                                   
	}

  $op['copy_search'] = "&PHP_wi_num=".$PHP_wi_num."&PHP_smpl_id=".$PHP_smpl_id."&PHP_dept=".$PHP_dept."&PHP_ord_cust=".$PHP_ord_cust."&PHP_etd=".$PHP_etd;
  

	page_display($op, "010", $TPL_WI_COPY_SEARCH);
	break;
	
	
//========================================================================================	
	case "wi_copy_view":

		check_authority("019","view");

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
		$where_str = " WHERE wi_id='".$op['wi']['id']."'";
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

   $op['copy_search'] = "&PHP_wi_num=".$PHP_wi_num."&PHP_smpl_id=".$PHP_smpl_id."&PHP_dept=".$PHP_dept."&PHP_ord_cust=".$PHP_ord_cust."&PHP_etd=".$PHP_etd;

    $op['org_wi_num'] = $PHP_wi_num;
    $op['org_smpl_id'] = $PHP_smpl_id;
    $op['org_etd'] = $PHP_etd;
		
		page_display($op, "019", $TPL_WI_COPY_VIEW);
		break;	

//=======================================================
	case "do_wi_ord_copy":

		check_authority("019","edit");		
		$f1=$wi->copy_ord_wi($PHP_wi_num,$PHP_smpl_id,$PHP_new_num,$PHP_etd,$PHP_cust,$PHP_dept,$GLOBALS['SCACHE']['ADMIN']['login_id'],$dt['date_str']);		
		if (!$f1){
			$op['msg']= $wi->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
// 將 wis [樣本製令]編號 寫入 樣本檔內[ smpl table ] NOTE: 主要是讓該款 樣本不能再做任何製令[需再複製]
		if (!$order->update_field("marked",$PHP_new_num,$PHP_smpl_id)) {
			$op['msg']= $order->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		if (!$order->update_field("m_status",'1',$PHP_smpl_id)) {
			$op['msg']= $order->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		
		$back_str = "wi.php?&PHP_action=wi_edit&PHP_id=".$f1."&PHP_style_code=".$PHP_new_num;					
		redirect_page($back_str);
		break;

//==================================================================================
case "edit_add_colorway":
check_authority("019","edit");

// 傳入 javascript傳出的參數 [ colorway : 必然輸入已由 javascript撿查 ]
$parm['qty']		= $PHP_qty;   // 由javascript傳出的陣列值 自動改成csv傳出
$parm['wi_id']		= $wi_id;
$parm['colorway']	= $colorway;
$parm['p_id']	= $_SESSION['partial_id'];

// 加入 新色組 ----- 加入 wiqty table -----------------
if(!$T_del = $wiqty->add($parm)){    
	$op['msg']= $wi->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}

// 將 製造令 完整show out ---------------------------------------------
//  wi 主檔 抓出來 ----------------------------------------------------
if(!$op = $wi->get_all($wi_id)){    //取出該筆 製造令記錄
	$op['msg']= $wi->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);
	break;
}

# 記錄使用者動態
$message = "Successfully add wi :".$op['wi']['wi_num']." colorway :".$colorway;
$log->log_add(0,"31E",$message);

if(!empty($PHP_revice)) {

}else{
	$back_str = "wi.php?&PHP_action=wi_edit&PHP_id=".$wi_id."&PHP_msg=".$message."&PHP_style_code=".$op['wi']['wi_num'];			
}

redirect_page($back_str);
break;
		
//=======================================================
		case "edit_del_colorway_ajax":

		check_authority("019","edit");
		//print_r($_GET);
		//exit;
		//判斷是否決定要隱藏刪除鈕及可否刪除該顏色的尺碼數量
		$schedule_qty_array = $schedule->get_order_schedule($PHP_wi_num,$_SESSION['partial_id']);
		$schd_sum=0;
		foreach($schedule_qty_array as $schd_key=>$schd_value)
		{
			$schd_sum += $schd_value['qty'];
		}
		//可以從$id查出(wiqty)p_id
		$where_str = " WHERE wi_id='".$wi_id."' AND p_id = '".$_SESSION['partial_id']."'";
		$T_wiqty = $wiqty->search(1,$where_str);
		$wi_sum=0;
		foreach($T_wiqty as $key=>$value)
		{
			$col_qty_array = explode(",", $value['qty']);
			foreach($col_qty_array as $key1=>$value1)
			{
				$wi_sum += $value1;
			}
		}
		$del_success = 0;
		if(($wi_sum - $del_qty) >= $schd_sum)
		{
		
			// 刪除 指定之色組 ----------------------------
			if(!$T_del =	$wiqty->del($id)){    
							$op['msg']= $wi->msg->get(2);
							$layout->assign($op);
							$layout->display($TPL_ERROR);  		    
							break;
			}

			$size_edit = 0;
			//檢視是否可以修改size(即是否存在colorway)
			if ($wi->check_size($PHP_wi_num))
			{
				$size_edit=1;
			}

			
			$disable_delete_icon=1;
			if($schd_sum >= ($wi_sum - $del_qty))
			{
				$disable_delete_icon=0;
			}
			$message = "Success delete wi[".$PHP_wi_num."] colorway[".$colorway."]";
			$log->log_add(0,"31E",$message);
			$del_success = 1;
		}
		
		echo $message."|".$size_edit."|".$disable_delete_icon."|".$del_success;
		exit;
		break;		
		
//=======================================================
	case "wi_edit":

		check_authority("019","edit");
		
		if(isset($PHP_p_id))$_SESSION['partial_id'] = $PHP_p_id;	//order_partial的ID
		// 將 製造令 完整show out ------
		//  wi 主檔 抓出來
		if(!$op = $wi->get_all($PHP_id)){    //取出該筆 製造令記錄
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}

	//檢視是否可以修改size(即是否存在colorway)
		if ($wi->check_size($op['wi']['style_code']))
		{
			$op['size_edit']=1;
		}

		//  wi_qty 數量檔 -----------------------------------------------------------------
		$where_str = " WHERE wi_id='".$PHP_id."' AND p_id = '".$_SESSION['partial_id']."'";
		$T_wiqty = $wiqty->search(1,$where_str);

		// 取出 size_scale --------
		$size_A = $size_des->get($op['smpl']['size']);
		$op['smpl']['size_scale']=$size_A['size_scale'];
			//????????????????????????????????????? 注意 有時沒有 size type 資料時??????
		$sizing = explode(",", $size_A['size']);
		$op['size'] = $sizing;
		$op['b_size'] = $size_A['base_size'];

		//取出此partial的schedule數量，準備判斷用
		$op['partial_schedule'] = $schedule->get_order_schedule($op['wi']['wi_num'],$_SESSION['partial_id']);
		$op['partial_schedule_qty']=0;
		foreach($op['partial_schedule'] as $key=>$value)
		{
			$op['partial_schedule_qty'] = $op['partial_schedule_qty']+$value['qty'];
		}
		//取出partial數量
		$op['T_wiqty'] = $T_wiqty;
		
		foreach($T_wiqty as $key=>$value)
		{
			//echo $value['id'] ;
			//echo "<br>";
			$col_qty_array = explode(",", $value['qty']);
			//print_r($col_qty_array);
			$col_sum=0;
			foreach($col_qty_array as $key1=>$value1)
			{
				$col_sum += $value1;
			}
			$op['wiqty_detail'][$key]['id'] = $value['id'];
			$op['wiqty_detail'][$key]['qty'] = $col_sum;
			$op['wiqty_detail'][$key]['p_id'] = $value['p_id'];
		}
		$wiqty_sum = 0 ;
		foreach($op['wiqty_detail'] as $key2 => $value2)
		{
			$wiqty_sum += $value2['qty'];
			//echo $value2['qty'];
		}
		$op['wiqty_sum'] = $wiqty_sum;
		$enable_delete = false;
		if($op['partial_schedule_qty'] < $op['wiqty_sum'])
		{
			$enable_delete =true;
		}
		// 做出 size table-------------------------------------------
		
			$op['show_del_breakdown'] = show_del_breakdown($T_wiqty,$sizing,'',$size_A['base_size'],$enable_delete,$wiqty_sum,$op['partial_schedule_qty']);
			$op['edit_breakdown'] = edit_breakdown($sizing,'',$size_A['base_size']);

			$op['msg'] = $wi->msg->get(2);

			// 做出 unit的 combo
		$op['unit'] = $arry2->select($APPAREL_UNIT,$op['wi']['unit'],'PHP_unit','select','');  	

		// creat sample type combo box
		$sample_def = $smpl_type->get_fields('smpl_type');   // 取出 樣本類別
		$op['smpl_type'] =  $arry2->select($sample_def,$op['wi']['smpl_type'],'PHP_smpl_type','select','');  	
			
		$op['msg'] = $wi->msg->get(2);
		
		
		//print_r($op);
		//exit;
		if(isset($PHP_msg))$op['msg'][] = $PHP_msg;
		page_display($op, "019", $TPL_WI_EDIT);
		break;		
		
		
//=======================================================
	case "do_wi_edit":

		check_authority("019","edit");
		$argv = array(	"id"			=>  $PHP_wi_id,
						"wi_num"		=>	$PHP_wi_num,
						"smpl_id"		=>	$PHP_smpl_id,						
						"version"		=>	$PHP_version,
						"etd"			=>	$PHP_etd,						
						"unit"			=>	$PHP_unit,
						"updator"		=>	$GLOBALS['SCACHE']['ADMIN']['login_id']
			);

// UPDATE  wi 資料表內[etd],[smpl_type][unit] 三個欄 且將version 加一
		//加入size部分資料		
		$size_id=$PHP_Size_id;
		if ($PHP_Size_add==1)
		{
			if ($PHP_Size_id==0)
			{
				$PHP_Size_item = strtoupper($PHP_Size_item);
				$PHP_Size = strtoupper($PHP_Size);
				$PHP_base_size = strtoupper($PHP_base_size);
				$parm=array(	'cust'			=>	$PHP_cust,
								'dept'			=>	$PHP_dept_code,
								'size'			=>	$PHP_Size_item,
								'size_scale'	=>	$PHP_Size,
								'base_size'		=>	$PHP_base_size,
								'check'			=>	1,
				);
				$size_id=$size_des->add($parm);				
			}
			$f1 = $order->update_field('size', $size_id, $PHP_smpl_id);
		}
		
		$updt = array(	"etd"				=>	$argv['etd'],						
										"unit"			=>	$argv['unit'],
										"version"		=>	$argv['version'] +1,
										"updator"		=>	$argv['updator'],
										"id"				=>	$argv['id']
		);
		if(!$wi->edit($updt)){
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}


		//--------------- 完成修改寫入------------------

		//--------------- 列出改後之記錄------------------
		//  wi 主檔 --------------------------------------------------------------------------------
		if(!$op = $wi->get_all($argv['id'])){    //取出該筆 製造令記錄
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}

		//  wi_qty 數量檔 --------------------------------------------------------------------------
			$where_str = " WHERE wi_id='".$op['wi']['id']."' AND p_id = '".$_SESSION['partial_id']."'";
		$T_wiqty = $wiqty->search(1,$where_str);

		$num_colors = count($T_wiqty);

		// 取出 size_scale -----------------------------------------------------
		$size_A = $size_des->get($op['smpl']['size']);
		$op['smpl']['size_scale']=$size_A['size_scale'];
			//????????????????????????????????????? 注意 有時沒有 size type 資料時??????
		$sizing = explode(",", $size_A['size']);

			// 做出 size table-------------------------------------------
		$reply = show_breakdown($T_wiqty,$sizing,$size_A['base_size']);
		$op['show_breakdown'][0] = $reply['html'];
		$op['total'] = $reply['total'];
		//-----------------------------------------------------------------

			# 記錄使用者動態
		$message = "Successfully update wi:[".$op['wi']['wi_num']."]";
		$log->log_add(0,"31E",$message);
		$op['msg'][] = $message;
		page_display($op, "019", $TPL_WI_VIEW);	
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "wi_del":

			// 必需要 manager login 才能真正的刪除 SUPLIER  ----------------------------------
		if(!$admin->is_power("019","del")  && !($GLOBALS['SCACHE']['ADMIN']['id'] == "SA" )) {
			$op['msg'][] = "sorry! 您沒有這項權限!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$op['wi'] = $wi->get($PHP_id);

		// 刪除 wi 主檔資料 ---------------------------------------------------
		if (!$wi->del($PHP_id)) {
			$op['msg'] = $wi->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;	    
		}
		// 刪除 wi qty 數量檔資料 ---------------------------------------------------
		if (!$wiqty->del($PHP_id,'1')) {
			$op['msg'] = $wiqty->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;	    
		}
					
		// 刪除 TI 說明檔資料 ---------------------------------------------------
		if (!$ti->del($PHP_id,'1')) {
			$op['msg'] = $ti->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;	    
		}
		
		
		// 解除 smpl 記錄中之 marked ---------------------------------------------------
		if (!$order->update_field("marked",'',$op['wi']['smpl_id'])) {
			$op['msg']= $order->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		// 解除 smpl 記錄中之 marked ---------------------------------------------------
		if (!$order->update_field("m_status",'0',$op['wi']['smpl_id'])) {
			$op['msg']= $order->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
					
					
					# 記錄使用者動態
		$message = "Successfully delete wi:[".$op['wi']['wi_num']."] 。";
//		$log->log_add(0,"21D",$message);
		
		$back_str = "wi.php?&PHP_action=wi_search&PHP_sr_startno=".$PHP_sr_startno."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh."&PHP_sch_fty=".$PHP_sch_fty."&PHP_un_fhs=".$PHP_un_fhs;
		redirect_page($back_str);
		break;
		

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//   wi_uncfm      //  WI CFM...........
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "wi_uncfm":
check_authority('020',"view");

if (!$op = $wi->search_uncfm('wi')) {
    $op['msg']= $wi->msg->get(2);
    $layout->assign($op);
    $layout->display($TPL_ERROR);
    break;
}

for ($i=0; $i<sizeof($op['wi']); $i++) {
    $where_str="WHERE smpl_code = '".$op['wi'][$i]['wi_num']."'";
    $op['wi'][$i]['lots']=$smpl_lots->get_fields('id',$where_str);
    $op['wi'][$i]['acc']=$smpl_acc->get_fields('id',$where_str);
}

$op['dept_id'] = get_dept_id();
$op['msg']= $wi->msg->get(2);
if(isset($PHP_msg))$op['msg'][] = $PHP_msg;

$note=$notify->search($GLOBALS['SCACHE']['ADMIN']['login_id'], "`read`=0");
$op['max_notify'] = $note['max_no'];

page_display($op, '020', $TPL_WI_UNCFM_LIST);
break;



//=======================================================
	case "do_wi_cfm":
		check_authority('020',"add");
		
		$parm = array(	"order_num" =>	$PHP_order_num,
						"id"		=>	$PHP_id,
						"date"		=>	$dt['date_str'],
						"user"		=>	$GLOBALS['SCACHE']['ADMIN']['login_id']
					  );
		if (isset($PHP_msg))
		{
		if (substr($PHP_msg,0,1)<> 's')
		{	
			if ($PHP_msg == "ERROR! Please  check Order Q'TY and Order WI Q'TY first!")
			{
				$redir_str='wi.php?PHP_action=wi_cfm_qty_edit&PHP_id='.$PHP_id.$PHP_back_str2.'&PHP_cfm_view=1&PHP_msg='.$PHP_msg.'&PHP_partial_mks='.$PHP_partial_mks;
				
				//$redir_str.='&back_PHP_id='.$back_PHP_id;
				redirect_page($redir_str);
				break;				
			}else{
				$redir_str='index2.php?PHP_action=wi_view&PHP_id='.$PHP_id.'&PHP_cfm_view=1&PHP_msg='.$PHP_msg;
				redirect_page($redir_str);
				break;
			}
		}
		}
		
		$f1 = $wi->cfm($parm);
		if($f1)
		{
			$message="success CFM WI:[".$PHP_wi."]";
			$log->log_add(0,"33A",$message);	

//傳送message給RD人員
			$rcd = $wi->get_all($PHP_id);
			if($rcd['wi']['status'] > 0 && $rcd['wi']['ti_cfm'] <> '0000-00-00' )
			{
	 			if($PHP_fty == 'WX') $PHP_fty = 'HJ';
	 			$messg  = ' 製造令(Working Instruction) : <a href=index2.php?PHP_action=wi_view&PHP_id='.$PHP_id.$PHP_back_str2.'>'.$PHP_order_num.'</a>';
	 			$messg  .=' 製造說明 (WorkSheet) : <a href=index2.php?PHP_action=ti_view&PHP_id='.$PHP_id.'&PHP_style_code='.$PHP_order_num.'&PHP_dept_code=&PHP_etdfsh=&PHP_cust=&PHP_wi_num=&PHP_etdstr=&PHP_fty_sch=&PHP_sr=1>'.$PHP_order_num.'</a>';
	 			$notify->system_msg_send_with_mail('3-3-S',$PHP_fty,$PHP_order_num,$messg);
			}

			//傳送message給主料預估者
			$messg  = '<a href="fab_comp.php?PHP_action=fab_comp_edit&PHP_id='.$PHP_id.'&PHP_sr=1">'.$PHP_order_num.'</a>';
			$notify->system_msg_send('3-3-S','RD',$PHP_order_num,$messg);

			$parm_update = array($PHP_id,'last_update',$dt['date_str']);
	 		$wi->update_field($parm_update);
	 		$parm_update = array($PHP_id,'updator',$GLOBALS['SCACHE']['ADMIN']['login_id']);
	 		$wi->update_field($parm_update);
		}else{
			$message = "WI CONFIRMED";
		}
		
		$redir_str='wi.php?PHP_action=wi_uncfm&PHP_msg='.$message;
		redirect_page($redir_str);
		break;		
		
//========================================================================================	
	case "wi_cfm_qty_edit":

		check_authority("019","edit");
//print_r($_GET);
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
		$where_str = " WHERE wi_id='".$op['wi']['id']."' AND p_id = '".$_SESSION['partial_id']."'";
		$T_wiqty = $wiqty->search(1,$where_str);

		$num_colors = count($T_wiqty);

		// 取出 size_scale ----------------------------------------------
		$size_A = $size_des->get($op['smpl']['size']);
		$op['smpl']['size_scale']=$size_A['size_scale'];
			//????????????????????????????????????? 注意 有時沒有 size type 資料時??????
		$sizing = explode(",", $size_A['size']);

			// 做出 size table-------------------------------------------
		$reply = breakdown_cfm_edit($T_wiqty,$sizing,$size_A['base_size']);
		$op['show_breakdown'] = $reply['html'];
		$op['total'] = $reply['total'];
		//-----------------------------------------------------------------
		$op['i'] = count($sizing);
		$op['j']= count($T_wiqty);
		//echo $op['wi']['wi_num'];
		//echo "<br>";
		//print_r($op);
		//加入order交期
		$op['partial_mks']= $PHP_partial_mks;
		//排產總數DTH12-0639
		//$test=5063;
		$op['order_schedule'] = $schedule->get_order_schedule($op['wi']['wi_num']);
		//$op['order_schedule'] = $schedule->get_order_schedule('DTH12-0639');
		$op['order_schedule_qty']=0;
		foreach($op['order_schedule'] as $key=>$value)
		{
			$op['order_schedule_qty'] = $op['order_schedule_qty']+$value['qty'];
		}
		//echo $op['order_schedule_qty'];
		//訂單總數 ps_id smpl
		$order_total=0;
		$order_total=$wi->get_value("select qty from s_order where id=".$op['smpl']['id']);
		//foreach($op['smpl']['ps_id'] as $ord_qty_key => $ord_qty_value)
		//{
			//echo $ord_qty_value ;
			//echo "<br>";
			//$order_total += $wi->order_qty_sum($ord_qty_value);
			
		//}
		$op['order_total_qty']=$order_total;
		$op['back_PHP_id'] = $PHP_id;
		//echo $op['order_total_qty'];
		//print_r($_GET);
		//print_r($op);
		//exit;
			$op['msg'] = $wi->msg->get(2);
			if (isset($PHP_msg))$op['msg'][]=$PHP_msg;
		page_display($op, "019", $TPL_WI_QTY_EDIT);			
		break;		
		
//========================================================================================	
	case "edit_color_qty":

		check_authority("019","edit");
		
		//print_r($_GET);
		//exit;
		$o_su=$_GET['PHP_ord_ie'] * $_GET['PHP_ord_new_qty'] ;
		$p_su=$_GET['PHP_ord_ie'] * $_GET['PHP_partial_new_total'];
		/*$parm = array(	"order_id"  =>	$PHP_ord_id,
					"order_qty"		=>	$PHP_ord_new_qty,
					"order_su"		=>	$o_su,
					"partial_id"	=>	$_SESSION['partial_id'],
					"partial_qty"	=>	$PHP_partial_new_total,
					"partial_su"	=>	$p_su
				  );*/
		
		############################################################################################################
		$tmp_qty_csv='';

		$wqty_id=explode(',',$PHP_wqty_id);
		$qty_item=explode(',',$PHP_qty_item);
		$tmp_i=count($wqty_id);
		$tmp_j=count($qty_item);
		$count_qty= count($qty_item)/ count($wqty_id);
		
		$k=0;
			for ($i=0; $i< sizeof($wqty_id); $i++)
			{
				for ($j=0; $j < $count_qty; $j++)
				{
					$tmp_qty_csv=$tmp_qty_csv.$qty_item[$k].',';
					$k++;
				}
				$tmp_qty_csv=substr($tmp_qty_csv,0,-1);
				$wiqty->update_field('qty', $tmp_qty_csv, $wqty_id[$i]);				
				$tmp_qty_csv='';
			}
			
		//xxxxx訂單數量xxxx
		$s_ord=$order->get($PHP_ord_id);
		$ord_part = $order->get_partial($_SESSION['partial_id']);
		
		if ($ord_part['p_qty'] <>$PHP_ord_qty)
		{
			
			$su=(int)($ord_part['p_qty']*$s_ord['ie1']);
			if ($s_ord['status'] >= 4 && $s_ord['status'] <> 5)
			{	//ETP~ETD的SU							 
				 $etp_su=decode_mon_yy_su($ord_part['p_etp_su']); //取出己存在etp-su並將su值與年,月分開
				 for ($i=0; $i<sizeof($etp_su); $i++) //將己存在su的年度記錄值刪除				 				 	
				 	$f1=$capaci->delete_su($s_ord['factory'], $etp_su[$i]['year'], $etp_su[$i]['mon'], 'pre_schedule', $etp_su[$i]['su']);			 						 
				 
				 $div = $order->distri_month_su($su,$ord_part['p_etp'],$ord_part['p_etd'],$s_ord['factory'],'pre_schedule'); //重算每月SU並儲存
				 $order->update_partial('p_etp_su', $div, $_SESSION['partial_id']);		
				 $order->update_distri_su($s_ord['order_num'],'p_etp_su','etp_su');  //儲存新etp_su
			}
			
			
//			if($s_ord['status'] >= 7){
//				 //ETS~ETF的SU(工廠排程)
//				 $fty_su=decode_mon_yy_su($ord_pdt['fty_su']);//取出己存在fty-su並將su值與年,月分開
//				 for ($i=0; $i<sizeof($fty_su); $i++)//將己存在su的年度記錄值刪除
//				 {				 	
//				 	$f1=$capaci->delete_su($s_ord['factory'], $fty_su[$i]['year'], $fty_su[$i]['mon'], 'schedule', $fty_su[$i]['su']);			 		
//				 }
//				 $div = $order->distri_month_su($su,$ord_pdt['ets'],$ord_pdt['etf'],$s_ord['factory'],'schedule');//重算每月SU並儲存
//				 $order->update_pdtion_field('fty_su', $div, $ord_pdt['id']);//儲存新fty_su				 
//			}

			// 寫入 qty和su
				$p_qty = $PHP_ord_qty - $ord_part['p_qty'];
				$p_su  = $su - $ord_part['p_su'];
				$s_ord['qty'] += $p_qty;
				$s_ord['su'] += $p_su;
				$order->update_partial('p_qty', $PHP_ord_qty, $_SESSION['partial_id']);
				$order->update_partial('p_su', $su, $_SESSION['partial_id']);
			  $order->update_2fields('qty','su', $s_ord['qty'], $s_ord['su'], $s_ord['id']);
			  

//			$order->update_field('qty', $PHP_ord_qty, $PHP_ord_id);
//			$order->update_field('su', $su, $PHP_ord_id);			
		}
		$wi->revise_qty($PHP_ord_id,$PHP_ord_new_qty,$o_su,$_SESSION['partial_id'],$PHP_partial_new_total,$p_su);
		
		
		$message="success edit order qty on wi cfm:[".$s_ord['order_num']."]";
		$log->log_add(0,"101A",$message);	
		$log->log_add(0,"31A",$message);	
			
		$op['msg'] = $wi->msg->get(2);
			
		$redir_str='index2.php?PHP_action=wi_view&PHP_id='.$PHP_wi_id.'&PHP_cfm_view=1&PHP_msg='.$message;
		redirect_page($redir_str);
		break;

//=======================================================
    case "wi_print":   //  .......

		if(!$admin->is_power("019","view")){ 
			$op['msg'][] = "sorry! 您沒有這項權限!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		if(is_numeric($PHP_id)){
			if(!$wi = $wi->get($PHP_id)){    //取出該筆 製造令記錄 ID
						$op['msg']= $wi->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
			}
		} else {
			if(!$wi = $wi->get(0,$PHP_id)){    //取出該筆 製造令記錄 WI_NUM
						$op['msg']= $wi->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
			}
		}

		//  smpl 樣本檔
		if(!$smpl = $order->get($wi['smpl_id'])){
					$op['msg']= $order->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}

//--------------------------------------------------------------------------
		// 取出 size_scale ----------------------------------------------
//		$where_str = " WHERE cust='".$wi['cust']."' AND size_scale='".$smpl['size']."' ";
		$size_A = $size_des->get($smpl['size']);
		$smpl['size_scale']=$size_A['size_scale'];
		$sizing = explode(",", $size_A['size']);

		//  wi_qty 數量檔
		//  wi_qty 數量檔
		$where_str = " WHERE wi_id='".$wi['id']."' ";
		if($_SESSION['partial_id'])
		{
			$where_str .=" AND p_id='".$_SESSION['partial_id']."'";
			$T_wiqty = $wiqty->search(1,$where_str);
			$num_colors = count($T_wiqty);
			// 做出 size table-------------------------------------------
			$reply = get_colorway_qty($T_wiqty,$sizing);
			$op['total'] = $reply['total'];   // 總件數
			$data[0] = $reply['data'];
			$colorway_name[0] = $reply['colorway'];
			$colorway_qty[0] = $reply['colorway_qty'];		
			$p_etd[0]='';
		}else{
			$where_str2 = " WHERE ord_num = '".$wi['wi_num']."' ORDER BY p_etd";
			$p_id = $order->get_fields('id', $where_str2,'order_partial');
			$p_etd = $order->get_fields('p_etd', $where_str2,'order_partial');
			$op['total'] =0;			
			for($i=0; $i<sizeof($p_id); $i++)
			{
				$where_str3 =$where_str." AND p_id='".$p_id[$i]."'";
				$T_wiqty = $wiqty->search(1,$where_str3);
				$num_colors = count($T_wiqty);
				// 做出 size table-------------------------------------------
				$reply = get_colorway_qty($T_wiqty,$sizing);
				$data[$i] = $reply['data'];
				$op['total'] += $reply['total'];
				$colorway_name[$i] = $reply['colorway'];
				$colorway_qty[$i] = $reply['colorway_qty'];									
			}
		}

		//-----------------------------------------------------------------

		//  取出 生產說明 記錄 -------------------------------------------------
		$where_str = " WHERE wi_id = '".$wi['id']."' ";
		$T = $ti->search(0,$where_str);  //取出該筆 樣本主料記錄
		$op['ti'] = $T['ti'];

		if (!is_array($op['ti'])) {
			$op['msg'] = $ti->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		if (!count($op['ti'])){	$op['ti_NONE'] = "1";		}

		

//-----------------------------------------------------------------------
			// 相片的URL決定
				$style_dir	= "./picture/";  
				$no_img		= "./images/graydot.jpg";
			if(file_exists($style_dir.$wi['style_code'].".jpg")){
				$wi['pic_url'] = $style_dir.$wi['style_code'].".jpg";
			} else {
				$wi['pic_url'] = $no_img;
			}
		
		//  取出主料記錄 ----------------------------------------------------
		$where_str = " WHERE smpl_code = '".$wi['style_code']."' ";
		$lots = $smpl_lots->search(0,$where_str);  //取出該筆 樣本主料記錄
		$op['lots_use'] = $lots;

		if (!is_array($op['lots_use'])) {
			$op['msg'] = $smpl_lots->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		if (!count($op['lots_use'])){	$op['lots_NONE'] = "1";		}

		//  取出副料料記錄 ---------------------------------------------------
		$where_str = " WHERE smpl_code = '".$wi['style_code']."' ";
		$acc = $smpl_acc->search(0,$where_str);  //取出該筆 樣本主料記錄		
		$op['acc_use'] = $acc;

		if (!is_array($op['acc_use'])) {     
			$op['msg'] = $smpl_acc->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

//---------------------------------- 資料庫作業結束 ------------------------------

include_once($config['root_dir']."/lib/class.pdf_wi.php");

$print_title = "Working Instruction";
$wi['cfm_date']=substr($wi['cfm_date'],0,10);
$print_title2 = "VER.".$wi['revise']."  on  ".$wi['cfm_date'];
$creator = $wi['confirmer'];

$pdf=new PDF_bom();
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
// [表匡] 訂單基本資料
$pdf->hend_title($PHP_id,$wi,$smpl,$op['total'],'Order#');

		$Y = $pdf->getY();

		$img_size = GetImageSize($wi['pic_url']);
		
		if ($img_size[0] > $img_size[1])
		{
			$pdf->Image($wi['pic_url'],10,28,47,0);
		}else{
			$pdf->Image($wi['pic_url'],10,28,0,47);
		}

		$pdf->ln();
		$Y = $pdf->getY();
		$X = $pdf->getX();
// 設定 colorway的數量表 - 欄位的寬  [size breakdown]
// 由 80~200 保留 30為 colorway內容 其它 90分配給數量欄
	$num_siz = count($sizing);
	$w_qty = intval(155/($num_siz+1));
	$w = array('40');  // 第一格為 40寬
	for ($i=0;$i<=$num_siz;$i++){
		array_push($w, $w_qty);
	}
	$xic = 0;
	for($i=0; $i<sizeof($p_etd); $i++)
	{
		$header = array_merge(array('Colorway') , $sizing);	
		$table_title = "Size Breakdown";
		if(sizeof($p_etd) > 1)$table_title .= '[ ETD : '.$p_etd[$i].' ]';
		$R = 100 ;
		$G = 85 ;
		$B = 85 ;
		$Y = 70+($i*(sizeof($data[$i])+1)*8);
		if (count($sizing)){
			$pdf->Table_1(10,$Y,$header,$data[$i],$w,$table_title,$R,$G,$B,$size_A['base_size']);
		}else{
			$pdf->Cell(10,70,'there is no sizing data exist !',1);
		}
		//$pdf->ln();
		if (sizeof($data) < 3)		
			$xic+=1;
		else
			$xic+=sizeof($data)-2;			
	}
	$xic+=sizeof($p_etd)-1;
		$Y =$pdf->getY();
		// 定訂 圖以下的座標 
		$pdf->SetXY($X,$Y+5);

// 主料
		$pdf->SetFont('Big5','',10);

	// 主料抬頭
if (count($lots)){
	$pdf->setx(10);
	$pdf->SetFont('Arial','B',14);
	$pdf->Cell(190,7,'Fabrics',0,1,'L');
	$pdf->Table_fab_title();
	$xic++;
	for ($i=0; $i<sizeof($lots); $i++)
	{
		if ($xic > 28)
		{
		$pdf->AddPage();
		$pdf->SetFont('Arial','B',14);
		// [表匡] 訂單基本資料
		$pdf->hend_title($PHP_id,$wi,$smpl,$op['total'],'Order#');		
		if ($img_size[0] > $img_size[1])
		{
			$pdf->Image($wi['pic_url'],10,28,47,0);
		}else{
			$pdf->Image($wi['pic_url'],10,28,0,47);
		}
		$pdf->ln();
		$pdf->ln();
		$pdf->ln();
		$pdf->setx(10);
		$pdf->SetFont('Arial','B',14);
		$pdf->Cell(190,7,'Fabrics',0,1,'C');
		$pdf->Table_fab_title();
		$pdf->SetFont('Big5','',10);
		$xic=2;
		$pdf->ln();
		}
		$xic=$xic+2;
		$pdf->Table_fab($lots[$i],$i);
		
		
	}

} // if $num_colors....

	$pdf->ln();

	// 副料抬頭
if (count($acc)){
	$pdf->setx(10);
	$pdf->SetFont('Arial','B',14);
	$pdf->cell(190,7,'Accessories',0,1,'L');
	$pdf->Table_acc_title('');		
	$pdf->ln();	
	$xic=$xic+5;
	for( $i=0; $i<sizeof($acc); $i++)
	{
		if ($xic > 28)
		{
			$pdf->AddPage();
			$pdf->SetFont('Arial','B',14);
			// [表匡] 訂單基本資料
			$pdf->hend_title($PHP_id,$wi,$smpl,$op['total'],'Order#');		
			if ($img_size[0] > $img_size[1])
			{
				$pdf->Image($wi['pic_url'],10,28,47,0);
			}else{
				$pdf->Image($wi['pic_url'],10,28,0,47);
			}
			$pdf->ln();
			$pdf->ln();
			$pdf->ln();
			$pdf->setx(10);
			$pdf->SetFont('Arial','B',14);
			$pdf->cell(190,7,'Accessories',0,1,'C');
			$pdf->SetFont('Arial','B',10);
			$pdf->Table_acc_title('');
			$pdf->SetFont('Big5','',10);
			$pdf->ln();
			$xic=3;
		}	
		$pdf->Table_acc($acc[$i],$i);
		$xic=$xic+2;					
		$pdf->ln();
	}

	
} // if $num_colors....


$name=$wi['wi_num'].'_wi.pdf';
$pdf->Output($name,'D');
break;			


//=======================================================
case "revise_wi":
check_authority("019","edit");
if(isset($PHP_p_id))$_SESSION['partial_id'] = $PHP_p_id;	//order_partial的ID

//刪除Bom相關資料
$f1=$bom->del_lots($PHP_id,1);
$f2=$bom->del_acc($PHP_id,1);

// 將 製造令 完整show out ------
//  wi 主檔 抓出來		
if(!$op = $wi->get_all($PHP_id)){    //取出該筆 製造令記錄
	$op['msg']= $wi->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}

//revise次數等輸入		
$parm['date'] 		= '0000-00-00 00:00:00';
$parm['user'] 		= "";
$parm['rev'] 		= $op['wi']['revise']+1;
$parm['id'] 		= $op['wi']['id'];
$parm['order_num'] 	= $op['wi']['smpl_id'];


if(!$T_updt =	$wi->revise($parm)){    
	$op['msg']= $wi->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}		

$back_str = "wi.php?&PHP_action=wi_edit&PHP_id=".$PHP_id."&PHP_style_code=".$op['wi']['smpl_id'];			
redirect_page($back_str);
break;			

//		
//		//檢視是否可以修改size(即是否存在colorway)
//		if ($wi->check_size($op['wi']['style_code']))
//		{
//			$op['size_edit']=1;
//		}
//
//		//  wi_qty 數量檔 -----------------------------------------------------------------
//		$where_str = " WHERE wi_id='".$PHP_id."' AND p_id = '".$_SESSION['partial_id']."'";
//		$T_wiqty = $wiqty->search(1,$where_str);
//
//		// 取出 size_scale --------
//		$size_A = $size_des->get($op['smpl']['size']);
//		$op['smpl']['size_scale']=$size_A['size_scale'];
//			//????????????????????????????????????? 注意 有時沒有 size type 資料時??????
//		$sizing = explode(",", $size_A['size']);
//
//
////search條件記錄			
//
//		// 做出 size table-------------------------------------------
//			$op['show_del_breakdown'] = show_del_breakdown($T_wiqty,$sizing,$PHP_back_str2,$size_A['base_size']);
//			$op['edit_breakdown'] = edit_breakdown($sizing,$PHP_back_str2,$size_A['base_size']);
//
//			$op['msg'] = $wi->msg->get(2);
//
//			// 做出 unit的 combo
//		$op['unit'] = $arry2->select($APPAREL_UNIT,$op['wi']['unit'],'PHP_unit','select','');  	
//
//		// creat sample type combo box
//		$sample_def = $smpl_type->get_fields('smpl_type');   // 取出 樣本類別
//		$op['smpl_type'] =  $arry2->select($sample_def,$op['wi']['smpl_type'],'PHP_smpl_type','select','');  	
//			
//		$op['msg'] = $wi->msg->get(2);
//		
//		$op['new_revise'] = $op['wi']['revise']+1;
//
//		page_display($op, "019", $TPL_WI_REVISE);
//		break;


//=======================================================
case "rev_wi_with_po":
check_authority("019","edit");

if(isset($PHP_p_id))$_SESSION['partial_id'] = $PHP_p_id;	//order_partial的ID

// revise次數等輸入		
$parm['rev'] 		= $PHP_rev;
$parm['id'] 		= $PHP_id;
$parm['order_num'] 	= $PHP_wi_num;
if(!$T_updt =	$wi->revise_with_po($parm)){    
	$op['msg']= $wi->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}

// 刪除Bom相關資料
// $f1=$bom->del_lots($PHP_id,2);
// $f2=$bom->del_acc($PHP_id,2);

// Disable BOM 相關資料
// $bom_rev = $PHP_bom_rev - 1;			
// if($bom_rev == 0) $bom_rev = 1;
// $parm2 = array($PHP_id, 'dis_ver', $bom_rev, 'bom_lots');
// $f1=$bom->update_wi_field($parm2);
// $parm2 = array($PHP_id, 'dis_ver', $bom_rev, 'bom_acc');
// $f1=$bom->update_wi_field($parm2);



// 將 製造令 完整show out ------
//  wi 主檔 抓出來		
if(!$op = $wi->get_all($PHP_id)){    //取出該筆 製造令記錄
	$op['msg']= $wi->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}

// $f3=$po->change_pp($op['wi']['wi_num']);


// 檢視是否可以修改size(即是否存在colorway)
if ($wi->check_size($op['wi']['style_code'])) {
	$op['size_edit']=1;
}

//  wi_qty 數量檔 -----------------------------------------------------------------
$where_str = " WHERE wi_id='".$PHP_id."' AND p_id = '".$_SESSION['partial_id']."'";
$T_wiqty = $wiqty->search(1,$where_str);

// 取出 size_scale --------
$size_A = $size_des->get($op['smpl']['size']);
$op['smpl']['size_scale']=$size_A['size_scale'];

// ????????????????????????????????????? 注意 有時沒有 size type 資料時??????
$sizing = explode(",", $size_A['size']);

// 做出 size table-------------------------------------------
$op['show_del_breakdown'] = show_del_breakdown($T_wiqty,$sizing,$PHP_back_str2,$size_A['base_size']);
$op['edit_breakdown'] = edit_breakdown($sizing,$PHP_back_str2,$size_A['base_size']);
$op['msg'] = $wi->msg->get(2);

// 做出 unit的 combo
$op['unit'] = $arry2->select($APPAREL_UNIT,$op['wi']['unit'],'PHP_unit','select','');  	

// creat sample type combo box
$sample_def = $smpl_type->get_fields('smpl_type');   // 取出 樣本類別
$op['smpl_type'] =  $arry2->select($sample_def,$op['wi']['smpl_type'],'PHP_smpl_type','select','');  	
$op['msg'] = $wi->msg->get(2);
$op['new_revise'] = $op['wi']['revise'];

page_display($op, "019", $TPL_WI_REVISE);
break;

//=======================================================
		case "do_wi_revise_show":


		check_authority("019","edit");
		$argv = array(	"id"			=>  $PHP_wi_id,
						"wi_num"		=>	$PHP_wi_num,
						"smpl_id"		=>	$PHP_smpl_id,
						"version"		=>	$PHP_version,
						"etd"				=>	$PHP_etd,
						
						"unit"			=>	$PHP_unit,
						"updator"		=>	$GLOBALS['SCACHE']['ADMIN']['login_id']
			);

	if(!$wi->check2($argv)){    // 輸入不正確--- 輸入項 有誤 [ 日期 ]
		// 將 製造令 完整show out ---------------------------------------------
		//  wi 主檔 抓出來 ----------------------------------------------------
		if(!$op = $wi->get_all($PHP_wi_id)){    //取出該筆 製造令記錄
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}

		//  wi_qty 數量檔 -----------------------------------------------------------------
		$where_str = " WHERE wi_id='".$PHP_wi_id."' ";
		$T_wiqty = $wiqty->search(1,$where_str);

		// 取出 size_scale --------
		$size_A = $size_des->get($op['smpl']['size']);
		$op['smpl']['size_scale']=$size_A['size_scale'];
			//????????????????????????????????????? 注意 有時沒有 size type 資料時??????
		$sizing = explode(",", $size_A['size']);

//------------search條件記錄
		$op['back_str']=$PHP_back_str;
		$op['back_str2']=$PHP_back_str2;

			// 做出 size table-------------------------------------------
			$op['show_del_breakdown'] = show_del_breakdown($T_wiqty,$sizing,$PHP_back_str2,$size_A['base_size']);
			$op['edit_breakdown'] = edit_breakdown($sizing,$PHP_back_str2,$size_A['base_size']);
		
			$op['msg'] = $wi->msg->get(2);

			// 做出 unit的 combo
		$op['unit'] = $arry2->select($APPAREL_UNIT,$op['wi']['unit'],'PHP_unit','select','');  	

		// creat sample type combo box
		$sample_def = $smpl_type->get_fields('smpl_type');   // 取出 樣本類別
		$op['smpl_type'] =  $arry2->select($sample_def,$op['wi']['smpl_type'],'PHP_smpl_type','select','');  	
		$op['new_revise'] = $op['wi']['revise'];		
		page_display($op, 3, 1, $TPL_WI_REVISE); 
		break;		
	}	// 輸入不正確--輸入項 有誤 [ 日期 ]- (((結束)))


// UPDATE  wi 資料表內[etd],[smpl_type][unit] 三個欄 且將version 加一
		//size修改
		$size_id=$PHP_Size_id;
		if ($PHP_Size_add==1)
		{
			if ($PHP_Size_id==0)
			{
				$PHP_Size_item = strtoupper($PHP_Size_item);
				$PHP_Size = strtoupper($PHP_Size);
				$PHP_base_size = strtoupper($PHP_base_size);
				$parm=array(	'cust'			=>	$PHP_cust,
								'dept'			=>	$PHP_dept_code,
								'size'			=>	$PHP_Size_item,
								'size_scale'	=>	$PHP_Size,
								'base_size'		=>	$PHP_base_size,
								'check'			=>	1,
				);
				$size_id=$size_des->add($parm);				
			}
			$f1 = $order->update_field('size', $size_id, $PHP_smpl_id);
		}

		
		$updt = array(	"etd"			=>	$argv['etd'],
						"unit"			=>	$argv['unit'],
						"version"		=>	$argv['version'],
						"updator"		=>	$argv['updator'],
						"id"			=>	$argv['id']
			);
				if(!$wi->edit($updt)){
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
				}

		//--------------- 完成修改寫入------------------

		//--------------- 列出改後之記錄------------------
		//  wi 主檔 --------------------------------------------------------------------------------
		if(!$op = $wi->get_all($argv['id'])){    //取出該筆 製造令記錄
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}
		

		//  wi_qty 數量檔 --------------------------------------------------------------------------
			$where_str = " WHERE wi_id='".$op['wi']['id']."' ";
		$T_wiqty = $wiqty->search(1,$where_str);

		$num_colors = count($T_wiqty);

		// 取出 size_scale -----------------------------------------------------
		$size_A = $size_des->get($op['smpl']['size']);
		$op['smpl']['size_scale']=$size_A['size_scale'];
			//????????????????????????????????????? 注意 有時沒有 size type 資料時??????
		$sizing = explode(",", $size_A['size']);

			// 做出 size table-------------------------------------------
		$reply = show_breakdown($T_wiqty,$sizing,$size_A['base_size']);
		$op['show_breakdown'] = $reply['html'];
		$op['total'] = $reply['total'];
		//-----------------------------------------------------------------

			# 記錄使用者動態
		$message = "Successfully update wi:[".$op['wi']['wi_num']."]";
		$log->log_add(0,"31E",$message);
		$op['msg'][] = $message;
 		$op['back_str']=$PHP_back_str;
 		$op['back_str2']=$PHP_back_str2;

		page_display($op, "019", $TPL_WI_REVISE_SHOW);
		break;

//=================================================2011/11/14
	case "search_smpl_ti_copy":
	
	$op['wi']=$smpl_wi->copy_ti_search($PHP_c_cust, $PHP_c_dept);

  $op['copy_search'] = "&PHP_smpl_id=".$PHP_smpl_id."&PHP_c_dept=".$PHP_c_dept."&PHP_c_cust=".$PHP_c_cust;
		if (isset($PHP_dept_code))
		{
			$PHP_back_str = "&PHP_sr_startno=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_ref=".$PHP_ref."&PHP_factory=".$PHP_factory."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh;
			$op['back_str']=$PHP_back_str;
			$PHP_back_str2 = "&PHP_sr=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_ref=".$PHP_ref."&PHP_factory=".$PHP_factory."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh;
			$op['back_str2']=$PHP_back_str2;

		}else{
			$op['back_str']="&PHP_sr_startno=&PHP_dept_code=&PHP_num=&PHP_cust=&PHP_ref=&PHP_factory=&PHP_etdstr=&PHP_etdfsh=";
			$op['back_str2']="&PHP_sr=&PHP_dept_code=&PHP_num=&PHP_cust=&PHP_ref=&PHP_factory=&PHP_etdstr=&PHP_etdfsh=";
			$PHP_back_str2=$op['back_str2'];
		}
	page_display($op, "010", $TPL_SMPL_TI_COPY_SEARCH2);
	break;

//========================================================2011/11/14
case "search_ti_copy":
	//echo $PHP_id." ".$PHP_c_cust." ".$PHP_c_dept;exit;
	$op['old_wi'] = $wi->get($PHP_id);
	$op['wi']=$wi->copy_ti_search($PHP_c_cust, $PHP_c_dept);

	$op['copy_search'] = "&PHP_smpl_id=".$PHP_smpl_id."&PHP_c_dept=".$PHP_c_dept."&PHP_c_cust=".$PHP_c_cust;
		if (isset($PHP_dept_code))
		{
			$PHP_back_str = "&PHP_sr_startno=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_ref=".$PHP_ref."&PHP_factory=".$PHP_factory."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh;
			$op['back_str']=$PHP_back_str;
			$PHP_back_str2 = "&PHP_sr=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_ref=".$PHP_ref."&PHP_factory=".$PHP_factory."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh;
			$op['back_str2']=$PHP_back_str2;

		}else{
			$op['back_str']="&PHP_sr_startno=&PHP_dept_code=&PHP_num=&PHP_cust=&PHP_ref=&PHP_factory=&PHP_etdstr=&PHP_etdfsh=";
			$op['back_str2']="&PHP_sr=&PHP_dept_code=&PHP_num=&PHP_cust=&PHP_ref=&PHP_factory=&PHP_etdstr=&PHP_etdfsh=";
			$PHP_back_str2=$op['back_str2'];
		}
	page_display($op, "010", $TPL_ORD_TI_COPY_SEARCH);
	break;

//==============================================2011/11/14
	case "ti_copy_view":

		check_authority('021',"view");
		$_SESSION['partial_id'] = '';
		// 將 製造令 完整show out ------
		//  wi 主檔
		
		if(!$op = $wi->get_all($PHP_id)){    //取出該筆 製造令記錄
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}

		$op['old_id'] = $PHP_old_id;
//--------------------------------------------------------------------------
		// 取出 size_scale ----------------------------------------------
		$size_A = $size_des->get($op['smpl']['size']);
		$op['smpl']['size_scale']=$size_A['size_scale'];
			//????????????????????????????????????? 注意 有時沒有 size type 資料時??????
		$sizing = explode(",", $size_A['size']);

		//  wi_qty 數量檔			
			$where_str2 = " WHERE ord_num = '".$op['wi']['wi_num']."' ORDER BY p_etd";
			$p_id = $order->get_fields('id', $where_str2,'order_partial');
			$p_etd = $order->get_fields('p_etd', $where_str2,'order_partial');
			
			$op['total'] =0; $op['g_ttl'] = 0;
			for($i=0; $i<sizeof($sizing); $i++)$op['size_ttl'][$i]=0;
			$etd = 0;
			for($i=0; $i<sizeof($p_id); $i++)
			{
				$where_str3 =" WHERE wi_id='".$op['wi']['id']."'  AND p_id='".$p_id[$i]."'";
				$T_wiqty = $wiqty->search(1,$where_str3);
				$num_colors = count($T_wiqty);
				// 做出 size table-------------------------------------------
				$reply = show_breakdown($T_wiqty,$sizing,$size_A['base_size'],$p_etd[$i]);
				$op['show_breakdown'][$i] = $reply['html'];
				$op['total'] += $reply['total'];
				for($j=0; $j<sizeof($sizing); $j++)
				{
					$op['g_ttl'] += $reply['size_ttl'][$j];				
					$op['size_ttl'][$j] +=$reply['size_ttl'][$j];
				}
				$etd = $etd > $p_etd[$i] ? $etd : $p_etd[$i] ;
			}
		$op['wi']['etd'] = $etd;
		//-----------------------------------------------------------------

		if(count($op['ti']))
		{
		 for ($i=0; $i<sizeof($op['ti']); $i++)
			$op['ti'][$i]['detail'] = str_replace( chr(13).chr(10), "<br>", $op['ti'][$i]['detail'] );
		}
		
		if(isset($op['sub_ti']))
		{
		 for ($i=0; $i<sizeof($op['sub_ti']); $i++)
		  for($j=0; $j<sizeof($op['sub_ti'][$i]['txt']); $j++)
				$op['sub_ti'][$i]['txt'][$j]['detail'] = str_replace( chr(13).chr(10), "<br>", $op['sub_ti'][$i]['txt'][$j]['detail'] );
		}				
	
	//檔案列表
		$op['done'] = $fils->get_file($op['wi']['wi_num']);
		
		$op['msg'] = $ti->msg->get(2);
		if(isset($PHP_msg)) $op['msg'][] = $PHP_msg;
		$PHP_back_str = "&PHP_sr_startno=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_etdfsh=".$PHP_etdfsh."&PHP_cust=".$PHP_cust."&PHP_wi_num=".$PHP_wi_num."&PHP_etdstr=".$PHP_etdstr."&PHP_fty_sch=".$PHP_fty_sch;
		$op['back_str']=$PHP_back_str;
		//$PHP_back_str2 = "&PHP_sr=".$PHP_sr."&PHP_c_dept=".$op['wi']['dept']."&PHP_c_cust=".$op['wi']['cust']."&PHP_etdfsh=".$PHP_etdfsh."&PHP_cust=".$PHP_cust."&PHP_wi_num=".$PHP_wi_num."&PHP_etdstr=".$PHP_etdstr."&PHP_fty_sch=".$PHP_fty_sch;
		$PHP_back_str2="&PHP_id=".$PHP_old_id."&PHP_c_dept=".$op['wi']['dept']."&PHP_c_cust=".$op['wi']['cust']."&PHP_fty_sch=".$PHP_fty_sch;
		
		$op['back_str2']=$PHP_back_str2;

		page_display($op, "010", $TPL_ORD_TI_COPY_VIEW);			
		break;
		
//==================================================2011/11/14
    case "do_ti_copy":
		//echo "here ".$PHP_id." ".$PHP_smpl_id;exit;
		check_authority('021',"add");
		// 將 製造令 完整show out ------
		//  wi 主檔
		
		$f1=$ti->del_wi_id($PHP_smpl_id,0);	//COPY動作執行前,先把之前的WS刪除,避免重覆資料
		$f1=$wi->copy_ti($PHP_id,$PHP_smpl_id);

		$op=$wi->get_all($PHP_smpl_id);
		$message = "Add WI:[".$op['wi']['wi_num']."] Description Copy。";
		$log->log_add(0,"34C",$message);
		$sub_ti = array();
		$redir_str=$PHP_SELF.'?PHP_action=ti_add&PHP_id='.$PHP_smpl_id.$PHP_back_str2.'&PHP_msg='.$message;
		
		redirect_page($redir_str);
		break;

//=============================================2011/11/14
    case "ti_add":
		check_authority('021',"add");
		// 將 製造令 完整show out ------
		//  wi 主檔
		if(!$op = $wi->get_all($PHP_id)){    //取出該筆 製造令記錄
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}

//--------------------------------------------------------------------------
		// 取出 size_scale ----------------------------------------------
		$size_A = $size_des->get($op['smpl']['size']);
		$op['smpl']['size_scale']=$size_A['size_scale'];
			//????????????????????????????????????? 注意 有時沒有 size type 資料時??????
		$sizing = explode(",", $size_A['size']);


		//  wi_qty 數量檔			
			$where_str2 = " WHERE ord_num = '".$op['wi']['wi_num']."' ORDER BY p_etd";
			$p_id = $order->get_fields('id', $where_str2,'order_partial');
			$p_etd = $order->get_fields('p_etd', $where_str2,'order_partial');
			$op['total'] =0; $op['g_ttl'] = 0;
			for($i=0; $i<sizeof($sizing); $i++)$op['size_ttl'][$i]=0;
			$etd = 0;
			for($i=0; $i<sizeof($p_id); $i++)
			{
				$where_str3 =" WHERE wi_id='".$op['wi']['id']."'  AND p_id='".$p_id[$i]."'";
				$T_wiqty = $wiqty->search(1,$where_str3);
				$num_colors = count($T_wiqty);
				// 做出 size table-------------------------------------------
				$reply = show_breakdown($T_wiqty,$sizing,$size_A['base_size'],$p_etd[$i]);
				$op['show_breakdown'][$i] = $reply['html'];
				$op['total'] += $reply['total'];
				for($j=0; $j<sizeof($sizing); $j++)
				{
					$op['g_ttl'] += $reply['size_ttl'][$j];				
					$op['size_ttl'][$j] +=$reply['size_ttl'][$j];
				}
			$etd = $etd > $p_etd[$i] ? $etd : $p_etd[$i] ;
			}
		$op['wi']['etd'] = $etd;
		//-----------------------------------------------------------------
		
	//檔案列表
		$op['done'] = $fils->get_file($op['wi']['wi_num']);

		if(!isset($PHP_msg) || $PHP_msg == '') $sub_ti = array();
		if(is_array($sub_ti) && sizeof($sub_ti) > 0) $op['sub_ti'] =  $sub_ti;




		if(!isset($op['sub_ti']))
		{
				 $op['sub_ti'][0]['name'] = 'main';
				 $op['sub_ti'][0]['i'] = 0;
				 $op['sub_ti'][0]['new_ti'] = 1;

			for($i = 0; $i<sizeof($ti_style[$op['smpl']['style']]); $i++)
			{
				$op['sub_ti'][0]['txt'][$i]['id'] = $i;
				$op['sub_ti'][0]['txt'][$i]['item'] = $ti_style[$op['smpl']['style']][$i];
				$op['sub_ti'][0]['txt'][$i]['detail'] = '';
			}		
		}

		if(sizeof($sub_ti) == 0) $sub_ti = $op['sub_ti'];


		
		$op['msg'] = $ti->msg->get(2);
		if(isset($PHP_msg)) $op['msg'][] = $PHP_msg;

		if (isset($PHP_c_dept))
		{
				$PHP_back_str = "&PHP_sr_startno=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_etdfsh=".$PHP_etdfsh."&PHP_cust=".$PHP_cust."&PHP_wi_num=".$PHP_wi_num."&PHP_etdstr=".$PHP_etdstr."&PHP_fty_sch=".$PHP_fty_sch;
				$op['back_str']=$PHP_back_str;
				$PHP_back_str2 = "&PHP_sr=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_etdfsh=".$PHP_etdfsh."&PHP_cust=".$PHP_cust."&PHP_wi_num=".$PHP_wi_num."&PHP_etdstr=".$PHP_etdstr."&PHP_fty_sch=".$PHP_fty_sch;
				$op['back_str2']=$PHP_back_str2;
		}else{
				$op['back_str']=$PHP_back_str;
				$op['back_str2']=$PHP_back_str2;				
		}
		page_display($op, '021', $TPL_TI_ADD);
		break;

//===========================================2011/11/14
    case "do_ti_other_add":
		check_authority("010","add");

		$sub_ti = array();
		for($i=0; $i<sizeof($PHP_detail); $i++)
		{
			 $sub_ti[$i]['name'] = $PHP_sub_name[$i];
			 $sub_ti[$i]['i'] = $i;
			 $sub_ti[$i]['new_ti'] = $PHP_new_ti[$i];
			 $j=0;
			foreach($PHP_detail[$i] as $key => $value)
			{
				$sub_ti[$i]['txt'][$j]['id'] = $key;
				$sub_ti[$i]['txt'][$j]['item'] = $PHP_item[$i][$key];
				$sub_ti[$i]['txt'][$j]['detail'] = $value;
				$j++;
			}
		}



   if($PHP_other_name)
   {
		 	$sub_ti[$i]['name'] = $PHP_other_name;
		 	$sub_ti[$i]['i'] = $i;
		 	$sub_ti[$i]['new_ti'] = 1;
		 	for($j = 0; $j<sizeof($ti_style[$PHP_style]); $j++)
		 	{
				$sub_ti[$i]['txt'][$j]['id'] = $j;
				$sub_ti[$i]['txt'][$j]['item'] = $ti_style[$PHP_style][$j];
				$sub_ti[$i]['txt'][$j]['detail'] = '';
			}				
			$message = "Add WI :[".$PHP_wi_num."] NEW GROUP [".$PHP_other_name."] 。";
		}else{
			$message = "Please input new gorup name";
		}


// 將 製造令 完整show out ------		
		$redir_str=$PHP_SELF.'?PHP_action=ti_add&PHP_id='.$PHP_wi_id.$PHP_back_str2.'&PHP_msg='.$message;
		redirect_page($redir_str);
		break;
		
//=====================================2011/11/14
    case "do_ti_add":
		check_authority('021',"add");
		
		for($i=0; $i<sizeof($PHP_detail); $i++)
		{		
			
			if($PHP_new_ti[$i] == 1)
			{
				foreach($PHP_detail[$i] as $key => $value)
				{
					if(strstr($value,'&#')) $value = $ch_cov->check_cov($value);			
					if(strstr($PHP_sub_name[$i],'&#')) $PHP_sub_name[$i] = $ch_cov->check_cov($PHP_sub_name[$i]);	
		
					$parm = array(	"wi_id"			=>	$PHP_wi_id,
													"item"			=>	$PHP_item[$i][$key],
													"detail"		=>	$value,
													'today'			=>	date('Y-m-d'),
													'times'			=>	date('H:i:s'),
													'ti_new'		=> '1',
													'sub_item'	=>	$PHP_sub_name[$i]
											 );

					$f1 = $ti->add($parm);
				}
		
			}else{
				foreach($PHP_detail[$i] as $key => $value)
				{
					if(strstr($value,'&#')) $value = $ch_cov->check_cov($value);
							
					$parm['field_name'] = 'detail';
					$parm['field_value'] = $value;
					$parm['id'] = $key;
					$f1 = $ti->update_field($parm);
				}
			}
		}	


// 將 製造令 完整show out ------
		$message = "Add WI:[".$PHP_num."] Description 。";
		$log->log_add(0,"23D",$message);

		$redir_str='wi.php?PHP_action=ti_view&PHP_id='.$PHP_wi_id.$PHP_back_str2.'&PHP_msg='.$message;
		
		redirect_page($redir_str);
		break;

//================================================2011/11/14
    case "ti_view":
	
		check_authority('021',"view");
		$_SESSION['partial_id'] = '';
		// 將 製造令 完整show out ------
		//  wi 主檔
		
		if(!$op = $wi->get_all($PHP_id)){    //取出該筆 製造令記錄
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}

//--------------------------------------------------------------------------
		// 取出 size_scale ----------------------------------------------
		$size_A = $size_des->get($op['smpl']['size']);
		$op['smpl']['size_scale']=$size_A['size_scale'];
			//????????????????????????????????????? 注意 有時沒有 size type 資料時??????
		$sizing = explode(",", $size_A['size']);

		//  wi_qty 數量檔			
			$where_str2 = " WHERE ord_num = '".$op['wi']['wi_num']."' ORDER BY p_etd";
			$p_id = $order->get_fields('id', $where_str2,'order_partial');
			$p_etd = $order->get_fields('p_etd', $where_str2,'order_partial');
			
			$op['total'] =0; $op['g_ttl'] = 0;
			for($i=0; $i<sizeof($sizing); $i++)$op['size_ttl'][$i]=0;
			$etd = 0;
			for($i=0; $i<sizeof($p_id); $i++)
			{
				$where_str3 =" WHERE wi_id='".$op['wi']['id']."'  AND p_id='".$p_id[$i]."'";
				$T_wiqty = $wiqty->search(1,$where_str3);
				$num_colors = count($T_wiqty);
				// 做出 size table-------------------------------------------
				$reply = show_breakdown($T_wiqty,$sizing,$size_A['base_size'],$p_etd[$i]);
				$op['show_breakdown'][$i] = $reply['html'];
				$op['total'] += $reply['total'];
				for($j=0; $j<sizeof($sizing); $j++)
				{
					$op['g_ttl'] += $reply['size_ttl'][$j];				
					$op['size_ttl'][$j] +=$reply['size_ttl'][$j];
				}
				$etd = $etd > $p_etd[$i] ? $etd : $p_etd[$i] ;
			}
		$op['wi']['etd'] = $etd;
		//-----------------------------------------------------------------

		if(count($op['ti']))
		{
		 for ($i=0; $i<sizeof($op['ti']); $i++)
			$op['ti'][$i]['detail'] = str_replace( chr(13).chr(10), "<br>", $op['ti'][$i]['detail'] );
		}
		
		if(isset($op['sub_ti']))
		{
		 for ($i=0; $i<sizeof($op['sub_ti']); $i++)
		  for($j=0; $j<sizeof($op['sub_ti'][$i]['txt']); $j++)
				$op['sub_ti'][$i]['txt'][$j]['detail'] = str_replace( chr(13).chr(10), "<br>", $op['sub_ti'][$i]['txt'][$j]['detail'] );
		}				
	
	//檔案列表
		$op['done'] = $fils->get_file($op['wi']['wi_num']);
		
		$op['msg'] = $ti->msg->get(2);
		if(isset($PHP_msg)) $op['msg'][] = $PHP_msg;
		$PHP_back_str = "&PHP_sr_startno=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_etdfsh=".$PHP_etdfsh."&PHP_cust=".$PHP_cust."&PHP_wi_num=".$PHP_wi_num."&PHP_etdstr=".$PHP_etdstr."&PHP_fty_sch=".$PHP_fty_sch;
		$op['back_str']=$PHP_back_str;
		$PHP_back_str2 = "&PHP_sr=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_etdfsh=".$PHP_etdfsh."&PHP_cust=".$PHP_cust."&PHP_wi_num=".$PHP_wi_num."&PHP_etdstr=".$PHP_etdstr."&PHP_fty_sch=".$PHP_fty_sch;
		$op['back_str2']=$PHP_back_str2;
		
		page_display($op, '021', $TPL_TI_VIEW);
		break;

//=================================================2011/11/14
	case "do_ti_cfm":
		check_authority('021',"view");
	
		$parm = array($PHP_id,'ti_cfm',$TODAY);
		
		$f1 = $wi->update_field($parm);
		$parm = array($PHP_id,'ti_cfm_user',$GLOBALS['SCACHE']['ADMIN']['login_id']);
		$f1 = $wi->update_field($parm);

		if (!$f1) {
			$op['msg']= $wi->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}	

		if ($PHP_revise == 0)
		{
				$parm=array($PHP_id,'ti_rev','1');
				$wi->update_field($parm);
		}
	
		$message="success CFM Worksheet:[".$PHP_wi."]";
		$log->log_add(0,"34A",$message);

//傳送message給RD人員
	$rcd = $wi->get_all($PHP_id);
	if($rcd['wi']['status'] > 0 && $rcd['wi']['ti_cfm'] <> '0000-00-00' )
	{
	 if($PHP_fty == 'WX') $PHP_fty = 'HJ';
	 $messg  = ' 製造令(Working Instruction) : <a href=index2.php?PHP_action=wi_view&PHP_id='.$PHP_id.'&PHP_dept_code=&PHP_num=&PHP_cust=&PHP_etdstr=&PHP_etdfsh=&PHP_sch_fty=&PHP_un_fhs=&PHP_sr=1>'.$PHP_wi.'</a>';
 	 $messg .= '製造說明 (WorkSheet) : <a href=index2.php?PHP_action=ti_view&PHP_id='.$PHP_id.$PHP_back_str2.'&PHP_ti=1>'.$PHP_wi.'</a>';
	 $notify->system_msg_send_with_mail('3-3-S',$PHP_fty,$PHP_wi,$messg);
	}
		
		$redir_str=$PHP_SELF.'?PHP_action=ti_view&PHP_id='.$PHP_id.$PHP_back_str2.'&PHP_ti=1&PHP_msg='.$message;
		redirect_page($redir_str);
		break;

//==================================================2011/11/14
	case "ti_search_wi":   //  先找出製造令.......

		check_authority('021',"view");

			if (!$op = $wi->search(1,0)) {
				$op['msg']= $wi->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			// op 被清除了 需再判斷一次 

		//2006/05/02 update
		$op['dept_id'] = get_dept_id();
		
		// 如果是 manager 進入時...
		if (substr($op['dept_id'],0,7) == "<select"){
			$op['manager_flag'] = 1;
		}
// print_r($op);
		$op['msg']= $wi->msg->get(2);				
		$back_str="&PHP_dept_code=".$PHP_dept_code."&PHP_etdfsh=".$PHP_etdfsh."&PHP_cust=".$PHP_cust."&PHP_wi_num=".$PHP_wi_num."&PHP_etdstr=".$PHP_etdstr."&PHP_fty_sch=".$PHP_fty_sch;
		$op['back_str']=$back_str;

		page_display($op, '021', $TPL_TI);
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "upload_order_file":	2011/11/14
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "upload_order_file":
	
		check_authority('021',"add");

		if(strstr($PHP_desp,'&#'))	$PHP_des = $ch_cov->check_cov($PHP_desp);
		
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
																		
		if ($_FILES['PHP_pttn']['size'] < $max_file_size && $_FILES['PHP_pttn']['size'] > 0)
		{
		
			if ($f_check == 1){   // 上傳檔的副檔名為 mdl 時 -----

				// upload pattern file to server
				$today = $GLOBALS['TODAY'];
				$user_name =  $GLOBALS['SCACHE']['ADMIN']['name'];
				$parm = array(	"file_name"		=>  $PHP_num,				
								"num"			=>  $PHP_num,
								"file_des"		=>	$PHP_desp,
								"file_user"		=>	$user_name,
								"file_date"		=>	$today
				);

				$A = $fils->get_name_id('file_det');			
				$pttn_name = $PHP_num."_".$A.".".$ext;  // 組合檔名
				$parm['file_name'] = $pttn_name;
				
				$str_long=strlen($pttn_name);
				$upload = new Upload;
				
				$upload->setMaxSize($max_file_size);
				$upload->uploadFile(dirname($PHP_SELF).'/wi_file/', 'other', 16, $pttn_name );
				$upload->setMaxSize($max_file_size);
				if (!$upload){
					$op['msg'][] = $upload;
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
				}
				if (!$A = $fils->upload_file($parm)){
					$op['msg'] = $fils->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
				}
				
				//傳送message給相關人員
				if($PHP_fty == 'WX') $PHP_fty = 'HJ';
				$messg  = ' 製造令(Working Instruction) : <a href=index2.php?PHP_action=wi_view&PHP_id='.$PHP_id.'&PHP_dept_code=&PHP_num=&PHP_cust=&PHP_etdstr=&PHP_etdfsh=&PHP_sch_fty=&PHP_un_fhs=&PHP_sr=1>'.$PHP_num.'</a>';
				$messg .= ' 製造說明 (WorkSheet) : <a href=index2.php?PHP_action=ti_view&PHP_id='.$PHP_id.$PHP_back_str2.'&PHP_ti=1>'.$PHP_num.'</a>';
				$messg .= ' <BR>已上傳新檔案：'.$pttn_name.'，檔案描述：'.$PHP_desp;
				$notify->system_msg_send_with_mail('3-3-F',$PHP_fty,$PHP_num,$messg);
					
				$message = "UPLOAD file of ".$PHP_num;
				$log->log_add(0,"31E",$message);
			} else {  // 上傳檔的副檔名  是  exe 時 -----

				$message = "upload file is incorrect format ! Please re-send.";
			}
		}else{  //上傳檔名重覆時
			$message = "Upload file is too big";
		}
	
		}else{
			$message="You don't pick any file or add file descript.";
		}	
			
//完成上傳檔案		

		$redir_str=$PHP_SELF.'?PHP_action=ti_view&PHP_id='.$PHP_id.$PHP_back_str2.'&PHP_msg='.$message;
		redirect_page($redir_str);
		break;	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "Revise_partial_qty":	2012/11/14Revise_partial_qty
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "Revise_partial_qty":
	
	//echo $_SESSION['partial_id'];
		if($_POST['PHP_ord_qty_allow']=='yes')
		{
			//print_r($_POST);
			//print_r($_GET);
			$o_su=$_POST['PHP_ord_ie'] * $_POST['PHP_ord_total_qty'] ;
			$p_su=$_POST['PHP_ord_ie'] * $_POST['PHP_this_partial_total'];
			$parm = array(	"order_id"  =>	$PHP_ord_id,
						"order_qty"		=>	$PHP_ord_total_qty,
						"order_su"		=>	$o_su,
						"partial_id"	=>	$_SESSION['partial_id'],
						"partial_qty"	=>	$PHP_this_partial_total,
						"partial_su"	=>	$p_su
					  );
			if($wi->revise_qty($parm))
			{
				$PHP_msg="Success!! Please try it again..";
			}
			$message="success edit order qty and SU on wi update:[".$_POST['PHP_ord_num']."]";
			$log->log_add(0,"020E",$message);	
			$message="success edit order partial qty and SU on partial update:[".$_POST['PHP_ord_num'].$_POST['PHP_this_partial_mks']."]";
			$log->log_add(0,"020E",$message);
			
			$back_str = "index2.php?PHP_action=wi_view&PHP_id=".$back_PHP_id."&PHP_cfm_view=1&PHP_msg=".$PHP_msg;
			echo $back_str;
			redirect_page($back_str);
			//$redir_str='index2.php?PHP_action=wi_view&PHP_id='.$PHP_id.'&PHP_cfm_view=1&PHP_msg='.$PHP_msg;
		}
		//$rcd = $wi->revise_qty($PHP_id);
		//$back_str = "wi.php?&PHP_action=wi_edit&PHP_id=".$f1."&PHP_style_code=".$parm['wi_num']."&PHP_msg=".$message;			
		//redirect_page($back_str);
		break;	
		
		
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	更新資料用，測試用(小錢)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "update_ie_su_cm":
	// error_reporting(1);
	$sel_sql="select id,order_num,qty,ie_time1,ie_time2,ie1,ie2,su,cm,fty_cm from s_order where etd like '2013%' and dept='DA' and factory='CF'";
	$all_data = $wi->sql_search($sel_sql);
	if($all_data)
	{
		$sql_array=array();
		foreach($all_data as $key=>$value)
		{
		
			// echo $value['ie_time1'].$value['ie_time2'].$value['ie1'].$value['ie2'].$value['qty'].$value['cm'].$value['fty_cm'].'<br>'; 
			$ie1 = (!empty($value['ie1']) && $value['ie1'] > 0 )? ( $value['cm'] / 2.45 ) : 0 ;
			$ie2 = (!empty($value['ie2']) && $value['ie2'] > 0 )? ( $value['fty_cm'] / 2.45 ) : 0 ;
			$ie_time1 = (!empty($value['ie_time1']))? $ie1 * 3000 : 0;
			$ie_time2 = (!empty($value['ie_time2']))? $ie2 * 3000 : 0;
			$su = $ie1* $value['qty'];
			
			$new_sql_str = " update s_order set `ie_time1` = '".$ie_time1."' , `ie_time2` = '".$ie_time2."' ,
			`ie1` = '".$ie1."' , `ie2` = '".$ie2."' , `su` = '".$su."'
			WHERE `id` = '".$value['id']."'
			";
			echo $new_sql_str.'<br>';
			
			//$sql_array[$key] = $new_sql_str;
			
			
			//echo $value['order_num'];
			//echo "<br>";
			// $new_ie=0;
			// echo $value['id']." ";
			// $init_sql=true;
			// if($value['fty_cm'] > 0)
			// {
				// echo "fty_cm ";
				// $new_ie = $value['fty_cm']/2.45;
				// if($value['ie1'] > 0)
				// {
					// if($init_sql)
					// {
						// $new_sql_str="update s_order set ";
						// $new_sql_str .= " ie1=".$new_ie ; 
						// $init_sql=false;
					// }
					// else
					// {
						// $new_sql_str .= " ,ie1=".$new_ie ; 
					// }
					
				// }
				// if($value['ie2'] > 0)
				// {
					// if($init_sql)
					// {
						// $new_sql_str="update s_order set ";
						// $new_sql_str .= " ie2=".$new_ie ; 
						// $init_sql=false;
					// }
					// else
					// {
						// $new_sql_str .= " ,ie2=".$new_ie ; 
					// }
				// }
				// $new_su = $new_ie*$value['qty'];
				// if($value['su'] > 0)
				// {
					// if($init_sql)
					// {
						// $new_sql_str="update s_order set ";
						// $new_sql_str .= " su=".$new_su ; 
						// $init_sql=false;
					// }
					// else
					// {
						// $new_sql_str .= " ,su=".$new_su ; 
					// }
				// }
				
			// }
			// else
			// {
				// echo "cm ie10000=".$value['ie1']." ";
				// $new_ie = $value['cm']/2.45;
				// if($value['ie1'] > 0 )
				// {
					// if($init_sql)
					// {
						// $new_sql_str="update s_order set ";
						// $new_sql_str .= " ie1999=".$new_ie ; 
						// $init_sql=false;
					// }
					// else
					// {
						// $new_sql_str .= " ,ie14444=".$new_ie ; 
					// }
					
				// }
				// if($value['ie2'] > 0 )
				// {
					// if($init_sql)
					// {
						// $new_sql_str="update s_order set ";
						// $new_sql_str .= " ie2999=".$new_ie ; 
						// $init_sql=false;
					// }
					// else
					// {
						// $new_sql_str .= " ,ie24444=".$new_ie ; 
					// }
				// }
				// $new_su = $new_ie*$value['qty'];
				// if($value['su'] > 0)
				// {
					// if($init_sql)
					// {
						// $new_sql_str="update s_order set ";
						// $new_sql_str .= " su=".$new_su ; 
						// $init_sql=false;
					// }
					// else
					// {
						// $new_sql_str .= " ,su=".$new_su ; 
					// }
				// }
				// echo " finish!!";
			// }
			// echo $new_sql_str;
			// echo "<br>";
		}
		//print_r($sql_array);
	}
	
	//print_r($all_data);
	exit;
	
	break;
//-------------------------------------------------------------------------

}   // end case ---------

?>
