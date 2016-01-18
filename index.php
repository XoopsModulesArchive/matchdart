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
$xoopsOption['template_main'] = 'xd_index.html';
require(XOOPS_ROOT_PATH.'/header.php');

include_once(XOOPS_ROOT_PATH.'/modules/matchdart/include/functions.php');
include_once(XOOPS_ROOT_PATH.'/modules/matchdart/include/header.php');

$xoopsTpl->assign('script', 'index.php');

//check for update
if (!xd_checkupdate()){
	$update_msg = "<p style='color:red; font-size:medium; font-weight:bold'>"._XD_UPDATEREQUIRED."</p><br>";
	$xoopsTpl->assign('update_msg', $update_msg);
}

// get User ID
is_object($xoopsUser) ? $xd_uid = $xoopsUser->getVar('uid') : $xd_uid = -1;

// get default values
$xd_d_seasonid =  xd_getdefaultseasonid();
$xd_d_eventid =  xd_getdefaulteventid();
$xd_d_display = $xoopsModuleConfig['defaultshow']; //default display of matches (all, own, none)
$xd_d_table = $xoopsModuleConfig['defaulttable']; //default table layout
$xd_d_sort = 'pts'; //default sort field (TODO: make table sortable by colum headers)

// get select box arrays
$season = xd_getseasons();
$event = xd_getevents();

// Set Session vars
if (!isset($_SESSION['seasonid'])) {
	$_SESSION['seasonid'] = $xd_d_seasonid;
}
if (!isset($_SESSION['eventid'])) {
	$_SESSION['eventid'] = $xd_d_eventid;
}
if (!isset($_SESSION['display'])) {
	$_SESSION['display'] = $xd_d_display;
}
if (!isset($_SESSION['table'])) {
	$_SESSION['table'] = $xd_d_table;
}

// check for changes
$sort = $xd_d_sort;

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

//check for GO_DISPLAY
if (isset($_POST['go_display']) AND $_POST['go_display'] == 1) {
	$_SESSION['display'] = intval($_POST['in_display']);
}

//check for GO_TABLE
if (isset($_POST['go_table']) AND $_POST['go_table'] == 1) {
	$_SESSION['table'] = intval($_POST['in_table']);
}

$seasonid = $_SESSION['seasonid'];
$eventid = $_SESSION['eventid'];
$display = $_SESSION['display'];
$table = $_SESSION['table'];

// end check for changes

// get display data
//get last updated date and time
$query = $xoopsDB->query("SELECT MAX(MatchCreated) AS last_updated FROM ".$xoopsDB->prefix("matchdart_matches"));
$timestamp = $xoopsDB->fetchArray($query);
$lastupdate = date($xd_print_lastupdate, $timestamp['last_updated']);

// get table underline data
$draw_line = array();
$query = $xoopsDB->query("SELECT EventLine FROM ".$xoopsDB->prefix("matchdart_eventnames")." WHERE EventID ='$eventid'");
$eventline = $xoopsDB->fetchArray($query);
$draw_line = explode(",", $eventline['EventLine']);

//get players active in selected season & event and the table data
if ($seasonid == 0)
{
	$seasonselect = '%';
} else
{
	$seasonselect = $seasonid;
}
if ($eventid == 0)
{
	$eventselect = '%';
} else
{
	$eventselect = $eventid;
}
$players = Array();
$players = xd_gettable('full',$seasonselect,$eventselect,$xd_degradefactor);

// get played match calendar
$played_nomatches = 0;
$calendarplayed= Array();
if ($display != 3) // calendar none
{
	if ($display == 1) // calendar ALL
	{
		$selectplayer = '';
	} elseif ($display == 2) // calendar OWN
	{
		$selectplayer = " AND (PH.PlayerUID = '$xd_uid' OR PA.PlayerUID = '$xd_uid') ";
	}

	$query_all = $xoopsDB->query("SELECT
	M.MatchID as id,
	DATE_FORMAT(M.MatchDate, '$xd_print_date') AS date,
	PH.PlayerName as homeplayer,
	PH.PlayerUID as homeuid,
	PA.PlayerName as awayplayer,
	PA.PlayerUID as awayuid,
	M.MatchHomeSets AS homesets,
	M.MatchAwaySets AS awaysets
	FROM ".$xoopsDB->prefix("matchdart_matches")." M, ".$xoopsDB->prefix("matchdart_players")." PH, ".$xoopsDB->prefix("matchdart_players")." PA
	WHERE PH.PlayerID = M.MatchHomeID AND
	PA.PlayerID = M.MatchAwayID AND
	M.MatchDate <= CURDATE() AND
	MatchSeasonID LIKE '$seasonselect' AND
	MatchEventID LIKE '$eventselect'".$selectplayer."
				ORDER BY M.MatchDate DESC");

	if($xoopsDB->getRowsNum($query_all) < 1) {
		$played_nomatches = 1;
	}
	else {
		$q = 1;
		$tempdate = '';
		while($display_row = $xoopsDB->fetchArray($query_all))
		{
			$calendarplayed[$q]['date'] = $display_row['date'];
			if ($q == 1 or $display_row['date'] != $tempdate)
			{
				$calendarplayed[$q]['datedisplay'] = $display_row['date'];
				$tempdate = $display_row['date'];
			} else
			{
				$calendarplayed[$q]['datedisplay'] = '-1';
			}
				
			$calendarplayed[$q]['homeplayer'] = $display_row['homeplayer'];
			$calendarplayed[$q]['homeuid'] = $display_row['homeuid'];
			$calendarplayed[$q]['awayplayer'] = $display_row['awayplayer'];
			$calendarplayed[$q]['awayuid'] = $display_row['awayuid'];
			$calendarplayed[$q]['homesets'] = $display_row['homesets'];
			$calendarplayed[$q]['awaysets'] = $display_row['awaysets'];
			//print_r($calendarplayed);
			$q++;
		}
	}
	// get upcoming match calendar
	$upcoming_nomatches = 0;
	$calendarupcoming= Array();
	$query_all = $xoopsDB->query("SELECT
	M.MatchID as id,
	DATE_FORMAT(M.MatchDate, '$xd_print_date') AS date,
	PH.PlayerName as homeplayer,
	PH.PlayerUID as homeuid,
	PA.PlayerName as awayplayer,
	PA.PlayerUID as awayuid,
	M.MatchHomeSets AS homesets,
	M.MatchAwaySets AS awaysets
	FROM ".$xoopsDB->prefix("matchdart_matches")." M, ".$xoopsDB->prefix("matchdart_players")." PH, ".$xoopsDB->prefix("matchdart_players")." PA
	WHERE PH.PlayerID = M.MatchHomeID AND
	PA.PlayerID = M.MatchAwayID AND
	M.MatchDate > CURDATE() AND
	MatchSeasonID LIKE '$seasonselect' AND
	MatchEventID LIKE '$eventselect'".$selectplayer."
				ORDER BY M.MatchDate DESC");
	if($xoopsDB->getRowsNum($query_all) < 1) {
		$upcoming_nomatches = 1;
	}
	else {
		$q = 1;
		$tempdate = '';
		while($display_row = $xoopsDB->fetchArray($query_all))
		{
			$calendarupcoming[$q]['date'] = $display_row['date'];
			if ($q == 1 or $display_row['date'] != $tempdate) {
				$calendarupcoming[$q]['datedisplay'] = $display_row['date'];
				$tempdate = $display_row['date'];
			} else {
				$calendarupcoming[$q]['datedisplay'] = '-1';
			}
				
			$calendarupcoming[$q]['homeplayer'] = $display_row['homeplayer'];
			$calendarupcoming[$q]['homeuid'] = $display_row['homeuid'];
			$calendarupcoming[$q]['awayplayer'] = $display_row['awayplayer'];
			$calendarupcoming[$q]['awayuid'] = $display_row['awayuid'];
			$calendarupcoming[$q]['homesets'] = $display_row['homesets'];
			$calendarupcoming[$q]['awaysets'] = $display_row['awaysets'];
			$q++;
		}
	}
}

// Sort the table data
switch($sort)
{
	case 'pts':
		usort($players, "xd_cmppoints");
		break;
}
// set position nos after sorting the table
$pos=0;
foreach ($players as $player) {
	$players[$pos]['pos'] = $pos+1;
	$pos++;
}

// assign data to templates
// assign preferences
$xoopsTpl->assign('myplayeruid',$xd_uid);

// assign season data
$xoopsTpl->assign('seasonid', $seasonid);
$xoopsTpl->assign('season', $season);

// assign event data
$xoopsTpl->assign('eventid', $eventid);
$xoopsTpl->assign('event', $event);

// assign display / table layout
$xoopsTpl->assign('display', $display);
$xoopsTpl->assign('table', $table);
if (isset($draw_line)) {
	$xoopsTpl->assign('draw_line', $draw_line);
}

// assign player stats data
$xoopsTpl->assign('players', $players);
$xoopsTpl->assign('lastupdate',$lastupdate);

// assign calendar data
if ($display != 3){
	$xoopsTpl->assign('playednomatches', $played_nomatches);
	$xoopsTpl->assign('calendarplayed',$calendarplayed);
	$xoopsTpl->assign('upcomingnomatches', $upcoming_nomatches);
	$xoopsTpl->assign('calendarupcoming',$calendarupcoming);
}
$xoopsTpl->assign('display',$display);

include(XOOPS_ROOT_PATH.'/footer.php');
?>