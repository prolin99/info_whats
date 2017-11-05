<?php
//  ------------------------------------------------------------------------ //
// 本模組由 prolin 製作
// 製作日期：2014-03-01
// $Id:$
// ------------------------------------------------------------------------- //

/*-----------引入檔案區--------------*/
$xoopsOption['template_main'] = "info_hardware_tpl.html";
include_once "header.php";
include_once "../function.php";

if ($_GET['id']) {
    //取得客戶端上傳硬體
    $sql = " select * from " . $xoopsDB->prefix("mac_info") .  " where id = '{$_GET['id']}'  " ;
    $result = $xoopsDB->query($sql) or die($sql."<br>". $xoopsDB->error());
    while ($row=$xoopsDB->fetchArray($result)) {
        $now_comp = $row ;
    }

    //取得客戶端上傳硬體訊息，一個月內
    $sql = " select * from " . $xoopsDB->prefix("mac_up_sysinfo") .
   " where (id = '{$_GET['id']}') and (sysinfo_day >= ( DATE_ADD(now() ,INTERVAL -30 DAY )) )
    order by  sysinfo_day  " ;

    $result = $xoopsDB->query($sql) or die($sql."<br>". $xoopsDB->error());
    while ($row=$xoopsDB->fetchArray($result)) {
        $row['w']= date('w', strtotime($row['sysinfo_day']))  ;
        $open_list[] = $row ;
        $d_of_w = date('w', strtotime($row['sysinfo_day']))  ;
        $week = date('W', strtotime($row['sysinfo_day']))  ;
        $open_week[$week][$d_of_w]['data']= $row ;
        $open_week[$week][$d_of_w]['boot']= 'boot' ;
        $open_week[$week][$d_of_w]['times'] ++ ;
        $open_week[$week][$d_of_w]['day']= substr($row['sysinfo_day'],8,2) ;
    }


    //上線記錄
    $open_week = get_id_online_rec($dtat_rec['id'] ,30  ) ;
    /*
    $sql = " select id, on_day , max(online_day) as max_d ,min(online_day) as min_d  ,  count(*) from  " . $xoopsDB->prefix("mac_online") .
    " where (id = '{$_GET['id']}') and (online_day >= ( DATE_ADD(now() ,INTERVAL -30 DAY )) )  " .
    " group by id,on_day "      ;
    $result = $xoopsDB->query($sql) or die($sql."<br>". $xoopsDB->error());
    while ($row=$xoopsDB->fetchArray($result)) {
        $d_of_w = date('w', strtotime($row['on_day']))  ;
        $week = date('W', strtotime($row['on_day']))  ;
        $open_week[$week][$d_of_w]['on']= 'on' ;
        $open_week[$week][$d_of_w]['b'] = substr($row['min_d'],11,5) ;
        $open_week[$week][$d_of_w]['e'] = substr($row['max_d'],11,5) ;
    }
    */
} else {
    //今天開機的記錄
    $sql = " select a.* ,i.comp , i.workgroup  , i.comp_dec , i.ps from " .
    $xoopsDB->prefix("mac_up_sysinfo") ." as  a ," . $xoopsDB->prefix("mac_info")  ." as  i " .
    "  where a.id=i.id  and a.sysinfo_day >= CURDATE()  order by  id  DESC,  sysinfo_day   DESC " ;
    $result = $xoopsDB->query($sql) or die($sql."<br>". $xoopsDB->error());
    while ($row=$xoopsDB->fetchArray($result)) {
        $row['w']= date('w', strtotime($row['sysinfo_day']))  ;
        $list[] = $row ;
    }
    //$mode = 'today_list' ;
}

$week_name=array(1=>'一',2=>'二',3=>'三',4=>'四',5=>'五',6=>'六', 0=>'日') ;


/*-----------秀出結果區--------------*/
$xoopsTpl->assign("now_comp", $now_comp);
$xoopsTpl->assign("open_week", $open_week);
$xoopsTpl->assign("open_list", $open_list);
$xoopsTpl->assign("list", $list);
//$xoopsTpl->assign("mode", $mode);
$xoopsTpl->assign("week_name", $week_name);


include_once 'footer.php';
