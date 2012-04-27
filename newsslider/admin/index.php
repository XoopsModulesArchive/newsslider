<?php
/**
 * $Id: index.php v 1.0 21 Jan 2012 Yerres Exp $
 * Module: newsslider
 * Version: v 1.00
 * Licence: GNU
 */

include_once "admin_header.php";

xoops_cp_header();

$indexAdmin = new ModuleAdmin();
echo $indexAdmin->addNavigation('index.php');
echo $indexAdmin->renderIndex();


echo "<P>"._AM_NWS_INTRO."</P><br/>";


include "admin_footer.php";
?>