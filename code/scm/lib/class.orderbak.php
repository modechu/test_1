<?php 

#++++++++++++++++++++++++++++++++++ ORDER  class ##### �q ��  ++++++++++++++++++++++++++++++++++++++++
#	->init($sql)		�ҩl (�ϥ� Msg_handle(); ���p�W sql)
#	->check($parm)		�ˬd �[�J�s �q��O�� �O�_���T
#	->mat_schedule_check($parm)		�ˬd �[�Jmat_schedule �O�� �O�_���T
#	->pd_schedule_check($parm)		�ˬd �[�J�Ͳ��Ƶ{ schedule �O�� �O�_���T
#	->check_add_ie($ie_time)		�ˬd �[�J�sIE�u�� �O�_���T  �ݥ��� �����
#	->update_2fields($field1, $field2, $value1, $value2, $id, $table='s_order')	�P�ɧ�s��� field����
#	->update_ie($parm)	
#	->add_material_etd($parm)		[�g�J�D�ƮƤ� ETD ]
#	->add_pd_schedule($parm)		[�g�J producting ETS �� ETF ]
#	->add_cfm_pd_schedule($parm)	[�g�J producting ETS �� ETF �� fty_su ] �T�{ �u�t�Ƶ{
#	->update_sorder_4_cfm_pd_schedule($parm)	[�g�J producting status �� �O���Ƶ{��ΤH]
#	->mat_ship($pd_id, $shp_date)		��s �q�� �O�� [�g�J�D�� �X�f��� ]
#	->macc_ship($pd_id, $shp_date)		��s �q�� �O�� [�g�J�D�n�ƮƮ� �X�f��� ]
#	->acc_ship($pd_id, $shp_date)		��s �q�� �O�� [�g�J�䥦�Ʈ� �X�f��� ]
#	->add_shipping($parm)		��s �q�� �X�f �O�� [�g�J pdtion -> qty_shp �� shp_date ]

#	->add($parm)				�[�J�s �q��O��  �Ǧ^ $id
#	->search($mode=0, $dept='',$limit_entries=0)			�j�M �q �� ���
#	->ord_search($mode=0, $where_str='', $limit_entries=0)	�j�M �q �� ���	 
#			mode = 2 :		�Ʋ��T�{�᪺�q��j�M ( status >= 7 )
#	->pdt_search($parm, $limit_entries=500)	�j�M �q�� �Ͳ����	 
#	->apved_search($mode=0, $where_str='', $limit_entries=0)	�j�M �q �� ���	 
#			mode = 2 :		�T�{�᪺�q��j�M ( status >= 4 )
#	->uncfm_search($mode=0, $limit_entries=0)	�j�M �ݽT�{���q�� ���	 
#	->unapv_search($mode=0,$limit_entries=0)	�j�M �� �֥i ���q�� ���	 
#	->schedule_search($mode=0)	�j�M �q �� ���	 [ �֥i��q�� ]
#	->schedule_uncfm_search($fty_id=0)	�j�M �|���T�{ �Ͳ��Ƶ{���q�� ���	 
#	->mat_schedule_search($mode=0)	�j�M �q���� [ �D�ƨ�Ʀ�{ ]	 


#	->get($id=0, $order_num=0)	��X���w�q��O����� RETURN $row[]
#	->mat_schedule_get($id=0, $wi_num=0)	��X���w�O������� RETURN $row[]
#	->get_ord_output($ord_num)	�w���q�q�渹�X ���X���椧�O�� RETURN $row[]  ??????????
#	->get_fields_4_del_pdt($ord_num)	���q�q�渹�X ���X�n�R���Y�ӥͲ��O�����q�椺�e RETURN $row[]
#	->schedule_get($id=0, $order_num=0)	��X���w�O������� RETURN $row[]
#	->get_pdtion($order_num, $factory)	��X���w�� pdtion �O�� RETURN $row[]
#	->revise_pdtion($parm, $id='', $ord_num='')	��s pdtion�����e [ order revise �ɪ���s ]
#	->revise_apv_ord($parm, $ord_num='')	��s �w�֥i�᪺�q�� �� status=1 , etp_su, ftp_su, ets, etf �k�s

#	->add_edit($parm)			��s �s�y�O �O�� [ �[�J�s �s�O��step2]
#	->edit($parm,$mode=0)		��s �q�� �O��   mode=0 : EDIT    mode=1 : REVISE
#	->send_cfm($parm)		 �q��e�X �ݽT�{  
#	->do_cfm($parm)		�q�� �T�{ok  �e�X�� �֥i  
#	->reject_cfm($parm)		�q�� REJECT �T�{  
#	->do_apv($parm)		�q�� �֥iok  ############>>>>>> �w�w�Ʋ�  
#	->reject_apv($parm)		�q�� REJECT �֥i  
#	->pd_out_update($parm)		�u�t�����ƶq����s pdtion 
#	->update_smpl_apv($parm)		 ��s �˥��T�{��� --- by order_num  

#	->distri_month_su($T_su, $s_date, $f_date, $fty, $cat, $mode=0)  �|��s capacity �������
#		Detail description	: �N �ƶq ����su �A���t��Ͳ������ 
#							: �g�J capacity table �� $field �� [�p�S��� error ]
#							: �Ǧ^ �}�C ( 200505=>su , 200506=>su, ......
#					$mode === 0 �� �����`���[�J <>   $mode = 1 ��  ���[�J�@�ӭt�� �Y��h 

#	->creat_pdtion($parm)		�[�J �s�� pdtion �q��O�� [ �g�J�u�t �� etp_su �Ʋ���]
#	->update_field($field, $val, $id)			��s s_order��ưO���� �Y�ӳ�@���
#	->update_pdtion_field($field, $val, $id)	��s pdtion��ưO���� �Y�ӳ�@���
#	->del($id,$mode=0)		�R�� [��ID]�R��  $mode=0: $id= �O����id; $mode<>0: $id=ORDER_num

#	->get_field_value($field,$id='',$ord_num='', $tbl='s_order')	���X �Y��  field����
#	->shift($argv, $parm)	 �q�沾�� ��s s_order , pdtion ���e

#
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class ORDER {
		
	var $sql;
	var $msg ;
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->init($sql)	�ҩl(�ϥ� Msg_handle() ; ���p�W sql)
#		�����p�W sql �~�i  �ҩl
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function init($sql) {
		$this->msg = new MSG_HANDLE();

		if (!$sql) {
			$this->msg->add("Error ! �L�k�p�W��Ʈw.");
		    return false;
		}
		$this->sql = $sql;
		return true;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->check($parm)		�ˬd �[�J�s �q��O�� �O�_���T
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function check($parm) {

		$this->msg = new MSG_HANDLE();
			############### �ˬd��J����	
	
		if (!$parm['style'] ) {
			$this->msg->add("Error ! �п�� �ڦ����O(style)�C");
		}
		if (!$parm['unit'] ) {
			$this->msg->add("Error ! �п�� �p�q��� �C");
		}
		if (!is_numeric($parm['uprice'] )) {
			$this->msg->add("Error ! �п�J���T FOB ����C");
		}
		if (!is_numeric($parm['qty'] )) {
			$this->msg->add("Error ! �п�J���T�ƶq�C");
		}
		if (!is_numeric($parm['mat_u_cost'] ) && !$parm['mat_u_cost']=='') {
			$this->msg->add("Error ! �п�J���T �D�Ƴ�즨���C");
		}
		if (!is_numeric($parm['mat_useage'] ) && !$parm['mat_useage']=='') {
			$this->msg->add("Error ! �п�J���T �D�Ƴ��ζq�C");
		}
		if (!is_numeric($parm['acc_u_cost'] ) && !$parm['acc_u_cost']=='') {
			$this->msg->add("Error ! �п�J���T �ƮƳ�즨���C");
		}
		if (!is_numeric($parm['quota_fee'] ) && !$parm['quota_fee']=='') {
			$this->msg->add("Error ! �п�J���T �t�B��즨���C");
		}
		if (!is_numeric($parm['comm_fee'] ) && !$parm['comm_fee']=='') {
			$this->msg->add("Error ! �п�J���T ������즨���C");
		}
		if (!is_numeric($parm['cm'] ) && !$parm['cm']=='') {
			$this->msg->add("Error ! �п�J���T �uú��즨���C");
		}
		if (!is_numeric($parm['smpl_fee'] ) && !$parm['smpl_fee']=='') {
			$this->msg->add("Error ! �п�J���T �˫~�����C");
		}

		if (!is_numeric($parm['emb'] ) && !$parm['emb']=='') {
			$this->msg->add("Error ! please check the embroidery cost !");
		}
		if (!is_numeric($parm['wash'] ) && !$parm['wash']=='') {
			$this->msg->add("Error ! please check the garment-wash cost !");
		}
		if (!is_numeric($parm['oth'] ) && !$parm['oth']=='') {
			$this->msg->add("Error ! please check the other-treatment cost !");
		}

		if (!$parm['factory'] ) {
			$this->msg->add("Error ! please choose the manufacture FACTORY�C");
		}

		if (!checkdate($parm['etdmonth'],$parm['etdday'],$parm['etdyear'])){
			$this->msg->add("Error ! �п�J���T ETD����C");
		}

		if (!checkdate($parm['etpmonth'],$parm['etpday'],$parm['etpyear'])){
			$this->msg->add("Error ! �п�J���T ETP����C");
		}

		$etd = $parm['etdyear']."-".$parm['etdmonth']."-".$parm['etdday'];
		$etp = $parm['etpyear']."-".$parm['etpmonth']."-".$parm['etpday'];
		
		if ($etp > $etd) {
			$this->msg->add("Error ! you have the wrong date of ETP or ETD�C");
		}

		if (count($this->msg->get(2))){
			return false;
		}
				
		return true;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->mat_schedule_check($parm)		�ˬd �[�Jmat_schedule �O�� �O�_���T
#						
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function mat_schedule_check($parm) {

		$this->msg = new MSG_HANDLE();
			############### �ˬd��J����	
			// �i�H����J��� -----

		if (($parm['mat_etdmon'] =='') && ($parm['mat_etdday'] =='') && ($parm['mat_etdyear'] =='')){
			$parm['mat_etdmon'] ='00';
			$parm['mat_etdday'] ='00';
			$parm['mat_etdyear'] ='0000';
		}else{
			if (!checkdate($parm['mat_etdmon'],$parm['mat_etdday'],$parm['mat_etdyear'])){
				$this->msg->add("Error ! �п�J���T �D�� �� ETD �C");
			}
		}

		if (($parm['macc_etdmon'] =='') && ($parm['macc_etdday'] =='') && ($parm['macc_etdyear'] =='')){
			$parm['macc_etdmon'] ='00';
			$parm['macc_etdday'] ='00';
			$parm['macc_etdyear'] ='0000';
		}else{
			if (!checkdate($parm['macc_etdmon'],$parm['macc_etdday'],$parm['macc_etdyear'])){
				$this->msg->add("Error ! �п�J���T �D�n�ƮƤ� ETD �C");
			}
		}

		if (($parm['acc_etdmon'] =='') && ($parm['acc_etdday'] =='') && ($parm['acc_etdyear'] =='')){
			$parm['acc_etdmon'] ='00';
			$parm['acc_etdday'] ='00';
			$parm['acc_etdyear'] ='0000';
		}else{
			if (!checkdate($parm['acc_etdmon'],$parm['acc_etdday'],$parm['acc_etdyear'])){
				$this->msg->add("Error ! �п�J���T �䥦�ƮƤ� ETD �C");
			}
		}
		if (count($this->msg->get(2))){
			return false;
		}
		
		return true;

	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->pd_schedule_check($parm)		�ˬd �[�J�Ͳ��Ƶ{ schedule �O�� �O�_���T
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function pd_schedule_check($parm) {

		$this->msg = new MSG_HANDLE();
			############### �ˬd��J����	
			// �i�H����J��� -----

		if (($parm['ets_mon'] =='') && ($parm['ets_day'] =='') && ($parm['ets_year'] =='') || (!checkdate($parm['ets_mon'],$parm['ets_day'],$parm['ets_year']))){
				$this->msg->add("Error ! illegal date of ETS �C");
		}

		if (($parm['etf_mon'] =='') && ($parm['etf_day'] =='') && ($parm['etf_year'] =='') || (!checkdate($parm['etf_mon'],$parm['etf_day'],$parm['etf_year']))){
				$this->msg->add("Error ! illegal date of ETF �C");
		}

		$ets = $parm['ets_year']."-".$parm['ets_mon']."-".$parm['ets_day'];
		$etf = $parm['etf_year']."-".$parm['etf_mon']."-".$parm['etf_day'];
		
		if ($ets > $etf) {
			$this->msg->add("Error ! you have the wrong date between ETS or ETF�C");
		}

		if (count($this->msg->get(2))){
			return false;
		}
		
		return true;

	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->check_add_ie($ie_time)	�ˬd �[�J�sIE�u�� �O�_���T  �ݥ��� �����
#				# mode =0:�@��add��check,  mode=1: edit�ɪ�check
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function check_add_ie($ie_time) {

		$this->msg = new MSG_HANDLE();
			############### �ˬd��J����

//		$T = $ie_time;
		if (!(is_numeric($ie_time)&&(intval($ie_time)==floatval($ie_time)))){  // ���ݬ����

			$this->msg->add("Error ! please input the correct figure [numeric only] �C");

			return false;
		}

		return true;
	} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_2fields($field1, $field2, $value1, $value2, $id, $table='s_order')	
#
#		�P�ɧ�s��� field���� 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_2fields($field1, $field2, $value1, $value2, $id, $table='s_order') {

		$sql = $this->sql;

		#####   ��s��Ʈw���e
		############ �� ���Ť���� ~~~~ �N���n�g�J ~~~~~~

		$q_str = "UPDATE ".$table." SET ".$field1." ='".$value1.
									"', ".$field2." ='".$value2.
								"' WHERE id=".	$id;

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  �L�k��s��Ʈw.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return $id;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_ie($parm)	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_ie($parm) {

		$sql = $this->sql;

		#####   ��s��Ʈw���e
		############ �� ���Ť���� ~~~~ �N���n�g�J ~~~~~~

		$q_str = "UPDATE s_order SET ie1 ='"	.$parm['ie1'].
							"', ie_time1 ='"	.$parm['ie_time1'].
							"', su ='"			.$parm['su'].
							"', status ='"		.$parm['status'].
								"' WHERE id="	.$parm['id'];

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  �L�k��s��Ʈw.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		return $parm['id'];
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add_material_etd($parm)		 [�g�J�D�ƮƤ� ETD ]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_material_etd($parm) {

		$sql = $this->sql;

		#####   ��s��Ʈw���e
		############ �� ���Ť���� ~~~~ �N���n�g�J ~~~~~~

		$q_str = "UPDATE pdtion SET ";
		if ($parm['mat_etd']) { $q_str = $q_str."mat_etd='".$parm['mat_etd']."'"; }
		if ($parm['macc_etd']) { $q_str = $q_str.", m_acc_etd='".$parm['macc_etd']."'"; }
		if ($parm['acc_etd']) { $q_str = $q_str.", acc_etd='".$parm['acc_etd']."'"; }

		$q_str = $q_str." WHERE id=".$parm['pd_id'];

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  �L�k��s��Ʈw.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$pdt_id = $parm['pd_id'];

		return $pdt_id;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add_pd_schedule($parm)		[�g�J producting ETS �� ETF ]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_pd_schedule($parm) {

		$sql = $this->sql;

		#####   ��s��Ʈw���e
		############ �� ���Ť���� ~~~~ �N���n�g�J ~~~~~~

		$q_str = "UPDATE pdtion SET ";

		if ($parm['ets']) { $q_str = $q_str."ets='".$parm['ets']."'"; }
		if ($parm['etf']) { $q_str = $q_str.", etf='".$parm['etf']."'"; }

		$q_str = $q_str." WHERE id=".$parm['pd_id'];

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  �L�k��s��Ʈw.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$pdt_id = $parm['pd_id'];

		return $pdt_id;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add_cfm_pd_schedule($parm)	[�g�J producting ETS �� ETF �� fty_su ]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_cfm_pd_schedule($parm) {

		$sql = $this->sql;

		#####   ��s��Ʈw���e
		############ �� ���Ť���� ~~~~ �N���n�g�J ~~~~~~

		$q_str = "UPDATE pdtion SET ets='".		$parm['ets'].
								"', etf='".		$parm['etf'].
								"', fty_su='".	$parm['fty_su'].
								"' WHERE id=".	$parm['pd_id'];

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  �L�k��s��Ʈw.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$pdt_id = $parm['pd_id'];

		return $pdt_id;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_sorder_4_cfm_pd_schedule($parm)	[�g�J producting status �� �O���Ƶ{��ΤH]
#									�T�{ �u�t�Ƶ{ �ᤧ�D�q���s
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_sorder_4_cfm_pd_schedule($parm) {

		$sql = $this->sql;

		#####   ��s��Ʈw���e
		############ �� ���Ť���� ~~~~ �N���n�g�J ~~~~~~

		$q_str = "UPDATE s_order SET status='".		$parm['status'].
								"', schd_er='".		$parm['schd_er'].
								"', schd_date='".	$parm['schd_date'].
								"' WHERE id=".		$parm['id'];

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  �L�k��s��Ʈw.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$pdt_id = $parm['id'];

		return $pdt_id;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->mat_ship($pd_id, $shp_date)		��s �q�� �O�� [�g�J�D�� �X�f��� ]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function mat_ship($pd_id, $shp_date) {

		$sql = $this->sql;

		#####   ��s��Ʈw���e
		############ �� ���Ť���� ~~~~ �N���n�g�J ~~~~~~

		$q_str = "UPDATE pdtion SET mat_shp='".$shp_date."' WHERE id=".$pd_id;

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  �L�k��s��Ʈw.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$pdt_id = $pd_id;

		return $pdt_id;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->macc_ship($pd_id, $shp_date)		��s �q�� �O�� [�g�J�D�n�ƮƮ� �X�f��� ]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function macc_ship($pd_id, $shp_date) {

		$sql = $this->sql;

		#####   ��s��Ʈw���e
		############ �� ���Ť���� ~~~~ �N���n�g�J ~~~~~~

		$q_str = "UPDATE pdtion SET m_acc_shp='".$shp_date."' WHERE id=".$pd_id;

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  �L�k��s��Ʈw.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$pdt_id = $pd_id;

		return $pdt_id;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->acc_ship($pd_id, $shp_date)		��s �q�� �O�� [�g�J�䥦�Ʈ� �X�f��� ]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function acc_ship($pd_id, $shp_date) {

		$sql = $this->sql;

		#####   ��s��Ʈw���e
		############ �� ���Ť���� ~~~~ �N���n�g�J ~~~~~~

		$q_str = "UPDATE pdtion SET acc_shp='".$shp_date."' WHERE id=".$pd_id;

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  �L�k��s��Ʈw.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$pdt_id = $pd_id;

		return $pdt_id;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add_shipping($parm)	��s �q�� �X�f �O�� [�g�J pdtion -> qty_shp �� shp_date ]
#									�T�{ �u�t�Ƶ{
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_shipping($parm) {

		$sql = $this->sql;

		#####   ��s��Ʈw���e
		############ �� ���Ť���� ~~~~ �N���n�g�J ~~~~~~

		$q_str = "UPDATE pdtion SET qty_shp=qty_shp + ".$parm['qty_shp'].
								", shp_date='".		$parm['shp_date'].
								"' WHERE id=".		$parm['pd_id'];

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  �L�k��s��Ʈw.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$pdt_id = $parm['pd_id'];

		return $pdt_id;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		�[�J�s �q��O��
#						�Ǧ^ $id
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add($parm) {
					
		$sql = $this->sql;

					# �[�J��Ʈw
		$q_str = "INSERT INTO s_order (dept,cust,order_num,ref,factory,style,qty,unit,style_num,patt_num,smpl_ord,uprice,quota,mat_u_cost,mat_useage, acc_u_cost,quota_fee,comm_fee,cm,smpl_fee,etd,etp,gmr,creator,emb,wash,oth,oth_treat,opendate) VALUES('".
							$parm['dept']."','".
							$parm['cust']."','".
							$parm['order_num']."','".
							$parm['ref']."','".
							$parm['factory']."','".
							$parm['style']."','".
							$parm['qty']."','".
							$parm['unit']."','".
							$parm['style_num']."','".
							$parm['patt_num']."','".
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
							$parm['etdyear']."-".$parm['etdmonth']."-".$parm['etdday']."','".
							$parm['etpyear']."-".$parm['etpmonth']."-".$parm['etpday']."','".
							$parm['gmr']."','".
							$parm['creator']."','".
							$parm['emb']."','".
							$parm['wash']."','".
							$parm['oth']."','".
							$parm['oth_treat']."','".
							$parm['open_date']."')";
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot append order");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$ord_id = $sql->insert_id();  //���X �s�� id

		$this->msg->add("append order#: [".$parm['order_num']."]�C") ;
	
		if($parm['pic_upload'])  {   // 2005/01/04 �令��javascript���X�ʤ@�� hidden�� �e�J��x
//		if($parm['pic1'] <> "none") {
	// �D�� ������ӹϤW��[�j��]��[�p��]
			//���ɥؿ�(�H pdt_id �ӳ]�����ɦW
			$upFile = new uploadIMG();
			$style_dir	= $GLOBALS['config']['root_dir']."/picture/";  

						# �W�ǹϬ۳B�z
			//�W�ǹ� 600X600
			$upFile->setSaveTo($style_dir,$parm['order_num'].".jpg");
			$up_result = $upFile->upload($parm['pic'], 600, 600);

				if ($up_result){
					$this->msg->add("���\�W�ǥD��");
				} else {
					$this->msg->add("�W�ǥD�� ����");
				}
		}
	
		return $ord_id;

	} // end func
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0, $dept='',$limit_entries=0) �j�M �q �� ���
#					// 2005/11/24 �[�J $limit_entries
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function search($mode=0, $dept='',$limit_entries=0) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv

		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT * FROM s_order ";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("id DESC");
		$srh->row_per_page = 20;

	if($limit_entries){    // �����w�̤j�q��~~~ 2005/11/28 �[�J
			$srh->q_limit = "LIMIT ".$limit_entries." ";
	}else{
//---2006/1115
		$pagesize=10;
		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
//---20061115--------
	}


	//2006/05/12 adding 
	$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
	if($user_dept == "J0"){
		$srh->add_where_condition("dept LIKE '%J%'", "PHP_dept","J","");
	}elseif($user_dept == "K0"){
		$srh->add_where_condition("dept LIKE '%K%'", "PHP_dept","K","");
	}

	
	if ($dept){  //  ��n��������
		$srh->add_where_condition("dept = '$dept'", "",$dept,"�����O=[ $dept ]. ");
	}

   if ($mode==1){

		if ($str = strtoupper($argv['PHP_ref']) )  { 
			$srh->add_where_condition("ref LIKE '%$str%'", "PHP_ref",$str,"�j�M �Ȥ�Ѧҽs���t��:[ $str ]���e "); 
			}
		
		if ($str = $argv['PHP_cust'] )  { 
			$srh->add_where_condition("cust = '$str'", "PHP_cust",$str,"�j�M �Ȥ�=[ $str ]. "); 
			}
		if ($str = $argv['PHP_order_num'] )  { 
			$srh->add_where_condition("order_num LIKE '%$str%'", "PHP_order_num",$str,"�j�M �q��s���t��:[ $str ]���e "); 
			}
		if ($str = $argv['PHP_factory'] )  { 
			$srh->add_where_condition("factory = '$str'", "PHP_factory",$str,"�j�M �ӻs�u�t=[ $str ]. "); 
			}
   }	
		$result= $srh->send_query2($limit_entries);   // 2005/11/24 �[�J $limit_entries
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}

		$this->msg->merge($srh->msg);
			if (!$result){   // ��d�M�L��Ʈ�
				$op['record_NONE'] = 1;
			}
		$op['sorder'] = $result;  // ��ƿ� �ߤJ $op
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['cgistr_post'] = $srh->get_cgi_str(1);
		$op['prev_no'] = $srh->prev_no;
		$op['next_no'] = $srh->next_no;
		$op['max_no'] = $srh->max_no;
		$op['last_no'] = $srh->last_no;
		$op['start_no'] = $srh->start_no;
		$op['per_page'] = $srh->row_per_page;
if($limit_entries == 0){
#####2006.11.14�s���X�ݭn��oup_put	start		
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] = $pages;
		$op['now_pp'] = $srh->now_pp;
        $op['lastpage']=$pages[$pagesize-1];
#####2006.11.14�s���X�ݭn��oup_put	end
}

		if ($op['start_no'] == 0) {	$op['PREV_page'] = ""; } else {	$op['PREV_page'] = "1"; }
		if ($op['next_no'] == 0) {	$op['NEXT_page'] = ""; } else {	$op['NEXT_page'] = "1"; }

		return $op;
	} // end func
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->ord_search($mode=0, $where_str='', $limit_entries=0)	�j�M �q �� ���	 
#			mode = 2 :  �Ʋ��T�{�᪺�q��j�M ( status >= 7 )
#			mode = 3 :  �w�����X�᪺�q��j�M ( status > 7 )
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function ord_search($mode=0, $where_str='', $limit_entries=0) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv

		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT * FROM s_order, pdtion ";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("s_order.id DESC");
		$srh->row_per_page = 20;


	if($limit_entries){    // �����w�̤j�q��~~~ 2005/11/28 �[�J
			$srh->q_limit = "LIMIT ".$limit_entries." ";
	}else{
		if ($argv['PHP_sr_startno']) {
			$srh->add_limit_condition($argv['PHP_sr_startno']);
		} 
	}
if ($mode==1 || $mode==2 || $mode==3){
//		$srh->row_per_page = 10;

//		if ($str = $argv['PHP_dept_code'] )  { 
//			$srh->add_where_condition("s_order.dept = '$str'", "PHP_dept_code",$str,"�����O = [ $str ]. ");
//		}

		if ($str = strtoupper($argv['PHP_ref']) )  { 
			$srh->add_where_condition("s_order.ref LIKE '%$str%'", "PHP_ref",$str,"�j�M �Ȥ�Ѧҽs���t��: [ $str ]���e "); 
			}
		
		if ($str = $argv['PHP_cust'] )  { 
			$srh->add_where_condition("s_order.cust = '$str'", "PHP_cust",$str,"�j�M �Ȥ� = [ $str ]. "); 
			}
		if ($str = $argv['PHP_order_num'] )  { 
			$srh->add_where_condition("s_order.order_num LIKE '%$str%'", "PHP_order_num",$str,"�j�M �q��s���t��: [ $str ]���e "); 
			}
		if ($str = $argv['PHP_factory'] )  { 
			$srh->add_where_condition("s_order.factory = '$str'", "PHP_factory",$str,"�j�M �ӻs�u�t = [ $str ]. "); 
			}
}		
if ($mode==1){   // ��n�j�M�� �q��O �q��T�{�᪺��
		$srh->add_where_condition("s_order.status >= 4", "","","");   
}		
if ($mode==2){   // ��n�j�M�� �q��O �Ʋ��T�{�᪺��
		$srh->add_where_condition("s_order.status >= 7", "","","");   
}		
if ($mode==3){   // ��n�j�M�� �q��O �Ʋ��T�{�᪺��
		$srh->add_where_condition("s_order.status > 7", "","","");   
}		
		$srh->add_where_condition("s_order.order_num = pdtion.order_num", "",$str,"");   // ���p����M ���M�n�[

		$result= $srh->send_query2($limit_entries);   // 2005/11/24 �[�J $limit_entries
//		$result= $srh->send_query2();
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
			if (!$result){   // ��d�M�L��Ʈ�
				$op['record_NONE'] = 1;
			}
		$op['sorder'] = $result;  // ��ƿ� �ߤJ $op
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['cgistr_post'] = $srh->get_cgi_str(1);
		$op['prev_no'] = $srh->prev_no;
		$op['next_no'] = $srh->next_no;
		$op['max_no'] = $srh->max_no;
		$op['last_no'] = $srh->last_no;
		$op['start_no'] = $srh->start_no;
		$op['per_page'] = $srh->row_per_page;

//echo "<br>[start_no]===>".$op['start_no'];
//echo "<br>[next_no]===>".$op['next_no'];
//echo "<br>[last_no]===>".$op['last_no'];

		if ($op['start_no'] == 0) {	$op['PREV_page'] = ""; } else {	$op['PREV_page'] = "1"; }
		if ($op['next_no'] == 0) {	$op['NEXT_page'] = ""; } else {	$op['NEXT_page'] = "1"; }

		return $op;
	} // end func
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->pdt_search($parm, $limit_entries=500)	�j�M �q�� �Ͳ����	 
#			
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function pdt_search($parm, $limit_entries=500) {
		$sql = $this->sql;
		$argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv

		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

	 $where_str = "WHERE ";
		if($str = $parm['ord_num']){
			$srh->add_where_condition("s_order.order_num LIKE '%$str%'", "PHP_order_num",$str,"order number contain: [ $str ] "); 
		}
		if ($str = $parm['cust'] )  { 
			$srh->add_where_condition("s_order.cust = '$str'", "PHP_cust",$str,"customer = [ $str ]. "); 
		}
		if(!$parm['ref']){
			$srh->add_where_condition("s_order.ref LIKE '%$str%'", "PHP_ref",$str,"ref contain: [ $str ] "); 
		}
		if ($str = $parm['fty'] )  { 
			$srh->add_where_condition("s_order.factory = '$str'", "PHP_factory",$str,"factory = [ $str ]. "); 
		}

	  $fields = "s_order.order_num,s_order.cust,s_order.style,s_order.qty,s_order.unit,pdtion.qty_done,pdtion.qty_shp,s_order.su,s_order.ie_time1,s_order.ie1,s_order.etd,pdtion.etf,s_order.etp,pdtion.ets,pdtion.start,pdtion.finish,pdtion.factory,s_order.opendate,s_order.apv_date,s_order.smpl_apv,s_order.ptn_upload,pdtion.mat_shp,pdtion.m_acc_shp,s_order.dept,s_order.creator,s_order.style_num,s_order.smpl_ord,s_order.patt_num,s_order.quota,s_order.revise,s_order.status";

		$q_header = "SELECT ".$fields." FROM s_order, pdtion ";

		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("s_order.id DESC");
//		$srh->row_per_page = 20;


	if($limit_entries){    // �����w�̤j�q��~~~ 2005/11/28 �[�J
			$srh->q_limit = "LIMIT ".$limit_entries." ";
	}else{
		if ($argv['PHP_sr_startno']) {
			$srh->add_limit_condition($argv['PHP_sr_startno']);
		} 
	}


		$srh->add_where_condition("s_order.order_num = pdtion.order_num", "",$str,"");   // ���p����M ���M�n�[

		$result= $srh->send_query2($limit_entries);   // 2005/11/24 �[�J $limit_entries
//		$result= $srh->send_query2();
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
			if (!$result){   // ��d�M�L��Ʈ�
				$op['record_NONE'] = 1;
			}
		$op['sorder'] = $result;  // ��ƿ� �ߤJ $op
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['cgistr_post'] = $srh->get_cgi_str(1);
		$op['prev_no'] = $srh->prev_no;
		$op['next_no'] = $srh->next_no;
		$op['max_no'] = $srh->max_no;
		$op['last_no'] = $srh->last_no;
		$op['start_no'] = $srh->start_no;
		$op['per_page'] = $srh->row_per_page;

//echo "<br>[start_no]===>".$op['start_no'];
//echo "<br>[next_no]===>".$op['next_no'];
//echo "<br>[last_no]===>".$op['last_no'];

		if ($op['start_no'] == 0) {	$op['PREV_page'] = ""; } else {	$op['PREV_page'] = "1"; }
		if ($op['next_no'] == 0) {	$op['NEXT_page'] = ""; } else {	$op['NEXT_page'] = "1"; }

		return $op;
	} // end func
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->apved_search($mode=0, $where_str='', $limit_entries=0)	�j�M �q �� ���	 
#			mode = 2 :  �T�{�᪺�q��j�M ( status >= 4 )
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function apved_search($mode=0, $where_str='', $limit_entries=0) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv

		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT * FROM s_order, pdtion ";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("s_order.id DESC");
		$srh->row_per_page = 20;

	if($limit_entries){    // �����w�̤j�q��~~~ 2005/11/28 �[�J
			$srh->q_limit = "LIMIT ".$limit_entries." ";
	}else{
		if ($argv['PHP_sr_startno']) {
			$srh->add_limit_condition($argv['PHP_sr_startno']);
		} 
	}
if ($mode==1 || $mode==2){
//		$srh->row_per_page = 10;

//		if ($str = $argv['PHP_dept_code'] )  { 
//			$srh->add_where_condition("s_order.dept = '$str'", "PHP_dept_code",$str,"�����O = [ $str ]. ");
//		}

		if ($str = strtoupper($argv['PHP_ref']) )  { 
			$srh->add_where_condition("s_order.ref LIKE '%$str%'", "PHP_ref",$str,"�j�M �Ȥ�Ѧҽs���t��: [ $str ]���e "); 
			}
		
		if ($str = $argv['PHP_cust'] )  { 
			$srh->add_where_condition("s_order.cust = '$str'", "PHP_cust",$str,"�j�M �Ȥ� = [ $str ]. "); 
			}
		if ($str = $argv['PHP_order_num'] )  { 
			$srh->add_where_condition("s_order.order_num LIKE '%$str%'", "PHP_order_num",$str,"�j�M �q��s���t��: [ $str ]���e "); 
			}
		if ($str = $argv['PHP_factory'] )  { 
			$srh->add_where_condition("s_order.factory = '$str'", "PHP_factory",$str,"�j�M �ӻs�u�t = [ $str ]. "); 
			}
}		
if ($mode==2){   // ��n�j�M�� �q��O �Ʋ��T�{�᪺��
		$srh->add_where_condition("s_order.status >= 4", "","","");   
}		
		$srh->add_where_condition("s_order.order_num = pdtion.order_num", "",$str,"");   // ���p����M ���M�n�[

		$result= $srh->send_query2();
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
			if (!$result){   // ��d�M�L��Ʈ�
				$op['record_NONE'] = 1;
			}
		$op['sorder'] = $result;  // ��ƿ� �ߤJ $op
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['cgistr_post'] = $srh->get_cgi_str(1);
		$op['prev_no'] = $srh->prev_no;
		$op['next_no'] = $srh->next_no;
		$op['max_no'] = $srh->max_no;
		$op['last_no'] = $srh->last_no;
		$op['start_no'] = $srh->start_no;
		$op['per_page'] = $srh->row_per_page;

//echo "<br>[start_no]===>".$op['start_no'];
//echo "<br>[next_no]===>".$op['next_no'];
//echo "<br>[last_no]===>".$op['last_no'];

		if ($op['start_no'] == 0) {	$op['PREV_page'] = ""; } else {	$op['PREV_page'] = "1"; }
		if ($op['next_no'] == 0) {	$op['NEXT_page'] = ""; } else {	$op['NEXT_page'] = "1"; }

		return $op;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->uncfm_search($mode=0,$limit_entries=0)	�j�M �ݽT�{���q�� ���	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function uncfm_search($mode=0,$limit_entries=0) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv

		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

	//2006/05/12 adding �P�_�O�_�� J0 �� K0
	$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
	if($user_dept == "J0"){
//	echo "<br>user dept....===>".$user_dept;
		$q_header = "SELECT * FROM s_order WHERE status=2 AND dept LIKE '%J%'  ";
	}elseif($user_dept == "K0"){
//	echo "<br>user dept....===>".$user_dept;
		$q_header = "SELECT * FROM s_order WHERE status=2 AND dept LIKE '%K%' ";
	}else{
		$q_header = "SELECT * FROM s_order WHERE status=2 ";
	}
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("id DESC");
		$srh->row_per_page = 20;

		if($limit_entries){    // �����w�̤j�q��~~~ 2005/11/28 �[�J
				$srh->q_limit = "LIMIT ".$limit_entries." ";
		}else{
			if ($argv['PHP_sr_startno']) {
				$srh->add_limit_condition($argv['PHP_sr_startno']);
			} 
		}
		
		$result= $srh->send_query2($limit_entries);
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
			if (!$result){   // ��d�M�L��Ʈ�
				$op['record_NONE'] = 1;
			}
		$op['sorder'] = $result;  // ��ƿ� �ߤJ $op
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

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->unapv_search($mode=0,$limit_entries=0)	�j�M �� �֥i ���q�� ���	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function unapv_search($mode=0,$limit_entries=0) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv

		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}

		$q_header = "SELECT * FROM s_order WHERE status=3 ";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("id DESC");
		$srh->row_per_page = 20;


		if($limit_entries){    // �����w�̤j�q��~~~ 2005/11/28 �[�J
				$srh->q_limit = "LIMIT ".$limit_entries." ";
		}else{
			if ($argv['PHP_sr_startno']) {
				$srh->add_limit_condition($argv['PHP_sr_startno']);
			} 
		}

		$result= $srh->send_query2($limit_entries);
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
			if (!$result){   // ��d�M�L��Ʈ�
				$op['record_NONE'] = 1;
			}
		$op['sorder'] = $result;  // ��ƿ� �ߤJ $op
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
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->schedule_search($mode=0)	�j�M �q �� ���	 [ �֥i��q�� ]
#	NOTE: ���ݳ]�w�i�J�� user ���� [ ���P�u�t ===== ==============
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function schedule_search($mode=0) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv

		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}
			 // ���p����M ==tabel s_order, pdtion
		$q_header = "SELECT s_order.id, s_order.order_num, s_order.cust, s_order.style_num, s_order.etd, s_order.etp, s_order.qty, s_order.unit, s_order.status, s_order.smpl_apv, pdtion.ets, s_order.factory, s_order.ref, pdtion.mat_etd, pdtion.m_acc_etd FROM s_order, pdtion ";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("id DESC");
		$srh->row_per_page = 20;

		if ($argv['PHP_sr_startno']) {
			$srh->add_limit_condition($argv['PHP_sr_startno']);
		} 
if ($mode==1){
		if (isset($argv['PHP_dept_code']) && $str = $argv['PHP_dept_code'] )  { 
			$srh->add_where_condition("s_order.dept = '$str'", "PHP_dept_code",$str,"�����O = [ $str ]. ");
		}

		if ($str = strtoupper($argv['PHP_ref']) )  { 
			$srh->add_where_condition("s_order.ref LIKE '%$str%'", "PHP_ref",$str,"�j�M �Ȥ�Ѧҽs���t��: [ $str ]���e "); 
			}
		
		if ($str = $argv['PHP_cust'] )  { 
			$srh->add_where_condition("s_order.cust = '$str'", "PHP_cust",$str,"�j�M �Ȥ� = [ $str ]. "); 
			}
		if ($str = $argv['PHP_order_num'] )  { 
			$srh->add_where_condition("s_order.order_num LIKE '%$str%'", "PHP_order_num",$str,"�j�M �q��s���t��: [ $str ]���e "); 
			}
		if ($str = $argv['PHP_factory'] )  { 
			$srh->add_where_condition("s_order.factory = '$str'", "PHP_factory",$str,"�j�M �ӻs�u�t = [ $str ]. "); 
			}
		$srh->add_where_condition("status >= 4", "PHP_status",$str,"");   // ******* ���M�n�[ �C��Ҭ� �֥i��
		$srh->add_where_condition("s_order.order_num = pdtion.order_num", "",$str,"");   // ���p����M ���M�n�[
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
		$op['sorder'] = $result;  // ��ƿ� �ߤJ $op
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['cgistr_post'] = $srh->get_cgi_str(1);
		$op['prev_no'] = $srh->prev_no;
		$op['next_no'] = $srh->next_no;
		$op['max_no'] = $srh->max_no;
		$op['last_no'] = $srh->last_no;
		$op['start_no'] = $srh->start_no;
		$op['per_page'] = $srh->row_per_page;

//echo "<br>[start_no]===>".$op['start_no'];
//echo "<br>[next_no]===>".$op['next_no'];
//echo "<br>[last_no]===>".$op['last_no'];

		if ($op['start_no'] == 0) {	$op['PREV_page'] = ""; } else {	$op['PREV_page'] = "1"; }
		if ($op['next_no'] == 0) {	$op['NEXT_page'] = ""; } else {	$op['NEXT_page'] = "1"; }

		return $op;
	} // end func
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->schedule_uncfm_search($fty_id=0)	�j�M �|���T�{ �Ͳ��Ƶ{���q�� ���	 
#	NOTE: ���ݳ]�w�i�J�� user ���� [ ���P�u�t ===== ==============
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function schedule_uncfm_search($fty_id=0) {   // ��  status = 6 [schedule �ݽT�{]

		$sql = $this->sql;
		$argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv

		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}
			 // ���p����M ==tabel s_order, pdtion
		$q_header = "SELECT s_order.id, s_order.order_num, s_order.cust,s_order.style_num, s_order.etd, s_order.etp, s_order.qty, s_order.unit, pdtion.ets, s_order.factory, pdtion.mat_etd, pdtion.m_acc_etd FROM s_order, pdtion ";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("id DESC");
		$srh->row_per_page = 20;

		if ($argv['PHP_sr_startno']) {
			$srh->add_limit_condition($argv['PHP_sr_startno']);
		} 

	if ($fty_id){
			$srh->add_where_condition("s_order.factory = '$fty_id'"); 
	}

		$srh->add_where_condition("status = 6 ");   // ******* ���M�n�[ �C��Ҭ� �ݮ֥ishcedule
		$srh->add_where_condition("s_order.order_num = pdtion.order_num ");   // ���p����M ���M�n�[
		
		$result= $srh->send_query2();
		if (!is_array($result)) {
			$this->msg->merge($srh->msg);
			return false;		    
		}
		$this->msg->merge($srh->msg);
			if (!$result){   // ��d�M�L��Ʈ�
				$op['record_NONE'] = 1;
			}
		$op['sorder'] = $result;  // ��ƿ� �ߤJ $op
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['cgistr_post'] = $srh->get_cgi_str(1);
		$op['prev_no'] = $srh->prev_no;
		$op['next_no'] = $srh->next_no;
		$op['max_no'] = $srh->max_no;
		$op['last_no'] = $srh->last_no;
		$op['start_no'] = $srh->start_no;
		$op['per_page'] = $srh->row_per_page;

//echo "<br>[start_no]===>".$op['start_no'];
//echo "<br>[next_no]===>".$op['next_no'];
//echo "<br>[last_no]===>".$op['last_no'];

		if ($op['start_no'] == 0) {	$op['PREV_page'] = ""; } else {	$op['PREV_page'] = "1"; }
		if ($op['next_no'] == 0) {	$op['NEXT_page'] = ""; } else {	$op['NEXT_page'] = "1"; }

		return $op;
	} // end func
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->mat_schedule_search($mode=0)	�j�M �q���� [ �D�ƨ�Ʀ�{ ]	 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function mat_schedule_search($mode=0) {

		$sql = $this->sql;
		$argv = $GLOBALS;   //�N�Ҧ��� globals ����J$argv

		$srh = new SEARCH();
		$cgi = array();
		if (!$srh->set_sql($sql)) {
			$this->msg->merge($srh->msg);
			return false;
		}
//		$q_header = "SELECT * FROM s_order ";
			 // ���p����M ==tabel s_order, pdtion
		$q_header = "SELECT s_order.id,s_order.order_num,s_order.cust,s_order.etd,s_order.etp,s_order.qty,s_order.unit,s_order.status, s_order.style,s_order.factory,pdtion.mat_etd,pdtion.m_acc_etd,pdtion.mat_shp FROM s_order, pdtion ";
		if (!$srh->add_q_header($q_header)) {
			$this->msg->merge($srh->msg);
			return false;
		}
		$srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
		$srh->add_sort_condition("id DESC");
		$srh->row_per_page = 20;

		if ($argv['PHP_sr_startno']) {
			$srh->add_limit_condition($argv['PHP_sr_startno']);
		} 
if ($mode==1){
//		if ($str = $argv['PHP_dept_code'] )  { 
//			$srh->add_where_condition("s_order.dept = '$str'", "PHP_dept_code",$str,"�����O = [ $str ]. ");
//		}

		if ($str = strtoupper($argv['PHP_ref']) )  { 
			$srh->add_where_condition("s_order.ref LIKE '%$str%'", "PHP_ref",$str,"�j�M �Ȥ�Ѧҽs���t��: [ $str ]���e "); 
			}
		
		if ($str = $argv['PHP_cust'] )  { 
			$srh->add_where_condition("s_order.cust = '$str'", "PHP_cust",$str,"�j�M �Ȥ� = [ $str ]. "); 
			}
		if ($str = $argv['PHP_order_num'] )  { 
			$srh->add_where_condition("s_order.order_num LIKE '%$str%'", "PHP_order_num",$str,"�j�M �q��s���t��: [ $str ]���e "); 
			}
		if ($str = $argv['PHP_factory'] )  { 
			$srh->add_where_condition("s_order.factory = '$str'", "PHP_factory",$str,"�j�M �ӻs�u�t = [ $str ]. "); 
			}
		$srh->add_where_condition("status >= 4", "PHP_status",$str,"");   // ******* ���M�n�[ �i�Ʋ�������
		$srh->add_where_condition("s_order.order_num = pdtion.order_num", "",$str,"");   // ���p����M ���M�n�[
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
		$op['sorder'] = $result;  // ��ƿ� �ߤJ $op
		$op['cgistr_get'] = $srh->get_cgi_str(0);
		$op['cgistr_post'] = $srh->get_cgi_str(1);
		$op['prev_no'] = $srh->prev_no;
		$op['next_no'] = $srh->next_no;
		$op['max_no'] = $srh->max_no;
		$op['last_no'] = $srh->last_no;
		$op['start_no'] = $srh->start_no;
		$op['per_page'] = $srh->row_per_page;

//echo "<br>[start_no]===>".$op['start_no'];
//echo "<br>[next_no]===>".$op['next_no'];
//echo "<br>[last_no]===>".$op['last_no'];

		if ($op['start_no'] == 0) {	$op['PREV_page'] = ""; } else {	$op['PREV_page'] = "1"; }
		if ($op['next_no'] == 0) {	$op['NEXT_page'] = ""; } else {	$op['NEXT_page'] = "1"; }

		return $op;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $order_num=0)	��X���w�q��O����� RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get($id=0, $order_num=0) {

		$sql = $this->sql;

		if ($id)	{
			$q_str = "SELECT * FROM s_order WHERE id='$id' ";
		} elseif ($order_num) {
			$q_str = "SELECT * FROM s_order WHERE order_num='$order_num' ";
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

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->mat_schedule_get($id=0, $wi_num=0)	��X���w�O������� RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function mat_schedule_get($id=0, $order_num=0) {

		$sql = $this->sql;


		// ���p����Ʈw�d�M ~~~~~~
	if ($id)	{
		$q_str = "SELECT * FROM s_order,pdtion WHERE s_order.id='$id' AND s_order.order_num=pdtion.order_num ";
	} elseif ($order_num) {
		$q_str = "SELECT * FROM s_order,pdtion WHERE order_num='$order_num' AND s_order.order_num=pdtion.order_num ";
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

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_ord_output($ord_num)	�w���q�q�渹�X ���X���椧�O�� RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_ord_output($ord_num) {

		$sql = $this->sql;
	
		$q_str = "SELECT s_order.opendate,s_order.cfm_date,s_order.creator,s_order.apv_date,s_order.schd_date,s_order.cust, s_order.qty,s_order.smpl_apv,s_order.factory,s_order.su,s_order.etd,s_order.etp,s_order.status,pdtion.ets,pdtion.etf,pdtion.mat_shp,pdtion.acc_shp,pdtion.m_acc_shp,pdtion.qty_done,pdtion.start,pdtion.finish,pdtion.shp_date FROM s_order,pdtion WHERE s_order.order_num='$ord_num' AND s_order.order_num=pdtion.order_num ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s����Ʈw!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			$this->msg->add("Error ! �L�k���O��!");
			return false;    
		}
		return $row;

	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_fields_4_del_pdt($ord_num)	���q�q�渹�X ���X�n�R���Y�ӥͲ��O�����q�椺�e RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_fields_4_del_pdt($ord_num) {

		$sql = $this->sql;
	
		$q_str = "SELECT s_order.id, s_order.status, pdtion.id AS p_id, pdtion.out_su FROM s_order,pdtion WHERE s_order.order_num='$ord_num' AND s_order.order_num=pdtion.order_num ";


		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot access databse, please contact the system Administrator !");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {
			$this->msg->add("Error ! �L�k���O��!");
			return false;    
		}
		return $row;

	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->schedule_get($id=0, $order_num=0)	��X���w�O������� RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function schedule_get($id=0, $order_num=0) {

		$sql = $this->sql;

		// ���p����Ʈw�d�M ~~~~~~
	if ($id)	{
		$q_str = "SELECT * FROM s_order,pdtion WHERE s_order.id='$id' AND s_order.order_num=pdtion.order_num ";
	} elseif ($order_num) {
		$q_str = "SELECT * FROM s_order,pdtion WHERE s_order.order_num='$order_num' AND s_order.order_num=pdtion.order_num ";
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

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_pdtion($order_num, $factory)	��X���w�� pdtion �O�� RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_pdtion($order_num, $factory) {

		$sql = $this->sql;

		$q_str = "SELECT * FROM pdtion WHERE order_num='$order_num' AND factory='$factory' ";

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

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->revise_pdtion($parm, $id='', $ord_num='')	��s pdtion�����e [ order revise �ɪ���s ]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function revise_pdtion($parm, $id='', $ord_num='') {

		$sql = $this->sql;

		#####   ��s��Ʈw���e
/*		//20060411��אּ �u�t���� revise  �B�ץ� �ѤF�N ftp_su�M��...�u�t�w�Ʋ��᪺revise
		if ($id){
			$q_str = "UPDATE pdtion SET factory='"	.$parm['factory'].
								"', etp_su='"	.$parm['etp_su'].
								"', ets='NULL', etf ='NULL'".
								"  WHERE id='"		.$id."'";
		}elseif($ord_num){
			$q_str = "UPDATE pdtion SET factory='"	.$parm['factory'].
								"', etp_su='"	.$parm['etp_su'].
								"', ets='NULL', etf ='NULL'".
								"  WHERE order_num='"		.$ord_num."'";
		}
*/		//20060411 �A���---- ���A�� ETS,ETF �]��revise�ɤw�M�L�F�ССС�

		if ($id){
			$q_str = "UPDATE pdtion SET etp_su='"	.$parm['etp_su'].
								"'  WHERE id='"		.$id."'";
		}elseif($ord_num){
			$q_str = "UPDATE pdtion SET etp_su='"	.$parm['etp_su'].
								"'  WHERE order_num='"		.$ord_num."'";
		}

//echo "q_str====>".$q_str;
//exit;

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  �L�k��s��Ʈw.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if ($id) {	$pdt = $id; } elseif($ord_num) { $pdt = $ord_num;}
		
		return $pdt;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->revise_apv_ord($parm, $ord_num='')	��s �w�֥i�᪺�q�� 
#			�� status=1 , etp_su, ftp_su, ets, etf �k�s
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function revise_apv_ord($parm, $ord_num='') {

		$sql = $this->sql;

		#####   ��s��Ʈw���e

		$q_str = "UPDATE s_order,pdtion SET pdtion.etp_su='".$parm['etp_su']."',pdtion.fty_su='".$parm['fty_su']."',s_order.opendate='".$parm['today']."',s_order.creator='".$parm['reviser']."', pdtion.ets='NULL', pdtion.etf ='NULL', s_order.status='1'   WHERE s_order.order_num='".$ord_num."' AND pdtion.order_num ='".$ord_num."'";

//echo "q_str====>".$q_str;
//exit;

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  �L�k��s��Ʈw.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		return $ord_num;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit($parm,$mode=0)		��s �q�� �O�� 
#			mode=0 : EDIT    mode=1 : REVISE
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function edit($parm, $mode=0) {

		$sql = $this->sql;
		
		if ($mode == 0){
		#####   ��s��Ʈw���e
			$q_str = "UPDATE s_order SET ".
							" ref='"		.$parm['ref'].
							"',	factory='"		.$parm['factory'].
							"',	style='"		.$parm['style'].
							"',	qty='"			.$parm['qty'].
							"',	su='"			.$parm['su'].
//							"', su_ratio='"		.$parm['su_ratio'].
							"', unit='"			.$parm['unit'].
//							"',	style_num='"	.$parm['style_num'].
//							"',	patt_num='"		.$parm['patt_num'].
							"', uprice='"		.$parm['uprice'].
							"',	quota='"		.$parm['quota'].
							"',	qty='"			.$parm['qty'].
							"', mat_u_cost='"	.$parm['mat_u_cost'].
							"',	mat_useage='"	.$parm['mat_useage'].
							"',	acc_u_cost='"	.$parm['acc_u_cost'].
							"',	quota_fee='"	.$parm['quota_fee'].
							"',	comm_fee='"		.$parm['comm_fee'].
							"', cm='"			.$parm['cm'].
							"',	smpl_fee='"		.$parm['smpl_fee'].
							"',	emb='"			.$parm['emb'].
							"',	wash='"			.$parm['wash'].
							"',	oth='"			.$parm['oth'].
							"',	oth_treat='"	.$parm['oth_treat'].

							"',	etd='"			.$parm['etdyear']."-".$parm['etdmonth']."-".$parm['etdday'].
							"', etp='"			.$parm['etpyear']."-".$parm['etpmonth']."-".$parm['etpday'].
							"', gmr='"			.$parm['gmr'].
							"', last_updator='"	.$parm['last_updator'].
							"', last_update=		NOW()".
							"  WHERE id='"		.$parm['id']."'";

		} elseif($mode ==1){      // --- order revise -----
			$q_str = "UPDATE s_order SET ".
							" ref='"		.$parm['ref'].
//							"',	factory='"		.$parm['factory'].
							"',	style='"		.$parm['style'].
							"',	qty='"			.$parm['qty'].
							"',	su='"			.$parm['su'].
//							"', su_ratio='"		.$parm['su_ratio'].
							"', unit='"			.$parm['unit'].
//							"',	style_num='"	.$parm['style_num'].
//							"',	patt_num='"		.$parm['patt_num'].
							"', uprice='"		.$parm['uprice'].
							"',	quota='"		.$parm['quota'].
							"',	qty='"			.$parm['qty'].
							"', mat_u_cost='"	.$parm['mat_u_cost'].
							"',	mat_useage='"	.$parm['mat_useage'].
							"',	acc_u_cost='"	.$parm['acc_u_cost'].
							"',	quota_fee='"	.$parm['quota_fee'].
							"',	comm_fee='"		.$parm['comm_fee'].
							"', cm='"			.$parm['cm'].
							"',	smpl_fee='"		.$parm['smpl_fee'].
							"',	emb='"			.$parm['emb'].
							"',	wash='"			.$parm['wash'].
							"',	oth='"			.$parm['oth'].
							"',	oth_treat='"	.$parm['oth_treat'].

							"',	etd='"			.$parm['etdyear']."-".$parm['etdmonth']."-".$parm['etdday'].
							"', etp='"			.$parm['etpyear']."-".$parm['etpmonth']."-".$parm['etpday'].
							"', gmr='"			.$parm['gmr'].
							"', revise="		.$parm['revise'].
							", status="			.$parm['status'].
							", cfmer='"			.''.
							"', cfm_date='"		.'0000-00-00'.
							"', apver='"		.''.
							"', apv_date='"		.'0000-00-00'.
							"', last_updator='"	.$parm['last_updator'].
							"', last_update=		NOW()".
							"  WHERE id='"		.$parm['id']."'";
		}

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  �L�k��s��Ʈw.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$pdt_id = $parm['id'];
		$pic_id = $parm['order_num'];

	//  �Ϫ��B�z ===============
	// �D�� ������ӹϤW��[�j��]��[�p��]
			//���ɥؿ�(�H pdt_id �ӳ]�����ɦW
			$upFile = new uploadIMG();
			$style_dir	= $GLOBALS['config']['root_dir']."/picture/";  
//			$no_img		= $GLOBALS['config']['root_dir']."/images/graydot.gif";

		if($parm['pic_upload'])  {   // 2005/01/04 �令��javascript���X�ʤ@�� hidden�� �e�J��x

						# �W�ǹϬ۳B�z
				//2004/05/03 ���ˬd�O�_�s�b �p�s�b�N������
			if(file_exists($style_dir.$parm['order_num'].".jpg")){
				unlink($style_dir.$parm['order_num'].".jpg") or die("�L�k�R���¹���:".$pic_id.".jpg");  // �R������
			}
				//�W�Ǥj�� 600X600
				$upFile->setSaveTo($style_dir,$parm['order_num'].".jpg");
				$up_result = $upFile->upload($parm['pic'], 600, 600);
		}

		return $pdt_id;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->send_cfm($parm)		 �q��e�X �ݽT�{  
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function send_cfm($parm) {

		$sql = $this->sql;

		#####   ��s��Ʈw���e
		$q_str = "UPDATE s_order SET status=		2".
							", last_updator='"	.$parm['last_updator'].
							"', last_update=		NOW()".
							"  WHERE id='"		.$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  �L�k��s��Ʈw.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$pdt_id = $parm['id'];
		
		return $pdt_id;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->do_cfm($parm)		�q�� �T�{ok  �e�X�� �֥i  
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function do_cfm($parm) {

		$sql = $this->sql;

		#####   ��s��Ʈw���e
		$q_str = "UPDATE s_order SET status=		3".
							", cfmer='"			.$parm['cfmer'].
							"', cfm_date=		NOW()".
							"  WHERE id='"		.$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  �L�k��s��Ʈw.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$pdt_id = $parm['id'];

		return $pdt_id;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->reject_cfm($parm)		�q�� REJECT �T�{  
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function reject_cfm($parm) {

		$sql = $this->sql;

		#####   ��s��Ʈw���e
		$q_str = "UPDATE s_order SET status=		5".
							", cfmer='"			.$parm['cfmer'].
							"', cfm_date=		NOW()".
							"  WHERE id='"		.$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  �L�k��s��Ʈw.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$pdt_id = $parm['id'];
		
		return $pdt_id;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->do_apv($parm)		�q�� �֥iok  ############>>>>>> �w�w�Ʋ�  
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function do_apv($parm) {

		$sql = $this->sql;

		#####   ��s��Ʈw���e
		$q_str = "UPDATE s_order SET status=		4".
							", apver='"			.$parm['apver'].
							"', apv_date=		NOW()".
							"  WHERE id='"		.$parm['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  �L�k��s��Ʈw.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$pdt_id = $parm['id'];

		return $pdt_id;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->reject_apv($parm)		�q�� REJECT �֥i  
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function reject_apv($parm) {

		$sql = $this->sql;

		#####   ��s��Ʈw���e
		$q_str = "UPDATE s_order SET status=		5".
							", apver='"			.$parm['apver'].
							"', apv_date=		NOW()".
							"  WHERE id='"		.$parm['id']."'";


		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  �L�k��s��Ʈw.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$pdt_id = $parm['id'];
		
		return $pdt_id;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->pd_out_update($parm)		�u�t�����ƶq����s pdtion 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function pd_out_update($parm) {

		$sql = $this->sql;
		
		#####   ��s��Ʈw���e
//		$q_str = "UPDATE pdtion SET qty_done="		.$parm['qty_done'].
		$q_str = "UPDATE pdtion SET qty_done= qty_done+".$parm['qty'].
								", qty_update='"	.$parm['k_date'].
								"', out_su='"		.$parm['out_su'].
								"'  WHERE id='"		.$parm['pd_id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot update pdtion table.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$pdt_id = $parm['pd_id'];
		
		return $pdt_id;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_smpl_apv($parm)		 ��s �˥��T�{��� --- by order_num  
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_smpl_apv($parm) {

		$sql = $this->sql;

		#####   ��s��Ʈw���e
		if($parm['mode'] ==1){			//�[�Jmode �Ӥ��O�⦸��smpl apv ----
			$q_str = "UPDATE s_order SET smpl_apv='"	.$parm['smpl_apv'].
								"', smpl_apv_type='"	.$parm['smpl_apv_type'].
								"'  WHERE order_num ='"	.$parm['order_num']."'";
		}elseif($parm['mode'] ==2){
			$q_str = "UPDATE s_order SET smpl_apv2='"	.$parm['smpl_apv'].
								"', smpl_apv_type2='"	.$parm['smpl_apv_type'].
								"'  WHERE order_num ='"	.$parm['order_num']."'";
		}elseif($parm['mode'] ==3){
			$q_str = "UPDATE s_order SET smpl_apv3='"	.$parm['smpl_apv'].
								"', smpl_apv_type3='"	.$parm['smpl_apv_type'].
								"'  WHERE order_num ='"	.$parm['order_num']."'";
		}
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  �L�k��s��Ʈw.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		$ord_num = $parm['order_num'];
		
		return $ord_num;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ************************ �|��s capacity ������� ******************
#	->distri_month_su($T_su, $s_date, $f_date, $fty, $cat, $mode=0)
#		Detail description	: �N �ƶq ����su �A���t��Ͳ������ 
#							: �g�J capacity table �� $field �� [�p�S��� error ]
#							: �Ǧ^ �}�C ( 200505=>su , 200506=>su, ......
#		$fty, $cat ���F�n�b capacity table��s [ $cat �����O capacity���� c_type ���W ]
#
#			$mode === 0 �� �����`���[�J <>   $mode = 1 ��  ���[�J�@�ӭt�� �Y��h 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function distri_month_su($T_su, $s_date, $f_date, $fty, $cat, $mode=0) {


		if($mode==1) { $factor = -1; } else { $factor = 1; }  // �[�J ���t�� ���ݭn-- ��Υ[ capaci
 		$div = array();

		$distribute ='';   // ����csv �ܼ�
   
		list($s_year,$s_mon,$s_day) = split("-",$s_date);  // �}�l��
		list($f_year,$f_mon,$f_day) = split("-",$f_date);  // ������
		$days =	countDays($s_date,$f_date);
//		$T_su = $su;		// �` su ��
		$day_su = $T_su/$days;		// �C�鲣�X --(���p)

		// �p���`�@���X�Ӥ��?
		$y = $f_year - $s_year;
		$m = 12*$y + (12-$s_mon+1) - (12-$f_mon);

//echo "<br>total qty = ".$qty." are equal to  ".$T_su." s.u.<br>";
//echo "<br>from ".$s_date." to ".$f_date." totally distribute for ".$m." �Ӥ��; �p ".$days."�� <br>";

			$su_year = $s_year;	// �~�����p�ƾ�:: �}�l�]�q���_�Y�~
			$su_mon = $s_mon;	// ������p�ƾ�:: �}�l�]�w���_�Y��

			$divered_su =0;	// �w�g�έp��su�� �����̫�뤧�

		for ($i=0; $i<$m; $i++){
			if($su_mon >12){     // �p�ƾ��w��~����
				$su_year = $su_year+1;
				$su_mon = 1;
			}

			$mon = sprintf("%04d%02d", $su_year, $su_mon);   // ������аO

				// �p��C�몺�Ѽ� ---- �N su ���t�i�J
			if($s_mon==$f_mon){   // �p�G�}�l�M�̫�O�P�����-----
					$d = $f_day - $s_day ;
					$su = intval($day_su * $d);
			}else{
				if ($i==0){  // �Ĥ@�Ӥ�
					$d = getDaysInMonth($su_mon,$su_year)- intval($s_day);
					$su = intval($day_su * $d);
				} elseif($i==$m-1){  // �̫�@�Ӥ�
					$d = intval($f_day);
					$su = $T_su - $divered_su;
				} else{
					$d = getDaysInMonth($su_mon,$su_year);
					$su = intval($day_su * $d);
				}
			}

			$divered_su = $divered_su + $su; 

//echo "<br>   distribute-month ==> ".$mon." ==>".$d." days ==> for ".$su." su" ;
			$su_mon = $su_mon+1;
			$tmp_m = $mon;
			$div[$tmp_m] = $su;   // �m�J array 

		# #####============ �[�J capacity ->    #########################
	
			$su_m = substr($mon,4);
				
				$su = $su * $factor; 	// �[�J���t�� 2005/11/21
				
			if (!$F = $GLOBALS['capaci']->update_su($fty, $su_year, $su_m, $cat, $su)) {
				$this->msg->add("Error ! cannot update [".$cat."] field of capacity table, pls try later !");
				$this->msg->merge($sql->msg);
				return false;    
			}
			
			$distribute = $distribute.','.$mon.$su;
		}

		$distribute = substr($distribute,1);  // �h���}�Y��',' �Ÿ�

//echo "<br> distribute monthy su are ===>".$distribute;
//exit;
	
	// �Ǧ^���ѼƬ��@�� csv �p: 2005071200,200508850,

	return $distribute;

} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->creat_pdtion($parm)		�[�J �s�� pdtion �q��O�� [ �g�J�u�t �� etp_su �Ʋ���]
#								�Ǧ^ $id
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function creat_pdtion($parm) {
					
		$sql = $this->sql;

					# �[�J��Ʈw
		$q_str = "INSERT INTO pdtion (order_num,factory,etp_su) VALUES('".
							$parm['order_num']."','".
							$parm['factory']."','".
							$parm['etp_su']."')";
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s�W��ưO��.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$ord_id = $sql->insert_id();  //���X �s�� id

//		$this->msg->add("���\ �s�W PDTION �O��: [".$parm['order_num']."]�C") ;

		return $ord_id;

	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($field, $val, $id)		��s s_order��ưO���� �Y�ӳ�@���
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_field($field, $val, $id) {

		$sql = $this->sql;
		############### �ˬd��J����
		
		#####   ��s��Ʈw���e
	$q_str = "UPDATE s_order SET ".$field."='".$val."'  WHERE id=".$id." ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k��s��Ʈw���e.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_pdtion_field($field, $val, $id)		��s pdtion��ưO���� �Y�ӳ�@���
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function update_pdtion_field($field, $val, $id) {

		$sql = $this->sql;
		############### �ˬd��J����
		
		#####   ��s��Ʈw���e
	$q_str = "UPDATE pdtion SET ".$field."='".$val."'  WHERE id=".$id." ";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k��s��Ʈw���e.");
			$this->msg->merge($sql->msg);
			return false;    
		}
		
		return true;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->del($id,$mode=0)		�R��   [��ID]�R��
#							$mode=0: $id= �O����id; $mode<>0: $id=ORDER_num
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function del($id,$mode=0) {

		$sql = $this->sql;
		if (!$id) {
			$this->msg->add("Error ! �Ы��� �q�� ����ɪ� ID.");		    
			return false;
		}
		if($mode){
			$q_str = "DELETE FROM s_order WHERE order_num='$id' ";
		}else{
			$q_str = "DELETE FROM s_order WHERE id='$id' ";
		}

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! �L�k�s����Ʈw !");
			$this->msg->merge($sql->msg);
			return false;    
		}

		return true;
	} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_field_value($field,$id='',$ord_num='', $tbl='s_order')	���X �Y��  field����
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_field_value($field, $id='',$ord_num='',$tbl='s_order') {
		$sql = $this->sql;
		$row = array();

		if ($id){
	  		$q_str = "SELECT ".$field." FROM ".$tbl." WHERE id='".$id."' ";
		} elseif($ord_num){
	  		$q_str = "SELECT ".$field." FROM ".$tbl." WHERE order_num='".$ord_num."' ";
		} else {
			$this->msg->add("Error! not enough info to get data record !");
			$this->msg->merge($sql->msg);
			return false;    
		}

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error! �L�k�s����Ʈw!");
			$this->msg->merge($sql->msg);
			return false;    
		}
			
		$row = $sql->fetch($q_result);

		$field_val = $row[0];

		return $field_val;
	} // end func
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->shift($argv, $parm)	 �q�沾�� ��s s_order , pdtion ���e
#			
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function shift($argv, $parm) {

		$sql = $this->sql;
		
		#####   ��s s_order ��Ʈw���e

			$q_str = "UPDATE s_order SET ".
							" schd_er ='"		.$argv['schd_er'].
							"',	schd_date='"	.$argv['schd_date'].
							"',	factory='"		.$argv['factory'].
							"',	status=	"		.$argv['status'].
							", last_updator='"	.$argv['last_updator'].
							"', last_update=		NOW()".
							"  WHERE id='"		.$argv['id']."'";

		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error !  �L�k��s��Ʈw.");
			$this->msg->merge($sql->msg);
			return false;    
		}

		#####   ��s pdtion ��Ʈw���e

			$q_str2 = "UPDATE pdtion SET ".
							" ets ='"		.$parm['ets'].
							"',	etf='"		.$parm['etf'].
							"',	factory='"	.$parm['factory'].
							"',	fty_su=	'"	.$parm['fty_su'].
							"'  WHERE id='"	.$parm['id']."'";

		if (!$q_result2 = $sql->query($q_str2)) {
			$this->msg->add("Error !  �L�k��s��Ʈw.");
			$this->msg->merge($sql->msg);
			return false;    
		}



		return $argv['id'];
	} // end func




} // end class


?>