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

if ($_GET['setDay']>0)
  $set_day = 0 - $_GET['setDay'] ;
else
  $set_day = -7;


//取得硬體有問題部份
$sql = " select * from " . $xoopsDB->prefix("mac_info") .  " where dangerFg<>0  order by  id    DESC " ;
$result = $xoopsDB->query($sql) or die($sql."<br>". $xoopsDB->error());
while ($row=$xoopsDB->fetchArray($result)) {
  $danger_list[] = $row ;

}
$all_list['hardware']=$danger_list ;

//取得重要但未上線資料
$sql = " select * from " . $xoopsDB->prefix("mac_info") .  " where recode_time <( DATE_ADD(CURDATE()  ,INTERVAL -1 HOUR ))  and point >0  order by  id    DESC " ;
//echo $sql;
$result = $xoopsDB->query($sql) or die($sql."<br>". $xoopsDB->error());
while ($row=$xoopsDB->fetchArray($result)) {
  $import_list[] = $row ;
}
$all_list['offline']=$import_list ;




//一日內開機超過 3 次
$sql = " select  i.* , count(*) as cc  from " . $xoopsDB->prefix("mac_up_sysinfo") . " as  u , " . $xoopsDB->prefix("mac_info") ." as i  " .
" where u.id= i.id   and (u.on_day >= ( DATE_ADD(CURDATE()  ,INTERVAL $set_day DAY )) )    group by u.id,on_day   " .
"  having cc > 3 "
;

$result = $xoopsDB->query($sql) or die($sql."<br>". $xoopsDB->error());
while ($row=$xoopsDB->fetchArray($result)) {
  $over3_list[] = $row ;
}
$all_list['times3']=$over3_list;


//在星期六、日開機  （一個月內）
$sql = " select DISTINCT i.* from " . $xoopsDB->prefix("mac_up_sysinfo") . " as  u , " . $xoopsDB->prefix("mac_info") ." as i  " .
" where u.id= i.id   and (u.on_day >= ( DATE_ADD(CURDATE()  ,INTERVAL $set_day DAY )) )  and  (  (DAYOFWEEK(u.on_day) =1) or (DAYOFWEEK(u.on_day) = 7)  )   order by  u.id     " ;
//echo $sql;
$result = $xoopsDB->query($sql) or die($sql."<br>". $xoopsDB->error());
while ($row=$xoopsDB->fetchArray($result)) {
  $notworkday_list[] = $row ;
}
$all_list['notworkday']=$notworkday_list ;

//愈七日未開機
$sql = " select  i.* , max(on_day) as maxd from " . $xoopsDB->prefix("mac_up_sysinfo") . " as  u , " . $xoopsDB->prefix("mac_info") ." as i  " .
" where u.id= i.id      group by u.id   " .
"  having maxd < ( DATE_ADD(CURDATE()  ,INTERVAL $set_day DAY )) "
;

$result = $xoopsDB->query($sql) or die($sql."<br>". $xoopsDB->error());
while ($row=$xoopsDB->fetchArray($result)) {
  $over7_list[] = $row ;
}
$all_list['over7']=$over7_list;


//前一天 非重要設備 21：00～ 12：00 上線
$sql = " select DISTINCT i.*    ,o.on_day   from ". $xoopsDB->prefix("mac_info") ." as i  , " .$xoopsDB->prefix("mac_online") . " as  o " .
" where i.id= o.id  and i.point=0 and (o.on_day >= ( DATE_ADD(CURDATE() ,INTERVAL -1 DAY )) )  and HOUR(online_day) > 21   order  by  i.id    " ;
//echo $sql;
$result = $xoopsDB->query($sql) or die($sql."<br>". $xoopsDB->error());
while ($row=$xoopsDB->fetchArray($result)) {
  $mylist[] = $row ;
}
$all_list['tooLate']=$mylist;



//非重要設備，在星期六、日上線  （一個月內）
$sql = " select DISTINCT i.*   from ". $xoopsDB->prefix("mac_info") ." as i  , " .$xoopsDB->prefix("mac_online") . " as  o " .
" where i.id= o.id  and i.point=0 and (o.on_day >= ( DATE_ADD(CURDATE()  ,INTERVAL $set_day DAY )) )  and  (  (DAYOFWEEK(o.on_day) =1) or (DAYOFWEEK(o.on_day) = 7)  )   order  by  i.id    " ;
//echo $sql;
$result = $xoopsDB->query($sql) or die($sql."<br>". $xoopsDB->error());
while ($row=$xoopsDB->fetchArray($result)) {
  $notworkday_online_list[] = $row ;
}
$all_list['notworkday_online']=$notworkday_online_list;








$cht_list= array(
  'hardware'=>'硬體配備有問題' ,
  'offline'=>'重要設備離線一小時以上' ,
  'tooLate'=>'非重要設備昨晚上線' ,
  'notworkday'=>"假日開機（ $set_day 天內）" ,
  'times3'=>"一天內開機超過3次（ $set_day 天內）" ,
  'over7'=>"逾 $set_day 日未開機" ,
  'notworkday_online'=>"非重要設備假日上線（ $set_day 天內）"
) ;






/*-----------秀出結果區--------------*/
/*
$xoopsTpl->assign("danger_list", $danger_list);
$xoopsTpl->assign("import_list", $import_list);
$xoopsTpl->assign("notworkday_list", $notworkday_list);
$xoopsTpl->assign("over7_list", $over7_list);
$xoopsTpl->assign("notworkday_online_list", $notworkday_online_list);
*/

$xoopsTpl->assign("cht_list", $cht_list);

$xoopsTpl->assign("all_list", $all_list);
include_once 'footer.php';
