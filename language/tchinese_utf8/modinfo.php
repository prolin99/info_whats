<?php
//  ------------------------------------------------------------------------ //
// 本模組由 prolin 製作
// 製作日期：2014-02-16
// $Id:$
// ------------------------------------------------------------------------- //

define("_MI_INFOWHOS_NAME","網路設備記錄");
define("_MI_INFOWHOS_AUTHOR","prolin (prolin@tn.edu.tw)");
define("_MI_INFOWHOS_CREDITS","prolin");
define("_MI_INFOWHOS_DESC","網路設備記錄");

define("_MI_INFOWHOS_CONFIG_TITLE1","網段分配");
define("_MI_INFOWHOS_CONFIG_DESC1","網段分配規劃");

define("_MI_INFOWHOS_CONFIG_TITLE2","學校 ipv4");
define("_MI_INFOWHOS_CONFIG_DESC2","以逗號分隔不同的網段開頭，如 192.168.1,192.168.2");

define("_MI_INFOWHOS_CONFIG_TITLE3","學校 ipv6");
define("_MI_INFOWHOS_CONFIG_DESC3","IPV6網段開頭碼，如：2001:288:752a:");

define("_MI_INFOWHOS_CONFIG_TITLE4","ipv4動態分配區，不出現在下方 DHCP 設定內容中");
define("_MI_INFOWHOS_CONFIG_DESC4","動態分配區，如：192.168.1.100~192.168.1.150");

define("_MI_INFOWHOS_CONFIG_TITLE5","nmap掃描結果檔(配合 ip-scan.sh)");
define("_MI_INFOWHOS_CONFIG_DESC5","nmap記錄檔，要放在網頁位置，飛蕃雲端使用者，請以 IP 網址。");

define("_MI_INFOWHOS_CONFIG_TITLE6","DHCP記錄檔");
define("_MI_INFOWHOS_CONFIG_DESC6","需要在同一台機器，例：/var/lib/dhcpd/dhcpd.leases ");

define("_MI_INFOWHOS_CONFIG_TITLE7","定時連線的代號(配合 ip-scan.sh)");
define("_MI_INFOWHOS_CONFIG_DESC7","自訂一組代碼，.../modules/comp.php?do=代碼");

define("_MI_INFOWHOS_CONFIG_TITLE8","arp 掃描結果檔(配合 ip-scan.sh)");
define("_MI_INFOWHOS_CONFIG_DESC8","arp 記錄檔，要放在網頁位置，飛蕃雲端使用者，請以 IP 網址。");


define("_MI_INFOWHOS_CONFIG_TITLE9","是否開放登記");
define("_MI_INFOWHOS_CONFIG_DESC9","開放用戶登記使用的電腦");

define("_MI_INFOWHOS_CONFIG_TITLE11","未啟動主機是否EMAIL警告");
define("_MI_INFOWHOS_CONFIG_DESC11","設定為重要且要警告的主機偵測至未開機時，是否要 EMAIL 通知？");

define("_MI_INFOWHOS_CONFIG_TITLE12","通知郵件位置");
define("_MI_INFOWHOS_CONFIG_DESC12","EMAIL 位置");
?>
