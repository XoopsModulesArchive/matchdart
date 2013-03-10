<?php

/*
 *************************************************************
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 * 
 *************************************************************
 * MatchDart by wild0ne <info@matchdart.de>
 * developed & tested with XOOPS 2.0.18
 *************************************************************
 * Version 1.5 | 04-MAR-2008
 *************************************************************
 * READ manual.txt for installing & other help!!!
 * ************************************************************
 */

require('../../mainfile.php');
$xoopsOption['template_main'] = 'xd_stats.html';
require(XOOPS_ROOT_PATH.'/header.php');

include_once(XOOPS_ROOT_PATH.'/modules/matchdart/include/functions.php');
include_once(XOOPS_ROOT_PATH.'/modules/matchdart/include/header.php');

$xoopsTpl->assign('script', 'stats.php');

// get User ID
is_object($xoopsUser) ? $xd_uid = $xoopsUser->getVar('uid') : $xd_uid = -1;

//check for update
if (!xd_checkupdate()){
	$update_msg = "<p style='color:red; font-size:medium; font-weight:bold'>"._XD_UPDATEREQUIRED."</p><br>";
	$xoopsTpl->assign('update_msg', $update_msg);
}

// get module preferences
$xd_print_date = $xoopsModuleConfig['printdate'];
$xd_d_seasonid =  xd_getdefaultseasonid();
$xd_d_eventid =  xd_getdefaulteventid();
$xd_d_displaystats = 1;
$xd_d_playerid = xd_getmyplayerid($xd_uid);

// get select box arrays
$season = xd_getseasons();
$event = xd_getevents();
$player = xd_getplayers();

// Set Session vars
if (!isset($_SESSION['seasonid'])) {
	$_SESSION['seasonid'] = $xd_d_seasonid;
}
if (!isset($_SESSION['eventid'])) {
	$_SESSION['eventid'] = $xd_d_eventid;
}
if (!isset($_SESSION['displaystats'])) {
	$_SESSION['displaystats'] = $xd_d_displaystats;
}
if (!isset($_SESSION['statsplayer']) or $_SESSION['statsplayer'] <= 0) {
	$_SESSION['statsplayer'] = $xd_d_playerid;
}

// check for changes
// check for GO_MOVETO
if (isset($_POST['go_moveto'])) {
	$xd_moveto = $_POST['in_moveto'];
	header("Location: $xd_moveto");
}

//check for GO_SEASON
if (isset($_POST['go_season']) AND $_POST['go_season'] == 1) {
	$_SESSION['seasonid'] = intval($_POST['in_season']);
}

//check for GO_EVENT
if (isset($_POST['go_event']) AND $_POST['go_event'] == 1) {
	$_SESSION['eventid'] = intval($_POST['in_event']);
}

//check for GO_DISPLAYSTATS
if (isset($_POST['go_displaystats']) AND $_POST['go_displaystats'] == 1) {
	$_SESSION['displaystats'] = intval($_POST['in_displaystats']);
}

//check for GO_STATSPLAYER
if (isset($_POST['go_statsplayer']) AND $_POST['go_statsplayer'] == 1) {
	$_SESSION['statsplayer'] = intval($_POST['in_statsplayer']);
}

$seasonid = $_SESSION['seasonid'];
$eventid = $_SESSION['eventid'];
$displaystats = $_SESSION['displaystats'];
$playerid = $_SESSION['statsplayer'];

// calculate stats
if ($seasonid == 0) {
	$seasonselect = '%';
} else {
	$seasonselect = $seasonid;
}
if ($eventid == 0) {
	$eventselect = '%';
} else {
	$eventselect = $eventid;
}

//season/event stats or player stats
if ($displaystats == 1 OR $displaystats == 2) {
	$selwhere = "MatchSeasonID LIKE '$seasonselect' AND MatchEventID LIKE '$eventselect'";
}

$stats = array();

// Home Match Wins
if ($displaystats == 2) {
	$query = $xoopsDB->query("SELECT Count(*) AS homeMatchWins FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE $selwhere AND
	MatchHomeWinnerID = $playerid");
}
else {
	$query = $xoopsDB->query("SELECT Count(*) AS homeMatchWins FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE $selwhere
	AND MatchHomeWinnerID != -1");
}
$statsqry = $xoopsDB->fetchArray($query);
$stats['homeMatchWins'] = $statsqry['homeMatchWins'];
if (!isset($stats['homeMatchWins'])){$stats['homeMatchWins'] = 0;}

// Home Match Loses
if ($displaystats == 2) {
	$query = $xoopsDB->query("SELECT Count(*) AS homeMatchLoses FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE $selwhere AND
	MatchHomeLoserID = $playerid");
} else {
	$query = $xoopsDB->query("SELECT Count(*) AS homeMatchLoses FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE $selwhere AND
	MatchHomeLoserID != -1");
}
$statsqry = $xoopsDB->fetchArray($query);
$stats['homeMatchLoses'] = $statsqry['homeMatchLoses'];
if (!isset($stats['homeMatchLoses'])){$stats['homeMatchLoses'] = 0;}

// Away Match Wins
if ($displaystats == 2) {
	$query = $xoopsDB->query("SELECT Count(*) AS awayMatchWins FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE $selwhere AND
	MatchAwayWinnerID = $playerid");
}
else {
	$query = $xoopsDB->query("SELECT Count(*) AS awayMatchWins FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE $selwhere
	AND MatchAwayWinnerID != -1");
}
$statsqry = $xoopsDB->fetchArray($query);
$stats['awayMatchWins'] = $statsqry['awayMatchWins'];
if (!isset($stats['awayMatchWins'])){$stats['awayMatchWins'] = 0;}

// Away Match Loses
if ($displaystats == 2) {
	$query = $xoopsDB->query("SELECT Count(*) AS awayMatchLoses FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE $selwhere AND
	MatchAwayLoserID = $playerid");
} else {
	$query = $xoopsDB->query("SELECT Count(*) AS awayMatchLoses FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE $selwhere AND
	MatchAwayLoserID != -1");
}
$statsqry = $xoopsDB->fetchArray($query);
$stats['awayMatchLoses'] = $statsqry['awayMatchLoses'];
if (!isset($stats['awayMatchLoses'])){$stats['awayMatchLoses'] = 0;}

// Match Draws
if ($displaystats == 2) {
	//home Match Draws
	$query = $xoopsDB->query("SELECT Count(*) AS homeMatchDraws FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE $selwhere AND
	MatchHomeTieID = $playerid");
	$statsqry = $xoopsDB->fetchArray($query);
	$stats['homeMatchDraws'] = $statsqry['homeMatchDraws'];
	if (!isset($stats['homeMatchDraws'])){$stats['homeMatchDraws'] = 0;}

	//away draws
	$query = $xoopsDB->query("SELECT Count(*) AS awayMatchDraws FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE $selwhere AND
	MatchAwayTieID = $playerid");
	$statsqry = $xoopsDB->fetchArray($query);
	$stats['awayMatchDraws'] = $statsqry['awayMatchDraws'];
	if (!isset($stats['awayMatchDraws'])){$stats['awayMatchDraws'] = 0;}

	//total draws
	$stats['totMatchDraws'] = $stats['homeMatchDraws'] + $stats['awayMatchDraws'];
} else {
	//total draws
	$query = $xoopsDB->query("SELECT Count(*) AS Draws FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE $selwhere AND
	MatchHomeTieID != -1");
	$statsqry = $xoopsDB->fetchArray($query);
	if (!isset($statsqry['Draws'])){$statsqry['Draws'] = 0;}
	// total draws = awayMatchDraws = homeMatchDraws
	$stats['homeMatchDraws'] = $statsqry['Draws'];
	$stats['awayMatchDraws'] =  $statsqry['Draws'];
	$stats['totMatchDraws'] =  $statsqry['Draws'];
}

// Sums Matches
$stats['homeMatches'] = $stats['homeMatchWins'] + $stats['homeMatchLoses'] + $stats['homeMatchDraws'];
$stats['awayMatches'] = $stats['awayMatchWins'] + $stats['awayMatchLoses'] + $stats['awayMatchDraws'];
$stats['totMatchWins'] = $stats['homeMatchWins'] + $stats['awayMatchWins'];
$stats['totMatchLoses'] = $stats['homeMatchLoses'] + $stats['awayMatchLoses'];
if ($displaystats == 2) {
	$stats['totMatches'] = $stats['totMatchWins'] + $stats['totMatchLoses'] + $stats['totMatchDraws'];
} else {
	$stats['totMatches'] = ($stats['totMatchWins'] + $stats['totMatchLoses'])/2 + $stats['totMatchDraws'];
}
// end Matches

// Home Set Wins
if ($displaystats == 2) {
	$query = $xoopsDB->query("SELECT Sum(MatchHomeSets) AS homeSetWins
							FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE $selwhere AND 
	MatchHomeID = $playerid");
}
else {
	$query = $xoopsDB->query("SELECT Sum(MatchHomeSets) AS homeSetWins FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE $selwhere");
}
$statsqry = $xoopsDB->fetchArray($query);
$stats['homeSetWins'] = $statsqry['homeSetWins'];
if (!isset($stats['homeSetWins'])){$stats['homeSetWins'] = 0;}

// Home Set Loses
if ($displaystats == 2) {
	$query = $xoopsDB->query("SELECT Sum(MatchAwaySets) AS homeSetLoses
							FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE $selwhere AND 
	MatchHomeID = $playerid");
} else {
	$query = $xoopsDB->query("SELECT Sum(MatchAwaySets) AS homeSetLoses
							FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE $selwhere");
}
$statsqry = $xoopsDB->fetchArray($query);
$stats['homeSetLoses'] = $statsqry['homeSetLoses'];
if (!isset($stats['homeSetLoses'])){$stats['homeSetLoses'] = 0;}

// Away Set Wins
if ($displaystats == 2) {
	$query = $xoopsDB->query("SELECT Sum(MatchAwaySets) AS awaySetWins
							FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE $selwhere AND 
	MatchAwayID = $playerid");
}
else {
	$query = $xoopsDB->query("SELECT Sum(MatchAwaySets) AS awaySetWins FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE $selwhere");
}
$statsqry = $xoopsDB->fetchArray($query);
$stats['awaySetWins'] = $statsqry['awaySetWins'];
if (!isset($stats['awaySetWins'])){$stats['awaySetWins'] = 0;}

// Away Set Loses
if ($displaystats == 2) {
	$query = $xoopsDB->query("SELECT Sum(MatchHomeSets) AS awaySetLoses
							FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE $selwhere AND 
	MatchAwayID = $playerid");
} else {
	$query = $xoopsDB->query("SELECT Sum(MatchHomeSets) AS awaySetLoses
							FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE $selwhere");
}
$statsqry = $xoopsDB->fetchArray($query);
$stats['awaySetLoses'] = $statsqry['awaySetLoses'];
if (!isset($stats['awaySetLoses'])){$stats['awaySetLoses'] = 0;}

// Total Sets
$stats['totSetWins'] = $stats['homeSetWins'] + $stats['awaySetWins'];
$stats['totSetLoses'] = $stats['homeSetLoses'] + $stats['awaySetLoses'];

if ($displaystats == 2) {
	$stats['homeSets'] = $stats['homeSetWins'] + $stats['homeSetLoses'];
	$stats['awaySets'] = $stats['awaySetWins'] + $stats['awaySetLoses'];
	$stats['totSets'] = $stats['homeSets'] + $stats['awaySets'];
} else {
	$stats['homeSets'] = $stats['homeSetWins'];
	$stats['awaySets'] = $stats['awaySetWins'];
	$stats['totSets'] = $stats['totSetWins'];
}

// end Sets


// Home Leg Wins
if ($displaystats == 2) {
	$query = $xoopsDB->query("SELECT Sum(MatchHomeLegs) AS homeLegWins
							FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE $selwhere AND 
	MatchHomeID = $playerid");
}
else {
	$query = $xoopsDB->query("SELECT Sum(MatchHomeLegs) AS homeLegWins FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE $selwhere");
}
$statsqry = $xoopsDB->fetchArray($query);
$stats['homeLegWins'] = $statsqry['homeLegWins'];
if (!isset($stats['homeLegWins'])){$stats['homeLegWins'] = 0;}

// Home Leg Loses
if ($displaystats == 2) {
	$query = $xoopsDB->query("SELECT Sum(MatchAwayLegs) AS homeLegLoses
							FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE $selwhere AND 
	MatchHomeID = $playerid");
} else {
	$query = $xoopsDB->query("SELECT Sum(MatchAwayLegs) AS homeLegLoses
							FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE $selwhere");
}
$statsqry = $xoopsDB->fetchArray($query);
$stats['homeLegLoses'] = $statsqry['homeLegLoses'];
if (!isset($stats['homeLegLoses'])){$stats['homeLegLoses'] = 0;}

// Away Leg Wins
if ($displaystats == 2) {
	$query = $xoopsDB->query("SELECT Sum(MatchAwayLegs) AS awayLegWins
							FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE $selwhere AND 
	MatchAwayID = $playerid");
}
else {
	$query = $xoopsDB->query("SELECT Sum(MatchAwayLegs) AS awayLegWins FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE $selwhere");
}
$statsqry = $xoopsDB->fetchArray($query);
$stats['awayLegWins'] = $statsqry['awayLegWins'];
if (!isset($stats['awayLegWins'])){$stats['awayLegWins'] = 0;}

// Away Leg Loses
if ($displaystats == 2) {
	$query = $xoopsDB->query("SELECT Sum(MatchHomeLegs) AS awayLegLoses
							FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE $selwhere AND 
	MatchAwayID = $playerid");
} else {
	$query = $xoopsDB->query("SELECT Sum(MatchHomeLegs) AS awayLegLoses
							FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE $selwhere");
}
$statsqry = $xoopsDB->fetchArray($query);
$stats['awayLegLoses'] = $statsqry['awayLegLoses'];
if (!isset($stats['awayLegLoses'])){$stats['awayLegLoses'] = 0;}

// Total Legs
$stats['totLegWins'] = $stats['homeLegWins'] + $stats['awayLegWins'];
$stats['totLegLoses'] = $stats['homeLegLoses'] + $stats['awayLegLoses'];

if ($displaystats == 2) {
	$stats['homeLegs'] = $stats['homeLegWins'] + $stats['homeLegLoses'];
	$stats['awayLegs'] = $stats['awayLegWins'] + $stats['awayLegLoses'];
	$stats['totLegs'] = $stats['homeLegs'] + $stats['awayLegs'];
} else {
	$stats['homeLegs'] = $stats['homeLegWins'];
	$stats['awayLegs'] = $stats['awayLegWins'];
	$stats['totLegs'] = $stats['totLegWins'];
}
// end Legs

// Extended Player Stats
$query = $xoopsDB->query("SELECT Count(*) AS countPlayerStats
							FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE MatchPlayerStats != -1 AND $selwhere");
$statsqry = $xoopsDB->fetchArray($query);
$stats['PlayerStats'] = $statsqry['countPlayerStats'];

if ($stats['PlayerStats'] != 0) {

	// home Darts
	if ($displaystats == 2) {
		$query = $xoopsDB->query("SELECT Sum(MatchHomeDarts) AS homeDarts
							FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE MatchPlayerStats = 1 AND $selwhere AND 
		MatchHomeID = $playerid");
	} else {
		$query = $xoopsDB->query("SELECT Sum(MatchHomeDarts) AS homeDarts
							FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE MatchPlayerStats = 1 AND $selwhere");
	}
	$statsqry = $xoopsDB->fetchArray($query);
	$stats['homeDarts'] = $statsqry['homeDarts'];
	if (!isset($stats['homeDarts'])){$stats['homeDarts'] = 0;}

	// home MatchDarts
	if ($displaystats == 2) {
		$query = $xoopsDB->query("SELECT Sum(MatchHomeMatchDarts) AS homeMatchDarts
							FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE MatchPlayerStats = 1 AND $selwhere AND 
		MatchHomeID = $playerid");
	} else {
		$query = $xoopsDB->query("SELECT Sum(MatchHomeMatchDarts) AS homeMatchDarts
							FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE MatchPlayerStats = 1 AND $selwhere");
	}
	$statsqry = $xoopsDB->fetchArray($query);
	$stats['homeMatchDarts'] = $statsqry['homeMatchDarts'];
	if (!isset($stats['homeMatchDarts'])){$stats['homeMatchDarts'] = 0;}

	// away Darts
	if ($displaystats == 2) {
		$query = $xoopsDB->query("SELECT Sum(MatchAwayDarts) AS awayDarts
							FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE MatchPlayerStats = 1 AND $selwhere AND 
		MatchAwayID = $playerid");
	} else {
		$query = $xoopsDB->query("SELECT Sum(MatchAwayDarts) AS awayDarts
							FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE MatchPlayerStats = 1 AND $selwhere");
	}
	$statsqry = $xoopsDB->fetchArray($query);
	$stats['awayDarts'] = $statsqry['awayDarts'];
	if (!isset($stats['awayDarts'])){$stats['awayDarts'] = 0;}

	// away MatchDarts
	if ($displaystats == 2) {
		$query = $xoopsDB->query("SELECT Sum(MatchAwayMatchDarts) AS awayMatchDarts
							FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE MatchPlayerStats = 1 AND $selwhere AND 
		MatchAwayID = $playerid");
	} else {
		$query = $xoopsDB->query("SELECT Sum(MatchAwayMatchDarts) AS awayMatchDarts
							FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE MatchPlayerStats = 1 AND $selwhere");
	}
	$statsqry = $xoopsDB->fetchArray($query);
	$stats['awayMatchDarts'] = $statsqry['awayMatchDarts'];
	if (!isset($stats['awayMatchDarts'])){$stats['awayMatchDarts'] = 0;}

	// total Darts
	$stats['totDarts'] = $stats['homeDarts'] + $stats['awayDarts'];

	// total MatchDarts
	$stats['totMatchDarts'] = $stats['homeMatchDarts'] + $stats['awayMatchDarts'];

	// home PPT
	if ($displaystats == 2) {
		$query = $xoopsDB->query("SELECT Avg(MatchHomePPT) AS homeAvgPpt
							FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE MatchPlayerStats = 1 AND $selwhere AND 
		MatchHomeID = $playerid");
	} else {
		$query = $xoopsDB->query("SELECT Avg(MatchHomePPT) AS homeAvgPpt
							FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE MatchPlayerStats = 1 AND $selwhere");
	}
	$statsqry = $xoopsDB->fetchArray($query);
	$stats['homeAvgPpt'] = number_format(round(($statsqry['homeAvgPpt']),2),2,'.',',');
	if (!isset($stats['homeAvgPpt'])){$stats['homeAvgPpt'] = 0;}

	// away PPT
	if ($displaystats == 2) {
		$query = $xoopsDB->query("SELECT Avg(MatchAwayPPT) AS awayAvgPpt
							FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE MatchPlayerStats = 1 AND $selwhere AND 
		MatchAwayID = $playerid");
	} else {
		$query = $xoopsDB->query("SELECT Avg(MatchAwayPPT) AS awayAvgPpt
							FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE MatchPlayerStats = 1 AND $selwhere");
	}
	$statsqry = $xoopsDB->fetchArray($query);
	$stats['awayAvgPpt'] = number_format(round(($statsqry['awayAvgPpt']),2),2,'.',',');
	if (!isset($stats['awayAvgPpt'])){$stats['awayAvgPpt'] = 0;}

	// total PPT
	$stats['totAvgPpt'] = number_format(round((($stats['homeAvgPpt'] * $stats['homeMatches'] + $stats['awayAvgPpt'] * $stats['awayMatches'])/($stats['homeMatches'] + $stats['awayMatches'])),2),2,'.',',');

	// home PPD
	if ($displaystats == 2) {
		$query = $xoopsDB->query("SELECT Avg(MatchHomePPD) AS homeAvgPpd
							FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE MatchPlayerStats = 1 AND $selwhere AND 
		MatchHomeID = $playerid");
	} else {
		$query = $xoopsDB->query("SELECT Avg(MatchHomePPD) AS homeAvgPpd
							FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE MatchPlayerStats = 1 AND $selwhere");
	}
	$statsqry = $xoopsDB->fetchArray($query);
	$stats['homeAvgPpd'] = number_format(round(($statsqry['homeAvgPpd']),2),2,'.',',');
	if (!isset($stats['homeAvgPpd'])){$stats['homeAvgPpd'] = 0;}

	// away PPD
	if ($displaystats == 2) {
		$query = $xoopsDB->query("SELECT Avg(MatchAwayPPD) AS awayAvgPpd
							FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE MatchPlayerStats = 1 AND $selwhere AND 
		MatchAwayID = $playerid");
	} else {
		$query = $xoopsDB->query("SELECT Avg(MatchAwayPPD) AS awayAvgPpd
							FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE MatchPlayerStats = 1 AND $selwhere");
	}
	$statsqry = $xoopsDB->fetchArray($query);
	$stats['awayAvgPpd'] = number_format(round(($statsqry['awayAvgPpd']),2),2,'.',',');
	if (!isset($stats['awayAvgPpd'])){$stats['awayAvgPpd'] = 0;}

	// total PPD
	$stats['totAvgPpd'] = number_format(round((($stats['homeAvgPpd'] * $stats['homeMatches'] + $stats['awayAvgPpd'] * $stats['awayMatches'])/($stats['homeMatches'] + $stats['awayMatches'])),2),2,'.',',');
}
// Home 180
if ($displaystats == 2) {
	$query = $xoopsDB->query("SELECT Sum(MatchHome180) AS home180
							FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE $selwhere AND 
	MatchHomeID = $playerid");
}
else {
	$query = $xoopsDB->query("SELECT Sum(MatchHome180) AS home180 FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE $selwhere");
}
$statsqry = $xoopsDB->fetchArray($query);
$stats['home180'] = $statsqry['home180'];
if (!isset($stats['home180'])){$stats['home180'] = 0;}

// Away 180 scored
if ($displaystats == 2)
{
	$query = $xoopsDB->query("SELECT Sum(MatchAway180) AS away180
							FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE $selwhere AND 
	MatchAwayID = $playerid");
}
else {
	$query = $xoopsDB->query("SELECT Sum(MatchAway180) AS away180 FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE $selwhere");
}
$statsqry = $xoopsDB->fetchArray($query);
$stats['away180'] = $statsqry['away180'];
if (!isset($stats['away180'])){$stats['away180'] = 0;}

// Total 180
$stats['tot180'] = $stats['home180'] + $stats['away180'];

// Home Highfinish
if ($displaystats == 2) {
	$query = $xoopsDB->query("SELECT
	MatchHomeHighfinish AS homeHighfinish,
	P.PlayerName AS homeHighfinishPlayer,
	DATE_FORMAT(MatchDate, '$xd_print_date') AS homeHighfinishDate,
	O.PlayerName as homeHighfinishOpponent
	FROM ".$xoopsDB->prefix("matchdart_matches").",".$xoopsDB->prefix("matchdart_players")." P,".$xoopsDB->prefix("matchdart_players")." O
	WHERE MatchHomeID = $playerid AND
	P.PlayerID = MatchHomeID AND
	O.PlayerID = MatchAwayID AND
	$selwhere AND
	MatchHomeHighfinish != 0
	ORDER BY MatchHomeHighfinish DESC, MatchDate DESC, MatchID DESC LIMIT 1
					");
}
else {
	$query = $xoopsDB->query("SELECT
	MatchHomeHighfinish AS homeHighfinish,
	DATE_FORMAT(MatchDate, '$xd_print_date') AS homeHighfinishDate,
	PlayerName AS homeHighfinishPlayer
	FROM ".$xoopsDB->prefix("matchdart_matches").",".$xoopsDB->prefix("matchdart_players")."
	WHERE $selwhere AND
	PlayerID = MatchHomeID AND
	MatchHomeHighfinish != 0
	ORDER BY MatchHomeHighfinish DESC, MatchDate DESC, MatchID DESC LIMIT 1
					");
}
$statsqry = $xoopsDB->fetchArray($query);
$stats['homeHighfinish'] = $statsqry['homeHighfinish'];
$stats['homeHighfinishPlayer'] = $statsqry['homeHighfinishPlayer'];
$stats['homeHighfinishDate'] = $statsqry['homeHighfinishDate'];
$stats['homeHighfinishOpponent'] = 0;
if ($displaystats == 2) {
	$stats['homeHighfinishOpponent'] = $statsqry['homeHighfinishOpponent'];
}
if (!isset($stats['homeHighfinish'])){$stats['homeHighfinish'] = 0;}

// Away highest finish
if ($displaystats == 2) {
	$query = $xoopsDB->query("SELECT
	MatchAwayHighfinish AS awayHighfinish,
	P.PlayerName AS awayHighfinishPlayer,
	DATE_FORMAT(MatchDate, '$xd_print_date') AS awayHighfinishDate,
	O.PlayerName as awayHighfinishOpponent
	FROM ".$xoopsDB->prefix("matchdart_matches").",".$xoopsDB->prefix("matchdart_players")." P,".$xoopsDB->prefix("matchdart_players")." O
	WHERE MatchAwayID = $playerid AND
	P.PlayerID = MatchAwayID AND
	O.PlayerID = MatchHomeID AND
	$selwhere AND
	MatchAwayHighfinish != 0
	ORDER BY MatchAwayHighfinish DESC, MatchDate DESC, MatchID DESC LIMIT 1
					");
}
else {
	$query = $xoopsDB->query("SELECT
	MatchAwayHighfinish AS awayHighfinish,
	DATE_FORMAT(MatchDate, '$xd_print_date') AS awayHighfinishDate,
	PlayerName AS awayHighfinishPlayer
	FROM ".$xoopsDB->prefix("matchdart_matches").",".$xoopsDB->prefix("matchdart_players")."
	WHERE $selwhere AND
	PlayerID = MatchAwayID AND
	MatchAwayHighfinish != 0
	ORDER BY MatchAwayHighfinish DESC, MatchDate DESC, MatchID DESC LIMIT 1
					");
}
$statsqry = $xoopsDB->fetchArray($query);
$stats['awayHighfinish'] = $statsqry['awayHighfinish'];
$stats['awayHighfinishPlayer'] = $statsqry['awayHighfinishPlayer'];
$stats['awayHighfinishDate'] = $statsqry['awayHighfinishDate'];
$stats['awayHighfinishOpponent'] = 0;
if ($displaystats == 2) {
	$stats['awayHighfinishOpponent'] = $statsqry['awayHighfinishOpponent'];
}
if (!isset($stats['awayHighfinish'])){$stats['awayHighfinish'] = 0;}

// Total highest finish
if ($stats['homeHighfinish'] >= $stats['awayHighfinish']) {
	$stats['totHighfinish'] = $stats['homeHighfinish'];
	$stats['totHighfinishPlayer'] = $stats['homeHighfinishPlayer'];
	$stats['totHighfinishDate'] = $stats['homeHighfinishDate'];
	$stats['totHighfinishOpponent'] = $stats['homeHighfinishOpponent'];
} else {
	$stats['totHighfinish'] = $stats['awayHighfinish'];
	$stats['totHighfinishPlayer'] = $stats['awayHighfinishPlayer'];
	$stats['totHighfinishDate'] = $stats['awayHighfinishDate'];
	$stats['totHighfinishOpponent'] = $stats['awayHighfinishOpponent'];
}
// end Highfinish

// Averages
//reminder: number_format(round($value,1),$dec_places,$dec_sep,$thou_sep);
if ($stats['totMatches'] != 0) {
	$stats['totAvgMatchWins'] = number_format(round(($stats['totMatchWins']*100/$stats['totMatches']),2),0,'.',',');
	$stats['totAvgMatchDraws'] = number_format(round(($stats['totMatchDraws']*100/$stats['totMatches']),2),0,'.',',');
	$stats['totAvgMatchLoses'] = number_format(round(($stats['totMatchLoses']*100/$stats['totMatches']),2),0,'.',',');
	$stats['totAvgSets'] = number_format(round(($stats['totSets']/$stats['totMatches']),2),2,'.',',');
	$stats['totAvgSetWins'] = number_format(round(($stats['totSetWins']/$stats['totMatches']),2),2,'.',',');
	$stats['totAvgSetLoses'] = number_format(round(($stats['totSetLoses']/$stats['totMatches']),2),2,'.',',');
	$stats['totAvgLegs'] = number_format(round(($stats['totLegs']/$stats['totMatches']),2),2,'.',',');
	$stats['totAvgLegWins'] = number_format(round(($stats['totLegWins']/$stats['totMatches']),2),2,'.',',');
	$stats['totAvgLegLoses'] = number_format(round(($stats['totLegLoses']/$stats['totMatches']),2),2,'.',',');
	$stats['totAvg180'] = number_format(round(($stats['tot180']/$stats['totMatches']),2),2,'.',',');
}
if ($stats['homeMatches'] != 0) {
	$stats['homeAvgMatchWins'] = number_format(round(($stats['homeMatchWins']*100/$stats['homeMatches']),2),0,'.',',');
	$stats['homeAvgMatchDraws'] = number_format(round(($stats['homeMatchDraws']*100/$stats['homeMatches']),2),0,'.',',');
	$stats['homeAvgMatchLoses'] = number_format(round(($stats['homeMatchLoses']*100/$stats['homeMatches']),2),0,'.',',');
	$stats['homeAvgSets'] = number_format(round(($stats['homeSets']/$stats['homeMatches']),2),2,'.',',');
	$stats['homeAvgSetWins'] = number_format(round(($stats['homeSetWins']/$stats['homeMatches']),2),2,'.',',');
	$stats['homeAvgSetLoses'] = number_format(round(($stats['homeSetLoses']/$stats['homeMatches']),2),2,'.',',');
	$stats['homeAvgLegs'] = number_format(round(($stats['homeLegs']/$stats['homeMatches']),2),2,'.',',');
	$stats['homeAvgLegWins'] = number_format(round(($stats['homeLegWins']/$stats['homeMatches']),2),2,'.',',');
	$stats['homeAvgLegLoses'] = number_format(round(($stats['homeLegLoses']/$stats['homeMatches']),2),2,'.',',');
	$stats['homeAvg180'] = number_format(round(($stats['home180']/$stats['homeMatches']),2),2,'.',',');
}
if ($stats['awayMatches'] != 0) {
	$stats['awayAvgMatchWins'] = number_format(round(($stats['awayMatchWins']*100/$stats['awayMatches']),2),0,'.',',');
	$stats['awayAvgMatchDraws'] = number_format(round(($stats['awayMatchDraws']*100/$stats['awayMatches']),2),0,'.',',');
	$stats['awayAvgMatchLoses'] = number_format(round(($stats['awayMatchLoses']*100/$stats['awayMatches']),2),0,'.',',');
	$stats['awayAvgSets'] = number_format(round(($stats['awaySets']/$stats['awayMatches']),2),2,'.',',');
	$stats['awayAvgSetWins'] = number_format(round(($stats['awaySetWins']/$stats['awayMatches']),2),2,'.',',');
	$stats['awayAvgSetLoses'] = number_format(round(($stats['awaySetLoses']/$stats['awayMatches']),2),2,'.',',');
	$stats['awayAvgLegs'] = number_format(round(($stats['awayLegs']/$stats['awayMatches']),2),2,'.',',');
	$stats['awayAvgLegWins'] = number_format(round(($stats['awayLegWins']/$stats['awayMatches']),2),2,'.',',');
	$stats['awayAvgLegLoses'] = number_format(round(($stats['awayLegLoses']/$stats['awayMatches']),2),2,'.',',');
	$stats['awayAvg180'] = number_format(round(($stats['away180']/$stats['awayMatches']),2),2,'.',',');
}
if ($stats['PlayerStats'] != 0) {
	if ($stats['totMatches'] != 0) {
		$stats['totAvgDarts'] = number_format(round(($stats['totDarts']/$stats['totMatches']),2),2,'.',',');
		$stats['totAvgMatchDarts'] = number_format(round(($stats['totMatchDarts']/$stats['totMatches']),2),2,'.',',');
	}
	if ($stats['homeMatches'] != 0) {
		$stats['homeAvgDarts'] = number_format(round(($stats['homeDarts']/$stats['homeMatches']),2),2,'.',',');
		$stats['homeAvgMatchDarts'] = number_format(round(($stats['homeMatchDarts']/$stats['homeMatches']),2),2,'.',',');
	}
	if ($stats['awayMatches'] != 0) {
		$stats['awayAvgDarts'] = number_format(round(($stats['awayDarts']/$stats['awayMatches']),2),2,'.',',');
		$stats['awayAvgMatchDarts'] = number_format(round(($stats['awayMatchDarts']/$stats['awayMatches']),2),2,'.',',');
	}
}


// assign data to templates
// assign select box data
$xoopsTpl->assign('seasonid', $seasonid);
$xoopsTpl->assign('season', $season);
$xoopsTpl->assign('eventid', $eventid);
$xoopsTpl->assign('event', $event);
$xoopsTpl->assign('playerid', $playerid);
$xoopsTpl->assign('player', $player);
$xoopsTpl->assign('displaystats', $displaystats);
// assign stats data
$xoopsTpl->assign('stats', $stats);

include(XOOPS_ROOT_PATH.'/footer.php');
?>