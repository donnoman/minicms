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
  $Source: /cvsroot/cpg-contrib/minicms/cms.php,v $
  $Revision: 1.11 $
  $Author: donnoman $
  $Date: 2006/11/10 21:24:21 $
***************************************************/

require('include/init.inc.php');

$html = minicms();
$title = (isset($cms_array[0]['title'])) ? $cms_array[0]['title'] : $lang_minicms['article'];
pageheader($title);
echo $html;
pagefooter();

?>