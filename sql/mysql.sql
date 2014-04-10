CREATE TABLE `mac_info` (
  id int(11) NOT NULL AUTO_INCREMENT,
  ip varchar(50) NOT NULL,
  ip_v6 varchar(128) DEFAULT NULL,
  mac varchar(60) NOT NULL,
  comp varchar(40) NOT NULL,
  workgroup varchar(40) DEFAULT NULL,
  comp_dec varchar(40) DEFAULT NULL,
  ps varchar(60) DEFAULT NULL,
  recode_time datetime NOT NULL,
  creat_day datetime NOT NULL,
  deny tinyint(4) NOT NULL DEFAULT '0',
  phid varchar(20) NOT NULL,
  kind varchar(20) NOT NULL,
  ip_id int(11) NOT NULL,
  `point` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (id),
  UNIQUE KEY mac (mac),
  KEY ip (ip)
) ENGINE=MyISAM ;

 CREATE TABLE   `mac_input` (
  `id` int(11) DEFAULT NULL AUTO_INCREMENT,
  `ip` varchar(100) NOT NULL,
  `mac` varchar(100) NOT NULL,
  `user` varchar(100) NOT NULL,
  `place` varchar(100) NOT NULL,
  `uid` int(11) NOT NULL ,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  ;