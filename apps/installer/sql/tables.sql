
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

CREATE TABLE IF NOT EXISTS `tApps` (
  `ns` char(64) NOT NULL,
  `app_id` char(64) NOT NULL,
  `fs_name` char(64) NOT NULL,
  `version` char(16) NOT NULL,
  `inst_seq` int(4) NOT NULL,
  `exec_seq` int(4) NOT NULL,
  `stamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `flags` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;