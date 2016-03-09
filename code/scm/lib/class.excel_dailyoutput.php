<?php 

class  Excel_dailyoutput {
	
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
function get_wip1data($t_sql)
{

	//echo $t_sql."<br>";
	$connection_string = 'DRIVER={SQL Server};SERVER='.$GLOBALS['RFID_SERVER'].';DATABASE='.$GLOBALS['WIPONE_DB'].';CharacterSet => UTF-8'; 
	/* echo $connection_string."<br>";
	echo $GLOBALS['RFID_USER']."<br>";
	echo $GLOBALS['RFID_PASSWD']."<br>"; */
	$conn = odbc_connect($connection_string, $GLOBALS['RFID_USER'], $GLOBALS['RFID_PASSWD']);
	if (!$conn)
	{
		printf("<Br> %s disconnect<br>", $GLOBALS['RFID_SERVER']);
	}
	else
	{
		$sql = $t_sql;
		//echo $sql."<br>";
		$result = odbc_exec($conn, $sql);
		//echo  $result;
		$data = array();
		if($result)
		{
			while($row = odbc_fetch_array($result))
			{
			  //print_r($row);
			  $data[] = $row;
			  //print_r($_SESSION);
			}
		}
		else
		{
			if(empty($result))
			{
				//echo 'SQL No Data';
				echo "<script language=javascript> window.alert('SQL No Data')</script>";
			}
			else
			{
				//echo 'Excute SQL Failure';
				echo "<script language=javascript> window.alert('Excute SQL Failure')</script>";
			}
		}	
		if(!empty($data))
		return $data;
	}
}
function get_hrdata($t_sql)
{

	//echo $t_sql."<br>";
	$connection_string = 'DRIVER={SQL Server};SERVER='.$GLOBALS['RFID_SERVER'].';DATABASE='.$GLOBALS['WIPONE_HR_DB'].';CharacterSet => UTF-8'; 
	/* echo $connection_string."<br>";
	echo $GLOBALS['RFID_USER']."<br>";
	echo $GLOBALS['RFID_PASSWD']."<br>"; */
	$conn = odbc_connect($connection_string, $GLOBALS['RFID_USER'], $GLOBALS['RFID_PASSWD']);
	if (!$conn)
	{
		printf("<Br> %s disconnect<br>", $GLOBALS['RFID_SERVER']);
	}
	else
	{
		$sql = $t_sql;
		//echo $sql."<br>";
		$result = odbc_exec($conn, $sql);
		//echo  $result;
		$data = array();
		if($result)
		{
			while($row = odbc_fetch_array($result))
			{
			  //print_r($row);
			  $data[] = $row;
			  //print_r($_SESSION);
			}
		}
		else
		{
			if(empty($result))
			{
				//echo 'SQL No Data';
				echo "<script language=javascript> window.alert('SQL No Data')</script>";
			}
			else
			{
				//echo 'Excute SQL Failure';
				echo "<script language=javascript> window.alert('Excute SQL Failure')</script>";
			}
		}	
		if(!empty($data))
		return $data;
	}
}


 # FUN END
#
#
#
#
#
#

} # CLASS END
?>