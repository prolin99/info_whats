	#!/bin/bash

	PATH=/bin:/sbin:/usr/bin:/usr/sbin:/usr/local/bin:/usr/local/sbin:~/bin
	export PATH

	#nmap 掃描網載，產生記錄文字檔，要放在網頁可以存區處 (以下為範例，要修改網域，及要存放的位置)，
	#在偏好設定，nmap掃描結果檔   http://網址/nmap.txt ，加入 T3 看看可否更正確
	#/usr/bin/nmap -sP -T3 120.116.24.0/24 > /var/www/html/nmap.txt
	#改用 ip_arp_mt python
    /usr/bin/python3  /root/sh/ip_arp_mt.py >  /var/www/html/tmp/nmap.txt 2> /dev/null
	#取得 ipv6 資訊
	#在偏好設定，arp 掃描結果檔   http://網址/arp.txt
	#這部份無法正確反應 ipv6 設備是否在上線狀態，移除不用
	#ping6 -I eth0 -c3 ff02::1 > /dev/null
	#/sbin/ip -6 neigh show > /var/www/html/arp.txt


	#其中代碼為偏好設定--定時連線的代號 (現預設 info_whats )
	wget -O /dev/null http://你的網址/modules/info_whats/comp.php?do=info_whats   2> /dev/null
