ALTER TABLE `tag_tag` 
    CHANGE    `tag_term`            `tag_term`    varchar(64)        NOT NULL default '',
    ADD        `tag_status`         tinyint(1)         unsigned NOT NULL default '0' AFTER `tag_term`,
    ADD INDEX    `tag_status`    (`tag_status`);
    