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
require_once "init.object.php";

$PHP_SELF = $_SERVER['PHP_SELF'];
$perm = $GLOBALS['power'];
$op = array();

$TPL_MARKER = 'marker_search.html';
$TPL_MARKER_SMPL_VIEW = 'marker_smpl_view.html';
$TPL_MARKER_SMPL_LIST = 'marker_smpl_list.html';
$TPL_MARKER_SMPL_ADD = 'marker_smpl_add.html';
$TPL_MARKER_SMPL_EDIT = 'marker_smpl_edit.html';

$TPL_MARKER_ORD_ADD = 'marker_ord_add.html';
$TPL_MARKER_ORD_REPORT = 'marker_ord_report.html';
$TPL_MARKER_ORD_LIST = 'marker_ord_list.html';
$TPL_MARKER_ORD_REPORT_VIEW = 'marker_ord_report_view.html';
$TPL_MARKER_VIEW_WI = "marker_view_wi.html";

$GLOBALS['fab_type'][1] = 'shell';
$GLOBALS['fab_type'][2] = 'lining';
$GLOBALS['fab_type'][3] = 'fusible';
$GLOBALS['fab_type'][4] = 'non-fusible';
$GLOBALS['fab_type'][5] = 'pocketing';

$GLOBALS['unit_type'][1] = 'inch / yard';
$GLOBALS['unit_type'][2] = 'cm / meter';

$GLOBALS['unit_type2'][1] = 'yd.';
$GLOBALS['unit_type2'][2] = 'm.';
$GLOBALS['unit_type3'][1] = '(inch)';
$GLOBALS['unit_type3'][2] = '(cm)';
$GLOBALS['unit_type4'][1] = 'yard';
$GLOBALS['unit_type4'][2] = 'meter';

$GLOBALS['m_rmk'][1] = '[對格、對條]';
$GLOBALS['m_rmk'][2] = '[全順]';
$GLOBALS['m_rmk'][3] = '[件順]';
$GLOBALS['m_rmk'][4] = '[單拉]';
$GLOBALS['m_rmk'][5] = '[雙拉]';

# 2014/10/08 Mode 江大哥反映增加板子說明欄位
// echo $PHP_action.'<br>';
switch ($PHP_action) {

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "marker_search":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

case "marker_search":
check_authority("061","view");
// creat cust combo box
$where_str="order by cust_s_name"; //依cust_s_name排序
$cust_def = $cust->get_fields('cust_init_name',$where_str);
$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//取出客戶代號
for ($i=0; $i< sizeof($cust_def); $i++)
{
    $cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//以 [客戶代號-客戶簡稱] 呈現
}
$op['cust_select'] =  $arry2->select($cust_value,'','PHP_cust','select','',$cust_def_vue); 

$style_def = $style_type->get_fields('style_type');   // 取出 款式類別
$op['style_type_select'] =  $arry2->select($style_def,'','PHP_style','select','');
$op['factory'] = $arry2->select($FACTORY,'','PHP_factory','select','');
page_display($op, "061", $TPL_MARKER);
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "marker_list":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

case "marker_smpl_list":
check_authority("061","view");

$parm = array(
'PHP_num'		=> $PHP_num ,
'PHP_style'		=> $PHP_style ,
'PHP_factory'	=> $PHP_factory ,
);

if (!$op = $Marker->search(1)) {
    $op['msg']= $order->msg->get(2);
    $layout->assign($op);
    $layout->display($TPL_ERROR);
    break;
}
$op['cgi']= $parm;

foreach($op['marker'] as $keys => $vals){
    foreach($vals as $key => $val){
        if($key === 'smpl_id'){
            $s_o = $smpl_ord->get($val);
            $size = $size_des->get($s_o['size']);
            $op['marker'][$keys]['main_pic'] = _pic_url('small','50',3,'smpl_pic',$s_o['num'],'jpg','','');
            $op['marker'][$keys]['ref'] = $s_o['ref'];
            $op['marker'][$keys]['smpl_type'] = $s_o['smpl_type'];
            $op['marker'][$keys]['style'] = $s_o['style'];
            $op['marker'][$keys]['size_range'] = $size['size_scale'];
            $op['marker'][$keys]['base_size'] = $size['base_size'];
        }
        if($key === 'fab_type'){
            if( $val <> 0 ){
                $op['marker'][$keys][$key] = $GLOBALS['fab_type'][$val];
            }else{
                $op['marker'][$keys][$key] = '';
            }
        }
    }
}

page_display($op, "061", $TPL_MARKER_SMPL_LIST);
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "marker_smpl_view":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

case "marker_smpl_view":
check_authority("061","view");
if(empty($PHP_id))$PHP_id='';
if(empty($PHP_smpl_id))$PHP_smpl_id='';
$op = $Marker->get(1,$PHP_id,$PHP_smpl_id);
$s_o = $smpl_ord->get($op['smpl_id']);
$op['factory']=$s_o['factory'];
$sql_str4marker_waste = "SELECT set_value FROM para_set WHERE set_name ='".$op['factory']."_mark_waste_value'";
$op['marker_waste_value']= $Marker->get_marker_waste_value($sql_str4marker_waste);
if($op){
    #$combo = $Marker->fab_item($op['fab_type'],$op['combo']);
    if($op['unit_type'] == 1){
        $op['unit1'] = 'inch';
        $op['unit2'] = 'yard';
    }else{
        $op['unit1'] = 'cm';
        $op['unit2'] = 'meter';
    }

    $op['fab_type']		= $GLOBALS['fab_type'][$op['fab_type']];
    $op['unit_type']	= $GLOBALS['unit_type'][$op['unit_type']];

    $op['description']  =	$op['description'];
    $op['remark'] 		=	$Marker->rmk_list($op['remark']);

    if( $s_o['marker_date'] <> '0000-00-00' ){
        $marker = $s_o['num'].'.mk.pdf';
        if(file_exists($GLOBALS['config']['root_dir'].'./smpl_marker/'.$marker)){
            $op['marker']	= $marker;
        }
    }

    $op['num'] = $s_o['num'];

    # 檢查 相片是否存在 2008.10.29
    $op['main_pic'] = _pic_url('small','50',3,'smpl_pic',$s_o['num'],'jpg','','');

    if (!$op['mks'] = $Marker->get(1,0,$op['smpl_id'],1)) {
        $op['msg']= $order->msg->get(2);
        $layout->assign($op);
        $layout->display($TPL_ERROR);
        break;
    }

    foreach($op['mks'] as $keys => $vals){
        if(is_array($vals)){
            foreach($vals as $key => $val){
                if($key === 'remark')$op['mks'][$keys][$key] = $Marker->rmk_list($val);
                if($key === 'fab_type'){
                    if( $val <> 0 ){
                        $op['mks'][$keys]['fab_t'] = $GLOBALS['fab_type'][$val];
                    }else{
                        $op['mks'][$keys]['fab_t'] = '';
                    }
                }
                if($key === 'unit_type'){
                    if( $val == 1 ){
                        $op['mks'][$keys]['unit1'] = 'inch';
                        $op['mks'][$keys]['unit2'] = 'yard';
                    }else{
                        $op['mks'][$keys]['unit1'] = 'cm';
                        $op['mks'][$keys]['unit2'] = 'meter';
                    }
                }
            }
			$op['mks'][$keys]['length_total'] = round($op['mks'][$keys]['length'],2)+round($op['marker_waste_value'],2);
        }
    }
}else{
    # 如果都沒有 marker ，也一併刪除檔案
    $s_o = $smpl_ord->get($PHP_smpl_id);
    if($s_o['marker_date'] == '0000-00-00'){
        $marker =$s_o['num'].'.mk.pdf';
        $Marker->del_marker(1,$PHP_smpl_id,$marker);

        # 沒資料時返回新增
        $redir_str = "marker.php?PHP_action=marker_smpl_add&PHP_smpl_id=".$PHP_smpl_id;
        redirect_page($redir_str);
    }else{
        $op['marker']	= $s_o['num'].'.mk.pdf';
    }
}
$op['length_total'] = round($op['length'],2)+round($op['marker_waste_value'],2);
//$op['length_total'] = $op['length']+$op['marker_waste_value'];
//print_r($op);
page_display($op, "061", $TPL_MARKER_SMPL_VIEW);
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "marker_smpl_view_ajx":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

case "marker_smpl_view_ajx":
check_authority("061","view");
if(empty($PHP_id))$PHP_id='';
if(empty($PHP_smpl_id))$PHP_smpl_id='';
$op = $Marker->get(1,$PHP_id,$PHP_smpl_id);

$out = $in = '' ;
if($op){

    $s_o = $GLOBALS['smpl_ord']->get($op['smpl_id']);

    if($s_o['marker_date'] <> '0000-00-00' ){
        $marker = (file_exists($GLOBALS['config']['root_dir'].'./smpl_marker/'.$s_o['num'].".mk.pdf"))? $s_o['num'].".mk.pdf" : '' ;
    }
    //$marker = $s_o['num'].".mk.pdf";

    if (!$op['mks'] = $Marker->get(1,0,$op['smpl_id'],1)) {
        $op['msg']= $order->msg->get(2);
        $layout->assign($op);
        $layout->display($TPL_ERROR);
        break;
    }

    foreach($op['mks'] as $keys => $vals){
		// echo $marker.'----'.$PHP_smpl_id.'----'.print_r($vals);
        if(is_array($vals)){
            foreach($vals as $key => $val){
                if( $key === 'remark' ||  $key === 9){
                    $in .= $key . '=' . big52uni($Marker->rmk_list($val)) . '^';
                }else{
                    $in .= $key . '=' . big52uni($op['mks'][$keys][$key]) . '^';
                }
            }
            $out .= $in . '|';
        }
    }
		
// exit;
} else {
    $s_o = $GLOBALS['smpl_ord']->get_fieldvalue($PHP_smpl_id,'num,marker_date');
    if($s_o['marker_date'] <> '0000-00-00' ){
        $marker = (file_exists($GLOBALS['config']['root_dir'].'./smpl_marker/'.$s_o['num'].".mk.pdf"))? $s_o['num'].".mk.pdf" : '' ;
    }
}

echo $out.'@'.$marker.'@'.$PHP_status;
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "marker_smpl_add":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "marker_smpl_add":
check_authority("061","add");

$op = $smpl_ord->get($PHP_smpl_id);
$op['choic_rmk'] = $Marker->choic_rmk();
if (!$op) {
    $op['msg'][] = "沒有該樣本單紀錄，請重新輸入！";
    $layout->assign($op);
    $layout->display($TPL_ERROR);
    break;
}

$op['select_fab']   = $GLOBALS['arry2']->select_id($GLOBALS['fab_type'],'','PHP_fab','select_fab','','');
$op['select_unit']  = $Marker->select_unit(1);

if( $op['marker_date'] <> '0000-00-00' ){
    $marker = $op['num'].'.mk.pdf';
    if(file_exists($GLOBALS['config']['root_dir'].'./smpl_marker/'.$marker)){
        $op['marker']	= $marker;
    }
}

# 檢查 相片是否存在 2008.10.29
$op['main_pic'] = _pic_url('small','50',3,'smpl_pic',$op['num'],'jpg','','');

if ($op['mks'] = $Marker->get(1,0,$PHP_smpl_id,1)) {
    foreach($op['mks'] as $keys => $vals){
        if(is_array($vals)){
            foreach($vals as $key => $val){
                if($key === 'fab_type'){
                    if( $val <> 0 ){
                        $op['mks'][$keys]['fab_t'] = $GLOBALS['fab_type'][$val];
                    }else{
                        $op['mks'][$keys]['fab_t'] = '';
                    }
                }

                if($key === 'unit_type'){
                    if( $val == 1 ){
                        $op['mks'][$keys]['unit1'] = 'inch';
                        $op['mks'][$keys]['unit2'] = 'yard';
                    }else{
                        $op['mks'][$keys]['unit1'] = 'cm';
                        $op['mks'][$keys]['unit2'] = 'meter';
                    }
                }
                if($key === 'remark'){
                    $op['mks'][$keys]['remark'] = $Marker->rmk_list($val);
                }
            }
        }
    }
} else {
    # 如果都沒有 marker ，也一併刪除檔案
    if($op['marker_date'] == '0000-00-00'){
        $marker = $op['num'].'.mk.pdf';
        $Marker->del_marker(1,$PHP_smpl_id,$marker);
        $op['marker']	= '';
    }else{
        $op['marker']	= $op['num'].'.mk.pdf';
    }
}

page_display($op, "061", $TPL_MARKER_SMPL_ADD);
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "marker_smpl_do_add":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

case "marker_smpl_do_add":
check_authority("061","add");

$perm = array(
'num'               => $PHP_num ,
'smpl_id'			=> $PHP_smpl_id ,
'fab_type' 		    => $PHP_fab ,
'unit_type'		    => $PHP_unit ,
'width' 			=> $PHP_width ,
'length'		 	=> $PHP_length ,
'description'       => $PHP_description,
'rmk'		 		=> $rmk,
'updator'		 	=> $GLOBALS['SCACHE']['ADMIN']['login_id']
);


if(!empty($perm['fab_type'])){
    $combo_num = $Marker->get_combo(1,$perm['smpl_id'],$perm['fab_type']);
    $combo = $combo_num+1;
}

if($PHP_id = $Marker->smpl_add($perm)){
    $message = "append sample marker: [".$PHP_num."] (".$GLOBALS['fab_type'][$PHP_fab].$combo.")";
    # 記錄使用者動態
    $log->log_add(0,"061A",$message);
}

if(empty($PHP_append)){
    $redir_str = "marker.php?PHP_action=marker_smpl_view&PHP_id=".$PHP_id;
    redirect_page($redir_str);
    break;
}else{
    $redir_str = "marker.php?PHP_action=marker_smpl_add&PHP_smpl_id=".$PHP_smpl_id;
    redirect_page($redir_str);
    break;
}



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "marker_smpl_edit":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

case "marker_smpl_edit":
check_authority("061","edit");
$op = $Marker->get(1,$PHP_id);
$s_o = $smpl_ord->get($op['smpl_id']);

$op['select_fab']	 = $GLOBALS['fab_type'][$op['fab_type']];
$op['select_unit'] = $Marker->select_unit(1,$op['unit_type']);
$op['remark'] = $Marker->edit_rmk($op['remark']);
if($op['unit_type'] == 1){
    $op['unit1'] = 'inch';
    $op['unit2'] = 'yard';
}else{
    $op['unit1'] = 'cm';
    $op['unit2'] = 'meter';
}

if( $s_o['marker_date'] <> '0000-00-00' ){
    $marker = $s_o['num'].'.mk.pdf';
    if(file_exists($GLOBALS['config']['root_dir'].'./smpl_marker/'.$marker)){
        $op['marker']	= $marker;
    }
}

# 檢查 相片是否存在 2008.10.29
$op['main_pic'] = _pic_url('small','50',3,'smpl_pic',$s_o['num'],'jpg','','');

$op['num'] = $s_o['num'];

page_display($op, "061", $TPL_MARKER_SMPL_EDIT);
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "marker_smpl_do_edit":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

case "marker_smpl_do_edit":
check_authority("061","edit");
$perm = array(
'id'			=> $PHP_id ,
'smpl_id'		=> $PHP_smpl_id ,
'num'			=> $PHP_num ,
'fab_type' 		=> $PHP_fab ,
'unit_type'		=> $PHP_unit ,
'width' 		=> $PHP_width ,
'length'		=> $PHP_length ,
'description'	=> $PHP_description,
'rmk'		 	=> $rmk,
'updator'		=> $GLOBALS['SCACHE']['ADMIN']['login_id']
);

if($Marker->edit($perm)){
    $message = "EDIT sample marker: [".$PHP_num."]";
    # 記錄使用者動態
    $log->log_add(0,"061E",$message);
}

$redir_str = "marker.php?PHP_action=marker_smpl_view&PHP_id=".$PHP_id;
redirect_page($redir_str);



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "marker_smpl_up":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

case "marker_smpl_up":
check_authority("061","add");

$perm = array(
'num'				=> $PHP_num ,
'smpl_id'			=> $PHP_smpl_id ,
'marker'		 	=> $PHP_marker ,
'save_path'         => dirname(__FILE__).'\smpl_marker\\',
'updator'		 	=> $GLOBALS['SCACHE']['ADMIN']['login_id']
);

if($PHP_id = $Marker->marker_smpl_upload($perm)){
    $message = "Upload sample marker : [".$PHP_num.".mk.zip]";
    # 記錄使用者動態
    $log->log_add(0,"061U",$message);
}

if(empty($PHP_append)){
    $redir_str = "marker.php?PHP_action=marker_smpl_view&PHP_id=".$PHP_id;
    redirect_page($redir_str);
    break;
}else{
    $redir_str = "marker.php?PHP_action=marker_smpl_add&PHP_smpl_id=".$PHP_smpl_id;
    redirect_page($redir_str);
    break;
}


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		job 24V sample MARKER download
#		case "smpl_marker_download":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "smpl_marker_download":
check_authority("061","view");

header("Content-type: application/octet-stream");
header("Content-Transfer-Encoding: binary");
header("Pragma:public");
header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
header("Content-Disposition: attachment; filename=".$PHP_num);
header("Pragma: no-cache");
header("Expires: 0");
readfile('./smpl_marker/'.$PHP_num);

break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		job 24V sample MARKER download
#		case "smpl_marker_download":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "marker_download":
check_authority("061","view");

header("Content-type: application/octet-stream");
header("Content-Transfer-Encoding: binary");
header("Pragma:public");
header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
header("Content-Disposition: attachment; filename=".$PHP_num);
header("Pragma: no-cache");
header("Expires: 0");
readfile('./order_marker/'.$PHP_num);

break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "marker_smpl_del":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

case "marker_smpl_del":
check_authority("061","edit");

if($status = $Marker->del(1,$PHP_id,$PHP_smpl_id,$PHP_fab_type,$PHP_combo,$action)){
    $message = "DELETE sample marker: [".$PHP_num."] (".$GLOBALS['fab_type'][$PHP_fab_type].$PHP_combo.")";
    # 記錄使用者動態
    $log->log_add(0,"061D",$message);
}

if($action=='view')
    if($status==1)
        $redir_str = "marker.php?PHP_action=marker_smpl_view&PHP_smpl_id=".$PHP_smpl_id;
    else
        $redir_str = "marker.php?PHP_action=marker_smpl_add&PHP_id=".$PHP_id."&PHP_smpl_id=".$PHP_smpl_id;
else
    $redir_str = "marker.php?PHP_action=marker_smpl_add&PHP_id=".$PHP_id."&PHP_smpl_id=".$PHP_smpl_id;

redirect_page($redir_str);



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "marker_smpl_edit_del":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

case "marker_smpl_pdf_del":
check_authority("061","edit");

if($Marker->del_marker(1,$PHP_smpl_id,$PHP_marker)){
    # 記錄使用者動態
    $message = "DELETE sample marker PDF: [".$PHP_num."]";
    $log->log_add(0,"061D",$message);
}

if(!empty($PHP_add))
	$redir_str = "marker.php?PHP_action=marker_smpl_add&PHP_smpl_id=".$PHP_smpl_id;
else
	$redir_str = "marker.php?PHP_action=marker_smpl_edit&PHP_id=".$PHP_id;
if(!empty($action))
	$redir_str = "marker.php?PHP_action=marker_smpl_view&PHP_smpl_id=".$PHP_smpl_id;
// echo $redir_str;
// exit;
redirect_page($redir_str);



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "marker_ord_list":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

case "marker_ord_list":
check_authority("061","view");

// $parm = array(
// 'PHP_order_num'		=> $PHP_order_num ,
// 'PHP_style'			=> $PHP_style ,
// 'PHP_factory'		=> $PHP_factory ,
// 'PHP_cust'		    => $PHP_cust
// );

if ( !$op = $Marker->search(2) ) {
    $op['msg']= $order->msg->get(2);
    $layout->assign($op);
    $layout->display($TPL_ERROR);
    break;
}
// $op['cgi']= $parm;

foreach($op['marker'] as $keys => $vals){

    foreach($vals as $key => $val){
        if($key === 'order_num'){
            $op['marker'][$keys]['main_pic'] = _pic_url('big','50',3,'picture',$val,'jpg','','');
            $op['marker'][$keys]['marker'] = $val.'.mk.zip';
        }
        if($key === 'size'){
            $size = $size_des->get($val);
            $op['marker'][$keys]['size_range'] = $size['size_scale'];
            $op['marker'][$keys]['base_size'] = $size['base_size'];
        }
        if($key === 'id'){
            $mk = $Marker->get(2,'',$val);
            if( $mk['fab_type'] <> 0 ){
                $op['marker'][$keys]['fab_type'] = $GLOBALS['fab_type'][$mk['fab_type']];
            }else{
                $op['marker'][$keys]['fab_type'] = '';
            }
        }
    }
}

// $op['back_str'] = "&PHP_s_num=$PHP_s_num&PHP_s_style=$PHP_s_style&PHP_sr_startno=$PHP_sr_startno";
page_display($op, "061", $TPL_MARKER_ORD_LIST);
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "marker_ord_add":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

case "marker_ord_add":
check_authority("061","view");

$op = $order->get('',$PHP_order_num);

if (!$op) {
    $op['msg'][] = "沒有該樣本單紀錄，請重新輸入！";
    $layout->assign($op);
    $layout->display($TPL_ERROR);
    break;
}

#$op['select_fab']	 = $Marker->select_fab(2,$op['id']);
$op['select_fab']	 = $GLOBALS['arry2']->select_id($GLOBALS['fab_type'],'','PHP_fab','select_fab','','');
$op['choic_rmk'] = $Marker->choic_rmk();
$op['select_unit'] = $Marker->select_unit(2);

$marker = $op['order_num'].'.mk.zip';
if(file_exists($GLOBALS['config']['root_dir'].'./order_marker/'.$marker)){
    $op['marker']	= $marker;
}

# 檢查 相片是否存在 2008.10.29
$op['main_pic'] = _pic_url('big','120',2,'picture',$op['order_num'],'jpg','','');

$length_total_sum = 0;
$fy=$op['factory'];
$sql_str4marker_waste = "SELECT set_value FROM para_set WHERE set_name ='".$fy."_mark_waste_value'";
$op['marker_waste_value']= $Marker->get_marker_waste_value($sql_str4marker_waste);

if ($op['mks'] = $Marker->get(2,0,$op['id'],1,null,'',1)) {
    foreach($op['mks'] as $keys => $vals){
        if(is_array($vals)){
            foreach($vals as $key => $val){
                if($key === 'ord_id'){
                    $op['mks'][$keys]['marker'] = $Marker->marker_list($val,$op['mks'][$keys]['fab_type'],$op['mks'][$keys]['combo']);
                    $op['mks'][$keys]['averages']  = $Marker->marker_list2(2,0,$val,1,$op['mks'][$keys]['fab_type'],$op['mks'][$keys]['combo'],$op['size']);
                }

                if($key === 'unit_type'){
                    $op['mks'][$keys]['unit'] = ($val)? $op['unit'] = $GLOBALS['unit_type2'][$val] : '';
                }
                if($key === 'fab_type'){
                    if( $val <> 0 ){
                        $op['mks'][$keys]['fab_t'] = $GLOBALS['fab_type'][$val];
                    }else{
                        $op['mks'][$keys]['fab_t'] = '';
                    }
                }
                if($key === 'remark'){
                    $op['mks'][$keys]['remark'] = $Marker->rmk_list($val);
                }
            }
			$op['mks'][$keys]['marker_waste'] = $op['marker_waste_value'];//單拉只有一層
			$op['mks'][$keys]['length_total'] = $op['mks'][$keys]['averages']+$op['mks'][$keys]['marker_waste'];
        }
    }
}


if(!$op['ord'] = $wi->get(0,$PHP_order_num)){    //取出該筆 製造令記錄
    $op['msg']= $wi->msg->get(2);
    $layout->assign($op);
    $layout->display($TPL_ERROR);
    break;
}
$op['wi_id'] = $op['ord']['id'];
$where_str = " WHERE wi_id='".$op['ord']['id']."' ";
$T_wiqty = $GLOBALS['wiqty']->get_fields('id,wi_id,colorway,qty',$where_str);

if(!$op['ord'] = $GLOBALS['order']->get($op['ord']['smpl_id'])){
    $this->msg->add("Error ! Can't find this order record!");
    return false;
}

$size_A = $size_des->get($op['ord']['size']);

# 主要版資料
if(empty($PHP_id))$PHP_id=null;
if(empty($PHP_ord_id))$PHP_ord_id=null;
if(empty($PHP_fab_type))$PHP_fab_type=null;
if(empty($PHP_combo))$PHP_combo=null;
$op['mk'] = $Marker->get(2,$op['mks'][0]['id'],$op['mks'][0]['ord_id'],null,$op['mks'][0]['fab_type'],$op['mks'][0]['combo']);


# 與現有資料相減
if(empty($PHP_status))$PHP_status='';
$op['list'] = $Marker->report_title($PHP_order_num,$T_wiqty,$size_A,'',$op['mk'],$PHP_status);

if(isset($PHP_msg))$op['msg'][] = $PHP_msg;

page_display($op, "061", $TPL_MARKER_ORD_ADD);
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "marker_ord_up":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

case "marker_ord_up":
check_authority("061","add");

$perm = array(
'order_num'		=> $PHP_order_num ,
'ord_id'			=> $PHP_ord_id ,
'marker'		 	=> $PHP_marker ,
'save_path'         => dirname(__FILE__).'\order_marker\\',
'updator'		 	=> $GLOBALS['SCACHE']['ADMIN']['login_id']
);

if($PHP_id = $Marker->marker_ord_upload($perm)){
    $message = "Upload order marker : [".$PHP_order_num.".mk.zip]";
    # 記錄使用者動態
    $log->log_add(0,"061U",$message);
}


if(empty($PHP_append)){
    $redir_str = "marker.php?PHP_action=marker_ord_view&PHP_id=".$PHP_id;
    redirect_page($redir_str);
}else{
    $redir_str = "marker.php?PHP_action=marker_ord_add&PHP_order_num=".$PHP_order_num;
    redirect_page($redir_str);
}



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "marker_ord_do_add":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

case "marker_ord_do_add":
check_authority("061","add");

if(!$PHP_lots_code)
{
    $message = "Please choice a fabric number first!";
    $redir_str = "marker.php?PHP_action=marker_ord_add&PHP_order_num=".$PHP_order_num."&PHP_msg=".$message;
    redirect_page($redir_str);
}


$perm = array(
'order_num'		=> $PHP_order_num ,
'ord_id'		=> $PHP_ord_id ,
'lots_code'		=> $PHP_lots_id,
'mk_num' 		=> null ,
'fab_type' 		=> $PHP_fab ,
'unit_type'		=> $PHP_unit ,
'width'			=> $PHP_width ,
'description'   => $PHP_description ,
'rmk'			=> $rmk ,
'updator'		=> $GLOBALS['SCACHE']['ADMIN']['login_id']
);

if($PHP_id = $Marker->ord_add($perm)){

    if(!empty($parm['fab_type'])){
        $combo_num = $Marker->get_combo(2,$parm['ord_id'],$parm['fab_type']);
        $combo = $combo_num+1;
    }

    # 記錄使用者動態
    $message = "append order marker: [".$PHP_order_num."] (".$GLOBALS['fab_type'][$PHP_fab].$combo.")";
    $log->log_add(0,"061A",$message);

    if(isset($rmk[1]))$order->update_field_num('fab_type',1, $PHP_order_num);
}

if(empty($PHP_append)){
    $redir_str = "marker.php?PHP_action=marker_ord_view&PHP_id=".$PHP_id;
    redirect_page($redir_str);
}else{
    $redir_str = "marker.php?PHP_action=marker_ord_add&PHP_order_num=".$PHP_order_num;
    redirect_page($redir_str);
}



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "marker_ord_report_add":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

case "marker_ord_report_add":
check_authority("061","add");

if(!$op = $wi->get(0,$PHP_order_num)){    //取出該筆 製造令記錄
    $op['msg']= $wi->msg->get(2);
    $layout->assign($op);
    $layout->display($TPL_ERROR);
    break;
}

$where_str = " WHERE wi_id='".$op['id']."' ";
$T_wiqty = $GLOBALS['wiqty']->get_fields('id,wi_id,colorway,qty',$where_str);

if(!$op['smpl'] = $GLOBALS['order']->get($op['smpl_id'])){
    $this->msg->add("Error ! Can't find this order record!");
    return false;
}

$size_A = $size_des->get($op['smpl']['size']);

# 主要版資料
if(empty($PHP_id))$PHP_id=null;
if(empty($PHP_ord_id))$PHP_ord_id=null;
if(empty($PHP_fab_type))$PHP_fab_type=null;
if(empty($PHP_combo))$PHP_combo=null;
$op['mk'] = $Marker->get(2,$PHP_id,$PHP_ord_id,null,$PHP_fab_type,$PHP_combo);

if(!$op['mk'])
redirect_page("marker.php?PHP_action=marker_ord_add&PHP_order_num=".$PHP_order_num);

$op['select_unit'] = $Marker->select_unit(2,$op['mk']['unit_type']);
$op['mks'] = $Marker->get(2,0,$op['mk']['ord_id'],1,$op['mk']['fab_type'],$op['mk']['combo']);

# 主料類別
$fab = $op['mk']['fab_type'];
$op['fab_type'] = $GLOBALS['fab_type'][$fab];

# Remarks 用布方法
$op['rmk'] = $Marker->rmk_list($op['mk']['remark']);

# Marker NUM
$op['mk_name'] = $PHP_order_num;

# 與現有資料相減
if(empty($PHP_status))$PHP_status='';
$op['list'] = $Marker->report_main($PHP_order_num,$T_wiqty,$size_A,$op['mk'],$PHP_status);

$averages = $clothes = $estimate = '';
$length_total_sum = 0;
$fy=$op['smpl']['factory'];
$sql_str4marker_waste = "SELECT set_value FROM para_set WHERE set_name ='".$fy."_mark_waste_value'";
$op['marker_waste_value']= $Marker->get_marker_waste_value($sql_str4marker_waste);
if($op['mks']){
    foreach($op['mks'] as $keys => $vals){
        if(is_array($vals)){
            foreach($vals as $key => $val){
                if($key === 'mk_num'){
                    if( !empty($val) or "$val" === '0' )
                        $op['mks'][$keys][$key] = $GLOBALS['ALPHA2'][$val];
                    else
                        $op['new'] = 'yes';
                }
                if($key === 'fab_type'){
                    $op['mks'][$keys]['mk_name'] = $Marker->fab_item($val);
                }
                if($key === 'assortment'){
                    $ppp = $nw_asmt = '';
                    if($val){
                        #echo $val.'<br>';
                        $asmt = explode('|',$val);
                        $s_cr = explode(',',$asmt[0]);#
                        $s_lv = explode(',',$asmt[1]);#
                        $s_asmt = explode(',',$asmt[2]);
                        $size = explode(',',$size_A['size']);
						$total_lv=0;
                        foreach( $s_cr as $skey => $sval ){
                            $nw_asmt .= $T_wiqty[$sval-1]['colorway'].' [ ';
                            #if( $s_asmt[$skey] ){
                                foreach( $s_asmt as $skeys => $svals ){
                                    if($svals){
                                        $nw_asmt .= $size[$skeys].' - <font color="blue"><b>'.$svals.'</b></font> , ';
                                    }
                                }
                            #}
                            $nw_asmt = substr($nw_asmt,0,-2);
                            $nw_asmt .= ']'.' x <font color="red"><b>'.$s_lv[$skey].'L</b></font><br>';
							$total_lv = $total_lv + $s_lv[$skey];
						}
                        $op['mks'][$keys][$key] = substr($nw_asmt,0,-2);
						$op['mks'][$keys]['marker_waste'] = $total_lv*$op['marker_waste_value']; //新增計算層數的耗損量
                    }

                    if($op['mks'][$keys]['length'] and !empty($val) ){
                        $asmts = $Marker->average($val,$op['mks'][$keys]['length'],$op['marker_waste_value']);
                        $op['mks'][$keys]['averages'] = $asmts['averages'];
                        $clothes += $op['mks'][$keys]['clothes'] = $asmts['clothes'];
                        $estimate += $op['mks'][$keys]['estimate'] = $asmts['estimate'];
						$consume += $op['mks'][$keys]['consume'] = $asmts['consume'];
                    }
					$op['mks'][$keys]['length_total'] = round($op['mks'][$keys]['clothes'],2)+round($op['mks'][$keys]['marker_waste'],2);
					$length_total_sum += $op['mks'][$keys]['length_total'];					
                }
            }
        }
    }
}
$op['select_unit'] = $Marker->select_unit(2,$op['mk']['unit_type']);
$op['unit'] = $GLOBALS['unit_type2'][$op['mk']['unit_type']];
$op['unit2'] = $GLOBALS['unit_type3'][$op['mk']['unit_type']];
if(!empty($op['mk']['mk_num']))$op['mk_num'] = $GLOBALS['ALPHA2'][$op['mk']['mk_num']];

$op['PHP_order_num'] = $PHP_order_num;
$op['PHP_id'] = $PHP_id;
$op['PHP_status'] = $PHP_status;
$op['wi_id'] = $op['id'];
if($clothes and $estimate){
	$op['averages'] = round(($clothes) / $estimate,2);
    //$op['averages'] = round($clothes / $estimate,2);
    $op['clothes'] = $clothes;
    $op['estimate'] = $estimate;
	$op['length_total_sum'] = $length_total_sum;
}
$op['marker_waste'] = round($op['marker_waste_value'],2);//單拉只有一層
$op['length_total'] = $op['averages']+$op['marker_waste'];

page_display($op, "061", $TPL_MARKER_ORD_REPORT);
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "marker_ord_report_view":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

case "marker_ord_report_view":
check_authority("061","view");

if(!$op = $wi->get(0,$PHP_order_num)){    //取出該筆 製造令記錄
    $op['msg']= $wi->msg->get(2);
    $layout->assign($op);
    $layout->display($TPL_ERROR);
    break;
}

$where_str = " WHERE wi_id='".$op['id']."' ";
$T_wiqty = $GLOBALS['wiqty']->get_fields('id,wi_id,colorway,qty',$where_str);

if(!$op['smpl'] = $GLOBALS['order']->get($op['smpl_id'])){
    $this->msg->add("Error ! Can't find this order record!");
    return false;
}

$size_A = $size_des->get($op['smpl']['size']);

# 主要版資料
if(empty($PHP_id))$PHP_id=null;
if(empty($PHP_ord_id))$PHP_ord_id=null;
if(empty($PHP_fab_type))$PHP_fab_type=null;
if(empty($PHP_combo))$PHP_combo=null;
$op['mk'] = $Marker->get(2,$PHP_id,$PHP_ord_id,null,$PHP_fab_type,$PHP_combo);
if(!$op['mk'])
redirect_page("marker.php?PHP_action=marker_ord_add&PHP_order_num=".$PHP_order_num);

$op['select_unit'] = $Marker->select_unit(2,$op['mk']['unit_type']);
$op['mks'] = $Marker->get(2,0,$op['mk']['ord_id'],1,$op['mk']['fab_type'],$op['mk']['combo']);

# 主料類別
$fab = $op['mk']['fab_type'];
$op['fab_type'] = $GLOBALS['fab_type'][$fab];

# Remarks 用布方法
$op['rmk'] = $Marker->rmk_list($op['mk']['remark']);

# Marker NUM
$op['mk_name'] = $PHP_order_num;

# 與現有資料相減
if(empty($PHP_status))$PHP_status='';
$op['list'] = $Marker->report_title($PHP_order_num,$T_wiqty,$size_A,'',$op['mk'],$PHP_status);

$averages = $clothes = $estimate = '';
$length_total_sum = 0;
$fy=$op['smpl']['factory'];
$sql_str4marker_waste = "SELECT set_value FROM para_set WHERE set_name ='".$fy."_mark_waste_value'";
$op['marker_waste_value']= $Marker->get_marker_waste_value($sql_str4marker_waste);
if($op['mks']){
    foreach($op['mks'] as $keys => $vals){
        if(is_array($vals)){
            foreach($vals as $key => $val){
                if($key === 'fab_type'){
                    $op['mks'][$keys]['mk_name'] = $Marker->fab_item($val);
                }
                if($key === 'mk_num'){
                    if( !empty($val) or "$val" === '0' )
                        $op['mks'][$keys][$key] = $GLOBALS['ALPHA2'][$val];
                    else
                        $op['new'] = 'yes';
                }
                if($key === 'assortment'){
                    $ppp = $nw_asmt = '';
                    if($val){
                        #echo $val.'<br>';
                        $asmt = explode('|',$val);
                        $s_cr = explode(',',$asmt[0]);#
                        $s_lv = explode(',',$asmt[1]);#
                        $s_asmt = explode(',',$asmt[2]);
                        $size = explode(',',$size_A['size']);
						$total_lv=0;
                        foreach( $s_cr as $skey => $sval ){
                            #echo $skey.'<br>';
                            $nw_asmt .= $T_wiqty[$sval-1]['colorway'].' [ ';
                            #if( $s_asmt[$skey] ){
                                foreach( $s_asmt as $skeys => $svals ){
                                    if($svals){
                                        $nw_asmt .= $size[$skeys].' - <font color="blue"><b>'.$svals.'</b></font> , ';
                                    }
                                }
                            #}
                            $nw_asmt = substr($nw_asmt,0,-2);
                            $nw_asmt .= ']'.' x <font color="red"><b>'.$s_lv[$skey].'L</b></font><br>';
							$total_lv = $total_lv + $s_lv[$skey];
						}
                        $op['mks'][$keys][$key] = substr($nw_asmt,0,-2);
						$op['mks'][$keys]['marker_waste'] = $total_lv*$op['marker_waste_value']; //新增計算層數的耗損量
                    }

                    if($op['mks'][$keys]['length'] and !empty($val) ){
                        //$asmts = $Marker->average($val,$op['mks'][$keys]['length']);
						$asmts = $Marker->average($val,$op['mks'][$keys]['length'],$op['marker_waste_value']);
                        $op['mks'][$keys]['averages'] = $asmts['averages'];
                        $clothes += $op['mks'][$keys]['clothes'] = $asmts['clothes'];
                        $estimate += $op['mks'][$keys]['estimate'] = $asmts['estimate'];
						$consume += $op['mks'][$keys]['consume'] = $asmts['consume'];
                    }
					$op['mks'][$keys]['length_total'] = round($op['mks'][$keys]['clothes'],2)+round($op['mks'][$keys]['marker_waste'],2);
					$length_total_sum += $op['mks'][$keys]['length_total'];
                }
            }
        }
    }
}
$op['select_unit'] = $Marker->select_unit(2,$op['mk']['unit_type']);
$op['unit'] = $GLOBALS['unit_type2'][$op['mk']['unit_type']];
$op['unit2'] = $GLOBALS['unit_type3'][$op['mk']['unit_type']];
$op['PHP_order_num'] = $PHP_order_num;
$op['PHP_id'] = $PHP_id;
$op['wi_id'] = $op['id'];
if($clothes and $estimate){
    //$op['averages'] = round($clothes / $estimate,2);
	$op['averages'] = round(($clothes) / $estimate,2);
    $op['clothes'] = $clothes;
    $op['estimate'] = $estimate;
	$op['length_total_sum'] = $length_total_sum;
}
$op['marker_waste'] = round($op['marker_waste_value'],2);//單拉只有一層
$op['length_total'] = $op['averages']+$op['marker_waste'];
page_display($op, "061", $TPL_MARKER_ORD_REPORT_VIEW);
break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "marker_report_ord_do_add":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

case "marker_ord_report_do_add":
check_authority("061","add");

# asmt
$cr_str = $lv_str = $cw_str = '';
foreach($cr as $key => $val){
    if($val=='on' && $lv[$key])$ac1=1;
    $cr_str .=  $key.',';
    $lv_str .=  $lv[$key].',';
}
$cr_str = substr($cr_str,0,-1);
$lv_str = substr($lv_str,0,-1);

foreach($cw as $key => $val){
    if($val)$ac2=1;
    $cw_str .=  $val.',';
}
$cw_str = substr($cw_str,0,-1);

if($ac1 && $ac2){
    $assortment = $cr_str.'|'.$lv_str.'|'.$cw_str;
    $parm = array(
    'ord_id' 			=> $PHP_ord_id ,
    'mk_num' 			=> $PHP_mk ,
    'fab_type'		=> $PHP_fab_type ,
    'lots_code'		=> $PHP_lots_code,
    'unit_type'		=> $PHP_unit ,
    'width' 			=> $PHP_width ,
    'combo'				=> $PHP_combo ,
    'remark' 			=> $PHP_remark ,
    'assortment'	=> $assortment ,
    'length'			=> $PHP_length ,
    'updator'		 	=> $GLOBALS['SCACHE']['ADMIN']['login_id']
    );

    if($PHP_status == 'append'){

        if($Marker->ord_add($parm,$PHP_status)){

            if(!empty($parm['fab_type'])){
                $combo_num = $Marker->get_combo(2,$parm['ord_id'],$parm['fab_type']);
                $combo = $combo_num+1;
            }

            # 記錄使用者動態
            $message = "append order marker: [".$PHP_order_num."] (".$GLOBALS['fab_type'][$PHP_fab_type].$combo.") ".$GLOBALS['ALPHA2'][$PHP_mk]."";
            $log->log_add(0,"061A",$message);
        }


        $redir_str = "marker.php?PHP_action=marker_ord_report_add&PHP_order_num=".$PHP_order_num."&PHP_id=".$PHP_id;
    }else if($PHP_status == 'edit'){
        $Marker->update_field_num(2,'mk_num',$PHP_mk,$PHP_id,$PHP_order_num);
        $Marker->update_field_num(2,'assortment',$assortment,$PHP_id,$PHP_order_num);
        $Marker->update_field_num(2,'length',$PHP_length,$PHP_id,$PHP_order_num);
		
		# 記錄使用者動態
		$message = "update order marker: [".$PHP_order_num."]  ".$GLOBALS['ALPHA2'][$PHP_mk]."";
		$log->log_add(0,"061E",$message);		
        $redir_str = "marker.php?PHP_action=marker_ord_report_add&PHP_order_num=".$PHP_order_num."&PHP_id=".$PHP_id;
    }
}else{
    $redir_str = "marker.php?PHP_action=marker_ord_report_add&PHP_order_num=".$PHP_order_num."&PHP_id=".$PHP_id;
}

redirect_page($redir_str);



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "marker_file_del":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

case "marker_file_del":
check_authority("061","edit");

if($Marker->del_marker(2,$PHP_id,$PHP_marker)){
    # 記錄使用者動態
    $message = "DELETE order marker ZIP: [".$PHP_order_num."]";
    $log->log_add(0,"061D",$message);
}


$redir_str = "marker.php?PHP_action=marker_ord_add&PHP_order_num=".$PHP_order_num."&PHP_id=".$PHP_id;
redirect_page($redir_str);



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "marker_ord_del":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

case "marker_ord_del":
check_authority("061","edit");

if($status = $Marker->del(2,$PHP_id,$PHP_ord_id,$PHP_fab_type,$PHP_combo,$PHP_status)){
    # 記錄使用者動態
    if(empty($PHP_mk_num))$PHP_mk_num='';
    $message = "DELETE order marker: [".$PHP_order_num."] (".$GLOBALS['fab_type'][$PHP_fab_type].$PHP_combo.") ".$GLOBALS['ALPHA2'][$PHP_mk].$PHP_mk_num."";
    $log->log_add(0,"061D",$message);
}

if(empty($PHP_status))
    $redir_str = "marker.php?PHP_action=marker_ord_add&PHP_order_num=".$PHP_order_num;
else
    if($status==2)
        $redir_str = "marker.php?PHP_action=marker_ord_add&PHP_order_num=".$PHP_order_num;
    else
        $redir_str = "marker.php?PHP_action=marker_ord_report_add&PHP_order_num=".$PHP_order_num."&PHP_ord_id=".$PHP_ord_id."&PHP_fab_type=".$PHP_fab_type."&PHP_combo=".$PHP_combo;

redirect_page($redir_str);



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "marker_ord_edit":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

case "marker_ord_edit":
check_authority("061","edit");

$Marker->update_field_ord('unit_type',$PHP_unit,$PHP_ord_id,$PHP_fab_type ,$PHP_combo,$PHP_order_num);
$Marker->update_field_ord('width',$PHP_width,$PHP_ord_id ,$PHP_fab_type ,$PHP_combo,$PHP_order_num);
$Marker->update_field_ord('description',$PHP_description,$PHP_ord_id ,$PHP_fab_type ,$PHP_combo,$PHP_order_num);
if($PHP_lots_id) $Marker->update_field_ord('lots_code',$PHP_lots_id,$PHP_ord_id ,$PHP_fab_type ,$PHP_combo,$PHP_order_num);

$redir_str = "marker.php?PHP_action=marker_ord_report_add&PHP_order_num=".$PHP_order_num."&PHP_ord_id=".$PHP_ord_id."&PHP_fab_type=".$PHP_fab_type."&PHP_combo=".$PHP_combo;
redirect_page($redir_str);




#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "marker_ord_report_view":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

case "marker_pdf":
check_authority("061","view");

if(!$op = $wi->get(0,$PHP_order_num)){    //取出該筆 製造令記錄
    $op['msg']= $wi->msg->get(2);
    $layout->assign($op);
    $layout->display($TPL_ERROR);
    break;
}

$where_str = " WHERE wi_id='".$op['id']."' ";
$T_wiqty = $GLOBALS['wiqty']->get_fields('id,wi_id,colorway,qty',$where_str);

if(!$op['smpl'] = $GLOBALS['order']->get($op['smpl_id'])){
    $this->msg->add("Error ! Can't find this order record!");
    return false;
}

$size_A = $size_des->get($op['smpl']['size']);

# 主要版資料
if(empty($PHP_id))$PHP_id=null;
if(empty($PHP_ord_id))$PHP_ord_id=null;
if(empty($PHP_fab_type))$PHP_fab_type=null;
if(empty($PHP_combo))$PHP_combo=null;
$op['mk'] = $Marker->get(2,$PHP_id,$PHP_ord_id,null,$PHP_fab_type,$PHP_combo);
if(!$op['mk'])
redirect_page("marker.php?PHP_action=marker_ord_add&PHP_order_num=".$PHP_order_num);

$op['select_unit'] = $Marker->select_unit(2,$op['mk']['unit_type']);
$op['mks'] = $Marker->get(2,0,$op['mk']['ord_id'],1,$op['mk']['fab_type'],$op['mk']['combo']);

# 主料類別
$fab = $op['mk']['fab_type'];
$op['fab_type'] = $GLOBALS['fab_type'][$fab];

# Remarks 用布方法
$op['rmk'] = $Marker->rmk_list($op['mk']['remark']);

# Marker NUM
$op['mk_name'] = $PHP_order_num;

# 與現有資料相減
if(empty($PHP_status))$PHP_status='';
$op['list'] = $Marker->report_title($PHP_order_num,$T_wiqty,$size_A,'',$op['mk'],$PHP_status);

$averages = $clothes = $estimate = '';
if($op['mks']){
    foreach($op['mks'] as $keys => $vals){
        if(is_array($vals)){
            foreach($vals as $key => $val){
                if($key === 'fab_type'){
                    $op['mks'][$keys]['mk_name'] = $Marker->fab_item($val);
                }
                if($key === 'mk_num'){
                    if( !empty($val) or "$val" === '0' )
                        $op['mks'][$keys][$key] = $GLOBALS['ALPHA2'][$val];
                    else
                        $op['new'] = 'yes';
                }
                if($key === 'assortment'){
                    $ppp = $nw_asmt = $colorway = $asmt_p = $level = '' ;
                    if($val){
                        #echo $val.'<br>';
                        $asmt = explode('|',$val);
                        $s_cr = explode(',',$asmt[0]);#
                        $s_lv = explode(',',$asmt[1]);#
                        $s_asmt = explode(',',$asmt[2]);
                        $size = explode(',',$size_A['size']);
                        foreach( $s_cr as $skey => $sval ){
                            $colorway = $T_wiqty[$sval-1]['colorway'];
                            $nw_asmt .= $T_wiqty[$sval-1]['colorway'].' [ ';
                            #if( $s_asmt[$skey] ){
                                foreach( $s_asmt as $skeys => $svals ){
                                    if($svals){
                                        $asmt_p.= $size[$skeys].' - '.$svals.' , ';
                                        $nw_asmt .= $size[$skeys].' - <font color="blue"><b>'.$svals.'</b></font> , ';
                                    }
                                }
                            #}
                            $nw_asmt = substr($nw_asmt,0,-2);
                            $nw_asmt .= ']'.' x <font color="red"><b>'.$s_lv[$skey].'L</b></font><br>';
                            $op['mks'][$keys]['colorway'][$skey] = $colorway;
                            $op['mks'][$keys]['asmts'][$skey] = substr($asmt_p,0,-2);
                            $asmt_p='';
                            $op['mks'][$keys]['level'][$skey] = $s_lv[$skey];
                        }
                        $op['mks'][$keys][$key] = substr($nw_asmt,0,-2);
                    }

                    if($op['mks'][$keys]['length'] and !empty($val) ){
                        $asmts = $Marker->average($val,$op['mks'][$keys]['length']);
                        $op['mks'][$keys]['averages'] = $asmts['averages'];
                        $clothes += $op['mks'][$keys]['clothes'] = $asmts['clothes'];
                        $estimate += $op['mks'][$keys]['estimate'] = $asmts['estimate'];
                    }
                }
            }
        }
    }
}
$op['select_unit'] = $Marker->select_unit(2,$op['mk']['unit_type']);
$op['unit'] = $GLOBALS['unit_type4'][$op['mk']['unit_type']];
$op['unit2'] = $GLOBALS['unit_type3'][$op['mk']['unit_type']];
$op['PHP_order_num'] = $PHP_order_num;
$op['PHP_id'] = $PHP_id;
if($clothes and $estimate){
    $op['averages'] = $clothes / $estimate;
    $op['clothes'] = $clothes;
    $op['estimate'] = $estimate;
}

include_once($config['root_dir']."/lib/class.pdf_marker.php");

$print_title = "Marker";
$print_title2 = "  on  ".$op['mk']['last_update'];
$creator = $op['mk']['updator'];
$SetSubject = $op['mk_name'];

// 檢查 相片是否存在
if(file_exists($GLOBALS['config']['root_dir']."/picture/".$op['wi_num'].".jpg")){
    $main_pic = "./picture/".$op['wi_num'].".jpg";
} else {
    $main_pic = "./images/graydot.jpg";
}

if(empty($op['averages']))$op['averages']='';
$_SESSION['PDF']['head'] = array(
'mk_name'		=> $op['mk_name'] ,
'fab_type'	=> $op['fab_type'].$op['mk']['combo'] ,
'unit'			=> $op['unit'] ,
'unit2'			=> $op['unit2'] ,
'width'			=> $op['mk']['width'] ,
'rmk'				=> $op['rmk'] ,
'averages'	=> $op['averages'] ,
'pic_url'		=> $main_pic ,
);


$print_ort = 1;
# 輸出頁面設定
$ORT = (empty($print_ort))? 'P' : 'L';
$FMT = 'A4';
$UNT = 'mm';
$pdf =new PDF_marker();
$pdf->FPDF($ORT,$UNT,$FMT);

# 預設線寬
$_SESSION['PDF']['line'] = '.2';
# 增加每隔資料的寬度
$_SESSION['PDF']['vw'] = 0;
# 增加每隔欄位的寬度
$_SESSION['PDF']['iw'] = 0;
# A4 大小的寬度

if($ORT=='P'){
    $_SESSION['PDF']['w'] = $tmp_full_w = 190;	# 預設可用寬度
    $_SESSION['PDF']['h'] = 285; 								# 預設可用高度
}else{
    $_SESSION['PDF']['w'] = $tmp_full_w = 275;	# 預設可用寬度
    $_SESSION['PDF']['h'] = 200;								# 預設可用高度
}
$_SESSION['PDF']['cut'] = 0;
$_SESSION['PDF']['lh'] = 7;

$pdf->SetAutoPageBreak('on',10);
$pdf->SetRightMargin(10);
$pdf->SetLeftMargin(10);
$pdf->AddBig5Font();
$pdf->SetDrawColor('0,0,0');
$pdf->SetTextColor('0,0,0');
$pdf->AddPage();

# --[檔案說明]---------------------------------
$pdf->SetCreator('SCM');					# 應用程式
$pdf->SetAuthor($creator);				# 作者
$pdf->SetTitle($print_title);			# 標題
$pdf->SetSubject($SetSubject);		# 主題
$pdf->SetKeywords('Marker');			# 關鍵字
# ---------------------------------------------

//$pdf->hend_title($_SESSION['PDF']['head']);
$pdf->assortment();
$pdf->marker_list();


/*
$pdf->org_fty();
$pdf->material();
if(!empty($op['tran_log']))
$pdf->remark();
$pdf->new_fty();
*/
$name=$op['mk_name'].'_'.$op['fab_type'].$op['mk']['combo'].'_marker.pdf';
$pdf->Output($name,'D');
unset($_SESSION['PDF']);
break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "show_fabric_search":
			if(!$op = $wi->get_all($PHP_id)){    //取出該筆 製造令記錄 ID
						$op['msg']= $wi->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
			}
/*
	//  取出  BOM  主料記錄 --------------------------------------------------------------
		 $op['bom_lots_NONE']= '';
	 	 $lots_use = $apply->get_lots_det($op['wi']['id']);  //取出該筆 bom 內ALL主料記錄
		 $op['lots_use'] = array();
		 for($i=0; $i<sizeof($lots_use); $i++)
		 {
		 		$mk = 0;
		 		
		 		for($j=0; $j<sizeof($op['lots_use']); $j++)
		 		{
		 			
		 			if($op['lots_use'][$j]['lots_code'] == $lots_use[$i]['lots_code']) $mk = 1;		 		
		 		}

		 		if($mk == 0)
		 		{
		 			
		 			$op['lots_use'][]= $lots_use[$i];
		 		}
		 }
		 
*/
		$op['msg'] = $wi->msg->get(2);
		if (isset($PHP_msg)) $op['msg'][] = $PHP_msg;

page_display($op, '052', $TPL_MARKER_VIEW_WI);	
break;





} // end case ---------
?>
