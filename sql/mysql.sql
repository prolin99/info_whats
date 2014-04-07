CREATE TABLE `mac_info` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_type` varchar(20) DEFAULT NULL,
  `item` varchar(40) NOT NULL DEFAULT '',
  `authority` varchar(40) NOT NULL DEFAULT '',
  `paid_method` varchar(40) NOT NULL DEFAULT '',
  `announce_note` varchar(40) NOT NULL DEFAULT '',
  `announce_note2` varchar(40) NOT NULL DEFAULT '',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `comment` varchar(40) DEFAULT NULL,
  `creater` varchar(20) DEFAULT NULL,
  `cooperate` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`item_id`)
) ENGINE=MyISAM ;

 CREATE TABLE IF `mac_input` (
  `id` int(11) DEFAULT NULL AUTO_INCREMENT,
  `ip` varchar(100) NOT NULL,
  `mac` varchar(100) NOT NULL,
  `user` varchar(100) NOT NULL,
  `place` varchar(100) NOT NULL,
  `uid` int(11) NOT NULL
) ENGINE=MyISAM  ;