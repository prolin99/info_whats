<?php
//  ------------------------------------------------------------------------ //
// 本模組由 prolin 製作
// 製作日期：2014-03-01
// $Id:$
// ------------------------------------------------------------------------- //
$i=0 ;
$adminmenu[$i]['title'] = '設備記錄';
$adminmenu[$i]['link'] = "admin/index.php";
$adminmenu[$i]['desc'] = '設備記錄' ;
$adminmenu[$i]['icon'] = 'images/admin/home.png' ;

$i++ ;
$adminmenu[$i]['title'] = '重要設備';
$adminmenu[$i]['link'] = "admin/index.php?do=point";
$adminmenu[$i]['desc'] = '重要設備' ;
$adminmenu[$i]['icon'] = 'images/admin/main.png' ;

$i++ ;
$adminmenu[$i]['title'] = '未登記';
$adminmenu[$i]['link'] = "admin/index.php?do=mystery";
$adminmenu[$i]['desc'] = '未登記設備' ;
$adminmenu[$i]['icon'] = 'images/admin/logadm.png' ;

$i++ ;
$adminmenu[$i]['title'] = "關於";
$adminmenu[$i]['link'] = "admin/about.php";
$adminmenu[$i]['desc'] = '說明';
$adminmenu[$i]['icon'] = 'images/admin/about.png';

?>