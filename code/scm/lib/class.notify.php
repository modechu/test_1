<?php 

#++++++++++++++++++++++ SUPL  class ##### 供應商  +++++++++++++++++++++++++++++++++++
#	->init($sql)							啟始 (使用 Msg_handle(); 先聯上 sql)
#	->add($parm)							加入
#	->search($mode=0)						Search   
#	->get($id=0, nbr=0)						抓出指定 記錄內資料   
#	->edit($parm)							更新 整筆資料
#	->update_field($parm)					更新 資料內 某個單一欄位
#	->del($id)								刪除 資料錄
#	->get_fields($n_field,$where_str="")	取出全部 部門表內的 $n_field 置入arry return
#
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class NOTIFY {
		
	var $sql;
	var $msg ;
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->init($sql)	啟始(使用 Msg_handle() ; 先聯上 sql)
#		必需聯上 sql 才可  啟始
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function init($sql) {
		$this->msg = new MSG_HANDLE();

		if (!$sql) {
			$this->msg->add("Error ! Can't connect database.");
		    return false;
		}
		$this->sql = $sql;
		return true;
	} // end func

					
					
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新 供應商
#						傳回 $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add($parm) {
					
		$sql = $this->sql;
		if(!isset($parm['backup'])) $parm['backup'] = 0;
			############### 檢查輸入項目	
		$q_str = "SELECT id FROM user WHERE login_id='".$parm['tuser']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$sql->num_rows($q_result) && $parm['tuser'] <> 'yankee' ) {
			$this->msg->add("SORRY ! This user (login-id) is not exist, Please check again!");
			return false;    	    
		}
		
		# 檢查是否有重覆
		$backup = ( $parm['backup'] == 1 )? 'y' : 'n' ;

		# 加入資料庫
		$q_str = "INSERT INTO `notify` SET 
		`t_user` 		= '".$parm['tuser']."',
		`f_user` 		= '".$parm['fuser']."',
		`title` 		= '".$parm['title']."',
		`msg` 			= '".$parm['msg']."',
		`sed_date`		= '".$parm['send_date']."',
		`backup`		= '".$backup."',
		`pop`			= '".$parm['pop']."' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't add data in database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$new_id = $sql->insert_id();  //取出 新的 id

//		$this->msg->add("成功 新增供應商 : [".$parm['cust_s_name']."]。"記錄) ;

		return $new_id;

	} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0, $where_str="")	Search  供應商 資料	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search($user, $where_str="") {

		$sql = $this->sql;
		$argv = $GLOBALS;   //將所有的 globals 都抓入$argv

		$srh = new SEARCH();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$q_header = "SELECT * FROM notify ";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		if(!empty($argv['msgr']))
		$srh->add_cgi_parm("msgr",$argv['msgr']);
		
		$srh->add_sort_condition("id DESC");
		
		$srh->row_per_page = 12;
		$pagesize=10;

		if ($argv['PHP_sr_startno'] && $where_str=="" ) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
		}else{
			$pages = $srh->get_page(1,$pagesize);
		}     	
#####2006.11.14 以數字型式顯示頁碼 end		
#####2006.11.14 以數字型式顯示頁碼 star

		if( !empty($argv['msgr']) && $argv['msgr']=='backup'){
			if ($str = $user ) $srh->add_where_condition("f_user  = '$str'");
			$srh->add_where_condition("backup = 'y'");  
		}else{
			if ($str = $user ) $srh->add_where_condition("t_user  = '$str'"); 
			$srh->add_where_condition("active = 'y'"); 
		}

		if ($where_str) $srh->add_where_condition($where_str); 


 		$result= $srh->send_query2();
		
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);

		if (!$result){   // 當查尋無資料時
			$op['record_NONE'] = 1;
		}

		$pop = 0;
		foreach($result as $key => $val){
			if( $val['pop'] == 'y' ) $pop = 1 ;
		}
		$op ['pop'] = $pop ;
		$op['notify'] = $result;  // 資料錄 拋入 $op		
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['cgistr_post'] = $srh->get_cgi_str(1);
		$op['prev_no'] = $srh->prev_no;
		$op['next_no'] = $srh->next_no;
		$op['max_no'] = $srh->max_no;
		$op['last_no'] = $srh->last_no;
		$op['start_no'] = $srh->start_no;
		$op['per_page'] = $srh->row_per_page;
		
#####2006.11.14新頁碼需要的oup_put	start
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] = $pages;
		$op['now_pp'] = $srh->now_pp;
		$op['lastpage']=$pages[$pagesize-1];	
#####2006.11.14新頁碼需要的oup_put	end
		return $op;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $supl_s_name=0)	抓出指定記錄內資料 RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get($id=0, $vndr_no=0) {

		$sql = $this->sql;

		if ($id)	{
			$q_str = "SELECT * FROM supl WHERE id='$id' ";
		} elseif ($vndr_no) {
			$q_str = "SELECT * FROM supl WHERE vndr_no='$vndr_no' ";
		} else {
			$this->msg->add("Error ! Please point supplier ID.");		    
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
		return $row;
	} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		更新 供應商資料 某個單一欄位
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field($parm) {

		$sql = $this->sql;
		############### 檢查輸入項目
		
		#####   更新資料庫內容
		$q_str = "UPDATE `notify` SET `".$parm['field_name']."` = '".$parm['field_value']."' , `pop` = 'n'  WHERE id = '".$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't update database.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return $q_str;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del($id)		刪除 供應商 資料  [由ID]刪除
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del($id,$msg='read') {

		$sql = $this->sql;
		if (!$id) {
			$this->msg->add("Error !Please point supplier ID.");		    
			return false;
		}
		
		if($msg=='read'){
			$q_str = "UPDATE `notify` SET `active` = 'n'  WHERE id = '".$id."' ";
		}else{
			$q_str = "UPDATE `notify` SET `backup` = 'n'  WHERE id = '".$id."' ";
		}

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Can't access database !");
			$this->msg->merge($sql->msg);
			return false;    
		}		

		$q_str = "SELECT `backup`,`active` FROM `notify` WHERE id = '".$id."' ORDER BY id";
		$q_result = $sql->query($q_str);    
		$row = $sql->fetch($q_result); 

		if($row['backup']=='n' && $row['active']=='n'){
			$q_str = "DELETE FROM `notify` WHERE `id`= '$id' ";
			$sql->query($q_str);
		}

		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fields($n_field,$where_str="")	取出全部 部門表內的 $n_field 置入arry return
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_fields($n_field,$where_str="") {
		$sql = $this->sql;
		$fields = array();
		$q_str = "SELECT ".$n_field." FROM notify ".$where_str;

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
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fields($n_field,$where_str="")	取出全部 部門表內的 $n_field 置入arry return
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function cov_html($PHP_msg) {
	global $config;

	$msg = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
	$msg .= '<html xmlns="http://www.w3.org/1999/xhtml">';
	$msg .= '<head>';
	$msg .= '<meta http-equiv="Content-Type" content="text/html; charset=utf8" />';
	$msg .= '<link rel=stylesheet type="text/css" href="./bom.css">';
	$msg .= '<title>MSG</title>';
	$msg .= '<style type="text/css"><!--';
	$msg .= '.style5 {	font-size: 12px; color: #666666; height: 20px;}';
	$msg .= '.style1 {	font-size: 16px; color: #000000; height: 20px;line-height:20pt;}';
	$msg .= '-->';
	$msg .= '</style>';
	$msg .= '</head>';
	$msg .= '<body>';
	$msg .= '<table width="600" border="0" align="center" cellpadding="0" cellspacing="0">';
	$msg .= '<tr>';
	$msg .= '<td><img src="'.$config['root_url'].dirname($PHP_SELF).'/images/Logo4_bar.gif" width="600" height="50" /></td>';
	$msg .= '</tr>';
	$msg .= '<tr>';
	$msg .= '<td class="style1">'.nl2br($PHP_msg).'</td>';
	$msg .= '</tr>';
	$msg .= '  <tr>';
	$msg .= '<td height="22" align="center" bgcolor="#CCCCCC"><span class="style5">CARNIVAL, COPY Right and All Right Reserved.</span></td>';
	$msg .= '</tr>';
	$msg .= '</table>';
	$msg .= '</body>';
	$msg .= '</html> ';

	return $msg;
} // end func	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fields($n_field,$where_str="")	取出全部 部門表內的 $n_field 置入arry return
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function system_msg_send($job,$group,$title,$context) {
	
	$mesg = $GLOBALS['para']->get_mesg(0,$job,$group); //取得訊息相關資訊
	$user_id = explode(',',$mesg['set_user']);

	$mesg['set_title'] = str_replace('send_num',$title,$mesg['set_title']);
	//$mesg['set_text'] = 	nl2br($mesg['set_text']);
	$mesg['set_text']  = str_replace('send_num',$context,$mesg['set_text']);
	$mesg['set_text']  = '<BR><BR>'.$mesg['set_text'].'<BR><BR>';

	$mesg['set_text'] = $this->cov_html($mesg['set_text']);  //message add

	for($i=0; $i<sizeof($user_id); $i++)
	{
		$parm2 = array(
			"tuser"		=>  $user_id[$i],
			"title"		=>	$mesg['set_title'],
			"msg"		=>	$mesg['set_text'],	
			"fuser"		=>	'System MSG',
			"send_date"	=>	date('Y-m-d'),
		);
		$f3=$this->add($parm2);  //message add
	}

	return true;
} // end func		
	
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->system_msg_send_with_mail
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function system_msg_send_with_mail($job,$group,$title,$context,$other='',$retitle='') {
	# retitle = 信件名稱修改

	$mesg = $GLOBALS['para']->get_mesg(0,$job,$group); //取得訊息相關資訊

	$user_id = explode(',',$mesg['set_user']);
	$mesg['set_title'] = str_replace('send_num',$title,$mesg['set_title']);
	$mesg['set_text']  = str_replace('send_num',$context,$mesg['set_text']);
	if($other) $mesg['set_text']  = str_replace('other_context',$other,$mesg['set_text']);

	$mesg['set_text']  = '<BR>'.$mesg['set_text'].'<p>';

	$mesg['set_text'] = $this->cov_html($mesg['set_text']);  //message add

	for($i=0; $i<sizeof($user_id); $i++)
	{
		$parm2 = array(	
			"tuser"			=>  $user_id[$i],
			"title"			=>	$mesg['set_title'],
			"msg"			=>	$mesg['set_text'],	
			"fuser"			=>	'System MSG',
			"send_date"		=>	date('Y-m-d'),
		);
		$f3=$this->add($parm2);  //message add

		$mail_user=$GLOBALS['user']->get(0,$user_id[$i]);
		$to = $mail_user['email'];

		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf8' . "\r\n";
		$headers .= 'From: System MSG <mode@tp.carnival.com.tw>' . "\r\n";
		$headers .= 'Reply-To: Mode <mode@tp.carnival.com.tw>' . "\r\n";
		$set_title = $mesg['set_title'].$retitle;
		$set_text = $mesg['set_text'];
		
		// echo $mail_user['email'];
		mail($mail_user['email'], "$set_title", "$set_text", "$headers");


	}

		
		return true;
	} // end func			

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add_sql($n_field,$where_str="")	取出全部 部門表內的 $n_field 置入arry return
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_sql($m_sql) {
					
		$sql = $this->sql;
		if (!$q_result = $sql->query($m_sql)) {
			return false;
		}else{
			return true;
		}	
	}

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

} // end class


?>