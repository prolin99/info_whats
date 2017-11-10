<?php
//  ------------------------------------------------------------------------ //
// 本模組由 無名氏 製作
// 製作日期：2014-02-16
// $Id:$
// ------------------------------------------------------------------------- //
//引入TadTools的函式庫
if (!file_exists(XOOPS_ROOT_PATH."/modules/tadtools/tad_function.php")) {
    redirect_header("http://www.tad0616.net/modules/tad_uploader/index.php?of_cat_sn=50", 3, _TAD_NEED_TADTOOLS);
}
include_once XOOPS_ROOT_PATH."/modules/tadtools/tad_function.php";


/********************* 自訂函數 *********************/

/********************* 預設函數 *********************/


function get_mac()
{
    global $xoopsModuleConfig ;
    //如果不是在校內，傳回空值
    //取得 IP (可能ipv6 或 ipv4)
    if ($_SERVER['HTTP_X_FORWARDED_FOR']) {
        $remoIP=$_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $remoIP=$_SERVER['REMOTE_ADDR'];
    }
    //echo $remoIP ;
    //是否在本校網段
    //ipv4
    $ip4_array = preg_split('/,/', $xoopsModuleConfig['iw_ip_v4']) ;
    foreach ($ip4_array  as $k => $ipv4_v) {
        $pos[] = stripos($remoIP, $ipv4_v);
    }

    //ipv6
    $pos[] = stripos($remoIP, $xoopsModuleConfig['iw_ip_v6']);

    foreach ($pos as $k => $v) {
        if ($v !== false) {
            $in_school = true  ;
        }
    }

    //dcs 問題 ：120.116.24.31:33748
    $remoIP_array = preg_split('/:/', $remoIP) ;
    if (count($remoIP_array) ==2) {
        $remoIP=$remoIP_array[0] ;
    }

    if ($in_school) {
        $data['ip'] =$remoIP ;

        //取得  mac ，但得在同一網域中才能
        //echo "/sbin/ip neigh |grep $remoIP "  ;
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $arp=`arp -a $remoIP`;
            $lines=explode("\n", $arp);

            #look for the output line describing our IP address
            foreach ($lines as $line) {
                $cols=preg_split('/\s+/', trim($line));
                if ($cols[0]==$remoIP) {
                    $data['mac'] =$cols[1];
                }
            }
        } else {

            //LINUX 由 ip neigh 中找相符列，再切開取得 mac 卡號
            //exec("/sbin/ip neigh") ;
            // $remoIP="120.116.24.12" ;
            $ip_list =  shell_exec("/sbin/ip neigh |grep $remoIP  ") ;
            $ip_list_n = preg_split('/\n+/', $ip_list) ;    //多行時
            //echo $ip_list."<br />" ;
            //$ipv6_arr = preg_split('/\s+/' ,$ip_list ) ;

            foreach ($ip_list_n as $li => $line) {
                //echo $line."<br />" ;
                $ipv6_arr = preg_split('/\s+/', $line) ;

                if ($ipv6_arr[0]==$remoIP) {
                    $data['mac'] =  $ipv6_arr[4] ;
                    //echo $data['mac']."<br />" ;
                }
            }
        }
    }

    return $data ;
}



function get_from_data($uid, $ip, $mac)
{
    global $xoopsDB;
    if ($ip) {
        //$sql = " select id , ip , user ,  place   from " . $xoopsDB->prefix("mac_input")  ." where ip ='$ip' and $uid='$uid'  " ;
        if ($mac) {
            $sql = " select id , ip , user ,  place   from " . $xoopsDB->prefix("mac_input")  ." where mac ='$mac'  order by id DESC  " ;
        } else {
            $sql = " select id , ip , user ,  place   from " . $xoopsDB->prefix("mac_input")  ." where ip ='$ip'  order by id DESC  " ;
        }
        $result = $xoopsDB->query($sql) or die($sql."<br>". $xoopsDB->error());
        $data_list=$xoopsDB->fetchArray($result) ;

        return $data_list ;
    }
}


function get_from_rec($uid, $ip, $mac)
{
    global $xoopsDB;
    if ($ip or $mac) {
        if ($mac) {
            $sql = " select id ,comp, ps  from " . $xoopsDB->prefix("mac_info")  ." where   mac ='$mac'   " ;
        } else {
            $sql = " select id, comp, ps  from " . $xoopsDB->prefix("mac_info")  ." where  ip ='$ip'      " ;
        }
        $result = $xoopsDB->query($sql) or die($sql."<br>". $xoopsDB->error());
        $data_list=$xoopsDB->fetchArray($result) ;
        // # 後文字不呈現
        $keywords = preg_split("/#/", $data_list['ps']);
        $data_list['ps'] = $keywords [0] ;

        return $data_list ;
    }
}


//以周陣列 傳回上線時間
function get_id_online_rec($id , $days=30 ,$prei =10)
{
    global $xoopsDB , $xoopsModuleConfig;
    $prei= $xoopsModuleConfig['iw_ip_scan_time'] ;

    //上線記錄
    $days = $days * -1 ;
    $sql = " select id, on_day , max(online_day) as max_d ,min(online_day) as min_d  ,  count(*) as cc from  " . $xoopsDB->prefix("mac_online") .
    " where (id = '$id') and (online_day >= ( DATE_ADD(now() ,INTERVAL $days DAY )) )  " .
    " group by id,on_day "      ;

    $result = $xoopsDB->query($sql) or die($sql."<br>". $xoopsDB->error());
    while ($row=$xoopsDB->fetchArray($result)) {
        $d_of_w = date('w', strtotime($row['on_day']))  ;
        $week = date('W', strtotime($row['on_day']))  ;
        $open_week[$week][$d_of_w]['on']= 'on' ;
        $open_week[$week][$d_of_w]['day']= $row['on_day'] ;
        $open_week[$week][$d_of_w]['on_hour']= number_format(($row['cc'] * $prei / 60),1) ;
        $open_week[$week][$d_of_w]['b'] = substr($row['min_d'],11,5) ;
        $open_week[$week][$d_of_w]['e'] = substr($row['max_d'],11,5) ;
    }


    //取得客戶端上傳硬體訊息，一個月內
    $sql = " select id, on_day , count(*) as cc from " . $xoopsDB->prefix("mac_up_sysinfo") .
   " where (id = '$id') and (on_day >= ( DATE_ADD(now() ,INTERVAL $days DAY )) ) " .
   " group by id,on_day "      ;


    $result = $xoopsDB->query($sql) or die($sql."<br>". $xoopsDB->error());
    while ($row=$xoopsDB->fetchArray($result)) {

        $d_of_w = date('w', strtotime($row['on_day']))  ;
        $week = date('W', strtotime($row['on_day']))  ;
        //$open_week[$week][$d_of_w]['data']= $row ;
        $open_week[$week][$d_of_w]['boot']= 'boot' ;
        $open_week[$week][$d_of_w]['times'] = $row['cc'];
        $open_week[$week][$d_of_w]['D']= substr($row['on_day'],8,2) ;
    }
    $sql = " select * from " . $xoopsDB->prefix("mac_up_sysinfo") .
     " where (id = '$id' ) and (on_day >= ( DATE_ADD(now() ,INTERVAL $days DAY )) ) " .
     " order by sysinfo_day "      ;
     $result = $xoopsDB->query($sql) or die($sql."<br>". $xoopsDB->error());
     while ($row=$xoopsDB->fetchArray($result)) {
         $row['w']= date('w', strtotime($row['on_day']))  ;
         $open_list[] = $row ;
     }

    $open_week['list']=$open_list ;
    return $open_week ;

}


function get_dhcp_lease($dhcp_log){
  global $xoopsDB ;
  /*
  lease 120.116.25.48 {
  starts 5 2017/11/10 00:33:41;
  ends 6 2017/11/11 00:33:41;
  cltt 5 2017/11/10 00:33:41;
  binding state active;
  next binding state free;
  hardware ethernet bc:5f:f4:d5:da:db;
  uid "\001\274_\364\325\332\333";
  client-hostname "library01-PC";
  }
  */
  if ($dhcp_log) {
      //$dhcp_lease = file_get_contents($dhcp_log, FILE_USE_INCLUDE_PATH);
      $ch = curl_init();
      $options = array(CURLOPT_URL => $dhcp_log,
          CURLOPT_HEADER => false,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_FOLLOWLOCATION => true,
      );
      curl_setopt_array($ch, $options);
      $dhcp_lease = curl_exec($ch);
      curl_close($ch);

      $dhcp_arr= preg_split('/[\n;]/', $dhcp_lease) ;

      foreach ($dhcp_arr  as $k=>$v) {
          //lease 120.116.25.40 {  ，取得 ip
          $success = preg_match('/lease\b.+{/', trim($v));
          if ($success) {
              $keywords = preg_split("/[\s]+/", trim($v));
              $gdip=$keywords[1] ;
              //echo  $gdip ;
              continue ;
          }

          //starts 5 2017/11/10 00:33:41;
          $success = preg_match('/starts\b.+{/', trim($v));
          if ($success) {
              $keywords = preg_split("/[\s]+/", trim($v));
              $gdip=$keywords[1] ;
              //echo  $gdip ;
              continue ;
          }


          // hardware ethernet 74:da:38:cd:4e:40;   取得 mac
          $success = preg_match('/hardware ethernet.+/', trim($v));
          if ($success) {
              $keywords = preg_split("/[\s]+/", trim($v));
              $mac=strtoupper($keywords[2]) ;
              continue ;
          }

          //client-hostname "ta103-101";  取得名稱
          $cl_name='' ;
          $success = preg_match('/client-hostname.+/', trim($v));
          if ($success) {
              $keywords = preg_split("/[\s]+/", trim($v));
              $cl_name=$keywords[1] ;
              $cl_name= preg_replace('/"/', '', $cl_name);

              $sql = "select  id ,comp from " . $xoopsDB->prefix("mac_info") .  "     where  mac = '$mac' and comp=''   " ;
 
              $result = $xoopsDB->queryF($sql) or die($sql."<br>". $xoopsDB->error());
              $srow = $xoopsDB->fetchArray($result) ;
              if ( $srow['id']  ) {
                $sql = " update  " . $xoopsDB->prefix("mac_info") .  "  set ip='$gdip'  , comp = '$cl_name'  where  mac = '$mac' " ;
                $result = $xoopsDB->queryF($sql) or die($sql."<br>". $xoopsDB->error());

              }

              continue ;
          }

          //結束
          $success = preg_match('/}$/', trim($v));
          if ($success) {
            $cl_name='' ;
            $gdip='' ;
          }

      }
      return $dhcp_lease ;
  }
}
