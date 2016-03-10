<?php
##################  2004/11/10  ########################
#			index.php  �D�{��
#		for Carnival SCM [Sample]  management
#			Jack Yang     2004/11/10
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//		job 39  [�Ͳ��s�y][�Ͳ�����]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

require_once "config.php";
require_once "config.admin.php";
require_once "init.object.php";

$PHP_SELF = $_SERVER['PHP_SELF'];
$perm = $GLOBALS['power'];
$op = array();

//�uú���o
session_register ('sch_parm');

//�q��ETD,ETP����
$tmp = $para->get(0,'ord_append_ETP');
$ord_append_ETP_limit = increceDaysInDate($TODAY,$tmp['set_value']);
$tmp = $para->get(0,'ord_append_ETD');
$ord_append_ETD_limit = increceDaysInDate($TODAY,$tmp['set_value']);

$ord_submit_ETP_limit = $TODAY;
$tmp = $para->get(0,'ord_submit_ETD');
$ord_submit_ETD_limit = increceDaysInDate($TODAY,$tmp['set_value']);

$english = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');

include_once($config['root_dir']."/lib/class.transport.php");
$transport = new TRANSPORT();
if (!$transport->init($mysql,"log")) { print "error!! cannot initialize database for ORDER SHIFT class"; exit; }

#++++++++++++++    SAMPLE ORDER +++++++++++++  2007/09/11  +++++++++++++++++
#		 job 101    �q��
#++++++++++++++++++++++++++++++++++++++++++++  2007/09/11  +++++++++++++++++
#			case "ord_ship_edit"
#			case "ord_ship_del"
# 		case "ord_custpo_edit"		�ϥ�AJAX�ק�customer po (�b�����q��e�i�ק�)
#			case "do_order_revise"
#			case "revise_order"
#			case "order_edit"
#			case "do_order_edit"
#		 	case "do_order_rmk"
# 		case "order_log_del"
# 		case "do_order_rmk_edit"
#			case "do_etd_edit"	2007-05-18�W�[ETD�i�b����ɭԭק�
#			case "order_apvl"
#			case "order_pic_edit"
#			case "do_order_pic_edit"
#			case "edit_order_season"
#			case "stock_ord_add"
#			case "do_order_add":

// echo $PHP_action.'<br>';
switch ($PHP_action) {


#========================================================================================
# case "order_combine_edit"	�X�����p�q��
#========================================================================================
case "order_combine_edit":
check_authority('065',"edit");

$order->update_field( 'combine' , $_POST['PHP_combine'] , $PHP_id );

$mesg = "SUCCESSFULLY UPDATE Combine Order , ON ORDER :".$_POST['PHP_combine'];

$old = ( !empty($_POST['PHP_combine_old']) ) ? $_POST['PHP_combine_old'] . ' => ' : '';
$mesg_log = "UPDATE Combine Order [ ".$_POST['PHP_ord_num']." ] : ".$old.$_POST['PHP_combine'];
$log->log_add(0,"065E",$mesg_log);

$back_str = "index2.php?PHP_action=order_view&PHP_id=".$PHP_id."&PHP_msg=".$mesg;
redirect_page($back_str);
break;



#-------------------------------------------------------------------------------------
case "drop_cust_po":
check_authority('065',"edit");

$S_ROOT = dirname(__FILE__);
$Flag = substr(($S_ROOT),2,1);

// �ߧY�R�� print_r($_GET);
$str_arr = '';

// �j�M�O�_���� PO
$cust_po = $order->get_field_value( 'cust_po' , $order_id , '' );

$no_1 = 0;

$c_arr = explode('|',$cust_po);

foreach($c_arr as $key => $val){
	$tmp = explode('/',$val);
	$file_status = '';
	if ( $tmp[0] == $po_num ) {
		if( !empty($files) && file_exists($S_ROOT.$Flag."ord_cust_po".$Flag.$files) ) {
			$file_status = '';
			if( unlink($S_ROOT.$Flag."ord_cust_po".$Flag.$files) ) {
				$file_status = 'ok';
			} else {
				$file_status = 'no';
			}
		}
	} else {
		$str_arr .= (( $no_1 > 0 )?'|':'').$val;
		$no_1++;
	}
}

$po_status = '';

if ( $order->update_field('cust_po',$str_arr,$order_id) ) {
	$po_status = 'ok';
} else {
	$po_status = 'no';
}

echo $po_status.'@'.$file_status;
break;



#-------------------------------------------------------------------------------------
case "check_cust_po":

$S_ROOT = dirname(__FILE__);
$Flag = substr(($S_ROOT),2,1);

if( !empty($file) && file_exists($S_ROOT.$Flag."ord_cust_po".$Flag.$file) ) {
	$file_status = 'in';
} else {
	$file_status = 'no';
}

echo $file_status.','.$file;

break;


//= start case ====================================================

#-------------------------------------------------------------------------------------
#			case "ord_ship_edit":
#-------------------------------------------------------------------------------------
case "ord_ship_edit":
$ord_det = $order->get($PHP_id);
if($ord_det['ship_way'] == '')
{
	$ord_det['ship_way'] = $PHP_ship_way;
	$ord_det['ship_dist'] = $PHP_ship_dist;    	
	$ord_det['ship_day'] = $PHP_ship_day;
}else{
	$ord_det['ship_way'] = $ord_det['ship_way'].'|'.$PHP_ship_way;
	$ord_det['ship_dist'] = $ord_det['ship_dist'].'|'.$PHP_ship_dist;
	$ord_det['ship_day'] = $ord_det['ship_day'].'|'.$PHP_ship_day;
}

$f1 = $order->update_field('ship_way', $ord_det['ship_way'], $PHP_id);
$f1 = $order->update_field('ship_dist', $ord_det['ship_dist'], $PHP_id);
$f1 = $order->update_field('ship_day', $ord_det['ship_day'], $PHP_id);    	    
break;



#-------------------------------------------------------------------------------------
#			 case "ord_ship_del":
#-------------------------------------------------------------------------------------
case "ord_ship_del":
    
$f1 = $order->update_field('ship_way', '', $PHP_id);
$f1 = $order->update_field('ship_dist', '', $PHP_id);
$f1 = $order->update_field('ship_day', '', $PHP_id);

break;



#========================================================================================
# case "ord_custpo_edit"		�ϥ�AJAX�ק�customer po (�b�����q��e�i�ק�)
#========================================================================================
case "ord_custpo_edit":
check_authority('065',"edit");

$cust_po = $order->get_field_value( 'cust_po' , $PHP_id , '' );

$S_ROOT = dirname(__FILE__);
// echo $S_ROOT; 
$Flag = substr(($S_ROOT),2,1);
// echo $Flag;exit;
$po_str = '';
$po_number = 0;
$upload = new Upload;

foreach($PHP_cust_po as $k => $po ){
	if( $_FILES['PHP_cust_file']['size'][$k] > $upload->max_file_size || $_FILES['PHP_cust_file']['error'][$k]=='1' || $_FILES['PHP_cust_file']['error'][$k]==='' ){
		$mesg = "ERROR [ ". $_FILES['PHP_cust_file']['name'][$k]." ] UPLOAD FILE IS OVER 3M! ";
		$back_str ="index2.php?PHP_action=order_view&PHP_id=".$PHP_id."&PHP_msg=".$mesg;
		redirect_page($back_str);
	}
}

foreach($PHP_cust_po as $k => $po ){

	if ( !empty($po) ) {
		echo "ff";
		$filename = $_FILES['PHP_cust_file']['name'][$k];
		
		if(!empty($filename) && file_exists($S_ROOT.$Flag."ord_cust_po".$Flag.$filename)){
			unlink($S_ROOT.$Flag."ord_cust_po".$Flag.$filename);
		}
		
		$upload->ArrUploadFile($S_ROOT.$Flag."ord_cust_po".$Flag, 'other', 20, $filename ,$k );
		
		if (!$upload){
			$op['msg'][] = $upload;
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$mesg = "SUCCESSFULLY UPDATE CUSTOMER PO , ON ORDER :".$PHP_ord_num;

		$log->log_add(0,"101E",$mesg);
		
		$mk = ( $po_number <> 0 ) ? '|' : '' ;
		
		$po_str .= $mk.($po).'/'.$filename;
		
		$po_number++;
	}

}

if( $po_number == 0 ) $mesg = '�S���ק�����ơI';

$order->update_field('cust_po',(!empty($cust_po)? $cust_po.'|' : '' ).$po_str, $PHP_id);
$back_str ="index2.php?PHP_action=order_view&PHP_id=".$PHP_id."&PHP_msg=".$mesg;
redirect_page($back_str);
break;	
	


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#			 case "do_order_revise"
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_order_revise":
check_authority('065',"edit");

//-------------���X�ӵ��O��
$old = $order->get(0,$PHP_order_num);  
if (!$old) {
    $op['msg'] = $order->msg->get(2);
    $layout->assign($op);
    $layout->display($TPL_ERROR);  		    
    break;
}

$smpl=$smpl_ord->get(0,0,$PHP_smpl_ord);
$PHP_num = $smpl['num'];

$PHP_style = $smpl['style'];
$PHP_style_num =$smpl['ref'];
$PHP_apv_date=$smpl['apv_date'];
$PHP_spt=$smpl['spt'];
$PHP_orders=$smpl['orders'];

$op['order'] = $order->get($PHP_id);
$PHP_old_order="";
if ($op['order']['smpl_ord'] <> $PHP_smpl_ord) //�˥��s�����ܮ�
{
    $smpl=$smpl_ord->get(0,0,$op['order']['smpl_ord']);
    $old_order=$smpl['orders'];		
    $old_smpl_ord=$smpl['num'];
    $old_ord=explode('|',$old_order);			
    if ($PHP_order_num==$old_ord[sizeof($old_ord)-1]){$r_size=sizeof($old_ord)-2;}else{$r_size=sizeof($old_ord)-1;}
    for ($i=0; $i<sizeof($old_ord); $i++)
    {
        if ($old_ord[$i] <> $PHP_order_num)
        {
            if($i==$r_size){
            $PHP_old_order=$PHP_old_order.$old_ord[$i];
            }else{
            $PHP_old_order=$PHP_old_order.$old_ord[$i].'|';
            }
        }
    }
    $PHP_status=0;
    $PHP_su=0;
    if ($PHP_apv_date=='0000-00-00')
    {	
        $PHP_ie_time2=0;
    }else{
        $PHP_ie_time2=$smpl['spt'];
    }
    $PHP_ie2 = 0;
}else{
    $PHP_old_order="no_order";
    $old_smpl_ord="";
    $PHP_status=1;
    $PHP_ie_time2 = $op['order']['ie_time2'];
    $PHP_ie2 = $op['order']['ie2'];
}	

$PHP_etd = '0000-00-00'; $PHP_etp='9999-99-99';
$PHP_qty = $PHP_su = 0;

foreach($PHP_ps_qty as $key => $value)
{
    $etd_name = 'PHP_etd_'.$key;
    $etp_name = 'PHP_etp_'.$key;
    $remark =   'PHP_remark_'.$key;
    if($PHP_ps_qty[$key] > 0 && $$etd_name && $$etp_name)
    {
        $PHP_qty +=$PHP_ps_qty[$key];						
        $ps_etd[] = $$etd_name;
        $ps_etp[] = $$etp_name;
        $ps_remark[] = $$remark;
        $ps_qty[] = $PHP_ps_qty[$key];
        $ps_id[]	= $PHP_ps_id[$key];
        $ps_su[]=  number_format(($PHP_ps_qty[$key] * $PHP_ie1),0,'','');
        $PHP_su += number_format(($PHP_ps_qty[$key] * $PHP_ie1),0,'','');		
        if($$etd_name > $PHP_etd)$PHP_etd = $$etd_name;
        if($$etp_name < $PHP_etp)$PHP_etp = $$etp_name;
    }
}
// echo $PHP_etd;
$argv = array(
	"id"				=>	$PHP_id,
	"order_num"			=>	$PHP_order_num,
	"dept"				=>	$PHP_dept,
	"ref"				=>	$PHP_ref,
	"factory"			=>	$PHP_factory,
	"style"				=>	$PHP_style,

	"unit"				=>	$PHP_unit,
	"style_num"			=>	$PHP_style_num,
	"patt_num"			=>	$PHP_patt_num,
	"uprice"			=>	$PHP_uprice,
	"quota"				=>	$PHP_quota,


	"ie1"				=>	$PHP_ie1,

	"old_num"			=>	$old_smpl_ord,
	"old_orders"		=>	$PHP_old_order,

	"smpl_ord"			=>	$PHP_smpl_ord,
	"num"				=>	$PHP_num,
	"smpl_apv"			=>	'',						
	"ie_time1"			=>	$op['order']['ie_time1'],
	"ie_time2"			=>	$PHP_ie_time2,
	"ie2"				=>	$PHP_ie2,
	"orders"			=>	$PHP_orders,						


	"mat_u_cost"		=>	$PHP_mat_u_cost,
	"mat_useage"		=>	$PHP_mat_useage,
	"acc_u_cost"		=>	$PHP_acc_u_cost,
	"quota_fee"			=>	$PHP_quota_fee,
	"comm_fee"			=>	$PHP_comm_fee,
	"cm"				=>	$PHP_cm,
	"smpl_fee"			=>	$PHP_smpl_fee,

	"emb"				=>	$PHP_emb,
	"wash"				=>	$PHP_wash,
	"oth"				=>	$PHP_oth,
	"oth_treat"			=>	$PHP_oth_treat,

	"pic"				=>	$PHP_pic,
	"pic_upload"		=>	$PHP_pic_upload,

	"etd"				=>	$PHP_etd,
	"etp"				=>	$PHP_etp,						
	"revise"			=>	$old['revise']+1,
	"status"			=>	$PHP_status,						
	"last_updator"		=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],

	"agent"				=>	$PHP_agent,
	"fusible"			=>	$PHP_fus_u_cost,
	"interline"			=>	$PHP_int_u_cost,
	"line_sex"			=>	$PHP_line,
	"lots_unit"			=>	$PHP_lots_unit,
	"oth_pic"			=>	$PHP_oth_pic,
	"oth_pic_upload"	=> 	$PHP_oth_pic_upload,
	'pic_num'			=>	$PHP_oth_pic_num,
	'qty'				=>	$PHP_qty,
	'su'				=>	$PHP_su,
	'rev_des'			=>	$PHP_des,

	'season'			=>	$PHP_season,
	'syear'				=>	$PHP_syear,

	"ps_etd"			=>	$ps_etd,
	"ps_etp"			=>	$ps_etp,
	"ps_remark"			=>	$ps_remark,
	"ps_qty"			=>	$ps_qty,
	"ps_su"				=>	$ps_su,
	"ps_id"				=>	$ps_id,
	"ps_num"			=>	sizeof($ps_qty)	

);


$op['order'] = $argv;			// ��ݭn��^�� �i�� ---- ps_id
$check = $order->check($argv);   // �浹 order class ��check [���جۦP]
// .....................................��J��� �����~��  �A�^��˥���J���
if (!$check || !$PHP_des) {
    $op['order'] = $order->get($PHP_id);
    //------

    // creat factory combo box
    $user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
    if ( $user_dept=="HJ" || $user_dept=="LY" ){
        if($user_dept=="HJ") $fty = array($user_dept,'MA');
        if($user_dept=="LY") $fty = array($user_dept);
        $op['factory'] = $arry2->select($fty,$op['order']['factory'],'PHP_factory','select','');
    }else{
        $op['factory'] = $arry2->select($FACTORY,$op['order']['factory'],'PHP_factory','select','');  	
    }

    // creat style type combo box
    $style_def = $style_type->get_fields('style_type');   // ���X �ڦ����O
    $op['style'] =  $arry2->select($style_def,$op['order']['style'],'PHP_style','select','');
        
    // creat apparel unit combo box
    $op['unit'] = $arry2->select($APPAREL_UNIT,$op['order']['unit'],'PHP_unit','select','');  	

    //*****//2006.12.08	�ק�sample���U�Ԧ� start
    $s_date=increceDaysInDate($TODAY,-1200); //12�Ӥ�e
    // $where="where last_update > '".$s_date."'and cust='".$op['order']['cust']."'  AND dept ='".$op['order']['dept']."' order by num";
    $where="where last_update > '".$s_date."'and cust='".$op['order']['cust']."' order by num";

    $smpl_no=$smpl_ord->get_fields("num",$where);		
    $j=0;
    if($smpl_no){
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

    $op['smpl_no']=$arry2->select($sml_no,$op['order']['smpl_ord'],'PHP_smpl_ord','select','');
    //*****//2006.12.08	�ק�sample���U�Ԧ�	end

        // �ˬd �ۤ��O�_�s�b
    if(file_exists($GLOBALS['config']['root_dir']."/picture/".$op['order']['order_num'].".jpg")){
        $op['main_pic'] = "./picture/".$op['order']['order_num'].".jpg";
    } else {
        $op['main_pic'] = "./images/graydot.gif";
    }

    //08.09.23 ��L�ۤ��[�J
    $md = $op['order']['pic_num'] % 5;
    if ($md > 0) $md--;
    $op['order']['pic_num'] = $op['order']['pic_num'] + $md ;
    if(!$op['order']['pic_num'])$op['order']['pic_num'] = 5;
    for($i=0; $i< $op['order']['pic_num']; $i++)
    {		
        if(file_exists($GLOBALS['config']['root_dir']."/picture/".$op['order']['order_num']."_".$i.".jpg")){
            $op['oth_pic'][$i] = "./picture/".$op['order']['order_num']."_".$i.".jpg";
        } else {
            $op['oth_pic'][$i] = "./images/graydot.gif";
        }
        $op['oth_num'][$i] = $i;
        if((($i+1) % 5 == 0) )
        {
            $op['oth_ll'][$i] =1;
        }else{
            $op['oth_ll'][$i] =0;
        }
    }

    $op['ttl_pic'] = $i-1;


    $where_str="ORDER BY cust_s_name"; //��cust_s_name�Ƨ�
    $cust_def = $cust->get_fields('cust_init_name',$where_str);
    $cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//���X�Ȥ�N��
    for ($i=0; $i< sizeof($cust_def); $i++)
    {
        $cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//�H [�Ȥ�N��-�Ȥ�²��] �e�{
    }
    $op['agent_select'] =  $arry2->select($cust_value,$op['order']['agent'],'PHP_agent','select','',$cust_def_vue); 

    //�Ͳ��u���O�k�k�� -- 08.08.27
    $line_value = array('F','M','A');
    $line_key = array('Woman','Man','Ann');
    $op['line_select'] =  $arry2->select($line_key,$op['order']['line_sex'],'PHP_line','select','',$line_value); 
    //�D�Ƴ��--08.09.22
    $op['lots_unit'] =  $arry2->select($LOTS_PRICE_UNIT,$op['order']['lots_unit'],'PHP_lots_unit','select',''); 

    //�~�שu�`		
    $op['season_select'] =  $arry2->select($SEASON,$op['order']['season'],'PHP_season','select',''); 
    $op['year_select'] =  $arry2->select($YEAR_WORK,$op['order']['syear'],'PHP_syear','select',''); 


        $op['msg'] = $order->msg->get(2);
        if(!$PHP_des) $op['msg'][] = 'Please input the reason for order revies';
    $op['new_revise'] = $op['order']['revise'] +1;
    page_display($op, '065', $TPL_ORDER_REVISE);	// ��^ revise ����
    break;
}

// ��J�����T��............after check input.........

// �p��  gm rate -----------------------------
# 2013-07-11 �N���ѥH�᪺�q�檺 handling_fee �g�^ s_order ��(factory = LY �B �ư� L �}�Y���q��)
# 2013-07-11 �ѩ� �uú �ثe�O��ʬD���q�A�ۦ�[�J handling_fee�C�令 �uú �h�@�� �N handling_fee �ǤJ�p��(2013-07-11�{����s��ҷs�إߪ��q��~�ǤJ�p��)
# 2013-07-11 ���ѥH�e�� handling_fee ��줣��ȡA��ε{���P�_2012/08/15����Nhandling_fee�]��0.25�A�ت��O���e�x���(order list�Border show�Border cfm...��)���`
$handling_row = $order->get_fields("set_value", " where set_name='handling_fee'","para_set");
if($argv['factory'] == 'LY' and $old['opendate'] >= '2013-07-11' and substr($old['order_num'],0,1) <> 'L'){
	$argv['handling_fee'] = $old['handling_fee'];
}elseif($argv['factory'] == 'LY' and $old['opendate'] >= '2012-08-15' and substr($old['order_num'],0,1) <> 'L'){
	$argv['handling_fee'] = $handling_row[0];
}else{
	$argv['handling_fee'] = 0.00;
}

$unit_cost = ($argv['mat_u_cost']* $argv['mat_useage'])+ $argv['interline']+ $argv['fusible']+ $argv['acc_u_cost'] + $argv['quota_fee'] + $argv['comm_fee'] + $argv['cm'] + $argv['emb'] + $argv['wash'] +$argv['oth'] + $argv['handling_fee'];
$grand_cost = $unit_cost*$argv['qty'] + $argv['smpl_fee'];
$sales = $argv['uprice']*$argv['qty'];
$gm = $sales - $grand_cost;
    //2005/05/07 edit
    if ($sales){
        $rate = ($gm/ $sales)*100;
        $argv['gmr'] = number_format($rate, 2, '.', ',');
    }else{
        $rate = 0;
        $argv['gmr'] = 0.00;
    }

// ========= add to DB (s_order �� update ) =======
#M11022502 �ק� REVISE (Partial)
// if ( $old['status'] >= 7 ) {
    # �w�Ʋ����A�U renew �q��A�����R���w�Ʋ������
    # �R���ӭq�檺  partial + schedule
    # ����A�̾ڨ��X���Ͳ��u�M����A��ӥͲ��u����Ƨ�X���s�ƾ�
    // $order->del_partial($PHP_order_num);
    // $re_sch = $schedule->del_ord_sch($PHP_order_num);
// }

if (!$order->edit($argv,1,$old['status'])) {		// mode =1 : ��revise �� edit
    $op['msg'] = $order->msg->get(2);
    $layout->assign($op);
    $layout->display($TPL_ERROR);  		    
    break;	    
}


// ---- �N ���ɤ�(s_order) �s�J (r_order) ----

// ========= add to  r_order table DB =======
//  ���O������ lastupdat ��Ƥ��e
    $old['last_update'] = $GLOBALS['THIS_TIME'];
    $old['last_updator'] = $GLOBALS['SCACHE']['ADMIN']['login_id'];
if (!$r_id = $r_order->add($old)) {		// revise �� table
    $op['msg'] = $r_order->msg->get(2);
    $layout->assign($op);
    $layout->display($TPL_ERROR);  		    
    break;	    
}

// ---- �g�J log�� [�[�J���� �B link���Ӫ� r_order �O��] ----
$argv2['order_num']	= $argv['order_num'];
$argv2['user']		= $argv['last_updator'];

    if ($argv['revise'] ==1){ 
            $original="original";
        }else{
            $R = $argv['revise']-1;
            $original="(REVISE:".$R.") ";
        }
$argv2['des']	= "\<span class=\'bgdy9\'\>revise from \<a href=\"javascript\:openRemote\(\'".$r_id."\'\)\;\">".$original."\<\/a\> to \:\(REVISE\:".$argv['revise']."\)\<\/span\>";

// ========= add to  order_log DB =======
if (!$order_log->add($argv2,"revise")) {
    $op['msg'] = $order_log->msg->get(2);
    $layout->assign($op);
    $layout->display($TPL_ERROR);  		    
    break;	    
}
//�W�[revise log		
$argv2['des'] = 'REVISE FOR - '.$PHP_des;
$order_log->add($argv2,"revise");

// ---- if status > 3 ====>  A. ���capaci ��   b. ��s��Ӫ�pation�� ETP_SU���----
if($old['status'] > 3){

// �g�J capaci  :::: �b distri-month_su ���g�J �� factory �� pre_schedule �]mode==1 ���
$etp_su = $order->get_field_value('etp_su','',$PHP_order_num,'pdtion');
$etp_su=decode_mon_yy_su($etp_su); //���X�v�s�betp-su�ñNsu�ȻP�~,����}
for ($i=0; $i<sizeof($etp_su); $i++) //�N�v�s�bsu���~�װO���ȧR��						 	
    $f1=$capaci->delete_su($old['factory'], $etp_su[$i]['year'], $etp_su[$i]['mon'], 'pre_schedule', $etp_su[$i]['su']);			 		
for($i=0; $i<sizeof($old['ps_id']); $i++) $order->update_partial('p_etp_su', '', $old['ps_id']);

if ($old['status'] > 6)
{
    $pdt_ord = $order->schedule_get(0,$PHP_order_num);  //���X�ӵ��O��
    // $schedule->del_ord_sch($PHP_order_num);
    $order->delete_month_su( $pdt_ord['ets'], $pdt_ord['etf'],$pdt_ord['su'],$pdt_ord['factory'],'schedule');
    $order->del_cfm_pd_schedule($pdt_ord['id']);
}


//��s��Ӫ�pation�� ----
$parm2['factory'] = $old['factory'];
$parm2['etp_su'] = '';
$parm2['fty_su'] = '';
$parm2['today'] = $TODAY;
$parm2['reviser'] = $GLOBALS['SCACHE']['ADMIN']['login_id'];


// 20060411 ��� �P�ɧ�s s_order.status�@�Ρ@ets, etf, fty_su, creator, opendate
if (!$order->revise_apv_ord($parm2, $old['order_num'])) {
    $op['msg'] = $order->msg->get(2);
    $layout->assign($op);
    $layout->display($TPL_ERROR);  		    
    break;	    
}



}

# �O���ϥΪ̰ʺA
$message = "REVISE order#:$PHP_order_num to (REVISE: ".$argv['revise'].")�E";
$log->log_add(0,"101R",$message);

$op['order'] = $order->get($PHP_id);  //���X�ӵ��O��

//�ק�wi��etd
$parm=array($op['order']['order_num'],'etd',$PHP_etd);
$wi->update_field_num($parm);

if (!$op['order']) {
    $op['msg'] = $order->msg->get(2);
    $layout->assign($op);
    $layout->display($TPL_ERROR);  		    
    break;
}

    // �ˬd �ۤ��O�_�s�b
if(file_exists($GLOBALS['config']['root_dir']."/picture/".$op['order']['order_num'].".jpg")){
    $op['main_pic'] = "./picture/".$op['order']['order_num'].".jpg";
} else {
    $op['main_pic'] = "./images/graydot.gif";
}

$op['msg'] = $order->msg->get(2);

//------------------------------

$allow = $admin->decode_perm('065');   // �]�w �s�W�R���v��


//   2005/08/31 ��Ѹ�Ʈw�� su [�H ie �p���J ]
$su = $op['order']['su'];
$op['order']['f_su'] = number_format($su, 0, '', ',');

// �p�� �����Ѽ� -------- �������R���� ------------
$op['order']['sales'] = number_format($sales, 2, '.', ',');
$op['order']['unit_cost'] = number_format($unit_cost, 2, '.', ',');
$op['order']['grand_cost'] = number_format($grand_cost, 2, '.', ',');
$op['order']['gm'] = number_format($gm, 2, '.', ',');
$op['order']['gm_rate'] = number_format($rate, 2, '.', ',');

// �p�� lead time
$op['lead_time'] = countDays ($op['order']['etp'],$op['order']['etd']);

$op['msg'][] = $message;

page_display($op, '065', $TPL_ORDER_REVISE_SHOW);
break;

		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "revise_order"
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "revise_order":
check_authority('065',"edit");

//-------------���X�ӵ��O��
$op['order'] = $order->get(0,$PHP_ord_num);  
if (!$op['order']) {
    $op['msg'] = $order->msg->get(2);
    $layout->assign($op);
    $layout->display($TPL_ERROR);  		    
    break;
}

//-------------creat ETD, ETP date combo box
//*****//2006.12.08	�ק�sample���U�Ԧ� start
$s_date=increceDaysInDate($TODAY,-1200); //12�Ӥ�e
// $where="where last_update > '".$s_date."'and cust='".$op['order']['cust']."' AND dept='".$op['order']['dept']."' order by num";
$where="where last_update > '".$s_date."'and cust='".$op['order']['cust']."' order by num";

$smpl_no=$smpl_ord->get_fields("num",$where);		
$j=0;
if($smpl_no){
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

$op['smpl_no']=$arry2->select($sml_no,$op['order']['smpl_ord'],'PHP_smpl_ord','select','');
//*****//2006.12.08	�ק�sample���U�Ԧ�	end		

// creat factory combo box
// creat factory combo box
$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
if ($user_dept=="HJ" || $user_dept=="LY")
{
    if($user_dept=="HJ") $fty = array($user_dept,'MA');
    if($user_dept=="LY") $fty = array($user_dept);
    $op['factory'] = $arry2->select($fty,$op['order']['factory'],'PHP_factory','select','');
//	$op['fac_flag']=1;
}else{
    $op['factory'] = $arry2->select($FACTORY,$op['order']['factory'],'PHP_factory','select','');  	
}

// creat style type combo box
$style_def = $style_type->get_fields('style_type');   // ���X �ڦ����O
$op['style'] =  $arry2->select($style_def,$op['order']['style'],'PHP_style','select','');  	
// creat apparel unit combo box
$op['unit'] = $arry2->select($APPAREL_UNIT,$op['order']['unit'],'PHP_unit','select','');  	

// �ˬd �ۤ��O�_�s�b
if(file_exists($GLOBALS['config']['root_dir']."/picture/".$op['order']['order_num'].".jpg")){
    $op['main_pic'] = "./picture/".$op['order']['order_num'].".jpg";
} else {
    $op['main_pic'] = "./images/graydot.gif";
}


//08.09.23 ��L�ۤ��[�J
$md = $op['order']['pic_num'] % 5;
if ($md > 0) $md--;
$op['order']['pic_num'] = $op['order']['pic_num'] + $md ;
if(!$op['order']['pic_num'])$op['order']['pic_num'] = 5;
for($i=0; $i< $op['order']['pic_num']; $i++)
{		
    if(file_exists($GLOBALS['config']['root_dir']."/picture/".$op['order']['order_num']."_".$i.".jpg")){
        $op['oth_pic'][$i] = "./picture/".$op['order']['order_num']."_".$i.".jpg";
    } else {
        $op['oth_pic'][$i] = "./images/graydot.gif";
    }
    $op['oth_num'][$i] = $i;
    if((($i+1) % 5 == 0) )
    {
        $op['oth_ll'][$i] =1;
    }else{
        $op['oth_ll'][$i] =0;
    }
}

$op['ttl_pic'] = $i-1;

$op['msg'] = $order->msg->get(2);
$where_str="ORDER BY cust_s_name"; //��cust_s_name�Ƨ�
$cust_def = $cust->get_fields('cust_init_name',$where_str);
$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//���X�Ȥ�N��
for ($i=0; $i< sizeof($cust_def); $i++)
{
    $cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//�H [�Ȥ�N��-�Ȥ�²��] �e�{
}
$op['agent_select'] =  $arry2->select($cust_value,$op['order']['agent'],'PHP_agent','select','',$cust_def_vue); 


$op['new_revise'] = $op['order']['revise'] +1;
$op['search'] = '1';

//�Ͳ��u���O�k�k�� -- 08.08.27
$line_value = array('F','M','A');
$line_key = array('Woman','Man','Ann');
$op['line_select'] =  $arry2->select($line_key,$op['order']['line_sex'],'PHP_line','select','',$line_value); 
//�D�Ƴ��--08.09.22
$op['lots_unit'] =  $arry2->select($LOTS_PRICE_UNIT,$op['order']['lots_unit'],'PHP_lots_unit','select',''); 

//�~�שu�`		
$op['season_select'] =  $arry2->select($SEASON,$op['order']['season'],'PHP_season','select',''); 
$op['year_select'] =  $arry2->select($YEAR_WORK,$op['order']['syear'],'PHP_syear','select',''); 

page_display($op, '065', $TPL_ORDER_REVISE);
break;		
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#			case "order_edit"
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "order_edit":	
check_authority('065',"edit");			
$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];	
$op['order'] = $order->get($PHP_id);
if (!$op['order']) {
    $op['msg'] = $order->msg->get(2);
    $layout->assign($op);
    $layout->display($TPL_ERROR);  		    
    break;
}
// �d�ݬO���O�Ӧ۷s�W�q�� �᪺�I�s???   ��s�W�� �|show�� html �]�i edit ~~ �D�Ӧ�list


//*****//2006.12.08	�ק�sample���U�Ԧ� start
$s_date=increceDaysInDate($TODAY,-1200); //12�Ӥ�e
// $where="where last_update > '".$s_date."'and cust='".$op['order']['cust']."' AND dept='".$op['order']['dept']."' order by num";
$where="where last_update > '".$s_date."'and cust='".$op['order']['cust']."' order by num";

$smpl_no=$smpl_ord->get_fields("num",$where);		
$j=0;
if($smpl_no){									//�Nsample order��Ƥ���"-x"����������"-x"
    if(strlen($op['order']['smpl_ord']) > 9)$op['order']['smpl_ord']=substr($op['order']['smpl_ord'],0,-2);	
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
    #M11020803 �ק令�Ͳ��e���i�ק� sample order �ҥH���O�_�ӡA�� html �P�_
    #if ($op['order']['smpl_ord'])	$op['sup_null']=1;	//�S��sample order���ϥΪ̦��o����sample order��
}


$op['smpl_no']=$arry2->select($sml_no,$op['order']['smpl_ord'],'PHP_smpl_ord','select','');
//*****//2006.12.08	�ק�sample���U�Ԧ�	end
#M11020803 �ק令�Ͳ��e���i�ק� sample order �ҥH���O�_�ӡA�� html �P�_
#if ($op['order']['ie1'] > 0) $op['ie_flag']=1; //�P�_�O�_��IE��
// creat factory combo box

#M11010501
if ($user_dept=="HJ" || $user_dept=="LY"){
    if($user_dept=="HJ") $fty = array($user_dept,'MA');
    if($user_dept=="LY") $fty = array($user_dept);
    // if($user_dept=="DA") $fty = array('CF');
    $op['factory'] = $arry2->select($fty,$op['order']['factory'],'PHP_factory','select','');
    // $op['fac_flag']=0;
}else{
    $op['factory'] = $arry2->select($FACTORY,$op['order']['factory'],'PHP_factory','select','');  	
}

// creat style type combo box
$style_def = $style_type->get_fields('style_type');   // ���X �ڦ����O
$op['style'] =  $arry2->select($style_def,$op['order']['style'],'PHP_style','select','');  	
// creat apparel unit combo box
$op['unit'] = $arry2->select($APPAREL_UNIT,$op['order']['unit'],'PHP_unit','select','');  	

// �ˬd �ۤ��O�_�s�b
if(file_exists($GLOBALS['config']['root_dir']."/picture/".$op['order']['order_num'].".jpg")){
    $op['main_pic'] = "./picture/".$op['order']['order_num'].".jpg";
} else {
    $op['main_pic'] = "./images/graydot.gif";
}

//��L�ۤ��[�J
$md = $op['order']['pic_num'] % 5;
if ($md > 0) $md--;
$op['order']['pic_num'] = $op['order']['pic_num'] + $md ;
if(!$op['order']['pic_num'])$op['order']['pic_num'] = 5;
for($i=0; $i< $op['order']['pic_num']; $i++){
    if(file_exists($GLOBALS['config']['root_dir']."/picture/".$op['order']['order_num']."_".$i.".jpg")){
        $op['oth_pic'][$i] = "./picture/".$op['order']['order_num']."_".$i.".jpg";
    } else {
        $op['oth_pic'][$i] = "./images/graydot.gif";
    }
    $op['oth_num'][$i] = $i;
    if((($i+1) % 5 == 0) ){
        $op['oth_ll'][$i] =1;
    }else{
        $op['oth_ll'][$i] =0;
    }
}
$op['ttl_pic'] = $i-1;



$where_str="ORDER BY cust_s_name"; //��cust_s_name�Ƨ�
$cust_def = $cust->get_fields('cust_init_name',$where_str);
$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//���X�Ȥ�N��
for ($i=0; $i< sizeof($cust_def); $i++){
    $cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//�H [�Ȥ�N��-�Ȥ�²��] �e�{
}
$op['agent_select'] =  $arry2->select($cust_value,$op['order']['agent'],'PHP_agent','select','',$cust_def_vue); 

//�Ͳ��u���O�k�k�� -- 08.08.27
$line_value = array('F','M','A');
$line_key = array('Woman','Man','Ann');
$op['line_select'] =  $arry2->select($line_key,$op['order']['line_sex'],'PHP_line','select','',$line_value); 
//�D�Ƴ��--08.09.22
$op['lots_unit'] =  $arry2->select($LOTS_PRICE_UNIT,$op['order']['lots_unit'],'PHP_lots_unit','select',''); 

//�~�שu�`		
$op['season_select'] =  $arry2->select($SEASON,$op['order']['season'],'PHP_season','select',''); 
$op['year_select'] =  $arry2->select($YEAR_WORK,$op['order']['syear'],'PHP_syear','select',''); 

$op['msg'] = $order->msg->get(2);

$op['search'] = '1';
page_display($op, '065', $TPL_ORDER_EDIT);		
break;
		
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "do_order_edit":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_order_edit":
check_authority('065',"edit");
		
$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
$op['order'] = $order->get($PHP_id);
$PHP_old_order="";

$PHP_old_order="no_order";
$old_smpl_ord="";
$smpl=$smpl_ord->get(0,0,$PHP_smpl_ord);

#M11020803 �ק� �j�f�q��Ͳ��e�i�ק� sample order
$PHP_num = $smpl['num'];
$PHP_style = $smpl['style'];
$PHP_style_num =$smpl['ref'];
$PHP_apv_date=$smpl['apv_date'];		
$PHP_style_n = $PHP_style;
$PHP_spt = $op['order']['ie_time1'];
$PHP_ie = $op['order']['ie1'];

$PHP_orders="";
// �A�p�� su ����........
$PHP_su =  number_format(($PHP_qty * $PHP_ie),0,'','');
if ($PHP_ie>0){$PHP_status=1;}else{$PHP_status=0;}
$PHP_ie_time2=$op['order']['ie_time2'];

$PHP_etd = '0000-00-00'; $PHP_etp='9999-99-99';
$PHP_qty = 0;

# Partial
foreach($PHP_ps_qty as $key => $value)
{
    $etd_name   = 'PHP_etd_'.$key;
    $etp_name   = 'PHP_etp_'.$key;
    $remark  = 'PHP_remark_'.$key;
    if($PHP_ps_qty[$key] > 0 && $$etd_name && $$etp_name )
    {
        $PHP_qty        +=  $PHP_ps_qty[$key];				
        $ps_etd[]       =   $$etd_name;
        $ps_etp[]       =   $$etp_name;
        $ps_qty[]       =   $PHP_ps_qty[$key];
        $ps_id[]    	=	$PHP_ps_id[$key];
        $ps_su[]        =   number_format(($PHP_ps_qty[$key] * $PHP_ie),0,'','');
        $ps_remark[]    =   $$remark;
        if($$etd_name > $PHP_etd)$PHP_etd = $$etd_name;
        if($$etp_name < $PHP_etp)$PHP_etp = $$etp_name;
    }
    
}

$argv = array(
"id"			    =>	$PHP_id,
"order_num"			=>	$PHP_order_num,
"dept"				=>	$PHP_dept,
"ref"				=>	$PHP_ref,
"factory"			=>	$PHP_factory,
"style"				=>	$PHP_style_n,
"qty"				=>	$PHP_qty,
"unit"				=>	$PHP_unit,
"style_num"			=>	$PHP_style_num,
"patt_num"			=>	$PHP_patt_num,
"uprice"			=>	$PHP_uprice,
"quota"				=>	$PHP_quota,
"ie1"				=>	$PHP_ie1,
"old_num"			=>	$old_smpl_ord,
"old_orders"		=>	$PHP_old_order,
"smpl_ord"			=>	$PHP_smpl_ord,
"num"				=>	$PHP_num,
"smpl_apv"			=>	$PHP_apv_date,
"ie1"				=>	$PHP_ie,
"ie_time1"			=>	$PHP_spt,
"ie_time2"			=>	$PHP_ie_time2,
"su"				=>	$PHP_su,
"orders"			=>	$PHP_orders,
"status"			=>	$PHP_status,
"mat_u_cost"		=>	$PHP_mat_u_cost,
"mat_useage"		=>	$PHP_mat_useage,
"acc_u_cost"		=>	$PHP_acc_u_cost,
"quota_fee"			=>	$PHP_quota_fee,
"comm_fee"			=>	$PHP_comm_fee,
"cm"				=>	$PHP_cm,
"smpl_fee"			=>	$PHP_smpl_fee,
"pic"				=>	$PHP_pic,
"pic_upload"		=>	$PHP_pic_upload,
"emb"				=>	$PHP_emb,
"wash"				=>	$PHP_wash,
"oth"				=>	$PHP_oth,
"oth_treat"			=>	$PHP_oth_treat,
"etd"				=>	$PHP_etd,
"etp"				=>	$PHP_etp,						
"last_updator"		=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
"agent"				=>	$PHP_agent,
"fusible"			=>	$PHP_fus_u_cost,
"interline"			=>	$PHP_int_u_cost,
"line_sex"			=>	$PHP_line,
"lots_unit"			=>	$PHP_lots_unit,
"oth_pic"			=>	$PHP_oth_pic,
"oth_pic_upload"	=> 	$PHP_oth_pic_upload,
"pic_num"			=>	$PHP_oth_pic_num,
"season"			=>	$PHP_season,
"syear"				=>	$PHP_syear,
"ps_etd"			=>	$ps_etd,
"ps_etp"			=>	$ps_etp,
"ps_qty"			=>	$ps_qty,
"ps_su"				=>	$ps_su,
"ps_id"				=>	$ps_id,
"ps_remark"			=>	$ps_remark,
"ps_num"			=>	sizeof($ps_qty)						
);

# �P�_��J���L���D
$check = $order->check($argv);
if (!$check) {  
    $op['msg']= $order->msg->get(2);
    $op['order'] = $order->get($PHP_id);
    if (!$op['order']) {
        $op['msg'] = $order->msg->get(2);
        $layout->assign($op);
        $layout->display($TPL_ERROR);  		    
        break;
    }

    $etd = apart_date(0,$op['order']['etd']);
    $etp = apart_date(0,$op['order']['etp']);

    $s_date=increceDaysInDate($TODAY,-1200); //12�Ӥ�e
    // $where="where last_update > '".$s_date."'and cust='".$op['order']['cust']."' AND dept='".$op['order']['dept']."' order by num";
    $where="where last_update > '".$s_date."'and cust='".$op['order']['cust']."' order by num";

    $op['sup_null']=1;
            
    if ($user_dept=="HJ" || $user_dept=="LY"){
        if($user_dept=="HJ") $fty = array($user_dept,'MA');
        if($user_dept=="LY") $fty = array($user_dept);
        $op['factory'] = $arry2->select($fty,$op['order']['factory'],'PHP_factory','select','');
    }else{
        $op['factory'] = $arry2->select($FACTORY,$op['order']['factory'],'PHP_factory','select','');
    }

    $style_def = $style_type->get_fields('style_type');   // ���X �ڦ����O
    $op['style'] =  $arry2->select($style_def,$op['order']['style'],'PHP_style','select','');
    $op['unit'] = $arry2->select($APPAREL_UNIT,$op['order']['unit'],'PHP_unit','select','');

    // �ˬd �ۤ��O�_�s�b
    if(file_exists($GLOBALS['config']['root_dir']."/picture/".$op['order']['order_num'].".jpg")){
        $op['main_pic'] = "./picture/".$op['order']['order_num'].".jpg";
    } else {
        $op['main_pic'] = "./images/graydot.gif";
    }

    $md = $op['order']['pic_num'] % 5;
    if ($md > 0) $md--;
    $op['order']['pic_num'] = $op['order']['pic_num'] + $md ;
    if(!$op['order']['pic_num'])$op['order']['pic_num'] = 5;
    for($i=0; $i< $op['order']['pic_num']; $i++)
    {
        if(file_exists($GLOBALS['config']['root_dir']."/picture/".$op['order']['order_num']."_".$i.".jpg")){
            $op['oth_pic'][$i] = "./picture/".$op['order']['order_num']."_".$i.".jpg";
        } else {
            $op['oth_pic'][$i] = "./images/graydot.gif";
        }
        $op['oth_num'][$i] = $i;
        if((($i+1) % 5 == 0) )
        {
            $op['oth_ll'][$i] =1;
        }else{
            $op['oth_ll'][$i] =0;
        }
    }
    $op['ttl_pic'] = $i-1;

    $where_str="ORDER BY cust_s_name"; //��cust_s_name�Ƨ�
    $cust_def = $cust->get_fields('cust_init_name',$where_str);
    $cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//���X�Ȥ�N��
    for ($i=0; $i< sizeof($cust_def); $i++)
    {
        $cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//�H [�Ȥ�N��-�Ȥ�²��] �e�{
    }
    $op['agent_select'] =  $arry2->select($cust_value,$op['order']['agent'],'PHP_agent','select','',$cust_def_vue);

    //�Ͳ��u���O�k�k�� -- 08.08.27
    $line_value = array('F','M','A');
    $line_key = array('Woman','Man','Ann');
    $op['line_select'] =  $arry2->select($line_key,$op['order']['line_sex'],'PHP_line','select','',$line_value);
    //�D�Ƴ��--08.09.22
    $op['lots_unit'] =  $arry2->select($LOTS_PRICE_UNIT,$op['order']['lots_unit'],'PHP_lots_unit','select','');

    //�~�שu�`
    $op['season_select'] =  $arry2->select($SEASON,$op['order']['season'],'PHP_season','select','');
    $op['year_select'] =  $arry2->select($YEAR_WORK,$op['order']['syear'],'PHP_syear','select','');


    $op['msg'] = $order->msg->get(2);
    page_display($op, '065', $TPL_ORDER_EDIT);
    break;
}

# 2013/07/11 �N���ѥH�᪺�q�檺 handling_fee �g�^ s_order ��(factory = LY �B �ư� L �}�Y���q��)
# 2013/07/11 �ѩ� �uú �ثe�O��ʬD���q�A�ۦ�[�J handling_fee�C�令 �uú �h�@�� �N handling_fee �ǤJ�p��(2013/07/11�{����s��ҷs�إߪ��q��~�ǤJ�p��)
# 2013/07/11 ���ѥH�e�� handling_fee ��줣��ȡA��ε{���P�_2012/08/15����Nhandling_fee�]��0.25�A�ت��O���e�x���(order list�Border show�Border cfm...��)���`
if($argv['factory'] == "LY" and $PHP_opendate >= "2013-07-11" and substr($argv['order_num'],0,1) <> "L"){
	$argv['handling_fee'] = $argv['handling_fee'];
}elseif($argv['factory'] == "LY" and $PHP_opendate >= "2012-08-15" and substr($argv['order_num'],0,1) <> "L"){
	$handling_row = $order->get_fields("set_value", " where set_name='handling_fee'","para_set");
	$argv['handling_fee'] = $handling_row[0];
}else{
	$argv['handling_fee'] = 0.00;
}

// �p��  gm rate -----------------------------
$unit_cost = ($argv['mat_u_cost']* $argv['mat_useage'])+ $argv['interline']+ $argv['fusible']+ $argv['acc_u_cost'] + $argv['quota_fee'] + $argv['comm_fee'] + $argv['cm'] + $argv['emb'] + $argv['wash'] + $argv['oth'] + $argv['handling_fee'];
$grand_cost = $unit_cost*$argv['qty'] + $argv['smpl_fee'];
$sales = $argv['uprice']*$argv['qty'];
$gm = $sales - $grand_cost;
if ($sales){
    $rate = ( $gm / $sales ) * 100 ;
}

$argv['gmr'] = number_format($rate, 2, '.', ',');
$argv['su'] = $PHP_qty * $PHP_ie1;

// ========= add to DB ======= 
if (!$order->edit($argv)) {
    $op['msg'] = $order->msg->get(2);
    $layout->assign($op);
    $layout->display($TPL_ERROR);  		    
    break;	    
}

# �O���ϥΪ̰ʺA
$message = "Edit Order record:[$PHP_order_num]";
$log->log_add(0,"101E",$message);

$back_str ="index2.php?PHP_action=order_view&PHP_id=".$PHP_id."&PHP_msg=".$message;
redirect_page($back_str);   	    



#====================================================
#		 case "do_order_rmk":
#====================================================
case "do_order_rmk":
		
		$argv2['order_num']	= $PHP_order_num;
		$argv2['user']			= $GLOBALS['SCACHE']['ADMIN']['login_id'];
		$argv2['des'] 			= $PHP_des;
		$order_log->add($argv2,'user_log');
		$message ="SUCCESS ADD MESSAGE ON ORDER :".$PHP_order_num;

		$back_str ="index2.php?PHP_action=order_view&PHP_id=".$PHP_id."&PHP_msg=".$message;
		redirect_page($back_str); 

break;	
		
		
#====================================================
# case "order_log_del":
#====================================================
case "order_log_del":
		
		$order_log->del($PHP_id);
		

		$back_str ="index2.php?PHP_action=order_view&PHP_id=".$PHP_ord_id;
		redirect_page($back_str); 

break;			
		
		
#====================================================
# case "do_order_rmk_edit"
#====================================================
case "do_order_rmk_edit":
		

		
		$parm['field_name'] = 'des';
		$parm['field_value'] = $PHP_des;
		$parm['id'] = $PHP_id;
		$order_log->update_field($parm);
		

		$back_str ="index2.php?PHP_action=order_view&PHP_id=".$PHP_ord_id;
		redirect_page($back_str); 

break;					


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#			case "do_etd_edit"	2007-05-18�W�[ETD�i�b����ɭԭק�
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_etd_edit":	//2007-05-18�W�[ETD�i�b����ɭԭק�
check_authority('065',"edit");			

if ($PHP_etds < $TODAY)	//�ˬdETD�O�_�j�󤵤�
{
    $message=$PHP_etds."Sorry ! please update your unreasonable ETD (After today at least)�C";
}else if($PHP_etds < $PHP_etp){	//�ˬdETD�O�_�j��ETP
    $message="ERROR ! ETD < ETP�C";
}else{
    $order->update_partial('p_etd', $PHP_etds, $PHP_ps_id);
    // $order->update_partial('p_etp', $PHP_etp, $PHP_ps_id);
    $order->update_partial('remark', $PHP_remarks, $PHP_ps_id);
    if ($PHP_status > 1)	//�T�{�O�_�ݭn�ק�C��SU�O��
    {
         $where_str=' WHERE id = '.$PHP_ps_id;
         $etp_su = $order->get_fields('p_etp_su', $where_str,'order_partial');				 
         $etp_su=decode_mon_yy_su($etp_su[0]); //���X�v�s�betp-su�ñNsu�ȻP�~,����}
         for ($i=0; $i<sizeof($etp_su); $i++) //�N�v�s�bsu���~�װO���ȧR��
         {				 	
            $f1=$capaci->delete_su($PHP_fty, $etp_su[$i]['year'], $etp_su[$i]['mon'], 'pre_schedule', $etp_su[$i]['su']);			 		
         }	
         $div = $order->distri_month_su($PHP_su,$PHP_etp,$PHP_etds,$PHP_fty,'pre_schedule'); //����C��SU���x�s
         $order->update_partial('p_etp_su', $div, $PHP_ps_id);		
         $order->update_distri_su($PHP_ord_num,'p_etp_su','etp_su');

//				 $ord_pdt=$order->get_pdtion($PHP_ord_num, $PHP_fty);
//				 $etp_su=decode_mon_yy_su($ord_pdt['etp_su']); //���X�v�s�betp-su�ñNsu�ȻP�~,����}
//
//				 $div = $order->distri_month_su($PHP_su,$PHP_etp,$PHP_etds,$PHP_fty,'pre_schedule'); //����C��SU���x�s
//				 $order->update_pdtion_field('etp_su', $div, $ord_pdt['id']);  //�x�s�setp_su
    }
            
    $ord_rec = $order->get($PHP_id);
    $PHP_etds = '0000-00-00'; $PHP_etp='9999-99-99';			
    foreach($ord_rec['ps_etd'] as $key => $value)
    {
        if($ord_rec['ps_etd'][$key] > $PHP_etds)$PHP_etds = $ord_rec['ps_etd'][$key];
        if($ord_rec['ps_etp'][$key] < $PHP_etp)$PHP_etp = $ord_rec['ps_etp'][$key];
    }
    
    $order->update_field('etd',$PHP_etds,$PHP_id);
    $order->update_field('etp',$PHP_etp,$PHP_id);
    
    #M110428 partial �ɶ� wi �]�@�֭ק� �Ȯɥ��@�_��A���T�w�S�����p�ʳs����R���A�]������ƻP�q�檺�ۦP
    $str_etd = array($ord_rec['order_num'],'etd',$PHP_etds);
    $wi->update_field_num($str_etd);
    
    $order->update_field('last_update',$TODAY,$PHP_id);
    $order->update_field('last_updator',$GLOBALS['SCACHE']['ADMIN']['login_id'],$PHP_id);
    // $message="Successfully edit REMARK and ETD and ETP on order :".$PHP_ord_num;
    $message="Successfully edit REMARK and ETD on order :".$PHP_ord_num;
    $log->log_add(0,"101E",$message);

}

$back_str ="index2.php?PHP_action=order_view&PHP_id=".$PHP_id."&PHP_msg=".$message;
redirect_page($back_str);  


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#			case "order_apvl"
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	case "order_apvl":
		check_authority('065',"edit");
		
		
		if (!$U = $order->update_field_num("smpl_apv",$TODAY,$PHP_num)){//�u�糧�i�q��[�Jsmpl_apv
				$op['msg'] = $smpl_ord->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
		if ($PHP_smpl_num) //�P�_�O�_���˫~��s��
		{
			$smpl=$smpl_ord->get(0,0,$PHP_smpl_num);		
			$parm['num']		= $PHP_smpl_num;
			$parm['field_name'] = 'apv_date';
			$parm['field_value']= $TODAY;
			$order_num=explode("|",$smpl['orders']);		
//*****//2006.12.19�W�[order��smpl_approval start
		$U = $order->update_field_like("smpl_apv", $TODAY, 'smpl_ord', $PHP_smpl_num);
	//	$U = $order->update_field_like("ie_time2", $smpl['spt'], 'smpl_ord', $PHP_smpl_num);

//*****//2006.12.19�W�[order��smpl_approval end
		  if($smpl['apv_date'] == '0000-00-00')
		  {
		 		 $U = $smpl_ord->update_apv($parm);
		  }
			$message = " Sample APPROVAL for :[".$PHP_smpl_num."] IN order : ".$PHP_num;
			# �O���ϥΪ̰ʺA
			$log->log_add(0,"21A",$message);
			$log->log_add(0,"101E",$message);
		}else{ //�S���˥����			
			$message = " Sample APPROVAL IN order : ".$PHP_num;
			# �O���ϥΪ̰ʺA			
			$log->log_add(0,"101E",$message);
		}

		$redirect_str ="index2.php?PHP_action=order_view&cgiget=index2.php?PHP_action=order_search&PHP_id=".$PHP_id."&PHP_msg=".$message;
		redirect_page($redirect_str); 		    	    
	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		job 24V sample OUTPUT view
#		case "order_pic_edit":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++	
	case "order_pic_edit":	
		check_authority('065',"edit");			
		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];	
		$op['order'] = $order->get($PHP_id);
		if (!$op['order']) {
			$op['msg'] = $order->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$op = $order->orgainzation_ord($op);


		// �ˬd �ۤ��O�_�s�b
		if(file_exists($GLOBALS['config']['root_dir']."/picture/".$op['order']['order_num'].".jpg")){
			$op['main_pic'] = "./picture/".$op['order']['order_num'].".jpg";
		} else {
			$op['main_pic'] = "./images/graydot.gif";
		}

//08.09.23 ��L�ۤ��[�J

	$md = $op['order']['pic_num'] % 5;
	if ($md > 0) $md--;
	$op['order']['pic_num'] = $op['order']['pic_num'] + $md ;
	if(!$op['order']['pic_num'])$op['order']['pic_num'] = 5;
	for($i=0; $i< $op['order']['pic_num']; $i++)
	{		
		if(file_exists($GLOBALS['config']['root_dir']."/picture/".$op['order']['order_num']."_".$i.".jpg")){
			$op['oth_pic'][$i] = "./picture/".$op['order']['order_num']."_".$i.".jpg";
		} else {
			$op['oth_pic'][$i] = "./images/graydot.gif";
		}
		$op['oth_num'][$i] = $i;
		if((($i+1) % 5 == 0) )
		{
			$op['oth_ll'][$i] =1;
		}else{
			$op['oth_ll'][$i] =0;
		}
	}
	$op['ttl_pic'] = $i-1;

	page_display($op, '065', $TPL_ORDER_PIC_EDIT);		
	break;
		



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#			case "do_order_pic_edit":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	case "do_order_pic_edit":
		check_authority('065',"edit");			

		$argv = array(	"id"							=>	$PHP_id,
										"order_num"				=>	$PHP_order_num,
										"oth_pic"					=>	$PHP_oth_pic,
										"oth_pic_upload"	=> 	$PHP_oth_pic_upload,
										'pic_num'					=>	$PHP_oth_pic_num,
						);


		// ========= add to DB =======
		if (!$order->edit_picture($argv)) {
			$op['msg'] = $order->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;	    
		}
					# �O���ϥΪ̰ʺA
		$message = "Edit Order Picture:[$PHP_order_num]�E";
		$log->log_add(0,"101E",$message);

		$back_str ="index2.php?PHP_action=order_view&PHP_id=".$PHP_id."&PHP_msg=".$message;
		redirect_page($back_str);   	





#-------------------------------------------------------------------------------------
#			case "edit_order_season":
#-------------------------------------------------------------------------------------
    case "edit_order_season":
		$op['season_select'] =  $arry2->select($SEASON,$op['order']['season'],'PHP_season','select',''); 
		$op['year_select'] =  $arry2->select($YEAR_WORK,$op['order']['syear'],'PHP_syear','select',''); 
		if(!$PHP_season || !$PHP_syear)
		{
			$message = "Please select season and year first!";
		}else{
			$f1 = $order->update_field('season', $PHP_season, $PHP_id);
			$f1 = $order->update_field('syear', $PHP_syear, $PHP_id);
			$message = "Successfully edit Order season:[$PHP_ord_num]�E";
			$log->log_add(0,"101E",$message);

		}
		$back_str ="index2.php?PHP_action=order_view&PHP_id=".$PHP_id."&PHP_msg=".$message;
		redirect_page($back_str);   	
		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#			case "stock_ord_add"
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

    case "stock_ord_add":
		check_authority('065',"add");
		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];   // �P�w�i�J����������
		
		$op['order'] = $order->get($PHP_id);
		$op['order']['qty'] = $PHP_qty;
		$op['cust_id'] = 'GV';
		// ���X�~�X...
		$dt = decode_date(1);
		$year_code = substr($dt['year'],-1);
		
		//�˥��������w
		$op['smpl_no']='<input type="text" name="PHP_smpl_ord" size="15" class=select value="'.$op['order']['smpl_ord'].'">';

		//�u�t���w
		$op['factory'] = $arry2->select($FACTORY,$op['order']['factory'],'PHP_factory','select','');

		// creat style type combo box
		$style_def = $style_type->get_fields('style_type');   // ���X �ڦ����O
		$op['style'] =  $arry2->select($style_def,$op['order']['style'],'PHP_style','select','');  	
		// creat apparel unit combo box
		$op['unit'] = $arry2->select($APPAREL_UNIT,$op['order']['unit'],'PHP_unit','select','');  	

		$where_str="ORDER BY cust_s_name"; //��cust_s_name�Ƨ�
		$cust_def = $cust->get_fields('cust_init_name',$where_str);
		$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//���X�Ȥ�N��
		for ($i=0; $i< sizeof($cust_def); $i++)
		{
			$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//�H [�Ȥ�N��-�Ȥ�²��] �e�{
		}
		$op['agent_select'] =  $arry2->select($cust_value,'','PHP_agent','select','',$cust_def_vue); 
		
		
//�Ͳ��u���O�k�k�� -- 08.08.27
		$line_value = array('F','M','A');
		$line_key = array('Woman','Man','Ann');
		$op['line_select'] =  $arry2->select($line_key,$op['order']['line_sex'],'PHP_line','select','',$line_value); 
//�D�Ƴ��--08.09.22
		$op['lots_unit'] =  $arry2->select($LOTS_PRICE_UNIT,$op['order']['lots_unit'],'PHP_lots_unit','select',''); 

//�~�שu�`		
		$op['season_select'] =  $arry2->select($SEASON,$op['order']['season'],'PHP_season','select',''); 
		$op['year_select'] =  $arry2->select($YEAR_WORK,$op['order']['syear'],'PHP_syear','select',''); 


		// ���X�ﶵ��� �ζǤJ���Ѽ�
		$op['dept_id']	 = $op['order']['dept'];
		// pre  �q��s��....
		$op['order_precode'] = substr($op['order']['dept'],1,1).'GV'.$year_code."-xxx";
		$op['stock_mk'] = 1;
		$op['org_ord_num'] = $op['order']['order_num'];
		$op['stk_des'] = $PHP_color_qty;
page_display($op, '065', $TPL_ORDER_ADD);
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "do_order_add":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_order_add":
check_authority('065',"add");

# �uú���o
$FTY_CM = $_SESSION['FTY_CM'];
$ORG_CM = $FTY_CM[$PHP_factory];

$dt = decode_date(1);

// smpl_type
$smpl=$smpl_ord->get(0,0,$PHP_smpl_ord);
$PHP_num = $smpl['num'];
$PHP_style = $smpl['style'];
$PHP_style_num =$smpl['ref'];
$PHP_apv_date=$smpl['apv_date'];

if(!$PHP_spt)$PHP_spt=$smpl['spt'];
$PHP_ie = number_format(($PHP_spt/$GLOBALS['IE_TIME']),2,'.','');

$PHP_orders=$smpl['orders'];

// �p�� su ����
$PHP_su =  number_format(($PHP_qty * $PHP_ie),0,'','');
if ($PHP_apv_date=='0000-00-00'){$PHP_ie_time2=0;}else{$PHP_ie_time2=$PHP_spt;}
	
$PHP_etd = '0000-00-00'; $PHP_etp='9999-99-99';
$PHP_qty = 0;
foreach($PHP_ps_qty as $key => $value)
{
    $etd_name = 'PHP_etd_'.$key;
    $etp_name = 'PHP_etp_'.$key;
    $remark = 'PHP_remark_'.$key;
    if($PHP_ps_qty[$key] > 0 && $$etd_name && $$etp_name)
    {
        $PHP_qty        +=  $PHP_ps_qty[$key];				
        $ps_etd[]       =   $$etd_name;
        $ps_etp[]       =   $$etp_name;
        $ps_remark[]    =   $$remark;
        $ps_qty[]       =   $PHP_ps_qty[$key];
        $ps_su[]        =   number_format(($PHP_ps_qty[$key] * $PHP_ie),0,'','');
        if($$etd_name > $PHP_etd)$PHP_etd = $$etd_name;
        if($$etp_name < $PHP_etp)$PHP_etp = $$etp_name;
    }
}

$parm = array(
    "dept"			=>	$PHP_dept,
    "cust"			=>	$PHP_cust,
    "ref"			=>	$PHP_ref,
    "factory"		=>	$PHP_factory,
    "style"			=>	$PHP_style,
    "qty"			=>	$PHP_qty,
    "unit"			=>	$PHP_unit,
    "style_num"		=>	$PHP_style_num,
    "patt_num"		=>	$PHP_patt_num,
    "smpl_ord"		=>	$PHP_smpl_ord,
    "uprice"		=>	$PHP_uprice,
    "quota"			=>	$PHP_quota,


    "num"			=>	$PHP_num,
    "smpl_apv"		=>	'',
    "ie1"			=>	$PHP_ie,
    "ie_time1"		=>	$PHP_spt,
    "ie_time2"		=>	$PHP_ie_time2,
    "su"			=>	$PHP_su,
    "orders"		=>	$PHP_orders,						

    "mat_u_cost"	=>	$PHP_mat_u_cost,
    "mat_useage"	=>	$PHP_mat_useage,
    "acc_u_cost"	=>	$PHP_acc_u_cost,
    "quota_fee"		=>	$PHP_quota_fee,
    "comm_fee"		=>	$PHP_comm_fee,
    "cm"			=>	$PHP_cm,
    "smpl_fee"		=>	$PHP_smpl_fee,

    "etd"			=>	$PHP_etd,
    "etp"			=>	$PHP_etp,						

    "pic"			=>	$PHP_pic,
    "pic_upload"	=>	$PHP_pic_upload,

    "emb"			=>	$PHP_emb,
    "wash"			=>	$PHP_wash,
    "oth"			=>	$PHP_oth,
    "oth_treat"		=>	$PHP_oth_treat,

    "creator"		=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
    "open_date"		=>	$dt['date'],				

    "agent"			=> 	$PHP_agent,
    "fusible"		=>	$PHP_fus_u_cost,
    "interline"		=>	$PHP_int_u_cost,
    "offer"			=>	$PHP_offer,
	"org_cm"		=>	$ORG_CM,
    "line_sex"		=>	$PHP_line,
    "lots_unit"		=>	$PHP_lots_unit,

    "oth_pic"		=>	$PHP_oth_pic,
    "oth_pic_upload"=>	$PHP_oth_pic_upload,

    "cust_po"		=>	'',							
    "season"		=>	$PHP_season,
    "s_year"		=>	$PHP_syear,

    "ps_etd"		=>	$ps_etd,
    "ps_etp"		=>	$ps_etp,
    "ps_qty"		=>	$ps_qty,
    "ps_remark"		=>	$ps_remark,
    "ps_num"		=>	sizeof($ps_qty)
);
	
$op['order'] = $parm;

if (!$PHP_stk_mk && !$f1 = $order->check($parm)) {  // ��J��� �����T��
    $op['msg'] = $order->msg->get(2);

    $where_str="ORDER BY cust_s_name"; //��cust_s_name�Ƨ�
    $cust_def = $cust->get_fields('cust_init_name',$where_str);
    $cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//���X�Ȥ�N��
    for ($i=0; $i< sizeof($cust_def); $i++){
        $cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//�H [�Ȥ�N��-�Ȥ�²��] �e�{
    }
    $op['agent_select'] =  $arry2->select($cust_value,$PHP_agent,'PHP_agent','select','',$cust_def_vue); 

    // �C�X �s�W �q�檺���� ---------------------------------------------
    $op['cust_id'] = $PHP_cust;
    $op['dept_id'] = $PHP_dept;			
    // ���X�~�X...
    $dt = decode_date(1);
    $year_code = substr($dt['year'],-2);

    //sample���U�Ԧ� start
    $s_date=increceDaysInDate($TODAY,-1200); //12�Ӥ�e
    $where="where last_update > '".$s_date."' and cust='".$PHP_cust."' and dept='".$PHP_dept."'order by num";

    $smpl_no=$smpl_ord->get_fields("num",$where);		
    $j=0;
    if ($smpl_no){
        $sml_no[0]=substr($smpl_no[0],0,9);
        for($i=1;$i< sizeof($smpl_no); $i++){
            if($sml_no[$j] != substr($smpl_no[$i],0,9)){	
                $j++;
                $sml_no[$j]=substr($smpl_no[$i],0,9);
            }
        }
    } else {
        $sml_no=array('');
    }
    
    $op['smpl_no']=$arry2->select($sml_no,$PHP_smpl_ord,'PHP_smpl_ord','select','');
    //sample���U�Ԧ�	end

    // pre  �q��s��....
    $op['order_precode'] = substr($PHP_dept,1,1).$PHP_cust.$year_code."-xxxx";
    // creat unit combo box
    $op['unit'] = $arry2->select($APPAREL_UNIT,$parm['unit'],'PHP_unit','select','');  	
    // creat factory combo box
    $user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];   // �P�w�i�J����������

    if ($user_dept=="HJ" || $user_dept=="LY"){
        if($user_dept=="HJ") $fty = array($user_dept,'MA');
        if($user_dept=="LY") $fty = array($user_dept);
        $op['factory'] = $arry2->select($fty,$parm['factory'],'PHP_factory','select','');
    }else{
        $op['factory'] = $arry2->select($FACTORY,$parm['factory'],'PHP_factory','select','');  	
    }

    //�Ͳ��u���O�k�k�� -- 08.08.27
    $line_value = array('F','M','A');
    $line_key = array('Woman','Man','Ann');
    $op['line_select'] =  $arry2->select($line_key,$PHP_line,'PHP_line','select','',$line_value); 
    //�D�Ƴ��--08.09.22
    $op['lots_unit'] =  $arry2->select($LOTS_PRICE_UNIT,$PHP_lots_unit,'PHP_lots_unit','select',''); 

    //�~�שu�`		
    $op['season_select'] =  $arry2->select($SEASON,$PHP_season,'PHP_season','select',''); 
    $op['year_select'] =  $arry2->select($YEAR_WORK,$PHP_syear,'PHP_syear','select',''); 


    // creat style type combo box
    $style_def = $style_type->get_fields('style_type');   // ���X �ڦ����O
    $op['style'] =  $arry2->select($style_def,$parm['style'],'PHP_style','select','');  	
    $op['offer'] = $PHP_offer;
    $op['stock_mk'] = $PHP_stk_mk;
    $op['org_ord_num'] = $PHP_org_ord;
    $op['stk_des'] = $PHP_stk_des;
    page_display($op, '065', $TPL_ORDER_ADD);					    	    
    break;

}   // end check

$dt = decode_date(1);
$year_code = substr($dt['year'],-2);

# 2013/07/11 �N���ѥH�᪺�q�檺 handling_fee �g�^ s_order ��(factory = LY �B �ư� L �}�Y���q��)
# 2013/07/11 �ѩ� �uú �ثe�O��ʬD���q�A�ۦ�[�J handling_fee�C�令 �uú �h�@�� �N handling_fee �ǤJ�p��(2013/07/11�{����s��ҷs�إߪ��q��~�ǤJ�p��)
# 2013/07/11 ���ѥH�e�� handling_fee ��줣��ȡA��ε{���P�_2012/08/15����Nhandling_fee�]��0.25�A�ت��O���e�x���(order list�Border show�Border cfm...��)���`

if($parm['factory'] == "LY" and $GLOBALS['SCACHE']['ADMIN']['dept'] == "LY" ){
	$handling_row = $order->get_fields("set_value", " where set_name='handling_fee'","para_set");
	$parm['handling_fee'] = $handling_row[0];
}else{
	$parm['handling_fee'] = 0.00;
}

// �p��  gm rate -----------------------------
$unit_cost = ($parm['mat_u_cost']* $parm['mat_useage'])+ $parm['interline']+ $parm['fusible']+ $parm['acc_u_cost'] + $parm['quota_fee'] + $parm['comm_fee'] + $parm['cm'] + $parm['emb'] + $parm['wash'] + $parm['oth'] + $parm['handling_fee'];
$grand_cost = $unit_cost*$parm['qty'] + $parm['smpl_fee'];
$sales = $parm['uprice']*$parm['qty'];
$gm = $sales - $grand_cost;
if ($sales){
    $rate = ( $gm / $sales )*100;
} else {
    $rate =0;
}
$parm['gmr'] = number_format($rate, 2, '.', ',');

// add to DB
# ����q�渹�X substr($PHP_dept_code,1,1)
$ord_num = $order->get_order_number($dt['year']);
$dept = ( substr($parm['dept'],0,1) == 'K' ) ? substr($parm['dept'],1,1) : substr($parm['dept'],0,1);
$parm['order_num'] = $dept.$PHP_cust.$year_code.'-'.$ord_num;

$f1 = $order->add($parm);

if (!$f1) {  // �S�����\��J��Ʈ�
    $op['msg'] = $order->msg->get(2);
    $layout->assign($op);
    $layout->display($TPL_ERROR);  		    
    break;
} // end if (!$F1)--------- ���\��J ---------------------------------

if ($PHP_offer)
{
    // �W�[����remark
    $PHP_status = $PHP_com = "Create Order :[ ".$parm['order_num']." ] On :[".$TODAY."]";
    $PHP_s_date = $PHP_c_date = $TODAY;
    $PHP_s_user = $PHP_c_user = $GLOBALS['SCACHE']['ADMIN']['login_id'];
    $parm2 = array(	
        "of_num"    =>	$PHP_offer,
        "subject"   =>	'others',
        "comment"   =>	$PHP_com,
        "status"    =>	$PHP_status,
        "c_date"    =>	$PHP_c_date,
        "c_user"    =>	$PHP_c_user,
        "s_date"    =>	$PHP_s_date,
        "s_user"    =>	$PHP_s_user,
    );

    // ------------ add to DB --------------------------
    $f = $offer->add_log($parm2);			  
}

if($PHP_stk_mk)
{
    $order->update_field_num('ie1',$PHP_ie, $parm['order_num']);
    $order->update_field_num('status','2', $parm['order_num']);
    $order->update_field_num('fty_cm',$PHP_fty_cm, $parm['order_num']);
	$order->update_field_num('org_cm',$ORG_CM, $parm['order_num']);
    $PHP_stk_full = '';
    if($PHP_stk_des)$PHP_stk_full = ' COLOR : '.$PHP_stk_des.' �b��';

    $argv2['order_num']	= $PHP_org_ord;
    $argv2['user']			= $GLOBALS['SCACHE']['ADMIN']['login_id'];
    $argv2['des'] 			= '���Ͳ� ; �q�� :'.$parm['order_num'].',�ƶq :'.$PHP_qty.' '.$PHP_stk_full;
    $order_log->add($argv2,"");

    $argv2['order_num']	= $parm['order_num'];
    $argv2['des'] 			= '���Ͳ� ; ��q�� :'.$PHP_org_ord.' '.$PHP_stk_full;
    $order_log->add($argv2,"");

    $stk['ord_org'] = $PHP_org_ord;
    $stk['ord_new'] = $parm['order_num'];
    $stk['des'] = $PHP_stk_full;
    $order_log->add_stock_ord($stk);
}

$message = "append order: [".$parm['order_num']."]";
$log->log_add(0,"101A",$message);

$back_str ="index2.php?PHP_action=order_view&PHP_id=".$f1."&PHP_msg=".$message;
redirect_page($back_str);   



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "order_del_bysales":
    echo $PHP_status;
    echo $GLOBALS['SCACHE']['ADMIN']['name'];
    // print_r($GLOBALS['SCACHE']);
		if(!$admin->is_power('065',"add") && $PHP_name != $GLOBALS['SCACHE']['ADMIN']['name']){ 
			$op['msg'][] = "sorry! you don't have this Authority !";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$op['order'] = $order->get($PHP_id);  //���X�ӵ��O��


		$order_rec = $order->get($PHP_id);  //���X�ӵ��O��	
		
		if($PHP_status < 2)
		{		
				$f1 = $order->update_field('status', -2, $PHP_id);
				$f1 = $order->update_field('del_mk', 3, $PHP_id);
				$f1 = $order->update_field('del_date', $TODAY, $PHP_id);
				$f1 = $order->update_field('del_user', $GLOBALS['SCACHE']['ADMIN']['login_id'], $PHP_id);
				$message="success delete order : ".$PHP_num.".";
				$log->log_add(0,"101D",$message);
				$redirect_str ="index2.php?PHP_action=order_search&PHP_msg=".$message;
				redirect_page($redirect_str); 		    	    		
		}else{
		
			$f1 = $order->update_field('del_mk', 2, $PHP_id);
			$f1 = $order->update_field('del_date', $TODAY, $PHP_id);
			$f1 = $order->update_field('del_user', $GLOBALS['SCACHE']['ADMIN']['login_id'], $PHP_id);

			$redir_str = "exception.php?PHP_action=exc_ord_add&PHP_cust=".$op['order']['cust']."&PHP_dept_code=".$op['order']['dept']."&PHP_order=".$op['order']['order_num']."&PHP_qty=".$op['order']['qty'];
			redirect_page($redir_str);
		}

//=======================================================
	case "send_order_cfm":	
		check_authority('065',"add");			
		
		$where_str = " WHERE order_num like '".substr($PHP_order_num,0,10)."%' AND (status = 1 OR status = 5)";
		$ord_id = $order->get_fields('id', $where_str);		
		for($i=0; $i<sizeof($ord_id); $i++)
		{
			$PHP_ETP = $order->get_field_value('etp',$ord_id[$i]);
			$PHP_ETD = $order->get_field_value('etd',$ord_id[$i]);
			$PHP_order_num = $order->get_field_value('order_num',$ord_id[$i]);

			if ($ord_submit_ETP_limit > $PHP_ETP || $ord_submit_ETD_limit > $PHP_ETD) //��ETP�p�󤵤��7������쭶�X�{���~�T��
			{
				$op['msg'][] = "Sorry ! please update your unreasonable ETP ( before today at least) or ETD ( 20 days before today at least)";		
				$back_str ="index2.php?PHP_action=order_view&PHP_id=".$ord_id[$i]."&cgiget=".$cgiget."&cgino=".$cgino."&cgi_1=".$cgi_1."&cgi_2=".$cgi_2."&cgi_3=".$cgi_3."&cgi_4=".$cgi_4."&cgi_5=".$cgi_5."&cgi_6=".$cgi_6."&cgi_del=".$cgi_del."&sub_error=1";
			
				redirect_page($back_str);   	    
				break;		
			}		
			// �ק� status ��

			$argv = array(	"id"						=>	$ord_id[$i],
											"last_updator"	=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
											"status"				=>	2
										);

			// ========= add to DB =======
			if (!$order->send_cfm($argv)) {
				$op['msg'] = $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;	    
			}

					# �O���ϥΪ̰ʺA
			$message = "Success submit order:[$PHP_order_num]�E";
			$log->log_add(0,"101CFM",$message);
		}

		$back_str ="index2.php?PHP_action=order_view&PHP_id=".$PHP_id."&PHP_msg=".$message;
		redirect_page($back_str);
		break;



//=======================================================
case "do_order_cfm":
check_authority('066',"add");

// �ק� status ��
if ( !$PHP_stk_ord && ($ord_submit_ETP_limit > $PHP_ETP || $ord_submit_ETD_limit > $PHP_ETD || $PHP_QTY==0)) 
{//��[ETP�p�󤵤��7��],[ETD�p�󤵤��14��],[Q'ty=0]�����쭶�X�{���~�T��
    if ($PHP_QTY == 0)	
        $msg = "Sorry ! The Qty  is unreasonable.<br>( Qty must more than 0) <br>Please Reject this order to update it first�C";
    else	
        $msg = "Sorry ! The ETP or ETD date is unreasonable.<br>(ETP : before today at leaset. ETD : 20 days before today at least)<br>Please Reject this order to update it first";				

    $redirect_str =  "index2.php?PHP_action=order_cfm_view&cgino=".$PHP_sr_startno."&PHP_id=".$PHP_id."&max_rec=".$max_rec."&per_page=".$per_page."&cgiget=".$PHP_back_str."&PHP_msg=".$msg;
    redirect_page($redirect_str);
    break;			
}	

$argv = array(
    "id"		=>	$PHP_id,
    "cfmer"		=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
    "status"	=>	3
);

// ========= add to DB =======
if (!$order->do_cfm($argv)) {
    $op['msg'] = $order->msg->get(2);
    $layout->assign($op);
    $layout->display($TPL_ERROR);  		    
    break;	    
}

# �O���ϥΪ̰ʺA
$message = "Confirm order:[$PHP_order_num]�E";
$log->log_add(0,"066A",$message);

//��(�}�l���O������ +1 �����`����) �B (�`��ư��H�C���ƾl1)�� �n��@��
if(($PHP_sr_startno +1 == $max_rec) && ($max_rec %  $per_page == 1)){
    if ($PHP_sr_startno != 0 )$PHP_sr_startno = $PHP_sr_startno - $per_page;				
}

$PHP_back_str = $PHP_back_str ."&PHP_sr_startno=".$PHP_sr_startno;
redirect_page($PHP_back_str);
break;



//=======================================================
	case "reject_order_cfm":
		check_authority('066',"add");
		// �ק� status ��
		$where_str = " WHERE order_num like '".substr($PHP_order_num,0,10)."%' AND status = 2";
		$ord_id = $order->get_fields('id', $where_str);		

		for($i=0; $i<sizeof($ord_id); $i++)
		{
			$PHP_ETP = $order->get_field_value('etp',$ord_id[$i]);
			$PHP_ETD = $order->get_field_value('etd',$ord_id[$i]);
			$PHP_order_num = $order->get_field_value('order_num',$ord_id[$i]);
			$argv = array(	"id"			=>	$ord_id[$i],
											"cfmer"		=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
											"status"	=>	5
										);

			$argv2 = array(	"order_num"	=>	$PHP_order_num,
											"user"			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
											"des"				=>	"CFM REJECT : for - ".$PHP_detail
										);

			// ========= add to  order DB =======
			if (!$order->reject_cfm($argv)) {
				$op['msg'] = $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;	    
			}

			// ========= add to  order_log DB =======
			$order_log->add($argv2);
			# �O���ϥΪ̰ʺA
			$message = "Reject order confirm:[$PHP_order_num]�E";
			$log->log_add(0,"102ARJ",$message);

		//��(�}�l���O������ +1 �����`����) �B (�`��ư��H�C���ƾl1)�� �n��@��
			if(($PHP_sr_startno +1 == $max_rec) && ($max_rec %  $per_page == 1)){
				if ($PHP_sr_startno != 0 )$PHP_sr_startno = $PHP_sr_startno - $per_page;
			}
		}
		
		$PHP_back_str = $PHP_back_str ."&PHP_sr_startno=".$PHP_sr_startno;
		redirect_page($PHP_back_str);
		break;



//=======================================================
case "do_order_rev":
check_authority('075',"add");
// �ק� status ��

if ( !$PHP_stk_ord && ($ord_submit_ETP_limit > $PHP_etp || $ord_submit_ETD_limit > $PHP_etd || $PHP_QTY==0)) 
{//��[ETP�p�󤵤��7��],[ETD�p�󤵤��14��],[Q'ty=0]�����쭶�X�{���~�T��
	if ($PHP_QTY == 0)	
		$_SESSION['MSG'][] =  "Sorry ! The Qty  is unreasonable.<br>( Qty must more than 0) <br>Please Reject this order to update it first�C";
	else	
		$_SESSION['MSG'][] =  "Sorry ! The ETP or ETD date is unreasonable.<br>(ETP : before today at leaset. ETD : 20 days before today at least)<br>Please Reject this order to update it first";				

	$redirect_str =  "index2.php?PHP_action=order_rev_view&cgino=".$PHP_sr_startno."&PHP_id=".$PHP_id."&max_rec=".$max_rec."&per_page=".$per_page."&cgiget=".$PHP_back_str."&PHP_msg=".$msg;
	redirect_page($redirect_str);
	break;			
}	

$argv = array(
	"id"		=>	$PHP_id,
	"rev_user"	=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
	"status"	=>	3
);

// ========= add to DB =======
if (!$order->do_rev($argv)) {
	$op['msg'] = $order->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;	    
}

# �O���ϥΪ̰ʺA
$message = "Review order:[$PHP_order_num]�E";
$log->log_add(0,"075A",$message);

// ��(�}�l���O������ + 1 �����`����) �B (�`��ư��H�C���ƾl1)�� �n��@��
if(($PHP_sr_startno + 1 == $max_rec) && ($max_rec %  $per_page == 1)){
	if ($PHP_sr_startno != 0 )$PHP_sr_startno = $PHP_sr_startno - $per_page;				
}

$PHP_back_str = $PHP_back_str ."&PHP_sr_startno=".$PHP_sr_startno;
redirect_page($PHP_back_str);
break;



//=======================================================
case "reject_order_rev":
check_authority('075',"add");
// �ק� status ��
$where_str = " WHERE order_num like '".substr($PHP_order_num,0,10)."%' AND status = 13";
$ord_id = $order->get_fields('id', $where_str);		

for($i=0; $i<sizeof($ord_id); $i++)
{
	$PHP_ETP = $order->get_field_value('etp',$ord_id[$i]);
	$PHP_ETD = $order->get_field_value('etd',$ord_id[$i]);
	$PHP_order_num = $order->get_field_value('order_num',$ord_id[$i]);
	$argv = array(
		"id"		=>	$ord_id[$i],
		"rev_user"	=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
		"status"	=>	5
	);

	$argv2 = array(	
		"order_num"	=>	$PHP_order_num,
		"user"		=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
		"des"		=>	"REV REJECT : for - ".$PHP_detail
	);

	// ========= add to  order DB =======
	if (!$order->reject_rev($argv)) {
		$op['msg'] = $order->msg->get(2);
		$layout->assign($op);
		$layout->display($TPL_ERROR);  		    
		break;	    
	}

	// ========= add to  order_log DB =======
	$order_log->add($argv2);
	# �O���ϥΪ̰ʺA
	$message = "Reject order Review :[$PHP_order_num]�E";
	$log->log_add(0,"075ARJ",$message);

	//��(�}�l���O������ + 1 �����`����) �B (�`��ư��H�C���ƾl1)�� �n��@��
	if(($PHP_sr_startno + 1 == $max_rec) && ($max_rec %  $per_page == 1)){
		if ($PHP_sr_startno != 0 )$PHP_sr_startno = $PHP_sr_startno - $per_page;
	}
}

$PHP_back_str = $PHP_back_str ."&PHP_sr_startno=".$PHP_sr_startno;
redirect_page($PHP_back_str);
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "order_del_REV_bysales":
if(!$admin->is_power('075',"add") && $PHP_name != $GLOBALS['SCACHE']['ADMIN']['name']){ 
	$op['msg'][] = "sorry! you don't have this Authority !";
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}
$op['order'] = $order->get($PHP_id);  //���X�ӵ��O��


$order_rec = $order->get($PHP_id);  //���X�ӵ��O��	

if($PHP_status < 2)
{		
	// $f1 = $order->update_field('status', -2, $PHP_id);
	$f1 = $order->update_field('del_mk', 2, $PHP_id);
	$f1 = $order->update_field('rev_date', $TODAY, $PHP_id);
	$f1 = $order->update_field('rev_user', $GLOBALS['SCACHE']['ADMIN']['login_id'], $PHP_id);
	$message="success delete order : ".$PHP_num.".";
	$log->log_add(0,"065D",$message);
	$redirect_str ="index2.php?PHP_action=order_search&PHP_msg=".$message;
	redirect_page($redirect_str); 		    	    		
}else{
	$redir_str = "exception.php?PHP_action=exc_ord_add&PHP_cust=".$op['order']['cust']."&PHP_dept_code=".$op['order']['dept']."&PHP_order=".$op['order']['order_num']."&PHP_qty=".$op['order']['qty'];
	redirect_page($redir_str);
}


//=======================================================
case "do_order_apv":
check_authority('067',"add");

if (!$PHP_stk_ord && ($ord_submit_ETP_limit > $PHP_etp || $ord_submit_ETD_limit > $PHP_etd || $PHP_QTY==0))
{//��[ETP�p�󤵤��7��],[ETD�p�󤵤��14��],[Q'ty=0]�����쭶�X�{���~�T��			
	if ($PHP_QTY == 0)			
		$msg = "Sorry ! The Q\'ty  is unreasonable.<br>( Q\'ty must more than 0) <br>Please Reject this order to update it first�C";
	else
		$msg = "Sorry ! The ETP or ETD date is unreasonable.<br>(ETP : before today at leaset. ETD : 20 days before today at least)<br>Please Reject this order to update it first";				
			
	$redirect_str =  "index2.php?PHP_action=order_apv_view&cgino=".$PHP_sr_startno."&PHP_id=".$PHP_id."&max_rec=".$max_rec."&per_page=".$per_page."&cgiget=".$PHP_back_str."&PHP_msg=".$msg;
	redirect_page($redirect_str);
	break;				
}	

// �g�J capaci. pre-schedule ===  
// ���ˬd capacity �ɤ��O�_�w�g���O�� ???
$s_year = substr($PHP_etp,0,4);
$e_year = substr($PHP_etd,0,4);
for($x=$s_year;$x<$e_year+1;$x++){		
	if (!$T=$capaci->get($PHP_factory, $x)) {
		$op['msg'] = $capaci->msg->get(2);
		$layout->assign($op);
		$layout->display($TPL_ERROR);  		    
		exit;
	}
}

// �ק� status ��  --------- s_order table -------------------------------
$output = $order->get_output($PHP_order_num);
$status = $output > 0 ? 8 : 4;
$argv = array(
    "id"			=>	$PHP_id,
    "apver"			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
    "status"		=>	$status
);

// ========= add to s_order APV �O�� =======  ????????  �O�_�g�J �n�ݦ��S�� capacity �O��
if (!$order->do_apv($argv)) {
	$op['msg'] = $order->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;	    
}

$order->update_field('org_etd', $PHP_etd, $PHP_id); 

// �g�J capaci. pre-schedule ===  �b order->diver_month_su ���g�J

$where_str=" WHERE ord_num = '".$PHP_order_num."' GROUP BY id"; //���o�q��partial ship����
$p_etp = $order->get_fields('p_etp', $where_str,'order_partial');
$p_etd = $order->get_fields('p_etd', $where_str,'order_partial');
$p_su = $order->get_fields('p_su', $where_str,'order_partial');
$p_id = $order->get_fields('id', $where_str,'order_partial');


//			$div = $order->distri_month_su($PHP_su,$PHP_etp,$PHP_etd,$PHP_factory,'pre_schedule');

$parm = array(	
    "order_num"		=>	$PHP_order_num,
    "factory"       =>	$PHP_factory,
    "etp_su"		=>	'',     
);

	
if (!$pdt = $order->get_pdtion($PHP_order_num, $PHP_factory)){  //�P�_�O�_���ӵ� pdtion �p�G�S �h�s�W  �p�G�� �N�N���Ƨ�s
	for($i=0; $i<sizeof($p_id); $i++)// partial shipment�U�X�f����tetp_su
	{
		$div = $order->distri_month_su($p_su[$i],$p_etp[$i],$p_etd[$i],$PHP_factory,'pre_schedule'); //����C��SU���x�s
		$order->update_partial('p_etp_su', $div, $p_id[$i]);	
	}				
	// ========= create PDTION �ӵ� �O�� =======
	$order->creat_pdtion($parm);		
	$order->update_distri_su($PHP_order_num,'p_etp_su','etp_su'); //�g�J�q��etp_su
}else{ 				
	$p_etp_su = $order->get_fields('p_etp_su', $where_str,'order_partial');
	for($i=0; $i<sizeof($p_id); $i++)// partial shipment�U�X�f����tetp_su
	{
//		 			$etp_su=decode_mon_yy_su($p_etp_su[$i]); //���X�v�s�betp-su�ñNsu�ȻP�~,����}
//				 	for ($j=0; $j<sizeof($etp_su); $j++) //�N�v�s�bsu���~�װO���ȧR��				 					 	
//				 		$f1=$capaci->delete_su($PHP_factory, $etp_su[$j]['year'], $etp_su[$j]['mon'], 'pre_schedule', $etp_su[$j]['su']);			 		
		
		$div = $order->distri_month_su($p_su[$i],$p_etp[$i],$p_etd[$i],$PHP_factory,'pre_schedule'); //����C��SU���x�s				
		$order->update_partial('p_etp_su', $div, $p_id[$i]);					 		
	}				
	$order->update_distri_su($PHP_order_num,'p_etp_su','etp_su'); //�g�J�q��etp_su
	
	// ========= ��s  PDTION �ӵ� �O�� ==   [ �� $pdt ��� id ] =====
	//$order->revise_pdtion($parm,$pdt['id']);
	
	//�[schedule	
	$schedule->add_ord_schedule($PHP_order_num);	
	if($schedule->check_sch_finish($PHP_order_num))
	{
		$schedule->add_capacity($PHP_order_num);
		$message = "UPDATE order [ ".$PHP_order_num." ] production schedule [ETS, ETF] ";
		$log->log_add(0,"067A",$message);
	}
}

# �O���ϥΪ̰ʺA
$message = "Approval order:[".$PHP_order_num."]";
$log->log_add(0,"103A",$message);

//��(�}�l���O������ +1 �����`����) �B (�`��ư��H�C���ƾl1)�� �n��@��
if(($PHP_sr_startno +1 == $max_rec) && ($max_rec %  $per_page == 1)){
	if ($PHP_sr_startno != 0 )$PHP_sr_startno = $PHP_sr_startno - $per_page;					
	
}

$PHP_back_str = $PHP_back_str ."&PHP_sr_startno=".$PHP_sr_startno;
redirect_page($PHP_back_str);
break;



//=======================================================
	case "reject_order_apv":

		check_authority('067',"add");
		$where_str = " WHERE order_num like '".substr($PHP_order_num,0,10)."%' AND status = 3";
		$ord_id = $order->get_fields('id', $where_str);		

		for($i=0; $i<sizeof($ord_id); $i++)
		{

			$PHP_ETP = $order->get_field_value('etp',$ord_id[$i]);
			$PHP_ETD = $order->get_field_value('etd',$ord_id[$i]);
			$PHP_order_num = $order->get_field_value('order_num',$ord_id[$i]);
			$PHP_su = $order->get_field_value('su',$ord_id[$i]);
			// �ק� status ��
			$argv = array(	"id"			=>	$ord_id[$i],
											"apver"		=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
											"status"	=>	5
									);

			$argv2 = array(	"order_num"	=>	$PHP_order_num,
											"user"			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
											"des"			=>	"APV REJECT : for - ".$PHP_detail
										);

			// ========= add to  order DB =======
			if (!$order->reject_apv($argv)) {
				$op['msg'] = $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;	    
			}

			// ========= add to  order_log DB =======
			$order_log->add($argv2);
			
			# �O���ϥΪ̰ʺA
			$message = "Reject order approval:[$PHP_order_num]�E";
			$log->log_add(0,"103ARJ",$message);

			//��(�}�l���O������ +1 �����`����) �B (�`��ư��H�C���ƾl1)�� �n��@��
			if(($PHP_sr_startno +1 == $max_rec) && ($max_rec %  $per_page == 1)){
				if ($PHP_sr_startno != 0 )$PHP_sr_startno = $PHP_sr_startno - $per_page;
			}
		}
		$PHP_back_str = $PHP_back_str ."&PHP_sr_startno=".$PHP_sr_startno;
		redirect_page($PHP_back_str);
		break;



//=======================================================
case "partial_del":
    $order->dle_partial($PHP_id,$PHP_ord_num);
exit;


//=======================================================
case "creat_partial":
//		$q_str = "SELECT s_order.order_num, etd, etp, qty, su, etp_su, fty_su, m_status,
//										 ets, etf, ext_su, ext_qty, ext_period, 
//										 qty_done, qty_shp, status, shp_date
//						  FROM s_order, pdtion
//							WHERE  pdtion.order_num = s_order.order_num";														
//
//		$q_result = $mysql->query($q_str);		
//		while($row = $mysql->fetch($q_result))
//		{
//			$rec[] = $row;
//		}		
//    for($i=0; $i<sizeof($rec); $i++)
//    {
//					$pdt_status = 0;
//					if ($rec[$i]['status'] == 10)$pdt_status = 2;
//					if ($rec[$i]['status'] == 12)$pdt_status = 4;
//    					# �[�J��Ʈw
//				$q_str = "INSERT INTO order_partial (ord_num, mks, p_etd,	p_etp,	p_qty,	p_su,	p_etp_su,
//									p_fty_su,	wi_status,	p_ets,	p_etf,	p_qty_done, p_shp_date, p_qty_shp, 
//									pdt_status, ext_period, ext_su,	ext_qty	) VALUES('".
//									$rec[$i]['order_num']."','A','".									
//									$rec[$i]['etd']."','".
//									$rec[$i]['etp']."','".
//									$rec[$i]['qty']."','".
//									$rec[$i]['su']."','".
//									$rec[$i]['etp_su']."','".
//									$rec[$i]['fty_su']."','".
//									$rec[$i]['m_status']."','".									
//									$rec[$i]['ets']."','".				
//									$rec[$i]['etf']."','".	
//									$rec[$i]['qty_done']."','".
//									$rec[$i]['shp_date']."','".
//									$rec[$i]['qty_shp']."','".
//									$pdt_status."','".
//									$rec[$i]['ext_period']."','".			
//									$rec[$i]['ext_su']."','".				
//									$rec[$i]['ext_qty']."')";
//				$q_result = $mysql->query($q_str);
//    }

//		$q_str = "SELECT s_order.order_num, order_partial.id, schedule.id as sch_id										
//						  FROM s_order, order_partial, schedule
//							WHERE  order_partial.ord_num = s_order.order_num AND schedule.ord_num = s_order.order_num";														
//
//		$q_result = $mysql->query($q_str);		
//		$rec = array();
//		while($row = $mysql->fetch($q_result)) $rec[] = $row;
//	
//    for($i=0; $i<sizeof($rec); $i++)
//    {
//			$q_str =  "UPDATE schedule SET p_id='".$rec[$i]['id']."'  WHERE id = '".$rec[$i]['sch_id']."'";
//			$q_result = $mysql->query($q_str);
//    }
//    
//
//		$q_str = "SELECT s_order.order_num, order_partial.id, wiqty.id as wiqty_id
//						  FROM s_order, order_partial, wi, wiqty
//							WHERE  order_partial.ord_num = s_order.order_num AND wi.wi_num = s_order.order_num AND
//										 wi.id = wiqty.wi_id";														
//
//		$q_result = $mysql->query($q_str);		
//		$rec = array();
//		while($row = $mysql->fetch($q_result)) $rec[] = $row;    
//    for($i=0; $i<sizeof($rec); $i++)
//    {
//			$q_str =  "UPDATE wiqty SET p_id='".$rec[$i]['id']."'  WHERE id = '".$rec[$i]['wiqty_id']."'";
//			$q_result = $mysql->query($q_str);
//    }

    $q_str = "SELECT s_order.order_num, order_partial.id
        FROM s_order, order_partial, saw_out_put
        WHERE order_partial.ord_num = s_order.order_num AND 
        saw_out_put.ord_num = order_partial.ord_num AND
        saw_out_put.ord_num = s_order.order_num AND p_id = 0
        GROUP BY saw_out_put.ord_num
    ";														

    $q_result = $mysql->query($q_str);		
    $rec = array();
    while($row = $mysql->fetch($q_result)) $rec[] = $row;    
    for($i=0; $i<sizeof($rec); $i++) {
        $q_str =  "UPDATE saw_out_put SET p_id='".$rec[$i]['id']."'  WHERE ord_num = '".$rec[$i]['order_num']."'";
        // echo $q_str."<BR>";
        $q_result = $mysql->query($q_str);
    }

//		$q_str = "SELECT s_order.order_num, order_partial.id
//						  FROM s_order, order_partial, shipping
//							WHERE  order_partial.ord_num = s_order.order_num AND shipping.ord_num = s_order.order_num AND
//										 order_partial.ord_num = shipping.ord_num AND p_id = 0
//							GROUP BY order_num";														
//
//		$q_result = $mysql->query($q_str);		
//		$rec = array();
//		while($row = $mysql->fetch($q_result)) $rec[] = $row;    
//		echo sizeof($rec)."<BR>";
//    for($i=0; $i<sizeof($rec); $i++)
//    {
//			$q_str =  "UPDATE shipping SET p_id='".$rec[$i]['id']."'  WHERE ord_num = '".$rec[$i]['order_num']."'";
//		echo $q_str."<BR>";
//			$q_result = $mysql->query($q_str);
//    }





//		$q_str = "SELECT s_order.order_num, order_partial.id, pdtion.ext_period, pdtion.ext_qty, pdtion.ext_su,
//										 pdtion.qty_done, pdtion.qty_shp, status, pdtion.shp_date
//						  FROM s_order, pdtion, order_partial
//							WHERE  pdtion.order_num = s_order.order_num AND order_partial.ord_num = s_order.order_num";
//		$rec = array();
//		$q_result = $mysql->query($q_str);
//		while($row = $mysql->fetch($q_result)) $rec[] = $row;    
//
//    for($i=0; $i<sizeof($rec); $i++)
//    {
//			$pdt_status = 0;
//			if ($rec[$i]['status'] == 10)$pdt_status = 2;
//			if ($rec[$i]['status'] == 12)$pdt_status = 4;
//			$q_str =  "UPDATE order_partial SET ext_qty='".$rec[$i]['ext_qty']."', ext_su ='".$rec[$i]['ext_su']."',
//								 ext_period='".$rec[$i]['ext_period']."', p_qty_done='".$rec[$i]['qty_done']."', p_shp_date=".$rec[$i]['shp_date']."'
//								 p_qty_shp ='".$rec[$i]['qty_shp']."', pdt_status ='".$pdt_status."'  WHERE id = '".$rec[$i]['id']."'";
//		echo $q_str."<BR>";
//			$q_result = $mysql->query($q_str);			
//    }
exit;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "ship_invoice_view":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "ship_invoice_view":
// check_authority($AUTH,$PGM[1]);
check_authority('065',"view");

$op = $shipping_doc->get_invoice($PHP_id);
// print_r($op);
#���b���
$nd = 5;

$sd = '';
$op['ckdate'] = 0;
if ( $op['invoice']['ship_date'] < increceMonthInDate2(date("Y-m").'-01',-1) ) {
	$sd = date("Y-m").'-01';
} else {
	if ( date("d") > $nd ) {
		$sd = date("Y-m").'-01';
	} else {
		$sd = increceMonthInDate2(date("Y-m").'-01',-1);
	}
}

# �t�κ޲z�̩M �|�p�H���i�H�L����ק�
if ( $op['invoice']['ship_date'] >= $sd || $_SESSION['USER']['ADMIN']['dept'] == 'SA' || $_SESSION['USER']['ADMIN']['dept'] == 'AC' ) {
	$op['ckdate'] = 1;
} else {
	$op['ckdate'] = 0;
}


page_display($op,'065','ship_order_view.html');
break;



//= end case ======================================================
}

?>
