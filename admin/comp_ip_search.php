<?php

if ($_GET['sip'] ) {
	echo '查詢 ip : ' . $_GET['sip']   ."\n" ;

	$ip_list =  shell_exec("nmap  --script smb-os-discovery -p 445 -Pn  " .  $_GET['sip']  ) ;
	echo $ip_list  ;

}
