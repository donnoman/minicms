<?php
/**************************************************
  CPG MiniCMS Plugin for Coppermine Photo Gallery
***************************************************/
require_once('include/init.inc.php');

$html = minicms();
$title = (isset($cms_array[0]['title'])) ? $cms_array[0]['title'] : $lang_minicms['article'];
pageheader($title);
echo $html;
pagefooter();

?>