<?php 

class  hr_attend{
	
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
function get_line_utf8($t_sql,$column_name)
{
	$conn = new COM("ADODB.Connection",NULL, CP_UTF8) or die("Cannot start ADO");
	$conn->Open("Provider=SQLOLEDB; Data Source=192.168.1.30;Initial Catalog=HR; User ID=lydbbackup; Password=carnival@@27113171");
	define(‘ADODB_FETCH_DEFAULT’,0);
	define(‘ADODB_FETCH_NUM’,1);
	define(‘ADODB_FETCH_ASSOC’,2);
	define(‘ADODB_FETCH_BOTH’,3);

	//$sql = "SELECT c_em_id,c_em_code,c_em_name FROM t_employee";
	
	$conn->Execute("SET NAMES 'utf8'"); 
	$conn->SetFetchMode(ADODB_FETCH_ASSOC); // Return associative array
	$rs = &$conn->Execute($t_sql);

	$num_columns = $rs->Fields->Count();
	$rowcount = 0;
	$temp=array();
	if (!$rs) 
	{
	  print $conn->ErrorMsg(); // Displays the error message if no results could be returned
	}
	else {
		
		while (!$rs->EOF) 
		{
			$temp[$rowcount] = $rs->Fields[0]->value; 
			$rowcount ++;
			$rs->MoveNext();  //  Moves to the next row
		}
			
	} // end else 
	
return $temp;	
	
}
function get_2way_utf8($t_sql,$column_name)
{
	$conn = new COM("ADODB.Connection",NULL, CP_UTF8) or die("Cannot start ADO");
	$conn->Open("Provider=SQLOLEDB; Data Source=192.168.1.30;Initial Catalog=HR; User ID=lydbbackup; Password=carnival@@27113171");
	define(‘ADODB_FETCH_DEFAULT’,0);
	define(‘ADODB_FETCH_NUM’,1);
	define(‘ADODB_FETCH_ASSOC’,2);
	define(‘ADODB_FETCH_BOTH’,3);

	//$sql = "SELECT c_em_id,c_em_code,c_em_name FROM t_employee";
	
	$conn->Execute("SET NAMES 'utf8'"); 
	$conn->SetFetchMode(ADODB_FETCH_ASSOC); // Return associative array
	$rs = &$conn->Execute($t_sql);
	/* echo $t_sql."<br>";
	exit; */
	$num_columns = $rs->Fields->Count();
	$rowcount = 0;
	$temp=array();
	if (!$rs) 
	{
	  print $conn->ErrorMsg(); // Displays the error message if no results could be returned
	}
	else {
		
		if($column_name)
		{
			
			//需要設定欄位名稱
			while (!$rs->EOF) 
			{
				for ($i=0; $i < $num_columns; $i++) 
				{
					$temp[$rowcount][$i] = $rs->Fields[$i]->value;
				}   
				$rowcount ++;
				$rs->MoveNext();  //  Moves to the next row
			}
			
		}
		else
		{
			//無須設定欄位名稱
			
			while (!$rs->EOF) 
			{
				for ($i=0; $i < $num_columns; $i++) 
				{
					$temp[$rowcount][$i] = $rs->Fields[$i]->value;
				}   
				$rowcount ++;
				$rs->MoveNext();  //  Moves to the next row
			}
			
		} 
		// end while
	} // end else 
	/* echo "finish";
	exit; */
	/* print_r($temp);
	exit; */
return $temp;	
	
}
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
	$connection_string = 'DRIVER={SQL Server};SERVER='.$GLOBALS['RFID_SERVER'].';DATABASE='.$GLOBALS['WIPONE_HR_DB'].';CharacterSet => UTF-8;'; 
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
		odbc_exec("SET NAMES 'utf8'");
		$result = odbc_exec($conn, $sql);
		echo  $result;
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
function attend_calc($month,$onduty_type,$attend_detail)
{
	
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