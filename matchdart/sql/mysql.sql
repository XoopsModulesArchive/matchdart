CREATE TABLE matchdart_matches (
	  MatchID int(10) unsigned NOT NULL auto_increment,
	  MatchSeasonID int(10) default NULL,
	  MatchEventID int(10) default NULL,
	  MatchDate date default NULL,
	  MatchHomeID smallint(4) default NULL,
	  MatchAwayID smallint(4) default NULL,
	  MatchHomeBonus int(4) default NULL,
	  MatchAwayBonus int(4) default NULL,
	  MatchHomeBpoints int(4) default NULL,
	  MatchAwayBpoints int(4) default NULL,
	  MatchHomeWinnerID smallint(4) default NULL,
	  MatchHomeLoserID smallint(4) default NULL,
	  MatchAwayWinnerID smallint(4) default NULL,
	  MatchAwayLoserID smallint(4) default NULL,
	  MatchHomeTieID smallint(4) default NULL,
	  MatchAwayTieID smallint(4) default NULL,
	  MatchHomeSets int(4) default NULL,
	  MatchHomeHighfinish int(4) default NULL,
	  MatchAwaySets int(4) default NULL,
	  MatchAwayHighfinish int(4) default NULL,
	  MatchCreated int(12) default NULL,
	  MatchVariant varchar(10) default NULL,
	  MatchInitialScore int(5) default NULL,
	  MatchHome180 int(3) default NULL,
	  MatchAway180 int(3) default NULL,
	  MatchType varchar(20) default NULL,
	  MatchSingleIn tinyint(1) default NULL,
	  MatchDoubleIn tinyint(1) default NULL,
	  MatchSingleOut tinyint(1) default NULL,
	  MatchDoubleOut tinyint(1) default NULL,
	  MatchHomeLegs int(3) default NULL,
	  MatchAwayLegs int(3) default NULL,
	  MatchHomeDarts int(3) default NULL,
	  MatchAwayDarts int(3) default NULL,
	  MatchHomeMatchDarts int(3) default NULL,
	  MatchAwayMatchDarts int(3) default NULL,
	  MatchHomePPT double default NULL,
	  MatchAwayPPT double default NULL,
	  MatchHomePPD double default NULL,
	  MatchAwayPPD double default NULL,
	  MatchPlayerStats int(1) not NULL default -1,
	  PRIMARY KEY  (MatchID),
	  KEY MatchSeasonID (MatchSeasonID),
	  KEY MatchEventID (MatchEventID),
	  KEY MatchHomeID (MatchHomeID),
	  KEY MatchAwayID (MatchAwayID),
	  KEY MatchHomeWinnerID (MatchHomeWinnerID),
	  KEY MatchHomeLoserID (MatchHomeLoserID),
	  KEY MatchAwayWinnerID (MatchAwayWinnerID),
	  KEY MatchAwayLoserID (MatchAwayLoserID),
	  KEY MatchHomeTieID (MatchHomeTieID),
	  KEY MatchAwayTieID (MatchAwayTieID)
	) TYPE=MyISAM;

CREATE TABLE matchdart_players (
	  PlayerID smallint(4) unsigned NOT NULL auto_increment,
	  PlayerUID mediumint(8) default '0',
	  PlayerSeasonID int(10) default NULL,
	  PlayerEventID int(10) default NULL,
	  PlayerName varchar(128) default NULL,
	  PRIMARY KEY  (PlayerID)
	) TYPE=MyISAM;
	
CREATE TABLE matchdart_seasonnames (
	  SeasonID int(10) unsigned NOT NULL auto_increment,
	  SeasonName varchar(64) default NULL,
	  SeasonPublish tinyint(1) default NULL,
	  SeasonLine varchar(32) default NULL,
	  SeasonDefault tinyint(1) default NULL,
	  PRIMARY KEY  (SeasonID)
	) TYPE=MyISAM;
	
INSERT INTO matchdart_seasonnames (SeasonID, SeasonName, SeasonPublish, SeasonLine, SeasonDefault)
	VALUES ('1', '2005', '1', '1', '1');

CREATE TABLE matchdart_eventnames (
	  EventID int(10) unsigned NOT NULL auto_increment,
	  EventName varchar(128) default NULL,
	  EventPublish tinyint(1) default NULL,
	  EventPointsWin int(3) default NULL,
	  EventPointsDraw int(3) default NULL,
	  EventPointsLoss int(3) default NULL,
	  EventLine varchar(32) default NULL,
	  EventDefault tinyint(1) default NULL,
	  EventMatchVariant varchar(10) default NULL,
  	  EventInitialScore int(5) default NULL,
	  EventPlayerStats tinyint(1) default NULL,
	  PRIMARY KEY  (EventID)
	) TYPE=MyISAM;
	
INSERT INTO matchdart_eventnames (EventID, EventName, EventPublish, EventPointsWin, EventPointsDraw, EventPointsLoss, EventLine, EventDefault, EventMatchVariant, EventInitialScore, EventPlayerStats)
	VALUES ('1', 'Premier', '1', '3', '1', '0', '1,2', '1', 'x01', 301, 1);
