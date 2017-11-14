<?php
//  ------------------------------------------------------------------------ //
// 本模組由 prolin 製作
// 製作日期：2014-03-01
// $Id:$
// ------------------------------------------------------------------------- //
/*-----------引入檔案區--------------*/

include_once 'header.php';

/*-----------function區--------------*/

/*-----------執行動作判斷區----------*/

$key = $xoopsModuleConfig['iw_key'];
if ($_POST['do'] <> $key) {
    echo 'wrong' ;
    exit() ;
}



$data = get_mac() ;

if (!$data['ip'] ){
  echo 'deny ip  ,not in school !' ;
  exit() ;
}



$uploaddir = XOOPS_ROOT_PATH."/uploads/info_whats/" ;
$uploadfile = $uploaddir . basename($_FILES['uploaded']['name']);


if (move_uploaded_file($_FILES['uploaded']['tmp_name'], $uploadfile)) {
    //echo "File is valid, and was successfully uploaded.\n";
    get_system_info($uploadfile) ;
    //刪除
    unlink($uploadfile) ;
}

//echo 'Here is some more debugging info:';
//print_r($_FILES);
