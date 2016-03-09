<?php
header("Content-type: application/octet-stream");
header("Content-Transfer-Encoding: binary");
header("Pragma:public");
header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
header("Content-Disposition: attachment; filename=".$_GET['fname']."");
header("Pragma: no-cache");
header("Expires: 0");
readfile("./".$_GET['fdir']."/".$_GET['fname']); 

?>