CREATE TABLE `mac_info` (
  id int(11) NOT NULL AUTO_INCREMENT,
  ip varchar(50) NOT NULL,
  ip_v6 varchar(128) DEFAULT NULL,
  mac varchar(60) NOT NULL,
  comp varchar(40) DEFAULT NULL,
  workgroup varchar(40) DEFAULT NULL,
  comp_dec varchar(40) DEFAULT NULL,
  ps varchar(60) DEFAULT NULL,
  recode_time datetime NOT NULL,
  creat_day datetime NOT NULL,
  deny tinyint(4) NOT NULL DEFAULT '0',
  phid varchar(20) DEFAULT NULL,
  kind varchar(20) DEFAULT NULL,
  ip_id int(11) NOT NULL DEFAULT '0',
  `point` tinyint(1) NOT NULL DEFAULT '0',
  modify_day datetime  NULL ,
  `uuid` varchar(64) DEFAULT NULL,
  `bios` varchar(80) DEFAULT NULL,
  `cpu` varchar(80) DEFAULT NULL,
  `memory` bigint(20) NOT NULL,
  `dhcpserver` varchar(80) DEFAULT NULL,
  `ipaddress` varchar(100) DEFAULT NULL,
  `sysinfo_day` datetime DEFAULT NULL,
  `dangerFG` int(11) NOT NULL DEFAULT '0',
  ipv4_ext varchar(20) DEFAULT NULL,
  ipv4_in varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mac` (`mac`),
  KEY `ip` (`ip`),
  KEY `uuid` (`uuid`)
) ENGINE=MyISAM ;

 CREATE TABLE   `mac_input` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(100) DEFAULT NULL,
  `mac` varchar(100) DEFAULT NULL,
  `user` varchar(100) DEFAULT NULL,
  `place` varchar(100) DEFAULT NULL,
  `uid` int(11) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  ;


CREATE TABLE   `mac_up_sysinfo` (
  `uid` bigint(20) NOT NULL AUTO_INCREMENT,
  `id` int(11) NOT NULL DEFAULT '0',
  `uuid` varchar(80) NOT NULL,
  `bios` varchar(80) DEFAULT NULL,
  `cpu` varchar(80) DEFAULT NULL,
  `memory` bigint(20) DEFAULT NULL,
  `dhcpserver` varchar(80) DEFAULT NULL,
  `ipaddress` varchar(100) DEFAULT NULL,
  `sysinfo_day` datetime DEFAULT NULL,
  `dangerFG` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`),
  KEY `ip` (`id`),
  KEY `uuid` (`uuid`,`sysinfo_day`)
)  ENGINE=MyISAM  ;


CREATE TABLE  `mac_online`
  `oid` BIGINT NOT NULL AUTO_INCREMENT ,
  `id` int(11) NOT NULL DEFAULT '0' ,
  `online_day` DATETIME NULL ,
  PRIMARY KEY (`oid` ),
  KEY ip (id),
  INDEX   (`id`, `online_day`)
)  ENGINE=MyISAM  ;
