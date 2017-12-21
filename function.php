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
    //取得已填報的資料
    global $xoopsDB;
    if ($ip) {
        //$sql = " select id , ip , user ,  place   from " . $xoopsDB->prefix("mac_input")  ." where ip ='$ip' and $uid='$uid'  " ;
        if ($mac) {
            $sql = " select * from " . $xoopsDB->prefix("mac_input")  ." where mac ='$mac'  order by id DESC  " ;
        } else {
            $sql = " select *  from " . $xoopsDB->prefix("mac_input")  ." where ip ='$ip'  order by id DESC  " ;
        }
        $result = $xoopsDB->query($sql) or die($sql."<br>". $xoopsDB->error());
        $data_list=$xoopsDB->fetchArray($result) ;

        return $data_list ;
    }
}


function get_from_rec($uid, $ip, $mac)
{
    //取得已記錄在 mac_info 表中的資料
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
function get_id_online_rec($id, $days=30, $prei =10)
{
    global $xoopsDB , $xoopsModuleConfig;
    $prei= $xoopsModuleConfig['iw_ip_scan_time'] ;

    //上線記錄
    $days = $days * -1 ;
    $sql = " select id, on_day , max(online_day) as max_d ,min(online_day) as min_d  ,  count(*) as cc from  " . $xoopsDB->prefix("mac_online") .
    " where (id = '$id') and (online_day >= ( DATE_ADD(CURDATE() ,INTERVAL $days DAY )) )  " .
    " group by id,on_day "      ;

    $result = $xoopsDB->query($sql) or die($sql."<br>". $xoopsDB->error());
    while ($row=$xoopsDB->fetchArray($result)) {
        $d_of_w = date('w', strtotime($row['on_day']))  ;
        $week = date('W', strtotime($row['on_day']))  ;
        $open_week[$week][$d_of_w]['on']= 'on' ;
        $open_week[$week][$d_of_w]['day']= $row['on_day'] ;
        $open_week[$week][$d_of_w]['on_hour']= number_format(($row['cc'] * $prei / 60), 1) ;
        $open_week[$week][$d_of_w]['b'] = substr($row['min_d'], 11, 5) ;
        $open_week[$week][$d_of_w]['e'] = substr($row['max_d'], 11, 5) ;
    }


    //取得客戶端上傳硬體訊息，一個月內
/*
    $sql = " select id, on_day , count(*) as cc from " . $xoopsDB->prefix("mac_up_sysinfo") .
    " where (id = '$id') and (on_day >= ( DATE_ADD(CURDATE() ,INTERVAL $days DAY )) ) " .
    " group by id,on_day "      ;


    $result = $xoopsDB->query($sql) or die($sql."<br>". $xoopsDB->error());
    while ($row=$xoopsDB->fetchArray($result)) {
        $d_of_w = date('w', strtotime($row['on_day']))  ;
        $week = date('W', strtotime($row['on_day']))  ;
        //$open_week[$week][$d_of_w]['data']= $row ;
        $open_week[$week][$d_of_w]['boot']= 'boot' ;
        $open_week[$week][$d_of_w]['times'] = $row['cc'];
        $open_week[$week][$d_of_w]['D']= substr($row['on_day'], 8, 2) ;
    }
*/
    $sql = " select * from " . $xoopsDB->prefix("mac_up_sysinfo") .
     " where (id = '$id' ) and (on_day >= ( DATE_ADD(CURDATE() ,INTERVAL $days DAY )) ) " .
     " order by sysinfo_day "      ;
    $result = $xoopsDB->query($sql) or die($sql."<br>". $xoopsDB->error());
    while ($row=$xoopsDB->fetchArray($result)) {
      $d_of_w = date('w', strtotime($row['on_day']))  ;
      $week = date('W', strtotime($row['on_day']))  ;
      $open_week[$week][$d_of_w]['boot']= 'boot' ;
      $open_week[$week][$d_of_w]['times'] ++ ;
      $open_week[$week][$d_of_w]['D']= substr($row['on_day'], 8, 2) ;
      $open_week[$week][$d_of_w]['turnon_list'] .= substr($row['sysinfo_day'],11,5) . ' , ';

      $row['w']= $d_of_w ;
      $open_list[] = $row ;
    }


    //如果沒有開機訊息，再查看內部多台開機記錄
    if ($open_week and  empty($open_list) ) {
      $sql = " select ip  from " . $xoopsDB->prefix("mac_info") . " where id = '$id' "  ;
      $result = $xoopsDB->query($sql) or die($sql."<br>". $xoopsDB->error());
      while ($row=$xoopsDB->fetchArray($result)) {
        $ext_ip = $row['ip'] ;
      }
      if ($ext_ip) {
        //多台參數
        $in_id =0 ;
        //子查詢
        $sql = " select * from " . $xoopsDB->prefix("mac_up_sysinfo") .
         " where    (on_day >= ( DATE_ADD(CURDATE() ,INTERVAL $days DAY )) ) " .
         " and (id  in  " .
         " ( select id from " . $xoopsDB->prefix("mac_info") . " where ipv4_ext='$ext_ip'  ) ".
         " )" .
         " order by on_day ,id , sysinfo_day "      ;
         //echo $sql ;
         $result = $xoopsDB->query($sql) or die($sql."<br>". $xoopsDB->error());
         while ($row=$xoopsDB->fetchArray($result)) {

           $d_of_w = date('w', strtotime($row['on_day']))  ;
           $week = date('W', strtotime($row['on_day']))  ;
           $open_week[$week][$d_of_w]['boot']= 'boot' ;
           $open_week[$week][$d_of_w]['times'] ++ ;
           $open_week[$week][$d_of_w]['D']= substr($row['on_day'], 8, 2) ;

           if ($in_id<>$row['id']) {
             $in_id = $row['id'] ;
             $open_week[$week][$d_of_w]['turnon_list'] .="(#$in_id)" ;
           }

           $open_week[$week][$d_of_w]['turnon_list'] .= substr($row['sysinfo_day'],11,5) . ' , ';
           $row['w']= $d_of_w ;
           $open_list[] = $row ;
         }
      }
    }


    $open_week['list']=$open_list ;
    return $open_week ;
}


function get_dhcp_lease($dhcp_log)
{
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
                if ($srow['id']) {
                    $sql = " update  " . $xoopsDB->prefix("mac_info") .  "  set  comp = '$cl_name'  where  mac = '$mac' " ;
                    //echo 'qqqq' . $sql ;
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






//開機上傳的檔案
function get_system_info($file)
{
    global $xoopsModuleConfig ,$xoopsDB ;

    //echo "<p>$file</p>" ;

    $output = file_get_contents($file, FILE_USE_INCLUDE_PATH);
    //utf 16 to utf8
    $output = mb_convert_encoding($output, "UTF-8", "UTF-16");
    $lines = preg_split('/\n/', $output);
    foreach ($lines as $k =>$v) {
        $keyword_find = false ;
        //echo $v . '<br>' ;

        $success = preg_match('/UUID/i', trim($v));
        if ($success) {
            $key='uuid' ;
            $keyword_find = true ;
            continue ;
        }

        $success = preg_match('/BIOSVersion/i', trim($v));
        if ($success) {
            $key='bios' ;
            $keyword_find = true ;
            continue ;
        }

        $success = preg_match('/^Name/i', trim($v));
        if ($success) {
            $key='cpu' ;
            $keyword_find = true ;
            continue ;
        }

        $success = preg_match('/MaxCapacity/i', trim($v));
        if ($success) {
            $key='memory' ;
            $keyword_find = true ;
            continue ;
        }

        $success = preg_match('/TotalPhysicalMemory/i', trim($v));
        if ($success) {
            $key='realmemory' ;
            $keyword_find = true ;
            continue ;
        }

        $success = preg_match('/DHCPServer/i', trim($v));
        if ($success) {
            $key='dhcpserver' ;
            $keyword_find = true ;
            continue ;
        }

        $success = preg_match('/Manufacturer/i', trim($v));
        if ($success) {
            $key='baseboard' ;
            $keyword_find = true ;
            continue ;
        }

        $success = preg_match('/IPAddress/i', trim($v));
        if ($success) {
            $key='ip_mac' ;
            $keyword_find = true ;
            continue ;
        }

        $success = preg_match('/(YourIp:)(.+)/i', trim($v), $vpart);
        if ($success) {
            $info_data['ext_ip']= trim($vpart[2]) ;
            $key='' ;
            continue ;
        }



        //非 key 行，前有指定 key ，內容不是空的
        if ((!$keyword_find) and  ($key<>'') and trim($v)) {
            //xp 版本
            if ($key=='ip_mac') {
                /*
                $success = preg_match('/^([a-fA-F0-9]{2}[:|\-]){5}[a-fA-F0-9]{2}/', $v);
                if ($success) {
                    $info_data['mac'] = strtoupper($v) ;
                }
                */

                $success = preg_match('/^{"(.+)"}(.+)/', trim($v), $vpart);
                if ($success) {
                    $info_data['ip_mac'] = trim($v) ;
                }
            } else {
                $info_data[$key]= trim($v) ;
                $key='' ;
            }
        }
    }



    /*
    UUID
    4F112500-97CC-0920-0811-154239000000
    BIOSVersion            Name
    {"ACRSYS - 20090616"}  Default System BIOS
    Name
    Pentium(R) Dual-Core  CPU      E5200  @ 2.50GHz
    MaxCapacity
    4194304
    DHCPServer
    120.116.24.4

    IPAddress                                                                                                                MACAddress

    {"120.116.25.122", "fe80::8999:d1ee:e:f252", "2001:288:752a:0:b52d:6ae5:59df:f48e", "2001:288:752a:0:8999:d1ee:e:f252"}  00:25:11:4F:CC:97

    */
    /* xp
    UUID
FFFFFFFF-FFFF-FFFF-FFFF-FFFFFFFFFFFF
BIOSVersion                                                                          Name
{"ACRSYS - 42302e31", "Phoenix - AwardBIOS v6.00PG", "Phoenix - AwardBIOS v6.00PG"}  Phoenix - AwardBIOS v6.00PG
Name
Intel Pentium II 處理器
MaxCapacity
4194304
DHCPServer
120.116.24.4
IPAddress           MACAddress
                  50:50:54:50:30:30
                  33:50:6F:45:30:30
                  30:0F:20:52:41:53
{"120.116.25.134"}  00:1C:25:8A:50:81
                  00:1C:25:8A:50:81
YourIp: 120.116.25.134
    */

    $ipv6_pre = $xoopsModuleConfig['iw_ip_v6'] ;

    $ip_data_arr = preg_split("/[,}]+/", $info_data['ip_mac']);
    foreach ($ip_data_arr as $k =>$v) {
        $v= preg_replace('/[{"\s]/', '', $v);
        //  echo $v .'<br>' ;
        $success = preg_match('/^(\d{1,3}\.){3}\d{1,3}$/', $v);
        if ($success) {
            $info_data['ip_v4'] = $v ;
            continue ;
        }

        $success = preg_match("/^$ipv6_pre/", $v);
        if ($success) {
            $info_data['ip_v6'] = $v ;
            continue ;
        }

        $success = preg_match('/^([a-fA-F0-9]{2}[:|\-]){5}[a-fA-F0-9]{2}/', $v);
        if ($success) {
            $info_data['mac'] = strtoupper($v) ;
        }
    }
    if ($info_data['ip_v4'] == $info_data['ext_ip']) {
        $info_data['ext_ip']='' ;
    }



    //找原始資料
    $has_old_id = 0 ;
    $sql = ' select * from '.$xoopsDB->prefix('mac_info')." where   mac = '{$info_data['mac']}'  ";


    $result = $xoopsDB->query($sql) or die($sql.'<br>'.$xoopsDB->error());
    while ($row = $xoopsDB->fetchArray($result)) {
        $has_old_id = $row['id'];
        $row['memory']+= 0 ;
        $row['realmemory']+= 0 ;
        $mac_info= $row;
    }

    $danger_fg = 0 ;

    //已有記錄
    if ($has_old_id) {
        if ($mac_info['uuid']) {
            if (($mac_info['uuid']<> $info_data['uuid']) or ($mac_info['cpu']<> $info_data['cpu']) or ($mac_info['memory']<> $info_data['memory']) or ($info_data['realmemory']>0  and ($mac_info['realmemory']<> $info_data['realmemory'] ) )       ) {
                $danger_fg = 1 ;
            }
            $sql = ' update '.$xoopsDB->prefix('mac_info')."  set
           dhcpserver='{$info_data['dhcpserver']}' ,
           sysinfo_day=now() ,
           ipv4_in='{$info_data['ip_v4']}'  ,
           ip_v6='{$info_data['ip_v6']}'  ,
           ipv4_ext='{$info_data['ext_ip']}'  ,
           baseboard='{$info_data['baseboard']}'  ,
           dangerFG ='$danger_fg'
           where id='$has_old_id'
           ";
            //  echo $sql ."<br>"  ;
        } else {
            //第一次取得 client 檔案
            $sql = ' update '.$xoopsDB->prefix('mac_info')."  set
           uuid='{$info_data['uuid']}' ,
           bios='{$info_data['bios']}' ,
           cpu='{$info_data['cpu']}' ,
           memory='{$info_data['memory']}' ,
           realmemory='{$info_data['realmemory']}' ,
           dhcpserver='{$info_data['dhcpserver']}' ,
           baseboard='{$info_data['baseboard']}'  ,
           sysinfo_day=now() ,
           ipv4_in='{$info_data['ip_v4']}'  ,
           ip_v6='{$info_data['ip_v6']}'  ,
           ipv4_ext='{$info_data['ext_ip']}'  ,
           dangerFG ='$danger_fg'
           where id='$has_old_id'
           ";
            //  echo $sql ."<br>"  ;
        }
        $result = $xoopsDB->queryF($sql) or die($sql.'<br>'.$xoopsDB->error());
    } else {
        //放進記錄
        $sql = ' insert into  '.$xoopsDB->prefix('mac_info')."
       (id ,ip ,ip_v6 , mac ,recode_time ,creat_day ,ip_id ,comp ,phid ,kind , uuid , bios , cpu , memory , dhcpserver , sysinfo_day ,ipv4_ext ,ipv4_in , realmemory , baseboard)
         values ('0','{$info_data['ip_v4']}','{$info_data['ip_v6']}','{$info_data['mac']}',now() , now() ,'0' ,'','','' , '{$info_data['uuid']}' ,'{$info_data['bios']}','{$info_data['cpu']}','{$info_data['memory']}','{$info_data['dhcpserver']}', now() ,'{$info_data['ext_ip']}' ,'{$info_data['ip_v4']}' ,'{$info_data['realmemory']}' ,'{$info_data['baseboard']}' ) ";
        $result = $xoopsDB->queryF($sql) or die($sql.'<br>'.$xoopsDB->error());
        $has_old_id = $xoopsDB->getInsertId();
    }
    online($has_old_id) ;
    //開機 info 記錄
    $sql = ' insert into  '.$xoopsDB->prefix('mac_up_sysinfo')."
   (uid,id , uuid , bios , cpu , memory , realmemory,  dhcpserver , ipaddress , sysinfo_day , dangerFG ,on_day , baseboard )
     values ('0', '$has_old_id' , '{$info_data['uuid']}' ,'{$info_data['bios']}','{$info_data['cpu']}','{$info_data['memory']}' ,'{$info_data['realmemory']}','{$info_data['dhcpserver']}','{$info_data['ip_mac']}', now() ,'$danger_fg' , now() ,'{$info_data['baseboard']}' )";
    $result = $xoopsDB->queryF($sql) or die($sql.'<br>'.$xoopsDB->error());
}







//上線記錄
function online($id)
{
    global $xoopsDB  , $this_on_array;
    if (($id <=0) or  in_array($id, $this_on_array)) {
        echo $id .'****<br>' ;
        return 0 ;
    } else {
        $this_on_array[]= $id ;
        echo $id .'<br>' ;
        //上線記錄
        $sql = ' insert into  '.$xoopsDB->prefix('mac_online').
          "(oid ,id ,online_day , on_day )
          values ('0','$id',now() ,now()  ) ";
        $result = $xoopsDB->queryF($sql) or die($sql.'<br>'.$xoopsDB->error());

        //本身連線更新
        $sql = ' update '.$xoopsDB->prefix('mac_info')." set  recode_time=now()    where id='$id' ";
        $result = $xoopsDB->queryF($sql) or die($sql.'<br>'.$xoopsDB->error());
    }
}


//加強顯示 ，財產編號加樣式
function disp_impact($v){
  if (trim($v)){
    //@電腦財產編號 +螢幕編號
    $success = preg_match('/(.+)(@[0-9]+)(\s*)(\+[0-9]+)(.*)/', trim($v) ,$v_part);
    if ($success ){
      $v_part[2]='<span class="label label-success">'. $v_part[2] .'</span>' ;
      $v_part[4]='<span class="label label-success">'. $v_part[4] .'</span>' ;
      for ($i=1 ; $i <= count($v_part) ; $i++)
        $new_str .= $v_part[$i] ;
      return $new_str ;
    }else {
      //@電腦財產編號
      $success = preg_match('/(.+)(@[0-9]+)(.*)/', trim($v) ,$v_part);
      if ($success ){
        $v_part[2]='<span class="label label-success">'. $v_part[2] .'</span>' ;
        for ($i=1 ; $i <= count($v_part) ; $i++)
          $new_str .= $v_part[$i] ;
        return $new_str ;
      }else
        return $v ;
    }

  }

}
