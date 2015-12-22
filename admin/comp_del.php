<?php 
 include_once "header.php";
if ($_GET['del_id'] ) {
	$id_array = preg_split('/_/',$_GET['del_id'] ) ;
	$id = $id_array[1]  ;
	if ( $id >0 ) {
  		$sql = " delete from " . $xoopsDB->prefix("mac_info") .  " where id='$id' " ;
 		$result = $xoopsDB->queryF($sql) or die($sql."<br>". mysql_error()); 					
 
     		echo "delete $id  " ;
     	}else {
     		echo "delete error  {$_GET['del_id'] }  " ;
     	}
}     	