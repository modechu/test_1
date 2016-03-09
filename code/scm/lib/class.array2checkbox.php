<?php
	
class array2checkbox {

	var $decoded = array();
	var $encoded;
	var $html;
	var $msg;

	#################################################################
	# Short description: 把所有可能的 ARRAY 解開
	#
	#
	#################################################################
	function decode_array($ary) {
	    
		$this->decoded = split(",",$ary);

		return $this->decoded;
	} // end func

	#################################################################
	# Short description: 把 ARRAY  pack起來
	#
	#
	#################################################################
	function encode_array($ary) {

		if (!is_array($ary)) {
			return false;		    
		}
	    $this->encoded = join(",",$ary);
		return $this->encoded;

	} // end func
	#################################################################
	# Short description:  製造 html output
	#
	#	$GLOBALS_DEF 是設定好的 該array的所有項目(如所有的尺寸:config設)
	#	$set_ary 為已選取的項目 (如某款式的尺碼)
	#	$col_max 為每列最多的 colume數目[內設為 10 可加入參數改變]
	#   @@@@ NOTE : checkbox 的變數名稱 為 $PHP_chkbx[]
	#	$html_class : 為html lay out的 css class 名稱
	#
	#################################################################
	function layout($GLOBALS_DEF, $set_ary=0, $col_max=10, $html_class="dgrn") {

		if (!is_array($set_ary)) {
			$set_ary= array();
		}
		$this->html = "";

		$row=0;
		$col=0;

		foreach ($GLOBALS_DEF AS  $key=>$val  ) {
			if ($col==$col_max) {
			    $col=0;
				$row++;
			}
			if ($col==0) {
				$html .= "<tr>";
			}

			$sel_str = (in_array($val , $set_ary))? " CHECKED " : "";

			$html .= '<td><input type=checkbox name="PHP_chkbx[]" value="'. $val .'" '.$sel_str.'><span class="'.$html_class.'">'.$val.'</td>'."\n";
			
			$col++;
		}
			# 補上剩下的<TD>
		for ($i=$col; $i<$col_max ; $i++ ) {
			$html .="<td>&nbsp;</td>\n";
		}

		$html .= "</tr>";
		
		return $html;

	} // end func
	

}	

?>