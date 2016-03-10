<?php 

#++++++++++++++++++++++ CAPACI  class   +++++++++++++++++++++++++++++++++++
#	->init($sql)							啟始 (使用 Msg_handle(); 先聯上 sql)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class EXPENSE {
		
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
#	->add($parm)		加入新 capacity 記錄
#						傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add($parm) {
					
		$this->msg = new MSG_HANDLE();
		$sql = $this->sql;

					# 加入資料庫
		$q_str = "INSERT INTO expense (
									yy,						mm,					type,					dept,cost,
									salary,				rent,				stationery,		travel,
									freight,			postal,			fix,					ad,									
									water,				insurance,	communicat,		donate,
									taxes,				bed_dabt,		depreciation,	other_1,
									book,					food,				welfare,			rd,
									teach,				medicine,		commission,		labor_1,	
									other_2,			other_3,		customs,			sample,
									bank,					pack,				labor_2,			health,
									material,			share,			prize,				retire_1,
									reach_cost,		build,			retire_2,			cloth_fix,
									work_cloth,		manage,			safety
									) VALUES('".
									$parm['yy']."','".		 			$parm['mm']."','".		 		$parm['type']."','".					$parm['dept']."','".$parm['cost']."','".
									$parm['salary']."','". 			$parm['rent']."','".	 		$parm['sationery']."','".			$parm['travel']."','".
									$parm['freight']."','".			$parm['postal']."','".		$parm['fix']."','".						$parm['ad']."','".
									$parm['water']."','". 			$parm['insurance']."','".	$parm['communicat']."','".		$parm['donate']."','".
									$parm['taxes']."','". 			$parm['bed_dabt']."','".	$parm['depreciation']."','".	$parm['other_1']."','".
									$parm['book']."','". 				$parm['food']."','".			$parm['welfare']."','".				$parm['rd']."','".
									$parm['teach']."','". 			$parm['medicine']."','".	$parm['commission']."','".		$parm['labor_1']."','".
									$parm['other_2']."','". 		$parm['other_3']."','".		$parm['customs']."','".				$parm['sample']."','".
									$parm['bank']."','".				$parm['pack']."','".			$parm['labor_2']."','".				$parm['health']."','".
									$parm['material']."','".		$parm['share']."','".			$parm['prize']."','".					$parm['retire_1']."','".
									$parm['reach_cost']."','".	$parm['build']."','".			$parm['retire_2']."','".			$parm['cloth_fix']."','".
									$parm['work_cloth']."','".	$parm['manage']."','".		$parm['safety'].
									"')";
//echo $q_str."<BR>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot append year [".$parm['yy']."] record.");
			$this->msg->merge($sql->msg);
			return false;    
		}
					
		return true;

	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($yy,$mm)
#			搜尋 某月份預算花費  資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get($yy,$mm,$dept) {

		$sql = $this->sql;
		$txt = array('cost','salary','rent','stationery','travel','freight','postal','fix','ad','water','insurance',
								 'communicat','donate','taxes','bed_dabt','depreciation','other_1','book','food',
								 'welfare','rd','teach','medicine','commission','labor_1','other_2','other_3','customs',
								 'sample','bank','pack','labor_2','health','material','share','prize','retire_1',
								 'reach_cost','build','retire_2','cloth_fix','work_cloth','manage','safety');	

		$q_str = "SELECT *	 FROM expense	 WHERE yy = '".$yy."' AND mm = '".$mm."' AND dept = '".$dept."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot access database, pls try later !");
			$this->msg->merge($sql->msg);
			return false;    
		}
			
		while ($row = $sql->fetch($q_result)) {
			$ttl = 0;
			foreach($txt as $key => $vaule) 
			{
				$ttl += $row[$vaule];
			}
			$row['ttl'] = $ttl;
			if($row['type'] == 'F') $op['f'] = $row;						
			if($row['type'] == 'E') $op['e'] = $row;
		}
		foreach($txt as $key => $vaule) 
		{
			$op['d'][$vaule] = $op['e'][$vaule] - $op['f'][$vaule];
		}		
			$op['d']['ttl'] = $op['e']['ttl'] - $op['f']['ttl'];
		return $op;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->check($parm)		檢查 加入新記錄 是否正確
#								# 檢查該年月資料是否己存在
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function check($yy,$dept) {

		$this->msg = new MSG_HANDLE();
		$sql = $this->sql;
			############### 檢查輸入項目

		$q_str = "SELECT *	 FROM expense	 WHERE yy = '".$yy."' AND dept = '".$dept."'";
	
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot access database, pls try later !");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if($row = $sql->fetch($q_result))
		{
			return false;
		}	
					
		return true;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	-> edit($parm))		更新 資料
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function edit($parm) {

		$sql = $this->sql;
		############### 檢查輸入項目
		$this->msg = new MSG_HANDLE();

		
		#####   更新資料庫內容  // 直接在 sql 內加入
			$q_str = "UPDATE expense SET safety='".$parm['safety'].	
										"',cost='"				.$parm['cost']."',salary='"				.$parm['salary'].						"',rent='"				.$parm['rent'].			
										"',stationery='"		.$parm['sationery'].				"',travel='"			.$parm['travel'].
										"',freight='"				.$parm['freight'].					"',postal='"			.$parm['postal'].
										"',fix='"						.$parm['fix'].							"',ad='"					.$parm['ad'].
										"',water='"					.$parm['water'].						"',insurance='"		.$parm['insurance'].
										"',communicat='"		.$parm['communicat'].				"',donate='"			.$parm['donate'].
										"',taxes='"					.$parm['taxes'].						"',bed_dabt='"		.$parm['bed_dabt'].
										"',depreciation='"	.$parm['depreciation'].			"',other_1='"			.$parm['other_1'].
										"',book='"					.$parm['book'].							"',food='"				.$parm['food'].
										"',welfare='"				.$parm['welfare'].					"',rd='"					.$parm['rd'].
										"',teach='"					.$parm['teach'].						"',medicine='"		.$parm['medicine'].
										"',commission='"		.$parm['commission'].				"',labor_1='"			.$parm['labor_1'].
										"',other_2='"				.$parm['other_2'].					"',other_3='"			.$parm['other_3'].
										"',customs='"				.$parm['customs'].					"',sample='"			.$parm['sample'].
										"',bank='"					.$parm['bank'].							"',pack='"				.$parm['pack'].
										"',labor_2='"				.$parm['labor_2'].					"',health='"			.$parm['health'].
										"',material='"			.$parm['material'].					"',share='"				.$parm['share'].
										"',prize='"					.$parm['prize'].						"',retire_1='"		.$parm['retire_1'].
										"',reach_cost='"		.$parm['reach_cost'].				"',build='"				.$parm['build'].
										"',retire_2='"			.$parm['retire_2'].					"',cloth_fix='"		.$parm['cloth_fix'].
										"',work_cloth='"		.$parm['work_cloth'].				"',manage='"			.$parm['manage'].
										"' WHERE id='".$parm['id']."' ";

		$q_result = $sql->query($q_str);



		
		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($yy,$mm)
#			搜尋 某月份預算花費  資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search($yy,$dept,$mm) {
		$op['f'] = $op['e'] = array();
		$sql = $this->sql;
//		$mm = date('m');
		$where_str = '';
		$txt = array('cost','salary','rent','stationery','travel','freight','postal','fix','ad','water','insurance',
								 'communicat','donate','taxes','bed_dabt','depreciation','other_1','book','food',
								 'welfare','rd','teach','medicine','commission','labor_1','other_2','other_3','customs',
								 'sample','bank','pack','labor_2','health','material','share','prize','retire_1',
								 'reach_cost','build','retire_2','cloth_fix','work_cloth','manage','safety');	
	
		if($dept) $where_str = " AND dept ='".$dept."'";
		$field_str = 'mm, yy, type';
		for($i=0; $i<sizeof($txt); $i++)$field_str .= ", sum(".$txt[$i].")as ".$txt[$i];
		$q_str = "SELECT $field_str	 FROM expense	 WHERE yy = '".$yy."' ".$where_str." GROUP BY yy, mm, type";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot access database, pls try later !");
			$this->msg->merge($sql->msg);
			return false;    
		}
			
		while ($row = $sql->fetch($q_result)) {
			$ttl = 0;
			for($i=0; $i<sizeof($txt); $i++)	$ttl += $row[$txt[$i]];
			$row['ttl'] = $ttl;
			if($row['type'] == 'F') $op['f'][] = $row;						
			if($row['type'] == 'E') $op['e'][] = $row;
		}
		$txt[] = 'ttl';
		for($i=0; $i<sizeof($op['f']); $i++)
		{
			for($j=0; $j<sizeof($txt); $j++)
			{
				if(!isset($op['sub_f'][$txt[$j]]))$op['sub_f'][$txt[$j]] = 0;
				if(!isset($op['sub_e'][$txt[$j]]))$op['sub_e'][$txt[$j]] = 0;
				if($mm >= $op['f'][$i]['mm'])
				{
					if($op['f'][$i][$txt[$j]] <> 0)$op['sub_f'][$txt[$j]] +=  $op['f'][$i][$txt[$j]];
					if($op['e'][$i][$txt[$j]] <> 0)$op['sub_e'][$txt[$j]] +=  $op['e'][$i][$txt[$j]];
				}
			}			
		}

		if(isset($op['sub_f']))for($j=0; $j<sizeof($txt); $j++)	$op['diff'][$txt[$j]] = $op['sub_e'][$txt[$j]] - $op['sub_f'][$txt[$j]];

		return $op;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($yy,$mm)
#			搜尋 某月份預算花費  資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_forecast($yy,$dept='') {

		$sql = $this->sql;
		$mm = date('m');
		$txt = array('cost','salary','rent','stationery','travel','freight','postal','fix','ad','water','insurance',
								 'communicat','donate','taxes','bed_dabt','depreciation','other_1','book','food',
								 'welfare','rd','teach','medicine','commission','labor_1','other_2','other_3','customs',
								 'sample','bank','pack','labor_2','health','material','share','prize','retire_1',
								 'reach_cost','build','retire_2','cloth_fix','work_cloth','manage','safety');	
	
		$q_str = "SELECT *	 FROM expense	 WHERE yy = '".$yy."' AND type = 'F'";
		if($dept) $q_str .= " AND dept='".$dept."'";
		$q_str .= " ORDER BY mm";
	
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot access database, pls try later !");
			$this->msg->merge($sql->msg);
			return false;    
		}
			
		while ($row = $sql->fetch($q_result)) {
			$ttl = 0;
			for($i=0; $i<sizeof($txt); $i++) 
			{
				$ttl += $row[$txt[$i]];
			}
			$row['ttl'] = $ttl;
			$op['f'][] = $row;	
			for($i=0; $i<sizeof($txt); $i++) 
			{
				if(!isset($op['item'][$txt[$i]]))$op['item'][$txt[$i]] = 0;
				if($row[$txt[$i]] > 0)$op['item'][$txt[$i]] +=  $row[$txt[$i]];
			}			
			if(!isset($op['item']['ttl']))$op['item']['ttl'] = 0;	
			if($row['ttl'] > 0)$op['item']['ttl'] +=  $row['ttl'];
		}		
		
		
		
		return $op;
	} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($yy,$mm)
#			搜尋 某月份預算花費  資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function sales_analysis($yy,$mm) {

		$sql = $this->sql;
//		$mm = date('m');
		$txt = array('cost','salary','rent','stationery','travel','freight','postal','fix','ad','water','insurance',
								 'communicat','donate','taxes','bed_dabt','depreciation','other_1','book','food',
								 'welfare','rd','teach','medicine','commission','labor_1','other_2','other_3','customs',
								 'sample','bank','pack','labor_2','health','material','share','prize','retire_1',
								 'reach_cost','build','retire_2','cloth_fix','work_cloth','manage','safety');	
	
		$mm = date('m');
		for($i=1; $i<13; $i++) $op['now']['F'][$i] = $op['now']['E'][$i] = 0;
//今年 	
		$q_str = "SELECT *	 FROM expense	 WHERE yy = '".$yy."'  ORDER BY mm";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot access database, pls try later !");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		while ($row = $sql->fetch($q_result)) {
			if(!isset($op['now'][$row['type']][$row['mm']]))$op['now'][$row['type']][$row['mm']] = 0;			
			for($i=0; $i<sizeof($txt); $i++) $op['now'][$row['type']][$row['mm']] +=  $row[$txt[$i]];			
		}		

		$op['now']['F'][0]=$op['now_ttl']['F']=$op['now']['E'][0]=$op['now_ttl']['E']=0;
		for($i=1; $i<13; $i++)
		{
		 	if($i<= $mm)$op['now']['F'][0] += $op['now']['F'][$i];
		  $op['now_ttl']['F'] += $op['now']['F'][$i];
		  if($i<= $mm)$op['now']['E'][0] += $op['now']['E'][$i];
		  $op['now_ttl']['E'] += $op['now']['E'][$i];	 
		}
//前一年
		$yy = $yy -1;
		$q_str = "SELECT *	 FROM expense	 WHERE yy = '".$yy."'  ORDER BY mm";
	
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot access database, pls try later !");
			$this->msg->merge($sql->msg);
			return false;    
		}			
	
		$mm_ary = array(1,2,3,4,5,6,7,8,9,10,11,12);
		for($i=0; $i<sizeof($mm_ary); $i++)
		{
			$op['bfr']['F'][$mm_ary[$i]] = 0;
			$op['bfr']['E'][$mm_ary[$i]] = 0;
		}
		while ($row = $sql->fetch($q_result)) {
			if(!isset($op['bfr'][$row['type']][$row['mm']]))$op['bfr'][$row['type']][$row['mm']] = 0;			
			for($i=0; $i<sizeof($txt); $i++) $op['bfr'][$row['type']][$row['mm']] +=  $row[$txt[$i]];
		}			
		
		$op['bfr']['F'][0]=$op['bfr_ttl']['F']=$op['bfr']['E'][0]=$op['bfr_ttl']['E']=0;
		for($i=1; $i<13; $i++)
		{
		 	if($i<= $mm)$op['bfr']['F'][0] += $op['bfr']['F'][$i];
		 	$op['bfr_ttl']['F'] += $op['bfr']['F'][$i];
		 	if($i<= $mm)$op['bfr']['E'][0] += $op['bfr']['E'][$i];
		 	$op['bfr_ttl']['E'] += $op['bfr']['E'][$i];		 
		}		
		return $op;
	} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_etd_ord($fty, $year)	抓出指定記錄內未核可訂單SU資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_ord_qty($fty, $yystr,$yyend, $where_str='') {

		$sql = $this->sql;		
		$rtn = array();
		$q_str = "SELECT sum(s_order.qty) as qty, YEAR(etd)as etd_year, MONTH(etd) as etd_month
						  FROM s_order 
							WHERE s_order.factory='".$fty."' AND etd >= '".$yystr."' AND etd <='".$yyend."' AND
										s_order.status >= 4 AND  s_order.status <> 5 ".$where_str." 
							GROUP BY etd_year, etd_month";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot access database, pls try later !");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			if($row['etd_month'] < 10)$row['etd_month'] = '0'.$row['etd_month'];
			$tmp = $row['etd_year']."-".$row['etd_month'];
			$rtn[$tmp] = $row['qty'];
			
		}
//		echo $yymm."====>".$row['su']."<br>";
		return $rtn;
	} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_etd_ord($fty, $year)	抓出指定記錄內未核可訂單SU資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_compare_det($fty, $mm, $yy) {
		
		$sql = $this->sql;		
		$rtn = array();
		$cmp_det = array();
		$q_str = "SELECT sum(s_order.qty) as ord_qty, sum(s_order.uprice * qty) as ord_fob, 
										 cust_init_name as cust_iname, s_order.cust
						  FROM s_order, cust
							WHERE s_order.factory='".$fty."' AND MONTH(etd) = '".$mm."'  AND s_order.status >= 4 AND
										YEAR(etd) = '".$yy."' AND
										s_order.status <> 5  AND s_order.cust=cust.cust_s_name  AND s_order.cust_ver = cust.ver										
							GROUP BY s_order.cust";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot access database, pls try later !");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			$cmp_det[$row['cust']] = $row;			
		}
		
		
		$q_str = "SELECT forecast2.*, cust_init_name as cust_iname
						  FROM forecast2, cust
							WHERE fty ='".$fty."' AND method = 'forecast'  AND year = '".$yy."' AND
										forecast2.cust=cust.cust_s_name  AND forecast2.cust_ver = cust.ver										
							";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot access database, pls try later !");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$mm--;
		while ($row = $sql->fetch($q_result)) {
			$fcst['q'] = csv2array($row['qty']);
			$fcst['f'] = csv2array($row['fcst']);
			$fcst['c'] = csv2array($row['cm']);
			if($fcst['q'][$mm] > 0)
			{
				if(!isset($cmp_det[$row['cust']]))
				{
					$cmp_det[$row['cust']]['cust'] = $row['cust'];
					$cmp_det[$row['cust']]['cust_iname'] = $row['cust_iname'];
					$cmp_det[$row['cust']]['ord_qty'] = $cmp_det[$row['cust']]['ord_fob'] = 0;
				}
				$cmp_det[$row['cust']]['fcst_qty'] = $fcst['q'][$mm];
				$cmp_det[$row['cust']]['fcst_fob'] = $fcst['f'][$mm] + ($fcst['c'][$mm]/100 * $fcst['f'][$mm]);
			}				
		}
		$rtn['det'] = array();
		$rtn['fcst_fob_ttl'] = $rtn['fcst_qty_ttl'] = $rtn['ord_fob_ttl'] = $rtn['ord_qty_ttl'] = $rtn['diff_fob_ttl'] = $rtn['diff_qty_ttl'] = 0;		
		foreach($cmp_det as $key => $value)
		{
			if(!isset($cmp_det[$key]['fcst_fob']))$cmp_det[$key]['fcst_fob'] = $cmp_det[$key]['fcst_qty'] = 0;
//			$cmp_det[$key]['diff_fob'] = $cmp_det[$key]['ord_fob'] - $cmp_det[$key]['fcst_fob'];
//			$cmp_det[$key]['diff_qty'] = $cmp_det[$key]['ord_qty'] - $cmp_det[$key]['fcst_qty'];
			$rtn['det'][] = $cmp_det[$key];
			$rtn['fcst_fob_ttl'] += $cmp_det[$key]['fcst_fob'];
			$rtn['fcst_qty_ttl'] += $cmp_det[$key]['fcst_qty'];
			$rtn['ord_fob_ttl'] += $cmp_det[$key]['ord_fob'];
			$rtn['ord_qty_ttl'] += $cmp_det[$key]['ord_qty'];
//			$rtn['diff_fob_ttl'] += $cmp_det[$key]['diff_fob'];
//			$rtn['diff_qty_ttl'] += $cmp_det[$key]['diff_qty'];
			
		}
		
		
		
//		echo $yymm."====>".$row['su']."<br>";
		return $rtn;
	} // end func










#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

} // end class


?>