<?php
class ACCORIES {
		
	var $sql;
	var $msg ;
	function init($sql) {
		$this->msg = new MSG_HANDLE();

		if (!$sql) {
                        $this->msg->add("Error !");
		    return false;
		}
		$this->sql = $sql;
		return true;
	} // end func
	
	function add($parm) {
						
			$sql = $this->sql;
	
	
			foreach($parm["acc_name"] as $key => $val)
			{
				if($val)
				{
                         //Fabric 
          $q_str = "insert into accories set ord_num ='".$parm["Number"]."',
												 acc_name='".$parm["acc_name"][$key]."',
												 spec='".$parm["spec"][$key]."',
												 unit='".$parm["unit"][$key]."',
												 u_price='".$parm["u_price"][$key]."',
												 ord_supl='".$parm["ord_supl"][$key]."',
												 yy='".$parm["yy"][$key]."'
												 ";
												 
					if (!$q_result = $sql->query($q_str)) {
          $this->msg->add("Error !");
					$this->msg->merge($sql->msg);
					return false;    
					}
				}
		}
	}


} // end class
?>
