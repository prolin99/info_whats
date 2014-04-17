<?php
 include_once "header.php";
 //標記為重要
if ($_GET['id'] ) {
	$id_k = preg_split("/[_]/", $_GET['id']);
    //一定要為數字
    if (is_int($id_k) ){
     	$sql = " update " . $xoopsDB->prefix("mac_info") .  " set  point = not point  where id='{$id_k[0]}' " ; 

      	$result = $xoopsDB->queryF($sql) or die($sql."<br>". mysql_error());
    }
 
 
}