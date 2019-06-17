#!/usr/bin/env python3
#使用 arp 方式取得 ip 和 mac
#採多核多緒模式執行
#sudo python3 ip_arp_mt.py

from scapy.all import *
import sys,getopt
from multiprocessing.dummy import Pool as ThreadPool



def get_it(ip):
    #arp 
    arpPkt = Ether(dst="ff:ff:ff:ff:ff:ff")/ARP(pdst=ip, hwdst="ff:ff:ff:ff:ff:ff")
    res = srp1(arpPkt, timeout=1, verbose=0)
    if res:
        print ("IP= " + res.psrc + "    , MAC= " + res.hwsrc )


if __name__ == '__main__':
    ip_list=[]
    for i in range(256):
        ip_list.append("120.116.24.%s" % i)
    for i in range(254):
        ip_list.append("120.116.25.%s" % i)

    pool = ThreadPool(4)
    res=pool.map(get_it,ip_list)
    pool.close()
    pool.join()
