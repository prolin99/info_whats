<?php
//  ------------------------------------------------------------------------ //
// 本模組由 無名氏 製作
// 製作日期：2014-02-16
// $Id:$
// ------------------------------------------------------------------------- //
//引入TadTools的函式庫
if(!file_exists(TADTOOLS_PATH."/tad_function.php")){
 redirect_header("http://www.tad0616.net/modules/tad_uploader/index.php?of_cat_sn=50",3, _TAD_NEED_TADTOOLS);
}
include_once TADTOOLS_PATH."/tad_function.php";

/********************* 自訂函數 *********************/

/********************* 預設函數 *********************/
//圓角文字框
function div_3d($title="",$main="",$kind="raised",$style="",$other=""){
	$main="<table style='width:auto;{$style}'><tr><td>
	<div class='{$kind}'>
	<h1>$title</h1>
	$other
	<b class='b1'></b><b class='b2'></b><b class='b3'></b><b class='b4'></b>
	<div class='boxcontent'>
 	$main
	</div>
	<b class='b4b'></b><b class='b3b'></b><b class='b2b'></b><b class='b1b'></b>
	</div>
	</td></tr></table>";
	return $main;
}

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
	
  	if  ($in_school) {
		$data['ip'] =$remoIP ;
 		//由 ip neigh 中找相符列，再切開取得 mac 卡號
 		$ip_list =  exec("ip neigh |grep $remoIP " ) ;
 		$ipv6_arr = preg_split('/\s+/' ,$ip_list ) ;
 		$data['mac'] =  $ipv6_arr[4] ;
 
	}	
	return $data ;
}



function get_from_data($uid,$ip) {
	global $xoopsDB;
	if  ($ip) {
		$sql = " select id , ip , user ,  place   from " . $xoopsDB->prefix("mac_input")  ." where ip ='$ip' and $uid='$uid'  " ;
 		$result = $xoopsDB->query($sql) or die($sql."<br>". mysql_error()); 		
 		$data_list=$xoopsDB->fetchArray($result) ;
		
 		return $data_list ;
	}		
}	