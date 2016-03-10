<?php
##################################################################
#  CLASS ARRAY2HTML
#		由 array 做成 html必要的輸出
##################################################################

class ARRAY2HTML {

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
	# Short description:  製造 html checkbox output [複選時可用]
	#
	#	$GLOBALS_DEF 是設定好的 該array的所有項目(如所有的尺寸:config設)
	#	$set_ary 為已選取的項目 (如某款式的尺碼)
	#	$col_max 為每列最多的 colume數目[內設為 10 可加入參數改變]
	#   @@@@ NOTE : checkbox 的變數名稱 為 $PHP_chkbx[]
	#	$html_class : 為html lay out的 css class 名稱
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
			# 補上剩下的<TD>
		for ($i=$col; $i<$col_max ; $i++ ) {
			$html .="<td>&nbsp;</td>\n";
		}

		$html .= "</tr>";
		
		return $html;

	} // end func
	
/*	#################################################################
	# Short description:  製造 html slelect output [下拉選單]
	#
	#	$GLOBALS_DEF 是設定好的ARRAY HTML下拉選值同array值
	#	$select 為已選取的項目 (如某款式的尺碼)
	#	$val_arry 為選項的真值 [必需與$GLOBALS_DEF 同數目
	#   @@@@ NOTE : checkbox 的變數名稱 為 $PHP_chkbx[]
	#	
	#   $name 為 select 物件名
	#
	#################################################################
	function select($DEF, $select=0, $name, $css ) {

		$this->html = "";
		$col=0;
		foreach ($DEF AS  $key=>$val  ) {

			if ($col==0) {
				$this->html .= "<select NAME='".$name."' class=".$css.">\n";
				$this->html .= "<option VALUE=''>-- 選擇 --</option>\n";
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
	# Short description:  製造 html slelect output [下拉選單]
	#
	#  ※ 全部同上只是加上 onChange 的 jb script @@@@@@@
	#
	#	$GLOBALS_DEF 是設定好的ARRAY HTML下拉選值同array值
	#	$select 為已選取的項目 (如某款式的尺碼)
	#	$val_arry 為選項的真值 [必需與$GLOBALS_DEF 同數目
	#   
	#	
	#   $name 為 select 物件名
	#	$onChange 為"selected" 時 需視select的opt為送出之結果 [用於更新時]
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
	# Short description:  製造 html slelect output [下拉選單]
	#
	#  ※ 全部同上只是加上 onChange 的 jb script @@@@@@@
	#
	#	$GLOBALS_DEF 是設定好的ARRAY HTML下拉選值同array值
	#	$select 為已選取的項目 (如某款式的尺碼)
	#	$val_arry 為選項的真值 [必需與$GLOBALS_DEF 同數目
	#   
	#	
	#   $name 為 select 物件名
	#	$onChange 為"selected" 時 需視select的opt為送出之結果 [用於更新時]
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
	# Short description:  製造 html slelect output [下拉選單]
	#
	#  ※ 全部同上只是加上 onChange 的 jb script @@@@@@@
	#
	#	$GLOBALS_DEF 是設定好的ARRAY HTML下拉選值同array值
	#	$select 為已選取的項目 (如某款式的尺碼)
	#	$val_arry 為選項的真值 [必需與$GLOBALS_DEF 同數目
	#   
	#	
	#   $name 為 select 物件名
	#	$onChange 為"selected" 時 需視select的opt為送出之結果 [用於更新時]
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
	# 依陣列做下拉選單的key與value
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
				# 分割 value
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
				// # 分割 value
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
	# Short description:  製造 html slelect output [下拉選單]
	#
	#  ※ 全部同上只是加上 onChange 的 jb script @@@@@@@
	#
	#	$GLOBALS_DEF 是設定好的ARRAY HTML下拉選值同array值
	#	$select 為已選取的項目 (如某款式的尺碼)
	#	$val_arry 為選項的真值 [必需與$GLOBALS_DEF 同數目
	#   
	#	
	#   $name 為 select 物件名
	#	$onChange 為"selected" 時 需視select的opt為送出之結果 [用於更新時]
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