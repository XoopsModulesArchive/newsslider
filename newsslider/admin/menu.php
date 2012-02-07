<?php
/**
 * $Id: menu.php v 1.0 21 Jan 2012 Yerres Exp $
 * Module: newsslider
 * Version: 1.1
 * Author: Yerres
 * Licence: GNU
 */
global $xoopsModule;
$module_handler =& xoops_gethandler("module");
$xoopsModule =& XoopsModule::getByDirname('newsslider');
$modid =& $module_handler->get($xoopsModule->getVar("mid"));

$i = 0;
if (strstr(XOOPS_VERSION, "XOOPS 2.0")){
  $adminmenu[$i]['title'] = _MI_NWS_MENU;
  $adminmenu[$i]['link'] = "admin/myblocksadmin.php";
} else {
  $adminmenu[$i]['title'] = _MI_NWS_MENU;// uncomment if necessary, this can cause errors
  $adminmenu[$i]['link'] = '../../modules/system/admin.php?fct=blocksadmin&op=list&filter=1&selgen=' . $xoopsModule->getVar('mid') . '&selmod=-2&selgrp=-1&selvis=-1';
  //$adminmenu[$i]['link'] = '../../modules/system/admin.php?fct=blocksadmin&op=list';
}

if (isset($xoopsModule)) {
  $i=0;
	$headermenu[$i]['title'] = _MI_NWS_UPDATEMODULE;
	$headermenu[$i]['link'] = XOOPS_URL . "/modules/system/admin.php?fct=modulesadmin&op=update&module=" . $xoopsModule->getVar('dirname');
}
?>