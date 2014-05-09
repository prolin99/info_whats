<?php
//  ------------------------------------------------------------------------ //
// 本模組由 無名氏 製作
// 製作日期：2014-02-16
// $Id:$
// ------------------------------------------------------------------------- //
//引入TadTools的函式庫
if(!file_exists(XOOPS_ROOT_PATH."/modules/tadtools/tad_function.php")){
 redirect_header("http://www.tad0616.net/modules/tad_uploader/index.php?of_cat_sn=50",3, _TAD_NEED_TADTOOLS);
}
include_once XOOPS_ROOT_PATH."/modules/tadtools/tad_function.php";


/********************* 自訂函數 *********************/

/********************* 預設函數 *********************/


function get_mac() {
	global $xoopsModuleConfig ;
	//如果不是在校內，傳回空值
   	//取得 IP (可能ipv6 或 ipv4)
  	if ($_SERVER['HTTP_X_FORWARDED_FOR']){ 
     		$remoIP=$_SERVER['HTTP_X_FORWARDED_FOR']; 
  	}  else { 
     		$remoIP=$_SERVER['REMOTE_ADDR']; 
  	}   
  	 
  	//是否在本校網段
	//ipv4
  	$ip4_array = preg_split('/,/' , $xoopsModuleConfig['iw_ip_v4'] ) ;
  	foreach ($ip4_array  as $k => $ipv4_v ) 
  		$pos[] = stripos($remoIP,  $ipv4_v  );	
  	
  	//ipv6 
  	$pos[] = stripos($remoIP,  $xoopsModuleConfig['iw_ip_v6']  );
  	 
  	foreach ($pos as $k => $v) {
		if  ($v !== false  )
		  	$in_school = true  ; 
  	}
  	
  	//dcs 問題 ：120.116.24.31:33748
	$remoIP_array = preg_split('/:/' , $remoIP ) ;
	if  (count($remoIP_array) ==2) 
	 	$remoIP=$remoIP_array[0] ;
	
  	if  ($in_school) {
		$data['ip'] =$remoIP ;

  		//取得  mac ，但得在同一網域中才能
  		//echo "/sbin/ip neigh |grep $remoIP "  ;		
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			$arp=`arp -a $remoIP`;
			$lines=explode("\n", $arp);

			#look for the output line describing our IP address
			foreach($lines as $line)		{
				$cols=preg_split('/\s+/', trim($line));
				if ($cols[0]==$remoIP)
					$data['mac'] =$cols[1];
			}
			
		}else {	
			
			//LINUX 由 ip neigh 中找相符列，再切開取得 mac 卡號
			
			$ip_list =  exec("/sbin/ip neigh |grep $remoIP " ) ;
			$ipv6_arr = preg_split('/\s+/' ,$ip_list ) ;
			$data['mac'] =  $ipv6_arr[4] ;
		}
 
	}	

	return $data ;
}



function get_from_data($uid,$ip, $mac) {
	global $xoopsDB;
	if  ($ip) {
		//$sql = " select id , ip , user ,  place   from " . $xoopsDB->prefix("mac_input")  ." where ip ='$ip' and $uid='$uid'  " ;
		if  ($mac)
			$sql = " select id , ip , user ,  place   from " . $xoopsDB->prefix("mac_input")  ." where mac ='$mac'  order by id DESC  " ;
		else 
			$sql = " select id , ip , user ,  place   from " . $xoopsDB->prefix("mac_input")  ." where ip ='$ip'  order by id DESC  " ;
 		$result = $xoopsDB->query($sql) or die($sql."<br>". mysql_error()); 		
 		$data_list=$xoopsDB->fetchArray($result) ;
		
 		return $data_list ;
	}		
}	


function get_from_rec($uid, $ip ,$mac) {
	global $xoopsDB;
	if  ($ip or $mac) {
		if  ($mac) 
			$sql = " select comp, ps  from " . $xoopsDB->prefix("mac_info")  ." where   mac ='$mac'   " ;
		else			
			$sql = " select comp, ps  from " . $xoopsDB->prefix("mac_info")  ." where  ip ='$ip'      " ;
 		$result = $xoopsDB->query($sql) or die($sql."<br>". mysql_error()); 		
 		$data_list=$xoopsDB->fetchArray($result) ;
		
 		return $data_list ;
	}		
}