<?php
//  ------------------------------------------------------------------------ //
// 本模組由 prolin 製作
// 製作日期：2014-03-01
// $Id:$
// ------------------------------------------------------------------------- //
/*-----------引入檔案區--------------*/

$xoopsOption['template_main'] = "info_index_tpl.html";
include_once "header.php";
include_once XOOPS_ROOT_PATH."/header.php";



/*-----------function區--------------*/



//$uid=$xoopsUser->uid() ;
/*-----------執行動作判斷區----------*/
//$op=empty($_REQUEST['op'])?"":$_REQUEST['op'];


if ($_POST['act_add']  and $_POST['user']) {
    $myts =& MyTextSanitizer::getInstance();
    $_POST['ip']=$myts->addSlashes($_POST['ip']);
    $_POST['mac']=$myts->addSlashes($_POST['mac']);
    $_POST['user']=$myts->addSlashes($_POST['user']);
    $_POST['place']=$myts->addSlashes($_POST['place']);
    /*
    if ($_POST['id'] )
        $sql = "UPDATE " . $xoopsDB->prefix("mac_input") .  " set  user ='{$_POST['user']}' , place='{$_POST['place']}' where id = '{$_POST['id']}' " ;
    else
    */
    $uid = $uid+0 ;

    $sql = " insert into  " . $xoopsDB->prefix("mac_input") .  "  (id ,ip ,mac ,user,place,uid )
				     values ('0','{$_POST['ip']}','{$_POST['mac']}','{$_POST['user']}' , '{$_POST['place']}' ,'$uid' ) " ;
    $result = $xoopsDB->query($sql) or die($sql."<br>". $xoopsDB->error());

    $have_input ='記錄已寫入' ;
}


//--------------------------------------------------------------------------------

$data = get_mac() ;

//取得填報資料
$data_get = get_from_data($uid, $data['ip'], $data['mac']) ;
//已寫入 mac_info 中的資料。
$data_rec = get_from_rec($uid, $data['ip'], $data['mac']) ;
//取得上線記錄 3天，10分 間隔
$online = get_id_online_rec($data_rec['id'] ,7   ) ;

$week_name=array(1=>'一',2=>'二',3=>'三',4=>'四',5=>'五',6=>'六', 0=>'日') ;
/*-----------秀出結果區--------------*/

$xoopsTpl->assign("toolbar", toolbar_bootstrap($interface_menu)) ;
//$xoopsTpl->assign("bootstrap", get_bootstrap()) ;
//$xoopsTpl->assign("jquery", get_jquery(true)) ;
$xoopsTpl->assign("data", $data) ;
$xoopsTpl->assign("data_get", $data_get) ;
$xoopsTpl->assign("data_rec", $data_rec) ;
$xoopsTpl->assign("have_input", $have_input) ;
$xoopsTpl->assign("input_mode", $xoopsModuleConfig['iw_input']) ;
$xoopsTpl->assign("client_file", $xoopsModuleConfig['iw_FtpClient']) ;
$xoopsTpl->assign("online", $online) ;
$xoopsTpl->assign("week_name", $week_name) ;

include_once XOOPS_ROOT_PATH.'/footer.php';
