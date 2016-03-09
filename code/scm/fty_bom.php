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

include_once($config['root_dir']."/lib/class.wi_fty.php");
$wi_fty = new WI_FTY();
if (!$wi_fty->init($mysql,"log")) { print "error!! cannot initialize database for ORDER SHIFT class"; exit; }

$op = array();

session_register	('sch_parm');
session_register	('partial_id');
// echo $PHP_action;
switch ($PHP_action) {
//=FTY_BOM=============================================================
    case "fty_bom":

		check_authority('024',"view");

		$where_str = $manager = $dept_id = '';		 
		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];   // �P�w�i�J����������
		$team = $GLOBALS['SCACHE']['ADMIN']['team_id'];  // �P�w�i�J����������(team)
		$sales_dept_ary = get_sales_dept();// ���X �~�Ȫ����� [���tK0] ------
		// for ($i=0; $i<count($sales_dept_ary);$i++){
			// if($user_dept == $sales_dept_ary[$i]){

				// �p�G�O�~�ȳ��i�J �hdept_code ���w�ӷ~�ȳ�---
				// $dept_id = $sales_dept_ary[$i];  
			// }
		// }
		$op['dept_id'] = $dept_id;
		// if (!$dept_id || $team<>'MD') {    // ���O�~�ȳ��H[�]���t K0 ���H ]�i�J��
			// $op['manager_flag'] = 1;
			// $manager_v = 1;
			//�~�ȳ��� select���
			$op['dept_id'] = $arry2->select($sales_dept_ary,"","PHP_dept_code","select",""); 
 
		// }

								
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
		for ($i=0; $i< sizeof($cust_def); $i++)
		{
			$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];
		}
		$op['cust_select'] =  $arry2->select($cust_value,'','PHP_cust','select','',$cust_def_vue); 
		$op['fty_select'] =  $arry2->select($FACTORY,'','PHP_fty_sch','select',''); 
		for ($i=0; $i< sizeof($FACTORY); $i++)
		{
			if ($user_dept == $FACTORY[$i]) $op['fty_select'] = $user_dept."<input type='hidden' name='PHP_fty_sch' value='$user_dept'>";
		}

		$op['msg']= $wi->msg->get(2);

//080725message�W�[		
	$note=$notify->search($GLOBALS['SCACHE']['ADMIN']['login_id'], "`read`=0");
	$op['max_notify'] = $note['max_no'];

		page_display($op, '024', $TPL_FTY_BOM_SEARCH);		    	    
		break;		
		
//=bom_search_wi======================================================
	
	case "bom_search_wi":   //  ����X�s�y�O.......

		check_authority('024',"view");
		if(isset($PHP_etdfsh))
		{
			$sch_parm = array();
			$sch_parm = array(	"dept_code"		=>  $PHP_dept_code,
													"etdfsh"			=>  $PHP_etdfsh,
													"cust"				=>	$PHP_cust,
													"wi_num"			=>	$PHP_wi_num,
													"etdstr"			=>	$PHP_etdstr,
													"fty_sch"			=>	$PHP_fty_sch,
													"sr_startno"			=>	$PHP_sr_startno
				);
			}else{
				if(isset($PHP_sr_startno))$sch_parm['sr_startno'] = $PHP_sr_startno;
				$PHP_dept_code = $sch_parm['dept_code'];
				$PHP_etdfsh = $sch_parm['etdfsh'];
				$PHP_cust = $sch_parm['cust'];
				$PHP_wi_num = $sch_parm['wi_num'];
				$PHP_etdstr = $sch_parm['etdstr'];
				$PHP_fty_sch = $sch_parm['fty_sch'];
				$PHP_sr_startno = $sch_parm['sr_startno'];
			}



			if (!$op = $wi_fty->search()) {
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
		for ($i=0; $i< sizeof($op['wi']); $i++)
		{
			$op['wi'][$i]['acc_ap'] = $bom->get_aply($op['wi'][$i]['id'], 'bom_acc');
			$op['wi'][$i]['lots_ap'] = $bom->get_aply($op['wi'][$i]['id'], 'bom_lots');
		}

		$op['msg']= $wi_fty->msg->get(2);				

		page_display($op, '024', $TPL_FTY_BOM);
		break;		
		
//=======================================================
	case "fty_bom_view":
		check_authority('024',"view");
//��Sample order�ƻs[�s�y�O]		
		if(isset($PHP_fty_chk) && $PHP_fty_chk == 0)$f1=$wi_fty->copy_wi($PHP_id);

		$redir_str="fty_bom.php?&PHP_action=bom_view&PHP_id=".$PHP_id;
		redirect_page($redir_str);
//=======================================================


case "bom_view":

if(!$op['wi'] = $wi->get($PHP_id)){    //���X�ӵ� �s�y�O�O��
	$op['msg']= $wi->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}
//  smpl �˥���
if(!$op['smpl'] = $order->get($op['wi']['smpl_id'])){
	$op['msg']= $order->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}

// ���X size_scale ----------------------------------------------
$size_A = $size_des->get($op['smpl']['size']);
$op['smpl']['size_scale']=$size_A['size_scale'];
// �`�N ���ɨS�� size type ��Ʈ�??????
$sizing = explode(",", $size_A['size']);

// wi_qty �ƶq��			
$where_str2 = " WHERE ord_num = '".$op['wi']['wi_num']."' ORDER BY p_etd";
$p_id = $order->get_fields('id', $where_str2,'order_partial');
$p_etd = $order->get_fields('p_etd', $where_str2,'order_partial');
$op['num_colors'] = $op['total'] =0; $op['g_ttl'] = 0;
for($i=0; $i<sizeof($sizing); $i++)$op['size_ttl'][$i]=0;
$k=0;

for($i=0; $i<sizeof($p_id); $i++)
{
	$where_str3 =" WHERE wi_id='".$op['wi']['id']."'  AND p_id='".$p_id[$i]."'";
	$T_wiqty = $wi_fty->wiqty_search(1,$where_str3);
	$num_colors = count($T_wiqty);
	$op['num_colors'] += $num_colors;
	$op['partial'][$i]['size'] = $num_colors;
	$op['partial'][$i]['etd'] = $p_etd[$i];
	
	//-------------------- ��z �� title ��������� ----------------------------------
	for ($j=0;$j<$num_colors;$j++){					
		$op['color'][$k]['name'] = $T_wiqty[$j]['colorway'];
		$T_qty = explode(",", $T_wiqty[$j]['qty']);
		$op['color'][$k]['qty'] = array_sum($T_qty);
		$k++;
	}
	
	// ���X size table-------------------------------------------
	$reply = show_breakdown($T_wiqty,$sizing,$size_A['base_size'],$p_etd[$i]);
	$op['show_breakdown'][$i] = $reply['html'];				
	$op['total'] += $reply['total'];
	for($j=0; $j<sizeof($sizing); $j++)
	{
		$op['g_ttl'] += $reply['size_ttl'][$j];				
		$op['size_ttl'][$j] +=$reply['size_ttl'][$j];
	}				
}

// �ۤ���URL�M�w ------------------------------------------------------
$style_dir	= "./picture/";  
$no_img		= "./images/graydot.gif";
if(file_exists($style_dir.$op['wi']['style_code'].".jpg")){
	$op['wi']['pic_url'] = $style_dir.$op['wi']['style_code'].".jpg";
} else {
	$op['wi']['pic_url'] = $no_img;
}



//  ���X�D�ƥήưO�� --------------------------------------------------------------
$op['lots_NONE']= '';
$where_str = " WHERE smpl_code = '".$op['wi']['style_code']."' ";
$lots_used['lots_use'] = $smpl_lots->search(0,$where_str);  //���X�ӵ� �˥��D�ưO��

if (!is_array($lots_used['lots_use'])) {
	$op['msg'] = $smpl_lots->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}
$op['mat_support'] = 0;
for($i=0; $i<sizeof($lots_used['lots_use']); $i++)
{
	if(	$lots_used['lots_use'][$i]['support'] > 0)$op['mat_support'] = 1;
}
$num_lots_used = count($lots_used['lots_use']);
if (!$num_lots_used){	$op['lots_NONE'] = "1";		}

//  ���X�ƮƥήưO�� --------------------------------------------------------------
 $op['acc_NONE']= '';
$where_str = " WHERE smpl_code = '".$op['wi']['style_code']."' ";
$acc_used['acc_use'] = $smpl_acc->search(0,$where_str);  //���X�ӵ� �˥��D�ưO��
for($i=0; $i<sizeof($acc_used['acc_use']); $i++)
{
	if($acc_used['acc_use'][$i]['support'] > 0)$op['mat_support'] = 1;
}
if (!is_array($acc_used['acc_use'])) {     
	$op['msg'] = $smpl_acc->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}
$num_acc_used = count($acc_used['acc_use']);
if (!$num_acc_used){	$op['acc_NONE'] = "1";		}

//  ���X  BOM  �D�ưO�� --------------------------------------------------------------

$op['pa'] = $op['cust_rcv'] = '';
 $op['bom_lots_NONE']= '';
$where_str = " WHERE wi_id = '".$op['wi']['id']."' ";
$bom_lots = $bom->search_lots(0,$where_str);  //���X�ӵ� bom ��ALL�D�ưO��
for ($pi=0; $pi<sizeof($bom_lots['lots']); $pi++)
{
	if ($bom_lots['lots'][$pi]['ap_mark'] <> '')
	{
		$op['pa'] = 1;
		break;
	}			
}
if (!is_array($bom_lots['lots'])) {
	$op['msg'] = $bom->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}
$num_bom_lots = count($bom_lots['lots']);
if (!$num_bom_lots){	$op['bom_lots_NONE'] = "1";		}

//  ���X  BOM  �ƮưO�� --------------------------------------------------------------
 $op['bom_acc_NONE']= '';
$where_str = " WHERE wi_id = '".$op['wi']['id']."' ";
$bom_acc = $bom->search_acc(0,$where_str);  //���X�ӵ� bom ��ALL�ƮưO��

for ($pi=0; $pi<sizeof($bom_acc['acc']); $pi++)
{
	if ($bom_acc['acc'][$pi]['ap_mark'] <> '' || $op['pa'] == 1)
	{
		$op['pa'] = 1;
		break;
	}
}

if (!is_array($bom_acc['acc'])) {
	$op['msg'] = $bom->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}
$num_bom_acc = count($bom_acc['acc']);
if (!$num_bom_acc){	$op['bom_acc_NONE'] = "1";		}



// --------------- �D�� ------------ ���X $bom_lots_list[]  array....
	$op['bom_lots_list'] = array();
if (!$op['lots_NONE']){   // �˥����D�ƥήưO��---
	$op['bom_lots_list'] =  bom_lots_view($num_lots_used,$bom_lots['lots'],$lots_used['lots_use'],$op['num_colors']);
}   
// ---------------------- end BOM data layout [�D��] ----------------------------

// --------------- �Ʈ� ------------ ���X $bom_acc_list[]  array....
	$op['bom_acc_list'] = array();
if (!$op['acc_NONE']){   // �˥����ƮƥήưO��---
	$op['bom_acc_list'] =  bom_acc_view($num_acc_used,$bom_acc['acc'],$acc_used['acc_use'],$op['num_colors']);
}  
// ---------------------- end BOM data layout [�Ʈ�] ----------------------------



// $op['bom_lots_list'] = $wi_fty->get_lots($op['wi']['wi_num']);
// $op['bom_acc_list'] = $wi_fty->get_acc($op['wi']['wi_num']);

$op['done']=$fils->get_bom_file($op['wi']['wi_num']);
$op['bom_log'] = $bom->get_log($op['wi']['id']);

if (isset($PHP_ex_order))
{
	$op['edit_non']=1;
}

$op['fty_pattern'] = $bom->get_fty_pattern($op['wi']['id']);

page_display($op, '024', $TPL_FTY_BOM_VIEW);
break;

		
		
//=======================================================
	case "fty_wi_edit":

		check_authority('024',"edit");
		
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
		$T_wiqty = $wi_fty->wiqty_search(1,$where_str);

		// ���X size_scale --------
		$size_A = $size_des->get($op['smpl']['size']);
		$op['smpl']['size_scale']=$size_A['size_scale'];
			//????????????????????????????????????? �`�N ���ɨS�� size type ��Ʈ�??????
		$sizing = explode(",", $size_A['size']);
		$op['size'] = $sizing;
		$op['b_size'] = $size_A['base_size'];


		// ���X size table-------------------------------------------
			$op['show_del_breakdown'] = show_del_breakdown($T_wiqty,$sizing,'',$size_A['base_size']);
			$op['edit_breakdown'] = edit_breakdown($sizing,'',$size_A['base_size']);

			$op['msg'] = $wi->msg->get(2);

			// ���X unit�� combo
		$op['unit'] = $arry2->select($APPAREL_UNIT,$op['wi']['unit'],'PHP_unit','select','');  	

		// creat sample type combo box
		$sample_def = $smpl_type->get_fields('smpl_type');   // ���X �˥����O
		$op['smpl_type'] =  $arry2->select($sample_def,$op['wi']['smpl_type'],'PHP_smpl_type','select','');  	
			
		$op['msg'] = $wi->msg->get(2);
		if(isset($PHP_msg))$op['msg'][] = $PHP_msg;
		page_display($op, '024', $TPL_FTY_WI_EDIT);
		break;		

//==================================================================================
	case "edit_add_colorway":
		check_authority('024',"edit");
		// �ǤJ javascript�ǥX���Ѽ� [ colorway : ���M��J�w�� javascript�߬d ]
		$parm['qty']		= $PHP_qty;   // ��javascript�ǥX���}�C�� �۰ʧ令csv�ǥX
		$parm['wi_id']		= $wi_id;
		$parm['colorway']	= $colorway;
		$parm['p_id']	= $_SESSION['partial_id'];

//�R��Bom�������
			$f1=$wi_fty->del_lots($wi_id,1);
			$f2=$wi_fty->del_acc($wi_id,1);

		// �[�J �s��� ----- �[�J wiqty table -----------------
		if(!$T_del =	$wi_fty->add_wiqty($parm)){    
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

		$back_str = "fty_bom.php?&PHP_action=fty_wi_edit&PHP_id=".$wi_id."&PHP_msg=".$message."&PHP_style_code=".$op['wi']['wi_num'];			
		redirect_page($back_str);
		break;

//=======================================================
		case "edit_del_colorway_ajax":

		check_authority('024',"edit");
		// �R�� ���w����� ----------------------------
		if(!$T_del =	$wi_fty->del_wiqty($id)){    
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

		$message = "Success delete wi[".$PHP_wi_num."] colorway[".$colorway."]";
		$log->log_add(0,"31E",$message);
    echo $message."|".$size_edit;
		exit;
		break;

//=fty_bom_add======================================================
    case "fty_bom_add":

		check_authority('024',"add");
		//-------------------- �N BOM �s�y�O show out ------------------
		//  wi �D��
		if(!$op['wi'] = $wi->get($PHP_id)){    //���X�ӵ� �s�y�O�O��
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}
		//  smpl �˥���
		if(!$op['smpl'] = $order->get($op['wi']['smpl_id'])){
					$op['msg']= $order->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}
		// ���X size_scale ----------------------------------------------
		$size_A = $size_des->get($op['smpl']['size']);
		$op['smpl']['size_scale']=$size_A['size_scale'];
		$sizing = explode(",", $size_A['size']);
		//  wi_qty �ƶq��				
			$where_str2 = " WHERE ord_num = '".$op['wi']['wi_num']."' ORDER BY p_etd";
			$p_id = $order->get_fields('id', $where_str2,'order_partial');
			$p_etd = $order->get_fields('p_etd', $where_str2,'order_partial');
			$op['num_colors'] = $op['total'] =0; $op['g_ttl'] = 0;
			for($i=0; $i<sizeof($sizing); $i++)$op['size_ttl'][$i]=0;
			$k=0;
			for($i=0; $i<sizeof($p_id); $i++)
			{
				$where_str3 =" WHERE wi_id='".$op['wi']['id']."'  AND p_id='".$p_id[$i]."'";
				$T_wiqty = $wi_fty->wiqty_search(1,$where_str3);
				$num_colors = count($T_wiqty);
				$op['num_colors'] += $num_colors;
				$op['partial'][$i]['size'] = $num_colors;
				$op['partial'][$i]['etd'] = $p_etd[$i];
				//-------------------- ��z �� title ��������� ----------------------------------
				for ($j=0;$j<$num_colors;$j++){					
					$op['color'][$k]['name'] = $T_wiqty[$j]['colorway'];
					$T_qty = explode(",", $T_wiqty[$j]['qty']);
					$op['color'][$k]['qty'] = array_sum($T_qty);
					$color_qty[$k] = array_sum($T_qty);
					for ($l=0; $l<sizeof($T_qty); $l++) $size_qty[$sizing[$l]][$k] = $T_qty[$l];	
					$k++;
				
				}
				// ���X size table-------------------------------------------
				$reply = show_breakdown($T_wiqty,$sizing,$size_A['base_size'],$p_etd[$i]);
				$op['show_breakdown'][$i] = $reply['html'];				
				$op['total'] += $reply['total'];
				for($j=0; $j<sizeof($sizing); $j++)
				{
					$op['g_ttl'] += $reply['size_ttl'][$j];				
					$op['size_ttl'][$j] +=$reply['size_ttl'][$j];
				}				
			}
		//-----------------------------------------------------------------

		// �ۤ���URL�M�w ------------------------------------------------------
				$style_dir	= "./picture/";  
				$no_img		= "./images/graydot.gif";
			if(file_exists($style_dir.$op['wi']['style_code'].".jpg")){
				$op['wi']['pic_url'] = $style_dir.$op['wi']['style_code'].".jpg";
			} else {
				$op['wi']['pic_url'] = $no_img;
			}

		//  ���X�D�ƥήưO�� --------------------------------------------------------------
		 $op['lots_NONE']= '';
		$where_str = " WHERE smpl_code = '".$op['wi']['style_code']."' ";
		$lots_used['lots_use'] = $smpl_lots->search(0,$where_str);  //���X�ӵ� �˥��D�ưO��

		$num_lots_used = count($lots_used['lots_use']);
		if (!$num_lots_used){	$op['lots_NONE'] = "1";		}

		//  ���X�ƮƥήưO�� --------------------------------------------------------------
		 $op['acc_NONE']= '';
		$where_str = " WHERE smpl_code = '".$op['wi']['style_code']."' ";
		$acc_used['acc_use'] = $smpl_acc->search(0,$where_str);  //���X�ӵ� �˥��D�ưO��

		$num_acc_used = count($acc_used['acc_use']);
		if (!$num_acc_used){	$op['acc_NONE'] = "1";		}

		//  ���X  BOM  �D�ưO�� --------------------------------------------------------------
		 $op['bom_lots_NONE']= '';
		$where_str = " WHERE wi_id = '".$op['wi']['id']."' ";
		$bom_lots = $wi_fty->search_lots(0,$where_str);  //���X�ӵ� bom ��ALL�D�ưO��
		$op['bom_lots'] = $bom_lots['lots'];

		$num_bom_lots = count($bom_lots['lots']);
		if (!$num_bom_lots){	$op['bom_lots_NONE'] = "1";		}
		
		//  ���X  BOM  �ƮưO�� --------------------------------------------------------------
		 $op['bom_acc_NONE']= '';
		$where_str = " WHERE wi_id = '".$op['wi']['id']."' ";
		$bom_acc = $wi_fty->search_acc(0,$where_str);  //���X�ӵ� bom ��ALL�ƮưO��
		$op['bom_acc'] = $bom_acc['acc'];

		$num_bom_acc = count($bom_acc['acc']);
		if (!$num_bom_acc){	$op['bom_acc_NONE'] = "1";		}

		//-------------------- ��z������� ---- for bom table TITLE------------------------------
		if ($num_colors){
			for ($i=0;$i<$num_colors;$i++){
				$op['color'][$i]['name'] = $T_wiqty[$i]['colorway'];
				$T_qty = explode(",", $T_wiqty[$i]['qty']);
				$op['color'][$i]['qty'] = array_sum($T_qty);
				$color_qty[$i] = array_sum($T_qty);
				for ($j=0; $j<sizeof($T_qty); $j++)
				{
					$size_qty[$sizing[$j]][$i] = $T_qty[$j];
				}
			}
		} else {
				$color_qty[] = 0;   // �קK error
				$op['colors_NONE'] = "1";
		}

	// --------------- �D�� ------------ ���X $bom_lots_list[]  array....
		$op['bom_lots_list'] = array();
	    if (!$op['lots_NONE']){   // �˥����D�ƥήưO��---
	      $op['bom_lots_list'] =bom_lots_edit($num_lots_used,$bom_lots['lots'],$lots_used['lots_use'],$op['num_colors'], $PHP_id, $color_qty);
	    } 
	// ---------------------- end BOM data layout [�D��] ----------------------------

	// --------------- �Ʈ� ------------ ���X $bom_acc_list[]  array....
		$op['bom_acc_list'] = array();
		if (!$op['acc_NONE']){   // �˥����ƮƥήưO��---
	   		$op['bom_acc_list'] =bom_acc_edit($num_acc_used,$bom_acc['acc'],$acc_used['acc_use'],$op['num_colors'], $PHP_id, $color_qty,$sizing,$size_qty);	
		} 
	// ---------------------- end BOM data layout [�Ʈ�] ----------------------------

			// ��� ��i�� -----
			if (!$num_colors){	
				$op['total_fields'] = 9;   // BOM table���`����
			}else{
				$op['total_fields'] = 8 + $op['num_colors'];   // BOM table���`����
			}
		
		
		$op['done']=$fils->get_bom_file($op['wi']['wi_num']);
		// ---------------------- end BOM data layout table ----------------------------

		if (isset($PHP_msg))$op['msg'][]=$PHP_msg;
		page_display($op, '024', $TPL_FTY_BOM_ADD);  	    
		break;
		
//=======================================================
    case "do_bom_add":

		check_authority('024',"add");
		// �[�J bom ��
		$dt = decode_date(1);
		
		if ($PHP_mat=="lots"){
			$parm = array(	"wi_id"					=>	$PHP_wi_id,
											"color"					=>	$PHP_color,
											"lots_used_id"	=>	$PHP_mat_use_id,
											"qty"						=>	$PHP_qty,		
											"this_day"			=>	$dt['date']	
				);
			$f1 = $wi_fty->add_lots($parm);   // �[�J
			$K = "Fabric";
		}elseif($PHP_mat=="acc"){
			$mat_use_id = explode('x',$PHP_mat_use_id);
			$parm = array(	"wi_id"				=>	$PHP_wi_id,
											"color"				=>	$PHP_color,
											"acc_used_id"	=>	$mat_use_id[0],
											"qty"					=>	$PHP_qty,		
											"this_day"		=>	$dt['date'],
											"size"				=>	$PHP_size,
				);
			$f1 = $bom->add_acc($parm);   // �[�J
			if ($PHP_size)
			{
				$acc_use = $smpl_acc->get($PHP_mat_use_id);
				if(!strstr($acc_use['use_for'],$PHP_size_des))
				{
					$placement = $acc_use['use_for']." (".$PHP_size_des.")";
					$acc_parm = array ( $mat_use_id[0],'use_for',$placement);
					$smpl_acc->update_field($acc_parm);
				}
			}
			$K = "Accessory";
		}else{
			$op['msg'][] = "Error ! Not point materiel catagory of BOM !";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		if (!$f1) {  // �S�����\��J��Ʈ�
			$op['msg'] = $wi->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

					# �O���ϥΪ̰ʺA
		$op['wi'] = $wi->get($PHP_wi_id);			
		$message = "Successfully add ".$K." record on BOM [ ".$op['wi']['wi_num']." ] ";			
		$log->log_add(0,"37A",$message);
		$op['msg'][] = $message;
		
		$redir_str="fty_bom.php?&PHP_action=fty_bom_add&PHP_id=".$PHP_wi_id."&PHP_msg=".$message;	
	  redirect_page($redir_str);  	    
		break;		
		
		
//=do_bom_del_ajx======================================================
    case "do_bom_del_ajx":

		check_authority('024',"add");
		// �R�� bom ��
		
		if ($PHP_mat=="lots"){
			$T = "Fabric";
			$f1 = $wi_fty->del_lots($PHP_bom_id);   
		}elseif($PHP_mat=="acc"){
			$T = "Accessory";
			$f1 = $wi_fty->del_acc($PHP_bom_id);   // �[�J
		}else{
			$message = "Error ! Don't point delete information of BOM !";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}


			
		//-------------------- �N BOM �s�y�O show out ------------------
		//  wi �D��
		if(!$op['wi'] = $wi->get($PHP_id)){    //���X�ӵ� �s�y�O�O��
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}


		$message = "Delete ".$T." Record on BOM : [ ".$op['wi']['wi_num' ]."]";
				# �O���ϥΪ̰ʺA
		$log->log_add(0,"37D",$message);
		echo $message;
		exit;
		break;		
		

//=======================================================
	case "bom_print":
		check_authority('024',"view");

		if(!$op['wi'] = $wi->get($PHP_id)){    //���X�ӵ� �s�y�O�O��
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}
		//  smpl �˥���
		$op['smpl'] = $order->get($op['wi']['smpl_id']);
		
		$size_A = $size_des->get($op['smpl']['size']);
		$op['smpl']['size_scale']=$size_A['size_scale'];
		$sizing = explode(",", $size_A['size']);
		//-------------------- wi_qty �ƶq�� -------------------------------
			$where_str2 = " WHERE ord_num = '".$op['wi']['wi_num']."' ORDER BY p_etd";
			$p_id = $order->get_fields('id', $where_str2,'order_partial');
			$p_etd = $order->get_fields('p_etd', $where_str2,'order_partial');
			$op['total'] = $color_cut =0;			
			for($i=0; $i<sizeof($p_id); $i++)
			{
				$where_str3 =" WHERE wi_id='".$op['wi']['id']."' AND p_id='".$p_id[$i]."'";
				$T_wiqty = $wiqty->search(1,$where_str3);
				$num_colors = count($T_wiqty);
				// ���X size table-------------------------------------------
				$reply = get_colorway_qty($T_wiqty,$sizing);
				$data[$i] = $reply['data'];
				$op['total'] += $reply['total'];
				$colorway_name[$i] = $reply['colorway'];
				$colorway_qty[$i] = $reply['colorway_qty'];		
				$color_cut +=sizeof($data[$i]);
				$use_title[$p_etd[$i]]['name'] = $reply['colorway'];	
				$use_title[$p_etd[$i]]['qty'] = $reply['colorway_qty'];
				//for($j=0; $j<sizeof($reply['colorway']); $j++) echo $reply['colorway'][$j]."==>".$reply['colorway_qty'][$j]."<BR>";
			}


		//-----------------------------------------------------------------

		// �ۤ���URL�M�w ------------------------------------------------------
				$style_dir	= "./picture/";  
				$no_img		= "./images/graydot.jpg";
			if(file_exists($style_dir.$op['wi']['style_code'].".jpg")){
				$op['wi']['pic_url'] = $style_dir.$op['wi']['style_code'].".jpg";
			} else {
				$op['wi']['pic_url'] = $no_img;
			}
			$bom_lots = $wi_fty->get_lots($op['wi']['wi_num']);
			$bom_acc = $wi_fty->get_acc($op['wi']['wi_num']);


		$op['done']=$fils->get_bom_file($op['wi']['wi_num']);
		$op['bom_log'] = $bom->get_log($op['wi']['id']);

//PDF
	$print_title = "Bill Of Material";
	$op['wi']['bcfm_date']=substr($op['wi']['bcfm_date'],0,10);
	$print_title2 = "VER.".$op['wi']['bom_rev']."  on  ".$op['wi']['bcfm_date'];
	$creator = $op['wi']['bcfm_user'];
	$ll = 0;
	$parm=array( 'PHP_id'		=>	$op['wi']['wi_num'],
				 'prt_ord'				=>	'Order #',
				 'cust'						=>	$op['smpl']['cust_iname'],
				 'ref'						=>	$op['smpl']['ref'],
				 'dept'						=>	$op['smpl']['dept'],
				 'size_scale'			=>	$op['smpl']['size_scale'],
				 'style'					=>	$op['smpl']['style'],
				 'qty'						=>	$op['total'],
				 'unit'						=>	$op['wi']['unit'],
				 'etd'						=>	$op['wi']['etd'],
				 'pic_url'				=>	$op['wi']['pic_url'],
				 );

	$ary_title = $parm;
	include_once($config['root_dir']."/lib/class.pdf_fty_bom_L.php");

	$pdf=new PDF_fty_bom_L('L','mm','A4');
	$pdf->AddBig5Font();	
	$pdf->Open();
	$pdf->AddPage();
	$pdf->SetFont('Arial','B',14);
	$ll = 0;

		$Y = $pdf->getY();

		$img_size = GetImageSize($op['wi']['pic_url']);
		
		if ($img_size[0] > $img_size[1])
		{
			$pdf->Image($op['wi']['pic_url'],10,50,40,0);
		}else{
			$pdf->Image($op['wi']['pic_url'],10,50,0,40);
		}
		$pdf->ln();
		
		$Y = $pdf->getY();
		$X = $pdf->getX();
// �]�w colorway���ƶq�� - ��쪺�e  [size breakdown]
// �� 80~200 �O�d 30�� colorway���e �䥦 90���t���ƶq��
	$num_siz = count($sizing);
	$w_qty = intval(175/ ($num_siz+1));
	$w = array('50');  // �Ĥ@�欰 50�e
	for ($i=0;$i<=$num_siz;$i++){
		array_push($w, $w_qty);
	}

	for($i=0; $i<sizeof($p_etd); $i++)
	{
		$header = array_merge(array('Colorway') , $sizing);	
		$table_title = "Size Breakdown";
		if(sizeof($p_etd) > 1)$table_title .= '[ ETD : '.$p_etd[$i].' ]';
		$R = 100 ;
		$G = 85 ;
		$B = 85 ;
		$Y = 50+($i*(sizeof($data[$i])+1)*8);
		if (count($sizing)){
			$pdf->Table_1(50,$Y,$header,$data[$i],$w,$table_title,$R,$G,$B,$size_A['base_size']);
		}else{
			$pdf->Cell(10,70,'there is no sizing data exist !',1);
		}
		//$pdf->ln();
		$ll+=sizeof($data)+2;	
	}
//�D��
		$pdf->ln();
		$header1 = array('Fabric ','Consump.','size','','Placement');
		$title = 'Fabric usage';
		$w1 =  array(28,15,5,0.5,60);//26
		$pdf->Table_2_title($title,$header1,$w1,$use_title,100,85,85);
		$ll+=4;
	
		for($i=0; $i<sizeof($bom_lots); $i++)
		{

			if(isset($bom_lots[$i]['color_use']))
			{
			$ll+=2;
			if ($ll >= 20)
			{	    
				$ll=0;
				$pdf->AddPage();
				$pdf->SetXY(10,50);
				$pdf->Table_2_title($title,$header1,$w1,$use_title,100,85,85);
			}
			$bom_lots[$i]['des_m']='';
			if($bom_lots[$i]['weight'])$bom_lots[$i]['des_m'] .="   Weight : ".$bom_lots[$i]['weight'].";";
			if($bom_lots[$i]['width'])$bom_lots[$i]['des_m'] .="  c.Width : ".$bom_lots[$i]['width'].";";
			if($bom_lots[$i]['des'])$bom_lots[$i]['des_m'] .="   Supplier's # : ".$bom_lots[$i]['des'].";";
			if($bom_lots[$i]['comp'])$bom_lots[$i]['des_m'] .="  Construction : ".$bom_lots[$i]['comp'].";";
			if($bom_lots[$i]['des_m'])$bom_lots[$i]['des_m'] = substr($bom_lots[$i]['des_m'],0,-1);		
			$pdf->Table_2($bom_lots[$i],$w1);
			}
		}

		$ll+=4;
		if ($ll >= 20)
		{	    
			$ll=0;
			$pdf->AddPage();
			$pdf->SetXY(10,50);		
		}		
//�Ʈ�	
		$header1 = array('Accessory ','Consump.','size','','Placement');
		$title = 'Accessory usage';
		$w1 =  array(28,15,5,0.5,60);//26
		$pdf->Table_2_title($title,$header1,$w1,$use_title,100,85,85);
		for($i=0; $i<sizeof($bom_acc); $i++)
		{
			$ll+=2;
			if ($ll >= 20)
			{	    
				$ll=0;
				$pdf->AddPage();
				$pdf->SetXY(10,50);
				$pdf->Table_2_title($title,$header1,$w1,$use_title,100,85,85);
			}
			$bom_acc[$i]['des_m']='';
			if($bom_acc[$i]['specify']) $bom_acc[$i]['des_m'] = "Construction : ".$bom_acc[$i]['specify'];
			$pdf->Table_2($bom_acc[$i],$w1);
			
		}				

	$name=$op['wi']['wi_num'].'_bom.pdf';
	$pdf->Output($name,'D');









//-------------------------------------------------------------------------

}   // end case ---------

?>
