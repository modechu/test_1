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
			for($i=1; $i<=$days; $i++)
			{
				$tmp_date = increceDaysInDate($str_date,$i);
			
				if(date('w',strtotime($tmp_date)) == 0) $hdy++;			
			}
			$days += $hdy;
			$fnl_date = increceDaysInDate($str_date,$days);
		}else{
			for($i=-1; $i>=$days; $i--)
			{
				$tmp_date = increceDaysInDate($str_date,$i);
			
				if(date('w',strtotime($tmp_date)) == 0) $hdy++;			
			}
			$days += $hdy;
			$fnl_date = increceDaysInDate($str_date,$days);		
		}

		return $fnl_date;
	} // end func	
	

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->increceDay($str_date,$days)
#		重算排產日期(加入假日)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function increceSchdDay($org_ets,$org_etf,$new_ets) {
		$hdy = 0;
		$p_day = countDays ($org_ets,$org_etf);
		$str_wk = date('w',strtotime($org_ets));
		$end_wk = date('w',strtotime($org_etf));
		$hdy = $p_day / 7;
		$tmp = explode('.',$hdy);
		$hdy = $tmp[0];
		$md = $p_day % 7;

		if(($md + $str_wk) > 7 || $str_wk > $end_wk) $hdy++;
	
/*		
		for($i=1; $i<=$p_day; $i++)
		{
			$tmp_date = increceDaysInDate($org_etf,$i);			
			if(date('w',strtotime($tmp_date)) == 0) $hdy++;			
		}
*/
		
		$p_day -= $hdy;  //計算實際排產日數

		$str_wk = date('w',strtotime($new_ets));
		$end_wk = date('w',strtotime(increceDaysInDate($new_ets,$p_day)));
		$hdy = $p_day / 7;
		$tmp = explode('.',$hdy);
		$hdy = $tmp[0];
		$md = $p_day % 7;
		if(($md + $str_wk) > 7 || $str_wk > $end_wk) $hdy++;
/*		
		$hdy = 0;
		for($i=1; $i<=$p_day; $i++)
		{
			$tmp_date = increceDaysInDate($new_ets,$i);			
			if(date('w',strtotime($tmp_date)) == 0) $hdy++;			
		}		
		
*/		
		$p_day += $hdy;
		
		$fnl_date = increceDaysInDate($new_ets,$p_day);
		$p_day = countDays ($new_ets,$fnl_date);
//	echo $fnl_date.'--->'.$hdy.'--->'.$p_day."<BR>";			
		return $fnl_date;
	} // end func	
	
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, season=0)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_order($fty,$limit_date='') {

		$sql = $this->sql;
		
		$today= date('Y-m-d');
		$rtn = array();
		$q_str = "SELECT s_order.order_num, s_order.su, pdtion.ext_su  , status, mat_eta, m_acc_eta,
										 s_order.qty, pdtion.ext_qty, s_order.style, s_order.etd
							FROM   s_order,pdtion 
							WHERE  s_order.order_num = pdtion.order_num AND s_order.status >= 4 AND 
										 s_order.status <= 8 AND s_order.status <> 5 AND										 
										 ext_qty < qty AND s_order.factory ='".$fty."' AND s_order.etd > '2009-12-31'";
		if($limit_date) $q_str .= " AND etd < '".$limit_date."'";
		$q_str .= "	ORDER BY s_order.etd";

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
	function auto_schedule($order,$sch_period,$fty) {

		$sql = $this->sql;
		
		$today= date('Y-m-d');
		$rtn = array();
		$q_str = "SELECT s_order.order_num, s_order.su, s_order.qty, pdtion.ext_su , pdtion.ext_qty, 
										 status, mat_eta, m_acc_eta, style, s_order.apv_date,
											pdtion.ext_period
							FROM   s_order,pdtion 
							WHERE  s_order.order_num = pdtion.order_num AND
										 s_order.order_num ='".$order."'
							ORDER BY pdtion.mat_eta, m_acc_eta";
//echo $q_str."<BR>";
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
					$rtn_mk = 1;
					$tmp_date = $row['etf'];
					$ets =increceDaysInDate($row['etf'],1);
				}
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
	 	 $ext_period = ($ord_rec['su']-$ord_rec['ext_su']) /( 9.6 * $rtn['worker'] *$rtn['success_rate']);
	 	 $tmp = explode($ext_period,'.');
	 	 if(isset($tmp[1]) && $tmp[1])$ext_period++;
     if( $ext_period <= 1) $ext_period = 2;
     $etf = $this->increceDay($ets,$ext_period);





					# 加入資料庫
		$q_str = "INSERT INTO schedule (line_id, ord_num,su, qty, ets,etf,rel_ets,rel_etf,fty,open_date,open_user) 
							VALUES ('".$rtn['id']."','"
												.$order."','"
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




		return $season_id;
	} // end func	
	
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, season=0)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_by_mm($fty,$sch_date,$fsh_date) {

		$sql = $this->sql;
		$finish_rate = $_SESSION['finish_rate'];
		
		$today= date('Y-m-d');
		$rtn = array();
		$str_date = $sch_date."-01";
		
		$q_str = "SELECT pdt_saw_line.*, (pdt_saw_line.success_rate*9.6) as day_avg FROM pdt_saw_line
							WHERE  pdt_saw_line.fty ='".$fty."' AND worker > 0 AND del_mk = 0 ORDER BY line";							
							

		$q_result = $sql->query($q_str);		
		while($row = $sql->fetch($q_result))
		{
			$tmp = explode(' ',$row['line']);

			if(isset($tmp[1]))$row['line'] = $tmp[0]."+".$tmp[1];
			$rtn[] = $row;
		}		
		
		
		for($i=0; $i<sizeof($rtn); $i++)
		{
			$q_str = "SELECT schedule.*, s_order.style, s_order.smpl_apv, s_order.status as ord_status ,
											s_order.etd, s_order.id as ord_id, pdtion.mat_shp, pdtion.m_acc_shp,
											s_order.style_num, s_order.qty as ord_qty, s_order.ie1, s_order.m_status
								FROM schedule, s_order, pdtion
								WHERE  s_order.order_num = schedule.ord_num AND schedule.ord_num = pdtion.order_num AND
											 s_order.order_num = pdtion.order_num AND
											 schedule.line_id ='".$rtn[$i]['id']."' AND schedule.rel_etf >= '".$str_date."' AND
											 ((schedule.rel_ets <> '0000-00-00' AND rel_ets <='".$fsh_date."') OR 
											 (schedule.rel_ets = '0000-00-00' AND schedule.ets <='".$fsh_date."'))								
								ORDER BY rel_etf";							
//	echo $q_str."<BR>";							
			$q_result = $sql->query($q_str);		
			
			$j=$str_date;
			while($row = $sql->fetch($q_result))
			{
//				if(isset($f_date) && $f_date == $row['rel_ets']) $row['rel_ets'] =increceDaysInDate($row['rel_ets'],1);
				$tmp = explode('-',$row['ord_num']);
				$row['ord_init'] = $tmp[1];
				if($row['rel_ets'] != '0000-00-00'){$s_date = $row['rel_ets'];}else{$s_date = $row['ets'];}
//				if($row['rel_etf'] != '0000-00-00'){$f_date = $row['rel_etf'];}else{$f_date = $row['etf'];}
				
				if($j == $str_date && $s_date > $str_date)
				{
					$non_row['day_range'] = dayCount($j,$s_date);
					$non_row['flag'] = 0;
					$non_row['flag_color'] = "#eeeeee";
					$rtn[$i]['sch_rec'][] = $non_row;
				}				

					if($j != $s_date && $j != $str_date)
					{
						$non_row['day_range'] = dayCount($j,$s_date);
						
						$non_row['flag'] = 0;
						$non_row['flag_color'] = "#eeeeee";
						if($non_row['day_range'] )$rtn[$i]['sch_rec'][] = $non_row;
					}
					
					if($s_date < $str_date)
					{
						
						$row['day_range'] = dayCount($str_date,$row['rel_etf'])+1;
						
					}else if($row['rel_etf'] > $fsh_date){

						$row['day_range'] = dayCount($s_date,$fsh_date)+1;
					}else{
						
						
						$row['day_range'] = dayCount($s_date,$row['rel_etf'])+1;
//						echo $s_date." ===> ".$row['rel_etf']." ===> ".$row['day_range']."<BR>";
					}				
				
					if($row['rel_ets'] < $row['ets'] && $row['rel_ets'] <> '0000-00-00' ){$ets = $row['rel_ets'];}else{$ets = $row['ets'];}
					
					
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
					}else if(group_order_style($row['style']) != $rtn[$i]['style'] && $rtn[$i]['style'] != 'mut.'){
						$row['flag'] = 2;
						$row['flag_color'] = "#870BFB"; //不同style黃色
					}else{
						$row['flag'] = 1;
						$row['flag_color'] = "#FFFFFF";
					}				

				 if($row['smpl_apv'] == '0000-00-00'){		//無樣本 			
						$row['flag'] = 7;
						//$row['flag_color'] = "#FF6F43";		//無樣本 橘色			
				 }				
				$rtn[$i]['sch_rec'][] = $row;
				$j = increceDaysInDate($row['rel_etf'],1);
			}			
		}

		
		return $rtn;
	} // end func	
	
	
	
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
//	echo $q_str."<BR>";							
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

		  		$period_dif = countDays ($oth_sch[$i]['rel_ets'],$new_etf);   //計算插單後需要移動的日期數		  		
			 		$oth_sch[$i]['new_ets'] = increceDaysInDate($oth_sch[$i]['rel_ets'],$period_dif);

		 		  $sp_period = $oth_sch[$i]['su'] /( 9.6 * $rtn[0]['worker'] * $rtn[0]['success_rate']);
		 			$tmp = explode('.',$sp_period);
 					if(isset($tmp[1]) && $tmp[1])$sp_period++;
 					if ( $sp_period <= 1) $sp_period = 2;
		 		  $oth_sch[$i]['new_etf'] = $this->increceDay($oth_sch[$i]['new_ets'],$sp_period);			



//			 		$oth_sch[$i]['new_etf'] = $this->increceSchdDay($oth_sch[$i]['rel_ets'],$oth_sch[$i]['rel_etf'],$oth_sch[$i]['new_ets']);
					$q_str =  "UPDATE schedule SET rel_etf='".$oth_sch[$i]['new_etf']."', rel_ets='".$oth_sch[$i]['new_ets']."', etf='".$oth_sch[$i]['new_etf']."', ets='".$oth_sch[$i]['new_ets']."'  WHERE id = '".$oth_sch[$i]['id']."'";
					$q_result = $sql->query($q_str);
					$new_etf =  increceDaysInDate($oth_sch[$i]['new_etf'],1);
		  	 
		  }
		}
		
//組合訂單
	$this->group_schedule($new_id);
		
//重算生產線目前訂單後其他訂單排產 end		
		
		
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
 		  $rtn[0]['sp_period'] = $rtn[0]['sp_su'] /( 9.6 * $rtn[0]['worker'] * $rtn[0]['success_rate']);
 			$tmp = explode('.',$rtn[0]['sp_period']);
 			if(isset($tmp[1]) && $tmp[1])$rtn[0]['sp_period']++;
		  if ( $rtn[0]['sp_period'] <= 1)$rtn[0]['sp_period'] = 2;
 //= increceDaysInDate($ets,$rtn[0]['sp_period']);			
			$rtn[0]['new_etf'] = $this->increceDay($ets,$rtn[0]['sp_period']);
					# 加入資料庫
		$q_str = "INSERT INTO schedule (line_id, ord_num,des,su, qty, ets,etf,rel_ets,rel_etf,fty,open_date,open_user) 
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


		 		  $sp_period = $oth_sch[$i]['su'] /( 9.6 * $rtn[0]['worker'] * $rtn[0]['success_rate']);
		 			$tmp = explode('.',$sp_period);
 					if(isset($tmp[1]) && $tmp[1])$sp_period++;
 					if ( $sp_period <= 1) $sp_period = 2;
		 		  $oth_sch[$i]['new_etf'] = $this->increceDay($oth_sch[$i]['new_ets'],$sp_period);			


//			 		$oth_sch[$i]['new_etf'] = $this->increceSchdDay($oth_sch[$i]['rel_ets'],$oth_sch[$i]['rel_etf'],$oth_sch[$i]['new_ets']);
//			 		$oth_sch[$i]['new_etf'] = increceDaysInDate($oth_sch[$i]['rel_etf'],$period_dif);
					$q_str =  "UPDATE schedule SET rel_etf='".$oth_sch[$i]['new_etf']."', rel_ets='".$oth_sch[$i]['new_ets']."', etf='".$oth_sch[$i]['new_etf']."', ets='".$oth_sch[$i]['new_ets']."'  WHERE id = '".$oth_sch[$i]['id']."'";
					$q_result = $sql->query($q_str);
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

			$this->del_schedule($parm['org_id'],0,$parm['ord_num']);
		
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

		 		  $sp_period = $oth_sch[$i]['su'] /( 9.6 * $rtn['worker'] *$rtn['success_rate']);
		 			$tmp = explode('.',$sp_period);
 					if(isset($tmp[1]) && $tmp[1])$sp_period++;
 					if ( $sp_period <= 1) $sp_period = 2;
		 		  $oth_sch[$i]['new_etf'] = $this->increceDay($oth_sch[$i]['new_ets'],$sp_period);			


//			 		$oth_sch[$i]['new_etf'] = $this->increceSchdDay($oth_sch[$i]['rel_ets'],$oth_sch[$i]['rel_etf'],$oth_sch[$i]['new_ets']);
					$q_str =  "UPDATE schedule SET rel_etf='".$oth_sch[$i]['new_etf']."', rel_ets='".$oth_sch[$i]['new_ets']."', etf='".$oth_sch[$i]['new_etf']."', ets='".$oth_sch[$i]['new_ets']."'  WHERE id = '".$oth_sch[$i]['id']."'";
					$q_result = $sql->query($q_str);
					$new_etf =  increceDaysInDate($oth_sch[$i]['new_etf'],1);
		  	 
		  }
		}
//重算生產線目前訂單後其他訂單排產 end		

//組合訂單
	$this->group_schedule($new_id);
		
		return $new_id;
	} // end func	




#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del_schedule($id) 產除生產線排產
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del_schedule($id,$qty,$ord_num) {

		$sql = $this->sql;
		
		$today= date('Y-m-d');
		$rtn = array();


		$q_str = "SELECT s_order.order_num, s_order.su, pdtion.ext_su  , status, mat_eta, m_acc_eta, style,
											pdtion.ext_period, s_order.ie1, s_order.apv_date
							FROM   s_order,pdtion 
							WHERE  s_order.order_num = pdtion.order_num AND
										 s_order.order_num ='".$ord_num."'
							ORDER BY pdtion.mat_eta, m_acc_eta";

		$q_result = $sql->query($q_str);
		$ord_rec = $sql->fetch($q_result);
		$ord_sch_date = $ord_rec['apv_date'];
		
		$del_su = NUMBER_FORMAT(($qty * $ord_rec['ie1']),'0','.','');



		$q_str = "SELECT pdt_saw_line.*, schedule.rel_etf, schedule.rel_ets, schedule.ets , 
										 schedule.id as sch_id, schedule.su, schedule.qty
							FROM pdt_saw_line, schedule
							WHERE pdt_saw_line.id = schedule.line_id  AND schedule.id ='".$id."'"	;
							
			$q_result = $sql->query($q_str);	
			$row = $sql->fetch($q_result);

			$row['su'] -= $del_su;
			$row['qty'] -= $qty;
			$new_rel_ets = $row['rel_ets'];
			$line_id = $row['id'];
			$org_rtn = $row;
			if($row['su'] > 0 && $del_su)
			{
				
				if($row['success_rate'] == 0)$row['success_rate'] = 1;
 		 		$row['sp_period'] = $row['su'] /( 9.6 * $row['worker'] * $row['success_rate']);
 				$tmp = explode('.',$row['sp_period']);
 				if(isset($tmp[1]) && $tmp[1])$row['sp_period']++;
 				if($row['sp_period'] <= 1) $row['sp_period'] = 2;
 		  	$row['new_rel_etf'] = $row['new_etf'] = $this->increceDay($row['ets'],$row['sp_period']);	
 		  	if($row['rel_ets'] <> '0000-00-00')$row['new_rel_etf'] = $this->increceDay($row['rel_ets'],$row['sp_period']);		
				
				$q_str = "UPDATE schedule SET 
									su='".$row['su']."',
									qty='".$row['qty']."',
									etf='".$row['new_etf']."',
									rel_etf='".$row['new_rel_etf']."'
									WHERE id='".$id."'";
				$q_result = $sql->query($q_str);
				$new_rel_ets = increceDaysInDate($row['new_rel_etf'],1);
			}else{
			
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
		$q_result = $sql->query($q_str);
		while($row = $sql->fetch($q_result)) 
		{
			$oth_sch[] = $row; 
		}
		if(sizeof($oth_sch) > 0)
		{
		 	for($i=0; $i<sizeof($oth_sch); $i++)  //從排插單後訂單
		 	{

		  		$period_dif = countDays ($oth_sch[$i]['rel_ets'],$new_rel_ets);   //計算插單後需要移動的日期數
			 		$oth_sch[$i]['new_ets'] = increceDaysInDate($oth_sch[$i]['rel_ets'],$period_dif);

		 		  $sp_period = $oth_sch[$i]['su'] /( 9.6 * $org_rtn['worker'] *$org_rtn['success_rate']);
		 			$tmp = explode('.',$sp_period);
					if(isset($tmp[1]) && $tmp[1])$sp_period++;
 					if ( $sp_period <= 1) $sp_period = 2;
		 		  $oth_sch[$i]['new_etf'] = $this->increceDay($oth_sch[$i]['new_ets'],$sp_period);			


//			 		$oth_sch[$i]['new_etf'] = $this->increceSchdDay($oth_sch[$i]['rel_ets'],$oth_sch[$i]['rel_etf'],$oth_sch[$i]['new_ets']);
					$q_str =  "UPDATE schedule SET rel_etf='".$oth_sch[$i]['new_etf']."', rel_ets='".$oth_sch[$i]['new_ets']."', etf='".$oth_sch[$i]['new_etf']."', ets='".$oth_sch[$i]['new_ets']."'  WHERE id = '".$oth_sch[$i]['id']."'";

					$q_result = $sql->query($q_str);
					$new_rel_ets =  increceDaysInDate($oth_sch[$i]['new_etf'],1);
					
	  	 
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
			
		$q_str = "SELECT max(rel_etf) as etf, min(rel_ets) as ets, sum(su) as su, sum(qty) as qty	
							FROM   schedule
							WHERE  schedule.ord_num ='".$ord_num."'
							GROUP BY ord_num"	;
echo $q_str."<BR>";
			$q_result = $sql->query($q_str);	
			if($row = $sql->fetch($q_result))
			{
			//訂單加入ets,etf
				$q_str = "UPDATE pdtion SET ets ='".$row['ets']."', etf ='".$row['etf']."' WHERE order_num='".$ord_num."'";
				$q_result = $sql->query($q_str);
echo $q_str."<BR>";
			//訂單加入己排產數量
				$q_str = "UPDATE pdtion SET ext_su ='".$row['su']."', ext_qty ='".$row['qty']."'  WHERE order_num='".$ord_num."'";
				$q_result = $sql->query($q_str);
echo $q_str."<BR>";
			//訂單加入預計生產所需日數
				$day_range = dayCount($row['ets'],$row['etf']);
				$q_str = "UPDATE pdtion SET ext_period ='".$day_range."'  WHERE order_num='".$ord_num."'";
				$q_result = $sql->query($q_str);
echo $q_str."<BR>";
			}else{
			//訂單加入ets,etf
				$q_str = "UPDATE pdtion SET ets = NULL, etf = NULL, ext_su ='0', ext_period ='0', ext_qty = 0 WHERE order_num='".$ord_num."'";

				$q_result = $sql->query($q_str);			
echo $q_str."<BR>";
			}



//紀錄sub_con	
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
				$sub_con = 1;
		}else{
				$q_str = "UPDATE pdtion SET sub_con = 0 WHERE order_num='".$ord_num."'";
				$q_result = $sql->query($q_str);	
				$sub_con = 0;					
		}



//紀錄in house	
		$q_str = "SELECT schedule.id
							FROM   schedule, pdt_saw_line
							WHERE  schedule.line_id = pdt_saw_line.id AND schedule.ord_num ='".$ord_num."' AND
										 pdt_saw_line.sc = 0
							GROUP BY ord_num"	;

		$q_result = $sql->query($q_str);	
		if($row = $sql->fetch($q_result))
		{
				$in_house = 1;
		}else{
				$in_house = 0;			
		}
		
		$q_str = "UPDATE pdtion SET in_house ='".$in_house."', sub_con = '".$sub_con."' WHERE order_num='".$ord_num."'";
		$q_result = $sql->query($q_str);			
		
echo $q_str."<BR>";			


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
echo $q_str."<BR>";			
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
				$F = $GLOBALS['capaci']->delete_su($ord_rec['factory'], $yy, $mm,'schedule', $mm_su);
			}
			

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
	function reload_schedule($line_id, $ord_num) {

		$sql = $this->sql;
		
		$today= date('Y-m-d');
		$rtn = array();

		$q_str = "SELECT pdt_saw_line.*  FROM pdt_saw_line
							WHERE  pdt_saw_line.id ='".$line_id."' AND sc = 0";
		$q_result = $sql->query($q_str);
		if(!$line_det = $sql->fetch($q_result)) return false;
		

		$q_str = "SELECT schedule.*  FROM schedule
							WHERE  schedule.line_id ='".$line_id."' AND ord_num='".$ord_num."' AND pdt_qty < qty";
		$q_result = $sql->query($q_str);
		if(!$ord_sch = $sql->fetch($q_result)) return false;

		$q_str = "SELECT schedule.*  FROM schedule
							WHERE  schedule.line_id ='".$line_id."' AND ord_num='".$ord_num."' AND pdt_qty >= qty";
		$q_result = $sql->query($q_str);
		$oth_sch_su = $oth_sch_qty = 0;
		while($rows = $sql->fetch($q_result))
		{
			$oth_sch_su += $rows['su'];
			$oth_sch_qty += $rows['qty'];
		}
		

		$q_str = "SELECT sum(qty) as qty, sum(su) as su  FROM saw_out_put 						  
							WHERE  saw_out_put.line_id ='".$line_id."' AND ord_num='".$ord_num."'
							GROUP BY line_id, ord_num";				
		$q_result = $sql->query($q_str);
		if(!$pdt = $sql->fetch($q_result)) return false;

//計算出生產後的預計完成日 start
		$pdt['su'] = $pdt['su'] - $oth_sch_su;
		$pdt['qty'] = $pdt['qty'] - $oth_sch_qty;
		if($ord_sch['status'] > 0)	return false;
		if(( $pdt['qty'] / $ord_sch['qty']) >= 0.95)
		{
			$q_str =  "UPDATE schedule SET status='2' WHERE id = '".$ord_sch['id']."'";	
			$q_result = $sql->query($q_str);
			$now_etf = date('Y-m-d');
			if($now_etf < $ord_sch['rel_ets'])$now_etf = $ord_sch['rel_ets'];
			$period_dif = countDays ($ord_sch['rel_etf'],$now_etf);
		}else{				 
			$ext_period =  ($ord_sch['su'] - $pdt['su'])/(9.6*$line_det['worker']*$line_det['success_rate']);

	 		$tmp = explode($ext_period,'.');
	 		if(isset($tmp[1]) && $tmp[1])$ext_period++;     
	 		if($ext_period < 0)$ext_period = 0;
	 		$rld_ets = $ord_sch['rel_ets'];
	 		if(date('Y-m-d') > $rld_ets)$rld_ets = date('Y-m-d');
//    	$now_etf = increceDaysInDate($rld_ets,$ext_period);		
			$now_etf = $this->increceDay($rld_ets,$ext_period);		
			if($now_etf < $ord_sch['rel_ets'])$now_etf = increceDaysInDate($ord_sch['rel_ets'],1);			
    	$period_dif = countDays ($ord_sch['rel_etf'],$now_etf);   //原預計完成日與目前預計完成日差
  	}
    
//計算出生產後的預計完成日 end

		$q_str =  "UPDATE schedule SET rel_etf='".$now_etf."', pdt_qty = '".$pdt['qty']."' WHERE id = '".$ord_sch['id']."'";

		$q_result = $sql->query($q_str);
		$this->add_ord_schedule($ord_num);
		
//重算生產線目前訂單後其他訂單排產 start
		$new_etf = increceDaysInDate($now_etf,1);
		$oth_sch = array();
		$q_str = "SELECT schedule.*  FROM schedule
							WHERE  schedule.line_id = '".$line_id."' AND rel_ets > '".$ord_sch['rel_etf']."' ORDER BY rel_ets";
		
		$q_result = $sql->query($q_str);
		$i = 0;
		while($row = $sql->fetch($q_result)) 
		{		
			$period_dif = countDays ($row['rel_ets'],$new_etf);
			$row['now_ets'] = increceDaysInDate($row['rel_ets'],$period_dif);

			if($i < 3)
			{
 		  	$sp_period = $row['su'] /(9.6*$line_det['worker']*$line_det['success_rate']);

 				$tmp = explode('.',$sp_period);
				if(isset($tmp[1]) && $tmp[1])$sp_period++;
				if ( $sp_period <= 1) $sp_period = 2;
 		  	$row['now_etf'] = $this->increceDay($row['now_ets'],$sp_period);			
 		  }else{
 		  	$row['now_etf'] = $this->increceSchdDay($row['rel_ets'],$row['rel_etf'],$row['now_ets']);
 			}

			$i++;

//			$row['now_etf'] = increceDaysInDate($row['rel_etf'],$period_dif);
//			$row['now_etf'] = $this->increceSchdDay($row['rel_ets'],$row['rel_etf'],$row['now_ets']);
			$oth_sch[] = $row; 
			$new_etf =  increceDaysInDate($row['now_etf'],1);
		}
		for($i=0; $i<sizeof($oth_sch); $i++)
		{
			$q_str =  "UPDATE schedule SET rel_etf='".$oth_sch[$i]['now_etf']."', rel_ets='".$oth_sch[$i]['now_ets']."' WHERE id = '".$oth_sch[$i]['id']."'";
			$q_result = $sql->query($q_str);
			
			$this->add_ord_schedule($oth_sch[$i]['ord_num']);
			
	 		if($this->check_sch_finish($oth_sch[$i]['ord_num']))
 			{
 				$this->add_capacity($oth_sch[$i]['ord_num']);
 			}		
			
			
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
							WHERE  ord_num='".$ord_num."' AND rel_etf > '".$today."' AND status = 0";
	
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

			$q_str =  "UPDATE schedule SET rel_etf='".$now_etf."', status = 2 WHERE id = '".$ord_sch[$i]['id']."'";  

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

		$q_str =  "UPDATE schedule SET rel_etf='".$now_etf."', pdt_qty = '".$pdt['qty']."', status = 0 WHERE id = '".$ord_sch['id']."'";
  
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


		$q_str = "SELECT schedule.*, pdt_saw_line.line as line_name, pdt_saw_line.style, pdt_saw_line.worker 
							FROM schedule, pdt_saw_line
							WHERE  schedule.line_id = pdt_saw_line.id AND ord_num='".$ord_num."'";

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
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->order_get($ord_num) 取得訂單所有排產資料
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_last_date($fty) {

		$sql = $this->sql;
		
		$today= date('Y-m-d');
		$rtn = array();


		$q_str = "SELECT schedule.rel_etf  FROM schedule
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
		$q_str = "SELECT sum(qty) as qty, sum(su) as su, line_id  FROM saw_out_put 						  
							WHERE  ord_num='".$ord_num."'
							GROUP BY line_id, ord_num";				

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
				$q_result = $sql->query($q_str);
				if($row = $sql->fetch($q_result))
				{
					if($pdt[$i]['qty'] > $row['qty']){$qty = $row['qty'];}else{$qty = $pdt[$i]['qty'];}
					$q_str =  "UPDATE schedule SET  pdt_qty = '".$qty."' WHERE id = '".$row['id']."'";
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
		$q_str = "SELECT * FROM schedule WHERE id= '".$id."'";	
		$q_result = $sql->query($q_str);
		if(!$sch = $sql->fetch($q_result))  return false;


    $q_str = "SELECT * FROM schedule 
    					WHERE rel_etf < '".$sch['rel_ets']."'  AND ord_num = '".$sch['ord_num']."' AND line_id = '".$sch['line_id']."'
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
    					WHERE rel_ets >'".$sch['rel_etf']."'  AND ord_num = '".$sch['ord_num']."' AND line_id = '".$sch['line_id']."'
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
#			mode = 3 :  已有產出後的訂單搜尋 ( status > 7 )
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function pdt_ord_search($mode=0, $where_str='', $limit_entries=0) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv
		$ln_id = $_SESSION['sch_ln_id'];
		$limit_day = increceDaysInDate($GLOBALS['TODAY'],-360);
	
		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT s_order.* , pdtion.*, cust_init_name as cust_iname, s_order.id as s_id 
								 FROM s_order, pdtion, cust, schedule";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("s_order.id DESC");
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
	
if ($mode==1 || $mode==2 || $mode==3 || $mode==4 || $mode==5){
		$mesg = '';
		if ($str = strtoupper($argv['PHP_ref']) )  { 
			$srh->add_where_condition("s_order.ref LIKE '%$str%'", "PHP_ref",$str); 
			$mesg.= "  ref# : [ $str ]. ";
			}
		
		if ($str = $argv['PHP_cust'] )  { 
			$srh->add_where_condition("s_order.cust = '$str'", "PHP_cust",$str); 
			$mesg.= "  Cust. = [ $str ]. ";
			}
		if ($str = $argv['PHP_order_num'] )  { 
			$srh->add_where_condition("s_order.order_num LIKE '%$str%'", "PHP_order_num",$str); 
			$mesg.= "  Order# : [ $str ]. ";
			}
		if ($str = $argv['PHP_factory'] )  { 
			$srh->add_where_condition("s_order.factory = '$str'", "PHP_factory",$str); 
			$mesg.= "  FTY = [ $str ]. ";
			}
		if ($mesg)
		{
			$msg = "Search ".$mesg;
			$this->msg->add($msg);
		}
}		

if ($mode==4){   // 當要搜尋的 訂單是 排產確認後的時
	$where_strs ='';
		if ($str = $argv['PHP_ship'] ) $where_strs = $where_strs." || s_order.status = 12";
		if ($str = $argv['PHP_finish'] ) $where_strs = $where_strs." || s_order.status = 10";

		$srh->add_where_condition("s_order.status = 7 || s_order.status = 8 ".$where_strs, "","","");   
		$srh->add_where_condition("finish >= '".$limit_day."' or finish IS NULL or finish ='0000-00-00'", "","","");  		 
		$srh->add_where_condition("schedule.line_id = '".$ln_id."'");
}	

		$srh->add_where_condition("s_order.order_num = pdtion.order_num", "",$str,"");   // 關聯式察尋 必然要加
		$srh->add_where_condition("s_order.cust = cust.cust_s_name AND s_order.cust_ver = cust.ver");   // 關聯式察尋 必然要加
		$srh->add_where_condition("s_order.order_num = schedule.ord_num");
		$srh->add_where_condition("pdtion.order_num = schedule.ord_num");
				
		
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
	function check_qty($ord_num,$qty) {

		$sql = $this->sql;

		$q_str = "SELECT s_order.qty
							FROM   s_order
							WHERE  s_order.order_num ='".$ord_num."'";

		$q_result = $sql->query($q_str);
		$ord_rec = $sql->fetch($q_result);
		
		$q_str = "SELECT sum(schedule.qty)as qty FROM schedule WHERE schedule.ord_num ='".$ord_num."' GROUP BY ord_num";
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
		$finish_rate = $_SESSION['finish_rate'];
		
		$today= date('Y-m-d');
		$sch_date = increceDaysInDate($today,-5);
		$rtn = array();
		//$str_date = $sch_date."-01";
		
		$q_str = "SELECT pdt_saw_line.*, (pdt_saw_line.success_rate*9.6) as day_avg FROM pdt_saw_line
							WHERE  pdt_saw_line.fty ='".$fty."' AND worker > 0 AND del_mk = 0 ORDER BY line";							
							

		$q_result = $sql->query($q_str);		
		while($row = $sql->fetch($q_result))
		{
			$tmp = explode(' ',$row['line']);

			if(isset($tmp[1]))$row['line'] = $tmp[0]."+".$tmp[1];
			$rtn[] = $row;
		}		
		
		$ord_rtn = array();
		for($i=0; $i<sizeof($rtn); $i++)
		{
			$q_str = "SELECT schedule.*, s_order.style, s_order.status as ord_status ,
											s_order.etd, s_order.id as ord_id, 
											s_order.qty as ord_qty, s_order.ie1
								FROM schedule, s_order
								WHERE  s_order.order_num = schedule.ord_num AND schedule.pdt_qty > 0 AND
											 (schedule.pdt_qty /schedule.qty) < 0.95 AND schedule.rel_etf > '".$sch_date."' AND
											 schedule.line_id ='".$rtn[$i]['id']."' 								
								ORDER BY rel_etf DESC 
								LIMIT 1";							
//	echo $q_str."<BR>";							
			$q_result = $sql->query($q_str);		
			
		//	$j=$str_date;
			while($row = $sql->fetch($q_result))
			{					
				$this->reload_schedule($rtn[$i]['id'],$row['ord_num']);				
			}			
		}

		
		return $rtn;
	} // end func	
	



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

} // end class


?>