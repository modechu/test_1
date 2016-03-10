<?php 

#++++++++++++++++++++++ FABRIC  class ##### �}�o����  +++++++++++++++++++++++++++++++++++
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

class FABRIC {
		
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
#	->check($parm, $mode=0)		�ˬd �[�J�s ���ưO�� �O�_���T
#								# mode =0:�@��add��check,  mode=1: edit�ɪ�check
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function check($parm, $mode=0) {

		$this->msg = new MSG_HANDLE();
			############### �ˬd��J����
	if($mode==0){
		if (!$parm['kind']) {
			$this->msg->add("Error ! �п�� ���� �C");
//		    return false;
		}
	}
		if (!$parm['cat']) {
			$this->msg->add("Error ! �п�� ���� �C");
//		    return false;
		}
		if (!$parm['name'] = trim($parm['name'])) {
			$this->msg->add("Error ! �п�J���W�A�p�L���W�п�J[N/A] �C");
//		    return false;
		}

		// ������� ���ݭn��J��---------
		if (trim($parm['weight'])){
			if (!$parm['unit_wt']) {
				$this->msg->add("Error ! �п�� �X����� �C");
//				return false;
			}
		}
		
		if (trim($parm['width'])){
			if (!$parm['width_unit']) {
				$this->msg->add("Error ! �п�� �X�e��� �C");
//				return false;
			}
		}

		if (trim($parm['price'])){
			if (!$parm['currency']) {
				$this->msg->add("Error ! �п�� ���ȳ�� �C");
//				return false;
			}
		}
		if (trim($parm['price'])){
			if (!$parm['unit_price']) {
				$this->msg->add("Error ! �п�� �p����� �C");
//				return false;
			}
		}

		if (trim($parm['term'])){
			if (!$parm['location']) {
				$this->msg->add("Error ! �п�� �������󪺦a�I �C");
//				return false;
			}
		}

		if (count($this->msg->get(2))){
			return false;
		}
					
		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		�[�J�s ���ưO��
#						�Ǧ^ $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add($parm) {
					
		$sql = $this->sql;

					# �[�J��Ʈw
		$q_str = "INSERT INTO fabric (art_code,
									cat,
									supl,
									supl_ref,
									name,
									content,
									width,
									width_unit,
									weight,
									unit_wt,
									construct,
									finish,
									co,
									price,
									unit_price,
									term,
									location,
									leadtime,
									currency,
									remark) VALUES('".
									$parm['art_code']."','".
									$parm['cat']."','".
									$parm['supl']."','".
									$parm['supl_ref']."','".
									$parm['name']."','".
									$parm['content']."','".
									$parm['width']."','".
									$parm['width_unit']."','".
									$parm['weight']."','".
									$parm['unit_wt']."','".
									$parm['construct']."','".
									$parm['finish']."','".
									$parm['co']."','".
									$parm['price']."','".
									$parm['unit_price']."','".
									$parm['term']."','".
									$parm['location']."','".
									$parm['leadtime']."','".
									$parm['currency']."','".
									$parm['remark']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s�W��ưO��.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$pdt_id = $sql->insert_id();  //���X �s�� id

		$this->msg->add("���\ �s�W ���ưO��: [".$parm['art_code']."]�C") ;

	
	// �D�� ������ӹϤW��[�j��]��[�p��]
			//���ɥؿ�(�H pdt_id �ӳ]�����ɦW
			$upFile = new uploadIMG();
			$style_dir	= $GLOBALS['config']['root_dir']."/fabric/";  
			$no_img		= $GLOBALS['config']['root_dir']."/images/graydot.gif";
		if($parm['pic_upload'])  {   // 2005/01/04 �令��javascript���X�ʤ@�� hidden�� �e�J��x
//		if($parm['pic1'] <> "none") {
						# �W�ǹϬ۳B�z
			//�W�Ǥj�� 600X600
			$upFile->setSaveTo($style_dir,$pdt_id.".jpg");
			$up_result = $upFile->upload($parm['pic'], 600, 600);
			//�W�Ǥp�� 150x150
//			$upFile->setSaveTo($style_dir,"s".$pdt_id.".jpg");
//			$up_result = $upFile->upload($parm['pic'], 150, 150);
				if ($up_result){
					$this->msg->add("���\�W�ǥD��");
				} else {
					$this->msg->add("�W�ǥD�� ����");
				}
		}else{
			// �Ngray �ϴ_�s �@��
			copy($no_img,$style_dir.$pdt_id.".jpg");
//			copy($no_img,$style_dir."s".$pdt_id.".jpg");
		}

		return $pdt_id;

	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0,$where_str='')	�j�M  ���	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search($mode=0,$where_str="",$limit_entries=0) {

		$sql = $this->sql;
//		$argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv
		$argv = $_SESSION['sch_parm'];
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

		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['action']);
		$srh->add_sort_condition("id DESC ");
		if($limit_entries) 
		{
			$srh->row_per_page = $limit_entries;
		}else{
			$srh->row_per_page = 20;
		}

	if($limit_entries){    // �����w�̤j�q��~~~ 2005/11/28 �[�J
			$srh->q_limit = "LIMIT ".$limit_entries." ";
			
	}else{
##--*****--2006.11.16���X�s�W start		##	
			
		$pagesize=10;
		if ($argv['sr_startno']) {
			$pages = $srh->get_page($argv['sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
 ##--*****--2006.11.16���X�s�W end	   ##
	}

if ($mode==1){
		if ($str =$argv['cat'] )  { 
			$srh->add_where_condition("cat = '$str'", "PHP_cat",$str,"Fabric cate.= [ $str ]. "); }
		if ($str = $argv['art_code'] )  { 
			$srh->add_where_condition("art_code LIKE '%$str%'", "PHP_name",$str,"Fabric # :[ $str ]. "); }
		if ($str = $argv['name'] )  { 
			$srh->add_where_condition("name LIKE '%$str%'", "PHP_name",$str,"Name :[ $str ]. "); }
		if ($str = $argv['content'] )  { 
			$srh->add_where_condition("content LIKE '%$str%'", "PHP_content",$str,"Content: [ $str ] "); }
		if ($str = $argv['supl_ref'] )  { 
			$srh->add_where_condition("supl_ref LIKE '%$str%'", "PHP_supl_ref",$str,"Supplier ref. : [ $str ] "); }
		if ($str = $argv['finish'] )  { 
			$srh->add_where_condition("finish LIKE '%$str%'", "PHP_finish",$str,"Finish : [ $str ] "); }
		if ($str = $argv['supl'] )  { 
			$srh->add_where_condition("supl = '$str'", "PHP_supl",$str,"Supplier = [ $str ]. "); }
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

		$op['fabric'] = $result;  // ��ƿ� �ߤJ $op
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['cgistr_post'] = $srh->get_cgi_str(1);
		$op['max_no'] = $srh->max_no;
		$op['start_no'] = $srh->start_no;
		
if(!$limit_entries){ 
##--*****--2006.11.16���X�s�W start			
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] =  $pages;
		$op['now_pp'] = $srh->now_pp;
    $op['lastpage']=$pages[$pagesize-1];		
##--*****--2006.11.16���X�s�W end
}


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
#	->search($mode=0,$where_str='')	�j�M  ���	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search_stock($mode=0,$where_str="",$limit_entries=0) {

		$sql = $this->sql;
//		$argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv
		$argv = $_SESSION['sch_parm'];
		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT fab_stock.*, lots.lots_name, lots.comp, lots.specify 
								 FROM fab_stock, lots ".$where_str;
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['action']);
		$srh->add_sort_condition("fab_stock.art_code DESC ");
		if($limit_entries) 
		{
			$srh->row_per_page = $limit_entries;
		}else{
			$srh->row_per_page = 20;
		}

	if($limit_entries){    // �����w�̤j�q��~~~ 2005/11/28 �[�J
			$srh->q_limit = "LIMIT ".$limit_entries." ";
			
	}else{
##--*****--2006.11.16���X�s�W start		##	
			
		$pagesize=10;
		if ($argv['sr_startno']) {
			$pages = $srh->get_page($argv['sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
 ##--*****--2006.11.16���X�s�W end	   ##
	}
		$srh->add_where_condition("fab_stock.art_code = lots.lots_code");
if ($mode==1){
		if ($str =$argv['cat'] )  { 
			$srh->add_where_condition("fab_stock.cat = '$str'", "PHP_cat",$str,"Fabric cate.= [ $str ]. "); }
		if ($str = $argv['art_code'] )  { 
			$srh->add_where_condition("fab_stock.art_code LIKE '%$str%'", "PHP_name",$str,"Fabric # :[ $str ]. "); }
		if ($str = $argv['name'] )  { 
			$srh->add_where_condition("fab_stock.name LIKE '%$str%'", "PHP_name",$str,"Name :[ $str ]. "); }
		if ($str = $argv['content'] )  { 
			$srh->add_where_condition("lots.comp LIKE '%$str%'", "PHP_content",$str,"Content: [ $str ] "); }
		if ($str = $argv['supl_ref'] )  { 
			$srh->add_where_condition("fab_stock.supl_ref LIKE '%$str%'", "PHP_supl_ref",$str,"Supplier ref. : [ $str ] "); }
		if ($str = $argv['finish'] )  { 
			$srh->add_where_condition("lots.cons LIKE '%$str%'", "PHP_finish",$str,"Finish : [ $str ] "); }
		if ($str = $argv['supl'] )  { 
			$srh->add_where_condition("fab_stock.supl = '$str'", "PHP_supl",$str,"Supplier = [ $str ]. "); }
		if ($str = $argv['fty'] )  { 
			$srh->add_where_condition("fab_stock.fty = '$str'", "PHP_fty",$str,"stock area = [ $str ]. "); }

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

		$op['fabric'] = $result;  // ��ƿ� �ߤJ $op
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['cgistr_post'] = $srh->get_cgi_str(1);
		$op['max_no'] = $srh->max_no;
		$op['start_no'] = $srh->start_no;
		
if(!$limit_entries){ 
##--*****--2006.11.16���X�s�W start			
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] =  $pages;
		$op['now_pp'] = $srh->now_pp;
    $op['lastpage']=$pages[$pagesize-1];		
##--*****--2006.11.16���X�s�W end
}


		return $op;
	} // end func	


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		�[�J�s ���ưO��
#						�Ǧ^ $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_stock($parm) {
					
		$sql = $this->sql;


		foreach($parm as $key => $value)$parm[$key] = str_replace("'","\'",$parm[$key]);	
					# �[�J��Ʈw
		$q_str = "INSERT INTO fab_stock (art_code,
									cat,
									supl,
									supl_ref,
									name,
									co,
									qty,
									unit,
									fty,
									ord_num,
									est_qty,
									po_unit,
									bom_color,
									remark) VALUES('".
									$parm['mat_code']."','".
									$parm['cat']."','".
									$parm['supl']."','".
									$parm['supl_ref']."','".
									$parm['name']."','".
									$parm['co']."','".
									$parm['qty']."','".
									$parm['unit']."','".
									$parm['fty']."','".
									$parm['ord_num']."','".
									$parm['est_qty']."','".
									$parm['po_unit']."','".
									$parm['bom_color']."','".
									$parm['remark']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s�W��ưO��.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$pdt_id = $sql->insert_id();  //���X �s�� id

		$this->msg->add("���\ �s�W ���ưO��: [".$parm['art_code']."]�C") ;

	
	// �D�� ������ӹϤW��[�j��]��[�p��]
			//���ɥؿ�(�H pdt_id �ӳ]�����ɦW
			$upFile = new uploadIMG();
			$style_dir	= $GLOBALS['config']['root_dir']."/fab_stock/";  
			$no_img		= $GLOBALS['config']['root_dir']."/images/graydot.gif";
		if($parm['pic_upload'])  {   // 2005/01/04 �令��javascript���X�ʤ@�� hidden�� �e�J��x
						# �W�ǹϬ۳B�z
			//�W�Ǥj�� 600X600
			$upFile->setSaveTo($style_dir,$pdt_id.".jpg");
			$up_result = $upFile->upload($parm['pic'], 600, 600);

       //�W�Ǥp�� 50x50
       $upFile->setSaveTo($style_dir,"s".$pdt_id.".jpg");
       $up_result = $upFile->upload($parm['pic'], 50, 50);
			
				if ($up_result){
					$this->msg->add("���\�W�ǥD��");
				} else {
					$this->msg->add("�W�ǥD�� ����");
				}
		}else{
			// �Ngray �ϴ_�s �@��
			copy($no_img,$style_dir.$pdt_id.".jpg");
		}

		return $pdt_id;

	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $art_code=0)	��X���w�O������� RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_stock($id=0, $art_code=0) {

		$sql = $this->sql;

		if ($id)	{
			$q_str = "SELECT fab_stock.*, lots.comp, lots.specify, lots.width, lots.weight, lots.cons,
											 lots.lots_name 
								FROM fab_stock, lots WHERE art_code = lots.lots_code AND fab_stock.id='$id' ";
		} elseif ($fabric_code) {
			$q_str = "SELECT fab_stock.*, lots.comp, lots.specify, lots.width, lots.weight, lots.cons,
										   lots.lots_name
								FROM fab_stock, lots WHERE art_code = lots.lots_code AND art_code='$art_code' ";
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
	function edit_stock($parm) {

		$sql = $this->sql;

		foreach($parm as $key => $value)$parm[$key] = str_replace("'","\'",$parm[$key]);	


		#####   ��s��Ʈw���e
		$q_str = "UPDATE fab_stock SET cat='"		.$parm['cat'].
							"',	supl='"				.$parm['supl'].
							"', co='"					.$parm['co'].
							"', name='"				.$parm['name'].

							"', supl_ref='"		.$parm['supl_ref'].
							"', qty='"				.$parm['qty'].
							"', unit='"				.$parm['unit'].
							"', fty='"				.$parm['fty'].

							"', remark='"			.$parm['remark'].

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
			$style_dir	= $GLOBALS['config']['root_dir']."/fab_stock/";  
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

        //�W�Ǥp�� 150x150
        $upFile->setSaveTo($style_dir,"s".$pdt_id.".jpg");
        $up_result = $upFile->upload($parm['pic'], 50, 50);


		}

		
		return $pdt_id;
	} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del($id)		�R�� ���ưO��  [��ID]�R��
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del_stock($id) {

		$sql = $this->sql;
		if (!$id) {
			$this->msg->add("Error !�Ы��� ���� ��ƪ� ID.");		    
			return false;
		}
		$q_str = "DELETE FROM fab_stock WHERE id='$id' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s����Ʈw !");
			$this->msg->merge($sql->msg);
			return false;    
		}

			// �Ϫ��R�� -------
			$style_dir	= $GLOBALS['config']['root_dir']."/fab_stock/";  

			if(file_exists($style_dir.$id.".jpg")){
				unlink($style_dir.$id.".jpg") or die("�L�k�R������:".$id.".jpg");  // �R������
			}

			if(file_exists($style_dir."s".$id.".jpg")){
				unlink($style_dir."s".$id.".jpg") or die("�L�k�R������:".$id.".jpg");  // �R������
			}

		return true;
	} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $order_num=0)	��X���w�q��O����� RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_po_stock($code,$color,$ord_num) {
			$sql = $this->sql;
			$rtn = $exc_rec = array();
			$mk = 0;
			// sup_code
//�@�����
			$q_str = "SELECT 		lots_use.lots_code as mat_code, lots_name, bom_lots.color, 
													sum(ap_det.ap_qty) as ap_qty,  sum(ap_det.po_qty) as po_qty, 
													ap_det.unit, bom_lots.id as bom_id, smpl_code, ap_det.po_unit,
													ap.sup_code
								FROM  		ap_det , bom_lots, lots_use, ap
								WHERE 		ap_det.bom_id = bom_lots.id AND lots_use.id = bom_lots.lots_used_id AND 
													ap.ap_num = ap_det.ap_num AND ap.status = 12 AND ap.special = 0 AND
													ap_det.mat_cat = 'l' AND lots_use.lots_code = '".$code."' AND 
													bom_lots.color = '".$color."' AND smpl_code = '".$ord_num."'
								GROUP BY  smpl_code";				
//echo $q_str."<BR><BR>";
			$q_result = $sql->query($q_str);
			while($row = $sql->fetch($q_result))
			{
				$row['ap_qty'] = change_unit_qty($row['unit'],$row['po_unit'],$row['ap_qty']);	
				$rtn = $row; 
				$mk = 1;
			}

//�B�~����
			$q_str = "SELECT 		lots_use.lots_code as mat_code, lots_name, bom_lots.color, 
													sum(ap_det.ap_qty) as ap_qty,  sum(ap_det.po_qty) as po_qty, 
													ap_det.unit, bom_lots.id as bom_id, smpl_code, ap_det.po_unit,
													ap.sup_code
								FROM  		ap_det , bom_lots, lots_use, ap
								WHERE 		ap_det.bom_id = bom_lots.id AND lots_use.id = bom_lots.lots_used_id AND 
													ap.ap_num = ap_det.ap_num AND ap.status = 12 AND ap.special > 0 AND
													ap_det.mat_cat = 'l' AND lots_use.lots_code = '".$code."' AND 
													bom_lots.color = '".$color."' AND smpl_code = '".$ord_num."'
								GROUP BY  smpl_code";		
			#M11020802 �ק�䤣�� wi ��� ���� status �M color ���P�_
			$q_str = "SELECT 		lots_use.lots_code as mat_code, lots_name, bom_lots.color, 
													sum(ap_det.ap_qty) as ap_qty,  sum(ap_det.po_qty) as po_qty, 
													ap_det.unit, bom_lots.id as bom_id, smpl_code, ap_det.po_unit,
													ap.sup_code
								FROM  		ap_det , bom_lots, lots_use, ap
								WHERE 		ap_det.bom_id = bom_lots.id AND lots_use.id = bom_lots.lots_used_id AND 
													ap.ap_num = ap_det.ap_num AND 
													ap_det.mat_cat = 'l' AND lots_use.lots_code = '".$code."' AND 
													smpl_code = '".$ord_num."'
								GROUP BY  smpl_code";				
// echo $q_str."<BR><BR>";
			$q_result = $sql->query($q_str);
			while($row = $sql->fetch($q_result))
			{
				$row['ap_qty'] = 0;	
				if($mk == 0)
				{
					$rtn =  $row;
					$mk = 1;
				}else{
					$po_qty = change_unit_qty($row['po_unit'],$rtn['po_unit'],$row['po_qty']);	
					$ap_qty = change_unit_qty($row['po_unit'],$rtn['po_unit'],$row['ap_qty']);	
					$rtn['po_qty'] +=  $po_qty;
					$rtn['ap_qty'] +=  $ap_qty;
				}				
			}



//�w������
			$q_str = "SELECT  ap_special.mat_code, ap_special.color, bom_lots.qty as bom_qty, lots_name,
												po_unit, lots_use.unit, ap_special.po_qty, lots_use.smpl_code, ap.sup_code
								FROM  	bom_lots , ap_special, lots_use, ap
								WHERE 	bom_lots.ap_mark = ap_special.ap_num AND bom_lots.lots_used_id = lots_use.id AND 
												ap.ap_num = ap_special.ap_num AND
												bom_lots.color = ap_special.color AND lots_use.lots_code = ap_special.mat_code AND
												lots_use.lots_code = '".$code."' AND bom_lots.color = '".$color."' AND
												lots_use.smpl_code = '".$ord_num."'"
												;	
// echo $q_str."<BR><BR>";	
			$q_result = $sql->query($q_str);
			while($row = $sql->fetch($q_result))
			{
				$tmp_qty = explode(',',$row['bom_qty']);
				$row['ap_qty'] = array_sum($tmp_qty);
				$row['ap_qty'] = change_unit_qty($row['unit'],$row['po_unit'],$row['ap_qty']);	
				if($mk == 0)
				{
						$rtn =  $row;
				}else{
						$po_qty = change_unit_qty($row['po_unit'],$rtn['po_unit'],$row['po_qty']);	
						$ap_qty = change_unit_qty($row['po_unit'],$rtn['po_unit'],$row['ap_qty']);	
						$rtn['po_qty'] +=  $po_qty;
						$rtn['ap_qty'] +=  $ap_qty;
				}
			}
			$rtn['est_qty'] = $rtn['po_qty'] - $rtn['ap_qty'];


		return $rtn;
	} // end func		






#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

} // end class


?>