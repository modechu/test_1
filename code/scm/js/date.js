
<!--
/*日期相關的Javascript函式
		取得今日的日期[xxxx-xx-xx]
*/


//取得今日的日期[xxxx-xx-xx]
function GetTodayDate()
{
   today= new Date();
   y= today.getYear();
   m= (today.getMonth() + 1);
   if (m<10)
   {
     m='0'+m;
   }
   d= today.getDate();
   if (d<10)
   {
     d='0'+d;
   }
return y+'-'+m+'-'+d
}

--> 
