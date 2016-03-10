<?php
	include_once($config['root_dir']."/lib/chinese.php");
	define('FPDF_FONTPATH','font/');


class PDF_shp_pack_jg2 extends PDF_Chinese 	{


	var $B=0;
    var $I=0;
    var $U=0;
    var $HREF='';
    var $ALIGN='';
	var $angle=0;


//Page header
function Header() {

}

function hend_title($parm)
{
// 長度:205
/*
	$tmp = explode(' ',$parm['bill_addr2']);
	$bill_str1 = $bill_str2 = $bill_str3 = '';
	for($i=0; $i<sizeof($tmp); $i++)
	{
		$len1 = strlen($bill_str1)+strlen($tmp[$i]);
		$len2 = strlen($bill_str2)+strlen($tmp[$i]);
		$len3 = strlen($bill_str3)+strlen($tmp[$i]);
		if($len1 < 45 && strlen($bill_str2) <= 1)
		{
			$bill_str1 = $bill_str1.$tmp[$i]." ";
		}else if($len2 < 45 && strlen($bill_str3) <= 1){
			$bill_str2 = $bill_str2.$tmp[$i]." ";
		}else{
			$bill_str3 = $bill_str3.$tmp[$i]." ";
		}
	}
	*/
	$etd = $eta = '';
	if($parm['fv_etd']) $etd = $parm['fv_etd'];
	if($parm['mv_etd']) $etd = $parm['mv_etd'];
	if($parm['fv_eta']) $eta = $parm['fv_eta'];
	if($parm['mv_eta']) $eta = $parm['mv_eta'];

	$txt = explode(chr(13).chr(10),$parm['comm']);

	
	$this->SetFont('Arial','B',8);
	$this->Cell(100, 5,"JAG Packing List",'TL',0,'L',0);
	$this->Cell(35, 5,"Factory Name:",'T',0,'L',0);
	$this->Cell(70, 5,$GLOBALS['SHIP_TO'][$parm['fty']]['Name'],'TR',1,'L',0);

	$this->SetFont('Arial','',8);
	$this->Cell(100, 5,"Email to: Shipments@inv.com",'BL',0,'L',0);
	$this->Cell(35, 5,"Contact Email/Phone#:",'B',0,'L',0);
	$this->Cell(70, 5,$parm['submit_mail'],'BR',1,'L',0);

	$this->SetFont('Arial','',7);
	$this->Cell(29, 5,"Date:",1,0,'C',0);
	$this->Cell(24, 5,"RCV Date:",1,0,'L',0);
	$this->Cell(24, 5,"",1,0,'L',0);
	$this->Cell(31, 5,"Est Load Date:",1,0,'L',0);
	$this->Cell(29, 5,$parm['lod_date'],1,0,'C',0);
	$this->Cell(23, 5,"Mode:",1,0,'L',0);
	$this->Cell(45, 5,"Comments:",1,1,'C',0);


	$this->Cell(29, 3,"",'TLR',0,'L',0);
	$this->Cell(24, 3,"Est Sail Date:",'TLR',0,'L',0);
	$this->Cell(24, 3,"",'TLR',0,'L',0);
	$this->Cell(31, 3,"",'TLR',0,'L',0);
	$this->Cell(29, 3,'','TLR',0,'L',0);
	$this->Cell(18, 3,"",'TLR',0,'L',0);
	$this->Cell(5, 3,"",'TLR',0,'L',0);
	$this->Cell(45, 3,"",'TLR',1,'L',0);

	$mk = '';
	if($parm['mode'] == 'Air-')$mk = 'X';
	$this->Cell(29, 3,$parm['submit_date'],'BLR',0,'C',0);
	$this->Cell(24, 3,"(ETD Origin):",'BLR',0,'L',0);
	$this->Cell(24, 3,$etd,'BLR',0,'C',0);
	$this->Cell(31, 3,"Est Discharge Date:(ETA)",'BLR',0,'L',0);
	$this->Cell(29, 3,$parm['dischg_date'],'BLR',0,'C',0);
	$this->Cell(18, 3,"Air-",'BLR',0,'L',0);
	$this->Cell(5, 3,$mk,'BLR',0,'L',0);
	$this->Cell(45, 3,$txt[0],'BLR',1,'L',0);

	$mk = '';
	if($parm['mode'] == 'MLB-')$mk = 'X';
	if(!isset($txt[1]))$txt[1]='';
	$this->Cell(29, 5,"",'LR',0,'C',0);
	$this->Cell(24, 5,"ETA Dest Date",1,0,'L',0);
	$this->Cell(24, 5,$eta,1,0,'C',0);
	$this->Cell(31, 5,"Carrier",1,0,'L',0);
	$this->Cell(29, 5,$parm['carrier'],1,0,'C',0);
	$this->Cell(18, 5,"MLB-",'BLR',0,'L',0);
	$this->Cell(5, 5,$mk,'BLR',0,'L',0);
	$this->Cell(45, 5,$txt[1],1,1,'C',0);

	$mk = '';
	if($parm['mode'] == 'Sea-')$mk = 'X';
	if(!isset($txt[2]))$txt[2]='';
	$this->Cell(29, 5,"",'LR',0,'C',0);
	$this->Cell(24, 5,"Vehicle:",1,0,'L',0);
	$this->Cell(24, 5,'',1,0,'C',0);
	$this->Cell(31, 5,"Voyage/Trip:",1,0,'L',0);
	$this->Cell(29, 5,$parm['voyage'],1,0,'C',0);
	$this->Cell(18, 5,"Sea-",'BLR',0,'L',0);
	$this->Cell(5, 5,$mk,'BLR',0,'L',0);
	$this->Cell(45, 5,$txt[2],1,1,'C',0);

	if(!isset($txt[3]))$txt[3]='';
	$this->Cell(29, 5,"",'LR',0,'C',0);
	$this->Cell(24, 5,"Rcv City:",1,0,'L',0);
	$this->Cell(24, 5,$parm['ship_from'],1,0,'C',0);
	$this->Cell(31, 5,"Site:",1,0,'L',0);
	$this->Cell(29, 5,'',1,0,'C',0);
	$this->Cell(18, 5,"Sea/Truck-",'BLR',0,'L',0);
	$this->Cell(5, 5,'','BLR',0,'L',0);
	$this->Cell(45, 5,$txt[3],1,1,'C',0);


	$this->Cell(29, 5,"",'LR',0,'C',0);
	$this->Cell(24, 5,"Facility:",1,0,'L',0);
	$this->Cell(24, 5,'',1,0,'C',0);
	$this->Cell(31, 5,"Facility Type:(CY/CFS)",1,0,'L',0);
	$this->Cell(29, 5,$parm['facility_value'],1,0,'C',0);
	$this->Cell(23, 5,"HBL#/HAWB#",'BLR',0,'L',0);
	$this->Cell(45, 5,$parm['hbl'],1,1,'C',0);

	$this->Cell(29, 3,"",'LR',0,'L',0);
	$this->Cell(24, 3,"",'TLR',0,'L',0);
	$this->Cell(24, 3,"",'TLR',0,'L',0);
	$this->Cell(31, 3,"Disch ID:",'TLR',0,'L',0);
	$this->Cell(29, 3,'','TLR',0,'L',0);
	$this->Cell(23, 3,"",'TLR',0,'L',0);
	$this->Cell(45, 3,"",'TLR',1,'L',0);

	$this->Cell(29, 3,'PO Type:','BLR',0,'C',0);
	$this->Cell(24, 3,"Load:",'BLR',0,'L',0);
	$this->Cell(24, 3,$parm['ship_from'],'BLR',0,'C',0);
	$this->Cell(31, 3,"(Port of Discharge)",'BLR',0,'L',0);
	$this->Cell(29, 3,$parm['ship_to'],'BLR',0,'C',0);
	$this->Cell(23, 3,"MBL#",'BLR',0,'L',0);
	$this->Cell(45, 3,'','BLR',1,'L',0);


	$this->Cell(29, 5,"Select PO Type Below",1,0,'C',0);
	$this->Cell(24, 5,"Whse Spot:",1,0,'L',0);
	$this->Cell(24, 5,'X',1,0,'C',0);
	$this->Cell(31, 5,"Sample ctn",1,0,'L',0);
	$this->Cell(29, 5,'',1,0,'C',0);
	$this->Cell(23, 5,"Mother Vessel",1,0,'L',0);
	$this->Cell(45, 5,$parm['mv'],1,1,'C',0);

	$mk = '';
	if($parm['po_type'] == 'Fert')$mk = 'X';
	$this->Cell(25, 3,"",'TLR',0,'C',0);
	$this->Cell(4,  3,'','TLR',0,'C',0);
	$this->Cell(24, 3,"Commercial invoice",'TLR',0,'L',0);
	$this->Cell(24, 3,"",'TLR',0,'L',0);
	$this->Cell(31, 3,"",'TLR',0,'L',0);
	$this->Cell(29, 3,'','TLR',0,'L',0);
	$this->Cell(68, 3,"",'TLR',1,'L',0);

	$this->Cell(25, 3,"Fert:",'BLR',0,'C',0);
	$this->Cell(4,  3,$mk,'BLR',0,'C',0);
	$this->Cell(24, 3,"#:",'BLR',0,'L',0);
	$this->Cell(24, 3,$parm['inv_num'],'BLR',0,'C',0);
	$this->Cell(31, 3,"Freight Receipt:",'BLR',0,'L',0);
	$this->Cell(29, 3,$parm['freight_rcp'],'BLR',0,'C',0);
	$this->Cell(68, 3,"",'BLR',1,'L',0);


	$mk = '';
	if($parm['po_type'] == 'PrePack(PPK)')$mk = 'X';
	$this->Cell(25, 3,"",'TLR',0,'C',0);
	$this->Cell(4,  3,'','TLR',0,'C',0);
	$this->Cell(24, 3,"Freight Receipt",'TLR',0,'L',0);
	$this->Cell(24, 3,"",'TLR',0,'L',0);
	$this->Cell(31, 3,"",'TLR',0,'L',0);
	$this->Cell(29, 3,'','TLR',0,'L',0);
	$this->Cell(23, 3,"",'TLR',0,'L',0);
	$this->Cell(45, 3,'','TLR',1,'C',0);

	$this->Cell(25, 3,"PrePack(PPK):",'BLR',0,'C',0);
	$this->Cell(4,  3,$mk,'BLR',0,'C',0);
	$this->Cell(24, 3,"Date:",'BLR',0,'L',0);
	$this->Cell(24, 3,$parm['freight_date'],'BLR',0,'C',0);
	$this->Cell(31, 3,"Dock Receipt:",'BLR',0,'L',0);
	$this->Cell(29, 3,'','BLR',0,'C',0);
	$this->Cell(23, 3,"Feeder Vessel",'BLR',0,'L',0);
	$this->Cell(45, 3,$parm['fv'],'BLR',1,'C',0);


	$mk = '';
	if($parm['po_type'] == 'ZSUT S(Suit S)')$mk = 'X';
	$this->Cell(25, 5,"ZSUT S(Suit S):",1,0,'C',0);
	$this->Cell(4,  5,$mk,1,0,'C',0);
	$this->Cell(24, 5,"Complete Status:",1,0,'L',0);
	$this->Cell(24, 5,'C',1,0,'C',0);
	$this->Cell(31, 5,"Act Freight Forwader:",1,0,'L',0);
	$this->Cell(29, 5,'SCHENKER',1,0,'C',0);
	$this->Cell(68, 5,"",1,1,'L',0);

	$mk = '';
	if($parm['po_type'] == 'ZSUT C(Suit C)')$mk = 'X';
	$this->Cell(25, 5,"ZSUT C(Suit C):",1,0,'C',0);
	$this->Cell(4,  5,$mk,1,0,'C',0);
	$this->Cell(24, 5,"Org Inland Trk:",1,0,'L',0);
	$this->Cell(24, 5,'',1,0,'C',0);
	$this->Cell(31, 5,"FTY Ship Date:",1,0,'L',0);
	$this->Cell(29, 5,$parm['fty_ship_date'],1,0,'C',0);
	$this->Cell(23, 5,"Org ID:",1,0,'L',0);
	$this->Cell(45, 5,"",1,1,'L',0);

	$this->Cell(29, 3,"",'TLR',0,'C',0);
	$this->Cell(24, 3,"",'TLR',0,'L',0);
	$this->Cell(24, 3,"",'TLR',0,'L',0);
	$this->Cell(31, 3,"Facility ship to(4-Digit",'TLR',0,'L',0);
	$this->Cell(29, 3,'','TLR',0,'L',0);
	$this->Cell(23, 3,"",'TLR',0,'L',0);
	$this->Cell(45, 3,'','TLR',1,'C',0);

	$this->Cell(29, 3,"Container Number:",'BLR',0,'C',0);
	$this->Cell(24, 3,"FOB Point:",'BLR',0,'L',0);
	$this->Cell(24, 3,$parm['ship_from'],'BLR',0,'C',0);
	$this->Cell(31, 3,"Code):",'BLR',0,'L',0);
	$this->Cell(29, 3,$parm['facility_code'],'BLR',0,'C',0);
	$this->Cell(23, 3,"Dest ID:",'BLR',0,'L',0);
	$this->Cell(45, 3,'','BLR',1,'C',0);

	$this->Cell(29, 3,"",'TLR',0,'C',0);
	$this->Cell(24, 3,"Icon Term1:",'TLR',0,'L',0);
	$this->Cell(24, 3,"",'TLR',0,'L',0);
	$this->Cell(31, 3,"",'TLR',0,'L',0);
	$this->Cell(29, 3,'','TLR',0,'L',0);
	$this->Cell(68, 3,'','TLR',1,'C',0);

	$this->Cell(29, 3,$parm['conter_num'],'BLR',0,'C',0);
	$this->Cell(24, 3,"(PO term)",'BLR',0,'L',0);
	$this->Cell(24, 3,$parm['ship_term'],'BLR',0,'C',0);
	$this->Cell(31, 3,"Facility ship to(Name):",'BLR',0,'L',0);
	$this->Cell(29, 3,$parm['facility_name'],'BLR',0,'C',0);
	$this->Cell(68, 3,'','BLR',1,'C',0);
}

function ord_rec($parm1,$parm2,$new_pg=1)
{
// 長度:205
		$this->SetFillColor(0,0,0);
		if($new_pg)$this->Cell(205, 1,'',1,1,'L',1);

		$this->SetFont('Arial','',7);


		$this->Cell(8, 3,'','TLR',0,'L',0);
		$this->Cell(13, 3,'','TLR',0,'L',0);
		$this->Cell(7, 3,'','TLR',0,'L',0);
		$this->Cell(24, 3,'','TLR',0,'L',0);
		$this->Cell(16, 3,'Gross','TLR',0,'L',0);
		$this->Cell(9, 3,'','TLR',0,'L',0);
		$this->Cell(15, 3,'Dim Weight','TLR',0,'L',0);	
		$this->Cell(10, 3,'','TLR',0,'L',0);
		$this->Cell(1, 3,'','TLR',0,'L',1);
		$this->Cell(8, 3,'','TLR',0,'L',0);
		$this->Cell(13, 3,'','TLR',0,'L',0);
		$this->Cell(7, 3,'','TLR',0,'L',0);
		$this->Cell(24, 3,'','TLR',0,'L',0);	
		$this->Cell(16, 3,'Gross','TLR',0,'L',0);	
		$this->Cell(9, 3,'','TLR',0,'L',0);	
		$this->Cell(15, 3,'Dim Weight','TLR',0,'L',0);
		$this->Cell(10, 3,'','TLR',1,'L',0);

		$this->Cell(8, 3,'Item#','BLR',0,'L',0);
		$this->Cell(13, 3,$parm1['item'],'BLR',0,'L',0);

		$this->Cell(7, 3,'po#','BLR',0,'L',0);
		$this->Cell(24, 3,$parm1['po'],'BLR',0,'L',0);
		$this->Cell(16, 3,'Weight KGS:','BLR',0,'L',0);
		$this->Cell(9, 3,Number_format($parm1['gw'],2),'BLR',0,'L',0);
		$this->Cell(15, 3,'KGS:','BLR',0,'L',0);
		$this->Cell(10, 3,'','BLR',0,'L',0);
		$this->Cell(1, 3,'','BLR',0,'L',1);
		$this->Cell(8, 3,'Item#','BLR',0,'L',0);
		$this->Cell(13, 3,$parm2['item'],'BLR',0,'L',0);
		$this->Cell(7, 3,'po#','BLR',0,'L',0);
		$this->Cell(24, 3,$parm2['po'],'BLR',0,'L',0);
		$this->Cell(16, 3,'Weight KGS:','BLR',0,'L',0);
		$this->Cell(9, 3,Number_format($parm2['gw'],2),'BLR',0,'L',0);
		$this->Cell(15, 3,'KGS:','BLR',0,'L',0);
		$this->Cell(10, 3,'','BLR',1,'L',0);
		

		$this->Cell(21, 5,'Material#:',1,0,'L',0);
		$this->Cell(31, 5,$parm1['color'],1,0,'L',0);
		$this->Cell(25, 5,'Component#:',1,0,'L',0);
		$this->Cell(25, 5,$parm1['compt'],1,0,'L',0);
		$this->Cell(1, 5,'',1,0,'L',1);
		$this->Cell(21, 5,'Material#:',1,0,'L',0);
		$this->Cell(31, 5,$parm2['color'],1,0,'L',0);
		$this->Cell(25, 5,'Component#:',1,0,'L',0);
		$this->Cell(25, 5,$parm2['compt'],1,1,'L',0);

		$this->Cell(21, 5,'Material F/H#:',1,0,'L',0);
		$this->Cell(31, 5,$parm1['mat_ft'],1,0,'C',0);
		$this->Cell(25, 5,'Ship F/H#:',1,0,'L',0);
		$this->Cell(25, 5,$parm1['ship_ft'],1,0,'C',0);
		$this->Cell(1, 5,'',1,0,'L',1);
		$this->Cell(21, 5,'Material F/H#:',1,0,'L',0);
		$this->Cell(31, 5,$parm2['mat_ft'],1,0,'C',0);
		$this->Cell(25, 5,'Ship F/H#:',1,0,'L',0);
		$this->Cell(25, 5,$parm2['ship_ft'],1,1,'C',0);
		
		$this->Cell(21, 5,'FOB QTY #1:',1,0,'L',0);
		$this->Cell(31, 5,$parm1['qty'],1,0,'C',0);
		$this->Cell(25, 5,'FOB Uprice #1:',1,0,'L',0);
		$this->Cell(25, 5,$parm1['fob'],1,0,'C',0);
		$this->Cell(1, 5,'',1,0,'L',1);
		$this->Cell(21, 5,'FOB QTY #1:',1,0,'L',0);
		$this->Cell(31, 5,$parm2['qty'],1,0,'C',0);
		$this->Cell(25, 5,'FOB Uprice #1:',1,0,'L',0);
		$this->Cell(25, 5,$parm2['fob'],1,1,'C',0);

		$this->Cell(21, 5,'FOB CO #1:',1,0,'L',0);
		$this->Cell(31, 5,$parm1['fob_co'],1,0,'C',0);
		$this->Cell(25, 5,'Other Uprice :',1,0,'L',0);
		$this->Cell(25, 5,"",1,0,'C',0);
		$this->Cell(1, 5,'',1,0,'L',1);
		$this->Cell(21, 5,'FOB CO #1:',1,0,'L',0);
		$this->Cell(31, 5,$parm2['fob_co'],1,0,'C',0);
		$this->Cell(25, 5,'Other Uprice :',1,0,'L',0);
		$this->Cell(25, 5,"",1,1,'C',0);
		
		$this->Cell(21, 5,'Net Weight:',1,0,'L',0);
		$this->Cell(31, 5,$parm1['nw'],1,0,'C',0);
		$this->Cell(25, 5,'CMT Unit Cost :',1,0,'L',0);
		$this->Cell(25, 5,"",1,0,'C',0);
		$this->Cell(1, 5,'',1,0,'L',1);
		$this->Cell(21, 5,'Net Weight:',1,0,'L',0);
		$this->Cell(31, 5,$parm2['nw'],1,0,'C',0);
		$this->Cell(25, 5,'CMT Unit Cost :',1,0,'L',0);
		$this->Cell(25, 5,"",1,1,'C',0);
		
		$this->Cell(21, 5,'Fabric Unit Cst:',1,0,'L',0);
		$this->Cell(31, 5,"",1,0,'C',0);
		$this->Cell(25, 5,'Hanger Cose #1 :',1,0,'L',0);
		$this->Cell(25, 5,"",1,0,'C',0);
		$this->Cell(1, 5,'',1,0,'L',1);
		$this->Cell(21, 5,'Fabric Unit Cst:',1,0,'L',0);
		$this->Cell(31, 5,"",1,0,'C',0);
		$this->Cell(25, 5,'Hanger Cose #1 :',1,0,'L',0);
		$this->Cell(25, 5,"",1,1,'C',0);		

		$this->Cell(21, 5,'Hanger Qty #1:',1,0,'L',0);
		$this->Cell(31, 5,$parm1['qty'],1,0,'C',0);
		$this->Cell(25, 5,'Hanger price #1 :',1,0,'L',0);
		$this->Cell(25, 5,$parm1['hanger'],1,0,'C',0);
		$this->Cell(1, 5,'',1,0,'L',1);
		$this->Cell(21, 5,'Hanger Qty #1:',1,0,'L',0);
		$this->Cell(31, 5,$parm2['qty'],1,0,'C',0);
		$this->Cell(25, 5,'Hanger price #1 :',1,0,'L',0);
		$this->Cell(25, 5,$parm2['hanger'],1,1,'C',0);	
		
		$this->Cell(21, 5,'Belt Qty :',1,0,'L',0);
		$this->Cell(31, 5,$parm1['qty'],1,0,'C',0);
		$this->Cell(25, 5,'Belt Uprice :',1,0,'L',0);
		$this->Cell(25, 5,$parm1['belt'],1,0,'C',0);
		$this->Cell(1, 5,'',1,0,'L',1);
		$this->Cell(21, 5,'Belt Qty :',1,0,'L',0);
		$this->Cell(31, 5,$parm2['qty'],1,0,'C',0);
		$this->Cell(25, 5,'Belt Uprice :',1,0,'L',0);
		$this->Cell(25, 5,$parm2['belt'],1,1,'C',0);		

		$this->Cell(21, 5,'Belt CO :',1,0,'L',0);
		$this->Cell(31, 5,$parm1['belt_co'],1,0,'C',0);
		$this->Cell(25, 5,'Accessory Qty :',1,0,'L',0);
		$this->Cell(25, 5,"",1,0,'C',0);
		$this->Cell(1, 5,'',1,0,'L',1);
		$this->Cell(21, 5,'Belt CO :',1,0,'L',0);
		$this->Cell(31, 5,$parm2['belt_co'],1,0,'C',0);
		$this->Cell(25, 5,'Accessory Qty :',1,0,'L',0);
		$this->Cell(25, 5,"",1,1,'C',0);		

		$this->Cell(21, 5,'Accessory Upr :',1,0,'L',0);
		$this->Cell(31, 5,'',1,0,'C',0);
		$this->Cell(25, 5,'Accessory CO :',1,0,'L',0);
		$this->Cell(25, 5,"",1,0,'C',0);
		$this->Cell(1, 5,'',1,0,'L',1);
		$this->Cell(21, 5,'Accessory Upr :',1,0,'L',0);
		$this->Cell(31, 5,'',1,0,'C',0);
		$this->Cell(25, 5,'Accessory CO :',1,0,'L',0);
		$this->Cell(25, 5,"",1,1,'C',0);

		$this->SetFont('Arial','',6);
		$this->Cell(21, 5,'FW Comm Name #1 ',1,0,'L',0);
		$this->Cell(31, 5,'',1,0,'C',0);
		$this->SetFont('Arial','',7);
		$this->Cell(25, 5,'Genus/Species :',1,0,'L',0);
		$this->Cell(25, 5,"",1,0,'C',0);
		$this->Cell(1, 5,'',1,0,'L',1);
		$this->SetFont('Arial','',6);		
		$this->Cell(21, 5,'FW Comm Name #1 ',1,0,'L',0);
		$this->Cell(31, 5,'',1,0,'C',0);
		$this->SetFont('Arial','',7);
		$this->Cell(25, 5,'Genus/Species :',1,0,'L',0);
		$this->Cell(25, 5,"",1,1,'C',0);
		
		$this->SetFont('Arial','',6);
		$this->Cell(21, 5,'FW Country Orig #1 ',1,0,'L',0);
		$this->Cell(31, 5,'',1,0,'C',0);
		$this->SetFont('Arial','',7);		
		$this->Cell(25, 5,'FW PC Per Garm1 :',1,0,'L',0);
		$this->Cell(25, 5,"",1,0,'C',0);
		$this->Cell(1, 5,'',1,0,'L',1);
		$this->SetFont('Arial','',6);		
		$this->Cell(21, 5,'FW Country Orig #1 ',1,0,'L',0);
		$this->Cell(31, 5,'',1,0,'C',0);
		$this->SetFont('Arial','',7);		
		$this->Cell(25, 5,'FW PC Per Garm1 :',1,0,'L',0);
		$this->Cell(25, 5,"",1,1,'C',0);	
		
		$this->Cell(21, 5,'FW Cost #1 :',1,0,'L',0);
		$this->Cell(31, 5,'',1,0,'C',0);
		$this->Cell(25, 5,'FOB Qty #2 :',1,0,'L',0);
		$this->Cell(25, 5,"",1,0,'C',0);
		$this->Cell(1, 5,'',1,0,'L',1);
		$this->Cell(21, 5,'FW Cost #1 :',1,0,'L',0);
		$this->Cell(31, 5,'',1,0,'C',0);
		$this->Cell(25, 5,'FOB Qty #2 :',1,0,'L',0);
		$this->Cell(25, 5,"",1,1,'C',0);

		$this->Cell(21, 5,'FOB Uprice #2 :',1,0,'L',0);
		$this->Cell(31, 5,'',1,0,'C',0);
		$this->Cell(25, 5,'FOB CO #2 :',1,0,'L',0);
		$this->Cell(25, 5,"",1,0,'C',0);
		$this->Cell(1, 5,'',1,0,'L',1);
		$this->Cell(21, 5,'FOB Uprice #2 :',1,0,'L',0);
		$this->Cell(31, 5,'',1,0,'C',0);
		$this->Cell(25, 5,'FOB CO #2 :',1,0,'L',0);
		$this->Cell(25, 5,"",1,1,'C',0);
		
		$this->Cell(21, 5,'Hanger Code #2 :',1,0,'L',0);
		$this->Cell(31, 5,'',1,0,'C',0);
		$this->Cell(25, 5,'Hanger Qty #2 :',1,0,'L',0);
		$this->Cell(25, 5,"",1,0,'C',0);
		$this->Cell(1, 5,'',1,0,'L',1);
		$this->Cell(21, 5,'Hanger Code #2 :',1,0,'L',0);
		$this->Cell(31, 5,'',1,0,'C',0);
		$this->Cell(25, 5,'Hanger Qty #2 :',1,0,'L',0);
		$this->Cell(25, 5,"",1,1,'C',0);		

		$this->Cell(21, 5,'Hanger Price #2 :',1,0,'L',0);
		$this->Cell(31, 5,'',1,0,'C',0);
		$this->Cell(25, 5,'Visa # :',1,0,'L',0);
		$this->Cell(25, 5,"",1,0,'C',0);
		$this->Cell(1, 5,'',1,0,'L',1);
		$this->Cell(21, 5,'Hanger Price #2 :',1,0,'L',0);
		$this->Cell(31, 5,'',1,0,'C',0);
		$this->Cell(25, 5,'Visa # :',1,0,'L',0);
		$this->Cell(25, 5,"",1,1,'C',0);		
			
//		$this->SetFont('Arial','U',8);
}


function item_txt($parm)
{
		$this->SetFont('Arial','',8);
		$this->Cell(195, 4,$parm['des'],0,0,'L',0);
		$this->ln();
		$this->Cell(15, 4,'CONTENT: ',0,0,'L',0);
		$this->Cell(180, 4,$parm['content'],0,0,'L',0);
		$this->ln();
		$this->SetFont('Arial','UB',9);
		$this->Cell(40, 4,'P.O. NO.',0,0,'C',0);
		$this->Cell(40, 4,'STYLE NO.',0,0,'C',0);
		$this->Cell(40, 4,'COLOR',0,0,'C',0);		
		$this->ln();
		$this->SetFont('Arial','',8);
		$this->Cell(40, 4,$parm['cust_po'],0,0,'C',0);
		$this->Cell(40, 4,$parm['style_num'],0,0,'C',0);
		$this->Cell(40, 4,$parm['color'],0,0,'C',0);		
		$this->Cell(15, 4,NUMBER_FORMAT($parm['bk_qty'],0),0,0,'R',0);	
		$this->Cell(10, 4,'PCS',0,0,'C',0);	
		$this->Cell(12, 4,'@USD',0,0,'R',0);	
		$this->Cell(13, 4,NUMBER_FORMAT($parm['uprice'],2),0,0,'R',0);	
		$this->Cell(25, 4,"US$".NUMBER_FORMAT($parm['amount'],2),0,0,'R',0);	

		$this->ln();
		

}

function item_total($parm)
{
		$vl1 = $vl2 = '';
		$this->SetFont('Arial','B',8);
		$this->Cell(120,0.5,'',0,0,'L',0);
		$this->Cell(75,0.5,'','B',0,'C',0);
		$this->ln();
		$this->Cell(110, 4,'TOTAL:',0,0,'R',0);
		$this->Cell(25, 4,NUMBER_FORMAT($parm['qty'],0),0,0,'R',0);	
		$this->Cell(10, 4,'PCS',0,0,'C',0);	
		$this->Cell(50, 4,"US$".NUMBER_FORMAT($parm['amount'],2),0,0,'R',0);	

		$this->ln();
		for($i=0; $i<(strlen($parm['qty'])); $i++) $vl1 .= 'V';
		for($i=0;	$i<(strlen(NUMBER_FORMAT($parm['amount'],2,'',''))+2); $i++) $vl2 .= 'V';
		$this->Cell(110,4,'',0,0,'L',0);
		$this->Cell(25, 4,$vl1,0,0,'R',0);
		$this->Cell(10, 4,'VVV',0,0,'C',0);	
		$this->Cell(50, 4,$vl2,0,0,'R',0);			
		$this->ln();
		$this->Cell(175,4,'SAY TOTAL U.S. DOLLORS '.$parm['eng_amount'].'.',0,0,'L',0);
		$this->ln();
}
function inv_txt($title,$txt)
{
	$this->SetFont('Arial','B',8);
	$this->Cell(175,7,$title,0,0,'L',0);
	$this->ln();
	$this->SetFont('Arial','',8);
	for($i=0; $i<sizeof($txt); $i++)
	{
		$this->Cell(175,4,$txt[$i],0,0,'L',0);
		$this->ln();
	}	
}

function pack_txt($parm)
{
	$this->ln();
	$this->SetFont('Arial','B',8);
	$this->Cell(20,4,'TOTAL',0,0,'L',0);
	$this->SetFont('Arial','',8);
	$this->Cell(20,4,'PACKED.:',0,0,'L',0);
	$this->Cell(20,4,$parm['ctn'],0,0,'R',0);
	$this->Cell(20,4,'  CTNS',0,0,'L',0);
	$this->ln();

	$this->Cell(20,4,'',0,0,'L',0);
	$this->Cell(20,4,'N.N.W.:',0,0,'L',0);
	$this->Cell(20,4,$parm['nnw'],0,0,'R',0);
	$this->Cell(20,4,'  KGS',0,0,'L',0);
	$this->ln();

	$this->Cell(20,4,'',0,0,'L',0);
	$this->Cell(20,4,'N.W.:',0,0,'L',0);
	$this->Cell(20,4,$parm['nw'],0,0,'R',0);
	$this->Cell(20,4,'  KGS',0,0,'L',0);
	$this->ln();	

	$this->Cell(20,4,'',0,0,'L',0);
	$this->Cell(20,4,'G.W.:',0,0,'L',0);
	$this->Cell(20,4,$parm['gw'],0,0,'R',0);
	$this->Cell(20,4,'  KGS',0,0,'L',0);
	$this->ln();

}


function RotatedText($x,$y,$txt,$angle) {
    //Text rotated around its origin
    $this->Rotate($angle,$x,$y);
    $this->Text($x,$y,$txt);
    $this->Rotate(0);
}

function Rotate($angle,$x=-1,$y=-1)
{
    if($x==-1)
        $x=$this->x;
    if($y==-1)
        $y=$this->y;
    if($this->angle!=0)
        $this->_out('Q');
    $this->angle=$angle;
    if($angle!=0)
    {
        $angle*=M_PI/180;
        $c=cos($angle);
        $s=sin($angle);
        $cx=$x*$this->k;
        $cy=($this->h-$y)*$this->k;
        $this->_out(sprintf('q %.5f %.5f %.5f %.5f %.2f %.2f cm 1 0 0 1 %.2f %.2f cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
    }
}


//Page footer
function Footer() {
	global $creator;
	global $print_title2;
	//Position at 1.5 cm from bottom
    $this->SetY(-15);
    //Arial italic 8

    $this->SetFont('Big5','I',8);
    //Page number
	$this->AliasNbPages('<nb>'); 
    $this->Cell(0,10,$print_title2.'                   Created by : '.$creator,0,0,'R');
}



// table founctions----------
function LoadData($file) {
    //Read file lines
    $lines=file($file);
    $data=array();
    foreach($lines as $line)
        $data[]=explode(';',chop($line));
    return $data;
}


//---------- table with Muti cell ----------------	
var $widths;
var $aligns;

function SetWidths($w)
{
    //Set the array of column widths
    $this->widths=$w;
}

function SetAligns($a)
{
    //Set the array of column alignments
    $this->aligns=$a;
}

function Row($data)
{
    //Calculate the height of the row
    $nb=0;
    for($i=0;$i<count($data);$i++)
        $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
    $h=5*$nb;
    //Issue a page break first if needed
    $this->CheckPageBreak($h);
    //Draw the cells of the row
    for($i=0;$i<count($data);$i++)
    {
        $w=$this->widths[$i];
        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
        //Save the current position
        $x=$this->GetX();
        $y=$this->GetY();
        //Draw the border
        $this->Rect($x,$y,$w,$h);
        //Print the text
        $this->MultiCell($w,5,$data[$i],0,$a);
        //Put the position to the right of the cell
        $this->SetXY($x+$w,$y);
    }
    //Go to the next line
    $this->Ln($h);
}

function CheckPageBreak($h)
{
    //If the height h would cause an overflow, add a new page immediately
    if($this->GetY()+$h>$this->PageBreakTrigger)
        $this->AddPage($this->CurOrientation);
}

function NbLines($w,$txt)
{
    //Computes the number of lines a MultiCell of width w will take
    $cw=&$this->CurrentFont['cw'];
    if($w==0)
        $w=$this->w-$this->rMargin-$this->x;
    $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
    $s=str_replace("\r",'',$txt);
    $nb=strlen($s);
    if($nb>0 and $s[$nb-1]=="\n")
        $nb--;
    $sep=-1;
    $i=0;
    $j=0;
    $l=0;
    $nl=1;
    while($i<$nb)
    {
        $c=$s[$i];
        if($c=="\n")
        {
            $i++;
            $sep=-1;
            $j=$i;
            $l=0;
            $nl++;
            continue;
        }
        if($c==' ')
            $sep=$i;
        $l+=$cw[$c];
        if($l>$wmax)
        {
            if($sep==-1)
            {
                if($i==$j)
                    $i++;
            }
            else
                $i=$sep+1;
            $sep=-1;
            $j=$i;
            $l=0;
            $nl++;
        }
        else
            $i++;
    }
    return $nl;
}






	


//-------------------------------------------------------------	
	function WriteHTML($html)  {
        //HTML parser
        $html=str_replace("\n",' ',$html);
        $a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
        foreach($a as $i=>$e)  {
            if($i%2==0)  {
                //Text
                if($this->HREF)
                    $this->PutLink($this->HREF,$e);
                elseif($this->ALIGN == 'center')
                    $this->Cell(0,5,$e,0,1,'C');
                else
                    $this->Write(5,$e);
            } else  {
                //Tag
                if($e{0}=='/')
                    $this->CloseTag(strtoupper(substr($e,1)));
                else  {
                    //Extract properties
                    $a2=split(' ',$e);
                    $tag=strtoupper(array_shift($a2));
                    $prop=array();
                    foreach($a2 as $v)
                        if(ereg('^([^=]*)=["\']?([^"\']*)["\']?$',$v,$a3))
                            $prop[strtoupper($a3[1])]=$a3[2];
					
					$this->OpenTag($tag,$prop);
                }
            }
        }
    }

    function OpenTag($tag,$prop)  {
        //Opening tag
        if($tag=='B' or $tag=='I' or $tag=='U')
            $this->SetStyle($tag,true);
        if($tag=='A')
            $this->HREF=$prop['HREF'];
        if($tag=='BR')
            $this->Ln(5);
        if($tag=='P')
            $this->ALIGN=$prop['ALIGN'];
        if($tag=='HR')  {
            if( $prop['WIDTH'] != '' )
                $Width = $prop['WIDTH'];
            else
                $Width = $this->w - $this->lMargin-$this->rMargin;
            $this->Ln(2);
            $x = $this->GetX();
            $y = $this->GetY();
            $this->SetLineWidth(0.4);
            $this->Line($x,$y,$x+$Width,$y);
            $this->SetLineWidth(0.2);
            $this->Ln(2);
        }
    }

    function CloseTag($tag) {
        //Closing tag
        if($tag=='B' or $tag=='I' or $tag=='U')
            $this->SetStyle($tag,false);
        if($tag=='A')
            $this->HREF='';
        if($tag=='P')
            $this->ALIGN='';
    }

    function SetStyle($tag,$enable) {
        //Modify style and select corresponding font
        $this->$tag+=($enable ? 1 : -1);
        $style='';
        foreach(array('B','I','U') as $s)
            if($this->$s>0)
                $style.=$s;
        $this->SetFont('',$style);
    }

    function PutLink($URL,$txt) {
        //Put a hyperlink
        $this->SetTextColor(0,0,255);
        $this->SetStyle('U',true);
        $this->Write(5,$txt,$URL);
        $this->SetStyle('U',false);
        $this->SetTextColor(0);
    }




}  // end of class

?>
