<?php
session_start();
session_register	('sch_parm');




#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

require_once "config.php";
require_once "config.admin.php";

$PHP_SELF = $_SERVER['PHP_SELF'];

$perm = $GLOBALS['power'];

require_once "init.object.php";

$op = array();

//$ACC = $acc->get_acc_name();



switch ($PHP_action) {
//=======================================================

 
 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//			 job 81-1-2    每日產出量 月報表
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	case "monthy_daily_ouput":

		check_authority('055',"view");
		$TPL_CAPACITY_OUTPUT = "capacity_output.html";
		$op['fty'] = $PHP_fty;
		$op['year'] = $PHP_year;
		$op['month'] = $PHP_month;
		$op['ttl']['qty'] = $op['ttl']['out_qty'] = $op['ttl']['out_su'] = 0;
		$op['ord_out'] = $daily->get_month_output($PHP_fty,$PHP_year.'-'.$PHP_month); // 取出 業務的部門 [不含K0] ------
		for($i=0; $i<sizeof($op['ord_out']); $i++)
		{
			if(file_exists($GLOBALS['config']['root_dir']."/picture/".$op['ord_out'][$i]['order_num'].".jpg")){
				$op['ord_out'][$i]['main_pic'] = "./picture/".$op['ord_out'][$i]['order_num'].".jpg";
			} else {
				$op['ord_out'][$i]['main_pic'] = "./images/graydot.gif";
			}	
			$op['ttl']['qty'] += $op['ord_out'][$i]['qty'];
			$op['ttl']['out_qty'] += $op['ord_out'][$i]['out_qty'];
			$op['ttl']['out_su'] += $op['ord_out'][$i]['out_su'];
		}
		
		$m_key = "m".$PHP_month;
		if ($PHP_su <> $op['ttl']['out_su']){
			if (!$update = $capaci->update_field($PHP_fty,$PHP_year,"actual",$m_key,$op['ttl']['out_su'])) {   
				$op['msg']= "cannot update capacity database !, please contact system Administraor";
				$layout->assign($op);
				$layout->display($TPL_ERROR);  		    
				break;
			}
		}		
		page_display($op, '055', $TPL_CAPACITY_OUTPUT); 		    	    
		break;

  
  
  
  

	
//-------------------------------------------------------------------------

}   // end case ---------

?>
