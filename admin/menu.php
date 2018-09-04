<?php
//  ------------------------------------------------------------------------ //
// 本模組由 prolin 製作
// 製作日期：2014-03-01
// $Id:$
// ------------------------------------------------------------------------- //
$adminmenu = array();

$i=0 ;



$adminmenu[$i]['title'] = '設備記錄';
$adminmenu[$i]['link'] = "admin/index.php";
$adminmenu[$i]['desc'] = '設備記錄' ;
$adminmenu[$i]['icon'] = 'images/admin/home.png' ;

$i++ ;
$adminmenu[$i]['title'] = '警示';
$adminmenu[$i]['link'] = "admin/danger.php";
$adminmenu[$i]['desc'] = '可能有狀況的設備' ;
$adminmenu[$i]['icon'] = 'images/admin/messagebox_warning.png' ;

$i++ ;
$adminmenu[$i]['title'] = '重要設備';
$adminmenu[$i]['link'] = "admin/index.php?do=point";
$adminmenu[$i]['desc'] = '重要設備' ;
$adminmenu[$i]['icon'] = 'images/admin/success.png' ;

$i++ ;
$adminmenu[$i]['title'] = '目前填表';
$adminmenu[$i]['link'] = "admin/index.php?do=input&sort=6";
$adminmenu[$i]['desc'] = '目前登記設備' ;
$adminmenu[$i]['icon'] = 'images/admin/main.png' ;

$i++ ;
$adminmenu[$i]['title'] = '未登記';
$adminmenu[$i]['link'] = "admin/index.php?do=mystery";
$adminmenu[$i]['desc'] = '未登記設備' ;
$adminmenu[$i]['icon'] = 'images/admin/problem.png' ;

$i++ ;
$adminmenu[$i]['title'] = 'AP內';
$adminmenu[$i]['link'] = "admin/ext_in.php";
$adminmenu[$i]['desc'] = '在 AP 後' ;
$adminmenu[$i]['icon'] = 'images/admin/router.png' ;




$i++ ;
$adminmenu[$i]['title'] = '開機記錄';
$adminmenu[$i]['link'] = "admin/hardware.php";
$adminmenu[$i]['desc'] = '開機上傳記錄' ;
$adminmenu[$i]['icon'] = 'images/admin/turn-on.png' ;

$i++ ;
$adminmenu[$i]['title'] = "關於";
$adminmenu[$i]['link'] = "admin/about.php";
$adminmenu[$i]['desc'] = '說明';
$adminmenu[$i]['icon'] = 'images/admin/about.png';

?>
