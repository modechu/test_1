<?php 

#++++++++++++++++++++++ DEPT  class ##### �����O +++++++++++++++++++++++++++++++++++
#	->init($sql)							�ҩl (�ϥ� Msg_handle(); ���p�W sql)
#	->add($parm)							�[�J
#	->search($mode=0)						�j�M   
#	->get($id=0, nbr=0)						��X���w �O�������   
#	->get_field_value($dept_code,$field_name)	��dept_code ��X�Y���$field_name����
#	->update($parm)							��s �㵧���
#	->update_field($parm)					��s ��Ƥ� �Y�ӳ�@���
#	->del($id)								�R�� ��ƿ�
#	->get_fields($n_field,$where_str="")	���X���� �������� $n_field �m�Jarry return
#	->get_smpl_serious($dept, $year_code)	�Ѧ~�פγ����O ���X smpl ���Ǹ�
#	->get_smpl_num($year_code, $cust): �Ѧ~�פγ����O ��X �˥��q�� ���Ǹ�  �A�s�J��Ʈw
#	->get_wis_num($dept_code, $year_code): �Ѧ~�פγ����O ��X �˥��s�y�O ���Ǹ�  �A�s�J��Ʈw
#	->get_fabric_serious($dept_code, $year_code,$kind):		�Ѧ~�פγ����O ��X smpl ���Ǹ�  �A�s�J��Ʈw
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class DEPT {
		
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


/*----------
# �\�໡�� : 
# �ɤJ�ܼ� : 
# ��J�ܼ� : 
# ��X�ܼ� : 
# ���g��� : 2010/06/02
# �Ƶ����� : 
----------*/

function search($mode=0,$where_str='',$table='',$m_sql='',$page_view='',$show_num = 10) {
	global $Search;

	if(!empty($set_dept))$run2=1;
	if(!empty($run2)){
		unset($_SESSION['PAGE'][$page_view]);
		$status=0;
		$where_str = "where ";
		if (!empty($run2)){
			$where_str .= $set_dept;
			$status=1;
		}
	}

	$op = $Search->page_sorting($table,$m_sql,$page_view,$where_str,$show_num);

	return $op;
} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_field_value($n_field,$where_str="")	���X���� �������� $n_field �m�Jarry return
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_field_value($n_field,$where_str="") {
  $sql = $this->sql;
  
  $fields = array();
  $q_str = "SELECT ".$n_field." FROM dept ".$where_str;
  // echo $q_str,'<br>';
  if (!$q_result = $sql->query($q_str)) {
    $_SESSION['msg'][] = ("Error! �L�k�s����Ʈw!");
    return false;
  }

  while ($row = $sql->fetch($q_result)) {
    $fields[] = $row;
  }

  return $fields;
} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		�[�J�s�����O
#		�[�J�s�����O			�Ǧ^ $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add($parm) {

		$sql = $this->sql;
			############### �ˬd��J����					
		if (!$parm['dept_code'] = trim($parm['dept_code'])) {
			$this->msg->add("Error ! �п�J�����N�� !");
		    return false;
		}
					# �����令�j�g
		$parm['dept_code'] = strtoupper($parm['dept_code']);	

					# �ˬd�O�_������
		$q_str = "SELECT id FROM dept WHERE dept_code='".$parm['dept_code']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s����Ʈw.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if ($sql->num_rows($q_result) ) {
			$this->msg->add("SORRY ! �o�ӥN���w�g���O�������ϥΤF�A�д��O���N��!");
			return false;    	    
		}
					# �[�J��Ʈw
		$q_str = "INSERT INTO dept (dept_name, dept_code, chief) VALUES ('".$parm['dept_name']."','".$parm['dept_code']."','".$parm['chief']."')";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s�W��ưO��.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$dept_id = $sql->insert_id();  //���X �s�� id
		$this->msg->add("���\ �s�W�����O : [".$parm['dept_code']."]�C") ;
		return $dept_id;

	} // end func
	
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0)	�j�M �����O���	 
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search2($mode=0) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv
		$srh = new SEARCH();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$q_header = "SELECT * FROM dept";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("dept_code ");
		$srh->row_per_page = 12;

		if ($argv['PHP_sr_startno']) {
			$srh->add_limit_condition($argv['PHP_sr_startno']);		    
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

		$op['dept'] = $result;  // ��ƿ� �ߤJ $op
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
#	->get($id=0, dept_code=0)	��X���w�O������� RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get($id=0, $dept_code=0) {

		$sql = $this->sql;

		if ($id)	{
			$q_str = "SELECT * FROM dept WHERE id='$id' ";
		} elseif ($dept_code) {
			$q_str = "SELECT * FROM dept WHERE dept_code='$dept_code' ";
		} else {
			$this->msg->add("Error ! �Ы��� ������Ʀb��Ʈw���� ID.");		    
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
#	->update($parm)		��s �����O���
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update($parm) {

		$sql = $this->sql;
		############### �ˬd��J����
					# �����令�j�g
		$parm['dept_code'] = strtoupper($parm['dept_code']);	

					# �ˬd�O�_���o���ɮצs�b
		$q_str = "SELECT id FROM dept WHERE id='".$parm['id']."'";
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s����Ʈw.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$sql->num_rows($q_result) ) {
			$this->msg->add("SORRY ! �L�k����Ʈw�O��!");
			return false;    	    
		}

###########  �߬d �s�[�J�� dept_code �O�_���ߤ@ [note:�i��|����Ӫ��ۤv #####
$q_str2 = "SELECT id FROM dept WHERE dept_code='".$parm['dept_code']."'"." AND id <> '".$parm['id']."'";

		if (!$q_result2 = $sql->query($q_str2)) {
			$this->msg->add("Error ! �L�k�s����Ʈw.");
			return false;    	    
		}
		if ($sql->num_rows($q_result2) ) {
			$this->msg->add("SORRY ! �o�ӥN���w�g���O�������ϥΤF�A�д��O���N��!");
			return false;    	    
		}
		
		#####   ��s��Ʈw���e
		$q_str = "UPDATE dept SET dept_code='".$parm['dept_code']."', dept_name='".$parm['dept_name']."', chief='". $parm['chief']."'  WHERE id='".$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  �L�k��s��Ʈw.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($parm)		��s �����O��Ƥ� �Y�ӳ�@���
#								$parm = [$id, $field_name, $field_value]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field($parm) {

		$sql = $this->sql;
		############### �ˬd��J����
		
		#####   ��s��Ʈw���e
		$q_str = "UPDATE dept SET ".$parm['field_name']."='".$parm['field_value']."'  WHERE id='".$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k��s��Ʈw���e.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del($id)		�R�� �����O���  [��ID]�R��
#			
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del($id) {

		$sql = $this->sql;
		if (!$id) {
			$this->msg->add("Error !�Ы��� dept ��ƪ� ID.");		    
			return false;
		}
		$q_str = "DELETE FROM dept WHERE id='$id' ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s����Ʈw !");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fields($n_field,$where_str="")	���X���� �������� $n_field �m�Jarry return
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_fields($n_field,$where_str="") {
		$sql = $this->sql;
		$fields = array();
		$q_str = "SELECT ".$n_field." FROM dept".$where_str;
// echo $q_str;
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


# ????????????????????????????????????????????????????????????
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_smpl_serious($dept_code, $year_code): �Ѧ~�פγ����O ��X smpl ���Ǹ�  �A�s�J��Ʈw
#      �B��� dept_code ���X smpl_num�A�[�W �����O�Φ~�X(���)�F�p�Ӧ~���ɦ۰ʥ[�W �Gsmpl_num��cvs
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	function get_smpl_serious($dept_code, $year_code) {
		$sql = $this->sql;

		$dept = $this->get(0,$dept_code);

		if (!$dept) {
			$this->msg->add("Error ! �L�k�s�������O��Ʈw.");		    
			return false;
		}

		$year_serious = decode_number($dept['style_num']);    //�~�קǸ����}�C....�H��X���D

			if (isset($year_serious[$year_code])) {   // �Ǹ� �s�b��

				$serious = $year_serious[$year_code] + 1+1000000;
				$style_code = "S".$dept_code.$year_code."-".substr($serious,3);
				$new_num = substr($serious,3);
				$year_serious[$year_code] = $new_num;
				$style_num = encode_number($year_serious);
					
			} else {

				$style_code = "S".$dept_code.$year_code."-"."0001";
				$year_serious[$year_code] = "0001";
				$style_num = encode_number($year_serious);  // �令cvs�r��[function.php]
			}

			//  �g�J�s�� num �J��Ʈw
			$q_str = "UPDATE dept SET style_num ='".$style_num."'  WHERE dept_code ='".$dept_code."'";

			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! �L�k��s��Ʈw���e.");
				$this->msg->merge($sql->msg);
				return false;    
			}
		
		return $style_code;

	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_wis_num($dept_code, $year_code): �Ѧ~�פγ����O ��X �˥��s�y�O ���Ǹ�  �A�s�J��Ʈw
#      �B��� dept_code ���X wsi_num�A�[�W �����O�Φ~�X(���)�F�p�Ӧ~���ɦ۰ʥ[�W �Gwsi_num��cvs
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	function get_wis_num($dept_code, $year_code) {
		$sql = $this->sql;

		$dept = $this->get(0,$dept_code);

		if (!$dept) {
			$this->msg->add("Error ! �L�k�s�������O��Ʈw.");		    
			return false;
		}

		$year_serious = decode_number($dept['wis_num']);    //�~�קǸ����}�C....�H��X���D

					if (isset($year_serious[$year_code])) {   // �Ǹ� �s�b��

						$serious = $year_serious[$year_code] + 1+1000000;
						$style_code = "WI-".$dept_code.$year_code."-".substr($serious,3);
						$new_num = substr($serious,3);
						
						$year_serious[$year_code] = $new_num;
						$style_num = encode_number($year_serious);
					
					} else {

						$style_code = "WI-".$dept_code.$year_code."-"."0001";
						$year_serious[$year_code] = "0001";
						$style_num = encode_number($year_serious);  // �令cvs�r��[function.php]
					}

			//  �g�J�s�� num �J��Ʈw
			$q_str = "UPDATE dept SET wis_num ='".$style_num."'  WHERE dept_code ='".$dept_code."'";

			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! �L�k��s��Ʈw���e.");
				$this->msg->merge($sql->msg);
				return false;    
			}
		
		return $style_code;

	} // end func



#############   2005/05/17 ��s �s�@�O �s��
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_wi_num($dept_code, $year_code): �Ѧ~�פγ����O ��X �˥��s�y�O ���Ǹ�  �A�s�J��Ʈw
#      �B��� dept_code ���X wsi_num�A�[�W �����O�Φ~�X(���)�F�p�Ӧ~���ɦ۰ʥ[�W �Gwsi_num��cvs
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	function get_wi_num($dept_code, $year_code) {
		$sql = $this->sql;

		$dept = $this->get(0,$dept_code);

		if (!$dept) {
			$this->msg->add("Error ! �L�k�s�������O��Ʈw.");		    
			return false;
		}

		$year_serious = decode_number($dept['wis_num']);    //�~�קǸ����}�C....�H��X���D

					if (isset($year_serious[$year_code])) {   // �Ǹ� �s�b��

						$serious = $year_serious[$year_code] + 1+1000000;
						$style_code = "WI-".$dept_code.$year_code."-".substr($serious,3);
						$new_num = substr($serious,3);
						
						$year_serious[$year_code] = $new_num;
						$style_num = encode_number($year_serious);
					
					} else {

						$style_code = "WIS-".$dept_code.$year_code."-"."0001";
						$year_serious[$year_code] = "0001";
						$style_num = encode_number($year_serious);  // �令cvs�r��[function.php]
					}

			//  �g�J�s�� num �J��Ʈw
			$q_str = "UPDATE dept SET wis_num ='".$style_num."'  WHERE dept_code ='".$dept_code."'";

			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! �L�k��s��Ʈw���e.");
				$this->msg->merge($sql->msg);
				return false;    
			}
		
		return $style_code;

	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_order_num($dept_code, $year_code, $cust): �Ѧ~�פγ����O ��X �q�� ���Ǹ�  �A�s�J��Ʈw
#      �B��� dept_code ���X order_num�A�[�W �����O�Φ~�X(���)�F�p�Ӧ~���ɦ۰ʥ[�W �Gorder_num��cvs
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	# ���t�~�O���s�W�q��ɳ������O��
	function get_order_num_close($dept_code, $year_code, $cust) {
		$sql = $this->sql;
		$dept_name = $dept_code;

		if($dept_code == 'KB') $dept_code = 'KA';
		if($dept_code == 'J1') $dept_code = 'KA';
		if($dept_code == 'LY') $dept_code = 'KA';
		
		$dept = $this->get(0,$dept_code);  // ���b��Ʈw��ݦ��S���ӵ���� 

		if (!$dept) {
			$this->msg->add("Error ! �L�k�s�������O��Ʈw.");		    
			return false;
		}

		$year_serious = decode_ord_number($dept['order_num']);    //�~�קǸ����}�C....�H��X���D

					if (isset($year_serious[$year_code])) {   // �Ǹ� �s�b��
						$serious = $year_serious[$year_code] + 1+1000;
						#M11010501
						// echo $dept_code;
						if ( $dept_name == "HJ" || $dept_name == 'LY' || $dept_name == "DA" || $dept_name == "CF" )//�P�_�����O�_�N��u�t,�N��u�t��==>���������Ĥ@�Ӧr
						{															  //���N��u�t��==>���������ĤG�Ӧr	
							$order_code = substr($dept_name,0,1).$cust.$year_code."-".substr($serious,1);							
						}else{
							$order_code = substr($dept_name,1,1).$cust.$year_code."-".substr($serious,1);
						}
						$new_num = substr($serious,1);
						
						$year_serious[$year_code] = $new_num;
						$order_num = encode_number($year_serious);
					
					} else {
						$order_code = substr($dept_name,1,1).$cust.$year_code."-"."0001";
						$year_serious[$year_code] = "0001";
						$order_num = encode_number($year_serious);  // �令cvs�r��[function.php]
					}
					

			//  �g�J�s�� num �J��Ʈw
			$q_str = "UPDATE dept SET order_num ='".$order_num."'  WHERE dept_code ='".$dept_code."'";

			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! �L�k��s��Ʈw���e.");
				$this->msg->merge($sql->msg);
				return false;    
			}
		
		return $order_code;

	} // end func



#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_order_num($dept_code, $year_code, $cust): �Ѧ~�פγ����O ��X �q�� ���Ǹ�  �A�s�J��Ʈw
#      �B��� dept_code ���X order_num�A�[�W �����O�Φ~�X(���)�F�p�Ӧ~���ɦ۰ʥ[�W �Gorder_num��cvs
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	function get_pre_order_num($dept_code, $year_code, $cust) {
		$sql = $this->sql;

		$dept = $this->get(0,$dept_code);  // ���b��Ʈw��ݦ��S���ӵ���� 

		if (!$dept) {
			$this->msg->add("Error ! �L�k�s�������O��Ʈw.");		    
			return false;
		}

		$year_serious = decode_ord_number($dept['pre_ord_num']);    //�~�קǸ����}�C....�H��X���D

					if (isset($year_serious[$year_code])) {   // �Ǹ� �s�b��
						$serious = $year_serious[$year_code] + 1+1000;
						$order_code = "P".$year_code.$cust."-".substr($serious,1);
						$new_num = substr($serious,1);
						
						$year_serious[$year_code] = $new_num;
						$order_num = encode_number($year_serious);
					
					} else {
						$order_code = "P".$year_code.$cust."-"."001";
						$year_serious[$year_code] = "001";
						$order_num = encode_number($year_serious);  // �令cvs�r��[function.php]
					}
					

			//  �g�J�s�� num �J��Ʈw
			$q_str = "UPDATE dept SET pre_ord_num ='".$order_num."'  WHERE dept_code ='".$dept_code."'";

			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! �L�k��s��Ʈw���e.");
				$this->msg->merge($sql->msg);
				return false;    
			}
		
		return $order_code;

	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_smpl_num($year_code, $cust): �Ѧ~�פγ����O ��X �˥��q�� ���Ǹ�  �A�s�J��Ʈw
#      �B��g�J dept_code="PM"(�ͥ�) "S"+�~�X(����)+�Ȥ�(���)+4��y��
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	function get_smpl_num($year_code, $cust) {
		$sql = $this->sql;

		$dept = $this->get(0,'PM');  // ����Ʈw��ݦ��S���ͥ��o�ӳ�� [�N���� PM] 

		if (!$dept) {
			$this->msg->add("Error ! cannot get dept PM from database.");		    
			return false;
		}

		$year_serious = decode_smpl_number($dept['order_num']);    //�~�קǸ����}�C....�H��X���D

			if (isset($year_serious[$year_code])) {   // �Ǹ� �s�b��
				$serious = $year_serious[$year_code] + 1+10000;
				$order_code = "S".$year_code.$cust."-".substr($serious,1);
				$new_num = substr($serious,1);
						
				$year_serious[$year_code] = $new_num;
				$order_num = encode_number($year_serious);
					
			} else {
				$order_code = "S".$year_code.$cust."-"."0001";
				$year_serious[$year_code] = "0001";
				$order_num = encode_number($year_serious);  // �令cvs�r��[function.php]

			}

			//  �g�J�s�� num �J��Ʈw [�g�J�ͥ��� order_num �椺 ----
			$q_str = "UPDATE dept SET order_num ='".$order_num."'  WHERE dept_code ='PM'";

			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! unable to update DEPT database.");
				$this->msg->merge($sql->msg);
				return false;    
			}
		
		return $order_code;

	} // end func





#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fabric_serious($dept_code, $year_code,$kind): �Ѧ~�פγ����O ��X smpl ���Ǹ�  �A�s�J��Ʈw
#      �B��� dept_code ���X smpl_num�A�[�W �����O�Φ~�X(���)�F�p�Ӧ~���ɦ۰ʥ[�W �Gsmpl_num��cvs
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	function get_fabric_serious($dept_code, $year_code, $kind) {
		$sql = $this->sql;

		$dept = $this->get(0,'PM');

		if (!$dept) {
			$this->msg->add("Error ! �L�k�s�������O��Ʈw.");		    
			return false;
		}

		$year_serious = decode_number($dept['fabric_num']);    //�~�קǸ����}�C....�H��X���D

					if (isset($year_serious[$year_code])) {   // �Ǹ� �s�b��

						$serious = $year_serious[$year_code] + 1+1000000;
						$art_code = "F".$dept_code.$year_code.$kind."-".substr($serious,3);
						$new_num = substr($serious,3);
						$year_serious[$year_code] = $new_num;
						$fabric_num = encode_number($year_serious);
					
					} else {

						$art_code = "F".$dept_code.$year_code.$kind."-"."0001";
						$year_serious[$year_code] = "0001";
						$fabric_num = encode_number($year_serious);  // �令cvs�r��[function.php]
					}

			//  �g�J�s�� num �J��Ʈw
			$q_str = "UPDATE dept SET fabric_num ='".$fabric_num."'  WHERE dept_code ='PM'";

			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! �L�k��s��Ʈw���e.");
				$this->msg->merge($sql->msg);
				return false;    
			}
		
		return $art_code;

	} // end func


#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fabric_serious($dept_code, $year_code,$kind): �Ѧ~�פγ����O ��X smpl ���Ǹ�  �A�s�J��Ʈw
#      �B��� dept_code ���X smpl_num�A�[�W �����O�Φ~�X(���)�F�p�Ӧ~���ɦ۰ʥ[�W �Gsmpl_num��cvs
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	function get_fab_stock_serious($dept_code, $year_code, $kind) {
		$sql = $this->sql;

		$dept = $this->get(0,'PM');

		if (!$dept) {
			$this->msg->add("Error ! �L�k�s�������O��Ʈw.");		    
			return false;
		}

		$year_serious = decode_number($dept['fabstock_num']);    //�~�קǸ����}�C....�H��X���D

					if (isset($year_serious[$year_code])) {   // �Ǹ� �s�b��

						$serious = $year_serious[$year_code] + 1+1000000;
						$art_code = "F".$dept_code.$year_code.$kind."-".substr($serious,3);
						$new_num = substr($serious,3);
						$year_serious[$year_code] = $new_num;
						$fabric_num = encode_number($year_serious);
					
					} else {

						$art_code = "F".$dept_code.$year_code.$kind."-"."0001";
						$year_serious[$year_code] = "0001";
						$fabric_num = encode_number($year_serious);  // �令cvs�r��[function.php]
					}

			//  �g�J�s�� num �J��Ʈw
			$q_str = "UPDATE dept SET fabstock_num ='".$fabric_num."'  WHERE dept_code ='PM'";

			if (!$q_result = $sql->query($q_str)) {
				$this->msg->add("Error ! �L�k��s��Ʈw���e.");
				$this->msg->merge($sql->msg);
				return false;    
			}
		
		return $art_code;

	} // end func

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


} // end class


?>