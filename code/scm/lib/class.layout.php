<?php 

########################  class layout  #####################################
#			用來layout的class，目前使用Smarty
#
#			layout->assign()
#			layout->fetch($tpl)
#			layout->display($tpl,$debug=0)
#
########################  class layout  #####################################
class  LAYOUT{

	var $smarty ;
	var $debug=0;
	
	###################################################################
	#			function LAYOUT()
	#				建新的 smarty
	###################################################################
	function LAYOUT() {

		$this->smarty = new Smarty;
										# 加入常用的函數
		$this->smarty->assign("PHP_SELF",$GLOBALS["PHP_SELF"]);
		$this->smarty->assign("NOW",date("H : i | D , M  d , Y"));
        #debug:
		// $this->smarty->debugging=1;
	} // end func
    
	###################################################################
	#			layout->assign()
	###################################################################
	function assign() {

		$numargs = func_num_args();
		$args = func_get_args();
		if ($numargs==2) {
			$this->smarty->assign($args[0], $args[1]);	    
		}	elseif ($numargs==1) {
			$this->smarty->assign($args[0]);	      
		}	else {
			return false;
		}
	    return true;
	} // end func

	###################################################################
	#			layout->fetch($tpl)
	###################################################################
	function fetch($tpl) {

		$smarty = $this->smarty;
 		$out=$smarty->fetch("$tpl");
		return $out;
	} // end func
	
	###################################################################
	#			layout->display($tpl,$debug=0)
	###################################################################
	function display($tpl,$debug=0) {

		if ($debug | $this->debug) {
			$smarty->debugging =1;
		}
		$smarty = $this->smarty;
 		$smarty->display("$tpl");
		return true;
	} // end func




} // end class








?>