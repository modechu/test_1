function isIE(){
if (navigator.appName!="Microsoft Internet Explorer") {return false}
return true
}

function correctPNG() 
{
   for(var i=0; i<document.images.length; i++)
   {
	  var img = document.images[i]
	  var LW=img.width
	  var LH=img.height
	  var imgName = img.src.toUpperCase()
	  if (imgName.substring(imgName.length-3, imgName.length) == "PNG")
	  { 
         img.style.filter+="progid:DXImageTransform.Microsoft.AlphaImageLoader(src="+img.src+", sizingmethod=scale);" 
         img.src="js/transparent.gif"
         img.width=LW
         img.height=LH
	  }
   }
}
if (isIE()) {window.attachEvent("onload", correctPNG);}