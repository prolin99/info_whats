<?php
 include_once "header.php";

if ($_GET['id'] ) {
    $v = $_GET['gv']+0 ;
	$id_k = preg_split("/[_]/", $_GET['id']);

     	$sql = " update " . $xoopsDB->prefix("mac_info") .  " set  point =  '$v'  where id='{$id_k[0]}' " ;

      	$result = $xoopsDB->queryF($sql) or die($sql."<br>". mysql_error());

echo $sql ;
}
