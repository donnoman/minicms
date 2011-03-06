<?php
/**************************************************
  MiniCMS Plugin for Coppermine Photo Gallery
  *************************************************
  MiniCMS
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
***************************************************/
global $CONFIG;
$name='MiniCMS';
$description='MiniCMS provides a small content management system within the Coppermine Picture Gallery application that enables the admin to add textual content to existing coppermine pages and to add new pages with the look and feel of the existing ones.';
$author='Donnoman@donovanbray.com';
$version='2.1';
$plugin_cpg_version = array('min' => '1.4', 'max' => '1.4.99');

if (file_exists("plugins/minicms/lang/{$CONFIG['lang']}.php")) {
  require "plugins/minicms/lang/{$CONFIG['lang']}.php";
} else require 'plugins/minicms/lang/english.php';

$install_info=<<<EOT
    <a href="plugins/minicms/readme" title="README" class="admin_menu">README</a>&nbsp;
    <a href="plugins/minicms/changelog" title="CHANGELOG" class="admin_menu">CHANGELOG</a>&nbsp;
    <a href="index.php?file=minicms/cms_config" title="{$lang_minicms['config_title']}" class="admin_menu">{$lang_minicms['config_title']}</a>
EOT;

$extra_info = <<<EOT
    <a href="plugins/minicms/readme" title="README" class="admin_menu">README</a>&nbsp;
    <a href="plugins/minicms/changelog" title="CHANGELOG" class="admin_menu">CHANGELOG</a>
EOT;

?>