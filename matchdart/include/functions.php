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

if (!defined('XOOPS_ROOT_PATH')) {
	die('XOOPS root path not defined');
}

/**
 * xd_getdefaultseasonid
 *
 * Returns default season id
 *
 * @package matchdart
 * @author wild0ne (mailto:wild0ne@partypilger.de)
 * @copyright (c) wild0ne
 * @param
 */
function xd_getdefaultseasonid()
{
	global $xoopsDB;

	$query = $xoopsDB->query("SELECT SeasonID FROM ".$xoopsDB->prefix("matchdart_seasonnames")." WHERE SeasonDefault=1");
	$xd_default_season = $xoopsDB->fetchArray($query);
	if (!isset($xd_default_season['SeasonID'])) {
		$query = $xoopsDB->query("SELECT SeasonID FROM ".$xoopsDB->prefix("matchdart_seasonnames")." ORDER BY SeasonId ASC LIMIT 1");
		$xd_default_season = $xoopsDB->fetchArray($query);
	}
	$xd_default_seasonid =  $xd_default_season['SeasonID'];
	Return $xd_default_seasonid;
}

/**
 * xd_getdefaulteventid
 *
 * Returns default event id
 *
 * @package matchdart
 * @author wild0ne (mailto:wild0ne@partypilger.de)
 * @copyright (c) wild0ne
 * @param
 */
function xd_getdefaulteventid()
{
	global $xoopsDB;

	$query = $xoopsDB->query("SELECT EventID FROM ".$xoopsDB->prefix("matchdart_eventnames")." WHERE EventDefault=1");

	$xd_default_event = $xoopsDB->fetchArray($query);
	if (!isset($xd_default_event['EventID'])) {
		$query = $xoopsDB->query("SELECT EventID FROM ".$xoopsDB->prefix("matchdart_eventnames")." ORDER BY EventId ASC LIMIT 1");
		$xd_default_event = $xoopsDB->fetchArray($query);
	}
	$xd_default_eventid =  $xd_default_event['EventID'];
	Return $xd_default_eventid;
}

/**
 * xd_getdefaultmatchtypeid
 *
 * Returns default matchtype id
 *
 * @package matchdart
 * @author wild0ne (mailto:wild0ne@partypilger.de)
 * @copyright (c) wild0ne
 * @param $eventid    get default matchtype for related event
 */
function xd_getdefaultmatchtypeid($eventid)
{
	global $xoopsDB;

	$query = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("matchdart_eventmatchtypes").
		" WHERE MatchTypeEventID = '$eventid'
	ORDER BY MatchTypeID LIMIT 1");
	$mt_default_matchtype = $xoopsDB->fetchArray($query);
	$mt_default_matchtypeid = $mt_default_matchtype['MatchTypeID'];
	Return $mt_default_matchtypeid;
}

/**
 * xd_getseasons
 *
 * Returns seasons (Array) for select box
 *
 * @package matchdart
 * @author wild0ne (mailto:wild0ne@partypilger.de)
 * @copyright (c) wild0ne
 * @param
 */
function xd_getseasons()
{
	global $xoopsDB;
	$seasons = array();
	$q = 1;
	$query = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("matchdart_seasonnames")." WHERE SeasonPublish = '1' ORDER BY SeasonName");
	while($myrow = $xoopsDB->fetchArray($query) )
	{
		$seasons[$q]['id'] = $myrow['SeasonID'];
		$seasons[$q]['name'] = $myrow['SeasonName'];
		$q++;
	}
	Return $seasons;
}

/**
 * xd_getevents
 *
 * Returns events (Array) for select box
 *
 * @package matchdart
 * @author wild0ne (mailto:wild0ne@partypilger.de)
 * @copyright (c) wild0ne
 * @param
 */
function xd_getevents()
{
	global $xoopsDB;
	$events = array();
	$q = 1;
	$query = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("matchdart_eventnames")." WHERE EventPublish = '1' ORDER BY EventName");
	while($myrow = $xoopsDB->fetchArray($query) )
	{
		$events[$q]['id'] = $myrow['EventID'];
		$events[$q]['name'] = $myrow['EventName'];
		$q++;
	}
	Return $events;
}

/**
 * xd_getplayers
 *
 * Returns players (Array) for select box
 *
 * @package matchdart
 * @author wild0ne (mailto:wild0ne@partypilger.de)
 * @copyright (c) wild0ne
 * @param
 */
function xd_getplayers()
{
	global $xoopsDB;
	$players=array();
	$q=1;
	$query = $xoopsDB->query("SELECT PlayerID, PlayerName
		FROM ".$xoopsDB->prefix("matchdart_players").
		" ORDER BY PlayerName");
	while($myrow = $xoopsDB->fetchArray($query) )
	{
		$players[$q]['id'] = $myrow['PlayerID'];
		$players[$q]['name'] = $myrow['PlayerName'];
		$q++;
	}
	Return $players;
}

/**
 * xd_getmyplayerid
 *
 * Returns playerid for registered xoops user
 *
 * @package matchdart
 * @author wild0ne (mailto:wild0ne@partypilger.de)
 * @copyright (c) wild0ne
 * @param
 */
function xd_getmyplayerid($uid)
{
	global $xoopsDB;
	if ($uid == -1)
	{
		$query = $xoopsDB->query("SELECT PlayerID
			FROM ".$xoopsDB->prefix("matchdart_players").
			" ORDER BY PlayerName ASC LIMIT 1");
		$myplayer = $xoopsDB->fetchArray($query);
		//print_r($myplayer);
		if($xoopsDB->getRowsNum($query) < 1) {
			$myplayerid = -1;
		} else {
			$myplayerid = $myplayer['PlayerID'];
		}
	} elseif ($uid == 'pvpp2') {
		$query = $xoopsDB->query("SELECT PlayerID
			FROM ".$xoopsDB->prefix("matchdart_players").
			" ORDER BY PlayerName DESC LIMIT 1");
		$myplayer = $xoopsDB->fetchArray($query);
		//print_r($myplayer);
		if($xoopsDB->getRowsNum($query) < 1) {
			$myplayerid = -1;
		} else {
			$myplayerid = $myplayer['PlayerID'];
		}
	} else {
		$query = $xoopsDB->query("SELECT PlayerID
			FROM ".$xoopsDB->prefix("matchdart_players").
			" WHERE PlayerUID = '$uid'");
		$myplayer = $xoopsDB->fetchArray($query);
		//print_r($myplayer);
		if($xoopsDB->getRowsNum($query) < 1) {
			$query = $xoopsDB->query("SELECT PlayerID
			FROM ".$xoopsDB->prefix("matchdart_players").
			" ORDER BY PlayerName ASC LIMIT 1");
			$myplayer = $xoopsDB->fetchArray($query);
			//print_r($myplayer);
			if($xoopsDB->getRowsNum($query) < 1) {
				$myplayerid = -1;
			} else {
				$myplayerid = $myplayer['PlayerID'];
			}
		} else {
			$myplayerid = $myplayer['PlayerID'];
		}
	}
	Return $myplayerid;
}


/**
 * xd_getdegradefactor
 *
 * Returns degrade factor if the ranking table is displayed for all seasons
 *
 * ToDo: change base factor to eventdata
 *
 * @package matchdart
 * @author wild0ne (mailto:wild0ne@partypilger.de)
 * @copyright (c) wild0ne
 * @param $matchdate, $eventid
 */
function xd_getdegradefactor($date,$basefactor)
{
	//build the difference of actual year - year of match
	global $xoopsDB;
	//echo "date ",$date,"factor ",$basefactor;
	$yeardiff = intval(date('Y') - substr($date, 0, 4));
	//multiply with correction factor for each year --> factor to the power of yeardifference
	$factor = Pow($basefactor, $yeardiff);
	return $factor;
}

/**
 * xd_cmppoints
 *
 * Returns Array() with the ranking table sorted by points
 *
 *
 * @package matchdart
 * @author wild0ne (mailto:wild0ne@partypilger.de)
 * @copyright (c) wild0ne
 * @param $seasonselect, $eventselect
 */
function xd_cmppoints($a, $b)
{
	if ($a['points'] == $b['points'])
	{
		return 0;
	} else
	{
		return ($a['points'] > $b['points']) ? -1 : 1;
	}
}

/**
 * xd_gettable
 *
 * Returns Array() with the ranking table for selected season & event
 *
 *
 * @package matchdart
 * @author wild0ne (mailto:wild0ne@partypilger.de)
 * @copyright (c) wild0ne
 * @param $seasonselect, $eventselect
 */
function xd_gettable($cont, $char_season, $char_event, $int_factor)
{
	$context = $cont;
	$seasonselect = $char_season;
	$eventselect = $char_event;
	$degradefactor = $int_factor;
	if ($seasonselect == '%')
	{
		$dec_places = '1';
		$dec_sep = '.';
		$thou_sep = ',';

	} else
	{
		$dec_places = '0';
		$dec_sep = '.';
		$thou_sep = ',';
	}

	//echo '$degradefactor ', $degradefactor,'<br>';

	Global $xoopsDB;
	$players = array();
	$q = 1;
	$query = $xoopsDB->query("SELECT DISTINCT
		P.PlayerName,
		P.PlayerID,
		P.PlayerUID 
		FROM ".$xoopsDB->prefix("matchdart_players")." P, ".$xoopsDB->prefix("matchdart_matches")." M
	WHERE M.MatchSeasonID LIKE '$seasonselect' AND
	M.MatchEventID LIKE '$eventselect' AND
	(P.PlayerID = M.MatchHomeID OR
	P.PlayerID = M.MatchAwayID)
	ORDER BY P.PlayerID
		");

	while($myrow = $xoopsDB->fetchArray($query) )
	{
		$players[$q]['id'] = $myrow['PlayerID'];
		$players[$q]['name'] = $myrow['PlayerName'];
		$players[$q]['uid'] = $myrow['PlayerUID'];

		$players[$q]['pos'] = 0;
		$players[$q]['homewins'] = 0;
		$players[$q]['homewinpoints'] = 0;
		$players[$q]['homeloss'] = 0;
		$players[$q]['homelosspoints'] = 0;
		$players[$q]['homedraw'] = 0;
		$players[$q]['homedrawpoints'] = 0;
		$players[$q]['homesets'] = 0;
		$players[$q]['homelegs'] = 0;
		$players[$q]['awaywins'] = 0;
		$players[$q]['awaywinpoints'] = 0;
		$players[$q]['awayloss'] = 0;
		$players[$q]['awaylosspoints'] = 0;
		$players[$q]['awaydraw'] = 0;
		$players[$q]['awaydrawpoints'] = 0;
		$players[$q]['awaysets'] = 0;
		$players[$q]['awaylegs'] = 0;
		$players[$q]['homehighfinish'] = 0;
		$players[$q]['homehighfinishlost'] = 0;
		$players[$q]['awayhighfinish'] = 0;
		$players[$q]['awayhighfinishlost'] = 0;
		$players[$q]['homematches'] = 0;
		$players[$q]['awaymatches'] = 0;
		$players[$q]['homebonus'] = 0;
		$players[$q]['awaybonus'] = 0;
		$players[$q]['home180'] = 0;
		$players[$q]['away180'] = 0;
		$players[$q]['home180lost'] = 0;
		$players[$q]['away180lost'] = 0;
		$players[$q]['homesetslost'] = 0;
		$players[$q]['awaysetslost'] = 0;
		$players[$q]['homelegslost'] = 0;
		$players[$q]['awaylegslost'] = 0;
			
		//get stats for active player in selected season & event
		$query_stats = $xoopsDB->query("SELECT
				M.*,
				E.EventPointsWin,
				E.EventPointsDraw,
				E.EventPointsLoss
				FROM
				".$xoopsDB->prefix("matchdart_matches")." M, ".$xoopsDB->prefix("matchdart_eventnames")." E 
		WHERE
		M.MatchSeasonID LIKE '$seasonselect' AND
		M.MatchEventID LIKE '$eventselect' AND
		M.MatchEventID = E.EventID AND
		(M.MatchHomeWinnerID = ".$players[$q]['id']." OR
				M.MatchHomeLoserID = ".$players[$q]['id']." OR
				M.MatchAwayWinnerID = ".$players[$q]['id']." OR
				M.MatchAwayLoserID = ".$players[$q]['id']." OR
				M.MatchHomeTieID = ".$players[$q]['id']." OR
				M.MatchAwayTieID = ".$players[$q]['id'].")
				");
		//echo "playerid : ",$players[$q]['id'],"<br>";

		while($stat_row = $xoopsDB->fetchArray($query_stats))
		{
			$eventid = $stat_row['MatchEventID'];
			//echo "--> eventid : $eventid - ";
			//echo "EventPointsWin :",$stat_row['EventPointsWin']," - ";
			//echo "EventPointsLoss :",$stat_row['EventPointsLoss']," - ";
			//echo "HomeWinner :",$stat_row['MatchHomeWinnerID']," - ";
			//echo "AwayWinner :",$stat_row['MatchAwayWinnerID']," - ";

			//Home win
			if($stat_row['MatchHomeWinnerID'] == $players[$q]['id'])
			{
				$players[$q]['homematches']++;
				if($degradefactor == 0 OR $seasonselect!='%')
				{
					$players[$q]['homewins']++;
					$players[$q]['homewinpoints'] = $players[$q]['homewinpoints'] + $stat_row['EventPointsWin'];
				}
				elseif($degradefactor != 0 AND $seasonselect=='%')
				{
					$players[$q]['homewins']++;
					$players[$q]['homewinpoints'] = $players[$q]['homewinpoints'] + xd_getdegradefactor($stat_row['MatchDate'],$degradefactor) * $stat_row['EventPointsWin'];
					//echo "--> homewin / pts :",$players[$q]['homewins']," / ",$players[$q]['homewinpoints']," - ";

				}
			}
			//Home lose
			if($stat_row['MatchHomeLoserID'] == $players[$q]['id'])
			{
				$players[$q]['homematches']++;
				if($degradefactor == 0 OR $seasonselect!='%')
				{
					$players[$q]['homeloss']++;
					$players[$q]['homelosspoints'] = $players[$q]['homelosspoints'] + $stat_row['EventPointsLoss'];
				}
				elseif($degradefactor != 0 AND $seasonselect=='%')
				{
					$players[$q]['homeloss']++;
					$players[$q]['homelosspoints'] = $players[$q]['homelosspoints'] + xd_getdegradefactor($stat_row['MatchDate'],$degradefactor) * $stat_row['EventPointsLoss'];
				}
			}
			//Home draw
			if($stat_row['MatchHomeTieID'] == $players[$q]['id'])
			{
				$players[$q]['homematches']++;
				if($degradefactor == 0 OR $seasonselect!='%')
				{
					$players[$q]['homedraw']++;
					$players[$q]['homedrawpoints'] = $players[$q]['homedrawpoints'] + $stat_row['EventPointsDraw'];
				}
				elseif($degradefactor != 0 AND $seasonselect=='%')
				{
					$players[$q]['homedraw']++;
					$players[$q]['homedrawpoints'] = $players[$q]['homedrawpoints'] + xd_getdegradefactor($stat_row['MatchDate'],$degradefactor) * $stat_row['EventPointsDraw'];
				}
			}
			//Home bonus
			if($stat_row['MatchHomeID'] == $players[$q]['id'])
			{
				if($degradefactor == 0 OR $seasonselect!='%')
				{
					$players[$q]['homebonus'] = $players[$q]['homebonus'] + $stat_row['MatchHomeBonus'];
				}
				elseif($degradefactor != 0 AND $seasonselect=='%')
				{
					$players[$q]['homebonus'] = $players[$q]['homebonus'] + xd_getdegradefactor($stat_row['MatchDate'],$degradefactor) * $stat_row['MatchHomeBonus'];
				}
			}
			//other stats
			if($stat_row['MatchHomeID'] == $players[$q]['id'])
			{
				//sets
				$players[$q]['homesets'] = $players[$q]['homesets'] + $stat_row['MatchHomeSets'];
				$players[$q]['homesetslost'] = $players[$q]['homesetslost'] + $stat_row['MatchAwaySets'];
				//legs
				$players[$q]['homelegs'] = $players[$q]['homelegs'] + $stat_row['MatchHomeLegs'];
				$players[$q]['homelegslost'] = $players[$q]['homelegslost'] + $stat_row['MatchAwayLegs'];
				//Highfinish
				if ($stat_row['MatchHomeHighfinish'] > $players[$q]['homehighfinish'])
				{
					$players[$q]['homehighfinish'] = $stat_row['MatchHomeHighfinish'];
				}
				if ($stat_row['MatchAwayHighfinish'] > $players[$q]['homehighfinishlost'])
				{
					$players[$q]['homehighfinishlost'] = $stat_row['MatchAwayHighfinish'];
				}
				//180
				$players[$q]['home180'] = $players[$q]['home180'] + $stat_row['MatchHome180'];
				$players[$q]['home180lost'] = $players[$q]['home180lost'] + $stat_row['MatchAway180'];
			}

			//Away win
			if($stat_row['MatchAwayWinnerID'] == $players[$q]['id'])
			//echo " Auswärtssieg! ",$degradefactor," ",$seasonselect;
			{
				$players[$q]['awaymatches']++;
				if($degradefactor == 0 OR $seasonselect!='%')
				{
					$players[$q]['awaywins']++;
					$players[$q]['awaywinpoints'] = $players[$q]['awaywinpoints'] + $stat_row['EventPointsWin'];
				}
				elseif($degradefactor != 0 AND $seasonselect=='%')
				{
					$players[$q]['awaywins']++;
					$players[$q]['awaywinpoints'] = $players[$q]['awaywinpoints'] + xd_getdegradefactor($stat_row['MatchDate'],$degradefactor) * $stat_row['EventPointsWin'];
					//echo "--> awaywin / pts :",$players[$q]['awaywins']," / ",$players[$q]['awaywinpoints']," - ";
				}
			}
			//Away lose
			if($stat_row['MatchAwayLoserID'] == $players[$q]['id'])
			{
				$players[$q]['awaymatches']++;
				if($degradefactor == 0 OR $seasonselect!='%')
				{
					$players[$q]['awayloss']++;
					$players[$q]['awaylosspoints'] = $players[$q]['awaylosspoints'] + $stat_row['EventPointsLoss'];
				}
				elseif($degradefactor != 0 AND $seasonselect=='%')
				{
					$players[$q]['awayloss']++;
					$players[$q]['awaylosspoints'] = $players[$q]['awaylosspoints'] + xd_getdegradefactor($stat_row['MatchDate'],$degradefactor) * $stat_row['EventPointsLoss'];
				}
			}
			//Away draw
			if($stat_row['MatchAwayTieID'] == $players[$q]['id'])
			{
				$players[$q]['awaymatches']++;
				if($degradefactor == 0 OR $seasonselect!='%')
				{
					$players[$q]['awaydraw']++;
					$players[$q]['awaydrawpoints'] = $players[$q]['awaydrawpoints'] + $stat_row['EventPointsDraw'];
				}
				elseif($degradefactor != 0 AND $seasonselect=='%')
				{
					$players[$q]['awaydraw']++;
					$players[$q]['awaydrawpoints'] = $players[$q]['awaydrawpoints'] + xd_getdegradefactor($stat_row['MatchDate'],$degradefactor) * $stat_row['EventPointsDraw'];
				}
			}
			//Away bonus
			if($stat_row['MatchAwayID'] == $players[$q]['id'])
			{
				if($degradefactor == 0 OR $seasonselect!=0)
				{
					$players[$q]['awaybonus'] = $players[$q]['awaybonus'] + $stat_row['MatchAwayBonus'];
				}
				elseif($degradefactor != 0 AND $seasonselect=='%')
				{
					$players[$q]['awaybonus'] = $players[$q]['awaybonus'] + xd_getdegradefactor($stat_row['MatchDate'],$degradefactor) * $stat_row['MatchAwayBonus'];
				}
			}
			//other stats
			if($stat_row['MatchAwayID'] == $players[$q]['id'])
			{
				//sets
				$players[$q]['awaysets'] = $players[$q]['awaysets'] + $stat_row['MatchAwaySets'];
				$players[$q]['awaysetslost'] = $players[$q]['awaysetslost'] + $stat_row['MatchHomeSets'];
				//sets
				$players[$q]['awaylegs'] = $players[$q]['awaylegs'] + $stat_row['MatchAwayLegs'];
				$players[$q]['awaylegslost'] = $players[$q]['awaylegslost'] + $stat_row['MatchHomeLegs'];
				//Highfinish
				if ($stat_row['MatchAwayHighfinish'] > $players[$q]['awayhighfinish'])
				{
					$players[$q]['awayhighfinish'] = $stat_row['MatchAwayHighfinish'];
				}
				if ($stat_row['MatchHomeHighfinish'] > $players[$q]['awayhighfinishlost'])
				{
					$players[$q]['awayhighfinishlost'] = $stat_row['MatchHomeHighfinish'];
				}
				//180
				$players[$q]['away180'] = $players[$q]['away180'] + $stat_row['MatchAway180'];
				$players[$q]['away180lost'] = $players[$q]['away180lost'] + $stat_row['MatchHome180'];
			}
			//echo"<br>";
		} //end calculation for player[$q]

		$players[$q]['matches'] = $players[$q]['homematches'] + $players[$q]['awaymatches'];
		$players[$q]['wins'] = $players[$q]['homewins'] + $players[$q]['awaywins'];
		$players[$q]['winpoints'] = $players[$q]['homewinpoints'] + $players[$q]['awaywinpoints'];
		$players[$q]['loss'] = $players[$q]['homeloss'] + $players[$q]['awayloss'];
		$players[$q]['losspoints'] = $players[$q]['homelosspoints'] + $players[$q]['awaylosspoints'];
		$players[$q]['draw'] = $players[$q]['homedraw'] + $players[$q]['awaydraw'];
		$players[$q]['drawpoints'] = $players[$q]['homedrawpoints'] + $players[$q]['awaydrawpoints'];
		$players[$q]['bonus'] = $players[$q]['homebonus'] + $players[$q]['awaybonus'];
		$players[$q]['points'] = number_format(round($players[$q]['winpoints'] + $players[$q]['losspoints'] + $players[$q]['drawpoints'] + $players[$q]['bonus'],1),$dec_places,$dec_sep,$thou_sep);
		$players[$q]['sets'] = $players[$q]['homesets'] + $players[$q]['awaysets'];
		$players[$q]['setslost'] = $players[$q]['homesetslost'] + $players[$q]['awaysetslost'];
		$players[$q]['legs'] = $players[$q]['homelegs'] + $players[$q]['awaylegs'];
		$players[$q]['legslost'] = $players[$q]['homelegslost'] + $players[$q]['awaylegslost'];
		$players[$q]['180'] = $players[$q]['home180'] + $players[$q]['away180'];
		$players[$q]['180lost'] = $players[$q]['home180lost'] + $players[$q]['away180lost'];
		if ($players[$q]['homehighfinish'] >= $players[$q]['awayhighfinish'])
		{
			$players[$q]['highfinish'] = $players[$q]['homehighfinish'];
		} else {
			$players[$q]['highfinish'] = $players[$q]['awayhighfinish'];
		}
		if ($players[$q]['homehighfinishlost'] >= $players[$q]['awayhighfinishlost'])
		{
			$players[$q]['highfinishlost'] = $players[$q]['homehighfinishlost'];
		} else {
			$players[$q]['highfinishlost'] = $players[$q]['awayhighfinishlost'];
		}
		$players[$q]['matchdelta'] = $players[$q]['wins'] - $players[$q]['loss'];
		$players[$q]['setdelta'] = $players[$q]['sets'] - $players[$q]['setslost'];
		$q++;
		//echo "<br>";
	}

	Return $players;
}

/**
 * xd_getmoduleoption
 *
 * Returns a module's option
 * copied from News module adapted to matchdart
 * original author: Instant Zero
 *
 * Return's a module's option (for the matchdart module)
 *
 * @package Matchdart
 * @author mawi27 (http://www.matchdart.de)
 * @copyright (c) mawi27
 * @param string $option	module option's name
 */
function xd_getmoduleoption($option, $repmodule='matchdart')
{
	global $xoopsModuleConfig, $xoopsModule;
	static $tbloptions= Array();
	if(is_array($tbloptions) && array_key_exists($option,$tbloptions)) {
		return $tbloptions[$option];
	}

	$retval = false;
	if (isset($xoopsModuleConfig) && (is_object($xoopsModule) && $xoopsModule->getVar('dirname') == $repmodule && $xoopsModule->getVar('isactive'))) {
		if(isset($xoopsModuleConfig[$option])) {
			$retval= $xoopsModuleConfig[$option];
		}
	} else {
		$module_handler =& xoops_gethandler('module');
		$module =& $module_handler->getByDirname($repmodule);
		$config_handler =& xoops_gethandler('config');
		if ($module) {
			$moduleConfig =& $config_handler->getConfigsByCat(0, $module->getVar('mid'));
			if(isset($moduleConfig[$option])) {
				$retval= $moduleConfig[$option];
			}
		}
	}
	$tbloptions[$option]=$retval;
	return $retval;
}

/**
 * xd_checkupdate
 *
 * matches table is altered since version 1.5
 * check if new columns exist
 *
 *
 * @package MatchDart
 * @author mawi27 (http://www.matchdart.de)
 * @copyright (c) mawi27
 */
function xd_checkupdate()
{
	Global $xoopsDB;
	$query = $xoopsDB->query("SELECT MatchHomePPT FROM ".$xoopsDB->prefix("matchdart_matches"));
	Return $query;
}

?>
