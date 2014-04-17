<?php
//$Id: comp.php,v 1.1.2.6 2005/11/01 15:33:15 brucelyc Exp $
include "config.php";
//匯入資料

 
if ($_GET['sip'] ) {
	echo '查詢 ip : ' . $_GET['sip']   ."\n" ;
    $myts =& MyTextSanitizer::getInstance();
    $_GET['sip']=$myts->addSlashes($_GET['sip']);

	$ip_list =  shell_exec("nmap  --script smb-os-discovery -p 445 -Pn  " .  $_GET['sip']  ) ;
	echo $ip_list  ;
 
 
 
}