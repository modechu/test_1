<?php 

#++++++++++++++++++++++++++++++++++ ORDER  class ##### 訂 單  ++++++++++++++++++++++++++++++++++++++++
#	->init($sql)		啟始 (使用 Msg_handle(); 先聯上 sql)
#	->bom_search($supl,$cat)	查詢BOM的主副料
#
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class COST {
		
	var $sql;
	var $msg ;
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->init($sql)	啟始(使用 Msg_handle() ; 先聯上 sql)
#		必需聯上 sql 才可  啟始
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function init($sql) {
		$this->msg = new MSG_HANDLE();

		if (!$sql) {
			$this->msg->add("Error ! Data base can't connect.");
		    return false;
		}
		$this->sql = $sql;
		return true;
	} // end func



/*
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fab_eta($ord_num)
#
#		取得主料的ETA(最後日期)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_output($s_date,$e_date,$fty,$rem_date) {

		$sql = $this->sql;
		$output = array();
		$eta_date='';

		//工繳取得
		$para_cm = $GLOBALS['para']->get(0,'hj-cm');
		$FTY_CM['HJ'] = $para_cm['set_value'];
		$para_cm = $GLOBALS['para']->get(0,'ly-cm');
		$FTY_CM['LY'] = $para_cm['set_value'];

		############ 當 AP_mark為空時 ~~~~ 就不要寫入 ~~~~~~(代表主料有未請購)
		$q_str = "SELECT s_order.*, sum(daily_out.qty)as out_qty, sum(daily_out.su)as out_su, 
										 s_order.fty_cm as fty_cm, style_type.des as style_des, s_order.ie2, s_order.id as ord_id
							FROM s_order, daily_out, style_type  
							WHERE s_order.order_num =daily_out.ord_num AND  s_order.factory = '$fty'
										AND s_order.style = style_type.style_type
										AND daily_out.k_date >= '$s_date' AND daily_out.k_date <= '$e_date'
							GROUP BY daily_out.ord_num ";
//echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't find.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
		 $where_str = " AND out_month = '$rem_date' AND ord_num = '".$row['order_num']."' GROUP BY ord_num";
	   $row['rem'] = $this->get_rem_fld('sum(rem_qty) as remun_qty',$where_str);
	   $row['rmn_qty'] = $row['out_qty'] - $row['rem'];
	   $tmp =  explode(' ',$row['style_des']);
	   $row['style_des'] = $tmp[0];
	   if($row['fty_cm'] == 0) 
	   {	   
	   		$row['fty_cm'] =  number_format(($row['ie2'] * $FTY_CM[$fty]),2,'.','');
	   		$GLOBALS['order']->update_field('fty_cm', $row['fty_cm'], $row['ord_id']);
	   }
	   if ($row['rmn_qty'] > 0)   $output[]=$row;
	   
		}


		return $output;
	} // end func	
*/	



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fab_eta($ord_num)
#
#		取得主料的ETA(最後日期)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_output($s_date,$e_date,$fty,$rem_date,$dept) {

		$sql = $this->sql;
		$output = array();
		$eta_date='';
		$ship_rtn = array();
		//工繳取得
		$FTY_CM = $_SESSION['FTY_CM'];
		
		$dept = !empty($dept)? " AND s_order.dept = '".$dept."' " : '' ;

		// $q_str = "SELECT distinct s_order.order_num,s_order.status
							// FROM s_order, shipping
							// WHERE ( s_order.order_num = shipping.ord_num AND  s_order.factory = '$fty' AND 
										// shipping.k_date like '$rem_date%' ) $dept OR s_order.status = '14' ";
// echo $q_str.'<br>';
		// if (!$q_result = $sql->query($q_str)) {
			// $this->msg->add("Error !  Database can't find.");
			// $this->msg->merge($sql->msg);
			// return false;    
		// }
		// while ($row = $sql->fetch($q_result)) {
				// $ship_rtn[] = $row['order_num'];
				// $status[] = $row['status'];
	   
		// }
		
		
		$q_str = "SELECT DISTINCT `ord_num` FROM `shipping` WHERE `shipping`.`k_date` LIKE '".$rem_date."%' AND `factory` = '".$fty."' GROUP BY `ord_num` ";
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't find.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		// echo $q_str.'<br>';
		while ($row = $sql->fetch($q_result)) {
			
			$q_str2 = "SELECT `order_num` , `status` FROM `s_order` WHERE `order_num` = '".$row['ord_num']."' ".$dept." ;";

			if($q_result2 = $sql->query($q_str2)){
				while ($row2 = $sql->fetch($q_result2)) {
					$ship_rtn[] = $row2['order_num'];
					$status[] = $row2['status'];
				}
			}
		}
		
		
		for($i=0; $i<sizeof($ship_rtn); $i++)
		{
			if( $status[$i] == '14' ){
				$q_str = "SELECT s_order. * , sum( saw_out_put.qty ) AS out_qty, sum( saw_out_put.su ) AS out_su, s_order.fty_cm AS fty_cm, style_type.des AS style_des, s_order.ie1, s_order.id AS ord_id
					FROM s_order, saw_out_put, style_type
					WHERE s_order.order_num = saw_out_put.ord_num AND s_order.style = style_type.style_type AND s_order.order_num = '".$ship_rtn[$i]."' GROUP BY saw_out_put.ord_num 
					";
			}else{
                $q_str = "SELECT s_order.*, sum(shipping.qty)as out_qty, sum(shipping.su)as out_su, 
											 s_order.fty_cm as fty_cm, style_type.des as style_des, s_order.ie1, s_order.id as ord_id
								FROM s_order, shipping, style_type  
								WHERE s_order.order_num =shipping.ord_num AND s_order.style = style_type.style_type
									      AND shipping.k_date <= '$e_date'
							      AND  s_order.order_num = '".$ship_rtn[$i]."' 										
								GROUP BY shipping.ord_num ";
			}
			
			$q_result = $sql->query($q_str);
			if( $row = $sql->fetch($q_result))
			{
		  	$where_str = "AND out_month <= '$rem_date'  AND ord_num = '".$row['order_num']."' GROUP BY ord_num";
	    	$row['rem'] = $this->get_rem_fld('sum(rem_qty) as remun_qty',$where_str);
	    	$row['rmn_qty'] = $row['out_qty'] - $row['rem'];
	    	$tmp =  explode(' ',$row['style_des']);
	    	$row['style_des'] = $tmp[0];

	
	    	if ( $row['rmn_qty'] > 0) {
                
                $ie = $row['ie1'];
                if($row['ie2'] > 0) $ie = $row['ie2'];
                $row['fty_cm'] =  number_format(($ie * $FTY_CM[$fty]),2,'.','');
                $GLOBALS['order']->update_field('fty_cm', $row['fty_cm'], $row['ord_id']);
				
				# 2013/07/11 將 2013/07/10 以後的訂單 自動帶入 handling_fee
				if($row['factory'] == 'LY' and $row['opendate'] >= '2013-07-10' and substr($row['order_num'],0,1) <> 'L'){
					$row['handling_fee'] = $row['handling_fee'];
				}else{
					$row['handling_fee'] = 0.00;
				}
				$row['acost'] = $row['fty_cm'] + $row['handling_fee'];
				
                $output[]=$row;
            }
            
	  	}
	}
    return $output;
} // end func	



	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fab_eta($ord_num)
#
#		取得主料的ETA(最後日期)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_rem_fld($fld,$where) {

		$sql = $this->sql;
		$eta_date='';
		############ 當 AP_mark為空時 ~~~~ 就不要寫入 ~~~~~~(代表主料有未請購)
		$q_str = "SELECT ".$fld." FROM remun, remun_det WHERE remun.id = remun_det.rem_id ".$where;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't find.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$row = $sql->fetch($q_result);

		if (isset($row[0]))
		{
			return $row[0];
		}else{
			return false;
		}
	} // end func	
	


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fab_eta($ord_num)
#
#		取得主料的ETA(最後日期)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_exceptional($ord_num) {

		$sql = $this->sql;
		$eta_date='';
		############ 當 AP_mark為空時 ~~~~ 就不要寫入 ~~~~~~(代表主料有未請購)
		$q_str = "SELECT id FROM exceptional WHERE exceptional.ord_num = '".$ord_num."' AND oth_exc = 1";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't find.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$row = $sql->fetch($q_result);

		if (isset($row[0]))
		{
			return $row[0];
		}else{
			return '';
		}
	} // end func	


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	-> buy_get_no($hend,$n_field,$tables)	為新單據做編號
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_no($hend,$n_field,$tables) {
		$sql = $this->sql;
		$fields = array();		
		$q_str = "SELECT ". $n_field." FROM ".$tables." where ".$n_field. " like '%".$hend."%' order by ".$n_field." desc limit 1";
		if (!$q_result = $sql->query($q_str)) {		//搜尋最後一筆
			$this->msg->add("Error! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {	//如果沒有資料的話
			$buy_no = '1';
		
		}else{	//將最後一筆的數字+1
			$long = strlen($hend);
			$buy_no = substr($row[$n_field],$long);	//去掉表頭
			
			settype($buy_no, 'integer');
			$buy_no=$buy_no+1;
			settype($buy_no, 'string');			
		}
		
		if (strlen($buy_no) == 1)	//在數字前補0到達四位數字
		{
			$buy_no=$hend."00".$buy_no;
		}else if(strlen($buy_no) == 2){
			$buy_no=$hend."0".$buy_no;
		}else{
			$buy_no=$hend.$buy_no;
		}		
		return $buy_no;
	} // end func
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->  get_cost_no($hend,$n_field,$tables)	為新單據做編號
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_cost_no($hend,$n_field,$tables) {
		$sql = $this->sql;
		$fields = array();		
		$q_str = "SELECT ". $n_field." FROM ".$tables." where ".$n_field. " like '%".$hend."%' order by ".$n_field." desc limit 1";
		if (!$q_result = $sql->query($q_str)) {		//搜尋最後一筆
			$this->msg->add("Error! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {	//如果沒有資料的話
			$buy_no = '1';
		
		}else{	//將最後一筆的數字+1
			$long = strlen($hend);
			$buy_no = substr($row[$n_field],$long);	//去掉表頭
			
			settype($buy_no, 'integer');
			$buy_no=$buy_no+1;
			settype($buy_no, 'string');			
		}
		
		if (strlen($buy_no) == 1)	//在數字前補0到達四位數字
		{
			$buy_no=$hend."0".$buy_no;
		}else{
			$buy_no=$hend.$buy_no;
		}		
		return $buy_no;
	} // end func	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新 訂單記錄
#						傳回 $id
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_main($parm) {
					
		$sql = $this->sql;

					# 加入資料庫(2007.03.02加入尺吋資料)
		$q_str = "INSERT INTO remun (num,fty,dept,rem_date,out_month,open_user) 
				  VALUES('".
							$parm['num']."','".
							$parm['fty']."','".
							$parm['dept']."','".
							date('Y-md')."','".						
							$parm['out_month']."','".	
							$parm['submit_user']."')";
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot append order");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$ord_id = $sql->insert_id();  //取出 新的 id

		return $ord_id;

	} // end func
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新 訂單記錄
#						傳回 $id
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_det($parm) {
					
		$sql = $this->sql;

					# 加入資料庫(2007.03.02加入尺吋資料)
		$q_str = "INSERT INTO remun_det (rem_id,ord_num,acc_cost,oth_cost,exc_cost,smpl,rmk,rem_qty) 
				  VALUES('".
							$parm['rem_id']."','".
							$parm['ord_num']."','".
							$parm['acc_cost']."','".		
							$parm['oth_cost']."','".	
							$parm['exc_cost']."','".					
							$parm['smpl']."','".	
							$parm['rmk']."','".
							$parm['rem_qty']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot append order");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$ord_id = $sql->insert_id();  //取出 新的 id

		return $ord_id;

	} // end func
	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->emat_del($id)		刪除一般請購明細
#			mode=0 : EDIT    mode=1 : REVISE
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function del_remun($id, $rem_id ,$PHP_ord_num) {
$sql = $this->sql;

	$q_str="DELETE FROM remun_det WHERE id='".$id."'";
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error !  Database can't update.");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	$GLOBALS['order']->update_field_num('fty_cm','',$PHP_ord_num);


	$where_str = " AND rem_id = '$rem_id' ";
	$rem = $this->get_rem_fld('remun_det.id',$where_str);
	if(!$rem)
	{
		$q_str="DELETE FROM remun WHERE id='".$rem_id."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		return '';
	}

	return $rem_id;

}// end func	
	


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->function update_fields($field1, $value1, $id, $table='remun')
#
#		同時更新兩個 field的值 (以編號)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_fields($field1, $value1, $id, $table='remun') {

		$sql = $this->sql;

		#####   更新資料庫內容
		############ 當 為空日期時 ~~~~ 就不要寫入 ~~~~~~

		$q_str = "UPDATE ".$table." SET ".$field1." ='".$value1.
								"' WHERE id= '".	$id ."'";
//echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return $id;
	} // end func
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->function update_fields($field1, $value1, $id, $table='remun')
#
#		同時更新兩個 field的值 (以編號)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_rem_fields($field1, $value1, $id, $table='remun') {

		$sql = $this->sql;

		#####   更新資料庫內容
		############ 當 為空日期時 ~~~~ 就不要寫入 ~~~~~~

		$q_str = "UPDATE ".$table." SET ".$field1." ='".$value1.
								"' WHERE rem_id= '".	$id ."'";
//echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return $id;
	} // end func	


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0, $dept='',$limit_entries=0) 搜尋 訂 單 資料
#					// 2005/11/24 加入 $limit_entries
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_remun($mode=0, $limit_entries=0) {

		$sql = $this->sql;
		$argv = $_SESSION['sch_parm'];   //將所有的 globals 都抓入$argv

		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT remun.*, sum(remun_det.rem_qty)as qty, sum(remun_det.smpl)as smpl, sum((remun_det.rem_qty+remun_det.smpl) * (remun_det.acc_cost+remun_det.cost+remun_det.oth_cost+remun_det.exc_cost + s_order.handling_fee )) as acost FROM remun, remun_det, s_order ";
		
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['action']);
		$srh->add_sort_condition("remun.id DESC");
		$srh->row_per_page = 20;

	if($limit_entries){    // 當有限定最大量時~~~ 2005/11/28 加入
			$srh->q_limit = "LIMIT ".$limit_entries." ";
	}else{
##--*****--2006.11.16頁碼新增 start		##		
		$pagesize=10;
		if ($argv['sr_startno']) {
			$pages = $srh->get_page($argv['sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
 ##--*****--2006.11.16頁碼新增 end	   ##
	}


	//2006/05/12 adding 
   if ($mode == 1){
		$mesg = '';
		if ($str = $argv['fty'] )  { 
			$srh->add_where_condition("remun.fty = '$str'", "PHP_FTY",$str); 
			$mesg.= "  FTY : [ $str ]. ";
			}
		
		if ($str = $argv['year'] )  { 
			$srh->add_where_condition("remun.out_month like '$str%'", "PHP_year",$str); 
			$mesg.= "  Year = [ $str ]. ";
			}
		if ($str = $argv['month'] )  { 
			$srh->add_where_condition("remun.out_month like '%-$str'", "PHP_month",$str); 
			$mesg.= "  Month = [ $str ]. ";
			}
		if ($mesg)
		{
			$msg = "Search ".$mesg;
			$this->msg->add($msg);
		}			
   }	
   
   if ($mode == 2) $srh->add_where_condition("remun.status = 2");
   if ($mode == 3) $srh->add_where_condition("remun.status = 4");
   
   $srh->add_where_condition("remun.id = remun_det.rem_id");
   $srh->add_where_condition("s_order.order_num = remun_det.ord_num");
   $srh->add_group_condition("remun_det.rem_id");
		$result= $srh->send_query2($limit_entries);   // 2005/11/24 加入 $limit_entries
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}

		$this->msg->merge($srh->msg);
			if (!$result){   // 當查尋無資料時
				$op['record_NONE'] = 1;
			}
		$op['rem'] = $result;  // 資料錄 拋入 $op
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['cgistr_post'] = $srh->get_cgi_str(1);
		$op['prev_no'] = $srh->prev_no;
		$op['next_no'] = $srh->next_no;
		$op['max_no'] = $srh->max_no;
		$op['last_no'] = $srh->last_no;
		$op['start_no'] = $srh->start_no;
		$op['per_page'] = $srh->row_per_page;
		// echo $srh->q_str;
if(!$limit_entries){ 
##--*****--2006.11.16頁碼新增 start			
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] = $pages;
		$op['now_pp'] = $srh->now_pp;
    $op['lastpage']=$pages[$pagesize-1];		
##--*****--2006.11.16頁碼新增 end
}	

		return $op;
	} // end func
	
	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0, $dept='',$limit_entries=0) 搜尋 訂 單 資料
#					// 2005/11/24 加入 $limit_entries
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function search_remun_check($mode=0, $limit_entries=0) {

    $sql = $this->sql;
    $argv = $GLOBALS;   //將所有的 globals 都抓入$argv

    $srh = new SEARCH();
    $cgi = array();
    if (!$srh->set_sql($sql)) {
        $this->msg->merge($srh->msg);
        return false;
    }

    $q_header = "SELECT remun.*, sum(rem_qty)as qty, sum(smpl)as smpl, sum((rem_qty+smpl) * (acc_cost+cm)) as cost FROM remun, remun_det, s_order ";
    
    if (!$srh->add_q_header($q_header)) {
        $this->msg->merge($srh->msg);
        return false;
    }
    $srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
    $srh->add_sort_condition("remun.id DESC");
    $srh->row_per_page = 20;

	if($limit_entries){    // 當有限定最大量時~~~ 2005/11/28 加入
			$srh->q_limit = "LIMIT ".$limit_entries." ";
	}else{
        ##--*****--2006.11.16頁碼新增 start		##		
		$pagesize=10;
		if (isset($argv['PHP_sr_cm']) && $argv['PHP_sr_cm']) {
			$pages = $srh->get_page($argv['PHP_sr_cm'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
        ##--*****--2006.11.16頁碼新增 end	   ##
	}
   
	# 分部門顯示
	$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
	$user_team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
    if ($user_team == 'MD') {
        $srh->add_where_condition("s_order.dept = '$user_dept'", "",$user_dept,"Department=[ $user_dept ]. ");
    } else {
        if ( if_factory() && !$argv['PHP_factory'] ) {
            $srh->add_where_condition("s_order.factory = '$user_dept'", "",$user_dept,"Factory=[ $user_dept ]. ");
        } else {
            $dept_group = get_dept_group();
            for ($i=0; $i< sizeof($dept_group); $i++) {			
                $srh->or_where_condition("s_order.dept = '$dept_group[$i]'", "",$dept_group[$i],"Department=[ $dept_group[$i] ]. ");
            }
        }
    }
    
    // $srh->add_where_condition("remun.dept = $user_dept");
    if ($mode == 2) $srh->add_where_condition("remun.status = 2");
    if ($mode == 3) $srh->add_where_condition("remun.status = 4");
    if ($mode == 4) $srh->add_where_condition("remun.status = 3");

    $srh->add_where_condition("remun.id = remun_det.rem_id");
    $srh->add_where_condition("s_order.order_num = remun_det.ord_num");
    $srh->add_group_condition("remun_det.rem_id");
    $result= $srh->send_query2($limit_entries);   // 2005/11/24 加入 $limit_entries
    if (!is_array($result)) {
        $this->msg->merge($srh->msg);
        return false;		    
    }
    
    // echo $srh->q_str;

    $this->msg->merge($srh->msg);
    if (!$result){   // 當查尋無資料時
        $op['record_NONE'] = 1;
    }
    $op['rem'] = $result;  // 資料錄 拋入 $op
    $op['cgistr_get'] = $srh->get_cgi_str(0);
    $op['cgistr_post'] = $srh->get_cgi_str(1);
    
    $op['max_no'] = $srh->max_no;
    // echo $srh->q_str;
    if(!$limit_entries){ 
        ##--*****--2006.11.16頁碼新增 start			
        $op['maxpage'] =$srh->get_max_page();
        $op['pages'] = $pages;
        $op['now_pp'] = $srh->now_pp;
        $op['lastpage']=$pages[$pagesize-1];		
        ##--*****--2006.11.16頁碼新增 end
    }	

    return $op;
} // end func

	
	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $order_num=0)	抓出指定訂單記錄資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get($id) {

$sql = $this->sql;

# 主檔
$q_str = "SELECT remun.* FROM remun WHERE  remun.id = $id";
if (!$q_result = $sql->query($q_str)) {
    $this->msg->add("Error ! Database can't access!");
    $this->msg->merge($sql->msg);
    return false;    
}

if (!$row = $sql->fetch($q_result)) {
    $this->msg->add("Error ! Can't find this record!");
    return false;    
}

$op['remun']=$row;

# 改變Login帳號為名字
$po_user=$GLOBALS['user']->get(0,$op['remun']['submit_user']);
$row['submit_id'] = $row['submit_user'];
if ($po_user['name'])$op['remun']['submit_user'] = $po_user['name'];	

$po_user=$GLOBALS['user']->get(0,$op['remun']['open_user']);
$row['open_id'] = $row['open_user'];
if ($po_user['name'])$op['remun']['open_user'] = $po_user['name'];	

$po_user=$GLOBALS['user']->get(0,$op['remun']['cfm_user']);
$row['cfm_id'] = $row['cfm_user'];
if ($po_user['name'])$op['remun']['cfm_user'] = $po_user['name'];	

$po_user=$GLOBALS['user']->get(0,$op['remun']['rev_user']);
$row['rev_id'] = $row['rev_user'];
if ($po_user['name'])$op['remun']['rev_user'] = $po_user['name'];	

$po_user=$GLOBALS['user']->get(0,$op['remun']['apv_user']);
$row['apv_id'] = $row['apv_user'];
if ($po_user['name'])$op['remun']['apv_user'] = $po_user['name'];	

$fty = $op['remun']['fty'];
$e_date = $op['remun']['out_month']."-31";

$q_strs = "SELECT s_order.status , remun.status as r_status  FROM remun,s_order,remun_det WHERE  remun_det.rem_id = remun.id AND s_order.order_num = remun_det.ord_num AND remun.id = '".$op['remun']['id']."'";
if (!$q_results = $sql->query($q_strs)) {
    $this->msg->add("Error ! Database can't access!");
    $this->msg->merge($sql->msg);
    return false;    
}

$rows = $sql->fetch($q_results);

# 明細
if ( !empty($rows['order_num']) && ( $rows['status'] == '14' || $rows['order_num'] == 'AMN0-237' ) ){

    $q_str="SELECT remun_det.*, remun_det.cost as cm_cost, s_order.fty_cm as fty_cm, s_order.org_cm, s_order.style_num, sum(saw_out_put.qty) as qty, s_order.smpl_ord, s_order.id as ord_id, 
    style_type.des as style_des, s_order.ie1, s_order.id as ord_id, s_order.qty as ord_qty, s_order.ie2 ,s_order.order_num ,s_order.dept 
                FROM remun, remun_det, s_order, saw_out_put, style_type 
                WHERE 
                remun_det.rem_id = remun.id AND s_order.order_num = remun_det.ord_num AND 
                s_order.order_num = saw_out_put.ord_num AND s_order.style = style_type.style_type AND 
                saw_out_put.ord_num = remun_det.ord_num AND remun.id = $id GROUP BY s_order.order_num;"; 
                
} else {

    $q_str="SELECT remun_det.*, remun_det.cost as cm_cost, s_order.fty_cm as fty_cm, s_order.org_cm, s_order.style_num, sum(shipping.qty) as qty, 
               s_order.smpl_ord, style_type.des as style_des, s_order.ie1,s_order.order_num, s_order.opendate, s_order.factory, s_order.handling_fee,
               s_order.id as ord_id, s_order.qty as ord_qty, s_order.ie2 ,s_order.dept 
                FROM remun, remun_det, s_order, shipping, style_type
                WHERE 
                remun_det.rem_id = remun.id AND s_order.order_num = remun_det.ord_num AND 
                s_order.order_num = shipping.ord_num AND s_order.style = style_type.style_type AND 
                shipping.k_date <= '".$e_date."' AND 
                shipping.ord_num = remun_det.ord_num AND remun.id = $id GROUP BY s_order.order_num"; 
}
				
// echo $q_str."<br>"; #fty_cm

if (!$q_result = $sql->query($q_str)) {
    $this->msg->add("Error ! Database can't access!");
    $this->msg->merge($sql->msg);
    return false;
}

$i=0;
$tmp_cost =	$tmp_qty = $tmp_smpl = $tmp_ord = 0;
while ($row1 = $sql->fetch($q_result)) {

    $where_str = " AND out_month = '".$op['remun']['out_month']."' AND ord_num = '".$row1['ord_num']."' GROUP BY ord_num";
    $rem_tal = $this->get_rem_fld('sum(rem_qty) as remun_qty',$where_str);
    $row1['rmn_qty'] = $row1['qty'] - $rem_tal + $row1['rem_qty'];

    $ie = $row1['ie1'];
    if($row1['ie2'] > 0)$ie = $row1['ie2'];
    
    # 如果有 Final IE 強制修改 CM ，將未 Submit 的 IE * CM
    if ( $rows['r_status'] ==  '0' ) {
	
		# GET 原始 CM
		$row1['cm_cost'] = number_format(($ie * $row1['org_cm']),2,'.','');
		
		# 如果該單有寫異常修改 FOB 時修改
		if ( $exc_cost = $this->get_exceptional_cost($row1['order_num']) ) {
			$row1['cm_cost'] = $exc_cost;
		}

		$this->update_fields('cost',$row1['cm_cost'],$row1['id'],'remun_det');
    }
	
	$row1['a_cost'] = $row1['cm_cost'] + $row1['acc_cost'] + $row1['oth_cost'] + $row1['exc_cost'] + $row1['handling_fee'];
    $row1['cost'] = $row1['a_cost'] * ($row1['rem_qty']+$row1['smpl']);

    $tmp_cost  = $tmp_cost +$row1['cost'];
    $tmp_qty += $row1['rem_qty'];
    $tmp_ord += $row1['ord_qty'];
    $tmp_smpl += $row1['smpl'];

    $tmp =  explode(' ',$row1['style_des']);
    $row1['style_des'] = $tmp[0];
    $row1['exc_id'] = $this->get_exceptional($row1['ord_num']);
    $op['rem_det'][]=$row1;

}

$op['total_cost'] = $tmp_cost;
$op['total_qty'] = $tmp_qty;
$op['total_smpl'] = $tmp_smpl;
$op['total_ord_qty'] = $tmp_ord;

# 備註
$q_str="SELECT remun_log.* FROM remun_log WHERE rem_id = $id ";

if (!$q_result = $sql->query($q_str)) {
    $this->msg->add("Error ! Database can't access!");
    $this->msg->merge($sql->msg);
    return false;    
}

while ($row1 = $sql->fetch($q_result)) {		
    $op['rem_log'][]=$row1;			
}

# 上傳檔
$q_str="SELECT remun_file.* FROM remun_file WHERE num = $id ";

if (!$q_result = $sql->query($q_str)) {
    $this->msg->add("Error ! Database can't access!");
    $this->msg->merge($sql->msg);
    return false;    
}

while ($row1 = $sql->fetch($q_result)) {		
    $op['done'][]=$row1;			
}

// print_r($op); # rem_det
return $op;

} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_exceptional_cost($ord_num)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_exceptional_cost($ord_num) {

	$sql = $this->sql;
	$eta_date='';
	############ 當 AP_mark為空時 ~~~~ 就不要寫入 ~~~~~~(代表主料有未請購)
	$q_str = "
	SELECT `exc_static`.`org_rec` 
	FROM `exceptional` , `exc_static` 
	WHERE `exceptional`.`id` = `exc_static`.`exc_id` AND `exceptional`.`ord_num` = '".$ord_num."' AND `exc_static`.`state` = '8' ;";
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error !  Database can't find.");
		$this->msg->merge($sql->msg);
		return false;    
	}
	$row = $sql->fetch($q_result);

	if (isset($row[0]))
	{
		return $row[0];
	}else{
		return '';
	}
} // end func	


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit_det($parm)	
#
#		同時更新兩個 field的值 (以編號)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function edit_det($parm) {

		$sql = $this->sql;

		#####   更新資料庫內容
		############ 當 為空日期時 ~~~~ 就不要寫入 ~~~~~~

		$q_str = "UPDATE remun_det SET acc_cost ='".$parm['acc_cost'].
																		"', oth_cost='".$parm['oth_cost'].
																		"', exc_cost='".$parm['exc_cost'].
																		"', smpl='".$parm['smpl'].
																		"',rem_qty='".$parm['rem_qty'].
																		"',rmk='".$parm['rmk'].
								"' WHERE id= '".	$parm['id'] ."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func




#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit_det($parm)	
#
#		同時更新兩個 field的值 (以編號)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function submit_remun ($id,$sub_user) {

		$sql = $this->sql;

		#####   更新資料庫內容
		############ 當 為空日期時 ~~~~ 就不要寫入 ~~~~~~

		$q_str = "UPDATE remun SET status ='2', submit_user='".$sub_user."', submit_date='".date('Y-m-d').
								"' WHERE id= '".	$id ."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->cfm_remun($id,$sub_user)	
#
#		工繳確認
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function cfm_remun($id,$sub_user) {

		$sql = $this->sql;

		#####   更新資料庫內容
		############ 當 為空日期時 ~~~~ 就不要寫入 ~~~~~~

		$q_str = "UPDATE remun SET status ='4', cfm_user='".$sub_user."', cfm_date='".date('Y-m-d')."' WHERE id= '".$id ."'";
		// echo $q_str.'<br>';
		// exit;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->rev_remun($id,$sub_user)	
#
#		工繳確認
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function rev_remun($id,$sub_user) {

		$sql = $this->sql;

		#####   更新資料庫內容
		############ 當 為空日期時 ~~~~ 就不要寫入 ~~~~~~

		// $q_str = "UPDATE remun SET status = '4', rev_user='".$sub_user."', rev_date='".date('Y-m-d')."' WHERE id = '".$id ."'";
		$q_str = "UPDATE remun SET status = '6', apv_user='".$sub_user."', apv_date='".date('Y-m-d')."' WHERE id = '".$id ."'";

		// echo $q_str.'<br>';
		// exit;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->apv_remun($id,$sub_user)
#
#		工繳核可
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function apv_remun($id,$sub_user) {

		$sql = $this->sql;

		#####   更新資料庫內容
		############ 當 為空日期時 ~~~~ 就不要寫入 ~~~~~~

		$q_str = "UPDATE remun SET status ='6', apv_user='".$sub_user."', apv_date='".date('Y-m-d').
								"' WHERE id= '".	$id ."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func	

	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit_det($parm)	
#
#		同時更新兩個 field的值 (以編號)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function reject_remun($id) {

		$sql = $this->sql;

		#####   更新資料庫內容
		############ 當 為空日期時 ~~~~ 就不要寫入 ~~~~~~

		$q_str = "UPDATE remun SET status ='7', rev_user='', rev_date='', cfm_user='', cfm_date='', submit_user ='', submit_date = ''
							WHERE id= '".	$id ."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func		
/*
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fab_eta($ord_num)
#
#		取得主料的ETA(最後日期)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_cm($parm) {
   $output = array();
		$sql = $this->sql;
		$eta_date='';

		$q_str = "SELECT s_order.*, avg(remun_det.acc_cost) as fty_acc_cost, sum(remun_det.rem_qty) as rem_qty, style_type.des as style_des, remun_det.id as rem_id
										 , s_order.fty_cm as fty_cm
							FROM s_order, remun_det, remun, style_type
							WHERE s_order.order_num =remun_det.ord_num AND remun_det.rem_id = remun.id AND s_order.style = style_type.style_type
							
										AND  s_order.factory = '".$parm['fty']."'
										AND s_order.dept = '".$parm['dept']."' AND s_order.cust = '".$parm['cust']."'  
										AND remun.out_month = '".$parm['out_date']."' AND remun_det.sc_mk = 0
							GROUP BY remun_det.ord_num ";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't find.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			$row['mat_cost'] = $row['mat_useage'] * $row['mat_u_cost'];
			$row['in_cost'] = $row['rem_qty'] * $row['uprice'];
			$row['acc_u_cost'] = $row['interline'] + $row['acc_u_cost'] + $row['fusible'];
			$row['a_cost'] = $row['mat_u_cost'] + $row['acc_u_cost']+$row['fty_acc_cost']+$row['comm_fee']+$row['fty_cm'];
	   	$row['cost'] = $row['a_cost'] * $row['rem_qty'];
	   	$row['gross'] = $row['in_cost'] - $row['cost'];
	   	$row['gross_rate'] = $row['gross'] / $row['in_cost'] * 100;
	   	$row['ship_qty'] =  $this->get_ship_qty($row['order_num'], $parm['out_date']);
	   	if($row['ship_qty'] > 0)
	   	{ 
				$row['ship_cm'] = ( $row['fty_cm'] * $row['rem_qty'] ) / $row['ship_qty'];
	   		$output[]=$row;
	   	}
	   
		}


		return $output;
	} // end func		
	
	*/
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fab_eta($ord_num)
#
#		取得主料的ETA(最後日期)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_cm($parm) {
   $output = array();
		$sql = $this->sql;
		$eta_date='';

		$q_str = "SELECT s_order.*, avg(remun_det.acc_cost) as fty_acc_cost, sum(remun_det.rem_qty) as rem_qty, style_type.des as style_des, remun_det.id as rem_id
										 , s_order.fty_cm as fty_cm, remun.out_month
							FROM s_order, remun_det, remun, style_type
							WHERE s_order.order_num =remun_det.ord_num AND remun_det.rem_id = remun.id AND s_order.style = style_type.style_type
							
										AND  s_order.factory = '".$parm['fty']."'
										AND s_order.dept = '".$parm['dept']."' AND s_order.cust = '".$parm['cust']."'  
										AND remun.out_month <= '".$parm['out_date']."' AND remun_det.sc_mk = 1
							GROUP BY remun_det.ord_num ";
//echo $q_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't find.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
//	   	$row['ship_qty'] =  $this->get_ship_qty($row['order_num'], $parm['out_date']);
				$row['ship_qty'] = $row['rem_qty'];
	   	if($row['ship_qty'] > 0)
	   	{ 
				$row['fty_acc_cost'] = number_format($row['fty_acc_cost'],2,'.','');
				$row['mat_cost'] = $row['mat_useage'] * $row['mat_u_cost'];
				$row['in_cost'] = $row['rem_qty'] * $row['uprice'];
				$row['acc_u_cost'] = $row['interline'] + $row['acc_u_cost'] + $row['fusible'];
				$row['a_cost'] = $row['mat_u_cost'] + $row['acc_u_cost']+$row['fty_acc_cost']+$row['comm_fee']+$row['fty_cm'];
	   		$row['cost'] = $row['a_cost'] * $row['rem_qty'];
	   		$row['gross'] = $row['in_cost'] - $row['cost'];
	   		$row['gross_rate'] = $row['gross'] / $row['in_cost'] * 100;								
				
				//計算出口時的CM
//				$row['ship_cm'] = ( $row['fty_cm'] * $row['rem_qty'] ) / $row['ship_qty'];
//				$row['ship_cm'] = number_format($row['ship_cm'],2,'.','');
					$row['ship_cm'] = $row['fty_cm'];
				//取得工繳資料ID
				$where_str = " AND ord_num = '".$row['order_num']."' AND remun.out_month <= '".$parm['out_date']."' AND remun_det.sc_mk = 1";
				$rem_id =  $this->get_rem_det('remun_det.id', $where_str);
				$row['rem_id'] = array2csv($rem_id);
				
				
	   		$output[]=$row;
	   	}
	   
		}


		return $output;
	} // end func			
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fab_eta($ord_num)
#
#		取得主料的ETA(最後日期)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_ship_qty($ord_num, $out_date) {
   $output = array();
		$sql = $this->sql;
		$eta_date='';

		$q_str = "SELECT  sum(qty) as qty										 
							FROM shipping
							WHERE ord_num = '".$ord_num."' AND k_date like '".$out_date."%'
							GROUP BY shipping.ord_num ";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't find.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$row = $sql->fetch($q_result);


		return $row['qty'];
	} // end func			
	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fab_eta($ord_num)
#
#		取得主料的ETA(最後日期)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_rem_det($field, $where_str) {
   $rtn_ary = array();
		$sql = $this->sql;
		$eta_date='';

		$q_str = "SELECT  $field 	FROM remun_det, remun
							WHERE remun_det.rem_id = remun.id ".$where_str;
						
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't find.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result))
		{
			$rtn_ary[] = $row[0];
		}


		return $rtn_ary;
	} // end func		
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新 訂單記錄
#						傳回 $id
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_cost_main($parm) {
					
		$sql = $this->sql;

//查詢最後版本 
		$q_str = "SELECT ver FROM cust WHERE cust_s_name='".$parm['cust']."' ORDER BY ver DESC LIMIT 1";
		$q_result = $sql->query($q_str);
		$cust_row = $sql->fetch($q_result);	

					# 加入資料庫(2007.03.02加入尺吋資料)
		$q_str = "INSERT INTO salescost (sc_num,cust,cust_ver,fty,dept,out_month,open_user,inv,open_date) 
				  VALUES('".
				  		$parm['num']."','".
							$parm['cust']."','".
							$cust_row['ver']."','".
							$parm['fty']."','".
							$parm['dept']."','".						
							$parm['out_month']."','".	
							$parm['open_user']."','".
							$parm['inv']."','".
							$parm['open_date']."')";
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot append order");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$ord_id = $sql->insert_id();  //取出 新的 id

		return $ord_id;

	} // end func	


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新 訂單記錄
#						傳回 $id
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_cost_det($parm) {

		$sql = $this->sql;

					# 加入資料庫(2007.03.02加入尺吋資料)
		$q_str = "INSERT INTO salescost_det (cost_id,remdt_id,etd,qty,quota,fob,fab_cost,yy,acc_cost,acc_f_cost,smpl_cost,comm,rmk,cm) 
				  VALUES('".
							$parm['cost_id']."','".
							$parm['rem_id']."','".
							$parm['etd']."','".						
							$parm['qty']."','".	
							$parm['quota']."','".							
							$parm['fob']."','".
							$parm['fab_cost']."','".
							$parm['yy']."','".
							$parm['acc_cost']."','".
							$parm['acc_f_cost']."','".							
							$parm['smpl_cost']."','".
							$parm['comm']."','".	
							$parm['remark']."','".						
							$parm['cm']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot append order");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$ord_id = $sql->insert_id();  //取出 新的 id

		return $ord_id;

	} // end func	 
	
	
 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->emat_del($id)		刪除一般請購明細
#			mode=0 : EDIT    mode=1 : REVISE
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del_cost($id, $cost_id) {
	$sql = $this->sql;

	 	$q_str="DELETE FROM salescost_det WHERE id='".$id."'";
	 	if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}         
		 $where_str = " AND cost_id = '$cost_id' ";
	   $rem = $this->get_cost_fld('salescost_det.id',$where_str);
		 if(!$rem)
		 {
	 			$q_str="DELETE FROM salescost WHERE id='".$cost_id."'";
	 			if (!$q_result = $sql->query($q_str)) {
					$this->msg->add("Error !  Database can't update.");
					$this->msg->merge($sql->msg);
					return false;    
				}
				return '';
			}
	
			return $cost_id;
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fab_eta($ord_num)
#
#		取得主料的ETA(最後日期)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_cost_fld($fld,$where) {

		$sql = $this->sql;
		$eta_date='';
		$q_str = "SELECT ".$fld." FROM salescost, salescost_det WHERE salescost.id = salescost_det.cost_id ".$where;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't find.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$row = $sql->fetch($q_result);

		if (isset($row[0]))
		{
			return $row[0];
		}else{
			return false;
		}
	} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fab_eta($ord_num)
#
#		取得主料的ETA(最後日期)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_cost($id,$ck=0) {

		$sql = $this->sql;
		$eta_date='';

		$q_str = "SELECT salescost.*, cust.cust_init_name as cust_init, dept.dept_name	
							FROM salescost, cust, dept
						  WHERE  dept.dept_code = salescost.dept AND cust.cust_s_name = salescost.cust
							       AND salescost.cust_ver = cust.ver AND salescost.id ='$id'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't find.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$op['cost'] = $sql->fetch($q_result);
if($ck == 0)echo "<font color=#FFFFFF>xxx</font>";
	$po_user=$GLOBALS['user']->get(0,$op['cost']['open_user']);
	if ($po_user['name'])$op['cost']['open_user'] = $po_user['name'];	
	$po_user=$GLOBALS['user']->get(0,$op['cost']['submit_user']);
	if ($po_user['name'])$op['cost']['submit_user'] = $po_user['name'];	
	$po_user=$GLOBALS['user']->get(0,$op['cost']['cfm_user']);
	if ($po_user['name'])$op['cost']['cfm_user'] = $po_user['name'];
	$po_user=$GLOBALS['user']->get(0,$op['cost']['rev_user']);
	if ($po_user['name'])$op['cost']['rev_user'] = $po_user['name'];		
	$po_user=$GLOBALS['user']->get(0,$op['cost']['apv_user']);
	if ($po_user['name'])$op['cost']['apv_user'] = $po_user['name'];	
	if ($op['cost']['status'] == 4 || $op['cost']['version'] > 0 ) $op['cost']['version']++;

/*
		$q_str = "SELECT s_order.order_num, (s_order.mat_useage*s_order.mat_u_cost) as o_mat,
										 s_order.etd as o_etd, s_order.quota as o_quota, s_order.uprice,
										 s_order.mat_useage, s_order.mat_u_cost,
										 (s_order.interline+s_order.acc_u_cost+s_order.fusible) as o_acc,  
										 s_order.comm_fee, s_order.cm as fty_cm, s_order.uprice, salescost_det.*,
										 remun_det.acc_cost as fty_acc_cost,  style_type.des as style_des,
										 sum(shipping.qty) as rem_qty
							FROM s_order, remun_det, style_type, salescost, salescost_det, shipping
							WHERE s_order.order_num =remun_det.ord_num AND s_order.style = style_type.style_type
										AND s_order.order_num = shipping.ord_num AND shipping.ord_num = remun_det.ord_num
										AND salescost.id = salescost_det.cost_id AND salescost_det.remdt_id = remun_det.id
										AND shipping.k_date like '".$op['cost']['out_month']."%'
										AND salescost.id ='$id' 
							GROUP BY s_order.order_num";


		$q_str = "SELECT s_order.order_num, (s_order.mat_useage*s_order.mat_u_cost) as o_mat,
										 s_order.etd as o_etd, s_order.quota as o_quota, s_order.uprice,
										 s_order.mat_useage, s_order.mat_u_cost,
										 (s_order.interline+s_order.acc_u_cost+s_order.fusible) as o_acc,  
										 s_order.comm_fee, s_order.cm as fty_cm, s_order.uprice, salescost_det.*,
										 remun_det.acc_cost as fty_acc_cost, remun_det.rem_qty, style_type.des as style_des
							FROM s_order, remun_det, style_type, salescost, salescost_det
							WHERE s_order.order_num =remun_det.ord_num AND s_order.style = style_type.style_type
										AND salescost.id = salescost_det.cost_id AND salescost_det.remdt_id = remun_det.id
										AND salescost.id ='$id'";
*/


		$q_str = "SELECT s_order.order_num, (s_order.mat_useage*s_order.mat_u_cost) as o_mat,
										 s_order.etd as o_etd, s_order.quota as o_quota, s_order.uprice,
										 s_order.mat_useage, s_order.mat_u_cost, s_order.qty as ord_qty,
										 (s_order.interline+s_order.acc_u_cost+s_order.fusible) as o_acc,  
										 s_order.comm_fee, s_order.cm as fty_cm, s_order.uprice, salescost_det.*,
										 remun_det.acc_cost as fty_acc_cost, remun_det.rem_qty, style_type.des as style_des										
							FROM s_order, remun_det, style_type, salescost, salescost_det
							WHERE s_order.order_num =remun_det.ord_num AND s_order.style = style_type.style_type
										AND salescost.id = salescost_det.cost_id AND salescost_det.remdt_id = remun_det.id
										AND salescost.id ='$id'";


		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't find.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
//			$tmp = $this->get_cm_by_cost($row['order_num'],$op['cost']['out_month']);
	//		$row['rem_qty'] = $tmp['rem_qty'];
			$row['rem_qty'] = $row['ord_qty'];
//			$row['fty_acc_cost'] = $tmp['fty_acc_cost'];
			//訂單的成本(預估)
			$row['o_in_cost'] = $row['rem_qty'] * $row['uprice'];
			$row['o_a_cost'] = $row['o_mat'] + $row['o_acc']+$row['fty_acc_cost']+$row['comm_fee']+$row['fty_cm'];
	   	$row['o_cost'] = $row['o_a_cost'] * $row['rem_qty'];
	   	$row['o_gross'] = $row['o_in_cost'] - $row['o_cost'];
	   	$row['o_gross_rate'] = $row['o_gross'] / $row['o_in_cost'] * 100;
	    
	    //訂單的成本(實際)
			$row['c_fab'] = $row['fab_cost'] * $row['yy'];
			$row['c_in_cost'] = $row['qty'] * $row['fob'];
			$row['c_a_cost'] = $row['c_fab'] + $row['acc_cost']+$row['acc_f_cost']+$row['comm']+$row['cm'];
	   	$row['c_cost'] = $row['c_a_cost'] * $row['qty'];
	   	$row['c_gross'] = $row['c_in_cost'] - $row['c_cost'];
	   	$row['c_gross_rate'] = $row['c_gross'] / $row['c_in_cost'] * 100;
	  

	    //訂單的成本(差異)
			$row['d_qty'] = $row['qty'] - $row['rem_qty'] ;
			$row['d_fob'] = $row['fob'] - $row['uprice'] ;
			$row['d_in_cost'] = $row['c_in_cost'] - $row['o_in_cost'] ;
			$row['d_cm'] = $row['cm'] - $row['fty_cm'] ;
			



	    $tmp =  explode(' ',$row['style_des']);
	    $row['style_des'] = $tmp[0];
	    
	    
	    $op['scost'][]=$row;
	//    echo $row['order_num']."<br>";
	   
		}

		$q_str = "SELECT salescost_log.*	FROM salescost_log WHERE  salescost_log.cost_id ='$id'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't find.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {	    
	    $op['cost_log'][]=$row;	   
		}

		return $op;
	} // end func		




#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fab_eta($ord_num)
#
#		取得主料的ETA(最後日期)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_cm_by_cost($ord_num,$out_month) {

		$sql = $this->sql;


		$q_str = "SELECT avg(remun_det.acc_cost) as fty_acc_cost, sum(remun_det.rem_qty) as rem_qty, 
										 sum(remun_det.smpl) as smpl_qty
							FROM  remun_det, remun
							WHERE remun_det.rem_id = remun.id
										AND remun_det.ord_num = '$ord_num'
										AND remun.out_month ='$out_month'";


		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't find.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$row = $sql->fetch($q_result);


		return $row;
	} // end func		


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0, $dept='',$limit_entries=0) 搜尋 訂 單 資料
#					// 2005/11/24 加入 $limit_entries
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_cost($mode=0, $limit_entries=0) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv

		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT salescost.*, sum(qty * fob) as in_cost,
												sum(((fab_cost*yy)+acc_cost+acc_f_cost+comm+cm)*qty) as cost,
												sum((qty * fob) - (((fab_cost*yy)+acc_cost+acc_f_cost+comm+cm)*qty)) as gross, 
												(sum((qty * fob) - (((fab_cost*yy)+acc_cost+acc_f_cost+comm+cm)*qty))/sum(qty * fob))*100 as gross_rate
								 FROM salescost, salescost_det ";
		
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("salescost.id DESC");
		$srh->row_per_page = 20;

	if($limit_entries){    // 當有限定最大量時~~~ 2005/11/28 加入
			$srh->q_limit = "LIMIT ".$limit_entries." ";
	}else{
##--*****--2006.11.16頁碼新增 start		##		
		$pagesize=10;
		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
 ##--*****--2006.11.16頁碼新增 end	   ##
	}


	//2006/05/12 adding 
	$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
	$user_team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
	$sale_f_mang = $GLOBALS['SALES_F_MANG'];
	$sale_mang = $GLOBALS['SALES_MANG'];
	for ($i=0; $i< sizeof($sale_f_mang); $i++)
	{			
			if($user_dept == $sale_f_mang[$i]) 	$srh->add_where_condition("salescost.dept LIKE '".$sale_mang[$i]."%'", "PHP_dept",$sale_mang[$i],"");		
	}
	
	$sales_dept = $GLOBALS['SALES_DEPT'];
	if ($user_team == 'MD')	$srh->add_where_condition("salescost.dept = '$user_dept'", "PHP_dept","$user_dept","Search Dept =[ $user_dept ]");
	for ($i=0; $i< sizeof($sales_dept); $i++)
	{			
			if($user_dept == $sales_dept[$i] && $user_team <> 'MD') 	$srh->add_where_condition("salescost.dept = '$user_dept'", "PHP_dept","$user_dept","Search Dept =[ $user_dept ]");
	}

  
   if ($mode==1){
		$mesg = '';
		if ($str = $argv['PHP_FTY'] )  { 
			$srh->add_where_condition("salescost.fty = '$str'", "PHP_FTY",$str); 
			$mesg.= "  FTY : [ $str ]. ";
			}
		
		if ($str = $argv['PHP_year'] )  { 
			$srh->add_where_condition("salescost.out_month like '$str%'", "PHP_year",$str); 
			$mesg.= "  Year = [ $str ]. ";
			}
		if ($str = $argv['PHP_month'] )  { 
			$srh->add_where_condition("salescost.out_month like '%-$str'", "PHP_month",$str); 
			$mesg.= "  Month = [ $str ]. ";
			}
		if ($str = $argv['PHP_cust'] )  { 
			$srh->add_where_condition("salescost.cust = '$str'", "PHP_cust",$str); 
			$mesg.= "  Cust. = [ $str ]. ";
			}
		if ($str = $argv['PHP_dept'] )  { 
			$srh->add_where_condition("salescost.dept = '$str'", "PHP_dept",$str); 
			$mesg.= "  Dept. = [ $str ]. ";
			}
		if ($mesg)
		{
			$msg = "Search ".$mesg;
			$this->msg->add($msg);
		}			
   }	
   if ($mode==2){
   		if ($user_team == 'MD' ) $srh->add_where_condition("salescost.dept = $user_dept");
			$srh->add_where_condition("salescost.status = 2");
   }
   if ($mode==3){
   		if ($user_team == 'MD' ) $srh->add_where_condition("salescost.dept = $user_dept");
			$srh->add_where_condition("salescost.status = 4");
   }   
   
   $srh->add_where_condition("salescost.id = salescost_det.cost_id");
   $srh->add_group_condition("salescost_det.cost_id");
		$result= $srh->send_query2($limit_entries);   // 2005/11/24 加入 $limit_entries
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}

		$this->msg->merge($srh->msg);
			if (!$result){   // 當查尋無資料時
				$op['record_NONE'] = 1;
			}
		$op['cost'] = $result;  // 資料錄 拋入 $op
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['cgistr_post'] = $srh->get_cgi_str(1);
		$op['prev_no'] = $srh->prev_no;
		$op['next_no'] = $srh->next_no;
		$op['max_no'] = $srh->max_no;
		$op['last_no'] = $srh->last_no;
		$op['start_no'] = $srh->start_no;
		$op['per_page'] = $srh->row_per_page;
		
if(!$limit_entries){ 
##--*****--2006.11.16頁碼新增 start			
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] = $pages;
		$op['now_pp'] = $srh->now_pp;
    $op['lastpage']=$pages[$pagesize-1];		
##--*****--2006.11.16頁碼新增 end
}	

		return $op;
	} // end func
	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0, $dept='',$limit_entries=0) 搜尋 訂 單 資料
#					// 2005/11/24 加入 $limit_entries
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function search_uncfm_cost($mode=0, $limit_entries=0) {

    $sql = $this->sql;
    $argv = $GLOBALS;   //將所有的 globals 都抓入$argv

    $srh = new SEARCH();
    $cgi = array();
    if (!$srh->set_sql($sql)) {
        $this->msg->merge($srh->msg);
        return false;
    }

    $q_header = "SELECT salescost.*, sum(qty * fob) as in_cost,
                                            sum(((fab_cost*yy)+acc_cost+acc_f_cost+comm+cm)*qty) as cost,
                                            sum((qty * fob) - (((fab_cost*yy)+acc_cost+acc_f_cost+comm+cm)*qty)) as gross, 
                                            (sum((qty * fob) - (((fab_cost*yy)+acc_cost+acc_f_cost+comm+cm)*qty))/sum(qty * fob))*100 as gross_rate
                             FROM salescost, salescost_det ";
		
    if (!$srh->add_q_header($q_header)) {
        $this->msg->merge($srh->msg);
        return false;
    }
    $srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
    $srh->add_sort_condition("salescost.id DESC");
    $srh->row_per_page = 10;

	if($limit_entries){    // 當有限定最大量時~~~ 2005/11/28 加入
        $srh->q_limit = "LIMIT ".$limit_entries." ";
	}else{
        ##--*****--2006.11.16頁碼新增 start		##		
		$pagesize=10;
		if (isset($argv['PHP_sr_cost']) && $argv['PHP_sr_cost']) {
			$pages = $srh->get_page($argv['PHP_sr_cost'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
        ##--*****--2006.11.16頁碼新增 end	   ##
	}
    
	//2006/05/12 adding 
	$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
	$user_team = $GLOBALS['SCACHE']['ADMIN']['team_id'];

	if ($user_dept <> 'SA' && $user_dept <> 'GM') $srh->add_where_condition("remun.dept = '$user_dept'");

    if ($mode==2){
        // if ($user_team == 'MD' ) $srh->add_where_condition("salescost.dept = $user_dept");
        $srh->add_where_condition("salescost.status = 2");
    }
    if ($mode==3){
        // if ($user_team == 'MD' ) $srh->add_where_condition("salescost.dept = $user_dept");
        $srh->add_where_condition("salescost.status = 4");
    }   
    if ($mode==4){
        // if ($user_team == 'MD' ) $srh->add_where_condition("salescost.dept = $user_dept");
        $srh->add_where_condition("salescost.status = 3");
    }   
   
    $srh->add_where_condition("salescost.id = salescost_det.cost_id");
    $srh->add_group_condition("salescost_det.cost_id");
    $result= $srh->send_query2($limit_entries);   // 2005/11/24 加入 $limit_entries
    if (!is_array($result)) {
        $this->msg->merge($srh->msg);
        return false;		    
    }

    $this->msg->merge($srh->msg);
    if (!$result){   // 當查尋無資料時
        $op['record_NONE'] = 1;
    }
    $op['cost'] = $result;  // 資料錄 拋入 $op
    $op['max_no'] = $srh->max_no;
		
    if(!$limit_entries){ 
        ##--*****--2006.11.16頁碼新增 start			
        $op['maxpage'] =$srh->get_max_page();
        $op['pages'] = $pages;
        $op['now_pp'] = $srh->now_pp;
        $op['lastpage']=$pages[$pagesize-1];		
        ##--*****--2006.11.16頁碼新增 end
    }
	
    return $op;
} // end func		


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit_det($parm)	
#
#		同時更新兩個 field的值 (以編號)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function edit_cost_det($parm) {

		$sql = $this->sql;

		#####   更新資料庫內容


		$q_str = "UPDATE salescost_det SET      etd 				='".$parm['etd'].
																		"', qty					='".$parm['qty'].																		
																		"', quota				='".$parm['quota'].
																		"', fob					='".$parm['fob'].
																		"', fab_cost		='".$parm['fab_cost'].
																		"', yy					='".$parm['yy'].
																		"', acc_cost		='".$parm['acc_cost'].
																		"', acc_f_cost	='".$parm['acc_f_cost'].
																		"', smpl_cost		='".$parm['smpl_cost'].
																		"', comm				='".$parm['comm'].																		
																		"', cm					='".$parm['cm'].
								"' WHERE id= '".	$parm['id'] ."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func	
	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit_det($parm)	
#
#		同時更新兩個 field的值 (以編號)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function cost_update_fld($field,$value,$id) {

		$sql = $this->sql;

		#####   更新資料庫內容


		$q_str = "UPDATE salescost SET ".$field." ='".$value.
								"' WHERE id= '".	$id ."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func		
	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新 訂單記錄
#						傳回 $id
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_cost_log($parm) {

		$sql = $this->sql;

					# 加入資料庫(2007.03.02加入尺吋資料)
		$q_str = "INSERT INTO salescost_log (cost_id,log_user,k_time,subj,des) 
				  VALUES('".
							$parm['cost_id']."','".
							$parm['log_user']."','".
							$parm['k_time']."','".						
							$parm['subj']."','".									
							$parm['des']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot append order");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$ord_id = $sql->insert_id();  //取出 新的 id

		return $ord_id;

	} // end func	 	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新 訂單記錄
#						傳回 $id
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_cm_log($parm) {

		$sql = $this->sql;

					# 加入資料庫(2007.03.02加入尺吋資料)
		$q_str = "INSERT INTO remun_log (rem_id,log_user,k_time,des) 
				  VALUES('".
							$parm['rem_id']."','".
							$parm['log_user']."','".
							$parm['k_time']."','".																						
							$parm['des']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot append order");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$ord_id = $sql->insert_id();  //取出 新的 id

		return $ord_id;

	} // end func	 
	
	
 

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新 訂單記錄
#						傳回 $id
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function ord_cm_cost($rem_id) {

    $sql = $this->sql;

    # 加入資料庫(加入尺吋資料)
    $q_str = "SELECT `s_order`.`order_num` , `s_order`.`fty_cm` , `remun_det`.`id`
                        FROM `s_order`, `remun_det`
                        WHERE `remun_det`.`ord_num` = `s_order`.`order_num` AND `rem_id` = '".$rem_id."'
                        GROUP BY s_order.order_num";

    // echo $q_str.'<br>';
    $q_result = $sql->query($q_str);

    while ($row = $sql->fetch($q_result))
    {
        # 寫入 APV 當時的 IE
        $sql3 = "UPDATE `remun_det` SET `cost` = '".$row['fty_cm']."' WHERE id = '".$row['id']."' ;";
        $res3 = mysql_query($sql3);
        $this->add_ord_cm_cost($row['order_num'],1);				
    }

    return 1;

} // end func	 



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add_ord_cm_cost($rem_id,$mode = 0)
#						計算訂單實際成本
#						mode = 0 工繳己核可
#						mode = 1 工繳等待核可
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function add_ord_cm_cost($ord_num,$mode = 0) {

    $sql = $this->sql;
    $cm_ary = array();

    # 加入資料庫(加入尺吋資料)
    $q_str = "SELECT sum( (remun_det.rem_qty + remun_det.smpl) * ( remun_det.acc_cost + remun_det.oth_cost + remun_det.exc_cost ) ) AS cm_cost, 
                                     order_num
                        FROM s_order, remun_det
                        WHERE remun_det.ord_num = s_order.order_num AND order_num = '".$ord_num."'
                        GROUP BY s_order.order_num";

    $q_result = $sql->query($q_str);
    $cm_ary = $sql->fetch($q_result);

    # 工廠的成本			
    $q_str = "UPDATE s_order SET rel_cm_cost ='".$cm_ary['cm_cost']."' WHERE order_num= '".$cm_ary['order_num']."'";

    $q_result = $sql->query($q_str);
    
    if($mode == 1)
    {
        $rcv_check = 0;
        $wi_rec = $GLOBALS['wi']->get(0, $cm_ary['cm_cost']);
        
        $lots_ap = $GLOBALS['bom']->get_aply_quck($wi_rec['id'], 'bom_lots');	
        $acc_ap = $GLOBALS['bom']->get_aply_quck($wi_rec['id'], 'bom_acc');	
        
        if($acc_ap == 2 && $lots_ap == 2) $rcv_check = $GLOBALS['receive']->check_rcvd($cm_ary['order_num']);	
        if($rcv_check > 0)	$GLOBALS['receive']->add_ord_cost($cm_ary['order_num'],'l');
        if($rcv_check > 0)  $GLOBALS['receive']->add_ord_cost($cm_ary['order_num'],'a');
        
    }			
    
    return 1;

} // end func		



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新 訂單記錄
#						傳回 $id
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function ord_cm_cost_num($num) {

		$sql = $this->sql;
		$cm_ary = array();

					# 加入資料庫(2007.03.02加入尺吋資料)
		$q_str = "SELECT sum( (remun_det.rem_qty + remun_det.smpl) * ( remun_det.acc_cost + s_order.fty_cm ) ) AS cm_cost, order_num
							FROM s_order, remun_det
							WHERE remun_det.ord_num = s_order.order_num AND s_order.order_num = '".$num."'
							GROUP BY s_order.order_num";

		$q_result = $sql->query($q_str);
		
		if ($row = $sql->fetch($q_result))
		{
			$q_str = "UPDATE s_order SET rel_cm_cost ='".$row['cm_cost']."' WHERE order_num= '".$row['order_num']."'";
			$q_result = $sql->query($q_str);
		}
		
		return $row['cm_cost'];
	

	} // end func
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->cm2excel()
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function cm2excel($post) {
		$sql = $this->sql;
		
		$rtn = array();
		$q_str = "SELECT remun.*, sum(remun_det.rem_qty)as qty, sum(remun_det.smpl)as smpl, 
						 sum((remun_det.rem_qty+remun_det.smpl) * (remun_det.acc_cost+remun_det.cost+remun_det.oth_cost+remun_det.exc_cost + s_order.handling_fee )) as acost 
				  FROM remun, remun_det, s_order 
				  WHERE remun.status = 6 and (remun.fty = '".$post['PHP_FTY']."') AND (remun.out_month between '".$post['PHP_year']."-01' and '".$post['PHP_year']."-12') AND (remun.id = remun_det.rem_id) AND (s_order.order_num = remun_det.ord_num) 
				  GROUP BY remun_det.rem_id 
				  ORDER BY remun.id DESC";

		$q_result = $sql->query($q_str);
		
		// $i = 0;
		while($row = $sql->fetch($q_result)){
			$rtn[] = $this->get($row['id']);

			// $rtn[$i]['remun'] = $row;
			// $rtn[$i]['remun']['det'][] = $this->get($row['id']);
			// $i++;
            
		}
		
		return $rtn;
	

	} // end func

	
	
	
	
	
	
	


} // end class


?>