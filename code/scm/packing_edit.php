<?php
#
#
#
session_start();
// echo $PHP_action.'<br>';
#
#
#
require_once "config.php";
require_once "config.admin.php";
require_once "init.object.php";
//include_once($config['root_dir']."/lib/class.packing_edit.php");
include_once($config['root_dir']."/lib/class.packing_edit.php");
//if (!$invoice_import->init($mysql,"log")) { print "error!! cannot initialize database for LOG class"; exit; }
#
#
#
$PHP_SELF = $_SERVER['PHP_SELF'];
$perm = $GLOBALS['power'];
$op = array();
$packing_edit = new PACKING_EDIT();
if (!$packing_edit->init($mysql,"log")) { print "error!! cannot initialize database for packing_edit class"; exit; }
#
#
# Template
//$TPL_ARRIVAL_TIME = 'arrival_time.html';
//$TPL_ARRIVAL_TIME_SEARCH = 'arrival_time_search.html';
#
#
#
$AUTH = '999';
#
#
#
switch ($PHP_action) {
#
#
# :main
default;
case "main":
check_authority($AUTH,"view");
$op['css'] = array( 'js/calendar/css/jscal2.css' , 'js/calendar/css/border-radius.css' , 'js/calendar/css/gold/gold.css','css/packing_edit.css' );
$op['js'] = array( 'js/jquery.min.js'  ,'js/packing_edit.js', 'js/jquery.blockUI.js' , 'js/calendar/js/jscal2.js' , 'js/calendar/js/lang/en.js' );
$op['ship_way'] = $arry2->select($PO_SHIP,'','PHP_shipway','select','');

$where_str="order by cust_s_name"; //依cust_s_name排序
//echo $where_str;
$cust_def = $packing_edit->get_cust('cust_init_name',$where_str);  //取出客戶簡稱
//print_r($cust_def);
$cust_def_vue = $packing_edit->get_cust('cust_s_name',$where_str);	//取出客戶代號
//print_r($cust_def_vue);
$op['cust_select'] =  $arry2->select($cust_def,'','PHP_cust','select','',$cust_def_vue); 
//print_r($op);
$op['pt_date'] = $TODAY;
$op['pt_dates'] = date('Ymd');
//print_r($op);
page_display($op,$AUTH,'packing_edit.html');
break;


case "get_shipway":
$shipway = $arry2->select($PO_SHIP,'','scm_ord'.$_POST['PHP_rowindex'].'_shipway_select','select','');
echo $shipway;
break;



case "consignee":
//處理consignee
//沒客戶不能做 
$iorder = array();
$t_sql = "select dept,cust from s_order where order_num='".$_POST['PHP_order']."'";
$iorder = $packing_edit->get_order_info($t_sql);
//print_r($iorder);

if($_POST['cust_id'] != '')
{
	if($iorder['cust'] == $_POST['cust_id'])
	{
		if(sizeof($iorder) > 0)
		{
			$cust_consignee = $packing_edit->get_consignee($_POST['cust_id'],$iorder['dept']);//js檔案用動態取得
			$consignee_id=array();
			$consignee_fname=array();
			foreach($cust_consignee as $consignee_key=>$consignee_val)
			{
				$consignee_id[$consignee_key] = $consignee_val['id'];
				$consignee_fname[$consignee_key] = $consignee_val['f_name'];
			}
			//print_r($consignee_id);
			if($_POST['PHP_order_type'] == 'SCM')
			{
				$consignee_result = $arry2->select($consignee_fname,'','scm_ord'.$_POST['rowindex'].'_consignee_select','select','',$consignee_id); 
				echo $consignee_result;
			}
			else
			{
				$consignee_result = $arry2->select($consignee_fname,'','consignee_select','select','',$consignee_id); 
				echo $consignee_result;
			}
		}
	}
	else
	{
		echo "";
	}
}
else
{
	echo "";
}
break;




/*全部一起儲存*/
case "packlist_save":
//echo "test";
print_r($_POST);
//exit;
break;
/*單獨一列儲存儲存*/
case "packlist_save_one":

$carton_start = $_POST['PHP_carton_start'];
$carton_end = $_POST['PHP_carton_end'];
$shipway = $_POST['PHP_shipway'];
$po_order = str_replace(' ','_',$_POST["PHP_po_order"]);
$carton_group = $po_order.date("Ymd",strtotime($_POST["PHP_shipdate"])).($carton_start<10?"0".$carton_start:$carton_start).($carton_end<10?"0".$carton_end:$carton_end);
$result4sql_init = true;
$test='';

$q_str_check = "select distinct order_no,order_partial from packing_det where po_no='";
$q_str_check .= $po_order;
$q_str_check .= "' and shipping_date='";
$q_str_check .= $_POST["PHP_shipdate"];
$q_str_check .= "' and shipway='";
$q_str_check .= $shipway;
$q_str_check .= "' and order_no='";
$q_str_check .= $_POST['PHP_scm_order'];
$q_str_check .= "' and order_partial='";
$q_str_check .= $_POST['PHP_partial'];
$q_str_check .= "' and carton_no in(";
for( $carton_i = $carton_start;$carton_i <= $carton_end ;$carton_i++ )
{
	if($carton_i == $carton_end)
	{
		$q_str_check .= $carton_i;
	}
	else
	{
		$q_str_check .= $carton_i.",";
	}
}
$q_str_check .= ")";
//echo $q_str_check;
$chk_carton_result = $packing_edit->chk_carton_number($q_str_check);
$alert_message = "Order:".$chk_carton_result['order_no']."; Partial:".$chk_carton_result['order_partial']." carton duplicate,Please check!!";
if(sizeof($chk_carton_result) > 0)
{
	//停止Insert(不行，若有突然要新增訂單配比，勢必要增加)
	echo $alert_message;
	
}
else
{
	for($carton_s=$carton_start;$carton_s<=$carton_end;$carton_s++)
	{

		foreach($_POST['PHP_sizebreakdown'] as $sbd_key=>$sbd_val)
		{
			foreach($sbd_val as $sbd_key1=>$sbd_val1)
			{
				$myval = split("[,]", $sbd_val1);
					$sub_str = "insert into packing_det(carton_no,carton_group,po_no,order_no,order_partial,pack_style,hanger,color,size,qty,ucc,prepack,shipping_date,shipway,shipping_to,status,open_user,open_date) ";
					$sub_str .= "values(" ;
					$sub_str .= $carton_s.",'";
					$sub_str .= $carton_group."','";
					
					$sub_str .= $po_order."','";
					$sub_str .= $_POST["PHP_scm_order"]."','";
					$sub_str .= $_POST["PHP_partial"]."','";
					$sub_str .= $_POST["PHP_packstyle"]."','";
					$sub_str .= $_POST["PHP_hanger"]."','";

					$sub_str .= $myval[0]."','";
					$sub_str .= $myval[1]."',";
					$sub_str .= $myval[2].",'"; 
					$sub_str .= $_POST["PHP_ucc"]."','";
					$sub_str .= $_POST["PHP_prepack"]."','";
					$sub_str .= $_POST["PHP_shipdate"]."','";
					$sub_str .= $_POST["PHP_shipway"]."',";
					$sub_str .= $_POST["PHP_shipto"].",0,'";
					$sub_str .= $GLOBALS['SCACHE']['ADMIN']['login_id']."','";
					$sub_str .= date("Y-m-d")."'";
					$sub_str .= ")"; 
					
					$result4sql = $packing_edit->insert_sub($sub_str);
					//$message .= $sub_str."<br>";
			}
		}
		
	}
		echo $result4sql;








	
}


/* foreach($_POST['PHP_sizebreakdown'] as $sbd_key=>$sbd_val)
		{ */












/* for($carton_s=$carton_start;$carton_s<=$carton_end;$carton_s++)
{

	$query_main = "select distinct packing_main.sid from packing_main left join packing_det on packing_main.sid = packing_det.main_sid ";
	$query_main .= "where packing_det.po_no = '";
	$query_main .= $po_order;
	$query_main .= "' and packing_main.shipway = '";
	$query_main .= $shipway;
	$query_main .= "' and packing_main.shipping_date = '";
	$query_main .= $_POST["PHP_shipdate"];
	$query_main .= "'";
	$chk_carton_result = $packing_edit->chk_carton_number($query_main);
	
	if(sizeof($chk_carton_result)==0)
	{
		$main_str = "insert into packing_main(shipping_date,shipway,shipping_to,status,open_user,open_date) values('";
		$main_str .= $_POST["PHP_shipdate"]."','";
		$main_str .= $_POST["PHP_shipway"]."',";
		$main_str .= $_POST["PHP_shipto"].",0,'";
		$main_str .= $GLOBALS['SCACHE']['ADMIN']['login_id']."','";
		$main_str .= date("Y-m-d")."')";
		$myid = $packing_edit->insert_main($main_str);

		if(!$myid){echo 'Insert failure!!';break;}
		foreach($_POST['PHP_sizebreakdown'] as $sbd_key=>$sbd_val)
		{
			foreach($sbd_val as $sbd_key1=>$sbd_val1)
			{
				$myval = split("[,]", $sbd_val1);
					$sub_str = "insert into packing_det(main_sid,carton_no,carton_group,po_no,order_no,order_partial,pack_style,hanger,color,size,qty,ucc,prepack,shipping_date) ";
					$sub_str .= "values(".$myid."," ;
					$sub_str .= $carton_s.",'";
					$sub_str .= $carton_group."','";
					
					$sub_str .= $po_order."','";
					$sub_str .= $_POST["PHP_scm_order"]."','";
					$sub_str .= $_POST["PHP_partial"]."','";
					$sub_str .= $_POST["PHP_packstyle"]."','";
					$sub_str .= $_POST["PHP_hanger"]."','";

					$sub_str .= $myval[0]."','";
					$sub_str .= $myval[1]."',";
					$sub_str .= $myval[2].",'"; 
					$sub_str .= $_POST["PHP_ucc"]."','";
					$sub_str .= $_POST["PHP_prepack"]."','";
					$sub_str .= $_POST["PHP_shipdate"]."'";
					$sub_str .= ")";

					$result4sql = $packing_edit->insert_sub($sub_str);
					
			}
		}
	}
	else
	{
		$myid = $chk_carton_result['sid'];
		foreach($_POST['PHP_sizebreakdown'] as $sbd_key=>$sbd_val)
		{
			foreach($sbd_val as $sbd_key1=>$sbd_val1)
			{
				$myval = split("[,]", $sbd_val1);
					$sub_str = "insert into packing_det(main_sid,carton_no,carton_group,po_no,order_no,order_partial,pack_style,hanger,color,size,qty,ucc,prepack,shipping_date) ";
					$sub_str .= "values(".$myid."," ;
					$sub_str .= $carton_s.",'";
					$sub_str .= $carton_group."','";
					
					$sub_str .= $po_order."','";
					$sub_str .= $_POST["PHP_scm_order"]."','";
					$sub_str .= $_POST["PHP_partial"]."','";
					$sub_str .= $_POST["PHP_packstyle"]."','";
					$sub_str .= $_POST["PHP_hanger"]."','";

					$sub_str .= $myval[0]."','";
					$sub_str .= $myval[1]."',";
					$sub_str .= $myval[2].",'"; 
					$sub_str .= $_POST["PHP_ucc"]."','";
					$sub_str .= $_POST["PHP_prepack"]."','";
					$sub_str .= $_POST["PHP_shipdate"]."'";
					$sub_str .= ")";
					
					$result4sql = $packing_edit->insert_sub($sub_str);
			}
		}
	}
}
echo $result4sql ; */

break;


case "check_carton":
$carton_start = $_POST['PHP_carton_start'];
$carton_end = $_POST['PHP_carton_end'];
$po = $_POST['PHP_po'];
$shipdate = $_POST['PHP_shipdate'];
$q_str = "select carton_no from packing_det ";
$q_str .= "where po_no = '".$po."' and ";
$q_str .= "carton_no in (";
for($c_start = $carton_start ; $c_start <= $carton_end; $c_start++)
{
	if($c_start == $carton_end)
	{
		$q_str .= $c_start.') and ';
	}
	else
	{
		$q_str .= $c_start.',';
	}
}
$q_str .= "shipping_date = '".$shipdate."'";
$carton = $packing_edit->chk_carton_number($q_str);


print_r($carton);


break;





case "get_partial":
//print_r($_POST);

$mks = $packing_edit->get_order_partial($_POST['PHP_order']);
/* print_r($mks);
exit; */
if($mks == 0)
{
	echo "no order partial";
}
else
{
	if($_POST['PHP_order_type'] == 'SCM')
	{
		foreach($mks as $mks_key => $mks_val)
		{
			$new_mks[] = $mks_val['mks'];
		}
		$mks_select = $arry2->select($new_mks,'','scm_ord'.$_POST['PHP_rowindex'].'_partial_select','select','',$new_mks); 
		$jquery = "<script language='javascript'>";
		$jquery .= "$('#scm_ord".$_POST['PHP_rowindex']."_partial_select').change(function(){ ";
			
			$jquery .= "var order = $('#main_order').val();";
			$jquery .= "var mks = $('#scm_ord".$_POST['PHP_rowindex']."_partial_select').find(':selected').val();";
			
			$jquery .= "$.post('./packing_edit.php?PHP_action=get_partial_order',{PHP_order:order,PHP_partial:mks,PHP_rowindex:".$_POST['PHP_rowindex'].",PHP_order_type:'SCM'},function(data){";
				//在packing_edit.php的 get_partial_order 執行
				$jquery .= "$('#scm_ord".$_POST['PHP_rowindex']."_sizebreakdown').html(data);";
				//$jquery .= "alert($('#scm_ALR14-0468_A_sizebreak_1 tr').index());";
				//$jquery .= "$('#btn_new_record').css('width',$('#tbl_scm_carton_edit').width()+8);";
				//$jquery .= "$('#btn_save').css('width',$('#tbl_scm_carton_edit').width()+8);";
				//$jquery .= "alert('test');";
			$jquery .= "});";				
		$jquery .= "});";
		$jquery .= "</script>";
		$result = $mks_select.$jquery;
		echo $result;
	}
	else
	{
		foreach($mks as $mks_key => $mks_val)
		{
			$new_mks[] = $mks_val['mks'];
		}
		$mks_select = $arry2->select($new_mks,'','po_ord'.$_POST['PHP_rowindex'].'_partial_select','select','',$new_mks); 
		$jquery = "<script language='javascript'>";
		$jquery .= "$('#po_ord".$_POST['PHP_rowindex']."_partial_select').change(function(){ ";
			
			$jquery .= "var order = $('#po_ord".$_POST['PHP_rowindex']."_order').val();";
			$jquery .= "var mks = $('#po_ord".$_POST['PHP_rowindex']."_partial_select').find(':selected').val();";
			
			$jquery .= "$.post('./packing_edit.php?PHP_action=get_partial_order',{PHP_order:order,PHP_partial:mks,PHP_rowindex:".$_POST['PHP_rowindex'].",PHP_order_type:'PO'},function(data){";
				//在packing_edit.php的 get_partial_order 執行
				$jquery .= "$('#po_ord".$_POST['PHP_rowindex']."_sizebreakdown').html(data);";
				
				//$jquery .= "alert($('#po_ALR14-0468_A_sizebreak_1 tr').index());";
				
				//$jquery .= "$('#btn_new_record').css('width',$('#tbl_scm_carton_edit').width()+8);";
				//$jquery .= "$('#btn_save').css('width',$('#tbl_scm_carton_edit').width()+8);";
				//$jquery .= "alert('test');";
			$jquery .= "});";				
		$jquery .= "});";
		
	
		
		
		
		$jquery .= "</script>";
		$result = $mks_select.$jquery;
		echo $result;
	}
} 

//$arry2->select();
break;

case "get_partial_order":
//雖說有Order才會有Partial,但是Order還是有可能會被使用者誤改，此階段需在檢查一下訂單號
//echo 'test';
//exit;
$my_order = $_POST['PHP_order'];
$my_partial = $_POST['PHP_partial'];
$my_rowindex = $_POST['PHP_rowindex'];
$my_order_type = $_POST['PHP_order_type'];
//print_r($sizebreakdown);
if(trim($my_order) == '')
{
	echo 'order_empty';
}
else
{
	if(trim($my_partial)=='')
	{
		echo 'partial_empty';
	}
	else
	{
		$sizebreakdown = $packing_edit->get_sizebreakdown($my_order,$my_partial);
		
		if($sizebreakdown == 0)
		{
			$return_message='No Size Breakdown!!';
			echo $return_message;
		}
		else
		{
			//print_r($sizebreakdown);
			$size=explode(',',$sizebreakdown[0]['size']);
			if($my_order_type == 'PO')
			{
				$SizeBreakdown_table = '<table id="po_'.$my_order.'_'.$my_partial.'_sizebreak_'.$my_rowindex.'" cellpadding="5" border="1">';
			}
			else
			{
				$SizeBreakdown_table = '<table id="scm_'.$my_order.'_'.$my_partial.'_sizebreak_'.$my_rowindex.'" cellpadding="5" border="1">';
			}
			
			$SizeBreakdown_table .= '<tr idx="nouse">';
			$SizeBreakdown_table .= '<td></td>';
			/*標頭上欄位(尺碼)*/
			foreach($size as $key_size => $val_size)
			{
				$SizeBreakdown_table .= '<td>';
				$SizeBreakdown_table .= $val_size;
				$SizeBreakdown_table .= '</td>';
			}
			$SizeBreakdown_table .= '</tr>';
			/*標頭左欄位與表身(顏色+尺碼數量)*/
			$SizeBreakdown_jquery = '';
			foreach($sizebreakdown as $key_sizeBD => $val_sizeBD)
			{
				$SizeBreakdown_table .= '<tr idx="'.$my_rowindex.'">';
				$SizeBreakdown_table .= '<td>'.$sizebreakdown[$key_sizeBD]['colorway'].'</td>';
				foreach($size as $key_size => $val_size)
				{
					$SizeBreakdown_table .= '<td>';
					if($my_order_type == 'PO')
					{
						$SizeBreakdown_table .= '<input id="po_ord'.$my_rowindex.'_'.$key_sizeBD.'_'.$key_size.'" name="po['.$my_rowindex.']['.$my_order.']['.$my_partial.']['.$sizebreakdown[$key_sizeBD]['colorway'].']['.$val_size.'][]" class="cls_po'.$my_rowindex.'"  color="'.$sizebreakdown[$key_sizeBD]['colorway'].'" size="'.$val_size.'" type="text" style="width:25px"/>';
					}
					else
					{
						$SizeBreakdown_table .= '<input id="scm_ord'.$my_rowindex.'_'.$key_sizeBD.'_'.$key_size.'" name="scm['.$my_rowindex.']['.$my_order.']['.$my_partial.']['.$sizebreakdown[$key_sizeBD]['colorway'].']['.$val_size.'][]" class="cls_scm'.$my_rowindex.'" color="'.$sizebreakdown[$key_sizeBD]['colorway'].'" size="'.$val_size.'" type="text" style="width:25px"/>';
					}
					
					$SizeBreakdown_table .= '</td>';
					
				}
				$SizeBreakdown_table .= '</tr>';

				
			}
			
			if($my_order_type == 'PO')
			{
				$SizeBreakdown_table .= '<input type="hidden" id="po_ord'.$my_rowindex.'_colorsize" value="'.sizeof($sizebreakdown).'_'.sizeof($size).'" />';
				$SizeBreakdown_table .= '<script language="javascript">';
				$SizeBreakdown_table .= 'var myregex=new RegExp(/\D|^0/);';
				$SizeBreakdown_table .= '$(".cls_po'.$my_rowindex.'").keyup(function(){';
				$SizeBreakdown_table .= '$(this).val($(this).val().replace(myregex,""));';
				//$SizeBreakdown_table .= 'alert($("#po_ALR15-0347_A_sizebreak_1 tr").index());';
				$SizeBreakdown_table .= '}).bind("paste",function(){ ';
				$SizeBreakdown_table .= '$(this).val($(this).val().replace(myregex,""));';
				$SizeBreakdown_table .= '}).css("ime-mode", "disabled");';
				$SizeBreakdown_table .= '</script>';
				echo $SizeBreakdown_table;
			}
			else
			{
				$SizeBreakdown_table .= '<input type="hidden" id="scm_ord'.$my_rowindex.'_colorsize" value="'.sizeof($sizebreakdown).'_'.sizeof($size).'" />';
				$SizeBreakdown_table .= '<input type="hidden" id="po_ord'.$my_rowindex.'_colorsize" value="'.sizeof($sizebreakdown).'_'.sizeof($size).'" />';
				$SizeBreakdown_table .= '<script language="javascript">';
				$SizeBreakdown_table .= 'var myregex=new RegExp(/\D|^0/);';
				$SizeBreakdown_table .= '$(".cls_scm'.$my_rowindex.'").keyup(function(){';
				$SizeBreakdown_table .= '$(this).val($(this).val().replace(myregex,""));';
				$SizeBreakdown_table .= '}).bind("paste",function(){ ';
				$SizeBreakdown_table .= '$(this).val($(this).val().replace(myregex,""));';
				$SizeBreakdown_table .= '}).css("ime-mode", "disabled");';
				$SizeBreakdown_table .= '</script>';
				echo $SizeBreakdown_table;
			}
		}
		//print_r(sizeof($sizebreakdown[0]['colorway']));
	}
} 






//echo $_POST['PHP_partial'];
break;
case "get_packing_po_view":
//取得已經安排好配比的PO資料，純瀏覽畫面
//print_r($_POST);
$t_sql = "select carton_group,po_no,shipping_date,shipway,shipping_to,carton_no,order_no,order_partial,pack_style,hanger,color,size,qty,ucc,prepack ";
$t_sql .= "from packing_det ";
$t_sql .= "where ";
$t_sql .= "po_no = '";
$t_sql .= $_POST['PHP_po_order'];
$t_sql .= "' ";
$t_sql .= "order by shipping_date,shipway,carton_no,order_no,order_partial,color,size ";
//echo $t_sql;
$packing = $packing_edit->get_packing_detail4po($t_sql);
//print_r($packing);
$order = "";
$color = "";
$mysize = "";
$test = "";
$mysize_array = array();
$mypacking_array = array();
foreach($packing as $p_key => $p_val)
{
	$mypacking_array[$p_val['po_no']][$p_val['shipping_date']][$p_val['shipway']][$p_val['carton_no']]['shipway'] = $p_val['shipway'] ; 
	$mypacking_array[$p_val['po_no']][$p_val['shipping_date']][$p_val['shipway']][$p_val['carton_no']]['shipto'] = $packing_edit->get_consignee_addr($p_val['shipping_to']) ; 
	$mypacking_array[$p_val['po_no']][$p_val['shipping_date']][$p_val['shipway']][$p_val['carton_no']]['ucc'] = $p_val['ucc'] ; 
	$mypacking_array[$p_val['po_no']][$p_val['shipping_date']][$p_val['shipway']][$p_val['carton_no']]['prepack'] = $p_val['prepack'] ; 
	$mypacking_array[$p_val['po_no']][$p_val['shipping_date']][$p_val['shipway']][$p_val['carton_no']]['pack_style'] = $p_val['pack_style'] ; 
	$mypacking_array[$p_val['po_no']][$p_val['shipping_date']][$p_val['shipway']][$p_val['carton_no']]['hanger'] = $p_val['hanger'] ; 

	if($order != $p_val['order_no'])
	{
		$order = $p_val['order_no'];
		$color = $p_val['color'];
		$mysize = $packing_edit->get_order_size($order);
		$mysize_array = split(",",$mysize['size']);
		foreach($mysize_array as $sz_key => $sz_val)
		{
			$mypacking_array[$p_val['po_no']][$p_val['shipping_date']][$p_val['shipway']][$p_val['carton_no']]['order'][$p_val['order_no']][$p_val['order_partial']][$p_val['color']][$sz_val] = 0;
		}
		$mypacking_array[$p_val['po_no']][$p_val['shipping_date']][$p_val['shipway']][$p_val['carton_no']]['order'][$p_val['order_no']][$p_val['order_partial']][$p_val['color']][$p_val['size']] = $p_val['qty'];
		//print_r($mypacking_array );
	}
	else
	{
		if($color != $p_val['color'])
		{
			$color = $p_val['color'];
			$mysize = $packing_edit->get_order_size($order);
			$mysize_array = split(",",$mysize['size']);
			foreach($mysize_array as $sz_key => $sz_val)
			{
				$mypacking_array[$p_val['po_no']][$p_val['shipping_date']][$p_val['shipway']][$p_val['carton_no']]['order'][$p_val['order_no']][$p_val['order_partial']][$p_val['color']][$sz_val] = 0;
			}
		}
		$mypacking_array[$p_val['po_no']][$p_val['shipping_date']][$p_val['shipway']][$p_val['carton_no']]['shipway'] = $p_val['shipway'] ; 
		$mypacking_array[$p_val['po_no']][$p_val['shipping_date']][$p_val['shipway']][$p_val['carton_no']]['shipto'] = $packing_edit->get_consignee_addr($p_val['shipping_to']) ; 
		$mypacking_array[$p_val['po_no']][$p_val['shipping_date']][$p_val['shipway']][$p_val['carton_no']]['ucc'] = $p_val['ucc'] ; 
		$mypacking_array[$p_val['po_no']][$p_val['shipping_date']][$p_val['shipway']][$p_val['carton_no']]['prepack'] = $p_val['prepack'] ; 
		$mypacking_array[$p_val['po_no']][$p_val['shipping_date']][$p_val['shipway']][$p_val['carton_no']]['pack_style'] = $p_val['pack_style'] ; 
		$mypacking_array[$p_val['po_no']][$p_val['shipping_date']][$p_val['shipway']][$p_val['carton_no']]['hanger'] = $p_val['hanger'] ; 
		$mypacking_array[$p_val['po_no']][$p_val['shipping_date']][$p_val['shipway']][$p_val['carton_no']]['order'][$p_val['order_no']][$p_val['order_partial']][$p_val['color']][$p_val['size']] = $p_val['qty'];
		
	}
}
//算span
$po_span = 0;
$date_span = 0;
$way_span = 0;
$way_kep_tmp = '';
foreach($mypacking_array as $po_key => $po_val)
{
	foreach($po_val as $date_key => $date_val)
	{
		foreach($date_val as $way_key => $way_val)
		{
			foreach($way_val as $carton_key => $carton_val)
			{
				if( $way_kep_tmp != $way_key)
				{
					$way_kep_tmp = $way_key;
					
					$way_span++;
				}
				else
				{
					$way_span++;
				}
			}
			
			$mypacking_array[$po_key][$date_key][$way_key]['span'] = $way_span;
			$date_span = $date_span + $way_span;
			$way_span=0;
		}
		$mypacking_array[$po_key][$date_key]['span'] = $date_span;
		$po_span = $po_span + $date_span ;
		$date_span =0;
	}
	$mypacking_array[$po_key]['span'] = $po_span;
	$po_span = 0;
}

$html_packing_list = "<table cellpadding='5' border='1'>";
$my_order = "";
//print_r($mypacking_array);
foreach($mypacking_array as $po_key => $po_val)
{
	/* if($po_key != 'span')
	{ */
		$html_packing_list .= "<tr>";
		$html_packing_list .= "<td>Cust PO</td>";
		$html_packing_list .= "<td>Ship Date</td>";
		
		$html_packing_list .= "<td>Ship Way</td>";
		$html_packing_list .= "<td>Carton NO</td>";
		
		$html_packing_list .= "<td>Ship To</td>";
		$html_packing_list .= "<td>Pack Style</td>";
		$html_packing_list .= "<td>Hanger</td>";
		/* $html_packing_list .= "<td>UCC Remark</td>";
		$html_packing_list .= "<td>Prepack Code</td>"; */
		$html_packing_list .= "<td>Order Detail</td>";
		$html_packing_list .= "</tr>";
		
		$html_packing_list .= "<tr>";
		$html_packing_list .= "<td rowspan=".$po_val['span'].">".$po_key."</td>" ;
		foreach($po_val as $shipdate_key => $shipdate_val)
		{
			if($shipdate_key != 'span')
			{
			
				//echo sizeof($po_val[$shipdate_key]);//幾箱
				
				$html_packing_list .= "<td rowspan=".$shipdate_val['span'].">".$shipdate_key."</td>";
				$order_column_begin = false;
				$order_column_finish = false;
				//$html_packing_list .= "<td>".$shipway_key."</td>";
				foreach($shipdate_val as $shipway_key => $shipway_val)
				{
					if($shipway_key != 'span')
					{
						
						$html_packing_list .= "<td rowspan=".$shipway_val['span'].">".$shipway_key."</td>";
						foreach($shipway_val as $carton_key => $carton_val)
						{
							if($carton_key != 'span')
							{
								$html_packing_list .= "<td>".$carton_key."</td>";
								//$html_packing_list .= "<td>".$carton_val['shipway']."</td>";
								$html_packing_list .= "<td width=200px>".$carton_val['shipto']."</td>";
								$html_packing_list .= "<td>".$carton_val['pack_style']."</td>";
								$html_packing_list .= "<td>".($carton_val['hanger']=='checked'?"<input type='checkbox' checked onclick='return false;'/>":"<input type='checkbox' onclick='return false;' />")."</td>";
								/* $html_packing_list .= "<td>".$carton_val['ucc']."</td>";
								$html_packing_list .= "<td>".$carton_val['prepack']."</td>"; */
								
								//$html_packing_list .= "<td>"."</td>";
								
								$html_packing_list .= "<td>";
								//$html_packing_list .= sizeof($carton_val['order']);
								foreach($carton_val['order'] as $order_key => $order_val)
								{
									//$html_packing_list .= $order_key;
									$html_packing_list .= "<table cellpadding='5' border='1'>";
									$html_packing_list .= "<tr>";
									$html_packing_list .= "<td rowspan=".sizeof($order_val).">".$order_key."</td>";
									foreach($order_val as $partial_key => $partial_val)
									{
										$html_packing_list .= "<td>".$partial_key."</td>";
										$html_packing_list .= "<td>";
										$mysize = $packing_edit->get_order_size($order_key);
										$mysize_array = split(",",$mysize['size']);
										$html_packing_list .= "<table cellpadding='5' border='1'>";
										$html_packing_list .= "<tr><td></td>";
										foreach($mysize_array as $sizelab_key => $sizelab_val)
										{
											$html_packing_list .= "<td>".$sizelab_val."</td>";
										}
										$html_packing_list .="</tr>";
										foreach($partial_val as $color_key => $color_val)
										{
											$html_packing_list .="<tr>";
											$html_packing_list .="<td>".$color_key."</td>";
											foreach($color_val as $size_key => $size_val)
											{
												$html_packing_list .="<td>".$size_val."</td>";	
											}
											$html_packing_list .="</tr>";
										}
										
										
										
										
										
										$html_packing_list .= "</tr></table>";
										$html_packing_list .= "</td>";//顏色尺碼加在此
										$html_packing_list .= "</tr>";
									}
									
									$html_packing_list .= "</table>";
								} 
								
								$html_packing_list .= "</td>";
								
								/* if(!$order_column_begin)
								{
									$html_packing_list .= "<td rowspan=".sizeof($po_val[$shipdate_key]).">";
									$order_column_begin = true;
									//order_detail
									foreach($carton_val['order'] as $order_key => $order_val)
									{
										if($my_order != $order_key)
										{
											$my_order = $order_key;
											$html_packing_list .= "<table cellpadding='5' border='1'>";
										}
										else
										{
											
										}
									}
								}
								if(!$order_column_finish)
								{
									$html_packing_list .= "</td>";
									$order_column_finish = true;
								} */
								
								
								
								//<input type="checkbox" name="vehicle" value="Car" checked>
								
								
								
								
								
								$html_packing_list .= "</tr>" ;
								//echo $carton_val['shipway'];//出貨方式
							}
						}
					}
				}
				
				
			}
		}
	/* } */
}
$html_packing_list .= "<table>";
//print_r($mypacking_array);
echo $html_packing_list;
break;
case "get_packing_po4scmno_view":
//取得已經安排好配比的PO資料，純瀏覽畫面

break;

#
#
} # CASE END
?>