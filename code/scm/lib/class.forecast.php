<?php 

#++++++++++++++++++++++ FORECAST  class ##### 預算  +++++++++++++++++++++++++++++++++++
#	->init($sql)							啟始 (使用 Msg_handle(); 先聯上 sql)
#	->add($parm)							加入
#	->search($mode=0)						搜尋   
#	->get($id=0, nbr=0)						抓出指定 記錄內資料   
#	->edit($parm)							更新 整筆資料
#	->update_field($parm)					更新 資料內 某個單一欄位
#	->del($id)								刪除 資料錄
#	->get_fields($n_field,$where_str="")	取出全部 部門表內的 $n_field 置入arry return
#
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class FORECAST {
		
	var $sql;
	var $msg ;
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->init($sql)	啟始(使用 Msg_handle() ; 先聯上 sql)
#		必需聯上 sql 才可  啟始
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function init($sql) {
		$this->msg = new MSG_HANDLE();

		if (!$sql) {
			$this->msg->add("Error ! Cannot connect database, please contact the Administrator.");
		    return false;
		}
		$this->sql = $sql;
		return true;
	} // end func

					
					
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新 forecast
#						傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function add($parm) {
            
$sql = $this->sql;

//查詢最後版本 
$q_str = "SELECT ver FROM cust WHERE cust_s_name='".$parm['cust']."' ORDER BY ver DESC LIMIT 1";
$q_result = $sql->query($q_str);
$cust_row = $sql->fetch($q_result);	

############### 檢查輸入項目	
//  輸入是否為數字項

# 加入資料庫
$q_str = "INSERT INTO forecast (year,method,fty,cust,cust_ver,uprc,top,botton,top_su,bottom_su,qty,cm,dept,fcst) VALUES ('".
$parm['year']."','".
$parm['method']."','".
$parm['fty']."','".
$parm['cust']."','".
$cust_row['ver']."','".
$parm['uprc']."','".
$parm['top']."','".
$parm['botton']."','".
$parm['top_su']."','".
$parm['bottom_su']."','".
$parm['qty']."','".
$parm['cm']."','".
$parm['dept']."','".
$parm['fcst']."')";

if (!$q_result = $sql->query($q_str)) {
    $this->msg->add("Error ! cannot access database");
    $this->msg->merge($sql->msg);
    return false;    
}

$new_id = $sql->insert_id();  //取出 新的 id

return $new_id;

} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit($parm)		更新 forecast 資料
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function edit($parm) {

$sql = $this->sql;
#####   更新資料庫內容

$q_str = "UPDATE `forecast` SET 
`uprc`        =   '".$parm['uprc']."', 
`qty`         =   '".$parm['qty']."', 
`top`         =   '".$parm['top']."', 
`botton`      =   '".$parm['botton']."', 
`top_su`      =   '".$parm['top_su']."', 
`bottom_su`   =   '".$parm['bottom_su']."', 
`cm`          =   '".$parm['cm']."', 
`fcst`        =   '".$parm['fcst']."'  
WHERE `id`    =   '".$parm['id']."' ;";

if (!$q_result = $sql->query($q_str)) {
    $this->msg->add("Error !  無法更新資料庫.");
    $this->msg->merge($sql->msg);
    return false;
}

return $parm['id'];
} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0, $where_str="")	搜尋  FORECAST 資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function search($mode=0, $where_str="") {

    $sql = $this->sql;
    $argv = $GLOBALS;   //將所有的 globals 都抓入$argv
    $srh = new SEARCH();
    if (!$srh->set_sql($sql)) {
        $this->msg->merge($srh->msg);
        return false;
    }
    $where_str=$where_str." and cust.cust_s_name=forecast.cust AND cust.ver = forecast.cust_ver ";
    $q_header = "SELECT forecast.*, cust_init_name as cust_iname FROM forecast ,cust ".$where_str;
// echo '<br>['.$q_header."]<br>";	 
    if (!$srh->add_q_header($q_header)) {
        $this->msg->merge($srh->msg);
        return false;
    }

    $result= $srh->send_query3();
    if (!is_array($result)) {
        $this->msg->merge($srh->msg);
        return false;		    
    }
    $this->msg->merge($srh->msg);
        if (!$result){   // 當查尋無資料時
            $op['record_NONE'] = 1;
        } else {
            $op['record_NONE'] = "";
        }

    $op['fcst'] = $result;  // 資料錄 拋入 $op
    $op['cgistr_get'] = $srh->get_cgi_str(0);
    $op['max_no'] = $srh->max_no;

    return $op;
} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0,$parm='',$method='')	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get($id=0,$parm='',$method='') {

		$sql = $this->sql;
		$where_str = '';

		if ($id)	{
			$q_str = "SELECT * FROM forecast WHERE id='$id' ";
		} elseif($parm) {

			if(isset($parm['fty'])){
				if ($where_str) { $where_str = $where_str." AND ";   }
				$where_str = $where_str ."fty ='".$parm['fty']."' ";
			}
			if(isset($parm['year'])){
				if ($where_str) { $where_str = $where_str." AND ";   }
				$where_str = $where_str ."year ='".$parm['year']."' ";
			}
			if(isset($parm['cust'])){
				if ($where_str) { $where_str = $where_str." AND ";   }
				$where_str = $where_str ."cust ='".$parm['cust']."' ";
			}
			if(isset($parm['dept'])){
				if ($where_str) { $where_str = $where_str." AND ";   }
				$where_str = $where_str ."dept ='".$parm['dept']."' ";
			}
			if($method){
				if ($where_str) { $where_str = $where_str." AND ";   }
				$where_str = $where_str ."method ='".$method."' ";
			}

			// if($where_str) { $where_str = " WHERE ".$where_str; }

			$q_str = "SELECT * FROM forecast WHERE ".$where_str;
		} else {
			$this->msg->add("Error ! please specify searching data for forecast table.");		    
			return false;
		}



		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			$this->msg->add("Error ! cannot find this record!");
			return false;    
		}
		return $row;
	} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($fty, $year,  $cat='capacity')	抓出指定記錄內資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_fcst($dept, $year, $cat='') {

		$sql = $this->sql;
		$where_str = '';
		
		if($dept) $where_str .= " AND dept = '".$dept."'";
		if($cat) $where_str .= " AND c_type= '".$cat."'";
		
		$q_str = "SELECT * FROM sales_forecast WHERE year='".$year."'".$where_str;
//echo $q_str."<BR>";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot access database, pls try later !");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			$this->msg->add("Sorry ! cannot find dept[".$dept."] of year:[".$year."] for [".$cat."] record!");
			return false;    
		}
		return $row;
	} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

} // end class


?>