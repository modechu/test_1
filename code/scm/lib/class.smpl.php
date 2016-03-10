<?php 

#++++++++++++++++++++++ SMPL  class ##### �˥�  +++++++++++++++++++++++++++++++++++
#	->init($sql)							�ҩl (�ϥ� Msg_handle(); ���p�W sql)
#	->add($parm)							�[�J
#	->copy_smpl($parm)						�ƻs�s �˥��O��
#	->search($mode=0)						�j�M   
#	->get($id=0, nbr=0)						��X���w �O�������   
#	->edit($parm)							��s �㵧���
#	->update_field($parm)					��s ��Ƥ� �Y�ӳ�@���
#	->del($id)								�R�� ��ƿ�
#	->get_fields($n_field,$where_str="")	���X���� �������� $n_field �m�Jarry return
#
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class SMPL {
		
	var $sql;
	var $msg ;
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->init($sql)	�ҩl(�ϥ� Msg_handle() ; ���p�W sql)
#		�����p�W sql �~�i  �ҩl
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function init($sql) {
		$this->msg = new MSG_HANDLE();

		if (!$sql) {
			$this->msg->add("Error ! �L�k�p�W��Ʈw.");
		    return false;
		}
		$this->sql = $sql;
		return true;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->check($parm)		�ˬd �[�J�s �˥��O�� �O�_���T
#						
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function check($parm) {

		$this->msg = new MSG_HANDLE();
			############### �ˬd��J����	
		if (!$parm['cust'] = trim($parm['cust'])) {
			$this->msg->add("Error ! �п�J�Ȥ�O �C");
		    return false;
		}
		if (!$parm['year'] = trim($parm['year'])) {
			$this->msg->add("Error ! �п�J�~�� �C");
		    return false;
		}
							
//		if ($this->msg){
//			return false;
//		}
					
//echo "<br>[debug in  check procedure.............]";
		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		�[�J�s �˥��O��
#						�Ǧ^ $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add($parm) {
					
		$sql = $this->sql;

					# �[�J��Ʈw
		$q_str = "INSERT INTO smpl (style_code,
									dept,
									cust,
									cust_ref,
									year,
									season,
									creator,
									size_type,
									size_scale,
									style_type,
									style_relate,
									quota,
									chief_val,
									ling_val,
									opendate,
									memo) VALUES('".
									$parm['style_code']."','".
									$parm['dept_code']."','".
									$parm['cust']."','".
									$parm['cust_ref']."','".
									$parm['year']."','".
									$parm['season']."','".
									$parm['creator']."','".
									$parm['size_type']."','".
									$parm['size_scale']."','".
									$parm['style_type']."','".
									$parm['style_relate']."','".
									$parm['quota']."','".
									$parm['chief_val']."','".
									$parm['ling_val']."',
									NOW(),'".
									$parm['comments']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s�W��ưO��.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$pdt_id = $sql->insert_id();  //���X �s�� id

		$this->msg->add("���\ �s�W�˥��O��: [".$parm['style_code']."]�C") ;

	
	// �D�� ������ӹϤW��[�j��]��[�p��]
			//���ɥؿ�(�H pdt_id �ӳ]�����ɦW
			$upFile = new uploadIMG();
			$style_dir	= $GLOBALS['config']['root_dir']."/pics/";  
			$no_img		= $GLOBALS['config']['root_dir']."/images/graydot.gif";
		if($parm['pic1_upload'])  {   // 2005/01/04 �令��javascript���X�ʤ@�� hidden�� �e�J��x
//		if($parm['pic1'] <> "none") {
						# �W�ǹϬ۳B�z
			//�W�Ǥj�� 600X600
			$upFile->setSaveTo($style_dir,$pdt_id.".jpg");
			$up_result = $upFile->upload($parm['pic1'], 600, 600);
			//�W�Ǥp�� 150x150
			$upFile->setSaveTo($style_dir,"s".$pdt_id.".jpg");
			$up_result = $upFile->upload($parm['pic1'], 150, 150);
				if ($up_result){
					$this->msg->add("���\�W�ǥD��");
				} else {
					$this->msg->add("�W�ǥD�� ����");
				}
		}else{
			// �Ngray �ϴ_�s �@��
			copy($no_img,$style_dir.$pdt_id.".jpg");
			copy($no_img,$style_dir."s".$pdt_id.".jpg");
		}


	// ���� A�W��
		if($parm['pic2_upload'])  {   // 2005/01/04 �令��javascript���X�ʤ@�� hidden�� �e�J��x
//		if($parm['pic2'] <> "none"){
						# �W�ǹϬ۳B�z
			//���ɥؿ�(�H pdt_id �ӳ]�����ɦW �e���[�W "A" //�W�ǹ� 320X320
			$upFile->setSaveTo($style_dir,"A".$pdt_id.".jpg");
			$up_result = $upFile->upload($parm['pic2'], 320, 320);
				if ($up_result){
					$this->msg->add("���\�W�� ���Ϥ@");
				} else {
					$this->msg->add("�W�Ǧ��Ϥ@ ����");
				}
		}else{
			// �Ngray �ϴ_�s �@��
			copy($no_img,$style_dir."A".$pdt_id.".jpg");
		}

	// ���� B�W��
		if($parm['pic3_upload'])  {   // 2005/01/04 �令��javascript���X�ʤ@�� hidden�� �e�J��x
//		if($parm['pic3'] <> "none"){
						# �W�ǹϬ۳B�z
			//���ɥؿ�(�H pdt_id �ӳ]�����ɦW �e���[�W "B" //�W�ǹ� 320X320
			$upFile->setSaveTo($style_dir,"B".$pdt_id.".jpg");
			$up_result = $upFile->upload($parm['pic3'], 320, 320);
				if ($up_result){
					$this->msg->add("���\�W�� ���ϤG");
				} else {
					$this->msg->add("�W�Ǧ��ϤG ����");
				}
		}else{
			// �Ngray �ϴ_�s �@��
			copy($no_img,$style_dir."B".$pdt_id.".jpg");
		}

		return $pdt_id;

	} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->copy_smpl($parm)		�ƻs�s �˥��O��
#						�Ǧ^ $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function copy_smpl($parm) {
					
		$sql = $this->sql;

					# �[�J��Ʈw
		$q_str = "INSERT INTO smpl (style_code,
									dept,
									cust,
									cust_ref,
									year,
									season,
									creator,
									size_type,
									size_scale,
									style_type,
									style_relate,
									quota,
									chief_val,
									ling_val,
									opendate,
									copykey,
									memo) VALUES('".
									$parm['style_code']."','".
									$parm['dept_code']."','".
									$parm['cust']."','".
									$parm['cust_ref']."','".
									$parm['year']."','".
									$parm['season']."','".
									$parm['creator']."','".
									$parm['size_type']."','".
									$parm['size_scale']."','".
									$parm['style_type']."','".
									$parm['style_relate']."','".
									$parm['quota']."','".
									$parm['chief_val']."','".
									$parm['ling_val']."',
									NOW(),'".
									"*','".
									$parm['comments']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s�W��ưO��.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$pdt_id = $sql->insert_id();  //���X �s�� id

		// ��O�� �g�J key ����� ------------




		$updt = array ( 'id'			=> $parm['ori_id'],
						'field_name'	=> 'copykey',
						'field_value'	=> $parm['copykey']	);

		if (!$this->update_field($updt)) {
			$this->msg->add("Error ! �L�k ��s���ưO��.");
			$this->msg->merge($sql->msg);
			return false;    
		}







//		$this->msg->add("���\ �ƻs�˥��O��: [".$parm['style_code']."]�C") ;

	
	// �D�� ������ӹϤW��[�j��]��[�p��]
			//���ɥؿ�(�H pdt_id �ӳ]�����ɦW
			$upFile = new uploadIMG();
			$style_dir	= $GLOBALS['config']['root_dir']."/pics/";  
			$no_img		= $GLOBALS['config']['root_dir']."/images/graydot.gif";
		if($parm['pic1_upload'])  {   // 2005/01/04 �令��javascript���X�ʤ@�� hidden�� �e�J��x
//		if($parm['pic1'] <> "none") {
						# �W�ǹϬ۳B�z
			//�W�Ǥj�� 600X600
			$upFile->setSaveTo($style_dir,$pdt_id.".jpg");
			$up_result = $upFile->upload($parm['pic1'], 600, 600);
			//�W�Ǥp�� 150x150
			$upFile->setSaveTo($style_dir,"s".$pdt_id.".jpg");
			$up_result = $upFile->upload($parm['pic1'], 150, 150);
				if ($up_result){
					$this->msg->add("���\�W�ǥD��");
				} else {
					$this->msg->add("�W�ǥD�� ����");
				}
		}else{
			if(file_exists($style_dir.$parm['ori_id'].".jpg")){  // �ƻs�����ɦ��Ϯ�
				copy($style_dir.$parm['ori_id'].".jpg",$style_dir.$pdt_id.".jpg");
				copy($style_dir."s".$parm['ori_id'].".jpg",$style_dir."s".$pdt_id.".jpg");
			} else {
				// �Ngray �ϴ_�s �@��
				copy($no_img,$style_dir.$pdt_id.".jpg");
				copy($no_img,$style_dir."s".$pdt_id.".jpg");
			}
		}


	// ���� A�W��
		if($parm['pic2_upload'])  {   // 2005/01/04 �令��javascript���X�ʤ@�� hidden�� �e�J��x
//		if($parm['pic2'] <> "none"){
						# �W�ǹϬ۳B�z
			//���ɥؿ�(�H pdt_id �ӳ]�����ɦW �e���[�W "A" //�W�ǹ� 320X320
			$upFile->setSaveTo($style_dir,"A".$pdt_id.".jpg");
			$up_result = $upFile->upload($parm['pic2'], 320, 320);
				if ($up_result){
					$this->msg->add("���\�W�� ���Ϥ@");
				} else {
					$this->msg->add("�W�Ǧ��Ϥ@ ����");
				}
		}else{
			if(file_exists($style_dir."A".$parm['ori_id'].".jpg")){  // �ƻs�����ɦ��Ϯ�
				copy($style_dir."A".$parm['ori_id'].".jpg",$style_dir."A".$pdt_id.".jpg");
			} else {
				// �Ngray �ϴ_�s �@��
				copy($no_img,$style_dir."A".$pdt_id.".jpg");
			}
		}

	// ���� B�W��
		if($parm['pic3_upload'])  {   // 2005/01/04 �令��javascript���X�ʤ@�� hidden�� �e�J��x
//		if($parm['pic3'] <> "none"){
						# �W�ǹϬ۳B�z
			//���ɥؿ�(�H pdt_id �ӳ]�����ɦW �e���[�W "B" //�W�ǹ� 320X320
			$upFile->setSaveTo($style_dir,"B".$pdt_id.".jpg");
			$up_result = $upFile->upload($parm['pic3'], 320, 320);
				if ($up_result){
					$this->msg->add("���\�W�� ���ϤG");
				} else {
					$this->msg->add("�W�Ǧ��ϤG ����");
				}
		}else{
			if(file_exists($style_dir."B".$parm['ori_id'].".jpg")){  // �ƻs�����ɦ��Ϯ�
				copy($style_dir."B".$parm['ori_id'].".jpg",$style_dir."B".$pdt_id.".jpg");
			} else {
				// �Ngray �ϴ_�s �@��
				copy($no_img,$style_dir."B".$pdt_id.".jpg");
			}
		}


		return $pdt_id;

	} // end func
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0,$where_str="")	�j�M �˥� ���	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search($mode=0,$where_str="") {

		$sql = $this->sql;
		$argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv
		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}
	
		$q_header = "SELECT * FROM smpl ".$where_str;
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("style_code DESC , id DESC ");
		$srh->row_per_page = 12;

		if ($argv['PHP_sr_startno']) {
			$srh->add_limit_condition($argv['PHP_sr_startno']);
		} 
if ($mode==1){
		if ($str = $argv['PHP_dept_code'] )  { 
			$srh->add_where_condition("dept = '$str'", "PHP_dept_code",$str,"�����O = [ $str ]. "); }

		if ($str = strtoupper($argv['PHP_style_code']) )  { 
			$srh->add_where_condition("style_code LIKE '%$str%'", "PHP_style_code",$str,"�j�M �ڦ��s���t��[ $str ]���e. "); }
		
		if ($str = $argv['PHP_cust'] )  { 
			$srh->add_where_condition("cust = '$str'", "PHP_cust",$str,"�j�M �Ȥ� = [ $str ]. "); }
		if ($str = $argv['PHP_cust_ref'] )  { 
			$srh->add_where_condition("cust_ref LIKE '%$str%'", "PHP_cust_ref",$str,"�j�M�Ȥ�s���t��: [ $str ]���e "); }

		if ($str = $argv['PHP_year'] )  { 
			$srh->add_where_condition("year = '$str'", "PHP_year",$str,"�j�M�~�� = [ $str ]. "); }
		if ($str = $argv['PHP_season'] )  { 
			$srh->add_where_condition("season = '$str'", "PHP_season",$str,"�j�M�u�` = [ $str ]. "); }
		if ($str = $argv['PHP_style_type'] )  { 
			$srh->add_where_condition("style_type = '$str'", "PHP_style_type",$str,"�j�M�ڦ����O = [ $str ]. "); }

}		
		$result= $srh->send_query2();
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
			if (!$result){   // ��d�M�L��Ʈ�
				$op['record_NONE'] = 1;
			}

		$op['smpl'] = $result;  // ��ƿ� �ߤJ $op
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['cgistr_post'] = $srh->get_cgi_str(1);
		$op['prev_no'] = $srh->prev_no;
		$op['next_no'] = $srh->next_no;
		$op['max_no'] = $srh->max_no;
		$op['last_no'] = $srh->last_no;
		$op['start_no'] = $srh->start_no;
		$op['per_page'] = $srh->row_per_page;

		if ($op['start_no'] == 0) {	$op['PREV_page'] = ""; } else {	$op['PREV_page'] = "1"; }
		if ($op['next_no'] == 0) {	$op['NEXT_page'] = ""; } else {	$op['NEXT_page'] = "1"; }

		return $op;
	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $smpl_code=0)	��X���w�O������� RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get($id=0, $smpl_code=0) {

		$sql = $this->sql;

		if ($id)	{
			$q_str = "SELECT * FROM smpl WHERE id='$id' ";
		} elseif ($smpl_code) {
			$q_str = "SELECT * FROM smpl WHERE style_code='$smpl_code' ";
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
		$q_str = "UPDATE smpl SET cust='"		.$parm['cust'].
							"',	cust_ref='"		.$parm['cust_ref'].
							"', season='"		.$parm['season'].
							"', updator='"		.$parm['updator'].
							"', size_type='"	.$parm['size_type'].
							"', size_scale='"	.$parm['size_scale'].
							"', style_type='"	.$parm['style_type'].
							"', style_relate='"	.$parm['style_relate'].
							"', quota='"		.$parm['quota'].
							"', chief_val='"	.$parm['chief_val'].
							"', ling_val='"		.$parm['ling_val'].
							"', lastupdate=		NOW()" .
							", memo='"			.$parm['comments'].
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
			$style_dir	= $GLOBALS['config']['root_dir']."/pics/";  
			$no_img		= $GLOBALS['config']['root_dir']."/images/graydot.gif";

		if($parm['pic1_upload'])  {   // 2005/01/04 �令��javascript���X�ʤ@�� hidden�� �e�J��x
//		if(($parm['pic1'] != "") && ($parm['pic1'] != "none"))  {   // *** �`�N : �bunix�U�i��|�O "none" ���O "".........
//echo "<br>[debug] parm['pic1']=====> ".$parm['pic1'];
//echo "<br>[debug] pic1 ����s�ɮ� ";
//exit;

						# �W�ǹϬ۳B�z
				//2004/05/03 ���ˬd�O�_�s�b �p�s�b�N������
//				$file1 = $style_dir.$pdt_id.".jpg";
			if(file_exists($style_dir.$pdt_id.".jpg")){
				unlink($style_dir.$pdt_id.".jpg") or die("�L�k�R���¹���:".$pdt_id.".jpg");  // �R������
			}
				//�W�Ǥj�� 600X600
				$upFile->setSaveTo($style_dir,$pdt_id.".jpg");
				$up_result = $upFile->upload($parm['pic1'], 600, 600);

				//2004/05/03 ���ˬd�O�_�s�b �p�s�b�N������
			if(file_exists($style_dir."s".$pdt_id.".jpg")){
				unlink ($style_dir."s".$pdt_id.".jpg") or die("�L�k�R���¹���:"."s".$pdt_id.".jpg");  // �R������
			}
				//�W�Ǥp�� 150x150
				$upFile->setSaveTo($style_dir,"s".$pdt_id.".jpg");
				$up_result = $upFile->upload($parm['pic1'], 150, 150);
		}

//echo "<br>[debug] pic1 �S�� ��s�ɮ� ";
//exit;


	// ���� A�W��
		if($parm['pic2_upload'])  {   // 2005/01/04 �令��javascript���X�ʤ@�� hidden�� �e�J��x
//		if(($parm['pic2'] != "") && ($parm['pic2'] != "none")){
			//2004/05/03 ���ˬd�O�_�s�b �p�s�b�N������
			if(file_exists($style_dir."A".$pdt_id.".jpg")){
				unlink($style_dir."A".$pdt_id.".jpg") or die("�L�k�R���¹���:"."A".$pdt_id.".jpg");  // �R������
			}
						# �W�ǹϬ۳B�z
			//���ɥؿ�(�H pdt_id �ӳ]�����ɦW �e���[�W "A" //�W�ǹ� 320x320
			$upFile->setSaveTo($style_dir,"A".$pdt_id.".jpg");
			$up_result = $upFile->upload($parm['pic2'], 320, 320);
		}
	// ���� B�W��
		if($parm['pic3_upload'])  {   // 2005/01/04 �令��javascript���X�ʤ@�� hidden�� �e�J��x
//		if(($parm['pic3'] != "") && ($parm['pic3'] != "none")){
			//2004/05/03 ���ˬd�O�_�s�b �p�s�b�N������
			if(file_exists($style_dir."B".$pdt_id.".jpg")){
				unlink($style_dir."B".$pdt_id.".jpg") or die("�L�k�R���¹���:"."B".$pdt_id.".jpg");  // �R������
			}
						# �W�ǹϬ۳B�z
			//���ɥؿ�(�H pdt_id �ӳ]�����ɦW �e���[�W "B" //�W�ǹ� 320x320
			$upFile->setSaveTo($style_dir,"B".$pdt_id.".jpg");
			$up_result = $upFile->upload($parm['pic3'], 320, 320);
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
	$q_str = "UPDATE smpl SET ".$parm['field_name']."='".$parm['field_value']."'  WHERE id='".$parm['id']."' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k��s��Ʈw���e.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del($id)		�R�� �˥��O��  [��ID]�R��
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del($id) {

		$sql = $this->sql;
		if (!$id) {
			$this->msg->add("Error !�Ы��� ������ ��ƪ� ID.");		    
			return false;
		}
		$q_str = "DELETE FROM smpl WHERE id='$id' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s����Ʈw !");
			$this->msg->merge($sql->msg);
			return false;    
		}

			// �Ϫ��R�� -------
			$style_dir	= $GLOBALS['config']['root_dir']."/pics/";  

			if(file_exists($style_dir.$id.".jpg")){
				unlink($style_dir.$id.".jpg") or die("�L�k�R������:".$id.".jpg");  // �R������
			}
			if(file_exists($style_dir."s".$id.".jpg")){
				unlink ($style_dir."s".$id.".jpg") or die("�L�k�R������:"."s".$id.".jpg");  // �R������
			}
			if(file_exists($style_dir."A".$id.".jpg")){
				unlink($style_dir."A".$id.".jpg") or die("�L�k�R������:"."A".$id.".jpg");  // �R������
			}
			if(file_exists($style_dir."B".$id.".jpg")){
				unlink($style_dir."B".$id.".jpg") or die("�L�k�R������:"."B".$id.".jpg");  // �R������
			}






		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fields($n_field,$where_str="")	���X���� �������� $n_field �m�Jarry return
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_fields($n_field,$where_str="") {
		$sql = $this->sql;
		$fields = array();
		$q_str = "SELECT ".$n_field." FROM smpl ".$where_str;

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

} // end class


?>