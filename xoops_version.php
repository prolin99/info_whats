<?php
//  ------------------------------------------------------------------------ //
// 本模組由 prolin 製作
// 製作日期：2014-02-16
// $Id:$
// ------------------------------------------------------------------------- //

//---基本設定---//

$modversion['name'] ='網路設備記錄';				//模組名稱
$modversion['version']	= '0.8';				//模組版次
$modversion['author'] = 'prolin(prolin@tn.edu.tw)';		//模組作者
$modversion['description'] ='網路設備記錄';			//模組說明
$modversion['credits']	= 'prolin';				//模組授權者
$modversion['license']		= "GPL see LICENSE";		//模組版權
$modversion['official']		= 0;				//模組是否為官方發佈1，非官方0
$modversion['image']		= "images/logo.png";		//模組圖示
$modversion['dirname'] = basename(dirname(__FILE__));		//模組目錄名稱

//---模組狀態資訊---//
//$modversion['status_version'] = '0.8';
$modversion['release_date'] = '2014-04-10';
$modversion['module_website_url'] = 'https://github.com/prolin99/info_whats';
$modversion['module_website_name'] = 'prolin';
$modversion['module_status'] = 'release';
$modversion['author_website_url'] = 'http://www.syps.tn.edu.tw';
$modversion['author_website_name'] = 'prolin';
$modversion['min_php']= 5.2;



//---啟動後台管理界面選單---//
$modversion['system_menu'] = 1;//---資料表架構---//
$modversion['sqlfile']['mysql'] = "sql/mysql.sql";
$modversion['tables'][1] = "mac_info";
$modversion['tables'][1] = "mac_input";

//---管理介面設定---//
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = "admin/index.php";
$modversion['adminmenu'] = "admin/menu.php";
 
//---使用者主選單設定---//
$modversion['hasMain'] = 1;


//---樣板設定---要有指定，才會編譯動作，//
$modversion['templates'] = array();
$i=1;
$modversion['templates'][$i]['file'] = 'info_admin_tpl.html';
$modversion['templates'][$i]['description'] = 'info_admin_tpl.html';
$i++ ;
$modversion['templates'][$i]['file'] = 'info_index_tpl.html';
$modversion['templates'][$i]['description'] = 'info_index_tpl.html';
 
$i=0 ;
//偏好設定


$i++ ;
$modversion['config'][$i]['name'] = 'iw_ip_rang';
$modversion['config'][$i]['title']   = '_MI_INFOWHOS_CONFIG_TITLE1';
$modversion['config'][$i]['description'] = '_MI_INFOWHOS_CONFIG_DESC1';
$modversion['config'][$i]['formtype']    = 'textarea';
$modversion['config'][$i]['valuetype']   = 'text';
$modversion['config'][$i]['default'] ="網段分配規劃填寫才此" ;

$i++ ;
$modversion['config'][$i]['name'] = 'iw_ip_v4';
$modversion['config'][$i]['title']   = '_MI_INFOWHOS_CONFIG_TITLE2';
$modversion['config'][$i]['description'] = '_MI_INFOWHOS_CONFIG_DESC2';
$modversion['config'][$i]['formtype']    = 'textbox';
$modversion['config'][$i]['valuetype']   = 'text';
$modversion['config'][$i]['default'] ="120.116.24,120.116.25" ;

$i++ ;
$modversion['config'][$i]['name'] = 'iw_ip_v6';
$modversion['config'][$i]['title']   = '_MI_INFOWHOS_CONFIG_TITLE3';
$modversion['config'][$i]['description'] = '_MI_INFOWHOS_CONFIG_DESC3';
$modversion['config'][$i]['formtype']    = 'textbox';
$modversion['config'][$i]['valuetype']   = 'text';
$modversion['config'][$i]['default'] ="2001:288:752a:" ;



$i++ ;
$modversion['config'][$i]['name'] = 'iw_ip_v4_dhcp';
$modversion['config'][$i]['title']   = '_MI_INFOWHOS_CONFIG_TITLE4';
$modversion['config'][$i]['description'] = '_MI_INFOWHOS_CONFIG_DESC4';
$modversion['config'][$i]['formtype']    = 'textbox';
$modversion['config'][$i]['valuetype']   = 'text';
$modversion['config'][$i]['default'] ="120.116.25.42~120.116.25.62" ;


$i++ ;
$modversion['config'][$i]['name'] = 'iw_ip_scan_url';
$modversion['config'][$i]['title']   = '_MI_INFOWHOS_CONFIG_TITLE5';
$modversion['config'][$i]['description'] = '_MI_INFOWHOS_CONFIG_DESC5';
$modversion['config'][$i]['formtype']    = 'textbox';
$modversion['config'][$i]['valuetype']   = 'text';
$modversion['config'][$i]['default'] ="http://你的網址/nmap.txt" ;

$i++ ;
$modversion['config'][$i]['name'] = 'iw_ip_neigh_url';
$modversion['config'][$i]['title']   = '_MI_INFOWHOS_CONFIG_TITLE8';
$modversion['config'][$i]['description'] = '_MI_INFOWHOS_CONFIG_DESC8';
$modversion['config'][$i]['formtype']    = 'textbox';
$modversion['config'][$i]['valuetype']   = 'text';
$modversion['config'][$i]['default'] ="http://你的網址/arp.txt" ;


$i++ ;
$modversion['config'][$i]['name'] = 'iw_ip_dhcp_log';
$modversion['config'][$i]['title']   = '_MI_INFOWHOS_CONFIG_TITLE6';
$modversion['config'][$i]['description'] = '_MI_INFOWHOS_CONFIG_DESC6';
$modversion['config'][$i]['formtype']    = 'textbox';
$modversion['config'][$i]['valuetype']   = 'text';
$modversion['config'][$i]['default'] ="" ;

$i++ ;
$modversion['config'][$i]['name'] = 'iw_key';
$modversion['config'][$i]['title']   = '_MI_INFOWHOS_CONFIG_TITLE7';
$modversion['config'][$i]['description'] = '_MI_INFOWHOS_CONFIG_DESC7';
$modversion['config'][$i]['formtype']    = 'textbox';
$modversion['config'][$i]['valuetype']   = 'text';
$modversion['config'][$i]['default'] ="info_whats" ;


$i++ ;
$modversion['config'][$i]['name'] = 'iw_input';
$modversion['config'][$i]['title']   = '_MI_INFOWHOS_CONFIG_TITLE9';
$modversion['config'][$i]['description'] = '_MI_INFOWHOS_CONFIG_DESC9';
$modversion['config'][$i]['formtype']    = 'yesno';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default'] ="1" ;
?>