<?php
#
#
#
session_start();
//ini_set('display_errors', true);

// echo $PHP_action.'<br>';
#
#
#
//echo "test";
//phpinfo();
require_once "config.php";
require_once "config.admin.php";
require_once "init.object.php";
//require_once 'adodb.inc.php';
include_once($config['root_dir']."/lib/class.hr_attend.php");
//include_once($config['root_dir']."/ado/adodb.inc.php");
//GLOBAL $ADODB_FETCH_MODE;
	/* $conn = new COM("ADODB.Connection",NULL, CP_UTF8) or die("Cannot start ADO");
	$conn->Open("Provider=SQLOLEDB; Data Source=192.168.1.30;Initial Catalog=HR; User ID=lydbbackup; Password=carnival@@27113171");
	define(‘ADODB_FETCH_DEFAULT’,0);
	define(‘ADODB_FETCH_NUM’,1);
	define(‘ADODB_FETCH_ASSOC’,2);
	define(‘ADODB_FETCH_BOTH’,3);

	$sql = "SELECT c_em_id,c_em_code,c_em_name FROM t_employee";
	$conn->Execute("SET NAMES 'utf8'"); 
	$conn->SetFetchMode(ADODB_FETCH_ASSOC); // Return associative array
	$rs = &$conn->Execute($sql);
	print_r($rs->Fields);

	$num_columns = $rs->Fields->Count();
	
	$rowcount = 0;
	$temp=array();
	while (!$rs->EOF) 
		{
			for ($i=0; $i < $num_columns; $i++) 
			{
				$temp[$rowcount][$i] = $rs->Fields[$i]->value;
			}   
			$rowcount ++;
			$rs->MoveNext();  //  Moves to the next row
		} */
	/* if (!$rs) 
	{
		echo "test";
		exit;
		while (!$rs->EOF) 
		{
			for ($i=0; $i < $num_columns; $i++) 
			{
				$temp[$rowcount][$i] = $rs->Fields[$i]->value;
			}   
			$rowcount ++;
			$rs->MoveNext();  //  Moves to the next row
		}
	} */
	/* print_r($temp);
	exit; */



$hr_attend = new hr_attend();
if (!$hr_attend->init($mysql,"log")) { print "error!! cannot initialize database for hr_attend class"; exit; }
#
#
#
$PHP_SELF = $_SERVER['PHP_SELF'];
$perm = $GLOBALS['power'];
$op = array();
#
#
#
$AUTH = '107';
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
//$op['test'] = "test_morial";
if($_GET['PHP_line'] =='')
{
	$line = "1";
}
else
{
	$line = $_GET['PHP_line'];
}
if($_GET['PHP_start_date'] =='')
{
	//$date_start = date ("Y-m-d");
	$date_start = '2015-04-14';
}
else
{
	$date_start = $_GET['PHP_start_date'];
}
if($_GET['PHP_end_date'] =='')
{
	//$date_end = date ("Y-m-d");
	$date_end = '2015-04-14';
}
else
{
	$date_end = $_GET['PHP_end_date'];
}
//echo $_GET['PHP_line']."<br>";
//echo $_GET['PHP_start_date']."<br>";
//echo $_GET['PHP_end_date']."<br>"; 
/******下拉選單區***********/
$t_sql = "select c_co_code from t_code where c_co_in='033'"; //下拉選單sectioncode
$sectioncode = array();
$sectioncode = $hr_attend->get_line_utf8($t_sql);
$t_sql = "select c_co_name from t_code where c_co_in='033'"; //下拉選單組別
$linename = array();
$linename = $hr_attend->get_line_utf8($t_sql);
$op['line_select'] =$arry2->select($linename,$line,"PHP_line","select","",$sectioncode);
/******下拉選單區***********/
/*******員工、考勤基本資料*******/
$t_sql = "select c_em_id,c_em_code,c_em_name from t_employee where c_em_append3='".$_GET['PHP_line']."' and c_em_status=0";
//echo $t_sql;
$emp = array();
$emp = $hr_attend->get_2way_utf8($t_sql);

$t_sql = "select c_as_emp,c_as_date,c_as_time,c_as_place 
			from t_attend_souce 
			where c_as_emp in (";
foreach($emp as $emp_key => $emp_val)
{
	if($emp_key == (sizeof($emp)-1))
	{
		$t_sql .= $emp_val[0];
	}
	else
	{
		$t_sql .= $emp_val[0].",";
	}
}

$t_sql .= ") and c_as_date between '";
$t_sql .= $_GET['PHP_start_date'];
$t_sql .="' and '";
$t_sql .=$_GET['PHP_end_date'];
$t_sql .= "' order by c_as_emp,c_as_date,c_as_time";   
//echo $t_sql ;
$attend = array();
$attend = $hr_attend->get_hrdata($t_sql);
print_r($emp);

/* $attend = array();
$attend = $hr_attend->get_2way_utf8($t_sql); */
/*******員工、考勤基本資料*******/
/************組合新的員工考勤資料，以組別為主********************/
$new_emp_attend = array();
$temp_emp='';
foreach($emp as $emp_key => $emp_val)
{
	foreach($attend as $attend_key => $attend_val)
	{
		if($temp_emp != $emp_val[0])
		{
			//不同員工
			if($emp_key !=0 )
			{
				//非第一筆資料
			}
			$temp_emp=$emp_val[0];
			
		}
		else
		{
			//同員工
		}
		//$new_emp_attend[]
	}
}
/************組合新的員工考勤資料，以組別為主********************/











page_display($op,$AUTH,'hr_attend.html');
break;



case "attend_detail":
//print_r($_GET);
$attend_detail=array();	
$thedate=$_GET['PHP_date'];
$empid=$_GET['PHP_empid'];
$t_sql = "  USE HR;
				select 
				emp.c_em_code,
				attend.c_as_date,
				attend.c_as_time,
				attend.c_as_place 
				from t_employee emp
				left join t_attend_souce attend on attend.c_as_emp = emp.c_em_id
				where attend.c_as_date = '";
	$t_sql .= $thedate;
	$t_sql .= "' and emp.c_em_code = '";
	$t_sql .= $empid;
	$t_sql .= "' order by attend.c_as_date,attend.c_as_time,attend.c_as_work";
//echo $t_sql;

$attend_detail = $hr_attend->get_hrdata($t_sql);
echo "<html>";
echo "<head>";
echo "<link rel=stylesheet type='text/css' href='./bom.css'>
		<style>
		table, th, td {
		border: 1px solid black;
		}
		</style>
";
echo "</head>";
echo "<body>";
echo "<table>";
echo "<tr>";
echo "<td align='center'>Emp ID</td>";
echo "<td align='center'>Date</td>";
echo "<td align='center'>Attendance</td>";
echo "<td align='center'>Reader ID</td>";
echo "</tr>";
foreach($attend_detail as $key_attend => $val_attend)
{
	
	echo "<tr>";
	if($key_attend == 0)
	{
		echo "<td rowspan='".sizeof($attend_detail)."'  align='center'>".$val_attend['c_em_code']."</td>";
		echo "<td rowspan='".sizeof($attend_detail)."'  align='center'>".$val_attend['c_as_date']."</td>";
	}
	echo "<td>".$val_attend['c_as_time']."</td>";
	echo "<td  align='center'>".$val_attend['c_as_place']."</td>";
	echo "</tr>";
	
	
}
echo "</table>";
echo "</body>";
echo "</html>";
//print_r($attend_detail);

break;
#
#
#
#
#
#
} # CASE END
?>