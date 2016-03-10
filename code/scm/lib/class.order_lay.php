<?php 

class ORDER_LAY {

	var $sql;
	var $msg ;

	function init($sql) {
		$this->msg = new MSG_HANDLE();

		if (!$sql) {
			$this->msg->add("Error ! Data base can't connect.");
		    return false;
		}
		$this->sql = $sql;
		return true;
	} // end func



	function search_mode($mode='',$where_str='',$table='',$m_sql='',$page_view='',$show_num=10000) {

		$sql = $this->sql;
		$srh = new SEARCH();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		if (!empty($mode['status'])){
			unset($_SESSION['PAGE']);
      if( $mode['date'] < date('Y-m') )
        $where_str = "where `s_order`.`order_num` = `pdtion`.`order_num` AND `s_order`.`dept` != `s_order`.`factory` AND ( `s_order`.`status` = '12' ) ";
      else
        $where_str = "where `s_order`.`order_num` = `pdtion`.`order_num` AND `s_order`.`dept` != `s_order`.`factory` AND ( `s_order`.`status` = '7' OR `s_order`.`status` = '8' OR `s_order`.`status` = '10' OR `s_order`.`status` = '12' ) ";
      $scmds=1;
			foreach($mode as $key => $val){
				if(!empty($val)){
					$and = ($scmds==0)? '' : ' AND ';
					if( $key == 'factory' || $key == 'cust'  ){
						$where_str .= $and.'`s_order`.`'.$key."` = '".$val."' ";
					}else if( $key == 'status' ){
          }else if( $key == 'order_num' ){
            $where_str .= $and.'`s_order`.`'.$key."` LIKE '%".$val."%' ";
					}else if( $key == 'date' ){
            $where_str .= $and."( `pdtion`.`shp_date` LIKE '".$val."%' OR `s_order`.`etd` LIKE '".$val."%' )";
					}else{
					}
				}
			}
	
			if ( $where_str == 'where ')$where_str=''; 
		}
		$op = $this->page_sorting_lay($table,$m_sql,$page_view,$where_str,$show_num,$mode['date']);
		return $op;
	} // end funcc



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	function page_sorting() : 分頁排序
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

function page_sorting_lay($table,$item,$page_view,$where_str='',$show_num = 1000,$date){
	$PHP_SELF = $_SERVER['PHP_SELF'];
	$sql = $this->sql;

	$MySql = csv2array($item);
	$mos ='';
	for ($m=0;$m < count($MySql);$m++) {
		$mos .= " ".$MySql[$m].",";
	}

	if ( !isset($_SESSION['PAGE']['this_sort']) ) $_SESSION['PAGE']['this_sort'] = $MySql[0];
	if ( !isset($_SESSION['PAGE']['sorting']) ) $_SESSION['PAGE']['sorting'] = "ASC";
	if ( isset($_GET['sorting']) ) $_SESSION['PAGE']['sorting'] = $_GET['sorting'];

  $q_sql  = "select ".substr($mos,0,-1)." from ".$table." ".$where_str." ";
  $q_sql .= "order by ".$_SESSION['PAGE']['this_sort']." ".$_SESSION['PAGE']['sorting']." ";

  $pages = $sql->query($q_sql);
  $op['nT_f'] = $sql->num_rows($pages);
  
  $op['sales_sum'] = 0;
  $op['gmc_sum'] = 0;
  $op['m_sum'] = 0;
  $op['workers'] = 0;
  
  $op['sales_sum_s'] = $op['gmc_sum_s'] = $op['m_sum_s'] = $op['workers_s'] = 0;
  $op['sales_sum_f'] = $op['gmc_sum_f'] = $op['m_sum_f'] = $op['workers_f'] = 0;
  $op['sales_sum_p'] = $op['gmc_sum_p'] = $op['m_sum_p'] = $op['workers_p'] = 0;
  $op['sales_sum_c'] = $op['gmc_sum_c'] = $op['m_sum_c'] = $op['workers_c'] = 0;
  
  while($row = $sql->fetch($pages)){

    # -------------------
    $row['id'] = $row['id'];
    $row['order_num'] = $row['order_num'];
    $row['opendate'] = $row['opendate'];
    $row['dept'] = $row['dept'];
    $row['cust'] = $row['cust'];
    $row['style'] = $row['style'];
    $row['factory'] = $row['factory'];
    $row['status'] = $row['status'];
    $row['start'] = $row['start'];
    $row['finish'] = $row['finish'];
    $row['ship'] = $row['shp_date'];
    $row['etd'] = $row['etd'];
    

    if( $row['status'] == 12 or $row['status'] == 10 ){

      $row['tr'] = 'onMouseOver="bgColor=\'#FCF4A4\'" onMouseOut="bgColor=\'#CCCCCC\'" bgColor="#CCCCCC"';
    
      $order_worker = $this->order_worker($row['order_num']);
      $row['day'] = $order_worker['day'];
      
      $row['tsu'] = $order_worker['su'];
      $row['tqty'] = $order_worker['qty'];
      
      $gm = $this->get_gm($row);

      $row['gm'] = $gm['gm'];
      $row['stts'] = $gm['stts'];
      // SALES
      $row['sales'] = $row['uprice'] * $row['tqty'];
      // GM / USD
      $row['gmc'] = $row['gm'] * $row['sales'] / 100 ;
      
      $row['workers'] = $order_worker['workers'];
      
      $op['workers_s'] += $row['workers'];
      $op['sales_sum_s'] += $row['sales'];
      $op['gmc_sum_s'] += $row['gmc'];
      
      if( !empty($op['gmc_sum_s']) && !empty($op['sales_sum_s']) )
      $op['m_sum_s'] = ( $op['gmc_sum_s'] / $op['sales_sum_s'] ) * 100 ;
      if( !empty($op['sales_sum_s']) && !empty($op['workers_s']) )
      $op['contri_s'] = $op['sales_sum_s'] / $op['workers_s'];       
      
      if( !empty($order_worker['su']) && !empty($order_worker['workers']) ){
        // $row['wv'] = ( $order_worker['su'] * 2 ) / $order_worker['workers'];
        $row['wv'] = $row['sales'] / $order_worker['workers'];
 //       echo $row['sales'].'===>'.$order_worker['workers'].'====>'.$row['order_num'].'<BR>';
      }else{
        $row['wv'] = 0;
      }
      
      if(empty($row['tqty']))$row['tqty']= $row['qty'];
      if(empty($row['tsu']))$row['tsu']= $row['su'];
    }

    

    if( $row['status'] == 8 or $row['status'] == 7){
      $row['day'] = 0 ;
      $row['tr'] = 'onMouseOver="bgColor=\'#FCF4A4\'" onMouseOut="bgColor=\'#ffffff\'" bgColor="#ffffff"';
    
   // 	$ore_rec =  $GLOBALS['order']->orgainzation_ord($row);
      $gm = $this->get_gm($row);
      $row['gm'] = $gm['gm'];
      $row['stts'] = $gm['stts'];
      // SALES
      $row['sales'] = $row['uprice'] * $row['qty'];
      
      // GM / USD
      $row['gmc'] = $row['gm'] * $row['sales'] / 100 ;

      $op['sales_sum_p'] += $row['sales'];
      $op['gmc_sum_p'] += $row['gmc'];
      $op['m_sum_p'] = ( $op['gmc_sum_p'] / $op['sales_sum_p'] ) * 100 ;
      
      if(empty($row['tqty']))$row['tqty']= $row['qty'];
      if(empty($row['tsu']))$row['tsu']= $row['su'];
    }

    $op['pages'][] = $row;
  }
  // print_r($op);
  return $op;
} // end func



// 工廠實際產出
function order_worker($order_num){
	$sql = $this->sql;

	$q_str = "SELECT `qty`,`workers`,`su`,`out_date` FROM `saw_out_put` WHERE `ord_num` = '".$order_num."' ";

	if (!$q_sql = $sql->query($q_str)) {
		$this->msg->add("Error ! Can't access database!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	unset($row);
	$out_date = $str['workers'] = $str['qty'] = $str['su'] = $str['day'] = 0;
	while ($row = $sql->fetch($q_sql)) {
		$str['workers'] += (int)$row['workers'];
		$str['qty'] += $row['qty'];
		$str['su'] += $row['su'];
		if( $out_date <> $row['out_date'] ){
		  $str['day']++;
  		$out_date = $row['out_date'];
	  }
	}

	return $str;
}



// 出口時間
function get_sf($order_num){
	$sql = $this->sql;

	$q_str = "SELECT `start`,`finish` FROM `pdtion` WHERE `order_num` = '".$order_num."' ";

	if (!$q_sql = $sql->query($q_str)) {
		$this->msg->add("Error ! Can't access database!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	unset($row);
	while ($row = $sql->fetch($q_sql)) {
		$str['start'] = $row['start'];
		$str['finish'] = $row['finish'];
	}

	return $str;
}



function get_gm($str){
  $wi_rec = $op = $parm = $ord = array() ;
  $parm = $ord['order'] = $str;
      if( $str['status'] == 7 or $str['status'] == 8 ){
					$str['unit_cost'] = ($str['mat_u_cost']* $str['mat_useage'])+ $str['interline']+ $str['fusible']+ $str['acc_u_cost'] + $str['quota_fee'] + $str['comm_fee'] + $str['cm'] + $str['emb'] + $str['wash'] + $str['oth'];
					$str['grand_cost'] = $str['unit_cost']*$str['qty'] + $str['smpl_fee'];
					$str['sales'] = $str['uprice']*$str['qty'];
					$str['gm'] = $str['sales'] - $str['grand_cost'];
					if ($str['sales']){
						$str['gm_rate'] = ($str['gm']/ $str['sales'])*100;
					}else{
						$str['gm_rate'] = 0;
					} 
	  		  $v_gms['gm'] = $str['gm_rate'];
		      $v_gms['stts'] = ' [ ord ]'; 					     
      		return $v_gms;
      }
			if($str['rel_cm_cost'] == 0) $str['rel_cm_cost'] = $GLOBALS['cost']->add_ord_cm_cost($str['order_num']);	
			if(($str['rel_mat_cost'] > 0 || $str['mat_u_cost'] == 0) && $str['rel_acc_cost'] > 0)
			{

				$rcv_other =  NUMBER_FORMAT((( $str['quota_fee'] + $str['comm_fee'] ) ),2);
		
				if(!isset($str['ship_fob']) || $str['ship_fob'] == 0)$str['ship_fob'] = $str['uprice'];
				if($str['fty_cm'] == 0) $str['fty_cm'] = $str['ie1'] * $FTY_CM[$str['factory']];
	
				$rcv_grand_cost	= $str['rel_mat_cost']+$str['rel_acc_cost']+$str['rel_cm_cost'] + ($rcv_other + $str['fty_cm'])*$str['qty'] +$str['smpl_fee'];
	
				$rcv_gm = ($str['ship_fob'] *  $str['qty']) - $rcv_grand_cost;
				$str['rcv_gmr'] = ($rcv_gm/ (($str['ship_fob'] *  $str['qty'])))*100;				
	  		$v_gms['gm'] = $str['rcv_gmr'];
		    $v_gms['stts'] = ' [ rcv ]'; 
				return $v_gms;
			}
}

} // end class

?>