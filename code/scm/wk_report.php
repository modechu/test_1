<?php
session_start();
session_register	('SCACHE');
session_register	('PAGE');
session_register	('authority');
session_register	('where_str');
session_register	('parm');
session_register	('PHP_ses_etd');
session_register	('PHP_unstatus');
// echo $PHP_action.'<br>';
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
$monitor = new MONITOR();
if (!$monitor->init($mysql,"log")) { print "error!! cannot initialize database for LOG class"; exit; }

$op = array();

$MM['F']['01'] = array('11','12','01','02');
$MM['F']['02'] = array('12','01','02','03');
$MM['F']['03'] = array('01','02','03','04');
$MM['F']['04'] = array('02','03','04','05');
$MM['F']['05'] = array('03','04','05','06');
$MM['F']['06'] = array('04','05','06','07');
$MM['F']['07'] = array('05','06','07','08');
$MM['F']['08'] = array('06','07','08','09');
$MM['F']['09'] = array('07','08','09','10');
$MM['F']['10'] = array('08','09','10','11');
$MM['F']['11'] = array('09','10','11','12');
$MM['F']['12'] = array('10','11','12','01');

$YY['F']['01']	= array(-1,-1,0,0);
$YY['F']['02']	= array(-1,0,0,0);
$YY['F']['03']	= array(0,0,0,0);
$YY['F']['04']	= array(0,0,0,0);
$YY['F']['05']	= array(0,0,0,0);
$YY['F']['06']	= array(0,0,0,0);
$YY['F']['07']	= array(0,0,0,0);
$YY['F']['08']	= array(0,0,0,0);
$YY['F']['09']	= array(0,0,0,0);
$YY['F']['10']	= array(0,0,0,0);
$YY['F']['11']	= array(0,0,0,0);
$YY['F']['12']	= array(0,0,0,1);

$MM['M']['01'] = array('12','01','02');
$MM['M']['02'] = array('01','02','03');
$MM['M']['03'] = array('02','03','04');
$MM['M']['04'] = array('03','04','05');
$MM['M']['05'] = array('04','05','06');
$MM['M']['06'] = array('05','06','07');
$MM['M']['07'] = array('06','07','08');
$MM['M']['08'] = array('07','08','09');
$MM['M']['09'] = array('08','09','10');
$MM['M']['10'] = array('09','10','11');
$MM['M']['11'] = array('10','11','12');
$MM['M']['12'] = array('11','12','01');

$YY['M']['01']	= array(-1,0,0);
$YY['M']['02']	= array(0,0,0);
$YY['M']['03']	= array(0,0,0);
$YY['M']['04']	= array(0,0,0);
$YY['M']['05']	= array(0,0,0);
$YY['M']['06']	= array(0,0,0);
$YY['M']['07']	= array(0,0,0);
$YY['M']['08']	= array(0,0,0);
$YY['M']['09']	= array(0,0,0);
$YY['M']['10']	= array(0,0,0);
$YY['M']['11']	= array(0,0,0);
$YY['M']['12']	= array(0,0,1);

$MM['L']['01'] = array('12','01','02','03');
$MM['L']['02'] = array('01','02','03','04');
$MM['L']['03'] = array('02','03','04','05');
$MM['L']['04'] = array('03','04','05','06');
$MM['L']['05'] = array('04','05','06','07');
$MM['L']['06'] = array('05','06','07','08');
$MM['L']['07'] = array('06','07','08','09');
$MM['L']['08'] = array('07','08','09','10');
$MM['L']['09'] = array('08','09','10','11');
$MM['L']['10'] = array('09','10','11','12');
$MM['L']['11'] = array('10','11','12','01');
$MM['L']['12'] = array('11','12','01','02');

$YY['L']['01']	= array(-1,0,0,0);
$YY['L']['02']	= array(0,0,0,0);
$YY['L']['03']	= array(0,0,0,0);
$YY['L']['04']	= array(0,0,0,0);
$YY['L']['05']	= array(0,0,0,0);
$YY['L']['06']	= array(0,0,0,0);
$YY['L']['07']	= array(0,0,0,0);
$YY['L']['08']	= array(0,0,0,0);
$YY['L']['09']	= array(0,0,0,0);
$YY['L']['10']	= array(0,0,0,0);
$YY['L']['11']	= array(0,0,0,1);
$YY['L']['12']	= array(0,0,1,1);

$MM['S']['01'] = array('01','02','03','04','05','06','07','08','09','10','11','12');
$MM['S']['02'] = array('01');
$MM['S']['03'] = array('01','02');
$MM['S']['04'] = array('01','02','03');
$MM['S']['05'] = array('01','02','03','04');
$MM['S']['06'] = array('01','02','03','04','05');
$MM['S']['07'] = array('01','02','03','04','05','06');
$MM['S']['08'] = array('01','02','03','04','05','06','07');
$MM['S']['09'] = array('01','02','03','04','05','06','07','08');
$MM['S']['10'] = array('01','02','03','04','05','06','07','08','09');
$MM['S']['11'] = array('01','02','03','04','05','06','07','08','09','10');
$MM['S']['12'] = array('01','02','03','04','05','06','07','08','09','10','11');

$MONTHS = array('01','02','03','04','05','06','07','08','09','10','11','12');

session_register	('un_rcv_date');
$para_cm = $para->get(0,'un_rcv_date');
$un_rcv_date = $para_cm['set_value'] * -1;
// echo $PHP_action;
switch ($PHP_action) {
//=======================================================

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "fab_comp":	  //搜尋未完成主料預估用量
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "wk_report":
 	check_authority('065',"view");

	$this_yy = date('Y');
	$this_mm = date('m');
	$this_dd = date('d');
	if($this_dd < 10)
	{
		$mon_ary = $MM['F'][$this_mm];
		$year_ary = $YY['F'][$this_mm];
	}else if($this_dd > 20){
		$mon_ary = $MM['L'][$this_mm];
		$year_ary = $YY['L'][$this_mm];
	}else{
		$mon_ary = $MM['M'][$this_mm];
		$year_ary = $YY['M'][$this_mm];		
	}	
/*
	$act = $capaci->get('HJ', $this_yy,'actual');
	$so1 = $capaci->get_subcon('HJ', $this_yy);	
	$out_1 = $out_2 = $out_3 = 0;
	for($i=0; $i<sizeof($MM['S'][$this_mm]); $i++) 
	{
		$out_1 += ($act['m'.$MM['S'][$this_mm][$i]] - $so1[$MM['S'][$this_mm][$i]]);
	}

	$out_1 /= ($i);
	echo "<br>HJ ".$MM['S'][$this_mm][0]."-".$MM['S'][$this_mm][$i-1]."AVG out-put (IN-House): ".number_format($out_1);

	echo "<br>HJ ".$MM['S'][$this_mm][$i-1]." out-put (IN-House): ".number_format($act['m'.$MM['S'][$this_mm][$i-1]]- $so1[$MM['S'][$this_mm][$i-1]]);

	
	echo "<br>HJ THIS MONTH OUTPUT(IN-House) : ".number_format($act['m'.$this_mm]- $so1[$this_mm]);
	
	for($i=0; $i<sizeof($MONTHS); $i++) $out_3 += $act['m'.$MONTHS[$i]];	
	echo "<br>HJ TOTAL OUTPUT : ".number_format($out_3);

	echo	"<br>HJ SUB-CON OUTPUT :".number_format($so1[13]);
	*/
	
	$act = $capaci->get('LY', $this_yy,'actual');
	$so1 = $capaci->get_subcon('LY', $this_yy);	
	$out_1 = $out_2 = $out_3 = 0;
	for($i=0; $i<sizeof($MM['S'][$this_mm]); $i++) $out_1 += ($act['m'.$MM['S'][$this_mm][$i]]-$so1[$MM['S'][$this_mm][$i]]);

	$out_1 /= ($i);
	echo "<br><br>LY ".$MM['S'][$this_mm][0]."-".$MM['S'][$this_mm][$i-1]."AVG out-put (IN House): ".number_format($out_1);

	echo "<br><br>LY ".$MM['S'][$this_mm][$i-1]." out-put (IN House): ".number_format($act['m'.$MM['S'][$this_mm][$i-1]]-$so1[$MM['S'][$this_mm][$i-1]]);
	
	echo "<br>LY THIS MONTH OUTPUT (IN House): ".number_format($act['m'.$this_mm]-$so1[$this_mm]);
	
	for($i=0; $i<sizeof($MONTHS); $i++) $out_3 += $act['m'.$MONTHS[$i]];	
	echo "<br>LY TOTAL OUTPUT : ".number_format($out_3);

	echo	"<br>LY SUB-CON OUTPUT :".number_format($so1[13]);	
		
	$hj_un_g_out=$hj_un_m_out=$hj_un_out=$ly_un_out=0;
	for($i=0; $i<sizeof($mon_ary); $i++)
	{
		$yymm = ($this_yy+$year_ary[$i])."-".$mon_ary[$i];
/*
		$where_str = "AND s_order.line_sex = 'M'";
		$f1 = $order->get_one_etd_ord('HJ', $yymm,$where_str);	
		echo "<br><br>HJ ".$mon_ary[$i]."(ETD) MAN Q'TY : ".number_format($f1);

		$where_str = "AND s_order.line_sex = 'F'";
		$f1 = $order->get_one_etd_ord('HJ', $yymm,$where_str);	
		echo "<br><br>HJ ".$mon_ary[$i]."(ETD) Girl Q'TY : ".number_format($f1);
		
		$f1 = $order->get_one_etd_ord('HJ', $yymm);	
		echo "<br><br>HJ ".$mon_ary[$i]."(ETD) Q'TY : ".number_format($f1);
*/
		$yymm = ($this_yy+$year_ary[$i])."-".$mon_ary[$i];
		$f1 = $order->get_one_etd_ord('LY', $yymm);	
		echo "<br><br>LY ".$mon_ary[$i]."(ETD) Q'TY : ".number_format($f1);

/*
		$where_str = "AND s_order.line_sex = 'M'";
		$f1 = $order->get_one_etd_unout('HJ', $yymm,$where_str);	
		echo "<br>HJ ".$mon_ary[$i]."(ETD) UN-OUTPUT Man Q'TY : ".number_format($f1);
		if($mon_ary[$i] <= $this_mm) $hj_un_m_out+=$f1;
		
		$where_str = "AND s_order.line_sex = 'F'";
		$f1 = $order->get_one_etd_unout('HJ', $yymm,$where_str);	
		echo "<br>HJ ".$mon_ary[$i]."(ETD) UN-OUTPUT Girl Q'TY : ".number_format($f1);
		if($mon_ary[$i] <= $this_mm) $hj_un_g_out+=$f1;

		$f1 = $order->get_one_etd_unout('HJ', $yymm);	
		echo "<br>HJ ".$mon_ary[$i]."(ETD) UN-OUTPUT Q'TY : ".number_format($f1);
		if($mon_ary[$i] <= $this_mm) $hj_un_out+=$f1;
*/
		$f1 = $order->get_one_etd_unout('LY', $yymm);	
		echo "<br>LY ".$mon_ary[$i]."(ETD) UN-OUTPUT Q'TY : ".number_format($f1);
		if($mon_ary[$i] <= $this_mm) $ly_un_out+=$f1;
	}
//	echo "<br><br>HJ until this month MAN unout-put ".number_format($hj_un_m_out);	
//	echo "<br><br>HJ until this month Girl unout-put ".number_format($hj_un_g_out);	

//	echo "<br><br>HJ until this month unout-put ".number_format($hj_un_out);	

	echo "<br>LY until this month unout-put ".number_format($ly_un_out);	
	


	$yy=date('Y');
	$mm=date('m');
	$mm++;
	if ($mm == 13)
	{
		$mm=1;
		$yy++;
	}
	if ($mm < 10) $mm='0'.$mm;
	$parm['s_date']=$yy.'-'.$mm.'-01';
	$parm['e_date']=$yy.'-'.$mm.'-31';
	$parm['fty']='HJ';		

	$m1=$mm-2;
	if ($m1 <= 0) $m1=$m1+12;
	if ($m1 < 10 )$m1='0'.$m1;
	$m2=$mm-1;
	if ($m2 <= 0) $m2=$m2+12;
	if ($m2 < 10 )$m2='0'.$m2;

	$target=$yy.$mm;
	$targetary=array($target,$yy.$m1,$yy.$m2);	



/*
	$rptdo=$report->rep_sch_search($parm,'F');  //取出該筆副料記錄(自己生產)
	$rptdo_un_su=0;
	for ($i=0; $i<sizeof($rptdo); $i++)
	{
		$fty_su=explode(',',$rptdo[$i]['fty_su']);
		$rptdo[$i]['fty_su_a']=$rptdo[$i]['fty_su']=0;
		for ($j=0; $j< sizeof($fty_su); $j++)  //取得當月su
		{
			if ($target >= substr($fty_su[$j],0,6)) 
			{
				$tmp_fty_out=substr($fty_su[$j],6);
				for ($tg=0; $tg<3; $tg++)
				{
					if($targetary[$tg] == substr($fty_su[$j],0,6))$rptdo[$i]['fty_su']=$rptdo[$i]['fty_su']+$tmp_fty_out;
				}
				
				$rptdo[$i]['fty_su_a']=$rptdo[$i]['fty_su_a']+$tmp_fty_out;							
			}			
		}		
		//計算工廠未完成量			    
			$out_su=explode(',',$rptdo[$i]['out_su']);
			$rptdo[$i]['out_su']=0;
			for ($j=0; $j< sizeof($out_su); $j++)  //取得當月su
			{
				if ($target >= substr($out_su[$j],0,6)) 
				{
					$tmp_out_su=substr($out_su[$j],6);	
					$rptdo[$i]['out_su']=$rptdo[$i]['out_su']+$tmp_out_su;					
				}
			}
			$rptdo[$i]['un_su']=$rptdo[$i]['fty_su_a']-$rptdo[$i]['out_su'];
			if ( $rptdo[$i]['un_su'] <=0) $rptdo[$i]['un_su'] =0;		

		$rptdo_un_su=$rptdo_un_su+$rptdo[$i]['un_su'];
	}
	echo "<br><br>HJ Girl available production : ".number_format($rptdo_un_su);
	$tmp1 = $rptdo_un_su;
	
	
	
	$rptdo=$report->rep_sch_search($parm,'M');  //取出該筆副料記錄(自己生產)
	$rptdo_un_su=0;
	for ($i=0; $i<sizeof($rptdo); $i++)
	{
		$fty_su=explode(',',$rptdo[$i]['fty_su']);
		$rptdo[$i]['fty_su_a']=$rptdo[$i]['fty_su']=0;
		for ($j=0; $j< sizeof($fty_su); $j++)  //取得當月su
		{
			if ($target >= substr($fty_su[$j],0,6)) 
			{
				$tmp_fty_out=substr($fty_su[$j],6);
				for ($tg=0; $tg<3; $tg++)
				{
					if($targetary[$tg] == substr($fty_su[$j],0,6))$rptdo[$i]['fty_su']=$rptdo[$i]['fty_su']+$tmp_fty_out;
				}
				
				$rptdo[$i]['fty_su_a']=$rptdo[$i]['fty_su_a']+$tmp_fty_out;							
			}			
		}		
		//計算工廠未完成量			    
			$out_su=explode(',',$rptdo[$i]['out_su']);
			$rptdo[$i]['out_su']=0;
			for ($j=0; $j< sizeof($out_su); $j++)  //取得當月su
			{
				if ($target >= substr($out_su[$j],0,6)) 
				{
					$tmp_out_su=substr($out_su[$j],6);	
					$rptdo[$i]['out_su']=$rptdo[$i]['out_su']+$tmp_out_su;					
				}
			}
			$rptdo[$i]['un_su']=$rptdo[$i]['fty_su_a']-$rptdo[$i]['out_su'];
			if ( $rptdo[$i]['un_su'] <=0) $rptdo[$i]['un_su'] =0;		

		$rptdo_un_su=$rptdo_un_su+$rptdo[$i]['un_su'];
	}
	echo "<br>HJ MAN available production : ".number_format($rptdo_un_su);	
	echo "<br>HJ available production : ".number_format($rptdo_un_su+$tmp1);
	
	$rptun=$report->rep_unsch_search($parm,'F');
  $tmp=substr($rptun[0]['etd'],0,7);
  $rptun_tsu_tal=0;    
	for ($i=0; $i<sizeof($rptun); $i++)
	{
				$rptun_tsu_tal=$rptun_tsu_tal+$rptun[$i]['su'];	
  }
  echo "<br>HJ un-available Girl production : ".number_format($rptun_tsu_tal);
	$tmp1= $rptun_tsu_tal;

	$rptun=$report->rep_unsch_search($parm,'M');
  $tmp=substr($rptun[0]['etd'],0,7);
  $rptun_tsu_tal=0;    
	for ($i=0; $i<sizeof($rptun); $i++)
	{
				$rptun_tsu_tal=$rptun_tsu_tal+$rptun[$i]['su'];	
  }
  echo "<br>HJ un-available Man production : ".number_format($rptun_tsu_tal);
	echo "<br>HJ un-available production : ".number_format($rptun_tsu_tal+$tmp1);
	

	$rpt_una=$report->rep_unasch_search($parm,'F');
	$rptnt=$report->rep_nosch_search($parm,'F'); 
  $rpt_una_su = 0;
	for ($i=0; $i<sizeof($rpt_una); $i++)$rpt_una_su=$rpt_una_su+$rpt_una[$i]['su'];
  $rptnt_su = 0;
	for ($i=0; $i<sizeof($rptnt); $i++) $rptnt_su=$rptnt_su+$rptnt[$i]['su'];
	echo "<br>HJ un-Schedule Girl production : ".number_format($rpt_una_su+$rptnt_su);
	$tmp1=$rpt_una_su+$rptnt_su;

	$rpt_una=$report->rep_unasch_search($parm,'M');
	$rptnt=$report->rep_nosch_search($parm,'M'); 
  $tmp=substr($rpt_una[0]['etd'],0,7);
  $rpt_una_su = 0;
	for ($i=0; $i<sizeof($rpt_una); $i++)$rpt_una_su=$rpt_una_su+$rpt_una[$i]['su'];
  $rptnt_su = 0;
	for ($i=0; $i<sizeof($rptnt); $i++) $rptnt_su=$rptnt_su+$rptnt[$i]['su'];
	echo "<br>HJ un-Schedule Man production : ".number_format($rpt_una_su+$rptnt_su);
	echo "<br>HJ un-Schedule production : ".number_format($rpt_una_su+$rptnt_su+$tmp1);
*/






	
	
	
	$parm['fty']='LY';
	$rptdo=$report->rep_sch_search($parm);  //取出該筆副料記錄(自己生產)
	$rptdo_un_su=0;
	for ($i=0; $i<sizeof($rptdo); $i++)
	{
		$fty_su=explode(',',$rptdo[$i]['fty_su']);
		$rptdo[$i]['fty_su_a']=$rptdo[$i]['fty_su']=0;
		for ($j=0; $j< sizeof($fty_su); $j++)  //取得當月su
		{
			if ($target >= substr($fty_su[$j],0,6)) 
			{
				$tmp_fty_out=substr($fty_su[$j],6);
				for ($tg=0; $tg<3; $tg++)
				{
					if($targetary[$tg] == substr($fty_su[$j],0,6))$rptdo[$i]['fty_su']=$rptdo[$i]['fty_su']+$tmp_fty_out;
				}
				
				$rptdo[$i]['fty_su_a']=$rptdo[$i]['fty_su_a']+$tmp_fty_out;							
			}			
		}		
		//計算工廠未完成量			    
			$out_su=explode(',',$rptdo[$i]['out_su']);
			$rptdo[$i]['out_su']=0;
			for ($j=0; $j< sizeof($out_su); $j++)  //取得當月su
			{
				if ($target >= substr($out_su[$j],0,6)) 
				{
					$tmp_out_su=substr($out_su[$j],6);	
					$rptdo[$i]['out_su']=$rptdo[$i]['out_su']+$tmp_out_su;					
				}
			}
			$rptdo[$i]['un_su']=$rptdo[$i]['fty_su_a']-$rptdo[$i]['out_su'];
			if ( $rptdo[$i]['un_su'] <=0) $rptdo[$i]['un_su'] =0;		

		$rptdo_un_su=$rptdo_un_su+$rptdo[$i]['un_su'];
	}
	echo "<br><br>LY available production : ".number_format($rptdo_un_su)	;		
	
	
	$rptun=$report->rep_unsch_search($parm);

  $tmp=substr($rptun[0]['etd'],0,7);
  $rptun_tsu_tal=0;    
	for ($i=0; $i<sizeof($rptun); $i++)
	{
				$rptun_tsu_tal=$rptun_tsu_tal+$rptun[$i]['su'];	
  }
  echo "<br>LY un-available production : ".number_format($rptun_tsu_tal);	
	
	$rpt_una=$report->rep_unasch_search($parm);
	$rptnt=$report->rep_nosch_search($parm); 
  $tmp=substr($rpt_una[0]['etd'],0,7);
  $rpt_una_su = 0;
	for ($i=0; $i<sizeof($rpt_una); $i++)$rpt_una_su=$rpt_una_su+$rpt_una[$i]['su'];
  $rptnt_su = 0;
	for ($i=0; $i<sizeof($rptnt); $i++) $rptnt_su=$rptnt_su+$rptnt[$i]['su'];
	echo "<br>LY un-Schedule production : ".number_format($rpt_una_su+$rptnt_su);
	
	
	break;




//====================================================
case "rep_smplfty_list":
check_authority('072',"view");

		$factory = $_POST['PHP_sample'];
		$dept = $_POST['PHP_dept'];
		$DateStart = $_POST['PHP_start'];
		$DateEnd = $_POST['PHP_finish'];
		$Cust = $_POST['PHP_cust'];
		

	$rpt_cl=$report->smpl_fty_searchNew($DateStart,$DateEnd,$factory,$dept,$Cust,10);

		$qty = $num = $done  = $rem = $FactorySum = $MonthSum = 0;
		$Top_qty = $BTM_qty = $Top_done =$BTM_done = $Top_rem = $BTM_rem = 0;
		$Top_Month_qty = $BTM_Month_qty = $Top_Month_done =$BTM_Month_done = $Top_Month_rem = $BTM_Month_rem = 0;
		$Top_Total_qty = $BTM_Total_qty = $Top_Total_done =$BTM_Total_done = $Top_Total_rem = $BTM_Total_rem = 0;
		
		$tmp_mm = $tmp_fty = '';
		$rpt_cl_new = array();
		$rpt_full_sum=array();
		$rpt_SmplNoClose=array();
		
		$op['dateStart']=$DateStart;
		$op['dateEnd']=$DateEnd;
		$op['fty']=$factory;
		$op['dept']=$dept;
		
		/*****明細及工廠加總*****/
		for($i=0; $i<sizeof($rpt_cl); $i++)
		{
			/*這個迴圈好像把同月份的東西加總，至於加哪些東西我也不知道*/
			$tmp = explode('-',$rpt_cl[$i]['etd']);//把原本etd裡面的值拆分的三個陣列，已"-"為拆分字符
			
			$mm = substr($tmp[0],2).'-'.$tmp[1];//將第一個陣列的第三個值直到最後一個值取出，並與第二個陣列結合,例2012-01-20 => 12-01

			if($tmp_mm == '') $tmp_mm = $mm;
			if($tmp_fty == '' ) 
			{
				$tmp_fty = $rpt_cl[$i]['factory'];
				$rpt_cl_new[$num]['check']='Header';
			}

			/*判斷工廠是否已經轉換*/
			if($tmp_fty != '' && $tmp_fty != $rpt_cl[$i]['factory'] )
			{
				/*不同工廠*/
				/*************切換加總**************/
				$rpt_cl_new[$num]['fty']=$tmp_fty;
				$rpt_cl_new[$num]['YearMonth']=$tmp_mm;
				$rpt_cl_new[$num]['s_qty'] = $qty;
				$rpt_cl_new[$num]['s_rem'] = $rem;
				$rpt_cl_new[$num]['s_done'] = $done;
				$rpt_cl_new[$num]['s_num'] = $num;
				$rpt_cl_new[$num]['s_mm'] = $tmp_mm;
				$rpt_cl_new[$num]['tb_mk'] = 1;
				$rpt_cl_new[$num]['last']='YES';
				$rpt_cl_new[$num]['check']='Footer';
				$rpt_cl_new[$num]['monthsum']=$MonthSum;
				$rpt_cl_new[$num]['factorysum']=$FactorySum;
				$rpt_cl_new[$num]['factoryqty']=$FactoryQty;
				$rpt_cl_new[$num]['factoryrem']=$FactoryRem;
				$rpt_cl_new[$num]['factorydone']=$FactoryDone;	
				$rpt_cl_new[$num]['Top_qty']=$Top_qty;
				$rpt_cl_new[$num]['Top_done']=$Top_done;
				$rpt_cl_new[$num]['Top_rem']=$Top_rem;
				$rpt_cl_new[$num]['BTM_qty']=$BTM_qty;
				$rpt_cl_new[$num]['BTM_done']=$BTM_done;
				$rpt_cl_new[$num]['BTM_rem']=$BTM_rem;
				/* $rpt_cl_new[$num]['Top_Month_qty']=$Top_Month_qty;
				$rpt_cl_new[$num]['Top_Month_done']=$Top_Month_done;
				$rpt_cl_new[$num]['Top_Month_rem']=$Top_Month_rem;
				$rpt_cl_new[$num]['BTM_Month_qty']=$BTM_Month_qty;
				$rpt_cl_new[$num]['BTM_Month_done']=$BTM_Month_done;
				$rpt_cl_new[$num]['BTM_Month_rem']=$BTM_Month_rem;*/
				$rpt_cl_new[$num]['Top_Total_qty']=$Top_Total_qty;
				$rpt_cl_new[$num]['Top_Total_done']=$Top_Total_done;
				$rpt_cl_new[$num]['Top_Total_rem']=$Top_Total_rem;
				$rpt_cl_new[$num]['BTM_Total_qty']=$BTM_Total_qty;
				$rpt_cl_new[$num]['BTM_Total_done']=$BTM_Total_done;
				$rpt_cl_new[$num]['BTM_Total_rem']=$BTM_Total_rem;
				$MonthSum=$FactorySum=0;
				$num++;
				/***********切換加總(結束)************/
				
				$tmp_mm = $mm;
				$FactoryRem=$FactoryDone=$FactoryQty=0;
				$qty = $done  = $rem = 0;
				$Top_qty = $BTM_qty = $Top_done =$BTM_done = $Top_rem = $BTM_rem = 0;
				/* $Top_Month_qty = $BTM_Month_qty = $Top_Month_done =$BTM_Month_done = $Top_Month_rem = $BTM_Month_rem = 0; */
				$Top_Total_qty = $BTM_Total_qty = $Top_Total_done =$BTM_Total_done = $Top_Total_rem = $BTM_Total_rem = 0;
				
				$tmp_fty = $rpt_cl[$i]['factory'];
				$rpt_cl_new[$num]['rem'] = 0;
				if($rpt_cl[$i]['qty'] > $rpt_cl[$i]['qty_done']) 
				{
					$rpt_cl_new[$num]['rem'] = $rpt_cl[$i]['qty'] - $rpt_cl[$i]['qty_done'];
				}
				$rpt_cl_new[$num]['id']=$rpt_cl[$i]['id'];
				$rpt_cl_new[$num]['order']=$rpt_cl[$i]['num'];
				$rpt_cl_new[$num]['qty']=$rpt_cl[$i]['qty'];
				$rpt_cl_new[$num]['qty_down']=$rpt_cl[$i]['qty_done'];
				$rpt_cl_new[$num]['num']=$rpt_cl[$i]['num'];
				$rpt_cl_new[$num]['smpl_type']=$rpt_cl[$i]['smpl_type'];
				$rpt_cl_new[$num]['style']=$rpt_cl[$i]['style'];
				$rpt_cl_new[$num]['cust']=$rpt_cl[$i]['cust'];
				$rpt_cl_new[$num]['etd']=$rpt_cl[$i]['etd'];
				$rpt_cl_new[$num]['schd_smpl']=$rpt_cl[$i]['schd_smpl'];
				$rpt_cl_new[$num]['status']=$rpt_cl[$i]['status'];
				$rpt_cl_new[$num]['ref']=$rpt_cl[$i]['ref'];
				$rpt_cl_new[$num]['fty']=$tmp_fty;
				$rpt_cl_new[$num]['YearMonth']=$tmp_mm;
				$rpt_cl_new[$num]['last']='NO';
				$rpt_cl_new[$num]['check']='Header';
				$rpt_cl_new[$num]['p_etd']=$rpt_cl[$i]['schd_pttn'];
				$rpt_cl_new[$num]['s_etd']=$rpt_cl[$i]['schd_smpl'];
				$rpt_cl_new[$num]['ptn_down']=$rpt_cl[$i]['done_pttn'];
				$rpt_cl_new[$num]['sample_down']=$rpt_cl[$i]['done_smpl'];
				if($rpt_cl[$i]['style']=='JK' or $rpt_cl[$i]['style']=='BS' or $rpt_cl[$i]['style']=='BZ' or $rpt_cl[$i]['style']=='DR' or $rpt_cl[$i]['style']=='TP' or $rpt_cl[$i]['style']=='VS')
				{
					$Top_qty+=$rpt_cl[$i]['qty'];
					$Top_done+=$rpt_cl[$i]['qty_done'];
					$Top_rem+=$rpt_cl[$i]['rem'];
					/* $Top_Month_qty+=$rpt_cl[$i]['qty'];
					$Top_Month_done+=$rpt_cl[$i]['qty_done'];
					$Top_Month_rem+=$rpt_cl[$i]['rem'];*/
					$Top_Total_qty+=$rpt_cl[$i]['qty'];
					$Top_Total_done+=$rpt_cl[$i]['qty_done'];
					$Top_Total_rem+=$rpt_cl[$i]['rem'];
				}
				if($rpt_cl[$i]['style']=='PT' or $rpt_cl[$i]['style']=='SH' or $rpt_cl[$i]['style']=='SK' or $rpt_cl[$i]['style']=='SO')
				{
					$BTM_qty+=$rpt_cl[$i]['qty'];
					$BTM_done+=$rpt_cl[$i]['qty_done'];
					$BTM_rem+=$rpt_cl[$i]['rem'];
					/* $BTM_Month_qty+=$rpt_cl[$i]['qty'];
					$BTM_Month_done+=$rpt_cl[$i]['qty_done'];
					$BTM_Month_rem+=$rpt_cl[$i]['rem'];*/
					$BTM_Total_qty+=$rpt_cl[$i]['qty'];
					$BTM_Total_done+=$rpt_cl[$i]['qty_done'];
					$BTM_Total_rem+=$rpt_cl[$i]['rem'];
				}
					
				$rem += $rpt_cl_new[$num]['rem'];
				$done += $rpt_cl[$i]['qty_done'];
				$qty += $rpt_cl[$i]['qty'];
				$FactorySum++;
				$MonthSum++;
				$FactoryRem+=$rpt_cl_new[$num]['rem'];
				$FactoryDone+=$rpt_cl[$i]['qty_done'];
				$FactoryQty+=$rpt_cl[$i]['qty'];
				
				$num++;
			}
			else
			{
				/*相同工廠*/
				/*判斷月份是否已經轉換*/
				if($tmp_mm != '' && $tmp_mm != $mm )
				{
					/*不同月份*/
					/*************切換加總**************/
					$rpt_cl_new[$num]['fty']=$tmp_fty;
					$rpt_cl_new[$num]['YearMonth']=$tmp_mm;
					$rpt_cl_new[$num]['s_qty'] = $qty;
					$rpt_cl_new[$num]['s_rem'] = $rem;
					$rpt_cl_new[$num]['s_done'] = $done;
					$rpt_cl_new[$num]['s_num'] = $num;
					$rpt_cl_new[$num]['s_mm'] = $tmp_mm;
					$rpt_cl_new[$num]['tb_mk'] = 1;
					$rpt_cl_new[$num]['last']='YES';
					$rpt_cl_new[$num]['check']='';
					$rpt_cl_new[$num]['monthsum']=$MonthSum;
					$rpt_cl_new[$num]['factorysum']=$FactorySum;
					$rpt_cl_new[$num]['Top_qty']=$Top_qty;
					$rpt_cl_new[$num]['Top_done']=$Top_done;
					$rpt_cl_new[$num]['Top_rem']=$Top_rem;
					$rpt_cl_new[$num]['BTM_qty']=$BTM_qty;
					$rpt_cl_new[$num]['BTM_done']=$BTM_done;
					$rpt_cl_new[$num]['BTM_rem']=$BTM_rem;
					/* $rpt_cl_new[$num]['Top_Month_qty']=$Top_Month_qty;
					$rpt_cl_new[$num]['Top_Month_done']=$Top_Month_done;
					$rpt_cl_new[$num]['Top_Month_rem']=$Top_Month_rem ;
					$rpt_cl_new[$num]['BTM_Month_qty']=$BTM_Month_qty;
					$rpt_cl_new[$num]['BTM_Month_done']=$BTM_Month_done;
					$rpt_cl_new[$num]['BTM_Month_rem']=$BTM_Month_rem; */
					
					$MonthSum=0;
					$num++;
					/***********切換加總(結束)************/
					
					$tmp_mm = $mm;
					$qty = $done  = $rem = 0;
					$Top_qty = $BTM_qty = $Top_done =$BTM_done = $Top_rem = $BTM_rem = 0;
					/* $Top_Month_qty = $BTM_Month_qty = $Top_Month_done =$BTM_Month_done = $Top_Month_rem = $BTM_Month_rem = 0; */
					
					$rpt_cl_new[$num]['rem'] = 0;
					if($rpt_cl[$i]['qty'] > $rpt_cl[$i]['qty_done']) 
					{
						$rpt_cl_new[$num]['rem'] = $rpt_cl[$i]['qty'] - $rpt_cl[$i]['qty_done'];
					}
					$rpt_cl_new[$num]['id']=$rpt_cl[$i]['id'];
					$rpt_cl_new[$num]['order']=$rpt_cl[$i]['num'];
					$rpt_cl_new[$num]['qty']=$rpt_cl[$i]['qty'];
					$rpt_cl_new[$num]['qty_down']=$rpt_cl[$i]['qty_done'];
					$rpt_cl_new[$num]['smpl_type']=$rpt_cl[$i]['smpl_type'];
					$rpt_cl_new[$num]['style']=$rpt_cl[$i]['style'];
					$rpt_cl_new[$num]['cust']=$rpt_cl[$i]['cust'];
					$rpt_cl_new[$num]['etd']=$rpt_cl[$i]['etd'];
					$rpt_cl_new[$num]['schd_smpl']=$rpt_cl[$i]['schd_smpl'];
					$rpt_cl_new[$num]['status']=$rpt_cl[$i]['status'];
					$rpt_cl_new[$num]['ref']=$rpt_cl[$i]['ref'];
					$rpt_cl_new[$num]['fty']=$tmp_fty;
					$rpt_cl_new[$num]['YearMonth']=$tmp_mm;
					$rpt_cl_new[$num]['last']='NO';
					$rpt_cl_new[$num]['check']='';
					$rpt_cl_new[$num]['p_etd']=$rpt_cl[$i]['schd_pttn'];
					$rpt_cl_new[$num]['s_etd']=$rpt_cl[$i]['schd_smpl'];
					$rpt_cl_new[$num]['ptn_down']=$rpt_cl[$i]['done_pttn'];
					$rpt_cl_new[$num]['sample_down']=$rpt_cl[$i]['done_smpl'];
					if($rpt_cl[$i]['style']=='JK' or $rpt_cl[$i]['style']=='BS' or $rpt_cl[$i]['style']=='BZ' or $rpt_cl[$i]['style']=='DR' or $rpt_cl[$i]['style']=='TP' or $rpt_cl[$i]['style']=='VS')
					{
						$Top_qty+=$rpt_cl[$i]['qty'];
						$Top_done+=$rpt_cl[$i]['qty_done'];
						$Top_rem+=$rpt_cl[$i]['rem'];
						/* $Top_Month_qty+=$rpt_cl[$i]['qty'];
						$Top_Month_done+=$rpt_cl[$i]['qty_done'];
						$Top_Month_rem+=$rpt_cl[$i]['rem'];*/
						$Top_Total_qty+=$rpt_cl[$i]['qty'];
						$Top_Total_done+=$rpt_cl[$i]['qty_done'];
						$Top_Total_rem+=$rpt_cl[$i]['rem'];
					}
					if($rpt_cl[$i]['style']=='PT' or $rpt_cl[$i]['style']=='SH' or $rpt_cl[$i]['style']=='SK' or $rpt_cl[$i]['style']=='SO')
					{
						$BTM_qty+=$rpt_cl[$i]['qty'];
						$BTM_done+=$rpt_cl[$i]['qty_done'];
						$BTM_rem+=$rpt_cl[$i]['rem'];
						/* $BTM_Month_qty+=$rpt_cl[$i]['qty'];
						$BTM_Month_done+=$rpt_cl[$i]['qty_done'];
						$BTM_Month_rem+=$rpt_cl[$i]['rem']; */
						$BTM_Total_qty+=$rpt_cl[$i]['qty'];
						$BTM_Total_done+=$rpt_cl[$i]['qty_done'];
						$BTM_Total_rem+=$rpt_cl[$i]['rem'];
					}
					
					$rem += $rpt_cl_new[$num]['rem'];
					$done += $rpt_cl[$i]['qty_done'];
					$qty += $rpt_cl[$i]['qty'];
					$FactorySum++;
					$MonthSum++;
					$FactoryRem+=$rpt_cl_new[$num]['rem'];
					$FactoryDone+=$rpt_cl[$i]['qty_done'];
					$FactoryQty+=$rpt_cl[$i]['qty'];
					
					$num++;
					
				} 
				else
				{
					/*相同月份*/
					
					$rpt_cl_new[$num]['rem'] = 0;
					if($rpt_cl[$i]['qty'] > $rpt_cl[$i]['qty_done']) 
					{
						$rpt_cl_new[$num]['rem'] = $rpt_cl[$i]['qty'] - $rpt_cl[$i]['qty_done'];
					}
					if($num==0)
					{
						$rpt_cl_new[$num]['check']='Header';
					}
					else
					{
						$rpt_cl_new[$num]['check']='';
					}
					$rpt_cl_new[$num]['id']=$rpt_cl[$i]['id'];
					$rpt_cl_new[$num]['order']=$rpt_cl[$i]['num'];
					$rpt_cl_new[$num]['qty']=$rpt_cl[$i]['qty'];
					$rpt_cl_new[$num]['qty_down']=$rpt_cl[$i]['qty_done'];
					$rpt_cl_new[$num]['smpl_type']=$rpt_cl[$i]['smpl_type'];
					$rpt_cl_new[$num]['style']=$rpt_cl[$i]['style'];
					$rpt_cl_new[$num]['cust']=$rpt_cl[$i]['cust'];
					$rpt_cl_new[$num]['etd']=$rpt_cl[$i]['etd'];
					$rpt_cl_new[$num]['schd_smpl']=$rpt_cl[$i]['schd_smpl'];
					$rpt_cl_new[$num]['status']=$rpt_cl[$i]['status'];
					$rpt_cl_new[$num]['ref']=$rpt_cl[$i]['ref'];
					$rpt_cl_new[$num]['fty']=$tmp_fty;
					$rpt_cl_new[$num]['YearMonth']=$tmp_mm;
					$rpt_cl_new[$num]['last']='NO';
					$rpt_cl_new[$num]['p_etd']=$rpt_cl[$i]['schd_pttn'];
					$rpt_cl_new[$num]['s_etd']=$rpt_cl[$i]['schd_smpl'];
					$rpt_cl_new[$num]['ptn_down']=$rpt_cl[$i]['done_pttn'];
					$rpt_cl_new[$num]['sample_down']=$rpt_cl[$i]['done_smpl'];
					if($rpt_cl[$i]['style']=='JK' or $rpt_cl[$i]['style']=='BS' or $rpt_cl[$i]['style']=='BZ' or $rpt_cl[$i]['style']=='DR' or $rpt_cl[$i]['style']=='TP' or $rpt_cl[$i]['style']=='VS')
					{
						$Top_qty+=$rpt_cl[$i]['qty'];
						$Top_done+=$rpt_cl[$i]['qty_done'];
						$Top_rem+=$rpt_cl[$i]['rem'];
						/* $Top_Month_qty+=$rpt_cl[$i]['qty'];
						$Top_Month_done+=$rpt_cl[$i]['qty_done'];
						$Top_Month_rem+=$rpt_cl[$i]['rem'];*/
						$Top_Total_qty+=$rpt_cl[$i]['qty'];
						$Top_Total_done+=$rpt_cl[$i]['qty_done'];
						$Top_Total_rem+=$rpt_cl[$i]['rem'];
					}
					if($rpt_cl[$i]['style']=='PT' or $rpt_cl[$i]['style']=='SH' or $rpt_cl[$i]['style']=='SK' or $rpt_cl[$i]['style']=='SO')
					{
						$BTM_qty+=$rpt_cl[$i]['qty'];
						$BTM_done+=$rpt_cl[$i]['qty_done'];
						$BTM_rem+=$rpt_cl[$i]['rem'];
						/* $BTM_Month_qty+=$rpt_cl[$i]['qty'];
						$BTM_Month_done+=$rpt_cl[$i]['qty_done'];
						$BTM_Month_rem+=$rpt_cl[$i]['rem'];*/
						$BTM_Total_qty+=$rpt_cl[$i]['qty'];
						$BTM_Total_done+=$rpt_cl[$i]['qty_done'];
						$BTM_Total_rem+=$rpt_cl[$i]['rem'];
					}
					
					$rem += $rpt_cl_new[$num]['rem'];
					$done += $rpt_cl[$i]['qty_done'];
					$qty += $rpt_cl[$i]['qty'];
					$FactorySum++;
					$MonthSum++;
					$FactoryRem+=$rpt_cl_new[$num]['rem'];
					$FactoryDone+=$rpt_cl[$i]['qty_done'];
					$FactoryQty+=$rpt_cl[$i]['qty'];

					$num++;
				}
			}
		}
		/*******切換加總(最後一筆處理)********/
		$rpt_cl_new[$num]['fty']=$tmp_fty;
		$rpt_cl_new[$num]['YearMonth']=$tmp_mm;
		$rpt_cl_new[$num]['s_qty'] = $qty;
		$rpt_cl_new[$num]['s_rem'] = $rem;
		$rpt_cl_new[$num]['s_done'] = $done;
		$rpt_cl_new[$num]['s_num'] = $num;
		$rpt_cl_new[$num]['s_mm'] = $tmp_mm;
		$rpt_cl_new[$num]['tb_mk'] = 1;
		$rpt_cl_new[$num]['last']='YES';
		$rpt_cl_new[$num]['check']='Footer';
		$rpt_cl_new[$num]['monthsum']=$MonthSum;
		$rpt_cl_new[$num]['factorysum']=$FactorySum;
		$rpt_cl_new[$num]['factoryqty']=$FactoryQty;
		$rpt_cl_new[$num]['factoryrem']=$FactoryRem;
		$rpt_cl_new[$num]['factorydone']=$FactoryDone;
		
		$rpt_cl_new[$num]['Top_qty']=$Top_qty;
		$rpt_cl_new[$num]['Top_done']=$Top_done;
		$rpt_cl_new[$num]['Top_rem']=$Top_rem;
		$rpt_cl_new[$num]['BTM_qty']=$BTM_qty;
		$rpt_cl_new[$num]['BTM_done']=$BTM_done;
		$rpt_cl_new[$num]['BTM_rem']=$BTM_rem;
		/* $rpt_cl_new[$num]['Top_Month_qty']=$Top_Month_qty;
		$rpt_cl_new[$num]['Top_Month_done']=$Top_Month_done;
		rpt_cl_new[$num]['Top_Month_rem']=$Top_Month_rem;
		$rpt_cl_new[$num]['BTM_Month_qty']=$BTM_Month_qty;
		$rpt_cl_new[$num]['BTM_Month_done']=$BTM_Month_done;
		$rpt_cl_new[$num]['BTM_Month_rem']=$BTM_Month_rem;*/
		$rpt_cl_new[$num]['Top_Total_qty']=$Top_Total_qty;
		$rpt_cl_new[$num]['Top_Total_done']=$Top_Total_done;
		$rpt_cl_new[$num]['Top_Total_rem']=$Top_Total_rem;
		$rpt_cl_new[$num]['BTM_Total_qty']=$BTM_Total_qty;
		$rpt_cl_new[$num]['BTM_Total_done']=$BTM_Total_done;
		$rpt_cl_new[$num]['BTM_Total_rem']=$BTM_Total_rem;
		/***********切換加總(結束)************/
		
		/*****明細及工廠加總(結束)*****/
	
		/*****月份統計及工廠加總*****/
		$indexNum=0;
		$YearMonthCheck="";
		$qtyMonthSum=$doneMonthSum=0;
		$qtySum=$doneSum=0;
		$orderSum=$factoryOrderSum=0;
		$TopSum=$BTMSum=$TopDownSum=$BTMDownSum=0;
		$TopFullSum=$TopDownFullSum=$BTMFullSum=$BTMDownFullSum=0;
		$isFooter=false;
		for($j=0; $j<sizeof($rpt_cl_new); $j++)
		{
			if($rpt_cl_new[$j]['last']=='YES')
			{
				if($YearMonthCheck=='')
				{
					$isFooter=true;
					$rpt_full_sum[$indexNum]['check']="Start";
					$YearMonthCheck=$rpt_cl_new[$j]['YearMonth'];
				}
				if($rpt_cl_new[$j]['check']=='Footer')
				{
					//此工廠最後一筆
					$rpt_full_sum[$indexNum]['fty']=$rpt_cl_new[$j]['fty'];
					$YearMonthCheck=$rpt_cl_new[$j]['YearMonth'];
					$rpt_full_sum[$indexNum]['YearMonth']=$YearMonthCheck;
					$rpt_full_sum[$indexNum]['FTY_First']='NO';
					$rpt_full_sum[$indexNum]['FTY_Finish']='YES';
					$rpt_full_sum[$indexNum]['Top_qty']=$rpt_cl_new[$j]['Top_qty'];
					$rpt_full_sum[$indexNum]['Top_done']=$rpt_cl_new[$j]['Top_done'];
					$rpt_full_sum[$indexNum]['BTM_qty']=$rpt_cl_new[$j]['BTM_qty'];
					$rpt_full_sum[$indexNum]['BTM_done']=$rpt_cl_new[$j]['BTM_done'];
					$rpt_full_sum[$indexNum]['qty_sum']=$rpt_cl_new[$j]['s_qty'];
					$rpt_full_sum[$indexNum]['done_sum']=$rpt_cl_new[$j]['s_done'];
					$rpt_full_sum[$indexNum]['monthsum']=$rpt_cl_new[$j]['monthsum'];
					$rpt_full_sum[$indexNum]['debug']="3";
					$factoryOrderSum+=$rpt_cl_new[$j]['monthsum'];
					$rpt_full_sum[$indexNum]['FTY_Sum']=$factoryOrderSum;
					$TopSum+=$rpt_cl_new[$j]['Top_qty'];
					$rpt_full_sum[$indexNum]['Top_Sum']=$TopSum;
					$BTMSum+=$rpt_cl_new[$j]['BTM_qty'];
					$rpt_full_sum[$indexNum]['BTM_Sum']=$BTMSum;
					$TopDownSum+=$rpt_cl_new[$j]['Top_done'];
					$rpt_full_sum[$indexNum]['Top_Down_Sum']=$TopDownSum;
					$BTMDownSum+=$rpt_cl_new[$j]['BTM_done'];
					$rpt_full_sum[$indexNum]['BTM_Down_Sum']=$BTMDownSum;
					$isFooter=true;
					$factoryOrderSum=$TopSum=$BTMSum=$TopDownSum=$BTMDownSum=0;
					
					
					
					
					
					$orderSum+=$rpt_cl_new[$j]['monthsum'];
					$TopFullSum+=$rpt_cl_new[$j]['Top_qty'];
					$TopDownFullSum+=$rpt_cl_new[$j]['Top_done'];
					$BTMFullSum+=$rpt_cl_new[$j]['BTM_qty'];
					$BTMDownFullSum+=$rpt_cl_new[$j]['BTM_done'];
				}
				else
				{
					
					//非此工廠的最後一筆
					if($isFooter)
					{
						//此工廠的第一筆開始
						$rpt_full_sum[$indexNum]['fty']=$rpt_cl_new[$j]['fty'];
						$YearMonthCheck=$rpt_cl_new[$j]['YearMonth'];
						$rpt_full_sum[$indexNum]['YearMonth']=$YearMonthCheck;
						$rpt_full_sum[$indexNum]['FTY_First']='YES';
						$rpt_full_sum[$indexNum]['FTY_Finish']='NO';
						$rpt_full_sum[$indexNum]['Top_qty']=$rpt_cl_new[$j]['Top_qty'];
						$rpt_full_sum[$indexNum]['Top_done']=$rpt_cl_new[$j]['Top_done'];
						$rpt_full_sum[$indexNum]['BTM_qty']=$rpt_cl_new[$j]['BTM_qty'];
						$rpt_full_sum[$indexNum]['BTM_done']=$rpt_cl_new[$j]['BTM_done'];
						$rpt_full_sum[$indexNum]['qty_sum']=$rpt_cl_new[$j]['s_qty'];
						$rpt_full_sum[$indexNum]['done_sum']=$rpt_cl_new[$j]['s_done'];
						$rpt_full_sum[$indexNum]['monthsum']=$rpt_cl_new[$j]['monthsum'];
						$rpt_full_sum[$indexNum]['debug']="1";
						$factoryOrderSum+=$rpt_cl_new[$j]['monthsum'];
						$TopSum+=$rpt_cl_new[$j]['Top_qty'];
						$BTMSum+=$rpt_cl_new[$j]['BTM_qty'];
						$TopDownSum+=$rpt_cl_new[$j]['Top_done'];
						$BTMDownSum+=$rpt_cl_new[$j]['BTM_done'];
						
						$isFooter=false;
						$orderSum+=$rpt_cl_new[$j]['monthsum'];
						$TopFullSum+=$rpt_cl_new[$j]['Top_qty'];
						$TopDownFullSum+=$rpt_cl_new[$j]['Top_done'];
						$BTMFullSum+=$rpt_cl_new[$j]['BTM_qty'];
						$BTMDownFullSum+=$rpt_cl_new[$j]['BTM_done'];
					}
					else
					{
						//非此工廠的第一筆
						$rpt_full_sum[$indexNum]['fty']=$rpt_cl_new[$j]['fty'];
						$YearMonthCheck=$rpt_cl_new[$j]['YearMonth'];
						$rpt_full_sum[$indexNum]['YearMonth']=$YearMonthCheck;
						$rpt_full_sum[$indexNum]['FTY_First']='NO';
						$rpt_full_sum[$indexNum]['FTY_Finish']='NO';
						$rpt_full_sum[$indexNum]['Top_qty']=$rpt_cl_new[$j]['Top_qty'];
						$rpt_full_sum[$indexNum]['Top_done']=$rpt_cl_new[$j]['Top_done'];
						$rpt_full_sum[$indexNum]['BTM_qty']=$rpt_cl_new[$j]['BTM_qty'];
						$rpt_full_sum[$indexNum]['BTM_done']=$rpt_cl_new[$j]['BTM_done'];
						$rpt_full_sum[$indexNum]['qty_sum']=$rpt_cl_new[$j]['s_qty'];
						$rpt_full_sum[$indexNum]['done_sum']=$rpt_cl_new[$j]['s_done'];
						$rpt_full_sum[$indexNum]['monthsum']=$rpt_cl_new[$j]['monthsum'];
						$rpt_full_sum[$indexNum]['debug']="2";
						$factoryOrderSum+=$rpt_cl_new[$j]['monthsum'];
						$TopSum+=$rpt_cl_new[$j]['Top_qty'];
						$BTMSum+=$rpt_cl_new[$j]['BTM_qty'];
						$TopDownSum+=$rpt_cl_new[$j]['Top_done'];
						$BTMDownSum+=$rpt_cl_new[$j]['BTM_done'];
						
						$orderSum+=$rpt_cl_new[$j]['monthsum'];
						$TopFullSum+=$rpt_cl_new[$j]['Top_qty'];
						$TopDownFullSum+=$rpt_cl_new[$j]['Top_done'];
						$BTMFullSum+=$rpt_cl_new[$j]['BTM_qty'];
						$BTMDownFullSum+=$rpt_cl_new[$j]['BTM_done'];
					}
					
					
				}
				
				$indexNum++;
			}
			
			/* $rpt_full_sum[$indexNum]['YearMonth'] */
			
		}
		/* print_r($rpt_cl_new);
		exit(); */
		$rpt_full_sum[$indexNum]['check']="End";
		$rpt_full_sum[$indexNum]['Top_Full']=$TopFullSum;
		$rpt_full_sum[$indexNum]['TopDown_Full']=$TopDownFullSum;
		$rpt_full_sum[$indexNum]['BTM_Full']=$BTMFullSum;
		$rpt_full_sum[$indexNum]['BTMDown_Full']=$BTMDownFullSum;
		$rpt_full_sum[$indexNum]['MonthSum_Full']=$orderSum;
		
		$TopFullSum=$TopDownFullSum=$BTMFullSum=$BTMDownFullSum=$orderSum=0;
		$YearMonthCheck="";
		/*****月份統計及工廠加總(結束)*****/
		
		/*****列出尚未case close的樣品單(開始)*****/
		$rpt_cl=$report->smpl_fty_searchNew($DateStart,$DateEnd,$factory,$dept,$Cust);
		$qty = $num = $done  = $rem = $FactorySum = $MonthSum = 0;
		$Top_qty = $BTM_qty = $Top_done =$BTM_done = $Top_rem = $BTM_rem = 0;
		$Top_Month_qty = $BTM_Month_qty = $Top_Month_done =$BTM_Month_done = $Top_Month_rem = $BTM_Month_rem = 0;
		$Top_Total_qty = $BTM_Total_qty = $Top_Total_done =$BTM_Total_done = $Top_Total_rem = $BTM_Total_rem = 0;
		$tmp_mm = $tmp_fty = '';
		$FactoryRem=$FactoryDone=$FactoryQty=0;
		
		for($i=0; $i<sizeof($rpt_cl); $i++)
		{
					
			/*這個迴圈好像把同月份的東西加總，至於加哪些東西我也不知道*/
			$tmp = explode('-',$rpt_cl[$i]['etd']);//把原本etd裡面的值拆分的三個陣列，已"-"為拆分字符
			
			$mm = substr($tmp[0],2).'-'.$tmp[1];//將第一個陣列的第三個值直到最後一個值取出，並與第二個陣列結合,例2012-01-20 => 12-01

			if($tmp_mm == '') $tmp_mm = $mm;
			if($tmp_fty == '' ) 
			{
				
				$tmp_fty = $rpt_cl[$i]['factory'];
				$rpt_SmplNoClose[$num]['check']='Header';
				
			}
			
		
				/*判斷工廠是否已經轉換*/
				if($tmp_fty != '' && $tmp_fty != $rpt_cl[$i]['factory'] )
				{
					/*不同工廠*/
					/*************切換加總**************/
					$rpt_SmplNoClose[$num]['fty']=$tmp_fty;
					$rpt_SmplNoClose[$num]['YearMonth']=$tmp_mm;
					$rpt_SmplNoClose[$num]['s_qty'] = $qty;
					$rpt_SmplNoClose[$num]['s_rem'] = $rem;
					$rpt_SmplNoClose[$num]['s_done'] = $done;
					$rpt_SmplNoClose[$num]['s_num'] = $num;
					$rpt_SmplNoClose[$num]['s_mm'] = $tmp_mm;
					$rpt_SmplNoClose[$num]['tb_mk'] = 1;
					$rpt_SmplNoClose[$num]['last']='YES';
					$rpt_SmplNoClose[$num]['check']='Footer';
					$rpt_SmplNoClose[$num]['monthsum']=$MonthSum;
					$rpt_SmplNoClose[$num]['factorysum']=$FactorySum;
					$rpt_SmplNoClose[$num]['factoryqty']=$FactoryQty;
					$rpt_SmplNoClose[$num]['factoryrem']=$FactoryRem;
					$rpt_SmplNoClose[$num]['factorydone']=$FactoryDone;	
					$rpt_SmplNoClose[$num]['Top_qty']=$Top_qty;
					$rpt_SmplNoClose[$num]['Top_done']=$Top_done;
					$rpt_SmplNoClose[$num]['Top_rem']=$Top_rem;
					$rpt_SmplNoClose[$num]['BTM_qty']=$BTM_qty;
					$rpt_SmplNoClose[$num]['BTM_done']=$BTM_done;
					$rpt_SmplNoClose[$num]['BTM_rem']=$BTM_rem;

					$rpt_SmplNoClose[$num]['Top_Total_qty']=$Top_Total_qty;
					$rpt_SmplNoClose[$num]['Top_Total_done']=$Top_Total_done;
					$rpt_SmplNoClose[$num]['Top_Total_rem']=$Top_Total_rem;
					$rpt_SmplNoClose[$num]['BTM_Total_qty']=$BTM_Total_qty;
					$rpt_SmplNoClose[$num]['BTM_Total_done']=$BTM_Total_done;
					$rpt_SmplNoClose[$num]['BTM_Total_rem']=$BTM_Total_rem;
					$MonthSum=$FactorySum=0;
					$num++;
					/***********切換加總(結束)************/
					
					$tmp_mm = $mm;
					$FactoryRem=$FactoryDone=$FactoryQty=0;
					$qty = $done  = $rem = 0;
					$Top_qty = $BTM_qty = $Top_done =$BTM_done = $Top_rem = $BTM_rem = 0;
					/* $Top_Month_qty = $BTM_Month_qty = $Top_Month_done =$BTM_Month_done = $Top_Month_rem = $BTM_Month_rem = 0; */
					$Top_Total_qty = $BTM_Total_qty = $Top_Total_done =$BTM_Total_done = $Top_Total_rem = $BTM_Total_rem = 0;
					
					$tmp_fty = $rpt_cl[$i]['factory'];
					$rpt_SmplNoClose[$num]['rem'] = 0;
					if($rpt_cl[$i]['qty'] > $rpt_cl[$i]['qty_done']) 
					{
						$rpt_SmplNoClose[$num]['rem'] = $rpt_cl[$i]['qty'] - $rpt_cl[$i]['qty_done'];
					}
					$rpt_SmplNoClose[$num]['id']=$rpt_cl[$i]['id'];
					$rpt_SmplNoClose[$num]['order']=$rpt_cl[$i]['num'];
					$rpt_SmplNoClose[$num]['qty']=$rpt_cl[$i]['qty'];
					$rpt_SmplNoClose[$num]['qty_down']=$rpt_cl[$i]['qty_done'];
					$rpt_SmplNoClose[$num]['num']=$rpt_cl[$i]['num'];
					$rpt_SmplNoClose[$num]['smpl_type']=$rpt_cl[$i]['smpl_type'];
					$rpt_SmplNoClose[$num]['style']=$rpt_cl[$i]['style'];
					$rpt_SmplNoClose[$num]['cust']=$rpt_cl[$i]['cust'];
					$rpt_SmplNoClose[$num]['etd']=$rpt_cl[$i]['etd'];
					$rpt_SmplNoClose[$num]['schd_smpl']=$rpt_cl[$i]['schd_smpl'];
					$rpt_SmplNoClose[$num]['status']=$rpt_cl[$i]['status'];
					$rpt_SmplNoClose[$num]['ref']=$rpt_cl[$i]['ref'];
					$rpt_SmplNoClose[$num]['fty']=$tmp_fty;
					$rpt_SmplNoClose[$num]['YearMonth']=$tmp_mm;
					$rpt_SmplNoClose[$num]['last']='NO';
					$rpt_SmplNoClose[$num]['check']='Header';
					/* $rpt_SmplNoClose[$num]['p_etd']=$rpt_cl[$i]['schd_pttn']; */
					$rpt_SmplNoClose[$num]['p_etf']=$rpt_cl[$i]['schd_pttn'];
					/* $rpt_SmplNoClose[$num]['s_etd']=$rpt_cl[$i]['schd_smpl']; */
					$rpt_SmplNoClose[$num]['s_etf']=$rpt_cl[$i]['schd_smpl'];
					$rpt_SmplNoClose[$num]['ptn_down']=$rpt_cl[$i]['done_pttn'];
					$rpt_SmplNoClose[$num]['sample_down']=$rpt_cl[$i]['done_smpl'];
					if($rpt_cl[$i]['style']=='JK' or $rpt_cl[$i]['style']=='BS' or $rpt_cl[$i]['style']=='BZ' or $rpt_cl[$i]['style']=='DR' or $rpt_cl[$i]['style']=='TP' or $rpt_cl[$i]['style']=='VS')
					{
						$Top_qty+=$rpt_cl[$i]['qty'];
						$Top_done+=$rpt_cl[$i]['qty_done'];
						$Top_rem+=$rpt_cl[$i]['rem'];
						$Top_Total_qty+=$rpt_cl[$i]['qty'];
						$Top_Total_done+=$rpt_cl[$i]['qty_done'];
						$Top_Total_rem+=$rpt_cl[$i]['rem'];
					}
					if($rpt_cl[$i]['style']=='PT' or $rpt_cl[$i]['style']=='SH' or $rpt_cl[$i]['style']=='SK' or $rpt_cl[$i]['style']=='SO')
					{
						$BTM_qty+=$rpt_cl[$i]['qty'];
						$BTM_done+=$rpt_cl[$i]['qty_done'];
						$BTM_rem+=$rpt_cl[$i]['rem'];
						$BTM_Total_qty+=$rpt_cl[$i]['qty'];
						$BTM_Total_done+=$rpt_cl[$i]['qty_done'];
						$BTM_Total_rem+=$rpt_cl[$i]['rem'];
					}
						
					$rem += $rpt_SmplNoClose[$num]['rem'];
					$done += $rpt_cl[$i]['qty_done'];
					$qty += $rpt_cl[$i]['qty'];
					$FactorySum++;
					$MonthSum++;
					$FactoryRem+=$rpt_SmplNoClose[$num]['rem'];
					$FactoryDone+=$rpt_cl[$i]['qty_done'];
					$FactoryQty+=$rpt_cl[$i]['qty'];
					
					$num++;
				}
				else
				{
					/*相同工廠*/
					/*判斷月份是否已經轉換*/
					if($tmp_mm != '' && $tmp_mm != $mm )
					{
						/*不同月份*/
						/*************切換加總**************/
						$rpt_SmplNoClose[$num]['fty']=$tmp_fty;
						$rpt_SmplNoClose[$num]['YearMonth']=$tmp_mm;
						$rpt_SmplNoClose[$num]['s_qty'] = $qty;
						$rpt_SmplNoClose[$num]['s_rem'] = $rem;
						$rpt_SmplNoClose[$num]['s_done'] = $done;
						$rpt_SmplNoClose[$num]['s_num'] = $num;
						$rpt_SmplNoClose[$num]['s_mm'] = $tmp_mm;
						$rpt_SmplNoClose[$num]['tb_mk'] = 1;
						$rpt_SmplNoClose[$num]['last']='YES';
						$rpt_SmplNoClose[$num]['check']='';
						$rpt_SmplNoClose[$num]['monthsum']=$MonthSum;
						$rpt_SmplNoClose[$num]['factorysum']=$FactorySum;
						$rpt_SmplNoClose[$num]['Top_qty']=$Top_qty;
						$rpt_SmplNoClose[$num]['Top_done']=$Top_done;
						$rpt_SmplNoClose[$num]['Top_rem']=$Top_rem;
						$rpt_SmplNoClose[$num]['BTM_qty']=$BTM_qty;
						$rpt_SmplNoClose[$num]['BTM_done']=$BTM_done;
						$rpt_SmplNoClose[$num]['BTM_rem']=$BTM_rem;
						
						$MonthSum=0;
						$num++;
						/***********切換加總(結束)************/
						
						$tmp_mm = $mm;
						$qty = $done  = $rem = 0;
						$Top_qty = $BTM_qty = $Top_done =$BTM_done = $Top_rem = $BTM_rem = 0;
						
						$rpt_SmplNoClose[$num]['rem'] = 0;
						if($rpt_cl[$i]['qty'] > $rpt_cl[$i]['qty_done']) 
						{
							$rpt_SmplNoClose[$num]['rem'] = $rpt_cl[$i]['qty'] - $rpt_cl[$i]['qty_done'];
						}
						$rpt_SmplNoClose[$num]['id']=$rpt_cl[$i]['id'];
						$rpt_SmplNoClose[$num]['order']=$rpt_cl[$i]['num'];
						$rpt_SmplNoClose[$num]['qty']=$rpt_cl[$i]['qty'];
						$rpt_SmplNoClose[$num]['qty_down']=$rpt_cl[$i]['qty_done'];
						$rpt_SmplNoClose[$num]['smpl_type']=$rpt_cl[$i]['smpl_type'];
						$rpt_SmplNoClose[$num]['style']=$rpt_cl[$i]['style'];
						$rpt_SmplNoClose[$num]['cust']=$rpt_cl[$i]['cust'];
						$rpt_SmplNoClose[$num]['etd']=$rpt_cl[$i]['etd'];
						$rpt_SmplNoClose[$num]['schd_smpl']=$rpt_cl[$i]['schd_smpl'];
						$rpt_SmplNoClose[$num]['status']=$rpt_cl[$i]['status'];
						$rpt_SmplNoClose[$num]['ref']=$rpt_cl[$i]['ref'];
						$rpt_SmplNoClose[$num]['fty']=$tmp_fty;
						$rpt_SmplNoClose[$num]['YearMonth']=$tmp_mm;
						$rpt_SmplNoClose[$num]['last']='NO';
						$rpt_SmplNoClose[$num]['check']='';
						/* $rpt_SmplNoClose[$num]['p_etd']=$rpt_cl[$i]['schd_pttn']; */
						$rpt_SmplNoClose[$num]['p_etf']=$rpt_cl[$i]['schd_pttn'];
						/* $rpt_SmplNoClose[$num]['s_etd']=$rpt_cl[$i]['schd_smpl']; */
						$rpt_SmplNoClose[$num]['s_etf']=$rpt_cl[$i]['schd_smpl'];
						$rpt_SmplNoClose[$num]['ptn_down']=$rpt_cl[$i]['done_pttn'];
						$rpt_SmplNoClose[$num]['sample_down']=$rpt_cl[$i]['done_smpl'];
						if($rpt_cl[$i]['style']=='JK' or $rpt_cl[$i]['style']=='BS' or $rpt_cl[$i]['style']=='BZ' or $rpt_cl[$i]['style']=='DR' or $rpt_cl[$i]['style']=='TP' or $rpt_cl[$i]['style']=='VS')
						{
							$Top_qty+=$rpt_cl[$i]['qty'];
							$Top_done+=$rpt_cl[$i]['qty_done'];
							$Top_rem+=$rpt_cl[$i]['rem'];
							$Top_Total_qty+=$rpt_cl[$i]['qty'];
							$Top_Total_done+=$rpt_cl[$i]['qty_done'];
							$Top_Total_rem+=$rpt_cl[$i]['rem'];
						}
						if($rpt_cl[$i]['style']=='PT' or $rpt_cl[$i]['style']=='SH' or $rpt_cl[$i]['style']=='SK' or $rpt_cl[$i]['style']=='SO')
						{
							$BTM_qty+=$rpt_cl[$i]['qty'];
							$BTM_done+=$rpt_cl[$i]['qty_done'];
							$BTM_rem+=$rpt_cl[$i]['rem'];
							$BTM_Total_qty+=$rpt_cl[$i]['qty'];
							$BTM_Total_done+=$rpt_cl[$i]['qty_done'];
							$BTM_Total_rem+=$rpt_cl[$i]['rem'];
						}
						
						$rem += $rpt_SmplNoClose[$num]['rem'];
						$done += $rpt_cl[$i]['qty_done'];
						$qty += $rpt_cl[$i]['qty'];
						$FactorySum++;
						$MonthSum++;
						$FactoryRem+=$rpt_SmplNoClose[$num]['rem'];
						$FactoryDone+=$rpt_cl[$i]['qty_done'];
						$FactoryQty+=$rpt_cl[$i]['qty'];
						
						$num++;
						
					} 
					else
					{
						/*相同月份*/
						
						$rpt_SmplNoClose[$num]['rem'] = 0;
						if($rpt_cl[$i]['qty'] > $rpt_cl[$i]['qty_done']) 
						{
							$rpt_SmplNoClose[$num]['rem'] = $rpt_cl[$i]['qty'] - $rpt_cl[$i]['qty_done'];
						}
						if($num==0)
						{
							$rpt_SmplNoClose[$num]['check']='Header';
						}
						else
						{
							$rpt_SmplNoClose[$num]['check']='';
						}
						$rpt_SmplNoClose[$num]['id']=$rpt_cl[$i]['id'];
						$rpt_SmplNoClose[$num]['order']=$rpt_cl[$i]['num'];
						$rpt_SmplNoClose[$num]['qty']=$rpt_cl[$i]['qty'];
						$rpt_SmplNoClose[$num]['qty_down']=$rpt_cl[$i]['qty_done'];
						$rpt_SmplNoClose[$num]['smpl_type']=$rpt_cl[$i]['smpl_type'];
						$rpt_SmplNoClose[$num]['style']=$rpt_cl[$i]['style'];
						$rpt_SmplNoClose[$num]['cust']=$rpt_cl[$i]['cust'];
						$rpt_SmplNoClose[$num]['etd']=$rpt_cl[$i]['etd'];
						$rpt_SmplNoClose[$num]['schd_smpl']=$rpt_cl[$i]['schd_smpl'];
						$rpt_SmplNoClose[$num]['status']=$rpt_cl[$i]['status'];
						$rpt_SmplNoClose[$num]['ref']=$rpt_cl[$i]['ref'];
						$rpt_SmplNoClose[$num]['fty']=$tmp_fty;
						$rpt_SmplNoClose[$num]['YearMonth']=$tmp_mm;
						$rpt_SmplNoClose[$num]['last']='NO';
						/* $rpt_SmplNoClose[$num]['p_etd']=$rpt_cl[$i]['schd_pttn']; */
						$rpt_SmplNoClose[$num]['p_etf']=$rpt_cl[$i]['schd_pttn'];
						/* $rpt_SmplNoClose[$num]['s_etd']=$rpt_cl[$i]['schd_smpl']; */
						$rpt_SmplNoClose[$num]['s_etf']=$rpt_cl[$i]['schd_smpl'];
						$rpt_SmplNoClose[$num]['ptn_down']=$rpt_cl[$i]['done_pttn'];
						$rpt_SmplNoClose[$num]['sample_down']=$rpt_cl[$i]['done_smpl'];
						if($rpt_cl[$i]['style']=='JK' or $rpt_cl[$i]['style']=='BS' or $rpt_cl[$i]['style']=='BZ' or $rpt_cl[$i]['style']=='DR' or $rpt_cl[$i]['style']=='TP' or $rpt_cl[$i]['style']=='VS')
						{
							$Top_qty+=$rpt_cl[$i]['qty'];
							$Top_done+=$rpt_cl[$i]['qty_done'];
							$Top_rem+=$rpt_cl[$i]['rem'];
							$Top_Total_qty+=$rpt_cl[$i]['qty'];
							$Top_Total_done+=$rpt_cl[$i]['qty_done'];
							$Top_Total_rem+=$rpt_cl[$i]['rem'];
						}
						if($rpt_cl[$i]['style']=='PT' or $rpt_cl[$i]['style']=='SH' or $rpt_cl[$i]['style']=='SK' or $rpt_cl[$i]['style']=='SO')
						{
							$BTM_qty+=$rpt_cl[$i]['qty'];
							$BTM_done+=$rpt_cl[$i]['qty_done'];
							$BTM_rem+=$rpt_cl[$i]['rem'];
							$BTM_Total_qty+=$rpt_cl[$i]['qty'];
							$BTM_Total_done+=$rpt_cl[$i]['qty_done'];
							$BTM_Total_rem+=$rpt_cl[$i]['rem'];
						}
						
						$rem += $rpt_SmplNoClose[$num]['rem'];
						$done += $rpt_cl[$i]['qty_done'];
						$qty += $rpt_cl[$i]['qty'];
						$FactorySum++;
						$MonthSum++;
						$FactoryRem+=$rpt_SmplNoClose[$num]['rem'];
						$FactoryDone+=$rpt_cl[$i]['qty_done'];
						$FactoryQty+=$rpt_cl[$i]['qty'];
						
						$num++;
					}
				}
			
		}
		/*******切換加總(最後一筆處理)********/
		$rpt_SmplNoClose[$num]['fty']=$tmp_fty;
		$rpt_SmplNoClose[$num]['YearMonth']=$tmp_mm;
		$rpt_SmplNoClose[$num]['s_qty'] = $qty;
		$rpt_SmplNoClose[$num]['s_rem'] = $rem;
		$rpt_SmplNoClose[$num]['s_done'] = $done;
		$rpt_SmplNoClose[$num]['s_num'] = $num;
		$rpt_SmplNoClose[$num]['s_mm'] = $tmp_mm;
		$rpt_SmplNoClose[$num]['tb_mk'] = 1;
		$rpt_SmplNoClose[$num]['last']='YES';
		$rpt_SmplNoClose[$num]['check']='Footer';
		$rpt_SmplNoClose[$num]['monthsum']=$MonthSum;
		$rpt_SmplNoClose[$num]['factorysum']=$FactorySum;
		$rpt_SmplNoClose[$num]['factoryqty']=$FactoryQty;
		$rpt_SmplNoClose[$num]['factoryrem']=$FactoryRem;
		$rpt_SmplNoClose[$num]['factorydone']=$FactoryDone;
		
		$rpt_SmplNoClose[$num]['Top_qty']=$Top_qty;
		$rpt_SmplNoClose[$num]['Top_done']=$Top_done;
		$rpt_SmplNoClose[$num]['Top_rem']=$Top_rem;
		$rpt_SmplNoClose[$num]['BTM_qty']=$BTM_qty;
		$rpt_SmplNoClose[$num]['BTM_done']=$BTM_done;
		$rpt_SmplNoClose[$num]['BTM_rem']=$BTM_rem;
		$rpt_SmplNoClose[$num]['Top_Total_qty']=$Top_Total_qty;
		$rpt_SmplNoClose[$num]['Top_Total_done']=$Top_Total_done;
		$rpt_SmplNoClose[$num]['Top_Total_rem']=$Top_Total_rem;
		$rpt_SmplNoClose[$num]['BTM_Total_qty']=$BTM_Total_qty;
		$rpt_SmplNoClose[$num]['BTM_Total_done']=$BTM_Total_done;
		$rpt_SmplNoClose[$num]['BTM_Total_rem']=$BTM_Total_rem;
		/***********切換加總(結束)************/
		
		/*****明細及工廠加總(結束)*****/
		
		
		
		
		
		
		/*****列出尚未case close的樣品單(結束)*****/

	$op['rpt_cl_new'] = $rpt_SmplNoClose;
	$op['rpt_full'] = $rpt_full_sum;
	page_display($op,'072', $TPL_RPT_SMPL_FTY);
break;



//====================================================
case "un_rcv_list":
	$op['un_rcv'] = $report->po_un_rcv();
	$op['un_rcv_date'] = $un_rcv_date;	
	$op['un_rcv'] = bubble_sort_s($op['un_rcv']);

	$op['today']=$TODAY;
page_display($op,'072', $TPL_UN_RCV_MAIN);
break;

case "un_rcv_list_top":
$op['un_rcv_date'] = $un_rcv_date;
page_display($op,'072', $TPL_UN_RCV_TOP);
break;

case "un_rcv_list_main":
	$op['un_rcv'] = $report->po_un_rcv();

	$op['today']=$TODAY;
page_display($op,'072', $TPL_UN_RCV_MAIN);
break;	

//====================================================
case "ord_prd_print":
		$rpt=$report->search_f_ord_tbl($SCH_mm);
		$rpt_m=$report->search_m_ord_tbl($SCH_mm);

//---------------------------------------------------------------------
include_once($config['root_dir']."/lib/class.pdf_ord_prd.php");

$print_title="Production Order Check-List";
$mark = 'MONTH : '.$SCH_mm.' ORDER RECORDS';

$pdf=new PDF_ord_prd('L','mm','A4');
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();
$pdf->SetAutoPageBreak(1,15);
$pdf->SetFont('Arial','B',14);

for($i=0; $i<sizeof($rpt); $i++)
{
	$acc_rec = $lot_rec = array();
	if($rpt[$i]['factory'] == 'LY')
	{
		$rpt[$i]['sch_rec'] = $schedule->order_get($rpt[$i]['order_num']);
		$pdf->ord_recs($rpt[$i]);
		$recs = $po->get_ship_rmk($rpt[$i]['order_num'],'l');
		if($recs)
		{
			$lot_rec = explode('|',$recs);
			$pdf->show_mat_recs($lot_rec);
		}
		$recs = $po->get_ship_rmk($rpt[$i]['order_num'],'a');
		if($recs)
		{
			$acc_rec = explode('|',$recs);
			$pdf->show_mat_recs($acc_rec);
		}

	}
}

$name=$SCH_mm.'_rcvd.pdf';
$pdf->Output($name,'D');

break;
	
	
//====================================================
case "rpt_ord_book_search":
	$parm = array('fty'	=>	$PHP_factory,
							 	'str'	=>	$PHP_start,
							 	'end'	=>	$PHP_finish
							 );
	
	$op['sorder'] = $report->get_order_det($parm);
	$op['title'] = $parm;
page_display($op,'072', $TPL_RPT_ORD_BOOKING);
break;




//====================================================
case "rpt_ord_dist_search":
check_authority('072',"view");

if (!$PHP_start) $PHP_start = date('Y')."-01-01";
if (!$PHP_finish) $PHP_finish = $TODAY;
$dist = array ('1000','2000','3000','4000','5000');
// print_r($_POST);
$rpt=$report->ord_qty_cut_search($PHP_factory,$PHP_start,$PHP_finish,$dist);  //取出記錄	

$op['ord_total'] = 0;
for ($i=0; $i< sizeof ($rpt); $i++)
{
$op['ord_total'] = $op['ord_total'] + $rpt[$i];
}

for ($i=0; $i< sizeof ($rpt); $i++)
{
$ord_rate[$i] = ($rpt[$i] / $op['ord_total']) * 100;
}

$op['ord']=$rpt;
$op['ord_rate']=$ord_rate;
$op['dist']=$dist;
//	$op['year']=$PHP_year;
$op['today']=$TODAY;
$op['fty']=$PHP_factory;
$op['start']=$PHP_start;
$op['finish']=$PHP_finish;

page_display($op,'072', $TPL_RPT_ORD_DIST);
break;




//====================================================
case "rep_day_count":
	check_authority('072',"view");
	if (!$PHP_year1)$PHP_year1=date('Y');
	$search_day=$PHP_year1;
	if (!$PHP_factory)
	{				
		$msg = "Please choice factory or year first";
		$redir_str = "index2.php?PHP_action=out_search&PHP_msg=".$msg;
		redirect_page($redir_str);
	}	
	
	$rpt=$report->order_day_list($search_day,$PHP_factory);  //取出記錄	
	$tmp=$rpt[0]['cust'];
	$rlt[0]['mk_cust']=$rpt[0]['cust_init'];
	$rlt[0]['cust']=$rpt[0]['cust'];
	
	$smpl = $mtl = 0; $apv=0; $avl=0; $x=0; $y=0;
	$rlt[$y]['mtl_days']=$rlt[$y]['smpl_days']=$rlt[$y]['avi_days']=0;	$rlt[$y]['apv_days'] = 0;	$rlt[$y]['open_days'] =0;
	$rlt[$y]['qty'] = 0;	$rlt[$y]['su'] = 0;

	$ord_ttl['mtl_days']=$ord_ttl['smpl_days']=$ord_ttl['avi_days']=0;	$ord_ttl['apv_days'] = 0;	$ord_ttl['open_days'] =0;
	$ord_ttl['qty'] = 0;	$ord_ttl['su'] = 0;
	$ttl_smpl = $ttl_mtl = 0; $ttl_apv=0; $ttl_avl=0; $ttl_x=0;
	
	if ($rpt)
	{
		for ($i=0; $i<sizeof($rpt); $i++)
		{
			if ($tmp<>$rpt[$i]['cust'])
			{
//echo $tmp."==>".$rlt[$y]['avi_days']."==>".$avl."<BR>";
				if ($avl > 0) $rlt[$y]['avi_days'] = (int)($rlt[$y]['avi_days'] / $avl);
				if ($apv > 0) $rlt[$y]['apv_days'] = (int)($rlt[$y]['apv_days'] / $apv);
				if ($smpl > 0) $rlt[$y]['smpl_days'] = (int)($rlt[$y]['smpl_days'] / $smpl);
				if ($mtl > 0) $rlt[$y]['mtl_days'] = (int)($rlt[$y]['mtl_days'] / $mtl);
				if ($x > 0) $rlt[$y]['open_days']  = (int)($rlt[$y]['open_days'] / $x);
				if ($x > 0) $rlt[$y]['avg_qty']        = (int)($rlt[$y]['qty'] / $x);
				if ($x > 0) $rlt[$y]['avg_su']         = (int)($rlt[$y]['su'] / $x);
				$rlt[$y]['sort']=$rlt[$y]['avi_days'];
				$tmp_y=$y;				
				$tmp=$rpt[$i]['cust'];
				$y++;
				$rlt[$y]['mk_cust']=$rpt[$i]['cust_init'];
				$rlt[$y]['cust']=$rpt[$i]['cust'];
				$smpl = $mtl = 0; $apv=0; $avl=0; $x=0;
				$rlt[$y]['mtl_days']=$rlt[$y]['smpl_days']=$rlt[$y]['avi_days']=0;	$rlt[$y]['apv_days'] = 0;	$rlt[$y]['open_days'] =0;
				$rlt[$y]['qty'] = 0;	$rlt[$y]['su'] = 0;
			}
			
			$open_day=countDays($rpt[$i]['opendate'],$rpt[$i]['etd']);
			$rlt[$y]['open_days']=$rlt[$y]['open_days']+$open_day;
			$rlt[$y]['qty']=$rlt[$y]['qty']+$rpt[$i]['qty'];
			$rlt[$y]['su']=$rlt[$y]['su']+$rpt[$i]['su'];	

			$ord_ttl['qty']+=$rpt[$i]['qty'];
			$ord_ttl['su']+=$rpt[$i]['su'];	
			
			$x++;
			$ttl_x++;
			if ($rpt[$i]['apv_date'] <> '0000-00-00')
			{
				$rlt[$y]['apv_days']=$rlt[$y]['apv_days']+countDays($rpt[$i]['apv_date'],$rpt[$i]['etd']);
				$apv++;

				$ord_ttl['apv_days'] += countDays($rpt[$i]['apv_date'],$rpt[$i]['etd']);
				$ttl_apv++;

			}
			
			if ($rpt[$i]['mat_shp']<>'' && $rpt[$i]['m_acc_shp']<>'' && $rpt[$i]['smpl_apv'] <>'0000-00-00')
			{
				$tmp1=$rpt[$i]['mat_shp'];
				if ($rpt[$i]['m_acc_shp'] > $tmp1)$tmp1=$rpt[$i]['m_acc_shp'];
				if ($rpt[$i]['smpl_apv'] > $tmp1)$tmp1=$rpt[$i]['smpl_apv'];
				if($rpt[$i]['start'] <> '' && $rpt[$i]['start'] < $tmp1)$tmp1=$rpt[$i]['start'];
				$rpt[$i]['avi_date']=$tmp1;
				$rlt[$y]['avi_days']=$rlt[$y]['avi_days']+countDays($rpt[$i]['avi_date'],$rpt[$i]['etd']);
				$avl++;
//echo $rpt[$i]['order_num']."==>".$rpt[$i]['avi_date']."===>".countDays($rpt[$i]['avi_date'],$rpt[$i]['etd'])."<BR>";

				$ord_ttl['avi_days']+=countDays($rpt[$i]['avi_date'],$rpt[$i]['etd']);
				$ttl_avl++;

			}
			
			if ($rpt[$i]['mat_shp']<>'' && $rpt[$i]['m_acc_shp']<>'' && ($rpt[$i]['mat_shp']<> '1111-11-11' || $rpt[$i]['m_acc_shp'] <> '1111-11-11') )
			{
				$tmp1=$rpt[$i]['mat_shp'];
				if ($rpt[$i]['m_acc_shp'] > $tmp1)$tmp1=$rpt[$i]['m_acc_shp'];
				$rpt[$i]['mtl_date']=$tmp1;
				$rlt[$y]['mtl_days']=$rlt[$y]['mtl_days']+countDays($rpt[$i]['mtl_date'],$rpt[$i]['etd']);
				$mtl++;

				$ord_ttl['mtl_days']+=countDays($rpt[$i]['mtl_date'],$rpt[$i]['etd']);
				//echo countDays($rpt[$i]['mtl_date'],$rpt[$i]['etd'])."<BR>";
				$ttl_mtl++;

			}

			if ($rpt[$i]['smpl_apv'] <>'0000-00-00')
			{
				$rpt[$i]['smpl_date']=$rpt[$i]['smpl_apv'];
				$rlt[$y]['smpl_days']=$rlt[$y]['smpl_days']+countDays($rpt[$i]['smpl_apv'],$rpt[$i]['etd']);
				$smpl++;

				$ord_ttl['smpl_days']+=countDays($rpt[$i]['smpl_apv'],$rpt[$i]['etd']);
				$ttl_smpl++;

			}
			
		}
		
		
		if ($tmp_y <> $y)
		{
				if ($avl > 0) $rlt[$y]['avi_days'] = (int)($rlt[$y]['avi_days'] / $avl);
				if ($apv > 0) $rlt[$y]['apv_days'] = (int)($rlt[$y]['apv_days'] / $apv);
				if ($smpl > 0) $rlt[$y]['smpl_days'] = (int)($rlt[$y]['smpl_days'] / $smpl);
				if ($mtl > 0) $rlt[$y]['mtl_days'] = (int)($rlt[$y]['mtl_days'] / $mtl);

				if ($x > 0) $rlt[$y]['open_days']  = (int)($rlt[$y]['open_days'] / $x);
				if ($x > 0) $rlt[$y]['avg_qty']        = (int)($rlt[$y]['qty'] / $x);
				if ($x > 0) $rlt[$y]['avg_su']         = (int)($rlt[$y]['su'] / $x);
				$rlt[$y]['sort']=$rlt[$y]['avi_days'];
		}

		if ($ttl_avl > 0) $ord_ttl['avi_days']   = (int)($ord_ttl['avi_days'] / $ttl_avl);
		if ($ttl_apv > 0) $ord_ttl['apv_days']   = (int)($ord_ttl['apv_days'] / $ttl_apv);
		if ($ttl_smpl > 0) $ord_ttl['smpl_days'] = (int)($ord_ttl['smpl_days'] / $ttl_smpl);
		if ($ttl_mtl > 0) $ord_ttl['mtl_days'] 	 = (int)($ord_ttl['mtl_days'] / $ttl_mtl);
		if ($ttl_x > 0) $ord_ttl['open_days']  	 = (int)($ord_ttl['open_days'] / $ttl_x);
		if ($ttl_x > 0) $ord_ttl['avg_qty']      = (int)($ord_ttl['qty'] / $ttl_x);
		if ($ttl_x > 0) $ord_ttl['avg_su']       = (int)($ord_ttl['su'] / $ttl_x);

	}else{
		$op['msg']="Without any records";
	}
	
	$rlt=bubble_sort($rlt);
	$op['ord']=$rlt;
	$op['ord_ttl'] = $ord_ttl;
	$op['year']=$PHP_year1;
	$op['today']=$TODAY;
	$op['fty']=$PHP_factory;
	if (!isset($PHP_excel))
	{
		page_display($op,'071', $TPL_RPT_ORD_COUNT);
		break;
	}else{
	 require_once($config['root_dir']."/lib/spreadsheets/Worksheet.php");
	 require_once($config['root_dir']."/lib/spreadsheets/Workbook.php");

	  function HeaderingExcel($filename) {
		  header("Content-type: application/vnd.ms-excel");
		  header("Content-Disposition: attachment; filename=$filename" );
		  header("Expires: 0");
		  header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
		  header("Pragma: public");
		  }

	  // HTTP headers
	  HeaderingExcel('ord_count.xls');
	 
	  // Creating a workbook
	  $workbook = new Workbook("-");

	  // Creating the first worksheet
	  $worksheet1 =& $workbook->add_worksheet('ORDER Lead-Time Analysis');

		$now = $GLOBALS['THIS_TIME'];

	// 寫入 title

	  $formatot =& $workbook->add_format();
	  $formatot->set_size(10);
	  $formatot->set_align('center');
	  $formatot->set_color('white');
	  $formatot->set_pattern(1);
	  $formatot->set_fg_color('navy');
	  $formatot->set_num_format(4);
	 
	  $f3 =& $workbook->add_format(); //置右
	  $f3->set_size(10);
	  $f3->set_align('right');
	  $f3->set_num_format(3);
	  

	  $clumn = array(10,14,14,20,20,20);
//	  $title = array('customer', 'QTY', 'SU', 'Open Lead-Time', 'APV Lead-Time', 'In House Lead-Time');
		 $title = array('customer', 'QTY', 'SU', 'In House Lead-Time', 'material Lead-Time', 'smpl Lead-Time');
	  for ($i=0; $i< sizeof($clumn); $i++)  //設格子
	  {
	  	$worksheet1->set_column(0,$i,$clumn[$i]);
	  }
	  $worksheet1->write_string(0,1,'ORDER Lead-Time Analysis by customer');
	  $worksheet1->write_string(0,4,$TODAY);
	  for ($i=0; $i< sizeof($title); $i++)  //標題
	  {
	  	$worksheet1->write_string(1,$i,$title[$i],$formatot);
	  }
	  	  	  
	  $j=2;
	  for ($i=0; $i< sizeof($rlt); $i++)
	  {
	  	$worksheet1->write_string($j,0,$rlt[$i]['mk_cust']);
	  	$worksheet1->write_number($j,1,$rlt[$i]['qty'],$f3);
	  	$worksheet1->write_number($j,2,$rlt[$i]['su'],$f3);
//	  	$worksheet1->write_number($j,3,$rlt[$i]['open_days'],$f3);
//	  	$worksheet1->write_number($j,4,$rlt[$i]['apv_days'],$f3);
	  	$worksheet1->write_number($j,3,$rlt[$i]['avi_days'],$f3);
	  	$worksheet1->write_number($j,4,$rlt[$i]['mtl_days'],$f3);
	  	$worksheet1->write_number($j,5,$rlt[$i]['smpl_days'],$f3);

	  	$j++;
	  }

  	$worksheet1->write_string($j,0,'TOTAL');
  	$worksheet1->write_number($j,1,$ord_ttl['qty'],$f3);
  	$worksheet1->write_number($j,2,$ord_ttl['su'],$f3);
	 	$worksheet1->write_number($j,3,$ord_ttl['avi_days'],$f3);
	 	$worksheet1->write_number($j,4,$ord_ttl['mtl_days'],$f3);
	 	$worksheet1->write_number($j,5,$ord_ttl['smpl_days'],$f3);

  	  $workbook->close();
	break;
		
	}



//====================================================
case "ord_count_det":
	check_authority('072',"view");

	$rpt=$report->order_cust_day_list($PHP_year, $PHP_cust,$PHP_factory);  //取出記錄	
	$mk=explode('-',$rpt[0]['etd']);
	$open_days=0;	$apv_days=0;	$avi_days=0; $qty=0; $su=0; $apv=0; $avl=0; $x=0;
	$smpl_days = 0; $mtl_days=0; $smpl =0; $mtl=0;
	if ($rpt)
	{
		for ($i=0; $i<sizeof($rpt); $i++)
		{
			$tmp=explode('-',$rpt[$i]['etd']);
			if ($mk[1]<>$tmp[1])
			{
				$mk[1]=$tmp[1];
				$rpt[$i]['bk']=1;
			}
			
			$rpt[$i]['open_days']=countDays($rpt[$i]['opendate'],$rpt[$i]['etd']);
			$open_days=$open_days+$rpt[$i]['open_days'];
			$qty=$qty+$rpt[$i]['qty'];
			$su=$su+$rpt[$i]['su'];	
			$x++;
			if ($rpt[$i]['apv_date'] <> '0000-00-00')
			{
				$rpt[$i]['apv_days']=countDays($rpt[$i]['apv_date'],$rpt[$i]['etd']);
				$apv_days=$apv_days+$rpt[$i]['apv_days'];
				$apv++;
			}
			
			if ($rpt[$i]['mat_shp']<>'' && $rpt[$i]['m_acc_shp']<>'' && $rpt[$i]['smpl_apv'] <>'0000-00-00')
			{
				$tmp1=$rpt[$i]['mat_shp'];
				if ($rpt[$i]['m_acc_shp'] > $tmp1)$tmp1=$rpt[$i]['m_acc_shp'];
				if ($rpt[$i]['smpl_apv'] > $tmp1)$tmp1=$rpt[$i]['smpl_apv'];
				if($rpt[$i]['start'] <> '' && $rpt[$i]['start'] < $tmp1)$tmp1 = $rpt[$i]['start'];
				$rpt[$i]['avi_date']=$tmp1;
				$rpt[$i]['avi_days']=countDays($rpt[$i]['avi_date'],$rpt[$i]['etd']);
				$avi_days=$avi_days+$rpt[$i]['avi_days'];
				$avl++;
			}
			
			if ($rpt[$i]['mat_shp']<>'' && $rpt[$i]['m_acc_shp']<>'' && ($rpt[$i]['mat_shp']<>'1111-11-11' || $rpt[$i]['m_acc_shp']<>'1111-11-11' ))
			{
				$tmp1=$rpt[$i]['mat_shp'];
				if ($rpt[$i]['m_acc_shp'] > $tmp1)$tmp1=$rpt[$i]['m_acc_shp'];
				$rpt[$i]['mtl_date']=$tmp1;
				$rpt[$i]['mtl_days']=countDays($rpt[$i]['mtl_date'],$rpt[$i]['etd']);
				$mtl_days+=$rpt[$i]['mtl_days'];
				$mtl++;
			}

			if ($rpt[$i]['smpl_apv'] <>'0000-00-00' )
			{
				$rpt[$i]['smpl_days']=countDays($rpt[$i]['smpl_apv'],$rpt[$i]['etd']);
				$smpl_days+=$rpt[$i]['smpl_days'];
				$smpl++;
			}			
		}
		$avg_apv_days=$avg_avi_days=0;
		$avg_smpl_days=$avg_mtl_days=0;
		$avg_open_days=(int)($open_days/$x);
		$avg_qty=(int)($qty/$x);
		$avg_su=(int)($su/$x);
		if ($apv > 0)$avg_apv_days=(int)($apv_days/$apv);
		if ($avl > 0) $avg_avi_days=(int)($avi_days/$avl);
		if ($mtl > 0) $avg_mtl_days=(int)($mtl_days/$mtl);
		if ($smpl > 0) $avg_smpl_days=(int)($smpl_days/$smpl);
	}else{
		$op['msg']="Without any records";
	}

	
	$op['ord']=$rpt;
	$op['year']=$PHP_year;	$op['cust']=$PHP_cust_ini;
	$op['open_days']=$open_days;	$op['apv_days']=$apv_days;	$op['avi_days']=$avi_days;
	$op['mtl_days']=$mtl_days;    $op['smpl_days']=$smpl_days;
	$op['qty']=$qty;	$op['su']=$su;
	$op['avg_open_days']=$avg_open_days;
	$op['avg_apv_days']=$avg_apv_days;
	$op['avg_avi_days']=$avg_avi_days;
	$op['avg_mtl_days']=$avg_mtl_days;
	$op['avg_smpl_days']=$avg_smpl_days;

	$op['avg_qty']=$avg_qty;
	$op['avg_su']=$avg_su;
	$op['today']=$TODAY;
	page_display($op,'072', $TPL_RPT_ORD_COUNT_DET);
break;

	
//====================================================
case "rep_day_count_excel":
	check_authority('072',"view");
	if (!$PHP_year1)$PHP_year1=date('Y');
	$search_day=$PHP_year1;
	$rpt=$report->order_day_list($search_day,$PHP_factory);  //取出記錄	
	$tmp=$rpt[0]['cust'];
	
	$apv=0; $avl=0; $x=0; $y=0;
	$avi_days=0;	$apv_days = 0;	$open_days =0;	$qty = 0;	$su = 0;
	$smpl_days =0; $mtl_days = 0; $smpl = 0; $mtl = 0;
	if ($rpt)
	{
		for ($i=0; $i<sizeof($rpt); $i++)
		{
			if ($tmp<>$rpt[$i]['cust'])
			{
				$j=$i-1;
				$rpt[$j]['tal_smpl_days'] = (int)($smpl_days);
				$rpt[$j]['tal_mtl_days'] = (int)($mtl_days);
				$rpt[$j]['tal_avi_days'] = (int)($avi_days);
				$rpt[$j]['tal_apv_days'] = (int)($apv_days);
				$rpt[$j]['tal_open_days']= (int)($open_days);
				$rpt[$j]['tal_qty']      = (int)($qty);
				$rpt[$j]['tal_su']       = (int)($su);
				
				$rpt[$j]['avg_avi_days']=$rpt[$j]['avg_apv_days']=0;
				$rpt[$j]['avg_mtl_days']=$rpt[$j]['avg_smpl_days']=0;
				if ($mtl > 0) $rpt[$j]['avg_mtl_days'] = (int)($mtl_days / $mtl);
				if ($smpl > 0) $rpt[$j]['avg_smpl_days'] = (int)($smpl_days / $smpl);
				if ($avl > 0) $rpt[$j]['avg_avi_days'] = (int)($avi_days / $avl);
				if ($apv > 0) $rpt[$j]['avg_apv_days'] = (int)($apv_days / $apv);
				if ($x > 0)   $rpt[$j]['avg_open_days']= (int)($open_days / $x);
				if ($x > 0)   $rpt[$j]['avg_qty']      = (int)($qty / $x);
				if ($x > 0)   $rpt[$j]['avg_su']       = (int)($su / $x);
				$rpt[$j]['bl']=1;				
				
				$tmp=$rpt[$i]['cust'];							
				$apv=0; $avl=0; $x=0;
				$avi_days=0;	$apv_days = 0;	$open_days =0;
				$qty = 0;	$su = 0;
				$smpl_days =0; $mtl_days = 0; $smpl = 0; $mtl = 0;
			}			
			$rpt[$i]['open_days'] = countDays($rpt[$i]['opendate'],$rpt[$i]['etd']);
			$open_days=$open_days+$rpt[$i]['open_days'];			
			$qty=$qty+$rpt[$i]['qty'];
			$su=$su+$rpt[$i]['su'];	
			$x++;
			
			$rpt[$i]['apv_days']='';
			if ($rpt[$i]['apv_date'] <> '0000-00-00')
			{
				$rpt[$i]['apv_days'] = countDays($rpt[$i]['apv_date'],$rpt[$i]['etd']);
				$apv_days=$apv_days+$rpt[$i]['apv_days'];
				$apv++;
			}
			
			$rpt[$i]['avi_date']=$rpt[$i]['avi_days']='';
			if ($rpt[$i]['mat_shp']<>'' && $rpt[$i]['m_acc_shp']<>'' && $rpt[$i]['smpl_apv'] <>'0000-00-00')
			{
				$tmp1=$rpt[$i]['mat_shp'];
				if ($rpt[$i]['m_acc_shp'] > $tmp1)$tmp1=$rpt[$i]['m_acc_shp'];
				if ($rpt[$i]['smpl_apv'] > $tmp1)$tmp1=$rpt[$i]['smpl_apv'];
				if($rpt[$i]['start'] <> '' && $rpt[$i]['start'] < $tmp1)$tmp1=$rpt[$i]['start'];
				$rpt[$i]['avi_date']=$tmp1;
				$rpt[$i]['avi_days'] = countDays($rpt[$i]['avi_date'],$rpt[$i]['etd']);
				$avi_days=$avi_days+$rpt[$i]['avi_days'];
				$avl++;
			}		

			$rpt[$i]['mtl_date']=$rpt[$i]['mtl_days']='';
			if ($rpt[$i]['mat_shp']<>'' && $rpt[$i]['m_acc_shp']<>'' && ($rpt[$i]['mat_shp']<>'1111-11-11' || $rpt[$i]['m_acc_shp']<>'1111-11-11'))
			{
				$tmp1=$rpt[$i]['mat_shp'];
				if ($rpt[$i]['m_acc_shp'] > $tmp1)$tmp1=$rpt[$i]['m_acc_shp'];
				$rpt[$i]['mtl_date']=$tmp1;
				$rpt[$i]['mtl_days'] = countDays($rpt[$i]['mtl_date'],$rpt[$i]['etd']);
				$mtl_days+=$rpt[$i]['mtl_days'];
				$mtl++;
			}

			$rpt[$i]['smpl_days']=0;
			if ($rpt[$i]['smpl_apv'] <>'0000-00-00')
			{
				$rpt[$i]['smpl_days'] = countDays($rpt[$i]['smpl_apv'],$rpt[$i]['etd']);
				$smpl_days+=$rpt[$i]['smpl_days'];
				$smpl++;
			}	
		}
		$j=$i-1;
		$rpt[$j]['tal_mtl_days'] = (int)($mtl_days);
		$rpt[$j]['tal_smpl_days'] = (int)($smpl_days);
		$rpt[$j]['tal_avi_days'] = (int)($avi_days);
		$rpt[$j]['tal_apv_days'] = (int)($apv_days);
		$rpt[$j]['tal_open_days']= (int)($open_days);
		$rpt[$j]['tal_qty']      = (int)($qty);
		$rpt[$j]['tal_su']       = (int)($su);
				
		$rpt[$j]['avg_avi_days']=$rpt[$j]['avg_apv_days']=0;
		$rpt[$j]['avg_smpl_days'] = $rpt[$j]['avg_mtl_days']  =0;
		if ($mtl > 0) $rpt[$j]['avg_mtl_days'] = (int)($mtl_days / $mtl);
		if ($smpl > 0) $rpt[$j]['avg_smpl_days'] = (int)($smpl_days / $smpl);
		if ($avl > 0) $rpt[$j]['avg_avi_days'] = (int)($avi_days / $avl);
		if ($apv > 0) $rpt[$j]['avg_apv_days'] = (int)($apv_days / $apv);
		if ($x > 0)   $rpt[$j]['avg_open_days']= (int)($open_days / $x);
		if ($x > 0)   $rpt[$j]['avg_qty']      = (int)($qty / $x);
		if ($x > 0)   $rpt[$j]['avg_su']       = (int)($su / $x);
		$rpt[$j]['bl']=1;				
	}else{
		$op['msg']="Without any records";
	}

	
	 require_once($config['root_dir']."/lib/spreadsheets/Worksheet.php");
	 require_once($config['root_dir']."/lib/spreadsheets/Workbook.php");

	  function HeaderingExcel($filename) {
		  header("Content-type: application/vnd.ms-excel");
		  header("Content-Disposition: attachment; filename=$filename" );
		  header("Expires: 0");
		  header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
		  header("Pragma: public");
		  }

	  // HTTP headers
	  HeaderingExcel('ord_count_det.xls');
	 
	  // Creating a workbook
	  $workbook = new Workbook("-");

	  // Creating the first worksheet
	  $worksheet1 =& $workbook->add_worksheet('ORDER Lead-Time Analysis');

		$now = $GLOBALS['THIS_TIME'];

	// 寫入 title

	  $formatot =& $workbook->add_format();
	  $formatot->set_size(10);
	  $formatot->set_align('center');
	  $formatot->set_color('white');
	  $formatot->set_pattern(1);
	  $formatot->set_fg_color('navy');
	  $formatot->set_num_format(4);
	  
	  $f3 =& $workbook->add_format(); //置右
	  $f3->set_size(10);
	  $f3->set_align('right');
	  $f3->set_num_format(3);
	  
	  $f5 =& $workbook->add_format();  //灰底白字置中
	  $f5->set_size(10);
	  $f5->set_align('center');
	  $f5->set_color('white');
	  $f5->set_pattern(1);
	  $f5->set_fg_color('grey');
	  
	  $f6 =& $workbook->add_format();  //灰底白字置右
	  $f6->set_size(10);
	  $f6->set_color('white');
	  $f6->set_pattern(1);
	  $f6->set_align('right');
	  $f6->set_num_format(3);
	  $f6->set_fg_color('grey');

	  $clumn = array(10,10,10,10,10,12,10,12,10,12,10);
//	  $title = array('customer','order #','ETD','QTY', 'SU', 'Open date','OPN-Ld.T', 'APV date','APV-Ld.T','InH date', 'InH-Ld.T');
	  $title = array('customer','order #','ETD','QTY', 'SU', 'InH date', 'InH-Ld.T','MTL. date','MTL-Ld.T','SMPL date','SMPL-Ld. date');
	  for ($i=0; $i< sizeof($clumn); $i++)
	  {
	  	$worksheet1->set_column(0,$i,$clumn[$i]);
	  }
	  $worksheet1->write_string(0,1,'ORDER Lead-Time Analysis by customer');
	  $worksheet1->write_string(0,10,$TODAY);
	  for ($i=0; $i< sizeof($title); $i++)
	  {
	  	$worksheet1->write_string(1,$i,$title[$i],$formatot);
	  }
	  	  	  
	  $j=2;
	  for ($i=0; $i< sizeof($rpt); $i++)
	  {	  	
	  	$worksheet1->write_string($j,0,$rpt[$i]['cust_init']);
	  	$worksheet1->write_string($j,1,$rpt[$i]['ord_num']);
	  	$worksheet1->write_string($j,2,$rpt[$i]['etd']);
	  	$worksheet1->write_number($j,3,$rpt[$i]['qty'],$f3);
	  	$worksheet1->write_number($j,4,$rpt[$i]['su'],$f3);
//	  	$worksheet1->write_string($j,5,$rpt[$i]['opendate']);
//	  	$worksheet1->write_number($j,6,$rpt[$i]['open_days'],$f3);
//	  	$worksheet1->write_string($j,7,$rpt[$i]['apv_date']);
//	  	$worksheet1->write_number($j,8,$rpt[$i]['apv_days'],$f3);
	  	$worksheet1->write_string($j,5,$rpt[$i]['avi_date']);
	  	$worksheet1->write_number($j,6,$rpt[$i]['avi_days'],$f3);
	  	$worksheet1->write_string($j,7,$rpt[$i]['mtl_date']);
	  	$worksheet1->write_number($j,8,$rpt[$i]['mtl_days'],$f3);
	  	$worksheet1->write_string($j,9,$rpt[$i]['smpl_apv']);
	  	$worksheet1->write_number($j,10,$rpt[$i]['smpl_days'],$f3);

	  	$j++;
	  	if (isset($rpt[$i]['bl']))
	  	{
	  		$worksheet1->write_string($j,0,'Total');
	  		$worksheet1->write_string($j,1,$rpt[$i]['cust_init']);
	  		$worksheet1->write_number($j,3,$rpt[$i]['tal_qty'],$f3);
	  		$worksheet1->write_number($j,4,$rpt[$i]['tal_su'],$f3);
//	  		$worksheet1->write_number($j,6,$rpt[$i]['tal_open_days'],$f3);
//	  		$worksheet1->write_number($j,8,$rpt[$i]['tal_apv_days'],$f3);
	  		$worksheet1->write_number($j,6,$rpt[$i]['tal_avi_days'],$f3);
	  		$worksheet1->write_number($j,8,$rpt[$i]['tal_mtl_days'],$f3);
	  		$worksheet1->write_number($j,10,$rpt[$i]['tal_smpl_days'],$f3);
	  		$j++;
	  		
	  		$worksheet1->write_string($j,0,'Average',$f5);
	  		$worksheet1->write_string($j,1,$rpt[$i]['cust_init'],$f5);
	  		$worksheet1->write_string($j,2,'',$f5);
				$worksheet1->write_number($j,3,$rpt[$i]['avg_qty'],$f6);
	  		$worksheet1->write_number($j,4,$rpt[$i]['avg_su'],$f6);
//	  		$worksheet1->write_string($j,5,'',$f5);
//	  		$worksheet1->write_number($j,6,$rpt[$i]['avg_open_days'],$f6);
//	  		$worksheet1->write_string($j,7,'',$f5);
//	  		$worksheet1->write_number($j,8,$rpt[$i]['avg_apv_days'],$f6);
	  		$worksheet1->write_string($j,5,'',$f5);
	  		$worksheet1->write_number($j,6,$rpt[$i]['avg_avi_days'],$f6);
	  		$worksheet1->write_string($j,7,'',$f5);
	  		$worksheet1->write_number($j,8,$rpt[$i]['avg_mtl_days'],$f6);
	  		$worksheet1->write_string($j,9,'',$f5);
	  		$worksheet1->write_number($j,10,$rpt[$i]['avg_smpl_days'],$f6);

	  		$j=$j+2;
	  	}
	  }

  	  $workbook->close();
	break;


//====================================================
case "pdtion_qty_search":
	check_authority('072',"view");
	if (!$PHP_start) $PHP_start = date('Y')."-01-01";
	if (!$PHP_finish) $PHP_finish = $TODAY;
	$rpt=$report->pdtion_qty_search($PHP_factory,$PHP_start,$PHP_finish);  //取出記錄	
	
	$i = 0;

	
	$op['ord_total'] = 0;
	$op['qty_total'] =$op['su_total'] =0;
	for ($i=0; $i< sizeof($rpt); $i++)
	{
		$key_name = $rpt[$i]['days'];
		if(!isset($tmp_rec[$key_name]))
		{
			$tmp_rec[$key_name]['days'] = $rpt[$i]['days'];
			$tmp_rec[$key_name]['sort'] = $tmp_rec[$key_name]['count'] = $tmp_rec[$key_name]['qty'] = $tmp_rec[$key_name]['su'] = 0;
		}
		$tmp_rec[$key_name]['count']++;
		$tmp_rec[$key_name]['sort']++;
		$tmp_rec[$key_name]['qty'] += $rpt[$i]['qty'];
		$tmp_rec[$key_name]['su'] += $rpt[$i]['su'];
		
		$op['ord_total']++;
		$op['qty_total'] =$op['qty_total'] + $rpt[$i]['qty'];
		$op['su_total'] = $op['su_total'] + $rpt[$i]['su'];
	}
	$rpt = array();
	foreach ($tmp_rec as $key => $value)
	{
		$tmp_rec[$key]['su_rate'] = $tmp_rec[$key]['su'] / $op['su_total'] * 100;
		$rpt[] = $tmp_rec[$key];
	}
	$rpt = bubble_sort($rpt);
	
	$op['ord']=$rpt;
	$op['today']=$TODAY;
	$op['fty']=$PHP_factory;
	$op['start']=$PHP_start;
	$op['finish']=$PHP_finish;
	//print_r($op);
	page_display($op,'072', $TPL_RPT_PDTION_QTY);
break;

	
	
//====================================================
case "tmp_smpl_add":
	$smpl_ord->tmp_smpl_use();
	echo "FINISH";
break;	
	
//====================================================
case "rpt_fab_comp":
	$TPL_RPT_FAB_COMP = "rpt_fab_comp.html";
	$op['fab_chk']=$admin->workmsg('fab_chk');
	page_display($op,'072', $TPL_RPT_FAB_COMP);
break;	
	
	
	
	
	
	
//-------------------------------------------------------------------------

}   // end case ---------

?>
