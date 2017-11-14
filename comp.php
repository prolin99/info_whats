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
