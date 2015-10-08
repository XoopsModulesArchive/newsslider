<?php
/** news_feature.php v.1
  * XOOPS - PHP Content Management System
  * Copyright (c) 2011 <http://www.xoops.org/>
  *
  * Module: newsslider 1.2
  * Author: Yerres
  * Licence : GPL
  * 
*/
if( ! defined( 'XOOPS_ROOT_PATH' ) ) die( 'XOOPS root path not defined' ) ;


function b_news_feature_show( $options ) {
    global $xoopsDB, $xoopsUser, $xoTheme;
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
    
    $block['speed'] = isset($options[0]) && $options[0] != '' ?  $options[0] : '5';
    $block['includedate'] = ($options[1]==1)? 1:0;
    $block['author'] = ($options[2]==1)? 1:0;
    $block['topic'] = ($options[3]==1)? 1:0;
    $block['sort'] = $options[4];
    $block['jquery'] = ($options[7]==1)? 1:0;
   
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
       $stories = $tmpstory->getAllPublished(4, 0, $restricted, 0, true, true, $options[4]);
    } else {
        $topics = array_slice($options, 13);
        $stories = $tmpstory->getAllPublished(4, 0, $restricted, $topics, true,true, $options[4]);
    }

    unset($tmpstory);
      if(count($stories)==0)  return '';
      $i=0;
      
      foreach ( $stories as $story ) {
        $news = array();

        $title = $story->title();
        if (strlen($title) > $options[5])
          $title = xoops_substr($title,0,$options[5]+3);
        $news['title'] = $title;
        $news['id'] = $story->storyid();
        $news['date'] = formatTimestamp($story->published(), $dateformat);
        $news['no'] = $i++;
        $news['author']= sprintf("%s %s",_POSTEDBY,$story->uname());
        $news_topic_title = $story->topic_title();
        $news['topic_title'] = $myts -> htmlSpecialChars( xoops_substr ( $news_topic_title, 0, 35-1 ));
        //$news['topic_title'] = $myts -> htmlSpecialChars( xoops_substr ( $news_topic_title, 0, $options[5]-1 ));
        if (file_exists(XOOPS_ROOT_PATH . '/modules/newsslider/images/image'.$i.'.jpg')) {
          $news['picture'] = 'image'.$i.'.jpg';
        } else {
          $news['picture'] = 'image1.jpg';
        }        
        if (file_exists(XOOPS_ROOT_PATH . '/modules/newsslider/images/image'.$i.'-small.jpg')) {
          $news['thumb'] = 'image'.$i.'-small.jpg';
        } else {
          $news['picture'] = 'image1-small.jpg';
        }
 
        if ($options[6] > 0) {
          //$html = $story->nohtml() == 1 ? 0 : 1;
          $html = $options[8] == 1 ? 0 : 1;//
          $smiley = $options[9] == 1 ? 0 : 1;
          $xcode = $options[10] == 1 ? 0 : 1;
          $image = $options[11] == 1 ? 0 : 1;
          $br = $options[12] == 1 ? 0 : 1;
          //--- for News versions prior to 1.60
          if ($module->getVar('version') <= 160) {
            $news['teaser'] = xoops_substr($myts->displayTarea(strip_tags($story->hometext)), 0, $options[6]+3);
          } else {
            $news['teaser'] = news_truncate_tagsafe(strip_tags($myts->displayTarea($story->hometext, $html, $smiley, $xcode, $image, $br )), $options[6]+3);

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
    $xoTheme -> addStylesheet( 'modules/newsslider/style.css' );
 
    return $block;
}

//----
function b_news_feature_edit( $options ){
	global $xoopsDB;
	$myts = & MyTextSanitizer :: getInstance();
	$form  = "<table width='100%' border='0'  class='bg2'>";
	$form .= "<tr><th width='50%'>"._OPTIONS."</th><th width='50%'>"._MB_NWS_SETTINGS."</th></tr>";
	$form .= "<tr><td class='even'>"._MB_NWS_BPACE."</td><td class='odd'><input type='text' name='options[0]' size='16' maxlength=2 value='".$options[0]."' /></td></tr>";
	//---
	$form .= "<tr><td class='even'>"._MB_NWS_SHOWDATE."</td><td class='odd'>";
	$form .= "<input type='radio' name='options[1]' value='1'".(($options[1]==1)?" checked='checked'":"")." />"._YES."&nbsp;";
	$form .= "<input type='radio' name='options[1]' value='0'".(($options[1]==0)?" checked='checked'":"")." />"._NO."<br /></td></tr>";
	//---
	$form .= "<tr><td class='even'>"._MB_NWS_SHOWAUTH."</td><td class='odd'>";
	$form .= "<input type='radio' name='options[2]' value='1'".(($options[2]==1)?" checked='checked'":"")." />"._YES."&nbsp;";
	$form .= "<input type='radio' name='options[2]' value='0'".(($options[2]==0)?" checked='checked'":"")." />"._NO."<br /></td></tr>";
	//---
	$form .= "<tr><td class='even'>"._MB_NWS_SHOWTOPIC."</td><td class='odd'>";
	$form .= "<input type='radio' name='options[3]' value='1'".(($options[3]==1)?" checked='checked'":"")." />"._YES."&nbsp;";
	$form .= "<input type='radio' name='options[3]' value='0'".(($options[3]==0)?" checked='checked'":"")." />"._NO."<br /></td></tr>";
  //---  
	$form .= "<tr><td class='even'>"._MB_NWS_SORT."</td><td class='odd'><select name='options[4]'>";
	$form .= "<option value='topicid' ".(($options[4]=='topicid')?" selected='selected'":"").">"._MB_NWS_TOPIC."</option>\n";
	$form .= "<option value='published' ".(($options[4]=='published')?" selected='selected'":"").">"._MB_NWS_DATE."</option>\n";
	$form .= "<option value='counter' ".(($options[4]=='counter')?" selected='selected'":"").">"._MB_NWS_HITS."</option>\n";
	$form .= "<option value='title' ".(($options[4]=='title')?" selected='selected'":"").">"._MB_NWS_NAME."</option>\n";
	$form .= "</select></td></tr>\n";
  //---
  $form .= "<tr><td class='even'>"._MB_NWS_CHARS."</td><td class='odd'><input type='text' name='options[5]' value='".$options[5]."'/></td></tr>";
  $form .= "<tr><td class='even'>"._MB_NWS_TEASER." </td><td class='odd'><input type='text' name='options[6]' value='".$options[6]."' /></td></tr>";
  //---
	$form .= "<tr><td class='even'>"._MB_NWS_JQUERY."</td><td class='odd'>";
	$form .= "<input type='radio' name='options[7]' value='1'".(($options[7]==1)?" checked='checked'":"")." />"._YES."&nbsp;";
	$form .= "<input type='radio' name='options[7]' value='0'".(($options[7]==0)?" checked='checked'":"")." />"._NO."<br /></td></tr>";  //---
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
	$form .= "<tr><td class='even'>"._MB_NWS_IMAGE."</td><td class='odd'>";
	$form .= "<input type='radio' name='options[11]' value='1'".(($options[11]==1)?" checked='checked'":"")." />"._YES."&nbsp;";
	$form .= "<input type='radio' name='options[11]' value='0'".(($options[11]==0)?" checked='checked'":"")." />"._NO."<br /></td></tr>";
	//---
	$form .= "<tr><td class='even'>"._MB_NWS_BR."</td><td class='odd'>";
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