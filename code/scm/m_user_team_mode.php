<?php
define("DB_HOST","localhost");
define("DB_LOGIN","root");
define("DB_PASSWORD","");
define("DB_NAME","scm_test");
header('content-type:text/html;charset=big-5');
################################################################################################
# 新的

$A_Item[1][0]=array('dir','PRIMARY');
$A_Item[1][1][0]=array('1','customer',"primary.php?PHP_action=cust","main","001");
$A_Item[1][2][0]=array('2','supplier',"primary.php?PHP_action=supl","main","002");
$A_Item[1][3][0]=array('3','fabric',"primary.php?PHP_action=lots","main","003");
$A_Item[1][4][0]=array('4','accessory',"primary.php?PHP_action=acc","main","004");
$A_Item[1][5][0]=array('5','Old. Supl.',"$PHP_SELF?PHP_action=tmp_supl&PHP_supl_f_name=&PHP_vndr_no",'main',"005");
$A_Item[1][6][0]=array('6','Consignee',"consignee.php?PHP_action=consignee",'main',"006");
$A_Item[1][7][0]=array('7','Currency',"rate.php?PHP_action=rate","main",'007');  
  
$A_Item[2][0]=array('dir','SMPLE');
$A_Item[2][1][0]=array('8','sample order',"$PHP_SELF?PHP_action=smpl_ord","main","008");
$A_Item[2][2][0]=array('9','FAB/ACC',"$PHP_SELF?PHP_action=smpl_fa","main","009");
$A_Item[2][3][0]=array('10','W I/W S',"$PHP_SELF?PHP_action=smpl_wi","main","010");
$A_Item[2][4][0]=array('11','W I submit',"$PHP_SELF?PHP_action=smpl_wi_uncfm","main","011");
$A_Item[2][5][0]=array('12','BOM',"$PHP_SELF?PHP_action=smpl_bom","main","012");
$A_Item[2][6][0]=array('13','BOM submit',"$PHP_SELF?PHP_action=smpl_bom_uncfm","main","013");
$A_Item[2][7][0]=array('14','support',"$PHP_SELF?PHP_action=smpl_supplier","main","014");
$A_Item[2][8][0]=array('15','schedule',"$PHP_SELF?PHP_action=smpl_schedule","main","015");
$A_Item[2][9][0]=array('16','out-put',"$PHP_SELF?PHP_action=smpl_output","main","016");
#$A_Item[2][10][0]=array('17','SCH. rpt.',"smpl_sch.php?PHP_action=pd_schedule","main","017");
$A_Item[2][10][0]=array('17','SCH. rpt.',"sorry.html",'main','017');  
  
$A_Item[3][0]=array('dir','WI/WS/BOM');
$A_Item[3][1][0]=array('18','FAB/ACC',"$PHP_SELF?PHP_action=order_fa","main",'018');
$A_Item[3][2][0]=array('19','W I',"wi.php?PHP_action=wi","main",'019');
$A_Item[3][3][0]=array('20','W I submit',"wi.php?PHP_action=wi_uncfm",'main','020');
$A_Item[3][4][0]=array('21','WorkSheet',"$PHP_SELF?PHP_action=ti",'main','021');  
$A_Item[3][5][0]=array('22','BOM',"bom.php?PHP_action=bom","main",'022');
$A_Item[3][6][0]=array('23','BOM CFM',"bom.php?PHP_action=bom_uncfm",'main','023');
$A_Item[3][7][0]=array('24','FTY assort',"fty_bom.php?PHP_action=fty_bom",'main','024');
  
$A_Item[4][0]=array('dir','PDTION');
$A_Item[4][1][0]=array('25','I E',"ie.php?PHP_action=IE_record","main",'025');
$A_Item[4][2][0]=array('26','schedule',"schedule.php?PHP_action=pd_schedule","main",'026');
$A_Item[4][3][0]=array('27','CUTTING',"cutting.php?PHP_action=cutting","main",'027');
$A_Item[4][4][0]=array('28','Production',"monitor.php?PHP_action=monitor_daily","main",'028');
$A_Item[4][5][0]=array('29','SHIPPING',"ship.php?PHP_action=shipping","main",'029');
$A_Item[4][6][0]=array('30','C.M.',"cost.php?PHP_action=Remun","main",'030');
$A_Item[4][7][0]=array('31','Sales Cost',"cost.php?PHP_action=cost","main",'031');
$A_Item[4][8][0]=array('32','P/L',"shipdoc.php?PHP_action=packing","main",'032');
$A_Item[4][9][0]=array('33','CFM P/L',"shipdoc.php?PHP_action=cfm_packing","main",'033'); 
$A_Item[4][10][0]=array('34','ShpDoc',"shipdoc.php?PHP_action=shipdoc","main",'034');
$A_Item[4][11][0]=array('35','ShpDoc cfm',"shipdoc.php?PHP_action=cfm_shipdoc","main",'035');
$A_Item[4][12][0]=array('36','QC rpt.',"ord_qc.php?PHP_action=ord_qc","main",'036');  
  
$A_Item[5][0]=array('dir','PA / PO');
$A_Item[5][1][0]=array('37','PO.',"$PHP_SELF?PHP_action=apply",'main','037');
$A_Item[5][2][0]=array('38','王安',"po.php?PHP_action=po_ann",'main','038');
$A_Item[5][3][0]=array('39','SHIP',"po_ship.php?PHP_action=po_ship",'main','039');
$A_Item[5][4][0]=array('40','APB',"apb.php?PHP_action=apb",'main','040');
$A_Item[5][5][0]=array('41','CFM PO.',"po.php?PHP_action=po_cfm_search",'main','041');
$A_Item[5][6][0]=array('42','APV PO.',"po.php?PHP_action=po_apv_search",'main','042');
$A_Item[5][7][0]=array('43','Receiving',"receive.php?PHP_action=rcvd",'main','043');
$A_Item[5][8][0]=array('44','inspect rpt.',"rcv_rpt.php?PHP_action=lots_rpt",'main','044');
  
$A_Item[6][0]=array('dir','USER');
$A_Item[6][1][0]=array('45','team setting',"$PHP_SELF?PHP_action=team","main",'045');
$A_Item[6][2][0]=array('46','user setting',"$PHP_SELF?PHP_action=user&PHP_sort=dept","main",'046');
$A_Item[6][3][0]=array('47','access log',"$PHP_SELF?PHP_action=log_admin","main",'047');
$A_Item[6][4][0]=array('48','message','notify.php?PHP_action=notify','main','048');
  
$A_Item[7][0]=array('dir','CATEGORY');
$A_Item[7][1][0]=array('49','sample',"$PHP_SELF?PHP_action=smpl_type","main",'049');
$A_Item[7][2][0]=array('50','style',"$PHP_SELF?PHP_action=style_type","main",'050');
$A_Item[7][3][0]=array('51','------',"acc_cat.php?PHP_action=acc_cat","main",'051');
$A_Item[7][4][0]=array('52','fab. stock',"fabric.php?PHP_action=stock","main",'052');
$A_Item[7][5][0]=array('53','size scale',"$PHP_SELF?PHP_action=size_des","main",'053');
$A_Item[7][6][0]=array('54','department',"$PHP_SELF?PHP_action=dept","main",'054');

$A_Item[8][0]=array('dir','ADMIN.');
$A_Item[8][1][0]=array('55','CAPACITY',"$PHP_SELF?PHP_action=capacity_search","main",'055');
$A_Item[8][2][0]=array('56','ORD static',"pdt_static.php?PHP_action=ord_static","main",'056');
$A_Item[8][3][0]=array('57','Ord. sumry','pdt_static.php?PHP_action=pdt_search','main','057');
$A_Item[8][4][0]=array('58','Ord. Cost','cost_analysis.php?PHP_action=cost_analysis','main','058');
$A_Item[8][5][0]=array('59','Budget','expense.php?PHP_action=expense','main','059');
$A_Item[8][6][0]=array('60','Project',"project.php?PHP_action=main","main",'060');

$A_Item[9][0]=array('dir','SALES');
$A_Item[9][1][0]=array('61','Marker',"marker.php?PHP_action=marker_search","main",'061');
$A_Item[9][2][0]=array('62','OFFER',"offer.php?PHP_action=offer","main",'062');
$A_Item[9][3][0]=array('63','CUT PLAN',"fty_marker.php?PHP_action=marker_search","main",'063');
$A_Item[9][4][0]=array('64','CUT REPORT',"fty_marker.php?PHP_action=marker_rpt_search","main",'064');
  
$A_Item[10][0]=array('dir','ORDER');
$A_Item[10][1][0]=array('65','order rec.',"$PHP_SELF?PHP_action=order_entry","main",'065');
$A_Item[10][2][0]=array('66','CFM order',"$PHP_SELF?PHP_action=order_cfm","main",'066');
$A_Item[10][3][0]=array('67','APV order',"$PHP_SELF?PHP_action=order_apv","main",'067');
$A_Item[10][4][0]=array('68','mat. status',"$PHP_SELF?PHP_action=material_schedule","main",'068');
$A_Item[10][5][0]=array('69','Exceptional',"exception.php?PHP_action=exception","main",'069');
$A_Item[10][6][0]=array('70','debit note',"dabit.php?PHP_action=debit","main",'070');
$A_Item[10][7][0]=array('71','Fab. YY',"fab_comp.php?PHP_action=fab_comp","main",'071');
$A_Item[10][8][0]=array('72','reports',"$PHP_SELF?PHP_action=out_search","main",'072');

#################################################################################################################

# 舊的
$B_Item[1][0]=array('dir','PRIMARY');
$B_Item[1][1][0]=array('001','customer',"primary.php?PHP_action=cust","main","Customer");
$B_Item[1][2][0]=array('002','supplier',"primary.php?PHP_action=supl","main","Supplier");
$B_Item[1][3][0]=array('003','fabric',"primary.php?PHP_action=lots","main","Fabric");
$B_Item[1][4][0]=array('004','accessory',"primary.php?PHP_action=acc","main","Accessory");
$B_Item[1][5][0]=array('005','Old. Supl.',"$PHP_SELF?PHP_action=tmp_supl&PHP_supl_f_name=&PHP_vndr_no",'main',"Supl");
$B_Item[1][6][0]=array('006','Consignee',"consignee.php?PHP_action=consignee",'main',"Consignee");
$B_Item[1][7][0]=array('007','Currency',"rate.php?PHP_action=rate","main",'Currency');  
  
$B_Item[2][0]=array('dir','SMPLE');
$B_Item[2][1][0]=array('008','sample order',"$PHP_SELF?PHP_action=smpl_ord","main","sample order");
$B_Item[2][2][0]=array('009','FAB/ACC',"$PHP_SELF?PHP_action=smpl_fa","main","FAB/ACC");
$B_Item[2][3][0]=array('010','W I/W S',"$PHP_SELF?PHP_action=smpl_wi","main","W I");
$B_Item[2][4][0]=array('011','W I submit',"$PHP_SELF?PHP_action=smpl_wi_uncfm","main","CFM W I");
$B_Item[2][5][0]=array('012','BOM',"$PHP_SELF?PHP_action=smpl_bom","main","BOM");
$B_Item[2][6][0]=array('013','BOM submit',"$PHP_SELF?PHP_action=smpl_bom_uncfm","main","CFM BOM");
$B_Item[2][7][0]=array('014','support',"$PHP_SELF?PHP_action=smpl_supplier","main","supplies");
$B_Item[2][8][0]=array('015','schedule',"$PHP_SELF?PHP_action=smpl_schedule","main","smpl_schdl");
$B_Item[2][9][0]=array('016','out-put',"$PHP_SELF?PHP_action=smpl_output","main","out-put");
#$B_Item[2][10][0]=array('017','SCH. rpt.',"smpl_sch.php?PHP_action=pd_schedule","main","out-put");
$B_Item[2][10][0]=array('017','SCH. rpt.',"sorry.html",'main','out-put');  
  
$B_Item[3][0]=array('dir','WI/WS/BOM');
$B_Item[3][1][0]=array('018','FAB/ACC',"$PHP_SELF?PHP_action=order_fa","main",'FAB/ACC');
$B_Item[3][2][0]=array('019','W I',"wi.php?PHP_action=wi","main",'W I');
$B_Item[3][3][0]=array('020','W I submit',"wi.php?PHP_action=wi_uncfm",'main','CFM W I');
$B_Item[3][4][0]=array('021','WorkSheet',"$PHP_SELF?PHP_action=ti",'main','WorkSheet');  
$B_Item[3][5][0]=array('022','BOM',"bom.php?PHP_action=bom","main",'BOM');
$B_Item[3][6][0]=array('023','BOM CFM',"bom.php?PHP_action=bom_uncfm",'main','CFM BOM');
$B_Item[3][7][0]=array('024','FTY assort',"fty_bom.php?PHP_action=fty_bom",'main','FTY BOM');
  
$B_Item[4][0]=array('dir','PDTION');
$B_Item[4][1][0]=array('025','I E',"ie.php?PHP_action=IE_record","main",'');
$B_Item[4][2][0]=array('026','schedule',"schedule.php?PHP_action=pd_schedule","main",'schedule');
$B_Item[4][3][0]=array('027','CUTTING',"cutting.php?PHP_action=cutting","main",'cutting');
$B_Item[4][4][0]=array('028','Production',"monitor.php?PHP_action=monitor_daily","main",'monitor');
$B_Item[4][5][0]=array('029','SHIPPING',"ship.php?PHP_action=shipping","main",'SHIPPING');
$B_Item[4][6][0]=array('030','C.M.',"cost.php?PHP_action=Remun","main",'CM');
$B_Item[4][7][0]=array('031','Sales Cost',"cost.php?PHP_action=cost","main",'SALES COST');
$B_Item[4][8][0]=array('032','P/L',"shipdoc.php?PHP_action=packing","main",'PL');
$B_Item[4][9][0]=array('033','CFM P/L',"shipdoc.php?PHP_action=cfm_packing","main",'CFM PL'); 
$B_Item[4][10][0]=array('034','ShpDoc',"shipdoc.php?PHP_action=shipdoc","main",'SHIPDOC');
$B_Item[4][11][0]=array('035','ShpDoc cfm',"shipdoc.php?PHP_action=cfm_shipdoc","main",'CFM SHIPDOC');
$B_Item[4][12][0]=array('036','QC rpt.',"ord_qc.php?PHP_action=ord_qc","main",'QC');  
  
$B_Item[5][0]=array('dir','PA / PO');
$B_Item[5][1][0]=array('037','PO.',"$PHP_SELF?PHP_action=apply",'main','Apply');
$B_Item[5][2][0]=array('038','王安',"po.php?PHP_action=po_ann",'main','CFM Apply');
$B_Item[5][3][0]=array('039','SHIP',"po_ship.php?PHP_action=po_ship",'main','PO Ship');
$B_Item[5][4][0]=array('040','APB',"apb.php?PHP_action=apb",'main','APB');
$B_Item[5][5][0]=array('041','CFM PO.',"po.php?PHP_action=po_cfm_search",'main','CFM Po');
$B_Item[5][6][0]=array('042','APV PO.',"po.php?PHP_action=po_apv_search",'main','APV Po');
$B_Item[5][7][0]=array('043','Receiving',"receive.php?PHP_action=rcvd",'main','APV Po');
$B_Item[5][8][0]=array('044','inspect rpt.',"rcv_rpt.php?PHP_action=lots_rpt",'main','Lots rpt.');
  
$B_Item[6][0]=array('dir','USER');
$B_Item[6][1][0]=array('045','team setting',"$PHP_SELF?PHP_action=team","main",'team setting');
$B_Item[6][2][0]=array('046','user setting',"$PHP_SELF?PHP_action=user&PHP_sort=dept","main",'user setting');
$B_Item[6][3][0]=array('047','access log',"$PHP_SELF?PHP_action=log_admin","main",'access log');
$B_Item[6][4][0]=array('048','message','notify.php?PHP_action=notify','main','notify');
  
$B_Item[7][0]=array('dir','CATEGORY');
$B_Item[7][1][0]=array('049','sample',"$PHP_SELF?PHP_action=smpl_type","main",'sample');
$B_Item[7][2][0]=array('050','style',"$PHP_SELF?PHP_action=style_type","main",'style');
$B_Item[7][3][0]=array('051','------',"acc_cat.php?PHP_action=acc_cat","main",'Acc. Catagory');
$B_Item[7][4][0]=array('052','fab. stock',"fabric.php?PHP_action=stock","main",'season');
$B_Item[7][5][0]=array('053','size scale',"$PHP_SELF?PHP_action=size_des","main",'size scale');
$B_Item[7][6][0]=array('054','department',"$PHP_SELF?PHP_action=dept","main",'department');

$B_Item[8][0]=array('dir','ADMIN.');
$B_Item[8][1][0]=array('055','CAPACITY',"$PHP_SELF?PHP_action=capacity_search","main",'CAPACITY');
$B_Item[8][2][0]=array('056','ORD static',"pdt_static.php?PHP_action=ord_static","main",'PDT static');
$B_Item[8][3][0]=array('057','Ord. sumry','pdt_static.php?PHP_action=pdt_search','main','----');
$B_Item[8][4][0]=array('058','Ord. Cost','cost_analysis.php?PHP_action=cost_analysis','main','----');
$B_Item[8][5][0]=array('059','Budget','expense.php?PHP_action=expense','main','----');
$B_Item[8][6][0]=array('060','Project',"project.php?PHP_action=main","main",'----');

$B_Item[9][0]=array('dir','SALES');
$B_Item[9][1][0]=array('061','Marker',"marker.php?PHP_action=marker_search","main",'MARKER');
$B_Item[9][2][0]=array('062','OFFER',"offer.php?PHP_action=offer","main",'OFFER');
$B_Item[9][3][0]=array('063','CUT PLAN',"fty_marker.php?PHP_action=marker_search","main",'CUT_PLAN');
$B_Item[9][4][0]=array('064','CUT REPORT',"fty_marker.php?PHP_action=marker_rpt_search","main",'CUT_REPORT');
  
$B_Item[10][0]=array('dir','ORDER');
$B_Item[10][1][0]=array('065','order rec.',"$PHP_SELF?PHP_action=order_entry","main",'order_rec');
$B_Item[10][2][0]=array('066','CFM order',"$PHP_SELF?PHP_action=order_cfm","main",'CFM_ord');
$B_Item[10][3][0]=array('067','APV order',"$PHP_SELF?PHP_action=order_apv","main",'APV_ord');
$B_Item[10][4][0]=array('068','mat. status',"$PHP_SELF?PHP_action=material_schedule","main",'mat_rcvd');
$B_Item[10][5][0]=array('069','Exceptional',"exception.php?PHP_action=exception","main",'production');
$B_Item[10][6][0]=array('070','debit note',"dabit.php?PHP_action=debit","main",'debit note');
$B_Item[10][7][0]=array('071','Fab. YY',"fab_comp.php?PHP_action=fab_comp","main",'order status');
$B_Item[10][8][0]=array('072','reports',"$PHP_SELF?PHP_action=out_search","main",'reports');
	


/*

$B_Item[1][0]=array('dir','PRIMARY');
$B_Item[1][1][0]=array('001','customer',"$PHP_SELF?PHP_action=cust","main","Customer");
$B_Item[1][2][0]=array('002','supplier',"$PHP_SELF?PHP_action=supl","main","Supplier");
$B_Item[1][3][0]=array('003','fabric',"$PHP_SELF?PHP_action=lots","main","Fabric");
$B_Item[1][4][0]=array('004','accessory',"$PHP_SELF?PHP_action=acc","main","Accessory");
$B_Item[1][5][0]=array('005','Old. Supl.',"$PHP_SELF?PHP_action=tmp_supl&PHP_supl_f_name=&PHP_vndr_no",'main',"Supl");

$B_Item[2][0]=array('dir','SMPLE');
$B_Item[2][1][0]=array('006','sample order',"$PHP_SELF?PHP_action=smpl_ord","main","sample order");
$B_Item[2][2][0]=array('007','FAB/ACC',"$PHP_SELF?PHP_action=smpl_fa","main","FAB/ACC");
$B_Item[2][3][0]=array('008','W I/W S',"$PHP_SELF?PHP_action=smpl_wi","main","W I");
//$B_Item[2][4][0]=array('txt','Worksheet',"$PHP_SELF?PHP_action=Worksheet","main","Worksheet");
$B_Item[2][4][0]=array('009','W I submit',"$PHP_SELF?PHP_action=smpl_wi_uncfm","main","CFM W I");
$B_Item[2][5][0]=array('010','BOM',"$PHP_SELF?PHP_action=smpl_bom","main","BOM");
$B_Item[2][6][0]=array('011','BOM submit',"$PHP_SELF?PHP_action=smpl_bom_uncfm","main","CFM BOM");
$B_Item[2][7][0]=array('012','support',"$PHP_SELF?PHP_action=smpl_supplier","main","supplies");
$B_Item[2][8][0]=array('013','schedule',"$PHP_SELF?PHP_action=smpl_schedule","main","smpl_schdl");
$B_Item[2][9][0]=array('014','out-put',"$PHP_SELF?PHP_action=smpl_output","main","out-put");

$B_Item[3][0]=array('dir','WI/WS/BOM');
$B_Item[3][1][0]=array('016','FAB/ACC',"$PHP_SELF?PHP_action=order_fa","main",'FAB/ACC');
$B_Item[3][2][0]=array('015','W I',"$PHP_SELF?PHP_action=wi","main",'W I');
$B_Item[3][3][0]=array('017','W I submit',"$PHP_SELF?PHP_action=wi_uncfm",'main','CFM W I');
$B_Item[3][4][0]=array('018','WorkSheet',"$PHP_SELF?PHP_action=ti",'main','WorkSheet');  
$B_Item[3][5][0]=array('019','BOM',"$PHP_SELF?PHP_action=bom","main",'BOM');
$B_Item[3][6][0]=array('020','BOM submit',"$PHP_SELF?PHP_action=bom_uncfm",'main','CFM BOM');

$B_Item[4][0]=array('dir','PDTION');
$B_Item[4][1][0]=array('021','I E',"$PHP_SELF?PHP_action=IE_record","main",'');
$B_Item[4][2][0]=array('022','schedule',"$PHP_SELF?PHP_action=pd_schedule","main",'schedule');
$B_Item[4][3][0]=array('023','CFM SCHDL',"$PHP_SELF?PHP_action=cfm_pd_schedule","main",'CFM_SCHDL');
$B_Item[4][4][0]=array('024','production',"$PHP_SELF?PHP_action=production","main",'production');
$B_Item[4][5][0]=array('025','SHIPPING',"$PHP_SELF?PHP_action=shipping","main",'SHIPPING');

$B_Item[5][0]=array('dir','PA / PO');
$B_Item[5][1][0]=array('026','PA.',"$PHP_SELF?PHP_action=apply",'main','Apply');
$B_Item[5][2][0]=array('027','CFM. PA.',"$PHP_SELF?PHP_action=apply_cfm_search",'main','CFM Apply');
$B_Item[5][3][0]=array('028','APV. PA.',"$PHP_SELF?PHP_action=apply_apv_search",'main','APV Apply');
$B_Item[5][4][0]=array('029','PO.',"po.php?PHP_action=po",'main','Po');
$B_Item[5][5][0]=array('030','CFM PO.',"po.php?PHP_action=po_cfm_search",'main','CFM Po');
$B_Item[5][6][0]=array('053','APV PO.',"po.php?PHP_action=po_apv_search",'main','APV Po');
$B_Item[5][7][0]=array('054','notify','notify.php?PHP_action=notify','main','notify');

$B_Item[6][0]=array('dir','USER');
$B_Item[6][1][0]=array('052','GroupSetting',"group_setting.php?PHP_action=group",'main','GroupSetting');
$B_Item[6][2][0]=array('031','team setting',"$PHP_SELF?PHP_action=team","main",'team setting');
$B_Item[6][3][0]=array('032','user setting',"$PHP_SELF?PHP_action=user&PHP_sort=dept","main",'user setting');
$B_Item[6][4][0]=array('033','access log',"$PHP_SELF?PHP_action=log_admin","main",'access log');

$B_Item[7][0]=array('dir','CATEGORY');
$B_Item[7][1][0]=array('034','sample',"$PHP_SELF?PHP_action=smpl_type","main",'sample');
$B_Item[7][2][0]=array('035','style',"$PHP_SELF?PHP_action=style_type","main",'style');
$B_Item[7][3][0]=array('036','supplier',"$PHP_SELF?PHP_action=supl_type","main",'supplier');
$B_Item[7][4][0]=array('037','season',"$PHP_SELF?PHP_action=season","main",'season');
$B_Item[7][5][0]=array('038','size scale',"$PHP_SELF?PHP_action=size_des","main",'size scale');
$B_Item[7][6][0]=array('039','department',"$PHP_SELF?PHP_action=dept","main",'department');

$B_Item[8][0]=array('dir','ADMIN.');
$B_Item[8][1][0]=array('040','CAPACITY',"$PHP_SELF?PHP_action=capacity_search","main",'CAPACITY');
$B_Item[8][2][0]=array('041','ORD static',"$PHP_SELF?PHP_action=pdt_search","main",'PDT static');
$B_Item[8][3][0]=array('042','----','sorry.html','main','----');

$B_Item[9][0]=array('dir','SALES');
$B_Item[9][1][0]=array('043','FORECAST',"$PHP_SELF?PHP_action=sales_forecast","main",'FORECAST');
$B_Item[9][2][0]=array('044','OFFER',"$PHP_SELF?PHP_action=offer","main",'OFFER');

$B_Item[10][0]=array('dir','ORDER');
$B_Item[10][1][0]=array('045','order rec.',"$PHP_SELF?PHP_action=order_entry","main",'order_rec');
$B_Item[10][2][0]=array('046','CFM order',"$PHP_SELF?PHP_action=order_cfm","main",'CFM_ord');
$B_Item[10][3][0]=array('047','APV order',"$PHP_SELF?PHP_action=order_apv","main",'APV_ord');
$B_Item[10][4][0]=array('048','mat. status',"$PHP_SELF?PHP_action=material_schedule","main",'mat_rcvd');
$B_Item[10][5][0]=array('049','monitor(TMP)',"monitor.php?PHP_action=monitor","main",'monitor');
$B_Item[10][6][0]=array('050','ord. status',"$PHP_SELF?PHP_action=ord_schedule","main",'order status');
$B_Item[10][7][0]=array('051','reports',"$PHP_SELF?PHP_action=out_search","main",'reports');

*/

	$ADMIN_PERM_DEF = array( "view" => 1 , "add" => 2 , "edit" => 4 , "del" => 8 , "group" => 16 , "all" => 32);
	$ADMIN_PERM = array( "1" => "view" , "2" => "add" , "3" => "edit" , "4" => "del" , "5" => "group" , "6" => "all" );
	$p_job = array( "1" => "View" , "2" => "Append" , "3" => "Edit" , "4" => "Delete" , "5" => "VGroup" , "6" => "VAll" );

###############################################################################################################
#echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
function Connect_db(){
	if(empty($link_db)){
		$link_db = @mysql_connect(DB_HOST, DB_LOGIN, DB_PASSWORD);
		if(!$link_db){
			echo ("FATAL:Couldn't connect to db.<br>");
			exit;
		}
	}
	
	$_SESSION["Select_DB"] = @mysql_select_db(DB_NAME, $link_db);
	if(!$_SESSION["Select_DB"]){
		echo ("FATAL:Couldn't connect to db.<br>");
		exit;
	}
	// mysql_query("SET NAMES UTF8");
	// $sql = "SET NAMES UTF8";
	// $result = mysql_query($sql);
	// if (!$result){
 		// echo "Could not successfully run query (".$sql.") from DB: " . mysql_error();
		// exit;
	// }
}

# 要改第幾 X 個值
// $user_num = '0';

# 要換的值
// $user_val = '63';

# table
// $table = 'user';		$up_table = 'user';		$sql = "SELECT id,name,perm FROM `".$table."`";
// $table = 'team';	$up_table = 'team';		$sql = "SELECT id,team_name ,perm FROM `".$table."`";


if ($table){
	Connect_db();
	$tmp = '';
	$res = mysql_query($sql);
	while($tmp = mysql_fetch_array($res)){
		$name[$tmp['id']] = $tmp['name'];
		$per[$tmp['id']] = explode(',',$tmp['perm']);
	}

	#print_r($per);
	#[1] => Array ( [0] => 7 [1] => 7 [2] => 7 [3] => 7 [4] => 7 [5] => 0 [6] => 0 [7] => 0 [8] => 0 [9] => 0 [10] => 0 [11] => 0 [12] => 7 [13] => 7 [14] => 7 [15] => 7 [16] => 7 [17] => 0 [18] => 0 [19] => 0 [20] => 0 [21] => 15 [22] => 15 [23] => 15 [24] => 9 [25] => 0 [26] => 7 [27] => 0 [28] => 0 [29] => 0 [30] => 7 [31] => 7 [32] => 1 [33] => 0 [34] => 7 [35] => 1 [36] => 7 [37] => 3 [38] => 3 [39] => 3 [40] => 0 [41] => 1 [42] => 1 [43] => 0 [44] => 1 [45] => 1 [46] => 1 [47] => 1 [48] => 1 [49] => 1 [50] => 1 ) 
	#[2] => Array ( [0] => 7 [1] => 7 [2] => 7 [3] => 7 [4] => 7 [5] => 7 [6] => 7 [7] => 7 [8] => 7 [9] => 7 [10] => 7 [11] => 1 [12] => 1 [13] => 1 [14] => 7 [15] => 7 [16] => 7 [17] => 7 [18] => 7 [19] => 7 [20] => 1 [21] => 1 [22] => 1 [23] => 1 [24] => 1 [25] => 0 [26] => 0 [27] => 15 [28] => 0 [29] => 0 [30] => 1 [31] => 15 [32] => 1 [33] => 0 [34] => 7 [35] => 0 [36] => 0 [37] => 7 [38] => 1 [39] => 1 [40] => 1 [41] => 0 [42] => 7 [43] => 5 [44] => 7 [45] => 3 [46] => 1 [47] => 3 [48] => 1 [49] => 1 [50] => 1 ) 
	#[3] => Array ( [0] => 7 [1] => 0 [2] => 0 [3] => 0 [4] => 0 [5] => 7 [6] => 1 [7] => 1 [8] => 1 [9] => 1 [10] =>
	
	#舊的選單陣列 [pop1] 比照舊的選單陣列順序產生的權限續號 - 灌上新的選單id	←比照資料庫裡的權限順序
	$pop1=''; 
	for($k0=1; $k0 < count($B_Item)+1; $k0++){
		for($k1=0; $k1 < count($B_Item[$k0]); $k1++){
			if ( $k1 != 0 ){
				$pop[] = $B_Item[$k0][$k1][0][0];
			}
		}
	}
	
	# print_r($pop);
	#	[0] => 001
	#	[1] => 002
	#	[2] => 003
	#	[3] => 004
  

	# 比對舊的選單 和 更改會員的權限	和新的比對 新的選單和舊選單比對合併 [pop3]

	$user_num = array();
	foreach($per AS $key => $valus ){
		$perm = '';
		foreach($valus AS $_keys => $_vals ){
			$perm = $perm.$pop[$_keys].'-'.$_vals.',';
		}
		$perm = substr($perm,0,-1);
		$user_perm[$key] = $perm;
		
	}
	#print_r($tmp);

#---------------------------------------------------------------------------------------------------------------
	foreach($per as $key => $val){
		#$name[$key] = trim($name[$key]);
		$sql = "update `".($up_table)."` set perm='".($user_perm[$key])."' where id='".($key)."'";
		// echo $sql."<br>";
		if (mysql_query($sql)){
			if(empty($name[$key]))$name[$key]='---無名---';
			echo "[".$name[$key]."] 轉換 OK <br>";
		}
	}
}


		foreach ($A_Item AS $key => $val ){
			$m = $key;
			print "<table width=100%><tr>";
			foreach ($A_Item[$m] AS $key => $val ){
				$o = $key;
				if ( $o != 0 ){
					# comtent
					if ($o == 1){
						print "<td class=ssign align=right width=130 nowrap>".$m.".".$A_Item[$m][0][1]."：</td>";
					}else{
						print "<td class=ssign align=left width=180 nowrap></td>";
					}
					print "<td class=b10 nowrap>[ ".$o." ] ".$A_Item[$m][$o][0][1]."</td>";
					# item
					print "<td width='60%' bgcolor='#CEE4E3' class=frnt>";
					for($bm=1;$bm<count($B_Item)+1;$bm++){

						for($bo=0;$bo<count($B_Item[$bm]);$bo++){
							if ( $bo != 0 ){
								#print "[ ".$A_Item[$m][$o][0][0]."<>";
								#print $B_Item[$bm][$bo][0][0]." ] -- ";
								if($A_Item[$m][$o][0][0] == $B_Item[$bm][$bo][0][0]){
									print "[ ".$B_Item[$bm][$bo][0][1]." ]";
									print $B_Item[$bm][$bo][0][0]." -- ";
									print $A_Item[$m][$o][0][4]." -- ";
								}else{
									#print "[ ".$A_Item[$m][$o][0][0]."<>";
									#print $B_Item[$bm][$bo][0][1]." ] -- ";
								}
							}
						}
					}#print "　　　　　　　　　　　　　-- ".$A_Item[$m][$o][0][1].$A_Item[$m][$o][0][0]." --";
					print "</td>";
					print "</tr>";
				}
			}
			print "<tr><td colspan=9 class=frnt align=center><hr width=100% size=2 /></td></tr></table>";
		}


?>

