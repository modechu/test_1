<?php
  session_Start();

 // $A_Power[1][1]='�O';
//  $A_Power[1][2]='';
//  $A_Power[2][2]='�O';


//  $A_Power[2]=$_SESSION['PowerManage'];
//  $A_Power[3]=$_SESSION['PowerControl'];
//  $A_Power[4]=$_SESSION['PowerSales'];
//  $A_Power[5]=$_SESSION['PowerAccount'];
//  $A_Power[6]=$_SESSION['IsAdminister'];

  
  $A_Item[1][0]=array('dir','�򥻸��');
  $A_Item[1][1][0]=array('txt','�Ȥ�','sorry.html','main');
  $A_Item[1][2][0]=array('txt','������','sorry.html','main');
  $A_Item[1][3][0]=array('txt','�D��','sorry.html','main');
  $A_Item[1][4][0]=array('txt','�Ʈ�','sorry.html','main');

  $A_Item[2][0]=array('dir','�˥�');
  $A_Item[2][1][0]=array('txt','�˥��򥻸��','sorry.html','main');
  $A_Item[2][2][0]=array('txt','�˥��ή�','sorry.html','main');
  $A_Item[2][3][0]=array('txt','�˥��ؤo','sorry.html','main');

  $A_Item[3][0]=array('dir','�Ͳ��s�y');
  $A_Item[3][1][0]=array('txt','�s�y�O','sorry.html','main');
  $A_Item[3][2][0]=array('txt','�Ͳ��������','sorry.html','main');
  $A_Item[3][3][0]=array('txt','comments','sorry.html','main');
  $A_Item[3][4][0]=array('txt','�T�{�s�y�O','sorry.html','main');
  $A_Item[3][5][0]=array('txt','�ή�','sorry.html','main');
  $A_Item[3][6][0]=array('txt','����T�{','sorry.html','main');
  $A_Item[3][7][0]=array('txt','�Ͳ��i��','sorry.html','main');

  $A_Item[4][0]=array('dir','�˥�BOM');
  $A_Item[4][1][0]=array('txt','BOM','sorry.html','main');
  $A_Item[4][2][0]=array('txt','�T�{BOM','sorry.html','main');

  $A_Item[5][0]=array('dir','�i�X�f');
  $A_Item[5][1][0]=array('txt','�X�f','sorry.html','main');
  $A_Item[5][2][0]=array('txt','���f','sorry.html','main');
  
  $A_Item[6][0]=array('dir','�t�κ޲z');
  $A_Item[6][1][0]=array('txt','�ϥΪ̳]�w','sorry.html','main');
  $A_Item[6][2][0]=array('txt','�t�ζi�X�O��','sorry.html','main');

  $A_Item[7][0]=array('dir','��Ը��');
  $A_Item[7][1][0]=array('txt','�˥����O','sorry.html','main');
  $A_Item[7][2][0]=array('txt','�ڦ����O','sorry.html','main');
  $A_Item[7][3][0]=array('txt','�جq���O','sorry.html','main');
  $A_Item[7][4][0]=array('txt','�u�`���O','sorry.html','main');
  $A_Item[7][5][0]=array('txt','�ؽX���O','sorry.html','main');
  $A_Item[7][6][0]=array('txt','�������O','templates/dept.html','main');

  $A_Item[8][0]=array('dir','�޲z��T');
  $A_Item[8][1][0]=array('txt','�˥����פ��R','sorry.html','main');
  $A_Item[8][2][0]=array('txt','�˥��Ͳ��έp','sorry.html','main');

  $A_Item[9][0]=array('dir','��F�ư�');
  $A_Item[9][1][0]=array('txt','�~���p����','sorry.html','main');

echo "<br>[ debug ]A_Item) ===>".count($A_Item);
exit;


// �NPOWER�ҳ]���L  
  for ($i=1;$i<=count($A_Item);$i++){
      for ($j=1;$j<=(count($A_Item[$i])-1);$j++)
	  {
	    $A_Power[$i][$j]='';
	  }
  }
//	    $A_Power[1][1]='�O';
	    $A_Power[7][6]='�O';

  
  
  
  
  
?>

<html>

<head>
<meta http-equiv="Content-Language" content="zh-tw">
<meta http-equiv="Content-Type" content="text/html; charset=big5">
<title>�Ÿ�  ������޲z -  �˥����U�t��</title>
<style>
<!--
a            { font-size: 10 pt; font-family: �ө���; color:#666666  text-decoration:none }
a:link       { font-size: 10 pt; font-family: �ө���; color:#666666 }
a:visited    { font-size: 10 pt; font-family: �ө���; color:#0000FF; text-decoration:none }
a:active     { font-size: 10 pt; font-family: �ө���; color:#c54e6b}
a:hover      { font-size: 10 pt; font-family: �ө���; color:#FF0000}
.F1          { font-size: 11 pt; font-family: �ө���; color: #000080 }
.F2          { font-size: 30 pt; font-family: Georgia; color: #7F9AD4; letter-spacing:3pt }
.F3          { font-size:  8 pt; font-family: �ө���; color: #000000 }
.F4          { font-size:  8 pt; font-family: �ө���; color: #FFFFFF }
.F5          { font-size:  9 pt; font-family: �ө���; color: #333333 }
.F6          { font-size:  9 pt; font-family: �ө���; color: #000080 }
.F7          { font-size: 10 pt; font-family: �ө���; color: #999999 }
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
            <a href="Main.php" target="main"><img border="0" src="images/carnival2.gif" width="81" height="62" alt="�^����"></a>
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
      if ($A_Power[$i][$j]=='�O')
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
            <iframe name="main" src="index2.php?PHP_action='login'" marginwidth="0" marginheight="0" height="100%" width="100%" border="0" frameborder="5">�z���s�������䴩���m�ج[�Υثe���]�w������ܤ��m�ج[�C</iframe>
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
  <span class="F3">���q�G106 �x�_�����R���|�q�G�Q�����C��</span>
  <span class="F4">|</span>
  <span class="F3">�q�ܡG(02)2711-3171</span>
  <span class="F4">|</span>
  <span class="F3">�ǯu�G(02)2711-0900</span>
</div>

<table id="W2" border="0" cellspacing="0" cellpadding="0" width="280" style="position: absolute">
  <tr>
    <td width="60%">
      <a href="Logout.php">�n�X</a><span class="F7"> |</span>
      <a href="General/ChangePass/Edit.php" target="main">�K�X�ܧ�</a>
    </td>
  </tr>
</table>

</body>

</html>