<?php
//  ------------------------------------------------------------------------ //
// 本模組由 prolin 製作
// 製作日期：2014-03-01
// $Id:$
// ------------------------------------------------------------------------- //
/*-----------引入檔案區--------------*/

include_once 'header.php';

/*-----------function區--------------*/

/*-----------執行動作判斷區----------*/

$key = $xoopsModuleConfig['iw_key'];

if ($_GET['do'] <> $key) {
    echo 'wrong' ;
    exit() ;
}

echo 'start--'.'<br/>' ;

    $this_on_array = array() ;

    //-----nmap------------------------------------------
    $nmap_url = $xoopsModuleConfig['iw_ip_scan_url'];
    /*
    /usr/bin/nmap -sP 120.116.24.0/23 > /var/www/html/nmap.txt
    */
    $ch = curl_init();
    $options = array(CURLOPT_URL => $nmap_url,
        CURLOPT_HEADER => false,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
    );
    curl_setopt_array($ch, $options);
    $output = curl_exec($ch);
    curl_close($ch);

    $lines = preg_split('/\n/', $output);
    //var_dump( $lines)  ;
    foreach ($lines as $line_num => $line) {
        //echo " $line <br />"   ;
        $ip_0 = '';
        list($mark, $is, $mac, $ifor, $ip1, $ip_0) = preg_split("/[\s]+/", $line);
        if ($mark == 'Nmap') {
            //Nmap scan report for dns.syps.tn.edu.tw (120.116.24.1)
            //Nmap scan report for 120.116.24.18
            //echo " $mark , $is,  $mac , $ifor ,  $ip1 , $ip_0 <br />"   ;
            $up_ip = $ip1;

            if (substr($ip_0, 0, 1) == '(') {
                $up_ip = substr($ip_0, 1, -1);
            }
        }

        if (($mark == 'MAC') and ($up_ip != '') and ($mac != 'FF:FF:FF:FF:FF:FF')) {
            //MAC Address: 00:D0:E9:40:59:C0 (Advantage Century Telecommunication)
            //MAC Address: EC:A8:6B:A5:F0:85 (Unknown)
            $nip = '';
            $ip = trim($up_ip);
            $mac = trim($mac);
            $up_ip = '';
            $ip_k = preg_split('/[.]/', $ip);
            $ip_id = $ip_k[2] * 1000 + $ip_k[3];
            $find_id = 0 ;
            if ($mac) {
                $sql = ' select * from '.$xoopsDB->prefix('mac_info')." where   mac = '$mac'  ";
                $err_comp_list[] = "$ip -- $sqlstr <br >";
                //echo "$ip -- $sqlstr <br >" ;
                echo $mac . '---<br>' ;
                $result = $xoopsDB->query($sql) or die($sql.'<br>'.$xoopsDB->error());
                while ($row = $xoopsDB->fetchArray($result)) {
                    $find_id = $row['id'];
                    $nip = $row['mac'];
                    //上線中 ，寫入 mac_online
                    online( $find_id ) ;
                    //echo $row['mac'] .'<br />' ;
                }

                if (!$nip) {
                    $sql = ' insert into  '.$xoopsDB->prefix('mac_info')."  (id ,ip ,mac ,recode_time ,creat_day ,ip_id ,comp ,phid ,kind)
				               values ('0','$ip','$mac',now() , now() ,'$ip_id' ,'','','' ) ";
                    $result = $xoopsDB->queryF($sql) or die($sql.'<br>'.$xoopsDB->error());
                    //echo "$sqlstr <br >" ;
                    $find_id = $xoopsDB->getInsertId();
                    //上線中 ，寫入 mac_online
                    online( $find_id ) ;
                } else {
                    //更新

                    $sql = ' update '.$xoopsDB->prefix('mac_info')."  set  ip='$ip' ,recode_time=now()  ,ip_id ='$ip_id'  where mac='$mac' ";

                    $result = $xoopsDB->queryF($sql) or die($sql.'<br>'.$xoopsDB->error());
                     //echo "$sqlstr <br >" ;
                }


            }
        }
    } //foreach


    //-------ip-neigh-show-- (無法反應是否還在上線狀態，不再使用)--------------------------------------------------------------
    /*
    ipv6
    linux ------
    ping6 -I eth0 -c3 ff02::1 > /dev/null
    /sbin/ip neigh show > /var/www/html/ip-neigh-show.txt
    2001:288:752a::6 dev eth0 lladdr 00:1d:d8:b7:1f:18 STALE
    120.116.25.31 dev eth0 lladdr c0:3f:d5:ff:0a:c7 STALE
    fe80::3e15:c2ff:fef0:81bf dev eth0 lladdr 3c:15:c2:f0:81:bf router STALE
    */

    //(無法反應是否還在上線狀態，不再使用)
    /*
    $nmap_url = $xoopsModuleConfig['iw_ip_neigh_url'];

    $ch = curl_init();
    $options = array(CURLOPT_URL => $nmap_url,
        CURLOPT_HEADER => false,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
    );
    curl_setopt_array($ch, $options);
    $output = curl_exec($ch);
    curl_close($ch);

    $lines = preg_split('/\n/', $output);

    foreach ($lines as $line_num => $line) {
        //可能要先 ping6 已有的 ipv6 ??
        //REACHABLE ?

        $arp_list = preg_split("/[\s]+/", $line);
        $a_ip = $arp_list[0];
        $a_mac = strtoupper($arp_list[4]);

        if (isset($a_mac)) {
            $pos = stripos($a_ip, $xoopsModuleConfig['iw_ip_v6']);
            if ($pos !== false) {
                $sql = ' update  '.$xoopsDB->prefix('mac_info')."  set  ip_v6='$a_ip' ,recode_time=now()    where mac='$a_mac' ";
            } else {
                $sql = ' update  '.$xoopsDB->prefix('mac_info')."  set   recode_time=now()    where mac='$a_mac' ";
            }

            $result = $xoopsDB->queryF($sql) or die($sql.'<br>'.$xoopsDB->error());
            //echo "$sql <br >" ;


            //上線中 ，寫入 mac_online
            $sql = ' select * from '.$xoopsDB->prefix('mac_info')." where   mac = '$a_mac'  ";
            $result = $xoopsDB->query($sql) or die($sql.'<br>'.$xoopsDB->error());
            while ($row = $xoopsDB->fetchArray($result)) {
                $find_id = $row['id'];
                online( $find_id ) ;
                //echo $row['mac'] .'<br />' ;
            }

        }
    }
    */




    //------主機離線 EMAIL 通知
    $alert_fg = $xoopsModuleConfig['iw_alert'];
    $email = $xoopsModuleConfig['iw_alert_Email'];

    if ($alert_fg and $email) {
        $title = '主機離線訊息警告';
        //和最新記錄差 30 秒，代表離線，但如果一小時以上不再發 EMAIL
        $sql = ' select  *  from   '.$xoopsDB->prefix('mac_info').'  where    (recode_time<= DATE_SUB(NOW(), INTERVAL 30 second)   )  and  (recode_time>= DATE_SUB(NOW(), INTERVAL 1 hour)   )      and  point >=2 ';
        //echo $sql ;
        $result = $xoopsDB->query($sql);

        while ($row = $xoopsDB->fetchArray($result)) {
            $content .= " 主機離線 ip: {$row['ip']} <br> IPv6:{$row['ip_v6']} <br> mac: {$row['mac']} <br>主機：( {$row['comp']}  )  {$row['ps']} <br>最後上線時間: {$row['recode_time']}  <br />";
        }
        //echo $content ;
        $xoopsMailer = &getMailer();
        $xoopsMailer->multimailer->ContentType = 'text/html';
        $xoopsMailer->addHeaders('MIME-Version: 1.0');
        $msg .= $xoopsMailer->sendMail($email, $title, $content, $headers);
    }


    //----- ftp  systeminfo --------------------------------

    $use_ftp_fg =  $xoopsModuleConfig['iw_FtpFG'] ;
    $ftp_user= $xoopsModuleConfig['iw_FtpUser'] ;
    $ftp_passwd = $xoopsModuleConfig['iw_FtpPassWD'] ;
    $ftp_server = $xoopsModuleConfig['iw_Ftpserver'] ;
    $ftp_path = $xoopsModuleConfig['iw_FtpPath'] ;

    if ($use_ftp_fg) {
        ### 連接的 FTP 伺服器是 localhost
        $conn_id = ftp_connect($ftp_server);

        ### 登入 FTP, 帳號是 USERNAME, 密碼是 PASSWORD
        $login_result = ftp_login($conn_id, $ftp_user, $ftp_passwd);
        //取得遠端檔名

        // 切換成被動模式(true) turn passive mode on
        ftp_pasv($conn_id, true);

        ftp_chdir($conn_id, $ftp_path);

        // 列出當前目錄所有檔案/目錄
        $filelist = ftp_nlist($conn_id, ".");

        foreach ($filelist as $k => $fname) {
            if (preg_match("/txt$/i", $fname)) {
                $local_file = XOOPS_ROOT_PATH."/uploads/info_whats/"  .$fname ;
                $handle = fopen($local_file, 'w');
                if (ftp_fget($conn_id, $handle, $fname, FTP_BINARY, 0)) {

                    //讀取檔案到資料庫中
                    get_system_info($local_file) ;
                    //刪除下載的檔案
                    unlink($local_file) ;
                    ftp_delete($conn_id, $fname );
                } else {
                    echo "下載 $remote_file 到 $local_file 失敗\n";
                }
                fclose($handle); // 關閉檔案
            } //.txt
        }//foreach
        ftp_close($conn_id);
    }

echo 'end --' .'<br/>'  ;


//上線記錄
function online($id){
    global $xoopsDB  , $this_on_array;
    if ( ($id <=0) or  in_array($id, $this_on_array) ){
      return 0 ;
    }else {
      $this_on_array[]= $id ;

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

//開機上傳的檔案
function get_system_info($file)
{
    global $xoopsModuleConfig ,$xoopsDB ;

    echo "<p>$file</p>" ;

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

        $success = preg_match('/IPAddress/i', trim($v));
        if ($success) {
            $key='ip_mac' ;
            $keyword_find = true ;
            continue ;
        }

        $success = preg_match('/(YourIp:)(.+)/i', trim($v) ,$vpart);
        if ($success) {
            $info_data['ext_ip']= trim($vpart[2]) ;
            $key='' ;
            continue ;
        }



        //非 key 行，前有指定 key ，內容不是空的
        if ((!$keyword_find) and  ($key<>'') and trim($v)) {
            //xp 版本
            if ($key=='ip_mac'){
              /*
              $success = preg_match('/^([a-fA-F0-9]{2}[:|\-]){5}[a-fA-F0-9]{2}/', $v);
              if ($success) {
                  $info_data['mac'] = strtoupper($v) ;
              }
              */
              $success = preg_match('/^{"(.+)"}(.+) /', $v ,$vpart);
              if ($success) {
                  $info_data['ip_v4'] = strtoupper($vpart[1]) ;
                  $info_data['mac'] = strtoupper($vpart[2]) ;
                  $info_data['ip_mac'] = strtoupper($vpart[0]) ;
                  $key='' ;
              }

            }else {
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
    if ($info_data['ip_v4'] == $info_data['ext_ip'])
      $info_data['ext_ip']='' ;



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
            if (($mac_info['uuid']<> $info_data['uuid']) or ($mac_info['cpu']<> $info_data['cpu']) or ($mac_info['memory']<> $info_data['memory']) or ($mac_info['realmemory']<> $info_data['realmemory'])  ) {
                $danger_fg = 1 ;
            }
            $sql = ' update '.$xoopsDB->prefix('mac_info')."  set
           dhcpserver='{$info_data['dhcpserver']}' ,
           sysinfo_day=now() ,
           ipv4_in='{$info_data['ip_v4']}'  ,
           ip_v6='{$info_data['ip_v6']}'  ,
           ipv4_ext='{$info_data['ext_ip']}'  ,
           dangerFG ='$danger_fg'
           where id='$has_old_id'
           ";
          //  echo $sql ."<br>"  ;
        } else {
            $sql = ' update '.$xoopsDB->prefix('mac_info')."  set
           uuid='{$info_data['uuid']}' ,
           bios='{$info_data['bios']}' ,
           cpu='{$info_data['cpu']}' ,
           memory='{$info_data['memory']}' ,
           realmemory='{$info_data['realmemory']}' ,
           dhcpserver='{$info_data['dhcpserver']}' ,
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
       (id ,ip ,ip_v6 , mac ,recode_time ,creat_day ,ip_id ,comp ,phid ,kind , uuid , bios , cpu , memory , dhcpserver , sysinfo_day ,ipv4_ext ,ipv4_in , realmemory)
         values ('0','{$info_data['ip_v4']}','{$info_data['ip_v6']}','{$info_data['mac']}',now() , now() ,'0' ,'','','' , '{$info_data['uuid']}' ,'{$info_data['bios']}','{$info_data['cpu']}','{$info_data['memory']}','{$info_data['dhcpserver']}', now() ,'{$info_data['ext_ip']}' ,'{$info_data['ip_v4']}' ,'{$info_data['realmemory']}' ) ";
        $result = $xoopsDB->queryF($sql) or die($sql.'<br>'.$xoopsDB->error());
        $has_old_id = $xoopsDB->getInsertId();
    }
    online( $has_old_id ) ;
    //開機 info 記錄
    $sql = ' insert into  '.$xoopsDB->prefix('mac_up_sysinfo')."
   (uid,id , uuid , bios , cpu , memory , realmemory,  dhcpserver , ipaddress , sysinfo_day , dangerFG ,on_day )
     values ('0', '$has_old_id' , '{$info_data['uuid']}' ,'{$info_data['bios']}','{$info_data['cpu']}','{$info_data['memory']}' ,'{$info_data['realmemory']}','{$info_data['dhcpserver']}','{$info_data['ip_mac']}', now() ,'$danger_fg' , now() )";
    $result = $xoopsDB->queryF($sql) or die($sql.'<br>'.$xoopsDB->error());

}
