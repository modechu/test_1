<?php



define("DB_HOST","localhost");
define("DB_LOGIN","root");
define("DB_PASSWORD","");
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
}
Connect_db();
###############################################################################################################

$q_str="select s_order.id,s_order.order_num,s_order.cust,s_order.style,s_order.style_num,s_order.apv_date,s_order.revise,cust.cust_init_name,s_order.size,size_des.size,size_des.size_scale,size_des.base_size,order_partial.id as partial_id,order_partial.p_etd as partial_etd,order_partial.remark as port,wiqty.wi_id,wiqty.colorway,wiqty.qty
from s_order
inner join cust on cust.cust_s_name = s_order.cust
inner join size_des on size_des.id = s_order.size
inner join order_partial on order_partial.ord_num = s_order.order_num
inner join wiqty on wiqty.p_id = order_partial.id
where s_order.apv_date='";
//$q_str.= date('Y-m-d');
$q_str.= "2012-11-05";
$q_str.= "' ";
//$q_str.= "and s_order.order_num='DTH12-0895' ";
$q_str.= "order by partial_id,wiqty.colorway";
//echo $q_str;
//ExportXML(sql_query_array($q_str));
ExportExcel("test_excel.xls","test",sql_query_array($q_str));
function sql_query_array($sql_str)
{
	$res = mysql_query($sql_str);
	$myarr=array();
	$i=0;
	while($tmp = mysql_fetch_array($res)){
		$myarr[$i]=$tmp;
        $i++;
	}
	return $myarr;
}
//之後修改成tag一個陣列，資料一個陣列
function ExportXML($myarray) {
	
	
	if(sizeof($myarray) > 0)
	{
		header("Content-Type: text/xml");
		$xml='<?xml version="1.0"  encoding="utf-8" ?>';
		$xml.="<xml>";
		
		foreach($myarray as $key => $value)
		{
			$tmp_size = explode(',',$value['size']);
			$tmp_qty = explode(',',$value['qty']);
			($value['port']=='')? $tmp_port = '' : $tmp_port = $value['port'];
			foreach($tmp_size as $key1 => $value1)
			{
				$xml.="<item>";
				
				$xml.="<Buyer>";
				$xml.=$value['cust_init_name'];
				$xml.="</Buyer>";
				$xml.="<Port>";
				$xml.= $tmp_port;
				$xml.="</Port>";
				$xml.="<ordernumber>";
				$xml.=$value['order_num'];
				$xml.="</ordernumber>";
				$xml.="<version>";
				$xml.=$value['revise']+1;
				$xml.="</version>";
				$xml.="<shipmentDate>";
				$xml.=$value['partial_etd'];
				$xml.="</shipmentDate>";
				$xml.="<Color>";
				$xml.=$value['colorway'];
				$xml.="</Color>";
				$xml.="<Size>";
				$xml.=$value1;
				$xml.="</Size>";
				$xml.="<orderqty>";
				($tmp_qty[$key1]=='')? $xml.= '0' : $xml.=$tmp_qty[$key1];;
				$xml.="</orderqty>";
				$xml.="<style>";
				$xml.=$value['style_num'];
				$xml.="</style>";
				
				$xml.="</item>";
			
			}
		}
		
		$xml.="</xml>";
		
		$fp = fopen(dirname(__FILE__).'\xml_files\\output_test'.date("Ymd").'.xml', 'w');
		fwrite($fp, $xml);
		fclose($fp);
		
		
	}
	else
	{
		echo date('Y-m-d')." 無資料";
	}
    
}

function ExportExcel($filename,$sheetname,$myarray)
{
	require_once("./lib/spreadsheets/Worksheet.php");
	require_once("./lib/spreadsheets/Workbook.php");
	
	//$filename未設定
	// print_r($myarray);
	// exit;
	header("Content-type: application/vnd.ms-excel");
	header("Content-Disposition: attachment; filename=$filename");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
	header("Pragma: public");
	
	// Creating a workbook
	$workbook = new Workbook("-");

	// Creating the first worksheet
	$worksheet1 =& $workbook->add_worksheet($sheetname);
	
	//欄位屬性用
	$formatot =& $workbook->add_format();
	$formatot->set_size(10);
	$formatot->set_align('left');
	$formatot->set_color('block');
	$formatot->set_pattern();
	$formatot->set_fg_color('white');
	
	//設定欄位寬度
	$ary_size = array(20,20,5,6,20,7,12,6,8,3,3,3,20,20,20,20,20,10,8,8,20,10,20);
	for ($i=0; $i<sizeof($ary_size); $i++){
		$worksheet1->set_column(0,$i,$ary_size[$i]);	  
	}
	
	//
	$x = 0;
	$tmp_size = array();
	$tmp_qty  = array();
	
	foreach($myarray as $excel_key => $excel_value)
	{
			//echo $excel_value['port'];
			//echo "<br>";
		if($x == 0)
		{
			$worksheet1->write($x,0,"Buyer",$formatot);
			$worksheet1->write($x,1,"Port",$formatot);
			$worksheet1->write($x,2,"City",$formatot);
			$worksheet1->write($x,3,"Season",$formatot);
			$worksheet1->write($x,4,"ordernumber",$formatot);
			$worksheet1->write($x,5,"version",$formatot);
			$worksheet1->write($x,6,"shipmentDate",$formatot);
			$worksheet1->write($x,7,"itemid",$formatot);
			$worksheet1->write($x,8,"itemtype",$formatot);
			$worksheet1->write($x,9,"upc",$formatot);
			$worksheet1->write($x,10,"Ean",$formatot);
			$worksheet1->write($x,11,"Sku",$formatot);
			$worksheet1->write($x,12,"Color",$formatot);
			$worksheet1->write($x,13,"Size",$formatot);
			$worksheet1->write($x,14,"orderqty",$formatot);
			$worksheet1->write($x,15,"style",$formatot);
			$worksheet1->write($x,16,"shortDesc",$formatot);
			$worksheet1->write($x,17,"Full Desc",$formatot);
			$worksheet1->write($x,18,"Qty Uom",$formatot);
			$worksheet1->write($x,19,"UnitPrice",$formatot);
			$worksheet1->write($x,20,"provincestate",$formatot);
			$worksheet1->write($x,21,"country",$formatot);
			$worksheet1->write($x,22,"postalcode",$formatot);
			$x++;
			$tmp_size = explode(',',$excel_value['size']);
			$tmp_qty = explode(',',$excel_value['qty']);
			($excel_value['port']=='')? $tmp_port = '' : $tmp_port = $excel_value['port'];
			foreach($tmp_size as $size_key => $size_value)
			{
				$worksheet1->write($x,0,$excel_value['cust_init_name'],$formatot);
				$worksheet1->write($x,1,$tmp_port,$formatot);
				$worksheet1->write($x,2,"",$formatot);
				$worksheet1->write($x,3,"",$formatot);
				$worksheet1->write($x,4,$excel_value['order_num'],$formatot);
				$worksheet1->write($x,5,$excel_value['revise']+1,$formatot);
				$worksheet1->write($x,6,$excel_value['partial_etd'],$formatot);
				$worksheet1->write($x,7,"",$formatot);
				$worksheet1->write($x,8,"",$formatot);
				$worksheet1->write($x,9,"",$formatot);
				$worksheet1->write($x,10,"",$formatot);
				$worksheet1->write($x,11,"",$formatot);
				$worksheet1->write($x,12,$excel_value['colorway'],$formatot);
				$worksheet1->write($x,13,$size_value,$formatot);
				$worksheet1->write($x,14,($tmp_qty[$size_key]=='')? 0:$tmp_qty[$size_key],$formatot);
				$worksheet1->write($x,15,$excel_value['style_num'],$formatot);
				$worksheet1->write($x,16,"",$formatot);
				$worksheet1->write($x,17,"",$formatot);
				$worksheet1->write($x,18,"",$formatot);
				$worksheet1->write($x,19,"",$formatot);
				$worksheet1->write($x,20,"",$formatot);
				$worksheet1->write($x,21,"",$formatot);
				$worksheet1->write($x,22,"",$formatot);
				$x++;
			}
		}
		else
		{
			$tmp_size = explode(',',$excel_value['size']);
			$tmp_qty = explode(',',$excel_value['qty']);
			($excel_value['port']=='')? $tmp_port = '' : $tmp_port = $excel_value['port'];
			foreach($tmp_size as $size_key => $size_value)
			{
				$worksheet1->write($x,0,$excel_value['cust_init_name'],$formatot);
				$worksheet1->write($x,1,$tmp_port,$formatot);
				$worksheet1->write($x,2,"",$formatot);
				$worksheet1->write($x,3,"",$formatot);
				$worksheet1->write($x,4,$excel_value['order_num'],$formatot);
				$worksheet1->write($x,5,$excel_value['revise']+1,$formatot);
				$worksheet1->write($x,6,$excel_value['partial_etd'],$formatot);
				$worksheet1->write($x,7,"",$formatot);
				$worksheet1->write($x,8,"",$formatot);
				$worksheet1->write($x,9,"",$formatot);
				$worksheet1->write($x,10,"",$formatot);
				$worksheet1->write($x,11,"",$formatot);
				$worksheet1->write($x,12,$excel_value['colorway'],$formatot);
				$worksheet1->write($x,13,$size_value,$formatot);
				$worksheet1->write($x,14,($tmp_qty[$size_key]=='')? 0:$tmp_qty[$size_key],$formatot);
				$worksheet1->write($x,15,$excel_value['style_num'],$formatot);
				$worksheet1->write($x,16,"",$formatot);
				$worksheet1->write($x,17,"",$formatot);
				$worksheet1->write($x,18,"",$formatot);
				$worksheet1->write($x,19,"",$formatot);
				$worksheet1->write($x,20,"",$formatot);
				$worksheet1->write($x,21,"",$formatot);
				$worksheet1->write($x,22,"",$formatot);
				$x++;
			}
		}
	}
	$workbook->close();
	
	
}







?>