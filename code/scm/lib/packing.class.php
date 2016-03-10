<?php

class PACKING {



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

function get_cust_s_name(){

	$sql = $this->sql;
	$t_sql = 'select cust_s_name,cust_init_name from cust order by cust_s_name';
	$cust = array();
	if( !$result = $sql->query($t_sql) ){

		return false;
	}
	while( $row = $sql->fetch($result) ) {

        $cust[] = $row;
        
    }
	return $cust;

}
function get_cust_po($cust,$year){

	$sql = $this->sql;
	$year = $year."-01-01";
	$t_sql = "select cust_po from s_order where cust='".$cust."' and cust_po !='' and etd >= '".$year."' order by etd desc";
	//echo $t_sql;
	$cust_po_pdf = array();
	if( !$result = $sql->query($t_sql) ){

		return false;
	}
	while( $row = $sql->fetch($result) ) {

        $cust_po_pdf[] = $row;
        
    }
	$temp1=array();
	$temp2=array();
	foreach($cust_po_pdf as $pdf_key => $pdf_val)
	{
		$temp1[] =  explode('|', $pdf_val['cust_po']);

	}
	foreach($temp1 as $temp1_key => $temp1_val)
	{
		foreach($temp1_val as $temp1_val_key => $temp1_val_val)
		{
			$temp1[$temp1_key][$temp1_val_key] = explode('/', $temp1_val_val);
		}
	}
	$temp_po='';
	$value_exist = false;
	foreach($temp1 as $tmp1_key => $tmp1_val)
	{
		foreach($tmp1_val as $tmp2_key => $tmp2_val)
		{
			if($temp_po != '')
			{
				foreach($temp2 as $temp2_key => $temp2_val)
				{
					if($temp2_val == $tmp2_val[0])
					{
						//$temp2[] = $tmp2_val[0];
						$value_exist=true;
					}
				}
				if(!$value_exist)
				{
					$temp2[] = $tmp2_val[0];
				}
			}
			else
			{
				$temp_po = $tmp2_val[0];
				$temp2[] = $tmp2_val[0];
			}
		}
	}
	$cust_po = array();
	$cust_po = $temp2;
	return $cust_po;

}

function po_get_order($cust_po,$cust_name,$year){

	$year .= '-01-01';
	$sql = $this->sql;
	$t_sql = "SELECT order_num FROM s_order WHERE cust_po LIKE  '%";
	$t_sql .= $cust_po;
	$t_sql .="%' AND cust = '";
	$t_sql .= $cust_name;
	$t_sql .="' and etd >= '";
	$t_sql .=$year; 
	$t_sql .="'";
	//echo $t_sql;
	$scm_order = array();
	if( !$result = $sql->query($t_sql) ){

		return false;
	}
	while( $row = $sql->fetch($result) ) {

        $scm_order[] = $row;
        
    }
	//print_r($cust);
	return $scm_order;


}
function get_order_partial($order){

	$sql = $this->sql;
	$t_sql = "SELECT mks FROM order_partial WHERE ord_num = '";
	$t_sql .= $order;
	$t_sql .="'";
	//echo $t_sql;
	$scm_order = array();
	if( !$result = $sql->query($t_sql) ){

		return false;
	}
	while( $row = $sql->fetch($result) ) {

        $scm_orderpartial[] = $row;
        
    }
	//print_r($cust);
	return $scm_orderpartial;


}
function get_order_size($order){

	$sql = $this->sql;
	$t_sql = "SELECT size_des.size FROM s_order LEFT  JOIN size_des ON s_order.size = size_des.id WHERE s_order.order_num = '";
	$t_sql .= $order;
	$t_sql .="'";
	//echo $t_sql;
	$scm_order = array();
	if( !$result = $sql->query($t_sql) ){

		return false;
	}
	while( $row = $sql->fetch($result) ) {

        $scm_ordersize[] = $row;
        
    }
	//print_r($cust);
	return $scm_ordersize;


}












































































































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



} # END CLASS

?>