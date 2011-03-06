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
  $Source: /cvsroot/cpg-contrib/minicms/include/init.inc.php,v $
  $Revision: 1.17 $
  $Author: donnoman $
  $Date: 2006/11/10 21:24:24 $
***************************************************/

if (!defined('IN_COPPERMINE')) { die('Not in Coppermine...');}

define('MINICMS_DBVER','1.4.8');

// submit your lang file for this plugin on the coppermine forums
// plugin will try to use the configured language if it is available.

if (file_exists("plugins/minicms/lang/{$CONFIG['lang']}.php")) {
  require_once "plugins/minicms/lang/{$CONFIG['lang']}.php";
} else require_once 'plugins/minicms/lang/english.php';

$CONFIG['TABLE_CMS'] = $CONFIG['TABLE_PREFIX'] . "cms";
$CONFIG['TABLE_CMS_CONFIG'] = $CONFIG['TABLE_PREFIX'] . "cms_config";

$results=cpg_db_query("SHOW TABLES LIKE '{$CONFIG['TABLE_CMS_CONFIG']}'");
if (!$row=mysql_fetch_row($results)) minicms_configure(false);
mysql_free_result($results);

$results = cpg_db_query("SELECT * FROM {$CONFIG['TABLE_CMS_CONFIG']}");
while ($row = mysql_fetch_array($results)) {
    $MINICMS[$row['name']] = $row['value'];
} // while
mysql_free_result($results);

$HTML_SUBST_DECODE = array_flip($HTML_SUBST); //used to reverse Coppermines init.inc.php gpc processing

$MINICMS['conType']=array('cat','thumb','img','section');
$MINICMS['conTypebyName']=array_flip($MINICMS['conType']);

if (defined('DISPLAYIMAGE_PHP')) {
    $MINICMS['type']=$MINICMS['conTypebyName']['img'];
} elseif (defined('THUMBNAILS_PHP')) {
    $MINICMS['conid']=isset($_REQUEST['album']) ? (int)$_REQUEST['album'] : -1;
    $MINICMS['type']=$MINICMS['conTypebyName']['thumb'];
} elseif (isset($_REQUEST['file']) && $_REQUEST['file'] =='minicms/cms') {
    if (isset($_REQUEST['id'])) {
        $MINICMS['ID']=(int)$_REQUEST['id'];
        $MINICMS['conid']='';
        $MINICMS['type']='';
    } else {
      $MINICMS['conid']=(int)$_REQUEST['conid'];
      $MINICMS['type']=(int)$_REQUEST['type'];
    }
} else {
    $MINICMS['conid']=isset($_REQUEST['cat']) ? (int)$_REQUEST['cat'] : 0;
    $MINICMS['type']=$MINICMS['conTypebyName']['cat'];
}

require 'plugins/minicms/include/themes.inc.php';