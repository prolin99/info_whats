<?php
//  ------------------------------------------------------------------------ //
// 本模組由 prolin 製作
// 製作日期：2014-03-01
// $Id:$
// ------------------------------------------------------------------------- //
/*-----------引入檔案區--------------*/
use XoopsModules\Tadtools\Utility;

$xoopsOption['template_main'] = "info_index_tpl.html";
include_once "header.php";
include_once XOOPS_ROOT_PATH."/header.php";



/*-----------function區--------------*/



//$uid=$xoopsUser->uid() ;
/*-----------執行動作判斷區----------*/
//$op=empty($_REQUEST['op'])?"":$_REQUEST['op'];


if ($_POST['act_add']  and $_POST['user']) {
    $myts =& MyTextSanitizer::getInstance();
    $_POST['ip']=$xoopsDB->escape($_POST['ip']);
    $_POST['mac']=$xoopsDB->escape($_POST['mac']);
    $_POST['user']=$xoopsDB->escape($_POST['user']);
    $_POST['place']=$xoopsDB->escape($_POST['place']);
    $_POST['c_id']=$xoopsDB->escape($_POST['c_id']);
    $_POST['s_id']=$xoopsDB->escape($_POST['s_id']);
    $uid = $xoopsDB->escape($_POST['uid']);
    /*
    if ($_POST['id'] )
        $sql = "UPDATE " . $xoopsDB->prefix("mac_input") .  " set  user ='{$_POST['user']}' , place='{$_POST['place']}' where id = '{$_POST['id']}' " ;
    else
    */
    $uid = $uid+0 ;

    $sql = " insert into  " . $xoopsDB->prefix("mac_input") .  "  (id ,ip ,mac ,user,place,uid ,c_id , s_id )
				     values ('0','{$_POST['ip']}','{$_POST['mac']}','{$_POST['user']}' , '{$_POST['place']}' ,'$uid' ,'{$_POST['c_id']}' ,'{$_POST['s_id']}'  ) " ;
    $result = $xoopsDB->query($sql) or die($sql."<br>". $xoopsDB->error());

    $have_input ='記錄已寫入' ;
}


//--------------------------------------------------------------------------------

$data = get_mac() ;

//取得填報資料
$data_get = get_from_data($uid, $data['ip'], $data['mac']) ;

//取得已在 mac_info 記錄中的資料。
$data_rec = get_from_rec($uid, $data['ip'], $data['mac']) ;

//財產編碼加 樣式
$data_rec["ps"]=disp_impact($data_rec["ps"]) ;

//取得上線記錄 3天，10分 間隔
$online = get_id_online_rec($data_rec['id'] ,7   ) ;

$week_name=array(1=>'一',2=>'二',3=>'三',4=>'四',5=>'五',6=>'六', 0=>'日') ;
/*-----------秀出結果區--------------*/

$xoopsTpl->assign("toolbar", Utility::toolbar_bootstrap($interface_menu)) ;
//$xoopsTpl->assign("bootstrap", Utility::get_bootstrap()) ;
//$xoopsTpl->assign("jquery", Utility::get_jquery(true)) ;
$xoopsTpl->assign("data", $data) ;
$xoopsTpl->assign("data_get", $data_get) ;
$xoopsTpl->assign("data_rec", $data_rec) ;
$xoopsTpl->assign("have_input", $have_input) ;
$xoopsTpl->assign("input_mode", $xoopsModuleConfig['iw_input']) ;
$xoopsTpl->assign("client_file", $xoopsModuleConfig['iw_FtpClient']) ;
$xoopsTpl->assign("online", $online) ;
$xoopsTpl->assign("week_name", $week_name) ;

include_once XOOPS_ROOT_PATH.'/footer.php';
