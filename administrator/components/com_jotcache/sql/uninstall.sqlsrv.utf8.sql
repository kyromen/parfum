-- version 5.1.0
-- package JotCache
-- category Joomla 3.4
-- copyright (C) 2010-2015 Vladimir Kanich
-- license GNU General Public License version 2 or later
IF EXISTS (SELECT * FROM sysobjects 
WHERE id = object_id(N'[dbo].[#__jotcache]')
AND type in (N'U'))
DROP TABLE [#__jotcache];
IF EXISTS (SELECT * FROM sysobjects 
WHERE id = object_id(N'[dbo].[#__jotcache_exclude]')
AND type in (N'U'))
DROP TABLE [#__jotcache_exclude];