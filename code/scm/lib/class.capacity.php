<?php 

#++++++++++++++++++++++ CAPACITY  class ##### �}�o����  +++++++++++++++++++++++++++++++++++
#	->init($sql)							�ҩl (�ϥ� Msg_handle(); ���p�W sql)
#   ->divert_month_su($qty, $ratio, $s_date, $f_date)   ���t�U����� su �ǥX �}�C[200507=>254 ..]
#	->add_etp_su($parm)		�[�J order monthy etp su [etp_su]
#	->add_fty_su($parm)		�[�J order fty schedule su [fty_su]

#	->add($parm)							�[�J
#	->search($mode=0)						�j�M   
#	->get($id=0, nbr=0)						��X���w �O�������   
#	->edit($parm)							��s �㵧���
#	->update_field($parm)					��s ��Ƥ� �Y�ӳ�@���
#	->del($id)								�R�� ��ƿ�
#	->get_fields($n_field,$where_str="")	���X���� ���� $n_field �m�Jarry return
#
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class CAPACITY {
		
	var $sql;
	var $msg ;
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->init($sql)	�ҩl(�ϥ� Msg_handle() ; ���p�W sql)
#		�����p�W sql �~�i  �ҩl
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function init($sql) {
		$this->msg = new MSG_HANDLE();

		if (!$sql) {
			$this->msg->add("Error ! Database can't connect.");
		    return false;
		}
		$this->sql = $sql;
		return true;
	} // end func


/*
################# ????   2005/07/28 ��J  class order   �令�e�X csv ####################
#	divert_month_su($qty, $ratio, $s_date, $f_date)
# Detail description	: �N �ƶq ����su �A���t��Ͳ������ 
#						: �Ǧ^ �}�C ( 200505=>su , 200506=>su, ......
####################################################
function divert_month_su($qty, $ratio, $s_date, $f_date) {

 		$div = array();
   
		list($s_year,$s_mon,$s_day) = split("-",$s_date);
		list($f_year,$f_mon,$f_day) = split("-",$f_date);
		$days =	countDays($s_date,$f_date);
		$T_su = intval($ratio * $qty);		// �` su ��
		$day_su = $T_su/$days;		// �C�鲣�X --(���p)

		// �p���`�@���X�Ӥ��?
		$y = $f_year - $s_year;
		$m = 12*$y + (12-$s_mon+1) - (12-$f_mon);

echo "<br>total qty = ".$qty." are equal to  ".$T_su." s.u.<br>";
echo "<br>from ".$s_date." to ".$f_date." totally distribute for ".$m." �Ӥ��; �p ".$days."�� <br>";

			$su_year = $s_year;	// �~�����p�ƾ�
			$su_mon = $s_mon;	// ������p�ƾ�

			$divered_su =0;	// �w�g�έp��su�� �����̫�뤧�

		for ($i=0; $i<$m; $i++){
			if($su_mon >12){
				$su_year = $su_year+1;
				$su_mon = 1;
			}

		$mon[$i] = sprintf("%04d%02d", $su_year, $su_mon);   // ������аO


			// �p��C�몺�Ѽ� ---- �N su ���t�i�J
		if($s_mon==$f_mon){   // �p�G�}�l�M�̫�O�P�����-----
				$d = $f_day - $s_day ;
				$su[$i] = intval($day_su * $d);
		}else{
			if ($i==0){  // �Ĥ@�Ӥ�
				$d = getDaysInMonth($su_mon,$su_year)- intval($s_day);
				$su[$i] = intval($day_su * $d);
			} elseif($i==$m-1){  // �̫�@�Ӥ�
				$d = intval($f_day);
				$su[$i] = $T_su - $divered_su;
			} else{
				$d = getDaysInMonth($su_mon,$su_year);
				$su[$i] = intval($day_su * $d);
			}
		}

		$divered_su = $divered_su + $su[$i];

echo "<br>   distribute-month ==> ".$mon[$i]." ==>".$d." days ==> for ".$su[$i]." su" ;
		$su_mon = $su_mon+1;
		$tmp_m = $mon[$i];
		$div[$tmp_m] = $su[$i];   // �m�J array 
		}


return $div;





} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add_etp_su($parm)		�[�J order monthy su [etp_su]
#								�Ǧ^ $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_etp_su($parm) {
					
		$sql = $this->sql;

					# �[�J��Ʈw
		$q_str = "INSERT INTO etp_su (mon,
									factory,
									ord_num,
									etp_su) VALUES('".
									$parm['mon']."','".
									$parm['factory']."','".
									$parm['ord_num']."','".
									$parm['etp_su']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s�W ".$parm['ord_num']." etp_su ��ưO��.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$pdt_id = $sql->insert_id();  //���X �s�� id

		return $pdt_id;

	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add_fty_su($parm)		�[�J order fty schedule su [fty_su]
#								�Ǧ^ $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_fty_su($parm) {
					
		$sql = $this->sql;

					# �[�J��Ʈw
		$q_str = "INSERT INTO fty_su (mon,
									factory,
									ord_num,
									etp_su) VALUES('".
									$parm['mon']."','".
									$parm['factory']."','".
									$parm['ord_num']."','".
									$parm['fty_su']."')";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s�W ".$parm['ord_num']." fty_su ��ưO��.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$pdt_id = $sql->insert_id();  //���X �s�� id

		return $pdt_id;

	} // end func
*/

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_capacity_su($field,$fty='',$mon='', $tbl='capacity')	���X �Y��  field����
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_capacity_su($field, $fty='',$mon='',$tbl='capacity') {
		$sql = $this->sql;
		$row = array();

  		$q_str = "SELECT ".$field." FROM ".$tbl." WHERE factory='".$fty."' AND mon='".$mon."' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! Database can't access!");
			$this->msg->merge($sql->msg);
			return false;    
		}
			
		$row = $sql->fetch($q_result);

		$field_val = $row[0];

		return $field_val;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_su_field($fty,$mon,$su_field,$val)		��s capacity ��ƿ� �Y�ӳ�@���
#													###### �]���u�� su ��i�Ϊ����[�� *******				
#													###### �]���u�� su ��i�Ϊ����[�� *******				
#													###### �]���u�� su ��i�Ϊ����[�� *******				
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_su_field($fty, $mon, $su_field, $val) {

		$sql = $this->sql;
		############### �ˬd��J����
		
		#####   ��s��Ʈw���e  // �����b sql ���[�J
	$q_str = "UPDATE capacity SET ".$su_field."=".$su_field."+".$val."  WHERE factory='".$fty."' AND mon='".$mon."' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k��s��Ʈw���e.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func



/*
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
		}
	}
		if (!$parm['cat']) {
			$this->msg->add("Error ! �п�� ���� �C");
		}
		if (!$parm['name'] = trim($parm['name'])) {
			$this->msg->add("Error ! �п�J���W�A�p�L���W�п�J[N/A] �C");
		}

		// ������� ���ݭn��J��---------
		if (trim($parm['weight'])){
			if (!$parm['unit_wt']) {
				$this->msg->add("Error ! �п�� �X����� �C");
			}
		}
		
		if (trim($parm['width'])){
			if (!$parm['width_unit']) {
				$this->msg->add("Error ! �п�� �X�e��� �C");
			}
		}

		if (trim($parm['price'])){
			if (!$parm['currency']) {
				$this->msg->add("Error ! �п�� ���ȳ�� �C");
			}
		}
		if (trim($parm['price'])){
			if (!$parm['unit_price']) {
				$this->msg->add("Error ! �п�� �p����� �C");
			}
		}

		if (trim($parm['term'])){
			if (!$parm['location']) {
				$this->msg->add("Error ! �п�� �������󪺦a�I �C");
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
#	->search($mode=0,$where_str="")	�j�M ���� ���	 
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
		$srh->row_per_page = 48;

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
			$this->msg->add("Error ! Database can't access!");
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
			$this->msg->add("Error ! Database can't access !");
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
			$this->msg->add("Error! Database can't access!");
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

*/





#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

} // end class


?>