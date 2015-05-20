ALTER TABLE `#__easybook` ADD `gbid` INT NOT NULL DEFAULT '1' AFTER `id` ;

CREATE TABLE IF NOT EXISTS `#__easybook_gb` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `introtext` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

INSERT INTO `#__easybook_gb` (`id`, `title`, `introtext`) VALUES
(1, 'Default Guestbook', '&lt;p&gt;This is the introtext from the guestbook entry!&lt;/p&gt;');