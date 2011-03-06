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

  $Source: /cvsroot/cpg-contrib/minicms/rss-related.php,v $
  $Revision: 1.4 $
  $Author: donnoman $
  $Date: 2006/11/10 21:24:21 $
***************************************************/
if (!defined('IN_COPPERMINE')) die('Not in Coppermine...');
require('include/init.inc.php');

if ($MINICMS['rss_enabled']==0) die($lang_minicms['no_rss']);

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
}
if (isset($conid)) {
    $query[] = " conid='{$MINICMS['conid']}' AND type='{$MINICMS['type']}'";
    $REFERER .= "&amp;conid={$MINICMS['conid']}&amp;type={$MINICMS['type']}";
}
if (isset($keyword) || !count($query)) {
    $keyword=($keyword) ? $keyword : 'BLOG'; // if nothing else related looks for keyword BLOG (config setting)
    $query[] = " keywords like '%$keyword%' AND type={$MINICMS['conTypebyName']['img']}";
    $REFERER .= "&amp;keyword=$keyword";
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
mysql_data_seek($result,0); //put the pointer back to the first entry

header("Content-type: text/xml");
header("Pragma: public");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    print <<<EOT
<?xml version="1.0"?>
<rss version="2.0">
<channel>
<title>{$CONFIG['gallery_name']}</title>
<link>{$CONFIG['ecards_more_pic_target']}index.php?file=minicms/related</link>
<description>{$CONFIG['gallery_description']}</description>
EOT;

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
    $RFC822 = '%a, %d %b %y %T %Z';
    $cms['modified'] = localised_date($cms['modified'], $RFC822);


        $title_bar = <<<EOT
                    {$cms['title']}
EOT;

    $cms['content'] = htmlentities(strip_tags(html_entity_decode(stripslashes($cms['content'])))); //used to reverse Coppermines init.inc.php gpc processing
		//if description is longer than setting, add elipses after truncating text
		if (strlen($cms['content']) > $MINICMS['rss_description_length']) {
			$cms['content'] = substr($cms['content'],0,$MINICMS['rss_description_length']).'...';
		}

 		//if config allows, include image in rss feed
	  if ($MINICMS['rss_include_image']==1) {
			$rss_image = "&lt;img src=&quot;{$CONFIG['ecards_more_pic_target']}{$cms['thumb_url']}&quot; border=&quot;0&quot; align=&quot;left&quot;  alt=&quot;&quot; &gt;";
			}

    print <<<EOT

<item>
<title>{$cms['title']}</title>
<link>{$CONFIG['ecards_more_pic_target']}index.php?file=minicms/related&amp;id={$cms['ID']}</link>
<description>{$cms['content']} &lt;a href=&quot;{$CONFIG['ecards_more_pic_target']}{$cms['thumb_link']}&quot; target=&quot;_blank&quot;&gt;[{$lang_minicms['rss_more_lnk']}]$rss_image&lt;/a&gt;</description>
<pubDate>{$cms['modified']}</pubDate>
</item>
EOT;
}

mysql_free_result($result);


ob_end_flush();
?>
</channel>
</rss>