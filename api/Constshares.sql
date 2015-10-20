CREATE TABLE IF NOT EXISTS `constshares` (
  `p_id` int(11) NOT NULL,
  `g_id` int(11) NOT NULL,
  `percentage` float(6,3) NOT NULL DEFAULT '0.000',
  `timeblocks` varchar(6) NOT NULL DEFAULT '0-96',
  PRIMARY KEY (`p_id`,`g_id`,`timeblocks`),
  FOREIGN KEY (`g_id`) REFERENCES `generators` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`p_id`) REFERENCES `constituents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
)

INSERT INTO `wrldc_schedule`.`constshares` (`p_id`, `g_id`, `percentage`, `timeblocks`) VALUES ('6', '4', '0.000', '0-96');