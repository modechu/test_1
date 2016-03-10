<?php

class MARKER {

	var $sql;
	var $msg ;

	#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	#	->init($sql)	啟始(使用 Msg_handle() ; 先聯上 sql)
	#		必需聯上 sql 才可  啟始
	#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function init($sql) {
		$this->msg = new MSG_HANDLE();

		if (!$sql) {
			$this->msg->add("Error ! Data base can't connect.");
		    return false;
		}
		$this->sql = $sql;
		return true;
	} // end func



	#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	#	->smpl_add($parm)		加入新 訂單記錄
	#						傳回 $id
	#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function smpl_add($parm) {
		$sql = $this->sql;

		if(!empty($parm['fab_type'])){
			$combo_num = $this->get_combo(1,$parm['smpl_id'],$parm['fab_type']);
			$combo = $combo_num+1;
			#$combo_num = $this->get_combo(1,$parm['smpl_id']);
			#$combo = ( $parm['fab_type'] == 2 )? ($combo_num+1) : 0 ;
		}


		$rmk='';
		if( $parm['rmk'] ){
			foreach( $parm['rmk'] as $key => $val){
				$rmk .= $key.'|';
			}
		}
		$parm['remark'] = substr($rmk,0,-1);

		$q_str = "
		INSERT INTO `marker_smpl` (
		`id` , `smpl_id` , `fab_type` , `unit_type` , `combo` ,  `width` , `length` ,
		`last_update` , `updator` , `description` , `remark` )
		VALUES (
		'',
		'".$parm['smpl_id']."',
		'".$parm['fab_type']."',
		'".$parm['unit_type']."',
		'".$combo."',
		'".$parm['width']."',
		'".$parm['length']."',
		'".date("Y-m-d H:i:s")."',
		'".$parm['updator']."' ,
		'".$parm['description']."' ,
		'".$parm['remark']."'
		);";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot append order");
			$this->msg->merge($sql->msg);
			return 0;
		}

		$marker_id = $sql->insert_id();  //取出 新的 id

		return $marker_id;
	} // end func



	function fab_item($fab_type) {
		#$combo = (!empty($combo))? $combo : '';
		switch ($fab_type) {

		case 1:
		$str = 'S';
		return $str;
		break;

		case 2:
		$str = 'L';
		return $str;
		break;

		case 3:
		$str = 'F';
		return $str;
		break;

		case 4:
		$str = 'N';
		return $str;
		break;

		case 5:
		$str = 'P';
		return $str;
		break;

	} // end func
}



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->marker_smpl_upload($parm)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function marker_smpl_upload($parm){

		if($parm['marker']) {
			$filename = $_FILES['PHP_marker']['name'];
			$ext =  strtolower(preg_replace("/.*\.([^.]+)$/","\\1", $filename));

			if ($ext == "pdf"){
				// upload marker file to server  // 指定為 pdf 副檔名
				# $fab_item = $this->fab_item($parm['fab_type'],$combo);
				$marker_name = $parm['num'].'.mk.pdf';

				$upload = new Upload;
				// $upload->uploadFile(dirname($_SERVER['PHP_SELF']).'/smpl_marker/', 'marker', 16, $marker_name );
				$upload->uploadFile($parm['save_path'], 'marker', 16, $marker_name );
				if (!$upload){
					$op['msg'][] = $upload;
					$layout->assign($op);$layout->display($TPL_ERROR);
					break;
				}

				#$message = "UPLOAD Marker of #".$parm['smpl_id'];
				# 更改 smpl_ord 的原先日期 ------------
				$parm = array(	"marker_date"	=>  $GLOBALS['TODAY'],
								"last_update"	=>  $GLOBALS['TODAY'],
								"updator"		=>  $GLOBALS['SCACHE']['ADMIN']['login_id'],
								"id"			=>  $parm['smpl_id'],
				);

				if (!$A = $GLOBALS['smpl_ord']->upload_marker($parm)){
					$op['msg'] = $GLOBALS['smpl_ord']->msg->get(2);
					$GLOBALS['layout']->assign($op);
					$GLOBALS['layout']->display($TPL_ERROR);
					break;
				}

				# 記錄使用者動態
				$message = "Upload smple marker : [".$parm['num'].".mk.pdf]";
				$GLOBALS['log']->log_add(0,"061U",$message);

			} else {  // 上傳檔的副檔名  不是  mdl 時 -----
				$message = "upload MARKER file is incorrect format, Please re-send. [*.zip]";
			}
		}
		return 1;
	} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_img($num)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function edit($parm) {
		$sql = $this->sql;
		/*
		if(!empty($parm['fab_type'])){
			$combo_num = $this->get_combo(1,$parm['smpl_id'],$parm['fab_type']);
			$combo = $combo_num+1;
			#$combo_num = $this->get_combo(1,$parm['smpl_id']);
			#$combo = ($parm['fab_type']==2)? ($combo_num) : 0 ;
		}
*/
		$rmk='';
		if( $parm['rmk'] ){
			foreach( $parm['rmk'] as $key => $val){
				$rmk .= $key.'|';
			}
		}
		$parm['remark'] = substr($rmk,0,-1);

		$q_str = "UPDATE `marker_smpl` SET
		unit_type 	    ='"	.$parm['unit_type']."',
		width 			='"	.$parm['width']."',
		length 			='"	.$parm['length']."',
		last_update	    = NOW() ,
		updator			='"	.$parm['updator']."' ,
		description     ='"	.$parm['description']."' , 
		remark			='"	.$parm['remark']."'
		WHERE id 		="	.$parm['id'];

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't update.");
			$this->msg->merge($sql->msg);
			return 0;
		}

		return 1;
	} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_combo($smpl_id)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_combo($tb=1,$id,$fab_type) {
		$sql = $this->sql;
		if( $tb == 1 )
			$q_str = "SELECT `combo` FROM `marker_smpl` WHERE smpl_id = '".$id."' AND fab_type = '".$fab_type."' order by combo DESC limit 1";
		else
			$q_str = "SELECT `combo` FROM `marker_ord` WHERE ord_id = '".$id."' AND fab_type = '".$fab_type."' order by combo DESC limit 1";
		if (!$q_result = $sql->query($q_str)){$this->msg->add("Error ! cannot access database, pls try later !");$this->msg->merge($sql->msg);return 0;}
		if (!$row = $sql->fetch($q_result)){return 0;}
		return $row['combo'];
	} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field_num
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field_num($tb=1,$field, $val, $id,$order_num='') {

		$sql = $this->sql;
		if($tb==1)
		$q_str = "UPDATE `marker_smpl` SET ".$field."='".$val."'  WHERE id ='".$id."'";
		else
		$q_str = "UPDATE `marker_ord` SET ".$field."='".$val."'  WHERE id ='".$id."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't update.");
			$this->msg->merge($sql->msg);
			return 0;
		}
		# 記錄使用者動態
		$message = "EDIT order marker: [".$order_num."] (".$field.")";
		$GLOBALS['log']->log_add(0,"061E",$message);
		return 1;
	} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field_num
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field_ord($field, $val, $ord_id ,$fab_type=0 ,$combo=0,$order_num='') {

		$sql = $this->sql;

		$q_str = "UPDATE `marker_ord` SET ".$field."='".$val."' WHERE ord_id ='".$ord_id."' and fab_type ='".$fab_type."' and combo ='".$combo."' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't update.");
			$this->msg->merge($sql->msg);
			return 0;
		}else{
			# 記錄使用者動態
			$message = "EDIT order marker: [".$order_num."] (".$field.")";
			$GLOBALS['log']->log_add(0,"061E",$message);
		}

		return 1;
	} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search($mode=0, $dept='',$limit_entries=0) {

		$argv = $GLOBALS;
		$sql = $this->sql;

		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		if($mode == 1){
			$q_header = "SELECT DISTINCT
			A1.id,A1.smpl_id,A1.fab_type,A1.unit_type,A1.combo,A1.width,A1.length,
			A2.num,A2.ref,A2.style,A2.smpl_type
			FROM `marker_smpl` as A1 LEFT JOIN `smpl_ord` as A2 ON A1.smpl_id = A2.id ";
		}else if($mode == 2){
			$q_header = "SELECT s_order.* , wi.id as wi_id, wi.status as wi_status, cust_init_name as cust_iname, wi.ti_cfm
			FROM s_order, cust Left Join wi On s_order.order_num = wi.wi_num";
		}

		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("smpl_id DESC");
		$srh->row_per_page = 7;

		if($limit_entries){    // 當有限定最大量時~~~ 2005/11/28 加入
			$srh->q_limit = "LIMIT ".$limit_entries." ";
		}else{
			##--*****--2006.11.16頁碼新增 start		##
			$pagesize=10;
			if ($argv['PHP_sr_startno']) {
				$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);
			}else{
				$pages = $srh->get_page(1,$pagesize);
			}
	 		##--*****--2006.11.16頁碼新增 end	   ##
		}

		$mesg = '';
		if ($mode == 1) {

			if (!isset($argv['PHP_num']))$argv['PHP_num']='';
			if (!isset($argv['PHP_style']))$argv['PHP_style']='';

			if ($str = strtoupper($argv['PHP_num']) ) {
				$srh->add_where_condition("A2.num LIKE '%$str%'", "PHP_num",$str);
				$mesg = "  Sample number : [ $str ]. ";
			}

			if ($str = $argv['PHP_style'] ) {
				$srh->add_where_condition("A2.style = '$str'", "PHP_style",$str);
				$mesg.= "  Style = [ $str ]. ";
			}
			
			if ($str = $argv['PHP_factory'] ) {
				$srh->add_where_condition("A2.factory = '$str'", "PHP_factory",$str);
				$mesg .= "  Factory = [ $str ]. ";
			}
			if ($mesg) {
				$msg = "Search ".$mesg;
				$this->msg->add($msg);
			}

		}else if ($mode == 2){
			if (!isset($argv['PHP_order_num']))$argv['PHP_order_num']='';
			if ($str = strtoupper($argv['PHP_order_num']) ) {
                $op['cgi']['PHP_order_num'] = $str;
				$srh->add_where_condition("s_order.order_num LIKE '%$str%'", "PHP_order_num",$str);
				$mesg = "  Order number : [ $str ]. ";
			}
			if ($str = $argv['PHP_style'] ) {
                $op['cgi']['PHP_style'] = $str;
				$srh->add_where_condition("s_order.style = '$str'", "PHP_style",$str);
				$mesg .= "  Style = [ $str ]. ";
			}
			if ($str = $argv['PHP_factory'] ) {
                $op['cgi']['PHP_factory'] = $str;
				$srh->add_where_condition("s_order.factory = '$str'", "PHP_factory",$str);
				$mesg .= "  Factory = [ $str ]. ";
			}
			if ($str = $argv['PHP_cust'] ) {
                $op['cgi']['PHP_cust'] = $str;
				$srh->add_where_condition("s_order.cust = '$str'", "PHP_cust",$str);
				$mesg .= "  Cust. = [ $str ]. ";
			}
            
			// echo $argv['PHP_factory'];
			$srh->add_where_condition("s_order.cust = cust.cust_s_name AND cust.ver = s_order.cust_ver");   // 關聯式察尋 必然要加
	//	   	$srh->add_where_condition("s_order.status < '12'");
			$srh->add_where_condition("s_order.status >= 0");
			// $srh->add_where_condition("wi.status > 1");
		}

		$result = $srh->send_query2($limit_entries);   // 2005/11/24 加入 $limit_entries
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;
		}


		$op['marker']	 			= $result;  // 資料錄 拋入 $op

		$this->msg->merge($srh->msg);
		if (!$result){   // 當查尋無資料時
			$op['record_NONE'] = 1;
		}
		$op['cgistr_get'] 	= $srh->get_cgi_str(0);
		$op['cgistr_post']	= $srh->get_cgi_str(1);
		$op['prev_no']			= $srh->prev_no;
		$op['next_no'] 			= $srh->next_no;
		$op['max_no'] 			= $srh->max_no;
		$op['last_no'] 			= $srh->last_no;
		$op['start_no'] 		= $srh->start_no;
		$op['per_page'] 		= $srh->row_per_page;
// echo $srh->q_str;
		if(!$limit_entries){
			##--*****--2006.11.16頁碼新增 start
			$op['maxpage']	=	$srh->get_max_page();
			$op['pages']		= $pages;
			$op['now_pp']		= $srh->now_pp;
			$op['lastpage']	=	$pages[$pagesize-1];
			##--*****--2006.11.16頁碼新增 end
		}

		return $op;
	} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get($tb=1,$id=0,$tb_id=0,$all=null,$fab_type=null,$combo=null,$group=null) {
		
		$sql = $this->sql;

		if ( $tb == 1 ) {
			if ($id)	{
				$q_str = "SELECT * FROM `marker_smpl` WHERE id='$id'";
			} elseif ($tb_id) {
				$q_str = "SELECT * FROM `marker_smpl` WHERE smpl_id='$tb_id' order by fab_type,combo";
			} else {
				$this->msg->add("Error ! please specify order number.");
				return false;
			}
		} else {
			$add = (isset($fab_type))? " and `fab_type` = '".$fab_type."' and `combo` = '".$combo."' " : "";
			$group = (isset($group))? " GROUP BY fab_type,combo " : "";
			if ($id) {
				$q_str = "SELECT marker_ord.* FROM `marker_ord` WHERE  id='$id' $add";
			} elseif ($tb_id) {
				$q_str = "SELECT marker_ord.* FROM `marker_ord` WHERE  ord_id='$tb_id' $add $group ";
			} else {
				$this->msg->add("Error ! please specify order number.");
				return false;
			}
		}

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;
		}

		if (!empty($all)) {
			$row =array();
			while ($rows = $sql->fetch($q_result)) {
				$row[] = $rows;
			}
			for ($i=0; $i<sizeof($row); $i++)
			{
                if(!empty($row[$i]['lots_code'])){
                    $q_str = "SELECT `lots_use`.`lots_code` as `mat_code` , `lots_use`.`est_1` , `lots_use`.`unit` FROM `lots_use` WHERE `id` = '".$row[$i]['lots_code']."'";
                    
                    $q_result = $sql->query($q_str);
                    $tmp_lots = $sql->fetch($q_result);
                    $row[$i]['mat_code'] = $tmp_lots['mat_code'];
                    $row[$i]['est_1'] = $tmp_lots['est_1'];
                    $row[$i]['b_unit'] = $tmp_lots['unit'];
                }
			}
		} else {
			if (!$row = $sql->fetch($q_result)) {
				$this->msg->add("Error ! Can't find this record!");
				return false;
			}
            if(!empty($row['lots_code'])){
                $q_str = "SELECT `lots_use`.`lots_code` as `mat_code` , `lots_use`.`est_1` , `lots_use`.`unit` FROM `lots_use` WHERE `id` = '".$row['lots_code']."'";

                $q_result = $sql->query($q_str);
                $tmp_lots = $sql->fetch($q_result);
                $row['mat_code'] = $tmp_lots['mat_code'];		
                $row['est_1'] = $tmp_lots['est_1'];
                $row['b_unit'] = $tmp_lots['unit'];
                $po_user=$GLOBALS['user']->get(0,$row['updator']);
                if ($po_user['name'])$row['updator'] = $po_user['name'];
                $row['last_update'] = substr($row['last_update'],0,10);
                // echo '<br>'.$row['mat_code'];
            }
		}
		
		if(!empty($row))return $row;
	} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->select_fab
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function select_fab($tb=1,$id,$fab='') {
		$sql = $this->sql;
		if($tb==1)
			$q_str = "SELECT DISTINCT `fab_type` FROM `marker_smpl` WHERE smpl_id = '".$id."' ";
		else
			$q_str = "SELECT DISTINCT `fab_type` FROM `marker_ord` WHERE ord_id = '".$id."' and fab_type = '".$fab."' ";

		if (!$q_result = $sql->query($q_str)){$this->msg->add("Error ! cannot access database, pls try later !");$this->msg->merge($sql->msg);return 0;}
		while ($row = $sql->fetch($q_result)) {
			$fab_type[] = $row['fab_type'];
		}

		$now_arr = array_keys($GLOBALS['fab_type']);
		if(!empty($fab_type))$count1 = count($fab_type);
		$count2 = count($now_arr)+1;
		for($i=1;$i < $count2;$i++){
			$yes = 0;
			if(!empty($count1)){
				for($y=0;$y < $count1;$y++){
					if( $fab_type[$y] == $i ){
						$yes = 1;
					}
				}
			}
			if(!empty($i)){
				if($yes <> 1 ){
					$fab_t[$i] = $GLOBALS['fab_type'][$i];
				}
			}
		}
		$fab_t[2] = $GLOBALS['fab_type'][2] = 'combo';
		if(!empty($fab)) $fab_t[$fab] = $GLOBALS['fab_type'][$fab];

		ksort($fab_t);

		$str = $GLOBALS['arry2']->select_id($fab_t,$fab,'PHP_fab','select_fab','','');

		return $str;
	} // end func




#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->select_fab
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function select_mk($ord_id,$fab_type,$combo,$mk='') {
		$sql = $this->sql;
		$q_str = "SELECT `mk_num` FROM `marker_ord` WHERE `ord_id` = '".$ord_id."' and fab_type = '".$fab_type."'  and combo = '".$combo."' ";

		if (!$q_result = $sql->query($q_str)){$this->msg->add("Error ! cannot access database, pls try later !");$this->msg->merge($sql->msg);return 0;}
		while ($row = $sql->fetch($q_result)) {
			$mk_arr[] = $row['mk_num'];
		}

		$now_arr = array_keys($GLOBALS['ALPHA2']);
		if(!empty($mk_arr))$count1 = count($mk_arr);
		$count2 = count($now_arr);
		for($i=0;$i < $count2;$i++){
			$yes = 0;
			for($y=0;$y < $count1;$y++){
				if( isset($mk_arr[$y]) and $mk_arr[$y] == "$i" ){
					$yes = 1;
				}
			}

			if(isset($i)){
				if($yes <> 1 ){
					$fab_t[$i] = $GLOBALS['ALPHA2'][$i];
				}
			}
		}

		if( !empty($mk) || $mk === '0') $fab_t[$mk] = $GLOBALS['ALPHA2'][$mk];

		ksort($fab_t);
		$str = $GLOBALS['arry2']->select_id($fab_t,$mk,'PHP_mk','select_mk','','','','',1);

		return $str;
	} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->select_unit
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function select_unit($tb=1,$val='') {
		if($tb==1)
		$str = $GLOBALS['arry2']->select_id($GLOBALS['unit_type'],$val,'PHP_unit','select_unit','','chg_unit();');
		else
		$str = $GLOBALS['arry2']->select_id($GLOBALS['unit_type2'],$val,'PHP_unit','select_unit','','chg_unit();');

		return $str;
	} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del($tb=1,$id,$ord_id,$fab_type='',$combo='',$status='') {

		$sql = $this->sql;
		$nc='';
		if($tb==1){
			$q_str = "DELETE FROM marker_smpl WHERE id = '".$id."' ";
			if (!$q_result = $sql->query($q_str)){$this->msg->add("Error ! 無法存取資料庫 !");$this->msg->merge($sql->msg);return 0;};

			$q_str = "select `id`,`combo` from `marker_smpl` WHERE smpl_id = '".$ord_id."' and fab_type = '".$fab_type."' and combo = '".$combo."' ";
			if (!$q_result = $sql->query($q_str)){$this->msg->add("Error ! cannot access database, pls try later !");$this->msg->merge($sql->msg);return 0;}

			if (!$row = $sql->fetch($q_result)){
				# 如果之後還有 combo 自動減 1
				$q_str = "select `id`,`combo` from `marker_smpl` where smpl_id = '".$ord_id."' and fab_type = '".$fab_type."' and combo > '".$combo."' ";
				if (!$q_result = $sql->query($q_str)){$this->msg->add("Error ! cannot access database, pls try later !");$this->msg->merge($sql->msg);return 0;}
				while ($row = $sql->fetch($q_result)) {
					$nc[]=$row;
					$q_str2 = "UPDATE `marker_smpl` SET `combo` = '".( $row['combo'] - 1 )."' where `id` = '".$row['id']."' ;";
					if (!$q_result2 = $sql->query($q_str2)){$this->msg->add("Error ! cannot access database, pls try later !");$this->msg->merge($sql->msg);return 0;}
				}
				if($status){
					if(!is_array($nc) && $combo == '1' ){
						return 2;
					}else {
						return 1;
					}
				}else{
					return 1;
				}

			}else{
				echo '222';
				return 2;
			}
		}else{
			if($status)
				$q_str = "DELETE FROM marker_ord WHERE id = '".$id."'";
			else
				$q_str = "DELETE FROM marker_ord WHERE ord_id = '".$id."' and fab_type = '".$fab_type."' and combo = '".$combo."' ";
			if (!$q_result = $sql->query($q_str)){$this->msg->add("Error ! 無法存取資料庫 !");$this->msg->merge($sql->msg);return 0;};

			$q_str = "select `id`,`combo` from `marker_ord` WHERE ord_id = '".$ord_id."' and fab_type = '".$fab_type."' and combo = '".$combo."' ";
			if (!$q_result = $sql->query($q_str)){$this->msg->add("Error ! cannot access database, pls try later !");$this->msg->merge($sql->msg);return 0;}

			if (!$row = $sql->fetch($q_result)){
				# 如果之後還有 combo 自動減 1
				$q_str = "select `id`,`combo` from `marker_ord` where ord_id = '".$ord_id."' and fab_type = '".$fab_type."' and combo > '".$combo."' ";
				if (!$q_result = $sql->query($q_str)){$this->msg->add("Error ! cannot access database, pls try later !");$this->msg->merge($sql->msg);return 0;}
				while ($row = $sql->fetch($q_result)) {
					$q_str2 = "UPDATE `marker_ord` SET `combo` = '".( $row['combo'] - 1 )."' where `id` = '".$row['id']."' ;";
					if (!$q_result2 = $sql->query($q_str2)){$this->msg->add("Error ! cannot access database, pls try later !");$this->msg->merge($sql->msg);return 0;}
				}
				return 2;
			}else{
				return 1;
			}
		}
	} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del_marker(
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del_marker($tb=1,$id,$marker) {

		$folder = ($tb==1)? '/smpl_marker/' : '/order_marker/';

		if(file_exists($GLOBALS['config']['root_dir'].$folder.$marker)){
			unlink($GLOBALS['config']['root_dir'].$folder.$marker) or die("can't delete old marker:".$marker);  // 刪除舊檔
		}
		if($tb==1){
			# 更改 smpl_ord 的原先日期 ------------
			$parm = array(	"marker_date"	=>  '0000-00-00',
											"last_update"	=>  $GLOBALS['TODAY'],
											"updator"			=>  $GLOBALS['SCACHE']['ADMIN']['login_id'],
											"id"					=>  $id,
			);

			if (!$A = $GLOBALS['smpl_ord']->upload_marker($parm)){
				$op['msg'] = $GLOBALS['smpl_ord']->msg->get(2);
				$GLOBALS['layout']->assign($op);
				$GLOBALS['layout']->display($TPL_ERROR);
				break;
			}
			return 1;
		}else{
			# 更改 order 的上傳日期 ------------
			if (!$A = $GLOBALS['order']->update_field('marker_date','0000-00-00',$id)){
				$op['msg'] = $GLOBALS['order']->msg->get(2);
				$GLOBALS['layout']->assign($op);
				$GLOBALS['layout']->display($TPL_ERROR);
				break;
			}
			return 1;
		}

	} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->report_main(
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function report_main($order_num,$T_wiqty,$size_A,$mk='',$status='') {
		$this->T_wiqty = $T_wiqty;

		# 不同的刪減 *************
		#if($mk['fab_type'] <> 3){
		if($mk['fab_type']){

			# get fab. type asmt.
			$T_qty = $this->re_color($T_wiqty);

			if($T_qty){
				# is true chge.
				foreach($T_qty as $key => $val){
					if($val['assortment'])
						$this->T_wiqty = $this->re_color($this->T_wiqty,$val['assortment'],1,1);
				}
				$T_wiqty = (empty($status))? $this->T_wiqty : $this->re_color($this->T_wiqty,$mk['assortment'],1,2);
			}
			return $this->report_color($order_num,$T_wiqty,$size_A,$this->T_wiqty,$mk,$status);
		}else{
/*
			# get fab. type asmt.
			$T_qty = $this->re_no_color($T_wiqty);

			if($T_qty){
				# is true chge.
				foreach($T_qty as $key => $val){
					if($val['assortment'])
						$this->T_wiqty = $this->re_color($this->T_wiqty,$val['assortment'],1,1);
				}
				$T_wiqty = (empty($status))? $this->T_wiqty : $this->re_no_color($this->T_wiqty,$mk['assortment'],1,2);
			}
			return $this->report_no_color($order_num,$T_wiqty,$size_A,$this->T_wiqty,$mk,$status);
*/
		}

	}


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->re_color(
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function re_color($T_wiqty,$assortment='',$status='',$p=1) {
		global $op;

		if($assortment){

			$asmt = explode('|',$assortment);
			$a_cr = explode(',',$asmt[0]);
			$a_lv = explode(',',$asmt[1]);
			$a_cw = explode(',',$asmt[2]);

			foreach($T_wiqty as $key => $val){
				$qty = $val['qty'];

				foreach($a_cr as $ckey => $cval){

					if($key == ($cval - 1) ){ # data in one
						$qty = explode(',',$val['qty']);
						$nw = $nw_qty = '';

						# Colorway
						foreach($a_cw as $wkey => $wval){
							if($wval){
								if($p==1){
								 $nw_qty[$wkey] = $qty[$wkey] - ( $wval * $a_lv[$ckey] );
								}else{
									$nw_qty[$wkey] = $qty[$wkey] + ( $wval * $a_lv[$ckey] );
								}
							}else{
								 $nw_qty[$wkey] = $qty[$wkey];
							}
						}

						$qty = '';
						foreach($nw_qty as $nkey => $nval)
							$qty .= $nval.',';
						$qty = substr($qty,0,-1);
					}
				}
				$T_wiqty2[$key]['qty'] = $qty;
				$T_wiqty2[$key]['colorway'] = $val['colorway'];
			}
			return $T_wiqty2;
		}else{
			# 判斷是否有 asmt
			$T_wiqty = '';
			foreach($op['mks'] as $key => $val)
			if($val['assortment'])$T_wiqty[] = array( 'id' => $val['id'] , 'assortment' => $val['assortment']);
			return $T_wiqty;
		}
	}



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->re_no_color(
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function re_no_color($T_wiqty,$assortment='',$status='',$p=1) {
		global $op;

		if($assortment){

			$asmt = explode('|',$assortment);
			$a_cr = explode(',',$asmt[0]);
			$a_lv = explode(',',$asmt[1]);
			$a_cw = explode(',',$asmt[2]);

			foreach($T_wiqty as $key => $val){
				$qty = $val['qty'];

				foreach($a_cr as $ckey => $cval){

					if($key == ($cval - 1) ){ # data in one
						$qty = explode(',',$val['qty']);
						$nw = $nw_qty = '';

						# Colorway
						foreach($a_cw as $wkey => $wval){
							if($wval){
								if($p==1){
								 $nw_qty[$wkey] = $qty[$wkey] - ( $wval * $a_lv[$ckey] );
								}else{
									$nw_qty[$wkey] = $qty[$wkey] + ( $wval * $a_lv[$ckey] );
								}
							}else{
								 $nw_qty[$wkey] = $qty[$wkey];
							}
						}

						$qty = '';
						foreach($nw_qty as $nkey => $nval)
							$qty .= $nval.',';
						$qty = substr($qty,0,-1);
					}
				}
				$T_wiqty2[$key]['qty'] = $qty;
				$T_wiqty2[$key]['colorway'] = $val['colorway'];
			}
			return $T_wiqty2;
		}else{
			# 判斷是否有 asmt
			$T_wiqty = '';
			foreach($op['mks'] as $key => $val)
			if($val['assortment'])$T_wiqty[] = array( 'id' => $val['id'] , 'assortment' => $val['assortment']);
			return $T_wiqty;
		}
	}


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->report_color(     layout
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function report_title($order_num,$T_wiqty,$size_A,$T_wiqty2='',$mk='',$status='') {

		$col = count(explode(',',$size_A['size']));
		$row = count($T_wiqty);

		if( !empty($status) ){
			if( !empty($mk['assortment']) ){
				$asmt = explode('|',$mk['assortment']);
				$a_cr = explode(',',$asmt[0]);
				$a_lv = explode(',',$asmt[1]);
				$a_cw = explode(',',$asmt[2]);
			}
		}else{
			$mk['length'] = '' ;
		}

		$html = '
			<tr>
        <td colspan="'.($col+1).'" align="center" bgcolor="#FFFFFF">配碼 ( Assortment ) </td>

      </tr>';

		$html .= '
      <tr>
        <td width="160" align="center" bgcolor="#FFFFFF">
					<table width="100%" border="0" cellspacing="0" cellpadding="3">
					  <tr>
					    <td width="30%" align="left">配色 Color</td>
					    <td width="10%" align="center">\</td>
					    <td width="30%" align="right">Size</td>
					  </tr>
					</table>
        </td>';
    $size_A = explode(',',$size_A['size']);
		foreach($size_A as $key){
		$html .= '
        <td align="center" bgcolor="#FFFFFF">'.$key.'</td>';
		}
		$html .= '
      </tr>';

		$m1=1;

    foreach($T_wiqty as $key => $val){
    $html .= '
      <tr>
        <td align="left" bgcolor="#FFFFFF">&nbsp;'.$val['colorway'].'</td>';

	      $qty = explode(',',$val['qty']);
        $m2=1;
        foreach($qty as $qkey => $qval){
        	if(empty($qval))$qval=0;
        	$t_qty = $qval;

        	# + asmt
        	if( !empty($T_wiqty2[$key]) ){
            $qty2 = explode(',',$T_wiqty2[$key]['qty']);
            $t_qty = $qty2[$qkey];
          }
		$html .= '
        <td align="center" bgcolor="#FFFFCC">
        '.$t_qty.'</td>';
        $m2++;
      	}
    $html .= '
      </tr>';
    $m1++;
    }
    $str['title'] = $html;
    return $str;
  }



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->report_color(     layout
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function report_color($order_num,$T_wiqty,$size_A,$T_wiqty2='',$mk='',$status='') {

		$col = count(explode(',',$size_A['size']));
		$row = count($T_wiqty);

		if( !empty($status) ){
			if( !empty($mk['assortment']) ){
				$asmt = explode('|',$mk['assortment']);
				$a_cr = explode(',',$asmt[0]);
				$a_lv = explode(',',$asmt[1]);
				$a_cw = explode(',',$asmt[2]);
			}
		}else{
			$mk['length'] = '' ;
		}

		$html = '
			<tr>
				<td rowspan="'.($row+2).'" align="center" bgcolor="#FFFFFF">馬克<br />MK #</td>
        <td colspan="'.($col+1).'" align="center" bgcolor="#FFFFFF">配碼 ( Assortment ) </td>
        <td rowspan="'.($row+2).'" align="center" bgcolor="#FFFFFF">層數<br />Level</td>
        <td rowspan="'.($row+2).'" bgcolor="#FFFFFF">碼長 ('.$GLOBALS['unit_type2'][$mk['unit_type']].')<br />Length</td>
        <td rowspan="'.($row+2).'" bgcolor="#FFFFFF">&nbsp;</td>
      </tr>';

		$html .= '
      <tr>
        <td align="center" bgcolor="#FFFFFF">
					<table width="100%" border="0" cellspacing="0" cellpadding="3">
					  <tr>
					    <td width="40%" align="left" nowrap>配色 Color</td>
					    <td width="20%" align="center">\</td>
					    <td width="40%" align="right">Size</td>
					  </tr>
					</table>
        </td>';
    $size_A = explode(',',$size_A['size']);
		foreach($size_A as $key){
		$html .= '
        <td width="30" align="center" bgcolor="#FFFFFF">'.$key.'</td>';
		}
		$html .= '
      </tr>';

		$m1=1;

    foreach($T_wiqty as $key => $val){
    $html .= '
      <tr>
        <td align="left" bgcolor="#FFFFFF">&nbsp;'.$val['colorway'].'</td>';

	      $qty = explode(',',$val['qty']);
        $m2=1;
        foreach($qty as $qkey => $qval){
        	if(empty($qval))$qval=0;
        	$t_qty = $qval;

        	# + asmt
        	if( !empty($T_wiqty2[$key]) ){
            $qty2 = explode(',',$T_wiqty2[$key]['qty']);
            $t_qty = $qty2[$qkey];
          }
		$html .= '
        <td align="center" bgcolor="#FFFFCC"><input type="text" name="m'.$m1.'_'.$m2.'" id="m'.$m1.'_'.$m2.'" style="width:25px;border-top:0px;border-left:0px;border-right:0px;border-bottom:0px;background-color:#FFFFCC;text-align:right;" value="'.$t_qty.'" readonly />
        <input type="hidden" name="mh'.$m1.'_'.$m2.'" id="m'.$m1.'_'.$m2.'" value="'.$qval.'" style="width:25px;border-top:0px;border-left:0px;border-right:0px;border-bottom:0px;background-color:#FFFFCC;text-align:right;" /></td>';
        $m2++;
      	}
    $html .= '
      </tr>';
    $m1++;
    }
    $str['title'] = $html;

    if( !empty($status) )
			$m_num = $this->select_mk($mk['ord_id'],$mk['fab_type'],$mk['combo'],$mk['mk_num']);
		else
			$m_num = $this->select_mk($GLOBALS['op']['mk']['ord_id'],$GLOBALS['op']['mk']['fab_type'],$GLOBALS['op']['mk']['combo']);



		$m1 = 1;
		foreach($T_wiqty as $key => $val){
		if($m1 == 1){
    $st = '
			<tr bgcolor="#CCCCCC">
				<td rowspan="'.$row.'" valign="top" bgcolor="#CCCCCC">'.$m_num.'</td>
			  <td align="left" valign="top" bgcolor="#CCCCCC">
				  <table width="100%" border="0" cellspacing="0" cellpadding="0">
			      <tr>
			        <td align="left">&nbsp;'.$val['colorway'].'</td>';
			# + asmt
			$mc = 0;
			$lv = '';
			if( !empty($a_cr) ){
				foreach($a_cr as $ckey => $cval){
					if($key == ($cval - 1) ){
						$st .= '<td align="right"><input name="cr['.$m1.']" id="cr['.$m1.']" type="checkbox" onClick="chk(\''.$row.'\',\''.$col.'\')" checked="checked" /></td>';
						$mc = 1;
						$lv = $a_lv[$ckey];
					}
				}
			}
			if($mc <> 1)
			$st .= '<td align="right"><input name="cr['.$m1.']" id="cr['.$m1.']" type="checkbox" onClick="chk(\''.$row.'\',\''.$col.'\')" /></td>';

			$st .= '
			      </tr>
			    </table>
			  </td>';
		$m2 = 1;

		# + asmt
		foreach($size_A as $keys => $vals){
		$vals = (!empty($a_cw))? $a_cw[$keys]:'';
		$st .= '
				<td rowspan="'.$row.'" valign="top" bgcolor="#CCCCCC"><input type="text" name="cw['.$m2.']" id="cw['.$m2.']" style="width:20px;text-align:right;" value="'.$vals.'" onKeyUp="chk(\''.$row.'\',\''.$col.'\')" /></td>';
		$m2++;
		}

		$st .= '
			  <td valign="top" bgcolor="#CCCCCC"><input name="lv['.$m1.']" id="lv['.$m1.']" type="text" style="width:36px;text-align:right;" value="'.$lv.'" onKeyUp="chk(\''.$row.'\',\''.$col.'\')"/></td>
			  <td rowspan="'.$row.'" valign="top" bgcolor="#CCCCCC"><input name="PHP_length" id="length" type="text" style="width:36px;text-align:right;" value="'.$mk['length'].'" onClick="chk(\''.$row.'\',\''.$col.'\')"/></td>
			  <td rowspan="'.$row.'" valign="top" bgcolor="#CCCCCC">';
		if(!empty($status) && $status == 'edit'){
			$st .= '
			  <input type="button" name="Smt_add" id="Smt_add" value="Update" onclick="atc(\'edit\')" style="width:52px;cursor:pointer;" /><br>';
		}else{
			$st .= '
			  <input type="button" name="Smt_add" id="Smt_add" value="Append" onclick="atc(\'append\')" style="width:52px;cursor:pointer;" />';
		}
			$st .= '
			  </td>
			</tr>';
		}
		if($m1 <> 1){
		$st .= '
			<tr bgcolor="#CCCCCC">
			  <td align="left" valign="top" bgcolor="#CCCCCC">
				  <table width="100%" border="0" cellspacing="0" cellpadding="0">
			      <tr>
			        <td align="left">&nbsp;'.$val['colorway'].'</td>';
			# + asmt
			if( !empty($a_cr) ){
				$mc = 0;
				$lv = '';
				foreach($a_cr as $ckey => $cval){
					if($key == ($cval - 1) ){
						$st .= '<td align="right"><input name="cr['.$m1.']" id="cr['.$m1.']" type="checkbox" onClick="chk(\''.$row.'\',\''.$col.'\')" checked="checked" /></td>';
						$mc = 1;
						$lv = $a_lv[$ckey];
					}
				}
			}
			if($mc <> 1)
			$st .= '<td align="right"><input name="cr['.$m1.']" id="cr['.$m1.']" type="checkbox" onClick="chk(\''.$row.'\',\''.$col.'\')" /></td>';

			$st .= '
			      </tr>
			    </table>
				</td>
			  <td valign="top" bgcolor="#CCCCCC"><input name="lv['.$m1.']" id="lv['.$m1.']" type="text" style="width:36px;text-align:right;" value="'.$lv.'" onKeyUp="chk(\''.$row.'\',\''.$col.'\')"/></td>
			</tr>
		';
		}
		$m1++;
		}

    $str['main'] = $st;

		return $str;
	}



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->report_no_color(     layout
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function report_no_color($order_num,$T_wiqty,$size_A,$T_wiqty2='',$mk='',$status='') {

		$col = count(explode(',',$size_A['size']));
		$row = count($T_wiqty);
		$html = '
			<tr>
        <td width="60" rowspan="3" align="center" bgcolor="#FFFFFF">馬克<br />MK #</td>
        <td colspan="'.($col+1).'" align="center" bgcolor="#FFFFFF">配馬 ( Assortment ) </td>
        <td rowspan="3" align="center" bgcolor="#FFFFFF">層數<br />Level</td>
        <td rowspan="3" align="center" bgcolor="#FFFFFF" nowrap>馬克長度<br />Length</td>
        <td rowspan="3" bgcolor="#FFFFFF">&nbsp;</td>
      </tr>';


		$html .= '
      <tr>
        <td align="center" bgcolor="#FFFFFF">
					<table width="100%" border="0" cellspacing="0" cellpadding="3">
					  <tr>
					    <td width="40%" align="left">配色 Color</td>
					    <td width="20%" align="center">\</td>
					    <td width="40%" align="right">Size</td>
					  </tr>
					</table>
        </td>';
    $size_A = explode(',',$size_A['size']);
		foreach($size_A as $key){
		$html .= '
        <td width="30" align="center" bgcolor="#FFFFFF">'.$key.'</td>';
		}
		$html .= '
      </tr>';


    foreach($T_wiqty as $key){
      $qty = explode(',',$key['qty']);
      foreach($qty as $qkey => $qval){
      	if(empty($qq[$qkey]))$qq[$qkey]=0;
      	$qq[$qkey] = $qq[$qkey] + $qval;
    	}
    }

    $html .= '
      <tr>
        <td align="center" bgcolor="#FFFFFF">Total</td>';
		$m1=1;
		foreach($qq as $key => $val){
		$html .= '
        <td align="center" bgcolor="#FFFFCC">
        <input type="text" name="m'.$m1.'" id="m'.$m1.'" value="'.$val.'" style="width:25px;border-top:0px;border-left:0px;border-right:0px;border-bottom:0px;background-color:#FFFFCC;text-align:right;" readonly />
        <input type="hidden" name="mh'.$m1.'" id="m'.$m1.'" value="'.$qq[$key].'" />
        </td>';
    $m1++;
    }

    $html .= '
      </tr>';

    $str['title'] = ($html);
    #htmlentities

    if( !empty($status) )
			$m_num = $this->select_mk($mk['ord_id'],$mk['mk_num']);
		else
			$m_num = $this->select_mk($GLOBALS['op']['mk']['ord_id']);

		$m1 = 1;
		foreach($T_wiqty as $key){
		if($m1 == 1){
    $st = '
			<tr bgcolor="#CCCCCC">
        <td rowspan="'.$row.'" valign="top" bgcolor="#CCCCCC">'.$m_num.'</td>
			  <td align="left" valign="top" bgcolor="#CCCCCC">
				  <table width="100%" border="0" cellspacing="0" cellpadding="0">
			      <tr>
			        <td align="left">&nbsp;'.$key['colorway'].'</td>
			        <td align="right"><input name="cr['.$m1.']" id="cr['.$m1.']" type="checkbox" onClick="chk_no(\''.$row.'\',\''.$col.'\')"/></td>
			      </tr>
			    </table>
			  </td>';
		$m2 = 1;
		foreach($size_A as $keys){
		$st .= '
				<td valign="top" bgcolor="#CCCCCC">
					<input type="text" name="cw'.$m1.'['.$m2.']" id="cw'.$m1.'['.$m2.']" style="width:20px;text-align:right;" value="" onKeyUp="chk_no(\''.$row.'\',\''.$col.'\')" />
				</td>';
		$m2++;
		}
		$st .= '
			  <td valign="top" bgcolor="#CCCCCC"><input name="lv['.$m1.']" id="lv['.$m1.']" type="text" style="width:36px;text-align:right;" value="" onKeyUp="chk_no(\''.$row.'\',\''.$col.'\')"/></td>
			  <td rowspan="'.$row.'" valign="top" bgcolor="#CCCCCC"><input name="length" type="text" style="width:36px;text-align:right;" value="" /></td>
			  <td rowspan="'.$row.'" valign="top" bgcolor="#CCCCCC">';
		if(!empty($status) && $status == 'edit'){
			$st .= '
			  <input type="button" name="Smt_add" id="Smt_add" value="Update" onclick="atc(\'edit\')" style="width:52px;cursor:pointer;" /><br>';
		}else{
			$st .= '
			  <input type="button" name="Smt_add" id="Smt_add" value="Append" onclick="atc(\'append\')" style="width:52px;cursor:pointer;" />';
		}
			$st .= '
				</td>
			</tr>';
		}


		if($m1 <> 1){
		$st .= '
			<tr bgcolor="#CCCCCC">
			  <td align="left" valign="top" bgcolor="#CCCCCC">
				  <table width="100%" border="0" cellspacing="0" cellpadding="0">
			      <tr>
			        <td align="left">&nbsp;'.$key['colorway'].'</td>
			        <td align="right"><input name="cr['.$m1.']" id="cr['.$m1.']" type="checkbox" onClick="chk_no(\''.$row.'\',\''.$col.'\')"/></td>
			      </tr>
			    </table>
				</td>';
		$m2 = 1;
		foreach($size_A as $keys){
		$st .= '
				<td valign="top" bgcolor="#CCCCCC">
					<input type="text" name="cw'.$m1.'['.$m2.']" id="cw'.$m1.'['.$m2.']" style="width:20px;text-align:right;" value="" onKeyUp="chk_no(\''.$row.'\',\''.$col.'\')" />
				</td>';
		$m2++;
		}
		$st .= '
			  <td valign="top" bgcolor="#CCCCCC"><input name="lv['.$m1.']" id="lv['.$m1.']" type="text" style="width:36px;text-align:right;" value="" onKeyUp="chk_no(\''.$row.'\',\''.$col.'\')"/></td>
			</tr>
		';
		}


		$m1++;
		}

    $str['main'] = $st;

		return $str;
	}



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->marker_ord_upload($parm)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function marker_ord_upload($parm){

		if($parm['marker']) {

			$filename = $_FILES['PHP_marker']['name'];
			$ext =  strtolower(preg_replace("/.*\.([^.]+)$/","\\1", $filename));

			if ($ext == "zip"){
				// 上傳檔的副檔名為 mdl 時 -----
				// upload marker file to server  // 指定為 pdf 副檔名
				# $fab_item = $this->fab_item($parm['fab_type'],$combo);
				$marker_name = $parm['order_num'].'.mk.zip';

				$upload = new Upload;
				//$upload->uploadFile(dirname($_SERVER['PHP_SELF']).'/order_marker/', 'marker', 16, $marker_name );
				$upload->uploadFile($parm['save_path'], 'marker', 16, $marker_name );
				if (!$upload){
					$op['msg'][] = $upload;
					$layout->assign($op);
					$layout->display($TPL_ERROR);
					break;
				}

				# 更改 order 的上傳日期 ------------
				if (!$A = $GLOBALS['order']->update_field('marker_date',$GLOBALS['TODAY'],$parm['ord_id'])){
					$op['msg'] = $GLOBALS['order']->msg->get(2);
					$GLOBALS['layout']->assign($op);
					$GLOBALS['layout']->display($TPL_ERROR);
					break;
				}

			} else {  // 上傳檔的副檔名  不是  mdl 時 -----
				$message = "upload MARKER file is incorrect format, Please re-send. [*.zip]";
				$_SESSION['MSG'][] = $message;
			}
		}
		return 1;
	} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->ord_add($parm)		加入新 訂單記錄
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	function ord_add($parm,$status='') {
		$sql = $this->sql;

		if(empty($status))$status='';
		if(!empty($parm['fab_type'])){
			$combo_num = $this->get_combo(2,$parm['ord_id'],$parm['fab_type']);
			#$combo = ( $parm['fab_type'] == 2 )? ($combo_num+1) : 0 ;
			$combo = $combo_num+1;
		}

		/*
		else{
			if($dt = $this->get(2,0,$parm['ord_id'])){
				$GLOBALS['msg'] = '資料重複！';
				exit;
			}
		}
		*/

		$rmk='';
		if( $parm['rmk'] ){
			foreach( $parm['rmk'] as $key => $val){
				$rmk .= $key.'|';
			}
		}
		$parm['remark'] = substr($rmk,0,-1);

		if($status == 'append'){
			$combo = $parm['combo'];
		}else{
			$parm['mk_num'] = $parm['assortment'] = '';
		}

		$q_str = "
		INSERT INTO `marker_ord` (
		`id` , `ord_id` ,lots_code, `mk_num` , `fab_type` , `unit_type` , `combo` , `width` , `length` ,
		`last_update` , `updator` , `description` , `remark` , `assortment` )
		VALUES (
		'',
		'".$parm['ord_id']."',
		'".$parm['lots_code']."',
		'".$parm['mk_num']."',
		'".$parm['fab_type']."',
		'".$parm['unit_type']."',
		'".$combo."',
		'".$parm['width']."',
		'".$parm['length']."',
		'".date("Y-m-d H:i:s")."',
		'".$parm['updator']."' ,
		'".$parm['description']."' ,
		'".$parm['remark']."' ,
		'".$parm['assortment']."'
		);";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot append order");
			$this->msg->merge($sql->msg);
			return 0;
		}

		$marker_id = $sql->insert_id();  //取出 新的 id

		return $marker_id;
	} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->marker_list(
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	function marker_list($id=null,$fab_type=null,$combo=null){
		$str = $this->get(2,null,$id,1,$fab_type,$combo);
		$html = '';
		foreach($str as $key)
			if( !empty($key['mk_num']) or $key['mk_num'] === '0' )
				$html .= $this->fab_item($key['fab_type']).$key['combo'].$GLOBALS['ALPHA2'][$key['mk_num']].' , ';
		if($html) return $html = substr($html,0,-2);
	} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->marker_list2(
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	function marker_list2($tb,$id,$tb_id=null,$all=null,$fab_type=null,$combo=null,$size){

		$str = $this->get($tb,null,$tb_id,$all,$fab_type,$combo);
		$size = $GLOBALS['size_des']->get($size);

		$asmts = $averages = $clothes = $estimate = '';
		if($str){
			foreach($str as $keys => $vals){
				if(is_array($vals)){
					foreach($vals as $key => $val){
						if( $key === 'assortment' ){
							$asmts = $this->average($val,$str[$keys]['length']);
							if(!empty($asmts)){
								$clothes += $asmts['clothes'];
								$estimate += $asmts['estimate'];
								$averages = $clothes / $estimate;
							}
						}
					}
				}
			}
		}

		if(!empty($averages)) return $averages;
	} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->choic_rmk(
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	function rmk_list($str){
		$html = '';
		$str = explode('|',$str);
		foreach($str as $key){
			if($key)$html .= $GLOBALS['m_rmk'][$key].'，';
		}
		if($html) $html = substr($html,0,-2).'。';
		return $html;
	} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->choic_rmk(
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	function choic_rmk(){
		$html = '';
		foreach($GLOBALS['m_rmk'] as $key => $val)
			$html .= '<input name="rmk['.$key.']" id="rmk['.$key.']" type="checkbox" value="radiobutton" /> '.$val.'&nbsp;&nbsp;&nbsp;';
		return $html;
	} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit_rmk(
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	function edit_rmk($str){

		$rmk = explode('|',$str);

		$html = '';
		foreach($GLOBALS['m_rmk'] as $key => $val){
			$checked = '';
			foreach($rmk as $mkey){
				if( $mkey == $key)$checked = 'checked="checked"';
			}
			$html .= '<input name="rmk['.$key.']" id="rmk['.$key.']" type="checkbox" value="radiobutton" '.$checked.' /> '.$val.'&nbsp;&nbsp;&nbsp;';
		}
		return $html;
	} // end func




#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->average(
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	function average($asmt=0,$length=0,$def_waste=0){
		$asmts = $clothes = $estimate = '';

		if( !empty($asmt) && !empty($length) ){
			$asmt = explode('|',$asmt);
			$a_lv = explode(',',$asmt[1]);
			$a_cw = explode(',',$asmt[2]);

			foreach($a_lv as $key){
				$clothes += $key * $length;
				$consume += $key * $def_waste;
				foreach($a_cw as $keys){
					if( !empty($keys) )
					$estimate += $key * $keys;
				}
			}
			//if( !empty($clothes) and !empty($estimate) )$averages = $clothes / $estimate;
			if( !empty($clothes) and !empty($estimate) )$averages = round(($clothes+$consume) / $estimate , 2);
		}


			if(!empty($averages))$asmts['averages'] = $averages;
			if(!empty($clothes))$asmts['clothes'] = $clothes;
			if(!empty($estimate))$asmts['estimate'] = $estimate;
			if(!empty($consume))$asmts['consume'] = $consume;


		#echo $averages.' = '.$clothes.' / '.$estimate.'<br>';
		if(empty($averages))$averages='';
		if(empty($asmts))$asmts='';
		return $asmts;
	} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_lots_use_mk($lots_id,$ord_id) cutting report用
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_lots_use_mk($lots_id,$ord_id) {
		$sql = $this->sql;
		$q_str = "SELECT assortment, length FROM `marker_ord` 
							WHERE ord_id = '".$ord_id."' AND lots_code = '".$lots_id."'";

		if (!$q_result = $sql->query($q_str))return 0;
		
		$averages = $clothes = $estimate = 0;
		while($row = $sql->fetch($q_result))
		{
			$asmts = $this->average($row['assortment'],$row['length']);
			if(!empty($asmts)){
					$clothes += $asmts['clothes'];
					$estimate += $asmts['estimate'];					
			}						 
		}
		if($estimate > 0)$averages = $clothes / $estimate;
		return $averages;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_mark_waste_value cutting report用
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_marker_waste_value($sql_str) {
		$sql = $this->sql;
		$q_result = $sql->query($sql_str);
		//echo $q_result;
		$row = $sql->fetch($q_result);
		if($row['set_value']>0) return $row['set_value'];
		return 0;
	} // end func









}
?>