<?php
use XoopsModules\Tadtools\Utility;

function xoops_module_uninstall_info_whats(&$module) {
	Utility::delete_directory(XOOPS_ROOT_PATH."/uploads/_info_whats") ;
	return true;
}




?>
