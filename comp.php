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

if ($_GET['do'] == $key) {

    $nmap_url = $xoopsModuleConfig['iw_ip_scan_url'];
    //echo $nmap_url . '<br >' ;

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
    list($mark, $is,  $mac, $ifor,  $ip1, $ip_0) = preg_split("/[\s]+/", $line);
    if ($mark == 'Nmap') {
        //echo " $mark , $is,  $mac , $ifor ,  $ip1 , $ip_0 <br />"   ;
        $up_ip = $ip1;

        if (substr($ip_0, 0, 1) == '(') {
            $up_ip = substr($ip_0, 1, -1);
        }
    }

    if (($mark == 'MAC') and ($up_ip != '') and ($mac != 'FF:FF:FF:FF:FF:FF')) {
        $nip = '';
        $ip = trim($up_ip);
        $mac = trim($mac);
        $up_ip = '';
        $ip_k = preg_split('/[.]/', $ip);
        $ip_id = $ip_k[2] * 1000 + $ip_k[3];
        if ($mac) {
            $sql = ' select * from '.$xoopsDB->prefix('mac_info')." where   mac = '$mac'  ";
            $err_comp_list[] = "$ip -- $sqlstr <br >";
            //echo "$ip -- $sqlstr <br >" ;

            $result = $xoopsDB->query($sql) or die($sql.'<br>'.mysql_error());
            while ($row = $xoopsDB->fetchArray($result)) {
                $nip = $row['mac'];
            }

            if (!$nip) {
                $sql = ' insert into  '.$xoopsDB->prefix('mac_info')."  (id ,ip ,mac ,recode_time ,creat_day ,ip_id)
				               values ('0','$ip','$mac',now() , now() ,'$ip_id' ) ";
                $result = $xoopsDB->queryF($sql) or die($sql.'<br>'.mysql_error());
                //echo "$sqlstr <br >" ;
            } else {
                //更新

                $sql = ' update '.$xoopsDB->prefix('mac_info')."  set  ip='$ip' ,recode_time=now()  ,ip_id ='$ip_id'  where mac='$mac' ";

                $result = $xoopsDB->queryF($sql) or die($sql.'<br>'.mysql_error());
                //echo "$sqlstr <br >" ;
            }
        }
    }
} //foreach

    /*
    ipv6
    linux ------
    ping6 -I eth0 -c3 ff02::1 > /dev/null
    /sbin/ip neigh show > /var/www/html/ip-neigh-show.txt
    */

    $nmap_url = $xoopsModuleConfig['iw_ip_neigh_url'];
    //echo $nmap_url . '<br >' ;
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
        $arp_list = preg_split("/[\s]+/", $line);
        $a_ip = $arp_list[0];

        $a_mac = $arp_list[4];

        if (isset($a_mac)) {
            $pos = stripos($a_ip,  $xoopsModuleConfig['iw_ip_v6']);
            if ($pos !== false) {
                $sql = ' update  '.$xoopsDB->prefix('mac_info')."  set  ip_v6='$a_ip' ,recode_time=now()    where mac='$a_mac' ";
            } else {
                $sql = ' update  '.$xoopsDB->prefix('mac_info')."  set   recode_time=now()    where mac='$a_mac' ";
            }

            $result = $xoopsDB->queryF($sql) or die($sql.'<br>'.mysql_error());
            //echo "$sql <br >" ;
        }
    }

    //------主機離線 EMAIL 通知
    $alert_fg =   $xoopsModuleConfig['iw_alert'];
    $email =    $xoopsModuleConfig['iw_alert_Email'];


    if ($alert_fg and $email) {
        $title ="主機離線訊息警告" ;
        //
        $sql = ' select  *  from   '.$xoopsDB->prefix('mac_info')."  where    recode_time<= now()-30    and  point >=2 ";
        echo $sql ;
        $result = $xoopsDB->query($sql) ;

        while ($row=  $xoopsDB->fetchArray($result)) {
            $content .=" 主機離線 ip: {$row['ip']} -- {$row['ip_v6']} ,mac: {$row['mac']} ( {$row['comp']}  )  {$row['ps']} 最後上線時間: {$row['recode_time']}  <br />";
        }
        $xoopsMailer                           = &getMailer();
        $xoopsMailer->multimailer->ContentType = "text/html";
        $xoopsMailer->addHeaders("MIME-Version: 1.0");
        $msg .= $xoopsMailer->sendMail($email, $title, $content, $headers)  ;
    }




    exit;
}
