<?php 

require_once "lib/class.auth.php";
require_once "lib/class.sql_log.php";

/*----------
# 功能說明 : 系統管理帳號資訊
----------*/
$_SESSION['ADMIN']['manager'] = array(
    '10106'     =>  'ssnorman'    ,   
    'lamode'    =>  'ss6699'      ,   
    'morial'    =>  'ss21551' 
);

$_SESSION['ADMIN']['10106']     = array(    'name'     =>  '孫以智'    ,       'email'      =>  'jerrysun@tp.carnival.com.tw'  );
$_SESSION['ADMIN']['mode']      = array(    'name'     =>  '朱治柏'    ,       'email'      =>  'mode@tp.carnival.com.tw'  );
$_SESSION['ADMIN']['morial']    = array(    'name'     =>  '錢方斌'    ,       'email'      =>  'morial@tp.carnival.com.tw'  );

$SYS_DEPT = '代工事業體';

/*----------
# 功能說明 : 系統目錄權限
----------*/
$i=1;	$m=0;
$A_Item[$i][$m++]=array("dir","PRIMARY");#
$A_Item[$i][$m++][0]=array("txt","Customer","Customer","primary.php?PHP_action=cust","main","001");#1-1
$A_Item[$i][$m++][0]=array("txt","Supplier","Supplier","primary.php?PHP_action=supl","main","002");#1-2
$A_Item[$i][$m++][0]=array("txt","Fabric","Fabric","primary.php?PHP_action=lots","main","003");#1-3
$A_Item[$i][$m++][0]=array("txt","Accessory","Accessory","primary.php?PHP_action=acc","main","004");#1-4
$A_Item[$i][$m++][0]=array("txt","Size Scale","Size Scale","$PHP_SELF?PHP_action=size_des","main","053");#1-5
$A_Item[$i][$m++][0]=array("txt","Sample Cat.","Sample Cat.","$PHP_SELF?PHP_action=smpl_type","main","049");#1-6
$A_Item[$i][$m++][0]=array("txt","Style Cat.","Style Cat.","$PHP_SELF?PHP_action=style_type","main","050");#1-7
$A_Item[$i][$m++][0]=array("txt","Consignee","Consignee","consignee.php?PHP_action=consignee","main","006");#1-8
// $A_Item[$i][$m++][0]=array("txt","I.E. Data","I.E. Data","ie_class.php","main","097");#1-9
// $A_Item[$i][$m++][0]=array("txt","Equipment","Equipment","sorry.html","main","");#1-10
// $A_Item[$i][$m++][0]=array("txt","Human","Human","sorry.html","main","");#1-11

$i++;	$m=0;
$A_Item[$i][$m++]=array("dir","SAMPLE");
$A_Item[$i][$m++][0]=array("txt","Sample Order","Sample Order","$PHP_SELF?PHP_action=smpl_ord","main","008");#2-1
$A_Item[$i][$m++][0]=array("txt","FAB / ACC","Fabric / Accessory","$PHP_SELF?PHP_action=smpl_fa","main","009");#2-2
$A_Item[$i][$m++][0]=array("txt","W I / W S","Working Instruct / Worksheet","$PHP_SELF?PHP_action=smpl_wi","main","010");#2-3
$A_Item[$i][$m++][0]=array("txt","W I Submit","Working Instruct Submit","$PHP_SELF?PHP_action=smpl_wi_uncfm","main","011");#2-4
$A_Item[$i][$m++][0]=array("txt","BOM","Bill of Material","$PHP_SELF?PHP_action=smpl_bom","main","012");#2-5
$A_Item[$i][$m++][0]=array("txt","BOM Submit","Bill of Material Submit","$PHP_SELF?PHP_action=smpl_bom_uncfm","main","013");#2-6
$A_Item[$i][$m++][0]=array("txt","Support","Support","$PHP_SELF?PHP_action=smpl_supplier","main","014");#2-7
$A_Item[$i][$m++][0]=array("txt","Schedule","Schedule","$PHP_SELF?PHP_action=smpl_schedule","main","015");#2-8
$A_Item[$i][$m++][0]=array("txt","Out-Put","Out-Put","$PHP_SELF?PHP_action=smpl_output","main","016");#2-9
$A_Item[$i][$m++][0]=array("txt","Reports","Schedule Reports","smpl_sch.php?PHP_action=pd_schedule","main","017");#2-10

$i++;	$m=0;
$A_Item[$i][$m++]=array("dir","ORDER");#
$A_Item[$i][$m++][0]=array("txt","Order Records","Order Records","$PHP_SELF?PHP_action=order_entry","main","065");#3-1
$A_Item[$i][$m++][0]=array("txt","Confirm Order","Confirm Order","$PHP_SELF?PHP_action=order_cfm","main","066");#3-2
$A_Item[$i][$m++][0]=array("txt","Review Order","Review Order","$PHP_SELF?PHP_action=order_rev","main","075");#3-3
$A_Item[$i][$m++][0]=array("txt","Approval Order","Approval Order","$PHP_SELF?PHP_action=order_apv","main","067");#3-4
$A_Item[$i][$m++][0]=array("txt","Material Info","Material Info","$PHP_SELF?PHP_action=material_schedule","main","068");#3-5
$A_Item[$i][$m++][0]=array("txt","FAB / ACC","Fabric / Accessory","$PHP_SELF?PHP_action=order_fa","main","018");#3-6
$A_Item[$i][$m++][0]=array("txt","W I / W S","Working Instruct / Worksheet","wi.php?PHP_action=wi","main","019");#3-7
$A_Item[$i][$m++][0]=array("txt","WorkSheet","WorkSheet","$PHP_SELF?PHP_action=ti","main","021");#
$A_Item[$i][$m++][0]=array("txt","W I Submit","Working Instruct Submit","wi.php?PHP_action=wi_uncfm","main","020");#3-8
$A_Item[$i][$m++][0]=array("txt","BOM","Bill of Material","bom.php?PHP_action=bom","main","022");#3-9
$A_Item[$i][$m++][0]=array("txt","BOM CFM","Bill of Material Confirm","bom.php?PHP_action=bom_uncfm","main","023");#3-10

$i++;	$m=0;
$A_Item[$i][$m++]=array("dir","I.E. / YY");#
$A_Item[$i][$m++][0]=array("txt","Sample","Sample Section","sorry.html","main","095");#4-1
$A_Item[$i][$m++][0]=array("txt","Order","Order Section","ie.php?PHP_action=IE_record","main","025");##4-2
$A_Item[$i][$m++][0]=array("txt","Fabric YY","Fabric YY","fab_comp.php?PHP_action=fab_comp","main","071");#4-3
$A_Item[$i][$m++][0]=array("txt","Marker","Marker","marker.php?PHP_action=marker_search","main","061");#4-4

$i++;	$m=0;
$A_Item[$i][$m++]=array("dir","P.O. / SHIP.");#
$A_Item[$i][$m++][0]=array("txt","P.O.","Purchase Order","$PHP_SELF?PHP_action=apply","main","037");#5-1
$A_Item[$i][$m++][0]=array("txt","CFM P.O.","Confirm Purchase Order","po.php?PHP_action=po_cfm_search","main","041");#5-2
$A_Item[$i][$m++][0]=array("txt","APV P.O.","Approval Purchase Order","po.php?PHP_action=po_apv_search","main","042");#5-3
$A_Item[$i][$m++][0]=array("txt","Note","Note","po.php?PHP_action=po_ann","main","038");#5-4
$A_Item[$i][$m++][0]=array("txt","SHIP","SHIP","po_ship.php?PHP_action=po_ship","main","039");#
$A_Item[$i][$m++][0]=array("txt","Ship advice","Ship advice","supplier_ship.php?PHP_action=main","main","091");#5-5
$A_Item[$i][$m++][0]=array("txt","APB.","Accounts Payable\n驗收付款","apb.php?PHP_action=apb","main","040");#5-6

$i++;	$m=0;
$A_Item[$i][$m++]=array("dir","PRODUCTION");#
$A_Item[$i][$m++][0]=array("txt","Schedule","Schedule","schedule.php?PHP_action=main","main","026");#6-1
$A_Item[$i][$m++][0]=array("txt","Cutting","Cutting","cutting.php?PHP_action=cutting","main","027");#6-2
$A_Item[$i][$m++][0]=array("txt","Out-Put","Out-Put","monitor.php?PHP_action=monitor_daily","main","028");#6-3
$A_Item[$i][$m++][0]=array("txt","DAILY OUT PUT","DAILY OUT PUT","w_daily_out.php?PHP_action=main","main","099");
$A_Item[$i][$m++][0]=array("txt","RFID OUT PUT","RFID OUT PUT","rfid.php?PHP_action=main","main","105");
$A_Item[$i][$m++][0]=array("txt","EXCEL OUT PUT","EXCEL OUT PUT","excel_dailyoutput.php?PHP_action=main","main","106");
//$A_Item[$i][$m++][0]=array("txt","test","test","hr_attend.php?PHP_action=main","main","107");
$A_Item[$i][$m++][0]=array("txt","morial_test","morial_test","pack.php?PHP_action=main","main","107");
//$A_Item[$i][$m++][0]=array("txt","morial_test2","morial_test2","invoice_import.php?PHP_action=main","main","108");
$A_Item[$i][$m++][0]=array("txt","LINE QC","LINE QC","w_line_qc.php?PHP_action=main","main","100");
$A_Item[$i][$m++][0]=array("txt","CUTTING PLAN","工廠計畫裁剪數量&#13;Cutting Plan","fty_colorway.php?PHP_action=main","main","111");
$A_Item[$i][$m++][0]=array("txt","BOM ( Factory )","廠務端BOM表用量&#13;BOM物料翻譯&#13;需求用量確認&#13;輸入 (物料翻譯) 和 (損耗) 以及送出 (發料通知) 和 (領料通知) 和 (讀取確認紀錄) &#13;訂單明細表&#13;Bom of Materials","bom_of_materials.php?PHP_action=main","main","088");#6-4
$A_Item[$i][$m++][0]=array("txt","Sends the material","Material release&#13;生產領發用量設定","bom_mat_release.php?PHP_action=main","main","089");#6-5
// $A_Item[$i][$m++][0]=array("txt","Fab/Acc Submit","goods issue , Material requisition &#13;Submit material notice","bom_mat_release.php?PHP_action=requi_notify","main","090");#6-6
// $A_Item[$i][$m++][0]=array("txt","Fab/Acc Confirm","goods issue , Material requisition &#13;領、發料通知&#13;CFM material notice","bom_mat_release.php?PHP_action=uncfm_search","main","098");#6-7

$i++;	$m=0;
$A_Item[$i][$m++]=array("dir","WAREHOUSING");#
$A_Item[$i][$m++][0]=array("txt","Material Arrival","到料時間&#13;Material Arrival","arrival_time.php?PHP_action=main","main","082");#7-1
$A_Item[$i][$m++][0]=array("txt","Acceptance","驗收入庫&#13;Acceptance","incoming_material.php?PHP_action=incoming_material","main","083");#7-2
$A_Item[$i][$m++][0]=array("txt","Send","領料 / 領用&#13;Send / To get","receiving_to_get_material.php?PHP_action=main","main","084");#7-3
$A_Item[$i][$m++][0]=array("txt","Adjust","退料 / 調整&#13;Adjustment","returned_material_adjustment.php?PHP_action=main","main","085");#7-4
$A_Item[$i][$m++][0]=array("txt","Stock","庫存 / 儲位&#13;Stock","stock.php?PHP_action=main","main","086");#7-5
$A_Item[$i][$m++][0]=array("txt","Inventory","盤點&#13;Inventory","inventory.php?PHP_action=main","main","087");#7-6
$A_Item[$i][$m++][0]=array("txt","Report","報表&#13;Report","stock_report.php?PHP_action=main","main","102");#7-7
$A_Item[$i][$m++][0]=array("txt","Storage","成品倉&#13;Storage","storage.php?PHP_action=main","main","104");#7-8
$A_Item[$i][$m++][0]=array("txt","Storage-transfer","成品倉-轉倉&#13;Storage-transfer","storage.php?PHP_action=transfer","main","110");#7-8
// $A_Item[$i][$m++][0]=array("txt","Fabric","舊庫存主料","stock_old_mat.php?PHP_action=main","main","103");#7-8
// $A_Item[$i][$m++][0]=array("txt","Acc","舊庫存副料","stock_old_mat.php?PHP_action=acc","main","103");#7-9


$i++;	$m=0;
$A_Item[$i][$m++]=array("dir","QC REPORT");#
$A_Item[$i][$m++][0]=array("txt","Main Fabric","Main Fabric","rcv_rpt.php?PHP_action=lots_rpt","main","044");#8-1
$A_Item[$i][$m++][0]=array("txt","Cutting","Cutting","sorry.html","main","");#8-2
$A_Item[$i][$m++][0]=array("txt","In-Line","In-Line","sorry.html","main","");#8-3
$A_Item[$i][$m++][0]=array("txt","Buyer","Buyer","ord_qc.php?PHP_action=ord_qc","main","036");#8-4

$i++;	$m=0;
$A_Item[$i][$m++]=array("dir","SHIPPING");#
$A_Item[$i][$m++][0]=array("txt","P/L","P/L","shipdoc.php?PHP_action=packing","main","032");##
$A_Item[$i][$m++][0]=array("txt","CFM P/L","Confirm P/L","shipdoc.php?PHP_action=cfm_packing","main","033");#
// $A_Item[$i][$m++][0]=array("txt","ShpDoc","ShpDoc","shipdoc.php?PHP_action=shipdoc","main","034");#
// $A_Item[$i][$m++][0]=array("txt","ShpDoc cfm","ShpDoc Confirm","shipdoc.php?PHP_action=cfm_shipdoc","main","035");#
$A_Item[$i][$m++][0]=array("txt","ShipDoc Invoice Import","ShipDoc Invoice Import","invoice_import.php?PHP_action=main","main","108");
$A_Item[$i][$m++][0]=array("txt","ShipDoc","Shipping Document","shipping.php?PHP_action=main","main","073");#9-1
//$A_Item[$i][$m++][0]=array("txt","ShipDoc cfm","Shipping Document Confirm","shipping_cfm.php?PHP_action=main","main","074");#9-2
$A_Item[$i][$m++][0]=array("txt","ShipDoc cfm(NEW)","Shipping Document Confirm(NEW)","shipping_cfm.php?PHP_action=main_new","main","109");#9-2
//$A_Item[$i][$m++][0]=array("txt","ShipDoc cfm(NEW)","Shipping Document Confirm(NEW)","shipping_cfm.php?PHP_action=main_new","main","109");#9-2

$i++;	$m=0;
$A_Item[$i][$m++]=array("dir","ACCOUNTING");#
$A_Item[$i][$m++][0]=array("txt","C.M.","Cutting & Making","cost.php?PHP_action=Remun","main","030");#10-1

$i++;	$m=0;
$A_Item[$i][$m++]=array("dir","MANAGEMENT");#
$A_Item[$i][$m++][0]=array("txt","Capacity","Capacity","$PHP_SELF?PHP_action=capacity_search","main","055");#11-1
$A_Item[$i][$m++][0]=array("txt","Order Static","Order Static","pdt_static.php?PHP_action=ord_static","main","056");#11-2
$A_Item[$i][$m++][0]=array("txt","Order Summary","Order Summary","pdt_static.php?PHP_action=pdt_search","main","057");#11-3
$A_Item[$i][$m++][0]=array("txt","PDC. Analysis","Production Analysis","production_analysis.php?PHP_action=main","main","101");
$A_Item[$i][$m++][0]=array("txt","Order BreakDown","Order BreakDown","order_breakdown.php?PHP_action=main","main","092");#11-4
$A_Item[$i][$m++][0]=array("txt","Cost BreakDown","Cost BreakDown","cost_breakdown.php?PHP_action=main","main","096");#11-4
$A_Item[$i][$m++][0]=array("txt","Order Analysis","Order Analysis","cost_analysis.php?PHP_action=cost_analysis","main","058");#11-5
$A_Item[$i][$m++][0]=array("txt","Exceptional","Exceptional","exception.php?PHP_action=exception","main","069");#11-6
$A_Item[$i][$m++][0]=array("txt","Un Out-Put","Un Out-Put","sorry.html","main","");#11-7
$A_Item[$i][$m++][0]=array("txt","Reports","Reports","$PHP_SELF?PHP_action=out_search","main","072");#

$i++;	$m=0;
$A_Item[$i][$m++]=array("dir","USER");#
$A_Item[$i][$m++][0]=array("txt","Team Setting","Team Setting","$PHP_SELF?PHP_action=team","main","045");#12-1
$A_Item[$i][$m++][0]=array("txt","User Setting","User Setting","$PHP_SELF?PHP_action=user&PHP_sort=dept","main","046");#12-2
$A_Item[$i][$m++][0]=array("txt","Message","Message","notify.php?PHP_action=notify","main","048");#12-3
$A_Item[$i][$m++][0]=array("txt","Forum","Forum","forum.php?Ac=forum_index","main","079");#12-4

$i++;	$m=0;
$A_Item[$i][$m++]=array("dir","ADMIN.");#
$A_Item[$i][$m++][0]=array("txt","Parameter","Parameter","para.php?PHP_action=para_set","main","094");#13-1
$A_Item[$i][$m++][0]=array("txt","Department","Department","$PHP_SELF?PHP_action=dept","main","054");#13-2
$A_Item[$i][$m++][0]=array("txt","Access Log","Access Log","$PHP_SELF?PHP_action=log_admin","main","047");#13-3
$A_Item[$i][$m++][0]=array("txt","Currency","Currency","rate.php?PHP_action=rate","main","007");#13-4


$i++;	$m=0;
$A_Item[$i][$m++]=array("dir","OTHER.");#
$A_Item[$i][$m++][0]=array("txt","Fab. stock","Fabric stock","fabric.php?PHP_action=stock","main","052");#
$A_Item[$i][$m++][0]=array("txt","CUT PLAN","Cutting Plan","fty_marker.php?PHP_action=marker_search","main","063");#
$A_Item[$i][$m++][0]=array("txt","CUT REPORT","Cutting Report","fty_marker.php?PHP_action=marker_rpt_search","main","064");#
$A_Item[$i][$m++][0]=array("txt","OFFER","Offer","offer.php?PHP_action=offer","main","062");#
$A_Item[$i][$m++][0]=array("txt","Debit Note","Debit Note","dabit.php?PHP_action=debit","main","070");#
$A_Item[$i][$m++][0]=array("txt","SHIPPING","Shipping","ship.php?PHP_action=shipping","main","029");#
$A_Item[$i][$m++][0]=array("txt","Sales Cost","Sales Cost","cost.php?PHP_action=cost","main","031");#
$A_Item[$i][$m++][0]=array("txt","Budget","Budget","expense.php?PHP_action=expense","main","059");#
$A_Item[$i][$m++][0]=array("txt","Project","Project","project.php?PHP_action=main","main","060");#
$A_Item[$i][$m++][0]=array("txt","BOM APB","BOM APB","bom_po_apb.php?PHP_action=bom_apb_search","main","093");#
$A_Item[$i][$m++][0]=array("txt","Line OutPut","Line OutPut","line.php?PHP_action=main","main","081");#
$A_Item[$i][$m++][0]=array("txt","Receiving","Receiving","receive.php?PHP_action=rcvd","main","043");#
$A_Item[$i][$m++][0]=array("txt","CFM Rcvd.","Confirm Received","receive.php?PHP_action=rcvd_cfm_search","main","076");#
$A_Item[$i][$m++][0]=array("txt","TW Receiving","TW Receiving","tw_receive.php?PHP_action=rcvd","main","077");#
$A_Item[$i][$m++][0]=array("txt","CFM TW Rcvd.","Confirm TW Received","tw_receive.php?PHP_action=rcvd_cfm_search","main","078");#
$A_Item[$i][$m++][0]=array("txt","I E","Industrial Engineering","ie.php?PHP_action=IE_record","main","025");#
$A_Item[$i][$m++][0]=array("txt","FTY Assort","Factory Assort","fty_bom.php?PHP_action=fty_bom","main","024");#
$A_Item[$i][$m++][0]=array("txt","Confirm Sample","Confirm Sample","sample.php?PHP_action=cfm_smpl","main","080");#
$A_Item[$i][$m++][0]=array("txt","SCH. rpt.","SCH. rpt.","sorry.html","main","017");


$A_Item[$i][$m++][0]=array("txt","------","------","acc_cat.php?PHP_action=acc_cat","main","051");#
$A_Item[$i][$m++][0]=array("txt","Old Supplier","Old Supplier","$PHP_SELF?PHP_action=tmp_supl&PHP_supl_f_name=&PHP_vndr_no","main","005");#

$i++;	$m=0;
$A_Item[$i][$m++]=array("dir","Morial_test");#
$A_Item[$i][$m++][0]=array("txt","Packing_list","包裝","packing_list.php?PHP_action=main","main","999");#
$A_Item[$i][$m++][0]=array("txt","Packing_edit","Packing_edit","Packing_edit.php?PHP_action=main","main","9999");#

$_SESSION["ITEM"]["A_Item"] = $A_Item;



/*----------
# 功能說明 : 系統權限，在tEAM & uSER 設定時用到
# "view" , "add" , "edit" , "del" , "submit" , "confirm" , "review" , "approval" , "revise" , "reopen" , "updata"
----------*/
$_SESSION['ITEM']['ADMIN_PERM_DEF'] = array( "view" => 1 , "add" => 2 , "edit" => 4 , "del" => 8 , "admin" => 16 );

/*----------
# 功能說明 : 系統權限，每頁的權限判斷，打陣列裡的 key 直代表
----------*/
$_SESSION['ITEM']['ADMIN_PERM'] = array( '1' => "view" , '2' => "add" , '3' => "edit" , '4' => "del" , '5' => "admin" );

/*----------
# 功能說明 : 系統權限，每頁的最大權限
----------*/
$_SESSION['full_key'] = 0;
foreach ($_SESSION['ITEM']['ADMIN_PERM_DEF'] AS  $key => $val  ) $_SESSION['full_key'] = $_SESSION['full_key'] | $val;

/*----------
# 功能說明 : 工廠國定假日設定
----------*/
session_register	('hd_ary');
$_SESSION['hd_ary'] = array();
$_SESSION['hd_ary']['LY']['2014']['01'] = array ('01','27','28','29','30','31');
$_SESSION['hd_ary']['LY']['2014']['02'] = array ('01','02','03','04','05','06','07','08');
$_SESSION['hd_ary']['LY']['2014']['04'] = array ('09','30');
$_SESSION['hd_ary']['LY']['2014']['05'] = array ('01');
$_SESSION['hd_ary']['LY']['2014']['09'] = array ('01','02');
$_SESSION['hd_ary']['LY']['2014']['10'] = array ('13','14','15','16','17','18','19');
$_SESSION['hd_ary']['LY']['2015']['04'] = array ('28'=>1,'29'=>1,'30'=>1);
$_SESSION['hd_ary']['LY']['2015']['05'] = array ('01'=>1,'02'=>1);

$_SESSION['hd_ary']['HJ']['2014']['01'] = array ('01','31');
$_SESSION['hd_ary']['HJ']['2014']['02'] = array ('01','02','03','04','05','06');
$_SESSION['hd_ary']['HJ']['2014']['04'] = array ('05','06','07');
$_SESSION['hd_ary']['HJ']['2014']['05'] = array ('01','02','03','31');
$_SESSION['hd_ary']['HJ']['2014']['06'] = array ('01','02');
$_SESSION['hd_ary']['HJ']['2014']['09'] = array ('06','07','08');
$_SESSION['hd_ary']['HJ']['2014']['10'] = array ('01','02','03','04','05','06','07');

$_SESSION['hd_ary']['CF']['2014']['01'] = array ('01','14','31');
$_SESSION['hd_ary']['CF']['2014']['02'] = array ('25');
$_SESSION['hd_ary']['CF']['2014']['03'] = array ('20');
$_SESSION['hd_ary']['CF']['2014']['04'] = array ('09','17','18','19','20');
$_SESSION['hd_ary']['CF']['2014']['05'] = array ('01','27');
$_SESSION['hd_ary']['CF']['2014']['06'] = array ('12','21');
$_SESSION['hd_ary']['CF']['2014']['07'] = array ('30');
$_SESSION['hd_ary']['CF']['2014']['08'] = array ('21','24','25');
$_SESSION['hd_ary']['CF']['2014']['09'] = array ('23');
$_SESSION['hd_ary']['CF']['2014']['10'] = array ('05');
$_SESSION['hd_ary']['CF']['2014']['11'] = array ('01','02','30');
$_SESSION['hd_ary']['CF']['2014']['12'] = array ('21','24','25','26','30','31');


/*----------
# 功能說明 : MENU CSS
----------*/
define('TXT_css_1', 'F6');
define('TXT_css_2', 'F5');



#彈跳照片，自動調整視窗大小
$TPL_POPUP_IMG = "popup_img.html";

$TPL_PRINT_TEST = "test_print.html";

$TPL_ADMIN_LOG_ADMIN = "log_admin.index.html";
$TPL_ADMIN_LOG_ADMIN_SHOW = "log_admin_show.html";
$TPL_ADMIN_LOG_CFM_DEL = "log_cfm_del.html";
$TPL_BACK_LOG = "back_log.html";
$TPL_CHANGE_FORM = "changepass.html";

//20060904
$TPL_SMPL_ORD = "smpl_ord.html";
$TPL_SMPL_ORD_ADD = "smpl_ord_add.html";
$TPL_SMPL_ORD_SHOW = "smpl_ord_show.html";
$TPL_SMPL_ORD_EDIT = "smpl_ord_edit.html";
$TPL_SMPL_ORD_LIST = "smpl_ord_list.html";
$TPL_SMPL_SUPPLIER = "smpl_supplier.html";
$TPL_SMPL_SUPPLIER_LIST = "smpl_supplier_list.html";
$TPL_SMPL_SUPPLIER_SHOW = "smpl_supplier_show.html";

$TPL_SMPL_SCHEDULE = "smpl_schedule.html";
$TPL_SMPL_SCHEDULE_LIST = "smpl_schedule_list.html";
$TPL_SMPL_SCHEDULE_SHOW = "smpl_schedule_show.html";

$TPL_SMPL_OUTPUT = "smpl_output.html";
$TPL_SMPL_OUTPUT_LIST = "smpl_output_list.html";
$TPL_SMPL_OUTPUT_SHOW = "smpl_output_show.html";
$TPL_SMPL_ORD_FOLLOW = "smpl_ord_follow.html";

$TPL_OFFER = "offer.html";
$TPL_OFFER_SHOW = "offer_view.html";
$TPL_OFFER_EDIT = "offer_edit.html";
$TPL_OFFER_RD_EDIT = "offer_rd_edit.html";
$TPL_OFFER_SA_EDIT = "offer_sa_edit.html";
$TPL_OFFER_LIST = "offer_list.html";
$TPL_OFFER_ADD = "offer_add.html";
$TPL_OFFER_REV = "offer_rev.html";
$TPL_OFFER_RD_REV = "offer_rd_rev.html";
$TPL_OFFER_SA_REV = "offer_sa_rev.html";

$TPL_ORDER = "order.html";
$TPL_ORDER_LIST = "order_list.html";
$TPL_ORDER_SHOW = "order_show.html";
$TPL_ORDER_ADD = "order_add.html";
$TPL_ORDER_EDIT = "order_edit.html";
$TPL_ORDER_REVISE = "order_revise.html";
$TPL_ORDER_REVISE_SHOW = "order_revise_show.html";

$TPL_ORDER_SHIFT = "order_shift.html";


$TPL_UNCFM_SCHEDULE_LIST = "uncfm_schedule_list.html";
$TPL_CFM_SCHEDULE_SHOW = "schedule_cfm_show.html";

$TPL_UNCFM_ORDER_LIST = "uncfm_order_list.html";
$TPL_ORDER_SHOW_CFM = "order_show_cfm.html";

$TPL_UNREV_ORDER_LIST = "unrev_order_list.html";
$TPL_ORDER_SHOW_REV = "order_show_rev.html";

$TPL_UNAPV_ORDER_LIST = "unapv_order_list.html";
$TPL_ORDER_SHOW_APV = "order_show_apv.html";



$TPL_LOGIN = "index0.html";
$TPL_MAIN = "index.main.php";

$TPL_CUST = "cust.html";
$TPL_CUST_ADD = "cust_add.html";
$TPL_CUST_VIEW = "cust_view.html";
$TPL_CUST_EDIT = "cust_edit.html";

$TPL_SUPL = "supl.html";
$TPL_SUPL_SEARCH = "supl_search.html";
$TPL_SUPL_ADD = "supl_add.html";
$TPL_SUPL_VIEW = "supl_view.html";
$TPL_SUPL_EDIT = "supl_edit.html";

$TPL_LOTS = "lots.html";
$TPL_LOTS_ADD = "lots_add.html";
$TPL_LOTS_VIEW = "lots_view.html";
$TPL_LOTS_EDIT = "lots_edit.html";

$TPL_ACC = "acc.html";
$TPL_ACC_ADD = "acc_add.html";
$TPL_ACC_VIEW = "acc_view.html";
$TPL_ACC_EDIT = "acc_edit.html";

$TPL_SMPL = "smpl.html";
$TPL_SMPL_SEARCH = "smpl_search.html";
$TPL_SMPL_ADD = "smpl_add.html";
$TPL_SMPL_VIEW = "smpl_view.html";
$TPL_SMPL_EDIT = "smpl_edit.html";
$TPL_SMPL_LIST = "smpl_list.html";  // 製造令時使用
$TPL_SMPL_COPY = "smpl_copy.html";

$TPL_SMPL_MAT_SEARCH = "smpl_mat_search.html";
$TPL_SMPL_MAT = "smpl_mat.html";    // 樣本列表
$TPL_SMPL_MAT_ADD = "smpl_mat_add.html";   // 樣本 主料窗
$TPL_LOTS_SEARCH = "lots_search.html";     // 樣本 資料錄查尋窗
$TPL_LOTS_LIST = "lots_list.html";    // 另開新窗 用於加入母窗之主料代號欄位[子窗]
$TPL_SMPL_MAT_VIEW = "smpl_mat_view.html";

$TPL_SMPL_ACC_SEARCH = "smpl_acc_search.html";
$TPL_SMPL_ACC = "smpl_acc.html";    // 樣本列表
$TPL_SMPL_ACC_ADD = "smpl_acc_add.html";   // 樣本 副料窗
$TPL_ACC_SEARCH = "acc_search.html";     // 樣本 資料錄查尋窗
$TPL_ACC_LIST = "acc_list.html";    // 另開新窗 用於加入母窗之副料代號欄位[子窗]
$TPL_SMPL_ACC_VIEW = "smpl_acc_view.html";

$TPL_WI = "wi_list2.html";
$TPL_WI_SEARCH = "wi_search.html";
$TPL_WI_ADD = "wi_add.html";
$TPL_WI_ADD2 = "wi_add2.html";
$TPL_WI_VIEW = "wi_view.html";
$TPL_WI_EDIT = "wi_edit.html";
$TPL_WI_UNCFM_LIST = "wi_uncfm_list.html";
$TPL_WI_REVISE = "wi_revise.html";
$TPL_WI_REVISE_SHOW = "wi_revise_show.html";
$TPL_WI_CFM_VIEW = "wi_cfm_view.html";
$TPL_WI_QTY_EDIT = "wi_qty_edit.html";

$TPL_TI = "ti.html";
$TPL_TI_SEARCH = "ti_search.html";
$TPL_TI_ADD = "ti_add.html";
$TPL_TI_VIEW = "ti_view.html";

$TPL_BOM = "bom.html";
$TPL_BOM_SEARCH = "bom_search.html";
$TPL_BOM_ADD = "bom_add.html";
$TPL_BOM_VIEW = "bom_view.html";
$TPL_BOM_UNCFM_LIST="bom_uncfm_list.html";
$TPL_BOM_CFM_EDIT = "bom_cfm_edit.html";
$TPL_BOM_REVISE_SHOW = "bom_revise_show.html";
$TPL_BOM_CFM_VIEW = "bom_cfm_view.html";
$TPL_BOM_CON_ADD = "bom_con_add.html";

//BOM copy
$TPL_BOM_COPY_SEARCH = "bom_copy_search.html";
$TPL_BOM_COPY_VIEW = "bom_copy_view.html";

$TPL_DEPT = "dept.html";
$TPL_DEPT_UPDATE = "dept.update.html";

$TPL_SMPL_TYPE = "smpl_type.html";
$TPL_SMPL_TYPE_UPDATE = "smpl_type.update.html";

$TPL_STYLE_TYPE = "style_type.html";
$TPL_STYLE_TYPE_UPDATE = "style_type.update.html";

$TPL_SEASON = "season.html";
$TPL_SEASON_UPDATE = "season.update.html";

$TPL_SIZE_DES = "size_des.html";
$TPL_SIZE_DES_ADD = "size_des_add.html";
$TPL_SIZE_DES_EDIT = "size_des_edit.html";
$TPL_SIZE_DES_LIST = "size_des_list.html";
$TPL_SIZE_DES_SHOW = "size_des_show.html";

$TPL_SIZE_TYPE = "size_type.html";    //TEMP?????????????
$TPL_SIZE_TYPE_UPDATE = "size_type.update.html"; // TEMP??????????

$TPL_SUPL_TYPE = "supl_type.html";
$TPL_SUPL_TYPE_UPDATE = "supl_type.update.html";

$TPL_TEAM = "team.html";
$TPL_TEAM_ADD = "team_add.html";
$TPL_TEAM_VIEW = "team_view.html";
$TPL_TEAM_EDIT = "team_edit.html";

$TPL_USER = "user.html";
$TPL_USER_ADD = "user_add.html";
$TPL_CFM_USER_ADD = "user_cfm_add.html";
$TPL_USER_VIEW = "user_view.html";
$TPL_USER_EDIT = "user_edit.html";

$TPL_FABRIC = "fabric.html";
$TPL_FABRIC_SEARCH = "fabric_search.html";
$TPL_FABRIC_ADD = "fabric_add.html";
$TPL_FABRIC_VIEW = "fabric_view.html";
$TPL_FABRIC_EDIT = "fabric_edit.html";

$TPL_FABRIC_PRINT_SEARCH = "fabric_print_search.html";
$TPL_FABRIC_PRINT_LIST = "fabric_print_list.html";

$TPL_AG_SEARCH	= "ag_search.html";
$TPL_AG			= "ag.html";
$TPL_AG_ADD = "ag_add.html";

$TPL_SCHEDULE = "schedule.html";
$TPL_SCHEDULE_ORDER_LIST = "schedule_order_list.html"; 
$TPL_SCHEDULE_SHOW = "schedule_show.html";

$TPL_ORD_SCHEDULE = "ord_schedule.html";
$TPL_ORD_SCHEDULE_LIST = "ord_schedule_list.html";
$TPL_ORD_SCHEDULE_SHOW = "ord_schedule_show.html";

$TPL_MAT_SCHEDULE = "mat_schedule.html";
$TPL_MAT_SCHEDULE_LIST = "mat_schedule_list.html";
$TPL_MAT_SCHEDULE_SHOW = "mat_schedule_show.html";

$TPL_PRODUCTION = "production.html";
$TPL_PRODUCTION_LIST = "production_list.html";  
$TPL_SHIFT_SHOW = "shift_show.html";

$TPL_SHIPPING = "shipping.html";
$TPL_SHIPPING_LIST = "shipping_list.html";  

$TPL_CAPACITY_SEARCH = "capacity_search.html";
$TPL_ADD_CAPACITY = "add_capacity.html";
$TPL_CAPACITY_VIEW = "capacity.view.html";
$TPL_CAPACITY_UPDATE = "capacity_update.html";

$TPL_SMPL_APV_REC = "smpl_apv_rec.html";
$TPL_SMPL_APV_REC_LIST = "smpl_apv_rec_list.html";
$TPL_SMPL_APV_REC_SHOW = "smpl_apv_rec_show.html";

$TPL_IE = "IE.html";
$TPL_IE_LIST = "IE_list.html";
$TPL_IE_REC_SHOW = "IE_rec_show.html";

$TPL_MONTHY_DAILY_OUTPUT = "monthy_daily_output.html";
$TPL_DAILY_OUTPUT = "daily_output.html";
$TPL_ORDER_OUTPUT = "order_output.html";
$TPL_ETP_OUTPUT = "etp_output.html";
$TPL_FTY_SCHEDULE = "fty_schedule.html";
$TPL_FTY_SCHEDULE2 = "fty_schedule2.html";
$TPL_MON_SHP = "mon_shp.html";

$TPL_FORECAST =	"forecast.html";
$TPL_FORECAST_SEARCH =	"forecast_search.html";
$TPL_FORECAST_ADD =		"forecast_add.html";
$TPL_FORECAST_UPDATE =	"forecast_update.html";
$TPL_FORECAST_SHOW	= "forecast_show.html";

$TPL_ORDER_FA = "order_fa.html";
$TPL_ORDER_FA_LIST = "order_fa_list.html";
$TPL_ORDER_FA_EDIT = "order_fa_edit.html";
$TPL_ORDER_FA_SHOW = "order_fa_show.html";
$TPL_LOT_S_LIST = "lot_s_list.html";
$TPL_ACC_S_LIST = "acc_s_list.html";

$TPL_SMPL_FA = "smpl_fa.html";
$TPL_SMPL_FA_LIST = "smpl_fa_list.html";
$TPL_SMPL_FA_EDIT = "smpl_fa_edit.html";
$TPL_SMPL_FA_SHOW = "smpl_fa_show.html";

$TPL_SMPL_WI = "smpl_wi_list2.html";
$TPL_SMPL_WI_SEARCH = "smpl_wi_search.html";
$TPL_SMPL_WI_ADD = "smpl_wi_add.html";
$TPL_SMPL_WI_ADD2 = "smpl_wi_add2.html";
$TPL_SMPL_WI_VIEW = "smpl_wi_view.html";
$TPL_SMPL_WI_EDIT = "smpl_wi_edit.html";
$TPL_SMPL_WI_UNCFM_LIST = "smpl_wi_uncfm_list.html";
$TPL_SMPL_WI_REVISE = "smpl_wi_revise.html";
$TPL_SMPL_WI_REVISE_SHOW = "smpl_wi_revise_show.html";
$TPL_SMPL_WI_LIST = "smpl_wi_list.html";
$TPL_SMPL_WI_CFM_VIEW = "smpl_wi_cfm_view.html";
$TPL_SMPL_WI_QTY_EDIT = "smpl_wi_qty_edit.html";
$TPL_SMPL_WI_COPY_SEARCH = "smpl_wi_copy_search.html";

$TPL_SMPL_BOM = "smpl_bom.html";
$TPL_SMPL_BOM_SEARCH = "smpl_bom_search.html";
$TPL_SMPL_BOM_ADD = "smpl_bom_add.html";
$TPL_SMPL_BOM_VIEW = "smpl_bom_view.html";
$TPL_SMPL_BOM_UNCFM_LIST="smpl_bom_uncfm_list.html";
$TPL_SMPL_BOM_CFM_EDIT = "smpl_bom_cfm_edit.html";
$TPL_SMPL_BOM_REVISE_SHOW = "smpl_bom_revise_show.html";
$TPL_SMPL_BOM_CFM_VIEW = "smpl_bom_cfm_view.html";
$TPL_SMPL_BOM_CON_ADD = "smpl_bom_con_add.html";

$TPL_SMPL_TI_ADD = "smpl_ti_add.html";
$TPL_SMPL_TI_VIEW = "smpl_ti_view.html";

$TPL_SMPL_SIZE_LIST = "smpl_size_list.html";
$TPL_SMPL_SIZE_ADD = "smpl_size_add.html";


$TPL_TMP_SUPL = "tmp_supl.html";

$TPL_ADMIN_LOGIN_FORM = "index.html";

//2007-04
$TPL_OUT_VIEW = "out_view.html";
$TPL_OUT_SEARCH = "out_search.html";
$TPL_RPT_SCH_VIEW= "rpt_sch_view.html";
$TPL_UNETP_OUTPUT= "unetp_output.html";
$TPL_CUST_OS="rpt_cust_os.html";
$TPL_SMPL_CUST="rpt_smpl_cust.html";
$TPL_UNFINISH_SAMPLE="unfinish_sample.html";
$TPL_RPT_ORD_COUNT="rpt_ord_count.html";
$TPL_RPT_ORD_COUNT_DET="rpt_ord_count_det.html";
$TPL_RPT_SMPL_USE_COUNT="rpt_smpl_use_count.html";
$TPL_RPT_SMPL_USE_DET="rpt_smpl_use_det.html";
$TPL_RPT_STYLE_OUT="rpt_style_out.html";
$TPL_RPT_ORD_DIST="rpt_ord_dist.html";

$TPL_SUPNO="supno.htm";

$TPL_APLY="aply.html";
$TPL_APLY_ADD="aply_add.html";
$TPL_APLY_LIST="aply_list.html";
$TPL_APLY_EDIT="aply_edit.html";
$TPL_APLY_SHOW="aply_show.html";
$TPL_APLY_REVISE="aply_revise.html";
$TPL_APLY_REVISE_SHOW="aply_revise_show.html";
$TPL_APLY_APV_LIST="aply_apv_list.html";
$TPL_APLY_APV_SHOW="aply_apv_show.html";
$TPL_APLY_BOM_LIST="aply_bom_list.html";
$TPL_APLY_VIEW_WI="aply_view_wi.html";
$TPL_APLY_ADD_SCH = "aply_add_sch.html";
$TPL_APLY_VIEW_WI_SUB="aply_view_wi_sub.html";
$TPL_APLY_SPC_BOM_LIST="aply_spc_bom_list.html";
//$TPL_APLY_SPC_VIEW_WI="aply_spc_view_wi.html";
$TPL_APLY_SPC_VIEW_BOM="aply_spce_view_bom.html";
$TPL_APLY_EXT_VIEW_BOM="aply_ext_view_bom.html";
$TPL_APLY_SPC_ADD = "aply_spc_add.html";
$TPL_APLY_EXT_ADD = "aply_ext_add.html";
$TPL_APLY_CFM_LIST = "aply_cfm_list.html";
$TPL_APLY_SHOW_CFM = "aply_show_cfm.html";
$TPL_APLY_APV_LIST = "aply_apv_list.html";
$TPL_APLY_SHOW_APV = "aply_show_apv.html";
$TPL_APLY_SHOW_REV = "aply_show_rev.html";

$TPL_PO = "po.html";
$TPL_PO_BOM_LIST = "po_bom_list.html";
$TPL_PO_BOM_VIEW = "po_bom_view.html";
$TPL_PO_ADD = "po_add.html";
$TPL_PO_SHOW = "po_show.html";
$TPL_PO_LIST = "po_list.html";
$TPL_PO_CFM_LIST = "po_cfm_list.html";
$TPL_PO_SHOW_CFM = "po_show_cfm.html";
$TPL_PO_APV_LIST = "po_apv_list.html";
$TPL_PO_SHOW_APV = "po_show_apv.html";
$TPL_PO_SHOW_REV = "po_show_rev.html";
$TPL_PO_TRACK_SHOW = "po_track_show.html";

$TPL_RCVD ="rcvd.html";
$TPL_RCV_ADD = "rcv_add.html";
$TPL_RCV_SHOW = "rcv_show.html";
$TPL_RCV_LIST = "rcv_list.html";
$TPL_RCV_EDIT = "rcv_edit.html";
$TPL_RCV_REVISE = "rcv_revise.html";
$TPL_RCV_PO_LIST = "rcv_po_list.html";
$TPL_RCV_RECV_SHOW = "rcv_rcvd_show.html";
$TPL_RCV_CFM_LIST = "rcv_cfm_list.html";
$TPL_RCV_CFM_SHOW = "rcv_cfm_show.html";

$TPL_TW_RCVD ="tw_rcvd.html";
$TPL_TW_RCV_PO_LIST = "tw_rcv_po_list.html";
$TPL_TW_RCV_ADD = "tw_rcv_add.html";
$TPL_TW_RCV_SHOW = "tw_rcv_show.html";
$TPL_TW_RCV_EDIT = "tw_rcv_edit.html";
$TPL_TW_RCV_LIST = "tw_rcv_list.html";
$TPL_TW_RCV_CFM_LIST = "tw_rcv_cfm_list.html";
$TPL_TW_RCV_CFM_SHOW = "tw_rcv_cfm_show.html";

$TPL_APB ="apb.html";
$TPL_APB_ADD = "apb_add.html";
$TPL_APB_SHOW = "apb_show.html";
$TPL_APB_LIST = "apb_list.html";
$TPL_APB_EDIT = "apb_edit.html";
$TPL_APB_REVISE = "apb_revise.html";
$TPL_APB_PO_LIST = "apb_po_list.html";
$TPL_APB_RECV_SHOW = "rcv_rcvd_show.html";
$TPL_APB_PO_SHOW = "rcv_rcvd_show.html";
$TPL_APB_SUP_LIST = "apb_sup_list.html";
$TPL_APB_PO_BEFORE_LIST = "apb_po_before_list.html";
$TPL_APB_PO_BEFORE_ADD = "apb_po_before_add.html";
$TPL_APB_PO_BEFORE_SHOW = "apb_po_before_show.html";
$TPL_APB_PO_BEFORE_EDIT = "apb_po_before_edit.html";
$TPL_PDT_SEARCH="pdt_search.html";
$TPL_PDT_VIEW="pdt_view.html";
$TPL_ETP_ORD="etp_ord.html";
$TPL_ETP_SCH="etp_sch.html";
$TPL_ETP_OUT="etp_out.html";
$TPL_ETP_UNSHP="etp_unshp.html";
$TPL_ETP_MON_SHP="etp_mon_shp.html";
$TPL_ETP_MON_OUT="etp_mon_out.html";
$TPL_ETP_MON_UNSHP="etp_mon_unshp.html";
$TPL_ETP_MON_UNOUT="etp_mon_unout.html";

$TPL_NOTIFY="notify.html";
$TPL_NOTIFY_SEND="notify_send.html";
$TPL_USER_SEARCH="user_search.html";

$TPL_EX_SUPL_ADD = "ex_supl_add.html";
$TPL_EX_SUPL 	 = "ex_supl_view.html";

$TPL_CUST_SHIFT = "cust_shift.html";

$TPL_LOTS_SUB_ADD = "lots_sub_add.html";
$TPL_LOTS_SUB_VIEW = "lots_sub_view.html";

$TPL_ACC_SUB_ADD = "acc_sub_add.html";
$TPL_ACC_SUB_VIEW = "acc_sub_view.html";

$TPL_SMPL_WI_COPY_SEARCH = "smpl_wi_copy_search.html";
$TPL_SMPL_WI_COPY_VIEW = "smpl_wi_copy_view.html";

$TPL_SMPL_TI_COPY_SEARCH = "smpl_ti_copy_search.html";
$TPL_SMPL_TI_COPY_SEARCH2 = "smpl_ti_copy_search2.html";
$TPL_SMPL_TI_COPY_VIEW = "smpl_ti_copy_view.html";
$TPL_SMPL_TI_COPY_VIEW2 = "smpl_ti_copy_view2.html";
$TPL_ORD_TI_COPY_SEARCH = "ord_ti_copy_search.html";
$TPL_ORD_TI_COPY_VIEW = "ord_ti_copy_view.html";



$TPL_OUT_PUT_LC = "out_put_lc.html";

$TPL_BOM_PA_LIST = "bom_pa_list.html";
$TPL_BOM_PA_VIEW = "bom_pa_view.html";
$TPL_BOM_PO_VIEW = "bom_po_view.html";
$TPL_BOM_PO_STATUS_VIEW = "bom_po_status_view.html";
$TPL_APLY_ORD_LIST = "aply_ord_list.html";
$TPL_APLY_ORD_SHOW = "aply_ord_show.html";

$TPL_ORD_FANL_VIEW = "ord_fanl_view.html";

//monitor
$TPL_MONITOR = "monitor.html";
$TPL_MONITOR_ADD = "monitor_add.html";
$TPL_MONITOR_EDIT = "monitor_edit.html";
$TPL_MONITOR_LIST = "monitor_list.html";
$TPL_MONITOR_SHOW = "monitor_show.html";
$TPL_ORDER_S_LIST = "order_s_list.html";
$TPL_MONITOR_MAIN = "monitor_main.html";
$TPL_SAW = "saw.html";
$TPL_SAW_ADD = "saw_add.html";
$TPL_SAW_DAILY_VIEW = "saw_daily_view.html";
$TPL_SAW_LIST ="saw_list.html";
$TPL_SAW_REPORT = "saw_report.html";
$TPL_SAW_REPORT_SU = "saw_report_su.html";
$TPL_SAW_REPORT_PC = "saw_report_pc.html";
$TPL_SAW_LINE_ADD ="saw_line_add.html";
$TPL_SAW_LINE_EDIT ="saw_line_edit.html";
$TPL_SAW_ORD_PIC = "saw_ord_pic.html";
$TPL_SAW_LINE_PIC = "saw_line_pic.html";

//報表
$TPL_RCVD_RPT = "rcvd_rpt.html";
$TPL_RCV_RPT_USED = "rcv_rpt_used.html";
$TPL_RCV_RPT_DTL = "rcv_rpt_dtl.html";

//匯率
$TPL_RATE = "rate.html";
$TPL_RATE_UPDATE = "rate.update.html";

//代工費申請
$TPL_REMUN = "remun.html";
$TPL_REMUN_ADD = "remun_add.html";
$TPL_REMUN_SHOW = "remun_show.html";
$TPL_REMUN_LIST = "remun_list.html";
$TPL_REMUN_EDIT = "remun_edit.html";

//成本--業務
$TPL_SALESCOST = "salescost.html";
$TPL_SALESCOST_ADD = "salescost_add.html";
$TPL_SALESCOST_SHOW = "salescost_show.html";
$TPL_SALESCOST_LIST = "salescost_list.html";
$TPL_SALESCOST_edit = "salescost_edit.html";
$TPL_SALESCOST_CFM_LIST = "salescost_cfm_list.html";
$TPL_SALESCOST_CFM_SHOW = "salescost_cfm_show.html";
$TPL_SALESCOST_APV_LIST = "salescost_apv_list.html";
$TPL_SALESCOST_APV_SHOW = "salescost_apv_show.html";
$TPL_SALESCOST_SHOW_REV = "salescost_show_rev.html";

//Shipping報表
$TPL_SHIP_OUT_ADD = 'ship_out_add.html';
$TPL_SHIP_OUT_SHOW = 'ship_out_show.html';
$TPL_SHIP_OUT_CFM = 'ship_out_cfm.html';

//C.M.在capacity
$TPL_MON_CM = 'mon_cm.html';

//Para. Set
$TPL_PARA = "para.html";
$TPL_PARA_UPDATE = "para_update.html";

//報表新增 080506
$TPL_OUT_VIEW_ORD = "out_view_ord.html";
$TPL_ord_qty_RPT2 = "ord_qty_rpt2.html";

//Production新增
$TPL_SAW_ADM = "saw_adm.html";
$TPL_SAW_ADM_ADD = "saw_amd_add.html";

//accessory catagory
$TPL_ACC_CAT = "acc_cat.html";
$TPL_ACC_CAT_UPDATE ="acc_cat_update.html";

//Ord static新增080514
$TPL_ETP_UNAPV_ORD = "etp_unapv_ord.html";

//請購 -- 預先請購
$TPL_APLY_PP_LIST = "aply_pp_list.html";
$TPL_APLY_PP_ADD = "aply_pp_add.html";
$TPL_APLY_MATCH_PP = "aply_match_pp.html";

//請購 -- 輸入王安編號
$TPL_PO_ANN = "po_ann.html";
$TPL_PO_ANN_LIST = "po_ann_list.html";

//ADMIN -- Forcast
$TPL_ETP_MON_FCST = "etp_mon_fcst.html";

//匯率重做
$TPL_RATE_SEARCH = "rate_search.html";
$TPL_RATE_ADD = "rate_add.html";
$TPL_RATE_VIEW = "rate_view.html";
$TPL_RATE_EDIT = "rate_edit.html";

//Pre-Order
$TPL_PRE_ORDER = "pre_order.html";
$TPL_PRE_ORDER_ADD = "pre_order_add.html";
$TPL_PRE_ORDER_SHOW = "pre_order_show.html";
$TPL_PRE_ORDER_LIST = "pre_order_list.html";
$TPL_PRE_ORDER_EDIT = "pre_order_edit.html";
$TPL_PRE_ORDER_REVISE = "pre_order_revise.html";
$TPL_PRE_ORDER_REV_SHOW = "pre_order_rev_show.html";
$TPL_ETP_MON_PRE_ORD = "etp_mon_pre_ord.html";

//異常報告
$TPL_EXCEPTION = "exception.html";
$TPL_EXC_ADD = "exc_add.html";
$TPL_EXC_LIST = "exc_list.html";
$TPL_EXC_SHOW = "exc_show.html";
$TPL_EXC_EDIT = "exc_edit.html";
$TPL_EXC_COMM = "exc_comm.html";

//客戶增加搜尋08.07.07
$TPL_CUST_SEARCH = "cust_search.html";

//使用者增加搜尋08.07.07
$TPL_USER_MAIN = "user_main.html";

//Capactiy增加業務與工廠分開
$TPL_CAPACITY_DET = "capacity_det.html";

//客戶復制
$TPL_CUST_COPY_SEARCH = "cust_copy_search.html";
$TPL_CUST_COPY_VIEW = "cust_copy_view.html";

//WI複製part2
$TPL_WI_COPY_SEARCH = "wi_copy_search.html";
$TPL_WI_COPY_VIEW = "wi_copy_view.html";

//C.M.工繳confirm和approval
$TPL_REMUN_CFM_LIST = "remun_cfm_list.html";
$TPL_REMUN_CFM_SHOW = "remun_cfm_show.html";
$TPL_REMUN_REV_LIST = "remun_rev_list.html";
$TPL_REMUN_REV_SHOW = "remun_rev_show.html";
$TPL_REMUN_APV_LIST = "remun_apv_list.html";
$TPL_REMUN_APV_SHOW = "remun_apv_show.html";

//Setting for Message
$TPL_SET = "set.html";
$TPL_MESG_LIST = "mesg_list.html";
$TPL_MESG_SHOW = "mesg_show.html";
$TPL_MESG_EDIT = "mesg_edit.html";

//EXCEPTION CFM
$TPL_EXC_SHOW_CFM = "exc_show_cfm.html";

//EXCEPTION REV
$TPL_EXC_SHOW_REV = "exc_show_rev.html";

//異常報告核可
$TPL_EXC_SHOW_APV = "exc_show_apv.html";

//PO和驗收的LIST
$TPL_RCV_PO_SHOW = "rcv_po_show.html";

//PDT_STATIC (ORD改前後六個月)
$TPL_PDTS_SEARCH = "pdts_search.html";
$TPL_PDTS_VIEW = "pdts_view.html";
$TPL_PDTS_VIEW_DET = "pdts_view_det.html";

//工繳異常報告
$TPL_REM_EXC = "rem_exc.html";
$TPL_REM_EXC_ADD = "rem_exc_add.html";
$TPL_REM_EXC_SHOW = "rem_exc_show.html";
$TPL_REM_EXC_LIST = "rem_exc_list.html";
$TPL_REM_EXC_EDIT = "rem_exc_edit.html";
$TPL_REM_EXC_COMM = "rem_exc_comm.html";

//Ship document
$TPL_SHIPDOC = "shipdoc.html";

//訂單移轉
$TPL_PO_SHIFT_LIST = "po_shift_list.html";
$TPL_APLY_SHIFT_EDIT = "aply_shift_edit.html";
$TPL_PO_SHIFT_ADD = "po_shift_add.html";

//FORECAST
$TPL_FCST2 = "fcst2.html";
$TPL_FCST2_ADD = "fcst2_add.html";
$TPL_FCST2_SHOW = "fcst2_show.html";
$TPL_FCST2_SEARCH = "fcst2_search.html";
$TPL_FCST2_UPDATE = "fcst2_update.html";

//轉運單驗收
$TPL_RCV_TRANS_ADD_LIST = "rcv_trans_add_list.html";
$TPL_RCV_TRANS_ADD = "rcv_trans_add.html";
$TPL_RCV_TRANS_SHOW = "rcv_trans_show.html";
$TPL_RCV_TRANS_EDIT = "rcv_trans_edit.html";
$TPL_RCV_TRANS_SHOW_REV = "rcv_trans_show_rev.html";

//訂單的Pending list
$TPL_ORD_PRD_PEND = "ord_prd_pend.html";
$TPL_ORD_PRD_PEND_TOP = "ord_prd_pend_top.html";
$TPL_ORD_PRD_PEND_MAIN = "ord_prd_pend_main.html";

//Notic的系統alert
$TPL_NOTIC1 = "notic1.html";
$TPL_NOTIC2 = "notic2.html";

//採購單的pending list
$TPL_PO_PRD_PEND = "po_prd_pend.html";
$TPL_PO_PRD_PEND_TOP = "po_prd_pend_top.html";
$TPL_PO_PRD_PEND_MAIN = "po_prd_pend_main.html";

//訂單的Pending list--分frame
$TPL_ORD_PRD_TAL = "ord_prd_tal.html";
$TPL_ORD_PRD_TAL_TOP = "ord_prd_tal_top.html";
$TPL_ORD_PRD_TAL_MAIN = "ord_prd_tal_main.html";
$TPL_ORD_EXCEPTIONAL = "ord_exceptional.html";

//訂單成本分析
$TPL_ORD_COST_VIEW = "ord_cost_view.html";
$TPL_ORD_COST_SEARCH = "ord_cost_search.html";
$TPL_ORD_COST_LIST = "ord_cost_list.html";


//訂單主料用量建立
$TPL_FABCOMP_LIST = "fabcomp_list.html";
$TPL_FABCOMP_EDIT = "fabcomp_edit.html";
$TPL_FABCOMP_SHOW = "fabcomp_show.html";

//SHIPPING DOCUMENT08-12-26
$TPL_SHIPDOC_ADD_LIST = "shipdoc_add_list.html";
$TPL_SHIPDOC_PACK_ADD = "shipdoc_pack_add.html";
$TPL_SHIPDOC_ADD_SUB_LIST = "shipdoc_add_sub_list.html";
$TPL_SHIPDOC_PACK_VIEW = "shipdoc_pack_view.html";
$TPL_SHIPDOC_LIST = "shipdoc_list.html";
$TPL_SHIPDOC_PACK_EDIT = "shipdoc_pack_edit.html";
$TPL_SHIPDOC_INV_VIEW = "shipdoc_inv_view.html";
$TPL_SHIPDOC_INV_EDIT = "shipdoc_inv_edit.html";

//PO Ship
$TPL_PO_SHIP = "po_ship.html";
$TPL_PO_SHIP_ADD_LIST	= "po_ship_add_list.html";
$TPL_PO_SHIP_ADD = "po_ship_add.html";
$TPL_PO_SHIP_ADD_SHOW = "po_ship_add_show.html";
$TPL_PO_SHIP_SHOW = "po_ship_show.html";
$TPL_PO_SHIP_LIST	= "po_ship_list.html";
$TPL_PO_SHIPD_SHOW = "po_shipd_show.html";

//訂單修圖
$TPL_ORDER_PIC_EDIT = "order_pic_edit.html";

//OFFER APVD
$TPL_OFFER_VIEW_APV = "offer_view_apv.html";

//圖片跳出來
$TPL_POPUP_IMG = "popup_img.html";

//Fabric YY加search
$TPL_FABCOMP_SEARCH = "fabcomp_search.html";

//report生產線
$TPL_RPT_YEAR_PDT_LINE = "rpt_year_pdt_line.html";

//索賠
$TPL_DEBIT = "debit.html";
$TPL_DEBIT_ADD = "debit_add.html";
$TPL_DEBIT_SHOW = "debit_show.html";
$TPL_DEBIT_LIST = "debit_list.html";
$TPL_DEBIT_EDIT = "debit_edit.html";
$TPL_DEBIT_COMM = "debit_comm.html";
$TPL_DEBIT_SHOW_APV = "debit_show_apv.html";

//異常報告--選擇採購單
$TPL_EXC_PO_ADD_LIST = "exc_po_add_list.html";


//訂單出貨追蹤08-12-30
$TPL_ORD_SHIP_TAL = "ord_ship_tal.html";
$TPL_ORD_SHIP_TAL_MAIN = "ord_ship_tal_main.html";
$TPL_ORD_SHIP_TAL_TOP = "ord_ship_tal_top.html";

//生產報表分男女裝
$TPL_RPT_YEAR_PDT_LINE_DET = "rpt_year_pdt_line_det.html";

//SHIPPING DOCUMENT CONFIRM
$TPL_SHIPDOC_CFM = "shipdoc_cfm.html";
$TPL_SHIPDOC_LIST_CFM	= "shipdoc_list_cfm.html";
$TPL_SHIPDOC_PACK_VIEW_CFM = "shipdoc_pack_view_cfm.html";
$TPL_SHIPDOC_PACK_EDIT_CFM = "shipdoc_pack_edit_cfm.html";
$TPL_SHIPDOC_INV_VIEW_CFM = "shipdoc_inv_view_cfm.html";
$TPL_SHIPDOC_INV_EDIT_CFM = "shipdoc_inv_edit_cfm.html";
$TPL_SHIPDOC_LIST_APV	= "shipdoc_list_apv.html";
$TPL_SHIPDOC_PACK_VIEW_APV = "shipdoc_pack_view_apv.html";
$TPL_SHIPDOC_INV_VIEW_APV = "shipdoc_inv_view_apv.html";

//生產報表各線明細
$TPL_RPT_PDT_LINE = "rpt_pdt_line.html";

//consignee
$TPL_CONSIGNEE_SEARCH = "consignee_search.html";
$TPL_CONSIGNEE_ADD = "consignee_add.html";
$TPL_CONSIGNEE = "consignee.html";
$TPL_CONSIGNEE_VIEW = "consignee_view.html";
$TPL_CONSIGNEE_EDIT = "consignee_edit.html";
$TPL_CONSIGNEE_ADD_SUB = "consignee_add_sub.html";

//sales forecast
$TPL_SF_SEARCH = "sf_search.html";
$TPL_SF_ADD = "sf_add.html";

//forecast report
$TPL_FCST2_RPT = "fcst2_rpt.html";
$TPL_FCST2_RPT_ORD = "fcs2_rpt_ord.html";
$TPL_FCST2_RPT_SHIP = "fcs2_rpt_ship.html";

//fabric stock
$TPL_STOCK_SEARCH = "stock_search.html";
$TPL_STOCK = "stock.html";
$TPL_STOCK_ADD = "stock_add.html";
$TPL_STOCK_VIEW = "stock_view.html";
$TPL_STOCK_EDIT = "stock_edit.html";

//shipdoc 新增頁0323
$TPL_SHIPDOC_PACK_INBOX_ADD = "shipdoc_pack_inbox_add.html";
$TPL_SHIPDOC_PACK_INBOX_VIEW = "shipdoc_pack_inbox_view.html";
$TPL_SHIPDOC_PACK_INBOX_EDIT = "shipdoc_pack_inbox_edit.html";
$TPL_SHIPDOC_PACK_INBOX_VIEW_CFM = "shipdoc_pack_inbox_view_cfm.html";

//sample ship
$TPL_FTY_SMPL_SHIP = "fty_smpl_ship.html";
$TPL_FTY_SMPL_SHIP_ADD = "fty_smpl_ship_add.html";
$TPL_FTY_SMPL_SHIP_SHOW = "fty_smpl_ship_show.html";
$TPL_FTY_SMPL_SHIP_LIST = "fty_smpl_ship_list.html";
$TPL_SMPL_RCVD = "smpl_rcvd.html";
$TPL_SMPL_RCVD_ADD_LIST = "smpl_rcv_add_list.html";
$TPL_SMPL_RCVD_ADD = "smpl_rcv_add.html";
$TPL_SMPL_RCVD_SHOW = "smpl_rcv_show.html";
$TPL_SMPL_RCVD_LIST = "smpl_rcv_list.html";
$TPL_SMPL_RCVD_EDIT = "smpl_rcv_edit.html";
$TPL_SMPL_SHIP = "smpl_ship.html";
$TPL_SMPL_SHIP_ADD = "smpl_ship_add.html";
$TPL_SMPL_SHIP_SHOW = "smpl_ship_show.html";
$TPL_SMPL_SHIP_LIST = "smpl_ship_list.html";
$TPL_SMPL_SHIP_EDIT = "smpl_ship_edit.html";

$TPL_RPT_SMPL_FTY = "rpt_smpl_fty.html";

//2009-06-29 訂單匯總報表
$TPL_ETP_ORD_PRICE = "etp_ord_price.html";

//2009-07-07 驗布報告
$TPL_LOTS_RPT = "lots_rpt.html";
$TPL_LR_PO_LIST = 'lr_po_list.html';
$TPL_LR_ADD_SHOW = 'lr_add_show.html';
$TPL_LR_ADD = 'lr_add.html';
$TPL_LR_SHOW = 'lr_show.html';
$TPL_LR_LIST = 'lr_list.html';
$TPL_LR_EDIT = 'lr_edit.html';
$TPL_LR_ORD_LIST = 'lr_ord_list.html';

//OVERTIME
$TPL_OVERTIME = "overtime.html";
$TPL_OVERTIME_ADD = "overtime_add.html";
$TPL_OVERTIME_VIEW = "overtime_view.html";

//schedule
$TPL_SCH = "sch.html";
$TPL_SCH_ADD = "sch_add.html";
$TPL_SCH_VIEW = "sch_view.html";
$TPL_SCH_CFM = "sch_cfm.html";
$TPL_SCH_CFM_EDIT = "sch_cfm_edit.html";

//shipdoc 新增頁0826
$TPL_SHIPDOC_INV = "shipdoc_inv.html";
$TPL_SHIPDOC_INV_LIST = "shipdoc_inv_list.html";
$TPL_SHIPDOC_INV_LIST_CFM = "shipdoc_inv_list_cfm.html";
$TPL_SHIPDOC_INV_CFM= "shipdoc_inv_cfm.html";

//未驗收採購單列表(alert)
$TPL_UN_RCV = "un_rcv.html";
$TPL_UN_RCV_TOP = "un_rcv_top.html";
$TPL_UN_RCV_MAIN = "un_rcv_main.html";

//部門預算
$TPL_EXPENSE = "expense.html";
$TPL_EXPENSE_ADD = "expense_add.html";
$TPL_EXPENSE_VIEW = "expense_view.html";
$TPL_EXPENSE_EDIT = "expense_edit.html";
$TPL_EX_FORCAST_ADD = "ex_forcast_add.html";
$TPL_EX_FORCAST_VIEW = "ex_forcast_view.html";
$TPL_EX_FORCAST_EDIT = "ex_forcast_edit.html";
$TPL_EXPENSE_LIST = "expense_list.html";
$TPL_EXPENSE_ANALISIS = "expense_analysis.html";

//SHIPDOC INVOICE修改 09-11-08
$TPL_SHIPDOC_INV_EDIT_CHECK = "shipdoc_inv_edit_check.html";
$TPL_SHIPDOC_INV_EDIT_MULTI = "shipdoc_inv_edit_multi.html";

//用料報告
$TPL_REMNANT = "remnant.html";
$TPL_REMNANT_LIST = "remnant_list.html";
$TPL_REMNANT_VIEW = "remnant_view.html";
$TPL_REMNANT_EDIT = "remnant_EDIT.html";

//SHIPDOC PACKING修改 10-01-28
$TPL_SHIPDOC_PACK_BAR_ADD = "shipdoc_pack_bar_add.html";
$TPL_SHIPDOC_PACK_BAR_EDIT = "shipdoc_pack_bar_edit.html";
$TPL_SHIPDOC_PACK_BAR_VIEW = "shipdoc_pack_bar_view.html";
$TPL_SHIPDOC_PACK_BAR_VIEW_CFM = "shipdoc_pack_bar_view_cfm.html";

//URGENT 10-02-01
$TPL_URGENT = "urgent.html";
$TPL_URGENT_SEND = "urgent_send.html";

//SCHEDULE 訂單顯示10-02-25
$TPL_SCH_ORD_SHOW = "sch_ord_show.html";
$TPL_SCH_VIEW_SMALL = "sch_view_small.html";

//QC 10-04-01
$TPL_ORD_QC = "ord_qc.html";
$TPL_ORD_QC_LIST = "ord_qc_list.html";

//PO的出貨備註 10-04-26
$TPL_PO_SHIP_RMK = "po_ship_rmk.html";

//樣本排產
$TPL_SMPL_SCH = "smpl_sch.html";
$TPL_SMPL_SCH_ADD = "smpl_sch_add.html";
$TPL_SMPL_SCH_VIEW_SMALL = "smpl_sch_view_small.html";
$TPL_SMPL_SCH_ORD_SHOW = "smpl_sch_ord_show.html";

//訂單booking
$TPL_RPT_ORD_BOOKING = "rpt_ord_booking.html";

//訂單裁剪
$TPL_CUTTING = "cutting.html";
$TPL_CUTTING_LIST = "cutting_list.html";
$TPL_CUTTING_DAILY_LIST = "cutting_daily_list.html";
$TPL_CUTTING_ORD_LIST = "cutting_ord_list.html";
$TPL_CUTTING_DAILY_VIEW = "cutting_daily_view.html";

//訂單包裝
$TPL_PACKING = "packing.html";
$TPL_PACKING_LIST = "packing_list.html";
$TPL_PACKING_DAILY_LIST = "packing_daily_list.html";
$TPL_PACKING_ORD_LIST = "packing_ord_list.html";

$TPL_RPT_PDTION_QTY = "rpt_pdtion_qty.html";

//未完成排產 10-08-10
$TPL_SCH_UN_FINISH = "sch_un_finish.html";

//IE TYPE 10-09-01
$TPL_IE_TYPE = "ie_type.html";
$TPL_IE_MAIN_TYPE = "ie_main_type.html";
$TPL_IE_SUB_TYPE = "ie_sub_type.html";
$TPL_IE_DTL_TYPE = "ie_DTL_type.html";

//PO報表
$TPL_PO_FAB_RPT = "po_fab_rpt.html";
$TPL_PO_FAB_RPT_DET = "po_fab_rpt_det.html";

//庫存新增
$TPL_STOCK_BOM_LIST = "stock_bom_list.html";
$TPL_STOCK_VIEW_WI = "stock_view_wi.html";

$TPL_MARKER_VIEW_WI = "marker_view_wi.html";
$TPL_SEASON_ANALYSIS = "season_analysis.html";

//工廠BOM
$TPL_FTY_BOM_SEARCH = "fty_bom_search.html";
$TPL_FTY_BOM = "fty_bom.html";
$TPL_FTY_BOM_VIEW = "fty_bom_view.html";
$TPL_FTY_WI_EDIT = "fty_wi_edit.html";
$TPL_FTY_BOM_ADD = "fty_bom_add.html";




?>