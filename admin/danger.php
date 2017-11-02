<?php
//  ------------------------------------------------------------------------ //
// 本模組由 prolin 製作
// 製作日期：2014-03-01
// $Id:$
// ------------------------------------------------------------------------- //

/*-----------引入檔案區--------------*/
$xoopsOption['template_main'] = "info_danger_tpl.html";
include_once "header.php";
include_once "../function.php";


//取得硬體有問題部份
$sql = " select * from " . $xoopsDB->prefix("mac_info") .  " where dangerFg<>0  order by  id    DESC " ;
$result = $xoopsDB->query($sql) or die($sql."<br>". $xoopsDB->error());
while ($row=$xoopsDB->fetchArray($result)) {
  $danger_list[] = $row ;
}

//取得重要但未上線資料
$sql = " select * from " . $xoopsDB->prefix("mac_info") .  " where recode_time <( DATE_ADD(now() ,INTERVAL -1 HOUR ))  and point >0  order by  id    DESC " ;
//echo $sql;
$result = $xoopsDB->query($sql) or die($sql."<br>". $xoopsDB->error());
while ($row=$xoopsDB->fetchArray($result)) {
  $import_list[] = $row ;
}









/*-----------秀出結果區--------------*/
$xoopsTpl->assign("danger_list", $danger_list);
$xoopsTpl->assign("import_list", $import_list);


include_once 'footer.php';
