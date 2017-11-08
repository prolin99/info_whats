<?php

function xoops_module_update_info_whats(&$module, $old_version)
{
    if (!chk_add_modify()) {
        go_update_add_modify();
    }
    //增加目錄
    mk_dir(XOOPS_ROOT_PATH."/uploads/info_whats");

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

    return true;

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
    `memory` BIGINT NULL ,
    `realmemory` bigint(20) NOT NULL,
    `dhcpserver` VARCHAR(80) NULL ,
    `ipaddress` VARCHAR(100) NULL ,
    `sysinfo_day` DATETIME NULL ,
    `on_day` date NULL ,
    `dangerFG` int(11)  NOT NULL DEFAULT '0' ,
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
     ADD `ipaddress` VARCHAR(100) NULL ,
     ADD `sysinfo_day` DATETIME    NULL  ,
     ADD `dangerFG` int(11)  NOT NULL DEFAULT '0' ,
     ADD `ipv4_ext` varchar(20) DEFAULT NULL,
     ADD `ipv4_in` varchar(20) DEFAULT NULL,
     ADD INDEX `uuid` (`uuid`); " ;
    $xoopsDB->queryF($sql);

}


//-------------------------------------------------------
//建立目錄
function mk_dir($dir=""){
    //若無目錄名稱秀出警告訊息
    if(empty($dir))return;
    //若目錄不存在的話建立目錄
    if (!is_dir($dir)) {
        umask(000);
        //若建立失敗秀出警告訊息
        mkdir($dir, 0777);
    }
}

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
