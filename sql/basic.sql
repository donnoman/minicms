#/**************************************************
#  CPG MiniCMS Plugin for Coppermine Photo Gallery
#***************************************************/

#
# Table structure for table `CPG_cms`
#

CREATE TABLE `CPG_cms` (
  `ID` int(11) NOT NULL auto_increment,
  `catid` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `content` text NOT NULL default '',
  PRIMARY KEY  (`ID`, `catid`),
  FULLTEXT KEY `title` (`title`,`content`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

#
# Table structure for table `CPG_cms_config`
#

CREATE TABLE CPG_cms_config (
  name varchar(40) NOT NULL default '',
  value varchar(255) NOT NULL default '',
  PRIMARY KEY  (name)
) TYPE=MyISAM;

ALTER TABLE `CPG_cms` ADD `pos` int(11) NOT NULL default '0';
ALTER TABLE `CPG_cms` ADD `type` int(11) NOT NULL default '0';
ALTER TABLE `CPG_cms` CHANGE `catid` `conid` int(11) NOT NULL default '0';
ALTER TABLE `CPG_cms` CHANGE `pos` `cpos` int(11) NOT NULL default '0';

ALTER TABLE `CPG_cms` ADD `modified` TIMESTAMP NOT NULL;
ALTER TABLE `CPG_cms` ADD `start` DATETIME;
ALTER TABLE `CPG_cms` ADD `end` DATETIME;

INSERT INTO `CPG_cms` (conid,title,content,type) VALUES ('0','Welcome to Coppermine', 'Simple test of CPG MiniCMS','0');
INSERT INTO `CPG_cms_config` VALUES ('dbver', '0.0');
INSERT INTO `CPG_cms_config` VALUES ('redirect_index_php', '');
INSERT INTO `CPG_cms_config` VALUES ('related_size', 'thumb');
INSERT INTO `CPG_cms_config` VALUES ('editor', 'fckeditor');
INSERT INTO `CPG_cms_config` VALUES ('rss_enabled', '0');
INSERT INTO `CPG_cms_config` VALUES ('rss_description_length', '50');
INSERT INTO `CPG_cms_config` VALUES ('rss_include_image', '0');
INSERT INTO `CPG_cms_config` VALUES ('rss_image_size', 'thumb');


UPDATE `CPG_cms` SET `modified`=NOW() WHERE `modified`='0000-00-00 00:00:00';

# Cleanup - Values that shouldn't exist anymore:
ALTER TABLE `CPG_cms` DROP `pos`;

# Write this dbver to the config table
# This should match the DBVER constant in init.inc.php
UPDATE CPG_cms_config SET value='1.5.8' WHERE name='dbver';

