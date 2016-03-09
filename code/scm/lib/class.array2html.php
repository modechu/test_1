<?php
##################################################################
#  CLASS ARRAY2HTML
#		�� array ���� html���n����X
##################################################################

class ARRAY2HTML {

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
	# Short description:  �s�y html checkbox output [�ƿ�ɥi��]
	#
	#	$GLOBALS_DEF �O�]�w�n�� ��array���Ҧ�����(�p�Ҧ����ؤo:config�])
	#	$set_ary ���w��������� (�p�Y�ڦ����ؽX)
	#	$col_max ���C�C�̦h�� colume�ƥ�[���]�� 10 �i�[�J�ѼƧ���]
	#   @@@@ NOTE : checkbox ���ܼƦW�� �� $PHP_chkbx[]
	#	$html_class : ��html lay out�� css class �W��
	#
	#################################################################
	function checkbox($GLOBALS_DEF, $set_ary=0, $col_max=10, $html_class="dgrn") {

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
	
/*	#################################################################
	# Short description:  �s�y html slelect output [�U�Կ��]
	#
	#	$GLOBALS_DEF �O�]�w�n��ARRAY HTML�U�Կ�ȦParray��
	#	$select ���w��������� (�p�Y�ڦ����ؽX)
	#	$val_arry ���ﶵ���u�� [���ݻP$GLOBALS_DEF �P�ƥ�
	#   @@@@ NOTE : checkbox ���ܼƦW�� �� $PHP_chkbx[]
	#	
	#   $name �� select ����W
	#
	#################################################################
	function select($DEF, $select=0, $name, $css ) {

		$this->html = "";
		$col=0;
		foreach ($DEF AS  $key=>$val  ) {

			if ($col==0) {
				$this->html .= "<select NAME='".$name."' class=".$css.">\n";
				$this->html .= "<option VALUE=''>-- ��� --</option>\n";
			}
			if ($val == $select) {
				$sel_str = " SELECTED ";
			} else {
				$sel_str ="";
			}

			$this->html .= '<option VALUE="'.$val.'" '.$sel_str.'>'.$val.'</option>'."\n";
			
			$col++;
		}
		$this->html .= "</select>";
		return $this->html;

	} // end func
*/
	#################################################################
	# Short description:  �s�y html slelect output [�U�Կ��]
	#
	#  �� �����P�W�u�O�[�W onChange �� jb script @@@@@@@
	#
	#	$GLOBALS_DEF �O�]�w�n��ARRAY HTML�U�Կ�ȦParray��
	#	$select ���w��������� (�p�Y�ڦ����ؽX)
	#	$val_arry ���ﶵ���u�� [���ݻP$GLOBALS_DEF �P�ƥ�
	#   
	#	
	#   $name �� select ����W
	#	$onChange ��"selected" �� �ݵ�select��opt���e�X�����G [�Ω��s��]
	#################################################################
	function select($DEF, $select=0, $name="select_Opt", $css="", $onChange="", $value="" , $disabled=""  ) {

		$this->html = "";
		$col=0;
		
	if ($value=="")
	{
		//echo "A1";
		foreach ($DEF AS  $key=>$val  ) {

			if ($col==0) {
				$this->html .= '<select onChange="'.$onChange.'" ID="'.$name.'" NAME="'.$name.'" class="'.$css.'" '.$disabled.'>'."\n";
				$this->html .= '<option VALUE="">SELECT</option>'."\n";
			
			}
			if ($val == $select) {
				$sel_str = " SELECTED ";
			} else {
				$sel_str ="";
			}
			
			$this->html .= '<option VALUE="'.$val.'" '.$sel_str.'>'.$val.'</option>'."\n";
			
			$col++;
		}
	}else{
		//echo "A2";
		for ($i=0; $i < sizeof($DEF); $i++ ) {

			if ($col==0) {
//				$this->html .= "<select NAME='".$name."' class=".$css.">\n";
				$this->html .= '<select onChange="'.$onChange.'" ID="'.$name.'" NAME="'.$name.'" class="'.$css.'" '.$disabled.'>'."\n";
				$this->html .= '<option VALUE="">select</option>'."\n";
			
			}
			if ($value[$i] == $select) {
				$sel_str = " SELECTED ";
			} else {
				$sel_str ="";
			}
			
			$this->html .= '<option VALUE="'.$value[$i].'" '.$sel_str.'>'.$DEF[$i].'</option>'."\n";
			
			$col++;
		}
		if($this->html == '')return false;	
	}
		$this->html .= "</select>";

		return $this->html;

	} // end func

	function select_to_dhtml($DEF, $select=0, $name="select_Opt", $css="", $onChange="", $value="" ) {

		$this->html = "";
		$col=0;
		
	if ($value=="")
	{
		//echo "A1";
		foreach ($DEF AS  $key=>$val  ) {

			if ($col==0) {
				$this->html .= '<select onChange="'.$onChange.'" ID="'.$name.'" NAME="'.$name.'" class="'.$css.'" >';
				$this->html .= '<option VALUE="">SELECT</option>';
			
			}
			if ($val == $select) {
				$sel_str = " SELECTED ";
			} else {
				$sel_str ="";
			}
			
			$this->html .= '<option VALUE="'.$val.'" '.$sel_str.'>'.$val.'</option>';
			
			$col++;
		}
	}else{
		//echo "A2";
		for ($i=0; $i < sizeof($DEF); $i++ ) {

			if ($col==0) {
//				$this->html .= "<select NAME='".$name."' class=".$css.">";
				$this->html .= '<select onChange="'.$onChange.'" ID="'.$name.'" NAME="'.$name.'" class="'.$css.'" >';
				$this->html .= '<option VALUE="">select</option>';
			
			}
			if ($value[$i] == $select) {
				$sel_str = " SELECTED ";
			} else {
				$sel_str ="";
			}
			
			$this->html .= '<option VALUE="'.$value[$i].'" '.$sel_str.'>'.$DEF[$i].'</option>';
			
			$col++;
		}
		if($this->html == '')return false;	
	}
		$this->html .= "</select>";

		return $this->html;

	} // end func

	
	#################################################################
	# Short description:  �s�y html slelect output [�U�Կ��]
	#
	#  �� �����P�W�u�O�[�W onChange �� jb script @@@@@@@
	#
	#	$GLOBALS_DEF �O�]�w�n��ARRAY HTML�U�Կ�ȦParray��
	#	$select ���w��������� (�p�Y�ڦ����ؽX)
	#	$val_arry ���ﶵ���u�� [���ݻP$GLOBALS_DEF �P�ƥ�
	#   
	#	
	#   $name �� select ����W
	#	$onChange ��"selected" �� �ݵ�select��opt���e�X�����G [�Ω��s��]
	#################################################################
	function select2($DEF, $select=0, $name="select_Opt", $css="", $onChange="", $value="" ) {

		$this->html = "";
		$col=0;
		
	if ($value=="")
	{
		foreach ($DEF AS  $key=>$val  ) {

			if ($col==0) {
				$this->html .= '<select onChange="'.$onChange.'" NAME="'.$name.'" class="'.$css.'">'."\n";
				$this->html .= '<option VALUE="">select</option>\n';
			}
			if ($val == $select) {
				$sel_str = " SELECTED ";
			} else {
				$sel_str ="";
			}
			
			$this->html .= '<option VALUE="'.$val.'" '.$sel_str.'>'.$val.'</option>'."\n";
			
			$col++;
		}
	}else{
		
		for ($i=0; $i < sizeof($DEF); $i++ ) {

			if ($col==0) {
//				$this->html .= "<select NAME='".$name."' class=".$css.">\n";
				$this->html .= '<select onChange="'.$onChange.'" NAME="'.$name.'" class="'.$css.'">'."\n";
				$this->html .= '<option VALUE="">select</option>\n';
			}
			if ($value[$i] == $select) {
				$sel_str = " SELECTED ";
			} else {
				$sel_str ="";
			}
			
			$this->html .= '<option VALUE="'.$value[$i].'" '.$sel_str.'>'.$DEF[$i].'</option>'."\n";
			
			$col++;
		}
	}
		$this->html .= "</select>";

		return $this->html;

	} // end func
	
	
	
	#################################################################
	# Short description:  �s�y html slelect output [�U�Կ��]
	#
	#  �� �����P�W�u�O�[�W onChange �� jb script @@@@@@@
	#
	#	$GLOBALS_DEF �O�]�w�n��ARRAY HTML�U�Կ�ȦParray��
	#	$select ���w��������� (�p�Y�ڦ����ؽX)
	#	$val_arry ���ﶵ���u�� [���ݻP$GLOBALS_DEF �P�ƥ�
	#   
	#	
	#   $name �� select ����W
	#	$onChange ��"selected" �� �ݵ�select��opt���e�X�����G [�Ω��s��]
	#################################################################

function select_by_pline($DEF, $select=0, $name="select_Opt", $css="", $onChange="", $value="" ) {

		$this->html = "";
		$col=0;
		
	if ($value=="")
	{
		foreach ($DEF AS  $key=>$val  ) {

			if ($col==0) {
				$this->html .= "<select onChange=\"".$onChange."\" NAME='".$name."' class=".$css." >\n";
				$this->html .= "<option VALUE=''>select</option>\n";
			}
			if ($val == $select) {
				$sel_str = " SELECTED ";
			} else {
				$sel_str ="";
			}
			
			$this->html .= '<option VALUE="'.$val.'" '.$sel_str.'>'.$val.'</option>'."\n";
			
			$col++;
		}
	}else{
		
		for ($i=0; $i < sizeof($DEF); $i++ ) {

			if ($col==0) {
//				$this->html .= "<select NAME='".$name."' class=".$css.">\n";
				$this->html .= "<select onChange=\"".$onChange."\" NAME='".$name."' class=".$css.">\n";

			}
			if ($value[$i] == $select) {
				$sel_str = " SELECTED ";
			} else {
				$sel_str ="";
			}
			
			$this->html .= '<option VALUE="'.$value[$i].'" '.$sel_str.'>'.$DEF[$i].'</option>'."\n";
			
			$col++;
		}
	}
		$this->html .= "</select>";

		return $this->html;

	} // end func



	#################################################################
	# function select_id
	# �̰}�C���U�Կ�檺key�Pvalue
	#  
	#################################################################


	function select_id($DEF, $select="", $name="select_Opt", $id="" , $css="", $onChange="",$style="",$sort="",$nid=0 , $disabled="") {

		$this->html = '';
		$col=0;
		if(is_array($DEF)){
			if ($sort) arsort($DEF);
			foreach ($DEF AS  $key => $val  ) {
				if ($col==0) {
					$this->html .= '<select onChange="'.$onChange.'" NAME="'.$name.'" ID="'.$id.'" class="'.$css.'" style="width:'.$style.'px" '.$disabled.'>'."\n";
					if(empty($nid))$this->html .= '<option VALUE="">SELECT</option>'."\n";
				}
				if ($key == $select) {
					$sel_str = ' SELECTED ';
				} else {
					$sel_str = '';
				}
				# ���� value
				$this->html .= '<option VALUE="'.$key.'" '.$sel_str.'>'.$val.'</option>'."\n";
				$col++;
			}
			$this->html .= '</select>'."\n";
		}else{
			$this->html .= '<select onChange="'.$onChange.'" NAME="'.$name.'" class="'.$css.'" '.$disabled.'>'."\n";
			$this->html .= '<option VALUE="'.$key.'" SELECTED>'.$val.'</option></select>'."\n";
		}
		if(empty($DEF)) $this->html = 'No Data';
		return $this->html;
	} // end func



	// function select_id($DEF, $select="", $name="select_Opt", $id="" , $css="", $onChange="",$style="",$sort="",$nid=0) {

		// $this->html = '';
		// $col=0;
		// if(is_array($DEF)){
			// if ($sort) arsort($DEF);
			// foreach ($DEF AS  $key => $val  ) {
				// if ($col==0) {
					// $this->html .= '<select onChange="'.$onChange.'" NAME="'.$name.'" ID="'.$id.'" class="'.$css.'" style="width:'.$style.'px" >\n';
					// if(empty($nid))$this->html .= '<option VALUE="">select</option>\n';
				// }
				// if ($key == $select) {
					// $sel_str = ' SELECTED ';
				// } else {
					// $sel_str = '';
				// }
				// # ���� value
				// $this->html .= '<option VALUE="'.$key.'" '.$sel_str.'>'.$val.'</option>';
				// $col++;
			// }
			// $this->html .= '</select>';
		// }else{
			// $this->html .= '<select onChange="'.$onChange.'" NAME="'.$name.'" class="'.$css.'">';
			// $this->html .= '<option VALUE="'.$key.'" SELECTED>'.$val.'</option></select>';
		// }
		// if(empty($DEF)) $this->html = 'No Data';
		// return $this->html;
	// } // end func




	#################################################################
	# Short description:  �s�y html slelect output [�U�Կ��]
	#
	#  �� �����P�W�u�O�[�W onChange �� jb script @@@@@@@
	#
	#	$GLOBALS_DEF �O�]�w�n��ARRAY HTML�U�Կ�ȦParray��
	#	$select ���w��������� (�p�Y�ڦ����ؽX)
	#	$val_arry ���ﶵ���u�� [���ݻP$GLOBALS_DEF �P�ƥ�
	#   
	#	
	#   $name �� select ����W
	#	$onChange ��"selected" �� �ݵ�select��opt���e�X�����G [�Ω��s��]
	#################################################################
	function select_sch_line($DEF, $select=0, $name="select_Opt", $css="", $onChange="", $value="" ) {

		$this->html = "";
		$col=0;
		
	if ($value=="")
	{
		foreach ($DEF AS  $key=>$val  ) {

			if ($col==0) {
				$this->html .= "<select onChange=".$onChange." NAME='".$name."' class=".$css." >\n";
				$this->html .= "<option VALUE=''>select</option>\n";
			}
			if ($val == $select) {
				$sel_str = " SELECTED ";
			} else {
				$sel_str ="";
			}
			
			$this->html .= "<option VALUE='".$val."' ".$sel_str.">".$val."</option>"."\n";
			
			$col++;
		}
	}else{
		
		for ($i=0; $i < sizeof($DEF); $i++ ) {

			if ($col==0) {
//				$this->html .= "<select NAME='".$name."' class=".$css.">\n";
				$this->html .= "<select onChange=".$onChange." NAME='".$name."' class=".$css.">\n";
				$this->html .= "<option VALUE=''>select</option>\n";
			}
			if ($value[$i] == $select) {
				$sel_str = " SELECTED ";
			} else {
				$sel_str ="";
			}
			
			$this->html .= "<option VALUE='".$value[$i]."' ".$sel_str.">".$DEF[$i]."</option>"."\n";
			
			$col++;
		}
	}
		$this->html .= "</select>";

		return $this->html;

	} // end func


}	// end class

?>