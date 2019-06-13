<?php
use XoopsModules\Tadtools\Utility;

function xoops_module_install_info_whats(&$module) {

	Utility::mk_dir(XOOPS_ROOT_PATH."/uploads/info_whats");

	return true;
}

 

?>
