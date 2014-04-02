<?php
//  ------------------------------------------------------------------------ //
// 本模組由 prolin 製作
// 製作日期：2014-03-01
// $Id:$
// ------------------------------------------------------------------------- //
/*-----------引入檔案區--------------*/


include_once "header.php";
include_once XOOPS_ROOT_PATH."/header.php";
$xoopsOption['template_main'] = "info_index_tpl.html";


/*-----------function區--------------*/


 
 
/*-----------執行動作判斷區----------*/
//$op=empty($_REQUEST['op'])?"":$_REQUEST['op'];
 //判別是否在本校 IP 

 
$data = get_mac() ;



/*-----------秀出結果區--------------*/
$xoopsTpl->assign( "toolbar" , toolbar_bootstrap($interface_menu)) ;
$xoopsTpl->assign( "bootstrap" , get_bootstrap()) ;
$xoopsTpl->assign( "jquery" , get_jquery(true)) ;
$xoopsTpl->assign( "data" , $data ) ;
 
include_once XOOPS_ROOT_PATH.'/footer.php';

?>