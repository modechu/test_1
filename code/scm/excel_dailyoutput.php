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
include_once($config['root_dir']."/lib/class.excel_dailyoutput.php");
$excel_dailyoutput = new Excel_dailyoutput();
if (!$excel_dailyoutput->init($mysql,"log")) { print "error!! cannot initialize database for Excel_dailyoutput class"; exit; }
#
#
#
$PHP_SELF = $_SERVER['PHP_SELF'];
$perm = $GLOBALS['power'];
$op = array();
#
#
#
$AUTH = '106';
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
	//$date_start = '2015-04-14';
}
else
{
	$date_start = $_GET['PHP_start_date'];
}
if($_GET['PHP_end_date'] =='')
{
	$date_end = date ("Y-m-d");
	//$date_end = '2015-04-14';
}
else
{
	$date_end = $_GET['PHP_end_date'];
}
//echo $date_start."<br>";
//echo $date_end."<br>";
/*select�Ϊ��էO*/
$t_sql = "  USE HR;
			select c_co_name 
			from t_code
			where c_co_in='033' and c_CO_id <=952
";
$line_temp = $excel_dailyoutput->get_wip1data($t_sql);
/* print_r($line_temp);
exit;  */
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
//print_r($op['line_select']);
if($line!='' and $date_start!='' and $date_end!='')
{
	/*�b¾���u*/
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
					  and code.c_co_name='".$line."'";
//echo $t_sql;
	$emp = $excel_dailyoutput->get_wip1data($t_sql);
	/* print_r($emp);
	exit; */ 
	$op['line_emp_qty'] = sizeof($emp);
//echo $line;


	/*�q�b¾���u�d�u�H���d���*/
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
	/* $t_sql .= 	") and 
				c_as_date between '".$date_start."' and '".$date_end."' 
				and c_as_work is not null
				group by c_as_emp,c_as_code,c_as_time,c_as_work,c_as_date
				order by c_as_emp,c_as_time,c_as_date"; */
	$t_sql .= 	") and 
				c_as_date between '".$date_start."' and '".$date_end."' 
				
				group by c_as_emp,c_as_code,c_as_time,c_as_work,c_as_date
				order by c_as_emp,c_as_time,c_as_date";

	/* echo $t_sql."<br>";
	exit; */
	$emp_attend = $excel_dailyoutput->get_wip1data($t_sql);
	//print_r($emp_attend);
	//exit;
	$emp_attend_new = array();
	$emp_id_temp='';
	$emp_code_temp='';
	$temp_date='';
	$temp=0;
	$ondutytime_check = '';
	
	//�b¾���u�P�ҶԸ�Ƥ��A�b��l��emp_attend�[�J�u����T
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
	/* print_r($emp_attend);
	exit; */
	//�����q�ҶԸ�ƶ}�l�j����(�S���ҶԸ�ƴN���|���U�C���)
	/* print_r($emp_attend);
	exit;   */
	foreach($emp_attend as $attkey => $attvalue)
	{
		$tmp_count++;
		//echo $attkey."<br>";
		if($emp_id_temp != $attvalue['c_as_emp'])
		{
		    //�u�����P
			/* if($attvalue['c_as_date']=='2015-04-13' && $attvalue['c_as_emp']=='29612')
			{
				echo 'A1';
			} */
			if($attkey!=0)
			{
			    //���O�����}�C�����Ĥ@�����
				
				$checkdate = $temp_date.' 11:30:00';
				$last_checkdate = $temp_date.' 12:30:00';
				//$morning1_check = $temp_date.' 11:30:00';
				//$morning2_check = $temp_date.' 12:30:00';
				if(strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][0]) < strtotime($checkdate))
				{
					//���W�N�����d
					
					if(strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1]) < strtotime($last_checkdate))
					{
						//�u�����W�W�Z(�O�_�٭n�[�P�_�A�P�_12:30��d�A�Y��5�p�ɤF�A�٬O�n��1�p��)
						
						$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1])-strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][0]))/(60*60));
						//$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1])-strtotime($temp_date.' 07:30:00'))/(60*60));
					}
					else
					{
					    
						$ondutytime_check = $temp_date.' 18:15:00';
						if(strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1]) > strtotime($ondutytime_check))
						{
							//���[�Z
							if((strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1])) < strtotime($temp_date." 18:30:00"))
							{
								//���ϰ쬰18:15�U�Z���ҶԧP�_(OT2�p��)
								
								$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($temp_date." 18:30:00")-strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][0]))/(60*60)-1);
								//$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($temp_date." 18:30:00")-strtotime($temp_date.' 07:30:00'))/(60*60)-1);
							}
							else
							{
								//���ϰ쬰20:30�U�Z���ҶԧP�_(OT4�p��)
								
								$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1])-strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][0]))/(60*60)-1);//�p��o�ӭ��u�b�o�ѤW�Z�X�Ӥp��(��������1�p��)
								//$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1])-strtotime($temp_date.' 07:30:00'))/(60*60)-1);//�p��o�ӭ��u�b�o�ѤW�Z�X�Ӥp��(��������1�p��)
							}
						
						
						}
						else
						{
							//�S�[�Z
							$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1])-strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][0]))/(60*60)-1);
							//$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1])-strtotime($temp_date.' 07:30:00'))/(60*60)-1);
						}
					}
				}
				else
				{
					//�U�Ȥ~���d
					$ondutytime_check = $temp_date.' 18:15:00';
					if(strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1]) > strtotime($ondutytime_check))
					{
						//���[�Z
						if((strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1])) < strtotime($temp_date." 18:30:00"))
						{
							//���ϰ쬰18:15�U�Z���ҶԧP�_(OT2�p��)
							$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($temp_date." 18:30:00")-strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][0]))/(60*60));
						}
						else
						{
							//���ϰ쬰20:30�U�Z���ҶԧP�_(OT4�p��)
							$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1])-strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][0]))/(60*60));
						}
						
						
					}
					else
					{
						//�S�[�Z
						$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1])-strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][0]))/(60*60));
					}
				}
				
				$emp_attend_new[$emp_id_temp][$temp_date]['today_seconds'] = $emp_attend_new[$emp_id_temp][$temp_date]['work_hours']*60*60;
				$temp =0 ;
			}
			/* if($attvalue['c_as_empcode'] == 'D3025')
			{
			    echo "B2";
				echo "<br>";
			} */
			//���P�H
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
			//�P���u
			
			if($temp_date != $attvalue['c_as_date'])
			{
				//���P���
				$checkdate = $temp_date.' 11:30:00';
				$last_checkdate = $temp_date.' 12:30:00';
				if(strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][0]) < strtotime($checkdate))
				{
					//���W�N�����d
					
					if(strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1]) < strtotime($last_checkdate))
					{
						//�u�����W�W�Z
						
						$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1])-strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][0]))/(60*60));
						//$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1])-strtotime($temp_date." 07:30:00"))/(60*60));
						
					}
					else
					{
					    
						$ondutytime_check = $temp_date.' 18:15:00';
						if(strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1]) > strtotime($ondutytime_check))
						{
							//���[�Z
							if((strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1])) < strtotime($temp_date." 18:30:00"))
							{
								//���ϰ쬰18:15�U�Z���ҶԧP�_(OT2�p��)
								
								$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($temp_date." 18:30:00")-strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][0]))/(60*60)-1);
								//$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($temp_date." 18:30:00")-strtotime($temp_date." 18:30:00"))/(60*60)-1);
								
							}
							else
							{
								//���ϰ쬰20:30�U�Z���ҶԧP�_(OT4�p��)
								
								$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1])-strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][0]))/(60*60)-1);//�p��o�ӭ��u�b�o�ѤW�Z�X�Ӥp��(��������1�p��)
								//$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1])-strtotime($temp_date." 07:30:00"))/(60*60)-1);//�p��o�ӭ��u�b�o�ѤW�Z�X�Ӥp��(��������1�p��)
							}
						
						
						}
						else
						{
							//�S�[�Z
							$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1])-strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][0]))/(60*60)-1);
							//$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1])-strtotime($temp_date." 07:30:00"))/(60*60)-1);
						}
					}
				}
				else
				{
					//�U�Ȥ~���d
					$ondutytime_check = $temp_date.' 18:15:00';
					if(strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1]) > strtotime($ondutytime_check))
					{
						//���[�Z
						if((strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1])) < strtotime($temp_date." 18:30:00"))
						{
							//���ϰ쬰18:15�U�Z���ҶԧP�_(OT2�p��)
							$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($temp_date." 18:30:00")-strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][0]))/(60*60));
						}
						else
						{
							//���ϰ쬰20:30�U�Z���ҶԧP�_(OT4�p��)
							$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1])-strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][0]))/(60*60));
						}
						
						
					}
					else
					{
						//�S�[�Z
						$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1])-strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][0]))/(60*60));
					}
				}
				
				$emp_attend_new[$emp_id_temp][$temp_date]['today_seconds'] = $emp_attend_new[$emp_id_temp][$temp_date]['work_hours']*60*60;
				$temp=0;
				$temp_date= $attvalue['c_as_date'];
				//$emp_attend_new[$emp_id_temp][$attvalue['c_as_date']][$temp] = substr($attvalue['c_as_time'],0,-4) ;
				$emp_attend_new[$emp_id_temp][$attvalue['c_as_date']]['attend'][$temp] = substr($attvalue['c_as_time'],0,-4) ;			
				$emp_attend_new[$emp_id_temp][$temp_date]['showdate']=$attvalue['c_as_date'];
				$temp++;
			}
			else
			{
				//�P���
				$emp_attend_new[$emp_id_temp][$attvalue['c_as_date']]['attend'][$temp] = substr($attvalue['c_as_time'],0,-4) ;
				$temp++;
			}
		}
			
	}
	/* print_r($emp_attend_new);
	exit; */
	if($emp_attend)
	{
		//������Ƴ̫�@��
		
		$checkdate = $temp_date.' 11:30:00';
		$last_checkdate = $temp_date.' 12:30:00';
		if(strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][0]) < strtotime($checkdate))
		//if(strtotime($temp_date.' 11:30:10') < strtotime($checkdate))
		{
			
			//���W�N�����d
			if(strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1]) < strtotime($last_checkdate))
			{
				
				//�u�����W�W�Z
				$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1])-strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][0]))/(60*60));
				//$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1])-strtotime($temp_date.' 07:30:00'))/(60*60));
			}
			else
			{
				
				$ondutytime_check = $temp_date.' 18:15:00';
				if(strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1]) > strtotime($ondutytime_check))
				{
					//���[�Z		
						
					if((strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1])) < strtotime($temp_date." 18:30:00"))
					{
								
						//���ϰ쬰18:15�U�Z���ҶԧP�_(OT2�p��)
						$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($temp_date." 18:30:00")-strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][0]))/(60*60)-1);
						//$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($temp_date." 18:30:00")-strtotime($temp_date.' 07:30:00'))/(60*60)-1);
					}
					else
					{
								
						//���ϰ쬰20:30�U�Z���ҶԧP�_(OT4�p��)
						$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1])-strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][0]))/(60*60)-1);//�p��o�ӭ��u�b�o�ѤW�Z�X�Ӥp��(��������1�p��)
						//$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1])-strtotime($temp_date.' 07:30:00'))/(60*60)-1);//�p��o�ӭ��u�b�o�ѤW�Z�X�Ӥp��(��������1�p��)
					}
				}
				else
				{
					//�S�[�Z
					//echo 'C2';
					$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1])-strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][0]))/(60*60)-1);
					//$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1])-strtotime($temp_date.' 07:30:00'))/(60*60)-1);
				}
						//$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1])-strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][0]))/(60*60)-1);//�p��o�ӭ��u�b�o�ѤW�Z�X�Ӥp��(��������1�p��)
			}
					
		}
		else
		{
			
			//�U�Ȥ~���d
			$ondutytime_check = $temp_date.' 18:15:00';
			if(strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1]) > strtotime($ondutytime_check))
			{
				//���[�Z	
				if((strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1])) < strtotime($temp_date." 18:30:00"))
				{
					//���ϰ쬰18:15�U�Z���ҶԧP�_(OT2�p��)
					$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($temp_date." 18:30:00")-strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][0]))/(60*60));
				}
				else
				{
					//���ϰ쬰20:30�U�Z���ҶԧP�_(OT4�p��)
					$emp_attend_new[$emp_id_temp][$temp_date]['work_hours'] = floor((strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][sizeof($emp_attend_new[$emp_id_temp][$temp_date]['attend'])-1])-strtotime($emp_attend_new[$emp_id_temp][$temp_date]['attend'][0]))/(60*60));
				}
			}
				
		}
		$emp_attend_new[$emp_id_temp][$temp_date]['today_seconds'] = $emp_attend_new[$emp_id_temp][$temp_date]['work_hours']*60*60;
	}
	/* print_r($emp_attend_new);
	exit;  */
	$t_sql = "  USE NewWIPOne;
				select 
				done.empid,
				step.order_num,
				step.stepid,
				step.step_sec,
				done.qty,
				done.ot_qty,
				done.product_date 
				from dt_workdone done 
				left join dt_orderstep step on done.stepsid = step.sid 
				where done.product_date between '";
	$t_sql .= $date_start;
	$t_sql .= "' and '";
	$t_sql .= $date_end;
	$t_sql .= "' and done.line='";
	$t_sql .= $line;
	$t_sql .= "' order by done.empid,done.product_date,step.order_num,step.stepid";
	$workdone = array();
	//echo $t_sql."<br>";
	$workdone = $excel_dailyoutput->get_wip1data($t_sql);
	//echo $t_sql;
	//print_r($workdone);
	//exit;	
	$tmp_emprowspan=0;
	foreach($emp_attend_new as $empkey => $empvalue)
	{
		//echo $empvalue['emp_code']."<br>";
		foreach($workdone as $donekey => $donevalue)
		{
			
			//echo $donevalue['empid']."<br>";
			if($empvalue['emp_code'] == $donevalue['empid'])
			{
				$emp_attend_new[$empkey][$donevalue['product_date']]['order'][$donevalue['order_num']][$donevalue['stepid']]['sec'] = $donevalue['step_sec'];
				$emp_attend_new[$empkey][$donevalue['product_date']]['order'][$donevalue['order_num']][$donevalue['stepid']]['qty'] = $donevalue['qty'];
				$emp_attend_new[$empkey][$donevalue['product_date']]['order'][$donevalue['order_num']][$donevalue['stepid']]['ot_qty'] = $donevalue['ot_qty'];

			}

		}
	}
	$date_rowspan = 0;
	$tmp_date='';
	$emp_rowspan=0;
	$total_sec=0;
	foreach($emp_attend_new as $newkey => $newvalue)
	{
		foreach($newvalue as $emp_key => $emp_value)
		{
			if($emp_key != 'emp_code')
			{
				if($newvalue[$emp_key]['order'])
				{
					//��order�s�b
					foreach($newvalue[$emp_key]['order'] as $ord_key => $ord_value)
					{
						$emp_attend_new[$newkey][$emp_key]['order'][$ord_key]['rowspan'] = sizeof($ord_value);
						$date_rowspan += sizeof($ord_value);
						$emp_rowspan += sizeof($ord_value);
						foreach($newvalue[$emp_key]['order'][$ord_key] as $stepkey => $stepvalue)
						{
							$total_sec += ($stepvalue['qty']+$stepvalue['ot_qty'])*$stepvalue['sec'];
						}
						
						//��X���q�`���//�q�椣�P�`��ƭn����
						//��X�Ĳv(���q�`���/�W�Z�ɼ�x60x60)
					}
					$emp_attend_new[$newkey][$emp_key]['total_sec'] = $total_sec;
					$emp_attend_new[$newkey][$emp_key]['rowspan'] = $date_rowspan;
					$emp_attend_new[$newkey][$emp_key]['performance'] = round(($emp_attend_new[$newkey][$emp_key]['total_sec']/($emp_attend_new[$newkey][$emp_key]['work_hours']*60*60))* 100) ;
					$date_rowspan =0;
					$total_sec =0 ;
				}
				else
				{
					$emp_rowspan++;
				}
			}										
		}
		$emp_attend_new[$newkey]['rowspan'] =$emp_rowspan;
		$emp_rowspan =0;
	}
}
	//print_r($emp_attend_new);
	//exit;
	$op['emp_detail'] = $emp_attend_new;
//print_r($op);
page_display($op,$AUTH,'excel_dailyoutput_list.html');
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

$attend_detail = $excel_dailyoutput->get_hrdata($t_sql);
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