<html>
<head>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
</head>
<body>
<?php
session_start();
require_once "config.php";
require_once "config.admin.php";
require_once "init.object.php";
include($config['root_dir'].'/ado/adodb.inc.php'); 
echo $config['root_dir'];

$dbdriver='ado_mssql';
$server='192.168.1.30';
$user='lydbbackup';
$password='carnival@@27113171';
//$DATABASE='HR';
//$database='sqloledb';
//$dbdriver='ado://sa:cvttdev@172.16.22.40/sqloledb?charpage=65001';
$myDSN="PROVIDER=MSDASQL;DRIVER={SQL Server};SERVER={192.168.1.30};DATABASE=HR;UID=lydbbackup;PWD=carnival@@27113171;"; 

        

         $db = ADONewConnection($dbdriver); # eg 'mysql' or 'postgres'
         $db->debug = true;	
	 $db->charPage =65001;
         //$db->Connect($server, $user, $password, $database);
         $db->Connect($myDSN);
	 
	 //error:mssql server not support codes below
	 //$db->Execute("set names 'utf8'");
	 echo "before query"; 
         $rs = $db->Execute('select * from accounts');
         print "<pre>";
         print_r($rs->GetRows());
         print "</pre>";
?>
</body>
</html>