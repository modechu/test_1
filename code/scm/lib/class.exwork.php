<?php
class EXWORK {
		
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
	
	function add($parm) {
						
			$sql = $this->sql;
	
	
			foreach($parm["ex_work"] as $key => $val)
			{
				if($val)
				{
					//Fabric �D��
					$q_str = "insert into exwork set ord_num ='".$parm["Number"]."',
										ex_work='".$parm["ex_work"][$key]."',
										unit='".$parm["unit"][$key]."',
										u_price='".$parm["u_price"][$key]."',
										ord_supl='".$parm["ord_supl"][$key]."'
										";
					if (!$q_result = $sql->query($q_str)) {
					$this->msg->add("Error ! �L�k�s�W��ưO��.");
					$this->msg->merge($sql->msg);
					return false;    
					}
				}
			}
	}
} // end class
?>