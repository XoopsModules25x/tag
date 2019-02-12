ALTER TABLE `tag_tag`
  CHANGE `tag_term`   `tag_term` VARCHAR(64) NOT NULL DEFAULT '',
  ADD `tag_status` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0'
  AFTER `tag_term`,
  ADD INDEX `tag_status` (`tag_status`);
