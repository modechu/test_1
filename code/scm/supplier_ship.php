<?php
#
#
#
session_start();
// echo $PHP_action.'<br>';
#
#
#
require_once "config.php";
require_once "config.admin.php";
require_once "init.object.php";
include_once($config['root_dir']."/lib/po_supl.class.php");

$PO_SUPL = new PO_SUPL();
if (!$PO_SUPL->init($mysql,"log")) { print "error!! cannot initialize database for LOG class"; exit; }


// print_r($mysql);
#
#
#
$PHP_SELF = $_SERVER['PHP_SELF'];
$perm = $GLOBALS['power'];
$op = array();
#
#
# Template
$TPL_ARRIVAL_TIME = 'arrival_time.html';
$TPL_ARRIVAL_TIME_SEARCH = 'arrival_time_search.html';
#
#
#
$AUTH = '091';
#
#
#
switch ($PHP_action) {
#
#
#
#
#
#
# :main
default;
case "main":
check_authority($AUTH,"view");

global $SHIP;

unset($_SESSION['SUPL_SHIP']['po_parm']);
unset($_SESSION[$AUTH]);

$op['select_supl'] = $PO_SUPL->select_supl('M_ship','','','','');
$op['select_fty'] = $arry2->select($SHIP,'','M_finl_dist','','');

$op['select_from'] = $PO_SUPL->select_parameter(1,'M_org','','','','');
$op['select_to'] = $PO_SUPL->select_parameter(2,'M_dist','','','','');

$op['css'] = array( 'css/scm.css' );
$op['js'] = array( 'js/jquery.min.js' , 'js/jquery.blockUI.js' , 'js/supl_ship.js' );

page_display($op,$AUTH,'supl_ship_main.html');
break;
#
#
#
#
#
#
# :search_ship 
case "search_ship":
check_authority($AUTH,"view");

$M_ship = !empty($_POST['M_ship'])? $_POST['M_ship'] : (!empty($_SESSION[$AUTH][$PHP_action]['ship'])? $_SESSION[$AUTH][$PHP_action]['ship'] : '' );
$M_bl_num = !empty($_POST['M_bl_num'])? $_POST['M_bl_num'] : (!empty($_SESSION[$AUTH][$PHP_action]['bl_num'])? $_SESSION[$AUTH][$PHP_action]['bl_num'] : '' );
$M_po_num = !empty($_POST['M_po_num'])? $_POST['M_po_num'] : (!empty($_SESSION[$AUTH][$PHP_action]['po_num'])? $_SESSION[$AUTH][$PHP_action]['po_num'] : '' );
$M_org = !empty($_POST['M_org'])? $_POST['M_org'] : (!empty($_SESSION[$AUTH][$PHP_action]['org'])? $_SESSION[$AUTH][$PHP_action]['org'] : '' );
$M_dist = !empty($_POST['M_dist'])? $_POST['M_dist'] : (!empty($_SESSION[$AUTH][$PHP_action]['dist'])? $_SESSION[$AUTH][$PHP_action]['dist'] : '' );
$M_now_num = isset($_GET['M_now_num'])? $_GET['M_now_num'] : (isset($_SESSION[$AUTH][$PHP_action]['now_num'])? $_SESSION[$AUTH][$PHP_action]['now_num'] : '' );

$parm = array(
    'ship'          => $M_ship ,
    'bl_num'        => $M_bl_num ,
    'po_num'  		=> $M_po_num , 
    'org'           => $M_org , 
    'dist'          => $M_dist , 
    'action'        => $PHP_action , 
    'now_num'       => $M_now_num , 
    'page_num'      => 20 , 
);

$op = $PO_SUPL->search_ship($parm);
$_SESSION[$AUTH][$PHP_action] = $parm;

$op['css'] = array( 'css/scm.css' );
$op['js'] = array( 'js/jquery.min.js' , 'js/jquery.blockUI.js' , 'js/supl_ship.js' );

page_display($op,$AUTH,'supl_ship_search_ship.html');
break;
#
#
#
#
#
#
# :search_po 
case "search_po":
check_authority($AUTH,"view");

$M_po_num = !empty($_POST['M_po_num'])? $_POST['M_po_num'] : (!empty($_SESSION[$AUTH][$PHP_action]['po_num'])? $_SESSION[$AUTH][$PHP_action]['po_num'] : '' );
$M_ship = !empty($_POST['M_ship'])? $_POST['M_ship'] : (!empty($_SESSION[$AUTH][$PHP_action]['ship'])? $_SESSION[$AUTH][$PHP_action]['ship'] : '' );
$M_finl_dist = !empty($_POST['M_finl_dist'])? $_POST['M_finl_dist'] : (!empty($_SESSION[$AUTH][$PHP_action]['finl_dist'])? $_SESSION[$AUTH][$PHP_action]['finl_dist'] : '' );

$M_now_num = isset($_GET['M_now_num'])? $_GET['M_now_num'] : (isset($_SESSION[$AUTH][$PHP_action]['now_num'])? $_SESSION[$AUTH][$PHP_action]['now_num'] : '' );

$parm = array(
    'po_num'    => $M_po_num ,
    'ship'      => $M_ship ,
    'finl_dist' => $M_finl_dist , 
    'action'    => $PHP_action , 
    'now_num'   => $M_now_num , 
    'page_num'  => 20 , 
);
// print_r($parm);

$ac = 0;
for( $i=0; $i<sizeof($_SESSION['SUPL_SHIP']['po_parm']); $i++ ) {
	if($_SESSION['SUPL_SHIP']['po_parm'][$i] == $_GET['S_po_num']) $ac++;
}
if ( !empty($_GET['S_po_num']) && $ac === 0 ) $_SESSION['SUPL_SHIP']['po_parm'][] = $_GET['S_po_num'];

$op = $PO_SUPL->search_po($parm,$_SESSION['SUPL_SHIP']['po_parm']);
$_SESSION[$AUTH][$PHP_action] = $parm;
$op['now_po'] = $_SESSION['SUPL_SHIP']['po_parm'];

$op['css'] = array( 'css/scm.css' );
$op['js'] = array( 'js/jquery.min.js' , 'js/jquery.blockUI.js' , 'js/supl_ship.js' );

page_display($op,$AUTH,'supl_ship_search_po.html');
break;
#
#
#
#
#
#
# :ship_chg_status
case "ship_chg_status":
check_authority($AUTH,"edit");

# echo 
if( $PO_SUPL->ship_chg_status($_POST['M_ap_id'],$_POST['M_s_status']) ){
    $message = 'Success Update!, P.O. # '.$_POST['M_po_num'];
    // $_SESSION['MSG'][] = $message;
    $log->log_add(0,$AUTH.'E',$message);
    echo 'true,Success Update!'.$_POST['M_s_status'];
} else {
    echo 'false,Error!'.$_POST['M_s_status'];
}

break;
#
#
#
#
#
#
# :search_po_del 
case "search_po_del":
check_authority($AUTH,"view");

$M_po_num = !empty($_POST['M_po_num'])? $_POST['M_po_num'] : (!empty($_SESSION[$AUTH][$PHP_action]['po_num'])? $_SESSION[$AUTH][$PHP_action]['po_num'] : '' );
$M_ship = !empty($_POST['M_ship'])? $_POST['M_ship'] : (!empty($_SESSION[$AUTH][$PHP_action]['ship'])? $_SESSION[$AUTH][$PHP_action]['ship'] : '' );
$M_finl_dist = !empty($_POST['M_finl_dist'])? $_POST['M_finl_dist'] : (!empty($_SESSION[$AUTH][$PHP_action]['finl_dist'])? $_SESSION[$AUTH][$PHP_action]['finl_dist'] : '' );

$M_now_num = isset($_GET['M_now_num'])? $_GET['M_now_num'] : (isset($_SESSION[$AUTH][$PHP_action]['now_num'])? $_SESSION[$AUTH][$PHP_action]['now_num'] : '' );

$parm = array(
    'po_num'    => $M_po_num ,
    'ship'      => $M_ship ,
    'finl_dist' => $M_finl_dist , 
    'action'    => 'search_po' , 
    'now_num'   => $M_now_num , 
    'page_num'  => 20 , 
);

$po_parm = $_SESSION['SUPL_SHIP']['po_parm'];
unset($_SESSION['SUPL_SHIP']['po_parm']);
for ( $i=0; $i<sizeof($po_parm); $i++ ) {
	if($po_parm[$i] != $_GET['S_po_num']) $_SESSION['SUPL_SHIP']['po_parm'][] = $po_parm[$i];
}

$op = $PO_SUPL->search_po($parm,$_SESSION['SUPL_SHIP']['po_parm']);
$op['now_po'] = $_SESSION['SUPL_SHIP']['po_parm'];

$op['css'] = array( 'css/scm.css' );
$op['js'] = array( 'js/jquery.min.js' , 'js/jquery.blockUI.js' , 'js/supl_ship.js' );

page_display($op,$AUTH,'supl_ship_search_po.html');
break;
#
#
#
#
#
#
# :append_po
case "append_po":
check_authority($AUTH,"add");

$po_parm = $_SESSION['SUPL_SHIP']['po_parm'];

if( $id = $PO_SUPL->append_po($po_parm) ) {

    $message = 'Success Update! S'.$id;
    $log->log_add(0,$AUTH.'A',$message);

    unset($_SESSION['SUPL_SHIP']['po_parm']);

}

// echo '<br>SHIPPING# : '.date('ym').'******<br>';

header("location: ?PHP_action=supl_ship_view&M_id=".$id);
break;
#
#
#
#
#
#
# :supl_ship_submit
case "supl_ship_submit":
check_authority($AUTH,"add");

if( !empty($_POST['M_id']) ) {
    $id =  $_POST['M_id'];
    if( $PO_SUPL->supl_ship_submit($id) ) {
        $message = 'Success Submit! S'.$id;
        $log->log_add(0,$AUTH.'S',$message);
        header("location: ?PHP_action=supl_ship_view&M_id=".$id);
    }
} else {
    $_SESSION['MSG'][] = 'Login error!';
    header("location: ?PHP_action=logout");
}
break;
#
#
#
#
#
#
# :supl_ship_revise
case "supl_ship_revise":
check_authority($AUTH,"add");

if( !empty($_POST['M_id']) ) {
   $id =  $_POST['M_id'];
    if( $PO_SUPL->supl_ship_revise($id) ) {
        $message = 'Success Revise! S'.$id;
        $log->log_add(0,$AUTH.'R',$message);
        header("location: ?PHP_action=supl_ship_view&M_id=".$id);
    }
} else {
    $_SESSION['MSG'][] = 'Login error!';
    header("location: ?PHP_action=logout");
}
break;
#
#
#
#
#
#
# :supl_ship_view
case "supl_ship_view":
check_authority($AUTH,"view");

$M_id = !empty($_GET['M_id'])? $_GET['M_id'] : (!empty($_SESSION[$AUTH][$PHP_action]['id'])? $_SESSION[$AUTH][$PHP_action]['id'] : '' );
$_SESSION[$AUTH][$PHP_action]['id'] = $M_id;

$op = $PO_SUPL->get_shipping($M_id);

#確認是否已經有驗入
$op['chk_incoming'] = $PO_SUPL->chk_incoming($M_id);

$op['select_from'] = $PO_SUPL->select_parameter(1,'M_org','','','','');
$op['select_to'] = $PO_SUPL->select_parameter(2,'M_dist','','','','');
$op['select_ship_way'] = $PO_SUPL->select_parameter(3,'M_ship_way','','','chk_way();','');
$op['select_express'] = $PO_SUPL->select_parameter(4,'M_express','','','','');
$op['status_txt'] = $PO_SUPL->get_status($op['po_supl_ship']['status']);
!empty($_GET['PHP_msg']) ? $op['msg'][] = $_GET['PHP_msg'] : '';

# date
$op['dates'] = get_yy_mm_dd();
$op['dates_eta'] = get_yy_mm_dd();

$op['css'] = array( 'css/scm.css' , 'css/po_ship.css' );
$op['js'] = array( 'js/jquery.min.js' , 'js/jquery.blockUI.js' , 'js/supl_ship.js' );

// print_r($op);
page_display($op,$AUTH,'supl_ship_view.html');
break;
#
#
#
#
#
#
# :supl_ship_edit
case "supl_ship_edit":
check_authority($AUTH,"edit");

$M_id = !empty($_POST['M_id'])? $_POST['M_id'] : (!empty($_SESSION[$AUTH][$PHP_action]['id'])? $_SESSION[$AUTH][$PHP_action]['id'] : '' );
$_SESSION[$AUTH][$PHP_action]['id'] = $M_id;

$op = $PO_SUPL->get_shipping($M_id);

$op['select_from'] = $PO_SUPL->select_parameter(1,'M_org',$op['po_supl_ship']['org'],'','','');
$op['select_to'] = $PO_SUPL->select_parameter(2,'M_dist',$op['po_supl_ship']['dist'],'','','');
$op['select_ship_way'] = $PO_SUPL->select_parameter(3,'M_ship_way',$op['po_supl_ship']['ship_way'],'','chk_way();','');
$op['select_express'] = $PO_SUPL->select_parameter(4,'M_express',$op['po_supl_ship']['ex_cmpy'],'','','');
$op['status_txt'] = $PO_SUPL->get_status($op['status']);
 
// 判斷是否自行輸入
if ( $PO_SUPL->in_parameter(1,$op['po_supl_ship']['org']) ) {
    $op['po_supl_ship']['org2'] = '';
} else {
    $op['po_supl_ship']['org2'] = $op['po_supl_ship']['org'];
    $op['po_supl_ship']['org'] = '';
}

# date
$op['dates'] = get_yy_mm_dd($op['po_supl_ship']['ship_date']);
$op['dates_eta'] = get_yy_mm_dd($op['po_supl_ship']['ship_eta_date']);

$op['css'] = array( 'css/scm.css' , 'css/po_ship.css' );
$op['js'] = array( 'js/jquery.min.js' , 'js/jquery.blockUI.js' , 'js/supl_ship.js' );

// print_r($op);
page_display($op,$AUTH,'supl_ship_edit.html');
break;
#
#
#
#
#
#
# :supl_ship_up
case "supl_ship_up":
check_authority($AUTH,"edit");

if(!empty($_POST['M_ship_id'])){

    $parm = array(
        'id'            => $_POST['M_ship_id'] ,
        'bl_num'        => $_POST['M_bl_num'] ,
        'carrier_num'   => $_POST['M_carrier_num'] ,
        'org'           => $_POST['M_org'] ,
        'dist'          => $_POST['M_dist'] ,
        'ship_way'      => $_POST['M_ship_way'] ,
        'ex_cmpy'       => $_POST['M_express'] ,
        'ship_date'     => $_POST['M_ship_date'] ,
        'ship_eta_date' => $_POST['M_ship_eta_date'] 
    );

    // echo 
    if( $sql = $PO_SUPL->supl_ship_up($parm) ){
        echo 'true,Success Update!';
        $_SESSION['MSG'][] = $message = 'Success Update! S'.$_POST['M_ship_id'];
        $log->log_add(0,$AUTH.'E',$message);
    } else {
        echo 'false,error!!';
    }
    
} else {
    $_SESSION['MSG'][] = 'Login error!';
    header("location: ?PHP_action=logout");
}

break;
#
#
#
#
#
#
# :supl_ship_det_edit
case "supl_ship_det_edit":
check_authority($AUTH,"edit");

$M_det_id = !empty($_POST['M_det_id'])? $_POST['M_det_id'] : (!empty($_SESSION[$AUTH][$PHP_action]['det_id'])? $_SESSION[$AUTH][$PHP_action]['det_id'] : '' );
$_SESSION[$AUTH][$PHP_action]['det_id'] = $M_det_id;

$op = $PO_SUPL->supl_ship_det_edit($M_det_id);
// print_r($op);

$op['select_from'] = $PO_SUPL->select_parameter(1,'M_org','','','','');
$op['select_to'] = $PO_SUPL->select_parameter(2,'M_dist','','','','');
$op['select_ship_way'] = $PO_SUPL->select_parameter(3,'M_ship_way','','','chk_way();','');
$op['select_express'] = $PO_SUPL->select_parameter(4,'M_express','','','','');
$op['status_txt'] = $PO_SUPL->get_status($op['status']);

# date
$op['dates'] = get_yy_mm_dd($op['po_supl_ship']['ship_date']);
$op['dates_eta'] = get_yy_mm_dd($op['po_supl_ship']['ship_eta_date']);

$op['css'] = array( 'css/scm.css' , 'css/po_ship.css' );
$op['js'] = array( 'js/jquery.min.js' , 'js/jquery.blockUI.js' , 'js/supl_ship.js' );

// print_r($op);
page_display($op,$AUTH,'supl_ship_det_edit.html');
break;
#
#
#
#
#
#
# :supl_ship_det_del
case "supl_ship_det_del":
check_authority($AUTH,"del");

# echo 
if( $PO_SUPL->supl_ship_det_del($_POST['M_ship_det_id']) ){
    $message = 'Success Delete!, P.O. # '.$_POST['M_po_num'];
    $_SESSION['MSG'][] = $message;
    $log->log_add(0,$AUTH.'D',$message);
    echo 'true,Success Delete!';
} else {
    echo 'false,Error!';
}

break;
#
#
#
#
#
#
# :supl_ship_link_add
case 'supl_ship_link_add':
check_authority($AUTH,"add");

$parm = array(
    'po_supl_ship_id'       => $_POST['M_ship_id'] ,
    'po_supl_ship_det_id'   => $_POST['M_ship_det_id'] ,
    'ap_id'                 => $_POST['M_ap_id'] ,
    'mat_cat'               => $_POST['M_mat_cat'] ,
    'mat_id'                => $_POST['M_mat_id'] ,
    'color'                 => urldecode(uniDecode($_POST['M_color'],'big-5')) ,
    'size'                  => urldecode(uniDecode($_POST['M_size'],'big-5')) ,
    'po_unit'               => urldecode(uniDecode($_POST['M_po_unit'],'big-5')) ,
    
    'c_no'                  => urldecode(uniDecode($_POST['M_c_no'],'big-5')) ,
    'l_no'                  => urldecode(uniDecode($_POST['M_l_no'],'big-5')) ,
    'r_no'                  => urldecode(uniDecode($_POST['M_r_no'],'big-5')) ,
    'qty'                   => $_POST['M_qty'] ,
    'nw'                    => $_POST['M_nw'] ,
    'gw'                    => $_POST['M_gw'] ,
    'c_o'                   => urldecode(uniDecode($_POST['M_c_o'],'big-5')) 
);

# echo 
if( $link_id = $PO_SUPL->supl_ship_link_add($parm) ){
    $message = 'Success Append!, link_id '.$link_id;
    $log->log_add(0,$AUTH.'A',$message);
    echo 'true,Success Append!,'.$link_id;
} else {
    echo 'false,Error!';
}

break;
#
#
#
#
#
#
# :supl_ship_link_up
case 'supl_ship_link_up':
check_authority($AUTH,"edit");

$parm = array(
    'id'        => $_POST['M_link_id'] ,
    'c_no'      => urldecode(uniDecode($_POST['M_c_no'],'big-5')) ,
    'l_no'      => urldecode(uniDecode($_POST['M_l_no'],'big-5')) ,
    'r_no'      => urldecode(uniDecode($_POST['M_r_no'],'big-5')) ,
    'qty'       => $_POST['M_qty'] ,
    'nw'        => $_POST['M_nw'] ,
    'gw'        => $_POST['M_gw'] ,
    'c_o'       => urldecode(uniDecode($_POST['M_c_o'],'big-5')) 
);

# echo 
if( $PO_SUPL->supl_ship_link_up($parm) ){
    $message = 'Success Update!, link_id '.$_POST['M_link_id'];
    $log->log_add(0,$AUTH.'A',$message);
    echo 'true,Success Update!';
} else {
    echo 'false,Error!';
}

break;
#
#
#
#
#
#
# :supl_ship_det_up
case 'supl_ship_det_up':
check_authority($AUTH,"edit");

$parm = array(
    'po_supl_ship_id'       => $_POST['M_po_ship_id'] ,
    'po_supl_ship_det_id'   => $_POST['M_ship_det_id'] ,
    'po_num'                => $_POST['M_po_num'] ,
    'invoice_num'           => $_POST['M_invoice_num'] ,
    'pi_num'                => $_POST['M_pi_num'] 
);

# echo 
if( $pp = $PO_SUPL->supl_ship_det_up($parm) ){
    $message = 'Success Update!, ship_id S'.$_POST['M_po_ship_id'];
    $log->log_add(0,$AUTH.'E',$message);
    echo 'true,Success Append!';
} else {
    echo 'false,Error!';
}

break;
#
#
#
#
#
#
# :supl_ship_link_del
case 'supl_ship_link_del':
check_authority($AUTH,"del");

# echo 
if( $PO_SUPL->supl_ship_link_del($_POST['M_link_id']) ){
    $message = 'Success Delete!, link_id '.$_POST['M_link_id'];
    $log->log_add(0,$AUTH.'D',$message);
    echo 'true,Success Delete!';
} else {
    echo 'false,Error!';
}

break;
#
#
#
#
#
#
# :reply_arrival
case "reply_arrival":
check_authority($AUTH,"edit");

if( !empty($_GET['ship_id']) ) {
	if ( $Str = $WAREHOUSING->reply_arrival( $_GET['ship_id'] ) ) {
		$message = "Successfully Reply Arrival, carrier number : ". $_GET['carrier_num'];
		$log->log_add(0,$AUTH."E",$message);
		echo 'ok@'.$message;
	} else {
		echo 'off@nodata';
	}
}

break;
#
#
#
#
#
#
case "import_csv_file":
	check_authority($AUTH,"add");
	
	$ext = explode(".", $_FILES['PHP_csv_file']['name']);
	$ext_size = sizeof($ext);
	if($ext[$ext_size-1] <> "csv"){
		echo "<script>alert(' 匯入的檔案必須是 csv檔 ');history.go(-1);</script>";
	}
	
	$csvtext = array();
	$str_val = array();
	$po_ary = array();
	$csvtext = file($_FILES['PHP_csv_file']['tmp_name']);
	
    $csvtext_size = sizeof($csvtext);
    for($i=1;$i<$csvtext_size;$i++){
        $chk_val = explode(",", $csvtext[$i]);
        if(empty($chk_val[0])){
            echo "<script>alert(' NO:".$i." 請填入PO號碼!');history.go(-1);</script>";
            exit;
        }

        if(empty($chk_val[2])){
            echo "<script>alert(' NO:".$i." 請填入SCM物料號碼! [ A00-000-001 F00-000-001]');history.go(-1);</script>";
            exit;
        }
        if(empty($chk_val[5])){
            echo "<script>alert(' NO:".$i." 請填入物料單位!');history.go(-1);</script>";
            exit;
        }
    }
	
	$ship_id = $PO_SUPL->import_supl_main();
	if($ship_id){
		$csvtext_size = sizeof($csvtext);
		for($i=1;$i<$csvtext_size;$i++){
			$str_val = explode(",", $csvtext[$i]);
			$po_num = trim($str_val[0]);
			$invoice_num = trim($str_val[1]);
			$mat_code = trim(strtoupper($str_val[2]));
			$mat_cat = strtoupper(substr($mat_code,0,1)) == "A" ? "a" : "l";
			$color = trim($str_val[3]);
			$size = trim($str_val[4]);
			$unit = strtolower(trim($str_val[5]));
			$unit = $unit == "k pcs" ? "K pcs" : $unit;
			$c_no = trim($str_val[6]);
			$r_no = trim($str_val[7]);
			$l_no = trim($str_val[8]);
			$qty = trim($str_val[9]);
			$n_w = trim($str_val[10]);
			$g_w = trim($str_val[11]);
			$c_o = trim($str_val[12]);
			
			if(!empty($po_num)){
				if(!$ship_det_id = array_search($po_num, $po_ary)){
					$det_parm = array(
								"po_supl_ship_id"	=>	$ship_id,
								"po_num"			=>	$po_num,
								"invoice_num"		=>	$invoice_num
							);
					$ship_det_id = $PO_SUPL->import_supl_det($det_parm);
					$po_ary[$ship_det_id] = $po_num;
				}
				
				$link_parm = array(
								"po_supl_ship_id"		=>	$ship_id,
								"po_supl_ship_det_id"	=>	$ship_det_id,
								"ap_id"					=>	$PO_SUPL->get_field("id", "ap", "ap_num='".str_replace("O","A",$po_num)."'"),
								"mat_cat"				=>	$mat_cat,
								"mat_id"				=>	$mat_cat == "a"? $PO_SUPL->get_field("id", "acc", "acc_code='".$mat_code."'") : $PO_SUPL->get_field("id", "lots", "lots_code='".$mat_code."'"),
								"color"					=>	$color,
								"size"					=>	$size,
								"qty"					=>	$qty,
								"po_unit"				=>	$unit,
								"c_no"					=>	$c_no,
								"r_no"					=>	$r_no,
								"l_no"					=>	$l_no,
								"gw"					=>	$g_w,
								"nw"					=>	$n_w,
								"c_o"					=>	$c_o
						);
				
				$PO_SUPL->import_supl_link($link_parm);
			}
		}
	}
	
	$msg = "Successfully append";
	redirect_page($PHP_SELF."?PHP_action=supl_ship_view&M_id=".$ship_id."&PHP_msg=".$msg);
			
break;
#
#
#
#
#
#
case "del_ship":
	check_authority($AUTH,"add");
	
	echo $PO_SUPL->del_ship($_GET['id']);
	
			
break;
#
#
#
#
#
case "export_po_csv":
	check_authority($AUTH,"add");
	
	$op['po_row'] = $PO_SUPL->get_po4ship($_GET['PHP_po_num']);
	
	require_once($config['root_dir']."/lib/spreadsheets/Worksheet.php");
    require_once($config['root_dir']."/lib/spreadsheets/Workbook.php");

    function HeaderingExcel($filename) {
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=$filename");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
        header("Pragma: public");
    }

    // HTTP headers
    HeaderingExcel($_GET['PHP_po_num'].'.xls');
 
    // Creating a workbook
    $workbook = new Workbook("-");

    // Creating the first worksheet
    $worksheet1 =& $workbook->add_worksheet('ship');
	
    $ary_size = array(10,10,12,18,5,5,5,5,5,8,10,10,10);
    
    for ($i=0; $i<sizeof($ary_size); $i++){
        $worksheet1->set_column(0,$i,$ary_size[$i]);	  
    } 
  
	$ary = array("PO#","invoice","物料號碼","color","size","unit","箱號","捲號","缸號","qty","n/w","g/w","Made in");

    for ($i=0; $i<sizeof($ary); $i++){
         $worksheet1->write_string(0,$i,$ary[$i],$formatot);
    }	
	
	$po_row_size = sizeof($op['po_row']);
	for($i=0;$i<$po_row_size;$i++){
		$worksheet1->write($i+1,0,$_GET['PHP_po_num']);
		$worksheet1->write($i+1,2,$op['po_row'][$i]['mat_code']);
		$worksheet1->write($i+1,3,$op['po_row'][$i]['color']);
		$worksheet1->write($i+1,4,$op['po_row'][$i]['size']);
		$worksheet1->write($i+1,5,$op['po_row'][$i]['po_unit']);
		$worksheet1->write($i+1,9,$op['po_row'][$i]['po_qty']);
	}
    
	
    $workbook->close();
	
break;
#
#
#
#
#
#
case "import_csv_bl":
	check_authority($AUTH,"add");
	//print_r($_FILES['PHP_csv_file']['name']);
	$ext = explode(".", $_FILES['PHP_csv_file']['name']);
	$ext_size = sizeof($ext);
	if($ext[$ext_size-1] <> "csv"){
		echo "<script>alert(' 匯入的檔案必須是csv檔，請另存新檔為csv檔 ');history.go(-1);</script>";
	}
	
	$csvtext = array();
	$str_val = array();
	$po_ary = array();
	$csvtext = file($_FILES['PHP_csv_file']['tmp_name']);
	
	$csvtext_size = sizeof($csvtext);
	print_r($csvtext);
    for($i=1;$i<$csvtext_size;$i++){
        $chk_val = explode(",", $csvtext[$i]);
		
		if(empty($chk_val[9])){
			if(!empty($chk_val[0]))
			{
				echo "<script>alert(' 第 ".$i." 筆資料無 數量 !');history.go(-1);</script>";
				exit;
			}
        }
	}
	
	$ship_id = $_POST['PHP_ship_id'];
	if($ship_id){
		$csvtext_size = sizeof($csvtext);
		for($i=1;$i<$csvtext_size;$i++){
			$str_val = explode(",", $csvtext[$i]);
			$po_num = trim($str_val[0]);
			$invoice_num = trim($str_val[1]);
			$mat_code = trim(strtoupper($str_val[2]));
			$mat_cat = strtoupper(substr($mat_code,0,1)) == "A" ? "a" : "l";
			$color = trim($str_val[3]);
			$size = trim($str_val[4]);
			$unit = strtolower(trim($str_val[5]));
			$unit = $unit == "k pcs" ? "K pcs" : $unit;
			$c_no = trim($str_val[6]);
			$r_no = trim($str_val[7]);
			$l_no = trim($str_val[8]);
			$qty = trim($str_val[9]);
			$n_w = trim($str_val[10]);
			$g_w = trim($str_val[11]);
			$c_o = trim($str_val[12]);
			
			if(!empty($po_num)){
				if(!$ship_det_id = array_search($po_num, $po_ary)){
					$det_parm = array(
								"po_supl_ship_id"	=>	$ship_id,
								"po_num"			=>	$po_num,
								"invoice_num"		=>	$invoice_num
							);
					$ship_det_id = $PO_SUPL->import_supl_det($det_parm);
					$po_ary[$ship_det_id] = $po_num;
				}
				
				$link_parm = array(
								"po_supl_ship_id"		=>	$ship_id,
								"po_supl_ship_det_id"	=>	$ship_det_id,
								"ap_id"					=>	$PO_SUPL->get_field("id", "ap", "ap_num='".str_replace("O","A",$po_num)."'"),
								"mat_cat"				=>	$mat_cat,
								"mat_id"				=>	$mat_cat == "a"? $PO_SUPL->get_field("id", "acc", "acc_code='".$mat_code."'") : $PO_SUPL->get_field("id", "lots", "lots_code='".$mat_code."'"),
								"color"					=>	$color,
								"size"					=>	$size,
								"qty"					=>	$qty,
								"po_unit"				=>	$unit,
								"c_no"					=>	$c_no,
								"r_no"					=>	$r_no,
								"l_no"					=>	$l_no,
								"gw"					=>	$g_w,
								"nw"					=>	$n_w,
								"c_o"					=>	$c_o
						);
				
				$PO_SUPL->import_supl_link($link_parm);
			}
		}
		$msg = "Successfully import!";
	}
	
	redirect_page($PHP_SELF."?PHP_action=supl_ship_view&M_id=".$ship_id."&PHP_msg=".$msg);
	
break;
#
#
#
#
#
case "ship_del_po":
	check_authority($AUTH,"del");
	
	$rtn_flag = $PO_SUPL->del_ship_po($_GET['PHP_ship_det_id']);
	redirect_page($PHP_SELF."?PHP_action=supl_ship_view&M_id=".$_GET['PHP_ship_id']);
break;
#
#
#
#
#
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	case "get_ship_det":
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# new bom status 的 ship qty 明細用到此case
case "get_ship_det":
	check_authority($AUTH,"view");
	
	$op['ship'] = $PO_SUPL->get_by_po($_GET['PHP_bom_id'], $_GET['PHP_mat'], $_GET['PHP_mat_id'], $_GET['PHP_color'], $_GET['PHP_size']);
	

	page_display($op, '039', $TPL_PO_SHIPD_SHOW);			    	    
break;
#
#
#
#
#
} # CASE END
?>