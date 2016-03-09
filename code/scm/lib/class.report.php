<?php 

#++++++++++++++++++++++++++++++++++ Report  class ##### 報 表  ++++++++++++++++++++++++++++++++++++++++

#	->init($sql)	啟始(使用 Msg_handle() ; 先聯上 sql)
#	->out_search_sl($mode=0,$where_str='')	搜尋  業務接單資料
#	->out_search($mode=0,$where_str='')	搜尋  工廠接單資料
#	->get_output($mode=0,$where_str='')	將接單資料依月份整理
#	->rep_sch_search($id=0, $order_num=0)	抓出己排程且可生產的訂單
#	->rep_unsch_search($parm)	抓出己排程但不可生產的訂單(主副料和樣本核可 無)
#	->rep_nosch_search($parm)	抓出未排程但應排程的訂單(主副料預計到料日有)
#
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class REPORT {
		
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


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->out_search_sl($mode=0,$where_str='')	搜尋  業務接單資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function sl_qty_search($sch_date,$sch_fty) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv
		$srh = new SEARCH();
		$fields = array();
//條件 : $parm['s_date'] <= k_date <=$parm['e_date'] 且 factory=$parm['fty'] 且order_num NOT like '1%'
		$q_str = "SELECT pdtion.etp_su, s_order.cust, cust_init_name as cust_iname 
							FROM pdtion, s_order, cust
		          WHERE pdtion.order_num = s_order.order_num AND (s_order.cust=cust.cust_s_name AND s_order.cust_ver = cust.ver) AND
		          			s_order.status >= '0' AND s_order.dept <> 'J1' AND s_order.dept <> '$sch_fty' AND
		          			s_order.factory = '$sch_fty' AND etp_su like '%$sch_date%' 
		          ORDER BY s_order.cust";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			$tmp = explode(',',$row['etp_su']);
			for ($i=0; $i<sizeof($tmp); $i++)
			{
				$tmp_yy = substr($tmp[$i],0,6);
				$tmp_su = substr($tmp[$i],6);				
				if($tmp_yy == $sch_date) $row['su'] = $tmp_su;					
			} 
			$fields[] = $row;
		}
		return $fields;
	} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->out_search($mode=0,$where_str='')	搜尋  工廠接單資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function fty_qty_search($sch_date,$sch_fty) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv
		$srh = new SEARCH();
		$fields = array();

//條件 : 
		$q_str = "SELECT pdtion.etp_su, s_order.cust, cust_init_name as cust_iname 
							FROM pdtion, s_order, cust
		          WHERE pdtion.order_num = s_order.order_num AND (s_order.cust=cust.cust_s_name AND s_order.cust_ver = cust.ver) 
		          			AND s_order.status >= '0'	AND( s_order.dept = 'J1' OR s_order.dept = '$sch_fty')		          			
		          			AND s_order.factory = '$sch_fty' AND etp_su like '%$sch_date%' 
		          ORDER BY s_order.cust";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			$tmp = explode(',',$row['etp_su']);
			for ($i=0; $i<sizeof($tmp); $i++)
			{
				$tmp_yy = substr($tmp[$i],0,6);
				$tmp_su = substr($tmp[$i],6);				
				if($tmp_yy == $sch_date) $row['su'] = $tmp_su;					
			} 
			$fields[] = $row;	
							
		}
		return $fields;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_output($mode=0,$where_str='')	將接單資料依月份整理	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_ord_qty($fty,$PHP_year1,$cal)
{
	$ary_mc = array('01','02','03','04','05','06','07','08','09','10','11','12');
  $ary_m=array('jan','fab','mar','apl','may','jun','jly','aug','sep','oct','nov','dec');
  $op['total_p']=$op['sal_total_p']=$op['total']=$op['sal_total']=0;
  $tmp_cust = $tmp_cust_ini = '';
  $op['prt'] = $op['sals'] = array();
  $z = 0;
	for($i = 0; $i< sizeof($ary_mc); $i++)
	{
		 $op['fty_mt'][$i] = 0;
		$sch_date = $PHP_year1.$ary_mc[$i]; //年-月
		
		$qty_dly = $this->fty_qty_search($sch_date,$fty);  //取出所有工廠接單資料
		for ($j=0; $j<sizeof($qty_dly ); $j++) //每月訂單量的roop
		{
			$chk_cust = 0;
			
			 for($k = 0; $k< sizeof($op['prt']); $k++) //所有客人的roop
			 {			 		
			 		if($qty_dly[$j]['cust'] == $op['prt'][$k]['cust'])
			 		{
			 			$op['prt'][$k][$ary_m[$i]] += $qty_dly[$j]['su']; //每個客人每月總合
			 			$op['prt'][$k]['total'] += $qty_dly[$j]['su'];  // 每個客人年度總合
			 			$chk_cust = 1;
			 			break;
			 		}
			 }
			 
			 if($chk_cust == 0){  //當客人不存在時的工作
			 		for ($x=0; $x<sizeof($ary_m); $x++)	$op['prt'][$z][$ary_m[$x]]=0;			 		
					$op['prt'][$z]['cust'] = $qty_dly[$j]['cust'];
					$op['prt'][$z]['cust_iname'] = $qty_dly[$j]['cust_iname'];
					$op['prt'][$z][$ary_m[$i]] = $qty_dly[$j]['su']; //每個客人每月總合
					$op['prt'][$z]['total'] = $qty_dly[$j]['su'];  //每個客人年度總合
					$z++;
			 	}		
			 	$op['fty_mt'][$i] += $qty_dly[$j]['su']; //各月工廠訂單量總計
			 	$op['total'] += $qty_dly[$j]['su']; //所有工廠訂單量總計
			
		} // end for ($j=0; $j<sizeof($qty_dly ); $j++)
	}// end for($i = 0; $i< sizeof($ary_mc); $i++)
		
	unset($qty_dly);
	$z = 0;
	for($i = 0; $i< sizeof($ary_mc); $i++)
	{
		$op['sal_mt'][$i] = 0;
		$sch_date = $PHP_year1.$ary_mc[$i]; //年-月
		
		$qty_dly = $this->sl_qty_search($sch_date,$fty);  //取出所有工廠接單資料
		for ($j=0; $j<sizeof($qty_dly ); $j++) //每月訂單量的roop
		{
			$chk_cust = 0;
			
			 for($k = 0; $k< sizeof($op['sals']); $k++) //所有客人的roop
			 {			 		
			 		if($qty_dly[$j]['cust'] == $op['sals'][$k]['cust'])
			 		{
			 			$op['sals'][$k][$ary_m[$i]] += $qty_dly[$j]['su']; //每個客人每月總合
			 			$op['sals'][$k]['total'] += $qty_dly[$j]['su'];  // 每個客人年度總合
			 			$chk_cust = 1;
			 			break;
			 		}
			 }
			 if($chk_cust == 0){  //當客人不存在時的工作
			 		for ($x=0; $x<sizeof($ary_m); $x++)	$op['sals'][$z][$ary_m[$x]]=0;					 		
					$op['sals'][$z]['cust'] = $qty_dly[$j]['cust'];
					$op['sals'][$z]['cust_iname'] = $qty_dly[$j]['cust_iname'];
					$op['sals'][$z][$ary_m[$i]] = $qty_dly[$j]['su']; //每個客人每月總合
					$op['sals'][$z]['total'] = $qty_dly[$j]['su'];  //每個客人年度總合
					$z++;
			 	}		
			 	$op['sal_mt'][$i] += $qty_dly[$j]['su']; //各月業務訂單量總計
			 	$op['sal_total'] += $qty_dly[$j]['su']; //所有業務訂單量總計
			
		} // end for ($j=0; $j<sizeof($qty_dly ); $j++)
	}// end for($i = 0; $i< sizeof($ary_mc); $i++)	

	
	for ($i=0; $i< sizeof($ary_m); $i++)
	{
		$op['m_tal'][$i] = $op['fty_mt'][$i] + $op['sal_mt'][$i]; //各月訂單總量
		if ($op['sal_mt'][$i]==0){$op['sp_m'][$i]=0;}else{$op['sp_m'][$i] = ($op['sal_mt'][$i] / $op['m_tal'][$i])*100;} //各月業務訂單比
		if ($op['fty_mt'][$i]==0){$op['fp_m'][$i]=0;}else{$op['fp_m'][$i] = ($op['fty_mt'][$i] / $op['m_tal'][$i])*100;}	//各月工廠訂單比
	}
	$op['total_tal']=$op['sal_total']+$op['total']; //所有訂單量總計
	if ($op['sal_total'] > 0) $op['sal_total_p']=($op['sal_total'] / $op['total_tal'])*100;	//所有業務訂單比
	if ($op['total'] > 0) $op['total_p']=($op['total'] / $op['total_tal'])*100; //所有工廠訂單比

	return $op;
}
























#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->out_search_sl($mode=0,$where_str='')	搜尋  業務接單資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function out_search_sl($parm,$mode=0,$where_str="",$limit_entries=0) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv
		$srh = new SEARCH();
		$cgi = array();
//條件 : $parm['s_date'] <= k_date <=$parm['e_date'] 且 factory=$parm['fty'] 且order_num NOT like '1%'
		$q_str = "SELECT saw_out_put.out_date as k_date, saw_out_put.qty, saw_out_put.su, 
		                 saw_out_put.saw_fty as factory, saw_out_put.ord_num,
										 s_order.cust, cust.cust_init_name as cust_iname 
						  FROM saw_out_put, s_order, cust
		          WHERE (out_date >='". $parm['s_date']."') AND (out_date <= '".$parm['e_date']."') 
		             AND s_order.status >= '0'
		             AND (saw_out_put.ord_num = s_order.order_num) AND (saw_out_put.saw_fty = '".$parm['fty']."')
		             AND (saw_out_put.ord_num NOT LIKE '1%') AND (saw_out_put.ord_num NOT LIKE 'H%') AND (saw_out_put.ord_num NOT LIKE 'L%')
		             AND (s_order.cust=cust.cust_s_name) AND (s_order.cust_ver = cust.ver) 
		          GROUP BY saw_out_put.ord_num, saw_out_put.out_date
		          ORDER BY s_order.cust, out_date";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
				$fields[] = $row;	
		}
		if (!isset($fields))
		{
			$fields="none";
		}
		return $fields;
	} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->out_search($mode=0,$where_str='')	搜尋  工廠接單資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function out_search($parm,$mode=0,$where_str="",$limit_entries=0) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv
		$srh = new SEARCH();
		$cgi = array();

//條件 : $parm['s_date'] <= k_date <=$parm['e_date'] 且 factory=$parm['fty'] 且order_num like '1%'
		$q_str = "SELECT saw_out_put.out_date as k_date, saw_out_put.qty, saw_out_put.su, 
		                 saw_out_put.saw_fty as factory, saw_out_put.ord_num, 
		                 s_order.cust, cust_init_name as cust_iname 
		          FROM   saw_out_put, s_order, cust
		          WHERE (out_date >='". $parm['s_date']."') AND (out_date <= '".$parm['e_date']."') 
		             AND s_order.status >= '0'
		             AND (saw_out_put.ord_num = s_order.order_num) AND (saw_out_put.factory = '".$parm['fty']."')
		             AND (saw_out_put.ord_num like '1%' or saw_out_put.ord_num like 'H%' or saw_out_put.ord_num like 'L%') 
		             AND (s_order.cust=cust.cust_s_name  AND s_order.cust_ver = cust.ver) 
		         GROUP BY saw_out_put.ord_num, saw_out_put.out_date
		         ORDER BY s_order.cust, k_date";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
				$fields[] = $row;				
		}
		if (!isset($fields))
		{
			$fields="none";
		}
		return $fields;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_output($mode=0,$where_str='')	將接單資料依月份整理	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_output($parm,$PHP_year1,$cal)
{
  $out['daily'] = $this->out_search($parm,1);  //取出所有工廠接單資料
  $ary_m=array('jan','fab','mar','apl','may','jun','jly','aug','sep','oct','nov','dec');
  for ($i=0; $i< sizeof($ary_m); $i++) $op[$ary_m[$i]]=0; //預設值 0
  $op['total']=0;

  if ($out['daily']<>"none")
  {
	$op['prt'][0]['cust'] = $out['daily'][0]['cust'];
	$op['prt'][0]['cust_iname'] = $out['daily'][0]['cust_iname'];
	for ($i=0; $i< sizeof($ary_m); $i++) $op['prt'][0][$ary_m[$i]]=0;  //預設值 0
	$j=0;
	
	for ($i=0; $i<sizeof($out['daily'] ); $i++)
	{
				
		if ($out['daily'][$i]['k_date'] <= $PHP_year1.'-01-31' && $out['daily'][$i]['k_date'] >= $PHP_year1.'-01-01')
		{
			if ($out['daily'][$i]['cust']==$op['prt'][$j]['cust'])
			{ 
				$op['prt'][$j]['jan']=$op['prt'][$j]['jan']+$out['daily'][$i]['su'];
			}else{
			
				$j++;
				for ($x=0; $x<sizeof($ary_m); $x++)	$op['prt'][$j][$ary_m[$x]]=0;
				$op['prt'][$j]['cust'] = $out['daily'][$i]['cust'];
				$op['prt'][$j]['cust_iname'] = $out['daily'][$i]['cust_iname'];
				$op['prt'][$j]['jan'] = $out['daily'][$i]['su'];
			}
			
		}
		if ($out['daily'][$i]['k_date'] <= $PHP_year1.'-02-29' && $out['daily'][$i]['k_date'] >= $PHP_year1.'-02-01')
		{
			if ($out['daily'][$i]['cust']==$op['prt'][$j]['cust'])
			{
				$op['prt'][$j]['fab']=$op['prt'][$j]['fab']+$out['daily'][$i]['su'];
			}else{
				$j++;
				for ($x=0; $x<sizeof($ary_m); $x++)	$op['prt'][$j][$ary_m[$x]]=0;
				$op['prt'][$j]['cust'] = $out['daily'][$i]['cust'];
				$op['prt'][$j]['cust_iname'] = $out['daily'][$i]['cust_iname'];
				$op['prt'][$j]['fab'] = $out['daily'][$i]['su'];				
			}
			
		}
		if ($out['daily'][$i]['k_date'] <= $PHP_year1.'-03-31' && $out['daily'][$i]['k_date'] >= $PHP_year1.'-03-01')
		{
			if ($out['daily'][$i]['cust']==$op['prt'][$j]['cust'])
			{
				$op['prt'][$j]['mar']=$op['prt'][$j]['mar']+$out['daily'][$i]['su'];
			}else{
				$j++;
				for ($x=0; $x<sizeof($ary_m); $x++)	$op['prt'][$j][$ary_m[$x]]=0;
				$op['prt'][$j]['cust'] = $out['daily'][$i]['cust'];
				$op['prt'][$j]['cust_iname'] = $out['daily'][$i]['cust_iname'];
				$op['prt'][$j]['mar'] = $out['daily'][$i]['su'];				
			}
			
		}
		if ($out['daily'][$i]['k_date'] <= $PHP_year1.'-04-30' && $out['daily'][$i]['k_date'] >= $PHP_year1.'-04-01')
		{
			if ($out['daily'][$i]['cust']==$op['prt'][$j]['cust'])
			{
				$op['prt'][$j]['apl']=$op['prt'][$j]['apl']+$out['daily'][$i]['su'];
			}else{
				$j++;
				for ($x=0; $x<sizeof($ary_m); $x++)	$op['prt'][$j][$ary_m[$x]]=0;
				$op['prt'][$j]['cust'] = $out['daily'][$i]['cust'];
				$op['prt'][$j]['cust_iname'] = $out['daily'][$i]['cust_iname'];
				$op['prt'][$j]['apl'] = $out['daily'][$i]['su'];				
			}
			
		}
		if ($out['daily'][$i]['k_date'] <= $PHP_year1.'-05-31' && $out['daily'][$i]['k_date'] >= $PHP_year1.'-05-01')
		{
			if ($out['daily'][$i]['cust']==$op['prt'][$j]['cust'])
			{
				$op['prt'][$j]['may']=$op['prt'][$j]['may']+$out['daily'][$i]['su'];
			}else{
				$j++;
				for ($x=0; $x<sizeof($ary_m); $x++)	$op['prt'][$j][$ary_m[$x]]=0;
				$op['prt'][$j]['cust'] = $out['daily'][$i]['cust'];
				$op['prt'][$j]['cust_iname'] = $out['daily'][$i]['cust_iname'];
				$op['prt'][$j]['may'] = $out['daily'][$i]['su'];				
			}
			
		}
		if ($out['daily'][$i]['k_date'] <= $PHP_year1.'-06-30' && $out['daily'][$i]['k_date'] >= $PHP_year1.'-06-01')
		{
			if ($out['daily'][$i]['cust']==$op['prt'][$j]['cust'])
			{
				$op['prt'][$j]['jun']=$op['prt'][$j]['jun']+$out['daily'][$i]['su'];
			}else{
				$j++;
				for ($x=0; $x<sizeof($ary_m); $x++)	$op['prt'][$j][$ary_m[$x]]=0;
				$op['prt'][$j]['cust'] = $out['daily'][$i]['cust'];
				$op['prt'][$j]['cust_iname'] = $out['daily'][$i]['cust_iname'];
				$op['prt'][$j]['jun'] = $out['daily'][$i]['su'];				
			}
			
		}
		if ($out['daily'][$i]['k_date'] <= $PHP_year1.'-07-31' && $out['daily'][$i]['k_date'] >= $PHP_year1.'-07-01')
		{
			if ($out['daily'][$i]['cust']==$op['prt'][$j]['cust'])
			{
				$op['prt'][$j]['jly']=$op['prt'][$j]['jly']+$out['daily'][$i]['su'];
			}else{
				$j++;
				for ($x=0; $x<sizeof($ary_m); $x++)	$op['prt'][$j][$ary_m[$x]]=0;
				$op['prt'][$j]['cust'] = $out['daily'][$i]['cust'];
				$op['prt'][$j]['cust_iname'] = $out['daily'][$i]['cust_iname'];
				$op['prt'][$j]['jly'] = $out['daily'][$i]['su'];				
			}
			
		}
		if ($out['daily'][$i]['k_date'] <= $PHP_year1.'-08-31' && $out['daily'][$i]['k_date'] >= $PHP_year1.'-08-01')
		{
			if ($out['daily'][$i]['cust']==$op['prt'][$j]['cust'])
			{
				$op['prt'][$j]['aug']=$op['prt'][$j]['aug']+$out['daily'][$i]['su'];
			}else{
				$j++;
				for ($x=0; $x<sizeof($ary_m); $x++)	$op['prt'][$j][$ary_m[$x]]=0;
				$op['prt'][$j]['cust'] = $out['daily'][$i]['cust'];
				$op['prt'][$j]['cust_iname'] = $out['daily'][$i]['cust_iname'];
				$op['prt'][$j]['aug'] = $out['daily'][$i]['su'];				
			}
			
		}
		if ($out['daily'][$i]['k_date'] <= $PHP_year1.'-09-30' && $out['daily'][$i]['k_date'] >= $PHP_year1.'-09-01')
		{
			if ($out['daily'][$i]['cust']==$op['prt'][$j]['cust'])
			{
				$op['prt'][$j]['sep']=$op['prt'][$j]['sep']+$out['daily'][$i]['su'];
			}else{
				$j++;
				for ($x=0; $x<sizeof($ary_m); $x++)	$op['prt'][$j][$ary_m[$x]]=0;
				$op['prt'][$j]['cust'] = $out['daily'][$i]['cust'];
				$op['prt'][$j]['cust_iname'] = $out['daily'][$i]['cust_iname'];
				$op['prt'][$j]['sep'] = $out['daily'][$i]['su'];				
			}
			
		}
		if ($out['daily'][$i]['k_date'] <= $PHP_year1.'-10-31' && $out['daily'][$i]['k_date'] >= $PHP_year1.'-10-01')
		{
			if ($out['daily'][$i]['cust']==$op['prt'][$j]['cust'])
			{
				$op['prt'][$j]['oct']=$op['prt'][$j]['oct']+$out['daily'][$i]['su'];
			}else{
				$j++;
				for ($x=0; $x<sizeof($ary_m); $x++)	$op['prt'][$j][$ary_m[$x]]=0;
				$op['prt'][$j]['cust'] = $out['daily'][$i]['cust'];
				$op['prt'][$j]['cust_iname'] = $out['daily'][$i]['cust_iname'];
				$op['prt'][$j]['oct'] = $out['daily'][$i]['su'];				
			}
			
		}
		if ($out['daily'][$i]['k_date'] <= $PHP_year1.'-11-30' && $out['daily'][$i]['k_date'] >= $PHP_year1.'-11-01')
		{
			if ($out['daily'][$i]['cust']==$op['prt'][$j]['cust'])
			{
				$op['prt'][$j]['nov']=$op['prt'][$j]['nov']+$out['daily'][$i]['su'];
			}else{
				$j++;
				for ($x=0; $x<sizeof($ary_m); $x++)	$op['prt'][$j][$ary_m[$x]]=0;
				$op['prt'][$j]['cust'] = $out['daily'][$i]['cust'];
				$op['prt'][$j]['cust_iname'] = $out['daily'][$i]['cust_iname'];
				$op['prt'][$j]['nov'] = $out['daily'][$i]['su'];				
			}
			
		}
		if ($out['daily'][$i]['k_date'] <= $PHP_year1.'-12-30' && $out['daily'][$i]['k_date'] >= $PHP_year1.'-12-01')
		{
			if ($out['daily'][$i]['cust']==$op['prt'][$j]['cust'])
			{
				$op['prt'][$j]['dec']=$op['prt'][$j]['dec']+$out['daily'][$i]['su'];
			}else{
				$j++;
				for ($x=0; $x<sizeof($ary_m); $x++)	$op['prt'][$j][$ary_m[$x]]=0;
				$op['prt'][$j]['cust'] = $out['daily'][$i]['cust'];
				$op['prt'][$j]['cust_iname'] = $out['daily'][$i]['cust_iname'];
				$op['prt'][$j]['dec'] = $out['daily'][$i]['su'];				
			}
			
		}
	}
	
	//工廠接單各月總
	for ($i=0; $i<sizeof($op['prt']); $i++)
	{
		$op['prt'][$i]['total'] = $op['prt'][$i]['jan']+$op['prt'][$i]['fab']+$op['prt'][$i]['mar']+$op['prt'][$i]['apl']+$op['prt'][$i]['may']
		                          +$op['prt'][$i]['jun']+$op['prt'][$i]['jly']+$op['prt'][$i]['aug']+$op['prt'][$i]['sep']+$op['prt'][$i]['oct']
		                          +$op['prt'][$i]['nov']+$op['prt'][$i]['dec'];
		                          
		for ($x=0; $x<sizeof($ary_m); $x++)  $op[$ary_m[$x]] = $op[$ary_m[$x]]+$op['prt'][$i][$ary_m[$x]];

	}
	$op['total']=$op['jan']+$op['fab']+$op['mar']+$op['apl']+$op['may']+$op['jun']+$op['jly']
				+$op['aug']+$op['sep']+$op['nov']+$op['dec']+$op['oct'];
	}else{
		$op['OEM_msg']='Without any factory output';
	}
	unset($out['daily']);
	$out['daily'] = $this->out_search_sl($parm,1);  //取出所有業務接單
	
	for ($x=0; $x<sizeof($ary_m); $x++) $op['sal_'.$ary_m[$x]] = 0;  //預設值 0
	$op['sal_total']=0;

	if ($out['daily']<>"none")
	{
	$op['sals'][0]['cust'] = $out['daily'][0]['cust'];
	$op['sals'][0]['cust_iname'] = $out['daily'][0]['cust_iname'];
	for ($x=0; $x<sizeof($ary_m); $x++) $op['sals'][0][$ary_m[$x]]=0;  //預設值 0
	$j=0;	
	for ($i=0; $i<sizeof($out['daily'] ); $i++)
	{		
		if ($out['daily'][$i]['k_date'] <= $PHP_year1.'-01-31' && $out['daily'][$i]['k_date'] >= $PHP_year1.'-01-01')
		{
			if ($out['daily'][$i]['cust']==$op['sals'][$j]['cust'])
			{
				$op['sals'][$j]['jan']=$op['sals'][$j]['jan']+$out['daily'][$i]['su'];
			}else{
				$j++;
				for ($x=0; $x<sizeof($ary_m); $x++)	$op['sals'][$j][$ary_m[$x]]=0;
				$op['sals'][$j]['cust'] = $out['daily'][$i]['cust'];
				$op['sals'][$j]['cust_iname'] = $out['daily'][$i]['cust_iname'];
				$op['sals'][$j]['jan'] = $out['daily'][$i]['su'];
			}
			
		}
		if ($out['daily'][$i]['k_date'] <= $PHP_year1.'-02-31' && $out['daily'][$i]['k_date'] >= $PHP_year1.'-02-01')
		{
			if ($out['daily'][$i]['cust']==$op['sals'][$j]['cust'])
			{
				$op['sals'][$j]['fab']=$op['sals'][$j]['fab']+$out['daily'][$i]['su'];
			}else{
				$j++;
				for ($x=0; $x<sizeof($ary_m); $x++)	$op['sals'][$j][$ary_m[$x]]=0;
				$op['sals'][$j]['cust'] = $out['daily'][$i]['cust'];
				$op['sals'][$j]['cust_iname'] = $out['daily'][$i]['cust_iname'];
				$op['sals'][$j]['fab'] = $out['daily'][$i]['su'];
			}
			
		}
		if ($out['daily'][$i]['k_date'] <= $PHP_year1.'-03-31' && $out['daily'][$i]['k_date'] >= $PHP_year1.'-03-01')
		{
			if ($out['daily'][$i]['cust']==$op['sals'][$j]['cust'])
			{
				$op['sals'][$j]['mar']=$op['sals'][$j]['mar']+$out['daily'][$i]['su'];
			}else{
				$j++;
				for ($x=0; $x<sizeof($ary_m); $x++)	$op['sals'][$j][$ary_m[$x]]=0;
				$op['sals'][$j]['cust'] = $out['daily'][$i]['cust'];
				$op['sals'][$j]['cust_iname'] = $out['daily'][$i]['cust_iname'];
				$op['sals'][$j]['mar'] = $out['daily'][$i]['su'];				
			}
			
		}
		if ($out['daily'][$i]['k_date'] <= $PHP_year1.'-04-30' && $out['daily'][$i]['k_date'] >= $PHP_year1.'-04-01')
		{
			if ($out['daily'][$i]['cust']==$op['sals'][$j]['cust'])
			{
				$op['sals'][$j]['apl']=$op['sals'][$j]['apl']+$out['daily'][$i]['su'];
			}else{
				$j++;
				for ($x=0; $x<sizeof($ary_m); $x++)	$op['sals'][$j][$ary_m[$x]]=0;
				$op['sals'][$j]['cust'] = $out['daily'][$i]['cust'];
				$op['sals'][$j]['cust_iname'] = $out['daily'][$i]['cust_iname'];
				$op['sals'][$j]['apl'] = $out['daily'][$i]['su'];				
			}
			
		}
		if ($out['daily'][$i]['k_date'] <= $PHP_year1.'-05-31' && $out['daily'][$i]['k_date'] >= $PHP_year1.'-05-01')
		{
			if ($out['daily'][$i]['cust']==$op['sals'][$j]['cust'])
			{
				$op['sals'][$j]['may']=$op['sals'][$j]['may']+$out['daily'][$i]['su'];
			}else{
				$j++;
				for ($x=0; $x<sizeof($ary_m); $x++)	$op['sals'][$j][$ary_m[$x]]=0;
				$op['sals'][$j]['cust'] = $out['daily'][$i]['cust'];
				$op['sals'][$j]['cust_iname'] = $out['daily'][$i]['cust_iname'];
				$op['sals'][$j]['may'] = $out['daily'][$i]['su'];				
			}
			
		}
		if ($out['daily'][$i]['k_date'] <= $PHP_year1.'-06-30' && $out['daily'][$i]['k_date'] >= $PHP_year1.'-06-01')
		{
			if ($out['daily'][$i]['cust']==$op['sals'][$j]['cust'])
			{
				$op['sals'][$j]['jun']=$op['sals'][$j]['jun']+$out['daily'][$i]['su'];
			}else{
				$j++;
				for ($x=0; $x<sizeof($ary_m); $x++)	$op['sals'][$j][$ary_m[$x]]=0;
				$op['sals'][$j]['cust'] = $out['daily'][$i]['cust'];
				$op['sals'][$j]['cust_iname'] = $out['daily'][$i]['cust_iname'];
				$op['sals'][$j]['jun'] = $out['daily'][$i]['su'];				
			}
			
		}
		if ($out['daily'][$i]['k_date'] <= $PHP_year1.'-07-31' && $out['daily'][$i]['k_date'] >= $PHP_year1.'-07-01')
		{
			if ($out['daily'][$i]['cust']==$op['sals'][$j]['cust'])
			{
				$op['sals'][$j]['jly']=$op['sals'][$j]['jly']+$out['daily'][$i]['su'];
			}else{
				$j++;
				for ($x=0; $x<sizeof($ary_m); $x++)	$op['sals'][$j][$ary_m[$x]]=0;
				$op['sals'][$j]['cust'] = $out['daily'][$i]['cust'];
				$op['sals'][$j]['cust_iname'] = $out['daily'][$i]['cust_iname'];
				$op['sals'][$j]['jly'] = $out['daily'][$i]['su'];				
			}
			
		}
		if ($out['daily'][$i]['k_date'] <= $PHP_year1.'-08-31' && $out['daily'][$i]['k_date'] >= $PHP_year1.'-08-01')
		{
			if ($out['daily'][$i]['cust']==$op['sals'][$j]['cust'])
			{
				$op['sals'][$j]['aug']=$op['sals'][$j]['aug']+$out['daily'][$i]['su'];
			}else{
				$j++;
				for ($x=0; $x<sizeof($ary_m); $x++)	$op['sals'][$j][$ary_m[$x]]=0;
				$op['sals'][$j]['cust'] = $out['daily'][$i]['cust'];
				$op['sals'][$j]['cust_iname'] = $out['daily'][$i]['cust_iname'];
				$op['sals'][$j]['aug'] = $out['daily'][$i]['su'];				
			}
			
		}
		if ($out['daily'][$i]['k_date'] <= $PHP_year1.'-09-30' && $out['daily'][$i]['k_date'] >= $PHP_year1.'-09-01')
		{
			if ($out['daily'][$i]['cust']==$op['sals'][$j]['cust'])
			{
				$op['sals'][$j]['sep']=$op['sals'][$j]['sep']+$out['daily'][$i]['su'];
			}else{
				$j++;
				for ($x=0; $x<sizeof($ary_m); $x++)	$op['sals'][$j][$ary_m[$x]]=0;
				$op['sals'][$j]['cust'] = $out['daily'][$i]['cust'];
				$op['sals'][$j]['cust_iname'] = $out['daily'][$i]['cust_iname'];
				$op['sals'][$j]['sep'] = $out['daily'][$i]['su'];				
			}
			
		}
		if ($out['daily'][$i]['k_date'] <= $PHP_year1.'-10-31' && $out['daily'][$i]['k_date'] >= $PHP_year1.'-10-01')
		{
			if ($out['daily'][$i]['cust']==$op['sals'][$j]['cust'])
			{
				$op['sals'][$j]['oct']=$op['sals'][$j]['oct']+$out['daily'][$i]['su'];
			}else{
				$j++;
				for ($x=0; $x<sizeof($ary_m); $x++)	$op['sals'][$j][$ary_m[$x]]=0;
				$op['sals'][$j]['cust'] = $out['daily'][$i]['cust'];
				$op['sals'][$j]['cust_iname'] = $out['daily'][$i]['cust_iname'];
				$op['sals'][$j]['oct'] = $out['daily'][$i]['su'];				
			}
			
		}
		if ($out['daily'][$i]['k_date'] <= $PHP_year1.'-11-30' && $out['daily'][$i]['k_date'] >= $PHP_year1.'-11-01')
		{
			if ($out['daily'][$i]['cust']==$op['sals'][$j]['cust'])
			{
				$op['sals'][$j]['nov']=$op['sals'][$j]['nov']+$out['daily'][$i]['su'];
			}else{
				$j++;
				for ($x=0; $x<sizeof($ary_m); $x++)	$op['sals'][$j][$ary_m[$x]]=0;
				$op['sals'][$j]['cust'] = $out['daily'][$i]['cust'];
				$op['sals'][$j]['cust_iname'] = $out['daily'][$i]['cust_iname'];
				$op['sals'][$j]['nov'] = $out['daily'][$i]['su'];				
			}
			
		}
		if ($out['daily'][$i]['k_date'] <= $PHP_year1.'-12-31' && $out['daily'][$i]['k_date'] >= $PHP_year1.'-12-01')
		{
			if ($out['daily'][$i]['cust']==$op['sals'][$j]['cust'])
			{
				$op['sals'][$j]['dec']=$op['sals'][$j]['dec']+$out['daily'][$i]['su'];
			}else{
				$j++;
				for ($x=0; $x<sizeof($ary_m); $x++)	$op['sals'][$j][$ary_m[$x]]=0;
				$op['sals'][$j]['cust'] = $out['daily'][$i]['cust'];
				$op['sals'][$j]['cust_iname'] = $out['daily'][$i]['cust_iname'];
				$op['sals'][$j]['dec'] = $out['daily'][$i]['su'];				
			}	
		}	
	}
	
	//業務接單各月加總
	for ($i=0; $i<sizeof($op['sals']); $i++)
	{
		$op['sals'][$i]['total'] = $op['sals'][$i]['jan']+$op['sals'][$i]['fab']+$op['sals'][$i]['mar']+$op['sals'][$i]['apl']+$op['sals'][$i]['may']
		                          +$op['sals'][$i]['jun']+$op['sals'][$i]['jly']+$op['sals'][$i]['aug']+$op['sals'][$i]['sep']+$op['sals'][$i]['oct']
		                          +$op['sals'][$i]['nov']+$op['sals'][$i]['dec'];
		for ($x=0; $x<sizeof($ary_m); $x++)  $op['sal_'.$ary_m[$x]]=$op['sal_'.$ary_m[$x]]+$op['sals'][$i][$ary_m[$x]];
	}
	$op['sal_total']=$op['sal_jan']+$op['sal_fab']+$op['sal_mar']+$op['sal_apl']+$op['sal_may']
					+$op['sal_jun']+$op['sal_jly']+$op['sal_aug']+$op['sal_sep']+$op['sal_nov']
					+$op['sal_dec']+$op['sal_oct'];
	}else{
		$op['sale_msg']='Without any salse output';
	}
	
	$m_tal=array('jan_tal','fab_tal','mar_tal','apl_tal','may_tal','jun_tal','jly_tal','aug_tal','sep_tal','nov_tal','oct_tal','dec_tal');
	$sal_m=array('sal_jan','sal_fab','sal_mar','sal_apl','sal_may','sal_jun','sal_jly','sal_aug','sal_sep','sal_nov','sal_oct','sal_dec');
	$mm=array('jan','fab','mar','apl','may','jun','jly','aug','sep','nov','oct','dec');
	$sp_m=array('sal_jan_p','sal_fab_p','sal_mar_p','sal_apl_p','sal_may_p','sal_jun_p','sal_jly_p','sal_aug_p','sal_sep_p','sal_nov_p','sal_oct_p','sal_dec_p');
	$m_p=array('jan_p','fab_p','mar_p','apl_p','may_p','jun_p','jly_p','aug_p','sep_p','nov_p','oct_p','dec_p');
	
	for ($i=0; $i< sizeof($m_tal); $i++)
	{
		$op[$m_tal[$i]]=$op[$sal_m[$i]]+$op[$mm[$i]];
		if ($op[$sal_m[$i]]==0)
		{
			$op[$sp_m[$i]]="0.00";
		}else{
			$op[$sp_m[$i]]=($op[$sal_m[$i]] / $op[$m_tal[$i]])*100;		
		}	
		if ($op[$mm[$i]]==0)
		{
			$op[$m_p[$i]]="0.00";
		}else{
			$op[$m_p[$i]]=($op[$mm[$i]] / $op[$m_tal[$i]])*100;
		}	
	}

	$op['total_tal']=$op['sal_total']+$op['total'];
	if ($op['sal_total']==0)
	{
		$op['sal_total_p']="0.00";
	}else{
		$op['sal_total_p']=($op['sal_total'] / $op['total_tal'])*100;
	}
	
	if ($op['total']==0)
	{
		$op['total_p']="0.00";
	}else{
		$op['total_p']=($op['total'] / $op['total_tal'])*100;
	}	
	return $op;
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->rep_sch_search($id=0, $order_num=0)	抓出己排程且可生產的訂單
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function rep_sch_search($parm,$sex='') {

		$sql = $this->sql;
		if($sex) $sex = " AND line_sex = '".$sex."'";
		$parm['s_date'] = increceMonthInDate($parm['e_date'],2);
		
		// 關聯式資料庫查尋 ~~~~~~
		$q_str = "SELECT s_order.*, pdtion.*, s_order.id as sid, order_partial.p_qty as qty,
										 order_partial.p_su as su, order_partial.p_etd as etd, order_partial.p_etp as etp,
										 order_partial.p_ets as ets, order_partial.p_etf as etf,
										 order_partial.mks
							FROM s_order,pdtion, order_partial 
							WHERE  
		          s_order.order_num=pdtion.order_num  AND s_order.order_num = order_partial.ord_num AND
		          pdtion.order_num = order_partial.ord_num AND
		          s_order.status >= '0' and 		         
		          ((s_order.status = 7 and (mat_shp IS NOT NULL or mat_shp<>'0000-00-00')  and (m_acc_shp IS NOT NULL or m_acc_shp <> '0000-00-00')  and smpl_apv <> '0000-00-00' ) or s_order.status > 7)
		          and s_order.factory='".$parm['fty']."' and( s_order.status < 10  or (pdtion.finish IS NULL AND qty_done > qty_shp) or (pdtion.finish = '0000-00-00' AND qty_done > qty_shp) ) 
		          and ((p_ets <= '".$parm['s_date']."' and p_etf >= '".$parm['e_date']."') or (p_ets >= '".$parm['s_date']."' and p_ets <='".$parm['e_date']."') or (p_etf >= '".$parm['s_date']."' and p_etf<='".$parm['e_date']."'))".$sex." 
		          order by order_partial.etd, sub_con";
	          
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			$rpt_do[]=$row;		   
		}
		if (!empty($rpt_do))
		{
			return $rpt_do;	
		}else{
			return false;
		}
		
} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->rep_unsch_search($parm)	抓出己排程但不可生產的訂單(主副料和樣本核可 無)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function rep_unsch_search($parm,$sex='') {

		$sql = $this->sql;
		if($sex) $sex = " AND line_sex = '".$sex."'";
		$parm['s_date'] = increceMonthInDate($parm['e_date'],2);
		
		// 關聯式資料庫查尋 ~~~~~~
		$q_str = "SELECT s_order.*, pdtion.*, s_order.id as sid,
										 order_partial.p_su as su, order_partial.p_etd as etd, order_partial.p_etp as etp,
										 order_partial.p_qty as qty, order_partial.p_ets as ets, order_partial.p_etf as etf,
										 order_partial.mks
							FROM s_order,pdtion, order_partial 
							WHERE  
		          s_order.order_num=pdtion.order_num  AND s_order.order_num = order_partial.ord_num AND
		          pdtion.order_num = order_partial.ord_num AND
		          s_order.status > 5 and s_order.status < 8
		          and (mat_shp IS NULL or mat_shp ='0000-00-00' or m_acc_shp IS NULL or mat_shp ='0000-00-00' or smpl_apv = '0000-00-00' or s_order.status=6)
		          and s_order.factory='".$parm['fty']."'  
		          and ((p_ets <= '".$parm['s_date']."' and p_etf >= '".$parm['e_date']."') or (p_ets >= '".$parm['s_date']."' and p_ets <='".$parm['e_date']."') or (p_etf >= '".$parm['s_date']."' and p_etf<='".$parm['e_date']."'))".$sex."
		          order by order_partial.p_etd, sub_con";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			$rpt_do[]=$row;		   
		}
		if (!empty($rpt_do))
		{
			return $rpt_do;	
		}else{
			return false;
		}
		
} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->rep_nosch_search($parm)	抓出未排程但應排程的訂單(主副料預計到料日有)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function rep_nosch_search($parm,$sex='') {

		$sql = $this->sql;
		$parm['s_date'] = increceMonthInDate($parm['e_date'],2);
		if($sex) $sex = " AND line_sex = '".$sex."'";
		// 關聯式資料庫查尋 ~~~~~~
		$q_str = "SELECT s_order.*, pdtion.*, s_order.id as sid,
										 order_partial.p_su as su, order_partial.p_etd as etd, order_partial.p_etp as etp,
										 order_partial.p_qty as qty, order_partial.p_ets as ets, order_partial.p_etf as etf,
										 order_partial.mks
							FROM s_order,pdtion, order_partial 
							WHERE  
		          s_order.order_num=pdtion.order_num  AND  s_order.order_num = order_partial.ord_num AND
		          order_partial.ord_num = pdtion.order_num AND s_order.status < 6 AND s_order.status >= '0'
		          and (mat_etd IS NOT NULL or m_acc_etd IS NOT NULL or mat_etd = '0000-00-00' or m_acc_etd='0000-00-00')
		          and s_order.factory='".$parm['fty']."' 
		          and ((p_etp <= '".$parm['s_date']."' and p_etd >= '".$parm['e_date']."') or (p_etp >= '".$parm['s_date']."' and p_etp <='".$parm['e_date']."') or (p_etd >= '".$parm['s_date']."' and p_etd<='".$parm['e_date']."'))".$sex."
		          order by order_partial.p_etd";
	
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			$rpt_do[]=$row;		   
		}
		if (!empty($rpt_do))
		{
			return $rpt_do;	
		}else{
			return false;
		}
		
} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->rep_nosch_search($parm)	抓出未排程但應排程的訂單(主副料預計到料日有)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function rep_unasch_search($parm,$sex='') {

		$sql = $this->sql;
		if($sex) $sex = " AND line_sex = '".$sex."'";
		$parm['s_date'] = increceMonthInDate($parm['e_date'],2);
		// 關聯式資料庫查尋 ~~~~~~
		$q_str = "SELECT s_order.*, pdtion.*, s_order.id as sid, 
										 order_partial.p_su as su, order_partial.p_etd as etd, order_partial.p_etp as etp,
										 order_partial.p_qty as qty, order_partial.p_ets as ets, order_partial.p_etf as etf,
										 order_partial.mks
							FROM s_order,pdtion, order_partial 
							WHERE  
		          s_order.order_num=pdtion.order_num  AND s_order.order_num = order_partial.ord_num AND
		          order_partial.ord_num = pdtion.order_num AND s_order.status < 6 AND s_order.status >= '0'
		          and ((mat_etd IS NULL and m_acc_etd IS NULL)or(mat_etd ='0000-00-00' and m_acc_etd = '0000-00-00')  )
		          and s_order.factory='".$parm['fty']."' 
		          and ((etp <= '".$parm['s_date']."' and etd >= '".$parm['e_date']."') or (etp >= '".$parm['s_date']."' and etp <='".$parm['e_date']."') or (etd >= '".$parm['s_date']."' and etd<='".$parm['e_date']."'))".$sex."
		          order by order_partial.p_etd";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			$rpt_do[]=$row;		   
		}
		if (!empty($rpt_do))
		{
			return $rpt_do;	
		}else{
			return false;
		}
		
} // end func
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->cust_os_search($year)	年度每月客戶訂單樣本量
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function cust_os_search($year) {		
		$sql = $this->sql;
		$mm = array('01','02','03','04','05','06','07','08','09','10','11','12');

		// 關聯式資料庫查尋 ~~~~~~
		$q_str= "SELECT cust_s_name as cust, cust_f_name as name , cust.cust_init_name as cust_iname from cust order by cust_s_name";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			$result[]=$row;				
		}
		
		for ($i=0; $i < sizeof($mm); $i++)
		{
			$ym=$year."-".$mm[$i];		
			$q_str = "SELECT cust.cust_s_name AS cust, count( order_num ) AS s_ord, sum( qty ) AS s_qty , cust.cust_init_name as cust_iname, sum( qty*uprice ) AS fob 
  					  FROM s_order JOIN cust ON cust.cust_s_name = s_order.cust AND cust.ver = s_order.cust_ver
  					  WHERE s_order.opendate like '".$ym."%' AND s_order.status >= '0' GROUP BY s_order.cust";	
// echo $q_str.'<br>';
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! Database can't access!");
				$this->msg->merge($sql->msg);
				return false;    
			}
			while ($row = $sql->fetch($q_result)) {
				for ($j=0; $j< sizeof($result); $j++)
				{
					if ($result[$j]['cust'] == $row['cust'])
					{
						$result[$j]['order'][$i] = $row['s_ord'];
						$result[$j]['ord_qty'][$i] = $row['s_qty'];
						$result[$j]['ord_fob'][$i] = $row['fob'];
						break;
					}	
				}
						   
			}
			
			$q_str = "SELECT cust.cust_s_name AS cust, count( num ) AS smpl, sum( qty ) AS smpl_qty , cust.cust_init_name as cust_iname
  					  FROM smpl_ord JOIN cust ON cust.cust_s_name = smpl_ord.cust AND cust.ver = smpl_ord.cust_ver WHERE smpl_ord.open_date like '".$ym."%' GROUP BY smpl_ord.cust";	
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! Database can't access!");
				$this->msg->merge($sql->msg);
				return false;    
			}
            // echo $q_str.'<br>';
			while ($row = $sql->fetch($q_result)) {
				for ($j=0; $j< sizeof($result); $j++)
				{
					if ($result[$j]['cust'] == $row['cust'])
					{
						$result[$j]['smpl'][$i] = $row['smpl'];
						$result[$j]['smpl_qty'][$i] = $row['smpl_qty'];
						break;
					}	
				}
						   
			}

		}
		return $result;
		
} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->os_tal_search($parm)	每月樣本和訂單總量
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function os_tal_search($year) {		
		$sql = $this->sql;	
		$mm = array('01','02','03','04','05','06','07','08','09','10','11','12');

		// 關聯式資料庫查尋 ~~~~~~
		for ($i=0; $i < sizeof($mm); $i++)
		{
			$ym=$year."-".$mm[$i];		
			$q_str = "SELECT count( order_num ) AS s_ord, sum( qty ) AS s_qty  FROM s_order WHERE s_order.opendate like '".$ym."%' AND s_order.status >= '0' ";	
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! Database can't access!");
				$this->msg->merge($sql->msg);
				return false;    
			}
			while ($row = $sql->fetch($q_result)) {
				$result[$i]['order'] = $row['s_ord'];
				$result[$i]['ord_qty'] = $row['s_qty'];
						   
			}
			
			$q_str = "SELECT count( num ) AS smpl, sum( qty ) AS smpl_qty  FROM smpl_ord  WHERE smpl_ord.open_date like '".$ym."%'";	
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! Database can't access!");
				$this->msg->merge($sql->msg);
				return false;    
			}
			while ($row = $sql->fetch($q_result)) {
				$result[$i]['smpl'] = $row['smpl'];
				$result[$i]['smpl_qty'] = $row['smpl_qty'];		
			}

		}

		return $result;
		
} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->un_done_sample($year)	年度樣本未完成量
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function un_done_sample($year) {		
		$sql = $this->sql;	
		$mm = array('01','02','03','04','05','06','07','08','09','10','11','12');

		// 關聯式資料庫查尋 ~~~~~~
		for ($i=0; $i < sizeof($mm); $i++)
		{
			$ym=$year."-".$mm[$i];		
			$q_str = "SELECT sum( qty - qty_done ) AS qty_d  FROM smpl_ord  WHERE smpl_ord.open_date like '".$ym."%' and STATUS < 7 and qty > qty_done";	
			
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! Database can't access!");
				$this->msg->merge($sql->msg);
				return false;    
			}
			
			while ($row = $sql->fetch($q_result)) {
				$result[$i]['qty_undo'] = $row['qty_d'];		
			}

		}

		return $result;
		
} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->qty_unfinsh_search($parm)	抓出未完成的樣本單
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function qty_unfinsh_searche($year,$mm) {		
		$sql = $this->sql;	
		// 關聯式資料庫查尋 ~~~~~~
	
			$ym=$year."-".$mm;		
			$q_str = "SELECT id, num, qty, qty_done, open_date, etd, status, ref, smpl_type, style FROM smpl_ord WHERE smpl_ord.open_date like '".$ym."%' and STATUS < 7 and qty > qty_done order by open_date";
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! Database can't access!");
				$this->msg->merge($sql->msg);
				return false;    
			}
			
			while ($row = $sql->fetch($q_result)) {
				$result[] = $row;		
			}
		return $result;		
} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->cust_smplqty_searche($year,$cust)	各別客戶樣本資訊
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function cust_smplqty_searche($year,$cust) {		
		$sql = $this->sql;	
		// 關聯式資料庫查尋 ~~~~~~
	
			$ym=$year."-";		
			$q_str = "SELECT id, num, qty, status, qty_done, open_date, etd, status, ref, smpl_type, style FROM smpl_ord WHERE smpl_ord.open_date like '".$ym."%' and cust = '".$cust."' order by open_date";
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! Database can't access!");
				$this->msg->merge($sql->msg);
				return false;    
			}
			
			while ($row = $sql->fetch($q_result)) {
				$result[] = $row;		
			}
		return $result;		
} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->order_day_list($search_day)	年度訂單明細
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function order_day_list($search_day,$fty) {		
		$sql = $this->sql;	
		// 關聯式資料庫查尋 ~~~~~~			
			$q_str = "SELECT  *, s_order.order_num as ord_num, cust.cust_init_name as cust_init, s_order.id as s_id 
							  FROM  cust, s_order LEFT JOIN pdtion ON s_order.order_num = pdtion.order_num
								WHERE cust.cust_s_name=s_order.cust  AND 
											s_order.cust_ver = cust.ver AND s_order.status >= '3' AND 
											etd like '".$search_day."%'  AND s_order.factory = '".$fty."'
								order by cust , etd";

			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! Database can't access!");
				$this->msg->merge($sql->msg);
				return false;    
			}
			
			while ($row = $sql->fetch($q_result)) {
				$result[] = $row;		
			}
		if (!empty($result))
		{
			return $result;		
		}else{
			false;
		}
} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->order_cust_day_list($search_day,$cust)	年度訂單明細(依年份及客戶)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function order_cust_day_list($search_day,$cust,$fty) {		
		$sql = $this->sql;	
		// 關聯式資料庫查尋 ~~~~~~			
			$q_str = "SELECT  *, s_order.order_num as ord_num, cust.cust_init_name as cust_init, s_order.id as s_id 
			          FROM s_order, cust left join pdtion ON s_order.order_num = pdtion.order_num 
			          WHERE cust.cust_s_name=s_order.cust  AND s_order.cust_ver = cust.ver and 
			          etd like '".$search_day."%' and s_order.cust = '$cust' AND 
			          s_order.status >= '3' AND s_order.factory = '".$fty."'
			          order by cust , etd";

			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! Database can't access!");
				$this->msg->merge($sql->msg);
				return false;    
			}
			
			while ($row = $sql->fetch($q_result)) {
				$result[] = $row;		
			}
		if (!empty($result))
		{
			return $result;		
		}else{
			false;
		}
} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->order_cust_day_list($search_day,$cust)	年度訂單明細(依年份及客戶)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function smpl_use_search($search_day,$where_str='') {		
		$sql = $this->sql;	
		// 關聯式資料庫查尋 ~~~~~~			
/*
		$q_str = "SELECT  cust.cust_init_name as cust_init, cust, smpl_ord.num, qty, orders 
							FROM smpl_ord, cust 
							WHERE cust.cust_s_name=smpl_ord.cust  AND smpl_ord.cust_ver = cust.ver AND  open_date >= '".$search_day."'". $where_str .
						" ORDER BY cust, num desc ";
*/
		$q_str = "SELECT  cust.cust_init_name as cust_init, cust, max(smpl_ord.num) as num, 
									    count(smpl_ord.num) as num_count, SUM(qty) as qty, max(orders) as orders,
									    SUBSTRING( num, 1, 9 )as num_init
							FROM smpl_ord, cust 
							WHERE cust.cust_s_name=smpl_ord.cust  AND smpl_ord.cust_ver = cust.ver AND  last_update >= '".$search_day."'". $where_str .
						" GROUP BY SUBSTRING( num, 1, 9 ) 
						  ORDER BY cust, num desc ";

//	echo $q_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$i=0;	
		while ($row = $sql->fetch($q_result)) {
			$result[] = $row;				
		}
		
		
		
		if (!empty($result))
		{
			return $result;		
		}else{
			false;
		}
} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->order_cust_day_list($search_day,$cust)	年度訂單明細(依年份及客戶)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function style_out_search($fty,$start,$finish) {		
		$sql = $this->sql;	
		// 關聯式資料庫查尋 ~~~~~~			
/*
		$q_str = "SELECT SUM((qty/(TO_DAYS(finish)-TO_DAYS(start)+1))) as s_qty, SUM((su/(TO_DAYS(finish)-TO_DAYS(start)+1))) as s_su, 
				         COUNT(pdtion.order_num) as order_count, style_type.des as style,sum(qty) as sum_qty, SUM(su) as sum_su  
				      FROM pdtion, s_order , style_type
				  WHERE status > 8 and start >= '$start' and finish <= '$finish' and s_order.factory = '$fty' 
				  and pdtion.sub_con	= 0 and s_order.order_num = pdtion.order_num  and s_order.style = style_type.style_type
				  GROUP BY style ";
*/	
		$q_str = "SELECT style_type.des as style, sum(saw_out_put.qty) as qty, sum(saw_out_put.su) as su,
									   avg(saw_out_put.qty) as avg_qty, avg(saw_out_put.su) as avg_su, order_num,
									   s_order.style as s_style
				      FROM saw_out_put, s_order , style_type, pdt_saw_line
				      WHERE s_order.order_num = saw_out_put.ord_num AND saw_out_put.line_id = pdt_saw_line.id AND
				            s_order.style = style_type.style_type AND status > 8 AND
				            saw_out_put.out_date >= '$start' AND saw_out_put.out_date <= '$finish' AND 
				            s_order.factory = '$fty'  AND holiday	= 0 AND pdt_saw_line.sc = 0
				      GROUP BY order_num";		
	      	  
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$i=0;	
		
		while ($row = $sql->fetch($q_result)) {
			$result[$row['s_style']][] = $row;				
		}
		
		
		
		if (!empty($result))
		{
			return $result;		
		}else{
			false;
		}
} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->ord_qty_cut_search($search_day,$cust)	依訂單量計算計單張數
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function ord_qty_cut_search($fty,$start,$finish, $dist, $where_str='') {		
		$sql = $this->sql;
        //print_r($dist);
        //echo 	$where_str;
		// 關聯式資料庫查尋 ~~~~~~			
		// Q'ty < 1000
		$q_str = "SELECT COUNT(id)as ord_c FROM s_order WHERE qty < '".$dist[0]."'"
			 ." and etd >= '$start' and etd <= '$finish' and factory = '$fty' and status >= 4 and status <> 5 ".$where_str;
        /*$q_str = "SELECT Count(DISTINCT order_num) as ord_c FROM s_order, order_partial ";
        $q_str .= "WHERE s_order.order_num = order_partial.ord_num ";
        $q_str .= "AND order_partial.p_qty < '".$dist[0]."'";
        $q_str .= " AND order_partial.p_etd >= '".$start."' AND order_partial.p_etd <= '".$finish."'";
        $q_str .= " AND s_order.factory='".$fty."'";
        $q_str .= " AND s_order.status >= 4 AND s_order.status <> 5 ";
        $q_str .= $where_str;
        echo '<br>';
        echo $q_str;*/
		
        if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$row = $sql->fetch($q_result);	
		$result[0] = $row['ord_c'];		
		
		//1000>= Q'ty < 1500
		$q_str = "SELECT COUNT(id)as ord_c FROM s_order WHERE qty >='".$dist[0]."' and qty < '".$dist[1]."'"
			 ." and etd >= '$start' and etd <= '$finish' and factory = '$fty'  and status >= 4 and status <> 5 ".$where_str;
        /*$q_str = "SELECT Count(DISTINCT order_num) as ord_c FROM s_order, order_partial ";
        $q_str .= "WHERE s_order.order_num = order_partial.ord_num ";
        $q_str .= "AND order_partial.p_qty >= '".$dist[0]."' AND order_partial.p_qty < '".$dist[1]."'";
        $q_str .= " AND order_partial.p_etd >= '".$start."' AND order_partial.p_etd <= '".$finish."'";
        $q_str .= " AND s_order.factory='".$fty."'";
        $q_str .= " AND s_order.status >= 4 AND s_order.status <> 5 ";
        $q_str .= $where_str;*/
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$row = $sql->fetch($q_result);	
		$result[1] = $row['ord_c'];		

		//1500>= Q'ty < 3000
		$q_str = "SELECT COUNT(id)as ord_c FROM s_order WHERE qty >='".$dist[1]."' and qty < '".$dist[2]."'"
			 ." and etd >= '$start' and etd <= '$finish' and factory = '$fty'  and status >= 4 and status <> 5 ".$where_str;
        /*$q_str = "SELECT Count(DISTINCT order_num) as ord_c FROM s_order, order_partial ";
        $q_str .= "WHERE s_order.order_num = order_partial.ord_num ";
        $q_str .= "AND order_partial.p_qty >= '".$dist[1]."' AND order_partial.p_qty < '".$dist[2]."'";
        $q_str .= " AND order_partial.p_etd >= '".$start."' AND order_partial.p_etd <= '".$finish."'";
        $q_str .= " AND s_order.factory='".$fty."'";
        $q_str .= " AND s_order.status >= 4 AND s_order.status <> 5 ";
        $q_str .= $where_str;*/
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$row = $sql->fetch($q_result);	
		$result[2] = $row['ord_c'];		

		//3000>= Q'ty < 5000
		$q_str = "SELECT COUNT(id)as ord_c FROM s_order WHERE qty >='".$dist[2]."' and qty < '".$dist[3]."'"
			 ." and etd >= '$start' and etd <= '$finish' and factory = '$fty'  and status >= 4 and status <> 5 ".$where_str;
        /*$q_str = "SELECT Count(DISTINCT order_num) as ord_c FROM s_order, order_partial ";
        $q_str .= "WHERE s_order.order_num = order_partial.ord_num ";
        $q_str .= "AND order_partial.p_qty >= '".$dist[2]."' AND order_partial.p_qty < '".$dist[3]."'";
        $q_str .= " AND order_partial.p_etd >= '".$start."' AND order_partial.p_etd <= '".$finish."'";
        $q_str .= " AND s_order.factory='".$fty."'";
        $q_str .= " AND s_order.status >= 4 AND s_order.status <> 5 ";
        $q_str .= $where_str;*/
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$row = $sql->fetch($q_result);	
		$result[3] = $row['ord_c'];		

		//3000>= Q'ty < 5000
		//$q_str = "SELECT COUNT(id)as ord_c FROM s_order WHERE qty >='".$dist[3]."' and qty < '".$dist[4]."'"
		//	 ." and etd >= '$start' and etd <= '$finish' and factory = '$fty'  and status >= 4 and status <> 5 ".$where_str;
		/**
 * if (!$q_result = $sql->query($q_str)) {
 * 			$this->msg->add("Error ! Database can't access!");
 * 			$this->msg->merge($sql->msg);
 * 			return false;    
 * 		}
 */
		//$row = $sql->fetch($q_result);	
		//$result[4] = $row['ord_c'];		

			//Q'ty >= 5000
		$q_str = "SELECT COUNT(id)as ord_c FROM s_order WHERE qty >='".$dist[3]."'"
			 ." and etd >= '$start' and etd <= '$finish' and factory = '$fty'  and status >= 4 and status <> 5 ".$where_str;
        /*$q_str = "SELECT Count(DISTINCT order_num) as ord_c FROM s_order, order_partial ";
        $q_str .= "WHERE s_order.order_num = order_partial.ord_num ";
        $q_str .= "AND order_partial.p_qty >= '".$dist[3]."'";
        $q_str .= " AND order_partial.p_etd >= '".$start."' AND order_partial.p_etd <= '".$finish."'";
        $q_str .= " AND s_order.factory='".$fty."'";
        $q_str .= " AND s_order.status >= 4 AND s_order.status <> 5 ";
        $q_str .= $where_str;*/
        //echo $q_str;
        //exit;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$row = $sql->fetch($q_result);	
		$result[4] = $row['ord_c'];		
	
		if (!empty($result))
		{
			return $result;		
		}else{
			false;
		}
} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->ord_qty_cut_search($search_day,$cust)	依訂單量計算計單張數
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function ord_style_qty($fty,$start,$finish, $dist, $type, $where_str='') {		
		$sql = $this->sql;	
		// 關聯式資料庫查尋 ~~~~~~			
		// Q'ty < 1000
		$q_str = "SELECT COUNT(s_order.id)as ord_c FROM s_order, style_type 
							WHERE s_order.style = style_type.style_type AND style_type.memo = '".$type."' AND  
										qty < '".$dist[0]."' and etd >= '$start' and etd <= '$finish' and 
										factory = '$fty' and status >= 4 and status <> 5 ".$where_str;
//echo $q_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$row = $sql->fetch($q_result);	
		$result[0] = $row['ord_c'];		
		
		//1000<= Q'ty < 2000
		$q_str = "SELECT COUNT(s_order.id)as ord_c FROM s_order, style_type 
							WHERE s_order.style = style_type.style_type AND style_type.memo = '".$type."' AND 
										qty >='".$dist[0]."' and qty < '".$dist[1]."' and etd >= '$start' and 
										etd <= '$finish' and factory = '$fty'  and status >= 4 and status <> 5 ".$where_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$row = $sql->fetch($q_result);	
		$result[1] = $row['ord_c'];		

		//2000<= Q'ty < 3000
		$q_str = "SELECT COUNT(s_order.id)as ord_c FROM s_order, style_type 
							WHERE s_order.style = style_type.style_type AND style_type.memo = '".$type."' AND 
										qty >='".$dist[1]."' and qty < '".$dist[2]."' and etd >= '$start' and 
										etd <= '$finish' and factory = '$fty'  and status >= 4 and status <> 5 ".$where_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$row = $sql->fetch($q_result);	
		$result[2] = $row['ord_c'];		

		//3000<= Q'ty < 4000
		$q_str = "SELECT COUNT(s_order.id)as ord_c FROM s_order, style_type 
							WHERE s_order.style = style_type.style_type AND style_type.memo = '".$type."' AND 
										qty >='".$dist[2]."' and qty < '".$dist[3]."' and etd >= '$start' and 
										etd <= '$finish' and factory = '$fty'  and status >= 4 and status <> 5 ".$where_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$row = $sql->fetch($q_result);	
		$result[3] = $row['ord_c'];		

			//Q'ty >= 5000
		$q_str = "SELECT COUNT(s_order.id)as ord_c FROM s_order, style_type 
							WHERE s_order.style = style_type.style_type AND style_type.memo = '".$type."' AND 
										qty >='".$dist[3]."' and etd >= '$start' and etd <= '$finish' and 
										factory = '$fty'  and status >= 4 and status <> 5 ".$where_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$row = $sql->fetch($q_result);	
		$result[4] = $row['ord_c'];		
	
		if (!empty($result))
		{
			return $result;		
		}else{
			false;
		}
} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search_f_ord_tbl($sch_mon='')
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function search_f_ord_tbl($sch_mon='',$target='',$sday='',$PHP_cust='',$PHP_user='') {

	$where_str = '';
	$sql = $this->sql;
	
	$TODAY = date('Y-m-d');
	$DEPT = ($GLOBALS['SCACHE']['ADMIN']['dept'] == 'HJ' || $GLOBALS['SCACHE']['ADMIN']['dept'] == 'LY'|| $GLOBALS['SCACHE']['ADMIN']['dept'] == 'CF'|| $GLOBALS['SCACHE']['ADMIN']['dept'] == 'V1') ? " AND s_order.factory = '".$GLOBALS['SCACHE']['ADMIN']['dept']."' " : "" ;
	
	if($sch_mon)$where_str = " AND s_order.etd like '".date('Y').'-'.$sch_mon."%' ";
	
	#M11010501 修改大表區隔針織和平織的顯示

	if(!empty($_SESSION['pre_tal_type'])) {
		if ( $_SESSION['pre_tal_type'] == 'LY' ) {
			$where_str = " AND s_order.factory = 'LY' ".$where_str;
		} else if ( $_SESSION['pre_tal_type'] == 'CF' ) {
			$where_str = " AND s_order.factory = 'CF' ".$where_str;
		} else if ( $_SESSION['pre_tal_type'] == 'HJ' ) {
			$where_str = " AND s_order.factory = 'HJ' ".$where_str;
		} else {
			$where_str = $where_str;
		}
	}
    
    if(!empty($PHP_cust)) {
        $where_str .= " AND `s_order`.`cust` = '".$PHP_cust."' ";
    }
    
    if(!empty($PHP_user)) {
        $where_str .= " AND `s_order`.`creator` = '".$PHP_user."' ";
    }
	
	// 關聯式資料庫查尋 ~~~~~~ style mks
	$q_str = "SELECT s_order.order_num, s_order.style_num, s_order.ref,  s_order.style, 
                    s_order.factory, s_order.status, s_order.partial_num, s_order.smpl_apv,
                    s_order.ptn_upload, s_order.smpl_ord, 



                    pdtion.check_lots_po_etd, pdtion.check_lots_shp_eta,  pdtion.check_lots_rcv_date,
                    pdtion.check_acc_po_etd, pdtion.check_acc_shp_eta,  pdtion.check_acc_rcv_date,
                    pdtion.lots_po_etd, pdtion.lots_shp_eta,  pdtion.lots_rcv_date,
                    pdtion.acc_po_etd, pdtion.acc_shp_eta,  pdtion.acc_rcv_date,



                    pdtion.mat_etd, pdtion.sub_con,  pdtion.in_house, pdtion.m_acc_rcv,
                    pdtion.mat_eta, pdtion.mat_shp, pdtion.m_acc_etd, pdtion.m_acc_eta, 
                    pdtion.m_acc_shp,  pdtion.mat_rcv,  pdtion.mat_ship_way, pdtion.m_acc_ship_way,
                    pdtion.qty_cut, style_type.memo, 
                    wi.ti_cfm,wi.id as wi_id,
                    order_partial.p_etd as etd, order_partial.mks, order_partial.p_ets as ets,
                    order_partial.p_qty as qty, order_partial.p_su as su, order_partial.p_etf as etf,
                    order_partial.p_qty_done as qty_done
    FROM s_order,order_partial, style_type, pdtion LEFT JOIN wi ON wi.wi_num = pdtion.order_num
    WHERE style_type.style_type = s_order.style and pdtion.order_num = s_order.order_num and 
                order_partial.ord_num = s_order.order_num AND order_partial.ord_num = pdtion.order_num AND
                s_order.status > 3 and s_order.status <> 5 and s_order.status <> 14 and order_partial.pdt_status < 4 and 
                order_partial.p_etd >= '".$sday."' and s_order.line_sex = 'F' ".$where_str.$DEPT."
    ORDER BY s_order.factory desc , YEAR(order_partial.p_etd), MONTH(order_partial.p_etd)";
    
	if($target=='leadtime')
		$q_str .= ", order_partial.p_etd";
		
	if( $target == 'style' || $target == '' )
		$q_str .= ", style_type.memo DESC , order_partial.p_etd";

	// echo $q_str; # mat_shp
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	$i = $qty = $cout = $su = $dd = 0;
	$rpt = array();
    
    # 抓取狀態陣列
    $status = get_status();
    
	while ($row = $sql->fetch($q_result)) {		
		// if( ( $row['mat_eta'] ) && (!$row['mat_shp'] || $row['mat_shp'] == '1111-11-11')){
            // $row['mat_shp'] = $GLOBALS['order']->reset_ship_day('l',$row['wi_id']);
            // $row['mat_shp'] = $GLOBALS['order']->add_non_rcvd('l',$row['order_num']);
        // }	
		// if( !$row['mat_eta'] || $row['mat_eta'] == '0000-00-00' || !$row['mat_shp'] || $row['mat_shp'] == '1111-11-11' ){
            // $row['mat_shp'] = $GLOBALS['order']->reset_pdtion('l',$row['wi_id']);
            // $row['mat_shp'] = $GLOBALS['order']->add_non_rcvd('l',$row['order_num']);
        // }
        
		// if($row['m_acc_eta'] && (!$row['m_acc_shp'] ||$row['m_acc_shp'] == '1111-11-11')){
            // $row['m_acc_shp'] = $GLOBALS['order']->reset_ship_day('a',$row['wi_id']);
            // $row['m_acc_shp'] = $GLOBALS['order']->add_non_rcvd('a',$row['order_num'],2);
        // }

		$tmp_date=countDays($TODAY,$row['etd']);  
		$rpt[$i]=$row;
		$rpt[$i]['lead'] = $tmp_date;
		$rpt[$i]['rmk'] = $status[$row['status']];
		$j=$i-1;

		if ( $j < 0 ) $j=0;
		$tmp=$rpt[$j]['factory'];
		if ( $rpt[$i]['factory'] <> $tmp ) {
			$rpt[$j]['ln_mk']=1;
			$rpt[$j]['qty_tal']=$qty;
			$rpt[$j]['su_tal']=$su;
			$rpt[$j]['count']=$cout;
			$qty=0;
			$su=0;
			$cout=0;
			$dd=0;
		}

		if ( substr($rpt[$i]['etd'],0,7) <> $dd ) {
			if ($dd <> 0)$rpt[$j]['ln_mon']=1;
			$dd = substr($rpt[$i]['etd'],0,7);
		}

		$i++;
		$cout++;
		$qty=$qty+$row['qty'];
		$su=$su+$row['su'];
	}

	if ( !empty($rpt) ) {
        $j=$i-1;
        $rpt[$j]['ln_mk']=1;
        $rpt[$j]['qty_tal']=$qty;
        $rpt[$j]['su_tal']=$su;
        $rpt[$j]['count']=$cout;

		return $rpt;
	} else {
		return false;
	}
} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->rep_unsch_search($parm)	抓出己排程但不可生產的訂單(主副料和樣本核可 無) -- 女裝線
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function search_m_ord_tbl($sch_mon='',$target='',$sday='',$PHP_cust='',$PHP_user='') {

    $where_str = '';
    $sql = $this->sql;
    
    $TODAY = date('Y-m-d');
    $DEPT = ($GLOBALS['SCACHE']['ADMIN']['dept'] == 'HJ' || $GLOBALS['SCACHE']['ADMIN']['dept'] == 'LY'|| $GLOBALS['SCACHE']['ADMIN']['dept'] == 'CF'|| $GLOBALS['SCACHE']['ADMIN']['dept'] == 'V1') ? " AND s_order.factory = '".$GLOBALS['SCACHE']['ADMIN']['dept']."' " : "" ;

    if($sch_mon)$where_str = " AND s_order.etd like '".date('Y').'-'.$sch_mon."%' ";

    #M11010501 修改大表區隔針織和平織的顯示
    if(!empty($_SESSION['pre_tal_type'])) {
		if ( $_SESSION['pre_tal_type'] == 'LY' ) {
			$where_str = " AND s_order.factory = 'LY' ".$where_str;
		} else if ( $_SESSION['pre_tal_type'] == 'CF' ) {
			$where_str = " AND s_order.factory = 'CF' ".$where_str;
		} else if ( $_SESSION['pre_tal_type'] == 'HJ' ) {
			$where_str = " AND s_order.factory = 'HJ' ".$where_str;
		} else {
			$where_str = $where_str;
		}
    }
    
    if(!empty($PHP_cust)) {
        $where_str .= " AND `s_order`.`cust` = '".$PHP_cust."' ";
    }
    
    if(!empty($PHP_user)) {
        $where_str .= " AND `s_order`.`creator` = '".$PHP_user."' ";
    }
    
    // 關聯式資料庫查尋 ~~~~~~
    $q_str = "SELECT s_order.order_num, s_order.style_num, s_order.ref,  s_order.style, 
                    s_order.factory, s_order.status, s_order.partial_num, s_order.smpl_apv,
                    s_order.ptn_upload, s_order.smpl_ord,



                    pdtion.check_lots_po_etd, pdtion.check_lots_shp_eta,  pdtion.check_lots_rcv_date,
                    pdtion.check_acc_po_etd, pdtion.check_acc_shp_eta,  pdtion.check_acc_rcv_date,
                    pdtion.lots_po_etd, pdtion.lots_shp_eta,  pdtion.lots_rcv_date,
                    pdtion.acc_po_etd, pdtion.acc_shp_eta,  pdtion.acc_rcv_date,



                    pdtion.mat_etd, pdtion.sub_con,  pdtion.in_house, pdtion.m_acc_rcv,
                    pdtion.mat_eta, pdtion.mat_shp, pdtion.m_acc_etd, pdtion.m_acc_eta, 
                    pdtion.m_acc_shp,  pdtion.mat_rcv,  pdtion.mat_ship_way, pdtion.m_acc_ship_way,																						 
                    ti_cfm, pdtion.qty_cut, style_type.memo, 
                    order_partial.p_etd as etd, order_partial.mks, order_partial.p_ets as ets,
                    order_partial.p_qty as qty, order_partial.p_su as su, order_partial.p_etf as etf,
                    order_partial.p_qty_done as qty_done ,
                    wi.id as wi_id
    FROM s_order,order_partial, style_type, pdtion LEFT JOIN wi ON wi.wi_num = pdtion.order_num
    WHERE style_type.style_type = s_order.style and pdtion.order_num = s_order.order_num and 
                order_partial.ord_num = s_order.order_num AND order_partial.ord_num = pdtion.order_num AND
                s_order.status > 3 and s_order.status <> 5 and s_order.status <> 14 and order_partial.pdt_status < 4 and 
                order_partial.p_etd >= '".$sday."' and s_order.line_sex = 'M' ".$where_str.$DEPT."
    ORDER BY s_order.factory desc, YEAR(order_partial.p_etd), MONTH(order_partial.p_etd)";

    if($target=='leadtime')
        $q_str .= ", order_partial.p_etd";
        
    if($target=='style' || $target=='')
        $q_str .= ", style_type.memo DESC , order_partial.p_etd";
    
    // echo $q_str;

    if (!$q_result = $sql->query($q_str)) {
        $this->msg->add("Error ! Database can't access!");
        $this->msg->merge($sql->msg);
        return false;    
    }
    
    $i = $qty = $cout = $su = $dd = 0;
	$rpt = $mode = array();
    
    # 抓取狀態陣列
    $status = get_status();

    while ($row = $sql->fetch($q_result)) {
    
        if ( !empty($mode[$row['order_num']]) )
            $mode[$row['order_num']] ++ ;
        else
            $mode[$row['order_num']] = 1;
            
        // $row['mat_shp'] = $GLOBALS['order']->reset_pdtion('l',$row['wi_id']);
            
        $tmp_date=countDays($TODAY,$row['etd']);  
        $rpt[$i]=$row;
        $rpt[$i]['lead'] = $tmp_date;
        $rpt[$i]['rmk'] = $status[$row['status']];
        $j=$i-1;
        if ($j < 0) $j=0;
        $tmp=$rpt[$j]['factory'];
        if ($rpt[$i]['factory'] <> $tmp) 
        {
            $rpt[$j]['ln_mk']=1;
            $rpt[$j]['qty_tal']=$qty;
            $rpt[$j]['su_tal']=$su;
            $rpt[$j]['count']=$cout;
            $qty=0;
            $su=0;
            $cout=0;
            $dd=0;
        }
        if (substr($rpt[$i]['etd'],0,7) <> $dd) 
        {
            if ($dd <> 0)$rpt[$j]['ln_mon']=1;
            $dd = substr($rpt[$i]['etd'],0,7);
        }

        $i++;
        $cout++;
        $qty=$qty+$row['qty'];
        $su=$su+$row['su'];
    }
    
    if (!empty($rpt))
    {
        $j=$i-1;
        $rpt[$j]['ln_mk']=1;
        $rpt[$j]['qty_tal']=$qty;
        $rpt[$j]['su_tal']=$su;
        $rpt[$j]['count']=$cout;		
        

        foreach($rpt as $key => $val){
            if( !empty($val['order_num']) ){
                if( $mode[$val['order_num']] > 1 )
                    $rpt[$key]['mode'] = 1;
            }
        }

        return $rpt;	
    }else{
        return false;
    }
    
} // end func




#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search_ord_exceptional($sch_mon='')
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function search_ord_exceptional() {
	$where_str = '';
	$sql = $this->sql;
	

	$TODAY = date('Y-m-d');
	if($sch_mon)$where_str = " AND s_order.etd like '".date('Y').'-'.$sch_mon."%' ";
	
	#M11010501 修改大表區隔針織和平織的顯示
	if(!empty($_SESSION['pre_tal_type'])) {
		if ( $_SESSION['pre_tal_type'] == 'LY' ) {
			$where_str = " AND s_order.factory = 'LY' ".$where_str;
		} else if ( $_SESSION['pre_tal_type'] == 'CF' ) {
			$where_str = " AND s_order.factory = 'CF' ".$where_str;
		} else if ( $_SESSION['pre_tal_type'] == 'HJ' ) {
			$where_str = " AND s_order.factory = 'HJ' ".$where_str;
		} else {
			$where_str = $where_str;
		}
	}
	
	// 關聯式資料庫查尋 ~~~~~~ style
	$q_str = "SELECT s_order.order_num, s_order.style_num, s_order.ref,  s_order.style, 
										s_order.factory, s_order.status, s_order.partial_num, s_order.smpl_apv,
										s_order.ptn_upload, s_order.smpl_ord,
										pdtion.mat_etd, pdtion.sub_con,  pdtion.in_house, pdtion.m_acc_rcv,
										pdtion.mat_eta, pdtion.mat_shp, pdtion.m_acc_etd, pdtion.m_acc_eta, 
										pdtion.m_acc_shp,  pdtion.mat_rcv,  pdtion.mat_ship_way, pdtion.m_acc_ship_way,																						 
										ti_cfm, pdtion.qty_cut, style_type.memo, 
										order_partial.p_etd as etd, order_partial.mks, order_partial.p_ets as ets,
										order_partial.p_qty as qty, order_partial.p_su as su, order_partial.p_etf as etf,
										order_partial.p_qty_done as qty_done
						FROM s_order,order_partial, style_type, pdtion LEFT JOIN wi ON wi.wi_num = pdtion.order_num
						WHERE style_type.style_type = s_order.style and pdtion.order_num = s_order.order_num and 
									order_partial.ord_num = s_order.order_num AND order_partial.ord_num = pdtion.order_num AND
									s_order.status = 14 AND 
									s_order.dept <> 'HJ'".$where_str."
						ORDER BY s_order.factory desc , YEAR(order_partial.p_etd), MONTH(order_partial.p_etd)";
	if($target=='leadtime')
		$q_str .= ", order_partial.p_etd";
		
	if($target=='style' || $target=='')
		$q_str .= ", style_type.memo DESC , order_partial.p_etd";

	// echo $q_str;
	
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	$i=0;
	$qty=0;
	$cout=0;
	$su=0;
	$dd=0;
	
	$rpt = $mode = array();
	while ($row = $sql->fetch($q_result)) {		
		if($row['mat_eta'] && (!$row['mat_shp'] || $row['mat_shp'] == '1111-11-11')) $row['mat_shp'] = $GLOBALS['order']->add_non_rcvd('l',$row['order_num']);
		if($row['m_acc_eta'] && (!$row['m_acc_shp'] ||$row['m_acc_shp'] == '1111-11-11')) $row['m_acc_shp'] = $GLOBALS['order']->add_non_rcvd('a',$row['order_num'],2);
		
		// if ( !empty($mode[$row['order_num']]) )
			// $mode[$row['order_num']] ++ ;
		// else
			// $mode[$row['order_num']] = 1;
		
		$tmp_date=countDays($TODAY,$row['etd']);  
		$rpt[$i]=$row;
		$rpt[$i]['lead'] = $tmp_date;
		$rpt[$i]['rmk'] = get_ord_status($row['status']);
		$j=$i-1;
		if ($j < 0) $j=0;
		$tmp=$rpt[$j]['factory'];
		if ($rpt[$i]['factory'] <> $tmp) 
		{
			$rpt[$j]['ln_mk']=1;
			$rpt[$j]['qty_tal']=$qty;
			$rpt[$j]['su_tal']=$su;
			$rpt[$j]['count']=$cout;
			$qty=0;
			$su=0;
			$cout=0;
			$dd=0;
		}

		if (substr($rpt[$i]['etd'],0,7) <> $dd) 
		{
			if ($dd <> 0)$rpt[$j]['ln_mon']=1;
			$dd = substr($rpt[$i]['etd'],0,7);
		}

		$i++;
		$cout++;
		$qty=$qty+$row['qty'];
		$su=$su+$row['su'];
	}

	if (!empty($rpt))
	{
    
        $j=$i-1;
        $rpt[$j]['ln_mk']=1;
        $rpt[$j]['qty_tal']=$qty;
        $rpt[$j]['su_tal']=$su;
        $rpt[$j]['count']=$cout;
        
        // foreach($rpt as $key => $val){
            // if( !empty($val['order_num']) ){
                // if( $mode[$val['order_num']] > 1 )
                    // $rpt[$key]['mode'] = 1;
            // }
        // }
	

		return $rpt;
	}else{
		return false;
	}
} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->rep_unsch_search($parm)	抓出己排程但不可生產的訂單(主副料和樣本核可 無) -- 女裝線
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_ord_pend($sday) {

		$sql = $this->sql;
//		$parm['s_date'] = increceMonthInDate($parm['e_date'],2);
		$TODAY = date('Y-m-d');
		$i = 0;
		$fty_str = " AND s_order.dept <> s_order.factory";
		$DEPT = ($GLOBALS['SCACHE']['ADMIN']['dept'] == 'HJ' || $GLOBALS['SCACHE']['ADMIN']['dept'] == 'LY'|| $GLOBALS['SCACHE']['ADMIN']['dept'] == 'CF'|| $GLOBALS['SCACHE']['ADMIN']['dept'] == 'V1') ? " AND s_order.factory = '".$GLOBALS['SCACHE']['ADMIN']['dept']."' " : "" ;

		// 未建立WI訂單
		$q_str = "SELECT s_order.order_num, s_order.etd, s_order.opendate, s_order.style, 
										 s_order.id, s_order.status 
							FROM s_order LEFT JOIN wi ON s_order.order_num = wi.wi_num
							WHERE s_order.status > 1 AND s_order.status < 10 AND										 
										s_order.opendate > '".$sday."' AND wi.id IS NULL ".$fty_str . $DEPT;
	
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}

		while ($row = $sql->fetch($q_result)) {		
			$tmp_date=countDays($TODAY,$row['opendate']);  
			$rpt[$i]=$row;
			$rpt[$i]['spend'] = countDays($TODAY,$row['opendate']) * -1;
			$rpt[$i]['lead'] = countDays($TODAY,$row['etd']);
			$rpt[$i]['status'] = get_ord_status($rpt[$i]['status']);
			$rpt[$i]['sort'] = countDays($TODAY,$row['etd']) * -1;
			$i++;
		}

		// BOM未核可訂單
		$q_str = "SELECT s_order.order_num, s_order.etd, s_order.opendate, s_order.style,
										 s_order.id, s_order.status 
							FROM s_order, wi 
							WHERE s_order.order_num = wi.wi_num AND s_order.status > 1 AND 										
										s_order.status < 10 AND s_order.opendate > '".$sday."' AND wi.status < 2 ".$fty_str . $DEPT;
	
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}

		while ($row = $sql->fetch($q_result)) {		
			$tmp_date=countDays($TODAY,$row['opendate']);  
			$rpt[$i]=$row;
			$rpt[$i]['spend'] = countDays($TODAY,$row['opendate']) * -1;
			$rpt[$i]['lead'] = countDays($TODAY,$row['etd']);
			$rpt[$i]['status'] = get_ord_status($rpt[$i]['status']);
			$rpt[$i]['sort'] = countDays($TODAY,$row['etd']) * -1;
			$i++;
		}		


		// BOM未核可訂單
		$q_str = "SELECT s_order.order_num, s_order.etd, s_order.opendate, wi.id as wi_id, s_order.style,
										 wi.bcfm_date, s_order.id, s_order.status 
							FROM s_order, wi 
							WHERE s_order.order_num = wi.wi_num AND s_order.status > 1 AND 										
										s_order.status < 10 AND s_order.opendate > '".$sday."' AND wi.status >= 2".$fty_str . $DEPT;
	
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}

		while ($row = $sql->fetch($q_result)) {		
			$fab =  $GLOBALS['bom']->get_aply($row['wi_id'], 'bom_lots','wi_id');
			$m_acc = $GLOBALS['bom']->get_aply($row['wi_id'], 'bom_acc','wi_id',1);
			$o_acc = $GLOBALS['bom']->get_aply($row['wi_id'], 'bom_acc','wi_id',0);
			if($fab == 0 || $m_acc == 0 || $o_acc == 0 )
			{
				$rpt[$i]=$row;
				$rpt[$i]['spend'] = countDays($TODAY,$row['opendate']) * -1;
				$rpt[$i]['lead'] = countDays($TODAY,$row['etd']);
				$rpt[$i]['status'] = get_ord_status($rpt[$i]['status']);
				$rpt[$i]['fab'] = $fab;
				$rpt[$i]['m_acc'] = $m_acc;
				$rpt[$i]['o_acc'] = $o_acc;
				$rpt[$i]['sort'] = countDays($TODAY,$row['etd']) * -1;
				$i++;
			}			
		}	



		if (!empty($rpt))
		{
			return $rpt;	
		}else{
			return false;
		}
		
} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search_po_pend()
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_po_pend() {

		$sql = $this->sql;
//		$parm['s_date'] = increceMonthInDate($parm['e_date'],2);
		$TODAY = date('Y-m-d');
		$i = 0;
		$rpt = array();
		// 前版本未沖銷主料
		$q_str = "SELECT DISTINCT ap_det.po_eta, ap_det.ap_num,	 ap.po_num, ap_det.mat_cat,
										 ap.currency, ap.status, ap.dear, ap.arv_area, ap.ship_name, ap.sup_code,
										 wi.wi_num as smpl_code
							FROM bom_lots, ap_det, ap, wi
							WHERE bom_lots.id = ap_det.bom_id AND wi.id = bom_lots.id
								AND ap.status >= 0
								
							  AND ap.ap_num = ap_det.ap_num AND bom_lots.dis_ver > 0
								AND ap_det.mat_cat = 'l' 							
							ORDER BY wi.wi_num
							";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$ord_num = $po_num ='';
		while ($row = $sql->fetch($q_result)) {		
			if($row['smpl_code'] <> $ord_num || $row['po_num'] <> $po_num)
			{
				$ord_num = $row['smpl_code'] ;
				$po_num = $row['po_num'];
				$rpt[$i]=$row;
				$rpt[$i]['ord_status'] = $GLOBALS['order']->get_field_value('status','',$row['smpl_code']);
				$rpt[$i]['ord_status'] = get_ord_status($rpt[$i]['ord_status']);
				$vendor = $GLOBALS['supl']->get('',$row['sup_code']);
				$rpt[$i]['ven_name'] = $vendor['supl_s_name'];
				$i++;
			}
		}

		// 前版本未沖銷副料
		$q_str = "SELECT DISTINCT ap_det.po_eta, ap_det.ap_num,	 ap.po_num, ap_det.mat_cat,
										 ap.currency, ap.status, ap.dear, ap.arv_area, ap.ship_name, ap.sup_code, 
										 wi.wi_num as smpl_code
							FROM bom_acc, ap_det, ap, wi
							WHERE bom_acc.id = ap_det.bom_id AND bom_acc.wi_id = wi.id
							  AND ap.status >= 0
							  AND ap.ap_num = ap_det.ap_num AND bom_acc.dis_ver > 0
							  AND ap_det.mat_cat = 'a'  							
							ORDER BY wi.wi_num";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$ord_num = $po_num ='';	
		while ($row = $sql->fetch($q_result)) {		
			if($row['smpl_code'] <> $ord_num || $row['po_num'] <> $po_num)
			{
				$ord_num = $row['smpl_code'] ;
				$po_num = $row['po_num'];
				$rpt[$i]=$row;
				$rpt[$i]['ord_status'] = $GLOBALS['order']->get_field_value('status','',$row['smpl_code']);
				$rpt[$i]['ord_status'] = get_ord_status($rpt[$i]['ord_status']);
				$vendor = $GLOBALS['supl']->get('',$row['sup_code']);
				$rpt[$i]['ven_name'] = $vendor['supl_s_name'];
				$i++;
			}
		}		

		$i = 0;
		$rpt_2 = array();
		// 預先採購未沖銷
		$q_str = "SELECT DISTINCT ap_special.po_eta as po_eta, ap.ap_num, ap.po_num, ap_special.mat_cat,
										 ap.currency, ap.status, ap.dear, ap.arv_area, ap.ship_name, ap.sup_code,										
										 s_order.status as ord_status, ap_special.ord_num  
						FROM ap_special,ap, s_order 
						WHERE ap.ap_num = ap_special.ap_num 
						  AND ap_special.ord_num = s_order.order_num AND ap.status >= 0
							AND ap_special.pp_mark = 1 	
						ORDER BY s_order.order_num";
	
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$ord_num = $po_num ='';
		while ($row = $sql->fetch($q_result)) {		
			if($row['ord_num'] <> $ord_num || $row['po_num'] <> $po_num)
			{
				$ord_num = $row['ord_num'] ;
				$po_num = $row['po_num'];
				$rpt_2[$i]=$row;
				$rpt_2[$i]['ord_status'] = get_ord_status($rpt_2[$i]['ord_status']);		
				$vendor = $GLOBALS['supl']->get('',$row['sup_code']);
				$rpt2[$i]['ven_name'] = $vendor['supl_s_name'];		
				$i++;
			}
		}	

/*
		// 預先採購未沖銷副料
		$q_str = "SELECT ap_special.*, ap.status, ap.po_num, acc.acc_name as mat_name, 
										 s_order.status as ord_status  
						FROM ap_special,ap,acc, s_order 
						WHERE ap.ap_num = ap_special.ap_num AND ap_special.mat_code = acc.acc_code
									 AND ap_special.ord_num = s_order.order_num AND ap.status >= 0 
									AND ap_special.mat_cat ='a' AND ap_special.pp_mark = 1 
						ORDER BY s_order.order_num, acc.acc_code";
	
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}

		while ($row = $sql->fetch($q_result)) {		
			$rpt_2[$i]=$row;		
			$rpt_2[$i]['ord_status'] = get_ord_status($rpt_2[$i]['ord_status']);
			$i++;
		}	
*/		
		$rpt3 = array();		
		$i = 0;
		// 未送出採購單--主料
		$q_str = "SELECT DISTINCT ap_det.po_eta, ap_det.ap_num,	 ap.po_num, ap_det.mat_cat,
										 ap.currency, ap.status, ap.dear, ap.arv_area, ap.ship_name, ap.sup_code,
										 wi.wi_num as smpl_code 
							FROM bom_lots, ap_det, wi, ap
							WHERE bom_lots.wi_id = wi.id AND bom_lots.id = ap_det.bom_id 
								AND ap.status = 6 
							  AND ap.ap_num = ap_det.ap_num 
								AND ap_det.mat_cat = 'l' 
							ORDER BY wi.wi_num";
	
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
	 $ord_num = $po_num ='';	
		while ($row = $sql->fetch($q_result)) {		
			if($row['smpl_code'] <> $ord_num || $row['po_num'] <> $po_num)
			{
				$ord_num = $row['smpl_code'] ;
				$po_num = $row['po_num'];
				$rpt3[$i]=$row;
				$rpt3[$i]['ord_status'] = $GLOBALS['order']->get_field_value('status','',$row['smpl_code']);
				$rpt3[$i]['ord_status'] = get_ord_status($rpt3[$i]['ord_status']);
				$vendor = $GLOBALS['supl']->get('',$row['sup_code']);
				$rpt3[$i]['ven_name'] = $vendor['supl_s_name'];
				$i++;
			}
		}

		// 未送出採購單--副料
		$q_str = "SELECT DISTINCT ap_det.po_eta, ap_det.ap_num,	 ap.po_num, ap_det.mat_cat,
										 ap.currency, ap.status, ap.dear, ap.arv_area, ap.ship_name, ap.sup_code,
										 wi.wi_num as smpl_code 
							FROM bom_acc, ap_det, wi, ap
							WHERE bom_acc.wi_id = wi.id AND bom_acc.id = ap_det.bom_id 
							  AND ap.status = 6
							  AND ap.ap_num = ap_det.ap_num 
							  AND ap_det.mat_cat = 'a' 
							ORDER BY wi.wi_num";
	
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$ord_num = $po_num ='';
		while ($row = $sql->fetch($q_result)) {		
			if($row['smpl_code'] <> $ord_num || $row['po_num'] <> $po_num)
			{
				$ord_num = $row['smpl_code'] ;
				$po_num = $row['po_num'];
				$rpt3[$i]=$row;
				$rpt3[$i]['ord_status'] = $GLOBALS['order']->get_field_value('status','',$row['smpl_code']);
				$rpt3[$i]['ord_status'] = get_ord_status($rpt3[$i]['ord_status']);
				$vendor = $GLOBALS['supl']->get('',$row['sup_code']);
				$rpt3[$i]['ven_name'] = $vendor['supl_s_name'];
				$i++;
			}
		}				

		$op['pp'] = $rpt_2;
		$op['dis_ver'] = $rpt;
		$op['po_unsub'] = $rpt3;

		return $op;
		
} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->po_un_rcv()
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function po_un_rcv()
{
	
	
//未驗收採購單
	$sql = $this->sql;
	$TODAY = date('Y-m-d');
	$un_rcv_date = $_SESSION['un_rcv_date'];
	$search_date = increceDaysInDate($TODAY,$un_rcv_date);
	$rpt4 = array();
	
	$DEPT = ($GLOBALS['SCACHE']['ADMIN']['dept'] == 'HJ' || $GLOBALS['SCACHE']['ADMIN']['dept'] == 'LY'|| $GLOBALS['SCACHE']['ADMIN']['dept'] == 'CF'|| $GLOBALS['SCACHE']['ADMIN']['dept'] == 'V1') ? " AND ap.finl_dist = '".$GLOBALS['SCACHE']['ADMIN']['dept']."' " : "" ;

//副料	
		$q_str = "SELECT ap_det.po_eta, ap_det.ap_num, ap.po_num, ap_det.mat_cat, po_ship.carrier,
										 ap.arv_area, ap.finl_dist, ap.id as ap_id, po_ship.id as ship_id, 
										 wi.wi_num  as ord_num, po_ship.ship_date, po_ship.ship_way,
										 po_ship.ship_eta as sort, po_ship.ship_eta , shipper
							FROM `ap_det` , bom_acc, wi, ap, po_ship, po_ship_det
							WHERE  ap.ap_num = ap_det.ap_num AND ap_det.bom_id = bom_acc.id AND									 
										 bom_acc.wi_id = wi.id AND ap_det.po_spare = po_ship_det.po_id AND
										 po_ship_det.ship_id = po_ship.id AND										 
										 ap_det.mat_cat = 'a' AND  po_ship_det.special = 0 AND
										 ap_det.rcv_qty = 0 AND ap.status = 12 AND ap_det.ship_date <= '".$TODAY."' AND 
										 po_qty > 0 AND ap_det.ship_date <> '0000-00-00' AND po_apv_date > '2012-12-31' ".$DEPT."
							ORDER BY po_ship.ship_date, po_ship.carrier";
// echo $q_str."<BR>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		while ($row = $sql->fetch($q_result)) {	

		$po_user=$GLOBALS['user']->get(0,$row['shipper']);
		$row['shipper_id'] = $row['shipper'];
		if ($po_user['name'])$row['shipper'] = $po_user['name'];

			$rpt4[] = $row;
		}

//主料	
		$q_str = "SELECT ap_det.po_eta, ap_det.ap_num,	 ap.po_num, ap_det.mat_cat, po_ship.carrier,
										 ap.arv_area, ap.finl_dist, ap.id as ap_id, po_ship.id as ship_id,
										 wi.wi_num as ord_num, po_ship.ship_date, po_ship.ship_way,
										 po_ship.ship_eta as sort, po_ship.ship_eta, shipper
							FROM `ap_det` , bom_lots, wi, ap, po_ship, po_ship_det
							WHERE  ap.ap_num = ap_det.ap_num AND ap_det.mat_cat = 'l' AND 
										 po_ship_det.ship_id = po_ship.id AND ap_det.po_spare = po_ship_det.po_id AND
										 ap_det.bom_id = bom_lots.id AND bom_lots.wi_id = wi.id AND 
										 ap_det.rcv_qty = 0 AND  po_apv_date > '2012-12-31' AND
										 ap.status = 12 AND ap_det.ship_date <= '".$TODAY."' AND po_qty > 0 AND 
										 ap_det.ship_date <> '0000-00-00' AND po_ship_det.special = 0 ".$DEPT."
							ORDER BY po_ship.ship_date, po_ship.carrier";
//echo $q_str."<BR>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		while ($row = $sql->fetch($q_result)) {	
	//		$row['ord_status'] = get_ord_status($row['status']);
			$po_user=$GLOBALS['user']->get(0,$row['shipper']);
			$row['shipper_id'] = $row['shipper'];
			if ($po_user['name'])$row['shipper'] = $po_user['name'];

			$rpt4[] = $row;
		}


//特殊採購	
		$q_str = "SELECT ap_special.po_eta, ap_special.ap_num,	 ap.po_num, ap_special.mat_cat,
										 ap.arv_area, ap.finl_dist, ap.id as ap_id, po_ship.id as ship_id,
										 ap_special.ord_num, po_ship.carrier, po_ship.ship_date, po_ship.ship_way,
										 po_ship.ship_eta as sort, po_ship.ship_eta, shipper
							FROM `ap_special`, ap, po_ship, po_ship_det
							WHERE  ap.ap_num = ap_special.ap_num AND 
										 po_ship_det.ship_id = po_ship.id AND ap_special.po_spare = po_ship_det.po_id AND									 
										 ap_special.rcv_qty = 0 AND po_ship_det.special = 1 AND
										 ap.status = 12 AND ap_special.ship_date <= '".$TODAY."' AND 
										 po_qty > 0 AND ap_special.ship_date <> '0000-00-00' AND po_apv_date > '2012-12-31' ".$DEPT."
							ORDER BY po_ship.ship_date, po_ship.carrier";
//echo $q_str."<BR>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		while ($row = $sql->fetch($q_result)) {	
	//		$row['ord_status'] = get_ord_status($row['status']);
			$po_user=$GLOBALS['user']->get(0,$row['shipper']);
			$row['shipper_id'] = $row['shipper'];
			if ($po_user['name'])$row['shipper'] = $po_user['name'];

			$rpt4[] = $row;
		}
		
		
//		$rpt4 = bubble_sort_s($rpt4);

	$t_ary = array();		
	for($i=0; $i<sizeof($rpt4); $i++)
	{
		$id = $rpt4[$i]['ap_id'].'_'.$rpt4[$i]['ship_id'];
		$ord_num = explode('-',$rpt4[$i]['ord_num']);
		if(!isset($t_ary[$id])) 
		{
			$t_ary[$id] = $rpt4[$i];			
			$t_ary[$id]['ords'] = $ord_num[1].',';
		}else{
		if(!strstr($t_ary[$id]['ords'],$ord_num[1]))	$t_ary[$id]['ords'] .= $ord_num[1].',';
		}
	}
	$rpt4 = array();
	foreach($t_ary as $key => $value)
	{
		$t_ary[$key]['ords'] = substr($t_ary[$key]['ords'],0,-1);

		$rpt4[] = $t_ary[$key];
	}	

		
		
		
	return $rpt4;
}





#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->rep_unsch_search($parm)	抓出己排程但不可生產的訂單(主副料和樣本核可 無) -- 女裝線
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_ship_ord_tbl($sday) {

		$sql = $this->sql;
//		$parm['s_date'] = increceMonthInDate($parm['e_date'],2);
		$TODAY = date('Y-m-d');
		$search_date = increceDaysInDate($TODAY,-180);
		$DEPT = ($GLOBALS['SCACHE']['ADMIN']['dept'] == 'HJ' || $GLOBALS['SCACHE']['ADMIN']['dept'] == 'LY'|| $GLOBALS['SCACHE']['ADMIN']['dept'] == 'CF'|| $GLOBALS['SCACHE']['ADMIN']['dept'] == 'V1') ? " AND s_order.factory = '".$GLOBALS['SCACHE']['ADMIN']['dept']."' " : "" ;

		// 關聯式資料庫查尋 ~~~~~~
		$q_str = "SELECT s_order.order_num, s_order.style_num, s_order.ref, s_order.qty, s_order.style, 
											s_order.su, s_order.etd, s_order.factory, s_order.status, s_order.dept,											 
											s_order.smpl_apv , pdtion.ets, pdtion.etf, pdtion.qty_done,											
											pdtion.sub_con, pdtion.ob_way, pdtion.ob_date, pdtion.e_ship, pdtion.e_ship_way,
											pdtion.id as p_id,s_order.ship_way, s_order.ship_dist, s_order.ship_day
							FROM s_order, pdtion
							WHERE pdtion.order_num = s_order.order_num and s_order.status >= 8 and s_order.status < 12 and s_order.etd >= '".$sday."' and s_order.dept <> s_order.factory ". $DEPT ." 
							ORDER BY s_order.factory, s_order.etd, s_order.order_num";
// echo $q_str;	
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$i=0;
		$qty=0;
		$cout=0;
		$su=0;
		$dd=0;
        $rpt = array();
		while ($row = $sql->fetch($q_result)) {		
			$tmp_date=countDays($TODAY,$row['etd']);  
			$row['ship_way_ary'] = explode('|',$row['ship_way']);
			$row['ship_dist_ary'] = explode('|',$row['ship_dist']);
			$row['ship_day_ary'] = explode('|',$row['ship_day']);
	
			$row['ob_select'] =  $GLOBALS['arry2']->select($GLOBALS['ORD_SHIP'],$row['ob_way'],'PHP_ob_way','select'); 
			$row['es_select'] =  $GLOBALS['arry2']->select($GLOBALS['ORD_SHIP'],$row['e_ship_way'],'PHP_e_ship_way','select'); 

			
			$rpt[$i]=$row;
			$rpt[$i]['lead'] = $tmp_date;
			$rpt[$i]['rmk'] = get_ord_status($row['status']);
			$j=$i-1;
			if ($j < 0) $j=0;
			$tmp=$rpt[$j]['factory'];
			if ($rpt[$i]['factory'] <> $tmp) 
			{
				$rpt[$j]['ln_mk']=1;
				$rpt[$j]['qty_tal']=$qty;
				$rpt[$j]['su_tal']=$su;
				$rpt[$j]['count']=$cout;
				$qty=0;
				$cout=0;
				$dd=0;
			}
			if (substr($rpt[$i]['etd'],0,7) <> $dd) 
			{
				if ($dd <> 0)$rpt[$j]['ln_mon']=1;
				$dd = substr($rpt[$i]['etd'],0,7);
			}

			$i++;
			$cout++;
			$qty=$qty+$row['qty'];
			$su=$su+$row['su'];
		}
        
		if (!empty($rpt))
		{
            $j=$i-1;
            $rpt[$j]['ln_mk']=1;
            $rpt[$j]['qty_tal']=$qty;
            $rpt[$j]['su_tal']=$su;
            $rpt[$j]['count']=$cout;		

			return $rpt;	
		}else{
			return false;
		}
		
} // end func




#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->cust_os_search($year)	年度每月客戶訂單樣本量
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function smpl_fty_search($year,$fty) {		
		$sql = $this->sql;
		$mm = date('m');
		$result = array();
		if($mm == '01')
		{
			$search_date = ($year-1)."-12-01";
		}else{
			if(($mm = $mm -1) < 10) $mm = '0'.$mm;
			$search_date = $year.'-'.$mm.'-01';
		}

		// 關聯式資料庫查尋 ~~~~~~
			$q_str = "SELECT * 
  					  	FROM smpl_ord
  					  	WHERE etd < '".$search_date."' AND etd > '2009-01-01' AND status < 7 AND status > -1 AND  qty > qty_done AND factory='".$fty."'
  					  	ORDER BY etd, schd_smpl";	

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			$result[] = $row;
		}


		// 關聯式資料庫查尋 ~~~~~~
			$q_str = "SELECT * 
  					  	FROM smpl_ord
  					  	WHERE etd >= '".$search_date."' AND factory= '".$fty."' AND status > -1 AND status < 7
  					  	ORDER BY etd, schd_smpl";	

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			$result[] = $row;
		}

		
	
		return $result;
		
} // end func
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	客戶訂單樣本量
#   可依據日期區間、部門、工廠來篩選訂單樣本量
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function smpl_fty_searchNew($startDate,$endDate,$fty,$dept,$Cust,$CaseClose=0) {		
		$sql = $this->sql;
		$mm = date('m');
		$result = array();
		
		$q_select="SELECT * FROM smpl_ord ";
		$q_where="Where status in (0,1,2,3,4,5,6,".$CaseClose.")";
		$q_sort=" ORDER BY factory, etd, dept, cust, schd_smpl";
		
		
			//日期判斷Begin
			if(trim($startDate)=="" )
			{
				if(trim($endDate)=="" )
				{
					//無起迄日(列出今年資料)
					// 關聯式資料庫查尋 ~~~~~~
					if(empty($fty))
					{
						if(empty($dept))
						{
							if(empty($Cust))
							{
								/*工廠、部門與客戶都為空值*/
								$q_where.=" AND YEAR(etd) = YEAR(NOW())  ";
							}
							else
							{
								/*工廠、部門空值，但客戶有值*/
								$q_where.=" AND YEAR(etd) = YEAR(NOW()) AND cust='".$Cust."'";
							}
						}
						else
						{
							if(empty($Cust))
							{
								/*工廠、客戶空值、部門有值*/
								$q_where.=" AND YEAR(etd) = YEAR(NOW()) AND dept='".$dept."'";
							}
							else
							{
								/*工廠空值，部門與客戶有值*/
								$q_where.=" AND YEAR(etd) = YEAR(NOW()) AND cust='".$Cust."'";
								$q_where.=" AND dept='".$dept."'";
							}
							
						}
					}
					else
					{
						if(empty($dept))
						{
							if(empty($Cust))
							{
								/*客戶與部門空值、工廠有值*/
								$q_where.=" AND YEAR(etd) = YEAR(NOW()) AND factory='".$fty."'";
							}
							else
							{
								/*部門空值，工廠與客戶有值*/
								$q_where.=" AND YEAR(etd) = YEAR(NOW()) AND cust='".$Cust."'";
								$q_where.=" AND factory='".$fty."'";
							}							
						}
						else
						{
							if(empty($Cust))
							{
								/*客戶空值、工廠與部門有值*/
								$q_where.=" AND YEAR(etd) = YEAR(NOW()) AND factory='".$fty."'";
								$q_where.=" AND dept='".$dept."'";
							}
							else
							{
								/*部門、工廠與客戶有值*/
								$q_where.=" AND YEAR(etd) = YEAR(NOW()) AND cust='".$Cust."'";
								$q_where.=" AND factory='".$fty."'";
								$q_where.=" AND dept='".$dept."'";
							}	
							
						}
					}

				}
				else
				{
					//只有迄日(列出所有資料，日期要小於今日)
					// 關聯式資料庫查尋 ~~~~~~
					if(empty($fty))
					{
						if(empty($dept))
						{
							if(empty($Cust))
							{
								/*工廠、部門與客戶都為空值*/
								$q_where.=" AND etd > '2009-01-01' AND TO_DAYS(etd) <= TO_DAYS('".$endDate."')";
							}
							else
							{
								/*工廠、部門空值，但客戶有值*/		
								$q_where.=" AND etd > '2009-01-01' AND TO_DAYS(etd) <= TO_DAYS('".$endDate."')";
								$q_where.=" AND cust='".$Cust."'";
							}
						}
						else
						{
							/*工廠空值、部門有值*/
							if(empty($Cust))
							{
								/*工廠、客戶空值、部門有值*/
								$q_where.=" AND etd > '2009-01-01' AND TO_DAYS(etd) <= TO_DAYS('".$endDate."')";
								$q_where.=" AND dept='".$dept."'";
							}
							else
							{
								/*工廠空值，部門與客戶有值*/
								$q_where.=" AND etd > '2009-01-01' AND TO_DAYS(etd) <= TO_DAYS('".$endDate."')";
								$q_where.=" AND cust='".$Cust."'";
								$q_where.=" AND dept='".$dept."'";
							}
						}
					}
					else
					{
						if(empty($dept))
						{
							/*工廠有值、部門空值*/
							if(empty($Cust))
							{
								/*客戶與部門空值、工廠有值*/
								$q_where.=" AND etd > '2009-01-01' AND TO_DAYS(etd) <= TO_DAYS('".$endDate."')";
								$q_where.=" AND factory='".$fty."'";
							}
							else
							{
								/*部門空值，工廠與客戶有值*/
								$q_where.=" AND etd > '2009-01-01' AND TO_DAYS(etd) <= TO_DAYS('".$endDate."')";
								$q_where.=" AND cust='".$Cust."'";
								$q_where.=" AND factory='".$fty."'";
							}		

						}
						else
						{
							/*工廠部門都有值*/
							if(empty($Cust))
							{
								/*客戶空值、工廠與部門有值*/
								$q_where.=" AND etd > '2009-01-01' AND TO_DAYS(etd) <= TO_DAYS('".$endDate."')";
								$q_where.=" factory='".$fty."'";
								$q_where.=" AND dept='".$dept."'";
							}
							else
							{
								/*部門、工廠與客戶有值*/
								$q_where.=" AND etd > '2009-01-01' AND TO_DAYS(etd) <= TO_DAYS('".$endDate."')";
								$q_where.=" AND cust='".$Cust."'";
								$q_where.=" AND factory='".$fty."'";
								$q_where.=" AND dept='".$dept."'";
							}	
							
						}
					}

				}
			}	
			else
			{
				if(trim($endDate)=="" )
				{
					//只有起日(列出所有資料，日期要大於今日)
					// 關聯式資料庫查尋 ~~~~~~
					if(empty($fty))
					{
						if(empty($dept))
						{
							/*工廠與部門都為空值*/
							if(empty($Cust))
							{
								/*客戶、工廠與部門都為空值*/
								$q_where.=" AND etd > '2009-01-01' AND TO_DAYS(etd) >= TO_DAYS('".$startDate."')";

							}
							else
							{
								/*部門、工廠空值，但客戶有值*/
								$q_where.=" AND etd > '2009-01-01' AND TO_DAYS(etd) >= TO_DAYS('".$startDate."')";
								$q_where.=" AND cust='".$Cust."'";
							}	
						}
						else
						{
							/*工廠空值、部門有值*/
							if(empty($Cust))
							{
								/*客戶、工廠空值，但部門有值*/
								$q_where.=" AND etd > '2009-01-01' AND TO_DAYS(etd) >= TO_DAYS('".$startDate."')";
								$q_where.=" AND dept='".$dept."'";
							}
							else
							{
								/*工廠空值，部門與客戶有值*/
								$q_where.=" AND etd > '2009-01-01' AND TO_DAYS(etd) >= TO_DAYS('".$startDate."')";
								$q_where.=" AND cust='".$Cust."'";
								$q_where.=" AND dept='".$dept."'";
							}	
						}
					}
					else
					{
						if(empty($dept))
						{
							/*工廠有值、部門空值*/
							if(empty($Cust))
							{
								/*工廠有值，部門與客戶空值*/
								$q_where.=" AND etd > '2009-01-01' AND TO_DAYS(etd) >= TO_DAYS('".$startDate."')";
								$q_where.=" AND factory='".$fty."'";
							}
							else
							{
								/*工廠與客戶有值，部門空值*/
								$q_where.=" AND etd > '2009-01-01' AND TO_DAYS(etd) >= TO_DAYS('".$startDate."')";
								$q_where.=" AND cust='".$Cust."'";
								$q_where.=" AND factory='".$fty."'";
							}	

						}
						else
						{
							/*工廠部門都有值*/
							if(empty($Cust))
							{
								/*工廠與部門有值，客戶空值*/
								$q_where.=" AND etd > '2009-01-01' AND TO_DAYS(etd) >= TO_DAYS('".$startDate."')";
								$q_where.=" AND factory='".$fty."'";
								$q_where.=" AND dept='".$dept."'";
								
							}
							else
							{
								/*工廠、部門與客戶有值*/
								$q_where.=" AND etd > '2009-01-01' AND TO_DAYS(etd) >= TO_DAYS('".$startDate."')";
								$q_where.=" AND cust='".$Cust."'";
								$q_where.=" AND factory='".$fty."'";
								$q_where.=" AND dept='".$dept."'";
							}
							
						}
					}
							
				}
				else
				{
					//有起迄日(列出所有資料，日期要在設定的日期區間)
					// 關聯式資料庫查尋 ~~~~~~
					if(empty($fty))
					{
						if(empty($dept))
						{
							/*工廠與部門都為空值*/
							if(empty($Cust))
							{
								/*工廠、部門與客戶都為空值*/
								$q_where.=" AND etd > '2009-01-01'";
								$q_where.="	AND etd between '".$startDate."' AND '".$endDate."'";
								
							}
							else
							{
								/*工廠、部門空值，但客戶有值*/
								$q_where.=" AND etd > '2009-01-01'";
								$q_where.="	AND etd between '".$startDate."' AND '".$endDate."'";
								$q_where.=" AND cust='".$Cust."'";
							}
						}
						else
						{
							/*工廠空值、部門有值*/
							if(empty($Cust))
							{
								/*工廠、客戶空值、部門有值*/
								$q_where.=" AND etd > '2009-01-01'";
								$q_where.="	AND etd between '".$startDate."' AND '".$endDate."'";
								$q_where.=" AND dept='".$dept."'";
							}
							else
							{
								/*工廠空值，部門與客戶有值*/
								$q_where.=" AND etd > '2009-01-01'";
								$q_where.="	AND etd between '".$startDate."' AND '".$endDate."'";
								$q_where.=" AND cust='".$Cust."'";
								$q_where.=" AND dept='".$dept."'";
							}
						}
					}
					else
					{
						if(empty($dept))
						{
							/*工廠有值、部門空值*/
							if(empty($Cust))
							{
								/*工廠有值，部門與客戶空值*/
								$q_where.=" AND etd > '2009-01-01'";
								$q_where.="	AND etd between '".$startDate."' AND '".$endDate."'";
								$q_where.=" AND factory='".$fty."'";
							}
							else
							{
								/*工廠與客戶有值，部門空值*/
								$q_where.=" AND etd > '2009-01-01'";
								$q_where.="	AND etd between '".$startDate."' AND '".$endDate."'";
								$q_where.=" AND cust='".$Cust."'";
								$q_where.=" AND factory='".$fty."'";
							}	

						}
						else
						{
							/*工廠部門都有值*/
							if(empty($Cust))
							{
								/*工廠與部門有值，客戶空值*/
								$q_where.=" AND etd > '2009-01-01'";
								$q_where.="	AND etd between '".$startDate."' AND '".$endDate."'";
								$q_where.=" AND factory='".$fty."'";
								$q_where.=" AND dept='".$dept."'";
								
							}
							else
							{
								/*工廠、部門與客戶有值*/
								$q_where.=" AND etd > '2009-01-01'";
								$q_where.="	AND etd between '".$startDate."' AND '".$endDate."'";
								$q_where.=" AND cust='".$Cust."'";
								$q_where.=" AND factory='".$fty."'";
								$q_where.=" AND dept='".$dept."'";
							}
							
						}
					}
							
				}
			}
		//日期判斷End
		/* echo "this one Command <br>".$q_str;  */
		$q_str = $q_select.$q_where.$q_sort;
		// echo "this one Command <br>".$q_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			$result[] = $row;
		}

		return $result;
		
} // end func





#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->cust_os_search($year)	年度每月客戶訂單樣本量
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function smpl_fty_full_search($year,$fty) {		
		$sql = $this->sql;
		$mm = date('m');
		$rtn = $tmp = array();
		if($mm == '01')
		{
			$search_date = ($year-1)."-12-01";
		}else{
			if(($mm = $mm -1) < 10) $mm = '0'.$mm;
			$search_date = $year.'-'.$mm.'-01';
		}

		// 關聯式資料庫查尋 ~~~~~~
			$q_str = "SELECT YEAR(etd) as year , MONTH(etd) as mon, qty, qty_done as done, 
											 (qty - qty_done) as remain
  					  	FROM smpl_ord
  					  	WHERE etd like '".$year."%'  AND status > -1 AND factory='".$fty."'
  					  	ORDER BY etd, schd_smpl";	
// echo $q_str."<BR>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			if($row['mon'] < 10) $row['mon'] ='0'.$row['mon'];
			if(!isset($tmp[$row['mon']]['qty'])) $tmp[$row['mon']]['qty'] = 0;
			if(!isset($tmp[$row['mon']]['done'])) $tmp[$row['mon']]['done'] = 0;
			if(!isset($tmp[$row['mon']]['remain'])) $tmp[$row['mon']]['remain'] = 0;
			if(!isset($tmp[$row['mon']]['num'])) $tmp[$row['mon']]['num'] = 0;
			if($row['remain'] < 0)$row['remain'] = 0;
			$tmp[$row['mon']]['qty'] += $row['qty'];
			$tmp[$row['mon']]['done'] += $row['done'];
			$tmp[$row['mon']]['remain'] += $row['remain'];
			$tmp[$row['mon']]['num'] ++;
		}
		$i = 0;
		$year = substr($year,2);
		foreach ($tmp as $key	=>	$value)
		{
			
			$rtn[$i]['s_mm'] = $year."-".$key;
			$rtn[$i]['qty'] = $tmp[$key]['qty'];
			$rtn[$i]['done'] = $tmp[$key]['done'];
			$rtn[$i]['remain'] = $tmp[$key]['remain'];
			$rtn[$i]['num'] = $tmp[$key]['num'];
			$i++;
		}


		
	
		return $rtn;
		
} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->cust_os_search($year)	年度每月客戶訂單樣本量
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function sort_by_etf($parm) {		
	$i = 0;
	$rtn_ary = array();
	$mm_ary = array();
//	$tmp_mm = $mm = substr($parm[0]['etf'],0,7);
	for($i=0; $i<sizeof($parm); $i++)
	{
		$mm = substr($parm[$i]['etd'],0,7);
		if($parm[$i]['schd_smpl'] == '0000-00-00')$parm[$i]['schd_smpl'] = '9999-99-99';
		$parm[$i]['sort'] = $parm[$i]['schd_smpl'];
		$mm_ary[$mm][] = $parm[$i];

	}
	if(sizeof($mm_ary) > 0)
	{
		foreach($mm_ary as $key => $value)
		{
			$mm_ary[$key] = bubble_sort_s($mm_ary[$key]);
		}


		foreach($mm_ary as $key => $value)
		{
			for($j= 0; $j<sizeof($mm_ary[$key]); $j++)
			{
				if($mm_ary[$key][$j]['schd_smpl'] == '9999-99-99')$mm_ary[$key][$j]['schd_smpl'] = '0000-00-00';
			
				$rtn_ary[] = $mm_ary[$key][$j];
			}
		}
	}
		
		return $rtn_ary;
		
} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->cust_os_search($year)	年度每月客戶訂單樣本量
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_order_det($parm) {		
		$sql = $this->sql;
		$rtn = array();

		// 關聯式資料庫查尋 ~~~~~~
			$q_str = "SELECT s_order.*,  cust_init_name as cust_iname FROM s_order, cust
  					  	WHERE s_order.cust=cust.cust_s_name  AND s_order.cust_ver = cust.ver AND
  					  				factory='".$parm['fty']."' AND etd >= '".$parm['str']."' AND 
  					  				etd <= '".$parm['end']."'
  					  	ORDER BY etd";	
//echo $q_str."<BR>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			$row['ship_way'] = explode('|',$row['ship_way']);
			$row['ship_dist'] = explode('|',$row['ship_dist']);
			$row['row_span'] = sizeof($row['ship_way']);
			$row['cust_po'] = str_replace('|',',',$row['cust_po']);
			$rtn[] = $row;
		}
	
		return $rtn;
		
} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_month_overtime($sch_date,$fty)  取出每月每日加班資料
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_month_overtime($sch_date,$fty) {		
		$sql = $this->sql;
		$rtn = array();

		// 關聯式資料庫查尋 ~~~~~~
			$q_str = "SELECT  max( ot_wk )as ot_wk , max( ot_hr )as ot_hr , sum( over_qty )as ot_qty , 
											  max(workers) as workers, sum(work_qty) as wk_qty,  out_date
							  FROM   pdt_saw_line, saw_out_put
								WHERE saw_out_put.line_id =pdt_saw_line.id AND `out_date` LIKE '".$sch_date."%' AND 
											saw_fty = '".$fty."' AND holiday = 0 AND sc = 0
								GROUP BY `out_date` , line_id";	
//echo $q_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		while ($row = $sql->fetch($q_result)) {
			$row['ot_time'] = $row['ot_wk'] * $row['ot_hr'];
			$row['wk_time'] = $row['workers'] * 8;
//			$row['ot_ct'] = 1;
			if(!isset($ot_det[$row['out_date']]))
			{
				$ot_det[$row['out_date']] = $row;
			}else{
				$ot_det[$row['out_date']]['ot_time'] +=$row['ot_time'];
				$ot_det[$row['out_date']]['ot_hr'] +=$row['ot_hr'];
				$ot_det[$row['out_date']]['ot_wk'] +=$row['ot_wk'];
				$ot_det[$row['out_date']]['ot_qty'] +=$row['ot_qty'];
				$ot_det[$row['out_date']]['workers'] +=$row['workers'];
				$ot_det[$row['out_date']]['wk_time'] +=$row['wk_time'];
				$ot_det[$row['out_date']]['wk_qty'] +=$row['wk_qty'];
//				$ot_det[$row['out_date']]['ot_ct'] +=$row['ot_ct'];
			}
		}
		
		foreach($ot_det as $key => $value)
		{
//			$ot_det[$row['out_date']]['ot_hr'] = $ot_det[$row['out_date']]['ot_hr'] / $ot_det[$row['out_date']]['ot_ct'];
			$rtn[] = $ot_det[$key];
		}
		return $rtn;
		
} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->order_cust_day_list($search_day,$cust)	年度訂單明細(依年份及客戶)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function pdtion_qty_search($fty,$start,$finish) {		
		$sql = $this->sql;	
		// 關聯式資料庫查尋 ~~~~~~			
	
		$q_str = "SELECT sum(saw_out_put.qty) as qty, sum(saw_out_put.su) as su,
									   order_num, pdt_saw_line.id, count(order_num) as days
				      FROM saw_out_put, pdtion, pdt_saw_line
				      WHERE pdtion.order_num = saw_out_put.ord_num AND saw_out_put.line_id = pdt_saw_line.id AND
				            pdtion.finish >= '$start' AND pdtion.finish <= '$finish' AND 
				            pdt_saw_line.fty = '$fty'  AND holiday	= 0 AND pdt_saw_line.sc = 0
				      GROUP BY order_num, pdt_saw_line.id";		
			      	  
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$i=0;	
		
		while ($row = $sql->fetch($q_result)) {
			$result[] = $row;				
		}
		
		
		
		if (!empty($result))
		{
			return $result;		
		}else{
			false;
		}
} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->order_cust_day_list($search_day,$cust)	年度訂單明細(依年份及客戶)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_season_fob($year,$season='') {		
		$sql = $this->sql;	
		// 關聯式資料庫查尋 ~~~~~~	
		$where_str = '';		
		if($season)$where_str = "AND season = '".$season."'";
		$q_str = "SELECT 		avg(uprice) as fob, style, cust FROM s_order
							WHERE  		syear = '".$year."' ".$where_str."
							GROUP BY 	style, cust";		
//echo $q_str."<BR>";			      	  
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$i=0;	
		
		while ($row = $sql->fetch($q_result)) {
			$result[] = $row;				
		}
		
		
		
		if (!empty($result))
		{
			return $result;		
		}else{
			false;
		}
} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->order_cust_day_list($search_day,$cust)	年度訂單明細(依年份及客戶)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function group_fob_record($TTL,$top,$down,$style) {		
	
	//加總的整理 		
	 	foreach($TTL as $key => $value)
	 	{
	 		$t_cc = $b_cc = $cc = 0;
//	 		$TTL[$key]['TSUM'] $TTL[$key]['BSUM'] = 0; //宣告上下身加總初始值為0
	 		for($i=0; $i<sizeof($top); $i++)  //各客戶上身加總
	 		{
	 			$TTL[$key]['TSUM'] += $TTL[$key][$top[$i]];
	 			$TTL[$key]['SUM'] += $TTL[$key][$top[$i]];
	 			if($TTL[$key][$top[$i]] > 0) $t_cc++;
	 			if($TTL[$key][$top[$i]] > 0) $cc++;
	 		}
	 		if($t_cc > 0)$TTL[$key]['TSUM'] /= $t_cc;	 	

	 		for($i=0; $i<sizeof($down); $i++) //各客戶下身加總
	 		{
	 			$TTL[$key]['BSUM'] +=$TTL[$key][$down[$i]];
	 			$TTL[$key]['SUM'] += $TTL[$key][$down[$i]];
	 			if($TTL[$key][$down[$i]] > 0) $b_cc++;
	 			if($TTL[$key][$down[$i]] > 0) $cc++;
	 		}
	 		if($b_cc > 0)$TTL[$key]['BSUM'] /= $b_cc;	 		 	
	 		if($cc > 0)$TTL[$key]['SUM'] /= $cc;	 			
	 	}
	 	
	 	for($i=0; $i<sizeof($style); $i++) //
	 	{
	 		$i_cc = 0;	 $tmp_ttl = 0;
	 		foreach($TTL as $key => $value)
	 		{
	 			if(!isset($TTL['TTL'][$style[$i]]))$TTL['TTL'][$style[$i]] = 0;
	 			$tmp_ttl += $TTL[$key][$style[$i]];	 
//	 			echo $tmp_ttl."+=".$TTL[$key][$style[$i]].'<BR>';			
	 			if($TTL[$key][$style[$i]] > 0) $i_cc++;
	 		}
//	 		echo $style[$i].'==>'.$tmp_ttl."/".$i_cc."<BR>";
	 		if($i_cc > 0) $TTL['TTL'][$style[$i]] = $tmp_ttl / $i_cc;	 		
	 	}
	 	
	 	$i=$j=0;
	 	foreach($TTL as $key => $value)
	 	{	 		
	 		$j = 0;
	 		foreach($TTL[$key] as $s_key => $s_value)  
	 		{
	 			$rtn_array[$i][$j]['fob']=$s_value;
	 			$rtn_array[$i][$j]['cust']=$key;
	 			$rtn_array[$i][$j]['style']=$s_key;	 		
	 			$j++;
	 		} 		 	 			
		  $i++;
	 	}
	
	 return $rtn_array;
	
	
	
} // end func



} // end class


?>