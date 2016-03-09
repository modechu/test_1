<?php
  session_Start();

 // $A_Power[1][1]='是';
//  $A_Power[1][2]='';
//  $A_Power[2][2]='是';


//  $A_Power[2]=$_SESSION['PowerManage'];
//  $A_Power[3]=$_SESSION['PowerControl'];
//  $A_Power[4]=$_SESSION['PowerSales'];
//  $A_Power[5]=$_SESSION['PowerAccount'];
//  $A_Power[6]=$_SESSION['IsAdminister'];

  
  $A_Item[1][0]=array('dir','基本資料');
  $A_Item[1][1][0]=array('txt','客戶','sorry.html','main');
  $A_Item[1][2][0]=array('txt','供應商','sorry.html','main');
  $A_Item[1][3][0]=array('txt','主料','sorry.html','main');
  $A_Item[1][4][0]=array('txt','副料','sorry.html','main');

  $A_Item[2][0]=array('dir','樣本');
  $A_Item[2][1][0]=array('txt','樣本基本資料','sorry.html','main');
  $A_Item[2][2][0]=array('txt','樣本用料','sorry.html','main');
  $A_Item[2][3][0]=array('txt','樣本尺寸','sorry.html','main');

  $A_Item[3][0]=array('dir','生產製造');
  $A_Item[3][1][0]=array('txt','製造令','sorry.html','main');
  $A_Item[3][2][0]=array('txt','生產說明文件','sorry.html','main');
  $A_Item[3][3][0]=array('txt','comments','sorry.html','main');
  $A_Item[3][4][0]=array('txt','確認製造令','sorry.html','main');
  $A_Item[3][5][0]=array('txt','用料','sorry.html','main');
  $A_Item[3][6][0]=array('txt','交期確認','sorry.html','main');
  $A_Item[3][7][0]=array('txt','生產進度','sorry.html','main');

  $A_Item[4][0]=array('dir','樣本BOM');
  $A_Item[4][1][0]=array('txt','BOM','sorry.html','main');
  $A_Item[4][2][0]=array('txt','確認BOM','sorry.html','main');

  $A_Item[5][0]=array('dir','進出貨');
  $A_Item[5][1][0]=array('txt','出貨','sorry.html','main');
  $A_Item[5][2][0]=array('txt','收貨','sorry.html','main');
  
  $A_Item[6][0]=array('dir','系統管理');
  $A_Item[6][1][0]=array('txt','使用者設定','sorry.html','main');
  $A_Item[6][2][0]=array('txt','系統進出記錄','sorry.html','main');

  $A_Item[7][0]=array('dir','後勤資料');
  $A_Item[7][1][0]=array('txt','樣本類別','sorry.html','main');
  $A_Item[7][2][0]=array('txt','款式類別','sorry.html','main');
  $A_Item[7][3][0]=array('txt','尺段類別','sorry.html','main');
  $A_Item[7][4][0]=array('txt','季節類別','sorry.html','main');
  $A_Item[7][5][0]=array('txt','尺碼類別','sorry.html','main');
  $A_Item[7][6][0]=array('txt','部門類別','templates/dept.html','main');

  $A_Item[8][0]=array('dir','管理資訊');
  $A_Item[8][1][0]=array('txt','樣本結案分析','sorry.html','main');
  $A_Item[8][2][0]=array('txt','樣本生產統計','sorry.html','main');

  $A_Item[9][0]=array('dir','行政事務');
  $A_Item[9][1][0]=array('txt','業務聯絡單','sorry.html','main');

echo "<br>[ debug ]A_Item) ===>".count($A_Item);
exit;


// 將POWER皆設為無  
  for ($i=1;$i<=count($A_Item);$i++){
      for ($j=1;$j<=(count($A_Item[$i])-1);$j++)
	  {
	    $A_Power[$i][$j]='';
	  }
  }
//	    $A_Power[1][1]='是';
	    $A_Power[7][6]='是';

  
  
  
  
  
?>

<html>

<head>
<meta http-equiv="Content-Language" content="zh-tw">
<meta http-equiv="Content-Type" content="text/html; charset=big5">
<title>嘉裕  供應鍊管理 -  樣本輔助系統</title>
<style>
<!--
a            { font-size: 10 pt; font-family: 細明體; color:#666666  text-decoration:none }
a:link       { font-size: 10 pt; font-family: 細明體; color:#666666 }
a:visited    { font-size: 10 pt; font-family: 細明體; color:#0000FF; text-decoration:none }
a:active     { font-size: 10 pt; font-family: 細明體; color:#c54e6b}
a:hover      { font-size: 10 pt; font-family: 細明體; color:#FF0000}
.F1          { font-size: 11 pt; font-family: 細明體; color: #000080 }
.F2          { font-size: 30 pt; font-family: Georgia; color: #7F9AD4; letter-spacing:3pt }
.F3          { font-size:  8 pt; font-family: 細明體; color: #000000 }
.F4          { font-size:  8 pt; font-family: 細明體; color: #FFFFFF }
.F5          { font-size:  9 pt; font-family: 細明體; color: #333333 }
.F6          { font-size:  9 pt; font-family: 細明體; color: #000080 }
.F7          { font-size: 10 pt; font-family: 細明體; color: #999999 }
-->
</style>
</head>

<body scroll="no" topmargin="0" leftmargin="0" OnLoad="RePos()" OnResize="RePos()">
<SCRIPT language=JavaScript>  
<!--

<?php

  echo 'var OpenMenu=0;'."\n";
  echo 'var MaxMenu='.count($A_Item).';'."\n";
  echo 'var MaxSubMenu=new Array('.count($A_Item).');'."\n";
  for ($i=1;$i<=count($A_Item);$i++)  {
    echo 'MaxSubMenu['.$i.']='.(count($A_Item[$i])-1).';'."\n";
  }
?>

function RePos()	{
  document.all['W1'].style.left=document.body.clientWidth-600;
  document.all['W1'].style.top=document.body.clientHeight-20;
  document.all['W2'].style.left=document.body.clientWidth-120;
  document.all['W2'].style.top=15;
}

function ShowMenu(x)	 {
  for (i=1;i<=MaxMenu;i++)  {
    for (j=1;j<=MaxSubMenu[i];j++)    {
      document.all['SubMenu'+i+j].style.display="None";
    }
  }
  if (x==OpenMenu)  {
    OpenMenu=0;
  }  else  {
    for (j=1;j<=MaxSubMenu[x];j++)    {
      document.all['SubMenu'+x+j].style.display="";
	}
    OpenMenu=x;
  }
}

function MenuGetFoce1(x) {
  document.all['MenuText'+x].style.color="#c54e6b";
}

function MenuLoseFoce1(x)	{
  document.all['MenuText'+x].style.color="#333333";
}

function MenuGetFoce2(x) {
  document.all['MenuText'+x].style.color="#0000FF";
}

function MenuLoseFoce2(x)	{
  document.all['MenuText'+x].style.color="#000080";
}

function ShowPage(addr,tar)	{
  window.open(addr,tar)
}
//-->
</SCRIPT>

<table border="0" cellspacing="0" cellpadding="0" width="100%" height="100%">
  <tr>
    <td width="100" style="background-image: url('images/Logo1.gif'); background-repeat: repeat-y">
      <table border="0" cellspacing="0" cellpadding="0" width="100%" height="100%">
        <tr height="50">
          <td width="100%" align="center">
            <a href="Main.php" target="main"><img border="0" src="images/carnival2.gif" width="81" height="62" alt="回首頁"></a>
          </td>
        </tr>


		<tr>
          <td align="center" valign="top">
<?php
  for ($i=1;$i<=count($A_Item);$i++)
  {
    echo '            <table border="0" cellspacing="0" cellpadding="0" width="76" height="24" style="cursor: hand" OnMouseOver="MenuGetFoce1('.$i.')" OnMouseOut="MenuLoseFoce1('.$i.')" onClick="ShowMenu('.$i.')">'."\n";
    echo '              <tr height="25">'."\n";
    echo '                <td align="center" style="background-image: url(\'images/button1.gif\'); background-repeat: no-repeat">'."\n";
    echo '                  <span id="MenuText'.$i.'" class="F5">'.$A_Item[$i][0][1].'</span>'."\n";
    echo '                </td>'."\n";
    echo '              </tr>'."\n";
    echo '            </table>'."\n";
    for ($j=1;$j<=(count($A_Item[$i])-1);$j++)
    {
      if ($A_Power[$i][$j]=='是')
      {
        echo '            <table id="SubMenu'.$i.$j.'" border="0" cellspacing="0" cellpadding="0" width="76" style="cursor: hand; display: None" OnMouseOver="MenuGetFoce2('.$i.$j.')" OnMouseOut="MenuLoseFoce2('.$i.$j.')" onClick="ShowPage(\''.$A_Item[$i][$j][0][2].'\',\''.$A_Item[$i][$j][0][3].'\')">'."\n";
        echo '              <tr>'."\n";
        echo '                <td align="center">'."\n";
        echo '                  <span id="MenuText'.$i.$j.'" class="F6">'.$A_Item[$i][$j][0][1].'</span>'."\n";
        echo '                </td>'."\n";
        echo '              </tr>'."\n";
        echo '            </table>'."\n";
      }
      else
      {
        echo '            <table id="SubMenu'.$i.$j.'" border="0" cellspacing="0" cellpadding="0" width="76" style="display: None">'."\n";
        echo '              <tr>'."\n";
        echo '                <td align="center">'."\n";
        echo '                  <span class="F6" disabled>'.$A_Item[$i][$j][0][1].'</span>'."\n";
        echo '                </td>'."\n";
        echo '              </tr>'."\n";
        echo '            </table>'."\n";
      }
    }
  }
?>
          </td>
        </tr>
      </table>
    </td>
    <td>
      <table border="0" cellspacing="0" cellpadding="0" width="100%" height="100%">
<!-- top frame -->
		<tr height="50">
          <td width="30" style="background-image: url('images/Logo9.gif'); background-repeat: no-repeat">&nbsp;</td>
          <td>
            <table border="0" cellspacing="0" cellpadding="0" width="100%" height="100%">
              <tr>
                <td width="280" style="background-image: url('images/Logo4.gif'); background-repeat: no-repeat">&nbsp;</td>
                <td style="background-image: url('images/Logo5.gif'); background-repeat: repeat-x">&nbsp;</td>
                <td width="135" style="background-image: url('images/Logo6.gif'); background-repeat: no-repeat">&nbsp;</td>
              </tr>
            </table>
          </td>
          <td width="30" style="background-image: url('images/Logo91.gif'); background-repeat: no-repeat">&nbsp;</td>
        </tr>
<!-- end top frame -->
		<tr>
<!-- left frame -->
          <td width="30">
			<table border="0" cellspacing="0" cellpadding="0" width="100%" height="100%">
              <tr>
                <td width="100%" style="background-image: url('images/Logo7.gif'); background-repeat: repeat-y">&nbsp;</td>
              </tr>
            </table>
		  </td>
<!-- end left frame --> 
<!-- main frame -->
          <td>
            <iframe name="main" src="index2.php?PHP_action='login'" marginwidth="0" marginheight="0" height="100%" width="100%" border="0" frameborder="5">您的瀏覽器不支援內置框架或目前的設定為不顯示內置框架。</iframe>
          </td>
<!-- end main frame --> 

<!-- right frame -->
          <td width="30">
            <table border="0" cellspacing="0" cellpadding="0" width="100%" height="100%">
              <tr>
                <td width="100%" style="background-image: url('images/Logo8.gif'); background-repeat: repeat-y">&nbsp;</td>
              </tr>
            </table>				
		  </td>
<!-- end right frame -->
		</tr>
		<tr height="30">
			<td width="30">
				<table border="0" cellspacing="0" cellpadding="0" width="100%" height="100%">
				  <tr>
					<td width="100%" style="background-image: url('images/Logo7.gif'); background-repeat: repeat-y">&nbsp;</td>
				  </tr>
				</table>
			</td>
			<td>
				<table border="0" cellspacing="0" cellpadding="0" width="100%" height="100%">
				  <tr>
					<td width="100%" style="background-image: url('images/Logo3.gif'); background-repeat: repeat-x">&nbsp;</td>
				  </tr>
				</table>				
			</td>		
			<td width="30">
				<table border="0" cellspacing="0" cellpadding="0" width="100%" height="100%">
				  <tr>
					<td width="100%" style="background-image: url('images/Logo8.gif'); background-repeat: repeat-y">&nbsp;</td>
				  </tr>
				</table>				
			</td>		
		</tr>
	  </table>
    </td>
  </tr>
</table>

<div id="W1" style="position: absolute">
  <span class="F3">公司：106 台北市仁愛路四段二十五號七樓</span>
  <span class="F4">|</span>
  <span class="F3">電話：(02)2711-3171</span>
  <span class="F4">|</span>
  <span class="F3">傳真：(02)2711-0900</span>
</div>

<table id="W2" border="0" cellspacing="0" cellpadding="0" width="280" style="position: absolute">
  <tr>
    <td width="60%">
      <a href="Logout.php">登出</a><span class="F7"> |</span>
      <a href="General/ChangePass/Edit.php" target="main">密碼變更</a>
    </td>
  </tr>
</table>

</body>

</html>