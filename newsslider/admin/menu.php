<?php
/**
* $Id: menu.php,v 1.0 2011/12/03 11:52:53 yerres Exp $
* Module: newsslider
* Licence: GNU
*/

defined("XOOPS_ROOT_PATH") or die("XOOPS root path not defined");

$dirname = basename(dirname(dirname(__FILE__)));
$module_handler = xoops_gethandler('module');
$module = $module_handler->getByDirname($dirname);
$modid =& $module_handler->get($module->getVar("mid"));
$pathIcon32 = $module->getInfo('icons32');

//xoops_loadLanguage('admin', $dirname);
global $xoopsModule;
$module_handler =& xoops_gethandler("module");
$xoopsModule =& XoopsModule::getByDirname('newsslider');
$modid =& $module_handler->get($xoopsModule->getVar("mid"));


$adminmenu = array();

$i = 1;
$adminmenu[$i]["title"] = _MI_NWS_ADMENU1;
$adminmenu[$i]["link"] = 'admin/index.php';
$adminmenu[$i]["icon"] = $pathIcon32.'/home.png';
$i++;
$adminmenu[$i]['title'] = _MI_NWS_MENU;// comment this out if you like (can cause errors!)
//$adminmenu[$i]['link'] = '../../modules/system/admin.php?fct=blocksadmin&op=list&filter=1&selgen=' . $xoopsModule->getVar('mid') . '&selmod=-2&selgrp=-1&selvis=-1';
$adminmenu[$i]['link'] = '../../modules/system/admin.php?fct=blocksadmin&op=list';
$adminmenu[$i]["icon"] = $pathIcon32.'/block.png';
$i++;
$adminmenu[$i]["title"] = _MI_NWS_ADMENU2;
$adminmenu[$i]["link"] = 'admin/about.php';
$adminmenu[$i]["icon"] = $pathIcon32.'/about.png';

?>