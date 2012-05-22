
--- @file 0to0.1.1-beta.sql
--- @author giorno
--- @package N7
--- @license Apache License, Version 2.0, see LICENSE file
---
--- Installation script for uprade of MySQL tables. Used in upgrade script
--- for upgrades from versions before 0.1.1-beta to 0.1.1-beta.

---
--- Table for XML RPC authentication tokens.
---

CREATE TABLE  `n7_rpcsess` (
  `uid` bigint( 20 ) NOT NULL,
  `token` char( 32 ) NOT NULL,
  `expires` datetime NOT NULL,
  INDEX ( `uid` )
) ENGINE = INNODB;


---
--- Constraints for XML RPC tokens table.
---

ALTER TABLE  `n7_rpcsess` ADD FOREIGN KEY (`uid`) REFERENCES `core_users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE;
