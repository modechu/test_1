<?php
session_start();

session_register	('SCACHE');
session_register	('PAGE');
session_register	('authority');
session_register	('where_str');
session_register	('parm');
session_register	('PHP_ses_etd');
session_register	('PHP_unstatus');


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

require_once "config.php";
require_once "config.admin.php";

$PHP_SELF = $_SERVER['PHP_SELF'];

$perm = $GLOBALS['power'];

require_once "init.object.php";

$op = array();


switch ($PHP_action) {



#++++++++++++++    SAMPLE ORDER +++++++++++++  2006/11/08  +++++++++++++++++
#		 job 92    樣本 報價工作單記錄系統 
#++++++++++++++++++++++++++++++++++++++++++++  2006/11/09  +++++++++++++++++
#		case "offer":			job 92
#		case "add_offer":		job 92A
#		case "do_add_offer":	job 92A
#		case "offer_search":	job 92V
#		case "offer_view":		job 92V offer view
#		case "offer_edit":
#		case "do_offer_edit":


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "offer":	job 92
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "offer":

		check_authority('062',"view");
	
		$where_str = $manager = $dept_id = '';
		$dept_ary = array();

		$sales_dept_ary = get_sales_dept(); //  [不含K0] ------
		$my_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];   // 判定進入身份的指標

		$my_team = $GLOBALS['SCACHE']['ADMIN']['team_id'];


		// for ($i=0; $i<count($sales_dept_ary);$i++){
			// if($my_dept == $sales_dept_ary[$i]){
				// 如果是業務部進入 則dept_code 指定該業務部---
				// $dept_id = $sales_dept_ary[$i];  
//				$where_str = " WHERE dept = '".$dept_id."' ";
			// }
		// }

		if (!$dept_id) { // 當不是業務部人[也不含 K0 的人 ]進入時
			$manager = 1;
			// 業務部門 select選單
			$dept_ary = $arry2->select($sales_dept_ary,$GLOBALS['SCACHE']['ADMIN']['dept'],"PHP_dept_code","select",'get_dept(this)');  
		}

		$op['manager_flag'] = $manager;
		$op['dept_id'] = $dept_id;
		$op['dept_ary'] = $dept_ary;

		// creat cust combo box	 取出 客戶代號

		$where_str=$where_str."order by cust_s_name"; //依cust_s_name排序
		if(!$cust_def = $cust->get_fields('cust_init_name',$where_str)){;  //取出客戶簡稱
			$op['msg'][] = "sorry! there is no any customer record in your team, please add customer first!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//取出客戶代號
		for ($i=0; $i< sizeof($cust_def); $i++)
		{
			$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//以 [客戶代號-客戶簡稱] 呈現
		}
		$op['cust_select'] =  $arry2->select($cust_value,'','PHP_cust','select','check_add(this)',$cust_def_vue); 

		if (count($cust_def) == 0){  // 當找不到客戶資料時......
			$op['msg'][] = "sorry! selected department doesn't have any customer record yet, please add customer for this Team first !";
			 $layout->assign($op);
			 $layout->display($TPL_ERROR);		    	    
				break;
		}

		$style_def = $style_type->get_fields('style_type');   // 取出 款式類別
		$op['style_type'] =  $arry2->select($style_def,'','SCH_style_type','select','');


//080725message增加		
	$note=$notify->search($GLOBALS['SCACHE']['ADMIN']['login_id'], "`read`=0");
	$op['max_notify'] = $note['max_no'];

		page_display($op,'062', $TPL_OFFER);			
		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "add_offer":	job 92A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "add_offer":

		check_authority('062',"add");

		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];  

			$op['cust_id'] = $PHP_cust;

		// 傳入參數


			$where_str="ORDER BY cust_s_name"; //依cust_s_name排序 
			$cust_def = $cust->get_fields('cust_init_name',$where_str);
			$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//取出客戶代號
			for ($i=0; $i< sizeof($cust_def); $i++)
			{
				$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//以 [客戶代號-客戶簡稱] 呈現
			}
			
			$op['agent_select'] =  $arry2->select($cust_value,'','PHP_agent','select','',$cust_def_vue); 


//樣本單列表
			$s_date=increceDaysInDate($TODAY,-365); //12個月前
			$where="where last_update > '".$s_date."' and cust='".$PHP_cust."'  order by num";
		
			$smpl_no=$smpl_ord->get_fields("num",$where);		
	    $j=0;
	    if ($smpl_no){
				$sml_no[0]=substr($smpl_no[0],0,9);
				for($i=1;$i< sizeof($smpl_no); $i++)
				{
					if($sml_no[$j] != substr($smpl_no[$i],0,9)){	
						$j++;
						$sml_no[$j]=substr($smpl_no[$i],0,9);
					}
				}
			}else{
				$sml_no=array('');
			}
			$op['of']['smpl_select']=$arry2->select($sml_no,'','PHP_smpl_ord','select','');



		$op['of']['date'] = $TODAY;
		$op['of']['cust'] = $PHP_cust;
		$op['of']['merchan'] = $GLOBALS['SCACHE']['ADMIN']['login_id']; 
		$op['of']['dept_code'] = $PHP_dept_code;
		$op['of']['fty'] = $arry2->select($FACTORY,'','PHP_fty','select','');
		$style_def = $style_type->get_fields('style_type');   // 取出 款式類別
		$op['of']['style_type'] =  $arry2->select($style_def,'','PHP_style_type','select','');

		// create new PS number ------
		$sr_key = substr($THIS_YEAR,2,2).substr($TODAY,5,2).substr($TODAY,8,2).'-';

		$op['num'] = $sr_key.'xx';
		
		page_display($op,'062', $TPL_OFFER_ADD);			
		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "offer_fab_add": 92A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "offer_fab_add":
		check_authority('062',"add");
		if (!$PHP_id)	
		{
			$sr_key = substr($THIS_YEAR,2,2).substr($TODAY,5,2).substr($TODAY,8,2).'-';
			$num = $offer->get_new_number($sr_key); 
			$f1 = $offer->add_offer($num);
			$PHP_id = $f1;
			$PHP_num=$num;
		}
		$PHP_usage = trim($PHP_usage);
		$PHP_item = trim($PHP_item);
		$where_str = "WHERE id = ".$PHP_id;
		$item = $offer->get_one_field($PHP_group."_item",$where_str);
		$usage = $offer->get_one_field($PHP_group."_usage",$where_str);
		$price = $offer->get_one_field($PHP_group."_price",$where_str);
		$yy = $offer->get_one_field($PHP_group."_yy",$where_str);
		$num = $offer->get_one_field("num",$where_str);
		$ary_item = explode("|",$item);
		$ary_usage = explode("|",$usage);
		$sm_mk = 0;
		for($i=0; $i<sizeof($ary_item); $i++)
		{
			if(!isset($ary_usage[$i]))$ary_usage[$i]='';
			if($ary_usage[$i] == $PHP_usage && $ary_item[$i] == $PHP_item) $sm_mk = 1;
			
		}
		if($sm_mk == 0)
		{
			if(!$item)
			{
				$parm = array("item"	=>	$PHP_item,
											"usage"	=>	$PHP_usage,
											"price"	=>	$PHP_price,
											"yy"		=>	$PHP_yy,										
											"id"		=>	$PHP_id,
											);
			}else{
				$parm = array("item"	=>	$item."|".$PHP_item,
											"usage"	=>	$usage."|".$PHP_usage,
											"price"	=>	$price."|".$PHP_price,
											"yy"		=>	$yy."|".$PHP_yy,
											"id"		=>	$PHP_id,
											);
			}
		$f1 = $offer-> update_det($parm,$PHP_group);
		$message = "Add Material [ ".$PHP_item." ] detial";
		$s_total =$PHP_price * $PHP_yy;  //物料單筆小計
		//echo $s_total;
		$f_total = $PHP_cost + $s_total;  //金額總計
		$l_total = $f_total / $PHP_gm; //金額總計(包括GM)
		if ($PHP_group =='fab') $group_name ="Fabric";
		if ($PHP_group =='acc') $group_name ="Trimming";
		if ($PHP_group =='other') $group_name ="Other/Treatment";
		if ($PHP_group =='inter') $group_name ="InterLining";
		if ($PHP_group =='fus') $group_name ="Fusible";
		if (!$PHP_usage) $PHP_usage='&nbsp;';
		echo $message."|".$PHP_num."|".$PHP_id."|".$group_name."|".$PHP_item."|".$PHP_usage."|";
		if($PHP_usage=='&nbsp;')$PHP_usage='';
		echo "<input type='text' name=PHP_item_price_".$PHP_ii." value = ".$PHP_price." size=3 class=select>|";
		echo "<input type='text' name=PHP_item_yy_".$PHP_ii." value = ".$PHP_yy." size=3 class=select>|";
		
		echo "<input type='text' name=PHP_item_tal_".$PHP_ii." value = ".number_format($s_total,2,'.','')." size=3 readonly>|";
		echo "<input type='button' value='update'  onclick=\"edit_det('$PHP_id','$PHP_group','$PHP_item','$PHP_usage','$PHP_ii')\"> ";	
		echo "<input type='image' src='images/del.png'  onclick=\"del_det('$PHP_id','$PHP_group','$PHP_item','$PHP_usage',this)\">|";	

		echo number_format($f_total,2,'.','')."|".number_format($l_total,2,'.','');
	}else{
		$message = "Item and usage is already exist on $group_name, please check again";
		echo $message."|".$PHP_num."|".$PHP_id."|error";
	}
exit;
	break;
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "offer_mat_del": 92A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "offer_mat_del":
		check_authority('062',"add");
		$where_str = "WHERE id = ".$PHP_id;
		$item = $offer->get_one_field($PHP_group."_item",$where_str);
		$usage = $offer->get_one_field($PHP_group."_usage",$where_str);
		$price = $offer->get_one_field($PHP_group."_price",$where_str);
		$yy = $offer->get_one_field($PHP_group."_yy",$where_str);
		$num = $offer->get_one_field("num",$where_str);
		$ary_item = explode('|',$item);
		$ary_usage = explode('|',$usage);
		$ary_price = explode('|',$price);
		$ary_yy = explode('|',$yy);
		$del_price=$del_yy=0;
		$new_item=$new_usage=$new_price=$new_yy='';
		for ($i=0; $i<sizeof($ary_item); $i++)
		{
			if(!isset($ary_usage[$i]))$ary_usage[$i]='';
			if($ary_usage[$i] == $PHP_usage && $ary_item[$i] == $PHP_item)
			{
				$del_price=$ary_price[$i];
				$del_yy = $ary_yy[$i];
			}else{
				$new_item.=$ary_item[$i]."|";
				$new_usage.=$ary_usage[$i]."|";
				$new_price.=$ary_price[$i]."|";
				$new_yy.=$ary_yy[$i]."|";
			}
		}
		$new_item = substr($new_item,0,-1);
		$new_usage = substr($new_usage,0,-1);
		$new_price = substr($new_price,0,-1);
		$new_yy = substr($new_yy,0,-1);
		$parm = array("item"	=>	$new_item,
									"usage"	=>	$new_usage,
									"price"	=>	$new_price,
									"yy"		=>	$new_yy,										
									"id"		=>	$PHP_id,
									);

		$f1 = $offer-> update_det($parm,$PHP_group);
		$message = "Delete Material [ ".$PHP_item." ]";
		$s_total =$del_price * $del_yy;  //物料單筆小計
		//echo $s_total;
		$f_total = $PHP_cost - $s_total;  //金額總計
		$l_total = $f_total / $PHP_gm; //金額總計(包括GM)

		echo $message."|";
		echo number_format($f_total,2,'.','')."|".number_format($l_total,2,'.','');
exit;
	break;	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "offer_item_edit": 92A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "offer_item_edit":
		check_authority('062',"add");
		$where_str = "WHERE id = ".$PHP_id;
		$item = $offer->get_one_field($PHP_group."_item",$where_str);
		$usage = $offer->get_one_field($PHP_group."_usage",$where_str);
		$price = $offer->get_one_field($PHP_group."_price",$where_str);
		$yy = $offer->get_one_field($PHP_group."_yy",$where_str);
		$num = $offer->get_one_field("num",$where_str);
		$ary_item = explode('|',$item);
		$ary_usage = explode('|',$usage);
		$ary_price = explode('|',$price);
		$ary_yy = explode('|',$yy);
		$del_price=$del_yy=0;
		$new_item=$new_usage=$new_price=$new_yy='';
		for ($i=0; $i<sizeof($ary_item); $i++)
		{
			if(!isset($ary_usage[$i]))$ary_usage[$i]='';
			if($ary_usage[$i] == $PHP_usage && $ary_item[$i] == $PHP_item)
			{
				$old_price=$ary_price[$i];
				$old_yy = $ary_yy[$i];
				$new_item.=$ary_item[$i]."|";
				$new_usage.=$ary_usage[$i]."|";
				$new_price.=$PHP_price."|";
				$new_yy.=$PHP_qty."|";
				
			}else{
				$new_item.=$ary_item[$i]."|";
				$new_usage.=$ary_usage[$i]."|";
				$new_price.=$ary_price[$i]."|";
				$new_yy.=$ary_yy[$i]."|";
			}
		}
		$new_item = substr($new_item,0,-1);
		$new_usage = substr($new_usage,0,-1);
		$new_price = substr($new_price,0,-1);
		$new_yy = substr($new_yy,0,-1);
		$parm = array("item"	=>	$new_item,
									"usage"	=>	$new_usage,
									"price"	=>	$new_price,
									"yy"		=>	$new_yy,										
									"id"		=>	$PHP_id,
									);

		$f1 = $offer-> update_det($parm,$PHP_group);
		$message = "Update Material [ ".$PHP_item." ] [".$PHP_usage."]";
		$old_total =$old_price * $old_yy;  //物料單筆小計
		$new_total = $PHP_price * $PHP_qty;
		$def_total = $new_total - $old_total;
		//echo $s_total;
		$f_total = $PHP_cost + $def_total;  //金額總計
		$l_total = $f_total / $PHP_gm; //金額總計(包括GM)

		echo $message."|";
		echo number_format($f_total,2,'.','')."|".number_format($l_total,2,'.','');
exit;
	break;		
	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "do_add_offer": 92A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_add_offer":

		check_authority('062',"add");
			$PHP_cons = $PHP_cons1."|".$PHP_cons2."|".$PHP_cons3;
			$parm = array("id"	=>	$PHP_id,
					"num"						=>	$PHP_num,
					"merchan"				=>	$PHP_merchan,
					"dept"					=>	$PHP_dept_code,
					"buyer"					=>	$PHP_cust,
					"agent"					=>	$PHP_agent,
					"style"					=>	$PHP_style,
					"des"						=>	$PHP_des,
					"fab"						=>	$PHP_fab,
					"cons"					=>	$PHP_cons,
					"spt"						=>	$PHP_spt,

					"tech"					=>	$PHP_tech,
					
					"cm"						=>	$PHP_cm,
					"tt_cost"				=>	$PHP_cost,
					"gm_rate"				=>	$PHP_gm,

					"fty"						=>	$PHP_fty,
					"comm"					=>	$PHP_comm,
					"quota"					=>	$PHP_quota,
					"pic"						=>	$PHP_pic,
					"pic_upload"		=>	$PHP_pic_upload,
					"deal"					=>	$PHP_deal,
					"style_type"		=>	$PHP_style_type,
					"smpl_ord"			=>	$PHP_smpl_ord,
				);
		// check 輸入資料之正確	------------------------	
//		$op['of'] = $parm;  // 先將參數轉給 $op ----


		// 其它的處理 ---  將nl轉成br
			$parm['des'] = nl2br($parm['des']);
			$parm['fab'] = nl2br($parm['fab']);
			$parm['cons'] = nl2br($parm['cons']);
			$parm['tech'] = nl2br($parm['tech']);

		//------------ add to DB   --------------------------
		if (!$f = $offer->edit($parm)) {  
			$op['msg'] = $offer->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
		  break;
		}  // end if (!$F1)--------- 成功輸入 ---------------


		
		// 取出該筆 record 資料 -----------------------------
		if (!$op['of']=$offer->get($PHP_id)) {
			$op['msg']= $offer->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$message = " Add OFFER record: [".$op['of']['num']."]";
		$op['msg'][] = $message;
			# 記錄使用者動態
		$log->log_add(0,"92A",$message);


		$op['of_log'] = $offer->get_log($PHP_id);  //取出該筆記錄
		// 檢查 相片是否存在
		if(file_exists($GLOBALS['config']['root_dir']."/offer/".$op['of']['num'].".jpg")){
			$op['picture'] = "./offer/".$op['of']['num'].".jpg";
		} else {
			$op['picture'] = "./images/graydot.gif";
		}

		// HTML 文件參數
		$op['of'] = $offer->create_items($op['of']);

		$back_str = "offer.php?PHP_action=offer_search&PHP_dept_code=&PHP_num=&PHP_cust=&PHP_style=&SCH_str=&SCH_end=&SCH_agt=&SCH_style_type";
		$op['back_str'] = $back_str;
		$op['of_sub'] = $arry2->select($OFFER_LOG,'','PHP_subject','select','');

		page_display($op,'062', $TPL_OFFER_SHOW);			
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "offer_comment":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "offer_comment":

		check_authority('062',"add");		
		$PHP_c_date=$PHP_c_user=$PHP_s_date=$PHP_s_user='';
		if ($PHP_com)
		{
			$PHP_c_date = $TODAY;
			$PHP_c_user = $GLOBALS['SCACHE']['ADMIN']['login_id'];
			$PHP_com = nl2br($PHP_com); 
		}
		if ($PHP_status)
		{
			$PHP_s_date = $TODAY;
			$PHP_s_user = $GLOBALS['SCACHE']['ADMIN']['login_id'];
			$PHP_status = nl2br($PHP_status);
		}

		$parm = array(	
						"of_num"			=>	$PHP_of_num,
						"subject"			=>	$PHP_subject,
						"comment"			=>	$PHP_com,
						"status"			=>	$PHP_status,
						"c_date"			=>	$PHP_c_date,
						"c_user"			=>	$PHP_c_user,
						"s_date"			=>	$PHP_s_date,
						"s_user"			=>	$PHP_s_user,
		);
		$message = '';
		if ($PHP_subject)
		{
			//------------ add to DB   --------------------------
			if (!$f = $offer->add_log($parm)) {  
				$op['msg'] = $offer->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
			  break;
			}  // end if (!$F1)--------- 成功輸入 ---------------
		}else{
			$message = "Please select Subject First";
		}
	//	redirect_page($redir_str);
		
		$redir_str = "offer.php?PHP_action=offer_view&PHP_id=".$PHP_of_id."&cgiget=".$PHP_back_str."&PHP_msg=".$message."&SCH_style_type=".$SCH_style_type;
		redirect_page($redir_str);
//		page_display($op,'062', $TPL_OFFER_SHOW);			
		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "edit_offer_comment":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "edit_offer_comment":
		check_authority('062',"add");

		if ($PHP_com && !isset($PHP_c_date))
		{
			$PHP_c_date = $TODAY;
			$PHP_c_user = $GLOBALS['SCACHE']['ADMIN']['login_id'];
			$PHP_com = nl2br($PHP_com); 
			$parm = array(	
						"of_num"			=>	$PHP_of_num,
						"comment"			=>	$PHP_com,
						"c_date"			=>	$PHP_c_date,
						"c_user"			=>	$PHP_c_user,
						"id"					=>	$PHP_log_id,
			);
			$f = $offer->edit_log($parm,1);
		}
		if ($PHP_status && !isset($PHP_s_date))
		{
			$PHP_s_date = $TODAY; 
			$PHP_s_user = $GLOBALS['SCACHE']['ADMIN']['login_id'];
			$PHP_status = nl2br($PHP_status);
			$parm = array(	
						"of_num"			=>	$PHP_of_num,
						"status"			=>	$PHP_status,
						"s_date"			=>	$PHP_s_date,
						"s_user"			=>	$PHP_s_user,
						"id"					=>	$PHP_log_id,
			);
			$f = $offer->edit_log($parm,2);

		}
		
		$redir_str = "offer.php?PHP_action=offer_view&PHP_id=".$PHP_of_id."&cgiget=".$PHP_back_str;
		redirect_page($redir_str);
//		page_display($op,'062', $TPL_OFFER_SHOW);			
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "offer_search":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "offer_search":

		check_authority('062',"view");
		if(!isset($PHP_sourcing) || !$PHP_sourcing )$PHP_sourcing = 'id  DESC ';
		$parm = array(	"dept"		=>  $PHP_dept_code,
						"num"					=>  $PHP_num,
						"buyer"				=>	$PHP_cust,
						"style"				=>	$PHP_style,
						"agt"					=> 	$SCH_agt,
						"str"					=>	$SCH_str,
						"end"					=>	$SCH_end,
						'source'			=>	$PHP_sourcing,
						'style_type'	=>	$SCH_style_type
				);

		// ---- 利用PHP_dept_code判定是否 業務部門進入
		if (!$op = $offer->search(1)) {  
			$op['msg']= $offer->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		// ---- 如果 不是 呈承辦業務部門進入時...
		if (!$PHP_dept_code) {   
			$op['manager_flag'] = 1;
		}

		$op['dept_id'] = $PHP_dept_code;

		$op['msg']= $offer->msg->get(2);
		$op['cgi']= $parm;

//限制成本分析檢視 2008.10.01	
		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];   // 判定進入身份的指標(部門)
		$user_team = $GLOBALS['SCACHE']['ADMIN']['team_id'];   // 判定進入身份的指標(部門)

		$op['user_dept'] = $user_dept;
		if($user_dept == 'SA') $op['super_flag'] = 1;
		if(($user_dept == 'KA' || $user_dept == 'KB' || $user_dept == 'GM') && $user_team == 'SU') $op['super_flag'] = 1;
		if($user_team == 'TU') $op['non_cost'] = 1;

		page_display($op,'062', $TPL_OFFER_LIST);			
		break;
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		job 92V offer view
#		case "offer_view":		job 92V offer view
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "offer_view":

		check_authority('062',"view");

	if (isset($PHP_ver))
	{
		$op['of'] = $offer->get(0,$PHP_of_num,$PHP_ver);  //取出該筆記錄
		$op['none'] = 1;		
		$parm = $op['of'];
	}else{
		$op['of'] = $offer->get($PHP_id);  //取出該筆記錄
		if (!$op['of']) {
			$op['msg'] = $offer->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		
		$parm = $op['of'];
		
		// 加入返回前頁 --------------
				
		$back_str = trim($cgiget)."&PHP_sr_startno=".$PHP_sr_startno."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_style=".$PHP_style."&SCH_agt=".$SCH_agt."&SCH_str=".$SCH_str."&SCH_end=".$SCH_end."&PHP_sourcing=".$PHP_sourcing."&SCH_style_type=".$SCH_style_type;

		$back_str2 = "PHP_action=offer_view&PHP_id=".$PHP_id."&PHP_num=".$parm['num'];

		$op['back_str2'] = $back_str2;
		$op['back_str'] = $back_str;		
	}
	$op['of_log'] = $offer->get_log($op['of']['num']);  //取出該筆記錄

	$op['search'] = '1';
		// 檢查 相片是否存在
		if(file_exists($GLOBALS['config']['root_dir']."/offer/".$op['of']['num'].".jpg")){
			$op['picture'] = "./offer/".$op['of']['num'].".jpg";
		} else {
			$op['picture'] = "./images/graydot.gif";
		}
		$total=0;
		$op['of'] = $offer->create_items($op['of']);

		for ($i=0; $i < ($op['of']['ver']-1); $i++) 
		{
			$op['of']['ver_ary'][$i] = ($i+1);
		}
		$op['msg'] = $offer->msg->get(2);
		if(isset($PHP_msg) && $PHP_msg) $op['msg'][]=$PHP_msg;
		$op['of_sub'] = $arry2->select($OFFER_LOG,'','PHP_subject','select','');
		
		$user_team = $GLOBALS['SCACHE']['ADMIN']['team_id'];   // 判定進入身份的指標(部門)
		if($user_team == 'TU') $op['non_cost'] = 1;

		page_display($op,'062', $TPL_OFFER_SHOW);			
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "offer_submit":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "offer_submit":

		check_authority('062',"edit");
		
		$f1= $offer->update_field($PHP_id, 'status', 1);
		
		$op['of'] = $offer->get($PHP_id);  //取出該筆記錄
		if (!$op['of']) {
			$op['msg'] = $offer->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$op['of_log'] = $offer->get_log($op['of']['num']);  //取出該筆記錄		
		$parm = $op['of'];
		
		// 加入返回前頁 --------------
		$op['back_str'] = $PHP_back_str;
	$op['search'] = '1';
		// 檢查 相片是否存在
		if(file_exists($GLOBALS['config']['root_dir']."/offer/".$op['of']['num'].".jpg")){
			$op['picture'] = "./offer/".$op['of']['num'].".jpg";
		} else {
			$op['picture'] = "./images/graydot.gif";
		}
		$total=0;
		$op['of'] = $offer->create_items($op['of']);

//		$op['of']['tt_cost'] = get_currency($op['of']['tt_cost']);
		for ($i=0; $i < ($op['of']['ver']-1); $i++) 
		{
			$op['of']['ver_ary'][$i] = ($i+1);
		}
		$message= "Successfully submit offer :".$op['of']['num'];
		$op['msg'][] = $message;
		$log->log_add(0,"92A",$message);
		$op['of_sub'] = $arry2->select($OFFER_LOG,'','PHP_subject','select','');

		page_display($op,'062', $TPL_OFFER_SHOW);			
		break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "offer_edit":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "offer_edit":

		check_authority('062',"add");

		$my_dept = $GLOBALS['SCACHE']['ADMIN']['dept']; 

		if (!$parm = $offer->get($PHP_id)) {
			$op['msg'] = $offer->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		if ($parm['cons']) { $parm['cons'] = str_replace("<br />","",$parm['cons']);}
		if ($parm['tech']) { $parm['tech'] = str_replace("<br />","",$parm['tech']);}
		if ($parm['remark']) { $parm['remark'] = str_replace("<br />","",$parm['remark']);}
		if($my_dept != "RD"){  // 如果是研發部門人員進入時......
  // 如果是 SA 進入時......
			if ($parm['des']) { $parm['des'] = str_replace("<br />","",$parm['des']);}
			if ($parm['fab']) { $parm['fab'] = str_replace("<br />","",$parm['fab']);}
		}

		//  HTML 需要的參數 ---------------------------------------------
		$op['cust_id'] = $PHP_cust;
		$op['back_str'] = $PHP_back_str;
		$op['of'] = $parm;
		$op['my_dept'] = $my_dept;

		// 檢查 相片是否存在
		if(file_exists($GLOBALS['config']['root_dir']."/offer/".$op['of']['num'].".jpg")){
			$op['picture'] = "./offer/".$op['of']['num'].".jpg";
		} else {
			$op['picture'] = "./images/graydot.gif";
		}

		$total=0;
		$op['of'] = $offer->create_items($op['of']);
		$op['of']['fty'] = $arry2->select($FACTORY,$op['of']['fty'],'PHP_fty','select','');

		$where_str="ORDER BY cust_s_name"; //依cust_s_name排序
		$cust_def = $cust->get_fields('cust_init_name',$where_str);
		$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//取出客戶代號
		for ($i=0; $i< sizeof($cust_def); $i++)
		{
			$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//以 [客戶代號-客戶簡稱] 呈現
		}
		$op['agent_select'] =  $arry2->select($cust_value,$op['of']['agent'],'PHP_agent','select','',$cust_def_vue); 


//樣本單列表
			$s_date=increceDaysInDate($TODAY,-365); //12個月前
			$where="where last_update > '".$s_date."' and cust='".$op['of']['buyer']."'  order by num";
		
			$smpl_no=$smpl_ord->get_fields("num",$where);		
	    $j=0;
	    if ($smpl_no){
				$sml_no[0]=substr($smpl_no[0],0,9);
				for($i=1;$i< sizeof($smpl_no); $i++)
				{
					if($sml_no[$j] != substr($smpl_no[$i],0,9)){	
						$j++;
						$sml_no[$j]=substr($smpl_no[$i],0,9);
					}
				}
			}else{
				$sml_no=array('');
			}
			$op['of']['smpl_select']=$arry2->select($sml_no,$op['of']['smpl_ord'],'PHP_smpl_ord','select','');



		$style_def = $style_type->get_fields('style_type');   // 取出 款式類別
		$op['style_type'] =  $arry2->select($style_def,$op['of']['style_type'],'PHP_style_type','select','');


		page_display($op,'062', $TPL_OFFER_SA_EDIT);
	/*
		if($my_dept == "RD"){
			page_display($op,'062', $TPL_OFFER_RD_EDIT);			
		}else{
			page_display($op,'062', $TPL_OFFER_SA_EDIT);
		}
	*/
		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "do_offer_edit":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_offer_edit":

		check_authority('062',"add");
		$PHP_my_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
		$PHP_cons = $PHP_cons1."|".$PHP_cons2."|".$PHP_cons3;
		if($PHP_my_dept == 'RD'){
			$parm = array("id"			=>	$PHP_id,
									"num"				=>	$PHP_num,
									"my_dept"		=>	$PHP_my_dept,
									"spt"				=>	$PHP_spt,
									"cons"			=>	$PHP_cons,
									"tech"			=>	$PHP_tech,
									"remark"		=>	$PHP_remark,

									"back_str"	=>	$PHP_back_str,
					);

		}else{
			$parm = array(
					"id"						=>	$PHP_id,
					"my_dept"				=>	$PHP_my_dept,
					"num"						=>	$PHP_num,
					"merchan"				=>	$PHP_merchan,
					"dept"					=>	$PHP_dept_code,
					"buyer"					=>	$PHP_cust,
					"agent"					=>	$PHP_agent,
					"style"					=>	$PHP_style,
					"des"						=>	$PHP_des,
					"fab"						=>	$PHP_fab,
					"cons"					=>	$PHP_cons,
					"spt"						=>	$PHP_spt,

					"tech"					=>	$PHP_tech,
					
					"cm"						=>	$PHP_cm,
					"tt_cost"				=>	$PHP_cost,
					"gm_rate"				=>	$PHP_gm,

					"fty"						=>	$PHP_fty,
					"comm"					=>	$PHP_comm,
					"quota"					=>	$PHP_quota,
					
					"pic"						=>	$PHP_pic,
					"pic_upload"		=>	$PHP_pic_upload,
					"back_str"			=>	$PHP_back_str,
					"deal"					=>	$PHP_deal,
					"style_type"		=>	$PHP_style_type,
					"smpl_ord"			=>	$PHP_smpl_ord,
				);
		}

		// check 輸入資料之正確	------------------------	
		$op['of'] = $parm;  // 先將參數轉給 $op ----


		// 其它的處理 ---  將nl轉成br
		if($PHP_my_dept == 'RD'){
			$parm['cons'] = nl2br($parm['cons']);
			$parm['tech'] = nl2br($parm['tech']);
		}else{
			$parm['des'] = nl2br($parm['des']);
			$parm['fab'] = nl2br($parm['fab']);
			$parm['cons'] = nl2br($parm['cons']);
			$parm['tech'] = nl2br($parm['tech']);
		}

		//------------ add to DB   --------------------------
		if (!$f = $offer->edit($parm)) {  
			$op['msg'] = $offer->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
		  break;
		}  // end if (!$F1)--------- 成功輸入 ---------------

		$message = " update OFFER sheet:[".$PHP_num."]";
		$op['msg'][] = $message;

			# 記錄使用者動態
		$log->log_add(0,"92A",$message);
		
		// 取出該筆 record 資料 -----------------------------
		if (!$op['of']=$offer->get($f)) {
			$op['msg']= $offer->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$op['of_log'] = $offer->get_log($op['of']['num']);  //取出該筆記錄
		// 檢查 相片是否存在
		if(file_exists($GLOBALS['config']['root_dir']."/offer/".$op['of']['num'].".jpg")){
			$op['picture'] = "./offer/".$op['of']['num'].".jpg";
		} else {
			$op['picture'] = "./images/graydot.gif";
		}

		// HTML 文件參數
		$op['back_str'] = $parm['back_str'];

		$total=0;
		$op['of'] = $offer->create_items($op['of']);

		for ($i=0; $i < ($op['of']['ver']-1); $i++) 
		{
			$op['of']['ver_ary'][$i] = ($i+1);
		}
		$op['of_log'] = $offer->get_log($op['of']['num']);  //取出該筆記錄		
		$op['of_sub'] = $arry2->select($OFFER_LOG,'','PHP_subject','select','');
	
		page_display($op,'062', $TPL_OFFER_SHOW);			
		break;




#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "offer_revise":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "offer_revise":

		check_authority('062',"edit");

		$my_dept = $GLOBALS['SCACHE']['ADMIN']['dept']; 

		if (!$parm = $offer->get($PHP_id)) {
			$op['msg'] = $offer->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		if ($parm['cons']) { $parm['cons'] = str_replace("<br />","",$parm['cons']);}
		if ($parm['tech']) { $parm['tech'] = str_replace("<br />","",$parm['tech']);}
		if ($parm['remark']) { $parm['remark'] = str_replace("<br />","",$parm['remark']);}

		if($my_dept != "RD"){  // 如果是研發部門人員進入時......
  // 如果是 SA 進入時......
			if ($parm['des']) { $parm['des'] = str_replace("<br />","",$parm['des']);}
			if ($parm['fab']) { $parm['fab'] = str_replace("<br />","",$parm['fab']);}
		}

		//  HTML 需要的參數 ---------------------------------------------
		$op['cust_id'] = $parm['buyer'];
		$back_str='';
		if(isset($PHP_style))
			$back_str = trim($cgiget)."&PHP_sr_startno=".$PHP_sr_startno."&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_style=".$PHP_style."&PHP_sourcing=".$PHP_sourcing."&SCH_style_type=".$SCH_style_type;
		$op['back_str'] = $back_str;
		$op['of'] = $parm;
		$op['my_dept'] = $my_dept;

		// 檢查 相片是否存在
		if(file_exists($GLOBALS['config']['root_dir']."/offer/".$op['of']['num'].".jpg")){
			$op['picture'] = "./offer/".$op['of']['num'].".jpg";
		} else {
			$op['picture'] = "./images/graydot.gif";
		}

		$total=0;
		$op['of'] = $offer->create_items($op['of']);

		$op['of']['fty'] = $arry2->select($FACTORY,$op['of']['fty'],'PHP_fty','select','');

		$where_str="ORDER BY cust_s_name"; //依cust_s_name排序
		$cust_def = $cust->get_fields('cust_init_name',$where_str);
		$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//取出客戶代號
		for ($i=0; $i< sizeof($cust_def); $i++)
		{
			$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//以 [客戶代號-客戶簡稱] 呈現
		}
		$op['agent_select'] =  $arry2->select($cust_value,$op['of']['agent'],'PHP_agent','select','',$cust_def_vue); 

//樣本單列表
			$s_date=increceDaysInDate($TODAY,-365); //12個月前
			$where="where last_update > '".$s_date."' and cust='".$op['of']['buyer']."'  order by num";
		
			$smpl_no=$smpl_ord->get_fields("num",$where);		
	    $j=0;
	    if ($smpl_no){
				$sml_no[0]=substr($smpl_no[0],0,9);
				for($i=1;$i< sizeof($smpl_no); $i++)
				{
					if($sml_no[$j] != substr($smpl_no[$i],0,9)){	
						$j++;
						$sml_no[$j]=substr($smpl_no[$i],0,9);
					}
				}
			}else{
				$sml_no=array('');
			}
			$op['of']['smpl_select']=$arry2->select($sml_no,$op['of']['smpl_ord'],'PHP_smpl_ord','select','');


		$style_def = $style_type->get_fields('style_type');   // 取出 款式類別
		$op['style_type'] =  $arry2->select($style_def,$op['of']['style_type'],'PHP_style_type','select','');


		if($my_dept == "RD"){
			page_display($op,'062', $TPL_OFFER_RD_REV);			
		}else{
			page_display($op,'062', $TPL_OFFER_SA_REV);
		}

		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "do_offer_revise":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_offer_revise":

		check_authority('062',"edit");
		$PHP_my_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
		$PHP_cons = $PHP_cons1."|".$PHP_cons2."|".$PHP_cons3;

		if($PHP_my_dept == 'RD'){
			$parm = array("id"		=>	$PHP_id,
					"num"			=>	$PHP_num,
					"spt"			=>	$PHP_spt,
					"cons"			=>	$PHP_cons,
					"tech"			=>	$PHP_tech,
					"remark"		=>	$PHP_remark,
					"ver"			=>	($PHP_ver+1),
					"k_date"		=>	$TODAY,
					"merchan"		=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
					"back_str"		=>	$PHP_back_str,
					);

		}else{
			$parm = array("id"	=>	$PHP_id,
					"num"						=>	$PHP_num,
					"dept"					=>	$PHP_dept_code,
					"buyer"					=>	$PHP_cust,
					"agent"					=>	$PHP_agent,
					"style"					=>	$PHP_style,
					"des"						=>	$PHP_des,
					"fab"						=>	$PHP_fab,
					"cons"					=>	$PHP_cons,
					"spt"						=>	$PHP_spt,

					"tech"					=>	$PHP_tech,

					"cm"						=>	$PHP_cm,
					"tt_cost"				=>	$PHP_cost,
					"gm_rate"				=>	$PHP_gm,

					"fty"						=>	$PHP_fty,
					"comm"					=>	$PHP_comm,
					"quota"					=>	$PHP_quota,

					"pic"						=>	$PHP_pic,
					"pic_upload"		=>	$PHP_pic_upload,
					"ver"						=>	($PHP_ver+1),
					"k_date"				=>	$TODAY,
					"merchan"				=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
					"back_str"			=>	$PHP_back_str,
					"deal"					=>	$PHP_deal,
					"style_type"		=>	$PHP_style_type,
					"smpl_ord"			=>	$PHP_smpl_ord,
				);
		}

		// check 輸入資料之正確	------------------------	
		$op['of'] = $parm;  // 先將參數轉給 $op ----


		// 其它的處理 ---  將nl轉成br
		if($PHP_my_dept == 'RD'){
			$parm['cons'] = nl2br($parm['cons']);
			$parm['tech'] = nl2br($parm['tech']);
		}else{
			$parm['des'] = nl2br($parm['des']);
			$parm['fab'] = nl2br($parm['fab']);
			$parm['cons'] = nl2br($parm['cons']);
			$parm['tech'] = nl2br($parm['tech']);
		}

		//------------ add to DB   --------------------------
		if (!$f = $offer->revise($parm)) {  
			$op['msg'] = $offer->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
		  break;
		}  // end if (!$F1)--------- 成功輸入 ---------------

		$message = " REVISE OFFER sheet:[".$PHP_num."]";
		$op['msg'][] = $message;

			# 記錄使用者動態
		$log->log_add(0,"92A",$message);
		
		// 取出該筆 record 資料 -----------------------------
		if (!$op['of']=$offer->get($f)) {
			$op['msg']= $offer->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

//增加版本remark
		$PHP_status = $PHP_com = "RENEW New Version :[ ".$parm['ver']." ] On :[".$TODAY."]";
		$PHP_s_date = $PHP_c_date = $TODAY;
		$PHP_s_user = $PHP_c_user = $GLOBALS['SCACHE']['ADMIN']['login_id'];
		$parm = array(	
						"of_num"			=>	$op['of']['num'],
						"subject"			=>	'others',
						"comment"			=>	$PHP_com,
						"status"			=>	$PHP_status,
						"c_date"			=>	$PHP_c_date,
						"c_user"			=>	$PHP_c_user,
						"s_date"			=>	$PHP_s_date,
						"s_user"			=>	$PHP_s_user,
		);

		//------------ add to DB   --------------------------
		if (!$f = $offer->add_log($parm)) {  
			$op['msg'] = $offer->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
		  break;
		}  // end if (!$F1)--------- 成功輸入 ---------------




		$op['of_log'] = $offer->get_log($op['of']['num']);  //取出該筆記錄
		// 檢查 相片是否存在
		if(file_exists($GLOBALS['config']['root_dir']."/offer/".$op['of']['num'].".jpg")){
			$op['picture'] = "./offer/".$op['of']['num'].".jpg";
		} else {
			$op['picture'] = "./images/graydot.gif";
		}

		// HTML 文件參數
		$op['back_str'] = $PHP_back_str;

		$total=0;
		$op['of'] = $offer->create_items($op['of']);

		for ($i=0; $i < ($op['of']['ver']-1); $i++) 
		{
			$op['of']['ver_ary'][$i] = ($i+1);
		}
		$op['none']=1;
		$op['of_log'] = $offer->get_log($op['of']['num']);  //取出該筆記錄
		$op['of_sub'] = $arry2->select($OFFER_LOG,'','PHP_subject','select','');

		page_display($op,'062', $TPL_OFFER_SHOW);			
		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "offer_print_a":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "offer_print_a":

		check_authority('062',"view");

	if (isset($PHP_ver))
	{
		$of = $offer->get(0,$PHP_of_num,$PHP_ver);  //取出該筆記錄
	}else{
		$of = $offer->get($PHP_id);  //取出該筆記錄
	}

		// 檢查 相片是否存在
		if(file_exists($GLOBALS['config']['root_dir']."/offer/".$of['num'].".jpg")){
			$op['picture'] = "./offer/".$of['num'].".jpg";
		} else {
			$op['picture'] = "./images/graydot.jpg";
		}
		$of = $offer->create_item_pdf($of);

//---------------------------------- 資料庫作業結束 ------------------------------


include_once($config['root_dir']."/lib/class.pdf_offer_a.php");

$print_title = "OFFER";

$print_title2 = "VER.".$of['ver']."  on  ".$of['k_date'];
$PHP_id = $of['num'];
/*
$creat=$user->get(0,$of['merchan']);
	if ($creat['name'])$of['merchan'] = $creat['name'];
*/
$creator = $of['merchan'];

$pdf=new PDF_offer_a();
$pdf->SetAutoPageBreak("on");
$pdf->AliasNbPages(); 
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
// [表匡] 訂單基本資料
$pdf->hend_title($of);

		$Y = $pdf->getY();
		
		$img_size = GetImageSize($op['picture']);
		
		if ($img_size[0] > $img_size[1])
		{
			$pdf->Image($op['picture'],10,28,40,0);
		}else{
			$pdf->Image($op['picture'],10,28,0,40);
		}
		
		$pdf->ln();
		$Y = $pdf->getY();
		$X = $pdf->getX();
		$pdf->setXY(10,$Y+5);
		$pdf->descript('Sample ref. : ',$of['smpl_ord']);
		$pdf->descript('Style Description : ',$of['des']);
		$pdf->descript('Fabrication : ',$of['fab']);
		$pdf->descript('Fab. Consump. : ',$of['cons']);
		$pdf->ln();
		$pdf->Cell(185,5,"-------------------- unit cost --------------------",0,0,'C');
		$pdf->ln();
		$pdf->Table_title(); //Unit Cost的Title
		
		$pdf->Table_cost_a($of['fab_cat']); //主料
		$pdf->Table_cost_a($of['fus_cat']);	//fusible
		$pdf->Table_cost_a($of['inter_cat']); //interling
		$pdf->Table_cost_a($of['acc_cat']);		//accessory
		$pdf->Table_cost_a($of['oth_cat']);		//other

		//CM內容
		$pdf->Table_cost("C.M.", number_format($of['cm'],2,'.',''));
		$pdf->Table_cost("Quota", number_format($of['quota'],2,'.',''));


		//Mark-Up內容
		$pdf->Table_cost("Mark-Up", $of['gm_rate']."%");

		//Commission內容
		$pdf->Table_cost("Commission", $of['comm']."%");

		//Total Costing內容
		$pdf->Table_cost("Total Costing", number_format($of['tt_cost'],2,'.',''));
		$pdf->ln();
		//Offer Price內容
		$pdf->Table_cost("Offer Price", number_format($of['deal'],2,'.',''));

		$pdf->ln();
		//Technical / Equipment Advisements:
		$pdf->SetFont('Arial','',8);	
		$pdf->cell(50,5,"Technical / Equipment Advisements:",0,0,'R');
		$pdf->cell(135,5,$of['tech'],0,0,'L');		
		
		$name=$of['num'].'_a.pdf';
$pdf->Output($name,'D');
break;	


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "offer_print_b":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "offer_print_b":

		check_authority('062',"view");

	if (isset($PHP_ver))
	{
		$of = $offer->get(0,$PHP_of_num,$PHP_ver);  //取出該筆記錄
	}else{
		$of = $offer->get($PHP_id);  //取出該筆記錄
	}

		// 檢查 相片是否存在
		if(file_exists($GLOBALS['config']['root_dir']."/offer/".$of['num'].".jpg")){
			$op['picture'] = "./offer/".$of['num'].".jpg";
		} else {
			$op['picture'] = "./images/graydot.jpg";
		}
		$of = $offer->create_item_pdf($of);

//---------------------------------- 資料庫作業結束 ------------------------------


include_once($config['root_dir']."/lib/class.pdf_offer_a.php");

$print_title = "OFFER";

$print_title2 = "VER.".$of['ver']."  on  ".$of['k_date'];
$PHP_id = $of['num'];

$creator = $of['merchan'];

$pdf=new PDF_offer_a();
$pdf->SetAutoPageBreak("on");
$pdf->AliasNbPages(); 
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();

$pdf->SetFont('Arial','B',14);
// [表匡] 訂單基本資料
$pdf->hend_title($of);

		$Y = $pdf->getY();
		
		$img_size = GetImageSize($op['picture']);
		
		if ($img_size[0] > $img_size[1])
		{
			$pdf->Image($op['picture'],10,28,40,0);
		}else{
			$pdf->Image($op['picture'],10,28,0,40);
		}
		
		$pdf->ln();
		$Y = $pdf->getY();
		$X = $pdf->getX();
		$pdf->setXY(10,$Y+5);
		$pdf->descript('Style Description : ',$of['des']);
		$pdf->descript('Fabrication : ',$of['fab']);
		$pdf->descript('Fab. Consump. : ',$of['cons']);
		$pdf->ln();
		$pdf->Cell(185,5,"---------- unit cost ----------",0,0,'C');
		$pdf->ln();
		$pdf->Table_title(); //Unit Cost的Title
		
		$pdf->Table_cost_a($of['fab_cat']); //主料
		$pdf->Table_cost_a($of['fus_cat']);	//fusible
		$pdf->Table_cost_a($of['inter_cat']); //interling
		$pdf->Table_cost_a($of['acc_cat']);		//accessory
		$pdf->Table_cost_a($of['oth_cat']);		//other

		//CM-TP內容 (CM與mark-up合併)
		$pdf->Table_cost("CMTP", number_format($of['cmtp'],2,'.',''));

		//Total Costing內容
		$pdf->Table_cost("Total Costing", number_format($of['tt_cost'],2,'.',''));
		$pdf->ln();
		//Offer price內容
		$pdf->Table_cost("Offer Price", number_format($of['deal'],2,'.',''));


		$pdf->ln();
		//Technical / Equipment Advisements:
		$pdf->SetFont('Arial','',8);	
		$pdf->cell(50,5,"Technical / Equipment Advisements:",0,0,'R');
		$pdf->cell(135,5,$of['tech'],0,0,'L');		
		
		$name=$of['num'].'_b.pdf';
$pdf->Output($name,'D');
break;	


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "offer_print_c":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "offer_print_c":

		check_authority('062',"view");

	if (isset($PHP_ver))
	{
		$of = $offer->get(0,$PHP_of_num,$PHP_ver);  //取出該筆記錄
	}else{
		$of = $offer->get($PHP_id);  //取出該筆記錄
	}

		// 檢查 相片是否存在
		if(file_exists($GLOBALS['config']['root_dir']."/offer/".$of['num'].".jpg")){
			$op['picture'] = "./offer/".$of['num'].".jpg";
		} else {
			$op['picture'] = "./images/graydot.jpg";
		}
		$of = $offer->create_item_pdf($of);

//---------------------------------- 資料庫作業結束 ------------------------------


include_once($config['root_dir']."/lib/class.pdf_offer_a.php");

$print_title = "OFFER";

$print_title2 = "VER.".$of['ver']."  on  ".$of['k_date'];
$PHP_id = $of['num'];
//$creat=$user->get(0,$of['merchan']);
//	if ($creat['name'])$of['merchan'] = $creat['name'];

$creator = $of['merchan'];

$pdf=new PDF_offer_a();
$pdf->AliasNbPages(); 
$pdf->AddBig5Font();
$pdf->Open();
$pdf->AddPage();
$pdf->SetAutoPageBreak("on");
$pdf->SetFont('Arial','B',14);
// [表匡] 訂單基本資料
$pdf->hend_title($of);

		$Y = $pdf->getY();
		
		$img_size = GetImageSize($op['picture']);
		
		if ($img_size[0] > $img_size[1])
		{
			$pdf->Image($op['picture'],10,28,40,0);
		}else{
			$pdf->Image($op['picture'],10,28,0,40);
		}
		
		$pdf->ln();
		$Y = $pdf->getY();
		$X = $pdf->getX();
		$pdf->setXY(10,$Y+5);
		$pdf->descript('Style Description : ',$of['des']);
		$pdf->descript('Fabrication : ',$of['fab']);
		$pdf->descript('Fab. Consump. : ',$of['cons']);
		$pdf->ln();
		$pdf->Cell(185,5,"---------- unit cost ----------",0,0,'C');
		$pdf->ln();

		//Total Costing內容
//		$pdf->Table_cost("Total Costing", number_format($of['tt_cost'],2,'.',''));

		//Total Costing內容
		$pdf->Table_cost("Offer Price", number_format($of['deal'],2,'.',''));


		$pdf->ln();
		//Technical / Equipment Advisements:
		$pdf->SetFont('Arial','',8);	
		$pdf->cell(50,5,"Technical / Equipment Advisements:",0,0,'R');
		$pdf->cell(135,5,$of['tech'],0,0,'L');		
		
		$name=$of['num'].'_c.pdf';
$pdf->Output($name,'D');
break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		job 92V offer view
#		case "offer_view":		job 92V offer view
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "offer_to_order":

		check_authority('065',"add");

		$of = $offer->get($PHP_id);  //取出該筆記錄
        // print_r($of);
		// 檢查 相片是否存在
		$op['order']['pic'] = "./images/graydot.jpg";
		$of = $offer->create_items($of);
		
		//訂單資料整理

		// 取出年碼...
		$dt = decode_date(1);
		$year_code = substr($dt['year'],-2);
		
//*****//2006.12.08	修改sample為下拉式 start
		$s_date=increceDaysInDate($TODAY,-365); //12個月前
		$where="where last_update > '".$s_date."' and cust='".$of['buyer']."' and dept='".$of['dept']."'  order by num";
		
		$smpl_no=$smpl_ord->get_fields("num",$where);		
	    $j=0;
	    if ($smpl_no){
		$sml_no[0]=substr($smpl_no[0],0,9);
		for($i=1;$i< sizeof($smpl_no); $i++)
		{
			if($sml_no[$j] != substr($smpl_no[$i],0,9)){	
				$j++;
				$sml_no[$j]=substr($smpl_no[$i],0,9);
			}
		}
		}else{
			$sml_no=array('');
		}
		$op['smpl_no']=$arry2->select($sml_no,'','PHP_smpl_ord','select','');
//*****//2006.12.08	修改sample為下拉式	end

		$where_str="ORDER BY cust_s_name"; //依cust_s_name排序
		$cust_def = $cust->get_fields('cust_init_name',$where_str);
		$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//取出客戶代號
		for ($i=0; $i< sizeof($cust_def); $i++)
		{
			$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//以 [客戶代號-客戶簡稱] 呈現
		}
		$op['agent_select'] =  $arry2->select($cust_value,$of['agent'],'PHP_agent','select','',$cust_def_vue); 

		// creat style type combo box
		$style_def = $style_type->get_fields('style_type');   // 取出 款式類別
//		$op['style'] =  $arry2->select($style_def,'','PHP_style','select','');  	
		// creat apparel unit combo box
		$op['unit'] = $arry2->select($APPAREL_UNIT,'pc','PHP_unit','select','');  	

		// creat factory combo box
/*
		if (substr($of['dept'],0,1)=='H'){//判斷進入者的部門是否代表工廠			
			$op['factory'] = "HJ";
			$op['fac_flag']=1;	
		}elseif (substr($of['dept'],0,1)=='L'){		
			$op['factory'] = "LY";
			$op['fac_flag']=1;			
		}else{
			$op['factory'] = $arry2->select($FACTORY,$of['fty'],'PHP_factory','select',''); 	
		}  
*/

		if ($of['dept']=="HJ" || $of['dept']=="LY")
		{
			if($user_dept=="HJ") $fty = array($user_dept,'MA');
			if($user_dept=="LY") $fty = array($user_dept);
			$op['factory'] = $arry2->select($fty,$of['fty'],'PHP_factory','select','');
		}else{
			$op['factory'] = $arry2->select($FACTORY,$of['fty'],'PHP_factory','select','');  	
		}

		// pre  訂單編號....
		$op['order_precode'] = substr($of['dept'],0,1).$of['buyer'].$year_code."-xxx";
		if ($of['dept']=="HJ" || $of['dept']=="LY")		$op['order_precode'] = substr($of['dept'],0,1).$of['buyer'].$year_code."-xxxx";
		// 2005/07/30 加入 由 menu進入新增後的 back page 只向 全部列表
		$op['back_str'] = "/yankee/index2.php?PHP_action=order_search&PHP_sr_startno=0&PHP_dept_code=&PHP_order_num=&PHP_cust=&PHP_ref=&PHP_factory=";

		$op['cust_id'] = $of['buyer'];
		$op['dept_id'] = $of['dept'];
		$op['order']['ref'] = $of['style'];
		$op['order']['uprice'] = $of['tt_cost'];
		$op['order']['mat_useage'] =0;
		$op['order']['mat_u_cost'] =0;
		$op['order']['interline'] =0;
		$op['order']['fusible'] =0;
		$op['order']['acc_u_cost'] =0;
		for ($i=0; $i<sizeof($of['fab_cat']); $i++)
		{
			$op['order']['mat_useage'] += $of['fab_cat'][$i]['fq'];
			$op['order']['mat_u_cost'] += $of['fab_cat'][$i]['fc'];
		}
		$op['order']['mat_u_cost'] = $op['order']['mat_u_cost'] / $op['order']['mat_useage'];
		for ($i=0; $i<sizeof($of['inter_cat']); $i++)
		{
			$op['order']['interline'] += $of['inter_cat'][$i]['ic'];
		}
		for ($i=0; $i<sizeof($of['fus_cat']); $i++)
		{
			$op['order']['fusible'] += $of['fus_cat'][$i]['fsc'];
		}
		for ($i=0; $i<sizeof($of['acc_cat']); $i++)
		{
			$op['order']['acc_u_cost'] += $of['acc_cat'][$i]['ac'];
		}		
		$op['order']['quota_fee']=$of['quota'];
		$op['order']['comm_fee']= $of['tt_cost'] * ($of['comm']/100);
		$op['order']['cm']= $of['cm'];
		$op['order']['oth'] = 0;
		$op['order']['oth_treat'] ='';
		for ($i=0; $i<sizeof($of['oth_cat']); $i++)
		{
			if ($of['oth_cat'][$i]['oi'] == 'embroidery' )
			{
				$op['order']['emb'] = $of['oth_cat'][$i]['oc'];
			}else if($of['oth_cat'][$i]['oi'] == 'garment wash'){
				$op['order']['wash'] = $of['oth_cat'][$i]['oc'];
			}else{
				$op['order']['oth'] += $of['oth_cat'][$i]['oc'];
				$op['order']['oth_treat'].=$of['oth_cat'][$i]['oi'].",";				
			}
		}	
		$op['order']['oth_treat'] =substr($op['order']['oth_treat'],0,-1);
		$op['offer'] = $of['num'];

//生產線分別男女裝 -- 08.08.27
		$line_value = array('F','M');
		$line_key = array('Woman','Man');
		$op['line_select'] =  $arry2->select($line_key,'','PHP_line','select','',$line_value); 
//主料單位--08.09.22
		$op['lots_unit'] =  $arry2->select($LOTS_PRICE_UNIT,'','PHP_lots_unit','select',''); 


		page_display($op,'065', $TPL_ORDER_ADD);	
break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		job 92V offer view
#		case "offer_view":		job 92V offer view
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "offer_apv_view":

		check_authority('062',"view");

	if (isset($PHP_ver))
	{
		$op['of'] = $offer->get(0,$PHP_of_num,$PHP_ver);  //取出該筆記錄
		$op['none'] = 1;		
		$parm = $op['of'];
	}else{
		$op['of'] = $offer->get($PHP_id);  //取出該筆記錄
		if (!$op['of']) {
			$op['msg'] = $offer->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		
		$parm = $op['of'];
		
		// 加入返回前頁 --------------
				
		$op['back_str'] = "index2.php?PHP_action=order_apv&PHP_sr_of=".$PHP_sr_of;
		$op['sr_of'] = $PHP_sr_of;
		$op['max_no'] = $PHP_max_no;
		
	}
	$op['of_log'] = $offer->get_log($op['of']['num']);  //取出該筆記錄

	$op['search'] = '1';
		// 檢查 相片是否存在
		if(file_exists($GLOBALS['config']['root_dir']."/offer/".$op['of']['num'].".jpg")){
			$op['picture'] = "./offer/".$op['of']['num'].".jpg";
		} else {
			$op['picture'] = "./images/graydot.gif";
		}
		$total=0;
		$op['of'] = $offer->create_items($op['of']);

		for ($i=0; $i < ($op['of']['ver']-1); $i++) 
		{
			$op['of']['ver_ary'][$i] = ($i+1);
		}
		$op['msg'] = $offer->msg->get(2);
		if(isset($PHP_msg) && $PHP_msg) $op['msg'][]=$PHP_msg;
		$op['of_sub'] = $arry2->select($OFFER_LOG,'','PHP_subject','select','');
		page_display($op,'062', $TPL_OFFER_VIEW_APV);			
		break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		job 92V offer view
#		case "do_offer_apv":		job 92V offer view
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_offer_apv":

		check_authority('062',"add");

		$f1= $offer->update_field($PHP_id, 'status', 3);
		$message = "Success Apprvoal offer :[".$PHP_num."]";
		$log->log_add(0,"92AV",$message);
		if($PHP_max_no == 1)$PHP_sr_of=0;

		$PHP_back_str = "index2.php?PHP_action=order_apv&PHP_sr_of=".$PHP_sr_of;
		$redirect_str = $PHP_back_str ."&PHP_msg = Success Apprvoal offer :[".$PHP_num."]";

		redirect_page($PHP_back_str);
/*		
		$op['of'] = $offer->get($PHP_id);  //取出該筆記錄
		if (!$op['of']) {
			$op['msg'] = $offer->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		$op['of_log'] = $offer->get_log($op['of']['num']);  //取出該筆記錄		
		$parm = $op['of'];
		
		// 加入返回前頁 --------------
		$op['back_str'] = $PHP_back_str;
	$op['search'] = '1';
		// 檢查 相片是否存在
		if(file_exists($GLOBALS['config']['root_dir']."/offer/".$op['of']['num'].".jpg")){
			$op['picture'] = "./offer/".$op['of']['num'].".jpg";
		} else {
			$op['picture'] = "./images/graydot.gif";
		}
		$total=0;
		$op['of'] = $offer->create_items($op['of']);

//		$op['of']['tt_cost'] = get_currency($op['of']['tt_cost']);
		for ($i=0; $i < ($op['of']['ver']-1); $i++) 
		{
			$op['of']['ver_ary'][$i] = ($i+1);
		}
		$message= "Successfully submit offer :".$op['of']['num'];
		$op['msg'][] = $message;
		$log->log_add(0,"92A",$message);
		$op['of_sub'] = $arry2->select($OFFER_LOG,'','PHP_subject','select','');

		page_display($op,'062', $TPL_OFFER_VIEW_APV);			
*/
		break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		job 92V offer view
#		case "do_offer_apv":		job 92V offer view
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_offer_rjt":

		check_authority('062',"add");
		
		$f1= $offer->update_field($PHP_id, 'status', 5);
		$message = "Success Reject offer :[".$PHP_num."]";
		$log->log_add(0,"92ARJ",$message);
		if($PHP_max_no == 1)$PHP_sr_of=0;

//增加版本remark
		$PHP_status = $PHP_com = "Reject OFFER :[ ".$PHP_detail." ] ";
		$PHP_s_date = $PHP_c_date = $TODAY;
		$PHP_s_user = $PHP_c_user = $GLOBALS['SCACHE']['ADMIN']['login_id'];
		$parm = array(	
						"of_num"			=>	$PHP_num,
						"subject"			=>	'others',
						"comment"			=>	$PHP_com,
						"status"			=>	$PHP_status,
						"c_date"			=>	$PHP_c_date,
						"c_user"			=>	$PHP_c_user,
						"s_date"			=>	$PHP_s_date,
						"s_user"			=>	$PHP_s_user,
		);

		//------------ add to DB   --------------------------
		if (!$f = $offer->add_log($parm)) {  
			$op['msg'] = $offer->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
		  break;
		}  // end if (!$F1)--------- 成功輸入 ---------------

		$PHP_back_str = "index2.php?PHP_action=order_apv&PHP_sr_of=".$PHP_sr_of;
		$redirect_str = $PHP_back_str ."&PHP_msg = Success Reject offer :[".$PHP_num."]";


		redirect_page($PHP_back_str);


		exit;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		job 92V offer excel
#		case "offer_excel":		job 92V offer excel
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "offer_excel":
	check_authority('062',"view");

	$op['of'] = $offer->get($PHP_id);  //取出該筆記錄
	if (!$op['of']) {
		$op['msg'] = $offer->msg->get(2);
		$layout->assign($op);
		$layout->display($TPL_ERROR);
		break;
	}
	$op['of_log'] = $offer->get_log($op['of']['num']);  //取出該筆記錄

	$op['search'] = '1';


	# 檢查 相片是否存在 2008.10.29
	$op['main_pic'] = _pic_url('big','190',3,'offer',$op['of']['num'],'jpg','','');

	$total=0;
	$op['of'] = $offer->create_items($op['of']);

	for ($i=0; $i < ($op['of']['ver']-1); $i++)	{
		$op['of']['ver_ary'][$i] = ($i+1);
	}

	$op['msg'] = $offer->msg->get(2);
	if(isset($PHP_msg) && $PHP_msg) $op['msg'][]=$PHP_msg;
	$op['of_sub'] = $arry2->select($OFFER_LOG,'','PHP_subject','select','');

	require_once($config['root_dir']."/lib/spreadsheets/Worksheet.php");
	require_once($config['root_dir']."/lib/spreadsheets/Workbook.php");

  function HeaderingExcel($filename) {
	  header("Content-type: application/vnd.ms-excel");
	  header("Content-Disposition: attachment; filename=$filename" );
	  header("Expires: 0");
	  header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
	  header("Pragma: public");
	}

  // HTTP headers
  HeaderingExcel('offer_'.$op['of']['num'].'.xls');

  // Creating a workbook
  $workbook = new Workbook("-");

  // Creating the first worksheet
  $worksheet1 =& $workbook->add_worksheet('Offer');

	$now = $GLOBALS['THIS_TIME'];

	// 寫入 title

  // Format for the headings

	$f =& $workbook->add_format();
  $f->set_size(12);
  $f->set_bold(1);
  $f->set_color('white');
  $f->set_fg_color('black');
  $f->set_pattern();
  $f->set_align('left');

	$f_1 =& $workbook->add_format();
  $f_1->set_size(10);
  $f_1->set_bold(1);
  $f_1->set_color('white');
  $f_1->set_fg_color('black');
  $f_1->set_pattern();
  $f_1->set_align('right');

  $f1 =& $workbook->add_format();
  $f1->set_size(10);
	$f1->set_fg_color('yellow');
	$f1->set_color('black');
	$f1->set_bold(1);
	$f1->set_align('right');
	$f1->set_pattern(3);

  $f1_1 =& $workbook->add_format();
  $f1_1->set_size(10);
	$f1_1->set_color('navy');
	$f1_1->set_bold(1);
	$f1_1->set_align('top');
	$f1_1->set_align('right');

  $f2 =& $workbook->add_format();
  $f2->set_size(10);

  $f3 =& $workbook->add_format();
  $f3->set_size(10);
	$f3->set_color('navy');
	$f3->set_bold(1);
	$f3->set_text_wrap(1);
	$f3->set_align('vequal_space');
	$f3->set_align('merge');


  $f4 =& $workbook->add_format();
  $f4->set_size(10);
	$f4->set_color('black');
	$f4->set_bold(1);
	$f4->set_align('top');
	$f4->set_align('right');

  $f5 =& $workbook->add_format();
  $f5->set_size(10);
	$f5->set_color('black');
	$f5->set_align('right');

	$f6 =& $workbook->add_format();
  $f6->set_size(10);
  $f6->set_bg_color('black');
	$f6->set_align('top');
	$f6->set_align('left');
	$f6->set_text_wrap(0);

  $f7 =& $workbook->add_format();
  $f7->set_size(10);
  $f7->set_text_wrap(1);
  $f7->set_align('vequal_space');
	$f7->set_align('merge');

  $f8 =& $workbook->add_format();
	$f8->set_fg_color('yellow');
  $f8->set_size(10);
	$f8->set_color('black');
	$f8->set_align('right');
	$f8->set_pattern(3);

	# level stert
	$level = 0;
	$ary = array(25,25,15,8,3,8,3,8);
	$arr_count = count($ary);

  for ($i=0; $i < $arr_count; $i++){
  	$worksheet1->set_column($level,$i,$ary[$i],$f3);
  	$worksheet1->write($level,$i,'',$f_1);
  	if($i==1)
	  $worksheet1->write_string($level,$i,"STYLE OFFER working sheet",$f);
	  if($i==7 && !empty($op['of']['ver']))
	  $worksheet1->write_string($level,$i,"[ VERSION ] : ".$op['of']['ver'],$f_1);
	}



	$level++;
	$worksheet1->write_string($level,6,'currency :',$f1_1);
	$worksheet1->write_string($level,7,'$USD',$f6);
	$level++;
	$worksheet1->write_string($level,0,'DATE :',$f1_1);
	$worksheet1->write_string($level,1,$op['of']['k_date'],$f6);
	$worksheet1->write_string($level,2,'Merchandiser :',$f1_1);
	$worksheet1->write_string($level,3,$op['of']['merchan'],$f6);
	$worksheet1->write_string($level,6,'Buyer :',$f1_1);
	$worksheet1->write_string($level,7,$op['of']['buyer'],$f6);
	$level++;
	$worksheet1->write_string($level,0,'SAMPLE ref. : ',$f1_1);
	$worksheet1->write_string($level,1,$op['of']['smpl_ord'],$f6);

	$level++;
	$worksheet1->set_row($level, 1, $f3);
	for($i = 0 ; $i < $arr_count ; $i ++ )$worksheet1->write_string($level,$i,'',$f3);
	$level++;

  $ary_m = array();

  $ary_m[] = array("OFFER # :",$op['of']['num'],"Style :",$op['of']['style_type'],"Factory :",$op['of']['fty']);
  $ary_m[] = array("Agent :",$op['of']['agent']," SPT(sec.) :",$op['of']['spt'].' sec.',"IE :",$op['of']['ie']);
  $ary_m[] = array("Style # :",$op['of']['style']);
  $ary_m[] = array("Style Description :",$op['of']['des']);
  $ary_m[] = array("Fabrication :",$op['of']['fab']);
  $ary_m[] = array("Fab. Consump. :",$op['of']['cons']);

	$level++;

	$a_html = '';
	$ac = count($ary_m);
	for($i = 0 ; $i < $ac ; $i ++ ){
		$ac2 = count($ary_m[$i]);
	  for ($y=0; $y < $ac2 ; $y++){
	  	if($y==0)
	  	$worksheet1->write_string($level,$y,$ary_m[$i][$y],$f1_1);
			if($i==3)
			$worksheet1->merge_cells($level, 1, $level, 7);
	  	if($y==1 && $i==3)
	  	$worksheet1->write_string($level,$y,$ary_m[$i][$y],$f7);
	  	else if ($y==1 && $i<>3)
	  	$worksheet1->write_string($level,$y,$ary_m[$i][$y],$f2);
	  	if($i==3){
				$worksheet1->write_blank($level, 2, $f7);
				$worksheet1->write_blank($level, 3, $f7);
				$worksheet1->write_blank($level, 4, $f7);
				$worksheet1->write_blank($level, 5, $f7);
				$worksheet1->write_blank($level, 6, $f7);
				$worksheet1->write_blank($level, 7, $f7);
				$worksheet1->write_blank($level, 8, $f7);
			}
	  	if($y==2)
	  	$worksheet1->write_string($level,2,$ary_m[$i][$y],$f1_1);
	  	if($y==3)
	  	$worksheet1->write_string($level,3,$ary_m[$i][$y],$f6);
	  	if($y==4)
	  	$worksheet1->write_string($level,5,$ary_m[$i][$y],$f1_1);
	  	if($y==5)
	  	$worksheet1->write_string($level,6,$ary_m[$i][$y],$f6);
	  }
		$level++;
	}




	# unit cost
	$level++;

  for ($i=0; $i < $arr_count; $i++){
  	$worksheet1->set_column($level,$i,$ary[$i]);
  	$worksheet1->write($level,$i,'',$f_1);
  	if($i==1)
	  $worksheet1->write_string($level,$i," : unit cost : ",$f);
	}

	$level++;
	$level++;
	$iunit = array('','Item','Usage','U/Price','','YY/Q\'ty','','Cost');
  for ($i=0; $i < $arr_count; $i++){
  	$worksheet1->write($level,$i,$iunit[$i],$f3);
	}

		$level++;
		$worksheet1->set_row($level, 1, $f3);
		for($i = 0 ; $i < $arr_count ; $i ++ )$worksheet1->write_string($level,$i,'',$f_1);
		$level++;

	if(isset($op['of']['fab_cat'][0]['fi']) && $op['of']['fab_cat'][0]['fi']){
		$mode = 1;
		$key['fi']=$key['fu']=$key['fp']=$key['fq']=$key['fc']='';
		foreach($op['of']['fab_cat'] as $key){
			if($key['fi']){
				if(!isset($key['fi']))$key['fi']='';
				if(!isset($key['fu']))$key['fu']='';
				if(!isset($key['fp']))$key['fp']='';
				if(!isset($key['fq']))$key['fq']='';
				if(!isset($key['fc']))$key['fc']='';
				$fab_cat = array('',$key['fi'],$key['fu'],$key['fp'],'X',$key['fq'],'=',$key['fc']);

			 	for ($i=0; $i < $arr_count; $i++){
			 		$worksheet1->write_string($level,$i,$fab_cat[$i],$f5);
			 		if($i==1)
			  	$worksheet1->write_string($level,$i,$fab_cat[$i],$f2);
			 		if($i==4)
			  	$worksheet1->write_string($level,$i,$fab_cat[$i],$f3);
			  	if($i==6)
			  	$worksheet1->write_string($level,$i,$fab_cat[$i],$f3);
			  }

				if( $mode == 1 ){
					$worksheet1->write_string($level,0,'Fabric :',$f1_1);
				}

				$mode++;
				$level++;
			}
		}
		$level++;
		$worksheet1->set_row($level, 1, $f3);
		for($i = 0 ; $i < $arr_count ; $i ++ )$worksheet1->write_string($level,$i,'',$f_1);
		$level++;
 	}

 	if(isset($op['of']['fus_cat'][0]['fsi']) && $op['of']['fus_cat'][0]['fsi']){
		$mode = 1;
		$key['fsi']=$key['fsu']=$key['fsp']=$key['fsq']=$key['fsc']='';
		foreach($op['of']['fus_cat'] as $key){
			if($key['fsi']){
				if(!isset($key['fsi']))$key['fsi']='';
				if(!isset($key['fsu']))$key['fsu']='';
				if(!isset($key['fsp']))$key['fsp']='';
				if(!isset($key['fsq']))$key['fsq']='';
				if(!isset($key['fsc']))$key['fsc']='';

				if( $mode == 1 ){
					$fus_cat = array('Fabric :',$key['fsi'],$key['fsu'],$key['fsp'],'X',$key['fsq'],'=',$key['fsc']);
				}else{
					$fus_cat = array(''        ,$key['fsi'],$key['fsu'],$key['fsp'],'X',$key['fsq'],'=',$key['fsc']);
				}

			 	for ($i=0; $i < $arr_count; $i++){
			 		$worksheet1->write_string($level,$i,$fus_cat[$i],$f5);
			 		if($i==1)
			  	$worksheet1->write_string($level,$i,$fus_cat[$i],$f2);
			 		if($i==4)
			  	$worksheet1->write_string($level,$i,$fus_cat[$i],$f3);
			  	if($i==6)
			  	$worksheet1->write_string($level,$i,$fus_cat[$i],$f3);
			  }

				if( $mode == 1 ){
					$worksheet1->write_string($level,0,'Fusible :',$f1_1);
				}

				$mode++;
				$level++;
			}
		}
		$level++;
		$worksheet1->set_row($level, 1, $f3);
		for($i = 0 ; $i < $arr_count ; $i ++ )$worksheet1->write_string($level,$i,'',$f_1);
		$level++;
 	}

 	if(isset($op['of']['inter_cat'][0]['ii']) && $op['of']['inter_cat'][0]['ii']){
		$mode = 1;
		$key['ii']=$key['iu']=$key['ip']=$key['iq']=$key['ic']='';
		foreach($op['of']['inter_cat'] as $key){
			if($key['ii']){
				if(!isset($key['ii']))$key['ii']='';
				if(!isset($key['iu']))$key['iu']='';
				if(!isset($key['ip']))$key['ip']='';
				if(!isset($key['iq']))$key['iq']='';
				if(!isset($key['ic']))$key['ic']='';

				if( $mode == 1 ){
					$InterLining = array('InterLining :',$key['ii'],$key['iu'],$key['ip'],'X',$key['iq'],'=',$key['ic']);
				}else{
					$InterLining = array(''        ,$key['ii'],$key['iu'],$key['ip'],'X',$key['iq'],'=',$key['ic']);
				}

			 	for ($i=0; $i < $arr_count; $i++){
			 		$worksheet1->write_string($level,$i,$InterLining[$i],$f5);
			 		if($i==1)
			  	$worksheet1->write_string($level,$i,$InterLining[$i],$f2);
			 		if($i==4)
			  	$worksheet1->write_string($level,$i,$InterLining[$i],$f3);
			  	if($i==6)
			  	$worksheet1->write_string($level,$i,$InterLining[$i],$f3);
			  }

				if( $mode == 1 ){
					$worksheet1->write_string($level,0,'InterLining :',$f1_1);
				}

				$mode++;
				$level++;
			}
		}
		$level++;
		$worksheet1->set_row($level, 1, $f3);
		for($i = 0 ; $i < $arr_count ; $i ++ )$worksheet1->write_string($level,$i,'',$f_1);
		$level++;
 	}

 	if(isset($op['of']['acc_cat'][0]['ai']) && $op['of']['acc_cat'][0]['ai']){
		$mode = 1;
		$key['ai']=$key['au']=$key['ap']=$key['aq']=$key['ac']='';
		foreach($op['of']['acc_cat'] as $key){
			if($key['ai']){
				if(!isset($key['ai']))$key['ai']='';
				if(!isset($key['au']))$key['au']='';
				if(!isset($key['ap']))$key['ap']='';
				if(!isset($key['aq']))$key['aq']='';
				if(!isset($key['ac']))$key['ac']='';

				if( $mode == 1 ){
					$Trimming = array('Trimming :'	,$key['ai'],$key['au'],$key['ap'],'X',$key['aq'],'=',$key['ac']);
				}else{
					$Trimming = array(''        		,$key['ai'],$key['au'],$key['ap'],'X',$key['aq'],'=',$key['ac']);
				}

			 	for ($i=0; $i < $arr_count; $i++){
			 		$worksheet1->write_string($level,$i,$Trimming[$i],$f5);
			 		if($i==1)
			  	$worksheet1->write_string($level,$i,$Trimming[$i],$f2);
			 		if($i==4)
			  	$worksheet1->write_string($level,$i,$Trimming[$i],$f3);
			  	if($i==6)
			  	$worksheet1->write_string($level,$i,$Trimming[$i],$f3);
			  }

				if( $mode == 1 ){
					$worksheet1->write_string($level,0,'Trimming :',$f1_1);
				}

				$mode++;
				$level++;
			}
		}
		$level++;
		$worksheet1->set_row($level, 1, $f3);
		for($i = 0 ; $i < $arr_count ; $i ++ )$worksheet1->write_string($level,$i,'',$f_1);
		$level++;
 	}

 	if(isset($op['of']['oth_cat'][0]['oi']) && $op['of']['oth_cat'][0]['oi']){
		$mode = 1;
		$key['oi']=$key['ou']=$key['op']=$key['oq']=$key['oc']='';
		foreach($op['of']['oth_cat'] as $key){
			if($key['oi']){
				if(!isset($key['oi']))$key['oi']='';
				if(!isset($key['ou']))$key['ou']='';
				if(!isset($key['op']))$key['op']='';
				if(!isset($key['oq']))$key['oq']='';
				if(!isset($key['oc']))$key['oc']='';

				if( $mode == 1 ){
					$oth_cat = array('Other/Treatment :'	,$key['oi'],$key['ou'],$key['op'],'X',$key['oq'],'=',$key['oc']);
				}else{
					$oth_cat = array(''        		,$key['oi'],$key['ou'],$key['op'],'X',$key['oq'],'=',$key['oc']);
				}

			 	for ($i=0; $i < $arr_count; $i++){
			 		$worksheet1->write_string($level,$i,$oth_cat[$i],$f5);
			 		if($i==1)
			  	$worksheet1->write_string($level,$i,$oth_cat[$i],$f2);
			 		if($i==4)
			  	$worksheet1->write_string($level,$i,$oth_cat[$i],$f3);
			  	if($i==6)
			  	$worksheet1->write_string($level,$i,$oth_cat[$i],$f3);
			  }

				if( $mode == 1 ){
					$worksheet1->write_string($level,0,'Other/Treatment :',$f1_1);
				}

				$mode++;
				$level++;
			}
		}
		$level++;
		$worksheet1->set_row($level, 1, $f3);
		for($i = 0 ; $i < $arr_count ; $i ++ )$worksheet1->write_string($level,$i,'',$f_1);
		$level++;
 	}

	if(empty($op['of']['deal']))$op['of']['deal']='';
	$costn = array('C.M. :','quota charge :','Mark-Up :','Commission :','Total Costing :','Offer Price :');
	$cost = array($op['of']['cm'],$op['of']['quota'],$op['of']['gm_rate'],$op['of']['comm'],$op['of']['tt_cost'],$op['of']['deal']);
	$c_count = count($cost);
	for($i = 0 ; $i < $c_count ; $i ++ ){
		for($y = 0 ; $y < $arr_count ; $y ++ ){
			$hon = ($i==2||$i==3)?'%' : '';
			$worksheet1->write_string($level,$y,'------------------------------------------------------------',$f4);
			if($y==0)
			$worksheet1->write_string($level,$y,$costn[$i],$f4);
			if($y==7)
			$worksheet1->write_string($level,$y,$cost[$i].$hon,$f5);
			if($i==5&&$y==7)
			$worksheet1->write_string($level,$y,$cost[$i].$hon,$f8);
		}
		$level++;
	}
	$level++;
	$worksheet1->set_row($level, 1, $f3);
	for($i = 0 ; $i < $arr_count ; $i ++ )$worksheet1->write_string($level,$i,'',$f3);
	$level++;
	$level++;

	#Technical / Equipment
	if(!empty($op['of']['tech'])){
		$worksheet1->write_string($level,0,'Technical / Advisements :',$f3);
		$worksheet1->merge_cells($level, 1, $level, 7);
		$worksheet1->write_string($level,1,$op['of']['tech'],$f7);
		$worksheet1->write_blank($level, 2, $f7);
		$worksheet1->write_blank($level, 3, $f7);
		$worksheet1->write_blank($level, 4, $f7);
		$worksheet1->write_blank($level, 5, $f7);
		$worksheet1->write_blank($level, 6, $f7);
		$worksheet1->write_blank($level, 7, $f7);
		$worksheet1->write_blank($level, 8, $f7);

		$level++;
		$level++;
		$worksheet1->set_row($level, 1, $f3);
		for($i = 0 ; $i < $arr_count ; $i ++ )$worksheet1->write_string($level,$i,'',$f_1);
		$level++;
		$level++;
	}

	# Subject Event Status
	if(!empty($op['of_log'])){
		$worksheet1->write_string($level,0,'Subject',$f3);
		$worksheet1->merge_cells($level, 1, $level, 2);
		$worksheet1->write_string($level,1,'Event',$f3);
		$worksheet1->write_blank($level, 2, $f3);
		$worksheet1->merge_cells($level, 3, $level, 7);
		$worksheet1->write_string($level,3,'Status',$f3);
		$worksheet1->write_blank($level, 4, $f3);
		$worksheet1->write_blank($level, 5, $f3);
		$worksheet1->write_blank($level, 6, $f3);
		$worksheet1->write_blank($level, 7, $f3);
		$level++;


		if($op['of_log'][0]['subject']){
			$mode = 1;
			$key['fi']=$key['fu']=$key['fp']=$key['fq']=$key['fc']='';
			foreach($op['of_log'] as $key){
				if($key['subject']){
					if(!isset($key['subject']))$key['subject']='';
					if(!isset($key['comment']))$key['comment']='';
					if(!isset($key['c_date']))$key['c_date']='';
					if(!isset($key['status']))$key['status']='';
					if(!isset($key['s_date']))$key['s_date']='';
					#$of_log = array($key['subject'],$key['comment'],$key['c_date'],$key['status'],$key['s_date']);
					#$c_log = count($of_log);

				 	for ($i=0; $i < $arr_count; $i++){
				 		if($i==0)
				  	$worksheet1->write_string($level,$i,$key['subject'].' :',$f4);
				  	if($i==1){
				  		$worksheet1->merge_cells($level, 1, $level, 2);
				  		$worksheet1->write_string($level, 1,$key['comment']. ' (' .$key['c_date'].')',$f7);
				  		$worksheet1->write_blank($level, 2, $f7);
				  	}
				  	if($i==3){
				  		$worksheet1->merge_cells($level, 3, $level, 7);
				  		$worksheet1->write_string($level,3,$key['status']. ' (' .$key['s_date'].')',$f7);
							$worksheet1->write_blank($level, 4, $f7);
							$worksheet1->write_blank($level, 5, $f7);
							$worksheet1->write_blank($level, 6, $f7);
							$worksheet1->write_blank($level, 7, $f7);
				  	}
				  }

					$mode++;
					$level++;
				}
			}
			$level++;
			$worksheet1->set_row($level, 1, $f3);
			for($i = 0 ; $i < $arr_count ; $i ++ )$worksheet1->write_string($level,$i,'',$f_1);
			$level++;
	 	}
	}


  $workbook->close();

	break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "offer_copy":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "offer_copy":

		check_authority('062',"add");

		$my_dept = $GLOBALS['SCACHE']['ADMIN']['dept']; 

//建立新Offer  -- start
		$sr_key = substr($THIS_YEAR,2,2).substr($TODAY,5,2).substr($TODAY,8,2).'-';
		$num = $offer->get_new_number($sr_key); 
		$f1 = $offer->add_offer($num);
		$PHP_id = $f1;
		$PHP_num=$num;
//建立新Offer  -- end

		$offer->copy($PHP_org_id,$PHP_id);//複製offer

		if (!$parm = $offer->get($PHP_id)) {
			$op['msg'] = $offer->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		if ($parm['cons']) { $parm['cons'] = str_replace("<br />","",$parm['cons']);}
		if ($parm['tech']) { $parm['tech'] = str_replace("<br />","",$parm['tech']);}
		if ($parm['remark']) { $parm['remark'] = str_replace("<br />","",$parm['remark']);}
		if($my_dept != "RD"){  // 如果是研發部門人員進入時......
  // 如果是 SA 進入時......
			if ($parm['des']) { $parm['des'] = str_replace("<br />","",$parm['des']);}
			if ($parm['fab']) { $parm['fab'] = str_replace("<br />","",$parm['fab']);}
		}

		//  HTML 需要的參數 ---------------------------------------------
		$op['cust_id'] = $PHP_cust;

		$back_str = "offer.php?PHP_action=offer_search&PHP_dept_code=".$PHP_dept_code."&PHP_num=".$PHP_num."&PHP_cust=".$PHP_cust."&PHP_style=".$PHP_style."&SCH_str".$SCH_str."=&SCH_end=".$SCH_end."&SCH_agt=".$SCH_agt;
		$op['back_str'] = $back_str;
		$op['of'] = $parm;
		$op['my_dept'] = $my_dept;

		// 檢查 相片是否存在
		if(file_exists($GLOBALS['config']['root_dir']."/offer/".$op['of']['num'].".jpg")){
			$op['picture'] = "./offer/".$op['of']['num'].".jpg";
		} else {
			$op['picture'] = "./images/graydot.gif";
		}

		$total=0;
		$op['of'] = $offer->create_items($op['of']);
		$op['of']['fty'] = $arry2->select($FACTORY,$op['of']['fty'],'PHP_fty','select','');

		$where_str="ORDER BY cust_s_name"; //依cust_s_name排序
		$cust_def = $cust->get_fields('cust_init_name',$where_str);
		$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//取出客戶代號
		for ($i=0; $i< sizeof($cust_def); $i++)
		{
			$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//以 [客戶代號-客戶簡稱] 呈現
		}
		$op['agent_select'] =  $arry2->select($cust_value,$op['of']['agent'],'PHP_agent','select','',$cust_def_vue); 

		$msg = "Successfully copy OFFER from [".$PHP_org_num."] to [".$PHP_num."]";
		$op['msg'][]=$msg;
		$log->log_add(0,"92C",$msg);


		page_display($op,'062', $TPL_OFFER_SA_EDIT);
		break;





}   // end case ---------

?>
