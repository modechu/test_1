<?
	
define("DB_HOST","localhost");
define("DB_LOGIN","JackYang");
define("DB_PASSWORD","yankee610");
define("DB_NAME","scm_smpl");

# 連上資料庫
###############################################################################################################
function Connect_db(){
	if(empty($_SESSION["link_db"])){
		$_SESSION["link_db"] = @mysql_connect(DB_HOST, DB_LOGIN, DB_PASSWORD);
		if(!$_SESSION["link_db"]){
			echo ("FATAL:Couldn't connect to db.<br>");
			exit;
		}
	}

	$_SESSION["Select_DB"] = @mysql_select_db(DB_NAME, $_SESSION["link_db"]);
	if(!$_SESSION["Select_DB"]){
		echo ("FATAL:Couldn't connect to db.<br>");
		exit;
	}
    return $_SESSION["link_db"];
}
###############################################################################################################
$DB = Connect_db();

require_once("./lib/spreadsheets/Worksheet.php");
require_once("./lib/spreadsheets/Workbook.php");
$ord_num = "LNL14-1087";
//JNI14-0794/0795 /0796/0797/0798  IN WIPONE

$q_str="select s_order.id,s_order.order_num,s_order.cust,s_order.style,s_order.ie1,s_order.style_num,s_order.apv_date,s_order.revise,cust.cust_init_name,s_order.size,size_des.size,size_des.size_scale,size_des.base_size,order_partial.id as partial_id,order_partial.p_etd as partial_etd,order_partial.remark as port,wiqty.wi_id,wiqty.colorway,wiqty.qty,wi.bom_rev
from s_order 
inner join cust on cust.cust_s_name = s_order.cust
inner join size_des on size_des.id = s_order.size
inner join order_partial on order_partial.ord_num = s_order.order_num
inner join wiqty on wiqty.p_id = order_partial.id
inner join wi on wi.wi_num = s_order.order_num
where s_order.order_num='";
$q_str.= $ord_num;
$q_str.= "' ";
$q_str.= "order by partial_id,wiqty.colorway";

$order_info = array();
echo $q_str;
echo "<br>";
if (!$q_result = mysql_query($q_str)) {
	echo ("Error ! cannot access database, pls try later !");
	return false;    
}

while ($row = mysql_fetch_array($q_result)) 
{
	$order_info[] = $row;
}
// print_r($order_info);
//exit;
$init_excel=true;
$x = 0;
$tmp_size = array();
$tmp_qty  = array();
$tmp_ord_num='';

foreach($order_info as $excel_key => $excel_value)
{
	if($excel_value['order_num'] != $tmp_ord_num)
	{

		$init_excel=false;
		$tmp_ord_num = $excel_value['order_num'];
		// Creating a workbook
		$workbook = new Workbook("./wipone/".$excel_value['order_num']."_".$excel_value['bom_rev'].".xls");
		// Creating the first worksheet
		$worksheet1 =& $workbook->add_worksheet($excel_value['order_num']);
		//欄位屬性用
		$formatot =& $workbook->add_format();
		$formatot->set_size(10);
		$formatot->set_align('left');
		$formatot->set_color('block');
		$formatot->set_pattern();
		$formatot->set_fg_color('white');
		
		//設定欄位寬度
		$ary_size = array(20,20,7,12,20,20,20,20,20,8,8);
		for ($i=0; $i<sizeof($ary_size); $i++){
			$worksheet1->set_column(0,$i,$ary_size[$i]);	  
		}
		//換訂單了
		$x = 0;
		$worksheet1->write($x,0,"BuyerCode",$formatot);
		$worksheet1->write($x,1,"ordernumber",$formatot);
		$worksheet1->write($x,2,"version",$formatot);
		$worksheet1->write($x,3,"shipmentDate",$formatot);
		$worksheet1->write($x,4,"orderqty",$formatot);
		$worksheet1->write($x,5,"Color",$formatot);
		$worksheet1->write($x,6,"Size",$formatot);
		$worksheet1->write($x,7,"style",$formatot);
		$worksheet1->write($x,8,"Port",$formatot);
		$worksheet1->write($x,9,"ItemType",$formatot);
		$worksheet1->write($x,10,"Ext01",$formatot);
		$x++;
		///////////////////////////////////////////////////////////////////////////////
		$tmp_size = explode(',',$excel_value['size']);
		$tmp_qty = explode(',',$excel_value['qty']);
		($excel_value['port']=='')? $tmp_port = '' : $tmp_port = $excel_value['port'];
		foreach($tmp_size as $size_key => $size_value)
		{
			if($tmp_qty[$size_key]<>'')
			{
			$worksheet1->write($x,0,$excel_value['cust_init_name'],$formatot);
			$worksheet1->write($x,1,$excel_value['order_num'],$formatot);
			$worksheet1->write($x,2,$excel_value['revise']+1,$formatot);
			$worksheet1->write($x,3,$excel_value['partial_etd'],$formatot);
			$worksheet1->write($x,4,($tmp_qty[$size_key]=='')? 0:$tmp_qty[$size_key],$formatot);
			$worksheet1->write($x,5,$excel_value['colorway'],$formatot);
			$worksheet1->write($x,6,$size_value,$formatot);
			$worksheet1->write($x,7,$excel_value['style_num'],$formatot);
			$worksheet1->write($x,8,$tmp_port,$formatot);
			$worksheet1->write($x,9,$excel_value['style'],$formatot);
			$worksheet1->write($x,10,$excel_value['ie1'],$formatot);
			$x++;
			}
		}
		
	}
	else
	{
		//同訂單
		$tmp_size = explode(',',$excel_value['size']);
		$tmp_qty = explode(',',$excel_value['qty']);
		($excel_value['port']=='')? $tmp_port = '' : $tmp_port = $excel_value['port'];
		foreach($tmp_size as $size_key => $size_value)
		{
			if($tmp_qty[$size_key]<>'')
			{
			$worksheet1->write($x,0,$excel_value['cust_init_name'],$formatot);
			$worksheet1->write($x,1,$excel_value['order_num'],$formatot);
			$worksheet1->write($x,2,$excel_value['revise']+1,$formatot);
			$worksheet1->write($x,3,$excel_value['partial_etd'],$formatot);
			$worksheet1->write($x,4,($tmp_qty[$size_key]=='')? 0:$tmp_qty[$size_key],$formatot);
			$worksheet1->write($x,5,$excel_value['colorway'],$formatot);
			$worksheet1->write($x,6,$size_value,$formatot);
			$worksheet1->write($x,7,$excel_value['style_num'],$formatot);
			$worksheet1->write($x,8,$tmp_port,$formatot);
			$worksheet1->write($x,9,$excel_value['style'],$formatot);
			$worksheet1->write($x,10,$excel_value['ie1'],$formatot);
			$x++;
			}
		}	
	}
}
//最後一筆訂單或只有一筆使用
$workbook->close();
return true;

?>