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
//require_once "lib/excel/reader.php";
include_once($config['root_dir']."/lib/class.invoice_import.php");
//include_once($config['root_dir']."/lib/class.warehousing.php");
//include_once($config['root_dir']."/lib/excel/reader.php");

$invoice_import = new INVOICE_IMPORT();
if (!$invoice_import->init($mysql,"log")) { print "error!! cannot initialize database for LOG class"; exit; }
/* $warehousing = new WAREHOUSING();
if (!$warehousing->init($mysql,"log")) { print "error!! cannot initialize database for LOG class"; exit; } */


// print_r($mysql);
#
#
#
$PHP_SELF = $_SERVER['PHP_SELF'];
$perm = $GLOBALS['power'];
$op = array();
#
#
# Template
$TPL_INVOICE_IMPORT = 'invoice_import.html';
/* $TPL_ARRIVAL_TIME_SEARCH = 'arrival_time_search.html'; */
#
#
#
$AUTH = '108';
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
//$op['css'] = array( 'css/scm.css' );
//$op['js'] = array( 'js/jquery.min.js' , 'js/jquery.blockUI.js' , 'js/supl_ship.js' );

page_display($op,$AUTH,'invoice_import.html');
break;
#
#
case "cf_invoice_import":
	check_authority('108',"edit");
	//echo $PHP_action."<br>";
	//check_authority($AUTH,"add");
	//print_r($op);
	$ext = explode(".", $_FILES['PHP_csv_file']['name']);
	
	$ext_size = sizeof($ext);
	if($ext[$ext_size-1] <> "csv"){
		echo "<script>alert(' 匯入的檔案必須是 csv檔 ');history.go(-1);</script>";
	} 
	//print_r($ext);
	//exit;
	
	$csvtext = array();
	$str_val = array();
	$po_ary = array();
	$csvtext = file($_FILES['PHP_csv_file']['tmp_name']);
	
    $csvtext_size = sizeof($csvtext);
	$Invoice_import_list=array();//Invoice_import
	$cust_consignee=array();
	$tmp_invoice = '';
	$tmp_po='';
	$tmp_ship_date='';
	$tmp_style='';
	$tmp_color='';
	$tmp_size='';
	$calc_qty=0;
	$calc_fob=0;
	$tmp_dollar_unit ='';
    for($i=1;$i<$csvtext_size;$i++){
        $csv_val = explode(",", $csvtext[$i]);
		/* print_r($chk_val);
		echo "<br>"; */
		/* $calc_qty = 0;
		$calc_fob = 0; */
		/* print_r($chk_val);
		echo "<br>"; */
		//print_r($chk_val);
		foreach($csv_val as $chk_key=>$chk_val)
		{
			//$val_temp = strtoupper(trim($chk_val));
			//print_r($chk_val);
			//echo "<br>";
			$val_temp = strtoupper(trim($chk_val));
			//echo $val_temp;
			//echo $val_temp."<br>";
			switch($val_temp)
			{
				case "CUST#:":
					$get_cust = true;
				break;
				case "INV#:":
					$get_invoice = true;
				break;
				case "PO#:":
					//換PO，記錄
					$get_po = true;
				break;
				case "STYLE#:":
					$get_style = true;
				break;
				case "COLOR#:":
				
					//開始抓顏色資料;
					//還需要判斷是否為不同顏色
					$get_color =true;
				break;
				case "SHIPDATE#:":
					//出貨日期Shipment Date:
					$get_ship_date =true;
				break;
				case "FINISH#:":
					//結束
					$get_color =false;
				break;
				#######################################################
				
				case "INVOICE NO#:":
					$get_invoice = true;
				break;
				
				case "P.O. #:":
					//換PO，記錄
					$get_po = true;
				break;
				case "STYLE #:":
					$get_style = true;
				break;
				
				case "STYLE NO#:":
					$get_style = true;
				break;
				
				
				case "SAILING ON OR ABOUT#:":
					//出貨日期SAILING ON OR ABOUT:
					$get_ship_date =true;
				break;
				case "SHIP ON/ABOUT#:":
					//出貨日期SHIP ON/ABOUT 
					$get_ship_date =true;
				break;
				case "ON/ABOUT#:":
					//出貨日期SHIP ON/ABOUT 
					$get_ship_date =true;
				break;
				case "SHIPMENT DATE#:":
					//出貨日期Shipment Date:
					$get_ship_date =true;
				break;
				default:
					if($get_cust)
					{	
						$tmp_cust = trim($val_temp);
						$get_cust = false;
					}
					if(!$tmp_cust)
					{
						$tmp_cust='TH';
					}
					if($get_ship_date)
					{
						$tmp_ship_date = trim($chk_val);
						$op['ship_date'] = date("Y-m-d",strtotime($tmp_ship_date));
						$op['ship_dates'] = date("Ymd",strtotime($tmp_ship_date));
						$get_ship_date = false;
					}
					else
					{
						$op['ship_date'] = date("Y-m-d");
						$op['ship_dates'] = date("Ymd");
					}
					
					if($get_invoice)
					{
						$tmp_invoice = trim($chk_val);
						$get_invoice = false;
						/* echo $tmp_invoice;
						exit; */
					}
					if($get_po)
					{
						$tmp_po =  str_replace(' ','_',trim($chk_val));
						$get_po = false;
						//echo $tmp_po;
						/*exit; */
					}
					if($get_style)
					{
						$tmp_style = trim($chk_val);
						$get_style = false;
						//echo $tmp_style;
						/* exit; */
					}
					//echo $tmp_invoice .";;".$tmp_po.";;".$tmp_style."<br>";
					
					
					############################################################################################
					
					if(($tmp_cust !='') && ($tmp_invoice !=''))
					{
						//echo $tmp_cust;//." ".$tmp_invoice;
						/* echo "<br>"; */
						switch($tmp_cust)
						{
							case "TH":
							//consignee
							$op['cust'] = $tmp_cust;
							$op['fty'] = 'CF';
							$cust_consignee = $invoice_import->get_consignee($tmp_cust);
							$consignee_id=array();
							$consignee_fname=array();
							foreach($cust_consignee as $consignee_key=>$consignee_val)
							{
								$consignee_id[$consignee_key] = $consignee_val['id'];
								$consignee_fname[$consignee_key] = $consignee_val['f_name'];
							}
							$op['consignee'] = $arry2->select($consignee_fname,'','consignee_select','select','',$consignee_id);
							//echo $tmp_po.",".$tmp_style."<br>";
							if(($tmp_po !='') && ($tmp_style!=''))
							{
								if($get_color)
								{
									if($chk_key == 1)
									{
										if($chk_val[$chk_key]!='')
										{
											$get_color =false;
										}
										
									}
									else
									{
										if($chk_key == 2)
										{	
											if( trim($chk_val) != '')
											{
												//表示同款更換顏色
												$tmp_color = $chk_val;
												$op['color_check'] ='yescolor';
											}
										}
										else
										{
											if($chk_key == 3)
											{
												if(trim($chk_val) == '')
												{
													//沒有尺碼
													$tmp_size ='';
												}
												else
												{
													//有尺碼
													$tmp_size = $chk_val;
													$op['size_check'] ='yessize';
												}
											}
											else
											{
												if($tmp_size == '')
												{
													//沒有尺碼的Invoice
													if(trim($chk_val) !='')
													{
														if($chk_key==4)
														{
															$Invoice_import_list[$tmp_invoice][$tmp_po][$tmp_style][$tmp_color]['nosize']['qty'] = $chk_val;
															$calc_qty=$chk_val;
														}
														if($chk_key==6)
														{
															$tmp_dollar_unit = $chk_val;
														}
														if($chk_key==7)
														{
															$Invoice_import_list[$tmp_invoice][$tmp_po][$tmp_style][$tmp_color]['nosize']['fob'] =$tmp_dollar_unit." ".$chk_val;
															$calc_fob=$chk_val;
															$Invoice_import_list[$tmp_invoice][$tmp_po][$tmp_style][$tmp_color]['nosize']['amount'] = $tmp_dollar_unit ." ".($calc_fob * $calc_qty) ;
														}									
													}
													else
													{
														if($chk_key==6)
														{
															
															$tmp_dollar_unit ='US$';
														
														}
													}
													
												}
												else
												{
													//有尺碼的Invoice
													if(trim($chk_val) !='')
													{
														//$Invoice_import[$tmp_invoice][$tmp_po][$tmp_style][$tmp_color][$tmp_size][] = $chk_val;
														if($chk_key==4)
														{
															$Invoice_import_list[$tmp_invoice][$tmp_po][$tmp_style][$tmp_color][$tmp_size]['qty'] = $chk_val;
															$calc_qty=$chk_val;
														}
														if($chk_key==6)
														{

															$tmp_dollar_unit = $chk_val;
															/* $Invoice_import[$tmp_invoice][$tmp_po][$tmp_style][$tmp_color][$tmp_size]['dollar_unit'] = $chk_val; */
															//$tmp_dollar_unit = $chk_val;
														}
														if($chk_key==7)
														{
															//$Invoice_import[$tmp_invoice][$tmp_po][$tmp_style][$tmp_color][$tmp_size]['fob'] = $Invoice_import[$tmp_invoice][$tmp_po][$tmp_style][$tmp_color][$tmp_size]['dollar_unit'] . " " . $chk_val;
															$Invoice_import_list[$tmp_invoice][$tmp_po][$tmp_style][$tmp_color][$tmp_size]['fob'] = $tmp_dollar_unit ." ". $chk_val;
															$calc_fob=$chk_val;
															//$Invoice_import[$tmp_invoice][$tmp_po][$tmp_style][$tmp_color][$tmp_size]['amount'] = $Invoice_import[$tmp_invoice][$tmp_po][$tmp_style][$tmp_color][$tmp_size]['dollar_unit'] . " " . ($calc_fob * $calc_qty) ;
															$Invoice_import_list[$tmp_invoice][$tmp_po][$tmp_style][$tmp_color][$tmp_size]['amount'] = $tmp_dollar_unit ." ".($calc_fob * $calc_qty) ;
														} 
														/* $Invoice_import[$tmp_invoice][$tmp_po][$tmp_style][$tmp_color]['nosize']['ship_date'] =date("Y-m-d",strtotime($tmp_ship_date)) ; */	
													}
													else
													{
														if($chk_key==6)
														{
															
															$tmp_dollar_unit ='US$';
														
														}
													}
												}
												
											}
											
										}
										
										
									}
									
									
								}
							}
							break;
							
						}
					}
					
					######################################################################
					
					
					
					
			}
			/* echo "<br>"; */
		}
    }
	//print_r($Invoice_import_list);
	/* print_r($Invoice_import_list);
	exit; */
	if(trim($tmp_invoice) == '')
	{
		if(trim($tmp_po) == '')
		{
			if(trim($tmp_style) == '')
			{
				//echo "invoice + po + style = NULL";
				echo "<script language='javascript'> alert('No Invoice No. and PO No. and Style No.');</script>";
			}
			else
			{
				//echo "invoice + po = NULL";
				echo "<script language='javascript'> alert('No Invoice No. and PO No.');</script>";
			}
		}
		else
		{
			if(trim($tmp_style) == '')
			{
				//echo "invoice + style = NULL";
				echo "<script language='javascript'> alert('No Invoice No. and Style No.');</script>";
			}
			else
			{
				//echo "invoice = NULL";
				echo "<script language='javascript'> alert('No Invoice No.');</script>";
			}
		}
		//echo "invoice = NULL";
	}
	else
	{
		if(trim($tmp_po) == '')
		{
			if(trim($tmp_style) == '')
			{
				//echo "po + style = NULL";
				echo "<script language='javascript'> alert('No PO No. and Style No.');</script>";
			}
			else
			{
				//echo "po = NULL";
				echo "<script language='javascript'> alert('No PO No.');</script>";
			}
		}
		else
		{
			if(trim($tmp_style) == '')
			{
				//echo "style = NULL";
				echo "<script language='javascript'> alert('No Style No.');</script>";
			}
		}
	} 
	$span_color=0;
	$span_style=0;
	$span_po=0;
	$span_inv=0;
	$tmp_po='';
	$tmp_style='';
	$tmp_color='';
	$tmp_inv_exist=false;
	//print_r($Invoice_import_list);
	foreach($Invoice_import_list as $inv_key => $inv_val)
	{
		$tmp_inv_exist = $invoice_import->checkinv($inv_key);
		//echo $tmp_inv_exist;
		foreach($inv_val as $po_key => $po_val)
		{
			
			foreach($po_val as $style_key => $style_val)
			{
				
				foreach($style_val as $color_key => $color_val)
				{
					
					foreach($color_val as $size_key => $size_val)
					{
						//echo $size_key."<br>";
						$span_inv++;
						$span_po++;
						$span_style++;
						$span_color++;
						if(strtoupper($color_key) !='HANGER')
						{
							$ttl_qty = $ttl_qty + $size_val['qty'];
							
						}
						$amount = substr($size_val['amount'],4);
						$ttl_amount = $ttl_amount + $amount;
					}
					$Invoice_import_list[$inv_key][$po_key][$style_key][$color_key]['span'] = $span_color;
					$span_color=0;
				}
				$Invoice_import_list[$inv_key][$po_key][$style_key]['span'] = $span_style;
				$span_style=0;
			}
			$Invoice_import_list[$inv_key][$po_key]['span'] = $span_po;
			$span_po=0;
		}
		$Invoice_import_list[$inv_key]['span'] = $span_inv;
		$span_inv = 0;
	}

	/* print_r($Invoice_import_list);
	exit; */
	//$Invoice_import_new = array();
	//$Invoice_import_new = $Invoice_import_list;
	//print_r($Invoice_import_list);
	
	if($tmp_inv_exist)
	{
		//echo "yes";
		$op['inv_no'] = $inv_key;
		//有資料撈出給使用者
		$inv_array = array();
		$inv_array = $invoice_import->select_inv($inv_key);
		$inv_array_new = array();
		foreach($inv_array as $inv_key => $inv_val)
		{
			if($inv_val['color']=='nocolor')
			{
				if($inv_val['size']=='nosize')
				{
					//沒顏色沒尺碼
					$inv_array_new['both'][]= $inv_val;
				}
				else
				{
					//沒顏色
					$inv_array_new['nocolor'][]= $inv_val;
				}
			}
			else
			{
				if($inv_val['size']=='nosize')
				{
					//沒尺碼
					$inv_array_new['nosize'][]= $inv_val;
				}
				else
				{
					//都有
					$inv_array_new['all'][]= $inv_val;
				}
			}
			
		}
		//print_r($inv_array_new);
		
		$op['Invoice_import_list'] = $inv_array_new;
		page_display($op,$AUTH,'invoice_import_view.html');	
		
	}
	else
	{
		//echo "no";
		//資料庫沒資料，直接讓使用者編輯訂單號
		$op['ttl_qty'] = $ttl_qty;
		$op['ttl_amount'] = $ttl_amount;
		//print_r($Invoice_import_list);
		$op['Invoice_import']= $Invoice_import_list;
		//print_r($op);
		//print_r($op);
		page_display($op,$AUTH,'invoice_import.html');	
	}
	
		
break;
case "ly_invoice_import":
	check_authority('108',"edit");
	//echo $PHP_action."<br>";
	check_authority($AUTH,"add");
	
	
	$ext = explode(".", $_FILES['PHP_csv_file']['name']);
	
	$ext_size = sizeof($ext);
	if($ext[$ext_size-1] <> "csv"){
		echo "<script>alert(' 匯入的檔案必須是 csv檔 ');history.go(-1);</script>";
	} 
	//print_r($ext);
	//exit;
	
	$csvtext = array();
	$str_val = array();
	$po_ary = array();
	$csvtext = file($_FILES['PHP_csv_file']['tmp_name']);
	$tmp_finish = false;
    $csvtext_size = sizeof($csvtext);
	$Invoice_import_list=array();//Invoice_import
	$tmp_cust='';
	$tmp_invoice = '';
	$tmp_po='';
	$tmp_style='';
	$tmp_color='';
	$tmp_size='';
	$tmp_qty='';
	$tmp_fob=0;
	$tmp_unit='';
	
	/* echo $csvtext_size;
	exit; */
    for($i=1;$i<$csvtext_size;$i++){
        $csv_val = explode(",", $csvtext[$i]);
		//print_r($chk_val);
		//echo "<br>";
		//$qty=0;
		//print_r($csv_val);
		foreach($csv_val as $chk_key=>$chk_val)
		{
			
			$val_temp = strtoupper(trim($chk_val));
			switch($val_temp)
			{
				//每個客戶的INV長的不同，所以要判斷客戶再決定開關值怎麼給
				case "CUST#:":
					$get_cust = true;
					
				break;
				case "INV#:":
					
					$get_invoice = true;
				break;
				case "PO#:":
					if($tmp_cust == 'CB' || $tmp_cust == 'RE')
					{
						$get_po = true;
						$get_qty = true;
					}
					if($tmp_cust == 'LR')
					{
						$get_po = true;
						$get_style = true;
					}
					if($tmp_cust == 'CH')
					{
						//Lauren另一品牌
						$get_po = true;
						$get_style = true;
					}
					if($tmp_cust == 'NI')
					{
						$get_po = true;
						$get_qty = true;
					}
					if($tmp_cust == 'BK')
					{
						$get_po = true;
						$get_style = true;
					}
					//換PO，記錄
					//$get_po = true;
				break;
				case "STYLE#:":
					if($tmp_cust == 'CB' || $tmp_cust == 'RE')
					{
						$get_style = true;
						//$get_qty = true;
					}
					if($tmp_cust == 'LR')
					{
						$get_style = true;
						$get_qty = true;
					}
					if($tmp_cust == 'CH')
					{
						//Lauren另一品牌
						$get_style = true;
						$get_qty = true;
					}
					if($tmp_cust == 'NI')
					{
						$get_style = true;
					}
					/* if($tmp_cust == 'BK')
					{
						$get_style = true;
						$get_qty = true;
					}  */
					//$get_style = true;
				break;
				case "COLOR#:":
					//Liyuen沒顏色
					
				
					//開始抓顏色資料;
					//還需要判斷是否為不同顏色
					//$get_color =true;
				break;
				case "SHIPDATE#:":
					if($tmp_cust == 'CB' || $tmp_cust == 'RE')
					{
						$get_ship_date =true;
					}
					if($tmp_cust == 'LR')
					{
						$get_ship_date =true;
					}
					if($tmp_cust == 'CH')
					{
						//Lauren另一品牌
						$get_ship_date =true;
					}
					if($tmp_cust == 'NI')
					{
						$get_ship_date =true;
					}
					if($tmp_cust == 'BK')
					{
						$get_ship_date =true;
					}
					//出貨日期Shipment Date:
					//$get_ship_date =true;
				break; 
				
				default:

				if($get_ship_date)
				{
					$tmp_ship_date = trim($chk_val);
					$op['ship_date'] = date("Y-m-d",strtotime($tmp_ship_date));
					$op['ship_dates'] = date("Ymd",strtotime($tmp_ship_date));
					$get_ship_date = false;
				}
				else
				{
					$op['ship_date'] = date("Y-m-d");
					$op['ship_dates'] = date("Ymd");
				}
				if($get_cust)
				{	
					$tmp_cust = trim($chk_val);
					$get_cust = false;
				}
				if($get_invoice)
				{
					$tmp_invoice = trim($chk_val);
					$get_invoice = false;
					
				}
				if(($tmp_cust !='') && ($tmp_invoice !=''))
				{
					switch($tmp_cust)
					{
						case "BK":

							$op['cust'] = $tmp_cust;
							$op['fty'] = 'LY';
							$cust_consignee = $invoice_import->get_consignee($tmp_cust);
							$consignee_id=array();
							$consignee_fname=array();
							foreach($cust_consignee as $consignee_key=>$consignee_val)
							{
								$consignee_id[$consignee_key] = $consignee_val['id'];
								$consignee_fname[$consignee_key] = $consignee_val['f_name'];
							}
							$op['consignee'] = $arry2->select($consignee_fname,'','consignee_select','select','',$consignee_id);
							if($get_po)
							{
								
								if(trim($chk_val)!='')
								{
									//$tmp_po = trim($chk_val);
									$tmp_po = str_replace(' ','_',trim($chk_val));
									$get_po = false;
								}
							} 
							else
							{
								if($get_style)
								{		
									if($chk_key == 3)
									{
										if(trim($chk_val)!='')
										{
											$tmp_style=$chk_val;
										}
									}
									else
									{
										if(trim($chk_val)!='')
										{
											if($chk_key == 5)
											{
												$Invoice_import_list[$tmp_invoice][$tmp_po][$tmp_style]['nocolor']['nosize']['qty'] = $chk_val;
											}
											if($chk_key == 6)
											{
												$Invoice_import_list[$tmp_invoice][$tmp_po][$tmp_style]['nocolor']['nosize']['fob'] = 'US$ '.substr($chk_val,1,strlen($chk_val));
											}
											if($chk_key == 7)
											{
												$Invoice_import_list[$tmp_invoice][$tmp_po][$tmp_style]['nocolor']['nosize']['amount'] = 'US$ '.$chk_val;
											}
										}
									}
								} 
							}
							//print_r($Invoice_import_list);
						break;
						case "NI":
							//consignee
							$op['cust'] = $tmp_cust;
							$op['fty'] = 'LY';
							$cust_consignee = $invoice_import->get_consignee($tmp_cust);
							$consignee_id=array();
							$consignee_fname=array();
							foreach($cust_consignee as $consignee_key=>$consignee_val)
							{
								$consignee_id[$consignee_key] = $consignee_val['id'];
								$consignee_fname[$consignee_key] = $consignee_val['f_name'];
							}
							$op['consignee'] = $arry2->select($consignee_fname,'','consignee_select','select','',$consignee_id);
							if($get_po)
							{
								//$tmp_po = trim($chk_val);
								$tmp_po = str_replace(' ','_',trim($chk_val));
								$get_po = false;
							}
							else
							{
								
								if($get_qty)
								{
									if($chk_key == 6)
									{
										$tmp_qty = trim(substr($chk_val, 0, -4)); 
									}
									if($chk_key == 7)
									{
										$tmp_unit = $chk_val;
									}
									if($chk_key == 8)
									{
										$tmp_fob = $chk_val;
									}
								}
								if($get_style)
								{
									$tmp_style = $chk_val;
									$tmp_finish = true;
									$get_style = false;
								}
							}
						break;
						case "CB":
							//consignee
							$op['cust'] = $tmp_cust;
							$op['fty'] = 'LY';
							$cust_consignee = $invoice_import->get_consignee($tmp_cust);
							$consignee_id=array();
							$consignee_fname=array();
							foreach($cust_consignee as $consignee_key=>$consignee_val)
							{
								$consignee_id[$consignee_key] = $consignee_val['id'];
								$consignee_fname[$consignee_key] = $consignee_val['f_name'];
							}
							$op['consignee'] = $arry2->select($consignee_fname,'','consignee_select','select','',$consignee_id);
							if($get_po)
							{
								//$tmp_po = trim($chk_val);
								$tmp_po = str_replace(' ','_',trim($chk_val));
								$get_po = false;
							}
							else
							{
								if($get_qty)
								{
									if($chk_key == 6)
									{
										$tmp_qty = trim(substr($chk_val, 0, -4));
										//echo $tmp_qty."<br>";
									}
									if($chk_key == 7)
									{
										$tmp_unit = $chk_val;
									}
									if($chk_key == 8)
									{
										$tmp_fob = $chk_val;
									}
								}
								if($get_style)
								{
									$tmp_style = $chk_val;
									$tmp_finish = true;
									$get_style = false;
								}
							}
							//echo $tmp_qty."<br>";
							//echo $tmp_fob."<br>";
						break;
						case "RE":
							//consignee
							$op['cust'] = $tmp_cust;
							$op['fty'] = 'LY';
							$cust_consignee = $invoice_import->get_consignee($tmp_cust);
							$consignee_id=array();
							$consignee_fname=array();
							foreach($cust_consignee as $consignee_key=>$consignee_val)
							{
								$consignee_id[$consignee_key] = $consignee_val['id'];
								$consignee_fname[$consignee_key] = $consignee_val['f_name'];
							}
							$op['consignee'] = $arry2->select($consignee_fname,'','consignee_select','select','',$consignee_id);
							if($get_po)
							{
								//$tmp_po = trim($chk_val);
								$tmp_po = str_replace(' ','_',trim($chk_val));
								$get_po = false;
							}
							else
							{
								if($get_qty)
								{
									if($chk_key == 6)
									{
										//echo $chk_val."<br>";
										//$tmp_qty = trim($chk_val);
										$tmp_qty = trim(substr($chk_val, 0, -4));
									}
									if($chk_key == 7)
									{
										$tmp_unit = $chk_val;
									}
									if($chk_key == 8)
									{
										$tmp_fob = $chk_val;
									}
								}
								if($get_style)
								{
									$tmp_style = $chk_val;
									$tmp_finish = true;
									$get_style = false;
								}
							}
						break;
						case "LR":
							//consignee
							$op['cust'] = $tmp_cust;
							$op['fty'] = 'LY';
							$cust_consignee = $invoice_import->get_consignee($tmp_cust);
							$consignee_id=array();
							$consignee_fname=array();
							foreach($cust_consignee as $consignee_key=>$consignee_val)
							{
								$consignee_id[$consignee_key] = $consignee_val['id'];
								$consignee_fname[$consignee_key] = $consignee_val['f_name'];
							}
							$op['consignee'] = $arry2->select($consignee_fname,'','consignee_select','select','',$consignee_id);
							if($get_po)
							{
								//$tmp_po = trim($chk_val);
								$tmp_po = str_replace(' ','_',trim($chk_val));
								$get_po = false;
							}
							else
							{
								if($get_style)
								{
									$tmp_style = $chk_val;
									$get_style =false;
								}
								else
								{
									if($get_qty)
									{
										if($chk_key == 5)
										{
											$Invoice_import_list[$tmp_invoice][$tmp_po][$tmp_style]['nocolor']['nosize']['qty'] = $chk_val;
										}
										if($chk_key == 8)
										{
											$Invoice_import_list[$tmp_invoice][$tmp_po][$tmp_style]['nocolor']['nosize']['fob'] = "US$ ".$chk_val;
											//trim(substr($chk_val, 0, -4));
										}
										if($chk_key == 10)
										{
											$Invoice_import_list[$tmp_invoice][$tmp_po][$tmp_style]['nocolor']['nosize']['amount'] = 'US$ '.$chk_val;
											//$Invoice_import_list[$tmp_invoice][$tmp_po][$tmp_style]['nocolor']['nosize']['amount'] = 'US$ '.
										}
										//$Invoice_import_list[$tmp_invoice][$tmp_po][$tmp_style]['nocolor']['nosize'][] = $chk_val;
									}
								}
							}
						break;
						case "CH":
							//consignee
							$op['cust'] = $tmp_cust;
							$op['fty'] = 'LY';
							$cust_consignee = $invoice_import->get_consignee($tmp_cust);
							$consignee_id=array();
							$consignee_fname=array();
							foreach($cust_consignee as $consignee_key=>$consignee_val)
							{
								$consignee_id[$consignee_key] = $consignee_val['id'];
								$consignee_fname[$consignee_key] = $consignee_val['f_name'];
							}
							$op['consignee'] = $arry2->select($consignee_fname,'','consignee_select','select','',$consignee_id);
							if($get_po)
							{
								//$tmp_po = trim($chk_val);
								$tmp_po = str_replace(' ','_',trim($chk_val));
								$get_po = false;
							}
							else
							{
								if($get_style)
								{
									$tmp_style = $chk_val;
									$get_style =false;
								}
								else
								{
									if($get_qty)
									{
										if($chk_key == 5)
										{
											$Invoice_import_list[$tmp_invoice][$tmp_po][$tmp_style]['nocolor']['nosize']['qty'] = $chk_val;
										}
										if($chk_key == 8)
										{
											$Invoice_import_list[$tmp_invoice][$tmp_po][$tmp_style]['nocolor']['nosize']['fob'] = "US$ ".$chk_val;
											//trim(substr($chk_val, 0, -4));
										}
										if($chk_key == 10)
										{
											$Invoice_import_list[$tmp_invoice][$tmp_po][$tmp_style]['nocolor']['nosize']['amount'] = 'US$ '.$chk_val;
											//$Invoice_import_list[$tmp_invoice][$tmp_po][$tmp_style]['nocolor']['nosize']['amount'] = 'US$ '.
										}
										//$Invoice_import_list[$tmp_invoice][$tmp_po][$tmp_style]['nocolor']['nosize'][] = $chk_val;
									}
								}
							}
						break;
						default:
					}
				}
				
			}	
				
		}
		//print_r($Invoice_import_list);
		//exit;
		if($tmp_cust == 'BK')
		{
			$get_style=false;
		}	
		if($tmp_cust == 'NI')
		{
			$get_qty=false;
			if($tmp_finish)
			{
				$Invoice_import_list[$tmp_invoice][$tmp_po][$tmp_style]['nocolor']['nosize']['qty'] = $tmp_qty;
				//$Invoice_import_list[$tmp_invoice][$tmp_po][$tmp_style]['nocolor']['nosize'][1] = $tmp_unit;
				$Invoice_import_list[$tmp_invoice][$tmp_po][$tmp_style]['nocolor']['nosize']['fob'] = $tmp_unit." ".$tmp_fob;
				$Invoice_import_list[$tmp_invoice][$tmp_po][$tmp_style]['nocolor']['nosize']['amount'] = $tmp_unit." ".($tmp_qty*$tmp_fob);
				$tmp_finish =false;
			}
			
		}	
		if($tmp_cust == 'CB' || $tmp_cust == 'RE')
		{
			$get_qty=false;
			if($tmp_finish)
			{
				$Invoice_import_list[$tmp_invoice][$tmp_po][$tmp_style]['nocolor']['nosize']['qty'] = $tmp_qty;
				$Invoice_import_list[$tmp_invoice][$tmp_po][$tmp_style]['nocolor']['nosize']['fob'] = $tmp_unit." ".$tmp_fob;
				$Invoice_import_list[$tmp_invoice][$tmp_po][$tmp_style]['nocolor']['nosize']['amount'] = $tmp_unit." ".($tmp_qty*$tmp_fob);
				$tmp_finish =false;
			}
			
		}
		if($tmp_cust == 'LR')
		{
			$get_qty=false;
			
		}	
		if($tmp_cust == 'CH')
		{
			$get_qty=false;
			
		}			
		
	}
	if(trim($tmp_invoice) == '')
	{
		if(trim($tmp_po) == '')
		{
			if(trim($tmp_style) == '')
			{
				//echo "invoice + po + style = NULL";
				echo "<script language='javascript'> alert('No Invoice No. and PO No. and Style No.');</script>";
			}
			else
			{
				//echo "invoice + po = NULL";
				echo "<script language='javascript'> alert('No Invoice No. and PO No.');</script>";
			}
		}
		else
		{
			if(trim($tmp_style) == '')
			{
				//echo "invoice + style = NULL";
				echo "<script language='javascript'> alert('No Invoice No. and Style No.');</script>";
			}
			else
			{
				//echo "invoice = NULL";
				echo "<script language='javascript'> alert('No Invoice No.');</script>";
			}
		}
		//echo "invoice = NULL";
	}
	else
	{
		if(trim($tmp_po) == '')
		{
			if(trim($tmp_style) == '')
			{
				//echo "po + style = NULL";
				echo "<script language='javascript'> alert('No PO No. and Style No.');</script>";
			}
			else
			{
				//echo "po = NULL";
				echo "<script language='javascript'> alert('No PO No.');</script>";
			}
		}
		else
		{
			if(trim($tmp_style) == '')
			{
				//echo "style = NULL";
				echo "<script language='javascript'> alert('No Style No.');</script>";
			}
		}
	}
	/* print_r($Invoice_import_list);
	exit; */
	$span_color=0;
	$span_style=0;
	$span_po=0;
	$span_inv=0;
	$tmp_po='';
	$tmp_style='';
	$tmp_color='';
	$tmp_inv_exist=false;
	$ttl_qty = 0;
	$ttl_amount=0;
	//print_r($Invoice_import_list);
	
	foreach($Invoice_import_list as $inv_key => $inv_val)
	{
		$tmp_inv_exist = $invoice_import->checkinv($inv_key);
		//echo $tmp_inv_exist;
		foreach($inv_val as $po_key => $po_val)
		{
			
			foreach($po_val as $style_key => $style_val)
			{
				
				foreach($style_val as $color_key => $color_val)
				{
					
					foreach($color_val as $size_key => $size_val)
					{
						//echo $size_key."<br>";
						$span_inv++;
						$span_po++;
						$span_style++;
						$span_color++;

						$ttl_qty = $ttl_qty + $size_val['qty'];
						
						$amount = $size_val['qty'] * trim(substr($size_val['fob'],3,strlen($size_val['fob'])));
						
						$ttl_amount = $ttl_amount + $amount;
						$Invoice_import_list[$inv_key][$po_key][$style_key][$color_key][$size_key]['amount'] = "US$ " . $amount;
					}
					$Invoice_import_list[$inv_key][$po_key][$style_key][$color_key]['span'] = $span_color;
					$span_color=0;
				}
				$Invoice_import_list[$inv_key][$po_key][$style_key]['span'] = $span_style;
				$span_style=0;
			}
			$Invoice_import_list[$inv_key][$po_key]['span'] = $span_po;
			$span_po=0;
		}
		$Invoice_import_list[$inv_key]['span'] = $span_inv;
		$span_inv = 0;
	}
	$op['ttl_qty'] = $ttl_qty;
	$op['ttl_amount'] = $ttl_amount;
	
	//print_r($Invoice_import_list);
	//exit;
	if($tmp_inv_exist)
	{
		//echo "yes";
		$op['inv_no'] = $inv_key;
		//有資料撈出給使用者
		$inv_array = array();
		$inv_array = $invoice_import->select_inv($inv_key);
		$inv_array_new = array();
		foreach($inv_array as $inv_key => $inv_val)
		{
			if($inv_val['color']=='nocolor')
			{
				if($inv_val['size']=='nosize')
				{
					//沒顏色沒尺碼
					$inv_array_new['both'][]= $inv_val;
				}
				else
				{
					//沒顏色
					$inv_array_new['nocolor'][]= $inv_val;
				}
			}
			else
			{
				if($inv_val['size']=='nosize')
				{
					//沒尺碼
					$inv_array_new['nosize'][]= $inv_val;
				}
				else
				{
					//都有
					$inv_array_new['all'][]= $inv_val;
				}
			}
			
		}
		$op['Invoice_import_list'] = $inv_array_new;
		//print_r($op);
		page_display($op,$AUTH,'invoice_import_view.html');	
		
	}
	else
	{
		//echo "no";
		//資料庫沒資料，直接讓使用者編輯訂單號
		$op['Invoice_import']= $Invoice_import_list;
		page_display($op,$AUTH,'invoice_import.html');	
	}
			
break;
#
#
case "save_csv_file":
//print_r($_SESSION);
check_authority('108',"edit");
//echo $GLOBALS['SCACHE']['ADMIN']['login_id'];
//exit;
/* print_r($_POST);
exit;*/
/* if($GLOBALS['SCACHE']['ADMIN']['login_id']=='morial')
{
print_r($_POST);
exit;
}  */

foreach($_POST['PHP_INVOICE'] as $inv_key => $inv_value)
{
	//echo $inv_key.":<br>";
	$op['inv_no'] = $inv_key;
	//先判斷該INVOICE是否存在資料庫
	
	$inv_exist = $invoice_import->checkinv($inv_key);
	
	//echo $inv_exist;
	
	if($inv_exist)
	{
		
		//該INVOICE存在，從資料庫撈出給使用者更新
		//echo "true";
		//有資料撈出給使用者
		$inv_array = array();
		
		$op['ship_date'] = date("Y-m-d",strtotime($inv_shipdate_array['ship_date']));
		$inv_array_new = array();
		foreach($inv_array as $inv_key => $inv_val)
		{
			if($inv_val['color']=='nocolor')
			{
				if($inv_val['size']=='nosize')
				{
					//沒顏色沒尺碼
					$inv_array_new['both'][]= $inv_val;
				}
				else
				{
					//沒顏色
					$inv_array_new['nocolor'][]= $inv_val;
				}
			}
			else
			{
				if($inv_val['size']=='nosize')
				{
					//沒尺碼
					$inv_array_new['nosize'][]= $inv_val;
				}
				else
				{
					//都有
					$inv_array_new['all'][]= $inv_val;
				}
			}
			
		}
		
		$op['Invoice_import_list'] = $inv_array_new;
	
		
		page_display($op,$AUTH,'invoice_import_view.html');	
	}
	else
	{
		
		//新增shipping_doc資料表的insert語法 $_POST['PHP_ship_date']
		$t_sql = "insert into shipping_doc(inv_num,consignee_id,cust,factory,ttl_qty,ttl_amt,ship_date,open_user,open_date) values('";
		$t_sql .= $inv_key;
		$t_sql .= "',";
		$t_sql .= $_POST['consignee_select'];
		$t_sql .= ",'";
		$t_sql .= $_POST['PHP_cust'];
		$t_sql .= "','";
		$t_sql .= $_POST['PHP_fty'];
		$t_sql .= "',";
		$t_sql .= $_POST['PHP_ttl_qty'];
		$t_sql .= ",";
		$t_sql .= $_POST['PHP_ttl_amount'];
		$t_sql .= ",'";
		$t_sql .= $_POST['PHP_ship_date'];
		$t_sql .= "','";
		$t_sql .= $GLOBALS['SCACHE']['ADMIN']['login_id'];
		$t_sql .= "','";
		$t_sql .= date("Y-m-d");//需格式化
		$t_sql .= "')";
		
		//echo "select * from shipping_doc where inv_num='".$inv_key."'"."<br>";
		/* $inv_exist = $invoice_import->check_invoice_exist("select * from shipping_doc where inv_num='".$inv_key."'");
		if($inv_exist)
		{
			echo "<script language='javascript'>alert('INVOICE exist !!');</script>";
			page_display($op,$AUTH,'invoice_import.html');	
		}
		else
		{ */
			$myid = $invoice_import->sql_i_shipping_doc($t_sql);
			
			//echo $myid;
			//exit;
			//該INVOICE不存在於資料庫中
			//echo "false";
			//$ttl_qty=0;//Invoice總件數
			//$ttl_amount=0;//Invoice總件數的總金額
			//新增invoice_import資料表的insert語法
			$insert_array = array();
			foreach($inv_value as $po_key => $po_value)
			{
				//echo $po_key.":";
				foreach($po_value as $style_key => $style_value)
				{
					//echo $style_key."#";
					foreach($style_value as $color_key => $color_value)
					{
						if($color_key == 'nocolor')
						{
							foreach($color_value as $size_key => $size_value)
							{
								if($size_key == 'nosize')
								{
									//沒顏色沒尺碼
									//insert into invoice_import(inv_no,po,style,color,size,qty,unit,fob,dollar_unit,order) values();
									$scm = trim(substr($size_value['SCM'],0,strlen($size_value['SCM'])));
									$qty = trim(substr($size_value['qty'],0,strlen($size_value['qty'])));
									$fob = trim(substr($size_value['fob'],4,(strlen($size_value['fob'])-4)));
									$dollar_unit = trim(substr($size_value['fob'],0,4));
									//$amount = trim(substr($size_value['amount'],4,(strlen($size_value['amount'])-4)));
									$amount = $qty*$fob;
									$t_sql = "insert into invoice_import(inv_no,shipping_doc_id,po,style,color,size,qty,unit,fob,dollar_unit,amount,ord_num,ship_date) values('";
									$t_sql .= $inv_key;
									$t_sql .= "',";
									$t_sql .= $myid;
									$t_sql .= ",'";
									$t_sql .= $po_key;
									$t_sql .= "','";
									$t_sql .= $style_key;
									$t_sql .= "','";
									$t_sql .= $color_key;
									$t_sql .= "','";
									$t_sql .= $size_key;
									$t_sql .= "',";
									$t_sql .= $qty;
									$t_sql .= ",'";
									$t_sql .= "PCS";
									$t_sql .= "',";
									$t_sql .= $fob;
									$t_sql .= ",'";
									$t_sql .= $dollar_unit;
									$t_sql .= "',";
									$t_sql .= $amount;
									$t_sql .= ",'";
									$t_sql .= $scm;
									$t_sql .= "','";
									$t_sql .= $_POST['PHP_ship_date'];
									$t_sql .= "')";
									/* if($GLOBALS['SCACHE']['ADMIN']['login_id']=='morial')
									{
										echo $t_sql;
										exit;
									} */
									$insert_array[]=$t_sql;
									/* $ttl_qty = $ttl_qty + $qty;
									$ttl_amount = $ttl_amount + ($fob * $qty); */
									//echo $t_sql."<br>";
									
								}
								else
								{
									//沒顏色
									$scm = trim(substr($size_value['SCM'],0,strlen($size_value['SCM'])));
									$qty = trim(substr($size_value['qty'],0,strlen($size_value['qty'])));
									$fob = trim(substr($size_value['fob'],4,(strlen($size_value['fob'])-4)));
									$dollar_unit = trim(substr($size_value['fob'],0,4));
									//$amount = trim(substr($size_value['amount'],4,(strlen($size_value['amount'])-4)));
									$amount = $qty*$fob;
									$t_sql = "insert into invoice_import(inv_no,shipping_doc_id,po,style,color,size,qty,unit,fob,dollar_unit,amount,ord_num,ship_date) values('";
									$t_sql .= $inv_key;
									$t_sql .= "',";
									$t_sql .= $myid;
									$t_sql .= ",'";
									$t_sql .= $po_key;
									$t_sql .= "','";
									$t_sql .= $style_key;
									$t_sql .= "','";
									$t_sql .= $color_key;
									$t_sql .= "','";
									$t_sql .= $size_key;
									$t_sql .= "',";
									$t_sql .= $qty;
									$t_sql .= ",'";
									$t_sql .= "PCS";
									$t_sql .= "',";
									$t_sql .= $fob;
									$t_sql .= ",'";
									$t_sql .= $dollar_unit;
									$t_sql .= "',";
									$t_sql .= $amount;
									$t_sql .= ",'";
									$t_sql .= $scm;
									$t_sql .= "','";
									$t_sql .= $_POST['PHP_ship_date'];
									$t_sql .= "')";
									$insert_array[]=$t_sql;
									/* $ttl_qty = $ttl_qty + $qty;
									$ttl_amount = $ttl_amount + ($fob * $qty); */
									//echo $t_sql."<br>";
								}
							}
						}
						else
						{
							foreach($color_value as $size_key => $size_value)
							{
								
								if($size_key == 'nosize')
								{
									//沒尺碼
									$scm = trim(substr($size_value['SCM'],0,strlen($size_value['SCM'])));
									$qty = trim(substr($size_value['qty'],0,strlen($size_value['qty'])));
									$fob = trim(substr($size_value['fob'],4,(strlen($size_value['fob'])-4)));
									$dollar_unit = trim(substr($size_value['fob'],0,4));
									//$amount = trim(substr($size_value['amount'],4,(strlen($size_value['amount'])-4)));
									$amount = $qty*$fob;
									$t_sql = "insert into invoice_import(inv_no,shipping_doc_id,po,style,color,size,qty,unit,fob,dollar_unit,amount,ord_num,ship_date) values('";
									$t_sql .= $inv_key;
									$t_sql .= "',";
									$t_sql .= $myid;
									$t_sql .= ",'";
									$t_sql .= $po_key;
									$t_sql .= "','";
									$t_sql .= $style_key;
									$t_sql .= "','";
									$t_sql .= $color_key;
									$t_sql .= "','";
									$t_sql .= $size_key;
									$t_sql .= "',";
									$t_sql .= $qty;
									$t_sql .= ",'";
									$t_sql .= "PCS";
									$t_sql .= "',";
									$t_sql .= $fob;
									$t_sql .= ",'";
									$t_sql .= $dollar_unit;
									$t_sql .= "',";
									$t_sql .= $amount;
									$t_sql .= ",'";
									$t_sql .= $scm;
									$t_sql .= "','";
									$t_sql .= $_POST['PHP_ship_date'];
									$t_sql .= "')";
									$insert_array[]=$t_sql;
									/* $ttl_qty = $ttl_qty + $qty;
									$ttl_amount = $ttl_amount + ($fob * $qty); */
									//echo $t_sql."<br>";
								}
								else
								{
									//都有
									//insert into invoice_import(inv_no,po,style,color,size,qty,unit,fob,dollar_unit,order) values();
									
									$scm = trim(substr($size_value['SCM'],0,strlen($size_value['SCM'])));
									$qty = trim(substr($size_value['qty'],0,strlen($size_value['qty'])));
									$fob = trim(substr($size_value['fob'],4,(strlen($size_value['fob'])-4)));
									$dollar_unit = trim(substr($size_value['fob'],0,4));
									//$amount = trim(substr($size_value['amount'],4,(strlen($size_value['amount'])-4)));
									$amount = $qty*$fob;
									$t_sql = "insert into invoice_import(inv_no,shipping_doc_id,po,style,color,size,qty,unit,fob,dollar_unit,amount,ord_num,ship_date) values('";
									$t_sql .= $inv_key;
									$t_sql .= "',";
									$t_sql .= $myid;
									$t_sql .= ",'";
									$t_sql .= $po_key;
									$t_sql .= "','";
									$t_sql .= $style_key;
									$t_sql .= "','";
									$t_sql .= $color_key;
									$t_sql .= "','";
									$t_sql .= $size_key;
									$t_sql .= "',";
									$t_sql .= $qty;
									$t_sql .= ",'";
									$t_sql .= "PCS";
									$t_sql .= "',";
									$t_sql .= $fob;
									$t_sql .= ",'";
									$t_sql .= $dollar_unit;
									$t_sql .= "',";
									$t_sql .= $amount;
									$t_sql .= ",'";
									$t_sql .= $scm;
									$t_sql .= "','";
									$t_sql .= $_POST['PHP_ship_date'];
									$t_sql .= "')";
									$insert_array[]=$t_sql;
									/* $ttl_qty = $ttl_qty + $qty;
									$ttl_amount = $ttl_amount + ($fob * $qty); */
									//echo $t_sql."<br>";
								}
							}	
						}
					}
				}
			}
			
			//[consignee_select] 
			//$_POST['consignee_select'];
			/* print_r($insert_array);
			exit; */
			//陣列sql語法
			$invoice_import->sql_i_invoice($insert_array);
			//有資料撈出給使用者
			$inv_array = array();
			$inv_array = $invoice_import->select_inv($inv_key);
			$op['inv_no'] = $inv_key;
			$inv_status_array = $invoice_import->select_status($inv_key);
			$inv_shipdate_array = $invoice_import->select_shipdate($inv_key);
			$op['ship_date'] = date("Y-m-d",strtotime($inv_shipdate_array['ship_date']));
			$op['ship_dates'] = date("Ymd",strtotime($inv_shipdate_array['ship_date']));
			$op['inv_status'] = $inv_status_array['status'];
			$ttl_qty=0;
			$ttl_amount=0;
			$inv_array_new = array();
			foreach($inv_array as $inv_key => $inv_val)
			{
				if(strtoupper($inv_val['color']) != 'HANGER')
				{
					//echo $inv_val['color'];
					$ttl_qty = $ttl_qty + $inv_val['qty'];
				}
				$ttl_amount = $ttl_amount + ($inv_val['fob'] * $inv_val['qty']);
				$inv_array_new['all'][]= $inv_val;
				/* if($inv_val['color']=='nocolor')
				{
					if($inv_val['size']=='nosize')
					{
						//沒顏色沒尺碼
						$inv_array_new['both'][]= $inv_val;
					}
					else
					{
						//沒顏色
						$inv_array_new['nocolor'][]= $inv_val;
					}
				}
				else
				{
					if($inv_val['size']=='nosize')
					{
						//沒尺碼
						$inv_array_new['nosize'][]= $inv_val;
					}
					else
					{
						//都有
						$inv_array_new['all'][]= $inv_val;
					}
				} */
				
			}
			/* print_r($inv_array_new);
			exit; */
			$op['ttl_qty'] = $ttl_qty;
			$op['ttl_amount'] = $ttl_amount;
			$op['Invoice_import_list'] = $inv_array_new;
			page_display($op,$AUTH,'invoice_import_edit.html');	
		//}
	}
	
	
	//$op['Invoice_import_list'] = $inv_array_new;
	//print_r($op);
	/* print_r($op);
	exit; */
	//page_display($op,$AUTH,'invoice_import_view.html');		
}
break;
#
#
case "view_invoice":
check_authority('108',"edit");
//print_r($_POST);
//exit;
		$op['inv_no'] = $PHP_INV_NO;
		$inv_status_array = $invoice_import->select_status($PHP_INV_NO);
		
		$op['inv_status'] = $inv_status_array['status'];
		$inv_array = array();
		$inv_array = $invoice_import->select_inv($PHP_INV_NO);
		$inv_shipdate_array = $invoice_import->select_shipdate($PHP_INV_NO);
		$op['ship_date'] = date("Y-m-d",strtotime($inv_shipdate_array['ship_date']));
		//print_r($inv_array);
		$ttl_qty=0;
		$ttl_amount=0;
		$inv_array_new = array();
		if(sizeof($inv_array) > 0)
		{
			//echo "Data in DB";
			foreach($inv_array as $inv_key => $inv_val)
			{
				if(strtoupper($inv_val['color']) != 'HANGER')
				{
					$ttl_qty = $ttl_qty + $inv_val['qty'];
				}
				$ttl_amount = $ttl_amount + ($inv_val['fob'] * $inv_val['qty']);
				$inv_array_new['all'][]= $inv_val;
				/* if($inv_val['color']=='nocolor')
				{
					if($inv_val['size']=='nosize')
					{
						//沒顏色沒尺碼
						$inv_array_new['both'][]= $inv_val;
					}
					else
					{
						//沒顏色
						$inv_array_new['nocolor'][]= $inv_val;
					}
				}
				else
				{
					if($inv_val['size']=='nosize')
					{
						//沒尺碼
						$inv_array_new['nosize'][]= $inv_val;
					}
					else
					{
						//都有
						$inv_array_new['all'][]= $inv_val;
					}
				} */
				
			}
			$op['ttl_qty'] = $ttl_qty;
			$op['ttl_amount'] = $ttl_amount;
			$op['Invoice_import_list'] = $inv_array_new;
			//print_r($op);
			page_display($op,$AUTH,'invoice_import_view.html');	
		}
		else
		{
			echo "<script language='javascript'>alert('No Invoice in DB!!')</script>";
			page_display($op,$AUTH,'invoice_import.html');	
		}
		
	
break;
#
#
case "delete_invoice":
//check_authority('108',"delete");
//$op['inv_no'] = $PHP_INV_NO;
$inv = $_POST['PHP_INV_NO'];
//echo $inv;
$inv_status_array = $invoice_import->select_status($inv);
$inv_status = $inv_status_array['status'];

if($inv_status == 0)
{
	//未submit前可以delete
	$del_maintable = "delete from shipping_doc where inv_num = '".trim($inv)."'";
	
	$del_subtable = "delete from invoice_import where inv_no='".trim($inv)."'";
	//echo $del_maintable."<br>";
	$main_table = $invoice_import->sql_del_invoice($del_maintable);
	$sub_table = $invoice_import->sql_del_invoice($del_subtable);
	if($main_table and $sub_table)
	{
		echo  $inv_status.",1";
		//return '1,1';
	}
	else
	{
		echo $inv_status.",0";
		//return '1,0';
		//echo $del_maintable."<br>";
	}
}
else
{
	//submit後不可delete
	echo $inv_status.",0";
	//return '0,0';
} 
//sql_del_invoice


break;
#
#
case "edit_invoice":
check_authority('108',"edit");
		//print_r($_POST);
		$op['inv_no'] = $PHP_INV_NO;
		$inv_array = array();
		//$op['shipdate']
		$inv_array = $invoice_import->select_inv($PHP_INV_NO);
		$inv_status_array = $invoice_import->select_status($PHP_INV_NO);
		$op['inv_status'] = $inv_status_array['status'];
		$inv_shipdate_array = $invoice_import->select_shipdate($PHP_INV_NO);
		$op['ship_date'] = date("Y-m-d",strtotime($inv_shipdate_array['ship_date']));
		$op['ship_dates'] = date("Ymd",strtotime($inv_shipdate_array['ship_date']));
		$ttl_qty=0;
		$ttl_amount=0;
		$inv_array_new = array();
		foreach($inv_array as $inv_key => $inv_val)
		{
			if(strtoupper($inv_val['color']) != 'HANGER')
			{
				$ttl_qty = $ttl_qty + $inv_val['qty'];
			}
			$ttl_amount = $ttl_amount + ($inv_val['fob'] * $inv_val['qty']);
			$inv_array_new['all'][]= $inv_val;
			/* if($inv_val['color']=='nocolor')
			{
				if($inv_val['size']=='nosize')
				{
					//沒顏色沒尺碼
					//$inv_array_new['both'][]= $inv_val;
					$inv_array_new['all'][]= $inv_val;
				}
				else
				{
					//沒顏色
					//$inv_array_new['nocolor'][]= $inv_val;
					$inv_array_new['all'][]= $inv_val;
				}
			}
			else
			{
				if($inv_val['size']=='nosize')
				{
					//沒尺碼
					//$inv_array_new['nosize'][]= $inv_val;
					$inv_array_new['all'][]= $inv_val;
				}
				else
				{
					//都有
					$inv_array_new['all'][]= $inv_val;
				}
			} */
			
		}
		$op['ttl_qty'] = $ttl_qty;
		$op['ttl_amount'] = $ttl_amount;
		$op['Invoice_import_list'] = $inv_array_new;
	//print_r($op);
page_display($op,$AUTH,'invoice_import_edit.html');	
break;
#
#
case "invoice_edit_save":
check_authority('108',"edit");
	$inv_no=$_POST['inv'];
	$my_id=$_POST['thisid'];
	$qty=$_POST['qty'];
	$fob=$_POST['fob'];
	$remark=$_POST['remark'];
	$amount=$_POST['amount'];
	$ord=$_POST['ord_num']; 
	if($_POST['shipping_doc_id'])
	{	//表示Invoice改變

		$inv_status = $invoice_import->select_status($inv_no);
		if($inv_status['status'] == 2)
		{
			//已經Confirm
			echo 'Confirmed';
			
		}
		else
		{
			$t_sql = 'update invoice_import ';
			$t_sql .= 'set inv_no="'.$inv_no;
			$t_sql .= '",shipping_doc_id='.$_POST['shipping_doc_id'];
			$t_sql .= ',qty='.$qty;
			$t_sql .= ',fob='.$fob;
			$t_sql .= ',amount='.$amount;
			$t_sql .= ',remark="'.$remark;
			$t_sql .= '",ord_num="'.$ord;
			$t_sql .= '" where id='.$my_id;
			$status = $invoice_import->sql_u_invoice($t_sql);
			//echo $t_sql;
			if($status == 1)
			{
				$status ='success';
			}
			else
			{
				$status ='failure';
			}
			echo $status;
		}
	}
	else
	{
		$t_sql = 'update invoice_import ';
		$t_sql .= 'set inv_no="'.$inv_no;
		$t_sql .= '",qty='.$qty;
		$t_sql .= ',fob='.$fob;
		$t_sql .= ',amount='.$amount;
		$t_sql .= ',remark="'.$remark;
		$t_sql .= '",ord_num="'.$ord;
		$t_sql .= '" where id='.$my_id;
		$status = $invoice_import->sql_u_invoice($t_sql);
		if($status == 1)
		{
			$status ='success';
		}
		else
		{
			$status ='failure';
		}
		echo $status;
	}
	
	
	
	
	
	
//echo $status;
break;
#
#
case "get_shipdoc_id":
check_authority('108',"edit");

$inv_array = $invoice_import->select_inv($_POST['inv']);
if(sizeof($inv_array) > 0)
{
echo $inv_array[0]['shipping_doc_id'];
}
else
{
 echo 'no';
}
//echo $status;
break;
#
#

case "change_shipdate":
check_authority('108',"edit");

//print_r($_POST);
//detail table update
$old_data = $invoice_import->select_inv($_POST['inv']);
//print_r($old_data);


$t_sql = 'update invoice_import ';
$t_sql .= 'set ship_date="'.$_POST['shipdate'];
$t_sql .= '" where inv_no="'.$_POST['inv'];
$t_sql .= '"';
$update1 = $invoice_import->sql_u_invoice($t_sql);
//echo $t_sql;
$t_sql = 'update shipping_doc ';
$t_sql .= 'set ship_date="'.$_POST['shipdate'];
$t_sql .= '" where inv_num="'.$_POST['inv'];
$t_sql .= '"';
$update2 = $invoice_import->sql_u_invoice($t_sql); 
//echo $t_sql;
if($update1 and $update2)
{
	//兩個資料表都有改到
	echo true;
}
else
{
	//只更新到一個或都沒更新，需恢復原shipping date
	$t_sql = 'update invoice_import ';
	$t_sql .= 'set ship_date="'.$old_data['ship_date'];
	$t_sql .= '" where inv_no='.$_POST['inv'];
	$update1 = $invoice_import->sql_u_invoice($t_sql);

	$t_sql = 'update shipping_doc ';
	$t_sql .= 'set ship_date="'.$old_data['ship_date'];
	$t_sql .= '" where inv_num='.$_POST['inv'];
	$update2 = $invoice_import->sql_u_invoice($t_sql);
	echo false;
	
}

/* $t_sql = 'update invoice_import ';
		$t_sql .= 'set inv_no="'.$inv_no;
		$t_sql .= '",qty='.$qty;
		$t_sql .= ',fob='.$fob;
		$t_sql .= ',amount='.$amount;
		$t_sql .= ',remark="'.$remark;
		$t_sql .= '",ord_num="'.$ord;
		$t_sql .= '" where id='.$my_id;
		$status = $invoice_import->sql_u_invoice($t_sql); */
//echo $status;
break;
#
#

case "check_sorder":

$s_order = $invoice_import->get_sorder($_POST['PHP_order']);
//print_r($s_order);
if(sizeof($s_order) > 0)
{
	echo true;
}
else
{
	echo false;
}

break;
#
#

#
#
} # CASE END
?>