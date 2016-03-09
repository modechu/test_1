<?php 

class  W_LINE_QC {
	
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
# :GET_CARRIER_NUM
function get_daily_capacity($date) {

$db_utf = getDBconnectUTF();

$fields = false;

$q_str = "SELECT 
`sectioncapacitysetting`.`SectionCode`,`sectioncapacitysetting`.`LotId`,
`sectioncapacitysetting`.`SettingDate`,`sectioncapacitysetting`.`Qty`,
`s_order`.`ie1`,`s_order`.`ie2` 
FROM `sectioncapacitysetting` , `s_order` 
WHERE 
`sectioncapacitysetting`.`LotId` = `s_order`.`order_num` AND 
`sectioncapacitysetting`.`SettingDate` = '".$date." 00:00:00' 
;";
// echo $q_str.'<br>';
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
## :GET_SET_NUM
function get_set_num($line,$order,$Date) {

$db_utf = getDBconnectUTF();

$fields = false;

$q_str = "SELECT 
`sectioncapacitysetting`.`SectionCode`,`sectioncapacitysetting`.`LotId`,
`sectioncapacitysetting`.`SettingDate`,`sectioncapacitysetting`.`Qty`,
`s_order`.`ie1`,`s_order`.`ie2` 
FROM `ticketreaders` , `s_order` 
WHERE 
`ticketreaders`.`Line` = '".$line."' AND 
`ticketreaders`.`ReaderId` = `ticketreaderflowsg`.`ReaderId` AND  
`ticketreaders`.`ReaderId` = `ticketreaderflowsg`.`FlowSGId` AND 

// `ticketreaders`.`Line` = '".$order."' AND   
;";

$q_str = "
select DISTINCT ticketreaders.name,flowslots.lotid,ticketreaders.line,ticketreaderflowsg.readerid,workdone.WorkerId
from flowslots,flowssg,ticketreaderflowsg,ticketreaders left join workdone on ( ticketreaders.readerid = workdone.readerid and workdone.StepId != '900' )
where 
flowssg.flowid = flowslots.flowid and 
ticketreaderflowsg.flowsgid = flowssg.flowsgid and 
ticketreaders.readerid = ticketreaderflowsg.readerid and 
flowslots.Lotid = '".$order."' and 
ticketreaders.line = '".$line."' 
ORDER BY flowslots.lotid,ticketreaders.line,ticketreaders.name,ticketreaderflowsg.readerid 
;";

$q_str = "
select DISTINCT ticketreaders.name,flowslots.lotid,ticketreaders.line,ticketreaderflowsg.readerid
from flowslots,flowssg,ticketreaderflowsg,ticketreaders
where 
flowssg.flowid = flowslots.flowid and 
ticketreaderflowsg.flowsgid = flowssg.flowsgid and 
ticketreaders.readerid = ticketreaderflowsg.readerid and 
flowslots.Lotid = '".$order."' and 
ticketreaders.line = '".$line."' 
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
    // $row['SectionCode'] = substr($row['SectionCode'],-2,2);
	// $fields[] = str_pad($MOD,2,'0',STR_PAD_LEFT).' ) '.$row['name'].' / '.$this->get_WorkerId($row['readerid']).' / '.$row['readerid'];
	$fields[] = str_pad($MOD,2,'0',STR_PAD_LEFT).' ) '.$row['name'].' / '.$this->get_WorkerId($row['readerid'],$Date);
    // echo ($row['name']).'<br>';
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
## :GET_WORKERID
function get_WorkerId($readerid,$Date) {

$db_utf = getDBconnectUTF();

$fields = false;

$q_str = "
select `WorkerId`
from `workdone` 
where 
`readerid` = '".$readerid."' and 
`ScanTime` LIKE '".$Date."%' and 
`StepId` != '900'
LIMIT 1
;";

// echo $q_str.'<br>';
// echo date('h:i:s').'<p>';
if (!$q_result = mysql_db_query('dbo',$q_str,$db_utf)) {
	$_SESSION['MSG'][] = "Error! Can't access database!";
	return false;    
}

$num = mysql_num_rows($q_result);
if( $num > 0 ) {
    $fields = mysql_fetch_array($q_result);
    return $fields['WorkerId'];
} else {
    return false;
}

} # FUN END

#
#
#
#
#
#
} # CLASS END
?>