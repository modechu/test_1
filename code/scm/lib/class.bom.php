<?php 

#++++++++++++++++++++++ BOM  class ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->init($sql)							啟始 (使用 Msg_handle(); 先聯上 sql)
#	->add_lots($parm)		加入新 BOM 主料

#	->search($mode=0)						搜尋   
#	->get($id=0, nbr=0)						抓出指定 記錄內資料   
#	->edit($parm)							更新 整筆資料
#	->update_field($parm)					更新 資料內 某個單一欄位
#	->del($id)								刪除 資料錄
#	->get_fields($n_field,$where_str="")	取出全部 部門表內的 $n_field 置入arry return
#
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class  BOM {
		
	var $sql;
	var $msg ;
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->init($sql)	啟始(使用 Msg_handle() ; 先聯上 sql)
#		必需聯上 sql 才可  啟始
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function init($sql) {
		$this->msg = new MSG_HANDLE();

		if (!$sql) {
			$this->msg->add("Error ! 無法聯上資料庫.");
		    return false;
		}
		$this->sql = $sql;
		return true;
	} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->get_wiqty_qty_view($wi_num)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_wiqty_qty_view($wi_num,$size_arr) {

$sql = $this->sql;

$size = $size_arr['size'];
$base_size = $size_arr['base_size'];

#
$q_str = "SELECT * 
FROM `order_partial` 
WHERE `ord_num` = '".$wi_num."'
ORDER BY `mks` ASC ";

if (!$q_result = $sql->query($q_str)) {
	$this->msg->merge($srh->msg);
	return false;
}

$PHTML = '<table cellpadding="3" cellspacing="0" cols="0">';
$PHTML .= '<tr>';
$PHTML .= '<td id="Bom_TR_Title" colspan="2">Partial</td>';
$PHTML .= '<td id="Bom_TR_Title">ETD</td>';
$PHTML .= '</tr>';
$CHTML = '<table id="breakdown" cellpadding="3" cellspacing="0" cols="0">';

$size_range = explode(",", $size);

$color_size = array();
$partial = array();
$size_g_ttl = $size_pdc_g_ttl = array();

while ($partial = $sql->fetch($q_result)) {

    $PHTML .= '<tr id="Bom_TR_Index" p_id="'.$partial['id'].'" >';
    $PHTML .= '<td bgcolor="#EEEEEE">'.$partial['mks'].') </td>';
    $PHTML .= '<td>'.$partial['remark'].'</td>';
    $PHTML .= '<td>'.$partial['p_etd'].'</td>';
    
    $CHTML .= '<tr class="Bom_dis_'.$partial['id'].'">';
    $CHTML .= '<td id="Bom_TR_Title">'.$partial['mks'].') </td>';
    $CHTML .= '<td id="Bom_TR_Title">'.$partial['remark'].'</td>';
    $CHTML .= '<td id="Bom_TR_Title">'.$partial['p_etd'].'</td>';
    foreach( $size_range as $skey){
    $base_size_str = $base_size == $skey ? 'style="color:#4df24d;"' : '' ;
    $CHTML .= '<td id="Bom_TR_Size" '.$base_size_str.'>'.$skey.'</td>';
    }
    $CHTML .= '<td id="Bom_TR_Size">SUM</td>';

    $q_strs = "SELECT * 
    FROM `wiqty` 
    WHERE `p_id` = '".$partial['id']."'
    ORDER BY `id` ASC ";
    
    $colorway = array();
    $q_results = $sql->query($q_strs);
    $colorway_arr = $size_sum_ttl = $size_pdc_sum_ttl = array();
    
    while ($colorway = $sql->fetch($q_results)) {
        $colorway['id'];
        $colorway['wi_id'];
        $colorway['colorway'];
        $colorway['qty'];
        $colorway['pdc_color'];
        $colorway['pdc_qty'];
        $colorway['item'];
        $colorway['nnw'];
        $colorway['ship_color'];
        $colorway['p_id'];
        if( !$colorway_arr[$colorway['p_id'].$colorway['pdc_color']] ){
            $colorway_arr[$colorway['p_id'].$colorway['pdc_color']] = $colorway;
        } else {
            $colorway_arr[$colorway['p_id'].$colorway['pdc_color']]['pdc_qty'] = 
            implode( ',' , $this->adding( $colorway_arr[$colorway['p_id'].$colorway['pdc_color']]['pdc_qty'] , $colorway['pdc_qty'] ) );
        }
    }

    foreach ($colorway_arr as $colorway) {
        $id = $colorway['id'];
        $p_id = $colorway['p_id'];

        // $CHTML .= '<tr mark="1" class="Bom_dis_'.$partial['id'].'" id="Bom_TR_hover">';
        // $CHTML .= '<td id="Bom_TR_Item" colspan="3" bgcolor="EEEEEE">'.$colorway['colorway'].'</td>';
        $CHTML_PDT = '<td id="Bom_TR_Item" colspan="3" bgcolor="EEEEEE">'.$colorway['pdc_color'].'</td>';
        $color_array = $color_pdc_array = array();

        $c_row = explode(",",$colorway['qty']);
        foreach( $c_row as $cVal){
            $color_array[] = $cVal;
        }
        
        $c_pdc_row = explode(",",$colorway['pdc_qty']);
        foreach( $c_pdc_row as $cVal){
            $color_pdc_array[] = $cVal;
        }
        
        $color_sum = $color_pdc_sum = 0;
        foreach( $size_range as $skey => $v){
        $base_size_str = $base_size == $v ? 'style="color:#0fb562;"' : '' ;
        // $CHTML .= '<td id="Bom_TR_Value" '.$base_size_str.'>'.number_format($color_array[$skey],0,'',',').'</td>';
        $CHTML_PDT .= '<td id="Bom_TR_Value" '.$base_size_str.'>'.number_format($color_pdc_array[$skey],0,'',',').'</td>';
        $color_sum += $color_array[$skey];
        $color_pdc_sum += $color_pdc_array[$skey];
        $size_sum_ttl[$skey] += $color_array[$skey];
        $size_pdc_sum_ttl[$skey] += $color_pdc_array[$skey];
        $size_g_ttl[$skey] += $color_array[$skey];
        $size_pdc_g_ttl[$skey] += $color_pdc_array[$skey];
        }
        // $CHTML .= '<td id="Bom_TR_Total" bgcolor="EEEEEE">'.number_format($color_sum,0,'',',').'</td>';
        $CHTML_PDT .= '<td id="Bom_TR_Total" xname="PHP_sum_'.$id.'" bgcolor="EEEEEE">'.number_format($color_pdc_sum,0,'',',').'</td>';
        // $CHTML .= '</tr>';

        $CHTML .= '<tr mark="1" class="Bom_dis_'.$partial['id'].'" id="Bom_TR_hover">'.$CHTML_PDT.'</tr>';
    
    
    
        # ARRAY
        $color_size[0][$colorway['wi_id']][$id]['p_id'] = $partial['id'];
        $color_size[0][$colorway['wi_id']][$id]['qty'] = array_sum($color_array);
        $color_size[0][$colorway['wi_id']][$id]['colorway'] = $colorway['colorway'];
        for( $i=0; $i < count($size_range); $i++ ){
            @$color_size[1][$colorway['wi_id']][$size_range[$i]]['p_id'] = $partial['id'];
            @$color_size[1][$colorway['wi_id']][$size_range[$i]]['qty'] += $color_array[$size_range[$i]];
            @$color_size[1][$colorway['wi_id']][$size_range[$i]]['colorway'] = $colorway['colorway'];
            @$color_size[2][$colorway['wi_id']][$size_range[$i]][$id]['p_id'] = $partial['id'];
            @$color_size[2][$colorway['wi_id']][$size_range[$i]][$id]['qty'] += $color_array[$size_range[$i]];
            @$color_size[2][$colorway['wi_id']][$size_range[$i]][$id]['colorway'] = $colorway['colorway'];
        }
        $color_size[3][$colorway['wi_id']]['p_id'] = $partial['id'];
        $color_size[3][$colorway['wi_id']]['qty'] = array_sum($color_size[0][$colorway['wi_id']]);
        $color_size[3][$colorway['wi_id']]['colorway'] = $colorway['colorway'];
    }
    
    

    $PHTML .= '</tr>';    
    $CHTML .= '</tr>';
    
    // $CHTML .= '<tr class="Bom_dis_'.$partial['id'].'" id="Bom_TR_hover">';
    // $CHTML .= '<td id="Bom_TR_Total" bgcolor="CCCCCC" colspan="3">BOM TOTAL</td>';
    // $color_sum_ttl = 0;
    // foreach( $size_sum_ttl as $tkey => $tval ){
        // $CHTML .= '<td id="Bom_TR_Total" bgcolor="CCCCCC">'.number_format($tval,0,'',',').'</td>';
        // $color_sum_ttl += $tval;
    // }
    // $CHTML .= '<td id="Bom_TR_Total" bgcolor="CCCCCC">'.number_format($color_sum_ttl,0,'',',').'</td>';
    // $CHTML .= '</tr>';
    
    $CHTML .= '<tr class="Bom_dis_'.$partial['id'].'" id="Bom_TR_hover">';
    $CHTML .= '<td id="Bom_TR_Total" bgcolor="CCCCCC" colspan="3">BOM TOTAL</td>';
    $color_pdc_sum_ttl = 0;
    foreach( $size_pdc_sum_ttl as $tkey => $tval ){
        $CHTML .= '<td id="Bom_TR_Total" xxname="PHP_sum_'.$size_range[$tkey].'" yname="PHP_sum_'.$partial['id'].'_'.$size_range[$tkey].'" bgcolor="CCCCCC">'.number_format($tval,0,'',',').'</td>';
        $color_pdc_sum_ttl += $tval;
    }
    $CHTML .= '<td id="Bom_TR_Total" xyname="PHP_sum_'.$partial['id'].'" bgcolor="CCCCCC">'.number_format($color_pdc_sum_ttl,0,'',',').'</td>';
    $CHTML .= '</tr>';


}
    // $CHTML .= '<tr class="Bom_dis_dttl" id="Bom_TR_hover">';
    // $CHTML .= '<td id="Bom_TR_GTotal" bgcolor="ffffbf" colspan="3" algan="right">BOM G-TOTAL</td>';
    // $color_g_ttl = 0;
    // foreach( $size_g_ttl as $tkey => $tval ){
        // $CHTML .= '<td id="Bom_TR_GTotal" bgcolor="ffffbf">'.number_format($tval,0,'',',').'</td>';
        // $color_g_ttl += $tval;
    // }
    // $CHTML .= '<td id="Bom_TR_GTotal" bgcolor="ffffbf">'.number_format($color_g_ttl,0,'',',').'</td>';
    // $CHTML .= '</tr>';
    
    $CHTML .= '<tr class="Bom_dis_dttl" id="Bom_TR_hover">';
    $CHTML .= '<td id="Bom_TR_GTotal" bgcolor="ffffbf" colspan="3" algan="right">BOM G-TOTAL</td>';
    $color_pdc_g_ttl = 0;
    foreach( $size_pdc_g_ttl as $tkey => $tval ){
        $CHTML .= '<td id="Bom_TR_GTotal" yname="PHP_gtotal_'.$size_range[$tkey].'" bgcolor="ffffbf">'.number_format($tval,0,'',',').'</td>';
        $color_pdc_g_ttl += $tval;
    }
    $CHTML .= '<td id="Bom_TR_GTotal" xyname="PHP_gtotal" bgcolor="ffffbf">'.number_format($color_pdc_g_ttl,0,'',',').'</td>';
    $CHTML .= '</tr>';
    
$PHTML .= '<tr id="Bom_show_all" bgcolor="#EEEEEE" style="display:none;"><td colspan="3">Show All</td></tr>';
$PHTML .= '</table>';
$CHTML .= '</table>';

return array($PHTML,$CHTML,$color_size);

} // end func


function adding($row1,$row2){
    $row1 = explode(",", $row1);
    $row2 = explode(",", $row2);
    
    $qtys = array();
    foreach($row1 as $key => $qty){
        $qtys[$key] = ( $qty + $row2[$key] );
    }
    // print_r($qtys);
    return $qtys;
}


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->get_wiqty_qty_edit($wi_num)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_wiqty_qty_edit($wi_num,$size_arr) {

$sql = $this->sql;

$size = $size_arr['size'];
$base_size = $size_arr['base_size'];

#
$q_str = "SELECT * 
FROM `order_partial` 
WHERE `ord_num` = '".$wi_num."'
ORDER BY `mks` ASC ";

if (!$q_result = $sql->query($q_str)) {
	$this->msg->merge($srh->msg);
	return false;
}

$PHTML = '<table cellpadding="3" cellspacing="0" cols="0">';
$PHTML .= '<tr>';
$PHTML .= '<td id="Bom_TR_Title" colspan="2">Partial</td>';
$PHTML .= '<td id="Bom_TR_Title">ETD</td>';
$PHTML .= '</tr>';
$CHTML = '<table id="breakdown" cellpadding="3" cellspacing="0" cols="0">';
$DHTML = '<table id="breakdown" cellpadding="3" cellspacing="0" cols="0">';

$size_range = explode(",", $size);

$color_size = array();
$partial = array();
$size_g_ttl = $size_pdc_g_ttl = array();

while ($partial = $sql->fetch($q_result)) {

    $PHTML .= '<tr id="Bom_TR_Index" p_id="'.$partial['id'].'" >';
    $PHTML .= '<td bgcolor="#EEEEEE">'.$partial['mks'].') </td>';
    $PHTML .= '<td>'.$partial['remark'].'</td>';
    $PHTML .= '<td>'.$partial['p_etd'].'</td>';
    
    $CHTML .= '<tr class="Bom_dis_'.$partial['id'].'">';
    $CHTML .= '<td id="Bom_TR_Title">'.$partial['mks'].') </td>';
    $CHTML .= '<td id="Bom_TR_Title">'.$partial['remark'].'</td>';
    $CHTML .= '<td id="Bom_TR_Title">'.$partial['p_etd'].'</td>';
    
    $DHTML .= '<tr class="Bom_dis_'.$partial['id'].'">';
    $DHTML .= '<td id="Bom_TR_Title">'.$partial['mks'].') </td>';
    $DHTML .= '<td id="Bom_TR_Title">'.$partial['remark'].'</td>';
    $DHTML .= '<td id="Bom_TR_Title">'.$partial['p_etd'].'</td>';
    foreach( $size_range as $skey){
    $base_size_str = $base_size == $skey ? 'style="color:#4df24d;"' : '' ;
    $CHTML .= '<td id="Bom_TR_Size" '.$base_size_str.'>'.$skey.'</td>';
    $DHTML .= '<td id="Bom_TR_Size" '.$base_size_str.'>'.$skey.'</td>';
    }
    $CHTML .= '<td id="Bom_TR_Size">SUM</td>';
    $DHTML .= '<td id="Bom_TR_Size">SUM</td>';

    $q_strs = "SELECT * 
    FROM `wiqty` 
    WHERE `p_id` = '".$partial['id']."'
    ORDER BY `id` ASC ";
    
    $colorway = array();
    $q_results = $sql->query($q_strs);
    $size_sum_ttl = $size_pdc_sum_ttl = array();
    
    
    while ($colorway = $sql->fetch($q_results)) {
        $id = $colorway['id'];
        $p_id = $colorway['p_id'];

        $CHTML_PDT = '';
        $CHTML .= '<tr mark="1" class="Bom_dis_'.$partial['id'].'" id="Bom_TR_hover">';
        $CHTML .= '<td id="Bom_TR_Item" colspan="3" bgcolor="EEEEEE">'.$colorway['colorway'].'</td>';
        $CHTML_PDT .= '<td id="Bom_TR_Item2" colspan="3" bgcolor="EEEEEE"><input class="input_bom_txt" name="PHP_color['.$id.']" value="'.$colorway['pdc_color'].'"></td>';
        
        $DHTML_PDT = '';
        $DHTML .= '<tr mark="1" class="Bom_dis_'.$partial['id'].'" id="Bom_TR_hover">';
        $DHTML .= '<td id="Bom_TR_Item" colspan="3" bgcolor="EEEEEE">'.$colorway['colorway'].'</td>';
        $DHTML_PDT .= '<td id="Bom_TR_Item2" colspan="3">'.$colorway['pdc_color'].'</td>';
        $color_array = $color_pdc_array = array();

        $c_row = explode(",",$colorway['qty']);
        foreach( $c_row as $cVal){
            $color_array[] = $cVal;
        }
        
        $c_pdc_row = explode(",",$colorway['pdc_qty']);
        foreach( $c_pdc_row as $cVal){
            $color_pdc_array[] = $cVal;
        }
        
        $color_sum = $color_pdc_sum = 0;
        foreach( $size_range as $skey => $v){
        $base_size_str = $base_size == $v ? 'style="color:#0fb562;"' : '' ;
        $CHTML .= '<td id="Bom_TR_Value" '.$base_size_str.'>'.number_format($color_array[$skey],0,'',',').'</td>';
        $CHTML_PDT .= '<td id="Bom_TR_Value2" '.$base_size_str.'><input class="input_bom_qty" xname="PHP_qty_'.$id.'_'.$v.'" yname="PHP_qty_'.$p_id.'_'.$v.'" name="PHP_qty['.$id.']['.$v.']" value="'.$color_pdc_array[$skey].'" onkeyup="cal_fty_colorway(this,\''.$id.'\',\''.$v.'\',\''.$p_id.'\')" '.($color_array[$skey]>0?'':'readonly').'></td>';
        
        $DHTML .= '<td id="Bom_TR_Value" '.$base_size_str.'>'.number_format($color_array[$skey],0,'',',').'</td>';
        $DHTML_PDT .= '<td id="Bom_TR_Value2" '.$base_size_str.'>'.$color_pdc_array[$skey].'</td>';
        
        $color_sum += $color_array[$skey];
        $color_pdc_sum += $color_pdc_array[$skey];
        $size_sum_ttl[$skey] += $color_array[$skey];
        $size_pdc_sum_ttl[$skey] += $color_pdc_array[$skey];
        $size_g_ttl[$skey] += $color_array[$skey];
        $size_pdc_g_ttl[$skey] += $color_pdc_array[$skey];
        }
        $CHTML .= '<td id="Bom_TR_Total" bgcolor="EEEEEE">'.number_format($color_sum,0,'',',').'</td>';
        $CHTML_PDT .= '<td id="Bom_TR_Total2" xname="PHP_sum_'.$id.'" bgcolor="EEEEEE">'.number_format($color_pdc_sum,0,'',',').'</td>';
        $CHTML .= '</tr>';

        $CHTML .= '<tr mark="1" class="Bom_dis_'.$partial['id'].'" id="Bom_TR_hover">'.$CHTML_PDT.'</tr>';
    
        $DHTML .= '<td id="Bom_TR_Total" bgcolor="EEEEEE">'.number_format($color_sum,0,'',',').'</td>';
        $DHTML_PDT .= '<td id="Bom_TR_Total2" xname="PHP_sum_'.$id.'" bgcolor="EEEEEE">'.number_format($color_pdc_sum,0,'',',').'</td>';
        $DHTML .= '</tr>';

        $DHTML .= '<tr mark="1" class="Bom_dis_'.$partial['id'].'" id="Bom_TR_hover">'.$DHTML_PDT.'</tr>';
    
        # ARRAY
        $color_size[0][$colorway['wi_id']][$id]['p_id'] = $partial['id'];
        $color_size[0][$colorway['wi_id']][$id]['qty'] = array_sum($color_array);
        $color_size[0][$colorway['wi_id']][$id]['colorway'] = $colorway['colorway'];
        for( $i=0; $i < count($size_range); $i++ ){
            @$color_size[1][$colorway['wi_id']][$size_range[$i]]['p_id'] = $partial['id'];
            @$color_size[1][$colorway['wi_id']][$size_range[$i]]['qty'] += $color_array[$size_range[$i]];
            @$color_size[1][$colorway['wi_id']][$size_range[$i]]['colorway'] = $colorway['colorway'];
            @$color_size[2][$colorway['wi_id']][$size_range[$i]][$id]['p_id'] = $partial['id'];
            @$color_size[2][$colorway['wi_id']][$size_range[$i]][$id]['qty'] += $color_array[$size_range[$i]];
            @$color_size[2][$colorway['wi_id']][$size_range[$i]][$id]['colorway'] = $colorway['colorway'];
        }
        $color_size[3][$colorway['wi_id']]['p_id'] = $partial['id'];
        $color_size[3][$colorway['wi_id']]['qty'] = array_sum($color_size[0][$colorway['wi_id']]);
        $color_size[3][$colorway['wi_id']]['colorway'] = $colorway['colorway'];
    }
    
    

    $PHTML .= '</tr>';    
    $CHTML .= '</tr>';
    $DHTML .= '</tr>';
    
    $CHTML .= '<tr class="Bom_dis_'.$partial['id'].'" id="Bom_TR_hover">';
    $CHTML .= '<td id="Bom_TR_Total" bgcolor="CCCCCC" colspan="3">BOM TOTAL</td>';
    $DHTML .= '<tr class="Bom_dis_'.$partial['id'].'" id="Bom_TR_hover">';
    $DHTML .= '<td id="Bom_TR_Total" bgcolor="CCCCCC" colspan="3">BOM TOTAL</td>';
    $color_sum_ttl = 0;
    foreach( $size_sum_ttl as $tkey => $tval ){
        $CHTML .= '<td id="Bom_TR_Total" bgcolor="CCCCCC">'.number_format($tval,0,'',',').'</td>';
        $DHTML .= '<td id="Bom_TR_Total" bgcolor="CCCCCC">'.number_format($tval,0,'',',').'</td>';
        $color_sum_ttl += $tval;
    }
    $CHTML .= '<td id="Bom_TR_Total" bgcolor="CCCCCC">'.number_format($color_sum_ttl,0,'',',').'</td>';
    $CHTML .= '</tr>';
    $DHTML .= '<td id="Bom_TR_Total" bgcolor="CCCCCC">'.number_format($color_sum_ttl,0,'',',').'</td>';
    $DHTML .= '</tr>';
    
    $CHTML .= '<tr class="Bom_dis_'.$partial['id'].'" id="Bom_TR_hover">';
    $CHTML .= '<td id="Bom_TR_Total" bgcolor="CCCCCC" colspan="3">CUTTING TOTAL</td>';
    $DHTML .= '<tr class="Bom_dis_'.$partial['id'].'" id="Bom_TR_hover">';
    $DHTML .= '<td id="Bom_TR_Total" bgcolor="CCCCCC" colspan="3">CUTTING TOTAL</td>';
    $color_pdc_sum_ttl = 0;
    foreach( $size_pdc_sum_ttl as $tkey => $tval ){
        $CHTML .= '<td id="Bom_TR_Total" xxname="PHP_sum_'.$size_range[$tkey].'" yname="PHP_sum_'.$partial['id'].'_'.$size_range[$tkey].'" bgcolor="CCCCCC">'.number_format($tval,0,'',',').'</td>';
        $DHTML .= '<td id="Bom_TR_Total" xxname="PHP_sum_'.$size_range[$tkey].'" yname="PHP_sum_'.$partial['id'].'_'.$size_range[$tkey].'" bgcolor="CCCCCC">'.number_format($tval,0,'',',').'</td>';
        $color_pdc_sum_ttl += $tval;
    }
    $CHTML .= '<td id="Bom_TR_Total" xyname="PHP_sum_'.$partial['id'].'" bgcolor="CCCCCC">'.number_format($color_pdc_sum_ttl,0,'',',').'</td>';
    $CHTML .= '</tr>';

    $DHTML .= '<td id="Bom_TR_Total" xyname="PHP_sum_'.$partial['id'].'" bgcolor="CCCCCC">'.number_format($color_pdc_sum_ttl,0,'',',').'</td>';
    $DHTML .= '</tr>';


}
    $CHTML .= '<tr class="Bom_dis_dttl" id="Bom_TR_hover">';
    $CHTML .= '<td id="Bom_TR_GTotal" bgcolor="ffffbf" colspan="3" algan="right">BOM G-TOTAL</td>';
    $DHTML .= '<tr class="Bom_dis_dttl" id="Bom_TR_hover">';
    $DHTML .= '<td id="Bom_TR_GTotal" bgcolor="ffffbf" colspan="3" algan="right">BOM G-TOTAL</td>';
    $color_g_ttl = 0;
    foreach( $size_g_ttl as $tkey => $tval ){
        $CHTML .= '<td id="Bom_TR_GTotal" bgcolor="ffffbf">'.number_format($tval,0,'',',').'</td>';
        $DHTML .= '<td id="Bom_TR_GTotal" bgcolor="ffffbf">'.number_format($tval,0,'',',').'</td>';
        $color_g_ttl += $tval;
    }
    $CHTML .= '<td id="Bom_TR_GTotal" bgcolor="ffffbf">'.number_format($color_g_ttl,0,'',',').'</td>';
    $CHTML .= '</tr>';
    $DHTML .= '<td id="Bom_TR_GTotal" bgcolor="ffffbf">'.number_format($color_g_ttl,0,'',',').'</td>';
    $DHTML .= '</tr>';
    
    $CHTML .= '<tr class="Bom_dis_dttl" id="Bom_TR_hover">';
    $CHTML .= '<td id="Bom_TR_GTotal" bgcolor="ffffbf" colspan="3" algan="right">CUTTING G-TOTAL</td>';
    $DHTML .= '<tr class="Bom_dis_dttl" id="Bom_TR_hover">';
    $DHTML .= '<td id="Bom_TR_GTotal" bgcolor="ffffbf" colspan="3" algan="right">CUTTING G-TOTAL</td>';
    $color_pdc_g_ttl = 0;
    foreach( $size_pdc_g_ttl as $tkey => $tval ){
        $CHTML .= '<td id="Bom_TR_GTotal" yname="PHP_gtotal_'.$size_range[$tkey].'" bgcolor="ffffbf">'.number_format($tval,0,'',',').'</td>';
        $DHTML .= '<td id="Bom_TR_GTotal" yname="PHP_gtotal_'.$size_range[$tkey].'" bgcolor="ffffbf">'.number_format($tval,0,'',',').'</td>';
        $color_pdc_g_ttl += $tval;
    }
    $CHTML .= '<td id="Bom_TR_GTotal" xyname="PHP_gtotal" bgcolor="ffffbf">'.number_format($color_pdc_g_ttl,0,'',',').'</td>';
    $CHTML .= '</tr>';
    $DHTML .= '<td id="Bom_TR_GTotal" xyname="PHP_gtotal" bgcolor="ffffbf">'.number_format($color_pdc_g_ttl,0,'',',').'</td>';
    $DHTML .= '</tr>';
    
$PHTML .= '<tr id="Bom_show_all" bgcolor="#EEEEEE" style="display:none;"><td colspan="3">Show All</td></tr>';
$PHTML .= '</table>';
$CHTML .= '</table>';
$DHTML .= '</table>';

return array($PHTML,$CHTML,$color_size,$DHTML);

} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->get_bom_qty()
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_bom_qty($wi_id,$size_arr,$color_size) {

$sql = $this->sql;

$size = $size_arr['size'];
$size_range = explode(",", $size);

// id	acc_code	acc_name	des	specify	vendor1	unit1	currency1	term1	location1	price1	vendor2	unit2	currency2	term2	location2	price2	vendor3	unit3	currency3	term3	location3	price3	memo	mile_code	mile_name	last_user	arrange
# Fab
echo $q_str = "SELECT `bom_lots`.`id`,`bom_lots`.`wi_id`,`bom_lots`.`lots_used_id`,`bom_lots`.`color`,`bom_lots`.`qty`,`bom_lots`.`o_qty`,`bom_lots`.`ap_mark`,`bom_lots`.`size`,`bom_lots`.`pp_mark`,`bom_lots`.`dis_ver`,
`lots_use`.`lots_code`,`lots_use`.`lots_name`,`lots_use`.`unit`,`lots_use`.`est_1`,`lots_use`.`use_for`,`lots_use`.`support` , 
`lots`.`des` , `lots`.`comp` , `lots`.`memo` , `lots`.`width` 
FROM `bom_lots` , `lots_use` , `lots`
WHERE `bom_lots`.`wi_id` = '".$wi_id."' AND `bom_lots`.`lots_used_id` = `lots_use`.`id`  AND `lots`.`lots_code` = `lots_use`.`lots_code` 
ORDER BY `bom_lots`.`id` ASC ";

if (!$q_result = $sql->query($q_str)) {
	$this->msg->merge($srh->msg);
	return false;
}

while ($lots = $sql->fetch($q_result)) {
    $bom_lots[] = $lots;
}

$bom_photo = $this->get_bom_photo($wi_id);

$BHTML = '<table id="bom_list" cellpadding="0" cellspacing="0" cols="0">';

// $size_range = explode(",", $size);
// $partial = array();
// $size_g_ttl = array();
// while ($lots = $sql->fetch($q_result)) {

    $BHTML .= '<tr id="Bom_TR_Lots_Title">';
    $BHTML .= '<td rowspan="2" nowrap>Fabric Name</td>';
    $BHTML .= '<td rowspan="2" id="OPF" style="color:#0000FF;cursor:pointer;font-weight: bold;">Picture</td>';
    $BHTML .= '<td rowspan="2" UpLoad="1" style="display:none;">UpLoad</td>';
    $BHTML .= '<td rowspan="2">Detail</td>';
    $BHTML .= '<td rowspan="2">Des.</td>';
    $BHTML .= '<td rowspan="2">consump.</td>';
    $BHTML .= '<td rowspan="2">Color/cat.</td>';
    $BHTML .= '<td rowspan="2">Size</td>';
    $BHTML .= '<td rowspan="2">Width</td>';

    foreach( $color_size[0][$wi_id] as $cVal){
        // $c_key = explode("|",$cVal);
    $BHTML .= '<td id="bom_color_'.$cVal['p_id'].'">'.substr($cVal['colorway'],0,4).'.</td>';
        // $color_array[$c_key[0]] = $c_key[1];
    }
    $BHTML .= '<td rowspan="2">Total</td>';
    $BHTML .= '</tr>';
    
    $BHTML .= '<tr id="Bom_TR_Sub_Title">';
    foreach( $color_size[0][$wi_id] as $cVal){
    $BHTML .= '<td id="bom_color_'.$cVal['p_id'].'">'.number_format($cVal['qty'],0,'',',').'</td>';
    }
    $BHTML .= '</tr>';
    
foreach($bom_lots as $lots){

    $BHTML .= '<tr id="Bom_TR_List">';
    $BHTML .= '<td>'.$lots['lots_name'].'</td>';
    
    $BHTML .= '<td>';
    if ( $bom_photo['lots'][$lots['id']]['status'] == 0 ){
    $BHTML .= '
    <img id="pic" status="'.$bom_photo['lots'][$lots['id']]['status'].'" bom_code="lots_'.$lots['id'].'" bomid="'.$lots['id'].'" src="images/s_no_images.gif" width="50" height="50">';
    } else if ( $bom_photo['lots'][$lots['id']]['status'] == 1 ) {
    $BHTML .= '
    <img id="pic" status="'.$bom_photo['lots'][$lots['id']]['status'].'" bom_code="lots_'.$lots['id'].'" bomid="'.$lots['id'].'" src="images/bom_pic/lots_'.$lots['id'].'_small.jpg" width="50" height="50">';
    } else {
    }
    $BHTML .= '</td>';
    
    $BHTML .= '<td UpLoad="1" style="display:none;">';
    if ( $bom_photo['lots'][$lots['id']]['status'] == 0 ){
    $BHTML .= '
    <form name="form" action="" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="PHP_action" value="upload_bom_pic_file">
        <input type="hidden" name="PHP_bom_id" value="'.$lots['id'].'">
        <input name="lots_'.$lots['id'].'" type="file" id="lots_'.$lots['id'].'">
        <input id="uploadfile" type="button" value="upload" onclick="ajaxFileUpload(\''.$lots['wi_id'].'\',\'lots\',\''.$lots['id'].'\');return false;">
    </form>';
    } else if ( $bom_photo['lots'][$lots['id']]['status'] == 1 ) {
    $BHTML .= '
    creator : '.$bom_photo['lots'][$lots['id']]['creator'].'<br>'.$bom_photo['lots'][$lots['id']]['creat_date'].'<br>
    <a href="download.php?fdir=images/bom_pic/&fname=acc_'.$lots['id'].'.jpg">Download</a>';
    } else {
    }
    $BHTML .= '</td>';
    
    $BHTML .= '<td>'.$lots['use_for'].'</td>';
    $BHTML .= '<td>'.$lots['comp'].'</td>';
    $BHTML .= '<td align="right">'.number_format($lots['est_1'],3,'',',').' '.$lots['unit'].'</td>';
    $BHTML .= '<td>'.$lots['color'].'</td>';
    $BHTML .= '<td>'.$lots['size'].'</td>';
    $BHTML .= '<td>'.$lots['width'].'</td>';

    $color_array = array();
    $c_row = explode(",",$lots['qty']);
    $color_count = count($c_row);
    foreach( $c_row as $cVal){
        $c_key = explode("|",$cVal);
        $color_array[$c_key[0]] = $c_key[1];
    }
    
    
    
    // print_r($color_size[0][$wi_id]);
    $color_sum_ttl = 0;
    // switch($lots['arrange']){
    
        // case 0:
        // foreach( $color_size[0][$wi_id] as $skey => $sval){
        // $BHTML .= '<td bgcolor="EEEEEE" id="bom_color_'.$sval['p_id'].'" align="right">'.number_format($color_array[$skey],0,'',',').'</td>';
        // $color_sum_ttl += $color_array[$skey];
        // }
        // break;
        
        // case 1:
        // $BHTML .= '<td bgcolor="EEEEEE" id="colspan" colspan="'.$color_count.'" align="right">'.number_format($lots['qty'],0,'',',').'</td>';
        // $color_sum_ttl += $lots['qty'];
        // break;
        
        // case 2:
        // foreach( $color_size[2][$wi_id][$lots['size']] as $skey => $sval){
        // $BHTML .= '<td bgcolor="EEEEEE" id="bom_color_'.$sval['p_id'].'" align="right">'.number_format($color_array[$skey],0,'',',').'</td>';
        // $color_sum_ttl += $color_array[$skey];
        // }
        // break;
        
        // case 3:
        // $BHTML .= '<td bgcolor="EEEEEE" id="colspan" colspan="'.$color_count.'" align="right">'.number_format($lots['qty'],0,'',',').'</td>';
        // $color_sum_ttl += $lots['qty'];
        // break;
        
        // default:break;

    // }
    $BHTML .= '<td bgcolor="ffffbf" align="right">'.number_format($color_sum_ttl,0,'',',').'</td>';
    $BHTML .= '</tr>';
}

# Acc
$q_str = "SELECT `bom_acc`.`id`,`bom_acc`.`wi_id`,`bom_acc`.`acc_used_id`,`bom_acc`.`color`,`bom_acc`.`qty`,`bom_acc`.`o_qty`,`bom_acc`.`ap_mark`,`bom_acc`.`size`,`bom_acc`.`pp_mark`,`bom_acc`.`dis_ver`,`bom_acc`.`arrange`,
`acc_use`.`acc_code`,`acc_use`.`acc_name`,`acc_use`.`unit`,`acc_use`.`est_1`,`acc_use`.`use_for`,`acc_use`.`support` , 
`acc`.`des` , `acc`.`specify` , `acc`.`specify` , `acc`.`memo` , `acc`.`mile_name` 
FROM `bom_acc` , `acc_use` , `acc`
WHERE `bom_acc`.`wi_id` = '".$wi_id."' AND `bom_acc`.`acc_used_id` = `acc_use`.`id`  AND `acc`.`acc_code` = `acc_use`.`acc_code` 
ORDER BY `bom_acc`.`id` ASC ";

if (!$q_result = $sql->query($q_str)) {
	$this->msg->merge($srh->msg);
	return false;
}

while ($acc = $sql->fetch($q_result)) {
    $bom_acc[] = $acc;
}

    $BHTML .= '<tr id="Bom_TR_Acc_Title">';
    $BHTML .= '<td rowspan="2" nowrap>Accessory Name</td>';
    $BHTML .= '<td rowspan="2" id="OPF" style="color:#0000FF;cursor:pointer;font-weight: bold;">Picture</td>';
    $BHTML .= '<td rowspan="2" UpLoad="1" style="display:none;">UpLoad</td>';
    $BHTML .= '<td rowspan="2">Detail</td>';
    $BHTML .= '<td rowspan="2">Des.</td>';
    $BHTML .= '<td rowspan="2">consump.</td>';
    $BHTML .= '<td rowspan="2">Color/cat.</td>';
    $BHTML .= '<td rowspan="2">Size</td>';
    $BHTML .= '<td rowspan="2">Width</td>';

    foreach( $color_size[0][$wi_id] as $cVal){
        // $c_key = explode("|",$cVal);
    $BHTML .= '<td id="bom_color_'.$cVal['p_id'].'">'.substr($cVal['colorway'],0,4).'.</td>';
        // $color_array[$c_key[0]] = $c_key[1];
    }
    $BHTML .= '<td rowspan="2">Total</td>';
    $BHTML .= '</tr>';
    
    $BHTML .= '<tr id="Bom_TR_Sub_Title">';
    foreach( $color_size[0][$wi_id] as $cVal){
    $BHTML .= '<td id="bom_color_'.$cVal['p_id'].'">'.number_format($cVal['qty'],0,'',',').'</td>';
    }
    $BHTML .= '</tr>';
    
foreach($bom_acc as $acc){

    $BHTML .= '<tr id="Bom_TR_List">';
    $BHTML .= '<td>'.$acc['acc_name'].'</td>';
        
    $BHTML .= '<td>';
    if ( $bom_photo['acc'][$acc['id']]['status'] == 0 ){
    $BHTML .= '
    <img id="pic" status="'.$bom_photo['acc'][$acc['id']]['status'].'" bom_code="acc_'.$acc['id'].'" bomid="'.$acc['id'].'" src="images/s_no_images.gif" width="50" height="50">';
    } else if ( $bom_photo['acc'][$acc['id']]['status'] == 1 ) {
    $BHTML .= '
    <img id="pic" status="'.$bom_photo['acc'][$acc['id']]['status'].'" bom_code="acc_'.$acc['id'].'" bomid="'.$acc['id'].'" src="images/bom_pic/acc_'.$acc['id'].'_small.jpg" width="50" height="50">';
    } else {
    }
    $BHTML .= '</td>';
    
    $BHTML .= '<td UpLoad="1" style="display:none;">';
    if ( $bom_photo['acc'][$acc['id']]['status'] == 0 ){
    $BHTML .= '
    <form name="form" action="" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="PHP_action" value="upload_bom_pic_file">
        <input type="hidden" name="PHP_bom_id" value="'.$acc['id'].'">
        <input name="acc_'.$acc['id'].'" type="file" id="acc_'.$acc['id'].'">
        <input id="uploadfile" type="button" value="upload" onclick="ajaxFileUpload(\''.$acc['wi_id'].'\',\'acc\',\''.$acc['id'].'\');return false;">
    </form>';
    } else if ( $bom_photo['acc'][$acc['id']]['status'] == 1 ) {
    $BHTML .= '
    creator : '.$bom_photo['acc'][$acc['id']]['creator'].'<br>'.$bom_photo['acc'][$acc['id']]['creat_date'].'<br>
    <a href="download.php?fdir=images/bom_pic/&fname=acc_'.$acc['id'].'.jpg">Download</a>';
    } else {
    }

    $BHTML .= '</td>';

    $BHTML .= '<td>'.$acc['use_for'].'</td>';
    $BHTML .= '<td>'.$acc['specify'].'</td>';
    $BHTML .= '<td align="right">'.number_format($acc['est_1'],3,'',',').' '.$acc['unit'].'</td>';
    $BHTML .= '<td>'.$acc['color'].'</td>';
    $BHTML .= '<td>'.$acc['size'].'</td>';
    $BHTML .= '<td>'.$acc['width'].'</td>';


    $color_array = array();
    $c_row = explode(",",$acc['qty']);
    $color_count = count($color_size[0][$wi_id]);
    foreach( $c_row as $cVal){
        $c_key = explode("|",$cVal);
        $color_array[$c_key[0]] = $c_key[1];
    }
    // print_r($color_size[0][$wi_id]);
    $color_sum_ttl = 0;
    switch($acc['arrange']){
    
        case 0:
        foreach( $color_size[0][$wi_id] as $skey => $sval){
        $BHTML .= '<td bgcolor="EEEEEE" range="0" id="bom_color_'.$sval['p_id'].'" align="right">'.number_format($color_array[$skey],0,'',',').'</td>';
        $color_sum_ttl += $color_array[$skey];
        }
        break;
        
        case 1:
        $BHTML .= '<td bgcolor="EEEEEE" id="colspan" range="1" colspan="'.$color_count.'" align="right">'.number_format($acc['qty'],0,'',',').'</td>';
        $color_sum_ttl += $acc['qty'];
        break;
        
        case 2:
        foreach( $color_size[2][$wi_id][$acc['size']] as $skey => $sval){
        $BHTML .= '<td bgcolor="EEEEEE" range="2" id="bom_color_'.$sval['p_id'].'" align="right">'.number_format($color_array[$skey],0,'',',').'</td>';
        $color_sum_ttl += $color_array[$skey];
        }
        break;
        
        case 3:
        $BHTML .= '<td bgcolor="EEEEEE" id="colspan" range="3" colspan="'.$color_count.'" align="right">'.number_format($acc['qty'],0,'',',').'</td>';
        $color_sum_ttl += $acc['qty'];
        break;
        
        default:break;

    }
    $BHTML .= '<td bgcolor="ffffbf" align="right">'.number_format($color_sum_ttl,0,'',',').'</td>';
    $BHTML .= '</tr>';
}
$BHTML .= '</table>';

return $BHTML;

} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->get_wiqty($wi_num)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_wiqty($wi_num,$size_arr) {

$sql = $this->sql;

$size = $size_arr['size'];
$base_size = $size_arr['base_size'];

#
$q_str = "SELECT * 
FROM `order_partial` 
WHERE `ord_num` = '".$wi_num."'
ORDER BY `mks` ASC ";

if (!$q_result = $sql->query($q_str)) {
	$this->msg->merge($srh->msg);
	return false;
}

$PHTML = '<table cellpadding="3" cellspacing="0" cols="0">';
$PHTML .= '<tr>';
$PHTML .= '<td id="Bom_TR_Title" colspan="2">Partial</td>';
$PHTML .= '<td id="Bom_TR_Title">ETD</td>';
$PHTML .= '</tr>';
$CHTML = '<table id="breakdown" cellpadding="3" cellspacing="0" cols="0">';

$size_range = explode(",", $size);

$color_size = array();
$partial = array();
$size_g_ttl = array();
while ($partial = $sql->fetch($q_result)) {

    $PHTML .= '<tr id="Bom_TR_Index" p_id="'.$partial['id'].'" >';
    $PHTML .= '<td bgcolor="#EEE">'.$partial['mks'].') </td>';
    $PHTML .= '<td>'.$partial['remark'].'</td>';
    $PHTML .= '<td>'.$partial['p_etd'].'</td>';
    
    $CHTML .= '<tr class="Bom_dis_'.$partial['id'].'">';
    $CHTML .= '<td id="Bom_TR_Title">'.$partial['mks'].') </td>';
    $CHTML .= '<td id="Bom_TR_Title">'.$partial['remark'].'</td>';
    $CHTML .= '<td id="Bom_TR_Title">'.$partial['p_etd'].'</td>';
    foreach( $size_range as $skey){
    $base_size_str = $base_size == $skey ? 'style="color:#4df24d;"' : '' ;
    $CHTML .= '<td id="Bom_TR_Size" '.$base_size_str.'>'.$skey.'</td>';
    }
    $CHTML .= '<td id="Bom_TR_Size">SUM</td>';

    $q_strs = "SELECT * 
    FROM `wiqty` 
    WHERE `p_id` = '".$partial['id']."'
    ORDER BY `id` ASC ";
    
    $colorway = array();
    $q_results = $sql->query($q_strs);
    $size_sum_ttl = array();
    while ($colorway = $sql->fetch($q_results)) {
        $id = $colorway['id'];
       
        $CHTML .= '</tr>';
        $CHTML .= '<tr mark="1" class="Bom_dis_'.$partial['id'].'" id="Bom_TR_hover">';
        $CHTML .= '<td id="Bom_TR_Item" colspan="3" bgcolor="EEEEEE">'.$colorway['colorway'].'</td>';
        $color_array = array();
        $c_row = explode(",",$colorway['re_qty']);
        foreach( $c_row as $cVal){
            $c_key = explode("|",$cVal);
            $color_array[$c_key[0]] = $c_key[1];
        }
        $color_sum = 0;
        foreach( $size_range as $skey){
        $base_size_str = $base_size == $skey ? 'style="color:#0fb562;"' : '' ;
        $CHTML .= '<td id="Bom_TR_Value" '.$base_size_str.'>'.number_format($color_array[$skey],0,'',',').'</td>';
        $color_sum += $color_array[$skey];
        $size_sum_ttl[$skey] += $color_array[$skey];
        $size_g_ttl[$skey] += $color_array[$skey];
        }
        $CHTML .= '<td id="Bom_TR_Total" bgcolor="EEEEEE">'.number_format($color_sum,0,'',',').'</td>';
        
    
        # ARRAY
        $color_size[0][$colorway['wi_id']][$id]['p_id'] = $partial['id'];
        $color_size[0][$colorway['wi_id']][$id]['qty'] = array_sum($color_array);
        $color_size[0][$colorway['wi_id']][$id]['colorway'] = $colorway['colorway'];
        for( $i=0; $i < count($size_range); $i++ ){
            @$color_size[1][$colorway['wi_id']][$size_range[$i]]['p_id'] = $partial['id'];
            @$color_size[1][$colorway['wi_id']][$size_range[$i]]['qty'] += $color_array[$size_range[$i]];
            @$color_size[1][$colorway['wi_id']][$size_range[$i]]['colorway'] = $colorway['colorway'];
            @$color_size[2][$colorway['wi_id']][$size_range[$i]][$id]['p_id'] = $partial['id'];
            @$color_size[2][$colorway['wi_id']][$size_range[$i]][$id]['qty'] += $color_array[$size_range[$i]];
            @$color_size[2][$colorway['wi_id']][$size_range[$i]][$id]['colorway'] = $colorway['colorway'];
        }
        $color_size[3][$colorway['wi_id']]['p_id'] = $partial['id'];
        $color_size[3][$colorway['wi_id']]['qty'] = array_sum($color_size[0][$colorway['wi_id']]);
        $color_size[3][$colorway['wi_id']]['colorway'] = $colorway['colorway'];
    }
    
    

    $PHTML .= '</tr>';    
    $CHTML .= '</tr>';
    
    $CHTML .= '<tr class="Bom_dis_'.$partial['id'].'" id="Bom_TR_hover">';
    $CHTML .= '<td id="Bom_TR_Total" bgcolor="CCCCCC" colspan="3">TOTAL</td>';
    $color_sum_ttl = 0;
    foreach( $size_sum_ttl as $tkey => $tval ){
        $CHTML .= '<td id="Bom_TR_Total" bgcolor="CCCCCC">'.number_format($tval,0,'',',').'</td>';
        $color_sum_ttl += $tval;
    }
    $CHTML .= '<td id="Bom_TR_Total" bgcolor="CCCCCC">'.number_format($color_sum_ttl,0,'',',').'</td>';
    $CHTML .= '</tr>';
}
    $CHTML .= '<tr class="Bom_dis_dttl" id="Bom_TR_hover">';
    $CHTML .= '<td id="Bom_TR_GTotal" bgcolor="ffffbf" colspan="3" algan="right">G-TOTAL</td>';
    $color_g_ttl = 0;
    foreach( $size_g_ttl as $tkey => $tval ){
        $CHTML .= '<td id="Bom_TR_GTotal" bgcolor="ffffbf">'.number_format($tval,0,'',',').'</td>';
        $color_g_ttl += $tval;
    }
    $CHTML .= '<td id="Bom_TR_GTotal" bgcolor="ffffbf">'.number_format($color_g_ttl,0,'',',').'</td>';
    $CHTML .= '</tr>';
    
$PHTML .= '<tr id="Bom_show_all" bgcolor="#EEE" style="display:none;"><td colspan="3">Show All</td></tr>';
$PHTML .= '</table>';
$CHTML .= '</table>';

return array($PHTML,$CHTML,$color_size);

} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->get_bom($wi_id)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_bom($wi_id,$size_arr,$color_size) {

$sql = $this->sql;

$size = $size_arr['size'];
$size_range = explode(",", $size);

// id	acc_code	acc_name	des	specify	vendor1	unit1	currency1	term1	location1	price1	vendor2	unit2	currency2	term2	location2	price2	vendor3	unit3	currency3	term3	location3	price3	memo	mile_code	mile_name	last_user	arrange
# Fab
echo $q_str = "SELECT `bom_lots`.`id`,`bom_lots`.`wi_id`,`bom_lots`.`lots_used_id`,`bom_lots`.`color`,`bom_lots`.`qty`,`bom_lots`.`o_qty`,`bom_lots`.`re_qty`,`bom_lots`.`ap_mark`,`bom_lots`.`size`,`bom_lots`.`pp_mark`,`bom_lots`.`dis_ver`,`bom_lots`.`arrange`,
`lots_use`.`lots_code`,`lots_use`.`lots_name`,`lots_use`.`unit`,`lots_use`.`est_1`,`lots_use`.`use_for`,`lots_use`.`support` , 
`lots`.`des` , `lots`.`comp` , `lots`.`memo` , `lots`.`width` 
FROM `bom_lots` , `lots_use` , `lots`
WHERE `bom_lots`.`wi_id` = '".$wi_id."' AND `bom_lots`.`lots_used_id` = `lots_use`.`id`  AND `lots`.`lots_code` = `lots_use`.`lots_code` 
ORDER BY `bom_lots`.`id` ASC ";

if (!$q_result = $sql->query($q_str)) {
	$this->msg->merge($srh->msg);
	return false;
}

while ($lots = $sql->fetch($q_result)) {
    $bom_lots[] = $lots;
}

$bom_photo = $this->get_bom_photo($wi_id);

$BHTML = '<table id="bom_list" cellpadding="0" cellspacing="0" cols="0">';

// $size_range = explode(",", $size);
// $partial = array();
// $size_g_ttl = array();
// while ($lots = $sql->fetch($q_result)) {

    $BHTML .= '<tr id="Bom_TR_Lots_Title">';
    $BHTML .= '<td rowspan="2" nowrap>Fabric Name</td>';
    $BHTML .= '<td rowspan="2" id="OPF" style="color:#0000FF;cursor:pointer;font-weight: bold;">Picture</td>';
    $BHTML .= '<td rowspan="2" UpLoad="1" style="display:none;">UpLoad</td>';
    $BHTML .= '<td rowspan="2">Detail</td>';
    $BHTML .= '<td rowspan="2">Des.</td>';
    $BHTML .= '<td rowspan="2">consump.</td>';
    $BHTML .= '<td rowspan="2">Color/cat.</td>';
    $BHTML .= '<td rowspan="2">Size</td>';
    $BHTML .= '<td rowspan="2">Width</td>';

    foreach( $color_size[0][$wi_id] as $cVal){
        // $c_key = explode("|",$cVal);
    $BHTML .= '<td id="bom_color_'.$cVal['p_id'].'">'.substr($cVal['colorway'],0,4).'.</td>';
        // $color_array[$c_key[0]] = $c_key[1];
    }
    $BHTML .= '<td rowspan="2">Total</td>';
    $BHTML .= '</tr>';
    
    $BHTML .= '<tr id="Bom_TR_Sub_Title">';
    foreach( $color_size[0][$wi_id] as $cVal){
    $BHTML .= '<td id="bom_color_'.$cVal['p_id'].'">'.number_format($cVal['qty'],0,'',',').'</td>';
    }
    $BHTML .= '</tr>';
    
foreach($bom_lots as $lots){

    $BHTML .= '<tr id="Bom_TR_List">';
    $BHTML .= '<td>'.$lots['lots_name'].'</td>';
    
    $BHTML .= '<td>';
    if ( $bom_photo['lots'][$lots['id']]['status'] == 0 ){
    $BHTML .= '
    <img id="pic" status="'.$bom_photo['lots'][$lots['id']]['status'].'" bom_code="lots_'.$lots['id'].'" bomid="'.$lots['id'].'" src="images/s_no_images.gif" width="50" height="50">';
    } else if ( $bom_photo['lots'][$lots['id']]['status'] == 1 ) {
    $BHTML .= '
    <img id="pic" status="'.$bom_photo['lots'][$lots['id']]['status'].'" bom_code="lots_'.$lots['id'].'" bomid="'.$lots['id'].'" src="images/bom_pic/lots_'.$lots['id'].'_small.jpg" width="50" height="50">';
    } else {
    }
    $BHTML .= '</td>';
    
    $BHTML .= '<td UpLoad="1" style="display:none;">';
    if ( $bom_photo['lots'][$lots['id']]['status'] == 0 ){
    $BHTML .= '
    <form name="form" action="" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="PHP_action" value="upload_bom_pic_file">
        <input type="hidden" name="PHP_bom_id" value="'.$lots['id'].'">
        <input name="lots_'.$lots['id'].'" type="file" id="lots_'.$lots['id'].'">
        <input id="uploadfile" type="button" value="upload" onclick="ajaxFileUpload(\''.$lots['wi_id'].'\',\'lots\',\''.$lots['id'].'\');return false;">
    </form>';
    } else if ( $bom_photo['lots'][$lots['id']]['status'] == 1 ) {
    $BHTML .= '
    creator : '.$bom_photo['lots'][$lots['id']]['creator'].'<br>'.$bom_photo['lots'][$lots['id']]['creat_date'].'<br>
    <a href="download.php?fdir=images/bom_pic/&fname=acc_'.$lots['id'].'.jpg">Download</a>';
    } else {
    }
    $BHTML .= '</td>';
    
    $BHTML .= '<td>'.$lots['use_for'].'</td>';
    $BHTML .= '<td>'.$lots['comp'].'</td>';
    $BHTML .= '<td align="right">'.number_format($lots['est_1'],3,'',',').' '.$lots['unit'].'</td>';
    $BHTML .= '<td>'.$lots['color'].'</td>';
    $BHTML .= '<td>'.$lots['size'].'</td>';
    $BHTML .= '<td>'.$lots['width'].'</td>';

    $color_array = array();
    $c_row = explode(",",$lots['re_qty']);
    $color_count = count($c_row);
    foreach( $c_row as $cVal){
        $c_key = explode("|",$cVal);
        $color_array[$c_key[0]] = $c_key[1];
    }
    
    
    
    // print_r($color_size[0][$wi_id]);
    $color_sum_ttl = 0;
    switch($lots['arrange']){
    
        case 0:
        foreach( $color_size[0][$wi_id] as $skey => $sval){
        $BHTML .= '<td bgcolor="EEEEEE" id="bom_color_'.$sval['p_id'].'" align="right">'.number_format($color_array[$skey],0,'',',').'</td>';
        $color_sum_ttl += $color_array[$skey];
        }
        break;
        
        case 1:
        $BHTML .= '<td bgcolor="EEEEEE" id="colspan" colspan="'.$color_count.'" align="right">'.number_format($lots['re_qty'],0,'',',').'</td>';
        $color_sum_ttl += $lots['re_qty'];
        break;
        
        case 2:
        foreach( $color_size[2][$wi_id][$lots['size']] as $skey => $sval){
        $BHTML .= '<td bgcolor="EEEEEE" id="bom_color_'.$sval['p_id'].'" align="right">'.number_format($color_array[$skey],0,'',',').'</td>';
        $color_sum_ttl += $color_array[$skey];
        }
        break;
        
        case 3:
        $BHTML .= '<td bgcolor="EEEEEE" id="colspan" colspan="'.$color_count.'" align="right">'.number_format($lots['re_qty'],0,'',',').'</td>';
        $color_sum_ttl += $lots['re_qty'];
        break;
        
        default:break;

    }
    $BHTML .= '<td bgcolor="ffffbf" align="right">'.number_format($color_sum_ttl,0,'',',').'</td>';
    $BHTML .= '</tr>';
}

# Acc
$q_str = "SELECT `bom_acc`.`id`,`bom_acc`.`wi_id`,`bom_acc`.`acc_used_id`,`bom_acc`.`color`,`bom_acc`.`qty`,`bom_acc`.`o_qty`,`bom_acc`.`re_qty`,`bom_acc`.`ap_mark`,`bom_acc`.`size`,`bom_acc`.`pp_mark`,`bom_acc`.`dis_ver`,`bom_acc`.`arrange`,
`acc_use`.`acc_code`,`acc_use`.`acc_name`,`acc_use`.`unit`,`acc_use`.`est_1`,`acc_use`.`use_for`,`acc_use`.`support` , 
`acc`.`des` , `acc`.`specify` , `acc`.`specify` , `acc`.`memo` , `acc`.`mile_name` 
FROM `bom_acc` , `acc_use` , `acc`
WHERE `bom_acc`.`wi_id` = '".$wi_id."' AND `bom_acc`.`acc_used_id` = `acc_use`.`id`  AND `acc`.`acc_code` = `acc_use`.`acc_code` 
ORDER BY `bom_acc`.`id` ASC ";

if (!$q_result = $sql->query($q_str)) {
	$this->msg->merge($srh->msg);
	return false;
}

while ($acc = $sql->fetch($q_result)) {
    $bom_acc[] = $acc;
}

    $BHTML .= '<tr id="Bom_TR_Acc_Title">';
    $BHTML .= '<td rowspan="2" nowrap>Accessory Name</td>';
    $BHTML .= '<td rowspan="2" id="OPF" style="color:#0000FF;cursor:pointer;font-weight: bold;">Picture</td>';
    $BHTML .= '<td rowspan="2" UpLoad="1" style="display:none;">UpLoad</td>';
    $BHTML .= '<td rowspan="2">Detail</td>';
    $BHTML .= '<td rowspan="2">Des.</td>';
    $BHTML .= '<td rowspan="2">consump.</td>';
    $BHTML .= '<td rowspan="2">Color/cat.</td>';
    $BHTML .= '<td rowspan="2">Size</td>';
    $BHTML .= '<td rowspan="2">Width</td>';

    foreach( $color_size[0][$wi_id] as $cVal){
        // $c_key = explode("|",$cVal);
    $BHTML .= '<td id="bom_color_'.$cVal['p_id'].'">'.substr($cVal['colorway'],0,4).'.</td>';
        // $color_array[$c_key[0]] = $c_key[1];
    }
    $BHTML .= '<td rowspan="2">Total</td>';
    $BHTML .= '</tr>';
    
    $BHTML .= '<tr id="Bom_TR_Sub_Title">';
    foreach( $color_size[0][$wi_id] as $cVal){
    $BHTML .= '<td id="bom_color_'.$cVal['p_id'].'">'.number_format($cVal['qty'],0,'',',').'</td>';
    }
    $BHTML .= '</tr>';
    
foreach($bom_acc as $acc){

    $BHTML .= '<tr id="Bom_TR_List">';
    $BHTML .= '<td>'.$acc['acc_name'].'</td>';
        
    $BHTML .= '<td>';
    if ( $bom_photo['acc'][$acc['id']]['status'] == 0 ){
    $BHTML .= '
    <img id="pic" status="'.$bom_photo['acc'][$acc['id']]['status'].'" bom_code="acc_'.$acc['id'].'" bomid="'.$acc['id'].'" src="images/s_no_images.gif" width="50" height="50">';
    } else if ( $bom_photo['acc'][$acc['id']]['status'] == 1 ) {
    $BHTML .= '
    <img id="pic" status="'.$bom_photo['acc'][$acc['id']]['status'].'" bom_code="acc_'.$acc['id'].'" bomid="'.$acc['id'].'" src="images/bom_pic/acc_'.$acc['id'].'_small.jpg" width="50" height="50">';
    } else {
    }
    $BHTML .= '</td>';
    
    $BHTML .= '<td UpLoad="1" style="display:none;">';
    if ( $bom_photo['acc'][$acc['id']]['status'] == 0 ){
    $BHTML .= '
    <form name="form" action="" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="PHP_action" value="upload_bom_pic_file">
        <input type="hidden" name="PHP_bom_id" value="'.$acc['id'].'">
        <input name="acc_'.$acc['id'].'" type="file" id="acc_'.$acc['id'].'">
        <input id="uploadfile" type="button" value="upload" onclick="ajaxFileUpload(\''.$acc['wi_id'].'\',\'acc\',\''.$acc['id'].'\');return false;">
    </form>';
    } else if ( $bom_photo['acc'][$acc['id']]['status'] == 1 ) {
    $BHTML .= '
    creator : '.$bom_photo['acc'][$acc['id']]['creator'].'<br>'.$bom_photo['acc'][$acc['id']]['creat_date'].'<br>
    <a href="download.php?fdir=images/bom_pic/&fname=acc_'.$acc['id'].'.jpg">Download</a>';
    } else {
    }

    $BHTML .= '</td>';

    $BHTML .= '<td>'.$acc['use_for'].'</td>';
    $BHTML .= '<td>'.$acc['specify'].'</td>';
    $BHTML .= '<td align="right">'.number_format($acc['est_1'],3,'',',').' '.$acc['unit'].'</td>';
    $BHTML .= '<td>'.$acc['color'].'</td>';
    $BHTML .= '<td>'.$acc['size'].'</td>';
    $BHTML .= '<td>'.$acc['width'].'</td>';


    $color_array = array();
    $c_row = explode(",",$acc['re_qty']);
    $color_count = count($color_size[0][$wi_id]);
    foreach( $c_row as $cVal){
        $c_key = explode("|",$cVal);
        $color_array[$c_key[0]] = $c_key[1];
    }
    // print_r($color_size[0][$wi_id]);
    $color_sum_ttl = 0;
    switch($acc['arrange']){
    
        case 0:
        foreach( $color_size[0][$wi_id] as $skey => $sval){
        $BHTML .= '<td bgcolor="EEEEEE" range="0" id="bom_color_'.$sval['p_id'].'" align="right">'.number_format($color_array[$skey],0,'',',').'</td>';
        $color_sum_ttl += $color_array[$skey];
        }
        break;
        
        case 1:
        $BHTML .= '<td bgcolor="EEEEEE" id="colspan" range="1" colspan="'.$color_count.'" align="right">'.number_format($acc['re_qty'],0,'',',').'</td>';
        $color_sum_ttl += $acc['re_qty'];
        break;
        
        case 2:
        foreach( $color_size[2][$wi_id][$acc['size']] as $skey => $sval){
        $BHTML .= '<td bgcolor="EEEEEE" range="2" id="bom_color_'.$sval['p_id'].'" align="right">'.number_format($color_array[$skey],0,'',',').'</td>';
        $color_sum_ttl += $color_array[$skey];
        }
        break;
        
        case 3:
        $BHTML .= '<td bgcolor="EEEEEE" id="colspan" range="3" colspan="'.$color_count.'" align="right">'.number_format($acc['re_qty'],0,'',',').'</td>';
        $color_sum_ttl += $acc['re_qty'];
        break;
        
        default:break;

    }
    $BHTML .= '<td bgcolor="ffffbf" align="right">'.number_format($color_sum_ttl,0,'',',').'</td>';
    $BHTML .= '</tr>';
}
$BHTML .= '</table>';

return $BHTML;

} // end func


					
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add_lots($parm)		加入新 BOM 主料
#						傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_lots($parm) {
					
		$sql = $this->sql;

			############### 檢查輸入項目	

		$parm['color'] = trim($parm['color']);
					# 加入資料庫
		$q_str = "INSERT INTO bom_lots (wi_id,
									lots_used_id,
									color,
									qty,
									o_qty,
									k_date) VALUES('".
									$parm['wi_id']."','".
									$parm['lots_used_id']."','".
									$parm['color']."','".
									$parm['qty']."','".
									$parm['o_qty']."','".
									$parm['this_day']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法新增資料記錄.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$new_id = $sql->insert_id();  //取出 新的 id

		$this->msg->add("成功 新增 BOM 主料記錄。") ;

		return $new_id;

	} // end func
	

					
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add_acc($parm)		加入新 BOM 副料
#						傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function add_acc($parm) {
				
	$sql = $this->sql;

		############### 檢查輸入項目	

	$parm['color'] = trim($parm['color']);
	# 加入資料庫
	$q_str = "INSERT INTO bom_acc (wi_id,
								acc_used_id,
								color,
								qty,
								o_qty,
								size,
								k_date) VALUES('".
								$parm['wi_id']."','".
								$parm['acc_used_id']."','".
								$parm['color']."','".
								$parm['qty']."','".
								$parm['o_qty']."','".
								$parm['size']."','".
								$parm['this_day']."')";
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! 無法新增資料記錄.");
		$this->msg->merge($sql->msg);
		return false;    
	}

	$new_id = $sql->insert_id();  //取出 新的 id
	
	$this->msg->add("成功 新增 BOM 副料記錄。");
	
	return $new_id;
	
} // end func
	




#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search_lots ($mode=0, $where_str="")	搜尋  BOM 之主料 資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function search_lots($mode=0, $where_str="") {

$sql = $this->sql;

$argv = $GLOBALS;   //將所有的 globals 都抓入$argv
$srh = new SEARCH();
if (!$srh->set_sql($sql)) {
	$this->msg->merge($srh->msg);
	return false;
}

if($where_str) $where_str = $where_str. "AND dis_ver = 0";
if(!$where_str) $where_str = "WHERE dis_ver = 0";
$q_header = "SELECT * FROM bom_lots ".$where_str;
if (!$srh->add_q_header($q_header)) {
	$this->msg->merge($srh->msg);
	return false;
}

$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
$srh->add_sort_condition("id ");
$srh->row_per_page = 400;

$result= $srh->send_query2();
if (!is_array($result)) {
	$this->msg->merge($srh->msg);
	return false;		    
}
// echo $srh->q_str;
$this->msg->merge($srh->msg);
if (!$result){   // 當查尋無資料時
	$op['record_NONE'] = 1;
}

$op['lots'] = $result;  // 資料錄 拋入 $op

return $op;

} // end func
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search_lots ($mode=0, $where_str="")	搜尋  BOM 之主料 資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_lots_det($id) {

		$sql = $this->sql;
		$lots = array();
		
		$q_str = "SELECT bom_lots.*, lots_use.*, lots.comp, lots.width, lots.weight, lots.cons, 
												lots.des, lots.specify, lots.vendor1, lots.price1 
								 FROM bom_lots, lots_use, lots 
								 WHERE lots_use.lots_code=lots.lots_code AND bom_lots.lots_used_id = lots_use.id AND
								 			 dis_ver = 0 AND bom_lots.wi_id=".$id;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			$lots[] = $row;   
		}

		return $lots;
	} // end func	

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search_lots ($mode=0, $where_str="")	搜尋  BOM 之主料 資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_lots_copy($mode=0, $where_str="") {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv
		$srh = new SEARCH();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$q_header = "SELECT bom_lots.*, lots_use.lots_code as mat_code FROM bom_lots, lots_use ".$where_str;
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("id ");
		$srh->row_per_page = 4000;

		$result= $srh->send_query2();
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);

		return $result;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search_acc ($mode=0, $where_str="")	搜尋  BOM 之副料 資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_acc($mode=0, $where_str="") {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv
		$srh = new SEARCH();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		if($where_str) $where_str = $where_str. "AND dis_ver = 0";
		if(!$where_str) $where_str = "WHERE dis_ver = 0";


		$q_header = "SELECT *, id FROM bom_acc ".$where_str;
// echo $q_header;
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("id ");
		$srh->row_per_page = 4000;



		$result= $srh->send_query2();
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
			if (!$result){   // 當查尋無資料時
				$op['record_NONE'] = 1;
			}

		$op['acc'] = $result;  // 資料錄 拋入 $op

		return $op;
	} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search_lots ($mode=0, $where_str="")	搜尋  BOM 之主料 資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_acc_det($id) {

		$sql = $this->sql;
		$lots = array();
		
		$q_str = "SELECT bom_acc.*, acc_use.*, acc.des, acc.specify, acc.vendor1, acc.price1, acc.arrange 
								 FROM bom_acc, acc_use, acc 
								 WHERE acc_use.acc_code=acc.acc_code AND bom_acc.acc_used_id = acc_use.id AND
								 			 dis_ver = 0 AND bom_acc.wi_id=".$id;
//echo $q_str;								 			 
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			$lots[] = $row;   
		}

		return $lots;
	} // end func			

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search_acc ($mode=0, $where_str="")	搜尋  BOM 之副料 資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_acc_copy($mode=0, $where_str="") {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv
		$srh = new SEARCH();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$q_header = "SELECT bom_acc.*, acc_use.acc_code as mat_code FROM bom_acc, acc_use ".$where_str;
// echo $q_header;
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("id ");
		$srh->row_per_page = 999;


		$result= $srh->send_query2();
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
	
		return $result;
	} // end func	
	

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search_cfm($mode=0, $where_str='')	搜尋 製造令 資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_cfm($mode=0) {

		$sql = $this->sql;
		if($mode == 1)
		{
			$argv = $_SESSION['sch_parm'];
		}else{
			$argv = $GLOBALS;   //將所有的 globals 都抓入$argv
		}

		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT DISTINCT wi.*, cust.cust_init_name as cust_iname, s_order.factory, s_order.style											  
								 FROM wi, cust, s_order 
								 LEFT JOIN bom_acc ON wi.id	= bom_acc.wi_id 
                 LEFT JOIN bom_lots ON wi.id = bom_lots.wi_id";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("id DESC");
		$srh->row_per_page = 12;

		##--*****--2006.11.16頁碼新增 start		##	
		$pagesize=10;
		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
		##--*****--2006.11.16頁碼新增 end	   ##
		//2006/05/12 adding 

		if ($str = $argv['PHP_dept_code'] )  { 
			// $srh->add_where_condition("wi.dept = '$str'", "PHP_dept_code",$str,"Dept. = [ $str ]. "); 
		}else{
			//2007/11/12 adding 
			$team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
			$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
			//部門 : K0,J0,T0
			$sale_f_mang = $GLOBALS['SALES_F_MANG'];
			$sale_mang = $GLOBALS['SALES_MANG'];
			for ($i=0; $i< sizeof($sale_f_mang); $i++)
			{			
					if($user_dept == $sale_f_mang[$i]) 	$srh->add_where_condition("wi.dept LIKE '".$sale_mang[$i]."%'", "PHP_dept",$sale_mang[$i],"");		
			}
			//部門 : 業務部門
			$sales_dept = $GLOBALS['SALES_DEPT'];
			if ($team == 'MD')	$srh->add_where_condition("wi.dept = '$user_dept'", "PHP_dept","$user_dept","Search Dept =[ $user_dept ]");
			for ($i=0; $i< sizeof($sales_dept); $i++)
			{			
					if($user_dept == $sales_dept[$i] && $team <> 'MD') 	$srh->add_where_condition("wi.dept = '$user_dept'", "PHP_dept","$user_dept","Search Dept =[ $user_dept ]");
			}
			//部門 : 工廠
			if ($user_dept == 'HJ' || $user_dept == 'LY')	$srh->add_where_condition("s_order.factory = '$user_dept'");
		}
		if ( $str = $argv['PHP_cust'] )  { 
            if(!$argv['PHP_num'])
			$srh->add_where_condition("wi.cust = '$str'", "PHP_cust",$str,"Search customer = [ $str ]. "); 
        }
		if ( $str = $argv['PHP_num'] )  { 
			$srh->add_where_condition("wi.wi_num LIKE '%$str%'", "PHP_wi_num",$str,"Search wi # above: [ $str ] "); }

if (!isset($argv['PHP_full']))$argv['PHP_full']=0;

$srh->add_where_condition("wi.cust = cust.cust_s_name AND wi.cust_ver = cust.ver");
$srh->add_where_condition("wi.wi_num = s_order.order_num");
$srh->add_where_condition("(s_order.status >= 4) AND (s_order.status <> 5)");
//$srh->add_where_condition("wi.id 	= bom_lots.wi_id");
//$srh->add_where_condition("wi.id 	= bom_acc.wi_id");
if (!isset($argv['PHP_special']) && $argv['PHP_full'] == 0) $srh->add_where_condition("bom_acc.ap_mark = '' || bom_lots.ap_mark = ''");

	$srh->add_where_condition("wi.status > 1");
	$result= $srh->send_query2();
	// print_r($result);
	if (!is_array($result)) {
	// echo $srh->q_str;
		$this->msg->merge($srh->msg);
		return false;		    
	}

	$this->msg->merge($srh->msg);
	if (!$result){   // 當查尋無資料時
		$op['record_NONE'] = 1;
	}
	$op['wi'] = $result;  // 資料錄 拋入 $op
	$op['cgistr_get'] = $srh->get_cgi_str(0);
	$op['cgistr_post'] = $srh->get_cgi_str(1);
	$op['prev_no'] = $srh->prev_no;
	$op['next_no'] = $srh->next_no;
	$op['max_no'] = $srh->max_no;
	$op['last_no'] = $srh->last_no;
	$op['start_no'] = $srh->start_no;
	$op['per_page'] = $srh->row_per_page;
// echo $srh->q_str;
	##--*****--2006.11.16頁碼新增 start			
	$op['maxpage'] =$srh->get_max_page();
	$op['pages'] = $pages;
	$op['now_pp'] = $srh->now_pp;
	$op['lastpage']=$pages[$pagesize-1];		
	##--*****--2006.11.16頁碼新增 end

		return $op;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search_pa()	搜尋 製造令 資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_pa() {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv

		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

//		$q_header = "SELECT DISTINCT wi.*, cust.cust_init_name as cust_iname 
//								 FROM  cust, bom_lots, bom_acc, ap_det, (ap, wi) LEFT JOIN ap_special ON ap.ap_num = ap_special.ap_num AND wi.wi_num = ap_special.ord_num";


		$q_header = "SELECT DISTINCT wi.*, cust.cust_init_name as cust_iname 
								 FROM  cust, (ap, wi, ap_det) 
								 LEFT JOIN ap_special ON ap.ap_num = ap_special.ap_num AND wi.wi_num = ap_special.ord_num AND ap.po_num = ''
								 LEFT JOIN bom_acc ON wi.id	= bom_acc.wi_id AND bom_acc.id = ap_det.bom_id AND ap_det.mat_cat = 'a' AND ap_det.ap_num = ap.ap_num
								 LEFT JOIN bom_lots ON wi.id = bom_lots.wi_id AND bom_lots.id = ap_det.bom_id AND ap_det.mat_cat = 'l' AND ap_det.ap_num = ap.ap_num";
								


		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("id DESC");
		$srh->row_per_page = 12;

##--*****--2006.11.16頁碼新增 start		##		
		$pagesize=10;
		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
 ##--*****--2006.11.16頁碼新增 end	   ##
 	//2006/05/12 adding 

		if ($str = $argv['PHP_dept_code'] )  { 
			$srh->add_where_condition("wi.dept = '$str'", "PHP_dept_code",$str,"Dept. = [ $str ]. "); 
		}else{
			//2007/11/12 adding 
			$team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
			$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
		//部門 : K0,J0,T0
			$sale_f_mang = $GLOBALS['SALES_F_MANG'];
			$sale_mang = $GLOBALS['SALES_MANG'];
			for ($i=0; $i< sizeof($sale_f_mang); $i++)
			{			
					if($user_dept == $sale_f_mang[$i]) 	$srh->add_where_condition("wi.dept LIKE '".$sale_mang[$i]."%'", "PHP_dept",$sale_mang[$i],"");		
			}
		//部門 : 業務部門
			$sales_dept = $GLOBALS['SALES_DEPT'];
			if ($team == 'MD')	$srh->add_where_condition("wi.dept = '$user_dept'", "PHP_dept","$user_dept","Search Dept =[ $user_dept ]");
			for ($i=0; $i< sizeof($sales_dept); $i++)
			{			
					if($user_dept == $sales_dept[$i] && $team <> 'MD') 	$srh->add_where_condition("wi.dept = '$user_dept'", "PHP_dept","$user_dept","Search Dept =[ $user_dept ]");
			}	


		}		
		if ($str = $argv['PHP_cust'] )  { 
			$srh->add_where_condition("wi.cust = '$str'", "PHP_cust",$str,"Search customer = [ $str ]. "); }
		if ($str = $argv['PHP_num'] )  { 
			$srh->add_where_condition("wi.wi_num LIKE '%$str%'", "PHP_wi_num",$str,"Search wi # above: [ $str ] "); }


$srh->add_where_condition("wi.cust = cust.cust_s_name AND cust.ver = wi.cust_ver");
//$srh->add_where_condition("wi.id 	= bom_lots.wi_id");
//$srh->add_where_condition("wi.id 	= bom_acc.wi_id");
$srh->add_where_condition("(bom_acc.id = ap_det.bom_id AND ap_det.mat_cat = 'a' AND ap_det.ap_num = ap.ap_num) OR (bom_lots.id = ap_det.bom_id AND ap_det.mat_cat = 'l' AND ap_det.ap_num = ap.ap_num)OR (ap.ap_num = ap_special.ap_num AND wi.wi_num = ap_special.ord_num)");
$srh->add_where_condition("wi.status > 1");
$srh->add_where_condition("ap.po_num = ''");
		
		$result= $srh->send_query2();
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
			if (!$result){   // 當查尋無資料時
				$op['record_NONE'] = 1;
			}
		$op['wi'] = $result;  // 資料錄 拋入 $op
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['cgistr_post'] = $srh->get_cgi_str(1);
		$op['prev_no'] = $srh->prev_no;
		$op['next_no'] = $srh->next_no;
		$op['max_no'] = $srh->max_no;
		$op['last_no'] = $srh->last_no;
		$op['start_no'] = $srh->start_no;
		$op['per_page'] = $srh->row_per_page;

##--*****--2006.11.16頁碼新增 start			
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] = $pages;
		$op['now_pp'] = $srh->now_pp;
    $op['lastpage']=$pages[$pagesize-1];		
##--*****--2006.11.16頁碼新增 end

		return $op;
	} // end func
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $wi_id=0)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_aply($id=0, $table='', $fd='wi_id',$cate='x') {

		$sql = $this->sql;
		$rtn = 1;
		$mk=2;

		if($table == 'bom_acc')
		{		
			$q_str = "SELECT bom_acc.ap_mark, ap.status FROM bom_acc, acc_use LEFT JOIN ap ON ap.ap_mark = bom_acc.ap_mark 
								WHERE  bom_acc.acc_used_id = acc_use.id AND $fd='$id' AND dis_ver = 0 AND support = 0 ";
			if($cate <> 'x') $q_str .= " AND acc_use.acc_cat = '$cate'";
			$q_str .= ' GROUP BY bom_acc.id ';
			//echo $q_str."<br>";
		}
	
		if($table == 'bom_lots')
		{		
			$q_str = "SELECT bom_lots.ap_mark, ap.status FROM lots_use , bom_lots LEFT JOIN ap ON ap.ap_mark = bom_lots.ap_mark 
								WHERE bom_lots.lots_used_id =lots_use.id AND $fd='$id' AND dis_ver = 0 AND support = 0";
			$q_str .= ' GROUP BY bom_lots.id ';
			//echo $q_str."<br>";
		}
		// echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while($row = $sql->fetch($q_result))
		{
			//echo "BBB";
			$mk = 1;
		    if ($row['ap_mark'] == '' && $row['status'] <> '-1' )
			{
				if($row['ap_mark'] <> 'stock') return 0;
			} 
//echo $row['ap_mark'].' ==  '.$row['status'].'<br>';
		}
		return $mk;
	} // end func
		
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $wi_id=0)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search($table='', $where) {

		$sql = $this->sql;

		$q_str = "SELECT * FROM $table ".$where;
// echo $q_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while($row = $sql->fetch($q_result))
		{
			$result[]=$row;
		}
		return $result;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $wi_id=0)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get($id=0, $acc_code=0) {

		$sql = $this->sql;

		if ($id)	{
			$q_str = "SELECT * FROM ti WHERE id='$id' ";
		} elseif ($acc_code) {
			$q_str = "SELECT * FROM ti WHERE wi_id='$wi_id' ";
		} else {
			$this->msg->add("Error ! 請指明 生產說明資料在資料庫內的 ID.");		    
			return false;
		}

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			$this->msg->add("Error ! 無法找到這筆記錄!");
			return false;    
		}
		return $row;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del_lots($id,$mode=0)		刪除 BOM 主料記錄 
#					[mode=0: $id為bom_lots的id [單筆刪除] ; 
#					[mode=1: $id為wi_id 為整個 將 指定之製令的全部BOM主料記錄都刪除 ]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del_lots($id,$mode=0) {

		$sql = $this->sql;
		if (!$id) {
			$this->msg->add("Error !請指明 BOM的主料資料的 ID.");		    
			return false;
		}
		if($mode == 1){
			$q_str = "DELETE FROM bom_lots WHERE wi_id='$id' ";
		}else if($mode == 2){
			$q_str = "DELETE FROM bom_lots WHERE wi_id='$id' AND ap_mark=''";
		}else{
			$q_str = "DELETE FROM bom_lots WHERE id='$id' ";
		}
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫 !");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del_acc($id,$mode=0)		刪除 BOM 副料記錄 
#					[mode=0: $id為bom_lots的id [單筆刪除] ; 
#					[mode=1: $id為wi_id 為整個 將 指定之製令的全部BOM副料記錄都刪除 ]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del_acc($id,$mode=0) {

		$sql = $this->sql;
		if (!$id) {
			$this->msg->add("Error !請指明 BOM的副料資料的 ID.");		    
			return false;
		}
		if($mode == 1){
			$q_str = "DELETE FROM bom_acc WHERE wi_id='$id' ";
		}else if($mode == 2){
			$q_str = "DELETE FROM bom_acc WHERE wi_id='$id' AND ap_mark='' ";
		}else{
			$q_str = "DELETE FROM bom_acc WHERE id='$id' ";
		}
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫 !");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fields($n_field,$where_str="")	取出全部 部門表內的 $n_field 置入arry return
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_fields_lots($n_field,$where_str="") {
		$sql = $this->sql;
		$fields = array();
		$q_str = "SELECT ".$n_field." FROM bom_lots ".$where_str;

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}

			$match_limit = 500;
			$match = 0;
			while ($row = $sql->fetch($q_result)) {
				$fields[] = $row[0];
				$match++;
				if ($match==500) {
					break;
				}
			}
			if ($match != 500) {   // 保留 尚未作用
				$sql->free_result($q_result);
				$result =0;
				$this->q_result = $q_result;
			}
		
		return $fields;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fields($n_field,$where_str="")	取出全部 部門表內的 $n_field 置入arry return
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_fields_acc($n_field,$where_str="") {
		$sql = $this->sql;
		$fields = array();
		$q_str = "SELECT ".$n_field." FROM bom_acc ".$where_str;

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}

			$match_limit = 500;
			$match = 0;
			while ($row = $sql->fetch($q_result)) {
				$fields[] = $row[0];
				$match++;
				if ($match==500) {
					break;
				}
			}
			if ($match != 500) {   // 保留 尚未作用
				$sql->free_result($q_result);
				$result =0;
				$this->q_result = $q_result;
			}
		
		return $fields;
	} // end func
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		更新 資料記錄內 某個單一欄位
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field($parm) {

		$sql = $this->sql;
		############### 檢查輸入項目
		
		#####   更新資料庫內容
	$q_str = "UPDATE ".$parm[3]." SET ".$parm[1]."='".$parm[2]."'  WHERE id='".$parm[0]."'";
	// echo $q_str.'<br>';

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		更新 資料記錄內 某個單一欄位
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_wi_field($parm) {

		$sql = $this->sql;
		############### 檢查輸入項目
		
		#####   更新資料庫內容
	$q_str = "UPDATE ".$parm[3]." SET ".$parm[1]."='".$parm[2]."'  WHERE wi_id='".$parm[0]."'";
//echo $q_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_dis_lots($wi_id) 取得disable且己採購BOM主料資料
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_dis_lots($wi_id) {
		
		$return = array();
		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv

		$srh = new SEARCH();
		$q_str = "SELECT bom_lots.*, ap_det.po_qty, ap_det.po_unit, ap_det.prics, ap_det.ap_num,
										 lots_use.lots_code, lots_use.lots_name, lots_use.unit, lots_use.use_for,
										 ap_det.rcv_qty, ap.currency, ap.status, ap_det.id as ap_det_id
							FROM bom_lots, ap_det, lots_use, ap
							WHERE bom_lots.lots_used_id = lots_use.id AND bom_lots.id = ap_det.bom_id 
								AND ap.status >= 0
							  AND ap.ap_num = ap_det.ap_num AND bom_lots.dis_ver > 0
								AND ap_det.mat_cat = 'l' AND bom_lots.wi_id = '$wi_id'";
		// echo $q_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}

		while ($row = $sql->fetch($q_result)) {
			$bom_qty = 0;
			$tmp = explode(',',$row['qty']);
			for($i=0; $i<sizeof($tmp); $i++)
			{
				$bom_qty += $tmp[$i];
			}
			$row['bom_qty'] = $bom_qty;

			$tmp_num = substr($row['ap_num'],2);
			$row['po_num'] = "PO".$tmp_num;
			$return[] = $row;
		}


		return $return;
	} // end func	
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_dis_lots($wi_id) 取得disable且己採購BOM主料資料
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_dis_acc($wi_id) {
		
		$return = array();
		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv

		$srh = new SEARCH();
		$q_str = "SELECT bom_acc.*, ap_det.po_qty, ap_det.po_unit, ap_det.prics, ap_det.ap_num, 
										 acc_use.acc_code, acc_use.acc_name, acc_use.unit, acc_use.use_for,
										 ap_det.rcv_qty, ap.currency, ap.status, ap_det.id as ap_det_id
							FROM bom_acc, ap_det, acc_use, ap
							WHERE bom_acc.acc_used_id = acc_use.id AND bom_acc.id = ap_det.bom_id 
							  AND ap.status >= 0
							  AND ap.ap_num = ap_det.ap_num AND bom_acc.dis_ver > 0
							  AND ap_det.mat_cat = 'a' AND bom_acc.wi_id = '$wi_id'";
		// echo $q_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}

		while ($row = $sql->fetch($q_result)) {
			$bom_qty = 0;
			$tmp = explode(',',$row['qty']);
			for($i=0; $i<sizeof($tmp); $i++)
			{
				$bom_qty += $tmp[$i];
			}
			$row['bom_qty'] = $bom_qty;

			$tmp_num = substr($row['ap_num'],2);
			$row['po_num'] = "PO".$tmp_num;

			$return[] = $row;
		}


		return $return;
	} // end func		
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_dis_lots($wi_id) 取得disable且己採購BOM主料資料
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_old_lots($parm) {
		
		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv

		$srh = new SEARCH();
		$q_str = "SELECT bom_lots.*, ap_det.po_qty, ap_det.po_unit, ap_det.prics, ap_det.ap_num,
										 lots_use.lots_code, lots_use.lots_name, lots_use.unit, lots_use.use_for,
										 ap_det.rcv_qty, ap.currency, ap.status, ap_det.id as ap_det_id, ap_det.amount,
										 ap_det.prc_unit
							FROM bom_lots, ap_det, lots_use, ap
							WHERE bom_lots.lots_used_id = lots_use.id AND bom_lots.id = ap_det.bom_id 
								AND ap.status >= 0
							  AND ap.ap_num = ap_det.ap_num AND bom_lots.dis_ver > 0
								AND ap_det.mat_cat = 'l' AND bom_lots.id = '".$parm['bom_id']."'";
				//echo "eval(alert(".$q_str."))";
				//match_pa('PA14-0194','?PHP_action=apply_search_bom&PHP_sr_startno=1&PHP_dept_code=&PHP_cust=&PHP_num=0148&PHP_full=0','3024','lots','21453','99617')
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}

		if ($row = $sql->fetch($q_result)) {
			$bom_qty = 0;
			$tmp = explode(',',$row['qty']);
			for($i=0; $i<sizeof($tmp); $i++)
			{
				$bom_qty += $tmp[$i];
			}
			$row['bom_qty'] = $bom_qty;

			$tmp_num = substr($row['ap_num'],2);
			$row['po_num'] = "PO".$tmp_num;
			$row['mat_code'] = $row['lots_code'];
			$return = $row;
		}
		$q_str = "SELECT bom_lots.*	FROM bom_lots
							WHERE dis_ver = 0 AND ap_mark = ''
							AND color = '".$return['color']."' AND lots_used_id = '".$return['lots_used_id']."'";
							//	echo $q_str;
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		if ($row = $sql->fetch($q_result)) {
			$new_id = $row['id'];
			$new_qty = 0;
			$tmp = explode(',',$row['qty']);
			for($i=0; $i<sizeof($tmp); $i++)
			{
				$new_qty += $tmp[$i];
			}
			$dif_qty = $new_qty - $bom_qty;		//取得差異數量
			$dif_po_qty = $new_qty - $return['po_qty'];		//取得差異數量和PO比
			//	if($dif_po_qty > $dif_qty )$dif_qty = $dif_po_qty;
			$return['dif_qty'] = $dif_qty;
			$return['new_qty'] = $new_qty;
				

			$q_str = "UPDATE ap_det SET old_bom_id ='".$return['id']."'  WHERE id='".$return['ap_det_id']."'";
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! Can't access database!");
				$this->msg->merge($sql->msg);
				return false;    
			}
			
			$q_str = "UPDATE ap_det SET bom_id ='".$new_id."'  WHERE id='".$return['ap_det_id']."'";
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! Can't access database!");
				$this->msg->merge($sql->msg);
				return false;    
			}		

			$q_str = "UPDATE bom_lots SET ap_mark ='".$return['ap_num']."'  WHERE id='".$new_id."'";		
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! Can't access database!");
				$this->msg->merge($sql->msg);
				return false;    
			}
		}
		return $return;
	} // end func


	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_dis_lots($wi_id) 取得disable且己採購BOM主料資料
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_old_acc($parm) {
		
		$return = array();
		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv

		$srh = new SEARCH();
		$q_str = "SELECT bom_acc.*, ap_det.po_qty, ap_det.po_unit, ap_det.prics, ap_det.ap_num, 
										 acc_use.acc_code, acc_use.acc_name, acc_use.unit, acc_use.use_for,
										 ap_det.rcv_qty, ap.currency, ap.status, ap_det.id as ap_det_id, ap_det.amount,
										 ap_det.prc_unit
							FROM bom_acc, ap_det, acc_use, ap
							WHERE bom_acc.acc_used_id = acc_use.id AND bom_acc.id = ap_det.bom_id 
								AND ap.status >= 0
							  AND ap.ap_num = ap_det.ap_num AND bom_acc.dis_ver > 0
							  AND ap_det.mat_cat = 'a' AND bom_acc.id = '".$parm['bom_id']."'";
//		echo $q_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}

		if ($row = $sql->fetch($q_result)) {
			
			$bom_qty = 0;
			$tmp = explode(',',$row['qty']);
			for($i=0; $i<sizeof($tmp); $i++)
			{
				$bom_qty += $tmp[$i];
			}
			$row['bom_qty'] = $bom_qty;

			$tmp_num = substr($row['ap_num'],2);
			$row['po_num'] = "PO".$tmp_num;
			$row['mat_code'] = $row['acc_code'];

			$return = $row;
			
		}

		$q_str = "SELECT bom_acc.*	FROM bom_acc
							WHERE dis_ver = 0 AND ap_mark = ''
							  AND color = '".$return['color']."' AND acc_used_id = '".$return['acc_used_id']."'
							  AND size = '".$return['size']."'";
//		echo $q_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if ($row = $sql->fetch($q_result)) {
				$new_id = $row['id'];
				$new_qty = 0;
				$tmp = explode(',',$row['qty']);
				for($i=0; $i<sizeof($tmp); $i++)
				{
					$new_qty += $tmp[$i];
				}
				$dif_qty = $new_qty - $bom_qty;		//取得差異數量
				$dif_po_qty = $new_qty - $return['po_qty'];		//取得差異數量和PO比
//				if($dif_po_qty > $dif_qty )$dif_qty = $dif_po_qty;

				$return['dif_qty'] = $dif_qty;
				$return['new_qty'] = $new_qty;
				

			$q_str = "UPDATE ap_det SET old_bom_id ='".$return['id']."'  WHERE id='".$return['ap_det_id']."'";
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! Can't access database!");
				$this->msg->merge($sql->msg);
				return false;    
			}
			
			$q_str = "UPDATE ap_det SET bom_id ='".$new_id."'  WHERE id='".$return['ap_det_id']."'";
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! Can't access database!");
				$this->msg->merge($sql->msg);
				return false;    
			}		
			
			$q_str = "UPDATE bom_acc SET ap_mark ='".$return['ap_num']."'  WHERE id='".$new_id."'";		
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! Can't access database!");
				$this->msg->merge($sql->msg);
				return false;    
			}

				
				
		}

		return $return;
	} // end func			
	
	
/*	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_dis_lots($wi_id) 取得disable且己採購BOM主料資料
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_old_lots($bom_id) {
		
		$return = array();
		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv

		$srh = new SEARCH();
		$q_str = "SELECT bom_lots.* FROM bom_lots WHERE id = $bom_id";
//		echo $q_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$bom_row = $sql->fetch($q_result);
		$tmp = explode(',',$bom_row['qty']);
		$bom_qty = 0;
		for($i=0; $i<sizeof($tmp); $i++) $bom_qty += $tmp[$i];


		$q_str = "SELECT bom_lots.*, ap_det.po_qty, ap_det.po_unit, ap_det.prics, ap_det.ap_num,										 
										 ap_det.id as ap_det_id
							FROM bom_lots, ap_det
							WHERE bom_lots.id = ap_det.bom_id AND ap_det.mat_cat = 'l' AND ap_det.ap_num = bom_lots.ap_mark
								AND bom_lots.lots_used_id = '".$bom_row['lots_used_id']."' AND bom_lots.dis_ver > 0
								AND bom_lots.color = '".$bom_row['color']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}

		if ($row = $sql->fetch($q_result)) {
			$tmp = explode(',',$row['qty']);
			$old_qty = 0;
			for($i=0; $i<sizeof($tmp); $i++) $old_qty += $tmp[$i];
			$new_qty = $bom_qty - $old_qty;		//取得差異數量
			$parm = array('new_qty' => $new_qty,
										'old_qty'	=>	$old_qty,
										'bom_qty' =>	$bom_qty,
										'po_qty'	=>	$row['po_qty'],
										'po_unit'	=>	$row['po_unit'],
										'prics'	=>	$row['prics'],
										'ap_num'	=>	$row['ap_num']);
										
			$q_str = "UPDATE ap_det SET old_bom_id ='".$row['id']."'  WHERE id='".$row['ap_det_id']."'";
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! Can't access database!");
				$this->msg->merge($sql->msg);
				return false;    
			}
			
			$q_str = "UPDATE ap_det SET bom_id ='".$bom_id."'  WHERE id='".$row['ap_det_id']."'";
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! Can't access database!");
				$this->msg->merge($sql->msg);
				return false;    
			}		
			$q_str = "UPDATE bom_lots SET ap_mark ='".$row['ap_num']."'  WHERE id='".$bom_id."'";
		
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! Can't access database!");
				$this->msg->merge($sql->msg);
				return false;    
			}			
			
			
			return $parm;
		}


		return 'none';
	} // end func		
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_dis_lots($wi_id) 取得disable且己採購BOM主料資料
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_old_acc($bom_id) {
		
		$return = array();
		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv

		$srh = new SEARCH();
		$q_str = "SELECT bom_acc.* FROM bom_acc WHERE id = $bom_id";
//		echo $q_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$bom_row = $sql->fetch($q_result);
		$tmp = explode(',',$bom_row['qty']);
		$bom_qty = 0;
		for($i=0; $i<sizeof($tmp); $i++) $bom_qty += $tmp[$i];


		$q_str = "SELECT bom_acc.*, ap_det.po_qty, ap_det.po_unit, ap_det.prics, ap_det.ap_num,										 
										 ap_det.rcv_qty, ap_det.id as ap_det_id
							FROM bom_acc, ap_det
							WHERE bom_acc.id = ap_det.bom_id AND ap_det.mat_cat = 'a' AND ap_det.ap_num = bom_acc.ap_mark
								AND bom_acc.acc_used_id = '".$bom_row['acc_used_id']."' AND bom_acc.dis_ver > 0
								AND bom_acc.color = '".$bom_row['color']."' AND bom_acc.size = '".$bom_row['size']."'";
		echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}

		if ($row = $sql->fetch($q_result)) {
			$tmp = explode(',',$row['qty']);
			$old_qty = 0;
			for($i=0; $i<sizeof($tmp); $i++) $old_qty += $tmp[$i];
			$new_qty = $bom_qty - $old_qty;		//取得差異數量
			$parm = array('new_qty' => $new_qty,
										'old_qty'	=>	$old_qty,
										'bom_qty' =>	$bom_qty,
										'po_qty'	=>	$row['po_qty'],
										'po_unit'	=>	$row['po_unit'],
										'prics'	=>	$row['prics'],
										'ap_num'	=>	$row['ap_num']);
			

			$q_str = "UPDATE ap_det SET old_bom_id ='".$row['id']."'  WHERE id='".$row['ap_det_id']."'";
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! Can't access database!");
				$this->msg->merge($sql->msg);
				return false;    
			}
			
			$q_str = "UPDATE ap_det SET bom_id ='".$bom_id."'  WHERE id='".$row['ap_det_id']."'";
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! Can't access database!");
				$this->msg->merge($sql->msg);
				return false;    
			}		
			$q_str = "UPDATE bom_acc SET ap_mark ='".$row['ap_num']."'  WHERE id='".$bom_id."'";
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! Can't access database!");
				$this->msg->merge($sql->msg);
				return false;    
			}				

			return $parm;
		}


		return 'none';
	} // end func			
*/	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $wi_id=0)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_aply_quck($id=0, $table='', $fd='wi_id',$cate='x') {

		$sql = $this->sql;
		$rtn = 1;
		$mk=2;
		if($table == 'bom_acc')
		{		
			$q_str = "SELECT ap_mark FROM bom_acc, acc_use  
								WHERE  bom_acc.acc_used_id = acc_use.id AND $fd='$id' AND dis_ver = 0 AND support = 0 AND
											 ap_mark = ''";	
		}

		if($table == 'bom_lots')
		{		
			$q_str = "SELECT ap_mark FROM bom_lots, lots_use  
								WHERE bom_lots.lots_used_id =lots_use.id AND $fd='$id' AND dis_ver = 0 AND support = 0 AND
											ap_mark = ''";
		}
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		$non_po = array();
		while($row = $sql->fetch($q_result))
		{
			$non_po[] = $row;		
		}

		if(isset($non_po[0]['id']))
		{
			for($i=0; $i<sizeof($non_po); $i++)
			{
				if($table == 'bom_acc') $mat_cat = 'a';
				if($table == 'bom_lots') $mat_cat = 'l';
		
				$q_str = "SELECT id FROM ap_det
									WHERE  ap_det.bom_id = '".$non_po[$i]['id']."' AND ap_det.mat_cat = '".$mat_cat."'";
				$q_result = $sql->query($q_str);
				if(!$row = $sql->fetch($q_result))
				{			
					return 0;
				}	
			}
		}
		
		if($table == 'bom_acc')
		{		
			$q_str = "SELECT ap.status FROM bom_acc, acc_use, ap  
								WHERE  bom_acc.acc_used_id = acc_use.id AND ap.ap_num = bom_acc.ap_mark AND 
											 $fd='$id' AND dis_ver = 0 AND support = 0 AND ap.status < 12 AND ap.status > 0";
		}

		if($table == 'bom_lots')
		{		
			$q_str = "SELECT ap.status FROM bom_lots, lots_use, ap  
								WHERE bom_lots.lots_used_id =lots_use.id AND ap.ap_num = bom_lots.ap_mark AND 
											$fd='$id' AND dis_ver = 0 AND support = 0 AND ap.status < 12 AND ap.status > 0";
		}
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if($row = $sql->fetch($q_result))
		{
			return 0;			
		}		
		return $mk;
	} // end func	
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_log($id=0) 取得bom的log檔資料
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_log($id) {

		$sql = $this->sql;
		$q_str = "SELECT * FROM bom_log  WHERE  wi_id = '".$id."'";	
	
		$rtn = array();
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while($row = $sql->fetch($q_result))
		{
			$bom_user=$GLOBALS['user']->get(0,$row['user']);
			$row['user_id'] = $row['user'];
			if ($bom_user['name'])$row['user'] = $bom_user['name'];
			$rtn[] = $row;			
		}

		return $rtn;
	} // end func	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit($parm,$mode=0)		更新 訂單 記錄 
#			mode=0 : EDIT    mode=1 : REVISE
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_log($parm) {
	$sql = $this->sql;
	$parm['des']=str_replace("'", "\'",$parm['des']);
			$q_str = "INSERT INTO bom_log (wi_id,user,des,k_date) 
				  VALUES('".							
							$parm['wi_id']."','".
							$parm['user']."','".							
							$parm['des']."','".																													
							$parm['k_date']."')";
							//echo $q_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't insert.");
			$this->msg->merge($sql->msg);
			return false;    
		}   			
		
	return true;
	}// end func		
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_ord_size($PHP_wi_id){
	$sql = $this->sql;
	$q_str = "SELECT size_des.size FROM wi,s_order,size_des WHERE wi.id='$PHP_wi_id' and wi.wi_num=s_order.order_num and size_des.id=s_order.size";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	if (!$ord_size = $sql->fetch($q_result)) {
		$this->msg->add("Error ! Can't find this record!");
		return false;    
	}
	return $ord_size;
}

function get_p_id($PHP_wi_id){
	$sql = $this->sql;
	$q_str = "SELECT order_partial.id FROM wi,order_partial WHERE wi.id='$PHP_wi_id' and wi.wi_num=order_partial.ord_num order by order_partial.p_etd asc";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	while($row = $sql->fetch($q_result)) {
		$rtn[]=$row;
	}
	
	return $rtn;
}

function get_ord_qty($p_id){
		$sql = $this->sql;
		$q_str = "SELECT qty FROM wiqty WHERE wiqty.p_id='".$p_id."' order by id asc";
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while($row2 = $sql->fetch($q_result)) {
			$p_qty[]=$row2;
		}
	return $p_qty;	
}

#get_fty_pattern
function get_fty_pattern($wi_id){
	$sql = $this->sql;
	$q_str = "SELECT * FROM fty_pattern WHERE wi_id='".$wi_id."' order by id asc";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	while($row = $sql->fetch($q_result)) {
		$fty_pattern[]=$row;
	}
	return $fty_pattern;
}

#加入資料庫
function add_fty_pattern($parm){
	$sql = $this->sql;
	$q_str = "INSERT INTO fty_pattern(wi_id,file_name,file_ext,file_date,user) VALUES('".
				$parm['wi_id']."','".
				$parm['file_name']."','".
				$parm['file_ext']."','".
				$parm['file_date']."','".	
				$parm['user']."'
			)";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	return $sql->insert_id();
}





function get_cost($wi_id,$qty,$bcfm_date='',$opendate=''){

	$sql = $this->sql;
	
	$q_str = "
	SELECT 
	`bom_lots`.`id` as `bom_id` , `bom_lots`.`qty` as `bom_qty` , `bom_lots`.`ap_mark` , `bom_lots`.`pp_mark` , `bom_lots`.`color` , 
	`lots_use`.`lots_code` , `lots_use`.`unit` as `bom_unit` 
	
	FROM 
	`bom_lots` , `lots_use`
	
	WHERE 
	`bom_lots`.`wi_id` = '".$wi_id."' AND 
	`bom_lots`.`lots_used_id` = `lots_use`.`id` AND 
	`lots_use`.`support` = '0' 
	;";
	
	if (!$bom_q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	// echo '<br>'.$q_str.'<br>'; #  rate_date
	$ck_bom_lots = $ck_bom_lots_qty = $ck_po_lots = $ck_po_lots_qty = $bom_lots_ttl = $bom_lots_ttl = $po_lots_ttl = $rcv_lots_ttl = $stock = 0;
	while($bom_row = $sql->fetch($bom_q_result)) {
		
		$rows = array();
		
		# 紀錄 bom 比數
		$ck_bom_lots++;
		$ck_po_lots++;
		
		# ap_det.id + ap_det.po_spare + receive_det.id => rcv 索引
		
		# 判斷有無用庫存
		
		if( $bom_row['ap_mark'] == 'stock' ){
			
			$stock += 1;
			
			# 紀錄 bom 比數
			$ck_bom_lots_qty++;
			$ck_po_lots_qty++;
			
		} else {
		
			# 紀錄 bom 比數
			if ( $bom_row['ap_mark'] != '' ) $ck_po_lots_qty++;
			
			if( $bom_row['pp_mark'] == '1' ){
			
				$q_str = "
				SELECT 
				`ap`.`po_apv_date` , `ap`.`currency` , 
				`ap_special`.`id` as `po_id` , `ap_special`.`po_qty` , `ap_special`.`po_unit` , `ap_special`.`unit` as `prc_unit` , `ap_special`.`prics` , `ap_special`.`po_spare` 

				FROM 
				`ap_special`,`ap`

				WHERE 
				`ap`.`ap_num` = '".$bom_row['ap_mark']."' AND 
				`ap_special`.`color` = '".$bom_row['color']."' AND 
				`ap_special`.`mat_code` = '".$bom_row['lots_code']."' AND 
				`ap`.`ap_num` = `ap_special`.`ap_num` AND 
				`ap_special`.`mat_cat` = 'l' 
				
				;";
				
			} else {
			
				$q_str = "
				SELECT 
				`ap`.`po_apv_date` , `ap`.`currency` , 
				`ap_det`.`id` as `po_id` , `ap_det`.`po_qty` , `ap_det`.`po_unit` , `ap_det`.`prc_unit` , `ap_det`.`prics` , `ap_det`.`po_spare` 

				FROM 
				`ap_det`,`ap`
				
				WHERE 
				`ap_det`.`bom_id` = '".$bom_row['bom_id']."' AND 
				`ap`.`ap_num` = `ap_det`.`ap_num` AND 
				`ap`.`status` = '12'  AND 
				`ap_det`.`mat_cat` = 'l' 
				
				;";
			
			}
			
			$ap_q_results = $sql->query($q_str);
			
			$po_qty = 0;
			$po_unit = '';
			$prc_unit = '';
			
			$rcv_qty = 0;
			$rcv_currency = '';

			// echo '<br>'.$q_str.'<br>';
			while($ap_row = $sql->fetch($ap_q_results)) {

				# PO
				# 累加採購用量
				
				$po_qty += $ap_row['po_qty'];
				$po_unit = $ap_row['po_unit'];
				$prc_unit = $ap_row['prc_unit'];
				
				# PO 匯率時間
				$po_apv_date = $ap_row['po_apv_date'];
				#echo '<br>po_apv_date'.$po_apv_date.'<br>'; acc_rcv_ttl
				
				$q_str = "
				SELECT 
				`receive`.`rcv_sub_date` ,
				`rcv_po_link`.`qty` as `rcv_qty` , `rcv_po_link`.`currency` , `rcv_po_link`.`id` as `rcv_ids`
				
				FROM 
				`receive`,`receive_det`,`rcv_po_link`
				
				WHERE 
				`receive_det`.`po_id` = '".$ap_row['po_spare']."' AND 
				`rcv_po_link`.`po_id` = '".$ap_row['po_id']."' AND 
				`receive_det`.`mat_code` = '".$bom_row['lots_code']."' AND 
				`receive`.`rcv_num` = `receive_det`.`rcv_num` AND 
				`receive`.`tw_rcv` = '0' AND 
				`receive_det`.`id` = `rcv_po_link`.`rcv_id` 
				;";
				// echo '<br>'.$q_str.'<br>';
				$rcv_q_resultss = $sql->query($q_str);

				while($rcv_row = $sql->fetch($rcv_q_resultss)) {

					# RCV
					# 累加驗收用量
					$rcv_qty += $rcv_row['rcv_qty'];
					$rcv_currency = $rcv_row['currency'];
					
					
					# RCV 匯率時間
					$rcv_sub_date = $rcv_row['rcv_sub_date'];
					#echo '<br>rcv_sub_date'.$rcv_sub_date.'<br>';
					
					
					$q_str = "
					SELECT 
					`apb`.`inv_date` 
					
					FROM 
					`apb`,`apb_det`
					
					WHERE 
					`apb`.`rcv_num` = `apb_det`.`rcv_num` AND 
					`apb_det`.`po_id` = '".$ap_row['po_spare']."'
					;";
					
					$apb_q_result = $sql->query($q_str);
					
					while($apb_row = $sql->fetch($apb_q_result)) {
					
						# APB 匯率時間
						$inv_date = $apb_row['inv_date'];
						#echo '<br> inv_date = '.$inv_date.'<br>';
						
					}
					// 2015/07/02 增加 $opendate 
					// $p_date = !empty($inv_date) ? $inv_date : (!empty($rcv_sub_date) ? $rcv_sub_date : (!empty($po_apv_date) ? $po_apv_date : (!empty($bcfm_date) ? $bcfm_date : '' )));
					$p_date = $inv_date != '0000-00-00' ? $inv_date : ( $rcv_sub_date != '0000-00-00' ? $rcv_sub_date : ( $po_apv_date != '0000-00-00' ? $po_apv_date : ( $bcfm_date != '0000-00-00' ? $bcfm_date : $opendate )));
					
					
					# RCV 單件用量
					if ( $rcv_row['currency'] == "NTD" ) {
						$tm = $rcv_lots_ttl += ( $rcv_row['rcv_qty'] * change_unit_price($prc_unit,$po_unit,$ap_row['prics']) ); 
						#echo 'RCV( '. $rcv_row['rcv_qty'] . ' * ' . change_unit_price($prc_unit,$po_unit,$ap_row['prics']) . ' ) / ' . $qty . ' = '.$tm.' currency = '.$rcv_row['currency'].' ~ '.$rcv_row['rcv_ids'].'<br>';
					} else {
						$tm = $rcv_lots_ttl += ( $rcv_row['rcv_qty'] * change_unit_price($prc_unit,$po_unit,$ap_row['prics']) * $GLOBALS['rate']->get_rate($rcv_row['currency'],$p_date) ); 
						#echo 'RCV( '. $rcv_row['rcv_qty'] . ' * ' . change_unit_price($prc_unit,$po_unit,$ap_row['prics']) . ' * ' . $GLOBALS['rate']->get_rate($rcv_row['currency'],$p_date)  . ' ) / ' . $qty . ' = '.$tm.' currency = '.$rcv_row['currency'].' ~ '.$rcv_row['rcv_ids'].'<br>';
					}
					
				}
			
				# PO 單件用量
				if ( $ap_row['currency'] == "NTD" ) {
					$tm = $po_lots_ttl += ( $ap_row['po_qty'] * change_unit_price($prc_unit,$po_unit,$ap_row['prics']) ); 
					#echo 'PO( '. $ap_row['po_qty'] . ' * ' . change_unit_price($prc_unit,$po_unit,$ap_row['prics']) . ' ) / ' . $qty . ' = '.$tm.' currency = '.$ap_row['currency'].' ~ '. $ap_row['po_id'] .'<br>';
				} else {
					$tm = $po_lots_ttl += ( $ap_row['po_qty'] * change_unit_price($prc_unit,$po_unit,$ap_row['prics']) * $GLOBALS['rate']->get_rate($ap_row['currency'],$p_date) ); 
					#echo 'PO( '. $ap_row['po_qty'] . ' * ' . change_unit_price($prc_unit,$po_unit,$ap_row['prics']) . ' * ' . $GLOBALS['rate']->get_rate($ap_row['currency'],$p_date)  . ' ) / ' . $qty . ' = '.$tm.' currency = '.$ap_row['currency']. ' ~ '. $ap_row['po_id'] .'<br>';
				}

				#echo '<br> 總結 = '.$p_date.'<br>';
				$currency = $ap_row['currency'];
				$prics = $ap_row['prics'];
			}

			# BOM
			$rows['bom_id'] = $bom_row['bom_id'];
			# 累加 BOM 用量
			$rows['bom_qty'] = array_sum(explode(',',$bom_row['bom_qty']));
			$rows['bom_unit'] = $bom_row['bom_unit'];
			
			// 2015/07/02 增加 $opendate 
			$p_date = $p_date ? $p_date : $opendate;
			# BOM 單件用量
			if ( $currency == "NTD" ) {
				$tm = $bom_lots_ttl += ( $rows['bom_qty'] * change_unit_price($po_unit,$bom_row['bom_unit'],$prics) ); 
				// echo 'BOM( '. $rows['bom_qty'] . ' * ' . change_unit_price($po_unit,$bom_row['bom_unit'],$prics) . ' ) / ' . $qty . ' = '.$tm.' currency = '.$currency.'<br>';
			} else {
				$tm = $bom_lots_ttl += ( $rows['bom_qty'] * change_unit_price($po_unit,$bom_row['bom_unit'],$prics) * $GLOBALS['rate']->get_rate($currency,$p_date) ); 
				// echo 'BOM( '. $rows['bom_qty'] . ' * ' . change_unit_price($po_unit,$bom_row['bom_unit'],$prics) . ' * ' . $GLOBALS['rate']->get_rate($currency,$p_date)  . ' ) / ' . $qty . ' = '.$tm.' currency = '.$currency.'<br>';
			}
		

			
			# PO
			$rows['po_qty'] = $po_qty;
			$rows['po_unit'] = $po_unit;
			
			# RCV
			$rows['rcv_qty'] = $rcv_qty;
			$rows['rcv_currency'] = $rcv_currency;
			
			# 紀錄 bom 比數
			if( $rows['bom_qty'] > 0 ) $ck_bom_lots_qty ++;
		
			$bom[]=$rows;
			
		}
	}

	// echo '<br>bom_lots_ttl = '.$bom_lots_ttl.'<br>';
	// echo '<br>bom_lots_ttl = '.$bom_lots_ttl / $qty.'<br>';
	// echo '<br>po_lots_ttl = '.$po_lots_ttl.'<br>';
	// echo '<br>po_lots_ttl = '.$po_lots_ttl / $qty.'<br>';
	// echo '<br>rcv_lots_ttl = '.$rcv_lots_ttl.'<br>';
	// echo '<br>rcv_lots_ttl = '.$rcv_lots_ttl / $qty.'<br>';
	$mode['lots_bom_ttl'] = $bom_lots_ttl;
	$mode['lots_po_ttl'] = $po_lots_ttl;
	$mode['lots_rcv_ttl'] = $rcv_lots_ttl;
	$mode['lots_stock'] = ( $stock > 0 ) ? 1 : 0 ;
	
	
	$q_str = "
	SELECT 
	`bom_acc`.`id` as `bom_id` , `bom_acc`.`qty` as `bom_qty` , `bom_acc`.`ap_mark` , `bom_acc`.`pp_mark` , `bom_acc`.`color` , 
	`acc_use`.`acc_code` , `acc_use`.`unit` as `bom_unit` 
	
	FROM 
	`bom_acc` , `acc_use`
	
	WHERE 
	`bom_acc`.`wi_id` = '".$wi_id."' AND 
	`bom_acc`.`acc_used_id` = `acc_use`.`id`  AND 
	`acc_use`.`support` = '0' 

	;";
	
	if (!$bom_q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	
	$ck_bom_acc = $ck_bom_acc_qty = $ck_po_acc = $ck_po_acc_qty = $bom_acc_ttl = $po_acc_ttl = $rcv_acc_ttl = $support = 0;
	while($bom_row = $sql->fetch($bom_q_result)) {
		
		$rows = array();
		
		# 紀錄 bom 比數
		$ck_bom_acc ++;
		$ck_po_acc ++;

		# ap_det.id + ap_det.po_spare + receive_det.id => rcv 索引

		if( $bom_row['ap_mark'] == 'stock' ){
			
			$stock += 1;
			# 紀錄 bom 比數
			$ck_bom_acc_qty++;
			$ck_po_acc_qty++;
		
		} else {
		
			# 紀錄 bom 比數
			if ( $bom_row['ap_mark'] != '' ) $ck_po_acc_qty++;
			
			if( $bom_row['pp_mark'] == '1' ){
			
				$q_str = "
				SELECT 
				`ap`.`po_apv_date` , `ap`.`currency` , 
				`ap_special`.`id` as `po_id` , `ap_special`.`po_qty` , `ap_special`.`po_unit` , `ap_special`.`unit` as `prc_unit` , `ap_special`.`prics` , `ap_special`.`po_spare` 

				FROM 
				`ap_special`,`ap`

				WHERE 
				`ap`.`ap_num` = '".$bom_row['ap_mark']."' AND 
				`ap_special`.`color` = '".$bom_row['color']."' AND 
				`ap`.`ap_num` = `ap_special`.`ap_num` AND 
				`ap_special`.`mat_code` = '".$bom_row['acc_code']."' AND 	
				`ap`.`status` = '12'  AND 				
				`ap_special`.`mat_cat` = 'a' 
				
				;";
				
			} else {
			
				$q_str = "
				SELECT 
				`ap`.`po_apv_date` , `ap`.`currency` , 
				`ap_det`.`id` as `po_id` , `ap_det`.`po_qty` , `ap_det`.`po_unit` , `ap_det`.`prc_unit` , `ap_det`.`prics` , `ap_det`.`po_spare` 

				FROM 
				`ap_det`,`ap`
				
				WHERE 
				`ap_det`.`bom_id` = '".$bom_row['bom_id']."' AND 
				`ap`.`ap_num` = `ap_det`.`ap_num` AND 
				`ap_det`.`mat_cat` = 'a' 
				
				;";
				
			}
			// echo '<br>'.$q_str.'<br>';
			$ap_q_results = $sql->query($q_str);
			
			$po_qty = 0;
			$po_unit = '';
			$prc_unit = '';
			
			$rcv_qty = 0;
			$rcv_currency = '';

			
			while($ap_row = $sql->fetch($ap_q_results)) {

				# PO
				# 累加採購用量
				
				$po_qty += $ap_row['po_qty'];
				$po_unit = $ap_row['po_unit'];
				$prc_unit = $ap_row['prc_unit'];
				
				# PO 匯率時間
				$po_apv_date = $ap_row['po_apv_date'];
				#echo '<br>po_apv_date'.$po_apv_date.'<br>';
				
				$q_str = "
				SELECT 
				`receive`.`rcv_sub_date` ,
				`rcv_po_link`.`qty` as `rcv_qty` , `rcv_po_link`.`currency` 
				
				FROM 
				`receive`,`receive_det`,`rcv_po_link`
				
				WHERE 
				`receive_det`.`po_id` = '".$ap_row['po_spare']."' AND 
				`rcv_po_link`.`po_id` = '".$ap_row['po_id']."' AND 
				`receive_det`.`mat_code` = '".$bom_row['acc_code']."' AND 	
				`receive`.`rcv_num` = `receive_det`.`rcv_num` AND 
				`receive`.`tw_rcv` = '0' AND 
				`receive_det`.`id` = `rcv_po_link`.`rcv_id` 
				;";
				// echo '<br>'.$q_str.'<br>';
				$rcv_q_resultss = $sql->query($q_str);

				while($rcv_row = $sql->fetch($rcv_q_resultss)) {

					# RCV
					# 累加驗收用量
					$rcv_qty += $rcv_row['rcv_qty'];
					$rcv_currency = $rcv_row['currency'];
					
					
					# RCV 匯率時間
					$rcv_sub_date = $rcv_row['rcv_sub_date'];
					#echo '<br>rcv_sub_date'.$rcv_sub_date.'<br>';
					
					
					$q_str = "
					SELECT 
					`apb`.`inv_date` 
					
					FROM 
					`apb`,`apb_det`
					
					WHERE 
					`apb`.`rcv_num` = `apb_det`.`rcv_num` AND 
					`apb_det`.`po_id` = '".$ap_row['po_spare']."'
					;";
					
					$apb_q_result = $sql->query($q_str);
					
					while($apb_row = $sql->fetch($apb_q_result)) {
					
						# APB 匯率時間
						$inv_date = $apb_row['inv_date'];
						// echo '<br> inv_date = '.$inv_date.'<br>';
						
					}
					
					// 2015/07/02 增加 $opendate 
					$p_date = $inv_date != '0000-00-00' ? $inv_date : ( $rcv_sub_date != '0000-00-00' ? $rcv_sub_date : ( $po_apv_date != '0000-00-00' ? $po_apv_date : ( $bcfm_date != '0000-00-00' ? $bcfm_date : $opendate )));
					// echo $p_date;
					
					// 2015/07/02 增加 $opendate 
					$p_date = $p_date ? $p_date : $opendate;
					# RCV 單件用量
					if ( $rcv_row['currency'] == "NTD" ) {
						$tm = ( $rcv_row['rcv_qty'] * change_unit_price($prc_unit,$po_unit,$ap_row['prics']) ); 
						$rcv_acc_ttl += $tm;
						#echo 'RCV( '. $rcv_row['rcv_qty'] . ' * ' . change_unit_price($prc_unit,$po_unit,$ap_row['prics']) . ' ) / ' . $qty . ' = '.$tm.' currency = '.$rcv_row['currency'].'<br>';
					} else {
						$tm = ( $rcv_row['rcv_qty'] * change_unit_price($prc_unit,$po_unit,$ap_row['prics']) * $GLOBALS['rate']->get_rate($rcv_row['currency'],$p_date) ); 
						$rcv_acc_ttl += $tm;
						#echo 'RCV( '. $rcv_row['rcv_qty'] . ' * ' . change_unit_price($prc_unit,$po_unit,$ap_row['prics']) . ' * ' . $GLOBALS['rate']->get_rate($rcv_row['currency'],$p_date)  . ' ) / ' . $qty . ' = '.$tm.' currency = '.$rcv_row['currency'].'<br>';
					}
					
				}
			
				# PO 單件用量
				$tm = 0;
				if ( $ap_row['currency'] == "NTD" ) {
					$tm = ( $ap_row['po_qty'] * change_unit_price($prc_unit,$po_unit,$ap_row['prics']) ); 
					$po_acc_ttl += $tm;
					#echo 'PO( '. $ap_row['po_qty'] . ' * ' . change_unit_price($prc_unit,$po_unit,$ap_row['prics']) . ' ) / ' . $qty . ' = '.$tm.' currency = '.$ap_row['currency'].'<br>';
				} else {
					$tm = ( $ap_row['po_qty'] * change_unit_price($prc_unit,$po_unit,$ap_row['prics']) * $GLOBALS['rate']->get_rate($ap_row['currency'],$p_date) ); 
					$po_acc_ttl += $tm;
					#echo 'PO( '. $ap_row['po_qty'] . ' * ' . change_unit_price($prc_unit,$po_unit,$ap_row['prics']) . ' * ' . $GLOBALS['rate']->get_rate($ap_row['currency'],$p_date)  . ' ) / ' . $qty . ' = '.$tm.' currency = '.$ap_row['currency'].'<br>';
				}

				#echo '<br> 總結 = '.$p_date.'<br>';
				$currency = $ap_row['currency'];
				$prics = $ap_row['prics'];
			}

			# BOM
			$rows['bom_id'] = $bom_row['bom_id'];
			# 累加 BOM 用量
			$rows['bom_qty'] = array_sum(explode(',',$bom_row['bom_qty']));
			$rows['bom_unit'] = $bom_row['bom_unit'];

			// 2015/07/02 增加 $opendate 
			$p_date = $p_date ? $p_date : $opendate;
			if (  $currency == "NTD" ) {
				$tm = ( $rows['bom_qty'] * change_unit_price($prc_unit,$bom_row['bom_unit'],$prics) ); 
				$bom_acc_ttl += $tm;
				#echo 'BOM( '. $rows['bom_qty'] . ' * ' . change_unit_price($prc_unit,$rows['bom_unit'],$prics) . ' ) / ' . $qty . ' = '.$tm.' currency = '.$currency.'<br>';
			} else {
				$tm = ( $rows['bom_qty'] * change_unit_price($prc_unit,$bom_row['bom_unit'],$prics) * $GLOBALS['rate']->get_rate($currency,$p_date) ); 
				$bom_acc_ttl += $tm;
				#echo 'BOM( '. $rows['bom_qty'] . ' * ' . change_unit_price($prc_unit,$bom_row['bom_unit'],$prics) . ' * ' . $GLOBALS['rate']->get_rate($currency,$p_date)  . ' ) / ' . $qty . ' = '.$tm.' currency = '.$currency.'<br>';
			}		
			
			# PO
			$rows['po_qty'] = $po_qty;
			$rows['po_unit'] = $po_unit;
			
			# RCV
			$rows['rcv_qty'] = $rcv_qty;
			$rows['rcv_currency'] = $rcv_currency;
			
			# 紀錄 bom 比數
			if( $rows['bom_qty'] > 0 ) $ck_bom_acc_qty ++;
		
			$bom[]=$rows;
		}
	}

	// echo '<br>ck_bom_lots = '.$ck_bom_lots.'<br>';
	// echo '<br>ck_bom_lots_qty = '.$ck_bom_lots_qty.'<br>';
	// echo '<br>ck_bom_acc = '.$ck_bom_acc.'<br>';
	// echo '<br>ck_bom_acc_qty = '.$ck_bom_acc_qty.'<br>';
	
	// echo '<br>ck_po_lots = '.$ck_po_lots.'<br>';
	// echo '<br>ck_po_lots_qty = '.$ck_po_lots_qty.'<br>';
	// echo '<br>ck_po_acc = '.$ck_po_acc.'<br>';
	// echo '<br>ck_po_acc_qty = '.$ck_po_acc_qty.'<br>';
	
	$mode['ck_bom_lots'] = $ck_bom_lots;
	$mode['ck_bom_lots_qty'] = $ck_bom_lots_qty;
	$mode['ck_po_lots_qty'] = $ck_po_lots_qty;
	
	$mode['ck_bom_acc'] = $ck_bom_acc;
	$mode['ck_bom_acc_qty'] = $ck_bom_acc_qty ;
	$mode['ck_po_acc_qty'] = $ck_po_acc_qty ;
	
	
	
	
	// echo '<br>bom_acc_ttl = '.$bom_acc_ttl.'<br>';
	// echo '<br>bom_acc_ttl = '.$bom_acc_ttl / $qty.'<br>';
	// echo '<br>po_acc_ttl = '.$po_acc_ttl.'<br>';
	// echo '<br>po_acc_ttl = '.$po_acc_ttl / $qty.'<br>';
	// echo '<br>rcv_acc_ttl = '.$rcv_acc_ttl.'<br>';
	// echo '<br>rcv_acc_ttl = '.$rcv_acc_ttl / $qty.'<br>';
	$mode['acc_bom_ttl'] = $bom_acc_ttl;
	$mode['acc_po_ttl'] = $po_acc_ttl;
	$mode['acc_rcv_ttl'] = $rcv_acc_ttl;
	
	$mode['acc_stock'] = ( $stock > 0 ) ? 1 : 0 ;
	
	$mode['rate_date'] = $p_date;
	
	
	
	
	
	
	
	
	
	
	
	
	
	return $mode;
}


# 
function add_bom_photo($parm){

	$sql = $this->sql;
    
    $q_str = "
    SELECT `wi_id` 
    FROM `bom_photo`
    WHERE 
    `wi_id` = '".$parm['wi_id']."' AND 
    `mat` = '".$parm['mat']."' AND 
    `bom_id` = '".$parm['bom_id']."' 
    ;";

    if (!$q_result = $sql->query($q_str)) {
        $this->msg->add("Error ! 無法存取資料庫!");
        $this->msg->merge($sql->msg);
        return false;    
    }

    if (!$row = $sql->fetch($q_result)) {
        $q_str = "INSERT INTO `bom_photo`( `wi_id` , `mat` , `bom_id` , `creator` ,`creat_date` ) VALUES('".
        $parm['wi_id']."','".
        $parm['mat']."','".
        $parm['bom_id']."','".
        $parm['creator']."',NOW())";

        if (!$q_result = $sql->query($q_str)) {
            $this->msg->add("Error ! Database can't access!");
            $this->msg->merge($sql->msg);
            return false;    
        }
        
        return $sql->insert_id();
    } else {
        $q_str = "UPDATE `bom_photo` SET `creator` = '".$parm['creator']."' , `creat_date` = NOW() 
        WHERE `wi_id` = '".$parm['wi_id']."' AND `mat` = '".$parm['mat']."' AND `bom_id` = '".$parm['bom_id']."' ";
        if (!$q_result = $sql->query($q_str)) {
            $this->msg->add("Error ! Database can't access!");
            $this->msg->merge($sql->msg);
            return false;
        }
    
    }
}


# 
function get_bom_photo($wi_id){

	$sql = $this->sql;
    
    $q_str = "
    SELECT * 
    FROM `bom_photo`
    WHERE 
    `wi_id` = '".$wi_id."' 
    ;";

    if (!$q_result = $sql->query($q_str)) {
        $this->msg->add("Error ! 無法存取資料庫!");
        $this->msg->merge($sql->msg);
        return false;    
    }
    $bom_photo = array();
    while($bom_row = $sql->fetch($q_result)) {
        $mat = ( $bom_row['mat'] == 'l' ) ? 'lots' : 'acc' ;
        $status = 0 ;
        $status = ( $bom_row['creator'] != '' ) ? 1 : $status ;
        $status = ( $bom_row['confirmer'] != '' ) ? 2 : $status ;
        
        $user_row = array();
        if ( $bom_row['creator'] ){
            $q_strs = " SELECT * FROM `user` WHERE `id` = '".$bom_row['creator']."' ;";
            $q_results = $sql->query($q_strs);
            $user_row = $sql->fetch($q_results);
        }
        
        $bom_photo[$mat][$bom_row['bom_id']]['creator'] = $user_row['name'];
        $bom_photo[$mat][$bom_row['bom_id']]['creat_date'] = $bom_row['creat_date'];
        
        $user_row = array();
        if ( $bom_row['confirmer'] ){
            $q_strs = " SELECT * FROM `user` WHERE `id` = '".$bom_row['confirmer']."' ;";
            $q_results = $sql->query($q_strs);
            $user_row = $sql->fetch($q_results);
        }        
        
        $bom_photo[$mat][$bom_row['bom_id']]['confirmer'] = $user_row['name'];
        $bom_photo[$mat][$bom_row['bom_id']]['confirm_date'] = $bom_row['confirm_date'];
        $bom_photo[$mat][$bom_row['bom_id']]['status'] = $status;
    }
    
    return $bom_photo;
}


} // end class


?>