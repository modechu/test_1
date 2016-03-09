
$(document).ready(function() {
	get_markers = function (smpl_id,stds){
		$.ajax({
			url: 'marker.php?PHP_action=marker_smpl_view_ajx',
			type: 'GET',
			data: {
				PHP_smpl_id: smpl_id,
				PHP_status: stds
			},
			error: function(xhr) {
				alert('Ajax request µo¥Í¿ù»~'+xhr);
			},
			success: function(response) {
				//alert(response);
				document.getElementById("marker_out").style.visibility = "visible";
				var str='';
				var ttx='';

				var mks = response.responseText.split("@");
				var response = mks[0].split("|");
				var count = ( response.length > 0 ) ? ( response.length ) -1 : 0 ;

				for (var m=0; m < count; m++){
				
					var keys = response[m].split("^");
					var kcount = ( keys.length > 0) ? ( keys.length ) -1 : 0 ;
					
					for (var j=0; j < kcount; j++){
						var vals = keys[j].split("=");
						//alert( vals[0] + ' - ' + vals[1]);
						if(vals[0] == 'id' )			var idss		= (vals[1]);
						if(vals[0] == 'smpl_id' )		var smpl_id 	= (vals[1]);
						if(vals[0] == 'fab_type' )		var fab_type 	= (vals[1]);
						if(vals[0] == 'unit_type' )		var unit_type 	= (vals[1]);
						if(vals[0] == 'combo' )			var combo 		= (vals[1]);
						if(vals[0] == 'width' )			var widths 		= (vals[1]);
						if(vals[0] == 'length' )		var lengths 	= (vals[1]);
						if(vals[0] == 'remark' )		var remark 		= (unescape(vals[1]));
						//if(vals[0] == 'marker_date' )		var marker_date = (vals[1]);
						//str += unescape(vals[0]) + unescape(vals[1]);
					};
					
					ftype = get_ftype(fab_type);
					ut1 = get_ut1(unit_type);
					ut2 = get_ut2(unit_type);

					ttx += 
					'<tr bgcolor="#FFFFFF">'+
						'<td>&nbsp;<a href="javascript://" onClick="Edit_marker(\''+idss+'\',marker_out);" >'+ftype+combo+'</a></td>'+
						'<td>&nbsp;'+widths+ut1+'</td>'+
						'<td>&nbsp;'+lengths+ut2+'</td>'+
						'<td>&nbsp;'+remark+'</td>'+
					'</tr>';
				}

				if(mks[1]){
					var marker = '<td width="30" align="center" background="./images/bar_t.png" ><img src=\'./images/file2.gif\' border="0" alt="Download Marker" onClick="down_marker(\''+mks[1]+'\');" style="cursor:pointer" /></td>';
				} else {
					var marker = '<td width="30" align="center" background="./images/bar_t.png" ><img src=\'./images/file.gif\' border="0" alt="No Marker!!" /></td>';
				}
				
				if(!smpl_id) smpl_id = document.getElementById("PHP_id").value;

				if( mks[2] == 'edit' ){
					var edits = '<td width="30" align="center" background="./images/bar_t.png" ><img src=\'./images/m_app.png\' border="0" alt="Append Marker" onClick="Show_marker(\''+smpl_id+'\',marker_out);" style="cursor:pointer" /></td>';
				} else {
					var edits = '';
				}

				if( !ttx && !mks[1] ){
					ttx += 
					'<tr bgcolor="#FFFFFF">'+
						'<td colspan="6" align="center" height="32" ><font color="red">there is no record currently!</font></td>'+
					'</tr>';
				} else if ( !ttx ){
					ttx += 
					'<tr bgcolor="#FFFFFF">'+
						'<td colspan="6" align="center" height="32" ><font color="red">Marker (Old)!</font></td>'+
					'</tr>';
				}
				
				str += 
				'<table width="480" border="0" cellpadding="1" cellspacing="0" bgcolor="#000000" id="tblShow">'+
					'<tr>'+
						'<td><table width="100%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#999999">'+
							'<tr>'+
								'<td colspan="6" align="right" bgcolor="#FFFFFF" class="des"><table width="100%" border="0" cellpadding="0" cellspacing="0">'+
										'<tr onMousedown="initializedragie()" style="cursor:move">'+
											'<td height="32" align="center" background="./images/bar_t.png" ><img src=\'./images/bar_title.png\' border="0" /></td>'+
											marker+
											edits+
											'<td width="30" align="center" background="./images/bar_t.png" ><img src=\'./images/close.png\' border="0" alt="Close" onClick="outTip(marker_out);" style="cursor:pointer" /></td>'+
										'</tr>'+
								'</table></td>'+
							'</tr>'+
							'<tr>'+
								'<td width="80" align="center" bgcolor="#0D4368" class="des">Fab Type </td>'+
								'<td width="80" align="center" bgcolor="#0D4368" class="des">Width</td>'+
								'<td width="80" align="center" bgcolor="#0D4368" class="des"> (yy)</td>'+
								'<td align="center" bgcolor="#0D4368" class="des">Remark</td>'+
							'</tr>'+
							ttx +
						'</table>'+
						'</td>'+
					'</tr>'+
				'</table>';

				m_div1.innerHTML  = str;
				document.getElementById("marker_out").style.left = ( document.body.clientWidth / 2 ) - ( tblShow.offsetWidth / 2 );
				document.getElementById("marker_out").style.top = ( document.body.clientHeight / 2 ) - ( tblShow.offsetHeight / 2 );
			}
		});
	};
});


var transaction;
var sUrl;
var callbacks = {
	success: function(o) {
		document.getElementById("marker_out").style.visibility = "visible";
		var str='';
		var ttx='';
		var mks = o.responseText.split("@");
		var response = mks[0].split("|");
		var count = ( response.length > 0 ) ? ( response.length ) -1 : 0 ;
		for (var m=0; m < count; m++){
		
			var keys = response[m].split("^");
			var kcount = ( keys.length > 0) ? ( keys.length ) -1 : 0 ;
			
			for (var j=0; j < kcount; j++){
				var vals = keys[j].split("=");
				// alert( vals[0] + ' - ' + vals[1]);
				if(vals[0] == 'id' )			var idss		= (vals[1]);
				if(vals[0] == 'smpl_id' )		var smpl_id 	= (vals[1]);
				if(vals[0] == 'fab_type' )		var fab_type 	= (vals[1]);
				if(vals[0] == 'unit_type' )		var unit_type 	= (vals[1]);
				if(vals[0] == 'combo' )			var combo 		= (vals[1]);
				if(vals[0] == 'width' )			var widths 		= (vals[1]);
				if(vals[0] == 'length' )		var lengths 	= (vals[1]);
				if(vals[0] == 'description' )   var description = (unescape(vals[1]));
				if(vals[0] == 'remark' )		var remark 		= (unescape(vals[1]));
				//if(vals[0] == 'marker_date' )		var marker_date = (vals[1]);
				//str += unescape(vals[0]) + unescape(vals[1]);
			};
			
			ftype = get_ftype(fab_type);
			ut1 = get_ut1(unit_type);
			ut2 = get_ut2(unit_type);

			ttx += 
			'<tr bgcolor="#FFFFFF">'+
				'<td>&nbsp;<a href="javascript://" onClick="Edit_marker(\''+idss+'\',marker_out);" >'+ftype+combo+'</a></td>'+
				'<td>&nbsp;'+description+'</td>'+
				'<td>&nbsp;'+widths+ut1+'</td>'+
				'<td>&nbsp;'+lengths+ut2+'</td>'+
				'<td>&nbsp;'+remark+'</td>'+
			'</tr>';
		}

		if(mks[1]){
			var marker = '<td width="30" align="center" background="./images/bar_t.png" ><img src=\'./images/file2.gif\' border="0" alt="Download Marker" onClick="down_marker(\''+mks[1]+'\');" style="cursor:pointer" /></td>';
		} else {
			var marker = '<td width="30" align="center" background="./images/bar_t.png" ><img src=\'./images/file.gif\' border="0" alt="No Marker!!" /></td>';
		}
		
		if(!smpl_id) smpl_id = document.getElementById("PHP_id").value;

		if( mks[2] == 'edit' ){
			var edits = '<td width="30" align="center" background="./images/bar_t.png" ><img src=\'./images/m_app.png\' border="0" alt="Append Marker" onClick="Show_marker(\''+smpl_id+'\',marker_out);" style="cursor:pointer" /></td>';
		} else {
			var edits = '';
		}

		if( !ttx && !mks[1] ){
			ttx += 
			'<tr bgcolor="#FFFFFF">'+
				'<td colspan="6" align="center" height="32" ><font color="red">there is no record currently!</font></td>'+
			'</tr>';
		} else if ( !ttx ){
			ttx += 
			'<tr bgcolor="#FFFFFF">'+
				'<td colspan="6" align="center" height="32" ><font color="red">Marker (Old)!</font></td>'+
			'</tr>';
		}
		
		str += 
		'<table width="480" border="0" cellpadding="1" cellspacing="0" bgcolor="#000000" id="tblShow">'+
			'<tr>'+
				'<td><table width="100%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#999999">'+
					'<tr>'+
						'<td colspan="6" align="right" bgcolor="#FFFFFF" class="des"><table width="100%" border="0" cellpadding="0" cellspacing="0">'+
								'<tr onMousedown="initializedragie()" style="cursor:move">'+
									'<td height="32" align="center" background="./images/bar_t.png" ><img src=\'./images/bar_title.png\' border="0" /></td>'+
									marker+
									edits+
									'<td width="30" align="center" background="./images/bar_t.png" ><img src=\'./images/close.png\' border="0" alt="Close" onClick="outTip(marker_out);" style="cursor:pointer" /></td>'+
								'</tr>'+
						'</table></td>'+
					'</tr>'+
					'<tr>'+
						'<td width="80" align="center" bgcolor="#0D4368" class="des">Fab Type </td>'+
						'<td width="80" align="center" bgcolor="#0D4368" class="des">Description</td>'+
						'<td width="80" align="center" bgcolor="#0D4368" class="des">Width</td>'+
						'<td width="80" align="center" bgcolor="#0D4368" class="des"> (yy)</td>'+
						'<td align="center" bgcolor="#0D4368" class="des">Remark</td>'+
					'</tr>'+
					ttx +
				'</table>'+
				'</td>'+
			'</tr>'+
		'</table>';

		m_div1.innerHTML  = str;
		document.getElementById("marker_out").style.left = ( document.body.clientWidth / 2 ) - ( tblShow.offsetWidth / 2 );
		document.getElementById("marker_out").style.top = ( document.body.clientHeight / 2 ) - ( tblShow.offsetHeight / 2 );
	},
	failure: function(o) {
		alert("Error¡GCan't get data");
	}
};

function get_marker(smpl_id,stds) {
	m_div1.innerHTML  = '';
	var sUrl = 'marker.php?PHP_action=marker_smpl_view_ajx&PHP_smpl_id='+smpl_id+'&PHP_status='+stds;
	transaction = YAHOO.util.Connect.asyncRequest('GET', sUrl, callbacks, null);
}	

function get_ftype(id) {
	if(id==1) type = 'shell';
	if(id==2) type = 'lining';
	if(id==3) type = 'fusible';
	if(id==4) type = 'non-fusible';
	if(id==5) type = 'pocketing';
	return type;
}

function get_ut1(id) {
	return ( id == 1 ) ? ' (inch)' : ' (cm)' ;
}

function get_ut2(id) {
	return ( id == 1 ) ? ' (yard)' : ' (meter)' ;
}

function down_marker(num) {
	if ( confirm("download this style MARKER file?")) {
		location.href="marker.php?PHP_action=smpl_marker_download&PHP_num="+num;
	} 
}

function Show_marker(id,boxObj) {
	this.outTip(boxObj);
	var url ='marker.php?PHP_action=marker_smpl_add&PHP_smpl_id='+id; 	
	var nm = 'marker';
	window.open2(url,nm,'toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=660,height=600 top=50, left=100');
}

function Edit_marker(id,boxObj) {
	this.outTip(boxObj);
	var url ='marker.php?PHP_action=marker_smpl_view&PHP_id='+id; 	
	var nm = 'marker';
	window.open2(url,nm,'toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=660,height=600 top=50, left=100');
}

function overTip(boxObj) { 
	boxObj.style.visibility = "visible";
	boxObj.style.left = ( document.body.clientWidth / 2 ) - ( tblShow.offsetWidth / 2 );
	boxObj.style.top = ( document.body.clientHeight / 2 ) - ( tblShow.offsetHeight / 2 );
}

function outTip(boxObj) { 
	boxObj.style.visibility = "hidden"; 
}

var dragswitch=0
var nsx
var nsy
var nstemp

function drag_dropns(name){
	temp=eval(name)
	temp.captureEvents(Event.MOUSEDOWN | Event.MOUSEUP)
	temp.onmousedown=gons
	temp.onmousemove=dragns
	temp.onmouseup=stopns
}

function gons(e){
	temp.captureEvents(Event.MOUSEMOVE)
	nsx=e.x
	nsy=e.y
}

function dragns(e){
	if (dragswitch==1){
		temp.moveBy(e.x-nsx,e.y-nsy)
		return false
	}
}

function stopns(){
	temp.releaseEvents(Event.MOUSEMOVE)
}

var dragapproved=false
function drag_dropie(){
	if (dragapproved==true){
		document.all.marker_out.style.pixelLeft=tempx+event.clientX-iex
		document.all.marker_out.style.pixelTop=tempy+event.clientY-iey
		return false
	}
}

function initializedragie(){
	iex=event.clientX
	iey=event.clientY
	tempx=marker_out.style.pixelLeft
	tempy=marker_out.style.pixelTop
	dragapproved=true
	document.onmousemove=drag_dropie
}

if (document.all){
	document.onmouseup=new Function("dragapproved=false")
}

function hidebox(){
	if (document.all)
		marker_out.style.visibility="hidden"
	else if (document.layers)
		document.marker_out.visibility="hide"
}
