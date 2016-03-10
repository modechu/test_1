<?php
#
#
#
session_start();
ini_set('display_errors', true);

// echo $PHP_action.'<br>';
#
#
#
require_once "config.php";
require_once "config.admin.php";
require_once "init.object.php";
include_once($config['root_dir']."/lib/class.rfid.php");
$rfid = new RFID();
if (!$rfid->init($mysql,"log")) { print "error!! cannot initialize database for W_DAILY_OUT class"; exit; }
#
#
#
$PHP_SELF = $_SERVER['PHP_SELF'];
$perm = $GLOBALS['power'];
$op = array();
#
#
#
$AUTH = '105';
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
	//echo $line;
}
else
{
	$line = $_GET['PHP_line'];
}
if($_GET['PHP_start_date'] =='')
{
	$date_start = date ("Y-m-d");
}
else
{
	$date_start = $_GET['PHP_start_date'];
}
if($_GET['PHP_end_date'] =='')
{
	$date_end = date ("Y-m-d");
}
else
{
	$date_end = $_GET['PHP_end_date'];
}
//echo $date_start."<br>";
//echo $date_end."<br>";
/*select用的組別*/
$t_sql = "  USE HR;
			select c_co_name 
			from t_code
			where c_co_in='033' and c_CO_id <=952
";
$line_temp = $rfid->get_wip1data($t_sql);
$line_new = array();
//print_r($line['c_co_name'] );
foreach($line_temp as $linekey => $linevalue)
{
	$line_new[$linekey] = $linevalue['c_co_name'];
}
if($_GET['PHP_line'] =='')
{
	$op['line_select'] =$arry2->select($line_new,"1","PHP_line","select","");
}
else
{
	$op['line_select'] =$arry2->select($line_new,$line,"PHP_line","select","");
}
//$op['line_select'] =$arry2->select($line_new,"1","PHP_line","select","");
//print_r($op['line_select']);
if($line!='' and $date_start!='' and $date_end!='')
{
	/*在職員工*/
	$t_sql = "  USE HR;
				select emp.c_em_id,
				emp.c_em_code,
				emp.c_em_name,
				emp.c_em_append3,
				code.c_co_name  
				from t_employee emp
				left join t_code code on code.c_co_code = emp.c_em_append3
				where emp.c_em_status=0 
					  and code.c_co_in='033' 
					  and c_co_name='".$line."'";

	$emp = $rfid->get_wip1data($t_sql);
	$op['line_emp_qty'] = sizeof($emp);
//echo $line;


	/*從在職員工查工人打卡資料*/
	$t_sql = "  USE HR; 
				select c_as_emp,
					c_as_code,
					c_as_time,
					c_as_work,
					c_as_date 
				from t_attend_souce 
				where c_as_emp in (" ; //28017,28330
				for($i=0;$i<sizeof($emp);$i++)
				{
					if($i==(sizeof($emp)-1))
					{
						$t_sql .=$emp[$i]['c_em_id'];
					}
					else
					{
						$t_sql .=$emp[$i]['c_em_id'].',';
					}
				}
	$t_sql .= 	") and 
				c_as_date between '".$date_start."' and '".$date_end."' 
				and c_as_work is not null
				group by c_as_emp,c_as_code,c_as_time,c_as_work,c_as_date
				order by c_as_emp,c_as_time,c_as_date";

	//echo $t_sql."<br>";

	$emp_attend = $rfid->get_wip1data($t_sql);
	$emp_attend_new = array();
	$emp_id_temp='';
	$emp_code_temp='';
	$temp_date='';
	$temp=0;

	//在職員工與考勤資料比對，在原始的emp_attend加入工號資訊
	for($i=0;$i<sizeof($emp);$i++)
	{
		for($j=0;$j<sizeof($emp_attend);$j++)
		{
			if($emp[$i]['c_em_id'] == $emp_attend[$j]['c_as_emp'])
			{
				$emp_attend[$j]['c_as_empcode'] = $emp[$i]['c_em_code'];
			}
		}
	}
	//首先從考勤資料開始迴圈比對(沒有考勤資料就不會有下列資料)
	/*foreach($emp_attend as $attkey => $attvalue)
	{
		$tmp_count++;
		if($emp_id_temp != $attvalue['c_as_emp'])
		{
			
			if($attkey!=0)
			{
				$checkdate = $temp_date.' 11:30:00';
				$last_checkdate = $temp_date.' 12:30:00';
				if(strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][0]) < strtotime($checkdate))
				{
					//早上就有打卡
					if(strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1]) < strtotime($last_checkdate))
					{
						//只有早上上班
						$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1])-strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][0]))/(60*60));
					}
					else
					{
						if((strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1])) < strtotime($temp_date." 18:30:00"))
						{
							//此區域為18:15下班的考勤判斷(OT2小時)
							$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($temp_date." 18:30:00")-strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][0]))/(60*60)-1);
						}
						else
						{
							//此區域為20:30下班的考勤判斷(OT4小時)
							$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1])-strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][0]))/(60*60)-1);//計算這個員工在這天上班幾個小時(有扣中午1小時)
						}
						
					}
				}
				else
				{
					//下午才打卡
					if((strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1])) < strtotime($temp_date." 18:30:00"))
					{
						//此區域為18:15下班的考勤判斷(OT2小時)
						$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($temp_date." 18:30:00")-strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][0]))/(60*60));
					}
					else
					{
						//此區域為20:30下班的考勤判斷(OT4小時)
						$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1])-strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][0]))/(60*60));
					}
					
				}
				
				$emp_attend_new[$emp_id_temp][$temp_date]['today_seconds'] = $emp_attend_new[$emp_id_temp][$temp_date]['work_hours']*60*60;
				$temp =0 ;
			}
			//不同人
			$emp_id_temp= $attvalue['c_as_emp'];
			$emp_code_temp = $attvalue['c_as_empcode'];
			$temp_date= $attvalue['c_as_date'];
			$emp_attend_new[$emp_id_temp]['emp_code'] = $emp_code_temp ;
			$emp_attend_new[$emp_id_temp][$attvalue['c_as_date']]['attend'][$temp] = substr($attvalue['c_as_time'],0,-4) ;

			$emp_attend_new[$emp_id_temp][$temp_date]['showdate']=$attvalue['c_as_date'];
			$temp++;
		}
		else
		{
			//同員工
			if($temp_date != $attvalue['c_as_date'])
			{
				
				$checkdate = $temp_date.' 11:30:00';
				$last_checkdate = $temp_date.' 12:30:00';
				if(strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][0]) < strtotime($checkdate))
				//if(strtotime($temp_date.' 11:30:10') < strtotime($checkdate))
				{
					//早上就有打卡
					if(strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1]) < strtotime($last_checkdate))
					{
						
						//只有早上上班
						$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1])-strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][0]))/(60*60));
					}
					else
					{
						
						if((strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1])) < strtotime($temp_date." 18:30:00"))
						{
							
							//此區域為18:15下班的考勤判斷(OT2小時)
							$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($temp_date." 18:30:00")-strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][0]))/(60*60)-1);
						}
						else
						{
							
							//此區域為20:30下班的考勤判斷(OT4小時)
							$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1])-strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][0]))/(60*60)-1);//計算這個員工在這天上班幾個小時(有扣中午1小時)
						}
						//$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1])-strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][0]))/(60*60)-1);//計算這個員工在這天上班幾個小時(有扣中午1小時)
					}
					
				}
				else
				{
					//下午才打卡
					if((strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1])) < strtotime($temp_date." 18:30:00"))
					{
						//此區域為18:15下班的考勤判斷(OT2小時)
						$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($temp_date." 18:30:00")-strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][0]))/(60*60));
					}
					else
					{
						//此區域為20:30下班的考勤判斷(OT4小時)
						$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1])-strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][0]))/(60*60));
					}
					//$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1])-strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][0]))/(60*60));
				}
				//$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = round((strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1])-strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][0]))/(60*60)-1);//計算這個員工在這天上班幾個小時(有扣中午1小時)
				$emp_attend_new[$emp_id_temp][$temp_date]['today_seconds'] = $emp_attend_new[$emp_id_temp][$temp_date]['work_hours']*60*60;
				//不同日期
				$temp=0;
				$temp_date= $attvalue['c_as_date'];
				//$emp_attend_new[$emp_id_temp][$attvalue['c_as_date']][$temp] = substr($attvalue['c_as_time'],0,-4) ;
				$emp_attend_new[$emp_id_temp][$attvalue['c_as_date']]['attend'][$temp] = substr($attvalue['c_as_time'],0,-4) ;			
				$emp_attend_new[$emp_id_temp][$temp_date]['showdate']=$attvalue['c_as_date'];
				$temp++;
			}
			else
			{
				
				//$emp_attend_new[$emp_id_temp][$attvalue['c_as_date']][$temp] = substr($attvalue['c_as_time'],0,-4) ; 
				$emp_attend_new[$emp_id_temp][$attvalue['c_as_date']]['attend'][$temp] = substr($attvalue['c_as_time'],0,-4) ;
				$temp++;
			}
		}
			
	}
	if($emp_attend)
	{
		$checkdate = $temp_date.' 11:30:00';
		$last_checkdate = $temp_date.' 12:30:00';
		if(strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][0]) < strtotime($checkdate))
		//if(strtotime($temp_date.' 11:30:10') < strtotime($checkdate))
		{
			//早上就有打卡
			if(strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1]) < strtotime($last_checkdate))
			{
			
				//只有早上上班
				$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1])-strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][0]))/(60*60));
			}
			else
			{
						
				if((strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1])) < strtotime($temp_date." 18:30:00"))
				{
							
					//此區域為18:15下班的考勤判斷(OT2小時)
					$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($temp_date." 18:30:00")-strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][0]))/(60*60)-1);
				}
				else
				{
							
					//此區域為20:30下班的考勤判斷(OT4小時)
					$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1])-strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][0]))/(60*60)-1);//計算這個員工在這天上班幾個小時(有扣中午1小時)
				}
						//$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1])-strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][0]))/(60*60)-1);//計算這個員工在這天上班幾個小時(有扣中午1小時)
			}
					
		}
		else
		{
			//下午才打卡
			if((strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1])) < strtotime($temp_date." 18:30:00"))
			{
				//此區域為18:15下班的考勤判斷(OT2小時)
				$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($temp_date." 18:30:00")-strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][0]))/(60*60));
			}
			else
			{
				//此區域為20:30下班的考勤判斷(OT4小時)
				$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1])-strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][0]))/(60*60));
			}
				
		}
		$emp_attend_new[$emp_id_temp][$temp_date]['today_seconds'] = $emp_attend_new[$emp_id_temp][$temp_date]['work_hours']*60*60;
	} */
	
	foreach($emp_attend as $attkey => $attvalue)
	{
		$tmp_count++;
		if($emp_id_temp != $attvalue['c_as_emp'])
		{
			if($attkey!=0)
			{
				$checkdate = $temp_date.' 11:30:00';
				$last_checkdate = $temp_date.' 12:30:00';
				if(strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][0]) < strtotime($checkdate))
				{
					//早上就有打卡
					if(strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1]) < strtotime($last_checkdate))
					{
						//只有早上上班
						$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1])-strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][0]))/(60*60));
					}
					else
					{
						$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1])-strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][0]))/(60*60)-1);//計算這個員工在這天上班幾個小時(有扣中午1小時)
					}
				}
				else
				{
					//下午才打卡
					$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1])-strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][0]))/(60*60));
				}
				
				$emp_attend_new[$emp_id_temp][$temp_date]['today_seconds'] = $emp_attend_new[$emp_id_temp][$temp_date]['work_hours']*60*60;
				$temp =0 ;
			}
			//不同人
			$emp_id_temp= $attvalue['c_as_emp'];
			$emp_code_temp = $attvalue['c_as_empcode'];
			$temp_date= $attvalue['c_as_date'];
			$emp_attend_new[$emp_id_temp][$attvalue['c_as_date']]['attend'][$temp] = substr($attvalue['c_as_time'],0,-4) ;

			$emp_attend_new[$emp_id_temp][$temp_date]['showdate']=$attvalue['c_as_date'];
			$temp++;
		}
		else
		{
			if($temp_date != $attvalue['c_as_date'])
			{
				$checkdate = $temp_date.' 11:30:00';
				$last_checkdate = $temp_date.' 12:30:00';
				if(strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][0]) < strtotime($checkdate))
				{
					
					//早上就有打卡
					if(strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1]) < strtotime($last_checkdate))
					{
						//只有早上上班
						$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1])-strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][0]))/(60*60));
					}
					else
					{
						$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1])-strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][0]))/(60*60)-1);//計算這個員工在這天上班幾個小時(有扣中午1小時)
					}
					
				}
				else
				{
					//下午才打卡
					$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1])-strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][0]))/(60*60));
				}
				//$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = round((strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1])-strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][0]))/(60*60)-1);//計算這個員工在這天上班幾個小時(有扣中午1小時)
				$emp_attend_new[$emp_id_temp][$temp_date]['today_seconds'] = $emp_attend_new[$emp_id_temp][$temp_date]['work_hours']*60*60;
				//不同日期
				$temp=0;
				$temp_date= $attvalue['c_as_date'];
				//$emp_attend_new[$emp_id_temp][$attvalue['c_as_date']][$temp] = substr($attvalue['c_as_time'],0,-4) ;
				$emp_attend_new[$emp_id_temp][$attvalue['c_as_date']]['attend'][$temp] = substr($attvalue['c_as_time'],0,-4) ;			
				$emp_attend_new[$emp_id_temp][$temp_date]['showdate']=$attvalue['c_as_date'];
				$temp++;
			}
			else
			{
				//$emp_attend_new[$emp_id_temp][$attvalue['c_as_date']][$temp] = substr($attvalue['c_as_time'],0,-4) ; 
				$emp_attend_new[$emp_id_temp][$attvalue['c_as_date']]['attend'][$temp] = substr($attvalue['c_as_time'],0,-4) ;
				$temp++;
			}
		}
			
	}
	if($emp_attend)
	{
		$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1])-strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][0]))/(60*60)-1);//計算這個員工在這天上班幾個小時(有扣中午1小時)
		$emp_attend_new[$emp_id_temp][$temp_date]['today_seconds'] = $emp_attend_new[$emp_id_temp][$temp_date]['work_hours']*60*60;
	}
	//print_r($emp_attend_new);
	//print_r($emp_attend);
	 /*員工每日每工段生產總量*/
	 $t_sql = "  USE NewWIPOne;
				 select done.workerid,
				 done.lotid,
				 done.stepid,
				 step.stepdesc,
				 sum(done.packedqty) as qty,
				 convert(varchar(100),done.scantime,23) as scantime,
				 step.sas  
	 from workdone done
	 left join lotssteps step on step.lotid = done.lotid and step.stepid = done.stepid 
	 where convert(varchar(100),done.scantime,23) between '".$date_start."' and '".$date_end."'
	 group by done.workerid,done.lotid,done.stepid,step.stepdesc,convert(varchar(100),done.scantime,23),step.sas
	 order by done.workerid,convert(varchar(100),done.scantime,23),done.lotid,done.stepid";
	 $emp_production = $rfid->get_wip1data($t_sql);
	 //print_r($emp_production);
	 //print_r($emp_attend_new);
	 //echo  $t_sql;
	 //echo $t_sql."<br>";
	if($emp_production)
	{
		foreach($emp_attend_new as $empkey => $empvalue)
		{
			foreach($emp_production as $prodkey => $prodvalue)
			{
				//echo $empkey."<br>";
				//工號相同
				
				if($empkey == $prodvalue['workerid'])
				{
					//echo $empkey.':'.$prodvalue['lotid'].':'.$prodvalue['stepid'];
					//echo "<br>";
					//$total_sas=0;
					foreach($empvalue as $empsubkey => $empsubvalue)
					{
						
						//日期相同
						if($empsubkey == $prodvalue['scantime'])
						{
							//$emp_attend_new[$empkey][$empsubkey]['order']['show']=$prodvalue['lotid'];
							$emp_attend_new[$empkey][$empsubkey]['order'][$prodvalue['lotid']][$prodvalue['stepid']]['qty'] = floor($prodvalue['qty']);
							$emp_attend_new[$empkey][$empsubkey]['order'][$prodvalue['lotid']][$prodvalue['stepid']]['sas'] = floor($prodvalue['sas']);
							$emp_attend_new[$empkey][$empsubkey]['order'][$prodvalue['lotid']][$prodvalue['stepid']]['total_sas'] = floor($prodvalue['qty']) * floor($prodvalue['sas']);
							$total_sas += $emp_attend_new[$empkey][$empsubkey]['order'][$prodvalue['lotid']][$prodvalue['stepid']]['total_sas'];
							
						}
						
						
						//$emp_attend_new[$empkey][$empsubkey]['total_sas'] = $total_sas ;
					}
					
					//$emp_attend_new[$empkey][$empsubkey]['total_sas'] = $total_sas ;
					
				}
			}
		}
		$step_rowspan = 0;
		$order_rowspan = 0;//step加總
		$date_rowspan = 0;//訂單order_rowspan加總
		$emp_rowspan = 0;//日期date_rowspan加總
		$temp_order='';

		foreach($emp_attend_new as $emp1 => $val1)//工號
		{	

			foreach($val1 as $emp2 => $val2)//日期
			{
				
				$emp_sum_sas = 0;
				//echo $emp2."<br>";
				if($val2['order'])
				{
					foreach($val2['order'] as $emp3 => $val3)//訂單號
					{
						//echo $emp3."<br>";
						foreach($val3 as $emp4 => $val4)//工序號
						{
							$emp_sum_sas += $val4['total_sas'];	
							$step_rowspan++;
						}
						$emp_attend_new[$emp1][$emp2]['order'][$emp3]['order_rowspan'] = $step_rowspan;
						//echo $emp1.":".$emp2.":".$emp3 .":".$step_rowspan."<br>";
						$date_rowspan += $step_rowspan;
						$emp_rowspan += $step_rowspan;
						$step_rowspan=0;
					}
				}
				else
				{
					$emp_rowspan++;
				}
				
				$emp_attend_new[$emp1][$emp2]['date_rowspan'] = $date_rowspan;
				$date_rowspan = 0;
				$val2['total_sum_sas'] = $emp_sum_sas;
				
				$emp_attend_new[$emp1][$emp2]['total_sum_sas'] = $emp_sum_sas;
				$emp_attend_new[$emp1][$emp2]['performance'] = round($emp_sum_sas/$emp_attend_new[$emp1][$emp2]['today_seconds']*100);
			}
			if($emp_rowspan == 0)
			{
				$emp_attend_new[$emp1]['info']['emp_rowspan'] = sizeof($val1);
				$emp_attend_new[$emp1]['emp_rowspan'] = sizeof($val1);
			}
			else
			{
				$emp_attend_new[$emp1]['info']['emp_rowspan'] = $emp_rowspan;
				$emp_attend_new[$emp1]['emp_rowspan'] = $emp_rowspan;
			}
			
			$emp_rowspan =0;
			
			foreach($emp as $keychangeid => $valchangeid)
			{
				if($emp1 == $valchangeid['c_em_id'])
				{
					$emp_attend_new[$emp1]['info']['workerid'] = $valchangeid['c_em_code'];
					$emp_attend_new[$emp1]['info']['workername'] = $valchangeid['c_em_name'];
					$emp_attend_new[$emp1]['workerid'] = $valchangeid['c_em_code'];
					$emp_attend_new[$emp1]['workername'] = $valchangeid['c_em_name'];
				}
			}
		}
		//print_r($emp_attend_new);
		$op['emp_detail'] = $emp_attend_new;
	}
}
//print_r($op);
page_display($op,$AUTH,'rfid_list.html');
break;
#
#
#
#
#
#
} # CASE END
?>