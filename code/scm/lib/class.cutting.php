<?php 

#++++++++++++++++++++++ DAILY  class ##### 開發布料  +++++++++++++++++++++++++++++++++++
#	->init($sql)							啟始 (使用 Msg_handle(); 先聯上 sql)
#	->add($parm)							加入
#	->search($mode=0)						搜尋   
#	->get($id=0, nbr=0)						抓出指定 記錄內資料   
#	->edit($parm)							更新 整筆資料
#	->update_field($parm)					更新 資料內 某個單一欄位
#	->del($id)								刪除 資料錄
#	->get_fields($n_field,$where_str="")	取出全部 部門表內的 $n_field 置入arry return
#	->sum_cutting_out($year,$mon)	  加總 月份 之日產出數	[以array傳回 每日之 su ,qty] 
#	->sum_daily_subout($year,$mon)	  加總 月份 之日產出數(代工)	[以array傳回 每日之 su ,qty] 
#	->daily_subout($fty, $date)	搜尋  指定日期 指定工廠外發代工 之產出資料	 
#	->cutting_out($fty, $date)	搜尋  指定日期 指定工廠 之產出資料	 
#
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class CUTTING {

var $sql;
var $msg ;

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->init($sql)	啟始(使用 Msg_handle() ; 先聯上 sql)
#		必需聯上 sql 才可  啟始
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function init($sql) {
    $this->msg = new MSG_HANDLE();

    if (!$sql) {
        $this->msg->add("Error ! cannot connect database.");
        return false;
    }
    $this->sql = $sql;
    return true;
} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->check($parm)		檢查 加入新記錄 是否正確
#								# mode =0:一般add的check,  mode=1: edit時的check
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function check($parm) {

$this->msg = new MSG_HANDLE();
############### 檢查輸入項目

$T = $parm['qty'];
if ( $parm['qty'] <= 0){  // 必需為整數
    $this->msg->add("sorry! you have to key-in out-put Q'ty !");
    return false;
}		

if (!(is_numeric($T)&&(intval($T)==floatval($T)))){  // 必需為整數
    $this->msg->add("Error ! please input the correct qty [numeric only] 。");
    return false;
}

return true;
} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新 布料記錄
#						傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function add($parm) {

$sql = $this->sql;

# 加入資料庫
echo $q_str = "
INSERT INTO cutting_out (k_date,factory,ord_num,qty,su,p_id,user_id) 
VALUES(NOW(),'".
$parm['factory']."','".
$parm['ord_num']."','".
$parm['qty']."','".
$parm['su']."','".
$parm['p_id']."','".
$_SESSION['SCACHE']['ADMIN']['id']."'
)";

if (!$q_result = $sql->query($q_str)) {
    $this->msg->add("Error ! 無法新增資料記錄.");
    $this->msg->merge($sql->msg);
    return false;    
}

$cutting_id = $sql->insert_id();  //取出 新的 id

$this->msg->add("adding daily out record for : [".$parm['ord_num']."]。") ;

return $cutting_id;

} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->del($id)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function del($id) {

    $sql = $this->sql;
    if (!$id) {
        $this->msg->add("Error ! please idendify record ID for delete.");		    
        return false;
    }

    $q_str = "DELETE FROM cutting_out WHERE id = '".$id."' ;";

    if (!$q_result = $sql->query($q_str)) {
        $this->msg->add("Error ! cannot delete data file !");
        $this->msg->merge($sql->msg);
        return false;    
    }

    return true;
} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0,$where_str='')	搜尋  資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function search($parm,$mode=0) {

    $sql = $this->sql;
    $argv = $GLOBALS;   //將所有的 globals 都抓入$argv 
    $srh = new SEARCH();

    if (!$srh->set_sql($sql)) {
        $this->msg->merge($srh->msg);
        return false;
    }

    $q_str = "SELECT cutting_out.id, cutting_out.ord_num, cutting_out.qty, 
    cutting_out.su, cutting_out.factory, cutting_out.k_date, 
    s_order.unit,s_order.cust_po, s_order.style, s_order.etd, pdtion.qty_cut,
    (s_order.qty - pdtion.qty_cut) as rem_qty, s_order.style_num,
    s_order.qty as ord_qty , s_order.partial_num ,
    order_partial.id as p_id , order_partial.mks , order_partial.remark , order_partial.p_qty , order_partial.p_etd , user.name ,
    cust_init_name as cust_iname 
    FROM cutting_out , s_order, pdtion , cust , order_partial LEFT JOIN user ON ( user.id = cutting_out.user_id )
    WHERE 
    order_partial.id = cutting_out.p_id AND 
    cutting_out.ord_num = s_order.order_num AND 
    pdtion.order_num = s_order.order_num AND
    s_order.cust = cust.cust_s_name AND s_order.cust_ver = cust.ver AND
    cutting_out.ord_num = pdtion.order_num         
    ";

    $where_str = '';
    if ($mode==1){
        if ($str = $parm['factory'] )  $where_str .= " AND cutting_out.factory = '".$str."' ";
        if ($str = $parm['ord_num'] )  $where_str .= " AND cutting_out.ord_num LIKE '".$str."' ";
    }

    if (!$parm['ord_num'] ){
        if ($str = $parm['k_date'] )  $where_str .= " AND cutting_out.k_date = '".$str."' ";
    }

    $q_str = $q_str.$where_str. ' GROUP BY cutting_out.id';

    if (!$q_result = $sql->query($q_str)) {
        $this->msg->add("Error! 無法存取資料庫!");
        $this->msg->merge($sql->msg);
        return false;    
    }

    $rtn = array();
    while ($row = $sql->fetch($q_result)) {			
        $rtn[] = $row;
    }
    // echo $q_str;
    return $rtn;
} // end func




#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0,$where_str='')	搜尋  資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function ord_cut_search($ord_num) {

    $sql = $this->sql;
    $argv = $GLOBALS;   //將所有的 globals 都抓入$argv

    $q_str = "SELECT cutting_out.*
                      FROM   cutting_out
                        WHERE  cutting_out.ord_num = '".$ord_num."'";
     

    if (!$q_result = $sql->query($q_str)) {
        $this->msg->add("Error! 無法存取資料庫!");
        $this->msg->merge($sql->msg);
        return false;    
    }

    $rtn = array();
    while ($row = $sql->fetch($q_result)) {			
        $rtn[] = $row;
    }
    return $rtn;
} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		更新 副料 資料 某個單一欄位
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function update_field($parm) {

    $sql = $this->sql;
    ############### 檢查輸入項目
    
    #####   更新資料庫內容
    $q_str = "UPDATE cutting_out SET ".$parm['field_name']."='".$parm['field_value']."'  WHERE id='".$parm['id']."' ";

    if (!$q_result = $sql->query($q_str)) {
        $this->msg->add("Error ! 無法更新資料庫內容.");
        $this->msg->merge($sql->msg);
        return false;    
    }
    
    return true;
} // end func




#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fields($n_field,$where_str="")	取出全部 部門表內的 $n_field 置入arry return
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_fields($n_field,$where_str="") {
    $sql = $this->sql;
    $fields = array();
    $q_str = "SELECT ".$n_field." FROM cutting_out ".$where_str;

    if (!$q_result = $sql->query($q_str)) {
        $this->msg->add("Error! 無法存取資料庫!");
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




	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->ord_search($mode=0, $where_str='', $limit_entries=0)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function ord_search($mode=0, $where_str='', $limit_entries=0) {

    $sql = $this->sql;
    $argv = $GLOBALS;   //將所有的 globals 都抓入$argv

    $srh = new SEARCH();
    $cgi = array();
    if (!$srh->set_sql($sql)) {
        $this->msg->merge($srh->msg);
        return false;
    }

    $q_header = "SELECT s_order.* , pdtion.*, cust_init_name as cust_iname, s_order.id as s_id , order_partial.* , order_partial.id as p_id     
                             FROM s_order , pdtion , cust , order_partial ";
    if (!$srh->add_q_header($q_header)) {
        $this->msg->merge($srh->msg);
        return false;
    }
    $srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
    $srh->add_sort_condition("s_order.etd");
//		$srh->add_group_condition('s_order.order_num');
    $srh->row_per_page = 10;

if($limit_entries){    // 當有限定最大量時~~~ 2005/11/28 加入
        $srh->q_limit = "LIMIT ".$limit_entries." ";
}else{
#--*****--##  2006.11.14 以數字型式顯示頁碼 star		
    $pagesize=10;
    if ($argv['PHP_sr_startno']) {
        $pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
    }else{
        $pages = $srh->get_page(1,$pagesize);
    } 
##--*****--##  2006.11.14 以數字型式顯示頁碼 end
}

    $srh->add_where_condition("s_order.order_num = pdtion.order_num");   // 關聯式察尋 必然要加
    $srh->add_where_condition("s_order.order_num = order_partial.ord_num");   // 關聯式察尋 必然要加
    $srh->add_where_condition("s_order.cust = cust.cust_s_name AND s_order.cust_ver = cust.ver");   // 關聯式察尋 必然要加
//		$srh->add_where_condition("s_order.order_num = rcv_po_link.ord_num");   // 關聯式察尋 必然要加
//		$srh->add_where_condition("receive_det.id = rcv_po_link.rcv_id");   // 關聯式察尋 必然要加


    $user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
    $user_team = $GLOBALS['SCACHE']['ADMIN']['team_id'];
    $sales_dept = $GLOBALS['SALES_DEPT'];

    if ($user_team == 'MD')	$srh->add_where_condition("s_order.dept = '$user_dept'", "",$user_dept,"department=[ $user_dept ]. ");
    for ($i=0; $i< sizeof($sales_dept); $i++)
    {			
        if($user_dept == $sales_dept[$i] && $user_team <> 'MD') 	$srh->add_where_condition("s_order.dept = '$user_dept'", "",$user_dept,"department=[ $user_dept ]. ");
    } 	
	
if ($mode==2){
    $mesg = '';
    if ($str = strtoupper($argv['PHP_ref']) )  { 
        $srh->add_where_condition("s_order.ref LIKE '%$str%'", "PHP_ref",$str); 
        $mesg.= "  ref# : [ $str ]. ";
        }
    
    if ($str = $argv['PHP_cust'] )  { 
        $srh->add_where_condition("s_order.cust = '$str'", "PHP_cust",$str); 
        $mesg.= "  Cust. = [ $str ]. ";
        }
    if ($str = $argv['PHP_order_num'] )  { 
        $srh->add_where_condition("s_order.order_num LIKE '%$str%'", "PHP_order_num",$str); 
        $mesg.= "  Order# : [ $str ]. ";
        }
    if ($str = $argv['PHP_factory'] )  { 
        $srh->add_where_condition("s_order.factory = '$str'", "PHP_factory",$str); 
        $mesg.= "  FTY = [ $str ]. ";
        }
    if ($mesg)
    {
        $msg = "Search ".$mesg;
        $this->msg->add($msg);
    }
}		

    $today = date('Y-m-d');
    $srh->add_where_condition("s_order.status >= 4");
    if(empty($argv['PHP_finish']))
    $srh->add_where_condition("s_order.status < 10");
    // $srh->add_where_condition("order_partial.ext_qty > 0");
    $srh->add_where_condition("s_order.etd > '2014-01-01'");
    $srh->add_where_condition("s_order.smpl_apv <> '0000-00-00'");  
//		$srh->add_where_condition("pdtion.mat_rcv <= '".$today."' && !(pdtion.mat_rcv IS NULL)");
//		$srh->add_where_condition("!(pdtion.mat_shp IS NULL) || !(pdtion.mat_rcv IS NULL)");
//		$srh->add_where_condition("receive_det.mat_code like 'F%'");

    $result= $srh->send_query2($limit_entries);   // 2005/11/24 加入 $limit_entries
    if (!is_array($result)) {
        $this->msg->merge($srh->msg);
        return false;		    
    }
    $this->msg->merge($srh->msg);
        if (!$result){   // 當查尋無資料時
            $op['record_NONE'] = 1;
        }
    $op['sorder'] = $result;  // 資料錄 拋入 $op
    $op['cgistr_get'] = $srh->get_cgi_str(0);
    $op['cgistr_post'] = $srh->get_cgi_str(1);
    $op['max_no'] = $srh->max_no;
    // echo $srh->q_str;
##--*****--## 2006.11.14新頁碼需要的oup_put	start		
    $op['maxpage'] =$srh->get_max_page();
    $op['pages'] = $pages;
    $op['now_pp'] = $srh->now_pp;
$op['lastpage']=$pages[$pagesize-1];
##--*****--## 2006.11.14新頁碼需要的oup_put	end		

    return $op;
} // end func






#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->ord_search($mode=0, $where_str='', $limit_entries=0)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function daily_search($mode=0, $where_str='') {

    $sql = $this->sql;
    $argv = $_SESSION['sch_parm'];   //將所有的 globals 都抓入$argv

    $srh = new SEARCH();
    $cgi = array();
    if (!$srh->set_sql($sql)) {
        $this->msg->merge($srh->msg);
        return false;
    }

    $q_header = "SELECT k_date, sum(qty) as qty, sum(su) as su, factory  FROM cutting_out";
    $srh->add_q_header($q_header);
    
    $srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
    $srh->add_sort_condition("k_date");
    $srh->row_per_page = 31;
    $srh->add_group_condition('k_date');
    $pagesize=10;
    if(!$argv['PHP_sr_startno'])$argv['PHP_sr_startno'] = 1;
    $pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	 	

if ($mode==1){
    $mesg = '';
    if ($str = $argv['PHP_factory'] )  { 
        $srh->add_where_condition("factory = '$str'", "PHP_factory",$str); 
        $mesg.= "  FTY = [ $str ]. ";
        }
    if ($str = $argv['SCH_str'] )  { 
        $srh->add_where_condition("k_date >= '$str'", "SCH_str",$str); 
        $mesg.= "  Date >= [ $str ]. ";
        }	
    if ($str = $argv['SCH_end'] )  { 
        $srh->add_where_condition("k_date <= '$str'", "SCH_end",$str); 
        $mesg.= "  Date <= [ $str ]. ";
        }				
    if ($mesg)
    {
        $msg = "Search ".$mesg;
        $this->msg->add($msg);
    }
}		

    $result= $srh->send_query2();   // 2005/11/24 加入 $limit_entries
    if (!is_array($result)) {
        $this->msg->merge($srh->msg);
        return false;		    
    }
    $this->msg->merge($srh->msg);

    $op['cutting'] = $result;  // 資料錄 拋入 $op
    $op['cgistr_get'] = $srh->get_cgi_str(0);
    $op['cgistr_post'] = $srh->get_cgi_str(1);
    $op['max_no'] = $srh->max_no;
    
##--*****--## 2006.11.14新頁碼需要的oup_put	start		
    $op['maxpage'] =$srh->get_max_page();
    $op['pages'] = $pages;
    $op['now_pp'] = $srh->now_pp;
$op['lastpage']=$pages[$pagesize-1];
##--*****--## 2006.11.14新頁碼需要的oup_put	end		
    return $op;
} // end func


	



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->ord_cut_sum($ord_num)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function ord_cut_sum($ord_num) {

    $sql = $this->sql;

    $q_str = "SELECT sum(`qty`) as qty FROM `cutting_out` WHERE `ord_num` LIKE '".$ord_num."' GROUP BY `ord_num`";

    if (!$q_result = $sql->query($q_str)) {
        $this->msg->add("Error! 無法存取資料庫!");
        $this->msg->merge($sql->msg);
        return false;    
    }

    $qty = '';
    while ($row = $sql->fetch($q_result)) {			
        $qty = $row['qty'];
    }

    return $qty;
} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->mdf_ie($ord_num)
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function mdf_ie($order_num,$ie) {

$sql = $this->sql;

$q_str = "SELECT `id`,`qty` FROM `cutting_out` WHERE `ord_num` = '".$order_num."' ;";
// echo $q_str.'<br>';
if (!$q_result = $sql->query($q_str)) {
    $this->msg->add("Error! 無法存取資料庫!");
    $this->msg->merge($sql->msg);
    return false;    
}

$qty = '';
while ($row = $sql->fetch($q_result)) {			
    $qty = $row['qty'];
    $q_str = "UPDATE `cutting_out` SET `su` = '".set_su($ie,$row['qty'])."' WHERE `id` = '".$row['id']."' ;";
    // echo $q_str.'<br>';
    if (!$q_result = $sql->query($q_str)) {
        $this->msg->add("Error ! 無法更新資料庫內容.");
        $this->msg->merge($sql->msg);
        return false;    
    }
}

    return $qty;
} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

} // end class


?>