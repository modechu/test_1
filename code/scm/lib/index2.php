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
#
#	  order status :
#		status == 0			waitt'g for IE rec.
#		status == 1			wait for submit
#		status == 2			waitt'g for CFM
#		status == 3			waitt'g for APV
#		status == 4			APVED
#		status == 5			Reject
#		status == 6			SCHDL CFMing
#		status == 7			SCHDL CFMed
#		status == 8			PRODUCING
#		status == 10		= FINISHED =
#		status == 12		== SHIPPED ==
#		
#   2005/08/31 ��Ѹ�Ʈw�� su [�H ie �p���J ]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


//	case "fty_mon_schedule":	job 81-2-1	FTY SCHEDULE �u�t��Ʋ� 

//	case "sales_forecast":		job 91  �~�ȹw��
//	case "forecast_search":		job 91S  �w��search
//	case "forecast_add":		job 91A  �w�� ADD
//	case "do_forecast_add":		job 91A  �w�� ADD
//	case "forecast_update":		job 91A  �w���s
//	case "do_forecast_update":	job 91E  �w���s





#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//		job 39  [�Ͳ��s�y][�Ͳ�����]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//	case "shp_out":			job 39-4-1    SHIPPING �����
//	case "fty_etp_monthy":	job 39-3-1    FTY SCHEDULE �Ʋ� �����
//	case "etp_monthy":		job 39-2-1    SALES ETP �����
//	case "monthy_daily_ouput":	job 39-1-2    �C�鲣�X�q �����
//	case "daily_ouput":			job 39-1-3    �C�鲣�X�q ����
//	case "order_output":		job 39-1-4     �q�� ���X�q�O�� ����

//	case "schedule_excel":		job 36X    �T�{�q�� �O���ন excel ��
//	case "ie_excel":			job 35X    �Ͳ��O���ন excel ��
//	case "pdt_excel":			job 38X    �Ͳ� �O���ন excel ��
//	case "order_excel":			job 101X    �q�� �O���ন excel ��


//	case "IE_record":			job 35   IE �O��
//	case "ie_search":
//	case "ie_record_view":
//	case "add_ie1":
//	case "cfm_ie1":
//	case "add_ie2":
//	case "cfm_ie2":
//	case "smpl_apv_record":		job 105  �˥��T�{ �O��
//	case "smpl_apv_rec_search":
//	case "smpl_apv_rec_view":
//	case "add_smpl_apv_date":
//	*** 8 ********* CAPACITY **************
//	case "capacity_search":		job 81  �Ͳ�����
//	case "do_capacity_search":
//	case "capacity_add":
//	case "do_capacity_add":
//	case "capacity_update":
//	case "do_capacity_edit":
//	*** 3 ********* PRODUCTIOIN ***********
//	case "production":			job 38  �Ͳ��O��
//	case "production_search":
//	case "add_production":
//	case "finish_production":
//  case "re_open_finish":
//	case "del_production":
//	case "del_shipping":

//	case "shipping":			job 39  �X�f�O��
//	case "shipping_search":
//	case "add_shipping":


//	case "ord_schedule":		 job 106  �q�� �i��
//	case "ord_schedule_search":
//	case "ord_schedule_view":
//	case "cfm_pd_schedule":		job 37  �q �� �� �{  �T�{ 			
//	case "cfm_schedule_view":
//	case "do_cfm_pd_schedule":
//	case "material_schedule":	job 104  �D�Ʈƶi��
//	case "mat_schedule_search":
//	case "mat_schedule_view":
//	case "do_mat_schedule":
//	case "mat_ship":
//	case "macc_ship":
//	case "acc_ship":
//	case "pd_schedule":			job 36  �Ͳ��Ƶ{
//	case "schedule_search":
//	case "schedule_view":
//	case "do_pd_schedule":
//	
//	case "ag":					AG  �˥��i�װl��  [8, 3 ]
//	case "ag_search":
//	case "ag_add":
//	case "fabric_excel":
//	case "fabric_print_search":
//	case "do_fabric_print_search":
//	case "fabric_label_print":
//	case "fabric_label":
//	case "test2":
//	case "bom":				BOM  �Ͳ��ή�  [4, 1 ]
//	case "bom_print":
//	case "bom_search_wi":
//	case "bom_add":
//	case "do_bom_add":
//	case "bom_view":
//	case "do_bom_del":
//	case "wi":				job 31  �s�@�O
//	case "wi_search":
//	case "wi_add":
//	case "do_wi_add_1":
//	case "del_colorway":    // �s�W�s�@�O
//	case "add_colorway":      // �s�W�s�@�O
//	case "do_wi_add_qty":
//	case "do_wi_add_2":
//	case "wi_view":
//	case "wi_edit":
//	case "edit_del_colorway":
//	case "edit_add_colorway":
//	case "do_wi_edit":
//	case "wi_del":
//	case "ti":				 �Ͳ�����
//	case "ti_search_wi":   //  ����X�s�y�O.......
//	case "ti_add":
//	case "ti_view":
//	case "do_ti_add":
//	case "do_ti_del":
//	case "show_size_des_search":		====== ���F �s�W�s�y�O �ϥ�
//	case "size_des":		 size_des ����
//	case "size_des_add":
//	case "do_size_des_add":
//	case "size_des_view":
//	case "size_des_edit":
//	case "size_des_edit":
//	case "size_des_del":


//	case "smpl_acc":		�˥��Ʈ� 
//	case "smpl_acc_search":
//	case "smpl_acc_add":
//	case "smpl_acc_view":
//	case "show_acc_search":
//	case "do_smpl_acc_add":
//	case "do_smpl_acc_del":

//	case "smpl_mat":		�˥��D��
//	case "smpl_mat_search":
//	case "smpl_mat_add":
//	case "smpl_mat_view":
//	case "show_lots_search":
//	case "do_smpl_lots_add":
//	case "do_smpl_lots_del":

//	case "smpl":
//	case "smpl_search":
//	case "smpl_add":
//	case "do_smpl_add":
//	case "smpl_copy":
//	case "copy_smpl_add":
//	case "smpl_view":
//	case "smpl_edit":
//	case "do_smpl_edit":
//	case "smpl_del":
//	case "show_smpl_search":
//	case "acc":
//	case "acc_add":
//	case "do_acc_search":
//	case "do_acc_add":
//	case "acc_view":
//	case "acc_edit":
//	case "do_acc_edit":
//	case "acc_del":
//	case "lots":
//	case "do_lots_search":
//	case "lots_add":
//	case "do_lots_add":
//	case "lots_view":
//	case "lots_edit":
//	case "do_lots_edit":
//	case "lots_del":
//	case "supl":
//	case "supl_search":
//	case "supl_add":
//	case "do_supl_add":
//	case "supl_view":
//	case "supl_edit":
//	case "do_supl_edit":
//	case "supl_del":

//	case "cust":			job 11  cust
//	case "cust_add":
//	case "do_cust_add":
//	case "cust_view":
//	case "cust_edit":
//	case "do_cust_edit":
//	case "cust_del":
//	case "user":			job 62   USER  
//	case "user_add":
//	case "do_user_add":
//	case "cfm_user_add":
//	case "user_view":
//	case "user_edit":
//	case "do_user_edit":
//	case "user_disable":
//	case "user_enable":
//	case "user_del":
//	case "team":			job 61  team
//	case "team_add":
//	case "do_team_add":
//	case "team_view":
//	case "team_edit":
//	case "do_team_edit":
//	case "team_del":
//	case "fabric":			JOB  9-1   ��s�}�o [ ���ƶ}�o]
//	case "fabric_search":
//	case "fabric_add":
//	case "do_fabric_add":
//	case "fabric_view":
//	case "fabric_edit":
//	case "do_fabric_edit":
//	case "fabric_del":
//	case "supl_type":
//	case "do_supl_type_add":
//	case "supl_type_update":
//	case "do_supl_type_updat":
//	case "supl_type_del":
//	case "dept":
//	case "do_dept_add":
//	case "dept_update":
//	case "do_dept_updat":
//	case "dept_del":
//	case "smpl_type":
//	case "do_smpl_type_add":
//	case "smpl_type_update":
//	case "do_smpl_type_updat":
//	case "smpl_type_del":
//	case "style_type":
//	case "do_style_type_add":
//	case "style_type_update":
//	case "do_style_type_updat":
//	case "style_type_del":
//	case "season":
//	case "do_season_add":
//	case "season_update":
//	case "do_season_updat":
//	case "season_del":


#		system part
//	case "login":
//	case "exe_login":         (default;)
//	case "main":							notice
//	case "do_login":
//	case "changePass":
//	case "do_change_pass":
//	case "logout":
//	case "change_passwd":
//	case "change_passwd_step2":

//	case "show_page":
//	case "display_page":
//	case "log_admin":			   job 63  system log //  �e�{  admin log �D��
//	case "log_admin_show":		   �d�M admin log �ɤ��e
//	case "log_admin_cfm_delete":   �R�� admin log ��
//	case "log_admin_delete":	   �R�� admin log ��
//	
#		order part
//	case "order_entry":			job 101  �q ��
//	case "order_search":
//	case "order_view":			job 101 oreder record view
//	case "order_add":			job 101 order record add
//	case "do_order_add":
//	case "order_edit":
//	case "do_order_edit":
//	case "send_order_cfm":
//	case "order_cfm":			job 102  �q �� �T �{  [  �������v �� job ]
//	case "order_cfm_view":
//	case "do_order_cfm":
//	case "reject_order_cfm":
//	case "order_apv":			job 103  �q �� �� �i 
//	case "order_apv_view":
//	case "reject_order_apv":	

//	case "revise_order":		job 101R   revise order ��s�q�� �O��
//	case "do_order_revise":		job 101R   do revise order �O��
//	case "revise_order_view":	
//  case "shift_order":		�ഫ�q�� �ӻs�u�t	shift order
//  case "do_order_shift":

//	case "apply": ����

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

require_once "config.php";
require_once "config.admin.php";

$PHP_SELF = $_SERVER['PHP_SELF'];

$perm = $GLOBALS['power'];

require_once "init.object.php";

$op = array();

//$ACC = $acc->get_acc_name();



switch ($PHP_action) {
//=======================================================
    case "test":
 $im =  imagecreatetruecolor ( 300, 200);  
$black = imagecolorallocate ($im,  0, 0, 0 );  
$white = imagecolorallocate ($im,  255, 255, 255 );  

imagefilledrectangle ($im,10, 0,399,99 ,$white);  
imagerectangle ($im,20, 20,250,190 ,$black);  

header ("Content-type: image/png" );  
imagepng ($im); 

exit;

	require_once('class.schedule.graph.php');	
	$SCH = new ScheduleGraph;
	$SCH->title = "test schedule graph";
		
	echo '<img src="class.schedule.graph.php?' . $SCH->create_query_string().'" />';

	$im = imagecreate(50,50);
exit;		
		$time1 ="2007-04-11";
		$time2 ="2007-04-13";

		if ($time1 > $time2){
			echo "<br>time1 ===>".$time1." grater then  time2 ==>".$time2;
			$diff = countDays($time2, $time1);
			echo "  for differnet days ===>".$diff;
		}elseif($time2 > $time1){
			echo "<br>time2 ===>".$time2." > grater then  time1 ==>".$time1;
			$diff = countDays($time2, $time1);
			echo "  for differnet days ===>".$diff;
		}else{
			echo "no one is bigger.....";
		}



	exit;
	
	
	function filter_z_value($val) {

 		return ($val > 0);
	} // end func

		$arr = array(  'monday'=>100,
						'tuesday' =>50,
						'wednesday'=>60,
						'thersday'=>0,
						'friday' =>30,
						'saterday'=>0,
						'sunday'=>70,
			);
		$result = array_filter($arr, 'filter_0_value');
		print_r($arr);
		echo "<br><br>";
		print_r($result);
break;


#++++++++++++++    SAMPLE ORDER +++++++++++++  2006/11/08  +++++++++++++++++
#		 job 54    ���ʰO�� 
#++++++++++++++++++++++++++++++++++++++++++++  2006/11/09  +++++++++++++++++
#		case "apply":						job 51
#		case "apply_search_bom":			job 51
#		case "apply_add_search":			job 51
#		case "do_apply_add_search":			job 51
#		case "apply_wi_view":				job 51
#		case "apply_bom_view":				job 51
#		case "apply_ext_bom_view":			job 51
#		case "apply_add":					job 51
#		case "get_ship_ajax":				job 51
# 		case "apply_special_add":			job 51
#	 	case "apply_special_add_ajax":		job 51
#		case "apply_ext_add":				job 51
#		case "apply_spc_add_det":			job 51
#		case "apply_add_det":				job 51
#		case "del_ap_det_ajax":				job 51
#		case "del_ap_spc_ajax":				job 51
#		case "do_apply_del":				job 51
#		case "do_apply_add":				job 51
#		case "do_apply_spc_add1":			job 51
#		case "do_apply_spc_add2":			job 51
#		case "apply_edit":					job 51
#		case "mat_edit":					job 51
#		case "do_apply_edit":				job 51
#		case "apply_search":				job 51
#		case "pa_print":					job 51
#		case "apply_view":					job 51
#		case "do_apply_logs_add":			job 51
#		case "apply_submit":				job 51
#		case "apply_cfm_search":			job 52
#		case "apply_cfm_view":				job 52
#		case "do_apply_cfm":				job 52
#		case "reject_apply_cfm":			job 52
#		case "apply_apv_search":			job 53
#		case "apply_apv_view":				job 53
#		case "do_apply_apv":				job 53
#		case "reject_apply_apv":			job 53
#		case "apply_revise":				job 51
#		case  "bom_close_pa":				jbo 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apply":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

    case "apply":
		check_authority(5,1,"view");
		$where_str = $manager = $manager_v = $dept_id = '';
		$dept_ary = array();
		$sales_dept_ary = get_sales_dept(); // ���X �~�Ȫ����� [���tK0] ------
		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];   // �P�w�i�J����������
		
		$op['factory'] = $arry2->select($FACTORY,'','PHP_factory','select','');
		
		for ($i=0; $i<count($sales_dept_ary);$i++){
			if($user_dept == $sales_dept_ary[$i]){

				// �p�G�O�~�ȳ��i�J �hdept_code ���w�ӷ~�ȳ�---
				$dept_id = $sales_dept_ary[$i];  
			}
		}

		if (!$dept_id) {    // ���O�~�ȳ��H[�]���t K0 ���H ]�i�J��
			$manager = 1;
			$manager_v = 1;
			//�~�ȳ��� select���
			$dept_ary = $arry2->select($sales_dept_ary,"","PHP_dept_code","select","");  
		} else {
			$where_str = " WHERE dept = '".$dept_id."' ";			
		}

		$where_str=$where_str."order by cust_s_name"; //��cust_s_name�Ƨ�
		if(!$cust_def = $cust->get_fields('cust_init_name',$where_str)){;  //���X�Ȥ�²��
			$op['msg'][] = "sorry! there is no any customer record in your team, please add customer first!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//���X�Ȥ�N��
		for ($i=0; $i< sizeof($cust_def); $i++)
		{
			$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//�H [�Ȥ�N��-�Ȥ�²��] �e�{
		}

		$op['cust_select'] =  $arry2->select($cust_value,'','PHP_cust','select','',$cust_def_vue); 
		$op['factory_select'] = $arry2->select($SHIP,'','PHP_SCH_fty','select','');
		$op['cust_sch_select'] =  $arry2->select($cust_value,'','PHP_SCH_cust','select','',$cust_def_vue); 


		$op['manager_flag'] = $manager;
		$op['manager_v_flag'] = $manager_v;
		$op['dept_id'] = $dept_id;
		$op['dept_ary'] = $dept_ary;

		$op['msg'] = $order->msg->get(2);
		  	
		page_display($op, 5, 1, $TPL_APLY);			    	    
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apply_search_bom":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "apply_search_bom":   //  ����X�s�y�O.......

		check_authority(5,1,"view");
		
			if (!isset($PHP_full))$PHP_full = 0;

			if (!$op = $bom->search_cfm()) {
				$op['msg']= $wi->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			// op �Q�M���F �ݦA�P�_�@�� 
			for ($i=0; $i< sizeof($op['wi']); $i++)
			{
				$op['wi'][$i]['acc_ap'] = $bom->get_aply($op['wi'][$i]['id'], 'bom_acc');
				$op['wi'][$i]['lots_ap'] = $bom->get_aply($op['wi'][$i]['id'], 'bom_lots');
			}
			
			

		$op['msg']= $bom->msg->get(2);				
		$back_str="&PHP_dept_code=".$PHP_dept_code."&PHP_cust=".$PHP_cust."&PHP_num=".$PHP_num."&PHP_full=".$PHP_full;
		if ($GLOBALS['SCACHE']['ADMIN']['dept'] == 'SA') 
		{
			$op['close_flag'] = 1;
		}
		
		$op['back_str']=$back_str;
		if (isset($PHP_special))
		{
			page_display($op, 5,1, $TPL_APLY_SPC_BOM_LIST);
		}else{
			page_display($op, 5,1, $TPL_APLY_BOM_LIST);
		}
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apply_add_search":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "apply_add_search":   //  ����X�s�y�O.......

		check_authority(5,1,"view");
			
		$op['dept_id'] = $PHP_dept;
		$op['cust'] = $PHP_cust;
		$op['item'] = $PHP_item;
		page_display($op, 5,1, $TPL_APLY_ADD_SCH);
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_apply_add_search":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_apply_add_search":   //  ����X�s�y�O.......

		check_authority(5,1,"view");

			if (!$op = $bom->search_cfm()) {
				$op['msg']= $wi->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			// op �Q�M���F �ݦA�P�_�@�� 
			for ($i=0; $i< sizeof($op['wi']); $i++)
			{
				$op['wi'][$i]['acc_ap'] = $bom->get_aply($op['wi'][$i]['id'], 'bom_acc');
				$op['wi'][$i]['lots_ap'] = $bom->get_aply($op['wi'][$i]['id'], 'bom_lots');
			}
			
			

		$op['msg']= $bom->msg->get(2);				
		$back_str="&PHP_dept_code=".$PHP_dept_code."&PHP_cust=".$PHP_cust."&PHP_num=".$PHP_num;
		$op['back_str']=$back_str;
		$op['dept_id'] = $PHP_dept_code;
		$op['cust'] = $PHP_cust;

		$op['item'] = $PHP_item;
		page_display($op, 5,1, $TPL_APLY_ADD_SCH);
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apply_wi_view":			job 54
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "apply_wi_view":

		check_authority(5,1,"view");

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
		$where_str = " WHERE wi_id='".$op['wi']['id']."' ";
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

		//  ���X  BOM  �D�ưO�� --------------------------------------------------------------
		 $op['bom_lots_NONE']= '';
	 	 $op['lots_use'] = $apply->get_lots_det($op['wi']['id']);  //���X�ӵ� bom ��ALL�D�ưO��
		 $num_bom_lots = count($op['lots_use']);
		 if (!$num_bom_lots){	$op['bom_lots_NONE'] = "1";		}

		//  ���X  BOM  �ƮưO�� --------------------------------------------------------------
		$op['bom_acc_NONE']= '';
		$op['acc_use'] = $apply->get_acc_det($op['wi']['id']);  //���X�ӵ� bom ��ALL�ƮưO��
		$num_bom_acc = count($op['acc_use']);
		if (!$num_bom_acc){	$op['bom_acc_NONE'] = "1";		}

		$op['msg'] = $wi->msg->get(2);
		if (isset($PHP_msg)) $op['msg'][] = $PHP_msg;
		if (isset($PHP_item))
		{
			$back_str="?PHP_action=do_apply_add_search&PHP_sr_startno=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_cust=".$PHP_cust."&PHP_num=".$PHP_num."&PHP_item=".$PHP_item;
			$op['item'] = $PHP_item;
			$op['back_str']=$back_str;
			page_display($op, 3, 5, $TPL_APLY_VIEW_WI_SUB);		
		}else{
			$back_str="?PHP_action=apply_search_bom&PHP_sr_startno=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_cust=".$PHP_cust."&PHP_num=".$PHP_num."&PHP_full=".$PHP_full;
			$op['back_str']=$back_str;
			page_display($op, 5, 1, $TPL_APLY_VIEW_WI);		
		}
		break;
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	 case "apply_bom_view":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "apply_bom_view":

		check_authority(5,1,"view");
		//-------------------- �N �s�y�O show out ------------------

		//  wi �D�� + smpl �˥���
		$op['wi'] = $wi->get($PHP_id);
		$op['smpl'] = $order->get($op['wi']['smpl_id']);
		//-------------------- wi_qty �ƶq�� -------------------------------
		$where_str = " WHERE wi_id='".$op['wi']['id']."' ";
		$T_wiqty = $wiqty->search(1,$where_str);

		$num_colors = count($T_wiqty);
		$op['num_colors'] = $num_colors;
		// ���X size_scale ----------------------------------------------
		$size_A = $size_des->get($op['smpl']['size']);
		$op['smpl']['size_scale']=$size_A['size_scale'];
			//????????????????????????????????????? �`�N ���ɨS�� size type ��Ʈ�??????
		$sizing = explode(",", $size_A['size']);

			// ���X size table-------------------------------------------
		$reply = show_breakdown($T_wiqty,$sizing,$size_A['base_size']);
		$op['show_breakdown'] = $reply['html'];
		$op['total'] = $reply['total'];   // �`���
		//-----------------------------------------------------------------

		// �ۤ���URL�M�w ------------------------------------------------------
				$style_dir	= "./picture/";  
				$no_img		= "./images/graydot.gif";
			if(file_exists($style_dir.$op['wi']['style_code'].".jpg")){
				$op['wi']['pic_url'] = $style_dir.$op['wi']['style_code'].".jpg";
			} else {
				$op['wi']['pic_url'] = $no_img;
			}

		//  ���X  BOM  �D�ưO�� --------------------------------------------------------------
		 $op['bom_lots_NONE']= '';
	 	 $op['bom_lots_list'] = $apply->get_lots_det($op['wi']['id']);  //���X�ӵ� bom ��ALL�D�ưO��
		 $num_bom_lots = count($op['bom_lots_list']);
		 if (!$num_bom_lots){	$op['bom_lots_NONE'] = "1";		}

		//  ���X  BOM  �ƮưO�� --------------------------------------------------------------
		$op['bom_acc_NONE']= '';
		$op['bom_acc_list'] = $apply->get_acc_det($op['wi']['id']);  //���X�ӵ� bom ��ALL�ƮưO��
		$num_bom_acc = count($op['bom_acc_list']);
		if (!$num_bom_acc){	$op['bom_acc_NONE'] = "1";		}


			$back_str="?PHP_action=apply_search_bom&PHP_sr=".$PHP_sr_startno."&PHP_dept_code=".$PHP_dept_code."&PHP_cust=".$PHP_cust."&PHP_num=".$PHP_num."&PHP_full=".$PHP_full;
			$op['back_str']=$back_str;

		$op['mat_id'] = $PHP_bom_id;
		$op['mat_cate'] = $PHP_mat_cat;
//		echo $op['mat_cate'];
		page_display($op, 5,1, $TPL_APLY_SPC_VIEW_BOM);
		break;		
		

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apply_ext_bom_view":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "apply_ext_bom_view":

		check_authority(5,1,"view");

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
		$where_str = " WHERE wi_id='".$op['wi']['id']."' ";
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

		//  ���X  BOM  �D�ưO�� --------------------------------------------------------------
		 $op['bom_lots_NONE']= '';
	 	 $op['lots_use'] = $apply->get_lots_det($op['wi']['id']);  //���X�ӵ� bom ��ALL�D�ưO��
		 $num_bom_lots = count($op['lots_use']);
		 if (!$num_bom_lots){	$op['bom_lots_NONE'] = "1";		}

		//  ���X  BOM  �ƮưO�� --------------------------------------------------------------
		$op['bom_acc_NONE']= '';
		$op['acc_use'] = $apply->get_acc_det($op['wi']['id']);  //���X�ӵ� bom ��ALL�ƮưO��
		$num_bom_acc = count($op['acc_use']);
		if (!$num_bom_acc){	$op['bom_acc_NONE'] = "1";		}


		//  ���X  BOM  �[�ʰO�� --------------------------------------------------------------
		$op['ext_mat_NONE']= '';
		$op['ext_mat'] = $po->get_ext_ap($op['wi']['wi_num']);  //���X�ӵ� bom ��ALL�[�ʰO��
		$num_ext_mat = count($op['ext_mat']);
		if (!$num_ext_mat){	$op['ext_mat_NONE'] = "1";		}


		$op['msg'] = $wi->msg->get(2);		
		
		if (isset($PHP_msg))$op['msg'][]=$PHP_msg;

		$back_str="?PHP_action=apply_search_bom&PHP_sr_startno=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_cust=".$PHP_cust."&PHP_num=".$PHP_num."&PHP_special=1";
		$op['back_str']=$back_str;
		page_display($op, 5, 1, $TPL_APLY_EXT_VIEW_BOM);		

		break;
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apply_add":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

    case "apply_add":
		check_authority(5,1,"add");		
		
			if(!$ord = $wi->get($PHP_wi_id)){    //���X�ӵ� �s�y�O�O�� ID
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
													'ap_special'	=>	0,
													);

		$head="PA".date('y')."-";	//A+���+����=���ʳ�}�Y
		$parm_madd['ap_num']=$apply->get_no($head,'ap_num','ap');	//���o���ʪ��̫�s��
		$new_id = $apply->add($parm_madd);	//�s�WAP��Ʈw
		
		
		if ($PHP_item == 'lots')
		{
			$parm_mat = array (	'field_name'	=>	'vendor1',		//�D�ƥ[�J�����Ӹ��
													'field_value'	=>	$PHP_vendor,
													'code'				=>	$PHP_mat_code
												);
			$lots->update_field_code($parm_mat);

			$parm_mat = array (	'field_name'	=>	'price1',			//�D�ƥ[�J������
													'field_value'	=>	$PHP_price,
													'code'				=>	$PHP_mat_code
												);
			$lots->update_field_code($parm_mat);
			
			$where_str = "WHERE id = ". $PHP_mat_id;
			$mat=$bom->search('bom_lots', $where_str);

			$fd='bom_lots';	
			$mat_cat='l';		
		}else{
			$parm_mat = array (	'field_name'	=>	'vendor1',		//�Ʈƥ[�J�����Ӹ��
													'field_value'	=>	$PHP_vendor,
													'code'				=>	$PHP_mat_code
												);
			$acc->update_field_code($parm_mat);

			$parm_mat = array (	'field_name'	=>	'price1',			//�Ʈƥ[�J������
													'field_value'	=>	$PHP_price,
													'code'				=>	$PHP_mat_code
												);
			$lots->update_field_code($parm_mat);
			
			$where_str = "WHERE id = ". $PHP_mat_id;
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
			$parm_dadd = array( 'ap_num'	=>	$parm_madd['ap_num'],
													'mat_id'	=>	$mat[$i]['id'],
													'eta'			=>	$PHP_eta,
													'qty'			=>	$qty,
													'unit'		=>	$PHP_unit,
													'mat_cat'	=>	$mat_cat,
													);

			$parm_mat = array($mat[$i]['id'],'ap_mark',$parm_madd['ap_num'],$fd);
			$f1 = $apply->add_det($parm_dadd);	//�s�WAP��Ʈw
			$f2 = $bom->update_field($parm_mat);			
		}


	
		$op = $apply->get($parm_madd['ap_num']);
		
//�O���s�Wmessage
		$message = "Successfully Appended PA : ".$parm_madd['ap_num'];
		$op['msg'][] = $message;
		$log->log_add(0,"51A",$message);

//�զX�e��List
		if (isset($op['ap_det']))
		{
			$op['ap_det2'] = $apply->group_ap($op['ap_det']);	//�զP���ʩ���
		}

		
		$op['CURRENCY_select'] = $arry2->select($CURRENCY,'','PHP_CURRENCY','select','');
		$op['FACTORY_select'] = $arry2->select($SHIP,'','PHP_FACTORY','select',"ship_show(this)");

		for ($i=0; $i< 4; $i++)
		{
			if ($op['ap']['dm_way'] == $dm_way[1][$i]) $op['ap']['dm_way']=$dm_way[0][$i];
		}

		$op['dm_way_select'] = $arry2->select($dm_way[0],$op['ap']['dm_way'],"PHP_dm_way","select","");

		page_display($op, 5, 1, $TPL_APLY_ADD);			    	    
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	 case "get_ship_ajax":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "get_ship_ajax":
	if ($PHP_ship)
	{
		echo $SHIP_TO[$PHP_ship]['Name'].'|'.$SHIP_TO[$PHP_ship]['Addr'].'|'.$SHIP_TO[$PHP_ship]['TEL'].'|'.$SHIP_TO[$PHP_ship]['FAX'].'|'.$SHIP_TO[$PHP_ship]['Attn'];
	}else{
		echo '||||';
	}

	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "apply_special_add":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

    case "apply_special_add":
		check_authority(5,1,"add");		
		
		if ($PHP_ap_num == '')
		{	
				if(!$ord = $wi->get(0,$PHP_wi_num)){    //���X�ӵ� �s�y�O�O�� ID
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
														'ap_special'	=>	1,
														);
				$PHP_sup=$PHP_vendor;

			$head="PA".date('y')."-";	//A+���+����=���ʳ�}�Y
			$parm_madd['ap_num']=$apply->get_no($head,'ap_num','ap');	//���o���ʪ��̫�s��
			$new_id = $apply->add($parm_madd);	//�s�WAP��Ʈw
			$PHP_ap_num=$parm_madd['ap_num'];
		}
		
		if ($PHP_item == 'lots')
		{
			$mat_cat='l';	
			$parm_mat = array (	'field_name'	=>	'vendor1',
													'field_value'	=>	$PHP_sup,
													'code'				=>	$PHP_mat_code
												);
			$lots->update_field_code($parm_mat);

			$parm_mat = array (	'field_name'	=>	'price1',
													'field_value'	=>	$PHP_price,
													'code'				=>	$PHP_mat_code
												);
			$lots->update_field_code($parm_mat);

	
		}else{			
			$mat_cat='a';		
			$parm_mat = array (	'field_name'	=>	'vendor1',
													'field_value'	=>	$PHP_sup,
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
													'mat_id'	=>	$PHP_mat_id,
													'eta'			=>	$PHP_eta,
													'qty'			=>	$PHP_qty,
													'unit'		=>	$PHP_unit,
													'mat_cat'	=>	$mat_cat,
													);
		$f1 = $apply->add_det($parm_dadd);	
	
		$op = $apply->get($PHP_ap_num);
		
		$message = "Successfully appended PA : ".$PHP_ap_num;
		$op['msg'][] = $message;
		$log->log_add(0,"54A",$message);
		
		$op['wi_num'] = $PHP_wi_num;
		
	//  wi �D�� + smpl �˥���
		$op['wi'] = $wi->get(0,$PHP_wi_num);
		$op['smpl'] = $order->get($op['wi']['smpl_id']);
		//-------------------- wi_qty �ƶq�� -------------------------------
		$where_str = " WHERE wi_id='".$op['wi']['id']."' ";
		$T_wiqty = $wiqty->search(1,$where_str);

		$num_colors = count($T_wiqty);
		$op['num_colors'] = $num_colors;
		// ���X size_scale ----------------------------------------------
		$size_A = $size_des->get($op['smpl']['size']);
		$op['smpl']['size_scale']=$size_A['size_scale'];
			//????????????????????????????????????? �`�N ���ɨS�� size type ��Ʈ�??????
		$sizing = explode(",", $size_A['size']);

			// ���X size table-------------------------------------------
		$reply = show_breakdown($T_wiqty,$sizing,$size_A['base_size']);
		$op['show_breakdown'] = $reply['html'];
		$op['total'] = $reply['total'];   // �`���
		//-----------------------------------------------------------------

		// �ۤ���URL�M�w ------------------------------------------------------
				$style_dir	= "./picture/";  
				$no_img		= "./images/graydot.gif";
			if(file_exists($style_dir.$op['wi']['style_code'].".jpg")){
				$op['wi']['pic_url'] = $style_dir.$op['wi']['style_code'].".jpg";
			} else {
				$op['wi']['pic_url'] = $no_img;
			}

		//  ���X  BOM  �D�ưO�� --------------------------------------------------------------
		 $op['bom_lots_NONE']= '';
	 	 $op['bom_lots_list'] = $apply->get_lots_det($op['wi']['id']);  //���X�ӵ� bom ��ALL�D�ưO��
		 $num_bom_lots = count($op['bom_lots_list']);
		 if (!$num_bom_lots){	$op['bom_lots_NONE'] = "1";		}

		//  ���X  BOM  �ƮưO�� --------------------------------------------------------------
		$op['bom_acc_NONE']= '';
		$op['bom_acc_list'] = $apply->get_acc_det($op['wi']['id']);  //���X�ӵ� bom ��ALL�ƮưO��
		$num_bom_acc = count($op['bom_acc_list']);
		if (!$num_bom_acc){	$op['bom_acc_NONE'] = "1";		}


		$op['pa_num'] = $PHP_ap_num;
		$op['revise'] = $PHP_revise;
		page_display($op, 5,1, $TPL_APLY_SPC_VIEW_BOM);
		break;	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	  case "apply_special_add_ajax":		job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

    case "apply_special_add_ajax":
		check_authority(5,1,"add");		
				
		if ($PHP_item == 'lots')
		{
			$mat_cat='l';	
			$parm_mat = array (	'field_name'	=>	'vendor1',
													'field_value'	=>	$PHP_sup,
													'code'				=>	$PHP_mat_code
												);
			$lots->update_field_code($parm_mat);

			$parm_mat = array (	'field_name'	=>	'price1',
													'field_value'	=>	$PHP_price,
													'code'				=>	$PHP_mat_code
												);
			$lots->update_field_code($parm_mat);

	
		}else{			
			$mat_cat='a';		
			$parm_mat = array (	'field_name'	=>	'vendor1',
													'field_value'	=>	$PHP_sup,
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
													'mat_id'	=>	$PHP_mat_id,
													'eta'			=>	$PHP_eta,
													'qty'			=>	$PHP_qty,
													'unit'		=>	$PHP_unit,
													'mat_cat'	=>	$mat_cat,
													);
		$f1 = $apply->add_det($parm_dadd);	
	
		$ap_det = $apply->get_det($f1,$mat_cat);
		
		$message = "Successfully updated special PA deital : ".$PHP_ap_num;
		$op['msg'][] = $message;
		$log->log_add(0,"51A",$message);
		$det_html = $apply->ap_det_table($ap_det,$PHP_ap_num);
		echo $PHP_ap_num."|".$message."|".$ap_det['ord_num']."|".$ap_det['mat_code']."|".$ap_det['color']."|".$ap_det['ap_qty']."|".$ap_det['unit']."|".$ap_det['price1']."|".$ap_det['eta']."|".$det_html;
		

		$op['pa_num'] = $PHP_ap_num;
		$op['revise'] = $PHP_revise;
		break;	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apply_ext_add":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "apply_ext_add":
		check_authority(5,1,"add");		
		
			if(!$ord = $wi->get(0,$PHP_wi_num)){    //���X�ӵ� �s�y�O�O�� ID
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
		
		
		$op['wi_num'] = $PHP_wi_num;
		$op['item'] = $PHP_item;
		$op['CURRENCY_select'] = $arry2->select($CURRENCY,'','PHP_CURRENCY','select','');
		$op['FACTORY_select'] = $arry2->select($SHIP,'','PHP_FACTORY','select',"ship_show(this)");
		$op['ACC_select'] = $arry2->select($ACC_PRICE_UNIT,'','PHP_unit','select','');
		$op['FAB_select'] = $arry2->select($LOTS_PRICE_UNIT,'','PHP_unit','select','');

		$op['dm_way_select'] = $arry2->select($dm_way[0],'',"PHP_dm_way","select","");

		$op['revise'] = $PHP_revise;
		page_display($op, 5, 1, $TPL_APLY_EXT_ADD);			    	    
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apply_spc_add_det":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "apply_spc_add_det":
		check_authority(5,1,"add");		

		if ($PHP_ap_num =='')  //�Y�S��pa�s����,���إ߽��ʳ�(��)
		{			
			if(!$ord = $wi->get(0,$PHP_wi_num)){    //���X�ӵ� �s�y�O�O�� ID
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
			$head="PA".date('y')."-";	//A+���+����=���ʳ�}�Y
			$parm_madd['ap_num']=$apply->get_no($head,'ap_num','ap');	//���o���ʪ��̫�s��
			$new_id = $apply->add($parm_madd);	//�s�WAP��Ʈw
			$message = "Successfully appended PA : ".$parm_madd['ap_num'];
			$op['msg'][] = $message;
			$log->log_add(0,"51A",$message);
			$PHP_ap_num = $parm_madd['ap_num'];
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
		$f1 = $apply->add_special($parm_dadd);	//�s�WAP��Ʈw

		$op = $apply->get($PHP_ap_num);
		$message = "Successfully appended detial for PA  : ".$PHP_ap_num;
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

		page_display($op, 5, 1, $TPL_APLY_EXT_ADD);			    	    
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apply_add_det":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

    case "apply_add_det":
		check_authority(5,1,"add");		
		
			if(!$ord = $wi->get($PHP_wi_id)){    //���X�ӵ� �s�y�O�O�� ID
						$op['msg']= $wi->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
			}
					
		if ($PHP_item == 'lots')
		{
			$parm_mat = array (	'field_name' => 'vendor1','field_value' => $PHP_vendor,'code' => $PHP_mat_code);
			$lots->update_field_code($parm_mat);	//�[�J�D�ƨ�����
			
			$parm_mat = array (	'field_name'	=>	'price1','field_value'	=>	$PHP_price,	'code' =>	$PHP_mat_code);
			$lots->update_field_code($parm_mat);//�[�J�D�ƻ���

			$where_str = "WHERE id = ". $PHP_mat_id;
			$mat=$bom->search('bom_lots', $where_str);
			$fd='bom_lots';	
			$mat_cat='l';		
		}else{
			$parm_mat = array (	'field_name'	=>	'vendor1',			//�[�J�Ʈƨ�����
													'field_value'	=>	$PHP_vendor,
													'code'				=>	$PHP_mat_code
												);
			$acc->update_field_code($parm_mat);

			$parm_mat = array (	'field_name'	=>	'price1',				//�[�J�Ʈƻ���
													'field_value'	=>	$PHP_price,
													'code'				=>	$PHP_mat_code
												);
			$acc->update_field_code($parm_mat);

			$where_str = "WHERE id = ".$PHP_mat_id;
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
			$parm_dadd = array( 'ap_num'	=>	$PHP_ap_num,
													'mat_id'	=>	$mat[$i]['id'],
													'eta'			=>	$PHP_eta,
													'qty'			=>	$qty,
													'unit'		=>	$PHP_unit,
													'mat_cat'	=>	$mat_cat,
													);

			$parm_mat = array($mat[$i]['id'],'ap_mark',$PHP_ap_num,$fd);
			$f1 = $apply->add_det($parm_dadd);	//�s�WAP��Ʈw
			$f2 = $bom->update_field($parm_mat);			
		}


		$op = $apply->get($PHP_ap_num);

//�զX�e��List
		if (isset($op['ap_det']))
		{
			$op['ap_det2'] = $apply->group_ap($op['ap_det']);	//�զP���ʩ���
		}

	if ($op['ap']['arv_area'])
	{
		$op['ap']['ship_name'] = $SHIP_TO[$op['ap']['arv_area']]['Name'];
		$op['ap']['ship_addr'] = $SHIP_TO[$op['ap']['arv_area']]['Addr'];
		$op['ap']['ship_tel']  = $SHIP_TO[$op['ap']['arv_area']]['TEL'];
		$op['ap']['ship_fax']  = $SHIP_TO[$op['ap']['arv_area']]['FAX'];
		$op['ap']['ship_attn'] = $SHIP_TO[$op['ap']['arv_area']]['Attn'];
	}
		
		$msg = "Successfully add pa : ".$PHP_ap_num." detial";
		$op['msg'][]=$msg;

		$log->log_add(0,"51A",$msg);

		//�O���ק��+�ק��
		$apply->update_2fields('au_user','au_date',$GLOBALS['SCACHE']['ADMIN']['login_id'],$TODAY,$op['ap']['id']);

		
		$op['CURRENCY_select'] = $arry2->select($CURRENCY,$op['ap']['currency'],'PHP_CURRENCY','select','');
		$op['FACTORY_select'] = $arry2->select($SHIP,$op['ap']['arv_area'],'PHP_FACTORY','select',"ship_show(this)");
		for ($i=0; $i< 4; $i++)
		{
			if ($op['ap']['dm_way'] == $dm_way[1][$i]) $op['ap']['dm_way']=$dm_way[0][$i];
		}

		$op['dm_way_select'] = $arry2->select($dm_way[0],$op['ap']['dm_way'],"PHP_dm_way","select","");

		$op['revise'] = $PHP_revise;
		page_display($op, 5, 1, $TPL_APLY_ADD);			    	    
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "del_ap_det_ajax":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "del_ap_det_ajax":
		check_authority(5,1,"add");	
		
		$del_id = explode('|',$PHP_id);
		for ($i = 0; $i < sizeof($del_id); $i++)		$f1=$apply->mat_del($PHP_id);

		$del_mat_id = explode('|',$PHP_mat_id);
		if ($PHP_mat_cat == 'l') 
		{
			for ($i = 0; $i < sizeof($del_mat_id); $i++)
			{
				$parm_del = array($PHP_mat_id, 'ap_mark','','bom_lots');
				$bom->update_field($parm_del);		
			}
		}else if ($PHP_mat_cat == 'a'){
			for ($i = 0; $i < sizeof($del_mat_id); $i++)
			{
				$parm_del = array($PHP_mat_id, 'ap_mark','','bom_acc');
				$bom->update_field($parm_del);			
			}
		}
		
		//�O���ק��+�ק��

		$msg = "Successfully delete pa : ".$PHP_ap_num." mat : ".$PHP_mat_code." color & cat: ".$PHP_color;
		$log->log_add(0,"51A",$msg);

		echo $msg;
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "del_ap_spc_ajax":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "del_ap_spc_ajax":
		check_authority(5,1,"add");	
		$f1=$apply->special_del($PHP_id);
		
		$msg = "Successfully delete pa : ".$PHP_ap_num." mat : ".$PHP_mat_code." color & cat: ".$PHP_color;
		$log->log_add(0,"51A",$msg);

		echo $msg;
		break;
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_apply_del":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "do_apply_del":
		//check_authority(5,2,"add");	
		$op = $apply->get($PHP_ap_num);
		if (!$admin->is_power(5,2,"add"))
		{
			if (($op['ap']['ap_user'] != $GLOBALS['SCACHE']['ADMIN']['login_id'])){
				$op['msg'][]= "sorry! you don't have this Authoritys !";
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
		}

		if ($op['ap']['special'] == 0)
		{
			for ($i =0; $i< sizeof($op['ap_det']); $i++)
			{
				if ($op['ap_det'][$i]['mat_cat'] == 'l') 
				{
					$parm_del = array($op['ap_det'][$i]['bom_id'], 'ap_mark','','bom_lots');
				}else if ($op['ap_det'][$i]['mat_cat'] == 'a'){
					$parm_del = array($op['ap_det'][$i]['bom_id'], 'ap_mark','','bom_acc');
				}
				$bom->update_field($parm_del);			
			}
		}
	
		$f1 = $apply->del_pa($PHP_ap_num);
		$f1 = $po->del_po($PHP_ap_num);

		$msg = "Successfully delete pa : ".$PHP_ap_num;
		$log->log_add(0,"51A",$msg);
		if ($PHP_back_act == 'apply_wi_view')
		{
			$redirect = "index2.php?PHP_action=".$PHP_back_act."&PHP_sr".$PHP_sr_startno."&PHP_dept_code=".$PHP_dept_code."&PHP_cust=".$PHP_cust."&PHP_num=".$PHP_num."&PHP_msg=".$msg."&PHP_id=".$PHP_id."&PHP_full=".$PHP_full;
		}else if($PHP_back_act == 'apply_ext_bom_view'){
			$redirect = "index2.php?PHP_action=".$PHP_back_act."&PHP_sr".$PHP_sr_startno."&PHP_dept_code=".$PHP_dept_code."&PHP_cust=".$PHP_cust."&PHP_num=".$PHP_num."&PHP_special=1&PHP_msg=".$msg."&PHP_id=".$PHP_id;							
		}else{
			$redirect = "index2.php?PHP_action=".$PHP_back_act."&PHP_dept_code=".$PHP_dept_code."&PHP_SCH_num=".$PHP_SCH_num."&PHP_SCH_fty=".$PHP_SCH_fty."&PHP_SCH_cust=".$PHP_SCH_cust."&PHP_SCH_supl=".$PHP_SCH_supl."&PHP_sr_startno=".$PHP_sr_startno."&PHP_msg=".$msg;
		}

		redirect_page($redirect);

		break;	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_apply_add":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "do_apply_add":
		check_authority(5,1,"add");	

		$apply->update_fields_id('arv_area',$PHP_FACTORY,$PHP_id);  //Update �B�e�a(ID)
		$apply->update_fields_id('currency',$PHP_CURRENCY,$PHP_id);	//Update ���O
	
		$parm = array( 'field_name'		=>	'dm_way',		//Update Payment��Supplier
									 'field_value'	=>	$PHP_dmway,
									 'id'						=>	$PHP_sup_id
									 );
		$supl->update_field($parm);
		
	  if (!$PHP_FACTORY)	//�Y�L�B�e�a(ID)��,�O���B�e�a�ԲӸ��
	  {
	  	$apply->update_fields_id('ship_addr',$PHP_saddr,$PHP_id);		//�B�e�a�}
			$apply->update_fields_id('ship_name',$PHP_sname,$PHP_id);		//�B�e�a���q�W
			$apply->update_fields_id('ship_fax',$PHP_sfax,$PHP_id);			//�B�e�a�ǯu
			$apply->update_fields_id('ship_tel',$PHP_stel,$PHP_id);			//�B�e�a�q��
			$apply->update_fields_id('ship_attn',$PHP_sattn,$PHP_id);	  //�B�e�a�p���H
	  }  
	  
  $log_where = "AND item <> 'special'";	
	$op = $apply->get($PHP_ap_num,$log_where);		
	if (isset($op['ap_det']))
	{
		$op['ap_det2'] = $apply->group_ap($op['ap_det']);	//�զP���ʩ���
	}
		$msg = "Successfully update P/A : ".$PHP_ap_num." main record";
		$op['msg'][]=$msg;
		$log->log_add(0,"51A",$msg);	
//Ship To
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
	
	
	if (isset($PHP_SCH_num))
	{
		$back_str="?PHP_action=apply_search&PHP_dept_code=".$PHP_dept_code."&PHP_SCH_num=".$PHP_SCH_num."&PHP_SCH_fty=".$PHP_SCH_fty."&PHP_SCH_cust=".$PHP_SCH_cust."&PHP_SCH_supl=".$PHP_SCH_supl."&PHP_sr_startno=".$PHP_sr_startno;
	}else{
			$wi_id = $wi->get_field($op['ap_det2'][0]['ord_num'],'id');
			$back_str="?PHP_action=apply_wi_view&PHP_sr=&PHP_dept_code=".$op['ap']['dept']."&PHP_cust=".$op['ap']['cust']."&PHP_num=&PHP_full=&PHP_id=".$wi_id['id'];
			$op['back_str']=$back_str;
			$op['add_back'] = 'apply_wi_view';
	}
	$op['back_str']=$back_str;
		if ($op['ap']['revise'] > 0 || $op['ap']['apv_user']) $op['ap']['revise'] =$op['ap']['revise']+1;

	if ($PHP_revise == 1)
	{
		page_display($op, 5, 1, $TPL_APLY_SHOW_REV);
	}else{
		page_display($op, 5, 1, $TPL_APLY_SHOW);			    	    
	}
	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_apply_spc_add1":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "do_apply_spc_add1":
		check_authority(5,1,"add");	

		$op = $apply->get($PHP_ap_num);		
		$op['ap_det2'] = $apply->group_ap($op['ap_det']);	//�զP���ʩ���

		$op['CURRENCY_select'] = $arry2->select($CURRENCY,$op['ap']['currency'],'PHP_CURRENCY','select','');
		$op['FACTORY_select'] = $arry2->select($SHIP,$op['ap']['arv_area'],'PHP_FACTORY','select',"ship_show(this)");
		for ($i=0; $i< 4; $i++)
		{
			if ($op['ap']['dm_way'] == $dm_way[1][$i]) $op['ap']['dm_way']=$dm_way[0][$i];
		}
		$op['dm_way_select'] = $arry2->select($dm_way[0],$op['ap']['dm_way'],"PHP_dm_way","select","");

		if ($op['ap']['arv_area'])
		{
			$op['ap']['ship_name'] = $SHIP_TO[$op['ap']['arv_area']]['Name'];
			$op['ap']['ship_addr'] = $SHIP_TO[$op['ap']['arv_area']]['Addr'];
			$op['ap']['ship_tel']  = $SHIP_TO[$op['ap']['arv_area']]['TEL'];
			$op['ap']['ship_fax']  = $SHIP_TO[$op['ap']['arv_area']]['FAX'];
			$op['ap']['ship_attn'] = $SHIP_TO[$op['ap']['arv_area']]['Attn'];
		}

		$op['back_str']=$PHP_back_str;
		$op['revise'] = $PHP_revise;
		page_display($op, 5, 1, $TPL_APLY_SPC_ADD);			    	    
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_apply_spc_add2":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "do_apply_spc_add2":
		check_authority(5,1,"add");	

		$apply->update_2fields('currency','arv_area',$PHP_CURRENCY,$PHP_FACTORY,$PHP_id);
	
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

	  if (!$PHP_FACTORY)	//�Y�L�B�e�a(ID)��,�O���B�e�a�ԲӸ��
	  {
	  	$apply->update_fields_id('ship_addr',$PHP_saddr,$PHP_id);		//�B�e�a�}
			$apply->update_fields_id('ship_name',$PHP_sname,$PHP_id);		//�B�e�a���q�W
			$apply->update_fields_id('ship_fax',$PHP_sfax,$PHP_id);			//�B�e�a�ǯu
			$apply->update_fields_id('ship_tel',$PHP_stel,$PHP_id);			//�B�e�a�q��
			$apply->update_fields_id('ship_attn',$PHP_sattn,$PHP_id);	  //�B�e�a�p���H
	  }  
		$log_where = "AND item <> 'special'";	
		$op = $apply->get($PHP_ap_num,$log_where);
		
		
		if (isset($op['ap_det']))
		{
			$op['ap_det2'] = $apply->group_ap($op['ap_det']);	//�զP���ʩ���
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
		if ($op['ap']['revise'] > 0 || $op['ap']['apv_user']) $op['ap']['revise'] =$op['ap']['revise']+1;
	
		if ($PHP_revise == 1)
		{
			page_display($op, 5, 1, $TPL_APLY_SHOW_REV);
		}else{
			page_display($op, 5, 1, $TPL_APLY_SHOW);			    	    
		}		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apply_edit":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

    case "apply_edit":
		check_authority(5,1,"edit");		
			
		$op = $apply->get($PHP_aply_num);
				
//�զX�e��List
	if (isset($op['ap_det']))
	{
			$op['ap_det2'] = $apply->group_ap($op['ap_det']);	//�զP���ʩ���
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
		for ($i=0; $i< sizeof($id); $i++)		//���o�C�������ʶq,�å[�`
		{
			$where_str = "id = ".$id[$i];
			$row[$i]=$po->get_det_field('ap_qty',$PHP_table,$where_str);
			$s_total = $s_total + $row[$i]['ap_qty'];
		}		
		for ($i=0; $i< (sizeof($id)-1); $i++)		//�p��C�����ʶq(����)
		{
			$avg_qty[$i] = $PHP_qty * ($row[$i]['ap_qty'] / $s_total);
			$tmp = $tmp + $avg_qty[$i];
		}	
		$tmp_qty = $PHP_qty - $tmp;
		$avg_qty[$i] = $tmp_qty;
		
		
		for ($i=0; $i< sizeof($id); $i++)		//�[�JDB
		{
			$f1=$apply->update_fields_id('ap_qty',$avg_qty[$i], $id[$i], $PHP_table);
			$f1=$apply->update_fields_id('eta',$PHP_eta, $id[$i], $PHP_table);
		}	
		
		$message = "Successfully add P/A detial : ".$PHP_ap_num."record�C";
		$log->log_add(0,"51E",$message);		
		
		$message = "Successfully Edit P/A : ".$PHP_ap_num." record";
		echo $message;
break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_apply_edit":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "do_apply_edit":
		check_authority(5,1,"edit");	

		$apply->update_2fields('currency','arv_area',$PHP_CURRENCY,$PHP_FACTORY,$PHP_id);
	
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

	  if (!$PHP_FACTORY)	//�Y�L�B�e�a(ID)��,�O���B�e�a�ԲӸ��
	  {
	  	$apply->update_fields_id('ship_addr',$PHP_saddr,$PHP_id);		//�B�e�a�}
			$apply->update_fields_id('ship_name',$PHP_sname,$PHP_id);		//�B�e�a���q�W
			$apply->update_fields_id('ship_fax',$PHP_sfax,$PHP_id);			//�B�e�a�ǯu
			$apply->update_fields_id('ship_tel',$PHP_stel,$PHP_id);			//�B�e�a�q��
			$apply->update_fields_id('ship_attn',$PHP_sattn,$PHP_id);	  //�B�e�a�p���H
	  }  

		$f1=$apply->update_fields_id('special',$PHP_new_special,$PHP_id);	 //�O���ӽФ�

		$log_where = "AND item <> 'special'";	
		$op = $apply->get($PHP_ap_num,$log_where);
		
		
		if (isset($op['ap_det']))
		{
			$op['ap_det2'] = $apply->group_ap($op['ap_det']);	//�զP���ʩ���
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
			if (isset($PHP_SCH_num))
			{
				$back_str="?PHP_action=apply_search&PHP_dept_code=".$PHP_dept_code."&PHP_SCH_num=".$PHP_SCH_num."&PHP_SCH_fty=".$PHP_SCH_fty."&PHP_SCH_cust=".$PHP_SCH_cust."&PHP_SCH_supl=".$PHP_SCH_supl."&PHP_sr_startno=".$PHP_sr_startno;				
			}else{
				$back_str="?PHP_action=apply_search&PHP_dept_code=&PHP_SCH_num&PHP_SCH_fty=&PHP_SCH_cust=&PHP_SCH_supl=&PHP_sr_startno=";
			}
			$op['back_str']=$back_str;
		if ($op['ap']['revise'] > 0 || $op['ap']['apv_user']) $op['ap']['revise'] =$op['ap']['revise']+1;
	
		if ($PHP_revise == 1)
		{
			page_display($op, 5, 1, $TPL_APLY_SHOW_REV);
		}else{
			page_display($op, 5, 1, $TPL_APLY_SHOW);			    	    
		}		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apply_search":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "apply_search":
	check_authority(5,1,"view");	

	if (!$op = $apply->search(1, $PHP_dept_code)) {  
				$op['msg']= $apply->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			
		for ($i=0; $i<sizeof($op['apply']); $i++)
		{
			if ($op['apply'][$i]['special'] == 2){$table = 'ap_special';}else{$table = 'ap_det';}
			$where_str = "ap_num ='".$op['apply'][$i]['ap_num']."' GROUP BY ap_num";
			
			$row = $po->get_det_field('min(eta) as eta',$table,$where_str);
			$op['apply'][$i]['eta'] = $row['eta'];
		}
			
			
	$back_str="&PHP_dept_code=".$PHP_dept_code."&PHP_SCH_num=".$PHP_SCH_num."&PHP_SCH_fty=".$PHP_SCH_fty."&PHP_SCH_cust=".$PHP_SCH_cust."&PHP_SCH_supl=".$PHP_SCH_supl."&PHP_sr_startno=".$PHP_sr_startno;
	$op['back_str']=$back_str;

	if (!$PHP_dept_code) {   // �p�G ���O �e��~�ȳ����i�J��...
		$op['manager_flag'] = 1;
	}
	$op['dept_id'] = $PHP_dept_code;
	$op['msg']= $apply->msg->get(2);
	if (isset($PHP_msg)) $op['msg'][]=$PHP_msg;
	page_display($op, 5, 1, $TPL_APLY_LIST);
	break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "pa_print":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "pa_print":   //  .......
	check_authority(5,1,"add");	
	$where_str="AND( item like '%PA-Rmk%' OR item like '%special%')";		
	$op = $apply->get($PHP_aply_num,$where_str);

	//���ouser name
	$po_user=$user->get(0,$op['ap']['apv_user']);
	if ($po_user['name'])$op['ap']['apv_user'] = $po_user['name'];
	$po_user=$user->get(0,$op['ap']['cfm_user']);
	if ($po_user['name'])$op['ap']['cfm_user'] = $po_user['name'];
	$po_user=$user->get(0,$op['ap']['submit_user']);
	if ($po_user['name'])$op['ap']['submit_user'] = $po_user['name'];
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
					$op['ap_det2'][$j]['ap_qty'] = $op['ap_det'][$i]['ap_qty'] +$op['ap_det2'][$j]['ap_qty'];
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
//---------------------------------- ��Ʈw�@�~���� ------------------------------

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
$parm = array (	'pa_num'		=>	$op['ap']['ap_num'],
							'supplier'		=>	$op['ap']['s_name'],
							'dept'				=>	$dept_name[0],
							'ap_date'			=>	$op['ap']['apv_date'],
							'ap_user'			=>	$op['ap']['ap_user'],
							'currency'		=>	$op['ap']['currency'],
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
		$op['ap_det2'][$i]['price1'] = NUMBER_FORMAT($op['ap_det2'][$i]['price1'],2);
		$op['ap_det2'][$i]['ap_qty'] = NUMBER_FORMAT($op['ap_det2'][$i]['ap_qty'],2);
		$op['ap_det2'][$i]['amount'] = NUMBER_FORMAT($op['ap_det2'][$i]['amount'],2);
		
		$ords= explode('|',$op['ap_det2'][$i]['orders']);		
		$total = $total + ($op['ap_det2'][$i]['price1']*$op['ap_det2'][$i]['ap_qty']);
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
		$total = $total + ($op['ap_spec'][$i]['price1']*$op['ap_spec'][$i]['ap_qty']);
		$op['ap_spec'][$i]['price1'] = NUMBER_FORMAT($op['ap_spec'][$i]['price1'],2);
		$op['ap_spec'][$i]['ap_qty'] = NUMBER_FORMAT($op['ap_spec'][$i]['ap_qty'],2);
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

	$pdf->Cell(50,5,'APPROVAL : '.$op['ap']['apv_user'],'0',0,'C');	//PO Approval
	$pdf->Cell(50,5,'CONFIRM :'.$op['ap']['cfm_user'],0,0,'L');//PO Confirm
	
	$pdf->Cell(50,5,'PA :'.$op['ap']['submit_user'],0,0,'L');//PA submit
	

$name=$op['ap']['ap_num'].'_pa.pdf';
//echo "<meta http-equiv='refresh' content='50000; url=close2.html'>";
$pdf->Output($name,'D');


break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apply_view":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "apply_view":
		check_authority(5,1,"add");	

	$log_where = "AND item <> 'special'";	
	$op = $apply->get($PHP_aply_num,$log_where);
	if (isset($op['ap_det']))
	{
			$op['ap_det2'] = $apply->group_ap($op['ap_det']);	//�զP���ʩ���
	}
	
	for ($i=0; $i< 4; $i++)
	{
		if ($op['ap']['dm_way'] == $dm_way[1][$i]) $op['ap']['dm_way']=$dm_way[0][$i];
	}

//Payment
	if (isset($PHP_SCH_num))
	{
		$back_str="?PHP_action=apply_search&PHP_dept_code=".$PHP_dept_code."&PHP_SCH_num=".$PHP_SCH_num."&PHP_SCH_fty=".$PHP_SCH_fty."&PHP_SCH_cust=".$PHP_SCH_cust."&PHP_SCH_supl=".$PHP_SCH_supl."&PHP_sr_startno=".$PHP_sr_startno;
		$op['back_str']=$back_str;
	}else if(isset($PHP_add_back) && $PHP_add_back){
		if (isset($op['ap_det2'][0]['ord_num']))
		{
			$wi_id = $wi->get_field($op['ap_det2'][0]['ord_num'],'id');
		}else{
			$wi_id = $wi->get_field($op['ap_spec'][0]['ord_num'],'id');
		}
		$back_str="?PHP_action=".$PHP_add_back."&PHP_sr=&PHP_dept_code=".$op['ap']['dept']."&PHP_cust=".$op['ap']['cust']."&PHP_num=&PHP_full=&PHP_id=".$wi_id['id'];				
		$op['back_str']=$back_str;
	}
//ship to	
	if ($op['ap']['arv_area'])
	{
		$op['ap']['ship_name'] = $SHIP_TO[$op['ap']['arv_area']]['Name'];
		$op['ap']['ship_addr'] = $SHIP_TO[$op['ap']['arv_area']]['Addr'];
		$op['ap']['ship_tel']  = $SHIP_TO[$op['ap']['arv_area']]['TEL'];
		$op['ap']['ship_fax']  = $SHIP_TO[$op['ap']['arv_area']]['FAX'];
		$op['ap']['ship_attn'] = $SHIP_TO[$op['ap']['arv_area']]['Attn'];
	}
		if ($op['ap']['revise'] > 0 || $op['ap']['apv_user']) $op['ap']['revise'] =$op['ap']['revise']+1;

	if (isset($PHP_message)) $op['msg'][]=$PHP_message;

		page_display($op, 5, 1, $TPL_APLY_SHOW);			    	    
		break;




#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_apply_logs_add":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "do_apply_logs_add":
	check_authority(5,1,"view");	
	
	
	$parm= array(	'ap_num'		=>	$PHP_aply_num,
								'item'			=>	'PA-Rmk.',
								'des'				=>	$PHP_des,
								'user'			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
								'k_date'		=>	date('Y-m-d')
				);
	$f1=$apply->add_log($parm);
	
	if ($f1)
	{
		$message="Successfully add log :".$PHP_aply_num;
		$op['msg'][]=$message;
//		$log->log_add(0,"54E",$message);
	}else{
		$op['msg']= $apply->msg->get(2);
		$message = $op['msg'][0];
	}	
	$redir_str = "index2.php?PHP_action=apply_view&PHP_aply_num=".$PHP_aply_num."&PHP_message=".$message."&PHP_back=".$PHP_back_str;
	redirect_page($redir_str);



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apply_submit":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "apply_submit":
	check_authority(5,1,"view");	
	$f1=$apply->update_2fields('status', 'submit_date', '2', $TODAY, $PHP_id);
	$f1=$apply->update_fields('submit_user',$GLOBALS['SCACHE']['ADMIN']['login_id'], $PHP_aply_num);
		if ($f1)
	{
		$message="Successfully submit P/A :".$PHP_aply_num;
		$op['msg'][]=$message;
		$log->log_add(0,"51E",$message);
	}else{
		$op['msg']= $apply->msg->get(2);
		$message=$op['msg'][0];
	}	
		
	$redir_str = "index2.php?PHP_action=apply_view&PHP_aply_num=".$PHP_aply_num."&PHP_message=".$message."&PHP_back=".$PHP_back_str."&PHP_add_back=".$PHP_add_back;
	redirect_page($redir_str);
	break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apply_cfm_search":			job 52
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "apply_cfm_search":
	check_authority(5,2,"view");	

	if (!$op = $apply->search_cfm()) {  
				$op['msg']= $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
	$op['msg']= $apply->msg->get(2);
	page_display($op, 5, 2, $TPL_APLY_CFM_LIST);
	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apply_cfm_view":			job 52
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "apply_cfm_view":
		check_authority(5,2,"view");	

	$log_where = "AND item <> 'special'";		
	$op = $apply->get($PHP_aply_num,$log_where);
	if (isset($op['ap_det']))
	{
			$op['ap_det2'] = $apply->group_ap($op['ap_det']);	//�զP���ʩ���
	}
	$back_str="&PHP_sr_startno=".$PHP_sr_startno;
	$op['back_str']=$back_str;
	if (isset($PHP_message)) $op['msg'][]=$PHP_message;
//ship To
	if ($op['ap']['arv_area'])
	{
		$op['ap']['ship_name'] = $SHIP_TO[$op['ap']['arv_area']]['Name'];
		$op['ap']['ship_addr'] = $SHIP_TO[$op['ap']['arv_area']]['Addr'];
		$op['ap']['ship_tel']  = $SHIP_TO[$op['ap']['arv_area']]['TEL'];
		$op['ap']['ship_fax']  = $SHIP_TO[$op['ap']['arv_area']]['FAX'];
		$op['ap']['ship_attn'] = $SHIP_TO[$op['ap']['arv_area']]['Attn'];
	}
//Payment
		if ($op['ap']['revise'] > 0 || $op['ap']['apv_user']) $op['ap']['revise'] =$op['ap']['revise']+1;

	for ($i=0; $i< 4; $i++)
	{
		if ($op['ap']['dm_way'] == $dm_way[1][$i]) $op['ap']['dm_way']=$dm_way[0][$i];
	}

		page_display($op, 5, 2, $TPL_APLY_SHOW_CFM);			    	    
		break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_apply_cfm":			job 52
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "do_apply_cfm":
	check_authority(5,2,"add");	
	$f1=$apply->update_2fields('status', 'cfm_date', '3', $TODAY, $PHP_id);
	$f1=$apply->update_fields('cfm_user',$GLOBALS['SCACHE']['ADMIN']['login_id'], $PHP_aply_num);
		if ($f1)
	{
		$message="Successfully CFM P/A :".$PHP_aply_num;
		$op['msg'][]=$message;
		$log->log_add(0,"52A",$message);
	}else{
		$op['msg']= $apply->msg->get(2);
		$message=$op['msg'][0];
	}	
		
	$redir_str = "index2.php?PHP_action=apply_cfm_view&PHP_aply_num=".$PHP_aply_num."&PHP_message=".$message.$PHP_back_str;
	redirect_page($redir_str);
	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "reject_apply_cfm":			job 52
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "reject_apply_cfm":
	check_authority(5,2,"add");	
	
	$f1=$apply->update_2fields('status', 'au_date', '5', $TODAY, $PHP_id);
	$f1=$apply->update_fields('au_user',$GLOBALS['SCACHE']['ADMIN']['login_id'], $PHP_aply_num);
	
	$parm= array(	'ap_num'		=>	$PHP_aply_num,
								'item'			=>	'REJ-CFM',
								'des'				=>	$PHP_detail,
								'user'			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
								'k_date'		=>	date('Y-m-d')
				);
	$f1=$apply->add_log($parm);
	
	if ($f1)
	{
		$message="Successfully reject P/A :".$PHP_aply_num;
		$op['msg'][]=$message;
//		$log->log_add(0,"54E",$message);
	}else{
		$op['msg']= $apply->msg->get(2);
		$message = $op['msg'][0];
	}	
	$redir_str = "index2.php?PHP_action=apply_cfm_view&PHP_aply_num=".$PHP_aply_num."&PHP_message=".$message.$PHP_back_str;
	redirect_page($redir_str);
	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apply_apv_search":			job 53
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "apply_apv_search":
	check_authority(5,3,"view");	

	if (!$op = $apply->search_apv()) {  
				$op['msg']= $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
	$op['msg']= $apply->msg->get(2);
	page_display($op, 5, 3, $TPL_APLY_APV_LIST);
	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apply_apv_view":			job 53
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "apply_apv_view":
		check_authority(5,3,"view");	

	$log_where = "AND item <> 'special'";		
	$op = $apply->get($PHP_aply_num,$log_where);
	if (isset($op['ap_det']))
	{
			$op['ap_det2'] = $apply->group_ap($op['ap_det']);	//�զP���ʩ���
	}

	if ($op['ap']['arv_area'])
	{
		$op['ap']['ship_name'] = $SHIP_TO[$op['ap']['arv_area']]['Name'];
		$op['ap']['ship_addr'] = $SHIP_TO[$op['ap']['arv_area']]['Addr'];
		$op['ap']['ship_tel']  = $SHIP_TO[$op['ap']['arv_area']]['TEL'];
		$op['ap']['ship_fax']  = $SHIP_TO[$op['ap']['arv_area']]['FAX'];
		$op['ap']['ship_attn'] = $SHIP_TO[$op['ap']['arv_area']]['Attn'];
	}

		if ($op['ap']['revise'] > 0 || $op['ap']['apv_user']) $op['ap']['revise'] =$op['ap']['revise']+1;

	for ($i=0; $i< 4; $i++)
	{
		if ($op['ap']['dm_way'] == $dm_way[1][$i]) $op['ap']['dm_way']=$dm_way[0][$i];
	}

	$back_str="&PHP_sr_startno=".$PHP_sr_startno;
	$op['back_str']=$back_str;
	if (isset($PHP_message)) $op['msg'][]=$PHP_message;

		page_display($op, 5, 3, $TPL_APLY_SHOW_APV);			    	    
		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "do_apply_apv":			job 53
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "do_apply_apv":
	check_authority(5,3,"add");	
	$f1=$apply->update_2fields('status', 'apv_date', '4', $TODAY, $PHP_id);
	$f1=$apply->update_fields('apv_user',$GLOBALS['SCACHE']['ADMIN']['login_id'], $PHP_aply_num);
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
		$message="Successfully APV :".$PHP_aply_num;
		$op['msg'][]=$message;
		$log->log_add(0,"53A",$message);
	}else{
		$op['msg']= $apply->msg->get(2);
		$message=$op['msg'][0];
	}	

	$redir_str = "index2.php?PHP_action=apply_apv_view&PHP_aply_num=".$PHP_aply_num."&PHP_message=".$message.$PHP_back_str;
	redirect_page($redir_str);
	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "reject_apply_apv":			job 53
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "reject_apply_apv":
	check_authority(5,3,"add");	
	
	$f1=$apply->update_2fields('status', 'au_date', '5', $TODAY, $PHP_id);
	$f1=$apply->update_fields('au_user',$GLOBALS['SCACHE']['ADMIN']['login_id'], $PHP_aply_num);
	
	$parm= array(	'ap_num'		=>	$PHP_aply_num,
								'item'			=>	'REJ-APV',
								'des'				=>	$PHP_detail,
								'user'			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
								'k_date'		=>	date('Y-m-d')
				);
	$f1=$apply->add_log($parm);
	
	if ($f1)
	{
		$message="Successfully reject PA :".$PHP_aply_num;
		$op['msg'][]=$message;
	}else{
		$op['msg']= $apply->msg->get(2);
		$message = $op['msg'][0];
	}	
	$redir_str = "index2.php?PHP_action=apply_apv_view&PHP_aply_num=".$PHP_aply_num."&PHP_message=".$message.$PHP_back_str;
	redirect_page($redir_str);
	break;
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "apply_revise":			job 51
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

    case "apply_revise":
		check_authority(5,1,"edit");		
	$f1=$apply->update_2fields('apv_user', 'apv_date','','0000-00-00', $PHP_id);
	$f1=$apply->update_2fields('cfm_user', 'cfm_date','','0000-00-00', $PHP_id);
	$f1=$apply->update_2fields('submit_user', 'submit_date','','0000-00-00', $PHP_id);
	$f1=$apply->update_fields('status',0, $PHP_aply_num);
	$f1=$apply->update_fields('revise',($PHP_rev_time+1), $PHP_aply_num);		
			
		$op = $apply->get($PHP_aply_num);
				
//�զX�e��List
		if (isset($op['ap_det']))
		{
			$op['ap_det2'] = $apply->group_ap($op['ap_det']);	//�զP���ʩ���
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


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "bom_close_pa":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 case "bom_close_pa":
 	check_authority(5,1,"del");
 	
 	$parm=array($PHP_id,'ap_mark','x','bom_lots');
	$bom->update_wi_field($parm);

 	$parm=array($PHP_id,'ap_mark','x','bom_acc');
	$bom->update_wi_field($parm);

	$message= "Remark BOM [".$PHP_wi_num."] was purchased";
					# �O���ϥΪ̰ʺA
	$log->log_add(0,"51A",$message);
	echo $message;
 break;








	
	









#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "get_sup":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "show_supplier_add":
 	check_authority(1,2,"view");
		$op['supl_cat'] = $PHP_cat;
		$op['country_select'] = $arry2->select($COUNTRY,'','PHP_country','select','');  	
		$op['usance_select'] = $arry2->select($usance,"","PHP_usance","select","");
		$op['dm_way_select'] = $arry2->select($dm_way[0],"","PHP_dm_way","select","");
		$op['item']=$PHP_item;
		$op['add_view']=1;
		page_display($op, 1, 2, $TPL_EX_SUPL_ADD);
		break;
		


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_supl_add":	 	JOB 12A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_ex_supl_add":

		check_authority(1,2,"add");	
			
		$parm = array(	"supl_cat"			=>	$PHP_supl_cat,
						"vndr_no"			=>	$PHP_vndr_no,
						"country"			=>	$PHP_country,
						"supl_s_name"		=>	$PHP_supl_s_name,
						"supl_f_name"		=>	$PHP_supl_f_name,
						"uni_no"			=>	$PHP_uni_no,
						"cntc_phone"		=>	$PHP_cntc_phone,
						"cntc_addr"			=>	$PHP_cntc_addr,
						"cntc_person1"		=>	$PHP_cntc_person1,
						"cntc_cell1"		=>	$PHP_cntc_cell1,
						"email1"			=>	$PHP_email1,
						"cntc_person2"		=>	$PHP_cntc_person2,
						"cntc_cell2"		=>	$PHP_cntc_cell2,
						"email2"			=>	$PHP_email2,
						"cntc_fax"			=>	$PHP_cntc_fax,
						"usance"			=>	$PHP_usance,						
						"dm_way"			=>	$PHP_dm_way
				);

				$op['supl'] = $parm;

		$f1 = $supl->add($parm);
//		$f1=1;
		if ($f1) {  // ���\��J��Ʈ�
			$PHP_supl_s_name=$PHP_vndr_no;
			if (!$op['supl'] = $supl->get(0,$PHP_vndr_no)) {	//�j�M�C��
				$op['msg']= $supl->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

			$message= "Append supl:".$parm['supl_s_name'];

					# �O���ϥΪ̰ʺA
			$log->log_add(0,"12A",$message);
			
			$op['msg'][] = $message;
			// ���X������ ���������O�N��
			$cat_def = $supl_type->get_fields('supl_type');   
			$op['cat_select'] = $arry2->select($cat_def,"","PHP_supl_cat","select","");
			$op['item']=$PHP_item;
			$op['cfm_view']=1;
			page_display($op, 1, 2,  $TPL_EX_SUPL_ADD);
			break;
	
	
		}	else {  // ��S�����\��J�s�Wuser������....

			$op['supl'] = $parm;
			$op['supl_cat'] = $parm['supl_cat'];
			$op['msg'] = $supl->msg->get(2);


			$cat_def = $supl_type->get_fields('supl_type'); 
			$op['cat_select'] = $arry2->select($cat_def,"","PHP_supl_cat","select",""); 
			$op['country_select'] = $arry2->select($COUNTRY,$PHP_country,'PHP_country','select','');  
			
			$op['usance_select'] = $arry2->select($usance,$PHP_usance,"PHP_usance","select","");
			$op['dm_way_select'] = $arry2->select($dm_way[0],$PHP_dm_way,"PHP_dm_way","select","");	
			$op['item']=$PHP_item;
			$op['add_view']=1;
			page_display($op, 1, 2, $TPL_EX_SUPL_ADD);
			break;

		}
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "get_sup":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "show_supplier_search":
 
		$op['country_select'] = $arry2->select($COUNTRY,'','PHP_country','select','');  	//��O�����U�Ԧ�
		$cat_def = $SUPL_TYPE;   // ���X������ ���������O [ config.php ]
		$op['cat_select'] = $arry2->select($cat_def,"","PHP_supl_cat","select",""); 	//���������O�����U�Ԧ�
		if(isset($PHP_cat))$op['cat_select']=$PHP_cat."<input type=hidden name='PHP_supl_cat' value='$PHP_cat'>";
		$op['item']=$PHP_item;
		page_display($op, 1, 2, $TPL_SUPNO);
		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "do_get_sup":	 	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "do_get_sup":
 		$parm = array(	"supl_cat"			=>  $PHP_supl_cat,
						"country"			=>	$PHP_country,
						"supl_s_name"		=>	$PHP_supl_s_name,
						"supl_f_name"		=>	$PHP_supl_f_name
				);
		
		if (!$op = $supl->search(0)) {		//�j�M����U�������Ӹ��
			$op['msg']= $supl->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		for ($i=0; $i<sizeof($op['supl']); $i++)
		{
			$op['supl'][$i]['supl_s_name'] = str_replace("'"," ",$op['supl'][$i]['supl_s_name']);
		}
		$op['country_select'] = $arry2->select($COUNTRY,'','PHP_country','select','');  	//��O�����U�Ԧ�
		$cat_def = $SUPL_TYPE;   // ���X������ ���������O�N�� [ config.php ]
		$op['cat_select'] = $arry2->select($cat_def,"","PHP_supl_cat","select",""); 	//���������O�����U�Ԧ�
		if($PHP_supl_cat)$op['cat_select']=$PHP_supl_cat."<input type=hidden name='PHP_supl_cat' value='$PHP_supl_cat'>";
		$op['msg']= $supl->msg->get(2);
		$op['cgi']=$parm;
		$op['item']=$PHP_item;
		page_display($op, 1, 2, $TPL_SUPNO);
		break;
		
		
		





#++++++++++++++    SAMPLE ORDER +++++++++++++  2006/11/08  +++++++++++++++++
#		 job 92    �˥� �����u�@��O���t�� 
#++++++++++++++++++++++++++++++++++++++++++++  2006/11/09  +++++++++++++++++
#		case "offer":			job 92
#		case "add_offer":		job 92A
#		case "do_add_offer":	job 92A
#		case "offer_search":	job 92V
#		case "offer_view":		job 92V offer view
#		case "offer_edit":
#		case "do_offer_edit":


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "offer":	job 92
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "offer":

		check_authority(9,2,"view");
	
		$where_str = $manager = $dept_id = '';
		$dept_ary = array();

		$sales_dept_ary = get_sales_dept(); //  [���tK0] ------
		$my_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];   // �P�w�i�J����������

//		$my_dept = $GLOBALS['MY_DEPT']; 
		$my_team = $GLOBALS['SCACHE']['ADMIN']['team_id'];


		for ($i=0; $i<count($sales_dept_ary);$i++){
			if($my_dept == $sales_dept_ary[$i]){
				// �p�G�O�~�ȳ��i�J �hdept_code ���w�ӷ~�ȳ�---
				$dept_id = $sales_dept_ary[$i];  
				$where_str = " WHERE dept = '".$dept_id."' ";
			}
		}

		if (!$dept_id) { // ���O�~�ȳ��H[�]���t K0 ���H ]�i�J��
			$manager = 1;
			// �~�ȳ��� select���
			$dept_ary = $arry2->select($sales_dept_ary,"","PHP_dept_code","select","");  
		}

		$op['manager_flag'] = $manager;
		$op['dept_id'] = $dept_id;
		$op['dept_ary'] = $dept_ary;

		// creat cust combo box	 ���X �Ȥ�N��
	if ( $my_dept == 'CU')
	{
		$where_str="WHERE cust_s_name ='".$my_team."'";
//		echo $where_str;
		if(!$cust_def = $cust->get_fields('cust_init_name',$where_str)){;  //���X�Ȥ�²��
			$op['msg'][] = "sorry! there is no any customer record in your team, please add customer first!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$op['cust_select'] = "<b>".$cust_def[0]."</b><input type=hidden name='PHP_cust' value='".$my_team."'>";
		
	}else{
		$where_str=$where_str."order by cust_s_name"; //��cust_s_name�Ƨ�
		if(!$cust_def = $cust->get_fields('cust_init_name',$where_str)){;  //���X�Ȥ�²��
			$op['msg'][] = "sorry! there is no any customer record in your team, please add customer first!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//���X�Ȥ�N��
		for ($i=0; $i< sizeof($cust_def); $i++)
		{
			$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//�H [�Ȥ�N��-�Ȥ�²��] �e�{
		}
		$op['cust_select'] =  $arry2->select($cust_value,'','PHP_cust','select','',$cust_def_vue); 
	}
		if (count($cust_def) == 0){  // ��䤣��Ȥ��Ʈ�......
			$op['msg'][] = "sorry! selected department doesn't have any customer record yet, please add customer for this Team first !";
			 $layout->assign($op);
			 $layout->display($TPL_ERROR);		    	    
				break;
		}

		page_display($op, 9, 2, $TPL_OFFER);			
		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "add_offer":	job 92A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "add_offer":

		check_authority(9,2,"add");

		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];  
		$where_str ='';
	if(isset($form_add)){  // �ѷs�W���s�W�O����(�D�I��[�J�P�@�ӫȤ�ɪ��s�W)
		if (!$PHP_dept_code){   // �p�G�S�����ﳡ���O��.....[ �u�i��O manager �n�J ]
			if (!$PHP_cust){    // �p�G�]�S�����ﳡ���Ȥ��
				$op['msg'][] = "sorry! please select the <b>Buyer</b> and <b>Team</b>!";
			} else {
				$op['msg'][] = "sorry! please select the working <b>Team</b> !";
			}
		
			$op['manager_flag'] = 1;  //�u��manager�~�|�i��o��
			$sales_dept_ary = get_sales_dept(); // ���X �~�Ȫ����� [���tK0,J0] ------
			//�~�ȳ��� select���
			$op['dept_ary'] = $arry2->select($sales_dept_ary,"","PHP_dept_code","select","");  

			$where_str=$where_str."order by cust_s_name"; //��cust_s_name�Ƨ�
			if(!$cust_def = $cust->get_fields('cust_init_name',$where_str)){;  //���X�Ȥ�²��
				$op['msg'][] = "sorry! there is no any customer record in your team, please add customer first!";
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//���X�Ȥ�N��
			for ($i=0; $i< sizeof($cust_def); $i++)
			{
				$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//�H [�Ȥ�N��-�Ȥ�²��] �e�{
			}
			$op['cust_select'] =  $arry2->select($cust_value,'','PHP_cust','select','',$cust_def_vue); 


			page_display($op, 9, 2, $TPL_OFFER);			
			break;

		}  // end if (!$PHP_dept_code).................. 

		// ���ο�J�������O��M�Ȥ��� �d�ݬO�_���Ȥ����ݩ�o�ӳ���
		$where_str =" WHERE dept ='$PHP_dept_code' ";
		$where_str=$where_str."order by cust_s_name"; //��cust_s_name�Ƨ�
		if(!$cust_def = $cust->get_fields('cust_init_name',$where_str)){;  //���X�Ȥ�²��
			$op['msg'][] = "sorry! there is no any customer record in your team, please add customer first!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//���X�Ȥ�N��
		for ($i=0; $i< sizeof($cust_def); $i++)
		{
			$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//�H [�Ȥ�N��-�Ȥ�²��] �e�{
		}


		if (count($cust_def) == 0){  // ��䤣��Ȥ��Ʈ�......
			$op['msg'][] = "sorry! selected department doesn't have any Buyer record yet,<br> please add Buyer for this Team first !";
			$layout->assign($op);
			$layout->display($TPL_ERROR);		    	    
			break;
		} else {

			if (!$PHP_cust){ //�������O �L��ܫȤH/�B�������ȤH��Ʈ�[���@�w�Omanager�i�J]
				// creat cust combo box
				$op['cust_select'] =  $arry2->select($cust_value,'','PHP_cust','select','',$cust_def_vue); 
				$op['msg'][] = " please select your <b>Buyer</b> first !";
				$op['dept_id'] = $PHP_dept_code;

				page_display($op, 9, 2, $TPL_OFFER);			
				break;
			} // end if (!$PHP_cust).................. 

		} // endif (count($cust_def) == 0)

		 // dept �� cust �Ҧ���J�� ................
		    $where_str = " WHERE (cust_s_name='".$PHP_cust."' AND dept='".$PHP_dept_code."') ";
			$s1 = $cust->search(0,$where_str);  // �d�ݿ�w�Ȥ�� �����O�_���Ĭ�...........
		 if ($s1['record_NONE']){
			$op['msg'][] = "sorry! the selected <b>Buyer</b> is not for the <b>work Team</b>.";
			$op['dept_id'] = $PHP_dept_code;

			// creat cust combo box
			$where_str =" WHERE dept ='$PHP_dept_code' ";
			$where_str=$where_str."order by cust_s_name"; //��cust_s_name�Ƨ�
			if(!$cust_def = $cust->get_fields('cust_init_name',$where_str)){;  //���X�Ȥ�²��
				$op['msg'][] = "sorry! there is no any customer record in your team, please add customer first!";
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//���X�Ȥ�N��
			for ($i=0; $i< sizeof($cust_def); $i++)
			{
				$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//�H [�Ȥ�N��-�Ȥ�²��] �e�{
			}
			$op['cust_select'] =  $arry2->select($cust_value,'','PHP_cust','select','',$cust_def_vue); 


			page_display($op, 9, 2, $TPL_OFFER);			
			break;
		 } 
	}  // end if(isset($form_add)) ----

		 // �����ΫȤ� [��J��� �ҥ��T��]

			$op['cust_id'] = $PHP_cust;

		// �ǤJ�Ѽ�


			$where_str="WHERE dept = '".$PHP_dept_code."' order by cust_s_name"; //��cust_s_name�Ƨ�
			$cust_def = $cust->get_fields('cust_init_name',$where_str);
			$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//���X�Ȥ�N��
			for ($i=0; $i< sizeof($cust_def); $i++)
			{
				$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//�H [�Ȥ�N��-�Ȥ�²��] �e�{
			}
			$op['agent_select'] =  $arry2->select($cust_value,'','PHP_agent','select','',$cust_def_vue); 



		$op['of']['date'] = $TODAY;
		$op['of']['cust'] = $PHP_cust;
		$op['of']['merchan'] = $GLOBALS['SCACHE']['ADMIN']['login_id']; 
		$op['of']['dept_code'] = $PHP_dept_code;
		$op['of']['fty'] = $arry2->select($FACTORY,'','PHP_fty','select','');

		// create new PS number ------
		$sr_key = substr($THIS_YEAR,2,2).substr($TODAY,5,2).substr($TODAY,8,2).'-';

		$op['num'] = $sr_key.'xx';
		
		page_display($op, 9, 2, $TPL_OFFER_ADD);			
		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "do_add_offer":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "offer_fab_add":
		check_authority(9,2,"add");
		if (!$PHP_id)	
		{
			$sr_key = substr($THIS_YEAR,2,2).substr($TODAY,5,2).substr($TODAY,8,2).'-';
			$num = $offer->get_new_number($sr_key); 
			$f1 = $offer->add_offer($num);
			$PHP_id = $f1;
			$PHP_num=$num;
		}
		$where_str = "WHERE id = ".$PHP_id;
		$item = $offer->get_one_field($PHP_group."_item",$where_str);
		$usage = $offer->get_one_field($PHP_group."_usage",$where_str);
		$price = $offer->get_one_field($PHP_group."_price",$where_str);
		$yy = $offer->get_one_field($PHP_group."_yy",$where_str);
		$num = $offer->get_one_field("num",$where_str);
		if(!$item)
		{
			$parm = array("item"	=>	$PHP_item,
										"usage"	=>	$PHP_usage,
										"price"	=>	$PHP_price,
										"yy"		=>	$PHP_yy,										
										"id"		=>	$PHP_id,
										);
		}else{
			$parm = array("item"	=>	$item."|".$PHP_item,
										"usage"	=>	$usage."|".$PHP_usage,
										"price"	=>	$price."|".$PHP_price,
										"yy"		=>	$yy."|".$PHP_yy,
										"id"		=>	$PHP_id,
										);
		}
		$f1 = $offer-> update_det($parm,$PHP_group);
		$message = "Add Material [ ".$PHP_item." ] detial";
		$s_total =$PHP_price * $PHP_yy;  //���Ƴ浧�p�p
		//echo $s_total;
		$f_total = $PHP_cost + $s_total;  //���B�`�p
		$l_total = $f_total / $PHP_gm; //���B�`�p(�]�AGM)
		if ($PHP_group =='fab') $group_name ="Fabric";
		if ($PHP_group =='acc') $group_name ="Accessory";
		if ($PHP_group =='other') $group_name ="Other";
		if ($PHP_group =='inter') $group_name ="InterLining";
		if ($PHP_group =='fus') $group_name ="Fusible";
		if (!$PHP_usage) $PHP_usage='&nbsp;';
		echo $message."|".$PHP_num."|".$PHP_id."|".$group_name."|".$PHP_item."|".$PHP_usage."|".$PHP_price."|".$PHP_yy."|";
		echo number_format($s_total,2,'.','')."|";
		echo "<input type='image' src='images/del.png'  onclick=\"del_det('$PHP_id','$PHP_group','$PHP_item','$PHP_usage',this)\">|";	
		echo number_format($f_total,2,'.','')."|".number_format($l_total,2,'.','');
exit;
	break;
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "offer_mat_del":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "offer_mat_del":
		check_authority(9,2,"add");
		$where_str = "WHERE id = ".$PHP_id;
		$item = $offer->get_one_field($PHP_group."_item",$where_str);
		$usage = $offer->get_one_field($PHP_group."_usage",$where_str);
		$price = $offer->get_one_field($PHP_group."_price",$where_str);
		$yy = $offer->get_one_field($PHP_group."_yy",$where_str);
		$num = $offer->get_one_field("num",$where_str);
		$ary_item = explode('|',$item);
		$ary_usage = explode('|',$usage);
		$ary_price = explode('|',$price);
		$ary_yy = explode('|',$yy);
		$del_price=$del_yy=0;
		$new_item=$new_usage=$new_price=$new_yy='';
		for ($i=0; $i<sizeof($ary_item); $i++)
		{
			if($ary_usage[$i] == $PHP_usage && $ary_item[$i] == $PHP_item)
			{
				$del_price=$ary_price[$i];
				$del_yy = $ary_yy[$i];
			}else{
				$new_item.=$ary_item[$i]."|";
				$new_usage.=$ary_usage[$i]."|";
				$new_price.=$ary_price[$i]."|";
				$new_yy.=$ary_yy[$i]."|";
			}
		}
		$new_item = substr($new_item,0,-1);
		$new_usage = substr($new_usage,0,-1);
		$new_price = substr($new_price,0,-1);
		$new_yy = substr($new_yy,0,-1);
		$parm = array("item"	=>	$new_item,
									"usage"	=>	$new_usage,
									"price"	=>	$new_price,
									"yy"		=>	$new_yy,										
									"id"		=>	$PHP_id,
									);

		$f1 = $offer-> update_det($parm,$PHP_group);
		$message = "Delete Material [ ".$PHP_item." ]";
		$s_total =$del_price * $del_yy;  //���Ƴ浧�p�p
		//echo $s_total;
		$f_total = $PHP_cost - $s_total;  //���B�`�p
		$l_total = $f_total / $PHP_gm; //���B�`�p(�]�AGM)

		echo $message."|";
		echo number_format($f_total,2,'.','')."|".number_format($l_total,2,'.','');
exit;
	break;	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "do_add_offer":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_add_offer":

		check_authority(9,2,"edit");
			$PHP_cons = $PHP_cons1."|".$PHP_cons2."|".$PHP_cons3;
			$parm = array("id"	=>	$PHP_id,
					"num"						=>	$PHP_num,
					"merchan"				=>	$PHP_merchan,
					"dept"					=>	$PHP_dept_code,
					"buyer"					=>	$PHP_cust,
					"agent"					=>	$PHP_agent,
					"style"					=>	$PHP_style,
					"des"						=>	$PHP_des,
					"fab"						=>	$PHP_fab,
					"cons"					=>	$PHP_cons,
					"spt"						=>	$PHP_spt,

					"tech"					=>	$PHP_tech,
					
					"cm"						=>	$PHP_cm,
					"tt_cost"				=>	$PHP_cost,
					"gm_rate"				=>	$PHP_gm,

					"fty"						=>	$PHP_fty,
					"comm"					=>	$PHP_comm,
					"quota"					=>	$PHP_quota,
					"pic"						=>	$PHP_pic,
					"pic_upload"		=>	$PHP_pic_upload,
				);
		// check ��J��Ƥ����T	------------------------	
//		$op['of'] = $parm;  // ���N�Ѽ��൹ $op ----


		// �䥦���B�z ---  �Nnl�নbr
			$parm['des'] = nl2br($parm['des']);
			$parm['fab'] = nl2br($parm['fab']);
			$parm['cons'] = nl2br($parm['cons']);
			$parm['tech'] = nl2br($parm['tech']);

		//------------ add to DB   --------------------------
		if (!$f = $offer->edit($parm)) {  
			$op['msg'] = $offer->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
		  break;
		}  // end if (!$F1)--------- ���\��J ---------------


		
		// ���X�ӵ� record ��� -----------------------------
		if (!$op['of']=$offer->get($PHP_id)) {
			$op['msg']= $offer->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$message = " Add OFFER record: [".$op['of']['num']."]";
		$op['msg'][] = $message;
			# �O���ϥΪ̰ʺA
		$log->log_add(0,"92A",$message);


		$op['of_log'] = $offer->get_log($PHP_id);  //���X�ӵ��O��
		// �ˬd �ۤ��O�_�s�b
		if(file_exists($GLOBALS['config']['root_dir']."/offer/".$op['of']['num'].".jpg")){
			$op['picture'] = "./offer/".$op['of']['num'].".jpg";
		} else {
			$op['picture'] = "./images/graydot.gif";
		}

		// HTML ���Ѽ�
		$op['of'] = $offer->create_items($op['of']);

		$back_str = "index2.php?PHP_action=offer_search&PHP_dept_code=&PHP_num=&PHP_cust=&PHP_style=&SCH_str=&SCH_end=&SCH_agt=";
		$op['back_str'] = $back_str;
		$op['of_sub'] = $arry2->select($OFFER_LOG,'','PHP_subject','select','');

		page_display($op, 9, 2, $TPL_OFFER_SHOW);			
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "offer_comment":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "offer_comment":

		check_authority(9,2,"add");		
		$PHP_c_date=$PHP_c_user=$PHP_s_date=$PHP_s_user='';
		if ($PHP_com)
		{
			$PHP_c_date = $TODAY;
			$PHP_c_user = $GLOBALS['SCACHE']['ADMIN']['login_id'];
			$PHP_com = nl2br($PHP_com); 
		}
		if ($PHP_status)
		{
			$PHP_s_date = $TODAY;
			$PHP_s_user = $GLOBALS['SCACHE']['ADMIN']['login_id'];
			$PHP_status = nl2br($PHP_status);
		}

		$parm = array(	
						"of_num"			=>	$PHP_of_num,
						"subject"			=>	$PHP_subject,
						"comment"			=>	$PHP_com,
						"status"			=>	$PHP_status,
						"c_date"			=>	$PHP_c_date,
						"c_user"			=>	$PHP_c_user,
						"s_date"			=>	$PHP_s_date,
						"s_user"			=>	$PHP_s_user,
		);
		$message = '';
		if ($PHP_subject)
		{
			//------------ add to DB   --------------------------
			if (!$f = $offer->add_log($parm)) {  
				$op['msg'] = $offer->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
			  break;
			}  // end if (!$F1)--------- ���\��J ---------------
		}else{
			$message = "Please select Subject First";
		}
	//	redirect_page($redir_str);
		
		$redir_str = "index2.php?PHP_action=offer_view&PHP_id=".$PHP_of_id."&cgiget=".$PHP_back_str."&PHP_msg=".$message;
		redirect_page($redir_str);
//		page_display($op, 9, 2, $TPL_OFFER_SHOW);			
		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "offer_comment":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "edit_offer_comment":
		check_authority(9,2,"add");

		if ($PHP_com && !isset($PHP_c_date))
		{
			$PHP_c_date = $TODAY;
			$PHP_c_user = $GLOBALS['SCACHE']['ADMIN']['login_id'];
			$PHP_com = nl2br($PHP_com); 
			$parm = array(	
						"of_num"			=>	$PHP_of_num,
						"comment"			=>	$PHP_com,
						"c_date"			=>	$PHP_c_date,
						"c_user"			=>	$PHP_c_user,
						"id"					=>	$PHP_log_id,
			);
			$f = $offer->edit_log($parm,1);
		}
		if ($PHP_status && !isset($PHP_s_date))
		{
			$PHP_s_date = $TODAY; 
			$PHP_s_user = $GLOBALS['SCACHE']['ADMIN']['login_id'];
			$PHP_status = nl2br($PHP_status);
			$parm = array(	
						"of_num"			=>	$PHP_of_num,
						"status"			=>	$PHP_status,
						"s_date"			=>	$PHP_s_date,
						"s_user"			=>	$PHP_s_user,
						"id"					=>	$PHP_log_id,
			);
			$f = $offer->edit_log($parm,2);

		}
		
		$redir_str = "index2.php?PHP_action=offer_view&PHP_id=".$PHP_of_id."&cgiget=".$PHP_back_str;
		redirect_page($redir_str);
//		page_display($op, 9, 2, $TPL_OFFER_SHOW);			
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "offer_search":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "offer_search":

		check_authority(9,2,"view");

		$parm = array(	"dept"		=>  $PHP_dept_code,
						"num"			=>  $PHP_num,
						"buyer"		=>	$PHP_cust,
						"style"		=>	$PHP_style,
						"agt"			=> 	$SCH_agt,
						"str"			=>	$SCH_str,
						"end"			=>	$SCH_end
				);

		// ---- �Q��PHP_dept_code�P�w�O�_ �~�ȳ����i�J
		if (!$op = $offer->search(1)) {  
			$op['msg']= $offer->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		// ---- �p�G ���O �e�ӿ�~�ȳ����i�J��...
		if (!$PHP_dept_code) {   
			$op['manager_flag'] = 1;
		}

		$op['dept_id'] = $PHP_dept_code;

		$op['msg']= $offer->msg->get(2);
		$op['cgi']= $parm;

		page_display($op, 9, 2, $TPL_OFFER_LIST);			
		break;
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		job 92V offer view
#		case "offer_view":		job 92V offer view
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "offer_view":

		check_authority(9,2,"view");

	if (isset($PHP_ver))
	{
		$op['of'] = $offer->get(0,$PHP_of_num,$PHP_ver);  //���X�ӵ��O��
		$op['none'] = 1;		
		$parm = $op['of'];
	}else{
		$op['of'] = $offer->get($PHP_id);  //���X�ӵ��O��
		if (!$op['of']) {
			$op['msg'] = $offer->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		
		$parm = $op['of'];
		
		// �[�J��^�e�� --------------
				
		$back_str = trim($cgiget)."&PHP_sr_startno=".$PHP_sr_startno."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_style=".$PHP_style."&SCH_agt=".$SCH_agt."&SCH_str=".$SCH_str."&SCH_end=".$SCH_end;

		$back_str2 = "PHP_action=offer_view&PHP_id=".$PHP_id."&PHP_num=".$parm['num'];

		$op['back_str2'] = $back_str2;
		$op['back_str'] = $back_str;		
	}
	$op['of_log'] = $offer->get_log($op['of']['num']);  //���X�ӵ��O��

	$op['search'] = '1';
		// �ˬd �ۤ��O�_�s�b
		if(file_exists($GLOBALS['config']['root_dir']."/offer/".$op['of']['num'].".jpg")){
			$op['picture'] = "./offer/".$op['of']['num'].".jpg";
		} else {
			$op['picture'] = "./images/graydot.gif";
		}
		$total=0;
		$op['of'] = $offer->create_items($op['of']);

		for ($i=0; $i < ($op['of']['ver']-1); $i++) 
		{
			$op['of']['ver_ary'][$i] = ($i+1);
		}
		$op['msg'] = $offer->msg->get(2);
		if(isset($PHP_msg) && $PHP_msg) $op['msg'][]=$PHP_msg;
		$op['of_sub'] = $arry2->select($OFFER_LOG,'','PHP_subject','select','');
		page_display($op, 9, 2, $TPL_OFFER_SHOW);			
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		job 92V offer view
#		case "offer_view":		job 92V offer view
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "offer_submit":

		check_authority(9,2,"view");
		
		$f1= $offer->update_field($PHP_id, 'status', 1);
		
		$op['of'] = $offer->get($PHP_id);  //���X�ӵ��O��
		if (!$op['of']) {
			$op['msg'] = $offer->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$op['of_log'] = $offer->get_log($op['of']['num']);  //���X�ӵ��O��		
		$parm = $op['of'];
		
		// �[�J��^�e�� --------------
		$op['back_str'] = $PHP_back_str;
	$op['search'] = '1';
		// �ˬd �ۤ��O�_�s�b
		if(file_exists($GLOBALS['config']['root_dir']."/offer/".$op['of']['num'].".jpg")){
			$op['picture'] = "./offer/".$op['of']['num'].".jpg";
		} else {
			$op['picture'] = "./images/graydot.gif";
		}
		$total=0;
		$op['of'] = $offer->create_items($op['of']);

//		$op['of']['tt_cost'] = get_currency($op['of']['tt_cost']);
		for ($i=0; $i < ($op['of']['ver']-1); $i++) 
		{
			$op['of']['ver_ary'][$i] = ($i+1);
		}
		$message= "Successfully submit offer :".$op['of']['num'];
		$op['msg'][] = $message;
		$log->log_add(0,"92A",$message);
		$op['of_sub'] = $arry2->select($OFFER_LOG,'','PHP_subject','select','');

		page_display($op, 9, 2, $TPL_OFFER_SHOW);			
		break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "offer_edit":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "offer_edit":

		check_authority(9,2,"edit");

		$my_dept = $GLOBALS['SCACHE']['ADMIN']['dept']; 

		if (!$parm = $offer->get($PHP_id)) {
			$op['msg'] = $offer->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		if ($parm['cons']) { $parm['cons'] = str_replace("<br />","",$parm['cons']);}
		if ($parm['tech']) { $parm['tech'] = str_replace("<br />","",$parm['tech']);}
		if ($parm['remark']) { $parm['remark'] = str_replace("<br />","",$parm['remark']);}
		if($my_dept != "RD"){  // �p�G�O��o�����H���i�J��......
  // �p�G�O SA �i�J��......
			if ($parm['des']) { $parm['des'] = str_replace("<br />","",$parm['des']);}
			if ($parm['fab']) { $parm['fab'] = str_replace("<br />","",$parm['fab']);}
		}

		//  HTML �ݭn���Ѽ� ---------------------------------------------
		$op['cust_id'] = $PHP_cust;
		$op['back_str'] = $PHP_back_str;
		$op['of'] = $parm;
		$op['my_dept'] = $my_dept;

		// �ˬd �ۤ��O�_�s�b
		if(file_exists($GLOBALS['config']['root_dir']."/offer/".$op['of']['num'].".jpg")){
			$op['picture'] = "./offer/".$op['of']['num'].".jpg";
		} else {
			$op['picture'] = "./images/graydot.gif";
		}

		$total=0;
		$op['of'] = $offer->create_items($op['of']);
		$op['of']['fty'] = $arry2->select($FACTORY,$op['of']['fty'],'PHP_fty','select','');

		$where_str="WHERE dept = '".$op['of']['dept']."' order by cust_s_name"; //��cust_s_name�Ƨ�
		$cust_def = $cust->get_fields('cust_init_name',$where_str);
		$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//���X�Ȥ�N��
		for ($i=0; $i< sizeof($cust_def); $i++)
		{
			$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//�H [�Ȥ�N��-�Ȥ�²��] �e�{
		}
		$op['agent_select'] =  $arry2->select($cust_value,$op['of']['agent'],'PHP_agent','select','',$cust_def_vue); 

		page_display($op, 9, 2, $TPL_OFFER_SA_EDIT);
	/*
		if($my_dept == "RD"){
			page_display($op, 9, 2, $TPL_OFFER_RD_EDIT);			
		}else{
			page_display($op, 9, 2, $TPL_OFFER_SA_EDIT);
		}
	*/
		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "do_offer_edit":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_offer_edit":

		check_authority(9,2,"edit");
		$PHP_my_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
		$PHP_cons = $PHP_cons1."|".$PHP_cons2."|".$PHP_cons3;
		if($PHP_my_dept == 'RD'){
			$parm = array("id"			=>	$PHP_id,
									"num"				=>	$PHP_num,
									"my_dept"		=>	$PHP_my_dept,
									"spt"				=>	$PHP_spt,
									"cons"			=>	$PHP_cons,
									"tech"			=>	$PHP_tech,
									"remark"		=>	$PHP_remark,

									"back_str"	=>	$PHP_back_str,
					);

		}else{
			$parm = array("id"	=>	$PHP_id,
					"my_dept"				=>	$PHP_my_dept,
					"num"						=>	$PHP_num,
					"merchan"				=>	$PHP_merchan,
					"dept"					=>	$PHP_dept_code,
					"buyer"					=>	$PHP_cust,
					"agent"					=>	$PHP_agent,
					"style"					=>	$PHP_style,
					"des"						=>	$PHP_des,
					"fab"						=>	$PHP_fab,
					"cons"					=>	$PHP_cons,
					"spt"						=>	$PHP_spt,

					"tech"					=>	$PHP_tech,
					
					"cm"						=>	$PHP_cm,
					"tt_cost"				=>	$PHP_cost,
					"gm_rate"				=>	$PHP_gm,

					"fty"						=>	$PHP_fty,
					"comm"					=>	$PHP_comm,
					"quota"					=>	$PHP_quota,
					
					"pic"						=>	$PHP_pic,
					"pic_upload"		=>	$PHP_pic_upload,
					"back_str"			=>	$PHP_back_str,
				);
		}

		// check ��J��Ƥ����T	------------------------	
		$op['of'] = $parm;  // ���N�Ѽ��൹ $op ----


		// �䥦���B�z ---  �Nnl�নbr
		if($PHP_my_dept == 'RD'){
			$parm['cons'] = nl2br($parm['cons']);
			$parm['tech'] = nl2br($parm['tech']);
		}else{
			$parm['des'] = nl2br($parm['des']);
			$parm['fab'] = nl2br($parm['fab']);
			$parm['cons'] = nl2br($parm['cons']);
			$parm['tech'] = nl2br($parm['tech']);
		}

		//------------ add to DB   --------------------------
		if (!$f = $offer->edit($parm)) {  
			$op['msg'] = $offer->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
		  break;
		}  // end if (!$F1)--------- ���\��J ---------------

		$message = " update OFFER sheet:[".$PHP_num."]";
		$op['msg'][] = $message;

			# �O���ϥΪ̰ʺA
		$log->log_add(0,"92A",$message);
		
		// ���X�ӵ� record ��� -----------------------------
		if (!$op['of']=$offer->get($f)) {
			$op['msg']= $offer->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$op['of_log'] = $offer->get_log($op['of']['num']);  //���X�ӵ��O��
		// �ˬd �ۤ��O�_�s�b
		if(file_exists($GLOBALS['config']['root_dir']."/offer/".$op['of']['num'].".jpg")){
			$op['picture'] = "./offer/".$op['of']['num'].".jpg";
		} else {
			$op['picture'] = "./images/graydot.gif";
		}

		// HTML ���Ѽ�
		$op['back_str'] = $parm['back_str'];

		$total=0;
		$op['of'] = $offer->create_items($op['of']);

		for ($i=0; $i < ($op['of']['ver']-1); $i++) 
		{
			$op['of']['ver_ary'][$i] = ($i+1);
		}
		$op['of_log'] = $offer->get_log($op['of']['num']);  //���X�ӵ��O��		
		$op['of_sub'] = $arry2->select($OFFER_LOG,'','PHP_subject','select','');
	
		page_display($op, 9, 2, $TPL_OFFER_SHOW);			
		break;




#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "offer_revise":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "offer_revise":

		check_authority(9,2,"edit");

		$my_dept = $GLOBALS['SCACHE']['ADMIN']['dept']; 

		if (!$parm = $offer->get($PHP_id)) {
			$op['msg'] = $offer->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		if ($parm['cons']) { $parm['cons'] = str_replace("<br />","",$parm['cons']);}
		if ($parm['tech']) { $parm['tech'] = str_replace("<br />","",$parm['tech']);}
		if ($parm['remark']) { $parm['remark'] = str_replace("<br />","",$parm['remark']);}

		if($my_dept != "RD"){  // �p�G�O��o�����H���i�J��......
  // �p�G�O SA �i�J��......
			if ($parm['des']) { $parm['des'] = str_replace("<br />","",$parm['des']);}
			if ($parm['fab']) { $parm['fab'] = str_replace("<br />","",$parm['fab']);}
		}

		//  HTML �ݭn���Ѽ� ---------------------------------------------
		$op['cust_id'] = $parm['buyer'];
		$back_str='';
		if(isset($PHP_style))
			$back_str = trim($cgiget)."&PHP_sr_startno=".$PHP_sr_startno."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_style=".$PHP_style;
		$op['back_str'] = $back_str;
		$op['of'] = $parm;
		$op['my_dept'] = $my_dept;

		// �ˬd �ۤ��O�_�s�b
		if(file_exists($GLOBALS['config']['root_dir']."/offer/".$op['of']['num'].".jpg")){
			$op['picture'] = "./offer/".$op['of']['num'].".jpg";
		} else {
			$op['picture'] = "./images/graydot.gif";
		}

		$total=0;
		$op['of'] = $offer->create_items($op['of']);

		$op['of']['fty'] = $arry2->select($FACTORY,$op['of']['fty'],'PHP_fty','select','');

		$where_str="WHERE dept = '".$op['of']['dept']."' order by cust_s_name"; //��cust_s_name�Ƨ�
		$cust_def = $cust->get_fields('cust_init_name',$where_str);
		$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//���X�Ȥ�N��
		for ($i=0; $i< sizeof($cust_def); $i++)
		{
			$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//�H [�Ȥ�N��-�Ȥ�²��] �e�{
		}
		$op['agent_select'] =  $arry2->select($cust_value,$op['of']['agent'],'PHP_agent','select','',$cust_def_vue); 


		if($my_dept == "RD"){
			page_display($op, 9, 2, $TPL_OFFER_RD_REV);			
		}else{
			page_display($op, 9, 2, $TPL_OFFER_SA_REV);
		}

		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "do_offer_revise":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_offer_revise":

		check_authority(9,2,"edit");
		$PHP_my_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
		$PHP_cons = $PHP_cons1."|".$PHP_cons2."|".$PHP_cons3;

		if($PHP_my_dept == 'RD'){
			$parm = array("id"		=>	$PHP_id,
					"num"			=>	$PHP_num,
					"spt"			=>	$PHP_spt,
					"cons"			=>	$PHP_cons,
					"tech"			=>	$PHP_tech,
					"remark"		=>	$PHP_remark,
					"ver"			=>	($PHP_ver+1),
					"k_date"		=>	$TODAY,
					"merchan"		=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
					"back_str"		=>	$PHP_back_str,
					);

		}else{
			$parm = array("id"	=>	$PHP_id,
					"num"						=>	$PHP_num,
					"dept"					=>	$PHP_dept_code,
					"buyer"					=>	$PHP_cust,
					"agent"					=>	$PHP_agent,
					"style"					=>	$PHP_style,
					"des"						=>	$PHP_des,
					"fab"						=>	$PHP_fab,
					"cons"					=>	$PHP_cons,
					"spt"						=>	$PHP_spt,

					"tech"					=>	$PHP_tech,

					"cm"						=>	$PHP_cm,
					"tt_cost"				=>	$PHP_cost,
					"gm_rate"				=>	$PHP_gm,

					"fty"						=>	$PHP_fty,
					"comm"					=>	$PHP_comm,
					"quota"					=>	$PHP_quota,

					"pic"						=>	$PHP_pic,
					"pic_upload"		=>	$PHP_pic_upload,
					"ver"						=>	($PHP_ver+1),
					"k_date"				=>	$TODAY,
					"merchan"				=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
					"back_str"			=>	$PHP_back_str,
				);
		}

		// check ��J��Ƥ����T	------------------------	
		$op['of'] = $parm;  // ���N�Ѽ��൹ $op ----


		// �䥦���B�z ---  �Nnl�নbr
		if($PHP_my_dept == 'RD'){
			$parm['cons'] = nl2br($parm['cons']);
			$parm['tech'] = nl2br($parm['tech']);
		}else{
			$parm['des'] = nl2br($parm['des']);
			$parm['fab'] = nl2br($parm['fab']);
			$parm['cons'] = nl2br($parm['cons']);
			$parm['tech'] = nl2br($parm['tech']);
		}

		//------------ add to DB   --------------------------
		if (!$f = $offer->revise($parm)) {  
			$op['msg'] = $offer->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
		  break;
		}  // end if (!$F1)--------- ���\��J ---------------

		$message = " REVISE OFFER sheet:[".$PHP_num."]";
		$op['msg'][] = $message;

			# �O���ϥΪ̰ʺA
		$log->log_add(0,"92A",$message);
		
		// ���X�ӵ� record ��� -----------------------------
		if (!$op['of']=$offer->get($f)) {
			$op['msg']= $offer->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

//�W�[����remark
		$PHP_status = $PHP_com = "RENEW New Version :[ ".$parm['ver']." ] On :[".$TODAY."]";
		$PHP_s_date = $PHP_c_date = $TODAY;
		$PHP_s_user = $PHP_c_user = $GLOBALS['SCACHE']['ADMIN']['login_id'];
		$parm = array(	
						"of_num"			=>	$op['of']['num'],
						"subject"			=>	'others',
						"comment"			=>	$PHP_com,
						"status"			=>	$PHP_status,
						"c_date"			=>	$PHP_c_date,
						"c_user"			=>	$PHP_c_user,
						"s_date"			=>	$PHP_s_date,
						"s_user"			=>	$PHP_s_user,
		);

		//------------ add to DB   --------------------------
		if (!$f = $offer->add_log($parm)) {  
			$op['msg'] = $offer->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
		  break;
		}  // end if (!$F1)--------- ���\��J ---------------




		$op['of_log'] = $offer->get_log($op['of']['num']);  //���X�ӵ��O��
		// �ˬd �ۤ��O�_�s�b
		if(file_exists($GLOBALS['config']['root_dir']."/offer/".$op['of']['num'].".jpg")){
			$op['picture'] = "./offer/".$op['of']['num'].".jpg";
		} else {
			$op['picture'] = "./images/graydot.gif";
		}

		// HTML ���Ѽ�
		$op['back_str'] = $PHP_back_str;

		$total=0;
		$op['of'] = $offer->create_items($op['of']);

		for ($i=0; $i < ($op['of']['ver']-1); $i++) 
		{
			$op['of']['ver_ary'][$i] = ($i+1);
		}
		$op['none']=1;
		$op['of_log'] = $offer->get_log($op['of']['num']);  //���X�ӵ��O��
		$op['of_sub'] = $arry2->select($OFFER_LOG,'','PHP_subject','select','');

		page_display($op, 9, 2, $TPL_OFFER_SHOW);			
		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		job 92V offer view
#		case "offer_view":		job 92V offer view
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "offer_print_a":

		check_authority(9,2,"view");

	if (isset($PHP_ver))
	{
		$of = $offer->get(0,$PHP_of_num,$PHP_ver);  //���X�ӵ��O��
	}else{
		$of = $offer->get($PHP_id);  //���X�ӵ��O��
	}

		// �ˬd �ۤ��O�_�s�b
		if(file_exists($GLOBALS['config']['root_dir']."/offer/".$of['num'].".jpg")){
			$op['picture'] = "./offer/".$of['num'].".jpg";
		} else {
			$op['picture'] = "./images/graydot.jpg";
		}
		$of = $offer->create_item_pdf($of);

//---------------------------------- ��Ʈw�@�~���� ------------------------------


include_once($config['root_dir']."/lib/class.pdf_offer_a.php");

$print_title = "OFFER";

$print_title2 = "VER.".$of['ver']."  on  ".$of['k_date'];
$PHP_id = $of['num'];
$creat=$user->get(0,$of['merchan']);
	if ($creat['name'])$of['merchan'] = $creat['name'];

$creator = $of['merchan'];

$pdf=new PDF_offer_a();
$pdf->SetAutoPageBreak("on");
$pdf->AliasNbPages(); 
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
// [��J] �q��򥻸��
$pdf->hend_title($of);

		$Y = $pdf->getY();
		
		$img_size = GetImageSize($op['picture']);
		
		if ($img_size[0] > $img_size[1])
		{
			$pdf->Image($op['picture'],10,28,40,0);
		}else{
			$pdf->Image($op['picture'],10,28,0,40);
		}
		
		$pdf->ln();
		$Y = $pdf->getY();
		$X = $pdf->getX();
		$pdf->setXY(10,$Y+5);
		$pdf->descript('Style Description : ',$of['des']);
		$pdf->descript('Fabrication : ',$of['fab']);
		$pdf->descript('Fab. Consump. : ',$of['cons']);
		$pdf->ln();
		$pdf->Cell(185,5,"---------- unit cost ----------",0,0,'C');
		$pdf->ln();
		$pdf->Table_title(); //Unit Cost��Title
		//CM���e
		$pdf->Table_cost("C.M.", number_format($of['cm'],2,'.',''));
		
		$pdf->Table_cost_a($of['fab_cat']); //�D��
		$pdf->Table_cost_a($of['fus_cat']);	//fusible
		$pdf->Table_cost_a($of['inter_cat']); //interling
		$pdf->Table_cost_a($of['acc_cat']);		//accessory
		$pdf->Table_cost_a($of['oth_cat']);		//other

		//Mark-Up���e
		$pdf->Table_cost("Mark-Up", $of['gm_rate']."%");

		//Commission���e
		$pdf->Table_cost("Commission", $of['comm']."%");

		//Total Costing���e
		$pdf->Table_cost("Total Costing", number_format($of['tt_cost'],2,'.',''));

		$pdf->ln();
		//Technical / Equipment Advisements:
		$pdf->SetFont('Arial','',8);	
		$pdf->cell(50,5,"Technical / Equipment Advisements:",0,0,'R');
		$pdf->cell(135,5,$of['tech'],0,0,'L');		
		
		$name=$of['num'].'_a.pdf';
$pdf->Output($name,'D');
break;	


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		job 92V offer view
#		case "offer_view":		job 92V offer view
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "offer_print_b":

		check_authority(9,2,"view");

	if (isset($PHP_ver))
	{
		$of = $offer->get(0,$PHP_of_num,$PHP_ver);  //���X�ӵ��O��
	}else{
		$of = $offer->get($PHP_id);  //���X�ӵ��O��
	}

		// �ˬd �ۤ��O�_�s�b
		if(file_exists($GLOBALS['config']['root_dir']."/offer/".$of['num'].".jpg")){
			$op['picture'] = "./offer/".$of['num'].".jpg";
		} else {
			$op['picture'] = "./images/graydot.jpg";
		}
		$of = $offer->create_item_pdf($of);

//---------------------------------- ��Ʈw�@�~���� ------------------------------


include_once($config['root_dir']."/lib/class.pdf_offer_a.php");

$print_title = "OFFER";

$print_title2 = "VER.".$of['ver']."  on  ".$of['k_date'];
$PHP_id = $of['num'];
$creat=$user->get(0,$of['merchan']);
	if ($creat['name'])$of['merchan'] = $creat['name'];

$creator = $of['merchan'];

$pdf=new PDF_offer_a();
$pdf->SetAutoPageBreak("on");
$pdf->AliasNbPages(); 
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();

$pdf->SetFont('Arial','B',14);
// [��J] �q��򥻸��
$pdf->hend_title($of);

		$Y = $pdf->getY();
		
		$img_size = GetImageSize($op['picture']);
		
		if ($img_size[0] > $img_size[1])
		{
			$pdf->Image($op['picture'],10,28,40,0);
		}else{
			$pdf->Image($op['picture'],10,28,0,40);
		}
		
		$pdf->ln();
		$Y = $pdf->getY();
		$X = $pdf->getX();
		$pdf->setXY(10,$Y+5);
		$pdf->descript('Style Description : ',$of['des']);
		$pdf->descript('Fabrication : ',$of['fab']);
		$pdf->descript('Fab. Consump. : ',$of['cons']);
		$pdf->ln();
		$pdf->Cell(185,5,"---------- unit cost ----------",0,0,'C');
		$pdf->ln();
		$pdf->Table_title(); //Unit Cost��Title
		
		$pdf->Table_cost_a($of['fab_cat']); //�D��
		$pdf->Table_cost_a($of['fus_cat']);	//fusible
		$pdf->Table_cost_a($of['inter_cat']); //interling
		$pdf->Table_cost_a($of['acc_cat']);		//accessory
		$pdf->Table_cost_a($of['oth_cat']);		//other

		//CM-TP���e (CM�Pmark-up�X��)
		$pdf->Table_cost("CM-TP", number_format($of['cmtp'],2,'.',''));

		//Total Costing���e
		$pdf->Table_cost("Total Costing", number_format($of['tt_cost'],2,'.',''));

		$pdf->ln();
		//Technical / Equipment Advisements:
		$pdf->SetFont('Arial','',8);	
		$pdf->cell(50,5,"Technical / Equipment Advisements:",0,0,'R');
		$pdf->cell(135,5,$of['tech'],0,0,'L');		
		
		$name=$of['num'].'_b.pdf';
$pdf->Output($name,'D');
break;	


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		job 92V offer view
#		case "offer_view":		job 92V offer view
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "offer_print_c":

		check_authority(9,2,"view");

	if (isset($PHP_ver))
	{
		$of = $offer->get(0,$PHP_of_num,$PHP_ver);  //���X�ӵ��O��
	}else{
		$of = $offer->get($PHP_id);  //���X�ӵ��O��
	}

		// �ˬd �ۤ��O�_�s�b
		if(file_exists($GLOBALS['config']['root_dir']."/offer/".$of['num'].".jpg")){
			$op['picture'] = "./offer/".$of['num'].".jpg";
		} else {
			$op['picture'] = "./images/graydot.jpg";
		}
		$of = $offer->create_item_pdf($of);

//---------------------------------- ��Ʈw�@�~���� ------------------------------


include_once($config['root_dir']."/lib/class.pdf_offer_a.php");

$print_title = "OFFER";

$print_title2 = "VER.".$of['ver']."  on  ".$of['k_date'];
$PHP_id = $of['num'];
$creat=$user->get(0,$of['merchan']);
	if ($creat['name'])$of['merchan'] = $creat['name'];

$creator = $of['merchan'];

$pdf=new PDF_offer_a();
$pdf->AliasNbPages(); 
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();
$pdf->SetAutoPageBreak("on");
$pdf->SetFont('Arial','B',14);
// [��J] �q��򥻸��
$pdf->hend_title($of);

		$Y = $pdf->getY();
		
		$img_size = GetImageSize($op['picture']);
		
		if ($img_size[0] > $img_size[1])
		{
			$pdf->Image($op['picture'],10,28,40,0);
		}else{
			$pdf->Image($op['picture'],10,28,0,40);
		}
		
		$pdf->ln();
		$Y = $pdf->getY();
		$X = $pdf->getX();
		$pdf->setXY(10,$Y+5);
		$pdf->descript('Style Description : ',$of['des']);
		$pdf->descript('Fabrication : ',$of['fab']);
		$pdf->descript('Fab. Consump. : ',$of['cons']);
		$pdf->ln();
		$pdf->Cell(185,5,"---------- unit cost ----------",0,0,'C');
		$pdf->ln();

		//Total Costing���e
		$pdf->Table_cost("Total Costing", number_format($of['tt_cost'],2,'.',''));

		$pdf->ln();
		//Technical / Equipment Advisements:
		$pdf->SetFont('Arial','',8);	
		$pdf->cell(50,5,"Technical / Equipment Advisements:",0,0,'R');
		$pdf->cell(135,5,$of['tech'],0,0,'L');		
		
		$name=$of['num'].'_b.pdf';
$pdf->Output($name,'D');
break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		job 92V offer view
#		case "offer_view":		job 92V offer view
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "offer_to_order":

		check_authority(10,1,"add");

		$of = $offer->get($PHP_id);  //���X�ӵ��O��
		// �ˬd �ۤ��O�_�s�b
		if(file_exists($GLOBALS['config']['root_dir']."/offer/".$of['num'].".jpg")){
			$op['order']['pic'] = "./offer/".$of['num'].".jpg";
		} else {
			$op['order']['pic'] = "./images/graydot.jpg";
		}
		$of = $offer->create_items($of);
		
		//�q���ƾ�z

		// ���X�~�X...
		$dt = decode_date(1);
		$year_code = substr($dt['year'],-1);
		
//*****//2006.12.08	�ק�sample���U�Ԧ� start
		$s_date=increceDaysInDate($TODAY,-270); //9�Ӥ�e
		$where="where last_update > '".$s_date."' and cust='".$of['buyer']."'  order by num";
		
		$smpl_no=$smpl_ord->get_fields("num",$where);		
	    $j=0;
	    if ($smpl_no){
		$sml_no[0]=substr($smpl_no[0],0,9);
		for($i=1;$i< sizeof($smpl_no); $i++)
		{
			if($sml_no[$j] != substr($smpl_no[$i],0,9)){	
				$j++;
				$sml_no[$j]=substr($smpl_no[$i],0,9);
			}
		}
		}else{
			$sml_no=array('');
		}
		$op['smpl_no']=$arry2->select($sml_no,'','PHP_smpl_ord','select','');
//*****//2006.12.08	�ק�sample���U�Ԧ�	end

		$where_str="WHERE dept = '".$of['dept']."' order by cust_s_name"; //��cust_s_name�Ƨ�
		$cust_def = $cust->get_fields('cust_init_name',$where_str);
		$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//���X�Ȥ�N��
		for ($i=0; $i< sizeof($cust_def); $i++)
		{
			$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//�H [�Ȥ�N��-�Ȥ�²��] �e�{
		}
		$op['agent_select'] =  $arry2->select($cust_value,$of['agent'],'PHP_agent','select','',$cust_def_vue); 

		// creat style type combo box
		$style_def = $style_type->get_fields('style_type');   // ���X �ڦ����O
//		$op['style'] =  $arry2->select($style_def,'','PHP_style','select','');  	
		// creat apparel unit combo box
		$op['unit'] = $arry2->select($APPAREL_UNIT,'pc','PHP_unit','select','');  	

		// creat factory combo box
		if (substr($of['dept'],0,1)=='H'){//�P�_�i�J�̪������O�_�N��u�t			
			$op['factory'] = "HJ";
			$op['fac_flag']=1;	
		}elseif (substr($of['dept'],0,1)=='L'){		
			$op['factory'] = "LY";
			$op['fac_flag']=1;			
		}else{
			$op['factory'] = $arry2->select($FACTORY,$of['fty'],'PHP_factory','select',''); 	
		}  


		// pre  �q��s��....
		$op['order_precode'] = substr($of['dept'],1,1).$of['buyer'].$year_code."-xxx";
		if ($of['dept']=="HJ" || $of['dept']=="LY")		$op['order_precode'] = substr($of['dept'],0,1).$of['buyer'].$year_code."-xxx";
		// 2005/07/30 �[�J �� menu�i�J�s�W�᪺ back page �u�V �����C��
		$op['back_str'] = "/yankee/index2.php?PHP_action=order_search&PHP_sr_startno=0&PHP_dept_code=&PHP_order_num=&PHP_cust=&PHP_ref=&PHP_factory=";

		$op['cust_id'] = $of['buyer'];
		$op['dept_id'] = $of['dept'];
		$op['order']['ref'] = $of['style'];
		$op['order']['uprice'] = $of['tt_cost'];
		$op['order']['mat_useage'] =0;
		for ($i=0; $i<sizeof($of['fab_cat']); $i++)
		{
			$op['order']['mat_useage'] += $of['fab_cat']['fq']
		}


		page_display($op, 10, 1, $TPL_ORDER_ADD);	
break;

















#++++++++++++++    SAMPLE ORDER +++++++++++++  2006/09/04  +++++++++++++++++
#		 job 20    �˥��t�� 
#++++++++++++++++++++++++++++++++++++++++++++  2006/10/09  +++++++++++++++++
#		case "smpl_ord":	job 21
#		case "smpl_ord_add":
#		case "do_smpl_ord_add":
#		case "smpl_follow_order":
#		case "do_smpl_ord_follow":
#		case "smpl_ord_edit":
#		case "do_smpl_ord_edit":
#		case "smpl_ord_search":
#		case "smpl_ord_view":
#		case "smpl_ord_del":	�R���˥���(2007/03/19)
#		case "smpl_ord_sumb": 	�֥i�˥���(2009/03/19)
#		case "do_smpl_logs_add":       �˥� logs �g�J  (����)
#		case "smpl_supplier":	job 22
#		case "smpl_supplier_search":
#		case "smpl_supplier_view":
#		case "smpl_wi_rcvd":
#		case "smpl_wi_back":	�٥h ��� W/I ���쪺�O�� (�u����ѥi��)
#		case "smpl_pttn_rcvd":
#		case "smpl_pttn_back":	�٥h ��� pattern ���쪺�O�� (�u����ѥi��)
#		case "smpl_fab_rcvd":
#		case "smpl_fab_back":	�٥h ��� fabric ���쪺�O�� (�u����ѥi��)
#		case "smpl_acc_rcvd":
#		case "smpl_acc_back":	�٥h ��� fabric ���쪺�O�� (�u����ѥi��)
#		case "smpl_schedule":	job 23 �˥��Ʋ� [ pattern, Sample ]
#		case "smpl_schedule_search"
#		case "smpl_schedule_view":
#		case "smpl_pttn_schd":
#		case "smpl_smpl_schd":
#		case "smpl_output":		job 24
#		case "smpl_output_search":
#		case "smpl_output_view":
#		case "upload_smpl_pattern":
#		case "upload_smpl_marker":
#		case "smpl_pttn_download":
#		case "smpl_marker_download":
#		case "smpl_complete":
#		case "smpl_case_close":
#		case "pttn_resend":
#		case "marker_resend":
#		case "smpl_SPT":
#
#
#
#
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "smpl_ord":	job 21
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "smpl_ord":

		check_authority(2,1,"view");

		$where_str = $manager = $dept_id = '';
		$dept_ary = array();

		$sales_dept_ary = get_sales_dept(); //  [���tK0] ------
		
		$user_dept = $_SESSION['SCACHE']['ADMIN']['dept']; 
		$user_team = $_SESSION['SCACHE']['ADMIN']['team_id'];
		
		for ($i=0; $i<count($sales_dept_ary);$i++){
			if($user_dept == $sales_dept_ary[$i]){
				// �p�G�O�~�ȳ��i�J �hdept_code ���w�ӷ~�ȳ�---
				$dept_id = $sales_dept_ary[$i];  
			}
		}
		if (!$dept_id) {    // ���O�~�ȳ��H[�]���t K0 ���H ]�i�J��
			$manager = 1;
			// �~�ȳ��� select���
			$dept_ary = $arry2->select($sales_dept_ary,"","PHP_dept_code","select","");  
		} else {
			$where_str = " WHERE dept = '".$dept_id."' ";
		}
		
		$team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
		if(($user_dept == 'HJ' ||$user_dept == 'LY' ) && $team<>'MD')
		{
			$manager = 1;
			$manager_v = 1;
			//�~�ȳ��� select���
			$dept_ary = $arry2->select($sales_dept_ary,"","PHP_dept_code","select","");  
			$where_str ='';

		}
		
		$op['manager_flag'] = $manager;
		$op['dept_id'] = $dept_id;
		$op['dept_ary'] = $dept_ary;		
		$op['msg'] = $order->msg->get(2);
		
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
  

		// ------ �M�w factory ���Ѽ� = �}�C? �ƭ�?  RD�H���i�J�ɦ��t�O ----
		if($S = $smpl_ord->is_sample_team($user_team)){
			$op['factory'] = $user_team."<input type='hidden' name='PHP_factory' value='".$user_team."'>";
		}else{
			$op['factory'] = $arry2->select($SAMPLER,'','PHP_factory','select','');  	
		}
		if($user_team=="RD"){
			$op['factory'] = $user_dept."<input type='hidden' name='PHP_factory' value='".$user_dept."'>";
		}
		// --------------------------------------------------------------------

		page_display($op, 2, 1, $TPL_SMPL_ORD);			
		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		job 21A     SMPL ORDER record add
#		case "smpl_ord_add":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "smpl_ord_add":

		check_authority(2,1,"add");

		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];  
		$dept_id = $GLOBALS['SCACHE']['ADMIN']['dept'];		
		$user_team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
		$where_str ='';

		if (!$PHP_dept_code){   // �p�G�S�����ﳡ���O��.....[ �u�i��O manager �n�J ]
			if (!$PHP_cust){    // �p�G�]�S�����ﳡ���Ȥ��
				$op['msg'][] = "sorry! please select the <b>customer</b> and <b>Team</b>!";
			} else {
				$op['msg'][] = "sorry! please select the working <b>Team</b> !";
			}
				$op['manager_flag'] = 1;
			$sales_dept_ary = get_sales_dept(); // ���X �~�Ȫ����� [���tK0] ------
			//�~�ȳ��� select���
			$op['dept_ary'] = $arry2->select($sales_dept_ary,"","PHP_dept_code","select","");  

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
			// creat factory combo box
			$op['factory'] = $arry2->select($SAMPLER,'','PHP_factory','select','');  	
			if($user_team=="RD"){
				$op['factory'] = $user_dept."<input type='hidden' name='PHP_factory' value='".$user_dept."'>";
			}
			page_display($op, 2, 1, $TPL_SMPL_ORD);			
			break;

		 }  // end if (!$PHP_dept_code).................. 

		// ���ο�J�������O��M�Ȥ��� �d�ݬO�_���Ȥ����ݩ�o�ӳ���
		$where_str =" WHERE dept ='$PHP_dept_code' ";
		$where_str=$where_str."order by cust_s_name";
		$cust_def = $cust->get_fields('cust_init_name',$where_str);   // ���X �Ȥ�N��
		$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);
		for ($i=0; $i< sizeof($cust_def); $i++)
		{
			$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];
		}
		if (count($cust_def) == 0){  // ��䤣��Ȥ��Ʈ�......
			$op['msg'][] = "sorry! selected department doesn't have any customer record yet, please add customer for this Team first !";
			 $layout->assign($op);
			 $layout->display($TPL_ERROR);		    	    
				break;
		} else {

		 if (!$PHP_cust){	//�������O �L��ܫȤH/�B�������ȤH��Ʈ�[���@�w�Omanager�i�J]
		 
			// creat cust combo box
			sort($cust_def);
			$op['cust_select'] =  $arry2->select($cust_value,'','PHP_cust','select','',$cust_def_vue);   	
		
			$op['msg'][] = "sorry! please select your customer !";
			$op['dept_id'] = $PHP_dept_code;

			// creat factory combo box
				$op['factory'] = $arry2->select($SAMPLER,'','PHP_factory','select','');  	
			if($user_team=="RD"){
				$op['factory'] = $user_dept."<input type='hidden' name='PHP_factory' value='".$user_dept."'>";
			}
			page_display($op, 2, 1, $TPL_SMPL_ORD);			
			break;

		  } // end if (!$PHP_cust).................. 
		} // endif (count($cust_def) == 0)

		 // dept �� cust �Ҧ���J�� ................
		   $where_str = " WHERE (cust_s_name='".$PHP_cust."' AND dept='".$PHP_dept_code."') ";
			$s1 = $cust->search(0,$where_str);  // �d�ݿ�w�Ȥ�� �����O�_���Ĭ�...........
		 if ($s1['record_NONE']){
			$op['msg'][] = "sorry! the selected custormer is not belongs to your dept.�C";
			$op['dept_id'] = $PHP_dept_code;

			// creat cust combo box
			$where_str =" WHERE dept ='$PHP_dept_code' ";
			$where_str=$where_str."order by cust_s_name";
			$cust_def = $cust->get_fields('cust_init_name',$where_str);   // ���X �Ȥ�N��
			$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);   // ���X �Ȥ�N��
			for ($i=0; $i< sizeof($cust_def); $i++)
			{
				$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];
			}
			$op['cust_select'] =  $arry2->select($cust_value,'','PHP_cust','select','',$cust_def_vue);  

			// creat factory combo box
			$op['factory'] = $arry2->select($SAMPLER,'','PHP_factory','select','');  	

			page_display($op, 2, 1, $TPL_SMPL_ORD);			
			break;

		 } 
		 // �����ΫȤᤣ�t�X�ɵ���[��J��� �ҥ��T��]
			$op['msg']= $order->msg->get(2);
			$op['cust_id'] = $PHP_cust;

		// ���X�~�X...
		$dt = decode_date(1);
		$year_code = substr($dt['year'],-1);

		// creat ETD, FETA, AETA, PETA date combo box
		// �w�] customer pattern support�� yes
		$op['order']['pttn_suppt'] = 1;

		// creat factory combo box
		$op['factory'] = $arry2->select($SAMPLER,'','PHP_factory','select','');  	
		// creat style type combo box
		$style_def = $style_type->get_fields('style_type');   // ���X �ڦ����O
		$op['style_type'] =  $arry2->select($style_def,'','PHP_style_type','select','');  	
		// creat apparel unit combo box
		$op['unit'] = $arry2->select($APPAREL_UNIT,'','PHP_unit','select','');  	

		// creat STYLE CAT. combo box
		$op['style_cat'] = $arry2->select($STYLE_CAT,'','PHP_style_cat','select','');  	
		// creat SMPL TYPE combo box
		$smpl_def = $smpl_type->get_fields('smpl_type');   // ���X �ڦ����O
		$op['smpl_type'] =  $arry2->select($smpl_def,'','PHP_smpl_type','select','');  	

		// ���X�ﶵ��� �ζǤJ���Ѽ�
		$op['dept_id']	 = $PHP_dept_code;
		// pre  �q��s��....
		$op['order_precode'] = "S".$year_code.$PHP_cust."xxxx-X";

		page_display($op, 2, 1, $TPL_SMPL_ORD_ADD);			
		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "do_smpl_ord_add":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_smpl_ord_add":

		check_authority(2,1,"add");
		
		// ���X�~�X... �H��J�ɤ��~���~�X[���]
		$dt = decode_date(1);
		$year_code = substr($dt['year'],-1);

		$parm = array(	"dept"			=>	$PHP_dept,
						"cust"			=>	$PHP_cust,
						"ref"			=>	$PHP_ref,
						"factory"		=>	$PHP_factory,
						"style_type"	=>	$PHP_style_type,
						"qty"			=>	$PHP_rq + $PHP_backup,
						"rq"			=>	$PHP_rq,
						"backup"		=>	$PHP_backup,
						"unit"			=>	$PHP_unit,
						"smpl_type"		=>	$PHP_smpl_type,
						"style_cat"		=>	$PHP_style_cat,
						
						"orders"		=>	"",

//�s�W���ɤW��						
						"pic"			=>	$PHP_pic,
						"pic_upload"	=>	$PHP_pic_upload,
						
						"pttn_suppt"	=>	$PHP_pttn_suppt,
						"etd"			=>	$PHP_etd,
						"feta"			=>	$PHP_feta,
						"aeta"			=>	$PHP_aeta,
						"peta"			=>	$PHP_peta,
						"creator"		=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
						"open_date"		=>	$dt['date'],
						"updator"		=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
						"last_update"	=>	$dt['date']
			);

	$parm['fab_substu'] = (isset($PHP_fab_substu)) ? $PHP_fab_substu : '';
	$parm['acc_substu'] = (isset($PHP_acc_substu)) ? $PHP_acc_substu : '';
		
		// check ��J��Ƥ����T	------------------------	
			$op['smpl_ord'] = $parm;

	if (!$f1 = $smpl_ord->check($parm)) {  // ��J��� �����T��
		$op['msg'] = $smpl_ord->msg->get(2);

		// �C�X �s�W �q�檺���� ---------------------------------------------
		$op['cust_id'] = $PHP_cust;
		$op['dept_id'] = $PHP_dept;

		$op['msg']= $smpl_ord->msg->get(2);
		
		// creat factory combo box
		$op['factory'] = $arry2->select($SAMPLER,$parm['factory'],'PHP_factory','select','');  	
		// creat style type combo box
		$style_def = $style_type->get_fields('style_type');   // ���X �ڦ����O
		$op['style_type'] =  $arry2->select($style_def,$parm['style_type'],'PHP_style_type','select','');  	
		// creat apparel unit combo box
		$op['unit'] = $arry2->select($APPAREL_UNIT,$parm['unit'],'PHP_unit','select','');  	

		// creat STYLE CAT. combo box
		$op['style_cat'] = $arry2->select($STYLE_CAT,$parm['style_cat'],'PHP_style_cat','select','');  	
		// creat SMPL TYPE combo box
//		$op['smpl_type'] = $arry2->select($SMPL_TYPE,'','PHP_smpl_type','select','');  	
		$smpl_def = $smpl_type->get_fields('smpl_type');   // ���X �ڦ����O
		$op['smpl_type'] =  $arry2->select($smpl_def,$parm['smpl_type'],'PHP_smpl_type','select','');  	

		// ���X�ﶵ��� �ζǤJ���Ѽ�
		$op['dept_id']	 = $PHP_dept;
		// pre  �q��s��....
		$op['order_precode'] = "S".$year_code.$PHP_cust."xxxx-X";

		$op['order'] = $parm;

		page_display($op, 2, 1, $TPL_SMPL_ORD_ADD);			
		break;
		
	}   // end check [ ��ƿ�J���T�� ]
		
		//  �s�s �q �� ���X �]�P�ɧ�sdept�ɤ���num��[csv]
		$smpl_serious = $dept->get_smpl_num($year_code, $PHP_cust);  
		$parm['num'] = $smpl_serious."-1";  

		//------------------ add to DB	
		$f1 = $smpl_ord->add($parm);

		if (!$f1) {  // �S�����\�g�J��Ʈw��
				$op['msg'] = $smpl_ord->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
			break;
		}   // end if (!$F1)--------- ���\��J ---------------------------------

		$message = " Append New SAMPLE order: [".$parm['num']."]";
		
		// ���X�ӵ� order ��� ------------------------------------------------- 
		if (!$op['order']=$smpl_ord->get($f1)) {
			$op['msg']= $smpl_ord->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
			# �O���ϥΪ̰ʺA
		$log->log_add(0,"21A",$message);
		$op['msg'][] = $message;
		
				// �ˬd �ۤ��O�_�s�b
		if(file_exists($GLOBALS['config']['root_dir']."/smpl_pic/".$op['order']['num'].".jpg")){
			$op['main_pic'] = "./smpl_pic/".$op['order']['num'].".jpg";
		} else {
			$op['main_pic'] = "./images/graydot.gif";
		}
		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];   // �P�w�i�J����������(����)
		if (substr($user_dept,0,1)=="H" || substr($user_dept,0,1)=="L")  //�P�_�ϥΪ̳����O�_���u�t
		{                                                                //�H���j�קﵥ�v��
			if ($user_dept == $op['order']['dept'])$op['dept_edit']=1;
			if ($user_dept =="H0" && substr($op['order']['dept'],0,1)=="H")$op['dept_edit']=1;
			if ($user_dept =="L0" && substr($op['order']['dept'],0,1)=="L")$op['dept_edit']=1;
		}else{
			$op['dept_edit']=1;
		}		
		$op['back_str']="index2.php?PHP_action=smpl_ord";
		// creat LOG SUBJ combo box
		$op['logs_subj'] = $arry2->select($SMPL_LOG_SUBJ,'','PHP_subj','select','');  	
		$op['user_e']=$GLOBALS['SCACHE']['ADMIN']['name'];
		$op['ord_link_non']=1;

		$img_size = GetImageSize($op['main_pic']);
		if ($img_size[0] < $img_size[1]) $op['height'] = 1;

		
		page_display($op, 2, 1, $TPL_SMPL_ORD_SHOW);			
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "smpl_follow_order":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "smpl_follow_order":

		check_authority(2,1,"add");

		$parm = $smpl_ord->get($PHP_id);
		if (!$parm) {
			$op['msg'] = $smpl_ord->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		// �C�X �s�W �q�檺���� ---------------------------------------------
			$op['cust_id'] = $parm['cust'];
			$op['dept_id'] = $parm['dept'];

		// �s�q�渹�X -----------------------------------------start
			$body = substr($parm['num'],0,10);
		//  ���X�̤j �q�橵���X  ... �]�w �s�q�渹�X
		$orderA = $smpl_ord->get_order_like($body);
		if (!$orderA) {
			$op['msg'] = $smpl_ord->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		sort($orderA);
		$code = substr($orderA[count($orderA)-1]['num'],10) + 1;
		$new_num = $body."$code";
		// �s�q�渹�X -----------------------------------------end

			$op['msg']= $smpl_ord->msg->get(2);
	
		// creat factory combo box
		$op['factory'] = $arry2->select($SAMPLER,$parm['factory'],'PHP_factory','select','');  	
		// creat style type combo box
		$style_def = $style_type->get_fields('style_type');   // ���X �ڦ����O
		$op['style_type'] =  $arry2->select($style_def,$parm['style'],'PHP_style_type','select','');  	
		// creat unit combo box
		$op['unit'] = $arry2->select($APPAREL_UNIT,$parm['unit'],'PHP_unit','select','');  	

		// creat STYLE CAT. combo box
		$op['style_cat'] = $arry2->select($STYLE_CAT,'','PHP_style_cat','select','');  	
		// creat SMPL TYPE combo box
		$smpl_def = $smpl_type->get_fields('smpl_type');   // ���X �ڦ����O
		$op['smpl_type'] =  $arry2->select($smpl_def,'','PHP_smpl_type','select','');  	

		// ���X�ﶵ��� �ζǤJ���Ѽ�
		$op['dept_id']	 = $parm['dept'];
		// pre  �q��s��....
		$op['order_precode'] = $new_num;

		$op['order'] = $parm;
		$op['order']['num'] = $new_num;
		$op['order']['pttn_suppt'] = '1';
		$op['order']['fab_substu'] = '';
		$op['order']['acc_substu'] = '';
		$op['order']['rq'] = '';
		$op['order']['backup'] = '';


		page_display($op, 2, 1, $TPL_SMPL_ORD_FOLLOW);			
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "do_smpl_ord_follow":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_smpl_ord_follow":

		check_authority(2,1,"add");

		$parm = array(	"dept"			=>	$PHP_dept,
				"cust"			=>	$PHP_cust,
				"ref"			=>	$PHP_ref,
				"factory"		=>	$PHP_factory,
				"style_type"	=>	$PHP_style_type,
				"qty"			=>	$PHP_rq + $PHP_backup,
				"rq"			=>	$PHP_rq,
				"backup"		=>	$PHP_backup,
				"unit"			=>	$PHP_unit,
				"smpl_type"		=>	$PHP_smpl_type,
				"style_cat"		=>	$PHP_style_cat,
				
				"orders"		=>	$PHP_orders,
//�s�W���ɤW��						
				"pic"			=>	$PHP_pic,
				"pic_upload"	=>	$PHP_pic_upload,
					
				"pttn_suppt"	=>	$PHP_pttn_suppt,
				"etd"			=>	$PHP_etd,
				"feta"			=>	$PHP_feta,
				"aeta"			=>	$PHP_aeta,
				"peta"			=>	$PHP_peta,
				"creator"		=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
				"open_date"		=>	$dt['date'],
				"updator"		=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
				"last_update"	=>	$dt['date']
			);

	$parm['fab_substu'] = (isset($PHP_fab_substu)) ? $PHP_fab_substu : '';
	$parm['acc_substu'] = (isset($PHP_acc_substu)) ? $PHP_acc_substu : '';
		
		// check ��J��Ƥ����T	------------------------	
			$op['smpl_ord'] = $parm;

	if (!$f1 = $smpl_ord->check($parm)) {  // ��J��� �����T��
		$op['msg'] = $smpl_ord->msg->get(2);

		// �C�X �s�W �q�檺���� ---------------------------------------------
		$op['cust_id'] = $PHP_cust;
		$op['dept_id'] = $PHP_dept;

		$op['msg']= $smpl_ord->msg->get(2);
		
		// creat factory combo box
		$op['factory'] = $arry2->select($SAMPLER,$parm['factory'],'PHP_factory','select','');  	
		// creat style type combo box
		$style_def = $style_type->get_fields('style_type');   // ���X �ڦ����O
		$op['style_type'] =  $arry2->select($style_def,$parm['style_type'],'PHP_style_type','select','');  	
		// creat apparel unit combo box
		$op['unit'] = $arry2->select($APPAREL_UNIT,$parm['unit'],'PHP_unit','select','');  	

		// creat STYLE CAT. combo box
		$op['style_cat'] = $arry2->select($STYLE_CAT,$parm['style_cat'],'PHP_style_cat','select','');  	
		// creat SMPL TYPE combo box
		$smpl_def = $smpl_type->get_fields('smpl_type');   // ���X �ڦ����O
		$op['smpl_type'] =  $arry2->select($smpl_def,$parm['smpl_type'],'PHP_smpl_type','select','');  	

		// ���X�ﶵ��� �ζǤJ���Ѽ�
		$op['dept_id']	 = $PHP_dept;

		$op['order'] = $parm;

		// �q��s��....
		$op['order']['num'] = $PHP_num;

		page_display($op, 2, 1, $TPL_SMPL_ORD_FOLLOW);			
		break;
		
	}   // end check [ ��ƿ�J���T�� ]
		
		//  �s�s �q �� ���X 
		$parm['num'] = $PHP_num;  

		//------------------ add to DB	
		$f1 = $smpl_ord->add($parm);

		if (!$f1) {  // �S�����\�g�J��Ʈw��
				$op['msg'] = $smpl_ord->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
			break;

		}   // end if (!$F1)--------- ���\��J ---------------------------------

		$message = " Append New SAMPLE order: [".$parm['num']."]";
		
		// ----���X�ӵ� order ��� ---------------------------------------------
		if (!$op['order']=$smpl_ord->get($f1)) {
			$op['msg']= $smpl_ord->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		
				// �ˬd �ۤ��O�_�s�b
		if(file_exists($GLOBALS['config']['root_dir']."/smpl_pic/".$op['order']['num'].".jpg")){
			$op['main_pic'] = "./smpl_pic/".$op['order']['num'].".jpg";
		} else {
			$op['main_pic'] = "./images/graydot.gif";
		}
		
			# �O���ϥΪ̰ʺA
		$log->log_add(0,"21A",$message);
		$op['msg'][] = $message;

		// creat LOG SUBJ combo box
		$op['logs_subj'] = $arry2->select($SMPL_LOG_SUBJ,'','PHP_subj','select','');  	
		$op['user_e']=$GLOBALS['SCACHE']['ADMIN']['name'];

		$op['ord_link_non']=1;
		
		$img_size = GetImageSize($op['main_pic']);
		if ($img_size[0] < $img_size[1]) $op['height'] = 1;

		
		page_display($op, 2, 1, $TPL_SMPL_ORD_SHOW);			
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "smpl_ord_edit":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "smpl_ord_edit":

		check_authority(2,1,"edit");

		$parm = $smpl_ord->get($PHP_id);
		if (!$parm) {
			$op['msg'] = $smpl_ord->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		// �C�X �s�W �q�檺���� ---------------------------------------------
		$op['cust_id'] = $PHP_cust;

		$op['msg']= $smpl_ord->msg->get(2);
		
			// �ˬd �ۤ��O�_�s�b
		if(file_exists($GLOBALS['config']['root_dir']."/smpl_pic/".$parm['num'].".jpg")){
			$op['main_pic'] = "./smpl_pic/".$parm['num'].".jpg";
		} else {
			$op['main_pic'] = "./images/graydot.gif";
		}

		
		// creat factory combo box
		$op['factory'] = $arry2->select($SAMPLER,$parm['factory'],'PHP_factory','select','');  	

		// creat style type combo box
		$style_def = $style_type->get_fields('style_type');   // ���X �ڦ����O
		$op['style_type'] =  $arry2->select($style_def,$parm['style'],'PHP_style_type','select','');  	
		// creat apparel unit combo box
		$op['unit'] = $arry2->select($APPAREL_UNIT,$parm['unit'],'PHP_unit','select','');  	

		// creat STYLE CAT. combo box
		$op['style_cat'] = $arry2->select($STYLE_CAT,$parm['style_cat'],'PHP_style_cat','select','');  	

		// creat SMPL TYPE combo box
		$smpl_def = $smpl_type->get_fields('smpl_type');   // ���X �ڦ����O
		$op['smpl_type'] =  $arry2->select($smpl_def,$parm['smpl_type'],'PHP_smpl_type','select','');  	

		// ���X�ﶵ��� �ζǤJ���Ѽ�
		$op['dept_id']	 = $PHP_dept;

		$op['order'] = $parm;

		page_display($op, 2, 1, $TPL_SMPL_ORD_EDIT);			
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "do_smpl_ord_edit":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_smpl_ord_edit":

		check_authority(2,1,"edit");
		
		// ���X�~�X... �H��J�ɤ��~���~�X[���]
		$dt = decode_date(1);
		$year_code = substr($dt['year'],-1);

		$parm = array(	"id"					=>	$PHP_id,
										"num"					=>	$PHP_num,

										"dept"				=>	$PHP_dept,
										"cust"				=>	$PHP_cust,
										"ref"					=>	$PHP_ref,
										"factory"			=>	$PHP_factory,
										"style_type"	=>	$PHP_style_type,
										"qty"					=>	$PHP_rq + $PHP_backup,
										"rq"					=>	$PHP_rq,
										"backup"			=>	$PHP_backup,
										"unit"				=>	$PHP_unit,
										"smpl_type"		=>	$PHP_smpl_type,
										"style_cat"		=>	$PHP_style_cat,

//�s�W���ɤW��						
										"pic"					=>	$PHP_pic,
										"pic_upload"	=>	$PHP_pic_upload,
					
										"pttn_suppt"	=>	$PHP_pttn_suppt,
										"etd"					=>	$PHP_etd,
										"feta"				=>	$PHP_feta,
										"aeta"				=>	$PHP_aeta,
										"peta"				=>	$PHP_peta,
										"updator"			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
										"last_update"	=>	$dt['date']
			);

	$parm['fab_substu'] = (isset($PHP_fab_substu)) ? $PHP_fab_substu : '';
	$parm['acc_substu'] = (isset($PHP_acc_substu)) ? $PHP_acc_substu : '';
		
		// check ��J��Ƥ����T	------------------------	
			$op['smpl_ord'] = $parm;

	if (!$f1 = $smpl_ord->check($parm)) {  // ��J��� �����T��
		$op['msg'] = $smpl_ord->msg->get(2);

		// �C�X �s�W �q�檺���� ---------------------------------------------
		$op['cust_id'] = $PHP_cust;
		$op['dept_id'] = $PHP_dept;

		$op['msg']= $smpl_ord->msg->get(2);
		$op['main_pic']=$PHP_main_pic;
		// creat factory combo box
		$op['factory'] = $arry2->select($SAMPLER,$parm['factory'],'PHP_factory','select','');  	

		// creat style type combo box
		$style_def = $style_type->get_fields('style_type');   // ���X �ڦ����O
		$op['style_type'] =  $arry2->select($style_def,$parm['style_type'],'PHP_style_type','select','');  	

		// creat apparel unit combo box
		$op['unit'] = $arry2->select($APPAREL_UNIT,$parm['unit'],'PHP_unit','select','');  	

		// creat STYLE CAT. combo box
		$op['style_cat'] = $arry2->select($STYLE_CAT,$parm['style_cat'],'PHP_style_cat','select','');  	

		// creat SMPL TYPE combo box
		$smpl_def = $smpl_type->get_fields('smpl_type');   // ���X �ڦ����O
		$op['smpl_type'] =  $arry2->select($smpl_def,$parm['smpl_type'],'PHP_smpl_type','select','');  	

		// ���X�ﶵ��� �ζǤJ���Ѽ�
		$op['dept_id']	 = $PHP_dept;
		// pre  �q��s��....
		$op['order_precode'] = "S".$year_code.$PHP_cust."xxxx-X";

		$op['order'] = $parm;

		page_display($op, 2, 1, $TPL_SMPL_ORD_EDIT);			
		break;
	}
		// ��J�����T��............after check input.........
		// ========= add Eddition to DB =======
		if (!$smpl_ord->edit($parm)) {
			$op['msg'] = $smpl_ord->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;	    
		}

		$op['order'] = $smpl_ord->get($PHP_id);  //���X�ӵ��O��

		if (!$op['order']) {
			$op['msg'] = $smpl_ord->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		// ���X smpl_log ���q�� �����v50�����p�O��  -------------------
		$logs = $smpl_log->search50($op['order']['num']);  //���X�ӭq�����log�O��
		if (!$logs){
			$op['msg'] = $smpl_log->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		
				// �ˬd �ۤ��O�_�s�b
		if(file_exists($GLOBALS['config']['root_dir']."/smpl_pic/".$op['order']['num'].".jpg")){
			$op['main_pic'] = "./smpl_pic/".$op['order']['num'].".jpg";
		} else {
			$op['main_pic'] = "./images/graydot.gif";
		}

		$op['logs'] = $logs['log'];
		$op['log_records'] = $logs['records'];
		// creat LOG SUBJ combo box
		$op['logs_subj'] = $arry2->select($SMPL_LOG_SUBJ,'','PHP_subj','select','');  	
		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
		if (substr($user_dept,0,1)=="H" || substr($user_dept,0,1)=="L")  //�P�_�ϥΪ̳����O�_���u�t
		{                                                                //�H���j�קﵥ�v��
			if ($user_dept == $op['order']['dept'])$op['dept_edit']=1;
			if ($user_dept =="H0" && substr($op['order']['dept'],0,1)=="H")$op['dept_edit']=1;
			if ($user_dept =="L0" && substr($op['order']['dept'],0,1)=="L")$op['dept_edit']=1;			
		}else{
			$op['dept_edit']=1;
		}	
		
		$smpl=substr($op['order']['num'],0,9);
		if (!$op['ord_link']= $order->smpl_search($smpl))
		{
			$op['ord_link_non']=1;
		}
			
		# �O���ϥΪ̰ʺA
		$message = " update sample order:[".$op['order']['num']."]";
		$log->log_add(0,"21E",$message);
		$op['msg'][] = $message;
		$op['user_e']=$GLOBALS['SCACHE']['ADMIN']['name'];

		$img_size = GetImageSize($op['main_pic']);
		if ($img_size[0] < $img_size[1]) $op['height'] = 1;


		page_display($op, 2, 1, $TPL_SMPL_ORD_SHOW);			
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "smpl_ord_search":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "smpl_ord_search":

		check_authority(2,1,"view");

		if (!isset($SCH_wi))		$SCH_wi	='';
		if (!isset($SCH_close))	$SCH_close	='';
		if (!isset($SCH_pend))	$SCH_pend	='';


		$parm = array(	"dept"		=>  $PHP_dept_code,
										"num"			=>  $PHP_num,
										"cust"		=>	$PHP_cust,
										"ref"			=>	$PHP_ref,
										"factory"	=>	$PHP_factory,
										"op_str"	=>	$PHP_op_str,
										"op_fsh"	=>	$PHP_op_fsh,
										'wi'			=>	$SCH_wi,
										'close'		=>	$SCH_close,
										'pend'		=>	$SCH_pend,
												
				);

		// ---- �Q��PHP_dept_code�P�w�O�_ �~�ȳ����i�J
		if (!$op = $smpl_ord->search(1, $PHP_dept_code)) {  
			$op['msg']= $smpl_ord->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
				// �ˬd �ۤ��O�_�s�b 2007.03.23

	for ($i=0; $i < sizeof($op['sorder']); $i++)
	{
		if(file_exists($GLOBALS['config']['root_dir']."/smpl_pic/s_".$op['sorder'][$i]['num'].".jpg")){
			$op['sorder'][$i]['main_pic'] = "./smpl_pic/s_".$op['sorder'][$i]['num'].".jpg";
		} else {
			$op['sorder'][$i]['main_pic'] = "./images/graydot.gif";
		}
		$img_size = GetImageSize($op['sorder'][$i]['main_pic']);
		if ($img_size[0] < $img_size[1]) $op['sorder'][$i]['height'] = 1;

	}
		// ---- �p�G ���O �e��~�ȳ����i�J��...
		if (!$PHP_dept_code) {   
			$op['manager_flag'] = 1;
		}

		$op['dept_id'] = $PHP_dept_code;

		$op['msg']= $smpl_ord->msg->get(2);
		$op['cgi']= $parm;

		page_display($op, 2, 1, $TPL_SMPL_ORD_LIST);			
		break;
		
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "smpl_ord_del": add on 20070319
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "smpl_ord_del":

		check_authority(2,1,"view");

		$parm = array(	"dept"		=>  '',
										"num"			=>  '',
										"cust"		=>	'',
										"ref"			=>	'',
										"factory"	=>	'',
										"op_str"	=>	'',
										"op_fsh"	=>	'',
										'wi'			=>	'',
										'close'		=>	'',
										'pend'		=>	'',
				);
		$f1=$smpl_ord->del($PHP_id);
		// ---- �Q��PHP_dept_code�P�w�O�_ �~�ȳ����i�J
		if (!$op = $smpl_ord->search(0)) {  
			$op['msg']= $smpl_ord->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

	for ($i=0; $i < sizeof($op['sorder']); $i++)
	{
		if(file_exists($GLOBALS['config']['root_dir']."/smpl_pic/s_".$op['sorder'][$i]['num'].".jpg")){
			$op['sorder'][$i]['main_pic'] = "./smpl_pic/s_".$op['sorder'][$i]['num'].".jpg";
		} else {
			$op['sorder'][$i]['main_pic'] = "./images/graydot.gif";
		}
		$img_size = GetImageSize($op['sorder'][$i]['main_pic']);
		if ($img_size[0] < $img_size[1]) $op['sorder'][$i]['height'] = 1;

	}

		$op['msg']= $smpl_ord->msg->get(2);
		$op['cgi']= $parm;

		page_display($op, 2, 1, $TPL_SMPL_ORD_LIST);			
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		job 21V sample oreder submit [add by 2007 03 19]
#		case "smpl_ord_sumb":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "smpl_ord_sumb":

		check_authority(2,1,"view");
		if (!isset($cgi_1))
		{
			$cgi_1=$cgi_2=$cgi_3=$cgi_4=$cgi_5=$cgi_6='';
			$cgi_7=$cgi_8=$cgi_9=$cgi_10='';
			$cgino=$cgiget='';
		}

		$cgi = array(	"dept"		=>  $cgi_1,
									"num"			=>  $cgi_2,
									"cust"		=>	$cgi_3,
									"ref"			=>	$cgi_4,
									"factory"	=>	$cgi_5,
									"no"			=>	$cgino,
									"op_str"	=>	$cgi_6,
									"op_fsh"	=>	$cgi_7,
									"wi"			=>	$cgi_8,
									"close"		=>	$cgi_9,
									"pend"		=>	$cgi_10,									
									"str"			=>	trim($cgiget),
				);
		$f1	=	$smpl_ord->update_status($PHP_id,'0');
		
		
		$up_parm = array (	'field_name'	=>	'submit_date',
												'field_value'	=>	$TODAY,
												'id'					=>	$PHP_id
												);
		$f1 = $smpl_ord->update_field($up_parm);
			
		$op['order'] = $smpl_ord->get($PHP_id);  //���X�ӵ��O��
		if (!$op['order']) {
			$op['msg'] = $order->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		// ���X smpl_log ���q�� �����v50�����p�O��  -------------------
		$logs = $smpl_log->search50($op['order']['num']);  //���X�ӭq�����log�O��
		if (!$logs){
			$op['msg'] = $smpl_log->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$op['logs'] = $logs['log'];
		$op['log_records'] = $logs['records'];
		// �N�|�ǥX op['logs'] ��log����� �� $op['log_records']=1 �ӧP�_���L���
		//--------------------------------
		
		$parm = $op['order'];
		if(file_exists($GLOBALS['config']['root_dir']."/smpl_pic/".$op['order']['num'].".jpg")){
			$op['main_pic'] = "./smpl_pic/".$op['order']['num'].".jpg";
		} else {
			$op['main_pic'] = "./images/graydot.gif";
		}

		// �[�J��^�e�� --------------
		$back_str = trim($cgiget)."&PHP_sr_startno=".$cgino."&PHP_dept_code=".$cgi_1."&PHP_num=".$cgi_2."&PHP_cust=".$cgi_3."&PHP_ref=".$cgi_4."&PHP_factory=".$cgi_5."&PHP_op_str=".$cgi_6."&PHP_op_fsh=".$cgi_7."&SCH_wi=".$cgi_8."&SCH_close=".$cgi_9."&SCH_pend=".$cgi_10;

		$back_str2 = "PHP_action=smpl_ord_view&PHP_id=".$PHP_id."&PHP_num=".$parm['num'];

		$op['back_str2'] = $back_str2;
		$op['back_str'] = $back_str;
		$op['cgi'] = $cgi;
		// creat LOG SUBJ combo box
		$op['logs_subj'] = $arry2->select($SMPL_LOG_SUBJ,'','PHP_subj','select','');  	

		$op['msg'] = $smpl_ord->msg->get(2);
		$op['search'] = '1';
		$op['user_e']=$GLOBALS['SCACHE']['ADMIN']['name'];
		
		$smpl=substr($op['order']['num'],0,9);
		if (!$op['ord_link']= $order->smpl_search($smpl))
		{
			$op['ord_link_non']=1;
		}
		$img_size = GetImageSize($op['main_pic']);
		if ($img_size[0] < $img_size[1]) $op['height'] = 1;

		page_display($op, 2, 1, $TPL_SMPL_ORD_SHOW);			
		break;


	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		job 21V sample oreder view
#		case "smpl_ord_view":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "smpl_ord_view":

		check_authority(2,1,"view");

		$cgi = array(	"dept"		=>  $cgi_1,
									"num"			=>  $cgi_2,
									"cust"		=>	$cgi_3,
									"ref"			=>	$cgi_4,
									"factory"	=>	$cgi_5,
									"no"			=>	$cgino,
									"op_str"	=>	$cgi_6,
									"op_fsh"	=>	$cgi_7,
									"wi"			=>	$cgi_8,
									"close"		=>	$cgi_9,
									"pend"		=>	$cgi_10,									
									"str"			=>	trim($cgiget),
				);
		$op['order'] = $smpl_ord->get($PHP_id);  //���X�ӵ��O��
		if (!$op['order']) {
			$op['msg'] = $order->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		// �ˬd �ۤ��O�_�s�b
		if(file_exists($GLOBALS['config']['root_dir']."/smpl_pic/".$op['order']['num'].".jpg")){
			$op['main_pic'] = "./smpl_pic/".$op['order']['num'].".jpg";
		} else {
			$op['main_pic'] = "./images/graydot.gif";
		}
		// ���X smpl_log ���q�� �����v50�����p�O��  -------------------
		$logs = $smpl_log->search50($op['order']['num']);  //���X�ӭq�����log�O��
		if (!$logs){
			$op['msg'] = $smpl_log->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$smpl=substr($op['order']['num'],0,9);
		if (!$op['ord_link']= $order->smpl_search($smpl))
		{
			$op['ord_link_non']=1;
		}
		
		$op['logs'] = $logs['log'];
		$op['log_records'] = $logs['records'];
		// �N�|�ǥX op['logs'] ��log����� �� $op['log_records']=1 �ӧP�_���L���
		//--------------------------------
		
		$parm = $op['order'];
		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
		
		if (substr($user_dept,0,1)=="H" || substr($user_dept,0,1)=="L")  //�P�_�ϥΪ̳����O�_���u�t
		{                                                                //�H���j�קﵥ�v��
			if ($user_dept == $op['order']['dept'])$op['dept_edit']=1;
			if ($user_dept =="H0" && substr($op['order']['dept'],0,1)=="H")$op['dept_edit']=1;
			if ($user_dept =="L0" && substr($op['order']['dept'],0,1)=="L")$op['dept_edit']=1;			
		}else{
			$op['dept_edit']=1;
		}

		// �[�J��^�e�� --------------
		$back_str = trim($cgiget)."&PHP_sr_startno=".$cgino."&PHP_dept_code=".$cgi_1."&PHP_num=".$cgi_2."&PHP_cust=".$cgi_3."&PHP_ref=".$cgi_4."&PHP_factory=".$cgi_5."&PHP_op_str=".$cgi_6."&PHP_op_fsh=".$cgi_7."&SCH_wi=".$cgi_8."&SCH_close=".$cgi_9."&SCH_pend=".$cgi_10;

		$back_str2 = "PHP_action=smpl_ord_view&PHP_id=".$PHP_id."&PHP_num=".$parm['num'];
		$img_size = GetImageSize($op['main_pic']);
		if ($img_size[0] < $img_size[1]) $op['height'] = 1;
		
		$op['back_str2'] = $back_str2;
		$op['back_str'] = $back_str;
		$op['cgi'] = $cgi;
		// creat LOG SUBJ combo box
		$op['logs_subj'] = $arry2->select($SMPL_LOG_SUBJ,'','PHP_subj','select','');  	

		$op['msg'] = $smpl_ord->msg->get(2);
		$op['search'] = '1';
		$op['user_e']=$GLOBALS['SCACHE']['ADMIN']['name'];
		
		page_display($op, 2, 1, $TPL_SMPL_ORD_SHOW);			
		break;



//-------------------------------------------------------------------------------------
//			 job 101X    �q�� �O���ন excel ��
//-------------------------------------------------------------------------------------
	case "smpl_execk":

		if(!$admin->is_power(2,1,"view") && !$admin->is_power(2,1,"view")){  // �[�J���ͥ��]���excel�� 2006/0106
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$parm = array(	"dept"		=>  $PHP_dept_code,
										"num"			=>  $PHP_num,
										"cust"		=>	$PHP_cust,
										"ref"			=>	$PHP_ref,
										"factory"	=>	$PHP_factory,
										"op_str"	=>	$PHP_op_str,
										"op_fsh"	=>	$PHP_op_fsh,									
										'wi'			=>	$SCH_wi,
										'close'		=>	$SCH_close,
										'pend'		=>	$SCH_pend,
				);

		// ---- �Q��PHP_dept_code�P�w�O�_ �~�ȳ����i�J
		if (!$ord = $smpl_ord->search(1, $PHP_dept_code,1000)) {  
			$op['msg']= $smpl_ord->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$a = $ord['max_no'];

	

	
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
	  HeaderingExcel('smpl_order.xls');
	 
	  // Creating a workbook
	  $workbook = new Workbook("-");

	  // Creating the first worksheet
	  $worksheet1 =& $workbook->add_worksheet('order list');

		$now = $GLOBALS['THIS_TIME'];

	// �g�J title

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
	  $ary = array(12,5,5,8,8,8,14,14,5,3,15,20,12,15,15,20,15,15,3,14,14,14,14,3,12,12,12,12,20);
	  for ($i=0; $i < 29; $i++)	$worksheet1->set_column(0,$i,$ary[$i]);

	  $worksheet1->write_string(0,1,"Sample Order List");
	  $worksheet1->write(0,3,$now);
	  $ary = array("Smpl order#","cust","dept.","smpl type","style #","style cat.","ETD","style type","IE","","cust. pattern ETA","cust. pattern receive",
	  				"W/I ETA","W/I receive","Factory ETA","Factory receive","Acc. ETA","Acc. received","unit","reuire q'ty","backup q'ty","Q'ty","finish Q'ty",
	  				"","Pattern-ETF","Pattern-done","Sample-ETF","Sample-done","status");
	  for ($i=0; $i < 29; $i++)	 $worksheet1->write_string(1,$i,$ary[$i],$formatot);

	  // Format for the numbers
	  $formatnum =& $workbook->add_format();
	  $formatnum->set_size(10);
	  $formatnum->set_align('center');
	  $formatnum->set_color('black');
	  $formatnum->set_fg_color('B8D7D8');

	for ($i=0;$i < sizeof($ord['sorder']);$i++){

			if ($ord['sorder'][$i]['status'] == 0) { $status_des ="W/I expect.";
			}elseif($ord['sorder'][$i]['status'] == '1') { $status_des ="PTTN. expect";
			}elseif($ord['sorder'][$i]['status'] == '2') { $status_des ="FAB. expect";
			}elseif($ord['sorder'][$i]['status'] == '3') { $status_des ="ACC. expect";
			}elseif($ord['sorder'][$i]['status'] == '4') { $status_des ="PTTN schdle";
			}elseif($ord['sorder'][$i]['status'] == '5') { $status_des ="SMPL schdle";
			}elseif($ord['sorder'][$i]['status'] == '6') { $status_des ="DEVELOPING";
			}elseif($ord['sorder'][$i]['status'] == '7') { $status_des ="* PENDING *";
			}elseif($ord['sorder'][$i]['status'] == '8') { $status_des ="= case close =";
			}else{ $status_des ='';}

	if ($ord['sorder'][$i]['pttn_suppt'])
	{
		if ($ord['sorder'][$i]['peta']=='0000-00-00')$ord['sorder'][$i]['peta']='N/A';
		if ($ord['sorder'][$i]['rcvd_pttn']=='0000-00-00')$ord['sorder'][$i]['peta']='N/A';

	}else{
		$ord['sorder'][$i]['peta']="not support";
		$ord['sorder'][$i]['rcvd_pttn']="no need";
	}
	
	if ($ord['sorder'][$i]['fab_substu'])
	{

		$ord['sorder'][$i]['feta']="substitude";
		$ord['sorder'][$i]['rcvd_fab']="no need";

	}else{
		if ($ord['sorder'][$i]['feta']=='0000-00-00')$ord['sorder'][$i]['peta']='N/A';		
		if ($ord['sorder'][$i]['rcvd_fab']=='0000-00-00')$ord['sorder'][$i]['peta']='N/A';
	}

	if ($ord['sorder'][$i]['wi_date']=='0000-00-00')$ord['sorder'][$i]['wi_date']='N/A';	

	if ($ord['sorder'][$i]['acc_substu'])
	{

		$ord['sorder'][$i]['aeta']="substitude";
		$ord['sorder'][$i]['rcvd_acc']="no need";

	}else{
		if ($ord['sorder'][$i]['aeta']=='0000-00-00')$ord['sorder'][$i]['aeta']='N/A';		
		if ($ord['sorder'][$i]['rcvd_acc']=='0000-00-00')$ord['sorder'][$i]['peta']='N/A';
	}

	if ($ord['sorder'][$i]['schd_pttn']=='0000-00-00')$ord['sorder'][$i]['schd_pttn']='N/A';

	if ($ord['sorder'][$i]['done_pttn']=='0000-00-00')$ord['sorder'][$i]['done_pttn']='N/A';	
	
	if ($ord['sorder'][$i]['schd_smpl']=='0000-00-00')$ord['sorder'][$i]['schd_smpl']='N/A';	
	
	if ($ord['sorder'][$i]['done_smpl']=='0000-00-00')$ord['sorder'][$i]['done_smpl']='N/A';	
	
	  $worksheet1->write($i+2,0,$ord['sorder'][$i]['num'],$formatnum);
	  $worksheet1->write($i+2,1,$ord['sorder'][$i]['cust'],$f2);
	  $worksheet1->write($i+2,2,$ord['sorder'][$i]['dept'],$f2);
	  $worksheet1->write($i+2,3,$ord['sorder'][$i]['smpl_type']);
	  $worksheet1->write($i+2,4,$ord['sorder'][$i]['ref']);
	  $worksheet1->write($i+2,5,$ord['sorder'][$i]['style_cat']);
	  $worksheet1->write($i+2,6,$ord['sorder'][$i]['etd']);
	  $worksheet1->write($i+2,7,$ord['sorder'][$i]['style']);
	  $worksheet1->write($i+2,8,$ord['sorder'][$i]['ie']);
	  $worksheet1->write($i+2,9,'',$formatnum);
	  
	  $worksheet1->write($i+2,10,$ord['sorder'][$i]['peta'],$f2);
	  $worksheet1->write($i+2,11,$ord['sorder'][$i]['rcvd_pttn']);
	  $worksheet1->write($i+2,12,$ord['sorder'][$i]['feta'],$f2);
	  $worksheet1->write($i+2,13,$ord['sorder'][$i]['rcvd_fab']);
	  $worksheet1->write($i+2,14,$ord['sorder'][$i]['wi_date'],$f2);
	  $worksheet1->write($i+2,15,'maker:'.$ord['sorder'][$i]['factory']);
	  $worksheet1->write($i+2,16,$ord['sorder'][$i]['aeta'],$f2);

	  $worksheet1->write($i+2,17,$ord['sorder'][$i]['rcvd_acc']);
	  $worksheet1->write($i+2,18,$ord['sorder'][$i]['unit'],$f2);
	  $worksheet1->write($i+2,19,$ord['sorder'][$i]['rq']);
	  $worksheet1->write($i+2,20,$ord['sorder'][$i]['backup']);
	  $worksheet1->write($i+2,21,$ord['sorder'][$i]['qty']);
	  $worksheet1->write($i+2,22,$ord['sorder'][$i]['qty_done']);
	   $worksheet1->write($i+2,23,'',$formatnum);
	  $worksheet1->write($i+2,24,$ord['sorder'][$i]['schd_pttn']);
	  $worksheet1->write($i+2,25,$ord['sorder'][$i]['done_pttn']);
	  $worksheet1->write($i+2,26,$ord['sorder'][$i]['schd_smpl']);
	  $worksheet1->write($i+2,27,$ord['sorder'][$i]['done_smpl']);
	  $worksheet1->write($i+2,28,$status_des,$f2);
	
	}


  $workbook->close();


	break;






#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "do_smpl_logs_add":       �˥� logs �g�J  (����)
#					�䤤�� PENDING �M REDOING ��ӯS��ﶵ
#					�ұ��v�� 24E ���v���~��ϥ� ��̷|���� SMPL ORDER �� status
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_smpl_logs_add":

		if(!$admin->is_power(2,1,"add") && !$admin->is_power(2,7,"add") && !$admin->is_power(2,8,"add") &&  !$admin->is_power(2,9,"add")){ 
			$op['msg'][] = "sorry! you don't have this Authority !";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}


		$err_msg ='';
if(trim($PHP_subj) && trim($PHP_des)){  // subject & des ���ݦ����

		// �NPENDING �]�w���u�� 24E ���M���v��
		if($PHP_subj == 'PENDING' || $PHP_subj == 'REDOING'){   
		  if(!$admin->is_power(2,9,"edit")){
			$op['msg'][] = "sorry! you don't have this Authority !";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		  }
		}
		
		$parm = array(	"subj"		=>	$PHP_subj,
						"user"		=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
						"num"		=>	$PHP_num,
						"des"		=>	$PHP_des,
						"k_time"	=>	$TODAY,
				);

		//��subject �OPENDING�� ���S���J ---
		if($PHP_subj == 'PENDING'){   // �NPENDING �]�w���u�� 24E ���M���v��
			$parm['subj'] = '<span class=bgdy9>'.$PHP_subj.'</span>';
			$parm['des'] = '<span class=bgdy9>'.$PHP_des.'</span>';

			if(!$V = $smpl_ord->get_fieldvalue($PHP_id, 'status')){ //���Xstatus
				$op['msg']= $smpl_ord->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			
			if ($V['status'] == '7'){
				$op['msg'][] = "sorry! this order is under status <b>PENDING</b> already !";
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}else{   // �N��Ӫ�status �s�J remark
				$argu['id']			 = $PHP_id;
				$argu['field_name']  = 'remark';
				$argu['field_value'] = $V['status'];
				if(!$F = $smpl_ord->update_field($argu)){ //-�s�Jremark
					$op['msg']= $smpl_ord->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
				}
			
				// ��� smpl order �� status
				if(!$S = $smpl_ord->update_status($PHP_id, 7)){ 
					$op['msg']= $smpl_ord->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
				}
			}
		}elseif($PHP_subj == 'REDOING'){
			//---���Xstatus
			if(!$v = $smpl_ord->get_fieldvalue($PHP_id, 'status')){ 
				$op['msg']= $smpl_ord->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

			if($v['status'] == "7"){  // �T�w�{�b�q��O pending ��
				$parm['subj'] = '<span class=grn9>'.$PHP_subj.'</span>';
				$parm['des'] = '<span class=grn9>'.$PHP_des.'</span>';

				// ��� smpl order �� status [--��remark��^��]

				if(!$R = $smpl_ord->get_fieldvalue($PHP_id, 'remark')){ //���X remark
					$op['msg']= $smpl_ord->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
				}

				if(!$F = $smpl_ord->update_status($PHP_id, $R['remark'])){ //-�s�Jstatus
					$op['msg']= $smpl_ord->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
				}

			}else{
				$op['msg'][] = "sorry! you cannot select <b>REDOING</b> as order is not <b>PENDING</b> !";
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
		}

		// -- �g�J log table
		if(!$f1 = $smpl_log->add($parm)){
			$op['msg']= $smpl_log->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

}else{   //----if (trim($PHP_subj) && trim($PHP_des)) �S��subj�� �g�Jdes��

		$op['msg'][] =  "&nbsp;SORRY !! <b>Subject</b> selected and logs <b>Describe</b> are required!";
		$layout->assign($op);
		$layout->display($TPL_ERROR);  		    
		break;

}
		// �e�X�Ѽ� ----- redir page
		if (!isset($SCH_wi))		$SCH_wi	='';
		if (!isset($SCH_close))	$SCH_close	='';
		if (!isset($SCH_pend))	$SCH_pend	='';
		if (!isset($SCH_pttnd))	$SCH_pttnd	='';
		if (!isset($SCH_fab))		$SCH_fab	='';
		if (!isset($SCH_acc))		$SCH_acc	='';
		if (!isset($SCH_ptno))	$SCH_ptno	='';
		if (!isset($SCH_ptdu))	$SCH_ptdu	='';
		if (!isset($SCH_usmp))	$SCH_usmp	='';
		if (!isset($SCH_smpdu))	$SCH_smpdu	='';
		if (!isset($SCH_qty))		$SCH_qty	='';
		if (!isset($SCH_ie))		$SCH_ie	='';
		if (!isset($SCH_mk))		$SCH_mk	='';
		if (isset($cgi_8))			$SCH_wi	= $cgi_8;
		if (isset($cgi_9))			$SCH_close = $cgi_9;
		if (isset($cgi_10))			$SCH_pend = $cgi_10;

		$cgi = '&cgino='.$cgino.'&cgi_1='.$cgi_1.'&cgi_2='.$cgi_2.'&cgi_3='.$cgi_3.'&cgi_4='.$cgi_4.'&cgi_5='.$cgi_5."&PHP_op_str=".$cgi_6."&cgi_6=".$cgi_6."&PHP_op_fsh=".$cgi_7."&cgi_7".$cgi_7."&cgi_8=".$SCH_wi."&cgi_9=".$SCH_close."&cgi_10=".$SCH_pend."&SCH_ptno=".$SCH_ptno."&SCH_ptdu=".$SCH_ptdu."&SCH_usmp=".$SCH_usmp."&SCH_smpdu=".$SCH_smpdu."&SCH_qty=".$SCH_qty."&SCH_ie=".$SCH_ie."&SCH_mk=".$SCH_ie."&SCH_wi=".$SCH_wi."&SCH_close=".$SCH_close."&SCH_pend=".$SCH_pend."&SCH_pttnd=".$SCH_pttnd."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc;
		
		$redir_str = $PHP_SELF.'?PHP_action='.$back_action.'&PHP_id='.$PHP_id.'&cgiget='.$cgiget.$cgi;
		redirect_page($redir_str);
	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "smpl_supplier":	job 22
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "smpl_supplier":

		check_authority(2,7,"view");

		//---------------------------- �n�`�N�令  R&D �i adding--------
		$where_str = $manager = $dept_id = '';
		$dept_ary = array();
		$sales_dept_ary = get_sales_dept(); // ���X �~�Ȫ����� [���tK0] ----

		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];  
		$user_team = $GLOBALS['SCACHE']['ADMIN']['team_id'];

		for ($i=0; $i<count($sales_dept_ary);$i++){
			if($user_dept == $sales_dept_ary[$i]){

				// �p�G�O�~�ȳ��i�J �hdept_code ���w�ӷ~�ȳ�---
				$dept_id = $sales_dept_ary[$i];  
			}
		}

		if (!$dept_id) {    // ���O �~�ȳ��H[�]���t K0 ���H ]�i�J��
			$manager = 1;
			//�~�ȳ��� select���
			$dept_ary = $arry2->select($sales_dept_ary,"","PHP_dept_code","select","");  
		} else {
			$where_str = " WHERE dept = '".$dept_id."' ";
		}
		$team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
		if(($user_dept == 'HJ' ||$user_dept == 'LY' ) && $team<>'MD')
		{
			$manager = 1;
			$manager_v = 1;
			//�~�ȳ��� select���
			$dept_ary = $arry2->select($sales_dept_ary,"","PHP_dept_code","select","");  
			$where_str ='';

		}
		$op['manager_flag'] = $manager;
		$op['dept_id'] = $dept_id;
		$op['dept_ary'] = $dept_ary;

		$op['msg'] = $order->msg->get(2);

		// creat cust combo box
		// ���X �Ȥ�N��
		$where_str=$where_str."order by cust_s_name"; //��cust_s_name�Ƨ�
		if(!$cust_def = $cust->get_fields('cust_init_name',$where_str)){;  //���X�Ȥ�²��
			$op['msg'][] = "sorry! there is no any customer record in your team, please add customer first!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//���X�Ȥ�N��
		for ($i=0; $i< sizeof($cust_def); $i++)
		{
			$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//�H [�Ȥ�N��-�Ȥ�²��] �e�{
		}
		$op['cust_select'] =  $arry2->select($cust_value,'','PHP_cust','select','',$cust_def_vue); 

		// -------- �M�w factory ���Ѽ� = �}�C? �ƭ�?  RD�H���i�J�ɦ��t�O ----
		if($S = $smpl_ord->is_sample_team($user_team)){
			$op['factory'] = $user_team."<input type='hidden' name='PHP_factory' value='".$user_team."'>";
		}else{
			$op['factory'] = $arry2->select($SAMPLER,'','PHP_factory','select','');  	
		}
		if($user_team=="RD"){
				$op['factory'] = $user_dept."<input type='hidden' name='PHP_factory' value='".$user_dept."'>";
		}
		// --------------------------------------------------------------------
		page_display($op, 2, 7, $TPL_SMPL_SUPPLIER);			
		break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "smpl_supplier_search":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "smpl_supplier_search":

		check_authority(2,7,"view");

		if (!isset($SCH_wi))		$SCH_wi	='';
		if (!isset($SCH_close))	$SCH_close	='';
		if (!isset($SCH_pend))	$SCH_pend	='';
		if (!isset($SCH_pttnd))	$SCH_pttnd	='';
		if (!isset($SCH_fab))		$SCH_fab	='';
		if (!isset($SCH_acc))		$SCH_acc	='';
		if (!isset($SCH_ptno))	$SCH_ptno	='';
		if (!isset($SCH_ptdu))	$SCH_ptdu	='';
		if (!isset($SCH_usmp))	$SCH_usmp	='';
		if (!isset($SCH_smpdu))	$SCH_smpdu	='';
		if (!isset($SCH_qty))		$SCH_qty	='';
		if (!isset($SCH_ie))		$SCH_ie	='';
		if (!isset($SCH_mk))		$SCH_mk	='';
//�s�Wsearch����
		$parm = array(	"dept"			=>  $PHP_dept_code,
										"num"				=>  $PHP_num,
										"cust"			=>	$PHP_cust,
										"ref"				=>	$PHP_ref,
										"factory"		=>	$PHP_factory,
										'wi'				=>	$SCH_wi,
										'close'			=>	$SCH_close,
										'pend'			=>	$SCH_pend,
										'pttnd'			=>	$SCH_pttnd,
										'fab'				=>	$SCH_fab,
										'acc'				=>	$SCH_acc,
										'ptno'			=>	$SCH_ptno,
										'ptdu'			=>	$SCH_ptdu,
										'usmp'			=>	$SCH_usmp,
										'smpdu'			=>	$SCH_smpdu,
										'qty'				=>	$SCH_qty,
										'ie'				=>	$SCH_ie,
										'mk'				=>	$SCH_mk,
										"op_str"		=>	$PHP_op_str,
										"op_fsh"		=>	$PHP_op_fsh,
				);

		$login_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];

		// search �q�欰 supplier �e�{������ [ status < 4 ]
		//	status : 0 : W/I wait		status : 1 : PTTN wait
		//	status : 2 : FAB wait		status : 3 : ACC wait
		//	status : 4 : PTTN schdl		status : 5 : SMPL schdl
		//	status : 6 : PENDING
		//  status : 10: case close

		// �d�M status  <  5 
		if (!$op = $smpl_ord->supplier_search(1, $PHP_dept_code)) {  
			$op['msg']= $smpl_ord->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		
// �ˬd �ۤ��O�_�s�b 2007.03.23		
	for ($i=0; $i < sizeof($op['sorder']); $i++)
	{
		if(file_exists($GLOBALS['config']['root_dir']."/smpl_pic/s_".$op['sorder'][$i]['num'].".jpg")){
			$op['sorder'][$i]['main_pic'] = "./smpl_pic/s_".$op['sorder'][$i]['num'].".jpg";
		} else {
			$op['sorder'][$i]['main_pic'] = "./images/graydot.gif";
		}
		$op['sorder'][$i]['wi_cfm'] = substr($op['sorder'][$i]['wi_cfm'],0,10);
		$img_size = GetImageSize($op['sorder'][$i]['main_pic']);
		if ($img_size[0] < $img_size[1]) $op['sorder'][$i]['height'] = 1;
	}		
		

		// -- �i�Q�� login_dept �P�w�O�_ �� R&D �Χ󰪺޲z�H���i�J ?
		// -- �i�_�i EDIT , ADDING..... (�\�� switch...)
		if ($login_dept == "SA" || $login_dept == "GM" || $login_dept == "RD"){
			$op['manager_flag'] = 1;
		}
		$op['dept_id'] = $PHP_dept_code;
		$op['today'] = $GLOBALS['TODAY'];  // �ά�html�P�_

		$op['msg']= $smpl_ord->msg->get(2);
		$op['cgi']= $parm;
		$op['sub_back_str'] = "&SCH_ptno=".$SCH_ptno."&SCH_ptdu=".$SCH_ptdu."&SCH_usmp=".$SCH_usmp."&SCH_smpdu=".$SCH_smpdu."&SCH_qty=".$SCH_qty."&SCH_ie=".$SCH_ie."&SCH_mk=".$SCH_ie."&SCH_wi=".$SCH_wi."&SCH_close=".$SCH_close."&SCH_pend=".$SCH_pend."&SCH_pttnd=".$SCH_pttnd."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_op_str=".$PHP_op_str."&PHP_op_fsh=".$PHP_op_fsh;
		page_display($op, 2, 7, $TPL_SMPL_SUPPLIER_LIST);			
		break;

	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		job 22V sample SUPPLIER view
#		case "smpl_supplier_view":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "smpl_supplier_view":

		check_authority(2,7,"view");

		//----- ���X�ӵ��O��
		$op['order'] = $smpl_ord->get($PHP_id);  
		if (!$op['order']) {
			$op['msg'] = $smpl_ord->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		// �ˬd �ۤ��O�_�s�b
		if(file_exists($GLOBALS['config']['root_dir']."/smpl_pic/".$op['order']['num'].".jpg")){
			$op['main_pic'] = "./smpl_pic/".$op['order']['num'].".jpg";
		} else {
			$op['main_pic'] = "./images/graydot.gif";
		}
		//----- ���X smpl_log ���q�� �����v50�����p�O��  -------------------
		$logs = $smpl_log->search50($op['order']['num']); 
		if (!$logs){
			$op['msg'] = $smpl_log->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$op['logs'] = $logs['log'];
		$op['log_records'] = $logs['records'];
	
		// �N�|�ǥX op['logs'] ��log����� �� $op['log_records']=1 �ӧP�_���L���
		//--------------------------------
		$parm = $op['order'];

		// �[�J��^�e�� --------------
		$back_str = $cgiget."&PHP_sr_startno=".$cgino."&PHP_dept_code=".$cgi_1."&PHP_num=".$cgi_2."&PHP_cust=".$cgi_3."&PHP_ref=".$cgi_4."&PHP_factory=".$cgi_5."&SCH_ptno=".$SCH_ptno."&SCH_ptdu=".$SCH_ptdu."&SCH_usmp=".$SCH_usmp."&SCH_smpdu=".$SCH_smpdu."&SCH_qty=".$SCH_qty."&SCH_ie=".$SCH_ie."&SCH_mk=".$SCH_ie."&SCH_wi=".$SCH_wi."&SCH_close=".$SCH_close."&SCH_pend=".$SCH_pend."&SCH_pttnd=".$SCH_pttnd."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_op_str=".$PHP_op_str."&PHP_op_fsh=".$PHP_op_fsh;
		
		$back_str2 = "PHP_action=smpl_ord_view&PHP_id=".$PHP_id."&PHP_num=".$parm['num'];
		// creat LOG SUBJ combo box
		$op['logs_subj'] = $arry2->select($SMPL_LOG_SUBJ,'','PHP_subj','select','');  	

		$op['back_str2'] = $back_str2;
		$op['back_str'] = $back_str;
		$op['search'] = '1';

		$img_size = GetImageSize($op['main_pic']);
		if ($img_size[0] < $img_size[1]) $op['height'] = 1;

		$op['msg'] = $smpl_ord->msg->get(2);

		page_display($op, 2, 7, $TPL_SMPL_SUPPLIER_SHOW);			
		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "smpl_wi_rcvd":
#		job 22A					�O�� smpl_ord->status, smpl_ord->wi_date
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	case "smpl_wi_rcvd":

		check_authority(2,7,"add");

		$today = $GLOBALS['TODAY'];
		$parm = array(	"pd_id"		=>  $PHP_id,
						"ord_num"	=>  $PHP_num,
		);
##--*****�s�[���X start
		$redir_str = "index2.php?PHP_action=smpl_supplier_search&PHP_sr_startno=".$PHP_sr_startno."&PHP_dept_code=".$PHP_dept."&PHP_num=".$PHP_nums."&PHP_cust=".$PHP_cust."&PHP_ref=".$PHP_ref."&PHP_factory=".$PHP_factory."&SCH_ptno=".$SCH_ptno."&SCH_ptdu=".$SCH_ptdu."&SCH_usmp=".$SCH_usmp."&SCH_smpdu=".$SCH_smpdu."&SCH_qty=".$SCH_qty."&SCH_ie=".$SCH_ie."&SCH_mk=".$SCH_ie."&SCH_wi=".$SCH_wi."&SCH_close=".$SCH_close."&SCH_pend=".$SCH_pend."&SCH_pttnd=".$SCH_pttnd."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_op_str=".$PHP_op_str."&PHP_op_fsh=".$PHP_op_fsh;;
##--*****�s�[���X end
	// ���g�J  smpl_ord ���� wi_date  ---------------
			$argu['field_name'] = 'wi_date';
			$argu['field_value'] = $today;
			$argu['id'] = $PHP_id;
	if(!$ord = $smpl_ord->update_field($argu)){
		$op['msg'][] = $smpl_ord->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
	} 

	// --- �P�_�p���� status   ######  ---------------
	$smpl = $smpl_ord->get($PHP_id);  //���X�ӵ��O��
	if (!$smpl) {
		$op['msg'] = $smpl_ord->msg->get(2);
		$layout->assign($op);
		$layout->display($TPL_ERROR);  		    
		break;
	}

		if ($smpl['pttn_suppt'] && $smpl['rcvd_pttn'] == '0000-00-00'){
			$new_status = 1;
		}elseif(!$smpl['fab_substu'] && $smpl['rcvd_fab'] == '0000-00-00'){
			$new_status = 2;
		}elseif(!$smpl['acc_substu'] && $smpl['rcvd_acc'] == '0000-00-00'){
			$new_status = 3;
		}else{
			$new_status = 4;
		}

	// ---- �g�J  smpl_ord ���� status  ---------------
			$argu['field_name'] = 'status';
			$argu['field_value'] = $new_status;
			$argu['id'] = $PHP_id;
	if(!$ord = $smpl_ord->update_field($argu)){
		$op['msg'][] = $smpl_ord->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
	} 
		# �O���ϥΪ̰ʺA
		$message = "CFM received W/I of [".$PHP_num."] on:".$today;
		$log->log_add(0,"27A",$message);

	redirect_page($redir_str);
	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "smpl_wi_back":	�٥h ��� W/I ����O�� (�u����ѥi��)
#		job 22x					�O�� smpl_ord->status, smpl_ord->wi_date
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	case "smpl_wi_back":

		check_authority(2,7,"add");

		$today = $GLOBALS['TODAY'];
		$parm = array(	"pd_id"		=>  $PHP_id,
						"ord_num"	=>  $PHP_num,
		);
##--*****�s�[���X start
		$redir_str = "index2.php?PHP_action=smpl_supplier_search&PHP_sr_startno=".$PHP_sr_startno."&PHP_dept_code=".$PHP_dept."&PHP_num=".$PHP_nums."&PHP_cust=".$PHP_cust."&PHP_ref=".$PHP_ref."&PHP_factory=".$PHP_factory;
##--*****�s�[���X end
		// ���g�J  smpl_ord ���� wi_date  ---------------
			$argu['field_name'] = 'wi_date';
			$argu['field_value'] = '0000-00-00';
			$argu['id'] = $PHP_id;
		if(!$ord = $smpl_ord->update_field($argu)){
			$op['msg'][] = $smpl_ord->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
		} 

	// ��� status   ######  -----------------
	// �g�J  smpl_ord ���� status  ---------------
			$argu['field_name'] = 'status';
			$argu['field_value'] = 0;
			$argu['id'] = $PHP_id;
		if(!$ord = $smpl_ord->update_field($argu)){
			$op['msg'][] = $smpl_ord->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
		} 
				# �O���ϥΪ̰ʺA
		$message = "DELETE received W/I of [".$PHP_num."] on:".$today;
		$log->log_add(0,"27A",$message);

		redirect_page($redir_str);
		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "smpl_pttn_rcvd":
#		job 22x					�O�� smpl_ord->status, smpl_ord->rcvd_pttn
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	case "smpl_pttn_rcvd":

		check_authority(2,7,"add");

		$today = $GLOBALS['TODAY'];
		$parm = array(	"pd_id"		=>  $PHP_id,
						"ord_num"	=>  $PHP_num,
		);
##--*****�s�[���X start
		$redir_str = "index2.php?PHP_action=smpl_supplier_search&PHP_sr_startno=".$PHP_sr_startno."&PHP_dept_code=".$PHP_dept."&PHP_num=".$PHP_nums."&PHP_cust=".$PHP_cust."&PHP_ref=".$PHP_ref."&PHP_factory=".$PHP_factory."&SCH_ptno=".$SCH_ptno."&SCH_ptdu=".$SCH_ptdu."&SCH_usmp=".$SCH_usmp."&SCH_smpdu=".$SCH_smpdu."&SCH_qty=".$SCH_qty."&SCH_ie=".$SCH_ie."&SCH_mk=".$SCH_ie."&SCH_wi=".$SCH_wi."&SCH_close=".$SCH_close."&SCH_pend=".$SCH_pend."&SCH_pttnd=".$SCH_pttnd."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_op_str=".$PHP_op_str."&PHP_op_fsh=".$PHP_op_fsh;
##--*****�s�[���X end
	// ���g�J  smpl_ord ���� rcvd_pttn  ---------------
			$argu['field_name'] = 'rcvd_pttn';
			$argu['field_value'] = $today;
			$argu['id'] = $PHP_id;
		if(!$ord = $smpl_ord->update_field($argu)){
			$op['msg'][] = $smpl_ord->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
		} 

	// -- �P�_�p���� status   -----------------
		$smpl = $smpl_ord->get($PHP_id);  //���X�ӵ��O��
		if (!$smpl) {
			$op['msg'] = $smpl_ord->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		if ($smpl['wi_date'] == '0000-00-00'){
			$new_status = 0;
		}elseif(!$smpl['fab_substu'] && $smpl['rcvd_fab'] == '0000-00-00'){
			$new_status = 2;
		}elseif(!$smpl['acc_substu'] && $smpl['rcvd_acc'] == '0000-00-00'){
			$new_status = 3;
		}else{
			$new_status = 4;
		}

	// �g�J  smpl_ord�� status  ---------------
			$argu['field_name'] = 'status';
			$argu['field_value'] = $new_status;
			$argu['id'] = $PHP_id;
		if(!$ord = $smpl_ord->update_field($argu)){
			$op['msg'][] = $smpl_ord->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
		} 
		# �O���ϥΪ̰ʺA
		$message = " CFM received Pattern of [".$PHP_num."] on:".$today;
		$log->log_add(0,"27A",$message);
		redirect_page($redir_str);
		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "smpl_pttn_back":	�٥h ��� pattern ���쪺�O�� (�u����ѥi��)
#		job 22x					�O�� smpl_ord->status, smpl_ord->rcvd_pttn
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	case "smpl_pttn_back":

		check_authority(2,7,"add");

		$today = $GLOBALS['TODAY'];
		$parm = array(	"pd_id"		=>  $PHP_id,
						"ord_num"	=>  $PHP_num,
		);
##--*****�s�[���X start
		$redir_str = "index2.php?PHP_action=smpl_supplier_search&PHP_sr_startno=".$PHP_sr_startno."&PHP_dept_code=".$PHP_dept."&PHP_num=".$PHP_nums."&PHP_cust=".$PHP_cust."&PHP_ref=".$PHP_ref."&PHP_factory=".$PHP_factory."&SCH_ptno=".$SCH_ptno."&SCH_ptdu=".$SCH_ptdu."&SCH_usmp=".$SCH_usmp."&SCH_smpdu=".$SCH_smpdu."&SCH_qty=".$SCH_qty."&SCH_ie=".$SCH_ie."&SCH_mk=".$SCH_ie."&SCH_wi=".$SCH_wi."&SCH_close=".$SCH_close."&SCH_pend=".$SCH_pend."&SCH_pttnd=".$SCH_pttnd."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_op_str=".$PHP_op_str."&PHP_op_fsh=".$PHP_op_fsh;
##--*****�s�[���X end
	// ���g�J smpl_ord ���� rcvd_pttn  ---------------
			$argu['field_name'] = 'rcvd_pttn';
			$argu['field_value'] = '0000-00-00';
			$argu['id'] = $PHP_id;
		if(!$ord = $smpl_ord->update_field($argu)){
			$op['msg'][] = $smpl_ord->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
		} 

	// --- �P�_�p���� status     -----------------
		$smpl = $smpl_ord->get($PHP_id);  //���X�ӵ��O��
		if (!$smpl) {
			$op['msg'] = $smpl_ord->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		if ($smpl['wi_date'] == '0000-00-00'){
			$new_status = 0;
		}else{
			$new_status = 1;
		}

	// �g�J  smpl_ord ���� status  ---------------
			$argu['field_name'] = 'status';
			$argu['field_value'] = $new_status;
			$argu['id'] = $PHP_id;
		if(!$ord = $smpl_ord->update_field($argu)){
			$op['msg'][] = $smpl_ord->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
		} 
				# �O���ϥΪ̰ʺA
		$message = "DELETE received Pattern of [".$PHP_num."] on:".$today;
		$log->log_add(0,"27A",$message);

		redirect_page($redir_str);
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "smpl_fab_rcvd":
#		job 22x					�O�� smpl_ord->status, smpl_ord->rcvd_fab
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	case "smpl_fab_rcvd":
		check_authority(2,7,"add");

		$today = $GLOBALS['TODAY'];
		$parm = array(	"pd_id"		=>  $PHP_id,
						"ord_num"	=>  $PHP_num,
		);
##--*****�s�[���X start
		$redir_str = "index2.php?PHP_action=smpl_supplier_search&PHP_sr_startno=".$PHP_sr_startno."&PHP_dept_code=".$PHP_dept."&PHP_num=".$PHP_nums."&PHP_cust=".$PHP_cust."&PHP_ref=".$PHP_ref."&PHP_factory=".$PHP_factory."&SCH_ptno=".$SCH_ptno."&SCH_ptdu=".$SCH_ptdu."&SCH_usmp=".$SCH_usmp."&SCH_smpdu=".$SCH_smpdu."&SCH_qty=".$SCH_qty."&SCH_ie=".$SCH_ie."&SCH_mk=".$SCH_ie."&SCH_wi=".$SCH_wi."&SCH_close=".$SCH_close."&SCH_pend=".$SCH_pend."&SCH_pttnd=".$SCH_pttnd."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_op_str=".$PHP_op_str."&PHP_op_fsh=".$PHP_op_fsh;
##--*****�s�[���X end
	// ���g�J  smpl_ord ���� rcvd_fab  ---------------
			$argu['field_name'] = 'rcvd_fab';
			$argu['field_value'] = $today;
			$argu['id'] = $PHP_id;
		if(!$ord = $smpl_ord->update_field($argu)){
			$op['msg'][] = $smpl_ord->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
		} 

	// --- �P�_�p���� status  -----------------
		$smpl = $smpl_ord->get($PHP_id);  //���X�ӵ��O��
		if (!$smpl) {
			$op['msg'] = $smpl_ord->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		if ($smpl['wi_date'] == '0000-00-00'){
			$new_status = 0;
		}elseif($smpl['pttn_suppt'] && $smpl['rcvd_pttn'] == '0000-00-00'){
			$new_status = 1;
		}elseif(!$smpl['acc_substu'] && $smpl['rcvd_acc'] == '0000-00-00'){
			$new_status = 3;
		}else{
			$new_status = 4;
		}

	// �g�J  smpl_ord ���� status  ---------------
			$argu['field_name'] = 'status';
			$argu['field_value'] = $new_status;
			$argu['id'] = $PHP_id;
		if(!$ord = $smpl_ord->update_field($argu)){
			$op['msg'][] = $smpl_ord->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
		} 
				# �O���ϥΪ̰ʺA
		$message = "CFM received fabric of [".$PHP_num."] on:".$today;
		$log->log_add(0,"27A",$message);
		redirect_page($redir_str);
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "smpl_fab_back":	�٥h ��� fabric ���쪺�O�� (�u����ѥi��)
#		job 22x					�O�� smpl_ord->status, smpl_ord->rcvd_fab
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	case "smpl_fab_back":

		check_authority(2,7,"add");

		if(!$admin->is_power(2,7,"add")){ 
			$op['msg'][] = "sorry! you don't have this permission !";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$today = $GLOBALS['TODAY'];
		$parm = array(	"pd_id"		=>  $PHP_id,
						"ord_num"	=>  $PHP_num,
		);
##--*****�s�[���X start
		$redir_str = "index2.php?PHP_action=smpl_supplier_search&PHP_sr_startno=".$PHP_sr_startno."&PHP_dept_code=".$PHP_dept."&PHP_num=".$PHP_nums."&PHP_cust=".$PHP_cust."&PHP_ref=".$PHP_ref."&PHP_factory=".$PHP_factory."&SCH_ptno=".$SCH_ptno."&SCH_ptdu=".$SCH_ptdu."&SCH_usmp=".$SCH_usmp."&SCH_smpdu=".$SCH_smpdu."&SCH_qty=".$SCH_qty."&SCH_ie=".$SCH_ie."&SCH_mk=".$SCH_ie."&SCH_wi=".$SCH_wi."&SCH_close=".$SCH_close."&SCH_pend=".$SCH_pend."&SCH_pttnd=".$SCH_pttnd."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_op_str=".$PHP_op_str."&PHP_op_fsh=".$PHP_op_fsh;
##--*****�s�[���X end
	// ���g�J  smpl_ord ���� rcvd_fab  ---------------
			$argu['field_name'] = 'rcvd_fab';
			$argu['field_value'] = '0000-00-00';
			$argu['id'] = $PHP_id;
		if(!$ord = $smpl_ord->update_field($argu)){
			$op['msg'][] = $smpl_ord->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
		} 

	// --- �P�_�p���� status   -----------------
		$smpl = $smpl_ord->get($PHP_id);  //���X�ӵ��O��
		if (!$smpl) {
			$op['msg'] = $smpl_ord->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		if ($smpl['wi_date'] == '0000-00-00'){
			$new_status = 0;
		}elseif($smpl['pttn_suppt'] && $smpl['rcvd_pttn'] == '0000-00-00'){
			$new_status = 1;
		}else{
			$new_status = 2;
		}

	// �g�J  smpl_ord ���� status  ---------------
			$argu['field_name'] = 'status';
			$argu['field_value'] = $new_status;
			$argu['id'] = $PHP_id;
		if(!$ord = $smpl_ord->update_field($argu)){
			$op['msg'][] = $smpl_ord->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
		} 
				# �O���ϥΪ̰ʺA
		$message = "DELETE received fabric of [".$PHP_num."] on:".$today;
		$log->log_add(0,"27A",$message);

		redirect_page($redir_str);
		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "smpl_acc_rcvd":
#		job 22x					�O�� smpl_ord->status, smpl_ord->rcvd_acc
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	case "smpl_acc_rcvd":

		check_authority(2,7,"add");

		$today = $GLOBALS['TODAY'];
		$parm = array(	"pd_id"		=>  $PHP_id,
						"ord_num"	=>  $PHP_num,
		);
##--*****�s�[���X start
		$redir_str = "index2.php?PHP_action=smpl_supplier_search&PHP_sr_startno=".$PHP_sr_startno."&PHP_dept_code=".$PHP_dept."&PHP_num=".$PHP_nums."&PHP_cust=".$PHP_cust."&PHP_ref=".$PHP_ref."&PHP_factory=".$PHP_factory."&SCH_ptno=".$SCH_ptno."&SCH_ptdu=".$SCH_ptdu."&SCH_usmp=".$SCH_usmp."&SCH_smpdu=".$SCH_smpdu."&SCH_qty=".$SCH_qty."&SCH_ie=".$SCH_ie."&SCH_mk=".$SCH_ie."&SCH_wi=".$SCH_wi."&SCH_close=".$SCH_close."&SCH_pend=".$SCH_pend."&SCH_pttnd=".$SCH_pttnd."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_op_str=".$PHP_op_str."&PHP_op_fsh=".$PHP_op_fsh;
##--*****�s�[���X end
	// ���g�J  smpl_ord ���� rcvd_acc  ---------------
			$argu['field_name'] = 'rcvd_acc';
			$argu['field_value'] = $today;
			$argu['id'] = $PHP_id;
		if(!$ord = $smpl_ord->update_field($argu)){
			$op['msg'][] = $smpl_ord->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
		} 

	// ---  �P�_�p���� status   -----------------
		$smpl = $smpl_ord->get($PHP_id);  //���X�ӵ��O��
		if (!$smpl) {
			$op['msg'] = $smpl_ord->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		if ($smpl['wi_date'] == '0000-00-00'){
			$new_status = 0;
		}elseif($smpl['pttn_suppt'] && $smpl['rcvd_pttn'] == '0000-00-00'){
			$new_status = 1;
		}elseif(!$smpl['fab_substu'] && $smpl['rcvd_fab'] == '0000-00-00'){
			$new_status = 2;
		}else{
			$new_status = 4;
		}

	// �g�J  smpl_ord ���� status  ---------------
			$argu['field_name'] = 'status';
			$argu['field_value'] = $new_status;
			$argu['id'] = $PHP_id;
		if(!$ord = $smpl_ord->update_field($argu)){
			$op['msg'][] = $smpl_ord->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
		} 
		# �O���ϥΪ̰ʺA
		$message = "CFM received accessory of [".$PHP_num."] on:".$today;
		$log->log_add(0,"27A",$message);
		redirect_page($redir_str);
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "smpl_acc_back":	�٥h ��� fabric ���쪺�O�� (�u����ѥi��)
#		job 22x					�O�� smpl_ord->status, smpl_ord->rcvd_acc
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	case "smpl_acc_back":

		check_authority(2,7,"add");

		$today = $GLOBALS['TODAY'];
		$parm = array(	"pd_id"		=>  $PHP_id,
						"ord_num"	=>  $PHP_num,
		);
##--*****�s�[���X start
		$redir_str = "index2.php?PHP_action=smpl_supplier_search&PHP_sr_startno=".$PHP_sr_startno."&PHP_dept_code=".$PHP_dept."&PHP_num=".$PHP_nums."&PHP_cust=".$PHP_cust."&PHP_ref=".$PHP_ref."&PHP_factory=".$PHP_factory."&SCH_ptno=".$SCH_ptno."&SCH_ptdu=".$SCH_ptdu."&SCH_usmp=".$SCH_usmp."&SCH_smpdu=".$SCH_smpdu."&SCH_qty=".$SCH_qty."&SCH_ie=".$SCH_ie."&SCH_mk=".$SCH_ie."&SCH_wi=".$SCH_wi."&SCH_close=".$SCH_close."&SCH_pend=".$SCH_pend."&SCH_pttnd=".$SCH_pttnd."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_op_str=".$PHP_op_str."&PHP_op_fsh=".$PHP_op_fsh;
##--*****�s�[���X end
	// ���g�J  smpl_ord ���� rcvd_acc  ---------------
			$argu['field_name'] = 'rcvd_acc';
			$argu['field_value'] = '0000-00-00';
			$argu['id'] = $PHP_id;
		if(!$ord = $smpl_ord->update_field($argu)){
			$op['msg'][] = $smpl_ord->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
		} 
	// �q�_�p���� status   ######  -----------------
		$smpl = $smpl_ord->get($PHP_id);  //���X�ӵ��O��
		if (!$smpl) {
			$op['msg'] = $smpl_ord->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		if ($smpl['wi_date'] == '0000-00-00'){
			$new_status = 0;
		}elseif($smpl['pttn_suttp'] && $smpl['rcvd_pttn'] == '0000-00-00'){
			$new_status = 1;
		}elseif(!$smpl['fab_substu'] && $smpl['rcvd_fab'] == '0000-00-00'){
			$new_status = 2;
		}else{
			$new_status = 3;
		}

	// �g�J  smpl_ord ���� status  ---------------
			$argu['field_name'] = 'status';
			$argu['field_value'] = $new_status;
			$argu['id'] = $PHP_id;
		if(!$ord = $smpl_ord->update_field($argu)){
			$op['msg'][] = $smpl_ord->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
		} 
		# �O���ϥΪ̰ʺA
		$message = "DELETE received accessory of [".$PHP_num."] on:".$today;
		$log->log_add(0,"27A",$message);
		redirect_page($redir_str);
		break;




#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "smpl_schedule":	job 23 �˥��Ʋ� [ pattern, Sample ]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "smpl_schedule":

		check_authority(2,8,"view");

		//--------- �n�`�N�令  R&D �i adding--------
		$where_str = $manager = $dept_id = '';
		$dept_ary = array();
		$sales_dept_ary = get_sales_dept(); // ���X �~�Ȫ����� [���tK0] ------
		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept']; 
		$user_team = $GLOBALS['SCACHE']['ADMIN']['team_id'];

		for ($i=0; $i<count($sales_dept_ary);$i++){
			if($user_dept == $sales_dept_ary[$i]){
				// �p�G�O�~�ȳ��i�J �hdept_code ���w�ӷ~�ȳ�---
				$dept_id = $sales_dept_ary[$i];  
			}
		}

		if (!$dept_id) {    // ���O�~�ȳ��H[�]���t K0 ���H ]�i�J��
			$manager = 1;
			//�~�ȳ��� select���
			$dept_ary = $arry2->select($sales_dept_ary,"","PHP_dept_code","select","");  
		} else {
			$where_str = " WHERE dept = '".$dept_id."' ";
		}
		$team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
		if(($user_dept == 'HJ' ||$user_dept == 'LY' ) && $team<>'MD')
		{
			$manager = 1;
			$manager_v = 1;
			//�~�ȳ��� select���
			$dept_ary = $arry2->select($sales_dept_ary,"","PHP_dept_code","select","");  
			$where_str ='';

		}
		$op['manager_flag'] = $manager;
		$op['dept_id'] = $dept_id;
		$op['dept_ary'] = $dept_ary;

		$op['msg'] = $smpl_ord->msg->get(2);

		// creat cust combo box
		// ���X �Ȥ�N��
		$where_str=$where_str."order by cust_s_name"; //��cust_s_name�Ƨ�
		if(!$cust_def = $cust->get_fields('cust_init_name',$where_str)){;  //���X�Ȥ�²��
			$op['msg'][] = "sorry! there is no any customer record in your team, please add customer first!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//���X�Ȥ�N��
		for ($i=0; $i< sizeof($cust_def); $i++)
		{
			$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//�H [�Ȥ�N��-�Ȥ�²��] �e�{
		}
		$op['cust_select'] =  $arry2->select($cust_value,'','PHP_cust','select','',$cust_def_vue); 

		// ------ �M�w factory ���Ѽ� = �}�C? �ƭ�?  RD�H���i�J�ɦ��t�O ----
		if($S = $smpl_ord->is_sample_team($user_team)){
			$op['factory'] = $user_team."<input type='hidden' name='PHP_factory' value='".$user_team."'>";
		}else{
			$op['factory'] = $arry2->select($SAMPLER,'','PHP_factory','select','');  	
		}
		if($user_team=="RD"){
				$op['factory'] = $user_dept."<input type='hidden' name='PHP_factory' value='".$user_dept."'>";
		}
		// --------------------------------------------------------------------
		page_display($op, 2, 8, $TPL_SMPL_SCHEDULE);			
		break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "smpl_schedule_search"
#			job23
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "smpl_schedule_search":

		check_authority(2,8,"view");
//�s�Wsearch����
		if (!isset($SCH_wi))		$SCH_wi	='';
		if (!isset($SCH_close))	$SCH_close	='';
		if (!isset($SCH_pend))	$SCH_pend	='';
		if (!isset($SCH_pttnd))	$SCH_pttnd	='';
		if (!isset($SCH_fab))		$SCH_fab	='';
		if (!isset($SCH_acc))		$SCH_acc	='';
		if (!isset($SCH_ptno))	$SCH_ptno	='';
		if (!isset($SCH_ptdu))	$SCH_ptdu	='';
		if (!isset($SCH_usmp))	$SCH_usmp	='';
		if (!isset($SCH_smpdu))	$SCH_smpdu	='';
		if (!isset($SCH_qty))		$SCH_qty	='';
		if (!isset($SCH_ie))		$SCH_ie	='';
		if (!isset($SCH_mk))		$SCH_mk	='';

		$parm = array(	"dept"			=>  $PHP_dept_code,
										"num"				=>  $PHP_num,
										"cust"			=>	$PHP_cust,
										"ref"				=>	$PHP_ref,
										"factory"		=>	$PHP_factory,
										'wi'				=>	$SCH_wi,
										'close'			=>	$SCH_close,
										'pend'			=>	$SCH_pend,
										'pttnd'			=>	$SCH_pttnd,
										'fab'				=>	$SCH_fab,
										'acc'				=>	$SCH_acc,
										'ptno'			=>	$SCH_ptno,
										'ptdu'			=>	$SCH_ptdu,
										'usmp'			=>	$SCH_usmp,
										'smpdu'			=>	$SCH_smpdu,
										'qty'				=>	$SCH_qty,
										'ie'				=>	$SCH_ie,
										'mk'				=>	$SCH_mk,
										"op_str"		=>	$PHP_op_str,
										"op_fsh"		=>	$PHP_op_fsh,
				);

		$login_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];

		// search �q�欰 supplier �e�{������ [ status < 4 ]
		//	status : 0 : W/I wait		status : 1 : PTTN wait
		//	status : 2 : FAB wait		status : 3 : ACC wait
		//	status : 4 : PTTN schdl		status : 5 : SMPL schdl
		//	status : 6 : DEVELOPING		status : 7 : PENDING
		//  status : 10: case close

		// status  >  0  and  < 6   
		if (!$op = $smpl_ord->schedule_search(1, $PHP_dept_code)) {  
			$op['msg']= $smpl_ord->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
// �ˬd �ۤ��O�_�s�b 2007.03.23		
	for ($i=0; $i < sizeof($op['sorder']); $i++)
	{
		if(file_exists($GLOBALS['config']['root_dir']."/smpl_pic/s_".$op['sorder'][$i]['num'].".jpg")){
			$op['sorder'][$i]['main_pic'] = "./smpl_pic/s_".$op['sorder'][$i]['num'].".jpg";
		} else {
			$op['sorder'][$i]['main_pic'] = "./images/graydot.gif";
		}
		$img_size = GetImageSize($op['sorder'][$i]['main_pic']);
		if ($img_size[0] < $img_size[1]) $op['sorder'][$i]['height'] = 1;

	}

		//--- �i�Q�� login_dept �P�w�O�_ �� R&D �Χ󰪺޲z�H���i�J ?
		//--- �i�_�i EDIT , ADDING..... (�\�� switch...)
		if ($login_dept == "SA" || $login_dept == "GM" || $login_dept == "RD"){
			$op['manager_flag'] = 1;
		}

		$op['dept_id'] = $PHP_dept_code;
		$op['today'] = $GLOBALS['TODAY'];  // �ά�html�P�_

		$op['msg']= $smpl_ord->msg->get(2);
		$op['cgi']= $parm;
		$op['sub_back_str'] = "&SCH_ptno=".$SCH_ptno."&SCH_ptdu=".$SCH_ptdu."&SCH_usmp=".$SCH_usmp."&SCH_smpdu=".$SCH_smpdu."&SCH_qty=".$SCH_qty."&SCH_ie=".$SCH_ie."&SCH_mk=".$SCH_ie."&SCH_wi=".$SCH_wi."&SCH_close=".$SCH_close."&SCH_pend=".$SCH_pend."&SCH_pttnd=".$SCH_pttnd."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_op_str=".$PHP_op_str."&PHP_op_fsh=".$PHP_op_fsh;


			
		page_display($op, 2, 8, $TPL_SMPL_SCHEDULE_LIST);			
		break;

	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#			job 23V sample SCHEDULE view
#		case "smpl_schedule_view":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "smpl_schedule_view":

		check_authority(2,8,"view");

		//--------- ���X�ӵ��O��
		$op['order'] = $smpl_ord->get($PHP_id); 
		if (!$op['order']) {
			$op['msg'] = $smpl_ord->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		
		// �ˬd �ۤ��O�_�s�b 2007.03.23
		if(file_exists($GLOBALS['config']['root_dir']."/smpl_pic/".$op['order']['num'].".jpg")){
			$op['main_pic'] = "./smpl_pic/".$op['order']['num'].".jpg";
		} else {
			$op['main_pic'] = "./images/graydot.gif";
		}
		// --- ���X smpl_log ���q�� �����v50�����p�O��  -------------------
		$logs = $smpl_log->search50($op['order']['num']); 
		if (!$logs){
			$op['msg'] = $smpl_log->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$op['logs'] = $logs['log'];
		$op['log_records'] = $logs['records'];
		// --- �N�|�ǥX op['logs'] ��log����� �� $op['log_records']=1 �ӧP�_���L���
		//--------------------------------
		$parm = $op['order'];

		// �[�J��^�e�� --------------
		$back_str = $cgiget."&PHP_sr_startno=".$cgino."&PHP_dept_code=".$cgi_1."&PHP_num=".$cgi_2."&PHP_cust=".$cgi_3."&PHP_ref=".$cgi_4."&PHP_factory=".$cgi_5."&SCH_ptno=".$SCH_ptno."&SCH_ptdu=".$SCH_ptdu."&SCH_usmp=".$SCH_usmp."&SCH_smpdu=".$SCH_smpdu."&SCH_qty=".$SCH_qty."&SCH_ie=".$SCH_ie."&SCH_mk=".$SCH_ie."&SCH_wi=".$SCH_wi."&SCH_close=".$SCH_close."&SCH_pend=".$SCH_pend."&SCH_pttnd=".$SCH_pttnd."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_op_str=".$PHP_op_str."&PHP_op_fsh=".$PHP_op_fsh;
		
		$op['back_str'] = $back_str;
		$op['sub_back_str'] = "&SCH_ptno=".$SCH_ptno."&SCH_ptdu=".$SCH_ptdu."&SCH_usmp=".$SCH_usmp."&SCH_smpdu=".$SCH_smpdu."&SCH_qty=".$SCH_qty."&SCH_ie=".$SCH_ie."&SCH_mk=".$SCH_ie."&SCH_wi=".$SCH_wi."&SCH_close=".$SCH_close."&SCH_pend=".$SCH_pend."&SCH_pttnd=".$SCH_pttnd."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_op_str=".$PHP_op_str."&PHP_op_fsh=".$PHP_op_fsh;
		$op['cgi']= array( 'dept' 		=>	$cgi_1,
												'num'			=>	$cgi_2,
												'cust'		=>	$cgi_3,
												'ref'			=>	$cgi_4,
												'factory'	=>	$cgi_5
										 );	
		$op['cgistr_get'] = $cgiget;
		$op['start_no'] = $cgino;

		// creat LOG SUBJ combo box
		$op['logs_subj'] = $arry2->select($SMPL_LOG_SUBJ,'','PHP_subj','select','');  	
		$op['search'] = '1';

		# �O���ϥΪ̰ʺA
		$op['msg'] = $smpl_ord->msg->get(2);

		// �إ� ��� ����� combo list
		$op['cgistr_get'] = $cgiget;
		$img_size = GetImageSize($op['main_pic']);
		if ($img_size[0] < $img_size[1]) $op['height'] = 1;

		page_display($op, 2, 8, $TPL_SMPL_SCHEDULE_SHOW);			
		break;



	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		job 28A sample SCHEDULE arrangement for PATTERN
#		case "smpl_pttn_schd":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "smpl_pttn_schd":

		check_authority(2,8,"add");

		$parm = array(	"id"		=>  $PHP_id,
						"num"		=>  $PHP_num,
						"pttn_date"	=>	$PHP_pttn_date,
				);

		$op['order'] = $smpl_ord->get($PHP_id);  //���X�ӵ��O��
		if (!$op['order']) {
			$op['msg'] = $smpl_ord->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		//---- ��� status �O�� ------
		$parm['status'] = ($op['order']['schd_smpl'] <> '0000-00-00') ?  6 : 5;

	if(!$PHP_pttn_date){   //��J�� �ˬd
		$op['msg'] = $smpl_ord->msg->get(2);

	} else {
		if (!$A = $smpl_ord->add_pttn_schedule($parm)){
			$op['msg'] = $smpl_ord->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$op['msg'][] = $message = " ADD Pattern Schedule of #".$parm['num']." by : ".$parm['pttn_date'];
				# �O���ϥΪ̰ʺA
		$log->log_add(0,"28A",$message);

		$op['order'] = $smpl_ord->get($PHP_id);  //���X�ӵ��O��
		if (!$op['order']) {
			$op['msg'] = $smpl_ord->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
	}

	//----------------------------------------- �e�{ �O�� ---------
	// ���X smpl_log ���q�� �����v50�����p�O��  -------------------
		$logs = $smpl_log->search50($op['order']['num']); 
		if (!$logs){
			$op['msg'] = $smpl_log->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$op['logs'] = $logs['log'];
		$op['log_records'] = $logs['records'];
		// �N�|�ǥX op['logs'] ��log����� �� $op['log_records']=1 �ӧP�_���L���
		//--------------------------------
		$parm = $op['order'];

		// �[�J��^�e�� --------------
		$cgiget = $PHP_back;
		$back_str = $cgiget."&PHP_sr_startno=".$cgino."&PHP_dept_code=".$cgi_1."&PHP_num=".$cgi_2."&PHP_cust=".$cgi_3."&PHP_ref=".$cgi_4."&PHP_factory=".$cgi_5."&SCH_ptno=".$SCH_ptno."&SCH_ptdu=".$SCH_ptdu."&SCH_usmp=".$SCH_usmp."&SCH_smpdu=".$SCH_smpdu."&SCH_qty=".$SCH_qty."&SCH_ie=".$SCH_ie."&SCH_mk=".$SCH_ie."&SCH_wi=".$SCH_wi."&SCH_close=".$SCH_close."&SCH_pend=".$SCH_pend."&SCH_pttnd=".$SCH_pttnd."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_op_str=".$PHP_op_str."&PHP_op_fsh=".$PHP_op_fsh;
		
		$op['back_str'] = $back_str;
		$op['sub_back_str'] = "&SCH_ptno=".$SCH_ptno."&SCH_ptdu=".$SCH_ptdu."&SCH_usmp=".$SCH_usmp."&SCH_smpdu=".$SCH_smpdu."&SCH_qty=".$SCH_qty."&SCH_ie=".$SCH_ie."&SCH_mk=".$SCH_ie."&SCH_wi=".$SCH_wi."&SCH_close=".$SCH_close."&SCH_pend=".$SCH_pend."&SCH_pttnd=".$SCH_pttnd."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_op_str=".$PHP_op_str."&PHP_op_fsh=".$PHP_op_fsh;
		$op['search'] = '1';
		$op['cgi']= array( 'dept' 		=>	$cgi_1,
												'num'			=>	$cgi_2,
												'cust'		=>	$cgi_3,
												'ref'			=>	$cgi_4,
												'factory'	=>	$cgi_5
										 );	
		$op['cgistr_get'] = $cgiget;
		$op['start_no'] = $cgino;
				// �ˬd �ۤ��O�_�s�b 2007.03.23
		if(file_exists($GLOBALS['config']['root_dir']."/smpl_pic/".$op['order']['num'].".jpg")){
			$op['main_pic'] = "./smpl_pic/".$op['order']['num'].".jpg";
		} else {
			$op['main_pic'] = "./images/graydot.gif";
		}

		// creat LOG SUBJ combo box
		$op['logs_subj'] = $arry2->select($SMPL_LOG_SUBJ,'','PHP_subj','select','');  	
		$img_size = GetImageSize($op['main_pic']);
		if ($img_size[0] < $img_size[1]) $op['height'] = 1;

		page_display($op, 2, 8, $TPL_SMPL_SCHEDULE_SHOW);			
		break;

	


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		job 28A sample SCHEDULE arrangement for SAMPLE
#		case "smpl_smpl_schd":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "smpl_smpl_schd":

		check_authority(2,8,"add");

		$parm = array(	"id"		=>  $PHP_id,
						"num"		=>  $PHP_num,
						"smpl_date"	=>	$PHP_smpl_date,
				);
		//----- ���X�ӵ��O��
		$op['order'] = $smpl_ord->get($PHP_id);  
		if (!$op['order']) {
			$op['msg'] = $smpl_ord->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		//---- ��� status �O�� ------
		$parm['status'] = ($op['order']['schd_pttn'] <> '0000-00-00') ?  6 : 4;
	
	if(!$PHP_smpl_date){
		$op['msg'] = $smpl_ord->msg->get(2);

	} else {
		if (!$A = $smpl_ord->add_smpl_schedule($parm)){
			$op['msg'] = $smpl_ord->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$op['msg'][] = $message = " ADD Sample Schedule of #".$parm['num']." by : ".$parm['smpl_date'];
				# �O���ϥΪ̰ʺA
		$log->log_add(0,"23A",$message);

		$op['order'] = $smpl_ord->get($PHP_id);  //���X�ӵ��O��
		if (!$op['order']) {
			$op['msg'] = $smpl_ord->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
	}
		//--------------------------------------------- �e�{ �O�� ---------
		// ���X smpl_log ���q�� �����v50�����p�O��  -------------------
		$logs = $smpl_log->search50($op['order']['num']); 
		if (!$logs){
			$op['msg'] = $smpl_log->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$op['logs'] = $logs['log'];
		$op['log_records'] = $logs['records'];
		// �N�|�ǥX op['logs'] ��log����� �� $op['log_records']=1 �ӧP�_���L���
		//--------------------------------
		$parm = $op['order'];
		// �[�J��^�e�� --------------
		$cgiget = $PHP_back;
		$back_str = $cgiget."&PHP_sr_startno=".$cgino."&PHP_dept_code=".$cgi_1."&PHP_num=".$cgi_2."&PHP_cust=".$cgi_3."&PHP_ref=".$cgi_4."&PHP_factory=".$cgi_5."&SCH_ptno=".$SCH_ptno."&SCH_ptdu=".$SCH_ptdu."&SCH_usmp=".$SCH_usmp."&SCH_smpdu=".$SCH_smpdu."&SCH_qty=".$SCH_qty."&SCH_ie=".$SCH_ie."&SCH_mk=".$SCH_ie."&SCH_wi=".$SCH_wi."&SCH_close=".$SCH_close."&SCH_pend=".$SCH_pend."&SCH_pttnd=".$SCH_pttnd."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_op_str=".$PHP_op_str."&PHP_op_fsh=".$PHP_op_fsh;
		
		$op['back_str'] = $back_str;
		$op['sub_back_str'] = "&SCH_ptno=".$SCH_ptno."&SCH_ptdu=".$SCH_ptdu."&SCH_usmp=".$SCH_usmp."&SCH_smpdu=".$SCH_smpdu."&SCH_qty=".$SCH_qty."&SCH_ie=".$SCH_ie."&SCH_mk=".$SCH_ie."&SCH_wi=".$SCH_wi."&SCH_close=".$SCH_close."&SCH_pend=".$SCH_pend."&SCH_pttnd=".$SCH_pttnd."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_op_str=".$PHP_op_str."&PHP_op_fsh=".$PHP_op_fsh;
		$op['search'] = '1';
		$op['cgi']= array( 'dept' 		=>	$cgi_1,
												'num'			=>	$cgi_2,
												'cust'		=>	$cgi_3,
												'ref'			=>	$cgi_4,
												'factory'	=>	$cgi_5
										 );	
		$op['cgistr_get'] = $cgiget;
		$op['start_no'] = $cgino;
		// creat LOG SUBJ combo box
		$op['logs_subj'] = $arry2->select($SMPL_LOG_SUBJ,'','PHP_subj','select','');  	
				// �ˬd �ۤ��O�_�s�b 2007.03.23
		if(file_exists($GLOBALS['config']['root_dir']."/smpl_pic/".$op['order']['num'].".jpg")){
			$op['main_pic'] = "./smpl_pic/".$op['order']['num'].".jpg";
		} else {
			$op['main_pic'] = "./images/graydot.gif";
		}
		$img_size = GetImageSize($op['main_pic']);
		if ($img_size[0] < $img_size[1]) $op['height'] = 1;


		page_display($op, 2, 8, $TPL_SMPL_SCHEDULE_SHOW);			
		break;




#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "smpl_output":	job 29
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "smpl_output":

		check_authority(2,9,"view");

	//------------ �n�`�N�令  R&D �i adding--------
		$where_str = $manager = $dept_id = '';
		$dept_ary = array();
		$sales_dept_ary = get_sales_dept(); // ���X �~�Ȫ����� [���tK0] ------
		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];   // �P�w�i�J����������
		$user_team = $GLOBALS['SCACHE']['ADMIN']['team_id'];

		for ($i=0; $i<count($sales_dept_ary);$i++){
			if($user_dept == $sales_dept_ary[$i]){
				// �p�G�O�~�ȳ��i�J �hdept_code ���w�ӷ~�ȳ�---
				$dept_id = $sales_dept_ary[$i];  
			}
		}
		if (!$dept_id) {    // ���O�~�ȳ��H[�]���t K0 ���H ]�i�J��
			$manager = 1;
			//�~�ȳ��� select���
			$dept_ary = $arry2->select($sales_dept_ary,"","PHP_dept_code","select","");  
		} else {
			$where_str = " WHERE dept = '".$dept_id."' ";
		}
		$team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
		if(($user_dept == 'HJ' ||$user_dept == 'LY' ) && $team<>'MD')
		{
			$manager = 1;
			$manager_v = 1;
			//�~�ȳ��� select���
			$dept_ary = $arry2->select($sales_dept_ary,"","PHP_dept_code","select","");  
			$where_str ='';

		}		
		$op['manager_flag'] = $manager;
		$op['dept_id'] = $dept_id;
		$op['dept_ary'] = $dept_ary;

		$op['msg'] = $order->msg->get(2);

		// creat cust combo box
		// ���X �Ȥ�N��
		$where_str=$where_str."order by cust_s_name"; //��cust_s_name�Ƨ�
		if(!$cust_def = $cust->get_fields('cust_init_name',$where_str)){;  //���X�Ȥ�²��
			$op['msg'][] = "sorry! there is no any customer record in your team, please add customer first!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//���X�Ȥ�N��
		for ($i=0; $i< sizeof($cust_def); $i++)
		{
			$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//�H [�Ȥ�N��-�Ȥ�²��] �e�{
		}
		$op['cust_select'] =  $arry2->select($cust_value,'','PHP_cust','select','',$cust_def_vue); 

		// -------- �M�w factory ���Ѽ� = �}�C? �ƭ�?  RD�H���i�J�ɦ��t�O ----
		if($S = $smpl_ord->is_sample_team($user_team)){
			$op['factory'] = $user_team."<input type='hidden' name='PHP_factory' value='".$user_team."'>";
		}else{
			$op['factory'] = $arry2->select($SAMPLER,'','PHP_factory','select','');  	
		}
		if($user_team=="RD"){
				$op['factory'] = $user_dept."<input type='hidden' name='PHP_factory' value='".$user_dept."'>";
		}
		// --------------------------------------------------------------------
		page_display($op, 2, 9, $TPL_SMPL_OUTPUT);			
		break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "smpl_output_search":    JOB 24
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "smpl_output_search":

		check_authority(2,9,"view");

		if (!isset($SCH_wi))		$SCH_wi	='';
		if (!isset($SCH_close))	$SCH_close	='';
		if (!isset($SCH_pend))	$SCH_pend	='';
		if (!isset($SCH_pttnd))	$SCH_pttnd	='';
		if (!isset($SCH_fab))		$SCH_fab	='';
		if (!isset($SCH_acc))		$SCH_acc	='';
		if (!isset($SCH_ptno))	$SCH_ptno	='';
		if (!isset($SCH_ptdu))	$SCH_ptdu	='';
		if (!isset($SCH_usmp))	$SCH_usmp	='';
		if (!isset($SCH_smpdu))	$SCH_smpdu	='';
		if (!isset($SCH_qty))		$SCH_qty	='';
		if (!isset($SCH_ie))		$SCH_ie	='';
		if (!isset($SCH_mk))		$SCH_mk	='';
//�s�Wsearch����
		$parm = array(	"dept"			=>  $PHP_dept_code,
										"num"				=>  $PHP_num,
										"cust"			=>	$PHP_cust,
										"ref"				=>	$PHP_ref,
										"factory"		=>	$PHP_factory,
										'wi'				=>	$SCH_wi,
										'close'			=>	$SCH_close,
										'pend'			=>	$SCH_pend,
										'pttnd'			=>	$SCH_pttnd,
										'fab'				=>	$SCH_fab,
										'acc'				=>	$SCH_acc,
										'ptno'			=>	$SCH_ptno,
										'ptdu'			=>	$SCH_ptdu,
										'usmp'			=>	$SCH_usmp,
										'smpdu'			=>	$SCH_smpdu,
										'qty'				=>	$SCH_qty,
										'ie'				=>	$SCH_ie,
										'mk'				=>	$SCH_mk,
										"op_str"		=>	$PHP_op_str,
										"op_fsh"		=>	$PHP_op_fsh
				);


		$login_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];

		// search �q�欰 supplier �e�{������ [ status < 4 ]
		//	status : 0 : W/I wait		status : 1 : PTTN wait
		//	status : 2 : FAB wait		status : 3 : ACC wait
		//	status : 4 : PTTN schdl		status : 5 : SMPL schdl
		//	status : 6 : DEVELOPING
		//	status : 7 : PENDING
		//  status : 10: case close

		// status  > 0 

		if (!$op = $smpl_ord->output_search(1, $PHP_dept_code)) {  
			$op['msg']= $smpl_ord->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
// �ˬd �ۤ��O�_�s�b 2007.03.23		
	for ($i=0; $i < sizeof($op['sorder']); $i++)
	{
		if(file_exists($GLOBALS['config']['root_dir']."/smpl_pic/s_".$op['sorder'][$i]['num'].".jpg")){
			$op['sorder'][$i]['main_pic'] = "./smpl_pic/s_".$op['sorder'][$i]['num'].".jpg";
		} else {
			$op['sorder'][$i]['main_pic'] = "./images/graydot.gif";
		}
		$img_size = GetImageSize($op['sorder'][$i]['main_pic']);
		if ($img_size[0] < $img_size[1]) $op['sorder'][$i]['height'] = 1;

	}
			// op �Q�M���F �ݦA�P�_�@�� 
		//�i�Q�� login_dept �P�w�O�_ �� R&D �Χ󰪺޲z�H���i�J ?
		//�i�_�i edit , ADDING..... �\�� switch...
		if ($login_dept == "SA" || $login_dept == "GM" || $login_dept == "RD"){
			$op['manager_flag'] = 1;
		}

		$op['dept_id'] = $PHP_dept_code;
		$op['today'] = $GLOBALS['TODAY'];  // �ά�html�P�_

			$op['msg']= $smpl_ord->msg->get(2);
			$op['cgi']= $parm;

		page_display($op, 2, 9, $TPL_SMPL_OUTPUT_LIST);			
		break;

	


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		job 24V sample OUTPUT view
#		case "smpl_output_view":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "smpl_output_view":

		check_authority(2,9,"view");

		$op['order'] = $smpl_ord->get($PHP_id);  //���X�ӵ��O��
		if (!$op['order']) {
			$op['msg'] = $smpl_ord->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		// �ˬd �ۤ��O�_�s�b 2007.03.23
		if(file_exists($GLOBALS['config']['root_dir']."/smpl_pic/".$op['order']['num'].".jpg")){
			$op['main_pic'] = "./smpl_pic/".$op['order']['num'].".jpg";
		} else {
			$op['main_pic'] = "./images/graydot.gif";
		}

	//----- �p�� �q�� IE(SPT) �P�W�@��(�P�q��)�� IE(SPT)���t���� $diff
		$last_v = substr($op['order']['num'],10)-1;
		$last = substr($op['order']['num'],0,10).$last_v;
	
		if($last_v ==0){
			$op['diff'] = 100;
		}else{
			if (!$last_spt = $smpl_ord->get_fieldvalue('','spt', $last)) {
				$op['msg'] = $smpl_ord->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			if($last_spt['spt']){
				$op['diff'] = ($op['order']['spt']/$last_spt['spt']) * 100;
			}else{
				$op['diff'] = 100;
			}
		}
	//---------------------- end of SPT treating -----------------------------

		// ���X smpl_log ���q�� �����v50�����p�O��  -------------------
		$logs = $smpl_log->search50($op['order']['num']); 
		if (!$logs){
			$op['msg'] = $smpl_log->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$op['logs'] = $logs['log'];
		$op['log_records'] = $logs['records'];
		// �N�|�ǥX op['logs'] ��log����� �� $op['log_records']=1 �ӧP�_���L���
		//--------------------------------
		
		$parm = $op['order'];

		// �[�J��^�e�� --------------
		$back_str = $cgiget."&PHP_sr_startno=".$cgino."&PHP_dept_code=".$cgi_1."&PHP_num=".$cgi_2."&PHP_cust=".$cgi_3."&PHP_ref=".$cgi_4."&PHP_factory=".$cgi_5."&SCH_ptno=".$SCH_ptno."&SCH_ptdu=".$SCH_ptdu."&SCH_usmp=".$SCH_usmp."&SCH_smpdu=".$SCH_smpdu."&SCH_qty=".$SCH_qty."&SCH_ie=".$SCH_ie."&SCH_mk=".$SCH_ie."&SCH_wi=".$SCH_wi."&SCH_close=".$SCH_close."&SCH_pend=".$SCH_pend."&SCH_pttnd=".$SCH_pttnd."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_op_str=".$PHP_op_str."&PHP_op_fsh=".$PHP_op_fsh;
		$op['sub_back_str'] = "&PHP_sr_startno=".$cgino."&PHP_dept_code=".$cgi_1."&PHP_num=".$cgi_2."&PHP_cust=".$cgi_3."&PHP_ref=".$cgi_4."&PHP_factory=".$cgi_5."&SCH_ptno=".$SCH_ptno."&SCH_ptdu=".$SCH_ptdu."&SCH_usmp=".$SCH_usmp."&SCH_smpdu=".$SCH_smpdu."&SCH_qty=".$SCH_qty."&SCH_ie=".$SCH_ie."&SCH_mk=".$SCH_ie."&SCH_wi=".$SCH_wi."&SCH_close=".$SCH_close."&SCH_pend=".$SCH_pend."&SCH_pttnd=".$SCH_pttnd."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_op_str=".$PHP_op_str."&PHP_op_fsh=".$PHP_op_fsh;
		
		$back_str2 = "PHP_action=smpl_ord_view&PHP_id=".$PHP_id."&PHP_num=".$parm['num'];

		$op['back_str2'] = $back_str2;
		$op['back_str'] = $back_str;

		$op['cgi']= array( 'dept' 		=>	$cgi_1,
												'num'			=>	$cgi_2,
												'cust'		=>	$cgi_3,
												'ref'			=>	$cgi_4,
												'factory'	=>	$cgi_5
										 );	
		$op['cgistr_get'] = $cgiget;
		$op['start_no'] = $cgino;

		$op['msg'] = $smpl_ord->msg->get(2);
		$op['search'] = '1';

		// creat LOG SUBJ combo box
		$op['logs_subj'] = $arry2->select($SMPL_LOG_SUBJ,'','PHP_subj','select','');  	

		$img_size = GetImageSize($op['main_pic']);
		if ($img_size[0] < $img_size[1]) $op['height'] = 1;

		page_display($op, 2, 9, $TPL_SMPL_OUTPUT_SHOW);			
		break;

	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		job 24A sample pattern upload
#		case "upload_smpl_pattern":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "upload_smpl_pattern":

		check_authority(2,9,"add");

		$filename = $_FILES['PHP_pttn']['name'];
		$ext =  strtolower(preg_replace("/.*\.([^.]+)$/","\\1", $filename));

		if ($ext == "mdl"){   // �W���ɪ����ɦW�� mdl �� -----

			// upload pattern file to server
			$pttn_name = $PHP_num.'.mdl';  // ���w�� mdl ���ɦW
			$upload = new Upload;
			$upload->uploadFile(dirname($PHP_SELF).'/smpl_pttn/', 'pattern', 16, $pttn_name );
			if (!$upload){
				$op['msg'][] = $upload;
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			$today = $GLOBALS['TODAY'];
			$parm = array(	"done_pttn"		=>  $today,
				"last_update"	=>  $today,
				"updator"		=>  $GLOBALS['SCACHE']['ADMIN']['login_id'],
				"id"			=>  $PHP_id,
			);
			if (!$A = $smpl_ord->upload_pttn($parm)){
				$op['msg'] = $smpl_ord->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			$message = "UPLOAD Pattern of #".$PHP_num;
		} else {  // �W���ɪ����ɦW  ���O  mdl �� -----

			$message = "upload file is incorrect format ! Please re-send.";
		}
//----------------------------------------------------
		$op['order'] = $smpl_ord->get($PHP_id);  //���X�ӵ��O��
		if (!$op['order']) {
			$op['msg'] = $smpl_ord->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

	//----- �p�� �q�� IE(SPT) �P�W�@��(�P�q��) IE(SPT)���t���� $diff
		$last_v = substr($op['order']['num'],10)-1;
		$last = substr($op['order']['num'],0,10).$last_v;
	
		if($last_v ==0){
			$op['diff'] = 100;
		}else{
			if (!$last_spt = $smpl_ord->get_fieldvalue('','spt', $last)) {
				$op['msg'] = $smpl_ord->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			if($last_spt['spt']){
				$op['diff'] = ($op['order']['spt']/$last_spt['spt']) * 100;
			}else{
				$op['diff'] = 100;
			}
		}
	//---------------------- end of SPT treating -----------------------------

		// ���X smpl_log ���q�� �����v50�����p�O��  -------------------
		$logs = $smpl_log->search50($op['order']['num']); 
		if (!$logs){
			$op['msg'] = $smpl_log->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$op['logs'] = $logs['log'];
		$op['log_records'] = $logs['records'];
		// �N�|�ǥX op['logs'] ��log����� �� $op['log_records']=1 �ӧP�_���L���
		//--------------------------------
		$parm = $op['order'];

		// �[�J��^�e�� --------------
		$back_str = $cgiget."&PHP_sr_startno=".$cgino."&PHP_dept_code=".$cgi_1."&PHP_num=".$cgi_2."&PHP_cust=".$cgi_3."&PHP_ref=".$cgi_4."&PHP_factory=".$cgi_5."&SCH_ptno=".$SCH_ptno."&SCH_ptdu=".$SCH_ptdu."&SCH_usmp=".$SCH_usmp."&SCH_smpdu=".$SCH_smpdu."&SCH_qty=".$SCH_qty."&SCH_ie=".$SCH_ie."&SCH_mk=".$SCH_ie."&SCH_wi=".$SCH_wi."&SCH_close=".$SCH_close."&SCH_pend=".$SCH_pend."&SCH_pttnd=".$SCH_pttnd."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_op_str=".$PHP_op_str."&PHP_op_fsh=".$PHP_op_fsh;
		$op['sub_back_str'] = "&SCH_ptno=".$SCH_ptno."&SCH_ptdu=".$SCH_ptdu."&SCH_usmp=".$SCH_usmp."&SCH_smpdu=".$SCH_smpdu."&SCH_qty=".$SCH_qty."&SCH_ie=".$SCH_ie."&SCH_mk=".$SCH_ie."&SCH_wi=".$SCH_wi."&SCH_close=".$SCH_close."&SCH_pend=".$SCH_pend."&SCH_pttnd=".$SCH_pttnd."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_op_str=".$PHP_op_str."&PHP_op_fsh=".$PHP_op_fsh;
		
		$back_str2 = "PHP_action=smpl_ord_view&PHP_id=".$PHP_id."&PHP_num=".$parm['num'];

		$op['back_str2'] = $back_str2;
		$op['back_str'] = $back_str;

		$op['cgi']= array( 'dept' 		=>	$cgi_1,
												'num'			=>	$cgi_2,
												'cust'		=>	$cgi_3,
												'ref'			=>	$cgi_4,
												'factory'	=>	$cgi_5
										 );	
		$op['cgistr_get'] = $cgiget;
		$op['start_no'] = $cgino;
		// �ˬd �ۤ��O�_�s�b 2007.03.23
		if(file_exists($GLOBALS['config']['root_dir']."/smpl_pic/".$op['order']['num'].".jpg")){
			$op['main_pic'] = "./smpl_pic/".$op['order']['num'].".jpg";
		} else {
			$op['main_pic'] = "./images/graydot.gif";
		}

		$op['msg'][] = $message;
		$op['search'] = '1';

		// creat LOG SUBJ combo box
		$op['logs_subj'] = $arry2->select($SMPL_LOG_SUBJ,'','PHP_subj','select','');  	

		$img_size = GetImageSize($op['main_pic']);
		if ($img_size[0] < $img_size[1]) $op['height'] = 1;

	
		page_display($op, 2, 9, $TPL_SMPL_OUTPUT_SHOW);			
		break;


	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		job 29A sample marker upload     --- 
#		case "upload_smpl_marker":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "upload_smpl_marker":

		check_authority(2,9,"add");

		$filename = $_FILES['PHP_marker']['name'];
		$ext =  strtolower(preg_replace("/.*\.([^.]+)$/","\\1", $filename));

		if ($ext == "pdf"){   // �W���ɪ����ɦW�� mdl �� -----
			// upload marker file to server  // ���w�� pdf ���ɦW
			$marker_name = $PHP_num.'.mk'.'.pdf';
			$upload = new Upload;
			$upload->uploadFile(dirname($PHP_SELF).'/smpl_marker/', 'marker', 16, $marker_name );
			if (!$upload){
				$op['msg'][] = $upload;
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			$today = $GLOBALS['TODAY'];
			$parm = array(	"marker_date"	=>  $today,
											"last_update"	=>  $today,
											"updator"			=>  $GLOBALS['SCACHE']['ADMIN']['login_id'],
											"id"					=>  $PHP_id,
			);

			if (!$A = $smpl_ord->upload_marker($parm)){
				$op['msg'] = $smpl_ord->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			$message = "UPLOAD Marker of #".$PHP_num;

		} else {  // �W���ɪ����ɦW  ���O  mdl �� -----

			$message = "upload MARKER file is incorrect format, Please re-send.";
		}
//----------------------------------------------------
		$op['order'] = $smpl_ord->get($PHP_id);  //���X�ӵ��O��
		if (!$op['order']) {
			$op['msg'] = $smpl_ord->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

	//----- �p�� �q�� IE(SPT) �P�W�@��(�P�q��) ��IE(SPT)���t���� $diff
		$last_v = substr($op['order']['num'],10)-1;
		$last = substr($op['order']['num'],0,10).$last_v;
	
		if($last_v ==0){
			$op['diff'] = 100;
		}else{
			if (!$last_spt = $smpl_ord->get_fieldvalue('','spt', $last)) {
				$op['msg'] = $smpl_ord->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			if($last_spt['spt']){
				$op['diff'] = ($op['order']['spt']/$last_spt['spt']) * 100;
			}else{
				$op['diff'] = 100;
			}
		}
	//---------------------- end of SPT treating -----------------------------

		// ���X smpl_log ���q�� �����v50�����p�O��  -------------------
		$logs = $smpl_log->search50($op['order']['num']); 
		if (!$logs){
			$op['msg'] = $smpl_log->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$op['logs'] = $logs['log'];
		$op['log_records'] = $logs['records'];
		// �N�|�ǥX op['logs'] ��log����� �� $op['log_records']=1 �ӧP�_���L���
		//--------------------------------
		$parm = $op['order'];

		// �[�J��^�e�� --------------
		$back_str = $cgiget."&PHP_sr_startno=".$cgino."&PHP_dept_code=".$cgi_1."&PHP_num=".$cgi_2."&PHP_cust=".$cgi_3."&PHP_ref=".$cgi_4."&PHP_factory=".$cgi_5."&SCH_ptno=".$SCH_ptno."&SCH_ptdu=".$SCH_ptdu."&SCH_usmp=".$SCH_usmp."&SCH_smpdu=".$SCH_smpdu."&SCH_qty=".$SCH_qty."&SCH_ie=".$SCH_ie."&SCH_mk=".$SCH_ie."&SCH_wi=".$SCH_wi."&SCH_close=".$SCH_close."&SCH_pend=".$SCH_pend."&SCH_pttnd=".$SCH_pttnd."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_op_str=".$PHP_op_str."&PHP_op_fsh=".$PHP_op_fsh;
		$op['sub_back_str'] = "&PHP_sr_startno=".$cgino."&PHP_dept_code=".$cgi_1."&PHP_num=".$cgi_2."&PHP_cust=".$cgi_3."&PHP_ref=".$cgi_4."&PHP_factory=".$cgi_5."&SCH_ptno=".$SCH_ptno."&SCH_ptdu=".$SCH_ptdu."&SCH_usmp=".$SCH_usmp."&SCH_smpdu=".$SCH_smpdu."&SCH_qty=".$SCH_qty."&SCH_ie=".$SCH_ie."&SCH_mk=".$SCH_ie."&SCH_wi=".$SCH_wi."&SCH_close=".$SCH_close."&SCH_pend=".$SCH_pend."&SCH_pttnd=".$SCH_pttnd."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_op_str=".$PHP_op_str."&PHP_op_fsh=".$PHP_op_fsh;
		
		$back_str2 = "PHP_action=smpl_ord_view&PHP_id=".$PHP_id."&PHP_num=".$parm['num'];

		$op['back_str2'] = $back_str2;
		$op['back_str'] = $back_str;
		$op['cgi']= array( 'dept' 		=>	$cgi_1,
												'num'			=>	$cgi_2,
												'cust'		=>	$cgi_3,
												'ref'			=>	$cgi_4,
												'factory'	=>	$cgi_5
										 );	
		$op['cgistr_get'] = $cgiget;
		$op['start_no'] = $cgino;
		# �O���ϥΪ̰ʺA

		$op['msg'][] = $message;
		$op['search'] = '1';
		
		// �ˬd �ۤ��O�_�s�b 2007.03.23
		if(file_exists($GLOBALS['config']['root_dir']."/smpl_pic/".$op['order']['num'].".jpg")){
			$op['main_pic'] = "./smpl_pic/".$op['order']['num'].".jpg";
		} else {
			$op['main_pic'] = "./images/graydot.gif";
		}

		// creat LOG SUBJ combo box
		$op['logs_subj'] = $arry2->select($SMPL_LOG_SUBJ,'','PHP_subj','select','');  	

		$img_size = GetImageSize($op['main_pic']);
		if ($img_size[0] < $img_size[1]) $op['height'] = 1;


		page_display($op, 2, 9, $TPL_SMPL_OUTPUT_SHOW);			
		break;




#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		job 29V sample pattern download
#		case "smpl_pttn_download":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "smpl_pttn_download":

		if(!$admin->is_power(2,1,"view")  && !$admin->is_power(2,9,"view")){ 
			$op['msg'][] = "sorry! you don't have this Authority!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		// path example
		$myPath = './smpl_pttn/';

		// New Object
		$objDownload = new EasyDownload();

		// Set physical path
		$objDownload->setPath($myPath);

		// Set file name on the server (real full name)
		//$objDownload->setFileName($_GET["file"]);
		$objDownload->setFileName($PHP_num. '.mdl');

		// In case that it does not desire to effect download with original name.  
		// It configures the alternative name
		//$objDownload->setFileNameDown($_GET["fileName"] . $_GET["extension"]);
		$objDownload->setFileNameDown($PHP_num.'.mdl');

		// get file
		$objDownload->Send();

	break;

	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		job 24V sample MARKER download
#		case "smpl_marker_download":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "smpl_marker_download":

		if(!$admin->is_power(2,1,"view")  &&  !$admin->is_power(2,9,"view")){ 
			$op['msg'][] = "sorry! you don't have this Authority!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		// path example
		$myPath = './smpl_marker/';

		// New Object
		$objDownload = new EasyDownload();

		// Set physical path
		$objDownload->setPath($myPath);

		// Set file name on the server (real full name)
		//$objDownload->setFileName($_GET["file"]);
		$objDownload->setFileName($PHP_num. '.mk'.'.pdf');

		// In case that it does not desire to effect download with original name.  
		// It configures the alternative name
		//$objDownload->setFileNameDown($_GET["fileName"] . $_GET["extension"]);
		$objDownload->setFileName($PHP_num. '.mk'.'.pdf');

		// get file
		$objDownload->Send();

	break;
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		job 24A smpl output
#		case "smpl_complete":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "smpl_complete":

		check_authority(2,9,"add");

		$today = $GLOBALS['TODAY'];
		$parm = array(	"id"		=>  $PHP_id,
				"qty"			=>  $PHP_qty,
				"fdate"			=>  $GLOBALS['TODAY'],
				"num"			=>  $PHP_num,
				"last_update"	=>  $GLOBALS['TODAY'],
				"updator"		=>  $GLOBALS['SCACHE']['ADMIN']['login_id'],
				"factory"		=>  $PHP_factory,
		);

		// �[�J smpl_output ���O��
		if (!$C = $smpl_output->add($parm)){
			$op['msg'] = $smpl_output->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		//  ��s smpl_ord ���������ƶq
		if (!$A = $smpl_ord->add_complete($parm)){
			$op['msg'] = $smpl_ord->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$message = "add complete qty :".$PHP_qty." of #".$PHP_num;
//----------------------------------------------------
		$op['order'] = $smpl_ord->get($PHP_id);  //���X�ӵ��O��
		if (!$op['order']) {
			$op['msg'] = $smpl_ord->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

	//----- �p�� �q�� IE(SPT) �P�W�@��(�P�q��) ��IE(SPT)���t���� $diff
		$last_v = substr($op['order']['num'],10)-1;
		$last = substr($op['order']['num'],0,10).$last_v;
	
		if($last_v ==0){
			$op['diff'] = 100;
		}else{
			if (!$last_spt = $smpl_ord->get_fieldvalue('','spt', $last)) {
				$op['msg'] = $smpl_ord->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			if($last_spt['spt']){
				$op['diff'] = ($op['order']['spt']/$last_spt['spt']) * 100;
			}else{
				$op['diff'] = 100;
			}
		}
	//------------ end of SPT treating -----------------------------

		// ���X smpl_log ���q�� �����v50�����p�O��  ----------------
		$logs = $smpl_log->search50($op['order']['num']); 
		if (!$logs){
			$op['msg'] = $smpl_log->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$op['logs'] = $logs['log'];
		$op['log_records'] = $logs['records'];
		// �N�|�ǥX op['logs'] ��log����� �� $op['log_records']=1 �ӧP�_���L���
		//--------------------------------
		$parm = $op['order'];

		// �[�J��^�e�� --------------
		$back_str = $cgiget."&PHP_sr_startno=".$cgino."&PHP_dept_code=".$cgi_1."&PHP_num=".$cgi_2."&PHP_cust=".$cgi_3."&PHP_ref=".$cgi_4."&PHP_factory=".$cgi_5."&SCH_ptno=".$SCH_ptno."&SCH_ptdu=".$SCH_ptdu."&SCH_usmp=".$SCH_usmp."&SCH_smpdu=".$SCH_smpdu."&SCH_qty=".$SCH_qty."&SCH_ie=".$SCH_ie."&SCH_mk=".$SCH_ie."&SCH_wi=".$SCH_wi."&SCH_close=".$SCH_close."&SCH_pend=".$SCH_pend."&SCH_pttnd=".$SCH_pttnd."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_op_str=".$PHP_op_str."&PHP_op_fsh=".$PHP_op_fsh;
		$op['sub_back_str'] = "&SCH_ptno=".$SCH_ptno."&SCH_ptdu=".$SCH_ptdu."&SCH_usmp=".$SCH_usmp."&SCH_smpdu=".$SCH_smpdu."&SCH_qty=".$SCH_qty."&SCH_ie=".$SCH_ie."&SCH_mk=".$SCH_ie."&SCH_wi=".$SCH_wi."&SCH_close=".$SCH_close."&SCH_pend=".$SCH_pend."&SCH_pttnd=".$SCH_pttnd."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_op_str=".$PHP_op_str."&PHP_op_fsh=".$PHP_op_fsh;
		
		$back_str2 = "PHP_action=smpl_ord_view&PHP_id=".$PHP_id."&PHP_num=".$parm['num'];

		$op['back_str2'] = $back_str2;
		$op['back_str'] = $back_str;
		$op['cgi']= array( 'dept' 		=>	$cgi_1,
												'num'			=>	$cgi_2,
												'cust'		=>	$cgi_3,
												'ref'			=>	$cgi_4,
												'factory'	=>	$cgi_5
										 );	
		$op['cgistr_get'] = $cgiget;
		$op['start_no'] = $cgino;
				// �ˬd �ۤ��O�_�s�b 2007.03.23
		if(file_exists($GLOBALS['config']['root_dir']."/smpl_pic/".$op['order']['num'].".jpg")){
			$op['main_pic'] = "./smpl_pic/".$op['order']['num'].".jpg";
		} else {
			$op['main_pic'] = "./images/graydot.gif";
		}
		
		$op['msg'][] = $message;

		$op['search'] = '1';

		// creat LOG SUBJ combo box
		$op['logs_subj'] = $arry2->select($SMPL_LOG_SUBJ,'','PHP_subj','select','');  	

		$img_size = GetImageSize($op['main_pic']);
		if ($img_size[0] < $img_size[1]) $op['height'] = 1;


		page_display($op, 2, 9, $TPL_SMPL_OUTPUT_SHOW);			
		break;

	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		job 29E  case_close
#		case "smpl_case_close":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "smpl_case_close":

		check_authority(2,9,"edit");

		$today = $GLOBALS['TODAY'];
		$parm = array(	"id"			=>  $PHP_id,
				"case_close"	=>  $GLOBALS['TODAY'],
				"last_update"	=>  $GLOBALS['TODAY'],
				"updator"		=>  $GLOBALS['SCACHE']['ADMIN']['login_id'],
				"status"		=>  10,
		);

		//  ��s smpl_ord ���� �O��
		if (!$A = $smpl_ord->case_close($parm)){
			$op['msg'] = $smpl_ord->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$message = " close-case of #".$PHP_num;
		//----------------------------------------------------
		//���X�ӵ��O��
		$op['order'] = $smpl_ord->get($PHP_id);  
		if (!$op['order']) {
			$op['msg'] = $smpl_ord->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}


	//----- �p�� �q�� IE(SPT) �P�W�@��(�P�q��)��IE(SPT)���t���� $diff
		$last_v = substr($op['order']['num'],10)-1;
		$last = substr($op['order']['num'],0,10).$last_v;
	
		if($last_v ==0){
			$op['diff'] = 100;
		}else{
			if (!$last_spt = $smpl_ord->get_fieldvalue('','spt', $last)) {
				$op['msg'] = $smpl_ord->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			if($last_spt['spt']){
				$op['diff'] = ($op['order']['spt']/$last_spt['spt']) * 100;
			}else{
				$op['diff'] = 100;
			}
		}
	//---------------------- end of SPT treating --------------------------

		// ���X smpl_log ���q�� �����v50�����p�O��  -------------------
		$logs = $smpl_log->search50($op['order']['num']); 
		if (!$logs){
			$op['msg'] = $smpl_log->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$op['logs'] = $logs['log'];
		$op['log_records'] = $logs['records'];
		// �N�|�ǥX op['logs'] ��log����� �� $op['log_records']=1 �ӧP�_���L���
		//--------------------------------
		$parm = $op['order'];

		// �[�J��^�e�� --------------
// 09/27 adding ----------------------
		$back_str = "index2.php?PHP_action=smpl_output_search&PHP_sr_startno=".$cgino."&PHP_dept_code=".$cgi_1."&PHP_num=".$cgi_2."&PHP_cust=".$cgi_3."&PHP_ref=".$cgi_4."&PHP_factory=".$cgi_5."&SCH_ptno=".$SCH_ptno."&SCH_ptdu=".$SCH_ptdu."&SCH_usmp=".$SCH_usmp."&SCH_smpdu=".$SCH_smpdu."&SCH_qty=".$SCH_qty."&SCH_ie=".$SCH_ie."&SCH_mk=".$SCH_ie."&SCH_wi=".$SCH_wi."&SCH_close=".$SCH_close."&SCH_pend=".$SCH_pend."&SCH_pttnd=".$SCH_pttnd."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_op_str=".$PHP_op_str."&PHP_op_fsh=".$PHP_op_fsh;
		$op['sub_back_str'] = "&SCH_ptno=".$SCH_ptno."&SCH_ptdu=".$SCH_ptdu."&SCH_usmp=".$SCH_usmp."&SCH_smpdu=".$SCH_smpdu."&SCH_qty=".$SCH_qty."&SCH_ie=".$SCH_ie."&SCH_mk=".$SCH_ie."&SCH_wi=".$SCH_wi."&SCH_close=".$SCH_close."&SCH_pend=".$SCH_pend."&SCH_pttnd=".$SCH_pttnd."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_op_str=".$PHP_op_str."&PHP_op_fsh=".$PHP_op_fsh;
		
		$back_str2 = "PHP_action=smpl_ord_view&PHP_id=".$PHP_id."&PHP_num=".$parm['num'];

		$op['back_str2'] = $back_str2;
		$op['back_str'] = $back_str;
		$op['cgi']= array( 'dept' 		=>	$cgi_1,
												'num'			=>	$cgi_2,
												'cust'		=>	$cgi_3,
												'ref'			=>	$cgi_4,
												'factory'	=>	$cgi_5
										 );	
		$op['cgistr_get'] = $cgiget;
		$op['start_no'] = $cgino;
		$message = " close-case of #".$PHP_num;
		# �O���ϥΪ̰ʺA
		$log->log_add(0,"24E",$message);

/*
		// �[�J cgi �Ѽ� -------------
		$op['cgistr_get'] = $cgiget;
		$op['start_no'] = $cgino;
		$op['cgi']['dept'] = $cgi_1;
		$op['cgi']['num'] = $cgi_2;
		$op['cgi']['cust'] = $cgi_3;
		$op['cgi']['ref'] = $cgi_4;
		$op['cgi']['factory'] = $cgi_5;
*/
		$op['msg'][] = $message;

		$op['search'] = '1';
		
		// �ˬd �ۤ��O�_�s�b 2007.03.23
		if(file_exists($GLOBALS['config']['root_dir']."/smpl_pic/".$op['order']['num'].".jpg")){
			$op['main_pic'] = "./smpl_pic/".$op['order']['num'].".jpg";
		} else {
			$op['main_pic'] = "./images/graydot.gif";
		}

		// creat LOG SUBJ combo box
		$op['logs_subj'] = $arry2->select($SMPL_LOG_SUBJ,'','PHP_subj','select','');  	

		$img_size = GetImageSize($op['main_pic']);
		if ($img_size[0] < $img_size[1]) $op['height'] = 1;


		page_display($op, 2, 9, $TPL_SMPL_OUTPUT_SHOW);			
		break;

	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		job 29E pttn_resend   ���s�ǰe pattern
#		case "pttn_resend":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "pttn_resend":

		check_authority(2,9,"edit");

		$today = $GLOBALS['TODAY'];
		$parm = array(	"id"			=>  $PHP_id,
				"done_pttn"		=>  '0000-00-00',
				"last_update"	=>  $today,
				"updator"		=>  $GLOBALS['SCACHE']['ADMIN']['login_id'],
		);
		$P = $smpl_ord->pttn_resend($parm);  // ���s�e��
		if (!$P) {
			$op['msg'] = $smpl_ord->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
//----------------------------------------------------
		$op['order'] = $smpl_ord->get($PHP_id);  //���X�ӵ��O��
		if (!$op['order']) {
			$op['msg'] = $smpl_ord->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

	//----- �p�� �q�� IE(SPT) �P�W�@��(�P�q��)��IE(SPT)���t���� $diff
		$last_v = substr($op['order']['num'],10)-1;
		$last = substr($op['order']['num'],0,10).$last_v;
	
		if($last_v ==0){
			$op['diff'] = 100;
		}else{
			if (!$last_spt = $smpl_ord->get_fieldvalue('','spt', $last)) {
				$op['msg'] = $smpl_ord->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			if($last_spt['spt']){
				$op['diff'] = ($op['order']['spt']/$last_spt['spt']) * 100;
			}else{
				$op['diff'] = 100;
			}
		}
	//------------------- end of SPT treating -------------------

		// ���X smpl_log ���q�� �����v50�����p�O��  ----------------
		$logs = $smpl_log->search50($op['order']['num']);
		if (!$logs){
			$op['msg'] = $smpl_log->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$op['logs'] = $logs['log'];
		$op['log_records'] = $logs['records'];

		// �N�|�ǥX op['logs'] ��log����� �� $op['log_records']=1 �ӧP�_���L���
		//--------------------------------
		$parm = $op['order'];

		// �[�J��^�e�� --------------
// 09/27 adding ----------------------
		$back_str = "index2.php?PHP_action=smpl_output_search&PHP_sr_startno=".$cgino."&PHP_dept_code=".$cgi_1."&PHP_num=".$cgi_2."&PHP_cust=".$cgi_3."&PHP_ref=".$cgi_4."&PHP_factory=".$cgi_5."&SCH_ptno=".$SCH_ptno."&SCH_ptdu=".$SCH_ptdu."&SCH_usmp=".$SCH_usmp."&SCH_smpdu=".$SCH_smpdu."&SCH_qty=".$SCH_qty."&SCH_ie=".$SCH_ie."&SCH_mk=".$SCH_ie."&SCH_wi=".$SCH_wi."&SCH_close=".$SCH_close."&SCH_pend=".$SCH_pend."&SCH_pttnd=".$SCH_pttnd."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_op_str=".$PHP_op_str."&PHP_op_fsh=".$PHP_op_fsh;
		$op['sub_back_str'] = "&SCH_ptno=".$SCH_ptno."&SCH_ptdu=".$SCH_ptdu."&SCH_usmp=".$SCH_usmp."&SCH_smpdu=".$SCH_smpdu."&SCH_qty=".$SCH_qty."&SCH_ie=".$SCH_ie."&SCH_mk=".$SCH_ie."&SCH_wi=".$SCH_wi."&SCH_close=".$SCH_close."&SCH_pend=".$SCH_pend."&SCH_pttnd=".$SCH_pttnd."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_op_str=".$PHP_op_str."&PHP_op_fsh=".$PHP_op_fsh;

		$back_str2 = "PHP_action=smpl_ord_view&PHP_id=".$PHP_id."&PHP_num=".$parm['num'];

		$op['back_str2'] = $back_str2;
		$op['back_str'] = $back_str;
		$op['cgi']= array( 'dept' 		=>	$cgi_1,
												'num'			=>	$cgi_2,
												'cust'		=>	$cgi_3,
												'ref'			=>	$cgi_4,
												'factory'	=>	$cgi_5
										 );	
		$op['cgistr_get'] = $cgiget;
		$op['start_no'] = $cgino;
		$message = "DEL exist pattern for resend for #".$PHP_num;
				# �O���ϥΪ̰ʺA
		$log->log_add(0,"24E",$message);

		$op['msg'][] = $message;

		$op['search'] = '1';
				// �ˬd �ۤ��O�_�s�b 2007.03.23
		if(file_exists($GLOBALS['config']['root_dir']."/smpl_pic/".$op['order']['num'].".jpg")){
			$op['main_pic'] = "./smpl_pic/".$op['order']['num'].".jpg";
		} else {
			$op['main_pic'] = "./images/graydot.gif";
		}
		
		// creat LOG SUBJ combo box
		$op['logs_subj'] = $arry2->select($SMPL_LOG_SUBJ,'','PHP_subj','select','');  	

		$img_size = GetImageSize($op['main_pic']);
		if ($img_size[0] < $img_size[1]) $op['height'] = 1;

		
		page_display($op, 2, 9, $TPL_SMPL_OUTPUT_SHOW);			
		break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		job 29E marker_resend   ���s�ǰe MARKER 
#		case "marker_resend":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "marker_resend":

		check_authority(2,9,"edit");

		$today = $GLOBALS['TODAY'];
		$parm = array(	"id"			=>  $PHP_id,
				"marker_date"	=>  '0000-00-00',
				"last_update"	=>  $today,
				"updator"		=>  $GLOBALS['SCACHE']['ADMIN']['login_id'],
		);
		$P = $smpl_ord->marker_resend($parm);  // ���s�eMARK
		if (!$P) {
			$op['msg'] = $smpl_ord->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
//----------------------------------------------------
		$op['order'] = $smpl_ord->get($PHP_id);  //���X�ӵ��O��
		if (!$op['order']) {
			$op['msg'] = $smpl_ord->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

	//----- �p�� �q�� IE(SPT) �P�W�@��(�P�q��)��IE(SPT)���t���� $diff
		$last_v = substr($op['order']['num'],10)-1;
		$last = substr($op['order']['num'],0,10).$last_v;
	
		if($last_v ==0){
			$op['diff'] = 100;
		}else{
			if (!$last_spt = $smpl_ord->get_fieldvalue('','spt', $last)) {
				$op['msg'] = $smpl_ord->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			if($last_spt['spt']){
				$op['diff'] = ($op['order']['spt']/$last_spt['spt']) * 100;
			}else{
				$op['diff'] = 100;
			}
		}
	//---------------------- end of SPT treating ----------------------

		// ���X smpl_log ���q�� �����v50�����p�O��  -------------------
		$logs = $smpl_log->search50($op['order']['num']);
		if (!$logs){
			$op['msg'] = $smpl_log->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$op['logs'] = $logs['log'];
		$op['log_records'] = $logs['records'];

		// �N�|�ǥX op['logs'] ��log����� �� $op['log_records']=1 �ӧP�_���L���
		//--------------------------------
		$parm = $op['order'];

		// �[�J��^�e�� --------------
// 09/27 adding ----------------------
		$back_str = "index2.php?PHP_action=smpl_output_search&PHP_sr_startno=".$cgino."&PHP_dept_code=".$cgi_1."&PHP_num=".$cgi_2."&PHP_cust=".$cgi_3."&PHP_ref=".$cgi_4."&PHP_factory=".$cgi_5."&SCH_ptno=".$SCH_ptno."&SCH_ptdu=".$SCH_ptdu."&SCH_usmp=".$SCH_usmp."&SCH_smpdu=".$SCH_smpdu."&SCH_qty=".$SCH_qty."&SCH_ie=".$SCH_ie."&SCH_mk=".$SCH_ie."&SCH_wi=".$SCH_wi."&SCH_close=".$SCH_close."&SCH_pend=".$SCH_pend."&SCH_pttnd=".$SCH_pttnd."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_op_str=".$PHP_op_str."&PHP_op_fsh=".$PHP_op_fsh;
		$op['sub_back_str'] = "&SCH_ptno=".$SCH_ptno."&SCH_ptdu=".$SCH_ptdu."&SCH_usmp=".$SCH_usmp."&SCH_smpdu=".$SCH_smpdu."&SCH_qty=".$SCH_qty."&SCH_ie=".$SCH_ie."&SCH_mk=".$SCH_ie."&SCH_wi=".$SCH_wi."&SCH_close=".$SCH_close."&SCH_pend=".$SCH_pend."&SCH_pttnd=".$SCH_pttnd."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_op_str=".$PHP_op_str."&PHP_op_fsh=".$PHP_op_fsh;

		$back_str2 = "PHP_action=smpl_ord_view&PHP_id=".$PHP_id."&PHP_num=".$parm['num'];

		$op['back_str2'] = $back_str2;
		$op['back_str'] = $back_str;
		$op['cgi']= array( 'dept' 		=>	$cgi_1,
												'num'			=>	$cgi_2,
												'cust'		=>	$cgi_3,
												'ref'			=>	$cgi_4,
												'factory'	=>	$cgi_5
										 );	
		$op['cgistr_get'] = $cgiget;
		$op['start_no'] = $cgino;
				# �O���ϥΪ̰ʺA
		$message = "DEL exist MARKER for resend for #".$PHP_num;
		$log->log_add(0,"24E",$message);

		$op['msg'][] = $message;

		$op['search'] = '1';
				// �ˬd �ۤ��O�_�s�b 2007.03.23
		if(file_exists($GLOBALS['config']['root_dir']."/smpl_pic/".$op['order']['num'].".jpg")){
			$op['main_pic'] = "./smpl_pic/".$op['order']['num'].".jpg";
		} else {
			$op['main_pic'] = "./images/graydot.gif";
		}
		
		// creat LOG SUBJ combo box
		$op['logs_subj'] = $arry2->select($SMPL_LOG_SUBJ,'','PHP_subj','select','');  	

		$img_size = GetImageSize($op['main_pic']);
		if ($img_size[0] < $img_size[1]) $op['height'] = 1;


		page_display($op, 2, 9, $TPL_SMPL_OUTPUT_SHOW);			
		break;


	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		job 29A ADD SMPL SPT    ��J SPT ( IE) 
//		case "smpl_SPT":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "smpl_spt":

		check_authority(2,9,"add");

		$today = $GLOBALS['TODAY'];

		$PHP_IE = number_format(($PHP_spt/$GLOBALS['IE_TIME']),2,'.','');
			
		$parm = array(	"id"	=>  $PHP_id,
				"spt"			=>  $PHP_spt,
				"ie"			=>  $PHP_IE,
				"last_update"	=>  $today,
				"updator"		=>  $GLOBALS['SCACHE']['ADMIN']['login_id'],
		);

		$P = $smpl_ord->add_spt($parm);  // �[�J spt
		if (!$P) {
			$op['msg'] = $smpl_ord->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
//----------------------------------------------------
		$op['order'] = $smpl_ord->get($PHP_id);  //���X�ӵ��O��
		if (!$op['order']) {
			$op['msg'] = $smpl_ord->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

	//----- �p�� �q�� IE(SPT) �P�W�@��(�P�q��)��IE(SPT)���t���� $diff
		$last_v = substr($op['order']['num'],10)-1;
		$last = substr($op['order']['num'],0,10).$last_v;
	
		if($last_v ==0){
			$op['diff'] = 100;
		}else{
			if (!$last_spt = $smpl_ord->get_fieldvalue('','spt', $last)) {
				$op['msg'] = $smpl_ord->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			if($last_spt['spt']){
				$op['diff'] = ($op['order']['spt']/$last_spt['spt']) * 100;
			}else{
				$op['diff'] = 100;
			}
		}
	//---------------------- end of SPT treating -----------------------------

		// ���X smpl_log ���q�� �����v50�����p�O��  -------------------
		$logs = $smpl_log->search50($op['order']['num']); 
		if (!$logs){
			$op['msg'] = $smpl_log->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$op['logs'] = $logs['log'];
		$op['log_records'] = $logs['records'];

		// �N�|�ǥX op['logs'] ��log����� �� $op['log_records']=1 �ӧP�_���L���
		//--------------------------------
		$parm = $op['order'];

		// �[�J��^�e�� --------------
// 09/27 adding ----------------------
		$back_str = $cgiget."&PHP_sr_startno=".$cgino."&PHP_dept_code=".$cgi_1."&PHP_num=".$cgi_2."&PHP_cust=".$cgi_3."&PHP_ref=".$cgi_4."&PHP_factory=".$cgi_5."&SCH_ptno=".$SCH_ptno."&SCH_ptdu=".$SCH_ptdu."&SCH_usmp=".$SCH_usmp."&SCH_smpdu=".$SCH_smpdu."&SCH_qty=".$SCH_qty."&SCH_ie=".$SCH_ie."&SCH_mk=".$SCH_ie."&SCH_wi=".$SCH_wi."&SCH_close=".$SCH_close."&SCH_pend=".$SCH_pend."&SCH_pttnd=".$SCH_pttnd."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_op_str=".$PHP_op_str."&PHP_op_fsh=".$PHP_op_fsh;
		$op['sub_back_str'] = "&SCH_ptno=".$SCH_ptno."&SCH_ptdu=".$SCH_ptdu."&SCH_usmp=".$SCH_usmp."&SCH_smpdu=".$SCH_smpdu."&SCH_qty=".$SCH_qty."&SCH_ie=".$SCH_ie."&SCH_mk=".$SCH_ie."&SCH_wi=".$SCH_wi."&SCH_close=".$SCH_close."&SCH_pend=".$SCH_pend."&SCH_pttnd=".$SCH_pttnd."&SCH_fab=".$SCH_fab."&SCH_acc=".$SCH_acc."&PHP_op_str=".$PHP_op_str."&PHP_op_fsh=".$PHP_op_fsh;

		$back_str2 = "PHP_action=smpl_ord_view&PHP_id=".$PHP_id."&PHP_num=".$parm['num'];

		$op['back_str2'] = $back_str2;
		$op['back_str'] = $back_str;
		$op['cgi']= array( 'dept' 		=>	$cgi_1,
												'num'			=>	$cgi_2,
												'cust'		=>	$cgi_3,
												'ref'			=>	$cgi_4,
												'factory'	=>	$cgi_5
										 );	
		$op['cgistr_get'] = $cgiget;
		$op['start_no'] = $cgino;
				# �O���ϥΪ̰ʺA
		$message = " Add SPT :".$PHP_spt." for #".$PHP_num;
		$log->log_add(0,"24A",$message);

		$op['msg'][] = $message;

		$op['search'] = '1';
				// �ˬd �ۤ��O�_�s�b 2007.03.23
		if(file_exists($GLOBALS['config']['root_dir']."/smpl_pic/".$op['order']['num'].".jpg")){
			$op['main_pic'] = "./smpl_pic/".$op['order']['num'].".jpg";
		} else {
			$op['main_pic'] = "./images/graydot.gif";
		}
		
		// creat LOG SUBJ combo box
		$op['logs_subj'] = $arry2->select($SMPL_LOG_SUBJ,'','PHP_subj','select','');  	

		$img_size = GetImageSize($op['main_pic']);
		if ($img_size[0] < $img_size[1]) $op['height'] = 1;


		page_display($op, 2, 9, $TPL_SMPL_OUTPUT_SHOW);			
		break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		job 21A sample oreder apprval
#		case "smpl_appval":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "smpl_appval":		
		check_authority(2,1,"add");
	
		// ��Ʈw�n�ק諸�Ѽ� ----
		$PHP_smpl_ord=substr($PHP_num,0,9);
		$smpl=$smpl_ord->get(0,0,$PHP_smpl_ord);		
		$parm['num']		= $PHP_smpl_ord;
		$parm['field_name'] = 'apv_date';
		$parm['field_value']= $TODAY;
				
//*****//2006.12.19�W�[order��smpl_approval start
		$U = $order->update_field_like("smpl_apv", $TODAY, 'smpl_ord', $PHP_smpl_ord);
		$U = $order->update_field_like("ie_time2", $smpl['spt'], 'smpl_ord', $PHP_smpl_ord);

//*****//2006.12.19�W�[order��smpl_approval end
		
		if (!$U = $smpl_ord->update_apv($parm)){
			$op['msg'] = $smpl_ord->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		
		
		

		$message = " Sample APPROVAL for :#".$PHP_smpl_ord;
		# �O���ϥΪ̰ʺA
		$log->log_add(0,"21A",$message);
		
		// �e�X�Ѽ� ----- redir page
		$cgi = '&cgino='.$cgino.'&cgi_1='.$cgi_1.'&cgi_2='.$cgi_2.'&cgi_3='.$cgi_3.'&cgi_4='.$cgi_4.'&cgi_5='.$cgi_5."&cgi_6=".$cgi_6."&cgi_7=".$cgi_7."&cgi_8=".$cgi_8."&cgi_9=".$cgi_9."&cgi_10=".$cgi_10;
		
		$redir_str = 'index2.php?PHP_action=smpl_ord_view&PHP_id='.$PHP_id.'&cgiget='.$PHP_back.$cgi;
		redirect_page($redir_str);
		break;

		

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "smpl_fa":	job 22 sample���D�Ʈƥ[�J
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "smpl_fa":

		check_authority(2,2,"view");

		$where_str = $manager = $dept_id = '';
		$dept_ary = array();

		$sales_dept_ary = get_sales_dept(); //  [���tK0] ------
		
		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept']; 
		$user_team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
		
		for ($i=0; $i<count($sales_dept_ary);$i++){
			if($user_dept == $sales_dept_ary[$i]){
				// �p�G�O�~�ȳ��i�J �hdept_code ���w�ӷ~�ȳ�---
				$dept_id = $sales_dept_ary[$i];  
			}
		}
		if (!$dept_id || $user_team<>'MD') {    // ���O�~�ȳ��H[�]���t K0 ���H ]�i�J��
			$manager = 1;
		} else {
			$where_str = " WHERE dept = '".$dept_id."' ";
		}
		
		
		
		$op['manager_flag'] = $manager;
		$op['dept_id'] = $dept_id;
		
		// creat cust combo box	 ���X �Ȥ�N��
		$where_str=$where_str."order by cust_s_name"; //��cust_s_name�Ƨ�
		if(!$cust_def = $cust->get_fields('cust_init_name',$where_str)){;  //���X�Ȥ�²��
			$op['msg'][] = "sorry! there is no any customer record in your team, please add customer first!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//���X�Ȥ�N��
		for ($i=0; $i< sizeof($cust_def); $i++)
		{
			$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//�H [�Ȥ�N��-�Ȥ�²��] �e�{
		}
		$op['cust_select'] =  $arry2->select($cust_value,'','PHP_cust','select','',$cust_def_vue); 

		// ------ �M�w factory ���Ѽ� = �}�C? �ƭ�?  RD�H���i�J�ɦ��t�O ----
		if($S = $smpl_ord->is_sample_team($user_team)){
			$op['factory'] = $user_team."<input type='hidden' name='PHP_factory' value='".$user_team."'>";
		}else{
			$op['factory'] = $arry2->select($SAMPLER,'','PHP_factory','select','');  	
		}
		if($user_team=="RD"){
			$op['factory'] = $user_dept."<input type='hidden' name='PHP_factory' value='".$user_dept."'>";
		}
		// --------------------------------------------------------------------

		page_display($op, 2, 2, $TPL_SMPL_FA);			
		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "smpl_fa_search":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "smpl_fa_search":

		check_authority(2,2,"view");

		$parm = array(	"dept"		=>  $PHP_dept_code,
						"num"		=>  $PHP_num,
						"cust"		=>	$PHP_cust,
						"ref"		=>	$PHP_ref,
						"factory"	=>	$PHP_factory,
						"etdstr"	=>	$PHP_etdstr,
						"etdfsh"	=>	$PHP_etdfsh
				);

		// ---- �Q��PHP_dept_code�P�w�O�_ �~�ȳ����i�J
		if (!$op = $smpl_ord->fa_search(1, $PHP_dept_code)) {  
			$op['msg']= $smpl_ord->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		// �ˬd �ۤ��O�_�s�b 2007.03.23		
	for ($i=0; $i < sizeof($op['sorder']); $i++)
	{
		if(file_exists($GLOBALS['config']['root_dir']."/smpl_pic/s_".$op['sorder'][$i]['num'].".jpg")){
			$op['sorder'][$i]['main_pic'] = "./smpl_pic/s_".$op['sorder'][$i]['num'].".jpg";
		} else {
			$op['sorder'][$i]['main_pic'] = "./images/graydot.gif";
		}
		$img_size = GetImageSize($op['sorder'][$i]['main_pic']);
		if ($img_size[0] < $img_size[1]) $op['sorder'][$i]['height'] = 1;
	
	
	}
		// ---- �p�G ���O �e��~�ȳ����i�J��...
		if (!$PHP_dept_code) {   
			$op['manager_flag'] = 1;
		}

		$op['dept_id'] = $PHP_dept_code;
		$back_str = "&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_ref=".$PHP_ref."&PHP_factory=".$PHP_factory."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh;
		$op['back_str'] = $back_str;
		$op['msg']= $smpl_ord->msg->get(2);
		$op['cgi']= $parm;

		page_display($op, 2, 2, $TPL_SMPL_FA_LIST);			
		break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		job 21V sample oreder view
#		case "smpl_ord_view":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "smpl_fa_view":

		check_authority(2,2,"view");

		$op['order'] = $smpl_ord->get($PHP_id);  //���X�ӵ��O��
		if (!$op['order']) {
			$op['msg'] = $order->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		
		//size��scale�ܦ�id���x�s
		$size_data=$size_des->get($op['order']['size']);
		$op['order']['size_id']=$op['order']['size'];		
		$op['order']['size']=$size_data['size_scale'];
		
				// �ˬd �ۤ��O�_�s�b 2007.03.23
		if(file_exists($GLOBALS['config']['root_dir']."/smpl_pic/".$op['order']['num'].".jpg")){
			$op['main_pic'] = "./smpl_pic/".$op['order']['num'].".jpg";
		} else {
			$op['main_pic'] = "./images/graydot.gif";
		}
		$img_size = GetImageSize($op['main_pic']);
		if ($img_size[0] < $img_size[1]) $op['height'] = 1;


//		$op['back_str'] = $PHP_back_str;
		
		// ���X smpl_log ���q�� �����v50�����p�O��  -------------------
		$logs = $smpl_log->search50($op['order']['num']);  //���X�ӭq�����log�O��
		if (!$logs){
			$op['msg'] = $smpl_log->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		//�D�Ʈƨ��o-------------------------------------------------------------
		$where_str="where smpl_code='".$op['order']['num']."'";
		$op['fab'] = $smplord_lots->search(0,$where_str);  //���X�ӵ��D�ưO��			
		if (!isset($op['fab'][0])) {
			$op['fab']['msg']="No  Fabric record";
		}
		
		$op['acc'] = $smplord_acc->search(0,$where_str);  //���X�ӵ��ƮưO��		
		if (!isset($op['acc'][0])) {
			$op['acc']['msg']="No Accessory record";
		}

		
		

		$op['logs'] = $logs['log'];
		$op['log_records'] = $logs['records'];
		// �N�|�ǥX op['logs'] ��log����� �� $op['log_records']=1 �ӧP�_���L���
		//--------------------------------
		
		$parm = $op['order'];

		// �[�J��^�e�� --------------
		$back_str = "&PHP_sr_startno=".$cgino."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_ref=".$PHP_ref."&PHP_factory=".$PHP_factory."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh;
		$op['back_str'] = $back_str;

		$op['msg'] = $smpl_ord->msg->get(2);
		$op['search'] = '1';
		$op['now_pp']=$cgino;
		page_display($op, 2, 2, $TPL_SMPL_FA_SHOW);			
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "smpl_fa_edit":	
		check_authority(2,2,"edit");	
		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];	
		$op['order'] = $smpl_ord->get($PHP_id);  //���X�ӵ��O��
		if (!$op['order']) {
			$op['msg'] = $order->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
				//size��scale�ܦ�id���x�s
		$size_data=$size_des->get($op['order']['size']);
		$op['order']['size_id']=$op['order']['size'];		
		$op['order']['size']=$size_data['size_scale'];
		
		//�˵��O�_�i�H�ק�size(�Y�O�_�s�bcolorway)
		if ($smpl_wi->check_size($op['order']['num']))
		{
			$op['size_edit']=1;
		}
//		$op['size_edit']=1;
//�D�Ʈƨ��o-------------------------------------------------------------
		$where_str="where smpl_code='".$op['order']['num']."'";
		$op['fab'] = $smplord_lots->search(0,$where_str);  //���X�ӵ��D�ưO��			
		if (!isset($op['fab'][0])) {
			$op['fab']['msg']="No  Fabric record";
		}else{
			for ($i=0; $i<sizeof($op['fab']); $i++)
				$op['fab'][$i]['unit_select'] = $arry2->select($LOTS_PRICE_UNIT,$op['fab'][$i]['unit'],'PHP_unit','select','');
		}
		
		$op['acc'] = $smplord_acc->search(0,$where_str);  //���X�ӵ��ƮưO��		
		if (!isset($op['acc'][0])) {
			$op['acc']['msg']="No Accessory record";
		}else{
			for ($i=0; $i<sizeof($op['acc']); $i++)
				$op['acc'][$i]['unit_select'] = $arry2->select($ACC_PRICE_UNIT,$op['acc'][$i]['unit'],'PHP_unit','select','');
		}

		$op['msg'] = $smpl_ord->msg->get(2);
		
//*****//2007.03.02 �bsample���[�Jsize���U�Ԧ� start
		$where_str =" WHERE dept ='".$op['order']['dept']."' and cust='".$op['order']['cust']."' ";
		$size_def = $size_des->get_fields('size_scale',$where_str);   // ���X �ئT
		if (!isset($size_def[0]))$size_def[0]="";
		$op['size_select'] =  $arry2->select($size_def,$op['order']['size'],'PHP_size','select','');
		$op['use_select']	=	$arry2->select($USAGE_METHOD,'','PHP_use_method','select','');
		$op['fab_unit']	=	$arry2->select($LOTS_PRICE_UNIT,'','PHP_unit','select','');
		$op['acc_unit']	=	$arry2->select($ACC_PRICE_UNIT,'','PHP_unit','select','');
		$op['acc_name'] = $arry2->select($ACC,'','PHP_name','select','');

//*****//2007.03.02 �bsample���[�Jsize���U�Ԧ� end

		if (isset($PHP_msg))	$op['msg'][]=$PHP_msg;	//�O���ɤJ��message
		
		$op['search'] = '1';
				// �[�J��^�e�� 2005/05/05
		if (isset($PHP_back_str))
		{
			$op['back_str'] = $PHP_back_str;
		}else{
			$back_str = "&PHP_sr_startno=".$cgino."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_ref=".$PHP_ref."&PHP_factory=".$PHP_factory."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh;
			$op['back_str'] = $back_str;
		}
		$op['now_pp']=$cgino;
		page_display($op, 2, 2, $TPL_SMPL_FA_EDIT);		
		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_smpl_fa_update":	
		check_authority(2,2,"edit");	
		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
		if (!$PHP_unit) $PHP_unit=$PHP_old_unit;

		if ($PHP_item=='fab')
		{
			$parm=array($PHP_item_id,'unit',$PHP_unit);
			$smplord_lots->update_field($parm);
			
			$parm=array($PHP_item_id,'use_for',$PHP_use_for);
			$smplord_lots->update_field($parm);
		}
		
		if ($PHP_item=='acc')
		{
			$parm=array($PHP_item_id,'unit',$PHP_unit);
			$smplord_acc->update_field($parm);
			
			$parm=array($PHP_item_id,'use_for',$PHP_use_for);
			$smplord_acc->update_field($parm);
			
			if (!isset($PHP_main))$PHP_main=0;
			$parm=array($PHP_item_id,'acc_cat',$PHP_main);
			$smplord_acc->update_field($parm);
		}
			
		$op['order'] = $smpl_ord->get($PHP_id);  //���X�ӵ��O��
		if (!$op['order']) {
			$op['msg'] = $order->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
				//size��scale�ܦ�id���x�s
		$size_data=$size_des->get($op['order']['size']);
		$op['order']['size_id']=$op['order']['size'];		
		$op['order']['size']=$size_data['size_scale'];
		
		//�˵��O�_�i�H�ק�size(�Y�O�_�s�bcolorway)
		if ($smpl_wi->check_size($op['order']['num']))
		{
			$op['size_edit']=1;
		}
		$op['size_edit']=1;
		$op['back_str'] = $PHP_back_str;
		$op['now_pp']=$cgino;
//�D�Ʈƨ��o-------------------------------------------------------------
		$where_str="where smpl_code='".$op['order']['num']."'";
		$op['fab'] = $smplord_lots->search(0,$where_str);  //���X�ӵ��D�ưO��			
		if (!isset($op['fab'][0])) {
			$op['fab']['msg']="No  Fabric record";
		}else{
			for ($i=0; $i<sizeof($op['fab']); $i++)
				$op['fab'][$i]['unit_select'] = $arry2->select($LOTS_PRICE_UNIT,$op['fab'][$i]['unit'],'PHP_unit','select','');
		}
		
		$op['acc'] = $smplord_acc->search(0,$where_str);  //���X�ӵ��ƮưO��		
		if (!isset($op['acc'][0])) {
			$op['acc']['msg']="No Accessory record";
		}else{
			for ($i=0; $i<sizeof($op['acc']); $i++)
				$op['acc'][$i]['unit_select'] = $arry2->select($ACC_PRICE_UNIT,$op['acc'][$i]['unit'],'PHP_unit','select','');
		}

		$op['msg'] = $smpl_ord->msg->get(2);
		
//*****//2007.03.02 �bsample���[�Jsize���U�Ԧ� start
		$where_str =" WHERE dept ='".$op['order']['dept']."' and cust='".$op['order']['cust']."' ";
		$size_def = $size_des->get_fields('size_scale',$where_str);   // ���X �ئT
		if (!isset($size_def[0]))$size_def[0]="";
		$op['size_select'] =  $arry2->select($size_def,$op['order']['size'],'PHP_size','select','');
		$op['use_select']	=	$arry2->select($USAGE_METHOD,'','PHP_use_method','select','');
		$op['fab_unit']	=	$arry2->select($LOTS_PRICE_UNIT,'','PHP_unit','select','');
		$op['acc_unit']	=	$arry2->select($ACC_PRICE_UNIT,'','PHP_unit','select','');
		$op['acc_name'] = $arry2->select($ACC,'','PHP_name','select','');

//*****//2007.03.02 �bsample���[�Jsize���U�Ԧ� end

		$message="success update sample order:".$op['order']['num'].$PHP_item.":".$PHP_item_name;
		$op['msg'][]=$message;
		$log->log_add(0,"22A",$message);
		$op['search'] = '1';
				// �[�J��^�e�� 2005/05/05
		$op['back_str'] = $PHP_back_str;
		page_display($op, 2, 2, $TPL_SMPL_FA_EDIT);		
		break;
		
		
//=======================================================
    case "do_smpl_fa_edit":
		check_authority(2,2,"edit");
		$user = $GLOBALS['SCACHE']['ADMIN']['name'];   // �P�w�i�J����������
		$date = date('Y-m-d');
		if (!isset($PHP_main))$PHP_main=0;
		
		$parm = array(	"name"				=>	$PHP_name,
										"num"					=>	$PHP_smpl_num,
										"smpl_code"		=>	$PHP_smpl_code,
										"unit"				=>	$PHP_unit,
										"est"					=>	$PHP_est,
										"use_method"	=>	'',
										"use_for"			=>	$PHP_use_for,
										"user"				=>	$user,
										"acc_cat"			=>	$PHP_main,
										"add_date"		=>	$date,
										"select"			=>	1
			);
			if ($PHP_name = 'hook' && isset($bar)) $parm['name'] = "hook&bar";
		if ($PHP_item=='fab')	$f1 = $smplord_lots->add($parm);
		if ($PHP_item=='acc')	$f1 = $smplord_acc->add($parm);
		if ($f1)
		{
			if ($PHP_item=='fab')	$message= "Sueess add fabric:".$parm['num']."on order:".$parm['smpl_code'];
			if ($PHP_item=='acc')	$message= "Sueess add accessory:".$parm['num']."on order:".$parm['smpl_code'];
					# �O���ϥΪ̰ʺA
			$log->log_add(0,"22A",$message);
			$op['fab_unit']	=	$arry2->select($LOTS_PRICE_UNIT,'','PHP_unit','select','');
			$op['acc_unit']	=	$arry2->select($ACC_PRICE_UNIT,'','PHP_unit','select','');
			$op['acc_name'] = $arry2->select($ACC,'','PHP_name','select','');
		}else{
			if ($PHP_item=='fab')	$msg=$smplord_lots->msg->get(2);
			if ($PHP_item=='acc')	$msg=$smplord_acc->msg->get(2);
			$message=$msg[0];
			if ($PHP_item=='fab')	$op['fab_un_add'] = $parm;
			if ($PHP_item=='acc')	$op['acc_un_add'] = $parm;
			$op['fab_unit']	=	$arry2->select($LOTS_PRICE_UNIT,$PHP_unit,'PHP_unit','select','');
			$op['acc_unit']	=	$arry2->select($ACC_PRICE_UNIT,$PHP_unit,'PHP_unit','select','');
			$op['acc_name'] = $arry2->select($ACC,$PHP_name,'PHP_name','select','');
		}
		
		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];	
		$op['order'] = $smpl_ord->get($PHP_id);  //���X�ӵ��O��
		if (!$op['order']) {
			$op['msg'] = $order->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
						//size��scale�ܦ�id���x�s
		$size_data=$size_des->get($op['order']['size']);
		$op['order']['size_id']=$op['order']['size'];		
		$op['order']['size']=$size_data['size_scale'];
		
		//�˵��O�_�i�H�ק�size(�Y�O�_�s�bcolorway)
		if ($smpl_wi->check_size($op['order']['num']))
		{
			$op['size_edit']=1;
		}
		if (isset($PHP_back_str))
		{
			$op['back_str'] = $PHP_back_str;
		}else{
			$back_str = "&PHP_sr_startno=".$PHP_sr_startno."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_ref=".$PHP_ref."&PHP_factory=".$PHP_factory."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh;
			$op['back_str'] = $back_str;
		}			
	$op['now_pp']=$PHP_sr_startno;
		
//�D�Ʈƨ��o-------------------------------------------------------------
		$where_str="where smpl_code='".$op['order']['num']."'";
		$op['fab'] = $smplord_lots->search(0,$where_str);  //���X�ӵ��D�ưO��			
		if (!isset($op['fab'][0])) {
			$op['fab']['msg']="No  Fabric record";
		}else{
			for ($i=0; $i<sizeof($op['fab']); $i++)
				$op['fab'][$i]['unit_select'] = $arry2->select($LOTS_PRICE_UNIT,$op['fab'][$i]['unit'],'PHP_unit','select','');
		}
		
		$op['acc'] = $smplord_acc->search(0,$where_str);  //���X�ӵ��ƮưO��		
		if (!isset($op['acc'][0])) {
			$op['acc']['msg']="No Accessory record";
		}else{
			for ($i=0; $i<sizeof($op['acc']); $i++)
				$op['acc'][$i]['unit_select'] = $arry2->select($ACC_PRICE_UNIT,$op['acc'][$i]['unit'],'PHP_unit','select','');
		}

		$op['msg'][] = $message;

		$op['use_select']	=	$arry2->select($USAGE_METHOD,'','PHP_use_method','select','');

		page_display($op, 2, 2, $TPL_SMPL_FA_EDIT);		
		break;

//=======================================================			
    case "smpl_fab_del_ajx":		
		check_authority(2,2,"edit");
		if ($PHP_item=='fab')	$f1 = $smplord_lots->del($PHP_lots_id);
		if ($PHP_item=='acc')	$f1	= $smplord_acc->del($PHP_lots_id);
		
		if ($f1)
		{
			if ($PHP_item=='fab')	$message= "Sueess del fabric:".$PHP_lots_code."on order:".$PHP_order;
			if ($PHP_item=='acc')	$message= "Sueess del accessory:".$PHP_lots_code."on order:".$PHP_order;
					# �O���ϥΪ̰ʺA
			$log->log_add(0,"22A",$message);
		}else{
			if ($PHP_item=='fab')	$msg=$smpl_lots->msg->get(2);
			if ($PHP_item=='acc')	$msg=$smpl_acc->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
	echo $message;
	break;
	
	exit;

//=======================================================

	case "do_smpl_fa_edit_all":
		check_authority(2,2,"edit");
		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];   // �P�w�i�J����������
		
		$op['order'] = $smpl_ord->get($PHP_id);  //���X�ӵ��O��
		if (!$op['order']) {
			$op['msg'] = $smpl_ord->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$size_id=$PHP_size_id;				
		if ($PHP_size_id==0)		//size_des�[�J�s��
		{
			$PHP_size_item = strtoupper($PHP_size_item);
			$PHP_size = strtoupper($PHP_size);		
			$parm=array(	'cust'			=>	$op['order']['cust'],
							'dept'			=>	$op['order']['dept'],
							'size'			=>	$PHP_size_item,
							'size_scale'	=>	$PHP_size
			);
			$size_id=$size_des->add($parm);
		}
		$parm = array(	'field_name'	=>	'size',
						'field_value'	=>	$size_id,
						'id'			=>	$PHP_id
		);
		$f1 = $smpl_ord->update_field($parm); 	//sample�[�J�ssize

		if ($f1)		//message
		{
			$message="successfully update sample order:".$op['order']['num'];
			$log->log_add(0,"22E",$message);
		}else{
			$msg[]=$smpl_ord->msg->get(2);
			$message=$msg[0];
		}
			
		//�ɦV��ܭ�
		$back_str = "index2.php?&PHP_action=smpl_fa_edit&cgino=".$PHP_sr_startno."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_ref=".$PHP_ref."&PHP_factory=".$PHP_factory."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh."&PHP_msg=".$message."&PHP_id=".$PHP_id;
	
		redirect_page($back_str);
		break;

//=======================================================

	case "do_smpl_fa_edit_ajx":
		check_authority(2,2,"edit");
		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];   // �P�w�i�J����������
		
		$op['order'] = $smpl_ord->get($PHP_id);  //���X�ӵ��O��

		$size_id=$PHP_size_id;				
		if ($PHP_size_id==0)		//size_des�[�J�s��
		{
			$PHP_size_item = strtoupper($PHP_size_item);
			$PHP_size = strtoupper($PHP_size);		
			$parm=array(	'cust'			=>	$op['order']['cust'],
							'dept'			=>	$op['order']['dept'],
							'size'			=>	$PHP_size_item,
							'size_scale'	=>	$PHP_size,
							'base_size'		=>	$PHP_base_size,
							'check'			=>	1,
			);
			$size_id=$size_des->add($parm);
		}
		$parm = array(	'field_name'	=>	'size',
						'field_value'	=>	$size_id,
						'id'			=>	$PHP_id
		);
		$f1 = $smpl_ord->update_field($parm); 	//sample�[�J�ssize

		if ($f1)		//message
		{
			$message="successfully update sample order:".$op['order']['num'];
			$log->log_add(0,"22E",$message);
		}else{
			$msg[]=$smpl_ord->msg->get(2);
			$message=$msg[0];
		}
			
		$return_var=$PHP_size."|".$message;
		echo $return_var;
		exit;
		break;
//-------------------------------------------------------------------------------------
//			 job 22  �s�@�O
//-------------------------------------------------------------------------------------
    case "smpl_wi":
    	check_authority(2,3,"view");
		//2006/05/02 update
		$where_str = $manager = $dept_id = '';		 
		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];   // �P�w�i�J����������
		$team = $GLOBALS['SCACHE']['ADMIN']['team_id'];  // �P�w�i�J����������(team)
		$sales_dept_ary = get_sales_dept();// ���X �~�Ȫ����� [���tK0] ------		
		for ($i=0; $i<count($sales_dept_ary);$i++){
			if($user_dept == $sales_dept_ary[$i]){
				// �p�G�O�~�ȳ��i�J �hdept_code ���w�ӷ~�ȳ�---
				$dept_id = $sales_dept_ary[$i]; 
				$op['dept_id'] = $dept_id;
			}
		}
		
		if (!$dept_id || $team<>'MD') {    // ���O�~�ȳ��H[�]���t K0 ���H ]�i�J��
			//�~�ȳ��� select���
			$op['dept_id'] = $arry2->select($sales_dept_ary,"","PHP_dept_code","select",""); 
 
		} else {
			$where_str = " WHERE dept = '".$dept_id."' ";						
		}

				
		// �p�G�O manager �i�J��...
		if (substr($op['dept_id'],0,7) == "<select"){
			$op['manager_flag'] = 1;
		}
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


		$op['msg'] = $wi->msg->get(2);
		// creat sample type combo box
		$sample_def = $smpl_type->get_fields('smpl_type');   // ���X �˥����O
		$op['smpl_type_select'] =  $arry2->select($sample_def,'','PHP_smpl_type','select','');  	
	
		page_display($op, 2, 3, $TPL_SMPL_WI_SEARCH);	    	    
		break;


//=======================================================
    case "smpl_wi_search":
		check_authority(2,3,"view");
		
		if (!$op = $smpl_ord->fa_search(2, $PHP_dept_code)) {  
			$op['msg']= $smpl_ord->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		
		for ($i=0; $i< sizeof($op['sorder']);$i++)
		{
			$tmp = substr($op['sorder'][$i]['num'],-1)-1;
			if ($tmp > 0)
			{
				$tmp1=substr($op['sorder'][$i]['num'],0,-1).$tmp;
				$op['sorder'][$i]['copy']=$tmp1;				
			}
			if(file_exists($GLOBALS['config']['root_dir']."/smpl_pic/".$op['sorder'][$i]['num'].".jpg")){
				$op['sorder'][$i]['main_pic'] = "./smpl_pic/".$op['sorder'][$i]['num'].".jpg";
			} else {
				$op['sorder'][$i]['main_pic'] = "./images/graydot.gif";
			}
			if ($op['sorder'][$i]['wi_id'])
			{
				$where_str = "WHERE wi_id = '".$op['sorder'][$i]['wi_id']."'";
				$op['sorder'][$i]['ti'] = $smpl_ti->get_fields('id',$where_str);
				$op['sorder'][$i]['bom_lots'] = $smpl_bom->get_fields_lots('id',$where_str);
				$op['sorder'][$i]['bom_acc'] =  $smpl_bom->get_fields_acc('id',$where_str);
			}
			
		}
		
		$op['dept_id'] = $PHP_dept_code;
		$back_str = "&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_ref=".$PHP_ref."&PHP_factory=".$PHP_factory."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh;
		$op['back_str'] = $back_str;
		$op['msg']= $smpl_ord->msg->get(2);


		//2006/05/02 update
		$op['dept_id'] = get_dept_id();
		
		// �p�G�O manager �i�J��...
		if (substr($op['dept_id'],0,7) == "<select"){
			$op['manager_flag'] = 1;
		}

			page_display($op, 2, 3, $TPL_SMPL_WI);
		break;

//=======================================================
    case "smpl_wi_add":

		check_authority(2,3,"add");

			$op['wi']['cust'] = $PHP_customer;
			if ($PHP_size > 0)
			{
				$size_scale=$size_des->get_fields('size_scale',"where id='$PHP_size'");				
				$PHP_size_scale=$size_scale[0];
			}
			// ���X�ﶵ��� �ζǤJ���Ѽ�
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

			// �ۤ���URL�M�w ----------------------------------------------------------------
				$style_dir	= "./smpl_pic/";  
				$no_img		= "./images/graydot.gif";
			if(file_exists($style_dir.$PHP_style_code.".jpg")){
				$op['re_adding'] = 1;
				$op['pic_link'] = $style_dir.$PHP_style_code.".jpg";
			} else {
				$op['pic_link']= $no_img;
			}


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

			page_display($op, 2, 3, $TPL_SMPL_WI_ADD);	    	    
		break;

//=======================================================
	case "do_smpl_wi_add_1":

		check_authority(2,3,"add");
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
				$parm=array(	'cust'			=>	$PHP_cust,
								'dept'			=>	$PHP_dept_code,
								'size'			=>	$PHP_Size_item,
								'size_scale'	=>	$PHP_Size,
								'base_size'		=>	$PHP_base_size,
								'check'			=>	1,
				);
				$size_id=$size_des->add($parm);				
			}			
			$siz_ary['field_name']='size';
			$siz_ary['field_value']=$size_id;
			$siz_ary['id']=$PHP_smpl_id;
			$f1 = $smpl_ord->update_field($siz_ary);
		}

		$parm = array(	"dept"			=>	$PHP_dept_code,
						"cust"			=>	$PHP_cust,
						"cust_ref"		=>	$PHP_cust_ref,
						"smpl_id"		=>	$PHP_smpl_id,

						"etd"			=>	$PHP_etd,						
						"unit"			=>	$PHP_unit,

						"size_scale"	=>	$size_id,	// ���s�J wi

						"style_code"	=>	$PHP_style_code,
						"creator"		=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
						"open_date"		=>	$dt['date_str'],
						"version"		=>	"1"
			);

		// check �g�J wi ��	���		
			$op['wi'] = $parm;
			
		if (!$f1 = $smpl_wi->check($parm)) {  // �S�����\��J��Ʈ�
			$op['msg'] = $smpl_wi->msg->get(2);
			// ��J �������˥��Ѽ� ----
			$op['smpl']['size_scale'] = $PHP_Size;
			$op['smpl']['size'] = $size_id;
			$op['smpl']['style_code'] = $parm['style_code'];
			$op['smpl']['cust_ref'] = $parm['cust_ref'];
			$op['smpl']['id'] = $parm['smpl_id'];
			$op['smpl']['unit'] = $parm['unit'];
			$op['smpl']['etd'] = $parm['etd'];
			// �]�w re_adding �б�---
			$op['re_adding'] = "1";
			// �ۤ���URL�M�w ----------------------------------------------------------------
				$style_dir	= "./smpl_pic/";  
				$no_img		= "./images/graydot.gif";
			if(file_exists($style_dir.$op['wi']['style_code'].".jpg")){
				$op['pic_link'] = $style_dir.$op['wi']['style_code'].".jpg";
			} else {
				$op['pic_link']= $no_img;
			}

			// �C�X �s�W �s�@�O ��J�˥������� ---------------------------------------------
					
				// ���X�~�X...
				$dt = decode_date(1);
				$year_code = substr($dt['year'],-1);
				// pre  �s�y�O�s��....
				$op['wi']['wi_precode'] = $PHP_style_code;
				// creat sample type combo box
				$sample_def = $smpl_type->get_fields('smpl_type');   // ���X �˥����O
		    	$op['back_str']=$PHP_back_str;
				$op['back_str2']=$PHP_back_str2;

	
			page_display($op, 2, 3, $TPL_SMPL_WI_ADD);	    	    
		break;
		
		
		}   // end check
		
		// �s�s �s�@�O ���X----------------------------------------------------------------
		//  �s�s ��@�O���X �]�P�ɧ�sdept�ɤ���num��[csv]
		$parm['wi_num'] = $PHP_style_code; 
		$etd['field_name']='etd';
		$etd['field_value']=$PHP_etd;
		$etd['id']=$PHP_smpl_id;
		$f1 = $smpl_ord->update_field($etd);
		$f1 = $smpl_wi->add($parm);
	
		if (!$f1) {  // �S�����\��J��Ʈ�

			$op['msg'] = $wi->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);
			break;

		}   // end if (!$F1)---------  �����P�_ ------------------------------------
		$message = "Done ADD [".$parm['wi_num']."] W/I�C<br>Please add[colorway / breakdown] and [materiel consumption]�C";

// �N wis [�˥��s�O]�s�� �g�J �˥��ɤ�[ smpl table ] NOTE: �D�n�O���Ӵ� �˥�����A������s�O[�ݦA�ƻs]
				$etd1['field_name']="marked";
				$etd1['field_value']=$parm['wi_num'];
				$etd1['id']=$parm['smpl_id'];
				if (!$smpl_ord->update_field($etd1)) {
					$op['msg']= $order->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
				}
				$etd2['field_name']="m_status";
				$etd2['field_value']='1';
				$etd2['id']=$parm['smpl_id'];
				if (!$smpl_ord->update_field($etd2)) {
					$op['msg']= $order->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
				}

		
		// ���X�ӵ� wi ��� ---------------------------------------------------------- 
			if (!$op['wi']=$smpl_wi->get($f1)) {
				$op['msg']= $smpl_wi->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			$parm_update = array($op['wi']['id'],'last_update',$dt['date_str']);
			$smpl_wi->update_field($parm_update);
			$parm_update = array($op['wi']['id'],'updator',$GLOBALS['SCACHE']['ADMIN']['login_id']);
			$smpl_wi->update_field($parm_update);

			// �e�X �����Ѽ� ---------------------------------------
					$op['smpl'] =$smpl_ord->get_fieldvalue('','qty',$op['wi']['wi_num']);
					$op['smpl']['cust'] = $parm['cust'];
					$op['smpl']['style'] = $parm['cust_ref'];
					$op['smpl']['size_scale'] = $PHP_Size;
					

		//  wi_qty �ƶq�� -----------------------------------------------------------------
		$where_str = " WHERE wi_id='".$f1."' ";
		$T_wiqty = $smpl_wiqty->search(1,$where_str);
	
		// ���X size_scale --------
		
		$size_A = $size_des->get($size_id);
			//????????????????????????????????????? �`�N ���ɨS�� size type ��Ʈ�??????
		$sizing = explode(",", $size_A['size']);

			// ���X size table-------------------------------------------
			$reply = show_breakdown($T_wiqty,$sizing,$size_A['base_size']);
			$op['show_breakdown'] = $reply['html'];
			$op['total'] = $reply['total'];
		//-----------------------------------------------------------------			
			$op['edit_breakdown'] = edit_breakdown($sizing,$PHP_back_str2,$size_A['base_size']);

			// �ۤ���URL�M�w ----------------------------------------------------------------
				$style_dir	= "./smpl_pic/";  
				$no_img		= "./images/graydot.gif";
			if(file_exists($style_dir.$op['wi']['style_code'].".jpg")){
				$op['wi']['pic_url'] = $style_dir.$op['wi']['style_code'].".jpg";
			} else {
				$op['wi']['pic_url'] = $no_img;
			}

			$op['wi']['etd'] = $parm['etd'];
		
		//  ���X�D�ưO��  --------------------------------------------------------------------
		$where_str = " WHERE smpl_code = '".$op['wi']['style_code']."' ";
		$lots = $smplord_lots->search(0,$where_str);  //���X�ӵ� �˥��D�ưO��
		$op['lots_use'] = $lots;

		if (!is_array($op['lots_use'])) {
			$op['msg'] = $smplord_lots->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		if (!count($op['lots_use'])){	$op['lots_NONE'] = "1";		}


		//  ���X�ƮƮưO��  ------------------------------------------------------------------
		$where_str = " WHERE smpl_code = '".$op['wi']['style_code']."' ";
		$acc = $smplord_acc->search(0,$where_str);  //���X�ӵ� �˥��D�ưO��
		$op['acc_use'] = $acc;

		if (!is_array($op['acc_use'])) {     //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
			$op['msg'] = $smplord_acc->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		if (!count($op['acc_use'])){	$op['acc_NONE'] = "1";		}
		//------------------------------------------------------------------------------------
		# �O���ϥΪ̰ʺA
		$log->log_add(0,"23A",$message);
		$op['msg'][] = $message;
		$op['back_str']=$PHP_back_str;
		$op['back_str2']=$PHP_back_str2;
				page_display($op, 2, 3, $TPL_SMPL_WI_ADD2);	    	    

			break;

//=======================================================
	case "add_smpl_colorway":      // �s�W�s�@�O

		check_authority(2,3,"add");
		// �ǤJ javascript�ǥX���Ѽ� [ colorway : ���M��J�w�� javascript�߬d ]
		$parm['qty']		= $PHP_qty;   // ��javascript�ǥX���}�C�� �۰ʧ令csv�ǥX
		$parm['wi_id']		= $wi_id;
		$parm['colorway']	= $colorway;

		// �[�J �s��� ----- �[�J wiqty table -----------------
		if(!$T_del =	$smpl_wiqty->add($parm)){    
						$op['msg']= $wi->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
		}

		// �N �s�y�O ����show out ---------------------------------------------
		//  wi �D�� ��X�� ----------------------------------------------------
		if(!$op = $smpl_wi->get_all($wi_id)){    //���X�ӵ� �s�y�O�O��
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}


				# �O���ϥΪ̰ʺA
		$message = "Successful add w/i[".$op['wi']['wi_num']."] colorway[".$colorway."]�E";

		//  wi_qty �ƶq�� -----------------------------------------------------------------
		$where_str = " WHERE wi_id='".$wi_id."' ";
		$T_wiqty = $smpl_wiqty->search(1,$where_str);

		// ���X size_scale --------
		$size_A = $size_des->get($op['smpl']['size']);
		
		$op['smpl']['size_scale']=$size_A['size_scale'];
			//????????????????????????????????????? �`�N ���ɨS�� size type ��Ʈ�??????
		$sizing = explode(",", $size_A['size']);
//------------search����O��
		    if (isset($PHP_dept_code))
			{
				$PHP_back_str = "&PHP_sr_startno=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_ref=".$PHP_ref."&PHP_factory=".$PHP_factory."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh;
				$op['back_str']=$PHP_back_str;
				$PHP_back_str2 = "&PHP_sr=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_ref=".$PHP_ref."&PHP_factory=".$PHP_factory."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh;
				$op['back_str2']=$PHP_back_str2;
			}else{
				$op['back_str']=$PHP_back_str;
				$op['back_str2']=$PHP_back_str2;				
			}
			// ���X size table--------
			$op['show_del_breakdown'] = show_del_breakdown($T_wiqty,$sizing,$PHP_back_str2,$size_A['base_size']);
			$op['edit_breakdown'] = edit_breakdown($sizing,$PHP_back_str2,$size_A['base_size']);		
			$op['msg'][] = $message;
		page_display($op, 2, 3, $TPL_SMPL_WI_ADD2);	  
		break;

//=======================================================
	case "do_smpl_wi_add_2":

		check_authority(2,3,"add");
	// ��J�����T��............after check input.........
				// ���X�~�X... �H��J�ɤ��~���~�X[���]

		// �N�D�Ʈ� �w���g�J �D�Ʈƨϥ��ɤ� lots_use, acc_use  ....
		// �D��  --------------------------------------------------------------------------
		$parm = array(	"style_code"	=>	$PHP_style_code,
						"cust_ref"		=>	$PHP_cust_ref,
						"size_type"		=>	$PHP_size_type,
						"size_scale"	=>	$PHP_size_scale,
						
						"wi_id"		=>	$PHP_wi_id,
						"wi_num"	=>	$PHP_wi_num,
						"dept"		=>	$PHP_dept,
						"smpl_id"	=>	$PHP_smpl_id,
						"cust"		=>	$PHP_cust,		

						"etd"		=>	$PHP_etd,
						"smpl_type" =>	$PHP_smpl_type,

						"unit"		=>	$PHP_unit,
						"lots_est_array"	=>	$PHP_lots_est,	// �O�� array 
						"acc_est_array"		=>	$PHP_acc_est,  // �O�� array
						"ester"				=>	$GLOBALS['SCACHE']['ADMIN']['login_id']
			);
			
		if($parm['lots_est_array']){
			while(list($key,$val) = each($parm['lots_est_array'])){
				if($val){
					$lots = array($key,"est_1",$val);
					if(!$smplord_lots->update_field($lots)){
						$op['msg']= $smplord_lots->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
					}

					$updt = array($key,"ester_1",$parm['ester']);
					if(!$smplord_lots->update_field($updt)){
						$op['msg']= $smplord_lots->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
					}
				}
			}
		}
		// �Ʈ�  ----------------------------------------------------------------------------
		if($parm['acc_est_array']){
			while(list($key,$val) = each($parm['acc_est_array'])){
				if($val){
					$acc = array($key,"est_1",$val);
					if(!$smplord_acc->update_field($acc)){
						$op['msg']= $smplord_acc->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
					}

					$updt = array($key,"ester_1",$parm['ester']);
					if(!$smplord_acc->update_field($updt)){
						$op['msg']= $smplord_acc->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
					}
				}
			}
		}
		
		//------------------------------------ �����g�J wi �� �ήƹw��---------------
		// �N �s�y�O ����show out ------
		//  wi �D��
		if(!$op = $smpl_wi->get_all($parm['wi_id'])){
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}
//--------------------------------------------------------------------------
		
		//  wi_qty �ƶq��
			$where_str = " WHERE wi_id='".$op['wi']['id']."' ";
		$T_wiqty = $smpl_wiqty->search(1,$where_str);

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

				# �O���ϥΪ̰ʺA
		$message = "Successfully add full w/i : ".$op['wi']['wi_num']."record�C";
		$log->log_add(0,"23A",$message);
		$op['msg'][] = $message;

		$op['back_str']=$PHP_back_str;
		$op['back_str2']=$PHP_back_str2;
			page_display($op, 2, 3, $TPL_SMPL_WI_VIEW);
		break;

//========================================================================================	
	case "smpl_wi_view":

		check_authority(2,3,"view");

		// �N �s�y�O ����show out ------
		//  wi �D��

		if(is_numeric($PHP_id)){
			
			if(!$op = $smpl_wi->get_all($PHP_id)){    //���X�ӵ� �s�y�O�O�� ID
						$op['msg']= $smpl_wi->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
			}
		} else {
			if(!$op = $smpl_wi->get_all(0,$PHP_id)){    //���X�ӵ� �s�y�O�O�� WI_NUM
						$op['msg']= $smpl_wi->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
			}
		}

//--------------------------------------------------------------------------
		//  wi_qty �ƶq��
		$where_str = " WHERE wi_id='".$op['wi']['id']."' ";
		$T_wiqty = $smpl_wiqty->search(1,$where_str);

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
//�ɮצC��
		$op['done'] = $fils->get_smpl_file($op['wi']['wi_num']);

		if(count($op['ti']))
		{
		 for ($i=0; $i<sizeof($op['ti']); $i++)
			$op['ti'][$i]['detail'] = str_replace( chr(13).chr(10), "<br>", $op['ti'][$i]['detail'] );
		}


		$op['msg'] = $wi->msg->get(2);
		$back_str = "&PHP_sr_startno=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_ref=".$PHP_ref."&PHP_factory=".$PHP_factory."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh;
		$op['back_str']=$back_str;
		$back_str2 = "&PHP_sr=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_ref=".$PHP_ref."&PHP_factory=".$PHP_factory."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh;
		$op['back_str2']=$back_str2;
		$op['now_pp']=$PHP_sr;
		if (isset($PHP_ex_order))
		{
			$op['edit_non']=1;
		}
		if (isset($PHP_ti))
		{
			if (isset($PHP_msg))$op['msg'][]=$PHP_msg;
			page_display($op, 2, 3, $TPL_SMPL_TI_VIEW);
		}else if (isset($PHP_cfm_view)){
			if (isset($PHP_msg))$op['msg'][]=$PHP_msg;
			page_display($op, 2, 4, $TPL_SMPL_WI_CFM_VIEW);
		}else{
			page_display($op, 2, 3, $TPL_SMPL_WI_VIEW);
		}
		break;


//=======================================================
    case "smpl_wi_print":   //  .......

		if(!$admin->is_power(2,3,"view")){ 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		if(is_numeric($PHP_id)){
			if(!$wi = $smpl_wi->get($PHP_id)){    //���X�ӵ� �s�y�O�O�� ID
						$op['msg']= $wi->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
			}
		} else {
			if(!$wi = $smpl_wi->get(0,$PHP_id)){    //���X�ӵ� �s�y�O�O�� WI_NUM
						$op['msg']= $wi->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
			}
		}

		//  smpl �˥���
		if(!$smpl = $smpl_ord->get($wi['smpl_id'])){
					$op['msg']= $order->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}

//--------------------------------------------------------------------------
		//  wi_qty �ƶq��
			$where_str = " WHERE wi_id='".$wi['id']."' ";
		$T_wiqty = $smpl_wiqty->search(1,$where_str);

		$num_colors = count($T_wiqty);

		// ���X size_scale ----------------------------------------------
//		$where_str = " WHERE cust='".$wi['cust']."' AND size_scale='".$smpl['size']."' ";
		$size_A = $size_des->get($smpl['size']);
		$smpl['size_scale']=$size_A['size_scale'];
			//????????????????????????????????????? �`�N ���ɨS�� size type ��Ʈ�??????
		$sizing = explode(",", $size_A['size']);

			// ���X size table-------------------------------------------
		$reply = get_colorway_qty($T_wiqty,$sizing);

		$op['total'] = $reply['total'];   // �`���
		$data = $reply['data'];

		$colorway_name = $reply['colorway'];
		$colorway_qty = $reply['colorway_qty'];
		//-----------------------------------------------------------------

		//  ���X �Ͳ����� �O�� -------------------------------------------------
		$where_str = " WHERE wi_id = '".$wi['id']."' ";
		$T = $smpl_ti->search(0,$where_str);  //���X�ӵ� �˥��D�ưO��
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
				$style_dir	= "./smpl_pic/";  
				$no_img		= "./images/graydot.jpg";
			if(file_exists($style_dir.$wi['style_code'].".jpg")){
				$wi['pic_url'] = $style_dir.$wi['style_code'].".jpg";
			} else {
				$wi['pic_url'] = $no_img;
			}
		
		//  ���X�D�ưO�� ----------------------------------------------------
		$where_str = " WHERE smpl_code = '".$wi['style_code']."' ";
		$lots = $smplord_lots->search(0,$where_str);  //���X�ӵ� �˥��D�ưO��
		$op['lots_use'] = $lots;

		if (!is_array($op['lots_use'])) {
			$op['msg'] = $smplord_lots->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		if (!count($op['lots_use'])){	$op['lots_NONE'] = "1";		}

		//  ���X�ƮƮưO�� ---------------------------------------------------
		$where_str = " WHERE smpl_code = '".$wi['style_code']."' ";
		$acc = $smplord_acc->search(0,$where_str);  //���X�ӵ� �˥��D�ưO��
		$op['acc_use'] = $acc;

		if (!is_array($op['acc_use'])) {     
			$op['msg'] = $smplord_acc->msg->get(2);
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
$pdf->AliasNbPages(); 
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
// [��J] �q��򥻸��
$pdf->hend_title($PHP_id,$wi,$smpl,$op['total'],'Sample Order#');

		$Y = $pdf->getY();
		
		$img_size = GetImageSize($wi['pic_url']);
		
		if ($img_size[0] > $img_size[1])
		{
			$pdf->Image($wi['pic_url'],10,28,40,0);
		}else{
			$pdf->Image($wi['pic_url'],10,28,0,40);
		}
		
		$pdf->ln();
		$Y = $pdf->getY();
		$X = $pdf->getX();
// �]�w colorway���ƶq�� - ��쪺�e  [size breakdown]
// �� 80~200 �O�d 30�� colorway���e �䥦 90���t���ƶq��
	$num_siz = count($sizing);
	$w_qty = intval(155/($num_siz+1));
	$w = array('40');  // �Ĥ@�欰 30�e
	for ($i=0;$i<=$num_siz;$i++){
		array_push($w, $w_qty);
	}

	$header = array_merge(array('Colorway') , $sizing);	
	$table_title = "Size Breakdown";
	$R = 100;
	$G = 85;
	$B = 85;
	if (count($sizing)){
		$pdf->Table_1(10,70,$header,$data,$w,$table_title,$R,$G,$B,$size_A['base_size']);
	}else{
		$pdf->Cell(10,70,'there is no sizing data exist !',1);
	}

		// �w�q �ϥH�U���y�� 
		$pdf->SetXY($X,$Y+55);
//		$pdf->Write(5,'www.fpdf.org','http://www.fpdf.org'); 

// �D��
		$pdf->SetFont('Big5','',10);

$xic=1;
$x=1;
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
		$pdf->hend_title($PHP_id,$wi,$smpl,$op['total'],'Sample Order#');		
		if ($img_size[0] > $img_size[1])
		{
			$pdf->Image($wi['pic_url'],10,28,40,0);
		}else{
			$pdf->Image($wi['pic_url'],10,28,0,40);
		}

		$pdf->ln();
		$pdf->ln();
		$pdf->ln();
		$pdf->setx(10);
		$pdf->SetFont('Arial','B',14);
		$pdf->Cell(190,7,'Fabrics',0,1,'L');
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
		if ($xic > 26)
		{
			$pdf->AddPage();
			$pdf->SetFont('Arial','B',14);
			// [��J] �q��򥻸��
			$pdf->hend_title($PHP_id,$wi,$smpl,$op['total'],'Sample Order#');		
			if ($img_size[0] > $img_size[1])
			{
				$pdf->Image($wi['pic_url'],10,28,40,0);
			}else{
				$pdf->Image($wi['pic_url'],10,28,0,40);
			}

			$pdf->ln();
			$pdf->ln();
			$pdf->ln();
			$pdf->setx(10);
			$pdf->SetFont('Arial','B',14);
			$pdf->cell(190,7,'Accessories',0,1,'L');
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
$pdf->ln();

$name=$wi['wi_num'].'_wi.pdf';
$pdf->Output($name,'D');
break;	

//=======================================================
    case "smpl_ti_print":   //  .......

		if(!$admin->is_power(2,3,"view")){ 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		if(is_numeric($PHP_id)){
			if(!$wi = $smpl_wi->get($PHP_id)){    //���X�ӵ� �s�y�O�O�� ID
						$op['msg']= $wi->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
			}
		} else {
			if(!$wi = $smpl_wi->get(0,$PHP_id)){    //���X�ӵ� �s�y�O�O�� WI_NUM
						$op['msg']= $wi->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
			}
		}

		//  smpl �˥���
		if(!$smpl = $smpl_ord->get($wi['smpl_id'])){
					$op['msg']= $order->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}

//--------------------------------------------------------------------------
		//  wi_qty �ƶq��
			$where_str = " WHERE wi_id='".$wi['id']."' ";
		$T_wiqty = $smpl_wiqty->search(1,$where_str);

		$num_colors = count($T_wiqty);

		// ���X size_scale ----------------------------------------------
//		$where_str = " WHERE cust='".$wi['cust']."' AND size_scale='".$smpl['size']."' ";
		$size_A = $size_des->get($smpl['size']);
		$smpl['size_scale']=$size_A['size_scale'];
			//????????????????????????????????????? �`�N ���ɨS�� size type ��Ʈ�??????
		$sizing = explode(",", $size_A['size']);

			// ���X size table-------------------------------------------
		$reply = get_colorway_qty($T_wiqty,$sizing);

		$op['total'] = $reply['total'];   // �`���
		$data = $reply['data'];

		$colorway_name = $reply['colorway'];
		$colorway_qty = $reply['colorway_qty'];
		//-----------------------------------------------------------------

		//  ���X �Ͳ����� �O�� -------------------------------------------------
		$where_str = " WHERE wi_id = '".$wi['id']."' ";
		$T = $smpl_ti->search(0,$where_str);  //���X�ӵ� �˥��D�ưO��
		$op['ti'] = $T['ti'];
		if(count($op['ti']))
		{
		 for ($i=0; $i<sizeof($op['ti']); $i++)
			$op['ti'][$i]['detail'] = str_replace( chr(13).chr(10), "<br>", $op['ti'][$i]['detail'] );
		}

		if (!is_array($op['ti'])) {
			$op['msg'] = $ti->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		if (!count($op['ti'])){	$op['ti_NONE'] = "1";		}

		$op['done'] = $fils->get_smpl_file($wi['wi_num']);		
//		if (!count($op['done'])){	$op['done_NONE'] = "1";		}
//-----------------------------------------------------------------------
			// �ۤ���URL�M�w
				$style_dir	= "./smpl_pic/";  
				$no_img		= "./images/graydot.jpg";
			if(file_exists($style_dir.$wi['style_code'].".jpg")){
				$wi['pic_url'] = $style_dir."s_".$wi['style_code'].".jpg";
			} else {
				$wi['pic_url'] = $no_img;
			}

//---------------------------------- ��Ʈw�@�~���� ------------------------------
header("Content-type: application/pdf ");
header("Content-Transfer-Encoding: binary");

include_once($config['root_dir']."/lib/class.pdf_ti.php");


$print_title = "WorkSheet";


$print_title2 = "VER.".$wi['ti_rev']."  on  ".$wi['ti_cfm'];
$creator = $wi['ti_cfm_user'];

$ary_title = array ('prt_ord'	=>	'Sample Order#',
							 'id'				=>	$PHP_id,
							 'cust'			=>	$smpl['cust_iname'],
							 'ref'			=>	$smpl['ref'],
							 'dept'			=>	$smpl['dept'],
							 'size'			=>	$smpl['size_scale'],
							 'style'		=>	$smpl['style'],
							 'qty'			=>	$op['total'],
							 'unit'			=>	$wi['unit'],
							 'etd'			=>	$wi['etd'],
							 'img'			=>	$wi['pic_url'],							 
							 'img_size'	=>	GetImageSize($wi['pic_url']),
							);

$pdf=new PDF_ti();
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
// [��J] �q��򥻸��

		$X = $pdf->getX();

// �]�w colorway���ƶq�� - ��쪺�e  [size breakdown]
// �� 80~200 �O�d 30�� colorway���e �䥦 90���t���ƶq��
	$num_siz = count($sizing);
	$w_qty = intval(155/($num_siz+1));
	$w = array('40');  // �Ĥ@�欰 30�e
	for ($i=0;$i<= $num_siz;$i++){
		array_push($w, $w_qty);
	}

	$header = array_merge(array('Colorway') , $sizing);	
	$table_title = "Size Breakdown";
	$R = 100;
	$G = 85;
	$B = 85;
	if (count($sizing)){
		$pdf->Table_1(10,70,$header,$data,$w,$table_title,$R,$G,$B,$size_A['base_size']);
	}else{
		$pdf->Cell(10,70,'there is no sizing data exist !',1);
	}

		$Y = $pdf->getY();
		$X = $pdf->getX();

		// �w�q �ϥH�U���y�� 
		$pdf->SetXY($X,$Y+5);

// �D��
		$pdf->SetFont('Big5','',10);

$xic=1;
$x=7;$mark=0;
$pdf->ln();

for ($i=0; $i<sizeof($TI_ITEM); $i++)
{
	$tmp=$i+1;	
	for ($j=0; $j<sizeof($op['ti']); $j++)
	{
		if ($op['ti'][$j]['item']==$TI_ITEM[$i])
		{
			$mark=1;
			$star=$j;
			break;
		}
	}

	if($mark==1)
	{
		$x=$x+2;
		if ($x > 35)
		{
				$pdf->setx(10);
				$pdf->Cell(190,5,'','LBR',0,'L');	
				$pdf->AddPage();		
		}
		
		$pdf->SetFont('Big5','',10);		
		$pdf->ti_name_fields($tmp.")".$TI_ITEM[$i]);
		for ($j=$star; $j<sizeof($op['ti']); $j++)
		{

			if ($op['ti'][$j]['item']==$TI_ITEM[$i])
			{

				$pdf->SetFont('Big5','',10);
				$x=$pdf->ti_value_fields($op['ti'][$j]['detail'],$x,$tmp,$TI_ITEM[$i]);
			}

		}
		$pdf->setx(10);
		$pdf->Cell(190,5,'','LBR',0,'L');
		$pdf->ln();
		$mark=0;
	}
}


$pdf->SetFont('Arial','B',14);
$pdf->ln();
$x=$x+2;

$pdf->SetLineWidth(0.2);
if (count($op['done']))
{
		if ($x > 35)
		{
				$pdf->AddPage();		

		}
	$pdf->setx(10);
	$pdf->Cell(190,8,'Attached Files',1,0,'C');
	$pdf->ln();
	$pdf->setx(10);
	$pdf->Cell(10,8,'No.',1,0,'C');
	$pdf->Cell(85,8,'File name',1,0,'C');
	$pdf->Cell(95,8,'File Description',1,0,'C');
	$pdf->ln();
	$pdf->setx(10);
	$pdf->Cell(10,0.5,'',1,0,'C');
	$pdf->Cell(85,0.5,'',1,0,'C');
	$pdf->Cell(95,0.5,'',1,0,'C');
	$pdf->ln();
	$x=$x+2;
	for ($i=0; $i< sizeof($op['done']); $i++)
	{
		
		if ($x > 35)
		{
			$pdf->AddPage();		

			$pdf->setx(10);
			$pdf->Cell(190,8,'Attached Files',1,0,'C');
			$pdf->ln();
			$pdf->setx(10);			
			$pdf->Cell(10,8,'No.',1,0,'C');
			$pdf->Cell(85,8,'File name',1,0,'C');
			$pdf->Cell(95,8,'File Description',1,0,'C');
			$pdf->ln();
			$pdf->setx(10);
			$pdf->Cell(10,0.5,'',1,0,'C');
			$pdf->Cell(85,0.5,'',1,0,'C');
			$pdf->Cell(95,0.5,'',1,0,'C');
			$pdf->ln();
			$x=4;
		}
		$j=$i+1;
		
		$pdf->setx(10);
		$pdf->Cell(10,5,$j,1,0,'C');
		$pdf->SetFont('Big5','',10);
		$pdf->Cell(85,5,$op['done'][$i]['file_name'],1,0,'L');
		$pdf->Cell(95,5,$op['done'][$i]['file_des'],1,0,'L');
		$pdf->SetFont('Arial','B',10);
		$pdf->ln();
		$x++;
	}
}


$name=$wi['wi_num'].'_ws.pdf';

$pdf->Output($name,'D');

break;	

//=======================================================
	case "search_smpl_wi_copy":
	
	$op['wi']=$smpl_wi->copy_search($PHP_c_cust, $PHP_c_dept);

  $op['copy_search'] = "&PHP_wi_num=".$PHP_wi_num."&PHP_smpl_id=".$PHP_smpl_id."&PHP_c_dept=".$PHP_c_dept."&PHP_c_cust=".$PHP_c_cust."&PHP_c_etd=".$PHP_c_etd;
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
	page_display($op, 2, 3, $TPL_SMPL_WI_COPY_SEARCH);
	break;


//========================================================================================	
	case "smpl_wi_copy_view":

		check_authority(2,3,"view");

		// �N �s�y�O ����show out ------
		//  wi �D��
			if(!$op = $smpl_wi->get_all($PHP_id)){    //���X�ӵ� �s�y�O�O�� ID
						$op['msg']= $smpl_wi->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
			}
//--------------------------------------------------------------------------
		//  wi_qty �ƶq��
		$where_str = " WHERE wi_id='".$op['wi']['id']."' ";
		$T_wiqty = $smpl_wiqty->search(1,$where_str);

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

   $op['copy_search'] = "&PHP_wi_num=".$PHP_wi_num."&PHP_smpl_id=".$PHP_smpl_id."&PHP_c_dept=".$PHP_c_dept."&PHP_c_cust=".$PHP_c_cust."&PHP_c_etd=".$PHP_c_etd;

    $op['org_wi_num'] = $PHP_wi_num;
    $op['org_smpl_id'] = $PHP_smpl_id;
    $op['org_etd'] = $PHP_c_etd;
    
		$back_str = "&PHP_sr_startno=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_ref=".$PHP_ref."&PHP_factory=".$PHP_factory."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh;
		$op['back_str']=$back_str;
		$back_str2 = "&PHP_sr=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_ref=".$PHP_ref."&PHP_factory=".$PHP_factory."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh;
		$op['back_str2']=$back_str2;
		
		page_display($op, 2, 3, $TPL_SMPL_WI_COPY_VIEW);

		break;
		
		
		
//=======================================================
		
	case "do_smpl_wi_copy":

		check_authority(2,3,"edit");

		// �N �s�y�O ����show out ------
		//  wi �D�� ��X��
		$f1=$smpl_wi->copy_wi($PHP_wi_num,$PHP_smpl_id,$PHP_new_num,$PHP_etd,$dt['date_str'],$GLOBALS['SCACHE']['ADMIN']['login_id'],$PHP_dept,$PHP_cust);
		if (!$f1)
		{
			$op['msg']= $smpl_wi->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		
		// �N wis [�˥��s�O]�s�� �g�J �˥��ɤ�[ smpl table ] NOTE: �D�n�O���Ӵ� �˥�����A������s�O[�ݦA�ƻs]
				$etd1['field_name']="marked";
				$etd1['field_value']=$PHP_new_num;
				$etd1['id']=$PHP_smpl_id;
				if (!$smpl_ord->update_field($etd1)) {
					$op['msg']= $order->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
				}
				$etd2['field_name']="m_status";
				$etd2['field_value']='1';
				$etd2['id']=$PHP_smpl_id;
				if (!$smpl_ord->update_field($etd2)) {
					$op['msg']= $order->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
				}


		
		
		if(!$op['wi'] = $smpl_wi->get($f1)){    //���X�ӵ� �s�y�O�O��
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}

			$parm_update = array($op['wi']['id'],'last_update',$dt['date_str']);
			$wi->update_field($parm_update);
			$parm_update = array($op['wi']['id'],'updator',$GLOBALS['SCACHE']['ADMIN']['login_id']);
			$wi->update_field($parm_update);


		//  smpl �˥���  ��X��
		if(!$op['smpl'] = $smpl_ord->get($op['wi']['smpl_id'])){
					$op['msg']= $smpl->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}
	//�˵��O�_�i�H�ק�size(�Y�O�_�s�bcolorway)
		if ($smpl_wi->check_size($op['wi']['style_code']))
		{
			$op['size_edit']=1;
		}

		//  wi_qty �ƶq�� -----------------------------------------------------------------
		$where_str = " WHERE wi_id='".$f1."' ";
		$T_wiqty = $smpl_wiqty->search(1,$where_str);

		// ���X size_scale --------
//		$where_str = " WHERE cust='".$op['smpl']['cust']."' AND size_scale='".$op['smpl']['size']."' ";
		$size_A = $size_des->get($op['smpl']['size']);
		$op['smpl']['size_scale']=$size_A['size_scale'];
			//????????????????????????????????????? �`�N ���ɨS�� size type ��Ʈ�??????
		$sizing = explode(",", $size_A['size']);


//search����O��			
$op['back_str']=$PHP_back_str;
$op['back_str2']=$PHP_back_str2;

		// ���X size table-------------------------------------------
			$op['show_del_breakdown'] = show_del_breakdown($T_wiqty,$sizing,$PHP_back_str2,$size_A['base_size']);
			$op['edit_breakdown'] = edit_breakdown($sizing,$PHP_back_str2,$size_A['base_size']);



			// �ۤ���URL�M�w  ---------------------------------------------
				$style_dir	= "./smpl_pic/";  
				$no_img		= "./images/graydot.gif";
			if(file_exists($style_dir.$op['wi']['style_code'].".jpg")){
				$op['wi']['pic_url'] = $style_dir.$op['wi']['style_code'].".jpg";
			} else {
				$op['wi']['pic_url'] = $no_img;
			}
		
		//  ���X�D�ưO�� -----------------------------------------------------
		$where_str = " WHERE smpl_code = '".$op['wi']['style_code']."' ";
		$lots = $smplord_lots->search(0,$where_str);  //���X�ӵ� �˥��D�ưO��
		$op['lots_use'] = $lots;

		if (!is_array($op['lots_use'])) {
			$op['msg'] = $smplord_lots->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		if (!count($op['lots_use'])){	$op['lots_NONE'] = "1";		}

		//  ���X�ƮƮưO��  -----------------------------------------------------
		$where_str = " WHERE smpl_code = '".$op['wi']['style_code']."' ";
		$acc = $smplord_acc->search(0,$where_str);  //���X�ӵ� �˥��D�ưO��
		$op['acc_use'] = $acc;

		if (!is_array($op['acc_use'])) {     
			$op['msg'] = $smplord_acc->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		if (!count($op['acc_use'])){	$op['acc_NONE'] = "1";		}

			// ���X unit�� combo
		$op['unit'] = $arry2->select($APPAREL_UNIT,$op['wi']['unit'],'PHP_unit','select','');  	

		// creat sample type combo box
		$sample_def = $smpl_type->get_fields('smpl_type');   // ���X �˥����O
		$op['smpl_type'] =  $arry2->select($sample_def,$op['wi']['smpl_type'],'PHP_smpl_type','select','');  	
		$mseeage = "Successfully copy [".$op['wi']['wi_num']."] ";
		$log->log_add(0,"23E",$mseeage);
			$op['msg'] = $smpl_wi->msg->get(2);

		page_display($op, 2, 3, $TPL_SMPL_WI_EDIT);
		break;

//=======================================================
	case "smpl_wi_edit":

		check_authority(2,3,"edit");

		// �N �s�y�O ����show out ------
		//  wi �D�� ��X��
		if(!$op = $smpl_wi->get_all($PHP_id)){    //���X�ӵ� �s�y�O�O��
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}
	//�˵��O�_�i�H�ק�size(�Y�O�_�s�bcolorway)
		if ($smpl_wi->check_size($op['wi']['style_code']))
		{
			$op['size_edit']=1;
		}

		//  wi_qty �ƶq�� -----------------------------------------------------------------
		$where_str = " WHERE wi_id='".$PHP_id."' ";
		$T_wiqty = $smpl_wiqty->search(1,$where_str);

		// ���X size_scale --------
		$size_A = $size_des->get($op['smpl']['size']);
		$op['smpl']['size_scale']=$size_A['size_scale'];
			//????????????????????????????????????? �`�N ���ɨS�� size type ��Ʈ�??????
		$sizing = explode(",", $size_A['size']);


//search����O��			
		if (isset($PHP_dept_code))
		{
			$PHP_back_str = "&PHP_sr_startno=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_ref=".$PHP_ref."&PHP_factory=".$PHP_factory."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh;
			$op['back_str']=$PHP_back_str;
			$PHP_back_str2 = "&PHP_sr=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_ref=".$PHP_ref."&PHP_factory=".$PHP_factory."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh;
			$op['back_str2']=$PHP_back_str2;

		}else{
			$op['back_str']=$PHP_back_str;
			$op['back_str2']=$PHP_back_str2;
		}
		// ���X size table-------------------------------------------
		$op['show_del_breakdown'] = show_del_breakdown($T_wiqty,$sizing,$PHP_back_str2,$size_A['base_size']);
		$op['edit_breakdown'] = edit_breakdown($sizing,$PHP_back_str2,$size_A['base_size']);

		// ���X unit�� combo
		$op['unit'] = $arry2->select($APPAREL_UNIT,$op['wi']['unit'],'PHP_unit','select','');  	

		// creat sample type combo box
		$sample_def = $smpl_type->get_fields('smpl_type');   // ���X �˥����O
		$op['smpl_type'] =  $arry2->select($sample_def,$op['wi']['smpl_type'],'PHP_smpl_type','select','');  	
			
		$op['msg'] = $smpl_wi->msg->get(2);

		page_display($op, 2, 3, $TPL_SMPL_WI_EDIT);
		break;

//=======================================================
	case "smpl_edit_add_colorway":

		check_authority(2,3,"edit");
		// �ǤJ javascript�ǥX���Ѽ� [ colorway : ���M��J�w�� javascript�߬d ]
		$parm['qty']		= $PHP_qty;   // ��javascript�ǥX���}�C�� �۰ʧ令csv�ǥX
		$parm['wi_id']		= $wi_id;
		$parm['colorway']	= $colorway;

		// �[�J �s��� ----- �[�J wiqty table -----------------
		if(!$T_del =	$smpl_wiqty->add($parm)){    
						$op['msg']= $wi->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
		}

		// �N �s�y�O ����show out ---------------------------------------------
		//  wi �D�� ��X�� ----------------------------------------------------
		if(!$op = $smpl_wi->get_all($wi_id)){    //���X�ӵ� �s�y�O�O��
					$op['msg']= $smpl_wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}
//�˵��O�_�i�H�ק�size(�Y�O�_�s�bcolorway)
		if ($smpl_wi->check_size($op['wi']['style_code']))
		{
			$op['size_edit']=1;
		}

	//  ��s �s�y�O ���� ----- ��� wi table -----------------
/*
			$edit_version[0] = $wi_id;
			$edit_version[1] = "version";
			$edit_version[2] = $op['wi']['version']+1;
	
				if(!$T_updt =	$smpl_wi->update_field($edit_version)){    
								$op['msg']= $wi->msg->get(2);
								$layout->assign($op);
								$layout->display($TPL_ERROR);  		    
								break;
				}
			$op['wi']['version'] = $edit_version[2];    // �N�s�� version �N�J
*/
				# �O���ϥΪ̰ʺA
		$message = "Successfully add wi[".$op['wi']['wi_num']."] colorway[".$colorway."]�E";
		$log->log_add(0,"23E",$message);

		//  wi_qty �ƶq�� -----------------------------------------------------------------
		$where_str = " WHERE wi_id='".$wi_id."' ";
		$T_wiqty = $smpl_wiqty->search(1,$where_str);

		// ���X size_scale --------
		$size_A = $size_des->get($op['smpl']['size']);
		$op['smpl']['size_scale']=$size_A['size_scale'];
			//????????????????????????????????????? �`�N ���ɨS�� size type ��Ʈ�??????
		$sizing = explode(",", $size_A['size']);
		
		
		$back_str = "&PHP_sr_startno=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_ref=".$PHP_ref."&PHP_factory=".$PHP_factory."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh;
		$op['back_str']=$back_str;
		$back_str2 = "&PHP_sr=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_ref=".$PHP_ref."&PHP_factory=".$PHP_factory."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh;
		$op['back_str2']=$back_str2;


		// ���X size table-------------------------------------------		
		$op['show_del_breakdown'] = show_del_breakdown($T_wiqty,$sizing,$back_str2,$size_A['base_size']);
		$op['edit_breakdown'] = edit_breakdown($sizing,$back_str2,$size_A['base_size']);

		$op['msg'] = $wi->msg->get(2);

			// ���X unit�� combo
		$op['unit'] = $arry2->select($APPAREL_UNIT,$op['wi']['unit'],'PHP_unit','select','');  	

		// creat sample type combo box
		$sample_def = $smpl_type->get_fields('smpl_type');   // ���X �˥����O
		$op['smpl_type'] =  $arry2->select($sample_def,$op['wi']['smpl_type'],'PHP_smpl_type','select','');  	
			
		$op['msg'][] = $message;
		$op['new_revise'] = $op['wi']['revise'];
		
		if(!empty($PHP_revice))
		{
			page_display($op, 2, 3, $TPL_SMPL_WI_REVISE);
		}else{
			page_display($op, 2, 3, $TPL_SMPL_WI_EDIT); 
		}
		break;

//=======================================================
		case "smpl_edit_del_colorway_ajx":

		check_authority(2,3,"edit");
		
		$size_edit=0;
		
		// �R�� ���w����� ----------------------------
		if(!$T_del =	$smpl_wiqty->del($id)){    
						$op['msg']= $smpl_wiqty->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
		}



//�˵��O�_�i�H�ק�size(�Y�O�_�s�bcolorway)
		if ($smpl_wi->check_size($PHP_wi_num))
		{
			$size_edit=1;
		}

		# �O���ϥΪ̰ʺA
		$message = "Success delete wi[".$PHP_wi_num."] colorway[".$colorway."]";
		$log->log_add(0,"23E",$message);
		echo $message.'|'.$size_edit;
		exit;
		break;
		
//=======================================================
	case "do_smpl_wi_edit":

		check_authority(2,3,"edit");
		$argv = array(	"id"			=>  $PHP_wi_id,
									"wi_num"		=>	$PHP_wi_num,
									"smpl_id"		=>	$PHP_smpl_id,						
									"version"		=>	$PHP_version,
										"etd"			=>	$PHP_etd,
						
									"unit"			=>	$PHP_unit,
						"lots_est_array"	=>	$PHP_lots_est,
						"acc_est_array"		=>	$PHP_acc_est,
						"updator"		=>	$GLOBALS['SCACHE']['ADMIN']['login_id']
			);
			
		if(!$f1=$smpl_wi->check2($argv))
		{
					// �N �s�y�O ����show out ------
			//  wi �D�� ��X��
			if(!$op = $smpl_wi->get_all($PHP_wi_id)){    //���X�ӵ� �s�y�O�O��
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
			}
		//�˵��O�_�i�H�ק�size(�Y�O�_�s�bcolorway)
			if ($smpl_wi->check_size($op['wi']['style_code']))
			{
				$op['size_edit']=1;
			}

			//  wi_qty �ƶq�� -----------------------------------------------------------------
			$where_str = " WHERE wi_id='".$PHP_wi_id."' ";
			$T_wiqty = $smpl_wiqty->search(1,$where_str);

			// ���X size_scale --------
			$size_A = $size_des->get($op['smpl']['size']);
			$op['smpl']['size_scale']=$size_A['size_scale'];
			//????????????????????????????????????? �`�N ���ɨS�� size type ��Ʈ�??????
			$sizing = explode(",", $size_A['size']);


//search����O��			
			$op['back_str']=$PHP_back_str;
 			$op['back_str2']=$PHP_back_str2;
			// ���X size table-------------------------------------------
			$op['show_del_breakdown'] = show_del_breakdown($T_wiqty,$sizing,$PHP_back_str2,$size_A['base_size']);
			$op['edit_breakdown'] = edit_breakdown($sizing,$PHP_back_str2,$size_A['base_size']);
			// ���X unit�� combo
			$op['unit'] = $arry2->select($APPAREL_UNIT,$op['wi']['unit'],'PHP_unit','select','');  	
			
			$op['msg'] = $smpl_wi->msg->get(2);

			page_display($op, 2, 3, $TPL_SMPL_WI_EDIT);
			break;
		}
		if(!$op['wi'] = $smpl_wi->get($PHP_wi_id)){    //���X�ӵ� �s�y�O�O��
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}
		if(!$argv['etd']) $argv['etd']=$op['wi']['etd'];

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
			$smpl3['field_name']='size';
			$smpl3['field_value']=$size_id;
			$smpl3['id']=$PHP_smpl_id;	
			$f1 = $smpl_ord->update_field($smpl3);
		}
		
		$smpl2['field_name']='etd';
		$smpl2['field_value']=$argv['etd'];
		$smpl2['id']=$PHP_smpl_id;		
		$f1 = $smpl_ord->update_field($smpl2);
		
		$updt = array(	"etd"			=>	$argv['etd'],						
						"unit"			=>	$argv['unit'],
						"version"		=>	$argv['version'] +1,
						"updator"		=>	$argv['updator'],
						"id"			=>	$argv['id']
			);
				if(!$smpl_wi->edit($updt)){
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
				}


		// �N�D�Ʈ� �ק�� �w���g�J �D�Ʈƨϥ��ɤ� lots_use, acc_use  ....
		// �D�� -----------------------------------------------------------------------------
		if(($argv['lots_est_array'])){
			while(list($key,$val) = each($argv['lots_est_array'])){
				if($val){
					$lots = array($key,"est_1",$val);
					if(!$smplord_lots->update_field($lots)){
						$op['msg']= $smplord_lots->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
					}

					$updt = array($key,"ester_1",$argv['updator']);
					if(!$smplord_lots->update_field($updt)){
						$op['msg']= $smplord_lots->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
					}
				}
			}
		}
		// �Ʈ� ------------------------------------------------------------------------------
		if(($argv['acc_est_array'])){
			while(list($key,$val) = each($argv['acc_est_array'])){
				if($val){
					$acc = array($key,"est_1",$val);
					if(!$smplord_acc->update_field($acc)){
						$op['msg']= $smplord_acc->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
					}

					$updt = array($key,"ester_1",$argv['updator']);
					if(!$smplord_acc->update_field($updt)){
						$op['msg']= $smplord_acc->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
					}
				}
			}
		}

		//--------------- �����ק�g�J------------------

		//--------------- �C�X��ᤧ�O��------------------
		//  wi �D�� --------------------------------------------------------------------------------
		if(!$op = $smpl_wi->get_all($argv['id'])){    //���X�ӵ� �s�y�O�O��
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}

		//  wi_qty �ƶq�� --------------------------------------------------------------------------
			$where_str = " WHERE wi_id='".$op['wi']['id']."' ";
		$T_wiqty = $smpl_wiqty->search(1,$where_str);

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

		//�ɮצC��
		$op['done'] = $fils->get_smpl_file($op['wi']['wi_num']);
	
		# �O���ϥΪ̰ʺA
		$message = "Successfully update wi:[".$op['wi']['wi_num']."]";
		$log->log_add(0,"23E",$message);
		$op['msg'][] = $message;
 		$op['back_str']=$PHP_back_str;
 		$op['back_str2']=$PHP_back_str2;
			page_display($op, 2, 3, $TPL_SMPL_WI_VIEW);	
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "smpl_wi_del":

			// ���ݭn manager login �~��u�����R�� SUPLIER  ----------------------------------
		if(!$admin->is_power(2,3,"del")  && !($GLOBALS['SCACHE']['ADMIN']['id'] == "SA" )) {
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$op['wi'] = $smpl_wi->get($PHP_id);

		// �R�� wi �D�ɸ�� ---------------------------------------------------
		if (!$smpl_wi->del($PHP_id)) {
			$op['msg'] = $smpl_wi->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;	    
		}
		// �R�� wi qty �ƶq�ɸ�� ---------------------------------------------------
		if (!$smpl_wiqty->del($PHP_id,'1')) {
			$op['msg'] = $smpl_wi->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;	    
		}
					
		// �R�� TI �����ɸ�� ---------------------------------------------------
		if (!$smpl_ti->del($PHP_id,'1')) {
			$op['msg'] = $wi->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;	    
		}

		
		// �R�� COMMENTS �Ȥ�N���ɸ�� ---------------------------------------------------
		

		// �R�� BOM �ɸ�� ---------------------------------------------------

		
		
		
		// �Ѱ� smpl �O������ marked ---------------------------------------------------
		$smpl2['field_name']='marked';
		$smpl2['field_value']='';
		$smpl2['id']=$op['wi']['smpl_id'];
		if (!$smpl_ord->update_field($smpl2)) {
			$op['msg']= $order->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		// �Ѱ� smpl �O������ marked ---------------------------------------------------
		$smpl1['field_name']='m_status';
		$smpl1['field_value']='0';
		$smpl1['id']=$op['wi']['smpl_id'];
		if (!$smpl_ord->update_field($smpl1)) {
			$op['msg']= $order->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
					
					
					# �O���ϥΪ̰ʺA
		$message = "Successfully delete wi:[".$op['wi']['wi_num']."] �C";
		$log->log_add(0,"23D",$message);

		$back_str = "index2.php?&PHP_action=smpl_wi_search&PHP_sr_startno=".$PHP_sr_startno."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_ref=".$PHP_ref."&PHP_factory=".$PHP_factory."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh;
		redirect_page($back_str);
    	    
		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//   wi_cfm      //  WI CFM...........
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "smpl_wi_uncfm":
		check_authority(2,4,"view");
		
		if (!$op = $smpl_wi->search_uncfm('wi')) {
			$op['msg']= $smpl_wi->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$back_str="&PHP_dept_code=&PHP_num=&PHP_cust=&PHP_ref=&PHP_factory=&PHP_etdstr=&PHP_etdfsh=";
		for ($i=0; $i<sizeof($op['wi']); $i++)
		{
			$where_str="WHERE smpl_code = '".$op['wi'][$i]['wi_num']."'";
			$op['wi'][$i]['lots']=$smplord_lots->get_fields('id',$where_str);
			$op['wi'][$i]['acc']=$smplord_acc->get_fields('id',$where_str);
		}
		//2006/05/02 update
		$op['dept_id'] = get_dept_id();
		
		// �p�G�O manager �i�J��...
		if (substr($op['dept_id'],0,7) == "<select"){
			$op['manager_flag'] = 1;
		}	
				$op['msg']= $smpl_wi->msg->get(2);
				$op['back_str']= $back_str;
			page_display($op, 2, 4, $TPL_SMPL_WI_UNCFM_LIST);
		break;
//========================================================================================	
	case "smpl_wi_cfm_qty_edit":

		check_authority(2,3,"view");

		// �N �s�y�O ����show out ------
		//  wi �D��
			
		if(!$op = $smpl_wi->get_all($PHP_id)){    //���X�ӵ� �s�y�O�O�� ID
						$op['msg']= $smpl_wi->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
		}
//--------------------------------------------------------------------------
		//  wi_qty �ƶq��
		$where_str = " WHERE wi_id='".$op['wi']['id']."' ";
		$T_wiqty = $smpl_wiqty->search(1,$where_str);

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
		
		$back_str = "&PHP_sr_startno=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_ref=".$PHP_ref."&PHP_factory=".$PHP_factory."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh;
		$op['back_str']=$back_str;
		$back_str2 = "&PHP_sr=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_ref=".$PHP_ref."&PHP_factory=".$PHP_factory."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh;
		$op['back_str2']=$back_str2;
		$op['now_pp']=$PHP_sr;
			if (isset($PHP_msg))$op['msg'][]=$PHP_msg;
		page_display($op, 2, 3, $TPL_SMPL_WI_QTY_EDIT);			
		break;
		
	
//========================================================================================	
	case "smpl_edit_color_qty":

		check_authority(2,3,"view");
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
				$smpl_wiqty->update_field('qty', $tmp_qty_csv, $wqty_id[$i]);				
				$tmp_qty_csv='';
			}
			
		//xxxxx�q��ƶqxxxx
		$s_ord=$smpl_ord->get($PHP_ord_id);

		if ($s_ord['qty'] <>$PHP_ord_qty)
		{
			
			$tmp_qty = $PHP_ord_qty - $s_ord['qty'];
			$backup = $s_ord['backup']+$tmp_qty;
			$rq = $s_ord['rq'];
			if ( $backup < 0)
			{
				$rq = $rq+$backup;
				$backup = 0;
			}
			// �g�J total qty
			$parm=array('field_name'	=>	'qty',
									'field_value'	=>	$PHP_ord_qty,
									'id'					=>	$PHP_ord_id
			);
			$smpl_ord->update_field($parm);
			// �g�J back up qty
			$parm=array('field_name'	=>	'backup',
									'field_value'	=>	$backup,
									'id'					=>	$PHP_ord_id
			);
			$smpl_ord->update_field($parm);
			// �g�J require qty
			$parm=array('field_name'	=>	'rq',
									'field_value'	=>	$rq,
									'id'					=>	$PHP_ord_id
			);
			$smpl_ord->update_field($parm);
			
		}

		$message="success edit sample order qty on wi cfm:[".$s_ord['num']."]";
		$log->log_add(0,"21E",$message);	
		$log->log_add(0,"23E",$message);	
			
			$op['msg'] = $wi->msg->get(2);
			$PHP_back_str2 = "&PHP_sr=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_ref=".$PHP_ref."&PHP_factory=".$PHP_factory."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh;
			
			$redir_str=$PHP_SELF.'?PHP_action=smpl_wi_view&PHP_id='.$PHP_wi_id.$PHP_back_str2.'&PHP_cfm_view=1&PHP_msg='.$message;
			redirect_page($redir_str);
			break;
	
		
//=======================================================
	case "do_smpl_wi_cfm":
		check_authority(2,4,"view");
		
		$parm = array(	"order_num" =>	$PHP_order_num,
										"id"				=>	$PHP_id,
										"date"			=>	$dt['date_str'],
										"user"			=>	$GLOBALS['SCACHE']['ADMIN']['login_id']
					  );
		
		if (isset($PHP_msg))
		{
		if (substr($PHP_msg,0,1)<> 's')
		{
			//echo $PHP_msg;
			
			if ($PHP_msg == "ERROR! Please check Sample Order Q'TY and Sample WI Q'TY first!")
			{
				//echo "here";
				$redir_str=$PHP_SELF.'?PHP_action=smpl_wi_cfm_qty_edit&PHP_id='.$PHP_id.$PHP_back_str2.'&PHP_cfm_view=1&PHP_msg='.$PHP_msg;
				redirect_page($redir_str);
				break;		
			}
			$redir_str=$PHP_SELF.'?PHP_action=smpl_wi_view&PHP_id='.$PHP_id.$PHP_back_str2.'&PHP_cfm_view=1&PHP_msg='.$PHP_msg;
			redirect_page($redir_str);
			break;
		}
		}
		$f1 = $smpl_wi->cfm($parm);
		if (!$f1) {
			$op['msg']= $smpl_wi->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}		
		$message="success CFM WI:[".$PHP_wi."]";
		$log->log_add(0,"24A",$message);
		
			$parm_update = array($PHP_id,'last_update',$dt['date_str']);
			$smpl_wi->update_field($parm_update);
			$parm_update = array($PHP_id,'updator',$GLOBALS['SCACHE']['ADMIN']['login_id']);
			$smpl_wi->update_field($parm_update);

		// --- �P�_�p���� status   ######  ---------------
		$smpl = $smpl_ord->get($PHP_smpl_id);  //���X�ӵ��O��
		if (!$smpl) {
			$op['msg'] = $smpl_ord->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		if ($PHP_revise == 0)
		{
				$parm=array($PHP_id,'revise','1');
				$smpl_wi->update_field($parm);
		}

	if ($smpl['status'] < 4)
	{
		// ���g�J  smpl_ord ���� wi_date  ---------------
		$argu['field_name'] = 'wi_date';
		$argu['field_value'] = $TODAY;
		$argu['id'] = $PHP_smpl_id;
		if(!$ord = $smpl_ord->update_field($argu)){
			$op['msg'][] = $smpl_ord->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		} 
		if ($smpl['pttn_suppt'] && $smpl['rcvd_pttn'] == '0000-00-00'){
			$new_status = 1;
		}elseif(!$smpl['fab_substu'] && $smpl['rcvd_fab'] == '0000-00-00'){
			$new_status = 2;
		}elseif(!$smpl['acc_substu'] && $smpl['rcvd_acc'] == '0000-00-00'){
			$new_status = 3;
		}else{
			$new_status = 4;
		}
		
		// ---- �g�J  smpl_ord ���� status  ---------------
		$argu['field_name'] = 'status';
		$argu['field_value'] = $new_status;
		$argu['id'] = $PHP_smpl_id;
		if(!$ord = $smpl_ord->update_field($argu)){
			$op['msg'][] = $smpl_ord->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		} 
		# �O���ϥΪ̰ʺA
		$message1 = "CFM received W/I of [".$PHP_wi."] on:".$TODAY;
		$log->log_add(0,"27A",$message1);
	}
		
		if (!$op = $smpl_wi->search_uncfm('wi')) {
			$op['msg']= $wi->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		for ($i=0; $i<sizeof($op['wi']); $i++)
		{
			$where_str="WHERE smpl_code = '".$op['wi'][$i]['wi_num']."'";
			$op['wi'][$i]['lots']=$smplord_lots->get_fields('id',$where_str);
			$op['wi'][$i]['acc']=$smplord_acc->get_fields('id',$where_str);
		}

		$back_str="&PHP_dept_code=&PHP_style_code=&PHP_cust=&PHP_num=&PHP_factory=&PHP_ref=&PHP_etdstr=&PHP_etdfsh=";

		//2006/05/02 update
		$op['dept_id'] = get_dept_id();
		
		// �p�G�O manager �i�J��...
		if (substr($op['dept_id'],0,7) == "<select"){
			$op['manager_flag'] = 1;
		}
		
				$op['msg'][]= $message;
				$op['back_str']= $back_str;
			page_display($op, 2, 4, $TPL_SMPL_WI_UNCFM_LIST);
		break;

//=======================================================
	case "revise_smpl_wi":
		check_authority(2,3,"edit");

//�R��Bom�������
		$f1=$smpl_bom->del_lots($PHP_id,1);
		$f2=$smpl_bom->del_acc($PHP_id,1);	

		// �N �s�y�O ����show out ------
		//  wi �D�� ��X��		
		
		if(!$op = $smpl_wi->get_all($PHP_id)){    //���X�ӵ� �s�y�O�O��
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}
		
//�˵��O�_�i�H�ק�size(�Y�O�_�s�bcolorway)
		if ($smpl_wi->check_size($op['wi']['style_code']))
		{
			$op['size_edit']=1;
		}

//revise���Ƶ���J		
			$parm['date'] 		= '0000-00-00 00:00:00';
			$parm['user'] 		= "";
			$parm['rev'] 		= $op['wi']['revise']+1;
			$parm['id'] 		= $op['wi']['id'];
			$parm['order_num'] 	= $op['wi']['smpl_id'];
	

				if(!$T_updt =	$smpl_wi->revise($parm)){    
								$op['msg']= $wi->msg->get(2);
								$layout->assign($op);
								$layout->display($TPL_ERROR);  		    
								break;
				}		
			$op['wi']['revise']=$parm['rev'];

// ���g�J  smpl_ord ���� wi_date  ---------------
			$argu['field_name'] = 'wi_date';
			$argu['field_value'] = '0000-00-00';
			$argu['id'] = $op['smpl']['id'];
		if(!$ord = $smpl_ord->update_field($argu)){
			$op['msg'][] = $smpl_ord->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
		} 
// ��� status   ######  -----------------
// �g�J  smpl_ord ���� status  ---------------
			$argu['field_name'] = 'status';
			$argu['field_value'] = 0;
			$argu['id'] =  $op['smpl']['id'];
		if(!$ord = $smpl_ord->update_field($argu)){
			$op['msg'][] = $smpl_ord->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
		} 

		//  wi_qty �ƶq�� -----------------------------------------------------------------
		$where_str = " WHERE wi_id='".$PHP_id."' ";
		$T_wiqty = $smpl_wiqty->search(1,$where_str);

		// ���X size_scale --------
		$size_A = $size_des->get($op['smpl']['size']);
		$op['smpl']['size_scale']=$size_A['size_scale'];
			//????????????????????????????????????? �`�N ���ɨS�� size type ��Ʈ�??????
		$sizing = explode(",", $size_A['size']);


//search����O��			
		if (isset($PHP_dept_code))
		{
			$back_str = "&PHP_sr_startno=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_ref=".$PHP_ref."&PHP_factory=".$PHP_factory."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh;
			$op['back_str']=$back_str;
			$back_str2 = "&PHP_sr=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_ref=".$PHP_ref."&PHP_factory=".$PHP_factory."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh;
			$op['back_str2']=$back_str2;

		}else{
			$op['back_str']=$PHP_back_str;
			$op['back_str2']=$PHP_back_str2;
		}
		// ���X size table-------------------------------------------
		$op['show_del_breakdown'] = show_del_breakdown($T_wiqty,$sizing,$PHP_back_str2,$size_A['base_size']);
		$op['edit_breakdown'] = edit_breakdown($sizing,$PHP_back_str2,$size_A['base_size']);

		// ���X unit�� combo
		$op['unit'] = $arry2->select($APPAREL_UNIT,$op['wi']['unit'],'PHP_unit','select','');  	

		// creat sample type combo box
		$sample_def = $smpl_type->get_fields('smpl_type');   // ���X �˥����O
		$op['smpl_type'] =  $arry2->select($sample_def,$op['wi']['smpl_type'],'PHP_smpl_type','select','');  	
			
		$op['msg'] = $wi->msg->get(2);
		
		$op['new_revise'] = $op['wi']['revise'];

		page_display($op, 2, 3, $TPL_SMPL_WI_REVISE);
		break;

//=======================================================
		case "do_smpl_wi_revise_show":


		check_authority(2,3,"edit");
		$argv = array(	"id"			=>  $PHP_wi_id,
						"wi_num"		=>	$PHP_wi_num,
						"smpl_id"		=>	$PHP_smpl_id,
						"version"		=>	$PHP_version,
						"etd"			=>	$PHP_etd,
						
						"unit"			=>	$PHP_unit,
						"lots_est_array"	=>	$PHP_lots_est,
						"acc_est_array"		=>	$PHP_acc_est,
						"updator"		=>	$GLOBALS['SCACHE']['ADMIN']['login_id']
			);
			
		if(!$f1=$smpl_wi-> check2($argv))
		{
			// �N �s�y�O ����show out ------
			//  wi �D�� ��X��			
			if(!$op = $smpl_wi->get_all($PHP_wi_id)){    //���X�ӵ� �s�y�O�O��
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
			}
		
			//�˵��O�_�i�H�ק�size(�Y�O�_�s�bcolorway)
			if ($smpl_wi->check_size($op['wi']['style_code']))
			{
				$op['size_edit']=1;
			}
			//  wi_qty �ƶq�� -----------------------------------------------------------------
			$where_str = " WHERE wi_id='".$PHP_wi_id."' ";
			$T_wiqty = $smpl_wiqty->search(1,$where_str);

			// ���X size_scale --------
			$size_A = $size_des->get($op['smpl']['size']);
			$op['smpl']['size_scale']=$size_A['size_scale'];
			//????????????????????????????????????? �`�N ���ɨS�� size type ��Ʈ�??????
			$sizing = explode(",", $size_A['size']);


		//search����O��			
			$op['back_str']=$PHP_back_str;
			$op['back_str2']=$PHP_back_str2;
		// ���X size table-------------------------------------------
			$op['show_del_breakdown'] = show_del_breakdown($T_wiqty,$sizing,$PHP_back_str2,$size_A['base_size']);
			$op['edit_breakdown'] = edit_breakdown($sizing,$PHP_back_str2,$size_A['base_size']);
		// ���X unit�� combo
			$op['unit'] = $arry2->select($APPAREL_UNIT,$op['wi']['unit'],'PHP_unit','select','');  	
			
			$op['msg'] = $smpl_wi->msg->get(2);
		
			$op['new_revise'] = $op['wi']['revise'];

			page_display($op, 2, 3, $TPL_SMPL_WI_REVISE);
			break;
		}
		if(!$op['wi'] = $smpl_wi->get($argv['id'])){    //���X�ӵ� �s�y�O�O��
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}
		if (!$argv['etd'])$argv['etd']=$op['wi']['etd'];

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
			$smpl2['field_name']='size';
			$smpl2['field_value']=$size_id;
			$smpl2['id']=$PHP_smpl_id;
			$f1 = $smpl_ord->update_field($smpl2);
		}
		$smpl3['field_name']='etd';
		$smpl3['field_value']=$argv['etd'];
		$smpl3['id']=$PHP_smpl_id;
		$f1 = $smpl_ord->update_field($smpl3);
		
		$updt = array(	"etd"			=>	$argv['etd'],
						"unit"			=>	$argv['unit'],
						"version"		=>	$argv['version'],
						"updator"		=>	$argv['updator'],
						"id"			=>	$argv['id']
			);
				if(!$smpl_wi->edit($updt)){
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
				}


		// �N�D�Ʈ� �ק�� �w���g�J �D�Ʈƨϥ��ɤ� lots_use, acc_use  ....
		// �D�� -----------------------------------------------------------------------------
		if(($argv['lots_est_array'])){
			while(list($key,$val) = each($argv['lots_est_array'])){
				if($val){
					$lots = array($key,"est_1",$val);
					if(!$smplord_lots->update_field($lots)){
						$op['msg']= $smpl_lots->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
					}

					$updt = array($key,"ester_1",$argv['updator']);
					if(!$smplord_lots->update_field($updt)){
						$op['msg']= $smpl_lots->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
					}
				}
			}
		}
		// �Ʈ� ------------------------------------------------------------------------------
		if(($argv['acc_est_array'])){
			while(list($key,$val) = each($argv['acc_est_array'])){
				if($val){
					$acc = array($key,"est_1",$val);
					if(!$smplord_acc->update_field($acc)){
						$op['msg']= $smpl_acc->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
					}

					$updt = array($key,"ester_1",$argv['updator']);
					if(!$smplord_acc->update_field($updt)){
						$op['msg']= $smpl_acc->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
					}
				}
			}
		}

		//--------------- �����ק�g�J------------------

		//--------------- �C�X��ᤧ�O��------------------
		//  wi �D�� --------------------------------------------------------------------------------
		if(!$op = $smpl_wi->get_all($argv['id'])){    //���X�ӵ� �s�y�O�O��
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}
		//  wi_qty �ƶq�� --------------------------------------------------------------------------
		$where_str = " WHERE wi_id='".$op['wi']['id']."' ";
		$T_wiqty = $smpl_wiqty->search(1,$where_str);

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

		//�ɮצC��
		$op['done'] = $fils->get_smpl_file($op['wi']['wi_num']);
	
		# �O���ϥΪ̰ʺA
		$message = "Successfully Revise wi:[".$op['wi']['wi_num']."]�E";
		$log->log_add(0,"23E",$message);
		$op['msg'][] = $message;
 		$op['back_str']=$PHP_back_str;
 		$op['back_str2']=$PHP_back_str2;

		page_display($op, 2, 3, $TPL_SMPL_WI_REVISE_SHOW);
		break;



//=======================================================
//		 �Ͳ�����
//=======================================================


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
	page_display($op, 2, 3, $TPL_SMPL_TI_COPY_SEARCH);
	break;


//========================================================================================	
	case "smpl_ti_copy_view":

		check_authority(2,3,"view");

		// �N �s�y�O ����show out ------
		//  wi �D��

			if(!$op = $smpl_wi->get_all($PHP_id)){    //���X�ӵ� �s�y�O�O�� ID
						$op['msg']= $smpl_wi->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
			}
//--------------------------------------------------------------------------
		//  wi_qty �ƶq��
		$where_str = " WHERE wi_id='".$op['wi']['id']."' ";
		$T_wiqty = $smpl_wiqty->search(1,$where_str);

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

//�ɮצC��
		$op['done'] = $fils->get_smpl_file($op['wi']['wi_num']);

		if(count($op['ti']))
		{
		 for ($i=0; $i<sizeof($op['ti']); $i++)
			$op['ti'][$i]['detail'] = str_replace( chr(13).chr(10), "<br>", $op['ti'][$i]['detail'] );
		}

    $op['org_smpl_id'] = $PHP_smpl_id;
    $op['copy_search'] = "&PHP_smpl_id=".$PHP_smpl_id."&PHP_c_dept=".$PHP_c_dept."&PHP_c_cust=".$PHP_c_cust;

		$back_str = "&PHP_sr_startno=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_ref=".$PHP_ref."&PHP_factory=".$PHP_factory."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh;
		$op['back_str']=$back_str;
		$back_str2 = "&PHP_sr=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_ref=".$PHP_ref."&PHP_factory=".$PHP_factory."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh;
		$op['back_str2']=$back_str2;

		page_display($op, 3, 5, $TPL_SMPL_TI_COPY_VIEW);			
		break;

//=======================================================
    case "do_smpl_ti_copy":
		check_authority(2,3,"add");
		// �N �s�y�O ����show out ------
		//  wi �D��
		$f1=$smpl_wi->copy_ti($PHP_id,$PHP_smpl_id);
		
		if(!$op = $smpl_wi->get_all($PHP_smpl_id)){    //���X�ӵ� �s�y�O�O��
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}
//--------------------------------------------------------------------------
		//  wi_qty �ƶq��
			$where_str = " WHERE wi_id='".$op['wi']['id']."' ";
		$T_wiqty = $smpl_wiqty->search(1,$where_str);

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
		//---------------------------------------------------------------
		//�ɮצC��
		$op['done'] = $fils->get_smpl_file($op['wi']['wi_num']);

		$op['msg'] = $ti->msg->get(2);
		// creat combo
		$op['ti_item_select'] = $arry2->select($TI_ITEM,'','PHP_item','select',''); 

		$op['back_str']=$PHP_back_str;
		$op['back_str2']=$PHP_back_str2;

		$message = "Add WI:[".$op['wi']['wi_num']."] Description Copy�C";
		$op['msg'][] = $message;
		$log->log_add(0,"23D",$message);


		page_display($op, 2, 3, $TPL_SMPL_TI_ADD);
		break;


//=======================================================
    case "smpl_ti_add":
		check_authority(2,3,"add");
		// �N �s�y�O ����show out ------
		//  wi �D��
		if(!$op = $smpl_wi->get_all($PHP_id)){    //���X�ӵ� �s�y�O�O��
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}
//--------------------------------------------------------------------------
		//  wi_qty �ƶq��
			$where_str = " WHERE wi_id='".$op['wi']['id']."' ";
		$T_wiqty = $smpl_wiqty->search(1,$where_str);

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
		//---------------------------------------------------------------
		//�ɮצC��
		$op['done'] = $fils->get_smpl_file($op['wi']['wi_num']);

		$op['msg'] = $ti->msg->get(2);
		// creat combo
		$op['ti_item_select'] = $arry2->select($TI_ITEM,'','PHP_item','select',''); 
		if (isset($PHP_dept_code))
		{
		$back_str = "&PHP_sr_startno=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_ref=".$PHP_ref."&PHP_factory=".$PHP_factory."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh;
		$op['back_str']=$back_str;
		$back_str2 = "&PHP_sr=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_ref=".$PHP_ref."&PHP_factory=".$PHP_factory."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh;
		$op['back_str2']=$back_str2;
		}else{
			$op['back_str']=$PHP_back_str;
			$op['back_str2']=$PHP_back_str2;
		}
		if (isset($PHP_msg))$op['msg'][]=$PHP_msg;
		page_display($op, 2, 3, $TPL_SMPL_TI_ADD);
		break;

//=======================================================
    case "do_smpl_ti_add":
		check_authority(2,3,"add");
		$parm = array(	"wi_id"		=>	$PHP_wi_id,
						"item"		=>	$PHP_item,
						"detail"	=>	$PHP_detail
			);
		// ���X ���Ѫ���� -----
		$dt = decode_date(1);
		$parm['today'] = $dt['date'];


		$op['ti0'] = $parm;    // �׶}�P��Ʈw�I�s�X�Ӥ��O���Ĭ�.....

		$f1 = $smpl_ti->add($parm);
	if (!$f1) {   // ���J�������D��........
		$op['ti_item_select'] = $arry2->select($TI_ITEM,$op['ti0']['item'],'PHP_item','select','');  	
	} else {
		$op['ti_item_select'] = $arry2->select($TI_ITEM,'','PHP_item','select',''); 
		$op['ti0']['detail'] = "";
	}

// �N �s�y�O ����show out ------
		//  wi �D��
		if(!$op = $smpl_wi->get_all($PHP_wi_id)){    //���X�ӵ� �s�y�O�O��
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}
//--------------------------------------------------------------------------
		//  wi_qty �ƶq��
			$where_str = " WHERE wi_id='".$op['wi']['id']."' ";
		$T_wiqty = $smpl_wiqty->search(1,$where_str);

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


//�ɮצC��
		$op['done'] = $fils->get_smpl_file($op['wi']['wi_num']);

			

			// creat combo
		$op['ti_item_select'] = $arry2->select($TI_ITEM,'','PHP_item','select','');  	

					# �O���ϥΪ̰ʺA
		$message = "Add WI:[".$op['wi']['wi_num']."] Description �C";
		$op['msg'][] = $message;
		$log->log_add(0,"23D",$message);
		$op['back_str']=$PHP_back_str;
		$op['back_str2']=$PHP_back_str2;
			page_display($op, 2, 3, $TPL_SMPL_TI_ADD);  
		break;
		
//=======================================================
    case "do_smpl_ti_edit":
		check_authority(2,3,"add");
		
		$parm['field_name'] = 'detail';
		$parm['field_value'] = $PHP_detail;
		$parm['id'] = $PHP_id;

		$f1 = $smpl_ti->update_field($parm);

	$op['ti_item_select'] = $arry2->select($TI_ITEM,'','PHP_item','select',''); 
	$op['ti0']['detail'] = "";
	// �N �s�y�O ����show out ------
		//  wi �D��
		if(!$op = $smpl_wi->get_all($PHP_wi_id)){    //���X�ӵ� �s�y�O�O��
					$op['msg']= $smpl_wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}

//--------------------------------------------------------------------------
		//  wi_qty �ƶq��
			$where_str = " WHERE wi_id='".$op['wi']['id']."' ";
		$T_wiqty = $smpl_wiqty->search(1,$where_str);

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

//�ɮצC��
		$op['done'] = $fils->get_smpl_file($op['wi']['wi_num']);			
	// creat combo
		$op['ti_item_select'] = $arry2->select($TI_ITEM,'','PHP_item','select','');  	

					# �O���ϥΪ̰ʺA
		$message = "Edit WI:[".$op['wi']['wi_num']."] Description�C";
		$op['msg'][] = $message;
		$log->log_add(0,"23E",$message);
		$op['back_str']=$PHP_back_str;
		$op['back_str2']=$PHP_back_str2;
			page_display($op, 2, 3, $TPL_SMPL_TI_ADD);  
		break;		
		
		
//=======================================================
    case "do_smpl_ti_del":
		
		check_authority(2,3,"edit");
		$parm = array(	"id"	=>	$id,		
						"wi_id" =>	$wi_id,
						"item"	=>	$item
		);

		$f1 = $smpl_ti->del($parm['id']);
		if (!$f1) {
			$op['msg'] = $ti->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}


		// �N �s�y�O ����show out ------
		//  wi �D��
		if(!$op = $smpl_wi->get_all($parm['wi_id'])){    //���X�ӵ� �s�y�O�O��
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}

//--------------------------------------------------------------------------
		//  wi_qty �ƶq��
		$where_str = " WHERE wi_id='".$op['wi']['id']."' ";
		$T_wiqty = $smpl_wiqty->search(1,$where_str);

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

//�ɮצC��
		$op['done'] = $fils->get_smpl_file($op['wi']['wi_num']);
// creat combo
		$op['ti_item_select'] = $arry2->select($TI_ITEM,'','PHP_item','select','');  	

# �O���ϥΪ̰ʺA
		$message = "Delete WI:[".$op['wi']['wi_num']."] Description[".$parm['item']."]�C";
		$log->log_add(0,"23D",$message);
		$op['msg'][]=$message;
		$back_str = "&PHP_sr_startno=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_ref=".$PHP_ref."&PHP_factory=".$PHP_factory."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh;
		$op['back_str']=$back_str;
		$back_str2 = "&PHP_sr=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_ref=".$PHP_ref."&PHP_factory=".$PHP_factory."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh;
		$op['back_str2']=$back_str2;
			page_display($op, 2, 3, $TPL_SMPL_TI_ADD);	
		break;		


//=======================================================
    case "do_smpl_ws_file_del":
		
		check_authority(2,3,"edit");	

		$f1 = $fils->del_file($PHP_talbe,$PHP_id);
		if (!$f1) {
			$op['msg'] = $fils->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}


		// �N �s�y�O ����show out ------
		//  wi �D��
	if(!$op = $smpl_wi->get_all($PHP_wi_id)){    //���X�ӵ� �s�y�O�O��
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}

//--------------------------------------------------------------------------
		//  wi_qty �ƶq��
		$where_str = " WHERE wi_id='".$op['wi']['id']."' ";
		$T_wiqty = $smpl_wiqty->search(1,$where_str);

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

//�ɮצC��
		$op['done'] = $fils->get_smpl_file($op['wi']['wi_num']);
// creat combo
		$op['ti_item_select'] = $arry2->select($TI_ITEM,'','PHP_item','select','');  	

# �O���ϥΪ̰ʺA
		$message = "Delete WI:[".$op['wi']['wi_num']."] �C";
		$log->log_add(0,"23D",$message);
		$op['msg'][]=$message;
		$back_str = "&PHP_sr_startno=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_ref=".$PHP_ref."&PHP_factory=".$PHP_factory."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh;
		$op['back_str']=$back_str;
		$back_str2 = "&PHP_sr=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_ref=".$PHP_ref."&PHP_factory=".$PHP_factory."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh;
		$op['back_str2']=$back_str2;
			page_display($op, 2, 3, $TPL_SMPL_TI_ADD);	
		break;	

//=======================================================
	case "do_smpl_ti_cfm":
		check_authority(2,4,"view");

		$parm = array($PHP_id,'ti_cfm',$TODAY);
		$f1 = $smpl_wi->update_field($parm);
		$parm = array($PHP_id,'ti_cfm_user',$GLOBALS['SCACHE']['ADMIN']['login_id']);
		$f1 = $smpl_wi->update_field($parm);

		if (!$f1) {
			$op['msg']= $smpl_wi->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}		
		
		if ($PHP_revise == 0)
		{
				$parm=array($PHP_id,'ti_rev','1');
				$smpl_wi->update_field($parm);
		}
		$message="success CFM Worksheet:[".$PHP_wi."]";
		$log->log_add(0,"23A",$message);
		$redir_str=$PHP_SELF.'?PHP_action=smpl_wi_view&PHP_id='.$PHP_id.$PHP_back_str2.'&PHP_ti=1&PHP_msg='.$message;
		redirect_page($redir_str);
		break;
		
//=======================================================
	case "revise_smpl_ti":
		check_authority(2,3,"edit");
		
		$PHP_rev++;		//revies����+1
		$parm = array($PHP_id,'ti_rev',$PHP_rev);		//revies����+1�s�J��Ʈw
		$f1 = $smpl_wi->update_field($parm);
		

		if (!$f1) {
			$op['msg']= $smpl_wi->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}		

	
		$parm = array($PHP_id,'ti_cfm','0000-00-00');	//Worksheet�ܦ���cfm
		$f1 = $smpl_wi->update_field($parm);
		$parm = array($PHP_id,'ti_cfm_user','');
		$f1 = $smpl_wi->update_field($parm);
		if (!$f1) {
			$op['msg']= $smpl_wi->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}		
		$back_str2 = "&PHP_sr=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_ref=".$PHP_ref."&PHP_factory=".$PHP_factory."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh;
		$message="success Revise Worksheet:[".$PHP_wi."]";
		$log->log_add(0,"23A",$message);
		$redir_str=$PHP_SELF.'?PHP_action=smpl_ti_add&PHP_id='.$PHP_id.$back_str2.'&PHP_msg='.$message;
		redirect_page($redir_str);
		break;





		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		job 23A sample pattern upload
#		case "upload_smpl_pattern":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "upload_smpl_wi_file":

		check_authority(2,3,"add");

	//	echo $PHP_upfile;
		$filename = $_FILES['PHP_pttn']['name'];		

		$ext =  strtolower(preg_replace("/.*\.([^.]+)$/","\\1", $filename));
		$f_check = 0; //�P�_���ɦW�O�_�s�b
		for ($i=0; $i<sizeof( $GLOBALS['VALID_TYPES']); $i++)
		{
			if ($GLOBALS['VALID_TYPES'][$i] == $ext)
			{
				$f_check = 1;
				break;
			}
		}
		
//		$pttn_name = $PHP_num."_".$filename;  // �զX�ɦW
//		$pttn_name = str_replace('#','',$pttn_name);	
	//	echo $ext;
		if ($filename && $PHP_desp)
		{			
		if ($_FILES['PHP_pttn']['size'] < 2072864)
		{
			
		if ($f_check == 1){   // �W���ɪ����ɦW�� mdl �� -----

			$today = $GLOBALS['TODAY'];
			$user =  $GLOBALS['SCACHE']['ADMIN']['name'];
			$parm = array(	"file_name"		=>  $PHP_num,				
							"num"			=>  $PHP_num,
							"file_des"		=>	$PHP_desp,
							"file_user"		=>	$user,
							"file_date"		=>	$today
			);

			// upload pattern file to server
			if (!$A = $fils->upload_smpl_file($parm)){
				$op['msg'] = $fils->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
//			echo "here".$A;
			$pttn_name = $PHP_num."_".$A.".".$ext;
			$fils->	update_smpl_file($pttn_name,$A);

			$str_long=strlen($pttn_name);
			$upload = new Upload;
			$upload->setMaxSize(2072864);
			$upload->uploadFile(dirname($PHP_SELF).'/smpl_wi_file/', 'other', 16, $pttn_name );
			$upload->setMaxSize(2072864);
			if (!$upload){
				$op['msg'][] = $upload;
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			$message = "UPLOAD file of #".$PHP_num;
			$log->log_add(0,"23E",$message);
		} else {  // �W���ɪ����ɦW  �O  exe �� -----

			$message = "upload file is incorrect format ! Please re-send.";
		}
		}else{  //�W���ɦW���Ю�
			$message = "upload file is too big!!";
		}
		}else{
			$message="You don't pick any file or add any file description.";
		}	
			
		
		// �N �s�y�O ����show out ------
		//  wi �D��
		if(!$op = $smpl_wi->get_all(0,$PHP_num)){    //���X�ӵ� �s�y�O�O��
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}
		
//--------------------------------------------------------------------------
		//  wi_qty �ƶq��
		$where_str = " WHERE wi_id='".$op['wi']['id']."' ";
		$T_wiqty = $smpl_wiqty->search(1,$where_str);

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

//�ɮצC��
		$op['done'] = $fils->get_smpl_file($op['wi']['wi_num']);
		$op['msg'][] = $message;
// creat combo
		$op['ti_item_select'] = $arry2->select($TI_ITEM,'','PHP_item','select','');  	
		$op['back_str']=$PHP_back_str;
		$op['back_str2']=$PHP_back_str2;
		page_display($op, 3, 2, $TPL_SMPL_TI_ADD);	
		break;
				


		
// ---------------------- end BOM data layout [�Ʈ�] ----------------------------//-------------------------------------------------------------------------------------
//		SMPL BOM  �Ͳ��ή�  [4, 1 ]
//-------------------------------------------------------------------------------------
    case "smpl_bom":

		check_authority(2,5,"view");

		$where_str = $manager = $dept_id = '';		 
		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];   // �P�w�i�J����������
		$team = $GLOBALS['SCACHE']['ADMIN']['team_id'];  // �P�w�i�J����������(team)
		$sales_dept_ary = get_sales_dept();// ���X �~�Ȫ����� [���tK0] ------
		for ($i=0; $i<count($sales_dept_ary);$i++){
			if($user_dept == $sales_dept_ary[$i]){

				// �p�G�O�~�ȳ��i�J �hdept_code ���w�ӷ~�ȳ�---
				$dept_id = $sales_dept_ary[$i];  
			}
		}
		$op['dept_id'] = $dept_id;
		if (!$dept_id || $team<>'MD') {    // ���O�~�ȳ��H[�]���t K0 ���H ]�i�J��
			$op['manager_flag'] = 1;
			$manager_v = 1;
			//�~�ȳ��� select���
			$op['dept_id'] = $arry2->select($sales_dept_ary,"","PHP_dept_code","select",""); 
 
		} else {
			$where_str = " WHERE dept = '".$dept_id."' ";	
					
		}

								
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

		$op['msg']= $smpl_wi->msg->get(2);

		page_display($op, 2,5, $TPL_SMPL_BOM_SEARCH);		    	    
		break;		

//=======================================================
	
	case "smpl_bom_search_wi":   //  ����X�s�y�O.......

		check_authority(2,5,"view");

			if (!$op = $smpl_wi->search(1,1)) {
				$op['msg']= $smpl_wi->msg->get(2);
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

		$op['msg']= $smpl_wi->msg->get(2);				
		$back_str="&PHP_dept_code=".$PHP_dept_code."&PHP_etdstr=".$PHP_etdstr."&PHP_cust=".$PHP_cust."&PHP_wi_num=".$PHP_wi_num."&PHP_etdfsh=".$PHP_etdfsh;
		$op['back_str']=$back_str;

		page_display($op, 2,5, $TPL_SMPL_BOM);
		break;	
	
//=======================================================
    case "smpl_bom_print":   //  .......

		if(!$admin->is_power(2,5,"view")){ 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		//-------------------- �N �s�y�O ��� �Ѹ�Ʈw���X ------------------
		//  wi �D��
		if(!$wi = $smpl_wi->get(0,$PHP_id)){    //���X�ӵ� �s�y�O�O��
					$op['msg']= $smpl_wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}
		//  smpl �˥���
		if(!$smpl = $smpl_ord->get($wi['smpl_id'])){
					$op['msg']= $order->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_SMPL_ERROR);     
					break;
		}
		//-------------------- wi_qty �ƶq�� -------------------------------
			$where_str = " WHERE wi_id='".$wi['id']."' ";
		$T_wiqty = $smpl_wiqty->search(1,$where_str);

		$num_colors = count($T_wiqty);
		$op['num_colors'] = $num_colors;
		// ���X size_scale ----------------------------------------------
//		$where_str = " WHERE cust='".$wi['cust']."' AND size_scale='".$smpl['size']."' ";
		$size_A = $size_des->get($smpl['size']);
		$smpl['size_scale']=$size_A['size_scale'];
			//????????????????????????????????????? �`�N ���ɨS�� size type ��Ʈ�??????
		$sizing = explode(",", $size_A['size']);

			// ���X size table-------------------------------------------
		$reply = get_colorway_qty($T_wiqty,$sizing);

		$op['total'] = $reply['total'];   // �`���
		$data = $reply['data'];

		$colorway_name = $reply['colorway'];
		$colorway_qty = $reply['colorway_qty'];

		//-----------------------------------------------------------------

		// �ۤ���URL�M�w -----------------------------------------------------------------
			$style_dir	= "./smpl_pic/";  
			$no_img		= "./images/graydot.jpg";
			if(file_exists($style_dir.$wi['style_code'].".jpg")){
//				$wi['pic_url'] = $style_dir."s".$wi['smpl_id'].".jpg";
				$wi['pic_url'] = $style_dir.$wi['style_code'].".jpg";
			} else {
				$wi['pic_url'] = $no_img;
			}
		
		//  ���X�D�ƥήưO�� --------------------------------------------------------------
		 $op['lots_NONE']= '';
		$where_str = " WHERE smpl_code = '".$wi['style_code']."' ";
		$lots_used['lots_use'] = $smplord_lots->search(0,$where_str);  //���X�ӵ� �˥��D�ưO��

		if (!is_array($lots_used['lots_use'])) {
			$op['msg'] = $smplord_lots->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$num_lots_used = count($lots_used['lots_use']);
		if (!$num_lots_used){	$op['lots_NONE'] = "1";		
		}else{
			for ($i=0; $i<sizeof($lots_used['lots_use']); $i++)
			{
				$lots_det[$i][0]		= $lots_used['lots_use'][$i]['lots_code'];
				$lots_det[$i][1]		= $lots_used['lots_use'][$i]['lots_name'];
				$lots_det[$i][2]		= $lots_used['lots_use'][$i]['use_for'];
				
			}
		}





		//  ���X�ƮƥήưO�� --------------------------------------------------------------
		 $op['acc_NONE']= '';
		$where_str = " WHERE smpl_code = '".$wi['style_code']."' ";
		$acc_used['acc_use'] = $smplord_acc->search(0,$where_str);  //���X�ӵ� �˥��D�ưO��

		if (!is_array($acc_used['acc_use'])) {     
			$op['msg'] = $smplord_acc->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$num_acc_used = count($acc_used['acc_use']);
		if (!$num_acc_used){	$op['acc_NONE'] = "1";		
		}else{
			for ($i=0; $i<sizeof($acc_used['acc_use']); $i++)
			{
				$acc_det[$i][0]		= $acc_used['acc_use'][$i]['acc_code'];
				$acc_det[$i][1]		= $acc_used['acc_use'][$i]['acc_name'];
				$acc_det[$i][2]		= $acc_used['acc_use'][$i]['use_for'];
				
			}

		}




		//  ���X  BOM  �D�ưO�� --------------------------------------------------------------
		 $op['bom_lots_NONE']= '';
		$where_str = " WHERE wi_id = '".$wi['id']."' ";
		$bom_lots = $smpl_bom->search_lots(0,$where_str);  //���X�ӵ� bom ��ALL�D�ưO��

		if (!is_array($bom_lots['lots'])) {
			$op['msg'] = $smpl_bom->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$num_bom_lots = count($bom_lots['lots']);
		if (!$num_bom_lots){	$op['bom_lots_NONE'] = "1";		}

		//  ���X  BOM  �ƮưO�� --------------------------------------------------------------
		 $op['bom_acc_NONE']= '';
		$where_str = " WHERE wi_id = '".$wi['id']."' ";
		$bom_acc = $smpl_bom->search_acc(0,$where_str);  //���X�ӵ� bom ��ALL�ƮưO��

		if (!is_array($bom_acc['acc'])) {
			$op['msg'] = $smpl_bom->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$num_bom_acc = count($bom_acc['acc']);
		if (!$num_bom_acc){	$op['bom_acc_NONE'] = "1";		}

		// --------------- �D�� ------------ ���X layout  array....
			$data_m = array();
		if (!$op['lots_NONE']){   // �˥����D�ƥήưO��---

		  for ($i=0;$i<$num_lots_used;$i++){   // �� �D�ƥήưO���v�� loop
				$no_found_flag = 1;  // �w�] bom���䤣���ƪ��X��
			for ($k=0;$k<count($bom_lots['lots']);$k++){  // �� bom �D�ưO�� �v�� loop
				$T_bom = array();
				if ($bom_lots['lots'][$k]['lots_used_id'] == $lots_used['lots_use'][$i]['id'] ){
					$no_found_flag = "";  // �X�Ч���
					
					$T_bom[0]	= $lots_used['lots_use'][$i]['lots_code'];
					$T_bom[1]	= $lots_used['lots_use'][$i]['use_for'];
					$T_bom[2]	= $lots_used['lots_use'][$i]['est_1'].' '.$lots_used['lots_use'][$i]['unit'];
					$T_bom[3]	= $bom_lots['lots'][$k]['color'];
					$T_bom[4]	= '';  // layout �����u �]���[�J�@�Ӫŭ�

						$T_qty = explode(",", $bom_lots['lots'][$k]['qty']);
					for ($p=0;$p<$num_colors;$p++){
						if ($T_qty[$p]==0){	$T_qty[$p] = " ";	}
						$T_bom[5 + $p] = $T_qty[$p];
					}
					$T_bom[$p+5]			= '';
					$T_bom[$p+6]			= array_sum($T_qty)." ".$lots_used['lots_use'][$i]['unit'];
					$data_m[]	= $T_bom;
				}   // BOM �ɤ��� �D��id ���� �ή��ɪ�id ����

			}  // end for K... bom�ɤ��D�� �v�� ����
			if ($no_found_flag){  // �� bom�ɤ��L�o���ήƪ�id��

				$T_bom['lots_code']		= $lots_used['lots_use'][$i]['lots_code'];
				$T_bom['est_1']				= $lots_used['lots_use'][$i]['est_1'];
				$T_bom['unit']				= $lots_used['lots_use'][$i]['unit'];
				$T_bom['use_for']			= $lots_used['lots_use'][$i]['use_for'];

				$data_m[]		= $T_bom;
			}
		  }  // end for �� �D�ƥήưO���v�� loop

		}   // end---- if (!$op['lots_NONE']) 
		// ---------------------- end BOM data layout [�D��] ----------------------------

		// --------------- �Ʈ� ------------ ���X $bom_acc_list[]  array....
			$data_a = array();
		if (!$op['acc_NONE']){   // �˥����ƮƥήưO��---
			unset($T_bom);
			$T_bom = array();
		  for ($i=0;$i<$num_acc_used;$i++){   // �� �ƮƥήưO���v�� loop
				$no_found_flag = 1;  // �w�] bom���䤣���ƪ��X��
			for ($k=0;$k<count($bom_acc['acc']);$k++){  // �� bom �ƮưO�� �v�� loop
				$T_bom = array();
				if ($bom_acc['acc'][$k]['acc_used_id'] == $acc_used['acc_use'][$i]['id'] ){
					$no_found_flag = "";  // �X�Ч���

					$T_bom[0]		= $acc_used['acc_use'][$i]['acc_code'];
					$T_bom[1]	= $acc_used['acc_use'][$i]['use_for'];
					$T_bom[2]		= $acc_used['acc_use'][$i]['est_1'].' '.$acc_used['acc_use'][$i]['unit'];
					$T_bom[3]		= $bom_acc['acc'][$k]['color'];
					$T_bom[4]	= '';  // layout �����u �]���[�J�@�Ӫŭ�

						$T_qty = explode(",", $bom_acc['acc'][$k]['qty']);
					for ($p=0;$p<$num_colors;$p++){
						if ($T_qty[$p]==0){	$T_qty[$p] = " ";	}
						$T_bom[5 + $p] = $T_qty[$p];
					}
					$T_bom[$p+5]			= '';
					$T_bom[$p+6]			= array_sum($T_qty)." ".$acc_used['acc_use'][$i]['unit'];
					$data_a[]	= $T_bom;

				}   // BOM �ɤ��� �Ʈ�id ���� �ή��ɪ�id ����

			}  // end for K... bom�ɤ��Ʈ� �v�� ����

			if ($no_found_flag){  // �� bom�ɤ��L�o���ήƪ�id��

				if (!$num_colors){	$num_span = 1;	}else{ $num_span = $num_colors; }

				$T_bom['acc_code']		= $acc_used['acc_use'][$i]['acc_code'];
				$T_bom['est_1']			= $acc_used['acc_use'][$i]['est_1'];
				$T_bom['unit']			= $acc_used['acc_use'][$i]['unit'];
				$T_bom['use_for']		= $acc_used['acc_use'][$i]['use_for'];

				$data_a[]			= $T_bom;
			}
		  }  // end for �� �D�ƥήưO���v�� loop

		}   // end---- if (!$op['acc_NONE']) 
		// ---------------------- end BOM data layout [�Ʈ�] ----------------------------
		// ---------------------- end BOM data layout table ----------------------------

//---------------------------------- ��Ʈw�@�~���� ------------------------------

include_once($config['root_dir']."/lib/class.pdf_bom.php");

$print_title = "Bill Of Material";
$wi['bcfm_date']=substr($wi['bcfm_date'],0,10);
$print_title2 = "VER.".$wi['bom_rev']."  on  ".$wi['bcfm_date'];
$creator = $wi['bcfm_user'];
$pdf=new PDF_bom();
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();

$pdf->SetFont('Arial','B',14);
// [��J] �q��򥻸��
$parm=array( 'PHP_id'		=>	$PHP_id,
			 'prt_ord'		=>	'Sample Order #',
			 'cust'			=>	$smpl['cust_iname'],
			 'ref'			=>	$smpl['ref'],
			 'dept'			=>	$smpl['dept'],
			 'size_scale'	=>	$smpl['size_scale'],
			 'style'		=>	$smpl['style'],
			 'qty'			=>	$op['total'],
			 'unit'			=>	$wi['unit'],
			 'etd'			=>	$wi['etd'],
			 'pic_url'	=>	$wi['pic_url']
			 );
			
$pdf->hend_title($parm);
	
	


		$Y = $pdf->getY();
//		$pdf->setx(20);
		$img_size = GetImageSize($wi['pic_url']);
		
		if ($img_size[0] > $img_size[1])
		{
			$pdf->Image($wi['pic_url'],10,28,50,0);
		}else{
			$pdf->Image($wi['pic_url'],10,28,0,50);
		}
		$pdf->ln();
		$Y = $pdf->getY();
		$X = $pdf->getX();
// �]�w colorway���ƶq�� - ��쪺�e  [size breakdown]
// �� 80~200 �O�d 30�� colorway���e �䥦 90���t���ƶq��
	$num_siz = count($sizing);
	$w_qty = intval(155/ ($num_siz+1));
	$w = array('40');  // �Ĥ@�欰 30�e
	for ($i=0;$i<=$num_siz;$i++){
		array_push($w, $w_qty);
	}

	$header = array_merge(array('Colorway') , $sizing);	
	$table_title = "Size Breakdown";
	$R = 100;
	$G = 85;
	$B = 85;
	if (count($sizing)){
		$pdf->Table_1(10,75,$header,$data,$w,$table_title,$R,$G,$B,$size_A['base_size']);
	}else{
		$pdf->Cell(10,75,'there is no sizing data exist !',1);
	}
		// �w�q �ϥH�U���y�� 
		$pdf->SetXY($X,$Y+55);
	$X=$X+10;		
	// �D��
		$pdf->SetFont('Big5','',10);

	// �D�Ʃ��Y


if ($num_colors && $op['lots_NONE']<> 1){
	$header1 = array('Fabric #','Placement','Consump.','color/cat.','');
	$header2 = $colorway_name;
	for ($i=0;$i<count($colorway_qty);$i++){
		$header3[$i] = $colorway_qty[$i]." ".$wi['unit'];
	}


	$w1 =  array(18,55,18,24,0.5);
	$w2 = array();
	$w_color = intval(65/$num_colors);
	for ($i=0;$i<$num_colors;$i++){
		array_push($w2, $w_color);
	}
	$w3 = array(20);
	$title = 'Fabric usage';
	$data = $data_m;
	$ll=$pdf->Table_2(10,$Y+55,$header1,$header2,$header3,$data,$w1,$w2,$w3,$title,$R,$G,$B,0);
	
	
} // if $num_colors....



		$Y = $pdf->getY();
		$X = $pdf->getX();
		$pdf->SetXY($X,$Y);


	// �Ʈ� -------------------------
		$pdf->SetFont('Big5','',10);

	// �ƮƩ��Y
if ($num_colors && $op['acc_NONE']<> 1){
	$header1 = array('Acc. #','Placement','Consump.','color/cat.','');
	$header2 = $colorway_name;
	for ($i=0;$i<count($colorway_qty);$i++){
		$header3[$i] = $colorway_qty[$i]." ".$wi['unit'];
	}

	$w1 =  array(18,55,18,24,0.5);
	$w2 = array();
	$w_color = intval(65/$num_colors);
	for ($i=0;$i<$num_colors;$i++){
		array_push($w2, $w_color);
	}
	$w3 = array(20);
	$title = 'Accessory usage';
	$data = $data_a;
	$X=$X+10;		
	$ll=$pdf->Table_2(10,$Y+10,$header1,$header2,$header3,$data,$w1,$w2,$w3,$title,$R,$G,$B,$ll);



} // if $num_colors....


$name=$wi['wi_num'].'_bom.pdf';
$pdf->Output($name,'D');

break;	
	
	
//=======================================================
    case "smpl_bom_add":

		check_authority(2,5,"add");
		//-------------------- �N BOM �s�y�O show out ------------------
		//  wi �D��
		if(!$op['wi'] = $smpl_wi->get($PHP_id)){    //���X�ӵ� �s�y�O�O��
					$op['msg']= $smpl_wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}
		//  smpl �˥���
		if(!$op['smpl'] = $smpl_ord->get($op['wi']['smpl_id'])){
					$op['msg']= $order->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}

		//-------------------- wi_qty �ƶq�� -------------------------------
		$where_str = " WHERE wi_id='".$op['wi']['id']."' ";
		$T_wiqty = $smpl_wiqty->search(1,$where_str);

		$num_colors = count($T_wiqty);
		$op['num_colors'] = $num_colors;
		// ���X size_scale ----------------------------------------------
		$size_A = $size_des->get($op['smpl']['size']);

		$op['smpl']['size_scale']=$size_A['size_scale'];
			//????????????????????????????????????? �`�N ���ɨS�� size type ��Ʈ�??????
		$sizing = explode(",", $size_A['size']);

			// ���X size table-------------------------------------------
		$reply = show_breakdown($T_wiqty,$sizing,$size_A['base_size']);
		$op['show_breakdown'] = $reply['html'];
		$op['total'] = $reply['total'];   // �`���
		//-----------------------------------------------------------------



		// �ۤ���URL�M�w ------------------------------------------------------
				$style_dir	= "./smpl_pic/";  
				$no_img		= "./images/graydot.gif";
			if(file_exists($style_dir.$op['wi']['style_code'].".jpg")){
				$op['wi']['pic_url'] = $style_dir.$op['wi']['style_code'].".jpg";
			} else {
				$op['wi']['pic_url'] = $no_img;
			}

		//  ���X�D�ƥήưO�� --------------------------------------------------------------
		 $op['lots_NONE']= '';
		$where_str = " WHERE smpl_code = '".$op['wi']['style_code']."' ";
		$lots_used['lots_use'] = $smplord_lots->search(0,$where_str);  //���X�ӵ� �˥��D�ưO��

		if (!is_array($lots_used['lots_use'])) {
			$op['msg'] = $smplord_lots->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$num_lots_used = count($lots_used['lots_use']);
		if (!$num_lots_used){	$op['lots_NONE'] = "1";		}

		//  ���X�ƮƥήưO�� --------------------------------------------------------------
		 $op['acc_NONE']= '';
		$where_str = " WHERE smpl_code = '".$op['wi']['style_code']."' ";
		$acc_used['acc_use'] = $smplord_acc->search(0,$where_str);  //���X�ӵ� �˥��D�ưO��

		if (!is_array($acc_used['acc_use'])) {     
			$op['msg'] = $smplord_acc->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$num_acc_used = count($acc_used['acc_use']);
		if (!$num_acc_used){	$op['acc_NONE'] = "1";		}

		//  ���X  BOM  �D�ưO�� --------------------------------------------------------------
		 $op['bom_lots_NONE']= '';
		$where_str = " WHERE wi_id = '".$op['wi']['id']."' ";
		$bom_lots = $smpl_bom->search_lots(0,$where_str);  //���X�ӵ� bom ��ALL�D�ưO��
		$op['bom_lots'] = $bom_lots['lots'];

		if (!is_array($bom_lots['lots'])) {
			$op['msg'] = $smpl_bom->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$num_bom_lots = count($bom_lots['lots']);
		if (!$num_bom_lots){	$op['bom_lots_NONE'] = "1";		}
		
		//  ���X  BOM  �ƮưO�� --------------------------------------------------------------
		 $op['bom_acc_NONE']= '';
		$where_str = " WHERE wi_id = '".$op['wi']['id']."' ";
		$bom_acc = $smpl_bom->search_acc(0,$where_str);  //���X�ӵ� bom ��ALL�ƮưO��
		$op['bom_acc'] = $bom_acc['acc'];

		if (!is_array($bom_acc['acc'])) {
			$op['msg'] = $smpl_bom->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$num_bom_acc = count($bom_acc['acc']);
		if (!$num_bom_acc){	$op['bom_acc_NONE'] = "1";		}

		//-------------------- ��z������� ---- for bom table TITLE------------------------------
		if ($num_colors){
			for ($i=0;$i<$num_colors;$i++){
				$op['color'][$i]['name'] = $T_wiqty[$i]['colorway'];
				$T_qty = explode(",", $T_wiqty[$i]['qty']);
				$op['color'][$i]['qty'] = array_sum($T_qty);
				$color_qty[$i] = array_sum($T_qty);
			}
		} else {
				$color_qty[] = 0;   // �קK error
				$op['colors_NONE'] = "1";
		}

	// --------------- �D�� ------------ ���X $bom_lots_list[]  array....
		$op['bom_lots_list'] = array();
	    if (!$op['lots_NONE']){   // �˥����D�ƥήưO��---
	      $op['bom_lots_list'] =bom_lots_edit($num_lots_used,$bom_lots['lots'],$lots_used['lots_use'],$num_colors, $PHP_id, $color_qty);
	    } 
	// ---------------------- end BOM data layout [�D��] ----------------------------

	// --------------- �Ʈ� ------------ ���X $bom_acc_list[]  array....
		$op['bom_acc_list'] = array();
		if (!$op['acc_NONE']){   // �˥����ƮƥήưO��---
	   		$op['bom_acc_list'] =bom_acc_edit($num_acc_used,$bom_acc['acc'],$acc_used['acc_use'],$num_colors, $PHP_id, $color_qty);	
		} 
	// ---------------------- end BOM data layout [�Ʈ�] ----------------------------

	// ��� ��i�� -----
			if (!$num_colors){	
				$op['total_fields'] = 7;   // BOM table���`����
			}else{
				$op['total_fields'] = 6 + $num_colors;   // BOM table���`����
			}
		// ---------------------- end BOM data layout table ----------------------------
		
		
	if (isset($PHP_dept_code))
	{
		$back_str="&PHP_sr_startno=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_etdstr=".$PHP_etdstr."&PHP_cust=".$PHP_cust."&PHP_wi_num=".$PHP_wi_num."&PHP_etdfsh=".$PHP_etdfsh;
		$op['back_str']=$back_str;
		$back_str2="&PHP_sr=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_etdstr=".$PHP_etdstr."&PHP_cust=".$PHP_cust."&PHP_wi_num=".$PHP_wi_num."&PHP_etdfsh=".$PHP_etdfsh;
		$op['back_str2']=$back_str2;
	}else{
		$op['back_str']=$PHP_back_str;
		$op['back_str2']=$PHP_back_str2;
	}

		page_display($op, 2,5, $TPL_SMPL_BOM_ADD);  	    
		break;	
	
	
//=======================================================
    case "do_smpl_bom_add":

		check_authority(2,5,"add");
		// �[�J bom ��
		$dt = decode_date(1);
		
		if ($PHP_mat=="lots"){
			$parm = array(	"wi_id"					=>	$PHP_wi_id,
											"color"					=>	$PHP_color,
											"lots_used_id"	=>	$PHP_mat_use_id,
											"qty"						=>	$PHP_qty,		
											"this_day"			=>	$dt['date']	
				);
			$f1 = $smpl_bom->add_lots($parm);   // �[�J
			$K = "Fabric";
		}elseif($PHP_mat=="acc"){
			$parm = array(	"wi_id"					=>	$PHP_wi_id,
											"color"					=>	$PHP_color,
											"acc_used_id"		=>	$PHP_mat_use_id,
											"qty"						=>	$PHP_qty,		
											"this_day"			=>	$dt['date']	
				);
			$f1 = $smpl_bom->add_acc($parm);   // �[�J
			$K = "Accessory";
		}else{
			$op['msg'][] = "Error ! Not point materiel catagory of BOM !";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		if (!$f1) {  // �S�����\��J��Ʈ�
			$op['msg'] = $smpl_wi->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}


		//-------------------- �N BOM �s�y�O show out ------------------
		//  wi �D��
		if(!$op['wi'] = $smpl_wi->get($PHP_wi_id)){    //���X�ӵ� �s�y�O�O��
					$op['msg']= $smpl_wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}
		//  smpl �˥���
		if(!$op['smpl'] = $smpl_ord->get($op['wi']['smpl_id'])){
					$op['msg']= $order->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}

		$message = "Successfully add wi[ ".$op['wi']['wi_num']." ]  BOM ".$K." record COLOR =[ ".$PHP_color." ]";

		//------------- wi_qty �ƶq�� ----------------------------------
			$where_str = " WHERE wi_id='".$op['wi']['id']."' ";
		$T_wiqty = $smpl_wiqty->search(1,$where_str);

		$num_colors = count($T_wiqty);
		$op['num_colors'] = $num_colors;
		// ���X size_scale ----------------------------------------------
		$size_A = $size_des->get($op['smpl']['size']);
		$op['smpl']['size_scale']=$size_A['size_scale'];
			//????????????????????????????????????? �`�N ���ɨS�� size type ��Ʈ�??????
		$sizing = explode(",", $size_A['size']);

			// ���X size table-------------------------------------------
		$reply = show_breakdown($T_wiqty,$sizing,$size_A['base_size']);
		$op['show_breakdown'] = $reply['html'];
		$op['total'] = $reply['total'];   // �`���
		//-----------------------------------------------------------------

		// �ۤ���URL�M�w ------------------------------------------------------
				$style_dir	= "./smpl_pic/";  
				$no_img		= "./images/graydot.gif";
			if(file_exists($style_dir.$op['wi']['style_code'].".jpg")){
				$op['wi']['pic_url'] = $style_dir.$op['wi']['style_code'].".jpg";
			} else {
				$op['wi']['pic_url'] = $no_img;
			}

		//  ���X�D�ƥήưO�� --------------------------------------------------------------
		 $op['lots_NONE']= '';
		$where_str = " WHERE smpl_code = '".$op['wi']['style_code']."' ";
		$lots_used['lots_use'] = $smplord_lots->search(0,$where_str);  //���X�ӵ� �˥��D�ưO��

		if (!is_array($lots_used['lots_use'])) {
			$op['msg'] = $smplord_lots->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$num_lots_used = count($lots_used['lots_use']);
		if (!$num_lots_used){	$op['lots_NONE'] = "1";		}

		//  ���X�ƮƥήưO�� --------------------------------------------------------------
		 $op['acc_NONE']= '';
		$where_str = " WHERE smpl_code = '".$op['wi']['style_code']."' ";
		$acc_used['acc_use'] = $smplord_acc->search(0,$where_str);  //���X�ӵ� �˥��D�ưO��

		if (!is_array($acc_used['acc_use'])) {     
			$op['msg'] = $smplord_acc->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$num_acc_used = count($acc_used['acc_use']);
		if (!$num_acc_used){	$op['acc_NONE'] = "1";		}

		//  ���X  BOM  �D�ưO�� --------------------------------------------------------------
		 $op['bom_lots_NONE']= '';
		$where_str = " WHERE wi_id = '".$op['wi']['id']."' ";
		$bom_lots = $smpl_bom->search_lots(0,$where_str);  //���X�ӵ� bom ��ALL�D�ưO��
		$op['bom_lots'] = $bom_lots['lots'];

		if (!is_array($bom_lots['lots'])) {
			$op['msg'] = $smpl_bom->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$num_bom_lots = count($bom_lots['lots']);
		if (!$num_bom_lots){	$op['bom_lots_NONE'] = "1";		}

		//  ���X  BOM  �ƮưO�� --------------------------------------------------------------
		 $op['bom_acc_NONE']= '';
		$where_str = " WHERE wi_id = '".$op['wi']['id']."' ";
		$bom_acc = $smpl_bom->search_acc(0,$where_str);  //���X�ӵ� bom ��ALL�ƮưO��
		$op['bom_acc'] = $bom_acc['acc'];

		if (!is_array($bom_acc['acc'])) {
			$op['msg'] = $smpl_bom->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$num_bom_acc = count($bom_acc['acc']);
		if (!$num_bom_acc){	$op['bom_acc_NONE'] = "1";		}

		//-------------------- ��z������� ---- for bom table TITLE------------------------------
		if ($num_colors){
			for ($i=0;$i<$num_colors;$i++){
				$op['color'][$i]['name'] = $T_wiqty[$i]['colorway'];
				$T_qty = explode(",", $T_wiqty[$i]['qty']);
				$op['color'][$i]['qty'] = array_sum($T_qty);
				$color_qty[$i] = array_sum($T_qty);
			}
		} else {
				$color_qty[] = 0;   // �קK error
				$op['colors_NONE'] = "1";
		}

	// --------------- �D�� ------------ ���X $bom_lots_list[]  array....
		$op['bom_lots_list'] = array();
	    if (!$op['lots_NONE']){   // �˥����D�ƥήưO��---
	      $op['bom_lots_list'] =bom_lots_edit($num_lots_used,$bom_lots['lots'],$lots_used['lots_use'],$num_colors, $PHP_wi_id, $color_qty);
	    } 
	// ---------------------- end BOM data layout [�D��] ----------------------------

	// --------------- �Ʈ� ------------ ���X $bom_acc_list[]  array....
		$op['bom_acc_list'] = array();
		if (!$op['acc_NONE']){   // �˥����ƮƥήưO��---
	   		$op['bom_acc_list'] =bom_acc_edit($num_acc_used,$bom_acc['acc'],$acc_used['acc_use'],$num_colors, $PHP_wi_id, $color_qty);	
		} 
	// ---------------------- end BOM data layout [�Ʈ�] ----------------------------

	// ��� ��i�� -----
			if (!$num_colors){	
				$op['total_fields'] = 7;   // BOM table���`����
			}else{
				$op['total_fields'] = 6 + $num_colors;   // BOM table���`����
			}
		// ---------------------- end BOM data layout table ----------------------------

					# �O���ϥΪ̰ʺA
			$log->log_add(0,"25A",$message);
			$op['msg'][] = $message;

		$back_str="&PHP_sr_startno=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_etdstr=".$PHP_etdstr."&PHP_cust=".$PHP_cust."&PHP_wi_num=".$PHP_wi_num."&PHP_etdfsh=".$PHP_etdfsh;
		$op['back_str']=$back_str;
		$back_str2="&PHP_sr=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_etdstr=".$PHP_etdstr."&PHP_cust=".$PHP_cust."&PHP_wi_num=".$PHP_wi_num."&PHP_etdfsh=".$PHP_etdfsh;
		$op['back_str2']=$back_str2;
		if (isset($PHP_rev))
		{
			page_display($op, 2,5, $TPL_SMPL_BOM_CFM_EDIT);
		}else{
			page_display($op, 2,5, $TPL_SMPL_BOM_ADD);	
		}
	    	    
		break;
		
//=======================================================
    case "smpl_bom_consum":

		check_authority(2,5,"view");
		
		if(!$op = $smpl_wi->get_all($PHP_id)){    //���X�ӵ� �s�y�O�O�� ID
			$op['msg']= $smpl_wi->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

//--------------------------------------------------------------------------
		//  wi_qty �ƶq��
		$where_str = " WHERE wi_id='".$op['wi']['id']."' ";
		$T_wiqty = $smpl_wiqty->search(1,$where_str);

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

		// ---------------------- end BOM data layout table ----------------------------
		$back_str="&PHP_sr_startno=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_etdstr=".$PHP_etdstr."&PHP_cust=".$PHP_cust."&PHP_wi_num=".$PHP_wi_num."&PHP_etdfsh=".$PHP_etdfsh;
		$op['back_str']=$back_str;
		$back_str2="&PHP_sr=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_etdstr=".$PHP_etdstr."&PHP_cust=".$PHP_cust."&PHP_wi_num=".$PHP_wi_num."&PHP_etdfsh=".$PHP_etdfsh;
		$op['back_str2']=$back_str2;
		

		page_display($op, 2,5, $TPL_SMPL_BOM_CON_ADD);		    	    
		break;	

//=======================================================
	case "do_smpl_bom_con_edit":

		check_authority(2,3,"view");
		$tmp='';
		$x=0;
		$k=0;
		foreach($PHP_lots_est as $key=>$value)
		{
			$where_str="WHERE wi_id = '$PHP_wi_id' and lots_used_id = '".$key."' order by id" ;
			
			$bom_lots = $smpl_bom->get_fields_lots('qty',$where_str);
			$lots_id = $smpl_bom->get_fields_lots('id',$where_str);
			if (count($bom_lots))
			{
				for ($i=0; $i<sizeof($bom_lots); $i++)
				{
					$tmp_bom=explode(',',$bom_lots[$i]);
					for ($j=0; $j < sizeof($tmp_bom); $j++)
					{
						if ($tmp_bom[$j]=='' ||$tmp_bom[$j]==0)
						{
							$qty=0;
						}else{
							$qty=$bom_size[$j]*$value;
						}
						$tmp=$tmp.$qty.",";
					}
					$tmp=substr($tmp,0,-1);	
					$parm_bom[$k]=array($lots_id[$i],'qty',$tmp,'smpl_bom_lots');
					$tmp='';
					$k++;
				}
			}
			$parm_lots[$x]=array($key,'est_1',$value);			
			$x++;			
		}

		$x=0;
		$k=0;
		foreach($PHP_acc_est as $key=>$value)
		{
			$where_str="WHERE wi_id = '$PHP_wi_id' and acc_used_id = '".$key."' order by id" ;
			
			$bom_acc = $smpl_bom->get_fields_acc('qty',$where_str);
			$acc_id = $smpl_bom->get_fields_acc('id',$where_str);
			if (count($bom_acc))
			{
				for ($i=0; $i<sizeof($bom_acc); $i++)
				{
					$tmp_bom=explode(',',$bom_acc[$i]);
					for ($j=0; $j < sizeof($tmp_bom); $j++)
					{
						if ($tmp_bom[$j]=='' ||$tmp_bom[$j]==0)
						{
							$qty=0;
						}else{
							$qty=$bom_size[$j]*$value;
						}
						$tmp=$tmp.$qty.",";
					}
					$tmp=substr($tmp,0,-1);	
					$parm_bom_acc[$k]=array($acc_id[$i],'qty',$tmp,'smpl_bom_acc');
					$tmp='';
					$k++;
				}
			}
			$parm_acc[$x]=array($key,'est_1',$value);			
			$x++;	
		}
		for ($i=0; $i<sizeof($parm_bom); $i++)		$smpl_bom->update_field($parm_bom[$i]);
		for ($i=0; $i<sizeof($parm_lots); $i++)		$smplord_lots->update_field($parm_lots[$i]);
		for ($i=0; $i<sizeof($parm_bom_acc); $i++)	$smpl_bom->update_field($parm_bom_acc[$i]);
		for ($i=0; $i<sizeof($parm_acc); $i++)		$smplord_acc->update_field($parm_acc[$i]);
		
		$parm=array($PHP_wi_id,'revise',($PHP_revise+1))	;
		$smpl_wi->update_field($parm);

		$redt_p="index2.php?&PHP_action=smpl_bom_view".$PHP_back_str2."&PHP_id=".$PHP_wi_id;
		redirect_page($redt_p);
		break;

		
//=======================================================
    case "smpl_bom_view":

		check_authority(2,5,"view");
		//-------------------- �N BOM �s�y�O show out ------------------
		//  wi �D��
		if(!$op['wi'] = $smpl_wi->get($PHP_id)){    //���X�ӵ� �s�y�O�O��
					$op['msg']= $smpl_wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}
		//  smpl �˥���
		if(!$op['smpl'] = $smpl_ord->get($op['wi']['smpl_id'])){
					$op['msg']= $smpl_ord->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}
		
		//-------------------- wi_qty �ƶq�� -------------------------------
		$where_str = " WHERE wi_id='".$op['wi']['id']."' ";
		$T_wiqty = $smpl_wiqty->search(1,$where_str);

		$num_colors = count($T_wiqty);
		$op['num_colors'] = $num_colors;
		// ���X size_scale ----------------------------------------------
		$size_A = $size_des->get($op['smpl']['size']);
		$op['smpl']['size_scale']=$size_A['size_scale'];
			//????????????????????????????????????? �`�N ���ɨS�� size type ��Ʈ�??????
		$sizing = explode(",", $size_A['size']);

			// ���X size table-------------------------------------------
		$reply = show_breakdown($T_wiqty,$sizing,$size_A['base_size']);
		$op['show_breakdown'] = $reply['html'];
		$op['total'] = $reply['total'];   // �`���
		//-----------------------------------------------------------------

		// �ۤ���URL�M�w ------------------------------------------------------
				$style_dir	= "./smpl_pic/";  
				$no_img		= "./images/graydot.gif";
			if(file_exists($style_dir.$op['wi']['style_code'].".jpg")){
				$op['wi']['pic_url'] = $style_dir.$op['wi']['style_code'].".jpg";
			} else {
				$op['wi']['pic_url'] = $no_img;
			}
					
		//  ���X�D�ƥήưO�� --------------------------------------------------------------
		 $op['lots_NONE']= '';
		$where_str = " WHERE smpl_code = '".$op['wi']['style_code']."' ";
		$lots_used['lots_use'] = $smplord_lots->search(0,$where_str);  //���X�ӵ� �˥��D�ưO��

		if (!is_array($lots_used['lots_use'])) {
			$op['msg'] = $smplord_lots->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$num_lots_used = count($lots_used['lots_use']);
		if (!$num_lots_used){	$op['lots_NONE'] = "1";		}

		//  ���X�ƮƥήưO�� --------------------------------------------------------------
		 $op['acc_NONE']= '';
		$where_str = " WHERE smpl_code = '".$op['wi']['style_code']."' ";
		$acc_used['acc_use'] = $smplord_acc->search(0,$where_str);  //���X�ӵ� �˥��D�ưO��

		if (!is_array($acc_used['acc_use'])) {     
			$op['msg'] = $smplord_acc->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$num_acc_used = count($acc_used['acc_use']);
		if (!$num_acc_used){	$op['acc_NONE'] = "1";		}

		//  ���X  BOM  �D�ưO�� --------------------------------------------------------------
		 $op['bom_lots_NONE']= '';
		$where_str = " WHERE wi_id = '".$op['wi']['id']."' ";
		$bom_lots = $smpl_bom->search_lots(0,$where_str);  //���X�ӵ� bom ��ALL�D�ưO��

		if (!is_array($bom_lots['lots'])) {
			$op['msg'] = $smpl_bom->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$num_bom_lots = count($bom_lots['lots']);
		if (!$num_bom_lots){	$op['bom_lots_NONE'] = "1";		}

		//  ���X  BOM  �ƮưO�� --------------------------------------------------------------
		 $op['bom_acc_NONE']= '';
		$where_str = " WHERE wi_id = '".$op['wi']['id']."' ";
		$bom_acc = $smpl_bom->search_acc(0,$where_str);  //���X�ӵ� bom ��ALL�ƮưO��

		if (!is_array($bom_acc['acc'])) {
			$op['msg'] = $smpl_bom->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$num_bom_acc = count($bom_acc['acc']);
		if (!$num_bom_acc){	$op['bom_acc_NONE'] = "1";		}

		//-------------------- ��z �� title ��������� ----------------------------------
		if ($num_colors){
			for ($i=0;$i<$num_colors;$i++){
				$op['color'][$i]['name'] = $T_wiqty[$i]['colorway'];
				$T_qty = explode(",", $T_wiqty[$i]['qty']);
				$op['color'][$i]['qty'] = array_sum($T_qty);
			}
		}else{
			$op['colors_NONE'] = "1";
		}

		// --------------- �D�� ------------ ���X $bom_lots_list[]  array....
			$op['bom_lots_list'] = array();
		if (!$op['lots_NONE']){   // �˥����D�ƥήưO��---
			$op['bom_lots_list'] =  bom_lots_view($num_lots_used,$bom_lots['lots'],$lots_used['lots_use'],$num_colors);
		}   
		// ---------------------- end BOM data layout [�D��] ----------------------------

		// --------------- �Ʈ� ------------ ���X $bom_acc_list[]  array....
			$op['bom_acc_list'] = array();
		if (!$op['acc_NONE']){   // �˥����ƮƥήưO��---
			$op['bom_acc_list'] =  bom_acc_view($num_acc_used,$bom_acc['acc'],$acc_used['acc_use'],$num_colors);
		}  
		// ---------------------- end BOM data layout [�Ʈ�] ----------------------------

		// ��� ��i�� -----
			if (!$num_colors){	
				$op['total_fields'] = 8;   // BOM table���`����
			}else{
				$op['total_fields'] = 7 + $num_colors;   // BOM table���`����
			}
		// ---------------------- end BOM data layout table ----------------------------
		$back_str="&PHP_sr_startno=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_etdstr=".$PHP_etdstr."&PHP_cust=".$PHP_cust."&PHP_wi_num=".$PHP_wi_num."&PHP_etdfsh=".$PHP_etdfsh;
		$op['back_str']=$back_str;
		$back_str2="&PHP_sr=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_etdstr=".$PHP_etdstr."&PHP_cust=".$PHP_cust."&PHP_wi_num=".$PHP_wi_num."&PHP_etdfsh=".$PHP_etdfsh;
		$op['back_str2']=$back_str2;
		
		if (isset($PHP_ex_order))
		{
			$op['edit_non']=1;
		}
		
		if (isset($PHP_cfm_view))
		{
			$op['pp']=$PHP_sr;
			page_display($op, 2,5, $TPL_SMPL_BOM_CFM_VIEW);	
			
		}else{
			page_display($op, 2,5, $TPL_SMPL_BOM_VIEW);		    	    
		}
		break;		

//=======================================================
    case "do_smpl_bom_del_ajx":

		check_authority(2,5,"edit");
		// �R�� bom ��
		
		if ($PHP_mat=="lots"){
			$T = "Fabric";
			$f1 = $smpl_bom->del_lots($PHP_bom_id);   
		}elseif($PHP_mat=="acc"){
			$T = "Accessory";
			$f1 = $smpl_bom->del_acc($PHP_bom_id);   // �[�J
		}else{
			$message = "Error ! Don't point delete information of BOM !";
		}

		if (!$f1) {  // �S�����\�R����Ʈ�
			$message = $smpl_wi->msg->get(2);
		}
			
		//-------------------- �N BOM �s�y�O show out ------------------
		//  wi �D��
		if(!$op['wi'] = $smpl_wi->get($PHP_id)){    //���X�ӵ� �s�y�O�O��
					$op['msg']= $smpl_wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}

		$message = "Delete wi[ ".$op['wi']['wi_num' ]."]  BOM ".$T."Record:[".$mat_code."] ID=[".$PHP_bom_id."]";
		
		# �O���ϥΪ̰ʺA
		$log->log_add(0,"25D",$message);
		
		echo $message;		

		exit;
		break;
		
//=======================================================
    case "smpl_bom_revise_view":

		check_authority(2,5,"view");
		//-------------------- �N �s�y�O show out ------------------
		//  wi �D��
		if(!$op['wi'] = $smpl_wi->get($PHP_id)){    //���X�ӵ� �s�y�O�O��
					$op['msg']= $smpl_wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}
		//  smpl �˥���
		if(!$op['smpl'] = $smpl_ord->get($op['wi']['smpl_id'])){
					$op['msg']= $order->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}
		//-------------------- wi_qty �ƶq�� -------------------------------
			$where_str = " WHERE wi_id='".$op['wi']['id']."' ";
		$T_wiqty = $smpl_wiqty->search(1,$where_str);

		$num_colors = count($T_wiqty);
		$op['num_colors'] = $num_colors;
		// ���X size_scale ----------------------------------------------
		$size_A = $size_des->get($op['smpl']['size']);
		$op['smpl']['size_scale']=$size_A['size_scale'];
			//????????????????????????????????????? �`�N ���ɨS�� size type ��Ʈ�??????
		$sizing = explode(",", $size_A['size']);

			// ���X size table-------------------------------------------
		$reply = show_breakdown($T_wiqty,$sizing,$size_A['base_size']);
		$op['show_breakdown'] = $reply['html'];
		$op['total'] = $reply['total'];   // �`���
		//-----------------------------------------------------------------

		// �ۤ���URL�M�w ------------------------------------------------------
				$style_dir	= "./smpl_pic/";  
				$no_img		= "./images/graydot.gif";
			if(file_exists($style_dir.$op['wi']['style_code'].".jpg")){
				$op['wi']['pic_url'] = $style_dir.$op['wi']['style_code'].".jpg";
			} else {
				$op['wi']['pic_url'] = $no_img;
			}
					
		//  ���X�D�ƥήưO�� --------------------------------------------------------------
		 $op['lots_NONE']= '';
		$where_str = " WHERE smpl_code = '".$op['wi']['style_code']."' ";
		$lots_used['lots_use'] = $smplord_lots->search(0,$where_str);  //���X�ӵ� �˥��D�ưO��

		if (!is_array($lots_used['lots_use'])) {
			$op['msg'] = $smplord_lots->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$num_lots_used = count($lots_used['lots_use']);
		if (!$num_lots_used){	$op['lots_NONE'] = "1";		}

		//  ���X�ƮƥήưO�� --------------------------------------------------------------
		 $op['acc_NONE']= '';
		$where_str = " WHERE smpl_code = '".$op['wi']['style_code']."' ";
		$acc_used['acc_use'] = $smplord_acc->search(0,$where_str);  //���X�ӵ� �˥��D�ưO��

		if (!is_array($acc_used['acc_use'])) {     
			$op['msg'] = $smplord_acc->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$num_acc_used = count($acc_used['acc_use']);
		if (!$num_acc_used){	$op['acc_NONE'] = "1";		}

		//  ���X  BOM  �D�ưO�� --------------------------------------------------------------
		 $op['bom_lots_NONE']= '';
		$where_str = " WHERE wi_id = '".$op['wi']['id']."' ";
		$bom_lots = $smpl_bom->search_lots(0,$where_str);  //���X�ӵ� bom ��ALL�D�ưO��
//		$op['bom_lots'] = $bom_lots['lots'];

		if (!is_array($bom_lots['lots'])) {
			$op['msg'] = $smpl_bom->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$num_bom_lots = count($bom_lots['lots']);
		if (!$num_bom_lots){	$op['bom_lots_NONE'] = "1";		}

		//  ���X  BOM  �ƮưO�� --------------------------------------------------------------
		 $op['bom_acc_NONE']= '';
		$where_str = " WHERE wi_id = '".$op['wi']['id']."' ";
		$bom_acc = $smpl_bom->search_acc(0,$where_str);  //���X�ӵ� bom ��ALL�ƮưO��
//		$op['bom_acc'] = $bom_acc['acc'];

		if (!is_array($bom_acc['acc'])) {
			$op['msg'] = $smpl_bom->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$num_bom_acc = count($bom_acc['acc']);
		if (!$num_bom_acc){	$op['bom_acc_NONE'] = "1";		}

		//-------------------- ��z �� title ��������� ----------------------------------
		if ($num_colors){
			for ($i=0;$i<$num_colors;$i++){
				$op['color'][$i]['name'] = $T_wiqty[$i]['colorway'];
				$T_qty = explode(",", $T_wiqty[$i]['qty']);
				$op['color'][$i]['qty'] = array_sum($T_qty);
			}
		}else{
			$op['colors_NONE'] = "1";
		}

		// --------------- �D�� ------------ ���X $bom_lots_list[]  array....
			$op['bom_lots_list'] = array();
		if (!$op['lots_NONE']){   // �˥����D�ƥήưO��---
			$op['bom_lots_list'] =  bom_lots_view($num_lots_used,$bom_lots['lots'],$lots_used['lots_use'],$num_colors);
		}   
		// ---------------------- end BOM data layout [�D��] ----------------------------

		// --------------- �Ʈ� ------------ ���X $bom_acc_list[]  array....
			$op['bom_acc_list'] = array();
		if (!$op['acc_NONE']){   // �˥����ƮƥήưO��---
			$op['bom_acc_list'] =  bom_acc_view($num_acc_used,$bom_acc['acc'],$acc_used['acc_use'],$num_colors);
		}  
		// ---------------------- end BOM data layout [�Ʈ�] ----------------------------

		// ��� ��i�� -----
			if (!$num_colors){	
				$op['total_fields'] = 8;   // BOM table���`����
			}else{
				$op['total_fields'] = 7 + $num_colors;   // BOM table���`����
			}
		// ---------------------- end BOM data layout table ----------------------------
			$message = "Successfully revise sample wi : ".$op['wi']['wi_num'];
			$log->log_add(0,"25E",$message);
			$op['msg'][] = $message;

			page_display($op, 2,5, $TPL_SMPL_BOM_REVISE_SHOW);		    	    
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//   bom_cfm      //  WI CFM...........
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "smpl_bom_uncfm":
		check_authority(2,6,"view");
		
		if (!$op = $smpl_wi->search_uncfm('bom')) {
			$op['msg']= $smpl_wi->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$back_str="&PHP_dept_code=&PHP_etdstr=&PHP_cust=&PHP_wi_num=&PHP_etdfsh=";

		//2006/05/02 update
		$op['dept_id'] = get_dept_id();
		
		// �p�G�O manager �i�J��...
		if (substr($op['dept_id'],0,7) == "<select"){
			$op['manager_flag'] = 1;
		}	
				$op['msg']= $smpl_wi->msg->get(2);
				$op['back_str']= $back_str;
			page_display($op, 2,6, $TPL_SMPL_BOM_UNCFM_LIST);
		break;
		
//=======================================================
	case "do_smpl_bom_cfm":
		check_authority(2,6,"edit");
		
		$parm = array(	"order_num" =>	$PHP_order_num,
										"id"				=>	$PHP_id,
										"date"			=>	$dt['date_str'],
										"user"			=>	$GLOBALS['SCACHE']['ADMIN']['login_id']
					  );
		$f1 = $smpl_wi->bom_cfm($parm);
		if (!$f1) {
			$op['msg']= $smpl_wi->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$message="success CFM BOM:[".$PHP_wi."]";
			
		if (!$op = $smpl_wi->search_uncfm('bom')) {
			$op['msg']= $smpl_wi->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		if ($PHP_revise == 0)
		{
				$parm=array($PHP_id,'bom_rev','1');
				$smpl_wi->update_field($parm);
		}
		$back_str="&PHP_dept_code=&PHP_etdstr=&PHP_cust=&PHP_wi_num=&PHP_etdfsh=";

		//2006/05/02 update
		$op['dept_id'] = get_dept_id();
		
		// �p�G�O manager �i�J��...
		if (substr($op['dept_id'],0,7) == "<select"){
			$op['manager_flag'] = 1;
		}
		
				$op['msg'][]= $message;
				$log->log_add(0,"26A",$message);
				$op['back_str']= $back_str;
			page_display($op, 3,6, $TPL_SMPL_BOM_UNCFM_LIST);
		break;


//=======================================================
    case "revise_smpl_bom":

		check_authority(2,5,"edit");
		//-------------------- �N BOM �s�y�O show out ------------------
		//  wi �D��	
		if(!$op['wi'] = $smpl_wi->get($PHP_id)){    //���X�ӵ� �s�y�O�O��
					$op['msg']= $smpl_wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}
		$op['wi']['bom_rev']=$op['wi']['bom_rev']+1;
		
		$parm = array(	"order_num" =>	$op['wi']['style_code'],
						"id"		=>	$PHP_id,
						"date"		=>	'0000-00-00 00:00:00',
						"user"		=>	'',
						"bom_rev"	=>	$op['wi']['bom_rev']
					  );

		
		$f1=$smpl_wi->revise_bom($parm);
		if (!$f1)
		{
			$op['msg']= $smpl_wi->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		
		
		
		
		//  smpl �˥���
		if(!$op['smpl'] = $smpl_ord->get($op['wi']['smpl_id'])){
					$op['msg']= $order->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}

		//------------- wi_qty �ƶq�� ----------------------------------
			$where_str = " WHERE wi_id='".$op['wi']['id']."' ";
		$T_wiqty = $smpl_wiqty->search(1,$where_str);

		$num_colors = count($T_wiqty);
		$op['num_colors'] = $num_colors;
		// ���X size_scale ----------------------------------------------
		$size_A = $size_des->get($op['smpl']['size']);
		$op['smpl']['size_scale']=$size_A['size_scale'];
			//????????????????????????????????????? �`�N ���ɨS�� size type ��Ʈ�??????
		$sizing = explode(",", $size_A['size']);

			// ���X size table-------------------------------------------
		$reply = show_breakdown($T_wiqty,$sizing,$size_A['base_size']);
		$op['show_breakdown'] = $reply['html'];
		$op['total'] = $reply['total'];   // �`���
		//-----------------------------------------------------------------

		// �ۤ���URL�M�w ------------------------------------------------------
				$style_dir	= "./smpl_pic/";  
				$no_img		= "./images/graydot.gif";
			if(file_exists($style_dir.$op['wi']['style_code'].".jpg")){
				$op['wi']['pic_url'] = $style_dir.$op['wi']['style_code'].".jpg";
			} else {
				$op['wi']['pic_url'] = $no_img;
			}

		//  ���X�D�ƥήưO�� --------------------------------------------------------------
		 $op['lots_NONE']= '';
		$where_str = " WHERE smpl_code = '".$op['wi']['style_code']."' ";
		$lots_used['lots_use'] = $smplord_lots->search(0,$where_str);  //���X�ӵ� �˥��D�ưO��
//		$op['lots_used'] = $lots_used['lots_use'];

		if (!is_array($lots_used['lots_use'])) {
			$op['msg'] = $smplord_lots->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$num_lots_used = count($lots_used['lots_use']);
		if (!$num_lots_used){	$op['lots_NONE'] = "1";		}

		//  ���X�ƮƥήưO�� --------------------------------------------------------------
		 $op['acc_NONE']= '';
		$where_str = " WHERE smpl_code = '".$op['wi']['style_code']."' ";
		$acc_used['acc_use'] = $smplord_acc->search(0,$where_str);  //���X�ӵ� �˥��D�ưO��
//		$op['acc_used'] = $acc_used['acc_use'];

		if (!is_array($acc_used['acc_use'])) {     
			$op['msg'] = $smplord_acc->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$num_acc_used = count($acc_used['acc_use']);
		if (!$num_acc_used){	$op['acc_NONE'] = "1";		}

		//  ���X  BOM  �D�ưO�� --------------------------------------------------------------
		 $op['bom_lots_NONE']= '';
		$where_str = " WHERE wi_id = '".$op['wi']['id']."' ";
		$bom_lots = $smpl_bom->search_lots(0,$where_str);  //���X�ӵ� bom ��ALL�D�ưO��
		$op['bom_lots'] = $bom_lots['lots'];

		if (!is_array($bom_lots['lots'])) {
			$op['msg'] = $smpl_bom->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$num_bom_lots = count($bom_lots['lots']);
		if (!$num_bom_lots){	$op['bom_lots_NONE'] = "1";		}
		
		//  ���X  BOM  �ƮưO�� --------------------------------------------------------------
		 $op['bom_acc_NONE']= '';
		$where_str = " WHERE wi_id = '".$op['wi']['id']."' ";
		$bom_acc = $smpl_bom->search_acc(0,$where_str);  //���X�ӵ� bom ��ALL�ƮưO��
		$op['bom_acc'] = $bom_acc['acc'];

		if (!is_array($bom_acc['acc'])) {
			$op['msg'] = $smpl_bom->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$num_bom_acc = count($bom_acc['acc']);
		if (!$num_bom_acc){	$op['bom_acc_NONE'] = "1";		}

		//-------------------- ��z������� ---- for bom table TITLE------------------------------
		if ($num_colors){
			for ($i=0;$i<$num_colors;$i++){
				$op['color'][$i]['name'] = $T_wiqty[$i]['colorway'];
				$T_qty = explode(",", $T_wiqty[$i]['qty']);
				$op['color'][$i]['qty'] = array_sum($T_qty);
				$color_qty[$i] = array_sum($T_qty);
			}
		} else {
				$color_qty[] = 0;   // �קK error
				$op['colors_NONE'] = "1";
		}

	// --------------- �D�� ------------ ���X $bom_lots_list[]  array....
		$op['bom_lots_list'] = array();
	    if (!$op['lots_NONE']){   // �˥����D�ƥήưO��---
	      $op['bom_lots_list'] =bom_lots_edit($num_lots_used,$bom_lots['lots'],$lots_used['lots_use'],$num_colors, $PHP_id, $color_qty);
	    } 
	// ---------------------- end BOM data layout [�D��] ----------------------------

	// --------------- �Ʈ� ------------ ���X $bom_acc_list[]  array....
		$op['bom_acc_list'] = array();
		if (!$op['acc_NONE']){   // �˥����ƮƥήưO��---
	   		$op['bom_acc_list'] =bom_acc_edit($num_acc_used,$bom_acc['acc'],$acc_used['acc_use'],$num_colors, $PHP_id, $color_qty);	
		} 
	// ---------------------- end BOM data layout [�Ʈ�] ----------------------------
			// ��� ��i�� -----
			if (!$num_colors){	
				$op['total_fields'] = 7;   // BOM table���`����
			}else{
				$op['total_fields'] = 6 + $num_colors;   // BOM table���`����
			}
		// ---------------------- end BOM data layout table ----------------------------
		
		
	if (isset($PHP_dept_code))
	{
		$back_str="&PHP_sr_startno=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_etdstr=".$PHP_etdstr."&PHP_cust=".$PHP_cust."&PHP_wi_num=".$PHP_wi_num."&PHP_etdfsh=".$PHP_etdfsh;
		$op['back_str']=$back_str;
		$back_str2="&PHP_sr=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_etdstr=".$PHP_etdstr."&PHP_cust=".$PHP_cust."&PHP_wi_num=".$PHP_wi_num."&PHP_etdfsh=".$PHP_etdfsh;
		$op['back_str2']=$back_str2;
	}else{
		$op['back_str']=$PHP_back_str;
		$op['back_str2']=$PHP_back_str2;
	}

		page_display($op, 2,5, $TPL_SMPL_BOM_CFM_EDIT);  	    
		break;		
		
					
	
		
		










#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//		 job 81-2-1    FTY SCHEDULE �u�t��Ʋ� 
//	case "fty_mon_schedule":	job 81-2-1	FTY SCHEDULE �u�t��Ʋ� 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "fty_mon_schedule":
		check_authority(8,1,"view");

		$string = $PHP_year.$PHP_month;
		$op['dist'] = array();

		$m_array = array();

			$T = (($PHP_month -2) < 1) ? 12:0;
		$mon[0] = substr($PHP_month - 2 + $T + 100,1);
			$T = (($PHP_month -1) < 1) ? 12:0;
		$mon[1] = substr($PHP_month - 1 + $T + 100,1);
		$mon[2] = $PHP_month;
			$T = (($PHP_month +1) > 12) ? 12:0;
		$mon[3] = substr($PHP_month + 1 - $T + 100,1);
			$T = (($PHP_month +2) > 12) ? 12:0;
		$mon[4] = substr($PHP_month + 2 - $T + 100,1);

		if (!$result = $capaci->search_schdl($PHP_fty,$string,500)) {   
			$op['msg']= $capaci->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$op['ttl_su'] = 0;
		$data = array(); // for GANTT chart.....

	if($result != "none"){

		for ($i = 0; $i < count($result); $i++) {   // $i �� �q�檺�`�ƶq

			$op['ord'][$i]['ord_num'] = $result[$i]['order_num'];
			$op['ord'][$i]['qty'] = $result[$i]['qty'];
			$op['ord'][$i]['su'] = $result[$i]['su'];
			$op['ord'][$i]['ets'] = $result[$i]['ets'];
			$op['ord'][$i]['etf'] = $result[$i]['etf'];
			$op['ord'][$i]['shp'] = $result[$i]['qty_shp'];
			$op['ord'][$i]['done'] = $result[$i]['qty_done'];
			$op['ord'][$i]['status'] = $result[$i]['status'];
				
			// �p�� �e���Ӥ�άd�M�������� �ð�html�� layout
			// $etp_mon ���a���~������ �Y 200503 ...
			$etp_mon = csv2array($result[$i]['fty_su']);   
			for ($k=0; $k < count($mon); $k++){   // ���Ӥ몺��ư}�C�]�w
						$op['ord'][$i][$k] = '';  //���N�Ҧ��ȳ]��null

				for ($j=0; $j < count($etp_mon); $j++){

					$mon_key = substr($etp_mon[$j],4,2);  // $mon_key ����쪺mon
					$mon_su = substr($etp_mon[$j],6);

						if($mon_key == $mon[$k]){
							$op['ord'][$i][$k] = $mon_su;
						}
				}   //for ($j=0
				
			}  //for ($k=0;

			$op['ttl_su'] = $op['ttl_su'] + $op['ord'][$i][2];
		
		} //for ($i = 0

	} else{ //if($result != "none")
		$op['record_NONE'] = "1";
	}
		$op['fty'] = $PHP_fty;
		$op['year'] = $PHP_year;
		$op['month'] = $PHP_month;
		$op['mon'] = $mon;

		//  �j���N capaci ���� ��� schedule ��s~~ �D�n�O�٨S��쬰����|���t��
		//  check and ��a�ާ@�L  revise �� �A�T�{ �֥i�q�� ���e�� ���S��
		//  �Ȯɥαj����s���覡 ���[��S���� �Adisable�o�ӵ{��
			
				$m_key = "m".$PHP_month;
			if ($PHP_su <> $op['ttl_su']){
				if (!$update = $capaci->update_field($PHP_fty,$PHP_year,"schedule",$m_key,$op['ttl_su'])) {   
					$op['msg']= "cannot update capacity database !, please contact system Administraor";
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
				}
			}

	//GANTT CHART -----------------------------------------	
//�Ѽ�
$top_date = $PHP_year.'-'.$PHP_month.'-'.'01';
$btm_date = $PHP_year.'-'.$PHP_month.'-'.getDaysInMonth($PHP_month,$PHP_year);

include_once($config['root_dir']."/lib/src/jpgraph.php");
include_once($config['root_dir']."/lib/src/jpgraph_gantt.php");

$graph = new GanttGraph(700);

$graph->title->Set("FACTORY ORDER SCHEDULE MONTHY Chart");
$graph->subtitle->Set("FTY: ".$PHP_fty." schedule plan of month:[ ".$PHP_month." ]");

// Setup some "very" nonstandard colors
$graph->SetMargin(15,50,20,40);
$graph->title->SetFont(FF_ARIAL,FS_BOLD,10);
$graph->SetMarginColor('lightgreen@0.8');
$graph->SetBox(true,'yellow:0.6',2);
$graph->SetFrame(true,'darkgreen',4);
$graph->scale->divider->SetColor('yellow:0.6');
$graph->scale->dividerh->SetColor('yellow:0.6');
$graph->SetShadow();

$graph->SetDateRange($top_date,$btm_date);

// Display month and year scale with the gridlines
$graph->ShowHeaders(GANTT_HWEEK | GANTT_HDAY | GANTT_HMONTH | GANTT_HYEAR);
$graph->scale->week->SetStyle(WEEKSTYLE_FIRSTDAY2);

$graph->scale->month->SetFontColor("white");
$graph->scale->month->SetBackgroundColor("teal");

$graph->scale->month->grid->SetColor('gray');
$graph->scale->month->grid->Show(true);
$graph->scale->year->grid->SetColor('gray');
$graph->scale->year->grid->Show(true);

// set TODAY Vline
$vline = new GanttVline($TODAY,'Today','red',2,'solid');
$vline->SetDayOffset(0.5);
$vline->title->SetColor('red');
$graph->Add($vline);

// set beg-month Vline
$beg = new GanttVline($top_date,'beg-month','blue',2,'solid');
$beg->SetDayOffset(0.5);
$beg->title->SetColor('blue');
$graph->Add($beg);

// set end-month Vline
$end = new GanttVline($btm_date,'end-month','blue',2,'solid');
$end->SetDayOffset(0.5);
$end->title->SetColor('blue');
$graph->Add($end);

// Setup activity info
// For the titles we also add a minimum width of 100 pixels for the Task name column
$graph->scale->actinfo->SetColTitles(
    array('Order#','days','qty'),array(50,18,25));
$graph->scale->actinfo->SetBackgroundColor('teal');
$graph->scale->actinfo->SetFontColor("white");
$graph->scale->actinfo->SetFont(FF_ARIAL,FS_BOLD,8);
$graph->scale->actinfo->vgrid->SetStyle('solid');
$graph->scale->actinfo->vgrid->SetColor('gray');

// Create GANTT bars data -----------
	$data = array();
	if($result != "none"){

		for ($i = 0; $i < count($result); $i++) {   // $i �� �q�檺�`�ƶq

			$duration = strval( (($op['ord'][$i]['ets'] == '0000-00-00') || (!$op['ord'][$i]['ets'])) ? 0 : countDays($op['ord'][$i]['ets'],$op['ord'][$i]['etf']) +1 );
			$O_qty[$i] = number_format($op['ord'][$i]['qty']);
//			$duration = $duration." d";

if($op['ord'][$i]['qty'] != 0){
	$finish_rate = number_format(($op['ord'][$i]['done']/$op['ord'][$i]['qty']),4, '.', ',');
}else{
	$finish_rate = 0;
}
$T = $finish_rate *100;;
$finish[$i] = $T."%";
// �]�� jpgraph �S�k���� progress �j�� 100%
if ($finish_rate > 1){ $progress[$i] = 1; }else{ $progress[$i] = $finish_rate; }

		$data[$i] =  array($i,array($op['ord'][$i]['ord_num'],$duration,$O_qty[$i]),$op['ord'][$i]['ets'],$op['ord'][$i]['etf'],FF_ARIAL,FS_BOLD,8);
		}
	}

// Create the bars and add them to the gantt chart [from $DATA] 
for($i=0; $i<count($data); $i++) {
	$bar = new GanttBar($data[$i][0],$data[$i][1],$data[$i][2],$data[$i][3],$finish[$i],10);
	if( count($data[$i])>4 )
		$bar->title->SetFont($data[$i][4],$data[$i][5],$data[$i][6]);
	$bar->SetPattern(BAND_RDIAG,"yellow");
	$bar->SetFillColor("gray");
	$bar->progress->Set($progress[$i]);
	$bar->progress->SetPattern(GANTT_SOLID,"teal");
	$graph->Add($bar);
}
	
	$op['chart'] = $graph->Stroke('picture/fty_schedule.png');

		$layout->assign($op);
		$layout->display($TPL_FTY_SCHEDULE2);		    	    
	
	
	break;





#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//			job 91  [SALES][FORECAST]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//	case "sales_forecast":	job 91  �~�ȹw��
//	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "sales_forecast":
		check_authority(9,1,"view");

				// creat FTY combo box
		$op['factory'] = $arry2->select($FACTORY,'','PHP_fty','select','');  	
				// creat cust combo box
		$where_str="order by cust_s_name"; //��cust_s_name�Ƨ�
		$cust_def = $cust->get_fields('cust_init_name',$where_str);
		$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//���X�Ȥ�N��
		for ($i=0; $i< sizeof($cust_def); $i++)
		{
			$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//�H [�Ȥ�N��-�Ȥ�²��] �e�{
		}
		$op['cust'] =  $arry2->select($cust_value,'','PHP_cust','select','',$cust_def_vue); 

		$op['year_1'] = $arry2->select($YEAR_WORK,'','PHP_year1','select','');  	
		$op['year_2'] = $arry2->select($YEAR_WORK,'','PHP_year2','select','');  	
		$op['year_3'] = $arry2->select($YEAR_WORK,'','PHP_year3','select','');  	


		$op['msg'] = $order->msg->get(2);

		$allow = $admin->decode_perm(9,1);   // �]�w �s�W�R���v��
		if ($op['msg']){ $op['msg_YES'] = 1; } else { $op['msg_YES'] = "" ;}
		if ($allow['view'])		{	$op['view_flag'] = 1;} else {	$op['view_flag'] = "";}
		if ($allow['add'])		{	$op['add_flag'] = 1;} else {	$op['add_flag'] = "";}
		if ($allow['edit'])		{	$op['edit_flag'] = 1;} else {	$op['edit_flag'] = "";}
		if ($allow['del'])		{	$op['del_flag'] = 1;} else {	$op['del_flag'] = "";}

			$layout->assign($op);
			$layout->display($TPL_FORECAST);		    	    
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//	case "forecast_search":		job 91V  �w��search
//
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "forecast_search":
		check_authority(9,1,"view");
		//�ܤ֭n��� fty
		if (!$PHP_fty){
			$op['msg'][] = "Error ! you have to select the target Factory !";
		}
		if (!$PHP_year1){
			$PHP_year1 = $THIS_YEAR;
		}

		if (isset($op['msg'])){	
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$parm = array(	"fty"	=>  $PHP_fty,
						"cust"	=>  $PHP_cust,
						"year"	=>	$PHP_year1,
				);

		//�w�]�w where_str
		$where_str = " WHERE method='forecast' AND fty='".$PHP_fty."' AND year='".$PHP_year1."' ";
		
		//�p�G�S����Ȥ� �N�����j�M
		if($PHP_cust){
			$where_str = $where_str."AND cust='".$PHP_cust."' ";
		}

		//database search data....
		   if(!$op = $forecast->search(0,$where_str)){
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
		for ($j=0; $j<13; $j++){
			$op['q'][$j] = 0;
			$op['f'][$j] = 0;
			$op['c'][$j] = 0;

		}

		for ($i=0; $i<$op['max_no']; $i++){

			$op['fcst'][$i]['q'] = csv2array($op['fcst'][$i]['qty']);
			$op['fcst'][$i]['p'] = csv2array($op['fcst'][$i]['uprc']);
			$op['fcst'][$i]['f'] = csv2array($op['fcst'][$i]['fcst']);
			if ($op['fcst'][$i]['cm'] == '') $op['fcst'][$i]['cm'] ='0,0,0,0,0,0,0,0,0,0,0,0,0';
			$op['fcst'][$i]['c'] = csv2array($op['fcst'][$i]['cm']);
			
			//�[�`�������Ȥ�w��---
			for ($j=0; $j<13; $j++){

				$op['q'][$j] = $op['q'][$j] + $op['fcst'][$i]['q'][$j];
				$op['f'][$j] = $op['f'][$j] + $op['fcst'][$i]['f'][$j];
				$op['c'][$j] = $op['c'][$j] + $op['fcst'][$i]['c'][$j];
			}
		}

		//�p�G�j�M��Ʈw���Ƭ� 0 ��
		if (!$op['max_no']){
			$op['msg'][] = "sorry ! current database found nothig from your request !";

		}

		$op['fty'] = $PHP_fty;
		$op['year'] = $PHP_year1;

		$layout->assign($op);
		$layout->display($TPL_FORECAST_SEARCH);		    	    
	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//	case "forecast_add":		job 91A  �w�� ADD
//
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "forecast_add":
		check_authority(9,1,"add");
		// �p�G�S��~���� �����~
		if(!$PHP_year1)   {	$PHP_year1 = $THIS_YEAR;	}

		$parm = array(	"fty"	=>  $PHP_fty,
						"cust"	=>  $PHP_cust,
						"year"	=>	$PHP_year1,
				);

		$op['fty'] = $PHP_fty;
		$op['cust'] = $PHP_cust;
		$op['year'] = $PHP_year1;

		if (!$op['fty']){
			$op['msg'][] = "Error ! you have to select the target Factory !";
		}
		if(!$op['cust']){
			$op['msg'][] = "Error! you have to select one customer !";
		}
		if(!$op['year']){
			$op['msg'][] = "Error! you have to select target YEAR !";
		}

		if (isset($op['msg'])){	
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		//  �ˬd�ݬ� ��Ʈw�O�_�w�g �s�b ??
		   if($c = $forecast->get(0,$parm,'forecast')){
				$op['msg'][]= 'FY :'.$PHP_year1.'\'s forecast of factory[ '.$PHP_fty.' ] for:[ '.$PHP_cust.' ] is exist already !';
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

		$op['msg'] ='';

		$op['fty'] = $PHP_fty;
		$op['year1'] = $PHP_year1;
		$op['year2'] = $PHP_year2;
		$op['year3'] = $PHP_year3;
		
		if (!$op['fty']){
			$op['msg'][] = "Error ! you have to select the target Factory !";
		}
		if(!$op['year1']){
			$op['msg'][] = "Error! you have to select one target YEAR at least !";
		}

		if ($op['msg']){	
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$op['ft_ary'] = array(1,2,3,4,5,6,7,8,9,10,11,12);
		$layout->assign($op);
		$layout->display($TPL_FORECAST_ADD);		    	    
	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//	case "do_forecast_add":		job 91A  �w�� ADD
//
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "do_forecast_add":

		check_authority(9,1,"add");

		$parm = array(	"fty"	=>  $PHP_fty,
						"cust"	=>  $PHP_cust,
						"year"	=>	$PHP_year,
						"method"=>	"forecast",
				);

		$tt_qty = $tt_fcst = $tt_cm = 0;
		$fcst = array();

		for ($i=1; $i<13; $i++){

			$qty[$i]	= ($PHP_qty[$i] =='') ? 0 : $PHP_qty[$i];
			$uprc[$i]	= ($PHP_prc[$i] =='') ? 0 : $PHP_prc[$i];
			$cm[$i]	= ($PHP_cm[$i] =='') ? 0 : $PHP_cm[$i];
			$fcst[$i]	= $qty[$i] * $uprc[$i];
		
			$tt_fcst = $tt_fcst + $fcst[$i];
			$tt_qty = $tt_qty + $qty[$i];
			$tt_cm = $tt_cm + $cm[$i];
		}

			$qty[13]	= $tt_qty;
			$fcst[13]	= $tt_fcst;
			$cm[13]	= $tt_cm;
			$uprc[13]	= ($tt_qty !=0) ? number_format($tt_fcst/$tt_qty, 2, '.', '') : 0;

		$parm['uprc'] = Array2csv($uprc);
		$parm['qty'] = Array2csv($qty);
		$parm['fcst'] = Array2csv($fcst);
		$parm['cm'] = Array2csv($cm);
		//�g�Jforecast table
		if (!$result = $forecast->add($parm)) {   
			$op['msg']= $forecast->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		//��forecast table�A�s�X�� -----------------------
		if (!$f = $forecast->get($result)) {   
			$op['msg']= $forecast->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		
		$op['fcst'] = $f;

		//�Ncsv�令�}�C
			$op['fcst']['q'] = csv2array($f['qty']);
			$op['fcst']['p'] = csv2array($f['uprc']);
			$op['fcst']['f'] = csv2array($f['fcst']);
			$op['fcst']['c'] = csv2array($f['cm']);
			# �O���ϥΪ̰ʺA
		$message = "customer: [".$f['cust']."] in factory:[".$f['fty']."] forecast of year:[".$f['year']."]  done creating";

		$log->log_add(0,"91A",$message);
		$op['msg'][]= $message;

		$layout->assign($op);
		$layout->display($TPL_FORECAST_SHOW);		    	    
	break;






#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//	case "forecast_update":		job 91E  �w���s
//
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "forecast_update":
		check_authority(9,1,"edit");
		// �p�G�S��~���� �����~
		if(!$PHP_year1)   {	$PHP_year1 = $THIS_YEAR;	}

		$parm = array(	"fty"	=>  $PHP_fty,
						"cust"	=>  $PHP_cust,
						"year"	=>	$PHP_year1,
				);

		$op['fty'] = $PHP_fty;
		$op['cust'] = $PHP_cust;
		$op['year'] = $PHP_year1;

		if (!$op['fty']){
			$op['msg'][] = "Error ! you have to select the target Factory !";
		}
		if(!$op['cust']){
			$op['msg'][] = "Error! you have to select one customer !";
		}
		if(!$op['year']){
			$op['msg'][] = "Error! you have to select target YEAR !";
		}

		if (isset($op['msg'])){	
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		//  �ˬd�ݬ� ��Ʈw�O�_ �s�b ??
		   if(!$c = $forecast->get(0,$parm,'forecast')){
				$op['msg'][]= 'FY :'.$PHP_year1.'\'s forecast of factory[ '.$PHP_fty.' ] for:[ '.$PHP_cust.' ] is NOT EXIST in database !';
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

			$op['qty'] = csv2array($c['qty']);
			$op['prc'] = csv2array($c['uprc']);
			$op['cm'] = csv2array($c['cm']);

			$op['id'] = $c['id'];


		$layout->assign($op);
		$layout->display($TPL_FORECAST_UPDATE);		    	    
	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//	case "do_forecast_update":		job 91E  �w���s
//
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "do_forecast_update":

		check_authority(9,1,"edit");
		$parm = array(	"fty"	=>  $PHP_fty,
						"cust"	=>  $PHP_cust,
						"year"	=>	$PHP_year,
						"id"	=>	$PHP_id,
						"method"=>	"forecast",
				);

		$tt_qty = $tt_fcst = $tt_cm = 0;
		$fcst = array();

		for ($i=1; $i<13; $i++){

			$qty[$i]	= ($PHP_qty[$i] =='') ? 0 : $PHP_qty[$i];
			$uprc[$i]	= ($PHP_prc[$i] =='') ? 0 : $PHP_prc[$i];
			$cm[$i]	  = ($PHP_cm[$i] =='')  ? 0 : $PHP_cm[$i];
			$fcst[$i]	= $qty[$i] * $uprc[$i];

			$tt_fcst = $tt_fcst + $fcst[$i];
			$tt_cm  = $tt_cm + $cm[$i];
			$tt_qty = $tt_qty + $qty[$i];
		}

			$qty[13]	= $tt_qty;
			$cm[13] 	= $tt_cm;
			$fcst[13]	= $tt_fcst;
			$uprc[13]	= ($tt_qty !=0) ? number_format($tt_fcst/$tt_qty, 2, '.', '') : 0;

		$parm['uprc'] = Array2csv($uprc);
		$parm['qty'] = Array2csv($qty);
		$parm['fcst'] = Array2csv($fcst);
		$parm['cm'] = Array2csv($cm);
		//�g�Jforecast table
		if (!$result = $forecast->edit($parm)) {   
			$op['msg']= $forecast->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		//��forecast table�A�s�X�� -----------------------
		if (!$f = $forecast->get($result)) {   
			$op['msg']= $forecast->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		
		$op['fcst'] = $f;

		//�Ncsv�令�}�C
			$op['fcst']['q'] = csv2array($f['qty']);
			$op['fcst']['p'] = csv2array($f['uprc']);
			$op['fcst']['f'] = csv2array($f['fcst']);
			$op['fcst']['c'] = csv2array($f['cm']);
			# �O���ϥΪ̰ʺA
		$message = "customer: [".$f['cust']."] in factory:[".$f['fty']."] forecast of year:[".$f['year']."]  done UPDATE";

		$log->log_add(0,"91E",$message);
		$op['msg'][]= $message;

		$layout->assign($op);
		$layout->display($TPL_FORECAST_SHOW);		    	    
	break;






#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//		job 81  [ADMIN][CAPACITY]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++




#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//		 job 81-4-1    SHIPPed 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "shp_out":

		check_authority(8,1,"view");
		$string = $PHP_year.'-'.$PHP_month;
		$op['shp'] = array();

		if (!$result = $shipping->search_mon_shp($PHP_fty,$string,500)) {   
			$op['msg']= $shipping->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$op['ttl_shp_su'] = $op['ttl_shp_qty'] = $op['sales_fob'] = $op['ttl_odr_qty'] = 0;
		$op['shp'] = $result;

	if($result != "none"){

		for ($i = 0; $i < count($result); $i++) {   // $i ���X�f�O�� ����

			$op['ttl_shp_qty'] = $op['ttl_shp_qty'] + $result[$i]['s_qty'];
			$op['ttl_shp_su'] = $op['ttl_shp_su'] + $result[$i]['su'];
			$op['ttl_odr_qty'] = $op['ttl_odr_qty'] + $result[$i]['qty'];
			$op['sales_fob'] = $op['sales_fob'] + $result[$i]['s_qty']* $result[$i]['uprice'];
			$op['shp'][$i]['amt'] = $result[$i]['s_qty']* $result[$i]['uprice'];
		
		}
		// �p�G capacity���� shp�ƶq�P shipping table���q���P�� ��� capacity�����q
		if ($PHP_qty != $op['ttl_shp_su']){
			if (!$update = $capaci->update_field($PHP_fty,$PHP_year,"shipping","m".$PHP_month,$op['ttl_shp_qty'])) {   
				$op['msg']= "cannot update capacity database !, please contact system Administraor";
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
		}
			// �p�G capacity���� shp_fob ���B�P �p��X�Ӫ����B���P�� ��� capacity�������B
		if ($PHP_amt != $op['sales_fob']){
			if (!$update = $capaci->update_field($PHP_fty,$PHP_year,"shp_fob","m".$PHP_month,$op['sales_fob'])) {   
				$op['msg']= "cannot update capacity database !, please contact system Administraor";
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
		}

		$op['pc_avg'] = number_format($op['sales_fob']/$op['ttl_shp_qty'],2);

	} else{ //if($result != "none")
		$op['record_NONE'] = "1";
	}
		$op['fty'] = $PHP_fty;
		$op['year'] = $PHP_year;
		$op['mon'] = $PHP_month;
			
		page_display($op, 8, 1, $TPL_MON_SHP);    	    
	
	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//		 job 81-3-1    FTY SCHEDULE �Ʋ� �����
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "fty_etp_monthy":

		check_authority(4,5,"view");
		$string = $PHP_year.$PHP_month;
		$op['dist'] = array();

		$m_array = array();

			$T = (($PHP_month -2) < 1) ? 12:0;
		$mon[0] = substr($PHP_month - 2 + $T + 100,1);
			$T = (($PHP_month -1) < 1) ? 12:0;
		$mon[1] = substr($PHP_month - 1 + $T + 100,1);
		$mon[2] = $PHP_month;
			$T = (($PHP_month +1) > 12) ? 12:0;
		$mon[3] = substr($PHP_month + 1 - $T + 100,1);
			$T = (($PHP_month +2) > 12) ? 12:0;
		$mon[4] = substr($PHP_month + 2 - $T + 100,1);

		if (!$result = $capaci->search_schdl($PHP_fty,$string,500)) {   
			$op['msg']= $capaci->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$op['ttl_su'] = 0;

	if($result != "none"){

		for ($i = 0; $i < count($result); $i++) {   // $i �� �q�檺�`�ƶq

			$op['ord'][$i]['ord_num'] = $result[$i]['order_num'];
			$op['ord'][$i]['qty'] = $result[$i]['qty'];
			$op['ord'][$i]['su'] = $result[$i]['su'];
			$op['ord'][$i]['ets'] = $result[$i]['ets'];
			$op['ord'][$i]['etf'] = $result[$i]['etf'];
			$op['ord'][$i]['shp'] = $result[$i]['qty_shp'];
			$op['ord'][$i]['done'] = $result[$i]['qty_done'];
				
				
				// �p�� �e���Ӥ�άd�M�������� �ð�html�� layout
			$etp_mon = csv2array($result[$i]['fty_su']);   //$etp_mon ���a���~������ �Y 200503 ...
			for ($k=0; $k < count($mon); $k++){   // ���Ӥ몺��ư}�C�]�w
						$op['ord'][$i][$k] = '';  //���N�Ҧ��ȳ]��null

				for ($j=0; $j < count($etp_mon); $j++){

					$mon_key = substr($etp_mon[$j],4,2);  // $mon_key ����쪺mon
					$mon_su = substr($etp_mon[$j],6);

						if($mon_key == $mon[$k]){
							$op['ord'][$i][$k] = $mon_su;
						}
				}   //for ($j=0
				
			}  //for ($k=0;

			$op['ttl_su'] = $op['ttl_su'] + $op['ord'][$i][2];
		
		} //for ($i = 0

	} else{ //if($result != "none")
		$op['record_NONE'] = "1";
	}
		$op['fty'] = $PHP_fty;
		$op['year'] = $PHP_year;
		$op['month'] = $PHP_month;
		$op['mon'] = $mon;

		//  �j���N capaci ���� ��� schedule ��s~~ �D�n�O�٨S��쬰����|���t��
		//  check and ��a�ާ@�L  revise �� �A�T�{ �֥i�q�� ���e�� ���S��
		//  �Ȯɥαj����s���覡 ���[��S���� �Adisable�o�ӵ{��
			
				$m_key = "m".$PHP_month;
			if ($PHP_su <> $op['ttl_su']){
				if (!$update = $capaci->update_field($PHP_fty,$PHP_year,"schedule",$m_key,$op['ttl_su'])) {   
					$op['msg']= "cannot update capacity database !, please contact system Administraor";
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
				}
			}
		    	    
		page_display($op, 4, 5, $TPL_FTY_SCHEDULE);	
	
	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//			 job 81-2-1    SALES ETP �����
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "etp_monthy":

		check_authority(8,1,"view");
		$string = $PHP_year.$PHP_month;
		$op['dist'] = array();

		$m_array = array();

			$T = (($PHP_month -2) < 1) ? 12:0;
		$mon[0] = substr($PHP_month - 2 + $T + 100,1);
			$T = (($PHP_month -1) < 1) ? 12:0;
		$mon[1] = substr($PHP_month - 1 + $T + 100,1);
		$mon[2] = $PHP_month;
			$T = (($PHP_month +1) > 12) ? 12:0;
		$mon[3] = substr($PHP_month + 1 - $T + 100,1);
			$T = (($PHP_month +2) > 12) ? 12:0;
		$mon[4] = substr($PHP_month + 2 - $T + 100,1);

		if (!$result = $capaci->search_etp($PHP_fty,$string,500)) {   
			$op['msg']= $capaci->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$op['ttl_su'] = 0;
		$op['ttl_qty'] = 0;
		$op['sales_fob'] = 0;

	if($result != "none"){

		for ($i = 0; $i < count($result); $i++) {   // $i �� �q�檺�`�ƶq

			$op['ord'][$i]['ord_num'] = $result[$i]['order_num'];
			$op['ord'][$i]['qty'] = $result[$i]['qty'];
			$op['ord'][$i]['su'] = $result[$i]['su'];
			$op['ord'][$i]['etp'] = $result[$i]['etp'];
			$op['ord'][$i]['etd'] = $result[$i]['etd'];
			$op['ord'][$i]['shp'] = $result[$i]['qty_shp'];
			$op['ord'][$i]['done'] = $result[$i]['qty_done'];
			$op['ord'][$i]['status'] = $result[$i]['status'];
				
				// �p�� �e���Ӥ�άd�M�������� �ð�html�� layout
			$etp_mon = csv2array($result[$i]['etp_su']);   //$etp_mon ���a���~������ �Y 200503 ...
			for ($k=0; $k < count($mon); $k++){   // ���Ӥ몺��ư}�C�]�w
						$op['ord'][$i][$k] = '';  //���N�Ҧ��ȳ]��null

				for ($j=0; $j < count($etp_mon); $j++){

					$mon_key = substr($etp_mon[$j],4,2);  // $mon_key ����쪺mon
					$mon_su = substr($etp_mon[$j],6);

						if($mon_key == $mon[$k]){
							$op['ord'][$i][$k] = $mon_su;
						}
				}   //for ($j=0
			}  //for ($k=0;

			if ( $op['ord'][$i][2] == 0 || $result[$i]['ie1'] == 0)
			{
				$mon_qty = 0;
			}else{
				$mon_qty = $op['ord'][$i][2]/$result[$i]['ie1'];
			}

			$op['ttl_su'] = $op['ttl_su'] + $op['ord'][$i][2];
			$op['ttl_qty'] = $op['ttl_qty'] + $mon_qty;
			$op['sales_fob'] = $op['sales_fob'] + $mon_qty*$result[$i]['uprice'];
		
		} //for ($i = 0
			if($op['ttl_su']){
				$op['su_avg'] = number_format($op['sales_fob']/$op['ttl_su'],2);
			}else{ $op['su_avg'] = 0; }

			if($op['ttl_qty']){
				$op['pc_avg'] = number_format($op['sales_fob']/$op['ttl_qty'],2);
			}else{$op['pc_avg'] = 0; }

	} else{ //if($result != "none")
		$op['record_NONE'] = "1";
	}
		$op['fty'] = $PHP_fty;
		$op['year'] = $PHP_year;
		$op['month'] = $PHP_month;
		$op['mon'] = $mon;

		//  �j���N capaci ���� ��� pre_schedule ��s~~ �D�n�O�٨S��쬰����|���t��
		//  check and ��a�ާ@�L  revise �� �A�T�{ �֥i�q�� ���e�� ���S��
		//  �Ȯɥαj����s���覡 ���[��S���� �Adisable�o�ӵ{��
			
		$m_key = "m".$PHP_month;
		if ($PHP_su <> $op['ttl_su']){
			if (!$update = $capaci->update_field($PHP_fty,$PHP_year,"pre_schedule",$m_key,$op['ttl_su'])) {   
				$op['msg']= "cannot update capacity database !, please contact system Administraor";
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
		}
		
		page_display($op, 8, 1, $TPL_ETP_OUTPUT);
	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//			 case "un_etp_monthy":   ���֥i�q�� ETP �����
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "un_etp_monthy":

		check_authority(8,1,"view");
		$string = $PHP_year.$PHP_month;
		$op['dist'] = array();

		$m_array = array();

			$T = (($PHP_month -2) < 1) ? 12:0;
		$mon[0] = substr($PHP_month - 2 + $T + 100,1);
			$T = (($PHP_month -1) < 1) ? 12:0;
		$mon[1] = substr($PHP_month - 1 + $T + 100,1);
		$mon[2] = $PHP_month;
			$T = (($PHP_month +1) > 12) ? 12:0;
		$mon[3] = substr($PHP_month + 1 - $T + 100,1);
			$T = (($PHP_month +2) > 12) ? 12:0;
		$mon[4] = substr($PHP_month + 2 - $T + 100,1);

		if (!$result = $capaci->get_unord_itme($PHP_fty,$PHP_year,$PHP_month)) {   
			$op['msg']= $capaci->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$op['ttl_su'] = 0;
		$op['ttl_qty'] = 0;
		$op['sales_fob'] = 0;

	if($result != "none"){

		for ($i = 0; $i < sizeof($result); $i++) {   // $i �� �q�檺�`�ƶq

			$op['ord'][$i]['ord_num'] = $result[$i]['order_num'];
			$op['ord'][$i]['id'] = $result[$i]['id'];	
			$op['ord'][$i]['style'] = $result[$i]['style'];	
			$op['ord'][$i]['qty'] = $result[$i]['qty'];			
			$op['ord'][$i]['etp'] = $result[$i]['etp'];
			$op['ord'][$i]['etd'] = $result[$i]['etd'];
			$op['ord'][$i]['su'] = $result[$i]['su'];
			$op['ord'][$i]['status'] = get_ord_status($result[$i]['status']);
			$op['ord'][$i]['open'] = $result[$i]['opendate'];

				// �p���` SU				
			if ($result[$i]['ie1'] > 0)
			{
				$T_su=$result[$i]['su'];
			}else if ($result[$i]['style']=='PS' || $result[$i]['style']=='BS' || $result[$i]['style']=='BZ' || $result[$i]['style']=='DR' || $result[$i]['style']=='JK' || $result[$i]['style']=='PS-J' || $result[$i]['style']=='PS-P' || $result[$i]['style']=='PS-S' || $result[$i]['style']=='VS' || $result[$i]['style']=='SS'){
				$T_su=2*$result[$i]['qty'];
				$op['ord'][$i]['fsu']=$T_su;
				
			}else{
				$T_su=1*$result[$i]['qty'];
				$op['ord'][$i]['fsu']=$T_su;
			}
			
			//�p��C�� SU
			$msu=get_su($result[$i]['etp'],$result[$i]['etd'],$T_su);
			$sum=0;
			$ets=explode('-',$result[$i]['etp']);
			$etf=explode('-',$result[$i]['etd']);
			if ($ets[0]==$etf[0])
			{
				for ($y=0; $y<sizeof($msu); $y++)
				{
					$suc=explode('-',$msu[$y]);
					$sum=$sum+$suc[1];
				}
				$suc=explode('-',$msu[($y-1)]);
				$suc[1]=$suc[1]+($T_su-$sum);
				$msu[($y-1)]=$suc[0]."-".$suc[1];
			}

			for ($k=0; $k < count($mon); $k++){   // ���Ӥ몺��ư}�C�]�w
						$op['ord'][$i][$k] = '';  //���N�Ҧ��ȳ]��null

				for ($j=0; $j < sizeof($msu); $j++){

					$mon_su = explode('-',$msu[$j]);  // $mon_su[0]=�� $mon_su[1]=su��

						if($mon_su[0] == $mon[$k]){
							$op['ord'][$i][$k] = $mon_su[1];
						}
				}   //for ($j=0
			}  //for ($k=0;
			
			if ($result[$i]['ie1'] > 0)
			{
				$mon_qty=$op['ord'][$i][2]/$result[$i]['ie1'];
			}else if ($result[$i]['style']=='PS' || $result[$i]['style']=='PT' || $result[$i]['style']=='SH' || $result[$i]['style']=='SK' || $result[$i]['style']=='SO' || $result[$i]['style']=='SS'){
				$mon_qty=$op['ord'][$i][2]/ 200;				
			}else{
				$mon_qty=$op['ord'][$i][2]/ 100;				
			}

			$op['ttl_su'] = $op['ttl_su'] + $op['ord'][$i][2];
			$op['ttl_qty'] = $op['ttl_qty'] + $mon_qty;
			$op['sales_fob'] = $op['sales_fob'] + $mon_qty*$result[$i]['uprice'];
		
		} //for ($i = 0
			if($op['ttl_su']){
				$op['su_avg'] = number_format($op['sales_fob']/$op['ttl_su'],2);
			}else{ $op['su_avg'] = 0; }

			if($op['ttl_qty']){
				$op['pc_avg'] = number_format($op['sales_fob']/$op['ttl_qty'],2);
			}else{$op['pc_avg'] = 0; }

	} else{ //if($result != "none")
		$op['record_NONE'] = "1";
	}
		$op['fty'] = $PHP_fty;
		$op['year'] = $PHP_year;
		$op['month'] = $PHP_month;
		$op['mon'] = $mon;
		$op['today']=date('Y-m-d');
		    	    
		page_display($op, 8, 1, $TPL_UNETP_OUTPUT);
	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//			 job 81-1-2    �C�鲣�X�q ����� (�~�o)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "monthy_daily_ouput":

		check_authority(8,1,"view");
		$op['fty'] = $PHP_fty;
		$op['year'] = $PHP_year;
		$op['month'] = $PHP_month;
		
		$mon_days = getDaysInMonth($PHP_month,$PHP_year);

		$op['total_qty'] = 0;
		$op['total_su'] = 0;

			$daily_mon = $result = array();

			if (!$result = $daily->sum_daily_out($PHP_fty,$PHP_year,$PHP_month)) {   
				$op['msg']= $daily->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

		// smarty �p�ƥѹs�ҩl thus�ഫ �P�ɭp�⥲�n���޲z�ƾ�
		$out_data[0] = $schd_data[0] = $etp_data[0] = 0;  // GRAPHIC ��

	    for ($i = 1; $i <= count($result); $i++) {
			$op['total_qty'] = $op['total_qty'] + $result[$i]['qty'];
			
			$out_data[$i] = $op['total_su'] = $op['total_su'] + $result[$i]['su'];
			// out_data �Ψӵ�graphic�� 
			$op['daily_mon'][$i-1]['accu_su'] = number_format($op['total_su']);
			$op['daily_mon'][$i-1]['avg_su'] = number_format($op['total_su'] / $i);

			$op['daily_mon'][$i-1]['date'] = $result[$i]['date'];
			$op['daily_mon'][$i-1]['qty'] = number_format($result[$i]['qty']);
			$op['daily_mon'][$i-1]['su'] = number_format($result[$i]['su']);
		}
		//  2006/06/02 �[�J---------- ����������|���t��
		//  �j���N capaci ���� ��� pre_schedule ��s~~ �D�n�O�٨S��쬰����|���t��
		//  check and ��a�ާ@�L  revise �� �A�T�{ �֥i�q�� ���e�� ���S��
		//  �Ȯɥαj����s���覡 ���[��S���� �Adisable�o�ӵ{��
			
		$m_key = "m".$PHP_month;
		if ($PHP_su <> $op['total_su']){
			if (!$update = $capaci->update_field($PHP_fty,$PHP_year,"actual",$m_key,$op['total_su'])) {   
				$op['msg']= "cannot update capacity database !, please contact system Administraor";
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
		}

		//�]�w�@���ܼƨ�graphic��
		$year_mon = $PHP_year.$PHP_month;
		$end_mon = $PHP_year."-".$PHP_month."-".$mon_days;  //�멳��
		$beg_mon = $PHP_year."-".$PHP_month."-01";   //����
		//----- �]�C�骺�w�]��....
		for($i=1;$i<$mon_days+1;$i++){ 
				//$schd_d�����骺��Ѳ��q; $schd_data���֥[���q
			$schd_d[$i] = $schd_data[$i] = 0;  
				//$etp_d�����骺��ѭq��q; $etp_data���֥[�q��q
			$etp_d[$i] = $etp_data[$i] = 0;  
		}
//-----------------------------------------------------------------------------------
		//�p��u�t�Ʋ����C��q -------------------------(for graphic)

		if (!$schd_A = $capaci->search_schdl($PHP_fty,$year_mon,500)) {   
			$op['msg']= $capaci->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		//----------------------------- �C���p�� -----
		if ($schd_A != "none"){
			for($i=0;$i<count($schd_A);$i++){ //�ѧ�X�Ӫ��O���@�����p

				$fy_su = preg_grep("/^$year_mon/",csv2array($schd_A[$i]['fty_su']));
				foreach($fy_su as $su_key => $su_value);  //�u���X�Ȩӥ�

				//�}�l�� �� ������
				$sdate = ($schd_A[$i]['ets'] < $beg_mon ) ? $beg_mon : $schd_A[$i]['ets'];
				$edate = ($schd_A[$i]['etf'] > $end_mon ) ? $end_mon : $schd_A[$i]['etf'];

				$wdays = countDays($sdate, $edate); //�i�u�@�Ѽ�
				$t_su = substr($su_value, 6 );
				$day_su = intval($t_su/($wdays+1)); //�C�鲣�q

				$d = intval(substr($sdate,8));  //�}�l�����

				for($j=$d; $j<$wdays+$d+1; $j++){ //�Ѷ}�l���_
					if($j == intval(substr($edate,8))){  //�̫�@�Ѯ�
						$schd_d[$j] = $schd_d[$j] + ($t_su - ($day_su * $wdays));
					}else{
						$schd_d[$j] = $schd_d[$j] + $day_su;
					}
				}
			}
			for($j=1;$j<$mon_days+1;$j++){  //�֥[...�p��MTD
				$schd_data[$j] = $schd_data[$j-1] + $schd_d[$j];
			}

		}else{  // �S���Ӥ�Ʋ���� ....
			$no_schd_rec = "1";
		}
//-----------------------------------------------------------------------------------
		//�p�� �~�ȭq��Ʋ����C��q ---------------------(for graphic)
		if (!$etp_A = $capaci->search_etp($PHP_fty,$year_mon,500)) {   
			$op['msg']= $capaci->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		//----------------------------- �C���p�� -----
		if ($etp_A != "none"){
			for($i=0;$i<count($etp_A);$i++){ //�ѧ�X�Ӫ��O���@�����p
				$ord_su = preg_grep("/^$year_mon/",csv2array($etp_A[$i]['etp_su']));
				foreach($ord_su as $etp_key => $etp_value);  //�u���X�Ȩӥ�
				//�}�l�� �� ������
				$sdate = ($etp_A[$i]['etp'] < $beg_mon ) ? $beg_mon : $etp_A[$i]['etp'];
				$edate = ($etp_A[$i]['etd'] > $end_mon ) ? $end_mon : $etp_A[$i]['etd'];

					$wdays = countDays($sdate, $edate); //�i�u�@�Ѽ�
					$t_su = substr($etp_value, 6 );
					$day_su = intval($t_su/($wdays+1)); //�C�鲣�q

					$d = intval(substr($sdate,8));

					for($j=$d; $j<$wdays+$d+1; $j++){ //�Ѷ}�l���_
						if($j == intval(substr($edate,8))){  //�̫�@�Ѯ�
							$etp_d[$j] = $etp_d[$j] + ($t_su - ($day_su * $wdays));
						}else{
							$etp_d[$j] = $etp_d[$j] + $day_su;
						}
					}
			}
			for($j=1;$j<$mon_days+1;$j++){  //�֥[...�p��MTD
					$etp_data[$j] = $etp_data[$j-1] + $etp_d[$j];
			}

		}else{  // �S���Ӥ�Ʋ���� ....
			$no_etp_rec = "1";
		}
//-----------------------------------------------------------------------------------

	//******* Z Chart graph procedure ****************	

		//�ޤJ graph class
		include_once($config['root_dir']."/lib/src/jpgraph.php");
		include_once($config['root_dir']."/lib/src/jpgraph_line.php");

		//�p�ⲣ��data ---
		if(!$cp = $capaci->get($PHP_fty,$PHP_year)){ 
			$op['msg']= $capaci->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$f = "m".$PHP_month;
		$capa_mon = $cp["$f"];
		$capa_day = intval($capa_mon/$mon_days);
		
		for ($i=0; $i<$mon_days+1; $i++){
		  if($i==0){ 
			$capa_data[$i] = 0; 
		  }elseif($i==$mon_days){
			$capa_data[$i] = $capa_mon;
		  }else{
			$capa_data[$i] = $capa_data[$i-1] + $capa_day;
		  }
			$xx_data[$i] = $i;
		}

		$graphic_title = " FTY: ".$PHP_fty." production Z Chart for month: ".$PHP_year."/".$PHP_month;

		$graph = new Graph(700, 400, "auto");
		$graph->SetScale("textlin");
		$graph->ygrid->SetFill(true,'#EFEFEF@0.5','#BBCCFF@0.5');
		$graph->img->SetMargin(60,80,40,80);    
		$graph->subtitle->Set('( Daily Accumulated Chart )');
		$graph->SetShadow();
		// Setup X-scale
		$graph->xaxis->SetTickLabels($xx_data);
		$graph->xaxis->SetFont(FF_ARIAL,FS_NORMAL,8);

		// setup capacity plot
		$capaplot = new LinePlot($capa_data);
		$capaplot->SetWeight(1.5);
		$capaplot->SetColor('navy');
		$capaplot->SetLegend('capacity '.number_format($capa_data[$mon_days]).' SU');
		$graph->Add($capaplot);

		// setup etp plot
		$etpplot = new LinePlot($etp_data);
		$etpplot->SetWeight(1.5);
		$etpplot->SetColor('teal');
		$etpplot->SetLegend('Order '.number_format($etp_data[$mon_days]).' SU');
		$graph->Add($etpplot);

		// setup schedule plot
		$schdplot = new LinePlot($schd_data);
		$schdplot->SetWeight(1.5);
		$schdplot->SetColor('darkred');
		$schdplot->SetLegend('schedule '.number_format($schd_data[$mon_days]).' SU');
		$graph->Add($schdplot);

		// setup out-put plot
		$outplot = new LinePlot($out_data);
		$outplot->SetWeight(1.5);
		$outplot->SetColor('red');
		$outplot->SetLegend('out-put '.number_format($out_data[$mon_days]).' SU');
		$graph->Add($outplot);
		
		$graph->legend->SetShadow('gray@0.4',5);
		$graph->legend->Pos(0.25,0.3,"center","bottom");
		$graph->title->Set($graphic_title);
		$graph->title->SetFont(FF_FONT1,FS_BOLD);
		if($THIS_MON == $year_mon){  //���Ѫ��Х�
			// Add a vertical line at the end scale position '7'
			$to_day = intval($TODAY);
			$l1 = new PlotLine(VERTICAL,$THIS_DATE,"red",1.5);

			$graph->Add($l1);
		}

		$op['chart'] = $graph->Stroke('picture/monthy_out.png');




		$layout->assign($op);
		$layout->display($TPL_MONTHY_DAILY_OUTPUT);		    	    
	
	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//			 job 81-1-2    �C�鲣�X�q �����
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "monthy_daily_subouput":

		check_authority(8,1,"view");
		$op['fty'] = $PHP_fty;
		$op['year'] = $PHP_year;
		$op['month'] = $PHP_month;
		
		$mon_days = getDaysInMonth($PHP_month,$PHP_year);

		$op['total_qty'] = 0;
		$op['total_su'] = 0;

			$daily_mon = $result = array();

			if (!$result = $daily->sum_daily_subout($PHP_fty,$PHP_year,$PHP_month)) {   
				$op['msg']= $daily->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

		// smarty �p�ƥѹs�ҩl thus�ഫ �P�ɭp�⥲�n���޲z�ƾ�
		$out_data[0] = $schd_data[0] = $etp_data[0] = 0;  // GRAPHIC ��

	    for ($i = 1; $i <= count($result); $i++) {
			$op['total_qty'] = $op['total_qty'] + $result[$i]['qty'];
			
			$out_data[$i] = $op['total_su'] = $op['total_su'] + $result[$i]['su'];
			// out_data �Ψӵ�graphic�� 
			$op['daily_mon'][$i-1]['accu_su'] = number_format($op['total_su']);
			$op['daily_mon'][$i-1]['avg_su'] = number_format($op['total_su'] / $i);

			$op['daily_mon'][$i-1]['date'] = $result[$i]['date'];
			$op['daily_mon'][$i-1]['qty'] = number_format($result[$i]['qty']);
			$op['daily_mon'][$i-1]['su'] = number_format($result[$i]['su']);			
		}

		//�]�w�@���ܼƨ�graphic��
		$year_mon = $PHP_year.$PHP_month;
		$end_mon = $PHP_year."-".$PHP_month."-".$mon_days;  //�멳��
		$beg_mon = $PHP_year."-".$PHP_month."-01";   //����
		//----- �]�C�骺�w�]��....
		for($i=1;$i<$mon_days+1;$i++){ 
				//$schd_d�����骺��Ѳ��q; $schd_data���֥[���q
			$schd_d[$i] = $schd_data[$i] = 0;  
				//$etp_d�����骺��ѭq��q; $etp_data���֥[�q��q
			$etp_d[$i] = $etp_data[$i] = 0;  
		}
//-----------------------------------------------------------------------------------
		//�p��u�t�Ʋ����C��q -------------------------(for graphic)

		if (!$schd_A = $capaci->search_schdl($PHP_fty,$year_mon,500)) {   
			$op['msg']= $capaci->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		//----------------------------- �C���p�� -----
		if ($schd_A != "none"){
			for($i=0;$i<count($schd_A);$i++){ //�ѧ�X�Ӫ��O���@�����p

				$fy_su = preg_grep("/^$year_mon/",csv2array($schd_A[$i]['fty_su']));
				foreach($fy_su as $su_key => $su_value);  //�u���X�Ȩӥ�

				//�}�l�� �� ������
				$sdate = ($schd_A[$i]['ets'] < $beg_mon ) ? $beg_mon : $schd_A[$i]['ets'];
				$edate = ($schd_A[$i]['etf'] > $end_mon ) ? $end_mon : $schd_A[$i]['etf'];

				$wdays = countDays($sdate, $edate); //�i�u�@�Ѽ�
				$t_su = substr($su_value, 6 );
				$day_su = intval($t_su/($wdays+1)); //�C�鲣�q

				$d = intval(substr($sdate,8));  //�}�l�����

				for($j=$d; $j<$wdays+$d+1; $j++){ //�Ѷ}�l���_
					if($j == intval(substr($edate,8))){  //�̫�@�Ѯ�
						$schd_d[$j] = $schd_d[$j] + ($t_su - ($day_su * $wdays));
					}else{
						$schd_d[$j] = $schd_d[$j] + $day_su;
					}
				}
			}
			for($j=1;$j<$mon_days+1;$j++){  //�֥[...�p��MTD
				$schd_data[$j] = $schd_data[$j-1] + $schd_d[$j];
			}

		}else{  // �S���Ӥ�Ʋ���� ....
			$no_schd_rec = "1";
		}
//-----------------------------------------------------------------------------------
		//�p�� �~�ȭq��Ʋ����C��q ---------------------(for graphic)
		if (!$etp_A = $capaci->search_etp($PHP_fty,$year_mon,500)) {   
			$op['msg']= $capaci->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		//----------------------------- �C���p�� -----
		if ($etp_A != "none"){
			for($i=0;$i<count($etp_A);$i++){ //�ѧ�X�Ӫ��O���@�����p
				$ord_su = preg_grep("/^$year_mon/",csv2array($etp_A[$i]['etp_su']));
				foreach($ord_su as $etp_key => $etp_value);  //�u���X�Ȩӥ�
				//�}�l�� �� ������
				$sdate = ($etp_A[$i]['etp'] < $beg_mon ) ? $beg_mon : $etp_A[$i]['etp'];
				$edate = ($etp_A[$i]['etd'] > $end_mon ) ? $end_mon : $etp_A[$i]['etd'];

					$wdays = countDays($sdate, $edate); //�i�u�@�Ѽ�
					$t_su = substr($etp_value, 6 );
					$day_su = intval($t_su/($wdays+1)); //�C�鲣�q

					$d = intval(substr($sdate,8));

					for($j=$d; $j<$wdays+$d+1; $j++){ //�Ѷ}�l���_
						if($j == intval(substr($edate,8))){  //�̫�@�Ѯ�
							$etp_d[$j] = $etp_d[$j] + ($t_su - ($day_su * $wdays));
						}else{
							$etp_d[$j] = $etp_d[$j] + $day_su;
						}
					}
			}
			for($j=1;$j<$mon_days+1;$j++){  //�֥[...�p��MTD
					$etp_data[$j] = $etp_data[$j-1] + $etp_d[$j];
			}

		}else{  // �S���Ӥ�Ʋ���� ....
			$no_etp_rec = "1";
		}
//-----------------------------------------------------------------------------------

	//******* Z Chart graph procedure ****************	

		//�ޤJ graph class
		include_once($config['root_dir']."/lib/src/jpgraph.php");
		include_once($config['root_dir']."/lib/src/jpgraph_line.php");

		//�p�ⲣ��data ---
		if(!$cp = $capaci->get($PHP_fty,$PHP_year)){ 
			$op['msg']= $capaci->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$f = "m".$PHP_month;
		$capa_mon = $cp["$f"];
		$capa_day = intval($capa_mon/$mon_days);
		
		for ($i=0; $i<$mon_days+1; $i++){
		  if($i==0){ 
			$capa_data[$i] = 0; 
		  }elseif($i==$mon_days){
			$capa_data[$i] = $capa_mon;
		  }else{
			$capa_data[$i] = $capa_data[$i-1] + $capa_day;
		  }
			$xx_data[$i] = $i;
		}

		$graphic_title = " FTY: ".$PHP_fty." production Z Chart for month: ".$PHP_year."/".$PHP_month;

		$graph = new Graph(700, 400, "auto");
		$graph->SetScale("textlin");
		$graph->ygrid->SetFill(true,'#EFEFEF@0.5','#BBCCFF@0.5');
		$graph->img->SetMargin(60,80,40,80);    
		$graph->subtitle->Set('( Daily Accumulated Chart )');
		$graph->SetShadow();
		// Setup X-scale
		$graph->xaxis->SetTickLabels($xx_data);
		$graph->xaxis->SetFont(FF_ARIAL,FS_NORMAL,8);

		// setup capacity plot
		$capaplot = new LinePlot($capa_data);
		$capaplot->SetWeight(1.5);
		$capaplot->SetColor('navy');
		$capaplot->SetLegend('capacity '.number_format($capa_data[$mon_days]).' SU');
		$graph->Add($capaplot);

		// setup etp plot
		$etpplot = new LinePlot($etp_data);
		$etpplot->SetWeight(1.5);
		$etpplot->SetColor('teal');
		$etpplot->SetLegend('Order '.number_format($etp_data[$mon_days]).' SU');
		$graph->Add($etpplot);

		// setup schedule plot
		$schdplot = new LinePlot($schd_data);
		$schdplot->SetWeight(1.5);
		$schdplot->SetColor('darkred');
		$schdplot->SetLegend('schedule '.number_format($schd_data[$mon_days]).' SU');
		$graph->Add($schdplot);

		// setup out-put plot
		$outplot = new LinePlot($out_data);
		$outplot->SetWeight(1.5);
		$outplot->SetColor('red');
		$outplot->SetLegend('out-put '.number_format($out_data[$mon_days]).' SU');
		$graph->Add($outplot);
		
		$graph->legend->SetShadow('gray@0.4',5);
		$graph->legend->Pos(0.25,0.3,"center","bottom");
		$graph->title->Set($graphic_title);
		$graph->title->SetFont(FF_FONT1,FS_BOLD);
		if($THIS_MON == $year_mon){  //���Ѫ��Х�
			// Add a vertical line at the end scale position '7'
			$to_day = intval($TODAY);
			$l1 = new PlotLine(VERTICAL,$THIS_DATE,"red",1.5);

			$graph->Add($l1);
		}

		$op['chart'] = $graph->Stroke('picture/monthy_subout.png');
		$op['sub_con']=1;
		$op['sub_msg']="(sub-con.)";

	page_display($op, 8, 1, $TPL_MONTHY_DAILY_OUTPUT);    	    	
	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//			 job 81-1-3    �C�鲣�X�q ����
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "daily_ouput":

		check_authority(8,1,"view");
		$op['fty'] = $PHP_fty;
		$op['year'] = $PHP_year;
		$op['month'] = $PHP_month;
		$op['day'] = $PHP_day;
		$op['sub_con'] = $PHP_sub_con;
		$op['total_qty'] = 0;
		$op['total_su'] = 0;

		$op['the_date'] = $PHP_year."-".$PHP_month."-".$PHP_day;

		$daily_out = $result = array();
		if ($PHP_sub_con == 1)
		{
			if (!$result = $daily->daily_subout($PHP_fty, $op['the_date'])) {   
				$op['record_NONE'] = 1;				
			}
			$op['sub_msg']="(sub-con.)";
		}else{
			if (!$result = $daily->daily_out($PHP_fty, $op['the_date'])) {   
				$op['record_NONE'] = 1;
			}
		}

		// smarty �p�ƥѹs�ҩl thus�ഫ �P�ɭp�⥲�n���޲z�ƾ�
	    for ($i = 0; $i < sizeof($result); $i++) {	    	
			$op['total_qty'] = $op['total_qty'] + $result[$i]['qty'];
			$op['total_su'] = $op['total_su'] + $result[$i]['su'];

			$op['daily_out'][$i]['ord_num'] = $result[$i]['ord_num'];
			$op['daily_out'][$i]['qty'] = number_format($result[$i]['qty']);
			$op['daily_out'][$i]['su'] = number_format($result[$i]['su']);
		}
			$op['total_qty'] = number_format($op['total_qty']);
			$op['total_su'] = number_format($op['total_su']);
	
	page_display($op, 8, 1, $TPL_DAILY_OUTPUT);
	break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//			 job 81-1-4     �q�� ���X�q�O�� ����
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "order_output":

		check_authority(8,1,"view");
		$op['ord_num'] = $PHP_ord_num;
		$daily_out = $result = array();

			if (!$ord = $order->get_ord_output($PHP_ord_num)) {   
				$op['msg']= $daily->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			
			$op['order'] = $ord;
			$op['order']['order_num'] = $PHP_ord_num;			
		$ttl_qty = $ttl_su = 0;
		$plan_days = $schedule_days = $progress_days = 0;
		$est_fdate = ''; // ���⧹�����
		$est_period = 0; // �����`�Ͳ��ɵ{(��)

		// GANTT CHART �ѼƳ]�w
		$bar2_s = $bar2_f = $bar3_s = $bar3_f = '';   // bar2��bar3���_����
		$bar2_des = $bar3_des = ''; // bar2, bar3��������r
		$duration1 = $duration2 = $duration3 =''; 


	if (!$out = $daily->order_daily_out($PHP_ord_num)) {   
		$op['record_NONE'] = 1;

	}else{
		// smarty �p�ƥѹs�ҩl thus�ഫ �P�ɭp�⥲�n���޲z�ƾ�

	    for ($i = 0; $i < count($out); $i++) {
			$ttl_qty = $ttl_qty + $out[$i]['qty'];
			$ttl_su = $ttl_su + $out[$i]['su'];

			$op['order_out'][$i]['ttl_qty'] = $ttl_qty;
			$op['order_out'][$i]['ttl_su'] = $ttl_su;

			$op['order_out'][$i]['k_date'] = $out[$i]['k_date'];
			$op['order_out'][$i]['qty'] = $out[$i]['qty'];
			$op['order_out'][$i]['su'] = $out[$i]['su'];
			$op['order_out'][$i]['f_rate'] = $ttl_qty/$op['order']['qty']*100;
		}
		$op['order']['finish_qty'] = $ttl_qty;
		$op['order']['finish_su'] = $ttl_su;
		$op['order']['finish_rate'] = $ttl_qty/$op['order']['qty']*100;
	// �p�� in-house days [�p�G�S�� finish date�N��� "not finish" ]
		$op['order']['inhouse'] = countDays($op['order_out'][0]['k_date'],$op['order_out'][count($out)-1]['k_date'])+1;

		if (!$op['order']['finish']){
			$op['order']['finish'] ="not finish yet!";
	 	}
		if (!$op['order']['shp_date']){
			$op['order']['shp_date'] ="not ship yet!";
	 	}

		//�p�� ���p������(������k) ------------
		//�̫�@�� �����X�����
		$last_date = $op['order_out'][count($out)-1]['k_date'];
		if($ord['qty'] > $ttl_qty){
			//�����鲣
			$day_avg = $ttl_qty/$op['order']['inhouse'];
			//�w�p�٭n�X��
			$more_days = number_format(($ord['qty'] - $ttl_qty)/$day_avg);
			//�w�p������
			$est_fdate = increceDaysInDate($last_date,$more_days);
			$est_period = $op['order']['inhouse'] + $more_days +1;
		}else{
			$est_fdate = increceDaysInDate($last_date,1);
			$est_period = $op['order']['inhouse'] + 1;
		}
	}  //if (!$out


// -------- factors for Gantt chart & anysys -----------------
$plan_days = countDays($ord['etp'],$ord['etd']) +1;
$schedule_days = (($ord['ets'] =='0000-00-00') || (!$ord['ets'])) ? 0 : countDays($ord['ets'],$ord['etf']) +1;
$progress_days = (($ord['finish'] =='0000-00-00') || (!$ord['finish'])) ? 0 : countDays($ord['start'],$ord['finish']) +1;

$duration1 = $plan_days ." days";
$duration2 = $schedule_days ." days";
$duration3 = $progress_days ." days";

	// �w�] bar2, bar3 ����

	//������Ӥ���̤p ;�̫e��
	$top_date = ($ord['apv_date'] < $ord['etp']) ? $top_date = $ord['apv_date'] : $top_date = $ord['etp'];
$btm_date='0000-00-00';
if (($ord['status'] == 12)){
	$btm_date = $ord['shp_date'];
	$btm_date = increceDaysInDate($btm_date,2);

	$bar2_s = $ord['ets'];
	$bar2_f = $ord['etf'];
	$bar3_s = $ord['start'];
	$bar3_f = $ord['finish'];

	// �p�G�w�X�f ���o�S��finish�� --- �n����X�i�઺ ������
	if (!$ord['finish'] ||($ord['finish'] =='0000-00-00')){
		$bar3_f = $est_fdate;
		$duration3 = "est.:".$est_period." days";
	}



}elseif(($ord['status'] == 10)){  // finish production
	//������Ӥ���̫�
	$btm_date = ($ord['etd'] > $ord['etf']) ? $btm_date = $ord['etd'] : $btm_date = $ord['etf'];
	if($ord['finish'] && ($ord['finish'] !='0000-00-00')){
		$btm_date = ($btm_date > $ord['finish']) ? $btm_date = $btm_date : $btm_date = $ord['finish'];
	}
	$btm_date = increceDaysInDate($btm_date,2);

	$bar2_s = $ord['ets'];
	$bar2_f = $ord['etf'];
	$bar3_s = $ord['start'];
	$bar3_f = $ord['finish'];

}elseif(($ord['status'] == 8)){  // producing
	//������Ӥ���̫�
	$btm_date = ($ord['etd'] > $ord['etf']) ? $btm_date = $ord['etd'] : $btm_date = $ord['etf'];
	$btm_date = ($btm_date > $est_fdate) ? $btm_date = $btm_date : $btm_date = $est_fdate;
	$btm_date = increceDaysInDate($btm_date,2);

	$bar2_s = $ord['ets'];
	$bar2_f = $ord['etf'];

	$bar3_s = $ord['start'];
	$bar3_f = $est_fdate;  //�H�w����N�J
	$duration3 = "est.:".$est_period." days";
//******** bar3 ��


}elseif(($ord['status'] == 7)){  //cfm schedule no output
	//������Ӥ���̫�
	$btm_date = ($ord['etd'] > $ord['etf']) ? $btm_date = $ord['etd'] : $btm_date = $ord['etf'];
	$btm_date = increceDaysInDate($btm_date,2);

	$bar2_s = $ord['ets'];
	$bar2_f = $ord['etf'];

	$bar3_s = increceDaysInDate($top_date,5);
	$bar3_f = increceDaysInDate($top_date,5);
	$duration3 = "[unset]";

}elseif(($ord['status'] == 6)){  // schedule but not cfm schedule
	//������Ӥ���̫�
	$btm_date = ($ord['etd'] > $ord['etf']) ? $btm_date = $ord['etd'] : $btm_date = $ord['etf'];
	$btm_date = increceDaysInDate($btm_date,2);

	$bar2_s = $ord['ets'];
	$bar2_f = $ord['etf'];
	$duration2 = "[un-CFM]";
//******** bar2 ��


	$bar3_s = increceDaysInDate($top_date,5);
	$bar3_f = increceDaysInDate($top_date,5);
	$duration3 = "[unset]";


}elseif(($ord['status'] == 4)){  // order APV
	$btm_date = $ord['etd'];
	$btm_date = increceDaysInDate($btm_date,2);

	$bar2_s = increceDaysInDate($top_date,5);
	$bar2_f = increceDaysInDate($top_date,5);
	$duration2 = "[unset]";

	$bar3_s = increceDaysInDate($top_date,5);
	$bar3_f = increceDaysInDate($top_date,5);
	$duration3 = "[unset]";

}


if($ord['qty'] != 0){
	$finish_rate = number_format(($ttl_qty/$ord['qty']),4, '.', ',');
}else{
	$finish_rate = 0;
}
$T = $finish_rate *100;;
$finish = $T."%";

// �]�� jpgraph �S�k���� progress �j�� 100%
if ($finish_rate > 1){ $progress = 1; }else{ $progress = $finish_rate; }

// Gantt chart-----------------------------------------------------------------
include_once($config['root_dir']."/lib/src/jpgraph.php");
include_once($config['root_dir']."/lib/src/jpgraph_gantt.php");

$graph = new GanttGraph(700);

$graph->title->Set("ORDER PROGRESS Chart");
$graph->subtitle->Set($PHP_ord_num." - [".$ord['qty']." unit] FTY: ".$ord['factory']." by:[ ".$ord['creator']." ]");

// Setup some "very" nonstandard colors
$graph->SetMargin(15,50,20,40);
$graph->title->SetFont(FF_ARIAL,FS_BOLD,10);
$graph->SetMarginColor('lightgreen@0.8');
$graph->SetBox(true,'yellow:0.6',2);
$graph->SetFrame(true,'darkgreen',4);
$graph->scale->divider->SetColor('yellow:0.6');
$graph->scale->dividerh->SetColor('yellow:0.6');
$graph->SetShadow();

$graph->SetDateRange($top_date,$btm_date);

// Display month and year scale with the gridlines
$graph->ShowHeaders(GANTT_HWEEK | GANTT_HDAY | GANTT_HMONTH | GANTT_HYEAR);
$graph->scale->week->SetStyle(WEEKSTYLE_FIRSTDAY2);

$graph->scale->month->SetFontColor("white");
$graph->scale->month->SetBackgroundColor("teal");

$graph->scale->month->grid->SetColor('gray');
$graph->scale->month->grid->Show(true);
$graph->scale->year->grid->SetColor('gray');
$graph->scale->year->grid->Show(true);

// set TODAY Vline
$vline = new GanttVline($TODAY,'Today','red',2,'solid');
$vline->SetDayOffset(0.5);
$vline->title->SetColor('red');
$graph->Add($vline);


// set APV Vline
$apv = new GanttVline($ord['apv_date'],'APV','blue',2,'solid');
$apv->SetDayOffset(0.5);
$apv->title->SetColor('blue');
$graph->Add($apv);

// Setup activity info

// For the titles we also add a minimum width of 100 pixels for the Task name column
$graph->scale->actinfo->SetColTitles(
    array('Project','Duration'),array(70,20));
$graph->scale->actinfo->SetBackgroundColor('teal');
$graph->scale->actinfo->SetFontColor("white");
$graph->scale->actinfo->SetFont(FF_ARIAL,FS_BOLD,10);
$graph->scale->actinfo->vgrid->SetStyle('solid');
$graph->scale->actinfo->vgrid->SetColor('gray');

// Data for our example activities
$data = array(
	array(0,array("Pre-schedule",$duration1),$ord['etp'],$ord['etd'],FF_ARIAL,FS_BOLD,9),
	array(5,array("FTY schedule",$duration2),$bar2_s,$bar2_f,FF_ARIAL,FS_BOLD,9),
	array(6,array("Production",$duration3),$bar3_s,$bar3_f,FF_ARIAL,FS_BOLD,9)
);
	
// Create the bars and add them to the gantt chart

for($i=0; $i<count($data); ++$i) {
if ($data[$i][3] > 0)
{
	$bar = new GanttBar($data[$i][0],$data[$i][1],$data[$i][2],$data[$i][3],$finish,10);
	if( count($data[$i])>4 )
		$bar->title->SetFont($data[$i][4],$data[$i][5],$data[$i][6]);
	$bar->SetPattern(BAND_RDIAG,"yellow");
	$bar->SetFillColor("gray");
	$bar->progress->Set($progress);
	$bar->progress->SetPattern(GANTT_SOLID,"teal");
	$graph->Add($bar);
}
}
//���n�ɧ��� BAR2 �� BAR3���C�� =============



// milestone for fabric ship, main acc ship....
// fabric ship
if($ord['mat_shp'] && ($ord['mat_shp'] != '0000-00-00')){
	$mat_date = $ord['mat_shp'];
	$mat_des = $ord['mat_shp'];
}else{
	$mat_date = increceDaysInDate($top_date,3);
	$mat_des = 'no fabric info !';
}
$fabric_ship = new MileStone(1,"- fabric receive",$mat_date,"($mat_des)");
$fabric_ship->caption->SetFont(FF_ARIAL,FS_NORMAL,8);
$fabric_ship->title->SetColor('firebrick1');
$fabric_ship->caption->SetColor('black');
$graph->Add($fabric_ship);

// key accessories ship
if($ord['m_acc_shp'] && ($ord['m_acc_shp'] != '0000-00-00')){
	$macc_date = $ord['m_acc_shp'];
	$macc_des = $ord['m_acc_shp'];
}else{
	$macc_date = increceDaysInDate($top_date,3);
	$macc_des = 'no key acc. info !';
}
$macc_ship = new MileStone(2,"- key acc receive",$macc_date,"($macc_des)");
$macc_ship->caption->SetFont(FF_ARIAL,FS_NORMAL,8);
$macc_ship->title->SetColor('firebrick1');
$macc_ship->caption->SetColor('black');
$graph->Add($macc_ship);

// sample approved
if($ord['smpl_apv'] && ($ord['smpl_apv'] != '0000-00-00')){
	$smpl_date = $ord['smpl_apv'];
	$smpl_des = $ord['smpl_apv'];
}else{
	$smpl_date = increceDaysInDate($top_date,3);
	$smpl_des = 'no smpl APV info !';
}
$smpl_apv = new MileStone(3,"- sample APV",$smpl_date,"($smpl_des)");
$smpl_apv->caption->SetFont(FF_ARIAL,FS_NORMAL,8);
$smpl_apv->title->SetColor('tomato3');
$smpl_apv->caption->SetColor('black');
$graph->Add($smpl_apv);

// shipping
if($ord['shp_date'] && ($ord['shp_date'] != '0000-00-00')){
	$shp_date = $ord['shp_date'];
	$shp_des = $ord['shp_date'];
}else{
	$shp_date = increceDaysInDate($top_date,3);
	$shp_des = 'no shipping info !';
}
$shipping = new MileStone(7,"- shipping",$shp_date,"($shp_des)");
$shipping->caption->SetFont(FF_ARIAL,FS_BOLD,8);
$shipping->title->SetColor('blue');
$shipping->caption->SetColor('black');
$graph->Add($shipping);

		$op['order']['chart'] = $graph->Stroke('picture/order_out.png');
	
		$op['dur1'] = $duration1;
		$op['dur2'] = $duration2;
		$op['dur3'] = $duration3;

		if(file_exists($GLOBALS['config']['root_dir']."/picture/".$PHP_ord_num.".jpg")){
			$op['main_pic'] = "./picture/".$PHP_ord_num.".jpg";
		} else {
			$op['main_pic'] = "./images/graydot.gif";
		}


		$layout->assign($op);
		$layout->display($TPL_ORDER_OUTPUT);		    	    
	
	break;









#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//			job 36  [�Ͳ��s�y][�Ͳ� �Ƶ{]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//			 job 36X    �T�{�q�� �O���ন excel ��
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "schedule_excel":

		if(!$admin->is_power(3,6,"view")){ 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$parm = array(	"dept"		=>  $PHP_dept_code,
						"ord_num"	=>  $PHP_order_num,
						"cust"		=>	$PHP_cust,
						"ref"		=>	$PHP_ref,
						"fty"		=>	$PHP_factory,
				);

		if (!$ord = $order->apved_search(2,'',1000)) {  // 2005/11/24 �[�J�ĤT�ӰѼ� ���ܷj�M�j�p
			$op['msg']= $order->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$a = $ord['max_no'];
	
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

	// �g�J title

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
	  $f2->set_fg_color('white');

	  $worksheet1->set_column(0,0,10);
	  $worksheet1->set_column(0,1,5);
	  $worksheet1->set_column(0,2,5);
	  $worksheet1->set_column(0,3,8);
	  $worksheet1->set_column(0,4,3);
	  $worksheet1->set_column(0,5,8);
	  $worksheet1->set_column(0,6,8);
	  $worksheet1->set_column(0,7,8);
	  $worksheet1->set_column(0,8,6);
	  $worksheet1->set_column(0,9,6);
	  $worksheet1->set_column(0,10,14);
	  $worksheet1->set_column(0,11,14);
	  $worksheet1->set_column(0,12,14);
	  $worksheet1->set_column(0,13,14);
	  $worksheet1->set_column(0,14,14);
	  $worksheet1->set_column(0,15,14);
	  $worksheet1->set_column(0,16,5);
	  $worksheet1->set_column(0,17,0);
	  $worksheet1->set_column(0,18,14);
	  $worksheet1->set_column(0,19,14);
	  $worksheet1->set_column(0,20,14);
	  $worksheet1->set_column(0,21,14);
	  $worksheet1->set_column(0,22,14);
	  $worksheet1->set_column(0,23,14);
	  $worksheet1->set_column(0,24,0);
	  $worksheet1->set_column(0,25,4);
	  $worksheet1->set_column(0,26,8);
	  $worksheet1->set_column(0,27,10);
	  $worksheet1->set_column(0,28,10);
	  $worksheet1->set_column(0,29,10);
	  $worksheet1->set_column(0,30,10);
	  $worksheet1->set_column(0,31,6);
	  $worksheet1->set_column(0,32,15);

	  $worksheet1->write_string(0,1,"Order List ( order APVed )");
	  $worksheet1->write(0,8,"printed:".$now);

	  $worksheet1->write_string(1,0,"Order#",$formatot);
	  $worksheet1->write_string(1,1,"cust",$formatot);
	  $worksheet1->write_string(1,2,"style",$formatot);
	  $worksheet1->write_string(1,3,"Q'ty",$formatot);
	  $worksheet1->write_string(1,4,"unit",$formatot);
	  $worksheet1->write_string(1,5,"finish Q'ty",$formatot);
	  $worksheet1->write_string(1,6,"shp Q'ty",$formatot);
	  $worksheet1->write_string(1,7,"order SU",$formatot);
	  $worksheet1->write_string(1,8,"SPT",$formatot);
	  $worksheet1->write_string(1,9,"IE",$formatot);
	  $worksheet1->write_string(1,10,"ETD",$formatot);
	  $worksheet1->write_string(1,11,"ETF",$formatot);
	  $worksheet1->write_string(1,12,"ETP",$formatot);
	  $worksheet1->write_string(1,13,"ETS",$formatot);
	  $worksheet1->write_string(1,14,"start Date",$formatot);
	  $worksheet1->write_string(1,15,"finsh Date",$formatot);
	  $worksheet1->write_string(1,16,"FTY",$formatot);
	  $worksheet1->write_string(1,17,"",$formatot);
	  $worksheet1->write_string(1,18,"open date",$formatot);
	  $worksheet1->write_string(1,19,"order apv",$formatot);
	  $worksheet1->write_string(1,20,"smpl apv",$formatot);
	  $worksheet1->write_string(1,21,"patt upload",$formatot);
	  $worksheet1->write_string(1,22,"fab. shp",$formatot);
	  $worksheet1->write_string(1,23,"m. acc shp",$formatot);
	  $worksheet1->write_string(1,24,"",$formatot);
	  $worksheet1->write_string(1,25,"dept.",$formatot);
	  $worksheet1->write_string(1,26,"in charged",$formatot);
	  $worksheet1->write_string(1,27,"style#",$formatot);
	  $worksheet1->write_string(1,28,"smpl order#",$formatot);
	  $worksheet1->write_string(1,29,"pattn #",$formatot);
	  $worksheet1->write_string(1,30,"Quota cat.",$formatot);
	  $worksheet1->write_string(1,31,"revise",$formatot);
	  $worksheet1->write_string(1,32,"status",$formatot);

	  // Format for the numbers
	  $formatnum =& $workbook->add_format();
	  $formatnum->set_size(10);
	  $formatnum->set_align('center');
	  $formatnum->set_color('black');
	  $formatnum->set_fg_color('B8D7D8');

	for ($i=0; $i<sizeof($ord['sorder']); $i++){

			if ($ord['sorder'][$i]['status'] == 4) { $status_des ="APVED";
			}elseif($ord['sorder'][$i]['status'] == 5) { $status_des ="Reject";
			}elseif($ord['sorder'][$i]['status'] == 6) { $status_des ="SCHDL CFMing";
			}elseif($ord['sorder'][$i]['status'] == 7) { $status_des ="SCHDL CFMed";
			}elseif($ord['sorder'][$i]['status'] == 8) { $status_des ="PRODUCING";
			}elseif($ord['sorder'][$i]['status'] == 10) { $status_des =" FINISHED ";
			}elseif($ord['sorder'][$i]['status'] == 12) { $status_des =" SHIPPED ";
			}

	  $worksheet1->write($i+2,0,$ord['sorder'][$i]['order_num'],$formatnum);
	  $worksheet1->write($i+2,1,$ord['sorder'][$i]['cust'],$f2);
	  $worksheet1->write($i+2,2,$ord['sorder'][$i]['style'],$f2);
	  $worksheet1->write($i+2,3,$ord['sorder'][$i]['qty']);
	  $worksheet1->write($i+2,4,$ord['sorder'][$i]['unit']);
	  $worksheet1->write($i+2,5,$ord['sorder'][$i]['qty_done']);
	  $worksheet1->write($i+2,6,$ord['sorder'][$i]['qty_shp']);
	  $worksheet1->write($i+2,7,$ord['sorder'][$i]['su']);
	  $worksheet1->write($i+2,8,$ord['sorder'][$i]['ie_time1']);
	  $worksheet1->write($i+2,9,$ord['sorder'][$i]['ie1']);
	  $worksheet1->write($i+2,10,$ord['sorder'][$i]['etd'],$f2);
	  $worksheet1->write($i+2,11,$ord['sorder'][$i]['etf'],$f2);
	  $worksheet1->write($i+2,12,$ord['sorder'][$i]['etp'],$f2);
	  $worksheet1->write($i+2,13,$ord['sorder'][$i]['ets'],$f2);
	  $worksheet1->write($i+2,14,$ord['sorder'][$i]['start'],$f2);
	  $worksheet1->write($i+2,15,$ord['sorder'][$i]['finish'],$f2);
	  $worksheet1->write($i+2,16,$ord['sorder'][$i]['factory'],$f2);
	  $worksheet1->write($i+2,17,'',$formatot);
	  $worksheet1->write($i+2,18,$ord['sorder'][$i]['opendate'],$f2);
	  $worksheet1->write($i+2,19,$ord['sorder'][$i]['apv_date'],$f2);
	  $worksheet1->write($i+2,20,$ord['sorder'][$i]['smpl_apv'],$f2);
	  $worksheet1->write($i+2,21,$ord['sorder'][$i]['ptn_upload'],$f2);
	  $worksheet1->write($i+2,22,$ord['sorder'][$i]['mat_shp'],$f2);
	  $worksheet1->write($i+2,23,$ord['sorder'][$i]['m_acc_shp'],$f2);
	  $worksheet1->write($i+2,24,'',$formatot);
	  $worksheet1->write($i+2,25,$ord['sorder'][$i]['dept'],$f2);
	  $worksheet1->write($i+2,26,$ord['sorder'][$i]['creator'],$f2);
	  $worksheet1->write($i+2,27,$ord['sorder'][$i]['style_num'],$f2);
	  $worksheet1->write($i+2,28,$ord['sorder'][$i]['smpl_ord'],$f2);
	  $worksheet1->write($i+2,29,$ord['sorder'][$i]['patt_num'],$f2);
	  $worksheet1->write($i+2,30,$ord['sorder'][$i]['quota'],$f2);
	  $worksheet1->write($i+2,31,$ord['sorder'][$i]['revise'],$f2);
	  $worksheet1->write($i+2,32,$status_des,$f2);
	
	}

  $workbook->close();

	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//			job 35  [�Ͳ��s�y][IE��]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//			 job 35X    �Ͳ��O���ন excel ��
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "ie_excel":

		if(!$admin->is_power(3,5,"view")){ 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$parm = array(	"dept"		=>  $PHP_dept_code,
						"ord_num"	=>  $PHP_order_num,
						"cust"		=>	$PHP_cust,
						"ref"		=>	$PHP_ref,
						"fty"		=>	$PHP_factory,
				);

		if (!$ord = $order->search(1,'',1000)) {  // 2005/11/24 �[�J�ĤT�ӰѼ� ���ܷj�M�j�p
			$op['msg']= $order->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$a = $ord['max_no'];

	

	
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
	  HeaderingExcel('order.xls');
	 
	  // Creating a workbook
	  $workbook = new Workbook("-");

	  // Creating the first worksheet
	  $worksheet1 =& $workbook->add_worksheet('order list');

		$now = $GLOBALS['THIS_TIME'];

	// �g�J title

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

	  $worksheet1->set_column(0,0,10);
	  $worksheet1->set_column(0,1,5);
	  $worksheet1->set_column(0,2,5);
	  $worksheet1->set_column(0,3,8);
	  $worksheet1->set_column(0,4,3);
	  $worksheet1->set_column(0,5,8);
	  $worksheet1->set_column(0,6,6);
	  $worksheet1->set_column(0,7,6);
	  $worksheet1->set_column(0,8,14);
	  $worksheet1->set_column(0,9,14);
	  $worksheet1->set_column(0,10,5);
	  $worksheet1->set_column(0,11,0);
	  $worksheet1->set_column(0,12,14);
	  $worksheet1->set_column(0,13,14);
	  $worksheet1->set_column(0,14,14);
	  $worksheet1->set_column(0,15,14);
	  $worksheet1->set_column(0,16,0);
	  $worksheet1->set_column(0,17,4);
	  $worksheet1->set_column(0,18,8);
	  $worksheet1->set_column(0,19,10);
	  $worksheet1->set_column(0,20,10);
	  $worksheet1->set_column(0,21,10);
	  $worksheet1->set_column(0,22,10);
	  $worksheet1->set_column(0,23,6);
	  $worksheet1->set_column(0,24,15);

	  $worksheet1->write_string(0,1,"Order List");
	  $worksheet1->write(0,3,$now);

	  $worksheet1->write_string(1,0,"Order#",$formatot);
	  $worksheet1->write_string(1,1,"cust",$formatot);
	  $worksheet1->write_string(1,2,"style",$formatot);
	  $worksheet1->write_string(1,3,"Q'ty",$formatot);
	  $worksheet1->write_string(1,4,"unit",$formatot);
	  $worksheet1->write_string(1,5,"SU",$formatot);
	  $worksheet1->write_string(1,6,"SPT",$formatot);
	  $worksheet1->write_string(1,7,"IE",$formatot);
	  $worksheet1->write_string(1,8,"ETD",$formatot);
	  $worksheet1->write_string(1,9,"ETP",$formatot);
	  $worksheet1->write_string(1,10,"FTY",$formatot);
	  $worksheet1->write_string(1,11,"",$formatot);
	  $worksheet1->write_string(1,12,"open date",$formatot);
	  $worksheet1->write_string(1,13,"order apv",$formatot);
	  $worksheet1->write_string(1,14,"smpl apv",$formatot);
	  $worksheet1->write_string(1,15,"patt upload",$formatot);
	  $worksheet1->write_string(1,16,"",$formatot);
	  $worksheet1->write_string(1,17,"dept.",$formatot);
	  $worksheet1->write_string(1,18,"in charged",$formatot);
	  $worksheet1->write_string(1,19,"style#",$formatot);
	  $worksheet1->write_string(1,20,"smpl order#",$formatot);
	  $worksheet1->write_string(1,21,"pattn #",$formatot);
	  $worksheet1->write_string(1,22,"Quota cat.",$formatot);
	  $worksheet1->write_string(1,23,"revise",$formatot);
	  $worksheet1->write_string(1,24,"status",$formatot);


	  // Format for the numbers
	  $formatnum =& $workbook->add_format();
	  $formatnum->set_size(10);
	  $formatnum->set_align('center');
	  $formatnum->set_color('black');
//	  $formatnum->set_pattern('bold');
	  $formatnum->set_fg_color('B8D7D8');


	for ($i=0;$i<sizeof($ord['sorder']);$i++){

			$status_des = get_ord_status($ord['sorder'][$i]['status']);
	  
	  $worksheet1->write($i+2,0,$ord['sorder'][$i]['order_num'],$formatnum);
	  $worksheet1->write($i+2,1,$ord['sorder'][$i]['cust'],$f2);
	  $worksheet1->write($i+2,2,$ord['sorder'][$i]['style'],$f2);
	  $worksheet1->write($i+2,3,$ord['sorder'][$i]['qty']);
	  $worksheet1->write($i+2,4,$ord['sorder'][$i]['unit']);
	  $worksheet1->write($i+2,5,$ord['sorder'][$i]['su']);
	  $worksheet1->write($i+2,6,$ord['sorder'][$i]['ie_time1']);
	  $worksheet1->write($i+2,7,$ord['sorder'][$i]['ie1']);
	  $worksheet1->write($i+2,8,$ord['sorder'][$i]['etd'],$f2);
	  $worksheet1->write($i+2,9,$ord['sorder'][$i]['etp'],$f2);
	  $worksheet1->write($i+2,10,$ord['sorder'][$i]['factory'],$f2);
	  $worksheet1->write($i+2,11,'',$formatot);
	  $worksheet1->write($i+2,12,$ord['sorder'][$i]['opendate'],$f2);
	  $worksheet1->write($i+2,13,$ord['sorder'][$i]['apv_date'],$f2);
	  $worksheet1->write($i+2,14,$ord['sorder'][$i]['smpl_apv'],$f2);
	  $worksheet1->write($i+2,15,$ord['sorder'][$i]['ptn_upload'],$f2);
	  $worksheet1->write($i+2,16,'',$formatot);
	  $worksheet1->write($i+2,17,$ord['sorder'][$i]['dept'],$f2);
	  $worksheet1->write($i+2,18,$ord['sorder'][$i]['creator'],$f2);
	  $worksheet1->write($i+2,19,$ord['sorder'][$i]['style_num'],$f2);
	  $worksheet1->write($i+2,20,$ord['sorder'][$i]['smpl_ord'],$f2);
	  $worksheet1->write($i+2,21,$ord['sorder'][$i]['patt_num'],$f2);
	  $worksheet1->write($i+2,22,$ord['sorder'][$i]['quota'],$f2);
	  $worksheet1->write($i+2,23,$ord['sorder'][$i]['revise'],$f2);
	  $worksheet1->write($i+2,24,$status_des,$f2);
	
	}


  $workbook->close();


	break;


//-------------------------------------------------------------------------------------
//			 job 38X    �Ͳ� �O���ন excel ��
//-------------------------------------------------------------------------------------
	case "pdt_excel":

		check_authority(4,4,"view");

		$parm = array(	"ord_num"	=>  $PHP_order_num,
						"cust"		=>	$PHP_cust,
						"ref"		=>	$PHP_ref,
						"fty"		=>	$PHP_factory,
				);

		if (!$ord = $order->pdt_search($parm, 1000)) {  // 2006/05/07 �[�J�ĤT�ӰѼ� ���ܷj�M�j�p
			$op['msg']= $order->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$a = $ord['max_no'];

	

	
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
	  HeaderingExcel('produced.xls');
	 
	  // Creating a workbook
	  $workbook = new Workbook("-");

	  // Creating the first worksheet
	  $worksheet1 =& $workbook->add_worksheet('order list');

		$now = $GLOBALS['THIS_TIME'];

	// �g�J title

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
		$ary_col_size = array(10,5,5,8,3,8,8,8,6,6,14,14,14,14,14,14,5,0,14,14,14,14,14,14,0,4,8,10,10,10,10,6,15);
		for($i=0; $i< sizeof($ary_col_size); $i++) $worksheet1->set_column(0,$i,$ary_col_size[$i]);

	  $worksheet1->write_string(0,1,"Order List ( productiion scheudled CFMed )");
	  $worksheet1->write(0,8,"printed:".$now);
		
		$ary_col_title = array ("Order#","cust","style","Q'ty","unit","finish Q'ty","shp Q'ty","order SU",
														"SPT","IE","ETD","ETF","ETP","ETS","start Date","finsh Date","FTY","","open date",
														"order apv","smpl apv","patt upload","fab. shp","m. acc shp","","dept.",
														"in charged","style#","smpl order#","pattn #","Quota cat.","revise","status"
														);
		for($i=0; $i< sizeof($ary_col_title); $i++)  $worksheet1->write_string(1,$i,$ary_col_title[$i],$formatot);

	  // Format for the numbers
	  $formatnum =& $workbook->add_format();
	  $formatnum->set_size(10);
	  $formatnum->set_align('center');
	  $formatnum->set_color('black');
	  $formatnum->set_fg_color('B8D7D8');

	for ($i=0;$i<sizeof($ord['sorder']);$i++){
	 
	 $status_des = get_ord_status($ord['sorder'][$i]['status']);

	  $worksheet1->write($i+2,0,$ord['sorder'][$i]['order_num'],$formatnum);
	  $worksheet1->write($i+2,1,$ord['sorder'][$i]['cust'],$f2);
	  $worksheet1->write($i+2,2,$ord['sorder'][$i]['style'],$f2);
	  $worksheet1->write($i+2,3,$ord['sorder'][$i]['qty']);
	  $worksheet1->write($i+2,4,$ord['sorder'][$i]['unit']);

	  $worksheet1->write($i+2,5,$ord['sorder'][$i]['qty_done']);
	  $worksheet1->write($i+2,6,$ord['sorder'][$i]['qty_shp']);
	  
	  $worksheet1->write($i+2,7,$ord['sorder'][$i]['su']);
	  $worksheet1->write($i+2,8,$ord['sorder'][$i]['ie_time1']);
	  $worksheet1->write($i+2,9,$ord['sorder'][$i]['ie1']);
	  $worksheet1->write($i+2,10,$ord['sorder'][$i]['etd'],$f2);

	  $worksheet1->write($i+2,11,$ord['sorder'][$i]['etf'],$f2);
	  
	  $worksheet1->write($i+2,12,$ord['sorder'][$i]['etp'],$f2);

	  $worksheet1->write($i+2,13,$ord['sorder'][$i]['ets'],$f2);
	  $worksheet1->write($i+2,14,$ord['sorder'][$i]['start'],$f2);
	  $worksheet1->write($i+2,15,$ord['sorder'][$i]['finish'],$f2);
	  
	  $worksheet1->write($i+2,16,$ord['sorder'][$i]['factory'],$f2);
	  $worksheet1->write($i+2,17,'',$formatot);
	  $worksheet1->write($i+2,18,$ord['sorder'][$i]['opendate'],$f2);
	  $worksheet1->write($i+2,19,$ord['sorder'][$i]['apv_date'],$f2);
	  $worksheet1->write($i+2,20,$ord['sorder'][$i]['smpl_apv'],$f2);
	  $worksheet1->write($i+2,21,$ord['sorder'][$i]['ptn_upload'],$f2);

	  $worksheet1->write($i+2,22,$ord['sorder'][$i]['mat_shp'],$f2);
	  $worksheet1->write($i+2,23,$ord['sorder'][$i]['m_acc_shp'],$f2);
	  
	  $worksheet1->write($i+2,24,'',$formatot);
	  $worksheet1->write($i+2,25,$ord['sorder'][$i]['dept'],$f2);
	  $worksheet1->write($i+2,26,$ord['sorder'][$i]['creator'],$f2);
	  $worksheet1->write($i+2,27,$ord['sorder'][$i]['style_num'],$f2);
	  $worksheet1->write($i+2,28,$ord['sorder'][$i]['smpl_ord'],$f2);
	  $worksheet1->write($i+2,29,$ord['sorder'][$i]['patt_num'],$f2);
	  $worksheet1->write($i+2,30,$ord['sorder'][$i]['quota'],$f2);
	  $worksheet1->write($i+2,31,$ord['sorder'][$i]['revise'],$f2);
	  $worksheet1->write($i+2,32,$status_des,$f2);
	
	}


  $workbook->close();


	break;


//-------------------------------------------------------------------------------------
//			 job 101X    �q�� �O���ন excel ��
//-------------------------------------------------------------------------------------
	case "order_excel":

		if(!$admin->is_power(10,1,"view") && !$admin->is_power(10,4,"view")){  // �[�J���ͥ��]���excel�� 2006/0106
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$parm = array(	"dept"		=>  $PHP_dept_code,
						"ord_num"	=>  $PHP_order_num,
						"cust"		=>	$PHP_cust,
						"ref"		=>	$PHP_ref,
						"fty"		=>	$PHP_factory,
				);

		if (!$ord = $order->search(1,'',1000)) {  // 2005/11/24 �[�J�ĤT�ӰѼ� ���ܷj�M�j�p
			$op['msg']= $order->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$a = $ord['max_no'];

	

	
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
	  HeaderingExcel('order.xls');
	 
	  // Creating a workbook
	  $workbook = new Workbook("-");

	  // Creating the first worksheet
	  $worksheet1 =& $workbook->add_worksheet('order list');

		$now = $GLOBALS['THIS_TIME'];

	// �g�J title

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
	  $ary = array(10,5,5,8,3,8,14,14,5,10,10,10,6,10,10,10,6,0,14,14,14,14,6,6,0,4,8,10,10,10,10,10,10,10,6,20);
	  for ($i=0; $i<sizeof($ary); $i++)
	  {
	  	$worksheet1->set_column(0,$i,$ary[$i]);
	  }

	  $worksheet1->write_string(0,1,"Order List");
	  $worksheet1->write(0,3,$now);
	
	  $ary = array("Order#","cust","style","Q'ty","Unit","SU","ETD","ETP","FTY","@FOB prc","@CM cost","@fabric prc","YY","total Sales","total cost","gross margin","GM %",
	              "","open date","order apv","smpl apv","patt upload","SPT","IE","","dept.","in charged","style#","smpl order#","pattn #","Quota cat.","Quota fee",
	              "comm. fee","smpl fee","revise","status");
	  for ($i=0; $i<sizeof($ary); $i++)
	  {
	  	 $worksheet1->write_string(1,$i,$ary[$i],$formatot);
	  }       

	  // Format for the numbers
	  $formatnum =& $workbook->add_format();
	  $formatnum->set_size(10);
	  $formatnum->set_align('center');
	  $formatnum->set_color('black');
	  $formatnum->set_fg_color('B8D7D8');

	for ($i=0; $i < sizeof($ord['sorder']); $i++){
		$order_num = $ord['sorder'][$i]['order_num'];
		// �p���`���� �Τ�Q�v
		$tt_cost = ($ord['sorder'][$i]['mat_u_cost']*$ord['sorder'][$i]['mat_useage']+$ord['sorder'][$i]['acc_u_cost']+$ord['sorder'][$i]['quota_fee']+$ord['sorder'][$i]['comm_fee']+$ord['sorder'][$i]['cm'])*$ord['sorder'][$i]['qty']-$ord['sorder'][$i]['smpl_fee'];
		$total_cost = number_format($tt_cost,2,'.','');

		$tt_sales = number_format($ord['sorder'][$i]['qty']*$ord['sorder'][$i]['uprice'],2,'.','');
		$gross = number_format($tt_sales-$total_cost,2,'.','');
		$status_des = get_ord_status($ord['sorder'][$i]['status']);

	  $worksheet1->write($i+2,0,$order_num,$formatnum);
	  $worksheet1->write($i+2,1,$ord['sorder'][$i]['cust'],$f2);
	  $worksheet1->write($i+2,2,$ord['sorder'][$i]['style'],$f2);
	  $worksheet1->write($i+2,3,$ord['sorder'][$i]['qty']);
	  $worksheet1->write($i+2,4,$ord['sorder'][$i]['unit']);
	  $worksheet1->write($i+2,5,$ord['sorder'][$i]['su']);
	  $worksheet1->write($i+2,6,$ord['sorder'][$i]['etd'],$f2);
	  $worksheet1->write($i+2,7,$ord['sorder'][$i]['etp'],$f2);
	  $worksheet1->write($i+2,8,$ord['sorder'][$i]['factory'],$f2);
	  $worksheet1->write($i+2,9,$ord['sorder'][$i]['uprice']);
	  $worksheet1->write($i+2,10,$ord['sorder'][$i]['cm']);
	  $worksheet1->write($i+2,11,$ord['sorder'][$i]['mat_u_cost']);
	  $worksheet1->write($i+2,12,$ord['sorder'][$i]['mat_useage']);
	  $worksheet1->write($i+2,13,$tt_sales);
	  $worksheet1->write($i+2,14,$total_cost);
	  $worksheet1->write($i+2,15,$gross);
	  $worksheet1->write($i+2,16,$ord['sorder'][$i]['gmr']);

	  $worksheet1->write($i+2,17,'',$formatot);
	  $worksheet1->write($i+2,18,$ord['sorder'][$i]['opendate'],$f2);
	  $worksheet1->write($i+2,19,$ord['sorder'][$i]['apv_date'],$f2);
	  $worksheet1->write($i+2,20,$ord['sorder'][$i]['smpl_apv'],$f2);
	  $worksheet1->write($i+2,21,$ord['sorder'][$i]['ptn_upload'],$f2);
	  $worksheet1->write($i+2,22,$ord['sorder'][$i]['ie_time1']);
	  $worksheet1->write($i+2,23,$ord['sorder'][$i]['ie1']);
	  $worksheet1->write($i+2,24,'',$formatot);
	  $worksheet1->write($i+2,25,$ord['sorder'][$i]['dept'],$f2);
	  $worksheet1->write($i+2,26,$ord['sorder'][$i]['creator'],$f2);
	  $worksheet1->write($i+2,27,$ord['sorder'][$i]['style_num'],$f2);
	  $worksheet1->write($i+2,28,$ord['sorder'][$i]['smpl_ord'],$f2);
	  $worksheet1->write($i+2,29,$ord['sorder'][$i]['patt_num'],$f2);
	  $worksheet1->write($i+2,30,$ord['sorder'][$i]['quota'],$f2);
	  $worksheet1->write($i+2,31,$ord['sorder'][$i]['quota_fee']);
	  $worksheet1->write($i+2,32,$ord['sorder'][$i]['comm_fee']);
	  $worksheet1->write($i+2,33,$ord['sorder'][$i]['smpl_fee']);
	  $worksheet1->write($i+2,34,$ord['sorder'][$i]['revise'],$f2);
	  $worksheet1->write($i+2,35,$status_des,$f2);

	
	
	}


  $workbook->close();


	break;


















//-------------------------------------------------------------------------------------
//			 job 35   IE �O��
//-------------------------------------------------------------------------------------
    case "IE_record":
    	check_authority(4,1,"view");
		$op['msg'] = $order->msg->get(2);		
		// creat cust combo box
		$op['factory'] = $arry2->select($FACTORY,'','PHP_factory','select','');  	
		$where_str="order by cust_s_name"; //��cust_s_name�Ƨ�
		$cust_def = $cust->get_fields('cust_init_name',$where_str);
		$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//���X�Ȥ�N��
		for ($i=0; $i< sizeof($cust_def); $i++)
		{
			$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//�H [�Ȥ�N��-�Ȥ�²��] �e�{
		}
		$op['cust_select'] =  $arry2->select($cust_value,'','PHP_cust','select','',$cust_def_vue); 
		page_display($op, 4,1, $TPL_IE);		    	    
		break;



//=======================================================
    case "ie_search":
		check_authority(4,1,"view");
		
		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];   // �P�w�i�J����������
			// �q���ƦC��
			$parm = array(	
//							"dept"			=>  $PHP_dept_code,
							"order_num"		=>  $PHP_order_num,
							"cust"			=>	$PHP_cust,
							"ref"			=>	$PHP_ref,
							"factory"		=>	$PHP_factory,
				);

			
				$op['cgi']= $parm;

			
			if (!$op = $order->search(1)) {   // �Ȩ��X s_order tabel���� ��� ����pdtion
				$op['msg']= $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			for ($i=0; $i<sizeof($op['sorder']); $i++)
			{
				if (substr($user_dept,0,1)=="H" || substr($user_dept,0,1)=="L")
				{
					if ($user_dept == $op['sorder'][$i]['dept'])$op['sorder'][$i]['dept_edit']=1;
					if ($user_dept =="H0" && substr($op['sorder'][$i]['dept'],0,1)=="H")$op['sorder'][$i]['dept_edit']=1;
					if ($user_dept =="L0" && substr($op['sorder'][$i]['dept'],0,1)=="L")$op['sorder'][$i]['dept_edit']=1;
				}else{
					$op['sorder'][$i]['dept_edit']=1;
				}
			}

			$op['cgi']= $parm;
			$op['msg'] = $order->msg->get(2);
			page_display($op, 4,1, $TPL_IE_LIST); 
		break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		job 41A upload_order_pattern
#		case "upload_order_pattern":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "upload_order_pattern":

		check_authority(4,1,"add");
		$op['back_str'] = $PHP_back_str;
		$filename = $_FILES['PHP_pttn']['name'];
		$ext =  strtolower(preg_replace("/.*\.([^.]+)$/","\\1", $filename));

		if ($ext == "mdl"){   // �W���ɪ����ɦW�� mdl �� -----

			// upload pattern file to server
			$pttn_name = $PHP_num.'.mdl';  // ���w�� mdl ���ɦW
			$upload = new Upload;
			$upload->uploadFile(dirname($PHP_SELF).'/order_pttn/', 'pattern', 16, $pttn_name );
			if (!$upload){
				$op['msg'][] = $upload;				
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			$A=$order->update_field('ptn_upload', $GLOBALS['TODAY'], $PHP_id); //�O��pttn�W�Ǥ��			
			$message = "UPLOAD Pattern of #".$PHP_num;
		} else {  // �W���ɪ����ɦW  ���O  mdl �� -----

			$message = "upload file is incorrect format ! Please re-send.";
		}
//----------------------------------------------------
		$op['order'] = $order->get($PHP_id);  //���X�ӵ��O��
		if (!$op['order']) {
			$op['msg'] = $order->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}


		// �p�� lead time �� html �e�{ ------
		if($op['order']['etp'] && $op['order']['etd']){	$op['lead_time'] = countDays($op['order']['etp'],$op['order']['etd']);	}


		$op['back_str'] = $PHP_back_str;
		$op['id'] = $PHP_id;

			// �ˬd �ۤ��O�_�s�b
		if(file_exists($GLOBALS['config']['root_dir']."/picture/".$op['order']['order_num'].".jpg")){
			$op['main_pic'] = "./picture/".$op['order']['order_num'].".jpg";
		} else {
			$op['main_pic'] = "./images/graydot.gif";
		}

			// �p��  SU �� html -----------------------------
		//   2005/08/31 ��Ѹ�Ʈw�� su [�H ie �p���J ]
		$su = $op['order']['su'];
		$op['order']['f_su'] = number_format($su, 0, '', ',');
		$op['order']['qty'] = number_format($op['order']['qty'], 0, '', ',');

		$log->log_add(0,"41E",$message);

		$op['msg'][] = $message;
			page_display($op, 4,1, $TPL_IE_REC_SHOW); 		
		break;

//=======================================================
    case "order_pttn_resend":
		check_authority(4,1,"add");
				
		$A=$order->update_field('ptn_upload', '0000-00-00', $PHP_id); //�M��pttn�W�Ǥ��		
		$A=$order->update_field('paper_ptn','0', $PHP_id); //�O����paper pattern	

//----------------------------------------------------			
		$op['order'] = $order->get($PHP_id);  //���X�ӵ��O��
		if (!$op['order']) {
			$op['msg'] = $order->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}


		// �p�� lead time �� html �e�{ ------
		if($op['order']['etp'] && $op['order']['etd']){	$op['lead_time'] = countDays($op['order']['etp'],$op['order']['etd']);	}


		// �[�J��^�e�� 2005/05/05
		$back_str = $cgiget."&PHP_sr_startno=".$PHP_sr_startno."&PHP_dept_code=".$PHP_dept_code."&PHP_order_num=".$PHP_order_num."&PHP_cust=".$PHP_cust."&PHP_ref=".$PHP_ref."&PHP_factory=".$PHP_factory;

		$op['back_str'] = $back_str;
		$op['id'] = $PHP_id;

			// �ˬd �ۤ��O�_�s�b
		if(file_exists($GLOBALS['config']['root_dir']."/picture/".$op['order']['order_num'].".jpg")){
			$op['main_pic'] = "./picture/".$op['order']['order_num'].".jpg";
		} else {
			$op['main_pic'] = "./images/graydot.gif";
		}

			// �p��  SU �� html -----------------------------
		//   2005/08/31 ��Ѹ�Ʈw�� su [�H ie �p���J ]
		$su = $op['order']['su'];
		$op['order']['f_su'] = number_format($su, 0, '', ',');
		$op['order']['qty'] = number_format($op['order']['qty'], 0, '', ',');

		$message = "DEL exist pattern for resend for #".$PHP_num;
				# �O���ϥΪ̰ʺA
		$log->log_add(0,"41E",$message);

		$op['msg'][] = $message;
		page_display($op, 4,1, $TPL_IE_REC_SHOW); 
		break;


//=======================================================
    case "order_pttn_paper":
		check_authority(4,1,"add");
				
		$A=$order->update_field('ptn_upload', $TODAY, $PHP_id); //�W�Ǥ��	
		$A=$order->update_field('paper_ptn','1', $PHP_id); //�O����paper pattern	

//----------------------------------------------------			
		$op['order'] = $order->get($PHP_id);  //���X�ӵ��O��
		if (!$op['order']) {
			$op['msg'] = $order->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}


		// �p�� lead time �� html �e�{ ------
		if($op['order']['etp'] && $op['order']['etd']){	$op['lead_time'] = countDays($op['order']['etp'],$op['order']['etd']);	}


		// �[�J��^�e�� 2005/05/05
		$back_str = $cgiget."&PHP_sr_startno=".$PHP_sr_startno."&PHP_dept_code=".$PHP_dept_code."&PHP_order_num=".$PHP_order_num."&PHP_cust=".$PHP_cust."&PHP_ref=".$PHP_ref."&PHP_factory=".$PHP_factory;

		$op['back_str'] = $back_str;
		$op['id'] = $PHP_id;

			// �ˬd �ۤ��O�_�s�b
		if(file_exists($GLOBALS['config']['root_dir']."/picture/".$op['order']['order_num'].".jpg")){
			$op['main_pic'] = "./picture/".$op['order']['order_num'].".jpg";
		} else {
			$op['main_pic'] = "./images/graydot.gif";
		}

			// �p��  SU �� html -----------------------------
		//   2005/08/31 ��Ѹ�Ʈw�� su [�H ie �p���J ]
		$su = $op['order']['su'];
		$op['order']['f_su'] = number_format($su, 0, '', ',');
		$op['order']['qty'] = number_format($op['order']['qty'], 0, '', ',');

		$message = "Upload Paper Pattern for order #".$op['order']['order_num'];
				# �O���ϥΪ̰ʺA
		$log->log_add(0,"41P",$message);

		$op['msg'][] = $message;
		page_display($op, 4,1, $TPL_IE_REC_SHOW); 
		break;
//=======================================================
    case "ie_record_view":
		check_authority(4,1,"view");
		
		$op['cgi'] = array(	"cgiget"	=>  $cgiget,
							"cgino"		=>  $cgino,
							"cgi_1"		=>	$cgi_1,
							"cgi_2"		=>	$cgi_2,
							"cgi_3"		=>	$cgi_3,
							"cgi_4"		=>	$cgi_4,
							"cgi_5"		=>	$cgi_5,
				);
		
		$op['order'] = $order->get($PHP_id);  //���X�ӵ��O��
		if (!$op['order']) {
			$op['msg'] = $order->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}


		// �p�� lead time �� html �e�{ ------
		if($op['order']['etp'] && $op['order']['etd']){	$op['lead_time'] = countDays($op['order']['etp'],$op['order']['etd']);	}


		// �[�J��^�e�� 2005/05/05
		$back_str = $cgiget."&PHP_sr_startno=".$cgino."&PHP_dept_code=".$cgi_1."&PHP_order_num=".$cgi_2."&PHP_cust=".$cgi_3."&PHP_ref=".$cgi_4."&PHP_factory=".$cgi_5;

		$op['back_str'] = $back_str;
		$op['id'] = $PHP_id;

			// �ˬd �ۤ��O�_�s�b
		if(file_exists($GLOBALS['config']['root_dir']."/picture/".$op['order']['order_num'].".jpg")){
			$op['main_pic'] = "./picture/".$op['order']['order_num'].".jpg";
		} else {
			$op['main_pic'] = "./images/graydot.gif";
		}

			// �p��  SU �� html -----------------------------
		//   2005/08/31 ��Ѹ�Ʈw�� su [�H ie �p���J ]
		$su = $op['order']['su'];
		$op['order']['f_su'] = number_format($su, 0, '', ',');
		$op['order']['qty'] = number_format($op['order']['qty'], 0, '', ',');

			$op['msg'] = $order->msg->get(2);
			page_display($op, 4,1, $TPL_IE_REC_SHOW); 
		break;

//=======================================================
    case "add_ie1":

		check_authority(4,1,"add");
		$redir_str = $PHP_back."&PHP_sr_startno=".$PHP_sr_startno."&PHP_dept_code=".$cgi_1."&PHP_order_num=".$cgi_2."&PHP_cust=".$cgi_3."&PHP_ref=".$cgi_4."&PHP_factory=".$cgi_5;

			// �q���ƦC��
//			$parm = array(	"dept"			=>  $PHP_id,
//							"ord_num"		=>  $PHP_ord_num,
//							"cust"			=>	$PHP_ie_time1,
//				);

			if (!$op = $order->check_add_ie($PHP_ie_time1)) {   // �ˬd��J���O�_�� ���
				$op['msg']= $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    //   �w�� ��X�{error �L�k back�@�СССССС�
				break;
			}
			

			
			// �g�J IE �u�ɰO�� ---   �g�J  S_ORDER
//			// ���p�� ie1 ����

			// �ȼg�J �зǤu��[���] �|���p�J ie ��--- [ �T�{�ɦA���p�J ]---
			if (!$op = $order->update_field('ie_time1', $PHP_ie_time1, $PHP_id)) { 
				$op['msg']= $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

				# �O���ϥΪ̰ʺA
			$message = "added [".$PHP_ord_num."] IE SPT ";

//			$log->log_add(0,"35A",$message);


			
			redirect_page($redir_str);

		break;


//=======================================================
    case "cfm_ie1":
		
		check_authority(4,1,"edit");
		
	
		$redir_str = $PHP_back."&PHP_sr_startno=".$PHP_sr_startno."&PHP_dept_code=".$cgi_1."&PHP_order_num=".$cgi_2."&PHP_cust=".$cgi_3."&PHP_ref=".$cgi_4."&PHP_factory=".$cgi_5;
			
			if (!$op = $order->check_add_ie($PHP_ie_time1)) {   // �ˬd��J���O�_�� ���
				$op['msg']= $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    //   �w�� ��X�{error �L�k back�@�СССССС�
				break;
			}
			
			// �g�J IE  ---   �g�J  S_ORDER
			// ���p�� ie1 ����........
			$ie1 = number_format(($PHP_ie_time1/$GLOBALS['IE_TIME']),2,'.','');
			// �A�p�� su ����........
			$su =  number_format(($PHP_qty * $ie1),0,'','');

			
			$parm = array(	"id"			=>  $PHP_id,
							"su"			=>  $su,							
							"ie1"			=>  $ie1,
							"ie_time1"		=>	$PHP_ie_time1,
				);
			$ord_pdt=$order->get_pdtion($PHP_ord_num, $PHP_fty);
			
			if ($PHP_status >= 4 && $PHP_status <> 5)
			{	//ETP~ETD��SU							 
				 $etp_su=decode_mon_yy_su($ord_pdt['etp_su']); //���X�v�s�betp-su�ñNsu�ȻP�~,����}
				 for ($i=0; $i<sizeof($etp_su); $i++) //�N�v�s�bsu���~�װO���ȧR��
				 {				 	
				 	$f1=$capaci->delete_su($PHP_fty, $etp_su[$i]['year'], $etp_su[$i]['mon'], 'pre_schedule', $etp_su[$i]['su']);			 		
				 }
				 $div = $order->distri_month_su($su,$PHP_etp,$PHP_etd,$PHP_fty,'pre_schedule'); //����C��SU���x�s
				 $order->update_pdtion_field('etp_su', $div, $ord_pdt['id']);  //�x�s�setp_su
			}
			if($PHP_status >= 7){
				 //ETS~ETF��SU(�u�t�Ƶ{)
				 $fty_su=decode_mon_yy_su($ord_pdt['fty_su']);//���X�v�s�bfty-su�ñNsu�ȻP�~,����}
				 for ($i=0; $i<sizeof($fty_su); $i++)//�N�v�s�bsu���~�װO���ȧR��
				 {				 	
				 	$f1=$capaci->delete_su($PHP_fty, $fty_su[$i]['year'], $fty_su[$i]['mon'], 'schedule', $fty_su[$i]['su']);			 		
				 }
				 $div = $order->distri_month_su($su,$ord_pdt['ets'],$ord_pdt['etf'],$PHP_fty,'schedule');//����C��SU���x�s
				 $order->update_pdtion_field('fty_su', $div, $ord_pdt['id']);//�x�s�sfty_su
				 
			}
			if($PHP_status > 7){				 
				 //Out-put��SU
				 $tmp_div='';
				 $out_su=decode_mon_yy_su($ord_pdt['out_su']);//���X�v�s�bfty-su�ñNsu�ȻP�~,����}
		 		 if ($PHP_style=='PS' || $PHP_style=='BS' || $PHP_style=='BZ' || $PHP_style=='DR' || $PHP_style=='JK' || $PHP_style=='PS-J' || $PHP_style=='PS-P' || $PHP_style=='PS-S' || $PHP_style=='VS' || $PHP_style=='SS'){
					$tmp_ie=2;			
			 	 }else{
					$tmp_ie=1;			
				 }
				 for ($i=0; $i<sizeof($out_su); $i++)//�N�v�s�bsu���~�װO���ȧR��
				 {				 	
				 	$f1=$capaci->delete_su($PHP_fty, $out_su[$i]['year'], $out_su[$i]['mon'], 'actual', $out_su[$i]['su']);			 		
				 	$tmp_qty=number_format(($out_su[$i]['su'] / $tmp_ie),0,'','');
				 	$tmp_su=number_format(($tmp_qty * $ie1),0,'','');
				 	$f1=$capaci->update_su($PHP_fty, $out_su[$i]['year'], $out_su[$i]['mon'], 'actual', $tmp_su);
				 	$tmp_div=$tmp_div.$out_su[$i]['year'].$out_su[$i]['mon'].$tmp_su.",";
				 }
				 $div=substr($tmp_div,0,-1);
				 $f1=$order->update_pdtion_field('out_su', $div, $ord_pdt['id']);//�x�s�sfty_su
				 $day_out=$daily->order_daily_out($PHP_ord_num);
				 for ($i=0; $i<sizeof($day_out); $i++)
				 {
					$parm1['field_name']='su';
					$parm1['id']=$day_out[$i]['id'];
					$parm1['field_value']=number_format(($day_out[$i]['qty'] * $ie1),0,'','');
					$f1=$daily->update_field($parm1);
				 }	
				 $ship_out=$shipping->order_ship_out($PHP_ord_num);
				 for ($i=0; $i<sizeof($ship_out); $i++)
				 {
					$parm1['field_name']='su';
					$parm1['id']=$ship_out[$i]['id'];
					$parm1['field_value']=number_format(($ship_out[$i]['qty'] * $ie1),0,'','');
					$f1=$shipping->update_field($parm1);
				 }				
			}

			// �g�J �зǤu��[���] �� ie ��--- [ �g�J��ӭ� ]---
			if (!$op = $order->update_ie($parm)) { 
				$op['msg']= $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}				

				# �O���ϥΪ̰ʺA
			$message = "CFM [".$PHP_ord_num."] IE SPT ";

			$log->log_add(0,"41E",$message);

			redirect_page($redir_str);

		break;

//=======================================================
    case "add_ie2":

		check_authority(4,1,"add");
##--***** �s�W���Ƴ��� start
		$redir_str = $PHP_back."&PHP_sr_startno=".$PHP_sr_startno."&PHP_dept_code=".$cgi_1."&PHP_order_num=".$cgi_2."&PHP_cust=".$cgi_3."&PHP_ref=".$cgi_4."&PHP_factory=".$cgi_5;
##--*****�s�W���Ƴ��� end
			// �q���ƦC��

			if (!$op = $order->check_add_ie($PHP_ie_time2)) {   // �ˬd��J���O�_�� ���
				$op['msg']= $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    //   �w�� ��X�{error �L�k back�@�СССССС�
				break;
			}
			
			
			// �g�J IE �u�ɰO�� ---   �g�J  S_ORDER
//			// ���p�� ie2 ����

			// �ȼg�J �зǤu��[���] �|���p�J ie ��--- [ �T�{�ɦA���p�J ]---
			if (!$op = $order->update_field('ie_time2', $PHP_ie_time2, $PHP_id)) { 
				$op['msg']= $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

				# �O���ϥΪ̰ʺA
			$message = "Writing[".$PHP_ord_num."] final IE SPT ";

			$log->log_add(0,"41A",$message);

			redirect_page($redir_str);

		break;


//=======================================================
    case "cfm_ie2":

       check_authority(4,1,"edit");
		$redir_str = $PHP_back."&PHP_sr_startno=".$PHP_sr_startno."&PHP_dept_code=".$cgi_1."&PHP_order_num=".$cgi_2."&PHP_cust=".$cgi_3."&PHP_ref=".$cgi_4."&PHP_factory=".$cgi_5;


			if (!$op = $order->check_add_ie($PHP_ie_time2)) {   // �ˬd��J���O�_�� ���
				$op['msg']= $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    //   �w�� ��X�{error �L�k back�@�СССССС�
				break;
			}
			

			
			// �g�J IE  ---   �g�J  S_ORDER
			// ���p�� ie2 ����
			$ie2 = number_format(($PHP_ie_time2/$GLOBALS['IE_TIME']),2,'.','');

			// �g�J �зǤu��[���] �� ie ��--- [ �g�J��ӭ� ]---
			if (!$op = $order->update_2fields('ie_time2', 'ie2', $PHP_ie_time2, $ie2, $PHP_id)) { 
				$op['msg']= $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

				# �O���ϥΪ̰ʺA
			$message = "cfm [".$PHP_ord_num."] final IE SPT ";

			$log->log_add(0,"41E",$message);

			redirect_page($redir_str);

		break;









//-------------------------------------------------------------------------------------
//			 job 81  �Ͳ�����
//	
//-------------------------------------------------------------------------------------
    case "pdt_search":
		check_authority(8,2,"view");
				// creat cust combo box
		$op['factory'] = $arry2->select($FACTORY,'','PHP_fty','select','');  	
		$op['year_1'] = $arry2->select($YEAR_WORK,'','PHP_year1','select','');  	
		$op['month'] = $arry2->select($MONTH_WORK,'','PHP_month','select','');

		$op['msg'] = $order->msg->get(2);
	
			page_display($op, 8, 2, $TPL_PDT_SEARCH);    	    
		break;

//-------------------------------------------------------------------------------------
    case "pdt_view":
		check_authority(8,2,"view");

		$op['msg'] ='';

		$op['fty'] = $PHP_fty;
		$op['year1'] = $PHP_year1;
		
		if (!$op['fty']){
			$op['msg'][] = "Error ! you have to select target Factory !";
		}

		if(!$op['year1']){ 
			$op['year1'] = $GLOBALS['THIS_YEAR'];
			$PHP_year1 = $GLOBALS['THIS_YEAR'];
		
		}  // ��S��J�~���ɹw�]�����~

		if ($op['msg']){	
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		// ��Jcheck ����  �}�lsearch
//------------------------------------------  1 st year ----------------------
			
			if($PHP_year1){
			  $ord = $capaci->get_etd_ord($PHP_fty, $PHP_year1);
			  $mm=array('01','02','03','04','05','06','07','08','09','10','11','12','13');
			  for ($i=0; $i<13; $i++) $op['ord'][$mm[$i]]=0;
			  for ($i=0; $i<13; $i++) $op['sch'][$mm[$i]]=0;
			  for ($i=0; $i<13; $i++) $op['out'][$mm[$i]]=0;
			  for ($i=0; $i<13; $i++) $op['shp'][$mm[$i]]=0;
			  for ($i=0; $i<13; $i++) $op['un_shp'][$mm[$i]]=0;
			  for ($i=0; $i<13; $i++) $op['un_out'][$mm[$i]]=0;
			  for ($i=0; $i<13; $i++) $op['un_sch'][$mm[$i]]=0;
			  for ($i=0; $i<12; $i++) $out_tmp[$mm[$i]]=0;
			  for ($i=0; $i<12; $i++) $shp_tmp[$mm[$i]]=0;
   	  		  
   	  	for ($i=0; $i<sizeof($ord); $i++)
			  {
			  	$etd=explode('-',$ord[$i]['etd']);
				if ($etd[0] == $PHP_year1)
				{
					$op['ord'][$etd[1]]=$op['ord'][$etd[1]]+$ord[$i]['su'];  //order
										
					$tmp_su=0;
					$tmp_out_su=explode(',',$ord[$i]['out_su']);
					for ($x=0; $x< sizeof($tmp_out_su); $x++) //�֭pout-put su
					{
						$tmp=substr($tmp_out_su[$x],6);						
						$tmp_su=$tmp_su+$tmp;
					}
					$tmp_un_su=$ord[$i]['su']-$tmp_su; //�p��Un-Finish SU
					if ($tmp_su > $ord[$i]['su'] || $ord[$i]['status'] > 8) //�Y�vFinish�β��X�q�j��q��Q'ty
					{																												//Un-Finish SU��0
						$tmp_un_su=0;
					}
					$op['un_out'][$etd[1]]=$op['un_out'][$etd[1]]+$tmp_un_su;
					
					
					
					$qty=$ord[$i]['qty'] - $ord[$i]['qty_shp'];
					$tmp_shp=(int)($ord[$i]['qty_shp']*$ord[$i]['ie1']);  //ship						
					$tmp_un_shp=$ord[$i]['su']-$tmp_shp;
					if ($qty <= 0) $tmp_un_shp = 0;
					
					$op['un_shp'][$etd[1]]=$op['un_shp'][$etd[1]]+$tmp_un_shp;
					
					if($ord[$i]['status'] >= 7)
					{
						$op['sch'][$etd[1]]=$op['sch'][$etd[1]]+$ord[$i]['su'];	 //schedule				
					}
					if($ord[$i]['status'] >= 8)
					{						
						$op['out'][$etd[1]]=$op['out'][$etd[1]]+$tmp_su; //out-put
						
						if ( $ord[$i]['qty_shp'] > 0) 
						{
							$tmp_shp=(int)($ord[$i]['qty_shp']*$ord[$i]['ie1']);  //ship
							$op['shp'][$etd[1]]=$op['shp'][$etd[1]]+$tmp_shp;
							if ($tmp_shp > $ord[$i]['su'])$shp_tmp[$etd[1]]=$shp_tmp[$etd[1]]+($tmp_shp-$ord[$i]['su']);
						}						
					}					
				}
			  }
    		  $un_sch[0]=$un_shp[0]=$un_out[0]=$shp[0]=$out[0]=$sch[0]=$ord_etd[0]=0;	  
			  $ary=array('ord','sch','out','shp','un_sch','un_out','un_shp');

			  for ($i=0; $i<12; $i++)
			  {		
			  
			  	$op['un_sch'][$mm[$i]]=$op['ord'][$mm[$i]]-$op['sch'][$mm[$i]];
			  	
			  	for ($k=0; $k<sizeof($ary); $k++) $op[$ary[$k]]['13']=$op[$ary[$k]]['13']+$op[$ary[$k]][$mm[$i]];			  	
			  	$j=$i+1;
			  	$ord_etd[$j]=$ord_etd[$i]+$op['ord'][$mm[$i]];
			  	$sch[$j]=$sch[$i]+$op['sch'][$mm[$i]];
			  	$out[$j]=$out[$i]+$op['out'][$mm[$i]];
			  	$shp[$j]=$shp[$i]+$op['shp'][$mm[$i]];
			  	$un_sch[$j]=$un_sch[$i]+$op['un_sch'][$mm[$i]];
			  	$un_out[$j]=$un_out[$i]+$op['un_out'][$mm[$i]];
			  	$un_shp[$j]=$un_shp[$i]+$op['un_shp'][$mm[$i]];
			  	
			  }
			  
			  if(!$c1 = $capaci->get($PHP_fty, $PHP_year1,'capacity')){  //capacity
					$op['msg']= $capaci->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
				}
			   $sumc = 0;
				for ($j=1;$j<13;$j++){
						$sumc = $sumc + $c1[$j+3];
					$op['c1'][$j] = $PG->y[$j-1] = $c1[$j+3];  // ���X�}�C��������
				}
				$op['c1']['sum'] = $sumc;
			}   // end if ($PHP_year1)
			
			$capa[0]=0;
			for ($j=1;$j<13;$j++){				
				$capa[$j]=$capa[($j-1)]+$op['c1'][$j];
			}
		//�ޤJ graph class
		include_once($config['root_dir']."/lib/src/jpgraph.php");
		include_once($config['root_dir']."/lib/src/jpgraph_line.php");

		include ($config['root_dir']."/lib/src/jpgraph_bar.php");

		$graphic_title = " FTY: ".$PHP_fty." Order Z Chart for month: ".$PHP_year1;
	    $mm=array('0','JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC');
	    $mom=array(0,0,0,0,0,0,0,0,0,0,0,0,0);

		$graph = new Graph(700, 300, "auto");
		$graph->SetScale("textlin");
		$graph->ygrid->SetFill(true,'#EFEFEF@0.5','#BBCCFF@0.5');
		$graph->img->SetMargin(60,30,40,40);    
		$graph->subtitle->Set('( Monthly Accumulated Chart )');
		$graph->SetShadow();
	
		// Setup X-scale
		$graph->xaxis->SetTickLabels($mm);
		$graph->xaxis->SetFont(FF_ARIAL,FS_NORMAL,8);

		if($PHP_month){  //���Ѫ��Х�
			// Add a vertical line at the end scale position '7'
			$m=$PHP_month;
			$m = $m+1-1;
			$mom[$m]=$capa['12'];
			$bplot = new BarPlot($mom);
			$bplot->SetFillColor("gainsboro");
			$graph->Add($bplot);
						
		}

		// setup capacity plot
		$capaplot = new LinePlot($capa);
		$capaplot->SetWeight(1.5);
		$capaplot->SetColor('navy');
		$capaplot->SetLegend('capacity '.number_format($capa['12']).' SU');
		$graph->Add($capaplot);



		// setup etp plot
		$etpplot = new LinePlot($ord_etd);
		$etpplot->SetWeight(1.5);
		$etpplot->SetColor('teal');
		$etpplot->SetLegend('Order '.number_format($ord_etd['12']).' SU');
		$graph->Add($etpplot);

		// setup schedule plot
		$schdplot = new LinePlot($sch);
		$schdplot->SetWeight(1.5);
		$schdplot->SetColor('darkred');
		$schdplot->SetLegend('schedule '.number_format($sch['12']).' SU');
		$graph->Add($schdplot);

		// setup out-put plot
		$outplot = new LinePlot($out);
		$outplot->SetWeight(1.5);
		$outplot->SetColor('red');
		$outplot->SetLegend('out-put '.number_format($out['12']).' SU');
		$graph->Add($outplot);
		
		// setup ship plot
		$shpplot = new LinePlot($shp);
		$shpplot->SetWeight(1.5);
		$shpplot->SetColor('black');
		$shpplot->SetLegend('SHIP '.number_format($shp['12']).' SU');
		$graph->Add($shpplot);

		$graph->legend->SetShadow('gray@0.4',5);
		$graph->legend->Pos(0.25,0.3,"center","center");
		$graph->title->Set($graphic_title);
		$graph->title->SetFont(FF_FONT1,FS_BOLD,8);

		$op['echart_1'] = $graph->Stroke('picture/e_chart1.png');
	
					$op['msg']= $capaci->msg->get(2);

		$layout->assign($op);
		$layout->display($TPL_PDT_VIEW);		    	    

		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//			 job 81-2-1    SALES ETP �����
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "etp_ord_mm":

		check_authority(8,2,"view");
		$string = $PHP_year."-".$PHP_month;
		$op['dist'] = array();
		
		if (!$result = $capaci->search_etp_ord($PHP_fty,$string,500)) {   
			$op['msg']= $capaci->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$op['ttl_su'] = 0;
		$op['ttl_qty'] = 0;
		$op['sales_fob'] = 0;
		
	if($result != "none"){
		$op['ord']=$result;
		for ($i = 0; $i < count($result); $i++) {   // $i �� �q�檺�`�ƶq			
			$op['ord'][$i]['ord_num'] = $result[$i]['order_num'];			
			$op['ord'][$i]['status'] =get_ord_status($result[$i]['status']);
			$op['ttl_su'] = $op['ttl_su'] + $result[$i]['su'];
			$op['ttl_qty'] = $op['ttl_qty'] + $result[$i]['qty'];
		}
	} else{ //if($result != "none")
		$op['record_NONE'] = "1";
	}
		$op['fty'] = $PHP_fty;
		$op['year'] = $PHP_year;
		$op['month'] = $PHP_month;		
	    	    
	page_display($op, 8, 2, $TPL_ETP_ORD);
	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//			 job 82-2-1    SALES ETP �����
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "etp_shp_mm":

	check_authority(8,2,"view");
		$string = $PHP_year."-".$PHP_month;
		$op['dist'] = array();
		
		if (!$result = $capaci->search_etp_sch($PHP_fty,$string,500)) {   
			$op['msg']= $capaci->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$op['ttl_su'] = 0;
		$op['ttl_qty'] = 0;
		$op['sales_fob'] = 0;

	if($result != "none"){
		$op['ord']=$result;
		for ($i = 0; $i < count($result); $i++) {   // $i �� �q�檺�`�ƶq	
			$op['ord'][$i]['ord_num'] = $result[$i]['order_num'];
					
			$op['ord'][$i]['status'] =get_ord_status($result[$i]['status']);
			$op['ttl_su'] = $op['ttl_su'] + $result[$i]['su'];
			$op['ttl_qty'] = $op['ttl_qty'] + $result[$i]['qty'];
		}
	} else{ //if($result != "none")
		$op['record_NONE'] = "1";
	}
		$op['fty'] = $PHP_fty;
		$op['year'] = $PHP_year;
		$op['month'] = $PHP_month;		

	page_display($op, 8, 2, $TPL_ETP_SCH);       
	
	break;
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//			 job 82-2-1    SALES ETP �����
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "etp_unshp_mm":
	check_authority(8,2,"view");
		$string = $PHP_year."-".$PHP_month;
		$op['dist'] = array();
		
		if (!$result = $capaci->search_etp_unsch($PHP_fty,$string,500)) {   
			$op['msg']= $capaci->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$op['ttl_su'] = 0;
		$op['ttl_qty'] = 0;
		$op['sales_fob'] = 0;

	if($result != "none"){
		$op['ord']=$result;
		for ($i = 0; $i < count($result); $i++) {   // $i �� �q�檺�`�ƶq
			$op['ord'][$i]['ord_num'] = $result[$i]['order_num'];				
			$op['ord'][$i]['status'] =get_ord_status($result[$i]['status']);
			$op['ttl_su'] = $op['ttl_su'] + $result[$i]['su'];
			$op['ttl_qty'] = $op['ttl_qty'] + $result[$i]['qty'];
		}
	} else{ //if($result != "none")
		$op['record_NONE'] = "1";
	}
		$op['fty'] = $PHP_fty;
		$op['year'] = $PHP_year;
		$op['month'] = $PHP_month;		

		$op['un_sch'] = 1;
		    	    
	page_display($op, 8, 2, $TPL_ETP_ORD); 
	break;




#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//		 job 81-4-1    SHIPPed 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "etp_out_mm":

	check_authority(8,2,"view");
		$string = $PHP_year.'-'.$PHP_month;
		$op['shp'] = array();

		if (!$result = $capaci->sum_etp_out($PHP_fty,$PHP_year,$PHP_month)) {   
			$op['msg']= $shipping->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$op['ttl_out_su'] = $op['ttl_out_qty'] = $op['sales_fob'] = $op['ttl_ord_qty'] = 0;
		$j=0;
		
	if($result != "none"){

		for ($i = 0; $i < count($result); $i++) {   // $i ���X�f�O�� ����
			$op['out'][$i] = $result[$i];
			$su=0;
			$tmp_su=explode(',',$result[$i]['out_su']);
			for ($x=0; $x< sizeof($tmp_su); $x++)
			{
				$tmp=substr($tmp_su[$x],6);						
				$su=$su+$tmp;
			}
			$op['out'][$i]['su']=$su;
			$op['out'][$i]['ord_num']=$result[$i]['order_num'];
			$op['out'][$i]['s_qty']=$result[$i]['qty_done'];
				
			$op['ttl_out_qty'] = $op['ttl_out_qty'] + $result[$i]['qty_done'];
			$op['ttl_out_su'] = $op['ttl_out_su'] + $su;
			$op['ttl_ord_qty'] = $op['ttl_ord_qty'] + $result[$i]['qty'];	
			$op['out'][$i]['status'] =get_ord_status($result[$i]['status']);	
		}
		
	} else{ //if($result != "none")
		$op['record_NONE'] = "1";
	}
		$op['fty'] = $PHP_fty;
		$op['year'] = $PHP_year;
		$op['mon'] = $PHP_month;
	    	    
	page_display($op, 8, 2, $TPL_ETP_MON_OUT);
	break;	


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//		 job 81-4-1    SHIPPed 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "etp_unout_mm":

	check_authority(8,2,"view");
		$string = $PHP_year.'-'.$PHP_month;
		$op['shp'] = array();

		if (!$result = $capaci->search_etp_ord($PHP_fty,$string,500)) {   
			$op['msg']= $shipping->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$op['ttl_out_su'] = $op['ttl_out_qty'] = $op['sales_fob'] = $op['ttl_ord_qty'] = 0;
		$op['ttl_ord_su'] = $op['ttl_rem_su']=0;
		$op['ci_out_su'] = $op['ci_out_qty'] =  $op['ci_ord_qty'] = $op['ci_ord_su'] = $op['ci_rem_su']=0;
		$op['cc_out_su'] = $op['cc_out_qty'] =  $op['cc_ord_qty'] = $op['cc_ord_su'] = $op['cc_rem_su']=0;

		$op['ui_out_su'] = $op['ui_out_qty'] =  $op['ui_ord_qty'] = $op['ui_ord_su'] = $op['ui_rem_su']=0;
		$op['uc_out_su'] = $op['uc_out_qty'] =  $op['uc_ord_qty'] = $op['uc_ord_su'] = $op['uc_rem_su']=0;

		$j=0;$k=0;$x=0;$y=0;
	if($result != "none"){

		for ($i = 0; $i < count($result); $i++) {   // $i ���X�f�O�� ����
			
	    	$tmp_su=0;
			$tmp_out_su=explode(',',$result[$i]['out_su']);
			for ($g=0; $g< sizeof($tmp_out_su); $g++)
			{
				$tmp=substr($tmp_out_su[$g],6);						
				$tmp_su=$tmp_su+$tmp;
			}
	    	$su=$result[$i]['su']-$tmp_su;
			$qty=$result[$i]['qty']-$result[$i]['qty_done'];
			
			if($su > 0 && $result[$i]['status'] < 10)
			{
				if((!$result[$i]['mat_shp'] || $result[$i]['mat_shp'] == '0000-00-00' ||!$result[$i]['m_acc_shp'] || $result[$i]['m_acc_shp'] == '0000-00-00' ||$result[$i]['smpl_apv'] == '0000-00-00') && $result[$i]['status'] < 8)
				{
					if($result[$i]['sub_con'] == 0)
					{
						$op['uout_i'][$k] = $result[$i];				
						$op['uout_i'][$k]['rem_su']=$su;
						$op['uout_i'][$k]['out_su']=$tmp_su;
						$op['uout_i'][$k]['ord_num']=$result[$i]['order_num'];
						$op['uout_i'][$k]['s_qty']=$qty;
				
						$op['ui_out_qty'] = $op['ui_out_qty'] + $qty;
						$op['ui_out_su'] = $op['ui_out_su'] + $tmp_su;
						$op['ui_rem_su'] = $op['ui_rem_su'] + $su;
						$op['ui_ord_su'] = $op['ui_ord_su'] + $result[$i]['su'];
						$op['ui_ord_qty'] = $op['ui_ord_qty'] + $result[$i]['qty'];
						$op['uout_i'][$k]['status'] =get_ord_status($result[$i]['status']);						
						$k++;
					}else{
						$op['uout_c'][$x] = $result[$i];				
						$op['uout_c'][$x]['rem_su']=$su;
						$op['uout_c'][$x]['out_su']=$tmp_su;
						$op['uout_c'][$x]['ord_num']=$result[$i]['order_num'];
						$op['uout_c'][$x]['s_qty']=$qty;
				
						$op['uc_out_qty'] = $op['uc_out_qty'] + $qty;
						$op['uc_out_su'] = $op['uc_out_su'] + $tmp_su;
						$op['uc_rem_su'] = $op['uc_rem_su'] + $su;
						$op['uc_ord_su'] = $op['uc_ord_su'] + $result[$i]['su'];
						$op['uc_ord_qty'] = $op['uc_ord_qty'] + $result[$i]['qty'];
						$op['uout_c'][$x]['status'] =get_ord_status($result[$i]['status']);						
						$x++;
						
					}
				}else{
					if($result[$i]['sub_con'] == 0)
					{
						$op['out_i'][$j] = $result[$i];				
						$op['out_i'][$j]['rem_su']=$su;
						$op['out_i'][$j]['out_su']=$tmp_su;
						$op['out_i'][$j]['ord_num']=$result[$i]['order_num'];
						$op['out_i'][$j]['s_qty']=$qty;
				
						$op['ci_out_qty'] = $op['ci_out_qty'] + $qty;
						$op['ci_out_su'] = $op['ci_out_su'] + $tmp_su;
						$op['ci_rem_su'] = $op['ci_rem_su'] + $su;
						$op['ci_ord_su'] = $op['ci_ord_su'] + $result[$i]['su'];
						$op['ci_ord_qty'] = $op['ci_ord_qty'] + $result[$i]['qty'];
						$op['out_i'][$j]['status'] =get_ord_status($result[$i]['status']);						
						$j++;
					}else{
						$op['out_c'][$y] = $result[$i];				
						$op['out_c'][$y]['rem_su']=$su;
						$op['out_c'][$y]['out_su']=$tmp_su;
						$op['out_c'][$y]['ord_num']=$result[$i]['order_num'];
						$op['out_c'][$y]['s_qty']=$qty;
				
						$op['cc_out_qty'] = $op['cc_out_qty'] + $qty;
						$op['cc_out_su'] = $op['cc_out_su'] + $tmp_su;
						$op['cc_rem_su'] = $op['cc_rem_su'] + $su;
						$op['cc_ord_su'] = $op['cc_ord_su'] + $result[$i]['su'];
						$op['cc_ord_qty'] = $op['cc_ord_qty'] + $result[$i]['qty'];
						$op['out_c'][$y]['status'] =get_ord_status($result[$i]['status']);						
						$y++;
					
					}
				}				
				
			}
			
		
		}
		$op['ttl_out_su'] = $op['ci_out_su']+$op['cc_out_su']+$op['ui_out_su']+$op['uc_out_su'];
		$op['ttl_out_qty'] = $op['ci_out_qty']+$op['cc_out_qty']+$op['ui_out_qty']+$op['uc_out_qty'];
		$op['ttl_ord_qty'] = $op['ci_ord_qty']+$op['cc_ord_qty']+$op['ui_ord_qty']+$op['uc_ord_qty'];
		$op['ttl_ord_su'] = $op['ci_ord_su']+$op['cc_ord_su']+$op['ui_ord_su']+$op['uc_ord_su'];
		$op['ttl_rem_su']=$op['ci_rem_su']+$op['cc_rem_su']+$op['ui_rem_su']+$op['uc_rem_su'];

		$op['s_out_su'] = $op['ci_out_su']+$op['cc_out_su'];
		$op['s_out_qty'] = $op['ci_out_qty']+$op['cc_out_qty'];
		$op['s_ord_qty'] = $op['ci_ord_qty']+$op['cc_ord_qty'];
		$op['s_ord_su'] = $op['ci_ord_su']+$op['cc_ord_su'];
		$op['s_rem_su']=$op['ci_rem_su']+$op['cc_rem_su'];

		$op['us_out_su'] = $op['ui_out_su']+$op['uc_out_su'];
		$op['us_out_qty'] = $op['ui_out_qty']+$op['uc_out_qty'];
		$op['us_ord_qty'] = $op['ui_ord_qty']+$op['uc_ord_qty'];
		$op['us_ord_su'] = $op['ui_ord_su']+$op['uc_ord_su'];
		$op['us_rem_su']=$op['ui_rem_su']+$op['uc_rem_su'];

	} else{ //if($result != "none")
		$op['record_NONE'] = "1";
	}
		if ($j == 0 && $k == 0 && $x==0 && $y==0)$op['record_NONE'] = "1";
		$op['fty'] = $PHP_fty;
		$op['year'] = $PHP_year;
		$op['mon'] = $PHP_month;
		$op['un_shp']=1;
	    	    
	page_display($op, 8, 2, $TPL_ETP_MON_UNOUT);
	break;		

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//		 job 81-4-1    SHIPPed 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "etp_shp_out":

	check_authority(8,2,"view");
		$string = $PHP_year.'-'.$PHP_month;
		$op['shp'] = array();

		if (!$result = $capaci->sum_etp_out($PHP_fty,$PHP_year,$PHP_month)) {   
			$op['msg']= $shipping->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$op['ttl_shp_su'] = $op['ttl_shp_qty'] = $op['sales_fob'] = $op['ttl_odr_qty'] = 0;
		$j=0;

	if($result != "none"){

		for ($i = 0; $i < count($result); $i++) {   // $i ���X�f�O�� ����
			if($result[$i]['qty_shp'] > 0)
			{
				$op['shp'][$j] = $result[$i];
				$su = (int)($result[$i]['qty_shp'] * $result[$i]['ie1']);
				$op['shp'][$j]['su']=$su;
				$op['shp'][$j]['ord_num']=$result[$i]['order_num'];
				$op['shp'][$j]['k_date']=$result[$i]['shp_date'];
				$op['shp'][$j]['s_qty']=$result[$i]['qty_shp'];
				
				$op['ttl_shp_qty'] = $op['ttl_shp_qty'] + $result[$i]['qty_shp'];
				$op['ttl_shp_su'] = $op['ttl_shp_su'] + $su;
				$op['ttl_odr_qty'] = $op['ttl_odr_qty'] + $result[$i]['qty'];
				$op['sales_fob'] = $op['sales_fob'] + $result[$i]['qty_shp']* $result[$i]['uprice'];
				$op['shp'][$j]['amt'] = $result[$i]['qty_shp']* $result[$i]['uprice'];
				$j++;
			}
			
		
		}
		
		$op['pc_avg'] = number_format($op['sales_fob']/$op['ttl_shp_qty'],2);

	} else{ //if($result != "none")
		$op['record_NONE'] = "1";
	}
		$op['fty'] = $PHP_fty;
		$op['year'] = $PHP_year;
		$op['mon'] = $PHP_month;
		    	    
	page_display($op, 8, 2, $TPL_ETP_MON_SHP);
	break;	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//		 job 81-4-1    SHIPPed 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "etp_un_shp_out":

	check_authority(8,2,"view");
		$string = $PHP_year.'-'.$PHP_month;
		$op['shp'] = array();

		if (!$result = $capaci->search_etp_ord($PHP_fty,$string,500)) {   
			$op['msg']= $shipping->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$op['ttl_shp_su'] = $op['ttl_shp_qty'] = $op['sales_fob'] = $op['ttl_ord_qty'] = 0;
		$op['ttl_rem_su'] = $op['ttl_ord_su'] =$op['ttl_fsh_su']=0;
		$j=0;

	if($result != "none"){

		for ($i = 0; $i < count($result); $i++) {   // $i ���X�f�O�� ����
			
			$tmp_shp = (int)($result[$i]['qty_shp'] * $result[$i]['ie1']);
			$qty = $result[$i]['qty']-$result[$i]['qty_shp'];
			$su=$result[$i]['su']-$tmp_shp;
			
			if($qty > 0)
			{				
				$op['shp'][$j] = $result[$i];				
				$op['shp'][$j]['rem_su']=$su;
				$op['shp'][$j]['ord_num']=$result[$i]['order_num'];
				$op['shp'][$j]['s_qty']=$qty;
				$op['shp'][$j]['fsh_su']=(int)($result[$i]['qty_done'] * $result[$i]['ie1']);
				$op['shp'][$j]['shp_su']=$tmp_shp;
				
				$op['ttl_shp_qty'] = $op['ttl_shp_qty'] + $qty;
				$op['ttl_shp_su'] = $op['ttl_shp_su'] + $tmp_shp;
				$op['ttl_rem_su'] = $op['ttl_rem_su'] + $su;
				$op['ttl_fsh_su'] = $op['ttl_fsh_su'] + $op['shp'][$j]['fsh_su'];
				$op['ttl_ord_su'] = $op['ttl_ord_su'] + $op['shp'][$j]['su'];
				
				$op['ttl_ord_qty'] = $op['ttl_ord_qty'] + $result[$i]['qty'];
				$op['sales_fob'] = $op['sales_fob'] + $qty* $result[$i]['uprice'];
				$op['shp'][$j]['amt'] = $qty* $result[$i]['uprice'];
				
				$op['shp'][$j]['status'] =get_ord_status($result[$i]['status']);				
				$j++;
			}
			
		
		}
		
		$op['pc_avg'] = number_format($op['sales_fob']/$op['ttl_shp_qty'],2);

	} else{ //if($result != "none")
		$op['record_NONE'] = "1";
	}
		if ($j == 0)$op['record_NONE'] = "1";
		$op['fty'] = $PHP_fty;
		$op['year'] = $PHP_year;
		$op['mon'] = $PHP_month;
		$op['un_shp']=1;		    	    
	page_display($op, 8, 2, $TPL_ETP_MON_UNSHP);
	break;		
	
	
	
	
	
//-------------------------------------------------------------------------------------
//			 job 81  �Ͳ�����
//	
//-------------------------------------------------------------------------------------
    case "capacity_search":
		check_authority(8,1,"view");
				// creat cust combo box
		$op['factory'] = $arry2->select($FACTORY,'','PHP_fty','select','');  	
		$op['year_1'] = $arry2->select($YEAR_WORK,'','PHP_year1','select','');  	
		$op['year_2'] = $arry2->select($YEAR_WORK,'','PHP_year2','select','');  	
		$op['year_3'] = $arry2->select($YEAR_WORK,'','PHP_year3','select','');  

		$op['msg'] = $order->msg->get(2);
	
			page_display($op, 8, 1, $TPL_CAPACITY_SEARCH);    	    
		break;

//-------------------------------------------------------------------------------------
    case "do_capacity_search":
		check_authority(8,1,"view");

		$op['msg'] ='';

		$op['fty'] = $PHP_fty;
		$op['year1'] = $PHP_year1;
		$op['year2'] = $PHP_year2;
		$op['year3'] = $PHP_year3;
		
		// �P�_���J �~���ۦP�� ------------
		if(($op['year1']) == ($op['year2'])){ $op['year2'] =''; }
		if(($op['year1']) == ($op['year3'])){ $op['year3'] =''; }
		if(($op['year2']) == ($op['year3'])){ $op['year3'] =''; }
		
		if (!$op['fty']){
			$op['msg'][] = "Error ! you have to select target Factory !";
		}

		if(!$op['year1']){ 
			$op['year1'] = $GLOBALS['THIS_YEAR'];
			$PHP_year1 = $GLOBALS['THIS_YEAR'];
		
		}  // ��S��J�~���ɹw�]�����~

		if ($op['msg']){	
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		//�]�wgraphic (chart)
		require_once ($config['root_dir']."/lib/class.graphic.php");
		$PG = new PowerGraphic;
			$PG->axis_x    = 'Month';
			$PG->axis_y    = 'S.U.';
			$PG->graphic_1 = 'capacity';
			$PG->graphic_2 = 'order';
			$PG->graphic_3 = 'schedule';
			$PG->graphic_4 = 'output';
			$PG->skin      = 1;
			$PG->type      = 1;
			$PG->credits   = 0;
			$mm=array('JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC');
			for ($i=0; $i< 12; $i++)	$PG->x[$i] = $mm[$i];

		// ��Jcheck ����  �}�lsearch
		$ary1=array('c1','p1','s1','a1','h1','f1');
		$ary2=array('capacity','pre_schedule','schedule','actual','shipping','shp_fob');
//------------------------------------------  1 st year ----------------------
			if($PHP_year1){
			   if(!$op['u1'] = $capaci->get_unord($PHP_fty, $PHP_year1)){
			   		$op['msg']= $capaci->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
				}
				if(!$op['so1'] = $capaci->get_subcon($PHP_fty, $PHP_year1)){
			   		$op['msg']= $capaci->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
				}			
			   for ($i=0; $i<sizeof($ary1); $i++)
			   {
			   		if(!$$ary1[$i] = $capaci->get($PHP_fty, $PHP_year1,$ary2[$i])){
						$op['msg']= $capaci->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
					}
			   }	
				// �]�w html �� format
				$sumc = $sump = $sums = $suma = $sumh = $sumf= 0;
				for ($j=1;$j<13;$j++){
						$sumc = $sumc + $c1[$j+3];
					$op['c1'][$j] = $PG->y[$j-1] = $c1[$j+3];  // ���X�}�C��������
						$sump = $sump + $p1[$j+3];
					$op['p1'][$j] = $PG->z[$j-1] = $p1[$j+3];  // ���X�}�C��������
						$sums = $sums + $s1[$j+3];
					$op['s1'][$j] = $PG->a[$j-1] = $s1[$j+3];  // ���X�}�C��������
						$suma = $suma + $a1[$j+3];
					$op['a1'][$j] = $PG->b[$j-1] = $a1[$j+3];  // ���X�}�C��������
						$sumh = $sumh + $h1[$j+3];
					$op['h1'][$j] = $h1[$j+3];  // ���X�}�C��������
						$sumf = $sumf + $f1[$j+3];
					$op['f1'][$j] = $f1[$j+3];  // ���X�}�C��������
				}
				$op['c1']['sum'] = $sumc;
				$op['p1']['sum'] = $sump;
				$op['s1']['sum'] = $sums;
				$op['a1']['sum'] = $suma;
				$op['h1']['sum'] = $sumh;
				$op['f1']['sum'] = $sumf;

		//�]�wbar chart���Ѽ�
		$PG->title     = $PHP_fty." -".$PHP_year1;
		$op['chart_1'] = "class.graphic.php?" . $PG->create_query_string();

			}   // end if ($PHP_year1)

//------------------------------------------ 2 nd year ----------------------
			if($PHP_year2){
			$ary1=array('c2','p2','s2','a2','h2','f2');
			   if(!$op['u2'] = $capaci->get_unord($PHP_fty, $PHP_year2)){
			   		$op['msg']= $capaci->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
				}
				if(!$op['so2'] = $capaci->get_subcon($PHP_fty, $PHP_year2)){
			   		$op['msg']= $capaci->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
				}
			   for ($i=0; $i<sizeof($ary1); $i++)
			   {
			   		if(!$$ary1[$i] = $capaci->get($PHP_fty, $PHP_year2,$ary2[$i])){
						$op['msg']= $capaci->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
					}
			   }				  
				// �]�w html �� format
				$sumc = $sump = $sums = $suma = $sumh = $sumf = 0;
				for ($j=1;$j<13;$j++){
					$sumc = $sumc + $c2[$j+3];
					$op['c2'][$j] = $PG->y[$j-1] = $c2[$j+3];  // ���X�}�C��������
					$sump = $sump + $p2[$j+3];
					$op['p2'][$j] = $PG->z[$j-1] = $p2[$j+3];  // ���X�}�C��������
					$sums = $sums + $s2[$j+3];
					$op['s2'][$j] = $PG->a[$j-1] = $s2[$j+3];  // ���X�}�C��������
					$suma = $suma + $a2[$j+3];
					$op['a2'][$j] = $PG->b[$j-1] = $a2[$j+3];  // ���X�}�C��������
					$sumh = $sumh + $h2[$j+3];
					$op['h2'][$j] = $h2[$j+3];  // ���X�}�C��������
					$sumf = $sumf + $f2[$j+3];
					$op['f2'][$j] = $f2[$j+3];  // ���X�}�C��������
				}
				$op['c2']['sum'] = $sumc;
				$op['p2']['sum'] = $sump;
				$op['s2']['sum'] = $sums;
				$op['a2']['sum'] = $suma;
				$op['h2']['sum'] = $sumh;
				$op['f2']['sum'] = $sumf;

		//�]�wbar chart���Ѽ�
		$PG->title     = $PHP_fty." -".$PHP_year2;
		$op['chart_2'] = "class.graphic.php?" . $PG->create_query_string();

			}   // end if ($PHP_year2)

//------------------------------------------  3 rd year ----------------------
			if($PHP_year3){
			   if(!$op['u3'] = $capaci->get_unord($PHP_fty, $PHP_year3)){
			   		$op['msg']= $capaci->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
				}
				if(!$op['so3'] = $capaci->get_subcon($PHP_fty, $PHP_year3)){
			   		$op['msg']= $capaci->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
				}
			   if(!$c3 = $capaci->get($PHP_fty, $PHP_year3,'capacity')){
					$op['msg']= $capaci->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
				}
			   if(!$p3 = $capaci->get($PHP_fty, $PHP_year3,'pre_schedule')){
					$op['msg']= $capaci->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
				}
			   if(!$s3 = $capaci->get($PHP_fty, $PHP_year3,'schedule')){
					$op['msg']= $capaci->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
				}
			   if(!$a3 = $capaci->get($PHP_fty, $PHP_year3,'actual')){
					$op['msg']= $capaci->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
				}
			   if(!$h3 = $capaci->get($PHP_fty, $PHP_year3,'shipping')){
					$op['msg']= $capaci->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
				}
			   if(!$f3 = $capaci->get($PHP_fty, $PHP_year3,'shp_fob')){  //2006/03/30 adding
					$op['msg']= $capaci->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
				}
				// �]�w html �� format
				$sumc = $sump = $sums = $suma = $sumh = 0;
				for ($j=1;$j<13;$j++){
					$sumc = $sumc + $c3[$j+3];
					$op['c3'][$j] = $PG->y[$j-1] = $c3[$j+3];  // ���X�}�C��������
					$sump = $sump + $p3[$j+3];
					$op['p3'][$j] = $PG->z[$j-1] = $p3[$j+3];  // ���X�}�C��������
					$sums = $sums + $s3[$j+3];
					$op['s3'][$j] = $PG->a[$j-1] = $s3[$j+3];  // ���X�}�C��������
					$suma = $suma + $a3[$j+3];
					$op['a3'][$j] = $PG->b[$j-1] = $a3[$j+3];  // ���X�}�C��������
					$sumh = $sumh + $h3[$j+3];
					$op['h3'][$j] = $h3[$j+3];  // ���X�}�C��������
					$sumf = $sumf + $f3[$j+3];
					$op['f3'][$j] = $f3[$j+3];  // ���X�}�C��������
				}
				$op['c3']['sum'] = $sumc;
				$op['p3']['sum'] = $sump;
				$op['s3']['sum'] = $sums;
				$op['a3']['sum'] = $suma;
				$op['h3']['sum'] = $sumh;
				$op['f3']['sum'] = $sumf;

		//�]�wbar chart���Ѽ�
		$PG->title     = $PHP_fty." -".$PHP_year3;
		$op['chart_3'] = "class.graphic.php?" . $PG->create_query_string();

			}   // end if ($PHP_year3)

//---------------------------------------------------
					$op['msg']= $capaci->msg->get(2);
			page_display($op, 8, 1, $TPL_CAPACITY_VIEW);
		break;



//-------------------------------------------------------------------------------------
    case "capacity_add":

	check_authority(8,1,"add");
		//  �ˬd�ݬ� ��Ʈw�O�_�w�g �s�b ??
		   if($c = $capaci->get($PHP_fty, $PHP_year1,'capacity')){
				$op['msg'][]= 'year :'.$PHP_year1.'\'s capacity of factory[ '.$PHP_fty.' ] is exist already !';
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

		$op['msg'] ='';

		$op['fty'] = $PHP_fty;
		$op['year1'] = $PHP_year1;
		$op['year2'] = $PHP_year2;
		$op['year3'] = $PHP_year3;
		
		if (!$op['fty']){
			$op['msg'][] = "Error ! you have to select the target Factory !";
		}
		if(!$op['year1']){
			$op['msg'][] = "Error! you have to select one target YEAR at least !";
		}

		if ($op['msg']){	
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		page_display($op, 8, 1, $TPL_ADD_CAPACITY); 
		break;

//-------------------------------------------------------------------------------------
    case "do_capacity_add":

		check_authority(8,1,"add");
			$parm = array(	"fty"			=>  $PHP_fty,
							"year1"			=>  $PHP_year1,
							"year2"			=>  $PHP_year2,
							"year3"			=>  $PHP_year3,
			);
		$A1 = array();
		$A2 = array();
		$A3 = array();
		
			for ($i=1;$i<13;$i++){  // �ഫ��J�Ȭ� �}�C
				$t = 100+$i;
				$s = "PHP_".$PHP_year1.substr($t,1);
				$A1[$i] = $$s;
			}

	  if($PHP_year2){	
			for ($i=1;$i<13;$i++){
				$t = 100+$i;
				$s2 = "PHP_".$PHP_year2.substr($t,1);
				$A2[$i] = $$s2;
			}
	  }
		
	  if($PHP_year3){	
			for ($i=1;$i<13;$i++){
				$t = 100+$i;
				$s3 = "PHP_".$PHP_year3.substr($t,1);
				$A3[$i] = $$s3;
			}
	  }
			$parm['A1'] = $A1;
		  if($PHP_year2){	$parm['A2'] = $A2;	  }else{ $parm['A2'] ='';}
		  if($PHP_year3){	$parm['A3'] = $A3;	  }else{ $parm['A3'] ='';}

		if (!$f1 = $capaci->check($parm)) {  // ��J��� �����T��
			$op['msg'] = $capaci->msg->get(2);

			// �Ǧ^ �� html �ϥΤ��Ѽ� ;�]�A ��J����Ƥ]�Ǧ^ html
			$op['fty'] = $parm['fty'];
			$op['year1'] = $parm['year1'];
			$op['c1'] = $parm['A1'];
		  if($PHP_year2){	
			$op['year2'] = $parm['year2'];
			$op['c2'] = $parm['A2'];
		  }
		  if($PHP_year3){	
			$op['year3'] = $parm['year3'];
			$op['c3'] = $parm['A3'];
		  }

			$op['msg'] = $capaci->msg->get(2);
			if ($op['msg']){ $op['msg_YES'] = 1; } else { $op['msg_YES'] = "" ;}

			$layout->assign($op);
			$layout->display($TPL_ADD_CAPACITY);  
		break;

		}   // ���� �d�M��J����� ���T��

		// �g�J��Ʈw
		if (!$f1 = $capaci->add($parm)) {  // �g�J��Ʈw
			$op['msg'] = $capaci->msg->get(2);

			// �Ǧ^ �� html �ϥΤ��Ѽ� ;�]�A ��J����Ƥ]�Ǧ^ html
			$op['fty'] = $parm['fty'];
				$op['year1'] = $parm['year1'];
				$op['c1'] = $parm['A1'];
			  if($PHP_year2){	
				$op['year2'] = $parm['year2'];
				$op['c2'] = $parm['A2'];
			  }
			  if($PHP_year3){	
				$op['year3'] = $parm['year3'];
				$op['c3'] = $parm['A3'];
			  }


			$op['msg'] = $capaci->msg->get(2);
			if ($op['msg']){ $op['msg_YES'] = 1; } else { $op['msg_YES'] = "" ;}

			$layout->assign($op);
			$layout->display($TPL_ADD_CAPACITY);  
		break;

		}   // ���� �[�J��Ʈw


			// �Ǧ^ �� html �ϥΤ��Ѽ� ;�]�A ��J����Ƥ]�Ǧ^ html
			$op['fty'] = $parm['fty'];
			$op['year1'] = $parm['year1'];
			$parm['A1'][13] = array_sum($parm['A1']);   //�[�J total ��
			$op['c1'] = $parm['A1'];			
		  if($PHP_year2){	
			$op['year2'] = $parm['year2'];
			$parm['A2'][13] = array_sum($parm['A2']);   //�[�J total ��
			$op['c2'] = $parm['A2'];
		  }
		  if($PHP_year3){	
			$op['year3'] = $parm['year3'];
			$parm['A3'][13] = array_sum($parm['A3']);   //�[�J total ��
			$op['c3'] = $parm['A3'];
		  }

		$redir_str = $cgiself."?PHP_action=do_capacity_search&PHP_fty=".$PHP_fty."&PHP_year1=".$PHP_year1."&PHP_year2=".$PHP_year2."&PHP_year3=".$PHP_year3;

			redirect_page($redir_str);

		break;



//-------------------------------------------------------------------------------------
    case "capacity_update":

		check_authority(8,1,"edit");
		$op['msg'] ='';

		$op['fty'] = $PHP_fty;
		$op['year1'] = $PHP_year1;
		$op['year2'] = $PHP_year2;
		$op['year3'] = $PHP_year3;
		
		if (!$op['fty']){
			$op['msg'][] = "Error ! you have to select target Factory !";
		}
		if(!$op['year1']){
			$op['msg'][] = "Error! you have to select one target YEAR !";
		}

		if ($op['msg']){	
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
	// �۸�Ʈw ���X�ӵ�capacity�O�� -------------
			$op['c1'] = $capaci->get($PHP_fty, $PHP_year1, $cat='capacity');
		if ($PHP_year2){ 
			$op['c2'] = $capaci->get($PHP_fty, $PHP_year2, $cat='capacity'); 
			if(!$op['c2']){ $op['year2'] =''; }		
		}
		if ($PHP_year3){ 
			$op['c3'] = $capaci->get($PHP_fty, $PHP_year3, $cat='capacity'); 
			if(!$op['c3']){ $op['year3'] =''; }
		}

			page_display($op, 8, 1, $TPL_CAPACITY_UPDATE); 
		break;

//-------------------------------------------------------------------------------------
    case "do_capacity_edit":

		check_authority(8,1,"edit");
		$parm = array(	"fty"		=>  $PHP_fty,
						"year1"		=>  $PHP_year1,
						"year2"		=>  $PHP_year2,
						"year3"		=>  $PHP_year3,
						"c_type"	=>  'capacity',
			);
						$parm['A1']['id'] = $PHP_y1_id;
						$parm['A1'][1] = $PHP_y1_01;
						$parm['A1'][2] = $PHP_y1_02;
						$parm['A1'][3] = $PHP_y1_03;
						$parm['A1'][4] = $PHP_y1_04;
						$parm['A1'][5] = $PHP_y1_05;
						$parm['A1'][6] = $PHP_y1_06;
						$parm['A1'][7] = $PHP_y1_07;
						$parm['A1'][8] = $PHP_y1_08;
						$parm['A1'][9] = $PHP_y1_09;
						$parm['A1'][10] = $PHP_y1_10;
						$parm['A1'][11] = $PHP_y1_11;
						$parm['A1'][12] = $PHP_y1_12;

			if($PHP_year2){
						$parm['A2']['id'] = $PHP_y2_id;
						$parm['year2'] = $PHP_year2;
						$parm['A2'][1] = $PHP_y2_01;
						$parm['A2'][2] = $PHP_y2_02;
						$parm['A2'][3] = $PHP_y2_03;
						$parm['A2'][4] = $PHP_y2_04;
						$parm['A2'][5] = $PHP_y2_05;
						$parm['A2'][6] = $PHP_y2_06;
						$parm['A2'][7] = $PHP_y2_07;
						$parm['A2'][8] = $PHP_y2_08;
						$parm['A2'][9] = $PHP_y2_09;
						$parm['A2'][10] = $PHP_y2_10;
						$parm['A2'][11] = $PHP_y2_11;
						$parm['A2'][12] = $PHP_y2_12;
			}
			if($PHP_year3){
						$parm['A3']['id'] = $PHP_y3_id;
						$parm['year3'] = $PHP_year3;
						$parm['A3'][1] = $PHP_y2_01;
						$parm['A3'][2] = $PHP_y2_02;
						$parm['A3'][3] = $PHP_y2_03;
						$parm['A3'][4] = $PHP_y2_04;
						$parm['A3'][5] = $PHP_y2_05;
						$parm['A3'][6] = $PHP_y2_06;
						$parm['A3'][7] = $PHP_y2_07;
						$parm['A3'][8] = $PHP_y2_08;
						$parm['A3'][9] = $PHP_y2_09;
						$parm['A3'][10] = $PHP_y2_10;
						$parm['A3'][11] = $PHP_y2_11;
						$parm['A3'][12] = $PHP_y2_12;
			}

		//------------ �ˬd��J��Ƥ����T��  ---------------------------------

		if (!$f1 = $capaci->check($parm)) {  // ��J��� �����T��
			$op['msg'] = $capaci->msg->get(2);

			// �Ǧ^ �� html �ϥΤ��Ѽ� ;�]�A ��J����Ƥ]�Ǧ^ html
			$op['fty'] = $parm['fty'];
			$op['year1'] = $parm['year1'];
				$op['c1']['id'] = $parm['A1']['id'];
				$op['c1']['m01'] = $parm['A1'][1];
				$op['c1']['m02'] = $parm['A1'][2];
				$op['c1']['m03'] = $parm['A1'][3];
				$op['c1']['m04'] = $parm['A1'][4];
				$op['c1']['m05'] = $parm['A1'][5];
				$op['c1']['m06'] = $parm['A1'][6];
				$op['c1']['m07'] = $parm['A1'][7];
				$op['c1']['m08'] = $parm['A1'][8];
				$op['c1']['m09'] = $parm['A1'][9];
				$op['c1']['m10'] = $parm['A1'][10];
				$op['c1']['m11'] = $parm['A1'][11];
				$op['c1']['m12'] = $parm['A1'][12];
		  if($PHP_year2){	
			$op['year2'] = $parm['year2'];
				$op['c2']['id'] = $parm['A2']['id'];
				$op['c2']['m01'] = $parm['A2'][1];
				$op['c2']['m02'] = $parm['A2'][2];
				$op['c2']['m03'] = $parm['A2'][3];
				$op['c2']['m04'] = $parm['A2'][4];
				$op['c2']['m05'] = $parm['A2'][5];
				$op['c2']['m06'] = $parm['A2'][6];
				$op['c2']['m07'] = $parm['A2'][7];
				$op['c2']['m08'] = $parm['A2'][8];
				$op['c2']['m09'] = $parm['A2'][9];
				$op['c2']['m10'] = $parm['A2'][10];
				$op['c2']['m11'] = $parm['A2'][11];
				$op['c2']['m12'] = $parm['A2'][12];
		  }
		  if($PHP_year3){	
			$op['year3'] = $parm['year3'];
				$op['c3']['id'] = $parm['A3']['id'];
				$op['c3']['m01'] = $parm['A3'][1];
				$op['c3']['m02'] = $parm['A3'][2];
				$op['c3']['m03'] = $parm['A3'][3];
				$op['c3']['m04'] = $parm['A3'][4];
				$op['c3']['m05'] = $parm['A3'][5];
				$op['c3']['m06'] = $parm['A3'][6];
				$op['c3']['m07'] = $parm['A3'][7];
				$op['c3']['m08'] = $parm['A3'][8];
				$op['c3']['m09'] = $parm['A3'][9];
				$op['c3']['m10'] = $parm['A3'][10];
				$op['c3']['m11'] = $parm['A3'][11];
				$op['c3']['m12'] = $parm['A3'][12];
		  }

			$op['msg'] = $capaci->msg->get(2);

			page_display($op, 8, 1, $TPL_CAPACITY_UPDATE);
		break;

		}   // ���� �d�M��J����� ���T��

	// ---  �g�J capacity ��Ʈw ---------------------		
		if(!$A1 = $capaci->update_capacity($parm)){
			$op['msg']= $order->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
			
				# �O���ϥΪ̰ʺA
			$message = "update [ ".$PHP_fty." ]'s capacity of year :[".$PHP_year1." ".$PHP_year2." ".$PHP_year3."]";

			$log->log_add(0,"81E",$message);

			$redir_str = $cgiself."?PHP_action=do_capacity_search&PHP_fty=".$PHP_fty."&PHP_year1=".$PHP_year1."&PHP_year2=".$PHP_year2."&PHP_year3=".$PHP_year3;

			redirect_page($redir_str);

		break;


//-------------------------------------------------------------------------------------
//			 job 38  �Ͳ��O��

#	NOTE: ���ݳ]�w�i�J�� user ���� [ ���P�u�t ===== ============== ]

//-------------------------------------------------------------------------------------
    case "production":
    	check_authority(4,4,"view");
//---------------------------------- �P�_�O�_�u�t�H���i�J ---------------

		$where_str = $manager = $dept_id = $fty_select ='';
		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];   // �q�w�i�J����������

		for ($i=0; $i<count($GLOBALS['FACTORY']);$i++){
			if($user_dept == $GLOBALS['FACTORY'][$i]){
				$dept_id = $GLOBALS['FACTORY'][$i];   // �p�G�O�u�t�����H���i�J dept_id ���w�Ӥu�t---
			}
		}

		if (!$dept_id) {    // ���O�u�t�H���i�J��
			$manager = 1;
			$fty_select = $arry2->select($FACTORY,"","PHP_factory","select","");  //�u�t select���
		} else {
			$where_str = " WHERE factory = '".$dept_id."' ";
		}

		$op['manager_flag'] = $manager;
		$op['fty_id'] = $dept_id;
		$op['fty_select'] = $fty_select;

//---------------------------------- �P�_�O�_�u�t�H���i�J ---------------

		$op['msg'] = $order->msg->get(2);		
				// creat cust combo box

				$where_str ="";

		// ���X �����Ȥ�N��
		$where_str="order by cust_s_name"; //��cust_s_name�Ƨ�
		$cust_def = $cust->get_fields('cust_init_name',$where_str);
		$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//���X�Ȥ�N��
		for ($i=0; $i< sizeof($cust_def); $i++)
		{
			$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//�H [�Ȥ�N��-�Ȥ�²��] �e�{
		}
		$op['cust_select'] =  $arry2->select($cust_value,'','PHP_cust','select','',$cust_def_vue); 

		page_display($op, 4, 4, $TPL_PRODUCTION);	
				    	    
		break;



//=======================================================
    case "production_search":
		check_authority(4,4,"view");		
			// �q���ƦC��
			$parm = array(	
//						"dept"			=>  $PHP_dept_code,
						"order_num"		=>  $PHP_order_num,
						"cust"			=>	$PHP_cust,
						"ref"			=>	$PHP_ref,
						"factory"		=>	$PHP_factory,
				);

			//  �� ���X���� ���X�O���C��-------------+++++++++++++
			$argv = array(	"ord_num"	=>  $PHP_order_num,
							"k_date"	=>	$GLOBALS['TODAY'],
							"factory"	=>	$PHP_factory,
			);

				$op['cgi']= $parm;

			//  �� ���X���� ���X�O���C��-------------+++++++++++++
			if (!$pd = $daily->search($argv,1)) {   
				$op['msg']= $daily->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			
			if (!$op = $order->ord_search(2)) {   // �Ʋ��o�{�� ��mode =2 �Y�[�Jstatus>=7
				$op['msg']= $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

			// �ഫ�� html�� ���Ѽ�
			$op['pdt'] = $pd['daily'];
			$op['daily_records'] = $pd['max_no'];
			$op['today_qty'] = 0;  $op['today_su']= 0;
			$entry = count($op['pdt']);
			for($i=0;$i<$entry;$i++){  // �p�� ����Ͳ��`�ƶq
				$op['today_qty'] =	$op['today_qty'] + $op['pdt'][$i]['qty'];
				$op['today_su'] =	$op['today_su'] + $op['pdt'][$i]['su'];
			}

				$op['msg']= $order->msg->get(2);
				$op['msg']= $daily->msg->get(2);

				$op['cgi']= $parm;
		page_display($op, 4, 4, $TPL_PRODUCTION_LIST);
		break;



//-------------------------------------------------------------------------------------
    case "add_production":

		check_authority(4,4,"add");
		$today = $GLOBALS['TODAY'];

		$parm = array(	"pd_id"		=>  $PHP_pd_id,
						"ord_num"	=>  $PHP_ord_num,
						"back"		=>  $PHP_back,
						"qty"		=>  $PHP_qty,
						"k_date"	=>  $today,

				);

		// 2006/03/24 �令���i��J �s�� -------------
		if(!$PHP_qty){
			$op['msg'][] = "sorry! you have to key-in out-put Q'ty !";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
##--*****�s�W���Ƴ��� start
		$redir_str = $PHP_back."&PHP_sr_startno=".$PHP_sr_startno."&PHP_dept_code=".$cgi_1."&PHP_order_num=".$cgi_2."&PHP_cust=".$cgi_3."&PHP_ref=".$cgi_4."&PHP_factory=".$cgi_5;
##--*****�s�W���Ƴ��� end
//-----||  ��g pdtion->qty_don,qty_update ||     daily     ||  ��g capacity->actural(SU)  ||---------

	
	// �]�w�@�Ǩ䥦�� �ѼƤ� ���n�� �� ++++++++++++++++++++++++++++++++++++++++++++++++
	// �� s_order ���X su_ratio ��X su
		//   2005/08/31 ��Ѹ�Ʈw�� su [�H ie �p���J ]
		$su_ratio = $order->get_field_value('ie1', '', $PHP_ord_num);
		if ($su_ratio == 0)
		{
			$PHP_style = $order->get_field_value('style', '', $PHP_ord_num);
			if ($PHP_style=='PS' || $PHP_style=='BS' || $PHP_style=='BZ' || $PHP_style=='DR' || $PHP_style=='JK' || $PHP_style=='PS-J' || $PHP_style=='PS-P' || $PHP_style=='PS-S' || $PHP_style=='VS' || $PHP_style=='SS'){
				$cac_su=2*$PHP_qty;			
			}else{
				$cac_su=1*$PHP_qty;			
			}
		}else{
			$cac_su = $su_ratio * $PHP_qty;
		}
		$PHP_id = $order->get_field_value('id', '', $PHP_ord_num);
		$parm['factory'] = $order->get_field_value('factory', '', $PHP_ord_num);
		
		$parm['su'] = number_format($cac_su, 0, '', '');  //�|�ˤ��J ----- �浧

	// �����X ��� pdtion table������ƨӥ[�J[qty_done],[out_su]
		$qty_done = $order->get_field_value('qty_done', '', $PHP_ord_num,'pdtion');

	// ��Ĥ@�����X�� [�䤣����]--- �n�b pdtion �g�J start date �� ���� s_order��status >8
	// �H�ᦳ���X�ɴN�����A�g�J.....................
		if (!$qty_done) {  

			//-----------  �g�J s_order ....... status ->8
			if(!$A1 = $order->update_field('status','8',$PHP_id)){;   // ��s �q�檬�p�O��  status =>8
				$op['msg']= $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

			//-------------------------------------------------
			//------------  �g�J pdtion ......... start ����
			if(!$A1 = $order->update_pdtion_field('start',$GLOBALS['TODAY'],$PHP_pd_id)){ 
				$op['msg']= $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
		}

	// ���X��� pdtion ���� out_su �r��
		$out_su = $order->get_field_value('out_su', '', $PHP_ord_num,'pdtion');

	// �令�}�C ��=>su	
		$A_out_su = decode_mon_su($out_su);

	// ����[�J �s�����X su
		if(array_key_exists ($GLOBALS['THIS_MON'], $A_out_su )){ //���d�Ӥ���s���s�b
			$A_out_su[$GLOBALS['THIS_MON']] = $A_out_su[$GLOBALS['THIS_MON']] + $parm['su'];
		} else {
			$A_out_su[$GLOBALS['THIS_MON']] = $parm['su'];
		}
			
	// �N A_out_su �令 csv 
		$parm['out_su'] = encode_number($A_out_su);  // �ন csv 
		
		
		// �]�w���
	// *************************************************
	// *********  �g�J daily table *********************
			$f1 = $daily->check($parm);
		if (!$f1) {  // ��J��� �����T�� 
			$op['msg'] = $daily->msg->get(2);
			redirect_page($redir_str);
			break;
		}  // end if  ��J��� �����T 

		if(!$F = $daily->add($parm)){   // �s�W daily ��Ʈw
				$op['msg']= $daily->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
		} 


	// *************************************************
	// *********  ��g�J pdtion table *********************

		if(!$F1 = $order->pd_out_update($parm)){   // ��s pdtion ��Ʈw
				$op['msg'] = $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
		} 


	// *************************************************
	// *************************************************
	// *********  �g�J capacity table *********************
		$m_su = substr($GLOBALS['THIS_MON'],4);

		if(!$F1 = $capaci->update_su($parm['factory'],$GLOBALS['THIS_YEAR'],$m_su,'actual',$parm['su'])){  		$op['msg'][] = $capaci->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
		} 
			
				# �O���ϥΪ̰ʺA
		$message = "Add [ ".$PHP_ord_num." ] in Q'ty:[".$PHP_qty."] to FTY:[".$parm['factory']."]";

			$log->log_add(0,"44A",$message);

			redirect_page($redir_str);

		break;




//-------------------------------------------------------------------------------------
//--------- �ۦP��add_production �Ȧh�[�J ��g s_order->status �� pdtion->finish -----------
 
	case "finish_production":
//-------------------------------------------------------------------------------------
		check_authority(4,4,"add");

		$today = $GLOBALS['TODAY'];

		$parm = array(	"pd_id"		=>  $PHP_pd_id,
						"ord_num"	=>  $PHP_ord_num,
						"back"		=>  $PHP_back,
						"qty"		=>  $PHP_qty,
						"k_date"	=>  $today,

				);

		// �����X s_order �� id -------------------------------------------------
		$ord_id = $order->get_field_value('id', '',$PHP_ord_num);
		//------------------------------------------------------------ diff production_add ----
##--*****�s�W���� start
		$redir_str = $PHP_back."&PHP_sr_startno=".$PHP_sr_startno."&PHP_dept_code=".$cgi_1."&PHP_order_num=".$cgi_2."&PHP_cust=".$cgi_3."&PHP_ref=".$cgi_4."&PHP_factory=".$cgi_5;
##--*****�s�W���� end
	// 2006/03/24 �令 ���J�s�Ȯ�  ���g�J daily out ��table�Χ�g���n��table -------------
	$parm['factory'] = $order->get_field_value('factory', '', $PHP_ord_num);
	if($PHP_qty){  //����J finhish�ƶq��

	//------------- ��g pdtion->qty_don,qty_update ||     daily     || ��g capacity->actural(SU)  ";
	
	// �]�w�@�Ǩ䥦�� �ѼƤ� ���n�� �� ++++++++++++++++++++++++++++++++++++++++++++++++
		//   2005/08/31 ��Ѹ�Ʈw�� su [�H ie �p���J ]
		$su_ratio = $order->get_field_value('ie1', '', $PHP_ord_num);
		
		if ($su_ratio == 0)
		{
			$PHP_style = $order->get_field_value('style', '', $PHP_ord_num);
			if ($PHP_style=='PS' || $PHP_style=='BS' || $PHP_style=='BZ' || $PHP_style=='DR' || $PHP_style=='JK' || $PHP_style=='PS-J' || $PHP_style=='PS-P' || $PHP_style=='PS-S' || $PHP_style=='VS' || $PHP_style=='SS'){
				$cac_su=2*$PHP_qty;			
			}else{
				$cac_su=1*$PHP_qty;			
			}
		}else{
			$cac_su = $su_ratio * $PHP_qty;
		}
		
		$PHP_id = $order->get_field_value('id', '', $PHP_ord_num);
		$parm['factory'] = $order->get_field_value('factory', '', $PHP_ord_num);		
		$parm['su'] = number_format($cac_su, 0, '', '');  //�|�ˤ��J ----- �浧

	// �����X ���table������ƨӥ[�J[qty_done],[out_su]
		$qty_done = $order->get_field_value('qty_done', '', $PHP_ord_num,'pdtion');

	// ��Ĥ@�����X�� [�䤣����]--- �n�b pdtion �g�J start date 
	// �H�ᦳ���X�ɴN�����A�g�J.....................
		if (!$qty_done) {  
			//-------------------------------------------------
			//------------  �g�J pdtion ......... start ����
			if(!$A1 = $order->update_pdtion_field('start',$GLOBALS['TODAY'],$PHP_pd_id)){ 
				$op['msg']= $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
		}

	// ���X��� pdtion ���� out_su �r��
		$out_su = $order->get_field_value('out_su', '', $PHP_ord_num,'pdtion');

	// �令�}�C ��=>su	
		$A_out_su = decode_mon_su($out_su);

	// ����[�J �s�����X su
		if(array_key_exists ($GLOBALS['THIS_MON'], $A_out_su )){ //���d�Ӥ���s���s�b
			$A_out_su[$GLOBALS['THIS_MON']] = $A_out_su[$GLOBALS['THIS_MON']] + $parm['su'];
		} else {
			$A_out_su[$GLOBALS['THIS_MON']] = $parm['su'];
		}
			
	// �N A_out_su �令 csv 
		$parm['out_su'] = encode_number($A_out_su);  // �ন csv 
    //	$parm['qty_done'] = $qty_done + $PHP_qty;  // �s�� �������
		
		// �]�w���
	// *************************************************
	// *********  �g�J daily table *********************
			$f1 = $daily->check($parm);
		if (!$f1) {  // ��J��� �����T�� 
			$op['msg'] = $daily->msg->get(2);
			redirect_page($redir_str);
			break;
		}  // end if  ��J��� �����T 

		if(!$F = $daily->add($parm)){   // �s�W daily ��Ʈw
				$op['msg']= $daily->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
		} 


	// *************************************************
	// *********  ��g�J pdtion table *********************

		if(!$F1 = $order->pd_out_update($parm)){   // ��s pdtion ��Ʈw
				$op['msg'] = $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
		} 

	// *************************************************
	// *********  �g�J capacity table *********************
		$m_su = substr($GLOBALS['THIS_MON'],4);

		if(!$F1 = $capaci->update_su($parm['factory'],$GLOBALS['THIS_YEAR'],$m_su,'actual',$parm['su'])){  		$op['msg'][] = $capaci->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
		} 

	}  // if(!$PHP_qty) :::::::   ����� qty�� �~�|����W����......


	// *************** �P add_production ���P�B **********
	// *************************************************
	// *********  �g�J S_order->status,  pdtion->finish *********************
	//�Y�v��Ship���ƶq��,�q�檬�A��"==SHIPPED==";�Y�LShip�ƶq��,�q�檬�A��"=FINISHED="
		$shp_qty = $order->get_field_value('qty_shp',$PHP_pd_id,'',$tbl='pdtion');			
		if ($shp_qty > 0){$status = 12;}else{$status=10;}
		if(!$F1 = $order->update_field('status',$status, $ord_id)){
			$op['msg'][] = $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
		} 

		if(!$F1 = $order->update_pdtion_field('finish', $today, $PHP_pd_id)){
			$op['msg'][] = $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
		} 
		//------------------------------------------- diff production_add ----
				
				# �O���ϥΪ̰ʺA
		$message = "Add Q'ty:[".$PHP_qty."] and close:[ ".$PHP_ord_num." ] to FTY:[".$parm['factory']."]";
		$log->log_add(0,"44A",$message);



			redirect_page($redir_str);

		break;

//-------------------------------------------------------------------------------------
//	case "re_open_finish":		�� �Ͳ������� order �i�A reopen ,...
//								
//								 �[�J shipping ��
//-------------------------------------------------------------------------------------

	case "re_open_finish":

		check_authority(4,4,"add");
		$today = $GLOBALS['TODAY'];

		$parm = array(	"pd_id"		=>  $PHP_pd_id,
						"ord_num"	=>  $PHP_ord_num,
						"back"		=>  $PHP_back,
						"k_date"	=>  $today,
				);

		// �����X s_order �� id -------------------------------------------------
		$ord_id = $order->get_field_value('id', '',$PHP_ord_num);
		
		//---------------------------- diff production_add ----

		$redir_str = $PHP_back."&PHP_sr_startno=".$cgino."&PHP_dept_code=".$cgi_1."&PHP_order_num=".$cgi_2."&PHP_cust=".$cgi_3."&PHP_ref=".$cgi_4."&PHP_factory=".$cgi_5;

	// *************************************************
	// *********  ��g�J pdtion table *********************

		if(!$F1 = $order->update_pdtion_field("finish",'',$PHP_pd_id)){ // ��s pdtion ��Ʈw
				$op['msg'] = $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
		} 
	// *************************************************
	// *********  �g�J S_order->status, 
			
		if(!$F1 = $order->update_field('status', 8, $ord_id)){
			$op['msg'][] = $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
		} 

		//------------------------------------------- diff production_add ----
				
				# �O���ϥΪ̰ʺA
		$message = "RE-Open style :[ ".$PHP_ord_num." ] ";
		$log->log_add(0,"44A",$message);



			redirect_page($redir_str);

		break;














//-------------------------------------------------------------------------------------
 
	case "del_production":

//    �P add_production �u�O�N qty �令�t�� 
//              ���n�`�N��ӬO�_�w�Ofinish���q��......  �� del��O�_�٨S�}�l�Ͳ�
//-------------------------------------------------------------------------------------
		check_authority(4,4,"edit");
		$today = $GLOBALS['TODAY'];

		$parm = array(	
//						"pd_id"		=>  $PHP_pd_id,
						"ord_num"	=>  $PHP_ord_num,
						"back"		=>  $PHP_back,
						"factory"	=>  $PHP_fty,
						"qty"		=>  $PHP_qty * (-1),	// ���� �Nqty�令�t��
						"su"		=>  $PHP_su * (-1),	// ���� �Nqty�令�t��
						"k_date"	=>  $today,

				);
##--****�s�W���X start
		$redir_str = $PHP_back."&PHP_sr_startno=".$PHP_sr_startno."&PHP_dept_code=".$cgi_1."&PHP_order_num=".$cgi_2."&PHP_cust=".$cgi_3."&PHP_ref=".$cgi_4."&PHP_factory=".$cgi_5;
##--****�s�W���X end

	//2006/04/10 �令�@ �өR�O�������X�ƨ��X�ʧ@------��h�W�O�Ͳ����F�� �@�w��pdtion��
	if(!$tmp = $order->get_fields_4_del_pdt($PHP_ord_num)){
		$op['msg'][] = $order->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
	} 

		$parm['id'] = $tmp['id'];
		$parm['pd_id'] = $tmp['p_id'];
		$ord_status = $tmp['status'];
		$out_su = $tmp['out_su'];

	
	// �令�}�C ��=>su	
		$A_out_su = decode_mon_su($out_su);

	// ����  ���   �s�����X su
		if(array_key_exists ($GLOBALS['THIS_MON'], $A_out_su )){ //���d�Ӥ���s���s�b
			$A_out_su[$GLOBALS['THIS_MON']] = $A_out_su[$GLOBALS['THIS_MON']] + $parm['su'];
		} else {
			$A_out_su[$GLOBALS['THIS_MON']] = $parm['su'];
		}

	// �N �}�C A_out_su �����Ȭ��s���h�� **************
		$B_out_su = array_filter($A_out_su, 'filter_0_value');
	
	// �N B_out_su �令 csv 
		$parm['out_su'] = encode_number($B_out_su);  // �ন csv 
		
	// *************************************************
	// *********  �R�� daily table *********************
		if(!$F = $daily->del($PHP_daily_id)){   // �R�� daily ��Ʈw
				$op['msg']= $daily->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
		} 

	// *************************************************
	// *********  ��g�J pdtion table *********************

		if(!$F1 = $order->pd_out_update($parm)){   // ��s pdtion ��Ʈw
				$op['msg'] = $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
		} 

	// *************************************************
	// *********  �g�J capacity table *********************
		$m_su = substr($GLOBALS['THIS_MON'],4);

		if(!$F1 = $capaci->update_su($parm['factory'],$GLOBALS['THIS_YEAR'],$m_su,'actual',$parm['su'])){  		$op['msg'][] = $capaci->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
		} 

		//20060410�[�J �P�_�O�_��Ӫ� status��10[�������X�F] �h�令 status ��8 [�Ͳ���]
		if ($ord_status == '10'){
			//-----------  �g�J s_order ....... status ->8
			if(!$A1 = $order->update_field('status','8',$parm['id'])){;  // ��s �q�檬�p�O��  status =>8
				$op['msg']= $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
		}


//---------------�̫� ���X pdtion->qty_done �ݬݬO�_�� 0 ; �p�G��0 �h�|�������X ----
//--- �n�b pdtion �N start date �h�� �� ���� s_order��status- >7
	// �����X ��� pdtion table������ƨӥ[�J[qty_done],[out_su]

		if (!$qty_done = $order->get_field_value('qty_done', '', $PHP_ord_num,'pdtion')) {  

			//-------------------------------------------------
			//-----------  �g�J s_order ....... status ->7
			if(!$A1 = $order->update_field('status','7',$parm['id'])){;   // ��s �q�檬�p�O��  status =>8
				$op['msg']= $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

			//-------------------------------------------------
			//------------  �g�J pdtion ......... start ����
			if(!$A1 = $order->update_pdtion_field('start',NULL ,$parm['pd_id'])){ 
				$op['msg']= $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
		}
			
				# �O���ϥΪ̰ʺA
		$message = "delete production [ ".$PHP_ord_num." ] in Q'ty:[".$PHP_qty."] from FTY:[".$parm['factory']."]";

			$log->log_add(0,"44E",$message);


			redirect_page($redir_str);


	break;

//-------------------------------------------------------------------------------------
 
	case "del_shipping":

//    �P add_production �u�O�N qty �令�t�� ���n�`�N�[�J �O�_finish......
//-------------------------------------------------------------------------------------
		check_authority(4,5,"edit");

		$today = $GLOBALS['TODAY'];

		$parm = array(	
						"ord_num"	=>  $PHP_ord_num,
						"factory"	=>  $PHP_fty,
						"back"		=>  $PHP_back,
						"qty_shp"	=>  $PHP_qty * (-1),    // �����N qty �令 �t��
						"su_shp"	=>  $PHP_su * (-1),    // �����N qty �令 �t��
						"shp_date"	=>  $today,
				);



	// �����X s_order �� id , �� pdtion �� id ------------------------------------------
	if(!$parm['id'] = $order->get_field_value('id','',$PHP_ord_num)){
		$op['msg'][] = $order->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
	} 
	if(!$parm['pd_id'] = $order->get_field_value('id','',$PHP_ord_num,'pdtion')){
		$op['msg'][] = $order->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
	} 
//���Xorder��price	��SHIP FOB�ϥ�
	if(!$parm['uprice'] = $order->get_field_value('uprice','',$PHP_ord_num)){
		$op['msg'][] = $order->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
	} 


	// *************************************************
	// *********  delete table shipping  *****************
		if(!$F = $shipping->del($PHP_shp_id)){   // delete shipping ��Ʈw
				$op['msg']= $shipping->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
		} 


	// *********  �g�J pdtion->qty_shp, shp_date    *********************
		if(!$F1 = $order->add_shipping($parm)){
			$op['msg'][] = $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
		} 

	// *********  �g�J capaci-> shipping    ********************* 2006/03/30 adding
		if(!$F = $capaci->update_su($PHP_fty, $THIS_YEAR, substr($THIS_MON,4,2), 'shipping', $parm['qty_shp'])){  
			$op['msg'][] = $capaci->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
		}

		$fob=$parm['qty_shp']*$parm['uprice'];  //�[�JSHIP FOB�O��
		if(!$F = $capaci->update_su($PHP_fty, $THIS_YEAR, substr($THIS_MON,4,2), "shp_fob", $fob)){  
				$op['msg'][] = $capaci->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}
	
	// ���X PDTION->qty_shp �O�_��0 �~�M�w�O�_��g�g�J s_order-> status >10
	$shp_qty = $order->get_field_value('qty_shp','',$PHP_ord_num,'pdtion');
	// ���X PDTION->finish �O�_����� �~�M�wStatus�O�_��10	
	$finish = $order->get_field_value('finish',$parm['pd_id'],'',$tbl='pdtion');
	echo $finish;
	if($finish && $finish<>'0000-00-00' ){$status = 10;}else{$status = 8;}
	
	// *********  �g�J S_order->status   >10   *********************
		if(!$shp_qty){		// ----------�� qty_shp �٬O 0 :�٨S�X�L�f �X�f		
			if(!$F1 = $order->update_field('status', $status, $parm['id'])){
				$op['msg'][] = $order->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
			} 
		}

				# �O���ϥΪ̰ʺA
		$message = "Delete [".$PHP_ord_num."] shipping Q'ty:[".$PHP_qty."] from FTY:[".$parm['factory']."] ";
		$log->log_add(0,"44E",$message);
		
		$redir_str = "index2.php?PHP_action=shipping_search&PHP_sr_startno=".$PHP_sr_startno."&PHP_dept_code=".$PHP_dept."&PHP_order_num=".$PHP_order_num."&PHP_cust=".$PHP_cust."&PHP_ref=".$PHP_ref."&PHP_factory=".$PHP_factory;

			redirect_page($redir_str);


	break;

//-------------------------------------------------------------------------------------
//		case "shipping":	 job 39  �X�f�O��

#	NOTE: ���ݳ]�w�i�J�� user ���� [ ���P�u�t ===== ============== ]

//-------------------------------------------------------------------------------------
    case "shipping":
		check_authority(4,5,"view");
//---------------------------------- �P�_�O�_�u�t�H���i�J ---------------

		$where_str = $manager = $dept_id = $fty_select ='';
		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];   // �q�w�i�J����������

		for ($i=0; $i<count($GLOBALS['FACTORY']);$i++){
			if($user_dept == $GLOBALS['FACTORY'][$i]){
				$dept_id = $GLOBALS['FACTORY'][$i];   // �p�G�O�u�t�����H���i�J dept_id ���w�Ӥu�t---
			}
		}

		if (!$dept_id) {    // ���O�u�t�H���i�J��
			$manager = 1;
			$fty_select = $arry2->select($FACTORY,"","PHP_factory","select","");  //�u�t select���
		} else {
			$where_str = " WHERE factory = '".$dept_id."' ";
		}

		$op['manager_flag'] = $manager;
		$op['fty_id'] = $dept_id;
		$op['fty_select'] = $fty_select;

//---------------------------------- �P�_�O�_�u�t�H���i�J ---------------

		$op['msg'] = $order->msg->get(2);
		// ���X �����Ȥ�N��
		$where_str="order by cust_s_name"; //��cust_s_name�Ƨ�
		$cust_def = $cust->get_fields('cust_init_name',$where_str);
		$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//���X�Ȥ�N��
		for ($i=0; $i< sizeof($cust_def); $i++)
		{
			$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//�H [�Ȥ�N��-�Ȥ�²��] �e�{
		}
		$op['cust_select'] =  $arry2->select($cust_value,'','PHP_cust','select','',$cust_def_vue); 

		page_display($op, 4, 5, $TPL_SHIPPING);			    	    
		break;

//-------------------------------------------------------------------------------------
//		case "shipping_search":
//-------------------------------------------------------------------------------------

	case "shipping_search":

		check_authority(4,5,"view");
			// �q���ƦC��
			$parm = array(	
						"order_num"		=>  $PHP_order_num,
						"cust"			=>	$PHP_cust,
						"ref"			=>	$PHP_ref,
						"factory"		=>	$PHP_factory,
				);

			//  �� ���X���� ���X�O���C��-------------+++++++++++++
			$argv = array(	"ord_num"	=>  $PHP_order_num,
							"k_date"	=>	$GLOBALS['TODAY'],
							"factory"	=>	$PHP_factory,
			);

				$op['cgi']= $parm;

			//  �� ���X���� �X�f�O���C��-------------+++++++++++++
			if (!$shp = $shipping->search($argv,1)) {   
				$op['msg']= $shipping->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			
			if (!$op = $order->ord_search(3)) {   // �w�����X�� ��mode =3 �Y�[�Jstatus > 7
				$op['msg']= $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

			// �ഫ�� html�� ���Ѽ�
			$op['shp'] = $shp['shipping'];
			$op['shipping_records'] = $shp['max_no'];
			$op['shpping_qty'] = 0;  $op['shpping_su']= 0;


			$entry = count($op['shp']);
			for($i=0;$i<$entry;$i++){  // �p�� ����X�f�`�ƶq
				$op['shpping_qty'] =	$op['shpping_qty'] + $op['shp'][$i]['qty'];
				$op['shpping_su'] =		$op['shpping_su'] + $op['shp'][$i]['su'];
			}
			// op �Q�M���F �ݦA�P�_�@�� 

				$op['cgi']= $parm;

		page_display($op, 4, 5, $TPL_SHIPPING_LIST);	  
		break;


//-------------------------------------------------------------------------------------
//  ??????????????????????????????????????? �O�_�`�N �ǤJ [���X�Ϊ��Ѽ� ????????] no,...
	case "add_shipping":
//								�O�� s_order->status, pdtion->qty_shp, shp_date
//								 �[�J shipping ��
//-------------------------------------------------------------------------------------
		check_authority(4,5,"add");
		$today = $GLOBALS['TODAY'];

		$parm = array(	"pd_id"		=>  $PHP_pd_id,
						"ord_num"	=>  $PHP_ord_num,
						"qty_shp"	=>  $PHP_qty,
						"shp_date"	=>  $today,
						"remain"	=>	$PHP_remain
				);
		$redir_str = $PHP_back."&PHP_sr_startno=".$PHP_sr_startno."&PHP_dept_code=".$cgi_1."&PHP_order_num=".$cgi_2."&PHP_cust=".$cgi_3."&PHP_ref=".$cgi_4."&PHP_factory=".$cgi_5;

	// ���ˬd��J�������T ----------------------
		if (!$f1 = $shipping->check($parm)) {  // ��J��� �����T�� 
			$op['msg'] = $shipping->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}  // end if  ��J��� �����T 


	// �����X s_order �� id , factory, $su_ratio ------------------------------------------
	if(!$ord = $order->get(0, $PHP_ord_num)){
		$op['msg'][] = $order->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
	} 

	$parm['id'] = $ord['id'];
	$parm['factory'] = $ord['factory'];
		//   2005/08/31 ��Ѹ�Ʈw�� su [�H ie �p���J ]
	if ($ord['ie1'] == 0)
		{
			$PHP_style = $order->get_field_value('style', '', $PHP_ord_num);
			if ($PHP_style=='PS' || $PHP_style=='BS' || $PHP_style=='BZ' || $PHP_style=='DR' || $PHP_style=='JK' || $PHP_style=='PS-J' || $PHP_style=='PS-P' || $PHP_style=='PS-S' || $PHP_style=='VS' || $PHP_style=='SS'){
				$parm['su_ratio']=2;			
			}else{
				$parm['su_ratio']=1;			
			}
		}else{
			$parm['su_ratio'] = $ord['ie1'];
		}

	
	$cac_su = $parm['su_ratio'] * $parm['qty_shp'];
	$parm['su_shp'] = number_format($cac_su, 0, '', '');  //�|�ˤ��J ----- �浧
	
	// ���X PDTION->qty_shp �O�_��0 �~�M�w�O�_�g�J s_order-> status
	$shp_qty = $order->get_field_value('qty_shp',$PHP_pd_id,'',$tbl='pdtion');
	$finish = $order->get_field_value('finish',$PHP_pd_id,'',$tbl='pdtion');
	// *********  �g�J S_order->status   >12   *********************
		if($finish && $finish<>'0000-00-00' ){		// ��q��vFinish���A�~�|�ܦ�Shipped�_�h�����bproduction
			if(!$F1 = $order->update_field('status', 12, $parm['id'])){
				$op['msg'][] = $order->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
			} 
		}

	// *************************************************
	// *********  �g�J table shipping  *****************
		if(!$F = $shipping->add($parm)){   // �s�W shipping ��Ʈw
				$op['msg']= $shipping->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
		} 

	// *********  �g�J pdtion->qty_shp, shp_date    *********************
		if(!$F1 = $order->add_shipping($parm)){
			$op['msg'][] = $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
		} 

//2006/03/30 �s�W------------------------------
	// *********  �g�J capacity ��ƪ� 2006/03/30 �s�W�� shippiing    *********************
		//���P�_�O�_���ӵ� shipping �p�G�S�� �h�s�W  �p�G�� �N�N���Ƨ�s ~~~~

	// �g�J capaci. shipping ++++++++++++++++++++++++++++++++
	// ���ˬd capacity �ɤ��O�_�w�g���O�� ???  ���i�ೣ�O�h�l���P�_[ �i�H�[�b order_apv ]
		
		if (!$T=$capaci->get($cgi_5, $THIS_YEAR,'shipping')) {   //$cgi_5�� �u�t
				// ========= create capaci->shipping �ӵ� �O�� =======
			if(!$F1 = $capaci->append_new($THIS_YEAR,$cgi_5,'shipping')){
				$op['msg'][] = $capaci->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
			} 
		}

			$ky_mon = substr($THIS_MON,4,2);

				// ========= �g�J capaci. shipping ===   [�[�J��s]  :::  $cgi_5�� �u�t =====

			if(!$F = $capaci->update_su($cgi_5, $THIS_YEAR, substr($THIS_MON,4,2), 'shipping', $PHP_qty)){  
				$op['msg'][] = $capaci->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
			}

			$fob=$PHP_qty*$ord['uprice'];  //�[�JSHIP FOB�O��
			if(!$F = $capaci->update_su($cgi_5, $THIS_YEAR, substr($THIS_MON,4,2), "shp_fob", $fob)){  
				$op['msg'][] = $capaci->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
			}


				# �O���ϥΪ̰ʺA
		$message = "Add [ ".$PHP_ord_num." ] shipping record in Q'ty:[".$PHP_qty."] ";
		$log->log_add(0,"45A",$message);
		
##--*****�s�W���X start		
		$redir_str = "index2.php?PHP_action=shipping_search&PHP_sr_startno=".$PHP_sr_startno."&PHP_dept_code=".$PHP_dept."&PHP_order_num=".$PHP_order_nums."&PHP_cust=".$PHP_cust."&PHP_ref=".$PHP_ref."&PHP_factory=".$PHP_factory;
##--*****�s�W���X end

			redirect_page($redir_str);


	break;






















//-------------------------------------------------------------------------------------
//			 job 106  �q�� �i��

#	NOTE: ���ݳ]�w�i�J�� user ���� [ ���P�u�t ===== ============== ?????????????

//-------------------------------------------------------------------------------------
	case "ord_schedule":
	check_authority(10,6,"view");		
	$op['manager_flag'] = 1;  // 2005/09/13 �令�ֶi�J���O�@�� �ݿ�t�O

	$op['msg'] = $order->msg->get(2);
	// creat cust combo box

	$op['factory'] = $arry2->select($FACTORY,'','PHP_factory','select','');  	

	$where_str="order by cust_s_name"; //��cust_s_name�Ƨ�
	$cust_def = $cust->get_fields('cust_init_name',$where_str);
	$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//���X�Ȥ�N��

	for ($i=0; $i< sizeof($cust_def); $i++){
		$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//�H [�Ȥ�N��-�Ȥ�²��] �e�{
	}

	$op['cust_select'] =  $arry2->select($cust_value,'','PHP_cust','select','',$cust_def_vue); 
	page_display($op, 10, 6, $TPL_ORD_SCHEDULE);		    	    
	break;

//=======================================================
  case "ord_schedule_search":
	check_authority(10,6,"view");
	$PHP_etd ='';
	if (!empty($PHP_finish))$PHP_unstatus = ' < 10 ';
	if (!empty($PHP_shipping))$PHP_unstatus = ' < 12 ';
	if (!empty($PHP_finish)&&!empty($PHP_shipping))$PHP_unstatus = ' < 10 ';
	if (!empty($PHP_etd_start)&&!empty($PHP_etd_end))$PHP_ses_etd = "(etd BETWEEN '".$PHP_etd_start."' and '".$PHP_etd_end."')";$PHP_etd_start='';$PHP_etd_end='';
 
	if (!empty($PHP_status)){
		$parm = array(	"A1.dept"			=>  $PHP_dept_code,
						"A1.order_num"		=>  $PHP_order_num,
						"A1.cust"			=>	$PHP_cust,
						"A1.ref"			=>	$PHP_ref,
						"A1.factory"		=>	$PHP_factory,
						"etd_start"			=>	$PHP_etd_start,
						"etd_end"			=>	$PHP_etd_end,
						"etd"				=>	$PHP_ses_etd,
						"unstatus"			=>	$PHP_unstatus,
						"status"			=>	$PHP_status);
	}

	if(!isset($_SESSION['PAGE']['where_str']))$_SESSION['PAGE']['where_str']=$where_str='';
	$m_sql = "A1.order_num,A1.cust,A1.etd,A1.etp,A1.qty,A2.mat_etd,A2.m_acc_etd,A2.ets,A1.factory,A1.id,A1.unit,A1.status,A1.smpl_apv";
	if (!$op = $order->search_mode($parm,$_SESSION['PAGE']['where_str'],'s_order AS A1 INNER JOIN pdtion AS A2 INNER JOIN cust AS A3 ON A1.order_num = A2.order_num and A1.cust = A3.cust_s_name and A1.status >= 4',$m_sql,$PHP_action)) {
		$op['msg']= $order->msg->get(2);
		page_display($op, 10, 6, $TPL_ERROR);
		break;
	}

	$op['msg']= $order->msg->get(2);

	page_display($op, 10, 6, $TPL_ORD_SCHEDULE_LIST);			
	break;
//=======================================================
	case "ord_schedule_view":
	check_authority(10,6,"view");

		$op['cgi'] = array(	"cgiget"	=>  $cgiget,
							"cgino"		=>  $cgino,
							"cgi_1"		=>	$cgi_1,
							"cgi_2"		=>	$cgi_2,
							"cgi_3"		=>	$cgi_3,
							"cgi_4"		=>	$cgi_4,
							"cgi_5"		=>	$cgi_5,
				);
//echo "PHP_ID ====>".$PHP_id;
//exit;
		
		$op['order'] = $order->schedule_get(0,$PHP_order_num);  //���X�ӵ��O��
		if (!$op['order']) {
			$op['msg'] = $order->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		// ���X order_log ���q�� �����v50�����p�O��  -------------------
		$logs = $order_log->search50($op['order']['order_num']);  //���X�ӭq�����log�O��
		if (!$logs){
			$op['msg'] = $order->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$op['logs'] = $logs['order_log'];
		$op['log_records'] = $logs['records'];
			// �N�|�ǥX op['logs'] ��log����� �� $op['log_records']=1 �ӫq�_���L���
		//--------------------------------

		$parm = $op['order'];
if($parm['start'] =='0000-00-00'){ $parm['start'] =''; $op['order']['start']='';}
if($parm['finish'] =='0000-00-00'){ $parm['finish'] =''; $op['order']['finish']='';}




		// �p�� lead time �� html �e�{ ------
		if($parm['etp'] && $parm['etd']){	$op['lead_time'] = countDays($parm['etp'],$parm['etd']);	}
		if($parm['ets'] && $parm['etf']){	$op['pd_leadtime'] = countDays($parm['ets'],$parm['etf']);	}
		if($parm['start'] && $parm['finish']){	$op['work_days'] = countDays($parm['start'],$parm['finish']);	}

		// �[�J��^�e�� 2005/05/05
		$back_str = $cgiget."&PHP_sr_startno=".$cgino."&PHP_dept_code=".$cgi_1."&PHP_order_num=".$cgi_2."&PHP_cust=".$cgi_3."&PHP_ref=".$cgi_4."&PHP_factory=".$cgi_5;

		$op['back_str'] = $back_str;
//		$op['id'] = $PHP_id;


			// �ˬd �ۤ��O�_�s�b
		if(file_exists($GLOBALS['config']['root_dir']."/picture/".$op['order']['order_num'].".jpg")){
			$op['main_pic'] = "./picture/".$op['order']['order_num'].".jpg";
		} else {
			$op['main_pic'] = "./images/graydot.gif";
		}

			// �p��  SU �� html -----------------------------
//		$su = $op['order']['qty'] * $op['order']['su_ratio'];
		//   2005/08/31 ��Ѹ�Ʈw�� su [�H ie �p���J ]
		$su = $op['order']['su'];
		$op['order']['f_su'] = number_format($su, 0, '', ',');
		$op['order']['qty'] = number_format($op['order']['qty'], 0, '', ',');

			// �p�� ��t �� html �e�{ ----------- late_ship, late_start, late_schedule, late_work
		$today = $GLOBALS['TODAY'];
		if($parm['shp_date']){ $parm['shp'] = $parm['shp_date']; }else{ $parm['shp'] = $today; }
		if($parm['shp'] < $parm['etd']){ $factor =-1; } else { $factor =1; }
			$op['late_ship'] = $factor *(countDays($parm['shp'],$parm['etd']));

		if($parm['start']){ $parm['st'] = $parm['start']; }else{ $parm['st'] = $today; }
		if($parm['st'] < $parm['etp']){ $factor =-1; } else { $factor =1; }
			$op['late_start'] = $factor *(countDays($parm['st'],$parm['etp']));

	if($parm['start']){		
		if($parm['finish']){ $parm['upd'] = $parm['finish']; }else{ $parm['upd'] = $today; }
		if($parm['upd'] < $parm['etf']){ $factor =-1; } else { $factor =1; }
			$op['late_schedule'] = $factor *(countDays($parm['upd'],$parm['etf']));
	}else{
			$op['late_schedule'] = '';
	}

	if($parm['start']){		
//		if($parm['finish']){ $parm['upd'] = $parm['finish']; }else{ $parm['upd'] = $today; }
			$work_utd = countDays($parm['start'],$parm['upd']);
			$op['more_work'] = $work_utd - $op['pd_leadtime'];
	}else{
			$op['more_work'] = '';
	}


		$op['search'] = '1';
		$op['msg'] = $order->msg->get(2);
		page_display($op, 10, 6, $TPL_ORD_SCHEDULE_SHOW);				    
	break;


/*
//-------------------------------------------------------------------------------------
    case "do_ord_schedule":

		if(!$admin->is_power(3,6,"add")){ 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$parm = array(	"pd_id"		=>  $PHP_pd_id,
						"ets_year"	=>  $PHP_ets_year,
						"ets_mon"	=>  $PHP_ets_month,
						"ets_day"	=>  $PHP_ets_day,
		
						"etf_year"	=>  $PHP_etf_year,
						"etf_mon"	=>  $PHP_etf_month,
						"etf_day"	=>  $PHP_etf_day,
				);

		
		if (!$f1 = $order->pd_schedule_check($parm)) {  // ��J��� �����T��
			$op['msg'] = $order->msg->get(2);
			// ��^ order schedule �q�� show	

			$redir_str = $cgiself."?PHP_action=schedule_view&PHP_id=".$PHP_id."&cgiget=".$cgiget."&cgino=".$cgino."&cgi_1=".$cgi_1."&cgi_2=".$cgi_2."&cgi_3=".$cgi_3."&cgi_4=".$cgi_4."&cgi_5=".$cgi_5;


			redirect_page($redir_str);

			break;
		}  // end if  ��J��� �����T 


		//  ���T ��J �q�� �Ƶ{ --- ETS, ETF �g�J  // ����g����榡 --- �]�P�_�O�_���S���� !!

		if (($parm['ets_mon'] =='') && ($parm['ets_day'] =='') && ($parm['ets_year'] =='')){
			$parm['ets'] = '';
		}else{
			$parm['ets'] = $parm['ets_year'].'-'.$parm['ets_mon'].'-'.$parm['ets_day'];
		}

		if (($parm['etf_mon'] =='') && ($parm['etf_day'] =='') && ($parm['etf_year'] =='')){
			$parm['etf'] = '';
		}else{
			$parm['etf'] = $parm['etf_year'].'-'.$parm['etf_mon'].'-'.$parm['etf_day'];
		}


		$f1 = $order->add_pd_schedule($parm);   // ��s��Ʈw

		if (!$f1) {  // �S�����\��J��Ʈ�

			$op['msg'] = $order->msg->get(2);

			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    

		break;

		}   // end if (!$F1)---------  ���T�g�J  ------------------------------------

		// �p�G ��Ӥ�������F [ETS][ETF]�h��� �q�檺status�� 6 >�Ʋ��ݽT�{ SCHDL CFMing
		//  �n�Ҽ{========???? �U�@ �����_�q ???? �γ\�S�g�J ���ܮ� ???????
		if($parm['ets'] && $parm['etf']){
				$argv[1] = 'status';
				$argv[2] = '6';
				$argv[0] = $PHP_id;

				$A1 = $order->update_field($argv);   // ��s �q�檬�p�O��  status =>6
			if (!$A1) {  // �S�����\��J��Ʈ�
				$op['msg'] = $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}  				

		}

				
				
				# �O���ϥΪ̰ʺA
			$message = "�g�J�q��[ ".$cgi_2." ] �� ETS, ETF ";

//			$log->log_add(0,"104A",$message);

			$redir_str = $cgiself."?PHP_action=schedule_view&PHP_id=".$PHP_id."&cgiget=".$cgiget."&cgino=".$cgino."&cgi_1=".$cgi_1."&cgi_2=".$cgi_2."&cgi_3=".$cgi_3."&cgi_4=".$cgi_4."&cgi_5=".$cgi_5;

			redirect_page($redir_str);

		break;

*/







	


















//-------------------------------------------------------------------------------------
//			 job 37  �q �� �� �{  �T�{  [  �������v �� job ]

#	NOTE: ���ݳ]�w�i�J�� user ���� [ ���P�u�t ===== ============== ??????????????

//-------------------------------------------------------------------------------------
    case "cfm_pd_schedule":
		check_authority(4,3,"view");
		$id_ref = $GLOBALS['SCACHE']['ADMIN']['dept'];

		if (($id_ref == "SA") || ($id_ref == "GM")){   // �p�G�i�J�������O �W�ź޲z��[none] ���`�g�z��[GM]
			$id_ref ='';
		}


			if (!$op = $order->schedule_uncfm_search($id_ref)) {
				$op['msg']= $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

				$op['msg']= $order->msg->get(2);
			page_display($op, 4, 3, $TPL_UNCFM_SCHEDULE_LIST);			
		break;
//=======================================================
	case "cfm_schedule_view":
		check_authority(4,3,"add");
		$op['cgi'] = array(	"cgiget"	=>  $cgiget,
							"cgino"		=>  $cgino,
							"cgi_1"		=>	$cgi_1,
							"cgi_2"		=>	$cgi_2,
							"cgi_3"		=>	$cgi_3,
							"cgi_4"		=>	$cgi_4,
							"cgi_5"		=>	$cgi_5,
				);
		$op['order'] = $order->schedule_get($PHP_id);  //���X�ӵ��O��
			if (!$op['order']) {
				$op['msg'] = $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
		// ���X order_log ���q�� �����v50�����O��  -------------------
		$logs = $order_log->search50($op['order']['order_num']);  //���X�ӭq�����log�O��
		if (!$logs){
			$op['msg'] = $order->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$op['logs'] = $logs['order_log'];
		$op['log_records'] = $logs['records'];
			// �N�|�ǥX op['logs'] ��log����� �� $op['log_records']=1 �ӫq�_���L���
		//--------------------------------

		$parm = $op['order'];

		// �p�� lead time
		$op['lead_time'] = countDays ($parm['etp'],$parm['etd']);

		// �[�J��^�e�� 2005/05/05
		$back_str = $cgiget."&PHP_sr_startno=".$cgino."&PHP_dept_code=".$cgi_1."&PHP_order_num=".$cgi_2."&PHP_cust=".$cgi_3."&PHP_ref=".$cgi_4."&PHP_factory=".$cgi_5;

		$op['back_str'] = $back_str;
		$op['id'] = $PHP_id;


				# �O���ϥΪ̰ʺA
//		$log->log_add(0,"37A","�s�� �Ƶ{�q���ơG".$op['order']['order_num']);

			// �ˬd �ۤ��O�_�s�b
		if(file_exists($GLOBALS['config']['root_dir']."/picture/".$op['order']['order_num'].".jpg")){
			$op['main_pic'] = "./picture/".$op['order']['order_num'].".jpg";
		} else {
			$op['main_pic'] = "./images/graydot.gif";
		}

			// �p��  SU �� html -----------------------------
//		$su = $op['order']['qty'] * $op['order']['su_ratio'];
		//   2005/08/31 ��Ѹ�Ʈw�� su [�H ie �p���J ]
		$su = $op['order']['su'];
		$op['order']['f_su'] = number_format($su, 0, '', ',');
		$op['order']['qty'] = number_format($op['order']['qty'], 0, '', ',');

/*  *************************** 20060411�Ҽ{�O���O�h�l��????????????????
		// �]�w �����W�� acc_etd �p��e�{ --- �p�G�w�g���Facc_etd �h�����X�{ �p�G�S���N�X�{ �U�� �i��J
		if(!$op['order']['acc_etd']){
			$op['acc_etd_year'] = $arry2->select($YEAR_WORK,'','PHP_acc_etdyear','select','');  	
			$op['acc_etd_mon'] = $arry2->select($MONTH_WORK,'','PHP_acc_etdmon','select','');  	
			$op['acc_etd_day'] = $arry2->select($DAY_WORK,'','PHP_acc_etdday','select','');
		} else {
				$acc_year = substr($op['order']['acc_etd'],0,4);
			$op['acc_etd_year'] =$op['order']['acc_etd']."<input type='hidden' name='PHP_acc_etdyear' value='".$acc_year."'>";
				$acc_mon = substr($op['order']['acc_etd'],5,2);
			$op['acc_etd_mon'] ="<input type='hidden' name='PHP_acc_etdmon' value='".$acc_mon."'>";
				$acc_day = substr($op['order']['acc_etd'],8,2);
			$op['acc_etd_day'] ="<input type='hidden' name='PHP_acc_etdday' value='".$acc_day."'>";
		}
*/		
		
		// === �]�w �����W�� ets, etf �p��e�{ === ���� ETS �~�|�n�D cfm ===== �Ѷ} year, mon, day ���U��
	if ($op['order']['status'] == 7){

		$op['ets_year'] = $op['order']['ets']." CFMed";
		$op['etf_year'] = $op['order']['etf']." CFMed";

	}else{

		if((!$op['order']['ets']) || (!$op['order']['ets'])) {  // �� ets �� etf ���@�ӨS��J�� status �令 4

			if(!$A1 = $order->update_field('status','4',$PHP_id)){;   // ��s �q�檬�p�O��  status =>4
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

		} else {
			// creat ETS, date combo box
				$ets_year = substr($op['order']['ets'],0,4);
				$ets_mon = substr($op['order']['ets'],5,2);
				$ets_day = substr($op['order']['ets'],8,2);

			$op['ets_year'] = $arry2->select($YEAR_WORK,$ets_year,'PHP_ets_year','select','');  	
			$op['ets_mon'] = $arry2->select($MONTH_WORK,$ets_mon,'PHP_ets_month','select','');  	
			$op['ets_day'] = $arry2->select($DAY_WORK,$ets_day,'PHP_ets_day','select','');  	

			$op['submit_flag'] = "1";
		}   //  END of ETS

		if((!$op['order']['etf']) || (!$op['order']['etf'])) {  // �� ets �� etf ���@�ӨS��J�� status �令 4

			if(!$A1 = $order->update_field('status','4',$PHP_id)){;   // ��s �q�檬�p�O��  status =>4
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

		} else {
			// creat ETF, date combo box
				$etf_year = substr($op['order']['etf'],0,4);
				$etf_mon = substr($op['order']['etf'],5,2);
				$etf_day = substr($op['order']['etf'],8,2);

			$op['etf_year'] = $arry2->select($YEAR_WORK,$etf_year,'PHP_etf_year','select','');  	
			$op['etf_mon'] = $arry2->select($MONTH_WORK,$etf_mon,'PHP_etf_month','select','');  	
			$op['etf_day'] = $arry2->select($DAY_WORK,$etf_day,'PHP_etf_day','select','');  	

			$op['submit_flag'] = "1";
		}   // END of ETF
	}   // end of IF order.status = 6 �� schedule�T�{ ����--------------

		$op['msg'] = $order->msg->get(2);		
		page_display($op, 4, 3, $TPL_CFM_SCHEDULE_SHOW);	
			    	    
	break;

//-------------------------------------------------------------------------------------
    case "do_cfm_pd_schedule":
//-------------------------------------------------------------------------------------

// �T�{�Ƶ{��  ��gpdtion- ETS, ETF, FTY_SU  || ��g s_order -STATUS 7  ||  �n�g�J capacity - schedule ;

		check_authority(4,3,"add");
		$parm = array(	"pd_id"		=>  $PHP_pd_id,
						"ets"		=>  $PHP_ets,
						"etf"		=>  $PHP_etf,
						"sub_con"	=> 	$PHP_sub_con,
				);


			$redir_str = $cgiself."?PHP_action=cfm_schedule_view&PHP_id=".$PHP_id."&cgiget=".$cgiget."&cgino=".$cgino."&cgi_1=".$cgi_1."&cgi_2=".$cgi_2."&cgi_3=".$cgi_3."&cgi_4=".$cgi_4."&cgi_5=".$cgi_5;


		// ++++++++++++======  �g�J �Ʋ� ���u�t�� SU [FY_SU]  �p��X��� �Ʋ���� fty_su ------------
		//  NOTE :++++++ �ѼƶǤJ ��html �e�X hidden ���n�`�N format ���d�츹���Ѽ� �n��ѼƦW�l[$PHP_qty]
		//  �����X �ӵ� s_order ���

			if(!$ord = $order->get($PHP_id)){
				$op['msg'] = $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			} 

			// �g�J capacity ---------------
			// �ˬd capacity �ɤ��O�_�w�g���O�� ???
			$s_year = substr($parm['ets'],0,4);
			$e_year = substr($parm['etf'],0,4);
			for($i=$s_year;$i<$e_year+1;$i++){
				
				if (!$T=$capaci->get($ord['factory'], $i)) {
					$op['msg'] = $capaci->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  	
					
					exit;
//					break;	    
				}
			}
		//========= �P�ɼg�J capacity ���� schedule �椺 ===============		
		//   2005/08/31 ��Ѹ�Ʈw�� su [�H ie �p���J ]
//		$parm['fty_su'] = $order->divert_month_su($ord['qty'],$ord['su_ratio'],$parm['ets'],$parm['etf'],$ord['factory'],'schedule');
		$parm['fty_su'] = $order->distri_month_su($ord['su'],$parm['ets'],$parm['etf'],$ord['factory'],'schedule');

		$f1 = $order->add_cfm_pd_schedule($parm);   // ��s��Ʈw

		if (!$f1) {  // �S�����\��J��Ʈ�
			$op['msg'] = $order->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}   // end if (!$F1)---------  ���T�g�J  ------------------------------------


	//***********************************************************
	// �g�J s_order - status �� schedule���O��
		// �p�G ��Ӥ�������F [ETS][ETF]�h��� �q�檺status�� 6 >�Ʋ��ݽT�{ SCHDL CFMing
		//  �n�Ҽ{========???? �U�@ �����_�q ???? �γ\�S�g�J ���ܮ� ???????

		// ���X�~�X... �H��J�ɤ��~���~�X[���]
		$dt = decode_date(1);

		if($parm['ets'] && $parm['etf']){
			$argv = array(	"id"			=>  $PHP_id,
							"status"		=>  7,
							"schd_er"		=>	$GLOBALS['SCACHE']['ADMIN']['name'],
							"schd_date"		=>	$dt['date']
					);

				$A1 = $order->update_sorder_4_cfm_pd_schedule($argv);   // ��s �q�檬�p�O��  status =>7

			if (!$A1) {  // �S�����\��J��Ʈ�
				$op['msg'] = $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}  				

		}

//2006/03/20 �o�{log�ɨS�g�Jorder number �μg�� job�N�� ��				
			if(!$ord_num= $order->get_field_value("order_num", $PHP_id)){  
				$op['msg'] = $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}  				
//echo "<br>order number ===>".$ord_num;
//exit;
	
	//***********************************************************
				# �O���ϥΪ̰ʺA
			$message = "Confirm order [ ".$ord_num." ] production schedule [ETS, ETF] ";

			$log->log_add(0,"37A",$message);

			// ���� html ���e�{ ----- ���A�X�{ �@�T�{�� button ��ets, etf ������U��

				$op['ets_year'] = $parm['ets_year'];
				$op['ets_mon'] = $parm['ets_mon'];
				$op['ets_day'] = $parm['ets_day'];

				$op['etf_year'] = $parm['etf_year'];
				$op['etf_mon'] = $parm['etf_mon'];
				$op['etf_day'] = $parm['etf_day'];


			redirect_page($redir_str);

		break;













//-------------------------------------------------------------------------------------
//			 job 104  �D�Ʈƶi��
//-------------------------------------------------------------------------------------
    case "material_schedule":
    	check_authority(10,4,"view");
		$op['msg'] = $order->msg->get(2);

				// creat cust combo box
			if ($GLOBALS['SCACHE']['ADMIN']['dept']=='HJ' || $GLOBALS['SCACHE']['ADMIN']['dept']=='LY')
			{
				$op['factory'] = $GLOBALS['SCACHE']['ADMIN']['dept'];
				$op['fty_flag']=1;
			}else{
				$op['factory'] = $arry2->select($FACTORY,'','PHP_factory','select','');  	
			}

		$where_str="order by cust_s_name"; //��cust_s_name�Ƨ�
		$cust_def = $cust->get_fields('cust_init_name',$where_str);
		$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//���X�Ȥ�N��
		for ($i=0; $i< sizeof($cust_def); $i++)
		{
			$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//�H [�Ȥ�N��-�Ȥ�²��] �e�{
		}
		$op['cust_select'] =  $arry2->select($cust_value,'','PHP_cust','select','',$cust_def_vue); 
		page_display($op, 10, 4, $TPL_MAT_SCHEDULE);    	    
		break;

//=======================================================
    case "mat_schedule_search":
		check_authority(10,4,"view");		
		$parm = array(	
						"order_num"		=>  $PHP_order_num,
						"cust"			=>	$PHP_cust,
						"ref"			=>	$PHP_ref,
						"factory"		=>	$PHP_factory,
				);

			if (!$op = $order->mat_schedule_search(1)) {    // ���p����M ==tabel s_order, pdtion
				$op['msg']= $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

				$op['msg']= $order->msg->get(2);
				$op['cgi']= $parm;

			$allow = $admin->decode_perm(10,4);   // �]�w �s�W�R���v��
			page_display($op, 10, 4, $TPL_MAT_SCHEDULE_LIST);  			
		break;
	
//=======================================================
	case "mat_schedule_view":
		check_authority(10,4,"view");		
		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];   // �P�w�i�J����������
		$op['cgi'] = array(	"cgiget"	=>  $cgiget,
							"cgino"		=>  $cgino,
							"cgi_1"		=>	$cgi_1,
							"cgi_2"		=>	$cgi_2,
							"cgi_3"		=>	$cgi_3,
							"cgi_4"		=>	$cgi_4,
							"cgi_5"		=>	$cgi_5,
				);
		
		$op['order'] = $order->mat_schedule_get($PHP_id);  // ���p�� �d�M ���X�ӵ��O��
		if (!$op['order']) {
			$op['msg'] = $order->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		// ���X order_log ���q�� �����v50�����p�O��  -------------------
		$logs = $order_log->search50($op['order']['order_num']);  //���X�ӭq�����log�O��
		if (!$logs){
			$op['msg'] = $order->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$op['logs'] = $logs['order_log'];
		$op['log_records'] = $logs['records'];
			// �N�|�ǥX op['logs'] ��log����� �� $op['log_records']=1 �ӧP�_���L���
		//--------------------------------


		$parm = $op['order'];

		// �p�� lead time
		$op['lead_time'] = countDays ($parm['etp'],$parm['etd']);


		// �[�J��^�e�� 2005/05/05
		$back_str = $cgiget."&PHP_sr_startno=".$cgino."&PHP_dept_code=".$cgi_1."&PHP_order_num=".$cgi_2."&PHP_cust=".$cgi_3."&PHP_ref=".$cgi_4."&PHP_factory=".$cgi_5;

		$op['back_str'] = $back_str;

			// �ˬd �ۤ��O�_�s�b
		if(file_exists($GLOBALS['config']['root_dir']."/picture/".$op['order']['order_num'].".jpg")){
			$op['main_pic'] = "./picture/".$op['order']['order_num'].".jpg";
		} else {
			$op['main_pic'] = "./images/graydot.gif";
		}

			// �p��  SU �� html -----------------------------
		//   2005/08/31 ��Ѹ�Ʈw�� su [�H ie �p���J ]
		$su = $op['order']['su'];
		$op['order']['f_su'] = number_format($su, 0, '', ',');
		$op['order']['qty'] = number_format($op['order']['qty'], 0, '', ',');

	//--------------------------------------------------		
		// �]�w �����W�� mat_shp �p��e�{ --- �p�G�w�g���Fmat_shp �h�����X�{ �p�G�S�� �N�X�{ ����X�f���s
		if(!$op['order']['mat_shp']){	$op['mat_not_shp'] = '1';  		} 
		if(!$op['order']['m_acc_shp']){	$op['macc_not_shp'] = '1';  	} 
		if(!$op['order']['acc_shp']){	$op['acc_not_shp'] = '1';		} 

	//  �@�� �䥦���Ѽ�
		$op['cgino'] = $cgino;
		$op['id'] = $PHP_id;

		$op['msg'] = $order->msg->get(2);
		$op['search'] = '1';		
		
		if ($user_dept=="HJ" || $user_dept=="LY")//�P�_�i�J�̬O�_�Ӧۤu�t,�Y�Ӧۤu�t..�h�����P�_�O�_������v��
		{
			if ($user_dept == $op['order']['dept'])$op['dept_edit']=1;			
		}else{
			$op['dept_edit']=1;
		}
		
		page_display($op, 10, 4, $TPL_MAT_SCHEDULE_SHOW);	    	    
	break;

//-------------------------------------------------------------------------------------
    case "do_mat_schedule":

		check_authority(10,4,"add");
		$parm = array(	"pd_id"			=>  $PHP_pd_id,
						"mat_etd"		=>	$PHP_mat_etd,
						"order_num"		=>  $PHP_order_num,
						"macc_etd"		=>	$PHP_macc_etd,
						"acc_etd"		=>	$PHP_acc_etd,	
				);

		
		if ($PHP_mat_etd=='' && $PHP_macc_etd=='' && $PHP_acc_etd=='') {  // ��J��� �����T��
			
			// ��^ order mat schedule �q�� show	

			$redir_str = $cgiself."?PHP_action=mat_schedule_view&PHP_id=".$PHP_id."&cgiget=".$cgiget."&cgino=".$cgino."&cgi_1=".$cgi_1."&cgi_2=".$cgi_2."&cgi_3=".$cgi_3."&cgi_4=".$cgi_4."&cgi_5=".$cgi_5;
			$op['msg'][] = "Please choice one of the ETA date";			
			redirect_page($redir_str);
			break;
		}  // end if  ��J��� �����T 

		$f1 = $order->add_material_etd($parm);   // ��s��Ʈw

		if (!$f1) {  // �S�����\��J��Ʈ�

			$op['msg'] = $order->msg->get(2);

			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    

		break;

		}   // end if (!$F1)---------  ���T�g�J  ------------------------------------

				# �O���ϥΪ̰ʺA
			$message = "Writing material ETD on order : [ ".$parm['order_num']." ] �D�ƮƤ�ETD";
			$log->log_add(0,"104A",$message);

			$redir_str = $cgiself."?PHP_action=mat_schedule_view&PHP_id=".$PHP_id."&cgiget=".$cgiget."&cgino=".$cgino."&cgi_1=".$cgi_1."&cgi_2=".$cgi_2."&cgi_3=".$cgi_3."&cgi_4=".$cgi_4."&cgi_5=".$cgi_5;

			redirect_page($redir_str);

		break;

//-------------------------------------------------------------------------------------
    case "mat_ship":

		check_authority(10,4,"edit");
			$D = decode_date(1,0);
			$to_day = $D['date'];
			$mat_etd = $order->get_field_value('mat_etd',$PHP_pd_id,'','pdtion');				
			if($mat_etd =='0000-00-00' || !$mat_etd) $order->update_pdtion_field('mat_etd', $TODAY, $PHP_pd_id);

		$f1 = $order->mat_ship($PHP_pd_id, $to_day);   // ��s��Ʈw

		if (!$f1) {  // �S�����\��J��Ʈ�

			$op['msg'] = $order->msg->get(2);

			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    

		break;

		}   // end if (!$F1)---------  ���T�g�J  ------------------------------------

				# �O���ϥΪ̰ʺA
			$message = "Writing fabric received on order : [ ".$PHP_order_num." ]";
			$log->log_add(0,"104A",$message);

		$redir_str = $cgiself."?PHP_action=mat_schedule_view&PHP_id=".$PHP_id."&cgiget=".$cgiget."&cgino=".$cgino."&cgi_1=".$cgi_1."&cgi_2=".$cgi_2."&cgi_3=".$cgi_3."&cgi_4=".$cgi_4."&cgi_5=".$cgi_5;

		redirect_page($redir_str);

		break;

//-------------------------------------------------------------------------------------
    case "macc_ship":

		check_authority(10,4,"edit");

			$D = decode_date(1,0);
			$to_day = $D['date'];

		$macc_etd = $order->get_field_value('m_acc_etd',$PHP_pd_id,'','pdtion');				

		if($macc_etd =='0000-00-00' || !$macc_etd) $order->update_pdtion_field('m_acc_etd', $TODAY, $PHP_pd_id);

		$f1 = $order->macc_ship($PHP_pd_id, $to_day);   // ��s��Ʈw

		if (!$f1) {  // �S�����\��J��Ʈ�

			$op['msg'] = $order->msg->get(2);

			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    

		break;

		}   // end if (!$F1)---------  ���T�g�J  ------------------------------------

				# �O���ϥΪ̰ʺA
			$message = "Writing main accessory received on order : [ ".$PHP_order_num." ]";
			$log->log_add(0,"104A",$message);

		$redir_str = $cgiself."?PHP_action=mat_schedule_view&PHP_id=".$PHP_id."&cgiget=".$cgiget."&cgino=".$cgino."&cgi_1=".$cgi_1."&cgi_2=".$cgi_2."&cgi_3=".$cgi_3."&cgi_4=".$cgi_4."&cgi_5=".$cgi_5;

		redirect_page($redir_str);

		break;


//-------------------------------------------------------------------------------------
    case "acc_ship":
		check_authority(10,4,"edit");
			$D = decode_date(1,0);
			$to_day = $D['date'];

		$acc_etd = $order->get_field_value('acc_etd',$PHP_pd_id,'','pdtion');				
		if($acc_etd =='0000-00-00' || !$acc_etd) $order->update_pdtion_field('acc_etd', $TODAY, $PHP_pd_id);


		$f1 = $order->acc_ship($PHP_pd_id, $to_day);   // ��s��Ʈw

		if (!$f1) {  // �S�����\��J��Ʈ�

			$op['msg'] = $order->msg->get(2);

			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    

		break;

		}   // end if (!$F1)---------  ���T�g�J  ------------------------------------

				# �O���ϥΪ̰ʺA
			$message = "Writing other accessory received on order : [ ".$PHP_order_num." ]";
			$log->log_add(0,"104A",$message);

		$redir_str = $cgiself."?PHP_action=mat_schedule_view&PHP_id=".$PHP_id."&cgiget=".$cgiget."&cgino=".$cgino."&cgi_1=".$cgi_1."&cgi_2=".$cgi_2."&cgi_3=".$cgi_3."&cgi_4=".$cgi_4."&cgi_5=".$cgi_5;

		redirect_page($redir_str);

		break;















//-------------------------------------------------------------------------------------
//			 job 36  �Ͳ��Ƶ{
//-------------------------------------------------------------------------------------

#	NOTE: ���ݳ]�w�i�J�� user ���� [ ���P�u�t ===== ============== ?????????????

//-------------------------------------------------------------------------------------
    case "pd_schedule":
		check_authority(4,2,"view");
//---------------------------------- �P�_�O�_�u�t�H���i�J ---------------
		$where_str = $manager = $dept_id = $fty_select = '';

		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];   // �q�w�i�J����������

		for ($i=0; $i<count($FACTORY);$i++){
			if($user_dept == $FACTORY[$i]){
				$dept_id = $FACTORY[$i];   // �p�G�O�u�t�����H���i�J dept_id ���w�Ӥu�t---
			}
		}
		if (!$dept_id) {    // ���O�u�t�H���i�J��
			$manager = 1;
			$fty_select = $arry2->select($FACTORY,"","PHP_factory","select","");  //�u�t select���
		} else {
			$where_str = " WHERE factory = '".$dept_id."' ";
		}

		$op['manager_flag'] = $manager;
		$op['fty_id'] = $dept_id;
		$op['fty_select'] = $fty_select;

		$op['msg'] = $order->msg->get(2);

		// creat cust combo box
		$where_str="order by cust_s_name"; //��cust_s_name�Ƨ�
		$cust_def = $cust->get_fields('cust_init_name',$where_str);
		$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//���X�Ȥ�N��
		for ($i=0; $i< sizeof($cust_def); $i++)
		{
			$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//�H [�Ȥ�N��-�Ȥ�²��] �e�{
		}
		$op['cust_select'] =  $arry2->select($cust_value,'','PHP_cust','select','',$cust_def_vue); 
	
			page_display($op, 4, 2, $TPL_SCHEDULE);	    	    
		break;



//=======================================================
    case "schedule_search":

		check_authority(4,2,"view");
		$parm = array(	"dept"			=>  $PHP_dept_code,
						"order_num"		=>  $PHP_order_num,
						"cust"			=>	$PHP_cust,
						"ref"			=>	$PHP_ref,
						"factory"		=>	$PHP_factory,
				);

			if (!$op = $order->schedule_search(1)) {
				$op['msg']= $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			// op �Q�M���F �ݦA�P�_�@�� 

				$op['msg']= $order->msg->get(2);
				$op['cgi']= $parm;
			page_display($op, 4, 2, $TPL_SCHEDULE_ORDER_LIST);
		break;

	

//=======================================================
	case "schedule_view":

		check_authority(4,2,"view");
		$op['cgi'] = array(	"cgiget"	=>  $cgiget,
							"cgino"		=>  $cgino,
							"cgi_1"		=>	$cgi_1,
							"cgi_2"		=>	$cgi_2,
							"cgi_3"		=>	$cgi_3,
							"cgi_4"		=>	$cgi_4,
							"cgi_5"		=>	$cgi_5,
				);
		
		$op['order'] = $order->schedule_get($PHP_id);  //���X�ӵ��O��
		if (!$op['order']) {
			$op['msg'] = $order->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		// ���X order_log ���q�� �����v50�����p�O��  -------------------
		$logs = $order_log->search50($op['order']['order_num']);  //���X�ӭq�����log�O��
		if (!$logs){
			$op['msg'] = $order->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$op['logs'] = $logs['order_log'];
		$op['log_records'] = $logs['records'];
			// �N�|�ǥX op['logs'] ��log����� �� $op['log_records']=1 �ӧP�_���L���
		//--------------------------------

		$parm = $op['order'];

		// �p�� lead time
		$op['lead_time'] = countDays ($parm['etp'],$parm['etd']);

		// �[�J��^�e�� 2005/05/05
		$back_str = $cgiget."&PHP_sr_startno=".$cgino."&PHP_dept_code=".$cgi_1."&PHP_order_num=".$cgi_2."&PHP_cust=".$cgi_3."&PHP_ref=".$cgi_4."&PHP_factory=".$cgi_5;

		$op['back_str'] = $back_str;
		$op['id'] = $PHP_id;


			// �ˬd �ۤ��O�_�s�b
		if(file_exists($GLOBALS['config']['root_dir']."/picture/".$op['order']['order_num'].".jpg")){
			$op['main_pic'] = "./picture/".$op['order']['order_num'].".jpg";
		} else {
			$op['main_pic'] = "./images/graydot.gif";
		}

			// �p��  SU �� html -----------------------------
		//   2005/08/31 ��Ѹ�Ʈw�� su [�H ie �p���J ]
		$su = $op['order']['su'];
		$op['order']['f_su'] = number_format($su, 0, '', ',');
		$op['order']['qty'] = number_format($op['order']['qty'], 0, '', ',');

		
		if ($op['order']['ets'])$op['ets_flag']=1;
		// �]�w �����W�� ets, etf �p��e�{ --- �p�G�w�g���F �h�����X�{ �p�G�S���N�X�{ �U�� �i��J
		if (!$op['order']['ets'] || !$op['order']['etf'] ) $op['submit_flag'] = "1";
		$op['msg'] = $order->msg->get(2);	
		page_display($op, 4, 2, $TPL_SCHEDULE_SHOW);	    	    
	break;

//-------------------------------------------------------------------------------------
    case "do_pd_schedule":

		check_authority(4,2,"add");
		if (!isset($PHP_sub_con))$PHP_sub_con=0;
		$parm = array(	"pd_id"		=>  $PHP_pd_id,
						"ets"	=>  $PHP_ets,		
						"etf"	=>  $PHP_etf,
						"sub_con" => $PHP_sub_con
				);

		
		if (!$PHP_ets && !$PHP_etf) {  // ��J��� �����T��
			$op['msg'][] = "Please choice ETS or ETF date";
			// ��^ order schedule �q�� show	

			$redir_str = $cgiself."?PHP_action=schedule_view&PHP_id=".$PHP_id."&cgiget=".$cgiget."&cgino=".$cgino."&cgi_1=".$cgi_1."&cgi_2=".$cgi_2."&cgi_3=".$cgi_3."&cgi_4=".$cgi_4."&cgi_5=".$cgi_5;


			redirect_page($redir_str);

			break;
		}  // end if  ��J��� �����T 		

//2007-04-19 �u�t�i�H���N��Ƶ{���(���O�b�Ͳ���N�����)	
		$f1 = $order->add_pd_schedule_chk($parm);   // ��s��Ʈw
		if (!$f1) {  // �S�����\��J��Ʈ�

			$op['msg'] = $order->msg->get(2);

			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    

		break;

		}   // end if (!$F1)---------  ���T�g�J  ------------------------------------
	
		if ($PHP_status == 7)
		{
			$op['order'] = $order->schedule_get($PHP_ord_id);  //���X�ӵ��O��
			if (!$op['order']) {
				$op['msg'] = $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

			$order->delete_month_su( $op['order']['ets'], $op['order']['etf'],$op['order']['su'],$op['order']['factory'],'schedule');
			$order->del_cfm_pd_schedule($PHP_pd_id);
		}
		$f1 = $order->add_pd_schedule($parm);   // ��s��Ʈw
		
		if (!$f1) {  // �S�����\��J��Ʈ�

			$op['msg'] = $order->msg->get(2);

			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    

		break;

		}   // end if (!$F1)---------  ���T�g�J  ------------------------------------

		// �p�G ��Ӥ�������F [ETS][ETF]�h��� �q�檺status�� 6 >�Ʋ��ݽT�{ SCHDL CFMing
		//  �n�Ҽ{========???? �U�@ �����_�q ???? �γ\�S�g�J ���ܮ� ???????
		if($parm['ets'] && $parm['etf']){

			if(!$A1 = $order->update_field('status','6',$PHP_id)){;   // ��s �q�檬�p�O��  status =>4
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			
			if (!$A1) {  // �S�����\��J��Ʈ�
				$op['msg'] = $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}  				

		}

//2006/03/20 �o�{log�ɨS�g�Jorder number �μg�� job�N�� ��

			// ���X�q�渹�X ------
			if(!$ord_num= $order->get_field_value("order_num", $PHP_pd_id,'',"pdtion")){  
				$op['msg'] = $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}  				
	
//echo "<br>order number ===>".$ord_num;
//exit;
				# �O���ϥΪ̰ʺA
			$message = "Production schedule of ETP ETS write on [ ".$ord_num." ]  ";

			$log->log_add(0,"42A",$message);

			$redir_str = $cgiself."?PHP_action=schedule_view&PHP_id=".$PHP_id."&cgiget=".$cgiget."&cgino=".$cgino."&cgi_1=".$cgi_1."&cgi_2=".$cgi_2."&cgi_3=".$cgi_3."&cgi_4=".$cgi_4."&cgi_5=".$cgi_5;

			redirect_page($redir_str);

		break;

























//-------------------------------------------------------------------------------------
//		AG  �˥��i�װl��  [8, 3 ]
//-------------------------------------------------------------------------------------
    case "ag":
		if(!$admin->is_power(8,3,"view")){ 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		if ($GLOBALS['SCACHE']['ADMIN']['id'] == "SA" ) {   // �p�G�O manager �i�J��...
			$op['manager_flag'] = 1;
			$dept_def = $dept->get_fields('dept_code');   // ���X�����������O�N��
			$op['dept_select'] = $arry2->select($dept_def,"","PHP_dept_code","select","");  //�����Oselect���
		} else {
			$op['dept_code'] = $_SESSION['SCACHE']['ADMIN']['dept'];
		}

		$op['msg'] = $smpl->msg->get(2);

		$allow = $admin->decode_perm(2,1);   // �]�w �s�W�R���v��
		if ($op['msg']){ $op['msg_YES'] = 1; } else { $op['msg_YES'] = "" ;}
		if ($allow['view'])		{	$op['view_flag'] = 1;} else {	$op['view_flag'] = "";}
		if ($allow['add'])		{	$op['add_flag'] = 1;} else {	$op['add_flag'] = "";}
		if ($allow['edit'])		{	$op['edit_flag'] = 1;} else {	$op['edit_flag'] = "";}
		if ($allow['del'])		{	$op['del_flag'] = 1;} else {	$op['del_flag'] = "";}

				// creat cust combo box

				$where_str ="";
				if ($GLOBALS['SCACHE']['ADMIN']['id'] != "SA" ) {   // �p�G�O ���Omanager�i�J�� [manager�i�ݥ����˦�]...
						$where_str =" WHERE dept ='".$_SESSION['SCACHE']['ADMIN']['dept']."' ";
				}
				$cust_def = $cust->get_fields('cust_s_name',$where_str);   // ���X �Ȥ�N��
				$op['cust_select'] =  $arry2->select($cust_def,'','PHP_cust','select','');  	


			$layout->assign($op);
			$layout->display($TPL_AG_SEARCH);		    	    
		break;

//=======================================================
    case "ag_search":

		if(!$admin->is_power(8,3,"view")){ 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$parm = array(	"ag_num"		=>  $PHP_ag_num,
						"cust"			=>	$PHP_cust,
						"order_num"		=>  $PHP_order_num,
						"org_patt_num"	=>	$PHP_org_patt_num,
						"patt_num"		=>	$PHP_patt_num
				);

			if (!$op = $ag->search(1)) {
				$op['msg']= $wi->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			$op['manager_flag'] = 1;  // 2005/09/13 �令�ֶi�J���O�@�� �ݿ�t�O

			// op �Q�M���F �ݦA�P�_�@�� 
//		if ($GLOBALS['SCACHE']['ADMIN']['id'] == "SA" ) {   // �p�G�O manager �i�J��...
//			$op['manager_flag'] = 1;
//		} 
//		$op['dept_id'] = get_dept_id();

				$op['msg']= $wi->msg->get(2);
				$op['cgi']= $parm;

			$allow = $admin->decode_perm(8,3);   // �]�w �s�W�R���v��
			if ($op['msg']){ $op['msg_YES'] = 1; } else { $op['msg_YES'] = "" ;}
			if ($allow['view'])		{	$op['view_flag'] = 1;} else {	$op['view_flag'] = "";}
			if ($allow['add'])		{	$op['add_flag'] = 1;} else {	$op['add_flag'] = "";}
			if ($allow['edit'])		{	$op['edit_flag'] = 1;} else {	$op['edit_flag'] = "";}
			if ($allow['del'])		{	$op['del_flag'] = 1;} else {	$op['del_flag'] = "";}
//$op['NEXT_page'] = 1;
//$op['PREV_page'] = 1;

			$layout->assign($op);
			$layout->display($TPL_AG);  
		break;

	
	
//=======================================================
	case "ag_add":

		if(!$admin->is_power(8,3,"add")){ 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		if (!$PHP_dept_code){               // �p�G�S�����ﳡ���O��.....[�u�� manager �n�J�� ]
				if (!$PHP_cust){               // �p�G�]�S�����ﳡ���Ȥ��
					$op['msg'][] = "sorry! �Х���w �����O�θӳ������Ȥ�!";
				} else {
					$op['msg'][] = "sorry! �Х���w �����O!";
				}
			if ($GLOBALS['SCACHE']['ADMIN']['id'] == "SA" ) {   // �p�G�O manager �i�J��...
				$op['manager_flag'] = 1;
				$dept_def = $dept->get_fields('dept_code');   // ���X�����������O�N��
				$op['dept_select'] = $arry2->select($dept_def,"","PHP_dept_code","select","");  //�����Oselect���
			} 

				$allow = $admin->decode_perm(2,1);   // �]�w �s�W�R���v��
				if ($op['msg']){ $op['msg_YES'] = 1; } else { $op['msg_YES'] = "" ;}
				if ($allow['view'])		{	$op['view_flag'] = 1;} else {	$op['view_flag'] = "";}
				if ($allow['add'])		{	$op['add_flag'] = 1;} else {	$op['add_flag'] = "";}
				if ($allow['edit'])		{	$op['edit_flag'] = 1;} else {	$op['edit_flag'] = "";}
				if ($allow['del'])		{	$op['del_flag'] = 1;} else {	$op['del_flag'] = "";}

			// creat cust combo box
			$op['year_select'] = $arry2->select($YEAR_WORK,'','PHP_year','select','');  	

			$where_str ="";
			if ($GLOBALS['SCACHE']['ADMIN']['id'] != "SA" ) {   // �p�G�O ���Omanager �i�J��[manager�i�ݥ����˦�]...
					$where_str =" WHERE dept ='$PHP_dept_code' ";
			}
			$cust_def = $cust->get_fields('cust_s_name',$where_str);   // ���X �Ȥ�N��
			$op['cust_select'] =  $arry2->select($cust_def,'','PHP_cust','select','');  	

			// creat weight unit combo box
		$op['select_unit'] = $arry2->select($APPAREL_UNIT,'','PHP_unit','select','');  	

//			// creat style type combo box
//			$style_def = $style_type->get_fields('style_type');   // ���X �ڦ����O
//			$op['style_type_select'] =  $arry2->select($style_def,'','PHP_style_type','select','');  	

			 $layout->assign($op);
			 $layout->display($TPL_SMPL_SEARCH);		    	    
				break;
		 }  // end if (!$PHP_dept_code).................. 

		 if (!$PHP_cust){	// �������O �L��ܫȤH�� [�D su �i�J��]			
				$op['msg'][] = "sorry! �Х���w �Ȥ� !";
				$op['dept_code'] = $PHP_dept_code;

				$allow = $admin->decode_perm(2,1);   // �]�w �s�W�R���v��
				if ($op['msg']){ $op['msg_YES'] = 1; } else { $op['msg_YES'] = "" ;}
				if ($allow['view'])		{	$op['view_flag'] = 1;} else {	$op['view_flag'] = "";}
				if ($allow['add'])		{	$op['add_flag'] = 1;} else {	$op['add_flag'] = "";}
				if ($allow['edit'])		{	$op['edit_flag'] = 1;} else {	$op['edit_flag'] = "";}
				if ($allow['del'])		{	$op['del_flag'] = 1;} else {	$op['del_flag'] = "";}


				$where_str =" WHERE dept ='$PHP_dept_code' ";
			$cust_def = $cust->get_fields('cust_s_name',$where_str);   // ���X �Ȥ�N��
			$op['cust_select'] =  $arry2->select($cust_def,'','PHP_cust','select','');  	

			// creat weight unit combo box
		$op['select_unit'] = $arry2->select($APPAREL_UNIT,'','PHP_unit','select','');  	

//			// creat season combo box
//			$season_def = $season->get_fields('season');   // ���X �u�`
//			$op['season_select'] =  $arry2->select($season_def,'','PHP_season','select','');  	

			// creat style type combo box
//			$style_def = $style_type->get_fields('style_type');   // ���X �ڦ����O
//			$op['style_type_select'] =  $arry2->select($style_def,'','PHP_style_type','select','');  	

			 $layout->assign($op);
			 $layout->display($TPL_SMPL_SEARCH);		    	    
				break;
		 } // end if (!$PHP_cust).................. 

			 // dept �� cust �Ҧ���J�� ................

			   $where_str = " WHERE (cust_s_name='".$PHP_cust."' AND dept='".$PHP_dept_code."') ";
				$s1 = $cust->search(0,$where_str);  // �d�ݿ�w���Ȥ�γ����O�_���Ĭ�...........
		 if ($s1['record_NONE']){
				$op['msg'][] = "sorry! �z��w���Ȥ� ���ݩ��w�������C";
				$op['dept_code'] = $PHP_dept_code;

				$allow = $admin->decode_perm(2,1);   // �]�w �s�W�R���v��
				if ($op['msg']){ $op['msg_YES'] = 1; } else { $op['msg_YES'] = "" ;}
				if ($allow['view'])		{	$op['view_flag'] = 1;} else {	$op['view_flag'] = "";}
				if ($allow['add'])		{	$op['add_flag'] = 1;} else {	$op['add_flag'] = "";}
				if ($allow['edit'])		{	$op['edit_flag'] = 1;} else {	$op['edit_flag'] = "";}
				if ($allow['del'])		{	$op['del_flag'] = 1;} else {	$op['del_flag'] = "";}

			// creat cust combo box
			$op['year_select'] = $arry2->select($YEAR_WORK,'','PHP_year','select','');  	

			$where_str =" WHERE dept ='$PHP_dept_code' ";
			$cust_def = $cust->get_fields('cust_s_name',$where_str);   // ���X �Ȥ�N��
			$op['cust_select'] =  $arry2->select($cust_def,'','PHP_cust','select','');  	

			// creat weight unit combo box
		$op['select_unit'] = $arry2->select($APPAREL_UNIT,'','PHP_unit','select','');  	

			// creat season combo box
//			$season_def = $season->get_fields('season');   // ���X �u�`
//			$op['season_select'] =  $arry2->select($season_def,'','PHP_season','select','');  	

			// creat style type combo box
//			$style_def = $style_type->get_fields('style_type');   // ���X �ڦ����O
//			$op['style_type_select'] =  $arry2->select($style_def,'','PHP_style_type','select','');  	

			 $layout->assign($op);
			 $layout->display($TPL_SMPL_SEARCH);		    	    
				break;

		 } // �����ΫȤᤣ�t�X�ɵ���

				$op['msg']= $smpl->msg->get(2);
				$op['smpl']['cust'] = $PHP_cust;
			$allow = $admin->decode_perm(2,1);   // �]�w �s�W�R���v��
			if ($op['msg']){ $op['msg_YES'] = 1; } else { $op['msg_YES'] = "" ;}
			if ($allow['view'])		{	$op['view_flag'] = 1;} else {	$op['view_flag'] = "";}
			if ($allow['add'])		{	$op['add_flag'] = 1;} else {	$op['add_flag'] = "";}
			if ($allow['edit'])		{	$op['edit_flag'] = 1;} else {	$op['edit_flag'] = "";}
			if ($allow['del'])		{	$op['del_flag'] = 1;} else {	$op['del_flag'] = "";}

			// ���X�~�X...
			$dt = decode_date(1);
			$year_code = substr($dt['year'],-2);

			// ���X�ﶵ��� �ζǤJ���Ѽ�
		$op['smpl']['dept']	 = $PHP_dept_code;
			// pre  �˥��s��....
		$op['smpl']['style_precode'] = "S".$PHP_dept_code.".....";
			// creat cust combo box
			$op['year_select'] = $arry2->select($YEAR_WORK,'','PHP_year','select','');  	

			$where_str =" WHERE dept ='$PHP_dept_code' ";
			$cust_def = $cust->get_fields('cust_s_name',$where_str);   // ���X �Ȥ�N��
			$op['cust_select'] =  $arry2->select($cust_def,'','PHP_cust','select','');  	

			// creat weight unit combo box
		$op['select_unit'] = $arry2->select($APPAREL_UNIT,'','PHP_unit','select','');  	

			// creat etd date combo box
		$op['etd_yr'] = $arry2->select($YEAR_WORK,'','PHP_etd_yr','select','');  	
		$op['etd_mn'] = $arry2->select($MONTH_WORK,'','PHP_etd_mn','select','');  	
		$op['etd_dy'] = $arry2->select($DAY_WORK,'','PHP_etd_dy','select','');  	

			// creat patt received date combo box
		$op['patt_rcvd_yr'] = $arry2->select($YEAR_WORK,'','PHP_patt_rcvd_yr','select','');  	
		$op['patt_rcvd_mn'] = $arry2->select($MONTH_WORK,'','PHP_patt_rcvd_mn','select','');  	
		$op['patt_rcvd_dy'] = $arry2->select($DAY_WORK,'','PHP_patt_rcvd_dy','select','');  	

			// creat season combo box
//			$season_def = $season->get_fields('season');   // ���X �u�`
//			$op['season_select'] =  $arry2->select($season_def,'','PHP_season','select','');  	

			// creat sample type combo box
//			$sample_def = $smpl_type->get_fields('smpl_type');   // ���X �˥����O
//			$op['smpl_type_select'] =  $arry2->select($sample_def,'','PHP_smpl_type','select','');  	

			// creat style type combo box
//			$style_def = $style_type->get_fields('style_type');   // ���X �ڦ����O
//			$op['style_type_select'] =  $arry2->select($style_def,'','PHP_style_type','select','');  	
			
			// creat size type combo box
//			$size_def = $size_type->get_fields('size_type');   // ���X �ڦ����O
//			$op['size_type_select'] =  $arry2->select($size_def,'','PHP_size_type','select','');  	

//			$op['size_scale_select'] = $arry2->select($SIZE_SCALE,'','PHP_size_scale','select','');  	

		$layout->assign($op);
		$layout->display($TPL_AG_ADD);		    	    
	break;



















//-------------------------------------------------------------------------------------
	case "fabric_excel":

		if(!$admin->is_power(9,1,"view")){ 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$parm = array(	"art_code"		=>  $PHP_art_code,
						"name"			=>  $PHP_name,
						"cat"			=>	$PHP_cat,
						"content"		=>	$PHP_content,
						"supl"			=>	$PHP_supl,
						"supl_ref"		=>	$PHP_supl_ref
				);

		if (!$fb = $fabric->search(1,'',3000)) {  // 2005/05/16 �[�J�ĤT�ӰѼ� ���ܷj�M�j�p
			$op['msg']= $fabric->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$a = count($fb['fabric']);
		//echo "<br> count number.............".$a;

	
	
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
	  HeaderingExcel('list.xls');
	 
	  // Creating a workbook
	  $workbook = new Workbook("-");

	  // Creating the first worksheet
	  $worksheet1 =& $workbook->add_worksheet('fabric');

	// �g�J title

	  // Format for the headings
	  $formatot =& $workbook->add_format();
	  $formatot->set_size(10);
	  $formatot->set_align('center');
	  $formatot->set_color('white');
	  $formatot->set_pattern();
	  $formatot->set_fg_color('navy');

	  $worksheet1->set_column(0,0,15);
	  $worksheet1->set_column(0,1,10);
	  $worksheet1->set_column(0,2,20);
	  $worksheet1->set_column(0,3,10);
	  $worksheet1->set_column(0,4,10);

	  $worksheet1->set_column(0,5,30);
	  $worksheet1->set_column(0,6,10);
	  $worksheet1->set_column(0,7,20);
	  $worksheet1->set_column(0,8,15);
	  $worksheet1->set_column(0,9,15);
	  $worksheet1->set_column(0,10,10);
	  $worksheet1->set_column(0,11,15);
	  $worksheet1->set_column(0,12,15);
	  $worksheet1->set_column(0,13,10);
	  $worksheet1->set_column(0,14,30);

	  $worksheet1->write_string(1,0,"Artical",$formatot);
	  $worksheet1->write_string(1,1,"category",$formatot);
	  $worksheet1->write_string(1,2,"Art. name",$formatot);
	  $worksheet1->write_string(1,3,"suplier.",$formatot);
	  $worksheet1->write_string(1,4,"suplier ref.",$formatot);

	  $worksheet1->write_string(1,5,"content",$formatot);
	  $worksheet1->write_string(1,6,"construct",$formatot);
	  $worksheet1->write_string(1,7,"finish",$formatot);
	  $worksheet1->write_string(1,8,"width",$formatot);
	  $worksheet1->write_string(1,9,"weight",$formatot);
	  $worksheet1->write_string(1,10,"source",$formatot);
	  $worksheet1->write_string(1,11,"price",$formatot);
	  $worksheet1->write_string(1,12,"term",$formatot);
	  $worksheet1->write_string(1,13,"leadtime",$formatot);
	  $worksheet1->write_string(1,14,"remark",$formatot);




	for ($i=0;$i<sizeof($fb['fabric']);$i++){
		$art_code = $fb['fabric'][$i]['art_code'];
		$cat = $fb['fabric'][$i]['cat'];
		$supl = $fb['fabric'][$i]['supl'];
		$supl_ref = $fb['fabric'][$i]['supl_ref'];
		$name = $fb['fabric'][$i]['name'];
		$content = $fb['fabric'][$i]['content'];
		$construct = $fb['fabric'][$i]['construct'];
		$finish = $fb['fabric'][$i]['finish'];
		$width = $fb['fabric'][$i]['width'].' '.$fb['fabric'][$i]['width_unit'];
		$weight = $fb['fabric'][$i]['weight'].' '.$fb['fabric'][$i]['unit_wt'];
		$construct = $fb['fabric'][$i]['construct'];
		$co = $fb['fabric'][$i]['co'];
		if ($fb['fabric'][$i]['price']){
			$price = $fb['fabric'][$i]['currency'].' '.$fb['fabric'][$i]['price'].'/'.$fb['fabric'][$i]['unit_price'];
		}else{
			$price = '';
		}
		$term = $fb['fabric'][$i]['term'].' '.$fb['fabric'][$i]['location'];
		if ($fb['fabric'][$i]['leadtime']){
			$leadtime = $fb['fabric'][$i]['leadtime'].' days';
		}else{
			$leadtime = '';
		}
		$remark = $fb['fabric'][$i]['remark'];


	  $worksheet1->write_string($i+2,0,$art_code);
	  $worksheet1->write_string($i+2,1,$cat);
	  $worksheet1->write_string($i+2,2,$name);
	  $worksheet1->write_string($i+2,3,$supl);
	  $worksheet1->write_string($i+2,4,$supl_ref);
	  $worksheet1->write_string($i+2,5,$content);
	  $worksheet1->write_string($i+2,6,$construct);
	  $worksheet1->write_string($i+2,7,$finish);
	  $worksheet1->write_string($i+2,8,$width);
	  $worksheet1->write_string($i+2,9,$weight);
	  $worksheet1->write_string($i+2,10,$co);
	  $worksheet1->write_string($i+2,11,$price);
	  $worksheet1->write_string($i+2,12,$term);
	  $worksheet1->write_string($i+2,13,$leadtime);
	  $worksheet1->write_string($i+2,14,$remark);

	
	
	}


  $workbook->close();


	break;


//-------------------------------------------------------------------------------------
	case "fabric_print_search":

		if(!$admin->is_power(9,1,"view")){ 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

			// creat cust combo box
		$op['select_cat'] = $arry2->select($FABRIC_CAT,'','PHP_cat','select','');  	
			// create combo box for vendor fields.....
			$where_str =" WHERE supl_cat ='�D��' ";
			$supl_def = $supl->get_fields('supl_s_name',$where_str);   // ���X ���������O�N��
		$op['select_supl'] = $arry2->select($supl_def,'','PHP_supl','select','');  	

		$op['msg']= $fabric->msg->get(2);

		$allow = $admin->decode_perm(9,1);   // �]�w �s�W�R���v��
		if ($op['msg']){ $op['msg_YES'] = 1; } else { $op['msg_YES'] = "" ;}
		if ($allow['view'])		{	$op['view_flag'] = 1;} else {	$op['view_flag'] = "";}
		if ($allow['add'])		{	$op['add_flag'] = 1;} else {	$op['add_flag'] = "";}
		if ($allow['edit'])		{	$op['edit_flag'] = 1;} else {	$op['edit_flag'] = "";}
		if ($allow['del'])		{	$op['del_flag'] = 1;} else {	$op['del_flag'] = "";}
		$layout->assign($op);
		$layout->display($TPL_FABRIC_PRINT_SEARCH);		    	    

	break;

//=======================================================
    case "do_fabric_print_search":

		if(!$admin->is_power(9,1,"view")){ 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$parm = array(	"art_code"		=>  $PHP_art_code,
						"name"			=>  $PHP_name,
						"cat"			=>	$PHP_cat,
						"content"		=>	$PHP_content,
						"finish"		=>	$PHP_finish,
						"supl"			=>	$PHP_supl,
						"supl_ref"		=>	$PHP_supl_ref
				);
		if (!$op = $fabric->search(1)) {
			$op['msg']= $fabric->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

			$op['msg']= $fabric->msg->get(2);
			$op['cgi']= $parm;

		$allow = $admin->decode_perm(9,1);   // �]�w �s�W�R���v��
		if ($op['msg']){ $op['msg_YES'] = 1; } else { $op['msg_YES'] = "" ;}
		if ($allow['view'])		{	$op['view_flag'] = 1;} else {	$op['view_flag'] = "";}
		if ($allow['add'])		{	$op['add_flag'] = 1;} else {	$op['add_flag'] = "";}
		if ($allow['edit'])		{	$op['edit_flag'] = 1;} else {	$op['edit_flag'] = "";}
		if ($allow['del'])		{	$op['del_flag'] = 1;} else {	$op['del_flag'] = "";}
//$op['NEXT_page'] = 1;
//$op['PREV_page'] = 1;

			$layout->assign($op);
			$layout->display($TPL_FABRIC_PRINT_LIST);  
		break;

//=======================================================
    case "fabric_label_print":
		if(!$admin->is_power(9,1,"view")){ 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$a_code = array();
		$a_copy = array();
		$records = array();


		// �����e�J $labels, $numbers, $rows
		$a_code = explode(',',$labels);
		$a_copy = explode(',',$numbers);
		
		for($i=0;$i<$rows;$i++){
			if($a_copy[$i]){
				
				//-------------------- �N ���� ��� �Ѹ�Ʈw���X ------------------
					if (!$fb = $fabric->get($a_code[$i])) {  //���X�ӵ� ���ưO��
						$op['msg'] = $fabric->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
					}
				for($j=0;$j<$a_copy[$i];$j++){
					$records[] = array(		$fb['art_code'],
											$fb['content'],
											$fb['construct'],
											$fb['width'].' '.$fb['width_unit'],
											$fb['weight'].' '.$fb['unit_wt'],
											$fb['finish']	);

				}  // end for $j
			}  // end if

		}  // end for $i

		$copies = count($records);



	//  �}�l �]�w pdf ��
	include($config['root_dir']."/lib/class.pdf_fabric.php");

	$pdf=new PDF_fabric();

	$pdf->SetAutoPageBreak('auto',0);
	$pdf->Open();
	$pdf->AddPage();

	$pdf->SetMargins(0,0,0);

	$w1 = 10;
	$h1 = 5;
	$w2 = 30;
	$h2 = 5;

	$x =0;
	$y =0;

	$title = array('Art. No:','Composition:','Construction:','Width:','Weight:','Finished:');
//	$field = array('FK05W-0610','100% POLYESTER','213X106/75DX150D','61 inch','230 g/yd.','MICROFIBER,SANDING,W/R');

	$left = '1';
	$y = 0;

	for ($i=0;$i<$copies;$i++){
			 // ��u
			 $pdf->SetDrawColor(200,200,200);
			 $pdf->Line(105, 0, 105, 297); 
			 $pdf->Line(0, 37.125, 210, 37.125); 
			 $pdf->Line(0, 74.250, 210, 74.250); 
			 $pdf->Line(0, 111.375, 210, 111.375); 
			 $pdf->Line(0, 148.5, 210, 148.5); 
			 $pdf->Line(0, 185.625, 210, 185.625); 
			 $pdf->Line(0, 222.750, 210, 222.750); 
			 $pdf->Line(0, 259.875, 210, 259.875); 

		 if ($left=='1'){
			$x =0;
			$pdf->record($x,$y,$w1,$h1,$w2,$h2,$title,$records[$i]);
			$left = '';
		 } else {
			$x =105;
			$pdf->record($x,$y,$w1,$h1,$w2,$h2,$title,$records[$i]);
			$left = 1;
			$y= $y + 37.125;
		 }
		 if($y == '297'){ 
			$pdf->AddPage(); 

			$y = 0;
		 }

	}

	$pdf->Output();

	
break;



//=======================================================
    case "fabric_label":
		if(!$admin->is_power(9,1,"view")){ 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

	$records = array();
	
	$num_art = count($PHP_print_id);
	$copies = 0;

	for ($i=0;$i<$num_art;$i++){
		if ($PHP_print_copy[$PHP_print_id[$i]]){
		
		//-------------------- �N ���� ��� �Ѹ�Ʈw���X ------------------
			if (!$fb = $fabric->get($PHP_print_id[$i])) {  //���X�ӵ� ���ưO��
				$op['msg'] = $fabric->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			for ($j=0;$j<($PHP_print_copy[$PHP_print_id[$i]]);$j++){
				$records[] = array(	$fb['art_code'],
									$fb['content'],
									$fb['construct'],
									$fb['width'].' '.$fb['width_unit'],
									$fb['weight'].' '.$fb['unit_wt'],
									$fb['remark']	);

			}


			$copies = $copies + $PHP_print_copy[$PHP_print_id[$i]];
		}
	}


	//  �}�l �]�w pdf ��
	include_once($config['root_dir']."/lib/class.pdf_fabric.php");

	$pdf=new PDF_fabric();

	$pdf->SetAutoPageBreak('auto',0);
	$pdf->Open();
	$pdf->AddPage();

	$pdf->SetMargins(0,0,0);

	$w1 = 10;
	$h1 = 5;
	$w2 = 30;
	$h2 = 5;

	$x =0;
	$y =0;

	$title = array('Art. No:','Composition:','Construction:','Width:','Weight:','Remark:');
	$field = array('FK05W-0610','100% POLYESTER','213X106/75DX150D','61 inch','230 g/yd.','MICROFIBER,SANDING,W/R');

	$times = 39;
	$left = '1';
	$y = 0;

	for ($i=0;$i<$copies;$i++){
			 // ��u
			 $pdf->SetDrawColor(200,200,200);
			 $pdf->Line(105, 0, 105, 297); 
			 $pdf->Line(0, 37.125, 210, 37.125); 
			 $pdf->Line(0, 74.250, 210, 74.250); 
			 $pdf->Line(0, 111.375, 210, 111.375); 
			 $pdf->Line(0, 148.5, 210, 148.5); 
			 $pdf->Line(0, 185.625, 210, 185.625); 
			 $pdf->Line(0, 222.750, 210, 222.750); 
			 $pdf->Line(0, 259.875, 210, 259.875); 

		 if ($left=='1'){
			$x =0;
			$pdf->record($x,$y,$w1,$h1,$w2,$h2,$title,$records[$i]);
			$left = '';
		 } else {
			$x =105;
			$pdf->record($x,$y,$w1,$h1,$w2,$h2,$title,$records[$i]);
			$left = 1;
			$y= $y + 37.125;
		 }
		 if($y == '297'){ 
			$pdf->AddPage(); 

			$y = 0;
		 }

	}

	$pdf->Output();

	
break;





//-------------------------------------------------------------------------------------
    case "test2":

//  �}�l �]�w pdf ��
include_once($config['root_dir']."/lib/class.pdf_fabric.php");
//$pdf=new PDF_fabric('P','mm','A4');
$pdf=new PDF_fabric();
//$pdf->AddBig5Font();
$pdf->SetAutoPageBreak('auto',0);
$pdf->Open();
$pdf->AddPage();
//$pdf->SetFont('Arial','B',14);

	$pdf->SetMargins(0,0,0);

$w1 = 10;
$h1 = 5;
$w2 = 30;
$h2 = 5;

$x =0;
$y =0;

$title = array('Art. No:','Composition:','Construction:','Width:','Weight:','Remark:');
//$field = array('FK05W-0610','100% POLYESTER','213X106/75DX150D','61 inch','230 g/yd.','MICROFIBER,SANDING,W/R');

	$times = 39;
	$left = '1';
	$y = 0;

for ($i=0;$i<$copies;$i++){
		 // ��u
		 $pdf->SetDrawColor(200,200,200);
		 $pdf->Line(105, 0, 105, 297); 
		 $pdf->Line(0, 37.125, 210, 37.125); 
		 $pdf->Line(0, 74.250, 210, 74.250); 
		 $pdf->Line(0, 111.375, 210, 111.375); 
		 $pdf->Line(0, 148.5, 210, 148.5); 
		 $pdf->Line(0, 185.625, 210, 185.625); 
		 $pdf->Line(0, 222.750, 210, 222.750); 
		 $pdf->Line(0, 259.875, 210, 259.875); 

	 if ($left=='1'){
		$x =0;
		$pdf->record($x,$y,$w1,$h1,$w2,$h2,$title,$records[$i]);
		$left = '';
	 } else {
		$x =105;
		$pdf->record($x,$y,$w1,$h1,$w2,$h2,$title,$records[$i]);
		$left = 1;
		$y= $y + 37.125;
	 }
	 if($y == '297'){ 
		$pdf->AddPage(); 

		$y = 0;
	 }

}

$pdf->Output();

	break;
	




//-------------------------------------------------------------------------------------
//		BOM  �Ͳ��ή�  [4, 1 ]
//-------------------------------------------------------------------------------------
    case "bom":

		check_authority(3,5,"view");

		$where_str = $manager = $dept_id = '';		 
		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];   // �P�w�i�J����������
		$team = $GLOBALS['SCACHE']['ADMIN']['team_id'];  // �P�w�i�J����������(team)
		$sales_dept_ary = get_sales_dept();// ���X �~�Ȫ����� [���tK0] ------
		for ($i=0; $i<count($sales_dept_ary);$i++){
			if($user_dept == $sales_dept_ary[$i]){

				// �p�G�O�~�ȳ��i�J �hdept_code ���w�ӷ~�ȳ�---
				$dept_id = $sales_dept_ary[$i];  
			}
		}
		$op['dept_id'] = $dept_id;
		if (!$dept_id || $team<>'MD') {    // ���O�~�ȳ��H[�]���t K0 ���H ]�i�J��
			$op['manager_flag'] = 1;
			$manager_v = 1;
			//�~�ȳ��� select���
			$op['dept_id'] = $arry2->select($sales_dept_ary,"","PHP_dept_code","select",""); 
 
		} else {
			$where_str = " WHERE dept = '".$dept_id."' ";	
					
		}

								
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

		page_display($op, 3,5, $TPL_BOM_SEARCH);		    	    
		break;
//=======================================================
    case "bom_print":   //  .......

		if(!$admin->is_power(3,5,"view")){ 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		//-------------------- �N �s�y�O ��� �Ѹ�Ʈw���X ------------------
		//  wi �D��
		if(!$wi = $wi->get(0,$PHP_id)){    //���X�ӵ� �s�y�O�O��
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}
		//  smpl �˥���
		if(!$smpl = $order->get($wi['smpl_id'])){
					$op['msg']= $order->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);     
					break;
		}
		//-------------------- wi_qty �ƶq�� -------------------------------
			$where_str = " WHERE wi_id='".$wi['id']."' ";
		$T_wiqty = $wiqty->search(1,$where_str);

		$num_colors = count($T_wiqty);
		$op['num_colors'] = $num_colors;
		// ���X size_scale ----------------------------------------------
		$size_A = $size_des->get($smpl['size']);
		$smpl['size_scale']=$size_A['size_scale'];
			//????????????????????????????????????? �`�N ���ɨS�� size type ��Ʈ�??????
		$sizing = explode(",", $size_A['size']);

			// ���X size table-------------------------------------------
		$reply = get_colorway_qty($T_wiqty,$sizing);

		$op['total'] = $reply['total'];   // �`���
		$data = $reply['data'];

		$colorway_name = $reply['colorway'];
		$colorway_qty = $reply['colorway_qty'];

		//-----------------------------------------------------------------

		// �ۤ���URL�M�w -----------------------------------------------------------------
			$style_dir	= "./picture/";  
			$no_img		= "./images/graydot.jpg";
			if(file_exists($style_dir.$wi['style_code'].".jpg")){
				$wi['pic_url'] = $style_dir.$wi['style_code'].".jpg";
			} else {
				$wi['pic_url'] = $no_img;
			}
		
		//  ���X�D�ƥήưO�� --------------------------------------------------------------
		 $op['lots_NONE']= '';
		$where_str = " WHERE smpl_code = '".$wi['style_code']."' ";
		$lots_used['lots_use'] = $smpl_lots->search(0,$where_str);  //���X�ӵ� �˥��D�ưO��

		if (!is_array($lots_used['lots_use'])) {
			$op['msg'] = $smpl_lots->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$num_lots_used = count($lots_used['lots_use']);
		if (!$num_lots_used){	$op['lots_NONE'] = "1";		
		}else{
			for ($i=0; $i<sizeof($lots_used['lots_use']); $i++)
			{
				$lots_det[$i][0]		= $lots_used['lots_use'][$i]['lots_code'];
				$lots_det[$i][1]		= $lots_used['lots_use'][$i]['lots_name'];
				$lots_det[$i][2]		= $lots_used['lots_use'][$i]['use_for'];
				
			}

		}

		//  ���X�ƮƥήưO�� --------------------------------------------------------------
		 $op['acc_NONE']= '';
		$where_str = " WHERE smpl_code = '".$wi['style_code']."' ";
		$acc_used['acc_use'] = $smpl_acc->search(0,$where_str);  //���X�ӵ� �˥��D�ưO��

		if (!is_array($acc_used['acc_use'])) {     
			$op['msg'] = $smpl_acc->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$num_acc_used = count($acc_used['acc_use']);
		if (!$num_acc_used){	$op['acc_NONE'] = "1";		
		}else{
			for ($i=0; $i<sizeof($acc_used['acc_use']); $i++)
			{
				$acc_det[$i][0]		= $acc_used['acc_use'][$i]['acc_code'];
				$acc_det[$i][1]		= $acc_used['acc_use'][$i]['acc_name'];
				$acc_det[$i][2]		= $acc_used['acc_use'][$i]['use_for'];
				
			}
		
		}
		
		
		

		//  ���X  BOM  �D�ưO�� --------------------------------------------------------------
		 $op['bom_lots_NONE']= '';
		$where_str = " WHERE wi_id = '".$wi['id']."' ";
		$bom_lots = $bom->search_lots(0,$where_str);  //���X�ӵ� bom ��ALL�D�ưO��

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
		$where_str = " WHERE wi_id = '".$wi['id']."' ";
		$bom_acc = $bom->search_acc(0,$where_str);  //���X�ӵ� bom ��ALL�ƮưO��

		if (!is_array($bom_acc['acc'])) {
			$op['msg'] = $bom->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$num_bom_acc = count($bom_acc['acc']);
		if (!$num_bom_acc){	$op['bom_acc_NONE'] = "1";		}

		// --------------- �D�� ------------ ���X layout  array....
			$data_m = array();
		if (!$op['lots_NONE']){   // �˥����D�ƥήưO��---

		  for ($i=0;$i<$num_lots_used;$i++){   // �� �D�ƥήưO���v�� loop
				$no_found_flag = 1;  // �w�] bom���䤣���ƪ��X��
			for ($k=0;$k<count($bom_lots['lots']);$k++){  // �� bom �D�ưO�� �v�� loop
				$T_bom = array();
				if ($bom_lots['lots'][$k]['lots_used_id'] == $lots_used['lots_use'][$i]['id'] ){
					$no_found_flag = "";  // �X�Ч���

					$T_bom[0]	= $lots_used['lots_use'][$i]['lots_code'];
					$T_bom[1]	= $lots_used['lots_use'][$i]['use_for'];
					$T_bom[2]	= $lots_used['lots_use'][$i]['est_1'].' '.$lots_used['lots_use'][$i]['unit'];
					$T_bom[3]	= $bom_lots['lots'][$k]['color'];
					$T_bom[4]	= '';  // layout �����u �]���[�J�@�Ӫŭ�

						$T_qty = explode(",", $bom_lots['lots'][$k]['qty']);
					for ($p=0;$p<$num_colors;$p++){
						if ($T_qty[$p]==0){	$T_qty[$p] = " ";	}
						$T_bom[5 + $p] = $T_qty[$p];
					}
					$T_bom[$p+5]			= '';
					$T_bom[$p+6]			= array_sum($T_qty)." ".$lots_used['lots_use'][$i]['unit'];
					$data_m[]	= $T_bom;
				}   // BOM �ɤ��� �D��id ���� �ή��ɪ�id ����

			}  // end for K... bom�ɤ��D�� �v�� ����
			if ($no_found_flag){  // �� bom�ɤ��L�o���ήƪ�id��

				$T_bom['lots_code']		= $lots_used['lots_use'][$i]['lots_code'];
				$T_bom['est_1']			= $lots_used['lots_use'][$i]['est_1'];
				$T_bom['unit']			= $lots_used['lots_use'][$i]['unit'];
				$T_bom['use_for']			= $lots_used['lots_use'][$i]['use_for'];

				$data_m[]		= $T_bom;
			}
		  }  // end for �� �D�ƥήưO���v�� loop

		}   // end---- if (!$op['lots_NONE']) 
		// ---------------------- end BOM data layout [�D��] ----------------------------

		// --------------- �Ʈ� ------------ ���X $bom_acc_list[]  array....
			$data_a = array();
		if (!$op['acc_NONE']){   // �˥����ƮƥήưO��---
			unset($T_bom);
			$T_bom = array();
		  for ($i=0;$i<$num_acc_used;$i++){   // �� �ƮƥήưO���v�� loop
				$no_found_flag = 1;  // �w�] bom���䤣���ƪ��X��
			for ($k=0;$k<count($bom_acc['acc']);$k++){  // �� bom �ƮưO�� �v�� loop
				$T_bom = array();
				if ($bom_acc['acc'][$k]['acc_used_id'] == $acc_used['acc_use'][$i]['id'] ){
					$no_found_flag = "";  // �X�Ч���

					$T_bom[0]		= $acc_used['acc_use'][$i]['acc_code'];
					$T_bom[1]		= $acc_used['acc_use'][$i]['use_for'];
					$T_bom[2]		= $acc_used['acc_use'][$i]['est_1'].' '.$acc_used['acc_use'][$i]['unit'];
					$T_bom[3]		= $bom_acc['acc'][$k]['color'];
					$T_bom[4]	= '';  // layout �����u �]���[�J�@�Ӫŭ�

						$T_qty = explode(",", $bom_acc['acc'][$k]['qty']);
					for ($p=0;$p<$num_colors;$p++){
						if ($T_qty[$p]==0){	$T_qty[$p] = " ";	}
						$T_bom[5 + $p] = $T_qty[$p];
					}
					$T_bom[$p+5]			= '';
					$T_bom[$p+6]			= array_sum($T_qty)." ".$acc_used['acc_use'][$i]['unit'];
					$data_a[]	= $T_bom;

				}   // BOM �ɤ��� �Ʈ�id ���� �ή��ɪ�id ����

			}  // end for K... bom�ɤ��Ʈ� �v�� ����

			if ($no_found_flag){  // �� bom�ɤ��L�o���ήƪ�id��

				if (!$num_colors){	$num_span = 1;	}else{ $num_span = $num_colors; }

				$T_bom['acc_code']		= $acc_used['acc_use'][$i]['acc_code'];
				$T_bom['est_1']				= $acc_used['acc_use'][$i]['est_1'];
				$T_bom['unit']				= $acc_used['acc_use'][$i]['unit'];
				$T_bom['use_for']			= $acc_used['acc_use'][$i]['use_for'];

				$data_a[]			= $T_bom;
			}
		  }  // end for �� �D�ƥήưO���v�� loop

		}   // end---- if (!$op['acc_NONE']) 
		// ---------------------- end BOM data layout [�Ʈ�] ----------------------------
		// ---------------------- end BOM data layout table ----------------------------

//---------------------------------- ��Ʈw�@�~���� ------------------------------

include_once($config['root_dir']."/lib/class.pdf_bom.php");

$print_title = "Bill Of Material";
$wi['bcfm_date']=substr($wi['bcfm_date'],0,10);
$print_title2 = "VER.".$wi['bom_rev']."  on  ".$wi['bcfm_date'];
$creator = $wi['bcfm_user'];

$pdf=new PDF_bom();
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);

$parm=array( 'PHP_id'		=>	$PHP_id,
			 'prt_ord'				=>	'Order #',
			 'cust'						=>	$smpl['cust_iname'],
			 'ref'						=>	$smpl['ref'],
			 'dept'						=>	$smpl['dept'],
			 'size_scale'			=>	$smpl['size_scale'],
			 'style'					=>	$smpl['style'],
			 'qty'						=>	$op['total'],
			 'unit'						=>	$wi['unit'],
			 'etd'						=>	$wi['etd'],
			 'pic_url'				=>	$wi['pic_url'],
			 );

$pdf->hend_title($parm);			 
		$Y = $pdf->getY();
//		$pdf->setx(20);
		$img_size = GetImageSize($wi['pic_url']);
		
		if ($img_size[0] > $img_size[1])
		{
			$pdf->Image($wi['pic_url'],10,28,40,0);
		}else{
			$pdf->Image($wi['pic_url'],10,28,0,40);
		}
		$pdf->ln();
		$Y = $pdf->getY();
		$X = $pdf->getX();
// �]�w colorway���ƶq�� - ��쪺�e  [size breakdown]
// �� 80~200 �O�d 30�� colorway���e �䥦 90���t���ƶq��
	$num_siz = count($sizing);
	$w_qty = intval(155/ ($num_siz+1));
	$w = array('40');  // �Ĥ@�欰 30�e
	for ($i=0;$i<=$num_siz;$i++){
		array_push($w, $w_qty);
	}

	$header = array_merge(array('Colorway') , $sizing);	
	$table_title = "Size Breakdown";
	$R = 100;
	$G = 85;
	$B = 85;
	if (count($sizing)){
		$pdf->Table_1(10,70,$header,$data,$w,$table_title,$R,$G,$B,$size_A['base_size']);
	}else{
		$pdf->Cell(10,70,'there is no sizing data exist !',1);
	}
		$Y =$pdf->getY();
		// �w�q �ϥH�U���y�� 
	//	$pdf->SetXY($X,$Y);
		
if (sizeof($data) < 3)
{
	$ll=0;
}else{
	$ll=sizeof($data)-3;
}		
				
	// �D��
		$pdf->SetFont('Big5','',10);

	// �D�Ʃ��Y
if ($num_colors && $op['lots_NONE'] <> 1){
	$header1 = array('Fabric #','Placement','Consump.','color/cat.','');
	$header2 = $colorway_name;
	for ($i=0;$i<count($colorway_qty);$i++){
		$header3[$i] = $colorway_qty[$i]." ".$wi['unit'];
	}


	$w1 =  array(18,55,18,24,0.5);
	$w2 = array();
	$w_color = intval(65/$num_colors);
	for ($i=0;$i<$num_colors;$i++){
		array_push($w2, $w_color);
	}
	$w3 = array(25);
	$title = 'Fabric usage';
	$data = $data_m;
	$X=$X+10;	
	$ll=$pdf->Table_2(10,$Y+5,$header1,$header2,$header3,$data,$w1,$w2,$w3,$title,$R,$G,$B,$ll);

//	$header1 = array('Fabric #','Fabric Item','Placement');
//	$ll=$pdf->mat_det($X,$header1,$lots_det,$ll);

} // if $num_colors....

//		$pdf->ln();
		$Y = $pdf->getY();
		$X = $pdf->getX();
		$pdf->SetXY($X,$Y);


	// �Ʈ� -------------------------
		$pdf->SetFont('Big5','',10);

	// �ƮƩ��Y
if ($num_colors && $op['acc_NONE'] <> 1){
	$header1 = array('Acc. #','Placement','Consump.','color/cat.','');
	$header2 = $colorway_name;
	for ($i=0;$i<count($colorway_qty);$i++){
		$header3[$i] = $colorway_qty[$i]." ".$wi['unit'];
	}

	$w1 =  array(18,55,18,24,0.5);
	$w2 = array();
	$w_color = intval(65/$num_colors);
	for ($i=0;$i<$num_colors;$i++){
		array_push($w2, $w_color);
	}
	$w3 = array(25);
	$title = 'Accessory usage';
	$data = $data_a;
	$X=$X+10;	
	$ll=$pdf->Table_2(10,$Y+10,$header1,$header2,$header3,$data,$w1,$w2,$w3,$title,$R,$G,$B,$ll);

//	$header1 = array('ACCESSORY #','ACCESSORY Item','Placement');
//	$ll=$pdf->mat_det($X,$header1,$acc_det,$ll);

} // if $num_colors....

$name=$wi['wi_num'].'_bom.pdf';
$pdf->Output($name,'D');

break;

	
	
//=======================================================
	
	case "bom_search_wi":   //  ����X�s�y�O.......

		check_authority(3,5,"view");

			if (!$op = $wi->search(1,1)) {
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

		$op['msg']= $wi->msg->get(2);				
		$back_str="&PHP_dept_code=".$PHP_dept_code."&PHP_etdfsh=".$PHP_etdfsh."&PHP_cust=".$PHP_cust."&PHP_wi_num=".$PHP_wi_num."&PHP_etdstr=".$PHP_etdstr."&PHP_fty_sch=".$PHP_fty_sch;
		$op['back_str']=$back_str;

		page_display($op, 3,5, $TPL_BOM);
		break;


//=======================================================
	
	case "search_pa_fab":   //  ��XBOM�v�����ʪ��D�ƪ����ʳ�C��.......

		check_authority(3,5,"view");

			if (!$op = $apply->search_fab_bom($PHP_id,$PHP_num)) {
				$op['msg']= $wi->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
		page_display($op, 3,5, $TPL_BOM_PA_LIST);
		break;

//=======================================================
	
	case "search_pa_acc":   //  ��XBOM�v�����ʪ��D�ƪ����ʳ�C��.......

		check_authority(3,5,"view");

			if (!$op = $apply->search_acc_bom($PHP_id,$PHP_num)) {
				$op['msg']= $wi->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
		page_display($op, 3,5, $TPL_BOM_PA_LIST);
		break;
//=======================================================
    case "bom_add":

		check_authority(3,5,"add");
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

		//------------- wi_qty �ƶq�� ----------------------------------
			$where_str = " WHERE wi_id='".$op['wi']['id']."' ";
		$T_wiqty = $wiqty->search(1,$where_str);

		$num_colors = count($T_wiqty);
		$op['num_colors'] = $num_colors;
		// ���X size_scale ----------------------------------------------
		$size_A = $size_des->get($op['smpl']['size']);
		$op['smpl']['size_scale']=$size_A['size_scale'];
			//????????????????????????????????????? �`�N ���ɨS�� size type ��Ʈ�??????
		$sizing = explode(",", $size_A['size']);

			// ���X size table-------------------------------------------
		$reply = show_breakdown($T_wiqty,$sizing,$size_A['base_size']);
		$op['show_breakdown'] = $reply['html'];
		$op['total'] = $reply['total'];   // �`���
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

		if (!is_array($lots_used['lots_use'])) {
			$op['msg'] = $smpl_lots->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$num_lots_used = count($lots_used['lots_use']);
		if (!$num_lots_used){	$op['lots_NONE'] = "1";		}

		//  ���X�ƮƥήưO�� --------------------------------------------------------------
		 $op['acc_NONE']= '';
		$where_str = " WHERE smpl_code = '".$op['wi']['style_code']."' ";
		$acc_used['acc_use'] = $smpl_acc->search(0,$where_str);  //���X�ӵ� �˥��D�ưO��

		if (!is_array($acc_used['acc_use'])) {     
			$op['msg'] = $smpl_acc->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$num_acc_used = count($acc_used['acc_use']);
		if (!$num_acc_used){	$op['acc_NONE'] = "1";		}

		//  ���X  BOM  �D�ưO�� --------------------------------------------------------------
		 $op['bom_lots_NONE']= '';
		$where_str = " WHERE wi_id = '".$op['wi']['id']."' ";
		$bom_lots = $bom->search_lots(0,$where_str);  //���X�ӵ� bom ��ALL�D�ưO��
		$op['bom_lots'] = $bom_lots['lots'];

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
		$op['bom_acc'] = $bom_acc['acc'];

		if (!is_array($bom_acc['acc'])) {
			$op['msg'] = $bom->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$num_bom_acc = count($bom_acc['acc']);
		if (!$num_bom_acc){	$op['bom_acc_NONE'] = "1";		}

		//-------------------- ��z������� ---- for bom table TITLE------------------------------
		if ($num_colors){
			for ($i=0;$i<$num_colors;$i++){
				$op['color'][$i]['name'] = $T_wiqty[$i]['colorway'];
				$T_qty = explode(",", $T_wiqty[$i]['qty']);
				$op['color'][$i]['qty'] = array_sum($T_qty);
				$color_qty[$i] = array_sum($T_qty);
			}
		} else {
				$color_qty[] = 0;   // �קK error
				$op['colors_NONE'] = "1";
		}

	// --------------- �D�� ------------ ���X $bom_lots_list[]  array....
		$op['bom_lots_list'] = array();
	    if (!$op['lots_NONE']){   // �˥����D�ƥήưO��---
	      $op['bom_lots_list'] =bom_lots_edit($num_lots_used,$bom_lots['lots'],$lots_used['lots_use'],$num_colors, $PHP_id, $color_qty);
	    } 
	// ---------------------- end BOM data layout [�D��] ----------------------------

	// --------------- �Ʈ� ------------ ���X $bom_acc_list[]  array....
		$op['bom_acc_list'] = array();
		if (!$op['acc_NONE']){   // �˥����ƮƥήưO��---
	   		$op['bom_acc_list'] =bom_acc_edit($num_acc_used,$bom_acc['acc'],$acc_used['acc_use'],$num_colors, $PHP_id, $color_qty);	
		} 
	// ---------------------- end BOM data layout [�Ʈ�] ----------------------------

			// ��� ��i�� -----
			if (!$num_colors){	
				$op['total_fields'] = 7;   // BOM table���`����
			}else{
				$op['total_fields'] = 6 + $num_colors;   // BOM table���`����
			}
		// ---------------------- end BOM data layout table ----------------------------
		
	
	if (isset($PHP_dept_code))
	{
		$back_str="&PHP_sr_startno=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_etdfsh=".$PHP_etdfsh."&PHP_cust=".$PHP_cust."&PHP_wi_num=".$PHP_wi_num."&PHP_etdstr=".$PHP_etdstr."&PHP_fty_sch=".$PHP_fty_sch;
		$op['back_str']=$back_str;
		$back_str2="&PHP_sr=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_etdfsh=".$PHP_etdfsh."&PHP_cust=".$PHP_cust."&PHP_wi_num=".$PHP_wi_num."&PHP_etdstr=".$PHP_etdstr."&PHP_fty_sch=".$PHP_fty_sch;
		$op['back_str2']=$back_str2;
	}else{
		$op['back_str']=$PHP_back_str;
		$op['back_str2']=$PHP_back_str2;
	}

		page_display($op, 3,5, $TPL_BOM_ADD);  	    
		break;

//=======================================================
    case "do_bom_add":

		check_authority(3,5,"add");
		// �[�J bom ��
		$dt = decode_date(1);
		
		if ($PHP_mat=="lots"){
			$parm = array(	"wi_id"			=>	$PHP_wi_id,
							"color"			=>	$PHP_color,
							"lots_used_id"	=>	$PHP_mat_use_id,
							"qty"			=>	$PHP_qty,		
							"this_day"		=>	$dt['date']	
				);
			$f1 = $bom->add_lots($parm);   // �[�J
			$K = "Fabric";
		}elseif($PHP_mat=="acc"){
			$parm = array(	"wi_id"			=>	$PHP_wi_id,
							"color"			=>	$PHP_color,
							"acc_used_id"	=>	$PHP_mat_use_id,
							"qty"			=>	$PHP_qty,		
							"this_day"		=>	$dt['date']	
				);
			$f1 = $bom->add_acc($parm);   // �[�J
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


		//-------------------- �N BOM �s�y�O show out ------------------
		//  wi �D��
		if(!$op['wi'] = $wi->get($PHP_wi_id)){    //���X�ӵ� �s�y�O�O��
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

		$message = "Successfully add wi[ ".$op['wi']['wi_num']." ]  BOM ".$K." record Color =[ ".$PHP_color." ]";

		//------------- wi_qty �ƶq�� ----------------------------------
			$where_str = " WHERE wi_id='".$op['wi']['id']."' ";
		$T_wiqty = $wiqty->search(1,$where_str);

		$num_colors = count($T_wiqty);
		$op['num_colors'] = $num_colors;
		// ���X size_scale ----------------------------------------------
		$size_A = $size_des->get($op['smpl']['size']);
		$op['smpl']['size_scale']=$size_A['size_scale'];
			//????????????????????????????????????? �`�N ���ɨS�� size type ��Ʈ�??????
		$sizing = explode(",", $size_A['size']);

			// ���X size table-------------------------------------------
		$reply = show_breakdown($T_wiqty,$sizing,$size_A['base_size']);
		$op['show_breakdown'] = $reply['html'];
		$op['total'] = $reply['total'];   // �`���
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

		if (!is_array($lots_used['lots_use'])) {
			$op['msg'] = $smpl_lots->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$num_lots_used = count($lots_used['lots_use']);
		if (!$num_lots_used){	$op['lots_NONE'] = "1";		}

		//  ���X�ƮƥήưO�� --------------------------------------------------------------
		 $op['acc_NONE']= '';
		$where_str = " WHERE smpl_code = '".$op['wi']['style_code']."' ";
		$acc_used['acc_use'] = $smpl_acc->search(0,$where_str);  //���X�ӵ� �˥��D�ưO��

		if (!is_array($acc_used['acc_use'])) {     
			$op['msg'] = $smpl_acc->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$num_acc_used = count($acc_used['acc_use']);
		if (!$num_acc_used){	$op['acc_NONE'] = "1";		}

		//  ���X  BOM  �D�ưO�� --------------------------------------------------------------
		 $op['bom_lots_NONE']= '';
		$where_str = " WHERE wi_id = '".$op['wi']['id']."' ";
		$bom_lots = $bom->search_lots(0,$where_str);  //���X�ӵ� bom ��ALL�D�ưO��
		$op['bom_lots'] = $bom_lots['lots'];

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
		$op['bom_acc'] = $bom_acc['acc'];

		if (!is_array($bom_acc['acc'])) {
			$op['msg'] = $bom->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$num_bom_acc = count($bom_acc['acc']);
		if (!$num_bom_acc){	$op['bom_acc_NONE'] = "1";		}

		//-------------------- ��z������� ---- for bom table TITLE------------------------------
		if ($num_colors){
			for ($i=0;$i<$num_colors;$i++){
				$op['color'][$i]['name'] = $T_wiqty[$i]['colorway'];
				$T_qty = explode(",", $T_wiqty[$i]['qty']);
				$op['color'][$i]['qty'] = array_sum($T_qty);
				$color_qty[$i] = array_sum($T_qty);
			}
		} else {
				$color_qty[] = 0;   // �קK error
				$op['colors_NONE'] = "1";
		}

	// --------------- �D�� ------------ ���X $bom_lots_list[]  array....
		$op['bom_lots_list'] = array();
	    if (!$op['lots_NONE']){   // �˥����D�ƥήưO��---
	      $op['bom_lots_list'] =bom_lots_edit($num_lots_used,$bom_lots['lots'],$lots_used['lots_use'],$num_colors, $PHP_wi_id, $color_qty);
	    } 
	// ---------------------- end BOM data layout [�D��] ----------------------------

	// --------------- �Ʈ� ------------ ���X $bom_acc_list[]  array....
		$op['bom_acc_list'] = array();
		if (!$op['acc_NONE']){   // �˥����ƮƥήưO��---
	   		$op['bom_acc_list'] =bom_acc_edit($num_acc_used,$bom_acc['acc'],$acc_used['acc_use'],$num_colors, $PHP_wi_id, $color_qty);	
		} 
	// ---------------------- end BOM data layout [�Ʈ�] ----------------------------

	// ��� ��i�� -----
			if (!$num_colors){	
				$op['total_fields'] = 7;   // BOM table���`����
			}else{
				$op['total_fields'] = 6 + $num_colors;   // BOM table���`����
			}
// ---------------------- end BOM data layout table ----------------------------

					# �O���ϥΪ̰ʺA
			$log->log_add(0,"35A",$message);
			$op['msg'][] = $message;

		$back_str="&PHP_sr_startno=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_etdfsh=".$PHP_etdfsh."&PHP_cust=".$PHP_cust."&PHP_wi_num=".$PHP_wi_num."&PHP_etdstr=".$PHP_etdstr."&PHP_fty_sch=".$PHP_fty_sch;
		$op['back_str']=$back_str;
		$back_str2="&PHP_sr=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_etdfsh=".$PHP_etdfsh."&PHP_cust=".$PHP_cust."&PHP_wi_num=".$PHP_wi_num."&PHP_etdstr=".$PHP_etdstr."&PHP_fty_sch=".$PHP_fty_sch;
		$op['back_str2']=$back_str2;
		if (isset($PHP_rev))
		{
			page_display($op, 3,5, $TPL_BOM_CFM_EDIT);
		}else{
			page_display($op, 3,5, $TPL_BOM_ADD);	
		}
	    	    
		break;


//=======================================================
	case "bom_consum_add":

		check_authority(3,5,"view");

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
		$where_str = " WHERE wi_id='".$op['wi']['id']."' ";
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
		$back_str="&PHP_sr_startno=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_etdfsh=".$PHP_etdfsh."&PHP_cust=".$PHP_cust."&PHP_wi_num=".$PHP_wi_num."&PHP_etdstr=".$PHP_etdstr."&PHP_fty_sch=".$PHP_fty_sch;
		$op['back_str']=$back_str;
		$back_str2="&PHP_sr=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_etdfsh=".$PHP_etdfsh."&PHP_cust=".$PHP_cust."&PHP_wi_num=".$PHP_wi_num."&PHP_etdstr=".$PHP_etdstr."&PHP_fty_sch=".$PHP_fty_sch;
		$op['back_str2']=$back_str2;

			page_display($op, 4, 1, $TPL_BOM_CON_ADD);

			
		break;

//=======================================================
	case "do_bom_con_edit":

		check_authority(3,5,"view");
		$tmp='';
		$x=0;$k=0;
		foreach($PHP_lots_est as $key => $value)
		{
			$where_str="WHERE wi_id = '$PHP_wi_id' and lots_used_id = '".$key."' order by id" ;
			
			$bom_lots = $bom->get_fields_lots('qty',$where_str);
			$lots_id = $bom->get_fields_lots('id',$where_str);
			if (count($bom_lots))
			{
				for ($i=0; $i<sizeof($bom_lots); $i++)
				{
					$tmp_bom=explode(',',$bom_lots[$i]);
					for ($j=0; $j < sizeof($tmp_bom); $j++)
					{
						if ($tmp_bom[$j]=='' ||$tmp_bom[$j]==0)
						{
							$qty=0;
						}else{
							$qty=$bom_size[$j]*$value;
						}
						$tmp=$tmp.$qty.",";
					}
					$tmp=substr($tmp,0,-1);	
					$parm_bom[$k]=array($lots_id[$i],'qty',$tmp,'bom_lots');
					$k++;
					//echo "<br>bom lots qty = ".$tmp."<br>";
					$tmp='';
				}
			}
			$parm_lots[$x]=array($key,'est_1',$value);			
			$x++;			
		}

		$x=0;$k=0;
		foreach($PHP_acc_est as $key=>$value)
		{
			$where_str="WHERE wi_id = '$PHP_wi_id' and acc_used_id = '".$key."' order by id" ;
			
			$bom_acc = $bom->get_fields_acc('qty',$where_str);
			$acc_id = $bom->get_fields_acc('id',$where_str);
			if (count($bom_acc))
			{
				for ($i=0; $i<sizeof($bom_acc); $i++)
				{
					$tmp_bom=explode(',',$bom_acc[$i]);
					for ($j=0; $j < sizeof($tmp_bom); $j++)
					{
						if ($tmp_bom[$j]=='' ||$tmp_bom[$j]==0)
						{
							$qty=0;
						}else{
							$qty=$bom_size[$j]*$value;
						}
						$tmp=$tmp.$qty.",";
					}
					$tmp=substr($tmp,0,-1);	
					$parm_bom_acc[$k]=array($acc_id[$i],'qty',$tmp,'bom_acc');
					
					$tmp='';
					$k++;
				}
			}
			$parm_acc[$x]=array($key,'est_1',$value);			
			$x++;	
		}
		
		for ($i=0; $i<sizeof($parm_bom); $i++)		$bom->update_field($parm_bom[$i]);
		for ($i=0; $i<sizeof($parm_lots); $i++)		$smpl_lots->update_field($parm_lots[$i]);
		for ($i=0; $i<sizeof($parm_bom_acc); $i++)	$bom->update_field($parm_bom_acc[$i]);
		for ($i=0; $i<sizeof($parm_acc); $i++)		$smpl_acc->update_field($parm_acc[$i]);
				
		$parm=array($PHP_wi_id,'revise',($PHP_revise+1))	;
		$wi->update_field($parm);

		$redt_p="index2.php?&PHP_action=bom_view".$PHP_back_str2."&PHP_id=".$PHP_wi_id;
		redirect_page($redt_p);
		break;

//=======================================================
    case "bom_view":

		check_authority(3,5,"view");
		//-------------------- �N �s�y�O show out ------------------
		//  wi �D��
	if (isset($PHP_id))
	{
		if(!$op['wi'] = $wi->get($PHP_id)){    //���X�ӵ� �s�y�O�O��
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}
	}else{	
		if(!$op['wi']  = $wi->get(0,$PHP_bom_num)){    //���X�ӵ� �s�y�O�O��
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}
	}
		//  smpl �˥���
		if(!$op['smpl'] = $order->get($op['wi']['smpl_id'])){
					$op['msg']= $order->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}
		//-------------------- wi_qty �ƶq�� -------------------------------
			$where_str = " WHERE wi_id='".$op['wi']['id']."' ";
		$T_wiqty = $wiqty->search(1,$where_str);

		$num_colors = count($T_wiqty);
		$op['num_colors'] = $num_colors;
		// ���X size_scale ----------------------------------------------
		$size_A = $size_des->get($op['smpl']['size']);
		$op['smpl']['size_scale']=$size_A['size_scale'];
			//????????????????????????????????????? �`�N ���ɨS�� size type ��Ʈ�??????
		$sizing = explode(",", $size_A['size']);

			// ���X size table-------------------------------------------
		$reply = show_breakdown($T_wiqty,$sizing,$size_A['base_size']);
		$op['show_breakdown'] = $reply['html'];
		$op['total'] = $reply['total'];   // �`���
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

		if (!is_array($lots_used['lots_use'])) {
			$op['msg'] = $smpl_lots->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$num_lots_used = count($lots_used['lots_use']);
		if (!$num_lots_used){	$op['lots_NONE'] = "1";		}

		//  ���X�ƮƥήưO�� --------------------------------------------------------------
		 $op['acc_NONE']= '';
		$where_str = " WHERE smpl_code = '".$op['wi']['style_code']."' ";
		$acc_used['acc_use'] = $smpl_acc->search(0,$where_str);  //���X�ӵ� �˥��D�ưO��

		if (!is_array($acc_used['acc_use'])) {     
			$op['msg'] = $smpl_acc->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$num_acc_used = count($acc_used['acc_use']);
		if (!$num_acc_used){	$op['acc_NONE'] = "1";		}

		//  ���X  BOM  �D�ưO�� --------------------------------------------------------------

		$op['pa'] = '';
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

		//-------------------- ��z �� title ��������� ----------------------------------
		if ($num_colors){
			for ($i=0;$i<$num_colors;$i++){
				$op['color'][$i]['name'] = $T_wiqty[$i]['colorway'];
				$T_qty = explode(",", $T_wiqty[$i]['qty']);
				$op['color'][$i]['qty'] = array_sum($T_qty);
			}
		}else{
			$op['colors_NONE'] = "1";
		}

		// --------------- �D�� ------------ ���X $bom_lots_list[]  array....
			$op['bom_lots_list'] = array();
		if (!$op['lots_NONE']){   // �˥����D�ƥήưO��---
			$op['bom_lots_list'] =  bom_lots_view($num_lots_used,$bom_lots['lots'],$lots_used['lots_use'],$num_colors);
		}   
		// ---------------------- end BOM data layout [�D��] ----------------------------

		// --------------- �Ʈ� ------------ ���X $bom_acc_list[]  array....
			$op['bom_acc_list'] = array();
		if (!$op['acc_NONE']){   // �˥����ƮƥήưO��---
			$op['bom_acc_list'] =  bom_acc_view($num_acc_used,$bom_acc['acc'],$acc_used['acc_use'],$num_colors);
		}  
		// ---------------------- end BOM data layout [�Ʈ�] ----------------------------


			// ��� ��i�� -----
			if (!$num_colors){	
				$op['total_fields'] = 8;   // BOM table���`����
			}else{
				$op['total_fields'] = 7 + $num_colors;   // BOM table���`����
			}


		//  ���X  BOM  �[�ʰO�� --------------------------------------------------------------
		$op['ext_mat_NONE']= '';
		$op['ext_mat'] = $po->get_ext_ap($op['wi']['wi_num']);  //���X�ӵ� bom ��ALL�[�ʰO��
		$num_ext_mat = count($op['ext_mat']);
		if (!$num_ext_mat){	$op['ext_mat_NONE'] = "1";		}


		// ---------------------- end BOM data layout table ----------------------------
		$back_str="&PHP_sr_startno=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_etdfsh=".$PHP_etdfsh."&PHP_cust=".$PHP_cust."&PHP_wi_num=".$PHP_wi_num."&PHP_etdstr=".$PHP_etdstr."&PHP_fty_sch=".$PHP_fty_sch;
		$op['back_str']=$back_str;
		$back_str2="&PHP_sr=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_etdfsh=".$PHP_etdfsh."&PHP_cust=".$PHP_cust."&PHP_wi_num=".$PHP_wi_num."&PHP_etdstr=".$PHP_etdstr."&PHP_fty_sch=".$PHP_fty_sch;
		$op['back_str2']=$back_str2;

		if (isset($PHP_ex_order))
		{
			$op['edit_non']=1;
		}
		
		if(isset($PHP_cfm_view))
		{
			$op['pp']=$PHP_sr;
			page_display($op, 3,5, $TPL_BOM_CFM_VIEW);
		}else{
			page_display($op, 3,5, $TPL_BOM_VIEW);		    	    
		}
		break;




//=======================================================
    case "pa_bom_det":

		check_authority(3,5,"view");
		//-------------------- �N �s�y�O show out ------------------
		//  wi �D��
	if (isset($PHP_id))
	{
		if(!$op['wi'] = $wi->get($PHP_id)){    //���X�ӵ� �s�y�O�O��
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}
	}else{	
		if(!$op['wi']  = $wi->get(0,$PHP_bom_num)){    //���X�ӵ� �s�y�O�O��
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}
	}
		//  smpl �˥���
		if(!$op['order'] = $order->get($op['wi']['smpl_id'])){
					$op['msg']= $order->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
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
		//  ���X  BOM  �D�ưO�� --------------------------------------------------------------
		 $op['bom_lots_NONE']= '';
	 	 $op['bom_lots'] = $apply->get_lots_det($op['wi']['id']);  //���X�ӵ� bom ��ALL�D�ưO��
		 $num_bom_lots = count($op['bom_lots']);
		 if (!$num_bom_lots){	$op['bom_lots_NONE'] = "1";		}
		 if (isset($PHP_pa_num))
		 {
		 	for ($i =0; $i< sizeof($op['bom_lots']); $i++)
		 	{
		 			if ($op['bom_lots'][$i]['ap_mark'] == $PHP_pa_num) $op['bom_lots'][$i]['ln_mk'] = 1;
		 			if ($op['bom_lots'][$i]['spec_ap'])
		 			{
		 				for ($k =0; $k< sizeof($op['bom_lots'][$i]['spec_ap']); $k++)
		 				{
		 					if ($op['bom_lots'][$i]['spec_ap'][$k]['ap_num'] == $PHP_pa_num) $op['bom_lots'][$i]['ln_mk'] = 1;
		 				}
		 			}
		 	}
		 }

		//  ���X  BOM  �ƮưO�� --------------------------------------------------------------
		$op['bom_acc_NONE']= '';
		$op['bom_acc'] = $apply->get_acc_det($op['wi']['id']);  //���X�ӵ� bom ��ALL�ƮưO��
		$num_bom_acc = count($op['bom_acc']);
		if (!$num_bom_acc){	$op['bom_acc_NONE'] = "1";		}

		 if (isset($PHP_pa_num))
		 {
		 	for ($i =0; $i< sizeof($op['bom_acc']); $i++)
		 	{
		 			if ($op['bom_acc'][$i]['ap_mark'] == $PHP_pa_num) $op['bom_acc'][$i]['ln_mk'] = 1;
		 			if ($op['bom_acc'][$i]['spec_ap'])
		 			{
		 				for ($k =0; $k< sizeof($op['bom_acc'][$i]['spec_ap']); $k++)
		 				{
		 					if ($op['bom_acc'][$i]['spec_ap'][$k]['ap_num'] == $PHP_pa_num) $op['bom_acc'][$i]['ln_mk'] = 1;
		 				}
		 			}
		 	}
		 }

		//  ���X  BOM  �[�ʰO�� --------------------------------------------------------------
		$op['ext_mat_NONE']= '';
		$op['ext_mat'] = $apply->get_ext_ap($op['wi']['wi_num']);  //���X�ӵ� bom ��ALL�[�ʰO��
		$num_ext_mat = count($op['ext_mat']);
		if (!$num_ext_mat){	$op['ext_mat_NONE'] = "1";		}
		
		 if (isset($PHP_pa_num))
		 {
		 	for ($i =0; $i< sizeof($op['ext_mat']); $i++)
		 	{
		 			if ($op['ext_mat'][$i]['ap_num'] == $PHP_pa_num) $op['ext_mat'][$i]['ln_mk'] = 1;
		 	}
		 }		
		
		page_display($op, 3,5, $TPL_BOM_PA_VIEW);
		break;

//=======================================================
    case "do_bom_del_ajx":

		check_authority(3,5,"edit");
		// �R�� bom ��
		
		if ($PHP_mat=="lots"){
			$T = "Fabric";
			$f1 = $bom->del_lots($PHP_bom_id);   
		}elseif($PHP_mat=="acc"){
			$T = "Accessory";
			$f1 = $bom->del_acc($PHP_bom_id);   // �[�J
		}else{
			$message = "Error ! Don't point delete information of BOM !";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		if (!$f1) {  // �S�����\�R����Ʈ�
			$message = $wi->msg->get(2);
		}
			
		//-------------------- �N BOM �s�y�O show out ------------------
		//  wi �D��
		if(!$op['wi'] = $wi->get($PHP_id)){    //���X�ӵ� �s�y�O�O��
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}


		$message = "Delete wi[ ".$op['wi']['wi_num' ]."]  BOM ".$T." Record:[".$mat_code."] ID=[".$PHP_bom_id."]";
				# �O���ϥΪ̰ʺA
		$log->log_add(0,"35D",$message);
		echo $message;
		exit;
		break;

//=======================================================
    case "bom_revise_view":

		check_authority(3,5,"view");
		//-------------------- �N �s�y�O show out ------------------
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
		//-------------------- wi_qty �ƶq�� -------------------------------
			$where_str = " WHERE wi_id='".$op['wi']['id']."' ";
		$T_wiqty = $wiqty->search(1,$where_str);

		$num_colors = count($T_wiqty);
		$op['num_colors'] = $num_colors;
		// ���X size_scale ----------------------------------------------
		$size_A = $size_des->get($op['smpl']['size']);
		$op['smpl']['size_scale']=$size_A['size_scale'];
			//????????????????????????????????????? �`�N ���ɨS�� size type ��Ʈ�??????
		$sizing = explode(",", $size_A['size']);

			// ���X size table-------------------------------------------
		$reply = show_breakdown($T_wiqty,$sizing,$size_A['base_size']);
		$op['show_breakdown'] = $reply['html'];
		$op['total'] = $reply['total'];   // �`���
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

		if (!is_array($lots_used['lots_use'])) {
			$op['msg'] = $smpl_lots->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$num_lots_used = count($lots_used['lots_use']);
		if (!$num_lots_used){	$op['lots_NONE'] = "1";		}

		//  ���X�ƮƥήưO�� --------------------------------------------------------------
		 $op['acc_NONE']= '';
		$where_str = " WHERE smpl_code = '".$op['wi']['style_code']."' ";
		$acc_used['acc_use'] = $smpl_acc->search(0,$where_str);  //���X�ӵ� �˥��D�ưO��

		if (!is_array($acc_used['acc_use'])) {     
			$op['msg'] = $smpl_acc->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$num_acc_used = count($acc_used['acc_use']);
		if (!$num_acc_used){	$op['acc_NONE'] = "1";		}

		//  ���X  BOM  �D�ưO�� --------------------------------------------------------------
		 $op['bom_lots_NONE']= '';
		$where_str = " WHERE wi_id = '".$op['wi']['id']."' ";
		$bom_lots = $bom->search_lots(0,$where_str);  //���X�ӵ� bom ��ALL�D�ưO��

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

		if (!is_array($bom_acc['acc'])) {
			$op['msg'] = $bom->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$num_bom_acc = count($bom_acc['acc']);
		if (!$num_bom_acc){	$op['bom_acc_NONE'] = "1";		}

		//-------------------- ��z �� title ��������� ----------------------------------
		if ($num_colors){
			for ($i=0;$i<$num_colors;$i++){
				$op['color'][$i]['name'] = $T_wiqty[$i]['colorway'];
				$T_qty = explode(",", $T_wiqty[$i]['qty']);
				$op['color'][$i]['qty'] = array_sum($T_qty);
			}
		}else{
			$op['colors_NONE'] = "1";
		}

		// --------------- �D�� ------------ ���X $bom_lots_list[]  array....
			$op['bom_lots_list'] = array();

		if (!$op['lots_NONE']){   // �˥����D�ƥήưO��---
			$op['bom_lots_list'] =  bom_lots_view($num_lots_used,$bom_lots['lots'],$lots_used['lots_use'],$num_colors);

		}   
		// ---------------------- end BOM data layout [�D��] ----------------------------

		// --------------- �Ʈ� ------------ ���X $bom_acc_list[]  array....
			$op['bom_acc_list'] = array();

		if (!$op['acc_NONE']){   // �˥����ƮƥήưO��---
			$op['bom_acc_list'] =  bom_acc_view($num_acc_used,$bom_acc['acc'],$acc_used['acc_use'],$num_colors);

		}  
		// ---------------------- end BOM data layout [�Ʈ�] ----------------------------

			// ��� ��i�� -----
			if (!$num_colors){	
				$op['total_fields'] = 8;   // BOM table���`����
			}else{
				$op['total_fields'] = 7 + $num_colors;   // BOM table���`����
			}
		// ---------------------- end BOM data layout table ----------------------------
			$message = "success review bom : ".$op['wi']['wi_num'];
			$log->log_add(0,"35E",$message);
			$op['msg'][] = $message;
			page_display($op, 3,5, $TPL_BOM_REVISE_SHOW);		    	    
		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//   bom_cfm      //  WI CFM...........
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "bom_uncfm":
		check_authority(3,6,"view");
		
		if (!$op = $wi->search_uncfm('bom')) {
			$op['msg']= $wi->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$back_str="&PHP_dept_code=&PHP_etdfsh=&PHP_cust=&PHP_wi_num=&PHP_etdstr=&PHP_fty_sch=";

		//2006/05/02 update
		$op['dept_id'] = get_dept_id();
		
		// �p�G�O manager �i�J��...
		if (substr($op['dept_id'],0,7) == "<select"){
			$op['manager_flag'] = 1;
		}	
				$op['msg']= $wi->msg->get(2);
				$op['back_str']= $back_str;
			page_display($op, 3,6, $TPL_BOM_UNCFM_LIST);
		break;

//=======================================================
	case "do_bom_cfm":
		check_authority(3,6,"edit");
		
		$parm = array(	"order_num" =>	$PHP_order_num,
						"id"		=>	$PHP_id,
						"date"		=>	$dt['date_str'],
						"user"		=>	$GLOBALS['SCACHE']['ADMIN']['login_id']
					  );
		$f1 = $wi->bom_cfm($parm);
		if (!$f1) {
			$op['msg']= $wi->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$message="success CFM BOM:[".$PHP_wi."]";
		$log->log_add(0,"36A",$message);
		if (!$op = $wi->search_uncfm('bom')) {
			$op['msg']= $wi->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		
		if ($PHP_revise == 0)
		{
				$parm=array($PHP_id,'bom_rev','1');
				$wi->update_field($parm);
		}

		
		$back_str="&PHP_dept_code=&PHP_etdfsh=&PHP_cust=&PHP_wi_num=&PHP_etdstr=&PHP_fty_sch=";

		//2006/05/02 update
		$op['dept_id'] = get_dept_id();
		
		// �p�G�O manager �i�J��...
		if (substr($op['dept_id'],0,7) == "<select"){
			$op['manager_flag'] = 1;
		}
		
				$op['msg'][]= $message;
				$op['back_str']= $back_str;
			page_display($op, 3,6, $TPL_BOM_UNCFM_LIST);
		break;


//=======================================================
    case "revise_bom":

		check_authority(3,5,"edit");
		//-------------------- �N BOM �s�y�O show out ------------------
		//  wi �D��	
		if(!$op['wi'] = $wi->get($PHP_id)){    //���X�ӵ� �s�y�O�O��
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}
		$op['wi']['bom_rev']=$op['wi']['bom_rev']+1;
		
		$parm = array(	"order_num" =>	$op['wi']['style_code'],
						"id"		=>	$PHP_id,
						"date"		=>	'0000-00-00 00:00:00',
						"user"		=>	'',
						"bom_rev"	=>	$op['wi']['bom_rev']
					  );

		
		$f1=$wi->revise_bom($parm);
		if (!$f1)
		{
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

		//------------- wi_qty �ƶq�� ----------------------------------
			$where_str = " WHERE wi_id='".$op['wi']['id']."' ";
		$T_wiqty = $wiqty->search(1,$where_str);

		$num_colors = count($T_wiqty);
		$op['num_colors'] = $num_colors;
		// ���X size_scale ----------------------------------------------
		$size_A = $size_des->get($op['smpl']['size']);
		$op['smpl']['size_scale']=$size_A['size_scale'];
			//????????????????????????????????????? �`�N ���ɨS�� size type ��Ʈ�??????
		$sizing = explode(",", $size_A['size']);

			// ���X size table-------------------------------------------
		$reply = show_breakdown($T_wiqty,$sizing,$size_A['base_size']);
		$op['show_breakdown'] = $reply['html'];
		$op['total'] = $reply['total'];   // �`���
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
//		$op['lots_used'] = $lots_used['lots_use'];

		if (!is_array($lots_used['lots_use'])) {
			$op['msg'] = $smpl_lots->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$num_lots_used = count($lots_used['lots_use']);
		if (!$num_lots_used){	$op['lots_NONE'] = "1";		}

		//  ���X�ƮƥήưO�� --------------------------------------------------------------
		 $op['acc_NONE']= '';
		$where_str = " WHERE smpl_code = '".$op['wi']['style_code']."' ";
		$acc_used['acc_use'] = $smpl_acc->search(0,$where_str);  //���X�ӵ� �˥��D�ưO��
//		$op['acc_used'] = $acc_used['acc_use'];

		if (!is_array($acc_used['acc_use'])) {     
			$op['msg'] = $smpl_acc->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$num_acc_used = count($acc_used['acc_use']);
		if (!$num_acc_used){	$op['acc_NONE'] = "1";		}

		//  ���X  BOM  �D�ưO�� --------------------------------------------------------------
		 $op['bom_lots_NONE']= '';
		$where_str = " WHERE wi_id = '".$op['wi']['id']."' ";
		$bom_lots = $bom->search_lots(0,$where_str);  //���X�ӵ� bom ��ALL�D�ưO��
		$op['bom_lots'] = $bom_lots['lots'];

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
		$op['bom_acc'] = $bom_acc['acc'];

		if (!is_array($bom_acc['acc'])) {
			$op['msg'] = $bom->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$num_bom_acc = count($bom_acc['acc']);
		if (!$num_bom_acc){	$op['bom_acc_NONE'] = "1";		}

		//-------------------- ��z������� ---- for bom table TITLE------------------------------
		if ($num_colors){
			for ($i=0;$i<$num_colors;$i++){
				$op['color'][$i]['name'] = $T_wiqty[$i]['colorway'];
				$T_qty = explode(",", $T_wiqty[$i]['qty']);
				$op['color'][$i]['qty'] = array_sum($T_qty);
				$color_qty[$i] = array_sum($T_qty);
			}
		} else {
				$color_qty[] = 0;   // �קK error
				$op['colors_NONE'] = "1";
		}

	// --------------- �D�� ------------ ���X $bom_lots_list[]  array....
		$op['bom_lots_list'] = array();
	    if (!$op['lots_NONE']){   // �˥����D�ƥήưO��---
	      $op['bom_lots_list'] =bom_lots_edit($num_lots_used,$bom_lots['lots'],$lots_used['lots_use'],$num_colors, $PHP_id, $color_qty);
	    } 
	// ---------------------- end BOM data layout [�D��] ----------------------------

	// --------------- �Ʈ� ------------ ���X $bom_acc_list[]  array....
		$op['bom_acc_list'] = array();
		if (!$op['acc_NONE']){   // �˥����ƮƥήưO��---
	   		$op['bom_acc_list'] =bom_acc_edit($num_acc_used,$bom_acc['acc'],$acc_used['acc_use'],$num_colors, $PHP_id, $color_qty);	
		} 
	// ---------------------- end BOM data layout [�Ʈ�] ----------------------------

	// ��� ��i�� -----
			if (!$num_colors){	
				$op['total_fields'] = 7;   // BOM table���`����
			}else{
				$op['total_fields'] = 6 + $num_colors;   // BOM table���`����
			}
		// ---------------------- end BOM data layout table ----------------------------
		
		
	if (isset($PHP_dept_code))
	{
		$back_str="&PHP_sr_startno=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_etdfsh=".$PHP_etdfsh."&PHP_cust=".$PHP_cust."&PHP_wi_num=".$PHP_wi_num."&PHP_etdstr=".$PHP_etdstr."&PHP_fty_sch=".$PHP_fty_sch;
		$op['back_str']=$back_str;
		$back_str2="&PHP_sr=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_etdfsh=".$PHP_etdfsh."&PHP_cust=".$PHP_cust."&PHP_wi_num=".$PHP_wi_num."&PHP_etdstr=".$PHP_etdstr."&PHP_fty_sch=".$PHP_fty_sch;
		$op['back_str2']=$back_str2;
	}else{
		$op['back_str']=$PHP_back_str;
		$op['back_str2']=$PHP_back_str2;
	}

		page_display($op, 3,5, $TPL_BOM_CFM_EDIT);  	    
		break;






//-------------------------------------------------------------------------------------
//			 job 31  �s�@�O
//-------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------
//			 job 31  �s�@�O
//-------------------------------------------------------------------------------------
    case "wi":
    	check_authority(3,2,"view");
		//2006/05/02 update
		$where_str = $manager = $dept_id = '';		 
		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];   // �P�w�i�J����������
		$team = $GLOBALS['SCACHE']['ADMIN']['team_id'];  // �P�w�i�J����������(team)
		$sales_dept_ary = get_sales_dept();// ���X �~�Ȫ����� [���tK0] ------
		for ($i=0; $i<count($sales_dept_ary);$i++){
			if($user_dept == $sales_dept_ary[$i]){

				// �p�G�O�~�ȳ��i�J �hdept_code ���w�ӷ~�ȳ�---
				$dept_id = $sales_dept_ary[$i];  
			}
		}
		$op['dept_id'] = $dept_id;
		if (!$dept_id || $team<>'MD') {    // ���O�~�ȳ��H[�]���t K0 ���H ]�i�J��
			$op['manager_flag'] = 1;
			$manager_v = 1;
			//�~�ȳ��� select���
			$op['dept_id'] = $arry2->select($sales_dept_ary,"","PHP_dept_code","select",""); 
 
		} else {
			$where_str = " WHERE dept = '".$dept_id."' ";	
					
		}
		
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
				
		$op['fty'] = $arry2->select($FACTORY,"","PHP_sch_fty","select",""); 
		
		for ($i=0; $i< sizeof($FACTORY); $i++)
		{
			if ($user_dept == $FACTORY[$i]) $op['fty'] = $user_dept."<input type='hidden' name='PHP_sch_fty' value='$user_dept'>";
		}
		$op['msg'] = $wi->msg->get(2);
				// creat sample type combo box
				$sample_def = $smpl_type->get_fields('smpl_type');   // ���X �˥����O
				$op['smpl_type_select'] =  $arry2->select($sample_def,'','PHP_smpl_type','select','');  	
	
			page_display($op, 3, 2, $TPL_WI_SEARCH);	    	    
		break;
//=======================================================
    case "wi_search":
		check_authority(3,2,"view");
		if (!isset($PHP_un_fhs)){$PHP_un_fhs='';}
		if (!$op = $order->mater_search(1,$PHP_dept_code)) {
			$op['msg']= $order->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$back_str = "&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh."&PHP_sch_fty=".$PHP_sch_fty."&PHP_un_fhs=".$PHP_un_fhs;
		
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
		}

		//2006/05/02 update
		$op['dept_id'] = get_dept_id();
		
		// �p�G�O manager �i�J��...
		if (substr($op['dept_id'],0,7) == "<select"){
			$op['manager_flag'] = 1;
		}

				$op['msg']= $order->msg->get(2);
				$op['back_str']= $back_str;
			page_display($op, 3, 2, $TPL_WI);
		break;
		
//=======================================================
    case "wi_add":

		check_authority(3,2,"add");
			if ($PHP_size > 0)
			{
				$size_scale=$size_des->get_fields('size_scale',"where id='$PHP_size'");				
				$PHP_size_scale=$size_scale[0];
			}
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
		    if (isset($PHP_dept_code))
			{
				$PHP_back_str = "&PHP_sr_startno=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh."&PHP_sch_fty=".$PHP_sch_fty."&PHP_un_fhs=".$PHP_un_fhs;
				$op['back_str']=$PHP_back_str;
				$PHP_back_str2 = "&PHP_sr=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh."&PHP_sch_fty=".$PHP_sch_fty."&PHP_un_fhs=".$PHP_un_fhs;
				$op['back_str2']=$PHP_back_str2;
			}else{
				$op['back_str']="&PHP_sr_startno=&PHP_dept_code=&PHP_num=&PHP_cust=&PHP_etdstr=&PHP_etdfsh=&PHP_sch_fty=&PHP_un_fhs=";
				$op['back_str2']="&PHP_sr=&PHP_dept_code=&PHP_num=&PHP_cust=&PHP_etdstr=&PHP_etdfsh=&PHP_sch_fty=&PHP_un_fhs=";
				$PHP_back_str2=$op['back_str2'];
			}

			// ���X�ﶵ��� �ζǤJ���Ѽ�
			

			page_display($op, 3, 2, $TPL_WI_ADD);	    	    
		break;

//=======================================================
	case "do_wi_copy":

		check_authority(3,2,"edit");

//��Sample order�ƻs[�s�y�O]
		$f1=$wi->copy_wi($PHP_wi_num,$PHP_smpl_id,$PHP_new_num,$PHP_etd,$PHP_ord_cust,$PHP_dept,$GLOBALS['SCACHE']['ADMIN']['login_id'],$dt['date_str']);
		
		if (!$f1)
		{   echo "here1";
			$op['msg']= $wi->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
// �N wis [�˥��s�O]�s�� �g�J �˥��ɤ�[ smpl table ] NOTE: �D�n�O���Ӵ� �˥�����A������s�O[�ݦA�ƻs]
		if (!$order->update_field("marked",$PHP_new_num,$PHP_smpl_id)) {
			echo "here2";
			$op['msg']= $order->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		if (!$order->update_field("m_status",'1',$PHP_smpl_id)) {
			echo "here3";
			$op['msg']= $order->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		
		
		// �N �s�y�O ����show out ------
		//  wi �D�� ��X��		
		if(!$op = $wi->get_all($f1)){    //���X�ӵ� �s�y�O�O��
			echo "here5";
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}

			$parm_update = array($op['wi']['id'],'last_update',$dt['date_str']);
			$wi->update_field($parm_update);
			$parm_update = array($op['wi']['id'],'updator',$GLOBALS['SCACHE']['ADMIN']['login_id']);
			$wi->update_field($parm_update);


	//�˵��O�_�i�H�ק�size(�Y�O�_�s�bcolorway)
		if ($wi->check_size($op['wi']['style_code']))
		{
			$op['size_edit']=1;
		}

		//  wi_qty �ƶq�� -----------------------------------------------------------------
		$where_str = " WHERE wi_id='".$f1."' ";
		$T_wiqty = $wiqty->search(1,$where_str);

		// ���X size_scale --------
		$size_A = $size_des->get($op['smpl']['size']);
		$op['smpl']['size_scale']=$size_A['size_scale'];
			//????????????????????????????????????? �`�N ���ɨS�� size type ��Ʈ�??????
		$sizing = explode(",", $size_A['size']);


//search����O��			
		    if (isset($PHP_dept_code))
			{
				$PHP_back_str = "&PHP_sr_startno=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh."&PHP_sch_fty=".$PHP_sch_fty."&PHP_un_fhs=".$PHP_un_fhs;
				$op['back_str']=$PHP_back_str;
				$PHP_back_str2 = "&PHP_sr=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh."&PHP_sch_fty=".$PHP_sch_fty."&PHP_un_fhs=".$PHP_un_fhs;
				$op['back_str2']=$PHP_back_str2;
			}else{
				$op['back_str']="&PHP_sr_startno=&PHP_dept_code=&PHP_num=&PHP_cust=&PHP_etdstr=&PHP_etdfsh=&PHP_sch_fty=&PHP_un_fhs=";
				$op['back_str2']="&PHP_sr=&PHP_dept_code=&PHP_num=&PHP_cust=&PHP_etdstr=&PHP_etdfsh=&PHP_sch_fty=&PHP_un_fhs=";
				$PHP_back_str2=$op['back_str2'];
			}

		// ���X size table-------------------------------------------
			$op['show_del_breakdown'] = show_del_breakdown($T_wiqty,$sizing,$PHP_back_str2,$size_A['base_size']);
			$op['edit_breakdown'] = edit_breakdown($sizing,$PHP_back_str2,$size_A['base_size']);
			// ���X unit�� combo
		$op['unit'] = $arry2->select($APPAREL_UNIT,$op['wi']['unit'],'PHP_unit','select','');  	
			
		$op['msg'] = $wi->msg->get(2);

		page_display($op, 3, 2, $TPL_WI_EDIT);
		break;



//=======================================================
	case "do_wi_add_1":

		check_authority(3,2,"add");
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

		$parm = array(	"dept"			=>	$PHP_dept_code,
						"cust"			=>	$PHP_cust,
						"cust_ref"		=>	$PHP_cust_ref,
						"smpl_id"		=>	$PHP_smpl_id,

						"etd"			=>	$PHP_etd,						
						"unit"			=>	$PHP_unit,

						"size_scale"	=>	$size_id,	// ���s�J wi
						

						"style_code"	=>	$PHP_style_code,
						"creator"		=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
						"open_date"		=>	$dt['date_str'],
						"version"		=>	"1"
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
				$op['back_str2']=$PHP_back_str2;
				$op['back_str']=$PHP_back_str;
			
			page_display($op, 3, 1, $TPL_WI_ADD);	    	    
		break;
		
		}   // end check
		
		// �s�s �s�@�O ���X----------------------------------------------------------------
		//  �s�s ��@�O���X �]�P�ɧ�sdept�ɤ���num��[csv]
		$parm['wi_num'] = $PHP_style_code; 

//�ק�q��ETD   start
	$ord = $order->get($PHP_smpl_id);
	if ($ord['etd'] <> $PHP_etd)
	{	
		if ($ord['status'] > 3) //�T�{�O�_�ݭn�ק�C��SU�O��
		{
				 $ord_pdt=$order->get_pdtion($ord['order_num'], $ord['factory']);
				 $etp_su=decode_mon_yy_su($ord_pdt['etp_su']); //���X�v�s�betp-su�ñNsu�ȻP�~,����}
				 for ($i=0; $i<sizeof($etp_su); $i++) //�N�v�s�bsu���~�װO���ȧR��
				 {				 	
				 	$f1=$capaci->delete_su($ord['factory'], $etp_su[$i]['year'], $etp_su[$i]['mon'], 'pre_schedule', $etp_su[$i]['su']);			 		
				 }
				 $div = $order->distri_month_su($ord['su'],$ord['etp'],$PHP_etd,$ord['factory'],'pre_schedule'); //����C��SU���x�s
				 $order->update_pdtion_field('etp_su', $div, $ord_pdt['id']);  //�x�s�setp_su
		}
		$order->update_field('etd', $PHP_etd, $PHP_smpl_id);
		$order->update_field('last_update',$TODAY,$PHP_smpl_id);
		$order->update_field('last_updator',$GLOBALS['SCACHE']['ADMIN']['login_id'],$PHP_smpl_id);

		$message="Successfully edit ETD & ETP on order :".$ord['order_num'];
		$log->log_add(0,"101E",$message);
	}
//�ק�q��ETD   end		
		
		$f1 = $wi->add($parm);
	
		if (!$f1) {  // �S�����\��J��Ʈ�

			$op['msg'] = $wi->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);
			break;

		}   // end if (!$F1)---------  �����P�_ ------------------------------------
		$message = "Done ADD [".$parm['wi_num']."] W / I�FPlease add[colorway / breakdown] and [materiel consump.]�C";

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

		
		// ���X�ӵ� wi ��� ---------------------------------------------------------- 
			if (!$op['wi']=$wi->get($f1)) {
				$op['msg']= $wi->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
		
			$parm_update = array($op['wi']['id'],'last_update',$dt['date_str']);
			$wi->update_field($parm_update);
			$parm_update = array($op['wi']['id'],'updator',$GLOBALS['SCACHE']['ADMIN']['login_id']);
			$wi->update_field($parm_update);


			// �e�X �����Ѽ� ---------------------------------------
					$op['smpl']['cust'] = $parm['cust'];
					$op['smpl']['style'] = $parm['cust_ref'];
					$op['smpl']['size_scale'] = $PHP_Size;


		//  wi_qty �ƶq�� -----------------------------------------------------------------
		$where_str = " WHERE wi_id='".$f1."' ";
		$T_wiqty = $wiqty->search(1,$where_str);
	
		// ���X size_scale --------
		
		$size_A = $size_des->get($size_id);
			//????????????????????????????????????? �`�N ���ɨS�� size type ��Ʈ�??????
		$sizing = explode(",", $size_A['size']);

			// ���X size table-------------------------------------------
			$reply = show_breakdown($T_wiqty,$sizing,$size_A['base_size']);
			$op['show_breakdown'] = $reply['html'];
			$op['total'] = $reply['total'];
		//-----------------------------------------------------------------
		$op['back_str']=$PHP_back_str;
		$op['back_str2']=$PHP_back_str2;		
		$op['edit_breakdown'] = edit_breakdown($sizing,$PHP_back_str2,$size_A['base_size']);


			// �ۤ���URL�M�w ----------------------------------------------------------------
				$style_dir	= "./picture/";  
				$no_img		= "./images/graydot.gif";
			if(file_exists($style_dir.$op['wi']['style_code'].".jpg")){
				$op['wi']['pic_url'] = $style_dir.$op['wi']['style_code'].".jpg";
			} else {
				$op['wi']['pic_url'] = $no_img;
			}

			$op['wi']['etd'] = $parm['etd'];
		
		//  ���X�D�ưO��  --------------------------------------------------------------------
		$where_str = " WHERE smpl_code = '".$op['wi']['style_code']."' ";
		$lots = $smpl_lots->search(0,$where_str);  //���X�ӵ� �˥��D�ưO��
		$op['lots_use'] = $lots;

		if (!is_array($op['lots_use'])) {
			$op['msg'] = $smpl_lots->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		if (!count($op['lots_use'])){	$op['lots_NONE'] = "1";		}


		//  ���X�ƮƮưO��  ------------------------------------------------------------------
		$where_str = " WHERE smpl_code = '".$op['wi']['style_code']."' ";
		$acc = $smpl_acc->search(0,$where_str);  //���X�ӵ� �˥��D�ưO��
		$op['acc_use'] = $acc;

		if (!is_array($op['acc_use'])) {     //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
			$op['msg'] = $smpl_acc->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		if (!count($op['acc_use'])){	$op['acc_NONE'] = "1";		}
		//------------------------------------------------------------------------------------
						# �O���ϥΪ̰ʺA
				$log->log_add(0,"31A",$message);
				$op['msg'][] = $message;
				page_display($op, 3, 2, $TPL_WI_ADD2);	    	    

			break;

//=======================================================
/*
	case "del_colorway":    // �s�W�s�@�O
		check_authority(3,1,"add");
		// �R�� ���w����� ----------------------------
		if(!$T_del =	$wiqty->del($id)){    
						$op['msg']= $wi->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
		}


		// �N �s�y�O ����show out =========================================================
		//  wi �D�� ��X�� ----------------------------------------------------
		if(!$op = $wi->get_all($wi_id)){    //���X�ӵ� �s�y�O�O��
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}

				# �O���ϥΪ̰ʺA
		$message = "Append w/i[".$op['wi']['wi_num']."]; Successfully delete colorway[".$colorway."]�E";

		//  wi_qty �ƶq�� -----------------------------------------------------------------
		$where_str = " WHERE wi_id='".$wi_id."' ";
		$T_wiqty = $wiqty->search(1,$where_str);

		// ���X size_scale --------
		$size_A = $size_des->get($op['smpl']['size']);
		$op['smpl']['size_scale']=$size_A['size_scale'];
			//????????????????????????????????????? �`�N ���ɨS�� size type ��Ʈ�??????
		$sizing = explode(",", $size_A['size']);
		

//------------search����O��
		if (isset($PHP_dept_code))
		{
				$PHP_back_str = "&PHP_sr_startno=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh."&PHP_sch_fty=".$PHP_sch_fty."&PHP_un_fhs=".$PHP_un_fhs;
				$op['back_str']=$PHP_back_str;
				$PHP_back_str2 = "&PHP_sr=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh."&PHP_sch_fty=".$PHP_sch_fty."&PHP_un_fhs=".$PHP_un_fhs;
				$op['back_str2']=$PHP_back_str2;
		}else{
				$op['back_str']=$PHP_back_str;
				$op['back_str2']=$PHP_back_str2;				
		}


		// ���X size table--------
		$op['show_del_breakdown'] = show_del_breakdown($T_wiqty,$sizing,$PHP_back_str2,$size_A['base_size']);
		$op['edit_breakdown'] = edit_breakdown($sizing,$PHP_back_str2,$size_A['base_size']);

		$op['msg'][] = $message;
		page_display($op, 3, 1, $TPL_WI_ADD2); 
		break;
*/
//=======================================================
	case "add_colorway":      // �s�W�s�@�O

		check_authority(3,2,"add");
		// �ǤJ javascript�ǥX���Ѽ� [ colorway : ���M��J�w�� javascript�߬d ]
		$parm['qty']		= $PHP_qty;   // ��javascript�ǥX���}�C�� �۰ʧ令csv�ǥX
		$parm['wi_id']		= $wi_id;
		$parm['colorway']	= $colorway;

		// �[�J �s��� ----- �[�J wiqty table -----------------
		if(!$T_del =	$wiqty->add($parm)){    
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
		$message = "Successful add w/i[".$op['wi']['wi_num']."] colorway[".$colorway."]�E";
		$log->log_add(0,"31A",$message);


		//  wi_qty �ƶq�� -----------------------------------------------------------------
		$where_str = " WHERE wi_id='".$wi_id."' ";
		$T_wiqty = $wiqty->search(1,$where_str);

		// ���X size_scale --------
		$size_A = $size_des->get($op['smpl']['size']);
		$op['smpl']['size_scale']=$size_A['size_scale'];
			//????????????????????????????????????? �`�N ���ɨS�� size type ��Ʈ�??????
		$sizing = explode(",", $size_A['size']);
//------------search����O��
		if (isset($PHP_dept_code))
		{
				$PHP_back_str = "&PHP_sr_startno=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh."&PHP_sch_fty=".$PHP_sch_fty."&PHP_un_fhs=".$PHP_un_fhs;
				$op['back_str']=$PHP_back_str;
				$PHP_back_str2 = "&PHP_sr=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh."&PHP_sch_fty=".$PHP_sch_fty."&PHP_un_fhs=".$PHP_un_fhs;
				$op['back_str2']=$PHP_back_str2;
		}else{
				$op['back_str']=$PHP_back_str;
				$op['back_str2']=$PHP_back_str2;				
		}
		// ���X size table--------
		$op['show_del_breakdown'] = show_del_breakdown($T_wiqty,$sizing,$PHP_back_str2,$size_A['base_size']);
		$op['edit_breakdown'] = edit_breakdown($sizing,$PHP_back_str2,$size_A['base_size']);

			$op['msg'][] = $message;
		page_display($op, 3, 2, $TPL_WI_ADD2);	  
		break;



//=========================================================================
	case "do_wi_add_2":

		check_authority(3,2,"add");
	// ��J�����T��............after check input.........
				// ���X�~�X... �H��J�ɤ��~���~�X[���]

		// �N�D�Ʈ� �w���g�J �D�Ʈƨϥ��ɤ� lots_use, acc_use  ....
		// �D��  --------------------------------------------------------------------------
		$parm = array(	"style_code"	=>	$PHP_style_code,
						"cust_ref"		=>	$PHP_cust_ref,
						"size_type"		=>	$PHP_size_type,
						"size_scale"	=>	$PHP_size_scale,
						
						"wi_id"		=>	$PHP_wi_id,
						"wi_num"	=>	$PHP_wi_num,
						"dept"		=>	$PHP_dept,
						"smpl_id"	=>	$PHP_smpl_id,
						"cust"		=>	$PHP_cust,		

						"etd"		=>	$PHP_etd,
						"smpl_type" =>	$PHP_smpl_type,

						"unit"		=>	$PHP_unit,
						"lots_est_array"	=>	$PHP_lots_est,	// �O�� array 
						"acc_est_array"		=>	$PHP_acc_est,  // �O�� array
						"ester"				=>	$GLOBALS['SCACHE']['ADMIN']['login_id']
			);
		if($parm['lots_est_array']){
			while(list($key,$val) = each($parm['lots_est_array'])){
				if($val){
					$lots = array($key,"est_1",$val);
					if(!$smpl_lots->update_field($lots)){
						$op['msg']= $smpl_lots->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
					}

					$updt = array($key,"ester_1",$parm['ester']);
					if(!$smpl_lots->update_field($updt)){
						$op['msg']= $smpl_lots->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
					}
				}
			}
		}
		// �Ʈ�  ----------------------------------------------------------------------------
		if($parm['acc_est_array']){
			while(list($key,$val) = each($parm['acc_est_array'])){
				if($val){
					$acc = array($key,"est_1",$val);
					if(!$smpl_acc->update_field($acc)){
						$op['msg']= $smpl_acc->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
					}

					$updt = array($key,"ester_1",$parm['ester']);
					if(!$smpl_acc->update_field($updt)){
						$op['msg']= $smpl_acc->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
					}
				}
			}
		}
		
		//------------------------------------ �����g�J wi �� �ήƹw��---------------
		// �N �s�y�O ����show out ------
		//  wi �D��
		if(!$op = $wi->get_all($parm['wi_id'])){
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}

//--------------------------------------------------------------------------
		//  wi_qty �ƶq��
			$where_str = " WHERE wi_id='".$op['wi']['id']."' ";
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

		# �O���ϥΪ̰ʺA
		$message = "Successfully add full w/i : ".$op['wi']['wi_num']."record�C";
		$log->log_add(0,"31A",$message);
		$op['msg'][] = $message;
		$op['back_str']=$PHP_back_str;
		$op['back_str2']=$PHP_back_str2;
		page_display($op, 3, 2, $TPL_WI_VIEW);
		break;
	

//========================================================================================	
	case "wi_view":

		check_authority(3,2,"view");

		// �N �s�y�O ����show out ------
		//  wi �D��

		if(is_numeric($PHP_id)){
			if(!$op = $wi->get_all($PHP_id)){    //���X�ӵ� �s�y�O�O�� ID
						$op['msg']= $wi->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
			}
		} else {
			if(!$op = $wi->get_all(0,$PHP_id)){    //���X�ӵ� �s�y�O�O�� WI_NUM
						$op['msg']= $wi->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
			}
		}

//--------------------------------------------------------------------------
		//  wi_qty �ƶq��
		$where_str = " WHERE wi_id='".$op['wi']['id']."' ";
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

//�ɮצC��
		$op['done'] = $fils->get_file($op['wi']['wi_num']);
		 
		if(count($op['ti']))
		{
		 for ($i=0; $i<sizeof($op['ti']); $i++)
			$op['ti'][$i]['detail'] = str_replace( chr(13).chr(10), "<br>", $op['ti'][$i]['detail'] );
		}

		
			$op['msg'] = $wi->msg->get(2);
			$PHP_back_str = "&PHP_sr_startno=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh."&PHP_sch_fty=".$PHP_sch_fty."&PHP_un_fhs=".$PHP_un_fhs;
			$op['back_str']=$PHP_back_str;
			$PHP_back_str2 = "&PHP_sr=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh."&PHP_sch_fty=".$PHP_sch_fty."&PHP_un_fhs=".$PHP_un_fhs;
			$op['back_str2']=$PHP_back_str2;

		if (isset($PHP_ex_order))
		{
			$op['edit_non']=1;
		}

		if (isset($PHP_ti))
		{
			if (isset($PHP_msg))$op['msg'][]=$PHP_msg;
			page_display($op, 3, 5, $TPL_TI_VIEW);
		}else if (isset($PHP_cfm_view)){
			if (isset($PHP_msg))$op['msg'][]=$PHP_msg;
			page_display($op, 3, 3, $TPL_WI_CFM_VIEW);
		}else{
			page_display($op, 3, 2, $TPL_WI_VIEW);
		}

			
		break;
		
		
//=======================================================
    case "wi_print":   //  .......

		if(!$admin->is_power(3,2,"view")){ 
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
		//  wi_qty �ƶq��
			$where_str = " WHERE wi_id='".$wi['id']."' ";
		$T_wiqty = $wiqty->search(1,$where_str);

		$num_colors = count($T_wiqty);

		// ���X size_scale ----------------------------------------------
//		$where_str = " WHERE cust='".$wi['cust']."' AND size_scale='".$smpl['size']."' ";
		$size_A = $size_des->get($smpl['size']);
		$smpl['size_scale']=$size_A['size_scale'];
			//????????????????????????????????????? �`�N ���ɨS�� size type ��Ʈ�??????
		$sizing = explode(",", $size_A['size']);

			// ���X size table-------------------------------------------
		$reply = get_colorway_qty($T_wiqty,$sizing);

		$op['total'] = $reply['total'];   // �`���
		$data = $reply['data'];

		$colorway_name = $reply['colorway'];
		$colorway_qty = $reply['colorway_qty'];
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

	$header = array_merge(array('Colorway') , $sizing);	
	$table_title = "Size Breakdown";
	$R = 100;
	$G = 85;
	$B = 85;
	if (count($sizing)){
		$pdf->Table_1(10,70,$header,$data,$w,$table_title,$R,$G,$B,$size_A['base_size']);
	}else{
		$pdf->Cell(10,70,'there is no sizing data exist !',1);
	}

		$Y =$pdf->getY();
		// �w�q �ϥH�U���y�� 
		$pdf->SetXY($X,$Y+5);

// �D��
		$pdf->SetFont('Big5','',10);
if (sizeof($data) < 3)
{
	$xic=1;
}else{
	$xic=sizeof($data)-2;
}
	// �D�Ʃ��Y
if (count($lots)){
	$pdf->setx(10);
	$pdf->SetFont('Arial','B',14);
	$pdf->Cell(190,7,'Fabrics',0,1,'L');
	$pdf->Table_fab_title();
	$xic++;
	for ($i=0; $i<sizeof($lots); $i++)
	{
		if ($xic > 26)
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
		if ($xic > 26)
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
    case "ti_print":   //  .......

		if(!$admin->is_power(3,5,"view")){ 
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
		//  wi_qty �ƶq��
			$where_str = " WHERE wi_id='".$wi['id']."' ";
		$T_wiqty = $wiqty->search(1,$where_str);

		$num_colors = count($T_wiqty);

		// ���X size_scale ----------------------------------------------
		$size_A = $size_des->get($smpl['size']);
		$smpl['size_scale']=$size_A['size_scale'];
			//????????????????????????????????????? �`�N ���ɨS�� size type ��Ʈ�??????
		$sizing = explode(",", $size_A['size']);

			// ���X size table-------------------------------------------
		$reply = get_colorway_qty($T_wiqty,$sizing);

		$op['total'] = $reply['total'];   // �`���
		$data = $reply['data'];

		$colorway_name = $reply['colorway'];
		$colorway_qty = $reply['colorway_qty'];
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
		if (!count($op['ti']))
		{	$op['ti_NONE'] = "1";		
		}else{
			 for ($i=0; $i<sizeof($op['ti']); $i++)
				$op['ti'][$i]['detail'] = str_replace( chr(13).chr(10), "<br>", $op['ti'][$i]['detail'] );				

		}

		

//-----------------------------------------------------------------------
			// �ۤ���URL�M�w
				$style_dir	= "./picture/";  
				$no_img		= "./images/graydot.jpg";
			if(file_exists($style_dir.$wi['style_code'].".jpg")){
				$wi['pic_url'] = $style_dir.$wi['style_code'].".jpg";
			} else {
				$wi['pic_url'] = $no_img;
			}
		$op['done'] = $fils->get_file($wi['wi_num']);
		
//---------------------------------- ��Ʈw�@�~���� ------------------------------

include_once($config['root_dir']."/lib/class.pdf_ti.php");

$print_title = "WorkSheet";

$print_title2 = "VER.".$wi['ti_rev']."  on  ".$wi['ti_cfm'];
$creator = $wi['ti_cfm_user'];

$ary_title = array ('prt_ord'	=>	'Order #',
							 'id'				=>	$PHP_id,
							 'cust'			=>	$smpl['cust_iname'],
							 'ref'			=>	$smpl['ref'],
							 'dept'			=>	$smpl['dept'],
							 'size'			=>	$smpl['size_scale'],
							 'style'		=>	$smpl['style'],
							 'qty'			=>	$op['total'],
							 'unit'			=>	$wi['unit'],
							 'etd'			=>	$wi['etd'],
							 'img'			=>	$wi['pic_url'],							 
							 'img_size'	=>	GetImageSize($wi['pic_url']),
							);

$pdf=new PDF_ti();
$pdf->AddBig5Font();

$pdf->Open();
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
$pdf->SetCreator('angel'); 
//$pdf->SetAutoPageBreak(1,25);
// [��J] �q��򥻸��
/*
$pdf->hend_title($ary_title);

		$Y = $pdf->getY();
//		$pdf->setx(20);
		$img_size = GetImageSize($wi['pic_url']);
		
		if ($img_size[0] > $img_size[1])
		{
			$pdf->Image($wi['pic_url'],10,28,47,0);
		}else{
			$pdf->Image($wi['pic_url'],10,28,0,47);
		}

		$pdf->ln();
		$Y = $pdf->getY();
*/
		$X = $pdf->getX();

// �]�w colorway���ƶq�� - ��쪺�e  [size breakdown]
// �� 80~200 �O�d 30�� colorway���e �䥦 90���t���ƶq��
	$num_siz = count($sizing);
	$w_qty = intval(155/ ($num_siz+1));
	$w = array('40');  // �Ĥ@�欰 30�e
	for ($i=0;$i<=$num_siz;$i++){
		array_push($w, $w_qty);
	}

	$header = array_merge(array('Colorway') , $sizing);	
	$table_title = "Size Breakdown";
	$R = 100;
	$G = 85;
	$B = 85;
	if (count($sizing)){
		$pdf->Table_1(10,70,$header,$data,$w,$table_title,$R,$G,$B,$size_A['base_size']);
	}else{
		$pdf->Cell(10,70,'there is no sizing data exist !',1);
	}

		$Y =$pdf->getY();
		// �w�q �ϥH�U���y�� 
		$pdf->SetXY($X,$Y+5);


$pdf->ln();

//Worksheet���

if (sizeof($data) < 3)
{
	$x=7;
}else{
	$x=sizeof($data)-2+6;
}


$mark=0;
for ($i=0; $i<sizeof($TI_ITEM); $i++)
{
	for ($j=0; $j<sizeof($op['ti']); $j++)
	{
		if ($op['ti'][$j]['item']==$TI_ITEM[$i])
		{
			$mark=1;
			$star=$j;
			break;
		}
	}
	$tmp=$i+1;
	if($mark == 1)
	{
		$x=$x+2;

		if ($x > 35)//����
		{
			$pdf->setx(10);
			$pdf->Cell(190,5,'','LBR',0,'L');	
			$pdf->AddPage();		
			$pdf->SetFont('Arial','B',14);
			$x=4;
		}
		
		
		$pdf->ti_name_fields($tmp.")".$TI_ITEM[$i]);
		$pdf->SetFont('Big5','',10);
		for ($j=$star; $j<sizeof($op['ti']); $j++)
		{
			if ($op['ti'][$j]['item']==$TI_ITEM[$i])
			{
	/*		
				if ($x > 30)//����
				{
					$pdf->setx(10);
					$pdf->Cell(190,5,'','LBR',0,'L');	
					$pdf->AddPage();		
					$pdf->SetFont('Arial','B',14);
					$pdf->hend_title($PHP_id,$wi,$smpl,$op['total'],'Sample Order#','ti');
					if ($img_size[0] > $img_size[1])
					{
						$pdf->Image($wi['pic_url'],10,28,47,0);
					}else{
						$pdf->Image($wi['pic_url'],10,28,0,47);
					}

					$pdf->ln();		$pdf->ln();		$pdf->ln();
					$pdf->ti_name_fields($tmp.")".$TI_ITEM[$i]);
					$x=4;
				}
	*/			
				
				
				$x=$pdf->ti_value_fields($op['ti'][$j]['detail'],$x,$tmp,$TI_ITEM[$i]);
//				$x=$x+$rtn_ln;
			}


		}
		$pdf->setx(10);
		$pdf->Cell(190,5,'','LBR',0,'L');
		
		$pdf->ln();
		$mark = 0;
	}
}

$pdf->ln();
$x=$x+2;
$pdf->SetLineWidth(0.2);
$i=sizeof($op['done']);
if ($smpl['ptn_upload']<>'0000-00-00' && $smpl['paper_ptn']<> 1)
{

	$op['done'][$i]['file_name']=$smpl['order_num'].".mdl";
	$op['done'][$i]['file_des']="Order Pattern on : ".$smpl['ptn_upload'];
}
if (count($op['done']))
{


	if ($x > 35)//����
	{
			$pdf->AddPage();		
/*
			$pdf->SetFont('Arial','B',14);
			$pdf->hend_title($PHP_id,$wi,$smpl,$op['total'],'Sample Order#','ti');
			if ($img_size[0] > $img_size[1])
			{
				$pdf->Image($wi['pic_url'],10,28,47,0);
			}else{
				$pdf->Image($wi['pic_url'],10,28,0,47);
			}

			$pdf->ln();		$pdf->ln();		$pdf->ln();
			$x=4;
*/
	}

	$pdf->SetFont('Arial','B',14);
	$pdf->setx(10);
	$pdf->Cell(190,8,'Attached Files',1,0,'C');
	$pdf->ln();
	$pdf->setx(10);
	$pdf->Cell(10,8,'No.',1,0,'C');
	$pdf->Cell(85,8,'File name',1,0,'C');
	$pdf->Cell(95,8,'File Description',1,0,'C');
	$pdf->ln();
	$pdf->setx(10);
	$pdf->Cell(10,0.5,'',1,0,'C');
	$pdf->Cell(85,0.5,'',1,0,'C');
	$pdf->Cell(95,0.5,'',1,0,'C');
	$pdf->ln();
	$x=$x+2;
	for ($i=0; $i< sizeof($op['done']); $i++)
	{
		
		if ($x > 35)
		{
			$pdf->AddPage();		
/*
			$pdf->SetFont('Arial','B',14);
			$pdf->hend_title($PHP_id,$wi,$smpl,$op['total'],'Order #','ti');
			if ($img_size[0] > $img_size[1])
			{
				$pdf->Image($wi['pic_url'],10,28,47,0);
			}else{
				$pdf->Image($wi['pic_url'],10,28,0,47);
			}

			$pdf->ln();		$pdf->ln();		$pdf->ln();
*/
			$pdf->SetFont('Arial','B',14);
			$pdf->setx(10);
			$pdf->Cell(190,8,'Attached Files',1,0,'C');
			$pdf->ln();
			$pdf->setx(10);
			$pdf->Cell(10,8,'No.',1,0,'C');
			$pdf->Cell(85,8,'File name',1,0,'C');
			$pdf->Cell(95,8,'File Description',1,0,'C');
			$pdf->ln();
			$pdf->setx(10);
			$pdf->Cell(10,0.5,'',1,0,'C');
			$pdf->Cell(85,0.5,'',1,0,'C');
			$pdf->Cell(95,0.5,'',1,0,'C');
			$pdf->ln();
			$x=4;
		}
		
		
		
		$j=$i+1;
		$pdf->setx(10);		
		$pdf->Cell(10,5,$j,1,0,'C');
		$pdf->SetFont('Big5','',10);
		$pdf->Cell(85,5,$op['done'][$i]['file_name'],1,0,'L');
		$pdf->Cell(95,5,$op['done'][$i]['file_des'],1,0,'L');
		$pdf->SetFont('Arial','B',10);
		$pdf->ln();
		$x++;
	}
}

$name=$wi['wi_num'].'_ti.pdf';
$pdf->Output($name,'D');
break;	
//=======================================================
	case "wi_edit":

		check_authority(3,2,"edit");

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
		$where_str = " WHERE wi_id='".$PHP_id."' ";
		$T_wiqty = $wiqty->search(1,$where_str);

		// ���X size_scale --------
		$size_A = $size_des->get($op['smpl']['size']);
		$op['smpl']['size_scale']=$size_A['size_scale'];
			//????????????????????????????????????? �`�N ���ɨS�� size type ��Ʈ�??????
		$sizing = explode(",", $size_A['size']);
		$op['size'] = $sizing;
		$op['b_size'] = $size_A['base_size'];

//search����O��			
		if (isset($PHP_dept_code))
		{
				$PHP_back_str = "&PHP_sr_startno=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh."&PHP_sch_fty=".$PHP_sch_fty."&PHP_un_fhs=".$PHP_un_fhs;
				$op['back_str']=$PHP_back_str;
				$PHP_back_str2 = "&PHP_sr=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh."&PHP_sch_fty=".$PHP_sch_fty."&PHP_un_fhs=".$PHP_un_fhs;
				$op['back_str2']=$PHP_back_str2;
		}else{
				$op['back_str']=$PHP_back_str;
				$op['back_str2']=$PHP_back_str2;				
		}
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

		page_display($op, 3, 2, $TPL_WI_EDIT);
		break;
//=======================================================
		case "revise_wi":
		check_authority(3,2,"edit");
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
		
//�˵��O�_�i�H�ק�size(�Y�O�_�s�bcolorway)
		if ($wi->check_size($op['wi']['style_code']))
		{
			$op['size_edit']=1;
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
		
		//�˵��O�_�i�H�ק�size(�Y�O�_�s�bcolorway)
		if ($wi->check_size($op['wi']['style_code']))
		{
			$op['size_edit']=1;
		}

		//  wi_qty �ƶq�� -----------------------------------------------------------------
		$where_str = " WHERE wi_id='".$PHP_id."' ";
		$T_wiqty = $wiqty->search(1,$where_str);

		// ���X size_scale --------
		$size_A = $size_des->get($op['smpl']['size']);
		$op['smpl']['size_scale']=$size_A['size_scale'];
			//????????????????????????????????????? �`�N ���ɨS�� size type ��Ʈ�??????
		$sizing = explode(",", $size_A['size']);


//search����O��			
		if (isset($PHP_dept_code))
		{
				$PHP_back_str = "&PHP_sr_startno=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh."&PHP_sch_fty=".$PHP_sch_fty."&PHP_un_fhs=".$PHP_un_fhs;
				$op['back_str']=$PHP_back_str;
				$PHP_back_str2 = "&PHP_sr=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh."&PHP_sch_fty=".$PHP_sch_fty."&PHP_un_fhs=".$PHP_un_fhs;
				$op['back_str2']=$PHP_back_str2;
		}else{
				$op['back_str']=$PHP_back_str;
				$op['back_str2']=$PHP_back_str2;				
		}
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
		
		$op['new_revise'] = $op['wi']['revise']+1;

		page_display($op, 3, 2, $TPL_WI_REVISE);
		break;

//=======================================================
		case "do_wi_revise_show":


		check_authority(3,2,"edit");
		$argv = array(	"id"			=>  $PHP_wi_id,
						"wi_num"		=>	$PHP_wi_num,
						"smpl_id"		=>	$PHP_smpl_id,
						"version"		=>	$PHP_version,
						"etd"			=>	$PHP_etd,
						
						"unit"			=>	$PHP_unit,
						"lots_est_array"	=>	$PHP_lots_est,
						"acc_est_array"		=>	$PHP_acc_est,
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
		
//�ק�q��ETD   start
	$ord = $order->get($PHP_smpl_id);
	if($ord['etd']<>$argv['etd'])
	{
		if ($ord['status'] > 3) //�T�{�O�_�ݭn�ק�C��SU�O��
		{
				 $ord_pdt=$order->get_pdtion($ord['order_num'], $ord['factory']);
				 $etp_su=decode_mon_yy_su($ord_pdt['etp_su']); //���X�v�s�betp-su�ñNsu�ȻP�~,����}
				 for ($i=0; $i<sizeof($etp_su); $i++) //�N�v�s�bsu���~�װO���ȧR��
				 {				 	
				 	$f1=$capaci->delete_su($ord['factory'], $etp_su[$i]['year'], $etp_su[$i]['mon'], 'pre_schedule', $etp_su[$i]['su']);			 		
				 }
				 $div = $order->distri_month_su($ord['su'],$ord['etp'],$argv['etd'],$ord['factory'],'pre_schedule'); //����C��SU���x�s
				 $order->update_pdtion_field('etp_su', $div, $ord_pdt['id']);  //�x�s�setp_su
		}
		$order->update_field('etd', $argv['etd'], $PHP_smpl_id);
		$order->update_field('last_update',$TODAY,$PHP_smpl_id);
		$order->update_field('last_updator',$GLOBALS['SCACHE']['ADMIN']['login_id'],$PHP_smpl_id);

		$message="Successfully edit ETD & ETP on order :".$ord['order_num'];
		$log->log_add(0,"101E",$message);
	}
//�ק�q��ETD   end
		
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


		// �N�D�Ʈ� �ק�� �w���g�J �D�Ʈƨϥ��ɤ� lots_use, acc_use  ....
		// �D�� -----------------------------------------------------------------------------
		if(($argv['lots_est_array'])){
			while(list($key,$val) = each($argv['lots_est_array'])){
				if($val){
					$lots = array($key,"est_1",$val);
					if(!$smpl_lots->update_field($lots)){
						$op['msg']= $smpl_lots->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
					}

					$updt = array($key,"ester_1",$argv['updator']);
					if(!$smpl_lots->update_field($updt)){
						$op['msg']= $smpl_lots->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
					}
				}
			}
		}
		// �Ʈ� ------------------------------------------------------------------------------
		if(($argv['acc_est_array'])){
			while(list($key,$val) = each($argv['acc_est_array'])){
				if($val){
					$acc = array($key,"est_1",$val);
					if(!$smpl_acc->update_field($acc)){
						$op['msg']= $smpl_acc->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
					}

					$updt = array($key,"ester_1",$argv['updator']);
					if(!$smpl_acc->update_field($updt)){
						$op['msg']= $smpl_acc->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
					}
				}
			}
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

		page_display($op, 3, 2, $TPL_WI_REVISE_SHOW);
		break;
		
		
//=======================================================
		case "edit_del_colorway_ajax":

		check_authority(3,2,"edit");
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

		$message = "Success delete wi[".$PHP_wi_num."] colorway[".$colorway."]";
		$log->log_add(0,"31E",$message);
    echo $message."|".$size_edit;
		exit;
		break;
		
//==================================================================================


	case "edit_add_colorway":

		check_authority(3,2,"edit");
		// �ǤJ javascript�ǥX���Ѽ� [ colorway : ���M��J�w�� javascript�߬d ]
		$parm['qty']		= $PHP_qty;   // ��javascript�ǥX���}�C�� �۰ʧ令csv�ǥX
		$parm['wi_id']		= $wi_id;
		$parm['colorway']	= $colorway;

		// �[�J �s��� ----- �[�J wiqty table -----------------
		if(!$T_del =	$wiqty->add($parm)){    
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
//�˵��O�_�i�H�ק�size(�Y�O�_�s�bcolorway)
		if ($wi->check_size($op['wi']['style_code']))
		{
			$op['size_edit']=1;
		}

	//  ��s �s�y�O ���� ----- ��� wi table -----------------
			$edit_version[0] = $wi_id;
			$edit_version[1] = "version";
			$edit_version[2] = $op['wi']['version']+1;
	
				if(!$T_updt =	$wi->update_field($edit_version)){    
								$op['msg']= $wi->msg->get(2);
								$layout->assign($op);
								$layout->display($TPL_ERROR);  		    
								break;
				}
			$op['wi']['version'] = $edit_version[2];    // �N�s�� version �N�J

				# �O���ϥΪ̰ʺA
		$message = "Successfully add wi[".$op['wi']['wi_num']."] colorway[".$colorway."]�E";
		$log->log_add(0,"31E",$message);

		//  wi_qty �ƶq�� -----------------------------------------------------------------
		$where_str = " WHERE wi_id='".$wi_id."' ";
		$T_wiqty = $wiqty->search(1,$where_str);

		// ���X size_scale --------
		$size_A = $size_des->get($op['smpl']['size']);
		$op['smpl']['size_scale']=$size_A['size_scale'];
			//????????????????????????????????????? �`�N ���ɨS�� size type ��Ʈ�??????
		$sizing = explode(",", $size_A['size']);
		
		
		$PHP_back_str = "&PHP_sr_startno=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh."&PHP_sch_fty=".$PHP_sch_fty."&PHP_un_fhs=".$PHP_un_fhs;
		$op['back_str']=$PHP_back_str;
		$PHP_back_str2 = "&PHP_sr=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh."&PHP_sch_fty=".$PHP_sch_fty."&PHP_un_fhs=".$PHP_un_fhs;
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
			
		$op['msg'][] = $message;
		$op['new_revise'] = $op['wi']['revise'];
		
		if(!empty($PHP_revice))
		{
			page_display($op, 3, 2, $TPL_WI_REVISE);
		}else{
			page_display($op, 3, 2, $TPL_WI_EDIT); 
		}
		break;
//=======================================================
	case "do_wi_edit":

		check_authority(3,2,"edit");
		$argv = array(	"id"			=>  $PHP_wi_id,
						"wi_num"		=>	$PHP_wi_num,
						"smpl_id"		=>	$PHP_smpl_id,						
						"version"		=>	$PHP_version,
						"etd"			=>	$PHP_etd,
						
						"unit"			=>	$PHP_unit,
						"lots_est_array"	=>	$PHP_lots_est,
						"acc_est_array"		=>	$PHP_acc_est,
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
	// ���X unit�� combo
		$op['unit'] = $arry2->select($APPAREL_UNIT,$op['wi']['unit'],'PHP_unit','select',''); 
	// creat sample type combo box
		$sample_def = $smpl_type->get_fields('smpl_type');   // ���X �˥����O
		$op['smpl_type'] =  $arry2->select($sample_def,$op['wi']['smpl_type'],'PHP_smpl_type','select','');  	
		$op['msg'] = $wi->msg->get(2);
		page_display($op, 3, 1, $TPL_WI_EDIT); 
		break;		
	}	// ��J�����T--��J�� ���~ [ ��� ]- (((����)))


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
//�ק�q��ETD   start
	$ord = $order->get($PHP_smpl_id);
	if($ord['etd']<>$argv['etd'])
	{
		if ($ord['status'] > 3) //�T�{�O�_�ݭn�ק�C��SU�O��
		{
				 $ord_pdt=$order->get_pdtion($ord['order_num'], $ord['factory']);
				 $etp_su=decode_mon_yy_su($ord_pdt['etp_su']); //���X�v�s�betp-su�ñNsu�ȻP�~,����}
				 for ($i=0; $i<sizeof($etp_su); $i++) //�N�v�s�bsu���~�װO���ȧR��
				 {				 	
				 	$f1=$capaci->delete_su($ord['factory'], $etp_su[$i]['year'], $etp_su[$i]['mon'], 'pre_schedule', $etp_su[$i]['su']);			 		
				 }
				 $div = $order->distri_month_su($ord['su'],$ord['etp'],$argv['etd'],$ord['factory'],'pre_schedule'); //����C��SU���x�s
				 $order->update_pdtion_field('etp_su', $div, $ord_pdt['id']);  //�x�s�setp_su
		}
		$order->update_field('etd', $argv['etd'], $PHP_smpl_id);
		$order->update_field('last_update',$TODAY,$PHP_smpl_id);
		$order->update_field('last_updator',$GLOBALS['SCACHE']['ADMIN']['login_id'],$PHP_smpl_id);

		$message="Successfully edit ETD & ETP on order :".$ord['order_num'];
		$log->log_add(0,"101E",$message);
	}
//�ק�q��ETD   end
		
		$updt = array(	"etd"			=>	$argv['etd'],						
						"unit"			=>	$argv['unit'],
						"version"		=>	$argv['version'] +1,
						"updator"		=>	$argv['updator'],
						"id"			=>	$argv['id']
			);
				if(!$wi->edit($updt)){
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
				}


		// �N�D�Ʈ� �ק�� �w���g�J �D�Ʈƨϥ��ɤ� lots_use, acc_use  ....
		// �D�� -----------------------------------------------------------------------------
		if(($argv['lots_est_array'])){
			while(list($key,$val) = each($argv['lots_est_array'])){
				if($val){
					$lots = array($key,"est_1",$val);
					if(!$smpl_lots->update_field($lots)){
						$op['msg']= $smpl_lots->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
					}

					$updt = array($key,"ester_1",$argv['updator']);
					if(!$smpl_lots->update_field($updt)){
						$op['msg']= $smpl_lots->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
					}
				}
			}
		}
		// �Ʈ� ------------------------------------------------------------------------------
		if(($argv['acc_est_array'])){
			while(list($key,$val) = each($argv['acc_est_array'])){
				if($val){
					$acc = array($key,"est_1",$val);
					if(!$smpl_acc->update_field($acc)){
						$op['msg']= $smpl_acc->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
					}

					$updt = array($key,"ester_1",$argv['updator']);
					if(!$smpl_acc->update_field($updt)){
						$op['msg']= $smpl_acc->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
					}
				}
			}
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
			page_display($op, 3, 2, $TPL_WI_VIEW);	
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "wi_del":

			// ���ݭn manager login �~��u�����R�� SUPLIER  ----------------------------------
		if(!$admin->is_power(3,2,"del")  && !($GLOBALS['SCACHE']['ADMIN']['id'] == "SA" )) {
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
		$log->log_add(0,"21D",$message);
		
		$back_str = "index2.php?&PHP_action=wi_search&PHP_sr_startno=".$PHP_sr_startno."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh."&PHP_sch_fty=".$PHP_sch_fty."&PHP_un_fhs=".$PHP_un_fhs;
		redirect_page($back_str);
		break;





























//=======================================================
//		 �Ͳ�����
//=======================================================

//=======================================================
    case "ti":
		check_authority(3,4,"view");
		$where_str = $manager = $dept_id = '';		 
		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];   // �P�w�i�J����������
		$team = $GLOBALS['SCACHE']['ADMIN']['team_id'];  // �P�w�i�J����������(team)
		$sales_dept_ary = get_sales_dept();// ���X �~�Ȫ����� [���tK0] ------
		for ($i=0; $i<count($sales_dept_ary);$i++){
			if($user_dept == $sales_dept_ary[$i]){

				// �p�G�O�~�ȳ��i�J �hdept_code ���w�ӷ~�ȳ�---
				$dept_id = $sales_dept_ary[$i];  
			}
		}
		$op['dept_id'] = $dept_id;
		if (!$dept_id || $team<>'MD') {    // ���O�~�ȳ��H[�]���t K0 ���H ]�i�J��
			$op['manager_flag'] = 1;
			$manager_v = 1;
			//�~�ȳ��� select���
			$op['dept_id'] = $arry2->select($sales_dept_ary,"","PHP_dept_code","select",""); 
 
		} else {
			$where_str = " WHERE dept = '".$dept_id."' ";	
					
		}								
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

		page_display($op, 3,4, $TPL_TI_SEARCH);		    	    
		break;

//=======================================================
	case "ti_search_wi":   //  ����X�s�y�O.......

		check_authority(3,4,"view");

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

		$op['msg']= $wi->msg->get(2);				
		$back_str="&PHP_dept_code=".$PHP_dept_code."&PHP_etdfsh=".$PHP_etdfsh."&PHP_cust=".$PHP_cust."&PHP_wi_num=".$PHP_wi_num."&PHP_etdstr=".$PHP_etdstr."&PHP_fty_sch=".$PHP_fty_sch;
		$op['back_str']=$back_str;

		page_display($op, 3,4, $TPL_TI);
		break;

//=======================================================    
    case "ti_view":
		check_authority(3,4,"view");
		// �N �s�y�O ����show out ------
		//  wi �D��
		if(!$op = $wi->get_all($PHP_id)){    //���X�ӵ� �s�y�O�O��
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}

//--------------------------------------------------------------------------
		//  wi_qty �ƶq��
			$where_str = " WHERE wi_id='".$op['wi']['id']."' ";
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

		if(count($op['ti']))
		{
		 for ($i=0; $i<sizeof($op['ti']); $i++)
			$op['ti'][$i]['detail'] = str_replace( chr(13).chr(10), "<br>", $op['ti'][$i]['detail'] );
		}
	//�ɮצC��
		$op['done'] = $fils->get_file($op['wi']['wi_num']);
		
			$op['msg'] = $ti->msg->get(2);

			// creat combo
		$PHP_back_str = "&PHP_sr_startno=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_etdfsh=".$PHP_etdfsh."&PHP_cust=".$PHP_cust."&PHP_wi_num=".$PHP_wi_num."&PHP_etdstr=".$PHP_etdstr."&PHP_fty_sch=".$PHP_fty_sch;
		$op['back_str']=$PHP_back_str;
		$PHP_back_str2 = "&PHP_sr=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_etdfsh=".$PHP_etdfsh."&PHP_cust=".$PHP_cust."&PHP_wi_num=".$PHP_wi_num."&PHP_etdstr=".$PHP_etdstr."&PHP_fty_sch=".$PHP_fty_sch;
		$op['back_str2']=$PHP_back_str2;
		page_display($op, 3, 4, $TPL_TI_VIEW);
		break;

//=======================================================
		    
    case "ti_add":
		check_authority(3,4,"add");
		// �N �s�y�O ����show out ------
		//  wi �D��
		if(!$op = $wi->get_all($PHP_id)){    //���X�ӵ� �s�y�O�O��
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}

//--------------------------------------------------------------------------
		//  wi_qty �ƶq��
			$where_str = " WHERE wi_id='".$op['wi']['id']."' ";
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
		
	//�ɮצC��
		$op['done'] = $fils->get_file($op['wi']['wi_num']);
		
			$op['msg'] = $ti->msg->get(2);

			// creat combo
				$op['ti_item_select'] = $arry2->select($TI_ITEM,'','PHP_item','select',''); 
		if (isset($PHP_dept_code))
		{
				$PHP_back_str = "&PHP_sr_startno=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_etdfsh=".$PHP_etdfsh."&PHP_cust=".$PHP_cust."&PHP_wi_num=".$PHP_wi_num."&PHP_etdstr=".$PHP_etdstr."&PHP_fty_sch=".$PHP_fty_sch;
				$op['back_str']=$PHP_back_str;
				$PHP_back_str2 = "&PHP_sr=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_etdfsh=".$PHP_etdfsh."&PHP_cust=".$PHP_cust."&PHP_wi_num=".$PHP_wi_num."&PHP_etdstr=".$PHP_etdstr."&PHP_fty_sch=".$PHP_fty_sch;
				$op['back_str2']=$PHP_back_str2;
		}else{
				$op['back_str']=$PHP_back_str;
				$op['back_str2']=$PHP_back_str2;				
		}
		page_display($op, 3, 4, $TPL_TI_ADD);
		break;

//=======================================================
    case "do_ti_add":
		check_authority(3,4,"add");
		$parm = array(	"wi_id"		=>	$PHP_wi_id,
						"item"		=>	$PHP_item,
						"detail"	=>	$PHP_detail
			);
		// ���X ���Ѫ���� -----
		$dt = decode_date(1);
		$parm['today'] = $dt['date'];


		$op['ti0'] = $parm;    // �׶}�P��Ʈw�I�s�X�Ӥ��O���Ĭ�.....

		$f1 = $ti->add($parm);
	if (!$f1) {   // ���J�������D��........
		$op['ti_item_select'] = $arry2->select($TI_ITEM,$op['ti0']['item'],'PHP_item','select','');  	
	} else {
		$op['ti_item_select'] = $arry2->select($TI_ITEM,'','PHP_item','select',''); 
		$op['ti0']['detail'] = "";
	}

	// �N �s�y�O ����show out ------
		//  wi �D��
		if(!$op = $wi->get_all($PHP_wi_id)){    //���X�ӵ� �s�y�O�O��
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}
//--------------------------------------------------------------------------
		//  wi_qty �ƶq��
			$where_str = " WHERE wi_id='".$op['wi']['id']."' ";
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


//�ɮצC��
		$op['done'] = $fils->get_file($op['wi']['wi_num']);

		$op['msg'] = $ti->msg->get(2);

	// creat combo
		$op['ti_item_select'] = $arry2->select($TI_ITEM,'','PHP_item','select',''); 


					# �O���ϥΪ̰ʺA
		$message = "Add WI:[".$op['wi']['wi_num']."] Description�C";
		$log->log_add(0,"31A",$message);
		$op['back_str']=$PHP_back_str;
		$op['back_str2']=$PHP_back_str2;
			page_display($op, 3, 4, $TPL_TI_ADD);  
		break;
		
		

//=======================================================
    case "do_ti_edit":
		check_authority(3,4,"add");
		
		$parm['field_name'] = 'detail';
		$parm['field_value'] = $PHP_detail;
		$parm['id'] = $PHP_id;

		$f1 = $ti->update_field($parm);

	$op['ti_item_select'] = $arry2->select($TI_ITEM,'','PHP_item','select',''); 
	$op['ti0']['detail'] = "";
	// �N �s�y�O ����show out ------
		//  wi �D��
		if(!$op = $wi->get_all($PHP_wi_id)){    //���X�ӵ� �s�y�O�O��
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}
//--------------------------------------------------------------------------
		//  wi_qty �ƶq��
		$where_str = " WHERE wi_id='".$op['wi']['id']."' ";
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


//�ɮצC��
		$op['done'] = $fils->get_file($op['wi']['wi_num']);
		
		$op['msg'][] = "successfully edit worksheet";

	// creat combo
		$op['ti_item_select'] = $arry2->select($TI_ITEM,'','PHP_item','select',''); 

					# �O���ϥΪ̰ʺA
		$message = "Add WI:[".$op['wi']['wi_num']."] Description�C";
		$log->log_add(0,"31E",$message);
		$op['back_str']=$PHP_back_str;
		$op['back_str2']=$PHP_back_str2;
			page_display($op, 3, 4, $TPL_TI_ADD);  
		break;


//=======================================================
	case "do_ti_cfm":
		check_authority(3,4,"view");

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
		$log->log_add(0,"23A",$message);
		$redir_str=$PHP_SELF.'?PHP_action=ti_view&PHP_id='.$PHP_id.$PHP_back_str2.'&PHP_ti=1&PHP_msg='.$message;
		redirect_page($redir_str);
		break;
		
//=======================================================
	case "revise_ti":
		check_authority(3,4,"edit");
		
		$PHP_rev++;		//revies����+1
		$parm = array($PHP_id,'ti_rev',$PHP_rev);		//revies����+1�s�J��Ʈw
		$f1 = $wi->update_field($parm);
		if (!$f1) {
			$op['msg']= $wi->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}		

	
		$parm = array($PHP_id,'ti_cfm','0000-00-00');	//Worksheet�ܦ���cfm
		$f1 = $wi->update_field($parm);
		$parm = array($PHP_id,'ti_cfm_user','');	//Worksheet�ܦ���cfm
		$f1 = $wi->update_field($parm);
		if (!$f1) {
			$op['msg']= $wi->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}		
		$back_str2 = "&PHP_sr=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_etdfsh=".$PHP_etdfsh."&PHP_cust=".$PHP_cust."&PHP_wi_num=".$PHP_wi_num."&PHP_etdstr=".$PHP_etdstr."&PHP_fty_sch=".$PHP_fty_sch;
		$message="success Revise Worksheet:[".$PHP_wi."]";
		$log->log_add(0,"23A",$message);
		$redir_str=$PHP_SELF.'?PHP_action=ti_add&PHP_id='.$PHP_id.$back_str2.'&PHP_msg='.$message;
		redirect_page($redir_str);
		break;





//=======================================================
    case "do_ti_del":
		
		check_authority(3,4,"edit");
		$parm = array(	"id"	=>	$id,		
						"wi_id" =>	$wi_id,
						"item"	=>	$item
		);

		$f1 = $ti->del($parm['id']);
		if (!$f1) {
			$op['msg'] = $ti->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}


		// �N �s�y�O ����show out ------
		//  wi �D��
		if(!$op = $wi->get_all($parm['wi_id'])){    //���X�ӵ� �s�y�O�O��
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}

//--------------------------------------------------------------------------
		//  wi_qty �ƶq��
			$where_str = " WHERE wi_id='".$op['wi']['id']."' ";
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

//�ɮצC��
		$op['done'] = $fils->get_file($op['wi']['wi_num']);
		
			$op['msg'] = $ti->msg->get(2);
			$op['msg'][] = "Success delete [".$parm['item']."]Description !";

				// creat combo
			$op['ti_item_select'] = $arry2->select($TI_ITEM,'','PHP_item','select','');  	

					# �O���ϥΪ̰ʺA
		$message = "Delete WI:[".$op['wi']['wi_num']."] Description[".$parm['item']."]�C";
		$log->log_add(0,"31D",$message);
		$PHP_back_str = "&PHP_sr_startno=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_etdfsh=".$PHP_etdfsh."&PHP_cust=".$PHP_cust."&PHP_wi_num=".$PHP_wi_num."&PHP_etdstr=".$PHP_etdstr."&PHP_fty_sch=".$PHP_fty_sch;
		$op['back_str']=$PHP_back_str;
		$PHP_back_str2 = "&PHP_sr=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_etdfsh=".$PHP_etdfsh."&PHP_cust=".$PHP_cust."&PHP_wi_num=".$PHP_wi_num."&PHP_etdstr=".$PHP_etdstr."&PHP_fty_sch=".$PHP_fty_sch;
		$op['back_str2']=$PHP_back_str2;


			page_display($op, 3, 4, $TPL_TI_ADD);	
		break;



//=======================================================
    case "do_ws_file_del":
		
	//	check_authority(3,1,"edit");	

		$f1 = $fils->del_file($PHP_talbe,$PHP_id);
		if (!$f1) {
			$op['msg'] = $fils->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}


		// �N �s�y�O ����show out ------
		//  wi �D��
		if(!$op = $wi->get_all($PHP_wi_id)){    //���X�ӵ� �s�y�O�O��
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}

//--------------------------------------------------------------------------
		//  wi_qty �ƶq��
			$where_str = " WHERE wi_id='".$op['wi']['id']."' ";
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

//�ɮצC��
		$op['done'] = $fils->get_file($op['wi']['wi_num']);
		
			$op['msg'] = $ti->msg->get(2);
			$op['msg'][] = "Delete File form :[".$op['wi']['wi_num']."] �C";

				// creat combo
			$op['ti_item_select'] = $arry2->select($TI_ITEM,'','PHP_item','select','');  	

					# �O���ϥΪ̰ʺA
		$message = "Delete File form :[".$op['wi']['wi_num']."] �C";
		$log->log_add(0,"31D",$message);
		$PHP_back_str = "&PHP_sr_startno=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_etdfsh=".$PHP_etdfsh."&PHP_cust=".$PHP_cust."&PHP_wi_num=".$PHP_wi_num."&PHP_etdstr=".$PHP_etdstr."&PHP_fty_sch=".$PHP_fty_sch;
		$op['back_str']=$PHP_back_str;
		$PHP_back_str2 = "&PHP_sr=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_etdfsh=".$PHP_etdfsh."&PHP_cust=".$PHP_cust."&PHP_wi_num=".$PHP_wi_num."&PHP_etdstr=".$PHP_etdstr."&PHP_fty_sch=".$PHP_fty_sch;
		$op['back_str2']=$PHP_back_str2;


			page_display($op, 3, 4, $TPL_TI_ADD);	
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		job 24A sample pattern upload
#		case "upload_smpl_pattern":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "upload_order_file":

		check_authority(3,4,"add");

		
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
																		
		if ($_FILES['PHP_pttn']['size'] < 2072864)
		{
		if ($f_check == 1){   // �W���ɪ����ɦW�� mdl �� -----

			// upload pattern file to server
			$today = $GLOBALS['TODAY'];
			$user =  $GLOBALS['SCACHE']['ADMIN']['name'];
			$parm = array(	"file_name"		=>  $PHP_num,				
							"num"			=>  $PHP_num,
							"file_des"		=>	$PHP_desp,
							"file_user"		=>	$user,
							"file_date"		=>	$today
			);

				if (!$A = $fils->upload_file($parm)){
				$op['msg'] = $fils->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			
			$pttn_name = $PHP_num."_".$A.".".$ext;  // �զX�ɦW
			$fils->	update_file($pttn_name,$A);
			
			$str_long=strlen($pttn_name);
			$upload = new Upload;
			
			$upload->setMaxSize(2072864);
			$upload->uploadFile(dirname($PHP_SELF).'/wi_file/', 'other', 16, $pttn_name );
			$upload->setMaxSize(2072864);
			if (!$upload){
				$op['msg'][] = $upload;
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			$message = "UPLOAD file of #".$PHP_num;
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
			
		
		// �N �s�y�O ����show out ------
		//  wi �D��
		if(!$op = $wi->get_all(0,$PHP_num)){    //���X�ӵ� �s�y�O�O��
					$op['msg']= $wi->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
		}
		
		
//--------------------------------------------------------------------------
		//  wi_qty �ƶq��
			$where_str = " WHERE wi_id='".$op['wi']['id']."' ";
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

		
//�ɮצC��
		$op['done'] = $fils->get_file($op['wi']['wi_num']);
			$op['msg'][] = $message;
				// creat combo
			$op['ti_item_select'] = $arry2->select($TI_ITEM,'','PHP_item','select','');  	
		$op['back_str']=$PHP_back_str;
		$op['back_str2']=$PHP_back_str2;
		page_display($op, 3, 4, $TPL_TI_ADD);	
		break;
		

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//   wi_cfm      //  WI CFM...........
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "wi_uncfm":
		check_authority(3,3,"view");
		
		if (!$op = $wi->search_uncfm('wi')) {
			$op['msg']= $wi->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		for ($i=0; $i<sizeof($op['wi']); $i++)
		{
			$where_str="WHERE smpl_code = '".$op['wi'][$i]['wi_num']."'";
			$op['wi'][$i]['lots']=$smpl_lots->get_fields('id',$where_str);
			$op['wi'][$i]['acc']=$smpl_acc->get_fields('id',$where_str);
		}

		$PHP_back_str = "&PHP_sr=&PHP_dept_code=&PHP_num=&PHP_cust=&PHP_etdstr=&PHP_etdfsh=&PHP_sch_fty=&PHP_un_fhs=";
		//2006/05/02 update
		$op['dept_id'] = get_dept_id();
		
		// �p�G�O manager �i�J��...
		if (substr($op['dept_id'],0,7) == "<select"){
			$op['manager_flag'] = 1;
		}	
				$op['msg']= $wi->msg->get(2);
				$op['back_str']= $PHP_back_str;
			page_display($op, 3, 3, $TPL_WI_UNCFM_LIST);
		break;




//========================================================================================	
	case "wi_cfm_qty_edit":

		check_authority(3,2,"edit");

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
		$where_str = " WHERE wi_id='".$op['wi']['id']."' ";
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
		
			$op['msg'] = $wi->msg->get(2);
			$PHP_back_str = "&PHP_sr_startno=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh."&PHP_sch_fty=".$PHP_sch_fty."&PHP_un_fhs=".$PHP_un_fhs;
			$op['back_str']=$PHP_back_str;
			$PHP_back_str2 = "&PHP_sr=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh."&PHP_sch_fty=".$PHP_sch_fty."&PHP_un_fhs=".$PHP_un_fhs;
			$op['back_str2']=$PHP_back_str2;
			if (isset($PHP_msg))$op['msg'][]=$PHP_msg;
		page_display($op, 3, 2, $TPL_WI_QTY_EDIT);			
		break;
		
		
		

//========================================================================================	
	case "edit_color_qty":

		check_authority(3,2,"edit");
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

		if ($s_ord['qty'] <>$PHP_ord_qty)
		{
			$su=(int)($PHP_ord_qty*$s_ord['ie1']);
			
			$ord_pdt = $order->get_pdtion($s_ord['order_num'], $s_ord['factory']);
			if ($s_ord['status'] >= 4 && $s_ord['status'] <> 5)
			{	//ETP~ETD��SU							 
				 $etp_su=decode_mon_yy_su($ord_pdt['etp_su']); //���X�v�s�betp-su�ñNsu�ȻP�~,����}
				 for ($i=0; $i<sizeof($etp_su); $i++) //�N�v�s�bsu���~�װO���ȧR��
				 {				 	
				 	$f1=$capaci->delete_su($s_ord['factory'], $etp_su[$i]['year'], $etp_su[$i]['mon'], 'pre_schedule', $etp_su[$i]['su']);			 		
				 }
				 $div = $order->distri_month_su($su,$s_ord['etp'],$s_ord['etd'],$s_ord['factory'],'pre_schedule'); //����C��SU���x�s
				 $order->update_pdtion_field('etp_su', $div, $ord_pdt['id']);  //�x�s�setp_su
			}
			if($s_ord['status'] >= 7){
				 //ETS~ETF��SU(�u�t�Ƶ{)
				 $fty_su=decode_mon_yy_su($ord_pdt['fty_su']);//���X�v�s�bfty-su�ñNsu�ȻP�~,����}
				 for ($i=0; $i<sizeof($fty_su); $i++)//�N�v�s�bsu���~�װO���ȧR��
				 {				 	
				 	$f1=$capaci->delete_su($s_ord['factory'], $fty_su[$i]['year'], $fty_su[$i]['mon'], 'schedule', $fty_su[$i]['su']);			 		
				 }
				 $div = $order->distri_month_su($su,$ord_pdt['ets'],$ord_pdt['etf'],$s_ord['factory'],'schedule');//����C��SU���x�s
				 $order->update_pdtion_field('fty_su', $div, $ord_pdt['id']);//�x�s�sfty_su
				 
			}

			// �g�J qty�Msu
			$order->update_field('qty', $PHP_ord_qty, $PHP_ord_id);
			$order->update_field('su', $su, $PHP_ord_id);
			
		}
		$message="success edit order qty on wi cfm:[".$s_ord['order_num']."]";
		$log->log_add(0,"101A",$message);	
		$log->log_add(0,"31A",$message);	
			
			$op['msg'] = $wi->msg->get(2);
			$PHP_back_str2 = "&PHP_sr=".$PHP_sr."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh."&PHP_sch_fty=".$PHP_sch_fty."&PHP_un_fhs=".$PHP_un_fhs;
			
			$redir_str=$PHP_SELF.'?PHP_action=wi_view&PHP_id='.$PHP_wi_id.$PHP_back_str2.'&PHP_cfm_view=1&PHP_msg='.$message;
			redirect_page($redir_str);
			break;

//=======================================================
	case "do_wi_cfm":
		check_authority(3,3,"view");
		
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
		//		echo "here";
				$redir_str=$PHP_SELF.'?PHP_action=wi_cfm_qty_edit&PHP_id='.$PHP_id.$PHP_back_str2.'&PHP_cfm_view=1&PHP_msg='.$PHP_msg;
				redirect_page($redir_str);
				break;				
			}else{
				$redir_str=$PHP_SELF.'?PHP_action=wi_view&PHP_id='.$PHP_id.$PHP_back_str2.'&PHP_cfm_view=1&PHP_msg='.$PHP_msg;
				redirect_page($redir_str);
				break;
			}
		}
		}
		
		$f1 = $wi->cfm($parm);
		if (!$f1) {
			$op['msg']= $wi->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$message="success CFM WI:[".$PHP_wi."]";
		$log->log_add(0,"33A",$message);	

			$parm_update = array($PHP_id,'last_update',$dt['date_str']);
			$wi->update_field($parm_update);
			$parm_update = array($PHP_id,'updator',$GLOBALS['SCACHE']['ADMIN']['login_id']);
			$wi->update_field($parm_update);


		if (!$op = $wi->search_uncfm('wi')) {
			$op['msg']= $wi->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		if ($PHP_revise == 0)
		{
				$parm=array($PHP_id,'revise','1');
				$wi->update_field($parm);
		}
		for ($i=0; $i<sizeof($op['wi']); $i++)
		{
			$where_str="WHERE smpl_code = '".$op['wi'][$i]['wi_num']."'";
			$op['wi'][$i]['lots']=$smpl_lots->get_fields('id',$where_str);
			$op['wi'][$i]['acc']=$smpl_acc->get_fields('id',$where_str);
		}

		//2006/05/02 update
		$op['dept_id'] = get_dept_id();
		$PHP_back_str = "&PHP_sr=&PHP_dept_code=&PHP_num=&PHP_cust=&PHP_etdstr=&PHP_etdfsh=&PHP_sch_fty=&PHP_un_fhs=";
		
		$op['msg'][]= $message;
		$op['back_str']= $PHP_back_str;
		page_display($op, 3, 3, $TPL_WI_UNCFM_LIST);
	break;



































//================= ���F �s�W�s�y�O �ϥ� =============
    case "show_size_des_search":

		if(!$admin->is_power(3,2,"add")){ 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$where_str = " AND cust='".$PHP_cust."' AND dept='".$PHP_dept."' ";
		if (!$op = $size_des->search(0,$where_str)) {    // �s�X���ݳ����������Ȥ�O��
			$op['msg']= $size_des->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$op['msg']= $size_des->msg->get(2);
		$op['dept'] = $PHP_dept;
		$op['cust'] = $PHP_cust;

		$layout->assign($op);
		$layout->display($TPL_SIZE_DES_LIST);		    	    
	break;




#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//   size_des ����      //  2005/01/13 �}�l�ק�...........
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "size_des":
 
		check_authority(7,5,"view");
		
		$where_str = $manager = $dept_id = '';		 
		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];   // �P�w�i�J����������
		$sales_dept_ary = get_sales_dept();// ���X �~�Ȫ����� [���tK0] ------
		for ($i=0; $i<count($sales_dept_ary);$i++){
			if($user_dept == $sales_dept_ary[$i]){

				// �p�G�O�~�ȳ��i�J �hdept_code ���w�ӷ~�ȳ�---
				$dept_id = $sales_dept_ary[$i];  
			}
		}
		$op['dept_id'] = $dept_id;
		if (!$dept_id) {    // ���O�~�ȳ��H[�]���t K0 ���H ]�i�J��
			if (!$op = $size_des->search(0)) {    // �s�X�����Ȥ�O��
				$op['msg']= $size_des->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			$op['mag_falg'] = 1;
			$dept_def = $dept->get_fields('dept_code');   // ���X�����������O�N��
			$op['dept_id'] = $arry2->select($dept_def,"","PHP_dept_code","select","");  //�����Oselect���
		} else {
			$where_str = " AND dept = '".$_SESSION['SCACHE']['ADMIN']['dept']."'";
			if (!$op = $size_des->search(0,$where_str)) {    // �s�X���ݳ����������Ȥ�O��
				$op['msg']= $size_des->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			$op['dept_id'] = $_SESSION['SCACHE']['ADMIN']['dept'];
		}

			$op['msg'] = $size_des->msg->get(2);
			if (isset($PHP_msg))$op['msg'][]=$PHP_msg;
			page_display($op, 7, 5, $TPL_SIZE_DES);		    	    
		break;
//=======================================================
    case "size_des_add":
		
		check_authority(7,5,"add");
		$un_choice=0;
		if ($PHP_dept_code ==""){
			$message="please choice dept first";
			$un_choice=1;	    
		}else{
			$op['dept_code'] = $PHP_dept_code;
			// create cust combo box
			$where_str =" WHERE dept ='$PHP_dept_code' ";
			$cust_def = $cust->get_fields('cust_s_name',$where_str);   // ���X �Ȥ�N��
			$op['cust_select'] =  $arry2->select($cust_def,'','PHP_cust','select','');  	
			if (!isset($cust_def[0]))
			{
				$message = "sorry! This Department hasn't customer, please add customer fist.";
				$un_choice=1;
			}
		}
		if ($un_choice==1)
		{
					$where_str = $manager = $dept_id = '';		 
		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];   // �P�w�i�J����������
		$sales_dept_ary = get_sales_dept();// ���X �~�Ȫ����� [���tK0] ------
		for ($i=0; $i<count($sales_dept_ary);$i++){
			if($user_dept == $sales_dept_ary[$i]){

				// �p�G�O�~�ȳ��i�J �hdept_code ���w�ӷ~�ȳ�---
				$dept_id = $sales_dept_ary[$i];  
			}
		}
		$op['dept_id'] = $dept_id;
		if (!$dept_id) {    // ���O�~�ȳ��H[�]���t K0 ���H ]�i�J��
			if (!$op = $size_des->search(0)) {    // �s�X�����Ȥ�O��
				$op['msg']= $size_des->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			$op['mag_falg'] = 1;
			$dept_def = $dept->get_fields('dept_code');   // ���X�����������O�N��
			$op['dept_id'] = $arry2->select($dept_def,"","PHP_dept_code","select","");  //�����Oselect���
		} else {
			$where_str = " AND dept = '".$_SESSION['SCACHE']['ADMIN']['dept']."'";
			if (!$op = $size_des->search(0,$where_str)) {    // �s�X���ݳ����������Ȥ�O��
				$op['msg']= $size_des->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			$op['dept_id'] = $_SESSION['SCACHE']['ADMIN']['dept'];
		}

			$op['msg'][] = $message;
			page_display($op, 7, 5, $TPL_SIZE_DES);		    	    
		break;
		}
			page_display($op, 7, 5, $TPL_SIZE_DES_ADD);		    	    
		break;
//=======================================================
	case "do_size_des_add":

		check_authority(7,5,"add");
			# size  ��z  -------------
				$size_str = "";
				$size_check=0;
				$PHP_base_size = strtoupper($PHP_base_size);	
				for ($i=0;$i<count($PHP_size);$i++)	{
					$PHP_size[$i] = strtoupper($PHP_size[$i]);	
					if ($PHP_size[$i]){
						$size_str = $size_str.",".$PHP_size[$i];
						if ($PHP_size[$i] == $PHP_base_size) $size_check=1;
					}
				}

			$size_str = substr($size_str,1);   // �h���̫e�� ","
			$new_size = explode(',',$size_str);   // ��z�᪺�ؽX�}�C
			$size_scale = $new_size[0]." ~ ".$new_size[count($new_size)-1];
			//-----------------------------------------------------------------

		$parm = array(	"dept_code"		=>	$PHP_dept_code,
						"dept"			=>	$PHP_dept_code,
						"cust"			=>	$PHP_cust,
						"size"			=>	$size_str,
						"size_scale"	=>	$size_scale,
						"base_size"		=>	$PHP_base_size,
						"check"			=>	$size_check,
				);

				$op['size_des'] = $parm;
				$op['size_des']['size'] = $new_size;    // �}�C �ন op

		$f1 = $size_des->add($parm);
		if ($f1) {  // ���\��J��Ʈ�

			$message= "Successfully add customer:[".$parm['cust']."] size record";

					# �O���ϥΪ̰ʺA
			$log->log_add(0,"75A",$message);

		$where_str = $manager = $dept_id = '';		 
		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];   // �P�w�i�J����������
		$sales_dept_ary = get_sales_dept();// ���X �~�Ȫ����� [���tK0] ------
		for ($i=0; $i<count($sales_dept_ary);$i++){
			if($user_dept == $sales_dept_ary[$i]){

				// �p�G�O�~�ȳ��i�J �hdept_code ���w�ӷ~�ȳ�---
				$dept_id = $sales_dept_ary[$i];  
			}
		}
		$op['dept_id'] = $dept_id;
		if (!$dept_id) {    // ���O�~�ȳ��H[�]���t K0 ���H ]�i�J��
			if (!$op = $size_des->search(0)) {    // �s�X�����Ȥ�O��
				$op['msg']= $size_des->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			$op['mag_falg'] = 1;
			$dept_def = $dept->get_fields('dept_code');   // ���X�����������O�N��
			$op['dept_id'] = $arry2->select($dept_def,"","PHP_dept_code","select","");  //�����Oselect���
		} else {
			$where_str = " AND dept = '".$_SESSION['SCACHE']['ADMIN']['dept']."'";
			if (!$op = $size_des->search(0,$where_str)) {    // �s�X���ݳ����������Ȥ�O��
				$op['msg']= $size_des->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			$op['dept_id'] = $_SESSION['SCACHE']['ADMIN']['dept'];
		}

			$op['msg'][] = $message;
			page_display($op, 7, 5, $TPL_SIZE_DES);
		break;
	
		}	else {  // ��S�����\��J�s�Wuser������....

			$op['size_des'] = $parm;
			$op['size_des']['size'] = $new_size;    // �}�C �ন op
			$op['dept_code'] = $parm['dept_code'];
				// create cust combo box
				$where_str =" WHERE dept ='$PHP_dept_code' ";
				$cust_def = $cust->get_fields('cust_s_name',$where_str);   // ���X �Ȥ�N��
				$op['cust_select'] =  $arry2->select($cust_def,$PHP_cust,'PHP_cust','select','');  	

			$op['msg'] = $size_des->msg->get(2);
	
			page_display($op, 7, 5, $TPL_SIZE_DES_ADD);	    	    
		break;

		}
//========================================================================================	
	case "size_des_view":

		check_authority(7,5,"view");
		$op['size_des'] = $size_des->get($PHP_id);  //���X�ӵ��O��

		if (!$op['size_des']) {
			$op['msg'] = $size_des->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
			$op['msg'] = $size_des->msg->get(2);	
			page_display($op, 7, 5, $TPL_SIZE_DES_VIEW);	    	    
		break;
//=======================================================
	case "size_des_edit":		
		check_authority(7,5,"edit");
		$op['size_des'] = $size_des->get($PHP_id);
		if (!$op['size_des']) {
			$op['msg'] = $size_des->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

			// �B�z size �����D ---------------
			$op['size_des']['size']  = explode(",",$op['size_des']['size']); 
			if (isset($PHP_ex_edit))$op['ex_edit']=1;
			$op['msg'] = $size_des->msg->get(2);
  			$op['now_pp']=$PHP_pp;
		page_display($op, 7, 5, $TPL_SIZE_DES_EDIT);
		break;

//=======================================================
	case "do_size_des_edit":

		check_authority(7,5,"edit");
			# size  ��z  -------------
				$size_str = "";
				$size_check=0;
				$PHP_base_size = strtoupper($PHP_base_size);	
				for ($i=0;$i<count($PHP_size);$i++)	{
					$PHP_size[$i] = strtoupper($PHP_size[$i]);	

					if ($PHP_size[$i]){
						$size_str = $size_str.",".$PHP_size[$i];
						if ($PHP_size[$i] == $PHP_base_size) $size_check=1;
					}
				}
			$size_str = substr($size_str,1);   // �h���̫e�� ","
			$new_size = explode(',',$size_str);   // ��z�᪺�ؽX�}�C
			$size_scale = $new_size[0]." ~ ".$new_size[count($new_size)-1];
			//----------------------------------

		$argv = array(	"id"			=>  $PHP_id,
						"size"			=>	$size_str,
						"size_scale"	=>	$size_scale,
						"base_size"		=>	$PHP_base_size,
						"check"			=>	$size_check,

				);


		if (!$size_des->edit($argv)) {
			$op['msg'] = $size_des->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;	    
		}


					# �O���ϥΪ̰ʺA
		$message = "Successfully update customer:[$PHP_cust]size:[$size_scale]record�E";
		$log->log_add(0,"75E",$message);

		// �C�X�O��~~~

		unset($argv);  // �R�� �Ѽ�  
	if ($PHP_ex_edit)
	{
		$op['size_des'] = $size_des->get($PHP_id);
		if (!$op['size_des']) {
			$op['msg'] = $size_des->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

			// �B�z size �����D ---------------
			$op['size_des']['size']  = explode(",",$op['size_des']['size']); 
			if (isset($PHP_ex_edit))$op['ex_edit']=1;
			$op['msg'] = $size_des->msg->get(2);
  
		page_display($op, 7, 5, $TPL_SIZE_DES_SHOW);
	}else{
		$where_str = $manager = $dept_id = '';		 
		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];   // �P�w�i�J����������
		$user_team = $GLOBALS['SCACHE']['ADMIN']['team_id'];   // �P�w�i�J����������
		$sales_dept_ary = get_sales_dept();// ���X �~�Ȫ����� [���tK0] ------		
		if ($user_team=='MD')
		{
			$dept_id =  $_SESSION['SCACHE']['ADMIN']['dept'];
			
			$where_str = " AND dept = '".$_SESSION['SCACHE']['ADMIN']['dept']."'";			
		}else{			
			$manager = 1;
			$dept_def = $dept->get_fields('dept_code');   // ���X�����������O�N��
			$dept_id = $arry2->select($dept_def,"","PHP_dept_code","select","");  //�����Oselect���
		}
		if (!$op = $size_des->search(0,$where_str)) {    // �s�X�����Ȥ�O��
				$op['msg']= $size_des->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
		}
		$op['mag_falg'] =$manager;
		$op['dept_id']=$dept_id;
		page_display($op, 7, 5, $TPL_SIZE_DES);
	}
		break;
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "size_des_del":
						// ���ݭn manager login �~��u�����R�� user
		if(!$admin->is_power(7,5,"del") && !($GLOBALS['SCACHE']['ADMIN']['id'] == "SA" )) { 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$subj = $size_des->get($PHP_id);
		
		if (!$size_des->del($PHP_id)) {
			$op['msg'] = $size_des->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;	    
		}
					# �O���ϥΪ̰ʺA

		$message = "Delete Customer:[".$subj['cust']."] size:[".$subj['size_scale']."] record�C";
		$log->log_add(0,"75D",$message);
		$redirect="index2.php?PHP_action=size_des&PHP_sr_startno=".$PHP_sr_startno."&PHP_msg=".$message;
		redirect_page($redirect);
	break;

















#************************************************
#
#	�R��  begining~~
#
#*************************************************
//===temp backup delete begining   ======================
//=======================================================
//		 �˥��Ʈ� 
//=======================================================
    case "smpl_acc":
		if(!$admin->is_power(2,3,"view")){ 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

//		$op['dept_id'] = get_dept_id();
//		if ($GLOBALS['SCACHE']['ADMIN']['id'] == "SA" ) {   // �p�G�O manager �i�J��...
//			$op['manager_flag'] = 1;
//		}

		//2006/05/02 update
		$op['dept_id'] = get_dept_id();
		
		// �p�G�O manager �i�J��...
		if (substr($op['dept_id'],0,7) == "<select"){
			$op['manager_flag'] = 1;
		}

		$op['msg'] = $smpl_acc->msg->get(2);

		$allow = $admin->decode_perm(2,3);   // �]�w �s�W�R���v��
		if ($op['msg']){ $op['msg_YES'] = 1; } else { $op['msg_YES'] = "" ;}
		if ($allow['view'])		{	$op['view_flag'] = 1;} else {	$op['view_flag'] = "";}
		if ($allow['add'])		{	$op['add_flag'] = 1;} else {	$op['add_flag'] = "";}
		if ($allow['edit'])		{	$op['edit_flag'] = 1;} else {	$op['edit_flag'] = "";}
		if ($allow['del'])		{	$op['del_flag'] = 1;} else {	$op['del_flag'] = "";}

			// creat cust combo box
			$op['year_select'] = $arry2->select($YEAR_WORK,'','PHP_year','select','');  	

			$where_str ="";
			if ($GLOBALS['SCACHE']['ADMIN']['id'] != "SA" ) {   // �p�G�O ���Omanager�i�J�� [manager�i�ݥ����˦�]...
					$where_str =" WHERE dept ='".$_SESSION['SCACHE']['ADMIN']['dept']."' ";
			}
			$cust_def = $cust->get_fields('cust_s_name',$where_str);   // ���X �Ȥ�N��
			$op['cust_select'] =  $arry2->select($cust_def,'','PHP_cust','select','');  	

			// creat season combo box
			$season_def = $season->get_fields('season');   // ���X �u�`
			$op['season_select'] =  $arry2->select($season_def,'','PHP_season','select','');  	

			// creat style type combo box
			$style_def = $style_type->get_fields('style_type');   // ���X �ڦ����O
			$op['style_type_select'] =  $arry2->select($style_def,'','PHP_style_type','select','');  	

		$layout->assign($op);
		$layout->display($TPL_SMPL_ACC_SEARCH);		    	    
	break;

//===temp backup   ======================
//=======================================================
    case "smpl_acc_search":
		if(!$admin->is_power(2,3,"view")){ 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$parm = array(	"style_code"	=>  $PHP_style_code,
						"cust"			=>	$PHP_cust,
						"cust_ref"		=>	$PHP_cust_ref,
						"year"			=>	$PHP_year,
						"season"		=>	$PHP_season,
						"style_type"	=>	$PHP_style_type
				);
			if (!$op = $smpl->search(1)) {
				$op['msg']= $smpl->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			// op �Q�M���F �ݦA�P�_�@�� 
//			$op['dept_id'] = get_dept_id();
//			if ($GLOBALS['SCACHE']['ADMIN']['id'] == "SA" ) {  // �p�G�O manager �i�J��...
//				$op['manager_flag'] = 1;
//			}

			//2006/05/02 update
			$op['dept_id'] = get_dept_id();
			
			// �p�G�O manager �i�J��...
		if (substr($op['dept_id'],0,7) == "<select"){
				$op['manager_flag'] = 1;
			}

				$op['msg']= $smpl->msg->get(2);
				$op['cgi']= $parm;

			$allow = $admin->decode_perm(2,3);   // �]�w �s�W�R���v��
			if ($op['msg']){ $op['msg_YES'] = 1; } else { $op['msg_YES'] = "" ;}
			if ($allow['view'])		{	$op['view_flag'] = 1;} else {	$op['view_flag'] = "";}
			if ($allow['add'])		{	$op['add_flag'] = 1;} else {	$op['add_flag'] = "";}
			if ($allow['edit'])		{	$op['edit_flag'] = 1;} else {	$op['edit_flag'] = "";}
			if ($allow['del'])		{	$op['del_flag'] = 1;} else {	$op['del_flag'] = "";}
//$op['NEXT_page'] = 1;
//$op['PREV_page'] = 1;

			$layout->assign($op);
			$layout->display($TPL_SMPL_ACC);  
		break;
//=======================================================
    case "smpl_acc_add":
		
		if(!$admin->is_power(2,3,"add")){ 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$op['smpl'] = $smpl->get($PHP_id);  //���X�ӵ��˥�����

		if (!$op['smpl']) {
			$op['msg'] = $smpl->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
			// �ˬd �ۤ��O�_�s�b
		if(file_exists($GLOBALS['config']['root_dir']."/pics/".$op['smpl']['id'].".jpg")){
			$op['main_pic'] = "./pics/".$op['smpl']['id'].".jpg";
		} else {
			$op['main_pic'] = "./images/graydot.gif";
		}
		if(file_exists($GLOBALS['config']['root_dir']."/pics/"."A".$op['smpl']['id'].".jpg")){
			$op['A_pic'] = "./pics/"."A".$op['smpl']['id'].".jpg";
		} else{
			$op['A_pic'] = "./images/graydot.gif";
		}
		if(file_exists($GLOBALS['config']['root_dir']."/pics/"."B".$op['smpl']['id'].".jpg")){
			$op['B_pic'] = "./pics/"."B".$op['smpl']['id'].".jpg";
		} else{
			$op['B_pic'] = "./images/graydot.gif";
		}
		
		//  ���X�ƮưO��~~~~~~~~~~~~~~
		$where_str = " WHERE smpl_code = '".$op['smpl']['style_code']."' ";
		$acc = $smpl_acc->search(0,$where_str);  //���X�ӵ� �˥��ƮưO��
		$op['acc_use'] = $acc['acc_use'];
		if (!count($op['acc_use'])){	$op['acc_NONE'] = "1";		}
		if (!is_array($op['acc_use'])) {     //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
			$op['msg'] = $smpl_acc->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		
		// creat USAGE METHOD combo box
		$op['usage_select'] = $arry2->select($USAGE_METHOD,'','PHP_usage','select','');  	

		$op['msg'] = $smpl_acc->msg->get(2);

		$allow = $admin->decode_perm(2,3);   // �]�w �s�W�R���v��
		if ($op['msg']){ $op['msg_YES'] = 1; } else { $op['msg_YES'] = "" ;}
		if ($allow['view'])		{	$op['view_flag'] = 1;} else {	$op['view_flag'] = "";}
		if ($allow['add'])		{	$op['add_flag'] = 1;} else {	$op['add_flag'] = "";}
		if ($allow['edit'])		{	$op['edit_flag'] = 1;} else {	$op['edit_flag'] = "";}
		if ($allow['del'])		{	$op['del_flag'] = 1;} else {	$op['del_flag'] = "";}

			$layout->assign($op);
			$layout->display($TPL_SMPL_ACC_ADD);		    	    
		break;
//=======================================================
    case "smpl_acc_view":
		
		if(!$admin->is_power(2,3,"view")){ 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$op['smpl'] = $smpl->get($PHP_id);  //���X�ӵ��˥�����

		if (!$op['smpl']) {
			$op['msg'] = $smpl->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
			// �ˬd �ۤ��O�_�s�b
		if(file_exists($GLOBALS['config']['root_dir']."/pics/".$op['smpl']['id'].".jpg")){
			$op['main_pic'] = "./pics/".$op['smpl']['id'].".jpg";
		} else {
			$op['main_pic'] = "./images/graydot.gif";
		}
		if(file_exists($GLOBALS['config']['root_dir']."/pics/"."A".$op['smpl']['id'].".jpg")){
			$op['A_pic'] = "./pics/"."A".$op['smpl']['id'].".jpg";
		} else{
			$op['A_pic'] = "./images/graydot.gif";
		}
		if(file_exists($GLOBALS['config']['root_dir']."/pics/"."B".$op['smpl']['id'].".jpg")){
			$op['B_pic'] = "./pics/"."B".$op['smpl']['id'].".jpg";
		} else{
			$op['B_pic'] = "./images/graydot.gif";
		}
		
		//  ���X�ƮưO��~~~~~~~~~~~~~~
		$where_str = " WHERE smpl_code = '".$op['smpl']['style_code']."' ";
		$acc = $smpl_acc->search(0,$where_str);  //���X�ӵ� �˥��D�ưO��
		$op['acc_use'] = $acc['acc_use'];
		if (!count($op['acc_use'])){	$op['acc_NONE'] = "1";		}
		if (!is_array($op['acc_use'])) {     //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
			$op['msg'] = $smpl_acc->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		
		// creat USAGE METHOD combo box
//		$op['usage_select'] = $arry2->select($USAGE_METHOD,'','PHP_usage','select','');  	

		$op['msg'] = $smpl_acc->msg->get(2);

		$allow = $admin->decode_perm(2,3);   // �]�w �s�W�R���v��
		if ($op['msg']){ $op['msg_YES'] = 1; } else { $op['msg_YES'] = "" ;}
		if ($allow['view'])		{	$op['view_flag'] = 1;} else {	$op['view_flag'] = "";}
		if ($allow['add'])		{	$op['add_flag'] = 1;} else {	$op['add_flag'] = "";}
		if ($allow['edit'])		{	$op['edit_flag'] = 1;} else {	$op['edit_flag'] = "";}
		if ($allow['del'])		{	$op['del_flag'] = 1;} else {	$op['del_flag'] = "";}

			$layout->assign($op);
			$layout->display($TPL_SMPL_ACC_VIEW);		    	    
		break;

//=======================================================
    case "show_acc_search":

		if(!$admin->is_power(2,3,"add")){ 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

//		$op['cgi'] = $parm;

		$op['msg']= $acc->msg->get(2);

		$layout->assign($op);
		$layout->display($TPL_ACC_SEARCH);		    	    
	break;

//=======================================================
    case "do_smpl_acc_add":
		
		if(!$admin->is_power(2,3,"add")){ 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$parm = array(	"acc_code"			=>	$PHP_acc_code,
						"smpl_id"			=>	$PHP_smpl_id,
						"style_code"		=>	$PHP_style_code,
						"use_for"			=>	$PHP_acc_use_use_for,
						"use_method"		=>	$PHP_usage
			);
		$op['acc_use'] = $parm;

		$f1 = $smpl_acc->add($parm);

					# �O���ϥΪ̰ʺA
		$message = "�s�W�˥�:[$PHP_style_code]�ƮƸ��:[$PHP_acc_code]�E";
		$log->log_add(0,"23A",$message);

				// creat USAGE combo box
			$op['usage_select'] = $arry2->select($USAGE_METHOD,$op['acc_use']['use_method'],'PHP_usage','select','');  	
			$op['smpl'] = $smpl->get($op['acc_use']['smpl_id']);  //���X�ӵ� �˥��O��
			if (!$op['smpl']) {
				$op['msg'] = $smpl->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
				// �ˬd �ۤ��O�_�s�b
			if(file_exists($GLOBALS['config']['root_dir']."/pics/".$op['smpl']['id'].".jpg")){
				$op['main_pic'] = "./pics/".$op['smpl']['id'].".jpg";
			} else {
				$op['main_pic'] = "./images/graydot.gif";
			}
			if(file_exists($GLOBALS['config']['root_dir']."/pics/"."A".$op['smpl']['id'].".jpg")){
				$op['A_pic'] = "./pics/"."A".$op['smpl']['id'].".jpg";
			} else{
				$op['A_pic'] = "./images/graydot.gif";
			}
			if(file_exists($GLOBALS['config']['root_dir']."/pics/"."B".$op['smpl']['id'].".jpg")){
				$op['B_pic'] = "./pics/"."B".$op['smpl']['id'].".jpg";
			} else{
				$op['B_pic'] = "./images/graydot.gif";
			}
	
			//  ���X�ƮưO��~~~~~~~~~~~~~~
			$where_str = " WHERE smpl_code = '".$op['smpl']['style_code']."' ";
			$acc = $smpl_acc->search(0,$where_str);  //���X�ӵ� �˥��D�ưO��
			$op['acc_use'] = $acc['acc_use'];
			if (!count($op['acc_use'])){	$op['acc_NONE'] = "1";		}
			if (!is_array($op['acc_use'])) {     //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
				$op['msg'] = $smpl_acc->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			
			// creat USAGE combo box
			$op['usage_select'] = $arry2->select($USAGE_METHOD,'','PHP_usage','select','');  	

			$allow = $admin->decode_perm(2,3);   // �]�w �s�W�R���v��
			$op['msg'] = $smpl_acc->msg->get(2);

			if ($op['msg']){ $op['msg_YES'] = 1; } else { $op['msg_YES'] = "" ;}
			$allow = $admin->decode_perm(2,3);   // �]�w �s�W�R���v��
			if ($allow['view'])		{	$op['view_flag'] = 1;} else {	$op['view_flag'] = "";}
			if ($allow['add'])		{	$op['add_flag'] = 1;} else {	$op['add_flag'] = "";}
			if ($allow['edit'])		{	$op['edit_flag'] = 1;} else {	$op['edit_flag'] = "";}
			if ($allow['del'])		{	$op['del_flag'] = 1;} else {	$op['del_flag'] = "";}

			$layout->assign($op);
			$layout->display($TPL_SMPL_ACC_ADD);		    	    
		break;
//=======================================================
    case "do_smpl_acc_del":
		
		if(!$admin->is_power(2,3,"edit")){ 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$parm = array(	"id"			=>	$id,
						"style_code"	=>	$style_code,
						"acc_code"		=>	$acc_code
			);
				$op['acc_use'] = $parm;

		$f1 = $smpl_acc->del($parm['id']);

					# �O���ϥΪ̰ʺA
		$message = "�R���˥�:[$style_code]�ƮƸ��:[$acc_code]�E";
		$log->log_add(0,"23D",$message);

					// creat USAGE combo box
			$op['usage_select'] = $arry2->select($USAGE_METHOD,'','PHP_usage','select','');  	
			$op['smpl'] = $smpl->get(0,$op['acc_use']['style_code']);  //���X�ӵ� �˥��O��
			if (!$op['smpl']) {
				$op['msg'] = $smpl->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
				// �ˬd �ۤ��O�_�s�b
			if(file_exists($GLOBALS['config']['root_dir']."/pics/".$op['smpl']['id'].".jpg")){
				$op['main_pic'] = "./pics/".$op['smpl']['id'].".jpg";
			} else {
				$op['main_pic'] = "./images/graydot.gif";
			}
			if(file_exists($GLOBALS['config']['root_dir']."/pics/"."A".$op['smpl']['id'].".jpg")){
				$op['A_pic'] = "./pics/"."A".$op['smpl']['id'].".jpg";
			} else{
				$op['A_pic'] = "./images/graydot.gif";
			}
			if(file_exists($GLOBALS['config']['root_dir']."/pics/"."B".$op['smpl']['id'].".jpg")){
				$op['B_pic'] = "./pics/"."B".$op['smpl']['id'].".jpg";
			} else{
				$op['B_pic'] = "./images/graydot.gif";
			}
	
			//  ���X�ƮưO��~~~~~~~~~~~~~~
			$where_str = " WHERE smpl_code = '".$op['smpl']['style_code']."' ";
			$acc = $smpl_acc->search(0,$where_str);  //���X�ӵ� �˥��D�ưO��
			$op['acc_use'] = $acc['acc_use'];
			if (!count($op['acc_use'])){	$op['acc_NONE'] = "1";		}
			if (!is_array($op['acc_use'])) {     //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
				$op['msg'] = $smpl_acc->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			
			// creat USAGE combo box
			$op['usage_select'] = $arry2->select($USAGE_METHOD,'','PHP_usage','select','');  	

			$op['msg'] = $smpl_acc->msg->get(2);

			$allow = $admin->decode_perm(2,3);   // �]�w �s�W�R���v��
			if ($op['msg']){ $op['msg_YES'] = 1; } else { $op['msg_YES'] = "" ;}
			if ($allow['view'])		{	$op['view_flag'] = 1;} else {	$op['view_flag'] = "";}
			if ($allow['add'])		{	$op['add_flag'] = 1;} else {	$op['add_flag'] = "";}
			if ($allow['edit'])		{	$op['edit_flag'] = 1;} else {	$op['edit_flag'] = "";}
			if ($allow['del'])		{	$op['del_flag'] = 1;} else {	$op['del_flag'] = "";}

		$layout->assign($op);
		$layout->display($TPL_SMPL_ACC_ADD);		    	    
	break;



//=======================================================
    case "show_size_search":
		check_authority(7,5,"view");		
		$where_str = " AND cust='".$PHP_cust."' AND dept='".$PHP_dept."'";
		if (!$op = $size_des->wi_search(0,$where_str)) {    // �s�X���ݳ������w�Ȥ�marked��null���˥��O��
			$op['msg']= $smpl->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}	
		$op['msg']=$smpl->msg->get(2);
		$op['dept'] = $PHP_dept;
		$op['cust'] = $PHP_cust;	  
		page_display($op, 7, 5, $TPL_SMPL_SIZE_LIST);	  	    
	break;



#************************************************
#
#	�R��  endding~~
#
#*************************************************




















//=======================================================
    case "acc":
 
		check_authority(1,4,"view");
		$op['acc_name'] = $arry2->select($ACC,'','PHP_acc_name','select','');

		page_display($op, 1, 4, $TPL_ACC_SEARCH);		    	    
		break;

//=======================================================
    case "acc_add":

		check_authority(1,4,"view");
			// create combo box for vendor fields.....
		
		$op['acc_name'] = $arry2->select($ACC,'','PHP_acc_name','select','');
			$where_str =" WHERE supl_cat ='Accessory' ";
			$supl_def = $supl->get_fields('supl_s_name',$where_str);   // ���X ���������O�N��
		if (!$supl_def)$supl_def= array('');

		$op['unit1'] = $arry2->select($ACC_PRICE_UNIT,'','PHP_unit1','select','');  	
		$op['unit2'] = $arry2->select($ACC_PRICE_UNIT,'','PHP_unit2','select','');  	
		$op['unit3'] = $arry2->select($ACC_PRICE_UNIT,'','PHP_unit3','select','');  	
		
		$op['currency1'] = $arry2->select($CURRENCY,'','PHP_currency1','select','');  	
		$op['currency2'] = $arry2->select($CURRENCY,'','PHP_currency2','select','');  	
		$op['currency3'] = $arry2->select($CURRENCY,'','PHP_currency3','select','');  	
		
		$op['term1'] = $arry2->select($TRADE_TERM,'','PHP_term1','select','');  	
		$op['term2'] = $arry2->select($TRADE_TERM,'','PHP_term2','select','');  	
		$op['term3'] = $arry2->select($TRADE_TERM,'','PHP_term3','select','');  	
		$op['acc']['acc_code']="Axx-xxx-xxx";
	if (isset($PHP_status))
	{
		page_display($op, 1, 4, $TPL_ACC_SUB_ADD);
	}else{
		page_display($op, 1, 4, $TPL_ACC_ADD);		    	      	    
	}
	break;

 //=======================================================
    case "do_acc_search":

		check_authority(1,4,"view");
		$parm = array(	"acc_code"		=>	$PHP_acc_code,
										"acc_name"		=>	$PHP_acc_name,
										"des"					=>	$PHP_des,
				);
		
		if (!$op = $acc->search(1)) {
			$op['msg']= $acc->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		
			$op['msg']= $acc->msg->get(2);
			if (isset($PHP_msg)) $op['msg'][]=$PHP_msg;
			$op['cgi'] = $parm;
		$op['back_str'] = "&PHP_acc_code=".$PHP_acc_code."&PHP_acc_name=".$PHP_acc_name."&PHP_des=".$PHP_des;
		page_display($op, 1, 4, $TPL_ACC);		    	      	    		    	    
		break;
 
//=======================================================
	case "do_acc_add":

		check_authority(1,4,"add");
		$parm = array(	
						"acc_name"			=>	$PHP_acc_name,
						"mile_code"			=>	$PHP_mile_code,
						"mile_name"			=>	$PHP_mile_name,
						"des"						=>	$PHP_des,
						"specify"				=>	$PHP_specify,
						"vendor1"				=>	$PHP_vendor1,
						"price1"				=>	$PHP_price1,
						"unit1"					=>	$PHP_unit1,
						"currency1"			=>	$PHP_currency1,
						"term1"					=>	$PHP_term1,
						"vendor2"				=>	$PHP_vendor2,
						"price2"				=>	$PHP_price2,
						"unit2"					=>	$PHP_unit2,
						"currency2"			=>	$PHP_currency2,
						"term2"					=>	$PHP_term2,
						"vendor3"				=>	$PHP_vendor3,
						"price3"				=>	$PHP_price3,
						"unit3"					=>	$PHP_unit3,
						"currency3"			=>	$PHP_currency3,
						"term3"					=>	$PHP_term3,
				);
				$tmp =00;

			foreach( $ACC_key as $key => $value)
			{
				if ($PHP_acc_name == $value)
				{
						$tmp = $key;
						break;
				}
			}

				$hend="A".$tmp."-";
				$parm['acc_code'] = $acc->get_no($hend,'acc_code','acc');
				$op['acc'] = $parm;



		$f1 = $acc->add($parm);
		if ($f1) {  // ���\��J��Ʈ�
			
			$message= "Append acc record:".$parm['acc_code'];

						# �O���ϥΪ̰ʺA
		$log->log_add(0,"14A",$message);
		unset($parm);
		if (isset($PHP_status))
		{
			$op['acc'] = $acc->get($f1);  //���X�ӵ��O��
			$op['msg'][] = $message;
			page_display($op, 1, 4, $TPL_ACC_SUB_VIEW);
		}else{
				if (!$op = $acc->search(0)) {
					$op['msg']= $acc->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
				}				
				$op['msg'][] = $message;
				$op['back_str'] = "&PHP_acc_code=&PHP_acc_name=&PHP_des=";
			page_display($op, 1, 4, $TPL_ACC);		    	      	    
		}			
			break;

		}	else {  // ��S�����\��J�s�Wuser������....		
			$op['acc'] = $parm;
			$op['acc']['acc_code']="Axx-xxx-xxx";
		$op['acc_name'] = $arry2->select($ACC,$PHP_acc_name,'PHP_acc_name','select','');
			$where_str =" WHERE supl_cat ='Accessory' ";
			$supl_def = $supl->get_fields('supl_s_name',$where_str);   // ���X ���������O�N��
		if (!$supl_def)$supl_def= array('');
		$op['supl1'] = $arry2->select($supl_def,$op['acc']['vendor1'],'PHP_vendor1','select','');  	
		$op['supl2'] = $arry2->select($supl_def,$op['acc']['vendor2'],'PHP_vendor2','select','');  	
		$op['supl3'] = $arry2->select($supl_def,$op['acc']['vendor3'],'PHP_vendor3','select','');  	

		$op['unit1'] = $arry2->select($ACC_PRICE_UNIT,$op['acc']['unit1'],'PHP_unit1','select','');  	
		$op['unit2'] = $arry2->select($ACC_PRICE_UNIT,$op['acc']['unit2'],'PHP_unit2','select','');  	
		$op['unit3'] = $arry2->select($ACC_PRICE_UNIT,$op['acc']['unit3'],'PHP_unit3','select','');  	
		
		$op['currency1'] = $arry2->select($CURRENCY,$op['acc']['currency1'],'PHP_currency1','select','');  	
		$op['currency2'] = $arry2->select($CURRENCY,$op['acc']['currency2'],'PHP_currency2','select','');  	
		$op['currency3'] = $arry2->select($CURRENCY,$op['acc']['currency3'],'PHP_currency3','select','');  	

		$op['term3'] = $arry2->select($TRADE_TERM,$PHP_term3,'PHP_term3','select','');
		$op['term2'] = $arry2->select($TRADE_TERM,$PHP_term2,'PHP_term2','select','');
		$op['term1'] = $arry2->select($TRADE_TERM,$PHP_term1,'PHP_term1','select','');

						
			$op['msg'] = $acc->msg->get(2);

	if (isset($PHP_status))
	{
		
		page_display($op, 1, 4, $TPL_ACC_SUB_ADD);
	}else{
		page_display($op, 1, 4, $TPL_ACC_ADD);		    	      	    
	}		
	break;

		}
//========================================================================================	
	case "acc_view":

		check_authority(1,4,"view");
		if (isset($PHP_code))
		{
			$op['acc'] = $acc->get(0,$PHP_code);  //���X�ӵ��O��
		}else{
			$op['acc'] = $acc->get($PHP_id);  //���X�ӵ��O��
		}
		

		if (!$op['acc']) {
			$op['msg'] = $acc->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		// �S�O�]�w :
		if($op['acc']['price1']== "0.00") { $op['acc']['price1']="";}
		if($op['acc']['price2']== "0.00") { $op['acc']['price2']="";}
		if($op['acc']['price3']== "0.00") { $op['acc']['price3']="";}

				# �O���ϥΪ̰ʺA
//		$log->log_add(0,"14V","�s�� �ƮƸ�ơG".$op['acc']['acc_code']);

			$op['msg'] = $acc->msg->get(2);
#####2006.11.14�W�[�O���ثe���� start			
			$op['now_pp'] = $PHP_sr_startno;
			$parm = array(	"acc_code"		=>	$PHP_acc_code,
											"acc_name"		=>	$PHP_acc_name,
													"des"			=>	$PHP_des,
				);
			$op['cgi'] = $parm;
			$op['back_str'] = "&PHP_acc_code=".$PHP_acc_code."&PHP_acc_name=".$PHP_acc_name."&PHP_des=".$PHP_des;

#####2006.11.14�W�[�O���ثe���� end
	
			page_display($op, 1, 4, $TPL_ACC_VIEW);	    	    
		break;

//=======================================================
	case "acc_edit":

		check_authority(1,4,"edit");
		$op['acc'] = $acc->get($PHP_id);
		if (!$op['acc']) {
			$op['msg'] = $acc->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		// �S�O�]�w :
		if($op['acc']['price1']== "0.00") { $op['acc']['price1']="";}
		if($op['acc']['price2']== "0.00") { $op['acc']['price2']="";}
		if($op['acc']['price3']== "0.00") { $op['acc']['price3']="";}

		$op['acc_name'] = $arry2->select($ACC,$op['acc']['acc_name'],'PHP_acc_name','select','');

		$op['unit1'] = $arry2->select($ACC_PRICE_UNIT,$op['acc']['unit1'],'PHP_unit1','select','');  	
		$op['unit2'] = $arry2->select($ACC_PRICE_UNIT,$op['acc']['unit2'],'PHP_unit2','select','');  	
		$op['unit3'] = $arry2->select($ACC_PRICE_UNIT,$op['acc']['unit3'],'PHP_unit3','select','');  	
		
		$op['currency1'] = $arry2->select($CURRENCY,$op['acc']['currency1'],'PHP_currency1','select','');  	
		$op['currency2'] = $arry2->select($CURRENCY,$op['acc']['currency2'],'PHP_currency2','select','');  	
		$op['currency3'] = $arry2->select($CURRENCY,$op['acc']['currency3'],'PHP_currency3','select','');  	

		$op['term3'] = $arry2->select($TRADE_TERM,$op['acc']['term3'],'PHP_term3','select','');
		$op['term2'] = $arry2->select($TRADE_TERM,$op['acc']['term2'],'PHP_term2','select','');
		$op['term1'] = $arry2->select($TRADE_TERM,$op['acc']['term1'],'PHP_term1','select','');

						
			$op['msg'] = $supl->msg->get(2);
#####2006.11.14�W�[�O���ثe���� start			
			$op['now_pp'] = $PHP_sr_startno;
			if (isset($PHP_back_str))
			{
				$op['back_str'] =$PHP_back_str;
			}else{
				$op['back_str'] = "&PHP_acc_code=".$PHP_acc_code."&PHP_acc_name=".$PHP_acc_name."&PHP_des=".$PHP_des;			
			}

#####2006.11.14�W�[�O���ثe���� end
 		if ($GLOBALS['SCACHE']['ADMIN']['id'] == "SA" ) {   // �p�G�O manager �i�J��...
				$op['mag_flag'] = 1;
		}
		page_display($op, 1, 4,$TPL_ACC_EDIT); 
		break;

//=======================================================
	case "do_acc_edit":

		check_authority(1,4,"edit");
		$argv = array(	"id"	=>  $PHP_id,
						"acc_code"		=>	$PHP_acc_code,
						"acc_name"		=>	$PHP_acc_name,
						"des"					=>	$PHP_des,
						"specify"			=>	$PHP_specify,
						"mile_code"		=>	$PHP_mile_code,
						"mile_name"		=>	$PHP_mile_name,
						"vendor1"			=>	$PHP_vendor1,
						"price1"			=>	$PHP_price1,
						"unit1"				=>	$PHP_unit1,
						"currency1"		=>	$PHP_currency1,
						"term1"				=>	$PHP_term1,
						"vendor2"			=>	$PHP_vendor2,
						"price2"			=>	$PHP_price2,
						"unit2"				=>	$PHP_unit2,
						"currency2"		=>	$PHP_currency2,
						"term2"				=>	$PHP_term2,
						"vendor3"			=>	$PHP_vendor3,
						"price3"			=>	$PHP_price3,
						"unit3"				=>	$PHP_unit3,
						"currency3"		=>	$PHP_currency3,
						"term3"				=>	$PHP_term3,
						"user"				=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
				);

		if (!$acc->edit($argv)) {
			$op['msg'] = $acc->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;	    
		}

					# �O���ϥΪ̰ʺA
		$message = "Success update acc record:[$PHP_acc_code]�E";
		$log->log_add(0,"14E",$message);
		$redir_str = $PHP_SELF.'?PHP_action=do_acc_search&PHP_sr_startno='.$PHP_sr_startno.'&PHP_msg='.$message.$PHP_back_str;
		redirect_page($redir_str);

		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "acc_del":

				// ���ݭn manager login �~��u�����R�� SUPLIER
		if(!$admin->is_power(1,4,"del") &&  !($GLOBALS['SCACHE']['ADMIN']['id'] == "SA" )) { 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$op['acc'] = $acc->get($PHP_id);
		
		if (!$acc->del($PHP_id)) {
			$op['msg'] = $acc->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;	    
		}
					# �O���ϥΪ̰ʺA

		$message = "Delete acc:[".$op['acc']['acc_code']."] record�C";
		$log->log_add(0,"14D",$message);

		if (!$op = $acc->search(0)) {
			$op = array();
			$op['msg']= $acc->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		if ($GLOBALS['SCACHE']['ADMIN']['id'] == "SA" ) {   // �p�G�O manager �i�J��...
				$op['manager_flag'] = 1;
		}
		$op['msg'][] = $message;

		page_display($op, 1, 4, $TPL_ACC);		    	    
	break;









//=======================================================
    case "lots":
 
		check_authority(1,3,"view");
		
		page_display($op, 1, 3, $TPL_LOTS_SEARCH);		    	    
		break;

 //=======================================================
    case "do_lots_search":		
		check_authority(1,3,"view");
/*
		$parm = array(	"lots_code"		=>	$PHP_lots_code,
										"lots_name"		=>	$PHP_lots_name,
										"comp"			=>	$PHP_comp,
				);
*/		
		$SCH_code='';
		if (isset($SCH_cat1)) $SCH_code.=$SCH_cat1;
		if (isset($SCH_cat2)) $SCH_code.=$SCH_cat2;
		if (!$op = $lots->search(1)) {
			$op['msg']= $lots->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		
		$op['msg']= $lots->msg->get(2);
		if (isset($PHP_msg)) $op['msg'][]=$PHP_msg;
		$op['back_str']="&SCH_cat1=".$SCH_cat1."&SCH_cat2=".$SCH_cat2."&SCH_mile=".$SCH_mile."&SCH_cons=".$SCH_cons."&SCH_lots_code=".$SCH_lots_code."&SCH_lots_name=".$SCH_lots_name."&SCH_comp=".$SCH_comp;
		page_display($op, 1, 3, $TPL_LOTS);		    	    
		break;
 

//=======================================================
    case "lots_add":
		
		check_authority(1,3,"add");
			// create combo box for vendor fields.....

		$op['unit1'] = $arry2->select($LOTS_PRICE_UNIT,'','PHP_unit1','select','');  	
		$op['unit2'] = $arry2->select($LOTS_PRICE_UNIT,'','PHP_unit2','select','');  	
		$op['unit3'] = $arry2->select($LOTS_PRICE_UNIT,'','PHP_unit3','select','');  	
		
		$op['currency1'] = $arry2->select($CURRENCY,'','PHP_currency1','select','');  	
		$op['currency2'] = $arry2->select($CURRENCY,'','PHP_currency2','select','');  	
		$op['currency3'] = $arry2->select($CURRENCY,'','PHP_currency3','select','');  	
						    	    
		$op['term3'] = $arry2->select($TRADE_TERM,'','PHP_term3','select','');
		$op['term2'] = $arry2->select($TRADE_TERM,'','PHP_term2','select','');
		$op['term1'] = $arry2->select($TRADE_TERM,'','PHP_term1','select','');
		$op['lots']['lots_code']='Fxx-xxx-xxx';
		if (isset($PHP_status))
		{
			page_display($op, 1, 3, $TPL_LOTS_SUB_ADD);	    	    
		}else{
			page_display($op, 1, 3, $TPL_LOTS_ADD);	    	    
		}
		
		break;

//=======================================================
	case "do_lots_add":

		check_authority(1,3,"add");
		if (!isset($PHP_cat1)) $PHP_cat1='';
		if (!isset($PHP_cat2)) $PHP_cat2='';
		$parm = array(	
						"lots_name"			=>	$PHP_lots_name,
						"des"				=>	$PHP_des,
						"comp"				=>	$PHP_comp,
						"specify"			=>	$PHP_specify,
						
						"cons"				=>	$PHP_cons,
						"width"				=>	$PHP_width,
						"weight"			=>	$PHP_weight,
						
						
						"vendor1"			=>	$PHP_vendor1,
						"price1"			=>	$PHP_price1,
						"unit1"				=>	$PHP_unit1,
						"currency1"			=>	$PHP_currency1,
						"term1"				=>	$PHP_term1,
						
						"vendor2"			=>	$PHP_vendor2,
						"price2"			=>	$PHP_price2,
						"unit2"				=>	$PHP_unit2,
						"currency2"			=>	$PHP_currency2,
						"term2"				=>	$PHP_term2,
						
						"vendor3"			=>	$PHP_vendor3,
						"price3"			=>	$PHP_price3,
						"unit3"				=>	$PHP_unit3,
						"currency3"			=>	$PHP_currency3,
						"term3"				=>	$PHP_term3,
						"cat1"				=>	$PHP_cat1,
						"cat2"				=>	$PHP_cat2,
				);

				$hend="F".$PHP_cat1.$PHP_cat2."-";
				$parm['lots_code'] = $lots->get_no($hend,'lots_code','lots');
				$op['lots'] = $parm;

		$f1 = $lots->add($parm);
		if ($f1) {  // ���\��J��Ʈ�
			$message= "Append fabric record:".$parm['lots_code'];

					# �O���ϥΪ̰ʺA
			$log->log_add(0,"13A",$message);
			

			unset($parm);			

			
		if (isset($PHP_status))
		{
			$op['lots'] = $lots->get($f1);  //���X�ӵ��O��
			$op['msg'][] = $message;
			page_display($op, 1, 3, $TPL_LOTS_SUB_VIEW);	    	    
		}else{			
				if (!$op = $lots->search(0)) {
					$op['msg']= $lots->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
				}			
			$op['msg'][] = $message;
			$op['back_str']="&SCH_cat1=&SCH_cat2=&SCH_mile=&SCH_cons=&SCH_lots_code=&SCH_lots_name=&SCH_comp=";
			page_display($op, 1, 3, $TPL_LOTS);	    	    
		}

		break;
	
		}	else {  // ��S�����\��J�s�Wuser������....
			$parm['lots_code'] = 'Fxx-xxx-xxx';
			$op['lots'] = $parm;
		
		$op['unit1'] = $arry2->select($LOTS_PRICE_UNIT,$op['lots']['unit1'],'PHP_unit1','select','');  	
		$op['unit2'] = $arry2->select($LOTS_PRICE_UNIT,$op['lots']['unit2'],'PHP_unit2','select','');  	
		$op['unit3'] = $arry2->select($LOTS_PRICE_UNIT,$op['lots']['unit3'],'PHP_unit3','select','');  	
		
		$op['currency1'] = $arry2->select($CURRENCY,$op['lots']['currency1'],'PHP_currency1','select','');  	
		$op['currency2'] = $arry2->select($CURRENCY,$op['lots']['currency2'],'PHP_currency2','select','');  	
		$op['currency3'] = $arry2->select($CURRENCY,$op['lots']['currency3'],'PHP_currency3','select','');  	
		
		$op['term3'] = $arry2->select($TRADE_TERM,$PHP_term3,'PHP_term3','select','');
		$op['term2'] = $arry2->select($TRADE_TERM,$PHP_term2,'PHP_term2','select','');
		$op['term1'] = $arry2->select($TRADE_TERM,$PHP_term1,'PHP_term1','select','');
			
			$op['msg'] = $lots->msg->get(2);
		if (isset($PHP_status))
		{
			page_display($op, 1, 3, $TPL_LOTS_SUB_ADD);	    	    
		}else{
			page_display($op, 1, 3, $TPL_LOTS_ADD);	    	    
		}
		break;

		}
		
//========================================================================================	
	case "lots_view":

		check_authority(1,3,"view");
		if (isset($PHP_code))
		{
			$op['lots'] = $lots->get(0,$PHP_code);  //���X�ӵ��O��
		}else{
			$op['lots'] = $lots->get($PHP_id);  //���X�ӵ��O��
		}

		if (!$op['lots']) {
			$op['msg'] = $lots->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		// �S�O�]�w :
		if($op['lots']['price1']== "0.00") { $op['lots']['price1']="";}
		if($op['lots']['price2']== "0.00") { $op['lots']['price2']="";}
		if($op['lots']['price3']== "0.00") { $op['lots']['price3']="";}


				# �O���ϥΪ̰ʺA

		$op['cat_1']	= substr($op['lots']['lots_code'],1,1);
		$op['cat_2']	= substr($op['lots']['lots_code'],2,1);

			$op['msg'] = $lots->msg->get(2);
#####2006.11.14�W�[�O���ثe���� start			
			$op['now_pp'] = $PHP_sr_startno;
			$op['back_str']="&SCH_cat1=".$SCH_cat1."&SCH_cat2=".$SCH_cat2."&SCH_mile=".$SCH_mile."&SCH_cons=".$SCH_cons."&SCH_lots_code=".$SCH_lots_code."&SCH_lots_name=".$SCH_lots_name."&SCH_comp=".$SCH_comp;
			$op['cgi']=$parm;	

#####2006.11.14�W�[�O���ثe���� end			
			page_display($op, 1, 3, $TPL_LOTS_VIEW);		    	    
		break;

//=======================================================
	case "lots_edit":
	
		check_authority(1,3,"edit");
		$op['lots'] = $lots->get($PHP_id);
		if (!$op['lots']) {
			$op['msg'] = $lots->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		// �S�O�]�w :
		if($op['lots']['price1']== "0.00") { $op['lots']['price1']="";}
		if($op['lots']['price2']== "0.00") { $op['lots']['price2']="";}
		if($op['lots']['price3']== "0.00") { $op['lots']['price3']="";}
		
		$op['unit1'] = $arry2->select($LOTS_PRICE_UNIT,$op['lots']['unit1'],'PHP_unit1','select','');  	
		$op['unit2'] = $arry2->select($LOTS_PRICE_UNIT,$op['lots']['unit2'],'PHP_unit2','select','');  	
		$op['unit3'] = $arry2->select($LOTS_PRICE_UNIT,$op['lots']['unit3'],'PHP_unit3','select','');  	
		
		$op['currency1'] = $arry2->select($CURRENCY,$op['lots']['currency1'],'PHP_currency1','select','');  	
		$op['currency2'] = $arry2->select($CURRENCY,$op['lots']['currency2'],'PHP_currency2','select','');  	
		$op['currency3'] = $arry2->select($CURRENCY,$op['lots']['currency3'],'PHP_currency3','select','');  	
		
		$op['term3'] = $arry2->select($TRADE_TERM,$op['lots']['term3'],'PHP_term3','select','');
		$op['term2'] = $arry2->select($TRADE_TERM,$op['lots']['term2'],'PHP_term2','select','');
		$op['term1'] = $arry2->select($TRADE_TERM,$op['lots']['term1'],'PHP_term1','select','');
			
			$op['msg'] = $lots->msg->get(2);
		if ($GLOBALS['SCACHE']['ADMIN']['id'] == "SA" ) {   // �p�G�O manager �i�J��...
				$op['mag_flag'] = 1;
		}
#####2006.11.14�W�[�O���ثe���� start			
			$op['now_pp'] = $PHP_sr_startno;
			if (isset($PHP_back_str))
			{
				$op['back_str']=$PHP_back_str;
			}else{
				$op['back_str']="&SCH_cat1=".$SCH_cat1."&SCH_cat2=".$SCH_cat2."&SCH_mile=".$SCH_mile."&SCH_cons=".$SCH_cons."&SCH_lots_code=".$SCH_lots_code."&SCH_lots_name=".$SCH_lots_name."&SCH_comp=".$SCH_comp;
			}
			
#####2006.11.14�W�[�O���ثe���� end	  
		page_display($op, 1, 3, $TPL_LOTS_EDIT);
		break;
//=======================================================
	case "do_lots_edit":

		check_authority(1,3,"edit");
		$argv = array(	"id"				=>  $PHP_id,
						"lots_code"			=>	$PHP_lots_code,
						"lots_name"			=>	$PHP_lots_name,
						"des"				=>	$PHP_des,
						"comp"				=>	$PHP_comp,
						"specify"			=>	$PHP_specify,
						
						"cons"				=>	$PHP_cons,
						"width"				=>	$PHP_width,
						"weight"			=>	$PHP_weight,
						
						"vendor1"			=>	$PHP_vendor1,
						"price1"			=>	$PHP_price1,
						"unit1"				=>	$PHP_unit1,
						"currency1"			=>	$PHP_currency1,
						"term1"				=>	$PHP_term1,
						
						"vendor2"			=>	$PHP_vendor2,
						"price2"			=>	$PHP_price2,
						"unit2"				=>	$PHP_unit2,
						"currency2"			=>	$PHP_currency2,
						"term2"				=>	$PHP_term2,
						
						"vendor3"			=>	$PHP_vendor3,
						"price3"			=>	$PHP_price3,
						"unit3"				=>	$PHP_unit3,
						"currency3"			=>	$PHP_currency3,
						"term3"				=>	$PHP_term3,
						"user"				=>	$GLOBALS['SCACHE']['ADMIN']['login_id']
				);

		if (!$lots->edit($argv)) {
			$op['msg'] = $lots->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;	    
		}

					# �O���ϥΪ̰ʺA
		$message = "Success update fabric record:[$PHP_lots_code]�E";
		$log->log_add(0,"13E",$message);
		$redir_str = $PHP_SELF.'?PHP_action=do_lots_search&PHP_sr_startno='.$PHP_sr_startno.'&PHP_msg='.$message.$PHP_back_str;
		redirect_page($redir_str);

		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "lots_del":

				// ���ݭn manager login �~��u�����R�� SUPLIER
		if(!$admin->is_power(1,3,"del")  && !($GLOBALS['SCACHE']['ADMIN']['id'] == "SA")) {
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$op['lots'] = $lots->get($PHP_id);
		
		if (!$lots->del($PHP_id)) {
			$op['msg'] = $lots->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;	    
		}
					# �O���ϥΪ̰ʺA

		$message = "Delete fabric:[".$op['lots']['lots_code']."] �C";
		$log->log_add(0,"13D",$message);

		if (!$op = $lots->search(0)) {
			$op = array();
			$op['msg']= $lots->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		if ($GLOBALS['SCACHE']['ADMIN']['id'] == "SA" ) {   // �p�G�O manager �i�J��...
				$op['manager_flag'] = 1;
		}
		$op['msg'][] = $message;
		page_display($op, 1, 3, $TPL_LOTS);	    	    
	break;









//=======================================================
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "supl":	 	JOB 12V
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "supl":
 
		check_authority(1,2,"view");

		if ($GLOBALS['SCACHE']['ADMIN']['dept'] == "SA") {  
			$op['manager_flag'] = 1;
		}

		$op['msg']= $supl->msg->get(2);
		// ��� -----------
		$op['country_select'] = $arry2->select($COUNTRY,'','PHP_country','select','');  	
		$cat_def = $SUPL_TYPE;   // ���X������ ���������O�N�� [ config.php ]
		$op['cat_select'] = $arry2->select($cat_def,"","PHP_supl_cat","select",""); 

		page_display($op, 1, 2, $TPL_SUPL_SEARCH);
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "supl_search":	 	JOB 12V
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "supl_search":
 
		check_authority(1,2,"view");
			// ���----------
		$op['country_select'] = $arry2->select($COUNTRY,'','PHP_country','select','');  	

		$parm = array(	"supl_cat"			=>  $PHP_supl_cat,
						"country"			=>	$PHP_country,
						"supl_s_name"		=>	$PHP_supl_s_name,
						"supl_f_name"		=>	$PHP_supl_f_name
				);
		
		if (!$op = $supl->search(0)) {	//�j�M�C��
			$op['msg']= $supl->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		if ($GLOBALS['SCACHE']['ADMIN']['dept'] == "SA") { 
			$op['manager_flag'] = 1;
		}
		if ($PHP_supl_cat)$op['supl_cat'] = $PHP_supl_cat;

		$op['cgi'] = $parm;

		$op['msg']= $supl->msg->get(2);

		page_display($op, 1, 2, $TPL_SUPL);
		break;




#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "supl_add":	 	JOB 12A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "supl_add":
		
		check_authority(1,2,"add");

		if (!$PHP_supl_cat){

			$cat_def = $SUPL_TYPE;   // ���X������ ���������O�N�� [ config.php ]
			$op['cat_select'] = $arry2->select($cat_def,"","PHP_supl_cat","select","");  
			$op['country_select'] = $arry2->select($COUNTRY,'','PHP_country','select','');  	

			$op['msg'][] = "sorry! Please chooice supplier category !";

			page_display($op, 1, 2, $TPL_SUPL_SEARCH);
			break;
		}
		$op['supl_cat'] = $PHP_supl_cat;
		$op['country_select'] = $arry2->select($COUNTRY,'','PHP_country','select','');  	
		$op['usance_select'] = $arry2->select($usance,"","PHP_usance","select","");
		$op['dm_way_select'] = $arry2->select($dm_way[0],"","PHP_dm_way","select","");
		page_display($op, 1, 2, $TPL_SUPL_ADD);
		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_supl_add":	 	JOB 12A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_supl_add":

		check_authority(1,2,"add");		
		$parm = array(	"supl_cat"			=>	$PHP_supl_cat,
						"vndr_no"			=>	$PHP_vndr_no,
						"country"			=>	$PHP_country,
						"supl_s_name"		=>	$PHP_supl_s_name,
						"supl_f_name"		=>	$PHP_supl_f_name,
						"uni_no"			=>	$PHP_uni_no,
						"cntc_phone"		=>	$PHP_cntc_phone,
						"cntc_addr"			=>	$PHP_cntc_addr,
						"cntc_person1"		=>	$PHP_cntc_person1,
						"cntc_cell1"		=>	$PHP_cntc_cell1,
						"email1"			=>	$PHP_email1,
						"cntc_person2"		=>	$PHP_cntc_person2,
						"cntc_cell2"		=>	$PHP_cntc_cell2,
						"email2"			=>	$PHP_email2,
						"cntc_fax"			=>	$PHP_cntc_fax,
						"usance"			=>	$PHP_usance,						
						"dm_way"			=>	$PHP_dm_way
				);

				$op['supl'] = $parm;

		$f1 = $supl->add($parm);
		
		if ($f1) {  // ���\��J��Ʈ�
			$PHP_supl_s_name=$PHP_vndr_no;
			if (!$op = $supl->search(0)) {	//�j�M�C��
				$op['msg']= $supl->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

			$message= "Append supl:".$parm['supl_s_name'];

					# �O���ϥΪ̰ʺA
			$log->log_add(0,"12A",$message);
			
			$op['msg'][] = $message;
			// ���X������ ���������O�N��
			$cat_def = $supl_type->get_fields('supl_type');   
			$op['cat_select'] = $arry2->select($cat_def,"","PHP_supl_cat","select","");

			page_display($op, 1, 2, $TPL_SUPL);
			break;
	
	
		}	else {  // ��S�����\��J�s�Wuser������....

			$op['supl'] = $parm;
			$op['supl_cat'] = $parm['supl_cat'];
			$op['msg'] = $supl->msg->get(2);


			$cat_def = $supl_type->get_fields('supl_type'); 
			$op['cat_select'] = $arry2->select($cat_def,"","PHP_supl_cat","select",""); 
			$op['country_select'] = $arry2->select($COUNTRY,$PHP_country,'PHP_country','select','');  
			
			$op['usance_select'] = $arry2->select($usance,$PHP_usance,"PHP_usance","select","");
			$op['dm_way_select'] = $arry2->select($dm_way[0],$PHP_dm_way,"PHP_dm_way","select","");	

			page_display($op, 1, 2, $TPL_SUPL_ADD);
			break;

		}


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "supl_view":	 	JOB 12V
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "supl_view":

		check_authority(1,2,"view");

		$op['supl'] = $supl->get($PHP_id);  //���X�ӵ��O��

		if (!$op['supl']) {
			$op['msg'] = $supl->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		if ($op['supl']['usance'] == "000")	$op['supl']['usance']="0";		//�̥N���אּ�W��
		$ary=array('A','B','C','D','E','F','G','H','I');
		for ($i=0; $i<sizeof($ary); $i++)
		{
			if ($op['supl']['usance'] == $ary[$i])	$op['supl']['usance']=$usance[$i];
		}
				
		for ($i=0; $i< 4; $i++)
		{
			if ($op['supl']['dm_way'] == $dm_way[1][$i]) $op['supl']['dm_way']=$dm_way[0][$i];
		}
		$op['msg'] = $supl->msg->get(2);

		page_display($op, 1, 2, $TPL_SUPL_VIEW);
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "supl_edit":	 	JOB 12E
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "supl_edit":

		check_authority(1,2,"edit");

		$op['supl'] = $supl->get($PHP_id);
		if (!$op['supl']) {
			$op['msg'] = $supl->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}		
		$ary=array('A','B','C','D','E','F','G','H','I');
		for ($i=0; $i<sizeof($ary); $i++)
		{
			if ($op['supl']['usance'] == $ary[$i])	$op['supl']['usance']=$usance[$i];
		}
				
		for ($i=0; $i< 4; $i++)
		{
			if ($op['supl']['dm_way'] == $dm_way[1][$i]) $op['supl']['dm_way']=$dm_way[0][$i];
		}
		$op['country_select'] = $arry2->select($COUNTRY,$op['supl']['country'],'PHP_country','select','');  	
		$op['usance_select'] = $arry2->select($usance,$op['supl']['usance'],"PHP_usance","select","");
		$op['dm_way_select'] = $arry2->select($dm_way[0],$op['supl']['dm_way'],"PHP_dm_way","select","");	
		$op['msg'] = $supl->msg->get(2);

		page_display($op, 1, 2, $TPL_SUPL_EDIT);
		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_supl_edit":	 	JOB 12E
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_supl_edit":

		check_authority(1,2,"edit");

		$argv = array(	"id"				=>  $PHP_id,
						"supl_cat"			=>	$PHP_supl_cat,
						"country"			=>	$PHP_country,
						"supl_s_name"		=>	$PHP_supl_s_name,
						"supl_f_name"		=>	$PHP_supl_f_name,
						"uni_no"			=>	$PHP_uni_no,
						"cntc_phone"		=>	$PHP_cntc_phone,
						"cntc_addr"			=>	$PHP_cntc_addr,
						"cntc_person1"		=>	$PHP_cntc_person1,
						"cntc_cell1"		=>	$PHP_cntc_cell1,
						"email1"			=>	$PHP_email1,
						"cntc_person2"		=>	$PHP_cntc_person2,
						"cntc_cell2"		=>	$PHP_cntc_cell2,
						"email2"			=>	$PHP_email2,
						"cntc_fax"			=>	$PHP_cntc_fax,
						"usance"			=>	$PHP_usance,						
						"dm_way"			=>	$PHP_dm_way
				);


		if (!$supl->edit($argv)) {
			$op['msg'] = $supl->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;	    
		}

					# �O���ϥΪ̰ʺA
		$message = "Success update supplier:[$PHP_supl_s_name]�E";
		$log->log_add(0,"12E",$message);

		// �C�X�Ҧ����O��~~~
		$PHP_supl_s_name=$PHP_vndr_no;
		if (!$op = $supl->search(0)) {	//�j�M�C��
			$op['msg']= $supl->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

			// ���X�էO���
		if ($GLOBALS['SCACHE']['ADMIN']['dept'] == "SA") { 
				$op['manager_flag'] = 1;
		}

		$cat_def = $supl_type->get_fields('supl_type'); 
		$op['cat_select'] = $arry2->select($cat_def,"","PHP_supl_cat","select","");
		unset($argv);  // �R�� �Ѽ�
			
			$op['msg'][] = $message;

		page_display($op, 1, 2, $TPL_SUPL);
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "supl_del":	 	JOB 12D
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "supl_del":

		// ���ݭn manager login �~��u�����R�� SUPLIER
		if(!$admin->is_power(1,2,"del") && !($GLOBALS['SCACHE']['ADMIN']['id'] == "SA" )) {
			$op['msg'][] = "sorry! you don't have this Authority!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$op['supl'] = $supl->get($PHP_id);
		
		if (!$supl->del($PHP_id,$PHP_vndr_no)) {
			$op['msg'] = $supl->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;	    
		}
					# �O���ϥΪ̰ʺA

		$message = "�R�� ������:[".$op['supl']['supl_s_name']."] �O���C";
		$log->log_add(0,"12D",$message);

		if (!$op = $supl->search(1)) {	//�j�M�C��
			$op = array();
			$op['msg']= $supl->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		if ($GLOBALS['SCACHE']['ADMIN']['dept'] == "SA" || $GLOBALS['SCACHE']['ADMIN']['id'] == "SA" ||  $GLOBALS['SCACHE']['ADMIN']['dept'] == "AC" ||  $GLOBALS['SCACHE']['ADMIN']['dept'] == "MS") { 
				$op['manager_flag'] = 1;
		}
		$op['msg'][] = $message;
		  
		$op['cat_select'] = $arry2->select($SUPL_TYPE,"","PHP_supl_cat","select",""); 

		page_display($op, 1, 2, $TPL_SUPL);
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "tmp_supl":	 	JOB 61V
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	  case "tmp_supl":

		check_authority(1,5,"view");
		$parm=array(
						'vndr_no'		=>	$PHP_vndr_no,
						'supl_f_name'	=>	$PHP_supl_f_name,
						'now_page'		=>	$PHP_sr_startno
					);
		$cgi_link="&PHP_vndr_no=".$PHP_vndr_no."&PHP_supl_f_name=".$PHP_supl_f_name;
		if (!$op = $tmps1->search(0)) {		//�j�M�C��
			$op['msg']= $tmps1->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		
		   
		$op['cat_select'] = $arry2->select($SUPL_TYPE,"","PHP_supl_cat","select",""); 

		$op['msg'] = $tmps1->msg->get(2);
		$op['supl']=$parm;
		$op['cgi_link']=$cgi_link;
		page_display($op, 1, 5, $TPL_TMP_SUPL);
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "supl_view":	 	JOB 12V
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "tmp_supl_view":

		check_authority(1,2,"view");

		$op['supl'] = $tmps1->get($PHP_vndr_no);  //���X�ӵ��O��

		if (!$op['supl']) {
			$op['msg'] = $tmps1->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$ary=array('A','B','C','D','E','F','G','H','I');
		for ($i=0; $i<sizeof($ary); $i++)
		{
			if ($op['supl']['usance'] == $ary[$i])	$op['supl']['usance']=$usance[$i];
		}
		
		for ($i=0; $i< 4; $i++)
		{
			if ($op['supl']['dm_way'] == $dm_way[1][$i]) $op['supl']['dm_way']=$dm_way[0][$i];
		}		
		

		$op['msg'] = $tmps1->msg->get(2);

		page_display($op, 1, 2, $TPL_SUPL_VIEW);
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		  case "do_tmpsup_add":		 	JOB 61A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	  case "do_tmpsup_add":		
		check_authority(1,5,"add");
		$parm=array(
						'vndr_no'		=>	$PHP_vndr_no,
						'supl_f_name'	=>	$PHP_supl_f_name,
						'now_page'		=>	$PHP_sr_startno
					);
		$cgi_link="&PHP_vndr_no=".$PHP_vndr_no."&PHP_supl_f_name=".$PHP_supl_f_name;
		$f1 = $tmps1->exchange($PHP_id,$PHP_supl_cat);	//�N���w�������ӥ[�Jparts��
		if (!$op = $tmps1->search(0)) {		//�j�M�C��
			$op['msg']= $mec->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		
		$op['cat_select'] = $arry2->select($SUPL_TYPE,"","PHP_supl_cat","select",""); 
		$op['msg'] = $tmps1->msg->get(2);
		$op['supl']=$parm;
		$op['cgi_link']=$cgi_link;
		page_display($op, 1, 5, $TPL_TMP_SUPL);
		break;
 

 
 
 
 
 
 
 
 //----------------------------------------------------------------
 //			job 11  cust
 //----------------------------------------------------------------
    case "cust":
 		check_authority(1,1,"view");
		$in_id = $GLOBALS['SCACHE']['ADMIN']['dept'];
		$in_dept = '';
		$where_str ='';
		$manager ='';
		$dept_select = array();

		$sales_dept = get_sales_dept(); // ���X �~�Ȫ����� [���tK0] ------


	
		for ($i=0; $i<count($sales_dept);$i++){
			if($in_id == $sales_dept[$i]){
				$in_dept = $sales_dept[$i];   // �p�G�O�~�ȳ��i�J �hdept_code ���w�ӷ~�ȳ�---
			}
		}
		
		if (!$in_dept) {    // ���O�~�ȳ��H[�]���t K0 ���H ]�i�J��
			$manager = 1;
			$dept_select = $arry2->select($sales_dept,"","PHP_dept_code","select","");  //�~�ȳ��� select���
		} else {
			$where_str = " WHERE dept = '".$in_dept."' ";
		}

			if (!$op = $cust->search(0,$where_str)) {    // �s�X���ݳ����������Ȥ�O��
				$op['msg']= $cust->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}


		//   �]�� search �N $op���m �ҥH�A�a�J�䥦�Ѽ�
		$op['dept_code'] = $in_dept;
		$op['dept_select'] = $dept_select;
		$op['manager_flag'] = $manager;

		$op['msg'] = $cust->msg->get(2);
		page_display($op, 1, 1, $TPL_CUST);		    	    
		break;

//=======================================================
    case "cust_add":
		check_authority(1,1,"add");
			if ($PHP_dept_code ==""){
				$op['msg'][] = "sorry! Please choice dept. first!";

					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;

				$redir_str = $PHP_SELF.'?PHP_action=cust';
				redirect_page($redir_str);

				break;

			}
			$op['country_select'] = $arry2->select($COUNTRY,"","PHP_cust_country","select","");  //�~�ȳ��� select���

				$op['dept_code'] = $PHP_dept_code;
		page_display($op, 1, 1, $TPL_CUST_ADD);			    	    
		break;
//=======================================================
	case "cust_shift":
//	$PHP_dept;
	$where_str = "WHERE dept = '".$PHP_dept."'";
	$op=$cust->search(0,$where_str,100);
	
	$op['org_dept'] = $PHP_dept;
	$sales_dept = get_sales_dept(); // ���X �~�Ȫ����� [���tK0] ------
	$dept_select = $arry2->select($sales_dept,"","PHP_dept_code","select","");  //�~�ȳ��� select���
	$op['dept_select'] = $dept_select;
	page_display($op, 1, 1, $TPL_CUST_SHIFT);
	break;

//=======================================================
	case "do_cust_shift":

	foreach ($PHP_cust as $key => $value)
	{
		$q_str = "UPDATE cust SET ".$parm['field_name']."='".$parm['field_value']."'  WHERE id='".$parm['id']."'";

		 $parm = array ( 'field_name' 		=>	'dept',
		 								 'field_value'		=>	$PHP_dept_code,
		 								 'id'							=>	$key
		 								 );
		 $cust->update_field($parm);
		 $message = "success shift ".$value." to dept : ".$PHP_dept_code;
		 $log->log_add(0,"11E",$message);
	}
	$redir_str = $PHP_SELF.'?PHP_action=cust';
	redirect_page($redir_str);
	break;

//=======================================================
	case "do_cust_add":
		check_authority(1,1,"add");
		$parm = array(	"dept_code"			=>	$PHP_dept_code,
						"country"			=>	$PHP_cust_country,
						"cust_s_name"		=>	$PHP_cust_s_name,
						"cust_f_name"		=>	$PHP_cust_f_name,
						"cust_init_name"	=>	$PHP_cust_init_name,
						"cntc_phone"		=>	$PHP_cntc_phone,
						"cntc_addr"			=>	$PHP_cntc_addr,
						"cntc_person1"		=>	$PHP_cntc_person1,
						"cntc_cell1"		=>	$PHP_cntc_cell1,
						"email1"			=>	$PHP_email1,
						"cntc_person2"		=>	$PHP_cntc_person2,
						"cntc_cell2"		=>	$PHP_cntc_cell2,
						"email2"			=>	$PHP_email2,
						"cntc_fax"			=>	$PHP_cntc_fax
				);

				$op['cust'] = $parm;

		$f1 = $cust->add($parm);
		if ($f1) {  // ���\��J��Ʈ�

				if (!$op = $cust->search(0)) {
					$op['msg']= $cust->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
				}

				$message= "Append customer:".$PHP_cust_s_name." for dept.:".$PHP_dept_code;

						# �O���ϥΪ̰ʺA
				$log->log_add(0,"11A",$message);


		$in_id = $GLOBALS['SCACHE']['ADMIN']['dept'];
		$in_dept = '';
		$where_str ='';
		$manager ='';
		$dept_select = array();

		$sales_dept = get_sales_dept(); // ���X �~�Ȫ����� [���tK0] ------


		if ($in_id=='HJ' || $in_id=='CL' || $in_id=='WX' || $in_id	=='LY')
		{
			$in_dept=$in_id	;
		}else{
		for ($i=0; $i<count($sales_dept);$i++){
			if($in_id == $sales_dept[$i]){
				$in_dept = $sales_dept[$i];   // �p�G�O�~�ȳ��i�J �hdept_code ���w�ӷ~�ȳ�---
			}
		}
		}
		if (!$in_dept) {    // ���O�~�ȳ��H[�]���t K0 ���H ]�i�J��
			$manager = 1;
			$dept_select = $arry2->select($sales_dept,"","PHP_dept_code","select","");  //�����Oselect���
		} else {
			$where_str = " WHERE dept = '".$in_dept."' ";
		}

			if (!$op = $cust->search(0,$where_str)) {    // �s�X���ݳ����������Ȥ�O��
				$op['msg']= $cust->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}


		//   �]�� search �N $op���m �ҥH�A�a�J�䥦�Ѽ�
		$op['dept_code'] = $in_dept;
		$op['dept_select'] = $dept_select;
		$op['manager_flag'] = $manager;

		$op['msg'] = $cust->msg->get(2);
		$op['msg'][] = $message;		
		page_display($op, 1, 1, $TPL_CUST);	    	    
		break;
	

		
		
		}	else {  // ��S�����\��J�s�Wuser������....
			$op['country_select'] = $arry2->select($COUNTRY,$PHP_cust_country,"PHP_cust_country","select","");  //�~�ȳ��� select���
			$op['cust'] = $parm;
			$op['dept_code'] = $parm['dept_code'];
			$op['msg'] = $cust->msg->get(2);
		page_display($op, 1, 1, $TPL_CUST_ADD);    	    
		break;

		}

//========================================================================================	
	case "cust_view":
		check_authority(1,1,"view");
		$op['cust'] = $cust->get($PHP_id);  //���X�ӵ��O��

		if (!$op['cust']) {
			$op['msg'] = $cust->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

				# �O���ϥΪ̰ʺA
//		$log->log_add(0,"11V","�s���Ȥ��ơG".$op['cust']['cust_s_name']);

		$op['msg'] = $cust->msg->get(2);
#####2006.11.14�W�[�O���ثe���� start			
			$op['now_pp'] = $PHP_sr_startno;
#####2006.11.14�W�[�O���ثe���� end
	page_display($op, 1, 1, $TPL_CUST_VIEW);	    	    
	break;

//=======================================================
	case "cust_edit":
		check_authority(1,1,"edit");
		$op['cust'] = $cust->get($PHP_id);
		if (!$op['cust']) {
			$op['msg'] = $cust->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$op['country_select'] = $arry2->select($COUNTRY,$op['cust']['country'],"PHP_cust_country","select","");  //�~�ȳ��� select���
	
			$op['msg'] = $cust->msg->get(2);
				# �O���ϥΪ̰ʺA
//		$log->log_add(0,"11E","�i�J��s�Ȥ�O�� :".$op[cust][cust_s_name]);
#####2006.11.14�W�[�O���ثe���� start			
			$op['now_pp'] = $PHP_sr_startno;
#####2006.11.14�W�[�O���ثe���� end
		page_display($op, 1, 1, $TPL_CUST_EDIT);
		break;

//=======================================================
	case "do_cust_edit":
		check_authority(1,1,"edit");
		$argv = array(	"id"				=>  $PHP_id,
						"dept_code"			=>	$PHP_dept_code,
						"country"			=>	$PHP_cust_country,
						"cust_s_name"		=>	$PHP_cust_s_name,
						"cust_f_name"		=>	$PHP_cust_f_name,
						"cust_init_name"	=>	$PHP_cust_init_name,
						"cntc_phone"		=>	$PHP_cntc_phone,
						"cntc_addr"			=>	$PHP_cntc_addr,
						"cntc_person1"		=>	$PHP_cntc_person1,
						"cntc_cell1"		=>	$PHP_cntc_cell1,
						"email1"			=>	$PHP_email1,
						"cntc_person2"		=>	$PHP_cntc_person2,
						"cntc_cell2"		=>	$PHP_cntc_cell2,
						"email2"			=>	$PHP_email2,
						"cntc_fax"			=>	$PHP_cntc_fax
				);

		if (!$cust->edit($argv)) {
			$op['msg'] = $cust->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;	    
		}

					# �O���ϥΪ̰ʺA
		$message = "Update customer:[$PHP_cust_s_name]context�E";
		$log->log_add(0,"11E",$message);

	//--------------------------------------------------2005/0908

		$in_id = $GLOBALS['SCACHE']['ADMIN']['dept'];
		$in_dept = '';
		$where_str ='';
		$manager ='';
		$dept_select = array();

		$sales_dept = get_sales_dept(); // ���X �~�Ȫ����� [���tK0] ------
		
		if ($in_id=='HJ' || $in_id=='CL' || $in_id=='WX' || $in_id	=='LY')
		{
			$in_dept=$in_id	;
		}else{
		for ($i=0; $i<count($sales_dept);$i++){
			if($in_id == $sales_dept[$i]){
				$in_dept = $sales_dept[$i];   // �p�G�O�~�ȳ��i�J �hdept_code ���w�ӷ~�ȳ�---
			}
		}
		}
		
		if (!$in_dept) {    // ���O�~�ȳ��H[�]���t K0 ���H ]�i�J��
			$manager = 1;
			$dept_select = $arry2->select($sales_dept,"","PHP_dept_code","select","");  //�����Oselect���
		} else {
			$where_str = " WHERE dept = '".$in_dept."' ";
		}

			if (!$op = $cust->search(0,$where_str)) {    // �s�X���ݳ����������Ȥ�O��
				$op['msg']= $cust->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

		//   �]�� search �N $op���m �ҥH�A�a�J�䥦�Ѽ�
		$op['dept_code'] = $in_dept;
		$op['dept_select'] = $dept_select;
		$op['manager_flag'] = $manager;

		$op['msg'] = $cust->msg->get(2);
		$op['msg'][] = $message;
			unset($argv);  // �R�� �Ѽ�
			page_display($op, 1, 1, $TPL_CUST);    	    
		break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "cust_del":

						// ���ݭn manager login �~��u�����R�� user
		if(!$admin->is_power(1,1,"view") && !($GLOBALS['SCACHE']['ADMIIN']['id'] == 'SA')){ 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$op['cust'] = $cust->get($PHP_id);
		
		if (!$cust->del($PHP_id)) {
			$op['msg'] = $cust->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;	    
		}
					# �O���ϥΪ̰ʺA

		$message = "Delete customer:[".$op['cust']['cust_s_name']."] Record�C";
		$log->log_add(0,"11D",$message);

	//--------------------------------------------------2005/0908

		$in_id = $GLOBALS['SCACHE']['ADMIN']['dept'];
		$in_dept = '';
		$where_str ='';
		$manager ='';
		$dept_select = array();

		$sales_dept = get_sales_dept(); // ���X �~�Ȫ����� [���tK0] ------
		if ($in_id=='HJ' || $in_id=='CL' || $in_id=='WX' || $in_id	=='LY')
		{
			$in_dept=$in_id	;
		}else{
		for ($i=0; $i<count($sales_dept);$i++){
			if($in_id == $sales_dept[$i]){
				$in_dept = $sales_dept[$i];   // �p�G�O�~�ȳ��i�J �hdept_code ���w�ӷ~�ȳ�---
			}
		}
		}
		if (!$in_dept) {    // ���O�~�ȳ��H[�]���t K0 ���H ]�i�J��
			$manager = 1;
			$dept_select = $arry2->select($sales_dept,"","PHP_dept_code","select","");  //�����Oselect���
		} else {
			$where_str = " WHERE dept = '".$in_dept."' ";
		}

			if (!$op = $cust->search(0,$where_str)) {    // �s�X���ݳ����������Ȥ�O��
				$op['msg']= $cust->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

		//   �]�� search �N $op���m �ҥH�A�a�J�䥦�Ѽ�
		$op['dept_code'] = $in_dept;
		$op['dept_select'] = $dept_select;
		$op['manager_flag'] = $manager;

		$op['msg'] = $cust->msg->get(2);
		$op['msg'][] = $message;
			unset($argv);  // �R�� �Ѽ�			
			page_display($op, 1, 1, $TPL_CUST); 		    	    
		break;

  
  
  
  
  
  //=======================================================
  //		job   62   USER  
  //=======================================================
    case "user":
 
		check_authority(6,2,"view");
		$where_str = $manager_flag ='';

		// -------------------- �P�_ �i�J������ �O�_�� GM , SA, �� K0 ���H ------------------------------
		$user_id = $GLOBALS['SCACHE']['ADMIN']['dept'];

		// �p�G�O manager �i�J��...
		 //�p�G�O GM �� SA �� K0�h�Ǧ^ �����U�Կ��  
		 //�~�ȳ��h�u�C�X�~�ȳ������ -----// ��l�ҶǦ^�ӳ���ID
		if($user_id == "K0"){
			$where_str = " WHERE dept LIKE 'K%' ";
			$manager_flag = 1;
		}elseif($user_id == "J0"){
			$where_str = " WHERE dept LIKE 'J%' ";
			$manager_flag = 1;
		}elseif($user_id == "T0"){
			$where_str = " WHERE dept LIKE 'T%' ";
			$manager_flag = 1;
		}elseif($user_id == "GM" || $user_id == "SA"){
			$manager_flag = 1;
		}else{
			$where_str = " WHERE dept = '".$user_id."' ";
		}

		if (!$op = $user->search(0,$where_str)) {
			$op['msg']= $user->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$mgr = get_manager_dept();   //��X���Ҫ������d��~~~~

		// -------------------- �P�_ �i�J������ �O�_�� GM , SA, �� K0 ���H ------------------------------
		$op['dept_select'] = $mgr['dept_select'];
		$op['dept_id'] = $mgr['dept_id'];
		$op['manager_flag'] = $manager_flag;
		
		$op['msg'] = $user->msg->get(2);
		if (isset($PHP_sort))$op['sort']=$PHP_sort;
		page_display($op, 6, 2, $TPL_USER);		    	    
	break;



//=======================================================
    case "user_add":

		check_authority(6,2,"add");
		$where_str = $manager_flag ='';

		// -------------------- �P�_ �i�J������ �O�_�� GM , SA, �� K0 ���H ------------------------------
		$user_id = $GLOBALS['SCACHE']['ADMIN']['dept'];

		// �p�G�O manager �i�J��...
		 //�p�G�O GM �� SA �� K0�h�Ǧ^ �����U�Կ��  
		 //�~�ȳ��h�u�C�X�~�ȳ������ -----// ��l�ҶǦ^�ӳ���ID
		if($user_id == "K0"){
			$where_str = " WHERE dept LIKE 'K%' ";
			$manager_flag = 1;
		}elseif($user_id == "J0"){
			$where_str = " WHERE dept LIKE 'J%' ";
			$manager_flag = 1;
		}elseif($user_id == "GM" || $user_id == "SA"){
			$manager_flag = 1;
		}else{
			$where_str = " WHERE dept = '".$user_id."' ";
		}

		if (!$op = $user->search(0,$where_str)) {
			$op['msg']= $user->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$mgr = get_manager_dept();   //��X���Ҫ������d��~~~~

		// -------------------- �P�_ �i�J������ �O�_�� GM , SA, �� K0 ���H ------------------------------
		$op['dept_select'] = $mgr['dept_select'];
		$op['dept_id'] = $mgr['dept_id'];
		$op['manager_flag'] = $manager_flag;
		
		// �p�G�O manager �i�J��...
		// ���ݥ�select �����O --- �o�S�S�� select �� ----
		if (($op['manager_flag'] == 1) && ($PHP_dept_code =="")) { 

				$op['msg'][] = "sorry! please select the Department first !";

				page_display($op, 6, 2, $TPL_USER);		    	    
					break;
		}

			//���T��J�� �]��F������ ���X�ӳ��������Ҧ��u�@�էO
				$where_str = " WHERE dept_code='$PHP_dept_code'";
				$team_code = $team->get_fields('team_code', $where_str);   // ���X������TEAM�N��
				if(!$team_code){
					$op['msg'][] = "sorry! you have to add working team in this dept. first!";
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
				}
				
					// �u�@�էO�� select �����
				$op['team_select'] = $arry2->select($team_code,"","PHP_team_code","select","");  
				// 2006/05/02 adding .....
				$op['dept_code'] = $PHP_dept_code;
		    	    
			page_display($op, 6, 2, $TPL_USER_ADD);
		break;




//=======================================================
	case "do_user_add":

		check_authority(6,2,"add");
		$parm = array(	"dept_code"			=>	$PHP_dept_code,
						"emp_id"			=>	$PHP_emp_id,
						"login_id"			=>	$PHP_login_id,
						"login_pass1"		=>	$PHP_login_pass1,
						"login_pass2"		=>	$PHP_login_pass2,
						"team_code"			=>	$PHP_team_code,
						"name"				=>	$PHP_name,
						"phone"				=>	$PHP_phone,
						"cellphone"			=>	$PHP_cellphone,
						"email"				=>	$PHP_email
				);

				$op['user'] = $parm;

		// ���ˬd �O�_�w���user���u�@�էO  ��X perm
		if (!$parm['team_code']) {
			$op['msg'][] ="Error ! please select Team code !";

				$where_str = " WHERE dept_code='".$PHP_dept_code."'";
				$team_code = $team->get_fields('team_code', $where_str);   // ���X������TEAM�N��
					// �u�@�էO�� select �����
				$op['team_select'] = $arry2->select($team_code,"$PHP_team_code","PHP_team_code","select",""); 
				$op['dept_code'] = $parm['dept_code'];
				if ($op['msg']){ $op['msg_YES'] = 1; } else { $op['msg_YES'] = "" ;}

			$layout->assign($op);
			$layout->display($TPL_USER_ADD);		    	    
	    	break;
	
		}
			# ���X�ӲէO�N���� perm
			$where_str = "team_code ='".$PHP_team_code."' AND dept_code='".$PHP_dept_code."' ";
			$parm['perm'] = $team->get_field('perm', $where_str);

			$f1 = $user->check($parm);
			if ($f1) {  // ���\��J��Ʈ�

				// �Ѷ} permission
			$perm_array = $admin->extra_perm_str($parm['perm']);
			$op['perm'] = $admin->decode_perm_str($perm_array);
					# �O���ϥΪ̰ʺA
	//		$log->log_add(0,"62A","�i�J�T�{�s�Wuser :".$parm['login_id']);


			
			// �Ѷ} permission
//		$perm_array = $admin->extra_perm_str($_SESSION['SCACHE']['ADMIN']['perm']);
//		$op['perm'] = $admin->decode_perm_str($perm_array);

//---------------------------- 2005/09/13  adding......................
//  2005/02/02  �[�J �i�J�̤��v������ ---- �ȯ����L�[��Puser�@�ˤj���v��
//  �]�wuser �X�� ---------

		$A_Item = $GLOBALS['A_Item'];   // ����config.admin����menu��
		$op['power_flag'] = array();

	  for ($i=1;$i<=count($A_Item);$i++)	{
		  for ($j=1;$j<=(count($A_Item[$i])-1);$j++)  {

			if($admin->is_power($i,$j,"view")){ 
						$op['power_flag'][$i][$j]['view'] =1;
			} else {	$op['power_flag'][$i][$j]['view'] ='';	}

			if($admin->is_power($i,$j,"add")){ 
						$op['power_flag'][$i][$j]['add'] =1;
			} else {	$op['power_flag'][$i][$j]['add'] ='';	}

			if($admin->is_power($i,$j,"edit")){ 
						$op['power_flag'][$i][$j]['edit'] =1;
			} else {	$op['power_flag'][$i][$j]['edit'] ='';	}

			if($admin->is_power($i,$j,"del")){ 
						$op['power_flag'][$i][$j]['del'] =1;
			} else {	$op['power_flag'][$i][$j]['del'] ='';	}
		  }
	  }
//---------------------------- 2005/09/13  adding............(end) ..........

			//menu ���W�� ���v���W�� �H�N���a�J
			$op['mtitle'] = $GLOBALS['M_title'];  //2006/06/04 add
			$op['job'] = $GLOBALS['p_job'];  //2006/06/04 add
 
			page_display($op, 6, 2, $TPL_CFM_USER_ADD);
			break;
	
		}	else {  // ��S�����\��J�s�Wuser������....

			$op['msg'] = $user->msg->get(2);
			$op['user'] = $parm;
			$op['dept_code'] = $parm['dept_code'];
					// ���X�էO���

				
				$where_str = " WHERE dept_code='".$parm['dept_code']."'";
				$team_code = $team->get_fields('team_code', $where_str);   // ���X������TEAM�N��
					// �u�@�էO�� select �����
				$op['team_select'] = $arry2->select($team_code,"$PHP_team_code","PHP_team_code","select",""); 
				$op['dept_code'] = $parm['dept_code'];
				if ($op['msg']){ $op['msg_YES'] = 1; } else { $op['msg_YES'] = "" ;}

//			//menu ���W�� ���v���W�� �H�N���a�J
//			$op['mtitle'] = $GLOBALS['M_title'];  //2006/06/04 add
//			$op['job'] = $GLOBALS['p_job'];  //2006/06/04 add
		
			page_display($op, 6, 2, $TPL_USER_ADD);
		    	    
		break;

		}

 //=======================================================
	case "cfm_user_add":

		check_authority(6,2,"add");


		$parm = array(	"dept_code"			=>	$PHP_dept_code,
						"emp_id"			=>	$PHP_emp_id,
						"login_id"			=>	$PHP_login_id,
						"login_pass"		=>	$PHP_login_pass,
						"team_code"			=>	$PHP_team_code,
						"name"				=>	$PHP_name,
						"phone"				=>	$PHP_phone,
						"cellphone"			=>	$PHP_cellphone,
						"email"				=>	$PHP_email,
						"perm_dec"			=>	$PHP_perm_dec
				);

				$op['user'] = $parm;
	
  		// �v���}�C�令�r��
		$perm_array = $parm['perm_dec'];
		$parm['perm'] = $admin->encode_perm_str($perm_array);
		$f1 = $user->add($parm);

		if (!$op = $user->search(0)) {
			$op['msg']= $user->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$where_str = $manager_flag = '';
		
		// -------------------- �P�_ �i�J������ �O�_�� GM , SA, �� K0 ���H ------------------------------
		$user_id = $GLOBALS['SCACHE']['ADMIN']['dept'];

		// �p�G�O manager �i�J��...
		 //�p�G�O GM �� SA �� K0�h�Ǧ^ �����U�Կ��  
		 //�~�ȳ��h�u�C�X�~�ȳ������ -----// ��l�ҶǦ^�ӳ���ID
		if($user_id == "K0"){
			$where_str = " WHERE dept LIKE 'K%' ";
			$manager_flag = 1;
		}elseif($user_id == "J0"){
			$where_str = " WHERE dept LIKE 'J%' ";
			$manager_flag = 1;
		}elseif($user_id == "T0"){
			$where_str = " WHERE dept LIKE 'T%' ";
			$manager_flag = 1;
		}elseif($user_id == "GM" || $user_id == "SA"){
			$manager_flag = 1;
		}else{
			$where_str = " WHERE dept = '".$user_id."' ";
		}

		if (!$op = $user->search(0,$where_str)) {
			$op['msg']= $user->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$mgr = get_manager_dept();   //��X���Ҫ������d��~~~~

		// -------------------- �P�_ �i�J������ �O�_�� GM , SA, �� K0 ���H ------------------------------
		$op['dept_select'] = $mgr['dept_select'];
		$op['dept_id'] = $mgr['dept_id'];
		$op['manager_flag'] = $manager_flag;
#####2007.04.11�W�[�O���j�M start			
			$op['sort']="dept";			
#####2007.4.11�W�[�O���j�M end
			
			$op['msg'] = $team->msg->get(2);

		page_display($op, 6, 2, $TPL_USER);
		    	    
	
	break;

//========================================================================================	
	case "user_view":

		check_authority(6,2,"view");

		$op['user'] = $user->get($PHP_id);  //���X�ӵ��O��

		if (!$op['user']) {
			$op['msg'] = $team->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
			// �Ѷ} permission
		$perm_array = $admin->extra_perm_str($op['user']['perm']);
		$op['perm'] = $admin->decode_perm_str($perm_array);

				# �O���ϥΪ̰ʺA
//		$log->log_add(0,"62V","�s��USER ��ơG".$op['user']['login_id']);

			$op['msg'] = $user->msg->get(2);

			//menu ���W�� ���v���W�� �H�N���a�J
			$op['mtitle'] = $GLOBALS['M_title'];  //2006/06/04 add
			$op['job'] = $GLOBALS['p_job'];  //2006/06/04 add
#####2006.11.14�W�[�O���ثe���� start			
			$op['now_pp'] = $PHP_sr_startno;			
#####2006.11.14�W�[�O���ثe���� end

#####2007.04.11�W�[�O���j�M start			
			$op['sort']=$PHP_sort;			
#####2007.11.11�W�[�O���j�M end
	
			page_display($op, 6, 2, $TPL_USER_VIEW);
	    	    
		break;

//=======================================================
	case "user_edit":

		check_authority(6,2,"edit");
		$op['user'] = $user->get($PHP_id);
		if (!$op['user']) {
			$op['msg'] = $user->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

			// �Ѷ} permission
		$perm_array = $admin->extra_perm_str($op['user']['perm']);
		$op['perm'] = $admin->decode_perm_str($perm_array);

			// �u�@�էO�� select �����
		$team_id = $op['user']['team_id'];
		$dept_code = $op['user']['dept'];
					
			// ���X�էO���
		$where_str = " WHERE dept_code = '$dept_code' ";
		$team_code = $team->get_fields('team_code', $where_str);   // ���X������TEAM�N��
		$op['team_select'] = $arry2->select($team_code,"$team_id","PHP_team_code","select",""); 

			$op['msg'] = $user->msg->get(2);

//  2005/02/02  �[�J �i�J�̤��v������ ---- �ȯ����L�[��Puser�@�ˤj���v��
//  �]�wuser �X�� ---------

		$A_Item = $GLOBALS['A_Item'];   // ����config.admin����menu��
		$op['power_flag'] = array();

	  for ($i=1;$i<=count($A_Item);$i++)	{
		  for ($j=1;$j<=(count($A_Item[$i])-1);$j++)  {

			if($admin->is_power($i,$j,"view")){ 
						$op['power_flag'][$i][$j]['view'] =1;
			} else {	$op['power_flag'][$i][$j]['view'] ='';	}

			if($admin->is_power($i,$j,"add")){ 
						$op['power_flag'][$i][$j]['add'] =1;
			} else {	$op['power_flag'][$i][$j]['add'] ='';	}

			if($admin->is_power($i,$j,"edit")){ 
						$op['power_flag'][$i][$j]['edit'] =1;
			} else {	$op['power_flag'][$i][$j]['edit'] ='';	}

			if($admin->is_power($i,$j,"del")){ 
						$op['power_flag'][$i][$j]['del'] =1;
			} else {	$op['power_flag'][$i][$j]['del'] ='';	}
		  }
	  }
			//menu ���W�� ���v���W�� �H�N���a�J
			$op['mtitle'] = $GLOBALS['M_title'];  //2006/06/04 add
			$op['job'] = $GLOBALS['p_job'];  //2006/06/04 add
#####2006.11.14�W�[�O���ثe���� start			
			$op['now_pp'] = $PHP_sr_startno;
#####2006.11.14�W�[�O���ثe���� end
#####2007.04.11�W�[�O���j�M start			
			$op['sort']=$PHP_sort;			
#####2007.11.11�W�[�O���j�M end
 
		page_display($op, 6, 2, $TPL_USER_EDIT);

		break;

//=======================================================
	case "do_user_edit":

		check_authority(6,2,"edit");
		$argv = array(	"id"				=>	$PHP_id,
						"dept"				=>	$PHP_dept,
						"emp_id"			=>	$PHP_emp_id,
						"login_id"			=>	$PHP_login_id,
						"team_id"			=>	$PHP_team_code,
						"name"				=>	$PHP_name,
						"phone"				=>	$PHP_phone,
						"cellphone"			=>	$PHP_cellphone,
						"email"				=>	$PHP_email,
						"perm_dec"			=>	$PHP_perm_dec
				);

  		// �v���}�C�令�r��
		$perm_array = $argv['perm_dec'];
		$argv['perm'] = $admin->encode_perm_str($perm_array);

		if (!$user->edit($argv)) {
			$op['msg'] = $user->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;	    
		}

					# �O���ϥΪ̰ʺA
		$message = "Update USER:[$PHP_login_id]context�Blimit�E";
		$log->log_add(0,"62E",$message);


	//		$op['msg'][] = $message;
	//	$redir_str = $PHP_SELF."?PHP_action=user";
	//		redirect_page($redir_str);
	//	break;
		
		$where_str = $manager_flag ='';

		// -------------------- �P�_ �i�J������ �O�_�� GM , SA, �� K0 ���H ------------------------------
		$user_id = $GLOBALS['SCACHE']['ADMIN']['dept'];

		// �p�G�O manager �i�J��...
		 //�p�G�O GM �� SA �� K0�h�Ǧ^ �����U�Կ��  
		 //�~�ȳ��h�u�C�X�~�ȳ������ -----// ��l�ҶǦ^�ӳ���ID
	if($user_id == "K0"){
			$where_str = " WHERE dept LIKE 'K%' ";
			$manager_flag = 1;
		}elseif($user_id == "J0"){
			$where_str = " WHERE dept LIKE 'J%' ";
			$manager_flag = 1;
		}elseif($user_id == "T0"){
			$where_str = " WHERE dept LIKE 'T%' ";
			$manager_flag = 1;
		}elseif($user_id == "GM" || $user_id == "SA"){
			$manager_flag = 1;
		}else{
			$where_str = " WHERE dept = '".$user_id."' ";
		}
		if (!$op = $user->search(0,$where_str)) {
			$op['msg']= $user->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$mgr = get_manager_dept();   //��X���Ҫ������d��~~~~

		// -------------------- �P�_ �i�J������ �O�_�� GM , SA, �� K0 ���H ------------------------------
		$op['dept_select'] = $mgr['dept_select'];
		$op['dept_id'] = $mgr['dept_id'];
		$op['manager_flag'] = $manager_flag;
		
		$op['msg'][] = $message;
#####2006.11.14�W�[�O���ثe���� start			
			$op['now_pp'] = $PHP_sr_startno;
#####2006.11.14�W�[�O���ثe���� end
#####2007.04.11�W�[�O���j�M start			
			$op['sort']=$PHP_sort;			
#####2007.11.11�W�[�O���j�M end
		page_display($op, 6, 2, $TPL_USER);
	    	    
	break;
		
		
		
		

//=======================================================
	case "user_disable":

		check_authority(6,2,"del");
			// �إߧ�s��W��줧�}�C���
		$update = array();
		$update['id'] = $PHP_id;
		$update['field_name'] = "active";
		$update['field_value'] = "N";

		if (!$user->update_field($update)) {
			$op['msg'] = $user->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;	    
		}

					# �O���ϥΪ̰ʺA
		$message = "USER:[$PHP_login_id]ex-rights�E";
		$log->log_add(0,"62D",$message);

		$where_str = $manager_flag ='';

		// -------------------- �P�_ �i�J������ �O�_�� GM , SA, �� K0 ���H ------------------------------
		$user_id = $GLOBALS['SCACHE']['ADMIN']['dept'];

		// �p�G�O manager �i�J��...
		 //�p�G�O GM �� SA �� K0�h�Ǧ^ �����U�Կ��  
		 //�~�ȳ��h�u�C�X�~�ȳ������ -----// ��l�ҶǦ^�ӳ���ID
	if($user_id == "K0"){
			$where_str = " WHERE dept LIKE 'K%' ";
			$manager_flag = 1;
		}elseif($user_id == "J0"){
			$where_str = " WHERE dept LIKE 'J%' ";
			$manager_flag = 1;
		}elseif($user_id == "T0"){
			$where_str = " WHERE dept LIKE 'T%' ";
			$manager_flag = 1;
		}elseif($user_id == "GM" || $user_id == "SA"){
			$manager_flag = 1;
		}else{
			$where_str = " WHERE dept = '".$user_id."' ";
		}

		if (!$op = $user->search(0,$where_str)) {
			$op['msg']= $user->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$mgr = get_manager_dept();   //��X���Ҫ������d��~~~~

		// -------------------- �P�_ �i�J������ �O�_�� GM , SA, �� K0 ���H ------------------------------
		$op['dept_select'] = $mgr['dept_select'];
		$op['dept_id'] = $mgr['dept_id'];
		$op['manager_flag'] = $manager_flag;
		
		$op['msg'][] = $message;

#####2007.04.11�W�[�O���j�M start			
			$op['sort']=$PHP_sort;			
#####2007.4.11�W�[�O���j�M end

		page_display($op, 6, 2, $TPL_USER);
	    	    
	break;



//=======================================================
	case "user_enable":

		check_authority(6,2,"del");

			// �إߧ�s��W��줧�}�C���
		$update = array();
		$update['id'] = $PHP_id;
		$update['field_name'] = "active";
		$update['field_value'] = "Y";

		if (!$user->update_field($update)) {
			$op['msg'] = $user->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;	    
		}

					# �O���ϥΪ̰ʺA
		$message = "USER:[$PHP_login_id]re-rights�E";
		$log->log_add(0,"62D",$message);

		$where_str = $manager_flag ='';

		// -------------------- �P�_ �i�J������ �O�_�� GM , SA, �� K0 ���H ------------------------------
		$user_id = $GLOBALS['SCACHE']['ADMIN']['dept'];

		// �p�G�O manager �i�J��...
		 //�p�G�O GM �� SA �� K0�h�Ǧ^ �����U�Կ��  
		 //�~�ȳ��h�u�C�X�~�ȳ������ -----// ��l�ҶǦ^�ӳ���ID
	if($user_id == "K0"){
			$where_str = " WHERE dept LIKE 'K%' ";
			$manager_flag = 1;
		}elseif($user_id == "J0"){
			$where_str = " WHERE dept LIKE 'J%' ";
			$manager_flag = 1;
		}elseif($user_id == "T0"){
			$where_str = " WHERE dept LIKE 'T%' ";
			$manager_flag = 1;
		}elseif($user_id == "GM" || $user_id == "SA"){
			$manager_flag = 1;
		}else{
			$where_str = " WHERE dept = '".$user_id."' ";
		}

		if (!$op = $user->search(0,$where_str)) {
			$op['msg']= $user->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$mgr = get_manager_dept();   //��X���Ҫ������d��~~~~

		// -------------------- �P�_ �i�J������ �O�_�� GM , SA, �� K0 ���H ------------------------------
		$op['dept_select'] = $mgr['dept_select'];
		$op['dept_id'] = $mgr['dept_id'];
		$op['manager_flag'] = $manager_flag;
		
		$op['msg'][] = $message;

#####2007.04.11�W�[�O���j�M start			
			$op['sort']=$PHP_sort;			
#####2007.04.11�W�[�O���j�M end	
		page_display($op, 6, 2, $TPL_USER);
	    	    
	break;




#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "user_del":

			// ���ݭn manager login �~��u�����R�� user
		check_authority(6,2,"del");
		$op['user'] = $user->get($PHP_id);
		
		if (!$user->del($PHP_id)) {
			$op['msg'] = $user->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;	    
		}
					# �O���ϥΪ̰ʺA

		$message = "Delete Dept:[".$op['user']['dept']."]  USER:[".$op['user']['login_id']."]�C";
		$log->log_add(0,"62D",$message);


//----------------------------------------- new ----------------		
		$where_str = $manager_flag ='';

		// -------------------- �P�_ �i�J������ �O�_�� GM , SA, �� K0 ���H ------------------------------
		$user_id = $GLOBALS['SCACHE']['ADMIN']['dept'];

		// �p�G�O manager �i�J��...
		 //�p�G�O GM �� SA �� K0�h�Ǧ^ �����U�Կ��  
		 //�~�ȳ��h�u�C�X�~�ȳ������ -----// ��l�ҶǦ^�ӳ���ID
		if($user_id == "K0"){
			$where_str = " WHERE dept LIKE 'K%' ";
			$manager_flag = 1;
		}elseif($user_id == "GM" || $user_id == "SA"){
			$manager_flag = 1;
		}else{
			$where_str = " WHERE dept = '".$user_id."' ";
		}

		if (!$op = $user->search(0,$where_str)) {
			$op['msg']= $user->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$mgr = get_manager_dept();   //��X���Ҫ������d��~~~~

		// -------------------- �P�_ �i�J������ �O�_�� GM , SA, �� K0 ���H ------------------------------
		$op['dept_select'] = $mgr['dept_select'];
		$op['dept_id'] = $mgr['dept_id'];
		$op['manager_flag'] = $manager_flag;
		
		$op['msg'][] = $message;
		page_display($op, 6, 2, $TPL_USER);
		    	    
	break;

		
  
  
  
  
  
  
  
  
  
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		job 61 TEAM
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "team":
 
		if(!$admin->is_power(6,1,"view")){ 
			$op['msg'][] = "sorry! you don't have this Authorized !";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		if (!$op = $team->search(0)) {
			$op['msg']= $team->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$op['msg'] = $team->msg->get(2);

		$allow = $admin->decode_perm(6,1);   // �]�w �s�W�R���v��
		if ($op['msg']){ $op['msg_YES'] = 1; } else { $op['msg_YES'] = "" ;}
		if ($allow['view'])		{	$op['view_flag'] = 1;} else {	$op['view_flag'] = "";}
		if ($allow['add'])		{	$op['add_flag'] = 1;} else {	$op['add_flag'] = "";}
		if ($allow['edit'])		{	$op['edit_flag'] = 1;} else {	$op['edit_flag'] = "";}
		if ($allow['del'])		{	$op['del_flag'] = 1;} else {	$op['del_flag'] = "";}
#####2006.11.14�W�[�O���ثe���� start			
			$op['now_pp'] = $PHP_sr_startno;
#####2006.11.14�W�[�O���ثe���� end	
		$layout->assign($op);
		$layout->display($TPL_TEAM);		    	    
	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "team_add":

		if(!$admin->is_power(6,1,"add")){ 
			$op['msg'][] = "sorry! you don't have this Authorized !";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		
		// �p�G�O manager �i�J��...
//		if (($GLOBALS['SCACHE']['ADMIN']['id'] == "SA" ) || ($GLOBALS['SCACHE']['ADMIN']['id'] == "GM" ) || ($GLOBALS['SCACHE']['ADMIN']['id'] == "P0" ) || ($GLOBALS['SCACHE']['ADMIN']['id'] == "J0" )) {
//			$op['manager_flag'] = 1;
//		}

		//2006/05/02 update
		$op['dept_id'] = get_dept_id();
		
		// �p�G�O manager �i�J��...
		if (substr($op['dept_id'],0,7) == "<select"){
			$op['manager_flag'] = 1;
		}

// �|�ݴ��ե� �����D�޶i�J��O�_�O�������O�N��
// �p�G�Omanager ���� �i�[�W�����O --- �s�@�����O�U�Կ��


//  2005/02/02  �[�J �i�J�̤��v������ ---- �ȯ����L�[��Puser�@�ˤj���v��
//  �]�wuser �X�� ---------

		$A_Item = $GLOBALS['A_Item'];   // ����config.admin����menu��
		$op['power_flag'] = array();

		  for ($i=1;$i<=count($A_Item);$i++)	{
			  for ($j=1;$j<=(count($A_Item[$i])-1);$j++)  {

				if($admin->is_power($i,$j,"view")){ 
							$op['power_flag'][$i][$j]['view'] =1;
				} else {	$op['power_flag'][$i][$j]['view'] ='';	}

				if($admin->is_power($i,$j,"add")){ 
							$op['power_flag'][$i][$j]['add'] =1;
				} else {	$op['power_flag'][$i][$j]['add'] ='';	}

				if($admin->is_power($i,$j,"edit")){ 
							$op['power_flag'][$i][$j]['edit'] =1;
				} else {	$op['power_flag'][$i][$j]['edit'] ='';	}

				if($admin->is_power($i,$j,"del")){ 
							$op['power_flag'][$i][$j]['del'] =1;
				} else {	$op['power_flag'][$i][$j]['del'] ='';	}
			  }
		  }

		//menu ���W�� ���v���W�� �H�N���a�J
		$op['mtitle'] = $GLOBALS['M_title'];  //2006/06/04 add
		$op['job'] = $GLOBALS['p_job'];  //2006/06/04 add

			$layout->assign($op);
			$layout->display($TPL_TEAM_ADD);		    	    
		break;

 
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_team_add":

		if(!$admin->is_power(6,1,"add")){ 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$parm = array(	"team_code"		=>	$PHP_team_code,
						"team_name"		=>	$PHP_team_name,
						"dept_code"		=>	$PHP_dept_code,
						"perm"			=>	$PHP_perm_dec,
				);

		$f1 = $team->add($parm);
		if ($f1) {
				# �O���ϥΪ̰ʺA
			$log->log_add(0,"61A","Append[".$PHP_dept_code."]Dept Team limit: [".$PHP_team_code."]" );
			unset($parm);  // �R�� �Ѽ�
		}	else {
		    $op['msg'] = $team->msg->get(2);
			$op['team'] = $parm;
		
		if ($GLOBALS['SCACHE']['ADMIN']['id'] == "SA" ) {   // �p�G�O manager �i�J��...
			$op['manager_flag'] = 1;
			$dept_def = $dept->get_fields('dept_code');   // ���X�����������O�N��
			$op['dept_select'] = $arry2->select($dept_def,"","PHP_dept_code","select","");  //�����Oselect���
		}

// �|�ݴ��ե� �����D�޶i�J��O�_�O�������O�N��
// �p�G�Omanager ���� �i�[�W�����O --- �s�@�����O�U�Կ��

			if ($op['msg']){ $op['msg_YES'] = 1; } else { $op['msg_YES'] = "" ;}

				$layout->assign($op);
				$layout->display($TPL_TEAM_ADD);		    	    
					break;
			}

		if (!$op = $team->search(0)) {
			$op = array();
			$op['msg']= $team->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$allow = $admin->decode_perm(6, 1);   // �]�w �s�W�R���v��
			$op['msg'] = $team->msg->get(2);
			if ($op['msg']){ $op['msg_YES'] = 1; } else { $op['msg_YES'] = "" ;}
			$allow = $admin->decode_perm(6,1);   // �]�w �s�W�R���v��
			if ($allow['view'])		{	$op['view_flag'] = 1;} else {	$op['view_flag'] = "";}
			if ($allow['add'])		{	$op['add_flag'] = 1;} else {	$op['add_flag'] = "";}
			if ($allow['edit'])		{	$op['edit_flag'] = 1;} else {	$op['edit_flag'] = "";}
			if ($allow['del'])		{	$op['del_flag'] = 1;} else {	$op['del_flag'] = "";}
	
		$layout->assign($op);
		$layout->display($TPL_TEAM);		    	    
	break;
	
	
//========================================================================================	
	case "team_view":

		if(!$admin->is_power(6,1,"view")){ 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$op['team'] = $team->get($PHP_id);  //���X�ӵ��O��

		if (!$op['team']) {
			$op['msg'] = $team->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
			// �Ѷ} permission
		$perm_array = $admin->extra_perm_str($op['team']['perm']);
		$op['perm'] = $admin->decode_perm_str($perm_array);

				# �O���ϥΪ̰ʺA
//		$log->log_add(0,"61V","�s���u�@�էO�G".$op['team']['team_name']);

		//menu ���W�� ���v���W�� �H�N���a�J
		$op['mtitle'] = $GLOBALS['M_title'];  //2006/06/04 add
		$op['job'] = $GLOBALS['p_job'];  //2006/06/04 add
			
			$op['msg'] = $team->msg->get(2);
			if ($op['msg']){ $op['msg_YES'] = 1; } else { $op['msg_YES'] = "" ;}
			$allow = $admin->decode_perm(6,1);   // �]�w �s�W�R���v��
			if ($allow['view'])		{	$op['view_flag'] = 1;} else {	$op['view_flag'] = "";}
			if ($allow['add'])		{	$op['add_flag'] = 1;} else {	$op['add_flag'] = "";}
			if ($allow['edit'])		{	$op['edit_flag'] = 1;} else {	$op['edit_flag'] = "";}
			if ($allow['del'])		{	$op['del_flag'] = 1;} else {	$op['del_flag'] = "";}

#####2006.11.14�W�[�O���ثe���� start			
			$op['now_pp'] = $PHP_sr_startno;
#####2006.11.14�W�[�O���ثe���� end	
			$layout->assign($op);
			$layout->display($TPL_TEAM_VIEW);		    	    
		break;

//=======================================================
	case "team_edit":

		if(!$admin->is_power(6,1,"edit")){ 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$op['team'] = $team->get($PHP_id);

// �p�G�O manager �i�J��...
//		if ($GLOBALS['SCACHE']['ADMIN']['id'] == "SA" ) { 
//			$op['manager_flag'] = 1;
//		}
//		$op['dept_id'] = get_dept_id($op['team']['dept_code']);

		//2006/05/02 update
		$op['dept_id'] = get_dept_id();
		
		// �p�G�O manager �i�J��...
		if (substr($op['dept_id'],0,7) == "<select"){
			$op['manager_flag'] = 1;
		}

// �|�ݴ��ե� �����D�޶i�J��O�_�O�������O�N��
// �p�G�Omanager ���� �i�[�W�����O --- �s�@�����O�U�Կ��

		if (!$op['team']) {
			$op['msg'] = $team->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
			// �Ѷ} permission
		$perm_array = $admin->extra_perm_str($op['team']['perm']);
		$op['perm'] = $admin->decode_perm_str($perm_array);

//  2005/02/02  �[�J �i�J�̤��v������ ---- �ȯ����L�[��Puser�@�ˤj���v��
//  �]�wuser �X�� ---------

		$A_Item = $GLOBALS['A_Item'];   // ����config.admin����menu��
		$op['power_flag'] = array();

		  for ($i=1;$i<=count($A_Item);$i++)	{
			  for ($j=1;$j<=(count($A_Item[$i])-1);$j++)  {

				if($admin->is_power($i,$j,"view")){ 
							$op['power_flag'][$i][$j]['view'] =1;
				} else {	$op['power_flag'][$i][$j]['view'] ='';	}

				if($admin->is_power($i,$j,"add")){ 
							$op['power_flag'][$i][$j]['add'] =1;
				} else {	$op['power_flag'][$i][$j]['add'] ='';	}

				if($admin->is_power($i,$j,"edit")){ 
							$op['power_flag'][$i][$j]['edit'] =1;
				} else {	$op['power_flag'][$i][$j]['edit'] ='';	}

				if($admin->is_power($i,$j,"del")){ 
							$op['power_flag'][$i][$j]['del'] =1;
				} else {	$op['power_flag'][$i][$j]['del'] ='';	}
			  }
		  }
		$op['mtitle'] = $GLOBALS['M_title'];  //2006/06/04 add
		$op['job'] = $GLOBALS['p_job'];  //2006/06/04 add

				# �O���ϥΪ̰ʺA
//		$log->log_add(0,"61E","�i�J��s�s���v���O���� :".$op[team][team_code]);
#####2006.11.14�W�[�O���ثe���� start			
			$op['now_pp'] = $PHP_sr_startno;
#####2006.11.14�W�[�O���ثe���� end	
		$layout->assign($op);
		$layout->display($TPL_TEAM_EDIT);  
		break;

//=======================================================
	case "do_team_edit":

		if(!$admin->is_power(6,1,"edit")){ 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$argv = array(	"id"			=> $PHP_id,
						"dept_code"		=> $PHP_dept_code,
						"team_code"		=> $PHP_team_code,
						"team_name"		=> $PHP_team_name,
						"perm"			=> $PHP_perm_dec
					);
		if (!$team->edit($argv)) {
			$op['msg'] = $team->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;	    
		}

		// �C�X�Ҧ����O��~~~
		if (!$op = $team->search(0)) {
			$op['msg']= $team->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
			$op['msg'] = $team->msg->get(2);
			$message = "Success update Dept:[$PHP_dept_code] Team:[$PHP_team_name)]context and limit�E";
			$op['msg'][] = $message;

					# �O���ϥΪ̰ʺA
		$log->log_add(0,"61E",$message);

		$op['mtitle'] = $GLOBALS['M_title'];  //2006/06/04 add
		$op['job'] = $GLOBALS['p_job'];  //2006/06/04 add

			if ($op['msg']){ $op['msg_YES'] = 1; } else { $op['msg_YES'] = "" ;}
			$allow = $admin->decode_perm(6,1);   // �]�w �s�W�R���v��
			if ($allow['view'])		{	$op['view_flag'] = 1;} else {	$op['view_flag'] = "";}
			if ($allow['add'])		{	$op['add_flag'] = 1;} else {	$op['add_flag'] = "";}
			if ($allow['edit'])		{	$op['edit_flag'] = 1;} else {	$op['edit_flag'] = "";}
			if ($allow['del'])		{	$op['del_flag'] = 1;} else {	$op['del_flag'] = "";}
#####2006.11.14�W�[�O���ثe���� start			
			$op['now_pp'] = $PHP_sr_startno;
#####2006.11.14�W�[�O���ثe���� end	
		unset($argv);  // �R�� �Ѽ�
		$layout->assign($op);
		$layout->display($TPL_TEAM);  
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "team_del":

		if(!$admin->is_power(6,1,"del")){ 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$op['team'] = $team->get($PHP_id);
		
		if (!$team->del($PHP_id)) {
			$op['msg'] = $dept->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;	    
		}
					# �O���ϥΪ̰ʺA

		$message = "Delete Dept:[".$op['team']['dept_code']."] Team:[".$op['team']['team_name']."]�C";
		$log->log_add(0,"61D",$message);

		if (!$op = $team->search(0)) {
			$op = array();
			$op['msg']= $dept->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$op['msg'][] = $message;

			if ($op['msg']){ $op['msg_YES'] = 1; } else { $op['msg_YES'] = "" ;}
			$allow = $admin->decode_perm(6,1);   // �]�w �s�W�R���v��
			if ($allow['view'])	{	$op['view_flag'] = 1;} else {	$op['view_flag'] = "";}
			if ($allow['add'])	{	$op['add_flag'] = 1;} else {	$op['add_flag'] = "";}
			if ($allow['edit'])	{	$op['edit_flag'] = 1;} else {	$op['edit_flag'] = "";}
			if ($allow['del'])	{	$op['del_flag'] = 1;} else {	$op['del_flag'] = "";}
#####2006.11.14�W�[�O���ثe���� start			
			$op['now_pp'] = $PHP_sr_startno;
#####2006.11.14�W�[�O���ثe���� end	
		$layout->assign($op);
		$layout->display($TPL_TEAM);		    	    
	break;


//-----------------------------------------------------------------------------------
//		JOB  9-1   ��s�}�o [ ���ƶ}�o]
//
//-----------------------------------------------------------------------------------
    case "fabric":
		if(!$admin->is_power(9,1,"view")){ 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

			// creat cust combo box
		$op['select_cat'] = $arry2->select($FABRIC_CAT,'','PHP_cat','select','');  	
			// create combo box for vendor fields.....
			$where_str =" WHERE supl_cat ='�D��' ";
			$supl_def = $supl->get_fields('supl_s_name',$where_str);   // ���X ���������O�N��
		$op['select_supl'] = $arry2->select($supl_def,'','PHP_supl','select','');  	

		$op['msg']= $fabric->msg->get(2);

		$allow = $admin->decode_perm(9,1);   // �]�w �s�W�R���v��
		if ($op['msg']){ $op['msg_YES'] = 1; } else { $op['msg_YES'] = "" ;}
		if ($allow['view'])		{	$op['view_flag'] = 1;} else {	$op['view_flag'] = "";}
		if ($allow['add'])		{	$op['add_flag'] = 1;} else {	$op['add_flag'] = "";}
		if ($allow['edit'])		{	$op['edit_flag'] = 1;} else {	$op['edit_flag'] = "";}
		if ($allow['del'])		{	$op['del_flag'] = 1;} else {	$op['del_flag'] = "";}
		$layout->assign($op);
		$layout->display($TPL_FABRIC_SEARCH);		    	    
	break;

//=======================================================
    case "fabric_search":

		if(!$admin->is_power(9,1,"view")){ 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$parm = array(	"art_code"		=>  $PHP_art_code,
						"name"			=>  $PHP_name,
						"cat"			=>	$PHP_cat,
						"content"		=>	$PHP_content,
						"finish"		=>	$PHP_finish,
						"supl"			=>	$PHP_supl,
						"supl_ref"		=>	$PHP_supl_ref
				);
		if (!$op = $fabric->search(1)) {
			$op['msg']= $fabric->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

			$op['msg']= $fabric->msg->get(2);
			$op['cgi']= $parm;

		$allow = $admin->decode_perm(9,1);   // �]�w �s�W�R���v��
		if ($op['msg']){ $op['msg_YES'] = 1; } else { $op['msg_YES'] = "" ;}
		if ($allow['view'])		{	$op['view_flag'] = 1;} else {	$op['view_flag'] = "";}
		if ($allow['add'])		{	$op['add_flag'] = 1;} else {	$op['add_flag'] = "";}
		if ($allow['edit'])		{	$op['edit_flag'] = 1;} else {	$op['edit_flag'] = "";}
		if ($allow['del'])		{	$op['del_flag'] = 1;} else {	$op['del_flag'] = "";}
//$op['NEXT_page'] = 1;
//$op['PREV_page'] = 1;

			$layout->assign($op);
			$layout->display($TPL_FABRIC);  
		break;


//=======================================================
    case "fabric_add":
		
		if(!$admin->is_power(9,1,"add")){ 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
			$op['msg']= $fabric->msg->get(2);

		$allow = $admin->decode_perm(9,1);   // �]�w �s�W�R���v��
		if ($op['msg']){ $op['msg_YES'] = 1; } else { $op['msg_YES'] = "" ;}
		if ($allow['view'])		{	$op['view_flag'] = 1;} else {	$op['view_flag'] = "";}
		if ($allow['add'])		{	$op['add_flag'] = 1;} else {	$op['add_flag'] = "";}
		if ($allow['edit'])		{	$op['edit_flag'] = 1;} else {	$op['edit_flag'] = "";}
		if ($allow['del'])		{	$op['del_flag'] = 1;} else {	$op['del_flag'] = "";}

			// ���X�~�X...
			$dt = decode_date(1);
			$year_code = substr($dt['year'],-2);
			
			// ���X�ﶵ��� �ζǤJ���Ѽ�
			// pre  �s��....
		$op['fabric']['pre_art_code'] = "FK".$year_code."-...";

			// creat cat combo box
		$op['select_cat'] = $arry2->select($FABRIC_CAT,'','PHP_cat','select','');  	
		// creat fabric kind combo box
		$op['select_kind'] = $arry2->select($FABRIC_KIND,'','PHP_kind','select','');  	
			// create combo box for suplier .....
			$where_str =" WHERE supl_cat ='�D��' ";
			$supl_def = $supl->get_fields('supl_s_name',$where_str);   // ���X ���������O�N��
		$op['select_supl'] = $arry2->select($supl_def,'','PHP_supl','select','');  	
			// creat c/o combo box
		$op['select_co'] = $arry2->select($COUNTRY,'','PHP_co','select','');  	
			// creat location combo box
		$op['select_location'] = $arry2->select($COUNTRY,'','PHP_location','select','');  	
			// creat currency combo box
		$op['select_currency'] = $arry2->select($CURRENCY,'','PHP_currency','select','');  	
			// creat price_unit combo box
		$op['select_unit_price'] = $arry2->select($LOTS_PRICE_UNIT,'','PHP_unit_price','select','');  	
			// creat supply term combo box
		$op['select_term'] = $arry2->select($TRADE_TERM,'','PHP_term','select','');  	
			// creat weight unit combo box
		$op['select_unit_wt'] = $arry2->select($FABRIC_WT_UNIT,'','PHP_unit_wt','select','');  	
			// creat width unit combo box
		$op['select_unit_width'] = $arry2->select($FABRIC_WIDTH_UNIT,'','PHP_unit_width','select','');  	


		$layout->assign($op);
		$layout->display($TPL_FABRIC_ADD);		    	    

		break;

//=======================================================
	case "do_fabric_add":

		if(!$admin->is_power(9,1,"add")){ 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$parm = array(	"cat"			=>	$PHP_cat,
						"kind"			=>	$PHP_kind,
						"supl"			=>	$PHP_supl,
						"co"			=>	$PHP_co,
						"location"		=>	$PHP_location,
						"currency"		=>	$PHP_currency,
						"unit_price"	=>	$PHP_unit_price,
						"term"			=>	$PHP_term,
						"unit_wt"		=>	$PHP_unit_wt,

						"name"			=>	$PHP_name,
						"content"		=>	$PHP_content,
						"construct"		=>	$PHP_construct,
						"finish"		=>	$PHP_finish,
						"supl_ref"		=>	$PHP_supl_ref,
						"width"			=>	$PHP_width,
						"width_unit"	=>	$PHP_unit_width,
						"weight"		=>	$PHP_weight,
						"price"			=>	$PHP_price,
						"leadtime"		=>	$PHP_leadtime,

						"pic"			=>	$PHP_pic,
						"pic_upload"	=>	$PHP_pic_upload,

						"remark"		=>	$PHP_remark
			);
				$op['fabric'] = $parm;
			$check = $fabric->check($parm);
	// .....................................��J��� �����~��  �A�^��˥���J���
	if (!$check) {  
			$op['msg']= $fabric->msg->get(2);

				$allow = $admin->decode_perm(9,1);   // �]�w �s�W�R���v��
				if ($op['msg']){ $op['msg_YES'] = 1; } else { $op['msg_YES'] = "" ;}
				if ($allow['view'])		{	$op['view_flag'] = 1;} else {	$op['view_flag'] = "";}
				if ($allow['add'])		{	$op['add_flag'] = 1;} else {	$op['add_flag'] = "";}
				if ($allow['edit'])		{	$op['edit_flag'] = 1;} else {	$op['edit_flag'] = "";}
				if ($allow['del'])		{	$op['del_flag'] = 1;} else {	$op['del_flag'] = "";}

				// ���X�~�X...
				$dt = decode_date(1);
				$year_code = substr($dt['year'],-2);
				
				// ���X�ﶵ��� �ζǤJ���Ѽ�
				// PRE �s��....
		$op['fabric']['pre_art_code'] = "F".$year_code.".....";

			// creat fabric cat combo box
		$op['select_cat'] = $arry2->select($FABRIC_CAT,$op['fabric']['cat'],'PHP_cat','select','');  	
			// creat fabric kind combo box
		$op['select_kind'] = $arry2->select($FABRIC_KIND,$op['fabric']['kind'],'PHP_kind','select','');  	
			// create combo box for suplier .....
			$where_str =" WHERE supl_cat ='�D��' ";
			$supl_def = $supl->get_fields('supl_s_name',$where_str);   // ���X ���������O�N��
		$op['select_supl'] = $arry2->select($supl_def,$op['fabric']['supl'],'PHP_supl','select','');  	
			// creat c/o combo box
		$op['select_co'] = $arry2->select($COUNTRY,$op['fabric']['co'],'PHP_co','select','');  	
			// creat location combo box
		$op['select_location'] = $arry2->select($COUNTRY,$op['fabric']['location'],'PHP_location','select','');  	
			// creat currency combo box
		$op['select_currency'] = $arry2->select($CURRENCY,$op['fabric']['currency'],'PHP_currency','select','');  	
			// creat price_unit combo box
		$op['select_unit_price'] = $arry2->select($LOTS_PRICE_UNIT,$op['fabric']['unit_price'],'PHP_unit_price','select','');  	
			// creat supply term combo box
		$op['select_term'] = $arry2->select($TRADE_TERM,$op['fabric']['term'],'PHP_term','select','');  	
			// creat weight unit combo box
		$op['select_unit_wt'] = $arry2->select($FABRIC_WT_UNIT,$op['fabric']['unit_wt'],'PHP_unit_wt','select','');  	

			// creat width unit combo box
		$op['select_unit_width'] = $arry2->select($FABRIC_WIDTH_UNIT,$op['fabric']['width_unit'],'PHP_unit_width','select',''); 		
		
		$layout->assign($op);
		$layout->display($TPL_FABRIC_ADD);	
	break;
	}

	// ��J�����T��............after check input.........
	// �]�w ART_CODE ..........
	if ($PHP_kind == "��´��") { $kind = "W"; };
	if ($PHP_kind == "�w´��") { $kind = "N"; };
			
				// ���X�~�X...
				$dt = decode_date(1);
			$year_code = substr($dt['year'],-2);

			$dept_code = "K";      //.................................�ȮɥH K �����s�J .............
			$art_code = $dept->get_fabric_serious($dept_code, $year_code,$kind);  // �]�P�ɧ�sdept�ɤ���num��[csv]

				$parm['art_code'] = $art_code;
				$GLOBALS['PHP_art_code'] = $art_code; //���F�U����search �i�J search�ɪ��P�_��

		$op['fabric'] = $parm;

		$f1 = $fabric->add($parm);

		if (!$f1) {  // �����\��J��Ʈ�
			$op['msg'] = $fabric->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
			
			// �C�X�ӵ��O��
			$op['fabric'] = $fabric->get($f1);  //���X�ӵ��O��


			// �ˬd �ۤ��O�_�s�b
		if(file_exists($GLOBALS['config']['root_dir']."/fabric/".$op['fabric']['id'].".jpg")){
			$op['main_pic'] = "./fabric/".$op['fabric']['id'].".jpg";
		} else {
			$op['main_pic'] = "./images/graydot.gif";
		}

			
				# �O���ϥΪ̰ʺA
		$log->log_add(0,"91A","���\�s�W �}�o���ưO�� :".$op['fabric']['art_code']);


		$op['search'] = '';
		$op['msg'] = $fabric->msg->get(2);
		$allow = $admin->decode_perm(9,1);   // �]�w �s�W�R���v��
		if ($op['msg']){ $op['msg_YES'] = 1; } else { $op['msg_YES'] = "" ;}
		if ($allow['view'])		{	$op['view_flag'] = 1;} else {	$op['view_flag'] = "";}
		if ($allow['add'])		{	$op['add_flag'] = 1;} else {	$op['add_flag'] = "";}
		if ($allow['edit'])		{	$op['edit_flag'] = 1;} else {	$op['edit_flag'] = "";}
		if ($allow['del'])		{	$op['del_flag'] = 1;} else {	$op['del_flag'] = "";}


		$layout->assign($op);
		$layout->display($TPL_FABRIC_VIEW);		    	    
	break;

//========================================================================================	
	case "fabric_view":

		if(!$admin->is_power(9,1,"view")){ 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$op['fabric'] = $fabric->get($PHP_id);  //���X�ӵ��O��

		if (!$op['fabric']) {
			$op['msg'] = $fabric->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
// �[�J��^�e�� 2005/05/05
		$back_str = $cgiget."&PHP_sr_startno=".$cgino."&PHP_art_code=".$cgi_1."&PHP_cat=".$cgi_2."&PHP_name=".$cgi_3."&PHP_content=".$cgi_4."&PHP_supl_ref=".$cgi_5."&PHP_supl=".$cgi_6."&PHP_finish=".$cgi_7;

		$op['back_str'] = $back_str;
//------------------------------


				# �O���ϥΪ̰ʺA
//		$log->log_add(0,"91V","�s�� �}�o���Ƹ�ơG".$op['fabric']['art_code']);

			// �ˬd �ۤ��O�_�s�b
		if(file_exists($GLOBALS['config']['root_dir']."/fabric/".$op['fabric']['id'].".jpg")){
			$op['main_pic'] = "./fabric/".$op['fabric']['id'].".jpg";
		} else {
			$op['main_pic'] = "./images/graydot.gif";
		}

		$op['msg'] = $fabric->msg->get(2);
			$allow = $admin->decode_perm(9,1);   // �]�w �s�W�R���v��
		if ($op['msg']){ $op['msg_YES'] = 1; } else { $op['msg_YES'] = "" ;}
		if ($allow['view'])		{	$op['view_flag'] = 1;} else {	$op['view_flag'] = "";}
		if ($allow['add'])		{	$op['add_flag'] = 1;} else {	$op['add_flag'] = "";}
		if ($allow['edit'])		{	$op['edit_flag'] = 1;} else {	$op['edit_flag'] = "";}
		if ($allow['del'])		{	$op['del_flag'] = 1;} else {	$op['del_flag'] = "";}

		$op['search'] = '1';

		$layout->assign($op);
		$layout->display($TPL_FABRIC_VIEW);		    	    
	break;
//=======================================================
	case "fabric_edit":

		if(!$admin->is_power(9,1,"edit")){ 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$op['fabric'] = $fabric->get($PHP_id);
		if (!$op['fabric']) {
			$op['msg'] = $fabric->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

// �[�J��^�e�� 2005/05/05
		$back_str = $cgiget."&PHP_sr_startno=".$cgino."&PHP_art_code=".$cgi_1."&PHP_cat=".$cgi_2."&PHP_name=".$cgi_3."&PHP_content=".$cgi_4."&PHP_supl_ref=".$cgi_5."&PHP_supl=".$cgi_6."&PHP_finish=".$cgi_7;

		$op['back_str'] = $back_str;
//------------------------------
			// creat fabric cat combo box
		$op['select_cat'] = $arry2->select($FABRIC_CAT,$op['fabric']['cat'],'PHP_cat','select','');  	
//			// creat fabric kind combo box
//		$op['select_kind'] = $arry2->select($FABRIC_KIND,$op['fabric']['kind'],'PHP_kind','select','');  	
			// create combo box for suplier .....
			$where_str =" WHERE supl_cat ='�D��' ";
			$supl_def = $supl->get_fields('supl_s_name',$where_str);   // ���X ���������O�N��
		$op['select_supl'] = $arry2->select($supl_def,$op['fabric']['supl'],'PHP_supl','select','');  	
			// creat c/o combo box
		$op['select_co'] = $arry2->select($COUNTRY,$op['fabric']['co'],'PHP_co','select','');  	
			// creat location combo box
		$op['select_location'] = $arry2->select($COUNTRY,$op['fabric']['location'],'PHP_location','select','');  	
			// creat currency combo box
		$op['select_currency'] = $arry2->select($CURRENCY,$op['fabric']['currency'],'PHP_currency','select','');  	
			// creat price_unit combo box
		$op['select_unit_price'] = $arry2->select($LOTS_PRICE_UNIT,$op['fabric']['unit_price'],'PHP_unit_price','select','');  	
			// creat supply term combo box
		$op['select_term'] = $arry2->select($TRADE_TERM,$op['fabric']['term'],'PHP_term','select','');  	
			// creat weight unit combo box
		$op['select_unit_wt'] = $arry2->select($FABRIC_WT_UNIT,$op['fabric']['unit_wt'],'PHP_unit_wt','select','');  	
		
			// creat width unit combo box
		$op['select_unit_width'] = $arry2->select($FABRIC_WIDTH_UNIT,$op['fabric']['width_unit'],'PHP_unit_width','select',''); 

			// �ˬd �ۤ��O�_�s�b
		if(file_exists($GLOBALS['config']['root_dir']."/fabric/".$op['fabric']['id'].".jpg")){
			$op['main_pic'] = "./fabric/".$op['fabric']['id'].".jpg";
		} else {
			$op['main_pic'] = "./images/graydot.gif";
		}

			$op['msg'] = $fabric->msg->get(2);
			if ($op['msg']){ $op['msg_YES'] = 1; } else { $op['msg_YES'] = "" ;}

				# �O���ϥΪ̰ʺA
//		$log->log_add(0,"91E","�i�J��s�}�o���˰O�� :".$op['fabric']['art_code']);

		$op['search'] = '1';
		$layout->assign($op);
		$layout->display($TPL_FABRIC_EDIT);  
		break;

//=======================================================
	case "do_fabric_edit":

		if(!$admin->is_power(9,1,"edit")){ 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$argv = array(	"id"			=>	$PHP_id,
						"art_code"		=>	$PHP_art_code,
						"cat"			=>	$PHP_cat,
						"supl"			=>	$PHP_supl,
						"co"			=>	$PHP_co,
						"location"		=>	$PHP_location,
						"currency"		=>	$PHP_currency,
						"unit_price"	=>	$PHP_unit_price,
						"term"			=>	$PHP_term,
						"unit_wt"		=>	$PHP_unit_wt,

						"name"			=>	$PHP_name,
						"content"		=>	$PHP_content,
						"construct"		=>	$PHP_construct,
						"finish"		=>	$PHP_finish,
						"supl_ref"		=>	$PHP_supl_ref,
						"width"			=>	$PHP_width,
						"width_unit"	=>	$PHP_unit_width,
						"weight"		=>	$PHP_weight,
						"price"			=>	$PHP_price,
						"leadtime"		=>	$PHP_leadtime,

						"pic"			=>	$PHP_pic,
						"pic_upload"	=>	$PHP_pic_upload,

						"remark"		=>	$PHP_remark
			);


				$op['fabric'] = $argv;
			$check = $fabric->check($argv,1);
	// .....................................��J��� �����~��  �A�^��˥���J���
	if (!$check) {  
		$op['msg']= $fabric->msg->get(2);

		$allow = $admin->decode_perm(9,1);   // �]�w �s�W�R���v��
		if ($op['msg']){ $op['msg_YES'] = 1; } else { $op['msg_YES'] = "" ;}
		if ($allow['view'])		{	$op['view_flag'] = 1;} else {	$op['view_flag'] = "";}
		if ($allow['add'])		{	$op['add_flag'] = 1;} else {	$op['add_flag'] = "";}
		if ($allow['edit'])		{	$op['edit_flag'] = 1;} else {	$op['edit_flag'] = "";}
		if ($allow['del'])		{	$op['del_flag'] = 1;} else {	$op['del_flag'] = "";}


			// creat fabric cat combo box
		$op['select_cat'] = $arry2->select($FABRIC_CAT,$op['fabric']['cat'],'PHP_cat','select','');  	

			// create combo box for suplier .....
			$where_str =" WHERE supl_cat ='�D��' ";
			$supl_def = $supl->get_fields('supl_s_name',$where_str);   // ���X ���������O�N��
		$op['select_supl'] = $arry2->select($supl_def,$op['fabric']['supl'],'PHP_supl','select','');  	
			// creat c/o combo box
		$op['select_co'] = $arry2->select($COUNTRY,$op['fabric']['co'],'PHP_co','select','');  	
			// creat location combo box
		$op['select_location'] = $arry2->select($COUNTRY,$op['fabric']['location'],'PHP_location','select','');  	
			// creat currency combo box
		$op['select_currency'] = $arry2->select($CURRENCY,$op['fabric']['currency'],'PHP_currency','select','');  	
			// creat price_unit combo box
		$op['select_unit_price'] = $arry2->select($LOTS_PRICE_UNIT,$op['fabric']['unit_price'],'PHP_unit_price','select','');  	
			// creat supply term combo box
		$op['select_term'] = $arry2->select($TRADE_TERM,$op['fabric']['term'],'PHP_term','select','');  	
			// creat weight unit combo box
		$op['select_unit_wt'] = $arry2->select($FABRIC_WT_UNIT,$op['fabric']['unit_wt'],'PHP_unit_wt','select','');  	

			// creat width unit combo box
		$op['select_unit_width'] = $arry2->select($FABRIC_WIDTH_UNIT,$op['fabric']['width_unit'],'PHP_unit_width','select',''); 

			// �ˬd �ۤ��O�_�s�b
		if(file_exists($GLOBALS['config']['root_dir']."/fabric/".$op['fabric']['id'].".jpg")){
			$op['main_pic'] = "./fabric/".$op['fabric']['id'].".jpg";
		} else {
			$op['main_pic'] = "./images/graydot.gif";
		}

// �[�J��^�e�� 2005/05/05   ????????????????????? �٤��T�w�藍��
		$op['back_str'] = $PHP_back_str;
//------------------------------

		$layout->assign($op);
		$layout->display($TPL_FABRIC_EDIT);	
	break;
	}

	// ��J�����T��............after check input.........

		if (!$fabric->edit($argv)) {
			$op['msg'] = $fabric->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;	    
		}

					# �O���ϥΪ̰ʺA
		$message = "���\��s�}�o���Ƹ��:[$PHP_art_code]�����e�E";
		$log->log_add(0,"91E",$message);

		$op['fabric'] = $fabric->get($PHP_id);  //���X�ӵ��O��

		if (!$op['fabric']) {
			$op['msg'] = $fabric->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}


			// �ˬd �ۤ��O�_�s�b
		if(file_exists($GLOBALS['config']['root_dir']."/fabric/".$op['fabric']['id'].".jpg")){
			$op['main_pic'] = "./fabric/".$op['fabric']['id'].".jpg";
		} else {
			$op['main_pic'] = "./images/graydot.gif";
		}

		$op['msg'] = $fabric->msg->get(2);
		$op['msg'][] = $message;

// �[�J��^�e�� 2005/05/05

		$op['back_str'] = $PHP_back_str;
//------------------------------

			$allow = $admin->decode_perm(9,1);   // �]�w �s�W�R���v��
		if ($op['msg']){ $op['msg_YES'] = 1; } else { $op['msg_YES'] = "" ;}
		if ($allow['view'])		{	$op['view_flag'] = 1;} else {	$op['view_flag'] = "";}
		if ($allow['add'])		{	$op['add_flag'] = 1;} else {	$op['add_flag'] = "";}
		if ($allow['edit'])		{	$op['edit_flag'] = 1;} else {	$op['edit_flag'] = "";}
		if ($allow['del'])		{	$op['del_flag'] = 1;} else {	$op['del_flag'] = "";}

		$op['search'] = '1';
		$layout->assign($op);
		$layout->display($TPL_FABRIC_VIEW);		    	    
	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "fabric_del":

			// ���ݭn manager login �~��u�����R�� SUPLIER
		if(!$admin->is_power(9,1,"del") && !($GLOBALS['SCACHE']['ADMIN']['id'] == "SA" )) { 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$op['fabric'] = $fabric->get($PHP_id);
		
		if (!$fabric->del($PHP_id)) {
			$op['msg'] = $fabric->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;	    
		}
					# �O���ϥΪ̰ʺA

		$message = "�R�� �}�o�����O��:[".$op['fabric']['art_code']."] �C";
		$log->log_add(0,"91D",$message);

			// creat cust combo box
		$op['select_cat'] = $arry2->select($FABRIC_CAT,'','PHP_cat','select','');  	
			// create combo box for vendor fields.....
			$where_str =" WHERE supl_cat ='�D��' ";
			$supl_def = $supl->get_fields('supl_s_name',$where_str);   // ���X ���������O�N��
		$op['select_supl'] = $arry2->select($supl_def,'','PHP_supl','select','');  	

		$op['msg']= $fabric->msg->get(2);
		$op['msg'][] = $message;

		$allow = $admin->decode_perm(9,1);   // �]�w �s�W�R���v��
		if ($op['msg']){ $op['msg_YES'] = 1; } else { $op['msg_YES'] = "" ;}
		if ($allow['view'])		{	$op['view_flag'] = 1;} else {	$op['view_flag'] = "";}
		if ($allow['add'])		{	$op['add_flag'] = 1;} else {	$op['add_flag'] = "";}
		if ($allow['edit'])		{	$op['edit_flag'] = 1;} else {	$op['edit_flag'] = "";}
		if ($allow['del'])		{	$op['del_flag'] = 1;} else {	$op['del_flag'] = "";}
		unset($PHP_id);
		$layout->assign($op);
		$layout->display($TPL_FABRIC_SEARCH);		    	    
	break;













#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//   supl_type ����
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
/*
	  case "supl_type":

		if(!$admin->is_power(7,3,"view")){ 
//		if (!$admin->is_perm(7,3)) {		
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		if (!$op = $supl_type->search(0)) {
			$op['msg']= $supl_type->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$op['msg'] = $supl_type->msg->get(2);
//		if (!$op['msg']){
//			$op['msg'][] = "�C�X �����ӧO��ưO��";
//		}
//exit;

		$allow = $admin->decode_perm(7,3);   // �]�w �s�W�R���v��
		if ($op['msg']){ $op['msg_YES'] = 1; } else { $op['msg_YES'] = "" ;}
		if ($allow['view'])		{	$op['view_flag'] = 1;} else {	$op['view_flag'] = "";}
		if ($allow['add'])		{	$op['add_flag'] = 1;} else {	$op['add_flag'] = "";}
		if ($allow['edit'])		{	$op['edit_flag'] = 1;} else {	$op['edit_flag'] = "";}
		if ($allow['del'])		{	$op['del_flag'] = 1;} else {	$op['del_flag'] = "";}

		$layout->assign($op);
		$layout->display($TPL_SUPL_TYPE);		    	    
	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_supl_type_add":

//			$op = array();
//		$allow = $admin->decode_perm(7,3);   // �]�w �s�W�R���v��

		if(!$admin->is_power(7,3,"add")){ 
//		if (!$admin->is_perm(7,3) && !$allow['add']) {
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$parm = array(	"supl_type"	=>	$PHP_supl_type,
						"des"		=>	$PHP_des
				);

		$f1 = $supl_type->add($parm);
		if ($f1) {
				# �O���ϥΪ̰ʺA
//			$log->log_add(0,"73A","�s�W ���������O: [".$PHP_supl_type."]" );
			unset($parm);  // �R�� �Ѽ�
		}	else {
//		    $op['msg'] = $supl_type->msg->get(2);
			$op['supl_type'] = $parm;
		}

		if (!$op = $supl_type->search(0)) {
			$op = array();
			$op['msg']= $supl_type->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

			$op['msg'] = $supl_type->msg->get(2);
			$allow = $admin->decode_perm(7,3);   // �]�w �s�W�R���v��
			if ($op['msg']){ $op['msg_YES'] = 1; } else { $op['msg_YES'] = "" ;}
			if ($allow['view'])		{	$op['view_flag'] = 1;} else {	$op['view_flag'] = "";}
			if ($allow['add'])		{	$op['add_flag'] = 1;} else {	$op['add_flag'] = "";}
			if ($allow['edit'])		{	$op['edit_flag'] = 1;} else {	$op['edit_flag'] = "";}
			if ($allow['del'])		{	$op['del_flag'] = 1;} else {	$op['del_flag'] = "";}

		$layout->assign($op);
		$layout->display($TPL_SUPL_TYPE);	    	    
	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "supl_type_update":

		if(!$admin->is_power(7,3,"edit")){ 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$op['supl_type'] = $supl_type->get($PHP_id);
		$op['msg'][] = "��s���������O��ưO�� :".$op['supl_type']['supl_type'];
		// �ǤJ�������Ѽ�
//		$op['sr_startno'] = $PHP_sr_startno;
		$msg_YES = 1;
		$layout->assign($op);
		$layout->display($TPL_SUPL_TYPE_UPDATE);  
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_supl_type_updat":

		if(!$admin->is_power(7,3,"edit")){ 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$argv = array(	"id"		=> $PHP_id,
						"supl_type"	=> $PHP_supl_type,
						"des"		=> $PHP_des
					);
		if (!$supl_type->update($argv)) {
			$op['msg'] = $supl_type->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;	    
		}
					# �O���ϥΪ̰ʺA
//		$log->log_add(0,"73E","��s���������O�O���G".$PHP_supl_type);

		if (!$op = $supl_type->search(0)) {
			$op = array();
			$op['msg']= $supl_type->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

//		$op['msg'] = $supl_type->msg->get(2);

		$op['msg'][] = "���\��s[ $PHP_supl_type ] ���������O��ưO���C";

			if ($op['msg']){ $op['msg_YES'] = 1; } else { $op['msg_YES'] = "" ;}
			$allow = $admin->decode_perm(7,3);   // �]�w �s�W�R���v��
			if ($allow['view'])	{	$op['view_flag'] = 1;} else {	$op['view_flag'] = "";}
			if ($allow['add'])	{	$op['add_flag'] = 1;} else {	$op['add_flag'] = "";}
			if ($allow['edit'])	{	$op['edit_flag'] = 1;} else {	$op['edit_flag'] = "";}
			if ($allow['del'])	{	$op['del_flag'] = 1;} else {	$op['del_flag'] = "";}

		$layout->assign($op);
		$layout->display($TPL_SUPL_TYPE);		    	    
		
	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "supl_type_del":

//		$allow = $admin->decode_perm(7,3);   // �]�w �s�W�R���v��

		if(!$admin->is_power(7,3,"del")){ 
//		if (!$admin->is_perm(7,3) && !$allow['del']) {
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$op['supl_type'] = $supl_type->get($PHP_id);
		
		if (!$supl_type->del($PHP_id)) {
			$op['msg'] = $supl_type->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;	    
		}
					# �O���ϥΪ̰ʺA
//		$log->log_add(0,"73D","�R�����������O :[".$op['supl_type']['supl_type']."]");

		$message = "���\ �R�����������O :[".$op['supl_type']['supl_type']."]�C";

		if (!$op = $supl_type->search(0)) {
			$op = array();
			$op['msg']= $supl_type->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$op['msg'][] = $message;

			if ($op['msg']){ $op['msg_YES'] = 1; } else { $op['msg_YES'] = "" ;}
			$allow = $admin->decode_perm($perm[7][3]);   // �]�w �s�W�R���v��
			if ($allow['view'])	{	$op['view_flag'] = 1;} else {	$op['view_flag'] = "";}
			if ($allow['add'])	{	$op['add_flag'] = 1;} else {	$op['add_flag'] = "";}
			if ($allow['edit'])	{	$op['edit_flag'] = 1;} else {	$op['edit_flag'] = "";}
			if ($allow['del'])	{	$op['del_flag'] = 1;} else {	$op['del_flag'] = "";}

		$layout->assign($op);
		$layout->display($TPL_SUPL_TYPE);		    	    
	break;



*/
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//   dept ����
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	  case "dept":
		check_authority(7,6,"view");
		
		if (!$op = $dept->search(0)) {
			$op['msg']= $dept->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$op['msg'] = $dept->msg->get(2);
 
		page_display($op, 7, 6, $TPL_DEPT);   	    
	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_dept_add":

		check_authority(7,6,"add");
		$parm = array(	"dept_code"			=>	$PHP_dept_code,
										"dept_name"			=>	$PHP_dept_name,
										"chief"					=>	$PHP_chief
					);

		$f1 = $dept->add($parm);
		if ($f1) {
			unset($parm);  // �R�� �Ѽ�
		}	else {
			$op['dept'] = $parm;
		}

		if (!$op = $dept->search(0)) {
			$op = array();
			$op['msg']= $dept->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

			$op['msg'] = $dept->msg->get(2);
		    	    
		page_display($op, 7, 6, $TPL_DEPT);   	    

	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "dept_update":

		check_authority(7,6,"edit");
		$op['dept'] = $dept->get($PHP_id);
		$op['msg'][] = "��s������ưO�� :".$op['dept']['dept_code'];
		page_display($op, 7, 6, $TPL_DEPT_UPDATE);   	    

		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_dept_updat":

		check_authority(7,6,"edit");

		$argv = array(	"id"		=> $PHP_id,
						"dept_code"	=> $PHP_dept_code,
						"dept_name"	=> $PHP_dept_name,
						"chief"		=> $PHP_chief
					);
		if (!$dept->update($argv)) {
			$op['msg'] = $dept->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;	    
		}

		if (!$op = $dept->search(0)) {
			$op = array();
			$op['msg']= $dept->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}


		$op['msg'][] = "���\��s[ $PHP_dept_name ]�����O��ưO���C";

		page_display($op, 7, 6, $TPL_DEPT);   	    
		
	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "dept_del":

		if(!$admin->is_power(7,6,"del")){ 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$op['dept'] = $dept->get($PHP_id);
		
		if (!$dept->del($PHP_id)) {
			$op['msg'] = $dept->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;	    
		}
		$message = "�R�������O :[".$op['dept']['dept_name']."]�C";

		if (!$op = $dept->search(0)) {
			$op = array();
			$op['msg']= $dept->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$op['msg'][] = $message;

			if ($op['msg']){ $op['msg_YES'] = 1; } else { $op['msg_YES'] = "" ;}
			$allow = $admin->decode_perm(7,6);   // �]�w �s�W�R���v��
			if ($allow['view'])	{	$op['view_flag'] = 1;} else {	$op['view_flag'] = "";}
			if ($allow['add'])	{	$op['add_flag'] = 1;} else {	$op['add_flag'] = "";}
			if ($allow['edit'])	{	$op['edit_flag'] = 1;} else {	$op['edit_flag'] = "";}
			if ($allow['del'])	{	$op['del_flag'] = 1;} else {	$op['del_flag'] = "";}

		$layout->assign($op);
		$layout->display($TPL_DEPT);		    	    
	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//   smpl_type ����
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	  case "smpl_type":

		if(!$admin->is_power(7,1,"view")){ 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

if ($PHP_sr_startno){
	$GLOBALS['PHP_sr_startno'] = $PHP_sr_startno;
}

		if (!$op = $smpl_type->search(0)) {
			$op['msg']= $smpl_type->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$op['msg'] = $smpl_type->msg->get(2);

		if ($op['msg']){ $op['msg_YES'] = 1; } else { $op['msg_YES'] = "" ;}
		$allow = $admin->decode_perm(7,1);   // �]�w �s�W�R���v��
		if ($allow['view'])		{	$op['view_flag'] = 1;} else {	$op['view_flag'] = "";}
		if ($allow['add'])		{	$op['add_flag'] = 1;} else {	$op['add_flag'] = "";}
		if ($allow['edit'])		{	$op['edit_flag'] = 1;} else {	$op['edit_flag'] = "";}
		if ($allow['del'])		{	$op['del_flag'] = 1;} else {	$op['del_flag'] = "";}

		$layout->assign($op);
		$layout->display($TPL_SMPL_TYPE);		    	    
	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_smpl_type_add":

		if(!$admin->is_power(7,1,"add")){ 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$parm = array(	"smpl_type"		=>	$PHP_smpl_type,
						"des"			=>	$PHP_des
					);

		$f1 = $smpl_type->add($parm);
		if ($f1) {
			unset($parm);  // �R�� �Ѽ�
		}	else {
			$op['smpl_type'] = $parm;
		}

		if (!$op = $smpl_type->search(0)) {
			$op = array();
			$op['msg']= $smpl_type->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$op['msg'] = $smpl_type->msg->get(2);

			if ($op['msg']){ $op['msg_YES'] = 1; } else { $op['msg_YES'] = "" ;}
			$allow = $admin->decode_perm(7,1);   // �]�w �s�W�R���v��
			if ($allow['view'])		{	$op['view_flag'] = 1;} else {	$op['view_flag'] = "";}
			if ($allow['add'])		{	$op['add_flag'] = 1;} else {	$op['add_flag'] = "";}
			if ($allow['edit'])		{	$op['edit_flag'] = 1;} else {	$op['edit_flag'] = "";}
			if ($allow['del'])		{	$op['del_flag'] = 1;} else {	$op['del_flag'] = "";}

		$layout->assign($op);
		$layout->display($TPL_SMPL_TYPE);		    	    
	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "smpl_type_update":

		if(!$admin->is_power(7,1,"edit")){ 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$op['smpl_type'] = $smpl_type->get($PHP_id);
		$layout->assign($op);
		$layout->display($TPL_SMPL_TYPE_UPDATE);  
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_smpl_type_updat":


		if(!$admin->is_power(7,1,"edit")){ 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$argv = array(	"id"			=> $PHP_id,
						"smpl_type"		=> $PHP_smpl_type,
						"des"			=> $PHP_des
					);
		if (!$smpl_type->update($argv)) {
			$op['msg'] = $smpl_type->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;	    
		}

		if (!$op = $smpl_type->search(0)) {
			$op = array();
			$op['msg']= $smpl_type->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$op['msg'][] = "���\��s[ $PHP_smpl_type]�˫~�O��ưO���C";

			if ($op['msg']){ $op['msg_YES'] = 1; } else { $op['msg_YES'] = "" ;}
			$allow = $admin->decode_perm(7,1);   // �]�w �s�W�R���v��
			if ($allow['view'])	{	$op['view_flag'] = 1;} else {	$op['view_flag'] = "";}
			if ($allow['add'])	{	$op['add_flag'] = 1;} else {	$op['add_flag'] = "";}
			if ($allow['edit'])	{	$op['edit_flag'] = 1;} else {	$op['edit_flag'] = "";}
			if ($allow['del'])	{	$op['del_flag'] = 1;} else {	$op['del_flag'] = "";}

		$layout->assign($op);
		$layout->display($TPL_SMPL_TYPE);		    	    
		
	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "smpl_type_del":

		if(!$admin->is_power(7,1,"add")){ 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$op['smpl_type'] = $smpl_type->get($PHP_id);
		
		if (!$smpl_type->del($PHP_id)) {
			$op['msg'] = $smpl_type->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;	    
		}

		$message = "���\�R���˫~�O :[".$op['smpl_type']['smpl_type']."]�C";

		if (!$op = $smpl_type->search(0)) {
			$op = array();
			$op['msg']= $smpl_type->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$op['msg'][] = $message;

			if ($op['msg']){ $op['msg_YES'] = 1; } else { $op['msg_YES'] = "" ;}
			$allow = $admin->decode_perm(7,1);   // �]�w �s�W�R���v��
			if ($allow['view'])	{	$op['view_flag'] = 1;} else {	$op['view_flag'] = "";}
			if ($allow['add'])	{	$op['add_flag'] = 1;} else {	$op['add_flag'] = "";}
			if ($allow['edit'])	{	$op['edit_flag'] = 1;} else {	$op['edit_flag'] = "";}
			if ($allow['del'])	{	$op['del_flag'] = 1;} else {	$op['del_flag'] = "";}

		$layout->assign($op);
		$layout->display($TPL_SMPL_TYPE);		    	    
	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//   style_type ����
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	  case "style_type":

		if(!$admin->is_power(7,2,"view")){ 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		if (!$op = $style_type->search(0)) {
			$op['msg']= $style_type->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$op['msg'] = $style_type->msg->get(2);

		if ($op['msg']){ $op['msg_YES'] = 1; } else { $op['msg_YES'] = "" ;}
		$allow = $admin->decode_perm(7,2);   // �]�w �s�W�R���v��
		if ($allow['view'])		{	$op['view_flag'] = 1;} else {	$op['view_flag'] = "";}
		if ($allow['add'])		{	$op['add_flag'] = 1;} else {	$op['add_flag'] = "";}
		if ($allow['edit'])		{	$op['edit_flag'] = 1;} else {	$op['edit_flag'] = "";}
		if ($allow['del'])		{	$op['del_flag'] = 1;} else {	$op['del_flag'] = "";}

		$layout->assign($op);
		$layout->display($TPL_STYLE_TYPE);		    	    
	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_style_type_add":


		if(!$admin->is_power(7,2,"add")){ 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$parm = array(	"style_type"	=>	$PHP_style_type,
						"des"			=>	$PHP_des
				);

		$f1 = $style_type->add($parm);
		if ($f1) {
			unset($parm);  // �R�� �Ѽ�
		}	else {
			$op['style_type'] = $parm;
		}

		if (!$op = $style_type->search(0)) {
			$op = array();
			$op['msg']= $style_type->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

			$op['msg'] = $style_type->msg->get(2);
			if ($op['msg']){ $op['msg_YES'] = 1; } else { $op['msg_YES'] = "" ;}
			$allow = $admin->decode_perm(7,2);   // �]�w �s�W�R���v��
			if ($allow['view'])		{	$op['view_flag'] = 1;} else {	$op['view_flag'] = "";}
			if ($allow['add'])		{	$op['add_flag'] = 1;} else {	$op['add_flag'] = "";}
			if ($allow['edit'])		{	$op['edit_flag'] = 1;} else {	$op['edit_flag'] = "";}
			if ($allow['del'])		{	$op['del_flag'] = 1;} else {	$op['del_flag'] = "";}

		$layout->assign($op);
		$layout->display($TPL_STYLE_TYPE);		    	    
	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "style_type_update":


		if(!$admin->is_power(7,2,"edit")){ 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$op['style_type'] = $style_type->get($PHP_id);
		$op['msg'][] = "��s�ڦ����O��ưO�� :".$op['style_type']['style_type'];
		$msg_YES = 1;
		$layout->assign($op);
		$layout->display($TPL_STYLE_TYPE_UPDATE);  
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_style_type_updat":

		if(!$admin->is_power(7,2,"edit")){ 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$argv = array(	"id"			=> $PHP_id,
						"style_type"	=> $PHP_style_type,
						"des"			=> $PHP_des
					);
		if (!$style_type->update($argv)) {
			$op['msg'] = $style_type->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;	    
		}

		if (!$op = $style_type->search(0)) {
			$op = array();
			$op['msg']= $style_type->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$op['msg'][] = "���\��s[ $PHP_style_type ] �ڦ����O��ưO���C";

			if ($op['msg']){ $op['msg_YES'] = 1; } else { $op['msg_YES'] = "" ;}
			$allow = $admin->decode_perm(7,2);   // �]�w �s�W�R���v��
			if ($allow['view'])	{	$op['view_flag'] = 1;} else {	$op['view_flag'] = "";}
			if ($allow['add'])	{	$op['add_flag'] = 1;} else {	$op['add_flag'] = "";}
			if ($allow['edit'])	{	$op['edit_flag'] = 1;} else {	$op['edit_flag'] = "";}
			if ($allow['del'])	{	$op['del_flag'] = 1;} else {	$op['del_flag'] = "";}

		$layout->assign($op);
		$layout->display($TPL_STYLE_TYPE);		    	    
		
	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "style_type_del":

		if(!$admin->is_power(7,2,"del")){ 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$op['style_type'] = $style_type->get($PHP_id);
		
		if (!$style_type->del($PHP_id)) {
			$op['msg'] = $style_type->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;	    
		}

		$message = "���\ �R���ڦ����O :[".$op['style_type']['style_type']."]�C";

		if (!$op = $style_type->search(0)) {
			$op = array();
			$op['msg']= $style_type->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$op['msg'][] = $message;

			if ($op['msg']){ $op['msg_YES'] = 1; } else { $op['msg_YES'] = "" ;}
			$allow = $admin->decode_perm(7,2);   // �]�w �s�W�R���v��
			if ($allow['view'])	{	$op['view_flag'] = 1;} else {	$op['view_flag'] = "";}
			if ($allow['add'])	{	$op['add_flag'] = 1;} else {	$op['add_flag'] = "";}
			if ($allow['edit'])	{	$op['edit_flag'] = 1;} else {	$op['edit_flag'] = "";}
			if ($allow['del'])	{	$op['del_flag'] = 1;} else {	$op['del_flag'] = "";}

		$layout->assign($op);
		$layout->display($TPL_STYLE_TYPE);		    	    
	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//   season ����
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	  case "season":

		if(!$admin->is_power(7,4,"view")){ 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		if (!$op = $season->search(0)) {
			$op['msg']= $season->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$op['msg'] = $season->msg->get(2);

		if ($op['msg']){ $op['msg_YES'] = 1; } else { $op['msg_YES'] = "" ;}
		$allow = $admin->decode_perm(7,4);   // �]�w �s�W�R���v��
		if ($allow['view'])		{	$op['view_flag'] = 1;} else {	$op['view_flag'] = "";}
		if ($allow['add'])		{	$op['add_flag'] = 1;} else {	$op['add_flag'] = "";}
		if ($allow['edit'])		{	$op['edit_flag'] = 1;} else {	$op['edit_flag'] = "";}
		if ($allow['del'])		{	$op['del_flag'] = 1;} else {	$op['del_flag'] = "";}

		$layout->assign($op);
		$layout->display($TPL_SEASON);		    	    
	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_season_add":

		if(!$admin->is_power(7,4,"add")){ 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$parm = array(	"season"	=>	$PHP_season,
						"des"		=>	$PHP_des
				);

		$f1 = $season->add($parm);
		if ($f1) {
			unset($parm);  // �R�� �Ѽ�
		}	else {
			$op['season'] = $parm;
		}

		if (!$op = $season->search(0)) {
			$op = array();
			$op['msg']= $season->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

			$op['msg'] = $season->msg->get(2);
			$allow = $admin->decode_perm(7,4);   // �]�w �s�W�R���v��
			if ($op['msg']){ $op['msg_YES'] = 1; } else { $op['msg_YES'] = "" ;}
			if ($allow['view'])		{	$op['view_flag'] = 1;} else {	$op['view_flag'] = "";}
			if ($allow['add'])		{	$op['add_flag'] = 1;} else {	$op['add_flag'] = "";}
			if ($allow['edit'])		{	$op['edit_flag'] = 1;} else {	$op['edit_flag'] = "";}
			if ($allow['del'])		{	$op['del_flag'] = 1;} else {	$op['del_flag'] = "";}

		$layout->assign($op);
		$layout->display($TPL_SEASON);		    	    
	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "season_update":

		if(!$admin->is_power(7,4,"edit")){ 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$op['season'] = $season->get($PHP_id);
		$op['msg'][] = "��s�u�`���O��ưO�� :".$op['season']['season'];
		$msg_YES = 1;
		$layout->assign($op);
		$layout->display($TPL_SEASON_UPDATE);  
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_season_updat":

		if(!$admin->is_power(7,4,"edit")){ 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$argv = array(	"id"		=> $PHP_id,
						"season"	=> $PHP_season,
						"des"		=> $PHP_des
					);
		if (!$season->update($argv)) {
			$op['msg'] = $season->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;	    
		}
		if (!$op = $season->search(0)) {
			$op = array();
			$op['msg']= $season->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}


		$op['msg'][] = "���\��s[ $PHP_season ] �u�`�O��ưO���C";

			if ($op['msg']){ $op['msg_YES'] = 1; } else { $op['msg_YES'] = "" ;}
			$allow = $admin->decode_perm(7,4);   // �]�w �s�W�R���v��
			if ($allow['view'])	{	$op['view_flag'] = 1;} else {	$op['view_flag'] = "";}
			if ($allow['add'])	{	$op['add_flag'] = 1;} else {	$op['add_flag'] = "";}
			if ($allow['edit'])	{	$op['edit_flag'] = 1;} else {	$op['edit_flag'] = "";}
			if ($allow['del'])	{	$op['del_flag'] = 1;} else {	$op['del_flag'] = "";}

		$layout->assign($op);
		$layout->display($TPL_SEASON);		    	    
		
	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "season_del":

		if(!$admin->is_power(7,4,"del")){ 
			$op['msg'][] = "sorry! �z�S���o���v��!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$op['season'] = $season->get($PHP_id);
		
		if (!$season->del($PHP_id)) {
			$op['msg'] = $season->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;	    
		}

		$message = "���\ �R���u�`���O :[".$op['season']['season']."]�C";

		if (!$op = $season->search(0)) {
			$op = array();
			$op['msg']= $season->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$op['msg'][] = $message;

			if ($op['msg']){ $op['msg_YES'] = 1; } else { $op['msg_YES'] = "" ;}
			$allow = $admin->decode_perm(7,4);   // �]�w �s�W�R���v��
			if ($allow['view'])	{	$op['view_flag'] = 1;} else {	$op['view_flag'] = "";}
			if ($allow['add'])	{	$op['add_flag'] = 1;} else {	$op['add_flag'] = "";}
			if ($allow['edit'])	{	$op['edit_flag'] = 1;} else {	$op['edit_flag'] = "";}
			if ($allow['del'])	{	$op['del_flag'] = 1;} else {	$op['del_flag'] = "";}

		$layout->assign($op);
		$layout->display($TPL_SEASON);		    	    
	break;



 
 

 
 
 
 
 
 
 
 






#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		system part
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "login":
		if (!is_admin_login()) {
			$layout->display($TPL_LOGIN);		    
		}	else {
			$op['auth'] = $GLOBALS['SCACHE']['ADMIN'];
			$layout->assign($op);
			$layout->display($TPL_ADMIN_MAIN);		    	    
		}
		break;
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//========================== same as "do_login" bellow....=====================
	default;
	case "exe_login":


	if(($PHP_login_id=="carnival") && ($PHP_login_pass=="carnival")){
		$GLOBALS['SCACHE']['ADMIN']['name'] = "unknow";
			$op['msg'][] = "�o�ӱb���w���A�}��ϥ� !";
			$op['msg'][] = "�Шϥαz���^��W�٬�login ID �z���u�����K�X[password]�i�J !";
			$op['msg'][] = "�G�p�G�ڪ��]�w���� �άOID ���z�]�� �лP���p�� !";
			$op['msg'][] = "<a href='MAILTO:JACK@TP.CARNIVAL.COM.TW'>JACK@TP.CARNIVAL.COM.TW</A> �� �x�_���q����: 317 !";
			$op['msg'][] = "���±z !";
				unset($PHP_login_id);
				unset($PHP_login_pass);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
	}

		$f1 = $admin->login($PHP_login_id, $PHP_login_pass);
		if ($f1) {

			# �O���ϥΪ̰ʺA
			$log->log_add($GLOBALS['SCACHE']['ADMIN']['name'],"login","login�C");

			# �w�q�@�ǭӤH�Ѽ�
			$GLOBALS['MY_DEPT'] = $GLOBALS['SCACHE']['ADMIN']['dept'];
			$GLOBALS['MY_TEAM'] = $GLOBALS['SCACHE']['ADMIN']['team_id'];
			$GLOBALS['ME'] = $GLOBALS['SCACHE']['ADMIN']['name'];
			echo "<script></script>";

			$layout->assign($op);
			$layout->display($TPL_MAIN);
			
		}	else {
			# �O���ϥΪ̰ʺA
			$log->log_add(0,"login","cannot login�C($PHP_login_id : $PHP_login_pass)");

			$op['msg'] = $admin->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_LOGIN);		    
		}
		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "main":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "main":
  include_once($config['root_dir']."/lib/class.notify.php");
  $notify = new NOTIFY();
  if (!$notify->init($mysql,"log")) { print "error!! cannot initialize database for LOG class"; exit; }
	$note=$notify->search($GLOBALS['SCACHE']['ADMIN']['login_id'], "`read`=0");
	$op['max_notify'] = $note['max_no'];
	
	if ($GLOBALS['SCACHE']['ADMIN']['dept'] == 'PM' || $GLOBALS['SCACHE']['ADMIN']['dept'] == 'GM')
	{
		$op['order_rec']=$admin->workmsg('order_rec');
		$op['APV_ord']=$admin->workmsg('APV_ord');
		$op['CFM_ord']=$admin->workmsg('CFM_ord');
		$op['schedule']=$admin->workmsg('schedule');
		$op['CFM_SCHDL']=$admin->workmsg('CFM_SCHDL');
		$op['mater']=$admin->workmsg('mater');
		$op['mat_rcvd']=$admin->workmsg('mat_rcvd');
//		$op['ie_rec']=$admin->workmsg('ie_rec');
		$op['ex_etd']=$admin->workmsg('ex_etd');
		$op['schdl_out']=$admin->workmsg('schdl_out');
		$op['ord_pttn']=$admin->workmsg('ord_pttn');
//2007-08 ��oalert�s�W
		$op['smpl_apv']=$admin->workmsg('smpl_apv');
	}elseif($GLOBALS['SCACHE']['ADMIN']['dept'] == 'RD'){
		$op['order_rec']=$admin->workmsg('order_rec');
		$op['smpl_apv']=$admin->workmsg('smpl_apv');
		$op['ord_pttn']=$admin->workmsg('ord_pttn');
//		$op['ie_rec']=$admin->workmsg('ie_rec');
	//	$op['ex_etd']=$admin->workmsg('ex_etd');
	}elseif(substr($GLOBALS['SCACHE']['ADMIN']['dept'],0,1) == 'J' || substr($GLOBALS['SCACHE']['ADMIN']['dept'],0,1) == 'K' || substr($GLOBALS['SCACHE']['ADMIN']['dept'],0,1) == 'T'){
		$op['order_rec']=$admin->workmsg('order_rec');		
		if($admin->is_power(10,2,'add')) $op['CFM_ord']=$admin->workmsg('CFM_ord');
		$op['schedule']=$admin->workmsg('schedule');		
		$op['mater']=$admin->workmsg('mater');
		$op['mat_rcvd']=$admin->workmsg('mat_rcvd');
//		$op['ie_rec']=$admin->workmsg('ie_rec');
		$op['ex_etd']=$admin->workmsg('ex_etd');
		$op['schdl_out']=$admin->workmsg('schdl_out');
		$op['smpl_apv']=$admin->workmsg('smpl_apv');
		$op['ord_pttn']=$admin->workmsg('ord_pttn');
	}elseif($GLOBALS['SCACHE']['ADMIN']['dept'] == 'HJ' || $GLOBALS['SCACHE']['ADMIN']['dept'] == 'LY'|| $GLOBALS['SCACHE']['ADMIN']['dept'] == 'V1'){
		if($admin->is_power(10,1,'add')) $op['order_rec']=$admin->workmsg('order_rec');		
		if($admin->is_power(10,2,'add')) $op['CFM_ord']=$admin->workmsg('CFM_ord');
		$op['schedule']=$admin->workmsg('schedule');
		if($admin->is_power(4,3,'add')) $op['CFM_SCHDL']=$admin->workmsg('CFM_SCHDL');
		$op['mater']=$admin->workmsg('mater');
		$op['mat_rcvd']=$admin->workmsg('mat_rcvd');
//		$op['ie_rec']=$admin->workmsg('ie_rec');
		$op['ex_etd']=$admin->workmsg('ex_etd');
		$op['schdl_out']=$admin->workmsg('schdl_out');
		$op['smpl_apv']=$admin->workmsg('smpl_apv');
		$op['ord_pttn']=$admin->workmsg('ord_pttn');
	}else{
		if($admin->is_power(10,1,'add')) $op['order_rec']=$admin->workmsg('order_rec');		
		if($admin->is_power(10,2,'add')) $op['CFM_ord']=$admin->workmsg('CFM_ord');
		if($admin->is_power(10,3,'add')) $op['APV_ord']=$admin->workmsg('APV_ord');
		if($admin->is_power(4,2,'add')) $op['schedule']=$admin->workmsg('schedule');
		if($admin->is_power(4,3,'add')) $op['CFM_SCHDL']=$admin->workmsg('CFM_SCHDL');
		if($admin->is_power(10,4,'add') || $admin->is_power(10,4,'edit'))$op['mater']=$admin->workmsg('mater');
		if($admin->is_power(10,4,'add') || $admin->is_power(10,4,'edit')) $op['mat_rcvd']=$admin->workmsg('mat_rcvd');
//		if($admin->is_power(4,1,'add')) $op['ie_rec']=$admin->workmsg('ie_rec');
		if($admin->is_power(10,1,'add')) $op['ex_etd']=$admin->workmsg('ex_etd');
		if($admin->is_power(4,2,'add')) $op['schdl_out']=$admin->workmsg('schdl_out');
		if($admin->is_power(2,1,'add'))$op['smpl_apv']=$admin->workmsg('smpl_apv');
		if($admin->is_power(4,1,'add')) $op['ord_pttn']=$admin->workmsg('ord_pttn');
	}
	

	$op['today']=date('Y-m-d');
	$op['day30']=increceDaysInDate($TODAY,30);
	$op['cgi_get']="index2.php?PHP_action=main";
	$layout->assign($op);
	$layout->display($TPL_NOTICE);		    	    
	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_login":
		$f1 = $admin->login($PHP_login_id, $PHP_login_pass);
		if ($f1) {
					# �O���ϥΪ̰ʺA
			$log->log_add($GLOBALS['SCACHE']['ADMIN']['name'],"login","login successfully�C");
			$op['auth'] = $GLOBALS['SCACHE']['ADMIN'];

			$layout->assign($op);
			$layout->display($TPL_MAIN);		    	    
		}	else {
					# �O���ϥΪ̰ʺA
			$log->log_add(0,"login","fail to login�C($PHP_login_id : $PHP_login_pass)");

			$op['msg'] = $admin->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_LOGIN_FORM);		    
		}
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "changePass":

			$layout->assign($op);
			$layout->display($TPL_CHANGE_FORM);		    

	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_change_pass":

		$parm = array(	"id"			=>	$PHP_id,
						"pass"			=>	$PHP_pass,
						"new1"			=>	$PHP_new1,
						"new2"			=>	$PHP_new2,
			);
	
		$redir_str = $PHP_SELF."?PHP_action=logout";
		
		if(!$admin->change_pass($parm)) { 
			$op['msg'] = $admin->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
				# �O���ϥΪ̰ʺA
		$message = "change password for user ID: [ ".$PHP_id." ] ";

			redirect_page($redir_str);

		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "logout":
				# �O���ϥΪ̰ʺA
		$log->log_add(0,"logout","log out");
		unset($GLOBALS['SCACHE']['ADMIN']);
		$layout->display($TPL_LOGIN);		    
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "change_passwd":
		if (!$admin->is_perm("enable")) {
			$op['msg'][] = "sorry! you don't have this permission!!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
				# �O���ϥΪ̰ʺA
		$log->log_add(0,"password","enter to change password");
		$layout->display($TPL_ADMIN_CHANGE_PASSWD);
		break;
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "change_passwd_step2":
		if (!$admin->is_perm("enable")) {
			$op['msg'][] = "sorry! you don't have this permission!!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$admin_data = $GLOBALS[SCACHE][ADMIN];
		$old_pass = $admin_data[login_pass];
		if ($old_pass != $PHP_login_pass_old) {
			$op['msg'][] = "sorry ! you mistyped your old password !";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    		    
			break;
		}
		$admin_id = $admin_data[id];
		$parm = array ( 
				"id"			=> $admin_id,
				"login_pass1"	=> $PHP_login_pass1,
				"login_pass2"	=> $PHP_login_pass2);

		if (!$admin->admin_update(2,$parm)) {
			$op[msg] = $admin->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;		    
		}
		$admin->login($admin_data[login_id],$PHP_login_pass1);
				# �O���ϥΪ̰ʺA
		$log->log_add(0,"password","change password completely");
		$op[msg][] = "Successfully change your password";
		$layout->assign($op);
		$layout->display($TPL_ERROR);  		    		    
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "show_page":
				# ���o�@�Ǹ��
		$op[category_names] = array_values($webshop->class_name1);
		$op[category_ids] = array_keys($webshop->class_name1);
		$layout->assign($op);
      $layout->display($PHP_page);
		break;
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "display_page":

			$allow = $admin->decode_perm($PHP_i,$PHP_j);   // �]�w �s�W�R���v��
			if ($allow['view'])	{	$op['view_flag'] = 1;} else {	$op['view_flag'] = "";}
			if ($allow['add'])	{	$op['add_flag'] = 1;} else {	$op['add_flag'] = "";}
			if ($allow['edit'])	{	$op['edit_flag'] = 1;} else {	$op['edit_flag'] = "";}
			if ($allow['del'])	{	$op['del_flag'] = 1;} else {	$op['del_flag'] = "";}

			$layout->assign($op);
	
			$layout->display($PHP_page);
		break;




//  new adding  ---------------------------- 2005/04/08
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//		job 63  system log
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "log_admin":			//  �e�{  admin log �D��
      check_authority(6,3,"view");
      
      page_display($op, 6, 3, $TPL_ADMIN_LOG_ADMIN);

		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "log_admin_show":		//  �d�M admin log �ɤ��e

		check_authority(6,3,"view");
		if ($PHP_srch_by == 'all'){
			$mode = "all";
		}
		if ($PHP_srch_by == 'id'){
			$mode = "id";
		}
		if ($PHP_srch_by == 'date'){
			$PHP_sr_parm_date = $select_year.$select_month.$select_day;
			$mode = "date";
		}
		if ($PHP_srch_by == 'code'){
			$mode = "code";
		}

		if (!$op = $admin->admin_log_search($mode)) {
			$op = array();
			$op['msg']= $admin->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$op['msg'] = $admin->msg->get(2);
		if ($PHP_edit_flag) {
			$op['edit_flag'] =1;
		}

		page_display($op, 6, 3, $TPL_ADMIN_LOG_ADMIN_SHOW);
		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_log_dump":	 	JOB 11A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "log_dump":
		$where_str = "WHERE date <= '$PHP_date'";

		$op['logs'] = $log->search($where_str);
		$op['del_date'] = $PHP_date;
//		$op['del']['wherestr'] = $str;
		$op['del']['des'] = "Are you sure to delete records? ";
		$op['del']['detail'] = "Before Date: $PHP_date Log records" ;
		
		page_display($op, 6, 3, $TPL_ADMIN_LOG_CFM_DEL);
		
		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_log_dump":	 	JOB 11A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "do_log_dump":
		$f1 = $log->dump_log($PHP_date);
		if ($f1) $op['msg'][] = "Success to Delete ".$f1." log field in date <".$PHP_date;
		if (!$f1) $op['msg'] = $log->msg->get(2);
		$layout->assign($op);
		$layout->display($TPL_ERROR);  		
		
		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "log_admin_cfm_delete": //  �R�� admin log ��
			$str="";
		check_authority(6,3,"del");
		if ($PHP_del_mode == 0){  // �v�@�Ŀ�id�R����
				$id_arry = $PHP_del_id;
			$num = count($id_arry);
			if (!$num) {
				$op['msg'][] = "Error ! Please choice delete record on click box !";
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		
			} else {
				$op['del']['id_arry'] = $PHP_del_id;   //  template �n�� session �a�J�}�C
				$op['del']['detail'] = implode(", ",$id_arry);
				$op['del']['mode'] = $PHP_del_mode;
				$op['del']['des'] = "Delete log ID : ";

				$layout->assign($op);
				$layout->display($TPL_ADMIN_LOG_CFM_DEL);  
			}
		break;
		} elseif ($PHP_del_mode == 1){  // �����R����

				$op['del']['mode'] = $PHP_del_mode;
				$op['del']['des'] = "You sure to delete ";
				$op['del']['detail'] = "All Log records";
				$layout->assign($op);
				$layout->display($TPL_ADMIN_LOG_CFM_DEL);  		    
		break;
		} elseif ($PHP_del_mode == 2){  // ����R����
				$op['del']['mode'] = $PHP_del_mode;      // �a�X�Ѽƥ�
				//  ���]�w  $str
				$str = "";
			if (isset($PHP_del_opt_date)) {
				#�p���J�������
					$detail_date = $select_year."-".$select_month."-".$select_day;
				if ($PHP_del_by_date == "yes") {
					$PHP_del_opt_date = $select_year.$select_month.$select_day;
					$str = " WHERE date < $PHP_del_opt_date ";
					$detail = "Before Date: ".$detail_date."Log records ";
				} elseif ($PHP_del_by_date == "no")  {
					$PHP_del_opt_date = $detail_date;
					$str = " WHERE date LIKE '%$PHP_del_opt_date%' ";
					$detail = "Point Date : ".$detail_date." Log records ";
				} else {
					$op['msg'][] = "Error! Please choice one of delete rule! ";
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		
				break;
				}
			} elseif  (isset($PHP_del_opt_user)) {
					$PHP_del_opt_user = trim($PHP_del_opt_user);
				if (!$PHP_del_opt_user) {
					$op['msg'][] = "Error! Please input name! ";
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		
					break;
				}
				$str = " WHERE user =  '$PHP_del_opt_user' ";
				$detail = " USER:[ ".$PHP_del_opt_user." ] Log records. ";
			} elseif  (isset($PHP_del_opt_class)) {
				$PHP_del_opt_class = trim($PHP_del_opt_class);
				if (!$PHP_del_opt_class) {
					$op['msg'][] = "Error! Please input job ID ";
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		
						break;
				}
					$str = " WHERE class =  '$PHP_del_opt_class' ";
					$detail = "job ID above:[ ".$PHP_del_opt_class." ] Log records";
			}	//  $str �]�w����

			if ($str) {
				$op['del']['wherestr'] = $str;
				$op['del']['des'] = "Are you sure to delete records? ";
				$op['del']['detail'] = $detail;
				$layout->assign($op);
				$layout->display($TPL_ADMIN_LOG_CFM_DEL);  		    
			break;
			} else {
					# �O���ϥΪ̰ʺA
				$log->log_add(0,"63","Fall to delete log records");
				$op['msg'][] = "Error !The deletion condition of indetermination  !";
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		
					break;
			}
		}  // end mode=?

		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "log_admin_delete":	//  �R�� admin log ��
			$str="";
		check_authority(6,3,"del");
		if ($PHP_del_mode == 0){	// �v�@�Ŀ�id�R����

			$id_arry = $PHP_del_id;
			$num = count($id_arry);	//  �R���ɮ׼ƥ�
			if (!$num) {
				$op['msg'][] = "Error! You don't click any delete record!";
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		
			} else {
				$log->log_del(0, $id_arry,"");	// delete..........

				$new_log = "Delete these log records; id = :".$PHP_del_detail." ";			
				$log->log_add(0,"63D",$new_log);	//  �O���ϥΪ̰ʺA

				$op['msg'][] = $num."  log records successfully delete!";
				$layout->assign($op);
				$layout->display($TPL_BACK_LOG);  	
			}
		break;
		} else if ($PHP_del_mode == 1){  // �����R����

			$log->log_del(1);	// delete.........
			$log->log_add(0,"63D","Delete all log records by USER!");	//  �O���ϥΪ̰ʺA
			$op['msg'][] = " Successfully delete all log records by USER!";
			$layout->assign($op);
			$layout->display($TPL_BACK_LOG);  		    		    
		break;
		} else if ($PHP_del_mode == 2){  // ����R����
			$str = ereg_replace("[\x5c\]","",$PHP_del_where_str);    //  �����ϱ׽u \
			if ($str) {

			$num = $log->log_nums($str);//  �]�w �q�_�R���ɮ׼ƥ�

			if (!$num) {
				$op['msg'][] = "Error ! Can't find delete records in these rule!";
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		
			} else {
				$log->log_del(2,0,$str);  //  �R��
					//  �O���ϥΪ̰ʺA
				$new_log = "Success delete ".$PHP_del_detail." ; Total:".$num."record";	
				$log->log_add(0,"63D",$new_log);
				$op['msg'][] = $num. " Records successfully delete!";
				$layout->assign($op);
				$layout->display($TPL_BACK_LOG);  
			}
			break;
		} else {
			//  �O���ϥΪ̰ʺA
			$log->log_add(0,"63D","Fail to delete log records !");
			$op['msg'][] = "Error ! Fail delete command!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		
				break;
		}
		}  // end mode=?

		break;




#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#					order part
#
#	  order status :
#		status == 0			waitt'g for IE rec.
#		status == 1			wait for submit
#		status == 2			waitt'g for CFM
#		status == 3			waitt'g for APV
#		status == 4			APVED
#		status == 5			Reject
#		status == 6			SCHDL CFMing
#		status == 7			SCHDL CFMed
#		status == 8			PRODUCING
#		status == 10		= FINISHED =
#		status == 12		== SHIPPED ==
#		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#			 job 101  �q ��
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "order_entry":
		check_authority(10,1,"view");
		$where_str = $manager = $manager_v = $dept_id = '';
		$dept_ary = array();
		$sales_dept_ary = get_sales_dept(); // ���X �~�Ȫ����� [���tK0] ------
		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];   // �P�w�i�J����������
		$team = $GLOBALS['SCACHE']['ADMIN']['team_id'];		// �P�w�i�J������Team
		
		$op['factory'] = $arry2->select($FACTORY,'','PHP_factory','select','');

		for ($i=0; $i<count($sales_dept_ary);$i++){
			if($user_dept == $sales_dept_ary[$i]){

				// �p�G�O�~�ȳ��i�J �hdept_code ���w�ӷ~�ȳ�---
				$dept_id = $sales_dept_ary[$i];  
			}
		}

		if (!$dept_id) {    // ���O�~�ȳ��H[�]���t K0 ���H ]�i�J��
			$manager = 1;
			//�~�ȳ��� select���
			$dept_ary = $arry2->select($sales_dept_ary,"","PHP_dept_code","select","");  
		} else {
			$where_str = " WHERE dept = '".$dept_id."' ";			
		}
		if(($user_dept == 'HJ' ||$user_dept == 'LY' ) && $team<>'MD') 		
		{									//��u�t�H�������OMD Team
			$manager = 1;
			//�~�ȳ��� select���
			$dept_ary = $arry2->select($sales_dept_ary,"","PHP_dept_code","select","");  
			$where_str ='';

		}

		$op['manager_flag'] = $manager;
		$op['dept_id'] = $dept_id;
		$op['dept_ary'] = $dept_ary;

		$op['msg'] = $order->msg->get(2);
		// creat cust combo box
		// ���X �Ȥ�N��
		$where_str=$where_str."order by cust_s_name"; //��cust_s_name�Ƨ�
		if(!$cust_def = $cust->get_fields('cust_init_name',$where_str)){;  //���X�Ȥ�²��
			$op['msg'][] = "sorry! there is no any customer record in your team, please add customer first!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//���X�Ȥ�N��
		for ($i=0; $i< sizeof($cust_def); $i++)
		{
			$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//�H [�Ȥ�N��-�Ȥ�²��] �e�{
		}
		$op['cust_select'] =  $arry2->select($cust_value,'','PHP_cust','select','',$cust_def_vue); 

			// creat factory combo box
		  	
		page_display($op, 10, 1, $TPL_ORDER);			    	    
		break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "order_search":
		check_authority(10,1,"view");
	
		$parm = array(	"dept"		=>  $PHP_dept_code,	"order_num"	=>  $PHP_order_num,
						"cust"		=>	$PHP_cust,		"ref"		=>	$PHP_ref,
						"factory"	=>	$PHP_factory
				);

					//�i�Q��PHP_dept_code�P�w�O�_ �~�ȳ����i�J
			if (!$op = $order->search(1, $PHP_dept_code)) {  
				$op['msg']= $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}


		if (!$PHP_dept_code) {   // �p�G ���O �e��~�ȳ����i�J��...
			$op['manager_flag'] = 1;
		}
		$op['dept_id'] = $PHP_dept_code;

				$op['msg']= $order->msg->get(2);
				$op['cgi']= $parm;

			page_display($op, 10, 1, $TPL_ORDER_LIST);
		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "order_del":
       if (isset($PHP_bk))
       {
			check_authority(10,1,"add");
		}else{
			check_authority(10,1,"del");
		}

		$parm = array(	"dept"		=>  $PHP_dept_code,	"order_num"	=>  $PHP_order_num,
										"cust"		=>	$PHP_cust,		"ref"		=>	$PHP_ref,
										"factory"	=>	$PHP_factory
				);	
		$order_rec = $order->get($PHP_id);  //���X�ӵ��O��	
		
		$f1 = $wi->order_del($order_rec['order_num']);//�R���s�y�O����
		
		if ($PHP_status < 4 || $PHP_status == 5)	//�q�楼�֥i
		{
			$op['order'] = $order->get($PHP_id);  //���X�ӵ��O��
			if (!$op['order']) {
				$op['msg'] = $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			$f1=$order_log->del($op['order']['order_num']); //�R��order log
			$f1 = $order->del($PHP_id);						//�R���q��(s_order)
			$f1 = $order->del_pdtion($op['order']['order_num']); //�R���q��(pdtion)
		}elseif($PHP_status == 4 || $PHP_status == 6) {		//�v�֥i�B�Ʋ����T�{
			$op['order'] = $order->get($PHP_id);  //���X�ӵ��O��

			$f1 = $order->delete_month_su( $op['order']['etp'], $op['order']['etd'],$op['order']['su'],$op['order']['factory'],'pre_schedule');  //�R��ETD~ETP�~�פ�����t
			$f1 = $order->del_pdtion($op['order']['order_num']); //�R���q��(pdtion)
			$f1 = $order_log->del($op['order']['order_num']);  //�R��order log
			$f1 = $order->del($PHP_id);		//�R���q��(s_order)
		}elseif($PHP_status == 7){  //�v�Ʋ�
			$op['order'] = $order->get($PHP_id);  //���X�ӵ��O��

			$f1 = $order->delete_month_su( $op['order']['etp'], $op['order']['etd'],$op['order']['su'],$op['order']['factory'],'pre_schedule'); //�R��ETD~ETP�~�פ�����t

			$op['order'] = $order->schedule_get($PHP_id);  //���X�ӵ��Ͳ������O��

			$order->delete_month_su( $op['order']['ets'], $op['order']['etf'],$op['order']['su'],$op['order']['factory'],'schedule');	//�R��ETF~ETS�~�פ�����t
			$f1 = $order->del_pdtion($op['order']['order_num']);	//�R���q��(pdtion)
			$f1 = $order_log->del($op['order']['order_num']);	 //�R��order log
			$f1 = $order->del($PHP_id);	//�R���q��(s_order)
		}elseif($PHP_status == 8 || $PHP_status == 10 || $PHP_status == 12){	//�q��}�l�Ͳ���
			$op['order'] = $order->get($PHP_id);  //���X�ӵ��O��

			$f1 = $shipping->order_ship_del($op['order']['order_num'],$op['order']['uprice']);		//�R��ship�~�פ�����t
			$f1 = $shipping->ship_del($op['order']['order_num']);			//�R���C��ship�O��
			$f1 = $daily->order_daily_del($op['order']['order_num']);		//�R��out-pu�~�פ�����t	
			$f1 = $daily->daily_del($op['order']['order_num']);			//�R���C��out-put�O��
			$f1 = $order->delete_month_su( $op['order']['etp'], $op['order']['etd'],$op['order']['su'],$op['order']['factory'],'pre_schedule');	//�R��ETD~ETP�~�פ�����t

			$op['order'] = $order->schedule_get($PHP_id);  //���X�ӵ��Ͳ������O��

			$order->delete_month_su( $op['order']['ets'], $op['order']['etf'],$op['order']['su'],$op['order']['factory'],'schedule'); //�R��ETF~ETS�~�פ�����t
			$f1 = $order->del_pdtion($op['order']['order_num']); //�R���q��(pdtion)
			$f1 = $order_log->del($op['order']['order_num']); //�R��order log
			$f1 = $order->del($PHP_id);	//�R���q��(s_order)
			
		}
		
			$op['order']['cancer_date'] = $GLOBALS['THIS_TIME'];  //xxxx-xx-xx : xx.xx.xx����ɶ�
			$op['order']['cancer_user'] = $GLOBALS['SCACHE']['ADMIN']['login_id']; //�ϥΪ̱b��
			$old_orders=$smpl_ord->get_fieldvalue('','orders', $order_rec['smpl_ord']); //���o�˥���orders���e
			$id=$smpl_ord->get_fieldvalue('','id', $order_rec['smpl_ord']);//���o�˥���id
			$tmp=explode('|',$old_orders);
			$tmp_orders='';
			for ($i=0; $i< sizeof($tmp); $i++)  //����orders,�N�R���̥h��
			{
				if ($tmp[$i]<>$order_rec['order_num'])	$tmp_orders=$tmp_orders.$tmp[$i]."|";
			}
			if($tmp_orders<>'') $tmp_orders=substr($tmp_orders,0,-1);
			$s_parm['field_name']='orders';		$s_parm['field_value']