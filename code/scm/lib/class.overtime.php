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

class OVERTIME {
		
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
#	->get($id=0, $supl_s_name=0)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_get($date,$fty,$rate) {

		$sql = $this->sql;
		$tmp_date = explode('-',$date);
		$mm = $tmp_date[0].'-'.$tmp_date[1];
		$op['ot_det'] = array();
		$q_str = "SELECT overtime.* FROM overtime WHERE ot_date ='$date' AND fty = '$fty' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if ($row = $sql->fetch($q_result)) {
			$op['ot'] = $row;    
		}		

		$q_str = "SELECT overtime_det.* FROM overtime_det 
							WHERE overtime_det.ot_id = '".$op['ot']['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			$op['ot_det'][] = $row;    
		}				
	
		
		$q_str = "SELECT overtime.*, overtime_det.rate, overtime_det.ot_worker, overtime_det.ot_time,
										 overtime_det.ot_cost	
							FROM   overtime LEFT JOIN overtime_det ON overtime_det.ot_id = overtime.id
							WHERE  ot_date <>'".$date."' AND 
										 ot_date like '".$mm."%' AND fty = '".$fty."'
							ORDER BY overtime.ot_date, overtime_det.rate";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$ot_date = ''; $i = $j = -1;
		while ($row = $sql->fetch($q_result)) {
			$op['view'][]=$row;
/*
			if($ot_date != $row['ot_date'])
			{
				
				$i++;
				$j=0;
				$op['view'][$i]['id'] = $row['id'];
				$op['view'][$i]['ot_date'] = $row['ot_date'];
				$op['view'][$i]['worker'] = $row['worker'];
				$op['view'][$i]['fty'] = $row['fty'];
				if($row['rate'])
				{
					$op['view'][$i]['det'][$j]['rate'] = $row['rate'];
					$op['view'][$i]['det'][$j]['ot_worker'] = $row['ot_worker'];
					$op['view'][$i]['det'][$j]['ot_time'] = $row['ot_time'];
					$op['view'][$i]['det'][$j]['ot_cost'] = $row['ot_cost'];
					$op['view'][$i]['det'][$j]['rate_name'] = $rate[$row['rate']];
				}
					$ot_date = $row['ot_date'];
			}else{				
				if($row['rate'])
				{
					$j++;
					$op['view'][$i]['det'][$j]['rate'] = $row['rate'];
					$op['view'][$i]['det'][$j]['ot_worker'] = $row['ot_worker'];
					$op['view'][$i]['det'][$j]['ot_time'] = $row['ot_time'];
					$op['view'][$i]['det'][$j]['ot_cost'] = $row['ot_cost'];
					$op['view'][$i]['det'][$j]['rate_name'] = $rate[$row['rate']];				
				}
			}
*/			
		}		
		return $op;
	} // end func
						
					
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新 生產資料
#						傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add($date,$fty,$currency) {
					
		$sql = $this->sql;
		$back_mk=0;
		if (!$date)
		{
				$this->msg->add("Error ! Please input date first.");
				$back_mk=1;
		}
		if (!$fty)
		{
				$this->msg->add("Error ! Please select fty first.");
				$back_mk=1;
		}		
		if ($back_mk==1)
		{
			return false;
		}
		
		$q_str = "SELECT overtime.* FROM overtime WHERE ot_date ='$date' AND fty = '$fty' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			# 加入資料庫
			$q_str = "INSERT INTO overtime (ot_date,currency,
									 fty) VALUES('".
									$date."','".
									$currency."','".
									$fty."')";

			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! Can't add data in database.");
				$this->msg->merge($sql->msg);
				return false;    
			}
			$new_id = $sql->insert_id();  //取出 新的 id			
		}else{
			$new_id = $row['id'];
		}					

		return $new_id;

	} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新 生產資料
#						傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_det($parm) {
					
		$sql = $this->sql;
		$back_mk=0;
					
					# 加入資料庫
		$q_str = "INSERT INTO overtime_det ( ot_id,
									 ot_worker,
									 ot_time,
									 rate,
									 ot_cost) VALUES('".
									$parm['ot_id']."','".
									$parm['ot_worker']."','".
									$parm['ot_time']."','".
									$parm['rate']."','".
									$parm['ot_cost']."')";
//echo $q_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't add data in database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$new_id = $sql->insert_id();  //取出 新的 id


		return $new_id;

	} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		更新 供應商資料 某個單一欄位
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field($parm) {

		$sql = $this->sql;
		############### 檢查輸入項目
		
		#####   更新資料庫內容
		$q_str = "UPDATE overtime SET `".$parm['field_name']."` =' ".$parm['field_value']." '  WHERE id='".$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return $q_str;
	} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit($parm)		更新 USER 資料
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function edit_det($parm) {

		$sql = $this->sql;

		#####   更新資料庫內容
		$q_str = "UPDATE overtime_det SET ot_worker='"		.$parm['ot_worker'].
						 "', ot_time='"		.$parm['ot_time'].
						 "', rate='"			.$parm['rate'].
						 "', ot_cost='"		.$parm['ot_cost'].
						 "'  WHERE id='"	.$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Database can't update.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func	


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del($id)		刪除 供應商 資料  [由ID]刪除
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del_det($id) {

		$sql = $this->sql;
		if (!$id) {
			$this->msg->add("Error !Please point supplier ID.");		    
			return false;
		}
		$q_str = "DELETE FROM overtime_det WHERE id='$id' ";


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
	function get_all($date,$fty,$rate) {

		$sql = $this->sql;
		$tmp_date = explode('-',$date);
		$mm = $tmp_date[0].'-'.$tmp_date[1];
		$ot = array();
		$op['view'] = array();
		$q_str = "SELECT overtime.*, overtime_det.rate, overtime_det.ot_worker, overtime_det.ot_time,
										 overtime_det.ot_cost	
							FROM   overtime , overtime_det
							WHERE  ot_date like '".$mm."%' AND fty = '".$fty."' AND overtime_det.ot_id = overtime.id
							ORDER BY overtime.ot_date, overtime_det.rate";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$ot_date = ''; $i = $j = -1;
		while ($row = $sql->fetch($q_result)) {
//			$op['view'][]=$row;			
			$ot[$row['ot_date']][$row['rate']][] = $row;
/*			
			if($ot_date != $row['ot_date'])
			{				
				$i++;
				$j=0;
				$op['view'][$i]['id'] = $row['id'];
				$op['view'][$i]['ot_date'] = $row['ot_date'];
				$op['view'][$i]['worker'] = $row['worker'];
				$op['view'][$i]['fty'] = $row['fty'];
				if($row['rate'])
				{
					$op['view'][$i]['det'][$j]['rate'] = $row['rate'];
					$op['view'][$i]['det'][$j]['ot_worker'] = $row['ot_worker'];
					$op['view'][$i]['det'][$j]['ot_time'] = $row['ot_time'];
					$op['view'][$i]['det'][$j]['ot_cost'] = $row['ot_cost'];
					$op['view'][$i]['det'][$j]['rate_name'] = $rate[$row['rate']];
				}
					$ot_date = $row['ot_date'];
			}else{				
				if($row['rate'])
				{
					$j++;
					$op['view'][$i]['det'][$j]['rate'] = $row['rate'];
					$op['view'][$i]['det'][$j]['ot_worker'] = $row['ot_worker'];
					$op['view'][$i]['det'][$j]['ot_time'] = $row['ot_time'];
					$op['view'][$i]['det'][$j]['ot_cost'] = $row['ot_cost'];
					$op['view'][$i]['det'][$j]['rate_name'] = $rate[$row['rate']];				
				}
			}
*/		
		}		
		$x = 0;
		foreach($ot as $key => $value)
		{
			$ary_size = 0;
			foreach($ot[$key] as $r_key	=> $r_value)
			{
				if(sizeof($ot[$key][$r_key]) > $ary_size)$ary_size = sizeof($ot[$key][$r_key]);
			}
			for ($i=0; $i<$ary_size; $i++)
			{
				//先建立array
				$op['view'][$x]['id'] = $op['view'][$x]['ot_date'] = $op['view'][$x]['worker'] = '';				
				$op['view'][$x]['fty'] =$op['view'][$x]['currency'] ='';
				$op['view'][$x]['ot_cost'] = 0;
				for($j=1; $j<=6; $j++)
				{
					$op['view'][$x][$j]['ot_worker'] = $op['view'][$x][$j]['ot_time'] = $op['view'][$x][$j]['ot_cost'] = '';
				}
				//將資料寫入array
				for($j=1; $j<=6; $j++)
				{
					if(isset($ot[$key][$j][$i]))
					{
						$op['view'][$x]['id'] = $ot[$key][$j][$i]['id'];
						$op['view'][$x]['ot_date'] = $ot[$key][$j][$i]['ot_date'];
						$op['view'][$x]['worker'] = $ot[$key][$j][$i]['worker'];
						$op['view'][$x]['fty'] = $ot[$key][$j][$i]['fty'];
						$op['view'][$x]['currency'] = $ot[$key][$j][$i]['currency'];
						
						$op['view'][$x][$j]['ot_worker'] = $ot[$key][$j][$i]['ot_worker'];
						$op['view'][$x][$j]['ot_time'] = $ot[$key][$j][$i]['ot_time'];
						$op['view'][$x][$j]['ot_cost'] = $ot[$key][$j][$i]['ot_cost'];
						$op['view'][$x]['ot_cost'] += $ot[$key][$j][$i]['ot_cost'];
					}
				}
				$x++;
			}
			
		}
		$op['sum']['worker'] = $op['sum']['ot_cost'] = 0;
		$op['sum']['r1_w'] = $op['sum']['r2_w'] = $op['sum']['r3_w'] = $op['sum']['r4_w'] = $op['sum']['r5_w'] = $op['sum']['r6_w'] = 0;
		$op['sum']['r1_t'] = $op['sum']['r2_t'] = $op['sum']['r3_t'] = $op['sum']['r4_t'] = $op['sum']['r5_t'] = $op['sum']['r6_t'] = 0;
		$op['sum']['r1_c'] = $op['sum']['r2_c'] = $op['sum']['r3_c'] = $op['sum']['r4_c'] = $op['sum']['r5_c'] = $op['sum']['r6_c'] = 0;
		$tmp_date = '';
		for($i=0; $i<sizeof($op['view']); $i++)
		{
			if($tmp_date != $op['view'][$i]['ot_date'])
			{
				$op['sum']['worker'] += $op['view'][$i]['worker'];
				$tmp_date = $op['view'][$i]['ot_date'];
			}
			$op['sum']['r1_w'] += $op['view'][$i]['1']['ot_worker'];
			$op['sum']['r1_t'] += $op['view'][$i]['1']['ot_time'];
			$op['sum']['r1_c'] += $op['view'][$i]['1']['ot_cost'];
			
			$op['sum']['r2_w'] += $op['view'][$i]['2']['ot_worker'];
			$op['sum']['r2_t'] += $op['view'][$i]['2']['ot_time'];
			$op['sum']['r2_c'] += $op['view'][$i]['2']['ot_cost'];
			
			$op['sum']['r3_w'] += $op['view'][$i]['3']['ot_worker'];
			$op['sum']['r3_t'] += $op['view'][$i]['3']['ot_time'];
			$op['sum']['r3_c'] += $op['view'][$i]['3']['ot_cost'];
			
			$op['sum']['r4_w'] += $op['view'][$i]['4']['ot_worker'];
			$op['sum']['r4_t'] += $op['view'][$i]['4']['ot_time'];
			$op['sum']['r4_c'] += $op['view'][$i]['4']['ot_cost'];

			$op['sum']['r5_w'] += $op['view'][$i]['5']['ot_worker'];
			$op['sum']['r5_t'] += $op['view'][$i]['5']['ot_time'];
			$op['sum']['r5_c'] += $op['view'][$i]['5']['ot_cost'];

			$op['sum']['r6_w'] += $op['view'][$i]['6']['ot_worker'];
			$op['sum']['r6_t'] += $op['view'][$i]['6']['ot_time'];
			$op['sum']['r6_c'] += $op['view'][$i]['6']['ot_cost'];

			
			$op['sum']['ot_cost'] += $op['view'][$i]['ot_cost'];
		}
		
		
		
/*		
		$x=$y=$fst=$lst=$max=-1;
		$tmp_date = $tmp_rate = '';
		for($i=0; $i<sizeof($op['view']); $i++)
		{
			if($tmp_date != $op['view'][$i]['ot_date'])
			{
				$x++;
				$ot[$x]['ot_date'] = $op['view'][$i]['ot_date'];
				$ot[$x]['worker'] = $op['view'][$i]['worker'];
				$ot[$x]['fty'] = $op['view'][$i]['fty'];
				for($j=1; $j<=6; $j++)
				{
					$ot[$x][$j]['worker'] = $ot[$x][$j]['time'] = $ot[$x][$j]['cost'] = 0;
				}
				$ot[$x]['ot_cost'] = 0;
				$ot[$x][$op['view'][$i]['rate']]['worker'] = $op['view'][$i]['ot_worker'];
				$ot[$x][$op['view'][$i]['rate']]['time'] = $op['view'][$i]['ot_time'];
				$ot[$x][$op['view'][$i]['rate']]['cost'] = $op['view'][$i]['ot_cost'];
				$ot[$x]['ot_cost'] += $op['view'][$i]['ot_cost'];
				$max = $fst= $lst = $x;
				$tmp_date = $op['view'][$i]['ot_date'];
				$tmp_rate = $op['view'][$i]['rate'];
				
			}else{
				if($tmp_rate == $op['view'][$i]['rate'])
				{
					$x++;
					$ot[$x]['ot_date'] = $op['view'][$i]['ot_date'];
					$ot[$x]['worker'] = $op['view'][$i]['worker'];
					$ot[$x]['fty'] = $op['view'][$i]['fty'];
					for($j=1; $j<=6; $j++)
					{
						$ot[$x][$j]['worker'] = $ot[$x][$j]['time'] = $ot[$x][$j]['cost'] = 0;
					}
					$ot[$x]['ot_cost'] = 0;
					$ot[$x][$op['view'][$i]['rate']]['worker'] = $op['view'][$i]['ot_worker'];
					$ot[$x][$op['view'][$i]['rate']]['time'] = $op['view'][$i]['ot_time'];
					$ot[$x][$op['view'][$i]['rate']]['cost'] = $op['view'][$i]['ot_cost'];
					$ot[$x]['ot_cost'] += $op['view'][$i]['ot_cost'];
					if($x > $max) $max = $x;
					$lst = $x;
				}else{
				
				}
			}
		}
*/		
		
		
		return $op;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $supl_s_name=0)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function count_work_time($date,$fty,$sub_fty='') {

		$sql = $this->sql;

		$q_str = "SELECT overtime_det.ot_worker, overtime_det.ot_time as tme, sum(overtime_det.ot_worker*overtime_det.ot_time) as	ot_time, overtime.worker, ot_date
							FROM overtime, overtime_det
							WHERE overtime.id = overtime_det.ot_id AND ot_date like'$date%'
							GROUP BY ot_date";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$overtime = $regtime = 0;
		$out_date = '';
		while ($row = $sql->fetch($q_result)) {
			$overtime += $row['ot_time'];
//			if($date == '2009-09')echo $row['tme']."*".$row['ot_worker']."+";
			$regtime += ($row['worker']*8);
			$out_date = $row['ot_date'];
		}			
		$where_str = '';
		if($sub_fty)
		{
			if($sub_fty == "LY") $where_str =" AND sub_fty = ''";
			else $where_str = " AND sub_fty ='".$sub_fty."'";
		}
		$q_str = "SELECT max(ot_wk) as ot_wk, max(ot_hr) as	ot_hr, workers
							FROM saw_out_put, pdt_saw_line
							WHERE saw_out_put.line_id = pdt_saw_line.id AND out_date like'$date%' AND 
									  saw_fty = '$fty' AND out_date > '2009-09-27' AND holiday = 0 ".$where_str."
							GROUP BY out_date, line_id";
//echo $q_str."<BR>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		while ($row = $sql->fetch($q_result)) {
			$overtime += ($row['ot_wk'] * $row['ot_hr']);
			$regtime += ($row['workers']*8);
		}		

		
		
		
		
		$overtime_rate = 0;
		if($regtime > 0)$overtime_rate = ($overtime / $regtime)*100;
//		if($date == '2009-09') echo $overtime_rate ."= (".$overtime." / ".$regtime.")";
		return $overtime_rate;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $supl_s_name=0)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function count_work_cost($date,$fty,$sub_fty='') {

		$sql = $this->sql;
		$LY_SALARY = $_SESSION['LY_SALARY']; 
		$RATE = $_SESSION['RATE']; 
		$VND = $_SESSION['VND']; 
		
		$q_str = "SELECT ot_date, sum(overtime_det.ot_cost) as	ot_cost, overtime.worker, ot_date
							FROM overtime, overtime_det
							WHERE overtime.id = overtime_det.ot_id AND ot_date like'$date%'
							GROUP BY ot_date";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$overcost = $regcost = 0;
		$ot_date = '';
		while ($row = $sql->fetch($q_result)) {
			$overcost += $row['ot_cost'];
			$regcost += ($row['worker']*120/26);
			$ot_date = $row['ot_date'];
//			if($date == '2009-09') echo $row['worker']."*120/26+";
//			if($date == '2009-09')echo $row['worker']."-->".($row['worker']*120/26)."-->".$row['ot_cost']."<BR>";
		}			

		$where_str = '';
		if($sub_fty)
		{
			if($sub_fty == "LY") $where_str =" AND sub_fty = ''";
			else $where_str = " AND sub_fty ='".$sub_fty."'";
		}		
	
		$q_str = "SELECT max(ot_wk) as ot_wk, max(ot_hr) as	ot_hr, workers, out_date
							FROM saw_out_put, pdt_saw_line
							WHERE saw_out_put.line_id = pdt_saw_line.id AND out_date like'$date%' AND 
										saw_fty = '$fty' AND out_date >'2009-09-27'".$where_str."
							GROUP BY out_date, line_id
							";
//echo $q_str."<BR>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		while ($row = $sql->fetch($q_result)) {
			$unix_time = strtotime($row['out_date']); 
			$week_day = date('w', $unix_time);  	
			
			if( $week_day > 0)
			{															
				$tmp_h1 = ($row['ot_hr'] > 4) ? '4' : $row['ot_hr'];
				$tmp_h2 = ($row['ot_hr'] > 4) ? ($row['ot_hr']-4) : '0';
				$overcost += ($row['ot_wk']*$tmp_h1* ($LY_SALARY /8 )* $RATE[1] / $VND);
				$overcost += ($row['ot_wk']*$tmp_h2* ($LY_SALARY /8 )* $RATE[2] / $VND);
			}else{
				$tmp_h1 = ($row['ot_hr'] > 8) ? '8' : $row['ot_hr'];
				$tmp_h2 = ($row['ot_hr'] > 8) ? ($row['ot_hr']-8) : '0';
				$overcost += ($row['ot_wk']*$tmp_h1* ($LY_SALARY /8 )* $RATE[3] / $VND);
				$overcost += ($row['ot_wk']*$tmp_h2* ($LY_SALARY /8 )* $RATE[4] / $VND);								
			}			
			
			$regcost += ($row['workers']*120/26);						
		}		

		
		$overcost_rate = 0;
		if($regcost > 0)$overcost_rate = ($overcost / $regcost)*100;
//		if($date == '2009-09')echo $row['ot_date']."==>".$overcost."/".$regcost."=".$overcost_rate."</BR>";
		
		return $overcost_rate;
	} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

} // end class


?>