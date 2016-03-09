<?php

class PO_SUPL {



# :SQL LINK
function init($sql) {

	$this->msg = new MSG_HANDLE();

	if (!$sql) {
		$this->msg->add("Error ! Can't connect to database.");
		return false;
	}
	
	$this->sql = $sql;
	
	return true;
} # FUN END



function chk_incoming($id){

$sql = $this->sql;

$query = "SELECT `id` FROM `stock_inventory` WHERE `ship_id` = '".$id."' AND `type` = 'i' AND `del_mk` = 'n' ;";

if( !$result = $sql->query($query) ){

	return false;
}

$row_det = $sql->fetch($result);

if(!empty($row_det)){
    return true;
} else {
    return false;
}

} # END



function search_ship($parm){

global $PHP_SELF;

$sql = $this->sql;

$query  = "SELECT distinct `po_supl_ship`.* FROM `po_supl_ship`, `po_supl_ship_det` WHERE `po_supl_ship`.`del_mk` = 'n' ";
$query .= !empty($parm['ship']) ? " AND `po_supl_ship`.`id` LIKE '%".$parm['ship']."%' " : "" ;
$query .= !empty($parm['bl_num']) ? " AND `po_supl_ship`.`bl_num` LIKE '%".$parm['bl_num']."%' " : "" ;
$query .= !empty($parm['po_num']) ? " AND `po_supl_ship_det`.`po_num` LIKE '%".$parm['po_num']."%'" : "" ;
$query .= !empty($parm['org']) ? " AND `po_supl_ship`.`org` LIKE '%".$parm['org']."%' " : "" ;
$query .= !empty($parm['dist']) ? " AND `po_supl_ship`.`dist` LIKE '%".$parm['dist']."%' " : "" ;
$query .= " and `po_supl_ship`.`id` = `po_supl_ship_det`.`po_supl_ship_id`";
$query .= " order by `po_supl_ship`.`id` desc ";
$query .= " ;";

if( $result = $sql->query($query) ){

    $num_rows = mysql_num_rows($result);
    
    $record_begin = $parm['now_num'] * $parm['page_num'];
	$record_begin = $record_begin > $num_rows ? $num_rows : $record_begin;
	
    $query  = "SELECT distinct `po_supl_ship`.*, `user`.`login_id` FROM `po_supl_ship`, `po_supl_ship_det`, `user` WHERE `po_supl_ship`.`del_mk` = 'n' ";
	$query .= !empty($parm['ship']) ? " AND `po_supl_ship`.`id` LIKE '%".$parm['ship']."%' " : "" ;
	$query .= !empty($parm['bl_num']) ? " AND `po_supl_ship``bl_num` LIKE '%".$parm['bl_num']."%' " : "" ;
	$query .= !empty($parm['po_num']) ? " AND `po_supl_ship_det`.`po_num` LIKE '%".$parm['po_num']."%'" : "" ;
	$query .= !empty($parm['org']) ? " AND `po_supl_ship`.`org` LIKE '%".$parm['org']."%' " : "" ;
	$query .= !empty($parm['dist']) ? " AND `po_supl_ship`.`dist` LIKE '%".$parm['dist']."%' " : "" ;
	$query .= " and `po_supl_ship`.`id` = `po_supl_ship_det`.`po_supl_ship_id`";
	$query .= " and `user`.`id` = `po_supl_ship`.`open_user`";
	$query .= " order by `po_supl_ship`.`id` desc ";
	$query .= " limit ".$record_begin.", ".$parm['page_num'];
	$query .= " ;";

    // echo $query.'<br>';
    $result = $sql->query($query);
    
    $rows = array();
    
    while( $row = $sql->fetch($result) ) {

        $row['status_txt'] = $this->get_status($row['status']);

        $rows['ship'][] = $row;
        
    }

    $action = $parm['action'];
    $now_num = $parm['now_num'];
    $page_num = $parm['page_num'];
    
    @$PageTotal = ceil($num_rows / $page_num);
    
    $rows['page'] = '
                              <table width="100%" border="0" cellspacing="0" cellpadding="0" >
                                <tr>
                                  <td align="left" nowrap width="30%">';
    if ( $now_num > 0 ) {
        $rows['page'] .= ' <a href="'.$PHP_SELF.'?PHP_action='.$action.'&M_now_num=0" class="pnum" >最前頁</a> ';
    }
    
    if (($now_num-10)>= 0 ) {
        $rows['page'] .= ' <a href="'.$PHP_SELF.'?PHP_action='.$action.'&M_now_num='.($now_num-10).'" class="pnum" >上10頁</a> ';
    }
    
    if ( $now_num > 0 ) {
        $rows['page'] .= ' <a href="'.$PHP_SELF.'?PHP_action='.$action.'&M_now_num='.($now_num-1).'" class="pnum" >上一頁</a> ';
    }
    
    $rows['page'] .= '</td>
                                  <td align="center" nowrap width="40%">';
    #現在的頁數
    $o=0;
    for ($m=$now_num-2;$m < $PageTotal+1;$m++){
        if ( $m > 0 and $o < 7){
          $CSS = ( $m == $now_num+1 ) ? 'page_num_on' : 'page_num_off' ;
          $dd = ( $m <> 1 ) ? ' . ' : '' ;
          $rows['page'] .= ''.$dd.'<a href="'.$PHP_SELF.'?PHP_action='.$action.'&M_now_num='.($m-1).'" class="'.$CSS.'" title="'.$m.'">'.$m.'</a> ';
                                    $o++;
        }
    }
    
    $rows['page'] .= '</td>
                                  <td align="right" nowrap width="30%">';
                                  
    if ( $PageTotal > ($now_num+1) ) {
        $rows['page'] .= ' <a href="'.$PHP_SELF.'?PHP_action='.$action.'&M_now_num='.($now_num+1).'" class="pnum" >下一頁</a> ';
    }
    
    if ( ($now_num+11) <= $PageTotal ) {
        $rows['page'] .= ' <a href="'.$PHP_SELF.'?PHP_action='.$action.'&M_now_num='.($now_num+10).'" class="pnum" >下10頁</a> ';
    }
    
    if ( $PageTotal > ($now_num+1) ) {
        $rows['page'] .= ' <a href="'.$PHP_SELF.'?PHP_action='.$action.'&M_now_num='.($PageTotal-1).'" class="pnum" >最後頁</a> ';
    }
    
    $rows['page'] .= '</td>
                                  <td colspan="3" align="center">';

    $rows['page'] .= '</td>
                                </tr>
                              </table>
                            ';
    if( !empty($num_rows) ){
        $rows['page_text'] = '
                        <table width="100%" border="0" cellspacing="0" cellpadding="0" class="num_info">
                          <tr>
                            <td> 第 '. ($now_num+1) .' 頁，總計 '. $PageTotal .' 頁 ( 共 ' . $num_rows . ' 筆資料 ) </td>
                          </tr>
                        </table>
                      ';
    } else {
        $rows['page_text'] = '';
    }
    
// print_r($rows);
    return $rows;

}

} # END



function search_po($parm,$po){

global $PHP_SELF;

$sql = $this->sql;

$query  = "SELECT `ap`.* FROM `ap` WHERE `status` = '12' ";
$query .= !empty($parm['po_num']) ? " AND `po_num` LIKE '%".$parm['po_num']."%' " : " AND `ship_status` = '1' " ;
$query .= !empty($parm['ship']) ? " AND `sup_code` = '".$parm['ship']."' " : "" ;
$query .= !empty($parm['finl_dist']) ? " AND `finl_dist` = '".$parm['finl_dist']."' " : "" ;
if( is_array($po) ){
    foreach($po as $val) {
        $query .= !empty($val) ? " AND `po_num` != '".$val."' " : "" ;
    }
}
$query .= " order by id desc";
$query .= " ;";


if( $result = $sql->query($query) ){

    $num_rows = mysql_num_rows($result);
    
    $record_begin = $parm['now_num'] * $parm['page_num'];
    $record_begin = $record_begin > $num_rows ? $num_rows : $record_begin;

    $query  = "SELECT `ap`.* FROM `ap` WHERE `status` = '12' ";
    $query .= !empty($parm['po_num']) ? " AND `po_num` LIKE '%".$parm['po_num']."%' " : " AND `ship_status` = '1' " ;
    $query .= !empty($parm['ship']) ? " AND `sup_code` = '".$parm['ship']."' " : "" ;
    $query .= !empty($parm['finl_dist']) ? " AND `finl_dist` = '".$parm['finl_dist']."' " : "" ;
    if( is_array($po) ){
        foreach($po as $val) {
            $query .= !empty($val) ? " AND `po_num` != '".$val."' " : "" ;
        }
    }
	$query .= " order by id desc";
    $query .= " limit ".($record_begin)." , ".$parm['page_num'];
    $query .= " ;";
    // echo $query.'<br>';
    $result = $sql->query($query);
    
    $rows = array();
    
    while( $row = $sql->fetch($result) ) {

        $query_det  = " SELECT 
                    `ap_det`.`*`,sum(`ap_det`.`po_qty`) as `po_qty` , 
                    `supl`.*
                    
                    FROM 
                    `supl` , `ap_det` 
                    
                    WHERE 
                    `ap_det`.`ap_num` = '".$row['ap_num']."' AND 
                    `supl`.`vndr_no` = '".$row['sup_code']."' 
                    
                    GROUP BY 
                    `ap_det`.`color`,`ap_det`.`size`,`ap_det`.`mat_id` ;";
                        
        $result_det = $sql->query($query_det);

        $row['po_det'] = $sql->fetch($result_det);

        $rows['po'][] = $row;
        
    }

    $action = $parm['action'];
    $now_num = $parm['now_num'];
    $page_num = $parm['page_num'];
    
    @$PageTotal = ceil($num_rows / $page_num);
    
    $rows['page'] = '
                              <table width="100%" border="0" cellspacing="0" cellpadding="0" >
                                <tr>
                                  <td align="left" nowrap width="30%">';
    if ( $now_num > 0 ) {
        $rows['page'] .= ' <a href="'.$PHP_SELF.'?PHP_action='.$action.'&M_now_num=0" class="pnum" >最前頁</a> ';
    }
    
    if (($now_num-10)>= 0 ) {
        $rows['page'] .= ' <a href="'.$PHP_SELF.'?PHP_action='.$action.'&M_now_num='.($now_num-10).'" class="pnum" >上10頁</a> ';
    }
    
    if ( $now_num > 0 ) {
        $rows['page'] .= ' <a href="'.$PHP_SELF.'?PHP_action='.$action.'&M_now_num='.($now_num-1).'" class="pnum" >上一頁</a> ';
    }
    
    $rows['page'] .= '</td>
                                  <td align="center" nowrap width="40%">';
    #現在的頁數
    $o=0;
    for ($m=$now_num-2;$m < $PageTotal+1;$m++){
        if ( $m > 0 and $o < 7){
          $CSS = ( $m == $now_num+1 ) ? 'page_num_on' : 'page_num_off' ;
          $dd = ( $m <> 1 ) ? ' . ' : '' ;
          $rows['page'] .= ''.$dd.'<a href="'.$PHP_SELF.'?PHP_action='.$action.'&M_now_num='.($m-1).'" class="'.$CSS.'" title="'.$m.'">'.$m.'</a> ';
                                    $o++;
        }
    }
    
    $rows['page'] .= '</td>
                                  <td align="right" nowrap width="30%">';
                                  
    if ( $PageTotal > ($now_num+1) ) {
        $rows['page'] .= ' <a href="'.$PHP_SELF.'?PHP_action='.$action.'&M_now_num='.($now_num+1).'" class="pnum" >下一頁</a> ';
    }
    
    if ( ($now_num+11) <= $PageTotal ) {
        $rows['page'] .= ' <a href="'.$PHP_SELF.'?PHP_action='.$action.'&M_now_num='.($now_num+10).'" class="pnum" >下10頁</a> ';
    }
    
    if ( $PageTotal > ($now_num+1) ) {
        $rows['page'] .= ' <a href="'.$PHP_SELF.'?PHP_action='.$action.'&M_now_num='.($PageTotal-1).'" class="pnum" >最後頁</a> ';
    }
    
    $rows['page'] .= '</td>
                                  <td colspan="3" align="center">';

    $rows['page'] .= '</td>
                                </tr>
                              </table>
                            ';
    if( !empty($num_rows) ){
        $rows['page_text'] = '
                        <table width="100%" border="0" cellspacing="0" cellpadding="0" class="num_info">
                          <tr>
                            <td> 第 '. ($now_num+1) .' 頁，總計 '. $PageTotal .' 頁 ( 共 ' . $num_rows . ' 筆資料 ) </td>
                          </tr>
                        </table>
                      ';
    } else {
        $rows['page_text'] = '';
    }
    
// print_r($rows);
    return $rows;

}

} # END



function append_po($parm){

$sql = $this->sql;

$ship_id = get_insert_id('po_supl_ship');

$query = " INSERT INTO `po_supl_ship` SET
`id`            =    '".($ship_id)."' ,
`open_date`     =    NOW() ,
`open_user`     =    '".($_SESSION['USER']['ADMIN']['id'])."' ,
`last_up_date`  =    NOW() ,
`last_up_user`  =    '".($_SESSION['USER']['ADMIN']['id'])."' 
 ; ";

if( $result = $sql->query($query) ){

    foreach( $parm as $key ) {
    
        $ship_det_id = get_insert_id('po_supl_ship_det');

        $query = " INSERT INTO `po_supl_ship_det` SET
        `id`                =    '".($ship_det_id)."' ,
        `po_supl_ship_id`   =    '".($ship_id)."' ,
        `po_num`            =    '".($key)."' ,
        `open_date`         =    NOW() ,
        `open_user`         =    '".($_SESSION['USER']['ADMIN']['id'])."' , 
        `last_up_date`      =    NOW() , 
        `last_up_user`      =    '".($_SESSION['USER']['ADMIN']['id'])."' 
        ; ";

        if( !$result = $sql->query($query) ){
        
            return false;

        }

    }

    return $ship_id;
    
} else {
    
    return false;
    
}

} # END



function get_shipping($num=''){

$sql = $this->sql;

$query  = " SELECT * 
            FROM `po_supl_ship` 
            WHERE `po_supl_ship`.`id` = '".$num."' ";

$query .= " ;";

// echo $query.'<br>';

if( $result = $sql->query($query) ){

    $po_supl_ship_rows = array();

    $po_supl_ship_rows = $sql->fetch($result);
    
    # GET USER
    if ( $po_supl_ship_rows['submit_user'] ) {
        $po_supl_ship_rows['submit_user'] = get_user($po_supl_ship_rows['submit_user']);
    } else {
        $po_supl_ship_rows['open_user'] = get_user($po_supl_ship_rows['open_user']);
        $po_supl_ship_rows['last_up_user'] = get_user($po_supl_ship_rows['last_up_user']);
    }

}

$query  = " SELECT * , REPLACE(`po_num`,'O','A') as `ap_num`
            FROM `po_supl_ship_det` 
            WHERE `po_supl_ship_id` = '".$num."' AND 
            `del_mk` = 'n' ";
$query .= " ;";

// echo $query.'<br>';

if( $result = $sql->query($query) ){

    $po_supl_ship_det_rows = array();

    while( $row = $sql->fetch($result) ) {
        // 物料清單
        $query_det  = " 
        SELECT 
        `ap`.`id` as `ap_id`,
        `ap_det`.`*`,sum(`ap_det`.`po_qty`) as `po_qtys` , 
        `supl`.*

        FROM 
        `ap` , `ap_det` , `supl`

        WHERE 
        `ap`.`ap_num` = `ap_det`.`ap_num` AND 
        `ap`.`sup_code` = `supl`.`vndr_no` AND 
        `ap_det`.`ap_num` = '".$row['ap_num']."' 

        GROUP BY 
        `ap_det`.`color`,`ap_det`.`size`,`ap_det`.`mat_id` ;";

        $result_det = $sql->query($query_det);
        
        $rows_det = array();
        $m_i = 0;
		while( $row_det = $sql->fetch($result_det) ) {
           
            # 細項出貨明細
			$q_str = "select id, mat_id, color, size, qty, po_unit, c_no, r_no, l_no, gw, nw, c_o 
					  from po_supl_ship_link
					  where del_mk = 'n' and po_supl_ship_det_id = ".$row['id']." and mat_cat = '".$row_det['mat_cat']."' AND 
							mat_id = '".$row_det['mat_id']."' AND color = '".$row_det['color']."' AND 
							size = '".$row_det['size']."'";
			
			$s_det_link = $sql->query($q_str);
            while($s_link_row = $sql->fetch($s_det_link)){
				$row_det['po_supl_ship_link'][] = $s_link_row;
			}
			
			// 物料資訊
            $query_det_link  = "SELECT * FROM `".( $row_det['mat_cat'] == 'l' ? "lots" : "acc" )."` WHERE `".( $row_det['mat_cat'] == 'l' ? "lots" : "acc" )."`.`id` = '".$row_det['mat_id']."'  ;";
            $result_det_link = $sql->query($query_det_link);
            $row_det['det_link'] = $sql->fetch($result_det_link);
            
            // 核可數量
            $query_po_supl_ship_qtty_shipped_sum  = "
            SELECT DISTINCT 
            sum(qty) as `qtty_shipped_qty`

            FROM 
            `po_supl_ship_link`
            
            WHERE 
            `ap_id` = '".$row_det['ap_id']."' AND 
            `mat_cat` = '".$row_det['mat_cat']."' AND 
            `mat_id` = '".$row_det['mat_id']."' AND 
            `color` = '".$row_det['color']."' AND 
            `size` = '".$row_det['size']."' AND 
            `status` = '1' AND 
            `del_mk` = 'n' 

            ;";
            // echo $query_po_supl_ship_qtty_shipped_sum.'<br>';
            $result_qtty_shipped_link = $sql->query($query_po_supl_ship_qtty_shipped_sum);
            $qtty_shipped = $sql->fetch($result_qtty_shipped_link);
            $row_det['qtty_shipped_qty'] = $qtty_shipped['qtty_shipped_qty'];
            
            // 出貨數量
            $query_po_supl_ship_det_sum  = "
            SELECT DISTINCT 
            sum(qty) as `ship_qty`
            
            FROM 
            `po_supl_ship_link`
            
            WHERE 
            `po_supl_ship_det_id` = '".$row['id']."' AND 
            `mat_cat` = '".$row_det['mat_cat']."' AND 
            `mat_id` = '".$row_det['mat_id']."' AND 
            `color` = '".$row_det['color']."' AND 
            `size` = '".$row_det['size']."' AND 
            `del_mk` = 'n' 

            ;";
            // echo $query_po_supl_ship_det_sum.'<br>';
            $result_det_link = $sql->query($query_po_supl_ship_det_sum);
            $ship_qty = $sql->fetch($result_det_link);
            // echo $ship_qty['ship_qty'].'<br>';
            $row_det['ship_qty'] = $ship_qty['ship_qty'];
            
            $row_det['ship_total_qty'] = $row_det['ship_qty'] + $row_det['qtty_shipped_qty'];
			$row_det['i'] = $m_i;
			$m_i++;
			
            $rows_det[] = $row_det;

        }

        $row['po_det'] = $rows_det;
    
        $po_supl_ship_det_rows[] = $row;
    
    }

}


$rows = array();
$rows['po_supl_ship'] = $po_supl_ship_rows;
$rows['po_supl_ship_det'] = $po_supl_ship_det_rows;
// $rows['po_supl_ship_link'] = $po_supl_ship_link_rows;

return $rows;

} # END



function supl_ship_up($parm){

$sql = $this->sql;

$user_id = $_SESSION['USER']['ADMIN']['id'];

$query = " UPDATE `po_supl_ship` SET 
    `bl_num`            = '".($parm['bl_num'])."' , 
    `carrier_num`       = '".($parm['carrier_num'])."' , 
    `org`           = '".($parm['org'])."' , 
    `dist`          = '".($parm['dist'])."' , 
    `ship_way`      = '".($parm['ship_way'])."' , 
    `ex_cmpy`       = '".($parm['ex_cmpy'])."' , 
    `ship_date`     = '".($parm['ship_date'])."' , 
    `ship_eta_date` = '".($parm['ship_eta_date'])."' , 
    `last_up_date`  = NOW() , 
    `last_up_user`  = '".($user_id)."' 
    WHERE `id`      = '".($parm['id'])."' ;";

if( $result = $sql->query($query) ){
    return true;
}

return false;

} # END



function in_parameter($parameter_id,$key){

$sql = $this->sql;

$query = "SELECT `key` FROM `parameter_det` WHERE `parameter_id` = '".$parameter_id."' AND `key` = '".$key."' ;";

if( !$result = $sql->query($query) ){

	return false;
}

$row_det = $sql->fetch($result);

if(!empty($row_det)){
    return true;
} else {
    return false;
}

} # END



function select_supl($name,$select='',$css='',$onChange='',$width=''){

$sql = $this->sql;

$query = "SELECT `vndr_no` as `key` ,`supl_s_name` as `name` FROM `supl` 

ORDER BY `supl_s_name` ASC  ;";
// WHERE `ac_pay` > 0 OR `jan` > 0 OR `feb` > 0 OR `mar` > 0 OR `apr` > 0 OR `may` > 0 OR `jun` > 0 OR `july` > 0 OR `aug` > 0 OR `sep` > 0 OR `oct` > 0 OR `nev` > 0 OR `dec` > 0 OR `used` > 0 
if( !$result = $sql->query($query) ){

	return false;
    
}

$rows = array();

while( $row_det = $sql->fetch() ) {

    $rows[] = $row_det;

}

$select = get_select($rows,$name,$select,$css,$onChange,$width);

return $select;

} # END



function select_parameter($parameter_id,$name,$select='',$css='',$onChange='',$width=''){

$sql = $this->sql;

$query = "SELECT `key`,`name` FROM `parameter_det` WHERE `parameter_id` = '".$parameter_id."' ORDER BY `sort` ASC  ;";

if( !$result = $sql->query($query) ){

	return false;
    
}

$rows = array();

while( $row_det = $sql->fetch() ) {

    $rows[] = $row_det;

}

$select = get_select($rows,$name,$select,$css,$onChange,$width);

return $select;

} # END



function supl_ship_det_edit($ship_id){

$sql = $this->sql;

$query  = " SELECT * 
            FROM `po_supl_ship_det` 
            WHERE `id` = '".$ship_id."' ";
$query .= " ;";

if( $result = $sql->query($query) ){

$po_supl_ship_det_rows['po_supl_ship_det'] = $sql->fetch($result);

$arows = array();
$rows = array();
$query  = "SELECT `ap`.* FROM `ap` WHERE `ap`.`po_num` = '".$po_supl_ship_det_rows['po_supl_ship_det']['po_num']."' ";
// echo $query.'<br>';

$result = $sql->query($query);
$rows = $sql->fetch($result);
$po_supl_ship_det_rows['po'] = $rows;

$query  = " 
SELECT 
`ap_det`.`*`,sum(`ap_det`.`po_qty`) as `po_qtys` 

FROM 
`ap_det` 

WHERE 
`ap_det`.`ap_num` = '".$rows['ap_num']."'

GROUP BY 
`ap_det`.`color`,`ap_det`.`size`,`ap_det`.`mat_id`

;";

$result = $sql->query($query);
// echo $query.'<br>';
$rows_det = array();
# 判斷是否有驗收數量
$i_qty = 0;
while( $row = $sql->fetch($result) ) {
    // 主副料說明
    $query_det_link  = "
    SELECT 
    ".( $row['mat_cat'] == 'l' ? "lots_name,des,comp,specify,width" : "acc_name,des,specify" )." 
    
    FROM 
    `".( $row['mat_cat'] == 'l' ? "lots" : "acc" )."` 
    
    WHERE 
    `id` = '".$row['mat_id']."' 
    ;";
    
    $row['po_ap_id'] = $rows['id'];
    
    $result_det_link = $sql->query($query_det_link);
    $row['det_des'] = $sql->fetch($result_det_link);

    $query_po_supl_ship_qtty_shipped_sum  = "
    SELECT DISTINCT 
    sum(qty) as qtty_shipped_qty

    FROM 
    `po_supl_ship_link`
    
    WHERE 
    `ap_id` = '".$row['po_ap_id']."' AND 
    `mat_cat` = '".$row['mat_cat']."' AND 
    `mat_id` = '".$row['mat_id']."' AND 
    `color` = '".$row['color']."' AND 
    `size` = '".$row['size']."' AND 
    `status` = '1' AND 
    `del_mk` = 'n'
    ;";
    
    $result_qtty_shipped_link = $sql->query($query_po_supl_ship_qtty_shipped_sum);
    $qtty_shipped = $sql->fetch($result_qtty_shipped_link);
    $row['qtty_shipped_qty'] = $qtty_shipped['qtty_shipped_qty'];
    
    $query_po_supl_ship_det_sum  = "
    SELECT DISTINCT 
    sum(qty) as ship_qty
    
    FROM 
    `po_supl_ship_link`
    
    WHERE 
    `po_supl_ship_det_id` = '".$ship_id."' AND 
    `mat_cat` = '".$row['mat_cat']."' AND 
    `mat_id` = '".$row['mat_id']."' AND 
    `color` = '".$row['color']."' AND 
    `size` = '".$row['size']."' AND 
    `status` = '0' AND 
    `del_mk` = 'n'
    ;";
    // echo $query_po_supl_ship_det_sum.'<br>';
    $result_det_link = $sql->query($query_po_supl_ship_det_sum);
    $ship_qty = $sql->fetch($result_det_link);
    $row['ship_qty'] = $ship_qty['ship_qty'];
    
    $row['ship_total_qty'] = $row['ship_qty'] + $row['qtty_shipped_qty'];
    
    $query_po_supl_ship_link  = "
    SELECT  
    `id` , `qty` , `c_no` , `r_no` , `l_no` , `gw` , `nw` , `c_o` 
    
    FROM 
    `po_supl_ship_link`
    
    WHERE 
    `po_supl_ship_det_id` = '".$ship_id."' AND 
    `mat_cat` = '".$row['mat_cat']."' AND 
    `mat_id` = '".$row['mat_id']."' AND 
    `color` = '".$row['color']."' AND 
    `size` = '".$row['size']."' AND 
    `status` = '0' AND 
    `del_mk` = 'n' 
    ORDER BY `c_no`,`l_no`,`r_no` ASC  
    ;";
    
    $result_link = $sql->query($query_po_supl_ship_link);
    $ship_link = array();

    while( $row_link = $sql->fetch($result_link) ) {

        $query_stock_inventory = " SELECT `id` FROM `stock_inventory` WHERE `ship_link_id` = '".$row_link['id']."' ;";

        $result_stock_inventory = $sql->query($query_stock_inventory);

		if( $row_stock_inventory = $sql->fetch($result_stock_inventory) ){
            $row_link['in'] = 1;
        }
    
        $ship_link[] = $row_link;
        $i_qty += $row_link['qty'];
    }
    $row['link'] = $ship_link;

    $rows_det[] = $row;

}

$po_supl_ship_det_rows['po_det'] = $rows_det;
$po_supl_ship_det_rows['i_qty'] = $i_qty;

}

return $po_supl_ship_det_rows;

} # END





function supl_ship_link_add($parm){

$sql = $this->sql;

$link_id = get_insert_id('po_supl_ship_link');

$query = " INSERT INTO `po_supl_ship_link` SET
`id`                    =    '".($link_id)."' ,
`po_supl_ship_id`       =    '".($parm['po_supl_ship_id'])."' ,
`po_supl_ship_det_id`   =    '".($parm['po_supl_ship_det_id'])."' ,
`ap_id`                 =    '".($parm['ap_id'])."' ,
`mat_cat`               =    '".($parm['mat_cat'])."' ,
`mat_id`                =    '".($parm['mat_id'])."' ,
`color`                 =    '".($parm['color'])."' ,
`size`                  =    '".($parm['size'])."' ,
`qty`                   =    '".($parm['qty'])."' ,
`po_unit`               =    '".($parm['po_unit'])."' ,

`c_no`                  =    '".($parm['c_no'])."' ,
`r_no`                  =    '".($parm['r_no'])."' ,
`l_no`                  =    '".($parm['l_no'])."' ,
`gw`                    =    '".($parm['gw'])."' ,
`nw`                    =    '".($parm['nw'])."' ,
`c_o`                   =    '".($parm['c_o'])."' ,
`open_date`             =    NOW() ,
`open_user`             =    '".($_SESSION['USER']['ADMIN']['id'])."' , 
`last_up_date`          =    NOW() , 
`last_up_user`          =    '".($_SESSION['USER']['ADMIN']['id'])."' , 
`status`                =    '0' , 
`del_mk`                =    'n' 
 ; ";

if( $result = $sql->query($query) ){

    return $link_id;
    
} else {
    
    return false;
    
}

} # END





function supl_ship_link_up($parm){

$sql = $this->sql;

$query = " UPDATE `po_supl_ship_link` SET 
`c_no`                  =    '".($parm['c_no'])."' ,
`r_no`                  =    '".($parm['r_no'])."' ,
`l_no`                  =    '".($parm['l_no'])."' ,
`qty`                   =    '".($parm['qty'])."' ,
`gw`                    =    '".($parm['gw'])."' ,
`nw`                    =    '".($parm['nw'])."' ,
`c_o`                   =    '".($parm['c_o'])."' 
WHERE `id`              =    '".($parm['id'])."' 
 ; ";

if( $result = $sql->query($query) ){

    return true;
    
} else {
    
    return false;
    
}

} # END






function supl_ship_det_up($parm){

$sql = $this->sql;

$query = "  UPDATE `po_supl_ship_det` 
            SET `invoice_num` = '".($parm['invoice_num'])."' , `pi_num` = '".($parm['pi_num'])."' , `last_up_date` = NOW() , `last_up_user` = '".($_SESSION['USER']['ADMIN']['id'])."' 
            WHERE `id` = '".($parm['po_supl_ship_det_id'])."' ;";


if( $result = $sql->query($query) ){

    return true;
    
} else {
    
    return false;
    
}

} # END



function supl_ship_det_del($id){

$sql = $this->sql;

$query = " UPDATE `po_supl_ship_det` SET `last_up_date` = NOW() , `last_up_user` = '".($_SESSION['USER']['ADMIN']['id'])."' , `del_mk` = 'y' WHERE `id` = '".($id)."' ;";

if( $result = $sql->query($query) ){
    return true;
}
return false;
    
} # END



function supl_ship_link_del($id){

$sql = $this->sql;

$query = " UPDATE `po_supl_ship_link` SET `last_up_date` = NOW() , `last_up_user` = '".($_SESSION['USER']['ADMIN']['id'])."' , `del_mk` = 'y' WHERE `id` = '".($id)."' ;";

if( $result = $sql->query($query) ){
    return true;
}
return false;
    
} # END



function supl_ship_submit($id){

$sql = $this->sql;

$query = " UPDATE `po_supl_ship` SET `submit_date` = NOW() , `submit_user` = '".($_SESSION['USER']['ADMIN']['id'])."' , `status` = '1' WHERE `id` = '".($id)."' ;";

if( $result = $sql->query($query) ){

    $query = " UPDATE `po_supl_ship_det` SET `status` = '1' WHERE `po_supl_ship_id` = '".($id)."' ;";
    
    if( $result = $sql->query($query) ){

        $query = " UPDATE `po_supl_ship_link` SET `status` = '1' WHERE `po_supl_ship_id` = '".($id)."' ;";
        
        if( $result = $sql->query($query) ){

            return true;
        }
    }
}

return false;
    
} # END




function supl_ship_revise($id){

$sql = $this->sql;

$query = " UPDATE `po_supl_ship` SET `last_up_date` = NOW() , `last_up_user` = '".($_SESSION['USER']['ADMIN']['id'])."' , `submit_date` = '' , `submit_user` = '' , `ver` = `ver`+1 , `status` = '0' WHERE `id` = '".($id)."' ;";

if( $result = $sql->query($query) ){

    $query = " UPDATE `po_supl_ship_det` SET `status` = '0' WHERE `po_supl_ship_id` = '".($id)."' ;";
    
    if( $result = $sql->query($query) ){

        $query = " UPDATE `po_supl_ship_link` SET `status` = '0' WHERE `po_supl_ship_id` = '".($id)."' ;";
        
        if( $result = $sql->query($query) ){

            return true;
        }
    }
}

return false;
    
} # END




function ship_chg_status($id,$status){

$sql = $this->sql;

$query = " UPDATE `ap` SET `ship_status` = '".($status)."' WHERE `id` = '".($id)."' ;";

if( $result = $sql->query($query) ){

    return true;
}

return false;
    
} # END










































































function get_unconfirmed($supl=''){

$sql = $this->sql;

// $query  = "SELECT `ap`.* FROM `ap` WHERE `ship_status` = '1' ";
// $query .= !empty($supl) ? " AND `sup_code` = '".$supl."' " : "" ;
// $query .= " ;";


$query  = "SELECT *,REPLACE(`po_num`,'O','A') as `ap_num` , SUBSTRING(`ship_date`,'1','4') as `yys` , SUBSTRING(`ship_date`,'6','2') as `mms` , SUBSTRING(`ship_date`,'9','2') as `dds` FROM `po_supl_ship` WHERE `status` = '0' and `del_mk` = 'n' ";

// echo $query.'<br>';

if( $result = $sql->query($query) ){

    $rows = array();
    
    while( $row = $result->fetch_assoc() ) {
    
        // $query_ap  = "SELECT `ap`.* FROM `ap` WHERE `ship_status` = '1' ";
        // $query_ap .= !empty($supl) ? " AND `sup_code` = '".$supl."' " : "" ;
        // $query_ap .= " ;";
           
        // $result_ap = $sql->query($query_ap);
        $row['org_name'] = $this->in_parameter(1,$row['org']) ? $this->get_parameter(1,$row['org']) : $row['org'] ;
        $row['dist_name'] = $this->get_parameter(2,$row['dist']);
        

        $query_det  = " SELECT 
                    `ap_det`.`*`,sum(`ap_det`.`po_qty`) as `po_qty` , `ap_det`.`id` as `ap_det_id` , 
                    `supl`.*
                    
                    FROM 
                    `supl` , `ap_det` 
                    
                    WHERE 
                    `ap_det`.`ap_num` = '".$row['ap_num']."' AND 
                    `supl`.`vndr_no` = '".$supl."' 
                    
                    GROUP BY 
                    `ap_det`.`color`,`ap_det`.`size`,`ap_det`.`mat_id` ;";
                        
        $result_det = $sql->query($query_det);
        
        $rows_det = array();
        while( $row_det = $result_det->fetch_assoc() ) {
        
            $query_det_link  = "SELECT * FROM `".( $row_det['mat_cat'] == 'l' ? "lots" : "acc" )."` WHERE `".( $row_det['mat_cat'] == 'l' ? "lots" : "acc" )."`.`id` = '".$row_det['mat_id']."'  ;";
            $result_det_link = $sql->query($query_det_link);
            $row_det['det_link'] = $result_det_link->fetch_assoc();
            
            // 語系轉碼
            if ( $row_det['mat_cat'] == 'l' ){
                $row_det['det_link']['lots_name'] = iconv("BIG5","UTF-8",$row_det['det_link']['lots_name']);
                $row_det['det_link']['des'] = iconv("BIG5","UTF-8",$row_det['det_link']['des']);
                $row_det['det_link']['comp'] = iconv("BIG5","UTF-8",$row_det['det_link']['comp']);
                $row_det['det_link']['specify'] = iconv("BIG5","UTF-8",$row_det['det_link']['specify']);
                $row_det['det_link']['detrition'] = iconv("BIG5","UTF-8",$row_det['det_link']['detrition']);
                $row_det['det_link']['width'] = iconv("BIG5","UTF-8",$row_det['det_link']['width']);
                $row_det['det_link']['weight'] = iconv("BIG5","UTF-8",$row_det['det_link']['weight']);
                $row_det['det_link']['cons'] = iconv("BIG5","UTF-8",$row_det['det_link']['cons']);
            } else {
                $row_det['det_link']['acc_name'] = iconv("BIG5","UTF-8",$row_det['det_link']['acc_name']);
                $row_det['det_link']['des'] = iconv("BIG5","UTF-8",$row_det['det_link']['des']);
                $row_det['det_link']['specify'] = iconv("BIG5","UTF-8",$row_det['det_link']['specify']);
            }

            $query_det_link  = "SELECT SUM(`qty`) as `qty` FROM `po_supl_ship_det` WHERE `po_supl_ship_id` = '".$row['id']."' AND `status` = '0' AND `del_mk` = 'n' ;";
            // echo $query_det_link.'<br>';
            $result_det_link = $sql->query($query_det_link);
            $row_ship_det = $result_det_link->fetch_assoc();
            $row_det['ship_qty'] = $row_ship_det['qty'];

            // 語系轉碼
            $row_det['dm_way'] = iconv("BIG5","UTF-8",$row_det['dm_way']);
            $row_det['color'] = iconv("BIG5","UTF-8",$row_det['color']);
            
            $rows_det[] = $row_det;

        }

        $row['po_det'] = $rows_det;


        
        $rows[] = $row;
        
    }

    return $rows;

}

} # END



function get_po_num($po_num){

$sql = $this->sql;

$arows = array();
$rows = array();
$query  = "SELECT `ap`.* FROM `ap` WHERE `ap`.`po_num` = '".$po_num."' ";
$result = $sql->query($query);
$rows = $result->fetch_assoc();

$query  = " 
SELECT 
`ap_det`.`*`,sum(`ap_det`.`po_qty`) as `po_qty` 

FROM 
`ap_det` 

WHERE 
`ap_det`.`ap_num` = '".$rows['ap_num']."'

GROUP BY 
`ap_det`.`color`,`ap_det`.`size`,`ap_det`.`mat_id`

;";

$result = $sql->query($query);

$rows_det = array();
while( $row = $result->fetch_assoc() ) {


    $query_det_link  = "
    SELECT 
    ".( $row['mat_cat'] == 'l' ? "lots_name,des,comp,specify,width" : "acc_name,des,specify" )." 
    
    FROM 
    `".( $row['mat_cat'] == 'l' ? "lots" : "acc" )."` 
    
    WHERE 
    `id` = '".$row['mat_id']."' 
    ;";
    
    $result_det_link = $sql->query($query_det_link);
    $row['det_link'] = $result_det_link->fetch_assoc();

    
    // 語系轉碼
    if ( $row['mat_cat'] == 'l' ){
        $row['det_link']['lots_name'] = iconv("BIG5","UTF-8",$row['det_link']['lots_name']);
        $row['det_link']['des'] = iconv("BIG5","UTF-8",$row['det_link']['des']);
        $row['det_link']['comp'] = iconv("BIG5","UTF-8",$row['det_link']['comp']);
        $row['det_link']['specify'] = iconv("BIG5","UTF-8",$row['det_link']['specify']);
        $row['det_link']['detrition'] = iconv("BIG5","UTF-8",$row['det_link']['detrition']);
        $row['det_link']['width'] = iconv("BIG5","UTF-8",$row['det_link']['width']);
        $row['det_link']['weight'] = iconv("BIG5","UTF-8",$row['det_link']['weight']);
        $row['det_link']['cons'] = iconv("BIG5","UTF-8",$row['det_link']['cons']);
    } else {
        $row['det_link']['acc_name'] = iconv("BIG5","UTF-8",$row['det_link']['acc_name']);
        $row['det_link']['des'] = iconv("BIG5","UTF-8",$row['det_link']['des']);
        $row['det_link']['specify'] = iconv("BIG5","UTF-8",$row['det_link']['specify']);
    }
    
    
    $query_po_supl_ship_qtty_shipped_sum  = "
    SELECT DISTINCT 
    sum(qty) as qtty_shipped_qty

    FROM 
    `po_supl_ship_det`
    
    WHERE 
    `mat_cat` = '".$row['mat_cat']."' AND 
    `mat_id` = '".$row['mat_id']."' AND 
    `color` = '".$row['color']."' AND 
    `size` = '".$row['size']."' AND 
    `status` = '1' AND 
    `del_mk` = 'n'
    ;";
    // echo $query_po_supl_ship_det_sum.'<br>';
    $result_qtty_shipped_link = $sql->query($query_po_supl_ship_qtty_shipped_sum);
    $qtty_shipped = $result_qtty_shipped_link->fetch_assoc();
    $row['qtty_shipped_qty'] = $qtty_shipped['qtty_shipped_qty'];
    
    $query_po_supl_ship_det_sum  = "
    SELECT DISTINCT 
    sum(qty) as ship_qty
    
    FROM 
    `po_supl_ship_det`
    
    WHERE 
    `po_supl_ship_id` = '".$id."' AND 
    `mat_cat` = '".$row['mat_cat']."' AND 
    `mat_id` = '".$row['mat_id']."' AND 
    `color` = '".$row['color']."' AND 
    `size` = '".$row['size']."' AND 
    `status` = '0' AND 
    `del_mk` = 'n'
    ;";
    // echo $query_po_supl_ship_det_sum.'<br>';
    $result_det_link = $sql->query($query_po_supl_ship_det_sum);
    $ship_qty = $result_det_link->fetch_assoc();
    $row['ship_qty'] = $ship_qty['ship_qty'];
    
    $row['ship_total_qty'] = $row['ship_qty'] + $row['qtty_shipped_qty'];
    
    $query_po_supl_ship_det  = "
    SELECT  
    `id` , `qty` , `c_no` , `r_no` , `l_no` , `gw` , `nw` , `c_o` 
    
    FROM 
    `po_supl_ship_det`
    
    WHERE 
    `po_supl_ship_id` = '".$id."' AND 
    `mat_cat` = '".$row['mat_cat']."' AND 
    `mat_id` = '".$row['mat_id']."' AND 
    `color` = '".$row['color']."' AND 
    `size` = '".$row['size']."' AND 
    `status` = '0' AND 
    `del_mk` = 'n' 
    ;";
    // echo $query_po_supl_ship_det.'<br>';
    $result_det_link = $sql->query($query_po_supl_ship_det);
    $ship_det = array();
    while( $row_det = $result_det_link->fetch_assoc() ) {
        $ship_det[] = $row_det;
    }
    $row['dets'] = $ship_det;
    
    // 語系轉碼
    $row['dm_way'] = iconv("BIG5","UTF-8",$row['dm_way']);
    $row['color'] = iconv("BIG5","UTF-8",$row['color']);
    
    $rows_det[] = $row;

}

$rows['po_det'] = $rows_det;

$arows[] = $rows;

return $arows;

} # END





function get_po_list($po_num){

$sql = $this->sql;

$query  = "SELECT *,REPLACE(`po_num`,'O','A') as `ap_num` , SUBSTRING(`ship_date`,'1','4') as `yys` , SUBSTRING(`ship_date`,'6','2') as `mms` , SUBSTRING(`ship_date`,'9','2') as `dds` FROM `po_supl_ship` WHERE `po_num` = '".$po_num."' AND `del_mk` = 'n' ORDER BY `status` DESC ;";

// echo $query.'<br>';

if( $result = $sql->query($query) ){

    $rows = array();
    
    while( $row = $result->fetch_assoc() ) {
    
        $row['org_name'] = $this->in_parameter(1,$row['org']) ? $this->get_parameter(1,$row['org']) : $row['org'] ;
        $row['dist_name'] = $this->get_parameter(2,$row['dist']);

        $query_det  = " SELECT 
                    `ap_det`.`*`,sum(`ap_det`.`po_qty`) as `po_qty` , `ap_det`.`id` as `ap_det_id` , 
                    `supl`.*
                    
                    FROM 
                    `supl` , `ap_det` 
                    
                    WHERE 
                    `ap_det`.`ap_num` = '".$row['ap_num']."' AND 
                    `supl`.`vndr_no` = '".$row['supl_code']."' 
                    
                    GROUP BY 
                    `ap_det`.`color`,`ap_det`.`size`,`ap_det`.`mat_id` ;";
                        
        $result_det = $sql->query($query_det);
        // echo $query_det.'<br>';
        $rows_det = array();
        while( $row_det = $result_det->fetch_assoc() ) {
        
            $query_det_link  = "SELECT * FROM `".( $row_det['mat_cat'] == 'l' ? "lots" : "acc" )."` WHERE `".( $row_det['mat_cat'] == 'l' ? "lots" : "acc" )."`.`id` = '".$row_det['mat_id']."'  ;";
            $result_det_link = $sql->query($query_det_link);
            $row_det['det_link'] = $result_det_link->fetch_assoc();

            $query_det_link  = "SELECT SUM(`qty`) as `qty` FROM `po_supl_ship_det` WHERE `po_supl_ship_id` = '".$row['id']."' AND `del_mk` = 'n' ;";
            // echo $query_det_link.'<br>';
            $result_det_link = $sql->query($query_det_link);
            $row_ship_det = $result_det_link->fetch_assoc();
            $row_det['ship_qty'] = $row_ship_det['qty'];

            $rows_det[] = $row_det;

        }

        $row['po_det'] = $rows_det;
        
        $rows[] = $row;
        
    }

    return $rows;

}

} # END



function get_ship_po($id){

$sql = $this->sql;

$arows = array();
$rows = array();
$query  = "SELECT `ap`.* , `po_supl_ship`.`id` as `po_ship_id` , `po_supl_ship`.`carrier_num` , `po_supl_ship`.`pi_num` , `po_supl_ship`.`invoice_num` , `po_supl_ship`.`org` , `po_supl_ship`.`dist` , `po_supl_ship`.`ship_way` , `po_supl_ship`.`ex_cmpy` , `po_supl_ship`.`ship_date` , `po_supl_ship`.`ship_eta_date` FROM `ap` LEFT JOIN `po_supl_ship` ON ( `ap`.`po_num` = `po_supl_ship`.`po_num` ) WHERE `po_supl_ship`.`id` = '".$id."' AND `po_supl_ship`.`po_num` = `ap`.`po_num` ";
$result = $sql->query($query);
$rows = $result->fetch_assoc();

$query  = " 
SELECT 
`ap_det`.`*`,sum(`ap_det`.`po_qty`) as `po_qty` 

FROM 
`ap_det` 

WHERE 
`ap_det`.`ap_num` = '".$rows['ap_num']."'

GROUP BY 
`ap_det`.`color`,`ap_det`.`size`,`ap_det`.`mat_id`

;";

$result = $sql->query($query);

$rows_det = array();
while( $row = $result->fetch_assoc() ) {

    $query_det_link  = "
    SELECT 
    ".( $row['mat_cat'] == 'l' ? "lots_name,des,comp,specify,width" : "acc_name,des,specify" )." 
    
    FROM 
    `".( $row['mat_cat'] == 'l' ? "lots" : "acc" )."` 
    
    WHERE 
    `id` = '".$row['mat_id']."' 
    ;";
    
    $result_det_link = $sql->query($query_det_link);
    $row['det_link'] = $result_det_link->fetch_assoc();

    // 語系轉碼
    if ( $row['mat_cat'] == 'l' ){
        $row['det_link']['lots_name'] = iconv("BIG5","UTF-8",$row['det_link']['lots_name']);
        $row['det_link']['des'] = iconv("BIG5","UTF-8",$row['det_link']['des']);
        $row['det_link']['comp'] = iconv("BIG5","UTF-8",$row['det_link']['comp']);
        $row['det_link']['specify'] = iconv("BIG5","UTF-8",$row['det_link']['specify']);
        $row['det_link']['detrition'] = iconv("BIG5","UTF-8",$row['det_link']['detrition']);
        $row['det_link']['width'] = iconv("BIG5","UTF-8",$row['det_link']['width']);
        $row['det_link']['weight'] = iconv("BIG5","UTF-8",$row['det_link']['weight']);
        $row['det_link']['cons'] = iconv("BIG5","UTF-8",$row['det_link']['cons']);
    } else {
        $row['det_link']['acc_name'] = iconv("BIG5","UTF-8",$row['det_link']['acc_name']);
        $row['det_link']['des'] = iconv("BIG5","UTF-8",$row['det_link']['des']);
        $row['det_link']['specify'] = iconv("BIG5","UTF-8",$row['det_link']['specify']);
    }
    
    $query_po_supl_ship_qtty_shipped_sum  = "
    SELECT DISTINCT 
    sum(qty) as qtty_shipped_qty

    FROM 
    `po_supl_ship_det`
    
    WHERE 
    `mat_cat` = '".$row['mat_cat']."' AND 
    `mat_id` = '".$row['mat_id']."' AND 
    `color` = '".$row['color']."' AND 
    `size` = '".$row['size']."' AND 
    `status` = '1' AND 
    `del_mk` = 'n'
    ;";
    // echo $query_po_supl_ship_det_sum.'<br>';
    $result_qtty_shipped_link = $sql->query($query_po_supl_ship_qtty_shipped_sum);
    $qtty_shipped = $result_qtty_shipped_link->fetch_assoc();
    $row['qtty_shipped_qty'] = $qtty_shipped['qtty_shipped_qty'];
    
    $query_po_supl_ship_det_sum  = "
    SELECT DISTINCT 
    sum(qty) as ship_qty
    
    FROM 
    `po_supl_ship_det`
    
    WHERE 
    `po_supl_ship_id` = '".$id."' AND 
    `mat_cat` = '".$row['mat_cat']."' AND 
    `mat_id` = '".$row['mat_id']."' AND 
    `color` = '".$row['color']."' AND 
    `size` = '".$row['size']."' AND 
    `status` = '0' AND 
    `del_mk` = 'n'
    ;";
    // echo $query_po_supl_ship_det_sum.'<br>';
    $result_det_link = $sql->query($query_po_supl_ship_det_sum);
    $ship_qty = $result_det_link->fetch_assoc();
    $row['ship_qty'] = $ship_qty['ship_qty'];
    
    $row['ship_total_qty'] = $row['ship_qty'] + $row['qtty_shipped_qty'];
    
    $query_po_supl_ship_det  = "
    SELECT  
    `id` , `qty` , `c_no` , `r_no` , `l_no` , `gw` , `nw` , `c_o` 
    
    FROM 
    `po_supl_ship_det`
    
    WHERE 
    `po_supl_ship_id` = '".$id."' AND 
    `mat_cat` = '".$row['mat_cat']."' AND 
    `mat_id` = '".$row['mat_id']."' AND 
    `color` = '".$row['color']."' AND 
    `size` = '".$row['size']."' AND 
    `status` = '0' AND 
    `del_mk` = 'n' 
    ;";
    // echo $query_po_supl_ship_det.'<br>';
    $result_det_link = $sql->query($query_po_supl_ship_det);
    $ship_det = array();
    while( $row_det = $result_det_link->fetch_assoc() ) {
        $ship_det[] = $row_det;
    }
    $row['dets'] = $ship_det;

    // 語系轉碼
    $row['dm_way'] = iconv("BIG5","UTF-8",$row['dm_way']);
    $row['color'] = iconv("BIG5","UTF-8",$row['color']);
    
    $rows_det[] = $row;

}

$rows['po_det'] = $rows_det;

$arows[] = $rows;

return $arows;

} # END





function ship_po_view($id){

$sql = $this->sql;

$arows = array();
$rows = array();
$query  = "SELECT `ap`.* , `po_supl_ship`.`id` as `po_ship_id` , `po_supl_ship`.`carrier_num` , `po_supl_ship`.`pi_num` , `po_supl_ship`.`invoice_num` , `po_supl_ship`.`org` , `po_supl_ship`.`dist` , `po_supl_ship`.`ship_way` , `po_supl_ship`.`ex_cmpy` , `po_supl_ship`.`ship_date` , `po_supl_ship`.`ship_eta_date`  FROM `ap` LEFT JOIN `po_supl_ship` ON ( `ap`.`po_num` = `po_supl_ship`.`po_num` ) WHERE `po_supl_ship`.`id` = '".$id."' AND `po_supl_ship`.`po_num` = `ap`.`po_num` ";
$result = $sql->query($query);
$rows = $result->fetch_assoc();

$rows['org_name'] = $this->in_parameter(1,$rows['org']) ? $this->get_parameter(1,$rows['org']) : $rows['org'] ;
$rows['dist_name'] = $this->get_parameter(2,$rows['dist']);


$query  = " 
SELECT 
`ap_det`.`*`,sum(`ap_det`.`po_qty`) as `po_qty` 

FROM 
`ap_det` 

WHERE 
`ap_det`.`ap_num` = '".$rows['ap_num']."'

GROUP BY 
`ap_det`.`color`,`ap_det`.`size`,`ap_det`.`mat_id`

;";

$result = $sql->query($query);

$rows_det = array();
while( $row = $result->fetch_assoc() ) {

    $query_det_link  = "
    SELECT 
    ".( $row['mat_cat'] == 'l' ? "lots_name,des,comp,specify,width" : "acc_name,des,specify" )." 
    
    FROM 
    `".( $row['mat_cat'] == 'l' ? "lots" : "acc" )."` 
    
    WHERE 
    `id` = '".$row['mat_id']."' 
    ;";
    
    $result_det_link = $sql->query($query_det_link);
    $row['det_link'] = $result_det_link->fetch_assoc();

    $query_po_supl_ship_qtty_shipped_sum  = "
    SELECT DISTINCT 
    sum(qty) as qtty_shipped_qty

    FROM 
    `po_supl_ship_det`
    
    WHERE 
    `mat_cat` = '".$row['mat_cat']."' AND 
    `mat_id` = '".$row['mat_id']."' AND 
    `color` = '".$row['color']."' AND 
    `size` = '".$row['size']."' AND 
    `status` = '1' AND 
    `del_mk` = 'n'
    ;";
    // echo $query_po_supl_ship_det_sum.'<br>';
    $result_qtty_shipped_link = $sql->query($query_po_supl_ship_qtty_shipped_sum);
    $qtty_shipped = $result_qtty_shipped_link->fetch_assoc();
    $row['qtty_shipped_qty'] = $qtty_shipped['qtty_shipped_qty'];
    
    $query_po_supl_ship_det_sum  = "
    SELECT DISTINCT 
    sum(qty) as ship_qty
    
    FROM 
    `po_supl_ship_det`
    
    WHERE 
    `po_supl_ship_id` = '".$id."' AND 
    `mat_cat` = '".$row['mat_cat']."' AND 
    `mat_id` = '".$row['mat_id']."' AND 
    `color` = '".$row['color']."' AND 
    `size` = '".$row['size']."' AND 
    `status` = '1' AND 
    `del_mk` = 'n'
    ;";
    // echo $query_po_supl_ship_det_sum.'<br>';
    $result_det_link = $sql->query($query_po_supl_ship_det_sum);
    $ship_qty = $result_det_link->fetch_assoc();
    $row['ship_qty'] = $ship_qty['ship_qty'];
    
    $row['ship_total_qty'] = $row['ship_qty'] + $row['qtty_shipped_qty'];
    
    $query_po_supl_ship_det  = "
    SELECT  
    `id` , `qty` , `c_no` , `r_no` , `l_no` , `gw` , `nw` , `c_o` 
    
    FROM 
    `po_supl_ship_det`
    
    WHERE 
    `po_supl_ship_id` = '".$id."' AND 
    `mat_cat` = '".$row['mat_cat']."' AND 
    `mat_id` = '".$row['mat_id']."' AND 
    `color` = '".$row['color']."' AND 
    `size` = '".$row['size']."' AND 
    `status` = '1' AND 
    `del_mk` = 'n' 
    ;";
    // echo $query_po_supl_ship_det.'<br>';
    $result_det_link = $sql->query($query_po_supl_ship_det);
    $ship_det = array();
    while( $row_det = $result_det_link->fetch_assoc() ) {
        $ship_det[] = $row_det;
    }
    $row['dets'] = $ship_det;
    
    $rows_det[] = $row;

}

$rows['po_det'] = $rows_det;

$arows[] = $rows;

return $arows;

} # END



function append_supl_ship($parm){

$sql = $this->sql;

$query = " INSERT INTO `po_supl_ship` SET
`id`            =    '".($parm['id'])."' ,
`po_num`        =    '".($parm['po_num'])."' ,
`pi_num`        =    '".($parm['pi_num'])."' ,
`invoice_num`   =    '".($parm['invoice_num'])."' ,
`carrier_num`   =    '".($parm['carrier_num'])."' , 
`org`           =    '".($parm['org'])."' , 
`dist`          =    '".($parm['dist'])."' , 
`ship_way`      =    '".($parm['ship_way'])."' , 
`ex_cmpy`       =    '".($parm['ex_cmpy'])."' , 
`supl_code`     =    '".($_SESSION['po_user']['supl_code'])."' ,
`ship_date`     =    '".($parm['ship_date'])."' ,
`ship_eta_date` =    '".($parm['ship_eta_date'])."' ,
`open_date`     =    NOW() ,
`open_user`     =    '".($_SESSION['USER']['ADMIN']['id'])."' ,
`last_up_date`  =    NOW() ,
`last_up_user`  =    '".($_SESSION['USER']['ADMIN']['id'])."' 
 ; ";

if( $result = $sql->query($query) ){

    return true;
    
} else {
    
    return false;
    
}

} # END



function delete_supl_ship($id){

$sql = $this->sql;

$query = " UPDATE `po_supl_ship` SET `last_up_date` = NOW() , `last_up_user` = '".($_SESSION['USER']['ADMIN']['id'])."' , `del_mk` = 'y' WHERE `id` = '".($id)."' ;";

if( $result = $sql->query($query) ){
    $query = " UPDATE `po_supl_ship_det` SET `last_up_date` = NOW() , `last_up_user` = '".($_SESSION['USER']['ADMIN']['id'])."' , `del_mk` = 'y' WHERE `po_supl_ship_id` = '".($id)."' ;";
    if( $result = $sql->query($query) ){
        return true;
    }
}

return false;
    
} # END







function get_ship_det_id() {

$sql = $this->sql;

$query = "SELECT `id` FROM `po_supl_ship_det` ORDER BY `id` DESC LIMIT 0 , 1 ;";

if( !$result = $sql->query($query) ){

	return false;
}

$row = $result->fetch_assoc();

if ( empty($row) ) {
	$id = 0;
} else {
	$id = substr(@$row['id'],-6,6);
}

return date('ym').str_pad( ($id+1) , 6, 0, STR_PAD_LEFT);

} # END





function supl_ship_cfm($id){

$sql = $this->sql;

$user_id = $_SESSION['USER']['ADMIN']['id'];

$query = " UPDATE `po_supl_ship` SET `last_up_date` = NOW() , `last_up_user` = '".($_SESSION['USER']['ADMIN']['id'])."' , `status` = '1' WHERE `id` = '".($id)."' ;";

if( $result = $sql->query($query) ){

    $query = " UPDATE `po_supl_ship_det` SET `last_up_date` = NOW() , `last_up_user` = '".($_SESSION['USER']['ADMIN']['id'])."' , `status` = '1' WHERE `po_supl_ship_id` = '".($id)."' ;";
    
    if( $result = $sql->query($query) ){
    
        return true;
        
    }
}

return false;

} # END



function supl_ship_revise2222($id){

$sql = $this->sql;

$user_id = $_SESSION['USER']['ADMIN']['id'];

$query = " UPDATE `po_supl_ship` SET `last_up_date` = NOW() , `last_up_user` = '".($_SESSION['USER']['ADMIN']['id'])."' , `status` = '0' WHERE `id` = '".($id)."' ;";

if( $result = $sql->query($query) ){

    $query = " UPDATE `po_supl_ship_det` SET `last_up_date` = NOW() , `last_up_user` = '".($_SESSION['USER']['ADMIN']['id'])."' , `status` = '0' WHERE `po_supl_ship_id` = '".($id)."' ;";
    
    if( $result = $sql->query($query) ){
    
        return true;
        
    }
}

return false;

} # END




function get_parameter($parameter_id,$key){

global $mysqli_utf8;

$query = "SELECT `name` FROM `parameter_det` WHERE `parameter_id` = '".$parameter_id."' AND `key` = '".$key."' ;";

if( !$result = $mysqli_utf8->query($query) ){

	return false;
}

$rows = $result->fetch_assoc();

if(!empty($rows)){
    return $rows['name'];
} else {
    return false;
}

} # END



function get_status($status){

switch($status){

case 0:
return 'Waiting For Submit';

case 1;
return 'Submit';

}

} # END


function import_supl_main(){

$sql = $this->sql;

$ship_id = get_insert_id('po_supl_ship');

$query = " INSERT INTO `po_supl_ship` SET
`id`            =    '".($ship_id)."' ,
`open_date`     =    NOW() ,
`open_user`     =    '".($_SESSION['USER']['ADMIN']['id'])."' ,
`last_up_date`  =    NOW() ,
`last_up_user`  =    '".($_SESSION['USER']['ADMIN']['id'])."' 
 ; ";

if( $result = $sql->query($query) ){
	return $ship_id;
} else {
	return false;
}

} # END


function import_supl_det($parm){

$sql = $this->sql;

$ship_det_id = get_insert_id('po_supl_ship_det');

 $query = " INSERT INTO `po_supl_ship_det` SET
        `id`                =    '".$ship_det_id."' ,
        `po_supl_ship_id`   =    '".$parm['po_supl_ship_id']."' ,
        `po_num`            =    '".$parm['po_num']."' ,
		`invoice_num`       =    '".$parm['invoice_num']."' ,
        `open_date`         =    NOW() ,
        `open_user`         =    '".($_SESSION['USER']['ADMIN']['id'])."' , 
        `last_up_date`      =    NOW() , 
        `last_up_user`      =    '".($_SESSION['USER']['ADMIN']['id'])."' 
        ; ";

        if( $result = $sql->query($query) ){
			return $ship_det_id;
		}else{
			return false;
		}

} # END


function import_supl_link($parm){

$sql = $this->sql;

$link_id = get_insert_id('po_supl_ship_link');

$query = " INSERT INTO `po_supl_ship_link` SET
`id`                    =    '".($link_id)."' ,
`po_supl_ship_id`       =    '".($parm['po_supl_ship_id'])."' ,
`po_supl_ship_det_id`   =    '".($parm['po_supl_ship_det_id'])."' ,
`ap_id`                 =    '".($parm['ap_id'])."' ,
`mat_cat`               =    '".($parm['mat_cat'])."' ,
`mat_id`                =    '".($parm['mat_id'])."' ,
`color`                 =    '".($parm['color'])."' ,
`size`                  =    '".($parm['size'])."' ,
`qty`                   =    '".($parm['qty'])."' ,
`po_unit`               =    '".($parm['po_unit'])."' ,

`c_no`                  =    '".($parm['c_no'])."' ,
`r_no`                  =    '".($parm['r_no'])."' ,
`l_no`                  =    '".($parm['l_no'])."' ,
`gw`                    =    '".($parm['gw'])."' ,
`nw`                    =    '".($parm['nw'])."' ,
`c_o`                   =    '".($parm['c_o'])."' ,
`open_date`             =    NOW() ,
`open_user`             =    '".($_SESSION['USER']['ADMIN']['id'])."' , 
`last_up_date`          =    NOW() , 
`last_up_user`          =    '".($_SESSION['USER']['ADMIN']['id'])."' , 
`status`                =    '0' , 
`del_mk`                =    'n' 
 ; ";

if( $result = $sql->query($query) ){

    return $link_id;
    
} else {
    
    return false;
    
}

} # END


function get_field($field,$table,$where) {
	$sql = $this->sql;
	$q_str="SELECT ". $field. " 
			FROM ".$table." 
			WHERE ".$where;

	$q_result = $sql->query($q_str);
	$row = $sql->fetch($q_result);
	
	return $row[$field];
	
} # END


function del_ship($id) {
	$sql = $this->sql;
	$q_str="SELECT id
			FROM stock_inventory
			WHERE ship_id = ".$id;

	$q_result = $sql->query($q_str);
	if($row = $sql->fetch($q_result)){
		return true;
	}else{
		$q_str="DELETE FROM po_supl_ship
			   WHERE id = ".$id;
		if($q_result = $sql->query($q_str)){
			$q_str="DELETE FROM po_supl_ship_det
				   WHERE po_supl_ship_id = ".$id;
			$q_result = $sql->query($q_str);
			
			$q_str="DELETE FROM po_supl_ship_link
				   WHERE po_supl_ship_id = ".$id;
			$q_result = $sql->query($q_str);
		}
		
		return false;
	}
	
	
	
} # END



function get_po4ship($po_num) {
	$sql = $this->sql;
	$q_str="SELECT id, ap_num, mat_cat, mat_id, color, size, po_unit, sum(po_qty) as po_qty
			FROM ap_det
			WHERE ap_num = '".str_replace("O","A",$po_num)."' 
			GROUP BY mat_id, color, size, po_unit
			ORDER BY id asc";
	
	$rtn = array();
	$q_result = $sql->query($q_str);
	while($po_row = $sql->fetch($q_result)){
		$mat_tbl = $po_row['mat_cat'] == "l" ? "lots" : "acc";
		$q_str = "select ".$mat_tbl."_code as mat_code
				from ".$mat_tbl."
			   WHERE id = ".$po_row['mat_id'];
		
		$mat_res = $sql->query($q_str);
		$mat_row = $sql->fetch($mat_res);
		$po_row['mat_code'] = $mat_row['mat_code'];
		$rtn[] = $po_row;
	}
	
	return $rtn;;	
} # END


function del_ship_po($ship_det_id) {
	$sql = $this->sql;
	$q_str="DELETE FROM po_supl_ship_det 
			WHERE id = '".$ship_det_id."'";
	
	if($q_result = $sql->query($q_str)){
		$q_str="DELETE FROM po_supl_ship_link
				WHERE po_supl_ship_det_id = '".$ship_det_id."'";
		
		$q_result = $sql->query($q_str);
	}
	
	return true;
} # END


function get_by_po($bom_id, $mat, $mat_id, $color, $size) {
	$sql = $this->sql;

	$op['ship'] = $ap_num_ary = array();
	$po_num_str = '';
	
	$ap_num_ary = $this->get_po_num_by_bom_id($bom_id, $mat);
	foreach($ap_num_ary as $key => $val){
		$po_num_str .= "'".str_replace("PA", "PO", $val['ap_num'])."',";
	}
	$po_num_str = substr($po_num_str, 0, -1);
	
	$q_str="SELECT po_supl_ship.id, po_supl_ship.bl_num, po_supl_ship.org, po_supl_ship.dist, po_supl_ship.ship_way, po_supl_ship.ship_date, 
				   po_supl_ship.ship_eta_date, po_supl_ship_link.qty, po_supl_ship_link.l_no, po_supl_ship_link.r_no
			FROM  po_supl_ship, po_supl_ship_det, po_supl_ship_link
			WHERE po_supl_ship_link.mat_cat = '".$mat."' and po_supl_ship_link.mat_id = ".$mat_id." and po_supl_ship_link.color = '".$color."' and 
				  po_supl_ship_link.size = '".$size."' and po_supl_ship_det.id = po_supl_ship_link.po_supl_ship_det_id and 
				  po_supl_ship_det.po_num in (".$po_num_str.") and po_supl_ship.id = po_supl_ship_det.po_supl_ship_id
			ORDER BY po_supl_ship.bl_num desc";

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	while ($row1 = $sql->fetch($q_result)) {
		$ship[]=$row1;

	}

	return $ship;
} # END


function get_po_num_by_bom_id($bom_id, $mat_cat) {
	$sql = $this->sql;
	$det_row = array();
	
	$q_str="SELECT distinct ap_num
			FROM  ap_det
			WHERE mat_cat = '".$mat_cat."' and bom_id = '".$bom_id."'";

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	while ($row = $sql->fetch($q_result)) {
		$det_row[] = $row;

	}

	return $det_row;
} # END



} # END CLASS

?>