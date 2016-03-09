<?php 

#++++++++++++++++++++++ SEASON  class ++++  季節類別 +++++++++++++++++++++++++++++++++++
#	->init($sql)				啟始 (使用 Msg_handle(); 先聯上 sql)
#	->add($parm)				加入
#	->search($mode=0)			搜尋   
#	->get($id=0, nbr=0)			抓出指定 記錄內資料   
#	->update($parm)				更新 資料
#	->update_field($parm)		更新 資料內 某個單一欄位
#	->del($id)					刪除 資料錄
#	->get_fields($n_field,$where_str="")	取出全部 SEASON 的 $n_field 置入arry return
#
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class SCHEDULE {

var $sql;
var $msg;

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->init($sql)	啟始(使用 Msg_handle() ; 先聯上 sql)
function init($sql) {
    $this->msg = new MSG_HANDLE();

    if (!$sql) {
        $this->msg->add("Error ! Database can't connect.");
        return false;
    }
    $this->sql = $sql;
    return true;
} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# 取得工廠預估排產最後時間
function get_last_date($fty) {

$sql = $this->sql;

$q_str = "SELECT `schedule`.`rel_etf`  FROM `schedule` WHERE  `fty` = '".$fty."' ORDER BY `rel_etf` DESC LIMIT 1";
$q_result = $sql->query($q_str);
if($ord_sch = $sql->fetch($q_result)){
    return $ord_sch[0];
}
return false;

} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# 取得排產訂單
function get_schedule_num($fty,$order_num='') {

$sql = $this->sql;

$q_str = "SELECT s_order.order_num, s_order.partial_num, order_partial.mks
FROM s_order,order_partial
WHERE 
s_order.order_num = order_partial.ord_num AND
`factory` = '".$fty."' AND
order_partial.pdt_status < 4 
ORDER BY order_partial.ord_num,order_partial.mks";

if (!$q_result = $sql->query($q_str)) {
	$_SESSION['MSG'][] = "Error! Can't access database!";
	return false;    
}

while ($row = $sql->fetch($q_result)) {
    $mks = $row['partial_num'] > 1 ? '_'.$row['mks'] : '' ;
	$fields[] = $row['order_num'].$mks;
}

if(!empty($fields))
return $fields;
return false;

} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# 取得訂單資訊
function get_order_info($fty,$order_num,$mks) {

$sql = $this->sql;

$q_str = "SELECT 
s_order.order_num, s_order.partial_num, s_order.style, 
order_partial.mks, order_partial.id as p_id, order_partial.p_etd , order_partial.p_qty , order_partial.ext_qty 
FROM s_order,order_partial
WHERE 
s_order.order_num = order_partial.ord_num AND
s_order.order_num = '".$order_num."' AND 
order_partial.mks = '".$mks."' 
ORDER BY order_partial.id";


if (!$q_result = $sql->query($q_str)) {
	$_SESSION['MSG'][] = "Error! Can't access database!".$q_str;
	return false;    
}

while ($row = $sql->fetch($q_result)) {
    $mks = $row['partial_num'] > 1 ? '_'.$row['mks'] : '_' ;
	$fields[] = $row['order_num'].$mks.'_'.$row['p_id'].'_'.$row['style'].'_'.$row['p_etd'].'_'.$row['p_qty'].'_'.$row['ext_qty'].'_'.$row['order_num'];
}

if(!empty($fields))
return $fields;
return false;

} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# 取得生產線資訊
function get_line_info($fty) {

$sql = $this->sql;

$q_str = "SELECT `id` , `line` 
FROM `pdt_saw_line` 
WHERE 
`fty` = '".$fty."' AND 
`worker` > 0 AND 
`del_mk` = 0 
ORDER BY `line` ASC
";

if (!$q_result = $sql->query($q_str)) {
	$_SESSION['MSG'][] = "Error! Can't access database!".$q_str;
	return false;    
}

$fields = array();
while ($row = $sql->fetch($q_result)) {
	$fields[$row['id']] = $row['line'];
}

if(!empty($fields))
return $fields;
return false;

} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# 取得生產線資訊
function get_line_date($line_id) {

$sql = $this->sql;

#waitting
$q_str = "SELECT `rel_ets` , `rel_etf` FROM `schedule` WHERE `line_id` = '".$line_id."' AND `status` = 0 AND `pdt_qty` = 0 ORDER BY `rel_ets` ASC;";
if (!$q_result = $sql->query($q_str)) {
	$_SESSION['MSG'][] = "Error! Can't access database!".$q_str;
	return false;    
}
$rel_ets = '';
$fields = array();
while ($row = $sql->fetch($q_result)) {
    // $rel_ets = $row['rel_ets'] < date('Y-m-d') ? date('Y-m-d') : $row['rel_ets'] ;
    $rel_ets = $row['rel_ets'];
    $rel_etf = $row['rel_etf'];
	$fields[] = $rel_ets;
}

#pdtion 因為有些沒有等待生產訂單，所以也要搜尋生產中以及結束訂單
if(empty($fields)){
    $q_str = "SELECT `rel_etf` FROM `schedule` WHERE `line_id` = '".$line_id."' AND `status` = 0 AND `pdt_qty` > 0 ORDER BY `rel_etf` DESC LIMIT 1;";
    if (!$q_result = $sql->query($q_str)) {
        $_SESSION['MSG'][] = "Error! Can't access database!".$q_str;
        return false;    
    }
    if($etf = $sql->fetch($q_result)){
        // $fields[] = $etf[0];
    }
}

#finish
$q_str = "SELECT `ets` , `etf` FROM `schedule` WHERE `line_id` = '".$line_id."' AND `status` = 2 AND `pdt_qty` > 0 ORDER BY `ets` DESC LIMIT 1;";
if (!$q_result = $sql->query($q_str)) {
	$_SESSION['MSG'][] = "Error! Can't access database!".$q_str;
	return false;    
}


$fields[] = empty($fields)?date('Y-m-d'):$rel_etf;
return $fields;

} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# 取得工廠預估排產最後時間
function get_schedule($fty,$first_date,$last_date) {

$sql = $this->sql;

$sch_finish_rate = $_SESSION['sch_finish_rate'];
$FULL_TIME = $GLOBALS['FULL_TIME'];
$today= date('Y-m-d');
$str_date = $sch_date."-01";
$LINE = array();

$q_str = "SELECT `id` , `line` , `worker` , `style` , ( `pdt_saw_line`.`success_rate` * ".$FULL_TIME." ) as `day_avg` FROM `pdt_saw_line`
WHERE `pdt_saw_line`.`fty` ='".$fty."' AND `worker` > 0 AND `del_mk` = 0 ORDER BY `sc` ASC  , `line` ASC ";

$q_result = $sql->query($q_str);
$rtn = array();
while($row = $sql->fetch($q_result)) {
    $tmp = explode(' ',$row['line']);
    if(isset($tmp[1]))$row['line'] = $tmp[0]."+".$tmp[1];
    $rtn['line'][] = $row;
    $LINE[$row['id']] = $row['style'];
}


// : 排產ETF超過出口日 order_partial.p_etd > s_order.etd



// ◎	: 上、下身都可以生產	A
// △	: 孕婦組 Pregnant women	
// 生產加乘係數：1.1
// ((schedule.rel_ets <> '0000-00-00' AND rel_ets <='".$last_date."') OR (schedule.rel_ets = '0000-00-00' AND schedule.ets <='".$last_date."'))	
$q_str = "SELECT 
`schedule`.* , (`schedule`.`qty` - `schedule`.`pdt_qty`) as `rem_qty` , `schedule`.`qty` as `sch_qty` ,`schedule`.`pdt_qty` as `out_qty` , `schedule`.`id` as `s_id` , `schedule`.`p_id` , 

`s_order`.`style` , `s_order`.`smpl_apv` , `s_order`.`status` as `ord_status` , `s_order`.`id` as `ord_id` , `s_order`.`style_num`, `s_order`.`ie1`, `s_order`.`ie2`, `s_order`.`m_status` , 
`s_order`.`ptn_upload` , `s_order`.`dept` , `s_order`.`combine` , `s_order`.`partial_num` , `s_order`.`qty` , 

`pdtion`.`mat_shp`, `pdtion`.`m_acc_shp`, `pdtion`.`check_lots_rcv_date`, `pdtion`.`check_acc_rcv_date`, 

order_partial.p_etd , order_partial.wi_status , order_partial.mks , order_partial.p_qty as ord_qty , order_partial.p_qty as p_ord_qty , 
order_partial.ext_qty as p_sch_qty , order_partial.cut_qty as p_cut_qty , order_partial.p_qty_done as p_out_qty , 
schedule.qty as s_sch_qty , schedule.pdt_qty as s_out_qty , pdt_saw_line.sc 

FROM `schedule` , `s_order` , `pdtion` , `order_partial` , `pdt_saw_line` 

WHERE
`s_order`.`order_num` = `schedule`.`ord_num` AND 
`s_order`.`order_num` = `pdtion`.`order_num` AND 
`s_order`.`order_num` = `order_partial`.`ord_num` AND 
`schedule`.`line_id` = `pdt_saw_line`.`id` AND 
`schedule`.`p_id` = `order_partial`.`id` AND 
`schedule`.`fty` ='".$fty."' AND 
`schedule`.`rel_etf` > '".$first_date."' AND 
( schedule.rel_ets <='".$last_date."' OR `schedule`.`ets` <= '".$last_date."' ) 
ORDER BY `schedule`.`rel_ets` ASC";

// echo $q_str.'<br>';

$q_result = $sql->query($q_str);
$schedule = array();
while($row = $sql->fetch($q_result)) {
    $row['combine_str'] = $GLOBALS['order']->get_combine($row['combine']);
    $color = '#FFFFFF' ;
    $line_id = $row['line_id'];
    // : 訂單與生產線不同style $line_id = schedule.line_id $LINE[$line_id] != group_order_style(s_order.style)
    $style = ( $LINE[$line_id] == 'all' ) ? 0 : ( $LINE[$line_id] != group_order_style($row['style']) ? 1 : 0 ) ;
    $color = $style ? '#F7FF1B' : $color ;
    // : 副料未到齊 pdtion.check_acc_rcv_date = 1 有到齊
    $acc = ( $row['check_acc_rcv_date'] != 1 ) ? 1 : 0 ;
    $color = $acc ? '#FFC5F3' : $color ;
    // : 主料未到齊 pdtion.check_lots_rcv_date = 1 有到齊
    $lots = ( $row['check_lots_rcv_date'] != 1 ) ? 1 : 0 ;
    $color = $lots ? '#FD66DF' : $color ;    
    // : 無BOM  order_partial.wi_status = 1
    $bom = ( $row['wi_status'] != 1 ) ? 1 : 0 ;
    $color = $bom ? '#A36ED4' : $color ;    
    // : 樣本未確認 (粗斜體)  s_order.smpl_apv = 0000-00-00 沒有
    // $smpl_apv = ( $row['smpl_apv'] != '0000-00-00' ) ? 1 : 0 ;
    // $color = $smpl_apv ? '#' : $color ;
    // : 超過出口日 today > order_partial.p_etd
    $etd_over = ( $row['rel_etf'] > $row['p_etd'] ) ? 1 : 0 ;
    $color = $etd_over ? '#FD1838' : $color ;
    // : 生產中 schedule.pdt_qty > 0
    $pdting = ( $row['out_qty'] > 0 ) ? 1 : 0 ;
    $color = $pdting ? 'bg_line_pdtion' : $color ;
    // : 生產中,超過出口日 schedule.pdt_qty > 0 && today > order_partial.p_etd
    $pdting_over = ( $row['out_qty'] > 0 && date('Y-m-d') > $row['p_etd'] ) ? 1 : 0 ;
    $color = $pdting_over ? 'bg_line_pdtion_over' : $color ;
    // : ORDER FINISHED s_order.status = 10
    $finish = ( $row['ord_status'] == 10 ) ? 1 : 0 ;
    // $color = $finish ? '#D7D7FF' : $color ;
    $row['color'] = $color;
    $row['color_status'] = array( 
        'style' => $style , 
        'bom' => $bom , 
        'lots' => $lots , 
        'acc' => $acc , 
        'etd_over' => $etd_over ,
        'pdting' => $pdting ,
        'pdting_over' => $pdting_over ,
        'finish' => $finish 
    );
    
    $schedule[] = $row;
}

// print_r($rtn);
$schedule_arr = array();
#Finish
for($i=0; $i<sizeof($schedule); $i++) {
    $schedule[$i]['etf'] = $schedule[$i]['ets'] == $schedule[$i]['etf'] ? increceDaysInDate($schedule[$i]['ets'],1) :$schedule[$i]['etf'] ;
    if( $schedule[$i]['status'] == '2' && $schedule[$i]['etf'] > $first_date && $schedule[$i]['ets'] < $last_date){
        # 組訂單號
        $ord_num = ($schedule[$i]['partial_num']==1)?substr($schedule[$i]['ord_num'],6):substr($schedule[$i]['ord_num'],6).'('.$schedule[$i]['mks'].')';
        $schedule_arr[$schedule[$i]['line_id']]['finish']['ord_str'] = 
        empty($schedule_arr[$schedule[$i]['line_id']]['finish']['ord_str'])? 
        $ord_num : $schedule_arr[$schedule[$i]['line_id']]['finish']['ord_str'].','.$ord_num;
        $schedule_arr[$schedule[$i]['line_id']]['finish']['min'] = empty($schedule_arr[$schedule[$i]['line_id']]['finish']['min'])?$schedule[$i]['ets']:$schedule_arr[$schedule[$i]['line_id']]['finish']['min'];
        $schedule_arr[$schedule[$i]['line_id']]['finish']['max'] = empty($schedule_arr[$schedule[$i]['line_id']]['finish']['max'])?$schedule[$i]['etf']:$schedule_arr[$schedule[$i]['line_id']]['finish']['max'];
        $schedule_arr[$schedule[$i]['line_id']]['finish']['min'] = ( $schedule_arr[$schedule[$i]['line_id']]['finish']['min'] > $schedule[$i]['ets'] ) ? $schedule[$i]['ets'] : $schedule_arr[$schedule[$i]['line_id']]['finish']['min'] ;
        $schedule_arr[$schedule[$i]['line_id']]['finish']['max'] = ( $schedule_arr[$schedule[$i]['line_id']]['finish']['max'] < $schedule[$i]['etf'] ) ? $schedule[$i]['etf'] : $schedule_arr[$schedule[$i]['line_id']]['finish']['max'] ;
        $schedule_arr[$schedule[$i]['line_id']]['finish']['ets'] = $schedule[$i]['ets'];
        $schedule_arr[$schedule[$i]['line_id']]['finish']['etf'] = $schedule[$i]['etf'];

        $schedule_arr[$schedule[$i]['line_id']]['finish']['day'] = $day = array();
        if ( $schedule_arr[$schedule[$i]['line_id']]['finish']['min'] <= $first_date && $schedule_arr[$schedule[$i]['line_id']]['finish']['max'] >= $last_date ) {
            $schedule_arr[$schedule[$i]['line_id']]['finish']['day'][] = array( 'ord_str' => $schedule_arr[$schedule[$i]['line_id']]['finish']['ord_str'] , 'count' => countDays($first_date,$last_date) );
        } else if ( $schedule_arr[$schedule[$i]['line_id']]['finish']['min'] <= $first_date && $schedule_arr[$schedule[$i]['line_id']]['finish']['max'] < $last_date ) {
            $schedule_arr[$schedule[$i]['line_id']]['finish']['day'][] = array( 'ord_str' => $schedule_arr[$schedule[$i]['line_id']]['finish']['ord_str'] , 'count' => countDays($first_date,$schedule_arr[$schedule[$i]['line_id']]['finish']['max']) );
        } else if( $schedule_arr[$schedule[$i]['line_id']]['finish']['min'] > $first_date && $schedule_arr[$schedule[$i]['line_id']]['finish']['max'] >= $last_date ) {
            $schedule_arr[$schedule[$i]['line_id']]['finish']['day'][] = array( 'ord_str' => '' , 'count' => countDays($first_date,$schedule_arr[$schedule[$i]['line_id']]['finish']['min']) );
            $schedule_arr[$schedule[$i]['line_id']]['finish']['day'][] = array( 'ord_str' => $schedule_arr[$schedule[$i]['line_id']]['finish']['ord_str'] , 'count' => countDays($schedule_arr[$schedule[$i]['line_id']]['finish']['min'],$last_date) );
        } else if( $schedule_arr[$schedule[$i]['line_id']]['finish']['min'] > $first_date && $schedule_arr[$schedule[$i]['line_id']]['finish']['max'] < $last_date ) {
            $schedule_arr[$schedule[$i]['line_id']]['finish']['day'][] = array( 'ord_str' => '' , 'count' => countDays($first_date,$schedule_arr[$schedule[$i]['line_id']]['finish']['min']) );
            $schedule_arr[$schedule[$i]['line_id']]['finish']['day'][] = array( 'ord_str' => $schedule_arr[$schedule[$i]['line_id']]['finish']['ord_str'] , 'count' => countDays($schedule_arr[$schedule[$i]['line_id']]['finish']['min'],$schedule_arr[$schedule[$i]['line_id']]['finish']['max']) );
        // } else if( $schedule[$i]['ets'] < $first_date && $schedule_arr[$schedule[$i]['line_id']]['finish']['max'] < $last_date ) {        
        } else {
        
        }
        
        # 顯示個別生產時間
        $mks = $schedule[$i]['partial_num'] == 1 ? '' : $schedule[$i]['mks'];
        // if( $schedule[$i]['line_id'] == 1 )
        // echo $schedule[$i]['line_id'].' ord_num:'.$schedule[$i]['ord_num'].' first:'.$first_date.' ets:'.$schedule[$i]['ets'].' etf:'.$schedule[$i]['etf'].'<br>';
        $day = array();
        if( $schedule[$i]['ets'] <= $first_date ){
            $r_ets = $first_date;
            $day[] = array(
                's_id'      => $schedule[$i]['s_id'] , 
                'p_id'      => $schedule[$i]['p_id'] , 
                'count'     => countDays($r_ets,$schedule[$i]['etf']) ,
                'ord_num'   => $schedule[$i]['ord_num'] , 
                'mks'       => $mks?'('.$mks.')':'' , 
                'pattern'   => $schedule[$i]['ptn_upload'] ,
                'style'     => $schedule[$i]['style'] ,
                'ie1'       => $schedule[$i]['ie1'] ,
                'ie2'       => $schedule[$i]['ie2'] ,
                'ord_qty'   => $schedule[$i]['ord_qty'] ,
                'sch_qty'   => $schedule[$i]['sch_qty'] ,
                'out_qty'   => $schedule[$i]['out_qty'] ,
                'ets'       => $schedule[$i]['ets'] ,
                'etf'       => $schedule[$i]['etf'] ,
                'rel_ets'   => $schedule[$i]['rel_ets'] ,
                'rel_etf'   => $schedule[$i]['rel_etf'] ,
                'pre_ets'   => $schedule[$i]['pre_ets'] ,
                'pre_etf'   => $schedule[$i]['pre_etf'] ,
                'etd'       => $schedule[$i]['p_etd'] ,
                'combine'   => $schedule[$i]['combine'] , 
                'combine_str'   => $schedule[$i]['combine_str'] ,
                'color'     => $schedule[$i]['color'] ,
                'color_status'  => $schedule[$i]['color_status'] , 
                'p_ord_qty' => $schedule[$i]['p_ord_qty'] , 
                'p_sch_qty' => $schedule[$i]['p_sch_qty'] , 
                'p_cut_qty' => $schedule[$i]['p_cut_qty'] , 
                'p_out_qty' => $schedule[$i]['p_out_qty'] ,                 
                'pre_ets'   => $schedule[$i]['pre_ets'] , 
                'pre_etf'   => $schedule[$i]['pre_etf'] , 
                's_sch_qty' => $schedule[$i]['s_sch_qty'] , 
                's_out_qty' => $schedule[$i]['s_out_qty'] , 
                'status'    => $schedule[$i]['status'] , 
                'des'       => $schedule[$i]['des'] , 
                'sc'        => $schedule[$i]['sc']
            );
            $day[] = array(
                'count'     => countDays($schedule[$i]['etf'],$last_date) ,
                'ord_num'   => '' , 
                'mks'       => ''
            );
        } else if ( $schedule[$i]['etf'] >= $last_date ) {
            $r_ets = $first_date;
            $day[] = array(
                'count'     => countDays($first_date,$schedule[$i]['ets']) ,
                'ord_num'   => '' , 
                'mks'       => ''
            );
            $day[] = array(
                's_id'      => $schedule[$i]['s_id'] , 
                'p_id'      => $schedule[$i]['p_id'] , 
                'count'     => countDays($schedule[$i]['ets'],$last_date) ,
                'ord_num'   => $schedule[$i]['ord_num'] , 
                'mks'       => $mks?'('.$mks.')':'' , 
                'pattern'   => $schedule[$i]['ptn_upload'] ,
                'style'     => $schedule[$i]['style'] ,
                'ie1'       => $schedule[$i]['ie1'] ,
                'ie2'       => $schedule[$i]['ie2'] ,
                'ord_qty'   => $schedule[$i]['ord_qty'] ,
                'sch_qty'   => $schedule[$i]['sch_qty'] ,
                'out_qty'   => $schedule[$i]['out_qty'] ,
                'ets'       => $schedule[$i]['ets'] ,
                'etf'       => $schedule[$i]['etf'] ,
                'rel_ets'   => $schedule[$i]['rel_ets'] ,
                'rel_etf'   => $schedule[$i]['rel_etf'] ,
                'pre_ets'   => $schedule[$i]['pre_ets'] ,
                'pre_etf'   => $schedule[$i]['pre_etf'] ,
                'etd'       => $schedule[$i]['p_etd'] ,
                'combine'   => $schedule[$i]['combine'] , 
                'combine_str'   => $schedule[$i]['combine_str'] ,
                'color'     => $schedule[$i]['color'] ,
                'color_status'  => $schedule[$i]['color_status'] , 
                'p_ord_qty' => $schedule[$i]['p_ord_qty'] , 
                'p_sch_qty' => $schedule[$i]['p_sch_qty'] , 
                'p_cut_qty' => $schedule[$i]['p_cut_qty'] , 
                'p_out_qty' => $schedule[$i]['p_out_qty'] ,                 
                'pre_ets'   => $schedule[$i]['pre_ets'] , 
                'pre_etf'   => $schedule[$i]['pre_etf'] , 
                's_sch_qty' => $schedule[$i]['s_sch_qty'] , 
                's_out_qty' => $schedule[$i]['s_out_qty'] , 
                'status'    => $schedule[$i]['status'] , 
                'des'       => $schedule[$i]['des'] , 
                'sc'        => $schedule[$i]['sc']
            );
        } else {
            $r_ets = $schedule[$i]['ets'];
            $day[] = array(
                'count'     => countDays($first_date,$schedule[$i]['ets']) ,
                'ord_num'   => '' , 
                'mks'       => ''
            );
            
            $day[] = array(
                's_id'      => $schedule[$i]['s_id'] , 
                'p_id'      => $schedule[$i]['p_id'] , 
                'count'     => countDays($schedule[$i]['ets'],$schedule[$i]['etf']) ,
                'ord_num'   => $schedule[$i]['ord_num'] , 
                'mks'       => $mks?'('.$mks.')':'' , 
                'pattern'   => $schedule[$i]['ptn_upload'] ,
                'style'     => $schedule[$i]['style'] ,
                'ie1'       => $schedule[$i]['ie1'] ,
                'ie2'       => $schedule[$i]['ie2'] ,
                'ord_qty'   => $schedule[$i]['ord_qty'] ,
                'sch_qty'   => $schedule[$i]['sch_qty'] ,
                'out_qty'   => $schedule[$i]['out_qty'] ,
                'ets'       => $schedule[$i]['ets'] ,
                'etf'       => $schedule[$i]['etf'] ,
                'rel_ets'   => $schedule[$i]['rel_ets'] ,
                'rel_etf'   => $schedule[$i]['rel_etf'] ,
                'pre_ets'   => $schedule[$i]['pre_ets'] ,
                'pre_etf'   => $schedule[$i]['pre_etf'] ,
                'etd'       => $schedule[$i]['p_etd'] ,
                'combine'   => $schedule[$i]['combine'] , 
                'combine_str'   => $schedule[$i]['combine_str'] ,
                'color'     => $schedule[$i]['color'] ,
                'color_status'  => $schedule[$i]['color_status'] , 
                'p_ord_qty' => $schedule[$i]['p_ord_qty'] , 
                'p_sch_qty' => $schedule[$i]['p_sch_qty'] , 
                'p_cut_qty' => $schedule[$i]['p_cut_qty'] , 
                'p_out_qty' => $schedule[$i]['p_out_qty'] ,                 
                'pre_ets'   => $schedule[$i]['pre_ets'] , 
                'pre_etf'   => $schedule[$i]['pre_etf'] , 
                's_sch_qty' => $schedule[$i]['s_sch_qty'] , 
                's_out_qty' => $schedule[$i]['s_out_qty'] , 
                'status'    => $schedule[$i]['status'] , 
                'des'       => $schedule[$i]['des'] , 
                'sc'        => $schedule[$i]['sc']
            );
            $day[] = array(
                'count'     => countDays($schedule[$i]['etf'],$last_date) ,
                'ord_num'   => '' , 
                'mks'       => ''
            );
        }

        // : 生產中 s_order.status = 8
        // : 生產中,超過出口日 s_order.status = 8 && order_partial.p_etd > s_order.etd
        
        $schedule_arr[$schedule[$i]['line_id']]['finish']['line'][] = array(
            'ord_num'   => $schedule[$i]['ord_num'] , 
            'mks'       => '('.$mks.')' , 
            'r_ets'     => $r_ets , 
            'ets'       => $schedule[$i]['ets'] , 
            'etf'       => $schedule[$i]['etf'] ,
            'day'       => $day
        );
    }
}

#Pdtion
for($i=0; $i<sizeof($schedule); $i++) {
    if( $schedule[$i]['status'] == '0' AND $schedule[$i]['pdt_qty'] > '0' && $schedule[$i]['rel_etf'] > $first_date ){

        $p_first_date = (!empty($schedule_arr[$schedule[$i]['line_id']]['finish']['max'])?$schedule_arr[$schedule[$i]['line_id']]['finish']['max']:$first_date);
        $p_first_date = $p_first_date < $first_date ? $first_date : $p_first_date;
        
        # 組訂單號
        $ord_num = ($schedule[$i]['partial_num']==1)?substr($schedule[$i]['ord_num'],6):substr($schedule[$i]['ord_num'],6).'('.$schedule[$i]['mks'].')';
        $schedule_arr[$schedule[$i]['line_id']]['pdtion']['ord_str'] = 
        empty($schedule_arr[$schedule[$i]['line_id']]['pdtion']['ord_str'])? 
        $ord_num : $schedule_arr[$schedule[$i]['line_id']]['pdtion']['ord_str'].','.$ord_num;
        $schedule_arr[$schedule[$i]['line_id']]['pdtion']['ord_str'] .= '';
        
        $schedule_arr[$schedule[$i]['line_id']]['pdtion']['min'] = empty($schedule_arr[$schedule[$i]['line_id']]['pdtion']['min'])?$schedule[$i]['rel_ets']:$schedule_arr[$schedule[$i]['line_id']]['pdtion']['min'];
        $schedule_arr[$schedule[$i]['line_id']]['pdtion']['max'] = empty($schedule_arr[$schedule[$i]['line_id']]['pdtion']['max'])?$schedule[$i]['rel_etf']:$schedule_arr[$schedule[$i]['line_id']]['pdtion']['max'];
        $schedule_arr[$schedule[$i]['line_id']]['pdtion']['min'] = ( $schedule_arr[$schedule[$i]['line_id']]['pdtion']['min'] > $schedule[$i]['rel_ets'] ) ? $schedule[$i]['rel_ets'] : $schedule_arr[$schedule[$i]['line_id']]['pdtion']['min'] ;
        $schedule_arr[$schedule[$i]['line_id']]['pdtion']['max'] = ( $schedule_arr[$schedule[$i]['line_id']]['pdtion']['max'] < $schedule[$i]['rel_etf'] ) ? $schedule[$i]['rel_etf'] : $schedule_arr[$schedule[$i]['line_id']]['pdtion']['max'] ;

        $schedule_arr[$schedule[$i]['line_id']]['pdtion']['day'] = $day = array();
        if ( $schedule_arr[$schedule[$i]['line_id']]['pdtion']['min'] <= $p_first_date && $schedule_arr[$schedule[$i]['line_id']]['pdtion']['max'] >= $last_date ) {
            $schedule_arr[$schedule[$i]['line_id']]['pdtion']['day'][] = array( 'ord_str' => $schedule_arr[$schedule[$i]['line_id']]['pdtion']['ord_str'] , 'count' => countDays($p_first_date,$last_date) );
        } else if ( $schedule_arr[$schedule[$i]['line_id']]['pdtion']['min'] <= $p_first_date && $schedule_arr[$schedule[$i]['line_id']]['pdtion']['max'] < $last_date ) {
            $schedule_arr[$schedule[$i]['line_id']]['pdtion']['day'][] = array( 'ord_str' => $schedule_arr[$schedule[$i]['line_id']]['pdtion']['ord_str'] , 'count' => countDays($p_first_date,$schedule_arr[$schedule[$i]['line_id']]['pdtion']['max']) );
        } else if ( $schedule_arr[$schedule[$i]['line_id']]['pdtion']['min'] > $p_first_date && $schedule_arr[$schedule[$i]['line_id']]['pdtion']['max'] >= $last_date ) {
            $schedule_arr[$schedule[$i]['line_id']]['pdtion']['day'][] = array( 'ord_str' => '' , 'count' => countDays($p_first_date,$schedule_arr[$schedule[$i]['line_id']]['pdtion']['min']) );
            $schedule_arr[$schedule[$i]['line_id']]['pdtion']['day'][] = array( 'ord_str' => $schedule_arr[$schedule[$i]['line_id']]['pdtion']['ord_str'] , 'count' => countDays($schedule_arr[$schedule[$i]['line_id']]['pdtion']['min'],$last_date) );
        } else if ( $schedule_arr[$schedule[$i]['line_id']]['pdtion']['min'] > $p_first_date && $schedule_arr[$schedule[$i]['line_id']]['pdtion']['max'] < $last_date ) {
            $schedule_arr[$schedule[$i]['line_id']]['pdtion']['day'][] = array( 'ord_str' => '' , 'count' => countDays($p_first_date,$schedule_arr[$schedule[$i]['line_id']]['pdtion']['min']) );
            $schedule_arr[$schedule[$i]['line_id']]['pdtion']['day'][] = array( 'ord_str' => $schedule_arr[$schedule[$i]['line_id']]['pdtion']['ord_str'] , 'count' => countDays($schedule_arr[$schedule[$i]['line_id']]['pdtion']['min'],$schedule_arr[$schedule[$i]['line_id']]['pdtion']['max']) );
        } else {
        }
        
        # 顯示個別生產時間
        $mks = $schedule[$i]['partial_num'] == 1 ? '' : $schedule[$i]['mks'];
        $day = array();
        if( $schedule[$i]['rel_ets'] <= $first_date ){
            $r_rel_ets = $first_date;
            $day[] = array( 
                's_id'      => $schedule[$i]['s_id'] , 
                'p_id'      => $schedule[$i]['p_id'] , 
                'count'     => countDays($r_rel_ets,$schedule[$i]['rel_etf']) ,
                'ord_num'   => $schedule[$i]['ord_num'] , 
                'mks'       => $mks?'('.$mks.')':'' , 
                'pattern'   => $schedule[$i]['ptn_upload'] ,
                'style'     => $schedule[$i]['style'] ,
                'ie1'       => $schedule[$i]['ie1'] ,
                'ie2'       => $schedule[$i]['ie2'] ,
                'ord_qty'   => $schedule[$i]['ord_qty'] ,
                'sch_qty'   => $schedule[$i]['sch_qty'] ,
                'out_qty'   => $schedule[$i]['out_qty'] ,
                'ets'       => $schedule[$i]['ets'] ,
                'etf'       => $schedule[$i]['etf'] ,
                'rel_ets'   => $schedule[$i]['rel_ets'] ,
                'rel_etf'   => $schedule[$i]['rel_etf'] ,
                'pre_ets'   => $schedule[$i]['pre_ets'] ,
                'pre_etf'   => $schedule[$i]['pre_etf'] ,
                'etd'       => $schedule[$i]['p_etd'] ,
                'combine'   => $schedule[$i]['combine'] , 
                'combine_str'   => $schedule[$i]['combine_str'] ,
                'color'     => $schedule[$i]['color'] ,
                'color_status'  => $schedule[$i]['color_status'] , 
                'p_ord_qty' => $schedule[$i]['p_ord_qty'] , 
                'p_sch_qty' => $schedule[$i]['p_sch_qty'] , 
                'p_cut_qty' => $schedule[$i]['p_cut_qty'] , 
                'p_out_qty' => $schedule[$i]['p_out_qty'] ,                 
                's_sch_qty' => $schedule[$i]['s_sch_qty'] , 
                's_out_qty' => $schedule[$i]['s_out_qty'] , 
                'status'    => $schedule[$i]['status'] , 
                'des'       => $schedule[$i]['des'] , 
                'sc'        => $schedule[$i]['sc']
            );
            $day[] = array( 
                'count'     => countDays($schedule[$i]['rel_etf'],$last_date) ,
                'ord_num'   => '' , 
                'mks'       => ''
            );
        } else if ( $schedule[$i]['rel_etf'] >= $last_date ) {
            $r_rel_ets = $first_date;
            $day[] = array( 
                'count'     => countDays($first_date,$schedule[$i]['rel_ets']) ,
                'ord_num'   => '' , 
                'mks'       => ''
            );
            $day[] = array( 
                's_id'      => $schedule[$i]['s_id'] , 
                'p_id'      => $schedule[$i]['p_id'] , 
                'count'     => countDays($schedule[$i]['rel_ets'],$last_date) ,
                'ord_num'   => $schedule[$i]['ord_num'] , 
                'mks'       => $mks?'('.$mks.')':'' , 
                'pattern'   => $schedule[$i]['ptn_upload'] ,
                'style'     => $schedule[$i]['style'] ,
                'ie1'       => $schedule[$i]['ie1'] ,
                'ie2'       => $schedule[$i]['ie2'] ,
                'ord_qty'   => $schedule[$i]['ord_qty'] ,
                'sch_qty'   => $schedule[$i]['sch_qty'] ,
                'out_qty'   => $schedule[$i]['out_qty'] ,
                'ets'       => $schedule[$i]['ets'] ,
                'etf'       => $schedule[$i]['etf'] ,
                'rel_ets'   => $schedule[$i]['rel_ets'] ,
                'rel_etf'   => $schedule[$i]['rel_etf'] ,
                'pre_ets'   => $schedule[$i]['pre_ets'] ,
                'pre_etf'   => $schedule[$i]['pre_etf'] ,
                'etd'       => $schedule[$i]['p_etd'] ,
                'combine'   => $schedule[$i]['combine'] , 
                'combine_str'   => $schedule[$i]['combine_str'] ,
                'color'     => $schedule[$i]['color'] ,
                'color_status'  => $schedule[$i]['color_status'] , 
                'p_ord_qty' => $schedule[$i]['p_ord_qty'] , 
                'p_sch_qty' => $schedule[$i]['p_sch_qty'] , 
                'p_cut_qty' => $schedule[$i]['p_cut_qty'] , 
                'p_out_qty' => $schedule[$i]['p_out_qty'] ,                 
                's_sch_qty' => $schedule[$i]['s_sch_qty'] , 
                's_out_qty' => $schedule[$i]['s_out_qty'] , 
                'status'    => $schedule[$i]['status'] , 
                'des'       => $schedule[$i]['des'] , 
                'sc'        => $schedule[$i]['sc']
            );
        } else {
            $r_rel_ets = $schedule[$i]['rel_ets'];
            $day[] = array( 
                'count'     => countDays($first_date,$schedule[$i]['rel_ets']) ,
                'ord_num'   => '' , 
                'mks'       => ''
            );
            $day[] = array( 
                's_id'      => $schedule[$i]['s_id'] , 
                'p_id'      => $schedule[$i]['p_id'] , 
                'count'     => countDays($schedule[$i]['rel_ets'],$schedule[$i]['rel_etf']) ,
                'ord_num'   => $schedule[$i]['ord_num'] , 
                'mks'       => $mks?'('.$mks.')':'' , 
                'pattern'   => $schedule[$i]['ptn_upload'] ,
                'style'     => $schedule[$i]['style'] ,
                'ie1'       => $schedule[$i]['ie1'] ,
                'ie2'       => $schedule[$i]['ie2'] ,
                'ord_qty'   => $schedule[$i]['ord_qty'] ,
                'sch_qty'   => $schedule[$i]['sch_qty'] ,
                'out_qty'   => $schedule[$i]['out_qty'] ,
                'ets'       => $schedule[$i]['ets'] ,
                'etf'       => $schedule[$i]['etf'] ,
                'rel_ets'   => $schedule[$i]['rel_ets'] ,
                'rel_etf'   => $schedule[$i]['rel_etf'] ,
                'pre_ets'   => $schedule[$i]['pre_ets'] ,
                'pre_etf'   => $schedule[$i]['pre_etf'] ,
                'etd'       => $schedule[$i]['p_etd'] ,
                'combine'   => $schedule[$i]['combine'] , 
                'combine_str'   => $schedule[$i]['combine_str'] ,
                'color'     => $schedule[$i]['color'] ,
                'color_status'  => $schedule[$i]['color_status'] , 
                'p_ord_qty' => $schedule[$i]['p_ord_qty'] , 
                'p_sch_qty' => $schedule[$i]['p_sch_qty'] , 
                'p_cut_qty' => $schedule[$i]['p_cut_qty'] , 
                'p_out_qty' => $schedule[$i]['p_out_qty'] ,                 
                's_sch_qty' => $schedule[$i]['s_sch_qty'] , 
                's_out_qty' => $schedule[$i]['s_out_qty'] , 
                'status'    => $schedule[$i]['status'] , 
                'des'       => $schedule[$i]['des'] , 
                'sc'        => $schedule[$i]['sc']
            );
            $day[] = array( 
                'count'     => countDays($schedule[$i]['rel_etf'],$last_date) ,
                'ord_num'   => '' , 
                'mks'       => ''
            );
        }
        
        $schedule_arr[$schedule[$i]['line_id']]['pdtion']['line'][] = array( 
            'ord_num'   => $schedule[$i]['ord_num'] , 
            'mks'       => '('.$mks.')' , 
            'r_rel_ets' => $r_rel_ets , 
            'rel_ets'   => $schedule[$i]['rel_ets'] , 
            'rel_etf'   => $schedule[$i]['rel_etf'] ,
            'day'       => $day 
        );
    }
}

#waitting
for($i=0; $i<sizeof($schedule); $i++) {
    if( $schedule[$i]['status'] == '0' AND $schedule[$i]['pdt_qty'] == '0' AND $schedule[$i]['rel_ets'] < $last_date ){
    
        $p_first_date = !empty($schedule_arr[$schedule[$i]['line_id']]['waitting']['min']) ? $schedule_arr[$schedule[$i]['line_id']]['waitting']['min']:
        ( !empty($schedule_arr[$schedule[$i]['line_id']]['pdtion']['max']) ? $schedule_arr[$schedule[$i]['line_id']]['pdtion']['max']:
        ( !empty($schedule_arr[$schedule[$i]['line_id']]['finish']['max']) ? $schedule_arr[$schedule[$i]['line_id']]['finish']['max']:$first_date)
        );

        // $schedule_arr[$schedule[$i]['line_id']]['waitting']['day'] = $day = array();
        $mks = $schedule[$i]['partial_num'] == 1 ? '' : '('.$schedule[$i]['mks'].')';
        
        if( $schedule[$i]['rel_ets'] <= $first_date ){
            $schedule_arr[$schedule[$i]['line_id']]['waitting']['day'][] = array( 
                's_id'      => $schedule[$i]['s_id'] , 
                'p_id'      => $schedule[$i]['p_id'] , 
                'ord_nums'  => substr($schedule[$i]['ord_num'],6) , 
                'count'     => countDays($first_date,$schedule[$i]['rel_etf']) , 
                'mks'       => $mks ,
                'color'     => $schedule[$i]['color'] ,
                'color_status'  => $schedule[$i]['color_status'] ,
                
                'ord_num'   => $schedule[$i]['ord_num'] , 

                'pattern'   => $schedule[$i]['ptn_upload'] ,
                'style'     => $schedule[$i]['style'] ,
                'ie1'       => $schedule[$i]['ie1'] ,
                'ie2'       => $schedule[$i]['ie2'] ,
                'ord_qty'   => $schedule[$i]['ord_qty'] ,
                'sch_qty'   => $schedule[$i]['sch_qty'] ,
                'out_qty'   => $schedule[$i]['out_qty'] ,
                'ets'       => $schedule[$i]['ets'] ,
                'etf'       => $schedule[$i]['etf'] ,
                'rel_ets'   => $schedule[$i]['rel_ets'] ,
                'rel_etf'   => $schedule[$i]['rel_etf'] ,
                'pre_ets'   => $schedule[$i]['pre_ets'] ,
                'pre_etf'   => $schedule[$i]['pre_etf'] ,
                'etd'       => $schedule[$i]['p_etd'] ,
                'combine'   => $schedule[$i]['combine'] , 
                'combine_str'   => $schedule[$i]['combine_str'] , 
                's_id'      => $schedule[$i]['s_id'] , 
                'p_id'      => $schedule[$i]['p_id'] , 
                'p_ord_qty' => $schedule[$i]['p_ord_qty'] , 
                'p_sch_qty' => $schedule[$i]['p_sch_qty'] , 
                'p_cut_qty' => $schedule[$i]['p_cut_qty'] , 
                'p_out_qty' => $schedule[$i]['p_out_qty'] ,                 
                's_sch_qty' => $schedule[$i]['s_sch_qty'] , 
                's_out_qty' => $schedule[$i]['s_out_qty'] , 
                'status'    => $schedule[$i]['status'] , 
                'des'       => $schedule[$i]['des'] , 
                'sc'        => $schedule[$i]['sc']
            );
        } else if ( $schedule[$i]['rel_etf'] >= $last_date ) {
            $schedule_arr[$schedule[$i]['line_id']]['waitting']['day'][] = array( 
                's_id'      => $schedule[$i]['s_id'] , 
                'p_id'      => $schedule[$i]['p_id'] , 
                'ord_nums'  => substr($schedule[$i]['ord_num'],6) , 
                'count'     => countDays($schedule[$i]['rel_ets'],$last_date) , 
                'mks'       => $mks ,
                'color'     => $schedule[$i]['color'] ,
                'color_status'  => $schedule[$i]['color_status'] ,
                
                'ord_num'   => $schedule[$i]['ord_num'] , 

                'pattern'   => $schedule[$i]['ptn_upload'] ,
                'style'     => $schedule[$i]['style'] ,
                'ie1'       => $schedule[$i]['ie1'] ,
                'ie2'       => $schedule[$i]['ie2'] ,
                'ord_qty'   => $schedule[$i]['ord_qty'] ,
                'sch_qty'   => $schedule[$i]['sch_qty'] ,
                'out_qty'   => $schedule[$i]['out_qty'] ,
                'ets'       => $schedule[$i]['ets'] ,
                'etf'       => $schedule[$i]['etf'] ,
                'rel_ets'   => $schedule[$i]['rel_ets'] ,
                'rel_etf'   => $schedule[$i]['rel_etf'] ,
                'pre_ets'   => $schedule[$i]['pre_ets'] ,
                'pre_etf'   => $schedule[$i]['pre_etf'] ,
                'etd'       => $schedule[$i]['p_etd'] ,
                'combine'   => $schedule[$i]['combine'] , 
                'combine_str'   => $schedule[$i]['combine_str'] , 
                's_id'      => $schedule[$i]['s_id'] , 
                'p_id'      => $schedule[$i]['p_id'] , 
                'p_ord_qty' => $schedule[$i]['p_ord_qty'] , 
                'p_sch_qty' => $schedule[$i]['p_sch_qty'] , 
                'p_cut_qty' => $schedule[$i]['p_cut_qty'] , 
                'p_out_qty' => $schedule[$i]['p_out_qty'] ,                 
                's_sch_qty' => $schedule[$i]['s_sch_qty'] , 
                's_out_qty' => $schedule[$i]['s_out_qty'] , 
                'status'    => $schedule[$i]['status'] , 
                'des'       => $schedule[$i]['des'] , 
                'sc'        => $schedule[$i]['sc']
            );
        } else if ( !empty($schedule_arr[$schedule[$i]['line_id']]['pdtion']['line'][0]['ord_num']) ) {
            $schedule_arr[$schedule[$i]['line_id']]['waitting']['day'][] = array( 
                's_id'      => $schedule[$i]['s_id'] , 
                'p_id'      => $schedule[$i]['p_id'] , 
                'ord_nums'  => substr($schedule[$i]['ord_num'],6) , 
                'count'     => countDays($schedule[$i]['rel_ets'],$schedule[$i]['rel_etf']) , 
                'mks'       => $mks ,
                'color'     => $schedule[$i]['color'] ,
                'color_status'  => $schedule[$i]['color_status'] ,
                
                'ord_num'   => $schedule[$i]['ord_num'] , 

                'pattern'   => $schedule[$i]['ptn_upload'] ,
                'style'     => $schedule[$i]['style'] ,
                'ie1'       => $schedule[$i]['ie1'] ,
                'ie2'       => $schedule[$i]['ie2'] ,
                'ord_qty'   => $schedule[$i]['ord_qty'] ,
                'sch_qty'   => $schedule[$i]['sch_qty'] ,
                'out_qty'   => $schedule[$i]['out_qty'] ,
                'ets'       => $schedule[$i]['ets'] ,
                'etf'       => $schedule[$i]['etf'] ,
                'rel_ets'   => $schedule[$i]['rel_ets'] ,
                'rel_etf'   => $schedule[$i]['rel_etf'] ,
                'pre_ets'   => $schedule[$i]['pre_ets'] ,
                'pre_etf'   => $schedule[$i]['pre_etf'] ,
                'etd'       => $schedule[$i]['p_etd'] ,
                'combine'   => $schedule[$i]['combine'] , 
                'combine_str'   => $schedule[$i]['combine_str'] , 
                's_id'      => $schedule[$i]['s_id'] , 
                'p_id'      => $schedule[$i]['p_id'] , 
                'p_ord_qty' => $schedule[$i]['p_ord_qty'] , 
                'p_sch_qty' => $schedule[$i]['p_sch_qty'] , 
                'p_cut_qty' => $schedule[$i]['p_cut_qty'] , 
                'p_out_qty' => $schedule[$i]['p_out_qty'] ,                 
                's_sch_qty' => $schedule[$i]['s_sch_qty'] , 
                's_out_qty' => $schedule[$i]['s_out_qty'] , 
                'status'    => $schedule[$i]['status'] , 
                'des'       => $schedule[$i]['des'] , 
                'sc'        => $schedule[$i]['sc']
            );
        } else if ( $schedule[$i]['rel_ets'] > $first_date && empty($schedule_arr[$schedule[$i]['line_id']]['waitting']['day'][0]['ord_num']) && empty($schedule_arr[$schedule[$i]['line_id']]['waitting']['day'][1]['ord_num']) ) {
            $schedule_arr[$schedule[$i]['line_id']]['waitting']['day'][] = array( 
                'ord_nums'  => '' , 
                'count'     => countDays($p_first_date,$schedule[$i]['rel_ets'])
            );
            $schedule_arr[$schedule[$i]['line_id']]['waitting']['day'][] = array( 
                's_id'      => $schedule[$i]['s_id'] , 
                'p_id'      => $schedule[$i]['p_id'] , 
                'ord_nums'  => substr($schedule[$i]['ord_num'],6) , 
                'count'     => countDays($schedule[$i]['rel_ets'],$schedule[$i]['rel_etf']) , 
                'mks'       => $mks ,
                'color'     => $schedule[$i]['color'] ,
                'color_status'  => $schedule[$i]['color_status'] ,
                
                'ord_num'   => $schedule[$i]['ord_num'] , 

                'pattern'   => $schedule[$i]['ptn_upload'] ,
                'style'     => $schedule[$i]['style'] ,
                'ie1'       => $schedule[$i]['ie1'] ,
                'ie2'       => $schedule[$i]['ie2'] ,
                'ord_qty'   => $schedule[$i]['ord_qty'] ,
                'sch_qty'   => $schedule[$i]['sch_qty'] ,
                'out_qty'   => $schedule[$i]['out_qty'] ,
                'ets'       => $schedule[$i]['ets'] ,
                'etf'       => $schedule[$i]['etf'] ,
                'rel_ets'   => $schedule[$i]['rel_ets'] ,
                'rel_etf'   => $schedule[$i]['rel_etf'] ,
                'pre_ets'   => $schedule[$i]['pre_ets'] ,
                'pre_etf'   => $schedule[$i]['pre_etf'] ,
                'etd'       => $schedule[$i]['p_etd'] ,
                'combine'   => $schedule[$i]['combine'] , 
                'combine_str'   => $schedule[$i]['combine_str'] , 
                's_id'      => $schedule[$i]['s_id'] , 
                'p_id'      => $schedule[$i]['p_id'] , 
                'p_ord_qty' => $schedule[$i]['p_ord_qty'] , 
                'p_sch_qty' => $schedule[$i]['p_sch_qty'] , 
                'p_cut_qty' => $schedule[$i]['p_cut_qty'] , 
                'p_out_qty' => $schedule[$i]['p_out_qty'] ,                 
                's_sch_qty' => $schedule[$i]['s_sch_qty'] , 
                's_out_qty' => $schedule[$i]['s_out_qty'] , 
                'status'    => $schedule[$i]['status'] , 
                'des'       => $schedule[$i]['des'] , 
                'sc'        => $schedule[$i]['sc']
            );
        } else {
            $schedule_arr[$schedule[$i]['line_id']]['waitting']['day'][] = array( 
                's_id'      => $schedule[$i]['s_id'] , 
                'p_id'      => $schedule[$i]['p_id'] , 
                'ord_nums'  => substr($schedule[$i]['ord_num'],6) , 
                'count'     => countDays($schedule[$i]['rel_ets'],$schedule[$i]['rel_etf']) , 
                'mks'       => $mks ,
                'color'     => $schedule[$i]['color'] ,
                'color_status'  => $schedule[$i]['color_status'] ,
                
                'ord_num'   => $schedule[$i]['ord_num'] , 
                'pattern'   => $schedule[$i]['ptn_upload'] ,
                'style'     => $schedule[$i]['style'] ,
                'ie1'       => $schedule[$i]['ie1'] ,
                'ie2'       => $schedule[$i]['ie2'] ,
                'ord_qty'   => $schedule[$i]['ord_qty'] ,
                'sch_qty'   => $schedule[$i]['sch_qty'] ,
                'out_qty'   => $schedule[$i]['out_qty'] ,
                'ets'       => $schedule[$i]['ets'] ,
                'etf'       => $schedule[$i]['etf'] ,
                'rel_ets'   => $schedule[$i]['rel_ets'] ,
                'rel_etf'   => $schedule[$i]['rel_etf'] ,
                'pre_ets'   => $schedule[$i]['pre_ets'] ,
                'pre_etf'   => $schedule[$i]['pre_etf'] ,
                'etd'       => $schedule[$i]['p_etd'] ,
                'combine'   => $schedule[$i]['combine'] , 
                'combine_str'   => $schedule[$i]['combine_str'] , 
                's_id'      => $schedule[$i]['s_id'] , 
                'p_id'      => $schedule[$i]['p_id'] , 
                'p_ord_qty' => $schedule[$i]['p_ord_qty'] , 
                'p_sch_qty' => $schedule[$i]['p_sch_qty'] , 
                'p_cut_qty' => $schedule[$i]['p_cut_qty'] , 
                'p_out_qty' => $schedule[$i]['p_out_qty'] ,                 
                's_sch_qty' => $schedule[$i]['s_sch_qty'] , 
                's_out_qty' => $schedule[$i]['s_out_qty'] , 
                'status'    => $schedule[$i]['status'] , 
                'des'       => $schedule[$i]['des'] , 
                'sc'        => $schedule[$i]['sc']
            );
        }
    } else {
    }
}

// print_r($schedule_arr);
$sch_arr = array();
for($i=0;$i<count($rtn['line']);$i++){
    if( !empty($schedule_arr[$rtn['line'][$i]['id']]) ){
        $schedule_arr[$rtn['line'][$i]['id']]['line_arr'] = $rtn['line'][$i];
        $sch_arr[] = $schedule_arr[$rtn['line'][$i]['id']];
    } else {
        $day = countDays($first_date,$last_date);
        $finish = array( 
            'ord_num'   => '' , 
            'mks'       => '' , 
            'r_ets'     => $first_date , 
            'ets'       => $first_date , 
            'etf'       => $last_date ,
            'day'       => $day 
        );
        $sch_arr[] = array( 'line_arr' => $rtn['line'][$i] , 'finish' => $finish );
    }
}

return $sch_arr;
} // end func


############################## 
# 未生產訂單資料 Line ID
function get_waitting_line_id_val($line_id) {

$sql = $this->sql;

$q_str = "SELECT `schedule`.* , `pdt_saw_line`.`worker` , `pdt_saw_line`.`sc` 
FROM `schedule` , `pdt_saw_line` 
WHERE 
`schedule`.`line_id` = '".$line_id."' AND 
`schedule`.`status`= '0' AND 
`schedule`.`pdt_qty` = '0' AND 
`schedule`.`line_id` = `pdt_saw_line`.`id` 
ORDER BY `schedule`.`rel_ets` ASC ;";
$q_result = $sql->query($q_str);
$sch_arr = array();
while($row = $sql->fetch($q_result)) {
    $sch_arr[] = $row;
}
return $sch_arr;
} // end func



############################## 
# 取得工廠排產最後時間
function get_pdtion_rel_etf_last_date($line_id) {

$sql = $this->sql;

$q_str = "SELECT `rel_etf` FROM `schedule` WHERE `line_id` = '".$line_id."' AND `status` = '0' AND `pdt_qty` > '0' ORDER BY `rel_etf` DESC LIMIT 1";
$q_result = $sql->query($q_str);
if($rel_etf = $sql->fetch($q_result)){
    return $rel_etf[0];
}
return date('Y-m-d');

} // end func



############################## 
# 重新訂日期 By Line
function mdf_schedule_move_date($line_id,$Ets,$Etf) {

$sql = $this->sql;

$q_str = "UPDATE `schedule` SET `rel_ets` = '".$Ets."' , `rel_etf` = '".$Etf."' WHERE `id` = '".$line_id."' ;";
mysql_query($q_str);

} // end func



############################## 
# 重新訂日期 By Line
function mdf_partial($p_id,$statu=2) {

    $sql = $this->sql;
    
    $str = ( $statu == '2' ) ? ' min(`ets`) as `ets` , max(`etf`) as `etf` ' : ' min(`rel_ets`) as `ets` , max(`rel_etf`) as `etf` ';

    $q_str = "SELECT ".$str." , sum(`su`) as `su` , sum(`qty`) as `qty` , sum(`pdt_qty`) as `pdt_qty` , `ord_num`	
    FROM `schedule`
    WHERE `p_id` = '".$p_id."' AND ( `rel_ets` != '0000-00-00' OR `rel_etf` != '0000-00-00' OR `rel_ets` != NULL OR `rel_etf` != NULL )
    GROUP BY `ord_num` ;
    ";
    
    if( $q_results = $sql->query($q_str) ) {
        $rows = $sql->fetch($q_results);
        $fty_su = $GLOBALS['order']->distri_month_su($rows['su'],$rows['ets'],$rows['etf']);
        $ext_period = countDays($rows['ets'],$rows['etf']);
    } else {
        $rows['ets'] = $rows['etf'] = $fty_su = $ext_period = NULL;
    }

    $q_str = "UPDATE `order_partial` SET 
    `p_fty_su` = '".$fty_su."' , 	
    `ext_qty` = '".$rows['qty']."' , 
    `ext_su` = '".$rows['su']."' , 
    `p_ets` = '".$rows['ets']."' , 
    `p_etf` = '".$rows['etf']."' , 
    `p_qty_done` = '".$rows['pdt_qty']."' , 
    `ext_period` = '".$ext_period."' , 
    `pdt_status` = '".$statu."' 
    WHERE `id` = '".$p_id."' 
    ;";
    
    // echo $q_str.'<br>';
    if (!$q_results = $sql->query($q_str)) {
        $this->msg->add("Error ! 無法更新資料庫內容.");
        $this->msg->merge($sql->msg);
        return false;    
    }

    return $pdtion_arr;
} # end func



############################## 
# 重新訂日期 By Line
function mdf_pdtion($order_num) {

    $sql = $this->sql;

    $q_str = "SELECT 
    min(`ets`) as `ext_ets` , max(`etf`) as `ext_etf` , min(`rel_ets`) as `rel_ets` , max(`rel_etf`) as `rel_etf` ,  sum(`qty`) as `ext_qty`  , sum(`su`) as `ext_su` , min(`pre_ets`) as `pre_ets` , max(`pre_etf`) as `pre_etf`
    FROM `schedule`
    WHERE `ord_num` = '".$order_num."' 
    GROUP BY `ord_num` 
    ;";

    $q_results = $sql->query($q_str);
    if( $row = $sql->fetch($q_results) ) {
        $pre_ets = $row['pre_ets'];
        $pre_etf = $row['pre_etf'];
        $rel_ets = $row['rel_ets'];
        $rel_etf = $row['rel_etf'];
        $ext_ets = $row['ext_ets'] != '0000-00-00'?$row['ext_ets']:$rel_ets;
        $ext_etf = $row['ext_etf'] != '0000-00-00'?$row['ext_etf']:$rel_etf;
        $ext_qty = $row['ext_qty'];
        $ext_su = $row['ext_su'];
        $ext_period = countDays($ext_ets,$ext_etf);
    } else {
        $pre_ets = $pre_etf = $ext_ets = $ext_etf = $ext_qty = $ext_su = $ext_period = NULL;
    }

    $q_str = "SELECT 
    min(`p_etp`) as `ets` , max(`p_etd`) as `etf` , min(`p_ets`) as `rel_ets` , max(`p_etf`) as `rel_etf` , sum(`ext_su`) as `ext_su` 
    FROM `order_partial`
    WHERE `ord_num` = '".$order_num."' 
    GROUP BY `ord_num` 
    ;";

    $q_results = $sql->query($q_str);
    if( $row = $sql->fetch($q_results) ) {
        // $rel_ets = $row['rel_ets'];
        // $rel_etf = $row['rel_etf'];
        $ets = $row['ets'] != '0000-00-00'?$row['ets']:$rel_ets;
        $etf = $row['etf'] != '0000-00-00'?$row['etf']:$rel_etf;
        $fty_su = $GLOBALS['order']->distri_month_su($row['ext_su'],$rel_ets,$rel_etf);
    } else {
        $rel_ets = $rel_etf = $fty_su = NULL;
    }
    
    $q_str = "SELECT 
    sum(`work_qty`)+sum(`over_qty`) as `qty_done` 
    FROM `saw_out_put`
    WHERE `ord_num` = '".$order_num."' 
    GROUP BY `ord_num` 
    ;";

    $q_results = $sql->query($q_str);
    if( $row = $sql->fetch($q_results) ) {
        $qty_done = $row['qty_done'];
    } else {
        $qty_done = NULL;
    }

    $q_str = "SELECT 
    sum(`qty`) as `qty` 
    FROM `shipping`
    WHERE `ord_num` = '".$order_num."' 
    GROUP BY `ord_num` 
    ;";

    $q_results = $sql->query($q_str);
    if( $row = $sql->fetch($q_results) ) {
        $qty_shp = $row['qty'];
    } else {
        $qty_shp = NULL;
    }

    $q_str = "UPDATE 
    `pdtion` SET 
    `fty_su`        = '".$fty_su."' , 
    `ets`           = '".$ets."' , 
    `etf`           = '".$etf."' , 
    `pre_ets`       = '".$pre_ets."' , 
    `pre_etf`       = '".$pre_etf."' , 
    `rel_ets`       = '".$rel_ets."' , 
    `rel_etf`       = '".$rel_etf."' , 
    `qty_done`      = '".$qty_done."' , 
    `qty_shp`       = '".$qty_shp."' , 
    `ext_ets`       = '".$ext_ets."' , 
    `ext_etf`       = '".$ext_etf."' , 
    `ext_period`    = '".$ext_period."' , 
    `ext_qty`       = '".$ext_qty."' , 
    `ext_su`        = '".$ext_su."' 
    WHERE `order_num` = '".$order_num."' 
    ;";
    
    $sql->query($q_str);
    // echo $q_str.'<br>';

}



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# 重新設定生產中已完成、接近完成的訂單
function schedule_chk_finish($id) {

	$sql = $this->sql;
    
    $sch_finish_rate = $GLOBALS['sch_finish_rate'];

	$q_str = "
	SELECT pdt_saw_line.* FROM pdt_saw_line 
	WHERE `id` = '".$id."' AND 
    worker > 0 AND del_mk = 0 
    ";
    // echo $q_str.'<p>';
    $q_results = $sql->query($q_str);
    $row = $sql->fetch($q_results);
    
    $worker = $row['worker'];
    $worker = empty($worker)?30:$worker;

    # 抓出每組的平均產能
    $line_avg = $this->reset_line_avg($id,0);
    $avg = $line_avg * $worker;
    // echo '[[['.$line_avg.']]]<br>';
        
    $q_str = "SELECT 
    `s_order`.`ie1`,`s_order`.`ie2`,`s_order`.`qty`,`schedule`.`id`,`schedule`.`ord_num`,`schedule`.`p_id`,`pdtion`.`qty_done`
    FROM 
    `s_order`,`schedule`,`pdtion` 
    WHERE 
    `schedule`.`ord_num` = `s_order`.`order_num` AND 
    `pdtion`.`order_num` = `s_order`.`order_num` AND 
    `schedule`.`status` = '0' AND `schedule`.`line_id` = '".$id."'  
    GROUP BY `schedule`.`id` 
    ORDER BY  `schedule`.`rel_ets` ASC  , `schedule`.`id` ASC ;
    ";
    // echo $q_str.'<p>';
    
    $q_result = mysql_query($q_str);

    $mo = 0;
    while ($row = mysql_fetch_array($q_result)) {
        $ie = ( $row['ie2'] > 0 ) ? $row['ie2'] : $row['ie1'] ;
        $s_id = $row['id'];
        $p_id = $row['p_id'];
        $ord_num = $row['ord_num'];
        
        $qty = $row['qty'];
        $qty_done = $row['qty_done'];
        
        if( $qty_done >= $qty ){
        // if( $this->chk_saw_out_put($s_id) ){
            $this->set_schedule_status($s_id);
            $this->set_schedule_ets_etf($s_id);
            $this->mdf_partial($p_id);
            $this->mdf_pdtion($ord_num);
            $this->chk_pdc_status($ord_num);
            $GLOBALS['pdt_finish']->mdf_ie($ord_num,$ie);
        }
    }

} // end func	



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# 重新設定生產中及待產衝的訂單
function schedule_reset_pdtion_waitting($id) {

	$sql = $this->sql;
    
    $sch_finish_rate = $GLOBALS['sch_finish_rate'];

	$q_str = "
	SELECT pdt_saw_line.* FROM pdt_saw_line 
	WHERE `id` = '".$id."' AND 
    worker > 0 AND del_mk = 0 
    ";
    // echo $q_str.'<p>';
    $q_results = $sql->query($q_str);
    $row = $sql->fetch($q_results);
    
    $worker = $row['worker'];
    $worker = empty($worker)?30:$worker;

    # 抓出每組的平均產能
    $line_avg = $this->reset_line_avg($id,0);
    $avg = $line_avg * $worker;
    // echo '[[['.$line_avg.']]]<br>';
        
    $q_str = "SELECT 
    `s_order`.`ie1`,`s_order`.`ie2`,`s_order`.`qty` as `ord_qty`,
    `schedule`.`id`,`schedule`.`ord_num`,`schedule`.`ets` , 
    `schedule`.`qty`,`schedule`.`pdt_qty`,`schedule`.`fty`,`schedule`.`p_id`
    FROM 
    `s_order`,`schedule`
    WHERE 
    `schedule`.`ord_num` = `s_order`.`order_num` AND 
    `schedule`.`pdt_qty` > '0' AND `schedule`.`status` != '2' AND `schedule`.`line_id` = '".$id."' 
    GROUP BY `schedule`.`id` 
    ORDER BY  `schedule`.`rel_ets` ASC  , `schedule`.`id` ASC ;
    ";
    // echo $q_str.'<p>';
    
    $q_result = mysql_query($q_str);

    $mo = 0;
    while ($row = mysql_fetch_array($q_result)) {
        $ie = ( $row['ie2'] > 0 ) ? $row['ie2'] : $row['ie1'] ;
        $su = set_su($ie,$row['qty']);
        
        # 目前產出數量
        $output = $GLOBALS['order']->get_output($row['ord_num']);
        
        # 計算剩餘 SU / 計算生產時間 / 無條件進位
        $w_day = $this->chk_work_day($output,$row['ord_qty'],$avg,$ie,$row['qty'],$row['pdt_qty']);

        // if( $row['pdt_qty'] == 0 ) {
            // if( $mo == 0 ) {
                // $Ets = date('Y-m-d');
                // $TMP_ets = $Etf = $this->increceDay($Ets,$w_day,$row['fty']);
            // } else {
                // $Ets = $TMP_ets;
                // $TMP_ets = $Etf = $this->increceDay($TMP_ets,$w_day,$row['fty']);
            // }
        // } else {
            $Ets = $row['ets'];
            $TMP_ets = $Etf = $this->increceDay((empty($Ets)?date('Y-m-d'):($Ets<date('Y-m-d')?date('Y-m-d'):$Ets)),$w_day,$row['fty']);
        // }
        
        # 重新設定 su 並計算新的製作天數
        $q_str = "UPDATE `schedule` SET `rel_ets` = '".$Ets."' , `rel_etf` = '".$Etf."' WHERE `id` = '".$row['id']."' ;";
        mysql_query($q_str);
        // echo $q_str.'<br>';

        $p_id = $row['p_id'];
        $ord_num = $row['ord_num'];
        $this->mdf_partial($p_id,0);
        $this->mdf_pdtion($ord_num);
        $GLOBALS['pdt_finish']->mdf_ie($ord_num,$ie);
        $mo++;
    }
    
    $waitting_array = $this->get_waitting_line_id_val($id);
    $ets = $this->get_pdtion_rel_etf_last_date($id);
    // $line_avg = $this->reset_line_avg($id,0);
    // echo '<br>'.$ets.'~';
    // print_r($waitting_array);
    $i = 0 ;
    foreach( $waitting_array as $key => $val ) {
        // echo $val['ord_num'].'+11'.$val['id'].'/';
        
        $worker = $val['worker'];
        $worker = empty($worker)?30:$worker;
        # 抓出每組的平均產能
        $avg = $line_avg * $worker;
        # 計算剩餘 SU / 計算生產時間 / 無條件進位
        $w_day = $this->get_work_day($val['su'],$avg);
        // echo '<br>'.$key.'~';
        if( $i == 0 ) {
            $Ets = $ets;
            $TMP_ets = $Etf = $this->increceDay($Ets,$w_day,$val['fty']);
        } else {
            $Ets = $TMP_ets;
            $TMP_ets = $Etf = $this->increceDay($TMP_ets,$w_day,$val['fty']);
        }
        # `schedule`        `rel_ets`,`rel_etf`
        $this->mdf_schedule_move_date($val['id'],$Ets,$Etf);
        # `order_partial`   `p_fty_su`,`p_ets`,`p_etf`,`ext_period`
        $this->mdf_partial($val['p_id'],0);
        # `pdtion`          `fty_su`,`ext_period`
        $this->mdf_pdtion($val['ord_num']);
        $i++;
    }
} // end func	



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# 刪除排產
function delete_schedule($id) {

$sql = $this->sql;

$q_str = "DELETE FROM `schedule` WHERE `id` = '".$id."'";		
if (!$q_result = $sql->query($q_str)) {
    $this->msg->add("Error ! 無法刪除資料記錄.");
    $this->msg->merge($sql->msg);
    return false;    
}

} // end func	



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# 修改排產
function update_schedule($id,$qty,$su,$pre_ets,$pre_etf,$des) {

$sql = $this->sql;

$q_str = "UPDATE `schedule` SET `qty` = '".$qty."' , `su` = '".$su."' , `pre_ets` = '".$pre_ets."' , `pre_etf` = '".$pre_etf."' , `des` = '".$des."' WHERE `id` = '".$id."' ;";
if (!$q_result = $sql->query($q_str)) {
    $this->msg->add("Error ! 無法刪除資料記錄.");
    $this->msg->merge($sql->msg);
    return false;    
}

} // end func	



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->schedule_reload($fty)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function schedule_reload($fty) {

	$sql = $this->sql;
    
    $sch_finish_rate = $GLOBALS['sch_finish_rate'];

	$q_str = "
	SELECT pdt_saw_line.* FROM pdt_saw_line 
	WHERE pdt_saw_line.fty ='".$fty."' AND 
    worker > 0 AND del_mk = 0 
    ";
    // echo $q_str.'<p>';
    
	$q_results = mysql_query($q_str);	
    $saw_line = array();
    $sch_arr = array();
	while($saw_line = mysql_fetch_array($q_results)) {
    
        $line_id = $saw_line['id'];
        
        $this->schedule_chk_finish($line_id);
        $this->schedule_reset_pdtion_waitting($line_id);

    }		
} // end func	
























#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, season=0)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_by_mm($fty,$sch_date,$fsh_date) {

    $sql = $this->sql;
    $sch_finish_rate = $_SESSION['sch_finish_rate'];
    $FULL_TIME = $GLOBALS['FULL_TIME'];
    
    $today= date('Y-m-d');
    $rtn = array();
    $str_date = $sch_date."-01";
    
    $q_str = "
    SELECT pdt_saw_line.*, ( pdt_saw_line.success_rate * ".$FULL_TIME." ) as day_avg FROM pdt_saw_line
    WHERE pdt_saw_line.fty ='".$fty."' AND worker > 0 AND del_mk = 0 ORDER BY line
    ";

    $q_result = $sql->query($q_str);
    // $line_arr = array();
    while($row = $sql->fetch($q_result)) {
        // $line_arr[$row['line']] = $row['id'];
        $tmp = explode(' ',$row['line']);

        if(isset($tmp[1]))$row['line'] = $tmp[0]."+".$tmp[1];
        $rtn[] = $row;
    }		
    
    
    for($i=0; $i<sizeof($rtn); $i++) {
        $q_str = "SELECT schedule.*, s_order.style, s_order.smpl_apv, s_order.status as ord_status ,
                                        order_partial.p_etd as etd, s_order.id as ord_id, pdtion.mat_shp, pdtion.m_acc_shp,
                                        s_order.style_num, order_partial.p_qty as ord_qty, s_order.ie1, s_order.ie2, s_order.m_status, schedule.qty as sch_qty ,
                                        (schedule.qty - schedule.pdt_qty) as rem_qty, ptn_upload, s_order.dept, s_order.combine, 
                                        s_order.partial_num, order_partial.mks
                            FROM schedule, s_order, pdtion, order_partial
                            WHERE  s_order.order_num = schedule.ord_num AND schedule.ord_num = pdtion.order_num AND
                                         schedule.p_id = order_partial.id AND
                                         s_order.order_num = pdtion.order_num AND s_order.order_num = order_partial.ord_num AND
                                         schedule.line_id ='".$rtn[$i]['id']."' AND schedule.rel_etf > '".$str_date."' AND
                                         ((schedule.rel_ets <> '0000-00-00' AND rel_ets <='".$fsh_date."') OR 
                                         (schedule.rel_ets = '0000-00-00' AND schedule.ets <='".$fsh_date."'))								
                            ORDER BY rel_etf";							
// echo $q_str."<BR>";							
        $q_result = $sql->query($q_str);		
        
        $j=$str_date;
        while($row = $sql->fetch($q_result)) {
            $row['combine_str'] = $GLOBALS['order']->get_combine($row['combine']);
        
//				if(isset($f_date) && $f_date == $row['rel_ets']) $row['rel_ets'] =increceDaysInDate($row['rel_ets'],1);
            $tmp = explode('-',$row['ord_num']);
            $row['ord_init'] = $tmp[1];
            if($row['rel_ets'] != '0000-00-00'){$s_date = $row['rel_ets'];}else{$s_date = $row['ets'];}
//				if($row['rel_etf'] != '0000-00-00'){$f_date = $row['rel_etf'];}else{$f_date = $row['etf'];}
            
            if($j == $str_date && $s_date > $str_date) {
                $non_row['day_range'] = dayCount($j,$s_date);
                $non_row['flag'] = 0;
                $non_row['flag_color'] = "#eeeeee";
                $rtn[$i]['sch_rec'][] = $non_row;
            }				

            if($j != $s_date && $j != $str_date) {
                $non_row['day_range'] = dayCount($j,$s_date);
                $non_row['flag'] = 0;
                $non_row['flag_color'] = "#eeeeee";
                if($non_row['day_range'] )$rtn[$i]['sch_rec'][] = $non_row;
            }
            
            if($s_date < $str_date) {
                $row['day_range'] = dayCount($str_date,$row['rel_etf']);
            }else if($row['rel_etf'] > $fsh_date){
                $row['day_range'] = dayCount($s_date,$fsh_date);
            }else{
                $row['day_range'] = dayCount($s_date,$row['rel_etf']);
            }				
        
            if($row['rel_ets'] < $row['ets'] && $row['rel_ets'] <> '0000-00-00' ){$ets = $row['rel_ets'];}else{$ets = $row['ets'];}
            
            if( $row['ord_status'] >= '10' ||  ($row['pdt_qty']/$row['qty']) > $sch_finish_rate) {
                // echo $row['ord_num'].' ~ '.$row['pdt_qty'].' / '.$row['qty'].' > '.$sch_finish_rate.'<br>';
                $row['flag'] = 11;
                $row['flag_color'] = "#D7D7FF"; //生產FINISH 淺藍
            }else if( $row['rel_etf'] > $row['etd'] && $row['ord_status'] >= '8' && $row['pdt_qty'] > 0) {
                $row['flag'] = 10;
                $row['flag_color'] = "#96DDA3"; //排產etd超過出口日 但己生產
            }else if($row['ord_status'] >= '8' && $row['pdt_qty'] > 0){					
                $row['flag'] = 4;
                $row['flag_color'] = "#D0FDCD";	//生產綠色
            }else if( $row['rel_etf'] > $row['etd']) {
                $row['flag'] = 9;
                $row['flag_color'] = "#FD1838"; //排產etd超過出口日 紅色
            }else if(!$row['mat_shp'] && $row['dept'] != 'LY'){					
                $row['flag'] = 6;
                $row['flag_color'] = "#FD66DF";		//無主料 桃紅色
            }else if($row['m_status'] < 3 && $row['dept'] != 'LY'){					
                $row['flag'] = 8;
                $row['flag_color'] = "#A36ED4";		//無BOM 紫色
            }else if( !$row['m_acc_shp'] && $row['dept'] != 'LY'){					
                $row['flag'] = 5;
                $row['flag_color'] = "#FFC5F3";		//無副料 淺粉紅色
            }else if(group_order_style($row['style']) != $rtn[$i]['style'] && $rtn[$i]['style'] != 'mut.'){
                $row['flag'] = 2;
                $row['flag_color'] = "#F7FF1B"; //不同style黃色
            }else{
                $row['flag'] = 1;
                $row['flag_color'] = "#FFFFFF";
            }				

            // echo $row['ord_num'].' ~ '.$row['flag'].' / '.$row['flag_color'].'<br>';
            if($row['smpl_apv'] == '0000-00-00' && $row['dept'] != 'LY'){		//無樣本 			
                $row['flag'] = 7;
                //$row['flag_color'] = "#FF6F43";		//無樣本 橘色			
            }				
            $rtn[$i]['sch_rec'][] = $row;
            $j = increceDaysInDate($row['rel_etf'],0);
        }			
    }

    
    return $rtn;
} // end func	



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->increceDay($str_date,$days) Mode Design
# 計算排產日期(加入假日)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function increceDay($str_date,$days,$fty) {

    $hd_ary = $_SESSION['hd_ary'][$fty];

    $fnl_date = $str_date;
    if($days > 0){
        for($i=1; $i<=$days; $i++) {
            $fnl_date = increceDaysInDate($fnl_date,1);			
            if(date('w',strtotime($fnl_date)) == 0)$fnl_date = increceDaysInDate($fnl_date,1);	
            $tmp_ary = explode('-',$fnl_date);
            if(isset($hd_ary[$tmp_ary[0]][$tmp_ary[1]][$tmp_ary[2]])) {
                $fnl_date = increceDaysInDate($fnl_date,1);
            }
        }
    } else {
        $fnl_date = increceDaysInDate($fnl_date,1);			
        if(date('w',strtotime($fnl_date)) == 0)$fnl_date = increceDaysInDate($fnl_date,1);	
    }

    for($m=0;$m<7;$m++){
        if(date('w',strtotime($fnl_date)) == 0)$fnl_date = increceDaysInDate($fnl_date,1);	
        $tmp_ary = explode('-',$fnl_date);
        if(isset($hd_ary[$tmp_ary[0]][$tmp_ary[1]][$tmp_ary[2]])) {
            $fnl_date = increceDaysInDate($fnl_date,1);
        }
    }

    return $fnl_date;
} // end func	



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->increceDay($str_date,$days)
#		重算排產日期(加入假日)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function increceSchdDay($org_ets,$org_etf,$new_ets,$fty) {

$hd_ary = $_SESSION['hd_ary'][$fty];
$hdy = 0;		
$p_day = countDays ($org_ets,$org_etf);
$str_wk = date('w',strtotime($org_ets));
$end_wk = date('w',strtotime($org_etf));
$hdy = $p_day / 7;
$tmp = explode('.',$hdy);
$hdy = $tmp[0];
$md = $p_day % 7;

if(($md + $str_wk) > 7 || $str_wk > $end_wk) $hdy++;

$ets_ary = explode('-',$org_ets);
$etf_ary = explode('-',$org_etf);
foreach($hd_ary as $key => $value){		//特殊假日
    if($ets_ary[0] <= $key && $etf_ary[0] >= $key){			
        foreach($hd_ary[$key] as $sen_key => $sen_value){				
            if($ets_ary[1] <= $key && $etf_ary[1] >= $key){					
                for($i=0; $i<sizeof($hd_ary[$key][$sen_key]); $i++){
                    if($ets_ary[2] <= $hd_ary[$key][$sen_key][$i] && $etf_ary[2] >= $hd_ary[$key][$sen_key][$i])$hdy++;
                }
            }
        }
    }
}

/*		
for($i=1; $i<=$p_day; $i++)
{
    $tmp_date = increceDaysInDate($org_etf,$i);			
    if(date('w',strtotime($tmp_date)) == 0) $hdy++;			
}
*/

$tmp_day = $p_day -= $hdy;  //計算實際排產日數
$tmp_ets = $new_ets;

while(($tmp_day / 7) > 1)
{
    $hdy = 0;
    $str_wk = date('w',strtotime($tmp_ets));
    $end_wk = date('w',strtotime(increceDaysInDate($tmp_ets,$tmp_day)));
    $tmp = explode('.',($tmp_day / 7));
    $hdy = $tmp[0];
    $md = $tmp_day % 7;
    if(($md + $str_wk) > 7 || $str_wk > $end_wk) $hdy++;
    $p_day += $hdy;
    $tmp_day = $hdy;
    $tmp_ets = increceDaysInDate($tmp_ets,$tmp_day);			
}

$str_wk = date('w',strtotime($tmp_ets));
$end_wk = date('w',strtotime(increceDaysInDate($tmp_ets,$tmp_day)));
$md = $tmp_day % 7;
if(($md + $str_wk) > 7 || $str_wk > $end_wk) $p_day++;
$fnl_date = increceDaysInDate($new_ets,$p_day);




$hdy = 0;
$ets_ary = explode('-',$new_ets);
$etf_ary = explode('-',$fnl_date);	 
foreach($hd_ary as $key => $value){		//特殊假日
    if($ets_ary[0] <= $key && $etf_ary[0] >= $key){			
        foreach($hd_ary[$key] as $sen_key => $sen_value){				
            if($ets_ary[1] <= $key && $etf_ary[1] >= $key){					
                for($i=0; $i<sizeof($hd_ary[$key][$sen_key]); $i++){
                    if($ets_ary[2] <= $hd_ary[$key][$sen_key][$i] && $etf_ary[2] > $hd_ary[$key][$sen_key][$i])$hdy++;
                    if($etf_ary[2] == $hd_ary[$key][$sen_key][$i]) {
                        $fnl_date = increceDaysInDate($fnl_date,1);
                        if(date('w',strtotime($fnl_date)) == 0)$fnl_date = increceDaysInDate($fnl_date,1);
                    }
                    $etf_ary = explode('-',$fnl_date);
                }
            }
        }
    }
}
$fnl_date = increceDaysInDate($fnl_date,$hdy);
/*		
$hdy = 0;
for($i=1; $i<=$p_day; $i++)
{
    $tmp_date = increceDaysInDate($new_ets,$i);			
    if(date('w',strtotime($tmp_date)) == 0) $hdy++;			
}		

*/		
//		$p_day += $hdy;

//		$fnl_date = increceDaysInDate($new_ets,$p_day);
//		$p_day = countDays ($new_ets,$fnl_date);
    return $fnl_date;
} // end func	
	
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, season=0)	抓出指定記錄內資料 RETURN $row[] ptn_upload
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_order($fty,$limit_date='') {

		$sql = $this->sql;

		$today= date('Y-m-d');
		$rtn = array();
		$q_str = "SELECT s_order.order_num , s_order.combine , order_partial.p_su as su, order_partial.ext_su  , s_order.status,s_order.ie1,s_order.ie2,
										 order_partial.p_qty as qty, order_partial.ext_qty, s_order.style, 
										 order_partial.p_etd as etd, order_partial.mks, s_order.partial_num,
										 order_partial.id as p_id , s_order.ptn_upload , pdtion.mat_shp , pdtion.m_acc_shp 
							FROM   s_order, order_partial , pdtion
							WHERE	s_order.order_num = order_partial.ord_num AND 
									s_order.order_num = pdtion.order_num AND 
									s_order.status >= 4 AND 
										 s_order.status <= 8 AND s_order.status <> 5 AND										 
										 order_partial.ext_qty < order_partial.p_qty AND s_order.factory ='".$fty."' AND s_order.etd > '2009-12-31' ORDER BY `etd` , `combine`  ASC  ";
		if($limit_date) $q_str .= " AND p_etd < '".$limit_date."'";
		
		 // echo $q_str ."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		while ($row = $sql->fetch($q_result)) {
			// echo $row['etd'].' ~ '.countDays(date('Y-m-d'),$row['etd']).'<br>';
			$row['lead_time'] = countDays(date('Y-m-d'),$row['etd']);
			$row['rem_qty'] = $row['qty'] - $row['ext_qty'];
            $row['combine_str'] = $GLOBALS['order']->get_combine($row['combine']);
			$rtn[] = $row;  
		}
		
		// for($i=0; $i<sizeof($rtn); $i++)
		// {
			// $q_str="SELECT * FROM stk_ord_link WHERE ord_new = '".$rtn[$i]['order_num']."'";		
			// $q_result = $sql->query($q_str);
			// if($row = $sql->fetch($q_result))
			// {
				// $rtn[$i]['des'] = 'fellow up ORD# : <b>'.$row['ord_org'].'</b> '.$row['des'];
			// }
		// }
		
		return $rtn;
	} // end func	
	
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, season=0)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function auto_schedule($p_id,$order,$fty) {

$sql = $this->sql;

$FULL_TIME = $GLOBALS['FULL_TIME'];

$today= date('Y-m-d');
$rtn = array();
$q_str = "SELECT s_order.order_num, order_partial.p_su as su, order_partial.p_qty as qty, 
                                 order_partial.ext_su , order_partial.ext_qty, 
                                 status, mat_eta, m_acc_eta, style, s_order.apv_date,
                                    pdtion.ext_period, order_partial.p_etd as etd
                    FROM   s_order,pdtion,order_partial
                    WHERE  s_order.order_num = pdtion.order_num AND s_order.order_num = order_partial.ord_num AND
                                 s_order.order_num ='".$order."' AND order_partial.id='".$p_id."'
                    ORDER BY pdtion.mat_eta, m_acc_eta";

$q_result = $sql->query($q_str);
$ord_rec = $sql->fetch($q_result);		
$ord_sch_date = $ord_rec['apv_date'];


$sch_style = group_order_style($ord_rec['style']);
$rtn = array();
$rtn_mk = 0;

$q_str = "SELECT pdt_saw_line.*, max(schedule.rel_etf) as etf, count(schedule.line_id)as sch_count FROM pdt_saw_line
                    LEFT JOIN schedule ON pdt_saw_line.id = schedule.line_id 
                    WHERE pdt_saw_line.style ='".$sch_style."' AND schedule.id IS NULL AND 
                                pdt_saw_line.fty ='".$fty."' AND pdt_saw_line.del_mk = 0 AND pdt_saw_line.worker > 0 AND  pdt_saw_line.line REGEXP '^[0-9]'
                    GROUP BY pdt_saw_line.id";

$q_result = $sql->query($q_str);		
if($row = $sql->fetch($q_result))
{
    $rtn = $row;
    $rtn_mk = 1;
    $ets = increceDaysInDate($ord_sch_date,1);
}		



if($rtn_mk == 0)
{
    $q_str = "SELECT pdt_saw_line.*, max(schedule.rel_etf) as etf FROM pdt_saw_line, schedule  
                        WHERE pdt_saw_line.id = schedule.line_id AND pdt_saw_line.style ='".$sch_style."' AND 
                        pdt_saw_line.fty ='".$fty."' AND pdt_saw_line.del_mk = 0 AND pdt_saw_line.worker > 0 AND  pdt_saw_line.line REGEXP '^[0-9]'
                        GROUP BY pdt_saw_line.id
                        HAVING max(schedule.rel_etf) ='".$ord_sch_date."'";
        
    $q_result = $sql->query($q_str);
    $tmp_date = '9999-99-99';
    while($row = $sql->fetch($q_result))
    {
        $rtn = $row;
        $rtn_mk = 1;
        $ets = increceDaysInDate($ord_sch_date,1);
    }
}

if($rtn_mk == 0)					
{
    $q_str = "SELECT pdt_saw_line.*, max(schedule.rel_etf) as etf FROM pdt_saw_line, schedule
                        WHERE pdt_saw_line.id = schedule.line_id AND  
                                    pdt_saw_line.style ='".$sch_style."' AND pdt_saw_line.fty ='".$fty."'	AND
                                    pdt_saw_line.del_mk = 0 AND pdt_saw_line.worker > 0	AND  pdt_saw_line.line REGEXP '^[0-9]'					
                        GROUP BY pdt_saw_line.id
                        HAVING max(schedule.rel_etf) <'".$ord_sch_date."'";
                
    $q_result = $sql->query($q_str);
    $tmp_date = '1111-11-11';
    while($row = $sql->fetch($q_result))
    {
        if($tmp_date < $row['etf'])
        {
            $rtn = $row;
            $rtn_mk = 1;
            $tmp_date = $row['etf'];
            $ets =increceDaysInDate($ord_sch_date,1);
        }
    }

    
}
                    
                    
if($rtn_mk == 0)					
{
    $q_str = "SELECT pdt_saw_line.*, max(schedule.rel_etf) as etf FROM pdt_saw_line, schedule
                        WHERE pdt_saw_line.id = schedule.line_id AND pdt_saw_line.style ='".$sch_style."' AND 
                        pdt_saw_line.fty ='".$fty."' AND pdt_saw_line.del_mk = 0 AND pdt_saw_line.worker > 0 AND  pdt_saw_line.line REGEXP '^[0-9]'
                        GROUP BY pdt_saw_line.id
                        HAVING max(schedule.rel_etf) >'".$ord_sch_date."'";
                            
    $q_result = $sql->query($q_str);
            
    $tmp_date = '9999-99-99';
    while($row = $sql->fetch($q_result))
    {
        if($tmp_date > $row['etf'])
        {
            $rtn = $row;
            $rtn_mk = 2;
            $tmp_date = $row['etf'];
            $ets =increceDaysInDate($row['etf'],1);					
        }
    }				
  if($rtn_mk == 2)
  {
    $q_str = "SELECT rel_etf as etf FROM schedule, s_order 
                        WHERE s_order.order_num = schedule.ord_num AND line_id ='".$rtn['id']."' AND 
                                    etd <'".$ord_rec['etd']."'
                        ORDER BY etd DESC
                        Limit 1";
    $q_result = $sql->query($q_str);
    $row = $sql->fetch($q_result);
    $ets =increceDaysInDate($row['etf'],1);	
    
  }
}						
if($rtn_mk == 0)
{
    $this->msg->add("Can't find production line, please check production detail first.");
    $this->msg->merge($sql->msg);
    return 0;
}

if($ets < date('Y-m-d'))$ets = date('Y-m-d');
if($rtn['success_rate'] == 0) $rtn['success_rate'] = 1;
$ext_period = ($ord_rec['su']-$ord_rec['ext_su']) / ( $FULL_TIME * $rtn['worker'] * $rtn['success_rate']);
$tmp = explode($ext_period,'.');
if(isset($tmp[1]) && $tmp[1])$ext_period++;
if( $ext_period <= 1) $ext_period = 2;
$etf = $this->increceDay($ets,$ext_period,$fty);





            # 加入資料庫
$q_str = "INSERT INTO schedule (line_id, ord_num, p_id, su, qty, ets,etf,rel_ets,rel_etf,fty,open_date,open_user) 
                    VALUES ('".$rtn['id']."','"
                                        .$order."','"
                                        .$p_id."','"
                                        .($ord_rec['su']-$ord_rec['ext_su'])."','"
                                        .($ord_rec['qty']-$ord_rec['ext_qty'])."','"
                                        .$ets."','"
                                        .$etf."','"
                                        .$ets."','"
                                        .$etf."','"
                                        .$fty."','"
                                        .date('Y-m-d')."','"
                                        .$GLOBALS['SCACHE']['ADMIN']['login_id']."')";

if (!$q_result = $sql->query($q_str)) {
    $this->msg->add("Error ! 無法新增資料記錄.");
    $this->msg->merge($sql->msg);
    return false;    
}
$season_id = $sql->insert_id();  //取出 新的 id

if($rtn_mk == 2)
{
//重算生產線目前訂單後其他訂單排產 start		
    $oth_sch = array();
    $new_etf = increceDaysInDate($etf,1);
    $q_str = "SELECT schedule.* FROM schedule							
                        WHERE line_id ='".$rtn['id']."' AND rel_ets >= '".$ets."' AND id <> '".$season_id."'
                      ORDER BY rel_ets";	
             
    $q_result = $sql->query($q_str);
    while($row = $sql->fetch($q_result)) 
    {
        $oth_sch[] = $row; 
    }
    if(sizeof($oth_sch) > 0)
    {
        for($i=0; $i<sizeof($oth_sch); $i++)  //從排插單後訂單
        {

        $period_dif = countDays ($oth_sch[$i]['rel_ets'],$new_etf);   //計算插單後需要移動的日期數
            $oth_sch[$i]['new_ets'] = increceDaysInDate($oth_sch[$i]['rel_ets'],$period_dif);

          $sp_period = $oth_sch[$i]['su'] /( $FULL_TIME * $rtn['worker'] *$rtn['success_rate']);
            $tmp = explode('.',$sp_period);
            if(isset($tmp[1]) && $tmp[1])$sp_period++;
            if ( $sp_period <= 1) $sp_period = 2;
          $oth_sch[$i]['new_etf'] = $this->increceDay($oth_sch[$i]['new_ets'],$sp_period,$fty);			


            $q_str =  "UPDATE schedule SET rel_etf='".$oth_sch[$i]['new_etf']."', rel_ets='".$oth_sch[$i]['new_ets']."', etf='".$oth_sch[$i]['new_etf']."', ets='".$oth_sch[$i]['new_ets']."'  WHERE id = '".$oth_sch[$i]['id']."'";
            $q_result = $sql->query($q_str);
            $new_etf =  increceDaysInDate($oth_sch[$i]['new_etf'],1);
     
     }
    }
    //重算生產線目前訂單後其他訂單排產 end		

    //組合訂單
    $this->group_schedule($season_id);			
}


    return $season_id;
} // end func	

	
	

	
	

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, season=0)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_by_mm_dynamic($fty,$sch_date,$fsh_date) {

	$sql = $this->sql;
	$sch_finish_rate = $_SESSION['sch_finish_rate'];
    $FULL_TIME = $GLOBALS['FULL_TIME'];
	
	$today= date('Y-m-d');
	$_SESSION['line'] = $rtn = array();
	$str_date = $sch_date;
	
	$q_str = "
	SELECT pdt_saw_line.*, (pdt_saw_line.success_rate * ".$FULL_TIME." ) as day_avg FROM pdt_saw_line
	WHERE  pdt_saw_line.fty ='".$fty."' AND worker > 0 AND del_mk = 0 ORDER BY line
	";

	$q_result = $sql->query($q_str);		
	while($row = $sql->fetch($q_result))
	{
		$tmp = explode(' ',$row['line']);
		if(isset($tmp[1]))$row['line'] = $tmp[0]."+".$tmp[1];
		$_SESSION['line'][] = $row;
		$rtn[] = $row;
	}		
	
	for($i=0; $i<sizeof($rtn); $i++)
	{
		$q_str = "
		SELECT schedule.*, s_order.style, s_order.smpl_apv, s_order.status as ord_status ,
					order_partial.p_etd as etd, s_order.id as ord_id, pdtion.mat_shp, pdtion.m_acc_shp,
					s_order.style_num, order_partial.p_qty as ord_qty, s_order.ie1, s_order.m_status,
					(schedule.qty - schedule.pdt_qty) as rem_qty, ptn_upload, s_order.dept, 
					s_order.partial_num, order_partial.mks
		FROM schedule, s_order, pdtion, order_partial
		WHERE  s_order.order_num = schedule.ord_num AND schedule.ord_num = pdtion.order_num AND
					 schedule.p_id = order_partial.id AND
					 s_order.order_num = pdtion.order_num AND s_order.order_num = order_partial.ord_num AND
					 schedule.line_id ='".$rtn[$i]['id']."' AND schedule.rel_etf > '".$str_date."' 						
		ORDER BY rel_etf";
		
		// echo $q_str."<BR>";
		
		$q_result = $sql->query($q_str);		
		
		$j=$str_date;
		while ( $row = $sql->fetch($q_result)) {
//				if(isset($f_date) && $f_date == $row['rel_ets']) $row['rel_ets'] =increceDaysInDate($row['rel_ets'],1);
			$tmp = explode('-',$row['ord_num']);
			$row['ord_init'] = $tmp[1];
			$s_date = ( $row['rel_ets'] != '0000-00-00' ) ? $row['rel_ets'] : $row['ets'] ;
			
			// if( $s_date < $str_date ) $s_date = $str_date;
			// echo $s_date.'<br>';
//				if($row['rel_etf'] != '0000-00-00'){$f_date = $row['rel_etf'];}else{$f_date = $row['etf'];}
			
			// if($j == $str_date && $s_date > $str_date)
			// {
				// $non_row['day_range'] = dayCount($j,$s_date);
				// $non_row['flag'] = 0;
				// $non_row['flag_color'] = "#eeeeee";
				// $rtn[$i]['sch_rec'][] = $non_row;
			// }				

			// if( $j != $s_date && $j != $str_date ) {
				// $non_row['day_range'] = dayCount($j,$s_date);
				// $non_row['flag'] = 0;
				// $non_row['flag_color'] = "#eeeeee";
				// if($non_row['day_range'] )$rtn[$i]['sch_rec'][] = $non_row;
			// }
			
			// if( $s_date < $str_date ) {
				// $row['day_range'] = dayCount($str_date,$row['rel_etf']);
			// } else if ($row['rel_etf'] > $fsh_date){
				// $row['day_range'] = dayCount($s_date,$fsh_date);
			// } else {
				// $row['day_range'] = dayCount($s_date,$row['rel_etf']);
			// }				
		
			// if( $row['rel_ets'] < $row['ets'] && $row['rel_ets'] <> '0000-00-00' ){
				// $ets = $row['rel_ets'];
			// } else {
				// $ets = $row['ets'];
			// }
			
			if ( $row['ord_status'] >= '10' ||  ( $row['pdt_qty'] / $row['qty'] ) > $sch_finish_rate ) {
				$row['flag'] = 11;
				$row['flag_color'] = "#D7D7FF"; //生產FINISH 淺藍
			} else if ( $row['rel_etf'] > $row['etd'] && $row['ord_status'] >= '8' && $row['pdt_qty'] > 0) {
				$row['flag'] = 10;
				$row['flag_color'] = "#96DDA3"; //排產etd超過出口日 但己生產
			} else if ( $row['ord_status'] >= '8' && $row['pdt_qty'] > 0) {					
				$row['flag'] = 4;
				$row['flag_color'] = "#D0FDCD";	//生產綠色
			} else if ( $row['rel_etf'] > $row['etd']) {
				$row['flag'] = 9;
				$row['flag_color'] = "#FD1838"; //排產etd超過出口日 紅色
			} else if ( !$row['mat_shp'] && $row['dept'] != 'LY'){					
				$row['flag'] = 6;
				$row['flag_color'] = "#FD66DF";		//無主料 桃紅色
			} else if ( $row['m_status'] < 3 && $row['dept'] != 'LY') {					
				$row['flag'] = 8;
				$row['flag_color'] = "#A36ED4";		//無BOM 紫色
			} else if ( !$row['m_acc_shp'] && $row['dept'] != 'LY') {					
				$row['flag'] = 5;
				$row['flag_color'] = "#FFC5F3";		//無副料 淺粉紅色
			} else if ( group_order_style($row['style']) != $rtn[$i]['style'] && $rtn[$i]['style'] != 'mut.') {
				$row['flag'] = 2;
				$row['flag_color'] = "#F7FF1B"; //不同style黃色
			} else {
				$row['flag'] = 1;
				$row['flag_color'] = "#FFFFFF";
			}				

			if($row['smpl_apv'] == '0000-00-00' && $row['dept'] != 'LY'){		//無樣本 			
				$row['flag'] = 7;
				//$row['flag_color'] = "#FF6F43";		//無樣本 橘色			
			}
			
			$rtn[$i]['sch_rec'][] = $row;
			// $j = increceDaysInDate($row['rel_etf'],1);
			// print_r($rtn[$i]);
			// exit;
		}			
	}

	// print_r($rtn);
	return $rtn;
} // end func	
	
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, season=0)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_contract($fty,$sch_date,$fsh_date) {

		$sql = $this->sql;
		$sch_finish_rate = $_SESSION['sch_finish_rate'];
        
		$con = array();
		$today= date('Y-m-d');
		$rtn = array();
		$str_date = $sch_date."-01";
		
		$q_str = "SELECT pdt_saw_line.* FROM pdt_saw_line
							WHERE  pdt_saw_line.fty ='".$fty."' AND worker = 0 ORDER BY line";							
							

		$q_result = $sql->query($q_str);		
		while($row = $sql->fetch($q_result))
		{
			$tmp = explode(' ',$row['line']);			
			$rtn[] = $row;
		}		
		
		$j=-1;
		for($i=0; $i<sizeof($rtn); $i++)
		{

			$q_str = "SELECT schedule.*, s_order.style, s_order.smpl_apv, s_order.status as ord_status,
											 order_partial.p_etd as etd, s_order.id as ord_id, pdtion.mat_shp, pdtion.m_acc_shp,
											 s_order.style_num, order_partial.p_qty as ord_qty, s_order.ie1, s_order.m_status,s_order.combine,
											 s_order.partial_num, order_partial.mks
                                             ,(schedule.qty - schedule.pdt_qty) as rem_qty
								FROM schedule, s_order, pdtion, order_partial
								WHERE  s_order.order_num = schedule.ord_num AND schedule.ord_num = pdtion.order_num AND
											 s_order.order_num = pdtion.order_num AND 
											 s_order.order_num = order_partial.ord_num AND schedule.p_id = order_partial.id AND schedule.fty ='".$fty."' AND
											 schedule.line_id ='".$rtn[$i]['id']."' AND schedule.rel_etf > '".$str_date."' 	
								ORDER BY rel_etf";							
			$q_result = $sql->query($q_str);		
			
			
			while($row = $sql->fetch($q_result))
			{
                $row['combine_str'] = $GLOBALS['order']->get_combine($row['combine']);
				$j++;
				$tmp = explode('-',$row['ord_num']);
				$row['ord_init'] = $tmp[1];
				
				$con[$j] = $rtn[$i];
				$s_date = $row['ets'];
				
				if($s_date > $str_date)
				{
					$non_row['day_range'] = dayCount($str_date,$s_date);
					$non_row['flag'] = 0;
					$non_row['flag_color'] = "#eeeeee";
					$con[$j]['sch_rec'][] = $non_row;
				}

					
					if($s_date < $str_date)
					{						
						$row['day_range'] = dayCount($str_date,$row['rel_etf']);
					}else if($row['rel_etf'] > $fsh_date){
						$row['day_range'] = dayCount($s_date,$fsh_date);
					}else{						
						$row['day_range'] = dayCount($s_date,$row['rel_etf']);
					}				
							
					
					if( $row['ord_status'] >= '10' ||  ($row['pdt_qty']/$row['qty']) > $sch_finish_rate) 
					{
						$row['flag'] = 11;
						$row['flag_color'] = "#D7D7FF"; //FINISH 水藍
					}else if( $row['rel_etf'] > $row['etd'] && $row['ord_status'] >= '8' && $row['pdt_qty'] > 0) {
						$row['flag'] = 10;
						$row['flag_color'] = "#96DDA3"; //排產etd超過出口日 但己生產
					}else if($row['ord_status'] >= '8' && $row['pdt_qty'] > 0){					
						$row['flag'] = 4;
						$row['flag_color'] = "#D0FDCD";	//生產綠色
					}else if( $row['rel_etf'] > $row['etd']) {
						$row['flag'] = 9;
						$row['flag_color'] = "#FD1838"; //排產etd超過出口日 紅色
					}else if($row['m_status'] < 3){					
						$row['flag'] = 8;
						$row['flag_color'] = "#A36ED4";		//無BOM 紫色					
					}else if(!$row['mat_shp']){					
						$row['flag'] = 8;
						$row['flag_color'] = "#FF70E2";		//無主料 桃紅色
					}else if( !$row['m_acc_shp']){					
						$row['flag'] = 5;
						$row['flag_color'] = "#FFC5F3";		//無副料 淺粉紅色
					}else if($row['smpl_apv'] == '0000-00-00' && (dayCount($ets,$today) < 2 || $today > $ets) ){
						$row['flag'] = 3;
						$row['flag_color'] = "#FE00B9";		// 快生產但沒核樣單	紅色
					}else if(group_order_style($row['style']) != $rtn[$i]['style'] && $rtn[$i]['style'] != 'mut.'){
						$row['flag'] = 2;
						$row['flag_color'] = "#F8FF84"; //不同style黃色
					}else{
						$row['flag'] = 1;
						$row['flag_color'] = "#FFFFFF";
					}					

				 if($row['smpl_apv'] == '0000-00-00'){		//無樣本 			
						$row['flag'] = 7;
			
				 }
				
				$con[$j]['sch_rec'][] = $row;
				
			}			
		}
		
		return $con;
	} // end func	
	
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, season=0)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function count_schedule($id) {

		$sql = $this->sql;
		
			$q_str = "SELECT count(schedule.line_id)as sch_count  FROM pdt_saw_line, schedule								
								WHERE pdt_saw_line.id = schedule.line_id  AND status = 0 AND 
											pdt_saw_line.id =".$id."
								GROUP BY pdt_saw_line.id";
					
			$q_result = $sql->query($q_str);

			if(!$row = $sql->fetch($q_result))
			{

				return 0;
			}


		return $row[0];
	} // end func		
	


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->spare_schedule($parm)	將部分數量分配到其他生產線
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function spare_schedule($parm) {

    $sql = $this->sql;
    $FULL_TIME = $GLOBALS['FULL_TIME'];
    
    $today= date('Y-m-d');
    $rtn = array();
    $q_str = "SELECT s_order.order_num, order_partial.p_su as su, order_partial.ext_su  , status, 
                                   mat_eta, m_acc_eta, style,	order_partial.ext_period, s_order.ie1, s_order.apv_date
                        FROM   s_order,pdtion,order_partial
                        WHERE  s_order.order_num = pdtion.order_num AND s_order.order_num = order_partial.ord_num AND
                                     order_partial.id ='".$parm['p_id']."'
                        ORDER BY pdtion.mat_eta, m_acc_eta";

    $q_result = $sql->query($q_str);
    $ord_rec = $sql->fetch($q_result);
    
    $ord_sch_date = $ord_rec['apv_date'];
    
    $sp_su = NUMBER_FORMAT(($parm['sp_qty'] * $ord_rec['ie1']),'0','.','');
    $sch_style = group_order_style($ord_rec['style']);
    $rtn = array();
    $rtn_mk = 0;
    
    
    $ets = '1111-11-11';
    $q_str = "SELECT pdt_saw_line.*, max(schedule.rel_etf) as etf, count(schedule.line_id)as sch_count FROM pdt_saw_line
                        LEFT JOIN schedule ON pdt_saw_line.id = schedule.line_id 
                        WHERE pdt_saw_line.id ='".$parm['line_sp']."'
                        GROUP BY pdt_saw_line.id";

//	echo $q_str."<BR>";								
        $q_result = $sql->query($q_str);	
        $row = $sql->fetch($q_result);
                
        if($row['etf'] && $row['etf'] > $ord_sch_date)
        {
            $row['ets'] =increceDaysInDate($row['etf'],1);
            if($row['ets'] > $ets ) $ets = $row['ets'];
        }else{
            $row['ets']  = increceDaysInDate($ord_sch_date,1);
            if($row['ets'] > $ets ) $ets = $row['ets'];
        }
        $rtn[0] = $row;

        if($parm['pt_ets'])$rtn[0]['ets'] = $ets = increceDaysInDate($parm['pt_ets'],1);
        if($ets < date('Y-m-d'))$rtn[0]['ets'] = $ets = date('Y-m-d');
        $rtn[0]['sp_su'] = $sp_su; 
        if($rtn[0]['success_rate'] == 0)$rtn[0]['success_rate'] = 1;
      $rtn[0]['sp_period'] = $rtn[0]['sp_su'] /( $FULL_TIME * $rtn[0]['worker'] * $rtn[0]['success_rate']);
        $tmp = explode('.',$rtn[0]['sp_period']);
        if(isset($tmp[1]) && $tmp[1])$rtn[0]['sp_period']++;
        if ( $rtn[0]['sp_period'] <= 1) $rtn[0]['sp_period'] = 2;
// 		  $rtn[0]['new_etf'] = increceDaysInDate($ets,$rtn[0]['sp_period']);			
      $rtn[0]['new_etf'] = $this->increceDay($ets,$rtn[0]['sp_period'],$parm['fty']);			







                # 加入資料庫
    $q_str = "INSERT INTO schedule (line_id, ord_num, p_id, des, su, qty, ets,etf,rel_ets,rel_etf,fty,open_date,open_user) 
                        VALUES ('".$rtn[0]['id']."','"
                                            .$parm['ord_num']."','"	
                                            .$parm['p_id']."','"	
                                            .$parm['des']."','"												
                                            .$rtn[0]['sp_su']."','"
                                            .$parm['sp_qty']."','"
                                            .$ets."','"
                                            .$rtn[0]['new_etf']."','"
                                            .$ets."','"
                                            .$rtn[0]['new_etf']."','"
                                            .$parm['fty']."','"
                                            .date('Y-m-d')."','"
                                            .$GLOBALS['SCACHE']['ADMIN']['login_id']."')";
    $q_result = $sql->query($q_str);
    $new_id = $sql->insert_id();  //取出 新的 id		
    
//重算生產線目前訂單後其他訂單排產 start		
    $oth_sch = array();
    $rtn[0]['new_etf'] = increceDaysInDate($rtn[0]['new_etf'],1);
    $q_str = "SELECT schedule.* FROM schedule							
                        WHERE line_id ='".$rtn[0]['id']."' AND rel_ets >= '".$rtn[0]['ets']."' AND id <> '".$new_id."'
                      ORDER BY rel_ets";	
                      
    $q_result = $sql->query($q_str);
    while($row = $sql->fetch($q_result)) 
    {
        $oth_sch[] = $row; 
    }
    $new_etf = $rtn[0]['new_etf'];
    if(sizeof($oth_sch) > 0)
    {
        for($i=0; $i<sizeof($oth_sch); $i++)  //從排插單後訂單
        {

            $period_dif = countDays ($oth_sch[$i]['rel_ets'],$new_etf);   //計算插單後需要移動的日期數		  		
                $oth_sch[$i]['new_ets'] = increceDaysInDate($oth_sch[$i]['rel_ets'],$period_dif);

              $sp_period = $oth_sch[$i]['su'] /( $FULL_TIME * $rtn[0]['worker'] * $rtn[0]['success_rate']);
                $tmp = explode('.',$sp_period);
                if(isset($tmp[1]) && $tmp[1])$sp_period++;
                if ( $sp_period <= 1) $sp_period = 2;
              $oth_sch[$i]['new_etf'] = $this->increceDay($oth_sch[$i]['new_ets'],$sp_period,$parm['fty']);			

                $q_str =  "UPDATE schedule SET rel_etf='".$oth_sch[$i]['new_etf']."', rel_ets='".$oth_sch[$i]['new_ets']."', etf='".$oth_sch[$i]['new_etf']."', ets='".$oth_sch[$i]['new_ets']."'  WHERE id = '".$oth_sch[$i]['id']."'";
                $q_result = $sql->query($q_str);
//重加入訂單ETS ETF
                $this->add_ord_schedule($oth_sch[$i]['p_id']);					
                if($this->check_sch_finish($oth_sch[$i]['p_id']))$this->add_capacity($oth_sch[$i]['p_id']);
                
                $new_etf =  increceDaysInDate($oth_sch[$i]['new_etf'],1);
         
      }
    }
    
//組合訂單
$this->group_schedule($new_id);
    
//重算生產線目前訂單後其他訂單排產 end		
    
    
    $this->del_schedule($parm['id'],$parm['sp_qty'],$parm['ord_num'],$parm['p_id']);
    





    return $new_id;
} // end func	




#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->point_schedule($parm)	一開始指定生產線與數量
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function point_schedule($parm) {

    $sql = $this->sql;
    $FULL_TIME = $GLOBALS['FULL_TIME'];
    
    $today= date('Y-m-d');
    $rtn = array();
    $q_str = "SELECT s_order.order_num, order_partial.p_su as su, order_partial.ext_su  , status, 
                                     mat_eta, m_acc_eta, style,	order_partial.ext_period, s_order.ie1, s_order.apv_date
                        FROM   s_order,pdtion,order_partial
                        WHERE  s_order.order_num = pdtion.order_num AND order_partial.ord_num = s_order.order_num AND
                                     order_partial.id ='".$parm['p_id']."'
                        ORDER BY pdtion.mat_eta, m_acc_eta";


    $q_result = $sql->query($q_str);
    $ord_rec = $sql->fetch($q_result);		
    $ord_sch_date = $ord_rec['apv_date'];
    
    $sp_su = NUMBER_FORMAT(($parm['sp_qty'] * $ord_rec['ie1']),'0','.','');
    $sch_style = group_order_style($ord_rec['style']);
    $rtn = array();
    $rtn_mk = 0;
    

    $ets = '1111-11-11';
    $q_str = "SELECT pdt_saw_line.*, max(schedule.rel_etf) as etf, count(schedule.line_id)as sch_count FROM pdt_saw_line
                        LEFT JOIN schedule ON pdt_saw_line.id = schedule.line_id 
                        WHERE pdt_saw_line.id ='".$parm['line_sp']."'
                        GROUP BY pdt_saw_line.id";

                    
        $q_result = $sql->query($q_str);	
        $row = $sql->fetch($q_result);
                
        if($row['etf'] && $row['etf'] > $ord_sch_date)
        {
            $row['ets'] =increceDaysInDate($row['etf'],1);
            if($row['ets'] > $ets ) $ets = $row['ets'];
        }else{
            $row['ets']  = increceDaysInDate($ord_sch_date,1);
            if($row['ets'] > $ets ) $ets = $row['ets'];
        }
        $rtn[0] = $row;

        if($parm['pt_ets'])$rtn[0]['ets'] = $ets = increceDaysInDate($parm['pt_ets'],1);	
        if($ets < date('Y-m-d'))$ets = date('Y-m-d');	
        
        $rtn[0]['sp_su'] = $sp_su; 
        if($rtn[0]['success_rate'] == 0)$rtn[0]['success_rate'] = 1;
      $rtn[0]['sp_period'] = $rtn[0]['sp_su'] /( $FULL_TIME * $rtn[0]['worker'] * $rtn[0]['success_rate']);
        $tmp = explode('.',$rtn[0]['sp_period']);
        if(isset($tmp[1]) && $tmp[1])$rtn[0]['sp_period']++;
      if ( $rtn[0]['sp_period'] <= 1)$rtn[0]['sp_period'] = 2;
//= increceDaysInDate($ets,$rtn[0]['sp_period']);			
        $rtn[0]['new_etf'] = $this->increceDay($ets,$rtn[0]['sp_period'],$parm['fty']);
                # 加入資料庫
    $q_str = "INSERT INTO schedule (line_id, ord_num,p_id,des,su, qty, ets,etf,rel_ets,rel_etf,fty,open_date,open_user) 
                        VALUES ('".$rtn[0]['id']."','"
                                            .$parm['ord_num']."','"	
                                            .$parm['p_id']."','"	
                                            .$parm['des']."','"											
                                            .$rtn[0]['sp_su']."','"
                                            .$parm['sp_qty']."','"
                                            .$ets."','"
                                            .$rtn[0]['new_etf']."','"
                                            .$ets."','"
                                            .$rtn[0]['new_etf']."','"
                                            .$parm['fty']."','"
                                            .date('Y-m-d')."','"
                                            .$GLOBALS['SCACHE']['ADMIN']['login_id']."')";
    $q_result = $sql->query($q_str);
    $new_id = $sql->insert_id();  //取出 新的 id	
//重算生產線目前訂單後其他訂單排產 start		
    $oth_sch = array();
    $rtn[0]['new_etf'] = increceDaysInDate($rtn[0]['new_etf'],1);
    $q_str = "SELECT schedule.* FROM schedule							
                        WHERE line_id ='".$parm['line_sp']."' AND rel_ets >= '".$rtn[0]['ets']."' AND id <> '".$new_id."'
                      ORDER BY rel_ets";	
                     
    $q_result = $sql->query($q_str);
    while($row = $sql->fetch($q_result)) 
    {
        $oth_sch[] = $row; 
    }
    $new_etf = $rtn[0]['new_etf'];
    if(sizeof($oth_sch) > 0)
    {
        for($i=0; $i<sizeof($oth_sch); $i++)  //從排插單後訂單
        {

            $period_dif = countDays ($oth_sch[$i]['rel_ets'],$new_etf);   //計算插單後需要移動的日期數
                $oth_sch[$i]['new_ets'] = increceDaysInDate($oth_sch[$i]['rel_ets'],$period_dif);


              $sp_period = $oth_sch[$i]['su'] /( $FULL_TIME * $rtn[0]['worker'] * $rtn[0]['success_rate']);
                $tmp = explode('.',$sp_period);
                if(isset($tmp[1]) && $tmp[1])$sp_period++;
                if ( $sp_period <= 1) $sp_period = 2;
              $oth_sch[$i]['new_etf'] = $this->increceDay($oth_sch[$i]['new_ets'],$sp_period,$parm['fty']);			

                $q_str =  "UPDATE schedule SET rel_etf='".$oth_sch[$i]['new_etf']."', rel_ets='".$oth_sch[$i]['new_ets']."', etf='".$oth_sch[$i]['new_etf']."', ets='".$oth_sch[$i]['new_ets']."'  WHERE id = '".$oth_sch[$i]['id']."'";
                $q_result = $sql->query($q_str);
//重加入訂單ETS ETF
                $this->add_ord_schedule($oth_sch[$i]['p_id']);
                if($this->check_sch_finish($oth_sch[$i]['p_id']))$this->add_capacity($oth_sch[$i]['p_id']);

                $new_etf =  increceDaysInDate($oth_sch[$i]['new_etf'],1);
     
      }
    }		
//重算生產線目前訂單後其他訂單排產 end						

//組合訂單
$this->group_schedule($new_id);

    return $new_id;
} // end func	




#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->change_line($parm) 更換排產生產線
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function change_line($parm) {

    $sql = $this->sql;
    $FULL_TIME = $GLOBALS['FULL_TIME'];
    
    $today= date('Y-m-d');
    $rtn = array();
    $q_str = "SELECT s_order.order_num, order_partial.p_su as su, order_partial.ext_su  , status, 
                                     mat_eta, m_acc_eta, style,	order_partial.ext_period, s_order.apv_date
                        FROM   s_order,pdtion, order_partial
                        WHERE  s_order.order_num = pdtion.order_num AND s_order.order_num = order_partial.ord_num AND
                                     order_partial.id ='".$parm['p_id']."'
                        ORDER BY pdtion.mat_eta, m_acc_eta";

    $q_result = $sql->query($q_str);
    $ord_rec = $sql->fetch($q_result);
    $ord_sch_date = $ord_rec['apv_date'];
    
    $sch_style = group_order_style($ord_rec['style']);
    $rtn = array();
    $rtn_mk = 0;
    
    
    $ets = '1111-11-11';
    $q_str = "SELECT pdt_saw_line.*, max(schedule.rel_etf) as etf, count(schedule.line_id)as sch_count FROM pdt_saw_line
                        LEFT JOIN schedule ON pdt_saw_line.id = schedule.line_id 
                        WHERE pdt_saw_line.id ='".$parm['new_line']."'
                        GROUP BY pdt_saw_line.id";
//echo $q_str."<BR>";						
    $q_result = $sql->query($q_str);	
    $row = $sql->fetch($q_result);

    if($row['etf'] && $row['etf'] > $ord_sch_date)
    {
        $row['ets'] = increceDaysInDate($row['etf'],1);
    } else {
        $row['ets']  = increceDaysInDate($ord_sch_date,1);
    }
    $rtn = $row;

    if($parm['pt_ets'])$rtn['ets'] = increceDaysInDate($parm['pt_ets'],1);	
    if($rtn['ets'] < date('Y-m-d'))$rtn['ets']= date('Y-m-d');

    $q_str = "SELECT pdt_saw_line.*,  schedule.su, schedule.rel_etf, schedule.rel_ets, schedule.ets , 
                                     schedule.id as sch_id, schedule.qty
                        FROM pdt_saw_line, schedule
                        WHERE pdt_saw_line.id = schedule.line_id  AND schedule.id ='".$parm['org_id']."'"	;
//echo $q_str."<BR>";
        $q_result = $sql->query($q_str);	
        $org_rec = $sql->fetch($q_result);
        
//計算新生產線生產日數與 ETF

        if($rtn['success_rate'] == 0) $rtn['success_rate'] = 1;
        $rtn['ext_period'] = $org_rec['su'] /( $FULL_TIME * $rtn['worker'] *$rtn['success_rate']);						
        $tmp = explode('.',$rtn['ext_period']);
        if(isset($tmp[1]) && $tmp[1])$rtn['ext_period']++;
        if($rtn['ext_period'] <= 1)$rtn['ext_period'] = 2;
//	 		$new_etf = $rtn['new_etf'] = increceDaysInDate($rtn['ets'],$rtn['ext_period']);
        $new_etf = $rtn['new_etf'] = $this->increceDay($rtn['ets'],$rtn['ext_period'],$parm['fty']);
        
//echo $org_rec['su']." / ($FULL_TIME * ".$rtn['worker']." * ".$rtn['success_rate']."<BR>";

                # 加入資料庫
    $q_str = "INSERT INTO schedule (line_id, ord_num,p_id,des,su,qty,ets,etf,rel_ets,rel_etf,fty,open_date,open_user) 
                        VALUES ('".$parm['new_line']."','"
                                            .$parm['ord_num']."','"
                                            .$parm['p_id']."','"
                                            .$parm['des']."','"
                                            .$org_rec['su']."','"
                                            .$org_rec['qty']."','"
                                            .$rtn['ets']."','"
                                            .$rtn['new_etf']."','"
                                            .$rtn['ets']."','"
                                            .$rtn['new_etf']."','"
                                            .$parm['fty']."','"
                                            .date('Y-m-d')."','"
                                            .$GLOBALS['SCACHE']['ADMIN']['login_id']."')";


        if (!$q_result = $sql->query($q_str)) {
            $this->msg->add("Error ! 無法新增資料記錄.");
            $this->msg->merge($sql->msg);
            return false;    
        }

        $new_id = $sql->insert_id();  //取出 新的 id
        
    $new_line = $parm['new_line'];
    $new_ets = $rtn['ets'];
//刪除原排產

        $this->del_schedule($parm['org_id'],0,$parm['ord_num'],$parm['p_id']);
    
//重算生產線目前訂單後其他訂單排產 start		
    $oth_sch = array();
    $new_etf = increceDaysInDate($new_etf,1);
    $q_str = "SELECT schedule.* FROM schedule							
                        WHERE line_id ='".$new_line."' AND rel_ets >= '".$new_ets."' AND id <> '".$new_id."'
                      ORDER BY rel_ets";	
                 
    $q_result = $sql->query($q_str);
    while($row = $sql->fetch($q_result)) 
    {
        $oth_sch[] = $row; 
    }
    if(sizeof($oth_sch) > 0)
    {
        for($i=0; $i<sizeof($oth_sch); $i++)  //從排插單後訂單
        {

            $period_dif = countDays ($oth_sch[$i]['rel_ets'],$new_etf);   //計算插單後需要移動的日期數
                $oth_sch[$i]['new_ets'] = increceDaysInDate($oth_sch[$i]['rel_ets'],$period_dif);

              $sp_period = $oth_sch[$i]['su'] /( $FULL_TIME * $rtn['worker'] *$rtn['success_rate']);
                $tmp = explode('.',$sp_period);
                if(isset($tmp[1]) && $tmp[1])$sp_period++;
                if ( $sp_period <= 1) $sp_period = 2;
              $oth_sch[$i]['new_etf'] = $this->increceDay($oth_sch[$i]['new_ets'],$sp_period,$parm['fty']);			
                $q_str =  "UPDATE schedule SET rel_etf='".$oth_sch[$i]['new_etf']."', rel_ets='".$oth_sch[$i]['new_ets']."', etf='".$oth_sch[$i]['new_etf']."', ets='".$oth_sch[$i]['new_ets']."'  WHERE id = '".$oth_sch[$i]['id']."'";
                $q_result = $sql->query($q_str);
//重加入訂單ETS ETF
                $this->add_ord_schedule($oth_sch[$i]['p_id']);	
                if($this->check_sch_finish($oth_sch[$i]['p_id']))$this->add_capacity($oth_sch[$i]['p_id']);
                $new_etf =  increceDaysInDate($oth_sch[$i]['new_etf'],1);
         
      }
    }
//重算生產線目前訂單後其他訂單排產 end		

//組合訂單
$this->group_schedule($new_id);
    
    return $new_id;
} // end func	




#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->del_schedule($id) 產除生產線排產 // Mode 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#M11022502 修改 REVISE (Partial)
function del_schedule($id,$qty='',$ord_num,$p_id='') {

    $sql = $this->sql;
    $FULL_TIME = $GLOBALS['FULL_TIME'];
    
    $today = date('Y-m-d');
    $rtn = array();

    $q_str = "SELECT s_order.order_num, order_partial.p_su, order_partial.ext_su  , status, 
                                     mat_eta, m_acc_eta, style,
                                     order_partial.ext_period, s_order.ie1, s_order.apv_date
                        FROM   s_order,pdtion, order_partial
                        WHERE  s_order.order_num = pdtion.order_num AND s_order.order_num = order_partial.ord_num AND
                                     s_order.order_num ='".$ord_num."' AND order_partial.id='".$p_id."'
                        ORDER BY pdtion.mat_eta, m_acc_eta";

    $q_result = $sql->query($q_str);
    $ord_rec = $sql->fetch($q_result);
    $ord_sch_date = $ord_rec['apv_date'];
    
    $del_su = NUMBER_FORMAT(($qty * $ord_rec['ie1']),'0','.','');

    $q_str = "SELECT pdt_saw_line.*, schedule.rel_etf, schedule.rel_ets, schedule.ets , 
                                     schedule.id as sch_id, schedule.su, schedule.qty, schedule.pdt_qty
                        FROM pdt_saw_line, schedule
                        WHERE pdt_saw_line.id = schedule.line_id  AND schedule.id ='".$id."'"	;
							
    $q_result = $sql->query($q_str);	
    $row = $sql->fetch($q_result);

    $row['su'] -= $del_su;
    $row['qty'] -= $qty;
    $row['rem_qty'] = $row['qty'] - $qty - $row['pdt_qty'];
    $line_sc = $row['sc'];
    $new_rel_ets = $row['rel_ets'];
    $line_id = $row['id'];
    $fty = $row['fty'];
    $org_rtn = $row;
    
    if($row['su'] > 0 && $del_su){
				
        if($row['success_rate'] == 0)$row['success_rate'] = 1;
        $row['sp_period'] = ($row['rem_qty'] * $ord_rec['ie1']) /( $FULL_TIME * $row['worker'] * $row['success_rate']);

        $tmp = explode('.',$row['sp_period']);
        if(isset($tmp[1]) && $tmp[1])$row['sp_period'] = $tmp[0]+1;
        if($row['sp_period'] <= 1) $row['sp_period'] = 2;

        $row['new_rel_etf'] = $row['new_etf'] = $this->increceDay(date('Y-m-d'),$row['sp_period'],$fty);	
                
        //if($row['rel_ets'] <> '0000-00-00')$row['new_rel_etf'] = $this->increceDay($row['rel_ets'],$row['sp_period']);		
        
        $q_str = "UPDATE schedule SET 
                        su='".$row['su']."',
                        qty='".$row['qty']."',
                        etf='".$row['new_etf']."',
                        rel_etf='".$row['new_rel_etf']."'
                        WHERE id='".$id."'";
        //echo $q_str."<BR>";
        $q_result = $sql->query($q_str);
        //	$new_rel_ets = increceDaysInDate($row['new_rel_etf'],1);
    } else {
        $q_str = "DELETE FROM schedule WHERE id='".$id."'";		

        if (!$q_result = $sql->query($q_str)) {
            $this->msg->add("Error ! 無法刪除資料記錄.");
            $this->msg->merge($sql->msg);
            return false;
        }
    }			

    //重算生產線目前訂單後其他訂單排產 start		
    $oth_sch = array();		
    $q_str = "SELECT schedule.* FROM schedule							
                        WHERE line_id ='".$line_id."' AND rel_ets >= '".$new_rel_ets."' 
                      ORDER BY rel_ets";	
    //echo $q_str."<BR>";
    $q_result = $sql->query($q_str);
    while($row = $sql->fetch($q_result)) {
        $oth_sch[] = $row; 
    }
    
    if(sizeof($oth_sch) > 0 && $line_sc == 0) {
        //從排插單後訂單
        for($i=0; $i<sizeof($oth_sch); $i++) {

            $period_dif = countDays ($oth_sch[$i]['rel_ets'],$new_rel_ets);   //計算插單後需要移動的日期數
            $oth_sch[$i]['new_ets'] = increceDaysInDate($oth_sch[$i]['rel_ets'],$period_dif); # 得到新的日期 = ( 指定日期 , 加幾天 )

            $sp_period = $oth_sch[$i]['su'] / ( $FULL_TIME * $org_rtn['worker'] * $org_rtn['success_rate'] );
            $tmp = explode('.',$sp_period);
            if(isset($tmp[1]) && $tmp[1])$sp_period++;
            if ( $sp_period <= 1) $sp_period = 2;
            $oth_sch[$i]['new_etf'] = $this->increceDay($oth_sch[$i]['new_ets'],$sp_period,$fty);			

            $q_str =  "UPDATE schedule SET rel_etf='".$oth_sch[$i]['new_etf']."', rel_ets='".$oth_sch[$i]['new_ets']."', etf='".$oth_sch[$i]['new_etf']."', ets='".$oth_sch[$i]['new_ets']."'  WHERE id = '".$oth_sch[$i]['id']."'";
            $q_result = $sql->query($q_str);
            //重加入訂單ETS ETF
            $this->add_ord_schedule($oth_sch[$i]['p_id']);
            if($this->check_sch_finish($oth_sch[$i]['p_id']))$this->add_capacity($oth_sch[$i]['p_id']);
            $new_rel_ets =  increceDaysInDate($oth_sch[$i]['new_etf'],1);
	  	 
        }
    }
    
    //重算生產線目前訂單後其他訂單排產 end			

    return 1;
} // end func	





#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->del_schedule_qty($id) 產除排產 // Mode 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function del_schedule_qty($sid,$ie='',$sch_qty='',$del_qty='',$p_id='') {

    $sql = $this->sql;
    
    $status = '';
    if( $sch_qty == $del_qty ){
        $q_str = "DELETE FROM `schedule` WHERE `id` = '".$sid."'";
        $status = 'DELETE';
    } else {
        $qty = $sch_qty - $del_qty ;
        $su = $ie * $qty ;
        $q_str = "UPDATE `schedule` SET `su` ='".$su."' , `qty` = '".$qty."' WHERE `id` = '".$sid."'";
        $status = 'UPDATE';
    }
    // echo $q_str.'<br>';
    $q_result = $sql->query($q_str);  
    
    $GLOBALS['order']->mdf_partial_id_ie($p_id,$ie);
    
    return $status;
} // end func	



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add_ord_schedule($ord_num) 將排產資料加入訂單
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_ord_schedule($id) {

		$sql = $this->sql;
		
		$today= date('Y-m-d');
		$rtn = array();

		$q_str = "SELECT * FROM order_partial WHERE id=".$id;
		$q_result = $sql->query($q_str);			
		$ptl_rec = $sql->fetch($q_result);
			
		$q_str = "SELECT max(rel_etf) as etf, min(rel_ets) as ets, sum(su) as su, sum(qty) as qty, ord_num	
							FROM   schedule
							WHERE  schedule.p_id ='".$id."'
							GROUP BY ord_num"	;

			$q_result = $sql->query($q_str);	
			if($row = $sql->fetch($q_result))
			{
			//訂單加入ets,etf
				$q_str = "UPDATE order_partial SET p_ets ='".$row['ets']."', p_etf ='".$row['etf']."' WHERE id='".$id."'";
				$q_result = $sql->query($q_str);

			//訂單加入己排產數量
				$q_str = "UPDATE order_partial SET ext_su ='".$row['su']."', ext_qty ='".$row['qty']."' WHERE id='".$id."'";
				$q_result = $sql->query($q_str);

			//訂單加入預計生產所需日數
				$day_range = dayCount($row['ets'],$row['etf']);
				$q_str = "UPDATE order_partial SET ext_period ='".$day_range."'  WHERE id='".$id."'";
				$q_result = $sql->query($q_str);

			}else{
			//訂單加入ets,etf 這因該是不會發生的修改
				$q_str = "UPDATE order_partial SET p_ets = NULL, p_etf = NULL, ext_su ='0', ext_period ='0', ext_qty = 0, p_fty_su = '' WHERE id='".$id."'";

				$q_result = $sql->query($q_str);			

			}



//紀錄 sub_con 分別判斷 ( sub_con , in house ) 是否有外發和本廠製造
		$q_str = "SELECT schedule.id
							FROM   schedule, pdt_saw_line
							WHERE  schedule.line_id = pdt_saw_line.id AND schedule.ord_num ='".$ptl_rec['ord_num']."' AND
										 pdt_saw_line.sc = 1
							GROUP BY ord_num"	;
		$q_result = $sql->query($q_str);	
		if($row = $sql->fetch($q_result))
		{
				$q_str = "UPDATE pdtion SET sub_con = 1 WHERE order_num='".$ptl_rec['ord_num']."'";
//				$q_result = $sql->query($q_str);				
				$sub_con = 1;
		}else{
				$q_str = "UPDATE pdtion SET sub_con = 0 WHERE order_num='".$ptl_rec['ord_num']."'";
//				$q_result = $sql->query($q_str);	
				$sub_con = 0;					
		}



//紀錄in house	
		$q_str = "SELECT schedule.id
							FROM   schedule, pdt_saw_line
							WHERE  schedule.line_id = pdt_saw_line.id AND schedule.ord_num ='".$ptl_rec['ord_num']."' AND
										 pdt_saw_line.sc = 0
							GROUP BY ord_num"	;

		$q_result = $sql->query($q_str);	
		if($row = $sql->fetch($q_result))
		{
				$in_house = 1;
		}else{
				$in_house = 0;			
		}
		
		$q_str = "UPDATE pdtion SET in_house ='".$in_house."', sub_con = '".$sub_con."' WHERE order_num='".$ptl_rec['ord_num']."'";
		$q_result = $sql->query($q_str);			
		



		return 1;
	} // end func	


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->contractor_schedule($parm) 外發排產
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function contractor_schedule($parm) {

		$sql = $this->sql;
		
		$today= date('Y-m-d');
		$rtn = array();


		$q_str = "SELECT s_order.order_num, order_partial.p_su as su, order_partial.ext_su  , status, 
										 mat_eta, m_acc_eta, style,	order_partial.ext_period, s_order.ie1, s_order.qty
							FROM   s_order,pdtion, order_partial
							WHERE  s_order.order_num = pdtion.order_num AND s_order.order_num = order_partial.ord_num AND
										 s_order.order_num ='".$parm['ord']."' AND order_partial.id = '".$parm['p_id']."'
							ORDER BY pdtion.mat_eta, m_acc_eta";

		$q_result = $sql->query($q_str);
		$ord_rec = $sql->fetch($q_result);
		

		$su = NUMBER_FORMAT(($parm['qty'] * $ord_rec['ie1']),'0','.','');

					# 加入資料庫
		$q_str = "INSERT INTO schedule (line_id, ord_num, p_id, des,su,qty,ets,etf,rel_ets,rel_etf,fty,open_date,open_user) 
							VALUES ('".$parm['line_id']."','"
												.$parm['ord']."','"
												.$parm['p_id']."','"
												.$parm['des']."','"
												.$su."','"
												.$parm['qty']."','"
												.$parm['ets']."','"
												.$parm['etf']."','"
												.$parm['ets']."','"
												.$parm['etf']."','"
												.$parm['fty']."','"
												.date('Y-m-d')."','"
												.$GLOBALS['SCACHE']['ADMIN']['login_id']."')";

			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! 無法新增資料記錄.");
				$this->msg->merge($sql->msg);
				return false;    
			}

		$new_id = $sql->insert_id();  //取出 新的 id

		return $new_id;
	} // end func	

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		更新 季節類別資料內 某個單一欄位
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function update_field($parm) {

    $sql = $this->sql;
    ############### 檢查輸入項目
    
    #####   更新資料庫內容
    $q_str = "UPDATE schedule SET ".$parm['field_name']."='".$parm['field_value']."'  WHERE id='".$parm['id']."'";

    if (!$q_result = $sql->query($q_str)) {
        $this->msg->add("Error ! 無法更新資料庫內容.");
        $this->msg->merge($sql->msg);
        return false;    
    }
    
    return true;
} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->contractor_schedule($parm) 外發排產
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function check_sch_finish($id) {

		$sql = $this->sql;
		
		$today= date('Y-m-d');
		$rtn = array();


		$q_str = "SELECT ext_qty, p_qty as qty
							FROM   order_partial 
							WHERE  id ='".$id."'";

		$q_result = $sql->query($q_str);
		$ord_rec = $sql->fetch($q_result);
		
		if($ord_rec['qty'] > $ord_rec['ext_qty']) return false;
		



		return 1;
	} // end func	


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add_capacity($parm) 將排產capacity寫入記錄中
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_capacity($id) {

		$sql = $this->sql;
		
		$today= date('Y-m-d');
		$rtn = array();

		$q_str = "SELECT *	FROM   order_partial	WHERE  id ='".$id."'";
		$q_result = $sql->query($q_str);
		$ptl_rec = $sql->fetch($q_result);

		$q_str = "SELECT s_order.*, pdtion.etf, pdtion.ets, pdtion.id as pd_id, fty_su
							FROM   s_order,pdtion 
							WHERE  s_order.order_num = pdtion.order_num AND
										 s_order.order_num ='".$ptl_rec['ord_num']."'";

		$q_result = $sql->query($q_str);
		$ord_rec = $sql->fetch($q_result);


	  $div = $GLOBALS['order']->distri_month_su($ptl_rec['p_su'],$ptl_rec['p_ets'],$ptl_rec['p_etf'],$ord_rec['factory'],'schedule',0,0); //重算每月SU並儲存
		$GLOBALS['order']->update_partial('p_fty_su', $div, $id);		


		$q_str = "SELECT id	FROM   order_partial	WHERE  ord_num ='".$ptl_rec['ord_num']."' AND ext_qty < p_qty";
	
		$q_result = $sql->query($q_str);
		if($row = $sql->fetch($q_result)) return false;
		
		
		if($ord_rec['status'] >= 7) //刪掉過去的資料
		{
//			$GLOBALS['order']->delete_month_su( $ord_rec['ets'], $ord_rec['etf'],$ord_rec['su'],$ord_rec['factory'],'schedule');
			$fty_su = explode(',',$ord_rec['fty_su']);
			for ($i=0; $i<sizeof($fty_su); $i++)
			{
				$yy = substr($fty_su[$i],0,4);
				$mm = substr($fty_su[$i],4,2);
				$mm_su = substr($fty_su[$i],6);
				$F = $GLOBALS['capaci']->delete_su($ord_rec['factory'], $yy, $mm,'schedule', $mm_su);
			}
			
			$GLOBALS['order']->del_cfm_pd_schedule($id);
			// $GLOBALS['order']->del_cfm_pd_schedule($ptl_rec['ord_num']);
			// if(!$GLOBALS['order']->del_cfm_pd_schedule($ptl_rec['ord_num'])){
				// $pdt_ord = $GLOBALS['order']->schedule_get(0,$ptl_rec['ord_num']);
				// $GLOBALS['order']->del_cfm_pd_schedule($pdt_ord['id']);
			// }
		}	
		
		
		//取得訂單ETS,ETF
		$q_str = "SELECT max(p_etf) as etf, min(p_ets) as ets 	FROM order_partial	WHERE  ord_num ='".$ptl_rec['ord_num']."'";
		$q_result = $sql->query($q_str);
		$row = $sql->fetch($q_result);


		$parm = array(	"pd_id"		=>  $ord_rec['pd_id'],
										"ets"			=>  $row['ets'],
										"etf"			=>  $row['etf'],									
									);
		$parm['fty_su'] = $GLOBALS['order']->update_distri_su($ptl_rec['ord_num'],'p_fty_su','fty_su'); //取得fty_su csv
		$f1 = $GLOBALS['order']->add_cfm_pd_schedule($parm);   // 更新資料庫

		$fty_su = explode(',',$parm['fty_su']);
		for ($i=0; $i<sizeof($fty_su); $i++) //加入capacity
		{
			$yy = substr($fty_su[$i],0,4);
			$mm = substr($fty_su[$i],4,2);
			$mm_su = substr($fty_su[$i],6);
			$F = $GLOBALS['capaci']->update_su($ord_rec['factory'], $yy, $mm,'schedule', $mm_su);
		}


		if($ord_rec['status'] < 7)$ord_rec['status'] = 7;

		$argv = array(	"id"				=>  $ord_rec['id'],
										"status"		=>  $ord_rec['status'],
										"schd_er"		=>	$GLOBALS['SCACHE']['ADMIN']['name'],
										"schd_date"	=>	date('Y-m-d')
									);
		$A1 = $GLOBALS['order']->update_sorder_4_cfm_pd_schedule($argv);   // 更新 訂單狀況記錄  status =>7

		return true;
	} // end func	



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add_capacity($parm) 將排產capacity寫入記錄中
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del_capacity($ord_num) {

		$sql = $this->sql;
		
		$today= date('Y-m-d');
		$rtn = array();


		$q_str = "SELECT s_order.*, pdtion.etf, pdtion.ets, pdtion.id as pd_id, fty_su
							FROM   s_order,pdtion 
							WHERE  s_order.order_num = pdtion.order_num AND
										 s_order.order_num ='".$ord_num."'";

		$q_result = $sql->query($q_str);
		$ord_rec = $sql->fetch($q_result);
		
		
		$q_str = "SELECT sc FROM schedule, pdt_saw_line 
							WHERE  schedule.line_id = pdt_saw_line.id AND ord_num ='".$ord_num."' AND sc = 0";

		$q_result = $sql->query($q_str);
		if($row = $sql->fetch($q_result))
		{
			$sc = 0;
		}else{
			$sc = 1;
		}		
		
		if($ord_rec['status'] >= 7)
		{
//			$GLOBALS['order']->delete_month_su( $ord_rec['ets'], $ord_rec['etf'],$ord_rec['su'],$ord_rec['factory'],'schedule');
			$fty_su = explode(',',$ord_rec['fty_su']);
			for ($i=0; $i<sizeof($fty_su); $i++)
			{
				$yy = substr($fty_su[$i],0,4);
				$mm = substr($fty_su[$i],4,2);
				$mm_su = substr($fty_su[$i],6);
				$F = $GLOBALS['capaci']->delete_su($ord_rec['factory'], $yy, $mm,'schedule', $mm_su);
			}
			

			$GLOBALS['order']->del_cfm_pd_schedule($ord_rec['pd_id']);			
		}	
		$status = 4;
		if($ord_rec['etf'] && $ord_rec['etf'] > '0000-00-00') $status = 6;
		$argv = array(	"id"				=>  $ord_rec['id'],
										"status"		=>  $status,
										"schd_er"		=>	$GLOBALS['SCACHE']['ADMIN']['name'],
										"schd_date"	=>	date('Y-m-d')
									);

		$A1 = $GLOBALS['order']->update_sorder_4_cfm_pd_schedule($argv);   // 更新 訂單狀況記錄  status =>7



		return 1;
	} // end func	

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->contractor_schedule($parm) 外發排產
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_ln_det($id) {

		$sql = $this->sql;
		
		$today= date('Y-m-d');
		$rtn = array();


		$q_str = "SELECT schedule.*, pdt_saw_line.line FROM schedule, pdt_saw_line 
							WHERE  schedule.line_id = pdt_saw_line.id AND schedule.id = $id";

		$q_result = $sql->query($q_str);
		if($row = $sql->fetch($q_result))
		{
			return $row;
		}		

		return false;
	} // end func	



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->reload_schedule($parm) 外發排產
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function reload_schedule($line_id, $p_id, $sch_id='') {

    $sql = $this->sql;
    $FULL_TIME = $GLOBALS['FULL_TIME'];
    
    $sch_finish_rate = $GLOBALS['sch_finish_rate'];
    
    $today= date('Y-m-d');
    $rtn = array();
    $q_str = "SELECT pdt_saw_line.*  FROM pdt_saw_line
                        WHERE  pdt_saw_line.id ='".$line_id."' AND sc = 0";
    // echo $q_str.'<br>';
    $q_result = $sql->query($q_str);
    if(!$line_det = $sql->fetch($q_result)) return false;
    
    if($sch_id == '')
    {
        // 抓未finish且rel_ets最前面的單
        $q_str = "SELECT schedule.*  FROM schedule
                            WHERE  schedule.line_id ='".$line_id."' AND p_id='".$p_id."' AND pdt_qty < qty AND status = 0
                          ORDER BY rel_ets";
        // echo $q_str.'<br>';
        $q_result = $sql->query($q_str);
        if(!$ord_sch = $sql->fetch($q_result)) return false;
        if($ord_sch['status'] > 0)	return false;
        // 搜尋已達該 partial 的排產數量
        $q_str = "SELECT schedule.*, ie1  FROM schedule, s_order
                            WHERE  s_order.order_num = schedule.ord_num AND schedule.line_id ='".$line_id."' AND 
                                            p_id='".$p_id."' AND ( schedule.pdt_qty >= schedule.qty OR schedule.status = 2 )";
        // echo $q_str.'<br>';
        $q_result = $sql->query($q_str);
        $oth_sch_su = $oth_sch_qty = 0;
        while($rows = $sql->fetch($q_result))
        {
            $oth_sch_su += ($rows['pdt_qty'] * $rows['ie1']);
            $oth_sch_qty += $rows['pdt_qty'];
        }
    
        // echo 'oth_sch_su='.$oth_sch_su.'<br>';
        // echo 'oth_sch_qty='.$oth_sch_qty.'<br>';
        $q_str = "SELECT sum(qty) as qty, sum(su) as su  FROM saw_out_put 						  
                            WHERE  saw_out_put.line_id ='".$line_id."' AND p_id='".$p_id."'
                            GROUP BY line_id, ord_num";				
        // echo $q_str.'<br>';
        $q_result = $sql->query($q_str);
        if(!$pdt = $sql->fetch($q_result)) return false;

        // 計算該線該單的總產出數量 / 計算出生產後的預計完成日 start
        $pdt['su'] = $pdt['su'] - $oth_sch_su;
        $pdt['qty'] = $pdt['qty'] - $oth_sch_qty;

    } else {
        $q_str = "SELECT schedule.*  FROM schedule	WHERE id = '".$sch_id."'";
        // echo $q_str.'<br>';
        $q_result = $sql->query($q_str);
        if(!$ord_sch = $sql->fetch($q_result)) return false;
        $pdt['qty'] = $ord_sch['pdt_qty'];
    }

    // echo $pdt['qty'].' / '.$ord_sch['qty'].' >= $sch_finish_rate && '.$sch_id.'<br>';
    if(( $pdt['qty'] / $ord_sch['qty']) >= $sch_finish_rate && $sch_id == '') {
        $q_str =  "UPDATE schedule SET status = '2' WHERE id = '".$ord_sch['id']."'";
        // echo $q_str.'<br>';
        $q_result = $sql->query($q_str);
        $now_etf = date('Y-m-d');
        if($now_etf < $ord_sch['rel_ets'])$now_etf = $ord_sch['rel_ets'];
        $period_dif = countDays ($ord_sch['rel_etf'],$now_etf);
    } else if($sch_id == '') {
        $ext_period =  ( $ord_sch['su'] - $pdt['su'] ) / ( $FULL_TIME * $line_det['worker'] * $line_det['success_rate'] );

        $tmp = explode($ext_period,'.');
        if(isset($tmp[1]) && $tmp[1]) $ext_period++;     
        if($ext_period < 0)$ext_period = 0;
        $rld_ets = $ord_sch['rel_ets'];
        if(date('Y-m-d') > $rld_ets)$rld_ets = date('Y-m-d');
        
        // $now_etf = increceDaysInDate($rld_ets,$ext_period);		
        $now_etf = $this->increceDay($rld_ets,$ext_period,$line_det['fty']);		
        
        if(countDays ($ord_sch['rel_ets'],$now_etf) <= 0)$now_etf = increceDaysInDate($ord_sch['rel_ets'],2);			
        $period_dif = countDays ($ord_sch['rel_etf'],$now_etf);   //原預計完成日與目前預計完成日差
    } else {
        $now_etf = date('Y-m-d');
    }
        
    //寫入生產後的預計完成日 end
    $q_str =  "UPDATE schedule SET rel_etf='".$now_etf."', pdt_qty = '".$pdt['qty']."' WHERE id = '".$ord_sch['id']."'";
    // echo $q_str.'<br>';
    $q_result = $sql->query($q_str);
    //$this->add_ord_schedule($ord_num);
        
    //重算生產線目前訂單後其他訂單排產 start
    $new_etf = increceDaysInDate($now_etf,1);
    $oth_sch = array();
    $q_str = "SELECT schedule.*  FROM schedule
                            WHERE  schedule.line_id = '".$line_id."' AND rel_ets > '".$ord_sch['rel_ets']."' ORDER BY rel_ets";
    // echo $q_str.'<br>';
    $q_result = $sql->query($q_str);
    $i = 0;
    while($row = $sql->fetch($q_result)) {
        $period_dif = countDays ($row['rel_ets'],$new_etf);
        $row['now_ets'] = increceDaysInDate($row['rel_ets'],$period_dif);

        if($i < 6) {
            $sp_period = $row['su'] / ( $FULL_TIME * $line_det['worker']*$line_det['success_rate'] );

            $tmp = explode('.',$sp_period);
            if(isset($tmp[1]) && $tmp[1])$sp_period++;
            if ( $sp_period <= 1) $sp_period = 2;
            $row['now_etf'] = $this->increceDay($row['now_ets'],$sp_period,$line_det['fty']);			
        } else {
            $row['now_etf'] = $this->increceSchdDay($row['rel_ets'],$row['rel_etf'],$row['now_ets'],$row['fty']);
        }

        $i++;

        // $row['now_etf'] = increceDaysInDate($row['rel_etf'],$period_dif);
        // $row['now_etf'] = $this->increceSchdDay($row['rel_ets'],$row['rel_etf'],$row['now_ets']);
        $oth_sch[] = $row; 
        $new_etf =  increceDaysInDate($row['now_etf'],1);
    }
    
    for($i=0; $i<sizeof($oth_sch); $i++) {
        $q_str =  "UPDATE schedule SET rel_etf='".$oth_sch[$i]['now_etf']."', rel_ets='".$oth_sch[$i]['now_ets']."' WHERE id = '".$oth_sch[$i]['id']."'";
        // echo $q_str.'<br>';
        $q_result = $sql->query($q_str);
        //	$this->add_ord_schedule($oth_sch[$i]['ord_num']);
        //			
        //	if($this->check_sch_finish($oth_sch[$i]['ord_num']))
        // {
        //		$this->add_capacity($oth_sch[$i]['ord_num']);
        // }		
    }
    
    // 重算生產線目前訂單後其他訂單排產 end		
    // 現在生產中訂單的ETS前移
    if( $sch_id == '' && $ord_sch['rel_ets'] > $today ) {
        $etf = increceDaysInDate($ord_sch['rel_ets'],-1);
        $new_etf = increceDaysInDate($today,-1);
        $q_str = "SELECT id, pdt_qty FROM schedule 
                        WHERE rel_etf = '".$etf."'  AND line_id = '".$ord_sch['line_id']."'
                        ORDER BY rel_etf DESC LIMIT 1";

        // echo $q_str;
        $q_result = $sql->query($q_str);
        if(!$bfr_sch = $sql->fetch($q_result)) return false;
        if( $bfr_sch['pdt_qty'] == 0)return false;
        $q_str =  "UPDATE schedule SET rel_etf='".$new_etf."' WHERE id = '".$bfr_sch['id']."'";
        // echo $q_str.'<br>';
        $q_result = $sql->query($q_str);
        $q_str =  "UPDATE schedule SET rel_ets='".$today."'WHERE id = '".$ord_sch['id']."'";
        // echo $q_str.'<br>';
        $q_result = $sql->query($q_str);
    }
    
} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->reload_schedule_finish($ord_num)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function reload_schedule_finish($p_id) {

    $sql = $this->sql;
    
    $today= date('Y-m-d');
    $rtn = array();

    $q_str = "SELECT schedule.*  FROM schedule
              WHERE  p_id='".$p_id."' AND rel_etf > '".$today."' AND status < 2";

    $q_result = $sql->query($q_str);
    while($row = $sql->fetch($q_result)) {
        $ord_sch[] = $row;
    }
    
    if(!isset($ord_sch)) return false;

    for($i=0; $i<sizeof($ord_sch); $i++) {
    
        $now_etf = date('Y-m-d');
        if($now_etf < $ord_sch[$i]['rel_ets'])$now_etf = $ord_sch[$i]['rel_ets'];
        $period_dif = countDays ($ord_sch[$i]['rel_etf'],$now_etf);

        //計算出生產後的預計完成日 end
        $q_str =  "UPDATE schedule SET rel_etf='".$now_etf."', status = 2 WHERE id = '".$ord_sch[$i]['id']."'";  

        $q_result = $sql->query($q_str);
        //$this->add_ord_schedule($ord_num);			

        //重算生產線目前訂單後其他訂單排產 start
        $new_etf = increceDaysInDate($now_etf,1);
        $oth_sch = array();
    
        $q_str = "SELECT schedule.*  FROM schedule
                    WHERE  schedule.line_id = '".$ord_sch[$i]['line_id']."' AND 
                    rel_ets > '".$ord_sch[$i]['rel_etf']."' ORDER BY rel_ets";

        $q_result = $sql->query($q_str);
        while($row = $sql->fetch($q_result)) {		
            $period_dif = countDays ($row['rel_ets'],$new_etf);
            $row['now_ets'] = increceDaysInDate($row['rel_ets'],$period_dif);
            $row['now_etf'] = $this->increceSchdDay($row['rel_ets'],$row['rel_etf'],$row['now_ets'],$row['fty']);
            $oth_sch[] = $row; 
            $new_etf =  increceDaysInDate($row['now_etf'],1);
        }
        
        for($j=0; $j<sizeof($oth_sch); $j++) {
            $q_str =  "UPDATE schedule SET rel_etf='".$oth_sch[$j]['now_etf']."', rel_ets='".$oth_sch[$j]['now_ets']."' WHERE id = '".$oth_sch[$j]['id']."'";

            $q_result = $sql->query($q_str);
            //$this->add_ord_schedule($oth_sch[$j]['ord_num']);
        }
        //重算生產線目前訂單後其他訂單排產 end

    }

} // end func	



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->reload_schedule($parm) 外發排產
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function reload_del_schedule($line_id, $p_id,$qty,$ie,$from='pdtion') {

	$sql = $this->sql;
    
    $sch_finish_rate = $GLOBALS['sch_finish_rate'];
    $FULL_TIME = $GLOBALS['FULL_TIME'];
	
	$today= date('Y-m-d');
	$rtn = array();

	$q_str = "SELECT pdt_saw_line.*  FROM pdt_saw_line
						WHERE  pdt_saw_line.id ='".$line_id."'";
	$q_result = $sql->query($q_str);
	if(!$line_det = $sql->fetch($q_result)) return false;

	$q_str = "SELECT schedule.*  FROM schedule
						WHERE  schedule.line_id ='".$line_id."' AND p_id='".$p_id."' AND pdt_qty >=".$qty."
						ORDER BY id DESC";
	$q_result = $sql->query($q_str);
	if(!$ord_sch = $sql->fetch($q_result)) return false;
	$pdt_qty = $ord_sch['pdt_qty'] - $qty;

	// 計算出生產後的預計完成日 start
	$pdt['qty'] = $pdt_qty;
	$pdt['su']  = $pdt['qty'] * $ie;
	
	if( ( $pdt['qty'] / $ord_sch['qty']) >= $sch_finish_rate )
	{
		return true;
	} else {
		$ext_period = ( $ord_sch['su'] - $pdt['su'] ) / ( $FULL_TIME * $line_det['worker'] * $line_det['success_rate'] );
		$tmp = explode($ext_period,'.');
		if(isset($tmp[1]) && $tmp[1])$ext_period++;     
		if($ext_period < 0)$ext_period = 0;
		$rld_ets = $ord_sch['rel_ets'];
		if(date('Y-m-d') > $rld_ets)$rld_ets = date('Y-m-d');
		$now_etf = $this->increceDay($rld_ets,$ext_period,$line_det['fty']);
	}		

	if($now_etf < $ord_sch['rel_ets'])$now_etf = increceDaysInDate($ord_sch['rel_ets'],2);
	$period_dif = countDays ($ord_sch['rel_etf'],$now_etf);   //原預計完成日與目前預計完成日差
	
	// 計算出生產後的預計完成日 end
	$q_str =  "UPDATE schedule SET rel_etf='".$now_etf."', pdt_qty = '".$pdt['qty']."', status = 0 WHERE id = '".$ord_sch['id']."'";
	$q_result = $sql->query($q_str);
	
	// 重算生產線目前訂單後其他訂單排產 start
	$new_etf = increceDaysInDate($now_etf,1);
	$oth_sch = array();
	$q_str = "SELECT schedule.*  FROM schedule
						WHERE  schedule.line_id = '".$line_id."' AND rel_ets > '".$ord_sch['rel_etf']."' 
						ORDER BY rel_etf";
	
	$q_result = $sql->query($q_str);
	while($row = $sql->fetch($q_result)) 
	{
		$period_dif = countDays ($row['rel_ets'],$new_etf);
		$row['now_ets'] = increceDaysInDate($row['rel_ets'],$period_dif);
		$row['now_etf'] = $this->increceSchdDay($row['rel_ets'],$row['rel_etf'],$row['now_ets'],$row['fty']);
		$oth_sch[] = $row; 
		$new_etf =  increceDaysInDate($row['now_etf'],1);			
	}
	
	for($i=0; $i<sizeof($oth_sch); $i++)
	{
		$q_str =  "UPDATE schedule SET rel_etf='".$oth_sch[$i]['now_etf']."', rel_ets='".$oth_sch[$i]['now_ets']."' WHERE id = '".$oth_sch[$i]['id']."'";
		$q_result = $sql->query($q_str);
	}
	
	// 重算生產線目前訂單後其他訂單排產 end		
	// 重整開始生產日
	
	if($from=='pdtion' && $ord_sch['rel_ets'] == $today)
	{					
		$etf = increceDaysInDate($today,-1);
		$new_ets = increceDaysInDate($today,1);
		$q_str = "SELECT id FROM schedule 
							WHERE rel_etf = '".$etf."' AND line_id = '".$ord_sch['line_id']."'
							ORDER BY rel_etf DESC LIMIT 1";							
		$q_result = $sql->query($q_str);
		if(!$bfr_sch = $sql->fetch($q_result)) return false;
		$q_str =  "UPDATE schedule SET rel_etf='".$today."' WHERE id = '".$bfr_sch['id']."'";
		$q_result = $sql->query($q_str);
		$q_str =  "UPDATE schedule SET rel_ets='".$new_ets."'WHERE id = '".$ord_sch['id']."'";
		$q_result = $sql->query($q_str);
	}	
	
} // end func	



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->order_get($ord_num) 取得訂單所有排產資料 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function order_get($ord_num) {

		$sql = $this->sql;
		
		$today= date('Y-m-d');
		$rtn = array();


		$q_str = "SELECT schedule.*, pdt_saw_line.line as line_name, pdt_saw_line.style, pdt_saw_line.worker ,order_partial.mks,order_partial.remark,order_partial.p_qty,order_partial.p_etd as etd
							FROM pdt_saw_line , schedule left join order_partial on (schedule.p_id=order_partial.id)
							WHERE  schedule.line_id = pdt_saw_line.id AND schedule.ord_num='".$ord_num."' Order By order_partial.mks";

		$q_result = $sql->query($q_str);
		while($ord_sch = $sql->fetch($q_result))
		{
			$ord_sch['out_qty'] = $ord_sch['out_su'] = 0;
			$rtn[] = $ord_sch;
		}
	
		return $rtn;
	
		
	} // end func	


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->order_get($ord_num) 取得訂單所有排產資料
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_etf($fty,$line) {

		$sql = $this->sql;
		
		$today= date('Y-m-d');
		
		$rtn = array();


		$q_str = "SELECT schedule.rel_etf  FROM schedule
							WHERE  fty='".$fty."' AND line_id = '".$line."' AND rel_etf >='".$today."' 
							ORDER BY rel_etf";

		$q_result = $sql->query($q_str);
		$ck_mk = 0;
		while($ord_sch = $sql->fetch($q_result))
		{
			$rtn[] = $ord_sch[0];
			$ck_mk = 1;
		}
		if($ck_mk == 0)$rtn[] = '';
		return $rtn;
	
		
	} // end func	
	




#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->order_get($ord_num) 取得訂單所有排產資料
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function count_pdt_qty($ord_num) {
// echo '<br>';
		$sql = $this->sql;
   $pdt = array();
		$q_str = "SELECT sum(qty) as qty, sum(su) as su, line_id  FROM saw_out_put 						  
							WHERE  ord_num='".$ord_num."'
							GROUP BY line_id, ord_num";				
// echo $q_str.'<br>';
		$q_result = $sql->query($q_str);
		while($row = $sql->fetch($q_result)) 
		{
			$pdt[] = $row;
		}

    for($i=0; $i<sizeof($pdt); $i++)
    {
			$pdt_qty = $pdt[$i]['qty'] ;
			while($pdt[$i]['qty'] > 0)
			{
				$q_str = "SELECT schedule.*  FROM schedule
									WHERE  line_id = '".$pdt[$i]['line_id']."' AND ord_num='".$ord_num."' AND pdt_qty < qty AND pdt_qty < '".$pdt_qty."'";
									// echo $q_str.'<br>';
				$q_result = $sql->query($q_str);
				if($row = $sql->fetch($q_result))
				{
					if($pdt[$i]['qty'] > $row['qty']){$qty = $row['qty'];}else{$qty = $pdt[$i]['qty'];}
					$q_str =  "UPDATE schedule SET  pdt_qty = '".$qty."' WHERE id = '".$row['id']."'";
					// echo $q_str.'<br>';
  				// $q_result = $sql->query($q_str);
  				$pdt[$i]['qty'] = $pdt[$i]['qty'] - $row['qty'];
  			}else{
  				break;
  			}
			
			}    	
    }	

		
	} // end func	


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->group_schedule($new_id) 重組排產
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function group_schedule($id) {

		$sql = $this->sql;
   	$mk = 0;
		$q_str = "SELECT * FROM schedule WHERE id= '".$id."'";	
		$q_result = $sql->query($q_str);
		if(!$sch = $sql->fetch($q_result))  return false;


    $etf = increceDaysInDate($sch['rel_ets'],-1);
	  $ets = increceDaysInDate($sch['rel_etf'],1);
    $q_str = "SELECT * FROM schedule 
    					WHERE rel_etf = '".$etf."'  AND ord_num = '".$sch['ord_num'].
    				"' AND line_id = '".$sch['line_id']."' AND p_id= '".$sch['p_id']."'
    					ORDER BY rel_etf DESC LIMIT 1";
		$q_result = $sql->query($q_str);
		if($pre = $sql->fetch($q_result)) 
		{
			$pre['new_etf'] = $sch['rel_etf'];
			$pre['qty'] += $sch['qty'];
			$pre['su'] += $sch['su'];
			$q_str =  "UPDATE schedule SET rel_etf='".$pre['new_etf']."', etf='".$pre['new_etf']."', qty ='".$pre['qty']."', su ='".$pre['su']."' 
								 WHERE id = '".$pre['id']."'";
			$q_result = $sql->query($q_str);
			$q_str = "DELETE FROM schedule WHERE id='".$sch['id']."'";		
			$q_result = $sql->query($q_str);
			return true;

		}
		
    $q_str = "SELECT * FROM schedule 
    					WHERE rel_ets >'".$ets."'  AND ord_num = '".$sch['ord_num']."' AND line_id = '".$sch['line_id']."' AND p_id= '".$sch['p_id']."'
    					ORDER BY rel_ets";
		$q_result = $sql->query($q_str);
		if($pre = $sql->fetch($q_result)) 
		{
			$pre['new_ets'] = $sch['rel_ets'];
			$pre['qty'] += $sch['qty'];
			$pre['su'] += $sch['su'];
			$q_str =  "UPDATE schedule SET rel_ets='".$pre['new_ets']."', ets='".$pre['new_ets']."', qty ='".$pre['qty']."', su ='".$pre['su']."' 
								 WHERE id = '".$pre['id']."'";
			$q_result = $sql->query($q_str);
			$q_str = "DELETE FROM schedule WHERE id='".$sch['id']."'";		
			$q_result = $sql->query($q_str);
			return true;
		}
		
	} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->ord_search($mode=0, $where_str='', $limit_entries=0)	搜尋 訂 單 資料	 
#			mode = 2 :  排產確認後的訂單搜尋 ( status >= 7 )
#			mode = 3 :  已有產出後的訂單搜尋 ( status > 7 ) PHP_fty
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function pdt_ord_search($mode=0, $parm='', $limit_entries=0) {

	$sql = $this->sql;
	$argv = $parm; // 將所有的 globals 都抓入$argv
	$ln_id = $_SESSION['sch_ln_id'];
	$limit_day = increceDaysInDate($GLOBALS['TODAY'],-360);

	$srh = new SEARCH();
	$cgi = array();
	if (!$srh->set_sql($sql)) {
		$this->msg->merge($srh->msg);
		return false;
	}

	$q_header = "
    SELECT 
    schedule.id as sd_id, schedule.qty as s_sch_qty, schedule.rel_ets, schedule.rel_etf, schedule.pdt_qty, 
    s_order.* , s_order.id as s_id ,
    pdtion.*, 
    cust.cust_init_name as cust_iname, 
    order_partial.id as p_id, order_partial.p_qty as p_ord_qty , order_partial.mks, order_partial.cut_qty as p_cut_qty,order_partial.p_su as su ,
    order_partial.p_qty_done as p_out_qty,order_partial.ext_qty as p_sch_qty
    FROM s_order, pdtion, cust, schedule, order_partial";
							 
	if (!$srh->add_q_header($q_header)) {
		$this->msg->merge($srh->msg);
		return false;
	}
	$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
	$srh->add_sort_condition("s_order.id DESC");
	$srh->add_sort_condition("`schedule`.`rel_ets` ASC ");
	$srh->row_per_page = 20;
	
	

	if($limit_entries){    // 當有限定最大量時~~~ 2005/11/28 加入
			$srh->q_limit = "LIMIT ".$limit_entries." ";
	}else{
		#--*****--##  2006.11.14 以數字型式顯示頁碼 star		
		$pagesize=10;
		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
		}else{
			$pages = $srh->get_page(1,$pagesize);
		} 
		##--*****--##  2006.11.14 以數字型式顯示頁碼 end
	}
	
	$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
	$user_team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
	$sales_dept = $GLOBALS['SALES_DEPT'];
	if ($user_team == 'MD')	$srh->add_where_condition("s_order.dept = '$user_dept'", "",$user_dept,"department=[ $user_dept ]. ");
	for ($i=0; $i< sizeof($sales_dept); $i++)
	{			
		if($user_dept == $sales_dept[$i] && $user_team <> 'MD') 	$srh->add_where_condition("s_order.dept = '$user_dept'", "",$user_dept,"department=[ $user_dept ]. ");
	} 	
	
	if ( $mode==1 || $mode==2 || $mode==3 || $mode==4 || $mode==5 ){

		$mesg = '';
		if ($str = strtoupper($argv['PHP_ref']) ) {
			$srh->add_where_condition("s_order.ref LIKE '%$str%'", "PHP_ref",$str); 
			$mesg.= "  ref# : [ $str ]. ";
		}
		
		if ($str = $argv['PHP_cust'] ) {
			$srh->add_where_condition("s_order.cust = '$str'", "PHP_cust",$str); 
			$mesg.= "  Cust. = [ $str ]. ";
		}
		
		if ($str = $argv['PHP_order_num'] ) {
			$srh->add_where_condition("s_order.order_num LIKE '%$str%'", "PHP_order_num",$str); 
			$mesg.= "  Order# : [ $str ]. ";
		}
		
		if ($str = $argv['PHP_factory'] ) {
			$srh->add_where_condition("s_order.factory = '$str'", "PHP_factory",$str); 
			$mesg.= "  FTY = [ $str ]. ";
		}
		
		if ($mesg) {
			$msg = "Search ".$mesg;
			$this->msg->add($msg);
		}
	}

	if ( $mode == 4 ) {   // 當要搜尋的 訂單是 排產確認後的時
		$where_strs ='';
		if ($str = $argv['PHP_ship'] ) $where_strs = $where_strs." || s_order.status = 12";
		if ($str = $argv['PHP_finish'] ) $where_strs = $where_strs." || s_order.status = 10";

		$srh->add_where_condition("s_order.status = 4 || s_order.status = 6 || s_order.status = 7 || s_order.status = 8 ".$where_strs, "","","");   
		$srh->add_where_condition("finish >= '".$limit_day."' or finish IS NULL or finish ='0000-00-00'", "","","");  		 
		$srh->add_where_condition("schedule.line_id = '".$argv['ln_id']."'");
	}	

	$srh->add_where_condition("s_order.order_num = pdtion.order_num", "",$str,"");   // 關聯式察尋 必然要加
	$srh->add_where_condition("s_order.cust = cust.cust_s_name AND s_order.cust_ver = cust.ver");   // 關聯式察尋 必然要加
	$srh->add_where_condition("s_order.order_num = schedule.ord_num");
	$srh->add_where_condition("pdtion.order_num = schedule.ord_num");
	$srh->add_where_condition("s_order.order_num = order_partial.ord_num");		
	$srh->add_where_condition("schedule.p_id = order_partial.id");
	$srh->add_group_condition('schedule.id');
	
	$result= $srh->send_query2($limit_entries);   // 2005/11/24 加入 $limit_entries
	if (!is_array($result)) {
		$this->msg->merge($srh->msg);
		return false;		    
	}
	$this->msg->merge($srh->msg);
	if (!$result){   // 當查尋無資料時
		$op['record_NONE'] = 1;
	}

	$op['sorder'] = $result;  // 資料錄 拋入 $op
	$op['cgistr_get'] = $srh->get_cgi_str(0);
	$op['cgistr_post'] = $srh->get_cgi_str(1);
	$op['prev_no'] = $srh->prev_no;
	$op['next_no'] = $srh->next_no;
	$op['max_no'] = $srh->max_no;
	$op['last_no'] = $srh->last_no;
	$op['start_no'] = $srh->start_no;
	$op['per_page'] = $srh->row_per_page;
	// echo $srh->q_str;
	##--*****--## 2006.11.14新頁碼需要的oup_put	start		
	$op['maxpage'] =$srh->get_max_page();
	$op['pages'] = $pages;
	$op['now_pp'] = $srh->now_pp;
	$op['lastpage']=$pages[$pagesize-1];
	##--*****--## 2006.11.14新頁碼需要的oup_put	end		
	return $op;
} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->contractor_schedule($parm) 外發排產
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function check_qty($id,$qty) {

	$sql = $this->sql;

	$q_str = "SELECT p_qty as qty, ord_num	FROM   order_partial WHERE id ='".$id."'";
	$q_result = $sql->query($q_str);
	$ord_rec = $sql->fetch($q_result);
	
	$q_str = "SELECT sum(schedule.qty)as qty FROM schedule WHERE schedule.p_id ='".$id."' GROUP BY ord_num";

	$q_result = $sql->query($q_str);
	$sch_rec = $sql->fetch($q_result);
	if(!isset($sch_rec['qty']))$sch_rec['qty'] = 0;
	$sch_rec['qty'] += $qty;
	if($ord_rec['qty'] < $sch_rec['qty']) return false;
	
	return 1;
} // end func	



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->auto_reload($parm) 外發排產
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function auto_reload($fty) {

	$sql = $this->sql;
    
    $sch_finish_rate = $GLOBALS['sch_finish_rate'];

	$q_str = "
	SELECT pdt_saw_line.*, min(rel_ets) as rel_ets FROM pdt_saw_line, schedule
	WHERE schedule.line_id = pdt_saw_line.id AND pdt_saw_line.fty ='".$fty."' AND 
    worker > 0 AND del_mk = 0 
	GROUP BY line_id
	ORDER BY line
    ";
	$q_str = "
	SELECT pdt_saw_line.* FROM pdt_saw_line 
	WHERE pdt_saw_line.fty ='".$fty."' AND 
    worker > 0 AND del_mk = 0 
    ";
    // echo $q_str.'<p>';
	$q_results = mysql_query($q_str);	
    $saw_line = array();
    $sch_arr = array();
	while($saw_line = mysql_fetch_array($q_results)) {
    
        $line_id = $saw_line['id'];
        $line = $saw_line['line'];
        $worker = $saw_line['worker'];

        # 抓出訂單在每一組最早開始的時間點，判斷狀態是否更新還需耗費時間 ( status , qty ) 
        $q_str = "SELECT `schedule`.`rel_ets`,`pdt_saw_line`.`worker` 
        FROM `schedule`,`pdt_saw_line` 
        WHERE 
        `schedule`.`status` = '2' AND 
        `schedule`.`pdt_qty` > '0' AND 
        `schedule`.`line_id` = '".$line_id."' AND 
        `schedule`.`line_id` = `pdt_saw_line`.`id` AND 
        `schedule`.`rel_etf` <= '".date('Y-m-d')."'
        ORDER BY `schedule`.`rel_ets` DESC  LIMIT 0 , 1;
        ";
        
        $q_str = "SELECT `schedule`.`rel_ets`
        FROM `schedule`
        WHERE 
        `schedule`.`status` = '2' AND 
        `schedule`.`pdt_qty` > '0' AND 
        `schedule`.`line_id` = '".$line_id."' AND 
        `schedule`.`rel_etf` <= '".date('Y-m-d')."'
        ORDER BY `schedule`.`rel_ets` DESC  LIMIT 0 , 1;
        ";
        
        // echo $q_str.'<p>';
        
        $q_res = $sql->query($q_str);
        $row = $sql->fetch($q_res);
        $rel_ets = $row['rel_ets'];
        // $worker = $row['worker'];
        // 新訂單會抓不到人數 預設
        $worker = empty($worker)?30:$worker;
        
        # 抓出每組的平均產能
        $line_avg = $this->reset_line_avg($line_id);
        $avg = $line_avg * $worker;
        // echo '[[['.$line_avg.']]]<br>';
        
        $q_str = "SELECT 
        `s_order`.`ie1`,`s_order`.`ie2`,`s_order`.`qty` as `ord_qty`,
        `schedule`.`id`,`schedule`.`ord_num`,`schedule`.`ets`,`schedule`.`etf`,`schedule`.`rel_ets`,`schedule`.`rel_etf`,`schedule`.`status`,
        `schedule`.`qty`,`schedule`.`su`,`schedule`.`pdt_qty`,`schedule`.`fty`,`schedule`.`p_id`,
        `order_partial`.`p_qty`,`order_partial`.`ext_qty`,`order_partial`.`p_etp`,`order_partial`.`p_etd`,
        `pdt_saw_line`.`sc` 
        FROM 
        `s_order`,`schedule`,`order_partial`,`pdt_saw_line` 
        WHERE 
        `schedule`.`ord_num` = `s_order`.`order_num` AND `schedule`.`p_id` = `order_partial`.`id` AND `schedule`.`line_id` = `pdt_saw_line`.`id` AND 
        `schedule`.`rel_ets` >= '".$rel_ets."' AND `schedule`.`line_id` = '".$line_id."' 
        GROUP BY `schedule`.`id` 
        ORDER BY `schedule`.`status` DESC , `schedule`.`pdt_qty` DESC , `schedule`.`rel_ets` ASC  , `schedule`.`id` DESC ;
        ";
        // ORDER BY `schedule`.`status` DESC , `schedule`.`pdt_qty` DESC , `schedule`.`rel_ets` ASC , `schedule`.`rel_etf` ASC ;
        // echo $q_str.'<p>';
        
        $q_result = mysql_query($q_str);
        
        // `qty` 排產數量
        // `pdt_qty` 生產數量
        // $mo 驗證用
        $row = array();
        $row_arr = array();
        while ($row = mysql_fetch_array($q_result)) {
            $row_arr[] = $row;
        }
        $sch_arr[] = array( 'line_id' => $line_id , 'line' => $line , 'avg' => $avg , 'arr' => $row_arr );
	}		

    $partial_arr = $this->reset_schedule($sch_arr);
    // print_r($sch_arr);
    // exit;
    // print_r($sch_arr);
	return $partial_arr;
} // end func	



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->contractor_schedule($parm) 外發排產
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function un_finish($fty) {

	$sql = $this->sql;
	$today= date('Y-m-d');
	$rtn = array();
	$q_str = "SELECT	schedule.id, schedule.ord_num, rel_ets, rel_etf, schedule.qty, pdt_qty, 
										pdt_saw_line.line, s_order.etd, s_order.style, s_order.qty as ord_qty,
										SUBSTRING(ord_num,6,8) as ord_init
						FROM		schedule, s_order, pdt_saw_line
						WHERE		s_order.order_num = schedule.ord_num AND pdt_saw_line.id = schedule.line_id AND 
										s_order.status = 8 AND factory = '".$fty."' AND rel_etf < '".$today."'
						ORDER BY `ord_num`";

	$q_result = $sql->query($q_str);
	while($row = $sql->fetch($q_result)) 
	{
		$rtn[] = $row;		
	}
	
	return $rtn;
} // end func	



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del_ord_sch($ord_num) 由訂單刪除排產
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function del_ord_sch($ord_num) {

	$sql = $this->sql;
	$today= date('Y-m-d');
	$rtn = array();
	$q_str = "SELECT	id
						FROM		schedule
						WHERE		schedule.ord_num = '".$ord_num."'";

	$q_result = $sql->query($q_str);
	while($row = $sql->fetch($q_result)) 
	{
		$this->del_schedule($row['id'],'',$ord_num);
	}
	
	return true;
} // end func		



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->schedule_daily_auto() 由訂單刪除排產
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function schedule_daily_auto() {

  $sql = $this->sql;

  $q_str = "SELECT `p_id` FROM `schedule` WHERE `pdt_qty` = '0'";
  $q_result = $sql->query($q_str);
  while($row = $sql->fetch($q_result)) 
  {
    if ( $row['p_id'] ) {
      $this->add_ord_schedule($row['p_id']);					
      if($this->check_sch_finish($row['p_id']))
        $this->add_capacity($row['p_id']);
    }
  }

  return true;
} // end func			




#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_field_value($field,$id='',$ord_num='', $tbl='s_order')	取出 某個  field的值
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_field_value($field, $id='',$ord_num='') {
		$sql = $this->sql;
		$row = array();

		if ($id) {
	  		$q_str = "SELECT ".$field." FROM `schedule` WHERE id='".$id."' ";
		} elseif($ord_num) {
	  		$q_str = "SELECT ".$field." FROM `schedule` WHERE ord_num='".$ord_num."' ";
		} else {
			$this->msg->add("Error! not enough info to get data record !");
			$this->msg->merge($sql->msg);
			return false;    
		}

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$row = $sql->fetch($q_result);

		$field_val = $row[0];

		return $field_val;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add_schedule($parm)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function add_schedule($parm) {

// mode
$sql = $this->sql;

# 加入資料庫
$q_str = "INSERT INTO schedule ( line_id,  ord_num,su, qty, ets,etf,rel_ets,rel_etf,fty,open_date,open_user,des,p_id ) 
VALUES ('"
.$parm['line_id']."','"
.$parm['ord_num']."','"	
.$parm['su']."','"
.$parm['qty']."','"
.$parm['ets']."','"
.$parm['etf']."','"
.$parm['ets']."','"
.$parm['etf']."','"
.$parm['fty']."','"
.date('Y-m-d')."','"
.$GLOBALS['SCACHE']['ADMIN']['login_id']."','"
.$parm['des']."','"
.$parm['p_id']."')";

$q_result = $sql->query($q_str);
$new_id = $sql->insert_id();  //取出 新的 id	

return $new_id;

} // end func	ftyline_sp

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->point_dynamic_schedule($parm)	一開始指定生產線與數量 // mode 新增
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function point_dynamic_schedule($parm) {
		// mode
		$sql = $this->sql;
		# 加入資料庫
		$q_str = "INSERT INTO schedule ( line_id, ord_num,des,su, qty, ets,etf,rel_ets,rel_etf,fty,open_date,open_user ) 
							VALUES ('".$parm['line_id']."','"
												.$parm['ord_num']."','"	
												.$parm['des']."','"											
												.$parm['su']."','"
												.$parm['qty']."','"
												.$parm['ets']."','"
												.$parm['etf']."','"
												.$parm['ets']."','"
												.$parm['etf']."','"
												.$parm['fty']."','"
												.date('Y-m-d')."','"
												.$GLOBALS['SCACHE']['ADMIN']['login_id']."')";
		$q_result = $sql->query($q_str);
		$new_id = $sql->insert_id();  //取出 新的 id	

		return $new_id;
	} // end func	ftyline_sp

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del_dynamic_schedule($id) 產除生產線排產 // mode 新增
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del_dynamic_schedule($id) {

		$sql = $this->sql;

		$q_str = "DELETE FROM `schedule` WHERE `id` = '".$id."'";		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法刪除資料記錄.");
			$this->msg->merge($sql->msg);
			return false;    
		}
	} // end func	
	

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add_dynamic_schedule($ord_num) 將排產資料加入訂單 // mode 新增
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_dynamic_schedule($ord_num) {

		$sql = $this->sql;
		
		$today= date('Y-m-d');
		$rtn = array();
			
		$q_str = "SELECT max(rel_etf) as etf, min(rel_ets) as ets, sum(su) as su, sum(qty) as qty	
							FROM schedule
							WHERE schedule.ord_num ='".$ord_num."'
							GROUP BY ord_num"	;

    $q_result = $sql->query($q_str);	
    if($row = $sql->fetch($q_result))
    {
      //訂單加入ets,etf
      //訂單加入己排產數量
      //訂單加入預計生產所需日數
      $day_range = dayCount($row['ets'],$row['etf']);
      $q_str = "  UPDATE pdtion SET ets ='".$row['ets']."', etf ='".$row['etf']."' , 
                                    ext_su ='".$row['su']."', ext_qty ='".$row['qty']."' , 
                                    ext_period ='".$day_range."'
                  WHERE order_num='".$ord_num."'";
      $q_result = $sql->query($q_str);
    
    } else {
      // 排產清潔動作
      $q_str = "UPDATE pdtion SET ets = NULL, etf = NULL, ext_su ='0', ext_period ='0', ext_qty = 0 WHERE order_num='".$ord_num."'";
      $q_result = $sql->query($q_str);			

      // 修改狀態
      $q_str = "UPDATE s_order SET status = 6 WHERE order_num='".$ord_num."'";
      $q_result = $sql->query($q_str);			

      $pdt_ord = $GLOBALS['order']->schedule_get(0,$ord_num);  //取出該筆記錄
      $GLOBALS['order']->delete_month_su( $pdt_ord['ets'], $pdt_ord['etf'],$pdt_ord['su'],$pdt_ord['factory'],'schedule');
      $GLOBALS['order']->del_cfm_pd_schedule($pdt_ord['id']);
    }

    // 紀錄 in_house	
		$q_str = "SELECT schedule.id
							FROM   schedule, pdt_saw_line
							WHERE  schedule.line_id = pdt_saw_line.id AND schedule.ord_num ='".$ord_num."' AND
										 pdt_saw_line.sc = 0
							GROUP BY ord_num"	;

		$q_result = $sql->query($q_str);	
		if($row = $sql->fetch($q_result))
		{
      $q_str = "UPDATE pdtion SET in_house = 1 WHERE order_num='".$ord_num."'";
      $q_result = $sql->query($q_str);					
		} else {
      $q_str = "UPDATE pdtion SET in_house = 0 WHERE order_num='".$ord_num."'";
      $q_result = $sql->query($q_str);							
		}

    // 紀錄 sub_con	
		$q_str = "SELECT schedule.id
							FROM   schedule, pdt_saw_line
							WHERE  schedule.line_id = pdt_saw_line.id AND schedule.ord_num ='".$ord_num."' AND
										 pdt_saw_line.sc = 1
							GROUP BY ord_num"	;

		$q_result = $sql->query($q_str);	
		if($row = $sql->fetch($q_result))
		{
      $q_str = "UPDATE pdtion SET sub_con = 1 WHERE order_num='".$ord_num."'";
      $q_result = $sql->query($q_str);					
		} else {
      $q_str = "UPDATE pdtion SET sub_con = 0 WHERE order_num='".$ord_num."'";
      $q_result = $sql->query($q_str);							
		}
		
		return 1;
	} // end func	


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->up_scd($parm)	更新排產後面的訂單 // mode 新增
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function up_scd($parm) {
		// mode
		$sql = $this->sql;
		$q_str =  " UPDATE schedule SET 
								line_id='".$parm['line_id']."' , 
								rel_etf='".$parm['etf']."' , 
								rel_ets='".$parm['ets']."' , 

								qty='".$parm['qty']."' ,
								des='".$parm['des']."'  
								WHERE id = '".$parm['id']."'";
		if ($q_result = $sql->query($q_str) ){
			return 1;
		}
		
										// etf='".$parm['etf']."' , 
								// ets='".$parm['ets']."' ,
	} // end func	ftyline_sp


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++




#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_contractor($fty)	取出 某個  field的值
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_contractor($fty) {

	$sql = $this->sql;
	$field_val = array();

	$q_str = "SELECT `id` FROM `pdt_saw_line` WHERE `fty` = '".$fty."' and `line` = 'contractor'";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;
	}
	
	$row = $sql->fetch($q_result);
	
	return $row['id'];
} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_field_value($field,$id='',$ord_num='', $tbl='s_order')	取出 某個  field的值
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_line_id_value($line_id, $id='',$ord_num='') {

	$sql = $this->sql;
	$field_val = array();

	$q_str = "SELECT `id`,`ord_num`,`su`,`qty`,`rel_ets`,`rel_etf`,`pdt_qty`,`p_id` FROM `schedule` WHERE `line_id` = '".$line_id."' and `status` = '0' ORDER BY `rel_ets` ASC ";
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;
	}
	// echo $q_str;
	while($row = $sql->fetch($q_result)) {
		$field_val[] = $row;
	}
	
	return $field_val;
} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_field_value($field,$id='',$ord_num='', $tbl='s_order')	取出 某個  field的值
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function set_ets_etf($ord_qty,$nowQty,$qty,$s_id){

	$sql = $this->sql;
    
    $sch_finish_rate = $GLOBALS['sch_finish_rate'];

	$today = date('Y-m-d');
	
	# 超過 95%
	if( ( $nowQty / $ord_qty ) >= $sch_finish_rate ) {
		$q_str =  "UPDATE `schedule` SET `etf` = '".$today."' , `status` = '2' , `pdt_qty` = '".$nowQty."' WHERE id = '".$s_id."'";
		$q_result = $sql->query($q_str);
	# 超過 95% + 等於第一次產出
	} else if ( ( $nowQty / $ord_qty ) >= $sch_finish_rate &&  $nowQty == $qty ) {
		$q_str =  "UPDATE `schedule` SET `ets` = '".$today."' , `etf` = '".$today."' , `status` = '2' , `pdt_qty` = '".$nowQty."' WHERE id = '".$s_id."'";
		$q_result = $sql->query($q_str);
	# 第一次產出
	} else if ( $nowQty == $qty ) {
		$q_str =  "UPDATE `schedule` SET `ets` = '".$today."' , `pdt_qty` = '".$nowQty."' WHERE id = '".$s_id."'";
		$q_result = $sql->query($q_str);
	} else {
		# 不是開始也不是結束
		$q_str =  "UPDATE `schedule` SET `pdt_qty` = '".$nowQty."' WHERE id = '".$s_id."'";
		$q_result = $sql->query($q_str);
	}
	
}

function get_order_schedule( $ord_num,$partial_id=0)
{
	$sql = $this->sql;
	$field_val = array();
	if($partial_id == 0)
	{
		$q_str = "SELECT `id`,`ord_num`,`su`,`qty`,`p_id` FROM `schedule` WHERE `ord_num` = '".$ord_num."'";
		// echo $q_str;
		// echo "<br>";
	}
	else
	{
		$q_str = "SELECT `id`,`ord_num`,`su`,`qty`,`p_id` FROM `schedule` WHERE `ord_num` = '".$ord_num."' and p_id=".$partial_id;
		// echo $q_str;
		// echo "<br>";
	}
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;
	}
	// echo $q_str;
	while($row = $sql->fetch($q_result)) {
		$field_val[] = $row;
	}
	//print_r($field_val);
	//exit;
	return $field_val;
}



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# mdf_ie->+($ord_num)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function mdf_ie($ord_num) {

$sql = $this->sql;

$sch_finish_rate = $GLOBALS['sch_finish_rate'];

# 抓取該訂單出現在那些組
$line_arr = $this->get_line_for_ord($ord_num);

// $partial_arr = array();
foreach($line_arr as $key => $line_id){

    $GLOBALS['schedule']->schedule_chk_finish($line_id);
    $GLOBALS['schedule']->schedule_reset_pdtion_waitting($line_id);

    # 抓出訂單在每一組最早開始的時間點，判斷狀態是否更新還需耗費時間 ( status , qty ) 
    // $q_str = "SELECT `schedule`.`rel_ets`,`pdt_saw_line`.`worker` 
    // FROM `schedule`,`pdt_saw_line` 
    // WHERE 
    // `schedule`.`status` = '2' AND 
    // `schedule`.`pdt_qty` > '0' AND 
    // `schedule`.`line_id` = '".$val."' AND 
    // `schedule`.`line_id` = `pdt_saw_line`.`id` AND 
    // `schedule`.`rel_etf` <= '".date('Y-m-d')."'
    // ORDER BY `schedule`.`rel_ets` DESC  LIMIT 0 , 1;
    // ";   
    // echo $q_str.'<br>';
    
    // $q_result = $sql->query($q_str);
    // $row = $sql->fetch($q_result);
    // $rel_ets = $row['rel_ets'];
    // $worker = $row['worker'];

    # 抓出每組的平均產能
    // $line_avg = $this->reset_line_avg($val);
    // $avg = $line_avg * $worker;
    
    // $q_str = "SELECT 
    // `s_order`.`ie1`,`s_order`.`ie2`,`s_order`.`qty` as `ord_qty`,
    // `schedule`.`id`,`schedule`.`ord_num`,`schedule`.`ets`,`schedule`.`etf`,`schedule`.`rel_ets`,`schedule`.`rel_etf`,`schedule`.`status`,
    // `schedule`.`qty`,`schedule`.`su`,`schedule`.`pdt_qty`,`schedule`.`fty`,`schedule`.`p_id`,
    // `order_partial`.`p_qty`,`order_partial`.`ext_qty`,`order_partial`.`p_etp`,`order_partial`.`p_etd`,
    // `pdt_saw_line`.`sc` 
    // FROM 
    // `s_order`,`schedule`,`order_partial`,`pdt_saw_line` 
    // WHERE 
    // `schedule`.`ord_num` = `s_order`.`order_num` AND `schedule`.`p_id` = `order_partial`.`id` AND `schedule`.`line_id` = `pdt_saw_line`.`id` AND 
    // `schedule`.`rel_ets` >= '".$rel_ets."' AND `schedule`.`line_id` = '".$val."' 
    // GROUP BY `schedule`.`id` 
    // ORDER BY `schedule`.`status` DESC , `schedule`.`pdt_qty` > 0 , `schedule`.`rel_ets` ASC ;
    // ";
    // $q_result = $sql->query($q_str);
    // echo $q_str.'<br>';
    
    // `qty` 排產數量
    // `pdt_qty` 生產數量
    // $mo 驗證用
    // $row = array();
    // $row_arr = array();
    // while ($row = mysql_fetch_array($q_result)) {
        // $row_arr[] = $row;
    // }
    // $sch_arr[] = array( 'line_id' => $val , 'line' => $val , 'avg' => $avg , 'arr' => $row_arr );
}

    // $partial_arr = $this->reset_schedule($sch_arr);
    
    // return $partial_arr;
} // end func



##############################################
function reset_schedule($parm){

$sql = $this->sql;

$sch_finish_rate = $GLOBALS['sch_finish_rate'];

$mod['TMP'] = array();
$mod_arr = array();
foreach($parm as $rows){
    $line_id = $rows['line_id'] ;
    $line = $rows['line'] ;
    $avg = $rows['avg'] ;
    
    $mod['arr'] = array();
    foreach($rows['arr'] as $row){
        // if( $mod['TMP']['line_id'] == $rows['line_id'] && $mod['TMP']['ord_num'] == $row['ord_num'] && $mod['TMP']['p_id'] == $row['p_id'] && $mod['TMP']['sc'] == $row['sc'] ){
            // #合併訂單數量 PPPPPPPPPPPPPPSSSSSSSSSSSSSS 遺漏合併刪除排產如果有產出會變孤兒產出資料
            // $mode_pop = array_pop( $mod['arr'] );
            // $q_str = "UPDATE schedule SET `qty` = `qty` + '".$row['qty']."' , `su` = `su` + '".$row['su']."' , `pdt_qty` = `pdt_qty` + '".$row['pdt_qty']."' WHERE `id` = '".$mode_pop['id']."' ;";
            // $q_result = $sql->query($q_str);
            // $q_str = "DELETE FROM `schedule` WHERE `id` = '".$row['id']."'";
            // $q_result = $sql->query($q_str);
            
            // $row['id'] = $mode_pop['id'];
            // $row['rel_ets'] = $mode_pop['rel_ets'];
            // $row['rel_etf'] = $mode_pop['rel_etf'];
            // $row['qty'] = $row['qty'] + $mode_pop['qty'];
            // $row['su'] = $row['su'] + $mode_pop['su'];
            // $row['pdt_qty'] = $row['pdt_qty'] + $mode_pop['pdt_qty'];
        // }
        $mod['TMP']['line_id'] = $rows['line_id'];
        $mod['TMP']['ord_num'] = $row['ord_num'];
        $mod['TMP']['p_id'] = $row['p_id'];
        $mod['TMP']['sc'] = $row['sc'];
        $mod['TMP']['arr'] = $row;
        $mod['arr'][] = $row;
    }
    $mod_arr[] = array( 'line_id' => $line_id , 'line' => $line , 'avg' => $avg , 'arr' => $mod['arr'] );
}
// print_r($parm);
foreach($mod_arr as $rows){

    $line_id = $rows['line_id'];
    $line = $rows['line'];
    $avg = $rows['avg'];
    $Ets = $Etf = $First = '';

    foreach ($rows['arr'] as $row) {
    
        $ie = ( $row['ie2'] > 0 ) ? $row['ie2'] : $row['ie1'] ;
        $su = set_su($ie,$row['qty']);
    
        # 目前產出數量
        $output = $GLOBALS['order']->get_output($row['ord_num']);
        
        # 計算剩餘 SU / 計算生產時間 / 無條件進位
        $w_day = $this->chk_work_day($output,$row['ord_qty'],$avg,$ie,$row['qty'],$row['pdt_qty']);
        $mo = '';
        if ( $row['status'] != '2' && $row['pdt_qty'] > 0 && (( $row['pdt_qty'] / $row['qty'] ) >= $sch_finish_rate || ( $output / $row['ord_qty'] ) >= $sch_finish_rate) ) {
            # FINISH 訂單

            #ETS
            if( empty($First) ) {
                $Ets = $row['rel_ets'];
                $mo = ' (F1) ETS ('.$Ets.') ';
            } else {
                $Ets = $Etf;
                $mo = ' (F2) ETS ('.$Ets.') ';
            }
            
            #ETF 生產時間加上國定假日
            if( empty($First) ) {
                $Etf = $this->increceDay(date('Y-m-d'),$w_day,$row['fty']);        
                $mo .= ' (F1) ETF ('.$Etf.') ';
            } else {
                $Etf = $this->increceDay($Ets,$w_day,$row['fty']);
                $mo .= ' (F2) ETF ('.$Etf.') ';
            }

            # SET status
            // if( $row['status'] != '2' || ( $output / $row['ord_qty'] ) >= $sch_finish_rate) ) 
			$this->set_schedule_status($row['id']);
            $First ++;
        } else if ( $row['sc'] == 1 || $row['status'] == '2' ) {
            # FINISH & 外發訂單 固定
            $Ets = $row['rel_ets'];
            $Etf = $row['etf'];
            $mo = ' (S) ETS ('.$Ets.') (S) ETF ('.$Etf.') ';
        } else {
            # 生產排序訂單
            
            #ETS
            if( empty($First) && empty($Ets) ) {
                $Ets = $row['rel_ets'];
                $mo = ' (P1) ETS ('.$Ets.') ';
            } else {
                $Ets = $Etf;
                $mo = ' (P2) ETS ('.$Ets.') ';
            }
            
            #ETF 生產時間加上國定假日
            if( empty($First) && empty($Etf) ) {
                $Etf = $this->increceDay(date('Y-m-d'),$w_day,$row['fty']);
                $mo .= ' (P1) ETF ('.$Etf.') ';
            } else {
                $Etf = $this->increceDay($Ets,$w_day,$row['fty']);
                if( $Ets < date('Y-m-d'))$Etf = $this->increceDay(date('Y-m-d'),$w_day,$row['fty']);
                $mo .= ' (P2) ETF ('.$Etf.') ';
            }
            $First ++;
        }
        
        
        // echo $line.') ~ '. $row['ord_num'].' ~ '.$mo.' ~ '.$w_day.' ';
        // echo $output.' ~ '.$row['ord_qty'].' ~ '.$row['qty'].' ~ '.$row['sc'].' ~ '.$row['status'].' ~ ['.$row['etf'].']<br>';

        # 重新設定 su 並計算新的製作天數 ( status = 2 只調整 su 不調整天數 )
        $this->up_sch($row['id'],$su,$Ets,$Etf,$row['status']);

        # 組合修改陣列，去除相同訂單 ext_su
        $p_su = set_su($ie,$row['p_qty']);
        $partial_arr[$row['p_id']] = array( 'ie' => $ie , 'p_id' => $row['p_id'] , 'p_su' => $p_su , 'p_etp' => $row['p_etp'] , 'p_etd' => $row['p_etd'] , 'order_num' => $row['ord_num'] );
    }
    // echo '<br>';
}

return $partial_arr;

}


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# mdf_finish_ie->+($ord_num)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function mdf_finish_ie($ord_num) {

$sql = $this->sql;

$sch_finish_rate = $GLOBALS['sch_finish_rate'];

# 抓取該訂單出現在那些組
$line_arr = $this->get_line_for_ord($ord_num);

$partial_arr = array();
foreach($line_arr as $key => $val){

    # 抓出訂單在每一組最早開始的時間點，判斷狀態是否更新還需耗費時間 ( status , qty )
    $q_str = "SELECT `schedule`.`rel_ets`,`pdt_saw_line`.`worker` 
    FROM `schedule`,`pdt_saw_line` 
    WHERE 
    `schedule`.`status` = '2' AND 
    `schedule`.`pdt_qty` > '0' AND 
    `schedule`.`line_id` = '".$val."' AND 
    `schedule`.`line_id` = `pdt_saw_line`.`id` AND 
    `schedule`.`rel_etf` <= '".date('Y-m-d')."'
    ORDER BY `schedule`.`rel_ets` DESC  LIMIT 0 , 1;
    ";
    
    $q_result = $sql->query($q_str);
    $row = $sql->fetch($q_result);
    $rel_ets = $row['rel_ets'];
    $worker = $row['worker'];
    
    // echo $q_str.'<br>';
    
    # 抓出每組的平均產能
    $line_avg = $this->reset_line_avg($val);
    $avg = $line_avg * $worker;
    
    $q_str = "SELECT 
    `s_order`.`ie1`,`s_order`.`ie2`,`s_order`.`qty` as `ord_qty`,
    `schedule`.`id`,`schedule`.`ord_num`,`schedule`.`ets`,`schedule`.`etf`,`schedule`.`rel_ets`,`schedule`.`rel_etf`,`schedule`.`status`,
    `schedule`.`qty`,`schedule`.`su`,`schedule`.`pdt_qty`,`schedule`.`fty`,`schedule`.`p_id`,
    `order_partial`.`p_qty`,`order_partial`.`ext_qty`,`order_partial`.`p_etp`,`order_partial`.`p_etd`,
    `pdt_saw_line`.`sc` 
    FROM 
    `s_order`,`schedule`,`order_partial`,`pdt_saw_line` 
    WHERE 
    `schedule`.`ord_num` = `s_order`.`order_num` AND `schedule`.`p_id` = `order_partial`.`id` AND `schedule`.`line_id` = `pdt_saw_line`.`id` AND 
    `schedule`.`rel_ets` >= '".$rel_ets."' AND `schedule`.`line_id` = '".$val."' 
    GROUP BY `schedule`.`id` 
    ORDER BY `schedule`.`status` DESC , `schedule`.`pdt_qty` > 0 , `schedule`.`rel_ets` ASC ;
    ";
    $q_result = $sql->query($q_str);
    // echo $q_str.'<br>';
    
    // `qty` 排產數量
    // `pdt_qty` 生產數量
    // $mo 驗證用
    $row = array();
    $row_arr = array();
    while ($row = mysql_fetch_array($q_result)) {
        $row_arr[] = $row;
    }
    $sch_arr[] = array( 'line_id' => $val , 'line' => $val , 'avg' => $avg , 'arr' => $row_arr );
}

    $partial_arr = $this->reset_schedule($sch_arr);
    
    return $partial_arr;
} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# mdf_line_ie->+($ord_num)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function mdf_line_ie($sd_id) {

$sql = $this->sql;

$sch_finish_rate = $GLOBALS['sch_finish_rate'];

# 抓出訂單在每一組最早開始的時間點，判斷狀態是否更新還需耗費時間 ( status , qty )
$q_str = "SELECT `schedule`.`line_id`
FROM `schedule`
WHERE `schedule`.`id` = '".$sd_id."';
";

$q_result = $sql->query($q_str);
$row = $sql->fetch($q_result);
$line_id = $row['line_id'];

$q_str = "SELECT `schedule`.`rel_ets`,`pdt_saw_line`.`worker` 
FROM `schedule`,`pdt_saw_line` 
WHERE 
`schedule`.`status` = '2' AND 
`schedule`.`pdt_qty` > '0' AND 
`schedule`.`line_id` = '".$line_id."' AND 
`schedule`.`line_id` = `pdt_saw_line`.`id` AND 
`schedule`.`rel_ets` < '".date('Y-m-d')."' AND 
`schedule`.`rel_etf` <= '".date('Y-m-d')."'
ORDER BY `schedule`.`rel_ets` DESC  LIMIT 0 , 1;
";

$q_result = $sql->query($q_str);
$row = $sql->fetch($q_result);
$rel_ets = $row['rel_ets'];
$worker = $row['worker'];

// echo $q_str.'<br>';

# 抓出每組的平均產能
$line_avg = $this->reset_line_avg($line_id);
if(empty($line_avg)){
    echo '<br>Avg Error!! '.$line_avg.'<br>';
    exit;
}
$avg = $line_avg * $worker;

$q_str = "SELECT 
`s_order`.`ie1`,`s_order`.`ie2`,`s_order`.`qty` as `ord_qty`,
`schedule`.`id`,`schedule`.`ord_num`,`schedule`.`ets`,`schedule`.`etf`,`schedule`.`rel_ets`,`schedule`.`rel_etf`,`schedule`.`status`,
`schedule`.`qty`,`schedule`.`su`,`schedule`.`pdt_qty`,`schedule`.`fty`,`schedule`.`p_id`,
`order_partial`.`p_qty`,`order_partial`.`ext_qty`,`order_partial`.`p_etp`,`order_partial`.`p_etd`,
`pdt_saw_line`.`sc` 
FROM 
`s_order`,`schedule`,`order_partial`,`pdt_saw_line` 
WHERE 
`schedule`.`ord_num` = `s_order`.`order_num` AND `schedule`.`p_id` = `order_partial`.`id` AND `schedule`.`line_id` = `pdt_saw_line`.`id` AND 
`schedule`.`rel_ets` >= '".$rel_ets."' AND `schedule`.`line_id` = '".$line_id."' 
GROUP BY `schedule`.`id` 
ORDER BY `schedule`.`status` DESC , `schedule`.`pdt_qty` DESC , `schedule`.`rel_ets` ASC  , `schedule`.`id` DESC ;
";
$q_result = $sql->query($q_str);
// echo $q_str.'<br>';

// `qty` 排產數量
// `pdt_qty` 生產數量
// $mo 驗證用
$row = array();
$row_arr = array();
while ($row = mysql_fetch_array($q_result)) {
    $row_arr[] = $row;
}
$sch_arr[] = array( 'line_id' => $line_id , 'line' => $line_id , 'avg' => $avg , 'arr' => $row_arr );

$partial_arr = $this->reset_schedule($sch_arr);

return $partial_arr;
} // end func




#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# mdf_schedule_line->+($ord_num)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function mdf_schedule_line($line_id) {

$sql = $this->sql;

$sch_finish_rate = $GLOBALS['sch_finish_rate'];

# 抓出訂單在每一組最早開始的時間點，判斷狀態是否更新還需耗費時間 ( status , qty )

$q_str = "SELECT `schedule`.`rel_ets`,`pdt_saw_line`.`worker` 
FROM `schedule`,`pdt_saw_line` 
WHERE 
`schedule`.`status` = '2' AND 
`schedule`.`pdt_qty` > '0' AND 
`schedule`.`line_id` = '".$line_id."' AND 
`schedule`.`line_id` = `pdt_saw_line`.`id` AND 
`schedule`.`rel_etf` <= '".date('Y-m-d')."'
ORDER BY `schedule`.`rel_ets` DESC  LIMIT 0 , 1;
";

$q_result = $sql->query($q_str);
$row = $sql->fetch($q_result);
$rel_ets = $row['rel_ets'];
$worker = $row['worker'];

// echo $q_str.'<br>';
// exit;
# 抓出每組的平均產能
$line_avg = $this->reset_line_avg($line_id);
if(empty($line_avg)){
    echo '<br>Avg Error!! '.$line_avg.'<br>';
    exit;
}
$avg = $line_avg * $worker;

$q_str = "SELECT 
`s_order`.`ie1`,`s_order`.`ie2`,`s_order`.`qty` as `ord_qty`,
`schedule`.`id`,`schedule`.`ord_num`,`schedule`.`ets`,`schedule`.`etf`,`schedule`.`rel_ets`,`schedule`.`rel_etf`,`schedule`.`status`,
`schedule`.`qty`,`schedule`.`su`,`schedule`.`pdt_qty`,`schedule`.`fty`,`schedule`.`p_id`,
`order_partial`.`p_qty`,`order_partial`.`ext_qty`,`order_partial`.`p_etp`,`order_partial`.`p_etd`,
`pdt_saw_line`.`sc` 
FROM 
`s_order`,`schedule`,`order_partial`,`pdt_saw_line` 
WHERE 
`schedule`.`ord_num` = `s_order`.`order_num` AND `schedule`.`p_id` = `order_partial`.`id` AND `schedule`.`line_id` = `pdt_saw_line`.`id` AND 
`schedule`.`rel_ets` >= '".$rel_ets."' AND `schedule`.`line_id` = '".$line_id."' 
GROUP BY `schedule`.`id` 
ORDER BY `schedule`.`status` DESC , `schedule`.`pdt_qty` DESC , `schedule`.`rel_ets` ASC  , `schedule`.`id` DESC ;
";
// ORDER BY `schedule`.`status` DESC , `schedule`.`pdt_qty` > 0 , `schedule`.`rel_ets` ASC ;
$q_result = $sql->query($q_str);
// echo $q_str.'<br>';


// `qty` 排產數量
// `pdt_qty` 生產數量
// $mo 驗證用
$row = array();
$row_arr = array();
while ($row = mysql_fetch_array($q_result)) {
    $row_arr[] = $row;
}
$sch_arr[] = array( 'line_id' => $line_id , 'line' => $line_id , 'avg' => $avg , 'arr' => $row_arr );

$partial_arr = $this->reset_schedule($sch_arr);

return $partial_arr;
} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->chk_ets($id)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function chk_ets($ets,$c_ets) {

// echo $ets.'<br>';
// echo $c_ets.'<br>';

if( $c_ets > 0 ) {
    $ets = ( $ets > $c_ets ) ? $ets : $c_ets ;
} else {
    $ets = ( $ets < date('Y-m-d') ) ? date('Y-m-d') : $ets ;
}

return $ets;

} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->get_line_for_ord($ord_num)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_line_for_ord($ord_num) {

$sql = $this->sql;

$q_str = "SELECT DISTINCT `line_id` 
FROM `schedule` 
WHERE `ord_num` = '".$ord_num."' GROUP BY `line_id` ;
";
// echo $q_str.'<br>';
if (!$q_result = $sql->query($q_str)) {
    $this->msg->add("Error ! 無法更新資料庫內容.");
    $this->msg->merge($sql->msg);
    return false;    
}

$line_arr = array();
while ($row = $sql->fetch($q_result)) {	
    $line_arr[] = $row['line_id'];
}

return $line_arr;

} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->reset_line_avg($line) status = 0 包含加班 status > 0 不含加班
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function reset_line_avg($line_id,$status=0) {

$sql = $this->sql;

$pdt_range = $GLOBALS['pdt_range'];
$schedule_var = $GLOBALS['schedule_var'];
$df_rate = $GLOBALS['DF_RATE'];
$FULL_TIME = $GLOBALS['FULL_TIME'];
// echo 'pdt_range = '.$pdt_range.'<br>';
// echo 'schedule_var = '.$schedule_var.'<br>';
// echo 'FULL_TIME = '.$FULL_TIME.'<br>';
$q_str = "  SELECT 
`saw_out_put`.`work_qty` , `saw_out_put`.`over_qty` , `saw_out_put`.`workers` , `saw_out_put`.`ot_wk` , 
`s_order`.`ie1` , `s_order`.`ie2`

FROM 
`saw_out_put`,`s_order` 
WHERE 
`saw_out_put`.`qty` > 0 AND 
`saw_out_put`.`ord_num` = `s_order`.`order_num` AND 
`saw_out_put`.`line_id` ='".$line_id."' AND `saw_out_put`.`holiday` = '0' 
GROUP BY `saw_out_put`.`out_date` 
ORDER BY `saw_out_put`.`out_date` DESC 
LIMIT 0 , ".$pdt_range." 
;";

// echo $q_str.'<br>';
if (!$q_result = $sql->query($q_str)) {
    $this->msg->add("Error ! 無法更新資料庫內容.");
    $this->msg->merge($sql->msg);
    return false;    
}

$day_su = $rate = 0;

while ($row = $sql->fetch($q_result)) {

    $ie = $row['ie2'] > 0 ? $row['ie2'] : $row['ie1'] ;
    if( empty($status) ){
        $day_su += ( ( ( ( $row['work_qty'] * $ie ) / $FULL_TIME ) / $row['workers'] ) + ( ( ( $row['over_qty'] * $ie ) / $row['ot_hr'] ) / $row['ot_wk'] ) );
    } else {
        $day_su += ( ( ( $row['work_qty'] * $ie ) / $FULL_TIME ) / $row['workers'] );
    }

}

$rate = ( ( $day_su > 0 ) ? ( $day_su / $pdt_range ) : $df_rate ) * $schedule_var ;

$this->up_success_rate($line_id,$rate);

// echo '<p>';
return $rate * $FULL_TIME;

} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->get_sch_ets($id)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_sch_ets($id) {

$sql = $this->sql;

$q_str = "SELECT `out_date` 
FROM `saw_out_put` 
WHERE `s_id` = '".$id."'
ORDER BY `out_date` ASC  LIMIT 1 , 1 ;";
// echo $q_str.'<br>';
if (!$q_result = $sql->query($q_str)) {
    $this->msg->add("Error ! 無法更新資料庫內容.");
    $this->msg->merge($sql->msg);
    return false;    
}

if( $row = mysql_fetch_array($q_result) ) {
    return $row['out_date'];
} else {
    return 0;
}

} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->chk_saw_out_put($id)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function chk_saw_out_put($id) {

$sql = $this->sql;

$q_str = "SELECT `id` FROM `saw_out_put` WHERE `s_id` = '".$id."' ;";
$q_result = $sql->query($q_str);
if($ord_sch = $sql->fetch($q_result)){
    return true;
}
return false;

} // end func




#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->chk_saw_out_put_line($line_id)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function chk_saw_out_put_line($line_id) {

$sql = $this->sql;

$q_str = "SELECT `id` FROM `saw_out_put` WHERE `line_id` = '".$line_id."' AND `qty` > 0 ;";
$q_result = $sql->query($q_str);
if($ord_sch = $sql->fetch($q_result)){
    return false;
}
return true;

} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->set_schedule_status($id)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function set_schedule_status($id,$status=2) {

$sql = $this->sql;

$q_str = "UPDATE `schedule` SET `status` = '".$status."' WHERE `id` = '".$id."' ;";
// echo $q_str.'<br>';
if (!$q_result = $sql->query($q_str)) {
    $this->msg->add("Error ! 無法更新資料庫內容.");
    $this->msg->merge($sql->msg);
    return false;    
}

return true;

} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->set_schedule_ets_etf($id)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function set_schedule_ets_etf($id) {

$sql = $this->sql;

$q_str = "SELECT min( `out_date` ) AS `ets` , max( `out_date` ) AS `etf` FROM `saw_out_put` WHERE `s_id` = '".$id."' ;";
$q_result = $sql->query($q_str);
$out_put = $sql->fetch($q_result);

$q_str = "UPDATE `schedule` SET `ets` = '".$out_put['ets']."' , `etf` = '".$out_put['etf']."' WHERE `id` = '".$id."' ;";
// echo $q_str.'<br>';
if (!$q_result = $sql->query($q_str)) {
    $this->msg->add("Error ! 無法更新資料庫內容.");
    $this->msg->merge($sql->msg);
    return false;
}

return true;

} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->set_saw_out_put_ets_etf($ord_num)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function set_saw_out_put_ets_etf($ord_num) {

$sql = $this->sql;

$q_str = "SELECT min( `out_date` ) AS `out_ets` , max( `out_date` ) AS `out_etf` FROM `saw_out_put` WHERE `ord_num` = '".$ord_num."' ;";
$q_result = $sql->query($q_str);

if (!$out_put = $sql->fetch($q_result)) {
    return false;
}

return $out_put;

} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->up_finish_etf($id,$Date)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function up_finish_etf($id,$rel_etf) {

$sql = $this->sql;

$rel_etf = ( $rel_etf > date('Y-m-d') )? date('Y-m-d') : $rel_etf ;

$q_str = "UPDATE `schedule` SET `status` = '2' , `rel_etf` = '".$rel_etf."' WHERE `id` = '".$id."' ;";
// echo $q_str.'<br>';
if (!$q_result = $sql->query($q_str)) {
    $this->msg->add("Error ! 無法更新資料庫內容.");
    $this->msg->merge($sql->msg);
    return false;    
}

return $rel_etf;

} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->up_sch($id,$ie,$qty,$ets,$etf)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function up_sch($id,$su,$rel_ets,$rel_etf,$status) {

$sql = $this->sql;

$q_str = "SELECT min( `out_date` ) AS `ets` , max( `out_date` ) AS `etf` FROM `saw_out_put` WHERE `s_id` = '".$id."' ;";
$q_result = $sql->query($q_str);
$out_put = $sql->fetch($q_result);
$ets = !empty($out_put['ets'])? $out_put['ets'] : $rel_ets ;
$etf = !empty($out_put['etf'])? $out_put['etf'] : $rel_etf ;

if( $status == '2' ) {
    if( $etf < $rel_etf ){
        $rel_etf = $etf;
    }
}
$q_str = "UPDATE `schedule` SET `su` = '".$su."' , `ets` = '".$ets."' , `etf` = '".$etf."' , `rel_ets` = '".$rel_ets."' , `rel_etf` = '".$rel_etf."' WHERE `id` = '".$id."' ;";
// echo $q_str.'<br>';
if (!$q_result = $sql->query($q_str)) {
    $this->msg->add("Error ! 無法更新資料庫內容.");
    $this->msg->merge($sql->msg);
    return false;    
}

return true;

} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->up_success_rate($line_id,$pdt_pp)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function up_success_rate($line_id,$pdt_pp) {

$sql = $this->sql;

// $q_str = "UPDATE `pdt_saw_line` SET `success_rate` = '".($pdt_pp / $GLOBALS['DAY_SU'])."' WHERE `id` = '".$line_id."' ;";
$q_str = "UPDATE `pdt_saw_line` SET `success_rate` = '".($pdt_pp)."' WHERE `id` = '".$line_id."' ;";
// echo $q_str.'<br>';
if (!$q_result = $sql->query($q_str)) {
    $this->msg->add("Error ! 無法更新資料庫內容.");
    $this->msg->merge($sql->msg);
    return false;    
}

return true;

} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->set_pdt_qty($s_id,$nowQty)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function set_pdt_qty($s_id,$nowQty){

    $sql = $this->sql;

    $q_str =  "UPDATE `schedule` SET `pdt_qty` = '".$nowQty."' WHERE id = '".$s_id."'";
    $sql->query($q_str);

}



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->get_work_day($output,$ord_qty,$etf,$avg,$ie,$qty,$pdt_qty)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_work_day($su,$avg){
$w_day = ceil( $su / $avg );
$w_day = ( $w_day <= 0 ) ? 1 : $w_day;
return $w_day;
}


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->chk_work_day($output,$ord_qty,$etf,$avg,$ie,$qty,$pdt_qty)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function chk_work_day($output,$ord_qty,$avg,$ie,$qty,$pdt_qty){

    # 計算LINE剩餘 SU
    $line_su = set_su($ie,$qty) - set_su($ie,$pdt_qty);
    # 全訂單剩餘 SU
    $ord_su = set_su($ie,$ord_qty - $output);
    
    $su = ( $line_su < $ord_su ) ? $line_su : $ord_su ;
    
    # 計算生產時間 / 無條件進位
    $w_day = ceil( $su / $avg );
    $w_day = ( $w_day <= 0 ) ? 1 : $w_day;
    // echo $line_su.' ~ '.$w_day.'<br>';
    return $w_day;
}



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->chk_pdc_status($ord_num)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function chk_pdc_status($ord_num){

$sql = $this->sql;

// $q_str = "SELECT `status` FROM `s_order` WHERE `order_num` = '".$ord_num."' ;";
// $q_result = $sql->query($q_str);
// $row = mysql_fetch_array($q_result);
// $status = $row['status'];


// $q_str = "SELECT sum( `p_qty` ) AS `p_qty` , sum( `ext_qty` ) AS `ext_qty` , sum( `p_qty_done` ) AS `qty_done` FROM `order_partial` WHERE `ord_num` = '".$ord_num."' GROUP BY `ord_num` ;";
$q_str = "
SELECT `s_order`.`qty` , `s_order`.`status` , `pdtion`.`qty_shp` , `pdtion`.`qty_done` , `pdtion`.`ext_qty` 
FROM `s_order`,`pdtion` 
WHERE `s_order`.`order_num` = `pdtion`.`order_num` AND `pdtion`.`order_num` = '".$ord_num."' 
;";

if (!$q_result = $sql->query($q_str)) {
    $this->msg->add("Error ! 無法更新資料庫內容.");
    $this->msg->merge($sql->msg);
    return false;    
}
// echo $q_str.'<br>';
if( $row = mysql_fetch_array($q_result) ) {
    
    if( $row['qty_shp'] > 0 ) {
        $status = 12 ;
    } else if ( $row['qty_done'] >= $row['qty'] ) {
        $status = 10;
    } else if ( $row['qty_done'] > 0 ) {
        $status = 8;
    } else if ( $row['ext_qty'] >= $row['qty'] ) {
        $status = 7;
    } else if ( $row['ext_qty'] > 0 ) {
        $status = 6;
    } else if ( $row['status'] == '-1' && $row['status'] == '5' && $row['status'] == '13' && $row['status'] == '14' ) {
        $status = $row['status'];
    } else {
        $status = $row['status'];
    }

    $q_str =  "UPDATE `s_order` SET `status` = '".$status."' WHERE `order_num` = '".$ord_num."' ;";
    $sql->query($q_str);

}
// echo $q_str.'<br>';
}// end fun

} // end class
?>