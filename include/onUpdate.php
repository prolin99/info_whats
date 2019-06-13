<?php
use XoopsModules\Tadtools\Utility;

function xoops_module_update_info_whats(&$module, $old_version)
{
    if (!chk_add_modify()) {
        go_update_add_modify();
    }
    //增加目錄
    Utility::mk_dir(XOOPS_ROOT_PATH."/uploads/info_whats");

    //增加上傳部份的資料
    if (!chk_add_uuid()) {
        go_update_add_uuid();
    }
    //增加上傳部份的資料
    if (!chk_add_sysinfo()) {
        go_update_add_sysinfo();
    }

    if (!chk_add_online()) {
        go_update_add_online();
    }

    if (!chk_add_c_id()) {
      //財產編號
        go_update_add_c_id();
    }


    if (!chk_add_school_id()) {
      //財產編號 及 附帶螢幕等財產號
        go_update_add_school_id();
    }


    return true;

}

//ALTER TABLE `xx_mac_input` ADD `m_t` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `s_id`;
//---------------------------------------------------
function chk_add_school_id()
{
    global $xoopsDB;
    $sql = 'select count(`scM_id`)  from '.$xoopsDB->prefix('mac_info');
    //echo $sql ;
    $result = $xoopsDB->query($sql);
    if (empty($result)) {
        return false;
    }

    return true;
}

function go_update_add_school_id()
{
    global $xoopsDB;

    $sql = ' ALTER TABLE  '.$xoopsDB->prefix('mac_info'). " ADD `class_place` varchar(100) DEFAULT NULL , ADD `scM_id` varchar(100) DEFAULT NULL ,  ADD `scM_id2` varchar(100) DEFAULT NULL   ;  " ;
    //echo $sql ;
    $xoopsDB->queryF($sql);

    $sql = ' ALTER TABLE '.$xoopsDB->prefix('mac_input') ."  ADD `m_t` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP  ; ";

    $xoopsDB->queryF($sql);
}

//---------------------------------------------------
function chk_add_c_id()
{
    global $xoopsDB;
    $sql = 'select count(`c_id`)  from '.$xoopsDB->prefix('mac_input');
    //echo $sql ;
    $result = $xoopsDB->query($sql);
    if (empty($result)) {
        return false;
    }

    return true;
}

function go_update_add_c_id()
{
    global $xoopsDB;

    $sql = ' ALTER TABLE  '.$xoopsDB->prefix('mac_input'). "  ADD `uid` int(11) NOT NULL DEFAULT '0' ,   ADD `c_id` varchar(100) DEFAULT NULL ,  ADD `s_id` varchar(100) DEFAULT NULL   ;  " ;
    //echo $sql ;
    $xoopsDB->queryF($sql);
}
//---------------------------------------------------

function chk_add_online()
{
    global $xoopsDB;
    $sql = 'select count(`id`)  from '.$xoopsDB->prefix('mac_online');
    //echo $sql ;
    $result = $xoopsDB->query($sql);
    if (empty($result)) {
        return false;
    }

    return true;
}

function go_update_add_online()
{
    global $xoopsDB;

    $sql ='CREATE TABLE '. $xoopsDB->prefix('mac_online'). "
    ( `oid` bigint(20) NOT NULL AUTO_INCREMENT ,
    id int(11) NOT NULL DEFAULT '0' ,
    `online_day` DATETIME NULL ,
    `on_day` date NULL ,
    PRIMARY KEY (`oid` ),
    KEY id (id),
    INDEX   (`id`, `on_day`)
    )  ENGINE=MyISAM " ;

    //echo $sql ;
    $xoopsDB->queryF($sql);
}

//---------------------------------------------------

function chk_add_sysinfo()
{
    global $xoopsDB;
    $sql = 'select count(`uuid`)  from '.$xoopsDB->prefix('mac_up_sysinfo');
    //echo $sql ;
    $result = $xoopsDB->query($sql);
    if (empty($result)) {
        return false;
    }

    return true;
}

function go_update_add_sysinfo()
{
    global $xoopsDB;

    $sql ='CREATE TABLE '. $xoopsDB->prefix('mac_up_sysinfo'). "
    ( `uid` bigint(20) NOT NULL AUTO_INCREMENT ,
    id int(11) NOT NULL DEFAULT '0' ,
    `uuid` VARCHAR(80) NOT NULL ,
    `bios` VARCHAR(80) NULL ,
    `cpu` VARCHAR(80) NULL ,
    `memory` bigint(20) NULL ,
    `realmemory` bigint(20) NOT NULL,
    `dhcpserver` VARCHAR(80) NULL ,
    `ipaddress` VARCHAR(200) NULL ,
    `sysinfo_day` DATETIME NULL ,
    `on_day` date NULL ,
    `dangerFG` int(11)  NOT NULL DEFAULT '0' ,
    `baseboard` varchar(80) DEFAULT NULL,
    PRIMARY KEY (`uid` ),
    KEY id (id),
    KEY   (`id`,`on_day`)
    )  ENGINE=MyISAM " ;

    //echo $sql ;
    $xoopsDB->queryF($sql);
}
//----uuid--------------------------------------------------

function chk_add_uuid()
{
    global $xoopsDB;
    $sql = 'select count(`uuid`)  from '.$xoopsDB->prefix('mac_info');
    //echo $sql ;
    $result = $xoopsDB->query($sql);
    if (empty($result)) {
        return false;
    }

    return true;
}

function go_update_add_uuid()
{
    global $xoopsDB;

    // $sql = ' ALTER TABLE  '.$xoopsDB->prefix('mac_info'). '    ADD `modify_day` DATETIME    NULL   ;  ';

    $sql = 'ALTER TABLE '.$xoopsDB->prefix('mac_info'). "
     ADD `uuid` VARCHAR(64) NULL DEFAULT NULL ,
     ADD `bios` VARCHAR(80) NULL DEFAULT NULL ,
     ADD `cpu` VARCHAR(80) NULL ,
     ADD `memory` BIGINT NOT NULL ,
     ADD `realmemory` bigint(20) NOT NULL,
     ADD `dhcpserver` VARCHAR(80) NULL ,
     ADD `ipaddress` VARCHAR(200) NULL ,
     ADD `sysinfo_day` DATETIME    NULL  ,
     ADD `dangerFG` int(11)  NOT NULL DEFAULT '0' ,
     ADD `ipv4_ext` varchar(20) DEFAULT NULL,
     ADD `ipv4_in` varchar(20) DEFAULT NULL,
     ADD `baseboard` varchar(80) DEFAULT NULL,
     ADD INDEX `uuid` (`uuid`); " ;
    $xoopsDB->queryF($sql);

}


//-------------------------------------------------------

//-------------------------------------------------------
function chk_add_modify()
{
    global $xoopsDB;
    $sql = 'select count(`modify_day`)  from '.$xoopsDB->prefix('mac_info');
    //echo $sql ;
    $result = $xoopsDB->query($sql);
    if (empty($result)) {
        return false;
    }

    return true;
}

function go_update_add_modify()
{
    global $xoopsDB;

    $sql = ' ALTER TABLE  '.$xoopsDB->prefix('mac_info'). '    ADD `modify_day` DATETIME    NULL   ;  ';
    //echo $sql ;
    $xoopsDB->queryF($sql);
}
