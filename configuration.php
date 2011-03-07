<?php
/**************************************************
  CPG MiniCMS Plugin for Coppermine Photo Gallery
***************************************************/

$name='MiniCMS';
$description='MiniCMS provides a small content management system within the Coppermine Picture Gallery application that enables the admin to add textual content to existing coppermine pages and to add new pages with the look and feel of the existing ones.';
$author='Created for cpg1.4.x by Donnoman@donovanbray.com';
$author.='<br />Initial release for cpg1.5.x by <a href="http://forum.coppermine-gallery.net/index.php?action=profile;u=41609" rel="external" class="external">halnat</a>.';
$author.='<br />Made fully compatible to cpg1.5.x by <a href="http://forum.coppermine-gallery.net/index.php?action=profile;u=24278" rel="external" class="external">eenemeenemuu</a>';
$version='2.1';
$plugin_cpg_version = array('min' => '1.5.10');

$lang = isset($CONFIG['lang']) ? $CONFIG['lang'] : 'english';
include('plugins/minicms/lang/english.php');
if (in_array($lang, $enabled_languages_array) == TRUE && file_exists('plugins/minicms/lang/'.$lang.'.php')) {
    include('plugins/minicms/lang/'.$lang.'.php');
}

$extra_info = $install_info = '<a href="http://forum.coppermine-gallery.net/index.php/topic,68455.0.html" rel="external" class="admin_menu">'.cpg_fetch_icon('announcement', 1).'Announcement thread</a>';

?>