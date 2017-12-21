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
if ($_POST['fix_id']){
  $sql = " update  " . $xoopsDB->prefix("mac_info") .  " set uuid='' , dangerFG=0 where id = '{$_POST['fix_id']}'  " ;
  $result = $xoopsDB->query($sql) or die($sql."<br>". $xoopsDB->error());

  $sql = " update  " . $xoopsDB->prefix("mac_up_sysinfo") .  " set  dangerFG=0 where id = '{$_POST['fix_id']}'  " ;
  $result = $xoopsDB->query($sql) or die($sql."<br>". $xoopsDB->error());

  $_GET['id']=$_POST['fix_id'] ;
}

if ($_GET['id']) {
    //上線記錄
    $open_week = get_id_online_rec($_GET['id'], $xoopsModuleConfig['iw_link_data_days']  ) ;

    //取得客戶端上傳硬體
    $sql = " select * from " . $xoopsDB->prefix("mac_info") .  " where id = '{$_GET['id']}'  " ;
    $result = $xoopsDB->query($sql) or die($sql."<br>". $xoopsDB->error());
    while ($row=$xoopsDB->fetchArray($result)) {
        $row['MaxGB']=number_format($row['memory']/(1024*1024), 0) ;
        $row['GB']=number_format($row['realmemory']/(1024*1024*1024), 0) ;
        $row["ps"]=disp_impact($row["ps"]) ;
        $now_comp = $row ;
    }
} else {
    //今天開機的記錄
    $sql = " select a.* ,i.comp , i.workgroup  , i.comp_dec , i.ps from " .
    $xoopsDB->prefix("mac_up_sysinfo") ." as  a ," . $xoopsDB->prefix("mac_info")  ." as  i " .
    "  where a.id=i.id  and a.sysinfo_day >= CURDATE()  order by    sysinfo_day   DESC " ;
    $result = $xoopsDB->query($sql) or die($sql."<br>". $xoopsDB->error());
    while ($row=$xoopsDB->fetchArray($result)) {
        $row["ps"]=disp_impact($row["ps"]) ;
        $row['w']= date('w', strtotime($row['sysinfo_day']))  ;
        $row['GB']=number_format($row['realmemory']/(1024*1024*1024), 0) ;
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
$xoopsTpl->assign("show_days", $xoopsModuleConfig['iw_link_data_days'] );

include_once 'footer.php';
