<?php 

class  W_DAILY_OUT {
	
var $sql;
var $msg ;

#
#
#
#
#
#
# :SQL LINK
function init($sql) {

	$this->msg = new MSG_HANDLE();

	if (!$sql) {
		$this->msg->add("Error ! Can't connect to database.");
		return false;
	}
	
	$this->sql = $sql;
	
	return true;
} # FUN END
#
#
#
#
#
#
# :get_daily_out_put
function get_daily_out_put($date) {

$db_utf = getDBconnectUTF();

$fields = false;

$q_str = "
SELECT 
`workdone`.`ScanTime`,`workdone`.`PackedQty`,`workdone`.`FailedQty`,`workdone`.`SONumber`,
`ticketreaders`.`Line`
FROM `workdone` , `ticketreaders` 
WHERE `workdone`.`StepId` = 900 AND 
`workdone`.`ReaderId` = `ticketreaders`.`ReaderId` AND 
`workdone`.`ScanTime` LIKE '".$date."%' 
GROUP BY `workdone`.`WorkdoneId`
;";
//echo $q_str.'<br>';
if (!$q_result = mysql_db_query('dbo',$q_str,$db_utf)) {
	$_SESSION['MSG'][] = "Error! Can't access database!";
	return false;    
}

while ($row = mysql_fetch_array($q_result)) {
	$fields[] = $row;
}

if(!empty($fields))
return $fields;

} # FUN END
#
#
#
#
#
#
# :GET_CARRIER_NUM
function get_daily_capacity($date) {

//echo $date;
//exit;

$db_utf = getDBconnectUTF();

$fields = false;

$q_str = "SELECT 
`sectioncapacitysetting`.`SectionCode`,`sectioncapacitysetting`.`LotId`,
`sectioncapacitysetting`.`SettingDate`,`sectioncapacitysetting`.`Qty`,
`s_order`.`ie1`,`s_order`.`ie2`,`s_order`.`qty` 
FROM `sectioncapacitysetting` , `s_order` 
WHERE 
`sectioncapacitysetting`.`LotId` = `s_order`.`order_num` AND 
`sectioncapacitysetting`.`SettingDate` = '".$date." 00:00:00' 
;";

//echo $q_str.'<br>';
if (!$q_result = mysql_db_query('dbo',$q_str,$db_utf)) {
	$_SESSION['MSG'][] = "Error! Can't access database!";
	return false;    
}

while ($row = mysql_fetch_array($q_result)) {
    $row['SectionCode'] = substr($row['SectionCode'],-2,2);
	$fields[] = $row;
}
//print_r($fields);
if(!empty($fields))
return $fields;

} # FUN END
#
#
#
#
#
#
# :GET_SCM_CAPACITY
function get_scm_capacity($date,$fty) {

$sql = $this->sql;

// $Date_arr = explode('-',$date);
// $week = date("w",mktime(0,0,0,$Date_arr[1],$Date_arr[2],$Date_arr[0]));
// $Day = ( $week == 6 ) ? '+2' : '+1';

$out_date = $this->get_scm_wday($date,$fty);

$query  = " SELECT 
`saw_out_put`.`line_id` , 
`saw_out_put`.`saw_line` , 
`saw_out_put`.`out_date` , 
`saw_out_put`.`ord_num` , 
`saw_out_put`.`qty` , 
`saw_out_put`.`su` , 
`saw_out_put`.`workers` , 
`saw_out_put`.`ot_wk` , 
`saw_out_put`.`ot_hr` 
FROM `saw_out_put` , `pdt_saw_line` 
WHERE `saw_out_put`.`holiday` = '0' AND 
`pdt_saw_line`.`id` = `saw_out_put`.`line_id` AND 
`saw_out_put`.`saw_fty` = '".$fty."' AND 
`saw_out_put`.`out_date` = '".$out_date."' AND 
`saw_out_put`.`qty` > 0 ";

// echo $query.'<br>';

$saw_out_put = array();

if( $result = $sql->query($query) ){
    while($row = $sql->fetch($result)){
        // echo $row['saw_line'].'<br>';
        $saw_out_put[$row['saw_line']*1][$row['ord_num']] = number_format((($row['workers']*8)+($row['ot_wk']*$row['ot_hr']))*1.2);
        // print_r($saw_out_put);
    }
}

if(!empty($saw_out_put))
return $saw_out_put;

} # FUN END
function get_online_date($daily_output) {
	$sql = $this->sql;
	//print_r($daily_output);
	//exit;
	$saw_out_put = array();
	
	
	for($i=0;$i<sizeof($daily_output);$i++)
	{
		//print_r($daily_output);
		$saw_out_put_day = array();
		$query  = "SELECT ord_num ,saw_line ,out_date ,saw_fty  ";
		$query  .="FROM saw_out_put ";
		$query  .="WHERE holiday = '0' AND saw_fty='LY' AND ord_num = '";
		$query  .=$daily_output[$i]['LotId'];
		$query  .="' AND saw_line ='";
		$query  .=$daily_output[$i]['SectionCode'];
		//$query  .="' ";
		$query  .="' AND qty > 0  ";
		$query  .="AND out_date <= '".date("Y-m-d",strtotime($daily_output[$i]['SettingDate']))."' ";
		$query  .="group by ord_num , saw_line , out_date , saw_fty ";
		$query  .="order by ord_num , saw_line , out_date";  
		
		
		
		if( $result = $sql->query($query) ){
		//echo mysql_num_rows($result)."<br>";
			while($row = $sql->fetch($result)){
			
				$saw_out_put_day[] = $row;
			
			}
			$daily_output[$i]['online_day'] = sizeof($saw_out_put_day);
		} 
		else
		{
			$daily_output[$i]['online_day'] = 'ip13_sorder';
		}
		//echo sizeof($saw_out_put_day);
		/* $daily_output[$i]['online_day'] = sizeof($saw_out_put_day); */
		//print_r($saw_out_put_day);
		//echo "<hr>";
		//exit;
		//echo count($saw_out_put_day)."<br>";
	}
	//print_r($daily_output);


	if(!empty($daily_output))
	return $daily_output;
	
}
#
#
#
#
#
#
# :GET_SCM_CAPACITY
function get_scm_full_capacity($date,$fty) {

$sql = $this->sql;

// $Date_arr = explode('-',$date);
// $week = date("w",mktime(0,0,0,$Date_arr[1],$Date_arr[2],$Date_arr[0]));
// $Day = ( $week == 6 ) ? '+2' : '+1';
// echo $date;
// echo $out_date = increceDaysInDate($date,$Day);

// $hd_ary = $_SESSION['hd_ary'][$fty][$Date_arr[0]][$Date_arr[1]];

// if( in_array($out_date,$hd_ary) ){
    // $out_date = increceDaysInDate($out_date,$Day+1);
// }

$out_date = $this->get_scm_wday($date,$fty);

$query  = " SELECT 
`saw_out_put`.`line_id` , 
`saw_out_put`.`saw_line` , 
`saw_out_put`.`out_date` , 
`saw_out_put`.`ord_num` , 
`saw_out_put`.`qty` , 
`saw_out_put`.`su` , 
`saw_out_put`.`workers` , 
`saw_out_put`.`ot_wk` , 
`saw_out_put`.`ot_hr` ,
`pdt_saw_line`.`worker` 
FROM `saw_out_put` , `pdt_saw_line` 
WHERE `saw_out_put`.`holiday` = '0' AND 
`pdt_saw_line`.`id` = `saw_out_put`.`line_id` AND 
`saw_out_put`.`saw_fty` = '".$fty."' AND 
`saw_out_put`.`out_date` = '".$out_date."' AND 
`saw_out_put`.`qty` > 0 ";

// echo $query.'<br>';
// exit;
$saw_out_put = array();

if( $result = $sql->query($query) ){
    while($row = $sql->fetch($result)){
        // echo $row['worker'].' ~ '.$row['ot_hr'].'<br>';
        // $saw_out_put[$row['saw_line']*1][$row['ord_num']]['full_su'] += number_format((($row['workers']*8)+($row['ot_wk']*$row['ot_hr']))*1.2);
        if( $row['ot_hr'] > 0 ){
            $saw_out_put[$row['saw_line']*1][$row['ord_num']]['full_su'] += number_format((($row['workers']*8)+($row['ot_wk']*($row['ot_hr']-0.5)))*1.2);
        } else {
            $saw_out_put[$row['saw_line']*1][$row['ord_num']]['full_su'] += number_format((($row['workers']*8)+($row['ot_wk']*$row['ot_hr']))*1.2);
        }
        // $saw_out_put[$row['saw_line']*1][$row['ord_num']]['qty_str'] = '編制:'.$row['worker'].' , 到班:'.$row['workers'].' , 加班:'.$row['ot_wk'];
        $saw_out_put[$row['saw_line']*1][$row['ord_num']]['qty_str'] .= '到班:'.$row['workers'].' , 加班:'.$row['ot_wk'].' , 加班時數:'.$row['ot_hr']."\n";
        // echo $saw_out_put[$row['saw_line']*1][$row['ord_num']].'<br>';
        // print_r($saw_out_put);
    }
}

if(!empty($saw_out_put))
return $saw_out_put;

} # FUN END
#
#
#
#
#
## :GET_SET_NUM
function get_set_num($line,$order) {

$db_utf = getDBconnectUTF();

$fields = false;

$q_str = "
select DISTINCT ticketreaders.name,flowslots.lotid,ticketreaders.line,ticketreaderflowsg.readerid
from flowslots,flowssg,ticketreaderflowsg,ticketreaders
where 
flowssg.flowid = flowslots.flowid and 
ticketreaderflowsg.flowsgid = flowssg.flowsgid and 
ticketreaders.readerid = ticketreaderflowsg.readerid and 
flowslots.Lotid = '".$order."' and ticketreaders.line = '".$line."' 
ORDER BY flowslots.lotid,ticketreaders.line,ticketreaders.name,ticketreaderflowsg.readerid 
;";

// echo $q_str.'<br>';
// echo date('h:i:s').'<p>';
if (!$q_result = mysql_db_query('dbo',$q_str,$db_utf)) {
	$_SESSION['MSG'][] = "Error! Can't access database!";
	return false;    
}
$MOD = 1;
while ($row = mysql_fetch_array($q_result)) {
	$fields[] = str_pad($MOD,2,'0',STR_PAD_LEFT).' ) '.$row['name'];
    $MOD++;
}

// print_r($fields);
if(!empty($fields)){
    $fields = implode ("\n", $fields);
    return $fields;
}

} # FUN END
#
#
#
#
#
## :GET_SCM_WDAY
function get_scm_wday($date,$fty) {

$sql = $this->sql;

# 2014-06-09 開始導正輸入當天的產量，不再當天輸入前一天的產量
if( $date >= '2014-06-09' ) {

$query  = " SELECT 
`saw_out_put`.`out_date` 
FROM `saw_out_put` 
WHERE 
`saw_out_put`.`saw_fty` = '".$fty."' AND 
`saw_out_put`.`out_date` = '".$date."' AND 
`saw_out_put`.`holiday` = 0 LIMIT 0 , 1 ";

} else {

$query  = " SELECT 
`saw_out_put`.`out_date` 
FROM `saw_out_put` 
WHERE 
`saw_out_put`.`saw_fty` = '".$fty."' AND 
`saw_out_put`.`out_date` > '".$date."' AND 
`saw_out_put`.`holiday` = 0 LIMIT 0 , 1 ";


}
// echo $query.'<br>';

$out_date = '';
$row = $sql->fetch($result);
if( $result = $sql->query($query) ){
    $row = $sql->fetch($result);
    $out_date = $row['out_date'];
}

if(!empty($out_date))
return $out_date;

} # FUN END
#
#
#
#
#
## :GET_SCM_OUTPUT
function get_scm_output($order,$date) {

$sql = $this->sql;

$query  = " SELECT 
sum(`saw_out_put`.`qty`) as `output`
FROM `saw_out_put` 
WHERE 
`saw_out_put`.`ord_num` = '".$order."' AND `saw_out_put`.`out_date` <= '".$date."'
GROUP By `saw_out_put`.`ord_num` ";

// echo $query.'<br>';

$output = '';
$row = $sql->fetch($result);
if( $result = $sql->query($query) ){
    $row = $sql->fetch($result);
    $output = $row['output'];
}

if(!empty($output))
return $output;

} # FUN END
#
#
#
#
#
## :GET_RFID_OUTPUT
function get_rfid_output($order,$date) {

$db_utf = getDBconnectUTF();

$fields = false;

$q_str = "SELECT `workdone`.`WorkdoneId`,
sum(`workdone`.`PackedQty`) as `output`
FROM `workdone` , `ticketreaders` 
WHERE `workdone`.`StepId` = 900 AND 
`workdone`.`ReaderId` = `ticketreaders`.`ReaderId` AND 
`workdone`.`SONumber` = '".$order."' AND 
`workdone`.`ScanTime` <= '".$date." 99:99:99' 
GROUP BY `workdone`.`SONumber`
;";
// echo $q_str.'<br>';
if (!$q_result = mysql_db_query('dbo',$q_str,$db_utf)) {
	$_SESSION['MSG'][] = "Error! Can't access database!";
	return false;    
}

$output = '';
$row = mysql_fetch_array($q_result);
$output = $row['output'];

if(!empty($output))
return $output;

} # FUN END

function get_order($date_start,$date_end) {
    $db_utf = getDBconnectUTF();
    
    $fields = false;
    
    
    $q_str = "SELECT 
    `sectioncapacitysetting`.`SectionCode`,
    `sectioncapacitysetting`.`LotId`,
	`sectioncapacitysetting`.`SettingDate`,
    `s_order`.`ie1`,
    `s_order`.`ie2`,
    `s_order`.`qty`
    FROM `sectioncapacitysetting` , `s_order` 
    WHERE 
    `sectioncapacitysetting`.`LotId` = `s_order`.`order_num` AND 
    `sectioncapacitysetting`.`SettingDate` between '".$date_start." 00:00:00' and '".$date_end." 00:00:00' 
    group by `sectioncapacitysetting`.`SectionCode`,
    `sectioncapacitysetting`.`LotId`,
    `s_order`.`ie1`,
    `s_order`.`ie2`,
    `s_order`.`qty` 
    order by `sectioncapacitysetting`.`SettingDate`,
	`sectioncapacitysetting`.`SectionCode`,
	`sectioncapacitysetting`.`LotId`"
    ;
    
    /*
    $q_str = "SELECT 
    `sectioncapacitysetting`.`SectionCode`,`sectioncapacitysetting`.`LotId`,
    `sectioncapacitysetting`.`SettingDate`,`sectioncapacitysetting`.`Qty`,
    `s_order`.`ie1`,`s_order`.`ie2`,`s_order`.`qty` 
    FROM `sectioncapacitysetting` , `s_order` 
    WHERE 
    `sectioncapacitysetting`.`LotId` = `s_order`.`order_num` AND 
    `sectioncapacitysetting`.`SettingDate` = '".$date." 00:00:00' 
    ;";
    */
    //echo $q_str.'<br>';
    if (!$q_result = mysql_db_query('dbo',$q_str,$db_utf)) {
    	$_SESSION['MSG'][] = "Error! Can't access database!";
    	return false;    
    }
    
    while ($row = mysql_fetch_array($q_result)) {
        //$row['SectionCode'] = substr($row['SectionCode'],-2,2);
    	$fields[] = $row;
    }
    
    if(!empty($fields))
    return $fields;
    
}# FUN END
function get_orderProc($order) {
    $db_utf = getDBconnectUTF();
    
    $fields = false;
	
	
	
    $sql="SELECT `LotId`,`StepId`,`Sam`,`StepDesc`,`Code`  
	FROM `LotsSteps` 
    WHERE `LotId` in ('";
    for($i=0;$i<sizeof($order);$i++)
    {
        if($i==(sizeof($order)-1))
        {
            $sql.= $order[$i];
        }
        else
        {
            $sql.= $order[$i]."','";          
        }
                
    }
    
    $sql .= "') 
    and `StepDesc`!='' 
    ORDER BY `LotId`,`StepId`";  
    //echo $sql."<br>";
    if (!$q_result = mysql_db_query('dbo',$sql,$db_utf)) {
    	$_SESSION['MSG'][] = "Error! Can't access database!";
    	return false;    
    }
    
    while ($row = mysql_fetch_array($q_result)) {
        //$row['SectionCode'] = substr($row['SectionCode'],-2,2);
    	$fields[] = $row;
    }
    
    if(!empty($fields))
    return $fields;


    
}# FUN END
function get_orderProcQty($order,$date_start,$date_end) {
    $db_utf = getDBconnectUTF();
    
    $fields = false;
    //組合查詢訂單語法(訂單的生產日區間的生產量)
    //SELECT * FROM `workdone` WHERE `LotId`='ARE14-0117' and ScanTime between '' and ''
    /*
    $sql="select LotId,StepId,Sam,StepDesc,code from LotsSteps
    where lotid in ('";
    for($i=0;$i<sizeof($order);$i++)
    {
        if($i==(sizeof($order)-1))
        {
            $sql.= $order[$i];
        }
        else
        {
            $sql.= $order[$i]."','";          
        }
                
    }
    
    $sql .= "') 
    and stepdesc !='' 
    order by LotId,StepId";  
    */
    if (!$q_result = mysql_db_query('dbo',$sql,$db_utf)) {
    	$_SESSION['MSG'][] = "Error! Can't access database!";
    	return false;    
    }
    
    while ($row = mysql_fetch_array($q_result)) {
        //$row['SectionCode'] = substr($row['SectionCode'],-2,2);
    	$fields[] = $row;
    }
    
    if(!empty($fields))
    return $fields;
    
    
}# FUN END
#
#
#
#
#
#
# :GET_DAILY_OUT_PUT_FOR_LINE
function get_daily_out_put_for_line($date,$line) {

$db_utf = getDBconnectUTF();

$fields = false;

$q_str = "
SELECT 
`workdone`.`ScanTime`,`workdone`.`PackedQty`,`workdone`.`FailedQty`,`workdone`.`SONumber`,
`ticketreaders`.`Line` , `workdone`.`StepId` , `workdone`.`ReaderName` , `workdone`.`WorkerId` ,
`workdone`.`StepPrice` , `workdone`.`StepSam` , `LotsSteps`.`Sas`
FROM `workdone` LEFT JOIN `ticketreaders` ON ( `workdone`.`ReaderId` = `ticketreaders`.`ReaderId` ) LEFT JOIN `LotsSteps` ON ( `LotsSteps`.`LotId` = `workdone`.`SONumber` AND  `LotsSteps`.`StepId` = `workdone`.`StepId`  )
WHERE 
`workdone`.`ScanTime` LIKE '".$date."%' AND
`ticketreaders`.`Line` = '".$line."' 
GROUP BY `workdone`.`WorkdoneId`
ORDER BY `workdone`.`ReaderName` ASC , `workdone`.`StepId` ASC
;";

// echo $q_str.'<br>';
if (!$q_result = mysql_db_query('dbo',$q_str,$db_utf)) {
	$_SESSION['MSG'][] = "Error! Can't access database!";
	return false;    
}

while ($row = mysql_fetch_array($q_result)) {
	$fields[] = $row;
}

if(!empty($fields))
return $fields;

} # FUN END
#
#
#
#
#
#
# :GET_DAILY_CAPACITY_FOR_LINE
function get_daily_capacity_for_line($date,$line) {

$db_utf = getDBconnectUTF();

$fields = false;

$q_str = "SELECT 
`sectioncapacitysetting`.`SectionCode`,`sectioncapacitysetting`.`LotId`,
`sectioncapacitysetting`.`SettingDate`,`sectioncapacitysetting`.`Qty`,
`s_order`.`ie1`,`s_order`.`ie2`,`s_order`.`qty` 
FROM `sectioncapacitysetting` , `s_order` 
WHERE 
`sectioncapacitysetting`.`LotId` = `s_order`.`order_num` AND 
`sectioncapacitysetting`.`SettingDate` = '".$date." 00:00:00' AND
`sectioncapacitysetting`.`SectionCode` = '033".str_pad($line,3,'0',STR_PAD_LEFT)."' 
;";
//echo $q_str.'<br>';
if (!$q_result = mysql_db_query('dbo',$q_str,$db_utf)) {
	$_SESSION['MSG'][] = "Error! Can't access database!";
	return false;    
}

while ($row = mysql_fetch_array($q_result)) {
    $row['SectionCode'] = substr($row['SectionCode'],-2,2);
	$fields[] = $row;
}

if(!empty($fields))
return $fields;

} # FUN END
#
#
#
#
#
#
# :GET_SCM_FULL_CAPACITY_FOR_LINE
function get_scm_full_capacity_for_line($date,$fty,$line) {

$sql = $this->sql;

// $Date_arr = explode('-',$date);
// $week = date("w",mktime(0,0,0,$Date_arr[1],$Date_arr[2],$Date_arr[0]));
// $Day = ( $week == 6 ) ? '+2' : '+1';
// echo $date;
// echo $out_date = increceDaysInDate($date,$Day);

// $hd_ary = $_SESSION['hd_ary'][$fty][$Date_arr[0]][$Date_arr[1]];

// if( in_array($out_date,$hd_ary) ){
    // $out_date = increceDaysInDate($out_date,$Day+1);
// }

$out_date = $this->get_scm_wday($date,$fty);

$query  = " SELECT 
`saw_out_put`.`line_id` , 
`saw_out_put`.`saw_line` , 
`saw_out_put`.`out_date` , 
`saw_out_put`.`ord_num` , 
`saw_out_put`.`qty` , 
`saw_out_put`.`su` , 
`saw_out_put`.`workers` , 
`saw_out_put`.`ot_wk` , 
`saw_out_put`.`ot_hr` ,
`pdt_saw_line`.`worker` 
FROM `saw_out_put` , `pdt_saw_line` 
WHERE `saw_out_put`.`holiday` = '0' AND 
`pdt_saw_line`.`id` = `saw_out_put`.`line_id` AND 
`saw_out_put`.`saw_fty` = '".$fty."' AND 
`saw_out_put`.`out_date` = '".$out_date."' AND 
`saw_out_put`.`qty` > 0 AND
`saw_out_put`.`saw_line` = '".$line."'  ";

// echo $query.'<br>';
// exit;
$saw_out_put = array();

if( $result = $sql->query($query) ){
    while($row = $sql->fetch($result)){
        // echo $row['worker'].' ~ '.$row['ot_hr'].'<br>';
        // $saw_out_put[$row['saw_line']*1][$row['ord_num']]['full_su'] += number_format((($row['workers']*8)+($row['ot_wk']*$row['ot_hr']))*1.2);
        if( $row['ot_hr'] > 0 ){
            $saw_out_put[$row['saw_line']*1][$row['ord_num']]['full_su'] += number_format((($row['workers']*8)+($row['ot_wk']*($row['ot_hr']-0.5)))*1.2);
        } else {
            $saw_out_put[$row['saw_line']*1][$row['ord_num']]['full_su'] += number_format((($row['workers']*8)+($row['ot_wk']*$row['ot_hr']))*1.2);
        }
        // $saw_out_put[$row['saw_line']*1][$row['ord_num']]['qty_str'] = '編制:'.$row['worker'].' , 到班:'.$row['workers'].' , 加班:'.$row['ot_wk'];
        $saw_out_put[$row['saw_line']*1][$row['ord_num']]['qty_str'] .= 'workers:'.$row['workers'].' , ot_wk:'.$row['ot_wk'].' , ot_hr:'.$row['ot_hr']."\n";
        // echo $saw_out_put[$row['saw_line']*1][$row['ord_num']].'<br>';
        // print_r($saw_out_put);
    }
}

if(!empty($saw_out_put))
return $saw_out_put;

} # FUN END
#
#
#
#
#
#

} # CLASS END
?>