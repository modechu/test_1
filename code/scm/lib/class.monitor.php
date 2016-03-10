<?php 

#++++++++++++++++++++++ SUPL  class ##### 供應商  +++++++++++++++++++++++++++++++++++
#	->init($sql)							啟始 (使用 Msg_handle(); 先聯上 sql)
#	->add($parm)							加入
#	->search($mode=0)						Search   
#	->get($id=0, nbr=0)						抓出指定 記錄內資料   
#	->edit($parm)							更新 整筆資料
#	->update_field($parm)					更新 資料內 某個單一欄位
#	->del($id)								刪除 資料錄
#	->get_fields($n_field,$where_str="")	取出全部 部門表內的 $n_field 置入arry return
#
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class MONITOR {
		
	var $sql;
	var $msg ;
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->init($sql)	啟始(使用 Msg_handle() ; 先聯上 sql)
#		必需聯上 sql 才可  啟始
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function init($sql) {
		$this->msg = new MSG_HANDLE();

		if (!$sql) {
			$this->msg->add("Error ! Can't connect database.");
		    return false;
		}
		$this->sql = $sql;
		return true;
	} // end func

					
					
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新 生產資料
#						傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add($parm) {
					
		$sql = $this->sql;
		$back_mk=0;
		$parm['ord_num'] = trim($parm['ord_num']);
		$parm['ord_num'] = strtoupper($parm['ord_num']); 
		if (!$parm['ord_num'])
		{
				$this->msg->add("Error ! Please select Order #.");
				$back_mk=1;
		}
		if (!$parm['open_date'])
		{
				$this->msg->add("Error ! Please select Open Date.");
				$back_mk=1;
		}
		if ($back_mk==1)
		{
			return false;
		}
					# 加入資料庫
		$q_str = "INSERT INTO monitor ( fty,
									 line,
									 worker,
									 ord_num,
									 style,
									 ie,
									 open_date) VALUES('".
									$parm['fty']."','".
									$parm['line']."','".
									$parm['worker']."','".
									$parm['ord_num']."','".
									$parm['style']."','".
									$parm['ie']."','".
									$parm['open_date']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't add data in database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$new_id = $sql->insert_id();  //取出 新的 id

//		$this->msg->add("成功 新增供應商 : [".$parm['cust_s_name']."]。"記錄) ;

		return $new_id;

	} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新 生產資料
#						傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_det($parm) {
					
		$sql = $this->sql;
		$back_mk=0;
		
/*
		if (!$parm['qty'] && $parm['hdy'] == 0)
		{
				$this->msg->add("Error ! Please input Out-put Q'ty.");
				$back_mk=1;
		}
*/
		if (!$parm['out_date'])
		{
				$this->msg->add("Error ! Please select Out-put Date.");
				$back_mk=1;
		}
		if ($parm['out_date'] < $parm['open_date'])
		{
				$this->msg->add("Error ! Out-put Date < Open date.");
				$back_mk=1;
		}
		if ($back_mk==1)
		{
			return false;
		}					
					# 加入資料庫
		$q_str = "INSERT INTO monitor_det ( mon_id,
									 out_date,
									 hdy,
									 qty) VALUES('".
									$parm['mon_id']."','".
									$parm['out_date']."','".
									$parm['hdy']."','".
									$parm['qty']."')";
//echo $q_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't add data in database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$new_id = $sql->insert_id();  //取出 新的 id

//		$this->msg->add("成功 新增供應商 : [".$parm['cust_s_name']."]。"記錄) ;

		return $new_id;

	} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新 生產資料
#						傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_holday($parm) {
					
		$sql = $this->sql;
			
					# 加入資料庫
		$q_str = "INSERT INTO monitor_det ( mon_id,
									 out_date,
									 hdy) VALUES('".
									$parm['mon_id']."','".
									$parm['out_date']."','".
									$parm['hdy']."')";
//echo $q_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't add data in database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$new_id = $sql->insert_id();  //取出 新的 id

//		$this->msg->add("成功 新增供應商 : [".$parm['cust_s_name']."]。"記錄) ;

		return $new_id;

	} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0, $where_str="")	Search  供應商 資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search($mode=1) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv
		$srh = new SEARCH();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$q_header = "SELECT monitor.*, sum(monitor_det.qty)as sum_qty FROM monitor LEFT JOIN monitor_det ON monitor.id = monitor_det.mon_id";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("id");
		
#####2006.11.14 以數字型式顯示頁碼 star
		$srh->row_per_page = 20;
		$pagesize=10;

		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	}     	
#####2006.11.14 以數字型式顯示頁碼 end

 		$argv['PHP_LINE'] = str_replace('|','+',$argv['PHP_LINE']);


if ($mode==1){    //有條件的Search時
		$msg = '';
		if ($str = $argv['PHP_FTY'] )  { 
			$srh->add_where_condition("fty =  '$str'", "PHP_FTY",$str); 
			$msg.= " FTY : [ $str ]. ";
		}
		
		if ($str = $argv['PHP_LINE'] )  { 
			$srh->add_where_condition("line = '$str'", "PHP_LINE",$str); 
			$msg.= " Line : [ $str ]. ";
		}
		if ($str = $argv['PHP_ord'] )  { 
			$srh->add_where_condition("ord_num like '%$str%'", "PHP_ord",$str); 
			$msg.= " Order# : [ $str ]. ";
		}
		if ($str = $argv['PHP_style'] )  { 
			$srh->add_where_condition("style like '%$str%'", "PHP_style",$str); 
			$msg.= " style# : [ $str ]. ";
		}
		if ($msg)
		{
			$msg = "Search ".$msg;
			$this->msg->add($msg);
		}		

}

		$srh->add_group_condition("monitor.id");
 		$result= $srh->send_query2();
		
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);

		if (!$result){   // 當查尋無資料時
			$op['record_NONE'] = 1;
		}

		$op['monitor'] = $result;  // 資料錄 拋入 $op		
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['cgistr_post'] = $srh->get_cgi_str(1);
		$op['prev_no'] = $srh->prev_no;
		$op['next_no'] = $srh->next_no;
		$op['max_no'] = $srh->max_no;
		$op['last_no'] = $srh->last_no;
		$op['start_no'] = $srh->start_no;
		$op['per_page'] = $srh->row_per_page;
		
#####2006.11.14新頁碼需要的oup_put	start
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] = $pages;
		$op['now_pp'] = $srh->now_pp;
		$op['lastpage']=$pages[$pagesize-1];	
#####2006.11.14新頁碼需要的oup_put	end
		return $op;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $supl_s_name=0)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get($id=0) {

		$sql = $this->sql;

		$q_str = "SELECT monitor.*, s_order.ie_time1 as spt 
							FROM monitor, s_order 
							WHERE s_order.order_num = monitor.ord_num AND monitor.id='$id' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$op['mon'] = $sql->fetch($q_result)) {
			$this->msg->add("Error ! Can't find this record!");
			return false;    
		}		
		$q_str = "SELECT monitor_det.* 
							FROM monitor_det, monitor 
							WHERE monitor_det.mon_id = monitor.id AND mon_id='$id' ORDER BY monitor_det.out_date";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$s_qty=0;	$s_su=0;	$i=0;
		while ($row = $sql->fetch($q_result)) {
			$op['mon_det'][$i]=$row;
			$op['mon_det'][$i]['su']	=	(int)($op['mon_det'][$i]['qty'] * $op['mon']['ie']);
			$s_qty += 	$op['mon_det'][$i]['qty'];
			$s_su	 += 	$op['mon_det'][$i]['su'];
			$op['mon_det'][$i]['sum_qty']	=	$s_qty;
			$op['mon_det'][$i]['sum_su']	=	$s_su;
			$i++;
		}		
		return $op;
	} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		更新 供應商資料 某個單一欄位
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field($parm) {

		$sql = $this->sql;
		############### 檢查輸入項目
		
		#####   更新資料庫內容
		$q_str = "UPDATE monitor SET `".$parm['field_name']."` =' ".$parm['field_value']." '  WHERE id='".$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return $q_str;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del($id)		刪除 供應商 資料  [由ID]刪除
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del($id) {

		$sql = $this->sql;
		if (!$id) {
			$this->msg->add("Error !Please point supplier ID.");		    
			return false;
		}
		$q_str = "DELETE FROM monitor_det WHERE id='$id' ";
//echo $q_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database !");
			$this->msg->merge($sql->msg);
			return false;    
		}		

		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $supl_s_name=0)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function count_labor($id,$date,$labor) {

		$sql = $this->sql;

		$q_str = "SELECT monitor_det.*	FROM monitor_det
							WHERE monitor_det.mon_id='$id' AND monitor_det.out_date < '$date' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$qty = 0;
		while ($row = $sql->fetch($q_result)) {
			$qty = $qty + $row['qty'];
		}			
		$per_labor = $labor / $qty;
		return $per_labor;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_line($fty)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_line($fty) {

		$sql = $this->sql;
		$line = array();
		$where_str ='';
	

		if ($fty) $where_str = "AND fty = '$fty'";
		$q_str = "SELECT pdt_saw_line.* FROM pdt_saw_line WHERE del_mk = 0 $where_str  ORDER BY line";
//echo $q_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$i = 0;
		while ($row = $sql->fetch($q_result)) {		
			$line[$i] = $row;
			$line[$i]['line_style'] = $line[$i]['style'];
			$ord_rec = $this->get_least_ord($row['id']);
			$line[$i]['style'] = $ord_rec['style'];
			$line[$i]['ie'] = $ord_rec['ie'];
			$line[$i]['ord_num'] = $ord_rec['ord_num'];

			$i++;
		}		
		return $line;
	} // end func

    
    function get_line_id($fty){
        $Line_ARR = $this->get_line($fty);
        $LINE = array();
        foreach($Line_ARR as $line){
            $style = !empty($line['line_style']) ? ' ( '.$line['line_style'].' )' : '';
            $LINE[$line['id']] = $line['line'].$style;
        }
        return $LINE;
    }
    
    function get_line_name($id){
        $sql = $this->sql;
		$q_str = "SELECT `line` FROM `pdt_saw_line` WHERE `id` = '".$id."'; ";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
        $row = $sql->fetch($q_result);
        return $row['line'];
    }

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_line($fty)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_rpt_line($fty,$sc='',$sub='') {

		$sql = $this->sql;
        
        $FULL_TIME = $GLOBALS['FULL_TIME'];
        
		$line = array();
		$where_str ='';	

		if ($fty) $where_str .= " AND fty = '$fty'";
		if ($sc == 'i') $where_str .= " AND sc = 0";
		if ($sc == 's') $where_str .= " AND sc = 1";
		$where_str .= " AND sub_fty = '".$sub."'";
		$q_str = "SELECT DISTINCT pdt_saw_line.line, fty, (pdt_saw_line.success_rate * ".$FULL_TIME." ) as day_avg
							FROM  pdt_saw_line WHERE del_mk = 0 $where_str  ORDER BY line";
//echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$i = 0;
		while ($row = $sql->fetch($q_result)) {
			$line[$i] = $row;
			$i++;
		}		
		return $line;
	} // end func







#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_line($fty)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_least_ord($line_id,$sch_date='0000-00-00') {

		$sql = $this->sql;
		$line = array();
		$where_str ='';
		
		$q_str = "SELECT saw_out_put.ord_num, saw_out_put.p_id, s_order.style_num as style, s_order.ie1 as ie, s_order.qty, s_order.su
							FROM  saw_out_put, s_order
							WHERE s_order.order_num = saw_out_put.ord_num  AND saw_out_put.line_id ='$line_id' AND saw_out_put.out_date ='$sch_date'
							ORDER BY out_date DESC LIMIT 1";
// echo $q_str."<br>";						
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$row = $sql->fetch($q_result);
		return $row;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_line($fty)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_ln_saw($fty,$date,$new_date=0) {

	$sql = $this->sql;
	$line = array();
	$q_str = "SELECT 
						saw_out_put.* , saw_out_put.id as sid , saw_out_put.qty as p_qty , 
						pdt_saw_line.id, pdt_saw_line.line, pdt_saw_line.worker, pdt_saw_line.fty,
						order_partial.mks,
						s_order.partial_num
						
						FROM pdt_saw_line 
						LEFT JOIN saw_out_put ON saw_out_put.line_id = pdt_saw_line.id AND out_date = '".$date."' 
						LEFT JOIN order_partial ON order_partial.ord_num = saw_out_put.ord_num
						LEFT JOIN s_order ON s_order.order_num = order_partial.ord_num
						
						WHERE pdt_saw_line.del_mk = 0 AND fty = '".$fty."' 
						GROUP BY pdt_saw_line.line, saw_out_put.ord_num";
						
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Can'	t access database!");
		$this->msg->merge($sql->msg);
		return false;    
	}

	$i=0;
	if ($new_date == 0)$date = increceDaysInDate($date,-1);

	while ($row = $sql->fetch($q_result)) {
		$line[$i] = $row;			
		$line[$i]['ord_num'] = trim($line[$i]['ord_num']);
		// echo '<br>'.$line[$i]['s_id'].'-'.$i;
		if ($line[$i]['ord_num'])
		{
			// 有產出 ** 要改用 Final IE
			// echo '<br>'.$line[$i]['ord_num'].'-'.$line[$i]['s_id'];
			$line[$i]['style'] = $GLOBALS['order']->get_field_value('style_num','',$line[$i]['ord_num']);
			
			
			$line[$i]['ie'] = $GLOBALS['order']->get_field_value('ie1','',$line[$i]['ord_num']);
			$line[$i]['ord_qty'] = $GLOBALS['order']->get_field_value('qty','',$line[$i]['ord_num']);
			#M11021101 增加 cutting 的顯示
			$line[$i]['cut_qty'] = $GLOBALS['cutting']->ord_cut_sum($line[$i]['ord_num']);
			// $line[$i]['ord_su'] = $GLOBALS['order']->get_field_value('su','',$line[$i]['ord_num']);
			
			$line[$i]['saw_qty'] = $GLOBALS['order']->get_field_value('sum(qty)','',$line[$i]['ord_num'],'saw_out_put');
			$line[$i]['p_qty'] = $GLOBALS['order']->get_field_value('qty',$line[$i]['s_id'],$line[$i]['ord_num'],'schedule');
			$line[$i]['sch_out_qty'] = $this->get_schedule_finish_value('sum(qty)',$line[$i]['s_id'],'','saw_out_put');	
			// echo '<br>'.$line[$i]['sch_out_qty'];
			// echo $line[$i]['ord_num'].' - '.$line[$i]['s_id'].' - '.' 1 <br>';
		} else {
			// 沒產出
			$ord_rec = $this->get_least_ord($row['id'],$date);
			$line[$i]['style'] = $ord_rec['style'];
			$line[$i]['ie'] = $ord_rec['ie'];
			$line[$i]['ord_num'] = $ord_rec['ord_num'];
			$line[$i]['ord_qty'] = $ord_rec['qty'];
			#M11021101 增加 cutting 的顯示
			$line[$i]['cut_qty'] = $GLOBALS['cutting']->ord_cut_sum($ord_rec['ord_num']);
			// $line[$i]['ord_su'] = $ord_rec['su'];
			$line[$i]['p_id'] = $ord_rec['p_id'];
			$line[$i]['s_id'] = $ord_rec['s_id'];
			// echo $line[$i]['ord_num'].' - '.$line[$i]['s_id'].' - '.' 2 <br>';
		}

		$partial_num = $GLOBALS['order']->get_field_value('partial_num','',$line[$i]['ord_num'],'s_order');
		if( $partial_num > 1 )
			$line[$i]['mks'] = '('.$GLOBALS['order']->get_field_value('mks',$line[$i]['p_id'],$line[$i]['ord_num'],'order_partial').')';

		// $line[$i]['saw_su'] = $GLOBALS['order']->get_field_value('saw_su','',$line[$i]['ord_num'],'pdt_finish');
		// $line[$i]['sch_out_qty'] = $GLOBALS['order']->get_field_value('sum(qty) as qty','',$line[$i]['ord_num'],'saw_out_put');
		// $line[$i]['s_id'] = $GLOBALS['order']->get_field_value('s_id','',$line[$i]['ord_num'],'saw_out_put');

		$tmp_status = $GLOBALS['order']->get_field_value('status','',$line[$i]['ord_num']);
		if($tmp_status < 7)
		{
			$line[$i]['style'] = '';
			$line[$i]['ie'] = '';
			$line[$i]['ord_num'] = '';
			$line[$i]['cut_qty'] = '';
			$line[$i]['ord_qty'] = '';
			// $line[$i]['ord_su'] = '';
			$line[$i]['saw_qty'] = '';
			$line[$i]['saw_su'] = '';
		}

		// echo '<br>'.$line[$i]['ord_num'].'-'.$line[$i]['s_id'];
		// echo '<br>'.$date.' ~ '.$line[$i]['s_id'];
		// echo '<br>'.$q_str;
		$line[$i]['i'] = $i;
		$i++;
	}
	
	// print_r($line);
	return $line;
} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_line($fty)	抓出指定記錄內資料 RETURN $row[] ie 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# mode reset final ie 
function get_daily_out($fty,$date) {

	$sql = $this->sql;
	$line = array();
	$q_str = "SELECT 
    schedule.id as s_id , schedule.qty as s_sch_qty , schedule.pdt_qty as s_out_qty , schedule.rel_ets , schedule.rel_etf ,
    
    order_partial.id as p_id, order_partial.p_qty as p_ord_qty , order_partial.mks, order_partial.cut_qty as p_cut_qty,order_partial.p_su as su ,
    order_partial.p_qty_done as p_out_qty,order_partial.ext_qty as p_sch_qty,

    saw_out_put.* , saw_out_put.id as sid , 
    pdt_saw_line.id , pdt_saw_line.line , pdt_saw_line.worker , pdt_saw_line.fty ,
    

    s_order.partial_num , s_order.style , s_order.ie1 as ie1 , s_order.ie2 as ie2 , s_order.style_num as style , s_order.qty as ord_qty ,
    pdtion.ext_period

    FROM pdt_saw_line 
    LEFT JOIN saw_out_put ON saw_out_put.line_id = pdt_saw_line.id AND saw_out_put.out_date = '".$date."' 
    LEFT JOIN schedule ON schedule.id = saw_out_put.s_id
    LEFT JOIN order_partial ON order_partial.id = schedule.p_id
    LEFT JOIN s_order ON s_order.order_num = order_partial.ord_num
    LEFT JOIN pdtion ON s_order.order_num = pdtion.order_num

    WHERE pdt_saw_line.del_mk = 0 AND pdt_saw_line.worker > 0 AND pdt_saw_line.fty = '".$fty."'
    ORDER BY pdt_saw_line.line, saw_out_put.id";
						
	// echo "<br>".$q_str."<br>"; 
	
	if (!$mq_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Can'	t access database!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	$mon_det = array();
	$i = 0;
	
	# 抓前一天日期
	$date2 = increceDaysInDate($date,-1);
	while ($row = $sql->fetch($mq_result)) {
		if ( !empty($row['ord_num']) ) {

			if( $row['partial_num'] == 1 )
				$row['mks'] = '';

			$row['sch_out_qty'] = $this->get_schedule_finish_value('sum(qty)',$row['s_id'],'','saw_out_put');	

			$row['saw_qty'] = $GLOBALS['order']->get_field_value('sum(qty)','',$row['ord_num'],'saw_out_put');

			$row['i'] = $i;
            $row['ie'] = ( $row['ie2'] > 0 )? $row['ie2'] : $row['ie1'] ;
			$i++;
			$mon_det[] = $row;
		} else {
			
			$yastoday = $this->get_order_out($fty,$date2,$row['line']);
			// print_r($yastoday);
			foreach( $yastoday as $key ){

				if( $key['partial_num'] == 1 ){
					$key['mks'] = '';
				}
				$key['sid'] = '';
				
				$key['ot_wk'] = '';
				$key['ot_hr'] = '';
				
				$key['work_qty'] = '';
				$key['over_qty'] = '';
				$key['qty'] = '';
				
				$key['holiday'] = 0;

				$key['sch_out_qty'] = $this->get_schedule_finish_value('sum(qty)',$key['s_id'],'','saw_out_put');	
				$key['saw_qty'] = $GLOBALS['order']->get_field_value('sum(qty)','',$key['ord_num'],'saw_out_put');
				$key['i'] = $i;
				$i++;
                $key['ie'] = ( $key['ie2'] > 0 )? $key['ie2'] : $key['ie1'] ;
				$mon_det[] = $key;
			}
		}
	}

	return $mon_det;
} // end func



# mode reset final ie 
function get_order_out($fty,$date,$line) {

	$sql = $this->sql;
	$q_str = "SELECT 
    schedule.id as s_id , schedule.qty as s_sch_qty , schedule.pdt_qty as s_out_qty , schedule.rel_ets , schedule.rel_etf ,
    
    order_partial.id as p_id, order_partial.p_qty as p_ord_qty , order_partial.mks, order_partial.cut_qty as p_cut_qty,order_partial.p_su as su ,
    order_partial.p_qty_done as p_out_qty,order_partial.ext_qty as p_sch_qty,

    saw_out_put.* , saw_out_put.id as sid , 
    pdt_saw_line.id , pdt_saw_line.line , pdt_saw_line.worker , pdt_saw_line.fty ,
    

    s_order.partial_num , s_order.style , s_order.ie1 as ie1 , s_order.ie2 as ie2 , s_order.style_num as style , s_order.qty as ord_qty ,
    pdtion.ext_period
    
    FROM pdt_saw_line 
    LEFT JOIN saw_out_put ON saw_out_put.line_id = pdt_saw_line.id AND saw_out_put.out_date = '".$date."' 
    LEFT JOIN schedule ON schedule.id = saw_out_put.s_id
    LEFT JOIN order_partial ON order_partial.id = schedule.p_id
    LEFT JOIN s_order ON s_order.order_num = order_partial.ord_num
    LEFT JOIN pdtion ON s_order.order_num = pdtion.order_num

    WHERE pdt_saw_line.del_mk = 0 AND pdt_saw_line.worker > 0 AND pdt_saw_line.fty = '".$fty."' AND pdt_saw_line.line = '".$line."'
    ORDER BY pdt_saw_line.line, saw_out_put.id";
						// FROM pdt_saw_line 
						// LEFT JOIN saw_out_put ON saw_out_put.line_id = pdt_saw_line.id AND out_date = '".$date."' 
						// LEFT JOIN schedule ON schedule.id = saw_out_put.s_id
						// LEFT JOIN order_partial ON order_partial.id = schedule.p_id
						// LEFT JOIN s_order ON s_order.order_num = order_partial.ord_num
						
						// WHERE pdt_saw_line.del_mk = 0 AND pdt_saw_line.fty = '".$fty."' AND pdt_saw_line.line = '".$line."'
						// GROUP BY pdt_saw_line.line, saw_out_put.ord_num";
						
	// echo "<br>".$q_str."<br>";
	
	if (!$mq_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Can'	t access database!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	while ( $row = $sql->fetch($q_result)){
		$rows[] = $row;
	}
	
	return $rows;
}



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_line($fty)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function check_date($fty,$date) {

		$sql = $this->sql;
		$line = array();
		$q_str = "SELECT saw_out_put.out_date FROM saw_out_put, pdt_saw_line 
							WHERE pdt_saw_line.id =saw_out_put.line_id AND pdt_saw_line.fty = '$fty' 
								AND saw_out_put.out_date = '$date' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}

		while ($row = $sql->fetch($q_result)) {
			return false;
		}		
		return true;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新 生產資料
#						傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_line($parm) {
					
		$sql = $this->sql;
		$back_mk=0;
	#檢查生產線是否唯一			
		$q_str = "SELECT pdt_saw_line.line FROM pdt_saw_line 
							WHERE pdt_saw_line.fty  = '".$parm['fty']."' 
								AND pdt_saw_line.line = '".$parm['line']."' 
								AND pdt_saw_line.del_mk = 0";
//echo $q_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}

		while ($row = $sql->fetch($q_result)) {
			return false;
		}		
$id_mk = '';
		$q_str = "SELECT pdt_saw_line.id FROM pdt_saw_line 
							WHERE pdt_saw_line.fty  = '".$parm['fty']."' 
								AND pdt_saw_line.line = '".$parm['line']."' 
								AND pdt_saw_line.del_mk = 1";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}

		while ($row = $sql->fetch($q_result)) {
			$id_mk = $row['id'];
		}	
	if ($id_mk)
	{
		$q_str = "UPDATE pdt_saw_line SET del_mk = 0, worker = '".$parm['worker']."' WHERE id='".$id_mk."'";
	}else{
		# 加入資料庫
		$q_str = "INSERT INTO pdt_saw_line ( fty,
									 line,
									 sc,
									 sub_fty,
									 worker,
									 style
									) VALUES('".
									$parm['fty']."','".
									$parm['line']."','".
									$parm['sc']."','".
									$parm['sub_fty']."','".
									$parm['worker']."','".
									$parm['style']."')";
		}
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't add data in database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
//		$new_id = $sql->insert_id();  //取出 新的 id

//		$this->msg->add("成功 新增供應商 : [".$parm['cust_s_name']."]。"記錄) ;

		return true;

	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit_saw($parm)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function edit_line($parm) {

		$sql = $this->sql;
		############### 檢查輸入項目

	#檢查生產線是否唯一			
		$q_str = "SELECT pdt_saw_line.line FROM pdt_saw_line 
							WHERE pdt_saw_line.fty  = '".$parm['fty']."' 
								AND pdt_saw_line.line = '".$parm['line']."' 
								AND pdt_saw_line.id <> '".$parm['id']."'";
//echo $q_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}

		while ($row = $sql->fetch($q_result)) {
			return false;
		}	
		
		#####   更新資料庫內容
		$q_str = "UPDATE pdt_saw_line SET ".
						  "line ='".$parm['line']."',".
						  "sc ='".$parm['sc']."',".
						  "style ='".$parm['style']."',".
						  "worker ='".$parm['worker']."'".
							"  WHERE id='".$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return $q_str;
	} // end func	

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新 生產資料
#						傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function add_saw($parm) {
                
    $sql = $this->sql;
    $back_mk=0;
    
    # 加入資料庫
    $parm['ord_num'] = trim($parm['ord_num']);
    $parm['ord_num'] = strtoupper($parm['ord_num']); 

    $q_str = "INSERT INTO saw_out_put ( line_id,
                                 ord_num,
                                 p_id,
                                 s_id,
                                 out_date,
                                 work_qty,
                                 over_qty,
                                 qty,
                                 su,
                                 attendence,
                                 workers,
                                 ot_wk,
                                 ot_hr,
                                 saw_line,
                                 saw_fty,
                                 holiday
                                ) VALUES('".
                                $parm['line_id']."','".
                                $parm['ord_num']."','".
                                $parm['p_id']."','".
                                $parm['s_id']."','".
                                $parm['out_date']."','".
                                $parm['worktime']."','".
                                $parm['overtime']."','".
                                $parm['qty']."','".
                                $parm['su']."','".
                                $parm['attendence']."','".
                                $parm['workers']."','".
                                $parm['ot_wk']."','".
                                $parm['ot_hr']."','".
                                $parm['saw_line']."','".
                                $parm['saw_fty']."','".
                                $parm['holiday']."')";
// echo $q_str;
    if (!$q_result = $sql->query($q_str)) {
        $this->msg->add("Error ! Can't add data in database.");
        $this->msg->merge($sql->msg);
        return false;
    }
    
    $new_id = $sql->insert_id();  //取出 新的 id
    
    $q_str = "SELECT min(`out_date`) as `ets` FROM `saw_out_put` WHERE `s_id` = '".$parm['s_id']."' GROUP BY `ord_num` ;";
    $q_results = $sql->query($q_str);
    if( $row = $sql->fetch($q_results) ) {
        $q_str = "UPDATE `schedule` SET `ets` = '".$row['ets']."' WHERE `id` = '".$parm['s_id']."' ;";
        $sql->query($q_str);
    }

    // $this->update_partial_qty($parm['p_id']);
/*
if($parm['qty'] > 0)
{
    $q_str = "INSERT INTO daily_out ( k_date,
                                 ord_num,
                                 factory,
                                 qty,
                                 su
                                ) VALUES('".
                                $parm['out_date']."','".
                                $parm['ord_num']."','".
                                $parm['saw_fty']."','".
                                $parm['qty']."','".
                                $parm['su']."')";
//echo $q_str;
    if (!$q_result = $sql->query($q_str)) {
        $this->msg->add("Error ! Can't add data in database.");
        $this->msg->merge($sql->msg);
        return false;    
    }
}

*/


//		$this->msg->add("成功 新增供應商 : [".$parm['cust_s_name']."]。"記錄) ;
    // $finish_qty = $this->edit_finish($parm['ord_num']);
    $rtn['id'] = $new_id;
    // $rtn['f_qty'] = $finish_qty;
    return $rtn;

} // end func

	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新 生產資料
#						傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function del_saw($parm) {
                
    $sql = $this->sql;

    $q_str = "DELETE FROM saw_out_put WHERE id='".$parm['sid']."'";		
    // echo $q_str;
    if (!$q_result = $sql->query($q_str)) {
        $this->msg->add("Error ! Can't delete data in monitor database.");
        $this->msg->merge($sql->msg);
        return false;    
    }

    $q_str = "SELECT min(`out_date`) as `ets` FROM `saw_out_put` WHERE `s_id` = '".$parm['sid']."' GROUP BY `ord_num` ;";
    $q_results = $sql->query($q_str);
    if( $row = $sql->fetch($q_results) ) {
        $q_str = "UPDATE `schedule` SET `ets` = '".$row['ets']."' WHERE `id` = '".$parm['sid']."' ;";
        $sql->query($q_str);
    }
    
    return true;

} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit_saw($parm)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function edit_saw($parm) {

		$sql = $this->sql;
		############### 檢查輸入項目
		
		#####   更新資料庫內容
		$parm['ord_num'] = trim($parm['ord_num']);
		$parm['ord_num'] = strtoupper($parm['ord_num']); 

		$q_str = "UPDATE saw_out_put SET ".
						  "ord_num ='".$parm['ord_num']."',".
						  "qty ='".$parm['qty']."',".
						  "su ='".$parm['su']."',".
						  "holiday ='".$parm['holiday']."'".
							"  WHERE id='".$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$finish_qty = $this->edit_finish($parm['ord_num']);
		return $finish_qty;
	} // end func	
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit_saw($parm)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function edit_finish($ord_num) {

		$sql = $this->sql;
		############### 檢查輸入項目
		$q_str = "SELECT id FROM pdt_finish WHERE order_num = '$ord_num'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
#####   更新資料庫內容
		if($row = $sql->fetch($q_result)) {
			$q_str = "SELECT SUM(saw_out_put.qty) as saw_qty, SUM(saw_out_put.su) as saw_su 
								FROM saw_out_put WHERE ord_num ='$ord_num' GROUP BY ord_num";
			$q_result = $sql->query($q_str);
			$row_saw = $sql->fetch($q_result);
			
			$q_str = "UPDATE pdt_finish SET ".
						  "sawing ='".$row_saw['saw_qty']."',".
						  "saw_su ='".$row_saw['saw_su']."'".
							"  WHERE id='".$row['id']."'";
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! Can't access database!");
				$this->msg->merge($sql->msg);
				return false;    
			}
			$qty = $row_saw['saw_qty'];
			$su = $row_saw['saw_su'];
		}else{
			$q_str = "SELECT s_order.qty, s_order.factory, s_order.su, saw_out_put.ord_num, SUM(saw_out_put.qty) as saw_qty, SUM(saw_out_put.su) as saw_su
								FROM s_order, saw_out_put
								WHERE saw_out_put.ord_num = s_order.order_num AND	ord_num ='$ord_num'					
								GROUP BY saw_out_put.ord_num ";
			$q_result = $sql->query($q_str);
			$rec = $sql->fetch($q_result);
			$q_str = "INSERT INTO pdt_finish( order_num, fty, qty, su, sawing, saw_su
									) VALUES('".
									$rec['ord_num']."','".
									$rec['factory']."','".
									$rec['qty']."','".
									$rec['su']."','".
									$rec['saw_qty']."','".
									$rec['saw_su']."')";
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! Can't access database!");
				$this->msg->merge($sql->msg);
				return false;    
			}					
			$qty = $rec['saw_qty'];
			$su = $rec['saw_su'];
		}				
		
		
	return $qty;
	} // end func		
	
/*
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_line($fty)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function daily_saw_view($fty,$date) {

		$sql = $this->sql;
		$line = array();
		$q_str = "SELECT saw_out_put.*, pdt_saw_line.id, pdt_saw_line.line, pdt_saw_line.worker, 
										 s_order.style_num as style, s_order.ie1 as ie
							FROM pdt_saw_line, saw_out_put, s_order  
							WHERE saw_out_put.line_id = pdt_saw_line.id AND s_order.order_num = saw_out_put.ord_num
							AND fty = '$fty'  AND out_date = '$date' ORDER BY line";
//echo $q_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$i=0;
		while ($row = $sql->fetch($q_result)) {
			$line[$i] = $row;
			$i++;
		}		
		return $line;
	} // end func	
	
	*/
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_line($fty)	抓出指定記錄內資料 RETURN $row[] d_out
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function daily_saw_view($fty,$date) {

		$sql = $this->sql;
        
        $FULL_TIME = $GLOBALS['FULL_TIME'];
        
		$line = array();
		$s_date = substr($date,0,7)."-01";
		$e_date = substr($date,0,7)."-31";
		
//		$q_str = 
		
		
		
		$q_str = "SELECT saw_out_put.*, pdt_saw_line.id, pdt_saw_line.line, pdt_saw_line.worker, 
										 saw_out_put.id as sid, pdt_saw_line.sc,pdt_saw_line.success_rate 
							FROM saw_out_put
							RIGHT JOIN pdt_saw_line ON saw_out_put.line_id = pdt_saw_line.id AND out_date = '$date'
							WHERE pdt_saw_line.fty = '$fty' AND pdt_saw_line.id = saw_out_put.line_id  ORDER BY pdt_saw_line.sc, pdt_saw_line.line";
// echo $q_str.'<br>';
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$i=0;
		while ($row = $sql->fetch($q_result)) {
			$row['success_rate'] = $row['success_rate'] * $FULL_TIME; #9.6
			if(!$row['workers']) $row['workers'] = $row['worker'];
			if(!$row['attendence']) $row['attendence'] = $row['workers'];
			$line[$i] = $row;			
			$line[$i]['ord_num'] = trim($line[$i]['ord_num']);
			$line[$i]['ot_day'] = ($line[$i]['ot_wk'] * $line[$i]['ot_hr']) / $FULL_TIME; #8
            // echo ' ot_day = ' .$line[$i]['ot_day'].' = ( ot_wk = '.$line[$i]['ot_wk'].' * ot_hr = '.$line[$i]['ot_hr'].' ) /  FULL_TIME = '.$FULL_TIME.' <br>';
			$line[$i]['ot_day'] = NUMBER_FORMAT($line[$i]['ot_day'],1);
			$line[$i]['sspt'] = $line[$i]['spt'] = $line[$i]['out_rate'] =$line[$i]['style'] = $line[$i]['ie'] =$line[$i]['ord_style'] ='';
			if ($line[$i]['ord_num'])
			{
				$line[$i]['style'] = $GLOBALS['order']->get_field_value('style_num','',$line[$i]['ord_num']);
				$line[$i]['ie'] = $GLOBALS['order']->get_field_value('ie1','',$line[$i]['ord_num']);
				$line[$i]['ie2'] = $GLOBALS['order']->get_field_value('ie2','',$line[$i]['ord_num']);
				$line[$i]['ord_style'] = $GLOBALS['order']->get_field_value('style','',$line[$i]['ord_num']);
			
				$line[$i]['sspt'] = $line[$i]['ie'] * $FULL_TIME; #9.6
				$line[$i]['sspt'] = NUMBER_FORMAT($line[$i]['sspt'],2);
				$line[$i]['spt'] = ($line[$i]['ot_day'] + $line[$i]['workers']) * $line[$i]['sspt'];						
			}
			
            // 應產出(PC) = (( 加班人數 * 加班小時 / 8 ) + 正常班人數 ) * ( ie * 9.6 )
            
			$line[$i]['out_rate']  = $line[$i]['d_out'] = 0;
			if(($line[$i]['ot_day'] + $line[$i]['workers']) > 0) $line[$i]['d_out'] = $line[$i]['su'] / ($line[$i]['ot_day'] + $line[$i]['workers']);
            if($line[$i]['d_out'])
            // echo ' d_out = ' .$line[$i]['d_out'].' su = '.$line[$i]['su'].' / ot_day = '.$line[$i]['ot_day'].' + workers = '.$line[$i]['workers'].' <br>';
			//if($line[$i]['spt']  > 0)$line[$i]['out_rate'] = $line[$i]['qty'] / $line[$i]['spt'] * 100;

			$line[$i]['out_rate'] = $line[$i]['d_out'] / $FULL_TIME * 100; #9.6
            // echo 'out_rate = '.$line[$i]['out_rate']. ' = ' . $line[$i]['d_out'].' / ' .$FULL_TIME.'  * 100 <p>';
		
			$line[$i]['i'] =$i;
			$i++;
		}		
		return $line;
	} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->saw_search($mode=0, $where_str="")	Search  供應商 資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function saw_search($mode=1) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv
		$srh = new SEARCH();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT pdt_saw_line.*, saw_out_put.*, 
											  sum(saw_out_put.qty)as sum_qty, 
											  sum(saw_out_put.su)as sum_su, 
											  min(holiday) as holiday
									FROM pdt_saw_line, saw_out_put, s_order";

/*
		$q_header = "SELECT pdt_saw_line.*, saw_out_put.*, 
											  sum(saw_out_put.qty)as sum_qty, 
											  sum(saw_out_put.su)as sum_su, 
											  min(holiday) as holiday
									FROM pdt_saw_line, saw_out_put LEFT JOIN s_order ON s_order.order_num = saw_out_put.ord_num";
*/


		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		
#####2006.11.14 以數字型式顯示頁碼 star
		$srh->row_per_page = 20;
		$pagesize=10;

		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	}     	
#####2006.11.14 以數字型式顯示頁碼 end

		$argv['SCH_line'] = str_replace("|","+",$argv['SCH_line']);

if ($mode==1){    //有條件的Search時
		if ($str = $argv['SCH_fty'] )  { 
			$srh->add_where_condition("pdt_saw_line.fty =  '$str'", "SCH_fty",$str,"FTY: [ $str ]. "); }
		
		if ($str = $argv['SCH_line'] )  { 
			$srh->add_where_condition("pdt_saw_line.line = '$str'", "SCH_line",$str,"Line: [ $str ]. "); }
		if ($str = $argv['SCH_ord'] )  { 
			$srh->add_where_condition("ord_num like '%$str%'", "SCH_ord",$str,"Order # like [ $str ]. "); }

		if ($str = $argv['SCH_str'] )  { 			
			$srh->add_where_condition("out_date >= '%$str%'", "SCH_str",$str,"Out-put date :  $str ~"); }
		if ($str = $argv['SCH_end'] )  { 
				$end_msg='';
				if(!$argv['SCH_str']){$end_msg ="Out-put date : ~"; }
			$srh->add_where_condition("out_date <= '%$str%'", "SCH_end",$str,$end_msg." $str "); }
		if ($str = $argv['SCH_cust'] )  { 
			$srh->add_where_condition("s_order.cust = '$str'", "SCH_cust",$str,"Cust. :  [ $str ]. "); }
		if ($str = $argv['SCH_style'] )  { 
			$srh->add_where_condition("s_order.style_num like '%$str%'", "SCH_style",$str,"Style like  :  [ $str ]. "); }

}
		$srh->add_where_condition("pdt_saw_line.id = saw_out_put.line_id");
		$srh->add_where_condition("s_order.order_num = saw_out_put.ord_num");

		$srh->add_group_condition("out_date, pdt_saw_line.fty");
		$srh->add_sort_condition("out_date DESC");

 		$result= $srh->send_query2();
		
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);

		if (!$result){   // 當查尋無資料時
			$op['record_NONE'] = 1;
		}

		$op['monitor'] = $result;  // 資料錄 拋入 $op		
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['cgistr_post'] = $srh->get_cgi_str(1);
		$op['max_no'] = $srh->max_no;
		
#####2006.11.14新頁碼需要的oup_put	start
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] = $pages;
		$op['now_pp'] = $srh->now_pp;
		$op['lastpage']=$pages[$pagesize-1];	
#####2006.11.14新頁碼需要的oup_put	end
		return $op;
	} // end func
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->saw_rpt_search($mode=0, $where_str="")	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function saw_rpt_search($mode=1,$sc='',$sub='') {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv
		$srh = new SEARCH();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}
/*
		$q_header = "SELECT pdt_saw_line.*, saw_out_put.*, pdt_saw_line.id as pdt_id	
								 FROM pdt_saw_line, saw_out_put, s_order";

		$q_header = "SELECT pdt_saw_line.*, saw_out_put.*, pdt_saw_line.id as pdt_id	
								 FROM pdt_saw_line, saw_out_put LEFT JOIN s_order ON s_order.order_num = saw_out_put.ord_num";
*/
		$q_header = "SELECT pdt_saw_line.id as pdt_id, pdt_saw_line.line, sum(saw_out_put.qty) as qty, 
												sum(saw_out_put.su) as su, out_date, pdt_saw_line.fty, holiday
								 FROM pdt_saw_line, saw_out_put, s_order";

//echo $q_header;
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->row_per_page = 999999;
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("out_date, line");

		$argv['SCH_line'] = str_replace("|","+",$argv['SCH_line']);
		
if ($mode==1){    //有條件的Search時
		if ($str = $argv['SCH_fty'] )  { 
			$srh->add_where_condition("pdt_saw_line.fty =  '$str'", "PHP_FTY",$str,"Search Factory: [ $str ]. "); }
		
		if ($str = $argv['SCH_line'] )  { 
			$srh->add_where_condition("pdt_saw_line.line = '$str'", "PHP_LINE",$str,"Search Production Line: [ $str ]. "); }
		if ($str = $argv['SCH_ord'] )  { 
			$srh->add_where_condition("ord_num like '%$str%'", "PHP_ord",$str,"Search Production Order # like [ $str ]. "); }
		if ($str = $argv['SCH_str'] )  { 
			$srh->add_where_condition("out_date >= '%$str%'", "PHP_style",$str,"Search Out-put date >= [ $str ]. "); }
		if ($str = $argv['SCH_end'] )  { 
			$srh->add_where_condition("out_date <= '%$str%'", "PHP_style",$str,"Search Out-put date <=  [ $str ]. "); }
		if ($str = $argv['SCH_cust'] )  { 
			$srh->add_where_condition("s_order.cust = '$str'", "PHP_style",$str,"Cust. :  [ $str ]. "); }
		if ($str = $argv['SCH_style'] )  { 
			$srh->add_where_condition("s_order.style_num like '%$str%'", "PHP_style",$str,"Style like  :  [ $str ]. "); }

}

		$srh->add_where_condition("pdt_saw_line.id = saw_out_put.line_id");
		$srh->add_where_condition("s_order.order_num = saw_out_put.ord_num");
		$srh->add_where_condition("pdt_saw_line.sub_fty = '".$sub."'");
		if($sc == 'i')$srh->add_where_condition("pdt_saw_line.sc = 0");
		if($sc == 's')$srh->add_where_condition("pdt_saw_line.sc = 1");

		$srh->add_group_condition("out_date, line");

//		$srh->add_group_condition("out_date, pdt_saw_line.fty");
 		$result= $srh->send_query2();
		
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);

		if (!$result){   // 當查尋無資料時
			$op['record_NONE'] = 1;
		}

		$dm = $result;  // 資料錄 拋入 $op		

		return $dm;
	} // end func	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->saw_rpt_search($mode=0, $where_str="")	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function saw_rpt_date($mode=1,$sc='') {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv
		$srh = new SEARCH();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}
/*
		$q_header = "SELECT pdt_saw_line.*, saw_out_put.*, pdt_saw_line.id as pdt_id	
								 FROM pdt_saw_line, saw_out_put, s_order";

		$q_header = "SELECT pdt_saw_line.*, saw_out_put.*, pdt_saw_line.id as pdt_id	
								 FROM pdt_saw_line, saw_out_put LEFT JOIN s_order ON s_order.order_num = saw_out_put.ord_num";
*/
		$q_header = "SELECT out_date, max(holiday) as holiday
								 FROM pdt_saw_line, saw_out_put, s_order";

//echo $q_header;
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->row_per_page = 999999;
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("out_date, line");

		$argv['SCH_line'] = str_replace("|","+",$argv['SCH_line']);
		
if ($mode==1){    //有條件的Search時
		if ($str = $argv['SCH_fty'] )  { 
			$srh->add_where_condition("pdt_saw_line.fty =  '$str'", "PHP_FTY",$str,"Search Factory: [ $str ]. "); }
		
		if ($str = $argv['SCH_line'] )  { 
			$srh->add_where_condition("pdt_saw_line.line = '$str'", "PHP_LINE",$str,"Search Production Line: [ $str ]. "); }
		if ($str = $argv['SCH_ord'] )  { 
			$srh->add_where_condition("ord_num like '%$str%'", "PHP_ord",$str,"Search Production Order # like [ $str ]. "); }
		if ($str = $argv['SCH_str'] )  { 
			$srh->add_where_condition("out_date >= '%$str%'", "PHP_style",$str,"Search Out-put date >= [ $str ]. "); }
		if ($str = $argv['SCH_end'] )  { 
			$srh->add_where_condition("out_date <= '%$str%'", "PHP_style",$str,"Search Out-put date <=  [ $str ]. "); }
		if ($str = $argv['SCH_cust'] )  { 
			$srh->add_where_condition("s_order.cust = '$str'", "PHP_style",$str,"Cust. :  [ $str ]. "); }
		if ($str = $argv['SCH_style'] )  { 
			$srh->add_where_condition("s_order.style_num like '%$str%'", "PHP_style",$str,"Style like  :  [ $str ]. "); }

}
		$srh->add_where_condition("pdt_saw_line.id = saw_out_put.line_id");
		$srh->add_where_condition("s_order.order_num = saw_out_put.ord_num");
		$srh->add_group_condition("out_date");

//		$srh->add_group_condition("out_date, pdt_saw_line.fty");
 		$result= $srh->send_query2();
		
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);

		if (!$result){   // 當查尋無資料時
			$op['record_NONE'] = 1;
		}

		$dm = $result;  // 資料錄 拋入 $op		

		return $dm;
	} // end func		
	
	
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fields($n_field,$where_str="")	取出全部 部門表內的 $n_field 置入arry return
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_fields_saw($n_field,$where_str="") {

		$sql = $this->sql;
		$fields = array();
		$q_str = "SELECT ".$n_field." FROM pdt_saw_line, saw_out_put WHERE saw_out_put.line_id =pdt_saw_line.id ".$where_str;

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! Can't access can't find this record!");
			$this->msg->merge($sql->msg);
			return false;    
		}

			$match_limit = 500;
			$match = 0;
			while ($row = $sql->fetch($q_result)) {
				$fields[] = $row[0];
				/* $match++;
				if ($match==500) {
					break;
				} */
			}
			/* if ($match != 500) {   // 保留 尚未作用
				$sql->free_result($q_result);
				$result =0;
				$this->q_result = $q_result;
			} */
		
		return $fields;
	} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fields($n_field,$where_str="")	取出全部 部門表內的 $n_field 置入arry return
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_field_finish($n_field,$where_str="") {
		$sql = $this->sql;
		$fields = array();
		$q_str = "SELECT ".$n_field." FROM pdt_finish ".$where_str;
//echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! Can't access can't find this record!");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$row = $sql->fetch($q_result);
		
		return $row[0];
	} // end func	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del($id)		刪除 供應商 資料  [由ID]刪除
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del_line($id) {

		$sql = $this->sql;
		if (!$id) {
			$this->msg->add("Error !Please point supplier ID.");		    
			return false;
		}
//		$q_str = "DELETE FROM pdt_saw_line WHERE id='$id' ";

		$q_str = "UPDATE pdt_saw_line SET del_mk ='1' WHERE id='".$id."'";


//echo $q_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database !");
			$this->msg->merge($sql->msg);
			return false;    
		}		

		return true;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_line($fty)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_ord($ord_num,$ll='') {

		$sql = $this->sql;
		$line = array();
		if ($ll) $ll = " AND saw_line = '".$ll."'";
		$q_str = "SELECT saw_out_put.ord_num, saw_out_put.out_date, MIN(saw_out_put.holiday) as holiday, SUM(saw_out_put.su) AS su, s_order.style_num, SUM(saw_out_put.qty) AS out_qty
							FROM saw_out_put, s_order
							WHERE saw_out_put.ord_num = s_order.order_num AND saw_out_put.ord_num = '$ord_num'".$ll."
							GROUP BY saw_out_put.out_date ORDER BY saw_out_put.out_date, saw_out_put.saw_line ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$i=0;
		while ($row = $sql->fetch($q_result)) {
			$line[$i] = $row;			
			$i++;
		}		
		return $line;
	} // end func
	

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_line($fty)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_ord_line($ord_num) {

		$sql = $this->sql;
		$line = array();
		$q_str = "SELECT DISTINCT pdt_saw_line.line 
							FROM saw_out_put, pdt_saw_line 
							WHERE  saw_out_put.line_id =pdt_saw_line.id AND saw_out_put.ord_num = '$ord_num'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$i=0;
		while ($row = $sql->fetch($q_result)) {
			$line[$i] = $row;			
			$i++;
		}		
		return $line;
	} // end func
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_line($fty)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_line_det($ll,$fty,$s_date,$e_date) {

		$sql = $this->sql;
		$line = array();
		$where_str = '';
		if ($ll) $where_str .= " AND saw_line = '".$ll."'";
		if ($fty) $where_str .= " AND saw_fty ='".$fty."'";
		$q_str = "SELECT saw_out_put.workers, saw_out_put.ord_num, saw_out_put.out_date, MIN(saw_out_put.holiday) as holiday, SUM(saw_out_put.su) AS su, s_order.style_num FROM saw_out_put, s_order
							WHERE saw_out_put.ord_num = s_order.order_num 
							AND saw_out_put.out_date >='$s_date' AND saw_out_put.out_date <='$e_date' ".$where_str."
							GROUP BY saw_out_put.out_date ORDER BY saw_out_put.out_date, saw_out_put.saw_line ";
//echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$i=0;
		while ($row = $sql->fetch($q_result)) {
			$line[$i] = $row;			
			$i++;
		}		
		return $line;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_line($fty)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function saw_finish_creat() {

		$sql = $this->sql;
		$line = array();
		$q_str = "SELECT s_order.qty, s_order.factory, s_order.su, saw_out_put.ord_num, SUM(saw_out_put.qty) as saw_qty, SUM(saw_out_put.su) as saw_su
							FROM s_order, saw_out_put
							WHERE saw_out_put.ord_num = s_order.order_num 							
							GROUP BY saw_out_put.ord_num ";
//echo $q_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$i=0;
		while ($row = $sql->fetch($q_result)) {
			$rec[$i] = $row;			
			$i++;
		}		
		for ($i=0; $i<sizeof($rec); $i++)
		{
			$q_str = "INSERT INTO pdt_finish( order_num, fty, qty, su, sawing, saw_su
									) VALUES('".
									$rec[$i]['ord_num']."','".
									$rec[$i]['factory']."','".
									$rec[$i]['qty']."','".
									$rec[$i]['su']."','".
									$rec[$i]['saw_qty']."','".
									$rec[$i]['saw_su']."')";
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! Can't access database!");
				$this->msg->merge($sql->msg);
				return false;    
			}			
		}
		return true;
	} // end func	



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fields($n_field,$where_str="")	取出全部 部門表內的 $n_field 置入arry return
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_daily_saw_ord($ord_num) {
		$sql = $this->sql;
		$fields = array();
		$q_str = "SELECT * FROM saw_out_put WHERE saw_out_put.ord_num = '".$ord_num."'";
//echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! Can't access can't find this record!");
			$this->msg->merge($sql->msg);
			return false;    
		}


			while ($row = $sql->fetch($q_result)) {
				$fields[] = $row;
			}
		
		return $fields;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		更新 供應商資料 某個單一欄位
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_sawdet_field($parm) {

		$sql = $this->sql;
		############### 檢查輸入項目
		
		#####   更新資料庫內容
		$q_str = "UPDATE saw_out_put SET `".$parm['field_name']."` =' ".$parm['field_value']." '  WHERE id='".$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return $q_str;
	} // end func
	
	
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		更新 供應商資料 某個單一欄位
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_line_field($parm) {

		$sql = $this->sql;
		############### 檢查輸入項目
		
		#####   更新資料庫內容
		$q_str = "UPDATE pdt_saw_line SET `".$parm['field_name']."` = '".$parm['field_value']."'  WHERE id = '".$parm['id']."' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return $q_str;
	} // end func
	
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_line($fty)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_month_people($out_date,$fty,$sub_fty='') {

		$sql = $this->sql;
		$line = array();
/*
		$q_str = "SELECT  max(out_date) as out_date
							FROM saw_out_put
							WHERE saw_out_put.out_date like '%$out_date%' AND holiday = 0 AND saw_fty = '$fty'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$row = $sql->fetch($q_result);	
		$last_date = $row['out_date'];
	
		$q_str = "SELECT distinct line_id, max(workers) as workers
							FROM saw_out_put
							WHERE saw_out_put.out_date = '$last_date' AND holiday = 0 AND saw_fty = '$fty' GROUP BY line_id";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$workers=0;
		while ($row = $sql->fetch($q_result)) {
			$workers += $row['workers'];			
		}		
*/
	  $where_str ="";
		if($sub_fty)
		{
			if($sub_fty == "LY") $where_str =" AND sub_fty = ''";
			else $where_str = " AND sub_fty ='".$sub_fty."'";
		}
	
		$q_str = "SELECT DISTINCT line_id, out_date, max(workers) as workers
							FROM saw_out_put, pdt_saw_line
							WHERE saw_out_put.line_id = pdt_saw_line.id AND saw_out_put.out_date like '$out_date%' AND 
										holiday = 0 AND saw_fty = '$fty' AND pdt_saw_line.sc = 0 ".$where_str."
							GROUP BY line_id, out_date ORDER BY out_date, line_id";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
        // echo $q_str."<br>";
		$workers=0;
		$tmp_date = $tmp_line = '';
		while ($row = $sql->fetch($q_result)) {
			if($tmp_date != $row['out_date'] )
			{
				$tmp_line = '';
				$tmp_date = $row['out_date'];
			}
			if($tmp_line != $row['line_id'])
			{
				$workers += $row['workers'];
				// echo  $row['workers'].'=>'. $row['line_id'].'=>'. $row['out_date']."<BR>";
				$tmp_line = $row['line_id'];
			}
		}	

		$q_str = "SELECT distinct out_date
							FROM saw_out_put, pdt_saw_line
							WHERE saw_out_put.line_id = pdt_saw_line.id AND saw_out_put.out_date like '$out_date%' AND 
									  holiday = 0 AND saw_fty = '$fty'".$where_str;
// echo $q_str."<br>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$out_date=0;
		while ($row = $sql->fetch($q_result)) {
			$out_date ++;			
		}	
			
		if($out_date > 0)$workers = $workers / $out_date;
		$workers = (int)$workers;
//		echo $workers."<BR>";
return $workers;
	} // end func	

	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_line($fty)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_month_line($out_date,$fty,$sub_fty='') {

		$sql = $this->sql;
		$line = array();
		$where_str = '';
		if($sub_fty)
		{
			if($sub_fty == "LY") $where_str =" AND sub_fty = ''";
			else $where_str = " AND sub_fty ='".$sub_fty."'";
		}
		$q_str = "SELECT distinct line_id, saw_line
							FROM saw_out_put, pdt_saw_line
							WHERE saw_out_put.line_id = pdt_saw_line.id AND saw_out_put.out_date like '%$out_date%' AND
									  holiday = 0 AND pdt_saw_line.sc = 0	AND saw_fty = '$fty' ".$where_str;
//echo $q_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$i=0;
		while ($row = $sql->fetch($q_result)) {
			$i++;
		}		
		
return $i;
	} // end func		
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_line($fty)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_month_day($out_date,$fty) {

		$sql = $this->sql;
		$line = array();
		$q_str = "SELECT out_date
							FROM saw_out_put
							WHERE saw_out_put.out_date like '$out_date%' AND saw_fty = '$fty' 
							GROUP BY out_date";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$i=0;
		while ($row = $sql->fetch($q_result)) {
			$i++;
		}		
		
return $i;
	} // end func			
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fields($n_field,$where_str="")	取出全部 部門表內的 $n_field 置入arry return
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_fields_with_ord($n_field,$where_str="") {
		$sql = $this->sql;
		$fields = array();
		$q_str = "SELECT ".$n_field." FROM pdt_saw_line, saw_out_put, s_order 
		WHERE saw_out_put.line_id =pdt_saw_line.id AND s_order.order_num = saw_out_put.ord_num ".$where_str;

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! Can't access can't find this record!");
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
#	->get_with_ord($n_field,$where_str="")  由pdt_saw_line,saw_out_put,s_order中取值
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_with_ord($n_field,$where_str="") {
		$sql = $this->sql;
		$fields = array();
		$q_str = "SELECT ".$n_field." FROM pdt_saw_line, saw_out_put, s_order 
		WHERE saw_out_put.line_id =pdt_saw_line.id AND s_order.order_num = saw_out_put.ord_num ".$where_str;

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! Can't access can't find this record!");
			$this->msg->merge($sql->msg);
			return false;    
		}

			while ($row = $sql->fetch($q_result)) {
				$fields[] = $row;
			}
		
		return $fields;
	} // end func		
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->count_mon_peo($dates='2010-01')
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function count_mon_peo($dates='2010-01') {
		$sql = $this->sql;
		$fields = array();
		$yy = '2010';
		$mm = '01';
		$q_str = "SELECT DISTINCT workers, line_id, saw_fty, out_date
							FROM `saw_out_put` 
							WHERE out_date like '".$dates."%' AND saw_fty = 'LY' AND workers > 0 AND holiday = 0";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! Can't access can't find this record!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$peo = 0;
		while ($row = $sql->fetch($q_result)) {
				$peo += $row['workers'];
			}

		$q_str = "SELECT out_date 
							FROM `saw_out_put` 
							WHERE out_date
							LIKE '".$dates."%' AND saw_fty = 'LY' AND holiday = 0
							GROUP BY out_date ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! Can't access can't find this record!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$days = 0;
		while ($row = $sql->fetch($q_result)) {
				$days ++;
			}
		$mon_peo = $peo / $days;
		$q_str = "SELECT id 
							FROM `month_rpt` 
							WHERE dept = 'LY' AND type = 'pdt_people' AND year = '".$yy."'
							";
		$q_result = $sql->query($q_str);
		if(!$row = $sql->fetch($q_result))
		{
			$q_str = "INSERT INTO month_rpt SET dept = 'LY' , type = 'pdt_people' , year = '".$yy."'";
			$q_result = $sql->query($q_str);			
		}
		$mon_peo = NUMBER_FORMAT($mon_peo,0,'','');
		$q_str = "UPDATE month_rpt SET `".$mm."`  = '".$mon_peo."'";
		$q_result = $sql->query($q_str);			
		
		return 1;
	} // end func		
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fields($n_field,$where_str="")	取出全部 部門表內的 $n_field 置入arry return
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function update_success_rate( $where_str="",$line_id ) {
	
	$sql = $this->sql;
	
    $FULL_TIME = $GLOBALS['FULL_TIME'];
	$schedule_var = $this->get_schedule_var();
	
	$fields = array();
	$q_str = "SELECT sum(saw_out_put.qty * s_order.ie1) as su, max(saw_out_put.workers) as workers
						FROM saw_out_put, s_order 
						WHERE saw_out_put.ord_num =s_order.order_num ".$where_str;

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error! Can't access can't find this record!");
		$this->msg->merge($sql->msg);
		return false;    
	}

	$tmp_su = $tmp_pp_su = 0;
	$j = 0;
	while ($row = $sql->fetch($q_result)) {
		if($row['workers'] > 0)$tmp_su += ($row['su'] / $row['workers']);
		$j++;
	}
	
	if( $j == 0 ) {
        $pdt_pp = 0;
	} else {
        $pdt_pp = $tmp_su/ $j ;
    }

	$pp_rate = ( $pdt_pp / $FULL_TIME ) * $schedule_var;
 
	$parm = array(
        'field_name'	=>	'success_rate',
        'field_value'	=>	$pp_rate,
        'id'            =>	$line_id
	);
		
	$this->update_line_field($parm);
		
    return true;
} // end func	
	
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fields($n_field,$where_str="")	取出全部 部門表內的 $n_field 置入arry return
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_partial_qty($p_id) {
		$sql = $this->sql;
		$fields = array();
		$q_str = "SELECT sum(saw_out_put.qty) as qty FROM saw_out_put
						  WHERE p_id = '".$p_id."'";

		$q_result = $sql->query($q_str);
		$row = $sql->fetch($q_result);
		$q_str = "UPDATE order_partial SET p_qty_done ='".$row['qty']."'  WHERE id='".$p_id."'";
		$q_result = $sql->query($q_str);
		
		return true;
	} // end func		
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_schedule_finish_value($field,$id='',$ord_num='', $tbl='s_order')	取出 某個  field的值
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_schedule_finish_value($field, $id='',$s_id='',$tbl='saw_out_put') {
		$sql = $this->sql;
		$row = array();

		if ($id) {
	  		$q_str = "SELECT ".$field." FROM ".$tbl." WHERE `s_id`='".$id."' ";
		} elseif($s_id) {
	  		$q_str = "SELECT ".$field." FROM ".$tbl." WHERE `s_id` ='".$s_id."' ";
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

    
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->mdf_ie($ord_num)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function mdf_ie($ord_num,$ie) {

$sql = $this->sql;

$q_str = "SELECT `id`,`qty` FROM `saw_out_put` WHERE `qty` > 0 AND `ord_num` = '".$ord_num."' ;";
// echo $q_str.'<br>';
if (!$q_result = $sql->query($q_str)) {
    $this->msg->add("Error ! 無法更新資料庫內容.");
    $this->msg->merge($sql->msg);
    return false;    
}

$qty = '';
while ($row = $sql->fetch($q_result)) {			
    $q_str = "UPDATE `saw_out_put` SET `su` = '".set_su($ie,$row['qty'])."' WHERE `id` = '".$row['id']."' ;";
    // echo $q_str.'<br>';
    if (!$q_res = $sql->query($q_str)) {
        $this->msg->add("Error ! 無法更新資料庫內容.");
        $this->msg->merge($sql->msg);
        return false;    
    }
}

    return true;
} // end func
    

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->get_schedule_var()
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_schedule_var() {

	$sql = $this->sql;

	$q_str = "SELECT set_value 
			  FROM para_set
			  WHERE set_name = 'schedule_var';";

	$q_result = $sql->query($q_str);
	$row = $sql->fetch($q_result);

    return $row['set_value'];
} // end func





} // end class


?>