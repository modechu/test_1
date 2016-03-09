<?php
class EXWORK {
		
	var $sql;
	var $msg ;
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->init($sql)	啟始(使用 Msg_handle() ; 先聯上 sql)
#		必需聯上 sql 才可  啟始
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function init($sql) {
		$this->msg = new MSG_HANDLE();

		if (!$sql) {
			$this->msg->add("Error ! 無法聯上資料庫.");
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
					//Fabric 主料
					$q_str = "insert into exwork set ord_num ='".$parm["Number"]."',
										ex_work='".$parm["ex_work"][$key]."',
										unit='".$parm["unit"][$key]."',
										u_price='".$parm["u_price"][$key]."',
										ord_supl='".$parm["ord_supl"][$key]."'
										";
					if (!$q_result = $sql->query($q_str)) {
					$this->msg->add("Error ! 無法新增資料記錄.");
					$this->msg->merge($sql->msg);
					return false;    
					}
				}
			}
	}
} // end class
?>