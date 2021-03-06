CREATE TABLE IF NOT EXISTS `revisions` (
  `date` date NOT NULL DEFAULT '2015-11-10',
  `id` int(4) NOT NULL,
  `g_id` int(11) NOT NULL,
  `p_id` int(11) NOT NULL,
  `from_b` int(4) NOT NULL DEFAULT '0',
  `to_b` int(4) NOT NULL DEFAULT '96',
  `cat` varchar(4) NOT NULL,
  `val` varchar(30) NOT NULL DEFAULT '0',
  PRIMARY KEY (`date`,`id`, `g_id`, `p_id`, `to_b`, `cat`),
  FOREIGN KEY (`g_id`) REFERENCES `generators` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`p_id`) REFERENCES `constituents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
)