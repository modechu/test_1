<?php 

#++++++++++++++++++++++ SMPL_LOG  class �˥� �O��  +++++++++++++++++++++++++++++++++++
#	->init($sql)							�ҩl (�ϥ� Msg_handle(); ���p�W sql)
#	->add($parm)							�[�J
#	->search($mode=0)						�j�M   
#	->get($id=0, nbr=0)						��X���w �O�������   
#	->edit($parm)							��s �㵧���
#	->update_field($parm)					��s ��Ƥ� �Y�ӳ�@���
#	->del($id)								�R�� ��ƿ�
#	->get_fields($n_field,$where_str="")	���X���� �������� $n_field �m�Jarry return
#
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class SMPL_LOG {
		
	var $sql;
	var $msg ;
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->init($sql)	�ҩl(�ϥ� Msg_handle() ; ���p�W sql)
#		�����p�W sql �~�i  �ҩl
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function init($sql) {
		$this->msg = new MSG_HANDLE();

		if (!$sql) {
			$this->msg->add("Error ! cannot contact database.");
		    return false;
		}
		$this->sql = $sql;
		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		�[�J�s �O��
#						�Ǧ^ $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add($parm) {
					
		$sql = $this->sql;

		if (!trim($parm['subj'])) {
			$this->msg->add("Error ! please Select the <b>Subject</b>�C");
			return false;
		}

		if (!trim($parm['des'])) {
			$this->msg->add("Error ! you should have some <b>Describe</b> for the log�C");
			return false;
		}
		$parm['des'] = str_replace("'","\'",$parm['des']);
		
		for ($i=0; $i< sizeof($GLOBALS['chin_error_word']); $i++)
		{
			$parm['des'] = str_replace($GLOBALS['chin_error_word'][$i],$GLOBALS['chin_error_word'][$i]."\\",$parm['des']);
		}
		
		
					# �[�J��Ʈw
		$q_str = "INSERT INTO smpl_log (num,user,subj,k_time,des) VALUES('".
									$parm['num']."','".
									$parm['user']."','".
									$parm['subj']."','".
									$parm['k_time']."','".
									$parm['des']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Sorry ! cannot adding new SMPL_LOG to data.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$log_id = $sql->insert_id();  //���X �s�� id


		return $log_id;

	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search50($num)	�j�M  ���	
#		�ȧ���̪񪺤��Q�����  �ƧǨ̮ɶ�
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search50($num) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv
		$srh = new SEARCH();
//		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT * FROM smpl_log WHERE num='".$num."' ";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
//		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("id DESC ");
		$srh->row_per_page = 50;

//		if ($argv['PHP_sr_startno']) {
//			$srh->add_limit_condition($argv['PHP_sr_startno']);
//		} 

		$result['log']= $srh->send_query2(50);   // 2005/05/16 �[�J $limit_entries
		if (!is_array($result['log'])) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
			if (!$result['log']){   // ��d�M�L��Ʈ�
				$result['records'] = '';
			} else {
				$result['records'] = 1;
			}

		for ($i=0; $i<sizeof($result['log']); $i++)
		{
			$tmp_user=$GLOBALS['user']->get(0,$result['log'][$i]['user']);
			if ($tmp_user['name'])$result['log'][$i]['user'] = $tmp_user['name'];			
		}
//		foreach($result as $key => $value) echo $key."<br>";
		return $result;
	} // end func

/*

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0,$where_str="")	�j�M ���� ���	
#		�ȧ���̪񪺤��Q�����  �ƧǨ̮ɶ�
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search($mode=0,$where_str="",$limit_entries=0) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv
		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT * FROM fabric ".$where_str;
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("id DESC ");
		$srh->row_per_page = 50;

		if ($argv['PHP_sr_startno']) {
			$srh->add_limit_condition($argv['PHP_sr_startno']);
		} 
if ($mode==1){
		if ($str = $argv['PHP_cat'] )  { 
			$srh->add_where_condition("cat = '$str'", "PHP_cat",$str,"�������O = [ $str ]. "); }
		if ($str = $argv['PHP_art_code'] )  { 
			$srh->add_where_condition("art_code LIKE '%$str%'", "PHP_name",$str,"�j�M ���ƽs���t��[ $str ]���e. "); }
		if ($str = $argv['PHP_name'] )  { 
			$srh->add_where_condition("name LIKE '%$str%'", "PHP_name",$str,"�j�M ���ƦW�٧t��[ $str ]���e. "); }
		if ($str = $argv['PHP_content'] )  { 
			$srh->add_where_condition("content LIKE '%$str%'", "PHP_content",$str,"�j�M���Ʀ����t��: [ $str ]���e "); }
		if ($str = $argv['PHP_supl_ref'] )  { 
			$srh->add_where_condition("supl_ref LIKE '%$str%'", "PHP_supl_ref",$str,"�j�M�����ӽs���t��: [ $str ]���e "); }
		if ($str = $argv['PHP_finish'] )  { 
			$srh->add_where_condition("finish LIKE '%$str%'", "PHP_finish",$str,"�j�M���ƫ��t��: [ $str ]���e "); }
		if ($str = $argv['PHP_supl'] )  { 
			$srh->add_where_condition("supl = '$str'", "PHP_supl",$str,"������ = [ $str ]. "); }
}	

		$result= $srh->send_query2($limit_entries);   // 2005/05/16 �[�J $limit_entries
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
			if (!$result){   // ��d�M�L��Ʈ�
				$op['record_NONE'] = 1;
			}

		$op['fabric'] = $result;  // ��ƿ� �ߤJ $op
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['cgistr_post'] = $srh->get_cgi_str(1);
		$op['prev_no'] = $srh->prev_no;
		$op['next_no'] = $srh->next_no;
		$op['max_no'] = $srh->max_no;
		$op['last_no'] = $srh->last_no;
		$op['start_no'] = $srh->start_no;
		$op['per_page'] = $srh->row_per_page;
		$op['rows_this_page'] = $srh->rows_this_page;

		if ($op['start_no'] == 0) {	$op['PREV_page'] = ""; } else {	$op['PREV_page'] = "1"; }
		if ($op['next_no'] == 0) {	$op['NEXT_page'] = ""; } else {	$op['NEXT_page'] = "1"; }

		return $op;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $art_code=0)	��X���w�O������� RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get($id=0, $art_code=0) {

		$sql = $this->sql;

		if ($id)	{
			$q_str = "SELECT * FROM fabric WHERE id='$id' ";
		} elseif ($fabric_code) {
			$q_str = "SELECT * FROM fabric WHERE art_code='$art_code' ";
		} else {
			$this->msg->add("Error ! �Ы��� �D�Ƹ�Ʀb��Ʈw���� ID.");		    
			return false;
		}

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s����Ʈw!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			$this->msg->add("Error ! �L�k���o���O��!");
			return false;    
		}
		return $row;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit($parm)		��s �ƮưO��
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function edit($parm) {

		$sql = $this->sql;

		#####   ��s��Ʈw���e
		$q_str = "UPDATE fabric SET cat='"		.$parm['cat'].
							"',	supl='"			.$parm['supl'].
							"', co='"			.$parm['co'].
							"', location='"		.$parm['location'].
							"', currency='"		.$parm['currency'].
							"', unit_price='"	.$parm['unit_price'].
							"', term='"			.$parm['term'].
							"', unit_wt='"		.$parm['unit_wt'].
							"', name='"			.$parm['name'].
							"', content='"		.$parm['content'].
							"', construct='"	.$parm['construct'].
							"', finish='"		.$parm['finish'].

							"', supl_ref='"		.$parm['supl_ref'].
							"', width='"		.$parm['width'].
							"', width_unit='"	.$parm['width_unit'].
							"', weight='"		.$parm['weight'].
							"', price='"		.$parm['price'].
							"', leadtime='"		.$parm['leadtime'].

							"', remark='"		.$parm['remark'].

						"'  WHERE id='"			.$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  �L�k��s��Ʈw.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$pdt_id = $parm['id'];


	//  �Ϫ��B�z ===============
	// �D�� ������ӹϤW��[�j��]��[�p��]
			//���ɥؿ�(�H pdt_id �ӳ]�����ɦW
			$upFile = new uploadIMG();
			$style_dir	= $GLOBALS['config']['root_dir']."/fabric/";  
			$no_img		= $GLOBALS['config']['root_dir']."/images/graydot.gif";

		if($parm['pic_upload'])  {   // 2005/01/04 �令��javascript���X�ʤ@�� hidden�� �e�J��x

						# �W�ǹϬ۳B�z
				//2004/05/03 ���ˬd�O�_�s�b �p�s�b�N������
			if(file_exists($style_dir.$pdt_id.".jpg")){
				unlink($style_dir.$pdt_id.".jpg") or die("�L�k�R���¹���:".$pdt_id.".jpg");  // �R������
			}
				//�W�Ǥj�� 600X600
				$upFile->setSaveTo($style_dir,$pdt_id.".jpg");
				$up_result = $upFile->upload($parm['pic'], 600, 600);

				//2004/05/03 ���ˬd�O�_�s�b �p�s�b�N������
//			if(file_exists($style_dir."s".$pdt_id.".jpg")){
//				unlink ($style_dir."s".$pdt_id.".jpg") or die("�L�k�R���¹���:"."s".$pdt_id.".jpg");  // �R������
//			}
//				//�W�Ǥp�� 150x150
//				$upFile->setSaveTo($style_dir,"s".$pdt_id.".jpg");
//				$up_result = $upFile->upload($parm['pic1'], 150, 150);
		}

		
		return $pdt_id;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		��s �Ʈ� ��� �Y�ӳ�@���
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field($parm) {

		$sql = $this->sql;
		############### �ˬd��J����
		
		#####   ��s��Ʈw���e
	$q_str = "UPDATE fabric SET ".$parm['field_name']."='".$parm['field_value']."'  WHERE id='".$parm['id']."' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k��s��Ʈw���e.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del($id)		�R�� ���ưO��  [��ID]�R��
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del($id) {

		$sql = $this->sql;
		if (!$id) {
			$this->msg->add("Error !�Ы��� �}�o���� ��ƪ� ID.");		    
			return false;
		}
		$q_str = "DELETE FROM fabric WHERE id='$id' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s����Ʈw !");
			$this->msg->merge($sql->msg);
			return false;    
		}

			// �Ϫ��R�� -------
			$style_dir	= $GLOBALS['config']['root_dir']."/fabric/";  

			if(file_exists($style_dir.$id.".jpg")){
				unlink($style_dir.$id.".jpg") or die("�L�k�R������:".$id.".jpg");  // �R������
			}
//			if(file_exists($style_dir."s".$id.".jpg")){
//				unlink ($style_dir."s".$id.".jpg") or die("�L�k�R������:"."s".$id.".jpg");  // �R������
//			}




		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fields($n_field,$where_str="")	���X���� �������� $n_field �m�Jarry return
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_fields($n_field,$where_str="") {
		$sql = $this->sql;
		$fields = array();
		$q_str = "SELECT ".$n_field." FROM fabric ".$where_str;

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! �L�k�s����Ʈw!");
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
			if ($match != 500) {   // �O�d �|���@��
				$sql->free_result($q_result);
				$result =0;
				$this->q_result = $q_result;
			}
		
		return $fields;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

*/


} // end class


?>