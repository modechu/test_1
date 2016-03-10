<?php 

#++++++++++++++++++++++ revise ORDER  class ##### ��s �q ��  +++++++++++++++++++++++++++++++++++
#	->init($sql)							�ҩl (�ϥ� Msg_handle(); ���p�W sql)
#	->add($parm)							�[�J
#	->search($mode=0)						�j�M   
#	->get($id=0, nbr=0)						��X���w �O�������   
#	->add_edit($parm)						��s revise order �O�� [   ]
#	->edit($parm)							��s �㵧���



#	divert_month_su($qty, $ratio, $s_date, $f_date)
#	->update_field($parm)					��s ��Ƥ� �Y�ӳ�@���
#	->del($id)								�R�� ��ƿ�
#	->get_fields($n_field,$where_str="")	���X���� �������� $n_field �m�Jarry return
#
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class C_ORDER {
		
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
#	->add($parm)		�[�J�s revise �q��O��
#						�Ǧ^ $id
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add($parm) {
					
		$sql = $this->sql;

					# �[�J��Ʈw
		$q_str = "INSERT INTO cancer_ord (order_num,creator,opendate,dept,cust,ref,factory,style,qty,unit,style_num,patt_num,ptn_upload,smpl_ord,uprice,quota,mat_u_cost,mat_useage,acc_u_cost,quota_fee,comm_fee,cm,smpl_fee,etd,etp,gmr,ie_time1,ie_time2,ie1,ie2,su,cfmer,cfm_date,smpl_apv,smpl_apv_type,smpl_apv2,smpl_apv_type2,smpl_apv3,smpl_apv_type3,apver,apv_date,schd_date,schd_er,last_updator,last_update,status,revise,emb,wash,oth,oth_treat,cancer_date,cancer_user,remark) VALUES('".
							$parm['order_num']."','".
							$parm['creator']."','".
							$parm['opendate']."','".
							$parm['dept']."','".
							$parm['cust']."','".
							$parm['ref']."','".
							$parm['factory']."','".
							$parm['style']."','".
							$parm['qty']."','".
							$parm['unit']."','".
							$parm['style_num']."','".
							$parm['patt_num']."','".
							$parm['ptn_upload']."','".
							$parm['smpl_ord']."','".
							$parm['uprice']."','".
							$parm['quota']."','".
							$parm['mat_u_cost']."','".
							$parm['mat_useage']."','".
							$parm['acc_u_cost']."','".
							$parm['quota_fee']."','".
							$parm['comm_fee']."','".
							$parm['cm']."','".
							$parm['smpl_fee']."','".
							$parm['etd']."','".
							$parm['etp']."','".
							$parm['gmr']."','".
							$parm['ie_time1']."','".
							$parm['ie_time2']."','".
							$parm['ie1']."','".
							$parm['ie2']."','".
							$parm['su']."','".
							$parm['cfmer']."','".
							$parm['cfm_date']."','".
							$parm['smpl_apv']."','".
							$parm['smpl_apv_type']."','".
							$parm['smpl_apv2']."','".
							$parm['smpl_apv_type2']."','".
							$parm['smpl_apv3']."','".
							$parm['smpl_apv_type3']."','".
							$parm['apver']."','".
							$parm['apv_date']."','".
							$parm['schd_date']."','".
							$parm['schd_er']."','".
							$parm['last_updator']."','".
							$parm['last_update']."','".
							$parm['status']."','".
							$parm['revise']."','".
							$parm['emb']."','".
							$parm['wash']."','".
							$parm['oth']."','".
							$parm['oth_treat']."','".
							$parm['cancer_date']."','".
							$parm['cancer_user']."','".
							$parm['remark']."')";
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot append order");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$ord_id = $sql->insert_id();  //���X �s�� id

		$this->msg->add("append order#: [".$parm['order_num']."]�C") ;

		return $ord_id;

	} // end func
	

#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $wi_num=0)	��X���w�O������� RETURN $row[]
#++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get($id=0, $order_num=0) {

		$sql = $this->sql;

		if ($id)	{
			$q_str = "SELECT * FROM r_order WHERE id='$id' ";
		} elseif ($order_num) {
			$q_str = "SELECT * FROM r_order WHERE order_num='$order_num' ";
		} else {
			$this->msg->add("Error ! �Ы��� �q�� �b��Ʈw���� ID.");		    
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

} // end class


?>