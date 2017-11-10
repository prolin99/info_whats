<?php
//  ------------------------------------------------------------------------ //
// 本模組由 prolin 製作
// 製作日期：2014-03-01
// $Id:$
// ------------------------------------------------------------------------- //

/*-----------引入檔案區--------------*/
$xoopsOption['template_main'] = "info_ext_tpl.html";
include_once "header.php";
include_once "../function.php";


/*-----------function區--------------*/
//


/*-----------執行動作判斷區----------*/



 //=======================================================================
    //取得偏好設定
    $data['ip_rang'] = $xoopsModuleConfig['iw_ip_rang'] ;                    //分配規劃
    $data['ipv4'] = $xoopsModuleConfig['iw_ip_v4'] ;                        //ipv4 網段
    $data['ipv6'] = $xoopsModuleConfig['iw_ip_v6'] ;                        //ipv6 網段


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

    //取得資料表全部
  $sql = " select * from " . $xoopsDB->prefix("mac_info") .  "  where ip <>ipv4_in  and ipv4_in is not null order by   recode_time DESC " ;




    $result = $xoopsDB->query($sql) or die($sql."<br>". $xoopsDB->error());

    while ($row=$xoopsDB->fetchArray($result)) {
        $ipv4 = preg_split('/[:-]/', $row["mac"]) ;
        $row['ipv6'] = ($ipv4[0]^2) .$ipv4[1] .':' . $ipv4[2]  .'ff:fe' .$ipv4[3].':' . $ipv4[4] . $ipv4[5] ;
        //統一呈現大寫
        $row["mac"]=strtoupper($row["mac"]) ;



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
