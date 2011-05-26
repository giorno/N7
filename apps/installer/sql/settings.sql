--- Global user settings for N7 solution. These settings are fallback values for
--- the case user real settings are not found.
---
--- Warning! This script should not be run directly, but loaded and executed
--- from PHP code binding variables:
---
--- {$__1} should be replaced with table name
--- {$__2} should be replaced with solution namespace
---

--- Server configuration.
INSERT INTO `{$__1}` SET `scope` = "G", `ns` = "{$__2}", `key` = "server.url.site", `value` = "localhost/morb/gtdtab/page/";
INSERT INTO `{$__1}` SET `scope` = "G", `ns` = "{$__2}", `key` = "server.url.scheme", `value` = "http";
INSERT INTO `{$__1}` SET `scope` = "G", `ns` = "{$__2}", `key` = "server.url.modrw", `value` = "2";

--- Server timezone.
INSERT INTO `{$__1}` SET `scope` = "G", `ns` = "{$__2}", `key` = "server.tz", `value` = "Europe/Dublin";

--- Default language.
INSERT INTO `{$__1}` SET `scope` = "G", `ns` = "{$__2}", `key` = "usr.lang", `value` = "en";

--- Default list page size.
INSERT INTO `{$__1}` SET `scope` = "G", `ns` = "{$__2}", `key` = "usr.lst.len", `value` = "20";

--- Default list pager half-size.
INSERT INTO `{$__1}` SET `scope` = "G", `ns` = "{$__2}", `key` = "usr.lst.pagerhalf", `value` = "3";

--- Default user timezone.
INSERT INTO `{$__1}` SET `scope` = "G", `ns` = "{$__2}", `key` = "usr.tz", `value` = "Europe/Brussels";

--- Textareas minimal height.
INSERT INTO `{$__1}` SET `scope` = "G", `ns` = "{$__2}", `key` = "usr.ta.h.min", `value` = "160";
