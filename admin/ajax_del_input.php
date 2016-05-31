<?php 
 include_once "header.php";
if ($_GET['del_id'] ) {
	$id = intval($_GET['del_id'] ) ;

	if ( $id >0 ) {
  		$sql = " delete from " . $xoopsDB->prefix("mac_input") .  " where id='$id' " ;
 		$result = $xoopsDB->queryF($sql) or die($sql."<br>". $xoopsDB->error()); 					
 
     		echo "delete $id  " ;
     	}else {
     		echo "delete error  {$_GET['del_id'] }  " ;
     	}
}     	