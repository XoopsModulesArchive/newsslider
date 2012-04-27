<?php
/** news_bxslider.php v.1
  * XOOPS - PHP Content Management System
  * Copyright (c) 2011 <http://www.xoops.org/>
  *
  * Module: newsslider 1.2
  * Author: Yerres
  * Licence : GPL
  * 
*/
if( ! defined( 'XOOPS_ROOT_PATH' ) ) die( 'XOOPS root path not defined' ) ;

function b_news_bxslider_show( $options ) {
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
    
    $block['speed'] = isset($options[1]) && $options[1] != '' ?  $options[1] : '5';
    $block['float'] = isset($options[2]) && $options[2] != '' ?  $options[2] : '0';
    $block['imgwidth'] = isset($options[3]) && $options[3] != '' ?  $options[3] : '50';
    $block['textalign'] = isset($options[4]) && $options[4] != '' ?  $options[4] : '';
    $block['loop'] = ($options[5]==1)? 1:0;//
    $block['captions'] = ($options[6]==1)? 1:0;
    $block['sort'] = $options[7];
    $block['mode'] = isset($options[8]) && $options[8] != '' ?  $options[8] : '2';
    $block['ticker'] = isset($options[8]) && $options[8] == '3' ?  1 : 0;
    $block['easing'] = isset($options[9]) && $options[9] != '0' ?  'easeOutBounce' : 0;
    $block['tickerspeed'] = isset($options[10]) && $options[10] != '' ?  $options[10] : '25';    
    $block['topictitle'] = ($options[13]==1)? 1:0;
    $block['controls'] = ($options[14]==1)? 1:0;
    $block['includedate'] = ($options[15]==1)? 1:0;
    $block['author'] = ($options[16]==1)? 1:0;
    $block['jquery'] = ($options[17]==1)? 1:0;
    
    $tmpstory = new NewsStory;
    // for compatibility with old News versions
    if ($module->getVar('version') >= 150) {
      $restricted = news_getmoduleoption('restrictindex');
      $dateformat = news_getmoduleoption('dateformat');
      $infotips = news_getmoduleoption('infotips');
      //if($dateformat == '') $dateformat = 'M d, Y g:i:s A';
      if($dateformat == '') $dateformat = 'd. M Y';
    } else {
      $restricted = isset($newsConfig['restrictindex']) && $newsConfig['restrictindex'] == 1 ?  1: 0;
      $dateformat = isset($newsConfig['dateformat']) && $newsConfig['dateformat'] != '' ?  $newsConfig['dateformat']: 'M d, Y g:i:s A';
      $infotips  = '0';
    }

    if ($options[23] == 0) {
        $stories = $tmpstory->getAllPublished($options[0],0,$restricted,0,1,true, $options[7]);
    } else {
        $topics = array_slice($options, 23);
        $stories = $tmpstory->getAllPublished($options[0],0,$restricted,0,1,true, $options[7]);
    }
    unset($tmpstory);
      if(count($stories)==0)  return '';
      $i=0;
      foreach ( $stories as $story ) {
        $news = array();

        $title = $story->title();
        if (strlen($title) > $options[11])
          $title = xoops_substr($title,0,$options[11]+3);
        $news['title'] = $title;
        $news['id'] = $story->storyid();
        $news['date'] = formatTimestamp($story->published(), $dateformat);
        $news['no'] = $i++;
        $news['author']= sprintf("%s %s",_POSTEDBY,$story->uname());
        $news['topic_title'] = $story->topic_title();

        if (file_exists(XOOPS_ROOT_PATH . '/modules/newsslider/images/image'.$i.'.jpg')) {
          $news['picture'] = 'image'.$i.'.jpg';
        } else {
          $news['picture'] = 'image1.jpg';
        } 
        
        if ($options[12] > 0) {
          //$html = $story->nohtml() == 1 ? 0 : 1;
          $html = $options[18] == 1 ? 0 : 1;//
          $smiley = $options[19] == 1 ? 0 : 1;
          $xcode = $options[20] == 1 ? 0 : 1;
          $image = $options[21] == 1 ? 0 : 1;
          $br = $options[22] == 1 ? 0 : 1;
          //--- for News versions prior to 1.60
          if ($module->getVar('version') <= 160) {
            $news['teaser'] = xoops_substr($myts->displayTarea(strip_tags($story->hometext)), 0, $options[12]+3);
          } else {
            $news['teaser'] = news_truncate_tagsafe(strip_tags($myts->displayTarea($story->hometext, $html, $smiley, $xcode, $image, $br )), $options[12]+3);
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
    $xoTheme -> addStylesheet( 'modules/newsslider/bx_styles.css' );
    
    return $block;
}

//----
function b_news_bxslider_edit( $options ){
	global $xoopsDB;
	$myts = & MyTextSanitizer :: getInstance();
	$form  = "<table width='100%' border='0'  class='bg2'>";
	$form .= "<tr><th width='50%'>"._OPTIONS."</th><th width='50%'>"._MB_NWS_SETTINGS."</th></tr>";
	$form .= "<tr><td class='even'>"._MB_NWS_BLIMIT."</td><td class='odd'><input type='text' name='options[0]' size='16' maxlength=3 value='".$options[0]."' /></td></tr>";
	$form .= "<tr><td class='even'>"._MB_NWS_BPACE."</td><td class='odd'><input type='text' name='options[1]' size='16' maxlength=2 value='".$options[1]."' /></td></tr>";
  //---  
	$form .= "<tr><td class='even'>"._MB_NWS_FLOATIMG."</td><td class='odd'><select name='options[2]'>";
	$form .= "<option value='0' ".(($options[2]=='0')?" selected='selected'":"").">"._NO."</option>\n";
	$form .= "<option value='1' ".(($options[2]=='1')?" selected='selected'":"").">"._LEFT."</option>\n";
	$form .= "<option value='2' ".(($options[2]=='2')?" selected='selected'":"").">"._RIGHT."</option>\n";
	$form .= "</select></td></tr>\n";
  //---
  $form .= "<tr><td class='even'>"._MB_NWS_IMGWIDTH."</td><td class='odd'><input type='text' name='options[3]' size='16' maxlength=3 value='".$options[3]."' /></td></tr>";
  //---
  $form .= "<tr><td class='even'>"._MB_NWS_TEXTALIGN."</td><td class='odd'><select name='options[4]'>";
  $form .= "<option value='0' ".(($options[4]=='0')?" selected='selected'":"").">"._NONE."</option>\n";
  $form .= "<option value='1' ".(($options[4]=='1')?" selected='selected'":"").">"._LEFT."</option>\n";
  $form .= "<option value='2' ".(($options[4]=='2')?" selected='selected'":"").">"._RIGHT."</option>\n";
  $form .= "<option value='3' ".(($options[4]=='3')?" selected='selected'":"").">"._MB_NWS_JUSTIFY."</option>\n";
  $form .= "</select></td></tr>\n";
  //---
	$form .= "<tr><td class='even'>"._MB_NWS_AUTOROTATE."</td><td class='odd'>";
  $form .= "<input type='radio' name='options[5]' value='1'".(($options[5]==1)?" checked='checked'":"")." />"._YES."&nbsp;";
	$form .= "<input type='radio' name='options[5]' value='0'".(($options[5]==0)?" checked='checked'":"")." />"._NO."<br /></td></tr>";
  //---
	$form .= "<tr><td class='even'>"._MB_NWS_CAPTIONS."</td><td class='odd'>";
	$form .= "<input type='radio' name='options[6]' value='1'".(($options[6]==1)?" checked='checked'":"")." />"._YES."&nbsp;";
	$form .= "<input type='radio' name='options[6]' value='0'".(($options[6]==0)?" checked='checked'":"")." />"._NO."<br /></td></tr>";
  //---  
	$form .= "<tr><td class='even'>"._MB_NWS_SORT."</td><td class='odd'><select name='options[7]'>";
	$form .= "<option value='topicid' ".(($options[7]=='topicid')?" selected='selected'":"").">"._MB_NWS_TOPIC."</option>\n";
	$form .= "<option value='published' ".(($options[7]=='published')?" selected='selected'":"").">"._MB_NWS_DATE."</option>\n";
	$form .= "<option value='counter' ".(($options[7]=='counter')?" selected='selected'":"").">"._MB_NWS_HITS."</option>\n";
	$form .= "<option value='title' ".(($options[7]=='title')?" selected='selected'":"").">"._MB_NWS_NAME."</option>\n";
	$form .= "</select></td></tr>\n";
  //---
  $form .= "<tr><td class='even'>"._MB_NWS_EFFECT."</td><td class='odd'><select name='options[8]'>";
  $form .= "<option value='0' ".(($options[8]=='0')?" selected='selected'":"").">"._MB_NWS_HORIZONTAL."</option>\n";
  $form .= "<option value='1' ".(($options[8]=='1')?" selected='selected'":"").">"._MB_NWS_VERTICAL."</option>\n";
  $form .= "<option value='2' ".(($options[8]=='2')?" selected='selected'":"").">"._MB_NWS_FADE."</option>\n";
  $form .= "<option value='3' ".(($options[8]=='3')?" selected='selected'":"").">"._MB_NWS_TICKER."</option>\n";
  $form .= "</select></td></tr>\n";
  //---
	$form .= "<tr><td class='even'>"._MB_NWS_EASING."</td><td class='odd'>";
	$form .= "<input type='radio' name='options[9]' value='1'".(($options[9]==1)?" checked='checked'":"")." />"._YES."&nbsp;";
	$form .= "<input type='radio' name='options[9]' value='0'".(($options[9]==0)?" checked='checked'":"")." />"._NO."<br /></td></tr>";
  //---
	$form .= "<tr><td class='even'>"._MB_NWS_TICKERSPEED."</td><td class='odd'><input type='text' name='options[10]' size='4' value='".$options[10]."' /></td></tr>";
  //---
  $form .= "<tr><td class='even'>"._MB_NWS_CHARS."</td><td class='odd'><input type='text' name='options[11]' value='".$options[11]."'/></td></tr>";
  $form .= "<tr><td class='even'>"._MB_NWS_TEASER." </td><td class='odd'><input type='text' name='options[12]' value='".$options[12]."' /></td></tr>";
  //---
	$form .= "<tr><td class='even'>"._MB_NWS_TOPICT."</td><td class='odd'>";
	$form .= "<input type='radio' name='options[13]' value='1'".(($options[13]==1)?" checked='checked'":"")." />"._YES."&nbsp;";
	$form .= "<input type='radio' name='options[13]' value='0'".(($options[13]==0)?" checked='checked'":"")." />"._NO."<br /></td></tr>";
  //---
	$form .= "<tr><td class='even'>"._MB_NWS_BUTTONSNAV."</td><td class='odd'>";
	$form .= "<input type='radio' name='options[14]' value='1'".(($options[14]==1)?" checked='checked'":"")." />"._YES."&nbsp;";
	$form .= "<input type='radio' name='options[14]' value='0'".(($options[14]==0)?" checked='checked'":"")." />"._NO."<br /></td></tr>";
	//---
	$form .= "<tr><td class='even'>"._MB_NWS_SHOWDATE."</td><td class='odd'>";
	$form .= "<input type='radio' name='options[15]' value='1'".(($options[15]==1)?" checked='checked'":"")." />"._YES."&nbsp;";
	$form .= "<input type='radio' name='options[15]' value='0'".(($options[15]==0)?" checked='checked'":"")." />"._NO."<br /></td></tr>";
	//---
	$form .= "<tr><td class='even'>"._MB_NWS_SHOWAUTH."</td><td class='odd'>";
	$form .= "<input type='radio' name='options[16]' value='1'".(($options[16]==1)?" checked='checked'":"")." />"._YES."&nbsp;";
	$form .= "<input type='radio' name='options[16]' value='0'".(($options[16]==0)?" checked='checked'":"")." />"._NO."<br /></td></tr>";
  //---
	$form .= "<tr><td class='even'>"._MB_NWS_JQUERY."</td><td class='odd'>";
	$form .= "<input type='radio' name='options[17]' value='1'".(($options[17]==1)?" checked='checked'":"")." />"._YES."&nbsp;";
	$form .= "<input type='radio' name='options[17]' value='0'".(($options[17]==0)?" checked='checked'":"")." />"._NO."<br /></td></tr>";
	//---
	$form .= "<tr><td class='even'>&nbsp; </td> <td class='odd'>&nbsp;</td></tr>";
  //--- 
	$form .= "<tr><td class='even'>"._MB_NWS_HTML."</td><td class='odd'>";
	$form .= "<input type='radio' name='options[18]' value='1'".(($options[18]==1)?" checked='checked'":"")." />"._YES."&nbsp;";
	$form .= "<input type='radio' name='options[18]' value='0'".(($options[18]==0)?" checked='checked'":"")." />"._NO."<br /></td></tr>";
  //---  
	$form .= "<tr><td class='even'>"._MB_NWS_SMILEY."</td><td class='odd'>";
	$form .= "<input type='radio' name='options[19]' value='1'".(($options[19]==1)?" checked='checked'":"")." />"._YES."&nbsp;";
	$form .= "<input type='radio' name='options[19]' value='0'".(($options[19]==0)?" checked='checked'":"")." />"._NO."<br /></td></tr>";
  //---  
	$form .= "<tr><td class='even'>"._MB_NWS_XCODE."</td><td class='odd'>";
	$form .= "<input type='radio' name='options[20]' value='1'".(($options[20]==1)?" checked='checked'":"")." />"._YES."&nbsp;";
	$form .= "<input type='radio' name='options[20]' value='0'".(($options[20]==0)?" checked='checked'":"")." />"._NO."<br /></td></tr>";
	//---
	$form .= "<tr><td class='even'>"._MB_NWS_IMAGE."</td><td class='odd'>";
	$form .= "<input type='radio' name='options[21]' value='1'".(($options[21]==1)?" checked='checked'":"")." />"._YES."&nbsp;";
	$form .= "<input type='radio' name='options[21]' value='0'".(($options[21]==0)?" checked='checked'":"")." />"._NO."<br /></td></tr>";
	//---
  $form .= "<tr><td class='even'>"._MB_NWS_BR."</td><td class='odd'>";	
	$form .= "<input type='radio' name='options[22]' value='1'".(($options[22]==1)?" checked='checked'":"")." />"._YES."&nbsp;";
	$form .= "<input type='radio' name='options[22]' value='0'".(($options[22]==0)?" checked='checked'":"")." />"._NO."<br /></td></tr>";
  //--- get allowed topics
  $form .= "<tr><td class='even'>"._MB_NWS_TOPICS."</td><td class='odd'><select id=\"options[23]\" name=\"options[]\" multiple=\"multiple\">";
  $module_handler = xoops_gethandler("module");
  $newsModule = $module_handler->getByDirname("news");
  if (is_object($newsModule)) {
    $isAll = empty($options[23]) ? true : false;
    $options_tops = array_slice($options, 23);
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