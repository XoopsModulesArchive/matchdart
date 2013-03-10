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
$xoopsOption['template_main'] = 'xd_matches_main.html';
require(XOOPS_ROOT_PATH.'/header.php');

include_once(XOOPS_ROOT_PATH.'/modules/matchdart/include/functions.php');
include_once(XOOPS_ROOT_PATH.'/modules/matchdart/include/header.php');


$module_id = $xoopsModule->getVar('mid');

if (is_object($xoopsUser)) {
	$groups = $xoopsUser->getGroups();
} else {
	$groups = XOOPS_GROUP_ANONYMOUS;
}

$gperm_handler =& xoops_gethandler('groupperm');
if (!$gperm_handler->checkRight("match_submit", 1, $groups, $module_id)) {
	redirect_header(XOOPS_URL.'/modules/matchdart/index.php', 3, _NOPERM);
	exit();
}

$xoopsTpl->assign('script', 'matches.php');

//check for update
if (!xd_checkupdate()){
	$update_msg = "<p style='color:red; font-size:medium; font-weight:bold'>"._XD_UPDATEREQUIRED."</p><br>";
	$xoopsTpl->assign('update_msg', $update_msg);
	$xoopsTpl->assign('update_req', 1);
}

// get module preferences
$xd_print_date = $xoopsModuleConfig['printdate'];
$xd_print_lastupdate = $xoopsModuleConfig['printlastupdate'];
$xd_d_seasonid =  xd_getdefaultseasonid();
$xd_d_eventid =  xd_getdefaulteventid();
$addnew = 0;
$action = '';


// Set Session vars
if (!isset($_SESSION['seasonid']) or $_SESSION['seasonid'] == 0) {
	$_SESSION['seasonid'] = $xd_d_seasonid;
}
if (!isset($_SESSION['eventid']) or $_SESSION['eventid'] == 0) {
	$_SESSION['eventid'] = $xd_d_eventid;
}

// get select box arrays
$season = xd_getseasons();
$event = xd_getevents();

//get years for selection
//TODO: make this variable depending on actual year
$year = array(2000,2001,2002,2003,2004,2005,2006,2007,2008,2009,2010,2011,2012,2013,2014);
$date = array();
$date['day'] = date('j');
$date['month'] = date('n');
$date['year'] = date('Y');

// get players for selection
$player=array();
$q=1;
$query = $xoopsDB->query("SELECT PlayerID, PlayerName
		FROM ".$xoopsDB->prefix("matchdart_players").
		" ORDER BY PlayerName");
while($myrow = $xoopsDB->fetchArray($query) ) {
	$player[$q]['id'] = $myrow['PlayerID'];
	$player[$q]['name'] = $myrow['PlayerName'];
	$q++;
}

// end select box arrays

// check for changes

//check for GO_MOVETO
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

//check for ACTION
if (isset($_GET['action'])) {
	$action = $_GET['action'];
	$actionid = $_GET['id'];
	$xoopsTpl->assign('action',$action);
	$xoopsTpl->assign('actionid',$actionid);
	if ($action == 'addnew') {
		unset($_SESSION['dateday']);
		unset($_SESSION['datemonth']);
		unset($_SESSION['dateyear']);
	}
}

// end check for changes

$seasonid = $_SESSION['seasonid'];
$eventid = $_SESSION['eventid'];

// get selected event data
$eventdata = array();
$query = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("matchdart_eventnames")." WHERE EventPublish = '1'
AND EventID = '$eventid'");
$myrow = $xoopsDB->fetchArray($query);
$eventdata['id'] = $myrow['EventID'];
$eventdata['name'] = $myrow['EventName'];
$eventdata['variant'] = $myrow['EventMatchVariant'];
$eventdata['playerstats'] = $myrow['EventPlayerStats'];

// action = modify or delete, get match data
if ($action == 'modify' OR $action == 'delete'){
	$query = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE MatchID = $actionid");
	$matchdata = $xoopsDB->fetchArray($query);
	$matchdata['MatchYear'] = substr($matchdata['MatchDate'],0,4);
	$matchdata['MatchMonth'] = substr($matchdata['MatchDate'],5,2);
	$matchdata['MatchDay'] = substr($matchdata['MatchDate'],8,2);
	$xoopsTpl->assign('matchdata',$matchdata);
}

// check for GO_DELETE - DELETE Match
if (isset($_POST['go_delete'])) {
	$delete_matchid = $_POST['in_matchid'];
	$query = "DELETE FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE MatchId = $delete_matchid";
	$res=$xoopsDB->query($query);
	if(!$res) {
		$xoopsTpl->assign('msg', _XD_MATCHERROR."&nbsp;".$query);
	} else {
		$xoopsTpl->assign('msg', _XD_MATCHDELETED);
	}
	$action = '';
}


//check for GO_ADDNEW or GO_MODIFY - INSERT Form Data
if (isset($_POST['go_addnew']) or isset($_POST['go_modify'])) {
	$action = isset($_POST['go_addnew'])?'addnew':'modify';

	$myts = myTextSanitizer::getInstance();

	$modify_matchid = $_POST['in_matchid'];
	$seasonid = $myts->addslashes($_POST['in_season']);
	$eventid = $myts->addslashes($_POST['in_event']);
	$homeid = $myts->addslashes($_POST['in_homeid']);
	$awayid = $myts->addslashes($_POST['in_awayid']);
	$dateday = $myts->addslashes($_POST['in_day']);
	$_SESSION['dateday'] = $dateday;
	$datemonth = $myts->addslashes($_POST['in_month']);
	$_SESSION['datemonth'] = $datemonth;
	$dateyear = $myts->addslashes($_POST['in_year']);
	$_SESSION['dateyear'] = $dateyear;
	$matchdate = "$dateyear-$datemonth-$dateday";
	$homebp = $myts->addslashes($_POST['in_homebp']);
	$awaybp = $myts->addslashes($_POST['in_awaybp']);

	$homesets = $myts->addslashes($_POST['in_homesets']);
	$homelegs = $myts->addslashes($_POST['in_homelegs']);
	$homehighfinish = $myts->addslashes($_POST['in_homehighfinish']);
	$home180 = $myts->addslashes($_POST['in_home180']);
	
	$awaysets = $myts->addslashes($_POST['in_awaysets']);
	$awaylegs = $myts->addslashes($_POST['in_awaylegs']);
	$awayhighfinish = $myts->addslashes($_POST['in_awayhighfinish']);
	$away180 = $myts->addslashes($_POST['in_away180']);
	
	if ($eventdata['playerstats'] == 0) {
		$matchplayerstats = -1;
		
		$homedarts = -1;
		$homeppt = -1;
		$homeppd = -1;
		$homematchdarts = -1;
		
		$awaydarts = -1;
		$awayppt = -1;
		$awayppd = -1;
		$awaymatchdarts = -1;
	} else {
		$matchplayerstats = 1;
		
		$homedarts = $myts->addslashes($_POST['in_homedarts']);
		$homeppt = $myts->addslashes($_POST['in_homeppt']);
		$homeppd = $myts->addslashes($_POST['in_homeppd']);
		$homematchdarts = $myts->addslashes($_POST['in_homematchdarts']);
		
		$awaydarts = $myts->addslashes($_POST['in_awaydarts']);
		$awayppt = $myts->addslashes($_POST['in_awayppt']);
		$awayppd = $myts->addslashes($_POST['in_awayppd']);
		$awaymatchdarts = $myts->addslashes($_POST['in_awaymatchdarts']);
	}

	//not yet implemented
	$variant = '';
	$initialscore = 0;
	$type = '';
	$singlein = 0;
	$doublein = 0;
	$singleout = 0;
	$doubleout = 0;


	if (($homeid != $awayid) AND $homesets != '' AND $awaysets != '') {
		// calculate results
		// Set default
		$homewinner = -1;
		$homeloser = -1;
		$hometie = -1;
		$awaywinner = -1;
		$awayloser = -1;
		$awaytie = -1;

		//Home wins
		if($homesets > $awaysets) {
			$homewinner = $homeid;
			$awayloser = $awayid;
		}
		//Away wins
		elseif($homesets < $awaysets) {
			$homeloser = $homeid;
			$awaywinner = $awayid;
		}
		//Draw
		elseif($homesets == $awaysets) {
			$hometie = $homeid;
			$awaytie = $awayid;
		}
		// Legacy !!
		$homebonus = $homebp;
		$awaybonus = $awaybp;
		$homebpoints = $homebonus;
		$awaybpoints = $awaybonus;

		if ($action == 'addnew') {
			$query = "INSERT INTO ".$xoopsDB->prefix("matchdart_matches")." SET
			MatchSeasonID = '$seasonid',
			MatchEventID = '$eventid',
			MatchDate = '$matchdate',
			MatchVariant = '$variant',
			MatchInitialScore = '$initialscore',
			MatchType = '$type',
			MatchSingleIn = '$singlein',
			MatchDoubleIn = '$doublein',
			MatchSingleOut = '$singleout',
			MatchDoubleOut = '$doubleout',
			MatchHomeID = '$homeid',
			MatchHomeSets = '$homesets',
			MatchHomeBpoints = '$homebpoints',
			MatchHomeHighfinish = '$homehighfinish',
			MatchHome180 = '$home180',
			MatchAwayID = '$awayid',
			MatchAwaySets = '$awaysets',
			MatchAwayBpoints = '$awaybpoints',
			MatchAwayHighfinish = '$awayhighfinish',
			MatchAway180 = '$away180',
			MatchHomeBonus = '$homebonus',
			MatchAwayBonus = '$awaybonus',
			MatchHomeWinnerID = '$homewinner',
			MatchHomeLoserID = '$homeloser',
			MatchHomeTieID = '$hometie',
			MatchAwayWinnerID = '$awaywinner',
			MatchAwayLoserID = '$awayloser',
			MatchAwayTieID = '$awaytie',
			MatchHomeLegs = '$homelegs',
			MatchHomeDarts = '$homedarts',
			MatchHomePPT = '$homeppt',
			MatchHomePPD = '$homeppd',
			MatchHomeMatchDarts = '$homematchdarts',
			MatchAwayLegs = '$awaylegs',
			MatchAwayDarts = '$awaydarts',
			MatchAwayPPT = '$awayppt',
			MatchAwayPPD = '$awayppd',
			MatchAwayMatchDarts = '$awaymatchdarts',
			MatchPlayerStats = '$matchplayerstats',
			MatchCreated = ".time();
		} elseif ($action == 'modify') {
			$query = "UPDATE ".$xoopsDB->prefix("matchdart_matches")." SET
			MatchSeasonID = '$seasonid',
			MatchEventID = '$eventid',
			MatchDate = '$matchdate',
			MatchVariant = '$variant',
			MatchInitialScore = '$initialscore',
			MatchType = '$type',
			MatchSingleIn = '$singlein',
			MatchDoubleIn = '$doublein',
			MatchSingleOut = '$singleout',
			MatchDoubleOut = '$doubleout',
			MatchHomeID = '$homeid',
			MatchHomeSets = '$homesets',
			MatchHomeBpoints = '$homebpoints',
			MatchHomeHighfinish = '$homehighfinish',
			MatchHome180 = '$home180',
			MatchAwayID = '$awayid',
			MatchAwaySets = '$awaysets',
			MatchAwayBpoints = '$awaybpoints',
			MatchAwayHighfinish = '$awayhighfinish',
			MatchAway180 = '$away180',
			MatchHomeBonus = '$homebonus',
			MatchAwayBonus = '$awaybonus',
			MatchHomeWinnerID = '$homewinner',
			MatchHomeLoserID = '$homeloser',
			MatchHomeTieID = '$hometie',
			MatchAwayWinnerID = '$awaywinner',
			MatchAwayLoserID = '$awayloser',
			MatchAwayTieID = '$awaytie',
			MatchHomeLegs = '$homelegs',
			MatchHomeDarts = '$homedarts',
			MatchHomePPT = '$homeppt',
			MatchHomePPD = '$homeppd',
			MatchHomeMatchDarts = '$homematchdarts',
			MatchAwayLegs = '$awaylegs',
			MatchAwayDarts = '$awaydarts',
			MatchAwayPPT = '$awayppt',
			MatchAwayPPD = '$awayppd',
			MatchAwayMatchDarts = '$awaymatchdarts',
			MatchPlayerStats = '$matchplayerstats',
			MatchCreated = ".time().
			" WHERE MatchID = $modify_matchid";

			$action = '';
		}
		$res=$xoopsDB->query($query);
		if(!$res) {
			$xoopsTpl->assign('msg', _XD_MATCHERROR."&nbsp;".$query);
		}
	}
	// homeid=awayid or homesets/awaysets empty
	else {
		$msg='';
		if ($homeid == $awayid) {$msg = _XD_MATCHERROR."&nbsp;"._XD_MATCHSAMEPLAYER."<br>";}
		if ($homesets == '') {$msg = $msg._XD_MATCHERROR."&nbsp;"._XD_MATCHNOHOMESET."<br>";}
		if ($awaysets == '') {$msg = $msg._XD_MATCHERROR."&nbsp;"._XD_MATCHNOAWAYSET;}
		$xoopsTpl->assign('msg', "$msg");
	}
}
if (isset($_SESSION['dateday'])) {
	$date['day'] = $_SESSION['dateday'];
	$date['month'] = $_SESSION['datemonth'];
	$date['year'] = $_SESSION['dateyear'];
}

//end check for GO_ADDNEW - INSERT Form Data


// get match data
$match=array();
$q=1;
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

$query = $xoopsDB->query("SELECT
LM.*,
O.PlayerName AS hometeam,
OP.PlayerName AS awayteam,
DATE_FORMAT(LM.MatchDate, '$xd_print_date') AS date
FROM ".$xoopsDB->prefix("matchdart_matches")." LM, "
.$xoopsDB->prefix("matchdart_players")." O, "
.$xoopsDB->prefix("matchdart_players")." OP
WHERE O.PlayerID = LM.MatchHomeID AND
OP.PlayerID = LM.MatchAwayID AND
MatchSeasonID = '$seasonselect' AND
MatchEventID = '$eventselect'
ORDER BY LM.MatchID DESC");

while($myrow = $xoopsDB->fetchArray($query) ) {
	$match[$q]['hometeam'] = $myrow['hometeam'];
	$match[$q]['awayteam'] = $myrow['awayteam'];
	$match[$q]['id'] = $myrow['MatchID'];
	$match[$q]['defaultdate'] = $myrow['MatchDate'];
	$match[$q]['date'] = $myrow['date'];

	$match[$q]['variant'] = $myrow['MatchVariant'];
	$match[$q]['initialscore'] = $myrow['MatchInitialScore'];
	$match[$q]['type'] = $myrow['MatchType'];
	$match[$q]['singlein'] = $myrow['MatchSingleIn'];
	$match[$q]['doublein'] = $myrow['MatchDoubleIn'];
	$match[$q]['singleout'] = $myrow['MatchSingleOut'];
	$match[$q]['doubleout'] = $myrow['MatchDoubleOut'];

	$match[$q]['homesets'] = $myrow['MatchHomeSets'];
	$match[$q]['homelegs'] = $myrow['MatchHomeLegs'];
	$match[$q]['homebpoints'] = $myrow['MatchHomeBpoints'];
	$match[$q]['homehighfinish'] = $myrow['MatchHomeHighfinish'];
	$match[$q]['home180'] = $myrow['MatchHome180'];
	$match[$q]['homedarts'] = $myrow['MatchHomeDarts'];
	$match[$q]['homeppt'] = $myrow['MatchHomePPT'];
	$match[$q]['homeppd'] = $myrow['MatchHomePPD'];
	$match[$q]['homematchdarts'] = $myrow['MatchHomeMatchDarts'];
	
	$match[$q]['awaysets'] = $myrow['MatchAwaySets'];
	$match[$q]['awaylegs'] = $myrow['MatchAwayLegs'];
	$match[$q]['awaybpoints'] = $myrow['MatchAwayBpoints'];
	$match[$q]['awayhighfinish'] = $myrow['MatchAwayHighfinish'];
	$match[$q]['away180'] = $myrow['MatchAway180'];
	$match[$q]['awaydarts'] = $myrow['MatchAwayDarts'];
	$match[$q]['awayppt'] = $myrow['MatchAwayPPT'];
	$match[$q]['awayppd'] = $myrow['MatchAwayPPD'];
	$match[$q]['awaymatchdarts'] = $myrow['MatchAwayMatchDarts'];
	
	$q++;
}
// end match data

// assign data to templates

// assign season data
$xoopsTpl->assign('seasonid', $seasonid);
$xoopsTpl->assign('season', $season);
// assign event data
$xoopsTpl->assign('eventid', $eventid);
$xoopsTpl->assign('event', $event);
$xoopsTpl->assign('eventdata', $eventdata);
// assign player data
$xoopsTpl->assign('player', $player);
// assign year data
$xoopsTpl->assign('date', $date);
$xoopsTpl->assign('year', $year);
// assign match data
$xoopsTpl->assign('match', $match);
// open input form ADDNEW
$xoopsTpl->assign('action', $action);
// end assign data to templates

require(XOOPS_ROOT_PATH.'/footer.php');
?>