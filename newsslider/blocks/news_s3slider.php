<?php
/** news_s3slider.php v.1
  * XOOPS - PHP Content Management System
  * Copyright (c) 2011 <http://www.xoops.org/>
  *
  * Module: newsslider 1.2
  * Author: Yerres
  * Licence : GPL
  * 
*/
if( ! defined( 'XOOPS_ROOT_PATH' ) ) die( 'XOOPS root path not defined' ) ;


function b_news_s3slider_show( $options ) {
    global $xoopsDB, $xoopsUser;
    $myts = & MyTextSanitizer :: getInstance();

    $block = array();
    $module_handler = &xoops_gethandler('module');
    $module = &$module_handler->getByDirname('news');
    if (!isset($newsConfig)) {
        $config_handler = &xoops_gethandler('config');
        $newsConfig = &$config_handler->getConfigsByCat(0, $module->getVar('mid'));
    }
    if (!is_object($module)) return $block;
    include_once XOOPS_ROOT_PATH.'/modules/news/class/class.newsstory.php';
    include_once XOOPS_ROOT_PATH.'/modules/news/include/functions.php';
    
    $block['speed'] = isset($options[1]) && $options[1] != '' ?  $options[1] : '5';
    $block['includedate'] = ($options[2]==1)? 1:0;
    $block['author'] = ($options[3]==1)? 1:0;
    $block['sort'] = $options[4];
   
    $tmpstory = new NewsStory;
    // for compatibility with old News versions
    if ($module->getVar('version') >= 150) {
      $restricted = news_getmoduleoption('restrictindex');
      $dateformat = news_getmoduleoption('dateformat');
      $infotips = news_getmoduleoption('infotips');
      //if($dateformat == '') $dateformat = 'M d, Y g:i A';// Int.date
      if($dateformat == '') $dateformat = 'd. M Y';
    } else {
      $restricted = isset($newsConfig['restrictindex']) && $newsConfig['restrictindex'] == 1 ?  1: 0;
      $dateformat = isset($newsConfig['dateformat']) && $newsConfig['dateformat'] != '' ?  $newsConfig['dateformat']: 'd. M. Y G:i';
      $infotips  = '0';
    }

    if ($options[13] == 0) {
        $stories = $tmpstory->getAllPublished($options[0],0,$restricted,0,1, $options[4]);
    } else {
        $topics = array_slice($options, 13);
        $stories = $tmpstory->getAllPublished($options[0],0,$restricted,0,1, $options[4]);
    }

    unset($tmpstory);
      if(count($stories)==0)  return '';
      $i=0;
      
      foreach ( $stories as $story ) {
        $news = array();

        $title = $story->title();
        if (strlen($title) > $options[6])
          $title = xoops_substr($title,0,$options[6]+3);
        $news['title'] = $title;
        $news['id'] = $story->storyid();
        $news['date'] = formatTimestamp($story->published(), $dateformat);
        $news['class'] = $options[5];
        $news['no'] = $i++;
        //$news['author'] = $story->uname();
        $news['author']= sprintf("%s %s",_POSTEDBY,$story->uname());
        $news['topic_title'] = $story->topic_title();
        $news['picture'] = 'image'.$i.'.jpg';
 
        if ($options[7] > 0) {
          $html = $story->nohtml() == 1 ? 0 : 1;
          //$html = $options[8] == 1 ? 0 : 1;//
          $smiley = $options[9] == 1 ? 0 : 1;
          $xcode = $options[10] == 1 ? 0 : 1;
          $image = $options[11] == 1 ? 0 : 1;
          $br = $options[12] == 1 ? 0 : 1;
          //--- for News versions prior to 1.60
          if ($module->getVar('version') <= 160) {
            $news['teaser'] = xoops_substr($myts->displayTarea(strip_tags($story->hometext)), 0, $options[7]+3);
          } else {
            //--- for News version 1.60+
            //$news['teaser'] = news_truncate_tagsafe($myts->displayTarea($story->hometext, $html), $options[7]+3);
            $news['teaser'] = news_truncate_tagsafe(strip_tags($myts->displayTarea($story->hometext, $html, $smiley, $xcode, $image, $br )), $options[7]+3);
          }
          if($infotips>0) {
            $news['infotips'] = ' title="'.news_make_infotips($story->hometext()).'"';
          } else {
            $news['infotips'] = '';
          }
        } else {
          $news['teaser'] = '';
          if($infotips>0) {
            $news['infotips'] = ' title="'.news_make_infotips($story->hometext()).'"';
          } else {
            $news['infotips'] = '';
          }
        }
        $block['stories'][] = $news;
    }
    $block['lang_read_more']= _MB_NWS_READMORE;
    global $xoTheme; 
    $xoTheme -> addStylesheet( 'modules/newsslider/s3_style.css' );
    /*$jquery = ($options[8]==1) ? 1:0;
    if ( $jquery ) {
      if (file_exists(XOOPS_ROOT_PATH . '/modules/newsslider/js/jquery.min.js')) {
      if(isset($xoTheme) && is_object($xoTheme)) {
        $xoTheme -> addScript('/modules/newsslider/js/jquery.min.js', array( 'type' => 'text/javascript' ) );
        $xoTheme -> addScript('/modules/newsslider/js/s3Slider.js', array( 'type' => 'text/javascript' ) );
      }}
    }*/    
    return $block;
}

//----
function b_news_s3slider_edit( $options ){
	global $xoopsDB;
	$myts = & MyTextSanitizer :: getInstance();
	$form  = "<table width='100%' border='0'  class='bg2'>";
	$form .= "<tr><th width='50%'>"._OPTIONS."</th><th width='50%'>"._MB_NWS_SETTINGS."</th></tr>";
	$form .= "<tr><td class='even'>"._MB_NWS_BLIMIT."</td><td class='odd'><input type='text' name='options[0]' size='16' maxlength=3 value='".$options[0]."' /></td></tr>";
	//$form .= "<tr><td class='even'>"._MB_NWS_STORIES."</td><td class='odd'><input type='text' name='options[]' value='" . $options[1] . "' size='20'></td></tr>";
	$form .= "<tr><td class='even'>"._MB_NWS_BPACE."</td><td class='odd'><input type='text' name='options[1]' size='16' maxlength=2 value='".$options[1]."' /></td></tr>";
	//---
	$form .= "<tr><td class='even'>"._MB_NWS_SHOWDATE."</td><td class='odd'>";
	$form .= "<input type='radio' name='options[2]' value='1'".(($options[2]==1)?" checked='checked'":"")." />"._YES."&nbsp;";
	$form .= "<input type='radio' name='options[2]' value='0'".(($options[2]==0)?" checked='checked'":"")." />"._NO."<br /></td></tr>";
	//---
	$form .= "<tr><td class='even'>"._MB_NWS_SHOWAUTH."</td><td class='odd'>";
	$form .= "<input type='radio' name='options[3]' value='1'".(($options[3]==1)?" checked='checked'":"")." />"._YES."&nbsp;";
	$form .= "<input type='radio' name='options[3]' value='0'".(($options[3]==0)?" checked='checked'":"")." />"._NO."<br /></td></tr>";
  //---  
	$form .= "<tr><td class='even'>"._MB_NWS_SORT."</td><td class='odd'><select name='options[4]'>";
	$form .= "<option value='RAND()' ".(($options[4]=='RAND()')?" selected='selected'":"").">"._MB_NWS_RANDOM."</option>\n";
	$form .= "<option value='published' ".(($options[4]=='published')?" selected='selected'":"").">"._MB_NWS_DATE."</option>\n";
	$form .= "<option value='counter' ".(($options[4]=='counter')?" selected='selected'":"").">"._MB_NWS_HITS."</option>\n";
	$form .= "<option value='title' ".(($options[4]=='title')?" selected='selected'":"").">"._MB_NWS_NAME."</option>\n";
	$form .= "</select></td></tr>\n";
  //---
  $form .= "<tr><td class='even'>"._MB_NWS_SLIDESIDE."</td><td class='odd'><select name='options[5]'>";
  $form .= "<option value='top' ".(($options[5]=='top')?" selected='selected'":"").">"._MB_NWS_SLIDETOP."</option>\n";
  $form .= "<option value='bottom' ".(($options[5]=='bottom')?" selected='selected'":"").">"._MB_NWS_SLIDEBOTTOM."</option>\n";
  $form .= "<option value='left' ".(($options[5]=='left')?" selected='selected'":"").">"._MB_NWS_SLIDELEFT."</option>\n";
  $form .= "<option value='right' ".(($options[5]=='right')?" selected='selected'":"").">"._MB_NWS_SLIDERIGHT."</option>\n";
  $form .= "</select></td></tr>\n";
  //---
  $form .= "<tr><td class='even'>"._MB_NWS_CHARS."</td><td class='odd'><input type='text' name='options[6]' value='".$options[6]."'/></td></tr>";
  $form .= "<tr><td class='even'>"._MB_NWS_TEASER." </td><td class='odd'><input type='text' name='options[7]' value='".$options[7]."' /></td></tr>";
	//---
	$form .= "<tr><td class='even'>&nbsp; </td> <td class='odd'>&nbsp;</td></tr>";
  //--- 
	$form .= "<tr><td class='even'>"._MB_NWS_HTML."</td><td class='odd'>";
	$form .= "<input type='radio' name='options[8]' value='1'".(($options[8]==1)?" checked='checked'":"")." />"._YES."&nbsp;";
	$form .= "<input type='radio' name='options[8]' value='0'".(($options[8]==0)?" checked='checked'":"")." />"._NO."<br /></td></tr>";
  //---  
	$form .= "<tr><td class='even'>"._MB_NWS_SMILEY."</td><td class='odd'>";
	$form .= "<input type='radio' name='options[9]' value='1'".(($options[9]==1)?" checked='checked'":"")." />"._YES."&nbsp;";
	$form .= "<input type='radio' name='options[9]' value='0'".(($options[9]==0)?" checked='checked'":"")." />"._NO."<br /></td></tr>";
  //---  
	$form .= "<tr><td class='even'>"._MB_NWS_XCODE."</td><td class='odd'>";
	$form .= "<input type='radio' name='options[10]' value='1'".(($options[10]==1)?" checked='checked'":"")." />"._YES."&nbsp;";
	$form .= "<input type='radio' name='options[10]' value='0'".(($options[10]==0)?" checked='checked'":"")." />"._NO."<br /></td></tr>";
	//---
	$form .= "<tr><td class='even'>"._MB_NWS_BR."</td><td class='odd'>";
	$form .= "<input type='radio' name='options[11]' value='1'".(($options[11]==1)?" checked='checked'":"")." />"._YES."&nbsp;";
	$form .= "<input type='radio' name='options[11]' value='0'".(($options[11]==0)?" checked='checked'":"")." />"._NO."<br /></td></tr>";
	//---
	$form .= "<tr><td class='even'>"._MB_NWS_IMAGE."</td><td class='odd'>";
	$form .= "<input type='radio' name='options[12]' value='1'".(($options[12]==1)?" checked='checked'":"")." />"._YES."&nbsp;";
	$form .= "<input type='radio' name='options[12]' value='0'".(($options[12]==0)?" checked='checked'":"")." />"._NO."<br /></td></tr>";
  //--- get topics
  $form .= "<tr><td class='even'>"._MB_NWS_TOPICS."</td><td class='odd'><select id=\"options[13]\" name=\"options[]\" multiple=\"multiple\">";
  $module_handler = xoops_gethandler("module");
  $newsModule = $module_handler->getByDirname("news");
  if (is_object($newsModule)) {
    $isAll = empty($options[13]) ? true : false;
    $options_tops = array_slice($options, 13);
    include_once XOOPS_ROOT_PATH."/class/xoopsstory.php";
    $xt = new XoopsTopic($xoopsDB->prefix("topics"));
    $alltopics = $xt->getTopicsList();
    ksort($alltopics);
    $form .= "<option value=\"0\" ";
    if ($isAll) $form .= " selected=\"selected\"";
    $form .= ">"._ALL."</option>";
    foreach ($alltopics as $topicid => $topic) {
      $sel = ( $isAll || in_array($topicid, $options_tops) ) ? " selected" : "";
      $form .= "<option value=\"$topicid\" $sel>".$topic["title"]."</option>";
      }
  }
  $form .= '</select></td></tr><br />';
	$form .= "</table>";

	//-------
	
	return $form;
}
?>