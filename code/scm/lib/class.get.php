<?php



class M_GET {



/*----------
# 功能說明 : 不同身分搜尋
# 關聯變數 : 
# 輸入變數 : 
# 輸出變數 : 
# 撰寫日期 : 2010/06/01
# 備註說明 : 
----------*/
function get_fields($n_field,$table,$where='',$code='',$vars='') {
  global $MySQL;
  
  if($where=='dept'){
    $where_str =" WHERE dept = '".$code."' ";
  }

  if($where=='cust'){
    $where_str =" WHERE cust = '".$code."' ";
  }

  if($code=='' && $vars){
    $where_str = " WHERE ".$vars." ";
  }

  if($code=='' && $vars==''){
    $where_str = "";
  }

  if($where=='factory'){
    $where_str = " WHERE ".$code." ";
  }
  
  $t2 = '';
  if($where=='smpl_ord'){
    $where_str = " WHERE ( smpl_ord.dept = '".$code."' ) AND ( smpl_ord.status <> '10,7' ) ";
    $table .= " JOIN smpl_ord ON ( cust.cust_s_name = smpl_ord.cust ) ";
    $t2 = 1;
  }

  $fields = array();
  $q_str = "SELECT DISTINCT ".$n_field." FROM ".$table." ".$where_str;
  #echo '<p>',$q_str,'<p>';
  if (!$q_result = $MySQL->query($q_str)) {
    $_SESSION['SQL']['msg']->add("Error! Database can't access!");
    $_SESSION['SQL']['msg']->merge($MySQL->msg);
    return false;    
  }

  if ( $table == 'cust' || $t2){
    while ($row = $MySQL->fetch($q_result)) {
      if (!empty($row[0]))
      $fields[] = $row[0].' - '.$row[1];
    }
  }else{
    while ($row = $MySQL->fetch($q_result)) {
      $fields[] = $row[0];
    }
  }

  if(is_array($fields))sort($fields);
  return $fields;
} // end func



/*----------
# 功能說明 : 
# 關聯變數 : 
# 輸入變數 : 
# 輸出變數 : 
# 撰寫日期 : 2010/06/01
# 備註說明 : 
----------*/
function cust_sql($str) {
	global $MySQL;
  
	$q_sql = "SELECT DISTINCT `cust` FROM `smpl_ord` WHERE ".$str." ";
	$q_result = $MySQL->query($q_sql);
	while ($row = $MySQL->fetch($q_result)) {
		$fields[$row[0]] = $row[0];
	}
	return $fields;
}



/*----------
# 功能說明 : 組 sql 字串
# 關聯變數 : 
# 輸入變數 : 
# 輸出變數 : 
# 撰寫日期 : 2010/06/01
# 備註說明 : 
----------*/
function get_cust($field) {
  $str2 = '';
  foreach ( $field as $key => $val ) {
    $val = substr($val,0,2);
    $str .= " or ( `cust` = '".$val."' ) ";
  }
  $str = substr($str,4);
  $fields = $this->cust_sql($str);
  foreach ( $field as $key => $val ) {
    $str = substr($val,0,2);
    if($fields[$str])
      $cust[]=$val;
  }

  return $cust;
}




}  // end class

?>