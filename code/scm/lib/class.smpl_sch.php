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

class SMPL_SCH {
		
	var $sql;
	var $msg ;
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->init($sql)	啟始(使用 Msg_handle() ; 先聯上 sql)
#		必需聯上 sql 才可  啟始
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
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
#	->increceDay($str_date,$days)
#		計算排產日期(加入假日)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function increceDay($str_date,$days) {
		$hdy = 0;

		if($days > 0)
		{
			for($i=0; $i<=$days; $i=$i+12)
			{
				$tmp_date = increceHoursInDate($str_date,$i);
//				if(date('w',strtotime($tmp_date)) == 0) $hdy++;
				if(date('w',strtotime($tmp_date)) == 6) $hdy++;			
			}
			$hdy = $hdy - ($hdy % 2);
			$days += ($hdy*12*2);			
			$fnl_date = increceHoursInDate($str_date,$days);
		}else{
			for($i=0; $i>=$days; $i=$i-12)
			{
				$tmp_date = increceHoursInDate($str_date,$i);
			
//				if(date('w',strtotime($tmp_date)) == 0) $hdy++;
				if(date('w',strtotime($tmp_date)) == 6) $hdy++;			
			}
			$hdy = $hdy - ($hdy % 2);
			$days += ($hdy*12);
			$fnl_date = increceHoursInDate($str_date,$days);		
		}
		return $fnl_date;
	} // end func	
	

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->increceDay($str_date,$days)
#		重算排產日期(加入假日)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function increceSchdDay($org_ets,$org_etf,$new_ets) {
		$hdy = 0;
		$p_day = countHours($org_ets,$org_etf);
		for($i=0; $i<=$p_day; $i=$i+12)
		{
			$tmp_date = increceHoursInDate($org_ets,$i);			
			if(date('w',strtotime($tmp_date)) == 0) $hdy++;			
			if(date('w',strtotime($tmp_date)) == 6) $hdy++;
		}
		$p_day -= ($hdy*12);  //計算實際排產日數
		
		$hdy = 0;
		for($i=0; $i<=$p_day; $i=$i+12)
		{
			$tmp_date = increceHoursInDate($new_ets,$i);			
			if(date('w',strtotime($tmp_date)) == 6) $hdy++;		
		}		
		$hdy = $hdy - ($hdy % 2);
		$p_day += ($hdy*12 *2);
		$fnl_date = increceHoursInDate($new_ets,$p_day);

		return $fnl_date;
	} // end func	
	
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_line($fty)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_line($fty) {

		$sql = $this->sql;
		$line = array();
		$where_str ='';
	

		if ($fty) $where_str .= " AND fty = '$fty'";

		$q_str = "SELECT smpl_saw_line.* FROM smpl_saw_line WHERE del_mk = 0 $where_str  ORDER BY line";

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
	function get_ord_line($ord_num) {

		$sql = $this->sql;
		$line = array();
		$where_str ='';

		if ($ord_num) $where_str .= " AND ord_num = '$ord_num'";
		$q_str = "SELECT DISTINCT smpl_saw_line.id, smpl_saw_line.line 
						  FROM smpl_saw_line, smpl_schedule 
						  WHERE smpl_saw_line.id = smpl_schedule.line_id $where_str  ORDER BY line";

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
#	->get($id=0, season=0)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_order($fty,$limit_date='') {

		$sql = $this->sql;
		
		$today= date('Y-m-d');
		$rtn = array();
		$q_str = "SELECT smpl_ord.num, status, smpl_ord.style,
										 smpl_ord.qty, smpl_ord.ext_qty, smpl_ord.etd, smpl_ord.smpl_type
							FROM   smpl_ord
							WHERE  smpl_ord.status > 0 AND smpl_ord.status <= 6 AND	qty_done < qty AND										 									 
										 ext_qty < qty AND smpl_ord.etd > '2009-12-31' AND factory = 'CL'";
		if($limit_date) $q_str .= " AND etd < '".$limit_date."' ";
		$q_str .= "	ORDER BY smpl_ord.etd";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			$row['rem_qty'] = $row['qty'] - $row['ext_qty'];
			$rtn[] = $row;  
		}
		return $rtn;
	} // end func	
	
	
	

	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, season=0)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_by_mm($fty,$sch_date,$fsh_date) {

		$sql = $this->sql;
		$finish_rate = $_SESSION['finish_rate'];
		
		$today= date('Y-m-d');
		$rtn = array();
		$str_date = $sch_date;

		$q_str = "SELECT smpl_saw_line.* FROM smpl_saw_line
							WHERE  smpl_saw_line.fty ='".$fty."' AND worker > 0 AND del_mk = 0 ORDER BY line";							
							

		$q_result = $sql->query($q_str);		
		while($row = $sql->fetch($q_result)) $rtn[] = $row;
		
		
		
		for($i=0; $i<sizeof($rtn); $i++)
		{
			$q_str = "SELECT smpl_schedule.*, smpl_ord.style, smpl_ord.status as ord_status ,
											 smpl_ord.etd, smpl_ord.id as ord_id, smpl_ord.qty as ord_qty, smpl_ord.ie,
											 smpl_ord.smpl_type 											 
								FROM   smpl_schedule, smpl_ord
								WHERE  smpl_ord.num = smpl_schedule.ord_num AND	smpl_schedule.line_id ='".$rtn[$i]['id']."' AND										 
											 smpl_schedule.rel_etf > '".$str_date."' AND rel_ets <='".$fsh_date."'								
								ORDER BY rel_etf";							
						//	echo $q_str."<BR>";
			$q_result = $sql->query($q_str);		
			$j=$str_date;
			$fx = 0;
			while($row = $sql->fetch($q_result))
			{
				$row['ord_init'] = substr($row['ord_num'], 5);
				$s_date = $row['rel_ets'];
				
				if($j == $str_date && $s_date > $str_date)
				{
					$non_row['day_range'] = countHours($j,$s_date) / 12;
					$non_row['flag'] = 0;
					$non_row['flag_color'] = "#eeeeee";
					$rtn[$i]['sch_rec'][] = $non_row;
				}				

					if($j != $s_date && $j != $str_date)
					{
						$non_row['day_range'] = countHours($j,$s_date) / 12;						
						$non_row['flag'] = 0;
						$non_row['flag_color'] = "#eeeeee";
						if($non_row['day_range'] )$rtn[$i]['sch_rec'][] = $non_row;
					}
					
					if($s_date < $str_date)
					{					
						
//						echo $row['ord_num'].'===>'.$str_date.'===>'.countHours($s_date,$row['rel_etf']).'=====>'.(countHours($str_date,$row['rel_etf'])/ 12).'<BR>';
						$row['day_range'] = (countHours($str_date,$row['rel_etf'])) / 12;
						
					}else if($row['rel_etf'] > $fsh_date){

						$row['day_range'] = (countHours($s_date,$fsh_date)/12)+1;
					}else{				
//					  echo $row['ord_num'].'===>'.$s_date.'===>'.countHours($s_date,$row['rel_etf']).'=====>'.(countHours($s_date,$row['rel_etf'])/12).'<BR>';
						$row['day_range'] = (countHours($s_date,$row['rel_etf']))/12;
					//	if($fx == 0)	$row['day_range'] += 1;					
					}				
				
					$ets = $row['rel_ets'];
					if( $row['ord_status'] == '10' || $row['qty'] == $row['pdt_qty'] ) 
					{
						$row['flag'] = 11;
						$row['flag_color'] = "#D7D7FF"; //生產FINISH 淺藍
					}else if($row['pdt_qty'] > 0 && $row['rel_etf'] > $row['etd']){					
						$row['flag'] = 10;
						$row['flag_color'] = "#96DDA3";	//生產過期 深綠色
					}else if($row['pdt_qty'] > 0){					
						$row['flag'] = 10;
						$row['flag_color'] = "#D0FDCD";	//生產綠色
					}else if( $row['rel_etf'] > $row['etd']) {
						$row['flag'] = 9;
						$row['flag_color'] = "#FD1838"; //排產etd超過出口日 紅色
					}else if($row['ord_status'] == '7'){			
						$row['flag'] = 8;
						$row['flag_color'] = "#FD66DF";		//pending 桃紅色
					}else{
						$row['flag'] = 1;
						$row['flag_color'] = "#FFFFFF";
					}

/*
					if( $row['ord_status'] >= '10' ||  ($row['pdt_qty']/$row['qty']) > $finish_rate) 
					{
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
					}else if(!$row['mat_shp']){					
						$row['flag'] = 6;
						$row['flag_color'] = "#FD66DF";		//無主料 桃紅色
					}else if($row['m_status'] < 3){					
						$row['flag'] = 8;
						$row['flag_color'] = "#A36ED4";		//無BOM 紫色
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
						//$row['flag_color'] = "#FF6F43";		//無樣本 橘色			
				 }				
*/	
				$rtn[$i]['sch_rec'][] = $row;
				$j = $row['rel_etf'];
				$fx++;
			}			
		}

		
		return $rtn;
	} // end func	
	
	
/*	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, season=0)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_contract($fty,$sch_date,$fsh_date) {

		$sql = $this->sql;
		$finish_rate = $_SESSION['finish_rate'];
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
											 s_order.etd, s_order.id as ord_id, pdtion.mat_shp, pdtion.m_acc_shp,
											 s_order.style_num, s_order.qty as ord_qty, s_order.ie1, s_order.m_status
								FROM schedule, s_order, pdtion
								WHERE  s_order.order_num = schedule.ord_num AND schedule.ord_num = pdtion.order_num AND
											 s_order.order_num = pdtion.order_num AND
											 schedule.line_id ='".$rtn[$i]['id']."' AND schedule.rel_etf >= '".$str_date."' AND
											 ((schedule.rel_ets <> '0000-00-00' AND rel_ets <='".$fsh_date."') OR 
											 (schedule.rel_ets = '0000-00-00' AND schedule.ets <='".$fsh_date."'))								
								ORDER BY rel_etf";							
			$q_result = $sql->query($q_str);		
			
			
			while($row = $sql->fetch($q_result))
			{
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
						$row['day_range'] = dayCount($str_date,$row['rel_etf'])+1;						
					}else if($row['rel_etf'] > $fsh_date){
						$row['day_range'] = dayCount($s_date,$fsh_date)+1;
					}else{						
						$row['day_range'] = dayCount($s_date,$row['rel_etf'])+1;
					}				
							
					
					if( $row['ord_status'] >= '10' ||  ($row['pdt_qty']/$row['qty']) > $finish_rate) 
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
*/	
	
	
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
/*		
		$today= date('Y-m-d');
		$rtn = array();
		$q_str = "SELECT s_order.order_num, s_order.su, pdtion.ext_su  , status, mat_eta, m_acc_eta, style,
											pdtion.ext_period, s_order.ie1, s_order.apv_date
							FROM   s_order,pdtion 
							WHERE  s_order.order_num = pdtion.order_num AND
										 s_order.order_num ='".$parm['ord_num']."'
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
 		  $rtn[0]['sp_period'] = $rtn[0]['sp_su'] /( 9.6 * $rtn[0]['worker'] * $rtn[0]['success_rate']);
 			$tmp = explode('.',$rtn[0]['sp_period']);
 			if(isset($tmp[1]) && $tmp[1])$rtn[0]['sp_period']++;
 			if ( $rtn[0]['sp_period'] <= 1) $rtn[0]['sp_period'] = 2;
// 		  $rtn[0]['new_etf'] = increceDaysInDate($ets,$rtn[0]['sp_period']);			
 		  $rtn[0]['new_etf'] = $this->increceDay($ets,$rtn[0]['sp_period']);			







					# 加入資料庫
		$q_str = "INSERT INTO schedule (line_id, ord_num, des, su, qty, ets,etf,rel_ets,rel_etf,fty,open_date,open_user) 
							VALUES ('".$rtn[0]['id']."','"
												.$parm['ord_num']."','"	
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
		  	if($oth_sch[$i]['rel_ets'] < $new_etf)
		  	{
		  		$period_dif = countDays ($oth_sch[$i]['rel_ets'],$new_etf);   //計算插單後需要移動的日期數		  		
			 		$oth_sch[$i]['new_ets'] = increceDaysInDate($oth_sch[$i]['rel_ets'],$period_dif);
//			 		$oth_sch[$i]['new_etf'] = increceDaysInDate($oth_sch[$i]['rel_etf'],$period_dif);
//			 		$oth_sch[$i]['new_etf'] = $this->increceDay($oth_sch[$i]['rel_etf'],$period_dif);
			 		$oth_sch[$i]['new_etf'] = $this->increceSchdDay($oth_sch[$i]['rel_ets'],$oth_sch[$i]['rel_etf'],$oth_sch[$i]['new_ets']);
					$q_str =  "UPDATE schedule SET rel_etf='".$oth_sch[$i]['new_etf']."', rel_ets='".$oth_sch[$i]['new_ets']."', etf='".$oth_sch[$i]['new_etf']."', ets='".$oth_sch[$i]['new_ets']."'  WHERE id = '".$oth_sch[$i]['id']."'";
					$q_result = $sql->query($q_str);
					$new_etf =  increceDaysInDate($oth_sch[$i]['new_etf'],1);
		  	}else{
		  		break;
		  	}		  	 
		  }
		}
		
//組合訂單
	$this->group_schedule($new_id);
*/		
//重算生產線目前訂單後其他訂單排產 end		
		
		$new_id = $this->point_schedule($parm);
		$this->del_schedule($parm['id'],$parm['sp_qty'],$parm['ord_num']);
		
	
	
	
	
	
		return $new_id;
	} // end func	




#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->point_schedule($parm)	一開始指定生產線與數量
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function point_schedule($parm) {

		$sql = $this->sql;
		
		$today= date('Y-m-d');
		$rtn = array();
		$q_str = "SELECT smpl_ord.num, status, style, smpl_ord.ie, submit_date										 
							FROM   smpl_ord 
							WHERE  smpl_ord.num ='".$parm['ord_num']."'";

		$q_result = $sql->query($q_str);
		$ord_rec = $sql->fetch($q_result);		
		
		$ord_sch_date = $ord_rec['submit_date'].' 00:00:00';
		
		$sp_su = $parm['sp_qty'];
		$sch_style = group_order_style($ord_rec['style']);
		$rtn = array();
		$rtn_mk = 0;
		

		$ets = '1111-11-11';
		$q_str = "SELECT smpl_saw_line.*, max(smpl_schedule.rel_etf) as etf, 
										 count(smpl_schedule.line_id)as sch_count 
							FROM   smpl_saw_line
							LEFT JOIN smpl_schedule ON smpl_saw_line.id = smpl_schedule.line_id 
							WHERE smpl_saw_line.id ='".$parm['line_sp']."'
							GROUP BY smpl_saw_line.id";

						
			$q_result = $sql->query($q_str);	
			$row = $sql->fetch($q_result);
					
			if($row['etf'] && $row['etf'] > $ord_sch_date)
			{
				$row['ets'] =$row['etf'];
				if($row['ets'] > $ets ) $ets = $row['ets'];
			}else{
				$row['ets']  = $ord_sch_date;
				if($row['ets'] > $ets ) $ets = $row['ets'];
			}
			$rtn[0] = $row;

			if($parm['pt_ets'])$rtn[0]['ets'] = $ets = $parm['pt_ets'];	
			if(!$parm['pt_ets'])$parm['pt_ets']= $rtn[0]['ets'];
			if($ets < date('Y-m-d'))$ets = date('Y-m-d 00:00:00');	
			
			if($sch_style == 'top')$rtn[0]['sp_period'] = 24 * $parm['sp_qty'];
			if($sch_style == 'btm.')$rtn[0]['sp_period'] = 12 * $parm['sp_qty'];

 //= increceDaysInDate($ets,$rtn[0]['sp_period']);			
			$rtn[0]['new_etf'] = $this->increceDay($ets,$rtn[0]['sp_period']);
					# 加入資料庫
		$q_str = "INSERT INTO smpl_schedule (line_id, ord_num,des,su, qty, ets,etf,rel_ets,rel_etf,fty,open_date,open_user) 
							VALUES ('".$rtn[0]['id']."','"
												.$parm['ord_num']."','"	
												.$parm['des']."','"											
												.$parm['sp_qty']."','"
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
		$rtn[0]['new_etf'] = $rtn[0]['new_etf'];
		$q_str = "SELECT smpl_schedule.* FROM smpl_schedule							
							WHERE line_id ='".$parm['line_sp']."' AND rel_ets >= '".$parm['pt_ets']."' AND 
									  id <> '".$new_id."'
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
		  	if($oth_sch[$i]['rel_ets'] < $new_etf)
		  	{
		  		$period_dif = countHours ($oth_sch[$i]['rel_ets'],$new_etf);   //計算插單後需要移動的日期數
			 		$oth_sch[$i]['new_ets'] = increceHoursInDate($oth_sch[$i]['rel_ets'],$period_dif);
			 		$oth_sch[$i]['new_etf'] = $this->increceSchdDay($oth_sch[$i]['rel_ets'],$oth_sch[$i]['rel_etf'],$oth_sch[$i]['new_ets']);
//			 		$oth_sch[$i]['new_etf'] = increceDaysInDate($oth_sch[$i]['rel_etf'],$period_dif);
					$q_str =  "UPDATE smpl_schedule SET rel_etf='".$oth_sch[$i]['new_etf']."', rel_ets='".$oth_sch[$i]['new_ets']."', etf='".$oth_sch[$i]['new_etf']."', ets='".$oth_sch[$i]['new_ets']."'  WHERE id = '".$oth_sch[$i]['id']."'";
					$q_result = $sql->query($q_str);
					$new_etf = $oth_sch[$i]['new_etf'];
		  	}else{
		  		break;
		  	}		  	 
		  }
		}		
//重算生產線目前訂單後其他訂單排產 end						

//組合訂單
//	$this->group_schedule($new_id);

		return $new_id;
	} // end func	




#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->change_line($parm) 更換排產生產線
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function change_line($parm) {

		$sql = $this->sql;
	
	
	
		
		$new_id = $this->point_schedule($parm);
		
		$this->del_schedule($parm['org_id'],0,$parm['ord_num']);
/*		
		$today= date('Y-m-d');
		$rtn = array();
		$q_str = "SELECT s_order.order_num, s_order.su, pdtion.ext_su  , status, mat_eta, m_acc_eta, style,
											pdtion.ext_period, s_order.apv_date
							FROM   s_order,pdtion 
							WHERE  s_order.order_num = pdtion.order_num AND
										 s_order.order_num ='".$parm['ord_num']."'
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
				$row['ets'] =increceDaysInDate($row['etf'],1);
			}else{
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
			
//計算新生產線生產日數與ETF

			if($rtn['success_rate'] == 0) $rtn['success_rate'] = 1;
			$rtn['ext_period'] = $org_rec['su'] /( 9.6 * $rtn['worker'] *$rtn['success_rate']);						
	 		$tmp = explode('.',$rtn['ext_period']);
	 		if(isset($tmp[1]) && $tmp[1])$rtn['ext_period']++;
	 		if($rtn['ext_period'] <= 1)$rtn['ext_period'] = 2;
//	 		$new_etf = $rtn['new_etf'] = increceDaysInDate($rtn['ets'],$rtn['ext_period']);
	 		$new_etf = $rtn['new_etf'] = $this->increceDay($rtn['ets'],$rtn['ext_period']);
			
//echo $org_rec['su']." / (9.6 * ".$rtn['worker']." * ".$rtn['success_rate']."<BR>";

					# 加入資料庫
		$q_str = "INSERT INTO schedule (line_id, ord_num,des,su,qty,ets,etf,rel_ets,rel_etf,fty,open_date,open_user) 
							VALUES ('".$parm['new_line']."','"
												.$parm['ord_num']."','"
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
		  	if($oth_sch[$i]['rel_ets'] < $new_etf)
		  	{
		  		$period_dif = countDays ($oth_sch[$i]['rel_ets'],$new_etf);   //計算插單後需要移動的日期數
			 		$oth_sch[$i]['new_ets'] = increceDaysInDate($oth_sch[$i]['rel_ets'],$period_dif);
//			 		$oth_sch[$i]['new_etf'] = increceDaysInDate($oth_sch[$i]['rel_etf'],$period_dif);
			 		$oth_sch[$i]['new_etf'] = $this->increceSchdDay($oth_sch[$i]['rel_ets'],$oth_sch[$i]['rel_etf'],$oth_sch[$i]['new_ets']);
					$q_str =  "UPDATE schedule SET rel_etf='".$oth_sch[$i]['new_etf']."', rel_ets='".$oth_sch[$i]['new_ets']."', etf='".$oth_sch[$i]['new_etf']."', ets='".$oth_sch[$i]['new_ets']."'  WHERE id = '".$oth_sch[$i]['id']."'";
					$q_result = $sql->query($q_str);
					$new_etf =  increceDaysInDate($oth_sch[$i]['new_etf'],1);
		  	}else{
		  		break;
		  	}		  	 
		  }
		}
//重算生產線目前訂單後其他訂單排產 end		

//組合訂單
	$this->group_schedule($new_id);
*/		
		return $new_id;
	} // end func	




#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del_schedule($id) 產除生產線排產
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del_schedule($id,$qty,$ord_num) {

		$sql = $this->sql;
		
		$today= date('Y-m-d');
		$rtn = array();


		$q_str = "SELECT smpl_ord.num, status, style, smpl_ord.ie, submit_date											
							FROM   smpl_ord 
							WHERE  smpl_ord.num ='".$ord_num."'";										 

		$q_result = $sql->query($q_str);
		$ord_rec = $sql->fetch($q_result);
		$ord_sch_date = $ord_rec['submit_date'].' 00:00:00';
		$sch_style = group_order_style($ord_rec['style']);
		$del_su = $qty;



		$q_str = "SELECT smpl_saw_line.*, smpl_schedule.rel_etf, smpl_schedule.rel_ets, smpl_schedule.ets , 
										 smpl_schedule.id as sch_id, smpl_schedule.su, smpl_schedule.qty
							FROM smpl_saw_line, smpl_schedule
							WHERE smpl_saw_line.id = smpl_schedule.line_id  AND smpl_schedule.id ='".$id."'"	;
							
			$q_result = $sql->query($q_str);	
			$row = $sql->fetch($q_result);

			$row['su'] -= $del_su;
			$row['qty'] -= $qty;
			$new_rel_ets = $row['rel_ets'];
			$line_id = $row['id'];

			if($row['su'] > 0 && $del_su)
			{			
				if($sch_style == 'top')$row['sp_period'] = 24 * $row['qty'];
				if($sch_style == 'btm.')$row['sp_period'] = 12 * $row['qty'];
 			
 		  	$row['new_rel_etf'] = $row['new_etf'] = $this->increceDay($row['ets'],$row['sp_period']);	
 		  	if($row['rel_ets'] <> '0000-00-00')$row['new_rel_etf'] = $this->increceDay($row['rel_ets'],$row['sp_period']);		
				
				$q_str = "UPDATE smpl_schedule SET 
									su='".$row['su']."',
									qty='".$row['qty']."',
									etf='".$row['new_etf']."',
									rel_etf='".$row['new_rel_etf']."'
									WHERE id='".$id."'";
								
				$q_result = $sql->query($q_str);
				$new_rel_ets = $row['new_rel_etf'];
			}else{
			
				$q_str = "DELETE FROM smpl_schedule WHERE id='".$id."'";		

				$q_result = $sql->query($q_str);
			}			
			
			
//重算生產線目前訂單後其他訂單排產 start		
		$oth_sch = array();		
		$q_str = "SELECT smpl_schedule.* FROM smpl_schedule							
							WHERE line_id ='".$line_id."' AND rel_ets >= '".$new_rel_ets."' 
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
		  	if($oth_sch[$i]['rel_ets'] >= $new_rel_ets)
		  	{
		  		$period_dif = countHours ($oth_sch[$i]['rel_ets'],$new_rel_ets);   //計算插單後需要移動的日期數			 		
			 		$oth_sch[$i]['new_ets'] = increceHoursInDate($oth_sch[$i]['rel_ets'],$period_dif);
			 		//$oth_sch[$i]['new_etf'] = increceDaysInDate($oth_sch[$i]['rel_etf'],$period_dif);
			 		$oth_sch[$i]['new_etf'] = $this->increceSchdDay($oth_sch[$i]['rel_ets'],$oth_sch[$i]['rel_etf'],$oth_sch[$i]['new_ets']);
					$q_str =  "UPDATE smpl_schedule SET rel_etf='".$oth_sch[$i]['new_etf']."', rel_ets='".$oth_sch[$i]['new_ets']."', etf='".$oth_sch[$i]['new_etf']."', ets='".$oth_sch[$i]['new_ets']."'  WHERE id = '".$oth_sch[$i]['id']."'";
					$q_result = $sql->query($q_str);
					$new_rel_ets =  $oth_sch[$i]['new_etf'];
					
		  	}else{
		  		break;
		  	}		  	 
		  }
		}
//重算生產線目前訂單後其他訂單排產 end			
		$oth_sch = array();	

		return 1;
	} // end func	


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add_ord_schedule($ord_num) 將排產資料加入訂單
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_ord_schedule($ord_num) {

		$sql = $this->sql;
		
		$today= date('Y-m-d');
		$rtn = array();
	
		$q_str = "SELECT * FROM   smpl_ord WHERE  smpl_ord.num ='".$ord_num."'";
		$q_result = $sql->query($q_str);	
		$smpl_row = $sql->fetch($q_result);

			
		$q_str = "SELECT max(rel_etf) as etf, min(rel_ets) as ets, sum(su) as su, sum(qty) as qty	
							FROM   smpl_schedule
							WHERE  smpl_schedule.ord_num ='".$ord_num."'
							GROUP BY ord_num"	;
			$q_result = $sql->query($q_str);	
			if($row = $sql->fetch($q_result))
			{

			//訂單加入己排產數量
				$row['etf'] = substr($row['etf'],0,10);
				$q_str = "UPDATE smpl_ord SET  ext_qty ='".$row['qty']."', schd_smpl='".$row['etf']."'  WHERE num='".$ord_num."'";
				
				$q_result = $sql->query($q_str);
				
				$smpl_row['status'] = ($smpl_row['schd_pttn'] <> '0000-00-00') ?  6 : 4;
				$q_str = "UPDATE smpl_ord SET  status ='".$smpl_row['status']."' WHERE num='".$ord_num."'";
				$q_result = $sql->query($q_str);


			//訂單加入預計生產所需日數
//				$day_range = dayCount($row['ets'],$row['etf']);
//				$q_str = "UPDATE pdtion SET ext_period ='".$day_range."'  WHERE order_num='".$ord_num."'";
//				$q_result = $sql->query($q_str);

			}else{
			//訂單加入ets,etf
				$q_str = "UPDATE smpl_ord SET schd_smpl = NULL, ext_qty = 0 WHERE num='".$ord_num."'";
				$q_result = $sql->query($q_str);			

			}
		return 1;
	} // end func	


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->contractor_schedule($parm) 外發排產
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function contractor_schedule($parm) {

		$sql = $this->sql;
		
		$today= date('Y-m-d');
		$rtn = array();


		$q_str = "SELECT s_order.order_num, s_order.su, pdtion.ext_su  , status, mat_eta, m_acc_eta, style,
											pdtion.ext_period, s_order.ie1, s_order.qty
							FROM   s_order,pdtion 
							WHERE  s_order.order_num = pdtion.order_num AND
										 s_order.order_num ='".$parm['ord']."'
							ORDER BY pdtion.mat_eta, m_acc_eta";

		$q_result = $sql->query($q_str);
		$ord_rec = $sql->fetch($q_result);
		

		$su = NUMBER_FORMAT(($parm['qty'] * $ord_rec['ie1']),'0','.','');

					# 加入資料庫
		$q_str = "INSERT INTO schedule (line_id, ord_num,des,su,qty,ets,etf,rel_ets,rel_etf,fty,open_date,open_user) 
							VALUES ('".$parm['line_id']."','"
												.$parm['ord']."','"
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
	function check_sch_finish($ord_num) {

		$sql = $this->sql;
		
		$today= date('Y-m-d');
		$rtn = array();


		$q_str = "SELECT s_order.order_num, pdtion.ext_qty, s_order.qty
							FROM   s_order,pdtion 
							WHERE  s_order.order_num = pdtion.order_num AND
										 s_order.order_num ='".$ord_num."'";

		$q_result = $sql->query($q_str);
		$ord_rec = $sql->fetch($q_result);
		
		if($ord_rec['qty'] > $ord_rec['ext_qty']) return false;
		



		return 1;
	} // end func	


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add_capacity($parm) 將排產capacity寫入記錄中
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_capacity($ord_num) {

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
			}
			$F = $GLOBALS['capaci']->delete_su($ord_rec['factory'], $yy, $mm,'schedule', $mm_su);

			$GLOBALS['order']->del_cfm_pd_schedule($ord_rec['pd_id']);			
		}	


		$parm = array(	"pd_id"		=>  $ord_rec['pd_id'],
										"ets"			=>  $ord_rec['ets'],
										"etf"			=>  $ord_rec['etf'],
										"sub_con"	=> 	$sc ,
				);
		$parm['fty_su'] = $GLOBALS['order']->distri_month_su($ord_rec['su'],$ord_rec['ets'],$ord_rec['etf'],$ord_rec['factory'],'schedule');

		$f1 = $GLOBALS['order']->add_cfm_pd_schedule($parm);   // 更新資料庫

		if($ord_rec['status'] < 7)$ord_rec['status'] = 7;

		$argv = array(	"id"				=>  $ord_rec['id'],
										"status"		=>  $ord_rec['status'],
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


		$q_str = "SELECT smpl_schedule.*, smpl_saw_line.line FROM smpl_schedule, smpl_saw_line 
							WHERE  smpl_schedule.line_id = smpl_saw_line.id AND smpl_schedule.id = $id";

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
	function reload_schedule($line_id, $ord_num) {

		$sql = $this->sql;
		
		$today= date('Y-m-d');
		$rtn = array();

		$q_str = "SELECT smpl_saw_line.*  FROM smpl_saw_line
							WHERE  smpl_saw_line.id ='".$line_id."'";
		$q_result = $sql->query($q_str);
	
		if(!$line_det = $sql->fetch($q_result)) return false;
		

		$q_str = "SELECT smpl_schedule.*, smpl_ord.style  FROM smpl_schedule, smpl_ord
							WHERE  smpl_schedule.ord_num = smpl_ord.num AND 
										 smpl_schedule.line_id ='".$line_id."' AND ord_num='".$ord_num."' AND pdt_qty < smpl_schedule.qty";
	
		$q_result = $sql->query($q_str);
		if(!$ord_sch = $sql->fetch($q_result)) return false;
		$sch_style = group_order_style($ord_sch['style']);
/*
		$q_str = "SELECT smpl_schedule.*  FROM smpl_schedule
							WHERE  smpl_schedule.line_id ='".$line_id."' AND ord_num='".$ord_num."' AND pdt_qty >= qty";
		$q_result = $sql->query($q_str);
		$oth_sch_su = $oth_sch_qty = 0;
		while($rows = $sql->fetch($q_result))
		{
			$oth_sch_su += $rows['su'];
			$oth_sch_qty += $rows['qty'];
		}
*/		

		$q_str = "SELECT sum(qty) as qty  FROM smpl_output 						  
							WHERE  smpl_output.line_id ='".$line_id."' AND num='".$ord_num."'
							GROUP BY line_id, num";				
		
		$q_result = $sql->query($q_str);
		if(!$pdt = $sql->fetch($q_result)) return false;

//計算出生產後的預計完成日 start		
		$pdt['su'] = $pdt['qty'] = $pdt['qty'];
		
		if(( $pdt['qty'] >= $ord_sch['qty']))
		{
			$now_etf = date('Y-m-d').' 00:00:00';
			if($now_etf < $ord_sch['rel_ets'])$now_etf = $ord_sch['rel_ets'];
			$period_dif = countHours ($ord_sch['rel_etf'],$now_etf);
		}else{				 	
			if($sch_style == 'top')$ext_period = 24 * ($ord_sch['su'] - $pdt['su']);
			if($sch_style == 'btm.')$ext_period = 12 * ($ord_sch['su'] - $pdt['su']);
	 		$rld_ets = $ord_sch['rel_ets'];
	 		if(date('Y-m-d') > $rld_ets)$rld_ets = date('Y-m-d').' 00:00:00';
			
			$now_etf = $this->increceDay($rld_ets,$ext_period);		
			if($now_etf < $ord_sch['rel_ets'])$now_etf = increceHoursInDate($ord_sch['rel_ets'],12);			
    	$period_dif = countHours ($ord_sch['rel_etf'],$now_etf);   //原預計完成日與目前預計完成日差

  	}
    
//計算出生產後的預計完成日 end

		$q_str =  "UPDATE smpl_schedule SET rel_etf='".$now_etf."', pdt_qty = '".$pdt['qty']."' WHERE id = '".$ord_sch['id']."'";
		  
		$q_result = $sql->query($q_str);
		$this->add_ord_schedule($ord_num);
		
//重算生產線目前訂單後其他訂單排產 start
		$new_etf = $now_etf;
		$oth_sch = array();
		$q_str = "SELECT smpl_schedule.*  FROM smpl_schedule
							WHERE  smpl_schedule.line_id = '".$line_id."' AND rel_ets >= '".$ord_sch['rel_etf']."' ORDER BY rel_ets";

		$q_result = $sql->query($q_str);
		while($row = $sql->fetch($q_result)) 
		{		
			
			$period_dif = countHours ($row['rel_ets'],$new_etf);
			$row['now_ets'] = increceHoursInDate($row['rel_ets'],$period_dif);
			$row['now_etf'] = $this->increceSchdDay($row['rel_ets'],$row['rel_etf'],$row['now_ets']);
			$oth_sch[] = $row; 
			$new_etf =  $row['now_etf'];
		}
		for($i=0; $i<sizeof($oth_sch); $i++)
		{
			$q_str =  "UPDATE smpl_schedule SET rel_etf='".$oth_sch[$i]['now_etf']."', rel_ets='".$oth_sch[$i]['now_ets']."' WHERE id = '".$oth_sch[$i]['id']."'";
			$q_result = $sql->query($q_str);
			
			$this->add_ord_schedule($oth_sch[$i]['ord_num']);
			
		}
//重算生產線目前訂單後其他訂單排產 end		
		
	} // end func	


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->reload_schedule_finish($ord_num)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function reload_schedule_finish($ord_num) {

		$sql = $this->sql;
		
		$today= date('Y-m-d');
		$rtn = array();

		$q_str = "SELECT schedule.*  FROM schedule
							WHERE  ord_num='".$ord_num."' AND rel_etf > '".$today."'";
		
		$q_result = $sql->query($q_str);
		while($row = $sql->fetch($q_result)) 
		{
			$ord_sch[] = $row;
		}
		if(!isset($ord_sch)) return false;

		for($i=0; $i<sizeof($ord_sch); $i++)
		{
			$now_etf = date('Y-m-d');
			if($now_etf < $ord_sch[$i]['rel_ets'])$now_etf = $ord_sch[$i]['rel_ets'];
			$period_dif = countDays ($ord_sch[$i]['rel_etf'],$now_etf);

//計算出生產後的預計完成日 end

			$q_str =  "UPDATE schedule SET rel_etf='".$now_etf."' WHERE id = '".$ord_sch[$i]['id']."'";  

			$q_result = $sql->query($q_str);
			$this->add_ord_schedule($ord_num);			

//重算生產線目前訂單後其他訂單排產 start
			$new_etf = increceDaysInDate($now_etf,1);
			$oth_sch = array();
		
			$q_str = "SELECT schedule.*  FROM schedule
								WHERE  schedule.line_id = '".$ord_sch[$i]['line_id']."' AND 
											 rel_ets > '".$ord_sch[$i]['rel_etf']."' ORDER BY rel_ets";

			$q_result = $sql->query($q_str);
			while($row = $sql->fetch($q_result)) 
			{		
				$period_dif = countDays ($row['rel_ets'],$new_etf);
				$row['now_ets'] = increceDaysInDate($row['rel_ets'],$period_dif);
				$row['now_etf'] = $this->increceSchdDay($row['rel_ets'],$row['rel_etf'],$row['now_ets']);
				$oth_sch[] = $row; 
				$new_etf =  increceDaysInDate($row['now_etf'],1);
			}
			for($j=0; $j<sizeof($oth_sch); $j++)
			{
				$q_str =  "UPDATE schedule SET rel_etf='".$oth_sch[$j]['now_etf']."', rel_ets='".$oth_sch[$j]['now_ets']."' WHERE id = '".$oth_sch[$j]['id']."'";

				$q_result = $sql->query($q_str);
			
				$this->add_ord_schedule($oth_sch[$j]['ord_num']);
			
			}
//重算生產線目前訂單後其他訂單排產 end

		}

    

		
		


		
		
	} // end func	






#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->reload_schedule($parm) 外發排產
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function reload_del_schedule($line_id, $ord_num,$qty,$ie) {

		$sql = $this->sql;
		
		$today= date('Y-m-d');
		$rtn = array();

		$q_str = "SELECT pdt_saw_line.*  FROM pdt_saw_line
							WHERE  pdt_saw_line.id ='".$line_id."'";
							
		$q_result = $sql->query($q_str);
		if(!$line_det = $sql->fetch($q_result)) return false;


		$q_str = "SELECT schedule.*  FROM schedule
							WHERE  schedule.line_id ='".$line_id."' AND ord_num='".$ord_num."' AND pdt_qty >=".$qty."
							ORDER BY id DESC";

		$q_result = $sql->query($q_str);
		if(!$ord_sch = $sql->fetch($q_result)) return false;
		
		$pdt_qty = $ord_sch['pdt_qty'] - $qty;

		$q_str =  "UPDATE schedule SET pdt_qty = '".$pdt_qty."' WHERE id = '".$ord_sch['id']."'";

//計算出生產後的預計完成日 start
		$pdt['qty'] = $pdt_qty;
		$pdt['su']  = $pdt['qty'] * $ie;
		
		if(( $pdt['qty'] / $ord_sch['qty']) >= 0.95)
		{
			return true;
		}else{				 
		$ext_period =  ($ord_sch['su'] - $pdt['su'])/(9.6*$line_det['worker']*$line_det['success_rate']);
	 	$tmp = explode($ext_period,'.');
	 	if(isset($tmp[1]) && $tmp[1])$ext_period++;     
	 	if($ext_period < 0)$ext_period = 0;
	 	$rld_ets = $ord_sch['rel_ets'];
	 	if(date('Y-m-d') > $rld_ets)$rld_ets = date('Y-m-d');
//    $now_etf = increceDaysInDate($rld_ets,$ext_period);
    $now_etf = $this->increceDay($rld_ets,$ext_period);
  	}		
		
		
		
		
		
		
		

		
		if($now_etf < $ord_sch['rel_ets'])$now_etf = increceDaysInDate($ord_sch['rel_ets'],2);
    $period_dif = countDays ($ord_sch['rel_etf'],$now_etf);   //原預計完成日與目前預計完成日差
    
//計算出生產後的預計完成日 end

		$q_str =  "UPDATE schedule SET rel_etf='".$now_etf."', pdt_qty = '".$pdt['qty']."' WHERE id = '".$ord_sch['id']."'";
  
		$q_result = $sql->query($q_str);
		$this->add_ord_schedule($ord_num);

//重算生產線目前訂單後其他訂單排產 start
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
//			$row['now_etf'] = increceDaysInDate($row['rel_etf'],$period_dif);
			$row['now_etf'] = $this->increceSchdDay($row['rel_ets'],$row['rel_etf'],$row['now_ets']);
			$oth_sch[] = $row; 
			$new_etf =  increceDaysInDate($row['now_etf'],1);			
		}
		for($i=0; $i<sizeof($oth_sch); $i++)
		{
			$q_str =  "UPDATE schedule SET rel_etf='".$oth_sch[$i]['now_etf']."', rel_ets='".$oth_sch[$i]['now_ets']."' WHERE id = '".$oth_sch[$i]['id']."'";
			$q_result = $sql->query($q_str);
			
			$this->add_ord_schedule($oth_sch[$i]['ord_num']);
			
		}
//重算生產線目前訂單後其他訂單排產 end		
		
	} // end func	



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->order_get($ord_num) 取得訂單所有排產資料
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function order_get($ord_num) {

		$sql = $this->sql;
		
		$today= date('Y-m-d');
		$rtn = array();


		$q_str = "SELECT smpl_schedule.*, smpl_saw_line.line as line_name,  
										 smpl_saw_line.worker 
							FROM   smpl_schedule, smpl_saw_line
							WHERE  smpl_schedule.line_id = smpl_saw_line.id AND ord_num='".$ord_num."'";

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


		$q_str = "SELECT smpl_schedule.rel_etf  FROM smpl_schedule
							WHERE  fty='".$fty."' AND line_id = '".$line."' AND rel_etf >='".$today."' 
							ORDER BY rel_etf";
//echo $q_str;
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
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->order_get($ord_num) 取得訂單所有排產資料
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_last_date($fty) {

		$sql = $this->sql;
		
		$today= date('Y-m-d');
		$rtn = array();


		$q_str = "SELECT smpl_schedule.rel_etf  FROM smpl_schedule
							WHERE  fty='".$fty."' ORDER BY rel_etf DESC LIMIT 1";

		$q_result = $sql->query($q_str);
		
		if($ord_sch = $sql->fetch($q_result))
		{
			
			return $ord_sch[0];
		}

	
		
	} // end func	
	



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->order_get($ord_num) 取得訂單所有排產資料
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function count_pdt_qty($ord_num) {

		$sql = $this->sql;
   $pdt = array();
		$q_str = "SELECT sum(qty) as qty, line_id  FROM smpl_output 						  
							WHERE  ord_num='".$ord_num."'
							GROUP BY line_id, num";				

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
				$q_str = "SELECT smpl_schedule.*  FROM smpl_schedule
									WHERE  line_id = '".$pdt[$i]['line_id']."' AND ord_num='".$ord_num."' AND pdt_qty < qty AND pdt_qty < '".$pdt_qty."'";
				$q_result = $sql->query($q_str);
				if($row = $sql->fetch($q_result))
				{
					if($pdt[$i]['qty'] > $row['qty']){$qty = $row['qty'];}else{$qty = $pdt[$i]['qty'];}
					$q_str =  "UPDATE smpl_schedule SET  pdt_qty = '".$qty."' WHERE id = '".$row['id']."'";
  				$q_result = $sql->query($q_str);
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
		$q_str = "SELECT * FROM smpl_schedule WHERE id= '".$id."'";	
		$q_result = $sql->query($q_str);
		if(!$sch = $sql->fetch($q_result))  return false;


    $q_str = "SELECT * FROM smpl_schedule 
    					WHERE rel_etf <= '".$sch['rel_ets']."'  AND ord_num = '".$sch['ord_num']."' AND line_id = '".$sch['line_id']."'
    					ORDER BY rel_etf DESC LIMIT 1";
    					
		$q_result = $sql->query($q_str);
		if($pre = $sql->fetch($q_result)) 
		{
			$pre['new_etf'] = $sch['rel_etf'];
			$pre['qty'] += $sch['qty'];
			$pre['su'] += $sch['su'];
			$q_str =  "UPDATE smpl_schedule SET rel_etf='".$pre['new_etf']."', etf='".$pre['new_etf']."', qty ='".$pre['qty']."', su ='".$pre['su']."' 
								 WHERE id = '".$pre['id']."'";
			$q_result = $sql->query($q_str);
			$q_str = "DELETE FROM smpl_schedule WHERE id='".$sch['id']."'";		
			$q_result = $sql->query($q_str);
			return true;

		}
		
    $q_str = "SELECT * FROM smpl_schedule 
    					WHERE rel_ets >='".$sch['rel_etf']."'  AND ord_num = '".$sch['ord_num']."' AND line_id = '".$sch['line_id']."'
    					ORDER BY rel_ets";
		$q_result = $sql->query($q_str);
		if($pre = $sql->fetch($q_result)) 
		{
			$pre['new_ets'] = $sch['rel_ets'];
			$pre['qty'] += $sch['qty'];
			$pre['su'] += $sch['su'];
			$q_str =  "UPDATE smpl_schedule SET rel_ets='".$pre['new_ets']."', ets='".$pre['new_ets']."', qty ='".$pre['qty']."', su ='".$pre['su']."' 
								 WHERE id = '".$pre['id']."'";
			$q_result = $sql->query($q_str);
			$q_str = "DELETE FROM smpl_schedule WHERE id='".$sch['id']."'";		
			$q_result = $sql->query($q_str);
			return true;
		}
		
	} // end func





#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->contractor_schedule($parm) 外發排產
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function check_qty($ord_num,$qty) {

		$sql = $this->sql;

		$q_str = "SELECT smpl_ord.qty
							FROM   smpl_ord
							WHERE  smpl_ord.num ='".$ord_num."'";

		$q_result = $sql->query($q_str);
		$ord_rec = $sql->fetch($q_result);
		
		$q_str = "SELECT sum(smpl_schedule.qty)as qty 
							FROM smpl_schedule WHERE smpl_schedule.ord_num ='".$ord_num."' GROUP BY ord_num";

		$q_result = $sql->query($q_str);
		$sch_rec = $sql->fetch($q_result);
		if(!isset($sch_rec['qty']))$sch_rec['qty'] = 0;
		$sch_rec['qty'] += $qty;
		if($ord_rec['qty'] < $sch_rec['qty']) return false;
		


		return 1;
	} // end func	


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_ord_link($ord_num)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_ord_link($ord_num) {
		$ord_rec = '';
		$sql = $this->sql;
		$ord_num = substr($ord_num,0,-2);
		$q_str = "SELECT order_num
							FROM   s_order
							WHERE  smpl_ord like'%".$ord_num."%'";

		$q_result = $sql->query($q_str);
		while($row = $sql->fetch($q_result))
		{
			$ord_rec .= $row['order_num'].',';
		}
		$ord_rec = substr($ord_rec,0,-1);

		


		return $ord_rec;
	} // end func	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

} // end class


?>