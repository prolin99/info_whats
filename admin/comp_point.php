<?php
 include_once "header.php";
 
if ($_GET['id'] ) {
	$id_k = preg_split("/[_]/", $_GET['id']);
 
     	$sql = " update " . $xoopsDB->prefix("mac_info") .  " set  point = not point  where id='{$id_k[0]}' " ; 

      	$result = $xoopsDB->queryF($sql) or die($sql."<br>". mysql_error()); 			
 
 
}