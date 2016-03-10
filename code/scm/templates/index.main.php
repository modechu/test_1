{php}
	$A_Item		= $GLOBALS['A_Item'];
	$A_Power	= $GLOBALS['SCACHE']['ADMIN']['perm_dec'];
{/php}
{literal}
<html>
<head>
<meta http-equiv="Content-Language" content="zh-tw">
<meta http-equiv="Content-Type" content="text/html; charset=big5">
<title>CARNIVAL == SCM SYSTEM ==</title>
<link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
<link rel=stylesheet type="text/css" href="./css/bom.css">
<script type="text/javascript" src="./js/open_detial.js"></script>
<script type="text/javascript" src="./js/jquery.min.js"></script>
<style>
<!--
a			{ font-size: 10pt; font-family: Arial,細明體; color:#666666; text-decoration:none }
a:link		{ font-size: 10pt; font-family: Arial,細明體; color:#666666 }
a:visited	{ font-size: 10pt; font-family: Arial,細明體; color:#0000FF; text-decoration:none }
a:active	{ font-size: 10pt; font-family: Arial,細明體; color:#c54e6b}
a:hover		{ font-size: 10pt; font-family: Arial,細明體; color:#FF0000}
.F1			{ font-size: 11pt; font-family: Arial,細明體; color: #000080 }
.F2			{ font-size: 30pt; font-family: Arial,Georgia; color: #7F9AD4; letter-spacing:3pt }
.F3			{ font-size:  8pt; font-family: Arial,細明體; color: #000000 }
.F4			{ font-size:  8pt; font-family: Arial,細明體; color: #FFFFFF }
.F5			{ font-size:  9pt; font-family: Arial,細明體;font-weight: bold; color: #333333;cursor:pointer; }
.F6			{ font-size:  9pt; font-family: Arial,細明體;font-weight: bold; color: #000080; line-height:150%;cursor:pointer; }
.F7			{ font-size: 10pt; font-family: Arial,細明體; color: #999999 }
.mode		{ font-size:10pt;	font-family:"Verdana" , sans-serif;	color:#000080;	padding-bottom:20px; }
-->
</style>
</head>
<body style="background-color:#FFF;overflow-x:hidden;overflow-y:hidden;" scroll="no" topmargin="0" leftmargin="0" OnLoad="RePos()" OnResize="RePos()">
<SCRIPT language=JavaScript>  
<!--
{/literal}
{php}
	$A_Item		= $_SESSION['ITEM']['A_Item'];
	$A_Power	= $_SESSION['USER']['ADMIN']['perm_dec'];
    echo 'var OpenMenu=0;'."\n";
    echo 'var MaxMenu='.count($A_Item).';'."\n";
    echo 'var MaxSubMenu=new Array('.count($A_Item).');'."\n";
    for ($i=1;$i<=count($A_Item);$i++)  {
        echo 'MaxSubMenu['.$i.']='.(count($A_Item[$i])-1).';'."\n";
    }
{/php}

{literal}
function RePos() {
	// document.all['W1'].style.left=180;
	// document.all['W1'].style.top=document.body.clientHeight-134;
	// document.all['W1'].style.left=180;
	// document.all['W1'].style.bottom=8;
    
	document.all['W2'].style.left=document.body.clientWidth-200;
	document.all['W2'].style.top=5;

	//document.all['W3'].style.left=document.body.clientWidth-590;
	//document.all['W3'].style.top=5;

	//document.all['W4'].style.left=document.body.clientWidth-268;
	//document.all['W4'].style.top=24;
}

function ShowMenu(x) {
    $("table[id^='SubMenu']").attr({'style':'display:none;'}); 
    if (x==OpenMenu) {
        OpenMenu=0;
    } else {
        for (j=1;j<=MaxSubMenu[x];j++)	{
            $("table[id^='SubMenu"+x+"-"+j+"']").attr({'style':'display:;'}); 
        }
        OpenMenu=x;
    }
}

function MenuGetFoce1(x) {
    $("span[id='MenuText"+x+"']").attr({'style':'color=#c54e6b'}); 
}

function MenuLoseFoce1(x)	{
    $("span[id='MenuText"+x+"']").attr({'style':'color=#333333'}); 
}

function MenuGetFoce2(x) {
    $("span[id='MenuText"+x+"']").attr({'style':'color=#0000FF'}); 
}

function MenuLoseFoce2(x)	{
    $("span[id='MenuText"+x+"']").attr({'style':'color=#000080'}); 
}

function ShowPage(addr,tar)	{
    window.open2(addr,tar);
}
//-->
var menu_key = 1;
(function($){
	$(document).ready(function(){

        
        $('#menu_action').bind('click', function(){
            var ms = $("#menu_td");
            if($(ms).is(":visible")){
                // $(ms).hide();
                $("#menu_td").hide();
                // $("#note").html('&nbsp;→<br>&nbsp;→<br>&nbsp;→<br>&nbsp;M<br>&nbsp;E<br>&nbsp;N<br>&nbsp;U<br>&nbsp;→<br>&nbsp;→<br>&nbsp;→<br>');
                $("#menu_img").attr({"src":"./images/bullet_toggle_plus.png","title":"ON"});
                menu_key = 1;
                // $(this).removeClass("line_hover_bg");
            }else{
                // $(ms).show();
                $("#menu_td").show();
                // $("#note").html('');
                $("#menu_img").attr({"src":"./images/bullet_toggle_minus.png","title":"OFF"});
                menu_key = 0;
                // $(this).addClass("line_hover_bg");
            }
            // alert(menu_key);
        });
        
        
        $('.TXT_css_2').bind('click', function(){
            $('#div_menu').css({ left : '-200' },0);
            // $("#menu_img").attr({"src":"./images/bullet_toggle_minus.png","title":"OFF"});
        });
        
        $('td[id^=td_hover],#div_menu').hover(function(){
            if ( menu_key > 0 )
            $('#div_menu').css({ left : '0' },0);
        },function(){
            if ( menu_key > 0 )
            $('#div_menu').css({ left : '-200' },0);
            // $("#menu_img").attr({"src":"./images/bullet_toggle_plus.png","title":"OFF"});
        });
	});
})(jQuery);
</SCRIPT>
<div id="div_menu" style="position:absolute;overflow-x:hidden;overflow-y:auto;height:100%;width:200px;z-index:3;left:-200;">
<table border="0" cellspacing="0" cellpadding="0" width="200" height="100%">
    <tr>
        <td id="" width="200" valign="top" style="background-image: url('images/Logo1.gif'); background-repeat: repeat-y">
            <table id="scm_menu" border="0" cellspacing="0" cellpadding="0" width="100%" height="100%">
                <tr height="50">
                    <td width="100%" align="center">
                        <a href='index2.php?PHP_action=main' target='main'><img border=0 src='images/carnival2.gif' width=81 height=62 alt='回首頁'></a>
                    </td>
                </tr>
                <tr>
                    <td align="center" valign="top">
{/literal}

{php}
for ($i=1;$i<=count($A_Item);$i++){
    $m = 0;
    for ($j=1;$j<=(count($A_Item[$i])-1);$j++)if ($A_Power[$_SESSION['ITEM']['A_Item'][$i][$j][0][5]]<>0)$m=$m+1;
   	if (!empty($m)) $mode[$i] = 1;
    $m = '';
}

for ($i=1;$i<=count($A_Item);$i++) {
    if ($A_Item[$i][0][1] && $mode[$i]) {
		echo '                        <table border="0" cellspacing="0" cellpadding="0" width="180" height="24" style="cursor: hand" OnMouseOver="MenuGetFoce1('.$i.')" OnMouseOut="MenuLoseFoce1('.$i.')" onClick="ShowMenu('.$i.')">'."\n";
		echo '                          <tr height="25">'."\n";
		echo '                              <td align="center" width="180" style="background-image: url(\'images/button1.gif?'.$_SESSION['did'].'\'); background-repeat: no-repeat;">'."\n";
		echo '                                  <span id="MenuText'.$i.'" class="TXT_css_1">'.$A_Item[$i][0][1].'</span>'."\n";
		echo '                              </td>'."\n";
		echo '                          </tr>'."\n";
        echo '                        </table>'."\n";
    }
    for ($j=1;$j<=(count($A_Item[$i])-1);$j++) {
        if ( $A_Power[$_SESSION['ITEM']['A_Item'][$i][$j][0][5]]<>0 ) {
            echo '                        <table id="SubMenu'.$i.'-'.$j.'" border="0" cellspacing="0" cellpadding="0" width="180" style="cursor: hand; display: None" OnMouseOver="MenuGetFoce2('.$i.$j.')" OnMouseOut="MenuLoseFoce2('.$i.$j.')" onClick="ShowPage(\''.$A_Item[$i][$j][0][3].'\',\''.$A_Item[$i][$j][0][4].'\')" title="'.$A_Item[$i][$j][0][2].'">'."\n";
            echo '                          <tr>'."\n";
            echo '                              <td align="center">'."\n";
            echo '                                  <span id="MenuText'.$i.$j.'" class="TXT_css_2">'.$A_Item[$i][$j][0][1].'</span>'."\n";
            echo '                              </td>'."\n";
            echo '                          </tr>'."\n";
            echo '                        </table>'."\n";
        } else {
            echo '                        <table id="SubMenu'.$i.'-'.$j.'" border="0" cellspacing="0" cellpadding="0" width="180" style="display: None">'."\n";
            /* 顯示沒有權限的選項開始 */
            // echo '                           <tr>'."\n";
            // echo '                               <td align="center">'."\n";
            // echo '                                   <span class="TXT_css_3" disabled>'.$A_Item[$i][$j][0][1].'</span>'."\n";
            // echo '                               </td>'."\n";
            // echo '                           </tr>'."\n";
            /* 顯示沒有權限的選項結束 */
            echo '                        </table>'."\n";
        }
    }
}
{/php}

{literal}
                    </td>
                </tr>
				<tr>
					<td align="center" valign="top">{/literal} {$sys_admin} {literal}</td>
				</tr>
            </table>
        </td>
    </tr>
</table>
</div>
<table border="0" cellspacing="0" cellpadding="0" width="100%" height="100%">
    <tr>
        <td id="menu_td" width="200" valign="top" style="background-image: url('images/Logo1.gif'); background-repeat: repeat-y">
            <table id="scm_menu" border="0" cellspacing="0" cellpadding="0" width="100%" height="100%">
                <tr height="50">
                    <td width="100%" align="center">
                        <a href='index2.php?PHP_action=main' target='main'><img border=0 src='images/carnival2.gif' width=81 height=62 alt='回首頁'></a>
                    </td>
                </tr>
                <tr>
                    <td align="center" valign="top">
{/literal}

{php}
for ($i=1;$i<=count($A_Item);$i++){
    $m = 0;
    for ($j=1;$j<=(count($A_Item[$i])-1);$j++)if ($A_Power[$_SESSION['ITEM']['A_Item'][$i][$j][0][5]]<>0)$m=$m+1;
   	if (!empty($m)) $mode[$i] = 1;
    $m = '';
}

for ($i=1;$i<=count($A_Item);$i++) {
    if ($A_Item[$i][0][1] && $mode[$i]) {
		echo '                        <table border="0" cellspacing="0" cellpadding="0" width="180" height="24" style="cursor: hand" OnMouseOver="MenuGetFoce1('.$i.')" OnMouseOut="MenuLoseFoce1('.$i.')" onClick="ShowMenu('.$i.')">'."\n";
		echo '                          <tr height="25">'."\n";
		echo '                              <td align="center" width="180" style="background-image: url(\'images/button1.gif?'.$_SESSION['did'].'\'); background-repeat: no-repeat;">'."\n";
		echo '                                  <span id="MenuText'.$i.'" class="TXT_css_1">'.$A_Item[$i][0][1].'</span>'."\n";
		echo '                              </td>'."\n";
		echo '                          </tr>'."\n";
        echo '                        </table>'."\n";
    }
    for ($j=1;$j<=(count($A_Item[$i])-1);$j++) {
        if ( $A_Power[$_SESSION['ITEM']['A_Item'][$i][$j][0][5]]<>0 ) {
            echo '                        <table id="SubMenu'.$i.'-'.$j.'" border="0" cellspacing="0" cellpadding="0" width="180" style="cursor: hand; display: None" OnMouseOver="MenuGetFoce2('.$i.$j.')" OnMouseOut="MenuLoseFoce2('.$i.$j.')" onClick="ShowPage(\''.$A_Item[$i][$j][0][3].'\',\''.$A_Item[$i][$j][0][4].'\')" title="'.$A_Item[$i][$j][0][2].'">'."\n";
            echo '                          <tr>'."\n";
            echo '                              <td align="center">'."\n";
            echo '                                  <span id="MenuText'.$i.$j.'" class="TXT_css_2">'.$A_Item[$i][$j][0][1].'</span>'."\n";
            echo '                              </td>'."\n";
            echo '                          </tr>'."\n";
            echo '                        </table>'."\n";
        } else {
            echo '                        <table id="SubMenu'.$i.'-'.$j.'" border="0" cellspacing="0" cellpadding="0" width="180" style="display: None">'."\n";
            /* 顯示沒有權限的選項開始 */
            // echo '                           <tr>'."\n";
            // echo '                               <td align="center">'."\n";
            // echo '                                   <span class="TXT_css_3" disabled>'.$A_Item[$i][$j][0][1].'</span>'."\n";
            // echo '                               </td>'."\n";
            // echo '                           </tr>'."\n";
            /* 顯示沒有權限的選項結束 */
            echo '                        </table>'."\n";
        }
    }
}
{/php}

{literal}
                    </td>
                </tr>
				<tr>
					<td align="center" valign="top">{/literal} {$sys_admin} {literal}</td>
				</tr>
            </table>
        </td>
        <td>
            <table border="0" cellspacing="0" cellpadding="0" width="100%" height="100%">
<!-- top frame -->
                <tr height="50">
                    <td width="20" style="background-image: url('images/Logo9.gif'); background-repeat: no-repeat;background-position: right;">&nbsp;</td>
                    <td>
                        <table border="0" cellspacing="0" cellpadding="0" width="100%" height="100%">
                            <tr>
                                <td align="left" width="280" style="background-image: url('images/Logo4.gif'); background-repeat: no-repeat"><div id="menu_action"><img id="menu_img" src="images/bullet_toggle_minus.png" title="OFF" style="cursor: hand;"></div>&nbsp;</td>
                                <td style="background-image: url('images/Logo5.gif'); background-repeat: repeat-x" class="mode" nowrap><font color="red">Mode 練習場</font> USER NAME：{/literal}{php}echo $GLOBALS['SCACHE']['ADMIN']['name'].' ( '.$GLOBALS['SCACHE']['ADMIN']['dept'].' )';{/php}{literal}</td>
                                <td width="135" style="background-image: url('images/Logo6.gif'); background-repeat: no-repeat">&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                    <td width="20" style="background-image: url('images/Logo91.gif'); background-repeat: no-repeat;background-position: left;">&nbsp;</td>
                </tr>
<!-- end top frame -->
                <tr>
<!-- left frame -->
                    <td width="20" id="td_hover">
                        <table border="0" cellspacing="0" cellpadding="0" width="100%" height="100%">
                            <tr>
                                <td width="100%" height="100%" style="background-image: url('images/Logo7.gif'); background-repeat: repeat-y;background-position: right;">&nbsp;→<br>&nbsp;→<br>&nbsp;→<br>&nbsp;M<br>&nbsp;E<br>&nbsp;N<br>&nbsp;U<br>&nbsp;→<br>&nbsp;→<br>&nbsp;→<br>&nbsp;</td>
                            </tr>
                        </table>
                    </td>
<!-- end left frame --> 
<!-- main frame -->
                    <td>
                        <iframe name="main" src="index2.php?PHP_action=main" marginwidth="0" marginheight="0" height="100%" width="100%" border="0" frameborder="5">您的瀏覽器不支援內置框架或目前的設定為不顯示內置框架。</iframe>
                    </td>
<!-- end main frame --> 

<!-- right frame -->
                    <td width="20">
                        <table border="0" cellspacing="0" cellpadding="0" width="100%" height="100%">
                            <tr>
                                <td width="100%" style="background-image: url('images/Logo8.gif'); background-repeat: repeat-y;background-position: left;">&nbsp;</td>
                            </tr>
                        </table>				
                    </td>
<!-- end right frame -->
                </tr>
                <tr height="10">
                    <td width="20">
                        <table border="0" cellspacing="0" cellpadding="0" width="100%">
                            <tr>
                                <td width="100%" style="background-image: url('images/Logo7.gif'); background-repeat: repeat-y;background-position: right;">&nbsp;</td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table border="0" cellspacing="0" cellpadding="0" width="100%" style="vertical-align:middle;">
                            <tr style="background-image: url('images/Logo3.gif'); background-repeat: repeat-x;">
                                            <td nowrap>&nbsp;
                                                <span class="F3">Address：(106) 7th Floor, 25 Jen Ai Rd., Sec.4, Taipei, Taiwan</span>
                                                <span class="F4">|</span>
                                                <span class="F3">Tel：(02)2711-3171</span>
                                                <span class="F4">|</span>
                                                <span class="F3">Fax：(02)2711-0900</span>
                                            </td>
                            </tr>
                        </table>				
                    </td>		
                    <td width="20">
                        <table border="0" cellspacing="0" cellpadding="0" width="100%">
                            <tr>
                                <td width="100%" style="background-image: url('images/Logo8.gif'); background-repeat: repeat-y;background-position: left;">&nbsp;</td>
                            </tr>
                        </table>				
                    </td>		
                </tr>
            </table>
        </td>
    </tr>
</table>

<!-- <table id="W1" border="0" cellspacing="0" cellpadding="0" style="position: absolute">
    <tr>
        <td>
            <span class="F3">Address：(106) 7th Floor, 25 Jen Ai Rd., Sec.4, Taipei, Taiwan</span>
            <span class="F4">|</span>
            <span class="F3">Tel：(02)2711-3171</span>
            <span class="F4">|</span>
            <span class="F3">Fax：(02)2711-0900</span>
        </td>
    </tr>
</table> -->

<table id="W2" border="0" cellspacing="0" cellpadding="0" style="position: absolute">
    <tr>
        <td><span class="F5">
                <a href="index2.php?PHP_action=logout">logout</a> | <a href="index2.php?PHP_action=changePass">change password</a>
            </span>
        </td>
    </tr>
</table>

</body>

</html>

{/literal}