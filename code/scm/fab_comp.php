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
#			monitor.php  ¥Dµ{¦¡
#		for Carnival SCM [Sample]  management
#			Jack Yang     2004/11/10
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

require_once "config.php";
require_once "config.admin.php";
include_once($config['root_dir']."/lib/class.monitor.php");
$PHP_SELF = $_SERVER['PHP_SELF'];

$perm = $GLOBALS['power'];


require_once "init.object.php";
$monitor = new MONITOR();
if (!$monitor->init($mysql,"log")) { print "error!! cannot initialize database for LOG class"; exit; }

$op = array();

// echo $PHP_action.'<br>';

switch ($PHP_action) {
//=======================================================

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "fab_comp":	  //搜尋未完成主料預估用量
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "fab_comp":
check_authority('071',"view");

// creat cust combo box	 取出 客戶代號
$where_str = '';
$where_str = $where_str."order by cust_s_name";
if(!$cust_def = $cust->get_fields('cust_init_name',$where_str)){;  
    $op['msg'][] = "sorry! there is no any customer record in your team, please add customer first!";
    $layout->assign($op);
    $layout->display($TPL_ERROR);  		    
    break;
}
if(!$cust_def_vue = $cust->get_fields('cust_s_name',$where_str)){;  
    $op['msg'][] = "sorry! there is no any customer record in your team, please add customer first!";
    $layout->assign($op);
    $layout->display($TPL_ERROR);  		    
    break;
}
for ($i=0; $i< sizeof($cust_def); $i++) {
    $cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];
}
$op['cust_select'] =  $arry2->select($cust_value,'','PHP_cust','select','',$cust_def_vue); 
$op['factory_select'] = $arry2->select(get_factory_group(),'','PHP_sch_fty','select','');
$op['dept_select'] = $arry2->select(get_dept_group(),"","PHP_dept_code","select","");

$op['msg'] = $wi->msg->get(2);

//080725message增加		
$note=$notify->search($GLOBALS['SCACHE']['ADMIN']['login_id'], "`read`=0");
$op['max_notify'] = $note['max_no'];		

page_display($op,'071', $TPL_FABCOMP_SEARCH);
break;
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "fab_comp":	  //搜尋未完成主料預估用量
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "fab_comp_search":
check_authority('071',"view");

$op = $wi->search_un_fab_comp(1);
$back_str = "&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh."&PHP_sch_fty=".$PHP_sch_fty;
$op['back_str']	 = $back_str;
    
page_display($op,'071', $TPL_FABCOMP_LIST);
break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "fab_comp_edit":	 //進入加入預估用量頁
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//=======================================================
	case "fab_comp_edit":

		check_authority('071',"view");

		// 將 製造令 完整show out ------
		//  wi 主檔

			if(!$op = $wi->get_all($PHP_id)){    //取出該筆 製造令記錄 ID
						$op['msg']= $wi->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
			}

//--------------------------------------------------------------------------
		// 取出 size_scale ----------------------------------------------
		$size_A = $size_des->get($op['smpl']['size']);
		$op['smpl']['size_scale']=$size_A['size_scale'];
		$sizing = explode(",", $size_A['size']);
		
		//  wi_qty 數量檔			
			$where_str2 = " WHERE ord_num = '".$op['wi']['wi_num']."' ORDER BY p_etd";
			$p_id = $order->get_fields('id', $where_str2,'order_partial');
			$p_etd = $order->get_fields('p_etd', $where_str2,'order_partial');
			$op['total'] =0; $op['g_ttl'] = 0;
			for($i=0; $i<sizeof($sizing); $i++)$op['size_ttl'][$i]=0;
			for($i=0; $i<sizeof($p_id); $i++)
			{
				$where_str3 =" WHERE wi_id='".$op['wi']['id']."'  AND p_id='".$p_id[$i]."'";
				$T_wiqty = $wiqty->search(1,$where_str3);
				$num_colors = count($T_wiqty);
				// 做出 size table-------------------------------------------
				$reply = show_breakdown($T_wiqty,$sizing,$size_A['base_size'],$p_etd[$i]);
				$op['show_breakdown'][$i] = $reply['html'];
				$op['total'] += $reply['total'];
				for($j=0; $j<sizeof($sizing); $j++)
				{
					$op['g_ttl'] += $reply['size_ttl'][$j];				
					$op['size_ttl'][$j] +=$reply['size_ttl'][$j];
				}
				
			}
		//-----------------------------------------------------------------
		$mk = 1;
		for($i=0; $i<sizeof($op['lots_use']); $i++)
		{
		//	if($op['lots_use'][$i]['est_1'] == 0)$mk = 0;
			$op['lots_use'][$i]['po'] = $smpl_lots->check_po($op['lots_use'][$i]['id'],'po');
			if($op['lots_use'][$i]['po'] == 0)$mk = 0;
		}
		$op['est_mk'] = $mk;
		
		$op['msg'] = $wi->msg->get(2);
		$op['back_str'] = "&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_etdstr=".$PHP_etdstr."&PHP_etdfsh=".$PHP_etdfsh."&PHP_sch_fty=".$PHP_sch_fty;

		$op['PHP_sr']=$PHP_sr;
		page_display($op,'071', $TPL_FABCOMP_EDIT);			
		break;
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_fab_comp_edit":	 //加入預估用量
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++	
//=======================================================
	case "do_fab_comp_edit":

		check_authority('071',"view");
		$tmp='';
		$x=0;$k=0;
		$parm_bom = array();
		
		$ord_size = $bom->get_ord_size($PHP_wi_id);
		$ord_size = explode(',',$ord_size['size']);
		
		$bom_size = '';
		#取得qty
		$p_id = $bom->get_p_id($PHP_wi_id);
		$r=0;
		$sum_ttl = 0;
		$s = array();
		for($i=0;$i<sizeof($p_id);$i++){
			$ord_qty = $bom->get_ord_qty($p_id[$i]['id']);
			
			for($j=0;$j<sizeof($ord_qty);$j++){
				$ttl = 0;
				$tmp_qty = explode(',',$ord_qty[$j]['qty']);
				foreach($ord_size as $key=>$value){
					$bom_size[$r][$value] = $tmp_qty[$key];
					$s[$value]+=$bom_size[$r][$value];//顏色總合計
					$ttl+=$tmp_qty[$key];
				}
				$bom_size[$r]['cut_ttl'] = $ttl;
				$sum_ttl += $ttl;
				$r++;
			}
		}
		//print_r($s);exit;
		#加一行總計
		foreach($ord_size as $kk=>$vv){
			$bom_size[$r][$vv] = $s[$vv];
		}
		$bom_size[$r]['cut_ttl'] = $sum_ttl;
		
		
		foreach($PHP_lots_est as $key => $value)
		{
			$where_str="WHERE wi_id = '$PHP_wi_id' and lots_used_id = '".$key."' and dis_ver = 0 order by id" ;
			
			$bom_lots = $bom->get_fields_lots('qty',$where_str);
			$lots_id = $bom->get_fields_lots('id',$where_str);
			if (count($bom_lots))
			{
				for ($i=0; $i<sizeof($bom_lots); $i++)
				{
					$tmp_bom=explode(',',$bom_lots[$i]);
					for ($j=0; $j < sizeof($tmp_bom); $j++)
					{
						if ($tmp_bom[$j]=='' ||$tmp_bom[$j]==0)
						{
							$qty=0;
						}else{
							$qty=$bom_size[$j]['cut_ttl']*$value;
						}
						$tmp=$tmp.$qty.",";
					}
					$tmp=substr($tmp,0,-1);	
					$parm_bom[$k]=array($lots_id[$i],'qty',$tmp,'bom_lots');
					$k++;
					//echo "<br>bom lots qty = ".$tmp."<br>";
					$tmp='';
				}
			}
			$parm_lots[$x]=array($key,'est_1',$value);			
			$x++;			
		}
			
		for ($i=0; $i<sizeof($parm_bom); $i++)		$bom->update_field($parm_bom[$i]);
		for ($i=0; $i<sizeof($parm_lots); $i++)		$smpl_lots->update_field($parm_lots[$i]);
				
		$parm=array($PHP_wi_id,'revise',($PHP_revise+1))	;
		$wi->update_field($parm);

		$parm=array($PHP_wi_id,'marked',1);
		$wi->update_field($parm);


//-------------------------------------------
//  以上完成修改
//-------------------------------------------

			if(!$op = $wi->get_all($PHP_id)){    //取出該筆 製造令記錄 ID
						$op['msg']= $wi->msg->get(2);
						$layout->assign($op);
						$layout->display($TPL_ERROR);  		    
						break;
			}
/*
		//  wi_qty 數量檔 ---------------------------------------------
		$where_str = " WHERE wi_id='".$op['wi']['id']."' ";
		$T_wiqty = $wiqty->search(1,$where_str);

		$num_colors = count($T_wiqty);

		// 取出 size_scale ----------------------------------------------
		$size_A = $size_des->get($op['smpl']['size']);
		$op['smpl']['size_scale']=$size_A['size_scale'];
			//????????????????????????????????????? 注意 有時沒有 size type 資料時??????
		$sizing = explode(",", $size_A['size']);

			// 做出 size table-------------------------------------------
		$reply = show_breakdown($T_wiqty,$sizing,$size_A['base_size']);
		$op['show_breakdown'] = $reply['html'];
		$op['total'] = $reply['total'];
		//-----------------------------------------------------------------
*/		
		# 記錄使用者動態
		$message = "successfully update fabric consump. on WI :".$op['wi']['wi_num'];
		$log->log_add(0,"106A",$message);
		$op['msg'][] = $message;





		$op = $wi->search_un_fab_comp();

		$op['PHP_sr']=$PHP_sr;
		$op['back_str'] = $PHP_back_str;
		page_display($op,'071', $TPL_FABCOMP_LIST);
//		page_display($op, 10, 6, $TPL_FABCOMP_SHOW);			
		break;
	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "fab_comp_submit":	  //搜尋未完成主料預估用量
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "fab_comp_submit":
 	check_authority('071',"add");

		$parm=array($PHP_wi_id,'marked',1);
		$wi->update_field($parm);


		$op = $wi->search_un_fab_comp();

		# 記錄使用者動態
		$message = "successfully submit fabric consump. on WI :".$PHP_wi_num;
		$log->log_add(0,"106S",$message);
		$op['msg'][] = $message;
		
		
	page_display($op,'071', $TPL_FABCOMP_LIST);
	break;	
	
//-------------------------------------------------------------------------

}   // end case ---------

?>
