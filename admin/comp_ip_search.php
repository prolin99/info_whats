<?php

if ($_GET['sip'] ) {
	echo '查詢 ip : ' . $_GET['sip']   ."\n" ;
	$success = preg_match('/^(\d{1,3}\.){3}\d{1,3}$/',  $_GET['sip']);
	if ($success) {
 		$ip_list =  shell_exec("nmap  -Pn -p U:53,1900,T:80,135,139,445,1539,8080,9100 " .  $_GET['sip']  ) ;
	}else {
		//ipv6
		$ip_list =  shell_exec("nmap -6  -Pn -p U:53,1900,T:80,135,139,445,1539,8080,9100 " .  $_GET['sip']  ) ;
	}

	//$ip_list =  shell_exec("nmap  --script smb-os-discovery -p 445 -Pn  " .  $_GET['sip']  ) ;
	//$ip_list =  shell_exec("nmap  -sn -T2 " .  $_GET['sip']  ) ;
	echo $ip_list  ;

}
