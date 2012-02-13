<?php
/** news_glider.php v.1
  * XOOPS - PHP Content Management System
  * Copyright (c) 2011 <http://www.xoops.org/>
  *
  * Module: newsslider 1.1
  * Author: Yerres
  * Licence : GPL
  * 
*/
if( ! defined( 'XOOPS_ROOT_PATH' ) ) die( 'XOOPS root path not defined' ) ;

function b_news_glider_show( $options ) {
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
    $block['direction'] = $options[2];
    $block['persiststate'] = ($options[3]==1)? 1:0;
    $block['width'] = isset($options[4]) && $options[4] != '' ?  $options[4] : '350';
    $block['height'] = isset($options[5]) && $options[5] != '' ?  $options[5] : '160';
    $block['bgcolor'] = isset($options[6]) && $options[6] != '' ?  $options[6] : '#FFFFFF';
    $block['border'] = isset($options[7]) && $options[7] != '' ?  $options[7] : '0';
    $block['bordercolor'] = isset($options[8]) && $options[8] != '' ?  $options[8] : '#CFCFCF';
    $block['wrapperwidth'] = isset($options[9]) && $options[9] != '' ?  $options[9] : '330';
    $block['autorotate'] = ($options[10]==1)? 1:0;
    $block['acycles'] = isset($options[11]) && $options[11] != '' ?  $options[11] : '0';
    $block['includedate'] = ($options[12]==1)? 1:0;
    $uniqueid = substr(md5(uniqid(rand())),25);
    $block['divid'] = $uniqueid;
    $block['navi'] = ($options[17]==1)? 1:0;
   
    $block['sort'] = $options[13];
    $tmpstory = new NewsStory;
    // for compatibility with old News versions
    if ($module->getVar('version') >= 150) {
      $restricted = news_getmoduleoption('restrictindex');
      $dateformat = news_getmoduleoption('dateformat');
      $infotips = news_getmoduleoption('infotips');
      if($dateformat == '') $dateformat = 'M d, Y g:i:s A';
    } else {
      $restricted = isset($newsConfig['restrictindex']) && $newsConfig['restrictindex'] == 1 ?  1: 0;
      $dateformat = isset($newsConfig['dateformat']) && $newsConfig['dateformat'] != '' ?  $newsConfig['dateformat']: 'M d, Y g:i:s A';
      $infotips  = '0';
    }

    if ($options[23] == 0) {
        $stories = $tmpstory->getAllPublished($options[0],0,$restricted,0,1, $options[13]);
    } else {
        $topics = array_slice($options, 23);
        $stories = $tmpstory->getAllPublished($options[0],0,$restricted,0,1, $options[13]);
    }
    unset($tmpstory);
      if(count($stories)==0)  return '';
      $i=1;
      foreach ( $stories as $story ) {
        $news = array();

        $title = $story->title();
        if (strlen($title) > $options[15])
          $title = xoops_substr($title,0,$options[15]+3);
        $news['title'] = $title;
        $news['id'] = $story->storyid();
        $news['date'] = formatTimestamp($story->published(), $dateformat);
        $news['no'] = $i++;
        $news['author']= sprintf("%s %s",_POSTEDBY,$story->uname());
        $news['topic_title'] = $story->topic_title();

        if ($options[16] > 0) {
          $html = $story->nohtml() == 1 ? 0 : 1;
          //$html = $options[18] == 1 ? 0 : 1;//
          $smiley = $options[19] == 1 ? 0 : 1;
          $xcode = $options[20] == 1 ? 0 : 1;
          $image = $options[21] == 1 ? 0 : 1;
          $br = $options[22] == 1 ? 0 : 1;
          //--- for News versions prior to 1.60
          //$news['teaser'] = xoops_substr($myts->displayTarea(strip_tags($story->hometext)), 0, $options[16]+3);
          //--- for News version 1.60+
          $news['teaser'] = news_truncate_tagsafe(strip_tags($myts->displayTarea($story->hometext, $html, $smiley, $xcode, $image, $br )), $options[16]+3);
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
    return $block;
}

//----
function b_news_glider_edit( $options ){
	global $xoopsDB;
	$myts = & MyTextSanitizer :: getInstance();
	$form  = "<table width='100%' border='0'  class='bg2'>";
	$form .= "<tr><th width='50%'>"._OPTIONS."</th><th width='50%'>"._MB_NWS_SETTINGS."</th></tr>";
	$form .= "<tr><td class='even'>"._MB_NWS_BLIMIT."</td><td class='odd'><input type='text' name='options[0]' size='16' maxlength=3 value='".$options[0]."' /></td></tr>";
	$form .= "<tr><td class='even'>"._MB_NWS_BPACE."</td><td class='odd'><input type='text' name='options[1]' size='16' maxlength=2 value='".$options[1]."' /></td></tr>";
  //---
  $form .= "<tr><td class='even'>"._MB_NWS_DIRECTION."</td><td class='odd'><select name='options[2]'>";
  $form .= "<option value='downup' ".(($options[2]=='downup')?" selected='selected'":"").">"._MB_NWS_UP."</option>\n";
  $form .= "<option value='updown' ".(($options[2]=='updown')?" selected='selected'":"").">"._MB_NWS_DOWN."</option>\n";
  $form .= "<option value='leftright' ".(($options[2]=='leftright')?" selected='selected'":"").">"._LEFT."</option>\n";
  $form .= "<option value='rightleft' ".(($options[2]=='rightleft')?" selected='selected'":"").">"._RIGHT."</option>\n";
  $form .= "</select></td></tr>\n";
  //---
  $form .= "<tr><td class='even'>"._MB_NWS_PERSISTSTATE."</td><td class='odd'>";
  $form .= "<input type='radio' name='options[3]' value='1'".(($options[3]==1)?" checked='checked'":"")." />"._YES."&nbsp;";
  $form .= "<input type='radio' name='options[3]' value='0'".(($options[3]==0)?" checked='checked'":"")." />"._NO."<br /></td></tr>";
  //---
  $form .= "<tr><td class='even'>"._MB_NWS_BWIDTH."</td><td class='odd'><input type='text' name='options[4]' size='16' maxlength=4 value='".$options[4]."' /></td></tr>";
  $form .= "<tr><td class='even'>"._MB_NWS_BHEIGHT."</td><td class='odd'><input type='text' name='options[5]' size='16' maxlength=4 value='".$options[5]."' /></td></tr>";
  //---
	$form .= "<tr><td class='even'>"._MB_NWS_BACKGROUNDCOLOR."</td><td class='odd'><input type='text' name='options[6]' size='16' value='".$options[6]."' /></td></tr>";
	$form .= "<tr><td class='even'>"._MB_NWS_BBORDER."</td><td class='odd'><input type='text' name='options[7]' size='16' maxlength=2 value='".$options[7]."' /></td></tr>";
	$form .= "<tr><td class='even'>"._MB_NWS_BWRAPPERBCOLOR."</td><td class='odd'><input type='text' name='options[8]' size='16' value='".$options[8]."' /></td></tr>";
  
  //---
  $form .= "<tr><td class='even'>"._MB_NWS_BWRAPPERWIDTH."</td><td class='odd'><input type='text' name='options[9]' size='16' maxlength=4 value='".$options[9]."' /></td></tr>";
  //---
	$form .= "<tr><td class='even'>"._MB_NWS_AUTOROTATE."</td><td class='odd'>";
  $form .= "<input type='radio' name='options[10]' value='1'".(($options[10]==1)?" checked='checked'":"")." />"._YES."&nbsp;";
	$form .= "<input type='radio' name='options[10]' value='0'".(($options[10]==0)?" checked='checked'":"")." />"._NO."<br /></td></tr>";
  //---
  $form .= "<tr><td class='even'>"._MB_NWS_AUTORYCYLES."</td><td class='odd'><input type='text' name='options[11]' size='16' maxlength=4 value='".$options[11]."' /></td></tr>";
	//---
	$form .= "<tr><td class='even'>"._MB_NWS_SHOWDATE."</td><td class='odd'>";
	$form .= "<input type='radio' name='options[12]' value='1'".(($options[12]==1)?" checked='checked'":"")." />"._YES."&nbsp;";
	$form .= "<input type='radio' name='options[12]' value='0'".(($options[12]==0)?" checked='checked'":"")." />"._NO."<br /></td></tr>";
  //---  
	$form .= "<tr><td class='even'>"._MB_NWS_SORT."</td><td class='odd'><select name='options[13]'>";
	$form .= "<option value='RAND()' ".(($options[13]=='RAND()')?" selected='selected'":"").">"._MB_NWS_RANDOM."</option>\n";
	$form .= "<option value='published' ".(($options[13]=='published')?" selected='selected'":"").">"._MB_NWS_DATE."</option>\n";
	$form .= "<option value='counter' ".(($options[13]=='counter')?" selected='selected'":"").">"._MB_NWS_HITS."</option>\n";
	$form .= "<option value='title' ".(($options[13]=='title')?" selected='selected'":"").">"._MB_NWS_NAME."</option>\n";
	$form .= "</select></td></tr>\n";
  //---
  $form .= "<tr><td class='even'>"._MB_NWS_ORDER."</td><td class='odd'><select name='options[14]'>";
  $form .= "<option value='ASC' ".(($options[14]=='ASC')?" selected='selected'":"").">"._ASCENDING."</option>\n";
  $form .= "<option value='DESC' ".(($options[14]=='DESC')?" selected='selected'":"").">"._DESCENDING."</option>\n";
  $form .= "</select></td></tr>\n";
  //---
  $form .= "<tr><td class='even'>"._MB_NWS_CHARS."</td><td class='odd'><input type='text' name='options[15]' value='".$options[15]."'/></td></tr>";
  $form .= "<tr><td class='even'>"._MB_NWS_TEASER." </td><td class='odd'><input type='text' name='options[16]' value='".$options[16]."' /></td></tr>";
  //---
	$form .= "<tr><td class='even'>"._MB_NWS_BUTTONSNAV."</td><td class='odd'>";
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
	$form .= "<tr><td class='even'>"._MB_NWS_BR."</td><td class='odd'>";
	$form .= "<input type='radio' name='options[21]' value='1'".(($options[21]==1)?" checked='checked'":"")." />"._YES."&nbsp;";
	$form .= "<input type='radio' name='options[21]' value='0'".(($options[21]==0)?" checked='checked'":"")." />"._NO."<br /></td></tr>";
	//---
	$form .= "<tr><td class='even'>"._MB_NWS_IMAGE."</td><td class='odd'>";
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