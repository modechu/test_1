<?php 
##################  2006/11/15  ########################
#			initialized  class Object
#		for Carnival SCM [Sample]  management
#			Jack Yang     2004/11/10
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

$admin = new AUTH();
if (!$admin->init($mysql)) { print "error!! cannot initialize database for AUTH class";	exit; }

$log = new SQL_LOG();
if (!$log->init($mysql,"log")) { print "error!! cannot initialize database for LOG class"; exit; }

$dept = new DEPT();
if (!$dept->init($mysql)) {	print "error!! cannot initialize database for DEPT class"; exit; }

$smpl_type = new SMPL_TYPE();
if (!$smpl_type->init($mysql)) { print "error!! cannot initialize database for SMPL_TYPE class"; exit;	}

$style_type = new STYLE_TYPE();
if (!$style_type->init($mysql)) { print "error!! cannot initialize database for STYLE_TYPE class"; exit; }

$season = new SEASON();
if (!$season->init($mysql)) { print "error!! cannot initialize database for SEASON class"; exit; }

$size_type = new SIZE_TYPE();  // temp ?????????????????????
if (!$size_type->init($mysql)) { print "error!! cannot initialize database for SIZE_TYPE class"; exit; }  //temp ?????

$size_des = new SIZE_DES();
if (!$size_des->init($mysql)) { print "error!! cannot initialize database for SIZE_DES class"; exit; }


$supl_type = new SUPL_TYPE();
if (!$supl_type->init($mysql)) { print "error!! cannot initialize database for SUPL_TYPE class"; exit; }

$team = new TEAM();
if (!$team->init($mysql)) {	print "error!! cannot initialize database for TEAM class"; exit; }

$user = new USER();
if (!$user->init($mysql)) {	print "error!! cannot initialize database for USER class"; exit; }

$cust = new CUST();
if (!$cust->init($mysql)) {	print "error!! cannot initialize database for USER class"; exit; }

$supl = new SUPL();
if (!$supl->init($mysql)) {	print "error!! cannot initialize database for SUPL class"; exit; }

$lots = new LOTS();
if (!$lots->init($mysql)) {	print "error!! cannot initialize database for LOTS class"; exit; }

$acc = new ACC();
if (!$acc->init($mysql)) {	print "error!! cannot initialize database for ACC class"; exit; }

$smpl = new SMPL();
if (!$smpl->init($mysql)) {	print "error!! cannot initialize database for SMPL class"; exit; }

$wi = new WI();
if (!$wi->init($mysql)) {	print "error!! cannot initialize database for WI class"; exit; }

$wi_qty = new WI_QTY();
if (!$wi_qty->init($mysql)) {	print "error!! cannot initialize database for WI_QTY class"; exit; }

$wiqty = new WIQTY();
if (!$wiqty->init($mysql)) {	print "error!! cannot initialize database for WIQTY class"; exit; }


$smpl_lots = new SMPL_LOTS();
if (!$smpl_lots->init($mysql)) {	print "error!! cannot initialize database for SMPL_LOTS class"; exit; }

$smpl_acc = new SMPL_ACC();
if (!$smpl_acc->init($mysql)) {	print "error!! cannot initialize database for SMPL_ACC class"; exit; }

$ti = new TI();
if (!$ti->init($mysql)) {	print "error!! cannot initialize database for TI class"; exit; }

$fils = new FILES();
if (!$fils->init($mysql)) {	print "error!! cannot initialize database for FILE class"; exit; }



$bom = new BOM();
if (!$bom->init($mysql)) {	print "error!! cannot initialize database for BOM class"; exit; }

$ag = new AG();
if (!$ag->init($mysql)) {	print "error!! cannot initialize database for AG class"; exit; }

$order = new ORDER();
if (!$order->init($mysql)) { print "error!! cannot initialize database for ORDER class"; exit; }


$smpl_ord = new SMPL_ORD();
if (!$smpl_ord->init($mysql)) { print "error!! cannot initialize database for SMPL_ORD class"; exit; }

$smpl_log = new SMPL_LOG();
if (!$smpl_log->init($mysql)) { print "error!! cannot initialize database for SMPL_LOG class"; exit; }

$smpl_output = new SMPL_OUTPUT();
if (!$smpl_output->init($mysql)) { print "error!! cannot initialize database for SMPL_OUTPUT class"; exit; }



$r_order = new R_ORDER();
if (!$r_order->init($mysql)) { print "error!! cannot initialize database for R_ORDER class"; exit; }

$order_log = new ORDER_LOG();
if (!$order_log->init($mysql)) { print "error!! cannot initialize database for ORDER_LOG class"; exit; }

$capaci = new CAPACI();
if (!$capaci->init($mysql)) { print "error!! cannot initialize database for CAPACI class"; exit; }



$daily = new DAILY();
if (!$daily->init($mysql)) { print "error!! cannot initialize database for DAILY class"; exit; }

$shipping = new SHIPPING();
if (!$shipping->init($mysql)) { print "error!! cannot initialize database for SHIPPING class"; exit; }

$forecast = new FORECAST();
if (!$forecast->init($mysql)) { print "error!! cannot initialize database for FORECAST class"; exit; }

$offer = new OFFER();
if (!$offer->init($mysql)) { print "error!! cannot initialize database for OFFER class"; exit; }

$tmps1 = new TMPSUPL();
if (!$tmps1->init($mysql)) {	print "error!! cannot initialize database for PARTS class"; exit; }

$smplord_lots = new SMPL_ORD_LOTS();
if (!$smplord_lots->init($mysql)) {	print "error!! cannot initialize database for PARTS class"; exit; }

$smplord_acc = new SMPL_ORD_ACC();
if (!$smplord_acc->init($mysql)) {	print "error!! cannot initialize database for PARTS class"; exit; }

$smpl_wi = new SMPL_WI();
if (!$smpl_wi->init($mysql)) {	print "error!! cannot initialize database for PARTS class"; exit; }

$smpl_wiqty = new SMPL_WIQTY();
if (!$smpl_wiqty->init($mysql)) {	print "error!! cannot initialize database for PARTS class"; exit; }

$smpl_ti = new SMPL_TI();
if (!$smpl_ti->init($mysql)) {	print "error!! cannot initialize database for PARTS class"; exit; }

$smpl_bom = new SMPL_BOM();
if (!$smpl_bom->init($mysql)) {	print "error!! cannot initialize database for PARTS class"; exit; }

$apply = new APPLY();
if (!$apply->init($mysql)) {	print "error!! cannot initialize database for APPLY class"; exit; }

$po = new PO();
if (!$po->init($mysql)) {	print "error!! cannot initialize database for APPLY class"; exit; }

$receive = new RECEIVE();
if (!$receive->init($mysql)) {	print "error!! cannot initialize database for APPLY class"; exit; }

$tw_receive = new TW_RECEIVE();
if (!$tw_receive->init($mysql)) {	print "error!! cannot initialize database for TW_RECEIVE class"; exit; }

$c_order = new C_ORDER();
if (!$c_order->init($mysql)) {	print "error!! cannot initialize database for ORDER_C class"; exit; }

$report = new REPORT();
if (!$report->init($mysql)) {	print "error!! cannot initialize database for REPORT class"; exit; }

$rate = new RATE();
if (!$rate->init($mysql)) {	print "error!! cannot initialize database for RATE class"; exit; }

$ch_cov = new CHINESE_COV();

$para = new PARA();
if (!$para->init($mysql)) {	print "error!! cannot initialize database for RATE class"; exit; }

$except = new EXCEPTION();
if (!$except->init($mysql)) {	print "error!! cannot initialize database for RATE class"; exit; }

$notify = new NOTIFY();
if (!$notify->init($mysql,"log")) { print "error!! cannot initialize database for LOG class"; exit; }

//PO SHIP
$poship = new PO_SHIP();
  if (!$poship->init($mysql,"log")) { print "error!! cannot initialize database for LOG class"; exit; }

//C.M. & salescost
$cost = new COST();
if (!$cost->init($mysql)) {	print "error!! cannot initialize database for RATE class"; exit; }

//研發馬克2008-12-04
$Marker = new MARKER();
if (!$Marker->init($mysql,"log")) { print "error!! cannot initialize database for MARKER class"; exit;	}

//索賠2008-12-17
$debit = new DEBIT();
if (!$debit->init($mysql,"log")) { print "error!! cannot initialize database for Debit class"; exit;	}


//主料 2009-05-20
$fabric = new FABRIC();
if (!$fabric->init($mysql)) {	print "error!! cannot initialize database for FABRIC class"; exit; }

//Shipping document 2008-11-05
$ship_doc = new SHIPDOC();
  if (!$ship_doc->init($mysql,"log")) { print "error!! cannot initialize database for LOG class"; exit; }

//Shipping document 2012-03-05
$shipping_doc = new SHIPPING_DOC();
  if (!$shipping_doc->init($mysql,"log")) { print "error!! cannot initialize database for SHIPPING DOC class"; exit; }


//加班2009-07-289
$overtime = new OVERTIME();
if (!$overtime->init($mysql,"log")) { print "error!! cannot initialize database for Debit class"; exit;	}

//預算2010-02-01
$expense = new EXPENSE();
if (!$expense->init($mysql,"log")) { print "error!! cannot initialize database for Debit class"; exit;	}

//Forecast 2008-09-30
$fcst2 = new FCST2();
  if (!$fcst2->init($mysql,"log")) { print "error!! cannot initialize database for LOG class"; exit; }

//研發馬克2010-02-02
$fty_marker = new FTY_MARKER();
if (!$fty_marker ->init($mysql,"log")) { print "error!! cannot initialize database for MARKER class"; exit;	}

//排產
$schedule = new SCHEDULE();
if (!$schedule->init($mysql,"log")) { print "error!! cannot initialize database for Debit class"; exit;	}


//排產 樣本
$smpl_sch = new SMPL_SCH();
if (!$smpl_sch->init($mysql,"log")) { print "error!! cannot initialize database for Debit class"; exit;	}


//驗布報告2009-04-23
$rcv_rpt = new RCV_RPT();
if (!$rcv_rpt->init($mysql,"log")) { print "error!! cannot initialize database for Debit class"; exit;	}


//訂單裁剪
$cutting = new CUTTING();
if (!$cutting->init($mysql,"log")) { print "error!! cannot initialize database for Debit class"; exit;	}

//應付
$apb = new APB();
if (!$apb->init($mysql,"log")) { print "error!! cannot initialize database for Debit class"; exit;	}

$vend = new array2checkbox();
$arry2 = new array2html();


?>