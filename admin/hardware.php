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

if ($_GET['id']){
  //取得客戶端上傳硬體
  $sql = " select * from " . $xoopsDB->prefix("mac_info") .  " where id = '{$_GET['id']}'  " ;
  $result = $xoopsDB->query($sql) or die($sql."<br>". $xoopsDB->error());
  while ($row=$xoopsDB->fetchArray($result)) {
    $now_comp = $row ;
  }

  //取得客戶端上傳硬體
  $sql = " select * from " . $xoopsDB->prefix("mac_up_sysinfo") .  " where id = '{$_GET['id']}'  order by  uid    DESC " ;
  $result = $xoopsDB->query($sql) or die($sql."<br>". $xoopsDB->error());
  while ($row=$xoopsDB->fetchArray($result)) {
    $row['w']= date('w', strtotime($row['sysinfo_day']))  ;
    $list[] = $row ;
  }
}else {
  //今天開機的記錄
  $sql = " select a.* ,i.comp , i.workgroup  , i.comp_dec , i.ps from " .
    $xoopsDB->prefix("mac_up_sysinfo") ." as  a ," . $xoopsDB->prefix("mac_info")  ." as  i " .
    "  where a.id=i.id  and a.sysinfo_day >= CURDATE()  order by  uid    DESC " ;
  $result = $xoopsDB->query($sql) or die($sql."<br>". $xoopsDB->error());
  while ($row=$xoopsDB->fetchArray($result)) {
    $row['w']= date('w', strtotime($row['sysinfo_day']))  ;
    $list[] = $row ;

  }
  $mode = 'today_list' ;
}



/*-----------秀出結果區--------------*/
$xoopsTpl->assign("now_comp", $now_comp);
$xoopsTpl->assign("list", $list);
$xoopsTpl->assign("mode", $mode);


include_once 'footer.php';
