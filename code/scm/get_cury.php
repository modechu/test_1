<?php
session_start();
//mb_internal_encoding("big5");

echo '<meta http-equiv="Content-Type" content="text/html; charset=big5">';

define("DB_HOST","localhost");
define("DB_LOGIN","root");
define("DB_PASSWORD","");
define("DB_NAME","scm_maintain");

echo '~無使用中~';
exit;
//active_range();//當旬
history();//歷史記錄

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	getDaysInMonth($month,$year)  輸入年及月 回覆該月的日數
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function getDaysInMonth($month,$year)	{
	$daysInMonth = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
	 
	if ($month < 1 || $month > 12)     return 0;
	$d = $daysInMonth[$month - 1];
	if ($month == 2){
		if ($year%4 == 0){
			if ($year%100 == 0){
				if ($year%400 == 0){
					$d = 29; 
				}
			} else {
			 $d = 29;
			}
		}
	}
	return $d;
}

#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
#	increceDaysInDate($date,$nDays)  輸入日期 及要增加的日數 回覆增後的日期
#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function increceDaysInDate($date,$nDays) 	{ 
	if( !isset( $nDays ) ) { 	$nDays = 1;     } 
	$aVet = Explode( "-",$date ); 
	if(isset($aVet[1]))
	{
		return date( "Y-m-d",mktime(0,0,0,$aVet[1],$aVet[2]+$nDays,$aVet[0])); 
	}
}


#+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function active_range(){
	$filename = "../USD_NTD_".date("n").".txt";

	if (file_exists($filename)){
		$arrText = file($filename);
		//for ($i=0;$i<sizeof($arrText);$i++){$cury["cy"][$i] = explode(chr(9),$arrText[$i]);} //資料全部丟入$cury["cy"]
		echo "檔案 $filename 已存在，不再寫入資料庫";
	} else {

		//Connect_db();
		
		$arrText = file("http://web.customs.gov.tw/currency/currency/current.txt");
		$fp = fopen($filename, 'w');
		foreach ($arrText as $line_num => $line) {
			fwrite($fp, $line);
		}
		fclose($fp);
		for ($i=0;$i<sizeof($arrText);$i++){$cury["cy"][$i] = explode(chr(9),$arrText[$i]);}
		$mm_day = getDaysInMonth(date("n"),date("y"));
		$day_range1=array(0,10); //用來判斷是第幾旬
		$day_range2=array(10,20);
		$day_range3=array(20,$mm_day);
		$range=array(1=>$day_range1,2=>$day_range2,3=>$day_range3);
		
		add($cury,$range[$cury['cy'][1][3]]);
	}
}


function history(){
Connect_db();

$day_range1=array(0,10); //用來判斷是第幾旬
$day_range2=array(10,20);
$set_year = 94;
$file_name = './x86z.txt';
$arrText = file($file_name);
for ($i=0; $i<count($arrText); $i++) {

	// if( substr($arrText[$i],0,3) == 'USD' ) {
		// #echo substr($arrText[$i],0,3).'：'.substr($arrText[$i],3,3).' 年 / '.substr($arrText[$i],6,2).' 月，第 '.substr($arrText[$i],8,1).' 旬，賣出：' .substr($arrText[$i],9,3).'.'.substr($arrText[$i],12,5).' 買進：'.substr($arrText[$i],17,3).'.'.substr($arrText[$i],20,5).'<br>';
		// if( substr($arrText[$i],3,3) >= $set_year ) {
			// $y=(int)substr($arrText[$i],3,3)+1911;
			// $m=substr($arrText[$i],6,2);
			// $sell=substr($arrText[$i],9,3).".".substr($arrText[$i],12,5);
			// $buy=substr($arrText[$i],17,3).".".substr($arrText[$i],20,5);			
			// $mm_day = getDaysInMonth(substr($arrText[$i],6,2),(substr($arrText[$i],3,3)+1911));
			// $day_range3=array(20,$mm_day);
			// $range=array(1=>$day_range1,2=>$day_range2,3=>$day_range3);
			// update_in($y,$m,$sell,$buy,$range[substr($arrText[$i],8,1)],substr($arrText[$i],0,3));
			// update_out($y,$m,$sell,$buy,$range[substr($arrText[$i],8,1)],substr($arrText[$i],0,3));
		// }
	// }

	// if( substr($arrText[$i],0,3) == 'HKD' ) {
		// if( substr($arrText[$i],3,3) >= $set_year ) {
			// $y=(int)substr($arrText[$i],3,3)+1911;
			// $m=substr($arrText[$i],6,2);
			// $sell=substr($arrText[$i],9,3).".".substr($arrText[$i],12,5);
			// $buy=substr($arrText[$i],17,3).".".substr($arrText[$i],20,5);			
			// $mm_day = getDaysInMonth(substr($arrText[$i],6,2),(substr($arrText[$i],3,3)+1911));
			// $day_range3=array(20,$mm_day);
			// $range=array(1=>$day_range1,2=>$day_range2,3=>$day_range3);
			// update_in($y,$m,$sell,$buy,$range[substr($arrText[$i],8,1)],substr($arrText[$i],0,3));
			// update_out($y,$m,$sell,$buy,$range[substr($arrText[$i],8,1)],substr($arrText[$i],0,3));
		// }
	// }

	// if( substr($arrText[$i],0,3) == 'GBP' ) {
		// if( substr($arrText[$i],3,3) >= $set_year ) {
			// $y=(int)substr($arrText[$i],3,3)+1911;
			// $m=substr($arrText[$i],6,2);
			// $sell=substr($arrText[$i],9,3).".".substr($arrText[$i],12,5);
			// $buy=substr($arrText[$i],17,3).".".substr($arrText[$i],20,5);			
			// $mm_day = getDaysInMonth(substr($arrText[$i],6,2),(substr($arrText[$i],3,3)+1911));
			// $day_range3=array(20,$mm_day);
			// $range=array(1=>$day_range1,2=>$day_range2,3=>$day_range3);
			// update_in($y,$m,$sell,$buy,$range[substr($arrText[$i],8,1)],substr($arrText[$i],0,3));
			// update_out($y,$m,$sell,$buy,$range[substr($arrText[$i],8,1)],substr($arrText[$i],0,3));
		// }
	// }
	// if( substr($arrText[$i],0,3) == 'JPY' ) {
		// if( substr($arrText[$i],3,3) >= $set_year ) {
			// $y=(int)substr($arrText[$i],3,3)+1911;
			// $m=substr($arrText[$i],6,2);
			// $sell=substr($arrText[$i],9,3).".".substr($arrText[$i],12,5);
			// $buy=substr($arrText[$i],17,3).".".substr($arrText[$i],20,5);			
			// $mm_day = getDaysInMonth(substr($arrText[$i],6,2),(substr($arrText[$i],3,3)+1911));
			// $day_range3=array(20,$mm_day);
			// $range=array(1=>$day_range1,2=>$day_range2,3=>$day_range3);
			// update_in($y,$m,$sell,$buy,$range[substr($arrText[$i],8,1)],substr($arrText[$i],0,3));
			// update_out($y,$m,$sell,$buy,$range[substr($arrText[$i],8,1)],substr($arrText[$i],0,3));
		// }
	// }
	
	if( substr($arrText[$i],0,3) == 'EUR' ){
		if( substr($arrText[$i],3,3) >= $set_year ){
			$y=(int)substr($arrText[$i],3,3)+1911;
			$m=substr($arrText[$i],6,2);
			$sell=substr($arrText[$i],9,3).".".substr($arrText[$i],12,5);
			$buy=substr($arrText[$i],17,3).".".substr($arrText[$i],20,5);			
			$mm_day = getDaysInMonth(substr($arrText[$i],6,2),(substr($arrText[$i],3,3)+1911));
			$day_range3=array(20,$mm_day);
			$range=array(1=>$day_range1,2=>$day_range2,3=>$day_range3);
			update_in($y,$m,$sell,$buy,$range[substr($arrText[$i],8,1)],substr($arrText[$i],0,3));
			update_out($y,$m,$sell,$buy,$range[substr($arrText[$i],8,1)],substr($arrText[$i],0,3));
		}
	}
	
	// if( substr($arrText[$i],0,3) == 'RMB'  ) {
		// if( substr($arrText[$i],3,3) >= $set_year ) {
			// $y=(int)substr($arrText[$i],3,3)+1911;
			// $m=substr($arrText[$i],6,2);
			// $sell=substr($arrText[$i],9,3).".".substr($arrText[$i],12,5);
			// $buy=substr($arrText[$i],17,3).".".substr($arrText[$i],20,5);			
			// $mm_day = getDaysInMonth(substr($arrText[$i],6,2),(substr($arrText[$i],3,3)+1911));
			// $day_range3=array(20,$mm_day);
			// $range=array(1=>$day_range1,2=>$day_range2,3=>$day_range3);
			// update_in($y,$m,$sell,$buy,$range[substr($arrText[$i],8,1)],substr($arrText[$i],0,3));
			// update_out($y,$m,$sell,$buy,$range[substr($arrText[$i],8,1)],substr($arrText[$i],0,3));
		// }
	// }
	

}

}





function add_in($y,$m,$sell,$buy,$day_range){
	for($i=(int)$day_range[0]; $i<(int)$day_range[1]; $i++){
		$q_str = "INSERT INTO `rate_in` ( `rate_date` , `USD` , `HKD` , `GBP`, `JPY` , `EUR` , `RMB` ) 
						VALUES ('".increceDaysInDate(date("$y-$m-1"),$i)."','".
											$buy."','0','0','0','0','0')";
		mysql_query($q_str);
	}	
}

function add_out($y,$m,$sell,$buy,$day_range){
	for($i=(int)$day_range[0]; $i<(int)$day_range[1]; $i++){
		$q_str = "INSERT INTO `rate_out` ( `rate_date` , `USD` , `HKD` , `GBP`, `JPY` , `EUR` , `RMB` ) 
						VALUES ('".increceDaysInDate(date("$y-$m-1"),$i)."','".
											$sell."','0','0','0','0','0')";
		mysql_query($q_str);
	}	
}


function update_in($y,$m,$sell,$buy,$day_range,$table){
	for($i=(int)$day_range[0]; $i<(int)$day_range[1]; $i++){
		$q_str = "UPDATE `rate_in` SET `".$table."` = '".$buy."' WHERE `rate_date` = '".increceDaysInDate(date("$y-$m-1"),$i)."'";
		// echo $q_str.'<br>';
		mysql_query($q_str);
	}	
}

function update_out($y,$m,$sell,$buy,$day_range,$table){
	for($i=(int)$day_range[0]; $i<(int)$day_range[1]; $i++){
		$q_str = "UPDATE `rate_out` SET `".$table."` = '".$sell."' WHERE `rate_date` = '".increceDaysInDate(date("$y-$m-1"),$i)."'";
		mysql_query($q_str);
	}	
}



# 連上資料庫
###############################################################################################################
function Connect_db(){
	if(empty($_SESSION["link_db"])){
		$_SESSION["link_db"] = mysql_connect(DB_HOST, DB_LOGIN, DB_PASSWORD);
		if(!$_SESSION["link_db"]){
			echo ("FATAL:Couldn't connect to db.<br>");
			exit;
		}
	}
	
	$_SESSION["Select_DB"] = mysql_select_db(DB_NAME, $_SESSION["link_db"]);
	if(!$_SESSION["Select_DB"]){
		echo ("FATAL:Couldn't connect to db.<br>");
		exit;
	}
}
###############################################################################################################


?>