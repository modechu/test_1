<?php
session_start();
session_register	('SCACHE');
session_register	('PAGE');
session_register	('authority');
session_register	('where_str');
session_register	('parm');
session_register	('PHP_ses_etd');
session_register	('PHP_unstatus');

session_register	('sch_parm');
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

require_once "config.php";
require_once "config.admin.php";
require_once "init.object.php";

$PHP_SELF = $_SERVER['PHP_SELF'];
$perm = $GLOBALS['power'];
$op = array();
$TPL_MARKER_FTY	= 'marker_fty.html';
$TPL_MARKER_FTY_ADD = 'marker_fty_add.html';
$TPL_MARKER_FTY_REPORT = 'marker_fty_report.html';
$TPL_MARKER_FTY_LIST = 'marker_fty_list.html';
$TPL_MARKER_FTY_REPORT_VIEW = 'marker_fty_report_view.html';

$TPL_MARKER_RPT	= 'marker_rpt.html';
$TPL_MARKER_RPT_ADD = 'marker_rpt_add.html';
$TPL_MARKER_RPT_REPORT = 'marker_rpt_report.html';
$TPL_MARKER_RPT_LIST = 'marker_rpt_list.html';
$TPL_MARKER_RPT_REPORT_VIEW = 'marker_rpt_report_view.html';


$TPL_CUP_REPORT_VIEW = 'cut_report_view.html';

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

$GLOBALS['m_rmk'][1] = '[correspondence]';
$GLOBALS['m_rmk'][2] = '[single layer]';
$GLOBALS['m_rmk'][3] = '[face to face]';
$GLOBALS['m_rmk'][4] = '[two way cut]';
$GLOBALS['m_rmk'][5] = '[one way cut]';
// echo $PHP_action.'<br>';
switch ($PHP_action) {

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "marker_search":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

case "marker_search":
check_authority('063',"view");

$style_def = $style_type->get_fields('style_type');   // 取出 款式類別
$op['style_type_select'] =  $arry2->select($style_def,'','PHP_s_style','select','');
$op['style_type_select2'] =  $arry2->select($style_def,'','PHP_style','select','');
page_display($op,'063', $TPL_MARKER_FTY);
break;





#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		job 24V sample MARKER download
#		case "smpl_marker_download":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "marker_download":
check_authority('063',"view");

header("Content-type: application/octet-stream");
header("Content-Transfer-Encoding: binary");
header("Pragma:public");
header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
header("Content-Disposition: attachment; filename=".$PHP_num);
header("Pragma: no-cache");
header("Expires: 0");
readfile('./fty_marker/'.$PHP_num);

break;




#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "marker_ord_list":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

case "marker_ord_list":
check_authority('063',"view");
//	echo $SCH_finish;
//	if(!isset($SCH_finish))$SCH_finish=0;
if(isset($PHP_order_num))
{		
        $sch_parm = array();
        $sch_parm = array(	
                                    "PHP_order_num"		=>  $PHP_order_num,		"PHP_style"		=>	$PHP_style,
                                    'SCH_finish'			=>	$SCH_finish,
                                    "PHP_sr_startno"	=>	$PHP_sr_startno,	"PHP_action"	=>	$PHP_action
            );			
}else{
            if(isset($PHP_sr_startno))$sch_parm['PHP_sr_startno'] = $PHP_sr_startno;
}
if ( !$op = $fty_marker->search(2) ) {
    $op['msg']= $order->msg->get(2);
    $layout->assign($op);
    $layout->display($TPL_ERROR);
    break;
}
$op['cgi']= $sch_parm;

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
            $mk = $fty_marker->get(2,'',$val);
            if( $mk['fab_type'] <> 0 ){
                $op['marker'][$keys]['fab_type'] = $GLOBALS['fab_type'][$mk['fab_type']];
            }else{
                $op['marker'][$keys]['fab_type'] = '';
            }
        }
    }
    $assort = $fty_marker->get_main_shell($op['marker'][$keys]['id']);
        $op['marker'][$keys]['cut_qty'] = 0;
    for($i=0; $i<sizeof($assort); $i++)
    {
        $tmp = $fty_marker->average($assort[$i],10);			
        $op['marker'][$keys]['cut_qty'] += $tmp['estimate'];
    }
    
    
}
//	$op['msg']= $fty_marker->msg->get(2);
page_display($op,'063', $TPL_MARKER_FTY_LIST);
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "marker_ord_add":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

case "marker_ord_add":
check_authority('063',"view");

$op = $order->get('',$PHP_order_num);

$size_A = $size_des->get($op['size']);
$op['size_ccale']=explode(',',$size_A['size']);

$wi_rec = $wi->get('',$PHP_order_num);
$where_str = " WHERE wi_id='".$wi_rec['id']."' ";
$T_wiqty = $GLOBALS['wiqty']->get_fields('colorway,qty',$where_str);	
$T_wiqty = $wiqty->search(1,$where_str);	
$assort = $fty_marker->get_main_shell($op['id']);

$op['cut_assort'] = $fty_marker->creat_cut_assort($size_A['size'],$T_wiqty,$assort);

if (!$op) {
    $op['msg'][] = "沒有該樣本單紀錄，請重新輸入！";
    $layout->assign($op);
    $layout->display($TPL_ERROR);
    break;
}

#$op['select_fab']	 = $Marker->select_fab(2,$op['id']);
$op['select_fab']	 = $GLOBALS['arry2']->select_id($GLOBALS['fab_type'],'','PHP_fab','select_fab','','');
$op['choic_rmk'] = $fty_marker->choic_rmk();
$op['select_unit'] = $fty_marker->select_unit(2);

$marker = $op['order_num'].'.mk.zip';
if(file_exists($GLOBALS['config']['root_dir'].'./fty_marker/'.$marker)){
    $op['marker']	= $marker;
}

# 檢查 相片是否存在 2008.10.29
$op['main_pic'] = _pic_url('big','120',2,'picture',$op['order_num'],'jpg','','');

$length_total_sum = 0;
$fy=$op['factory'];
$sql_str4marker_waste = "SELECT set_value FROM para_set WHERE set_name ='".$fy."_mark_waste_value'";
$op['marker_waste_value']= $Marker->get_marker_waste_value($sql_str4marker_waste);

if ($op['mks'] = $fty_marker->get(2,0,$op['id'],1,null,'',1)) {
    foreach($op['mks'] as $keys => $vals){
        if(is_array($vals)){
            foreach($vals as $key => $val){
                if($key === 'ord_id'){
                    $op['mks'][$keys]['marker'] = $fty_marker->marker_list($val,$op['mks'][$keys]['fab_type'],$op['mks'][$keys]['combo']);
                    $op['mks'][$keys]['averages']  = $fty_marker->marker_list2(2,0,$val,1,$op['mks'][$keys]['fab_type'],$op['mks'][$keys]['combo'],$op['size']);
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
                    $op['mks'][$keys]['remark'] = $fty_marker->rmk_list($val);
                }
            }
			$op['mks'][$keys]['marker_waste'] = $op['marker_waste_value'];//單拉只有一層
			$op['mks'][$keys]['length_total'] = round($op['mks'][$keys]['averages'],2)+round($op['mks'][$keys]['marker_waste'],2);
			
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
$op['mk'] = $fty_marker->get(2,$op['mks'][0]['id'],$op['mks'][0]['ord_id'],null,$op['mks'][0]['fab_type'],$op['mks'][0]['combo']);


# 與現有資料相減
if(empty($PHP_status))$PHP_status='';
$op['list'] = $fty_marker->report_title($PHP_order_num,$T_wiqty,$size_A,'',$op['mk'],$PHP_status);

# REMARK
$op['mk_log'] = $fty_marker->get_log($PHP_order_num,'CP');
$op['mk_log']['des_view'] = str_replace( chr(13).chr(10), "<br>", $op['mk_log']['des'] );

if(isset($PHP_msg))$op['msg'][] = $PHP_msg;

page_display($op,'063', $TPL_MARKER_FTY_ADD);
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "marker_ord_up":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

case "marker_ord_up":
check_authority('063',"add");

$perm = array(
'order_num'		=> $PHP_order_num ,
'ord_id'			=> $PHP_ord_id ,
'marker'		 	=> $PHP_marker ,
'updator'		 	=> $GLOBALS['SCACHE']['ADMIN']['login_id']
);

if($PHP_id = $fty_marker->marker_ord_upload($perm)){
    $message = "Upload order marker : [".$PHP_order_num.".mk.zip]";
    # 記錄使用者動態
    $log->log_add(0,"063U",$message);
}


if(empty($PHP_append)){
    $redir_str = "fty_marker.php?PHP_action=marker_ord_view&PHP_id=".$PHP_id;
    redirect_page($redir_str);
}else{
    $redir_str = "fty_marker.php?PHP_action=marker_ord_add&PHP_order_num=".$PHP_order_num;
    redirect_page($redir_str);
}



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "marker_ord_do_add":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

case "marker_ord_do_add":
check_authority('063',"add");

if(!$PHP_lots_code)
{
    $message = "Please choice a fabric number first!";
    $redir_str = "fty_marker.php?PHP_action=marker_ord_add&PHP_order_num=".$PHP_order_num."&PHP_msg=".$message;
    redirect_page($redir_str);
}

$perm = array(
'order_num'		=> $PHP_order_num ,
'ord_id'			=> $PHP_ord_id ,
'mk_num' 			=> null ,
'fab_type' 		=> $PHP_fab ,
'lots_code'		=> $PHP_lots_id,
'unit_type'		=> $PHP_unit ,
'width'				=> $PHP_width ,
'rmk'					=> $rmk ,
'updator'		 	=> $GLOBALS['SCACHE']['ADMIN']['login_id']
);

if($PHP_id = $fty_marker->ord_add($perm)){
    $fty_marker->rpt_add($perm);
    if(!empty($parm['fab_type'])){
        $combo_num = $fty_marker->get_combo(2,$parm['ord_id'],$parm['fab_type']);
        $combo = $combo_num+1;
    }

    # 記錄使用者動態
    $message = "append order marker: [".$PHP_order_num."] (".$GLOBALS['fab_type'][$PHP_fab].$combo.")";
    $log->log_add(0,"063A",$message);
}

if(empty($PHP_append)){
    $redir_str = "fty_marker.php?PHP_action=marker_ord_view&PHP_id=".$PHP_id;
    redirect_page($redir_str);
}else{
    $redir_str = "fty_marker.php?PHP_action=marker_ord_add&PHP_order_num=".$PHP_order_num;
    redirect_page($redir_str);
}



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "marker_ord_report_add":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

case "marker_ord_report_add":
check_authority('063',"add");

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
$op['mk'] = $fty_marker->get(2,$PHP_id,$PHP_ord_id,null,$PHP_fab_type,$PHP_combo);
if(!$op['mk'])
{
    redirect_page("fty_marker.php?PHP_action=marker_ord_add&PHP_order_num=".$PHP_order_num);
}
//

$op['select_unit'] = $fty_marker->select_unit(2,$op['mk']['unit_type']);
$op['mks'] = $fty_marker->get(2,0,$op['mk']['ord_id'],1,$op['mk']['fab_type'],$op['mk']['combo']);

# 主料類別
$fab = $op['mk']['fab_type'];
$op['fab_type'] = $GLOBALS['fab_type'][$fab];

# Remarks 用布方法
$op['rmk'] = $fty_marker->rmk_list($op['mk']['remark']);

# Marker NUM
$op['mk_name'] = $PHP_order_num;

# 與現有資料相減
if(empty($PHP_status))$PHP_status='';
$op['list'] = $fty_marker->report_main($PHP_order_num,$T_wiqty,$size_A,$op['mk'],$PHP_status);

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
                    $op['mks'][$keys]['mk_name'] = $fty_marker->fab_item($val);
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
						$op['mks'][$keys]['marker_waste'] = $total_lv*$op['marker_waste_value']; 
					}

                    if($op['mks'][$keys]['length'] and !empty($val) ){
                        $asmts = $fty_marker->average($val,$op['mks'][$keys]['length']);
                        $op['mks'][$keys]['averages'] = $asmts['averages'];
                        $clothes += $op['mks'][$keys]['clothes'] = $asmts['clothes'];
                        $estimate += $op['mks'][$keys]['estimate'] = $asmts['estimate'];
                    }
					$op['mks'][$keys]['length_total'] = round($op['mks'][$keys]['clothes'],2)+round($op['mks'][$keys]['marker_waste'],2);
					$length_total_sum += $op['mks'][$keys]['length_total'];
                }
            }
        }
    }
}
$op['select_unit'] = $fty_marker->select_unit(2,$op['mk']['unit_type']);
$op['unit'] = $GLOBALS['unit_type2'][$op['mk']['unit_type']];
$op['unit2'] = $GLOBALS['unit_type3'][$op['mk']['unit_type']];
if(!empty($op['mk']['mk_num']))$op['mk_num'] = $GLOBALS['ALPHA2'][$op['mk']['mk_num']];

$op['PHP_order_num'] = $PHP_order_num;
$op['PHP_id'] = $PHP_id;
$op['PHP_status'] = $PHP_status;
$op['wi_id'] = $op['id'];
if($clothes and $estimate){
    $op['averages'] = $clothes / $estimate;
    $op['clothes'] = $clothes;
    $op['estimate'] = $estimate;
	$op['length_total_sum'] = $length_total_sum;
}
$op['marker_waste'] = $op['marker_waste_value'];//單拉只有一層
$op['length_total'] = round($op['averages'],2)+round($op['marker_waste'],2);
page_display($op,'063', $TPL_MARKER_FTY_REPORT);
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "marker_ord_report_view":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

case "marker_ord_report_view":
check_authority('063',"view");

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
$op['mk'] = $fty_marker->get(2,$PHP_id,$PHP_ord_id,null,$PHP_fab_type,$PHP_combo);
if(!$op['mk'])
redirect_page("fty_marker.php?PHP_action=marker_ord_add&PHP_order_num=".$PHP_order_num);

$op['select_unit'] = $fty_marker->select_unit(2,$op['mk']['unit_type']);
$op['mks'] = $fty_marker->get(2,0,$op['mk']['ord_id'],1,$op['mk']['fab_type'],$op['mk']['combo']);

# 主料類別
$fab = $op['mk']['fab_type'];
$op['fab_type'] = $GLOBALS['fab_type'][$fab];

# Remarks 用布方法
$op['rmk'] = $fty_marker->rmk_list($op['mk']['remark']);

# Marker NUM
$op['mk_name'] = $PHP_order_num;

# 與現有資料相減
if(empty($PHP_status))$PHP_status='';
$op['list'] = $fty_marker->report_title($PHP_order_num,$T_wiqty,$size_A,'',$op['mk'],$PHP_status);

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
                    $op['mks'][$keys]['mk_name'] = $fty_marker->fab_item($val);
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
						$op['mks'][$keys]['marker_waste'] = $total_lv*$op['marker_waste_value'];
                    }

                    if($op['mks'][$keys]['length'] and !empty($val) ){
                        $asmts = $fty_marker->average($val,$op['mks'][$keys]['length']);
                        $op['mks'][$keys]['averages'] = $asmts['averages'];
                        $clothes += $op['mks'][$keys]['clothes'] = $asmts['clothes'];
                        $estimate += $op['mks'][$keys]['estimate'] = $asmts['estimate'];
                    }
					$op['mks'][$keys]['length_total'] = round($op['mks'][$keys]['clothes'],2)+round($op['mks'][$keys]['marker_waste'],2);
					$length_total_sum += $op['mks'][$keys]['length_total'];
                }
            }
        }
    }
}
$op['select_unit'] = $fty_marker->select_unit(2,$op['mk']['unit_type']);
$op['unit'] = $GLOBALS['unit_type2'][$op['mk']['unit_type']];
$op['unit2'] = $GLOBALS['unit_type3'][$op['mk']['unit_type']];
$op['PHP_order_num'] = $PHP_order_num;
$op['PHP_id'] = $PHP_id;
$op['wi_id'] = $op['id'];
if($clothes and $estimate){
    $op['averages'] = $clothes / $estimate;
    $op['clothes'] = $clothes;
    $op['estimate'] = $estimate;
	$op['length_total_sum'] = $length_total_sum;
}
$op['marker_waste'] = $op['marker_waste_value'];//單拉只有一層
$op['length_total'] = round($op['averages'],2)+round($op['marker_waste'],2);
page_display($op,'063', $TPL_MARKER_FTY_REPORT_VIEW);
break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "marker_report_ord_do_add":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

case "marker_ord_report_do_add":
check_authority('063',"add");

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
    'fab_code'		=> $PHP_fab_code,
    'remain'			=> $PHP_remain,
    'updator'		 	=> $GLOBALS['SCACHE']['ADMIN']['login_id']
    );

    if($PHP_status == 'append'){

        if($fty_marker->ord_add($parm,$PHP_status)){

            if(!empty($parm['fab_type'])){
                $combo_num = $fty_marker->get_combo(2,$parm['ord_id'],$parm['fab_type']);
                $combo = $combo_num+1;
            }

            # 記錄使用者動態
            $message = "append order marker: [".$PHP_order_num."] (".$GLOBALS['fab_type'][$PHP_fab_type].$combo.") ".$GLOBALS['ALPHA2'][$PHP_mk]."";
            $log->log_add(0,"063A",$message);
        }


        $redir_str = "fty_marker.php?PHP_action=marker_ord_report_add&PHP_order_num=".$PHP_order_num."&PHP_id=".$PHP_id;
    }else if($PHP_status == 'edit'){

        $fty_marker->update_field_num('marker_fty','mk_num',$PHP_mk,$PHP_id,$PHP_order_num);
        $fty_marker->update_field_num('marker_fty','assortment',$assortment,$PHP_id,$PHP_order_num);
        $fty_marker->update_field_num('marker_fty','length',$PHP_length,$PHP_id,$PHP_order_num);
        $fty_marker->update_field_num('marker_fty','remain',$PHP_remain,$PHP_id,$PHP_order_num);
        $fty_marker->update_field_num('marker_fty','fab_code',$PHP_fab_code,$PHP_id,$PHP_order_num);

		# 記錄使用者動態
		$message = "update order marker: [".$PHP_order_num."]  ".$GLOBALS['ALPHA2'][$PHP_mk]."";
		$log->log_add(0,"063E",$message);
        $redir_str = "fty_marker.php?PHP_action=marker_ord_report_add&PHP_order_num=".$PHP_order_num."&PHP_id=".$PHP_id;
    }
}else{
    $redir_str = "fty_marker.php?PHP_action=marker_ord_report_add&PHP_order_num=".$PHP_order_num."&PHP_id=".$PHP_id;
}

redirect_page($redir_str);



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "marker_file_del":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

case "marker_file_del":
check_authority('063',"edit");

if($fty_marker->del_marker(2,$PHP_id,$PHP_marker)){
    # 記錄使用者動態
    $message = "DELETE order marker ZIP: [".$PHP_order_num."]";
    $log->log_add(0,"063D",$message);
}


$redir_str = "fty_marker.php?PHP_action=marker_ord_add&PHP_order_num=".$PHP_order_num."&PHP_id=".$PHP_id;
redirect_page($redir_str);



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "marker_ord_del":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

case "marker_ord_del":
check_authority('063',"edit");

if($status = $fty_marker->del(2,$PHP_id,$PHP_ord_id,$PHP_fab_type,$PHP_combo,$PHP_status)){
    # 記錄使用者動態
    if(empty($PHP_mk_num))$PHP_mk_num='';
    $message = "DELETE order marker: [".$PHP_order_num."] (".$GLOBALS['fab_type'][$PHP_fab_type].$PHP_combo.") ".$GLOBALS['ALPHA2'][$PHP_mk].$PHP_mk_num."";
    $log->log_add(0,"063D",$message);
}

if(empty($PHP_status))
    $redir_str = "fty_marker.php?PHP_action=marker_ord_add&PHP_order_num=".$PHP_order_num;
else
    if($status==2)
        $redir_str = "fty_marker.php?PHP_action=marker_ord_add&PHP_order_num=".$PHP_order_num;
    else
        $redir_str = "fty_marker.php?PHP_action=marker_ord_report_add&PHP_order_num=".$PHP_order_num."&PHP_ord_id=".$PHP_ord_id."&PHP_fab_type=".$PHP_fab_type."&PHP_combo=".$PHP_combo;

redirect_page($redir_str);



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "marker_ord_edit":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

case "marker_ord_edit":
check_authority('063',"edit");

$fty_marker->update_field_ord('unit_type',$PHP_unit,$PHP_ord_id,$PHP_fab_type ,$PHP_combo,$PHP_order_num);
$fty_marker->update_field_ord('width',$PHP_width,$PHP_ord_id ,$PHP_fab_type ,$PHP_combo,$PHP_order_num);
if($PHP_lots_id) $fty_marker->update_field_ord('lots_code',$PHP_lots_id,$PHP_ord_id ,$PHP_fab_type ,$PHP_combo,$PHP_order_num);

$redir_str = "fty_marker.php?PHP_action=marker_ord_report_add&PHP_order_num=".$PHP_order_num."&PHP_ord_id=".$PHP_ord_id."&PHP_fab_type=".$PHP_fab_type."&PHP_combo=".$PHP_combo;
redirect_page($redir_str);




#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "marker_pdf":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

case "marker_pdf":
check_authority('063',"view");

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
$op['mk'] = $fty_marker->get(2,$PHP_id,$PHP_ord_id,null,$PHP_fab_type,$PHP_combo);
if(!$op['mk'])
redirect_page("fty_marker.php?PHP_action=marker_ord_add&PHP_order_num=".$PHP_order_num);

$op['select_unit'] = $fty_marker->select_unit(2,$op['mk']['unit_type']);
$op['mks'] = $fty_marker->get(2,0,$op['mk']['ord_id'],1,$op['mk']['fab_type'],$op['mk']['combo']);

# 主料類別
$fab = $op['mk']['fab_type'];
$op['fab_type'] = $GLOBALS['fab_type'][$fab];

# Remarks 用布方法
$op['rmk'] = $fty_marker->rmk_list($op['mk']['remark']);

# Marker NUM
$op['mk_name'] = $PHP_order_num;

# 與現有資料相減
if(empty($PHP_status))$PHP_status='';
$op['list'] = $fty_marker->report_title($PHP_order_num,$T_wiqty,$size_A,'',$op['mk'],$PHP_status);

$averages = $clothes = $estimate = '';
if($op['mks']){
    foreach($op['mks'] as $keys => $vals){
        if(is_array($vals)){
            foreach($vals as $key => $val){
                if($key === 'fab_type'){
                    $op['mks'][$keys]['mk_name'] = $fty_marker->fab_item($val);
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
                        $asmts = $fty_marker->average($val,$op['mks'][$keys]['length']);
                        $op['mks'][$keys]['averages'] = $asmts['averages'];
                        $clothes += $op['mks'][$keys]['clothes'] = $asmts['clothes'];
                        $estimate += $op['mks'][$keys]['estimate'] = $asmts['estimate'];
                    }
                }
            }
        }
    }
}
$op['select_unit'] = $fty_marker->select_unit(2,$op['mk']['unit_type']);
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
$name=$op['mk_name'].'_'.$op['fab_type'].$op['mk']['combo'].'_cutting_plan.pdf';
$pdf->Output($name,'D');
unset($_SESSION['PDF']);
break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "fty_marker_des":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

case "fty_marker_des":
check_authority('063',"add");

$perm = array(
    'ord_num'		=> $PHP_order_num ,
    'item'			=> 'CP' ,
    'des'			 	=> $PHP_des,
    'user'		 	=> $GLOBALS['SCACHE']['ADMIN']['login_id'],
    'k_date'		=> date('Y-m-d'),
    'id'				=> $PHP_id
);

$PHP_id = $fty_marker->marker_log_upload($perm);


    $redir_str = "fty_marker.php?PHP_action=marker_ord_add&PHP_order_num=".$PHP_order_num;
    redirect_page($redir_str);





#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "marker_rpt_search":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

case "marker_rpt_search":
check_authority('064',"view");

    //2006/05/02 update
    $where_str = $manager = $dept_id = '';		 
    $user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];   // 判定進入身份的指標
    $team = $GLOBALS['SCACHE']['ADMIN']['team_id'];  // 判定進入身份的指標(team)
    $sales_dept_ary = get_sales_dept();// 取出 業務的部門 [不含K0] ------
    for ($i=0; $i<count($sales_dept_ary);$i++){
        if($user_dept == $sales_dept_ary[$i]){

            // 如果是業務部進入 則dept_code 指定該業務部---
            $dept_id = $sales_dept_ary[$i];  
        }
    }
    $op['dept_id'] = $dept_id;
    if (!$dept_id || $team<>'MD') {    // 當不是業務部人[也不含 K0 的人 ]進入時
        $op['manager_flag'] = 1;
        $manager_v = 1;
        //業務部門 select選單
        $op['dept_id'] = $arry2->select($sales_dept_ary,"","PHP_dept_code","select",""); 

    }
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
    // creat sample type combo box
    $sample_def = $smpl_type->get_fields('smpl_type');   // 取出 樣本類別
    $op['smpl_type_select'] =  $arry2->select($sample_def,'','PHP_smpl_type','select','');  	

//080725message增加		
$note=$notify->search($GLOBALS['SCACHE']['ADMIN']['login_id'], "`read`=0");
$op['max_notify'] = $note['max_no'];

page_display($op,'064', $TPL_MARKER_RPT);
break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "marker_rpt_list":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

case "marker_rpt_list":
check_authority('064',"view");

if(isset($PHP_num))
{
        $sch_parm = array();
        $sch_parm = array(	
                                    "PHP_num"					=>  $PHP_num,					"PHP_sch_fty"		=>	$PHP_sch_fty,
                                    "PHP_etdfsh"			=>	$PHP_etdfsh,			"PHP_etdstr"		=>	$PHP_etdstr,
                                    "PHP_cust"				=>	$PHP_cust,
                                    "PHP_sr_startno"	=>	$PHP_sr_startno,	"PHP_action"		=>	$PHP_action
            );			
}else{
            if(isset($PHP_sr_startno))$sch_parm['PHP_sr_startno'] = $PHP_sr_startno;
}
if ( !$op = $fty_marker->search_cut_report(2) ) {
    $op['msg']= $order->msg->get(2);
    $layout->assign($op);
    $layout->display($TPL_ERROR);
    break;
}
$op['cgi']= $sch_parm;

$where_str = " AND (lots_use.use_for like '%shell%' OR lots_use.use_for like '%combo%')";
foreach($op['marker'] as $key => $vals){

    $bom_lots = $po->cut_report_lots_det($op['marker'][$key]['wi_id'],$where_str,$op['marker'][$key]['order_num']);  //取出該筆 bom 內ALL主料記錄
    $op['marker'][$key]['bom_use'] = $op['marker'][$key]['mark_use'] = $op['marker'][$key]['cut_use'] = 0;
    $op['marker'][$key]['po_qty'] = $op['marker'][$key]['rcv_qty'] = $op['marker'][$key]['cut_qty'] = 0;
    $lots_tmp = array();
    $cut_avg = $mrk_avg = 0;
    
    for($i=0; $i<sizeof($bom_lots); $i++)
    {
        $mk = false;
        foreach($lots_tmp as $lt_key => $lt_value)//判斷是否是第一次出現這個主料
        {
            if($lt_key == $bom_lots[$i]['use_id'])
            {
                $mk = true;
                break;
            }
        }
        
        if(!$mk) //計算馬克,cut plan及bom的用量加總(shell + comble)
        {
            $cut_avg = $fty_marker->get_lots_use_mk($bom_lots[$i]['use_id'],$op['marker'][$key]['id']);
            $mrk_avg = $Marker->get_lots_use_mk($bom_lots[$i]['use_id'],$op['marker'][$key]['id']);
            $op['marker'][$key]['bom_use'] += $bom_lots[$i]['est_1'];
            $op['marker'][$key]['mark_use'] += $mrk_avg;
            $op['marker'][$key]['cut_use'] += $cut_avg;
            $lots_tmp[$bom_lots[$i]['use_id']] = $cut_avg;
        }
        //計算採購,驗收,裁剪總數
        // if(isset($bom_lots[$i]['po']['qty']))$op['marker'][$key]['po_qty'] += $bom_lots[$i]['po']['qty'];
		$op['marker'][$key]['po_qty'] = $fty_marker->get_po_qty($vals['order_num'], "l");
		if(isset($bom_lots[$i]['po']['rcv_qty']))$op['marker'][$key]['rcv_qty'] += $bom_lots[$i]['po']['rcv_qty'];
        $bom_qty = explode(',',$bom_lots[$i]['qty']);
        for($j=0; $j<sizeof($bom_qty); $j++)
        {			
            $op['marker'][$key]['cut_qty'] += $bom_qty[$j] / $bom_lots[$i]['est_1'] * $lots_tmp[$bom_lots[$i]['use_id']];
    }
    }
    
    
    
}
//	$op['msg']= $fty_marker->msg->get(2);
page_display($op,'064', $TPL_MARKER_RPT_LIST);
break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "marker_rpt_add":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

case "marker_rpt_add":
check_authority('064',"view");

$op = $order->get('',$PHP_order_num);

if (!$op) {
    $op['msg'][] = "沒有該樣本單紀錄，請重新輸入！";
    $layout->assign($op);
    $layout->display($TPL_ERROR);
    break;
}

#$op['select_fab']	 = $Marker->select_fab(2,$op['id']);
$op['select_fab']	 = $GLOBALS['arry2']->select_id($GLOBALS['fab_type'],'','PHP_fab','select_fab','','');
$op['choic_rmk'] = $fty_marker->choic_rmk();
$op['select_unit'] = $fty_marker->select_unit(2);

$marker = $op['order_num'].'.mk.zip';
if(file_exists($GLOBALS['config']['root_dir'].'./fty_marker/'.$marker)){
    $op['marker']	= $marker;
}

# 檢查 相片是否存在 2008.10.29
$op['main_pic'] = _pic_url('big','120',2,'picture',$op['order_num'],'jpg','','');

if ($op['mks'] = $fty_marker->get_rpt(2,0,$op['id'],1,null,'',1)) {
    foreach($op['mks'] as $keys => $vals){
        if(is_array($vals)){
            foreach($vals as $key => $val){
                if($key === 'ord_id'){
                    $op['mks'][$keys]['marker'] = $fty_marker->marker_rpt_list($val,$op['mks'][$keys]['fab_type'],$op['mks'][$keys]['combo']);
                    $op['mks'][$keys]['averages']  = $fty_marker->marker_rpt_list2(2,0,$val,1,$op['mks'][$keys]['fab_type'],$op['mks'][$keys]['combo'],$op['size']);
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
                    $op['mks'][$keys]['remark'] = $fty_marker->rmk_list($val);
                }
            }
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
$op['mk'] = $fty_marker->get(2,$op['mks'][0]['id'],$op['mks'][0]['ord_id'],null,$op['mks'][0]['fab_type'],$op['mks'][0]['combo']);


# 與現有資料相減
if(empty($PHP_status))$PHP_status='';
$op['list'] = $fty_marker->report_title($PHP_order_num,$T_wiqty,$size_A,'',$op['mk'],$PHP_status);

# REMARK
$op['mk_log'] = $fty_marker->get_log($PHP_order_num,'CR');
$op['mk_log']['des_view'] = str_replace( chr(13).chr(10), "<br>", $op['mk_log']['des'] );
page_display($op,'064', $TPL_MARKER_RPT_ADD);
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "marker_rpt_report_add":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

case "marker_rpt_report_add":
check_authority('064',"add");

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
$op['mk'] = $fty_marker->get_rpt(2,$PHP_id,$PHP_ord_id,null,$PHP_fab_type,$PHP_combo);
if(!$op['mk'])
{
    redirect_page("fty_marker.php?PHP_action=marker_rpt_add&PHP_order_num=".$PHP_order_num);
}
//

$op['select_unit'] = $fty_marker->select_unit(2,$op['mk']['unit_type']);
$op['mks'] = $fty_marker->get_rpt(2,0,$op['mk']['ord_id'],1,$op['mk']['fab_type'],$op['mk']['combo']);

# 主料類別
$fab = $op['mk']['fab_type'];
$op['fab_type'] = $GLOBALS['fab_type'][$fab];

# Remarks 用布方法
$op['rmk'] = $fty_marker->rmk_list($op['mk']['remark']);

# Marker NUM
$op['mk_name'] = $PHP_order_num;

# 與現有資料相減
if(empty($PHP_status))$PHP_status='';
$op['list'] = $fty_marker->rpt_report_main($PHP_order_num,$T_wiqty,$size_A,$op['mk'],$PHP_status);

$averages = $clothes = $estimate = '';	
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
                    $op['mks'][$keys]['mk_name'] = $fty_marker->fab_item($val);
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
                            //$nw_asmt .= ']'.' x <font color="red"><b>'.$s_lv[$skey].'L</b></font><br>';
                            $nw_asmt .= ']'.'<br>';
                        }
                        $op['mks'][$keys][$key] = substr($nw_asmt,0,-2);
                    }

                    if($op['mks'][$keys]['length'] and !empty($val) ){
                        $asmts = $fty_marker->average($val,$op['mks'][$keys]['length']);
                        $op['mks'][$keys]['averages'] = $asmts['averages'];
                        $clothes += $op['mks'][$keys]['clothes'] = $asmts['clothes'];
                        $estimate += $op['mks'][$keys]['estimate'] = $asmts['estimate'];
                    }
                }
            }
        }
    }
}
$op['select_unit'] = $fty_marker->select_unit(2,$op['mk']['unit_type']);
$op['unit'] = $GLOBALS['unit_type2'][$op['mk']['unit_type']];
$op['unit2'] = $GLOBALS['unit_type3'][$op['mk']['unit_type']];
if(!empty($op['mk']['mk_num']))$op['mk_num'] = $GLOBALS['ALPHA2'][$op['mk']['mk_num']];

$op['PHP_order_num'] = $PHP_order_num;
$op['PHP_id'] = $op['mk']['id'];
$op['PHP_status'] = $PHP_status;
$op['wi_id'] = $op['id'];
if($clothes and $estimate){
    $op['averages'] = $clothes / $estimate;
    $op['clothes'] = $clothes;
    $op['estimate'] = $estimate;
}

page_display($op,'064', $TPL_MARKER_RPT_REPORT);
break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "marker_rpt_del":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

case "marker_rpt_del":
check_authority('064',"edit");

if($status = $fty_marker->del_rpt(2,$PHP_id,$PHP_ord_id,$PHP_fab_type,$PHP_combo,$PHP_status)){
    # 記錄使用者動態
    if(empty($PHP_mk_num))$PHP_mk_num='';
    $message = "DELETE order marker: [".$PHP_order_num."] (".$GLOBALS['fab_type'][$PHP_fab_type].$PHP_combo.") ".$PHP_mk_num."";
    $log->log_add(0,"063D",$message);
}

if(empty($PHP_status))
    $redir_str = "fty_marker.php?PHP_action=marker_rpt_add&PHP_order_num=".$PHP_order_num;
else
    if($status==2)
        $redir_str = "fty_marker.php?PHP_action=marker_rpt_add&PHP_order_num=".$PHP_order_num;
    else
        $redir_str = "fty_marker.php?PHP_action=marker_rpt_report_add&PHP_order_num=".$PHP_order_num."&PHP_ord_id=".$PHP_ord_id."&PHP_fab_type=".$PHP_fab_type."&PHP_combo=".$PHP_combo;

redirect_page($redir_str);


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "marker_rpt_report_do_add":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

case "marker_rpt_report_do_add":
check_authority('064',"add");

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
    'unit_type'		=> $PHP_unit ,
    'width' 			=> $PHP_width ,
    'combo'				=> $PHP_combo ,
    'remark' 			=> $PHP_remark ,
    'assortment'	=> $assortment ,
    'length'			=> $PHP_length ,
    'updator'		 	=> $GLOBALS['SCACHE']['ADMIN']['login_id']
    );

    if($PHP_status == 'append'){

        if($fty_marker->rpt_add($parm,$PHP_status)){

            if(!empty($parm['fab_type'])){
                $combo_num = $fty_marker->get_rpt_combo(2,$parm['ord_id'],$parm['fab_type']);
                $combo = $combo_num+1;
            }

            # 記錄使用者動態
            $message = "append order marker: [".$PHP_order_num."] (".$GLOBALS['fab_type'][$PHP_fab_type].$combo.") ".$GLOBALS['ALPHA2'][$PHP_mk]."";
            $log->log_add(0,"063A",$message);
        }


        $redir_str = "fty_marker.php?PHP_action=marker_rpt_report_add&PHP_order_num=".$PHP_order_num."&PHP_id=".$PHP_id;
    }else if($PHP_status == 'edit'){
        $fty_marker->update_field_num('marker_rpt','mk_num',$PHP_mk,$PHP_id,$PHP_order_num);
        $fty_marker->update_field_num('marker_rpt','assortment',$assortment,$PHP_id,$PHP_order_num);
        $fty_marker->update_field_num('marker_rpt','length',$PHP_length,$PHP_id,$PHP_order_num);
		
		# 記錄使用者動態
		$message = "update order marker: [".$PHP_order_num."]  ".$GLOBALS['ALPHA2'][$PHP_mk]."";
		$log->log_add(0,"063E",$message);		
        $redir_str = "fty_marker.php?PHP_action=marker_rpt_report_add&PHP_order_num=".$PHP_order_num."&PHP_id=".$PHP_id;
    }
}else{
    $redir_str = "fty_marker.php?PHP_action=marker_rpt_report_add&PHP_order_num=".$PHP_order_num."&PHP_id=".$PHP_id;
}

redirect_page($redir_str);


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "marker_rpt_report_view":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

case "marker_rpt_report_view":
check_authority('064',"view");

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
$op['mk'] = $fty_marker->get_rpt(2,$PHP_id,$PHP_ord_id,null,$PHP_fab_type,$PHP_combo);
if(!$op['mk'])
redirect_page("fty_marker.php?PHP_action=marker_ord_add&PHP_order_num=".$PHP_order_num);

$op['select_unit'] = $fty_marker->select_unit(2,$op['mk']['unit_type']);
$op['mks'] = $fty_marker->get_rpt(2,0,$op['mk']['ord_id'],1,$op['mk']['fab_type'],$op['mk']['combo']);

# 主料類別
$fab = $op['mk']['fab_type'];
$op['fab_type'] = $GLOBALS['fab_type'][$fab];

# Remarks 用布方法
$op['rmk'] = $fty_marker->rmk_list($op['mk']['remark']);

# Marker NUM
$op['mk_name'] = $PHP_order_num;

# 與現有資料相減
if(empty($PHP_status))$PHP_status='';
$op['list'] = $fty_marker->report_title($PHP_order_num,$T_wiqty,$size_A,'',$op['mk'],$PHP_status);

$averages = $clothes = $estimate = '';
if($op['mks']){
    foreach($op['mks'] as $keys => $vals){
        if(is_array($vals)){
            foreach($vals as $key => $val){
                if($key === 'fab_type'){
                    $op['mks'][$keys]['mk_name'] = $fty_marker->fab_item($val);
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
                            $nw_asmt .= ']'.'<br>';
                        }
                        $op['mks'][$keys][$key] = substr($nw_asmt,0,-2);
                    }

                    if($op['mks'][$keys]['length'] and !empty($val) ){
                        $asmts = $fty_marker->average($val,$op['mks'][$keys]['length']);
                        $op['mks'][$keys]['averages'] = $asmts['averages'];
                        $clothes += $op['mks'][$keys]['clothes'] = $asmts['clothes'];
                        $estimate += $op['mks'][$keys]['estimate'] = $asmts['estimate'];
                    }
                }
            }
        }
    }
}
$op['select_unit'] = $fty_marker->select_unit(2,$op['mk']['unit_type']);
$op['unit'] = $GLOBALS['unit_type2'][$op['mk']['unit_type']];
$op['unit2'] = $GLOBALS['unit_type3'][$op['mk']['unit_type']];
$op['PHP_order_num'] = $PHP_order_num;
$op['PHP_id'] = $PHP_id;
$op['wi_id'] = $op['id'];
if($clothes and $estimate){
    $op['averages'] = $clothes / $estimate;
    $op['clothes'] = $clothes;
    $op['estimate'] = $estimate;
}

page_display($op,'064', $TPL_MARKER_RPT_REPORT_VIEW);
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "marker_rpt_pdf":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

case "marker_rpt_pdf":
check_authority('064',"view");

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
$op['mk'] = $fty_marker->get_rpt(2,$PHP_id,$PHP_ord_id,null,$PHP_fab_type,$PHP_combo);
if(!$op['mk'])
redirect_page("fty_marker.php?PHP_action=marker_ord_add&PHP_order_num=".$PHP_order_num);

$op['select_unit'] = $fty_marker->select_unit(2,$op['mk']['unit_type']);
$op['mks'] = $fty_marker->get_rpt(2,0,$op['mk']['ord_id'],1,$op['mk']['fab_type'],$op['mk']['combo']);

# 主料類別
$fab = $op['mk']['fab_type'];
$op['fab_type'] = $GLOBALS['fab_type'][$fab];

# Remarks 用布方法
$op['rmk'] = $fty_marker->rmk_list($op['mk']['remark']);

# Marker NUM
$op['mk_name'] = $PHP_order_num;

# 與現有資料相減
if(empty($PHP_status))$PHP_status='';
$op['list'] = $fty_marker->report_title($PHP_order_num,$T_wiqty,$size_A,'',$op['mk'],$PHP_status);

$averages = $clothes = $estimate = '';
if($op['mks']){
    foreach($op['mks'] as $keys => $vals){
        if(is_array($vals)){
            foreach($vals as $key => $val){
                if($key === 'fab_type'){
                    $op['mks'][$keys]['mk_name'] = $fty_marker->fab_item($val);
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
                            $nw_asmt .= ']'.' <br>';
                            $op['mks'][$keys]['colorway'][$skey] = $colorway;
                            $op['mks'][$keys]['asmts'][$skey] = substr($asmt_p,0,-2);
                            $asmt_p='';
                            $op['mks'][$keys]['level'][$skey] = $s_lv[$skey];
                        }
                        $op['mks'][$keys][$key] = substr($nw_asmt,0,-2);
                    }

                    if($op['mks'][$keys]['length'] and !empty($val) ){
                        $asmts = $fty_marker->average($val,$op['mks'][$keys]['length']);
                        $op['mks'][$keys]['averages'] = $asmts['averages'];
                        $clothes += $op['mks'][$keys]['clothes'] = $asmts['clothes'];
                        $estimate += $op['mks'][$keys]['estimate'] = $asmts['estimate'];
                    }
                }
            }
        }
    }
}
$op['select_unit'] = $fty_marker->select_unit(2,$op['mk']['unit_type']);
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

$name=$op['mk_name'].'_'.$op['fab_type'].$op['mk']['combo'].'_cutting_plan.pdf';
$pdf->Output($name,'D');
unset($_SESSION['PDF']);
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "rpt_marker_des":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

case "rpt_marker_des":
check_authority('064',"add");

$perm = array(
    'ord_num'		=> $PHP_order_num ,
    'item'			=> 'CR' ,
    'des'			 	=> $PHP_des,
    'user'		 	=> $GLOBALS['SCACHE']['ADMIN']['login_id'],
    'k_date'		=> date('Y-m-d'),
    'id'				=> $PHP_id
);

$PHP_id = $fty_marker->marker_log_upload($perm);


    $redir_str = "fty_marker.php?PHP_action=marker_rpt_add&PHP_order_num=".$PHP_order_num;
    redirect_page($redir_str);





#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "marker_rpt_view":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "marker_rpt_view":
check_authority('064',"view");
    //  wi 主檔
    if(!$op['wi']  = $wi->get(0,$PHP_order_num)){    //取出該筆 製造令記錄
        $op['msg']= $wi->msg->get(2);
        $layout->assign($op);
        $layout->display($TPL_ERROR);  		    
        break;
    }
if($op['wi']['bcfm_date'] == '0000-00-00 00:00:00'){    //取出該筆 製造令記錄
    $op['msg'][] = 'NOTIC : <b>WI & BOM</b> not built yet or unavailable!';
    $layout->assign($op);
    $layout->display($TPL_ERROR);  		    
    break;
}
//  smpl 樣本檔
$op['order'] = $order->get($op['wi']['smpl_id']);

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
    
$op['lead_time'] = countDays($op['order']['etp'],$op['order']['etd']);
//  取出  BOM  主料記錄 --------------------------------------------------------------
$op['bom_lots_NONE']= '';
$op['bom_lots'] = $po->cut_report_lots_det($op['wi']['id'],'',$PHP_order_num);  //取出該筆 bom 內ALL主料記錄
$num_bom_lots = count($op['bom_lots']);

for ($i =0; $i< sizeof($op['bom_lots']); $i++)
{
    $bom_qty = explode(',',$op['bom_lots'][$i]['qty']);
    $cut_avg = $fty_marker->get_lots_use_mk($op['bom_lots'][$i]['use_id'],$op['order']['id']);
    $mrk_avg = $Marker->get_lots_use_mk($op['bom_lots'][$i]['use_id'],$op['order']['id']);
    $op['bom_lots'][$i]['cut_qty'] = $op['bom_lots'][$i]['mrk_qty'] = 0;
    for($j=0; $j<sizeof($bom_qty); $j++)
    {
        $op['bom_lots'][$i]['cut_qty'] += $bom_qty[$j] / $op['bom_lots'][$i]['est_1'] * $cut_avg;
        $op['bom_lots'][$i]['mrk_qty'] += $bom_qty[$j] / $op['bom_lots'][$i]['est_1'] * $mrk_avg;
    }

} 

page_display($op,'065', $TPL_CUP_REPORT_VIEW);
break;

} // end case ---------
?>
