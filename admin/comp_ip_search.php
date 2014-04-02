<?php
//$Id: comp.php,v 1.1.2.6 2005/11/01 15:33:15 brucelyc Exp $
include "config.php";
//匯入資料

 
if ($_GET['sip'] ) {
	echo '查詢 ip : ' . $_GET['sip']   ."\n" ;
 
	$sqlstr = " select log.*  , teach.name  from login_log_new  log  , teacher_base teach  
		where   log.ip = '$_GET[sip] '   and log.teacher_sn = teach.teacher_sn
		ORDER BY  log.`login_time` DESC   LIMIT 0 , 5 " ;
 	//echo $sqlstr  ;
 
         	$recordSet = $CONN->Execute($sqlstr);
         	while ($row = $recordSet->FetchRow() ) {
               	 echo $row['login_time']    .  $row['name']   ."\n" ;

         	} 
 
 
}