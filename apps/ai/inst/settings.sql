--- Install global user settings for AI application. For rules applying here
--- read comments in solution settings.sql file.
--- {$__2} app namespace
--- {$__3} solution namespace

--- Default values for lists.
INSERT INTO `{$__1}` SET `scope` = "G", `ns` = "{$__2}", `key` = "usr.lst.Users", `value` = "a:4:{s:1:\"k\";s:0:\"\";s:1:\"o\";s:3:\"uid\";s:1:\"d\";s:3:\"ASC\";s:1:\"p\";i:1;}";

