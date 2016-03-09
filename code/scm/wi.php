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
#			index.php  �D�{��
#		for Carnival SCM [Sample]  management
#			Jack Yang     2004/11/10
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//		job 39  [�Ͳ��s�y][�Ͳ�����]
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
//			 job 31  �s�@�O
//-------------------------------------------------------------------------------------
case "wi":
check_authority("019","view");

$where_str = '';		 

// creat cust combo box	 ���X �Ȥ�N��
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

//080725message�W�[		
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
			
			$_SESSION['partial_id'] = $PHP_p_id;	//order_partial��ID
			
			// ���X�ﶵ��� �ζǤJ���Ѽ�
			$op['wi']['cust'] = $PHP_customer;							
			$op['smpl']['size_scale'] = $PHP_size_scale;
			$op['smpl']['size'] = $PHP_size;
			$op['smpl']['style_code'] = $PHP_style_code;
			$op['smpl']['cust_ref'] = $PHP_cust_ref;
			$op['smpl']['id'] = $PHP_smpl_id;
			$op['smpl']['etd'] = $PHP_etd;
			$op['smpl']['unit'] = $PHP_unit;				
			$op['wi']['dept']	 = $PHP_dept;
			// pre  �s�y�O�s��....
			$op['wi']['wi_precode'] = $PHP_style_code;


			// ���X�ﶵ��� �ζǤJ���Ѽ�
			

			page_display($op, "019", $TPL_WI_ADD);	    	    
		break;
		
//=======================================================
	case "do_wi_add_1":

		check_authority("019","add");
		// ���X�~�X... �H��J�ɤ��~���~�X[���]
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

										"size_scale"	=>	$size_id,	// ���s�J wi
						

										"style_code"	=>	$PHP_style_code,
										"creator"			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
										"open_date"		=>	$dt['date_str'],
										"version"			=>	"1"
								);

		// check �g�J wi ��	���		
			$op['wi'] = $parm;
		if (!$f1 = $wi->check($parm)) {  // �S�����\��J��Ʈ�
			$op['msg'] = $wi->msg->get(2);
			
			// ��J �������˥��Ѽ� ----
			$op['smpl']['size_scale'] = $PHP_Size;
			$op['smpl']['size'] = $size_id;
			$op['smpl']['style_code'] = $parm['style_code'];
			$op['smpl']['cust_ref'] = $parm['cust_ref'];
			$op['smpl']['id'] = $parm['smpl_id'];
			$op['smpl']['etd'] = $parm['etd'];
			$op['smpl']['unit'] = $PHP_unit;
			$op['smpl']['status'] = $PHP_status;
			// �]�w re_adding �б�---
			$op['re_adding'] = "1";
			// �ۤ���URL�M�w  ---------------------------------------------
				$style_dir	= "./picture/";  
				$no_img		= "./images/graydot.gif";
			if(file_exists($style_dir.$op['wi']['style_code'].".jpg")){
				$op['pic_link'] = $style_dir.$op['wi']['style_code'].".jpg";
			} else {
				$op['pic_link'] = $no_img;
			}

			// �C�X �s�W �s�@�O ��J�˥������� ---------------------------------------------
					
				// ���X�~�X...
				$dt = decode_date(1);
				$year_code = substr($dt['year'],-2);
				// pre  �s�y�O�s��....
				$op['wi']['wi_precode'] = $parm['style_code'];

					// �ﶵ�� combo �]�w
				$op['unit'] = $arry2->select($APPAREL_UNIT,$parm['unit'],'PHP_unit','select','');  	
				// creat sample type combo box
				$sample_def = $smpl_type->get_fields('smpl_type');   // ���X �˥����O
			page_display($op, 3, 1, $TPL_WI_ADD);	    	    
			break;		
		}   // end check
		
		// �s�s �s�@�O ���X----------------------------------------------------------------
		//  �s�s ��@�O���X �]�P�ɧ�sdept�ɤ���num��[csv]
		$parm['wi_num'] = $PHP_style_code; 

	
		
		$f1 = $wi->add($parm);
	
		if (!$f1) {  // �S�����\��J��Ʈ�

			$op['msg'] = $wi->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);
			break;

		}   // end if (!$F1)---------  �����P�_ ------------------------------------
		$message = "Done ADD [".$parm['wi_num']."] W / I�FPlease add colorway / breakdown and materiel consump.";
		$log->log_add(0,"31A",$message);
		// �N wis [�˥��s�O]�s�� �g�J �˥��ɤ�[ smpl table ] NOTE: �D�n�O���Ӵ� �˥�����A������s�O[�ݦA�ƻs]
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
		$_SESSION['partial_id'] = $PHP_p_id;	//order_partial��ID
//��Sample order�ƻs[�s�y�O]		
		$f1=$wi->copy_wi($PHP_wi_num,$PHP_smpl_id,$PHP_new_num,$PHP_etd,$PHP_ord_cust,$PHP_dept,$GLOBALS['SCACHE']['ADMIN']['login_id'],$dt['date_str']);
		
		if (!$f1)
		{  
			$op['msg']= $wi->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
// �N wis [�˥��s�O]�s�� �g�J �˥��ɤ�[ smpl table ] NOTE: �D�n�O���Ӵ� �˥�����A������s�O[�ݦA�ƻs]
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
	$_SESSION['partial_id'] = $PHP_p_id;	//order_partial��ID
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

		// �N �s�y�O ����show out ------
		//  wi �D��
			if(!$op = $wi->get_all($PHP_id)){    //���X�ӵ� �s�y�O�O�� ID
						$op['msg']= $wi->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
			}
//--------------------------------------------------------------------------
		//  wi_qty �ƶq��
		$where_str = " WHERE wi_id='".$op['wi']['id']."'";
		$T_wiqty = $wiqty->search(1,$where_str);

		$num_colors = count($T_wiqty);

		// ���X size_scale ----------------------------------------------
		$size_A = $size_des->get($op['smpl']['size']);
		$op['smpl']['size_scale']=$size_A['size_scale'];
			//????????????????????????????????????? �`�N ���ɨS�� size type ��Ʈ�??????
		$sizing = explode(",", $size_A['size']);

			// ���X size table-------------------------------------------
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
// �N wis [�˥��s�O]�s�� �g�J �˥��ɤ�[ smpl table ] NOTE: �D�n�O���Ӵ� �˥�����A������s�O[�ݦA�ƻs]
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

// �ǤJ javascript�ǥX���Ѽ� [ colorway : ���M��J�w�� javascript�߬d ]
$parm['qty']		= $PHP_qty;   // ��javascript�ǥX���}�C�� �۰ʧ令csv�ǥX
$parm['wi_id']		= $wi_id;
$parm['colorway']	= $colorway;
$parm['p_id']	= $_SESSION['partial_id'];

// �[�J �s��� ----- �[�J wiqty table -----------------
if(!$T_del = $wiqty->add($parm)){    
	$op['msg']= $wi->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}

// �N �s�y�O ����show out ---------------------------------------------
//  wi �D�� ��X�� ----------------------------------------------------
if(!$op = $wi->get_all($wi_id)){    //���X�ӵ� �s�y�O�O��
	$op['msg']= $wi->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);
	break;
}

# �O���ϥΪ̰ʺA
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
		//�P�_�O�_�M�w�n���çR���s�Υi�_�R�����C�⪺�ؽX�ƶq
		$schedule_qty_array = $schedule->get_order_schedule($PHP_wi_num,$_SESSION['partial_id']);
		$schd_sum=0;
		foreach($schedule_qty_array as $schd_key=>$schd_value)
		{
			$schd_sum += $schd_value['qty'];
		}
		//�i�H�q$id�d�X(wiqty)p_id
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
		
			// �R�� ���w����� ----------------------------
			if(!$T_del =	$wiqty->del($id)){    
							$op['msg']= $wi->msg->get(2);
							$layout->assign($op);
							$layout->display($TPL_ERROR);  		    
							break;
			}

			$size_edit = 0;
			//�˵��O�_�i�H�ק�size(�Y�O�_�s�bcolorway)
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
		
		if(isset($PHP_p_id))$_SESSION['partial_id'] = $PHP_p_id;	//order_partial��ID
		// �N �s�y�O ����show out ------
		//  wi �D�� ��X��
		if(!$op = $wi->get_all($PHP_id)){    //���X�ӵ� �s�y�O�O��
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}

	//�˵��O�_�i�H�ק�size(�Y�O�_�s�bcolorway)
		if ($wi->check_size($op['wi']['style_code']))
		{
			$op['size_edit']=1;
		}

		//  wi_qty �ƶq�� -----------------------------------------------------------------
		$where_str = " WHERE wi_id='".$PHP_id."' AND p_id = '".$_SESSION['partial_id']."'";
		$T_wiqty = $wiqty->search(1,$where_str);

		// ���X size_scale --------
		$size_A = $size_des->get($op['smpl']['size']);
		$op['smpl']['size_scale']=$size_A['size_scale'];
			//????????????????????????????????????? �`�N ���ɨS�� size type ��Ʈ�??????
		$sizing = explode(",", $size_A['size']);
		$op['size'] = $sizing;
		$op['b_size'] = $size_A['base_size'];

		//���X��partial��schedule�ƶq�A�ǳƧP�_��
		$op['partial_schedule'] = $schedule->get_order_schedule($op['wi']['wi_num'],$_SESSION['partial_id']);
		$op['partial_schedule_qty']=0;
		foreach($op['partial_schedule'] as $key=>$value)
		{
			$op['partial_schedule_qty'] = $op['partial_schedule_qty']+$value['qty'];
		}
		//���Xpartial�ƶq
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
		// ���X size table-------------------------------------------
		
			$op['show_del_breakdown'] = show_del_breakdown($T_wiqty,$sizing,'',$size_A['base_size'],$enable_delete,$wiqty_sum,$op['partial_schedule_qty']);
			$op['edit_breakdown'] = edit_breakdown($sizing,'',$size_A['base_size']);

			$op['msg'] = $wi->msg->get(2);

			// ���X unit�� combo
		$op['unit'] = $arry2->select($APPAREL_UNIT,$op['wi']['unit'],'PHP_unit','select','');  	

		// creat sample type combo box
		$sample_def = $smpl_type->get_fields('smpl_type');   // ���X �˥����O
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

// UPDATE  wi ��ƪ�[etd],[smpl_type][unit] �T���� �B�Nversion �[�@
		//�[�Jsize�������		
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


		//--------------- �����ק�g�J------------------

		//--------------- �C�X��ᤧ�O��------------------
		//  wi �D�� --------------------------------------------------------------------------------
		if(!$op = $wi->get_all($argv['id'])){    //���X�ӵ� �s�y�O�O��
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}

		//  wi_qty �ƶq�� --------------------------------------------------------------------------
			$where_str = " WHERE wi_id='".$op['wi']['id']."' AND p_id = '".$_SESSION['partial_id']."'";
		$T_wiqty = $wiqty->search(1,$where_str);

		$num_colors = count($T_wiqty);

		// ���X size_scale -----------------------------------------------------
		$size_A = $size_des->get($op['smpl']['size']);
		$op['smpl']['size_scale']=$size_A['size_scale'];
			//????????????????????????????????????? �`�N ���ɨS�� size type ��Ʈ�??????
		$sizing = explode(",", $size_A['size']);

			// ���X size table-------------------------------------------
		$reply = show_breakdown($T_wiqty,$sizing,$size_A['base_size']);
		$op['show_breakdown'][0] = $reply['html'];
		$op['total'] = $reply['total'];
		//-----------------------------------------------------------------

			# �O���ϥΪ̰ʺA
		$message = "Successfully update wi:[".$op['wi']['wi_num']."]";
		$log->log_add(0,"31E",$message);
		$op['msg'][] = $message;
		page_display($op, "019", $TPL_WI_VIEW);	
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "wi_del":

			// ���ݭn manager login �~��u�����R�� SUPLIER  ----------------------------------
		if(!$admin->is_power("019","del")  && !($GLOBALS['SCACHE']['ADMIN']['id'] == "SA" )) {
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$op['wi'] = $wi->get($PHP_id);

		// �R�� wi �D�ɸ�� ---------------------------------------------------
		if (!$wi->del($PHP_id)) {
			$op['msg'] = $wi->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;	    
		}
		// �R�� wi qty �ƶq�ɸ�� ---------------------------------------------------
		if (!$wiqty->del($PHP_id,'1')) {
			$op['msg'] = $wiqty->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;	    
		}
					
		// �R�� TI �����ɸ�� ---------------------------------------------------
		if (!$ti->del($PHP_id,'1')) {
			$op['msg'] = $ti->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;	    
		}
		
		
		// �Ѱ� smpl �O������ marked ---------------------------------------------------
		if (!$order->update_field("marked",'',$op['wi']['smpl_id'])) {
			$op['msg']= $order->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		// �Ѱ� smpl �O������ marked ---------------------------------------------------
		if (!$order->update_field("m_status",'0',$op['wi']['smpl_id'])) {
			$op['msg']= $order->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
					
					
					# �O���ϥΪ̰ʺA
		$message = "Successfully delete wi:[".$op['wi']['wi_num']."] �C";
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

//�ǰemessage��RD�H��
			$rcd = $wi->get_all($PHP_id);
			if($rcd['wi']['status'] > 0 && $rcd['wi']['ti_cfm'] <> '0000-00-00' )
			{
	 			if($PHP_fty == 'WX') $PHP_fty = 'HJ';
	 			$messg  = ' �s�y�O(Working Instruction) : <a href=index2.php?PHP_action=wi_view&PHP_id='.$PHP_id.$PHP_back_str2.'>'.$PHP_order_num.'</a>';
	 			$messg  .=' �s�y���� (WorkSheet) : <a href=index2.php?PHP_action=ti_view&PHP_id='.$PHP_id.'&PHP_style_code='.$PHP_order_num.'&PHP_dept_code=&PHP_etdfsh=&PHP_cust=&PHP_wi_num=&PHP_etdstr=&PHP_fty_sch=&PHP_sr=1>'.$PHP_order_num.'</a>';
	 			$notify->system_msg_send_with_mail('3-3-S',$PHP_fty,$PHP_order_num,$messg);
			}

			//�ǰemessage���D�ƹw����
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
		// �N �s�y�O ����show out ------
		//  wi �D��

			if(!$op = $wi->get_all($PHP_id)){    //���X�ӵ� �s�y�O�O�� ID
						$op['msg']= $wi->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
			}

//--------------------------------------------------------------------------
		//  wi_qty �ƶq��
		$where_str = " WHERE wi_id='".$op['wi']['id']."' AND p_id = '".$_SESSION['partial_id']."'";
		$T_wiqty = $wiqty->search(1,$where_str);

		$num_colors = count($T_wiqty);

		// ���X size_scale ----------------------------------------------
		$size_A = $size_des->get($op['smpl']['size']);
		$op['smpl']['size_scale']=$size_A['size_scale'];
			//????????????????????????????????????? �`�N ���ɨS�� size type ��Ʈ�??????
		$sizing = explode(",", $size_A['size']);

			// ���X size table-------------------------------------------
		$reply = breakdown_cfm_edit($T_wiqty,$sizing,$size_A['base_size']);
		$op['show_breakdown'] = $reply['html'];
		$op['total'] = $reply['total'];
		//-----------------------------------------------------------------
		$op['i'] = count($sizing);
		$op['j']= count($T_wiqty);
		//echo $op['wi']['wi_num'];
		//echo "<br>";
		//print_r($op);
		//�[�Jorder���
		$op['partial_mks']= $PHP_partial_mks;
		//�Ʋ��`��DTH12-0639
		//$test=5063;
		$op['order_schedule'] = $schedule->get_order_schedule($op['wi']['wi_num']);
		//$op['order_schedule'] = $schedule->get_order_schedule('DTH12-0639');
		$op['order_schedule_qty']=0;
		foreach($op['order_schedule'] as $key=>$value)
		{
			$op['order_schedule_qty'] = $op['order_schedule_qty']+$value['qty'];
		}
		//echo $op['order_schedule_qty'];
		//�q���`�� ps_id smpl
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
			
		//xxxxx�q��ƶqxxxx
		$s_ord=$order->get($PHP_ord_id);
		$ord_part = $order->get_partial($_SESSION['partial_id']);
		
		if ($ord_part['p_qty'] <>$PHP_ord_qty)
		{
			
			$su=(int)($ord_part['p_qty']*$s_ord['ie1']);
			if ($s_ord['status'] >= 4 && $s_ord['status'] <> 5)
			{	//ETP~ETD��SU							 
				 $etp_su=decode_mon_yy_su($ord_part['p_etp_su']); //���X�v�s�betp-su�ñNsu�ȻP�~,����}
				 for ($i=0; $i<sizeof($etp_su); $i++) //�N�v�s�bsu���~�װO���ȧR��				 				 	
				 	$f1=$capaci->delete_su($s_ord['factory'], $etp_su[$i]['year'], $etp_su[$i]['mon'], 'pre_schedule', $etp_su[$i]['su']);			 						 
				 
				 $div = $order->distri_month_su($su,$ord_part['p_etp'],$ord_part['p_etd'],$s_ord['factory'],'pre_schedule'); //����C��SU���x�s
				 $order->update_partial('p_etp_su', $div, $_SESSION['partial_id']);		
				 $order->update_distri_su($s_ord['order_num'],'p_etp_su','etp_su');  //�x�s�setp_su
			}
			
			
//			if($s_ord['status'] >= 7){
//				 //ETS~ETF��SU(�u�t�Ƶ{)
//				 $fty_su=decode_mon_yy_su($ord_pdt['fty_su']);//���X�v�s�bfty-su�ñNsu�ȻP�~,����}
//				 for ($i=0; $i<sizeof($fty_su); $i++)//�N�v�s�bsu���~�װO���ȧR��
//				 {				 	
//				 	$f1=$capaci->delete_su($s_ord['factory'], $fty_su[$i]['year'], $fty_su[$i]['mon'], 'schedule', $fty_su[$i]['su']);			 		
//				 }
//				 $div = $order->distri_month_su($su,$ord_pdt['ets'],$ord_pdt['etf'],$s_ord['factory'],'schedule');//����C��SU���x�s
//				 $order->update_pdtion_field('fty_su', $div, $ord_pdt['id']);//�x�s�sfty_su				 
//			}

			// �g�J qty�Msu
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
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		if(is_numeric($PHP_id)){
			if(!$wi = $wi->get($PHP_id)){    //���X�ӵ� �s�y�O�O�� ID
						$op['msg']= $wi->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
			}
		} else {
			if(!$wi = $wi->get(0,$PHP_id)){    //���X�ӵ� �s�y�O�O�� WI_NUM
						$op['msg']= $wi->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
			}
		}

		//  smpl �˥���
		if(!$smpl = $order->get($wi['smpl_id'])){
					$op['msg']= $order->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}

//--------------------------------------------------------------------------
		// ���X size_scale ----------------------------------------------
//		$where_str = " WHERE cust='".$wi['cust']."' AND size_scale='".$smpl['size']."' ";
		$size_A = $size_des->get($smpl['size']);
		$smpl['size_scale']=$size_A['size_scale'];
		$sizing = explode(",", $size_A['size']);

		//  wi_qty �ƶq��
		//  wi_qty �ƶq��
		$where_str = " WHERE wi_id='".$wi['id']."' ";
		if($_SESSION['partial_id'])
		{
			$where_str .=" AND p_id='".$_SESSION['partial_id']."'";
			$T_wiqty = $wiqty->search(1,$where_str);
			$num_colors = count($T_wiqty);
			// ���X size table-------------------------------------------
			$reply = get_colorway_qty($T_wiqty,$sizing);
			$op['total'] = $reply['total'];   // �`���
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
				// ���X size table-------------------------------------------
				$reply = get_colorway_qty($T_wiqty,$sizing);
				$data[$i] = $reply['data'];
				$op['total'] += $reply['total'];
				$colorway_name[$i] = $reply['colorway'];
				$colorway_qty[$i] = $reply['colorway_qty'];									
			}
		}

		//-----------------------------------------------------------------

		//  ���X �Ͳ����� �O�� -------------------------------------------------
		$where_str = " WHERE wi_id = '".$wi['id']."' ";
		$T = $ti->search(0,$where_str);  //���X�ӵ� �˥��D�ưO��
		$op['ti'] = $T['ti'];

		if (!is_array($op['ti'])) {
			$op['msg'] = $ti->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		if (!count($op['ti'])){	$op['ti_NONE'] = "1";		}

		

//-----------------------------------------------------------------------
			// �ۤ���URL�M�w
				$style_dir	= "./picture/";  
				$no_img		= "./images/graydot.jpg";
			if(file_exists($style_dir.$wi['style_code'].".jpg")){
				$wi['pic_url'] = $style_dir.$wi['style_code'].".jpg";
			} else {
				$wi['pic_url'] = $no_img;
			}
		
		//  ���X�D�ưO�� ----------------------------------------------------
		$where_str = " WHERE smpl_code = '".$wi['style_code']."' ";
		$lots = $smpl_lots->search(0,$where_str);  //���X�ӵ� �˥��D�ưO��
		$op['lots_use'] = $lots;

		if (!is_array($op['lots_use'])) {
			$op['msg'] = $smpl_lots->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		if (!count($op['lots_use'])){	$op['lots_NONE'] = "1";		}

		//  ���X�ƮƮưO�� ---------------------------------------------------
		$where_str = " WHERE smpl_code = '".$wi['style_code']."' ";
		$acc = $smpl_acc->search(0,$where_str);  //���X�ӵ� �˥��D�ưO��		
		$op['acc_use'] = $acc;

		if (!is_array($op['acc_use'])) {     
			$op['msg'] = $smpl_acc->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

//---------------------------------- ��Ʈw�@�~���� ------------------------------

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
// [��J] �q��򥻸��
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
// �]�w colorway���ƶq�� - ��쪺�e  [size breakdown]
// �� 80~200 �O�d 30�� colorway���e �䥦 90���t���ƶq��
	$num_siz = count($sizing);
	$w_qty = intval(155/($num_siz+1));
	$w = array('40');  // �Ĥ@�欰 40�e
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
		// �w�q �ϥH�U���y�� 
		$pdf->SetXY($X,$Y+5);

// �D��
		$pdf->SetFont('Big5','',10);

	// �D�Ʃ��Y
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
		// [��J] �q��򥻸��
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

	// �ƮƩ��Y
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
			// [��J] �q��򥻸��
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
if(isset($PHP_p_id))$_SESSION['partial_id'] = $PHP_p_id;	//order_partial��ID

//�R��Bom�������
$f1=$bom->del_lots($PHP_id,1);
$f2=$bom->del_acc($PHP_id,1);

// �N �s�y�O ����show out ------
//  wi �D�� ��X��		
if(!$op = $wi->get_all($PHP_id)){    //���X�ӵ� �s�y�O�O��
	$op['msg']= $wi->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}

//revise���Ƶ���J		
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
//		//�˵��O�_�i�H�ק�size(�Y�O�_�s�bcolorway)
//		if ($wi->check_size($op['wi']['style_code']))
//		{
//			$op['size_edit']=1;
//		}
//
//		//  wi_qty �ƶq�� -----------------------------------------------------------------
//		$where_str = " WHERE wi_id='".$PHP_id."' AND p_id = '".$_SESSION['partial_id']."'";
//		$T_wiqty = $wiqty->search(1,$where_str);
//
//		// ���X size_scale --------
//		$size_A = $size_des->get($op['smpl']['size']);
//		$op['smpl']['size_scale']=$size_A['size_scale'];
//			//????????????????????????????????????? �`�N ���ɨS�� size type ��Ʈ�??????
//		$sizing = explode(",", $size_A['size']);
//
//
////search����O��			
//
//		// ���X size table-------------------------------------------
//			$op['show_del_breakdown'] = show_del_breakdown($T_wiqty,$sizing,$PHP_back_str2,$size_A['base_size']);
//			$op['edit_breakdown'] = edit_breakdown($sizing,$PHP_back_str2,$size_A['base_size']);
//
//			$op['msg'] = $wi->msg->get(2);
//
//			// ���X unit�� combo
//		$op['unit'] = $arry2->select($APPAREL_UNIT,$op['wi']['unit'],'PHP_unit','select','');  	
//
//		// creat sample type combo box
//		$sample_def = $smpl_type->get_fields('smpl_type');   // ���X �˥����O
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

if(isset($PHP_p_id))$_SESSION['partial_id'] = $PHP_p_id;	//order_partial��ID

// revise���Ƶ���J		
$parm['rev'] 		= $PHP_rev;
$parm['id'] 		= $PHP_id;
$parm['order_num'] 	= $PHP_wi_num;
if(!$T_updt =	$wi->revise_with_po($parm)){    
	$op['msg']= $wi->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}

// �R��Bom�������
// $f1=$bom->del_lots($PHP_id,2);
// $f2=$bom->del_acc($PHP_id,2);

// Disable BOM �������
// $bom_rev = $PHP_bom_rev - 1;			
// if($bom_rev == 0) $bom_rev = 1;
// $parm2 = array($PHP_id, 'dis_ver', $bom_rev, 'bom_lots');
// $f1=$bom->update_wi_field($parm2);
// $parm2 = array($PHP_id, 'dis_ver', $bom_rev, 'bom_acc');
// $f1=$bom->update_wi_field($parm2);



// �N �s�y�O ����show out ------
//  wi �D�� ��X��		
if(!$op = $wi->get_all($PHP_id)){    //���X�ӵ� �s�y�O�O��
	$op['msg']= $wi->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}

// $f3=$po->change_pp($op['wi']['wi_num']);


// �˵��O�_�i�H�ק�size(�Y�O�_�s�bcolorway)
if ($wi->check_size($op['wi']['style_code'])) {
	$op['size_edit']=1;
}

//  wi_qty �ƶq�� -----------------------------------------------------------------
$where_str = " WHERE wi_id='".$PHP_id."' AND p_id = '".$_SESSION['partial_id']."'";
$T_wiqty = $wiqty->search(1,$where_str);

// ���X size_scale --------
$size_A = $size_des->get($op['smpl']['size']);
$op['smpl']['size_scale']=$size_A['size_scale'];

// ????????????????????????????????????? �`�N ���ɨS�� size type ��Ʈ�??????
$sizing = explode(",", $size_A['size']);

// ���X size table-------------------------------------------
$op['show_del_breakdown'] = show_del_breakdown($T_wiqty,$sizing,$PHP_back_str2,$size_A['base_size']);
$op['edit_breakdown'] = edit_breakdown($sizing,$PHP_back_str2,$size_A['base_size']);
$op['msg'] = $wi->msg->get(2);

// ���X unit�� combo
$op['unit'] = $arry2->select($APPAREL_UNIT,$op['wi']['unit'],'PHP_unit','select','');  	

// creat sample type combo box
$sample_def = $smpl_type->get_fields('smpl_type');   // ���X �˥����O
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

	if(!$wi->check2($argv)){    // ��J�����T--- ��J�� ���~ [ ��� ]
		// �N �s�y�O ����show out ---------------------------------------------
		//  wi �D�� ��X�� ----------------------------------------------------
		if(!$op = $wi->get_all($PHP_wi_id)){    //���X�ӵ� �s�y�O�O��
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}

		//  wi_qty �ƶq�� -----------------------------------------------------------------
		$where_str = " WHERE wi_id='".$PHP_wi_id."' ";
		$T_wiqty = $wiqty->search(1,$where_str);

		// ���X size_scale --------
		$size_A = $size_des->get($op['smpl']['size']);
		$op['smpl']['size_scale']=$size_A['size_scale'];
			//????????????????????????????????????? �`�N ���ɨS�� size type ��Ʈ�??????
		$sizing = explode(",", $size_A['size']);

//------------search����O��
		$op['back_str']=$PHP_back_str;
		$op['back_str2']=$PHP_back_str2;

			// ���X size table-------------------------------------------
			$op['show_del_breakdown'] = show_del_breakdown($T_wiqty,$sizing,$PHP_back_str2,$size_A['base_size']);
			$op['edit_breakdown'] = edit_breakdown($sizing,$PHP_back_str2,$size_A['base_size']);
		
			$op['msg'] = $wi->msg->get(2);

			// ���X unit�� combo
		$op['unit'] = $arry2->select($APPAREL_UNIT,$op['wi']['unit'],'PHP_unit','select','');  	

		// creat sample type combo box
		$sample_def = $smpl_type->get_fields('smpl_type');   // ���X �˥����O
		$op['smpl_type'] =  $arry2->select($sample_def,$op['wi']['smpl_type'],'PHP_smpl_type','select','');  	
		$op['new_revise'] = $op['wi']['revise'];		
		page_display($op, 3, 1, $TPL_WI_REVISE); 
		break;		
	}	// ��J�����T--��J�� ���~ [ ��� ]- (((����)))


// UPDATE  wi ��ƪ�[etd],[smpl_type][unit] �T���� �B�Nversion �[�@
		//size�ק�
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

		//--------------- �����ק�g�J------------------

		//--------------- �C�X��ᤧ�O��------------------
		//  wi �D�� --------------------------------------------------------------------------------
		if(!$op = $wi->get_all($argv['id'])){    //���X�ӵ� �s�y�O�O��
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}
		

		//  wi_qty �ƶq�� --------------------------------------------------------------------------
			$where_str = " WHERE wi_id='".$op['wi']['id']."' ";
		$T_wiqty = $wiqty->search(1,$where_str);

		$num_colors = count($T_wiqty);

		// ���X size_scale -----------------------------------------------------
		$size_A = $size_des->get($op['smpl']['size']);
		$op['smpl']['size_scale']=$size_A['size_scale'];
			//????????????????????????????????????? �`�N ���ɨS�� size type ��Ʈ�??????
		$sizing = explode(",", $size_A['size']);

			// ���X size table-------------------------------------------
		$reply = show_breakdown($T_wiqty,$sizing,$size_A['base_size']);
		$op['show_breakdown'] = $reply['html'];
		$op['total'] = $reply['total'];
		//-----------------------------------------------------------------

			# �O���ϥΪ̰ʺA
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
		// �N �s�y�O ����show out ------
		//  wi �D��
		
		if(!$op = $wi->get_all($PHP_id)){    //���X�ӵ� �s�y�O�O��
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}

		$op['old_id'] = $PHP_old_id;
//--------------------------------------------------------------------------
		// ���X size_scale ----------------------------------------------
		$size_A = $size_des->get($op['smpl']['size']);
		$op['smpl']['size_scale']=$size_A['size_scale'];
			//????????????????????????????????????? �`�N ���ɨS�� size type ��Ʈ�??????
		$sizing = explode(",", $size_A['size']);

		//  wi_qty �ƶq��			
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
				// ���X size table-------------------------------------------
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
	
	//�ɮצC��
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
		// �N �s�y�O ����show out ------
		//  wi �D��
		
		$f1=$ti->del_wi_id($PHP_smpl_id,0);	//COPY�ʧ@����e,���⤧�e��WS�R��,�קK���и��
		$f1=$wi->copy_ti($PHP_id,$PHP_smpl_id);

		$op=$wi->get_all($PHP_smpl_id);
		$message = "Add WI:[".$op['wi']['wi_num']."] Description Copy�C";
		$log->log_add(0,"34C",$message);
		$sub_ti = array();
		$redir_str=$PHP_SELF.'?PHP_action=ti_add&PHP_id='.$PHP_smpl_id.$PHP_back_str2.'&PHP_msg='.$message;
		
		redirect_page($redir_str);
		break;

//=============================================2011/11/14
    case "ti_add":
		check_authority('021',"add");
		// �N �s�y�O ����show out ------
		//  wi �D��
		if(!$op = $wi->get_all($PHP_id)){    //���X�ӵ� �s�y�O�O��
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}

//--------------------------------------------------------------------------
		// ���X size_scale ----------------------------------------------
		$size_A = $size_des->get($op['smpl']['size']);
		$op['smpl']['size_scale']=$size_A['size_scale'];
			//????????????????????????????????????? �`�N ���ɨS�� size type ��Ʈ�??????
		$sizing = explode(",", $size_A['size']);


		//  wi_qty �ƶq��			
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
				// ���X size table-------------------------------------------
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
		
	//�ɮצC��
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
			$message = "Add WI :[".$PHP_wi_num."] NEW GROUP [".$PHP_other_name."] �C";
		}else{
			$message = "Please input new gorup name";
		}


// �N �s�y�O ����show out ------		
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


// �N �s�y�O ����show out ------
		$message = "Add WI:[".$PHP_num."] Description �C";
		$log->log_add(0,"23D",$message);

		$redir_str='wi.php?PHP_action=ti_view&PHP_id='.$PHP_wi_id.$PHP_back_str2.'&PHP_msg='.$message;
		
		redirect_page($redir_str);
		break;

//================================================2011/11/14
    case "ti_view":
	
		check_authority('021',"view");
		$_SESSION['partial_id'] = '';
		// �N �s�y�O ����show out ------
		//  wi �D��
		
		if(!$op = $wi->get_all($PHP_id)){    //���X�ӵ� �s�y�O�O��
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}

//--------------------------------------------------------------------------
		// ���X size_scale ----------------------------------------------
		$size_A = $size_des->get($op['smpl']['size']);
		$op['smpl']['size_scale']=$size_A['size_scale'];
			//????????????????????????????????????? �`�N ���ɨS�� size type ��Ʈ�??????
		$sizing = explode(",", $size_A['size']);

		//  wi_qty �ƶq��			
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
				// ���X size table-------------------------------------------
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
	
	//�ɮצC��
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

//�ǰemessage��RD�H��
	$rcd = $wi->get_all($PHP_id);
	if($rcd['wi']['status'] > 0 && $rcd['wi']['ti_cfm'] <> '0000-00-00' )
	{
	 if($PHP_fty == 'WX') $PHP_fty = 'HJ';
	 $messg  = ' �s�y�O(Working Instruction) : <a href=index2.php?PHP_action=wi_view&PHP_id='.$PHP_id.'&PHP_dept_code=&PHP_num=&PHP_cust=&PHP_etdstr=&PHP_etdfsh=&PHP_sch_fty=&PHP_un_fhs=&PHP_sr=1>'.$PHP_wi.'</a>';
 	 $messg .= '�s�y���� (WorkSheet) : <a href=index2.php?PHP_action=ti_view&PHP_id='.$PHP_id.$PHP_back_str2.'&PHP_ti=1>'.$PHP_wi.'</a>';
	 $notify->system_msg_send_with_mail('3-3-S',$PHP_fty,$PHP_wi,$messg);
	}
		
		$redir_str=$PHP_SELF.'?PHP_action=ti_view&PHP_id='.$PHP_id.$PHP_back_str2.'&PHP_ti=1&PHP_msg='.$message;
		redirect_page($redir_str);
		break;

//==================================================2011/11/14
	case "ti_search_wi":   //  ����X�s�y�O.......

		check_authority('021',"view");

			if (!$op = $wi->search(1,0)) {
				$op['msg']= $wi->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			// op �Q�M���F �ݦA�P�_�@�� 

		//2006/05/02 update
		$op['dept_id'] = get_dept_id();
		
		// �p�G�O manager �i�J��...
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
		
			if ($f_check == 1){   // �W���ɪ����ɦW�� mdl �� -----

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
				$pttn_name = $PHP_num."_".$A.".".$ext;  // �զX�ɦW
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
				
				//�ǰemessage�������H��
				if($PHP_fty == 'WX') $PHP_fty = 'HJ';
				$messg  = ' �s�y�O(Working Instruction) : <a href=index2.php?PHP_action=wi_view&PHP_id='.$PHP_id.'&PHP_dept_code=&PHP_num=&PHP_cust=&PHP_etdstr=&PHP_etdfsh=&PHP_sch_fty=&PHP_un_fhs=&PHP_sr=1>'.$PHP_num.'</a>';
				$messg .= ' �s�y���� (WorkSheet) : <a href=index2.php?PHP_action=ti_view&PHP_id='.$PHP_id.$PHP_back_str2.'&PHP_ti=1>'.$PHP_num.'</a>';
				$messg .= ' <BR>�w�W�Ƿs�ɮסG'.$pttn_name.'�A�ɮ״y�z�G'.$PHP_desp;
				$notify->system_msg_send_with_mail('3-3-F',$PHP_fty,$PHP_num,$messg);
					
				$message = "UPLOAD file of ".$PHP_num;
				$log->log_add(0,"31E",$message);
			} else {  // �W���ɪ����ɦW  �O  exe �� -----

				$message = "upload file is incorrect format ! Please re-send.";
			}
		}else{  //�W���ɦW���Ю�
			$message = "Upload file is too big";
		}
	
		}else{
			$message="You don't pick any file or add file descript.";
		}	
			
//�����W���ɮ�		

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
#	��s��ƥΡA���ե�(�p��)
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
