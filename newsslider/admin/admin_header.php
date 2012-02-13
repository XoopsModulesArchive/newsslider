<?php
/**
 * $Id: admin_header.php v 1.0 21 Jan 2012 Yerres Exp $
 * Module: newsslider
 * Version: 1.1
 * Author: yerres
 * Licence: GNU
 */
  
include("../../../mainfile.php");
include '../../../include/cp_header.php';
global $xoopsModule,$xoopsConfig;


include_once XOOPS_ROOT_PATH."/modules/".$xoopsModule->dirname()."/admin/functions.php";

$myts =& MyTextSanitizer::getInstance();

if ( is_object( $xoopsUser)  ) {
    $xoopsModule = XoopsModule::getByDirname("newsslider");
    if ( !$xoopsUser->isAdmin($xoopsModule->mid()) ) {
        redirect_header(XOOPS_URL."/",1,_NOPERM);
        exit();
    }
} else {
    redirect_header(XOOPS_URL."/",1,_NOPERM);
    exit();
}

?>