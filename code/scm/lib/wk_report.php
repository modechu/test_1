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


switch ($PHP_action) {
//=======================================================

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "fab_comp":	  //搜尋未完成主料預估用量
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "wk_report":
 	check_authority(10,1,"view");

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

		$where_str = "AND s_order.line_sex = 'M'";
		$f1 = $order->get_one_etd_ord('HJ', $yymm,$where_str);	
		echo "<br><br>HJ ".$mon_ary[$i]."(ETD) MAN Q'TY : ".number_format($f1);

		$where_str = "AND s_order.line_sex = 'F'";
		$f1 = $order->get_one_etd_ord('HJ', $yymm,$where_str);	
		echo "<br><br>HJ ".$mon_ary[$i]."(ETD) Girl Q'TY : ".number_format($f1);
		
		$f1 = $order->get_one_etd_ord('HJ', $yymm);	
		echo "<br><br>HJ ".$mon_ary[$i]."(ETD) Q'TY : ".number_format($f1);

		$yymm = ($this_yy+$year_ary[$i])."-".$mon_ary[$i];
		$f1 = $order->get_one_etd_ord('LY', $yymm);	
		echo "<br><br>LY ".$mon_ary[$i]."(ETD) Q'TY : ".number_format($f1);


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

		$f1 = $order->get_one_etd_unout('LY', $yymm);	
		echo "<br>LY ".$mon_ary[$i]."(ETD) UN-OUTPUT Q'TY : ".number_format($f1);
		if($mon_ary[$i] <= $this_mm) $ly_un_out+=$f1;
	}
	echo "<br><br>HJ until this month MAN unout-put ".number_format($hj_un_m_out);	
	echo "<br><br>HJ until this month Girl unout-put ".number_format($hj_un_g_out);	

	echo "<br><br>HJ until this month unout-put ".number_format($hj_un_out);	

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
	echo "<br><br>LY MAN available production : ".number_format($rptdo_un_su)	;		
	
	
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
	check_authority(10,8,"view");
	
//	$PHP_year1 = date('Y');

	$op['full_cl'] = $report->smpl_fty_full_search($PHP_year1,'CL');
	$op['ttl_full_cl']['qty'] = $op['ttl_full_cl']['done'] = $op['ttl_full_cl']['remain'] = $op['ttl_full_cl']['num'] = 0;
	$op['ttl_full_ly']['qty'] = $op['ttl_full_ly']['done'] = $op['ttl_full_ly']['remain'] = $op['ttl_full_ly']['num'] = 0	;
	$op['ttl_full_wx']['qty'] = $op['ttl_full_wx']['done'] = $op['ttl_full_wx']['remain'] = $op['ttl_full_wx']['num'] = 0;
	$op['ttl_full_hj']['qty'] = $op['ttl_full_hj']['done'] = $op['ttl_full_hj']['remain'] = $op['ttl_full_hj']['num'] = 0;
	$op['ttl_full']['qty'] = $op['ttl_full']['done'] = $op['ttl_full']['remain'] = $op['ttl_full']['num'] = 0;

	for($i=0; $i<sizeof($op['full_cl']); $i++)
	{
		$op['ttl_full_cl']['qty'] += $op['full_cl'][$i]['qty'];
		$op['ttl_full_cl']['done'] += $op['full_cl'][$i]['done'];
		$op['ttl_full_cl']['remain'] += $op['full_cl'][$i]['remain'];
		$op['ttl_full_cl']['num'] += $op['full_cl'][$i]['num'];

		$op['ttl_full']['qty'] += $op['full_cl'][$i]['qty'];
		$op['ttl_full']['done'] += $op['full_cl'][$i]['done'];
		$op['ttl_full']['remain'] += $op['full_cl'][$i]['remain'];
		$op['ttl_full']['num'] += $op['full_cl'][$i]['num'];


	}
	$op['full_wx'] = $report->smpl_fty_full_search($PHP_year1,'WX');

	for($i=0; $i<sizeof($op['full_wx']); $i++)
	{
		$op['ttl_full_wx']['qty'] += $op['full_wx'][$i]['qty'];
		$op['ttl_full_wx']['done'] += $op['full_wx'][$i]['done'];
		$op['ttl_full_wx']['remain'] += $op['full_wx'][$i]['remain'];
		$op['ttl_full_wx']['num'] += $op['full_wx'][$i]['num'];

		$op['ttl_full']['qty'] += $op['full_wx'][$i]['qty'];
		$op['ttl_full']['done'] += $op['full_wx'][$i]['done'];
		$op['ttl_full']['remain'] += $op['full_wx'][$i]['remain'];
		$op['ttl_full']['num'] += $op['full_wx'][$i]['num'];

	}	
	$op['full_hj'] = $report->smpl_fty_full_search($PHP_year1,'HJ');

	for($i=0; $i<sizeof($op['full_hj']); $i++)
	{
		$op['ttl_full_hj']['qty'] += $op['full_hj'][$i]['qty'];
		$op['ttl_full_hj']['done'] += $op['full_hj'][$i]['done'];
		$op['ttl_full_hj']['remain'] += $op['full_hj'][$i]['remain'];
		$op['ttl_full_hj']['num'] += $op['full_hj'][$i]['num'];

		$op['ttl_full']['qty'] += $op['full_hj'][$i]['qty'];
		$op['ttl_full']['done'] += $op['full_hj'][$i]['done'];
		$op['ttl_full']['remain'] += $op['full_hj'][$i]['remain'];
		$op['ttl_full']['num'] += $op['full_hj'][$i]['num'];

	}
	$op['full_ly'] = $report->smpl_fty_full_search($PHP_year1,'LY');
	
	for($i=0; $i<sizeof($op['full_ly']); $i++)
	{
		$op['ttl_full_ly']['qty'] += $op['full_ly'][$i]['qty'];
		$op['ttl_full_ly']['done'] += $op['full_ly'][$i]['done'];
		$op['ttl_full_ly']['remain'] += $op['full_ly'][$i]['remain'];
		$op['ttl_full_ly']['num'] += $op['full_ly'][$i]['num'];

		$op['ttl_full']['qty'] += $op['full_ly'][$i]['qty'];
		$op['ttl_full']['done'] += $op['full_ly'][$i]['done'];
		$op['ttl_full']['remain'] += $op['full_ly'][$i]['remain'];
		$op['ttl_full']['num'] += $op['full_ly'][$i]['num'];

	}
	
	$rpt_cl=$report->smpl_fty_search($PHP_year1,'CL');  //取出記錄

/*
	$tmp_sort = array();
	$tmp_mm = '';
	for($i=0; $i<sizeof($tmp); $i++)
	{
			$etd = explode('-',$tmp[$i]['etd']);
			$mm = substr($etd[0],2).'-'.$etd[1];

			if($tmp_mm == '') $tmp_mm = $mm;
			if($tmp_mm != '' && $tmp_mm != $mm )
			{
				$tmp_sort=bubble_sort($tmp_sort);
				for($j=0; $j<sizeof($tmp_sort); $j++) $rpt[] = $tmp_sort[$j];
				$tmp_sort = array();
			}
			$tmp[$i]['sort'] = $tmp[$i]['schd_pttn'];
			$tmp_sort[] = $tmp[$i];
			
	}
	$tmp_sort=bubble_sort($tmp_sort);

	
	
	for($j=0; $j<sizeof($tmp_sort); $j++) $rpt[] = $tmp_sort[$j];
*/	

		$rpt_cl = $report->sort_by_etf($rpt_cl);

		$qty = $num = $done  = $rem = 0;
		$ttl['qty'] = $ttl['num'] = $ttl['done'] = $ttl['rem'] = 0;
		$tmp_mm = '';
		for($i=0; $i<sizeof($rpt_cl); $i++)
		{			 
			$tmp = explode('-',$rpt_cl[$i]['etd']);
			$mm = substr($tmp[0],2).'-'.$tmp[1];
			if($tmp_mm == '') $tmp_mm = $mm;
			if($tmp_mm != '' && $tmp_mm != $mm )
			{
				$rpt_cl[$i]['s_qty'] = $qty;
				$rpt_cl[$i]['s_rem'] = $rem;
				$rpt_cl[$i]['s_done'] = $done;
				$rpt_cl[$i]['s_num'] = $num;
				$rpt_cl[$i]['s_mm'] = $tmp_mm;
				$rpt_cl[$i]['tb_mk'] = 1;
				$tmp_mm = $mm;
				$qty = $num = $done  = $rem = 0;
			}
			$rpt_cl[$i]['rem'] = 0;
			if($rpt_cl[$i]['qty'] > $rpt_cl[$i]['qty_done']) $rpt_cl[$i]['rem'] = $rpt_cl[$i]['qty'] - $rpt_cl[$i]['qty_done'];
			$num++;
			$rem += $rpt_cl[$i]['rem'];
			$done += $rpt_cl[$i]['qty_done'];
			$qty += $rpt_cl[$i]['qty'];

			$ttl['num']++;
			$ttl['rem'] += $rpt_cl[$i]['rem'];
			$ttl['done'] += $rpt_cl[$i]['qty_done'];
			$ttl['qty'] += $rpt_cl[$i]['qty'];			
		}
		
		$rpt_cl[$i]['s_qty'] = $qty;
		$rpt_cl[$i]['s_rem'] = $rem;
		$rpt_cl[$i]['s_done'] = $done;
		$rpt_cl[$i]['s_num'] = $num;
		$rpt_cl[$i]['s_mm'] = $tmp_mm;
		$rpt_cl[$i]['tb_mk'] = 1;
		
		
		
	$op['rpt_cl']=$rpt_cl;
	$op['ttl_cl']=$ttl;
		

// 工廠  -- WX  --

	$rpt_wx=$report->smpl_fty_search($PHP_year1,'WX');  //取出記錄
	$rpt_wx = $report->sort_by_etf($rpt_wx);
		$qty = $num = $done  = $rem = 0;
		$ttl['qty'] = $ttl['num'] = $ttl['done'] = $ttl['rem'] = 0;
		$tmp_mm = '';
		for($i=0; $i<sizeof($rpt_wx); $i++)
		{			 
			$tmp = explode('-',$rpt_wx[$i]['etd']);
			$mm = substr($tmp[0],2).'-'.$tmp[1];
			if($tmp_mm == '') $tmp_mm = $mm;
			if($tmp_mm != '' && $tmp_mm != $mm )
			{
				$rpt_wx[$i]['s_qty'] = $qty;
				$rpt_wx[$i]['s_rem'] = $rem;
				$rpt_wx[$i]['s_done'] = $done;
				$rpt_wx[$i]['s_num'] = $num;
				$rpt_wx[$i]['s_mm'] = $tmp_mm;
				$rpt_wx[$i]['tb_mk'] = 1;
				$tmp_mm = $mm;
				$qty = $num = $done  = $rem = 0;
			}
			$rpt_wx[$i]['rem'] = 0;
			if($rpt_wx[$i]['qty'] > $rpt_wx[$i]['qty_done']) $rpt_wx[$i]['rem'] = $rpt_wx[$i]['qty'] - $rpt_wx[$i]['qty_done'];
			$num++;
			$rem += $rpt_wx[$i]['rem'];
			$done += $rpt_wx[$i]['qty_done'];
			$qty += $rpt_wx[$i]['qty'];

			$ttl['num']++;
			$ttl['rem'] += $rpt_wx[$i]['rem'];
			$ttl['done'] += $rpt_wx[$i]['qty_done'];
			$ttl['qty'] += $rpt_wx[$i]['qty'];			
		}
		
		$rpt_wx[$i]['s_qty'] = $qty;
		$rpt_wx[$i]['s_rem'] = $rem;
		$rpt_wx[$i]['s_done'] = $done;
		$rpt_wx[$i]['s_num'] = $num;
		$rpt_wx[$i]['s_mm'] = $tmp_mm;
		$rpt_wx[$i]['tb_mk'] = 1;

	$op['rpt_wx']=$rpt_wx;
	$op['ttl_wx']=$ttl;
	
	
// 工廠  -- HJ  --

	$rpt_hj=$report->smpl_fty_search($PHP_year1,'HJ');  //取出記錄
	$rpt_hj = $report->sort_by_etf($rpt_hj);
		$qty = $num = $done  = $rem = 0;
		$ttl['qty'] = $ttl['num'] = $ttl['done'] = $ttl['rem'] = 0;
		$tmp_mm = '';
		for($i=0; $i<sizeof($rpt_hj); $i++)
		{			 
			$tmp = explode('-',$rpt_hj[$i]['etd']);
			$mm = substr($tmp[0],2).'-'.$tmp[1];
			if($tmp_mm == '') $tmp_mm = $mm;
			if($tmp_mm != '' && $tmp_mm != $mm )
			{
				$rpt_hj[$i]['s_qty'] = $qty;
				$rpt_hj[$i]['s_rem'] = $rem;
				$rpt_hj[$i]['s_done'] = $done;
				$rpt_hj[$i]['s_num'] = $num;
				$rpt_hj[$i]['s_mm'] = $tmp_mm;
				$rpt_hj[$i]['tb_mk'] = 1;
				$tmp_mm = $mm;
				$qty = $num = $done  = $rem = 0;
			}
			$rpt_hj[$i]['rem'] = 0;
			if($rpt_hj[$i]['qty'] > $rpt_hj[$i]['qty_done']) $rpt_hj[$i]['rem'] = $rpt_hj[$i]['qty'] - $rpt_hj[$i]['qty_done'];
			$num++;
			$rem += $rpt_hj[$i]['rem'];
			$done += $rpt_hj[$i]['qty_done'];
			$qty += $rpt_hj[$i]['qty'];

			$ttl['num']++;
			$ttl['rem'] += $rpt_hj[$i]['rem'];
			$ttl['done'] += $rpt_hj[$i]['qty_done'];
			$ttl['qty'] += $rpt_hj[$i]['qty'];			
		}
		
		$rpt_hj[$i]['s_qty'] = $qty;
		$rpt_hj[$i]['s_rem'] = $rem;
		$rpt_hj[$i]['s_done'] = $done;
		$rpt_hj[$i]['s_num'] = $num;
		$rpt_hj[$i]['s_mm'] = $tmp_mm;
		$rpt_hj[$i]['tb_mk'] = 1;

	$op['rpt_hj']=$rpt_hj;
	$op['ttl_hj']=$ttl;


// 工廠  -- LY  --
	$rpt_ly=$report->smpl_fty_search($PHP_year1,'LY');  //取出記錄
	$rpt_ly = $report->sort_by_etf($rpt_ly);
		$qty = $num = $done  = $rem = 0;
		$ttl['qty'] = $ttl['num'] = $ttl['done'] = $ttl['rem'] = 0;
		$tmp_mm = '';
		for($i=0; $i<sizeof($rpt_ly); $i++)
		{			 
			$tmp = explode('-',$rpt_ly[$i]['etd']);
			$mm = substr($tmp[0],2).'-'.$tmp[1];
			if($tmp_mm == '') $tmp_mm = $mm;
			if($tmp_mm != '' && $tmp_mm != $mm )
			{
				$rpt_ly[$i]['s_qty'] = $qty;
				$rpt_ly[$i]['s_rem'] = $rem;
				$rpt_ly[$i]['s_done'] = $done;
				$rpt_ly[$i]['s_num'] = $num;
				$rpt_ly[$i]['s_mm'] = $tmp_mm;
				$rpt_ly[$i]['tb_mk'] = 1;
				$tmp_mm = $mm;
				$qty = $num = $done  = $rem = 0;
			}
			$rpt_ly[$i]['rem'] = 0;
			if($rpt_ly[$i]['qty'] > $rpt_ly[$i]['qty_done']) $rpt_ly[$i]['rem'] = $rpt_ly[$i]['qty'] - $rpt_ly[$i]['qty_done'];
			$num++;
			$rem += $rpt_ly[$i]['rem'];
			$done += $rpt_ly[$i]['qty_done'];
			$qty += $rpt_ly[$i]['qty'];

			$ttl['num']++;
			$ttl['rem'] += $rpt_ly[$i]['rem'];
			$ttl['done'] += $rpt_ly[$i]['qty_done'];
			$ttl['qty'] += $rpt_ly[$i]['qty'];			
		}
		
		$rpt_ly[$i]['s_qty'] = $qty;
		$rpt_ly[$i]['s_rem'] = $rem;
		$rpt_ly[$i]['s_done'] = $done;
		$rpt_ly[$i]['s_num'] = $num;
		$rpt_ly[$i]['s_mm'] = $tmp_mm;
		$rpt_ly[$i]['tb_mk'] = 1;

	$op['rpt_ly']=$rpt_ly;
	$op['ttl_ly']=$ttl;


	$op['ttl']['num'] = $op['ttl_cl']['num']+$op['ttl_wx']['num']+$op['ttl_hj']['num']+$op['ttl_ly']['num'];
	$op['ttl']['rem'] = $op['ttl_cl']['rem']+$op['ttl_wx']['rem']+$op['ttl_hj']['rem']+$op['ttl_ly']['rem'];	
	$op['ttl']['qty'] = $op['ttl_cl']['qty']+$op['ttl_wx']['qty']+$op['ttl_hj']['qty']+$op['ttl_ly']['qty'];	
	$op['ttl']['done'] = $op['ttl_cl']['done']+$op['ttl_wx']['done']+$op['ttl_hj']['done']+$op['ttl_ly']['done'];	
	
	
	$op['year'] = date('Y');
	$op['fty'] = $PHP_fty;
	$op['sch_year'] = $PHP_year1;
	page_display($op, 10,8, $TPL_RPT_SMPL_FTY);
break;






	
//-------------------------------------------------------------------------

}   // end case ---------

?>
