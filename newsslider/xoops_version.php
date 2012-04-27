<?php
//  ------------------------------------------------------------------------ //
//                         NewsSlider Module for                             //
//               XOOPS - PHP Content Management System 2.0                   //
//                          Version 1.0.0                                    //
//                    Copyright (c) 2011 Xoops                               //
// ------------------------------------------------------------------------- //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
if( ! defined( 'XOOPS_ROOT_PATH' ) ) die( 'XOOPS root path not defined' ) ;

$modversion['name'] = _MI_NWS_NAME;
$modversion['version'] = 1.2;
$modversion['description'] = _MI_NWS_DESC;
$modversion['author'] = "Yerres";
$modversion['help'] = 'page=help';
$modversion['official'] = 0;
$modversion['image'] = "images/slogo.png";
$modversion['credits'] = "see readme";
$modversion['dirname'] = "newsslider";
$modversion['license'] = "GPL see LICENSE";

$modversion["license_file"] = XOOPS_URL."/modules/newsslider/gpl.txt";
$modversion['license_url'] = "www.gnu.org/licenses/gpl-2.0.html/"; 
$modversion['status_version'] = '1.2';
$modversion["module_status"] = "stable";
$modversion["release"] = "2012-02-13";
$modversion['release_date'] = '2012-02-13'; 
$modversion['last_update'] = '2012-02-13'; 
$modversion['min_php']='5.2';
$modversion['min_xoops']="2.5";
$modversion['min_admin']='1.1';
$modversion['min_db']= array('mysql'=>'5.0.7', 'mysqli'=>'5.0.7');

$modversion["author_word"] = "-";
$modversion["module_website_url"] = "www.myxoops.org";
$modversion["module_website_name"] = "XOOPS";

$modversion['dirmoduleadmin'] = '/Frameworks/moduleclasses/moduleadmin';
$modversion['icons16'] = '../../Frameworks/moduleclasses/icons/16';
$modversion['icons32'] = '../../Frameworks/moduleclasses/icons/32';
$modversion['system_menu'] = 1;

// Admin things
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = "admin/index.php";
$modversion['adminmenu'] = "admin/menu.php";

// Blocks
$modversion['blocks']	= array();
$modversion["blocks"][]	= array(
	"file"			=> "news_scrolling.php",
	"name"			=> _MI_NWS_BNAME1,
	"description"	=> "Shows scrolling News",
	"show_func"		=> "b_scrolling_news_show",
	"edit_func"		=> "b_scrolling_news_edit",
	"options"		=> "5|2||up|0|1|1|0|published|50|250|1|0|0|0|0|0",
	"template"		=> "news_scrolling.html",
	"can_clone"		=> true,
	);
$modversion["blocks"][]	= array(
	"file"			=> "news_glider.php",
	"name"			=> _MI_NWS_BNAME2,
	"description"	=> "Shows News Featured Content Glider",
	"show_func"		=> "b_news_glider_show",
	"edit_func"		=> "b_news_glider_edit",
	"options"		=> "5|5|downup|0|250|160||||230|1|0|1|published|1|35|175|1|0|0|0|0|0|0",
	"template"		=> "news_glider.html",
	"can_clone"		=> true,
	);
$modversion["blocks"][]	= array(
	"file"			=> "news_feature.php",
	"name"			=> _MI_NWS_BNAME3,
	"description"	=> "Shows News Featured Content Slider",
	"show_func"		=> "b_news_feature_show",
	"edit_func"		=> "b_news_feature_edit",
	"options"		=> "5|1|0|0|published|35|175|1|1|0|0|0|0|0",
	"template"		=> "news_feature.html",
	"can_clone"		=> true,
	);
$modversion["blocks"][]	= array(
	"file"			=> "news_s3slider.php",
	"name"			=> _MI_NWS_BNAME4,
	"description"	=> "Shows XOOPS S3 Slider",
	"show_func"		=> "b_news_s3slider_show",
	"edit_func"		=> "b_news_s3slider_edit",
	"options"		=> "5|3|0|0|published|bottom|50|175|1|1|0|0|0|0|0",
	"template"		=> "news_s3slider.html",
	"can_clone"		=> true,
	);
$modversion["blocks"][]	= array(
	"file"			=> "news_bxslider.php",
	"name"			=> _MI_NWS_BNAME5,
	"description"	=> "Shows bx Slider",
	"show_func"		=> "b_news_bxslider_show",
	"edit_func"		=> "b_news_bxslider_edit",
	"options"		=> "5|5|0|50|0|1|0|published|2|0|25|50|250|1|1|0|0|0|0|0|0|0|0",
	"template"		=> "news_bxslider.html",
	"can_clone"		=> true,
	);

// other 
$modversion['hasMain'] = 0;
$modversion['hasSearch'] = 0;
$modversion['hasComments'] = 0;
$modversion['hasNotification'] = 0;


?>