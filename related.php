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

  $Source: /cvsroot/cpg-contrib/minicms/related.php,v $
  $Revision: 1.20 $
  $Author: donnoman $
  $Date: 2006/11/10 21:24:21 $
***************************************************/
if (!defined('IN_COPPERMINE')) die('Not in Coppermine...');
require('include/init.inc.php');
//require 'plugins/minicms/include/init.inc.php';

if (isset($_REQUEST['id'])) {
	$ID = (int)$_REQUEST['id'];
}
if (isset($_REQUEST['conid'])) {
	$MINICMS['conid'] = (int)$_REQUEST['conid'];
}
if (isset($_REQUEST['type'])) {
	$MINICMS['type'] = (int)$_REQUEST['type'];
}
if (!isset($cat)) { // makes sure we don't get in a loop and can't navigate the gallery when forwarding index.php
    $cat=0;
}
if (isset($_REQUEST['keyword'])) {
	$keyword = addslashes($_REQUEST['keyword']);
}
if (isset($_REQUEST['size'])) {
	$MINICMS['related_size'] = (array_key_exists($_REQUEST['size'],$lang_minicms_config_related_size)) ? $_REQUEST['size'] : $MINICMS['related_size'];
}

if (isset($ID)) {
    $query[] = " ID='$ID'";
    $REFERER .= "&amp;ID='$ID'";
    $idquery = "&amp;id=$ID";
}
if (isset($conid)) {
    $query[] = " conid='{$MINICMS['conid']}' AND type='{$MINICMS['type']}'";
    $REFERER .= "&amp;conid={$MINICMS['conid']}&amp;type={$MINICMS['type']}";
    $conidquery .= "&amp;conid={$MINICMS['conid']}&amp;type={$MINICMS['type']}";
}
if (isset($keyword) || !count($query)) {
    $keyword=($keyword) ? $keyword : 'BLOG'; // if nothing else related looks for keyword BLOG (config setting)
    $query[] = " keywords like '%$keyword%' AND type={$MINICMS['conTypebyName']['img']}";
    $REFERER .= "&amp;keyword=$keyword";
    $keywordquery = "&amp;keyword=$keyword";
}

$query=implode(' AND',$query);

if(count($FORBIDDEN_SET_DATA) > 0 ){
    $forbidden_set_string =" AND aid NOT IN (".implode(",", $FORBIDDEN_SET_DATA).")";
} else {
    $forbidden_set_string = '';
}

$order ="ORDER BY modified DESC ";
$query = "SELECT *, C.title as title, unix_timestamp(modified) as modified FROM {$CONFIG['TABLE_CMS']} as C , {$CONFIG['TABLE_PICTURES']} as P WHERE conid=pid AND $query $forbidden_set_string $order;";
$result = cpg_db_query($query);
if (!mysql_num_rows($result))
	cpg_die(CRITICAL_ERROR, $lang_minicms['non_exist'], __FILE__, __LINE__);
$cms=mysql_fetch_array($result);
pageheader($cms['title']); //use whatever title is the first article for the page
mysql_data_seek($result,0); //put the pointer back to the first entry

while ($cms=mysql_fetch_array($result)) {
    $CURRENT_PIC_DATA=$cms; //send a copy to get_pic_url it messes with the vars

    if (stristr($MINICMS['related_size'],'thumb')) {
        $cms['thumb_url'] = get_pic_url($CURRENT_PIC_DATA, 'thumb');
    } else {
        if($CONFIG['thumb_use']=='ht' && $CURRENT_PIC_DATA['pheight'] > $CONFIG['picture_width'] ){ // The wierd comparision is because only picture_width is stored
          $condition = true;
        }elseif($CONFIG['thumb_use']=='wd' && $CURRENT_PIC_DATA['pwidth'] > $CONFIG['picture_width']){
          $condition = true;
        }elseif($CONFIG['thumb_use']=='any' && max($CURRENT_PIC_DATA['pwidth'], $CURRENT_PIC_DATA['pheight']) > $CONFIG['picture_width']){
          $condition = true;
        }else{
         $condition = false;
        }
        if ($CONFIG['make_intermediate'] && $condition ) {
            $cms['thumb_url'] = get_pic_url($CURRENT_PIC_DATA, 'normal');
        } else {
            $cms['thumb_url'] = get_pic_url($CURRENT_PIC_DATA, 'fullsize');
        }
    }

    $cms['thumb_link'] = 'displayimage.php?pos='.(-$cms['pid']);
    $cms['modified'] = localised_date($cms['modified'], $lasthit_date_fmt);

//add comments link
//No link will display if the user cannot post comments and there are no comments. Otherwise, there is a link to view existing comments.
$com_count = cpg_db_query("SELECT count(*) FROM {$CONFIG['TABLE_COMMENTS']} where pid='{$cms['pid']}'");
if (mysql_num_rows($com_count)) {
  	$cms['comments_count'] = mysql_fetch_array($com_count);
    $cms['comments_count'] = $cms['comments_count'][0];
	if ($cms['comments_count'] > 0) {
		$cms['user_comment_lnk'] = '<div class="comments_lnk"><a href="'.'displayimage.php?pos='.(-$cms['pid']).'#comments' . '" target="_blank">'.$lang_minicms['comments_lnk'].' ('.($cms['comments_count']).')</a></div>';
	}
}	else {
		$cms['comments_count'] = 0;
		$cms['user_comment_lnk'] = '';
}

if (USER_CAN_POST_COMMENTS) {
	if ($cms['comments_count'] > 0) {
		$cms['user_comment_lnk'] = '<div class="comments_lnk"><a href="'.'displayimage.php?pos='.(-$cms['pid']).'#message' . '" target="_blank">'.$lang_minicms['comments_lnk'].' (' . ($cms['comments_count']) . ')</a></div>';
	} else {
		 $cms['user_comment_lnk'] = '<div class="comments_lnk"><a href="'.'displayimage.php?pos='.(-$cms['pid']).'#message' . '" target="_blank">'.$lang_minicms['comments_lnk'].'</a></div>';
	}
}

    if (GALLERY_ADMIN_MODE) {
    		$title_bar = <<<EOT
            <table width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr>
            <td width="100%">
                 {$cms['title']}
            </td>
            <td>
                 <img src="images/spacer.gif" width="38" height="1" style="display:block" />
                 <a href="index.php?file=minicms/cms_admin&amp;id={$cms['ID']}&amp;delete&amp;referer={$REFERER}&amp;"><img style="display:inline" src="images/delete.gif" border="0" alt="" /></a>
    			 <a href="index.php?file=minicms/cms_edit&amp;id={$cms['ID']}&amp;referer={$REFERER}"><img style="display:inline" src="images/edit.gif" border="0" /></a>
            </td>
            </tr>
            </table>

EOT;
    } else {
        $title_bar = <<<EOT
					<!-- user_comment_lnk -->
                    {$cms['title']}
EOT;
    }

    $cms['content'] = html_entity_decode(stripslashes($cms['content'])); //used to reverse Coppermines init.inc.php gpc processing

    starttable("100%", $title_bar, 2);

    print <<<EOT
		<tr>
				<td class="tableh2" colspan="2">
                   <span class="album_stat">{$cms['modified']}</span>
				</td>
		</tr>
    <tr>
				<td class="tableb" valign="top" width="100%">
						{$cms['content']}
				</td>
				<td class="tableb" valign="top" align="right" >

                   <a href="{$cms['thumb_link']}" target="_blank"><img class="image" src="{$cms['thumb_url']}" border="0" alt="" /></a>
						{$cms['user_comment_lnk']}

				</td>
		</tr>
EOT;
    endtable();
}

mysql_free_result($result);

/*  Not sure where this content will go, better to have them create it on the image they want to blog.
if (GALLERY_ADMIN_MODE) {
    $cms['title'] = $lang_minicms['minicms'].'</td><td width="1" class="tableh1"><a href="index.php?file=minicms/cms_edit&amp;id=new&amp;conid='.$MINICMS['conid'].'&amp;type='.$MINICMS['type'].'&amp;referer='.$REFERER.'"><img src="images/edit.gif" border="0" align="right"></a>';
    starttable("100%", $cms['title'], '1" width="100%');
    endtable();
}
 */

//if RSS is enabled, display RSS button
if ($MINICMS['rss_enabled']==1) {
    print <<<EOT
<div><a href="{$CONFIG['ecards_more_pic_target']}index.php?file=minicms/rss-related$keywordquery$idquery$conidquery"><img src="{$CONFIG['ecards_more_pic_target']}plugins/minicms/images/rss.gif" style="border:none;" /></a></div>

EOT;
}

pagefooter();
ob_end_flush();
?>