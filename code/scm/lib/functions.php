<?php



/*----------
# �\�໡�� : ���ͷs���~��ID
# ���p�ܼ� : 
# ��J�ܼ� : $table
# ��X�ܼ� : �~��ID
# ���g��� : 2013/09/05
# �Ƶ����� : int(10)
----------*/
function get_user($id) {

$MySQL = getDBconnect();

$q_str = "SELECT `id`,`emp_id`,`name`,`phone`,`cellphone`,`email` FROM `user` WHERE `id` = '".$id."' ;";

if (!$q_result = $MySQL->query($q_str)) {
	$_SESSION['MSG'][] = "Error! Can't access database!";
	return false;
}

$row = $MySQL->fetch($q_result);

return $row;

} # FUN END

function get_user_html($id){
    $user_arr = get_user($id);
    $str = $user_arr['name'];
    return $str;
}



function check_authority($auth, $job) {

	global $MYSQL_DB;

	# �����v������~ ���s�θ�Ʈw�W�٧@������
	if ( !empty($_SESSION[$MYSQL_DB]) )	$_SESSION['USER']['ADMIN'] = $GLOBALS['SCACHE']['ADMIN'] = $_SESSION[$MYSQL_DB];
  
	# 000 ���ΧP�_�˵��v��
	if($auth <> '000'){
		if( empty($_SESSION['USER']['ADMIN']['authority']) || $_SESSION['USER']['ADMIN']['authority'] != $auth ){
			$_SESSION['USER']['ADMIN']['authority'] = $auth;
			unset($_SESSION['TEMP']);
		}

		if(!$pp=$GLOBALS['admin']->is_power($auth,$job)){
			$op['msg'][] = $_SESSION['msg'][] = "sorry! you don't have this Authority !";
			page_display($op,$auth,'error.html');
			exit;
		}
	}

}


/*----------
# �\�໡�� : ��X����
# ���p�ܼ� : 
# ��J�ܼ� : $auth, $job
# ��X�ܼ� : true,false
# ���g��� : 
# �Ƶ����� : 
----------*/
function page_display($op,$auth,$tpl,$Title='',$SubTitle='',$des='') {

	global $MYSQL_DB;

	# �����v������~ ���s�θ�Ʈw�W�٧@������
	if ( !empty($_SESSION[$MYSQL_DB]) )	$_SESSION['USER']['ADMIN'] = $GLOBALS['SCACHE']['ADMIN'] = $_SESSION[$MYSQL_DB];

	if($auth <> '000'){
		$allow = $GLOBALS['admin']->decode_perm($auth);   // �]�w �s�W�R���v��
		// foreach ($allow AS $key => $val)$op["flag_".$key] = !empty($val)?$val:'';
        # �e�x�Ϊ��v���P�_
		foreach ($allow AS $key => $val)$op[$key."_flag"] = !empty($val)?$val:'';
	}
	// $op['admin_flag'] = '';
    
    # SA �޲z�̵n�J
	$op['m_flag'] = (!empty($_SESSION['USER']['ADMIN']['dept']) && $_SESSION['USER']['ADMIN']['dept'] == "SA")? 1 : '';
    
    # �����ɨϥΡA�w��Ū��ۦP���ɡA�i�ð��e�xjavascript���{���A���local�t��
    $op['did'] = $_SESSION['did'] = date('His');#$_SERVER['REQUEST_TIME'];
	
	# HEADER
	$op['Title'] = ' Carnival SCM [ '.$Title.' ] ';
    $op['SubTitle'] = $SubTitle;
    $op['date'] = date('Y-m-d H:i:s');

    // �ª��T��
	if(!empty($_SESSION['MSG']))$op['msg'] = $_SESSION['MSG'];
	$op['msg_YES'] = (isset($op['msg']) && $op['msg']) ? 1 : "";

    // �s���T��
    if(!empty($_SESSION['MSG'])){
        $op['MSG'] = ' �� '.$_SESSION['MSG'];
        unset($_SESSION['MSG']);
    } else {
        $op['MSG'] = '';
    }

    $op['Admin'] = (!empty($_SESSION['gs_admin']))? $_SESSION['gs_admin']: '0' ;
    $op['STATUS'] = (!empty($_SESSION['STATUS']))? $_SESSION['STATUS'] : '' ;
    @$op['img_sid'] = $_SERVER['REQUEST_TIME'];

    $op['medium'] = (!empty($op['medium']))? $op['medium'] : '' ;

	unset($_SESSION['MSG']);
	unset($_SESSION['msg']);	

    $op['PHP_action'] = $GLOBALS['PHP_action'];
    
	$GLOBALS['layout']->assign($op);
	$GLOBALS['layout']->display($tpl);
}



/*----------
# �\�໡�� : ���ͷs���~��ID
# ���p�ܼ� : 
# ��J�ܼ� : $table
# ��X�ܼ� : �~��ID
# ���g��� : 2013/09/05
# �Ƶ����� : int(10)
----------*/
function get_insert_id($table,$UTF8='',$DIE='') {

global $MySQL;

if(!empty($UTF8) && empty($DIE)) $MySQL->po_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD'], $GLOBALS['UTF_DB']);

$q_str = "SELECT `id` FROM `".$table."` WHERE `id` LIKE '".date('ym')."%' ORDER BY `id` DESC LIMIT 0 , 1 ;";

if (!$q_result = $MySQL->query($q_str)) {
	// $_SESSION['MSG'][] = "Error! Can't access database!";
	return false;
}

$row = $MySQL->fetch($q_result);

if ( empty($row) ) {
	$id = 0;
} else {
	$id = substr($row['id'],-6,6);
}

if(!empty($UTF8) && empty($DIE) ) $MySQL->po_disconnect();

return date('ym').str_pad( ($id+1) , 6, 0, STR_PAD_LEFT);

} # FUN END




/**\
@ 
\**/

function get_select($rows,$name,$select='',$css='',$onChange='',$width='') {

$html = '';

foreach ($rows AS  $key => $val  ) {

    if ($key==0) {
        $html .= '<select onChange="'.$onChange.'" name="'.$name.'" id="'.$name.'" class="'.$css.'" style="width:'.$width.'px;">\n';
        $html .= '<option value="">SELECT</option>\n';
    }

    if ($val['key'] == $select) {
        $sel_str = ' selected ';
    } else {
        $sel_str = '';
    }

    # ���� value
    $html .= '<option value="'.$val['key'].'" '.$sel_str.'>'.$val['name'].'</option>';
}

$html .= '</select>';

if(empty($rows)) $html = 'No Data';

return $html;

} # END






/**\
@ set_ie
\**/

function set_ie($time) {

return number_format(( $time / $GLOBALS['IE_TIME'] ),2,'','');

} # END



/**\
@ set_su
\**/

function set_su($ie,$qty) {
// function set_su($ie1,$ie2='',$qty) {
// $ie = !empty($ie2) ? $ie2 : $ie1 ;
return number_format(( $ie * $qty ),0,'','');

} # END







/**\
@ get_yy_mm_dd
\**/
function get_yy_mm_dd($date='') {

$arr = array();

if( !empty($date) )
    $date = explode("-", $date);

for ( $i = 0 ; $i < 10 ; $i ++ )
    $arr['yy'][date('Y')-5+$i] = date('Y')-5+$i;

if( !empty($date) && $date[0] != '0000' )
    $arr['yys'] = $date[0];
else
    $arr['yys'] = date('Y');

for ( $i = 1 ; $i < 13 ; $i ++ )
    $arr['mm'][str_pad( $i , 2, 0, STR_PAD_LEFT)] = str_pad( $i , 2, 0, STR_PAD_LEFT);
if( !empty($date) && $date[1] != '00' )
    $arr['mms'] = $date[1];
else
    $arr['mms'] = str_pad( date('n') , 2, 0, STR_PAD_LEFT);

for ( $i = 1 ; $i < date('t')+1 ; $i ++ )
    $arr['dd'][str_pad( $i , 2, 0, STR_PAD_LEFT)] = str_pad( $i , 2, 0, STR_PAD_LEFT);
if( !empty($date) && $date[2] != '00' )
    $arr['dds'] = $date[2];
else
    $arr['dds'] = str_pad( date('d') , 2, 0, STR_PAD_LEFT);

return $arr;

}






/*----------
# �\�໡�� : �ഫ�}�C
# ���p�ܼ� :  
# ��J�ܼ� : [0] => Array ( [0] => 15 [id] => 15 [1] => SU [team_code] => SU ) 
# ��X�ܼ� : SU => SU
# ���g��� : 2007/09/13
# ���g�H�� : Mode
# �Ƶ����� : 
----------*/

function array_one($str){
  foreach($str as $k => $v){
    $val[$v[1]] = $v[1];
  }
  return !empty($val)?$val:'';
} // end func



/*----------
# �\�໡�� : �Ǧ^�ϥΪ̩m�W
# ���p�ܼ� :  
# ��J�ܼ� : Login ID
# ��X�ܼ� : user name
# ���g��� : 2008/02/12
# ���g�H�� : Mode
# �Ƶ����� : 
----------*/

function _name($confirmer,$href=''){
  global $User;

  if(!empty($confirmer)){
    foreach($_SESSION['ADMIN']['manager'] as $key => $val)
     $manager=($confirmer == $key)?1:'';
    // $user_name = ($manager==1)? $confirmer : $User->get_field('name',"login_id = '$confirmer'");
    // $user_name = ($href==1)? $user_name : ' <a href="javascript:message_out(\''.$confirmer.'\')" style="cursor:pointer"> '.$user_name.' </a> ';
    $user_name = ($href==1)? $user_name : ' <a href="javascript:message_out(\''.$confirmer.'\')" style="cursor:pointer"> '.$confirmer.' </a> ';
  }
  if(!empty($user_name))return $user_name;
}




/**\
@ �P�_��Ƨ��O�_�s�b�A�إ߸�Ƨ�
\**/

function add_folder($folders){
  if(!is_dir($folders)){
    if (ereg("MSIE",$_SERVER["HTTP_USER_AGENT"])){
      mkdir ($folders, 0777);
    }else{
      mkdir ($folders, 0777);
      chown ($folders,'apache');
    }
  }
}


/**\
@ �Y�Ϥj�p�p��
\**/

function count_pic($set_width,$set_height,$im_width,$im_height){

	$max_width = $set_width;
	$max_height = $set_height;

	$x_ratio = $max_width / $im_width;
	$y_ratio = $max_height / $im_height;
	if ( ($im_width <= $max_width) && ($im_height <= $max_height) )	{
		$ok_width = $im_width;
		$ok_height = $im_height;
	}
	if ($x_ratio < $y_ratio){
		$ok_height = ceil($x_ratio * $im_height);
		$ok_width = $max_width;
	}
	if ($x_ratio > $y_ratio){
		$ok_width = ceil($y_ratio * $im_width);
		$ok_height = $max_height;
	}
	if ($x_ratio == $y_ratio){
		$ok_height = ceil($x_ratio * $im_height);
		$ok_width = ceil($y_ratio * $im_width);
	}
	$ok['width'] = $ok_width;
	$ok['height'] = $ok_height;
	return $ok;
}


/*----------
# �\�໡�� : �s�X
# ���p�ܼ� :  
# ��J�ܼ� : �r��
# ��X�ܼ� : base64
# ���g��� : 2008/07/17
# ���g�H�� : Mode
# �Ƶ����� : 
----------*/

function _base64($str){
  return "=?BIG-5?B?".base64_encode($str)."?="; 
}



/*----------
# �\�໡�� : �X�p�}�C�ƶq
# ���p�ܼ� :  
# ��J�ܼ� : 
# ��X�ܼ� : 
# ���g��� : 2012/02/09
# ���g�H�� : Mode
# �Ƶ����� : 
----------*/

function qty_arr($qty_arr,$qty_plus){

	$qty = explode( ',' , $qty_arr);
	$t_qty = array();
	foreach( $qty as $key => $val ) {
		if( !empty($qty_plus[$key]) ) {
			$t_qty[$key] = ($qty_plus[$key] + $val);
			// echo $t_qty[$key].'<br>';
		} else {
			$t_qty[$key] = $val;
		}
	}
	return $t_qty;
}



/*----------
# �\�໡�� : paymeny return
# ���p�ܼ� :  
# ��J�ܼ� : 
# ��X�ܼ� : 
# ���g��� : 2012/06/06
# ���g�H�� : Mode
# �Ƶ����� : mk = 2 = TT before ~ after
----------*/

function re_dm_way($dm_way){

    $dm_waya = explode('|',$dm_way);

    if( !empty($dm_waya[1]) ){
        $mk = 2;
        $dm_ways = $dm_waya[0].'% TT before shipment, '.$dm_waya[1].'% TT after shipment'; 
    } else {
        $mk = 0;
        $dm_ways = $dm_way;
        $dm_waya[0] = '';
        $dm_waya[1] = '';
    }

	return array( '0' => $mk , '1' => $dm_ways , 'before' => $dm_waya[0] , 'after' => $dm_waya[1]  ) ;
}



/*----------
# �\�໡�� : get_back_str
# ���p�ܼ� :  
# ��J�ܼ� : 
# ��X�ܼ� : 
# ���g��� : 2012/10/12
# ���g�H�� : Mode
# �Ƶ����� :
----------*/

function get_back_str($pm,$action,$reset=''){
	// echo $_GET['PHP_action'] . ' ~ ' . $reset.'<br>';
	// echo $_SESSION['action'][$pm][$action].'<br>';
	// if( $reset || !$_SESSION['action'][$pm][$action] ){

		$back_str = '?PHP_action='.$action;
		
		if( $_GET ) {
			if( $_GET['PHP_action'] != $PHP_action ){
				foreach($_GET as $k => $v){
					if($k != 'PHP_action')
						$back_str .= '&'.$k.'='.$v;
				}
			}
		} else {
			if( $_POST['PHP_action'] != $PHP_action ){
				foreach($_POST as $k => $v){
					if($k != 'PHP_action')
						$back_str .= '&'.$k.'='.$v;
				}
			}
		}
		
		$_SESSION['action'][$pm][$action] = $back_str;
		
	// }
	
	return $_SESSION['action'][$pm][$action];
	
}


// echo $_GET['PHP_action'] . ' ~ ' . $action.'<br>';
// if( $_GET ) {
	// if( $_GET['PHP_action'] != $action ){
		// $back_str = '?PHP_action='.$action;
		// foreach($_GET as $k => $v){
			// if($k != 'PHP_action')
				// $back_str .= '&'.$k.'='.$v;
		// }
		// $_SESSION['action'][$pm][$action] = $back_str;
	// }
// } else {
	// if( $_POST['PHP_action'] != $action ){
		// $back_str = '?PHP_action='.$action;
		// foreach($_POST as $k => $v){
			// if($k != 'PHP_action')
				// $back_str .= '&'.$k.'='.$v;
		// }
		// $_SESSION['action'][$pm][$action] = $back_str;
	// }
// }
// print_r($_SESSION['action']);




/*----------
# �\�໡�� : �p�� Size Breakdown ���ƶq �� HTML
# ���p�ܼ� :  
# ��J�ܼ� : 
# ��X�ܼ� : 
# ���g��� : 2012/12/07
# ���g�H�� : Mode
# �Ƶ����� : 
----------*/

function get_bom_colorway_qty_html($SIZE,$WIQTY){

$HTML = '
<table cellspacing="0" cellpadding="0" cols="0">
	<tr>
		<td class="colorway_title_size"></td>
';
$size = array();
$size = explode(',',$SIZE);
foreach( $size as $key => $val){
$HTML .= '
		<td class="colorway_title_size">'.$val.'</td>';
}

$HTML .= '
		<td class="colorway_title_size">SUM</td>
	</tr>';

$color_arr = array();
$size_qty = array();
foreach( $WIQTY as $wkey => $wval){
$HTML .= '
	<tr>
		<td class="colorway_color">'.$wval['colorway'].'</td>';

	$color = array();
	$wsize = array();
	$qty = array();
	
	$wsize = explode(',',$wval['qty']);	
	foreach( $wsize as $key => $val){
$HTML .= '
		<td class="colorway_qty">'.number_format($val).'</td>';
		if ( $size[$key] )
		$qty[$size[$key]]=$val;
		$size_qty[$size[$key]]+=$val;
	}
$HTML .= '
		<td class="colorway_qty_sum">'.number_format(array_sum($wsize)).'</td>
	</tr>';
	$color['colorway'] = $wval['colorway'];
	$color['qty'] = $qty;
	$color['ttl_qty'] = array_sum($wsize);
	
	$color_arr[] = $color;
}

$HTML .= '
	<tr>
		<td class="colorway_qty_ttl"></td>';
foreach( $size_qty as $key => $val){
$HTML .= '
		<td class="colorway_qty_ttl">'.number_format($val).'</td>';
}
$HTML .= '
		<td class="colorway_qty_ttl">'.number_format(array_sum($size_qty)).'</td>
	</tr>
</table>';

$color_arr['size_qty'] = $size_qty;
$color_arr['size_ttl_qty'] = array_sum($size_qty);

return $HTML;

}


/*----------
# �\�໡�� : �p�� Size Breakdown ���ƶq
# ���p�ܼ� :  
# ��J�ܼ� : 
# ��X�ܼ� : 
# ���g��� : 2012/12/07
# ���g�H�� : Mode
# �Ƶ����� : 
----------*/

function get_bom_colorway_qty($SIZE,$WIQTY){

	$size = array();
	$size = explode(',',$SIZE);
	foreach( $size as $key => $val){
		$size[$key]=$val;
		// echo $key.' => '.$val.'<br>';
	}
	
	$color_arr = array();
	$size_qty = array();
	foreach( $WIQTY as $wkey => $wval){
		$color = array();
		$wsize = array();
		$qty = array();
		
		$wsize = explode(',',$wval['qty']);	
		foreach( $wsize as $key => $val){
			// echo $size[$key].' => '.$key.' => '.$val.'<br>';
			if ( $size[$key] )
			$qty[$size[$key]]=$val;
			$size_qty[$size[$key]]+=$val;
			
			$color_arr[$wval['colorway']][$size[$key]] = $val;
		}
		
		$color['colorway'] = $wval['colorway'];
		$color['qty'] = $qty;
		$color['ttl_qty'] = array_sum($wsize);
		
		$color_arr[$wval['colorway']]['ttl_qty'] = array_sum($wsize);
		
		$color_arr[] = $color;
	}

	$color_arr['size_qty'] = $size_qty;
	$color_arr['size_ttl_qty'] = array_sum($size_qty);
	
	return $color_arr;
}
	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	function get_search() : test �۰ʷj�M����(SEARCH)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_search($SQL_KEY,$code,$action='') {

	global $M_get;

	$__dept[] = $_dept = $dept = $_SESSION['USER']['ADMIN']['dept'];
	if(substr($_dept,0,1) == "K")$_dept='K0';
	if(substr($_dept,0,1) == "J")$_dept='J0';

	if ( $dept == get_array_dept($__dept,'only_sales_dept6') ) $_dept = '';
	if ( $dept == get_array_dept($__dept,'only_sales_dept8') ) $_dept = 'factory';
	if ( $dept == get_array_dept($__dept,'only_sales_dept7') ) $_dept = 'super';
	
	$vars = $M_get->get_fields($code,$SQL_KEY);
	
	switch ($_dept) {



		case "super":
		$vars = array_filter($vars,'only_sales_dept5');
		sort($vars);
		$m = 0;
        $str='';
		foreach($vars AS $key => $val){
			$o = ($m)?"OR":"";
			$str .= " ".$o." ( ".$SQL_KEY." = '".$val."' ) ";
			++$m;
		}
		break;



		case "K0":
		$vars = array_filter($vars,'only_sales_dept'); 
		if($dept =="K0"){
			sort($vars);
			$m = 0;
			foreach($vars AS $key => $val){
				if($m)$o="or";
				$str .= " ".$o." ".$SQL_KEY." = '".$val."' ";
				++$m;
			}
		}else{
			$str = " ".$SQL_KEY." = '".$dept."' "; 
		}
		break;



		case "J0":
		$vars = array_filter($vars,'only_sales_dept2'); 
		if($dept =="J0"){
			sort($vars);
			$m = 0;
			foreach($vars AS $key => $val){
				if($m)$o="or";
				$str .= " ".$o." ".$SQL_KEY." = '".$val."' ";
				++$m;
			}
		}else{
			$str = " ".$SQL_KEY." = '".$dept."' "; 
		}
		break;



		case "factory":
		$vars = array_filter($vars,'only_sales_dept8');
		
		# �r����}�C��U�@��P�_�O�_���ӭ�
		$action = array($action);
		# �P�_ �� �O�_�� Production �ϰ�
		$only_factory = array_filter($action,'only_sales_dept9');
		if($only_factory){
			if ($_SESSION['USER']['ADMIN']['team_id'] != 'MD'){
				# �P�_���� ��
				$SQL_KEY = $_dept;
				$str = " ".$SQL_KEY." = '".$dept."' "; 
			}else{
				$str = " ".$SQL_KEY." = '".$dept."' "; 
			}
		#}elseif($for_fty){
		}else{
			if($_SESSION['USER']['ADMIN']['team_id'] == 'MD'){
				$str = " ".$SQL_KEY." = '".$dept."' ";
			}else{
				$str = " factory = '".$dept."' ";
			}				

			if( $action[0] == "factory" ) {
				if($_SESSION['USER']['ADMIN']['team_id'] != 'MD'){
					$str = " ".$code." = '".$dept."' ";
				}else{
					sort($vars);
					$m = 0;
					$str = '';
					foreach($vars AS $key => $val){
						if($m)$o="or";
						$str .= " ".$o." ".$code." = '".$val."' ";
						++$m;
					}
				}
			}
		}

		break;

		default:
		$str = "( ".$SQL_KEY." = '".$dept."' )";
		break;
	}
	return $str;
} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	function get_array_dept() : �����Ƕi�Ӫ� Action
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_array_dept($dept,$only){
	$str = array_filter($dept,$only);
	return !empty($str[0])?$str[0]:0;
}



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# this scripts only for personal useage : since 2002/08/23  JACK YANG
#	get_su($s_date,$e_date,$T_su,$year=0)	�p��U�륭��SU��
#	make_password($length=8)	reply $password
#	display_header($title)
#	display_footer()
#	display_fatal($error)		[��@�� error]
#	display_errors($errs)		errors �}�C $errs[]  �Ω�{��ù��W�����~
#   get_hidden_vars()			���X post �Ӫ����
#   show_back_fix_link()		�^�W���ץ����~
#   show_reload_aft_fixed()		�ץ��᪺ RELOAD PAGE
#   set_date_from_smarty($prefix, &$vars)
#	getDBconnect()
#	apart_date($mode=0,$date_str=0)	$mode=0:mysql��datetime�ন$year,$month,$day,$time��
#	decode_date($mode=0,$date_str=0)
#	su_mon_div($qty, $ratio, $s_date, $f_date)	�N �ƶq ����su �A���t��Ͳ������
#	is_admin_login()	�ˬd�O�_�n�J
#	verify_pid($pid)	�ˬd�����Ҧr��
#	redirect_page($url)
#	cfm_delete($str)		return: true / false
#	is_manager_login($id, $password)	return true/false
#	is_manager($id)			return: true / false
#	get_post_str()	return $string
#	getFileExtension($str)	return $ext
#	Array2csv($array)		: array to string[csv]
#	csv2array($str)			: CSV to ARRAY
#	Array2String($array)    : array to string	
#	string2Array($string,$separator_1="\t",$separator_2="\n")     : string	to Array
#	decode_number($number) : �N csv �r�� $number �Ѷ}�� �}�C $number���ꤻ��Ƥ�csv
#	decode_ord_number($number) : �N csv �r�� $number �Ѷ}���}�C $number����4��Ƥ�csv
#	decode_smpl_number($number) : �N csv �r�� $number �Ѷ}���}�C $number����5��Ƥ�csv

#	encode_number($number) : �N������ �~�O[��X]�Ǹ�[�|�X]�}�C�令cvs�ɦs�J{[050023,...]
#	get_dept($self_included=1) :  ��login������ �Ǧ^ �Ө����������N��    
#	get_dept_id() :  ��login������ �Ǧ^ �Ө����������N��   �p�G�O su �h�Ǧ^ �����U�Կ��
#	get_manager_dept($dept="") :  ��login������ �Ǧ^ �Ө����������N��    
#	get_sales_dept() :	���X �~�ȳ��� �}�C 
#	get_breakdown($sizing,$qty="") :  �ѶǤJ��sizing [array] ���X�@�� table 
#	get_colorway_qty($qty,$sizing)  �ѶǤJ��qty,sizing[ �Ҭ�array] ���X�@�� $data array
#	show_breakdown($qty,$sizing)   �ѶǤJ��qty,sizing[ �Ҭ�array] ���X�@�� table 
#	show_del_breakdown($qty,$sizing)   �ѶǤJ��qty,sizing[�Ҭ�array] ���X�@�� table 
#	edit_breakdown($sizing,$qty="")  �ѶǤJ��sizing[array] ���X�@�� table 
#	get_usage_breakdown($num_acc,$est,$num_color,$qty) :  
#					�ѶǤJ��$est,$num_color,$qty ���X�@�Ӱ}�C <TD> �r��[$num_lots]
#					$num_color �� td�ƶq, $qty�� array[$num_color],
#					�Ǧ^ �@�հ}�C �� td; $est�� array[$num_acc]
#	lots_choose_line($num_mat,$est,$num_color,$qty) :  
#					�ѶǤJ�� $num_mat,$num_color,$qty ���X�@�Ӱ}�C <TR> �ﶵ��
#					$num_mat,$num_color,$qty �Ҭ� array
#					�Ǧ^ �@�հ}�C �� <tr>; $est�� array[$num_acc]
#	acc_choose_line($num_mat,$est,$num_color,$qty) :  
#					�ѶǤJ�� $num_mat,$num_color,$qty ���X�@�Ӱ}�C <TR> �ﶵ��
#					$num_mat,$num_color,$qty �Ҭ� array
#					�Ǧ^ �@�հ}�C �� <tr>; $est�� array[$num_acc]
#	countDays ($begdate,$enddate)  �p���Ӥ��������� ; �Ǧ^���
#	dayCount ($begdate,$enddate)   �p���Ӥ��������� ; �Ǧ^���
#	extract_su ($monsu)	 �ǤJ�@�� csv�� ��} month��su �� //�ǥX �}�C--
#	getDaysInMonth($month,$year)  ��J�~�Τ� �^�иӤ몺���
#	increceDaysInDate($date,$nDays)  ��J��� �έn�W�[����� �^�мW�᪺���
#	decode_mon_su($number) :�Ncsv�r��$number�Ѷ}���}�C$number���e���X��year+mon[200507]
#      �A�N�}�C�����令�e ��ⳡ��, �A�N�e��key ���value ���}�C   �Ǧ^�}�C
#	decode_mon_yy_su($number) :�Ncsv�r��$number�Ѷ}���}�C$number
#			   				   �N$number�����e�|��ƪ�year,�����G�쪺mon,�βĤ���H�᪺su

#	encode_mon_su($su_array) : �N������su�}�C['200507'==>230,...�令cvs[200507230,...]
#	filter_0_value($Ary) : �N �}�C���Ȭ��s ������ 

#	function only_sales_dept($dept) : �N  DEPT�}�C ���Ȥ��O k �}�Y������ [�D�~�ȳ��]
#	function only_sales_dept2($dept) : �N  DEPT�}�C ���Ȥ��O J �}�Y������ [�D�~�ȳ��2]
#	function related_dept($parm) : 
#	function page_display($i, $j, $tpl) : 
#	function check_authority($i, $j, $job) : 
#	function bubble_sort($array) : ��w�Ƨ�  �j----->�p
#	function get_fy_salary($qty,$worker,$spt) : Monitor�p��uú
#
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_su($s_date,$e_date,$T_su,$year=0)	�p��U�륭��SU��
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_su($s_date,$e_date,$T_su,$year=0)
{
	$ets=explode('-',$s_date);
	$etf=explode('-',$e_date);
	$date_time=countDays($s_date,$e_date);
	if ($date_time == 0) $date_time=1;	
	$day_su=($T_su/$date_time);
	
	if ($ets[1]<>$etf[1] || ($ets[1] == $etf[1] && $ets[0]<>$etf[0]))
	{
		$tmp_day=getDaysInMonth($ets[1],$ets[0]);
		$end_date=$ets[0]."-".$ets[1]."-".$tmp_day;
		$mon_time=countDays($s_date,$end_date);			
		$mon_su=(int)($day_su*$mon_time);
		if ($year > 0 && $year<>$ets[0])
		{
			$msu[0]=$ets[1]."-0";
		}else{
			$msu[0]=$ets[1]."-".$mon_su;
		}
		$str_date=$etf[0]."-".$etf[1]."-0";		
		$mon_time=countDays($str_date,$e_date);		
		$mon_su=(int)($day_su*$mon_time);	
		
		if ($year > 0 && $year<>$etf[0])
		{
			$msu[1]=$etf[1]."-0";
		}else{
			$msu[1]=$etf[1]."-".$mon_su;
		}				

		$j=2;
		if ($etf[0] > $ets[0]) {$etf_tmp=$etf[1]+12;}else{$etf_tmp=$etf[1];}
		for ($i=($ets[1]+1); $i< $etf_tmp; $i++)
		{
			if ($i>12)
			{
				$xm=$i-12;
				$yy=$etf[0];
			}else{
				$xm=$i;
				$yy=$ets[0];
			}
			if ($i>12 && $year > 0 && $year < $etf[0]) break;
			if ($xm < 10){ $mon_mm="0".$xm;}else{$mon_mm=$xm;}
			$tmp_day=getDaysInMonth($mon_mm,$yy);			
			$mon_su=(int)($day_su*$tmp_day);

			$msu[$j]=$mon_mm."-".$mon_su;			
		$j++;
		}
		
	}else{
		$mon_su=(int)$date_time*$day_su;
		$msu[0]=$ets[1]."-".$mon_su;		
	}   
	return $msu;
} // end function


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_su($s_date,$e_date,$T_su,$year=0)	�p��U�륭��SU��
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_su_2($s_date,$e_date,$T_su,$year=0)
{	
	$ets=explode('-',$s_date);
	$etf=explode('-',$e_date);
	$date_time=countDays($s_date,$e_date);
	if ($date_time == 0) $date_time=1;	
	$day_su=($T_su/$date_time);

	if (substr($s_date,0,7) <> substr($e_date,0,7))
	{
		$mon_count = getMonthRange($s_date,$e_date); //�p��ETS~ETF�����X�Ӥ�
		//�p��ETS�Ҧb�����SU
		$end_date=$ets[0]."-".$ets[1]."-".getDaysInMonth($ets[1],$ets[0]); //ETS(Start)�Ҧb������̫�@��
		$mon_time=countDays($s_date,$end_date);			
		$mon_su=(int)($day_su*$mon_time);
		if ($year > 0 && $year<>$ets[0]) $mon_su=0; //�p�⤤���~�����b���w���~����
		$msu[0]=$ets[1]."-".$mon_su;	//�p��ETS(Start)�Ҧb�����SU
	
		//�p��ETS~ETF���������SU
		$tmp_yy =$ets[0];
		for ($i=1; $i<sizeof($mon_count); $i++)
		{
			$tmp_mm = $ets[1] + $i;  //�p��X��M�~
			if ($tmp_mm > 12)
			{
				$tmp_mm =$tmp_mm - 12;
				$tmp_yy++;
			}
			$tmp_day=getDaysInMonth($tmp_mm,$tmp_yy);			
			$mon_su=(int)($day_su*$tmp_day);
			if ($tmp_mm < 10) $tmp_mm="0".$tmp_mm;
			if ($year > 0 && $year<>$tmp_yy) $mon_su = 0; //�p�⤤���~�����b���w���~����
			$msu[$i]=$mon_mm."-".$mon_su;									
		}
		
		//�p��ETF��SU
		$str_date=$etf[0]."-".$etf[1]."-0";		
		$mon_time=countDays($str_date,$e_date);		//�p��ETF�Ӥ��ETF����X��
		$mon_su=(int)($day_su*$mon_time);			
		if ($year > 0 && $year<>$etf[0]) $mon_su=0; //�p�⤤���~�����b���w���~����
		$msu[$i]=$etf[$i]."-".$mon_su;				
	}else{
		$mon_su=(int)$date_time*$day_su;
		$msu[0]=$ets[1]."-".$mon_su;		
	}   
	return $msu;
} // end function



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# ->getMonthRange($s_date,$e_date)	�p��G�Ӥ�����j�X�Ӥ�
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function getMonthRange($s_date,$e_date)
{
	$str = explode ('-',$s_date);
	$end = explode ('-',$e_date);
	$count = ($end[0]-$str[0]) * 12;
	$count = $count +($end[1] - $str[1]);
	return $count;
}



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# make_password($length=8)
# pam : $length  ( default = 8)
# reply $password
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

function make_password($length=8){
    $vowels = 'aeiouy';
    $consonants = 'bdghjlmnpqrstvwxz';
    $password = '';
    $alt = time() % 2;
    srand(time());
    for ($i = 0; $i < $length; $i++) {
        if ($alt == 1) {
            $password .= $consonants[(rand() % 17)];
            $alt = 0;
        } else {
            $password .= $vowels[(rand() % 6)];
            $alt = 1;
        }
    }
    return $password;
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#  display_header($title)
#  ���}�� :  $header_displayed
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function display_header($title){
global $header_displayed ;
if ($header_displayed) return;
$header_displayed++;
include("$layout_dir/header.html");
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#  display_footer()
#  ���}�� :  $footer_displayed
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function display_footer(){
global $footer_displayed ;
if ($footer_displayed) return;
$footer_displayed++;
include("$layout_dir/footer.html");
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#  display_fatal($error)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function display_fatal($error){		// ��@�� error

global $img_url;
display_header("�t�ΰ�����~");
print<<<EOF
<table border=0 cellspacing=0 cellpadding=0 width=600 bgcolor=#ffffff>
<tr><td width=1><img src="$img_url/frameShad-cnr.gif" height=10 width=10></td>
<td><img src="$img_url/frameShad-top.gif" height=10 width=600></td></tr>
<tr><td  background="$img_url/frameShad-left.gif" height=100%><img SRC="$img_url/frameShad-left.gif"  width=10></td>
<td align=center>
	<table border=0 cellpadding=5 width=550>
		<tr><td>
		<font color=red>���~�T��: $error</font>
		<br><br>
		</td></tr></table>
		</td></tr></table>
EOF;
exit();
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#  display_errors($errs)			// errors �}�C $errs[]  �Ω�{��ù��W�����~
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function display_errors($errs){				

  display_header("�{��������~");
  foreach ((array)$errs as $e)
  print "<tr><td valign=top><LI class=err></td><td align=left class=err>$e</td></tr>\n";
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#   get_hidden_vars()		���X post �Ӫ����
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_hidden_vars(){

    global $HTTP_POST_VARS;
    $res = '';
    foreach ($HTTP_POST_VARS as $k=>$v){
      if ($k[0] == '@')
        if (is_array($v)) // array
            foreach ($v as $kk=>$vv)
             $res .= sprintf('<input type=hidden name="%s[]" value="%s">'."\n",
                htmlspecialchars($k), htmlspecialchars($vv));
        else
            $res .= sprintf('<input type=hidden name="%s" value="%s">'."\n",
                htmlspecialchars($k), htmlspecialchars($v));
    }
    return $res;
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#   show_back_fix_link()		�^�W���ץ����~
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function show_back_fix_link() {

	print <<<EOF
        �� <a href="javascript: history.back(-1)">�^�W��</a>  �ץ����~ �C<br><br>
EOF;
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#   show_reload_aft_fixed()		�ץ��᪺ RELOAD PAGE
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function show_reload_aft_fixed() {

    print <<<EOF
�ѨM��A�Ы� <a href="javascript: window.location.reload()">�o��</a> ��s����.<br><br>
EOF;
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#   set_date_from_smarty($prefix, &$vars)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function set_date_from_smarty($prefix, &$vars){
    return $vars[$prefix] = sprintf('%04d-%02d-%02d', 
        $vars[$prefix.  'Year'],
        $vars[$prefix.  'Month'],
        $vars[$prefix.  'Day']
    );
}

################### �H�U �`��###############
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	getDBconnect()
#		�p����Ʈw
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function getDBconnect() {

	$sql = new MySQL();
	if (!$sql->connect($GLOBALS['MYSQL_SERVER'],
						$GLOBALS['MYSQL_USER'],
						$GLOBALS['MYSQL_PASSWD'],
						$GLOBALS['MYSQL_DB'])) {
		return false;
	}
	return $sql;
}


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	getDBconnect()
#		�p����Ʈw
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function getDBconnect2() {

	$sql = new MySQL();
	if (!$sql->connect($GLOBALS['MYSQL_SERVER2'],
						$GLOBALS['MYSQL_USER2'],
						$GLOBALS['MYSQL_PASSWD2'],
						$GLOBALS['MYSQL_DB2'])) {
		return false;
	}
	return $sql;
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	getDBconnectUTF()
#		�p����Ʈw
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function getDBconnectUTF() {

    $sql = new MySQL();
    
	if(empty($_SESSION["link_utf_db"])){ 
        $_SESSION["link_utf_db"] = @mysql_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD']);
        if(!$_SESSION["link_utf_db"]){
            echo ("FATAL:Couldn't connect to db.<br>");
            exit;
        }
    }
	$_SESSION["Select_utf_DB"] = @mysql_select_db('dbo', $_SESSION["link_utf_db"]);
	if(!$_SESSION["Select_utf_DB"]){
        echo ("FATAL:Couldn't connect to db.<br>");
        exit;
    }
    // unset($_SESSION["link_utf_db"]);
    return $_SESSION["link_utf_db"];
}
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	getDBconnectWIPOne()
#		�p����Ʈw
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function getDBconnectWIPOne() {



    /* $sql = new MySQL();
    
	if(empty($_SESSION["link_utf_db"])){ 
        $_SESSION["link_utf_db"] = @mysql_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD']);
        if(!$_SESSION["link_utf_db"]){
            echo ("FATAL:Couldn't connect to db.<br>");
            exit;
        }
    }
	$_SESSION["Select_utf_DB"] = @mysql_select_db('dbo', $_SESSION["link_utf_db"]);
	if(!$_SESSION["Select_utf_DB"]){
        echo ("FATAL:Couldn't connect to db.<br>");
        exit;
    } */
    // unset($_SESSION["link_utf_db"]);
    return $_SESSION["link_utf_db"];
}
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	getDBconnectWIPOneHR()
#		�p����Ʈw
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function getDBconnectWIPOneHR() {

    /* $sql = new MySQL();
    
	if(empty($_SESSION["link_utf_db"])){ 
        $_SESSION["link_utf_db"] = @mysql_connect($GLOBALS['UTF_SERVER'], $GLOBALS['UTF_USER'], $GLOBALS['UTF_PASSWD']);
        if(!$_SESSION["link_utf_db"]){
            echo ("FATAL:Couldn't connect to db.<br>");
            exit;
        }
    }
	$_SESSION["Select_utf_DB"] = @mysql_select_db('dbo', $_SESSION["link_utf_db"]);
	if(!$_SESSION["Select_utf_DB"]){
        echo ("FATAL:Couldn't connect to db.<br>");
        exit;
    } */
    // unset($_SESSION["link_utf_db"]);
    return $_SESSION["link_utf_db"];
}
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	apart_date($mode=0,$date_str=0)
#		
# Detail description:
# @param	
#			$mode=0: mysql��datetime�ন$year,$month,$day,$time��
#			$mode=1 :���{�b��datetime
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function apart_date($mode=0,$date_str=0) {

    if ($mode==0) {
		list($year,$month,$day) = split("-",$date_str);
		$ts = mktime(0,0,0,$month,$day,$year);
		$dt = getdate($ts);		
    }	elseif ($mode==1) {
		$ts = time();
		$dt = getdate();			    
	}
    # ���媺�P���X
	$weekday_c = array("�P����","�P���@","�P���G","�P���T","�P���|","�P����","�P����","�P����"); 
	$dec['year'] = $dt['year'];
	$dec['month'] = $dt['mon'];
	$dec['day'] = $dt['mday'];
	$dec['weekday'] = $dt['weekday'];
	$dec['weekday_c'] = $weekday_c[$dt['wday']];
	$dec['timestamp'] = $ts;
	$dec['date'] = date("Y-m-d",$ts);

	return $dec;
} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	decode_date($mode=0,$date_str=0)
#		
# Detail description:
# @param	
#			$mode=0: mysql��datetime�ন$year,$month,$day,$time��
#			$mode=1 :���{�b��datetime
#			$mode=2: mysql��timestamp�ন$year,$month,$day,$time��
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function decode_date($mode=0,$date_str=0) {

    if ($mode==0) {
		list($date ,$time) = split(" ",$date_str);
		list($year,$month,$day) = split("-",$date);
		list($hour,$minute,$second) = split(":",$time);
		$ts = mktime($hour,$minute,$second,$month,$day,$year);
		$dt = getdate($ts);		
    }	elseif ($mode==1) {
		$ts = time();
		$dt = getdate();			    
	}	elseif ($mode==2) {
	    $year = substr($date_str,0,4);
	    $month = substr($date_str,4,2);
	    $day = substr($date_str,6,2);
	    $hour = substr($date_str,8,2);
	    $minute = substr($date_str,10,2);
	    $second = substr($date_str,12,2);
		$ts = mktime($hour,$minute,$second,$month,$day,$year);
		$dt = getdate($ts);		
	}
    # ���媺�P���X
	$weekday_c = array("�P����", "�P���@", "�P���G", "�P���T", "�P���|", "�P����", "�P����", "�P����"); 
	$dec['year'] = $dt['year'];
	$dec['month'] = $dt['mon'];
	$dec['day'] = $dt['mday'];
	$dec['time'] = $dt['hours'].":".$dt['minutes'].":".$dt['seconds'];
	$dec['hour'] = $dt['hours'];
	$dec['minute'] = $dt['minutes'];
	$dec['second'] = $dt['seconds'];
	$dec['weekday'] = $dt['weekday'];
	$dec['weekday_c'] = $weekday_c[$dt['wday']];
	$dec['timestamp'] = $ts;
	$dec['date_str'] = date("Y-m-d H:i:s",$ts);
	$dec['date'] = date("Y-m-d",$ts);

	return $dec;
} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	su_mon_div($qty, $ratio, $s_date, $f_date)
# Detail description	: �N �ƶq ����su �A���t��Ͳ������ 
#						: �Ǧ^ �}�C ( 200505=>su , 200506=>su, ......
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function su_mon_div($qty, $ratio, $s_date, $f_date)	{
    
    list($s_year,$s_mon,$s_day) = split("-",$s_date);
    list($f_year,$f_mon,$f_day) = split("-",$f_date);
    $days =	countDays($s_date,$f_date);
    $T_su = intval($ratio * $qty);		// �` su ��
    $day_su = intval($T_su/$days);		// �C�鲣�X --(���p)

} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	is_admin_login()	�ˬd�O�_�n�J
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function is_admin_login() {

	$test = $GLOBALS['SCACHE']['ADMIN'];
	if (!is_array($test)) {		return false;	}
    if (!$test['id']) {		return false;    }
	return true;
    
} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	verify_pid($pid)	�ˬd�����Ҧr��
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function verify_pid($pid) {

	if (strlen($pid) != 10) {
		return false;
	}
	$eng = substr($pid,0,1);
	$num = substr($pid,1);
	if (ord('A')>ord($eng) || ord('Z')<ord($eng)) {		return false;	}
	if (strlen($num) !=9) {		return false;	}
	return true;
    
} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	redirect_page($url)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function redirect_page($url) {

	header ("Location: $url"); 
    exit();
} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	cfm_delete($str)			return: true / false
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function cfm_delete($describe, $delete_str) {

//	display_header('�ЦA���T�{�R���H�U���???')
print<<<EOF
<table width=600 border=0 cellspacing=0 cellpadding=5 bgcolor=white>
    <tr>	<td width=30% class=g9 align=right>$describe</td>
            <td class-b9>&delete_str</td>
    </tr>
    <tr>
        <td width=50%><form>
            <input type="hidden" NAME="return" VALUE=1>
            <input type="submit" name="cfmyesbutton" value=" �T�{�R���W�C��ƵL�~ ! " class="button" style="width:100%"></form>
        </td>
        <td width=50%><form>
            <input type="hidden" NAME="return" VALUE=0>
            <input type="submit" name="cfmyesbutton" value=" ���R��!  �^�W�� ! " class="button" style="width:100%"></form>
        </td>
    </tr>
</table>
EOF;
display_footer();

}  // end function cfm_delete

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	is_manager_login($id, $password)	return true/false
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function is_manager_login($id, $password){
	//����config���Ѽ�
	if(is_array($_SESSION['ADMIN']['manager'])){
		foreach($_SESSION['ADMIN']['manager'] as $name => $pass) {
			if (($id == $name) && ($password == $pass)) {
				return true;
			}
		}
	}else{
	}
	return false;	
}  // end is_manager

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	is_manager($id)	return true/false
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function is_manager($id){
    global $manager;  //����config���Ѽ�
	foreach($manager as $name => $pw) {
	  if ($id == $name ){
		  return true;
	  }
	}
	return false;
}  // end is_manager

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	get_post_str()	return $string
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_post_str($HTTP_POST_VARS){
	$str = '';
	$count = 0;
	foreach ($HTTP_POST_VARS AS  $key=>$val  ) {
		if ($val){
			if (!$count){
				$str .= '?'.$key.'='.$val;
			} else {
				$str .= '&'.$key.'='.$val;
			}
		}
		$count++;
	}
	return $str;
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	getFileExtension($str)	return $ext
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    function getFileExtension($str) {
        $i = strrpos($str,".");
        if (!$i) { return ""; }

        $l = strlen($str) - $i;
        $ext = substr($str,$i+1,$l);
        return $ext;
    }

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	Array2csv($array)     : array to string[csv]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function array2csv($array){
		$result = "";
		$result = implode(',',$array);
	return $result;
	}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	csv2array($str)     : CSV to ARRAY
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function csv2array($str){
		$result = "";
		$result = explode(",", $str);
	return $result;
	}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	Array2String($array)     : array to string	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function array2String($array){
		//string or sign that separates values
		$delimiter=",";
		$result = "";
		for ($sp=0;$sp<count($array);$sp++){
			//get the array elements and assign them to a string Value
			$result= $result.$array[$sp].$delimiter;
		}
	return $result;
	}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	string2Array($string,$separator_1="\t",$separator_2="\n")     : string	to Array
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function string2Array($string,$separator_1="\t",$separator_2="\n") {
		$array = explode($separator_2, $string);

		for ($i=0; $i < sizeof($array); $i++){
			$fields = substr_count($array[$i], $separator_1);
			if($fields>0){	//2nd dimension array
				$array[$i] = explode($separator_1, $array[$i]);
			}
		}
		return $array;
	}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	decode_number($number) : �N csv �r�� $number �Ѷ}�� �}�C $number���ꤻ��Ƥ�csv
#      �A�N�}�C�����令�e�G ��| ���ⳡ��, �A�N�e�G��key ��|��value ���}�C 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function decode_number($number) {
		$decoded = array();

		$array = explode(",", $number);

		for ($i=0; $i < sizeof($array); $i++)     {
				$key = substr($array[$i],0,2);
				$val = substr($array[$i],2,4);
				$decoded[$key] = $val;
		}
		return $decoded;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	decode_ord_number($number) : �N csv �r�� $number �Ѷ}�� �}�C $number���� 4 ��Ƥ�csv
#      �A�N�}�C�����令�e1 ��3 ���ⳡ��, �A�N�e1��key ��3��value ���}�C 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function decode_ord_number($number) {
		$decoded = array();
		$array = explode(",", $number);
		
		for ($i=0; $i < sizeof($array); $i++)     {
				$key = substr($array[$i],0,1);
				$val = substr($array[$i],1,3);
				$decoded[$key] = $val;
		}
		return $decoded;
	} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	decode_smpl_number($number) : �N csv �r�� $number �Ѷ}�� �}�C $number���� 5 ��Ƥ�csv
#      �A�N�}�C�����令�e1 ��4 ���ⳡ��, �A�N�e1��key ��3��value ���}�C 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function decode_smpl_number($number) {
		$decoded = array();
		echo $number."<BR>";
		$array = explode(",", $number);

		for ($i=0; $i < sizeof($array); $i++)     {
				$key = substr($array[$i],0,1);
				$val = substr($array[$i],1,4);
				$decoded[$key] = $val;
				echo $key."=>".$val."<BR>";
		}
		return $decoded;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	encode_number($number) : �N������ �~�O[��X]�Ǹ�[�|�X] �}�C�令 cvs�ɦs�J{[050023,...]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function encode_number($num_array) {
		$cvs_str = "";
			while(list($key,$value) = each($num_array)	){
				
				if(is_numeric ($key) || $key){      // �p�G�O �Ū� �|����Ӫ�key ��Ĥ@�Ӱ}�C��
					$cvs_str = $cvs_str.$key.$value.",";
				}
	
				$cvs_str = $cvs_str.$key.$value.",";
			}//while

//		$cvs_str = substr($cvs_str,2);    // �h�Y
		$cvs_str = substr($cvs_str,0,-1); // �h��
		return $cvs_str;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	get_dept($self_included=1) :  ��login������ �Ǧ^ �Ө����������N��    
#		�p�GUSER�������N����X�O0 �h�Ǧ^ �e�X�������������C��U�Կ�� KA,KB,KC....
#		�p�G�O GM, SA �h�Ǧ^������������ ARRAY
#		���~ ��l��������USER�h �H�ӳ�����DEPT�Ǧ^(���OARRAY)
#		�p�G�]�t�ۤv�����O�h self_included =1 (default)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_dept($self_included=1) {

		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];

		// �p�G�O manager �i�J��...[ �t�κ޲z�� �άO super user (�󳡪��D��)....]
		if( ($user_dept == "GM") || ($user_dept == "SA") ){  
			// ���X�����������O�N��
			$dept_def = $GLOBALS['dept']->get_fields('dept_code');  
			sort($dept_def);
			$dept_id = $GLOBALS['arry2']->select($dept_def,"","PHP_dept_code","select","");

		}else{
					
			$z_key = substr($user_dept,1,1);
			$key = substr($user_dept,0,1);

			if($z_key == '0'){   //�����X��X = 0��

				$depts = $GLOBALS['dept']->get_fields('dept_code');  

				if(!$self_included){  //�p�G�n�h���ۤv�������O��----
					for($i=0;$i<count($depts);$i++){
						if ((substr($depts[$i],0,1) == $key) && ($depts[$i] <> $key.'0')){
							$dept_arry[] = $depts[$i];
						}
					}
				}else{
					for($i=0;$i<count($depts);$i++){
						if (substr($depts[$i],0,1) == $key){
							$dept_arry[] = $depts[$i];
						}
					}
				}

				sort($dept_arry);
				$dept_id = $GLOBALS['arry2']->select($dept_arry,"","PHP_dept_code","select",""); 
			}else{
				$dept_id = $_SESSION['SCACHE']['ADMIN']['dept'];
			}
		}

		return $dept_id;

	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	get_dept_id() :  ��login������ �Ǧ^ �Ө����������N��    
#				�p�G�O J0, K0  �h�Ǧ^ �ӷ~�ȳ����������U�Կ�� KA,KB,KC....�� J1,J2...
#				�p�G�O GM, SA �h�Ǧ^������������ ARRAY
#				���~ ��l��������USER�h �H�ӳ�����DEPT�Ǧ^(���OARRAY)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_dept_id() {
		// �p�G�O manager �i�J��...[ �t�κ޲z�� �άO super user (�󳡪��D��)....]

		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
		$dept_key = substr($user_dept,0,1);

		// 200604027 update		
		if($user_dept == "K0"){   //�~�ȳ����O K ���Ҧ�select���
			$dept_def = $GLOBALS['dept']->get_fields('dept_code');  
			$dept_def = array_filter($dept_def,'only_sales_dept'); 
			sort($dept_def);
			$dept_id = $GLOBALS['arry2']->select($dept_def,"","PHP_dept_code","select",""); 
		}elseif($user_dept == "J0"){  //�~�ȳ����O J ���Ҧ�select���
			$dept_def = $GLOBALS['dept']->get_fields('dept_code');  
			$dept_def = array_filter($dept_def,'only_sales_dept2'); 
			sort($dept_def);
			$dept_id = $GLOBALS['arry2']->select($dept_def,"","PHP_dept_code","select","");
		}elseif($user_dept == "T0"){  //�~�ȳ����O H ���Ҧ�select���
			$dept_def = $GLOBALS['dept']->get_fields('dept_code');  
			$dept_def = array_filter($dept_def,'only_sales_dept5'); 
			sort($dept_def);
			$dept_id = $GLOBALS['arry2']->select($dept_def,"","PHP_dept_code","select","");
		}elseif(($user_dept == "GM") || ($user_dept == "SA")){  // ���X�����������O�N��
			$dept_def = $GLOBALS['dept']->get_fields('dept_code');  
			
			sort($dept_def);
			$dept_id = $GLOBALS['arry2']->select($dept_def,"","PHP_dept_code","select","");
		}else{  //�䥦�� �ҥH�ӳ����� id ��id
			$dept_id = $_SESSION['SCACHE']['ADMIN']['dept'];
		}

		
		return $dept_id;

	} // end func
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# get_dept_code() :
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_dept_code($str='') {

	$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];


	$dept_def = $GLOBALS['dept']->get_fields('dept_code');  
	$dept_def = array_filter($dept_def,'only_sales_dept8'); 
	sort($dept_def);
	$dept_code = $GLOBALS['arry2']->select($dept_def,@$_GET['PHP_dept'],"PHP_dept","select",$str);

	
	return $dept_code;

} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# is_dept() :
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

function is_dept($dept){
	$arr_ddept = array( 'HJ','LY','CF','C1','V1' );
	if( in_array( $dept , $arr_ddept ) )
		return true;
	else
		return false;
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	get_manager_dept($dept="") :  ��login������ �Ǧ^ �Ө����������N��    
#		�p�G�O GM �� SA �� K0 �h�Ǧ^ �����U�Կ��  // ��l�ҶǦ^�ӳ���ID
#		�~�ȳ����h�u�C�X �~�ȳ������----
#		��X���Ҫ�[����]--------------------�Ǧ^ ARRAY ��ӭ� dept_select, dept_id
#			$dept���w�������]�w ... �i���]��""; �h���̤W�@��..
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# 2014/10/20 Mode ���g�k�L�k���L���� ��� get_dept_group ���N
	function get_manager_dept($dept="") {
		// �p�G�O manager �i�J��...[ �t�κ޲z�� �άO GM (�󳡪��D��) �άO�~�ȳ���....]

		$user_dept = $GLOBALS['SCACHE']['ADMIN']['dept'];
		// ���X�����������O�N��
		$dept_def = $GLOBALS['dept']->get_fields('dept_code');   

		if (($user_dept == "SA") || ($user_dept == "GM") || ($user_dept == "PM")) { 
			sort($dept_def);
			$mgr['dept_select'] = $GLOBALS['arry2']->select($dept_def,"$dept","PHP_dept_code","select","");  //�����Oselect���
			$mgr['dept_id'] = '';
			$mgr['dept_ary'] = $dept_def;
		}elseif($user_dept == "K0"){
			$dept_def = array_filter($dept_def,'only_sales_dept');  // �p�G�O�~��1�D�޶i�J �h�L�o���䥦����
			sort($dept_def);
			$mgr['dept_select'] = $GLOBALS['arry2']->select($dept_def,"$dept","PHP_dept_code","select","");  //�����Oselect���
			$mgr['dept_id'] = '';
			$mgr['dept_ary'] = $dept_def;
		}elseif($user_dept == "J0"){
			$dept_def = array_filter($dept_def,'only_sales_dept2');  // �p�G�O�~��2�D�޶i�J �h�L�o���䥦����
			sort($dept_def);
			$mgr['dept_select'] = $GLOBALS['arry2']->select($dept_def,"$dept","PHP_dept_code","select","");  //�����Oselect���
			$mgr['dept_id'] = '';
			$mgr['dept_ary'] = $dept_def;
		}elseif ($user_dept == "T0"){
			$dept_def = array_filter($dept_def,'only_sales_dept5');  // �p�G�O�~��1�D�޶i�J �h�L�o���䥦����
			sort($dept_def);
			$mgr['dept_select'] = $GLOBALS['arry2']->select($dept_def,"$dept","PHP_dept_code","select","");  //�����Oselect���
			$mgr['dept_id'] = '';
			$mgr['dept_ary'] = $dept_def;
		}elseif ($user_dept == "D0"){
			$dept_def = array_filter($dept_def,'only_sales_dept6');  // �p�G�O�~��1�D�޶i�J �h�L�o���䥦����
			sort($dept_def);
			$mgr['dept_select'] = $GLOBALS['arry2']->select($dept_def,"$dept","PHP_dept_code","select","");  //�����Oselect���
			$mgr['dept_id'] = '';
			$mgr['dept_ary'] = $dept_def;
		}elseif ($user_dept == "C0"){
			$dept_def = array_filter($dept_def,'only_sales_dept10');  // �p�G�O�~��1�D�޶i�J �h�L�o���䥦����
			sort($dept_def);
			$mgr['dept_select'] = $GLOBALS['arry2']->select($dept_def,"$dept","PHP_dept_code","select","");  //�����Oselect���
			$mgr['dept_id'] = '';
			$mgr['dept_ary'] = $dept_def;
		}else{
			$mgr['dept_id'] = $_SESSION['SCACHE']['ADMIN']['dept'];
			$mgr['dept_select'] = '';
			$mgr['dept_ary'] = '';
		}
		return $mgr;
	} // end func
	
	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	get_sales_dept() :	���X �~�ȳ��� �}�C 
#				�H�i�J�̨��� ���� �~�ȳ����� id�}�C(�ثe�]����ӷ~�ȳ�: K,J )
#				�Ҧ����~�ȳ� �Ҥ��t�� K0 , J0	
#				�Ǧ^ �~�ȳ����}�C   
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

# Mode �w�q����ܳ��� 2014/10/17
function get_sales_dept() {

    $dept_key = substr($GLOBALS['SCACHE']['ADMIN']['dept'],0,1);
    $dept_def = $GLOBALS['dept']->get_fields('dept_code');   // ���X�����������O�N��

    if ($dept_key=='K'){		 // �p�G�O K �������H...
        $dept_def = array_filter($dept_def,'only_sales_dept'); 
        sort($dept_def);
        array_shift($dept_def);   // �]�� sort �� K0 �û��|�b�̫e��....
    }elseif($dept_key=='J'){	 // �p�G�O J �������H...
        $dept_def = array_filter($dept_def,'only_sales_dept2'); 			
        sort($dept_def);
        array_shift($dept_def);   // �]�� sort �� J0 �û��|�b�̫e��....
    }elseif($dept_key=='H'){	 // �p�G�O H �������H...
        $dept_def = array("HJ","H0");						
        sort($dept_def);
        array_shift($dept_def);   // �]�� sort �� H0 �û��|�b�̫e��....
    }elseif($dept_key=='L'){	 // �p�G�O H �������H...
        $dept_def = array("LY","L0");							
        sort($dept_def);
        array_shift($dept_def);   // �]�� sort �� H0 �û��|�b�̫e��....
    }elseif($dept_key == 'T'){
        $dept_def = array("TK","T0"); 
        sort($dept_def);
        array_shift($dept_def);   // �]�� sort �� K0 �û��|�b�̫e��....		
    }elseif($dept_key == 'D'){
        $dept_def = array("DA","D0"); 
        sort($dept_def);
        array_shift($dept_def);   // �]�� sort �� D0 �û��|�b�̫e��....	
    }elseif($dept_key == 'C'){
        $dept_def = array("CF","C0"); 
        sort($dept_def);
        array_shift($dept_def);   // �]�� sort �� C0 �û��|�b�̫e��....
    }else{		// �䥦 ���O�~�ȳ������H�� �N�t�\�Ҧ����~��
        $dept_k =  array_filter($dept_def,'only_sales_dept'); 					
        sort($dept_k);
        array_shift($dept_k);   // �]�� sort �� K0 �û��|�b�̫e��....
        $dept_L =  array("LY","L0"); 					
        sort($dept_L);
        array_shift($dept_L);   // �]�� sort �� K0 �û��|�b�̫e��....
        $dept_j =  array_filter($dept_def,'only_sales_dept2'); 
        sort($dept_j);
        array_shift($dept_j);   // �]�� sort �� J0 �û��|�b�̫e��....						
        $dept_h =  array("HJ","H0"); 				
        sort($dept_h);
        array_shift($dept_h);   // �]�� sort �� H0 �û��|�b�̫e��....
        $dept_T =  array("TK","T0"); 					
        sort($dept_T);
        array_shift($dept_T);   // �]�� sort �� T0 �û��|�b�̫e��....
        #M11010501
        $dept_D =  array("DA","D0"); 					
        sort($dept_D);
        array_shift($dept_D);   // �]�� sort �� D0 �û��|�b�̫e��....
        $dept_C =  array("CF","C0"); 					
        sort($dept_C);
        array_shift($dept_C);   // �]�� sort �� C0 �û��|�b�̫e��....
        $dept_def = array_merge( $dept_k , $dept_j , $dept_h , $dept_L , $dept_T , $dept_D , $dept_C );
    }
    
    $dept_def = array_filter($dept_def,'only_sales_dept_mode');
    // sort($dept_def);

    return $dept_def; //�Ǧ^�}�C
} // end func



# Mode �w�q���d�߳��� 2014/10/17
function get_dept_group() {

    $dept_key = substr($GLOBALS['SCACHE']['ADMIN']['dept'],0,1);

    if ( $dept_key == 'K' ) {
        $dept_def = array( 'KA' , 'KB' , 'J1' , 'LY' , 'PO' );
    }elseif( $dept_key == 'J' ) {
        $dept_def = array( 'KA' , 'KB' , 'J1' , 'LY' , 'PO' );
    }elseif($dept_key == 'H' ) {
        $dept_def = array( 'HJ' );
    }elseif($dept_key == 'L' ) {
        $dept_def = array( 'LY' , 'KA' , 'KB' , 'J1' , 'DA' );
    }elseif($dept_key == 'D'){
        $dept_def = array( 'DA' );
    }elseif($dept_key == 'C'){
        $dept_def = array( 'CF' , 'DA' );
    } else {
        $dept_def = $GLOBALS['dept']->get_fields( 'dept_code' );
    }

    return $dept_def; //�Ǧ^�}�C

} // end func



# Mode �w�q���d�ߤu�t 2014/10/20
function get_factory_group() {

    $dept_key = substr($GLOBALS['SCACHE']['ADMIN']['dept'],0,1);

    if ( $dept_key == 'K' ) {
        $dept_def = array( 'HJ' , 'LY' , 'CF' );
    }elseif( $dept_key=='J' ) {
        $dept_def = array( 'HJ' , 'LY' , 'CF' );
    }elseif($dept_key=='H') {
        $dept_def = array( 'HJ' );
    }elseif($dept_key=='L') {
        $dept_def = array( 'LY' );
    }elseif($dept_key == 'D'){
        $dept_def = array( 'HJ' , 'LY' , 'CF' );
    }elseif($dept_key == 'C'){
        $dept_def = array( 'CF' );
    } else {
        $dept_def = array( 'HJ' , 'LY' , 'CF' );
    }

    return $dept_def; //�Ǧ^�}�C

} // end func



# Mode �w�q���d�� Team 2014/10/20
function get_team_group() {

    $dept_key = substr($GLOBALS['SCACHE']['ADMIN']['dept'],0,1);

    if ( $dept_key == 'K' ) {
        $dept_def = array( 'KA' , 'KB' , 'J1' , 'LY' , 'PO' );
    }elseif( $dept_key=='J' ) {
        $dept_def = array( 'KA' , 'KB' , 'J1' , 'LY' , 'PO' );
    }elseif($dept_key=='H') {
        $dept_def = array( 'HJ' );
    }elseif($dept_key=='L') {
        $dept_def = array( 'LY' );
    }elseif($dept_key == 'D'){
        $dept_def = array( 'DA' );
    }elseif($dept_key == 'C'){
        $dept_def = array( 'CF' );
    } else {
        $dept_def = $GLOBALS['dept']->get_fields('dept_code');
    }

    return $dept_def; //�Ǧ^�}�C

} // end func



# Mode �w�q���d�߳��� 2014/10/17
function if_factory() {

    $dept = $GLOBALS['SCACHE']['ADMIN']['dept'];

    $depr_arr = array( 'HJ' , 'LY' , 'CF' );

    return in_array( $dept , $depr_arr );

} // end func

function is_factory($dept) {

    $depr_arr = array( 'HJ' , 'LY' , 'CF' );

    return in_array( $dept , $depr_arr );

} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	get_sales_dept() :	���X �~�ȳ��� �}�C 
#				�H�i�J�̨��� ���� �~�ȳ����� id�}�C(�ثe�]����ӷ~�ȳ�: K,J )
#				�Ҧ����~�ȳ� �Ҥ��t�� K0 , J0	
#				�Ǧ^ �~�ȳ����}�C   
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_sales_dept2() {

		$dept_key = substr($GLOBALS['SCACHE']['ADMIN']['dept'],0,1);
		$dept_def = $GLOBALS['dept']->get_fields('dept_code');   // ���X�����������O�N��
		
		if ($dept_key=='K'){		 // �p�G�O K �������H...
			$dept_def = array_filter($dept_def,'only_sales_dept'); 
			sort($dept_def);
			array_shift($dept_def);   // �]�� sort �� K0 �û��|�b�̫e��....
		}elseif($dept_key=='J'){	 // �p�G�O J �������H...
			$dept_def = array_filter($dept_def,'only_sales_dept2'); 			
			sort($dept_def);
			array_shift($dept_def);   // �]�� sort �� J0 �û��|�b�̫e��....
		}elseif($dept_key=='H'){	 // �p�G�O H �������H...
			$dept_def = array("HJ","H0");						
			sort($dept_def);
			array_shift($dept_def);   // �]�� sort �� H0 �û��|�b�̫e��....
		}elseif($dept_key=='L'){	 // �p�G�O H �������H...
			$dept_def = array("LY","L0");							
			sort($dept_def);
			array_shift($dept_def);   // �]�� sort �� H0 �û��|�b�̫e��....
		}elseif($dept_key == 'T'){
			$dept_def = array("TK","T0"); 
			sort($dept_def);
			array_shift($dept_def);   // �]�� sort �� K0 �û��|�b�̫e��....		
		}elseif($dept_key == 'D'){
			#M11010501
			$dept_def = array("DA","D0"); 
			sort($dept_def);
			array_shift($dept_def);   // �]�� sort �� D0 �û��|�b�̫e��....
		}else{		// �䥦 ���O�~�ȳ������H�� �N�t�\�Ҧ����~��
			$dept_k =  array_filter($dept_def,'only_sales_dept'); 					
			sort($dept_k);
			array_shift($dept_k);   // �]�� sort �� K0 �û��|�b�̫e��....
			$dept_L =  array("LY","L0"); 					
			sort($dept_L);
			array_shift($dept_L);   // �]�� sort �� K0 �û��|�b�̫e��....
			$dept_j =  array_filter($dept_def,'only_sales_dept2'); 
			sort($dept_j);
			array_shift($dept_j);   // �]�� sort �� J0 �û��|�b�̫e��....						
			$dept_h =  array("HJ","H0"); 				
			sort($dept_h);
			array_shift($dept_h);   // �]�� sort �� H0 �û��|�b�̫e��....
			$dept_T =  array("TK","T0"); 					
			sort($dept_T);
			array_shift($dept_T);   // �]�� sort �� T0 �û��|�b�̫e��....
			#M11010501
			$dept_D =  array("DA","D0"); 					
			sort($dept_D);
			array_shift($dept_D);   // �]�� sort �� D0 �û��|�b�̫e��....

			$dept_def = array_merge($dept_k, $dept_j, $dept_h, $dept_L, $dept_T, $dept_D);
		}
		return $dept_def; //�Ǧ^�}�C
	} // end func
	

    
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	get_sales_dept() :	���X �~�ȳ��� �}�C 
#				�H�i�J�̨��� ���� �~�ȳ����� id�}�C(�ثe�]����ӷ~�ȳ�: K,J )
#				�Ҧ����~�ȳ� �Ҥ��t�� K0 , J0	
#				�Ǧ^ �~�ȳ����}�C   
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_full_sales_dept() {

		$dept_def = $GLOBALS['dept']->get_fields('dept_code');   // ���X�����������O�N��
		
			$dept_k =  array_filter($dept_def,'only_sales_dept'); 					
			sort($dept_k);
			array_shift($dept_k);   // �]�� sort �� K0 �û��|�b�̫e��....
			$dept_L =  array("LY","L0"); 					
			sort($dept_L);
			array_shift($dept_L);   // �]�� sort �� K0 �û��|�b�̫e��....
			$dept_j =  array_filter($dept_def,'only_sales_dept2'); 
			sort($dept_j);
			array_shift($dept_j);   // �]�� sort �� J0 �û��|�b�̫e��....						
			$dept_h =  array("HJ","H0"); 				
			sort($dept_h);
			array_shift($dept_h);   // �]�� sort �� H0 �û��|�b�̫e��....
			$dept_T =  array("TK","T0"); 					
			sort($dept_T);
			array_shift($dept_T);   // �]�� sort �� T0 �û��|�b�̫e��....
			#M11010501
			$dept_D =  array("DA","D0"); 					
			sort($dept_D);
			array_shift($dept_D);   // �]�� sort �� T0 �û��|�b�̫e��....

			$dept_def = array_merge($dept_k, $dept_j, $dept_h, $dept_L, $dept_T, $dept_D);
		return $dept_def; //�Ǧ^�}�C
	} // end func	
	
	#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	get_sales_dept() :	���X �~�ȳ��� �}�C 
#				��i�J�̬OSU�Τ��O�~�ȳ�����,�C�X�Ҧ��~�ȳ���ID,
#				�_�h,���o�i�J�̩��ݳ���ID
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_view_dept() {

		$team=$GLOBALS['SCACHE']['ADMIN']['team_id'];
		$dept_def = $GLOBALS['dept']->get_fields('dept_code');   // ���X�����������O�N��
		$dept_key = substr($GLOBALS['SCACHE']['ADMIN']['dept'],0,1);
		if ($team=="SU" || $dept_key<>'K' || $dept_key<>'J' || $dept_key<>'H' || $dept_key<>'L' || $dept_key<>'T' || $dept_key<>'D')
		{
			$dept_k =  array_filter($dept_def,'only_sales_dept'); 					
			sort($dept_k);
			array_shift($dept_k);   // �]�� sort �� K0 �û��|�b�̫e��....
			$dept_j =  array_filter($dept_def,'only_sales_dept2'); 
			sort($dept_j);
			array_shift($dept_j);   // �]�� sort �� J0 �û��|�b�̫e��....						
			$dept_t =  array_filter($dept_def,'only_sales_dept5'); 					
			sort($dept_t);
			array_shift($dept_t);   // �]�� sort �� T0 �û��|�b�̫e��....

			$dept_h =  array_filter($dept_def,'only_sales_dept3'); 				
			sort($dept_h);
			array_shift($dept_h);   // �]�� sort �� H0 �û��|�b�̫e��....
			$dept_def = array_merge($dept_k, $dept_j, $dept_h, $dept_t);
			$dept_def = $GLOBALS['arry2']->select($dept_def,"","PHP_dept_code","select",""); 
		}else{
			$dept_def=$GLOBALS['SCACHE']['ADMIN']['dept'];
		}
		return $dept_def; //�Ǧ^�}�C
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	get_breakdown($sizing,$qty="") :  �ѶǤJ��sizing [array] ���X�@�� table 
#						�W���ئT���Y  �U�C�� input�J�Ǧ^���table�� html
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_breakdown($sizing,$qty="") {

	$html = "";
	$html = $html." <tr>\n";
	$html = $html." <td class=ylw9 align=center bgcolor=#333333>colorway</td>\n";
	
	for($i=0;$i<count($sizing);$i++){
		$html = $html." <td bgcolor=#333333 class=ylw9 align=center>".$sizing[$i]."</td>\n";
	}
	
	$html = $html."</tr>\n";
	$html = $html."<tr>";
	$html = $html." <td class=grn9 align=center bgcolor=#cccccc><input type='text' name='PHP_colorway' size=16 class=select style='text-transform:uppercase'></td>";

	for($i=0;$i<count($sizing);$i++){
		 $html = $html." <td class=b9 align=center bgcolor=#999999>\n";
		if (!$qty){
		 $html = $html."   <input type='text' name=\"PHP_qty[".$i."]\" size=1 class=select style='text-align:center'></td>\n";
		}else{
		 $html = $html."   <input type='text' name=\"PHP_qty[".$i."]\" value='".$qty[$i]."' size=1 class=select style='text-align:center'></td>\n";
		}
	}
	$html = $html."</tr>\n";
	
	return $html;
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	get_colorway_qty($qty,$sizing)  �ѶǤJ��qty,sizing[ �Ҭ�array] ���X�@�� $data array
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_colorway_qty($qty,$sizing) {
			$data = array();
			$num_size = count($sizing);
			$num_color= count($qty);
			$total_qty =0;
			$colorway = array();
			$colorway_qty = array();
	  if ($num_color){

		for ($j=0;$j<$num_color;$j++){
			$data[$j][0] = $qty[$j]['colorway'];
			$colorway_qty[$j] =0;
			array_push($colorway,$qty[$j]['colorway']);   // �[�J ��զW�٪��}�C $colorway

			 $T_qty = explode(',',$qty[$j]['qty']);
			for($i=0;$i<$num_size;$i++){
				array_push($data[$j],$T_qty[$i]);
				$total_qty = $total_qty + $T_qty[$i];
				// �[�J ����`�ƶq���}�C $colorway_qty
				$colorway_qty[$j] = $colorway_qty[$j] + $T_qty[$i];  
			}
		}
	  } else {
		  if($num_size){
				$data[0][0] = "no record..";
				for($i=0;$i<$num_size;$i++){
					array_push($data[0],'N/A');
				}
		  }else{
			  $data[0] = 'NO records';
		  }
			$total_qty = '0';
	  }
	$reply['data'] = $data;
	$reply['total'] = $total_qty;
	$reply['colorway'] = $colorway;
	$reply['colorway_qty'] = $colorway_qty;
	return $reply;

	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	show_breakdown($qty,$sizing)   �ѶǤJ��qty,sizing[ �Ҭ�array] ���X�@�� table 
#						�W���ئT���Y  �U�C�� ���T���ƶq      �Ǧ^���table�� html
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function show_breakdown($qty,$sizing,$base_size='',$etd_value='',$etdid='',$remark='',$p_id='') {

    $num_size = count($sizing);
    $span_col = $num_size +1;
    $num_color= count($qty);
    $html = "";
    $tmp=0;
    $total_qty =0;
    
    $p_id_arr = array();

    if ($num_color){
        $html = $html." <tr>\n";
        $ori_etd_value = $etd_value."_".$etdid;
        $alt_etd_value ='[ETD:'.$etd_value.']colorway';
                                                        //morial�s�W
        if($etd_value) $etd_value ='<small>[ETD:'.$etd_value.']<BR>';
        
        $html = $html." <td style=cursor:pointer class=ylw9 align=center bgcolor=#376951 Mid=pid pid='".$p_id."' id='etd$ori_etd_value' >$etd_value colorway</td>\n";
        //morial�s�W
        /* $html = $html." <input type='hidden' class ='ylw9_etd$ori_etd_value' Value='".$mks."'>\n"; */
        $html = $html." <td bgcolor=#376951 class=back align=center>".$remark."</td>\n";
        for($i=0;$i<$num_size;$i++){
            if ( $sizing[$i] == $base_size)
            {
                $html = $html." <td bgcolor=#333333 class=ylw9 align=center><font color='#4EFF00'><b><i>".$sizing[$i]."</i></b></font></td>\n";
            }else{
                $html = $html." <td bgcolor=#333333 class=ylw9 align=center>".$sizing[$i]."</td>\n";
            }
            $siz_total[$i] = 0;
        }
        $html = $html." <td bgcolor=#333333 class=ylw9 align=center><b><i>SUM</i></b></td>\n";

        $html = $html." </tr>\n";			
        
        for ($j=0;$j<$num_color;$j++){
            $html = $html." <tr>\n";

            $html = $html." <td class=grn9 align=center bgcolor=#EEEEEE colspan=2 id='breakdown' pid='".$p_id."'>".$qty[$j]['colorway']."&nbsp;</td>";

            $T_qty = explode(',',$qty[$j]['qty']);
            
            $_SESSION['P_ID'][] = $p_id;
            for($i=0;$i<$num_size;$i++){
                $html = $html." <td class=bgdy9 align=center id='breakdown' pid='".$p_id."'>&nbsp;".$T_qty[$i]."</td>\n";
                $html = $html." <input type='hidden' name=\"bom_size[".$j."][".$sizing[$i]."]\" Value='".$T_qty[$i]."'>\n";
                $total_qty = $total_qty + $T_qty[$i];
                $tmp = $tmp +$T_qty[$i];
                $siz_total[$i] = $siz_total[$i] + $T_qty[$i]; 
            }
            $html = $html." <td class=b9 align=right bgcolor=#EEEEEE id='breakdown' pid='".$p_id."'>&nbsp;<b><i>".Number_format($tmp)."</i></b></td>\n";			
            $html = $html."</tr>\n";
            $html = $html." <input type='hidden' name='bom_size[".$j."][cut_ttl]' Value='".$tmp."'>\n";
            $tmp=0;
        }

        $html = $html." <td class=grn9  align=center bgcolor=#EEEEEE colspan=2 id='breakdown' pid='".$p_id."'>TOTAL&nbsp;</td>";
        for ($x=0; $x<sizeof($siz_total); $x++)
        {
            $html = $html." <input type='hidden' name=\"bom_size[".$j."][".$sizing[$x]."]\" Value='".$siz_total[$x]."'>\n";
            
            if ($siz_total[$x] == 0){$siz_ttl='&nbsp;';}else{$siz_ttl=Number_format($siz_total[$x]);}
            $html = $html." <td class=b9 align=center bgcolor=#EEEEEE id='breakdown' pid='".$p_id."'><i>".$siz_ttl."</i></td>\n";
            
        }
        //$html = $html." <td class=bgdy9 align=center colspan=".$i." bgcolor=#EEEEEE>&nbsp;</td>\n";
        $html = $html." <td class=b9 align=right bgcolor=#EEEEEE id='breakdown' pid='".$p_id."'>&nbsp;<b><i>".Number_format($total_qty)."<i/></b></td>\n";
        $html = $html." <input type='hidden' name='bom_size[".$j."][cut_ttl]' Value='".$total_qty."'>\n";
        //$siz_total[] = $total_qty;
    } else {
    $html = $html." <tr><td class=bgdy9 align=center colspan=".$span_col.">No Colorway data</td></tr>";	  
    }

    $reply['total'] = $total_qty;
    $reply['html'] = $html;
    $reply['size_ttl'] = $siz_total;
    return $reply;
} // end func

	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	show_breakdown($qty,$sizing)   �ѶǤJ��qty,sizing[ �Ҭ�array] ���X�@�� table 
#						�W���ئT���Y  �U�C�� ���T���ƶq      �Ǧ^���table�� html
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function breakdown_cfm_edit($qty,$sizing,$base_size='') {
			$num_size = count($sizing);
			$span_col = $num_size +1;
			$num_color= count($qty);
			$html = "";
			$tmp=0;
			$total_qty =0;
	  if ($num_color){
			$html = $html." <tr>\n";
			$html = $html." <td class=ylw9 align=center bgcolor=#333333>colorway</td>\n";
		for($i=0;$i<$num_size;$i++){
			if ( $sizing[$i] == $base_size)
			{
				$html = $html." <td bgcolor=#333333 class=ylw9 align=center><font color='#4EFF00'><b><i>".$sizing[$i]."</i></b></font></td>\n";
			}else{
				$html = $html." <td bgcolor=#333333 class=ylw9 align=center>".$sizing[$i]."</td>\n";
			}
		}
			$html = $html." <td bgcolor=#333333 class=ylw9 align=center>SUM</td>\n";
			$html = $html." </tr>\n";			
			
		for ($j=0;$j<$num_color;$j++){
			$html = $html." <tr>\n";

			 $html = $html." <td class=grn9 align=center>".$qty[$j]['colorway']."&nbsp;</td>";

			 $T_qty = explode(',',$qty[$j]['qty']);
			for($i=0;$i<$num_size;$i++){
				$html = $html." <td class=bgdy9 align=center bgcolor=#EEEEEE>&nbsp;<input  type='text' name='cq$j$i' size=1 class=select value='".$T_qty[$i]."' style=text-align:center onchange=count_qty('$j','$num_size')></td>\n";
				$total_qty = $total_qty + $T_qty[$i];
				$tmp = $tmp +$T_qty[$i];
			}
			$html = $html." <td class=bgdy9 align=center bgcolor=#838383>&nbsp;<input  type='label' name='cst$j' size=3 value='".$tmp."' readonly style=text-align:right ></td>\n";			
			$html = $html."</tr>\n";
			$html = $html." <input type='hidden' name='cid".$j."' Value='".$qty[$j]['id']."'>\n";
			$tmp=0;
		}
	  } else {
		$html = $html." <tr><td class=bgdy9 align=center colspan=".$span_col.">No Colorway data</td></tr>";
	  }
	$reply['total'] = $total_qty;
	$reply['html'] = $html;

	return $reply;
	} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	show_del_breakdown($qty,$sizing)   �ѶǤJ��qty,sizing[�Ҭ�array] ���X�@�� table 
#							�W���ئT���Y  �U�C�� ���T���ƶq      �Ǧ^���table�� html
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function show_del_breakdown($qty,$sizing,$back_str,$base_size='',$enable_delete,$wiqty_sum,$scheduleqty,$user_type="sample") {
	// print_r($user_type);
	// exit;
			$num_size = count($sizing);
			$span_col = $num_size +2;
			$num_color= count($qty);
			$html = "";
	  if ($num_color){
			$html = $html." <tr>\n";
			$html = $html." <td class=ylw9 align=center bgcolor=#333333>colorway</td>\n";
		for($i=0;$i<$num_size;$i++){
			if ( $sizing[$i] == $base_size)
			{
				$html = $html." <td bgcolor=#333333 class=ylw9 align=center><font color='#4EFF00'><b><i>".$sizing[$i]."</i></b></font></td>\n";
			}else{
				$html = $html." <td bgcolor=#333333 class=ylw9 align=center>".$sizing[$i]."</td>\n";
			}
		}
			$html = $html." <td bgcolor=#cccccc class=ssign align=center><b>SUM</b></td>\n";
			$html = $html." <td bgcolor=#cccccc class=ssign align=center width=22><b>del</b></td>\n";
			$html = $html." </tr>\n";

		for ($j=0;$j<$num_color;$j++){
			$html = $html." <tr id=$j bgcolor='#ffffff' onmouseover=\"bgColor='#66ffff'\" onmouseout=\"bgColor='#ffffff'\">\n";

			$html = $html." <td class=grn9 align=center bgcolor=#eeeeee>".$qty[$j]['colorway']."&nbsp;</td>";

			 $T_qty = explode(',',$qty[$j]['qty']);
			 $tal_qty = 0;
			for($i=0;$i<$num_size;$i++){
				$html = $html." <td class=bgdy9 align=center >&nbsp;".$T_qty[$i]."</td>\n";
				$tal_qty += $T_qty[$i];
			}
			$html = $html." <td class=ssign align=center bgcolor=#EEEEEE>&nbsp;".$tal_qty."</td>\n";
			$html = $html." <td bgcolor=#eeeeee class=ylw9 align=center width=22>";
			if($user_type=="sample")
			{
                $html = $html."<input type='image' src='./images/del.png' onclick =\"cfm('".$qty[$j]['id']."','".$qty[$j]['colorway']."','".$qty[$j]['wi_id']."','".$back_str."', this ) \">";
			}
			else
			{
				if($enable_delete)
				{
					//$html = $html."<input type='image' name='delete_icon' src='./images/del.png' onclick =\"cfm('".$qty[$j]['id']."','".$qty[$j]['colorway']."','".$qty[$j]['wi_id']."','".$back_str."', this ,'".$qty[$j]['qty']."','".$wiqty_sum."','".$scheduleqty."') \">";
					$html = $html."<img name='delete_icon' src='./images/del.png' onclick =\"delete_qty('".$qty[$j]['id']."','".$qty[$j]['colorway']."','".$qty[$j]['wi_id']."','".$back_str."', this ,'".$qty[$j]['qty']."','".$wiqty_sum."','".$scheduleqty."') \">";
					//onclick =\"cfm('".$qty[$j]['id']."','".$qty[$j]['colorway']."','".$qty[$j]['wi_id']."') \">";
				}
			}
			$html = $html."&nbsp;</td>\n";
//			$html = $html." <td bgcolor=#eeeeee class=ylw9 align=center width=22> <a href=\"javascript:cfm('".$qty[$j]['id']."','".$qty[$j]['colorway']."','".$qty[$j]['wi_id']."','".$back_str."'  )\" value='�R��' style='cursor:pointer'><img src='./images/del.png' border=0></a></td>\n";

			$html = $html."</tr>\n";
		}
	  } else {
		$html = $html." <tr><td class=bgdy9 align=center colspan=".$span_col."> No colorway data ! </td></tr>";
	  }
		return $html;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	edit_breakdown($sizing,$qty="")  �ѶǤJ��sizing[array] ���X�@�� table 
#				�W���ئT���Y  �U�C�� input�J       �Ǧ^���table�� html
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function edit_breakdown($sizing,$back_str,$base_size='',$qty="") {
		$num_size = count($sizing);

		$html = "";
		$html = $html."<table border cellspacing=0 cellpadding=2 width=470 bgcolor=#ffffff align=center>";
		$html = $html." <tr>\n";
		$html = $html." <td class=ylw9 align=center bgcolor=#333333>colorway</td>\n";
			for($i=0;$i<count($sizing);$i++){
				if ( $sizing[$i] == $base_size)
				{
					$html = $html." <td bgcolor=#333333 class=ylw9 align=center><font color='#4EFF00'><b><i>".$sizing[$i]."</i></b></font></td>\n";
				}else{
					$html = $html." <td bgcolor=#333333 class=ylw9 align=center>".$sizing[$i]."</td>\n";
				}
			}
			$html = $html." <td bgcolor=#cccccc class=ssign align=center><b>SUM</b></td>\n";
			$html = $html." <td bgcolor=#cccccc class=ssign align=center width=22><b>add</b></td>\n";
			$html = $html."</tr>\n";
			$html = $html."<tr>";
			$html = $html." <td class=grn9 align=center bgcolor=#cccccc><input type='text' id='colorway' name='PHP_colorway' size=16 class=select style='text-align:center' style='text-transform:uppercase' onkeyup=value=value.replace(/[^\w\-\u4e00-\u9fa5\.\*]/ig,'').toUpperCase()></td>";

			for($i=0;$i<count($sizing);$i++){
				 $html = $html." <td class=b9 align=center bgcolor=#999999>\n";
				if (!$qty){
				 $html = $html."   <input type='text' id=\"PHP_qty".$i."\" name=\"PHP_qty".$i."\" size=1 class=select style='text-align:center' onchange='count_sum(".count($sizing).")'></td>\n";
				 //$html = $html."   <input type='text' name=\"PHP_qty".$i."\" size=1 class=select style='text-align:center' onchange=\"qty_sum(".count($sizing).")\"></td>\n";
				}else{
				 $html = $html."   <input type='text' id=\"PHP_qty".$i."\" name=\"PHP_qty".$i."\" value='".$qty[$i]."' size=1 class=select style='text-align:center' onchange='count_sum(".count($sizing).")'></td>\n";
				 //$html = $html."   <input type='text' name=\"PHP_qty".$i."\" value='".$qty[$i]."' size=1 class=select style='text-align:center' onchange=\"qty_sum(".count($sizing).")\"></td>\n";
				}
			}
		 $html = $html." <td class=b9 align=center bgcolor=#eeeeee>\n<input type='text' id='qty_sum' name='PHP_sum' size=1 style='text-align:center' readonly></td>\n";

			$html = $html." <td bgcolor=#eeeeee class=ylw9 align=center width=22><a href='javascript:cfm_add($num_size)' value='ADD' style='cursor:pointer'><img src='./images/add2.gif' border=0></td>\n";
			$html = $html."</tr>\n";
			$html = $html."</table>\n";
//			$html="<input type=\"hidden\" name=\"PHP_back_str\" value={$back_str}>";
		return $html;
	}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	get_usage_breakdown($num_acc,$est,$num_color,$qty) :  
#					�ѶǤJ��$est,$num_color,$qty ���X�@�Ӱ}�C <TD> �r��[$num_lots]
#					$num_color �� td�ƶq, $qty�� array[$num_color],
#					�Ǧ^ �@�հ}�C �� td; $est�� array[$num_acc]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function get_usage_breakdown($num_mat,$est,$num_color,$qty) {
		$usage = array();
		$mat_useage = array();
		$usage_table = array();
	  for ($i=0;$i<$num_mat;$i++){
	  
			$html="";
		for ($j=0;$j<$num_color;$j++){
			$mat_useage = $est[$i] * $qty[$j];
			$html=$html."<td class=bgdy9 align=center>".$mat_useage."</td>\n";
			$usage['mat_useage'][$i][$j] = $mat_useage;
		}
		$usage['table'][$i] = $html;
		$usage['usage_csv'][$i] = implode('|',$usage['mat_useage'][$i]);

	  }
		return $usage;
	}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	lots_choose_line($num_mat,$est,$num_color,$qty) :  
#					�ѶǤJ�� $num_mat,$num_color,$qty ���X�@�Ӱ}�C <TR> �ﶵ��
#					$num_mat,$num_color,$qty �Ҭ� array
#					�Ǧ^ �@�հ}�C �� <tr>; $est�� array[$num_acc]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function lots_choose_line($num_mat,$est,$num_color,$qty,$lots_id) {
		$usage = array();
		$mat_useage = array();
		$usage_table = array();
	  for ($i=0;$i<$num_mat;$i++){
			$html="";
		for ($j=0;$j<$num_color;$j++){
			$mat_useage = $est[$i] * $qty[$j];
			$html=$html."<td class=bgdy9 align=center><input type=checkbox name=\"PHP_bom_qty".$lots_id[$i]."_$j\" value='".$mat_useage."'></td>\n";
			$usage['mat_useage'][$i][$j] = $mat_useage;
		}
		$usage['table'][$i] = $html;
		$usage['usage_csv'][$i] = implode('|',$usage['mat_useage'][$i]);
	  }
		return $usage;
	}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	acc_choose_line($num_mat,$est,$num_color,$qty) :  
#					�ѶǤJ�� $num_mat,$num_color,$qty ���X�@�Ӱ}�C <TR> �ﶵ��
#					$num_mat,$num_color,$qty �Ҭ� array
#					�Ǧ^ �@�հ}�C �� <tr>; $est�� array[$num_acc]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function acc_choose_line($num_mat,$est,$num_color,$qty,$lots_id) {
		$usage = array();
		$mat_useage = array();
		$usage_table = array();
	  for ($i=0;$i<$num_mat;$i++){
			$html="";
		for ($j=0;$j<$num_color;$j++){
			$mat_useage = $est[$i] * $qty[$j];
			$html=$html."<td class=bgdy9 align=center><input type=checkbox name=\"PHP_bom_qty".$lots_id[$i]."_$j\" value='".$mat_useage."'></td>\n";
			$usage['mat_useage'][$i][$j] = $mat_useage;
		}
		$usage['table'][$i] = $html;
		$usage['usage_csv'][$i] = implode('|',$usage['mat_useage'][$i]);
	  }
		return $usage;
	}




#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	dayCount ($begdate,$enddate)  �p���Ӥ��������� ; �Ǧ^���
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function dayCount ($begdate,$enddate) {

    list($byear,$bmonth,$bday) = split("-",$begdate);
    list($eyear,$emonth,$eday) = split("-",$enddate);

    $b = mktime(0,0,0,$bmonth,$bday,$byear);
    $e = mktime(0,0,0,$emonth,$eday,$eyear);

    $bg = getdate($b);		
    $en = getdate($e);		

	$beg['YEAR'] = $bg['year'];
	$beg['MONTH'] = $bg['mon'];
	$beg['DAY'] = $bg['mday'];

	$end['YEAR'] = $en['year'];
	$end['MONTH'] = $en['mon'];
	$end['DAY'] = $en['mday'];

	$start = gmmktime(0,0,0,$beg['MONTH'],$beg['DAY'],$beg['YEAR']);
	$endin = gmmktime(0,0,0,$end['MONTH'],$end['DAY'],$end['YEAR']);

	$day = 0;

	if ($start < $endin) {	$toward = 1;	} else  {	$toward = 0;	}

	$mover = $start;

	if ($start != $endin) {
		do {
			$day++;
		   if ($toward)  {
				$mover = gmmktime(0,0,0,$beg['MONTH'],($beg['DAY']+$day),$beg['YEAR']);
		   } else {
				$mover = gmmktime(0,0,0,$beg['MONTH'],($beg['DAY']-$day),$beg['YEAR']);
		   }
		} while ($mover != $endin);
	}
    
	return $day;
}


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	countDays ($begdate,$enddate)  �p���Ӥ��������� ; �Ǧ^���
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function countDays ($begdate,$enddate) {

	if( ($begdate=='0000-00-00') || ($enddate=='0000-00-00') || !$begdate || !$enddate ){
		return 0;
	}
    // return round(abs(strtotime($begdate)-strtotime($enddate))/86400);
    return round((strtotime($enddate)-strtotime($begdate))/86400);
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	countHours ($begdate,$enddate)  �p���Ӥ�������ɼ� ; �Ǧ^�ɼ�
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function countHours ($begdate,$enddate) {

	if( ($begdate=='0000-00-00') || ($enddate=='0000-00-00') || !$begdate || !$enddate ){
		return 0;
	}
    
	$day=round(abs(strtotime($enddate)-strtotime($begdate))/(60*60));
	return $day;
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	extract_su ($monsu)	 �ǤJ�@�� csv�� ��} month��su �� //�ǥX �}�C--
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function extract_su ($monsu) {
		$su_array = array();

		$su = explode(',',$monsu);
		for ($i=0; $i<count($su); $i++){
			$su_array[$i]['mon'] = substr($su[$i],0,6);
			$su_array[$i]['su'] = substr($su[$i],6);
		}
		return $su_array;
	}  // end func.

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	getDaysInMonth($month,$year)  ��J�~�Τ� �^�иӤ몺���
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function getDaysInMonth($month,$year)	{
		$daysInMonth = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
	   
	   if ($month < 1 || $month > 12)     return 0;
	   $d = $daysInMonth[$month - 1];
	   if ($month == 2){
	      if ($year%4 == 0){
	          if ($year%100 == 0){
	             if ($year%400 == 0){
				 	$d = 29; 
				 }
	          } else {
	               $d = 29;
			  }
		  }
	   }
	   return $d;
	}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	increceDaysInDate($date,$nDays)  ��J��� �έn�W�[����� �^�мW�᪺���
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function increceDaysInDate($date,$nDays) 	{ 
	    if( !isset( $nDays ) ) { 	$nDays = 1;     } 
	    $aVet = Explode( "-",$date ); 
	    if(isset($aVet[1]))
	    {
	    	return date( "Y-m-d",mktime(0,0,0,$aVet[1],$aVet[2]+$nDays,$aVet[0])); 
	  	}
//	    $list ($month, $day, $year) = Explode("-",$date ); 
//	    return date( "Y-m-d",mktime(0,0,0,$month,$day+$nDays,$year)); 
	} 	


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	increceHoursInDate($date,$nHour)  ��J��� �έn�W�[���p�� �^�мW�᪺���
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function increceHoursInDate($date,$nHour=0) 	{ 

//	    if( !$nHour ) { 	$nHour = 1;     } 
	    $aVet = Explode(" ",$date ); 
	    $dVet = explode('-',$aVet[0]);
	    $hVet = explode(':',$aVet[1]);
	    if(isset($aVet[1]))
	    {
	    	$rtn =  date( "Y-m-d H:i:s",mktime($hVet[0]+$nHour,$hVet[1],$hVet[2],$dVet[1],$dVet[2],$dVet[0])); 
	  		
	  		
	  	  return $rtn;
	  	}
//	    $list ($month, $day, $year) = Explode("-",$date ); 
//	    return date( "Y-m-d",mktime(0,0,0,$month,$day+$nDays,$year)); 
	} 	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	increceMonthInDate($date,$nmon)  ��J��� �έn�W�[����� �^�мW�᪺���
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function increceMonthInDate($date,$nmon) 	{ 
	    if( !isset( $nmon ) ) { 	$nmon = 1;     } 
	    $aVet = Explode( "-",$date ); 
	    $tmp= $aVet[1]-2;
	    if ($tmp < 1)
	    {
	    	$tmp=$tmp+12;
	    	$aVet[0]=$aVet[0]-1;
	    }
	    if ($tmp < 10)$tmp='0'.$tmp;
	    $aVet[1]=$tmp;
	    $new_day=$aVet[0]."-".$aVet[1]."-01";
	    return $new_day; 
	    
	}
	function increceMonthInDate2($date,$nDays) 	{ 
	    if( !isset( $nDays ) ) { 	$nDays = 1;     } 
	    $aVet = Explode( "-",$date ); 
	    if(isset($aVet[1]))
	    {
	    	return date( "Y-m-d",mktime(0,0,0,$aVet[1]+$nDays,$aVet[2],$aVet[0])); 
	  	}
//	    $list ($month, $day, $year) = Explode("-",$date ); 
//	    return date( "Y-m-d",mktime(0,0,0,$month,$day+$nDays,$year)); 
	} 	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	decode_mon_su($number) :�Ncsv�r��$number�Ѷ}���}�C$number���e���X��year+mon[200507]
#      �A�N�}�C�����令�e ��ⳡ��, �A�N�e��key ���value ���}�C   �Ǧ^�}�C
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function decode_mon_su($number) {
		$decoded = array();
		$array = explode(",", $number);

		for ($i=0; $i < sizeof($array); $i++) {
			$key = substr($array[$i],0,6);
			$val = substr($array[$i],6);
			$decoded[$key] = $val;
		}
		return $decoded;
	} // end func
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#		decode_mon_yy_su($number) :�Ncsv�r��$number�Ѷ}���}�C$number
#				   				   �N$number�����e�|��ƪ�year,�����G�쪺mon,�βĤ���H�᪺su
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function decode_mon_yy_su($number) {
		$decoded = array();
		$array = explode(",", $number);

		for ($i=0; $i < sizeof($array); $i++)     {
				$result[$i]['year']=substr($array[$i],0,4);
				$result[$i]['mon']=substr($array[$i],4,2);
				$result[$i]['su']=substr($array[$i],6);
		}
		return $result;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	encode_mon_su($su_array) : �N������su�}�C['200507'==>230,...�令cvs[200507230,...]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function encode_mon_su($su_array) {
		$cvs_str = "";
		while(list($key,$value) = each($su_array)	){
			$cvs_str = $cvs_str.$key.$value.",";
			}//while
//		$cvs_str = substr($cvs_str,2);    // �h�Y
		$cvs_str = substr($cvs_str,0,-1); // �h��
		return $cvs_str;
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	filter_0_value($Ary) : �N �}�C���Ȭ��s ������ 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function filter_0_value($val) {

 		return ($val > 0);
	} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	function only_sales_dept($dept) : �N DEPT�}�C ���Ȥ��O k �}�Y������ [�D�~�ȳ��]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function only_sales_dept($dept) {

	return ( substr($dept,0,1) == 'K' );

} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	function only_sales_dept2($dept, $key) : �N DEPT�}�C���O J �}�Y������ [�D�~�ȳ��2]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function only_sales_dept2($dept) {
	return (substr($dept,0,1) == 'J');

} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	function only_sales_dept2($dept, $key) : �N DEPT�}�C���O H �}�Y������ [�D�u�tHJ]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function only_sales_dept3($dept) {					
	return (substr($dept,0,1) == 'H');

} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	function only_sales_dept2($dept, $key) : �N DEPT�}�C���O L �}�Y������ [�D�u�tHJ]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function only_sales_dept4($dept) {					
	return (substr($dept,0,1) == 'L');

} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	function only_sales_dept2($dept, $key) : �N DEPT�}�C���O T �}�Y������ [�D�~�ȳ��2]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function only_sales_dept5($dept) {
	return (substr($dept,0,1) == 'T');

} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	function only_sales_dept2($dept, $key) : �N DEPT�}�C���O D �}�Y������ [�D�~�ȳ��2]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function only_sales_dept6($dept) {
	return (substr($dept,0,1) == 'D');

} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	function only_sales_dept2($dept, $key) : �N DEPT�}�C���O C �}�Y������ [�D�~�ȳ��2]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function only_sales_dept10($dept) {
	return (substr($dept,0,1) == 'C');

} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	function only_sales_dept7	�b�������n�J���C�������������
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function only_sales_dept7($dept) {
	return ( $dept == 'SA' || $dept == 'AC' || $dept == 'GM' || $dept == 'RD' || $dept == 'PM' || $dept == 'V1');
} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	function only_sales_dept8	�b�������n�J���C���u�t
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function only_sales_dept8($dept) {
	return ( $dept == 'HJ' || $dept == 'LY' || $dept == 'CF' || $dept == 'C1' || $dept == 'V1' );
} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	function only_sales_dept9 ����u�t�� page
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function only_sales_dept9($dept) {
	return ( $dept == 'ie_search' || $dept == 'schedule_search' || $dept == 'cfm_pd_schedule' || $dept == 'production_search' || $dept == 'shipping_search' || $dept == 'production_search_daily' || $dept == 'shipping_search_daily' || $dept == 'IE_record' || $dept == 'pd_schedule' || $dept == 'production' || $dept == 'shipping');
} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	function only_sales_dept_mode �u��ܲ{�b�һݪ�����
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function only_sales_dept_mode($dept) {
	return ( $dept == 'KA' || $dept == 'KB' || $dept == 'DA' || $dept == 'LY' || $dept == 'HJ' || $dept == 'CF' || $dept == 'CL' || $dept == 'PH' || $dept == 'SC' || $dept == 'J1' );
} // end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	function related_dept($parm) : 
#			�N $dept �}�C�r���t�� $key����X�� ��l���� 
#			$parm �t�� $parm['dept_array'], ['key']
#			�q�`�o�� function�� get_dept �I�s
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function related_dept($parm) {

		return (substr($parm['dept_array'],0,1) == $parm['key'] );

	} // end func



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	function page_display($i, $j, $tpl) : 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function page_display2($op, $i, $j, $tpl) {

		$allow = $GLOBALS['admin']->decode_perm($i,$j);   // �]�w �s�W�R���v��

		$op['view_flag'] = ($allow['view']) ? 1 : '';
		$op['add_flag'] = ($allow['add']) ? 1 : '';
		$op['edit_flag'] = ($allow['edit']) ? 1 : '';
		$op['del_flag'] = ($allow['del']) ? 1 : '';

		if(!empty($_SESSION['MSG']))
		$op['msg'] = $_SESSION['MSG'];
		$op['msg_YES'] = (isset($op['msg']) && $op['msg']) ? 1 : "";

		$GLOBALS['layout']->assign($op);
		$GLOBALS['layout']->display($tpl);
		unset($_SESSION['MSG']);
	}


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	function check_authority($i, $j, $job) : 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	// function check_authority($i, $j, $job) {
		// $TPL_ERROR = "error.html";
		// if(!isset($_SESSION['SCACHE']['ADMIN']['authority']))$_SESSION['SCACHE']['ADMIN']['authority']=$i.$j;
		// if( $_SESSION['SCACHE']['ADMIN']['authority'] != $i.$j ){
			// # �M�� ���X
			// unset($_SESSION['PAGE']);
			// $_SESSION['SCACHE']['ADMIN']['authority'] = $i.$j;
		// }

		// if(!$GLOBALS['admin']->is_power($i, $j, $job)){ 
			// $op['msg'][] = "sorry! you don't have this Authority !";
			// $GLOBALS['layout']->assign($op);
			// $GLOBALS['layout']->display($TPL_ERROR);  		    
			// exit;
		// }
		// return true;
	// }

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	function download() : �ΨӨ��ɮץi�H�U���Ӥ��O�����}��
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function download($upload_dir,$upload_file)
{
	$fp = fopen($upload_dir.$upload_file,"r"); 
	Header("Content-type: application/octet-stream");
	Header("Accept-Ranges: bytes");
	Header("Accept-Length: ".filesize($upload_dir.$upload_file));
	Header("Content-Disposition: attachment; filename=" . $upload_file);
	echo fread($fp,filesize($upload_dir . $upload_file));
	fclose($fp);
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	function get_currency($i, $j, $job) : 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_currency($cur) {		
    $cur=(string)$cur;
    
    $cur=explode('.',$cur);
    $cash="";
    $len=strlen($cur[0]);		
    $size=$len / 3;
    $size=(int)$size;
    $md = $len % 3;
    if ($md == 0)$size--;
    for ($i=0; $i < $size; $i++)
    {
        $len=$len - 3;
        $curen[$i]=substr($cur[0],$len,3);
        
    }
    $curen[$size]=substr($cur[0],0,$len);
    
    
    for ($i=$size; $i > 0; $i--)
    {
        $cash=$cash.$curen[$i].",";
    }
    if(isset($curen[0])) $cash=$cash.$curen[0];
    if (isset($cur[1]))
    { 
        if (strlen($cur[1])==1)
        {
            $cash=$cash.'.'.$cur[1]."0";
        }else{
        $tmp1=substr($cur[1],0,2);
        $tmp2=substr($cur[1],2,1);
        if ($tmp2 > 4) $tmp1++;
        $cash=$cash.'.'.$tmp1;
        }
        
    }else{
        $cash=$cash.'.00';
    }
    
    
    return $cash;
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	function bom_lots_view($num_lots_used,$bom_lots,$lots_used,$num_colors) : �զXbom lots��html
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function bom_lots_view($num_lots_used,$bom_lots,$lots_used,$num_colors,$bom_photo='') {
        // print_r($bom_photo);
        $T_pid = array();
        $N_pid = array();
        foreach($_SESSION['P_ID'] as $pid){
            // echo $pid;
            $T_pid[$pid]=$pid;
            $N_pid[$pid]+=1;
        }

		$op['bom_lots_list'] = array();	
		for ($i=0;$i<$num_lots_used;$i++){   // �� �D�ƥήưO���v�� loop
			$no_found_flag = 1;  // �w�] bom���䤣���ƪ��X��
			$html ='';
			for ($k=0;$k<count($bom_lots);$k++){  // �� bom �D�ưO�� �v�� loop
				$T_bom = array();
				$html ='';
				if ($bom_lots[$k]['lots_used_id'] == $lots_used[$i]['id'] ){
					$no_found_flag = "";  // �X�Ч���

					$T_bom['lots_code']		= $lots_used[$i]['lots_code'];
					$T_bom['lots_name']		= $lots_used[$i]['lots_name'];
					$T_bom['use_for']		= $lots_used[$i]['use_for'];
					$T_bom['est_1']			= $lots_used[$i]['est_1'];
					$T_bom['vendor1']		= $lots_used[$i]['vendor1'];
					$T_bom['price1']		= $lots_used[$i]['price1'];
					$T_bom['unit']			= $lots_used[$i]['unit'];
					$T_bom['width']			= $lots_used[$i]['width'];
					$T_bom['weight']		= $lots_used[$i]['weight'];
					$T_bom['comp']			= $lots_used[$i]['comp'];
					$T_bom['mile']			= $lots_used[$i]['des'];
					$T_bom['color']			= $bom_lots[$k]['color'];

					$T_qty = explode(",", $bom_lots[$k]['qty']);
					$T_o_qty = explode(",", $bom_lots[$k]['o_qty']);
					for ($p=0;$p<$num_colors;$p++) {
						$show_qty = number_format($T_qty[$p],0,'',',');
						if ( $show_qty==0 ){	$show_qty = "&nbsp;";	}
						$html = $html.'<td bgcolor="#ffffff" class="grn9" align="right" id="MK_pid" pid="'.$_SESSION['P_ID'][$p].'" ><b>'.$show_qty.'</b></td>'."\n";
					}
					$T_bom['list']			= $html;
					$T_bom['id']			= $bom_lots[$k]['id'];
					$T_bom['wi_id']			= $bom_lots[$k]['wi_id'];
					$T_bom['lots_used_id']	= $bom_lots[$k]['lots_used_id'];
					$T_bom['total']			= array_sum($T_qty);
					$T_bom['total_o_qty']	= array_sum($T_o_qty);
					$T_bom['creator']	    = $bom_photo[$bom_lots[$k]['id']]['creator'];
					$T_bom['creat_date']    = $bom_photo[$bom_lots[$k]['id']]['creat_date'];
					$T_bom['confirmer']     = $bom_photo[$bom_lots[$k]['id']]['confirmer'];
					$T_bom['confirm_date']  = $bom_photo[$bom_lots[$k]['id']]['confirm_date'];
					$T_bom['status']        = $bom_photo[$bom_lots[$k]['id']]['status'];
					$op['bom_lots_list'][]	= $T_bom;

				}   // BOM �ɤ��� �D��id ���� �ή��ɪ�id ����

			}  // end for K... bom�ɤ��D�� �v�� ����

			if ($no_found_flag){  // �� bom�ɤ��L�o���ήƪ�id��

				if (!$num_colors){	$num_span = 1;	}else{ $num_span = $num_colors; }

				$T_bom['lots_code']		= $lots_used[$i]['lots_code'];
				$T_bom['lots_name']		= $lots_used[$i]['lots_name'];
				$T_bom['use_for']		= $lots_used[$i]['use_for'];
				$T_bom['est_1']			= $lots_used[$i]['est_1'];
				$T_bom['vendor1']		= $lots_used[$i]['vendor1'];
				$T_bom['price1']		= $lots_used[$i]['price1'];
				$T_bom['unit']			= $lots_used[$i]['unit'];
				$T_bom['width']			= $lots_used[$i]['width'];
				$T_bom['weight']        = $lots_used[$i]['weight'];
				$T_bom['con']			= $lots_used[$i]['comp'];
				$T_bom['mile']			= $lots_used[$i]['des'];

				$T_bom['color']			= '&nbsp';
                
                foreach($T_pid as $pid){
                    $html = $html.'<td bgcolor="#ffffff" class="bgdy9" align="center" colspan="'.$N_pid[$pid].'" id="MK_pid" pid="'.$pid.'" ><b>-none-</b></td>'."\n";
                }    
				// $html = $html.'<td bgcolor="#ffffff" class="bgdy9" align="center" colspan="'.$num_span.'"><b>-none-</b></td>'."\n";
				$T_bom['list']			= $html;

				$op['bom_lots_list'][]			= $T_bom;
			}
        }  // end for �� �D�ƥήưO���v�� loop
        return $op['bom_lots_list'];
	}
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	function bom_lots_view($num_lots_used,$bom_lots,$lots_used,$num_colors) : �զXbom lots��html
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function bom_acc_view($num_acc_used,$bom_acc,$acc_used,$num_colors,$bom_photo='') {
        
        $T_pid = array();
        $N_pid = array();
        foreach($_SESSION['P_ID'] as $pid){
            // echo $pid;
            $T_pid[$pid]=$pid;
            $N_pid[$pid]+=1;
        }

		$op['bom_acc_list'] = array();	
		  for ($i=0;$i<$num_acc_used;$i++){   // �� �ƮƥήưO���v�� loop
				$no_found_flag = 1;  // �w�] bom���䤣���ƪ��X��
				$html ='';
			for ($k=0;$k<count($bom_acc);$k++){  // �� bom �ƮưO�� �v�� loop
				$T_bom = array();
				$html ='';
				if ($bom_acc[$k]['acc_used_id'] == $acc_used[$i]['id'] ){
					$no_found_flag = "";  // �X�Ч���

					$T_bom['acc_code']		= $acc_used[$i]['acc_code'];
					$T_bom['acc_name']		= $acc_used[$i]['acc_name'];
					$T_bom['acc_use']		= $acc_used[$i]['use_for'];
					$T_bom['est_1']			= $acc_used[$i]['est_1'];
					$T_bom['vendor1']		= $acc_used[$i]['vendor1'];
					$T_bom['price1']		= $acc_used[$i]['price1'];
					$T_bom['unit']			= $acc_used[$i]['unit'];
					$T_bom['cons']			=  $acc_used[$i]['specify'];

					$T_bom['color']			= $bom_acc[$k]['color'];
					$T_bom['size']			= $bom_acc[$k]['size'];

					$T_qty = explode(",", $bom_acc[$k]['qty']);
					$T_o_qty = explode(",", $bom_acc[$k]['o_qty']);

				if ($acc_used[$i]['arrange'] ==1 || $acc_used[$i]['arrange'] ==3)
				{
					$s_total=0;
					for ($p=0;$p<sizeof($T_qty);$p++) $s_total+=$T_qty[$p];
                    // $html = $html.'<td class="grn9" align="right" colspan="'.$num_colors.'" id="pdid_MKd" pid="'.$_SESSION['P_ID'][$p].'" >['.$N_pid[$_SESSION['P_ID'][$p]].']<b>'.number_format($s_total,0,'',',').'</b></td>'."\n";
                    foreach($T_pid as $pid){
                        $html = $html.'<td class="grn9" align="right" colspan="'.$N_pid[$pid].'" id="MK_pid" pid="'.$pid.'" ><b>'.number_format($s_total,0,'',',').'</b></td>'."\n";
                    }
				}else{				
					for ($p=0;$p<$num_colors;$p++){
						$show_qty = number_format($T_qty[$p],0,'',',');
						if ($show_qty==0){	$show_qty = "&nbsp;";	}
						$html = $html.'<td bgcolor="#ffffff" class="grn9" align="right" id="MK_pid" pid="'.$_SESSION['P_ID'][$p].'" ><b>'.$show_qty.'</b></td>'."\n";
					}
				}


					$T_bom['list']			= $html;
					$T_bom['id']			= $bom_acc[$k]['id'];
                    $T_bom['wi_id']			= $bom_acc[$k]['wi_id'];
					$T_bom['total']			= array_sum($T_qty);
					$T_bom['total_o_qty']	= array_sum($T_o_qty);
					$T_bom['creator']	    = $bom_photo[$bom_acc[$k]['id']]['creator'];
					$T_bom['creat_date']    = $bom_photo[$bom_acc[$k]['id']]['creat_date'];
					$T_bom['confirmer']     = $bom_photo[$bom_acc[$k]['id']]['confirmer'];
					$T_bom['confirm_date']  = $bom_photo[$bom_acc[$k]['id']]['confirm_date'];
					$T_bom['status']        = $bom_photo[$bom_acc[$k]['id']]['status'];
					$op['bom_acc_list'][]	= $T_bom;
					
					
				}   // BOM �ɤ��� �Ʈ�id ���� �ή��ɪ�id ����

			}  // end for K... bom�ɤ��Ʈ� �v�� ����

			if ($no_found_flag){  // �� bom�ɤ��L�o���ήƪ�id��

				if (!$num_colors){	$num_span = 1;	}else{ $num_span = $num_colors; }

				$T_bom['acc_code']		= $acc_used[$i]['acc_code'];
				$T_bom['acc_name']		= $acc_used[$i]['acc_name'];
				$T_bom['acc_use']		= $acc_used[$i]['use_for'];
				$T_bom['vendor1']		= $acc_used[$i]['vendor1'];
				$T_bom['vendor1']		= $acc_used[$i]['price1'];
				$T_bom['est_1']			= $acc_used[$i]['est_1'];
				$T_bom['unit']			= $acc_used[$i]['unit'];
				$T_bom['cons']			=  $acc_used[$i]['specify'];
	
				$T_bom['color']			= '&nbsp';
                
                foreach($T_pid as $pid){
                    $html = $html.'<td bgcolor="#ffffff" class="bgdy9" align="center" colspan="'.$N_pid[$pid].'" id="MK_pid" pid="'.$pid.'" ><b>-none-</b></td>'."\n";
                }                
                
				// $html = $html."<td bgcolor=#ffffff class=bgdy9 align=center colspan=".$num_span."><b>-none-</b></td>\n";
				$T_bom['list']			= $html;

				$op['bom_acc_list'][]   = $T_bom;

			}

		  }  // end for �� �D�ƥήưO���v�� loop
		  return $op['bom_acc_list'];
	}
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	function bom_lots_view($num_lots_used,$bom_lots,$lots_used,$num_colors) : �զXbom lots��html
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function bom_lots_edit($num_lots_used,$bom_lots,$lots_used,$num_colors, $PHP_id, $color_qty) {	
		$op['bom_lots_list'] = array();	
	for ($i=0;$i<$num_lots_used;$i++){   // �� �D�ƥήưO���v�� loop
			$html ='';

			// --------  bom ��J�C [ �D�� ] ------------
				$T_bom['tr'] = " bgcolor=#d6d6d6";
				$T_bom['change'] = 1;
				$T_bom['lots_code']		= $lots_used[$i]['lots_code'];
				$T_bom['lots_name']		= $lots_used[$i]['lots_name'];
				$T_bom['use_for']		= $lots_used[$i]['use_for'];
				$T_bom['est_1']			= $lots_used[$i]['est_1'];
				$T_bom['unit']			= $lots_used[$i]['unit'];
				$T_bom['ap_mark']  = '';
				$T = "<input type='text' size='15' name=\"PHP_lots_color_".$lots_used[$i]['id']."\" value='".$lots_used[$i]['color']."' size=3 class=select onkeyup=value=value.replace(/[^\w\-\u4e00-\u9fa5\.\*\s]/ig,'').toUpperCase()>";
				$T_bom['color']			= $T;
				$T_bom['size']			= '';
				$T_a = "<a style='cursor:pointer'><img src='./images/add2.gif' onclick=get_num(this,'".$lots_used[$i]['id']."','".$T_bom['size']."',$num_colors,'lots','') border='0'></img></a>";
				//$T_a = "<a href='javascript:cfm_add(".$PHP_id.",\"".$acc_used[$i]['id']."x".$size[$s]."\",".$num_colors.",\"acc\",\"".$size[$s]."\",\"by size and colorway\")' value='�s�W' style='cursor:pointer'><img src='./images/add2.gif' border=0>";

				$T_bom['remark']		= $T_a;

					$html="";
					for ($j=0;$j<$num_colors;$j++){
						$mat_useage = $lots_used[$i]['est_1'] * $color_qty[$j];
						$html=$html."<td class=bgdy9 align=center onmouseover=\""."overTip(preview,$('#bom_table tr:eq(0) td:eq(".(7+$j).")').val())\" onmouseout=outTip(preview)>
							<input type='checkbox' name=\"PHP_bom_qty".$lots_used[$i]['id']."_$j\" value=\"".$mat_useage."\">".
							'<input type="hidden" name="PHP_bom_o_qty'.$lots_used[$i]['id'].'_'.$j.'" value="'.$color_qty[$j].'">'.
						"</td>\n";
					}
				$T_bom['list']			= $html;

				$op['bom_lots_list'][]	= $T_bom;

				// --------  end bom ��J�C [ �D�� ]  ------------

		for ($k=0;$k<count($bom_lots);$k++){  // �� bom �D�ưO�� �v�� loop
			$T_bom = array();
			$html ='';
			if ($bom_lots[$k]['lots_used_id'] == $lots_used[$i]['id'] ){
															
				$T_bom['tr'] = " onmouseover=\"bgColor='#FBFBCA'\" onmouseout=\"bgColor='#ffffff'\"";
				$T_bom['change'] = 0;
				$T_bom['lots_code']		= $lots_used[$i]['lots_code'];
				$T_bom['lots_name']		= $lots_used[$i]['lots_name'];
				$T_bom['use_for']		= $lots_used[$i]['use_for'];				
				$T_bom['est_1']			= $lots_used[$i]['est_1'];
				$T_bom['unit']			= $lots_used[$i]['unit'];
				//$del ="<a href=\"javascript:cfm_del('".$bom_lots[$k]['id']."','".$PHP_id."','".$lots_used[$i]['lots_code']."','lots')\" value='Del' style=\"cursor:pointer\"><img src='images/del.png' alt='Del' border=0 valign=bottom></a>";
				$del ="<input type='image' src='images/del.png' onclick=\"cfm_del_ajx('".$bom_lots[$k]['id']."','".$PHP_id."','".$lots_used[$i]['lots_code']."','lots',this)\">";

				$T_bom['remark']		= $del;

				$T_bom['color']			= $bom_lots[$k]['color'];
				if (!isset($bom_lots[$k]['ap_mark'])) $bom_lots[$k]['ap_mark']='';
				$T_bom['ap_mark']			= $bom_lots[$k]['ap_mark'];

					$T_qty = explode(",", $bom_lots[$k]['qty']);
				for ($p=0;$p<$num_colors;$p++){
						if ($T_qty[$p]==0){	$T_qty[$p] = "&nbsp;";	}
					$html = $html."<td class=grn9 align=center><b>".$T_qty[$p]."</b></td>\n";
				}
				$T_bom['list']			= $html;
				$op['bom_lots_list'][]	= $T_bom;

			}   // BOM �ɤ��� �D��id ���� �ή��ɪ�id ����
		
		}  // end for K... bom�ɤ��D�� �v�� ����

	  
	  
	  
	  }  // end for �� �D�ƥήưO���v�� loop

		  return $op['bom_lots_list'];
	}	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	function bom_lots_view($num_lots_used,$bom_lots,$lots_used,$num_colors) : �զXbom lots��html
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function bom_acc_edit($num_acc_used,$bom_acc,$acc_used,$num_colors, $PHP_id, $color_qty,$size,$size_qty) {	
		$op['bom_acc_list'] = array();	
	  for ($i=0;$i<$num_acc_used;$i++){   // �� �ƮƥήưO���v�� loop
			$html ='';

		
		if($acc_used[$i]['arrange'] == 2)
		{
			for ($s=0; $s<sizeof($size); $s++)
			{	// --------  bom ��J�C [ �Ʈ� ] ����X ------------
				if($s == 0){$T_bom['change'] = 1;}else{$T_bom['change'] = 0;}
				$T_bom['tr'] = " bgcolor=#D9E2CD";
				$T_bom['acc_code']		= $acc_used[$i]['acc_code'];
				$T_bom['acc_name']		= $acc_used[$i]['acc_name'];
				$T_bom['acc_use']		= $acc_used[$i]['use_for'];
				$T_bom['est_1']			= $acc_used[$i]['est_1'];
				$T_bom['unit']			= $acc_used[$i]['unit'];
				$T_bom['ap_mark']		= '';
				$T_bom['size']		= $size[$s];
				
				$size_str=str_replace(".","_",$size[$s]);
				
				$T = "<input type='text' size='15' name=\"PHP_acc_color_".$acc_used[$i]['id']."x".$size_str."\" value='".$acc_used[$i]['color']."' size=4 class=select onkeyup=value=value.replace(/[^\w\-\u4e00-\u9fa5\.\*\s]/ig,'').toUpperCase()>";
				$T_bom['color']			= $T;
				//$T_a = "<a style='cursor:pointer'><img src='./images/add2.gif' onclick=get_num(this,'".$acc_used[$i]['id']."','".$T_bom['size']."',$num_colors,'acc','BySizeAndByColorway') border='0'></img></a>";
				$T_a = "<a style='cursor:pointer'><img src='./images/add2.gif' onclick='get_num(this,\"".$acc_used[$i]['id']."\",\"".$T_bom['size']."\",$num_colors,\"acc\",\"by size and colorway\")' border=\"0\"></img></a>";
				//$T_a ="<input type='image' src='images/add2.gif'   onclick='cfm_add(this,".$PHP_id.",\"".$acc_used[$i]['id']."x".$size[$s]."\",".$num_colors.",\"acc\",\"".$size[$s]."\",\"by size and colorway\")'>";

				$T_bom['remark']		= $T_a;
					$html="";
					for ($j=0;$j<$num_colors;$j++){
						$mat_useage = $acc_used[$i]['est_1'] * $size_qty[$size[$s]][$j];
						#M11021504 �ק� by colorway ���
						$disabled = $mat_useage == 0 ? 'disabled' : '' ;
						//$html=$html."<td class=bgdy9 align=center><input type='checkbox' name=\"PHP_bom_qty".$acc_used[$i]['id']."x".$size[$s]."_$j\" value=\"".$mat_useage."\" $disabled></td>\n";
						$html=$html."<td class=bgdy9 align=center onmouseover=\""."overTip(preview,$('#bom_table tr:eq(0) td:eq(".(7+$j).")').val())\" onmouseout=outTip(preview)>
										<input type='checkbox' name=\"PHP_bom_qty".$acc_used[$i]['id']."x".$size_str."_$j\" value=\"".$mat_useage."\" $disabled>".
										'<input type="hidden" name="PHP_bom_o_qty'.$acc_used[$i]['id'].'x'.$size_str.'_'.$j.'" value="'.$size_qty[$size[$s]][$j].'">'.										
									"</td>\n";
					}
				$T_bom['list']			= $html;
				$op['bom_acc_list'][]	= $T_bom;
			}
		}else if($acc_used[$i]['arrange'] == 1){
			for ($s=0; $s<sizeof($size); $s++)
			{// --------  bom ��J�C [ �Ʈ� ] ��X ------------
				if($s == 0){$T_bom['change'] = 1;}else{$T_bom['change'] = 0;}
				$T_bom['tr'] = " bgcolor=#D9E2CD";
				$T_bom['acc_code']		= $acc_used[$i]['acc_code'];
				$T_bom['acc_name']		= $acc_used[$i]['acc_name'];
				$T_bom['acc_use']		= $acc_used[$i]['use_for'];
				$T_bom['est_1']			= $acc_used[$i]['est_1'];
				$T_bom['unit']			= $acc_used[$i]['unit'];
				$T_bom['ap_mark']		= '';
				$T_bom['size']		= $size[$s];
				
				$size_str=str_replace(".","_",$size[$s]);
				
				$T = "<input type='text' size='15' name=\"PHP_acc_color_".$acc_used[$i]['id']."x".$size_str."\" value='".$acc_used[$i]['color']."' size=3 class=select onkeyup=value=value.replace(/[^\w\-\u4e00-\u9fa5\.\*\s]/ig,'').toUpperCase()>";
				$T_bom['color']			= $T;
				//$T_a = "<a style='cursor:pointer'><img src='./images/add2.gif' onclick=get_num(this,'".$acc_used[$i]['id']."','".$T_bom['size']."',1,'acc','BySize') border='0'></img></a>";
				$T_a = "<a style='cursor:pointer'><img src='./images/add2.gif' onclick='get_num(this,\"".$acc_used[$i]['id']."\",\"".$T_bom['size']."\",1,\"acc\",\"by size\")' border=\"0\"></img></a>";
				//$T_a ="<input type='image' src='images/add2.gif'   onclick='cfm_add(this,".$PHP_id.",\"".$acc_used[$i]['id']."x".$size[$s]."\",1,\"acc\",\"".$size[$s]."\",\"by size\")'>";

				$T_bom['remark']		= $T_a;
					$html="";
					$mat_useage=0;
                    $mat_all = 0;
					for ($j=0;$j<$num_colors;$j++){
						$mat_useage += $acc_used[$i]['est_1'] * $size_qty[$size[$s]][$j];
                        $mat_all += $size_qty[$size[$s]][$j];
					}
					$disabled = $mat_useage == 0 ? 'disabled' : 'checked' ;
					$html=$html."<td class=bgdy9 align=center colspan=".$j.">
									<input type='checkbox' name=\"PHP_bom_qty".$acc_used[$i]['id']."x".$size_str."_0\" value=\"".$mat_useage."\" $disabled>".
									'<input type="hidden" name="PHP_bom_o_qty'.$acc_used[$i]['id'].'x'.$size_str.'_0" value="'.$mat_all.'">'.
								"</td>\n";
					
				$T_bom['list']			= $html;
				$op['bom_acc_list'][]	= $T_bom;
			}		
		}else if($acc_used[$i]['arrange'] == 3){
			// --------  bom ��J�C [ �Ʈ� ] ����⤣��X ------------
				$T_bom['change'] = 1;
				$T_bom['tr'] = " bgcolor=#d6d6d6";
				$T_bom['acc_code']		= $acc_used[$i]['acc_code'];
				$T_bom['acc_name']		= $acc_used[$i]['acc_name'];
				$T_bom['acc_use']		= $acc_used[$i]['use_for'];
				$T_bom['est_1']			= $acc_used[$i]['est_1'];
				$T_bom['unit']			= $acc_used[$i]['unit'];
				$T_bom['ap_mark']		= '';
				$T_bom['size']			= '';
				$T = "<input type='text' size='15' name=\"PHP_acc_color_".$acc_used[$i]['id']."\" value='".$acc_used[$i]['color']."' size=3 class=select onkeyup=value=value.replace(/[^\w\-\u4e00-\u9fa5\.\*\s]/ig,'').toUpperCase()>";
				$T_bom['color']			= $T;
				//$T_a = "<a style='cursor:pointer'><img src='./images/add2.gif' onclick=get_num(this,'".$acc_used[$i]['id']."','".$T_bom['size']."',1,'acc','') border='0'></img></a>";
				$T_a = "<a style='cursor:pointer'><img src='./images/add2.gif' onclick='get_num(this,\"".$acc_used[$i]['id']."\",\"".$T_bom['size']."\",1,\"acc\",\"\")' border='0'></img></a>";
				//$T_a ="<input type='image' src='images/add2.gif'   onclick='cfm_add(this,".$PHP_id.",".$acc_used[$i]['id'].",1,\"acc\",\"\",\"\")'>";

				$T_bom['remark']		= $T_a;
					$html="";
					$mat_useage = 0;
					$mat_all = 0;
					for ($j=0;$j<$num_colors;$j++){
						$mat_useage += $acc_used[$i]['est_1'] * $color_qty[$j];
						$mat_all += $color_qty[$j];
					}
					
					$disabled = $mat_useage == 0 ? 'disabled' : 'checked' ;
					$html=$html."<td class=bgdy9 align=center colspan=$j>
									<input type='checkbox' name=\"PHP_bom_qty".$acc_used[$i]['id']."_0\" value=\"".$mat_useage."\" $disabled>".
									'<input type="hidden" name="PHP_bom_o_qty'.$acc_used[$i]['id'].'_0" value="'.$mat_all.'">'.
								"</td>\n";

				$T_bom['list']			= $html;
				$op['bom_acc_list'][]	= $T_bom;			
		}else{
			// --------  bom ��J�C [ �Ʈ� ] ��� ------------
			  $T_bom['change'] = 1;
				$T_bom['tr'] = " bgcolor=#d6d6d6";
				$T_bom['acc_code']		= $acc_used[$i]['acc_code'];
				$T_bom['acc_name']		= $acc_used[$i]['acc_name'];
				$T_bom['acc_use']		= $acc_used[$i]['use_for'];
				$T_bom['est_1']			= $acc_used[$i]['est_1'];
				$T_bom['unit']			= $acc_used[$i]['unit'];
				$T_bom['ap_mark']		= '';
				$T_bom['size']		= '';
				$T = "<input type='text' size='15' name=\"PHP_acc_color_".$acc_used[$i]['id']."\" value='".$acc_used[$i]['color']."' size=3 class=select onkeyup=value=value.replace(/[^\w\-\u4e00-\u9fa5\.\*\s]/ig,'').toUpperCase()>";
				$T_bom['color']			= $T;
				$T_a = "<a style='cursor:pointer'><img src='./images/add2.gif' onclick=get_num(this,'".$acc_used[$i]['id']."','".$T_bom['size']."',$num_colors,'acc','') border='0'></img></a>";
				//$T_a ="<input type='image' src='images/add2.gif'   onclick='cfm_add(this,".$PHP_id.",".$acc_used[$i]['id'].",".$num_colors.",\"acc\",\"\",\"\")'>";

				$T_bom['remark']		= $T_a;
					$html="";
					for ($j=0;$j<$num_colors;$j++){
						$mat_useage = $acc_used[$i]['est_1'] * $color_qty[$j];
						$html=$html."<td class=bgdy9 align=center onmouseover=\""."overTip(preview,$('#bom_table tr:eq(0) td:eq(".(7+$j).")').val())\" onmouseout=outTip(preview)>
										<input type='checkbox' name=\"PHP_bom_qty".$acc_used[$i]['id']."_$j\" value=\"".$mat_useage."\">".
										'<input type="hidden" name="PHP_bom_o_qty'.$acc_used[$i]['id'].'_'.$j.'" value="'.$color_qty[$j].'">'.
									"</td>\n";
					}
				$T_bom['list']			= $html;
				$op['bom_acc_list'][]	= $T_bom;
				// --------  end bom ��J�C [ �Ʈ� ]  ------------
			}

		for ($k=0;$k<count($bom_acc);$k++){  // �� bom �ƮưO�� �v�� loop
			$T_bom = array();
			$html ='';
			if ($bom_acc[$k]['acc_used_id'] == $acc_used[$i]['id'] ){
				$T_bom['tr'] = " onmouseover=\"bgColor='#D4E0F7'\" onmouseout=\"bgColor='#ffffff'\"";
				$T_bom['acc_code']		= $acc_used[$i]['acc_code'];
				$T_bom['acc_name']		= $acc_used[$i]['acc_name'];
				$T_bom['acc_use']		= $acc_used[$i]['use_for'];
				$T_bom['est_1']			= $acc_used[$i]['est_1'];
				$T_bom['unit']			= $acc_used[$i]['unit'];
				//$del ="<a href=\"javascript:cfm_del('".$bom_acc[$k]['id']."','".$PHP_id."','".$acc_used[$i]['acc_code']."','acc')\" value='Del' style=\"cursor:pointer\"><img src='images/del.png' alt='Del' border=0 valign=bottom></a>";
				$del ="<input type='image' src='images/del.png'   onclick=\"cfm_del_ajx('".$bom_acc[$k]['id']."','".$PHP_id."','".$acc_used[$i]['acc_code']."','acc',this)\">";

				$T_bom['remark']		= $del;
				$T_bom['color']			= $bom_acc[$k]['color'];
				$T_bom['size']			= $bom_acc[$k]['size'];
				if (!isset($bom_acc[$k]['ap_mark'])) $bom_acc[$k]['ap_mark']='';
				$T_bom['ap_mark']		= $bom_acc[$k]['ap_mark'];

				$T_qty = explode(",", $bom_acc[$k]['qty']);
				
				if ($acc_used[$i]['arrange'] ==1 || $acc_used[$i]['arrange'] ==3){
					$T_bom['tr'] = " onmouseover=\"bgColor='#D4E0F7'\" onmouseout=\"bgColor='#ffffff'\"";
					$s_total=0;
					for ($p=0;$p<sizeof($T_qty);$p++) $s_total+=$T_qty[$p];
					$html = $html."<td class=grn9 align=center colspan=$num_colors><b>".$s_total."</b></td>\n";
				}else{
					$T_bom['tr'] = " onmouseover=\"bgColor='#D4E0F7'\" onmouseout=\"bgColor='#ffffff'\"";
					for ($p=0;$p<$num_colors;$p++){
							if ($T_qty[$p]==0){	$T_qty[$p] = "&nbsp;";	}
						$html = $html."<td class=grn9 align=center><b>".$T_qty[$p]."</b></td>\n";
					}
				}
				$T_bom['list']			= $html;
				$op['bom_acc_list'][]	= $T_bom;

			}   // BOM �ɤ��� �Ʈ�id ���� �ή��ɪ�id ����

		}  // end for K... bom�ɤ��Ʈ� �v�� ����


	  }  // end for �� �ƮƥήưO���v�� loop

		  return $op['bom_acc_list'];
	}	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	function bom_lots_copy($num_lots_used,$lots_used,$num_colors,$color_qty) : �զXbom lots��html
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function bom_lots_copy($num_lots_used,$lots_used,$num_colors,$color_qty) {	
		$op['bom_lots_list'] = array();	
	for ($i=0;$i<$num_lots_used;$i++){   // �� �D�ƥήưO���v�� loop
			$html ='';

			// --------  bom ��J�C [ �D�� ] ------------
				$T_bom['tr'] = " bgcolor=#d6d6d6";
				$T_bom['change'] = 1;
				$T_bom['lots_code']		= $lots_used[$i]['lots_code'];
				$T_bom['id']					= $lots_used[$i]['id'];	

				$T_bom['est_1']			= $lots_used[$i]['est_1'];
					for ($j=0;$j<$num_colors;$j++){
						$mat_useage = $lots_used[$i]['est_1'] * $color_qty[$j];
						$T_bom['qty'][$j]=$mat_useage;
					}
				$lots_copy[]	= $T_bom;
	  
	  }  // end for �� �D�ƥήưO���v�� loop

		  return $lots_copy;
	}		
	


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	function bom_acc_copy($num_acc_used,$acc_used,$num_colors,$color_qty,$size,$size_qty) : �զXbom lots��html
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function bom_acc_copy($num_acc_used,$acc_used,$num_colors,$color_qty,$size,$size_qty) {	
		$acc_copy = array();	
	  for ($i=0;$i<$num_acc_used;$i++){   // �� �ƮƥήưO���v�� loop
		if($acc_used[$i]['arrange'] == 2)
		{
			for ($s=0; $s<sizeof($size); $s++)
			{// --------  bom ��J�C [ �Ʈ� ] ����X ------------
				$T_bom['acc_code']		= $acc_used[$i]['acc_code'];
				$T_bom['id']					= $acc_used[$i]['id'];
				$T_bom['size']		= $size[$s];
				$T_bom['est_1']			= $acc_used[$i]['est_1'];
					for ($j=0;$j<$num_colors;$j++){
						$mat_useage = $acc_used[$i]['est_1'] * $size_qty[$size[$s]][$j];
						$T_bom['qty'][$j]=$mat_useage;
					}
				$acc_copy[]	= $T_bom;
				
			}
		}else if($acc_used[$i]['arrange'] == 1){
			for ($s=0; $s<sizeof($size); $s++)
			{// --------  bom ��J�C [ �Ʈ� ] ��X ------------
				$T_bom['acc_code']		= $acc_used[$i]['acc_code'];
				$T_bom['id']					= $acc_used[$i]['id'];
				$T_bom['est_1']			= $acc_used[$i]['est_1'];
				$T_bom['size']		= $size[$s];
				$mat_useage=0;
				for ($j=0;$j<$num_colors;$j++){
					$mat_useage += $acc_used[$i]['est_1'] * $size_qty[$size[$s]][$j];
				}
				$T_bom['qty'][0]=$mat_useage;
				$acc_copy[]	= $T_bom;				
			}		
		}else if($acc_used[$i]['arrange'] == 3){
			// --------  bom ��J�C [ �Ʈ� ] ����⤣��X ------------
				$T_bom['acc_code']		= $acc_used[$i]['acc_code'];
				$T_bom['id']					= $acc_used[$i]['id'];
				$T_bom['est_1']				= $acc_used[$i]['est_1'];
				$T_bom['size']				= '';
				$mat_useage = 0;
				for ($j=0;$j<$num_colors;$j++){
					$mat_useage += $acc_used[$i]['est_1'] * $color_qty[$j];
				}
				$T_bom['qty'][0]=$mat_useage;
				$acc_copy[]	= $T_bom;			
		}else{
			// --------  bom ��J�C [ �Ʈ� ] ��� ------------
				$T_bom['acc_code']		= $acc_used[$i]['acc_code'];
				$T_bom['id']					= $acc_used[$i]['id'];
				$T_bom['est_1']				= $acc_used[$i]['est_1'];
				$T_bom['size']				= '';

					for ($j=0;$j<$num_colors;$j++){
						$mat_useage = $acc_used[$i]['est_1'] * $color_qty[$j];
						$T_bom['qty'][$j]=$mat_useage;
					}

				$acc_copy[]	= $T_bom;

				// --------  end bom ��J�C [ �Ʈ� ]  ------------
			}
	  }  // end for �� �ƮƥήưO���v�� loop

				


		  return $acc_copy;
	}	

	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	function bubble_sort($array) : ��w�Ƨ�  �j----->�p
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	
function bubble_sort($array){
    $count = count($array);
    if ($count <= 0) return false;

    for($i=0; $i<$count; $i++){
        for($j=$count-1; $j>$i; $j--){
            if ($array[$j]['sort'] > $array[$j-1]['sort']){
                $tmp = $array[$j];
                $array[$j] = $array[$j-1];
                $array[$j-1] = $tmp;
            }
        }
    }
    return $array;
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	function bubble_sort($array) : ��w�Ƨ�  �p----->�j
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

function bubble_sort_s($array){
    $count = count($array);
    if ($count <= 0) return false;
   
    for($i=0; $i<$count; $i++){
        for($j=$count-1; $j>$i; $j--){
            if ($array[$j]['sort'] < $array[$j-1]['sort']){
                $tmp = $array[$j];
                $array[$j] = $array[$j-1];
                $array[$j-1] = $tmp;
            }
        }
    }
    return $array;
}

# �j----->�p
function bubble_sort_desc($array){
    $count = count($array);
    if ($count <= 0) return false;

    for($i=0; $i<$count; $i++){
        for($j=$count-1; $j>$i; $j--){
            if ($array[$j]['sort'] < $array[$j-1]['sort']){
                $tmp = $array[$j];
                $array[$j] = $array[$j-1];
                $array[$j-1] = $tmp;
            }
        }
    }
    return $array;
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	function get_ord_status($status) : ���o�q�檺���A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	
function get_ord_status($status){
 	if ($status==0) return "wait for IE";
	if ($status==1) return "wait for submit";
	if ($status==2) return "Waiting for CFM";
	if ($status==3) return "Waiting for APV";
	if ($status==4) return "APVD";
	if ($status==5) return "Reject";
	if ($status==6) return "SCHDL CFMing";
	if ($status==7) return "SCHDL CFMed";
	if ($status==8) return "PRODUCING";
	if ($status==10) return "FINISH";
	if ($status==12) return "SHIPPED";
	if ($status==13) return "Waiting for Review";
	if ($status==14) return "==���X�f�A���`�I==";
} 

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	function get_status() : ���o���A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	
function get_status(){
 	$status[0] = "wait for IE";
	$status[1] = "wait for submit";
	$status[2] = "Waiting for CFM";
	$status[3] = "Waiting for APV";
	$status[4] = "APVD";
	$status[5] = "Reject";
	$status[6] = "SCHDL CFMing";
	$status[7] = "SCHDL CFMed";
	$status[8] = "PRODUCING";
	$status[10] = "FINISH";
	$status[12] = "SHIPPED";
	$status[13] = "Waiting for Review";
	$status[14] = "==���X�f�A���`�I==";
    return $status;
} 

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	function get_po_status($status) : ���o�q�檺���A
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

function get_po_status($status){
 	if ($status==-1) return "DELETED";
 	if ($status==0) return "Waiting P/A submit";
	if ($status==2) return "Waiting P/A CFM";
	if ($status==3) return "Waiting P/A APV";
	if ($status==4) return "<b>P/A APVD</b>";
	if ($status==5) return "<b>P/A Reject</b>";
	if ($status==6) return "Waiting P/O submit";
	if ($status==8) return "Waiting P/O CFM";
	if ($status==10) return "Waiting P/O APV";
	if ($status==12) return "<b>P/O APVD</b>";
	if ($status==13) return "<b>P/O Reject</b>";
} 

function get_material_status($status){
    $status_txt = '';
    switch($status){
    
        case '0':
        $status_txt = 'Un Submit';
        break;
        
        case '2':
        $status_txt = 'Un Confirm';
        break;
        
        case '4':
        $status_txt = 'Confirm';
        break;
        
        default:
        $status_txt = '';
        break;
    }
    return $status_txt;
} 

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	function get_fy_salary($qty,$worker,$spt) : Monitor�p��uú
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	
function get_fy_salary($det,$worker,$ie){ 
		$base_salary = 4500; //�򥻤u��
		$base_qty = 100;	//�򥻥Ͳ��q
		$up_qty = 10;			//�֭p�Ͳ��q
		$each_salary = 50000; //�򥻥Ͳ��q���u��
		$up_salary = 10000;  //�֭p�Ͳ��q���u��
		$tmp_up_qty = 0;
		$tmp_base_qty = 0;
		$TTL_WRNT = 0;
		
//		$su =(int)($qty * ($spt / 3400));
/*
		$su = $qty * $ie;
		if ($su >= $base_qty )
		{
			$tmp_up_qty = (int)(($su - $base_qty ) / $up_qty);
			$TTL_WRNT = ($each_salary + $tmp_up_qty * $up_salary) * $worker;
		}
		$salary = ($base_salary*$su + $TTL_WRNT );
		if ($qty == 0) $salary = 0;
		$salary = $salary / 16000;
		return $salary;
*/
} 


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	function get_fy_salary($qty,$worker,$spt) : Monitor�p��uú
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	
function change_unit_price($PHP_ounit,$PHP_nunit,$price){ 
	// echo $PHP_ounit.','.$PHP_nunit.','.$price;
	$unit_change[0]['S'] = 'yd';
	$unit_change[0]['B'] = 'meter';
	$unit_change[0]['R'] = '1.093613';
	
	$unit_change[1]['S'] = 'lb';
	$unit_change[1]['B'] = 'kg';
	$unit_change[1]['R'] = '2.20462';
	
	$unit_change[2]['S'] = 'pc';
	$unit_change[2]['B'] = 'gross';
	$unit_change[2]['R'] = '144';

	$unit_change[3]['S'] = 'pc';
	$unit_change[3]['B'] = 'dz';
	$unit_change[3]['R'] = '12';

	$unit_change[4]['S'] = 'dz';
	$unit_change[4]['B'] = 'gross';
	$unit_change[4]['R'] = '12';

	$unit_change[5]['S'] = 'inch';
	$unit_change[5]['B'] = 'ft';
	$unit_change[5]['R'] = '12';

	$unit_change[6]['S'] = 'inch';
	$unit_change[6]['B'] = 'yd';
	$unit_change[6]['R'] = '36';
	
	$unit_change[7]['S'] = 'inch';
	$unit_change[7]['B'] = 'meter';
	$unit_change[7]['R'] = '39.37';

	$unit_change[8]['S'] = 'ft';
	$unit_change[8]['B'] = 'yd';
	$unit_change[8]['R'] = '3';

	$unit_change[9]['S'] = 'ft';
	$unit_change[9]['B'] = 'meter';
	$unit_change[9]['R'] = '3.28084';

	$unit_change[10]['S'] = 'set';
	$unit_change[10]['B'] = 'gross';
	$unit_change[10]['R'] = '144';

	$unit_change[11]['S'] = 'set';
	$unit_change[11]['B'] = 'dz';
	$unit_change[11]['R'] = '12';

	$unit_change[12]['S'] = 'pc';
	$unit_change[12]['B'] = '100pcs';
	$unit_change[12]['R'] = '100';

	$unit_change[13]['S'] = 'pc';
	$unit_change[13]['B'] = 'K pcs';
	$unit_change[13]['R'] = '1000';

	$unit_change[14]['S'] = 'dz';
	$unit_change[14]['B'] = '100pcs';
	$unit_change[14]['R'] = '8.333';

	$unit_change[15]['S'] = 'dz';
	$unit_change[15]['B'] = 'K pcs';
	$unit_change[15]['R'] = '83.333';

	$unit_change[16]['S'] = '100pcs';
	$unit_change[16]['B'] = 'gross';
	$unit_change[16]['R'] = '1.44';

	$unit_change[17]['S'] = 'gross';
	$unit_change[17]['B'] = 'K pcs';
	$unit_change[17]['R'] = '6.944';

	$unit_change[18]['S'] = '100pcs';
	$unit_change[18]['B'] = 'K pcs';
	$unit_change[18]['R'] = '10';


	if ($PHP_ounit == $PHP_nunit) return $price;
	
	for ($i=0; $i<sizeof($unit_change); $i++)
	{
		if ($unit_change[$i]['S'] == $PHP_ounit && $unit_change[$i]['B'] == $PHP_nunit )
		{
			$price = $price * $unit_change[$i]['R'];
			return $price;
		}
		if ($unit_change[$i]['B'] == $PHP_ounit && $unit_change[$i]['S'] == $PHP_nunit )
		{
			$price = $price / $unit_change[$i]['R'];
			return $price;
		}
	}
	return 0;
} 


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	function change_unit_qty($PHP_ounit,$PHP_nunit,$price)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// function change_unit_qty($PHP_ounit,$PHP_nunit,$qty){ 
function change_unit_qty($PHP_ounit,$PHP_nunit,$qty,$weight=''){
	/* echo $PHP_ounit.",";
	echo $PHP_nunit.",";
	echo $qty.",";
	echo $weight."<br>";  */
	$unit_change[0]['S'] = 'yd';
	$unit_change[0]['B'] = 'meter';
	$unit_change[0]['R'] = '1.093613';

	$unit_change[1]['S'] = 'lb';
	$unit_change[1]['B'] = 'kg';
	$unit_change[1]['R'] = '2.20462';

	$unit_change[2]['S'] = 'pc';
	$unit_change[2]['B'] = 'gross';
	$unit_change[2]['R'] = '144';

	$unit_change[3]['S'] = 'pc';
	$unit_change[3]['B'] = 'dz';
	$unit_change[3]['R'] = '12';

	$unit_change[4]['S'] = 'dz';
	$unit_change[4]['B'] = 'gross';
	$unit_change[4]['R'] = '12';

	$unit_change[5]['S'] = 'inch';
	$unit_change[5]['B'] = 'ft';
	$unit_change[5]['R'] = '12';

	$unit_change[6]['S'] = 'inch';
	$unit_change[6]['B'] = 'yd';
	$unit_change[6]['R'] = '36';

	$unit_change[7]['S'] = 'inch';
	$unit_change[7]['B'] = 'meter';
	$unit_change[7]['R'] = '39.37';

	$unit_change[8]['S'] = 'ft';
	$unit_change[8]['B'] = 'yd';
	$unit_change[8]['R'] = '3';

	$unit_change[9]['S'] = 'ft';
	$unit_change[9]['B'] = 'meter';
	$unit_change[9]['R'] = '3.28084';

	$unit_change[10]['S'] = 'set';
	$unit_change[10]['B'] = 'gross';
	$unit_change[10]['R'] = '144';

	$unit_change[11]['S'] = 'set';
	$unit_change[11]['B'] = 'dz';
	$unit_change[11]['R'] = '12';

	$unit_change[12]['S'] = 'pc';
	$unit_change[12]['B'] = '100pcs';
	$unit_change[12]['R'] = '100';

	$unit_change[13]['S'] = 'pc';
	$unit_change[13]['B'] = 'K pcs';
	$unit_change[13]['R'] = '1000';

	$unit_change[14]['S'] = '100pcs';
	$unit_change[14]['B'] = 'K pcs';
	$unit_change[14]['R'] = '10';

	$unit_change[15]['S'] = 'yd';
	$unit_change[15]['B'] = 'g/yd.';
	$unit_change[15]['R'] = '1000';

	$unit_change[16]['S'] = 'meter';
	$unit_change[16]['B'] = 'g/yd.';
	$unit_change[16]['R'] = '1000';

	$unit_change[17]['S'] = 'inch';
	$unit_change[17]['B'] = 'g/yd.';
	$unit_change[17]['R'] = '1000';

	$unit_change[18]['S'] = 'ft';
	$unit_change[18]['B'] = 'g/yd.';
	$unit_change[18]['R'] = '1000';
	/* echo "==========================================<br>"; */
	if ($PHP_ounit == $PHP_nunit) 
	{
	/* echo $qty;
	echo '<br>'; 
	echo "***********************************<br>"; */
	return $qty;	
	}
	for ($i=0; $i<sizeof($unit_change); $i++){

		if ($unit_change[$i]['S'] == $PHP_ounit && $unit_change[$i]['B'] == $PHP_nunit && $PHP_nunit == 'g/yd.'){
			/* echo $qty.'x'.$weight.'/'.$unit_change[$i]['R']; */
			$qty = $qty * $weight / $unit_change[$i]['R'];
			/* echo '='.number_format($qty,2,'',''); */
			
			/* echo '<br>'; 
			echo "***********************************<br>"; */
			return number_format($qty,2,'','');
		}
    
		if ($unit_change[$i]['S'] == $PHP_ounit && $unit_change[$i]['B'] == $PHP_nunit){
			/* echo $qty.'/'.$unit_change[$i]['R']; */
			$qty= $qty / $unit_change[$i]['R'];
			/* echo '='.number_format($qty,2,'',''); */
			
			/* echo '<br>'; 
			echo "***********************************<br>"; */
			return number_format($qty,2,'','');
		}

		if ($unit_change[$i]['B'] == $PHP_ounit && $unit_change[$i]['S'] == $PHP_nunit){
			/* echo $qty.'x'.$unit_change[$i]['R']; */
			$qty = $qty * $unit_change[$i]['R'];
			/* echo '='.number_format($qty,2,'',''); */
			
			/* echo '<br>'; 
			echo "***********************************<br>"; */
			return number_format($qty,2,'','');
		}
	}
	//echo "finish<br>";
	return false;

} 

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	function change_unit_set($PHP_ounit,$PHP_nunit)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function change_unit_set($PHP_ounit,$PHP_nunit){

	$unit_change[0]['S'] = 'yd';
	$unit_change[0]['B'] = 'meter';
	$unit_change[0]['R'] = '1.093613';

	$unit_change[1]['S'] = 'yd';
	$unit_change[1]['B'] = 'g/yd.';
	$unit_change[1]['R'] = '1000';

	$unit_change[2]['S'] = 'lb';
	$unit_change[2]['B'] = 'kg';
	$unit_change[3]['R'] = '2.20462';

	$unit_change[3]['S'] = 'pc';
	$unit_change[3]['B'] = 'gross';
	$unit_change[3]['R'] = '144';

	$unit_change[4]['S'] = 'pc';
	$unit_change[4]['B'] = 'dz';
	$unit_change[5]['R'] = '12';

	$unit_change[5]['S'] = 'pc';
	$unit_change[5]['B'] = '100pcs';
	$unit_change[5]['R'] = '100';

	$unit_change[6]['S'] = 'pc';
	$unit_change[6]['B'] = 'K pcs';
	$unit_change[6]['R'] = '1000';
	
	$unit_change[7]['S'] = 'dz';
	$unit_change[7]['B'] = 'gross';
	$unit_change[7]['R'] = '12';
	
	$unit_change[8]['S'] = 'inch';
	$unit_change[8]['B'] = 'ft';
	$unit_change[8]['R'] = '12';
	
	$unit_change[9]['S'] = 'inch';
	$unit_change[9]['B'] = 'yd';
	$unit_change[9]['R'] = '36';

	$unit_change[10]['S'] = 'inch';
	$unit_change[10]['B'] = 'meter';
	$unit_change[10]['R'] = '39.37';

	$unit_change[11]['S'] = 'inch';
	$unit_change[11]['B'] = 'g/yd.';
	$unit_change[11]['R'] = '1000';

	$unit_change[12]['S'] = 'ft';
	$unit_change[12]['B'] = 'yd';
	$unit_change[12]['R'] = '3';

	$unit_change[13]['S'] = 'ft';
	$unit_change[13]['B'] = 'meter';
	$unit_change[13]['R'] = '3.28084';

	$unit_change[14]['S'] = 'ft';
	$unit_change[14]['B'] = 'g/yd.';
	$unit_change[14]['R'] = '1000';

	$unit_change[15]['S'] = 'set';
	$unit_change[15]['B'] = 'gross';
	$unit_change[15]['R'] = '144';

	$unit_change[16]['S'] = 'set';
	$unit_change[16]['B'] = 'dz';
	$unit_change[16]['R'] = '12';

	$unit_change[17]['S'] = '100pcs';
	$unit_change[17]['B'] = 'K pcs';
	$unit_change[17]['R'] = '10';

	$unit_change[18]['S'] = 'meter';
	$unit_change[18]['B'] = 'g/yd.';
	$unit_change[18]['R'] = '1000';
	
	if ($PHP_ounit == $PHP_nunit) return 1;	
	
	for ($i=0; $i<sizeof($unit_change); $i++) {

		if ($unit_change[$i]['S'] == $PHP_ounit && $unit_change[$i]['B'] == $PHP_nunit) {
			return $unit_change[$i]['R'];
		}

		if ($unit_change[$i]['B'] == $PHP_ounit && $unit_change[$i]['S'] == $PHP_nunit) {
			return $unit_change[$i]['R'];
		}

	}

	return false;
} 


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	function big52uni($text) html ������ uni
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

 function big52uni($text) {
    $rtext="";
    $max=strlen($text);
    for($i=0;$i<$max;$i++){
      $h=ord($text[$i]);
      if($h >= 160 && $i < $max-1){
        $rtext.="&#".base_convert(bin2hex(iconv("big5","ucs-2",substr($text,$i,2))),16,10).";";
        $i++;
      }else{
        $rtext.=$text[$i];
      }
    }
    return $rtext;
  }



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	function _pic_url($style='small',$size='50',$sence,$folder,$order_num,$type='jpg',$now_img='',$pic_num='')
# �Y��ϫ�
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

	function _pic_url($style='small',$size='50',$sence,$folder,$order_num,$type='jpg',$now_img='',$pic_num=''){
		# style �w�]�j��  = 0 �p��
		# size �ؤo
		# pic_num �r��

		$mb = md5(date('his'));
		#$pic_now = ( $now_img >= '0' )? '_'.$now_img : '';

		$pic_dir	= "./".$folder."/";
		$style=($style=='big')?'':'s_';
		$full_dir = $pic_dir.$style.$order_num.'.'.$type;
		$full_pic = $pic_dir.$order_num.'.'.$type;

		if(file_exists($full_dir)){
			$pic = $full_dir.'?'.$mb;
		}else{
			$full_pic = './images/no_images.gif?'.$mb;
			$pic = './images/'.$style.'no_images.gif?'.$mb;
		}

		if( !empty($pic_num) ){
			$pic_str = '';
			for($i=0;$i < $pic_num;$i++){
				if(file_exists($GLOBALS['config']['root_dir'].$pic_dir.$order_num.'_'.$i.".jpg")){
					$pic_str = $pic_str .  $i."|";
				}
			}
			$pic_num = substr($pic_str,0,-1);
		}

		if($sence==1){
			$sizes='width="'.$size.'" ';
			$pic = '<img id="pic" src="'.$pic.'?'.$mb.'" name="pic" '.$sizes.' border=0 >';
		}else if($sence==2){
			$sizes='width="'.$size.'" height="'.$size.'"';
			$pic = '<a href="javascript://" onClick="window.open2('."'pop_img.php?PHP_title=".$order_num."&PHP_pic_url=".$full_pic."&PHP_type=".$folder."&PHP_pic_num=".$pic_num."&PHP_now_img=".$now_img."','','resizable=yes,menubar=no,status=no,scrollbars=no,width=1,height=1,top=20,left=50,toolbar=no'".')" ><img id="pic" src="'.$pic.'" name="pic" '.$sizes.' border=0 ></a>';
		}else if($sence==3){
			$sizes='width="'.$size.'"';
			$pic = '<a href="javascript://" onClick="window.open2('."'pop_img.php?PHP_title=".$order_num."&PHP_pic_url=".$full_pic."&PHP_type=".$folder."&PHP_pic_num=".$pic_num."&PHP_now_img=".$now_img."','','resizable=yes,menubar=no,status=no,scrollbars=no,width=1,height=1,top=20,left=50,toolbar=no'".')" ><img id="pic" src="'.$pic.'" name="pic" '.$sizes.' border=0 ></a>';
		}else if($sence==4){
			$sizes='width="'.$size.'"';
			$pic = '<a href="javascript://" onClick="window.open2('."'pop_img.php?PHP_title=".$order_num."&PHP_type=".$folder."&PHP_pic_num=".$pic_num."&PHP_now_img=".$now_img."','','resizable=yes,menubar=no,status=no,scrollbars=no,width=1,height=1,top=20,left=50,toolbar=no'".')" ><img id="pic2" src="'.$pic_dir.$order_num.'_'.$now_img.'.'.$type.'" name="pic2" '.$sizes.' border=0 onMouseOver="change_picture(\''.$pic_dir.$order_num.'_'.$now_img.'.'.$type.'\')" onMouseOut="change_picture(\''.$full_pic.'\')"></a>';
		}else{
			$pic = $pic;
		}


		return $pic;
	}



#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	function   getnum($temp)
# �ഫ�ʦ�ƥH�����Ʀr���^��r
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function getnum($temp){   
	$result ='';
  $number=array(1=>"ONE",2=>"TWO",3=>"THREE",4=>"FOUR",5=>"FIVE",6=>"SIX",7=>"SEVEN",8=>"EIGHT",9=>"NINE",10=>"TEN"); 
  $ten_number=array(11=>"ELEVEN",12=>"TWELVE",13=>"THIRTEEN",14=>"FOURTEEN",15=>"FIFTEEN",16=>"SIXTEEN",17=>"SEVENTEEN",18=>"EIGHTEEN",19=>"NINETEEN");   
  $uten_number=array(2=>"TWENTY",3=>"THIRTY",4=>"FORTY",5=>"FIFTY",6=>"SIXTY",7=>"SEVENTY",8=>"EIGHTY",9=>"NINETY");   
  
  if($temp<100){   
  $hug=0;   
  $ten=$temp;   
  }else{   
  $hug=floor($temp/100);   
  $ten=$temp%100;   
  }   
  
  if($hug<>0){   
  	$result=$number[$hug]."   HUNDRED";   
  }  
  $ten = (int)$ten;
  if($ten)
  {
  	if($ten<11){   
  		$result.="   ".$number[$ten];   
  	}elseif($ten<20){   
  		$result.="   ".$ten_number[$ten];   
  	}else{   
  		if($ten%10==0)   $result.="   ".$uten_number[$ten/10];   
  		else   $result.="   ".$uten_number[floor($ten/10)]." ".$number[$ten%10];   
  	}
  }   
  return   $result;   
  }   
   
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	function engilsh_math($num)
# �ഫ�Ʀr���^��
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  
  function engilsh_math($num){   
		
		$num = NUMBER_FORMAT($num,2,'','');
		$math = array(0=>"", 1=>"THOUSAND",2=>"MILLION",3=>"BILLION",4=>"TRILLION",5=>"QUADRILLION",6=>"QUINTILLION",7=>"SEXTILLION",8=>"SEPTILLION",9=>"OCTILLION",10=>"NONILLION",11=>"DECILLION",12=>"UNDECILLION",13=>"DUODECILLION",14=>"TREDECILLION",15=>"SEPTEMDECILLION",16=>"OCTODECILLION",17=>"NOVEMDECILLION",18=>"VIGINTILLION");   
  	$rst = '';
  	$tmp=explode('.',$num);  
  	$numtmp  = $tmp[0];
  	for($i=0;$i<ceil(strlen($tmp[0])/3);$i++){   
  		$arr[$i]=substr($numtmp,-3,3);   
  		$numtmp=substr($numtmp,0,-3);   
  		
  	} 
  	
  	for($i=count($arr)-1;$i>=0;$i--){   
  		
  		$rst.=getnum($arr[$i])." ".$math[$i]." ";   
  	}   
  	$arr = array();
  	if($tmp[1] > 0)
  	{
  		$numtmp  = $tmp[1];
  		for($i=0;$i<ceil(strlen($tmp[1])/3);$i++){   
  			$arr[$i]=substr($numtmp,-3,3);   
  			$numtmp=substr($numtmp,0,-3);   	
  		} 
  		$rst.=" AND ";
  		for($i=count($arr)-1;$i>=0;$i--){    			
  			$rst.=getnum($arr[$i])." ".$math[$i]." ";   
  		}   
  		$rst.=" CENTS "; 	
		}
		return $rst;  
		
  } 
  
  
  
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	group_order_style($style)
# �P�M�ڥܬ��W���ΤU��
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  
function group_order_style($style){   
    
    if ($style=='PS' || $style=='BS' || $style=='BZ' || $style=='DR' || $style=='JK' || $style=='PS-J' || $style=='PS-P' || $style=='PS-S' || $style=='VS' || $style=='SS'){
        return "top";
    }else{
        return "btm.";
    }		
    
} 
  
//�B�zjavascript escape�r����X
function uniDecode($str,$charcode){
	$text = preg_replace_callback("/%u[0-9A-Za-z]{4}/",toUtf8,$str);
	return mb_convert_encoding($text, $charcode, 'utf-8');
}
function toUtf8($ar){
	foreach($ar as $val){
		$val = intval(substr($val,2),16);
		if($val < 0x7F){        // 0000-007F
			$c .= chr($val);
		}elseif($val < 0x800) { // 0080-0800
			$c .= chr(0xC0 | ($val / 64));
			$c .= chr(0x80 | ($val % 64));
		}else{                // 0800-FFFF
			$c .= chr(0xE0 | (($val / 64) / 64));
			$c .= chr(0x80 | (($val / 64) % 64));
			$c .= chr(0x80 | ($val % 64));
		}
	}
return $c;
}


function Size($path)
{
    $bytes = sprintf('%u', filesize($path));

    if ($bytes > 0)
    {
        $unit = intval(log($bytes, 1024));
        $units = array('B', 'KB', 'MB', 'GB');

        if (array_key_exists($unit, $units) === true)
        {
            return sprintf('%d %s', $bytes / pow(1024, $unit), $units[$unit]);
        }
    }

    return $bytes;
}


	function umoney($num,$type="usd") { 
		global $numTable,$commaTable,$moneyType; 

		//global $numTable; 
		$numTable[0]="ZERO "; 
		$numTable[1]="ONE "; 
		$numTable[2]="TWO "; 
		$numTable[3]="THREE "; 
		$numTable[4]="FOUR "; 
		$numTable[5]="FIVE "; 
		$numTable[6]="SIX "; 
		$numTable[7]="SEVEN "; 
		$numTable[8]="EIGHT "; 
		$numTable[9]="NINE "; 
		$numTable[10]="TEN "; 
		$numTable[11]="ELEVEN "; 
		$numTable[12]="TWELVE "; 
		$numTable[13]="THIRTEEN "; 
		$numTable[14]="FOURTEEN "; 
		$numTable[15]="FIFTEEN "; 
		$numTable[16]="SIXTEEN "; 
		$numTable[17]="SEVENTEEN "; 
		$numTable[18]="EIGHTEEN "; 
		$numTable[19]="NINETEEN "; 
		$numTable[20]="TWENTY "; 
		$numTable[30]="THIRTY "; 
		$numTable[40]="FORTY "; 
		$numTable[50]="FIFTY "; 
		$numTable[60]="SIXTY "; 
		$numTable[70]="SEVENTY "; 
		$numTable[80]="EIGHTY "; 
		$numTable[90]="NINETY "; 

		$commaTable[0]="HUNDRED "; 
		$commaTable[1]="THOUSAND "; 
		$commaTable[2]="MILLION "; 
		$commaTable[3]="MILLIARD "; 
		$commaTable[4]="BILLION "; 
		$commaTable[5]="????? "; 

		//��� 
		$moneyType["usd"]="DOLLARS "; 
		//$moneyType["usd_1"]="CENTS ONLY"; 
		$moneyType["usd_1"]=" ONLY"; 
		$moneyType["rmb"]="YUAN "; 
		$moneyType["rmb_1"]="FEN ONLY"; 

		if($type=="") $type="usd"; 
		$fnum = fmoney($num); 
		$numArray = explode(",",$fnum); 
		$resultArray = array(); 
		$k=0; 
		$cc=count($numArray); 
		//print_r($numArray);
		//exit;
		for($i = 0; $i < count($numArray); $i++) { 
		$num_str = $numArray[$i]; 
			//echo "<br>"; 
			//�p�Ʀ쪺�B�z400.21 
			if(eregi("\.",$num_str)) { 
				$dotArray = explode(".",$num_str); 
				//print_r($dotArray);
				if($dotArray[1] != 0) { 
					$resultArray[$k++]=format3num($dotArray[0]+0); 
					//$resultArray[$k++]=$moneyType[strtolower($type)]; 
					//$resultArray[$k++]="AND "; 
					$resultArray[$k++]=" AND CENTS "; 
					$resultArray[$k++]=format3num($dotArray[1]+0); 
					$resultArray[$k++]=$moneyType[strtolower($type)."_1"]; 
				} else { 
					$resultArray[$k++]=format3num($dotArray[0]+0); 
					$resultArray[$k++]=$moneyType[strtolower($type)]; 
				} 
			} else { 
				//�D�p�Ʀ쪺�B�z 
				if(($num_str+0)!=0) { 
					$resultArray[$k++]=format3num($num_str+0); 
					$resultArray[$k++]=$commaTable[--$cc]; 
					//�P�_�G���p�ƥ~��l�Y�����s�h�[and 
					for($j=$i; $j <= $cc; $j++) { 
						//echo "<br>"; 
						//echo $numArray[$j]; 
						if($numArray[$j] !=0) { 
							$resultArray[$k++]=" AND "; 
							break; 
						} 
					} 
				} 
			} 
		} 
		//print_r($resultArray);
		return join("",$resultArray); 
	} 

	function format3num($num) { 
		global $numTable,$commaTable; 
		$numlen = strlen($num); 
		for($i = 0,$j = 0;$i < $numlen; $i++) { 
			$bitenum[$j++] = substr($num,$i,1); 
		} 
		if($num==0) return ""; 
		if($numlen == 1) return $numTable[$num]; 
		if($numlen == 2) { 
			if($num <= 20) return $numTable[$num]; 
			//�Ĥ@�줣�i��s 
			if($bitenum[1]==0) { 
				return $numTable[$num]; 
			} else { 
				return trim($numTable[$bitenum[0]*10])."-".$numTable[$bitenum[1]]; 
			} 

		} 
		//�Ĥ@�Ӥ��i�ର�s 
		if($numlen == 3) { 
			if($bitenum[1]==0 && $bitenum[2]==0) { 
				//100 
				return $numTable[$bitenum[0]].$commaTable[0]; 
			} elseif($bitenum[1]==0) { 
				//102 
				return $numTable[$bitenum[0]].$commaTable[0].$numTable[$bitenum[2]]; 
			} elseif ($bitenum[2]==0) { 
				//120 
				return $numTable[$bitenum[0]].$commaTable[0].$numTable[$bitenum[1]*10]; 
			} else { 
				//123 
				return $numTable[$bitenum[0]].$commaTable[0].trim($numTable[$bitenum[1]*10])."-".$numTable[$bitenum[2]]; 
			} 
		} 
		return $num; 
	}
	function fmoney($num) { 
		$num=0+$num; 
		$num = sprintf("%.02f",$num); 
		if(strlen($num) <= 6) return $num; 
		//�q�̫�}�l��_�A�C3�Ӽƥ��[�@��"," 
		for($i=strlen($num)-1,$k=1, $j=100; $i >= 0; $i--,$k++) { 
			$one_num = substr($num,$i,1); 
			if($one_num ==".") { 
				$numArray[$j--] = $one_num; 
				$k=0; 
				continue; 
			} 

			if($k%3==0 and $i!=0) { 
				//�p�G���n�u�ѤU3�ӼƦr�A�h���[',' 
				$numArray[$j--] = $one_num; 
				$numArray[$j--] = ","; 
				$k=0; 
			} else { 
				$numArray[$j--]=$one_num; 
			} 
		} 
		ksort($numArray); 
		return join("",$numArray); 
	}
?>
