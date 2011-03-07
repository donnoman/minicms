<?php
/**************************************************
  MiniCMS Plugin for Coppermine Photo Gallery
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
    <a href="plugins/minicms/readme.txt" title="README" class="admin_menu">README</a>&nbsp;
    <a href="plugins/minicms/changelog.txt" title="CHANGELOG" class="admin_menu">CHANGELOG</a>&nbsp;
    <a href="index.php?file=minicms/cms_config" title="{$lang_minicms['config_title']}" class="admin_menu">{$lang_minicms['config_title']}</a>
EOT;

$extra_info = <<<EOT
    <a href="plugins/minicms/readme.txt" title="README" class="admin_menu">README</a>&nbsp;
    <a href="plugins/minicms/changelog.txt" title="CHANGELOG" class="admin_menu">CHANGELOG</a>
EOT;

?>