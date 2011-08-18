
--- @file tables.sql
--- @author giorno
--- @package N7
--- @license Apache License, Version 2.0, see LICENSE file
---
--- Script installing database tables specific for N7 solution. These extend
--- base tables installed by framework script.

---
--- Table of installed applications
---

CREATE TABLE IF NOT EXISTS `n7_at` (
  `ns` char(64) COLLATE utf8_unicode_ci NOT NULL,
  `app_id` char(64) COLLATE utf8_unicode_ci NOT NULL,
  `fs_name` char(64) COLLATE utf8_unicode_ci NOT NULL,
  `version` char(16) COLLATE utf8_unicode_ci NOT NULL,
  `inst_seq` int(4) NOT NULL,
  `exec_seq` int(4) NOT NULL,
  `i18n` text COLLATE utf8_unicode_ci NOT NULL,
  `stamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `flags` int(4) NOT NULL,
  KEY `ns` (`ns`,`app_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

---
--- Cache for blogs.
---

CREATE TABLE  `signed_news` (
  `lang` CHAR( 2 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `url` VARCHAR( 1024 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `title` VARCHAR( 1024 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `ts` TIMESTAMP NOT NULL
) ENGINE = MYISAM;
