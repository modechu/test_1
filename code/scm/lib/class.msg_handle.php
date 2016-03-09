<?php
#####################  class msg_handle  #####################
#	 class MySQL
#					msg_handle->add($msg=0)
#					msg_handle->get($mode=0)
#					msg_handle->check($mode=0) 

##########################################################

CLASS MSG_HANDLE {
	var $display_msg_ary;
	var $error_flag;

##########################################################
#	 MSG_HANDLE()
#				初始化
##########################################################
	function MSG_HANDLE() {
		$this->display_msg_ary = array();
		$error_flag = 0;
	}			// end func

##########################################################
#	 msg_handle->add($msg=0)
#				加入msg
##########################################################
	function add($msg=0) {

		$this->display_msg_ary[] = $msg;

		return true;
	}			// end func

##########################################################
#	 msg_handle->get($mode=0)
#				取得display_msg內的資料
#							mode=0 : HTML格式(預設)
#							mode=1 : Plain TEXT
#							mode=2 : 傳回msg_array#								
##########################################################
	function get($mode=0) {

		$msg_ary= $this->display_msg_ary;
		$return_str="";

		if ($mode == 0 ) {					//  HTML格式(預設)
					if (!$msg_ary) {
						return '<table border=1 align=center><tr><td> </td></tr></table>'."\n";
					}
						$return_str .= "<table border=1>\n";
						foreach ($msg_ary AS $val){
							$return_str .= "<tr><td><font size=2>". nl2br(ereg_replace(" ", " &nbsp;", htmlspecialchars($val))). "</font></td></tr>\n";
						}
					$return_str .= "</table>\n";
					return $return_str;

		}	elseif ($mode == 1) {			//   Plain TEXT
					if (!$msg_ary) {
						return "\n";
					}
					foreach ($msg_ary AS $val){
						$return_str .= $val."\n";
					}
					return $return_str;
		}	elseif ($mode==2) {				//  傳回msg_array#		
//			$ret = array();
					 return $msg_ary;
		}			// end if($mode.....)
	
	}			//  end func

##########################################################
#	 msg_handle->check($mode=0)
#				check if error happens			
#						mode=0: have message
#						mode="error": error happens
##########################################################
		function check($mode=0) {

			foreach ($this->msg AS $key=>$val) {
				if (strstr($val,"error") || stristr($val,"Error")) {
					$this->error_flag = 1;
				}
			}

			if ($mode="error" && $this->error_flag==1) {
				return true;
			}
			
			if (count($this->display_msg_ary) > 0) {
				return true;
			}
			return false;
		}			// end func

##########################################################
#			 msg_handle->merge($msg_object)
#				merge error msg
##########################################################
		function merge($msg_object) {
		
			if (get_class($msg_object) != "msg_handle") {
				$this->add("Error : enable merge msg object。");
				$this->add("      object:". get_class($msg_object));
				return false;
			}

			foreach($msg_object->display_msg_ary AS $key=>$val) {
					$this->display_msg_ary[] = $val;
			}
			return true;
		}			// end func

}				// end class






?>