<?php 

#++++++++++++++++++++++ WI  class ##### 製造令  +++++++++++++++++++++++++++++++++++
#	->init($sql)							啟始 (使用 Msg_handle(); 先聯上 sql)
#	->add($parm)							加入
#	->search($mode=0)						搜尋   
#	->get($id=0, nbr=0)						抓出指定 記錄內資料   
#	->add_edit($parm)						更新 製造令 記錄 [ 加入新 製令的step2]
#	->edit($parm)							更新 整筆資料
#	->update_field($parm)					更新 資料內 某個單一欄位
#	->del($id)								刪除 資料錄
#	->get_fields($n_field,$where_str="")	取出全部 部門表內的 $n_field 置入arry return
#
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class WI {
		
	var $sql;
	var $msg ;
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->init($sql)	啟始(使用 Msg_handle() ; 先聯上 sql)
#		必需聯上 sql 才可  啟始
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function init($sql) {
		$this->msg = new MSG_HANDLE();

		if (!$sql) {
			$this->msg->add("Error ! 無法聯上資料庫.");
		    return false;
		}
		$this->sql = $sql;
		return true;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->check($parm)		檢查 加入新 製造令記錄 是否正確
#						
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function check($parm) {

		$this->msg = new MSG_HANDLE();
			############### 檢查輸入項目
		$TODAY = date('Y-m-d');
		if(!$ord = $GLOBALS['order']->get($parm['smpl_id'])){
			$this->msg->add("Error ! Can't find this order record!");
			return false;
		}	
		if (!$parm['style_code'] ) {
			$this->msg->add("Error ! Please choice order on order list 。");
		}
		if (!$parm['size_scale'] ) {
			$this->msg->add("Error ! Make <b>Size Range</b> first, before father step.");
		}

		
		if (!$parm['unit'] ) {
			$this->msg->add("Error ! Please select Unit。");
		}

		
		if (!$parm['etd'] ) {
			$this->msg->add("Error ! Please input ETD date。");
		}
		if ($parm['etd'] < $TODAY && $ord['etd'] <> $parm['etd']) //檢查ETD是否大於今日
		{
			$this->msg->add("Sorry ! please update your unreasonable ETD ( After today at least)。");
		}
		if($parm['etd']< $ord['etp'] && $ord['etd'] <> $parm['etd']){  //檢查ETD是否大於ETP
			$this->msg->add("ERROR ! ETD < ETP。");			
		}

		if (count($this->msg->get(2))){
			return false;
		}
				
		return true;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->check2($parm)		檢查 加入記錄 是否正確 [ edit 時的 check ]
#						
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function check2($parm) {

		$this->msg = new MSG_HANDLE();
			
			############### 檢查輸入項目	
		$TODAY = date('Y-m-d');
		if(!$ord = $GLOBALS['order']->get($parm['smpl_id'])){
			$this->msg->add("Error ! Can't find this order record!");
			return false;
		}
		if (!$parm['etd'] ) {
			$this->msg->add("Error ! Please input date。");
		}
		if ($ord['etd'] <> $parm['etd'])
		{
			if ($parm['etd'] < $TODAY) //檢查ETD是否大於今日
			{
				$this->msg->add("Sorry ! please update your unreasonable ETD ( After today at least)。");
				return false;
			}
			if($parm['etd']< $ord['etp']){  //檢查ETD是否大於ETP
				$this->msg->add("ERROR ! ETD < ETP。");			
				return false;
			}
		}
		return true;

	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->check3($parm)		檢查 加入記錄 是否正確 [ step2 時的 check ]
#						
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function check3($parm) {

		$this->msg = new MSG_HANDLE();
			
			############### 檢查輸入項目	

		//  能檢查的是 主副料用料預估的輸入項目 



		return true;

	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新 樣本 製造令記錄
#						傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add($parm) {
					
		$sql = $this->sql;

//查詢最後版本 
		$q_str = "SELECT cust_ver as ver FROM s_order WHERE order_num='".$parm['wi_num']."' LIMIT 1";
		$q_result = $sql->query($q_str);
		$cust_row = $sql->fetch($q_result);	

					# 加入資料庫
		$q_str = "INSERT INTO wi (style_code,
									dept,
									cust,
									cust_ver,
									wi_num,
									smpl_id,
									etd,
									unit,									
									creator,
									open_date,
									version
									) VALUES('".
									$parm['style_code']."','".
									$parm['dept']."','".
									$parm['cust']."','".
									$cust_row['ver']."','".
									$parm['wi_num']."','".
									$parm['smpl_id']."','".
									$parm['etd']."','".
									$parm['unit']."','".									
									$parm['creator']."',
									NOW(),".
									$parm['version'].")";
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't add any data.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$pdt_id = $sql->insert_id();  //取出 新的 id

		$this->msg->add("Successfully add wi record: [".$parm['wi_num']."]。") ;

		return $pdt_id;

	} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0, $where_str='')	搜尋 製造令 資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function search($mode=0,$rule=0) {

    $sql = $this->sql;
    $argv = $GLOBALS;   //將所有的 globals 都抓入$argv

    $srh = new SEARCH();
    $cgi = array();
    if (!$srh->set_sql($sql)) {
        $this->msg->merge($srh->msg);
        return false;
    }

    $q_header = "SELECT DISTINCT bom_lots.ap_mark as lots_mark,bom_acc.ap_mark as acc_mark,wi.*, cust.cust_init_name as cust_iname, s_order.factory, s_order.etd FROM cust, s_order , wi left join bom_lots on ( wi.id = bom_lots.wi_id AND bom_lots.ap_mark = '' ) left join bom_acc on ( wi.id = bom_acc.wi_id AND bom_acc.ap_mark = '' ) ";
    if (!$srh->add_q_header($q_header)) {
        $this->msg->merge($srh->msg);
        return false;
    }
    $srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
    $srh->add_sort_condition("wi.id DESC");
    
    # 分頁
    $srh->row_per_page = 18;
	if($limit_entries) {
        $srh->q_limit = "LIMIT ".$limit_entries." ";
	} else {
		$pagesize=10;
		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
	}

	# 分部門顯示
    $user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
    $user_team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
    if ($user_team == 'MD') {
        $srh->add_where_condition("wi.dept = '$user_dept'", "",$user_dept,"Department=[ $user_dept ]. ");
    } else {
        if ( if_factory() && !$argv['PHP_factory'] ) {
            $srh->add_where_condition("s_order.factory = '$user_dept'", "",$user_dept,"Factory=[ $user_dept ]. ");
        } else {
            $dept_group = get_dept_group();
            for ($i=0; $i< sizeof($dept_group); $i++) {
                $srh->or_where_condition("wi.dept = '$dept_group[$i]'", "",$dept_group[$i],"Department=[ $dept_group[$i] ]. ");
            }
        }
    }
    
    if ($mode==1){
        if ($str = $argv['PHP_dept_code'] )  { 
            $srh->add_where_condition("wi.dept = '$str'", "PHP_dept_code",$str,"Dept. = [ $str ]. "); }
        
        if ($str = $argv['PHP_cust'] )  { 
            $srh->add_where_condition("wi.cust = '$str'", "PHP_cust",$str,"Search customer = [ $str ]. "); }
        if ($str = $argv['PHP_wi_num'] )  { 
            $srh->add_where_condition("wi.wi_num LIKE '%$str%'", "PHP_wi_num",$str,"Search wi # above: [ $str ] "); }
        if ($str = $argv['PHP_etdstr'] )  { 
            $srh->add_where_condition("wi.etd >= '$str'", "PHP_etdstr",$str,"search etd>=[ $str ]. "); 
            }
        if ($str = $argv['PHP_etdfsh'] )  { 
            $srh->add_where_condition("wi.etd <= '$str'", "PHP_etdfsh",$str,"search etd<=[ $str ]. "); 
            }	
        if ($str = $argv['PHP_fty_sch'] )  { 
            $srh->add_where_condition(" s_order.factory = '$str'", "PHP_fty_sch",$str,"search Factory=[ $str ]. "); 
        }	
    }
    
    $srh->add_where_condition("wi.cust = cust.cust_s_name AND wi.cust_ver = cust.ver");
    $srh->add_where_condition("s_order.cust = cust.cust_s_name AND s_order.cust_ver = cust.ver");
    $srh->add_where_condition("wi.wi_num = s_order.order_num");
    $srh->add_where_condition("s_order.status >=0");
    $srh->add_where_condition("wi.status > 0");
    
    $result= $srh->send_query2();
    if (!is_array($result)) {
        $this->msg->merge($srh->msg);
        return false;		    
    }
    $this->msg->merge($srh->msg);
    if (!$result){   // 當查尋無資料時
        $op['record_NONE'] = 1;
    }
    $op['wi'] = $result;  // 資料錄 拋入 $op
    $op['cgistr_get'] = $srh->get_cgi_str(0);
    $op['cgistr_post'] = $srh->get_cgi_str(1);
    $op['prev_no'] = $srh->prev_no;
    $op['next_no'] = $srh->next_no;
    $op['max_no'] = $srh->max_no;
    $op['last_no'] = $srh->last_no;
    $op['start_no'] = $srh->start_no;
    $op['per_page'] = $srh->row_per_page;
    // echo $srh->q_str;
    ##--*****--2006.11.16頁碼新增 start			
    $op['maxpage'] =$srh->get_max_page();
    $op['pages'] = $pages;
    $op['now_pp'] = $srh->now_pp;
    $op['lastpage']=$pages[$pagesize-1];		
    ##--*****--2006.11.16頁碼新增 end

    return $op;
} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0, $where_str='')	搜尋 製造令 資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function search_uncfm($mode=0) {

    $sql = $this->sql;
    $argv = $GLOBALS;   //將所有的 globals 都抓入$argv

    $srh = new SEARCH();
    $cgi = array();
    if (!$srh->set_sql($sql)) {
        $this->msg->merge($srh->msg);
        return false;
    }

    if ($mode=="wi") $q_header = "SELECT distinct wi.*, cust.cust_init_name as cust_iname, s_order.size, order_partial.id as p_id, order_partial.p_etd as etd, s_order.partial_num, order_partial.mks FROM wi, wiqty, cust, s_order, order_partial ";
    if ($mode=="bom") $q_header="SELECT distinct wi.*, cust.cust_init_name as cust_iname, s_order.factory FROM `wi`,bom_acc, cust, s_order";
    if (!$srh->add_q_header($q_header)) {
        $this->msg->merge($srh->msg);
        return false;
    }
    $srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
    $srh->add_sort_condition("id DESC");

    # 分頁
    $srh->row_per_page = 18;
	if($limit_entries) {
        $srh->q_limit = "LIMIT ".$limit_entries." ";
	} else {
		$pagesize=10;
		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
	}

	# 分部門顯示
    $user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
    $user_team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
    if ($user_team == 'MD') {
        $srh->add_where_condition("wi.dept = '$user_dept'", "",$user_dept,"Department=[ $user_dept ]. ");
    } else {
        if ( if_factory() && !$argv['PHP_factory'] ) {
            $srh->add_where_condition("s_order.factory = '$user_dept'", "",$user_dept,"Factory=[ $user_dept ]. ");
        } else {
            $dept_group = get_dept_group();
            for ($i=0; $i< sizeof($dept_group); $i++) {
                $srh->or_where_condition("wi.dept = '$dept_group[$i]'", "",$dept_group[$i],"Department=[ $dept_group[$i] ]. ");
            }
        }
    }

    if ($mode=="wi") {
        $srh->add_where_condition("order_partial.wi_status=0");	
        $srh->add_where_condition("wi.id=wiqty.wi_id");
        $srh->add_where_condition("s_order.order_num = order_partial.ord_num");
        $srh->add_where_condition("wi.wi_num = order_partial.ord_num");
        $srh->add_where_condition("order_partial.id=wiqty.p_id");
        //$srh->add_group_condition("wiqty.wi_id, wiqty.p_id");	
    }
    if ($mode=="bom") {
        $srh->add_where_condition("wi.status=1");	
        $srh->add_where_condition("wi.bom_status=2");	
        $srh->add_where_condition("wi.id=bom_acc.wi_id");			
    }
    $srh->add_where_condition("wi.wi_num=s_order.order_num");	
    $srh->add_where_condition("s_order.cust = cust.cust_s_name AND s_order.cust_ver = cust.ver");  
    $srh->add_where_condition("wi.cust = cust.cust_s_name AND wi.cust_ver = cust.ver");   // 關聯式察尋 必然要加
    // $srh->add_where_condition("wi.dept = cust.dept");   // 關聯式察尋 必然要加
    $srh->add_where_condition("s_order.status >=0");
    // $srh->add_where_condition("s_order.status !=10");
    // $srh->add_where_condition("s_order.status !=12");
    $result= $srh->send_query2();
    if (!is_array($result)) {
        $this->msg->merge($srh->msg);
        return false;		    
    }
    $this->msg->merge($srh->msg);
    if (!$result){   // 當查尋無資料時
        $op['record_NONE'] = 1;
    }
    $op['wi'] = $result;  // 資料錄 拋入 $op
    $op['cgistr_get'] = $srh->get_cgi_str(0);
    $op['cgistr_post'] = $srh->get_cgi_str(1);
    $op['prev_no'] = $srh->prev_no;
    $op['next_no'] = $srh->next_no;
    $op['max_no'] = $srh->max_no;
    $op['last_no'] = $srh->last_no;
    $op['start_no'] = $srh->start_no;
    $op['per_page'] = $srh->row_per_page;
    // echo $srh->q_str;
    ##--*****--2006.11.16頁碼新增 start			
    $op['maxpage'] =$srh->get_max_page();
    $op['pages'] = $pages;
    $op['now_pp'] = $srh->now_pp;
    $op['lastpage']=$pages[$pagesize-1];		
    ##--*****--2006.11.16頁碼新增 end

    return $op;
} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $wi_num=0)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get($id=0, $wi_num=0) {

		$sql = $this->sql;
		
		if ($id) {
			$q_str = "SELECT `wi`.*,`s_order`.`etd` FROM wi , `s_order` WHERE `wi`.`wi_num` = `s_order`.`order_num` and `wi`.`id`='$id' ";
		} elseif ($wi_num) {
			$q_str = "SELECT `wi`.*,`s_order`.`etd` FROM wi , `s_order` WHERE `wi`.`wi_num` = `s_order`.`order_num` and `wi`.`wi_num`='$wi_num' ";
		} else {
			$this->msg->add("Error ! Please point wi ID.");		    
			return false;
		}

		if (!$q_result = $sql->query($q_str)) {
			// echo "query error...";exit;
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			$this->msg->add("NOTICS :  <b> WI & BOM </b> not built yet or unavailable!");
			return false;    
		}
//將LOGIN_ID改成名字
		$tmp_user=$GLOBALS['user']->get(0,$row['creator']);
		$row['creator_id'] = $row['creator'];
		if ($tmp_user['name'])$row['creator'] = $tmp_user['name'];
		
		$tmp_user=$GLOBALS['user']->get(0,$row['updator']);
		$row['updator_id'] = $row['updator'];
		if ($tmp_user['name'])$row['updator'] = $tmp_user['name'];
		
 		$tmp_user=$GLOBALS['user']->get(0,@$row['bupdator']);
		$row['updator_id'] = @$row['bupdator'];
		if ($tmp_user['name'])$row['bupdator'] = $tmp_user['name'];
		
		$tmp_user=$GLOBALS['user']->get(0,$row['confirmer']);
		$row['confirmer_id'] = $row['confirmer'];
		if ($tmp_user['name'])$row['confirmer'] = $tmp_user['name'];
		
		$tmp_user=$GLOBALS['user']->get(0,$row['bcfm_user']);
		$row['bcfm_user_id'] = $row['bcfm_user'];
		if ($tmp_user['name'])$row['bcfm_user'] = $tmp_user['name'];
		
		$tmp_user=$GLOBALS['user']->get(0,$row['ti_cfm_user']);
		$row['ti_cfm_user_id'] = $row['ti_cfm_user'];
		if ($tmp_user['name'])$row['ti_cfm_user'] = $tmp_user['name'];

		$row['trim_user_id'] = $row['trim_user'];
		if ($tmp_user['name'])$row['trim_user'] = $tmp_user['name'];	

		$row['trim_cfm_user_id'] = $row['trim_cfm_user'];
		if ($tmp_user['name'])$row['trim_cfm_user'] = $tmp_user['name'];	
		
		return $row;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $wi_num=0)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_all($id=0, $wi_num=0,$mks='A') {

$sql = $this->sql;
if(!isset($_SESSION['partial_id']))$_SESSION['partial_id']='';
$p_id = $_SESSION['partial_id'];
if ($id)	{
	$q_str = "SELECT wi.* , order_partial.p_etd as etd, order_partial.p_etp as etp FROM wi , order_partial WHERE order_partial.ord_num = wi.wi_num and order_partial.mks = '$mks' and wi.id='$id' ";
} elseif ($wi_num) {
	$q_str = "SELECT wi.* , order_partial.p_etd as etd, order_partial.p_etp as etp FROM wi , order_partial WHERE order_partial.ord_num = wi.wi_num and order_partial.mks = '$mks' and wi.wi_num='$wi_num' ";
} else {
	$this->msg->add("Error ! Please point wi ID.");		    
	return false;
}

if (!$q_result = $sql->query($q_str)) {
	$this->msg->add("Error ! Can't access database!");
	$this->msg->merge($sql->msg);
	return false;    
}
if (!$row = $sql->fetch($q_result)) {
	$this->msg->add("Error ! Can't find this record!");
	return false;    
}

$op['wi']=$row;
$op['wi']['open_date']=substr($op['wi']['open_date'],0,10);
$op['wi']['last_update']=substr($op['wi']['last_update'],0,10);
$op['wi']['cfm_date']=substr($op['wi']['cfm_date'],0,10);

//改變Login帳號為名字
$tmp_user=$GLOBALS['user']->get(0,$op['wi']['creator']);
$op['wi']['creator_id'] = $op['wi']['creator'];
if ($tmp_user['name'])$op['wi']['creator'] = $tmp_user['name'];			
$tmp_user=$GLOBALS['user']->get(0,$op['wi']['updator']);
$op['wi']['updator_id'] = $op['wi']['updator'];
if ($tmp_user['name'])$op['wi']['updator'] = $tmp_user['name'];			
$tmp_user=$GLOBALS['user']->get(0,$op['wi']['confirmer']);
$op['wi']['confirmer_id'] = $op['wi']['confirmer'];
if ($tmp_user['name'])$op['wi']['confirmer'] = $tmp_user['name'];			
$tmp_user=$GLOBALS['user']->get(0,$op['wi']['bcfm_user']);
$op['wi']['bcfm_user_id'] = $op['wi']['bcfm_user'];
if ($tmp_user['name'])$op['wi']['bcfm_user'] = $tmp_user['name'];			
$tmp_user=$GLOBALS['user']->get(0,$op['wi']['ti_cfm_user']);
$op['wi']['ti_cfm_user_id'] = $op['wi']['ti_cfm_user'];
if ($tmp_user['name'])$op['wi']['ti_cfm_user'] = $tmp_user['name'];			


if($p_id)
{
	$q_str = "SELECT wi_status FROM order_partial WHERE id='$p_id' ";
	$q_result = $sql->query($q_str);
	$row = $sql->fetch($q_result);
	$op['wi']['status'] = $row['wi_status'];
}

//order記錄		
if(!$op['smpl'] = $GLOBALS['order']->get($op['wi']['smpl_id'])){
	$this->msg->add("Error ! Can't find this order record!");
	return false;
}		
$p_mk =0;
for($i=0; $i<sizeof($op['smpl']['ps_id']); $i++) {
	if($op['smpl']['ps_id'][$i] == $p_id) {
		$p_mk=1;
		$op['smpl']['etd'] = $op['smpl']['ps_etd'][$i];
		$op['smpl']['etp'] = $op['smpl']['ps_etp'][$i];
		$op['smpl']['qty'] = $op['smpl']['ps_qty'][$i];
		$op['smpl']['su'] = $op['smpl']['ps_su'][$i];
		break;				
	}			
}

//ti記錄
$where_str = " WHERE wi_id = '".$op['wi']['id']."' AND ti_new = 0";
$T = $GLOBALS['ti']->search(0,$where_str);  //取出該筆 樣本主料記錄
$op['ti'] = $T['ti'];

$where_str = " WHERE wi_id = '".$op['wi']['id']."' AND item = 'Comment' AND ti_new = 1";
$T = $GLOBALS['ti']->search(0,$where_str);  //取出該筆 樣本主料記錄		
if (count($T['ti'])) {
	$op['comment']['detail'] = $T['ti'][0]['detail'];
	$op['comment']['id'] = $T['ti'][0]['id'];
	$op['comm_flag'] = 1;
} else {
	$op['comm_flag'] = 0;
}

$sub_item = array();
$q_str = "SELECT sub_item FROM ti WHERE wi_id='".$op['wi']['id']."' AND ti_new = 1  GROUP BY sub_item ORDER BY id ";

if (!$q_result = $sql->query($q_str)) {
	$this->msg->add("Error ! Can't access database!");
	$this->msg->merge($sql->msg);
	return false;    
}

while ($row = $sql->fetch($q_result)) {
	$sub_item[] = $row['sub_item'];
}

for($i=0; $i<sizeof($sub_item); $i++)
{
	$where_str = " WHERE wi_id = '".$op['wi']['id']."' AND ti_new = 1 AND item <> 'Comment' AND sub_item = '".$sub_item[$i]."' ";
	$T = $GLOBALS['ti']->search(0,$where_str,'id');  //取出該筆 樣本主料記錄	
   
	if (count($T['ti']))
	{
		 if($sub_item[$i] == '')$sub_item[$i] ='main';
		 $op['sub_ti'][$i]['name'] = $sub_item[$i];
		 
		 
		 $op['sub_ti'][$i]['i'] = $i;
		 $op['sub_ti'][$i]['new_ti'] = 0;
		 $op['sub_ti'][$i]['txt'] = $T['ti'];
	}
}

//圖片位置
$style_dir	= "./picture/";  
$no_img		= "./images/graydot.gif";
if(file_exists($style_dir.$op['wi']['style_code'].".jpg")){
		$op['wi']['pic_url'] = $style_dir.$op['wi']['style_code'].".jpg";
} else {
		$op['wi']['pic_url'] = $no_img;
}

//  取出主料記錄 ----------------------------------------------------
// $where_str = " WHERE `wi_id` = '".$op['wi']['id']."' ";
$where_str = " WHERE smpl_code = '".$op['wi']['style_code']."' ";
$lots = $GLOBALS['smpl_lots']->search(0,$where_str);  //取出該筆 樣本主料記錄
// $lots = $GLOBALS['smpl_lots']->get_lots(0,$where_str);  //取出該筆 樣本主料記錄
$op['lots_use'] = $lots;

if (!is_array($op['lots_use'])) {
	$this->msg->add("Error ! Can't find this lots record!");
	return false;
}

if (!count($op['lots_use'])){	$op['lots_NONE'] = "1";		}

//  取出副料料記錄 ---------------------------------------------------
// $where_str = " WHERE `wi_id` = '".$op['wi']['id']."' ";
$where_str = " WHERE smpl_code = '".$op['wi']['style_code']."' ";
$acc = $GLOBALS['smpl_acc']->search(0,$where_str);  //取出該筆 樣本主料記錄
// $acc = $GLOBALS['smpl_acc']->get_acc(0,$where_str);  //取出該筆 樣本主料記錄
$op['acc_use'] = $acc;

if (!is_array($op['acc_use'])) {     
	$this->msg->add("Error ! Can't find this acc record!");
	return false;
}

if (!count($op['acc_use'])){ $op['acc_NONE'] = "1"; }

return $op;
} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add_edit($parm)		更新 製造令 記錄 [ 加入新 製令的step2]
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_edit($parm) {

		$sql = $this->sql;

		#####   更新資料庫內容
		$q_str = "UPDATE wi SET etd='"			.$parm['etd'].
							"',	smpl_type='"	.$parm['smpl_type'].
							"', unit='"			.$parm['unit'].
							"'  WHERE id='"		.$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$pdt_id = $parm['id'];

		
		
		return $pdt_id;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit($parm)		更新 製造令 記錄
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function edit($parm) {

		$sql = $this->sql;

		#####   更新資料庫內容
		$q_str = "UPDATE wi SET version='"	.$parm['version'].							
							"', etd='"			.$parm['etd'].
							"', unit='"			.$parm['unit'].
							"', updator='"		.$parm['updator'].
							"', last_update=	NOW()" .
						"  WHERE id='"			.$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  Can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$pdt_id = $parm['id'];
		
		return $pdt_id;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		更新 資料記錄內 某個單一欄位
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field($parm) {

		$sql = $this->sql;
		############### 檢查輸入項目
		
		#####   更新資料庫內容
	$q_str = "UPDATE wi SET ".$parm[1]."='".$parm[2]."'  WHERE id=".$parm[0]." ";
// echo $q_str.'<br>';
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		更新 資料記錄內 某個單一欄位
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field_num($parm) {

		$sql = $this->sql;
		############### 檢查輸入項目
		
		#####   更新資料庫內容
	$q_str = "UPDATE wi SET ".$parm[1]."='".$parm[2]."'  WHERE wi_num='".$parm[0]."' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del($id,$mode=0)		刪除 製作令記錄  [由ID]刪除
#							$mode=0: $id= 記錄之id; $mode<>0: $id=製令編號 wi_num
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del($id,$mode=0) {

		$sql = $this->sql;
		if (!$id) {
			$this->msg->add("Error ! Please point wi ID.");		    
			return false;
		}
		if($mode){
			$q_str = "DELETE FROM wi WHERE wi_num='$id' ";
		}else{
			$q_str = "DELETE FROM wi WHERE id='$id' ";
		}

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database !");
			$this->msg->merge($sql->msg);
			return false;    
		}



		return true;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->fty_pattern_del($id)		刪除 fty pattern  [由ID]刪除
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function fty_pattern_del($id) {

		$sql = $this->sql;
		if (!$id) {
			$this->msg->add("Error ! Please point wi ID.");		    
			return false;
		}
		$q_str = "DELETE FROM fty_pattern WHERE id='$id' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database !");
			$this->msg->merge($sql->msg);
			return false;    
		}
		return true;
	} // end func
	
		
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->order_del($id,$mode=0)		刪除 WI相關記錄  [由Order_NUM]刪除
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function order_del($wi_num) {
		$sql = $this->sql;
		$q_str = "DELETE FROM acc_use WHERE smpl_code='$wi_num' ";  //刪除副料的使用
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database !");
			$this->msg->merge($sql->msg);
			return false;    
		}			

		$q_str = "DELETE FROM lots_use WHERE smpl_code='$wi_num' "; //刪除主料的使用
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database !");
			$this->msg->merge($sql->msg);
			return false;    
		}		

		$q_str = "DELETE FROM file_det WHERE num='$wi_num' "; //刪除主料的使用
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database !");
			$this->msg->merge($sql->msg);
			return false;    
		}		

	
		$q_str = "SELECT id FROM wi WHERE wi_num = '".$wi_num."'";	//查詢訂單的WI所存的ID
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database !");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$row = $sql->fetch($q_result);
		if (isset($row['id']))
		{
			$q_str = "DELETE FROM wiqty WHERE wi_id='".$row['id']."' "; //刪除Wiqty(colorway)內容
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! Can't access database !");
				$this->msg->merge($sql->msg);
				return false;    
			}
			$q_str = "DELETE FROM ti WHERE wi_id='".$row['id']."' "; //刪除製作說明
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! Can't access database !");
				$this->msg->merge($sql->msg);
				return false;    
			}				
			$q_str = "DELETE FROM bom_acc WHERE wi_id='".$row['id']."' "; //刪除副料的預估需求量
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! Can't access database !");
				$this->msg->merge($sql->msg);
				return false;    
			}		
			$q_str = "DELETE FROM bom_lots WHERE wi_id='".$row['id']."' "; //刪除主料的預估需求量
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! Can't access database !");
				$this->msg->merge($sql->msg);
				return false;    
			}		
		}
		$q_str = "DELETE FROM wi WHERE wi_num='$wi_num' "; //刪除製造令
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database !");
			$this->msg->merge($sql->msg);
			return false;    
		}		

		return true;
	} // end func


/*
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fields($n_field,$where_str="")	取出全部 部門表內的 $n_field 置入arry return
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_fields($n_field,$where_str="") {
		$sql = $this->sql;
		$fields = array();
		$q_str = "SELECT ".$n_field." FROM wi ".$where_str;

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}

			$match_limit = 500;
			$match = 0;
			while ($row = $sql->fetch($q_result)) {
				$fields[] = $row[0];
				$match++;
				if ($match==500) {
					break;
				}
			}
			if ($match != 500) {   // 保留 尚未作用
				$sql->free_result($q_result);
				$result =0;
				$this->q_result = $q_result;
			}
		
		return $fields;
	} // end func
*/
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		更新 資料記錄內 某個單一欄位
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function submits_wi($parm) {

		$sql = $this->sql;
		############### 檢查輸入項目
		
		#####   更新資料庫內容
	$q_str = "UPDATE wi SET status= '1' WHERE id='".$parm['id']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
	$q_str = "UPDATE s_order SET m_status = '2' where order_num = '".$parm['order_num']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		return true;
	} // end func
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		更新 資料記錄內 某個單一欄位
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function cfm($parm) {

		$sql = $this->sql;
		############### 檢查輸入項目
		
		#####   更新資料庫內容
	$q_str = "UPDATE order_partial SET wi_status = '1' where id = '".$_SESSION['partial_id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}

	$q_str = "SELECT id FROM order_partial where ord_num = '".$parm['order_num']."' AND wi_status = 0";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if($row = $sql->fetch($q_result))
		{
			return false;			
		}else{
			$q_str = "UPDATE wi SET status= '1', cfm_date='".$parm['date']."', confirmer='".$parm['user']."' WHERE id='".$parm['id']."'";
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! can't update database.");
				$this->msg->merge($sql->msg);
				return false;    
			}
			$q_str = "UPDATE s_order SET m_status = '2' where order_num = '".$parm['order_num']."'";
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! can't update database.");
				$this->msg->merge($sql->msg);
				return false;    
			}
		}
		return true;
	} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		更新 資料記錄內 某個單一欄位
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function revise($parm) {

	$sql = $this->sql;
	############### 檢查輸入項目
	
	#####   更新資料庫內容
$q_str = "UPDATE wi SET status= '0', cfm_date='".$parm['date']."', confirmer='".$parm['user']."', revise='".$parm['rev']."' WHERE id='".$parm['id']."'";
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! can't update database.");
		$this->msg->merge($sql->msg);
		return false;    
	}
$q_str = "UPDATE s_order SET m_status = '1' where order_num = '".$parm['order_num']."'";
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! can't update database.");
		$this->msg->merge($sql->msg);
		return false;    
	}
$q_str = "UPDATE order_partial SET wi_status = '0' where id = '".$_SESSION['partial_id']."'";
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! can't update database.");
		$this->msg->merge($sql->msg);
		return false;    
	}		
	return true;
} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field2($parm) {

		$sql = $this->sql;
		############### 檢查輸入項目
		
		#####   更新資料庫內容
	$q_str = "UPDATE `wi` SET `".$parm[1]."`='0', `".$parm[2]."`='', `".$parm[3]."`='' WHERE `id` = '".$parm[0]."' ;";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		更新 資料記錄內 某個單一欄位
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function submits_bom($parm) {

		$sql = $this->sql;
		############### 檢查輸入項目
		
		#####   更新資料庫內容
	$q_str = "UPDATE wi SET status= '2' WHERE id='".$parm['id']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
	$q_str = "UPDATE s_order SET m_status = '3' where order_num = '".$parm['order_num']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		return true;
	} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		更新 資料記錄內 某個單一欄位
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function bom_cfm($parm) {

		$sql = $this->sql;
		############### 檢查輸入項目
		
		#####   更新資料庫內容
	$q_str = "UPDATE wi SET status= '2', bcfm_date='".$parm['date']."', bcfm_user='".$parm['user']."' WHERE id='".$parm['id']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
	$q_str = "UPDATE s_order SET m_status = '3' where order_num = '".$parm['order_num']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		return true;
	} // end func
	

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		更新 資料記錄內 某個單一欄位 order_num
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function revise_bom($parm) {

    $sql = $this->sql;
    ############### 檢查輸入項目
    
    #####   更新資料庫內容
 	$q_str = "UPDATE wi SET status= '1', cfm_date='".$parm['date']."', confirmer='".$parm['user']."', bcfm_date='".$parm['date']."', bcfm_user='".$parm['user']."', bom_rev='".$parm['bom_rev']."' WHERE id='".$parm['id']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
	$q_str = "UPDATE s_order SET m_status = '2' where order_num = '".$parm['order_num']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
    return true;
} // end func
	
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		更新 資料記錄內 某個單一欄位
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function check_size($num) {

		$sql = $this->sql;
		############### 檢查輸入項目
		$flag=false;
		#####   更新資料庫內容
		$q_str = "SELECT id FROM wi WHERE style_code='".$num."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			$this->msg->add("Error ! 無法找到這筆記錄!");
			return true;    
		}
		$id=$row['id'];
		$q_str = "SELECT id FROM wiqty WHERE wi_id='".$id."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$sql->num_rows($q_result) ) {
			$flag=true;    	    
		}

	
		return $flag;
	} // end func
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->copy_wi($parm)		更新 資料記錄內 某個單一欄位
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function copy_wi($smpl_num,$smpl_id,$new_num,$etd,$ord_cust,$dept,$user,$date)
{
	
		$sql = $this->sql;
		$q_str = "SELECT * FROM smpl_ord WHERE num like'".$smpl_num."%' Order By id DESC Limit 1 ";		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$smpl = $sql->fetch($q_result)) {
			$this->msg->add("Error ! Can't find this sample order record!");
			return false;    
		}
		$wi_num=$smpl['num'];
		$q_str="UPDATE s_order SET size ='".$smpl['size']."' where order_num='".$new_num."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		
		$q_str = "SELECT * FROM smpl_acc_use WHERE smpl_code='".$wi_num."' ";		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$j=0;
		while ($row = $sql->fetch($q_result)) {
			$str_acc[$j]= "insert into `acc_use` set ";
			foreach ($row as $key => $value)
			{
				if ($key=='use_for')
				{
					for ($i=0; $i< sizeof($GLOBALS['chin_error_word']); $i++)
					{
						$value = str_replace($GLOBALS['chin_error_word'][$i],$GLOBALS['chin_error_word'][$i]."\\",$value);
					}
					$value = str_replace("'","\'",$value);		
				}
				if (!is_int($key) && $key<>'id' && $key<>'smpl_code' && $key<>'est_1') 
					$str_acc[$j]=$str_acc[$j].$key."='".$value."',";					
			}
			$str_acc[$j]=$str_acc[$j]."smpl_code='".$new_num."'";
			$j++;
			
		}
		
		$q_str = "SELECT * FROM smpl_lots_use WHERE smpl_code='".$wi_num."' ";		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$j=0;
		while ($row = $sql->fetch($q_result)) {
			$str_lots[$j]= "insert into `lots_use` set ";
			foreach ($row as $key => $value)
			{
				if ($key=='use_for')
				{
					for ($i=0; $i< sizeof($GLOBALS['chin_error_word']); $i++)
					{
						$value = str_replace($GLOBALS['chin_error_word'][$i],$GLOBALS['chin_error_word'][$i]."\\",$value);
					}
					$value = str_replace("'","\'",$value);		
				}
				if (!is_int($key) && $key<>'id' && $key<>'smpl_code' && $key<>'est_1') 
					$str_lots[$j]=$str_lots[$j].$key."='".$value."',";					
			}
			$str_lots[$j]=$str_lots[$j]."smpl_code='".$new_num."'";
			$j++;
			
		}
	
		if (isset($str_lots))
		{
			for ($i=0; $i<sizeof($str_lots); $i++)
			{
				if (!$q_result = $sql->query($str_lots[$i])) {
					$this->msg->add("Error ! Can't access sample_lots_use table for add!".$i);
					$this->msg->merge($sql->msg);
					return false;    
				}
			}
		}
		if (isset($str_acc))
		{
			for ($i=0; $i<sizeof($str_acc); $i++)
			{
				if (!$q_result = $sql->query($str_acc[$i])) {
					$this->msg->add("Error ! Can't access sample_acc_use table for add!".$i);
					$this->msg->merge($sql->msg);
					return false;    
				}
			}
		}
		
		
				
		$q_str = "SELECT * FROM smpl_wi WHERE style_code='".$wi_num."'";				
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$j=0;
		if (!$row = $sql->fetch($q_result)) {
//查詢最後版本 
		$q_str = "SELECT ver FROM cust WHERE cust_s_name='".$ord_cust."' ORDER BY ver DESC LIMIT 1";
		$q_result = $sql->query($q_str);
		$cust_row = $sql->fetch($q_result);	


			$str_wi= "insert into `wi` set ";
			$str_wi=$str_wi."wi_num='".$new_num."', style_code='".$new_num."', smpl_id='".$smpl_id.
											"', etd='".$etd."', open_date='".$date."', creator='".$user.
											"', version='001', dept='".$dept."', cust_ver='".$cust_row['ver'].
											"', cust='".$ord_cust."', unit='pc'";    
			
		}else{
			$old_id=$row['id'];
			$str_wi= "insert into `wi` set ";
			foreach ($row as $key => $value)
			{
				if (!is_int($key) && ($key=='dept' || $key=='cust' || $key=='smpl_type' || $key=='unit' || $key=='cust_ver')) 
					$str_wi=$str_wi.$key."='".$value."',";					
			}
			$str_wi=$str_wi."wi_num='".$new_num."', style_code='".$new_num."', smpl_id='".$smpl_id."', etd='".$etd."', open_date='".$date."', creator='".$user."', version='001'";
		}	
		
		if (!$q_result = $sql->query($str_wi)) {
			$this->msg->add("Error ! Can't access sample_wi table for add!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		
		$pdt_id = $sql->insert_id();  //取出 新的 id
//		$pdt_id='999';	
		
		
		if (isset($old_id))
		{
//			$q_str = "SELECT * FROM smpl_wiqty WHERE wi_id='".$old_id."' ";		
//			if (!$q_result = $sql->query($q_str)) {
//				$this->msg->add("Error ! 無法存取資料庫!");
//				$this->msg->merge($sql->msg);
//				return true;    
//			}
//			$j=0;
//			while ($row_add = $sql->fetch($q_result)) {
//				$str_wiqty[$j]= "insert into `wiqty` set ";
//				foreach ($row_add as $key => $value)
//				{
//					if (!is_int($key) && $key<>'id' && $key<>'wi_id') 
//						$str_wiqty[$j]=$str_wiqty[$j].$key."='".$value."', ";					
//				}
//				$str_wiqty[$j]=$str_wiqty[$j]." wi_id='".$pdt_id."'";
//				$j++;
//			}
			
			$q_str = "SELECT * FROM smpl_ti WHERE wi_id='".$old_id."' ORDER BY id";		
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! 無法存取資料庫!");
				$this->msg->merge($sql->msg);
				return true;    
			}
			$j=0;
			while ($row_add = $sql->fetch($q_result)) {
				$str_ti[$j]= "insert into `ti` set ";
				foreach ($row_add as $key => $value)
				{					
					if ($key=='detail') $value=str_replace("'", "\'",$value);
					if ($key=='item') $value=str_replace("'", "\'",$value);
					if ($key=='detail' || $key=='item')
					{
						for ($i=0; $i< sizeof($GLOBALS['chin_error_word']); $i++)
						{
							$value = str_replace($GLOBALS['chin_error_word'][$i],$GLOBALS['chin_error_word'][$i]."\\",$value);
						}
					}
					if (!is_int($key) && $key<>'id' && $key<>'wi_id') 
						$str_ti[$j]=$str_ti[$j].$key."='".$value."', ";					
				}
				$str_ti[$j]=$str_ti[$j]." wi_id='".$pdt_id."'";
				$j++;	
			}
						
			
			if (isset($str_wiqty))
			{
				for ($i=0; $i<sizeof($str_wiqty); $i++)
				{
					if (!$q_result = $sql->query($str_wiqty[$i])) {
						$this->msg->add("Error ! Can't access sample_wiqty table for add!".$i);
						$this->msg->merge($sql->msg);
						return true;    
					}
				}
			}
			
			if (isset($str_ti))
			{
				for ($i=0; $i<sizeof($str_ti); $i++)
				{
					if (!$q_result = $sql->query($str_ti[$i])) {
						$this->msg->add("Error ! Can't access sample_ti table for add!".$i);
						$this->msg->merge($sql->msg);
						return true;    
					}
				}
			}
			
			
			
		}
		
		return $pdt_id;
		
}

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		更新 資料記錄內 某個單一欄位
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_field($num,$field) {

		$sql = $this->sql;
		
		$q_str = "SELECT $field FROM wi WHERE wi_num='".$num."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			$this->msg->add("Error ! 無法找到這筆記錄!");
			return false;    
		}
	
		return $row;
	} // end func




#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0, $where_str='')	搜尋 製造令 資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_bom_copy($sch_cust) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv

		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT wi.* FROM wi ";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_sort_condition("id DESC");
		$srh->row_per_page = 9999;
	
	$srh->add_where_condition("wi.status = 2");	
	$srh->add_where_condition("wi.cust = '$sch_cust'");
		
		$result= $srh->send_query2();
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
			if (!$result){   // 當查尋無資料時
				$op['record_NONE'] = 1;
			}
		$op['wi'] = $result;  // 資料錄 拋入 $op

		return $op;
	} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->copy_search($cust, $dept)	搜尋 製造令 資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function copy_search($cust, $dept) {
		
		$search_day = increceDaysInDate(date('Y-m-d'),-360);
		$return = array();
		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv

		$srh = new SEARCH();
		// $q_str = "SELECT id, wi_num FROM wi WHERE dept = '".$dept."' AND cust = '".$cust."' AND cfm_date > '".$search_day."'";
		// $q_str = "SELECT id, wi_num FROM wi WHERE dept = '".$dept."' AND cust = '".$cust."' ;";
		// $q_str = "SELECT id, wi_num FROM wi WHERE cust = '".$cust."' ;";
		$q_str = "SELECT id, wi_num FROM wi WHERE cfm_date > '".$search_day."';";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$i=$j=0;
		while ($row = $sql->fetch($q_result)) {
			if ($j > 5)
			{
				$j=0; 
				$i++;
			}
			 $return[$i]['id'.$j]=$row['id'];
			 $return[$i]['wi_num'.$j]=$row['wi_num'];
			 $j++;
		}


		return $return;
	} // end func	
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->copy_search($cust, $dept)	搜尋 製造令 資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function copy_search_all() {
		
		$search_day = increceDaysInDate(date('Y-m-d'),-400);
		$return = array();
		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv

		$srh = new SEARCH();
		$q_str = "SELECT id, wi_num FROM wi 
							WHERE cfm_date > '".$search_day."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$i=$j=0;
		while ($row = $sql->fetch($q_result)) {
			if ($j > 5)
			{
				$j=0; 
				$i++;
			}
			 $return[$i]['id'.$j]=$row['id'];
			 $return[$i]['wi_num'.$j]=$row['wi_num'];
			 $j++;
		}


		return $return;
	} // end func		
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->copy_wi($parm)		更新 資料記錄內 某個單一欄位
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function copy_ord_wi($wi_num,$smpl_id,$new_num,$etd,$ord_cust,$dept,$user,$date)
{
	
		$sql = $this->sql;
		
		$ord_rec = $GLOBALS['order']->get('',$new_num);
		if(isset($ord_rec['carry_ord']) && sizeof($ord_rec['carry_ord']) > 0)$st_mk = 1; else $st_mk = 0;
		
		$q_str = "SELECT size FROM s_order WHERE order_num = '".$wi_num."'";		

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$smpl = $sql->fetch($q_result)) {
			$this->msg->add("Error ! Can't find this sample order record!");
			return false;    
		}
		$q_str="UPDATE s_order SET size ='".$smpl['size']."' where order_num='".$new_num."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		
		$q_str = "SELECT * FROM acc_use WHERE smpl_code='".$wi_num."' ";		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$j=0;
		while ($row = $sql->fetch($q_result)) {
			$str_acc[$j]= "insert into `acc_use` set ";
			foreach ($row as $key => $value)
			{
				if ($key=='use_for')
				{
					for ($i=0; $i< sizeof($GLOBALS['chin_error_word']); $i++)
					{
						$value = str_replace($GLOBALS['chin_error_word'][$i],$GLOBALS['chin_error_word'][$i]."\\",$value);
					}
					$value = str_replace("'","\'",$value);		
				}
				if (!is_int($key) && $key<>'id' && $key<>'smpl_code' && $key<>'est_1') 
					$str_acc[$j]=$str_acc[$j].$key."='".$value."',";					
			}
			$str_acc[$j]=$str_acc[$j]."smpl_code='".$new_num."'";
			$j++;
			
		}
		
		$q_str = "SELECT * FROM lots_use WHERE smpl_code='".$wi_num."' ";		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$j=0;
		while ($row = $sql->fetch($q_result)) {
			$str_lots[$j]= "insert into `lots_use` set ";
			foreach ($row as $key => $value)
			{
				if ($key=='use_for')
				{
					for ($i=0; $i< sizeof($GLOBALS['chin_error_word']); $i++)
					{
						$value = str_replace($GLOBALS['chin_error_word'][$i],$GLOBALS['chin_error_word'][$i]."\\",$value);
					}
					$value = str_replace("'","\'",$value);		
				}
				if (!is_int($key) && $key<>'id' && $key<>'smpl_code' && $key<>'est_1') 
					$str_lots[$j]=$str_lots[$j].$key."='".$value."',";					
			}
			$str_lots[$j]=$str_lots[$j]."smpl_code='".$new_num."'";
			$j++;
			
		}
	
		if (isset($str_lots))
		{
			for ($i=0; $i<sizeof($str_lots); $i++)
			{
				if (!$q_result = $sql->query($str_lots[$i])) {
					$this->msg->add("Error ! Can't access sample_lots_use table for add!".$i);
					$this->msg->merge($sql->msg);
					return false;    
				}
			}
		}
		if (isset($str_acc))
		{
			for ($i=0; $i<sizeof($str_acc); $i++)
			{
				if (!$q_result = $sql->query($str_acc[$i])) {
					$this->msg->add("Error ! Can't access sample_acc_use table for add!".$i);
					$this->msg->merge($sql->msg);
					return false;    
				}
			}
		}
		
		
				
		$q_str = "SELECT * FROM wi WHERE wi_num = '".$wi_num."'";				
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$j=0;
		if (!$row = $sql->fetch($q_result)) {
			$str_wi= "insert into `wi` set ";
			$str_wi=$str_wi."wi_num='".$new_num."', style_code='".$new_num."', smpl_id='".$smpl_id."', etd='".$etd."', open_date='".$date."', creator='".$user."', version='001', dept='".$ord_rec['dept']."', cust='".$ord_rec['cust']."', cust_ver='".$ord_rec['cust_ver']."', unit='pc'";    
			
		}else{
			$old_id=$row['id'];
			$str_wi= "insert into `wi` set ";
			$str_wi.="dept='".$ord_rec['dept']."', cust='".$ord_rec['cust']."', cust_ver='".$ord_rec['cust_ver']."', unit='".$ord_rec['unit']."',";

			$str_wi=$str_wi."wi_num='".$new_num."', style_code='".$new_num."', smpl_id='".$smpl_id."', etd='".$etd."', open_date='".$date."', creator='".$user."', version='001'";
		}	
		if (!$q_result = $sql->query($str_wi)) {
			$this->msg->add("Error ! Can't access sample_wi table for add!");
			$this->msg->merge($sql->msg);
			return false;    
		}		
		
		$pdt_id = $sql->insert_id();  //取出 新的 id
//		$pdt_id='999';	
		
		
		if (isset($old_id))
		{
//			$q_str = "SELECT * FROM wiqty WHERE wi_id='".$old_id."' ";		
//			if (!$q_result = $sql->query($q_str)) {
//				$this->msg->add("Error ! 無法存取資料庫!");
//				$this->msg->merge($sql->msg);
//				return true;    
//			}
//			$j=0;
//			while ($row_add = $sql->fetch($q_result)) {
//				
//				$tmp = explode(',',$row_add['qty']);
//				$row_add['qty'] = '';
//				for($i=0;$i<sizeof($tmp); $i++)$row_add['qty'].=',';
//				$row_add['qty'] = substr($row_add['qty'],0,-1);
//				$str_wiqty[$j]= "insert into `wiqty` set ";
//				foreach ($row_add as $key => $value)
//				{
//					if (!is_int($key) && $key<>'id' && $key<>'wi_id') 
//						$str_wiqty[$j]=$str_wiqty[$j].$key."='".$value."', ";					
//				}
//				$str_wiqty[$j]=$str_wiqty[$j]." wi_id='".$pdt_id."'";
//				$j++;
//			}
			
			$q_str = "SELECT * FROM ti WHERE wi_id='".$old_id."' ORDER BY id";		
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! 無法存取資料庫!");
				$this->msg->merge($sql->msg);
				return true;    
			}
			$j=0;
			while ($row_add = $sql->fetch($q_result)) {
				$str_ti[$j]= "insert into `ti` set ";
				foreach ($row_add as $key => $value)
				{					
					if($st_mk == 1 && $key=='detail')$value='本訂單為跟單生產,詳細請參考 : '.$wi_num;
					if ($key=='detail' || $key == 'item') $value=str_replace("'", "\'",$value);
					if ($key=='detail' || $key == 'item')
					{
						for ($i=0; $i< sizeof($GLOBALS['chin_error_word']); $i++)
						{
							$value = str_replace($GLOBALS['chin_error_word'][$i],$GLOBALS['chin_error_word'][$i]."\\",$value);
						}
					}
					if (!is_int($key) && $key<>'id' && $key<>'wi_id') 
						$str_ti[$j]=$str_ti[$j].$key."='".$value."', ";					
				}
				$str_ti[$j]=$str_ti[$j]." wi_id='".$pdt_id."'";
				$j++;	
			}
						
			
			if (isset($str_wiqty))
			{
				for ($i=0; $i<sizeof($str_wiqty); $i++)
				{
					if (!$q_result = $sql->query($str_wiqty[$i])) {
						$this->msg->add("Error ! Can't access sample_wiqty table for add!".$i);
						$this->msg->merge($sql->msg);
						return true;    
					}
				}
			}
			
			if (isset($str_ti))
			{
				for ($i=0; $i<sizeof($str_ti); $i++)
				{
					if (!$q_result = $sql->query($str_ti[$i])) {
						$this->msg->add("Error ! Can't access sample_ti table for add!".$i);
						$this->msg->merge($sql->msg);
						return true;    
					}
				}
			}
			
			
			
		}
		
		return $pdt_id;
		
}	


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->check_po($id)	檢查PO是否存在	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function check_po($id) {
		
		$sql = $this->sql;

		$srh = new SEARCH();
		$q_str = "SELECT id FROM bom_acc 
							WHERE wi_id = $id AND ap_mark <> ''";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}

		while ($row = $sql->fetch($q_result)) {
			return 0;
		}

		$q_str = "SELECT id FROM bom_lots 
							WHERE wi_id = $id AND ap_mark <> ''";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}

		while ($row = $sql->fetch($q_result)) {
			return 0;
		}

		return 1;
	} // end func	
	
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->revise_with_po($parm)
#		在有PO的狀況下更新WI
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function revise_with_po($parm) {

	$sql = $this->sql;
	############### 檢查輸入項目
	
	#####   更新資料庫內容
	$q_str = "
	UPDATE wi SET status= '0',
	rev_po_mk='1',
	cfm_date='0000-00-00 00:00:00',
	confirmer='',
	revise='".$parm['rev']."'
	WHERE id='".$parm['id']."'";

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! can't update database.");
		$this->msg->merge($sql->msg);
		return false;
	}
	
	$q_str = "UPDATE s_order SET m_status = '1' where order_num = '".$parm['order_num']."'";
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! can't update database.");
		$this->msg->merge($sql->msg);
		return false;
	}

	$q_str = "UPDATE order_partial SET wi_status = '0' where id = '".$_SESSION['partial_id']."'";
	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! can't update database.");
		$this->msg->merge($sql->msg);
		return false;
	}

	return true;
} // end func	

	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search_un_fab_comp()	搜尋 製造令 未輸入主料用量 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function search_un_fab_comp($mode=0) {

    $sql = $this->sql;
    $argv = $GLOBALS;   //將所有的 globals 都抓入$argv

    $srh = new SEARCH();
    $cgi = array();
    if (!$srh->set_sql($sql)) {
        $this->msg->merge($srh->msg);
        return false;
    }

    $q_header = "SELECT distinct wi.*, cust.cust_init_name as cust_iname, 
                                          size_des.size_scale, size_des.base_size, s_order.smpl_ord, s_order.style 
                            FROM wi, cust, s_order, size_des ";
    if (!$srh->add_q_header($q_header)) {
        $this->msg->merge($srh->msg);
        return false;
    }
    $srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
    $srh->add_sort_condition("wi.marked, s_order.etd");

    # 分頁
    $srh->row_per_page = 18;
	if($limit_entries) {
        $srh->q_limit = "LIMIT ".$limit_entries." ";
	} else {
		$pagesize=10;
		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
	}

	# 分部門顯示
    $user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
    $user_team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
    if ($user_team == 'MD') {
        $srh->add_where_condition("s_order.dept = '$user_dept'", "",$user_dept,"Department=[ $user_dept ]. ");
    } else {
        if ( if_factory() && !$argv['PHP_factory'] ) {
            $srh->add_where_condition("s_order.factory = '$user_dept'", "",$user_dept,"Factory=[ $user_dept ]. ");
        } else {
            $dept_group = get_dept_group();
            for ($i=0; $i< sizeof($dept_group); $i++) {
                $srh->or_where_condition("s_order.dept = '$dept_group[$i]'", "",$dept_group[$i],"Department=[ $dept_group[$i] ]. ");
            }
        }
    }

    if ($mode==1){
        if ($str = $argv['PHP_cust'] ) {
            $srh->add_where_condition("wi.cust = '$str'", "PHP_cust",$str,"Search customer = [ $str ]. ");
        }
        if ($str = $argv['PHP_num'] ) {
            $srh->add_where_condition("wi.wi_num LIKE '%$str%'", "PHP_wi_num",$str,"Search wi # above: [ $str ] ");
        }
        if ($str = $argv['PHP_etdstr'] ) {
            $srh->add_where_condition("wi.etd >= '$str'", "PHP_etdstr",$str,"search etd>=[ $str ]. "); 
        }
        if ($str = $argv['PHP_etdfsh'] ) {
            $srh->add_where_condition("wi.etd <= '$str'", "PHP_etdfsh",$str,"search etd<=[ $str ]. "); 
        }	
        if ($str = $argv['PHP_sch_fty'] ) {
            $srh->add_where_condition(" s_order.factory = '$str'", "PHP_sch_fty",$str,"search Factory=[ $str ]. "); 
        }	
    }

    $srh->add_where_condition("(wi.status = 1 || (wi.status > 1 && wi.marked = 0))");	
//		$srh->add_where_condition("wi.marked = 0");	
    $srh->add_where_condition("wi.wi_num=s_order.order_num");
    $srh->add_where_condition("wi.cust = cust.cust_s_name AND wi.cust_ver = cust.ver");   // 關聯式察尋 必然要加
    $srh->add_where_condition("s_order.cust = cust.cust_s_name AND s_order.cust_ver = cust.ver");
    $srh->add_where_condition("s_order.status >=0");
    $srh->add_where_condition("s_order.size = size_des.id");
    // $srh->add_where_condition("s_order.status < 8");
    $srh->add_where_condition("s_order.status < 8 || s_order.status = 13");
    $result= $srh->send_query2();
    if (!is_array($result)) {
        $this->msg->merge($srh->msg);
        return false;		    
    }
    
    $this->msg->merge($srh->msg);
    if (!$result){   // 當查尋無資料時
        $op['record_NONE'] = 1;
    }
    
    $op['wi'] = $result;  // 資料錄 拋入 $op
    $op['cgistr_get'] = $srh->get_cgi_str(0);
    $op['cgistr_post'] = $srh->get_cgi_str(1);
    $op['prev_no'] = $srh->prev_no;
    $op['next_no'] = $srh->next_no;
    $op['max_no'] = $srh->max_no;
    $op['last_no'] = $srh->last_no;
    $op['start_no'] = $srh->start_no;
    $op['per_page'] = $srh->row_per_page;
    // echo $srh->q_str;
    ##--*****--2006.11.16頁碼新增 start			
    $op['maxpage'] =$srh->get_max_page();
    $op['pages'] = $pages;
    $op['now_pp'] = $srh->now_pp;
    $op['lastpage']=$pages[$pagesize-1];		
    ##--*****--2006.11.16頁碼新增 end

    return $op;
} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->copy_ti_search()	搜尋 製造令 資料	 2011/11/14
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function copy_ti_search($cust, $dept) {
		
		// $search_day = increceDaysInDate(date('Y-m-d'),-270);
		$return = array();
		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv

		$srh = new SEARCH();
		// $q_str = "SELECT id, wi_num FROM wi WHERE dept = '".$dept."' AND cust = '".$cust."' AND ti_cfm > '".$search_day."'";
		// $q_str = "SELECT id, wi_num FROM wi WHERE dept = '".$dept."' AND cust = '".$cust."' ;";
		$q_str = "SELECT id, wi_num FROM wi WHERE cust = '".$cust."' ;";
		
		// echo $q_str;
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$i=$j=0;
		while ($row = $sql->fetch($q_result)) {
			if ($j > 5)
			{
				$j=0; 
				$i++;
			}
			 $return[$i]['id'.$j]=$row['id'];
			 $return[$i]['wi_num'.$j]=$row['wi_num'];
			 $j++;
		}


		return $return;
}	

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->copy_ti($parm)	2011/11/14
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function copy_ti($old_id,$pdt_id){
		$sql = $this->sql;
		
			$q_str = "SELECT * FROM ti WHERE wi_id='".$old_id."' and item <> 'Comment' ORDER BY id";		
			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! 無法存取資料庫!");
				$this->msg->merge($sql->msg);
				return true;    
			}
			$j=0;
			while ($row_add = $sql->fetch($q_result)) {
				$str_ti[$j]= "insert into `ti` set ";
				foreach ($row_add as $key => $value)
				{
					if ($key=='detail' || $key=='item')
					{
						for ($i=0; $i< sizeof($GLOBALS['chin_error_word']); $i++)
						{
							$value = str_replace($GLOBALS['chin_error_word'][$i],$GLOBALS['chin_error_word'][$i]."\\",$value);
						}
					}
					if ($key=='detail') $value=str_replace("'", "\'",$value);
					if ($key=='item') $value=str_replace("'", "\'",$value);
					if (!is_int($key) && $key<>'id' && $key<>'wi_id' && $key<>'date') 
						$str_ti[$j]=$str_ti[$j].$key."='".$value."', ";					
				}
				$str_ti[$j].="date='".date("Y-m-d")."',";
				$str_ti[$j]=$str_ti[$j]." wi_id='".$pdt_id."'";
				$j++;	
			}
			
			if (isset($str_ti)){
				for ($i=0; $i<sizeof($str_ti); $i++)
				{
					//echo $str_ti[$i]."<BR>";
					if (!$q_result = $sql->query($str_ti[$i])) {
						$this->msg->add("Error ! Can't access sample_ti table for add!".$i);
						$this->msg->merge($sql->msg);
						return true;    
					}
				}
			}
		return true;
}	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->Order_qty_sum($parm)		更新 Partial與order qty,重算SU並更新
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function order_qty_sum($partial_id) {

		$sql = $this->sql;
		############### 檢查輸入項目
		
		#####   更新資料庫內容

	$q_str = "select * from wiqty where p_id = '".$partial_id."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		while ($row = $sql->fetch($q_result)) {
			$sub_item[] = $row;
		}
		$partial_sum=0;
		foreach($sub_item as $key => $value)
		{
			$qtys = explode(",", $value['qty']);
			foreach($qtys as $key1 => $value1)
			{
				$partial_sum += $value1;
			}
			//print_r($qtys);
		}
		
		//echo $partial_sum;
		



		
		return $partial_sum;
	} 	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->urevise_qty($parm)		更新 Partial與order qty,重算SU並更新
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function revise_qty($ord_id,$ord_qty,$ord_su,$partial_id,$partial_qty,$partial_su) { 

		$sql = $this->sql;
		############### 檢查輸入項目
		
		#####   更新資料庫內容
		//print_r($parm);
		//exit;
		$q_str = "UPDATE s_order SET qty = ".$ord_qty." , su=".$ord_su." where id = ".$ord_id;
		//echo $q_str;
		//echo "<br>";
		if (!$q_result = $sql->query($q_str)) {
			//echo "false1";
			$this->msg->add("Error ! can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$q_str = "UPDATE order_partial SET p_qty = ".$partial_qty." , p_su=".$partial_su." where id = ".$partial_id;
		//echo $q_str;
		//echo "<br>";
		
		
		
		if (!$q_result = $sql->query($q_str)) {
			//echo "false2";
			$this->msg->add("Error ! can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		//print_r($parm);
		
		return true;
	} // end func
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->urevise_qty($parm)		更新 Partial與order qty,重算SU並更新
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_value($q_str) {

		$sql = $this->sql;
		############### 檢查輸入項目
		
		#####   更新資料庫內容
		//print_r($parm);
		//$q_str = "UPDATE s_order SET qty = ".$parm['order_qty']." , su=".$parm['order_su']." where id = ".$parm['order_id'];
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$ord_qty = $sql->fetch($q_result);
		return $ord_qty['qty'];
	} // end func
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->Sql Search 搜尋傳進來的語法要的東西，測試用(小錢)
#								
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function sql_search($q_str) {
//echo $q_str;
		$sql = $this->sql;
		
		//$q_str = "SELECT $field FROM wi WHERE wi_num='".$num."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! 無法存取資料庫.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$i=0;
		while ($row = $sql->fetch($q_result)) {
		$all_data[$i] = $row;
		$i++;
		}
		

		return $all_data;
	} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->check_con_po($acc_used_id)				
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function check_con_po($acc_used_id) {
		$sql = $this->sql;
		$q_str = "SELECT ap_mark
				  FROM bom_acc
				  WHERE acc_used_id = '".$acc_used_id."' and ap_mark <> ''";
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		$row = $sql->fetch($q_result);
		if($row['ap_mark']){
			return $row['ap_mark'];
		}else{
			return false;
		}
	}
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

} // end class


?>