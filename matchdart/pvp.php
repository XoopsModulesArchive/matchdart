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
$xoopsOption['template_main'] = 'xd_pvp.html';
require(XOOPS_ROOT_PATH.'/header.php');

include_once(XOOPS_ROOT_PATH.'/modules/matchdart/include/functions.php');
include_once(XOOPS_ROOT_PATH.'/modules/matchdart/include/header.php');

$xoopsTpl->assign('script', 'pvp.php');

// get User ID
is_object($xoopsUser) ? $xd_uid = $xoopsUser->getVar('uid') : $xd_uid = -1;

//check for update
if (!xd_checkupdate()){
	$update_msg = "<p style='color:red; font-size:medium; font-weight:bold'>"._XD_UPDATEREQUIRED."</p><br>";
	$xoopsTpl->assign('update_msg', $update_msg);
}

// get module preferences
$xd_print_date = $xoopsModuleConfig['printdate'];
$xd_d_seasonid = '0'; // PvP stats default = ALL Seasons
$xd_d_eventid =  '0'; // PvP stats default = ALL Events
$xd_d_pvpplayer1 = xd_getmyplayerid($xd_uid);
$xd_d_pvpplayer2 = xd_getmyplayerid('pvpp2');

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
if (!isset($_SESSION['pvpplayer1']) or $_SESSION['pvpplayer1'] <= 0) {
	$_SESSION['pvpplayer1'] = $xd_d_pvpplayer1;
}
if (!isset($_SESSION['pvpplayer2']) or $_SESSION['pvpplayer2'] <= 0) {
	$_SESSION['pvpplayer2'] = $xd_d_pvpplayer2;
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

//check for GO_PVPPLAYER1
if (isset($_POST['go_pvpplayer1']) AND $_POST['go_pvpplayer1'] == 1) {
	$_SESSION['pvpplayer1'] = intval($_POST['in_pvpplayer1']);
}

//check for GO_PVPPLAYER2
if (isset($_POST['go_pvpplayer2']) AND $_POST['go_pvpplayer2'] == 1) {
	$_SESSION['pvpplayer2'] = intval($_POST['in_pvpplayer2']);
}

$seasonid = $_SESSION['seasonid'];
$eventid = $_SESSION['eventid'];
$pvpplayer1 = $_SESSION['pvpplayer1'];
$pvpplayer2 = $_SESSION['pvpplayer2'];

// end check for changes

// get names
$p1pvpname = getplayername($pvpplayer1);
$p2pvpname = getplayername($pvpplayer2);

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

// get pvp stats
$p1pvpstats = getpvpstats($pvpplayer1, $pvpplayer2, $seasonselect, $eventselect);
$p2pvpstats = getpvpstats($pvpplayer2, $pvpplayer1, $seasonselect, $eventselect);

// get player overall stats
$p1stats = getplayerstats($pvpplayer1, $seasonselect, $eventselect);
$p2stats = getplayerstats($pvpplayer2, $seasonselect, $eventselect);

// get last 5 matches P1vP2
$pvpmatch = Array();
if ($p1pvpstats['matches'] != -1) {
	$query = $xoopsDB->query("SELECT
		 DATE_FORMAT(MatchDate, '$xd_print_date') AS matchdate,
		 PH.PlayerName AS homeplayer,
		 PA.PlayerName AS awayplayer,
		 MatchHomeSets AS homesets,
		 MatchAwaySets AS awaysets,
		 SeasonName AS seasonname,
		 EventName AS eventname
		 FROM ".$xoopsDB->prefix("matchdart_matches").",
		 ".$xoopsDB->prefix("matchdart_players")." PH,
		 ".$xoopsDB->prefix("matchdart_players")." PA,
		 ".$xoopsDB->prefix("matchdart_seasonnames").",
		 ".$xoopsDB->prefix("matchdart_eventnames")."
		 WHERE
		 SeasonID = MatchSeasonID AND 
		 EventID = MatchEventID AND
		 (PH.PlayerID = MatchHomeID AND PA.PlayerID = MatchAwayID) AND
		 ((MatchHomeID = $pvpplayer1 AND MatchAwayID = $pvpplayer2) OR 
		 (MatchHomeID = $pvpplayer2 AND MatchAwayID = $pvpplayer1)) AND
		 (MatchSeasonID LIKE '$seasonselect' AND MatchEventID LIKE '$eventselect')
		 LIMIT 5");

	$q = 1;
	while($pvpmatchqry = $xoopsDB->fetchArray($query)) {
		$pvpmatch[$q]['date'] = $pvpmatchqry['matchdate'];
		$pvpmatch[$q]['homeplayer'] = $pvpmatchqry['homeplayer'];
		$pvpmatch[$q]['awayplayer'] = $pvpmatchqry['awayplayer'];
		$pvpmatch[$q]['homesets'] = $pvpmatchqry['homesets'];
		$pvpmatch[$q]['awaysets'] = $pvpmatchqry['awaysets'];
		$pvpmatch[$q]['seasonname'] = $pvpmatchqry['seasonname'];
		$pvpmatch[$q]['eventname'] = $pvpmatchqry['eventname'];
	$q++;
	}
}

// assign data to templates
// assign select box data
$xoopsTpl->assign('seasonid', $seasonid);
$xoopsTpl->assign('season', $season);
$xoopsTpl->assign('eventid', $eventid);
$xoopsTpl->assign('event', $event);
$xoopsTpl->assign('pvpplayer1', $pvpplayer1);
$xoopsTpl->assign('p1pvpname', $p1pvpname);
$xoopsTpl->assign('pvpplayer2', $pvpplayer2);
$xoopsTpl->assign('p2pvpname', $p2pvpname);
$xoopsTpl->assign('player', $player);
// assign pvp data
$xoopsTpl->assign('pvpmatch', $pvpmatch);
$xoopsTpl->assign('p1pvpstats', $p1pvpstats);
$xoopsTpl->assign('p2pvpstats', $p2pvpstats);
// assign stats data
$xoopsTpl->assign('p1stats', $p1stats);
$xoopsTpl->assign('p2stats', $p2stats);

// get the pvp stats
function getpvpstats($p1id, $p2id, $seasonselect, $eventselect) {
	Global $xoopsDB, $xd_print_date;
	
	$pvpplayer1 = $p1id;
	$pvpplayer2 = $p2id;

	$selwhere = " WHERE MatchSeasonID LIKE '$seasonselect' AND 
	MatchEventID LIKE '$eventselect' AND
	((MatchHomeID = $pvpplayer1 AND MatchAwayID = $pvpplayer2) OR
	(MatchHomeID = $pvpplayer2 AND MatchAwayID = $pvpplayer1))";
	
	$pvpstats = array();
	//declaration
	$pvpstats['pvphomewin'] = 0;
	$pvpstats['pvphomelose'] = 0;
	$pvpstats['pvphomedraw'] = 0;
	$pvpstats['pvphomesets'] = 0;
	$pvpstats['pvphomesetslose'] = 0;
	$pvpstats['pvpawaywin'] = 0;
	$pvpstats['pvpawaylose'] = 0;
	$pvpstats['pvpawaydraw'] = 0;
	$pvpstats['pvpawaysets'] = 0;
	$pvpstats['pvpawaysetslose'] = 0;
	$pvpstats['pvphomehighfinish'] = 0;
	$pvpstats['pvpawayhighfinish'] = 0;
	$pvpstats['pvphome180'] = 0;
	$pvpstats['pvpaway180'] = 0;
	$pvpstats['pvptot180'] = 0;

	$pvp_nomatches = 0;

	// Player 1 vs Player 2
	$query = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("matchdart_matches").$selwhere);

	if($xoopsDB->getRowsNum($query) < 1)
	{
		$pvpstats['matches'] = -1;
		//Return $pvpstats;
	}
	else
	{
		$pvpstats['matches'] = $xoopsDB->getRowsNum($query);
		
		while($statsqry = $xoopsDB->fetchArray($query))
		{
			//Player 1 PvP Home Win
			if ($statsqry['MatchHomeWinnerID'] == $pvpplayer1) {
				//echo "homewin p1";
				$pvpstats['pvphomewin']++;
			}
			//Player 1 PvP Home Lose
			if ($statsqry['MatchHomeLoserID'] == $pvpplayer1) {
				//echo "homelose p1";
				$pvpstats['pvphomelose']++;
			}
			//Player 1 PvP Home Draw
			if ($statsqry['MatchHomeTieID'] == $pvpplayer1) {
				//echo "homedraw p1";
				$pvpstats['pvphomedraw']++;
			}
			//Player 1 PvP Away Win
			if ($statsqry['MatchAwayWinnerID'] == $pvpplayer1) {
				$pvpstats['pvpawaywin']++;
			}
			//Player 1 PvP Away Lose
			if ($statsqry['MatchAwayLoserID'] == $pvpplayer1) {
				//echo "homelose p2";
				$pvpstats['pvpawaylose']++;
			}
			//Player 1 PvP Away Draw
			if ($statsqry['MatchAwayTieID'] == $pvpplayer1) {
				//echo "homedraw p2";
				$pvpstats['pvpawaydraw']++;
			}
			//Player 1 PvP Home Match
			if ($statsqry['MatchHomeID'] == $pvpplayer1) {
				//echo "homewin p1";
				$pvpstats['pvphomesets'] = $pvpstats['pvphomesets'] + $statsqry['MatchHomeSets'];
				$pvpstats['pvphomesetslose'] = $pvpstats['pvphomesetslose'] + $statsqry['MatchAwaySets'];
				$pvpstats['pvphomehighfinish'] = $statsqry['MatchHomeHighfinish'] >= $pvpstats['pvphomehighfinish'] ? $statsqry['MatchHomeHighfinish'] : $pvpstats['pvphomehighfinish'];
				$pvpstats['pvphome180'] = $pvpstats['pvphome180'] + $statsqry['MatchHome180'];
			}
			//Player 1 PvP Away Match
			if ($statsqry['MatchAwayID'] == $pvpplayer1) {
				//echo "homewin p1";
				$pvpstats['pvpawaysets'] = $pvpstats['pvpawaysets'] + $statsqry['MatchAwaySets'];
				$pvpstats['pvpawaysetslose'] = $pvpstats['pvpawaysetslose'] + $statsqry['MatchHomeSets'];
				$pvpstats['pvpawayhighfinish'] = $statsqry['MatchAwayHighfinish'] >= $pvpstats['pvpawayhighfinish'] ? $statsqry['MatchAwayHighfinish'] : $pvpstats['pvpawayhighfinish'];
				$pvpstats['pvpway180'] = $pvpstats['pvpaway180'] + $statsqry['MatchAway180'];
			}
			//$q++;
		} // end while statsqry
		//Sums etc.
		$pvpstats['pvphomematches'] = $pvpstats['pvphomewin'] + $pvpstats['pvphomelose'] + $pvpstats['pvphomedraw'];
		$pvpstats['pvpawaymatches'] = $pvpstats['pvpawaywin'] + $pvpstats['pvpawaylose'] + $pvpstats['pvpawaydraw'];

		$pvpstats['pvptotmatches'] = $pvpstats['pvphomematches'] + $pvpstats['pvpawaymatches'];
		$pvpstats['pvptotwins'] = $pvpstats['pvphomewin'] + $pvpstats['pvpawaywin'];
		$pvpstats['pvptotdraws'] = $pvpstats['pvphomedraw'] + $pvpstats['pvpawaydraw'];
		$pvpstats['pvptotloses'] = $pvpstats['pvphomelose'] + $pvpstats['pvpawaylose'];

		$pvpstats['pvptotsetswin'] = $pvpstats['pvphomesets'] + $pvpstats['pvpawaysets'];
		$pvpstats['pvptotsetslose'] = $pvpstats['pvphomesetslose'] + $pvpstats['pvpawaysetslose'];
		$pvpstats['pvptotsets'] = $pvpstats['pvptotsetswin'] + $pvpstats['pvptotsetslose'];

		$pvpstats['pvptothighfinish'] = $pvpstats['pvphomehighfinish'] >= $pvpstats['pvpawayhighfinish'] ? $pvpstats['pvphomehighfinish'] : $pvpstats['pvpawayhighfinish'];
		$pvpstats['pvptot180'] = $pvpstats['pvphome180'] + $pvpstats['pvpaway180'];

	} //end if rows existend
	Return $pvpstats;
}

// get the player names
function getplayername($playerid) {
	Global $xoopsDB;
	$query = $xoopsDB->query("SELECT PlayerName FROM ".$xoopsDB->prefix("matchdart_players")." WHERE
							PlayerID = '$playerid'");
	$nameqry = $xoopsDB->fetchArray($query);
	Return $nameqry['PlayerName'];
}

// get the player stats
function getplayerstats($playerid, $season, $event) {
	Global $xoopsDB, $xd_print_date;
	$stats = Array();

	// Home wins
	$query = $xoopsDB->query("SELECT Count(*) AS homewins FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE
							MatchSeasonID LIKE '$season' AND MatchEventID LIKE '$event' AND
							MatchHomeWinnerID = $playerid");
	$statsqry = $xoopsDB->fetchArray($query);
	$stats['homewins'] = $statsqry['homewins'];
	if (!isset($stats['homewins'])){$stats['homewins'] = 0;}  

	// Home Draws
	$query = $xoopsDB->query("SELECT Count(*) AS homedraws FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE
							MatchSeasonID LIKE '$season' AND MatchEventID LIKE '$event' AND
							MatchHomeTieID = $playerid");
	$statsqry = $xoopsDB->fetchArray($query);
	$stats['homedraws'] = $statsqry['homedraws']; 
	if (!isset($stats['homedraws'])){$stats['homedraws'] = 0;}
	
	// Home Loses
	$query = $xoopsDB->query("SELECT Count(*) AS homeloses FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE
							MatchSeasonID LIKE '$season' AND MatchEventID LIKE '$event' AND
							MatchHomeLoserID = $playerid");
	$statsqry = $xoopsDB->fetchArray($query);
	$stats['homeloses'] = $statsqry['homeloses'];
	if (!isset($stats['homeloses'])){$stats['homeloses'] = 0;}

	// Away Wins
	$query = $xoopsDB->query("SELECT Count(*) AS awaywins FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE
							MatchSeasonID LIKE '$season' AND MatchEventID LIKE '$event' AND
							MatchAwayWinnerID = $playerid");
	$statsqry = $xoopsDB->fetchArray($query);
	$stats['awaywins'] = $statsqry['awaywins'];
	if (!isset($stats['awaywins'])){$stats['awaywins'] = 0;} 

	// Away Draws
	$query = $xoopsDB->query("SELECT Count(*) AS awaydraws FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE
							MatchSeasonID LIKE '$season' AND MatchEventID LIKE '$event' AND
							MatchAwayTieID = $playerid");
	$statsqry = $xoopsDB->fetchArray($query);
	$stats['awaydraws'] = $statsqry['awaydraws']; 
	if (!isset($stats['awaydraws'])){$stats['awaydraws'] = 0;}
	
	// Away Loses
	$query = $xoopsDB->query("SELECT Count(*) AS awayloses FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE
							MatchSeasonID LIKE '$season' AND MatchEventID LIKE '$event' AND
							MatchAwayLoserID = $playerid");
	$statsqry = $xoopsDB->fetchArray($query);
	$stats['awayloses'] = $statsqry['awayloses'];
	if (!isset($stats['awayloses'])){$stats['awayloses'] = 0;}

	// Total Matches
	$stats['homematches'] = $stats['homewins'] + $stats['homeloses'] + $stats['homedraws'];
	$stats['awaymatches'] = $stats['awaywins'] + $stats['awayloses'] + $stats['awaydraws'];
	$stats['totmatches'] = $stats['homematches'] + $stats['awaymatches'];
	//total wins / loses / draws
	$stats['totwins'] = $stats['homewins'] + $stats['awaywins']; 
	$stats['totloses'] = $stats['homeloses'] + $stats['awayloses']; 
	$stats['totdraws'] = $stats['homedraws'] + $stats['awaydraws']; 

	// Home Sets Win
	$query = $xoopsDB->query("SELECT Sum(MatchHomeSets) AS homesetswin 
							FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE 
							MatchSeasonID LIKE '$season' AND MatchEventID LIKE '$event' AND
							MatchHomeID = $playerid");
	$statsqry = $xoopsDB->fetchArray($query);
	$stats['homesetswin'] = $statsqry['homesetswin'];
	if (!isset($stats['homesetswin'])){$stats['homesetswin'] = 0;}  

	// Home Sets Lose
	$query = $xoopsDB->query("SELECT Sum(MatchAwaySets) AS homesetslose 
							FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE 
							MatchSeasonID LIKE '$season' AND MatchEventID LIKE '$event' AND
							MatchHomeID = $playerid");
	$statsqry = $xoopsDB->fetchArray($query);
	$stats['homesetslose'] = $statsqry['homesetslose'];
	if (!isset($stats['homesetslose'])){$stats['homesetslose'] = 0;}

	// Away Sets Win
	$query = $xoopsDB->query("SELECT Sum(MatchAwaySets) AS awaysetswin 
							FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE 
							MatchSeasonID LIKE '$season' AND MatchEventID LIKE '$event' AND
							MatchAwayID = $playerid");
	$statsqry = $xoopsDB->fetchArray($query);
	$stats['awaysetswin'] = $statsqry['awaysetswin'];
	if (!isset($stats['awaysetswin'])){$stats['awaysetswin'] = 0;} 

	// Away Sets Lose
	$query = $xoopsDB->query("SELECT Sum(MatchHomeSets) AS awaysetslose 
							FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE 
							MatchSeasonID LIKE '$season' AND MatchEventID LIKE '$event' AND
							MatchAwayID = $playerid");
	$statsqry = $xoopsDB->fetchArray($query);
	$stats['awaysetslose'] = $statsqry['awaysetslose'];
	if (!isset($stats['awaysetslose'])){$stats['awaysetslose'] = 0;}
	
	// Total Sets
	$stats['homesets'] = $stats['homesetswin'] + $stats['homesetslose'];
	$stats['awaysets'] = $stats['awaysetswin'] + $stats['awaysetslose'];
	$stats['totsetswin'] = $stats['homesetswin'] + $stats['awaysetswin'];
	$stats['totsetslose'] = $stats['homesetslose'] + $stats['awaysetslose'];
	$stats['totsets'] = $stats['totsetswin'] + $stats['totsetslose'];
	
	// Home 180 scored
	$query = $xoopsDB->query("SELECT Sum(MatchHome180) AS home180 
							FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE 
							MatchSeasonID LIKE '$season' AND MatchEventID LIKE '$event' AND
							MatchHomeID = $playerid");
	$statsqry = $xoopsDB->fetchArray($query);
	$stats['home180'] = $statsqry['home180'];
	if (!isset($stats['home180'])){$stats['home180'] = 0;}  

	// Away 180 scored
	$query = $xoopsDB->query("SELECT Sum(MatchAway180) AS away180 
							FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE 
							MatchSeasonID LIKE '$season' AND MatchEventID LIKE '$event' AND
							MatchAwayID = $playerid");
	$statsqry = $xoopsDB->fetchArray($query);
	$stats['away180'] = $statsqry['away180'];
	if (!isset($stats['away180'])){$stats['away180'] = 0;} 

	// Total 180 scored
	$stats['tot180'] = $stats['home180'] + $stats['away180']; 

	// Home highest finish
	$query = $xoopsDB->query("SELECT
					MatchHomeHighfinish AS homehighfinish,
					P.PlayerName AS homehighfinishplayer,
					DATE_FORMAT(MatchDate, '$xd_print_date') AS homehighfinishdate,
					O.PlayerName as homehighfinishopponent
					FROM ".$xoopsDB->prefix("matchdart_matches").",".$xoopsDB->prefix("matchdart_players")." P,".$xoopsDB->prefix("matchdart_players")." O
					WHERE MatchHomeID = $playerid AND
					MatchSeasonID LIKE '$season' AND MatchEventID LIKE '$event' AND
					P.PlayerID = MatchHomeID AND
					O.PlayerID = MatchAwayID AND
					MatchHomeHighfinish != 0
					ORDER BY MatchHomeHighfinish DESC, MatchDate DESC, MatchID DESC LIMIT 1
					");
	$statsqry = $xoopsDB->fetchArray($query);
	$stats['homehighfinish'] = $statsqry['homehighfinish'];
	$stats['homehighfinishplayer'] = $statsqry['homehighfinishplayer'];
	$stats['homehighfinishdate'] = $statsqry['homehighfinishdate'];
	$stats['homehighfinishopponent'] = $statsqry['homehighfinishopponent'];
	if (!isset($stats['homehighfinish'])){$stats['homehighfinish'] = 0;}  

	// Away highest finish
	$query = $xoopsDB->query("SELECT
					MatchAwayHighfinish AS awayhighfinish,
					P.PlayerName AS awayhighfinishplayer,
					DATE_FORMAT(MatchDate, '$xd_print_date') AS awayhighfinishdate,
					O.PlayerName as awayhighfinishopponent
					FROM ".$xoopsDB->prefix("matchdart_matches").",".$xoopsDB->prefix("matchdart_players")." P,".$xoopsDB->prefix("matchdart_players")." O
					WHERE MatchAwayID = $playerid AND
					MatchSeasonID LIKE '$season' AND MatchEventID LIKE '$event' AND
					P.PlayerID = MatchAwayID AND
					O.PlayerID = MatchHomeID AND
					MatchAwayHighfinish != 0
					ORDER BY MatchAwayHighfinish DESC, MatchDate DESC, MatchID DESC LIMIT 1
					");
	$statsqry = $xoopsDB->fetchArray($query);
	$stats['awayhighfinish'] = $statsqry['awayhighfinish'];
	$stats['awayhighfinishplayer'] = $statsqry['awayhighfinishplayer'];
	$stats['awayhighfinishdate'] = $statsqry['awayhighfinishdate'];
	$stats['awayhighfinishopponent'] = $statsqry['awayhighfinishopponent'];
	if (!isset($stats['awayhighfinish'])){$stats['awayhighfinish'] = 0;} 

	// Total highest finish
	if ($stats['homehighfinish'] >= $stats['awayhighfinish'])
	{
		$stats['tothighfinish'] = $stats['homehighfinish'];
		$stats['tothighfinishplayer'] = $stats['homehighfinishplayer'];
		$stats['tothighfinishdate'] = $stats['homehighfinishdate'];
		$stats['tothighfinishopponent'] = $stats['homehighfinishopponent'];
	} else
	{
		$stats['tothighfinish'] = $stats['awayhighfinish'];
		$stats['tothighfinishplayer'] = $stats['awayhighfinishplayer']; 
		$stats['tothighfinishdate'] = $stats['awayhighfinishdate'];
		$stats['tothighfinishopponent'] = $stats['awayhighfinishopponent'];
	}

	// Averages
	//reminder: number_format(round($value,1),$dec_places,$dec_sep,$thou_sep);
	if ($stats['totmatches'] != 0)
	{
		//Totals
		$stats['avgtotwins'] = number_format(round(($stats['totwins']*100/$stats['totmatches']),2),2,'.',',');
		$stats['avgtotdraws'] = number_format(round(($stats['totdraws']*100/$stats['totmatches']),2),2,'.',',');
		$stats['avgtotloses'] = number_format(round(($stats['totloses']*100/$stats['totmatches']),2),2,'.',',');

		$stats['avgtotsets'] = number_format(round(($stats['totsets']/$stats['totmatches']),2),2,'.',',');
		$stats['avgtotsetswin'] = number_format(round(($stats['totsetswin']*100/$stats['totsets']),2),2,'.',',');
		$stats['avgtotsetslose'] = number_format(round(($stats['totsetslose']*100/$stats['totsets']),2),2,'.',',');

		$stats['avgtot180'] = number_format(round(($stats['tot180']/$stats['totmatches']),2),2,'.',',');
				
		//Home
		if ($stats['homematches'] != 0)
		{
			$stats['avghomewins'] = number_format(round(($stats['homewins']*100/$stats['homematches']),2),2,'.',',');
			$stats['avghomeloses'] = number_format(round(($stats['homeloses']*100/$stats['homematches']),2),2,'.',',');
			$stats['avghomedraws'] = number_format(round(($stats['homedraws']*100/$stats['homematches']),2),2,'.',',');
			
			$stats['avghomesets'] = number_format(round(($stats['homesets']/$stats['homematches']),2),2,'.',',');
			$stats['avghomesetswin'] = number_format(round(($stats['homesetswin']*100/$stats['homesets']),2),2,'.',',');
			$stats['avghomesetslose'] = number_format(round(($stats['homesetslose']*100/$stats['homesets']),2),2,'.',',');

			$stats['avghome180'] = number_format(round(($stats['home180']/$stats['homematches']),2),2,'.',',');
		}
		else
		{
			$stats['avghomewins'] = number_format(0,2,'.',',');
			$stats['avghomeloses'] = number_format(0,2,'.',',');
			$stats['avghomedraws'] = number_format(0,2,'.',',');
			
			$stats['avghomesets'] = number_format(0,2,'.',',');
			$stats['avghomesetswin'] = number_format(0,2,'.',',');
			$stats['avghomesetslose'] = number_format(0,2,'.',',');
			
			$stats['avghome180'] = number_format(0,2,'.',',');
		}
		
		//Away
		if ($stats['awaymatches'] != 0)
		{
			$stats['avgawaywins'] = number_format(round(($stats['awaywins']*100/$stats['awaymatches']),2),2,'.',',');
			$stats['avgawayloses'] = number_format(round(($stats['awayloses']*100/$stats['awaymatches']),2),2,'.',',');
			$stats['avgawaydraws'] = number_format(round(($stats['awaydraws']*100/$stats['awaymatches']),2),2,'.',',');
			
			$stats['avgawaysets'] = number_format(round(($stats['awaysets']/$stats['awaymatches']),2),2,'.',',');
			$stats['avgawaysetswin'] = number_format(round(($stats['awaysetswin']*100/$stats['awaysets']),2),2,'.',',');
			$stats['avgawaysetslose'] = number_format(round(($stats['awaysetslose']*100/$stats['awaysets']),2),2,'.',',');

			$stats['avgaway180'] = number_format(round(($stats['away180']/$stats['awaymatches']),2),2,'.',',');
		}
		else
		{
			$stats['avgawaywins'] = number_format(0,2,'.',',');
			$stats['avgawayloses'] = number_format(0,2,'.',',');
			$stats['avgawaydraws'] = number_format(0,2,'.',',');
			
			$stats['avgawaysets'] = number_format(0,2,'.',',');
			$stats['avgawaysetswin'] = number_format(0,2,'.',',');
			$stats['avgawaysetslose'] = number_format(0,2,'.',',');
			
			$stats['avgaway180'] = number_format(0,2,'.',',');
		}
	}
	Return $stats;
}


include(XOOPS_ROOT_PATH.'/footer.php');
?>