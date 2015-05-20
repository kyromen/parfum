-- version 5.1.0
-- package JotCache
-- category Joomla 3.4
-- copyright (C) 2010-2015 Vladimir Kanich
-- license GNU General Public License version 2 or later
IF NOT EXISTS (SELECT * FROM sysobjects 
WHERE id = object_id(N'[dbo].[#__jotcache]')
AND type in (N'U'))
CREATE TABLE [#__jotcache] (
  [fname] nvarchar(76) NOT NULL,
  [domain] nvarchar(255) NOT NULL,
  [com] nvarchar(100) NOT NULL,
  [view] nvarchar(100) NOT NULL,
  [id] bigint NOT NULL,
  [ftime] datetime NOT NULL,
  [mark] tinyint,
  [recache] tinyint,
  [recache_chck] tinyint,
  [agent] tinyint,
  [checked_out] bigint NOT NULL,
  [title] nvarchar(255) NOT NULL,
  [uri] nvarchar(2000) NOT NULL,
  [language] nvarchar(5) NOT NULL,
  [browser] nvarchar(50) NOT NULL,
  [qs] nvarchar(2000) NOT NULL,
  [cookies] nvarchar(2000) NOT NULL,
  CONSTRAINT [PK_#__jotcache_fname] PRIMARY KEY CLUSTERED (
	[fname] ASC
));
IF NOT EXISTS (SELECT * FROM sysobjects 
WHERE id = object_id(N'[dbo].[#__jotcache_exclude]')
AND type in (N'U'))
CREATE TABLE [#__jotcache_exclude] (
  [id] int IDENTITY(1,1) NOT NULL,
  [name] nvarchar(64) NOT NULL,
  [value] nvarchar(2000) NOT NULL,
  [type] tinyint,
CONSTRAINT [PK_#__jotcache_exclude_id] PRIMARY KEY CLUSTERED (
	[id] ASC
));