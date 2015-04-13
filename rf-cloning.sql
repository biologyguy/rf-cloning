SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

CREATE TABLE IF NOT EXISTS `plasmids` (
  `plasmid_id` int(11) NOT NULL AUTO_INCREMENT,
  `plasmid_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `sequence` text COLLATE utf8_unicode_ci NOT NULL,
  `plasmid_size` int(11) NOT NULL,
  `svg_vector_map` blob,
  `savvy_markers` text COLLATE utf8_unicode_ci,
  `savvy_enzymes` text COLLATE utf8_unicode_ci,
  `savvy_MCS` text COLLATE utf8_unicode_ci,
  `popularity` int(11) NOT NULL,
  `checksum` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `privacy` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`plasmid_id`),
  UNIQUE KEY `plasmid_user` (`plasmid_name`,`user_id`),
  KEY `popularity` (`popularity`),
  KEY `user_id` (`user_id`),
  KEY `plasmid_name` (`plasmid_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `projects` (
  `plasmid_id` int(11) NOT NULL AUTO_INCREMENT,
  `plasmid_name` text COLLATE utf8_unicode_ci,
  `insert_name` text COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `backbone_id` int(11) DEFAULT NULL,
  `backbone_database` text COLLATE utf8_unicode_ci NOT NULL,
  `sequence` text COLLATE utf8_unicode_ci NOT NULL,
  `plasmid_sequence` text COLLATE utf8_unicode_ci NOT NULL,
  `insert_sequence` text COLLATE utf8_unicode_ci NOT NULL,
  `insert_sites` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `fwd_primer` varchar(201) COLLATE utf8_unicode_ci NOT NULL,
  `rev_primer` varchar(201) COLLATE utf8_unicode_ci NOT NULL,
  `new_size` int(11) NOT NULL,
  `notes` text COLLATE utf8_unicode_ci,
  `savvy_markers` text COLLATE utf8_unicode_ci NOT NULL,
  `savvy_enzymes` text COLLATE utf8_unicode_ci NOT NULL,
  `savvy_meta` text COLLATE utf8_unicode_ci NOT NULL,
  `complete` tinyint(1) NOT NULL DEFAULT '0',
  `checksum` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `proj_hash` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`plasmid_id`),
  UNIQUE KEY `proj_hash` (`proj_hash`),
  KEY `user_id` (`user_id`),
  KEY `complete` (`complete`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `login` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `session_check` text COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`),
  KEY `login` (`login`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;


INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `login`, `password`, `email`, `session_check`) VALUES
(1, 'Auto', '', 'root', '', '', '');
