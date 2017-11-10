<?php
//  ------------------------------------------------------------------------ //
// 本模組由 prolin 製作
// 製作日期：2014-03-01
// $Id:$
// ------------------------------------------------------------------------- //

/*-----------引入檔案區--------------*/
$xoopsOption['template_main'] = "info_admin_tpl.html";
include_once "header.php";
include_once "../function.php";


/*-----------function區--------------*/
//


/*-----------執行動作判斷區----------*/


if ($_POST['Submit_add']) {
    //手動加入 mac
    $_POST['new_mac']= strtoupper(trim($_POST['new_mac'])) ;
    $sql = " insert into  " . $xoopsDB->prefix("mac_info") .  "  (id ,ip ,mac ,recode_time ,creat_day ,ip_id ,comp ,phid ,kind)
				               values ('0','','{$_POST['new_mac']}',now() , now() , 0 ,'' ,'' ,''  ) " ;
    $result = $xoopsDB->query($sql) or die($sql."<br>". $xoopsDB->error());
}


if ($_POST['btn_clear']) {
    //清除登記填報
    $sql = " TRUNCATE TABLE  " . $xoopsDB->prefix("mac_input")   ;
    $result = $xoopsDB->queryF($sql) or die($sql."<br>". $xoopsDB->error());
}

 //=======================================================================
    //取得偏好設定
    $data['ip_rang'] = $xoopsModuleConfig['iw_ip_rang'] ;                    //分配規劃
    $data['ipv4'] = $xoopsModuleConfig['iw_ip_v4'] ;                        //ipv4 網段
    $data['ipv6'] = $xoopsModuleConfig['iw_ip_v6'] ;                        //ipv6 網段

    $ip4_array = preg_split('/,/', $data['ipv4']) ;

    $dhcp_log = $xoopsModuleConfig['iw_ip_dhcp_log'] ;    //動態分配區記錄檔

    //dhcp 範圍
    //是否有多段
    $dhcp_rang_list = preg_split('/,/', $xoopsModuleConfig['iw_ip_v4_dhcp']) ;
        for ($ri=0 ;$ri< count($dhcp_rang_list) ; $ri++) {
            $dhcp_rang = preg_split('/~/', $dhcp_rang_list[$ri]) ;    //動態分配區
            $dhcp_begin = preg_split('/[\.]/', $dhcp_rang[0]) ;
            $dhcp_end = preg_split('/[\.]/', $dhcp_rang[1]) ;
            $dhcp_prefix= $dhcp_begin[0] . '.'  . $dhcp_begin[1] . '.'  . $dhcp_begin[2]   ;
            $dhcp_last_beg= $dhcp_begin[3] ;
            $dhcp_last_end= $dhcp_end[3] ;
            for ($i =$dhcp_last_beg ; $i <=$dhcp_last_end ; $i++) {
                $dhcp_list[]= $dhcp_prefix .'.'.$i ;
                //echo $dhcp_prefix .'.'.$i . ',' ;
            }
        }

    //echo $dhcp_prefix ;

    //----dhcp data mac to name ----------------
    //"/var/lib/dhcpd/dhcpd.leases"
    //只改名稱
    $dhcp_lease = get_dhcp_lease($dhcp_log) ;

    //取得登記資料 --------------------------------------
    $sql = " select *  from " . $xoopsDB->prefix("mac_input") . "  order by mac "  ;
    $result = $xoopsDB->query($sql) or die($sql."<br>". $xoopsDB->error());
    while ($row=$xoopsDB->fetchArray($result)) {
        if ($row["mac"]) {
            $row["mac"]=strtoupper($row["mac"]) ;
            $input_data[$row['mac']]['ps'] .= $row['user']  .'-' . $row['place'];
            $input_data[$row['mac']]['ip'] .= $row['ip'] ;
            $input_data[$row['mac']]['id'] = $row['id'] ;
        } else {
            $row["mac"]=strtoupper($row["mac"]) ;
            $input_data[$row['ip']]['ps'] .= $row['user']  .'-' . $row['place'];
            $input_data[$row['ip']]['ip'] .= $row['ip'] ;
            $input_data[$row['ip']]['id'] = $row['id'] ;
        }
    }

 //取得最近時間
    $sql = " select * from " . $xoopsDB->prefix("mac_info") .  " order by recode_time DESC  " ;
    $result = $xoopsDB->query($sql) or die($sql."<br>". $xoopsDB->error());
    $date_list=$xoopsDB->fetchArray($result) ;
    $last_recode_time = $date_list['recode_time'] ;  //判別到分
    // echo $last_recode_time ;
//排序

    if ($_POST['sort']) {
        $_GET['sort'] = $_POST['sort'] ;
    }
    if ($_GET['sort']) {
        $soid =$_GET['sort'] ;
    }


    //排序方式
  $sort_list= array('kind DESC', 'phid ' ,'ip_id' ,'mac' , 'comp' ,'modify_day DESC' , 'id DESC' ,'recode_time DESC'  ,'creat_day DESC' ,'id '  ) ;
    if (($soid <=0) or ($soid > count($sort_list))) {
        $soid=7 ;
    }

    $sortby = $sort_list[$soid-1] ;

    $where_list = array('point'=>' where point >=1 ' ,'mystery'=>' where  ps="" or ps is null '  )  ;
    $sp = $_GET['do'] ;
  $where_str = $where_list[$sp] ;

    //取得資料表全部
  $sql = " select * from " . $xoopsDB->prefix("mac_info") .  " $where_str  order by  $sortby   ,  recode_time DESC " ;




    $result = $xoopsDB->query($sql) or die($sql."<br>". $xoopsDB->error());

    while ($row=$xoopsDB->fetchArray($result)) {
        $ipv4 = preg_split('/[:-]/', $row["mac"]) ;
        $row['ipv6'] = ($ipv4[0]^2) .$ipv4[1] .':' . $ipv4[2]  .'ff:fe' .$ipv4[3].':' . $ipv4[4] . $ipv4[5] ;
        //統一呈現大寫
        $row["mac"]=strtoupper($row["mac"]) ;

        //dhcp log 記錄中，是否已在資料庫中
        if ($dhcp_mac_list[$row["mac"]] ==1) {
            $dhcp_mac_list[$row["mac"]]  =0 ;
        }

        $row['creat_day'] = substr($row['creat_day'], 2, 8) ;
        $row['ipv6_last'] = substr($row['ip_v6'], -19) ;
        $row['now'] =0 ;
        $row['recode_time']=substr($row['recode_time'], 0, -3)  ;
        if (substr($row['recode_time'], 0, 16)   == substr($last_recode_time, 0, 16)) {
            //echo  $row['ip']. substr($row['recode_time'],0,16).'   ==' .  substr( $last_recode_time,0,16)  .'<br>' ;
            //以分計，同時
            $row['now'] =1 ;
            $open_mode['now']++ ;
        } elseif (substr($row['recode_time'], 0, 10)  == substr($last_recode_time, 0, 10)) {
            //以日計，同日
            $row['now']=2 ;
            $open_mode['today']++ ;
        }

        //填報
        if ($input_data[$row['mac']]['ps']) {
            $row['input'] = $input_data[$row['mac']]['ps']  ;
            $row['input_id'] = $input_data[$row['mac']]['id']  ;
        } else {
            $row['input'] = $input_data[$row['ip']]['ps']  ;
            $row['input_id'] = $input_data[$row['ip']]['id']  ;
        }

        $input_data[$row['mac']]['in'] = true ;                //已在資料庫中


        //動態 IP 不列入下方文字框
        if (in_array($row['ip'], $dhcp_list)) {
            $row['dhcp'] = true ;
        }

        //IP 是否在本校網域中
        $ip_out_net = true ;
        foreach ($ip4_array as $k =>$my_ip_pre) {
            if (strpos($row['ip'], $my_ip_pre) !== false) {
                $ip_out_net = false;
            }
        }
        $row['out'] =$ip_out_net ;
        if ($ip_out_net) {
            $row['dhcp'] = true ;
        }


        $comp_list[] = $row ;
        $comp_list_use[$row['ip']] = true ;
    } //while


    //------輸入 mac 及 dhcpd release 取資料 --------------如在重要、未登記時，不處理這部份--------------------------------------------
    if (! $_GET['do']) {
        //登記資料，但不在掃描記錄中，加入資料中
        foreach ($input_data as $mac =>$comp_row) {
            if ($comp_row['in'] <> true) {
                $ip_k = preg_split("/[.]/", $comp_row['ip']);
                $ip_id = $ip_k[2]*1000 + $ip_k[3] ;
                $add_ipv6='' ;
                if ($ip_id==0) {        // ipv6
                    $add_ipv6= $comp_row['ip'] ;
                }
                if ($comp_row['ip'] <> $mac) {
                    $sql = " insert into  " . $xoopsDB->prefix("mac_info") .  "  (id ,ip ,ip_v6 ,mac ,recode_time ,creat_day , ps, ip_id ,comp ,phid ,kind)
					               values ('0','{$comp_row['ip']}', '$add_ipv6', '$mac',now() , now() ,'{$comp_row['ps']}','$ip_id' ,'','','') " ;

                    $result = $xoopsDB->queryF($sql) or die($sql."<br>". $xoopsDB->error());
                    $add_FG = true ;
                }
            }
        }

        /*
        if ($dhcp_log) {
            //dhcp log 尚未放資料庫的
            foreach ($dhcp_mac_list as $mac =>$v) {
                if ($v==1) {
                    $dhcp_mac_no_in_data .= $mac  .' (' .  $dhcp_List[$mac]  .  ");  " ;
                    $sql = " insert into  " . $xoopsDB->prefix("mac_info") .  " (id ,ip ,comp , mac  ,workgroup , comp_dec ,recode_time ,creat_day ,ip_id , phid ,kind)
					              values ('0','{$dhcp_mac_ip[$mac]}','{$dhcp_List[$mac]}','$mac','','',now() , now() ,0,'','') " ;
                    //echo "$sql <br>" ;
                    $result = $xoopsDB->queryF($sql) or die($sql."<br>". $xoopsDB->error());
                    $add_FG = true ;
                }
        }
        */

        if ($add_FG) { //有新增資料，重整一次
            redirect_header($_SERVER['PHP_SELF'], 3, '資料更新!');
        }
    }


    //檢查 IP 重覆，做提醒
    $sql = " SELECT ip, count( * ) AS cc     FROM " . $xoopsDB->prefix("mac_info") .
                    "  GROUP BY ip           HAVING cc >1 " ;
    $result = $xoopsDB->query($sql) or die($sql."<br>". $xoopsDB->error());
    while ($row=$xoopsDB->fetchArray($result)) {
        $err_comp_list[] = $row ;
    }

    //總筆數
    $sql = " select count(*) as cc from " . $xoopsDB->prefix("mac_info")  ;
    $result = $xoopsDB->query($sql) or die($sql."<br>". $xoopsDB->error());
    $date_list=$xoopsDB->fetchArray($result) ;
    $all_rec = $date_list['cc'] ;


    //已使用 IP
    $sql = " select ip  from " . $xoopsDB->prefix("mac_info") .  " order by  ip " ;

    $result = $xoopsDB->query($sql) or die($sql."<br>". $xoopsDB->error());
    while ($row=$xoopsDB->fetchArray($result)) {
        $comp_list_use[$row['ip']] = true ;
        $use_ip[] = $row['ip'] ;
    }

    // 空的 IPv4   列表

    foreach ($ip4_array as $k => $ipv) {

        //空的 IP
        $empt_list .="<h3>$ipv</h3>" ;

        for ($i=1 ; $i <=250 ; $i++) {
            $ip = $ipv . '.'  . $i ;
            //if (is_null($comp_list_use[$ip])) {
            if (! (in_array($ip, $use_ip))) {
                $empt_list .= $i . ' , ' ;
            }
            $empt_count ++ ;
        }
        if (($i % 32)==0) {
            $empt_list .='<br />' ;
        }
    }





/*-----------秀出結果區--------------*/

$xoopsTpl->assign("comp_list", $comp_list);
$xoopsTpl->assign("all_rec", $all_rec);
$xoopsTpl->assign("edit_id", $edit_id);
$xoopsTpl->assign("sortby", $sortby);
$xoopsTpl->assign("err_comp_list", $err_comp_list);
$xoopsTpl->assign("open_mode", $open_mode);
$xoopsTpl->assign("empt_count", $empt_count);
$xoopsTpl->assign("empt_list", $empt_list);

$xoopsTpl->assign("dhcp_lease", $dhcp_lease);
$xoopsTpl->assign("input_data", $input_data);
$xoopsTpl->assign("point", $_GET['do']);

$xoopsTpl->assign("dhcp_List", $dhcp_List);
//$xoopsTpl->assign("dhcp_mac_no_in_data",$dhcp_mac_no_in_data);

$xoopsTpl->assign("data", $data);


include_once 'footer.php';
