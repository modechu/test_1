<?php
session_start();

require_once "config.php";
require_once "config.admin.php";
require_once "init.object.php";
include_once($config['root_dir']."/lib/production_analysis.class.php");

$PRODUCTION_ANALYSIS = new PRODUCTION_ANALYSIS();
if (!$PRODUCTION_ANALYSIS->init($mysql,"log")) { print "error!! cannot initialize database for PRODUCTION_ANALYSIS class"; exit; }

$PHP_SELF = $_SERVER['PHP_SELF'];

$op = array();

$PHP_action = !empty($PHP_action) ? $PHP_action : '';
// echo $PHP_action;

$auth = '101';

switch ($PHP_action) {
//=======================================================

case "main":
check_authority($auth,"view");

$op['css'] = array( 'css/scm.css' , 'js/calendar/css/jscal2.css' , 'js/calendar/css/border-radius.css' , 'js/calendar/css/gold/gold.css' );
$op['js'] = array( 'js/jquery.min.js' , 'js/jquery.blockUI.js' , 'js/calendar/js/jscal2.js' , 'js/calendar/js/lang/en.js' , 'js/scm.js' );

// creat cust combo box
$op['factory'] = $arry2->select($FACTORY,'','PHP_fty','select','');  	
$op['year'] = $arry2->select($YEAR_WORK,$GLOBALS['THIS_YEAR'],'PHP_year','select','');  
$op['month'] = $arry2->select($MONTH_WORK,date('m'),'PHP_month','select','');	
$op['TODAY'] = $GLOBALS['TODAY'];	

$op['msg'] = $order->msg->get(2);

// message 
$note=$notify->search($GLOBALS['SCACHE']['ADMIN']['login_id'], "`read`=0");
$op['max_notify'] = $note['max_no'];

page_display($op,$auth,'production_analysis_main.html');    	    
break;



case "production_analysis_view_year":
check_authority($auth,"view");

$op['css'] = array( 'css/scm.css' , 'js/calendar/css/jscal2.css' , 'js/calendar/css/border-radius.css' , 'js/calendar/css/gold/gold.css' );
$op['js'] = array( 'js/jquery.min.js' , 'js/jquery.blockUI.js' , 'js/calendar/js/jscal2.js' , 'js/calendar/js/lang/en.js' , 'js/scm.js' , 'js/production_analysis.js' );

// print_r($_POST);
# GET POST
$op['fty'] = $PHP_fty = !empty($_POST['PHP_fty']) ? $_POST['PHP_fty'] : 'LY' ;
$op['year'] = $PHP_year = !empty($_POST['PHP_year']) ? $_POST['PHP_year'] : date("Y") ;

# 設定起始月
$PHP_month = 1;

# get output
$capaci = $PRODUCTION_ANALYSIS->get_capacity_mm($PHP_fty,$PHP_year);
$capaci_array = $capaci['date'];

$line_output = $PRODUCTION_ANALYSIS->get_line_output($PHP_fty,$PHP_year);
// print_r($line_output);
$op['line_output'] = $line_output;

for ($i=1; $i<13; $i++) {
    $month = str_pad($i,2,'0',STR_PAD_LEFT);
    
    # 月份數字
    $op['mm_name'][$i-1]['key'] = $month;
    # 月份英文
    $op['mm_name'][$i-1]['val'] = $MM2[date('m',mktime(0,0,0,$month,1,$PHP_year))];
    # 年月
    $op['mm_yaer'][$i-1] =date('Y',mktime(0,0,0,$month,1,$PHP_year));
    $op['mm_month'][$i-1] = date('m',mktime(0,0,0,$month,1,$PHP_year));
    
    # capacity
    $op['capacity'][$i-1] = $capaci_array[date('Y',mktime(0,0,0,$month,1,$PHP_year))][date('m',mktime(0,0,0,$month,1,$PHP_year))];
    $op['capacity_sum'] += $op['capacity'][$i-1];

}
// echo getDaysInMonth(1,$PHP_year);
foreach($line_output['row_array'] as $row){
    $su = $row['su'];
    $ets = $row['rel_ets'];
    $etf = $row['rel_etf'];
    $p_day = countDays($ets,$etf)+1;
    $balance_su = $su / $p_day;
    // echo $balance_su.'<br>';
    for ($i=1; $i<13; $i++) {
        $m_day = '';
        $month = str_pad($i,2,'0',STR_PAD_LEFT);
        $ets_day_array = explode('-',$ets);
        $etf_day_array = explode('-',$etf);
        // print_r($ets_day_array);
        // echo $ets_day_array[1].'-';
        // echo $month.'<br>';
        $d_month = $PHP_year.'-'.$month;
        $d_ets = substr($row['rel_ets'],0,7);
        $d_etf = substr($row['rel_etf'],0,7);
        $o = '';
        if( $d_ets < $d_month && $d_month == $d_etf ) {
            $m_day = $etf_day_array[2];
            $o = 'A';
        } else if ( $d_ets < $d_month && $d_month < $d_etf ) {
            $m_day = getDaysInMonth($month,$PHP_year);
            $o = 'B';
        } else if ( $d_ets == $d_month && $d_month == $d_etf ) {
            $m_day = $etf_day_array[2] - $ets_day_array[2] + 1;
            $o = 'C';
        } else if ( $d_ets == $d_month && $d_month < $d_etf ) {
            $m_day = countDays($ets,$PHP_year.'-'.$month.'-'.getDaysInMonth($month,$PHP_year))+1;
            $o = 'D';
        } else {
        }
        // echo $m_day.'<br>';
        if(!empty($m_day)){
            $line_su_month[$row['saw_line']][$month] += $balance_su * $m_day;
            // echo ' d_month = '.$d_month.' d_ets = '.$d_ets.' d_etf = '.$d_etf.'<br>';
            // echo $o.' - '.$row['saw_line'].' - '.$row['id'].' - '.$row['ord_num'].' SU =  '.$su.' /  p_day = '.$p_day.' '.$d_month.' ( '.$ets.' ~ '.$etf.' ) ' .$balance_su.' * '.$m_day.'<p>';
            // echo $line_su_month[$row['line_id']][$month].'<br>';
        }
    }
}
// print_r($line_output['row_array']);
$l_i = 0;
$plot_line_array = array();
$bplot1_data = array();
$bplot1_1_data = array();
$bplot2_1_data = array();
$bplot3_1_data = array();
$bplot2_data = array();
$bplot3_data = array();
$bplot4_data = array();

$capacity = array();
$ttl_out = array();
$ttl_out_base = array();
$ttl_out_over = array();
$ttl_out_t = array();
$ttl_out_f = array();
foreach($line_output['all_line'] as $line){
    $plot_line_array[] = $line;
    $target = $row = ''; 
    $row['line'] = $line;
    
    for ($i=1; $i<13; $i++) {
        $month = str_pad($i,2,'0',STR_PAD_LEFT);
        # 計算目標產量
        foreach($line_output['target'][$line][$month] as $out_date => $val){
            // echo $val['workers'];
            $target[$month]['qty'] += number_format(($val['workers']*8)+($val['ot_wk']*$val['ot_hr'])*1.2);
        }
    }
    
    $sub_target = $sub_schd = $sub_out = $sub_out_base = $sub_out_over = $sub_out_t = $sub_out_f = $sub_target_ttl = $sub_schd_ttl = $sub_out_ttl = $sub_out_base_ttl = $sub_out_over_ttl = $sub_out_t_ttl = $sub_out_f_ttl = 0;
    for ($i=1; $i<13; $i++) {
        $month = str_pad($i,2,'0',STR_PAD_LEFT);
        # 數據轉換
        $row['full_target'][$i-1]['qty'] = $target[$month]['qty'];
        $row['schd'][$i-1]['qty'] = ($target[$month]['qty'] > 0)?$line_su_month[$line][$month]:0;
        $row['out'][$i-1]['qty'] = ($target[$month]['qty'] > 0)?$line_output[$line][$month]['su']:0;
        $row['out_base'][$i-1]['qty'] = ($target[$month]['qty'] > 0)?$line_output[$line][$month]['su_base']:0;
        $row['out_over'][$i-1]['qty'] = ($target[$month]['qty'] > 0)?$line_output[$line][$month]['su_over']:0;
        $row['out_t'][$i-1]['qty'] = ($target[$month]['qty'] > 0)?$line_output[$line][$month]['su_t']:0;
        $row['out_f'][$i-1]['qty'] = ($target[$month]['qty'] > 0)?$line_output[$line][$month]['su_f']:0;
        
        # 橫向數據加總
        $sub_target += $row['full_target'][$i-1]['qty'];
        $sub_schd += $row['schd'][$i-1]['qty'];
        $sub_out +=  $row['out'][$i-1]['qty'];
        $sub_out_base +=  $row['out_base'][$i-1]['qty'];
        $sub_out_over +=  $row['out_over'][$i-1]['qty'];
        $sub_out_t +=  $row['out_t'][$i-1]['qty'];
        $sub_out_f +=  $row['out_f'][$i-1]['qty'];
        // echo $line.$row['out'][$i-1]['qty'].',';
        # 直向數據加總        
        $op['full_target_ttl'][$i-1]['qty'] += $row['full_target'][$i-1]['qty'];
        $op['ttl_schd'][$i-1]['qty'] += $row['schd'][$i-1]['qty'];
        $op['ttl_out'][$i-1]['qty'] += $row['out'][$i-1]['qty'];
        $op['ttl_out_base'][$i-1]['qty'] += $row['out_base'][$i-1]['qty'];
        $op['ttl_out_over'][$i-1]['qty'] += $row['out_over'][$i-1]['qty'];
        $op['ttl_out_t'][$i-1]['qty'] += $row['out_t'][$i-1]['qty'];
        $op['ttl_out_f'][$i-1]['qty'] += $row['out_f'][$i-1]['qty'];
        
        #月份計算
        $capacity[$i-1] += $row['full_target'][$i-1]['qty'];
        $ttl_out[$i-1] += $row['out'][$i-1]['qty'];
        $ttl_out_base[$i-1] += $row['out'][$i-1]['qty'];
        $ttl_out_over[$i-1] += $row['out'][$i-1]['qty'];
        $ttl_out_t[$i-1] += $row['out_t'][$i-1]['qty'];
        $ttl_out_f[$i-1] += $row['out_f'][$i-1]['qty'];
        
        $sub_target_ttl += $row['full_target'][$i-1]['qty'];
        $sub_schd_ttl += $row['schd'][$i-1]['qty'];
        $sub_out_ttl += $row['out'][$i-1]['qty'];
        $sub_out_base_ttl += $row['out_base'][$i-1]['qty'];
        $sub_out_over_ttl += $row['out_over'][$i-1]['qty'];
        $sub_out_t_ttl += $row['out_t'][$i-1]['qty'];
        $sub_out_f_ttl += $row['out_f'][$i-1]['qty'];

        // $row['out'][$i-1]['qty2'] = $line_output[$line][$month]['workers'];
        // $row['out'][$i-1]['qty3'] = $line_output[$line][$month]['ot_hr'];
    }
    
    // echo '<br>';
    # 橫向數據加總
    $op['sub_target'][$l_i]['qty'] += $sub_target;
    $op['sub_schd'][$l_i]['qty'] += $sub_schd;
    $op['sub_out'][$l_i]['qty'] += $sub_out;
    $op['sub_out_base'][$l_i]['qty'] += $sub_out_base;
    $op['sub_out_over'][$l_i]['qty'] += $sub_out_over;
    $op['sub_out_t'][$l_i]['qty'] += $sub_out_t;
    $op['sub_out_f'][$l_i]['qty'] += $sub_out_f;
    $l_i++;
    
    $op['sub_target_ttl'] += $sub_target_ttl;
    $op['sub_schd_ttl'] += $sub_schd_ttl;
    $op['sub_out_ttl'] += $sub_out_ttl;
    $op['sub_out_base_ttl'] += $sub_out_base_ttl;
    $op['sub_out_over_ttl'] += $sub_out_over_ttl;
    $op['sub_out_t_ttl'] += $sub_out_t_ttl;
    $op['sub_out_f_ttl'] += $sub_out_f_ttl;
    
    // $bplot1_data[] = $sub_target_ttl;
    // $bplot2_data[] = $sub_schd_ttl;
    // $bplot3_data[] = $sub_out_t_ttl;
    // $bplot4_data[] = $sub_out_f_ttl;
    
    // $bplot3_1_data[] = $sub_out_ttl;
    // $tl = $sub_schd_ttl - $sub_out_ttl;
    // $bplot2_1_data[] = $tl > 0 ? $tl : 0;    
    // $tl = $sub_target_ttl - ( ($sub_out_ttl>$sub_schd_ttl)?$sub_out_ttl:$sub_schd_ttl );
    // $bplot1_1_data[] = $tl > 0 ? $tl : 0;
    
    // $bplot4_data[] = ($sub_out_ttl/$sub_target_ttl)*100;
    // for ($i=1; $i<13; $i++) {
        // $month = str_pad($i,2,'0',STR_PAD_LEFT);
        // $row['out'][$i-1]['su'] = $line_output[$line][$month]['su'];
        // $row['out'][$i-1]['workers'] = $line_output[$line][$month]['workers'];
        // $row['out'][$i-1]['ot_wk'] = $line_output[$line][$month]['ot_wk'];
        // $row['out'][$i-1]['ot_hr'] = $line_output[$line][$month]['ot_hr'];
    // }
    $op['out_row'][] = $row;
}

$over = array();
$capacitys = array();
$output = array();
$taipei = array();
$factory = array();
foreach($ttl_out as $month => $su){

    array_push($taipei,($ttl_out_t[$month]));
    array_push($factory,($ttl_out_f[$month]));

    if( $su > $capacity[$month] ) {
        array_push($output,$capacity[$month]);
        array_push($capacitys,0);
        array_push($over,$su - $capacity[$month]);
    } else {
        array_push($output,$su);
        array_push($capacitys,($capacity[$month] - $su));
        array_push($over,0);
    }

}
// print_r($over);

require_once ('lib/src/jpgraph.php');
require_once ('lib/src/jpgraph_line.php');
require_once ('lib/src/jpgraph_bar.php');

$datax=array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');

// Create the graph. 
// $graph = new Graph(1000,350); 
$graph = new Graph(800,300);   
$graph->SetScale('textlin');

// Create the linear error plot
$Taipei=new LinePlot($taipei);
$Taipei->SetColor('#8f09f7');
$Taipei->SetWeight(2);
$Taipei->SetLegend('Taipei Order');

// Create the linear error plot
$Factory=new LinePlot($factory);
$Factory->SetColor('#33FFFF');
$Factory->SetWeight(2);
$Factory->SetLegend('Factory Order');

// Create the bar plot
$Capacity = new BarPlot($capacitys);
$Capacity->SetFillGradient('AntiqueWhite2','red:0.8',GRAD_VERT); # 柱狀顏色
$Capacity->SetColor('darkgreen');
$Capacity->SetLegend('Full Capacity');

$Output = new BarPlot($output);
$Output->SetFillGradient('olivedrab1','olivedrab4:0.8',GRAD_VERT); # 柱狀顏色
$Output->SetColor('darkred');
$Output->SetLegend('Output');

// Create the second bar
$Over = new BarPlot($over); # 超過產量陣列 array(10,12,35,15);
$Over->SetFillgradient('AntiqueWhite1','orange1:0.8',GRAD_VERT);  # 柱狀顏色
$Over->SetColor('darkgreen');
// $Over->SetWeight(0);
$Over->SetLegend('Output Over'); # 說明

// Create the grouped bar plot
// $gbplot = new GroupBarPlot(array($bplot3));
// $gbplot->SetWidth(0.7);  //柱?的?度

// Add the plots to t'he graph
// $graph->Add($gbplot);

$accbplot = new AccBarPlot(array($Output,$Capacity,$Over));
$accbplot->SetColor('darkgray');
// $accbplot->SetWeight(0.4);
$graph->Add($accbplot);

// Add the plots to t'he graph
$graph->Add($Taipei);
$graph->Add($Factory);

$graph->SetMarginColor('white');
$graph->img->SetMargin(60,20,60,40);
$graph->legend->Pos(0.5,0.08,"center","top");//位置
$graph->legend->SetLayout(1);
// $graph->img->SetMargin(60,130,20,40); //?置????距 左、右、上、下
$graph->SetShadow();
$graph->legend->SetFillColor('lightblue@0.9');//?置?列??背景?色和透明度
// $graph->legend->Pos(0.03,0.3,"right","center");//位置
$graph->legend->SetFont(FF_SIMSUN,FS_NORMAL,10);//?示字体 大小
 
$graph->title->Set($PHP_fty.' - '.$PHP_year. ' Year ');
// $graph->xaxis->title->Set('Line');
$graph->xaxis->title->Set('Month');
$graph->yaxis->SetLabelAngle(0);//
$graph->yaxis->title->Set('SU.');
$graph->yaxis->title->SetMargin(20);//距离Y?的距离

$graph->title->SetFont(FF_FONT1,FS_BOLD);
$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph->xaxis->SetTickLabels($datax);
//$graph->xaxis->SetTextTickInterval(2);
 
// Display the graph
$gdImgHandler = $graph->Stroke(_IMG_HANDLER);
$fileName = "images/production_analysis.png";
$graph->img->Stream($fileName);

page_display($op,$auth,'production_analysis_view_year.html');
break;



case "production_analysis_view_month":
check_authority($auth,"view");

$op['css'] = array( 'css/scm.css' , 'js/calendar/css/jscal2.css' , 'js/calendar/css/border-radius.css' , 'js/calendar/css/gold/gold.css' );
$op['js'] = array( 'js/jquery.min.js' , 'js/jquery.blockUI.js' , 'js/calendar/js/jscal2.js' , 'js/calendar/js/lang/en.js' , 'js/scm.js' , 'js/production_analysis.js' );

$datax=array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');

# GET POST
$op['fty'] = $PHP_fty = !empty($_POST['PHP_fty']) ? $_POST['PHP_fty'] : 'LY' ;
$op['year'] = $PHP_year = !empty($_POST['PHP_year']) ? $_POST['PHP_year'] : date("Y") ;
$op['month'] = $PHP_month = !empty($_POST['PHP_month']) ? $_POST['PHP_month'] : date("m") ;
$op['month_str'] = $datax[(($PHP_month*1)-1)];

# get output
// $capaci = $PRODUCTION_ANALYSIS->get_capacity_mm($PHP_fty,$PHP_year);
// $capaci_array = $capaci['date'];
$line_output = $PRODUCTION_ANALYSIS->get_line_output_month($PHP_fty,$PHP_year,$PHP_month);
$op['all_line'] = $line_output['all_line'];
$op['counter'] = count($line_output['all_line']);
$op['line_output'] = $line_output;
$op['ord_arr'] = $line_output['ord_arr'];
// print_r($line_output);

// for ($i=1; $i<13; $i++) {
    // $month = str_pad($i,2,'0',STR_PAD_LEFT);
    
    // # 月份數字
    // $op['mm_name'][$i-1]['key'] = $month;
    // # 月份英文
    // $op['mm_name'][$i-1]['val'] = $MM2[date('m',mktime(0,0,0,$month,1,$PHP_year))];
    // # 年月
    // $op['mm_yaer'][$i-1] =date('Y',mktime(0,0,0,$month,1,$PHP_year));
    // $op['mm_month'][$i-1] = date('m',mktime(0,0,0,$month,1,$PHP_year));
    
    // # capacity
    // $op['capacity'][$i-1] = $capaci_array[date('Y',mktime(0,0,0,$month,1,$PHP_year))][date('m',mktime(0,0,0,$month,1,$PHP_year))];
    // $op['capacity_sum'] += $op['capacity'][$i-1];

// }
// echo getDaysInMonth(1,$PHP_year);
// foreach($line_output['row_array'] as $row){
    // $su = $row['su'];
    // $ets = $row['rel_ets'];
    // $etf = $row['rel_etf'];
    // $p_day = countDays($ets,$etf)+1;
    // $balance_su = $su / $p_day;
    // # echo $balance_su.'<br>';
    // for ($i=1; $i<13; $i++) {
        // $m_day = '';
        // $month = str_pad($i,2,'0',STR_PAD_LEFT);
        // $ets_day_array = explode('-',$ets);
        // $etf_day_array = explode('-',$etf);
        // # print_r($ets_day_array);
        // # echo $ets_day_array[1].'-';
        // # echo $month.'<br>';
        // $d_month = $PHP_year.'-'.$month;
        // $d_ets = substr($row['rel_ets'],0,7);
        // $d_etf = substr($row['rel_etf'],0,7);
        // $o = '';
        // if( $d_ets < $d_month && $d_month == $d_etf ) {
            // $m_day = $etf_day_array[2];
            // $o = 'A';
        // } else if ( $d_ets < $d_month && $d_month < $d_etf ) {
            // $m_day = getDaysInMonth($month,$PHP_year);
            // $o = 'B';
        // } else if ( $d_ets == $d_month && $d_month == $d_etf ) {
            // $m_day = $etf_day_array[2] - $ets_day_array[2] + 1;
            // $o = 'C';
        // } else if ( $d_ets == $d_month && $d_month < $d_etf ) {
            // $m_day = countDays($ets,$PHP_year.'-'.$month.'-'.getDaysInMonth($month,$PHP_year))+1;
            // $o = 'D';
        // } else {
        // }
        // # echo $m_day.'<br>';
        // if(!empty($m_day)){
            // $line_su_month[$row['saw_line']][$month] += $balance_su * $m_day;
            // # echo ' d_month = '.$d_month.' d_ets = '.$d_ets.' d_etf = '.$d_etf.'<br>';
            // # echo $o.' - '.$row['saw_line'].' - '.$row['id'].' - '.$row['ord_num'].' SU =  '.$su.' /  p_day = '.$p_day.' '.$d_month.' ( '.$ets.' ~ '.$etf.' ) ' .$balance_su.' * '.$m_day.'<p>';
            // # echo $line_su_month[$row['line_id']][$month].'<br>';
        // }
    // }
// }
// print_r($line_output['row_array']);
$l_i = 0;
$plot_line_array = array();
$bplot1_data = array();
$bplot1_1_data = array();
$bplot2_1_data = array();
$bplot3_1_data = array();
$bplot2_data = array();
$bplot3_data = array();
$bplot4_data = array();

$capacity = array();
$ttl_out = array();
$ttl_out_t = array();
$ttl_out_f = array();

$sub_target = $sub_out = $sub_out_t = $sub_out_f = $sub_target_ttl = $sub_out_ttl = $sub_out_t_ttl = $sub_out_f_ttl = 0;

$i=1;
foreach($line_output['all_line'] as $line){

    $plot_line_array[] = $line;
    $target = $row = ''; 
    $row['line'] = $line;
    
    # 計算目標產量
    foreach($line_output['target'][$line] as $out_date => $val){
        $target += number_format(($val['workers']*8)+($val['ot_wk']*$val['ot_hr'])*1.2);
    }

    # 數據轉換
    $row['full_target'] = $target;
    $row['out'] = ($target > 0 && $line_output[$line]['su'] > 0 )?$line_output[$line]['su']:0;
    $row['out_t'] = ($target > 0 && $line_output[$line]['su_t'] > 0 )?$line_output[$line]['su_t']:0;
    $row['out_f'] = ($target > 0 && $line_output[$line]['su_f'] > 0 )?$line_output[$line]['su_f']:0;
    
    $row['ord_num'] = $line_output['ord_arr'][$line]['ord_num'];
    $row['ord_str'] = $line_output['ord_arr'][$line]['ord_str'];
    $row['ord_count'] = $line_output['ord_arr'][$line]['ord_count'];

    # 橫向數據加總
    $sub_target += $row['full_target'];
    $sub_out +=  $row['out'];
    $sub_out_t +=  $row['out_t'];
    $sub_out_f +=  $row['out_f'];
    
    # LINE 計算
    $capacity[$i] = $row['full_target'];
    $ttl_out[$i] = $row['out'];
    $ttl_out_t[$i] = $row['out_t'];
    $ttl_out_f[$i] = $row['out_f'];
    
    $sub_target_ttl += $row['full_target'][$i]['qty'];
    $sub_schd_ttl += $row['schd'][$i]['qty'];
    $sub_out_ttl +=  $row['out'][$i]['qty'];
    $sub_out_t_ttl +=  $row['out_t'][$i]['qty'];
    $sub_out_f_ttl +=  $row['out_f'][$i]['qty'];

    # 橫向數據加總
    $op['sub_target'] += $row['full_target'];
    $op['sub_out'] += $row['out'];
    $op['sub_out_t'] += $row['out_t'];
    $op['sub_out_f'] += $row['out_f'];

    
    $op['sub_target_ttl'] += $target;
    $op['sub_schd_ttl'] += $sub_schd_ttl;
    $op['sub_out_ttl'] +=  $sub_out_ttl;
    $op['sub_out_t_ttl'] +=  $sub_out_t_ttl;
    $op['sub_out_f_ttl'] +=  $sub_out_f_ttl;
    
    // $bplot1_data[] = $sub_target_ttl;
    // $bplot2_data[] = $sub_schd_ttl;
    // $bplot3_data[] = $sub_out_t_ttl;
    // $bplot4_data[] = $sub_out_f_ttl;
    
    // $bplot3_1_data[] = $sub_out_ttl;
    // $tl = $sub_schd_ttl - $sub_out_ttl;
    // $bplot2_1_data[] = $tl > 0 ? $tl : 0;    
    // $tl = $sub_target_ttl - ( ($sub_out_ttl>$sub_schd_ttl)?$sub_out_ttl:$sub_schd_ttl );
    // $bplot1_1_data[] = $tl > 0 ? $tl : 0;
    
    // $bplot4_data[] = ($sub_out_ttl/$sub_target_ttl)*100;
    // for ($i=1; $i<13; $i++) {
        // $month = str_pad($i,2,'0',STR_PAD_LEFT);
        // $row['out'][$i]['su'] = $line_output[$line]['su'];
        // $row['out'][$i]['workers'] = $line_output[$line]['workers'];
        // $row['out'][$i]['ot_wk'] = $line_output[$line]['ot_wk'];
        // $row['out'][$i]['ot_hr'] = $line_output[$line]['ot_hr'];
    // }
    $op['out_row'][] = $row;
    $i++;
}
// print_r($op['out_row']);

$over = array();
$capacitys = array();
$output = array();
$taipei = array();
$factory = array();
foreach($ttl_out as $i => $su){

    array_push($taipei,($ttl_out_t[$i]));
    array_push($factory,($ttl_out_f[$i]));

    if( $su > $capacity[$i] ) {
        array_push($output,$capacity[$i]);
        array_push($capacitys,0);
        array_push($over,$su - $capacity[$i]);
    } else {
        array_push($output,$su);
        array_push($capacitys,($capacity[$i] - $su));
        array_push($over,0);
    }

}
// print_r($over);

require_once ('lib/src/jpgraph.php');
require_once ('lib/src/jpgraph_line.php');
require_once ('lib/src/jpgraph_bar.php');



// Create the graph. 
// $graph = new Graph(1000,350); 
$graph = new Graph(1155,300);   
$graph->SetScale('textlin');

// Create the linear error plot
$Taipei=new LinePlot($taipei);
$Taipei->SetColor('#8f09f7');
$Taipei->SetWeight(2);
$Taipei->SetLegend('Taipei Order');

// Create the linear error plot
$Factory=new LinePlot($factory);
$Factory->SetColor('#33FFFF');
$Factory->SetWeight(2);
$Factory->SetLegend('Factory Order');

// Create the bar plot
$Capacity = new BarPlot($capacitys);
$Capacity->SetFillGradient('AntiqueWhite2','red:0.8',GRAD_VERT); # 柱狀顏色
$Capacity->SetColor('darkgreen');
$Capacity->SetLegend('Full Capacity');

$Output = new BarPlot($output);
$Output->SetFillGradient('olivedrab1','olivedrab4:0.8',GRAD_VERT); # 柱狀顏色
$Output->SetColor('darkred');
$Output->SetLegend('Output');

// Create the second bar
$Over = new BarPlot($over); # 超過產量陣列 array(10,12,35,15);
$Over->SetFillgradient('AntiqueWhite1','orange1:0.8',GRAD_VERT);  # 柱狀顏色
$Over->SetColor('darkgreen');
// $Over->SetWeight(0);
$Over->SetLegend('Output Over'); # 說明

// Create the grouped bar plot
// $gbplot = new GroupBarPlot(array($bplot3));
// $gbplot->SetWidth(0.7);  //柱?的?度

// Add the plots to t'he graph
// $graph->Add($gbplot);

$accbplot = new AccBarPlot(array($Output,$Capacity,$Over));
$accbplot->SetColor('darkgray');
// $accbplot->SetWeight(0.4);
$graph->Add($accbplot);

// Add the plots to t'he graph
$graph->Add($Taipei);
$graph->Add($Factory);

$graph->SetMarginColor('white');
$graph->img->SetMargin(60,20,60,40);
$graph->legend->Pos(0.5,0.08,"center","top");//位置
$graph->legend->SetLayout(1);
// $graph->img->SetMargin(60,130,20,40); //?置????距 左、右、上、下
$graph->SetShadow();
$graph->legend->SetFillColor('lightblue@0.9');//?置?列??背景?色和透明度
// $graph->legend->Pos(0.03,0.3,"right","center");//位置
$graph->legend->SetFont(FF_SIMSUN,FS_NORMAL,10);//?示字体 大小
 
$graph->title->Set($PHP_fty.' - '.$PHP_year. ' Year ');
$graph->xaxis->title->Set('Line');
$graph->yaxis->SetLabelAngle(0);//
$graph->yaxis->title->Set('SU.');
$graph->yaxis->title->SetMargin(20);//距离Y?的距离

$graph->title->SetFont(FF_FONT1,FS_BOLD);
$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph->xaxis->SetTickLabels($line_output['all_line']);
//$graph->xaxis->SetTextTickInterval(2);
 
// Display the graph
$gdImgHandler = $graph->Stroke(_IMG_HANDLER);
$fileName = "images/production_analysis.png";
$graph->img->Stream($fileName);

page_display($op,$auth,'production_analysis_view_month.html');
break;



case "production_analysis_view_week":
check_authority($auth,"view");

$op['css'] = array( 'css/scm.css' , 'js/calendar/css/jscal2.css' , 'js/calendar/css/border-radius.css' , 'js/calendar/css/gold/gold.css' );
$op['js'] = array( 'js/jquery.min.js' , 'js/jquery.blockUI.js' , 'js/calendar/js/jscal2.js' , 'js/calendar/js/lang/en.js' , 'js/scm.js' , 'js/production_analysis.js' );

# GET POST
$_SESSION['PHP_fty'] = !empty($_SESSION['PHP_fty']) ? $_SESSION['PHP_fty'] : 'LY' ;
$_SESSION['PHP_fty'] = $PHP_fty = !empty($_POST['PHP_fty']) ? $_POST['PHP_fty'] : $_SESSION['PHP_fty'] ;
$_SESSION['PHP_Date'] = !empty($_SESSION['PHP_Date']) ? $_SESSION['PHP_Date'] : date('Y-m-d') ;
$_SESSION['PHP_Date'] = $Date = !empty($_POST['PHP_Date']) ? $_POST['PHP_Date'] : $_SESSION['PHP_Date'] ;

# 一週日期
$Date_arr = explode('-',$Date);
$week = date("w",mktime(0,0,0,$Date_arr[1],$Date_arr[2],$Date_arr[0]));
$beginLastweek=mktime(0,0,0,$Date_arr[1],$Date_arr[2]-$week+1,$Date_arr[0] );
$endLastweek=mktime(23,59,59,$Date_arr[1],$Date_arr[2]-$week+7,$Date_arr[0] );

# 一周字串
$date_str = '';
for($i=0;$i<7;$i++){
    $date_str .= ','.date("Y/m/d",mktime(0,0,0,date('m',$beginLastweek),date('d',$beginLastweek)+$i,date('Y',$beginLastweek)) );
}
$date_str = substr($date_str,1);

// echo date("Ymd",$beginLastweek).'~'.date("Ymd",$endLastweek).'<br>';
$op['now_date'] = date("Y-m-d",$beginLastweek).' ~ '.date("Y-m-d",$endLastweek);
$Dates = str_replace('-','',$Date);

$op['Dates'] = $Dates;
$op['date_str'] = str_replace('/','',$date_str);

# get output
$line_output = $PRODUCTION_ANALYSIS->get_line_output_week($PHP_fty,date("Y-m-d",$beginLastweek),date("Y-m-d",$endLastweek),explode(',',str_replace('/','-',$date_str)));
// print_r($line_output);
$op['out_put'] = $line_output['out_put'];

$op['week'] = array('Mon.','Tue.','Wed.','Thu.','Fri.','Sat.','Sun.');
$op['week_date'] = explode(',',$date_str);
$op['order'] = $line_output['order'];
$op['target'] = $line_output['target'];
$op['all_line'] = $line_output['all_line'];
$op['fty'] = $PHP_fty;


require_once ('lib/src/jpgraph.php');
require_once ('lib/src/jpgraph_line.php');
require_once ('lib/src/jpgraph_bar.php');
 
$l1datay = array(11,9,2,4,3,13,17);

$l2datay = array(23,12,5,19,17,10,15);
$l3datay = array(22,16,2,11,13,10,13);
$datax=array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
 
// Create the graph. 
$graph = new Graph(1000,350);    
$graph->SetScale('textlin');
 
$graph->img->SetMargin(60,130,20,40); //?置????距 左、右、上、下
$graph->SetShadow();
 
// Create the linear error plot
// $l1plot=new LinePlot($bplot3_data);
// $l1plot->SetColor('#8f09f7');
// $l1plot->SetWeight(2);
// $l1plot->SetLegend('Prediction');
 
// Create the bar plot
// $bplot1 = new BarPlot($bplot1_1_data);
// $bplot1->SetFillColor('red:0.6');
// $bplot1->SetFillColor('#de5454');
// $bplot1->SetLegend('Capacity');
// $bplot2 = new BarPlot($bplot2_1_data);
// $bplot2->SetFillColor('#546ade');
// $bplot2->SetLegend('Schedule');
// $bplot3 = new BarPlot($bplot3_1_data);
// $bplot3->SetFillColor('#ecab3a');
// $bplot3->SetLegend('Output');


// Create the grouped bar plot
// $gbplot = new GroupBarPlot(array($bplot3));
// $gbplot->SetWidth(0.7);  //柱?的?度

// Add the plots to t'he graph
// $graph->Add($gbplot);

// $accbplot = new AccBarPlot(array($bplot3,$bplot2,$bplot1));
// $accbplot->SetColor('darkgray');
// $accbplot->SetWeight(0.4);
// $graph->Add($accbplot);

// Add the plots to t'he graph
// $graph->Add($l1plot);


$graph->legend->SetFillColor('lightblue@0.9');//?置?列??背景?色和透明度
$graph->legend->Pos(0.03,0.3,"right","center");//位置
$graph->legend->SetFont(FF_SIMSUN,FS_NORMAL,10);//?示字体 大小
 
$graph->title->Set($PHP_fty.' - '.$PHP_year);
$graph->xaxis->title->Set('Line');
$graph->yaxis->SetLabelAngle(0);//
$graph->yaxis->title->Set('SU.');
$graph->yaxis->title->SetMargin(20);//距离Y?的距离

$graph->title->SetFont(FF_FONT1,FS_BOLD);
$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph->xaxis->SetTickLabels($plot_line_array);
//$graph->xaxis->SetTextTickInterval(2);
 
// Display the graph
// $gdImgHandler = $graph->Stroke(_IMG_HANDLER);
// $fileName = "images/production_analysis_week.png";
// $graph->img->Stream($fileName);

page_display($op,$auth,'production_analysis_view_week.html');
break;



case "shipping_ord_det":
check_authority($auth,"view");

$mm = array("1"=>"01","2"=>"02","3"=>"03","4"=>"04","5"=>"05","6"=>"06","7"=>"07","8"=>"08","9"=>"09");
$op = $PRODUCTION_ANALYSIS->get_shipping_det($PHP_fty, $PHP_year, $mm[$PHP_month]);

$op['fty'] = $PHP_fty;
$op['year'] = $PHP_year;
$op['month'] = $PHP_month;

page_display($op,$auth,'shipping_ord.html');
break;




}   // end case ---------
?>
