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
#			index.php  主程式
#		for Carnival SCM [Sample]  management
#			Jack Yang     2004/11/10
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//		job 39  [生產製造][生產產能]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

require_once "config.php";
require_once "config.admin.php";

$PHP_SELF = $_SERVER['PHP_SELF'];

$perm = $GLOBALS['power'];


require_once "init.object.php";
/*
include_once($config['root_dir']."/lib/class.notify.php");
$notify = new NOTIFY();
if (!$notify->init($mysql,"log")) { print "error!! cannot initialize database for LOG class"; exit; }
*/

$op = array();

$dept_ary = array( 
    1	=>	'LY',
    2	=>	'HJ',
    4	=>	'CF',
    8	=>	'PM',
    16	=>	'PO'
);

$sand_ary['HJ'][0] = 'kihwen';
$sand_ary['HJ'][1] = 'simon';
$sand_ary['LY'][0] = 'henry';
$sand_ary['LY'][1] = 'martin-kao';
$sand_ary['CF'][0] = 'andyku';
$sand_ary['PM'][0] = 'jerry';

session_register('FTY_CM');
$para_cm = $para->get(0,'hj-cm');
$FTY_CM['HJ'] = $para_cm['set_value'];
$para_cm = $para->get(0,'ly-cm');
$FTY_CM['LY'] = $para_cm['set_value'];
$para_cm = $para->get(0,'cf-cm');
$FTY_CM['CF'] = $para_cm['set_value'];
$FTY_CM['SC'] = 0;

// if( $GLOBALS['SCACHE']['ADMIN']['name'] == 'mode')
// echo $PHP_action.'<br>';
switch ($PHP_action) {
//=======================================================
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# job 101 訂 單
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "exception":
		check_authority('069',"view");
		$where_str = $manager = $manager_v = $dept_id = '';
		$dept_ary = array();
		$sales_dept_ary = get_sales_dept(); // 取出 業務的部門 [不含K0] ------
		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];   // 判定進入身份的指標
		$team = $GLOBALS['SCACHE']['ADMIN']['team_id'];		// 判定進入身份的Team
		if($team == 'MD')
		{
			$dept_id = $user_dept;
			$where_str = " WHERE dept = '".$dept_id."' ";
		}

		if (!$dept_id) {    // 當不是業務部人[也不含 K0 的人 ]進入時
			$manager = 1;
			//業務部門 select選單
			$dept_ary = $arry2->select($sales_dept_ary,$GLOBALS['SCACHE']['ADMIN']['dept'],"PHP_dept_code","select","get_dept(this)");  
			$dept = $arry2->select($sales_dept_ary,$GLOBALS['SCACHE']['ADMIN']['dept'],"PHP_dept","select","");  
		}

		$op['manager_flag'] = $manager;
		$op['dept_id'] = $dept_id;
		$op['dept_ary'] = $dept_ary;
		$op['dept'] = $dept;

		$op['msg'] = $order->msg->get(2);
		// creat cust combo box
		// 取出 客戶代號
		$where_str=" order by cust_s_name"; //依cust_s_name排序
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


//080725message增加		
	$note=$notify->search($GLOBALS['SCACHE']['ADMIN']['login_id'], "`read`=0");
	$op['max_notify'] = $note['max_no'];

			// creat factory combo box
		  	
		page_display($op, '069', $TPL_EXCEPTION);			    	    
		break;
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "pre_order_add":		JOB 15A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "exc_add":
check_authority('069',"add");

$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];   // 判定進入身份的指標


// 取出選項資料 及傳入之參數
$op['msg']= $order->msg->get(2);
$op['cust_id'] = $PHP_cust;
$op['dept_id']	 = $PHP_dept_code;
$op['date'] = $TODAY;
// 2005/07/30 加入 由 menu進入新增後的 back page 只向 全部列表
page_display($op, '069',$TPL_EXC_ADD);		    	    
break;
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "pre_order_add":		JOB 15A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "exc_ord_add":
		check_authority('069',"add");
		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];   // 判定進入身份的指標


		// 取出選項資料 及傳入之參數
		$op['msg']= $order->msg->get(2);
		$op['cust_id'] = $PHP_cust;
		$op['dept_id']	 = $PHP_dept_code;
		$op['exc']['ord_num'] = $PHP_order.',';
		$op['rec']['qty'] = $PHP_qty;
		$op['ord_del'] = 1;
		$op['date'] = $TODAY;
		// 2005/07/30 加入 由 menu進入新增後的 back page 只向 全部列表
page_display($op, '069',$TPL_EXC_ADD);		    	    
	break;	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#			 job 069  訂 單 SEARCH --開新窗
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "order_search":
check_authority('065',"view");
if(isset($PHP_fty) && $PHP_fty)
{
    $op['factory'] = $arry2->select($FACTORY,$PHP_fty,'PHP_factory','select','');
    $op['fty_non'] = 1;
}else{
    $op['factory'] = $arry2->select($FACTORY,'','PHP_factory','select','');
    $op['fty_non'] = 1;
}
    
if($PHP_cust == '')
{
    $where_str=" order by cust_s_name"; //依cust_s_name排序
    $cust_def = $cust->get_fields('cust_init_name',$where_str);
    $cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//取出客戶代號
    for ($i=0; $i< sizeof($cust_def); $i++) $cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//以 [客戶代號-客戶簡稱] 呈現

    $op['cust_select'] =  $arry2->select($cust_value,'','PHP_cust','select',$cust_def_vue); 

}else{
    $op['cust_select'] = '<b>'.$PHP_cust.'</b><input type="hidden" name="PHP_cust" value="'.$PHP_cust.'">';
}
$op['dept'] = $PHP_dept;

    
page_display($op, '065', $TPL_ORDER_S_LIST);			    	    
break;	
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#			 job 069  訂 單 SEARCH --開新窗SEARCH
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    case "do_order_search":
		check_authority('069',"view");
		if(!isset($PHP_finish))$PHP_finish=0;
		if(!isset($PHP_ship))$PHP_ship=0;
		$parm = array(	"dept"		=>  $PHP_dept_code,	
										"order_num"	=>  $PHP_order_num,
										"cust"		=>	$PHP_cust,		
										"ref"		=>	$PHP_ref,
										"factory"	=>	$PHP_factory,
										"finish" => $PHP_finish,
										"ship" => $PHP_ship
									);
					//可利用PHP_dept_code判定是否 業務部門進入
			if (!$op = $order->ord_search(4)) {  
				$op['msg']= $order->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			for($i=0; $i<sizeof($op['sorder']);  $i++)
			{
				$op['sorder'][$i]['saw_qty'] = $order->get_field_value('qty_done','',$op['sorder'][$i]['order_num'],'pdtion');				
			}

		$op['factory'] = $arry2->select($FACTORY,$PHP_factory,'PHP_factory','select','');
		$op['fty_non'] = 1;


		if($PHP_cust == '')
		{
			$where_str=" order by cust_s_name"; //依cust_s_name排序
			$cust_def = $cust->get_fields('cust_init_name',$where_str);
			$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//取出客戶代號
			for ($i=0; $i< sizeof($cust_def); $i++) $cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//以 [客戶代號-客戶簡稱] 呈現

			$op['cust_select'] =  $arry2->select($cust_value,'','PHP_cust','select',$cust_def_vue); 

		}else{
			$op['cust_select'] = '<b>'.$PHP_cust.'</b><input type = hidden name=PHP_cust value='.$PHP_cust.'>';
		}			
	$op['dept'] = $PHP_dept_code;

				$op['msg']= $order->msg->get(2);
				$op['cgi']= $parm;

			page_display($op, '069', $TPL_ORDER_S_LIST);
		break;		
	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "pre_order_search":		JOB 15A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "exc_search":
check_authority('069',"view");

//可利用PHP_dept_code判定是否 業務部門進入
if (!$op = $except->search(1)) {  
    $op['msg']= $except->msg->get(2);
    $layout->assign($op);
    $layout->display($TPL_ERROR);  		    
    break;
}

for($i=0; $i<sizeof($op['exc']); $i++)
{
    $op['exc'][$i]['ex_status'] = $except->get_status_name($op['exc'][$i]['id'],$op['exc'][$i]['oth_static']);
    foreach($dept_ary as $key => $value)
    {
        if($op['exc'][$i]['oth_dept'] & $key)
        {						
            
            $where_str = "exc_id = ".$op['exc'][$i]['id']." AND comm_dept = '".$value."'";
            $comm_chk = $except->get_comm_field('Comm', $where_str);
            if($comm_chk)
            {
                $op['exc'][$i][$value] = 2;
            }else{
                $op['exc'][$i][$value] = 1;
            }
        }else{
            $op['exc'][$i][$value] = 0;
        }
    }
}


$op['msg']= $except->msg->get(2);
if(isset($PHP_msg))$op['msg'][] = $PHP_msg;
$op['back_str'] = "&SCH_ord=".$SCH_ord."&PHP_cust=".$PHP_cust."&PHP_dept=".$PHP_dept;

page_display($op, '069', $TPL_EXC_LIST);
break;
		
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "exc_show":		JOB 15A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "exc_show":

		check_authority('069',"view");

		// 取出該筆 order 資料 ------------------------------------------------- 
			if (!$op=$except->get($PHP_id)) {
				$op['msg']= $except->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
			
		$op['exc']['exc_des']=	str_replace( '刪除己核可之採購單務必追回己發出之採購合約', "<font color=red>刪除己核可之採購單務必追回己發出之採購合約</font>", $op['exc']['exc_des'] );
// print_r($op);
		$op['back_str'] = "exception.php?PHP_action=exc_search&PHP_sr_startno=".$PHP_sr_startno."&SCH_ord=".$SCH_ord."&PHP_cust=".$PHP_cust."&PHP_dept=".$PHP_dept;
		$op['SYS_DEPT'] = $SYS_DEPT;
		page_display($op, '069', $TPL_EXC_SHOW);				
		break;
			


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "exc_edit":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "exc_edit":

		check_authority('069',"add");

		// 取出該筆 order 資料 ------------------------------------------------- 
			if (!$op=$except->get($PHP_id)) {
				$op['msg']= $except->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

				foreach($dept_ary as $key => $value)
				{
					$op['exc'][$value] = 0;
					if($op['exc']['oth_dept'] & $key)
					{			
						$op['exc'][$value] = 1;
						$where_str = "exc_id = ".$PHP_id." AND comm_dept = '".$value."'";
						$comm_chk = $except->get_comm_field('Comm', $where_str);
						if($comm_chk)$op['exc'][$value] = 2;
						
					}
				}

		$op['exc']['exc_des'] = str_replace("<br>",chr(13).chr(10), $op['exc']['exc_des'] );
		$op['exc']['sys_des'] = str_replace("<br>",chr(13).chr(10), $op['exc']['sys_des'] );
		
		$op['back_str'] = $PHP_back_str;
		page_display($op, '069', $TPL_EXC_EDIT);				
		break;
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "section_update":		JOB 15E PHP_team
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_exc_edit":
		check_authority('069',"add");	
		$PHP_oth_dept = 0;
		if(isset($PHP_team))
		{
			foreach($PHP_team as $i => $tm_value)
			{
				$PHP_oth_dept+=$PHP_team[$i];
			}
		}

		$PHP_exc_des = str_replace("'","\'",$PHP_exc_des);		
		for ($i=0; $i< sizeof($GLOBALS['chin_error_word']); $i++)
		{
			$PHP_exc_des = str_replace($GLOBALS['chin_error_word'][$i],$GLOBALS['chin_error_word'][$i]."\\",$PHP_exc_des);
		}

		if(strstr($PHP_exc_des,'&#'))  //轉換繁簡體
		{
			$PHP_exc_des = $ch_cov->check_cov($PHP_exc_des);
		}	
		
		$parm = array(	
            "exc_des"		=>	$PHP_exc_des,
            'oth_dept'		=>	$PHP_oth_dept,
            'oth_static'	=>	$PHP_oth_static,
            'id'			=>	$PHP_id
        );

		$f1 = $except->edit($parm);  

		if(isset($PHP_state))
		{

			foreach($PHP_state as $key => $value)
			{
				
				$parm = array(
                    "exc_id"			=>	$PHP_id,
                    "state"				=>	$value,
                    "org_rec"			=>	$PHP_org[$key],										
                    "new_rec"			=>	$PHP_new[$key]										
                );
				$f2 = $except->add_static($parm);
			}
		}


		if(isset($PHP_e_org))
		{
			foreach($PHP_e_org as $key => $value)
			{
				$parm = array(												
                    "org_rec"			=>	$value,										
                    "new_rec"			=>	$PHP_e_new[$key],
                    "id"				=>	$key					
                );
				$f2 = $except->edit_state($parm);
			}
		}
/*
	$messg  = '<br><br>異常報告 : <a href="exception.php?PHP_action=exc_show&PHP_id='.$PHP_id.'&PHP_sr_startno=0&SCH_ord=&PHP_cust=">'.$PHP_order.'</a><br>';
	$messg .= '請點擊訂單編號連結異常報告<br><br>';
	$messg = $notify->cov_html($messg);  //message add
*/	
// print_r($dept_ary);
			foreach($dept_ary as $key => $value)
			{
				$new_dept = $PHP_oth_dept & $key;
				$old_dept = $PHP_org_dept & $key;
				if($new_dept && !$old_dept)
				{
					$parm = array(
                        "exc_id"			=>	$PHP_id,
                        "comm_dept"		    =>	$value,
                        "comm"				=>	'',																			
                    );
					$f2 = $except->add_comm($parm);
				
				}
				if($PHP_status == 1 || ($new_dept && !$old_dept))
				{
					//傳送異常報告加入意見訊息
					 $messg  = '<a href="exception.php?PHP_action=exc_show&PHP_id='.$PHP_id.'&PHP_sr_startno=0&SCH_ord=&PHP_cust=">'.$PHP_order.'</a>';
					 $notify->system_msg_send('10-5-A',$value,$PHP_order,$messg);
				}

				if(!$new_dept && $old_dept)
				{
					$where_str = "exc_id = ".$PHP_id." AND comm_dept = '".$value."'";
					$f4=$except->comm_del($where_str);
				}
			}
			if($PHP_status == 1) $except->update_fields('status', '0', $PHP_id);
/*
		if(isset($PHP_team))
		{


			for($i=0; $i < 4; $i++)
			{
				if(isset($PHP_team[$i]))
				{
					
					$parm = array(	"exc_id"			=>	$PHP_id,
													"comm_dept"		=>	$dept_ary[$PHP_team[$i]],
													"comm"				=>	'',																			
									);
					$f2 = $except->add_comm($parm);
					$send_dept = $dept_ary[$PHP_team[$i]];
					for($j=0; $j< sizeof($sand_ary[$send_dept]); $j++)
					{
						$parm2 = array(	"tuser"			=>  $sand_ary[$send_dept][$j],
													  "title"			=>	'您有異常報告 ['.$PHP_order.'] 需加入意見',
														"msg"				=>	$messg,	
														"fuser"			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
														"send_date"	=>	$TODAY,
													);
						$f3=$notify->add($parm2);  //message add						
					
					}			
				
				
				
				}
			}
		}

*/
		# 記錄使用者動態

	
		// 取出該筆 order 資料 ------------------------------------------------- 
			if (!$op=$except->get($PHP_id)) {
				$op['msg']= $pre_ord->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

		$message = "EDIT Exception On Order: [".$op['exc']['ord_num']."]";
		$log->log_add(0,"069E",$message);
		$op['msg'][] = $message;

		$op['exc']['exc_des']=	str_replace( '刪除己核可之採購單務必追回己發出之採購合約', "<font color=red>刪除己核可之採購單務必追回己發出之採購合約</font>", $op['exc']['exc_des'] );

		$op['back_str'] = $PHP_back_str;
		$op['SYS_DEPT'] = $SYS_DEPT;
		page_display($op, '069', $TPL_EXC_SHOW);				
		break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "exc_comm_edit":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "exc_comm_edit":

		check_authority('069',"edit");

		// 取出該筆 order 資料 ------------------------------------------------- 
			if (!$op=$except->get($PHP_id)) {
				$op['msg']= $except->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

		$op['dept'] = $GLOBALS['SCACHE']['ADMIN']['dept'];

		if(isset($op['comm']))
		{
			for($i=0; $i<sizeof($op['comm']); $i++)
				$op['comm'][$i]['Comm'] = str_replace("<br>",chr(13).chr(10), $op['comm'][$i]['Comm'] );
		}
//判定是否由業務apv	  <-指物料超量採購
		$op['apv_mk'] = 0;	
		$ord_nums = explode(',',$op['exc']['ord_num']);
		$ord_dept = $order->get_field_value('dept','',$ord_nums[0]);

		if($op['dept'] == $ord_dept)
		{

			for($i=0; $i<sizeof($op['state']); $i++)
			{
				if($op['state'][$i]['state'] == 6) $op['apv_mk'] = 1;
			}
		}
		$op['back_str'] = $PHP_back_str;
		$op['SYS_DEPT'] = $SYS_DEPT;
		page_display($op, '069', $TPL_EXC_COMM);				
		break;		
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case "do_exc_comm_edit":		JOB 069E
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "do_exc_comm_edit":
		check_authority('069',"edit");	
		
		$PHP_comm = str_replace("'","\'",$PHP_comm);		
		for ($i=0; $i< sizeof($GLOBALS['chin_error_word']); $i++)
		{
			$PHP_comm = str_replace($GLOBALS['chin_error_word'][$i],$GLOBALS['chin_error_word'][$i]."\\",$PHP_comm);
		}

		if(strstr($PHP_comm,'&#'))  //轉換繁簡體
		{
			$PHP_comm = $ch_cov->check_cov($PHP_comm);
		}	

		$parm = array(	
										'Comm'			=>	$PHP_comm,
										'id'				=>	$PHP_comm_id,
										'comm_user'	=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
										'comm_date'	=>	$TODAY,
		);

		$f1 = $except->edit_comm($parm);


		# 記錄使用者動態

	
		// 取出該筆 order 資料 ------------------------------------------------- 
			if (!$op=$except->get($PHP_id)) {
				$op['msg']= $pre_ord->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

	//當會辦意見輸入完成後傳送訊息給建立異常報告者
	if($op['com_mk'] == 1)
	{
		$messg  = '<br><br>異常報告 : <a href="exception.php?PHP_action=exc_show&PHP_id='.$PHP_id.'&PHP_sr_startno=0&SCH_ord=&PHP_cust=">'.$op['exc']['ord_num'].'</a><br>';
		$messg .= '會辦單位意見己加入完成 ， 請速SUBMIT ， 同時列印出來 呈上級核示<br><br>';
		$messg = $notify->cov_html($messg);  //message add

		$parm2 = array(	"tuser"		=>  $op['exc']['exc_user_id'],
						"title"		=>	'異常報告 ['.$op['exc']['ord_num'].'] 己完成會辦單位意見加入',
						"msg"		=>	$messg,	
						"fuser"		=>	'System MSG',
						"send_date"	=>	$TODAY,
					);
		$f3=$notify->add($parm2);  //message add
	}

	$message = "ADD ".$PHP_comm_dept." Comment On Order: [".$op['exc']['ord_num']."]";
	$log->log_add(0,"069C",$message);
	$op['msg'][] = $message;

	$op['exc']['exc_des']=	str_replace( '刪除己核可之採購單務必追回己發出之採購合約', "<font color=red>刪除己核可之採購單務必追回己發出之採購合約</font>", $op['exc']['exc_des'] );

	$op['back_str'] = $PHP_back_str;
	$op['SYS_DEPT'] = $SYS_DEPT;
	page_display($op, '069', $TPL_EXC_SHOW);				
	break;
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_exc_comm_apv":		JOB 069E
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_exc_comm_apv":
check_authority('069',"edit");	

$PHP_comm = str_replace("'","\'",$PHP_comm);		
for ($i=0; $i< sizeof($GLOBALS['chin_error_word']); $i++)
{
	$PHP_comm = str_replace($GLOBALS['chin_error_word'][$i],$GLOBALS['chin_error_word'][$i]."\\",$PHP_comm);
}

if(strstr($PHP_comm,'&#'))  //轉換繁簡體
{
	$PHP_comm = $ch_cov->check_cov($PHP_comm);
}	

$parm = array(	
	'Comm'		=>	$PHP_comm,
	'id'		=>	$PHP_comm_id,
	'comm_user'	=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
	'comm_date'	=>	$TODAY,
);

$f1 = $except->edit_comm($parm);


//核可異常報告
$parm = array( 'id' => $PHP_id , 'apv_user' =>	'' , 'apv_date' => $TODAY );
$f1 = $except->send_apv($parm);

# 記錄使用者動態

// 取出該筆 order 資料 ------------------------------------------------- 
if (!$op=$except->get($PHP_id)) {
	$op['msg']= $pre_ord->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}

//當會辦意見輸入完成後傳送訊息給建立異常報告者
if($op['exc']['po_num'])
{
	$messg  = '<br>採購單 : ['.$op['exc']['po_num'].']的 異常報告 : <a href="exception.php?PHP_action=exc_show&PHP_id='.$PHP_id.'&PHP_sr_startno=0&SCH_ord=&PHP_cust=">'.$op['exc']['ex_num'].'</a><br>';
	$messg .= '己核可(APVD) ， 請速針對採購單進行接續工作<br>';
	$messg = $notify->cov_html($messg);  //message add

	$parm2 = array(
		"tuser" 	=>  $op['exc']['exc_user_id'],
		"title"		=>	'PO : ['.$op['exc']['po_num'].']的異常報告 ['.$op['exc']['ex_num'].'] 己核可',
		"msg"		=>	$messg,	
		"fuser"		=>	'System MSG',
		"send_date"	=>	$TODAY,
	);
	
	$f3=$notify->add($parm2);  //message add
}


if($op['exc']['act_des']) 
{
	$act_des = explode('|',$op['exc']['act_des']);		
	if($act_des[0] == 'PREPO')
	{
		$f1=$apply->update_fields_id('pp_mark','2', $act_des[3], 'ap_special');			
		$parm = array($act_des[2], 'ap_mark', $act_des[4], $act_des[1]);
		$f1=$bom->update_field($parm);
		$parm = array($act_des[2], 'pp_mark', '1', $act_des[1]);
		$f1=$bom->update_field($parm);	
		$PHP_msg = "Suceess Match Pre-Purchase PO#: ".$act_des[4];
		$log->log_add(0,"51M",$PHP_msg);				
	}
}

$message = "ADD ".$PHP_comm_dept." Comment On Order: [".$op['exc']['ord_num']."]";
$log->log_add(0,"069C",$message);
$op['msg'][] = $message;

$message = "Approval exceptional On Order: [".$op['exc']['ord_num']."]";
$log->log_add(0,"069S",$message);
$op['msg'][] = $message;


$op['exc']['exc_des']=	str_replace( '刪除己核可之採購單務必追回己發出之採購合約', "<font color=red>刪除己核可之採購單務必追回己發出之採購合約</font>", $op['exc']['exc_des'] );


$op['back_str'] = $PHP_back_str;
page_display($op, '069', $TPL_EXC_SHOW);				
break;
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "pre_list_qty":		JOB 15E
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "exc_print":

		check_authority('069',"view");

		// 取出該筆 order 資料 ------------------------------------------------- 
			if (!$op=$except->get($PHP_id)) {
				$op['msg']= $except->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}


//---------------------------------- 資料庫作業結束 ------------------------------

include_once($config['root_dir']."/lib/class.pdf_exc.php");

$print_title = $SYS_DEPT." 異常報告";

$print_title2 = "報告日期:".$op['exc']['exc_date'];
$creator = $op['exc']['exc_user'];

if(!isset($op['ord_rec']))$op['ord_rec'] = '';
$ord_num = $op['exc']['ord_num'];
$title_ary = $op['ord_rec'];

$title_ary['ord_num'] = $op['exc']['ord_num'];
$title_ary['cust'] = $op['exc']['cust_iname'];

$title_ary['ord_nums'] = $op['exc']['ord_nums'];
$title_ary['po_num'] = $op['exc']['po_num'];
$title_ary['ex_num'] = $op['exc']['ex_num'];

$x = 0;

$pdf=new PDF_EXC();
$pdf->AddBig5Font();

$pdf->Open();
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
$pdf->SetCreator('angel');

$pdf->SetFont('Big5','',12);
$pdf->cell(190,7,'異常狀態',1,0,'C');
$pdf->ln();

if(isset($op['state']))
{
	for($i=0; $i<sizeof($op['state']); $i++)
	{
		$pdf->state($op['state'][$i]);
	}
$x = $i++;	
}



if($op['exc']['oth_exc'] == 1)
{
	$pdf->fld_title('單價改變原因');
}else{
	$pdf->fld_title('發生原因及預估損失');
}

$r = $pdf->exc_value($op['exc']['exc_des'],$op['exc']['sys_des']);
$x+=$r;

if(isset($op['comm']))
{
$pdf->fld_title('會辦單位意見');
$x++;
for($i=0; $i<sizeof($op['comm']); $i++)
{
		if($x > 31)
		{
			$pdf->Cell(190,3,'','RLB',0,'L');
			$pdf->AddPage();		
			$pdf->fld_title('會辦單位意見');	
			$x = 2;	
		}
		$x = $pdf->comm_value($op['comm'][$i],$x);
}
}
		if($x > 36)
		{
			$pdf->Cell(190,3,'','RLB',0,'L');
			$pdf->AddPage();		
			$pdf->fld_title('會辦單位意見');
			$x = 2;	
		}
		
if(isset($op['logs']))		
{
	for($i=0; $i<sizeof($op['logs']); $i++)
	{
		if($op['logs'][$i]['item'] <> 'REJECT' && !strstr($op['logs'][$i]['des'],'APV REJECT : for'))
		{

			if($x > 31)
			{
				$pdf->Cell(190,3,'','RLB',0,'L');
				$pdf->AddPage();		
				$pdf->fld_title('會辦單位意見');
				$x = 2;	
			}

			if($op['logs'][$i]['item'] == 'SA') $op['logs'][$i]['item'] = '生產企劃室';
			$parm = array('dept_name'	=>	$op['logs'][$i]['item'],
										'comm_user'	=>	$op['logs'][$i]['user'],
										'comm_date'	=>	$op['logs'][$i]['k_date'],
										'Comm'			=>	$op['logs'][$i]['des']
										);
			$x = $pdf->comm_value($parm,$x);
		}
	}
}

		if($x > 31)
		{
			$pdf->AddPage();			
			$x = 1;	
		}

//簽名
$pdf->SetY(248);
$pdf->cell(50,4,'總經理 : ',0,0,'L');
$pdf->cell(50,4,'副總經理 : '.$op['exc']['apv_user'],0,0,'L');
$pdf->cell(50,4,'主管 : ',0,0,'L');
$pdf->cell(40,4,'承辦單位 : '.$SYS_DEPT,0,0,'L');
$pdf->ln();
$pdf->ln();
$pdf->cell(50,4,'',0,0,'L');
$pdf->cell(50,4,'(責任歸屬裁定)',0,0,'L');
$pdf->cell(50,4,'',0,0,'L');
$pdf->cell(40,4,'承辦人 : '.$op['exc']['exc_user'],0,0,'L');

//備註
$pdf->SetFont('Big5','',7);
$pdf->ln();
$pdf->ln();
$pdf->cell(190,5,'* 本表應於事發2日內填寫，經相關單位會辦見後送交總經理室，呈總經理核簽，以為日後請款附件',0,0,'L');
$pdf->ln();
$pdf->SetY(267);

$pdf->cell(190,5,'* 生產企劃室應整合異常報告資料後於每月經營會議中匯告',0,0,'L');
$pdf->SetY(270);
$pdf->cell(190,5,'* 本表欄位不敷使用時，請自行黏貼附件填寫',0,0,'L');

$name=$op['exc']['ord_num'].'_exc.pdf';
$pdf->Output($name,'D');
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "section_update":		JOB 15E
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "exc_submit":
		check_authority('069',"add");	
		


		$parm = array(	
										'id'					=>	$PHP_id,
										'submit_user'	=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
										'submit_date'	=>	$TODAY,

			);

		$f1 = $except->send_submit($parm);


		# 記錄使用者動態

	
		// 取出該筆 order 資料 ------------------------------------------------- 
			if (!$op=$except->get($PHP_id)) {
				$op['msg']= $except->msg->get(2);
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}

		$message = "Submit exceptional On Order: [".$op['exc']['ord_num']."]";
		$log->log_add(0,"069S",$message);
		$op['msg'][] = $message;


//當會辦意見輸入完成後傳送訊息給建立異常報告者

		$messg  = '<a href="exception.php?PHP_action=exc_show_cfm&PHP_id='.$PHP_id.'&PHP_sr_exc=0">'.$op['exc']['ord_num'].'</a>';
		$notify->system_msg_send('10-5-S','PM',$op['exc']['ord_num'],$messg);

		$op['exc']['exc_des']=	str_replace( '刪除己核可之採購單務必追回己發出之採購合約', "<font color=red>刪除己核可之採購單務必追回己發出之採購合約</font>", $op['exc']['exc_des'] );



		$op['back_str'] = $PHP_back_str;
		$op['SYS_DEPT'] = $SYS_DEPT;
		page_display($op, '069', $TPL_EXC_SHOW);				
		break;
	
	
		
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		 job 81-4-1    ord_chk_ajax 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "ord_chk_ajax":

	//check_authority('069',"view");

		$result = $except->check($PHP_order);

		if($result)
		{
			echo 0;
		}else{
			echo 1;
		}
	
	break;			
		
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_section_edit":		JOB 15A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "exc_show_apv":
check_authority('067',"view");

// 取出該筆 order 資料 ------------------------------------------------- 
if (!$op=$except->get($PHP_id)) {
	$op['msg']= $except->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}

$op['exc']['exc_des']=	str_replace( '刪除己核可之採購單務必追回己發出之採購合約', "<font color=red>刪除己核可之採購單務必追回己發出之採購合約</font>", $op['exc']['exc_des'] );

if($PHP_sr_exc == 0)
{
	$op['back_str'] = "notify.php?PHP_action=notify";
}else{
	$op['back_str'] = "index2.php?PHP_action=order_apv&PHP_sr_exc=".$PHP_sr_exc;
}
$op['SYS_DEPT'] = $SYS_DEPT;
page_display($op, '067', $TPL_EXC_SHOW_APV);				
break;


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_section_edit":		JOB 15A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_exc_apv":
check_authority('067',"view");

$parm = array(	
	'id'		=>	$PHP_id,
	'apv_user'	=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],										
	'apv_date'	=>	$TODAY,
);

$f1 = $except->send_apv($parm);

$argv2 = array(
	"exc_id"	=>	$PHP_id,
	"user"		=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
	'item'		=>	$GLOBALS['SCACHE']['ADMIN']['dept'],
	"des"		=>	$PHP_detail
);

if($PHP_detail)	 $except->add_log($argv2);

if(isset($PHP_org)) $except->update_fields('org_rec', $PHP_org, $PHP_state_id, $table='exc_static');

// echo $PHP_state_id;
// 取出該筆 order 資料 ------------------------------------------------- 
if (!$op=$except->get($PHP_id)) {
	$op['msg']= $except->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}

$message = "Approval exceptional On Order: [".$op['exc']['ord_num']."]";
$log->log_add(0,"067S",$message);

if($op['exc']['po_num'])
{
	$messg  = '<br>採購單 : ['.$op['exc']['po_num'].']的 異常報告 : <a href="exception.php?PHP_action=exc_show&PHP_id='.$PHP_id.'&PHP_sr_startno=0&SCH_ord=&PHP_cust=">'.$op['exc']['ex_num'].'</a><br>';
	$messg .= '己核可(APVD) ， 請速針對採購單進行接續工作<br>';
	$messg = $notify->cov_html($messg);  //message add

	$parm2 = array(
		"tuser"			=>  $op['exc']['exc_user_id'],
		"title"			=>	'PO : ['.$op['exc']['po_num'].']的異常報告 ['.$op['exc']['ex_num'].'] 己核可',
		"msg"			=>	$messg,	
		"fuser"			=>	'System MSG',
		"send_date"		=>	$TODAY,
	);
	$f3=$notify->add($parm2);  //message add
}


if ($op['exc']['act_des']) {

	$act_des = explode('|',$op['exc']['act_des']);
	
	if($act_des[0] == 'DELPO') {
		$f1 = $apply->del_pa_apvd($act_des[1]);
		$message = "DELETE PO : [".$act_des[1]."]";
		$log->log_add(0,"51D",$message);		
	}
	
	if($act_des[0] == 'DELORD')	{
		$ord_num = explode(',',$op['exc']['ord_num']);
		for($i=0; $i<sizeof($ord_num); $i++)
		{
			$ord_rec = $order->get('',$ord_num[$i]);
			
			if($except->check_del_apvd('DELORD|'.$ord_num[$i]))
			{
				
				$op['msg'][] = $order->del_ord_by_exc($ord_rec['id'], $ord_rec['order_num'],$ord_rec['status']);
				$message="APVD Delete order : ".$ord_num[$i];
				$log->log_add(0,"103D",$message);
			}
		}
	}
	
	if($act_des[0] == 'PREPO') {
		$f1=$apply->update_fields_id('pp_mark','2', $act_des[3], 'ap_special');			
		$parm = array($act_des[2], 'ap_mark', $act_des[4], $act_des[1]);
		$f1=$bom->update_field($parm);
		$parm = array($act_des[2], 'pp_mark', '1', $act_des[1]);
		$f1=$bom->update_field($parm);	
		$PHP_msg = "Suceess Match Pre-Purchase PO#: ".$act_des[4];
		$log->log_add(0,"51M",$PHP_msg);				
	}

}

if($op['exc']['oth_exc'] == 1) {
	$order->update_field_num('fty_cm', $op['state'][0]['org_rec'], $op['exc']['ord_num']);
}

$op['exc']['exc_des']=	str_replace( '刪除己核可之採購單務必追回己發出之採購合約', "<font color=red>刪除己核可之採購單務必追回己發出之採購合約</font>", $op['exc']['exc_des'] );

$op['back_str'] = $PHP_back_str;
$op['msg'][] = "訂單 : [ ".$PHP_ord_num." ]異常報告己核可";
$op['SYS_DEPT'] = $SYS_DEPT;
page_display($op,'067', $TPL_EXC_SHOW_APV);				
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_section_edit":		JOB 15A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "reject_exc_apv":
check_authority('067',"view");

$f1 = $except->update_fields('status', '5', $PHP_id);

$argv2 = array(	
	"exc_id"	=>	$PHP_id,
	"user"		=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
	"item"		=>	"REJECT",
	"des"		=>	"APV REJECT : for - ".$PHP_detail
);

$except->add_log($argv2);
// 取出該筆 order 資料 ------------------------------------------------- 
if (!$op=$except->get($PHP_id)) {
	$op['msg']= $except->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}


$message = "Reject exceptional On Order: [".$op['exc']['ord_num']."]";
$log->log_add(0,"069S",$message);

if($op['exc']['act_des']) 
{
	$act_des = explode('|',$op['exc']['act_des']);
	if($act_des[0] == 'DELORD')
	{
		$ord_num = explode(',',$op['exc']['ord_num']);
		for($i=0; $i<sizeof($ord_num); $i++)
		{
			$ord_rec = $order->get('',$ord_num[$i]);
			$f1 = $order->update_field('del_mk', 2, $ord_rec['id']);
			// $f1 = $order->update_field('del_date', '0000-00-00', $ord_rec['id']);
			// $f1 = $order->update_field('del_user', '', $ord_rec['id']);
			
			$order->update_field('del_cfm_date','0000-00-00',$ord_rec['id']);
			$order->update_field('del_cfm_user','',$ord_rec['id']);

			$order->update_field('del_rev_date','0000-00-00',$ord_rec['id']);
			$order->update_field('del_rev_user','',$ord_rec['id']);

			$argv2 = array(	"order_num"	=>	$ord_num[$i],
				"user"	=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
				"des"	=>	"REJECT order Delete on approval : [ ".$PHP_detail." ]"
			);

			$order_log->add($argv2);
			$op['msg'][] = "REJECT order Delete on approval : ".$ord_num[$i].".  ";
//					$log->log_add(0,"103D",$message);
		}
	}
}


$op['exc']['exc_des']=	str_replace( '刪除己核可之採購單務必追回己發出之採購合約', "<font color=red>刪除己核可之採購單務必追回己發出之採購合約</font>", $op['exc']['exc_des'] );


$op['back_str'] = $PHP_back_str;
$op['msg'][] = $message;
page_display($op, '067', $TPL_EXC_SHOW_APV);				
break;						



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#			 job 101  訂 單
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "rem_exc":
check_authority('030',"view");

$where_str = $manager = $manager_v = $dept_id = '';
$dept_ary = array();
$sales_dept_ary = get_full_sales_dept(); // 取出 業務的部門 [不含K0] ------
$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];   // 判定進入身份的指標
$team = $GLOBALS['SCACHE']['ADMIN']['team_id'];		// 判定進入身份的Team
$dept_ary = $arry2->select($sales_dept_ary,$GLOBALS['SCACHE']['ADMIN']['dept'],"PHP_dept_code","select","get_dept(this)");  
$op['manager_flag'] = 1;
$op['dept_id'] = $dept_id;
$op['dept_ary'] = $dept_ary;

$op['msg'] = $order->msg->get(2);
// creat cust combo box
// 取出 客戶代號
$where_str=$where_str." order by cust_s_name"; //依cust_s_name排序
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


//080725message增加		
$note=$notify->search($GLOBALS['SCACHE']['ADMIN']['login_id'], "`read`=0");
$op['max_notify'] = $note['max_no'];

	// creat factory combo box
	
page_display($op,'030', $TPL_REM_EXC);			    	    
break;		


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "pre_order_add":		JOB 15A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "rem_exc_add":
check_authority('030',"add");

$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];   // 判定進入身份的指標

// 取出選項資料 及傳入之參數
$op['cust_id'] = $PHP_cust;
$op['dept_id']	 = $user_dept;
$op['ord_dept']	 = $PHP_dept_code;
if($user_dept == 'HJ' || $user_dept == 'LY')$op['ord_fty']	 = $user_dept;
//		$op['ord_fty']	 = 'HJ';
$op['date'] = $TODAY;
// 2005/07/30 加入 由 menu進入新增後的 back page 只向 全部列表
page_display($op,'030',$TPL_REM_EXC_ADD);		    	    
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_pre_order_add":		JOB 15A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_rem_exc_add":
check_authority('030',"add");	

if(!$PHP_order)
{
	$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];   // 判定進入身份的指標


	// 取出選項資料 及傳入之參數
	$op['cust_id'] = $PHP_cust;
	$op['dept_id']	 = $user_dept;
	$op['ord_dept']	 = $PHP_dept;
	if($user_dept == 'HJ' || $user_dept == 'LY')$op['ord_fty']	 = $user_dept;
	$op['date'] = $TODAY;
	$op['msg'][] = "Please select order first!";
// 2005/07/30 加入 由 menu進入新增後的 back page 只向 全部列表
	page_display($op,'030',$TPL_REM_EXC_ADD);		    	    
	break;			
}


$oth_dept=0;	
if(isset($PHP_team))
{
	foreach($PHP_team as $i => $tm_value)
	{
		$oth_dept+=$PHP_team[$i];
	}
}

$PHP_exc_des = str_replace("'","\'",$PHP_exc_des);		
for ($i=0; $i< sizeof($GLOBALS['chin_error_word']); $i++)
{
	$PHP_exc_des = str_replace($GLOBALS['chin_error_word'][$i],$GLOBALS['chin_error_word'][$i]."\\",$PHP_exc_des);
}			

if(strstr($PHP_exc_des,'&#'))  //轉換繁簡體
{
	$PHP_exc_des = $ch_cov->check_cov($PHP_exc_des);
}	
$ie1 = $order->get_field_value('ie1', '', $PHP_order);
$old_cm = $order->get_field_value('fty_cm', '', $PHP_order);
if($old_cm <= 0 )$old_cm = $FTY_CM[$PHP_ord_fty] * $ie1;
$PHP_sys_des = "訂單 [".$PHP_order."]原工繳單價 : [ ".$old_cm." ]".chr(13).chr(10);
$ord_num = explode(',',$PHP_order);
for($on=0;	$on<sizeof($ord_num); $on++)
{
$hend = 'EX'.date('y').'-';
$ex_num = $debit->get_no($hend,'ex_num','exceptional');

if(!isset($PHP_sys_des))$PHP_sys_des = '';
$parm = array(	"ord_num"			=>	$ord_num[$on],
								"dept"				=>	$PHP_dept,
								"cust"				=>	$PHP_cust,										
								"exc_date"		=>	$TODAY,										
								"exc_des"			=>	$PHP_exc_des,
								"exc_user"		=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
								'oth_dept'		=>	$oth_dept,
								'ex_num'			=>	$ex_num,
								'oth_static'	=>  '',
								'sys_des'			=>	$PHP_sys_des
	);


$f1_id = $except->add($parm);
$except->update_fields('oth_exc', 1, $f1_id);
if(isset($PHP_state))
{
	for($i=0; $i < 8; $i++)
	{
		if(isset($PHP_state[$i]))
		{
			$parm = array(	"exc_id"			=>	$f1_id,
											"state"				=>	$PHP_state[$i],
											"org_rec"			=>	$PHP_org[$i],										
											"new_rec"			=>	$PHP_new[$i]										
							);
			$f2 = $except->add_static($parm);
		}
	}
}

if(isset($PHP_team))
{
	foreach($PHP_team as $i => $tm_value)
	{				
			$parm = array(	"exc_id"			=>	$f1_id,
											"comm_dept"		=>	$dept_ary[$PHP_team[$i]],
											"comm"				=>	'',																			
							);
			$f2 = $except->add_comm($parm);
			
			$send_dept = $dept_ary[$PHP_team[$i]];

			//傳送異常報告加入意見訊息
			$messg  = '<a href="exception.php?PHP_action=rem_exc_show&PHP_id='.$f1_id.'&PHP_sr_startno=0&SCH_ord=&PHP_cust=">'.$PHP_order.'</a>';
			$notify->system_msg_send('10-5-A',$send_dept,$PHP_order,$messg);			
	}
}
$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
if($user_dept <> $PHP_ord_dept)
{
//增加業務部門異常意見加入
$parm = array(	"exc_id"			=>	$f1_id,
								"comm_dept"		=>	$PHP_ord_dept,
								"comm"				=>	'',																			
			);
$f2 = $except->add_comm($parm);

//傳送異常報告加入意見訊息
$messg  = '<a href="exception.php?PHP_action=rem_exc_show&PHP_id='.$f1_id.'&PHP_sr_startno=0&SCH_ord=&PHP_cust=">'.$PHP_order.'</a>';
$notify->system_msg_send('10-5-A',$PHP_ord_dept,$PHP_order,$messg);
}else{
$ord_rec = $order->get(0, $PHP_order);

//增加業務部門異常意見加入
$parm = array(	"exc_id"			=>	$f1_id,
								"comm_dept"		=>	$ord_rec['factory'],
								"comm"				=>	'',																			
			);
$f2 = $except->add_comm($parm);

//傳送異常報告加入意見訊息
$messg  = '<a href="exception.php?PHP_action=rem_exc_show&PHP_id='.$f1_id.'&PHP_sr_startno=0&SCH_ord=&PHP_cust=">'.$PHP_order.'</a>';
$notify->system_msg_send('10-5-A',$ord_rec['factory'],$PHP_order,$messg);

}


# 記錄使用者動態

$message = "Append Exception On Order: [".$ord_num[$on]."]";
$log->log_add(0,"069A",$message);
$op['msg'][] = $message;



$argv2 = array(	"order_num"	=>	$ord_num[$on],
								"user"	  	=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
								"des"		    =>	' <a href="javascript:open_rem_exc('.$f1_id.')">異常報告 </a>'
				);

if (!$order_log->add($argv2)) {
	$op['msg'] = $order_log->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;	    
}		
}


// 取出該筆 order 資料 ------------------------------------------------- 
	if (!$op=$except->get($f1_id)) {
		$op['msg']= $except->msg->get(2);
		$layout->assign($op);
		$layout->display($TPL_ERROR);  		    
		break;
	}

$op['back_str'] = "exception.php?PHP_action=rem_exc_search&PHP_sr_startno=0&SCH_ord=&PHP_cust=";
page_display($op,'030', $TPL_REM_EXC_SHOW);				
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "rem_exc_search":		JOB 15A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "rem_exc_search":
check_authority('030',"view");

if (!$op = $except->search_cm_exc(1,1)) {  
	$op['msg']= $except->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}


for($i=0; $i<sizeof($op['exc']); $i++)
{
	
	foreach($dept_ary as $key => $value)
	{
		if($op['exc'][$i]['oth_dept'] & $key)
		{						
			
			$where_str = "exc_id = ".$op['exc'][$i]['id']." AND comm_dept = '".$value."'";
			$comm_chk = $except->get_comm_field('Comm', $where_str);
			if($comm_chk && $comm_chk <> 'UN-COMM')
			{
				$op['exc'][$i][$value] = 2;
			}else{
				$op['exc'][$i][$value] = 1;
			}
		}else{
			$op['exc'][$i][$value] = 0;
		}
	}
	//判斷業務部門是否加入意見
	$where_str = "exc_id = ".$op['exc'][$i]['id']." AND comm_dept = '".$op['exc'][$i]['ord_dept']."'";
	$comm_chk = $except->get_comm_field('Comm', $where_str);
	if($comm_chk)
	{ 
		if($comm_chk <> 'UN-COMM')
		{
			$op['exc'][$i][$op['exc'][$i]['ord_dept']] = 2;
		}else{
			$op['exc'][$i][$op['exc'][$i]['ord_dept']] = 0;
		}
	}else{
		$op['exc'][$i][$op['exc'][$i]['ord_dept']] = 1;
	}


	//判斷業務部門是否加入意見
	$where_str = "exc_id = ".$op['exc'][$i]['id']." AND comm_dept = '".$op['exc'][$i]['ord_fty']."'";
	$comm_chk = $except->get_comm_field('Comm', $where_str);

	if($comm_chk)
	{
		if($comm_chk <> 'UN-COMM')
		{
			$op['exc'][$i][$op['exc'][$i]['ord_fty']] = 2;
		}else{
			$op['exc'][$i][$op['exc'][$i]['ord_fty']] = 0;
		}
	}else{
		$op['exc'][$i][$op['exc'][$i]['ord_fty']] = 1;
	}
	
}
	

$op['msg']= $except->msg->get(2);
if(isset($PHP_msg))$op['msg'][] = $PHP_msg;
$op['back_str'] = "&SCH_ord=".$SCH_ord."&PHP_cust=".$PHP_cust."&PHP_dept=".$PHP_dept;

page_display($op,'030', $TPL_REM_EXC_LIST);
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_section_edit":		JOB 15A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "rem_exc_show":
check_authority('030',"view");

// 取出該筆 order 資料 ------------------------------------------------- 
	if (!$op=$except->get($PHP_id)) {
		$op['msg']= $except->msg->get(2);
		$layout->assign($op);
		$layout->display($TPL_ERROR);  		    
		break;
	}

$op['back_str'] = "exception.php?PHP_action=rem_exc_search&PHP_sr_startno=".$PHP_sr_startno."&SCH_ord=".$SCH_ord."&PHP_cust=".$PHP_cust."&PHP_dept=".$PHP_dept;;
page_display($op,'030', $TPL_REM_EXC_SHOW);				
break;	

	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_section_edit":		JOB 15A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "rem_exc_edit":
check_authority('030',"add");

// 取出該筆 order 資料 ------------------------------------------------- 
if (!$op=$except->get($PHP_id)) {
	$op['msg']= $except->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}

foreach($dept_ary as $key => $value)
{
	$op['exc'][$value] = 0;
	if($op['exc']['oth_dept'] & $key)
	{			
		$op['exc'][$value] = 1;
		$where_str = "exc_id = ".$PHP_id." AND comm_dept = '".$value."'";
		$comm_chk = $except->get_comm_field('Comm', $where_str);
		if($comm_chk)$op['exc'][$value] = 2;
		
	}
}

$op['exc']['exc_des'] = str_replace("<br>",chr(13).chr(10), $op['exc']['exc_des'] );
$op['exc']['sys_des'] = str_replace("<br>",chr(13).chr(10), $op['exc']['sys_des'] );

$op['back_str'] = $PHP_back_str;
page_display($op,'030', $TPL_REM_EXC_EDIT);				
break;	


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "section_update":		JOB 15E
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_rem_exc_edit":
check_authority('030',"add");	

$PHP_oth_dept = 0;
if(isset($PHP_team))
{
	foreach($PHP_team as $i => $tm_value)
	{
		$PHP_oth_dept+=$PHP_team[$i];
	}
}

$PHP_exc_des = str_replace("'","\'",$PHP_exc_des);		
for ($i=0; $i< sizeof($GLOBALS['chin_error_word']); $i++)
{
	$PHP_exc_des = str_replace($GLOBALS['chin_error_word'][$i],$GLOBALS['chin_error_word'][$i]."\\",$PHP_exc_des);
}

if(strstr($PHP_exc_des,'&#'))  //轉換繁簡體
{
	$PHP_exc_des = $ch_cov->check_cov($PHP_exc_des);
}	

$parm = array(	
								"exc_des"			=>	$PHP_exc_des,
								'oth_dept'		=>	$PHP_oth_dept,
								'oth_static'	=>	'',
								'id'					=>	$PHP_id
	);

$f1 = $except->edit($parm);


foreach($PHP_e_org as $key => $value)
{
	
	$parm = array(												
									"org_rec"			=>	$value,										
									"new_rec"			=>	$PHP_e_new[$key],
									"id"					=>	$key					
					);
	$f2 = $except->edit_state($parm);
}


	foreach($dept_ary as $key => $value)
	{
		$new_dept = $PHP_oth_dept & $key;
		$old_dept = $PHP_org_dept & $key;
		if($new_dept && !$old_dept)
		{
			$parm = array(	"exc_id"			=>	$PHP_id,
											"comm_dept"		=>	$value,
											"comm"				=>	'',																			
							);
			$f2 = $except->add_comm($parm);

		//傳送異常報告加入意見訊息
		 $messg  = '<a href="exception.php?PHP_action=rem_exc_show&PHP_id='.$PHP_id.'&PHP_sr_startno=0&SCH_ord=&PHP_cust=">'.$PHP_order.'</a>';
		 $notify->system_msg_send('10-5-A',$value,$PHP_order,$messg);

		
		}
		if(!$new_dept && $old_dept)
		{
			$where_str = "exc_id = ".$PHP_id." AND comm_dept = '".$value."'";
			$f4=$except->comm_del($where_str);
		}
	}

# 記錄使用者動態


// 取出該筆 order 資料 ------------------------------------------------- 
	if (!$op=$except->get($PHP_id)) {
		$op['msg']= $pre_ord->msg->get(2);
		$layout->assign($op);
		$layout->display($TPL_ERROR);  		    
		break;
	}

$message = "EDIT Exception On Order: [".$op['exc']['ord_num']."]";
$log->log_add(0,"069E",$message);
$op['msg'][] = $message;


$op['back_str'] = $PHP_back_str;
page_display($op,'030', $TPL_REM_EXC_SHOW);				
break;
		

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_section_edit":		JOB 15A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "rem_exc_comm_edit":
check_authority('030',"edit");

// 取出該筆 order 資料 ------------------------------------------------- 
	if (!$op=$except->get($PHP_id)) {
		$op['msg']= $except->msg->get(2);
		$layout->assign($op);
		$layout->display($TPL_ERROR);  		    
		break;
	}

$op['dept'] = $GLOBALS['SCACHE']['ADMIN']['dept'];

// if($GLOBALS['SCACHE']['ADMIN']['login_id'] == 'kathyc') $op['dept'] = 'PM';

for($i=0; $i<sizeof($op['comm']); $i++)
	$op['comm'][$i]['Comm'] = str_replace("<br>",chr(13).chr(10), $op['comm'][$i]['Comm'] );

$op['back_str'] = $PHP_back_str;
page_display($op,'030', $TPL_REM_EXC_COMM);				
break;			



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "section_update":		JOB 15E
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_rem_exc_comm_edit":
check_authority('030',"edit");	

$PHP_comm = str_replace("'","\'",$PHP_comm);		
for ($i=0; $i< sizeof($GLOBALS['chin_error_word']); $i++)
{
	$PHP_comm = str_replace($GLOBALS['chin_error_word'][$i],$GLOBALS['chin_error_word'][$i]."\\",$PHP_comm);
}

if(strstr($PHP_comm,'&#'))  //轉換繁簡體
{
	$PHP_comm = $ch_cov->check_cov($PHP_comm);
}	

$parm = array(	
								'Comm'			=>	$PHP_comm,
								'id'				=>	$PHP_comm_id,
								'comm_user'	=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
								'comm_date'	=>	$TODAY,

	);

$f1 = $except->edit_comm($parm);


# 記錄使用者動態


// 取出該筆 order 資料 ------------------------------------------------- 
	if (!$op=$except->get($PHP_id)) {
		$op['msg']= $pre_ord->msg->get(2);
		$layout->assign($op);
		$layout->display($TPL_ERROR);  		    
		break;
	}

//當會辦意見輸入完成後傳送訊息給建立異常報告者
if($op['com_mk'] == 1)
{
	$messg  = '<br><br>異常報告 : <a href="exception.php?PHP_action=rem_exc_show&PHP_id='.$PHP_id.'&PHP_sr_startno=0&SCH_ord=&PHP_cust=">'.$op['exc']['ord_num'].'</a><br>';
	$messg .= '會辦單位意見己加入完成 ， 請速SUBMIT ， 同時列印出來 呈上級核示<br><br>';
	$messg = $notify->cov_html($messg);  //message add

	$parm2 = array(	"tuser"			=>  $op['exc']['exc_user_id'],
									"title"			=>	'異常報告 ['.$op['exc']['ord_num'].'] 己完成會辦單位意見加入',
									"msg"				=>	$messg,	
									"fuser"			=>	'System MSG',
									"send_date"	=>	$TODAY,
									);
	$f3=$notify->add($parm2);  //message add
}


$message = "ADD ".$PHP_comm_dept." Comment On Order: [".$op['exc']['ord_num']."]";
$log->log_add(0,"069C",$message);
$op['msg'][] = $message;

$op['exc']['exc_des']=	str_replace( '刪除己核可之採購單務必追回己發出之採購合約', "<font color=red>刪除己核可之採購單務必追回己發出之採購合約</font>", $op['exc']['exc_des'] );


$op['back_str'] = $PHP_back_str;
page_display($op,'030', $TPL_REM_EXC_SHOW);				
break;		


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "section_update":		JOB 15E
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "rem_exc_submit":
check_authority('030',"add");	



$parm = array(	
								'id'					=>	$PHP_id,
								'submit_user'	=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
								'submit_date'	=>	$TODAY,

	);

$f1 = $except->send_submit($parm);


# 記錄使用者動態


// 取出該筆 order 資料 ------------------------------------------------- 
	if (!$op=$except->get($PHP_id)) {
		$op['msg']= $except->msg->get(2);
		$layout->assign($op);
		$layout->display($TPL_ERROR);  		    
		break;
	}

$message = "Submit exceptional On Order: [".$op['exc']['ord_num']."]";
$log->log_add(0,"069S",$message);
$op['msg'][] = $message;


//當會辦意見輸入完成後傳送訊息給建立異常報告者

$messg  = '<a href="exception.php?PHP_action=exc_show_cfm&PHP_id='.$PHP_id.'&PHP_sr_exc=0">'.$op['exc']['ord_num'].'</a>';
$notify->system_msg_send('10-5-S','PM',$op['exc']['ord_num'],$messg);

$op['exc']['exc_des']=	str_replace( '刪除己核可之採購單務必追回己發出之採購合約', "<font color=red>刪除己核可之採購單務必追回己發出之採購合約</font>", $op['exc']['exc_des'] );



$op['back_str'] = $PHP_back_str;
page_display($op, '069', $TPL_REM_EXC_SHOW);				
break;		



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case "do_pre_order_add":		JOB 15A PHP_dept
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_exc_add":
check_authority('069',"add");

$oth_dept=0;	
if(!$PHP_order || !isset($PHP_state))
{
	$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];   // 判定進入身份的指標


	// 取出選項資料 及傳入之參數
	$op['msg'][]= 'Please input Order# and exceptional item first.';
	$op['cust_id'] = $PHP_cust;
	$op['dept_id'] = $PHP_dept;
	$op['date'] = $TODAY;

	page_display($op,'069',$TPL_EXC_ADD);		    	    
	break;
}

//判斷訂單刪除是否可刪
if(isset($PHP_state[9])) {
	$ord_num = explode(',',$PHP_order);
	for($i=0; $i<sizeof($ord_num); $i++) {
		$tmp = $order->get('',$ord_num[$i]);
		if($tmp['status'] >= 8) {
			$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];   // 判定進入身份的指標


			// 取出選項資料 及傳入之參數
			$op['msg'][]= $ord_num[$i].'was producted, Can\'t delete these order records.';
			$op['cust_id'] = $PHP_cust;
			$op['dept_id']	 = $PHP_dept;
			$op['date'] = $TODAY;
			// 2005/07/30 加入 由 menu進入新增後的 back page 只向 全部列表
			page_display($op,'069',$TPL_EXC_ADD);		    	    
			break;					
		}
	}
}

if(!isset($PHP_exc_from))$PHP_exc_from = 0;

if(isset($PHP_team)){
	foreach($PHP_team as $i => $tm_value){
		$oth_dept+=$PHP_team[$i];
	}
}

$PHP_exc_des = str_replace("'","\'",$PHP_exc_des);
for ($i=0; $i< sizeof($GLOBALS['chin_error_word']); $i++){
	$PHP_exc_des = str_replace($GLOBALS['chin_error_word'][$i],$GLOBALS['chin_error_word'][$i]."\\",$PHP_exc_des);
}			

if(strstr($PHP_exc_des,'&#'))  //轉換繁簡體
{
	$PHP_exc_des = $ch_cov->check_cov($PHP_exc_des);
}

$hend = 'EX'.date('y').'-';
$ex_num = $debit->get_no($hend,'ex_num','exceptional');
$PHP_order = substr($PHP_order,0,-1);
if(!isset($PHP_state_name))$PHP_state_name = '';
if(!isset($PHP_sys_exc))$PHP_sys_exc = '';

$parm_main = array(
	"ord_num"		=>	$PHP_order,
	"dept"			=>	$PHP_dept,
	"cust"			=>	$PHP_cust,										
	"exc_date"		=>	$TODAY,										
	"exc_des"		=>	$PHP_exc_des,
	"exc_user"		=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
	'oth_dept'		=>	$oth_dept,
	'ex_num'		=>	$ex_num,
	'oth_static'	=>	$PHP_state_name,
	'sys_des'		=>	$PHP_sys_des,
	'exc_from'		=>	$PHP_exc_from
);

$f1_id = $except->add($parm_main);

if(isset($PHP_state))
{
	foreach($PHP_state as $key => $value){
		$parm = array(
			"exc_id"	=>	$f1_id,
			"state"		=>	$PHP_state[$key],
			"org_rec"	=>	$PHP_org[$key],										
			"new_rec"	=>	$PHP_new[$key]										
		);
		$f2 = $except->add_static($parm);
		$parm_st[] = $parm;
	}
}
/*
$messg  = '<br><br>異常報告 : <a href="exception.php?PHP_action=exc_show&PHP_id='.$f1_id.'&PHP_sr_startno=0&SCH_ord=&PHP_cust=">'.$PHP_order.'</a><br>';
$messg .= '請點擊訂單編號連結異常報告<br><br>';
$messg = $notify->cov_html($messg);  //message add
*/
if(isset($PHP_team))
{
	foreach($PHP_team as $i => $tm_value)
	{
		$parm = array(
			"exc_id"		=>	$f1_id,
			"comm_dept"		=>	$dept_ary[$PHP_team[$i]],
			"comm"			=>	'',																			
		);
		$f2 = $except->add_comm($parm);
		$parm_tm[] = $parm;
		$send_dept = $dept_ary[$PHP_team[$i]];

		//傳送異常報告加入意見訊息
		$messg  = '<a href="exception.php?PHP_action=exc_show&PHP_id='.$f1_id.'&PHP_sr_startno=0&SCH_ord=&PHP_cust=">'.$PHP_order.'</a>';
		$notify->system_msg_send('10-5-A',$send_dept,$PHP_order,$messg);			
	}
}

$ord_nums = explode(',',$PHP_order);
$ord_dept = $order->get_field_value('dept','',$ord_nums[0]);
$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];

if( $user_dept == "KA" || $user_dept == "KB" || $user_dept == "DA" || $user_dept == "PO" ){

    //增加業務部門異常意見加入
    $parm = array(
        "exc_id"		=>	$f1_id,
        "comm_dept"		=>	$user_dept,
        "comm"			=>	'',																			
    );

    $f2 = $except->add_comm($parm);

    //傳送異常報告加入意見訊息
    $messg  = '<a href="exception.php?PHP_action=exc_show&PHP_id='.$f1_id.'&PHP_sr_startno=0&SCH_ord=&PHP_cust=">'.$PHP_order.'</a>';
    $notify->system_msg_send('10-5-A',$user_dept,$PHP_order,$messg);
    
}	

# 記錄使用者動態

$message = "Append Exception On Order: [".$PHP_order."]";
$log->log_add(0,"069A",$message);
$op['msg'][] = $message;


//採購單刪除
if($PHP_ap_num){
	$value = "DELPO|".$PHP_ap_num;
	$PHP_po = "PO".substr($PHP_ap_num,2);
	$except->update_fields('act_des', $value, $f1_id);
}

if($PHP_po) {			
    $except->update_fields('po_num', $PHP_po, $f1_id);
    $PHP_aply_num = "PA".substr($PHP_po,2);
    $parm= array(
        'ap_num'		=>	$PHP_aply_num,
        'item'			=>	'Document',
        'des'				=>	' <a href="javascript:open_exc('.$f1_id.')">異常報告 </a>',
        'user'			=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
        'k_date'		=>	date('Y-m-d')
    );
	$fp1=$apply->add_log($parm);			
}

if($PHP_exc_action){
	$except->update_fields('act_des', $PHP_exc_action, $f1_id);
}

$ord_num = explode(',',$PHP_order);

//訂單刪除
if(isset($PHP_state[9])) {		
	$value = "DELORD|".$PHP_order;
	$except->update_fields('act_des', $value, $f1_id);
	
	for($on=0;	$on<sizeof($ord_num); $on++) {
    
        //訂單刪除時有採購單處理為增加採購單異常報告 == start ==			
		$tmp = $po->po_search_by_ord($ord_num[$on],'','','order_del');
        
		for($px=0; $px<sizeof($tmp['apply']); $px++) {
			
			$po_det[$tmp['apply'][$px]['po_num']][] = $tmp['apply'][$px];
		}
		
		foreach($po_det as $key	=>	$value) {
        
			$parm_main['sys_des'] = $parm_main['exc_des'] = '';
			$parm_main['ex_num'] = $debit->get_no($hend,'ex_num','exceptional');
			$parm_main['sys_des'] .= '己成立採購單 : ['.$key.'], 採購物料明細如下:'.chr(13).chr(10);
			for($px=0; $px<sizeof($po_det[$key]); $px++)
			{
				$parm_main['sys_des'] .= '物料 : ['.$po_det[$key][$px]['mat_code'].'], 己採購數量為:'.$po_det[$key][$px]['qty'].' '.$po_det[$key][$px]['po_unit'].chr(13).chr(10);
			}
			$px1_id = $except->add($parm_main);

			$value = "DELORD|".$PHP_order;
			$except->update_fields('act_des', $value, $px1_id);

			$except->update_fields('po_num', $key, $px1_id);
			$PHP_aply_num = "PA".substr($key,2);
			$parm= array(
				'ap_num'	=>	$PHP_aply_num,
				'item'		=>	'Document',
				'des'		=>	' <a href="javascript:open_exc('.$px1_id.')">異常報告 </a>',
				'user'		=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
				'k_date'	=>	date('Y-m-d')
			);
            
			$fp1=$apply->add_log($parm);	
		
			for($px=0; $px<sizeof($parm_st); $px++) {
				$parm_st[$px]['exc_id'] = $px1_id;
				$f2 = $except->add_static($parm_st[$px]);
			}
            
			foreach($PHP_team as $i => $tm_value) {
            
				$parm = array(	
					"exc_id"		=>	$px1_id,
					"comm_dept"		=>	$dept_ary[$PHP_team[$i]],
					"comm"			=>	'',																			
				);
                
				$f2 = $except->add_comm($parm);
			
				$send_dept = $dept_ary[$PHP_team[$i]];

				//傳送異常報告加入意見訊息
				$messg  = '<a href="exception.php?PHP_action=exc_show&PHP_id='.$px1_id.'&PHP_sr_startno=0&SCH_ord=&PHP_cust=">'.$PHP_order.'</a>';
				$notify->system_msg_send('10-5-A',$send_dept,$PHP_order,$messg);							
			}				
        }

		//訂單刪除時有採購單處理為增加採購單異常報告 == END ==		
		$odr_rec = $order->get('',$ord_num[$on]);		
		$f1 = $order->update_field('del_mk', 3, $odr_rec['id']);
		$f1 = $order->update_field('del_date', $TODAY, $odr_rec['id']);
		$f1 = $order->update_field('del_user', $GLOBALS['SCACHE']['ADMIN']['login_id'], $odr_rec['id']);			
	}
}

for($on=0;	$on<sizeof($ord_num); $on++)
{	
    $argv2 = array(	
        "order_num"	=>	$ord_num[$on],
        "user"	  	=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
        "des"       =>	' <a href="javascript:open_exc('.$f1_id.')">異常報告 </a>'
    );

    if (!$order_log->add($argv2)) {
        $op['msg'] = $order_log->msg->get(2);
        $layout->assign($op);
        $layout->display($TPL_ERROR);  		    
        break;	    
    }		
}

// 取出該筆 order 資料 ------------------------------------------------- 
if (!$op=$except->get($f1_id)) {
    $op['msg']= $except->msg->get(2);
    $layout->assign($op);
    $layout->display($TPL_ERROR);  		    
    break;
}

$op['exc']['exc_des']=	str_replace( '刪除己核可之採購單務必追回己發出之採購合約', "<font color=red>刪除己核可之採購單務必追回己發出之採購合約</font>", $op['exc']['exc_des'] );

if($PHP_ord_shift)
{
	redirect_page($PHP_ord_shift);
}

$op['back_str'] = "exception.php?PHP_action=exc_search&PHP_sr_startno=0&SCH_ord=&PHP_cust=";
page_display($op, '069', $TPL_EXC_SHOW);				
break;




#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_search":			job 53
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "po_search":
check_authority('069',"view");

if(!isset($PHP_cust))$PHP_cust = '';
$where_str="order by cust_s_name"; //依cust_s_name排序
$cust_def = $cust->get_fields('cust_init_name',$where_str);
$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//取出客戶代號
for ($i=0; $i< sizeof($cust_def); $i++)
{
	$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//以 [客戶代號-客戶簡稱] 呈現
}

$op['factory_select'] = $arry2->select($SHIP,'','PHP_SCH_fty','select','');
$op['cust_sch_select'] =  "<input type=hidden name=PHP_SCH_cust value=$PHP_cust>".$PHP_cust;
//		$arry2->select($cust_value,$PHP_cust,'PHP_SCH_cust','select','',$cust_def_vue); 

$op['msg'] = $order->msg->get(2);

	
page_display($op, '069', $TPL_EXC_PO_ADD_LIST);			    	    
break;

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "po_search":			job 53
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_po_search":
check_authority('069',"view");

if(!isset($SCH_ord))$SCH_ord='';
if($SCH_ord == '')
{
	$op = $po->search(3);
}else{
	$op = $po->search_ord_po();
}	


$where_str="order by cust_s_name"; //依cust_s_name排序
$cust_def = $cust->get_fields('cust_init_name',$where_str);
$cust_def_vue = $cust->get_fields('cust_s_name',$where_str);	//取出客戶代號
for ($i=0; $i< sizeof($cust_def); $i++)
{
	$cust_value[$i]=$cust_def_vue[$i]." - ".$cust_def[$i];	//以 [客戶代號-客戶簡稱] 呈現
}

$op['factory_select'] = $arry2->select($SHIP,'','PHP_SCH_fty','select','');
//		$op['cust_sch_select'] =  $arry2->select($cust_value,'','PHP_SCH_cust','select','',$cust_def_vue); 
$op['cust_sch_select'] =  "<input type=hidden name=PHP_SCH_cust value=$PHP_SCH_cust>".$PHP_SCH_cust;

$op['msg'] = $order->msg->get(2);

$back_str="&PHP_SCH_num=".$PHP_SCH_num."&PHP_SCH_fty=".$PHP_SCH_fty."&PHP_SCH_cust=".$PHP_SCH_cust."&PHP_SCH_supl=".$PHP_SCH_supl."&PHP_sr_startno=".$PHP_sr_startno."&SCH_ord=".$SCH_ord;
$op['back_str']=$back_str;

	
page_display($op, '069', $TPL_EXC_PO_ADD_LIST);			    	    
break;		



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_section_edit":		JOB 15A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "exc_log_add":
check_authority('069',"view");

$argv2 = array(	"exc_id"	=>	$PHP_id,
								"user"		=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
								"item"		=>	$GLOBALS['SCACHE']['ADMIN']['dept'],
								"des"			=>	$PHP_detail
				);
if($PHP_detail)	 $except->add_log($argv2);

$redirect = "exception.php?PHP_action=".$PHP_to."&PHP_id=".$PHP_id.$PHP_back_str;
redirect_page($redirect);
			


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		job 23A sample pattern upload
#		case "upload_smpl_pattern":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "upload_exc_file":

check_authority('069',"add");

if(strstr($PHP_desp,'&#'))
{
	$PHP_desp = $ch_cov->check_cov($PHP_desp);
}	

$filename = $_FILES['PHP_pttn']['name'];		

$ext =  strtolower(preg_replace("/.*\.([^.]+)$/","\\1", $filename));
$f_check = 0; //判斷副檔名是否存在
for ($i=0; $i<sizeof( $GLOBALS['VALID_TYPES']); $i++)
{
	if ($GLOBALS['VALID_TYPES'][$i] == $ext)
	{
		$f_check = 1;
		break;
	}
}


if ($filename && $PHP_desp)
{			
if ($_FILES['PHP_pttn']['size'] < 2072864 && $_FILES['PHP_pttn']['size'] > 0)
{
	
if ($f_check == 1){   // 上傳檔的副檔名為 mdl 時 -----

	$today = $GLOBALS['TODAY'];
	$user_name =  $GLOBALS['SCACHE']['ADMIN']['name'];
	$parm = array(	"file_name"		=>  $PHP_id,				
									"ex_id"				=>  $PHP_id,
									"file_des"		=>	$PHP_desp,
									"file_user"		=>	$user_name,
									"file_date"		=>	$today
								);

	// upload pattern file to server
	$A = $fils->get_name_id('exc_file_det');
	$pttn_name = $PHP_id."_".$A.".".$ext;
	$parm['file_name'] = $pttn_name;
//			$fils->	update_smpl_file($pttn_name,$A);

	$str_long=strlen($pttn_name);
	$upload = new Upload;
	$upload->setMaxSize(2072864);
	// $fu1 = $upload->uploadFile(dirname($PHP_SELF).'/exc_file/', 'other', 16, $pttn_name );
	$fu1 = $upload->uploadFile(dirname(__FILE__).'\exc_file\\', 'other', 16, $pttn_name );
	$upload->setMaxSize(2072864);
	if (!$upload){
		$op['msg'][] = $upload;
		$layout->assign($op);
		$layout->display($TPL_ERROR);  		    
		break;
	}
	if (!$A = $fils->upload_exc_file($parm)){
		$op['msg'] = $fils->msg->get(2);
		$layout->assign($op);
		$layout->display($TPL_ERROR);  		    
		break;
	}
	$message = "UPLOAD file of #".$pttn_name;
	$log->log_add(0,"23E",$message);
} else {  // 上傳檔的副檔名  是  exe 時 -----

	$message = "upload file is incorrect format ! Please re-send.";
}
}else{  //上傳檔名重覆時
	$message = "upload file is too big!!";
}
}else{
	$message="You don't pick any file or add any file description.";
}	
	

// 將 製造令 完整show out ------
//  wi 主檔
	if (!$op=$except->get($PHP_id)) {
		$op['msg']= $except->msg->get(2);
		$layout->assign($op);
		$layout->display($TPL_ERROR);  		    
		break;
	}
	
$op['exc']['exc_des']=	str_replace( '刪除己核可之採購單務必追回己發出之採購合約', "<font color=red>刪除己核可之採購單務必追回己發出之採購合約</font>", $op['exc']['exc_des'] );

$op['back_str'] = $PHP_back_str;
page_display($op, '069', $TPL_EXC_SHOW);				
break;


//=======================================================
case "exc_file_del":
//	check_authority(3,1,"edit");	

$f1 = $fils->del_file('exc_file_det',$PHP_id);
if (!$f1) {
	$op['msg'] = $fils->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}

if(file_exists($GLOBALS['config']['root_dir']."/exc_file/".$PHP_file_name)){
	unlink("./exc_file/".$PHP_file_name);
}
$message = "DELETE file of #".$PHP_file_name;
$log->log_add(0,"23E",$message);
echo $message;
break;


//=======================================================
case "exc_add_num":
check_authority('069',"del");	

$exc = $except->get_un_num();
for($i=0; $i<sizeof($exc); $i++)
{
	$exc_date = explode('-',$exc[$i]['exc_date']);
	$hend = 'EX'.substr($exc_date[0],2).'-';
	$ex_num = $debit->get_no($hend,'ex_num','exceptional');
	
	echo $ex_num."<br>";
	$except->update_fields('ex_num', $ex_num, $exc[$i]['id']);
}
break;		


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_exc_comm_reject":		JOB 15E
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_exc_comm_reject":
check_authority('069',"edit");	

$PHP_comm = str_replace("'","\'",$PHP_comm);		
for ($i=0; $i< sizeof($GLOBALS['chin_error_word']); $i++)
{
	$PHP_comm = str_replace($GLOBALS['chin_error_word'][$i],$GLOBALS['chin_error_word'][$i]."\\",$PHP_comm);
}

if(strstr($PHP_comm,'&#'))  //轉換繁簡體
{
	$PHP_comm = $ch_cov->check_cov($PHP_comm);
}	

$parm = array(	
								'Comm'			=>	$PHP_comm,
								'id'				=>	$PHP_comm_id,
								'comm_user'	=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
								'comm_date'	=>	$TODAY,

	);

$f1 = $except->edit_comm($parm);
$except->update_fields('status', 1, $PHP_id);

# 記錄使用者動態


// 取出該筆 order 資料 ------------------------------------------------- 
	if (!$op=$except->get($PHP_id)) {
		$op['msg']= $pre_ord->msg->get(2);
		$layout->assign($op);
		$layout->display($TPL_ERROR);  		    
		break;
	}

//當會辦意見輸入完成後傳送訊息給建立異常報告者
	$messg  = '<br><br>異常報告 : <a href="exception.php?PHP_action=exc_show&PHP_id='.$PHP_id.'&PHP_sr_startno=0&SCH_ord=&PHP_cust=">'.$op['exc']['ord_num'].'</a><br>';
	$messg .= '會辦單位發出駁回意見 ， 請再次確認並修改異常報告<br><br>';
	$messg = $notify->cov_html($messg);  //message add

	$parm2 = array(	"tuser"			=>  $op['exc']['exc_user_id'],
									"title"			=>	'異常報告 ['.$op['exc']['ord_num'].'] 己駁回',
									"msg"				=>	$messg,	
									"fuser"			=>	'System MSG',
									"send_date"	=>	$TODAY,
									);
	$f3=$notify->add($parm2);  //message add


$message = "ADD ".$PHP_comm_dept." Comment On Order: [".$op['exc']['ord_num']."]";
$log->log_add(0,"069C",$message);
$op['msg'][] = $message;

$op['exc']['exc_des']=	str_replace( '刪除己核可之採購單務必追回己發出之採購合約', "<font color=red>刪除己核可之採購單務必追回己發出之採購合約</font>", $op['exc']['exc_des'] );


$op['back_str'] = $PHP_back_str;
$op['SYS_DEPT'] = $SYS_DEPT;
page_display($op, '069', $TPL_EXC_SHOW);				
break;		




#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "exc_show_cfmt":		JOB 15A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "exc_show_cfm":
check_authority('066',"add");

// 取出該筆 order 資料 ------------------------------------------------- 
if (!$op=$except->get($PHP_id)) {
	$op['msg']= $except->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}

$op['exc']['exc_des']=	str_replace( '刪除己核可之採購單務必追回己發出之採購合約', "<font color=red>刪除己核可之採購單務必追回己發出之採購合約</font>", $op['exc']['exc_des'] );

// if($GLOBALS['SCACHE']['ADMIN']['dept'] == 'PM'  && ($GLOBALS['SCACHE']['ADMIN']['team_id'] == 'SU'))$op['exc_flag'] = 1;
// if($GLOBALS['SCACHE']['ADMIN']['dept'] == 'SA') $op['exc_flag'] = 1;

if($PHP_sr_exc == 0)
{
	$op['back_str'] = "notify.php?PHP_action=notify";
}else{
	$op['back_str'] = "index2.php?PHP_action=order_cfm&PHP_sr_exc=".$PHP_sr_exc;
}
$op['SYS_DEPT'] = $SYS_DEPT;
page_display($op,'066', $TPL_EXC_SHOW_CFM);				
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "do_exc_cfm":		JOB 15A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_exc_cfm":
check_authority('066',"view");

$parm = array(	
	'id'        =>	$PHP_id,
	'cfm_user'	=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],										
	'cfm_date'	=>	$TODAY,
);

$f1 = $except->send_cfm($parm);

$argv2 = array(	
	"exc_id"	=>	$PHP_id,
	"user"		=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
	'item'		=>	$GLOBALS['SCACHE']['ADMIN']['dept'],
	"des"		=>	$PHP_detail
);

if($PHP_detail)	 $except->add_log($argv2);

// 取出該筆 order 資料 ------------------------------------------------- 
if (!$op=$except->get($PHP_id)) {
	$op['msg']= $except->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}

$ord_num = explode(',',$op['exc']['ord_num']);
for($i=0; $i<sizeof($ord_num); $i++)
{
	$ord_rec = $order->get('',$ord_num[$i]);
	$order->update_field('del_cfm_date', date('Y-m-d'),$ord_rec['id']);
	$order->update_field('del_cfm_user',$GLOBALS['SCACHE']['ADMIN']['login_id'],$ord_rec['id']);
}

$message = "CONFIRM exceptional On Order: [".$op['exc']['ord_num']."]";
$log->log_add(0,"066C",$message);		

$messg  = '<a href="exception.php?PHP_action=exc_show_rev&PHP_id='.$PHP_id.'&PHP_sr_exc=0">'.$op['exc']['ord_num'].'</a>';
$notify->system_msg_send('066-C','GM',$op['exc']['ord_num'],$messg);

$op['exc']['exc_des']=	str_replace( '刪除己核可之採購單務必追回己發出之採購合約', "<font color=red>刪除己核可之採購單務必追回己發出之採購合約</font>", $op['exc']['exc_des'] );


$op['back_str'] = $PHP_back_str;
$op['msg'][] = "訂單 : [ ".$PHP_ord_num." ]異常報告己CONFIRM";
$op['SYS_DEPT'] = $SYS_DEPT;
page_display($op, '066', $TPL_EXC_SHOW_CFM);				
break;



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# case  "reject_exc_cfm":		JOB 15A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "reject_exc_cfm":
check_authority('066',"view");

$f1 = $except->update_fields('status', '5', $PHP_id);

$argv2 = array(
	"exc_id"	=>	$PHP_id,
	"user"		=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
	"item"		=>	"REJECT",
	"des"		=>	"CFM REJECT : for - ".$PHP_detail
);

$except->add_log($argv2);
// 取出該筆 order 資料 ------------------------------------------------- 
if (!$op=$except->get($PHP_id)) {
	$op['msg']= $except->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}

$message = "Reject exceptional On Order: [".$op['exc']['ord_num']."]";
$log->log_add(0,"066S",$message);

$op['exc']['exc_des']=	str_replace( '刪除己核可之採購單務必追回己發出之採購合約', "<font color=red>刪除己核可之採購單務必追回己發出之採購合約</font>", $op['exc']['exc_des'] );

$op['back_str'] = $PHP_back_str;
$op['msg'][] = $message;
page_display($op, '066', $TPL_EXC_SHOW_APV);				
break;						




#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case  "exc_show_rev":		JOB 15A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "exc_show_rev":
check_authority('075',"view");

// 取出該筆 order 資料 ------------------------------------------------- 
if (!$op=$except->get($PHP_id)) {
	$op['msg']= $except->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}

$op['exc']['exc_des']=	str_replace( '刪除己核可之採購單務必追回己發出之採購合約', "<font color=red>刪除己核可之採購單務必追回己發出之採購合約</font>", $op['exc']['exc_des'] );

if($PHP_sr_exc == 0){
	$op['back_str'] = "notify.php?PHP_action=notify";
}else{
	$op['back_str'] = "index2.php?PHP_action=order_rev&PHP_sr_exc=".$PHP_sr_exc;
}

page_display($op,'075', $TPL_EXC_SHOW_REV);				
break;		



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case  "do_exc_cfm":		JOB 15A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "do_exc_rev":
check_authority('075',"view");

$parm = array(	
	'id'		=>	$PHP_id,
	'rev_user'	=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],										
	'rev_date'	=>	$TODAY,
);

$f1 = $except->send_rev($parm);

$argv2 = array(
	"exc_id"	=>	$PHP_id,
	"user"		=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
	'item'		=>	$GLOBALS['SCACHE']['ADMIN']['dept'],
	"des"		=>	$PHP_detail
);

if($PHP_detail)	 $except->add_log($argv2);

// 取出該筆 order 資料 ------------------------------------------------- 
if (!$op=$except->get($PHP_id)) {
	$op['msg']= $except->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}

$ord_num = explode(',',$op['exc']['ord_num']);
for($i=0; $i<sizeof($ord_num); $i++)
{
	$ord_rec = $order->get('',$ord_num[$i]);
	$order->update_field('del_rev_date', date('Y-m-d'),$ord_rec['id']);
	$order->update_field('del_rev_user',$GLOBALS['SCACHE']['ADMIN']['login_id'],$ord_rec['id']);
}


$message = "Review exceptional On Order: [".$op['exc']['ord_num']."]";
$log->log_add(0,"075R",$message);		

$messg  = '<a href="exception.php?PHP_action=exc_show_apv&PHP_id='.$PHP_id.'&PHP_sr_exc=0">'.$op['exc']['ord_num'].'</a>';
$notify->system_msg_send('066-C','GM',$op['exc']['ord_num'],$messg);

$op['exc']['exc_des']=	str_replace( '刪除己核可之採購單務必追回己發出之採購合約', "<font color=red>刪除己核可之採購單務必追回己發出之採購合約</font>", $op['exc']['exc_des'] );


$op['back_str'] = $PHP_back_str;
$op['msg'][] = "訂單 : [ ".$PHP_ord_num." ]異常報告己CONFIRM";

page_display($op, '075', $TPL_EXC_SHOW_CFM);				
break;				



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		case  "reject_exc_cfm":		JOB 15A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
case "reject_exc_rev":
check_authority('075',"view");

$f1 = $except->update_fields('status', '5', $PHP_id);

$argv2 = array(
	"exc_id"	=>	$PHP_id,
	"user"		=>	$GLOBALS['SCACHE']['ADMIN']['login_id'],
	"item"		=>	"REJECT",
	"des"		=>	"REVIEW REJECT : for - ".$PHP_detail
);

$except->add_log($argv2);

// 取出該筆 order 資料 ------------------------------------------------- 
if (!$op=$except->get($PHP_id)) {
	$op['msg']= $except->msg->get(2);
	$layout->assign($op);
	$layout->display($TPL_ERROR);  		    
	break;
}

$ord_num = explode(',',$op['exc']['ord_num']);
for($i=0; $i<sizeof($ord_num); $i++)
{
	$ord_rec = $order->get('',$ord_num[$i]);
	
	$order->update_field('del_mk', 1, $ord_rec['id']);
	$order->update_field('del_date', '0000-00-00', $ord_rec['id']);
	$order->update_field('del_user', '', $ord_rec['id']);

	$order->update_field('del_cfm_date','',$ord_rec['id']);
	$order->update_field('del_cfm_user','',$ord_rec['id']);
}

$message = "Reject exceptional On Order: [".$op['exc']['ord_num']."]";
$log->log_add(0,"075S",$message);

$op['exc']['exc_des']=	str_replace( '刪除己核可之採購單務必追回己發出之採購合約', "<font color=red>刪除己核可之採購單務必追回己發出之採購合約</font>", $op['exc']['exc_des'] );

$op['back_str'] = $PHP_back_str;
$op['msg'][] = $message;

page_display($op, '075', $TPL_EXC_SHOW_REV);				
break;						



}   // end case ---------

?>
