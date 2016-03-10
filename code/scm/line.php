<?php
session_start();

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

require_once "config.php";
require_once "config.admin.php";
include_once($config['root_dir']."/lib/class.line.php");
include_once($config['root_dir']."/lib/class.monitor.php");
require_once "init.object.php";

$Line = new LINE();
if (!$Line->init($mysql,"log")) { print "error!! cannot initialize database for LOG class"; exit; }
$monitor = new MONITOR();
if (!$monitor->init($mysql,"log")) { print "error!! cannot initialize database for LOG class"; exit; }

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

session_register	('FTY_CM');
$para_cm = $para->get(0,'hj-cm');
$FTY_CM['HJ'] = $para_cm['set_value'];
$para_cm = $para->get(0,'ly-cm');
$FTY_CM['LY'] = $para_cm['set_value'];
$FTY_CM['SC'] = 0;

session_register	('CUST_DEL');
$para_cm = $para->get(0,'cust_del');
$CUST_DEL = $para_cm['set_value'] ;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

$AUTH = '081';
$PGM = $_SESSION['ITEM']['ADMIN_PERM'];
$op = array();

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// echo $PHP_action.'<br>';
switch ($PHP_action) {
//=======================================================

case "main":
check_authority($AUTH,$PGM[1]);

$_SESSION['SHIPPING']['ord_parm'] = array();

if( !empty($user_dept) && ( $user_dept == 'HJ' || $user_dept == 'LY' || $user_dept == 'CF' ) )
{
	$op['fty'] = $op['fty2'] = "<B>".$user_dept."</B> <input type=hidden name='PHP_fty' value='$user_dept'>";
} else {
	$op['fty'] = $arry2->select($FACTORY,'','SCH_ftys','select','get_order(this)');
	$op['fty2'] = $arry2->select($FACTORY,'','SCH_fty','select','get_fty(this)');
}

$line = $monitor->get_fields_saw('DISTINCT pdt_saw_line.line');
$op['line_select'] = $arry2->select($line,'','SCH_line','select');

if(isset($PHP_msg)) $op['msg'][] = $PHP_msg;

page_display($op,$AUTH,'line_mian.html');
break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "get_order":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "get_order":
check_authority($AUTH,$PGM[1]);

$ord_num = $Line->get_order($SCH_fty);
$op['ORDER_search'] = $arry2->select($ord_num,'','SCH_ord','select');
echo $op['ORDER_search'];
break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "get_fty":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "get_fty":
check_authority($AUTH,$PGM[1]);

$where_str = " AND pdt_saw_line.del_mk = '0' AND fty = '$SCH_fty' AND ord_num <>'' ORDER BY `line` ASC";
// echo $where_str;
$line = $monitor->get_fields_saw('DISTINCT line',$where_str);
$op['LINE_search'] = $arry2->select($line,'','SCH_line','select');

$where_str = " AND pdt_saw_line.del_mk = '0' AND fty = '$SCH_fty' AND ord_num <>'' ORDER BY `saw_out_put`.`ord_num` ASC , `saw_out_put`.`out_date` DESC ";
$ord_num = $monitor->get_fields_saw('DISTINCT ord_num',$where_str);
$op['ORDER_search'] = $arry2->select($ord_num,'','SCH_ord','select');
if (!isset($ord_num[0])) $op['ORDER_search'] ="<input type=hidden name='SCH_ord' value=''>";
if (!isset($line[0])) $op['LINE_search'] ="<input type=hidden name='SCH_line' value=''>";

echo $op['LINE_search']."|".$op['ORDER_search'];
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "sch_daily":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "sch_daily":
check_authority($AUTH,$PGM[1]);

if ( isset($SCH_ftys) && isset($SCH_ord) ) {

	$SCH_des = (!empty($SCH_des))?$SCH_des:'';
	$_SESSION['sch_parm'] = array (
		'PHP_fty'			=>	$SCH_ftys,
		'SCH_ord'			=>	$SCH_ord,
		'PHP_action'		=>	$PHP_action,
		'PHP_sr_startno'	=>	$PHP_sr_startno
	);

} else {

	if( isset($PHP_sr_startno) ) $_SESSION['sch_parm']['PHP_sr_startno'] = $PHP_sr_startno;

}

$op = $Line->search(1);

// for ( $i=0; $i<sizeof($op['ship_doc']); $i++ ) {
	// $op['ship_doc'][$i]['status'] = $shipping_doc->get_status($op['ship_doc'][$i]['status']);
	// $str = $shipping_doc->get_data('ord_num,cust_po', $op['ship_doc'][$i]['id']);
	// $op['ship_doc'][$i]['ord_num'] = $str['ord_num'];
	// $op['ship_doc'][$i]['cust_po'] = $str['cust_po'];
// }

if(isset($_GET['PHP_msg'])) $op['msg'][] = $_GET['PHP_msg'];

page_display($op,$AUTH,'line_list.html');
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "sch_dailys":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "sch_dailys":
check_authority($AUTH,$PGM[1]);

if ( isset($SCH_inv) || ( isset($SCH_date_str) && isset($SCH_date_end)) || isset($PHP_cust) || isset($PHP_fty) ) {

    $SCH_des = (!empty($SCH_des))?$SCH_des:'';
		$_SESSION['sch_parm'] = array (
		'SCH_inv'			=>	$SCH_inv,
		'SCH_date_str'		=>	$SCH_date_str,
		'SCH_date_end'		=>	$SCH_date_end,
		'SCH_des'			=>	$SCH_des,
		'PHP_fty'			=>	$PHP_fty,
		'PHP_cust'			=>	$PHP_cust,
		'SCH_ord'			=>	$SCH_ord,
		'PHP_action'		=>	$PHP_action,
		'PHP_sr_startno'	=>	$PHP_sr_startno
	);

} else {

	if( isset($PHP_sr_startno) ) $_SESSION['sch_parm']['PHP_sr_startno'] = $PHP_sr_startno;

}

$shipping_doc->chk_shipping();

$op = $shipping_doc->search(1);

for ( $i=0; $i<sizeof($op['ship_doc']); $i++ ) {
	$op['ship_doc'][$i]['status'] = $shipping_doc->get_status($op['ship_doc'][$i]['status']);
	$str = $shipping_doc->get_data('ord_num,cust_po', $op['ship_doc'][$i]['id']);
	$op['ship_doc'][$i]['ord_num'] = $str['ord_num'];
	$op['ship_doc'][$i]['cust_po'] = $str['cust_po'];
}

if(isset($_GET['PHP_msg'])) $op['msg'][] = $_GET['PHP_msg'];

page_display($op,$AUTH,'ship_sch_list.html');
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "line_daily_view":	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "line_daily_view":
check_authority($AUTH,$PGM[1]);

$op = $Line->get_daily($SCH_ord,$SCH_line);

# 所有生產日期
$ldate = array();

$t_qty = array();
foreach($op['line'] as $line){
	array_push($ldate,$line['w_date']);
	
	@$t_qty[$line['w_date']] += $line['qty'];
}

$ldate = array_unique($ldate);


if(!empty($SCH_w_date)) {
	# 日期搜尋
	$op = $Line->get_daily($SCH_ord,$SCH_line,$SCH_w_date);

	# 先將該日期所有的工號列表
	$emp_id = array();
	foreach($op['line'] as $line){
		array_push($emp_id,$line['emp_id']);
	}
	$emp_id = array_unique($emp_id);
	$lemp_id = $arry2->select($emp_id,$SCH_emp_id,'SCH_emp_id','select','get_emp_id(this)');
	
	# 依照日期工號顯示
	$op = $Line->get_daily($SCH_ord,$SCH_line,$SCH_w_date,$SCH_emp_id);
	$tqty = 0;
	foreach($op['line'] as $line){
		@$tqty += $line['qty'];
	}
} else {
	$op['line'] = '';
}

$op['ldate'] = $arry2->select($ldate,$SCH_w_date,'SCH_w_date','select','get_w_date(this)');
$op['lemp_id'] = $lemp_id;

$op['order_num'] = $SCH_ord;
$op['lines'] = $SCH_line;
$op['w_date'] = $SCH_w_date;
$op['emp_id'] = $SCH_emp_id;
$op['tqty'] = $tqty;

include_once($config['root_dir']."/lib/src/jpgraph.php");
include_once($config['root_dir']."/lib/src/jpgraph_line.php");

$ydata = array();
$x_ary = array();
$i = 0;
$ydata[$i] = '';
$x_ary[$i] = '';
$i++;
foreach($t_qty as $k => $v ){
	$ydata[$i] = $v;
	$x_ary[$i] = $k;
	$i++;
}
$ydata[$i] = '';
$x_ary[$i] = '';

// Create the graph. These two calls are always required
$graph = new Graph(800,350,"auto");    
$graph->SetScale("textlin");

// Adjust the margin
$graph->img->SetMargin(70,30,30,60);    
$graph->SetShadow();

// Create the linear plot
$lineplot=new LinePlot($ydata);
$lineplot->mark->SetType(MARK_FILLEDCIRCLE);
$lineplot->value->show();
$lineplot->value->SetColor('red');
// $lineplot->value->SetColor('darkred');
$lineplot->value->SetFont(FF_FONT1,FS_BOLD);
// $lineplot->value->SetFormat('$%0.1f');
$lineplot->value->SetFormat( " %d");


// Add the plot to the graph
$graph->Add($lineplot);

$graph->title->Set("Order # : ".$SCH_ord." Line : ".$SCH_line);
$graph->xaxis->title->Set("Date");
$graph->yaxis->title->Set("Cutting Q'ty");

# 座標軸距離
$graph->xaxis->title->SetMargin(20);
$graph->yaxis->title->SetMargin(20);

$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);

$graph->title->SetFont(FF_FONT1,FS_BOLD);

// $graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);


// Setup X-scale
$graph->xaxis->SetTickLabels($x_ary);
$graph->xaxis->SetFont(FF_ARIAL,FS_BOLD,8);
$graph->xaxis->SetLabelAngle(30);
$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);

$lineplot->SetColor("blue");
$lineplot->SetWeight(2);

// Display the graph
$graph->Stroke('picture/line_daily.png');

page_display($op,$AUTH,'line_daily_view.html');
break;


//-------------------------------------------------------------------------

}   // end case ---------

?>
