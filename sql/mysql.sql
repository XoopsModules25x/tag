#--
#-- Table structure for table `tag`
#--

CREATE TABLE `tag_tag` (
  `tag_id`     INT(10) UNSIGNED            NOT NULL AUTO_INCREMENT,
  `tag_term`   VARCHAR(64) /* BINARY */    NOT NULL DEFAULT '',
  `tag_status` TINYINT(1) UNSIGNED         NOT NULL DEFAULT '0',
  `tag_count`  INT(10) UNSIGNED            NOT NULL DEFAULT '0',

  PRIMARY KEY (`tag_id`),
  KEY `tag_term`     (`tag_term`),
  KEY `tag_status`   (`tag_status`)
)
  ENGINE = MyISAM;

#-- --------------------------------------------------------
#
#--
#-- Table structure for table `link`
#--

CREATE TABLE `tag_link` (
  `tl_id`      INT(10) UNSIGNED     NOT NULL AUTO_INCREMENT,
  `tag_id`     INT(10) UNSIGNED     NOT NULL DEFAULT '0',
  `tag_modid`  SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
  `tag_catid`  INT(10) UNSIGNED     NOT NULL DEFAULT '0',
  `tag_itemid` INT(10) UNSIGNED     NOT NULL DEFAULT '0',
  `tag_time`   INT(10) UNSIGNED     NOT NULL DEFAULT '0',

  PRIMARY KEY (`tl_id`),
  KEY `tag_id`       (`tag_id`),
  KEY `tag_time`     (`tag_time`),
  KEY `tag_item`     (`tag_modid`, `tag_catid`, `tag_itemid`)
)
  ENGINE = MyISAM;

#-- --------------------------------------------------------

#--
#-- Table structure for table `stats`
#--

CREATE TABLE `tag_stats` (
  `ts_id`     INT(10) UNSIGNED     NOT NULL AUTO_INCREMENT,
  `tag_id`    INT(10) UNSIGNED     NOT NULL DEFAULT '0',
  `tag_modid` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
  `tag_catid` INT(10) UNSIGNED     NOT NULL DEFAULT '0',
  `tag_count` INT(10) UNSIGNED     NOT NULL DEFAULT '0',

  PRIMARY KEY (`ts_id`),
  KEY `tag_id`       (`tag_id`),
  KEY `tag_modid`    (`tag_modid`),
  KEY `tag_count`    (`tag_count`)
)
  ENGINE = MyISAM;
