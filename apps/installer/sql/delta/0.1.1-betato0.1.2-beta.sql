
--- @file 0.1.1-betato0.1.2-beta.sql
--- @author giorno
--- @package N7
--- @license Apache License, Version 2.0, see LICENSE file
---
--- Installation script for uprade of MySQL tables. Used in upgrade script
--- for upgrades from version 0.1.1-beta to 0.1.2-beta.

---
--- Required for PDO implementation of io\creat\chassis\session\settings class.
---
ALTER TABLE `core_settings` ADD UNIQUE `core_settings_index` ( `scope`, `id`, `ns`, `key` );

