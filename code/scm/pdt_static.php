<?php
session_start();
session_register	('SCACHE');
session_register	('PAGE');
session_register	('authority');
session_register	('where_str');
session_register	('parm');
session_register	('PHP_ses_etd');
session_register	('PHP_unstatus');
##################  2004/11/10  ########################
#			monitor.php  ¥Dµ{¦¡
#		for Carnival SCM [Sample]  management
#			Jack Yang     2004/11/10
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

require_once "config.php";
require_once "config.admin.php";
include_once($config['root_dir']."/lib/class.monitor.php");
$PHP_SELF = $_SERVER['PHP_SELF'];

$perm = $GLOBALS['power'];


require_once "init.object.php";
$op = array();

$MM_SPR['01'] = array ('10','11','12','01','02','03','04','05','06','07','08','09','10');
$MM_SPR['02'] = array ('11','12','01','02','03','04','05','06','07','08','09','10','11');
$MM_SPR['03'] = array ('12','01','02','03','04','05','06','07','08','09','10','11','12');
$MM_SPR['04'] = array ('01','02','03','04','05','06','07','08','09','10','11','12','01');
$MM_SPR['05'] = array ('02','03','04','05','06','07','08','09','10','11','12','01','02');
$MM_SPR['06'] = array ('03','04','05','06','07','08','09','10','11','12','01','02','03');
$MM_SPR['07'] = array ('04','05','06','07','08','09','10','11','12','01','02','03','04');
$MM_SPR['08'] = array ('05','06','07','08','09','10','11','12','01','02','03','04','05');
$MM_SPR['09'] = array ('06','07','08','09','10','11','12','01','02','03','04','05','06');
$MM_SPR['10'] = array ('07','08','09','10','11','12','01','02','03','04','05','06','07');
$MM_SPR['11'] = array ('08','09','10','11','12','01','02','03','04','05','06','07','08');
$MM_SPR['12'] = array ('09','10','11','12','01','02','03','04','05','06','07','08','09');

$YY_SPR['01'] = array ('-1','-1','-1','0','0','0','0','0','0','0','0','0','0');
$YY_SPR['02'] = array ('-1','-1','0','0','0','0','0','0','0','0','0','0','0');
$YY_SPR['03'] = array ('-1','0','0','0','0','0','0','0','0','0','0','0','0');
$YY_SPR['04'] = array ('0','0','0','0','0','0','0','0','0','0','0','0','1');
$YY_SPR['05'] = array ('0','0','0','0','0','0','0','0','0','0','0','1','1');
$YY_SPR['06'] = array ('0','0','0','0','0','0','0','0','0','0','1','1','1');
$YY_SPR['07'] = array ('0','0','0','0','0','0','0','0','0','1','1','1','1');
$YY_SPR['08'] = array ('0','0','0','0','0','0','0','0','1','1','1','1','1');
$YY_SPR['09'] = array ('0','0','0','0','0','0','0','1','1','1','1','1','1');
$YY_SPR['10'] = array ('0','0','0','0','0','0','1','1','1','1','1','1','1');
$YY_SPR['11'] = array ('0','0','0','0','0','1','1','1','1','1','1','1','1');
$YY_SPR['12'] = array ('0','0','0','0','1','1','1','1','1','1','1','1','1');
$PHP_action = !empty($PHP_action) ? $PHP_action : '';
// echo $PHP_action;
switch ($PHP_action) {
//=======================================================

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "pdt_search":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "pdt_search":
check_authority('057',"view");

// creat cust combo box
$op['factory'] = $arry2->select($FACTORY,'','PHP_fty','select','');  	
$op['year_1'] = $arry2->select($YEAR_WORK,date('Y'),'PHP_year1','select','');  	

$op['msg'] = $order->msg->get(2);

//080725message增加		
$note=$notify->search($GLOBALS['SCACHE']['ADMIN']['login_id'], "`read`=0");
$op['max_notify'] = $note['max_no'];

page_display($op,'057', $TPL_PDTS_SEARCH);    	    
break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "pdt_view":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "pdt_view":
check_authority('057',"view");

$this_mm = date('m');

if(!$PHP_year1) $PHP_year1 = $GLOBALS['THIS_YEAR'];
$op['fty'] = $PHP_fty = !empty($PHP_fty) ? $PHP_fty : 'LY' ;
$op['year1'] = $PHP_year1;
$j=0;
for ($i=0; $i<13; $i++) $op['forcast'][$i]=0;

if($this_mm < 7)
{
	$yy1 = $PHP_year1 - 1;
	$yy2 = $PHP_year1;
	$yy3 = $PHP_year1 + 1;
	
	//搜尋forcast
	$where_str = " WHERE method='forecast' AND fty='".$PHP_fty."' AND year='".$yy1."' ";
	$m_cast1 = $forecast->search(0,$where_str);
	
	$where_str = " WHERE method='forecast' AND fty='".$PHP_fty."' AND year='".$yy2."' ";
	$m_cast2 = $forecast->search(0,$where_str);
	
	$where_str = " WHERE method='forecast' AND fty='".$PHP_fty."' AND year='".$yy3."' ";
	$m_cast3 = $forecast->search(0,$where_str);
	
	for ($i=0; $i<sizeof($m_cast1['fcst']); $i++){
		$m_cast1['fcst'][$i]['f'] = csv2array($m_cast1['fcst'][$i]['qty']);
		$m_cast1['fcst'][$i]['t'] = csv2array($m_cast1['fcst'][$i]['top']);
		$m_cast1['fcst'][$i]['b'] = csv2array($m_cast1['fcst'][$i]['botton']);
		$m_cast1['fcst'][$i]['t_su'] = csv2array($m_cast1['fcst'][$i]['top_su']);
		$m_cast1['fcst'][$i]['b_su'] = csv2array($m_cast1['fcst'][$i]['bottom_su']);
		//加總全部的客戶預算---
		for ($j=0; $j<3; $j++) { //上年度
			if($MM_SPR[$this_mm][$j] == '01') break;	
				$k= ((int)$MM_SPR[$this_mm][$j]) - 1;
				$op['forcast'][$j] = $op['forcast'][$j] + ( $m_cast1['fcst'][$i]['t'][$k] * $m_cast1['fcst'][$i]['t_su'][$k] ) + ( $m_cast1['fcst'][$i]['b'][$k] * $m_cast1['fcst'][$i]['b_su'][$k] );
				// echo $op['forcast'][$j].' + '.$m_cast1['fcst'][$i]['f'][$k].' , ';
		}// endfor 上年度
	}// endfor forecast 上年度
// echo '<br>';
	for ($i=0; $i<sizeof($m_cast2['fcst']); $i++){
		$m_cast2['fcst'][$i]['f'] = csv2array($m_cast2['fcst'][$i]['qty']);
		$m_cast2['fcst'][$i]['t'] = csv2array($m_cast2['fcst'][$i]['top']);
		$m_cast2['fcst'][$i]['b'] = csv2array($m_cast2['fcst'][$i]['botton']);
		$m_cast2['fcst'][$i]['t_su'] = csv2array($m_cast2['fcst'][$i]['top_su']);
		$m_cast2['fcst'][$i]['b_su'] = csv2array($m_cast2['fcst'][$i]['bottom_su']);
		
		$m_cast3['fcst'][$i]['f'] = csv2array($m_cast3['fcst'][$i]['qty']);
		$m_cast3['fcst'][$i]['t'] = csv2array($m_cast3['fcst'][$i]['top']);
		$m_cast3['fcst'][$i]['b'] = csv2array($m_cast3['fcst'][$i]['botton']);
		$m_cast3['fcst'][$i]['t_su'] = csv2array($m_cast3['fcst'][$i]['top_su']);
		$m_cast3['fcst'][$i]['b_su'] = csv2array($m_cast3['fcst'][$i]['bottom_su']);
		$next_year_flag = false;
		
		for ($l=$j; $l<13; $l++){  //下年度
			$k= ((int)$MM_SPR[$this_mm][$l]) - 1;
			// $op['forcast'][$l] = $op['forcast'][$l] + ( $m_cast2['fcst'][$i]['t'][$k] * $m_cast2['fcst'][$i]['t_su'][$k] ) + ( $m_cast2['fcst'][$i]['b'][$k] * $m_cast2['fcst'][$i]['b_su'][$k] );
			
			if($MM_SPR[$this_mm][$l] == 12){
				$next_year_flag = true;
			}
			
			if(substr($MM_SPR[$this_mm][$l],0,1) == '0' and $next_year_flag){
				$op['forcast'][$l] = $op['forcast'][$l] + ( $m_cast3['fcst'][$i]['t'][$k] * $m_cast3['fcst'][$i]['t_su'][$k] ) + ( $m_cast3['fcst'][$i]['b'][$k] * $m_cast3['fcst'][$i]['b_su'][$k] );
			}else{
				$op['forcast'][$l] = $op['forcast'][$l] + ( $m_cast2['fcst'][$i]['t'][$k] * $m_cast2['fcst'][$i]['t_su'][$k] ) + ( $m_cast2['fcst'][$i]['b'][$k] * $m_cast2['fcst'][$i]['b_su'][$k] );
			}
		}// endfor 下年度					
	}// endfor forecast 下年度		

	//capcaity
	$c1_0 = $capaci->get($PHP_fty, $yy1,'capacity');
	$c1 = $capaci->get($PHP_fty, $yy2,'capacity');
	$c1_1 = $capaci->get($PHP_fty, $yy3,'capacity');
	$pre_year_flag = false;
	$idx_of = array_search($this_mm, $MM_SPR[$this_mm]);
	for ($j=0; $j<6; $j++) { //上年度
		if($MM_SPR[$this_mm][$j] == '01') break;	
		$tmp_m = 'm'.$MM_SPR[$this_mm][$j];
		(substr($MM_SPR[$this_mm][$j],0,1) == '1' and $idx_of > $j) ? $pre_year_flag = true : $pre_year_flag = false;
		if($pre_year_flag){
			$op['c1'][$j] = $c1_0[$tmp_m];
		}else{
			$op['c1'][$j] = $c1[$tmp_m];
		}
	}// endfor 上年度				
	
	$next_year_flag = false;
	for ($l=$j; $l<13; $l++){  //下年度
		$tmp_m = 'm'.$MM_SPR[$this_mm][$l];
		if($MM_SPR[$this_mm][$l] == 12){
			$next_year_flag = true;
		}
		if(substr($MM_SPR[$this_mm][$l],0,1) == '0' and $next_year_flag){
			$op['c1'][$l] = $c1_1[$tmp_m];
		}else{
			$op['c1'][$l] = $c1[$tmp_m];
		}
		
		
	}// endfor 下年度

} else{
	$yy1 = $PHP_year1;
	$yy2 = $PHP_year1+1;

	//搜尋forcast
	$where_str = " WHERE method='forecast' AND fty='".$PHP_fty."' AND year='".$yy1."' ";
	$m_cast1 = $forecast->search(0,$where_str);
	
	$where_str = " WHERE method='forecast' AND fty='".$PHP_fty."' AND year='".$yy2."' ";
	$m_cast2 = $forecast->search(0,$where_str);
	// print_r($m_cast1);
	for ($i=0; $i<sizeof($m_cast1['fcst']); $i++){
		$m_cast1['fcst'][$i]['f'] = csv2array($m_cast1['fcst'][$i]['qty']);
		$m_cast1['fcst'][$i]['t'] = csv2array($m_cast1['fcst'][$i]['top']);
		$m_cast1['fcst'][$i]['b'] = csv2array($m_cast1['fcst'][$i]['botton']);
		$m_cast1['fcst'][$i]['t_su'] = csv2array($m_cast1['fcst'][$i]['top_su']);
		$m_cast1['fcst'][$i]['b_su'] = csv2array($m_cast1['fcst'][$i]['bottom_su']);
		//加總全部的客戶預算---
		for ( $j = 0; $j < 12; $j++){  //下年度
			$k= ((int)$MM_SPR[$this_mm][$j]) - 1;
			$op['forcast'][$j] = $op['forcast'][$j] + ( $m_cast1['fcst'][$i]['t'][$k] * $m_cast1['fcst'][$i]['t_su'][$k] ) + ( $m_cast1['fcst'][$i]['b'][$k] * $m_cast1['fcst'][$i]['b_su'][$k] );
			if($MM_SPR[$this_mm][$j] == '12') break;
			// echo $MM_SPR[$this_mm][$j].'<br>';
		}// endfor 上年度
	}// endfor forecast 上年度
	// print_r($op['forcast']);
	$j++;

	for ($i=0; $i<sizeof($m_cast2['fcst']); $i++){
		$m_cast2['fcst'][$i]['f'] = csv2array($m_cast2['fcst'][$i]['qty']);
		$m_cast2['fcst'][$i]['t'] = csv2array($m_cast2['fcst'][$i]['top']);
		$m_cast2['fcst'][$i]['b'] = csv2array($m_cast2['fcst'][$i]['botton']);
		$m_cast2['fcst'][$i]['t_su'] = csv2array($m_cast2['fcst'][$i]['top_su']);
		$m_cast2['fcst'][$i]['b_su'] = csv2array($m_cast2['fcst'][$i]['bottom_su']);
		for ($l=$j; $l<13; $l++){  //下年度
			// echo $MM_SPR[$this_mm][$l].'<br>';
			$k= ((int)$MM_SPR[$this_mm][$l]) - 1;
			$op['forcast'][$l] = $op['forcast'][$l] + ( $m_cast2['fcst'][$i]['t'][$k] * $m_cast2['fcst'][$i]['t_su'][$k] ) + ( $m_cast2['fcst'][$i]['b'][$k] * $m_cast2['fcst'][$i]['b_su'][$k] );
		}// endfor 下年度								 
	}// endfor forecast 上年度
	
	//capacity
	$c1 = $capaci->get($PHP_fty, $yy1,'capacity');
	$c1_1 = $capaci->get($PHP_fty, $yy2,'capacity');
	
	for ($j=0; $j<12; $j++) { //上年度	
		$tmp_m = 'm'.$MM_SPR[$this_mm][$j];
		$op['c1'][$j] = $c1[$tmp_m];		
		if($MM_SPR[$this_mm][$j] == '12') break;			
	} // endfor 上年度				
	$j++;
	
	for ($l=$j; $l<13; $l++){  //下年度
		$tmp_m = 'm'.$MM_SPR[$this_mm][$l];
		$op['c1'][$l] = $c1_1[$tmp_m];
	} // endfor 下年度								
}

$yy = $PHP_year1 + $YY_SPR[$this_mm][0];
$yy_str = $yy."-".$MM_SPR[$this_mm][0].'-01';
$yy = $PHP_year1 + $YY_SPR[$this_mm][(sizeof($MM_SPR[$this_mm])-1)];
$yy_end = $yy."-".$MM_SPR[$this_mm][(sizeof($MM_SPR[$this_mm])-1)].'-31';
// echo '<br>'.$yy.'<br>';
// echo $this_mm.'<br>';
// 業務接單
$where_str = "";
$ord = $order->get_one_etd_ord_full($PHP_fty, $yy_str,$yy_end, $where_str);
$ord_p = $order->get_one_etd_ord_prc_full($PHP_fty, $yy_str,$yy_end, $where_str);
$schd = $order->get_one_etd_schd_full($PHP_fty, $yy_str,$yy_end, $where_str);
$output = $order->get_one_etd_output_full($PHP_fty, $yy_str,$yy_end, $where_str);
$ship = $order->get_one_etd_shp_full($PHP_fty, $yy_str,$yy_end, $where_str);
$ship_p = $order->get_one_etd_shp_prc_full($PHP_fty, $yy_str,$yy_end, $where_str);
$un_sch = $order->get_one_etd_unsch_full($PHP_fty, $yy_str,$yy_end, $where_str);
$un_out = $order->get_one_etd_unout_full($PHP_fty, $yy_str,$yy_end, $where_str);
$un_ord = $order->get_one_un_ord_full($PHP_fty, $yy_str,$yy_end, $where_str);
$where_str = " AND pdt_saw_line.sc = 1 ";
$sub_out = $order->get_one_etd_output_full($PHP_fty, $yy_str,$yy_end, $where_str);		
	
for($i=0; $i<sizeof($MM_SPR[$this_mm]); $i++)
{
	$yy = $PHP_year1 + $YY_SPR[$this_mm][$i];
	// echo $YY_SPR[$this_mm][$i].'<br>';
	$yymm = $yy."-".$MM_SPR[$this_mm][$i];
	$op['yy'][] = $yy;

	$op['ord'][$i]	= (!isset($ord[$yymm])) ? 0 : $ord[$yymm];
	$op['ord_p'][$i]	= (!isset($ord_p[$yymm])) ? 0 : $ord_p[$yymm];
	$op['schd'][$i]	= (!isset($schd[$yymm])) ? 0 : $schd[$yymm];
	$op['output'][$i]  = (!isset($output[$yymm])) ? 0 : $output[$yymm];
	$op['ship'][$i]   = (!isset($ship[$yymm])) ? 0 : $ship[$yymm];
	$op['ship_p'][$i]   = (!isset($ship_p[$yymm])) ? 0 : $ship_p[$yymm];
	$op['un_sch'][$i]   = (!isset($un_sch[$yymm])) ? 0 : $un_sch[$yymm];
	$op['un_out'][$i]   = (!isset($un_out[$yymm])) ? 0 : $un_out[$yymm];
	$op['un_ord'][$i]   = (!isset($un_ord[$yymm])) ? 0 : $un_ord[$yymm];
	$op['sub_out'][$i]   = (!isset($sub_out[$yymm])) ? 0 : $sub_out[$yymm];

	$op['mm_name'][$i] = $MM2[$MM_SPR[$this_mm][$i]];
	$op['sumy'][$i] = $op['ord'][$i] + $op['un_ord'][$i] + $op['forcast'][$i];
	$k= ((int)$MM_SPR[$this_mm][$i]) - 1;
	$op['i'][$i] =$k;
}		

$op['mm'] = $MM_SPR[$this_mm];
$op['this_mm'] = $this_mm;


// mode 分 top / button 
if($this_mm < 7) {

	//搜尋forcast
	for ($i=0; $i<sizeof($m_cast1['fcst']); $i++){
		$m_cast1['fcst'][$i]['t'] = csv2array($m_cast1['fcst'][$i]['top']);
		$m_cast1['fcst'][$i]['f'] = csv2array($m_cast1['fcst'][$i]['top_su']);			
		//加總全部的客戶預算---
		for ($j=0; $j<6; $j++) { //上年度
			$op['tp']['forcast'][$j] = !empty($op['tp']['forcast'][$j])?$op['tp']['forcast'][$j]:0; 
			if($MM_SPR[$this_mm][$j] == '01') break;	
				$k= ((int)$MM_SPR[$this_mm][$j]) - 1;
				$op['tp']['forcast'][$j] = $op['tp']['forcast'][$j] + ( $m_cast1['fcst'][$i]['t'][$k] * $m_cast1['fcst'][$i]['f'][$k] );
		}// endfor 上年度
	}// endfor forecast 上年度
		
	for ($i=0; $i<sizeof($m_cast2['fcst']); $i++){
		$m_cast2['fcst'][$i]['t'] = csv2array($m_cast2['fcst'][$i]['top']);
		$m_cast2['fcst'][$i]['f'] = csv2array($m_cast2['fcst'][$i]['top_su']);
		for ($l=$j; $l<13; $l++){  //下年度
			$op['tp']['forcast'][$l] = !empty($op['tp']['forcast'][$l])?$op['tp']['forcast'][$l]:0; 
			$k= ((int)$MM_SPR[$this_mm][$l]) - 1;
			$op['tp']['forcast'][$l] = $op['tp']['forcast'][$l] + ( $m_cast2['fcst'][$i]['t'][$k] * $m_cast2['fcst'][$i]['f'][$k] );
		}// endfor 下年度					
	}// endfor forecast 下年度		

} else{

	$yy1 = $PHP_year1;
	$yy2 = $PHP_year1+1;
	
	//搜尋forcast
	for ($i=0; $i<sizeof($m_cast1['fcst']); $i++){
		$m_cast1['fcst'][$i]['t'] = csv2array($m_cast1['fcst'][$i]['top']);
		$m_cast1['fcst'][$i]['f'] = csv2array($m_cast1['fcst'][$i]['top_su']);			
		//加總全部的客戶預算---
		for ($j=0; $j<12; $j++){  //上年度
			$op['tp']['forcast'][$j] = !empty($op['tp']['forcast'][$j])?$op['tp']['forcast'][$j]:0; 
			$k= ((int)$MM_SPR[$this_mm][$j]) - 1;
			$op['tp']['forcast'][$j] = $op['tp']['forcast'][$j] +( $m_cast1['fcst'][$i]['t'][$k] * $m_cast1['fcst'][$i]['f'][$k] );
			if($MM_SPR[$this_mm][$j] == '12') break;		
		}// endfor 上年度			
	}// endfor forecast 上年度
	$j++;

	for ($i=0; $i<sizeof($m_cast2['fcst']); $i++){
		$m_cast2['fcst'][$i]['t'] = csv2array($m_cast2['fcst'][$i]['top']);
		$m_cast2['fcst'][$i]['f'] = csv2array($m_cast2['fcst'][$i]['top_su']);
		for ($l=$j; $l<13; $l++){  //下年度
			$op['tp']['forcast'][$l] = !empty($op['tp']['forcast'][$l])?$op['tp']['forcast'][$l]:0; 
			$k= ((int)$MM_SPR[$this_mm][$l]) - 1;
			$op['tp']['forcast'][$l] = $op['tp']['forcast'][$l] + ( $m_cast2['fcst'][$i]['t'][$k] * $m_cast2['fcst'][$i]['f'][$k] );
		}// endfor 下年度								 
	}// endfor forecast 上年度					
}
// print_r($m_cast2);
//業務接單
$where_str = " AND ( s_order.style = 'VS' OR s_order.style = 'BZ' OR s_order.style = 'JK' OR s_order.style = 'BS' OR s_order.style = 'DR' OR s_order.style = 'TP' )";
$ord = $order->get_one_etd_ord_full($PHP_fty, $yy_str,$yy_end, $where_str);
$un_ord = $order->get_one_un_ord_full($PHP_fty, $yy_str,$yy_end, $where_str);
$output = $order->get_one_etd_output_full($PHP_fty, $yy_str,$yy_end, $where_str);
	
for($i=0; $i<sizeof($MM_SPR[$this_mm]); $i++)
{
	$yy = $PHP_year1 + $YY_SPR[$this_mm][$i];
	$yymm = $yy."-".$MM_SPR[$this_mm][$i];
	$op['tp']['yy'][] = $yy;

	$op['tp']['output'][$i]	= (!isset($output[$yymm])) ? 0 : $output[$yymm];
	$op['tp']['ord'][$i]	= (!isset($ord[$yymm])) ? 0 : $ord[$yymm];
	$op['tp']['un_ord'][$i]   = (!isset($un_ord[$yymm])) ? 0 : $un_ord[$yymm];
	$op['tp']['sumy'][$i] = $op['tp']['ord'][$i] + $op['tp']['un_ord'][$i] + @$op['tp']['forcast'][$i];
	
	$k= ((int)$MM_SPR[$this_mm][$i]) - 1;
	$op['tp']['i'][$i] =$k;
}
$op['tp']['mm'] = $MM_SPR[$this_mm];
$op['tp']['this_mm'] = $this_mm;





if($this_mm < 7) {

	$yy1 = $PHP_year1 - 1;
	$yy2 = $PHP_year1;
	
	//搜尋forcast
	for ($i=0; $i<sizeof($m_cast1['fcst']); $i++){
		$m_cast1['fcst'][$i]['b'] = csv2array($m_cast1['fcst'][$i]['botton']);			
		$m_cast1['fcst'][$i]['f'] = csv2array($m_cast1['fcst'][$i]['bottom_su']);			
		//加總全部的客戶預算---
		for ($j=0; $j<6; $j++) { //上年度
			$op['bt']['forcast'][$j] = !empty($op['bt']['forcast'][$j])?$op['bt']['forcast'][$j]:0; 
			if($MM_SPR[$this_mm][$j] == '01') break;	
				$k= ((int)$MM_SPR[$this_mm][$j]) - 1;
				$op['bt']['forcast'][$j] = $op['bt']['forcast'][$j] + ($m_cast1['fcst'][$i]['b'][$k] * $m_cast1['fcst'][$i]['f'][$k] );
		 }// endfor 上年度
	 }// endfor forecast 上年度
		
	for ($i=0; $i<sizeof($m_cast2['fcst']); $i++){
		$m_cast2['fcst'][$i]['b'] = csv2array($m_cast2['fcst'][$i]['botton']);			
		$m_cast2['fcst'][$i]['f'] = csv2array($m_cast2['fcst'][$i]['bottom_su']);
		for ($l=$j; $l<13; $l++){  //下年度
			$op['bt']['forcast'][$l] = !empty($op['bt']['forcast'][$l])?$op['bt']['forcast'][$l]:0; 
			$k= ((int)$MM_SPR[$this_mm][$l]) - 1;
			$op['bt']['forcast'][$l] = $op['bt']['forcast'][$l] + ($m_cast2['fcst'][$i]['b'][$k] * $m_cast2['fcst'][$i]['f'][$k] );
		}// endfor 下年度					
	}// endfor forecast 下年度		

} else{

	$yy1 = $PHP_year1;
	$yy2 = $PHP_year1+1;
	
	//搜尋forcast
	for ($i=0; $i<sizeof($m_cast1['fcst']); $i++){
		$m_cast1['fcst'][$i]['b'] = csv2array($m_cast1['fcst'][$i]['botton']);
		$m_cast1['fcst'][$i]['f'] = csv2array($m_cast1['fcst'][$i]['bottom_su']);			
		//加總全部的客戶預算---
		for ($j=0; $j<12; $j++){  //上年度
			$op['bt']['forcast'][$j] = !empty($op['bt']['forcast'][$j])?$op['bt']['forcast'][$j]:0; 
			$k= ((int)$MM_SPR[$this_mm][$j]) - 1;
			$op['bt']['forcast'][$j] = $op['bt']['forcast'][$j] +($m_cast1['fcst'][$i]['b'][$k] * $m_cast1['fcst'][$i]['f'][$k] );
			if($MM_SPR[$this_mm][$j] == '12') break;		
		}// endfor 上年度			
	}// endfor forecast 上年度
	$j++;

	for ($i=0; $i<sizeof($m_cast2['fcst']); $i++){
		$m_cast2['fcst'][$i]['b'] = csv2array($m_cast2['fcst'][$i]['botton']);
		$m_cast2['fcst'][$i]['f'] = csv2array($m_cast2['fcst'][$i]['bottom_su']);
		for ($l=$j; $l<13; $l++){  //下年度
			$op['bt']['forcast'][$l] = !empty($op['bt']['forcast'][$l])?$op['bt']['forcast'][$l]:0; 
			$k= ((int)$MM_SPR[$this_mm][$l]) - 1;
			$op['bt']['forcast'][$l] = $op['bt']['forcast'][$l] + ($m_cast2['fcst'][$i]['b'][$k] * $m_cast2['fcst'][$i]['f'][$k] );
		}// endfor 下年度								 
	}// endfor forecast 上年度					
}

// 業務接單 forcast
$where_str = " AND ( s_order.style = 'PT' OR s_order.style = 'SH' OR s_order.style = 'SO' OR s_order.style = 'SK' ) ";
// $where_str = "";
$ord = $order->get_one_etd_ord_full($PHP_fty, $yy_str,$yy_end, $where_str);
$un_ord = $order->get_one_un_ord_full($PHP_fty, $yy_str,$yy_end, $where_str);
$output = $order->get_one_etd_output_full($PHP_fty, $yy_str,$yy_end, $where_str);
	
for ($i=0; $i<sizeof($MM_SPR[$this_mm]); $i++) {
	$yy = $PHP_year1 + $YY_SPR[$this_mm][$i];
	$yymm = $yy."-".$MM_SPR[$this_mm][$i];
	$op['bt']['yy'][] = $yy;

	$op['bt']['output'][$i]	= (!isset($output[$yymm])) ? 0 : $output[$yymm];
	$op['bt']['ord'][$i]	= (!isset($ord[$yymm])) ? 0 : $ord[$yymm];
	$op['bt']['un_ord'][$i]   = (!isset($un_ord[$yymm])) ? 0 : $un_ord[$yymm];
	$op['bt']['sumy'][$i] = $op['bt']['ord'][$i] + $op['bt']['un_ord'][$i] + $op['bt']['forcast'][$i];
	$k= ((int)$MM_SPR[$this_mm][$i]) - 1;
	$op['bt']['i'][$i] =$k;
}

$op['bt']['mm'] = $MM_SPR[$this_mm];
$op['bt']['this_mm'] = $this_mm;
// print_r($op);
// forcast
page_display($op,'057', $TPL_PDTS_VIEW);
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "sf_ord_qty":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		case "sf_ord_qty":
		check_authority('057',"view");
		$this_mm = date('m');
		$this_mm = '01';
		if(!$PHP_year1) $PHP_year1 = $GLOBALS['THIS_YEAR'];
		$op['fty'] = $PHP_fty;
		$op['year1'] = $PHP_year1;
		$j=0;
		for ($i=0; $i<13; $i++) $op['fc1'][$i]=0;
		for ($i=0; $i<13; $i++) $op['fc2'][$i]=0;
		
		if($this_mm < 7)
		{
			$yy1 = $PHP_year1 - 1;
			$yy2 = $PHP_year1;
			$yy3 = $PHP_year1 + 1;
		  //搜尋forcast  -- 業務
			$where_str = " WHERE method='forecast' AND fty='".$PHP_fty."' AND year='".$yy1."' AND forecast.dept <> '".$PHP_fty."' ";
		  $m_cast = $forecast->search(0,$where_str);
			for ($i=0; $i<sizeof($m_cast['fcst']); $i++){
				$m_cast['fcst'][$i]['f'] = csv2array($m_cast['fcst'][$i]['qty']);			
				//加總全部的客戶預算---
				for ($j=0; $j<3; $j++) { //上年度
					if($MM_SPR[$this_mm][$j] == '01') break;	
						$k= ((int)$MM_SPR[$this_mm][$j]) - 1;
						$op['fc1'][$j] += $m_cast['fcst'][$i]['f'][$k];
					
				 }// endfor 上年度
			 }// endfor forecast 上年度
			
			  $where_str = " WHERE method='forecast' AND fty='".$PHP_fty."' AND year='".$yy2."' AND forecast.dept <> '".$PHP_fty."' ";
			  $m_cast = $forecast->search(0,$where_str);
				for ($i=0; $i<sizeof($m_cast['fcst']); $i++){
					$m_cast['fcst'][$i]['f'] = csv2array($m_cast['fcst'][$i]['qty']);
					for ($l=$j; $l<13; $l++){  //下年度
						$k= ((int)$MM_SPR[$this_mm][$l]) - 1;
						$op['fc1'][$l] += $m_cast['fcst'][$i]['f'][$k];
					}// endfor 下年度					
				
				}// endfor forecast 下年度		

		  //搜尋forcast  -- 工廠
			$where_str = " WHERE method='forecast' AND fty='".$PHP_fty."' AND year='".$yy1."' AND forecast.dept = '".$PHP_fty."' ";
		  $m_cast = $forecast->search(0,$where_str);
			for ($i=0; $i<sizeof($m_cast['fcst']); $i++){
				$m_cast['fcst'][$i]['f'] = csv2array($m_cast['fcst'][$i]['qty']);			
				//加總全部的客戶預算---
				for ($j=0; $j<6; $j++) { //上年度
					if($MM_SPR[$this_mm][$j] == '01') break;	
						$k= ((int)$MM_SPR[$this_mm][$j]) - 1;
						$op['fc2'][$j] += $m_cast['fcst'][$i]['f'][$k];
					
				 }// endfor 上年度
			 }// endfor forecast 上年度
			
			  $where_str = " WHERE method='forecast' AND fty='".$PHP_fty."' AND year='".$yy2."' AND forecast.dept = '".$PHP_fty."' ";
			  $m_cast = $forecast->search(0,$where_str);
				for ($i=0; $i<sizeof($m_cast['fcst']); $i++){
					$m_cast['fcst'][$i]['f'] = csv2array($m_cast['fcst'][$i]['qty']);
					for ($l=$j; $l<13; $l++){  //下年度
						$k= ((int)$MM_SPR[$this_mm][$l]) - 1;
						$op['fc2'][$l] += $m_cast['fcst'][$i]['f'][$k];
					}// endfor 下年度					
				
				}// endfor forecast 下年度						
				
				
				
				//capcaity
				$c1_0 = $capaci->get($PHP_fty, $yy1,'capacity');
				$c1 = $capaci->get($PHP_fty, $yy2,'capacity');
				$c1_1 = $capaci->get($PHP_fty, $yy3,'capacity');
				$pre_year_flag = false;
				$idx_of = array_search($this_mm, $MM_SPR[$this_mm]);
				for ($j=0; $j<6; $j++) { //上年度
					$tmp_m = 'm'.$MM_SPR[$this_mm][$j];
					(substr($MM_SPR[$this_mm][$j],0,1) == '1' and $idx_of > $j) ? $pre_year_flag = true : $pre_year_flag = false;
					if($pre_year_flag){
						$op['c1'][$j] = $c1_0[$tmp_m];
					}else{
						$op['c1'][$j] = $c1[$tmp_m];
					}
				 }// endfor 上年度				
				
				$next_year_flag = false;
				for ($l=$j; $l<13; $l++){  //下年度
					$tmp_m = 'm'.$MM_SPR[$this_mm][$l];
					if($MM_SPR[$this_mm][$l] == '12'){
						$next_year_flag = true;
					}
					if(substr($MM_SPR[$this_mm][$l],0,1) == '0' and $next_year_flag){
						$op['c1'][$l] = $c1_1[$tmp_m];
					}else{
						$op['c1'][$l] = $c1[$tmp_m];
					}
				}// endfor 下年度						
				
				
		}else{
			$yy1 = $PHP_year1;
			$yy2 = $PHP_year1+1;
		  //搜尋forcast  業務
		  $where_str = " WHERE method='forecast' AND fty='".$PHP_fty."' AND year='".$yy1."' AND forecast.dept <> '".$PHP_fty."' ";
		  $m_cast = $forecast->search(0,$where_str);
			for ($i=0; $i<sizeof($m_cast['fcst']); $i++){
				$m_cast['fcst'][$i]['f'] = csv2array($m_cast['fcst'][$i]['qty']);			
				//加總全部的客戶預算---
				for ($j=0; $j<12; $j++){  //上年度
					$k= ((int)$MM_SPR[$this_mm][$j]) - 1;
					$op['fc1'][$j] += $m_cast['fcst'][$i]['f'][$k];
					
					if($MM_SPR[$this_mm][$j] == '12') break;		
				}// endfor 上年度			
			 }// endfor forecast 上年度
			 $j++;

		   $where_str = " WHERE method='forecast' AND fty='".$PHP_fty."' AND year='".$yy2."' AND forecast.dept <> '".$PHP_fty."' ";
		   $m_cast = $forecast->search(0,$where_str);
		   for ($i=0; $i<sizeof($m_cast['fcst']); $i++){
		   	$m_cast['fcst'][$i]['f'] = csv2array($m_cast['fcst'][$i]['qty']);
				 for ($l=$j; $l<13; $l++){  //下年度
	 			 	 $k= ((int)$MM_SPR[$this_mm][$l]) - 1;
	 		 		 
					 $op['fc1'][$l] += $m_cast['fcst'][$i]['f'][$k];
		 		 }// endfor 下年度								 
		 	 }// endfor forecast 下年度
		 	 
		  //搜尋forcast  工廠
		  $where_str = " WHERE method='forecast' AND fty='".$PHP_fty."' AND year='".$yy1."' AND forecast.dept = '".$PHP_fty."' ";
		  $m_cast = $forecast->search(0,$where_str);
			for ($i=0; $i<sizeof($m_cast['fcst']); $i++){
				$m_cast['fcst'][$i]['f'] = csv2array($m_cast['fcst'][$i]['qty']);			
				//加總全部的客戶預算---
				for ($j=0; $j<12; $j++){  //上年度
					$k= ((int)$MM_SPR[$this_mm][$j]) - 1;
					$op['fc2'][$j] += $m_cast['fcst'][$i]['f'][$k];
					
					if($MM_SPR[$this_mm][$j] == '12') break;		
				}// endfor 上年度			
			 }// endfor forecast 上年度
			 $j++;

		   $where_str = " WHERE method='forecast' AND fty='".$PHP_fty."' AND year='".$yy2."' AND forecast.dept = '".$PHP_fty."' ";
		   $m_cast = $forecast->search(0,$where_str);
		   for ($i=0; $i<sizeof($m_cast['fcst']); $i++){
		   	$m_cast['fcst'][$i]['f'] = csv2array($m_cast['fcst'][$i]['qty']);
				 for ($l=$j; $l<13; $l++){  //下年度
	 			 	 $k= ((int)$MM_SPR[$this_mm][$l]) - 1;
	 		 		 
					 $op['fc2'][$l] += $m_cast['fcst'][$i]['f'][$k];
		 		 }// endfor 下年度								 
		 	 }// endfor forecast 下年度
		 	 
						
			//capacity
				$c1 = $capaci->get($PHP_fty, $yy1,'capacity');
				$c1_1 = $capaci->get($PHP_fty, $yy2,'capacity');
				for ($j=0; $j<12; $j++) { //上年度	
						$tmp_m = 'm'.$MM_SPR[$this_mm][$j];
						$op['c1'][$j] = $c1[$tmp_m];		
						if($MM_SPR[$this_mm][$j] == '12') break;			
				 }// endfor 上年度				
				 $j++;
				
				for ($l=$j; $l<13; $l++){  //下年度
						$tmp_m = 'm'.$MM_SPR[$this_mm][$l];
						$op['c1'][$l] = $c1_1[$tmp_m];
				}// endfor 下年度								
		}
	
		$mm_ord = $tmp_ann_ord = 0;
		

			$yy = $PHP_year1 + $YY_SPR[$this_mm][0];
			$yy_str = $yy."-".$MM_SPR[$this_mm][0].'-01';
			$yy = $PHP_year1 + $YY_SPR[$this_mm][(sizeof($MM_SPR[$this_mm])-1)];
			$yy_end = $yy."-".$MM_SPR[$this_mm][(sizeof($MM_SPR[$this_mm])-1)].'-31';

//業務接單
			$where_str = "AND s_order.dept <> '$PHP_fty'";
			$sal_ord = $order->get_one_etd_ord_full($PHP_fty, $yy_str,$yy_end, $where_str);
			$sal_ord_p = $order->get_one_etd_ord_prc_full($PHP_fty, $yy_str,$yy_end, $where_str);
			$sal_output = $order->get_one_etd_output_full($PHP_fty, $yy_str,$yy_end, $where_str);
			$sal_ship = $order->get_one_etd_shp_full($PHP_fty, $yy_str,$yy_end, $where_str);
			$sal_ship_p = $order->get_one_etd_shp_prc_full($PHP_fty, $yy_str,$yy_end, $where_str);
			$sal_un_out = $order->get_one_etd_unout_full($PHP_fty, $yy_str,$yy_end, $where_str);
			$sal_un_ord = $order->get_one_un_ord_full($PHP_fty, $yy_str,$yy_end, $where_str);
			$where_str = " AND pdt_saw_line.sc = 1 AND s_order.dept <> '$PHP_fty'";
			$sal_sub_out = $order->get_one_etd_output_full($PHP_fty, $yy_str,$yy_end, $where_str);
			
//工廠接單 -- 女裝
			$where_str = "AND s_order.dept = '$PHP_fty' AND s_order.line_sex = 'F'";
			$fty_ord = $order->get_one_etd_ord_full($PHP_fty, $yy_str,$yy_end, $where_str);
			$fty_ord_p = $order->get_one_etd_ord_prc_full($PHP_fty, $yy_str,$yy_end, $where_str);
			$fty_output = $order->get_one_etd_output_full($PHP_fty, $yy_str,$yy_end, $where_str);
			$fty_ship = $order->get_one_etd_shp_full($PHP_fty, $yy_str,$yy_end, $where_str);
			$fty_ship_p = $order->get_one_etd_shp_prc_full($PHP_fty, $yy_str,$yy_end, $where_str);
			$fty_un_out = $order->get_one_etd_unout_full($PHP_fty, $yy_str,$yy_end, $where_str);
			$fty_un_ord = $order->get_one_un_ord_full($PHP_fty, $yy_str,$yy_end, $where_str);
			$where_str = " AND pdt_saw_line.sc = 1 AND s_order.dept = '$PHP_fty' AND s_order.line_sex = 'F'";
			$fty_sub_out = $order->get_one_etd_output_full($PHP_fty, $yy_str,$yy_end, $where_str);	
			
			
			
			
//工廠接單 -- 男裝
			$where_str = "AND s_order.dept = '$PHP_fty' AND s_order.line_sex = 'M'";
			$mn_ord = $order->get_one_etd_ord_full($PHP_fty, $yy_str,$yy_end, $where_str);
			$mn_ord_p = $order->get_one_etd_ord_prc_full($PHP_fty, $yy_str,$yy_end, $where_str);
			$mn_output = $order->get_one_etd_output_full($PHP_fty, $yy_str,$yy_end, $where_str);
			$mn_ship = $order->get_one_etd_shp_full($PHP_fty, $yy_str,$yy_end, $where_str);
			$mn_ship_p = $order->get_one_etd_shp_prc_full($PHP_fty, $yy_str,$yy_end, $where_str);
			$mn_un_out = $order->get_one_etd_unout_full($PHP_fty, $yy_str,$yy_end, $where_str);
			$mn_un_ord = $order->get_one_un_ord_full($PHP_fty, $yy_str,$yy_end, $where_str);
			$where_str = " AND pdt_saw_line.sc = 1 AND s_order.dept = '$PHP_fty' AND s_order.line_sex = 'M'";
			$mn_sub_out = $order->get_one_etd_output_full($PHP_fty, $yy_str,$yy_end, $where_str);


//龍安廠
			$where_str = "AND s_order.line_sex = 'A'";
			$ann_ord = $order->get_one_etd_ord_full($PHP_fty, $yy_str,$yy_end, $where_str);
			$ann_ord_p = $order->get_one_etd_ord_prc_full($PHP_fty, $yy_str,$yy_end, $where_str);
			$ann_output = $order->get_one_etd_output_full($PHP_fty, $yy_str,$yy_end, $where_str);
			$ann_ship = $order->get_one_etd_shp_full($PHP_fty, $yy_str,$yy_end, $where_str);
			$ann_ship_p = $order->get_one_etd_shp_prc_full($PHP_fty, $yy_str,$yy_end, $where_str);
			$ann_un_out = $order->get_one_etd_unout_full($PHP_fty, $yy_str,$yy_end, $where_str);
			$ann_un_ord = $order->get_one_un_ord_full($PHP_fty, $yy_str,$yy_end, $where_str);
			$where_str = " AND pdt_saw_line.sc = 1 AND s_order.line_sex = 'A'";
			$ann_sub_out = $order->get_one_etd_output_full($PHP_fty, $yy_str,$yy_end, $where_str);


		for($i=0; $i<sizeof($MM_SPR[$this_mm]); $i++)
		{
			$yy = $PHP_year1 + $YY_SPR[$this_mm][$i];
			$yymm = $yy."-".$MM_SPR[$this_mm][$i];
			$op['yy'][] = $yy;
			
			$op['sal_ord'][$i]	= (!isset($sal_ord[$yymm])) ? 0 : $sal_ord[$yymm];
			$op['fty_ord'][$i]  = (!isset($fty_ord[$yymm])) ? 0 : $fty_ord[$yymm];
			$op['mn_ord'][$i]   = (!isset($mn_ord[$yymm])) ? 0 : $mn_ord[$yymm];
			$op['ann_ord'][$i]   = (!isset($ann_ord[$yymm])) ? 0 : $ann_ord[$yymm];

			$op['sal_ord_p'][$i]	= (!isset($sal_ord_p[$yymm])) ? 0 : $sal_ord_p[$yymm];
			$op['fty_ord_p'][$i]  = (!isset($fty_ord_p[$yymm])) ? 0 : $fty_ord_p[$yymm];
			$op['mn_ord_p'][$i]   = (!isset($mn_ord_p[$yymm])) ? 0 : $mn_ord_p[$yymm];
			$op['ann_ord_p'][$i]   = (!isset($ann_ord_p[$yymm])) ? 0 : $ann_ord_p[$yymm];

			$op['sal_output'][$i] = (!isset($sal_output[$yymm])) ? 0 : $sal_output[$yymm];
			$op['fty_output'][$i] = (!isset($fty_output[$yymm])) ? 0 : $fty_output[$yymm];
			$op['mn_output'][$i]  = (!isset($mn_output[$yymm])) ? 0 : $mn_output[$yymm];
			$op['ann_output'][$i]  = (!isset($ann_output[$yymm])) ? 0 : $ann_output[$yymm];

			$op['sal_ship'][$i]  = (!isset($sal_ship[$yymm])) ? 0 : $sal_ship[$yymm];			
			$op['fty_ship'][$i]  = (!isset($fty_ship[$yymm])) ? 0 : $fty_ship[$yymm];
			$op['mn_ship'][$i]   = (!isset($mn_ship[$yymm])) ? 0 : $mn_ship[$yymm];
			$op['ann_ship'][$i]   = (!isset($ann_ship[$yymm])) ? 0 : $ann_ship[$yymm];

			$op['sal_ship_p'][$i] = (!isset($sal_ship_p[$yymm])) ? 0 : $sal_ship_p[$yymm];
			$op['fty_ship_p'][$i] = (!isset($fty_ship_p[$yymm])) ? 0 : $fty_ship_p[$yymm];
			$op['mn_ship_p'][$i] = (!isset($mn_ship_p[$yymm])) ? 0 : $mn_ship_p[$yymm];
			$op['ann_ship_p'][$i] = (!isset($ann_ship_p[$yymm])) ? 0 : $ann_ship_p[$yymm];

			$op['sal_un_out'][$i] = (!isset($sal_un_out[$yymm])) ? 0 : $sal_un_out[$yymm];
			$op['fty_un_out'][$i] = (!isset($fty_un_out[$yymm])) ? 0 : $fty_un_out[$yymm];
			$op['mn_un_out'][$i] = (!isset($mn_un_out[$yymm])) ? 0 : $mn_un_out[$yymm];
			$op['ann_un_out'][$i] = (!isset($ann_un_out[$yymm])) ? 0 : $ann_un_out[$yymm];

			$op['sal_un_ord'][$i] = (!isset($sal_un_ord[$yymm])) ? 0 : $sal_un_ord[$yymm];
			$op['fty_un_ord'][$i] = (!isset($fty_un_ord[$yymm])) ? 0 : $fty_un_ord[$yymm];
			$op['mn_un_ord'][$i] = (!isset($mn_un_ord[$yymm])) ? 0 : $mn_un_ord[$yymm];
			$op['ann_un_ord'][$i] = (!isset($ann_un_ord[$yymm])) ? 0 : $ann_un_ord[$yymm];

			$op['sal_sub_out'][$i] = (!isset($sal_sub_out[$yymm])) ? 0 : $sal_sub_out[$yymm];
			$op['fty_sub_out'][$i] = (!isset($fty_sub_out[$yymm])) ? 0 : $fty_sub_out[$yymm];
			$op['mn_sub_out'][$i] = (!isset($mn_sub_out[$yymm])) ? 0 : $mn_sub_out[$yymm];
			$op['ann_sub_out'][$i] = (!isset($ann_sub_out[$yymm])) ? 0 : $ann_sub_out[$yymm];

			$op['sal_sumy'][$i] = $op['sal_ord'][$i] + $op['sal_un_ord'][$i] + $op['fc1'][$i];
			$op['fty_sumy'][$i] = $op['fty_ord'][$i] + $op['fty_un_ord'][$i] + $op['fc2'][$i];
			$op['mn_sumy'][$i] = $op['mn_ord'][$i] + $op['mn_un_ord'][$i];
			$op['ann_sumy'][$i] = $op['ann_ord'][$i] + $op['ann_un_ord'][$i];

			$mm_ord += $op['mn_ord'][$i];
			$tmp_ann_ord += $op['ann_ord'][$i];
			$op['ord_qty'][$i] = $op['sal_ord'][$i]+$op['fty_ord'][$i]+$op['mn_ord'][$i];
			$op['fty_all'][$i] = $op['fty_ord'][$i]+$op['mn_ord'][$i];			

				if($op['c1'][$i] > 0) 
				{
					$op['sal_rat'][$i] = ($op['sal_ord'][$i]/$op['c1'][$i])*100;
					$op['fty_rat'][$i] = ($op['fty_all'][$i]/$op['c1'][$i])*100;
					$op['ord_rat'][$i] = ($op['ord_qty'][$i]/$op['c1'][$i])*100;
				}else{
					$op['sal_rat'][$i] = 0;
					$op['fty_rat'][$i] = 0;
					$op['ord_rat'][$i] = 0;
				}
				$op['mn_check'] = $mm_ord;
				$op['ann_check'] = $tmp_ann_ord;

			$k= ((int)$MM_SPR[$this_mm][$i]) - 1;
			$op['i'][$i] =$k;
			$op['mm_name'][$i] = $MM2[$MM_SPR[$this_mm][$i]];			

		}

		
			$op['mm'] = $MM_SPR[$this_mm];
			$op['this_mm'] = $this_mm;
		page_display($op,'057', $TPL_PDTS_VIEW_DET);
	break;		
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//			 job 81-2-1    SALES ETP 月報表
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "etp_ord_prc":

		check_authority('056',"view");
		$string = $PHP_year."-".$PHP_month;
		$op['dist'] = array();
	
		if(isset($PHP_mk))
		{
			if($PHP_mk == 1)$where_str = " AND s_order.dept <> '$PHP_fty' AND s_order.dept <> 'J1'";
			if($PHP_mk == 2)$where_str = " AND (s_order.dept = '$PHP_fty' OR s_order.dept = 'J1') AND s_order.line_sex = 'F'";
			if($PHP_mk == 3)$where_str = " AND (s_order.dept = '$PHP_fty' OR s_order.dept = 'J1') AND s_order.line_sex = 'M'";
			if($PHP_mk == 4)$where_str = " AND s_order.line_sex = 'A'";

			if($PHP_mk == 1) $op['title_mk'] = '-- by sales';
			if($PHP_mk == 2) $op['title_mk'] = '-- by factory (女裝線)';
			if($PHP_mk == 3) $op['title_mk'] = '-- by factory (男裝線)';
			if($PHP_mk == 4) $op['title_mk'] = '-- 龍安廠';
			
		}else{
			$where_str ='';
		}	
		
		if (!$result = $capaci->search_etp_ord($PHP_fty,$string,500,$where_str)) {   
			$op['msg']= $capaci->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$op['ttl_su'] = 0;
		$op['ttl_qty'] = 0;
		$op['ttl_sales'] = 0;
		$op['sales_fob'] = 0;
		
	if($result != "none"){
		$op['ord']=$result;
		for ($i = 0; $i < count($result); $i++) {   // $i 為 訂單的總數量			
			$op['ord'][$i]['sort'] = $result[$i]['status'];
			$op['ord'][$i]['ord_num'] = $result[$i]['order_num'];			
			$op['ord'][$i]['status'] =get_ord_status($result[$i]['status']);
			$op['ord'][$i]['sales'] = $result[$i]['qty'] * $result[$i]['uprice'];
			
			$op['ttl_su'] = $op['ttl_su'] + $result[$i]['su'];
			$op['ttl_qty'] = $op['ttl_qty'] + $result[$i]['qty'];
			$op['ttl_sales'] = $op['ttl_sales'] + $op['ord'][$i]['sales'];
		}
		
		$op['ord'] = bubble_sort($op['ord']);
		
		$s_su = $s_qty = $s_sales = 0; $ship_mk  = 0;
		$for_num = sizeof($op['ord']);
		for ($i = 0; $i < $for_num; $i++) { // $i 為 訂單的總數量			
		
			if($ship_mk == 0 && $op['ord'][$i]['sort'] < 12)
			{
				if($i==0)
				{
					$j = $i;
					$op['ord'][$j]['sub_su'] = $s_su;
					$op['ord'][$j]['sub_qty'] = $s_qty;
					$op['ord'][$j]['sub_sales'] = $s_sales;
					/* $op['ord'][$j]['sub_mk'] = 1; */
					$ship_mk = 1;
					$s_su = $s_qty = $s_sales = 0;
				}
				else
				{ 
					$j = $i-1;
					$op['ord'][$j]['sub_su'] = $s_su;
					$op['ord'][$j]['sub_qty'] = $s_qty;
					$op['ord'][$j]['sub_sales'] = $s_sales;
					$op['ord'][$j]['sub_mk'] = 1;
					$ship_mk = 1;
					$s_su = $s_qty = $s_sales = 0;
				}
			}
			
			$s_su += $op['ord'][$i]['su'];
			$s_qty += $op['ord'][$i]['qty'];
			$s_sales += $op['ord'][$i]['sales'];
			

		}			
		$j = $i-1;
		$op['ord'][$j]['sub_su'] = $s_su;
		$op['ord'][$j]['sub_qty'] = $s_qty;
		$op['ord'][$j]['sub_sales'] = $s_sales;
		$op['ord'][$j]['sub_mk'] = 1;
	
		
		
	} else{ //if($result != "none")
		$op['record_NONE'] = "1";
	}
		$op['fty'] = $PHP_fty;
		$op['year'] = $PHP_year;
		$op['month'] = $PHP_month;		

	page_display($op,'056', $TPL_ETP_ORD_PRICE);
	break;
	
	
//-------------------------------------------------------------------------------------
// job 82  生產產能
// case "ord_static":
//-------------------------------------------------------------------------------------
case "ord_static":
check_authority('056',"view");

// creat cust combo box
$op['factory'] = $arry2->select($FACTORY,'','PHP_fty','select','');  	
$op['year_1'] = $arry2->select($YEAR_WORK,date('Y'),'PHP_year1','select','');  	
$op['month'] = $arry2->select($MONTH_WORK,'','PHP_month','select','');

$op['msg'] = $order->msg->get(2);

//080725message增加		
$note=$notify->search($GLOBALS['SCACHE']['ADMIN']['login_id'], "`read`=0");
$op['max_notify'] = $note['max_no'];

page_display($op,'056', $TPL_PDT_SEARCH);    	    
break;

//-------------------------------------------------------------------------------------
case "ord_static_view":
check_authority('056',"view");

$op['msg'] ='';

$op['fty'] = $PHP_fty;
$op['year1'] = $PHP_year1;
$op['month'] = $PHP_month;

if (!$op['fty']){
	$op['msg'][] = "Error ! you have to select target Factory !";
}

if(!$op['year1']){
	$op['year1'] = $GLOBALS['THIS_YEAR'];
	$PHP_year1 = $GLOBALS['THIS_YEAR'];
}  // 當沒輸入年份時預設為今年

if ($op['msg']){
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}

// 輸入check 結束  開始search
	
if($PHP_year1){
  
	//搜尋forcast
	$where_str = " WHERE method='forecast' AND fty='".$PHP_fty."' AND year='".$PHP_year1."' ";
	$m_cast = $forecast->search(0,$where_str);

	$ary=array('ord','sch','out','shp','shp_s','shp_tp','shp_ly','shp_s_p','shp_tp_p','shp_ly_p','un_sch','un_out','un_apv','sub_out','sumy','shp_p','ord_p','ord_T','ord_B');			  
	$mm=array('01','02','03','04','05','06','07','08','09','10','11','12','13');
	for ($k=0; $k<sizeof($ary); $k++) $op[$ary[$k]]['13']=0;			  	
	for ($i=0; $i<13; $i++) $op['forcast'][$mm[$i]]=0;   	  		  

	for ($i=0; $i<sizeof($m_cast['fcst']); $i++){
		$m_cast['fcst'][$i]['f'] = csv2array($m_cast['fcst'][$i]['qty']);			
		//加總全部的客戶預算---
		for ($j=0; $j<13; $j++){
			$op['forcast'][$mm[$j]] = $op['forcast'][$mm[$j]] + $m_cast['fcst'][$i]['f'][$j];
		}
	}

	for($i=0; $i<12; $i++)
	{
		$op['out'][$mm[$i]] = $op['sub_out'][$mm[$i]] = $op['un_out'][$mm[$i]] = $op['shp'][$mm[$i]] = $op['shp_p'][$mm[$i]]  = 0;
	}
	
	$tmp_out 		= $order->get_one_etd_output_full($PHP_fty, $PHP_year1."-01-01",$PHP_year1."-12-31");
	$tmp_sub_out 	= $order->get_one_etd_output_full($PHP_fty, $PHP_year1."-01-01",$PHP_year1."-12-31"," AND pdt_saw_line.sc = 1 ");
	$un_out 		= $order->get_one_etd_unout_full($PHP_fty, $PHP_year1."-01-01",$PHP_year1."-12-31");
	$tmp_shp 		= $order->get_one_etd_shp_full($PHP_fty, $PHP_year1."-01-01",$PHP_year1."-12-31");
	$tmp_shp_p 		= $order->get_one_etd_shp_prc_full($PHP_fty, $PHP_year1."-01-01",$PHP_year1."-12-31");
	$tmp_shp_s 		= $ship_doc->get_static_month_su($PHP_fty, $PHP_year1."-01-01",$PHP_year1."-12-31");
	$tmp_shp_s_p	= $ship_doc->get_static_month_prc($PHP_fty, $PHP_year1."-01-01",$PHP_year1."-12-31");

	if($PHP_fty == 'LY') {
		$tmp_shp_tp		= $ship_doc->get_static_month_su($PHP_fty, $PHP_year1."-01-01",$PHP_year1."-12-31"," AND ( shipping_doc_qty.ord_num LIKE 'A%' or shipping_doc_qty.ord_num LIKE 'B%' or shipping_doc_qty.ord_num LIKE 'D%' ) ");
		$tmp_shp_tp_p	= $ship_doc->get_static_month_prc($PHP_fty, $PHP_year1."-01-01",$PHP_year1."-12-31"," AND ( shipping_doc_qty.ord_num LIKE 'A%' or shipping_doc_qty.ord_num LIKE 'B%' or shipping_doc_qty.ord_num LIKE 'D%' ) ");
		
		$tmp_shp_ly		= $ship_doc->get_static_month_su($PHP_fty, $PHP_year1."-01-01",$PHP_year1."-12-31"," AND shipping_doc_qty.ord_num LIKE 'L%' ");
		$tmp_shp_ly_p	= $ship_doc->get_static_month_prc($PHP_fty, $PHP_year1."-01-01",$PHP_year1."-12-31"," AND shipping_doc_qty.ord_num LIKE 'L%' ");
	}
	
	foreach($tmp_out as $key	=>	$value)	{
		$tmp = explode('-',$key);
		$op['out'][$tmp[1]] = $value;
	}
	
	foreach($tmp_sub_out as $key	=>	$value)	{
		$tmp = explode('-',$key);
		$op['sub_out'][$tmp[1]] = $value;
	}
	
	foreach($un_out as $key	=>	$value)	{
		$tmp = explode('-',$key);
		$op['un_out'][$tmp[1]] = $value;
	}
	
	foreach($tmp_shp as $key	=>	$value)	{
		$tmp = explode('-',$key);
		$op['shp'][$tmp[1]] = $value;
	}
	
	foreach($tmp_shp_p as $key	=>	$value)	{
		$tmp = explode('-',$key);
		$op['shp_p'][$tmp[1]] = $value;
	}

	foreach($tmp_shp_s as $key	=>	$value)	{
		$tmp = explode('-',$key);
		$op['shp_s'][$tmp[1]] = $value;
	}

	if(!empty($tmp_shp_s_p))
	foreach($tmp_shp_s_p as $key	=>	$value)	{
		$tmp = explode('-',$key);
		$op['shp_s_p'][$tmp[1]] = $value;
	}
	
	foreach($tmp_shp_tp as $key	=>	$value)	{
		$tmp = explode('-',$key);
		$op['shp_tp'][$tmp[1]] = $value;
	}
	
	if($PHP_fty == 'LY') {
	
        if(!empty($tmp_shp_ly))
		foreach($tmp_shp_ly as $key	=>	$value)	{
			$tmp = explode('-',$key);
			$op['shp_ly'][$tmp[1]] = $value;
		}

        if(!empty($qty_shp_s))
		foreach($qty_shp_s as $key	=>	$value)	{
			$tmp = explode('-',$key);
			$op['qty_shp'][$tmp[1]] = $value;
		}



        if(!empty($tmp_shp_tp_p))
		foreach($tmp_shp_tp_p as $key	=>	$value)	{
			$tmp = explode('-',$key);
			$op['shp_tp_p'][$tmp[1]] = $value;
		}

        if(!empty($tmp_shp_ly_p))
		foreach($tmp_shp_ly_p as $key	=>	$value)	{
			$tmp = explode('-',$key);
			$op['shp_ly_p'][$tmp[1]] = $value;
		}
	}
	
	for($i=0; $i<12; $i++) {
		$yymm = $PHP_year1."-".$mm[$i];
		
		$op['ord'][$mm[$i]] = $order->get_one_etd_ord($PHP_fty, $yymm);
		$op['ord_T'][$mm[$i]] = $order->get_one_etd_ord_style($PHP_fty, $yymm, 'T');
		$op['ord_B'][$mm[$i]] = $order->get_one_etd_ord_style($PHP_fty, $yymm, 'B');
		$op['ord_p'][$mm[$i]] = $order->get_one_etd_ord_prc($PHP_fty, $yymm);
		$op['sch'][$mm[$i]] = $order->get_one_etd_schd($PHP_fty, $yymm);
		$op['un_sch'][$mm[$i]] = $op['ord'][$mm[$i]] - $op['sch'][$mm[$i]];
		$op['un_apv'][$mm[$i]] = $order->get_one_un_ord($PHP_fty, $yymm);
		$op['sumy'][$mm[$i]] = $op['ord'][$mm[$i]] + $op['un_apv'][$mm[$i]] + $op['forcast'][$mm[$i]];
	}

	$un_apv[0]=$un_sch[0]=$un_shp[0]=$un_out[0]=$shp[0]=$out[0]=$sch[0]=$ord_etd[0]=0;	  

	for ($i=0; $i<12; $i++)
	{		
		$op['un_sch'][$mm[$i]]=$op['ord'][$mm[$i]]-$op['sch'][$mm[$i]];
		for ($k=0; $k<sizeof($ary); $k++) @$op[$ary[$k]]['13']=$op[$ary[$k]]['13']+$op[$ary[$k]][$mm[$i]];			  	
		$j=$i+1;
		$ord_etd[$j]=$ord_etd[$i]+$op['ord'][$mm[$i]];
		$sch[$j]=$sch[$i]+$op['sch'][$mm[$i]];
		$out[$j]=$out[$i]+$op['out'][$mm[$i]];
		$shp[$j]=$shp[$i]+$op['shp'][$mm[$i]];
		$un_sch[$j]=$un_sch[$i]+$op['un_sch'][$mm[$i]];
		$un_out[$j]=$un_out[$i]+$op['un_out'][$mm[$i]];
	}
	  
	if(!$c1 = $capaci->get($PHP_fty, $PHP_year1,'capacity')){  //capacity
		$op['msg']= $capaci->msg->get(2);
		$layout->assign($op);
		$layout->display($TPL_ERROR);  		    
		break;
	}
		
	$sumc = 0;
	
	for ($j=1;$j<13;$j++){
		$sumc = $sumc + $c1[$j+3];
		$op['c1'][$j] = $PG->y[$j-1] = $c1[$j+3];  // 取出陣列內的項數
	}
	
	$op['c1']['sum'] = $sumc;
	
}   // end if ($PHP_year1)


$capa[0]=0;
for ($j=1;$j<13;$j++){				
	$capa[$j]=$capa[($j-1)]+$op['c1'][$j];
}

//引入 graph class
include_once($config['root_dir']."/lib/src/jpgraph.php");
include_once($config['root_dir']."/lib/src/jpgraph_line.php");

include ($config['root_dir']."/lib/src/jpgraph_bar.php");

$graphic_title = " FTY: ".$PHP_fty." Order Z Chart for month: ".$PHP_year1;
$mm=array('0','JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC');
$mom=array(0,0,0,0,0,0,0,0,0,0,0,0,0);

$graph = new Graph(700, 300, "auto");
$graph->SetScale("textlin");
$graph->ygrid->SetFill(true,'#EFEFEF@0.5','#BBCCFF@0.5');
$graph->img->SetMargin(60,30,40,40);    
$graph->subtitle->Set('( Monthly Accumulated Chart )');
$graph->SetShadow();

// Setup X-scale
$graph->xaxis->SetTickLabels($mm);
$graph->xaxis->SetFont(FF_ARIAL,FS_NORMAL,8);

if($PHP_month){  //今天的標示
	// Add a vertical line at the end scale position '7'
	$m=$PHP_month;
	$m = $m+1-1;
	$mom[$m]=$capa['12'];
	$bplot = new BarPlot($mom);
	$bplot->SetFillColor("gainsboro");
	$graph->Add($bplot);
				
}

// setup capacity plot
$capaplot = new LinePlot($capa);
$capaplot->SetWeight(1.5);
$capaplot->SetColor('navy');
$capaplot->SetLegend('capacity '.number_format($capa['12']).' SU');
$graph->Add($capaplot);



// setup etp plot
$etpplot = new LinePlot($ord_etd);
$etpplot->SetWeight(1.5);
$etpplot->SetColor('teal');
$etpplot->SetLegend('Order '.number_format($ord_etd['12']).' SU');
$graph->Add($etpplot);

// setup schedule plot
$schdplot = new LinePlot($sch);
$schdplot->SetWeight(1.5);
$schdplot->SetColor('darkred');
$schdplot->SetLegend('schedule '.number_format($sch['12']).' SU');
$graph->Add($schdplot);

// setup out-put plot
$outplot = new LinePlot($out);
$outplot->SetWeight(1.5);
$outplot->SetColor('red');
$outplot->SetLegend('out-put '.number_format($out['12']).' SU');
$graph->Add($outplot);

// setup ship plot
$shpplot = new LinePlot($shp);
$shpplot->SetWeight(1.5);
$shpplot->SetColor('black');
$shpplot->SetLegend('SHIP '.number_format($shp['12']).' SU');
$graph->Add($shpplot);

$graph->legend->SetShadow('gray@0.4',5);
$graph->legend->Pos(0.25,0.3,"center","center");
$graph->title->Set($graphic_title);
$graph->title->SetFont(FF_FONT1,FS_BOLD,8);

$op['echart_1'] = $graph->Stroke('picture/e_chart1.png');

			$op['msg']= $capaci->msg->get(2);
// print_r($op);
$layout->assign($op);
$layout->display($TPL_PDT_VIEW);		    	    

break;
	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//			 job 81-2-2    ord_qty_wkly 訂單狀態 -- 分業務和代工單
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "ord_qty_wkly":
		check_authority('056',"view");

		$op['msg'] ='';

		$op['fty'] = $PHP_fty;
		$op['year1'] = $PHP_year1;
		$op['month'] = $PHP_month;
		
		if (!$op['fty']){
			$op['msg'][] = "Error ! you have to select target Factory !";
		}

		if(!$op['year1']){ 
			$op['year1'] = $GLOBALS['THIS_YEAR'];
			$PHP_year1 = $GLOBALS['THIS_YEAR'];
		
		}  // 當沒輸入年份時預設為今年

		if ($op['msg']){	
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		// 輸入check 結束  開始search
//------------------------------------------  1 st year ----------------------
	// capacity start		
			  if(!$c1 = $capaci->get($PHP_fty, $PHP_year1,'capacity')){  //capacity
					$op['msg']= $capaci->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
				}
			   $sumc = 0;
				for ($j=1;$j<13;$j++){
						$sumc = $sumc + $c1[$j+3];
					$op['c1'][$j] = $PG->y[$j-1] = $c1[$j+3];  // 取出陣列內的項數
				}
				$op['c1']['sum'] = $sumc;
	// capacity end			
				
			
			if($PHP_year1){  // 業務訂單
			  
			  $mm=array('01','02','03','04','05','06','07','08','09','10','11','12','13');
			  $ary=array('sal_ord','sal_out','sal_shp','un_sal_out','sal_sub_out','sal_un_apv','sal_sumy','sal_shp_p','sal_ord_p');
		  	for ($k=0; $k<sizeof($ary); $k++) $op[$ary[$k]]['13']=0;			  	

		 //搜尋forcast
				$where_str = " WHERE method='forecast' AND fty='".$PHP_fty."' AND year='".$PHP_year1."' AND forecast.dept <> '".$PHP_fty."' ";
			  $m_cast = $forecast->search(0,$where_str);
			  for ($j=0; $j<13; $j++)	$op['fc1'][$mm[$j]] = 0;
				for ($i=0; $i<sizeof($m_cast['fcst']); $i++){
					$m_cast['fcst'][$i]['f'] = csv2array($m_cast['fcst'][$i]['qty']);			
					//加總全部的客戶預算---
					for ($j=0; $j<13; $j++){
						$op['fc1'][$mm[$j]] = $op['fc1'][$mm[$j]] + $m_cast['fcst'][$i]['f'][$j];
					}
				}	

				for($i=0; $i<12; $i++)
					$op['sal_out'][$mm[$i]] = $op['sal_sub_out'][$mm[$i]] = $op['un_sal_out'][$mm[$i]] = $op['sal_shp'][$mm[$i]] = $op['sal_shp_p'][$mm[$i]] =0;
			
				$tmp_out = $order->get_one_etd_output_full($PHP_fty, $PHP_year1."-01-01",$PHP_year1."-12-31"," AND s_order.dept <> '$PHP_fty'",2);
				$tmp_sub_out = $order->get_one_etd_output_full($PHP_fty, $PHP_year1."-01-01",$PHP_year1."-12-31"," AND pdt_saw_line.sc = 1 AND s_order.dept <> '$PHP_fty'",2);
				$un_out = $order->get_one_etd_unout_full($PHP_fty, $PHP_year1."-01-01",$PHP_year1."-12-31"," AND s_order.dept <> '$PHP_fty'",2);
				$tmp_shp = $order->get_one_etd_shp_full($PHP_fty, $PHP_year1."-01-01",$PHP_year1."-12-31"," AND s_order.dept <> '$PHP_fty'",2);
				$tmp_shp_p = $order->get_one_etd_shp_prc_full($PHP_fty, $PHP_year1."-01-01",$PHP_year1."-12-31"," AND s_order.dept <> '$PHP_fty'",2);

				foreach($tmp_out as $key	=>	$value) $op['sal_out'][$key] = $value;
				foreach($tmp_sub_out as $key	=>	$value)$op['sal_sub_out'][$key] = $value;
				foreach($un_out as $key	=>	$value)$op['un_sal_out'][$key] = $value;
				foreach($tmp_shp as $key	=>	$value)$op['sal_shp'][$key] = $value;
				foreach($tmp_shp_p as $key	=>	$value)$op['sal_shp_p'][$key] = $value;
	
				for($i=0; $i<12; $i++)
				{
					$yymm = $PHP_year1."-".$mm[$i];
					$where_str = " AND s_order.dept <> '$PHP_fty'";
					$op['sal_ord'][$mm[$i]] = $order->get_one_etd_ord($PHP_fty, $yymm, $where_str);
					$op['sal_ord_p'][$mm[$i]] = $order->get_one_etd_ord_prc($PHP_fty, $yymm, $where_str);
					$op['sal_sch'][$mm[$i]] = $order->get_one_etd_schd($PHP_fty, $yymm, $where_str);
					$op['sal_un_sch'][$mm[$i]] = $order->get_one_etd_unsch($PHP_fty, $yymm, $where_str);
					$op['sal_un_apv'][$mm[$i]] = $order->get_one_un_ord($PHP_fty, $yymm, $where_str);
					$op['sal_sumy'][$mm[$i]] = $op['sal_ord'][$mm[$i]] + $op['sal_un_apv'][$mm[$i]] + $op['fc1'][$mm[$i]];
//					$op['sal_sub_out'][$mm[$i]] = $order->get_one_etd_output($PHP_fty, $yymm, $where_str);
//					$op['un_sal_out'][$mm[$i]] = $order->get_one_etd_unout($PHP_fty, $yymm, $where_str);
//					$op['sal_out'][$mm[$i]] = $order->get_one_etd_output($PHP_fty, $yymm, $where_str);
//					$op['sal_shp'][$mm[$i]] = $order->get_one_etd_shp($PHP_fty, $yymm, $where_str);
//					$op['sal_shp_p'][$mm[$i]] = $order->get_one_etd_shp_prc($PHP_fty, $yymm, $where_str);
//					$where_str = " AND pdt_saw_line.sc = 1 AND s_order.dept <> '$PHP_fty'";

					for ($k=0; $k<sizeof($ary); $k++) $op[$ary[$k]]['13']+=$op[$ary[$k]][$mm[$i]];	
				}
			}   // end if ($PHP_year1)業務


			if($PHP_year1){  // 工廠訂單 -- 女裝
			   
			  $mm=array('01','02','03','04','05','06','07','08','09','10','11','12','13');
			  $ary=array('fty_ord','fty_out','fty_shp','un_fty_out','fty_un_apv','fty_sub_out','fty_sumy','fty_shp_p','fty_ord_p');
		  	for ($k=0; $k<sizeof($ary); $k++) $op[$ary[$k]]['13']=0;			  	

		 //搜尋forcast
				$where_str = " WHERE method='forecast' AND fty='".$PHP_fty."' AND year='".$PHP_year1."' AND forecast.dept = '".$PHP_fty."' ";
			  $m_cast = $forecast->search(0,$where_str);
			  for ($j=0; $j<13; $j++)	$op['fc2'][$mm[$j]] = 0;
				for ($i=0; $i<sizeof($m_cast['fcst']); $i++){
					$m_cast['fcst'][$i]['f'] = csv2array($m_cast['fcst'][$i]['qty']);			
					//加總全部的客戶預算---
					for ($j=0; $j<13; $j++){
						$op['fc2'][$mm[$j]] = $op['fc2'][$mm[$j]] + $m_cast['fcst'][$i]['f'][$j];
					}
				}	

				for($i=0; $i<12; $i++)
					$op['fty_out'][$mm[$i]] = $op['fty_sub_out'][$mm[$i]] = $op['un_fty_out'][$mm[$i]] = $op['fty_shp'][$mm[$i]] = $op['fty_shp_p'][$mm[$i]] =0;
			
				$tmp_out = $order->get_one_etd_output_full($PHP_fty, $PHP_year1."-01-01",$PHP_year1."-12-31"," AND (s_order.dept = '$PHP_fty' OR s_order.dept = 'J1') AND s_order.line_sex = 'F'",2);
				$tmp_sub_out = $order->get_one_etd_output_full($PHP_fty, $PHP_year1."-01-01",$PHP_year1."-12-31"," AND pdt_saw_line.sc = 1 AND (s_order.dept = '$PHP_fty' OR s_order.dept = 'J1') AND s_order.line_sex = 'F'",2);
				$un_out = $order->get_one_etd_unout_full($PHP_fty, $PHP_year1."-01-01",$PHP_year1."-12-31"," AND (s_order.dept = '$PHP_fty' OR s_order.dept = 'J1') AND s_order.line_sex = 'F'",2);
				$tmp_shp = $order->get_one_etd_shp_full($PHP_fty, $PHP_year1."-01-01",$PHP_year1."-12-31"," AND (s_order.dept = '$PHP_fty' OR s_order.dept = 'J1') AND s_order.line_sex = 'F'",2);
				$tmp_shp_p = $order->get_one_etd_shp_prc_full($PHP_fty, $PHP_year1."-01-01",$PHP_year1."-12-31"," AND (s_order.dept = '$PHP_fty' OR s_order.dept = 'J1') AND s_order.line_sex = 'F'",2);

				foreach($tmp_out as $key	=>	$value) $op['fty_out'][$key] = $value;
				foreach($tmp_sub_out as $key	=>	$value)$op['fty_sub_out'][$key] = $value;
				foreach($un_out as $key	=>	$value)$op['un_fty_out'][$key] = $value;
				foreach($tmp_shp as $key	=>	$value)$op['fty_shp'][$key] = $value;
				foreach($tmp_shp_p as $key	=>	$value)$op['fty_shp_p'][$key] = $value;

				for($i=0; $i<12; $i++)
				{
					$yymm = $PHP_year1."-".$mm[$i];
					$where_str = " AND (s_order.dept = '$PHP_fty' OR s_order.dept = 'J1') AND s_order.line_sex = 'F'";
					$op['fty_ord'][$mm[$i]] = $order->get_one_etd_ord($PHP_fty, $yymm, $where_str);
					$op['fty_ord_p'][$mm[$i]] = $order->get_one_etd_ord_prc($PHP_fty, $yymm, $where_str);
					$op['fty_un_apv'][$mm[$i]] = $order->get_one_un_ord($PHP_fty, $yymm, $where_str);
					$op['fty_sumy'][$mm[$i]] = $op['fty_ord'][$mm[$i]] + $op['fty_un_apv'][$mm[$i]] + $op['fc2'][$mm[$i]];
//					$where_str = " AND pdt_saw_line.sc = 1 AND (s_order.dept = '$PHP_fty' OR s_order.dept = 'J1') AND s_order.line_sex = 'F'";
//					$op['fty_sub_out'][$mm[$i]] = $order->get_one_etd_output($PHP_fty, $yymm, $where_str);
//					$op['fty_out'][$mm[$i]] = $order->get_one_etd_output($PHP_fty, $yymm, $where_str);
//					$op['fty_shp'][$mm[$i]] = $order->get_one_etd_shp($PHP_fty, $yymm, $where_str);
//					$op['fty_shp_p'][$mm[$i]] = $order->get_one_etd_shp_prc($PHP_fty, $yymm, $where_str);
//					$op['un_fty_out'][$mm[$i]] = $order->get_one_etd_unout($PHP_fty, $yymm, $where_str);

					for ($k=0; $k<sizeof($ary); $k++) $op[$ary[$k]]['13']+=$op[$ary[$k]][$mm[$i]];	

				}
			
			
			
			}//end if工廠接單


			if($PHP_year1){  // 工廠訂單  -- 男裝
			   
			  $mm=array('01','02','03','04','05','06','07','08','09','10','11','12','13');
 		    $ary=array('mn_ord','mn_out','mn_shp','un_mn_out','mn_un_apv','mn_sub_out','mn_sumy','mn_shp_p','mn_ord_p');
		  	for ($k=0; $k<sizeof($ary); $k++) $op[$ary[$k]]['13']=0;	

				for($i=0; $i<12; $i++)
					$op['mn_out'][$mm[$i]] = $op['mn_sub_out'][$mm[$i]] = $op['un_mn_out'][$mm[$i]] = $op['mn_shp'][$mm[$i]] = $op['mn_shp_p'][$mm[$i]] =0;
			
				$tmp_out = $order->get_one_etd_output_full($PHP_fty, $PHP_year1."-01-01",$PHP_year1."-12-31"," AND (s_order.dept = '$PHP_fty' OR s_order.dept = 'J1') AND s_order.line_sex = 'M'",2);
				$tmp_sub_out = $order->get_one_etd_output_full($PHP_fty, $PHP_year1."-01-01",$PHP_year1."-12-31"," AND pdt_saw_line.sc = 1 AND (s_order.dept = '$PHP_fty' OR s_order.dept = 'J1') AND s_order.line_sex = 'M'",2);
				$un_out = $order->get_one_etd_unout_full($PHP_fty, $PHP_year1."-01-01",$PHP_year1."-12-31"," AND (s_order.dept = '$PHP_fty' OR s_order.dept = 'J1') AND s_order.line_sex = 'M'",2);
				$tmp_shp = $order->get_one_etd_shp_full($PHP_fty, $PHP_year1."-01-01",$PHP_year1."-12-31"," AND (s_order.dept = '$PHP_fty' OR s_order.dept = 'J1') AND s_order.line_sex = 'M'",2);
				$tmp_shp_p = $order->get_one_etd_shp_prc_full($PHP_fty, $PHP_year1."-01-01",$PHP_year1."-12-31"," AND (s_order.dept = '$PHP_fty' OR s_order.dept = 'J1') AND s_order.line_sex = 'M'",2);

				foreach($tmp_out as $key	=>	$value) $op['mn_out'][$key] = $value;
				foreach($tmp_sub_out as $key	=>	$value)$op['mn_sub_out'][$key] = $value;
				foreach($un_out as $key	=>	$value)$op['un_mn_out'][$key] = $value;
				foreach($tmp_shp as $key	=>	$value)$op['mn_shp'][$key] = $value;
				foreach($tmp_shp_p as $key	=>	$value)$op['mn_shp_p'][$key] = $value;


				for($i=0; $i<12; $i++)
				{
					$yymm = $PHP_year1."-".$mm[$i];
					$where_str = " AND (s_order.dept = '$PHP_fty' OR s_order.dept = 'J1') AND s_order.line_sex = 'M'";
					$op['mn_ord'][$mm[$i]] = $order->get_one_etd_ord($PHP_fty, $yymm, $where_str);
					$op['mn_ord_p'][$mm[$i]] = $order->get_one_etd_ord_prc($PHP_fty, $yymm, $where_str);
					$op['mn_sch'][$mm[$i]] = $order->get_one_etd_schd($PHP_fty, $yymm, $where_str);
					$op['mn_un_sch'][$mm[$i]] = $order->get_one_etd_unsch($PHP_fty, $yymm, $where_str);
					$op['mn_un_apv'][$mm[$i]] = $order->get_one_un_ord($PHP_fty, $yymm, $where_str);
					$op['mn_sumy'][$mm[$i]] = $op['mn_ord'][$mm[$i]] + $op['mn_un_apv'][$mm[$i]];
//					$where_str = " AND pdt_saw_line.sc = 1 AND (s_order.dept = '$PHP_fty' OR s_order.dept = 'J1') AND s_order.line_sex = 'M'";
//					$op['mn_sub_out'][$mm[$i]] = $order->get_one_etd_output($PHP_fty, $yymm, $where_str);
//					$op['un_mn_out'][$mm[$i]] = $order->get_one_etd_unout($PHP_fty, $yymm, $where_str);
//					$op['mn_out'][$mm[$i]] = $order->get_one_etd_output($PHP_fty, $yymm, $where_str);
//					$op['mn_shp'][$mm[$i]] = $order->get_one_etd_shp($PHP_fty, $yymm, $where_str);
//					$op['mn_shp_p'][$mm[$i]] = $order->get_one_etd_shp_prc($PHP_fty, $yymm, $where_str);

					for ($k=0; $k<sizeof($ary); $k++) $op[$ary[$k]]['13']+=$op[$ary[$k]][$mm[$i]];	
			 }			
	
			
			}//end if工廠接單

			if($PHP_year1){  // 龍安廠
			   
			  $mm=array('01','02','03','04','05','06','07','08','09','10','11','12','13');
 		    $ary=array('ann_ord','ann_out','ann_shp','un_ann_out','ann_un_apv','ann_sub_out','ann_sumy','ann_shp_p','ann_ord_p');
		  	for ($k=0; $k<sizeof($ary); $k++) $op[$ary[$k]]['13']=0;	

				for($i=0; $i<12; $i++)
					$op['ann_out'][$mm[$i]] = $op['ann_sub_out'][$mm[$i]] = $op['un_ann_out'][$mm[$i]] = $op['ann_shp'][$mm[$i]] = $op['ann_shp_p'][$mm[$i]] =0;
			
				$tmp_out = $order->get_one_etd_output_full($PHP_fty, $PHP_year1."-01-01",$PHP_year1."-12-31"," AND s_order.line_sex = 'A'",2);
				$tmp_sub_out = $order->get_one_etd_output_full($PHP_fty, $PHP_year1."-01-01",$PHP_year1."-12-31"," AND pdt_saw_line.sc = 1  AND s_order.line_sex = 'A'",2);
				$un_out = $order->get_one_etd_unout_full($PHP_fty, $PHP_year1."-01-01",$PHP_year1."-12-31"," AND s_order.line_sex = 'A'",2);
				$tmp_shp = $order->get_one_etd_shp_full($PHP_fty, $PHP_year1."-01-01",$PHP_year1."-12-31"," AND s_order.line_sex = 'A'",2);
				$tmp_shp_p = $order->get_one_etd_shp_prc_full($PHP_fty, $PHP_year1."-01-01",$PHP_year1."-12-31"," AND s_order.line_sex = 'A'",2);

				foreach($tmp_out as $key	=>	$value) $op['mn_out'][$key] = $value;
				foreach($tmp_sub_out as $key	=>	$value)$op['mn_sub_out'][$key] = $value;
				foreach($un_out as $key	=>	$value)$op['un_mn_out'][$key] = $value;
				foreach($tmp_shp as $key	=>	$value)$op['mn_shp'][$key] = $value;
				foreach($tmp_shp_p as $key	=>	$value)$op['mn_shp_p'][$key] = $value;


				for($i=0; $i<12; $i++)
				{
					$yymm = $PHP_year1."-".$mm[$i];
					$where_str = " AND s_order.line_sex = 'A'";
					$op['ann_ord'][$mm[$i]] = $order->get_one_etd_ord($PHP_fty, $yymm, $where_str);
					$op['ann_ord_p'][$mm[$i]] = $order->get_one_etd_ord_prc($PHP_fty, $yymm, $where_str);
					$op['ann_sch'][$mm[$i]] = $order->get_one_etd_schd($PHP_fty, $yymm, $where_str);
					$op['ann_un_sch'][$mm[$i]] = $order->get_one_etd_unsch($PHP_fty, $yymm, $where_str);
					$op['ann_un_apv'][$mm[$i]] = $order->get_one_un_ord($PHP_fty, $yymm, $where_str);
					$op['ann_sumy'][$mm[$i]] = $op['ann_ord'][$mm[$i]] + $op['ann_un_apv'][$mm[$i]];
//					$where_str = " AND pdt_saw_line.sc = 1  AND s_order.line_sex = 'A'";
//					$op['ann_sub_out'][$mm[$i]] = $order->get_one_etd_output($PHP_fty, $yymm, $where_str);
//					$op['un_ann_out'][$mm[$i]] = $order->get_one_etd_unout($PHP_fty, $yymm, $where_str);
//					$op['ann_out'][$mm[$i]] = $order->get_one_etd_output($PHP_fty, $yymm, $where_str);
//					$op['ann_shp'][$mm[$i]] = $order->get_one_etd_shp($PHP_fty, $yymm, $where_str);
//					$op['ann_shp_p'][$mm[$i]] = $order->get_one_etd_shp_prc($PHP_fty, $yymm, $where_str);

					for ($k=0; $k<sizeof($ary); $k++) $op[$ary[$k]]['13']+=$op[$ary[$k]][$mm[$i]];	
			 }	
		}// endif 龍安廠
	
			for ($i=0; $i<13; $i++) 
			{
				if($i == 12){$j='sum';}else{$j=$i+1;}
				$op['ord_qty'][$mm[$i]] = $op['sal_ord'][$mm[$i]]+$op['fty_ord'][$mm[$i]]+$op['mn_ord'][$mm[$i]];
				$op['fty_all'][$mm[$i]] = $op['fty_ord'][$mm[$i]]+$op['mn_ord'][$mm[$i]];
				$op['sal_rat'][$mm[$i]] = ($op['sal_ord'][$mm[$i]]/$op['c1'][$j])*100;
				$op['fty_rat'][$mm[$i]] = ($op['fty_all'][$mm[$i]]/$op['c1'][$j])*100;
				$op['ord_rat'][$mm[$i]] = ($op['ord_qty'][$mm[$i]]/$op['c1'][$j])*100;
			}

		
		$layout->assign($op);
		$layout->display($TPL_ord_qty_RPT2);		    	    

		break;		


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//			 job 81-2-1    SALES ETP 月報表
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "style_ord_mm":

		check_authority('056',"view");
		$string = $PHP_year."-".$PHP_month;
		$op['dist'] = array();
	
		if(isset($PHP_mk))
		{
			if($PHP_mk == 1)$where_str = " AND s_order.dept <> '$PHP_fty' AND s_order.dept <> 'J1'";
			if($PHP_mk == 2)$where_str = " AND (s_order.dept = '$PHP_fty' OR s_order.dept = 'J1') AND s_order.line_sex = 'F'";
			if($PHP_mk == 3)$where_str = " AND (s_order.dept = '$PHP_fty' OR s_order.dept = 'J1') AND s_order.line_sex = 'M'";
			if($PHP_mk == 4)$where_str = " AND s_order.line_sex = 'A'";
			
			if($PHP_mk == 1) $op['title_mk'] = '-- by sales';
			if($PHP_mk == 2) $op['title_mk'] = '-- by factory (女裝線)';
			if($PHP_mk == 3) $op['title_mk'] = '-- by factory (男裝線)';
			if($PHP_mk == 4) $op['title_mk'] = '-- 龍安廠';
			if($PHP_style=='T')$op['title_mk'] = '-- 上身';
			if($PHP_style=='B')$op['title_mk'] = '-- 下身';
			
		}else{
			$where_str ='';
		}	
		
		if (!$result = $capaci->search_etp_ord_style($PHP_fty,$string,$PHP_style,$where_str)) {   
			$op['msg']= $capaci->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$op['ttl_su'] = 0;
		$op['ttl_qty'] = 0;
		$op['sales_fob'] = 0;
		
	if($result != "none"){
		$op['ord']=$result;
		for ($i = 0; $i < count($result); $i++) {   // $i 為 訂單的總數量			
			$op['ord'][$i]['ord_num'] = $result[$i]['order_num'];			
			$op['ord'][$i]['status'] =get_ord_status($result[$i]['status']);
			$op['ttl_su'] = $op['ttl_su'] + $result[$i]['su'];
			$op['ttl_qty'] = $op['ttl_qty'] + $result[$i]['qty'];
		}
	} else{ //if($result != "none")
		$op['record_NONE'] = "1";
	}
		$op['fty'] = $PHP_fty;
		$op['year'] = $PHP_year;
		$op['month'] = $PHP_month;		

//增加訂單量(Q'TY)分類統計訂單數	    	    
	$dist = array ('1000','1500','3000','5000');
	$s_date = $PHP_year."-".$PHP_month."-01";
	$e_date = $PHP_year."-".$PHP_month."-31";
	$where_str = 'AND factory = dept';
	$op['fty_qty']=$report->ord_style_qty($PHP_fty,$s_date,$e_date,$dist,$PHP_style,$where_str);  //取出記錄	
	$where_str = 'AND factory <> dept';
	$op['sales_qty']=$report->ord_style_qty($PHP_fty,$s_date,$e_date,$dist,$PHP_style,$where_str);  //取出記錄	

	$op['fty_qty_ttl'] = $op['sales_qty_ttl'] = $op['ord_qty_ttl'] = 0;
	for($i=0; $i<sizeof($op['fty_qty']); $i++)
	{
		$op['ord_qty'][$i] = $op['fty_qty'][$i] + $op['sales_qty'][$i];
		$op['fty_qty_ttl'] += $op['fty_qty'][$i];
		$op['sales_qty_ttl'] += $op['sales_qty'][$i];
		$op['ord_qty_ttl']	+= $op['ord_qty'][$i];
	}
	
	for($i=0; $i<sizeof($op['fty_qty']); $i++)
	{
		$op['ord_qty_prc'][$i] =( $op['ord_qty'][$i] / $op['ord_qty_ttl'])*100;
	}	

	$op['dist'] = array('~ 1000', '1000 ~ 1500', '1500 ~ 3000', '3000 ~ 5000', '5000 ~');
	    	    
	    	    
	    	    
	    	    
	page_display($op,'056', $TPL_ETP_ORD);
	break;
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//			Monthly_ord_Excel    大貨訂單月報表(Excel匯出)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "monthly_ord_excel":
		$string = $PHP_year."-".$PHP_month;
		$now_time=getdate(); 
		$year=$now_time['year'];
		$mon=$now_time['mon'];
		if($now_time['mon']<10)
		{
			$mon="0".$now_time['mon'];
		}
		$mday=$now_time['mday'];
		if($now_time['mday']<10)
		{
			$mday="0".$now_time['mday'];
		}
		$hour=$now_time['hours'];
		if($now_time['hours']<10)
		{
			$hour="0".$now_time['hours'];
		}
		$min=$now_time['minutes'];
		if($now_time['minutes']<10)
		{
			$min="0".$now_time['minutes'];
		}

		//這邊是訂單查出來的所有資料
		if (!$result = $capaci->search_etp_ord($PHP_fty,$string,500,$where_str)) {   
			$op['msg']= $capaci->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		foreach($result as $key=>$value)
		{
			$result[$key]['sort'] = $value['status'];
		}
		$result = bubble_sort($result);
		
		require_once($config['root_dir']."/lib/spreadsheets/Worksheet.php");
		require_once($config['root_dir']."/lib/spreadsheets/Workbook.php");
		
		function HeaderingExcel($filename) {
		  /* ob_end_clean(); */
		  header("Content-type: application/vnd.ms-excel");
		  header("Content-Disposition: attachment; filename=$filename" );
		  header("Expires: 0");
		  header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
		  header("Pragma: public");
		  }
		  
		// HTTP headers
		HeaderingExcel('order'.$PHP_year.$PHP_month."_".$year.$mon.$mday.$hour.$min.'.xls');
	 
		// Creating a workbook
		$workbook = new Workbook("-");

		// Creating the first worksheet	
		$worksheet1 =& $workbook->add_worksheet($PHP_year.$PHP_month);

		//表頭用
		 $title_style =& $workbook->add_format();
		 $title_style->set_size(10);
		 $title_style->set_align('center');
		 $title_style->set_color('white');
		 $title_style->set_pattern();
		 $title_style->set_fg_color('black');

		 $f2 =& $workbook->add_format();
		 $f2->set_size(10);
		 $f2->set_align('center');
		 $f2->set_color('navy');
		 $f2->set_pattern();
		 $f2->set_fg_color('white');
		
		//設定Excel欄位寬度
		$ary = array(15,5,10,10,20,8,8,20,15);
		$ary_columns=array("Order#","Style","Q'TY","S.U.","ETD","FOB@","CM@","Total_sales","Status");
		//設定第個欄位的Index及最後一個欄位的Index與每個欄位的寬度
	  	for ($i=0; $i<sizeof($ary); $i++)	  
	  		$worksheet1->set_column(0,$i,$ary[$i]);
		//寫入字串Row column string
		foreach($ary_columns as $key => $val)
		{
			$worksheet1->write(0,$key,$val,$title_style);
		}
		
		foreach($result as $key => $value)
		{
			if($value['partial_num'] > 1)
			{
				$worksheet1->write($key+1,0,$value['order_num'].$value['mks'],f2);
			}
			else
			{
				$worksheet1->write($key+1,0,$value['order_num'],f2);
			}
			
			$worksheet1->write($key+1,1,$value['style'],f2);
			$worksheet1->write($key+1,2,$value['qty'],f2);
			$worksheet1->write($key+1,3,$value['su'],f2);
			$worksheet1->write($key+1,4,$value['etd'],f2);
			$worksheet1->write($key+1,5,$value['uprice'],f2);
			$worksheet1->write($key+1,6,$value['cm'],f2);
			$worksheet1->write($key+1,7,($value['qty']*$value['uprice']),f2);
			$status=get_ord_status($value['status']);
			
			$worksheet1->write($key+1,8,$status,f2);
		}
		$workbook->close();
		
	break;	
		
//-------------------------------------------------------------------------

}   // end case ---------

?>
