<?php
//  ------------------------------------------------------------------------ //
// 本模組由 prolin 製作
// 製作日期：2014-03-01
// $Id:$
// ------------------------------------------------------------------------- //

/*-----------引入檔案區--------------*/
include_once "header_admin.php";
//樣版
$xoopsOption['template_main'] = "info_admin_tpl.html";
include_once "header.php";
 

/*-----------function區--------------*/
//

 
/*-----------執行動作判斷區----------*/
//$op = empty($_REQUEST['op'])? "":$_REQUEST['op'];

if ($_POST['Submit_add'] ) {
	//手動加入 mac 
	$_POST['new_mac']= strtoupper(trim($_POST['new_mac']) ) ;
	$sql = " insert into  " . $xoopsDB->prefix("mac_info") .  "  (id ,ip ,mac ,recode_time ,creat_day ,ip_id)  
				               values ('','','{$_POST['new_mac']}',now() , now() ,'' ) " ;
	$result = $xoopsDB->query($sql) or die($sql."<br>". mysql_error()); 			
}	


if ($_POST['btn_clear'] ) {
	//清除登記填報
	$sql = " TRUNCATE TABLE  " . $xoopsDB->prefix("mac_input")   ;
	$result = $xoopsDB->queryF($sql) or die($sql."<br>". mysql_error()); 			
}	
 
 //=======================================================================
 	//取得偏好設定
  	$data['ip_rang'] = $xoopsModuleConfig['iw_ip_rang'] ;					//分配規劃
 	$data['ipv4'] = $xoopsModuleConfig['iw_ip_v4'] ;						//ipv4 網段
 	$data['ipv6'] = $xoopsModuleConfig['iw_ip_v6'] ;						//ipv6 網段
 	$dhcp_rang = preg_split('/~/',$xoopsModuleConfig['iw_ip_v4_dhcp']) ;	//動態分配區

 	$dhcp_begin = preg_split('/[\.]/' ,$dhcp_rang[0]) ;
 
 	$dhcp_end = preg_split('/[\.]/' ,$dhcp_rang[1]) ;
 	$dhcp_prefix= $dhcp_begin[0] . '.'  . $dhcp_begin[1] . '.'  . $dhcp_begin[2]   ;
 	$dhcp_last_beg= $dhcp_begin[3] ; 
 	$dhcp_last_end= $dhcp_end[3] ; 
 	//echo $dhcp_prefix ;
 
 	
	//取得登記資料
	$sql = " select *  from " . $xoopsDB->prefix("mac_input") . "  order by mac "  ;
 	$result = $xoopsDB->query($sql) or die($sql."<br>". mysql_error()); 		
 	while($row=$xoopsDB->fetchArray($result)){

		$row["mac"]=strtoupper($row["mac"]) ;
   		$input_data[$row['mac']] .= $row['user']  .'-' . $row['place'];
 	}       	
 	
 //取得最近時間 
 	$sql = " select * from " . $xoopsDB->prefix("mac_info") .  " order by recode_time DESC  " ;
 	$result = $xoopsDB->query($sql) or die($sql."<br>". mysql_error()); 			
 	$date_list=$xoopsDB->fetchArray($result) ;
 	 $last_recode_time = $date_list['recode_time'] ;
 	 
//排序 	 
	if ($_POST['sort']) 
		$_GET['sort'] = $_POST['sort'] ;
	if ($_GET['sort']) 
		$sortby =$_GET['sort'] ;
	else 
		$sortby = 'id' ;
	if (  $sortby=='id' ) $sortby = 'id DESC' ;
 
  	$sql = " select * from " . $xoopsDB->prefix("mac_info") .  " order by  $sortby  ,  recode_time DESC " ;
 	$result = $xoopsDB->query($sql) or die($sql."<br>". mysql_error()); 		
 
 	while($row=$xoopsDB->fetchArray($result)){
       		$ipv4 = preg_split('/[:-]/' ,$row["mac"] ) ;
       		$row['ipv6'] = ($ipv4[0]^2) .$ipv4[1] .':' . $ipv4[2]  .'ff:fe' .$ipv4[3].':' . $ipv4[4] . $ipv4[5] ;
       		//統一呈現大寫
       		$row["mac"]=strtoupper($row["mac"]) ;

      		if ($dhcp_mac_list[$row["mac"]] ==1) {
       			$dhcp_mac_list[$row["mac"]]  =0 ; 
       		}

        	$row['creat_day'] = substr(  $row['creat_day'] ,2,8) ;
        	$row['ipv6_last'] = substr($row['ip_v6'],-19) ;
        
   		if ($row['recode_time']   == $last_recode_time) {
       			$row['now'] =1 ;
       			$open_mode['now']++ ;
  		 }elseif ( substr($row['recode_time'],0,10)  == substr($last_recode_time,0,10) ) {
       			$row['now']=2 ;
       			$open_mode['today']++ ;

   		}
   		
   		//填報
   		$row['input'] = $input_data[$row['mac']]  ;

   		//動態 IP 不列入下方文字框
  		if  (substr ($row['ip'],0,10) == $dhcp_prefix )  {
			$ip_k = preg_split("/[.]/", $row['ip']);
			if  ($ip_k[3] >$dhcp_last_beg and  $ip_k[3] <=$dhcp_last_end ) {
	    			$row['dhcp'] = true ; 

			}    

  		 }	
   		$comp_list[] = $row ;
   		$comp_list_use[$row['ip']] = true ;
			
 	} //while

 //是否有重覆
 	$sql = " SELECT ip, count( * ) AS cc     FROM " . $xoopsDB->prefix("mac_info") .  
                	"  GROUP BY ip           HAVING cc >1 " ;
 	$result = $xoopsDB->query($sql) or die($sql."<br>". mysql_error()); 		
 	while($row=$xoopsDB->fetchArray($result)){
   		$err_comp_list[] = $row ;
 	}                
 
 	$sql = " select count(*) as cc from " . $xoopsDB->prefix("mac_info")  ;
 	$result = $xoopsDB->query($sql) or die($sql."<br>". mysql_error()); 		
 	$date_list=$xoopsDB->fetchArray($result) ;
 	 $all_rec = $date_list['cc'] ; 	
 

	
 	//IPv4
 	$ip4_array = preg_split('/,/' , $data['ipv4'] ) ;
 	foreach ($ip4_array as $k => $ipv) {
		//空的 IP 
		$empt_list .="<h3>$ipv</h3>" ;	
 		for($i=1 ; $i <=250 ; $i++) {
			
			$ip = $ipv . '.'  . $i ;
			if (!$comp_list_use[$ip]) {
          			$empt_list .= $i . ' , ' ;
          			$empt_count ++ ;
         
			}
	 		if  (($i % 32)==0 )
              			$empt_list .='<br />' ;
 		}
 
	}		

	
 


/*-----------秀出結果區--------------*/
 
$xoopsTpl->assign("comp_list",$comp_list);
$xoopsTpl->assign("all_rec",$all_rec);
$xoopsTpl->assign("edit_id",$edit_id);
$xoopsTpl->assign("sortby",$sortby);
$xoopsTpl->assign("err_comp_list",$err_comp_list);
$xoopsTpl->assign("open_mode",$open_mode);
$xoopsTpl->assign("empt_count",$empt_count);
$xoopsTpl->assign("empt_list",$empt_list);

$xoopsTpl->assign("input_data",$input_data);
 
$xoopsTpl->assign("dhcp_List",$dhcp_List);
//$xoopsTpl->assign("dhcp_mac_no_in_data",$dhcp_mac_no_in_data);

$xoopsTpl->assign("data",$data);
 
 
include_once 'footer.php';
?>