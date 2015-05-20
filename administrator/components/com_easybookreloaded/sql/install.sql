CREATE TABLE IF NOT EXISTS  `#__easybook` (
  `id` int(10) NOT NULL auto_increment,
  `gbid` int(11) NOT NULL DEFAULT '1',
  `gbip` varchar(15) NOT NULL default '',
  `gbname` varchar(40) NOT NULL default '',
  `gbmail` varchar(60) default NULL,
  `gbmailshow` tinyint(1) NOT NULL default '0',
  `gbloca` varchar(50) default NULL,
  `gbpage` varchar(150) default NULL,
  `gbvote` int(10) default NULL,
  `gbtext` text NOT NULL,
  `gbdate` datetime default NULL,
  `gbtitle` VARCHAR(50) NULL,
  `gbcomment` text,
  `published` tinyint(1) NOT NULL default '0',
  `gbicq` varchar(20) default NULL,
  `gbaim` varchar(50) default NULL,
  `gbmsn` varchar(50) default NULL,
  `gbyah` varchar(50) default NULL,
  `gbskype` varchar(50) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__easybook_gb` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `introtext` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

INSERT IGNORE INTO `#__easybook_gb` (`id`, `title`, `introtext`) VALUES
(1, 'Default Guestbook', '&lt;p&gt;This is the introtext from the guestbook entry!&lt;/p&gt;');

CREATE TABLE IF NOT EXISTS  `#__easybook_badwords` (
  `id` int(10) NOT NULL auto_increment,
  `word` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;