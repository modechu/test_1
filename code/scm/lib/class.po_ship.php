<?php 

#++++++++++++++++++++++++++++++++++ ORDER  class ##### 訂 單  ++++++++++++++++++++++++++++++++++++++++
#	->init($sql)		啟始 (使用 Msg_handle(); 先聯上 sql)
#	->bom_search($supl,$cat)	查詢BOM的主副料


#
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

class PO_SHIP {
		
	var $sql;
	var $msg ;
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->init($sql)	啟始(使用 Msg_handle() ; 先聯上 sql)
#		必需聯上 sql 才可  啟始
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function init($sql) {
		$this->msg = new MSG_HANDLE();

		if (!$sql) {
			$this->msg->add("Error ! Data base can't connect.");
		    return false;
		}
		$this->sql = $sql;
		return true;
	} // end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新 訂單記錄
#						傳回 $id
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	function add_main() {					
		$sql = $this->sql;
		$today = date('Ymd');
		$q_str = "SELECT num FROM po_ship where num like '".$today."%' order by id desc limit 1";

		if (!$q_result = $sql->query($q_str)) {		//搜尋最後一筆
			$this->msg->add("Error! 無法存取資料庫!");
			$this->msg->merge($sql->msg);
			return false;    
		}
		if (!$row = $sql->fetch($q_result)) {	//如果沒有資料的話
			$buy_no = '1';
		
		}else{	//將最後一筆的數字+1
			$tmp = explode('-',$row['num']);
			
			$buy_no = number_format($tmp[1])+1;
		}
		$num = $today.'-'.$buy_no;
		
					# 加入資料庫(2007.03.02加入尺吋資料)
		$q_str = "INSERT INTO po_ship (ship_inv,num,shipper) 
				  VALUES('".$num."','".$num."','".$GLOBALS['SCACHE']['ADMIN']['login_id']."')";
		
		if (!$q_result = $sql->query($q_str)) {
			$this->msg->add("Error ! cannot append order");
			$this->msg->merge($sql->msg);
			return false;    
		}

		$ord_id = $sql->insert_id();  //取出 新的 id

		return $ord_id;

	} // end func
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->add($parm)		加入新 訂單記錄
#						傳回 $id
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function add_det($parm1) {					
$sql = $this->sql;

# 加入資料庫(2007.03.02加入尺吋資料)
$q_str = "INSERT INTO `po_ship_det` (ship_id,inv_num,po_id,ap_num,mat_cat,mat_id,color,size,special,qty) 
VALUES('".
$parm1['ship_id']."','".
$parm1['inv_num']."','".
$parm1['po_id']."','".
$parm1['ap_num']."','".
$parm1['mat_cat']."','".
$parm1['mat_id']."','".
$parm1['color']."','".
$parm1['size']."','".
$parm1['special']."','".																																
$parm1['qty']."')";

if (!$q_result = $sql->query($q_str)) {
    $this->msg->add("Error ! cannot append order");
    $this->msg->merge($sql->msg);
    return false;    
}

$ord_id = $sql->insert_id();  //取出 新的 id

return $ord_id;

} // end func	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $order_num=0)	抓出指定訂單記錄資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get( $id='' , $inv_num='') {
    $sql = $this->sql;
    
    $ttl_qty = 0;
//Ship主檔
    if($id)
        $q_str = "SELECT * FROM po_ship WHERE id= '$id'";
    else
        $q_str = "SELECT * FROM po_ship WHERE ship_inv = '$inv_num'";

    if (!$q_result = $sql->query($q_str)) {
        $this->msg->add("Error ! Database can't access!");
        $this->msg->merge($sql->msg);
        return false;    
    }
    if (!$row = $sql->fetch($q_result)) {
        $this->msg->add("Error ! Can't find this record!");
        return false;    
    }
    $op['po_ship']=$row;

    //改變Login帳號為名字
    $po_user=$GLOBALS['user']->get(0,$op['po_ship']['shipper']);
    $op['po_ship']['shipper_id'] = $op['po_ship']['shipper'];
    if ($po_user['name'])$op['po_ship']['shipper'] = $po_user['name'];
    $op['po_ship']['carrier_link'] = $this->get_carrier_link($op['po_ship']['carrier'],$op['po_ship']['ex_cmpy']);

    //明細
    $i=$m=0; 		
    $op['ship'] = array();
    $q_str="SELECT po_ship_det.*
                    FROM  po_ship_det
                    WHERE po_ship_det.special = 0 AND									
                          po_ship_det.ship_id='".$op['po_ship']['id']."'								  					  
                    ORDER BY po_ship_det.id";

    if (!$q_result = $sql->query($q_str)) {
        $this->msg->add("Error ! Database can't access!");
        $this->msg->merge($sql->msg);
        return false;    
    }

    $rcv_rmk = 0;//用來判斷是否已驗收
    while ($row1 = $sql->fetch($q_result)) {
        $row1['special'] = 0;
        $op['ship'][$m]=$row1;
        $ttl_qty += $row1['qty'];
        
        # 用來判斷是否超過 [ap]表格 po_qty $ap_num,$mat_cat,$mat_id,$color,$size
        $op['ship'][$m]['ship_qty'] = $this->get_ship_qty($op['ship'][$m]['ap_num'],$op['ship'][$m]['mat_cat'],$op['ship'][$m]['mat_id'],$op['ship'][$m]['color'],$op['ship'][$m]['size'],$row1['special']);
        
        # 判斷是否已驗收，已驗收的話，就不顯示del圖示
        $row1['special'] == 1 ? $tbl = "ap_special" : $tbl = "ap_det";
        // $ap_num = $GLOBALS['po']->get_det_field("ap_num",$tbl,"po_spare='".$row1['po_id']."' limit 1");
        $q_str = "SELECT receive_det.id FROM receive_det 
                    WHERE receive_det.po_id='".$row1['po_id']."' and receive_det.ap_num='".$op['ship'][$m]['ap_num']."' and receive_det.ship_det_id='".$row1['id']."'";

        $q_res = $sql->query($q_str);
        $rcv_row = $sql->fetch($q_res);
        if($rcv_row) $op['ship'][$m]['rcv_rmk'] = $rcv_rmk = 1;
        
        $m++;
    }
    // if($GLOBALS['SCACHE']['ADMIN']['login_id'] == 'morial')
    // {
        //echo $q_str."<br>";
        //print_r($op['ship']);
    // }
    for($i=0; $i<sizeof($op['ship']); $i++)
    {
        $q_str="SELECT ap.id,ap_det.wi_id, ap_det.unit, sum(ap_det.po_qty) as po_qty, ap_det.prics, ap_det.prc_unit, ap_det.color, ap_det.size, 
                                    ap_det.mat_cat, ap_det.mat_id, ap.currency, ap_det.po_unit, ap.ap_num, ap.po_num, ap.sup_code, ap.toler
                        FROM ap_det, ap
                        WHERE ap.ap_num = ap_det.ap_num AND 
                              `ap_det`.`ap_num` = '".$op['ship'][$i]['ap_num']."' AND 
                              `ap_det`.`mat_cat` = '".$op['ship'][$i]['mat_cat']."' AND 
                              `ap_det`.`mat_id` = '".$op['ship'][$i]['mat_id']."' AND 
                              `ap_det`.`color` = '".$op['ship'][$i]['color']."' AND 
                              `ap_det`.`size` = '".$op['ship'][$i]['size']."'						  
                      GROUP BY ap_det.ap_num ";
        // echo $q_str.'<br>';
        if (!$q_result = $sql->query($q_str)) {
            $this->msg->add("Error ! Database can't access!");
            $this->msg->merge($sql->msg);
            return false;    
        }		
        while ($row1 = $sql->fetch($q_result)) {			
            $op['ship'][$i]['unit']=$row1['unit'];
            $op['ship'][$i]['po_qty']=$row1['po_qty'];
            $op['ship'][$i]['percentage'] = number_format(($op['ship'][$i]['ship_qty'] - $row1['po_qty']) / $row1['po_qty'],3,'','') * 100;
            $op['ship'][$i]['prics']=$row1['prics'];
            $op['ship'][$i]['prc_unit']=$row1['prc_unit'];
            $op['ship'][$i]['wi_id']=$row1['wi_id'];
            $op['ship'][$i]['currency']=$row1['currency'];
            $op['ship'][$i]['po_unit']=$row1['po_unit'];
            $op['ship'][$i]['po_num']=$row1['po_num'];
            $op['ship'][$i]['ap_id']=$row1['id'];
            $op['ship'][$i]['sup_code']=$row1['sup_code'];
            $op['ship'][$i]['toler']=$row1['toler'];
            $tmp_toler = explode('|',$op['ship'][$i]['toler']);
            $op['ship'][$i]['toleri']=$tmp_toler[0];
            $op['ship'][$i]['tolern']=$tmp_toler[1];
            $op['ship'][$i]['toleri_qty']=$op['ship'][$i]['po_qty'] * (1+$tmp_toler[0]/100);
            $op['ship'][$i]['tolern_qty']=$op['ship'][$i]['po_qty'] * (1-$tmp_toler[1]/100);
            $op['ship'][$i]['balance'] = number_format($row1['po_qty'] - $op['ship'][$i]['ship_qty'],2,'','');
        }			
    }
    
    for($i=0; $i<sizeof($op['ship']); $i++)
    {
        $op['ship'][$i]['ord_num'] = array();
        $k=0;
        if($op['ship'][$i]['mat_cat'] == 'l')
        {
            $q_str = "SELECT bom_lots.color, lots_use.smpl_code, lots_use.lots_code, lots_use.lots_name, 
                                             lots.comp as con1, lots.width as con2
                                FROM	ap_det, bom_lots, lots_use, lots
                                WHERE ap_det.bom_id = bom_lots.id AND bom_lots.lots_used_id = lots_use.id AND
                                            lots_use.lots_code = lots.lots_code AND
                                            ap_det.mat_cat = 'l' AND 

                                  `ap_det`.`ap_num` = '".$op['ship'][$i]['ap_num']."' AND 
                                  `ap_det`.`mat_id` = '".$op['ship'][$i]['mat_id']."' AND 
                                  `ap_det`.`color` = '".$op['ship'][$i]['color']."' AND 
                                  `ap_det`.`size` = '".$op['ship'][$i]['size']."'					
                                            
                                GROUP BY lots_use.smpl_code";
            // echo $q_str.'<br>';
                                
            $q_result = $sql->query($q_str);															
            while ($row1 = $sql->fetch($q_result)) {
                $mk = 0;					
                $op['ship'][$i]['mat_code']=$row1['lots_code'];
                $op['ship'][$i]['mat_name']=$row1['lots_name'];
                $op['ship'][$i]['color']=$row1['color'];
                $op['ship'][$i]['con1']=$row1['con1'];
                $op['ship'][$i]['con2']=$row1['con2'];
                $op['ship'][$i]['ord_num'][]=$row1['smpl_code'];
                $op['ship'][$i]['k'][]=$k;
                $k++;
            }
        }else{
            $q_str = "SELECT bom_acc.color, acc_use.smpl_code, acc_use.acc_code, acc_use.acc_name, 
                                             acc_use.acc_cat , acc.specify as con1, acc.des as con2
                                FROM	ap_det, bom_acc, acc_use,acc
                                WHERE ap_det.bom_id = bom_acc.id AND bom_acc.acc_used_id = acc_use.id AND
                                            acc_use.acc_code = acc.acc_code AND
                                            ap_det.mat_cat = 'a' AND 
                                  `ap_det`.`ap_num` = '".$op['ship'][$i]['ap_num']."' AND 
                                  `ap_det`.`mat_id` = '".$op['ship'][$i]['mat_id']."' AND 
                                  `ap_det`.`color` = '".$op['ship'][$i]['color']."' AND 
                                  `ap_det`.`size` = '".$op['ship'][$i]['size']."'		
                                GROUP BY acc_use.smpl_code";

            $q_result = $sql->query($q_str);															
            while ($row1 = $sql->fetch($q_result)) {
                $mk = 0;
                if($row1['con1'] == $row1['con2'])$row1['con2'] = '';
                $op['ship'][$i]['mat_code']=$row1['acc_code'];
                $op['ship'][$i]['mat_name']=$row1['acc_name'];
                $op['ship'][$i]['acc_cat'][]=$row1['acc_cat'];
                $op['ship'][$i]['color']=$row1['color'];
                $op['ship'][$i]['con1']=$row1['con1'];
                $op['ship'][$i]['con2']=$row1['con2'];
                $op['ship'][$i]['ord_num'][]=$row1['smpl_code'];
                
                $op['ship'][$i]['k'][]=$k;
                $k++;
            }			
        }
    }

/*
//特殊採購 主料		
    $q_str="SELECT po_ship_det.*, ap_special.id as s_id, ap_special.unit, ap_special.po_qty, ap_special.prics, 
    ap_special.prc_unit, ap_special.mat_cat, ap.currency, ap_special.mat_code,
    ap_special.color, ap_special.ord_num, lots.lots_name as mat_name, ap_special.po_unit,
    ap.po_num, ap.ap_num, ap.sup_code, ap.toler
    FROM ap_special, ap, po_ship_det, lots 
    WHERE ap.ap_num = ap_special.ap_num AND 
    po_ship_det.po_id = ap_special.id AND 
    ap_special.mat_code = lots.lots_code AND 
    po_ship_det.special = 1 AND
    ap_special.mat_cat = 'l' AND 
    po_ship_det.ship_id='".$op['po_ship']['id']."'  
    ORDER BY po_ship_det.id";
    echo $q_str.'<br>';
    if (!$q_result = $sql->query($q_str)) {
        $this->msg->add("Error ! Database can't access!");
        $this->msg->merge($sql->msg);
        return false;    
    }
    if(sizeof($op['ship']) > 0)$i = sizeof($op['ship']);
    while ($row1 = $sql->fetch($q_result)) {
        $op['ship'][$i]=$row1;
        $op['ship'][$i]['toler']=$row1['toler'];
        $tmp_toler = explode('|',$op['ship'][$i]['toler']);
        $op['ship'][$i]['toleri']=$tmp_toler[0];
        $op['ship'][$i]['tolern']=$tmp_toler[1];
        $op['ship'][$i]['toleri_qty']=$op['ship'][$i]['po_qty'] * (1+$tmp_toler[0]/100);
        $op['ship'][$i]['tolern_qty']=$op['ship'][$i]['po_qty'] * (1-$tmp_toler[1]/100);
        $op['ship'][$i]['sup_code'] =  $row1['sup_code'];
        $op['ship'][$i]['ord_num'] = array();
        $op['ship'][$i]['ord_num'][0] = $row1['ord_num'];
        $op['ship'][$i]['k'][0] = 0;
        $ttl_qty += $row1['qty'];
        
        $op['ship'][$i]['ship_qty'] = $this->get_ship_qty($op['ship'][$m]['ap_num'],$op['ship'][$m]['mat_cat'],$op['ship'][$m]['mat_id'],$op['ship'][$m]['color'],$op['ship'][$m]['size'],1);
        $op['ship'][$i]['percentage'] = number_format(($op['ship'][$i]['ship_qty'] - $row1['po_qty']) / $row1['po_qty'],3,'','') * 100;
        
        $op['ship'][$i]['balance'] = $row1['po_qty'] - $op['ship'][$i]['ship_qty'];
        
        $i++;
    }

//特殊採購 副料			
    $q_str="SELECT po_ship_det.*, ap_special.id as s_id, ap_special.unit, ap_special.po_qty, ap_special.prics, 
                                ap_special.prc_unit, ap_special.mat_cat, ap.currency, ap_special.mat_code,
                                ap_special.color, ap_special.ord_num, acc.acc_name as mat_name, ap_special.po_unit,
                                ap.po_num, ap.ap_num, ap.sup_code, ap.toler
                    FROM ap_special, ap, po_ship_det, acc
                    WHERE ap.ap_num = ap_special.ap_num AND po_ship_det.po_id = ap_special.id AND 
                                ap_special.mat_code = acc.acc_code AND po_ship_det.special = 1 AND
                              ap_special.mat_cat = 'a' AND po_ship_det.ship_id='".$op['po_ship']['id']."'  ORDER BY po_ship_det.id";
echo $q_str.'<br>';
    if (!$q_result = $sql->query($q_str)) {
        $this->msg->add("Error ! Database can't access!");
        $this->msg->merge($sql->msg);
        return false;    
    }
    if(sizeof($op['ship']) > 0)$i = sizeof($op['ship']);
    
    while ($row1 = $sql->fetch($q_result)) {
        $op['ship'][$i]=$row1;
        $op['ship'][$i]['toler']=$row1['toler'];
        $tmp_toler = explode('|',$op['ship'][$i]['toler']);
        $op['ship'][$i]['toleri']=$tmp_toler[0];
        $op['ship'][$i]['tolern']=$tmp_toler[1];
        $op['ship'][$i]['toleri_qty']=$op['ship'][$i]['po_qty'] * (1+$tmp_toler[0]/100);
        $op['ship'][$i]['tolern_qty']=$op['ship'][$i]['po_qty'] * (1-$tmp_toler[1]/100);
        $op['ship'][$i]['sup_code'] =  $row1['sup_code'];
        $op['ship'][$i]['ord_num'] = array();
        $op['ship'][$i]['ord_num'][0] = $row1['ord_num'];
        $op['ship'][$i]['k'][0] = 0;
        $ttl_qty += $row1['qty'];
        
        $op['ship'][$i]['ship_qty'] = $this->get_ship_qty($op['ship'][$i]['po_id'],1);
        $op['ship'][$i]['percentage'] = number_format(($op['ship'][$i]['ship_qty'] - $row1['po_qty']) / $row1['po_qty'],3,'','') * 100;
        
        $op['ship'][$i]['balance'] = $row1['po_qty'] - $op['ship'][$i]['ship_qty'];
        
        $i++;
    }
*/
    
    $q_str="SELECT *
                    FROM  po_ship_file
                    WHERE po_ship_file.ship_id='".$op['po_ship']['id']."' ORDER BY id";
    // echo $q_str.'<br>';
    if (!$q_result = $sql->query($q_str)) {
        $this->msg->add("Error ! Database can't access!");
        $this->msg->merge($sql->msg);
        return false;    
    }
    
    $i=0;
    while ($row1 = $sql->fetch($q_result)) {
        $op['done'][$i]=$row1;		
        $i++;
    }		
    
    $op['ttl_qty'] = $ttl_qty;
    
    return $op;
} // end func
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->emat_del($id)		刪除一般請購明細
#			mode=0 : EDIT    mode=1 : REVISE
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function del_det($id,$special) {

$sql = $this->sql;

$q_str="SELECT `ap_num`,`mat_cat`,`mat_id`,`color`,`size` FROM `po_ship_det` WHERE `id` = '".$id."' ;";
if (!$q_result = $sql->query($q_str)) {
    $this->msg->add("Error ! Database can't access!");
    $this->msg->merge($sql->msg);
    return false;    
}
$row = $sql->fetch($q_result);  
$ap_num = $row['ap_num'];
$mat_cat = $row['mat_cat'];
$mat_id = $row['mat_id'];
$color = $row['color'];
$size = $row['size'];

$q_str="DELETE FROM `po_ship_det` WHERE `id` = '".$id."'";
if (!$q_result = $sql->query($q_str)) {
    $this->msg->add("Error !  Database can't update.");
    $this->msg->merge($sql->msg);
    return false;    
}     

#修改 po 明細的 ship 相關記錄
$q_str="SELECT `po_ship`.`ship_way` , `po_ship`.`ship_date` , `po_ship`.`ship_eta`
        FROM `po_ship` , `po_ship_det`
        WHERE 
        `po_ship_det`.`ap_num` = '".$ap_num."' AND 
        `po_ship_det`.`mat_cat` = '".$mat_cat."' AND 
        `po_ship_det`.`mat_id` = '".$mat_id."' AND 
        `po_ship_det`.`color` = '".$color."' AND 
        `po_ship_det`.`size` = '".$size."' AND 
        `po_ship_det`.`ship_id` = `po_ship`.`id` 
        ORDER BY `po_ship_det`.`ship_id` DESC LIMIT 1";

$q_result = $sql->query($q_str);
$ship_row = $sql->fetch($q_result);

$special == 1 ? $tbl = "ap_special" : $tbl = "ap_det";
$whare = " `ap_num` = '".$ap_num."' AND `mat_cat` = '".$mat_cat."' AND `mat_id` = '".$mat_id."' AND `color` = '".$color."' AND `size` = '".$size." ";
$f1 = $this->update_field("ship_date",$ship_row['ship_date'],$whare,$tbl);
$f1 = $this->update_field("ship_way",$ship_row['ship_way'],$whare,$tbl);
$f1 = $this->update_field("ship_eta",$ship_row['ship_date'],$whare,$tbl);
	
return true;

}// end func
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit_main($parm)		更新 ship 記錄 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function edit_main($parm) {

$sql = $this->sql;

$q_str = "UPDATE po_ship SET ".
"ship_inv='"	    .$parm['ship_inv'].
"', org ='"		    .$parm['org'].
"',	dist ='"		.$parm['dist'].
"',	ship_way ='"	.$parm['ship_way'].
"',	ex_cmpy ='"	    .$parm['ex_cmpy'].
"',	ship_date ='"   .$parm['ship_date'].		
"',	ship_eta ='"	.$parm['ship_eta'].
"',	carrier ='"	    .$parm['carrier'].
"',	ver ='"		    .$parm['ver'].
"',	status ='"	    .$parm['status'].
"',	tw_rcv ='"	    .$parm['tw_rcv'].
"'  WHERE id ='"    .$parm['id']."'";
// echo $q_str.'<br>';
if (!$q_result = $sql->query($q_str)) {
    $this->msg->add("Error !  Database can't update.");
    $this->msg->merge($sql->msg);
    return false;    
}

return true;

} // end func	
	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit_det($parm)		更新 po_ship_det 記錄 
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function edit_det($parm) {

    $sql = $this->sql;		
    
    $q_str = "UPDATE po_ship_det SET ".
                        " inv_num='".$parm['inv_num'].
                        "', qty='"	.$parm['qty'].
                        "'  WHERE id='"		.$parm['id']."'";

    if (!$q_result = $sql->query($q_str)) {
        $this->msg->add("Error !  Database can't update.");
        $this->msg->merge($sql->msg);
        return false;    
    }

    if($parm['special'] == 0)
    {
        $q_str = "UPDATE ap_det SET ".
                            "   ship_way='"					.$parm['ship_way'].	
                            "',	ship_date='"				.$parm['ship_date'].	
                            "',	ship_eta='"					.$parm['ship_eta'].
                            "'  WHERE po_spare='"		.$parm['po_id']."'";
        if($GLOBALS['SCACHE']['ADMIN']['login_id'] == 'morial')
        {
            //print_r($op);
            //exit;
            //echo $q_str;
            //echo "<br>";
            //exit;
        }
        if (!$q_result = $sql->query($q_str)) {
            $this->msg->add("Error !  Database can't update.");
            $this->msg->merge($sql->msg);
            return false;    
        }	   
    }else{
        $q_str = "UPDATE ap_special SET ".
                            "   ship_way='"					.$parm['ship_way'].	
                            "',	ship_date='"				.$parm['ship_date'].	
                            "',	ship_eta='"					.$parm['ship_eta'].
                            "'  WHERE id='"		.$parm['po_id']."'";
        

        if (!$q_result = $sql->query($q_str)) {
            $this->msg->add("Error !  Database can't update.");
            $this->msg->merge($sql->msg);
            return false;    
        }		
    }

    return true;
} // end func	

	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->search($mode=0, $dept='',$limit_entries=0) 搜尋 訂 單 資料
#					// 2005/11/24 加入 $limit_entries
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function search($mode=1, $dept='',$limit_entries=0) {

    $sql = $this->sql;
    $argv = $GLOBALS;   //將所有的 globals 都抓入$argv

    $srh = new SEARCH();
    $cgi = array();
    if (!$srh->set_sql($sql)) {
        $this->msg->merge($srh->msg);
        return false;
    }

    $q_header = "SELECT po_ship_det.*,po_ship.*
                             FROM po_ship,po_ship_det ";
                             
    $q_header = "SELECT po_ship_det.*,po_ship.* FROM po_ship left join po_ship_det on (`po_ship`.`id` = `po_ship_det`.`ship_id`)";
    
    if (!$srh->add_q_header($q_header)) {
        $this->msg->merge($srh->msg);
        return false;
    }
    $srh->add_cgi_parm("PHP_action",$argv['PHP_action']);
    $srh->add_sort_condition("po_ship.id DESC");
    $srh->row_per_page = 20;

	if($limit_entries){    // 當有限定最大量時~~~ 2005/11/28 加入
			$srh->q_limit = "LIMIT ".$limit_entries." ";
	}else{
        ##--*****--2006.11.16頁碼新增 start		##		
		$pagesize=10;
		if ($argv['PHP_sr_startno']) {
			$pages = $srh->get_page($argv['PHP_sr_startno'],$pagesize);	
        }else{
        	$pages = $srh->get_page(1,$pagesize);
    	} 
        ##--*****--2006.11.16頁碼新增 end	   ##
	}
    // $srh->add_where_condition("`po_ship`.`id` = `po_ship_det`.`ship_id`");
    $srh->add_group_condition("po_ship.ship_inv");

    if ($mode==1){
		$mesg = '';
		if ($str = strtoupper($argv['SCH_f_date']) )  { 
			$srh->add_where_condition("po_ship.ship_date >= '$str'", "SCH_f_date",$str); 
			$mesg.= "  Ship date : [ $str ] ~ ";
			}
		if ($str = strtoupper($argv['SCH_e_date']) )  { 
			$srh->add_where_condition("po_ship.ship_date <= '$str'", "SCH_e_date",$str); 
			if(!$argv['SCH_f_date'])$mesg.= "  Ship date : ~";
			$mesg.= "  ~ [ $str ] . ";
			}		
		if ($str = $argv['SCH_ship_num'] )  { 
			$srh->add_where_condition("po_ship.num like '%$str%'", "SCH_ship_num",$str); 
			$mesg.= "  Ship num #  : [ $str ]. ";
			}
		if ($str = $argv['SCH_org'] )  { 
			$srh->add_where_condition("po_ship.org = '$str'", "SCH_org",$str); 
			$mesg.= "  Ship From : [ $str ]. ";
			}
		if ($str = $argv['SCH_dist'] )  { 
			$srh->add_where_condition("po_ship.dist = '$str'", "SCH_dist",$str); 
			$mesg.= "  Ship To : [ $str ]. ";
			}
		if ($str = $argv['SCH_way'] )  { 
			$srh->add_where_condition("po_ship.ship_way = '$str'", "SCH_dist",$str); 
			$mesg.= "  Ship BY : [ $str ]. ";
			}
		if ($str = $argv['SCH_bl_num'] )  {
			$srh->add_where_condition("po_ship.carrier like '%$str%'", "SCH_carrier",$str); 
			$mesg.= "  B.L #  : [ $str ]. ";
			}
		if ($str = $argv['SCH_shipper'] )  {
			$srh->add_where_condition("po_ship.shipper like '%$str%'", "SCH_carrier",$str); 
			$mesg.= "  Ship user : [ $str ]. ";
			}	
		if ($str = $argv['SCH_inv'] )  { 
			$srh->add_where_condition("po_ship_det.inv_num like '%$str%'", "SCH_inv",$str); 
			$mesg.= "  Invoice #  : [ $str ]. ";
			}			
		if ($mesg)
		{
			$msg = "Search ".$mesg;
			$this->msg->add($msg);
		}			
    }	

    $result= $srh->send_query2($limit_entries);   // 2005/11/24 加入 $limit_entries
    if (!is_array($result)) {
        $this->msg->merge($srh->msg);
        return false;		    
    }

    $this->msg->merge($srh->msg);
    if (!$result){   // 當查尋無資料時
        $op['record_NONE'] = 1;
    }
    
    $op['ship'] = $result;  // 資料錄 拋入 $op
    $op['cgistr_get'] = $srh->get_cgi_str(0);
    $op['cgistr_post'] = $srh->get_cgi_str(1);
    $op['prev_no'] = $srh->prev_no;
    $op['next_no'] = $srh->next_no;
    $op['max_no'] = $srh->max_no;
    $op['last_no'] = $srh->last_no;
    $op['start_no'] = $srh->start_no;
    $op['per_page'] = $srh->row_per_page;
		
    if(!$limit_entries){ 
        ##--*****--2006.11.16頁碼新增 start			
		$op['maxpage'] =$srh->get_max_page();
		$op['pages'] = $pages;
		$op['now_pp'] = $srh->now_pp;
        $op['lastpage']=$pages[$pagesize-1];		
        ##--*****--2006.11.16頁碼新增 end
    }	
    // echo $srh->q_str;
    return $op;
} // end func	
	
	
	
	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->edit($parm,$mode=0)		更新 訂單 記錄 
#			mode=0 : EDIT    mode=1 : REVISE
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function update_field_id($f_name,$f_value,$id,$table='po_ship') {

    $sql = $this->sql;		
    $q_str = "UPDATE ".$table." SET ".$f_name."='".$f_value."'  WHERE id='".$id."'";

    if (!$q_result = $sql->query($q_str)) {
        $this->msg->add("Error !  Database can't update.");
        $this->msg->merge($sql->msg);
        return false;    
    }
   
    return true;
} // end func		
	
	
	
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_by_po($id=0, $order_num=0)	抓出指定訂單記錄資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_by_po($id,$special) {

	$sql = $this->sql;

	$op['ship'] = array();
	$q_str="SELECT po_ship_det.*, po_ship.*
					FROM  po_ship_det, po_ship
					WHERE po_ship_det.ship_id = po_ship.id AND 								
							  po_ship_det.po_id='".$id."'								  					  
				  ORDER BY po_ship_det.id";

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	$i=0;
	while ($row1 = $sql->fetch($q_result)) {

		$row1['carrier_link'] = $this->get_carrier_link($row1['carrier'],$row1['ex_cmpy']);
		$ship[]=$row1;

	}

	return $ship;
} // end func


	
function get_carrier_link($carrier,$ex_cmpy)
{
			$tmp_carr = $carr_link = '';
			if($ex_cmpy == 'DHL')
			{
				$tmp = explode('-',$carrier);
				if(sizeof($tmp) > 1)
				{
					for($j=0; $j<sizeof($tmp); $j++)$tmp_carr .= $tmp[$j];
				}else{
					$tmp = explode(' ',$carrier);	
					if(sizeof($tmp) > 1)
					{
						for($j=0; $j<sizeof($tmp); $j++)$tmp_carr .=$tmp[$j];
					}
				}
				$carr_link = "http://www.dhl.com.tw/publish/tw/zt/eshipping/track.high.html?pageToInclude=RESULTS&type=trackindex&awb_hidden=".$tmp_carr."+&di=00%7CJ%7C1J%7C2J%7C3J%7C4J%7C5J%7C6J&iac=%7C0%7C1%7C2%7C3%7C4%7C5%7C6%7C7%7C8%7C9%7CD%7CJ%7CKNO%7CLA%7CLB%7CLE%7CLF%7CLH%7CND%7CNL%7COD%7CPA%7CSI%7CST%7CUN%7CUT%7CVGL%7CVIB%7C&brand=DHL&AWB=".$carrier;

			}else if($ex_cmpy == 'FedEx'){
				$carr_link = "http://www.fedex.com/Tracking?sum=n&ascend_header=1&clienttype=dotcomreg&spnlk=spnl0&initial=n&cntry_code=tw&tracknumber_list=".$carrier;

			}
			return $carr_link;
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get($id=0, $order_num=0)	抓出指定訂單記錄資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function create_html($op)	
{
		$txt1='';
		$txt1.="<table border=1 cellspacing=1 cellpadding=3 width=750 bgcolor=#FFFFFF>";				
		$txt1.="<tr>";
		$txt1.="<td class=snvy align=left colspan=4 ><b>&nbsp;&nbsp;&nbsp;From :&nbsp; ".$op['po_ship']['org']."&nbsp;&nbsp; TO : &nbsp;".$op['po_ship']['dist']."</b> </td>";
		$txt1.="<td class=snvy align=right ><b>Carrier # : </b> </td>";
		$txt1.="<td class=frnt align=left >".$op['po_ship']['carrier']	 ."</td>";
		$txt1.="</tr>";
		$txt1.="<tr>";
		$txt1.="<td class=snvy align=right colspan=2 ><b>SHIP BY : &nbsp;".$op['po_ship']['ship_way']."</b> </td>";
		$txt1.="<td class=snvy align=right colspan=2 ><b>SHIP Date : ".$op['po_ship']['ship_date']."</b> </td>";
		$txt1.="<td class=snvy align=right colspan=2 ><b>ETA : ".$op['po_ship']['ship_eta']."</b> </td>";
		$txt1.="</tr>";
	
		$txt1.="<tr height=28>";
		$txt1.="<td width=30 class=back align=center><b>C/NO</b></td>";
		$txt1.="<td width=80 class=back align=center><b>Mat. Num</b></td>";
		$txt1.="<td class=back  align=center><b>Material</b></td>";
		$txt1.="<td width=60 class=back align=center><b>Order</b></td>";
		$txt1.="<td width=70  class=back align=center><b>Price</b></td>";
		$txt1.="<td width=80  class=back align=center><b>QTY</b></td>";
		$txt1.="</tr>";
	for($i=0; $i<sizeof($op['ship']); $i++)
	{
		$txt1.="<tr>";
		$txt1.="<td  align=left nowrap>&nbsp;<small>".$op['ship'][$i]['mat_code']."</small></td>";
		$txt1.="<td  align=left nowrap>&nbsp;<small>".$op['ship'][$i]['mat_name']." : [ ".$op['ship'][$i]['color']." ]</small></td>";
		$txt1.="<td  align=left nowrap><small>";
		if($op['ship'][$i]['special'] == 0)
		{		
			for($k=0; $k<sizeof($op['ship'][$i]['ord_num']); $k++)
			{
				$txt1.=$op['ship'][$i]['ord_num'][$k]."<BR>";
			}
		}else{
				$txt1.=$op['ship'][$i]['ord_num']."<BR>";
		}
	

		$txt1.="</td>";
		$txt1.="<td  align=right><small>".$op['ship'][$i]['currency']."$".$op['ship'][$i]['prics']."/".$op['ship'][$i]['prc_unit']."</small></td>";
		$txt1.="<td  align=right>".$op['ship'][$i]['qty']."&nbsp;".$op['ship'][$i]['po_unit']."</td>";
		$txt1.="</tr>";
	}
$txt1.="</table>";
 
 		return $txt1;
	}// end function
	
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_ship_qty($po_id,$spec)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_ship_qty($ap_num,$mat_cat,$mat_id,$color,$size,$spec='',$inv_num=''){

	$sql = $this->sql;
	
	$where_str = '';
	if(isset($spec)) $where_str.= " and special='".$spec."'";
	if($inv_num) $where_str.= " and inv_num='".$inv_num."'";
	
	$q_str = "SELECT sum(qty) as shipped_qty FROM po_ship_det 
				WHERE `ap_num` = '".$ap_num."' AND `mat_cat` = '".$mat_cat."' AND `mat_id` = '".$mat_id."' AND `color` = '".$color."' AND `size` = '".$size."'  ".$where_str." group BY ap_num";
// echo $q_str.'<br>';
    if (!$q_res = $sql->query($q_str)) {
        $this->msg->add("Error ! Database can't access!");
        $this->msg->merge($sql->msg);
        return false;    
    }
    $row = $sql->fetch($q_res);
    return $row['shipped_qty'];
}	

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->update_field($field,$value,$where_str,$table)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function update_field($field,$value,$where_str,$table) {
	$sql = $this->sql;		
	$q_str = "UPDATE ".$table." SET ".$field." = '".$value."' where ".$where_str;

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error !  Database can't update.");
		$this->msg->merge($sql->msg);
		return false;    
	}
	return true;
}	


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_ship_rcv_qty($ship_num,$po_id,$spec)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_ship_rcv_qty($ship_num,$po_id,$spec='',$inv_num=''){

	$sql = $this->sql;
	
	$where_str = '';
	if(isset($spec)) $where_str.= " and po_ship_det.special='".$spec."'";
	if($inv_num) $where_str.= " and po_ship_det.inv_num='".$inv_num."'";
	
	$q_str = "SELECT sum(po_ship_det.qty) as shipped_qty FROM po_ship, po_ship_det 
				WHERE po_ship.num='$ship_num' and po_ship_det.ship_id = po_ship.id and po_ship_det.po_id='".$po_id."' ".$where_str." group BY po_ship_det.po_id";

			if (!$q_res = $sql->query($q_str)) {
				$this->msg->add("Error ! Database can't access!");
				$this->msg->merge($sql->msg);
				return false;    
			}
			$row = $sql->fetch($q_res);
			return $row['shipped_qty'];
}


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->ship_del($id)	刪除ship檔案
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function ship_del($id) {
	$sql = $this->sql;
 	$q_str="DELETE FROM po_ship WHERE id='".$id."'";
 	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error !  Database can't update.");
		$this->msg->merge($sql->msg);
		return false;    
	}     
	return true;
}// end func

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->file_del($id)	刪除ship檔案
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function file_del($id) {
	$sql = $this->sql;
 	$q_str="DELETE FROM po_ship_file WHERE id='".$id."'";
 	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error !  Database can't update.");
		$this->msg->merge($sql->msg);
		return false;    
	}     
	return true;
}// end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->ship_set($id)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function ship_set($id, $parm) {
	$sql = $this->sql;
 	$q_str = "UPDATE po_ship SET ".
							"ship_set_user = '"	.$parm['ship_set_user'].
							"', ship_set_date = '".$parm['ship_set_date'].
							"' WHERE id = '".$id."'";
	
 	if ($q_result = $sql->query($q_str)) {
		return 1;
	}else{
		return 0;
	}
	
}// end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->ship_finish($id)
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function ship_finish($id, $parm) {
	$sql = $this->sql;
 	$q_str = "UPDATE po_ship SET ".
							"ship_finish_user = '"	.$parm['ship_finish_user'].
							"', ship_finish_date = '".$parm['ship_finish_date'].
							"' WHERE id = '".$id."'";
	
 	if ($q_result = $sql->query($q_str)) {
		return 1;
	}else{
		return 0;
	}
	
}// end func


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	->get_by_bom_id($ap_num,$mat_id,$color,$size)	抓出指定訂單記錄資料 RETURN $row[]
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function get_by_bom_id($bom_id, $mat_cat) {

	$sql = $this->sql;

	$ship = array();
	$q_str = "SELECT po_ship.*, po_ship_det.qty
			  FROM  po_ship_det, po_ship, ap_det
			  WHERE ap_det.bom_id = '".$bom_id."' and ap_det.mat_cat = '".$mat_cat."' and po_ship_det.po_id = ap_det.po_spare and 
					po_ship_det.ship_id = po_ship.id 
			  ORDER BY po_ship.id";

	if (!$q_result = $sql->query($q_str)) {
		$this->msg->add("Error ! Database can't access!");
		$this->msg->merge($sql->msg);
		return false;    
	}
	
	$i=0;
	while ($row1 = $sql->fetch($q_result)) {
		$ship[] = $row1;
	}

	return $ship;
} // end func


} // end class


?>