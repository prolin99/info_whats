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

//=======================================================================
    //取得偏好設定
    $data['ip_rang'] = $xoopsModuleConfig['iw_ip_rang'] ;                    //分配規劃
    $data['ipv4'] = $xoopsModuleConfig['iw_ip_v4'] ;                        //ipv4 網段
    $data['ipv6'] = $xoopsModuleConfig['iw_ip_v6'] ;                        //ipv6 網段


    $last_recode_time= date('Y-m-d') ;

    //取得在 AP 下的資料表全部
    $sql = " select * from " . $xoopsDB->prefix("mac_info") .
    "  where ipv4_in <>ipv4_ext  and ipv4_ext is not null  and ipv4_ext<>''  order by   recode_time DESC " ;
    $result = $xoopsDB->query($sql) or die($sql."<br>". $xoopsDB->error());

    while ($row=$xoopsDB->fetchArray($result)) {
        $ipv4 = preg_split('/[:-]/', $row["mac"]) ;
        $row['ipv6'] = ($ipv4[0]^2) .$ipv4[1] .':' . $ipv4[2]  .'ff:fe' .$ipv4[3].':' . $ipv4[4] . $ipv4[5] ;
        //統一呈現大寫
        $row["mac"]=strtoupper($row["mac"]) ;
        $row["ps"]=disp_impact($row["ps"]) ;


        $row['creat_day'] = substr($row['creat_day'], 2, 8) ;
        $row['ipv6_last'] = substr($row['ip_v6'], -19) ;
        $row['now'] =0 ;
        $row['recode_time']=substr($row['recode_time'], 0, -3)  ;

        if (substr($row['recode_time'], 0, 10)  == substr($last_recode_time, 0, 10)) {
            //以日計，同日
            $row['now']=2 ;
            $open_mode['today']++ ;
        }

        $comp_list[] = $row ;

    } //while







/*-----------秀出結果區--------------*/

$xoopsTpl->assign("comp_list", $comp_list);


$xoopsTpl->assign("open_mode", $open_mode);




include_once 'footer.php';
