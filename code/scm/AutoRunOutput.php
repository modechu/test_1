<?php

$csvtext = array();
$str_val = array();
$po_ary = array();
$csvtext = file($_FILES['PHP_csv_file']['tmp_name']);

$csvtext_size = sizeof($csvtext);
for($i=1;$i<$csvtext_size;$i++){
    $chk_val = explode(",", $csvtext[$i]);
    if(empty($chk_val[0])){
        echo "<script>alert(' NO:".$i." 請填入 PO 號碼!');history.go(-1);</script>";
        exit;
    }

    if(empty($chk_val[2])){
        echo "<script>alert(' NO:".$i." 請填入 SCM 物料號碼! [ A00-000-001 F00-000-001]');history.go(-1);</script>";
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
        $unit = trim($str_val[5]);
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

system("C:/print.vbs", $returnvalue);
echo $returnvalue;

break;
	
?>