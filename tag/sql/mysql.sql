
-- 
-- Table structure for table `tag`
-- 

CREATE TABLE `tag_tag` (
  `tag_id`              int(10)             unsigned NOT NULL auto_increment,
  `tag_term`            varchar(64) /* BINARY */    NOT NULL default '',
  `tag_status`          tinyint(1)             unsigned NOT NULL default '0',
  `tag_count`           int(10)             unsigned NOT NULL default '0',
  
  PRIMARY KEY           (`tag_id`),
  KEY `tag_term`        (`tag_term`),
  KEY `tag_status`      (`tag_status`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `link`
-- 

CREATE TABLE `tag_link` (
  `tl_id`                 int(10)             unsigned NOT NULL auto_increment,
  `tag_id`                 int(10)             unsigned NOT NULL default '0',
  `tag_modid`             smallint(5)         unsigned NOT NULL default '0',
  `tag_catid`             int(10)             unsigned NOT NULL default '0',
  `tag_itemid`             int(10)             unsigned NOT NULL default '0',
  `tag_time`             int(10)             unsigned NOT NULL default '0',
  
  PRIMARY KEY              (`tl_id`),
  KEY `tag_id`            (`tag_id`),
  KEY `tag_time`        (`tag_time`),
  KEY `tag_item`        (`tag_modid`, `tag_catid`, `tag_itemid`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `stats`
-- 

CREATE TABLE `tag_stats` (
  `ts_id`                 int(10)             unsigned NOT NULL auto_increment,
  `tag_id`                 int(10)             unsigned NOT NULL default '0',
  `tag_modid`             smallint(5)         unsigned NOT NULL default '0',
  `tag_catid`             int(10)             unsigned NOT NULL default '0',
  `tag_count`             int(10)             unsigned NOT NULL default '0',
  
  PRIMARY KEY              (`ts_id`),
  KEY `tag_id`            (`tag_id`),
  KEY `tag_modid`        (`tag_modid`),
  KEY `tag_count`        (`tag_count`)
) ENGINE=MyISAM;