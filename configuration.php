<?php
/**************************************************
  CPG MiniCMS Plugin for Coppermine Photo Gallery
  *************************************************
  CPGMiniCMS version: 1.0 - 1.6
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
  Coppermine version: 1.4.9
  CPGMiniCMS version: 1.7A1
  $Source: /cvsroot/cpg-contrib/minicms/configuration.php,v $
  $Revision: 1.8 $
  $Author: donnoman $
  $Date: 2006/11/10 21:24:21 $
***************************************************/
global $CONFIG;
$name='CPG MiniCMS';
$description='CPG MiniCMS provides a small CMS within the Coppermine Picture Gallery framework.';
$author='Donnoman@donovanbray.com from <a href="http://cpg-contrib.org" target="_blank">cpg-contrib.org</a>';
$version='1.6';

if (file_exists("plugins/minicms/lang/{$CONFIG['lang']}.php")) {
  require "plugins/minicms/lang/{$CONFIG['lang']}.php";
} else require 'plugins/minicms/lang/english.php';

$install_info=<<<EOT
    <table border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td class="admin_menu"><a target="_blank" href="plugins/minicms/README" title="README">README</a></td>
        <td class="admin_menu"><a target="_blank" href="plugins/minicms/CHANGELOG" title="CHANGELOG">CHANGELOG</a></td>
        <td class="admin_menu"><a href="index.php?file=minicms/cms_config" title="{$lang_minicms['config_title']}">{$lang_minicms['config_title']}</a></td>
    </tr>
    </table>
EOT;

$extra_info = <<<EOT
    <table border="0" cellspacing="0" cellpadding="0">
    <tr>
        <td class="admin_menu"><a target="_blank" href="plugins/minicms/README" title="README">README</a></td>
        <td class="admin_menu"><a target="_blank" href="plugins/minicms/CHANGELOG" title="CHANGELOG">CHANGELOG</a></td>
    </tr>
    </table>
EOT;

?>