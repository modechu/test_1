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
#			index.php  主程式
#		for Carnival SCM [Sample]  management
#			Jack Yang     2004/11/10
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//		job 39  [生產製造][生產產能]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

require_once "config.php";
require_once "config.admin.php";

$PHP_SELF = $_SERVER['PHP_SELF'];

$perm = $GLOBALS['power'];

require_once "init.object.php";

$op = array();

$TPL_FCST2_SEARCH_MAIN = 'fcst2_search_main.html';
$TPL_FCST2_SEARCH_TOP = 'fcst2_search_top.html';
$TPL_FCST2_SEARCH_FRAM = 'fcst2_search_fram.html';


switch ($PHP_action) {
//=======================================================
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//	case "sales_forecast":	job 91  業務預算
//	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "forecast":
		check_authority(9,3,"view");

				
		$op['factory'] = $arry2->select($FACTORY,'','PHP_fty','select','');  // creat FTY combo box 	
// creat cust combo box
		$where_str="order by cust_s_name"; //依cust_s_name排序
		$cust_def = $cust->get_fields('cust_init_name',$where_str);
		$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//取出客戶代號
		for ($i=0; $i< sizeof($cust_def); $i++)
		{
			$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//以 [客戶代號-客戶簡稱] 呈現
		}

		$op['cust'] =  $arry2->select($cust_value,'','PHP_cust','select','',$cust_def_vue); 

		$op['year_1'] = $arry2->select($YEAR_WORK,'','PHP_year1','select','');  	
		$op['year_2'] = $arry2->select($YEAR_WORK,'','PHP_year2','select','');  	
		$op['year_3'] = $arry2->select($YEAR_WORK,'','PHP_year3','select','');  	

		$sales_dept_ary = get_sales_dept(); // 取出 業務的部門 [不含K0] ------
		$op['dept'] = $arry2->select($sales_dept_ary,'','PHP_dept','select','');

//080725message增加		
	$note=$notify->search($GLOBALS['SCACHE']['ADMIN']['login_id'], "`read`=0");	
	$op['max_notify'] = $note['max_no'];

		page_display($op, 9, 3, $TPL_FCST2);	    	    
		break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//	case "forecast_add":		job 91A  預算 ADD
//
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "forecast_add":
		check_authority('061',"add");
		// 如果沒選年份時 為今年
		if(!$PHP_year1)   {	$PHP_year1 = $THIS_YEAR;	}

		$parm = array(	"fty"	=>  $PHP_fty,
						"cust"	=>  $PHP_cust,
						"year"	=>	$PHP_year1,
				);

		$op['fty'] = $PHP_fty;
		$op['cust'] = $PHP_cust;
		$op['year'] = $PHP_year1;

		if (!$op['fty']){
			$op['msg'][] = "Error ! you have to select the target Factory !";
		}
		if(!$op['cust']){
			$op['msg'][] = "Error! you have to select one customer !";
		}
		if(!$op['year']){
			$op['msg'][] = "Error! you have to select target YEAR !";
		}

		if (isset($op['msg'])){	
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		//  檢查看看 資料庫是否已經 存在 ??
		   if($c = $fcst2->get(0,$parm,'forecast')){
				$op['msg'][]= 'FY :'.$PHP_year1.'\'s forecast of factory[ '.$PHP_fty.' ] for:[ '.$PHP_cust.' ] is exist already !';
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

		$op['msg'] ='';

		$op['fty'] = $PHP_fty;
		$op['year1'] = $PHP_year1;
		
		if (!$op['fty']){
			$op['msg'][] = "Error ! you have to select the target Factory !";
		}
		if(!$op['year1']){
			$op['msg'][] = "Error! you have to select one target YEAR at least !";
		}

		if ($op['msg']){	
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$op['ft_ary'] = array(1,2,3,4,5,6,7,8,9,10,11,12);
		$layout->assign($op);
		$layout->display($TPL_FCST2_ADD);		    	    
	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//	case "do_forecast_add":		job 91A  預算 ADD
//
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "do_forecast_add":

		check_authority('061',"add");

		$parm = array(	"fty"	=>  $PHP_fty,
						"cust"	=>  $PHP_cust,
						"year"	=>	$PHP_year,
						"method"=>	"forecast",
				);

		$tt_top = $tt_botton = $tt_qty = $tt_fcst = $tt_cm = 0;
		$fcst = array();

		for ($i=1; $i<13; $i++){

			$qty[$i]	= ($PHP_qty[$i] =='') ? 0 : $PHP_qty[$i];
			
//			$top[$i]	= ($PHP_top[$i] =='') ? 0 : $PHP_top[$i];  //上衣
//			$botton[$i]	= ($PHP_botton[$i] =='') ? 0 : $PHP_botton[$i];  //下身
			
			$uprc[$i]	= ($PHP_prc[$i] =='') ? 0 : $PHP_prc[$i];  //單價
			$cm[$i]	= ($PHP_cm[$i] =='') ? 0 : $PHP_cm[$i];   //工繳
			
//			$qty[$i] = $top[$i]*2 + $botton[$i]; //計算數量
			
			$fcst[$i]	= $qty[$i] * $uprc[$i];		//計算收入
		
			$tt_fcst 		+= $fcst[$i];
//			$tt_top			+= $top[$i];
//			$tt_botton	+= $botton[$i];
			$tt_qty			+= $qty[$i];
			$tt_cm 			+= (($cm[$i]/100)*$qty[$i]*$uprc[$i]);
		}

//			$top[13]		= $tt_top;
//			$botton[13]	= $tt_botton;
			$qty[13]		= $tt_qty;
			$fcst[13]		= $tt_fcst;
			$cm[13]			= $tt_cm;
			$uprc[13]		= ($tt_qty !=0) ? number_format($tt_fcst/$tt_qty, 2, '.', '') : 0;

		$parm['uprc'] = Array2csv($uprc);
		$parm['qty'] = Array2csv($qty);
//		$parm['top'] = Array2csv($top);
//		$parm['botton'] = Array2csv($botton);
		$parm['fcst'] = Array2csv($fcst);
		$parm['cm'] = Array2csv($cm);
		//寫入forecast table
		if (!$result = $fcst2->add($parm)) {   
			$op['msg']= $forecast->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		//由forecast table再叫出來 -----------------------
		if (!$f = $fcst2->get($result)) {   
			$op['msg']= $forecast->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		
		$op['fcst'] = $f;

		//將csv改成陣列
			$op['fcst']['q'] = csv2array($f['qty']);
			$op['fcst']['p'] = csv2array($f['uprc']);
			$op['fcst']['f'] = csv2array($f['fcst']);
			$op['fcst']['c'] = csv2array($f['cm']);
//			$op['fcst']['t'] = csv2array($f['top']);
//			$op['fcst']['b'] = csv2array($f['botton']);
			# 記錄使用者動態
		$message = "customer: [".$f['cust']."] in factory:[".$f['fty']."] forecast of year:[".$f['year']."]  done creating";

		$log->log_add(0,"93A",$message);
		$op['msg'][]= $message;
	

		$layout->assign($op);
		$layout->display($TPL_FCST2_SHOW);		    	    
	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//	case "forecast_search_main":		job 91V  預算search
//
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	case "forecast_search":
		$op['fty'] = $PHP_fty;
		$op['yy'] = $PHP_year1;
		$op['cust'] = $PHP_cust;
		page_display($op, '072', $TPL_FCST2_SEARCH_FRAM);
	break;
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//	case "forecast_search_main":		job 91V  預算search
//
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	case "forecast_search_top":
		$op['fty'] = $PHP_fty;
		if(!$PHP_fty) $op['fty'] = 'ALL';
		$op['year'] = $PHP_year1;
		page_display($op, '072', $TPL_FCST2_SEARCH_TOP);
	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//	case "forecast_search_main":		job 91V  預算search
//
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

    case "forecast_search_main":
		check_authority('061',"view");
		//至少要選擇 fty
//		if (!$PHP_fty){
//			$op['msg'][] = "Error ! you have to select the target Factory !";
//		}
		if (!$PHP_year1){
			$PHP_year1 = $THIS_YEAR;
		}

		if (isset($op['msg'])){	
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$parm = array(	"fty"	=>  $PHP_fty,
						"cust"	=>  $PHP_cust,
						"year"	=>	$PHP_year1,
				);

		//定設定 where_str
		$where_str = " WHERE method='forecast' AND year='".$PHP_year1."' ";
		if($PHP_fty) $where_str = $where_str."  AND fty='".$PHP_fty."' ";
		
		//如果沒有選客戶 就全部搜尋
		if($PHP_cust){
			$where_str = $where_str."AND cust='".$PHP_cust."' ";
		}

		//database search data....
		   if(!$op = $fcst2->search(0,$where_str)){
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
		for ($j=0; $j<13; $j++){
			$op['q'][$j] = 0;
			$op['f'][$j] = 0;
			$op['c'][$j] = 0;
	//		$op['t'][$j] = 0;
	//		$op['b'][$j] = 0;

		}

		for ($i=0; $i<$op['max_no']; $i++){

			$op['fcst'][$i]['q'] = csv2array($op['fcst'][$i]['qty']);
			$op['fcst'][$i]['p'] = csv2array($op['fcst'][$i]['uprc']);
			$op['fcst'][$i]['f'] = csv2array($op['fcst'][$i]['fcst']);			
			$op['fcst'][$i]['c'] = csv2array($op['fcst'][$i]['cm']);
			for($j=0; $j<sizeof($op['fcst'][$i]['q']); $j++)
			{
				$op['fcst'][$i]['q'][$j] = $op['fcst'][$i]['q'][$j] /1000;
				$op['fcst'][$i]['f'][$j] = $op['fcst'][$i]['f'][$j] /1000;
//				$op['fcst'][$i]['c'][$j] = $op['fcst'][$i]['c'][$j] /1000;
			}
			
			//加總全部的客戶預算---
			for ($j=0; $j<13; $j++){

				$op['q'][$j] = $op['q'][$j] + $op['fcst'][$i]['q'][$j];
				$op['f'][$j] = $op['f'][$j] + $op['fcst'][$i]['f'][$j];
				if($j==12)
				{
					$op['c'][$j] = $op['c'][$j] + $op['fcst'][$i]['c'][$j] / 1000;
				}else{
					$op['c'][$j] = $op['c'][$j] + (($op['fcst'][$i]['c'][$j]/100)*$op['fcst'][$i]['f'][$j]);
				}
			}
		}


		//如果搜尋資料庫錄數為 0 時
		if (!$op['max_no']){
			$op['msg'][] = "sorry ! current database found nothig from your request !";

		}

		$op['fty'] = $PHP_fty;
		if(!$PHP_fty) $op['fty'] = 'ALL';
		$op['year'] = $PHP_year1;

		page_display($op, '061', $TPL_FCST2_SEARCH_MAIN);	    	    
	break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//	case "forecast_update":		job 91E  預算更新
//
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "forecast_update":
		check_authority('061',"edit");
		// 如果沒選年份時 為今年
		if(!$PHP_year1)   {	$PHP_year1 = $THIS_YEAR;	}

		$parm = array(	"fty"	=>  $PHP_fty,
						"cust"	=>  $PHP_cust,
						"year"	=>	$PHP_year1,
				);

		$op['fty'] = $PHP_fty;
		$op['cust'] = $PHP_cust;
		$op['year'] = $PHP_year1;

		if (!$op['fty']){
			$op['msg'][] = "Error ! you have to select the target Factory !";
		}
		if(!$op['cust']){
			$op['msg'][] = "Error! you have to select one customer !";
		}
		if(!$op['year']){
			$op['msg'][] = "Error! you have to select target YEAR !";
		}

		if (isset($op['msg'])){	
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		//  檢查看看 資料庫是否 存在 ??
		   if(!$c = $fcst2->get(0,$parm,'forecast')){
				$op['msg'][]= 'FY :'.$PHP_year1.'\'s forecast of factory[ '.$PHP_fty.' ] for:[ '.$PHP_cust.' ] is NOT EXIST in database !';
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

			$op['qty'] = csv2array($c['qty']);
			$op['prc'] = csv2array($c['uprc']);
			$op['cm'] = csv2array($c['cm']);
//			$op['top'] = csv2array($c['top']);
//			$op['botton'] = csv2array($c['botton']);

			$op['id'] = $c['id'];


		$layout->assign($op);
		$layout->display($TPL_FCST2_UPDATE);		    	    
	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//	case "do_forecast_update":		job 91E  預算更新
//
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "do_forecast_update":

		check_authority('061',"edit");
		$parm = array(	"fty"	=>  $PHP_fty,
						"cust"	=>  $PHP_cust,
						"year"	=>	$PHP_year,
						"id"	=>	$PHP_id,
						"method"=>	"forecast",
				);

		$tt_top = $tt_botton = $tt_qty = $tt_fcst = $tt_cm = 0;
		$fcst = array();

		for ($i=1; $i<13; $i++){

			$qty[$i]	= ($PHP_qty[$i] =='') ? 0 : $PHP_qty[$i];

//			$top[$i]	= ($PHP_top[$i] =='') ? 0 : $PHP_top[$i];
//			$botton[$i]	= ($PHP_botton[$i] =='') ? 0 : $PHP_botton[$i];


			$uprc[$i]	= ($PHP_prc[$i] =='') ? 0 : $PHP_prc[$i];
			$cm[$i]	  = ($PHP_cm[$i] =='')  ? 0 : $PHP_cm[$i];
			
//			$qty[$i] = $top[$i] * 2 + $botton[$i];
			$fcst[$i]	= $qty[$i] * $uprc[$i];

			$tt_fcst 		+= $fcst[$i];
			$tt_cm  		+= (($cm[$i]/100)*$qty[$i]* $uprc[$i]);
			$tt_qty 		+= $qty[$i];
//			$tt_top 		+= $top[$i];
//			$tt_botton	+= $botton[$i];
		}

			$qty[13]	= $tt_qty;
//			$top[13]	=	$tt_top;
//			$botton[13] = $tt_botton;
			$cm[13] 	= $tt_cm;
			$fcst[13]	= $tt_fcst;
			$uprc[13]	= ($tt_qty !=0) ? number_format($tt_fcst/$tt_qty, 2, '.', '') : 0;

		$parm['uprc']		= Array2csv($uprc);
		$parm['qty']		= Array2csv($qty);
		$parm['fcst']		= Array2csv($fcst);
		$parm['cm']			= Array2csv($cm);
//		$parm['top']		= Array2csv($top);
//		$parm['botton']	= Array2csv($botton);
		
		//寫入forecast table
		if (!$result = $fcst2->edit($parm)) {   
			$op['msg']= $forecast->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		//由forecast table再叫出來 -----------------------
		if (!$f = $fcst2->get($result)) {   
			$op['msg']= $forecast->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		
		$op['fcst'] = $f;

		//將csv改成陣列
			$op['fcst']['q'] = csv2array($f['qty']);
			$op['fcst']['p'] = csv2array($f['uprc']);
			$op['fcst']['f'] = csv2array($f['fcst']);
			$op['fcst']['c'] = csv2array($f['cm']);
//			$op['fcst']['t'] = csv2array($f['top']);
//			$op['fcst']['b'] = csv2array($f['botton']);
			# 記錄使用者動態
		$message = "customer: [".$f['cust']."] in factory:[".$f['fty']."] forecast of year:[".$f['year']."]  done UPDATE";

		$log->log_add(0,"93E",$message);
		$op['msg'][]= $message;

		$layout->assign($op);
		$layout->display($TPL_FCST2_SHOW);		    	    
	break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//	case "forecast_search":		job 91V  預算search
//
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "fcst_rpt":
		check_authority('061',"view");
		//至少要選擇 fty
		if (!$PHP_fty){
			$op['msg'][] = "Error ! you have to select the target Factory !";
		}
		if (!$PHP_year1){
			$PHP_year1 = $THIS_YEAR;
		}

		if (isset($op['msg'])){	
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}


		//定設定 where_str
		$where_str = " WHERE method='forecast' AND fty='".$PHP_fty."' AND year='".$PHP_year1."' ";
		
		//如果沒有選客戶 就全部搜尋
		if($PHP_dept){
			$where_str = $where_str."AND dept='".$PHP_dept."' ";
		}

		//database search data....
		   if(!$fcst = $fcst2->search(0,$where_str)){
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
		for ($j=0; $j<12; $j++){
			$op['f'][$j] = 0;
		}
		$op['f_ttl'] = 0;
		
		for ($i=0; $i<sizeof($fcst['fcst']); $i++){
			
			$ff = csv2array($fcst['fcst'][$i]['fcst']);			
			
			//加總全部的客戶預算---
			for ($j=0; $j<12; $j++){
				$op['f'][$j] = $op['f'][$j] + $ff[$j];
			}
			$op['f_ttl'] +=  $ff[12];
		}


		//如果搜尋資料庫錄數為 0 時
		if (sizeof($fcst['fcst']) == 0){
			$op['msg'][] = "sorry ! current database found nothig from your request !";

		}
		$where_str = '';
		if($PHP_dept){
			$where_str = " AND s_order.dept='".$PHP_dept."' ";
		}
		foreach($MM2 as $key => $value )
		{
//			$where_str = $where_str." AND s_order.dept <> s_order.factory";
			$op['ord'][] = $fcst2->get_ord_fob_mm($PHP_fty, $PHP_year1."-".$key, $where_str);
			$op['shp'][] = $fcst2->get_shp_fob_mm($PHP_fty, $PHP_year1."-".$key, $where_str);
			$op['mm_ary'][] = $value;
			$op['mm_num'][] = $key;
		}
		$op['ord_ttl'] = $op['shp_ttl'] = $op['dif_ord_ttl'] = $op['dif_shp_ttl'] = 0;
		$ord = $shp = array();
		for($i=0; $i<12; $i++)
		{
			$op['ord_ttl'] +=$op['ord'][$i];
			$op['shp_ttl'] +=$op['shp'][$i];
			
//			if( $op['ord'][$i] > 0 || ( $i<11 && $op['ord'][$i] == 0  && $op['ord'][($i+1)] > 0)) $ord[] = $op['ord'][$i];
//			if( $op['shp'][$i] > 0 || ( $i<11 && $op['shp'][$i] == 0 && $op['shp'][($i+1)] > 0)) $shp[] = $op['shp'][$i];
			
			$op['dif_ord'][$i] = $op['f'][$i] - $op['ord'][$i];
			$op['dif_shp'][$i] = $op['f'][$i] - $op['shp'][$i];
			
			$op['dif_ord_ttl'] += $op['dif_ord'][$i];
			$op['dif_shp_ttl'] += $op['dif_shp'][$i];
		}
		$dd = date('m');
		//$dd = int($dd);
		for($i=0; $i<12; $i++)
		{
			if($i > 0 && isset($op['ord'][$i]) && $op['ord'][$i] > 0)$ord[] = $op['ord'][$i] += $op['ord'][($i-1)];
			if($i > 0 && $i < $dd)$shp[] = $op['shp'][$i] += $op['shp'][($i-1)];
			if($i > 0 && isset($op['f'][$i]))$op['f'][$i] += $op['f'][($i-1)];
		}		

		$op['fty'] = $PHP_fty;
		$op['year'] = $PHP_year1;
		$op['dept'] = $PHP_dept;
		
		
		//引入 graph class
		include_once($config['root_dir']."/lib/src/jpgraph.php");
		include_once($config['root_dir']."/lib/src/jpgraph_line.php");

		include ($config['root_dir']."/lib/src/jpgraph_bar.php");

		$graphic_title = " FTY: ".$PHP_fty." Order Z Chart for month: ".$PHP_year1;
	  $mm=$op['mm_ary'];
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


		// setup capacity plot
		$capaplot = new LinePlot($op['f']);
		$capaplot->mark->SetType(MARK_UTRIANGLE);
		$capaplot->SetWeight(1.5);
		$capaplot->SetColor('navy');
		$capaplot->SetLegend('capacity '.number_format($op['f_ttl']).' SU');
		$graph->Add($capaplot);



		// setup etp plot
		$etpplot = new LinePlot($ord);
		$etpplot->SetColor('teal');
		$etpplot->mark->SetType(MARK_STAR);
		$etpplot->mark->SetFillColor("teal");
		$etpplot->SetWeight(1.5);
		
		$etpplot->SetLegend('Order '.number_format($op['ord_ttl']).' SU');
		$graph->Add($etpplot);

		// setup schedule plot
		$schdplot = new LinePlot($shp);
		$schdplot->SetWeight(1.5);
		$schdplot->SetColor('darkred');
		$schdplot->mark->SetType(MARK_FILLEDCIRCLE);
		$schdplot->mark->SetFillColor("darkred");

		$schdplot->SetLegend('schedule '.number_format($op['shp_ttl']).' SU');
		$graph->Add($schdplot);

		$graph->legend->SetShadow('gray@0.4',5);
		$graph->legend->Pos(0.25,0.3,"center","center");
		$graph->title->Set($graphic_title);
		$graph->title->SetFont(FF_FONT1,FS_BOLD,8);

		$op['echart_1'] = $graph->Stroke('picture/fcst2.png');		
		
		
		page_display($op, '061', $TPL_FCST2_RPT);	    	    
	break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//	case "ord_mm":		
//
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "ord_mm":
  	$where_str = '';
		if($PHP_dept){
			$where_str = $where_str."AND dept='".$PHP_dept."' ";
		}
		//$where_str = $where_str." AND s_order.dept <> s_order.factory";
		$op['ord'] = $fcst2->search_etp_ord($PHP_fty,$PHP_year."-".$PHP_month,500, $where_str);
		$op['qty_ttl'] = $op['fob_ttl'] = $op['gm_ttl'] = 0;
		for($i=0; $i<sizeof($op['ord']); $i++)
		{
			$op['qty_ttl'] +=$op['ord'][$i]['qty'];
			$op['fob_ttl'] +=$op['ord'][$i]['sales'];
			$op['gm_ttl'] +=$op['ord'][$i]['gm'];
		}

		$op['fty'] = $PHP_fty;
		$op['year'] = $PHP_year;
		$op['dept'] = $PHP_dept;
		$op['month'] = $PHP_month;
	page_display($op, '061', $TPL_FCST2_RPT_ORD);	    	    
	break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//	case "ord_mm":		
//
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  case "ship_mm":
  	$where_str = '';
		if($PHP_dept){
			$where_str = $where_str."AND dept='".$PHP_dept."' ";
		}
		//$where_str = $where_str." AND s_order.dept <> s_order.factory";
		$op['ord'] = $fcst2->search_ship_mm($PHP_fty,$PHP_year."-".$PHP_month, $where_str);
		$op['qty_ttl'] = $op['fob_ttl'] = $op['gm_ttl'] = $op['ship_fob_ttl'] = $op['ship_gm_ttl'] = $op['dif_fob_ttl'] = $op['dif_gm_ttl'] =0;
		for($i=0; $i<sizeof($op['ord']); $i++)
		{
				//補足工繳及物料成本

			
			if($op['ord'][$i]['rel_cm_cost'] == 0 && $op['ord'][$i]['dept'] != $op['ord'][$i]['factory'])$op['ord'][$i]['rel_cm_cost'] = $cost->ord_cm_cost_num($op['ord'][$i]['order_num']);
			if($op['ord'][$i]['rel_mat_cost'] == 0)
			{
				$mat_date = $order->get_field_value('mat_shp','',$op['ord'][$i]['order_num'],'pdtion');

			

				if($mat_date && $mat_date <> '0000-00-00' && $op['ord'][$i]['dept'] != $op['ord'][$i]['factory'])
				{
					$op['ord'][$i]['rel_mat_cost'] = $receive->add_ord_cost($op['ord'][$i]['order_num'],'l');
//						echo "here".$op['ord'][$i]['order_num']."==>".$mat_date."==>".$op['ord'][$i]['rel_mat_cost']."<br>";
				}else{
					if($op['ord'][$i]['po_mat_cost'] == 0 && $op['ord'][$i]['dept'] != $op['ord'][$i]['factory'])
					{
						$wi_rec = $wi->get_field($op['ord'][$i]['order_num'],'id');
						$lots_ap = $bom->get_aply_quck($wi_rec['id'], 'bom_lots');	
						if($lots_ap > 0)$op['ord'][$i]['po_mat_cost'] = $po->add_lots_cost($op['ord'][$i]['order_num'],$wi_rec['id']);							
					}
					$op['ord'][$i]['rel_mat_cost'] = $op['ord'][$i]['po_mat_cost'];
				}
			}
			if($op['ord'][$i]['rel_acc_cost'] == 0)
			{
				$macc_date = $order->get_field_value('m_acc_shp','',$op['ord'][$i]['order_num'],'pdtion');
				$acc_date = $order->get_field_value('acc_shp','',$op['ord'][$i]['order_num'],'pdtion');
				if(($macc_date && $macc_date <> '0000-00-00') && ($acc_date && $acc_date <> '0000-00-00') && $op['ord'][$i]['dept'] != $op['ord'][$i]['factory'])			
				{
					$op['ord'][$i]['rel_acc_cost'] = $receive->add_ord_cost($op['ord'][$i]['order_num'],'a');
				}else{
					if($op['ord'][$i]['po_acc_cost'] == 0 && $op['ord'][$i]['dept'] != $op['ord'][$i]['factory'])
					{
						$wi_rec = $wi->get_field($op['ord'][$i]['order_num'],'id');
						$acc_ap = $bom->get_aply_quck($wi_rec['id'], 'bom_acc');	
						if($acc_ap > 0)$op['ord'][$i]['po_acc_cost'] = $po->add_acc_cost($op['ord'][$i]['order_num'],$wi_rec['id']);							
					}				
					$op['ord'][$i]['rel_acc_cost'] = $op['ord'][$i]['po_acc_cost'];
				}				
			}			
		
		 $op['ord'][$i]['ship_cost'] = $op['ord'][$i]['rel_cm_cost'] + $op['ord'][$i]['rel_mat_cost'] + $op['ord'][$i]['rel_acc_cost'];
		 if($op['ord'][$i]['dept'] != $PHP_fty  && ($op['ord'][$i]['rel_mat_cost'] == 0 || $op['ord'][$i]['rel_acc_cost'] == 0)) 
		 {
//		 		echo $op['ord'][$i]['dept']."==>".$op['ord'][$i]['order_num']."<br>";
		 		$op['ord'][$i]['ship_cost'] = 0;
			}
		 $op['ord'][$i]['ship_cost'] *= ($op['ord'][$i]['ship_qty'] / $op['ord'][$i]['done_ship']);
		 $op['ord'][$i]['ship_gm'] = $op['ord'][$i]['ship_sales'] - $op['ord'][$i]['ship_cost'];
			
		 $op['ord'][$i]['sales']	*= ($op['ord'][$i]['ship_qty'] / $op['ord'][$i]['done_ship']);
		 $op['ord'][$i]['gm']	*= ($op['ord'][$i]['ship_qty'] / $op['ord'][$i]['done_ship']);
			
			$op['ord'][$i]['dif_gm'] = Number_format($op['ord'][$i]['ship_gm'] - $op['ord'][$i]['gm'],2,'.','');
			$op['ord'][$i]['dif_sales'] = Number_format($op['ord'][$i]['ship_sales'] - $op['ord'][$i]['sales'],2,'.','');
			
			$op['qty_ttl'] +=$op['ord'][$i]['ship_qty'];
			$op['fob_ttl'] +=$op['ord'][$i]['sales'];
			$op['ship_fob_ttl'] +=$op['ord'][$i]['ship_sales'];
			$op['dif_fob_ttl'] +=$op['ord'][$i]['dif_sales'];
			$op['gm_ttl'] +=$op['ord'][$i]['gm'];
			$op['ship_gm_ttl'] +=$op['ord'][$i]['ship_gm'];
			$op['dif_gm_ttl'] +=$op['ord'][$i]['dif_gm'];
	
		}

		$op['fty'] = $PHP_fty;
		$op['year'] = $PHP_year;
		$op['dept'] = $PHP_dept;
		$op['month'] = $PHP_month;
	page_display($op, '061', $TPL_FCST2_RPT_SHIP);	    	    
	break;






//-------------------------------------------------------------------------

}   // end case ---------

?>
