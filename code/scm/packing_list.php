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
include_once($config['root_dir']."/lib/class.packing_list.php");
//if (!$invoice_import->init($mysql,"log")) { print "error!! cannot initialize database for LOG class"; exit; }
#
#
#
$PHP_SELF = $_SERVER['PHP_SELF'];
$perm = $GLOBALS['power'];
$op = array();
$packing_list = new PACKING_LIST();
if (!$packing_list->init($mysql,"log")) { print "error!! cannot initialize database for packing_edit class"; exit; }
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
$op['js'] = array( 'js/jquery.min.js'  ,'js/packing_list.js', 'js/jquery.blockUI.js' , 'js/calendar/js/jscal2.js' , 'js/calendar/js/lang/en.js' );
$op['ship_way'] = $arry2->select($PO_SHIP,'','PHP_shipway','select','');
$op['pt_date'] = $TODAY;
$op['pt_dates'] = date('Ymd');

//$where_str="order by cust_s_name"; //依cust_s_name排序
//echo $where_str;
//$cust_def = $packing_edit->get_cust('cust_init_name',$where_str);  //取出客戶簡稱
//print_r($cust_def);
//$cust_def_vue = $packing_edit->get_cust('cust_s_name',$where_str);	//取出客戶代號
//print_r($cust_def_vue);
//$op['cust_select'] =  $arry2->select($cust_def,'','PHP_cust','select','',$cust_def_vue); 
//print_r($op);
//print_r($op);
page_display($op,$AUTH,'packing_list.html');
break;


case "get_packing_list":
$t_sql = "select carton_group,po_no,shipping_date,shipway,shipping_to,carton_no,order_no,order_partial,pack_style,hanger,color,size,qty,ucc,prepack ";
$t_sql .= "from packing_det ";
$t_sql .= "where ";
$t_sql .= "po_no like '%";
$t_sql .= $_POST['PHP_po_order'];
$t_sql .= "%' and ";
$t_sql .= "shipping_date ";
if(strtotime($_POST['PHP_date_start']) == strtotime($_POST['PHP_date_end']))
{
	$t_sql .= "= '".$_POST['PHP_date_start']."'  ";
}
else
{
	$t_sql .= "between '".$_POST['PHP_date_start']."' and '".$_POST['PHP_date_end']."'  ";
}
if((int)$_POST['PHP_carton_start'] == (int)$_POST['PHP_carton_end'] )
{
	if((int)$_POST['PHP_carton_start'] != 0)
	{
		$t_sql .= "and carton_no = ".(int)$_POST['PHP_carton_start']."  ";
	}
}
else
{
	$t_sql .= "and carton_no in (";
	for($cartoni = (int)$_POST['PHP_carton_start'];$cartoni<=(int)$_POST['PHP_carton_end'];$cartoni++)
	{
		if($cartoni == (int)$_POST['PHP_carton_end'])
		{
			$t_sql .= $cartoni.")  ";
		}
		else
		{
			$t_sql .= $cartoni.",";
		}
	}
}
if($_POST['PHP_shipway'] != '')
{
	$t_sql .= "and shipway like '".$_POST['PHP_shipway']."' ";
}

$t_sql .= "order by po_no,shipping_date,shipway,carton_no,order_no,order_partial,color,size ";
//echo $t_sql;
/* $date_init_start = new DateTime($_POST['PHP_date_start']);
$date_start = date("Y-m-d",$date_init_start); */
$packing = $packing_list->get_packing_detail4po($t_sql);
//print_r($packing);
$order = "";
$color = "";
$mysize = "";
$test = "";
$iorder = array();
$mysize_array = array();
$mypacking_array = array();
foreach($packing as $p_key => $p_val)
{
	if($p_key == 0)
	{
		$t_sql = "select dept,cust from s_order where order_num='".$p_val['order_no']."'";
		$iorder = $packing_list->get_order_info($t_sql);
	}
	$mypacking_array[$p_val['po_no']][$p_val['shipping_date']][$p_val['shipway']][$p_val['carton_no']]['shipway'] = $p_val['shipway'] ; 
	$mypacking_array[$p_val['po_no']][$p_val['shipping_date']][$p_val['shipway']][$p_val['carton_no']]['shipto'] = $packing_list->get_consignee_addr($p_val['shipping_to']) ; 
	$mypacking_array[$p_val['po_no']][$p_val['shipping_date']][$p_val['shipway']][$p_val['carton_no']]['ucc'] = $p_val['ucc'] ; 
	$mypacking_array[$p_val['po_no']][$p_val['shipping_date']][$p_val['shipway']][$p_val['carton_no']]['prepack'] = $p_val['prepack'] ; 
	$mypacking_array[$p_val['po_no']][$p_val['shipping_date']][$p_val['shipway']][$p_val['carton_no']]['pack_style'] = $p_val['pack_style'] ; 
	$mypacking_array[$p_val['po_no']][$p_val['shipping_date']][$p_val['shipway']][$p_val['carton_no']]['hanger'] = $p_val['hanger'] ; 
	
	if($order != $p_val['order_no'])
	{
		$order = $p_val['order_no'];
		$color = $p_val['color'];
		$mysize = $packing_list->get_order_size($order);
		$mysize_array = split(",",$mysize['size']);
		foreach($mysize_array as $sz_key => $sz_val)
		{
			$mypacking_array[$p_val['po_no']][$p_val['shipping_date']][$p_val['shipway']][$p_val['carton_no']]['order'][$p_val['order_no']][$p_val['order_partial']][$p_val['color']][$sz_val] = 0;
			//$mypacking_array[$p_val['po_no']][$p_val['shipping_date']][$p_val['carton_no']]['order'][$p_val['order_no']][$p_val['order_partial']][$p_val['color']][$sz_val] = 0;
		}
		$mypacking_array[$p_val['po_no']][$p_val['shipping_date']][$p_val['shipway']][$p_val['carton_no']]['order'][$p_val['order_no']][$p_val['order_partial']][$p_val['color']][$p_val['size']] = $p_val['qty'];
		//$mypacking_array[$p_val['po_no']][$p_val['shipping_date']][$p_val['carton_no']]['order'][$p_val['order_no']][$p_val['order_partial']][$p_val['color']][$p_val['size']] = $p_val['qty'];
		//print_r($mypacking_array );
	}
	else
	{
		if($color != $p_val['color'])
		{
			$color = $p_val['color'];
			$mysize = $packing_list->get_order_size($order);
			$mysize_array = split(",",$mysize['size']);
			foreach($mysize_array as $sz_key => $sz_val)
			{
				$mypacking_array[$p_val['po_no']][$p_val['shipping_date']][$p_val['shipway']][$p_val['carton_no']]['order'][$p_val['order_no']][$p_val['order_partial']][$p_val['color']][$sz_val] = 0;
				//$mypacking_array[$p_val['po_no']][$p_val['shipping_date']][$p_val['carton_no']]['order'][$p_val['order_no']][$p_val['order_partial']][$p_val['color']][$sz_val] = 0;
			}
		}
		$mypacking_array[$p_val['po_no']][$p_val['shipping_date']][$p_val['shipway']][$p_val['carton_no']]['shipway'] = $p_val['shipway'] ; 
		$mypacking_array[$p_val['po_no']][$p_val['shipping_date']][$p_val['shipway']][$p_val['carton_no']]['shipto'] = $packing_list->get_consignee_addr($p_val['shipping_to']) ; 
		$mypacking_array[$p_val['po_no']][$p_val['shipping_date']][$p_val['shipway']][$p_val['carton_no']]['ucc'] = $p_val['ucc'] ; 
		$mypacking_array[$p_val['po_no']][$p_val['shipping_date']][$p_val['shipway']][$p_val['carton_no']]['prepack'] = $p_val['prepack'] ; 
		$mypacking_array[$p_val['po_no']][$p_val['shipping_date']][$p_val['shipway']][$p_val['carton_no']]['pack_style'] = $p_val['pack_style'] ; 
		$mypacking_array[$p_val['po_no']][$p_val['shipping_date']][$p_val['shipway']][$p_val['carton_no']]['hanger'] = $p_val['hanger'] ; 
		$mypacking_array[$p_val['po_no']][$p_val['shipping_date']][$p_val['shipway']][$p_val['carton_no']]['order'][$p_val['order_no']][$p_val['order_partial']][$p_val['color']][$p_val['size']] = $p_val['qty'];
		$mypacking_array[$p_val['po_no']][$p_val['shipping_date']][$p_val['shipway']][$p_val['carton_no']]['main_sid'] = $p_val['main_sid'];
		$mypacking_array[$p_val['po_no']][$p_val['shipping_date']][$p_val['shipway']][$p_val['carton_no']]['cust_code'] = $iorder['cust'];
		$mypacking_array[$p_val['po_no']][$p_val['shipping_date']][$p_val['shipway']][$p_val['carton_no']]['order_dept'] = $iorder['dept']; 

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
			
			//if( $way_kep_tmp != $way_key)
			//{
			//	$way_kep_tmp = $way_key;
			//	$way_span++;
			//}
			if($way_key != 'span')
			{
				$way_span++;
			}	
			/* foreach($way_val as $carton_key => $carton_val)
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
			} */
			
			/* $mypacking_array[$po_key][$date_key][$way_key]['span'] = $way_span;
			$date_span = $date_span + $way_span;
			$way_span=0; */
			
		}
		$date_span = $date_span + $way_span;
		$mypacking_array[$po_key][$date_key]['span'] = $date_span;
		$po_span = $po_span + $date_span ;
		$way_span=0;
		$date_span =0;
	}
	$mypacking_array[$po_key]['span'] = $po_span;
	$po_span = 0;
}
//echo $t_sql;
/* print_r($mypacking_array);
exit; */
$jquery_packing_list = "";
$jquery_row_delete = "";
$jquery_event = "";
$jquery_packing_list_start .= "<script language='javascript'>";
$html_packing_list = "<table cellpadding='5' border='1'>";
$my_order = 0;
$my_shipdate = 0;
$my_shipway = 0;

	$html_packing_list .= "<tr>";
	$html_packing_list .= "<td>Cust PO</td>";
	$html_packing_list .= "<td>Ship Date</td>";
	$html_packing_list .= "<td>Ship Way</td>";
	$html_packing_list .= "<td>&nbsp;</td>";
	$html_packing_list .= "</tr>";

foreach($mypacking_array as $po_key => $po_val)
{
	$html_packing_list .= "<tr id='po_".$po_key.$shipdate_key."'>";
	$html_packing_list .= "<td rowspan=".$po_val['span'].">".$po_key."</td>" ;

	$my_order++;
	$my_shipdate = 0;
	$my_shipway = 0;
	foreach($po_val as $shipdate_key => $shipdate_val)
	{
		if($shipdate_key != 'span')
		{
			$my_shipway = 0;
			$html_packing_list .= "<td rowspan=".$shipdate_val['span'].">".$shipdate_key."</td>";
			//$html_packing_list .= "<td>".$shipdate_key."</td>";
			foreach($shipdate_val as $shipway_key => $shipway_val)
			{
				if($shipway_key != 'span')
				{
					$html_packing_list .= "<td>".$shipway_key."</td>";
					$order_column_begin = false;
					$order_column_finish = false;
					
					$html_packing_list .= "<td>";
					foreach($shipway_val as $carton_key => $carton_val)
					{
						if($carton_key != 'span')
						{
							$html_packing_list .= "<table id='tbl_".$po_key.$shipdate_key.$shipway_key.$carton_key."' cellpadding='5' border='1'>";
							$html_packing_list .= "<tr>";
								$html_packing_list .= "<td>Carton</td>";
								//$html_packing_list .= "<td>Ship Way</td>";
								$html_packing_list .= "<td>Ship To</td>";
								$html_packing_list .= "<td>Pack Style</td>";
								$html_packing_list .= "<td>Hanger</td>";
								$html_packing_list .= "<td>Order Detail</td>";
								$html_packing_list .= "<td></td>";
							$html_packing_list .= "</tr>";
							$html_packing_list .= "<tr>";
								//$html_packing_list .= "<td><a href='http://scm-b.carnival.com.tw/mode/packing_list.php?PHP_action=carton_edit&PHP_carton_no=".$carton_key."&PHP_po_no=".$po_key."&PHP_shipdate=".$shipdate_key."' target='_blank' title='Carton ".$carton_key."'>".$carton_key."</a></td>";
								$html_packing_list .= "<td>".$carton_key."<img src='images/edit.gif'  id='carton_".$carton_key.$po_key.$shipdate_key.$shipway_key."_edit' idx='' border='0'></td>";
									$jquery_event .= "$('#carton_".$carton_key.$po_key.$shipdate_key.$shipway_key."_edit').click(function(){";
										//$jquery_event .= "alert('".$carton_val['order_dept']."');";
										$jquery_event .= "$.post('./packing_list.php?PHP_action=carton_edit',{PHP_po_order:'".$po_key."',PHP_shipdate:'".$shipdate_key."',PHP_shipway:'".$shipway_key."',PHP_carton_no:".$carton_key.",PHP_cust:'".$carton_val['cust_code']."',PHP_dept:'".$carton_val['order_dept']."'},function(data){";
											
											//$jquery_event .= "alert(data);";
											$jquery_event .= "$('#carton_edit').html(data);";
											
										$jquery_event .= "});";
										/* $jquery_event .= "$.post('./packing_list.php?PHP_action=carton_edit',{PHP_po_order:'".$po_key."',PHP_shipdate:'".$shipdate_key."',PHP_carton_no:".$carton_key.",PHP_main_sid:".$carton_val['main_sid'].",PHP_cust:'".$carton_val['cust_code']."',PHP_dept:'".$carton_val['order_dept']."'},function(data){";
											
											$jquery_event .= "alert(data);";
											$jquery_event .= "$('#carton_edit').html(data);";
											
										$jquery_event .= "});"; */
									$jquery_event .= "});";
								//$html_packing_list .= "<td>".$carton_val['shipway']."</td>";
								$html_packing_list .= "<td width=200px>".$carton_val['shipto']."</td>";
								$html_packing_list .= "<td>".$carton_val['pack_style']."</td>";
								$html_packing_list .= "<td>".($carton_val['hanger']=='checked'?"<input type='checkbox' checked onclick='return false;'/>":"<input type='checkbox' onclick='return false;' />")."</td>";
								$html_packing_list .= "<td>";

								foreach($carton_val['order'] as $order_key => $order_val)
								{
									$html_packing_list .= "<table cellpadding='5' border='1'>";
									$html_packing_list .= "<tr>";
									$html_packing_list .= "<td rowspan=".sizeof($order_val).">".$order_key."</td>";
									foreach($order_val as $partial_key => $partial_val)
									{
										$html_packing_list .= "<td>".$partial_key."</td>";
										$html_packing_list .= "<td>";
										$mysize = $packing_list->get_order_size($order_key);
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
										$html_packing_list .= "</table>";
										$html_packing_list .= "</td>";//顏色尺碼加在此
										$html_packing_list .= "</tr>";
								
									}
									$html_packing_list .= "</table>";
								}
								
							$html_packing_list .= "</td>";
							$html_packing_list .= "<td><img src='images/trashcan.png'  id='trashcan".$po_key.$shipdate_key.$shipway_key.$carton_key."_delete' idx='' border='0'></td>";
							$jquery_event .= "$('#trashcan".$po_key.$shipdate_key.$shipway_key.$carton_key."_delete').click(function(){";
								//$jquery_event .= "alert('trashcan');";
								
								
								
								$jquery_event .= "$.post('./packing_list.php?PHP_action=carton_delete',{PHP_po_order:'".$po_key."',PHP_shipdate:'".$shipdate_key."',PHP_shipway:'".$shipway_key."',PHP_carton_no:".$carton_key."},function(data){";
									$jquery_event .= "alert(data);";
									/* $jquery_event .= "if(Number(data) == 1){";
										$jquery_event .= "$('#tbl_".$po_key.$shipdate_key.$shipway_key.$carton_key."').remove();";
									$jquery_event .= "} else { ";
										$jquery_event .= "if(Number(data) == 3){";
											$jquery_event .= "$('#tbl_".$po_key.$shipdate_key.$shipway_key.$carton_key."').remove();";
											$jquery_event .= "$('#po_".$po_key.$shipdate_key."').remove();";
										$jquery_event .= "} else {";
											$jquery_event .= "alert('Delete failure');";
										$jquery_event .= "}";
									$jquery_event .= "}"; */
								$jquery_event .= "});";
								
								
								
								
								
								
								
								
								
								
								
								/* $jquery_event .= "$.post('./packing_list.php?PHP_action=carton_delete',{PHP_po_order:'".$po_key."',PHP_shipdate:'".$shipdate_key."',PHP_shipway:'".$shipway_key."',PHP_carton_no:".$carton_key."},function(data){";
									$jquery_event .= "alert(data);";
									$jquery_event .= "if(Number(data) == 1){";
										$jquery_event .= "$('#tbl_".$po_key.$shipdate_key.$shipway_key.$carton_key."').remove();";
									$jquery_event .= "} else { ";
										$jquery_event .= "if(Number(data) == 3){";
											$jquery_event .= "$('#tbl_".$po_key.$shipdate_key.$shipway_key.$carton_key."').remove();";
											$jquery_event .= "$('#po_".$po_key.$shipdate_key."').remove();";
										$jquery_event .= "} else {";
											$jquery_event .= "alert('Delete failure');";
										$jquery_event .= "}";
									$jquery_event .= "}";
								$jquery_event .= "});"; */
							$jquery_event .= "});";
							$html_packing_list .= "</tr>";

							$html_packing_list .= "</table>";
						}
					}
					$html_packing_list .= "</td>";
					$html_packing_list .= "</tr>" ;
				}
				$my_shipway++; 
			}
		}
		$my_shipdate++;
	}
}
$html_packing_list .= "</table>";
$jquery_packing_list_end .= "</script>";
echo $html_packing_list.$jquery_packing_list_start.$jquery_event.$jquery_packing_list_end ;
break;


case "carton_edit":
//print_r($packing);
//print_r($_POST);
$po_no = $_POST['PHP_po_order'];
$shipdate = $_POST['PHP_shipdate'];
$carton = $_POST['PHP_carton_no'];
$shipway = $_POST['PHP_shipway'];
//$main_sid = $_POST['PHP_main_sid'];
$cust = $_POST['PHP_cust'];
$dept = $_POST['PHP_dept'];

//$t_sql = "select * from packing_main where sid =".$main_sid ;
//$main_array = array();
//$main_array = $packing_list->select_carton($t_sql);
//print_r($main_array);
//$shipto_array = array();
//$shipway = $arry2->select($PO_SHIP,$main_array[0]['shipway'],'edit_shipway_select','select','');
$t_sql = "select po_no,carton_no,pack_style,hanger,shipping_date,shipway,shipping_to,prepack,status,inv_no ";
$t_sql .= "from packing_det ";
$t_sql .= "where carton_no=";
$t_sql .= $carton;
$t_sql .= " and shipping_date='";
$t_sql .= $shipdate;
$t_sql .= "' and shipway='";
$t_sql .= $shipway;
$t_sql .= "' and po_no='";
$t_sql .= $po_no;
$t_sql .= "'";
$t_sql .= " group by po_no,carton_no,shipping_date,shipway";
$t_sql .= " order by po_no,carton_no,shipping_date,shipway";
$main_array = array();
$main_array = $packing_list->select_carton($t_sql);
$shipway_result = $arry2->select($PO_SHIP,$main_array[0]['shipway'],'edit_shipway_select','select','');








$cust_consignee = $packing_list->get_consignee($cust,$dept);
$consignee_id=array();
$consignee_fname=array();
foreach($cust_consignee as $consignee_key=>$consignee_val)
{
	$consignee_id[$consignee_key] = $consignee_val['id'];
	$consignee_fname[$consignee_key] = $consignee_val['f_name'];
}
$consignee_result = $arry2->select($consignee_fname,$main_array[0]['shipping_to'],'consignee_select','select','',$consignee_id); 


$t_sql = "select * from packing_det where carton_no =";
$t_sql .= $carton;
$t_sql .= " and shipping_date='";
$t_sql .= $shipdate;
$t_sql .= "'";
$t_sql .= " and shipway='";
$t_sql .= $shipway;
$t_sql .= "'";
$t_sql .= " and po_no='";
$t_sql .= $po_no;
$t_sql .= "'";
$t_sql .= " order by po_no,carton_no,order_no,order_partial,color,size"; 
//echo $t_sql;
$carton = $packing_list->select_carton($t_sql);

$carton_new = array();
$pack_val = array('bulk','pack');
$pack_fname = array('By Bulk','By Pack');
$pack_result = $arry2->select($pack_fname,$carton[0]['pack_style'],'packstyle_select','select','',$pack_val); 
//print_r($carton);
//echo $t_sql;
$partial_span = array();
$order_span = array();
$order_tmp = "";
$partial_tmp = "";
$color_tmp = "";
$partial_span_count = 0;
$order_span_count  = 0;
$test_debug = "";
foreach($carton as $c_eky => $c_val)
{
	$carton_no = $c_val['carton_no'];
	$main_sid = $c_val['main_sid'];
	$po_no = $c_val['po_no'];
	$hanger = $c_val['hanger'];
	$pack_style = $c_val['pack_style'];
	$prepack = $c_val['prepack'];
	$remark = $c_val['remark'];
	$inv_no = $c_val['inv_no'];
	$shipdate = $c_val['shipping_date'];
	$carton_new[$carton_no][$shipdate][$c_val['order_no']][$c_val['order_partial']][$c_val['color']][$c_val['size']] = $c_val['qty'];
	//$carton_new[$carton_no]['consignee']="";
	if($order_tmp != $c_val['order_no'])
	{
		//1
		if($c_eky == (sizeof($carton)-1))
		{
			//11
			$test_debug .= "11,";
			$carton_new[$carton_no][$shipdate][$order_tmp][$partial_tmp]['span'] = $partial_span_count;
			$carton_new[$carton_no][$shipdate][$order_tmp]['span'] = $order_span_count;
			$order_tmp = $c_val['order_no'];
			$partial_tmp = $c_val['order_partial'];
			$color_tmp = $c_val['color'];
			$partial_span_count = 0;
			$partial_span_count++;
			$order_span_count  = 0;
			$order_span_count++;
			$carton_new[$carton_no][$shipdate][$order_tmp][$partial_tmp]['span'] = $partial_span_count;
			$carton_new[$carton_no][$shipdate][$order_tmp]['span'] = $order_span_count;
		}
		else
		{
			//12
			$test_debug .= "12,";
			if($c_eky != 0 )
			{
				$carton_new[$carton_no][$shipdate][$order_tmp][$partial_tmp]['span'] = $partial_span_count;
				$carton_new[$carton_no][$shipdate][$order_tmp]['span'] = $order_span_count;
			}
			$order_tmp = $c_val['order_no'];
			$partial_tmp = $c_val['order_partial'];
			$color_tmp = $c_val['color'];
			$partial_span_count = 0;
			$partial_span_count++;
			$order_span_count  = 0;
			$order_span_count++;
		}
	}
	else
	{
		//2
		if($partial_tmp == $c_val['order_partial'])
		{
			//21
			if($color_tmp != $c_val['color'])
			{
				//211
				if($c_eky == (sizeof($carton)-1))
				{
					$test_debug .= "2111,";
					$color_tmp = $c_val['color'];
					$partial_span_count++;
					$order_span_count++;
					$carton_new[$carton_no][$shipdate][$order_tmp][$partial_tmp]['span'] = $partial_span_count;
					$carton_new[$carton_no][$shipdate][$order_tmp]['span'] = $order_span_count;
				}
				else
				{
					$test_debug .= "2112,";
					$color_tmp = $c_val['color'];
					$partial_span_count++;
					$order_span_count++;
				}
			}
			else
			{
				//212
				$test_debug .= "212,";
				if($c_eky == (sizeof($carton)-1))
				{
					$carton_new[$carton_no][$shipdate][$order_tmp][$partial_tmp]['span'] = $partial_span_count;
					$carton_new[$carton_no][$shipdate][$order_tmp]['span'] = $order_span_count;
				}
			}
		}
		else
		{
			//22
			if($c_eky == (sizeof($carton)-1))
			{
				//221
				$test_debug .= "221,";
				$carton_new[$carton_no][$shipdate][$order_tmp][$partial_tmp]['span'] = $partial_span_count;
				$partial_tmp = $c_val['order_partial'];
				$color_tmp = $c_val['color'];
				$partial_span_count = 0;
				$partial_span_count++;
				$order_span_count++;
				$carton_new[$carton_no][$shipdate][$order_tmp][$partial_tmp]['span'] = $partial_span_count;
				$carton_new[$carton_no][$shipdate][$order_tmp]['span'] = $order_span_count;
				
			}
			else
			{
				//222
				$test_debug .= "222,";
					$carton_new[$carton_no][$shipdate][$order_tmp][$partial_tmp]['span'] = $partial_span_count;
					$partial_tmp = $c_val['order_partial'];
					$color_tmp = $c_val['color'];
					$partial_span_count = 0;
					$partial_span_count++;
					$order_span_count++;
				
				
			}
		}
	}
}
//print_r($carton);
//print_r($carton_new);
//print_r(sizeof($order_span));
$order_jquery = "";
$jquery_start = "<script type='text/javascript'>";
$jquery_end = "</script>";
$order_table = "";
$partial_tmp = "";
$order_start = true;
$partial_tmp = "";
$color_tmp = "";
$color_tr = 0;
$partial_tr = 0;
$table_count = 0;

foreach($carton_new as $cno_key => $cno_val)
{
	foreach($cno_val as $shipdate_key => $shipdate_val)
	{
		$order_table .= "<table id='tbl_carton".$cno_key."' cellpadding='5' border='1'>";
		$order_table .= "<tr>";
		$order_table .= "<td>";
			$order_table .= "<table>";
			$order_table .= "<tr>";
			$order_table .= "<td align='right'>Purchase Order: </td><td>".$po_no."</td>";
			$order_table .= "</tr>";
			$order_table .= "<tr>";
			$order_table .= "<td align='right'>Carton NO : </td><td>".$cno_key."</td>";
			$order_table .= "</tr>";
			$order_table .= "<tr>";
			$order_table .= "<td align='right'>SHIP DATE : </td>";
			$order_table .= "<td>";
			$order_table .= "<font size='2' style='cursor:pointer;font-weight:bold;' id='edit_calendar-trigger' color='blue'>".$shipdate_key."</font>";
			$order_table .= "<input type='hidden' id='edit_calendar-inputField' name='PHP_edit_shipdate' size='8' value='".$shipdate_key."' />";
			$order_table .= "</td>";
			$order_table .= "</tr>";
			$order_table .= "<tr>";
			$order_table .= "<td align='right'>SHIP WAY : </td>";
			$order_table .= "<td>";
			$order_table .= $shipway_result;
			$order_table .= "</td>";
			$order_table .= "</tr>";
			$order_table .= "<tr>";
			$order_table .= "<td align='right'>Consignee : </td>";
			$order_table .= "<td>";
			$order_table .= $consignee_result;
			$order_table .= "</td>";
			$order_table .= "</tr>";
			$order_table .= "<tr>";
			$order_table .= "<td align='right'>Pack Style : </td>";
			$order_table .= "<td>";
			$order_table .= $pack_result;
			$order_table .= "</td>";
			$order_table .= "</tr>";
			$order_table .= "<tr>";
			$order_table .= "<td align='right'>Hanger : </td>";
			$order_table .= "<td>";
			$order_table .= ($carton[0]['hanger'] == 'checked')?"<input type='checkbox' id='hanger_check' checked />":"<input type='checkbox' id='hanger_check' />";
			$order_table .= "</td>";
			$order_table .= "</tr>";
			$order_table .= "</table>";
		$order_table .= "</td>";
		$order_table .= "</tr>";	
			
			
			
			$order_jquery .= "<script type='text/javascript'>";
			$order_jquery .= "cal_edit = Calendar.setup({";
				$order_jquery .= "weekNumbers : false,";
				$order_jquery .= "trigger     : 'edit_calendar-trigger',";
				$order_jquery .= "inputField  : 'edit_calendar-inputField',";
				$order_jquery .= "date        : ".date("Ymd",$shipdate_key)." ,";
				$order_jquery .= "selection   : ".date("Ymd",$shipdate_key)." ,";
				$order_jquery .= "showTime    : false,";
				$order_jquery .= "onSelect: function() {";
					$order_jquery .= "$('#edit_calendar-trigger').html($('#edit_calendar-inputField').val());";
					$order_jquery .= "cal_edit.hide();";
				$order_jquery .= "}";
			$order_jquery .= "});";
			$order_jquery .= "</script>";
			
			
			
		
		$order_table .= "<tr>";
		$order_table .= "<td>";
		foreach($shipdate_val as $order_key => $order_val)
		{
			$size = $packing_list->get_order_size($order_key);
			$size_array = split(",",$size['size']);
			//$order_table .= $order_key."<br>";//ALR15-0468
			//取出此訂單的尺碼資料，方便比對
			$order_table .= "<table id='tbl_".$order_key.$table_count."' cellpadding='5' border='1'>";
			//塞入尺碼段資料
			$order_table .= "<tr>";
			$order_table .= "<td>Order</td>";
			$order_table .= "<td>Partial</td>";
			$order_table .= "<td>Color</td>";
			foreach( $size_array as $sz_key => $sz_val)
			{
				$order_table .= "<td>";
				$order_table .= $sz_val;
				$order_table .= "</td>";
			}
			$order_table .= "<td></td>";
			$order_table .= "</tr>";
			$order_table .= "<tr>";
				$partial_count = 0;
				$color_count = 0;
				$size_count = 0;
				//$partial_count += sizeof($order_val);
				
				foreach($order_val as $partial_key => $partial_val)
				{
					
					if($partial_key == 'span')
					{
						$partial_count = $partial_count + $partial_val['span'] ;
					}
					if($partial_key != 'span')
					{
						//$order_table .= $partial_key."<br>";//A B C
						//$order_table .= sizeof($partial_val)."<br>";
						//$color_count = $color_count + (int)(sizeof($partial_val));
						foreach($partial_val as $color_key => $color_val)
						{
							
							//$order_table .= $color_key."<br>";//顏色+span
							
							/* if($color_key == 'span')
							{
								$color_count = $color_count + $color_val['span'] ;
							} */
							if($color_key != 'span')
							{
								
								
								if($partial_tr == 0)
								{
									
									if( $color_tr == 0)
									{
										
										
										$order_table .= "<td rowspan=".$order_val['span'].">";
										$order_table .= $order_key;
										$order_table .= "</td>";
										$order_table .= "<td rowspan=".$partial_val['span'].">";
										$order_table .= $partial_key;
										$order_table .= "</td>";
										$order_table .= "<td>";
										$order_table .= $color_key;
										$order_table .= "</td>";
										
										foreach($size_array as $szlab_key => $szlab_val)
										{
											$isize = "";
											$order_table .= "<td>";
											$order_table .= "<input name='".$order_key.$table_count."[".$color_key."][".$szlab_key."]' order='".$order_key."' partial='".$partial_key."' color='".$color_key."' size='".$szlab_val."' type='text' style='width:50px;' value='";
											foreach($color_val as $size_key => $size_val)
											{
												if($szlab_key == 0)
												{
													$size_count += sizeof($size_val);
												}
												
												if("$szlab_val" === "$size_key")
												{
														$order_table .= $size_val;
														$isize = $size_key;
												}
											}
											$order_table .= "' size='".$isize."'>";
											$order_table .= "</td>";
										}
										$order_table .= "<td rowspan='".$order_val['span']."'>";
										$order_table .= "<input type='button' id='save_".$order_key.$table_count."' po_no='".$po_no."' carton_no='".$cno_key."' value='Save'>";
										$order_table .= "</td>";
										$order_table .= "</tr>";
										
										$order_jquery .= "<script type='text/javascript'>";
											$order_jquery .= "$('#save_".$order_key.$table_count."').click(function() {";
												$order_jquery .= "var sizeint = 0;";
												$order_jquery .= "var colorsize = new Array();";
												
												$order_jquery .= "$('input[name^=\"".$order_key.$table_count."\"]').each(function() {";		
													$order_jquery .= "colorsize[sizeint] = $(this).attr('order')+','+$(this).attr('partial')+','+$(this).attr('color')+','+$(this).attr('size')+','+$(this).val(); ";
													$order_jquery .= "sizeint++; ";
												$order_jquery .= "});";
												
												$order_jquery .= "$.post('./packing_list.php?PHP_action=packing_list_update',{PHP_PO:$(this).attr('po_no'),PHP_carton:$(this).attr('carton_no'),PHP_calendar:$('#edit_calendar-inputField').val(),PHP_shipway:$('#edit_shipway_select').find(':selected').val(),PHP_consignee:$('#consignee_select').find(':selected').val(),PHP_packstyle:$('#packstyle_select').find(':selected').val(),PHP_hanger:($('#hanger_check').attr('checked')=='checked')?'checked':'unchecked',PHP_packing:colorsize},function(data) {";
													$order_jquery .= "alert(data);";
												$order_jquery .= "});";

												$order_jquery .= "if(($('#tbl_carton".$cno_key." tr').length - $('table#tbl_".$order_key.$table_count." tr').length) <= 11) {";
													$order_jquery .= "$('#tbl_carton".$cno_key."').remove();";
												$order_jquery .= "} else {";
													$order_jquery .= "$('table#tbl_".$order_key.$table_count."').remove(); ";
												$order_jquery .= "}";
												
											$order_jquery .= "});";
										$order_jquery .= "</script>";
									}
									else
									{
										$order_table .= "<tr>";
										$order_table .= "<td>";
										$order_table .= $color_key;
										$order_table .= "</td>";
										foreach($size_array as $szlab_key => $szlab_val)
										{
											$isize = "";
											$order_table .= "<td>";
											$order_table .= "<input name='".$order_key.$table_count."[".$color_key."][".$szlab_key."]' order='".$order_key."' partial='".$partial_key."' color='".$color_key."' size='".$szlab_val."' type='text' style='width:50px;' value='";
											foreach($color_val as $size_key => $size_val)
											{
												if("$szlab_val" === "$size_key")
												{
														$order_table .= $size_val;
														$isize = $size_key;
												}
											}
											$order_table .= "' size='".$isize."'>";
											$order_table .= "</td>";
										}
										$order_table .= "</tr>";
									}
								}
								else
								{
									if( $color_tr == 0)
									{
										$order_table .= "<tr>";
										$order_table .= "<td rowspan=".$partial_val['span'].">";
										$order_table .= $partial_key;
										$order_table .= "</td>";
										$order_table .= "<td>";
										$order_table .= $color_key;
										$order_table .= "</td>";
										foreach($size_array as $szlab_key => $szlab_val)
										{
											$isize = "";
											$order_table .= "<td>";
											$order_table .= "<input name='".$order_key.$table_count."[".$color_key."][".$szlab_key."]' order='".$order_key."' partial='".$partial_key."' color='".$color_key."' size='".$szlab_val."' type='text' style='width:50px;' value='";
											foreach($color_val as $size_key => $size_val)
											{
												if("$szlab_val" === "$size_key")
												{
														$order_table .= $size_val;
														$isize = $size_key;
												}
											}
											$order_table .= "' size='".$isize."'>";
											$order_table .= "</td>";
										}
										$order_table .= "</tr>";
									}
									else
									{
										$order_table .= "<tr>";
										$order_table .= "<td>";
										$order_table .= $color_key;
										$order_table .= "</td>";
										foreach($size_array as $szlab_key => $szlab_val)
										{
											$isize = "";
											$order_table .= "<td>";
											$order_table .= "<input  name='".$order_key.$table_count."[".$color_key."][".$szlab_key."]' order='".$order_key."' partial='".$partial_key."' color='".$color_key."' size='".$szlab_val."' type='text' style='width:50px;' value='";
											foreach($color_val as $size_key => $size_val)
											{
												if("$szlab_val" === "$size_key")
												{
														$order_table .= $size_val;
														$isize = $size_key;
												}
											}
											$order_table .= "' size='".$isize."'>";
											$order_table .= "</td>";
										}
										$order_table .= "</tr>";
									}
								}
								
							}
							//$order_table .= "<td>".$color_key."</td>";顏色+span
							

							$color_tr++;
						}
						
						$partial_tr++;
						$color_tr = 0;
					}

				}
				//產生一個隱藏檔，儲存陣列數據<hidden>
			
			//$order_table .= "<tr><td><input type='button' value='Save' /></td></tr>";
			
			$order_table .= "</table>";
			//$order_table .= "<input type='hidden' id='hidden_".$order_key.$table_count."' value='".$order_val['span'].","."'>";
			$partial_tr = 0;
			$color_tr = 0;
			$table_count++;
		}
	
	
		$order_table .= "</td>";
		$order_table .= "</tr>";
		$order_table .= "<tr>";
		$order_table .= "<td>";
		$order_table .= "</td>";
		$order_table .= "</tr>";
		$order_table .= "</table>";
	}
	
} 

echo $order_table.$order_jquery;


/* $_GET['PHP_carton_no']
$_GET['PHP_po_no']
$_GET['PHP_shipdate'] */



//page_display($op, '999', 'packing_revise.html');	
break;

case "packing_list_update":
//更新編輯修改資料
/* $_POST['PHP_PO']
$_POST['PHP_carton']
$_POST['PHP_calendar']
$_POST['PHP_shipway']
$_POST['PHP_consignee']
$_POST['PHP_packstyle']
$_POST['PHP_hanger']
$_POST['PHP_packing'] */

foreach($_POST['PHP_packing'] as $packing_key => $packing_val )
{
	$q_str = "";
	$packing_array = split(",",$packing_val);
	$q_str = "update packing_det set qty=".$packing_array[4]." where po_no='";
	$q_str .= $_POST['PHP_PO'];
	$q_str .= "' and carton_no=";
	$q_str .= $_POST['PHP_carton'];
	$q_str .= " and shipping_date='";
	$q_str .= $_POST['PHP_calendar'];
	$q_str .= "' and ";
	
}
print_r($packing_array);
break;
case "carton_delete":
//print_r($_POST);
//刪除時，要確認主表ID，避免子錶已刪除完，主表卻留著，有Invoice號碼，不可刪除
//$test = $_POST['PHP_po_order'];
//echo $test;
$t_sql = "select distinct inv_no from packing_det where carton_no=";
$t_sql .= $_POST['PHP_carton_no']; 
$t_sql .= " and po_no='";
$t_sql .= $_POST['PHP_po_order'];
$t_sql .= "' ";
$t_sql .= "and shipping_date='";
$t_sql .= $_POST['PHP_shipdate'];
$t_sql .= "' ";
$t_sql .= "and shipway='";
$t_sql .= $_POST['PHP_shipway'];
$t_sql .= "' ";
//$myinv = $packing_list->select_carton_inv($t_sql);
//echo $t_sql ;
//print_r($myinv);
if($myinv['inv_no'] == 0)
{
	//要刪除此箱前，先判斷該PO是否還有其他箱號，若是最後一箱，就把主表刪除
	$t_sql = "select distinct carton_no,main_sid from packing_det where ";
	$t_sql .= " po_no='";
	$t_sql .= $_POST['PHP_po_order'];
	$t_sql .= "' ";
	$t_sql .= "and shipping_date='";
	$t_sql .= $_POST['PHP_shipdate'];
	$t_sql .= "' ";
	$t_sql .= "and shipway='";
	$t_sql .= $_POST['PHP_shipway'];
	$t_sql .= "' ";
	$carton = $packing_list->select_carton($t_sql);
	
	
	if(sizeof($carton) == 1)
	{
		//此PO的的最後一箱
		$t_sql_sub = "delete from packing_det where carton_no=";
		$t_sql_sub .= $_POST['PHP_carton_no'];
		$t_sql_sub .= " and po_no='";
		$t_sql_sub .= $_POST['PHP_po_order'];
		$t_sql_sub .= "' ";
		$t_sql_sub .= "and shipping_date='";
		$t_sql_sub .= $_POST['PHP_shipdate'];
		$t_sql_sub .= "' ";
		$t_sql_sub .= "and shipway='";
		$t_sql_sub .= $_POST['PHP_shipway'];
		$t_sql_sub .= "' ";
		//$t_sql_main = "delete from packing_main where sid =".$carton[0]['main_sid'];
		$del_carton = $packing_list->delete_carton($t_sql_sub);
		//$del_carton = $packing_list->delete_carton($t_sql_main);
		if($del_carton)
		{
			$del_carton =3;
		}
		//$del_carton = 'A';
		echo $del_carton;
	}
	else
	{
		$t_sql = "delete from packing_det where carton_no=";
		$t_sql .= $_POST['PHP_carton_no'];
		$t_sql .= " and po_no='";
		$t_sql .= $_POST['PHP_po_order'];
		$t_sql .= "' ";
		$t_sql .= "and shipping_date='";
		$t_sql .= $_POST['PHP_shipdate'];
		$t_sql .= "' ";
		$t_sql .= "and shipway='";
		$t_sql .= $_POST['PHP_shipway'];
		$t_sql .= "' ";
		$del_carton = $packing_list->delete_carton($t_sql);
		//$del_carton = 'B';
		echo $del_carton;
	}
	
	/* $t_sql = "delete from packing_det where carton_no=";
	$t_sql .= $_POST['PHP_carton_no'];
	$t_sql .= " and po_no='";
	$t_sql .= $_POST['PHP_po_order'];
	$t_sql .= "' ";
	$t_sql .= "and shipping_date='";
	$t_sql .= $_POST['PHP_shipdate'];
	$t_sql .= "' ";
	$t_sql .= "and shipway='";
	$t_sql .= $_POST['PHP_shipway'];
	$t_sql .= "' ";
	$del_carton = $packing_list->delete_carton($t_sql);
	echo $del_carton; */
	
}
else
{
	echo 'fail';
}
break;
#
#
} # CASE END
?>