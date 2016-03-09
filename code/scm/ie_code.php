<?php

define("DB_HOST","192.168.1.13");
define("DB_LOGIN","root");
define("DB_PASSWORD","660909");
define("DB_NAME","scm_po");

# 連上資料庫
###############################################################################################################
function Connect_db(){
	if(empty($_SESSION["link_db"])){
		$_SESSION["link_db"] = @mysql_connect(DB_HOST, DB_LOGIN, DB_PASSWORD);
		if(!$_SESSION["link_db"]){
			echo ("FATAL:Couldn't connect to db.<br>");
			exit;
		}
	}

	$_SESSION["Select_DB"] = @mysql_select_db(DB_NAME, $_SESSION["link_db"]);
	if(!$_SESSION["Select_DB"]){
		echo ("FATAL:Couldn't connect to db.<br>");
		exit;
	}
    return $_SESSION["link_db"];
}
###############################################################################################################
$DB = Connect_db();

mysql_query("SET NAMES utf8"); 

$tag = '';

$tDaTa = array();
$DaTa = '名稱Cuïm chi tieát名稱Cuïm chi tieát工段名稱Teân coâng ñoaïn Tieáng Hoa翻譯工時單位';
# 取出財產編號對應的工號
$target = file('ie_code.txt');

$aNAME = $aVNAME = $aINAME = $aIVNAME = $aNOTE = $aNAMEk = $aINAMEk = array();
foreach ($target as $Tgt){
	list($NAME,$VNAME,$INAME,$IVNAME,$NOTE,$VNOTE,$TIME,$UNIT) = split($tag,$Tgt);

    $NAME = empty($NAME)? $INAME : $NAME ;
    $VNAME = empty($VNAME)? $IVNAME : $VNAME ;
    $INAME = empty($INAME)? $NAME : $INAME ;
    $IVNAME = empty($IVNAME)? $VNAME : $IVNAME ;
    
    $aNAME[$NAME] = $NAME;
    $aVNAME[$NAME] = $VNAME;
    $aINAME[$INAME] = $INAME;
    $aIVNAME[$INAME] = $IVNAME;
}

$inum = 1;
$RaNAME = $aNAME;
$aNAME = array();
foreach ($RaNAME as $flag){
    $aNAME[$inum] = $flag;
    $aNAMEk[$flag] = $inum;
    
    $q_str = "INSERT INTO `ie_major_class` SET 
    `sort` = '0' , 
    `zh_tw` = '".$flag."' , 
    `vi` = '".$aVNAME[$flag]."' , 
    `en` = '".$flag."' , 
    `del_mk` = 'n' 
    ";
    // echo $q_str.'<br>';
    // $res = mysql_query($q_str);
    
    $inum++;
    
}

$inum = 1;
$RaINAME = $aINAME;
$aINAME = array();
foreach ($RaINAME as $flag){
    $aINAME[$inum] = $flag;
    $aINAMEk[$flag] = $inum;
    
    $q_str = "INSERT INTO `ie_middle_class` SET 
    `sort` = '0' , 
    `zh_tw` = '".$flag."' , 
    `vi` = '".$aIVNAME[$flag]."' , 
    `en` = '".$flag."' , 
    `del_mk` = 'n' 
    ";
    // echo $q_str.'<br>';
    // $res = mysql_query($q_str);
    
    $inum++;
}

$inum1 = $inum2 = 0;
$inum = 1;
foreach ($target as $Tgt){
	list($NAME,$VNAME,$INAME,$IVNAME,$NOTE,$VNOTE,$TIME,$UNIT) = split($tag,$Tgt);

    $NAME = empty($NAME)? $INAME : $NAME ;
    $VNAME = empty($VNAME)? $IVNAME : $VNAME ;
    $INAME = empty($INAME)? $NAME : $INAME ;
    $IVNAME = empty($IVNAME)? $VNAME : $IVNAME ;

    $aNOTE[] = array( 'N1' => $aNAMEk[$NAME] , 'N2' => $aINAMEk[$INAME] , 'N3' => $inum , 'in_name' => $NOTE , 'in_vname' => $VNOTE , 'ie' => $TIME , 'unit' => $UNIT );
	// echo $NAME.'~'.$VNAME.'~'.$INAME.'~'.$IVNAME.'~'.$NOTE.'~'.$VNOTE.'~'.$TIME.'~'.$UNIT.'<br>';
    
    $unit = ( $UNIT == '件') ? '1' : '2' ;
    
    
    $q_str = "INSERT INTO `ie_small_class` SET 
    `sort` = '0' , 
    `major_id` = '".$aNAMEk[$NAME]."' , 
    `middle_id` = '".$aINAMEk[$INAME]."' , 
    `zh_tw` = '".$NOTE."' , 
    `vi` = '".$VNOTE."' , 
    `en` = '".$NOTE."' , 
    `sec` = '".$TIME."' , 
    `doub` = '' , 
    `unit_id` = '".$unit."' , 
    `del_mk` = 'n' 
    ";
    echo $q_str.'<br>';
    // $res = mysql_query($q_str);    
    
    $inum++; 
}
// `sort` INT( 4 ) NOT NULL DEFAULT  '0',
// `major_id` INT( 4 ) NOT NULL ,
// `middle_id` INT( 4 ) NOT NULL ,
// `zh_tw` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
// `vi` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
// `en` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
// `sec` INT( 4 ) NOT NULL DEFAULT  '0',
// `doub` INT( 4 ) NOT NULL DEFAULT  '0',


// print_r($aNAME);
// print_r($aVNAME);
// print_r($aINAME);
// print_r($aIVNAME);
// print_r($aNOTE);


// IPO(input process output)
// 例如我們現在正在做的IE公段秒數：
// I：公段名稱、技能等級、時間...
// P：人機配置、工時分析...
// O：人力、平均工時...
// 有了IPO最後你也必須要清楚知道
// 產出了這資料是要用到哪
// 要回饋到系統哪個地方或有哪邊的實際運用
// 總不能說閒來沒事做個這種紀錄


// ie_major_class
// ie_middle_class
// ie_small_class

// Major
// Middle
// Small


// id,sort,zh_tw,vi,en,del_mk


// CREATE TABLE  `scm_po`.`ie_unit` (
// `id` INT( 4 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
// `sort` INT( 4 ) NOT NULL DEFAULT  '0',
// `zh_tw` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
// `vi` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
// `en` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
// `del_mk` ENUM(  'y',  'n' ) NOT NULL DEFAULT  'n'
// ) ENGINE = MYISAM

// CREATE TABLE  `scm_po`.`ie_small_class` (
// `id` INT( 4 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
// `sort` INT( 4 ) NOT NULL DEFAULT  '0',
// `major_id` INT( 4 ) NOT NULL ,
// `middle_id` INT( 4 ) NOT NULL ,
// `zh_tw` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
// `vi` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
// `en` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
// `sec` INT( 4 ) NOT NULL DEFAULT  '0',
// `doub` INT( 4 ) NOT NULL DEFAULT  '0',
// `del_mk` ENUM(  'y',  'n' ) NOT NULL DEFAULT  'n'
// ) ENGINE = MYISAM
?>