<?php
session_start();

session_register	('sch_parm');
session_register	('sch_stock_parm');
require_once "config.php";
require_once "config.admin.php";

$PHP_SELF = $_SERVER['PHP_SELF'];

$perm = $GLOBALS['power'];

require_once "init.object.php";


session_register	('CUST_DEL');
$para_cm = $para->get(0,'cust_del');
$CUST_DEL = $para_cm['set_value'] ;

$op = array();

// echo $PHP_action;
switch ($PHP_action) {

#++++++++++++++    Fabric +++++++++++++++++++  2009/01/01  +++++++++++++++++
#		 job 11    客戶基本資料 CUSTOMER 
#++++++++++++++++++++++++++++++++++++++++++++  2009/01/01  +++++++++++++++++
#		case "cust":          主頁
#		case "cust_search":   搜尋
#		case "cust_shift":    移轉
#		case "do_cust_shift":
#		case "cust_add":      新增
#		case "do_cust_add":
#		case "cust_view":     檢視
#		case "cust_edit":     修改
#		case "do_cust_edit":
#		case "cust_del":      刪除
###################################################

//=======================================================
    case "cust":
 		check_authority("001","view");
		$in_id = $GLOBALS['SCACHE']['ADMIN']['dept'];
		$in_dept = '';
		$where_str ='';
		$manager ='';
		$dept_select = array();
		$sch_parm = array();

		$sales_dept = get_sales_dept(); // 取出 業務的部門 [不含K0] ------


	
		// for ($i=0; $i<count($sales_dept);$i++){
			// if($in_id == $sales_dept[$i]){
				// $in_dept = $sales_dept[$i];   // 如果是業務部進入 則dept_code 指定該業務部---
			// }
		// }
		
		if (!$in_dept) {    // 當不是業務部人[也不含 K0 的人 ]進入時
			$manager = 1;
			$dept_select = $arry2->select($sales_dept,$GLOBALS['SCACHE']['ADMIN']['dept'],"PHP_dept_code","select","");  //業務部門 select選單
			$dept_search = $arry2->select($sales_dept,$GLOBALS['SCACHE']['ADMIN']['dept'],"SCH_dept","select","");  //業務部門 select選單
		} else {
			$where_str = " WHERE dept = '".$in_dept."' ";
			$dept_search = '<b>'.$in_id.'</b><input type=hidden name=SCH_dept value='.$in_id.'>';
		}


		//   因為 search 將 $op重置 所以再帶入其它參數
		$op['dept_code'] = $in_dept;
		$op['dept_select'] = $dept_select;
		$op['manager_flag'] = $manager;
		$op['dept_search'] =	$dept_search ;
		$op['msg'] = $cust->msg->get(2);

//080725message增加		
	$note=$notify->search($GLOBALS['SCACHE']['ADMIN']['login_id'], "`read`=0");
	$op['max_notify'] = $note['max_no'];
		
		page_display($op,"001", $TPL_CUST_SEARCH);		    	    
		break;


//=======================================================
    case "cust_search":
 		check_authority("001","view");	
		
		$letter = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","0","1","2","3","4","5","6","7","8","9"); //add
		if(!isset($PHP_letter)) $PHP_letter = ''; //add
		
		if(isset($SCH_cust))
		{
			$SCH_ud = 1;
			if(!isset($SCH_sort))$SCH_sort = 'cust_s_name';
//			if(isset($sch_parm['SCH_sort']) && $sch_parm['SCH_sort'] == $SCH_sort)$SCH_ud *= -1;

			$sch_parm = array(	"SCH_cust"				=>  $SCH_cust,
								"SCH_name"				=>  $SCH_name,
								"SCH_dept"				=>	$SCH_dept,
								"SCH_sort"				=>	$SCH_sort,
								"SCH_ud"				=>	$SCH_ud,
								"PHP_sr_startno"		=>	$PHP_sr_startno,
								"PHP_action"			=>	$PHP_action,
								"PHP_letter"			=>  $PHP_letter //add,注意最後的逗號
				);

		}else if(isset($PHP_sr_startno)){
			$sch_parm['PHP_sr_startno'] = $PHP_sr_startno;
		}else if(isset($SCH_sort)){
			if (!isset($sch_parm['SCH_sort'])) $sch_parm['SCH_sort']=''; //add
			if( $sch_parm['SCH_sort'] == $SCH_sort){
				$sch_parm['SCH_ud'] *= -1;
			}else{
				$sch_parm['SCH_ud'] = 1;
				$sch_parm['SCH_sort'] = $SCH_sort; //add
				$sch_parm['SCH_cust']=$sch_parm['SCH_name']=$sch_parm['SCH_dept']=$sch_parm['PHP_sr_startno']=$sch_parm['PHP_letter']=$sch_parm['PHP_action']=''; //add
			}
		}else if(isset($PHP_letter)){ //add
			$sch_parm['PHP_letter'] = $PHP_letter;
			$sch_parm['PHP_sr_startno']=$sch_parm['SCH_cust']=$sch_parm['SCH_name']=''; //取消where條件
		}	
			
		if (!$op = $cust->search(2)) {    // 叫出所屬部門的全部客戶記錄
			$op['msg']= $cust->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		

		$op['msg'] = $cust->msg->get(2);
		$op['letter'] = $letter; //add
		if(isset($PHP_msg))$op['msg'][] = $PHP_msg;
		page_display($op,"001", $TPL_CUST);		    	    
		break;


//=======================================================
	case "cust_enable":

		check_authority("001","del");

		$update = array();
		$update['id'] = $PHP_id;
		$update['field_name'] = "active";
		$update['field_value'] = "y";

		if (!$cust->update_field($update)) {
			$op['msg'] = $cust->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;	    
		}

					# 記錄使用者動態
		$message = "Cust:[ $PHP_cust_s_name ] re-rights‧";
		$log->log_add(0,"11D",$message);

		if (!$op = $cust->search(2,$where_str)) {
			$op['msg']= $cust->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

	    $redir_str = $PHP_SELF.'?PHP_action=cust_search&PHP_sr_startno='.$PHP_sr_startno.'&PHP_msg='.$message;
		redirect_page($redir_str);
		
	break;
//==========================================================================
case "cust_disable":

		check_authority("001","del");
		
		$update = array();
		$update['id'] = $PHP_id;
		$update['field_name'] = "active";
		$update['field_value'] = "n";

		if (!$cust->update_field($update)) {
			$op['msg'] = $cust->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);
			break;	    
		}

					# 記錄使用者動態
		$message = "Cust:[ $PHP_cust_s_name ] Del-rights‧";
		$log->log_add(0,"11D",$message);

		if (!$op = $cust->search(2,$where_str)) {
			$op['msg']= $cust->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$redir_str = $PHP_SELF.'?PHP_action=cust_search&PHP_sr_startno='.$PHP_sr_startno.'&PHP_msg='.$message;
		redirect_page($redir_str);
		
	break;
//==========================================================================
    case "cust_add":
		check_authority("001","add");
			if ($PHP_dept_code ==""){
				$op['msg'][] = "sorry! Please choice dept. first!";

					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;
			}
			$op['country_select'] = $arry2->select($COUNTRY,"","PHP_cust_country","select","");  //業務部門 select選單

				$op['dept_code'] = $PHP_dept_code;
		page_display($op,"001", $TPL_CUST_ADD);			    	    
		break;
//=======================================================
	case "cust_shift":
	$where_str = "WHERE dept = '".$PHP_dept."'";
	$op=$cust->search(0,$where_str,100);
	
	$op['org_dept'] = $PHP_dept;
	$sales_dept = get_sales_dept(); // 取出 業務的部門 [不含K0] ------
	$dept_select = $arry2->select($sales_dept,$GLOBALS['SCACHE']['ADMIN']['dept'],"PHP_dept_code","select","");  //業務部門 select選單
	$op['dept_select'] = $dept_select;
	page_display($op,"001", $TPL_CUST_SHIFT);
	break;

//=======================================================
	case "do_cust_shift":

	foreach ($PHP_cust as $key => $value)
	{
		$q_str = "UPDATE cust SET ".$parm['field_name']."='".$parm['field_value']."'  WHERE id='".$parm['id']."'";

		 $parm = array ( 'field_name' 		=>	'dept',
		 								 'field_value'		=>	$PHP_dept_code,
		 								 'id'							=>	$key
		 								 );
		 $cust->update_field($parm);
		 $message = "success shift ".$value." to dept : ".$PHP_dept_code;
		 $log->log_add(0,"11E",$message);
	}
	$redir_str = $PHP_SELF.'?PHP_action=cust';
	redirect_page($redir_str);
	break;

//=======================================================
	case "do_cust_add":
		check_authority("001","add");
		$parm = array(	"dept_code"			=>	$PHP_dept_code,
						"country"					=>	$PHP_cust_country,
						"cust_s_name"			=>	$PHP_cust_s_name,
						"cust_f_name"			=>	$PHP_cust_f_name,
						"cust_init_name"	=>	$PHP_cust_init_name,
						"cntc_phone"			=>	$PHP_cntc_phone,
						"cntc_addr"				=>	$PHP_cntc_addr,
						"cntc_person1"		=>	$PHP_cntc_person1,
						"cntc_cell1"			=>	$PHP_cntc_cell1,
						"email1"					=>	$PHP_email1,
						"cntc_person2"		=>	$PHP_cntc_person2,
						"cntc_cell2"			=>	$PHP_cntc_cell2,
						"email2"					=>	$PHP_email2,
						"cntc_fax"				=>	$PHP_cntc_fax,
						"agent"						=>	$PHP_agent
				);

				$op['cust'] = $parm;

		$f1 = $cust->add($parm);
	
		if ($f1) {  // 成功輸入資料時
		
				# 記錄使用者動態
				$message= "Append customer:".$PHP_cust_s_name." for dept.:".$PHP_dept_code;						
				$log->log_add(0,"11A",$message);
				$redir_str = $PHP_SELF.'?PHP_action=cust_search&PHP_msg='.$message.'&SCH_sort=id';
				redirect_page($redir_str);
	
		}	else {  // 當沒有成功輸入新增user的欄位時....
			$op['country_select'] = $arry2->select($COUNTRY,$PHP_cust_country,"PHP_cust_country","select","");  //業務部門 select選單
			$op['cust'] = $parm;
			$op['dept_code'] = $parm['dept_code'];
			$op['msg'] = $cust->msg->get(2);
			page_display($op,"001", $TPL_CUST_ADD);    	    
			break;
		}

//========================================================================================	
	case "cust_view":
		check_authority("001","view");
		$op['cust'] = $cust->get($PHP_id);  //取出該筆記錄

		if (!$op['cust']) {
			$op['msg'] = $cust->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

	if(isset($PHP_msg))$op['msg'][] = $PHP_msg;

	page_display($op,"001", $TPL_CUST_VIEW);	    	    
	break;

//=======================================================
case "cust_edit":
check_authority("001","edit");
$op['cust'] = $cust->get($PHP_id);
if (!$op['cust']) {
	$op['msg'] = $cust->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}
$op['country_select'] = $arry2->select($COUNTRY,$op['cust']['country'],"PHP_cust_country","select","");  //業務部門 select選單

page_display($op,"001", $TPL_CUST_EDIT);
break;



//=======================================================
case "do_cust_edit":
check_authority("001","edit");
$argv = array(
	"id"				=>  $PHP_id,
	"dept_code"			=>	$PHP_dept_code,
	"country"			=>	$PHP_cust_country,
	"cust_s_name"		=>	$PHP_cust_s_name,
	"cust_f_name"		=>	$PHP_cust_f_name,
	"cust_init_name"	=>	$PHP_cust_init_name,
	"cntc_phone"		=>	$PHP_cntc_phone,
	"cntc_addr"			=>	$PHP_cntc_addr,
	"cntc_person1"		=>	$PHP_cntc_person1,
	"cntc_cell1"		=>	$PHP_cntc_cell1,
	"email1"			=>	$PHP_email1,
	"cntc_person2"		=>	$PHP_cntc_person2,
	"cntc_cell2"		=>	$PHP_cntc_cell2,
	"email2"			=>	$PHP_email2,
	"cntc_fax"			=>	$PHP_cntc_fax,
	"agent"				=>	$PHP_agent,
	"uni_no"			=>	$PHP_uni_no
);

if (!$cust->edit($argv)) {
	$op['msg'] = $cust->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;	    
}

# 記錄使用者動態
$message = "Update customer:[$PHP_cust_s_name]context‧";
$log->log_add(0,"11E",$message);
$redir_str = $PHP_SELF.'?PHP_action=cust_view&PHP_id='.$PHP_id.'&PHP_msg='.$message;
redirect_page($redir_str);


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "cust_del":

						// 必需要 manager login 才能真正的刪除 user
		check_authority("001","del");

		$op['cust'] = $cust->get($PHP_id);
		
		if (!$cust->del($PHP_id)) {
			$op['msg'] = $cust->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;	    
		}
					# 記錄使用者動態

		$message = "Delete customer:[".$op['cust']['cust_s_name']."] Record。";
		$log->log_add(0,"11D",$message);
		$redir_str = $PHP_SELF.'?PHP_action=cust_search&PHP_msg='.$message;
		redirect_page($redir_str);
	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "ver_update":

						// 必需要 manager login 才能真正的刪除 user
		check_authority("001","del");

		$cust->update_ver();
		exit;	
		
		
		
		
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "supl":	 	JOB 12V
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "supl":
 
		check_authority("002","view");

		if ($GLOBALS['SCACHE']['ADMIN']['dept'] == "SA") {  
			$op['manager_flag'] = 1;
		}
		$sch_parm = array();
		$op['msg']= $supl->msg->get(2);
		// 選單 -----------
		$op['country_select'] = $arry2->select($COUNTRY,'','PHP_country','select','');  	
		$cat_def = $SUPL_TYPE;   // 取出全部的 供應商類別代號 [ config.php ]
		$op['cat_select'] = $arry2->select($cat_def,"","PHP_supl_cat","select",""); 

//080725message增加		
	$note=$notify->search($GLOBALS['SCACHE']['ADMIN']['login_id'], "`read`=0");
	$op['max_notify'] = $note['max_no'];


		page_display($op,"002", $TPL_SUPL_SEARCH);
		break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "supl_search":	 	JOB 12V
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "supl_search":
 
		check_authority("002","view");
			// 選單----------
		$op['country_select'] = $arry2->select($COUNTRY,'','PHP_country','select','');  	

		if(isset($PHP_supl_s_name))
		{
			$SCH_ud = 1;
			if(!isset($SCH_sort))$SCH_sort = 'id';

			$sch_parm = array();
			$sch_parm = array(	
						"PHP_supl_cat"			=>  $PHP_supl_cat,
						"PHP_country"			=>	$PHP_country,
						"PHP_supl_s_name"		=>	$PHP_supl_s_name,
						"PHP_supl_f_name"		=>	$PHP_supl_f_name,
						"SCH_sort"				=>	$SCH_sort,
						"SCH_ud"				=>	$SCH_ud,
						"PHP_sr_startno"		=>	$PHP_sr_startno,
						"PHP_action"			=>	$PHP_action						
				);

			}else if(isset($PHP_sr_startno)){		
				$sch_parm['PHP_sr_startno'] = $PHP_sr_startno;
			}else if(isset($SCH_sort)){				
				if(isset($sch_parm['SCH_sort']) && $sch_parm['SCH_sort'] == $SCH_sort){$sch_parm['SCH_ud'] *= -1;}else{ $sch_parm['SCH_ud'] = 1;}
				$sch_parm['SCH_sort'] = $SCH_sort;
			}



		
		if (!$op = $supl->search(0)) {	//搜尋列表
			$op['msg']= $supl->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		if ($GLOBALS['SCACHE']['ADMIN']['dept'] == "SA") { 
			$op['manager_flag'] = 1;
		}
		if (isset($sch_parm['PHP_supl_cat']) && $sch_parm['PHP_supl_cat'])$op['supl_cat'] = $sch_parm['PHP_supl_cat'];

//		$op['cgi'] = $parm;

		$op['msg']= $supl->msg->get(2);
		if(isset($PHP_msg)) $op['msg'][] = $PHP_msg;
		page_display($op,"002", $TPL_SUPL);
		break;
		
		
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "supl_view":	 	JOB 12V
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "supl_view":

		check_authority("002","view");

		$op['supl'] = $supl->get($PHP_id);  //取出該筆記錄
		$op['supl_bank'] = $supl->get_bank(0,$op['supl']['vndr_no']);
		
		if (!$op['supl']) {
			$op['msg'] = $supl->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		if ($op['supl']['usance'] == "000")	$op['supl']['usance']="0";		//依代號改為名稱
		$ary=array('A','B','C','D','E','F','G','H','I');
		for ($i=0; $i<sizeof($ary); $i++)
		{
			if ($op['supl']['usance'] == $ary[$i])	$op['supl']['usance']=$usance[$i];
		}
				
		for ($i=0; $i< 4; $i++)
		{
			if ($op['supl']['dm_way'] == $dm_way[1][$i]) $op['supl']['dm_way']=$dm_way[0][$i];
		}
		$op['msg'] = $supl->msg->get(2);
	
		page_display($op,"002", $TPL_SUPL_VIEW);
		break;		
		

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "supl_add":	 	JOB 12A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "supl_add":
		
		check_authority("002","add");
		$sch_parm =array();
		if (!$PHP_supl_cat){

			$cat_def = $SUPL_TYPE;   // 取出全部的 供應商類別代號 [ config.php ]
			$op['cat_select'] = $arry2->select($cat_def,"","PHP_supl_cat","select","");  
			$op['country_select'] = $arry2->select($COUNTRY,'','PHP_country','select','');  	

			$op['msg'][] = "sorry! Please chooice supplier category !";

			page_display($op,"002", $TPL_SUPL_SEARCH);
			break;
		}
		$op['supl_cat'] = $PHP_supl_cat;
		$op['country_select'] = $arry2->select($COUNTRY,'','PHP_country','select','');  	
		$op['usance_select'] = $arry2->select($usance,"","PHP_usance","select","");
		$op['dm_way_select'] = $arry2->select($dm_way[0],"","PHP_dm_way","select","");
		page_display($op,"002", $TPL_SUPL_ADD);
		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_supl_add":	 	JOB 12A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_supl_add":

		check_authority("002","add");		
		$parm = array(	"supl_cat"			=>	$PHP_supl_cat,
										"vndr_no"				=>	$PHP_vndr_no,
										"country"				=>	$PHP_country,
										"supl_s_name"		=>	$PHP_supl_s_name,
										"supl_f_name"		=>	$PHP_supl_f_name,
										"uni_no"				=>	$PHP_uni_no,
										"cntc_phone"		=>	$PHP_cntc_phone,
										"cntc_addr"			=>	$PHP_cntc_addr,
										"cntc_person1"	=>	$PHP_cntc_person1,
										"cntc_cell1"		=>	$PHP_cntc_cell1,
										"email1"				=>	$PHP_email1,
										"cntc_person2"	=>	$PHP_cntc_person2,
										"cntc_cell2"		=>	$PHP_cntc_cell2,
										"email2"				=>	$PHP_email2,
										"cntc_fax"			=>	$PHP_cntc_fax,
										"usance"				=>	$PHP_usance,						
										"dm_way"				=>	$PHP_dm_way
				);

				$op['supl'] = $parm;

		$f1 = $supl->add($parm);
		
		if ($f1) {  // 成功輸入資料時

			$message= "Append supl:".$parm['supl_s_name'];
			$log->log_add(0,"12A",$message);  	# 記錄使用者動態

			$redir_str = 'primary.php?PHP_action=supl_search&PHP_msg='.$message;
			redirect_page($redir_str);
	
		}	else {  // 當沒有成功輸入新增user的欄位時....

			$op['supl'] = $parm;
			$op['supl_cat'] = $parm['supl_cat'];
			$op['msg'] = $supl->msg->get(2);


			$cat_def = $supl_type->get_fields('supl_type'); 
			$op['cat_select'] = $arry2->select($cat_def,"","PHP_supl_cat","select",""); 
			$op['country_select'] = $arry2->select($COUNTRY,$PHP_country,'PHP_country','select','');  
			
			$op['usance_select'] = $arry2->select($usance,$PHP_usance,"PHP_usance","select","");
			$op['dm_way_select'] = $arry2->select($dm_way[0],$PHP_dm_way,"PHP_dm_way","select","");	

			page_display($op,"002", $TPL_SUPL_ADD);
			break;

		}
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "supl_edit":	 	JOB 12E
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "supl_edit":

		check_authority("002","edit");

		if ($GLOBALS['SCACHE']['ADMIN']['dept'] == "SA") { 
			$op['manager_flag'] = 1;
		}
		
		$op['supl'] = $supl->get($PHP_id);
		$op['supl_bank'] = $supl->get_bank(0,$op['supl']['vndr_no']);
		
		if (!$op['supl']) {
			$op['msg'] = $supl->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}		
		$ary=array('A','B','C','D','E','F','G','H','I');
		for ($i=0; $i<sizeof($ary); $i++)
		{
			if ($op['supl']['usance'] == $ary[$i])	$op['supl']['usance']=$usance[$i];
		}
				
		for ($i=0; $i< 4; $i++)
		{
			if ($op['supl']['dm_way'] == $dm_way[1][$i]) $op['supl']['dm_way']=$dm_way[0][$i];
		}
		$op['country_select'] = $arry2->select($COUNTRY,$op['supl']['country'],'PHP_country','select','');  	
		$op['usance_select'] = $arry2->select($usance,$op['supl']['usance'],"PHP_usance","select","");
		$op['dm_way_select'] = $arry2->select($dm_way[0],$op['supl']['dm_way'],"PHP_dm_way","select","");	
		$op['msg'] = $supl->msg->get(2);

		page_display($op,"002", $TPL_SUPL_EDIT);
		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_supl_edit":	 	JOB 12E
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_supl_edit":

		check_authority("002","edit");

		foreach($PHP_bank_id as $k=>$v){
			if($v){
				$bank = array(	"id"	=>	$PHP_bank_id[$k],
							"bank_code"	=>	$PHP_bank_code[$k],
							"bank_name"	=>	$PHP_bank_name[$k],
							"account"	=>	$PHP_account[$k]
						);
				if (!$supl->bank_edit($bank)) {
					$op['msg'] = $supl->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;	    
				}
			}else{
				$bank = array(	"vndr_no"	=>	$PHP_vndr_no,
								"bank_code"	=>	$PHP_bank_code[$k],
								"bank_name"	=>	$PHP_bank_name[$k],
								"account"	=>	$PHP_account[$k]
						);
				if (!$supl->bank_add($bank)) {
					$op['msg'] = $supl->msg->get(2);
					$layout->assign($op);
					$layout->display($TPL_ERROR);  		    
					break;	    
				}
				$message = "Success append bank code:[$PHP_bank_code[$k]]‧";
				$log->log_add(0,"12E",$message);
			}
		}
		
		$argv = array(	"id"				=>  $PHP_id,
						"supl_cat"			=>	$PHP_supl_cat,
						"country"			=>	$PHP_country,
						"supl_s_name"		=>	$PHP_supl_s_name,
						"supl_f_name"		=>	$PHP_supl_f_name,
						"uni_no"			=>	$PHP_uni_no,
						"cntc_phone"		=>	$PHP_cntc_phone,
						"cntc_addr"			=>	$PHP_cntc_addr,
						"cntc_person1"		=>	$PHP_cntc_person1,
						"cntc_cell1"		=>	$PHP_cntc_cell1,
						"email1"			=>	$PHP_email1,
						"cntc_person2"		=>	$PHP_cntc_person2,
						"cntc_cell2"		=>	$PHP_cntc_cell2,
						"email2"			=>	$PHP_email2,
						"cntc_fax"			=>	$PHP_cntc_fax,
						"usance"			=>	$PHP_usance,						
						"dm_way"			=>	$PHP_dm_way
				);


		if (!$supl->edit($argv)) {
			$op['msg'] = $supl->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;	    
		}

					# 記錄使用者動態
		$message = "Success update supplier:[$PHP_supl_s_name]‧";
		$log->log_add(0,"12E",$message);

		$redir_str = 'primary.php?PHP_action=supl_search&PHP_msg='.$message;
		redirect_page($redir_str);


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "supl_del":	 	JOB 12D
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "supl_del":

		// 必需要 manager login 才能真正的刪除 SUPLIER
		if(!$admin->is_power("002","del") && !($GLOBALS['SCACHE']['ADMIN']['id'] == "SA" )) {
			$op['msg'][] = "sorry! you don't have this Authority!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$op['supl'] = $supl->get($PHP_id);
		
		if (!$supl->del($PHP_id,$PHP_vndr_no)) {
			$op['msg'] = $supl->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;	    
		}
					# 記錄使用者動態

		$message = "刪除 供應商:[".$op['supl']['supl_s_name']."] 記錄。";
		$log->log_add(0,"12D",$message);

		$redir_str = 'primary.php?PHP_action=supl_search&PHP_msg='.$message;
		redirect_page($redir_str);
	
	
	
//=======================================================
    case "lots": 
		check_authority("003","view");
		
//080725message增加		
	$note=$notify->search($GLOBALS['SCACHE']['ADMIN']['login_id'], "`read`=0");
	$op['max_notify'] = $note['max_no'];
			
		page_display($op,"003", $TPL_LOTS_SEARCH);		    	    
		break;

 //=======================================================
    case "do_lots_search":		
		check_authority("003","view");

		$SCH_code='';
		 
		if (isset($SCH_cat1)) $SCH_code.=$SCH_cat1;
		if (isset($SCH_cat2)) $SCH_code.=$SCH_cat2;
		if (!isset($SCH_cat1)) $SCH_cat1 ='';
		if (!isset($SCH_cat2)) $SCH_cat2 ='';

		if(isset($SCH_lots_code))
		{
			$SCH_ud = 1;
			if(!isset($SCH_sort))$SCH_sort = 'lots_code';
			$sch_parm = array();			
			$sch_parm = array(	"SCH_cat1"				=>  $SCH_cat1,
													"SCH_cat1"				=>  $SCH_cat2,
													"SCH_mile"				=>	$SCH_mile,
													"SCH_cons"				=>	$SCH_cons,
													"SCH_lots_code"		=>	$SCH_lots_code,
													"SCH_lots_name"		=>	$SCH_lots_name,
													"SCH_comp"				=>	$SCH_comp,
													"SCH_sort"				=>	$SCH_sort,
													"SCH_ud"					=>	$SCH_ud,
													"PHP_sr_startno"	=>	$PHP_sr_startno,
													"PHP_action"			=>	$PHP_action
				);
				
			}else if(isset($PHP_sr_startno)){
				$sch_parm['PHP_sr_startno'] = $PHP_sr_startno;
			}else if(isset($SCH_sort)){				
				if( $sch_parm['SCH_sort'] == $SCH_sort){$sch_parm['SCH_ud'] *= -1;}else{ $sch_parm['SCH_ud'] = 1;}
				$sch_parm['SCH_sort'] = $SCH_sort;
			}else if(isset($PHP_from) &&$PHP_from =='add'){
				$sch_parm = array(	"SCH_cat1"				=>  '',
														"SCH_cat1"				=>  '',
														"SCH_mile"				=>	'',
														"SCH_cons"				=>	'',
														"SCH_lots_code"		=>	'',
														"SCH_lots_name"		=>	'',
														"SCH_comp"				=>	'',
														"SCH_sort"				=>	'lots_code',
														"SCH_ud"					=>	'',
														"PHP_sr_startno"	=>	'',
														"PHP_action"			=>	''
				);
			
			}



		if (!$op = $lots->search(2)) {
			$op['msg']= $lots->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		
		$op['msg']= $lots->msg->get(2);
		if (isset($PHP_msg)) $op['msg'][]=$PHP_msg;
		page_display($op,"003", $TPL_LOTS);		    	    
		break;


//========================================================================================	
	case "lots_view":

		check_authority("003","view");
		if (isset($PHP_code))
		{
			$op['lots'] = $lots->get(0,$PHP_code);  //取出該筆記錄
		}else{
			$op['lots'] = $lots->get($PHP_id);  //取出該筆記錄
		}

		if (!$op['lots']) {
			$op['msg'] = $lots->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		// 特別設定 :
		if($op['lots']['price1']== "0.00") { $op['lots']['price1']="";}
		if($op['lots']['price2']== "0.00") { $op['lots']['price2']="";}
		if($op['lots']['price3']== "0.00") { $op['lots']['price3']="";}


				# 記錄使用者動態

			$op['cat_1']	= substr($op['lots']['lots_code'],1,1);
			$op['cat_2']	= substr($op['lots']['lots_code'],2,1);

			$op['msg'] = $lots->msg->get(2);
			$op['back_str']="&a";	
			page_display($op,"003", $TPL_LOTS_VIEW);		    	    
		break;
	
	
//=======================================================
    case "lots_add":
	check_authority("003","add");
	// create combo box for vendor fields.....


		$where_str=" order by cust_s_name"; //依cust_s_name排序
		$cust_def = $cust->get_fields('cust_init_name',$where_str);
		$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//取出客戶代號
		for ($i=0; $i< sizeof($cust_def); $i++)
		{
			$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//以 [客戶代號-客戶簡稱] 呈現
		}
		$op['cust_select'] =  $arry2->select($cust_value,'','PHP_cust','select','',$cust_def_vue); 


		$op['unit1'] = $arry2->select($LOTS_PRICE_UNIT,'','PHP_unit1','select','');  	
		$op['unit2'] = $arry2->select($LOTS_PRICE_UNIT,'','PHP_unit2','select','');  	
		$op['unit3'] = $arry2->select($LOTS_PRICE_UNIT,'','PHP_unit3','select','');  	
		
		$op['currency1'] = $arry2->select($CURRENCY,'','PHP_currency1','select','');  	
		$op['currency2'] = $arry2->select($CURRENCY,'','PHP_currency2','select','');  	
		$op['currency3'] = $arry2->select($CURRENCY,'','PHP_currency3','select','');  	
						    	    
		$op['term3'] = $arry2->select($TRADE_TERM,'','PHP_term3','select','');
		$op['term2'] = $arry2->select($TRADE_TERM,'','PHP_term2','select','');
		$op['term1'] = $arry2->select($TRADE_TERM,'','PHP_term1','select','');
		$op['lots']['lots_code']='Fxx-xxx-xxx';
		if (isset($PHP_status))
		{
			page_display($op,"003", $TPL_LOTS_SUB_ADD);	    	    
		}else{
			page_display($op,"003", $TPL_LOTS_ADD);	    	    
		}
		
		break;

//=======================================================
	case "do_lots_add":
	check_authority("003","add");
	
	#M11021501 增加 kind 的單位
	echo $PHP_cat1.':'.$PHP_cat2;
		if (!isset($PHP_cat1)) $PHP_cat1='';
		if (!isset($PHP_cat2)) $PHP_cat2='';
		$parm = array(	
						"lots_name"		=>	$PHP_lots_name,
						"des"					=>	$PHP_des,
						"comp"				=>	$PHP_comp,
						"unit"				=>	$PHP_unit,
						"specify"			=>	$PHP_specify,
						"custer"			=>	$PHP_cust,
						
						"cons"				=>	$PHP_cons,
						"width"				=>	$PHP_width,
						"weight"			=>	$PHP_weight,
						"memo"				=>	$PHP_memo,
						
						"vendor1"			=>	$PHP_vendor1,
						"price1"			=>	$PHP_price1,
						"unit1"				=>	$PHP_unit1,
						"currency1"		=>	$PHP_currency1,
						"term1"				=>	$PHP_term1,
						
						"vendor2"			=>	$PHP_vendor2,
						"price2"			=>	$PHP_price2,
						"unit2"				=>	$PHP_unit2,
						"currency2"		=>	$PHP_currency2,
						"term2"				=>	$PHP_term2,
						
						"vendor3"			=>	$PHP_vendor3,
						"price3"			=>	$PHP_price3,
						"unit3"				=>	$PHP_unit3,
						"currency3"		=>	$PHP_currency3,
						"term3"				=>	$PHP_term3,
						"cat1"				=>	$PHP_cat1,
						"cat2"				=>	$PHP_cat2,
				);

				$hend="F".$PHP_cat1.$PHP_cat2."-";
				$parm['lots_code'] = $lots->get_no($hend,'lots_code','lots');
				$op['lots'] = $parm;

		$f1 = $lots->add($parm);
		if ($f1) {  // 成功輸入資料時
			$message= "Append fabric record:".$parm['lots_code'];
					# 記錄使用者動態
			$log->log_add(0,"13A",$message);    
			$redir_str = 'primary.php?PHP_action=do_lots_search&PHP_msg='.$message.'&PHP_from=add';
			redirect_page($redir_str);
			break;
	
		}	else {  // 當沒有成功輸入新增user的欄位時....
			$parm['lots_code'] = 'Fxx-xxx-xxx';
			$op['lots'] = $parm;
		
		$op['unit1'] = $arry2->select($LOTS_PRICE_UNIT,$op['lots']['unit1'],'PHP_unit1','select','');  	
		$op['unit2'] = $arry2->select($LOTS_PRICE_UNIT,$op['lots']['unit2'],'PHP_unit2','select','');  	
		$op['unit3'] = $arry2->select($LOTS_PRICE_UNIT,$op['lots']['unit3'],'PHP_unit3','select','');  	
		
		$op['currency1'] = $arry2->select($CURRENCY,$op['lots']['currency1'],'PHP_currency1','select','');  	
		$op['currency2'] = $arry2->select($CURRENCY,$op['lots']['currency2'],'PHP_currency2','select','');  	
		$op['currency3'] = $arry2->select($CURRENCY,$op['lots']['currency3'],'PHP_currency3','select','');  	
		
		$op['term3'] = $arry2->select($TRADE_TERM,$PHP_term3,'PHP_term3','select','');
		$op['term2'] = $arry2->select($TRADE_TERM,$PHP_term2,'PHP_term2','select','');
		$op['term1'] = $arry2->select($TRADE_TERM,$PHP_term1,'PHP_term1','select','');

		$where_str=" order by cust_s_name"; //依cust_s_name排序
		$cust_def = $cust->get_fields('cust_init_name',$where_str);
		$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//取出客戶代號
		for ($i=0; $i< sizeof($cust_def); $i++)
		{
			$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//以 [客戶代號-客戶簡稱] 呈現
		}
		$op['cust_select'] =  $arry2->select($cust_value,$PHP_cust,'PHP_cust','select','',$cust_def_vue); 

			
			$op['msg'] = $lots->msg->get(2);
		if (isset($PHP_status))
		{
			page_display($op,"003", $TPL_LOTS_SUB_ADD);	    	    
		}else{
			page_display($op,"003", $TPL_LOTS_ADD);	    	    
		}
		break;

		}


//=======================================================
	case "lots_edit":
	
		check_authority("003","edit");
		$op['lots'] = $lots->get($PHP_id);
		if (!$op['lots']) {
			$op['msg'] = $lots->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		// 特別設定 :
		if($op['lots']['price1']== "0.00") { $op['lots']['price1']="";}
		if($op['lots']['price2']== "0.00") { $op['lots']['price2']="";}
		if($op['lots']['price3']== "0.00") { $op['lots']['price3']="";}
		
		$op['unit1'] = $arry2->select($LOTS_PRICE_UNIT,$op['lots']['unit1'],'PHP_unit1','select','');  	
		$op['unit2'] = $arry2->select($LOTS_PRICE_UNIT,$op['lots']['unit2'],'PHP_unit2','select','');  	
		$op['unit3'] = $arry2->select($LOTS_PRICE_UNIT,$op['lots']['unit3'],'PHP_unit3','select','');  	
		
		$op['currency1'] = $arry2->select($CURRENCY,$op['lots']['currency1'],'PHP_currency1','select','');  	
		$op['currency2'] = $arry2->select($CURRENCY,$op['lots']['currency2'],'PHP_currency2','select','');  	
		$op['currency3'] = $arry2->select($CURRENCY,$op['lots']['currency3'],'PHP_currency3','select','');  	
		
		$op['term3'] = $arry2->select($TRADE_TERM,$op['lots']['term3'],'PHP_term3','select','');
		$op['term2'] = $arry2->select($TRADE_TERM,$op['lots']['term2'],'PHP_term2','select','');
		$op['term1'] = $arry2->select($TRADE_TERM,$op['lots']['term1'],'PHP_term1','select','');
	
		$where_str=" order by cust_s_name"; //依cust_s_name排序
		$cust_def = $cust->get_fields('cust_init_name',$where_str);
		$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//取出客戶代號
		for ($i=0; $i< sizeof($cust_def); $i++)
		{
			$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//以 [客戶代號-客戶簡稱] 呈現
		}
		$op['cust_select'] =  $arry2->select($cust_value,$op['lots']['custer'],'PHP_cust','select','',$cust_def_vue); 
	
	
			
			$op['msg'] = $lots->msg->get(2);
		if ($GLOBALS['SCACHE']['ADMIN']['id'] == "SA" ) {   // 如果是 manager 進入時...
				$op['mag_flag'] = 1;
		}
		$op['back_str'] = '&a'; 
		page_display($op,"003", $TPL_LOTS_EDIT);
		break;
//=======================================================
	case "do_lots_edit":
	check_authority("003","edit");
	
	#M11021501 增加 kind 的單位
		$argv = array(	"id"	=>  $PHP_id,
						"lots_code"		=>	$PHP_lots_code,
						"lots_name"		=>	$PHP_lots_name,
						"des"					=>	$PHP_des,
						"comp"				=>	$PHP_comp,
						"unit"				=>	$PHP_unit,
						"specify"			=>	$PHP_specify,
						"custer"			=>	$PHP_cust,
						
						"cons"				=>	$PHP_cons,
						"width"				=>	$PHP_width,
						"weight"			=>	$PHP_weight,
						"memo"				=>	$PHP_memo,
						
						"vendor1"			=>	$PHP_vendor1,
						"price1"			=>	$PHP_price1,
						"unit1"				=>	$PHP_unit1,
						"currency1"		=>	$PHP_currency1,
						"term1"				=>	$PHP_term1,
						
						"vendor2"			=>	$PHP_vendor2,
						"price2"			=>	$PHP_price2,
						"unit2"				=>	$PHP_unit2,
						"currency2"		=>	$PHP_currency2,
						"term2"				=>	$PHP_term2,
						
						"vendor3"			=>	$PHP_vendor3,
						"price3"			=>	$PHP_price3,
						"unit3"				=>	$PHP_unit3,
						"currency3"		=>	$PHP_currency3,
						"term3"				=>	$PHP_term3,
						"user"				=>	$GLOBALS['SCACHE']['ADMIN']['login_id']
				);

		if (!$lots->edit($argv)) {
			$op['msg'] = $lots->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;	    
		}

					# 記錄使用者動態
		$message = "Success update fabric record:[$PHP_lots_code]‧";
		$log->log_add(0,"13E",$message);
		$redir_str = $PHP_SELF.'?PHP_action=do_lots_search&PHP_msg='.$message;
		redirect_page($redir_str);

		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "lots_del":

				// 必需要 manager login 才能真正的刪除 SUPLIER
		if(!$admin->is_power("003","del")  && !($GLOBALS['SCACHE']['ADMIN']['id'] == "SA")) {
			$op['msg'][] = "sorry! 您沒有這項權限!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$op['lots'] = $lots->get($PHP_id);
		
		if (!$lots->del($PHP_id)) {
			$op['msg'] = $lots->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;	    
		}
					# 記錄使用者動態

		$message = "Delete fabric:[".$op['lots']['lots_code']."] 。";
		$log->log_add(0,"13D",$message);
		$redir_str = $PHP_SELF.'?PHP_action=do_lots_search&PHP_msg='.$message;
		redirect_page($redir_str);
 	    
	break;	
	
	
//=======================================================
    case "acc":
 
		check_authority("004","view");
		$op['acc_name'] = $arry2->select($ACC,'','PHP_acc_name','select','');

//080725message增加		
	$note=$notify->search($GLOBALS['SCACHE']['ADMIN']['login_id'], "`read`=0");
	$op['max_notify'] = $note['max_no'];

		page_display($op,"004", $TPL_ACC_SEARCH);		    	    
		break;	
	
	
 //=======================================================
    case "do_acc_search":

		check_authority("004","view");

			if(isset($PHP_acc_code))
			{
				$SCH_ud = 1;
				if(!isset($SCH_sort))$SCH_sort = 'acc_code';
				$sch_parm = array();			
				$sch_parm = array(	"PHP_acc_code"		=>  $PHP_acc_code,
														"PHP_acc_name"		=>  $PHP_acc_name,
														"PHP_des"					=>	$PHP_des,
														"SCH_sort"				=>	$SCH_sort,
														"SCH_ud"					=>	$SCH_ud,
														"PHP_sr_startno"	=>	$PHP_sr_startno,
														"PHP_action"			=>	$PHP_action
					);
				
			}else if(isset($PHP_sr_startno)){
				$sch_parm['PHP_sr_startno'] = $PHP_sr_startno;
			}else if(isset($SCH_sort)){				
				if( $sch_parm['SCH_sort'] == $SCH_sort){$sch_parm['SCH_ud'] *= -1;}else{ $sch_parm['SCH_ud'] = 1;}
				$sch_parm['SCH_sort'] = $SCH_sort;
			}else if(isset($PHP_from) &&$PHP_from =='add'){					 
				$sch_parm = array(	"PHP_acc_code"		=>  '',
														"PHP_acc_name"		=>  '',
														"PHP_des"					=>	'',
														"SCH_sort"				=>	'acc_code',
														"SCH_ud"					=>	'',
														"PHP_sr_startno"	=>	'',
														"PHP_action"			=>	''
					);			
			}				 
		
		if (!$op = $acc->search(2)) {
			$op['msg']= $acc->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		
		$op['msg']= $acc->msg->get(2);
	
		if (isset($PHP_msg)) $op['msg'][]=$PHP_msg;
		page_display($op,"004", $TPL_ACC);		    	      	    		    	    
		break;
 	
 	
 	
//========================================================================================	
	case "acc_view":

		check_authority("004","view");
		if (isset($PHP_code))
		{
			$op['acc'] = $acc->get(0,$PHP_code);  //取出該筆記錄
		}else{
			$op['acc'] = $acc->get($PHP_id);  //取出該筆記錄
		}
		

		if (!$op['acc']) {
			$op['msg'] = $acc->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		// 特別設定 :
		if($op['acc']['price1']== "0.00") { $op['acc']['price1']="";}
		if($op['acc']['price2']== "0.00") { $op['acc']['price2']="";}
		if($op['acc']['price3']== "0.00") { $op['acc']['price3']="";}

		$op['msg'] = $acc->msg->get(2);
		$op['back_str'] = '&a';
	
		page_display($op,"004", $TPL_ACC_VIEW);	    	    
		break;
	
//=======================================================
    case "acc_add":

		check_authority("004","view");
			// create combo box for vendor fields.....
		
		$op['acc_name'] = $arry2->select($ACC,'','PHP_acc_name','select','');

		for ($i=1; $i<4; $i++)
		{
		$op['unit'.$i] = $arry2->select($ACC_PRICE_UNIT,'','PHP_unit'.$i,'select','');  	
		$op['currency'.$i] = $arry2->select($CURRENCY,'','PHP_currency'.$i,'select','');  	
		$op['term'.$i] = $arry2->select($TRADE_TERM,'','PHP_term'.$i,'select','');  	

		}

		$op['acc']['acc_code']="Axx-xxx-xxx";
	if (isset($PHP_status))
	{
		page_display($op,"004", $TPL_ACC_SUB_ADD);
	}else{
		page_display($op,"004", $TPL_ACC_ADD);		    	      	    
	}
	break;
		
//=======================================================
	case "do_acc_add":

		check_authority("004","add");
		if (!isset($PHP_size)) $PHP_size = 0;
		$parm = array(	
						"acc_name"			=>	$PHP_acc_name,
						"mile_code"			=>	$PHP_mile_code,
						"mile_name"			=>	$PHP_mile_name,
						"des"						=>	$PHP_des,
						"specify"				=>	$PHP_specify,
						"vendor1"				=>	$PHP_vendor1,
						"price1"				=>	$PHP_price1,
						"unit1"					=>	$PHP_unit1,
						"currency1"			=>	$PHP_currency1,
						"term1"					=>	$PHP_term1,
						"vendor2"				=>	$PHP_vendor2,
						"price2"				=>	$PHP_price2,
						"unit2"					=>	$PHP_unit2,
						"currency2"			=>	$PHP_currency2,
						"term2"					=>	$PHP_term2,
						"vendor3"				=>	$PHP_vendor3,
						"price3"				=>	$PHP_price3,
						"unit3"					=>	$PHP_unit3,
						"currency3"			=>	$PHP_currency3,
						"term3"					=>	$PHP_term3,
						"size_mk"				=>	$PHP_size
				);
				$tmp =00;

			foreach( $ACC_key as $key => $value)
			{
				if ($PHP_acc_name == $value)
				{
						$tmp = $key;
						break;
				}
			}

				$hend="A".$tmp."-";
				$parm['acc_code'] = $acc->get_no($hend,'acc_code','acc');
				$op['acc'] = $parm;



		$f1 = $acc->add($parm);
		if ($f1) {  // 成功輸入資料時
			
			$message= "Append acc record:".$parm['acc_code'];						
			$log->log_add(0,"14A",$message);# 記錄使用者動態
			unset($parm);
			if (isset($PHP_status))
			{
				$op['acc'] = $acc->get($f1);  //取出該筆記錄
				$op['msg'][] = $message;
				page_display($op,"004", $TPL_ACC_SUB_VIEW);
			}else{
				$redir_str = 'primary.php?PHP_action=do_acc_search&PHP_msg='.$message.'&PHP_from=add';
				redirect_page($redir_str);	    	      	    
			}			
			break;

		}	else {  // 當沒有成功輸入新增user的欄位時....		
			$op['acc'] = $parm;
			$op['acc']['acc_code']="Axx-xxx-xxx";
		$op['acc_name'] = $arry2->select($ACC,$PHP_acc_name,'PHP_acc_name','select','');

		$op['unit1'] = $arry2->select($ACC_PRICE_UNIT,$op['acc']['unit1'],'PHP_unit1','select','');  	
		$op['unit2'] = $arry2->select($ACC_PRICE_UNIT,$op['acc']['unit2'],'PHP_unit2','select','');  	
		$op['unit3'] = $arry2->select($ACC_PRICE_UNIT,$op['acc']['unit3'],'PHP_unit3','select','');  	
		
		$op['currency1'] = $arry2->select($CURRENCY,$op['acc']['currency1'],'PHP_currency1','select','');  	
		$op['currency2'] = $arry2->select($CURRENCY,$op['acc']['currency2'],'PHP_currency2','select','');  	
		$op['currency3'] = $arry2->select($CURRENCY,$op['acc']['currency3'],'PHP_currency3','select','');  	

		$op['term3'] = $arry2->select($TRADE_TERM,$PHP_term3,'PHP_term3','select','');
		$op['term2'] = $arry2->select($TRADE_TERM,$PHP_term2,'PHP_term2','select','');
		$op['term1'] = $arry2->select($TRADE_TERM,$PHP_term1,'PHP_term1','select','');

						
			$op['msg'] = $acc->msg->get(2);

	if (isset($PHP_status))
	{
		
		page_display($op,"004", $TPL_ACC_SUB_ADD);
	}else{
		page_display($op,"004", $TPL_ACC_ADD);		    	      	    
	}		
	break;

		}		
		

//=======================================================
	case "acc_edit":

		check_authority("004","edit");
		$op['acc'] = $acc->get($PHP_id);
		if (!$op['acc']) {
			$op['msg'] = $acc->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}
		// 特別設定 :
		if($op['acc']['price1']== "0.00") { $op['acc']['price1']="";}
		if($op['acc']['price2']== "0.00") { $op['acc']['price2']="";}
		if($op['acc']['price3']== "0.00") { $op['acc']['price3']="";}

		$op['acc_name'] = $arry2->select($ACC,$op['acc']['acc_name'],'PHP_acc_name','select','');

		$op['unit1'] = $arry2->select($ACC_PRICE_UNIT,$op['acc']['unit1'],'PHP_unit1','select','');  	
		$op['unit2'] = $arry2->select($ACC_PRICE_UNIT,$op['acc']['unit2'],'PHP_unit2','select','');  	
		$op['unit3'] = $arry2->select($ACC_PRICE_UNIT,$op['acc']['unit3'],'PHP_unit3','select','');  	
		
		$op['currency1'] = $arry2->select($CURRENCY,$op['acc']['currency1'],'PHP_currency1','select','');  	
		$op['currency2'] = $arry2->select($CURRENCY,$op['acc']['currency2'],'PHP_currency2','select','');  	
		$op['currency3'] = $arry2->select($CURRENCY,$op['acc']['currency3'],'PHP_currency3','select','');  	

		$op['term3'] = $arry2->select($TRADE_TERM,$op['acc']['term3'],'PHP_term3','select','');
		$op['term2'] = $arry2->select($TRADE_TERM,$op['acc']['term2'],'PHP_term2','select','');
		$op['term1'] = $arry2->select($TRADE_TERM,$op['acc']['term1'],'PHP_term1','select','');

						
			$op['msg'] = $supl->msg->get(2);
		$op['back_str'] = '&b';
 		if ($GLOBALS['SCACHE']['ADMIN']['id'] == "SA" ) {   // 如果是 manager 進入時...
				$op['mag_flag'] = 1;
		}

		page_display($op,"004",$TPL_ACC_EDIT); 
		break;

//=======================================================
	case "do_acc_edit":

		check_authority("004","edit");
		if (!isset($PHP_size))$PHP_size=0;
		$argv = array(	"id"	=>  $PHP_id,
						"acc_code"		=>	$PHP_acc_code,
						"acc_name"		=>	$PHP_acc_name,
						"des"					=>	$PHP_des,
						"specify"			=>	$PHP_specify,
						"mile_code"		=>	$PHP_mile_code,
						"mile_name"		=>	$PHP_mile_name,
						"vendor1"			=>	$PHP_vendor1,
						"price1"			=>	$PHP_price1,
						"unit1"				=>	$PHP_unit1,
						"currency1"		=>	$PHP_currency1,
						"term1"				=>	$PHP_term1,
						"vendor2"			=>	$PHP_vendor2,
						"price2"			=>	$PHP_price2,
						"unit2"				=>	$PHP_unit2,
						"currency2"		=>	$PHP_currency2,
						"term2"				=>	$PHP_term2,
						"vendor3"			=>	$PHP_vendor3,
						"price3"			=>	$PHP_price3,
						"unit3"				=>	$PHP_unit3,
						"currency3"		=>	$PHP_currency3,
						"term3"				=>	$PHP_term3,
						"user"				=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
						"size_mk"			=>	$PHP_size,
				);

		if (!$acc->edit($argv)) {
			$op['msg'] = $acc->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;	    
		}

					# 記錄使用者動態
		$message = "Success update acc record:[$PHP_acc_code]‧";
		$log->log_add(0,"14E",$message);
		$redir_str ='primary.php?PHP_action=do_acc_search&PHP_msg='.$message;
		redirect_page($redir_str);

		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "acc_del":

				// 必需要 manager login 才能真正的刪除 SUPLIER
		if(!$admin->is_power("004","del") &&  !($GLOBALS['SCACHE']['ADMIN']['id'] == "SA" )) { 
			$op['msg'][] = "sorry! 您沒有這項權限!";
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;
		}

		$op['acc'] = $acc->get($PHP_id);
		
		if (!$acc->del($PHP_id)) {
			$op['msg'] = $acc->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;	    
		}
					# 記錄使用者動態

		$message = "Delete acc:[".$op['acc']['acc_code']."] record。";
		$log->log_add(0,"14D",$message);
		$redir_str ='primary.php?PHP_action=do_acc_search&PHP_msg='.$message;
		redirect_page($redir_str);
	    	    
	break;

//-------------------------------------------------------------------------

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	#	供應商->銀行帳戶刪除
	#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_bank_del":

		if (!$f1 = $supl->bank_del($PHP_bank_id)) {
			$op['msg'] = $acc->msg->get(2);
			$layout->assign($op);
			$layout->display($TPL_ERROR);  		    
			break;	    
		}
		
		$message = "Success delete bank code:[$PHP_vndr_no][$PHP_bank_code]";
		$log->log_add(0,"12E",$message);
		
		echo $f1;
	    	    
	break;
	
}   // end case ---------

?>
