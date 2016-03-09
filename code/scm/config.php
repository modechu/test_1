<?
error_reporting(1);

$PHP_SELF = $_SERVER['PHP_SELF'];   //移入主程式內

$config = array();

$config['root_dir']    = dirname(__FILE__); 
$config['root_url']    = 'http://scm-b.carnival.com.tw';
$config['admin_email'] = 'mode@tp.carnival.com.tw';
$config['data_dir']    = $config['root_dir']."/data";

############## global varies ####################
$img_url		= $config['root_url']."/images/";
$pic_url		= $config['root_url']."/pics/";
############## db/mysql #######################
$MYSQL_SERVER = "localhost";
$MYSQL_USER = "JackYang";
$MYSQL_PASSWD = "yankee610";
$MYSQL_DB = "scm_test";
// $MYSQL_DB = "mode";
// $MYSQL_DB = "scm_tmp";
########  connect DB ###########

############## global varies ##################
$UTF_SERVER = "192.168.1.13";
$UTF_USER = "mode";
$UTF_PASSWD = "6699";
$UTF_DB = "scm_po_test";
########  connect DB ###########

############## global varies ##################
$RFID_SERVER = "192.168.1.30";
$RFID_USER = "lydbbackup";
$RFID_PASSWD = "carnival@@27113171";
$WIPONE_DB = "NewWIPOne";
$WIPONE_HR_DB = "HR"; 
########  connect DB ###########

include_once($config['root_dir']."/lib/functions.php");
include_once($config['root_dir']."/smarty/Smarty.class.php");
include_once($config['root_dir']."/lib/class.msg_handle.php");
include_once($config['root_dir']."/lib/class.mysql.php");
include_once($config['root_dir']."/lib/class.layout.php");
$MySQL = $mysql = getDBconnect();

if (!$mysql) {
	print " unusual error!! cannot connect database !!";
	exit;
}


########  變數設定 ###########
$FULL_TIME = 8;
$IE_TIME = 3000;
$DAY_SU = (60*60*$FULL_TIME)/$IE_TIME;

$DIST_TO['HJ'] = array(
	'Name'	=>	'Wuxi Hujia Garment Co., Ltd.',
	'Addr'	=>	'No.7, Bibo Branch Rd., Mashan District, Wuxi, Jiangsu Province, China.',
	'TEL'	=>	'865 10859 96003',
	'FAX'	=>	'865 10859 95418',
	'Attn'	=>	'Shenzhou'
);
$DIST_TO['LY'] = array(
	'Name'	=>	'LI YUEN GARMENT COMPANY LTD.',
	'Addr'	=>	'Trang Bang Industrial Park, An Binh Hamlet, An Tinh Village, Trang Bang District, Tay Ninh Province Vietnam.',
	'TEL'	=>	'84 66389 6888',
	'FAX'	=>	'84 66389 6735',
	'Attn'	=>	'Henry'
);
$DIST_TO['CF'] = array(	
	'Name'	=>	'PALMPHIL GARMENTS PHILIPPINE CORP.',
	'Addr'	=>	'BINARY ST. LIGHT INDUSTRY SCIENCE PARK 1, DIEZMO CABUYAO,LAGUNA, PHILIPPINES',
	'TEL'	=>	'+63-49543-1053',
	'FAX'	=>	'+63-49543-1056',
	'Attn'	=>	'Malou'
);
$DIST_TO['CL'] = array(	
	'Name'	=>	'CARNIVAL INDUSTRIAL CORPORATION (CHONG LI)',
	'Addr'	=>	'No.335 Sec.5 Min Chu Rd., Shuang Zuo Lee, Yang Mei Chen, Tao Yuan Hsien, Taiwan, R.O.C.',
	'TEL'	=>	'(886-3)490-2511',
	'FAX'	=>	'(886-3) 490-2092/490-4776',
	'Attn'	=>	'JudyWang'
);
$DIST_TO['CA'] = array(	
	'Name'	=>	'CARNIVAL INDUSTRIAL CORPORATION',
	'Addr'	=>	'7th Floor. 25 Jen AI Road, Section4, Taipei 106, Taiwan, R.O.C.',
	'TEL'	=>	'(886-2)2711-3171',
	'FAX'	=>	'(886-2)2731-7367',
	'Attn'	=>	'Fay,Kathy'
);

$SHIP_TO['HJ'] = array(
	'Name'	=>	'Wuxi Hujia Garment Co., Ltd.',
	'Addr'	=>	'No.7, Bibo Branch Rd., Mashan District, Wuxi, Jiangsu Province, China.',
	'TEL'	=>	'865 10859 96003',
	'FAX'	=>	'865 10859 95418',
	'Attn'	=>	'Shenzhou'
);
$SHIP_TO['LY'] = array(
	'Name'	=>	'LI YUEN GARMENT COMPANY LTD.',
	'Addr'	=>	'Trang Bang Industrial Park, An Binh Hamlet, An Tinh Village, Trang Bang District, Tay Ninh Province Vietnam.',
	'TEL'	=>	'84 66389 6888',
	'FAX'	=>	'84 66389 6735',
	'Attn'	=>	'Henry'
);
$SHIP_TO['CA'] = array(	
	'Name'	=>	'CARNIVAL INDUSTRIAL CORPORATION',
	'Addr'	=>	'7th Floor. 25 Jen AI Road, Section4, Taipei 106, Taiwan, R.O.C.',
	'TEL'	=>	'(886-2)2711-3171',
	'FAX'	=>	'(886-2)2731-7367',
	'Attn'	=>	'Fay,Kathy'
);
$SHIP_TO['CL'] = array(
	'Name'	=>	'CARNIVAL INDUSTRIAL CORPORATION (CHONG LI)',
	'Addr'	=>	'No.335 Sec.5 Min Chu Rd., Shuang Zuo Lee, Yang Mei Chen, Tao Yuan Hsien, Taiwan, R.O.C.',
	'TEL'	=>	'(886-3)490-2511',
	'FAX'	=>	'(886-3) 490-2092/490-4776',
	'Attn'	=>	'JudyWang'
);
$SHIP_TO['CT'] = array(
	'Name'	=>	'DIAMOND HOSIERY & THREAD CO.',
	'Addr'	=>	'NO.8,HAN YUAN 2nd ROAD, CHUNG LI, Taiwan, R.O.C.',
	'TEL'	=>	'(886-3)452-2187 #604',
	'FAX'	=>	'(886-3)452-0314',
	'Attn'	=>	'SUH-HUEY'
);
$SHIP_TO['FW'] = array(
	'Name'	=>	'Wonderful Transportation Ltd.',
	'Addr'	=>	'Unit 105, 1/F, Hong Kong Worsted Mills Industrial Building No. 31-39 Wo tong Tsui St., Kwai Chung, N.T. H.K.',
	'TEL'	=>	'852-2755-1111',
	'FAX'	=>	'852-2755-5599',
	'Attn'	=>	'Ms. Rachel'
);
$SHIP_TO['CF'] = array(
	'Name'	=>	'PALMPHIL GARMENTS PHILIPPINE CORP.',
	'Addr'	=>	'BINARY ST. LIGHT INDUSTRY SCIENCE PARK 1, DIEZMO CABUYAO,LAGUNA, PHILIPPINES',
	'TEL'	=>	'+63-49543-1053',
	'FAX'	=>	'+63-49543-1056',
	'Attn'	=>	'Malou'
);			



$REMUNER['HJ'] = array( 
	'Name'	=>	'WUXI HUJIA GARMENT CO., LTD.',
	'sub'	=>	'無錫湖嘉服裝有限公司 (WUXI HUJIA GARMENT CO..LTD)',												
	'ship'	=>	'無錫湖嘉服裝有限公司 (WUXI HUJIA GARMENT CO..LTD)',
	'bank'	=>	'BANK OF CHINA. MASHAN. WUXI BRANCH',
	'id'	=>	'26251408093014'
);
$REMUNER['LY'] = array( 
	'Name'	=>	'LI YUEN GARMENT CO., LTD.',
	'sub'	=>	'立元服裝有限公司 (LI YUEN GARMENT CO..LTD)',												
	'ship'	=>	'立元服裝有限公司 (LI YUEN GARMENT CO..LTD)',
	'bank'	=>	'MEGA BANK Hochiminh City Branch',
	'id'	=>	'010237008089'
);

$REMUNER['CF'] = array(
	'Name'	=>	'PALMPHIL GARMENTS PHILIPPINE CORP.',
	'sub'	=>	'PALMPHIL GARMENTS PHILIPPINE CORP.',												
	'ship'	=>	'PALMPHIL GARMENTS PHILIPPINE CORP.',
	'bank'	=>	'SECURITY BANK CORPORATION',
	'id'	=>	'0224-017345-201'
);

$SHIP = array('HJ','LY','CF','CA','CL','CT','FW');
$SHIP2 = array('CA','CL','CT','FW');
$DIST = array('HJ','LY','CF','CL','CA');
$ALPHA = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
$ALPHA2 = array('A','B','C','D','E','F','G','H','J','K','L','M','N','P','Q','R','S','T','U','V','W','X','Y','Z','1','2','3','4','5','6','7','8','9');
$SALES_F_MANG = array('K0','J0','DA');
$SALES_MANG = array('K','J','T','D');
$SALES_DEPT = array('KA','J1','DA');
$MAT_SUPPORT_KEY = array(1,2);
$MAT_SUPPORT = array('FTY-sourcing','CUST-support');

$MM = array(
	'01'	=>	'January',
	'02'	=>	'February',
	'03'	=>	'March',
	'04'	=>	'April',
	'05'	=>	'May',
	'06'	=>	'June',
	'07'	=>	'July',
	'08'	=>	'August',
	'09'	=>	'September',
	'10'	=>	'October',
	'11'	=>	'November',
	'12'	=>	'December'
);

$MM2 = array(
	'01'	=>	'JAN',
	'02'	=>	'FEB',
	'03'	=>	'MAR',
	'04'	=>	'APR',
	'05'	=>	'MAY',
	'06'	=>	'JUN',
	'07'	=>	'JUL',
	'08'	=>	'AUG',
	'09'	=>	'SEP',
	'10'	=>	'OCT',
	'11'	=>	'NOV',
	'12'	=>	'DEC'
);

$FABRIC_CAT = array(
	"人纖類",
	"棉織類",
	"毛織類",
	"絲織類",
	"混紡類",
	"麻織類",
	"Fancy Yarn"
);

$OFFER_LOG = array(
	"Fabric",
	"Trim",
	"Payment",
	"Price",
	"sample",
	"order",
	"Packing",
	"others"
);

$PO_ITEM = array(
	"Document",
	"Packing",
	"Remark"
);

$FABRIC_KIND = array(
	"梭織布",
	"針織布"
);


$FABRIC_WT_UNIT = array(
	"g/yd.",
	"g/M.",
	"g/M2.",
	"oz/yd.",
	"m/m.",
);

$LOTS_PRICE_UNIT = array(
	"yd",
	"meter",
	"inch",
	"ft",
	"kg",
	"lb",
	"SF",
	"g/yd.",
	"pc",
	"set"
);

$ACC =	array(	
	"button",
	"buckle",
	"cord",
	"carton",
	"elastic",
	"eyelet",
	"extra button bag",
	"hanger",
	"hook&bar",
	"hook&eye",
	"Hangtag",		
	"label",
	"label Main",	
	"label content",	
	"label co",
	"label size",
	"label care",
	"label ID",
	"label stretch",
	"label seyle",
	"label time",
	"patch",
	"piping",
	"polybag",
	"snap",
	"SHD.-pad",
	"SLV. Head",
	"sticker",	
	"stopper",			
	"Tag",				
	"tape",
	"thread",

	"zipper",
	"Nylon zipper",
	"other",						
);

$ACC_key =	array(	
	"03"	=>	"button",
	"15"	=>	"buckle",
	"05"	=>	"cord",
	"21"	=>	"carton",
	"11"	=>	"elastic",
	"13"	=>	"eyelet",
	"33"	=>	"extra button bag",
	"08"	=>	"hanger",
	"10"	=>	"hook&bar",
	"16"	=>	"hook&eye",
	"14"	=>	"Hangtag",
	"07"	=>	"label",
	"22"	=>	"label Main",
	"23"	=>	"label content",
	"24"	=>	"label co",
	"25"	=>	"label size",
	"26"	=>	"label care",
	"27"	=>	"label ID",
	"28"	=>	"label stretch",
	"29"	=>	"label seyle",
	"30"	=>	"label time",
	"34"	=>	"patch",
	"18"	=>	"piping",
	"20"	=>	"polybag",

	"00"	=>	"snap",
	"06"	=>	"SHD.-pad",
	"12"	=>	"SLV. Head",	
	"17"	=>	"sticker",	
	"19"	=>	"stopper",				
	"02"	=>	"Tag",										
	"04"	=>	"tape",	
	"31"	=>	"thread",		

	"01"	=>	"zipper",
	"32"	=>	"Nylon zipper",
	"09"	=>	"other",
);

$ACC_PRICE_UNIT = array(
	"pc",
	"lb",
	"kg",
	"dz",
	"gross",
	"inch",
	"ft",
	"yd",
	"meter",
	"set",
	"unit",
	"cone",
	"set"
);

$ACC_PRICE_UNIT_2 = array(
	"pc",
	"lb",
	"kg",
	"dz",
	"gross",
	"inch",
	"ft",
	"yd",
	"meter",
	"set",
	"unit",
	"K pcs",
	"100pcs",
	"cone",
	"set"
);

$UNIT_GROUP['FW'] = array('kg','lb');
$UNIT_GROUP['FL'] = array('yd','meter','inch','ft','g/yd.','kg');
$UNIT_GROUP['FA'] = array('SF');
$UNIT_GROUP['AP'] = array('pc','dz','gross');
$UNIT_GROUP['AW'] = array('kg','lb');
$UNIT_GROUP['AS'] = array('set','gross','dz');
$UNIT_GROUP['AU'] = array('unit');
$UNIT_GROUP['AP2'] = array('pc','dz','gross','K pcs','100pcs');
$UNIT_GROUP['AC'] = array('cone');

$FABRIC_WIDTH_UNIT = array(
	"inch",
	"c.m."
);

$CURRENCY = array(
	"USD",
	"NTD",
	"EUR",
	"HKD",
	"RMB",
	"GBP",
	"JPY",
);

$TRADE_TERM = array(
	"FOB",
	"FOR",
	"CIF",
	"CNF",
	"EX-FTY",
	"EX-Works",
);

$SUPL_TYPE = array(
	"FABRIC",
	"ACCESSORY",
	"SUBCON",
	"OTHERS",
);

$MAT_TYPE = array(
	"FABRIC",
	"Main ACCESSORY",
	"Other ACCESSORY"					
);

$APPAREL_UNIT = array("pc",);

$years = date('Y')+4;for( $i=2009;$i<$years;$i++){$YEAR_WORK[] = $i;}
$MONTH_WORK = array("01","02","03","04","05","06","07","08","09","10","11","12");
$DAY_WORK = array("01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31");

$USAGE_METHOD = array(
	"大貨用料",
	"代用料",
	"樣品用料"
);

$chin_error_word = array(
	"許\",
	"功\",
	"蓋\",
	"擺\",
	"么\",							
	"俞\",
	"么\",
	"涂\",
	"踊\",
	"\泞",
);
# "

$inv_style = array(
	"二聯式",
	"三聯式"				
);

$tax = array(
	"應稅",
	"零稅率",
	"免稅"
);

$cancel = array(
	"可扣抵",
	"固定資產可扣抵",
	"不可扣抵"
);

$FACTORY = array(
	"HJ",
	"LY",
	"CF",
	"V1",
	"C1",
	"SC"
);

$SAMPLER = array(
	"CL",
	"WX",
	"LY",
	"HM",
	"CF",
	"PH",
	"SC"
);

$COUNTRY = array(
	"USA",
	"CANADA",
	"CHINA",
	"TAIWAN",
	"PHILIPINE",
	"HONGKONG",
	"KOREA",
	"JAPAN",
	"ITALY",
	"VIETNAM",
	"TURKEY",
	"TAILAND",
	"INDIA",
	"ASIA",
	"INDONESIA",
	"CAMBODIA",
	"NETHERLANDS",
	"FRANCE"
);

$TI_ITEM = array(
	"Pattern",
	"Label Brand",
	"Fab./Acc.",
	"CUT/MATCH/FUSE",
	"Label Placement",
	"Construction",
	"Stitch / Seam",
	"Topstitching",
	"Closure / Fastener",
	"Wet Processing",
	"Tag / Packing",
	"Production Plant",
	"Comment",
	"other"
);

$SIZE_SCALE = array(
	"S-L",
	"S-XL",
	"P-XL",
	"PP-PL",
	"XS-L",
	"XS-XXL",
	"0-14",
	"2-16",
	"2-18",
	"4-16",
	"6-16",
	"14W-22W",
	"14W-26W",
	"24L-42L",
	"1X-3X"
);

$STYLE_CAT = array(
	"MISSY",
	"PETITE",
	"MAMA",
	"MAN",
	"BOY",
	"GIRL"
);

$SMPL_APV_TYPE = array(
	"PPS",
	"PPS MISSY",
	"PPS PETITE",
	"PPS MAMA",
	"FIT",
	"FIT MISSY",
	"FIT PETITE",
	"FIT MAMA",
	"repeat order",
);

$usance= array(
	"0",
	"30",
	"40",
	"60",
	"80",
	"90",
	"110",
	"140",
	"240"
);

$supl_property = array(
	"1.CL",
	"3.pool",
	"5.TY",
	"7.CA",
	"9.other"
);

$supl_use = array(
	"1.Office",
	"2.Factory"
);

$dm_way[0] = array(
	"L/C at sight",
	"T/T before shipment",
	"Check",
	"Check before shipment",
	"COD",
	"Bank Draft",
	"T/T after shipment",
	"Input T/T of shipment separately the BEFORE & AFTER",
	"月結30天",
	"月結45天",
	"月結60天",
	"月結80天",
	"D/D",
	"D/D befort shipment",
);

$dm_way_full[0] = array(
	"L/C at sight",
	"T/T before shipment",
	"Check",
	"COD",
	"Bank Draft",
	"T/T after shipment",
    "Input T/T of shipment separately the BEFORE & AFTER",
	"月結30天",
	"月結60天",
	"月結80天",
	"D/D",
	"D/D befort shipment",
);

$SEASON = array('S/S','F/W','BTS','H-day.','CORE 9999');

$dm_way[1] = array (4,3,2,1,5,6,7,8,9,10,11);

$vant =5;

$dt = decode_date(1);
$TODAY = $dt['date'];
$THIS_MON = $dt['year'].substr($dt['date'],5,2);
$THIS_YEAR = $dt['year'];
$THIS_TIME = $dt['date_str'];
$THIS_DATE = substr($THIS_TIME, 8, 2);

$ord_append_ETP_limit = increceDaysInDate($TODAY,1);
$ord_append_ETD_limit = increceDaysInDate($TODAY,30);

$ord_submit_ETP_limit = $TODAY;
$ord_submit_ETD_limit = increceDaysInDate($TODAY,20);

$MAIN_PROGRAM = "index2.php";

$SMPL_LOG_SUBJ = array(
	"W / I",
	"FABRIC",
	"ACCESSORY",
	"PATTERN",
	"SAMPLE",
	"MARKER",
	"ETD",
	"OTHERS",
	"PENDING",
	"REDOING",
);

$PO_SHIP = array("Air","Express","Vessel","Truck");
$ORD_SHIP = array("Air","SEA");
$ORD_DIST = array("USA","CANADA","PANAMA","EUROPE");

// # 台幣兌換 us hk eur
// $cury_d['NTD']['NTD'] = 1.00;
// $cury_d['NTD']['USD'] = 0.030723;	#美元
// $cury_d['NTD']['HKD'] = 0.23831;	#港幣
// $cury_d['NTD']['EUR'] = 0.022940;	#歐元

// # 美金兌換 nt hk eur
// $cury_d['USD']['USD'] = 1.00;
// $cury_d['USD']['NTD'] = 32.548;		#新台幣
// $cury_d['USD']['HKD'] = 7.7568;		#港幣
// $cury_d['USD']['EUR'] = 0.74733;	#歐元

// # 港幣兌換 nt us eur
// $cury_d['HKD']['HKD'] = 1.00;
// $cury_d['HKD']['NTD'] = 4.196;		#新台幣
// $cury_d['HKD']['USD'] = 0.12892;	#美元
// $cury_d['HKD']['EUR'] = 0.096263;	#歐元

// # 歐元兌換 nt us hk
// $cury_d['EUR']['EUR'] = 1.00;
// $cury_d['EUR']['NTD'] = 43.586;	#新台幣
// $cury_d['EUR']['USD'] = 1.3392;	#美元
// $cury_d['EUR']['HKD'] = 10.388;	#港幣								

$CONTAINER['SV'] = array(
	'name'		=>	'SCHENKER VIETNAM CO., LTD.',
	'addr'		=>	'UNIT 601, 6TH FLOOR, C.T PLAZA, 60A TRUONG SON STREET TAN BINH DISTRICT',												
	'city'		=>	'HO CHI MINH',
	'state'		=>	'',
	'postal'	=>	'',
	'country'	=>	'VIETNAM'
);

$PACK_COVER['LY'] = array(
	'name'		=>	'LI-YUEN GARMENT CO., LTD',
	'addr'		=>	'TRANG BANG INDUSTRIAL PARK AN BINH HAMLET, AN TINH VILLAGE TRANG BANG DISTRICT',
	'city'		=>	'',
	'state'		=>	'TAY NINH PROVINCE',
	'postal'	=>	'66',
	'country'	=>	'VIET NAM'
);

$PACK_COVER['CA'] = array(
	'name'		=>	'CARNIVAL INDUSTRIAL CORP',
	'addr'		=>	'7 FL, 25 JEN-AI ROAD SECTION 4,',
	'city'		=>	'TAIPEI',
	'state'		=>	'',
	'postal'	=>	'106',
	'country'	=>	'R.O.C'
);

$PACK_COVER[0]    = array(
	'name'		=>	'',
	'addr'		=>	'',
	'city'		=>	'',
	'state'		=>	'',
	'postal'	=>	'',
	'country'	=>	''
);	


$SHIP_PO_TYPE = array('Fert','PPK','Suit S','Suit C');
$SHIP_FACILLITY = array('CY','CFS');
$SHIP_MODE = array('Air','MLB','Sea');
$SHIP_METHOD = array('Flat Pack','GOH','Pre-Ticketed','CTH');

$FAB_TYPE = array('shell', 'combo', 'lining', 'fusible', 'pocketing');

# SMPL 樣式
$SMPL_TYPE = array( 'wash' => '1' , 'emb' => '2' , 'print' => '4' , 'quilting' => '8' );

//shipping document新增 --- start ----
$MID = array('VNLIYUETAY','CNWUXHUJ7WUX');
$SHIP_COUNTRY = array('VIETNAM','CHINA');
//shipping document新增 --- end ----

session_register ('SCACHE');
session_register ($MYSQL_DB);
session_register ('STOCK_USER');
# 工廠庫存畫面是越南文的人員login_id 用小寫
$_SESSION['STOCK_USER'] = array("nhan","doah","dao","gap");

$layout = new LAYOUT;

global $PHP_sr_startno ;

$GLOBALS['power'] = array();
$DEPT_K = array();
$DEPT_J = array();
$DEPT_SALES = array();

$GLOBALS['dept_J'] = array();
$GLOBALS['sales_dept_Array'] = array();

$MY_DEPT = '';
$MY_TEAM = '';
$ME = '';





#############################################
$layout_dir	=  $config['root_dir']."/templates";
$smpl_pttn_dir	= $config['root_dir']."/smpl_pttn/";
$vendor_list = array();
########### TPL  的設定  ############
$TPL_HEADING = "heading.html";
$TPL_FOOTING = "footing.html";
$TPL_ERROR = "error.html";
$TPL_NOTICE = "notice.html";


include_once($config['root_dir']."/lib/class.get.php");  $M_get = new M_GET();



include_once($config['root_dir']."/lib/class.mimemail.php");
include_once($config['root_dir']."/lib/class.search.php");  $Search = new SEARCH();
include_once($config['root_dir']."/lib/class.array2checkbox.php");
include_once($config['root_dir']."/lib/class.array2html.php");
include_once($config['root_dir']."/lib/class.uploadIMG.php");

include_once($config['root_dir']."/lib/class.cust.php");
include_once($config['root_dir']."/lib/class.supl.php");
include_once($config['root_dir']."/lib/class.lots.php");
include_once($config['root_dir']."/lib/class.acc.php");
include_once($config['root_dir']."/lib/class.tmpsupl.php");

include_once($config['root_dir']."/lib/class.smpl.php");
include_once($config['root_dir']."/lib/class.smpl_lots.php");   // 樣本主料 [ perhaps use mat. ]
include_once($config['root_dir']."/lib/class.smpl_acc.php");   // 樣本副料 [ perhaps use mat. ]

include_once($config['root_dir']."/lib/class.wi.php");
include_once($config['root_dir']."/lib/class.wi_qty.php");
include_once($config['root_dir']."/lib/class.wiqty.php");

include_once($config['root_dir']."/lib/class.ti.php");

include_once($config['root_dir']."/lib/class.file.php");

include_once($config['root_dir']."/lib/class.bom.php");

include_once($config['root_dir']."/lib/class.team.php");
include_once($config['root_dir']."/lib/class.user.php");


include_once($config['root_dir']."/lib/class.dept.php");
include_once($config['root_dir']."/lib/class.smpl_type.php");
include_once($config['root_dir']."/lib/class.style_type.php");    
include_once($config['root_dir']."/lib/class.season.php");
include_once($config['root_dir']."/lib/class.size_type.php");   // temp????????
include_once($config['root_dir']."/lib/class.supl_type.php");
include_once($config['root_dir']."/lib/class.size_des.php");

include_once($config['root_dir']."/lib/class.ag.php");

include_once($config['root_dir']."/lib/class.order.php");
include_once($config['root_dir']."/lib/class.r_order.php");
include_once($config['root_dir']."/lib/class.order_log.php");
include_once($config['root_dir']."/lib/class.c_order.php");

include_once($config['root_dir']."/lib/class.capaci.php");
include_once($config['root_dir']."/lib/class.daily.php");
include_once($config['root_dir']."/lib/class.shipping.php");

include_once($config['root_dir']."/lib/class.offer.php");
include_once($config['root_dir']."/lib/class.forecast.php");
include_once($config['root_dir']."/lib/class.smpl_ord.php");
include_once($config['root_dir']."/lib/class.smpl_log.php");
include_once($config['root_dir']."/lib/class.smpl_output.php");

include_once($config['root_dir']."/lib/Upload.class.php");
include_once($config['root_dir']."/lib/EasyDownload.php");

include_once($config['root_dir']."/lib/class.smplord_lots.php");
include_once($config['root_dir']."/lib/class.smplord_acc.php");
include_once($config['root_dir']."/lib/class.smpl_wi.php");
include_once($config['root_dir']."/lib/class.smpl_wiqty.php");
include_once($config['root_dir']."/lib/class.smpl_ti.php");
include_once($config['root_dir']."/lib/class.smpl_bom.php");


include_once($config['root_dir']."/lib/class.apply.php");
include_once($config['root_dir']."/lib/class.po.php");
include_once($config['root_dir']."/lib/class.receive.php");
include_once($config['root_dir']."/lib/class.tw_receive.php");

include_once($config['root_dir']."/lib/class.report.php");
include_once($config['root_dir']."/lib/class.rate.php");
include_once($config['root_dir']."/lib/class.chinese_cov.php");

include_once($config['root_dir']."/lib/class.exception.php");

include_once($config['root_dir']."/lib/class.notify.php");

//include others class
//para_set 於2008-04-22新增
include_once($config['root_dir']."/lib/class.para.php");

//PO SHIP 2008-11-13
include_once($config['root_dir']."/lib/class.po_ship.php");

//C.M. & sales cost 2008-11-25
include_once($config['root_dir']."/lib/class.cost.php");

//研發馬克 2008-12-04
include_once($config['root_dir']."/lib/class.marker.php");
//索賠 2008-12-17
include_once($config['root_dir']."/lib/class.debit.php");

//主料 2009-05-20
include_once($config['root_dir']."/lib/class.fabric.php");

//Shipping document 2008-11-05
include_once($config['root_dir']."/lib/class.shipdoc.php");

//加班 2009-7-7
include_once($config['root_dir']."/lib/class.overtime.php");

//部門預算 2010-02-01
include_once($config['root_dir']."/lib/class.expense.php");

//Forecast 2008-09-30
include_once($config['root_dir']."/lib/class.fcst2.php");

//研發馬克 2010-02-02
include_once($config['root_dir']."/lib/class.fty_marker.php");

//排產 2010-04-01
include_once($config['root_dir']."/lib/class.schedule.php");

//樣本排產 2010-05-02
include_once($config['root_dir']."/lib/class.smpl_sch.php");

//驗布報告 2009-7-7
include_once($config['root_dir']."/lib/class.rcv_rpt.php");

//訂單裁剪 2010-06-04
include_once($config['root_dir']."/lib/class.cutting.php");

//應付 2011-05-17
include_once($config['root_dir']."/lib/class.apb.php");

# SHIPPING 2012-03-05
include_once($config['root_dir']."/lib/class.shipping_doc.php");



?>