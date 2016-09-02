<?php

function xoops_module_update_info_whats(&$module, $old_version)
{
    if (!chk_add_modify()) {
        go_update_add_modify();
    }

    return true;
}

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
