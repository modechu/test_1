<?
error_reporting(1);
// phpinfo();

$server = '192.168.2.3';
$database = 'NewWIPOne';
$user = 'mode';
$password = '6699';
// $conn = odbc_connect("Driver={SQL Server Native Client 10.0};Server=$server;Database=$database;", $user, $password);
$connection_string = 'DRIVER={SQL Server};SERVER=192.168.2.3;DATABASE=NewWIPOne'; 
$conn = odbc_connect($connection_string, 'mode', '6699');
if($conn)
{
	$sql = "SELECT * FROM dbo.Users WHERE UserId = 'mode' ;";
	$result = odbc_exec($conn, $sql);
	if($result)
	{
        while($row = odbc_fetch_array($result))
        {
          print_r($row);
        }
	}
	else
	{
		echo '執行SQL失敗';
	}	
}
else
{
	echo '連線失敗';
}


exit;
# 資料庫中文亂碼轉檔用
echo '<meta http-equiv="Content-Type" content="text/html; charset=utf8">';
$outputfile= "OUTPUT.sql";
$DB_HOST="localhost";
$DB_USER="root";
$DB_PASS="";
$DB_DBNAME="scm_maintain";
// $DB_TABLE="bom_acc";
$DB_TABLE="bom_lots";
$fp=fopen($outputfile, "w+");

if(!$fp){  
echo "Can not open file for writing.\n";  
exit;
}

$dbcon=mysql_connect($DB_HOST, $DB_USER, $DB_PASS);



mysql_select_db($DB_DBNAME, $dbcon);
$str="SHOW TABLES FROM $DB_DBNAME";
$str="select * from $DB_TABLE where wi_id = '1218'";
mysql_query("SET NAMES latin1", $dbcon);
$rs=mysql_query($str, $dbcon);

while($row=mysql_fetch_row($rs)){  
	$i_sqlstr="INSERT INTO $DB_TABLE VALUES(";
	foreach($row as $rid=>$rval){   
		$row2[$rid]=$rval;#iconv("BIG5", "UTF-8", $rval);    
		if(strval($rid)=="0")      
			$i_sqlstr.="'".addslashes($row[$rid])."'";    
		else      
			$i_sqlstr.=", '".addslashes($row[$rid])."'";  
	} 
	$i_sqlstr.=");\r\n";

	$i_sqlstr = likepre($i_sqlstr);
	echo $i_sqlstr.'<br>';
}

// $NAMESTR="SET NAMES utf8;\r\n\r\n";

// fwrite($fp, $NAMESTR, strlen($NAMESTR));
// while($row=mysql_fetch_row($rs)){  
	// $i_sqlstr="INSERT INTO $DB_TABLE VALUES(";
	
	// $sqlstr="select * from $row[0]";
	// $rs2=mysql_query($sqlstr, $dbcon);
	// while($row2=mysql_fetch_row($rs2)){  
		// $i_sqlstr="INSERT INTO $row[0] VALUES(";
	
		// foreach($row2 as $rid=>$rval){   
			// $row2[$rid]=$rval;#iconv("BIG5", "UTF-8", $rval);    
			// if(strval($rid)=="0")      
				// $i_sqlstr.="'".addslashes($row2[$rid])."'";    
			// else      
				// $i_sqlstr.=", '".addslashes($row2[$rid])."'";  
		// }  

		// $i_sqlstr.=");\r\n";

		// $i_sqlstr = likepre($i_sqlstr);
		// echo $i_sqlstr.'<br>';
		
		// fwrite($fp, $i_sqlstr, strlen($i_sqlstr));
	// }
// }

mysql_free_result($rs);
mysql_close($dbcon);
// fclose($fp);
exit;

function likepre ($string) {
// $string = str_replace('"','&quot;',$string);
// $string = str_replace("'",'&#39;',$string);
// $string = str_replace("<","&lt;",$string);
// $string = str_replace(">","&gt;",$string);
// $string = str_replace("\t","　",$string);                //換為全角空格
// $string = str_replace("  ","　",$string);                //兩個半角空格換為全角空格
$string = htmlspecialchars($string);
return $string;
}
?>