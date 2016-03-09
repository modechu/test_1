<?php
	
class array2checkbox {

	var $decoded = array();
	var $encoded;
	var $html;
	var $msg;

	#################################################################
	# Short description: ��Ҧ��i�઺ ARRAY �Ѷ}
	#
	#
	#################################################################
	function decode_array($ary) {
	    
		$this->decoded = split(",",$ary);

		return $this->decoded;
	} // end func

	#################################################################
	# Short description: �� ARRAY  pack�_��
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
	# Short description:  �s�y html output
	#
	#	$GLOBALS_DEF �O�]�w�n�� ��array���Ҧ�����(�p�Ҧ����ؤo:config�])
	#	$set_ary ���w��������� (�p�Y�ڦ����ؽX)
	#	$col_max ���C�C�̦h�� colume�ƥ�[���]�� 10 �i�[�J�ѼƧ���]
	#   @@@@ NOTE : checkbox ���ܼƦW�� �� $PHP_chkbx[]
	#	$html_class : ��html lay out�� css class �W��
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
			# �ɤW�ѤU��<TD>
		for ($i=$col; $i<$col_max ; $i++ ) {
			$html .="<td>&nbsp;</td>\n";
		}

		$html .= "</tr>";
		
		return $html;

	} // end func
	

}	

?>