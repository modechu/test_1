
<!--
/*
(1)���v�n��:�}��
(2)�̦n�bHTTP���Y�̼g�W�r��.f10�˦���(CSS)�p�k��g�k----->.f10{ font-family: Arial; font-size: 10pt }
   �H�K�r���Ӥj,��M�A�]�i�H�ۭq�r���˦���.
(3)���U��JavaScript�Y�Ǧ�᭱���g����"<�Ѽ�>"��,��ܥi�H�ܰʪ��ƭȩΤ�r.
(4)Cal(RegFrm.T3,...)��Ƹ̩Ҽg�����O
   "RegFrm"-->�O���W��(�p�W���Ҽg��<form method="POST" name="RegFrm" action="">),
   "T3"------>�O���̨䤤�Ӥ���W��(�p�W���Ҽg��<input type="text" name="T3" size="20">)
*/
//====================����HTML===========================================================================
document.writeln("  <table id=TabCal borderColor='#0000ff' border='1' cellspacing='1' width='195' bgcolor='#ffffe0' style='position:absolute;display:none;z-index:100' onmouseover='StatusDisplay(1)' onmouseout='StatusDisplay(0)'><form name='YType'>");
document.writeln("    <tr>");
document.writeln("      <td colspan='7' width='185' align='center' onmousedown='EveDm()' onmouseup='EveUp()' onmousemove='EveMv()' style='cursor:move'>");
document.writeln("        <span class='f10' id=StrCap style='filter:glow(color=#555555,strength=5);height:5px; color:white'></span>");
document.writeln("      </td>");
document.writeln("    </tr>");
document.writeln("   <tr>");
document.writeln("      <td colspan='7' width='185' align='center'>");
document.writeln("           <img src='images/prev2.gif' onmousedown='AddSub(-10)' style='cursor:hand' width='16' height='15' align='middle'> <img style='cursor:hand' onmousedown='AddSub(-1)' src='images/prev.gif' width='16' height='15' align='middle'>");
document.writeln("            <select align='top' size='1' name='SeN' style='font-size: 12px' onChange='YChange()'>");
document.writeln("            <option value='1'>---</option>");
document.writeln("            <option value='2'>A.D.</option>");
document.writeln("            </select><span id=YrNum align='middle' class='f10'></span><span align='middle' class='f10'><b>(Y)</b></span><img style='cursor:hand' onmousedown='AddSub(1)' src='images/next.gif' width='16' height='15' align='middle'> <img src='images/next2.gif' onmousedown='AddSub(10)' style='cursor:hand' width='16' height='15' align='middle'>");
document.writeln("      </td>");
document.writeln("    </tr>");
document.writeln("    <tr>");
document.writeln("      <td colspan='7' width='185' align='center'>");
document.writeln("        <p align='center'>");
document.writeln("          <span style='cursor:hand' class='f10' onmousedown='ListHelp()'>[ <span onmouseover='ChangeCalColor(this,0,1),ChangeCalColor(this,1,2)' onmouseout='ChangeCalColor(this,0,5),ChangeCalColor(this,1,3)'>Desc.</span> ]</span>");
document.writeln("        <img style='cursor:hand' onmousedown='mAddSub(-1)' src='images/prev.gif' width='16' height='15' align='middle'> <span class='f9'><span id=MnNum></span><b>(M)</b></span> <img style='cursor:hand' onmousedown='mAddSub(1)' src='images/next.gif' width='16' height='15' align='middle'>");
document.writeln("          <span style='cursor:hand' class='f10' onmousedown='ListSeOut()'>[ <span onmouseover='ChangeCalColor(this,0,4),ChangeCalColor(this,1,2)' onmouseout='ChangeCalColor(this,0,5),ChangeCalColor(this,1,3)'>Close</span> ]</span>");                                                                                                                                                                                                                                              
document.writeln("        </p>");
document.writeln("      </td>");
document.writeln("    </tr>");
document.writeln("    <tr align='center' class='f10'>");
document.writeln("      <td bgcolor='#ffe0e0'>Sun</td>");
document.writeln("      <td>Mon</td>");
document.writeln("      <td>Tue</td>");
document.writeln("      <td>Wed</td>");
document.writeln("      <td>Thu</td>");
document.writeln("      <td>Fri</td>");
document.writeln("      <td bgcolor='#e0ffff'>Sat</td>");
document.writeln("    </tr>");
var cont_cals_ij = 0;
for(i=1;i<7;i++)
{ document.writeln("    <tr align='center' class='f10'>");
  for(j=1;j<8;j++)
  { cont_cals_ij++;
    switch (j)
    { case 1:
        document.writeln("      <td bgcolor='#ffe0e0'");
        break;
      case 7:
        document.writeln("      <td bgcolor='#e0ffff'");
        break;
      default:
        document.writeln("      <td                  ");
    }
    document.write(" id=s" + cont_cals_ij + " onmouseover='inMou(event.srcElement.id)' onmouseout='outMou(event.srcElement.id)' onmousedown='ClickSel()'></td>");
  }
  document.writeln("    </tr>");
}
document.writeln("    </form>");
document.writeln("  </table>");
document.writeln("  <table id=LiHp bgcolor='#30ffa4' align=center width=314 CELLPADDING=0 CELLSPACING=0 BORDER=0 style='position:absolute;display:none;z-index:2' height='98'><tr><td aling='left'>");
document.writeln("      <span class='f10'>[1] At year table click</span><img align='bottom' src='prev2.gif' width='10' height='10'><span class='f10'>or</span><img align='bottom' src='images/next2.gif' width='10' height='10'><span class='f10'>icon will add or Subtract���� year�D</span><br>");
document.writeln("      <span class='f10'>[2] At year table click</span><img align='bottom' src='prev.gif' width='10' height='10'><span class='f10'>or</span><img align='bottom' src='images/next.gif' width='10' height='10'><span class='f10'>icon will add or subtract 1 year�D</span><br>");
document.writeln("      <span class='f10'>[3] At month table click</span><img align='bottom' src='prev.gif' width='10' height='10'><span class='f10'>or</span><img align='bottom' src='images/next.gif' width='10' height='10'><span class='f10'>icon will add or subtrack 1 month�D</span><br>");  
document.writeln("      <span class='f10'>[4] At day frame move mouse to click will output that date�D<br>&nbsp;&nbsp;&nbsp;&nbsp;");
document.writeln("       (calendar will colse automatically)�D</span>&nbsp;<span class='f10'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
document.writeln("       [</span><span style='cursor:hand;background:white' class='f10' onmousedown='LiHpClose()' onmouseover='ChangeCalColor(this,0,4) ,ChangeCalColor(this,1,2)' onmouseout='ChangeCalColor(this,0,2) ,ChangeCalColor(this,1,3)'>Close</span><span class='f10'>]</span>");
document.writeln("      </td></tr>");
document.writeln("  </table>");

function ChangeCalColor(obj,Bg_Ft,nm)
{ if (Bg_Ft==0)
  { switch (nm)
    { case 1:
        obj.style.background="green";
        break;    
      case 2:
        obj.style.background="white";
        break;    
      case 3:
        obj.style.background="black";
        break;    
      case 4:
        obj.style.background="red";
        break;    
      default:
        obj.style.background="#ffffe0";
    }
  }
  else
  { switch (nm)
    { case 1:
        obj.style.color="green";
        break;    
      case 2:
        obj.style.color="white";
        break;    
      case 3:
        obj.style.color="black";
        break;    
      case 4:
        obj.style.color="red";
        break;    
      default:
        obj.style.color="#ffffe0"
    }  
  }
}
//====================����{��==========================================================================
var DefautStrCt = "calendar";     //�w�]���D.---------------------------------------------<�Ѽ�>
var StrCt;
var curCX;
var YearNumber;
var ListTop;                 
var NowDate = new Date();//---------------------------------------------------------------------<�Ѽ�>
/*
�q�X�������I,new Date()=�H�{�b���������I.
�Y�H��L���������I�i��g��new Date("yyyy/mm/dd").
*/
var nStaShelter; 
var bForBack;
var OutType;
var isDg = 0;
var nYearTypeNn = 0;
var oSeleOject;
var forw;
var bacw;
var TabId=document.getElementById("TabCal");
var IDCopy = "��"
var dStartPointDay;
var sYMDSel = toString();
var CloseDisplayTime = 18000;//������ܮɶ�(���:��)------------------------------------------------<�Ѽ�>
var mCloseDisplayTime = CloseDisplayTime*10000;
var CloseTabCalTimekeeperPro; //������ܭp�ɾ��{��
function Cal(SelectObjectEle,OutTp,pForw,pBacw,nSS,FB,CaptionName) //-----------------------------------------<�Ѽ�>
/*
==SelectObjectEle,OutTp,Forw,Bacw,nSS,FB �U�ѼƷN�q����=====================================
(1)***SelectObjectEle***
���w��X����,Ĵ�p�W�CSelectObjectEle<=RegFrm.T2),��oSeleOject����ѷӬ�RegFrm.T2.

(2)***OutTp*** 
��X����Φ� 1=>yyyy/mm/dd,0=>�褸(�Υ���)�~���.

(3)***Forw,Bacw***
Forw=>�V��~�p�ƺI��~.
Bacw=>�V�e�~�p�ƺI��~.
�`�N:Forw��Bacw���ȥ����O�j�󵥩�0����ƭ�.

(4)***nSS*** 
�q�X�������I(=NowDate)���B���I,�Y�H�������I���Q�鬰�B���I,�h��g��nSS = -1.
�Y�H�������I�����鬰�B���I,�h��g��nSS = 0.nSS����ƭȽd��-998 ~ +998.
��nSS>=999��nSS<=-999�h�L�B��,�J�����}��.
�ҿת��B��,�����O��A�I��Y���,�L�k�N�����X�ܫ��w����椸���.�]�J�L�@��.

(5)***FB*** 
�V�e�ΦV��B��,1=>�V�e��B��,0=>�V���B��.��nSS>=999��nSS<=-999�h�L�@��.

(6)***CaptionName*** 
���D�W��.�Y���Ŷ��X�h���w�]���D
============================================================================================
*/
{ var Yget=NowDate.getFullYear();
  var Mget=NowDate.getMonth();
  oSeleOject=new Object(SelectObjectEle);//--(��1)
  OutType = OutTp;//-------------------------(��2)
  forw = NowDate.getFullYear() + pForw;//----(��3)
  bacw = NowDate.getFullYear() - pBacw;//----(��4)
  nStaShelter = nSS;//-----------------------(��5)
  bForBack = FB;//---------------------------(��6)
  if(CaptionName==""){StrCt=DefautStrCt;}//--(��7)
  else{StrCt=CaptionName;}
  dStartPointDay = NowDate.getTime() + 864000000*nStaShelter;
  YearNumber=Yget;
  TabId.style.display="none";
  ListPri(Yget,Mget+1);
  TabId.style.left= document.body.scrollLeft+event.clientX;
  TabId.style.top = document.body.scrollTop+event.clientY;
  
  document.YType.SeN.options[1].selected = true;
  document.getElementById("YrNum").innerText=Yget;
  document.getElementById("MnNum").innerText=Mget+1;
  document.getElementById("StrCap").innerText=StrCt;
  TabId.style.display="block";
  CloseTabCalTimekeeperPro=setTimeout("TabId.style.display='none'",50000);
}

function ListSeIn()
{ TabId.style.display="block";
}

function ListSeOut()
{ if(document.getElementById("LiHp").style.display=="block"){LiHpClose();}
  TabId.style.display="none";
}

function ClickSel()
{ if(document.getElementById("LiHp").style.display=="block"){LiHpClose();}
  var IDCo = document.getElementById(IDCopy).innerText;
  var hBGround = document.getElementById(IDCopy).style.background;
  var bCanPut = 1;
  if(Math.abs(nStaShelter)<999 && !(IDCo=="��"))
  { var DateN = new Date(sYMDSel + IDCo);
    if(bForBack==1){if(dStartPointDay >= DateN.getTime()){bCanPut = 0;}}
    else{if(dStartPointDay <= DateN.getTime()){bCanPut = 0;}}
  }
  if (!(IDCo == "��") && bCanPut == 1)
  { var PrintStrin= document.getElementById("YrNum").innerText;
    var InPri;
    if(document.YType.SeN.value==1)
    { var nYearC = PrintStrin
      nYearTypeNn=1;
      if (PrintStrin <= 1)
      { PrintStrin -= 1;
        if (PrintStrin==0)
        { sYearTp='���ꤸ';
          PrintStrin="";
        }
        else
        { sYearTp='����e';
          PrintStrin=Math.abs(PrintStrin);
        }
      }
      else
      { sYearTp='����';
      }
      InPri = 1911 + eval(nYearC);
    }
    else
    { nYearTypeNn=0;
      sYearTp='C.D.';
      InPri=PrintStrin;
    }
    PrintStrin += "Y.";
    InPri += "-";
    PrintStrin += document.getElementById("MnNum").innerText;
    var M= document.getElementById("MnNum").innerText;
    if (M<10) M="0"+M;
    if (IDCo<10) IDCo="0"+IDCo;
    InPri += M;

    PrintStrin += "M.";
    InPri += "-";
    PrintStrin += IDCo + "D.";
    InPri += IDCo;
    if ( OutType == 1 )
    { oSeleOject.value = InPri;
    }
    else
    { oSeleOject.value = sYearTp+PrintStrin;
    }
    TabId.style.display="none";
  }
}

function ListPri(YP,MP)
{ var ss;
  var Num_i=1;
  var MonD=0;
  var Str_YMD=YP + "/" + MP + "/";
  var TheDate=new Date(Str_YMD+"1");
  var WeekFirst = TheDate.getDay();
  var tfYMD=0;
  sYMDSel = Str_YMD;
  if(TheDate.getFullYear()==NowDate.getFullYear()  && TheDate.getMonth()==NowDate.getMonth()){tfYMD=1;}
  for(i=1;i<43;i++)
  { var sid = document.getElementById("s"+ i);
    if(i=='1' || i=='8' || i=='15' || i=='22' || i=='29' || i=='36')
    { sid.style.background='#ffe0e0';
    }
    else if(i=='7' || i=='14' || i=='21' || i=='28' || i=='35' || i=='42')
    { sid.style.background='#e0ffff';
    }
    else
    { sid.style.background='#ffffe0';
    }
    sid.style.cursor='default';
  }
  for(i=0;i<7;i++)
  { var sid = document.getElementById("s"+(i+1));
    if(WeekFirst==i)
    { ss=i+1;
      i=7;
    }
    else
    { sid.style.background='#a9a9a9';
      sid.innerText="��";
      sid.style.fontWeight='normal';
      sid.style.color='black';
    }
  }
  for(i=ss;i<43;i++)
  { var sid = document.getElementById("s"+i);
    var DateN = new Date(Str_YMD + Num_i);
    if((DateN.getMonth()+1)==MP)
    {
      sid.innerText=Num_i;
      if(Math.abs(nStaShelter)>=999)
      { sid.style.cursor='hand';
      }
      else
      { if(bForBack==1)
        { if(dStartPointDay >= DateN.getTime())
          { sid.style.cursor='default';
            sid.style.background='#a9a9a9';
          }
          else{sid.style.cursor='hand';}
        }
        else
        { if(dStartPointDay <= DateN.getTime())
          { sid.style.cursor='default';
            sid.style.background='#a9a9a9';
          }
          else{sid.style.cursor='hand';}
        }
      }
      sid.style.color='black';
      if(tfYMD==1 && NowDate.getDate()==Num_i)
      { sid.style.color='blue';
        sid.style.fontWeight='bold';
      }
      else{sid.style.fontWeight='normal';}
    }
    else
    { sid.style.background='#a9a9a9';
      sid.style.fontWeight='normal';
      sid.style.color='black';
      sid.innerText="��";
    }
    Num_i++
  } 
}

function inMou(SN)
{ var sidC = document.getElementById(SN);
  IDCopy=SN;
  if(sidC.innerText!="��")
  { var DateN = new Date(sYMDSel + sidC.innerText);
    curCX=sidC.style.color;
    if(Math.abs(nStaShelter)<999)
    { if(bForBack==1)
      { if(dStartPointDay < DateN.getTime())
        { sidC.style.background='red';
          sidC.style.color='white';
        }
      }
      else
      { if(dStartPointDay > DateN.getTime())
        { sidC.style.background='red';
          sidC.style.color='white';
        }
      }
    }
    else
    { sidC.style.background='red';
      sidC.style.color='white';
    }
  }
} 

function outMou(SN)
{ var IDCopy="��";
  var sidC = document.getElementById(SN);
  if(SN=='s1' || SN=='s8' || SN=='s15' || SN=='s22' || SN=='s29' || SN=='s36')
  { sidC.style.background='#ffe0e0';
  }
  else if(SN=='s7' || SN=='s14' || SN=='s21' || SN=='s28' || SN=='s35' || SN=='s42')
  { sidC.style.background='#e0ffff';
  }
  else
  { sidC.style.background='#ffffe0';
  }
  sidC.style.color=curCX;
  if(sidC.innerText=="��"){sidC.style.background='#a9a9a9';}
  else
  { if(Math.abs(nStaShelter)<999)
    { var DateN = new Date(sYMDSel + sidC.innerText);
      if(bForBack==1)
      { if(dStartPointDay >= DateN.getTime())
        { sidC.style.background='#a9a9a9';
          sidC.style.color='black';
        }
      }
      else
      { if(dStartPointDay <= DateN.getTime())
        { sidC.style.background='#a9a9a9';
          sidC.style.color='black';
        }
      }    
    }
  }
} 

function YChange()
{ var yearSel=document.YType.SeN.value;
  if(yearSel==1)
  { YearNumber=YearNumber-1911;
    document.getElementById("YrNum").innerText=YearNumber;
  }
  else
  { YearNumber=YearNumber+1911;
    document.getElementById("YrNum").innerText=YearNumber;
  }
}

function AddSub(Nu)
{ var tf=0;
  var NumS=new Number(document.getElementById("YrNum").innerText) + Nu;
  var NumSY=NumS;
  if(document.YType.SeN.value==1){NumS=NumS+1911;}
  if(NumS > bacw &&  NumS <= forw)
  { ListPri(NumS,new Number(document.getElementById("MnNum").innerText));
    YearNumber=NumSY;
    document.getElementById("YrNum").innerText=NumSY;
    tf=1;
  }
  if(tf==0){alert("On limit�I")}
}

function mAddSub(Nu)
{ var tf=0;
  var NumYS=new Number(document.getElementById("YrNum").innerText);
  var NumYSS=NumYS;
  if(document.YType.SeN.value==1){NumYS=NumYS+1911;} 
  var NumMS=new Number(document.getElementById("MnNum").innerText);
  var NumMSS=NumMS;
  NumMS += Nu;
  if(NumMS==0){NumYS -= 1;NumMS = 12;}
  if(NumMS==13){NumYS += 1;NumMS = 1;}
  if(NumYS > bacw &&  NumYS <= forw)
  { ListPri(NumYS,NumMS);
    if(document.YType.SeN.value==1){NumYS-=1911;}
    YearNumber=NumYS;
    document.getElementById("YrNum").innerText=NumYS;
    document.getElementById("MnNum").innerText=NumMS;
    tf=1;
  }
  if(tf==0){alert("On limit�I")}
}

function ListHelp()
{ var LisHp = document.getElementById("LiHp");
  LisHp.style.top = screen.availHeight/2-100;
  LisHp.style.left = event.clientX-150;
  LisHp.style.display="block";
}

function LiHpClose()
{ document.getElementById("LiHp").style.display="none";
}

function EveDm(){isDg=1;}

function EveUp(){isDg=0;}

function EveMv()
{ if(isDg==1 && TabId.style.display=="block")
  { TabId.style.left=event.clientX-90;
    TabId.style.top=event.clientY-12;  
  }
  else
  { isDg==0;
  }
}

function StatusDisplay(Nm)
{ if(Nm==0)
  { CloseTabCalTimekeeperPro=setTimeout("TabId.style.display='none'",mCloseDisplayTime);}
  else
  { clearTimeout(CloseTabCalTimekeeperPro);}
}
--> 
