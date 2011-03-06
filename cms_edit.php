<?php
/**************************************************
  CPG MiniCMS Plugin for Coppermine Photo Gallery
  *************************************************
  CPGMiniCMS
  Copyright (c) 2005-2006 Donovan Bray <donnoman@donovanbray.com>
  *************************************************
  1.3.0  eXtended miniCMS
  Copyright (C) 2004 Michael Trojacher <m.trojacher@webtips.at>
  Original miniCMS Code (c) 2004 by Tarique Sani <tarique@sanisoft.com>,
  Amit Badkas <amit@sanisoft.com>
  *************************************************
  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 2 of the License, or
  (at your option) any later version.
  *************************************************
  Coppermine version: 1.4.x

  $Source: /cvsroot/cpg-contrib/minicms/cms_edit.php,v $
  $Revision: 1.21 $
  $Author: donnoman $
  $Date: 2007/02/10 21:08:58 $
***************************************************/

require('include/init.inc.php');

if (!(GALLERY_ADMIN_MODE))
	cpg_die(ERROR, $lang_errors['access_denied'], __FILE__, __LINE__);

if (isset($_REQUEST['referer'])) {
    $referer = urlencode(html_entity_decode($_REQUEST['referer']));
    if (strpos($referer, "http") !== false) {
      $referer = urlencode("index.php?file=minicms/cms_edit");
    }
} else {
    $referer = urlencode("index.php?file=minicms/cms_edit");
}

if (isset($_REQUEST['id'])) {
    $id = (int)$_REQUEST['id'];
} else {
    $id = -1;
}

if(isset($_REQUEST['submit']) && $_REQUEST['submit']==$lang_minicms['submit'] && $_REQUEST['id'] > -1){
    $MINICMS['conid']=(int)$_REQUEST['conid'];
    $MINICMS['type']=(int)$_REQUEST['type'];
    $title=mysql_real_escape_string($_REQUEST['title']);
    $content=mysql_real_escape_string($_REQUEST['minicms_content']);
    $query = "UPDATE {$CONFIG['TABLE_CMS']} SET title = '$title', content = '$content', type = '{$MINICMS['type']}' WHERE ID = '$id'";
    $result = cpg_db_query($query);
    if ($result) {
        $redirect=urldecode($referer);
        pageheader($_POST['title'], "<meta http-equiv=\"refresh\" content=\"3;url=$redirect\" />");
        msg_box($lang_minicms['minicms'], $lang_minicms['page_success'],$lang_continue." <br />", $redirect);
        pagefooter();
        exit;
    }
}

if(isset($_REQUEST['conid']) && isset($_REQUEST['id']) && $_REQUEST['id']=='-1' && $_REQUEST['submit']==$lang_minicms['submit']) {
    $MINICMS['conid']=(int)$_REQUEST['conid'];
    $MINICMS['type']=(int)$_REQUEST['type'];
    $title = (isset($_REQUEST['title'])) ? mysql_real_escape_string($_REQUEST['title']) : $lang_minicms['article'];
    $content=mysql_real_escape_string($_REQUEST['minicms_content']);
    $query="SELECT cpos FROM {$CONFIG['TABLE_CMS']} WHERE conid='{$MINICMS['conid']}' ORDER BY cpos DESC LIMIT 1";
    $result = cpg_db_query($query);
    if ($result) {
        $cms=mysql_fetch_array($result);
        mysql_free_result($result);
        $cms['cpos']+=1;
    } else {
        $cms['cpos']=0;
    }
	$query="INSERT INTO {$CONFIG['TABLE_CMS']} SET title = '$title',conid='{$MINICMS['conid']}',type='{$MINICMS['type']}',cpos='{$cms['cpos']}', content = '$content';";
	$result = cpg_db_query($query);
	if ($result) {
        $message = $lang_minicms['page_success'];
    } else {
        $message = $lang_minicms['page_fail'];
    }
    $id=mysql_insert_id();
	mysql_free_result($result);
}

if(isset($_REQUEST['submit']) && $_REQUEST['submit'] == $lang_minicms['preview']){
    $cms['ID'] = $_REQUEST['id'];
    $cms['conid'] = $_REQUEST['conid'];
    $cms['title'] = $_REQUEST['title'];
    $cms['content'] = $_REQUEST['minicms_content'];
    $cms['type'] = $_REQUEST['type'];
    //$message = $lang_minicms['preview'];
} elseif ($_REQUEST['id']=='new') {
	$cms['ID'] = -1;
    $cms['conid'] = $_REQUEST['conid'];
    $cms['title'] = $lang_minicms['new_content'];
    $cms['content'] = '';
    $cms['type'] = $_REQUEST['type'];
    //$message = $lang_minicms['new_content'];
}else {
    $query = "SELECT * FROM {$CONFIG['TABLE_CMS']} WHERE ID=$id";
    $result = cpg_db_query($query);
    if (!mysql_num_rows($result))
	cpg_die(CRITICAL_ERROR, $lang_minicms['non_exist'], __FILE__, __LINE__);
    $cms = mysql_fetch_array($result);
    mysql_free_result($result);
}

$cms['content'] = html_entity_decode(stripslashes($cms['content']));
$cms['message'] = (isset($message)) ? $message : '';

theme_minicms_edit($cms);

?>
