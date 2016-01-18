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
 * TPLLeagueStats is a league stats software designed for football (soccer)
 * team.
 * 
 * Copyright (C) 2003  Timo Leppänen / TPL Design
 * email:     info@tpl-design.com
 * www:       www.tpl-design.com/tplleaguestats
 *************************************************************
 * Ported to xoops by 
 * Mythrandir http://www.web-udvikling.dk and 
 * ralf57 http://www.madeinbanzi.it
 *************************************************************
 * Cricket League Version & Modifications by M0nty <vaughan.montgomery@gmail.com>
 * I have tested with XOOPS 2.0.13.1, but i have not tested with XOOPS 2.1 or higher!!!
 * Version 1.0 RC1 | 01-Oct-2005
 *************************************************************
 * 
 *************************************************************
 * MatchDart by wild0ne <info@matchdart.de>
 * developed & tested with XOOPS 2.0.18
 *************************************************************
 * Version 1.4.3 | 06-Feb-2008
 *************************************************************
 * READ manual.txt for installing & other help!!!
 * ************************************************************
 */

//Include file, which checks for permissions and sets navigation
include '../../../include/cp_header.php';
if (@include_once XOOPS_ROOT_PATH."/Frameworks/art/functions.php")
{
	include_once XOOPS_ROOT_PATH."/Frameworks/art/functions.admin.php";
	$noframework = 0;
} else
{
	$noframework = 1;
}


// Season select
if (isset($_POST['season_select'])) {
    $matchdart_season = explode("____",$_POST['season_select']);
}
elseif (isset($_POST['seasonid'])) {
    $matchdart_season = array ($_POST['seasonid'], $_POST['seasonname']);
}
elseif (!isset($_SESSION['season_id'])) {
    $sql = "SELECT SeasonID, SeasonName FROM ".$xoopsDB->prefix("matchdart_seasonnames")." WHERE SeasonDefault=1";
    $matchdart_seasonname = $xoopsDB->query($sql);
    $matchdart_seasonname = $xoopsDB->fetchArray($matchdart_seasonname);
    $matchdart_season = array($matchdart_seasonname['SeasonID'], $matchdart_seasonname['SeasonName']);
}
else {
    $matchdart_season = array($_SESSION['season_id'], $_SESSION['season_name']);
}

// Event select
if (isset($_POST['event_select'])) {
    $matchdart_event = explode("____",$_POST['event_select']);
}
elseif (isset($_POST['eventid'])) {
    $matchdart_event = array ($_POST['eventid'], $_POST['eventname']);
}
elseif (!isset($_SESSION['event_id'])) {
    $sql2 = "SELECT EventID, EventName FROM ".$xoopsDB->prefix("matchdart_eventnames")." WHERE EventDefault=1";
    $matchdart_eventname = $xoopsDB->query($sql2);
    $matchdart_eventname = $xoopsDB->fetchArray($matchdart_eventname);
    $matchdart_event = array($matchdart_eventname['EventID'], $matchdart_eventname['EventName']);
}
else {
    $matchdart_event = array($_SESSION['event_id'], $_SESSION['event_name']);
}



$_SESSION['season_id'] = $matchdart_season[0];
$_SESSION['season_name'] = $matchdart_season[1];
$matchdart_seasonid = $_SESSION['season_id'];
$matchdart_seasonname = $_SESSION['season_name'];
$_SESSION['event_id'] = $matchdart_event[0];
$_SESSION['event_name'] = $matchdart_event[1];
$matchdart_eventid = $_SESSION['event_id'];
$matchdart_eventname = $_SESSION['event_name'];

$PHP_SELF = $_SERVER['PHP_SELF'];
$matchdart_action = isset($_GET['action']) ? $_GET['action'] : null;
$matchdart_action = isset($_POST['action']) ? $_POST['action'] : $matchdart_action;

$matchdart_add_submit = isset($_POST['add_submit']) ? $_POST['add_submit'] : false;
$matchdart_modify_submit = isset($_POST['modify_submit']) ? $_POST['modify_submit'] : false;
$matchdart_delete_submit = isset($_POST['delete_submit']) ? $_POST['delete_submit'] : false;
$matchdart_modifyall_submit = isset($_POST['modifyall_submit']) ? $_POST['modifyall_submit']: null;

xoops_cp_header();
//if ($noframework == 0){loadModuleAdminMenu(4);}
//if ($noframework == 1){include 'navbar.php';}

//Exit check, if there are less than 2 teams in database independent of season and event - wild0ne
$query = $xoopsDB->query("SELECT PlayerID FROM ".$xoopsDB->prefix("matchdart_players"));


if($xoopsDB->getRowsNum($query) < 2)
{
    echo "<br><br>"._AM_XD_ADDTWOPLAYERS."<br><br>
		<a href=\"players.php\">" ._AM_XD_ADDPLAYERS. "</a>";
    exit();
}

if($matchdart_add_submit)
{
	$year = $matchdart_seasonname;
    $month = intval($_POST['month']);
    $day = intval($_POST['day']);
    $dateandtime = $year."-".$month."-".$day;
    
    $i = 0;
    
    while($i < 15)
    {
    	$home = $_POST['home'];	//home team id
        $home_bpoints = $_POST['home_bpoints']; //home team bonus points
        $home_sets = $_POST['home_sets'];
        $home_highfinish = $_POST['home_highfinish'];
        $home_180 = $_POST['home_180'];
        
        $away = $_POST['away'];	//away team id
        $away_bpoints = $_POST['away_bpoints']; //home team bonus points
        $away_sets = $_POST['away_sets'];
        $away_highfinish = $_POST['away_highfinish'];
        $away_180 = $_POST['away_180'];
        
        //Set the default
        $home_bonus = -1;
		$away_bonus = -1;
		$home_winner = -1;
        $home_loser = -1;
        $home_tie = -1;
        $away_winner = -1;
        $away_loser = -1;
        $away_tie = -1;
        
        //If home and away are not the same
        if($home[$i] != $away[$i])
        {
            $home[$i] = intval($home[$i]);
            $away[$i] = intval($away[$i]);
            $home_bpoints[$i] = $home_bpoints[$i] != null ? intval($home_bpoints[$i]) : null;
            $away_bpoints[$i] = $away_bpoints[$i] != null ? intval($away_bpoints[$i]) : null;
            $home_sets[$i] = $home_sets[$i] != null ? intval($home_sets[$i]) : null;
            $home_highfinish[$i] = $home_highfinish[$i] != null ? intval($home_highfinish[$i]) : null;
            $away_sets[$i] = $away_sets[$i] != null ? intval($away_sets[$i]) : null;
            $away_highfinish[$i] = $away_highfinish[$i] != null ? intval($away_highfinish[$i]) : null;

            //Home team wins
            if($home_sets[$i] > $away_sets[$i])
            {
                $home_winner = $home[$i];
                $away_loser = $away[$i];
				$home_bonus = $home_bpoints[$i];
				$away_bonus = $away_bpoints[$i];
            }
            
            //Away win
            elseif($home_sets[$i] < $away_sets[$i])
            {
                $away_winner = $away[$i];
                $home_loser = $home[$i];
				$home_bonus = $home_bpoints[$i];
				$away_bonus = $away_bpoints[$i];
            }
            
            //Draw
            elseif($home_sets[$i] == $away_sets[$i])
            {
                $home_tie = $home[$i];
                $away_tie = $away[$i];
				$home_bonus = $home_bpoints[$i];
				$away_bonus = $away_bpoints[$i];
            }
            
                if (($home_sets[$i] !== null) && ($away_sets[$i] !== null)) {
                	$xoopsDB->query("INSERT INTO ".$xoopsDB->prefix("matchdart_matches")." SET
						MatchSeasonID = '$matchdart_seasonid',
						MatchEventID = '$matchdart_eventid',
						MatchDate = '$dateandtime',
						MatchHomeID = '$home[$i]',
						MatchAwayID = '$away[$i]',
						MatchHomeWinnerID = '$home_winner',
						MatchHomeLoserID = '$home_loser',
						MatchHomeBonus = '$home_bonus',
						MatchAwayBonus = '$away_bonus',
						MatchHomeBpoints = '$home_bpoints[$i]',
						MatchAwayBpoints = '$away_bpoints[$i]',
						MatchAwayWinnerID = '$away_winner',
						MatchAwayLoserID = '$away_loser',
						MatchHomeTieID = '$home_tie',
						MatchAwayTieID = '$away_tie',
						MatchHomeSets = '$home_sets[$i]',
						MatchHomeHighfinish = '$home_highfinish[$i]',
						MatchAwaySets = '$away_sets[$i]',
						MatchAwayHighfinish = '$away_highfinish[$i]',
                        MatchCreated = ".time().",
                        MatchHome180 = '$home_180[$i]',
                        MatchAway180 = '$away_180[$i]'
                    	");
                	
                }
                else {
				  $xoopsDB->query("INSERT INTO ".$xoopsDB->prefix("matchdart_matches")." SET
						MatchSeasonID = '$matchdart_seasonid',
						MatchEventID = '$matchdart_eventid',
						MatchDate = '$dateandtime',
						MatchHomeID = '$home[$i]',
						MatchAwayID = '$away[$i]',
						MatchHomeBonus = '-1',
						MatchAwayBonus = '-1',
						MatchHomeWinnerID = '-1',
						MatchHomeLoserID = '-1',
						MatchAwayWinnerID = '-1',
						MatchAwayLoserID = '-1',
						MatchHomeTieID = '-1',
						MatchAwayTieID = '-1',
                        MatchCreated = ".time().",
                        MatchHome180 = '$home_180[$i]',
                        MatchAway180 = '$away_180[$i]'
						");
                }
        }
        $i++;
    }
}
elseif($matchdart_modifyall_submit)
{
	$year = $matchdart_seasonname;
    $month = intval($_POST['month']);
    $day = intval($_POST['day']);
    $dateandtime = $year."-".$month."-".$day;
    $qty = intval($_POST['qty']);
    
    //Delete old data from selected date
    $xoopsDB->query("DELETE FROM ".$xoopsDB->prefix("matchdart_matches")."
		WHERE MatchDate = '$dateandtime'
		");
    
    //Check the submitted form
    
	$i = 0;
    $home = $_POST['home'];	//home team id
    $home_bpoints = $_POST['home_bpoints']; //home team bonus points
    $home_sets = $_POST['home_sets'];
    $home_highfinish = $_POST['home_highfinish'];
    $home_180 = $_POST['home_180'];
    
    $away = $_POST['away'];	//away team id
    $away_bpoints = $_POST['away_bpoints']; //home team bonus points
    $away_sets = $_POST['away_sets'];
    $away_highfinish = $_POST['away_highfinish'];
    $away_180 = $_POST['away_180'];

    while($i < $qty)
    {
        $home[$i] = intval($home[$i]);
        $away[$i] = intval($away[$i]);
        $home_bpoints[$i] = $home_bpoints[$i] != null ? intval($home_bpoints[$i]) : null;
        $away_bpoints[$i] = $away_bpoints[$i] != null ? intval($away_bpoints[$i]) : null;
        $home_sets[$i] = $home_sets[$i] != null ? intval($home_sets[$i]) : null;
        $home_highfinish[$i] = $home_highfinish[$i] != null ? intval($home_highfinish[$i]) : null;
        $away_sets[$i] = $away_sets[$i] != null ? intval($away_sets[$i]) : null;
        $away_highfinish[$i] = $away_highfinish[$i] != null ? intval($away_highfinish[$i]) : null;

        //Set default
        $home_bonus = -1;
		$away_bonus = -1;
        $home_winner = -1;
        $home_loser = -1;
        $home_tie = -1;
        $away_winner = -1;
        $away_loser = -1;
        $away_tie = -1;
        
        //Home wins
        if($home_sets[$i] > $away_sets[$i])
        {
            $home_winner = $home[$i];
            $away_loser = $away[$i];
			$home_bonus = $home_bpoints[$i];
			$away_bonus = $away_bpoints[$i];
        }

        //Away wins
        elseif($home_sets[$i] < $away_sets[$i])
        {
            $away_winner = $away[$i];
            $home_loser = $home[$i];
			$home_bonus = $home_bpoints[$i];
			$away_bonus = $away_bpoints[$i];
        }

        //Draw
        elseif($home_sets[$i] == $away_sets[$i])
        {
            $home_tie = $home[$i];
            $away_tie = $away[$i];
			$home_bonus = $home_bpoints[$i];
			$away_bonus = $away_bpoints[$i];
        }

        if (($home_sets[$i] !== null) && ($away_sets[$i] !== null)) {
        	            $xoopsDB->query("INSERT INTO ".$xoopsDB->prefix("matchdart_matches")." SET
				MatchSeasonID = '$matchdart_seasonid',
				MatchEventID = '$matchdart_eventid',
				MatchDate = '$dateandtime',
				MatchHomeID = '$home[$i]',
				MatchAwayID = '$away[$i]',
				MatchHomeBonus = '$home_bonus',
				MatchAwayBonus = '$away_bonus',
				MatchHomeBpoints = '$home_bpoints[$i]',
				MatchAwayBpoints = '$away_bpoints[$i]',
				MatchHomeWinnerID = '$home_winner',
				MatchHomeLoserID = '$home_loser',
				MatchAwayWinnerID = '$away_winner',
				MatchAwayLoserID = '$away_loser',
				MatchHomeTieID = '$home_tie',
				MatchAwayTieID = '$away_tie',
				MatchHomeSets = '$home_sets[$i]',
				MatchHomeHighfinish = '$home_highfinish[$i]',
				MatchAwaySets = '$away_sets[$i]',
				MatchAwayHighfinish = '$away_highfinish[$i]',
                MatchCreated = ".time().",
                        MatchHome180 = '$home_180[$i]',
                        MatchAway180 = '$away_180[$i]'
				");
        }
        else {
        	            $xoopsDB->query("INSERT INTO ".$xoopsDB->prefix("matchdart_matches")." SET
						MatchSeasonID = '$matchdart_seasonid',
						MatchEventID = '$matchdart_eventid',
						MatchDate = '$dateandtime',
						MatchHomeID = '$home[$i]',
						MatchAwayID = '$away[$i]',
						MatchHomeBonus = '-1',
						MatchAwayBonus = '-1',
						MatchHomeWinnerID = '-1',
						MatchHomeLoserID = '-1',
						MatchAwayWinnerID = '-1',
						MatchAwayLoserID = '-1',
						MatchHomeTieID = '-1',
						MatchAwayTieID = '-1',
                        MatchCreated = ".time().",
                        MatchHome180 = '$home_180[$i]',
                        MatchAway180 = '$away_180[$i]'
						");
        }            
        $i++;
    }
}
elseif($matchdart_modify_submit)
{
    $mid = intval($_POST['mid']);
    $homeid = intval($_POST['homeid']);
    $awayid = intval($_POST['awayid']);
    $year = $matchdart_seasonname;
    $month = intval($_POST['month']);
    $day = intval($_POST['day']);
    $dateandtime = $year."-".$month."-".$day;

    $home = intval($_POST['home']);	//home team id
    $home_bpoints = $_POST['home_bpoints'] != null ? intval($_POST['home_bpoints']) : null;
    $home_sets = $_POST['home_sets'] != null ? intval($_POST['home_sets']) : null;
    $home_highfinish = $_POST['home_highfinish'] != null ? intval($_POST['home_highfinish']) : null;
    $home_180 = $_POST['home_180'];
    
    $away = intval($_POST['away']);	//away team id
    $away_bpoints = $_POST['away_bpoints'] != null ? intval($_POST['away_bpoints']) : null;
    $away_sets = $_POST['home_sets'] != null ? intval($_POST['away_sets']) : null;
    $away_highfinish = $_POST['home_highfinish'] != null ? intval($_POST['away_highfinish']) : null;
    $away_180 = $_POST['away_180'];
    
    //Set default
    $home_bonus = -1;
    $home_bonus = -1;
    $home_winner = -1;
    $home_loser = -1;
    $home_tie = -1;
    $away_winner = -1;
    $away_loser = -1;
    $away_tie = -1;
    
    //Check that home and away are not the same
    if($home != $away)
    {
        //Home wins
        if($home_sets > $away_sets)
        {
            $home_winner = $home;
            $away_loser = $away;
            $home_bonus = $home_bpoints;
            $away_bonus = $away_bpoints;
        }
        
        //Away wins
        elseif($home_sets < $away_sets)
        {
            $away_winner = $away;
            $home_loser = $home;
            $home_bonus = $home_bpoints;
            $away_bonus = $away_bpoints;
        }
        
        //Draw
        elseif($home_sets == $away_sets)
        {
            $home_tie = $home;
            $away_tie = $away;
            $home_bonus = $home_bpoints;
            $away_bonus = $away_bpoints;
        }
        
            if (($home_sets !== null) && ($away_sets !== null)) {
            	                $xoopsDB->query("UPDATE ".$xoopsDB->prefix("matchdart_matches")." SET
					MatchDate = '$dateandtime',
					MatchHomeID = '$home',
					MatchAwayID = '$away',
					MatchHomeBonus = '$home_bonus',
					MatchAwayBonus = '$away_bonus',
					MatchHomeBpoints = '$home_bpoints',
					MatchAwayBpoints = '$away_bpoints',
					MatchHomeWinnerID = '$home_winner',
					MatchHomeLoserID = '$home_loser',
					MatchAwayWinnerID = '$away_winner',
					MatchAwayLoserID = '$away_loser',
					MatchHomeTieID = '$home_tie',
					MatchAwayTieID = '$away_tie',
					MatchHomeSets = '$home_sets',
					MatchHomeHighfinish = '$home_highfinish',
					MatchAwaySets = '$away_sets',
					MatchAwayHighfinish = '$away_highfinish',
                    MatchCreated = ".time().",
                        MatchHome180 = '$home_180[$i]',
                        MatchAway180 = '$away_180[$i]'
					WHERE MatchID = '$mid'
					LIMIT 1
					");
            }
            else {
            	                $xoopsDB->query("UPDATE ".$xoopsDB->prefix("matchdart_matches")." SET
					MatchDate = '$dateandtime',
					MatchHomeID = '$home',
					MatchAwayID = '$away',
					MatchHomeBonus = '-1',
					MatchAwayBonus = '-1',
					MatchHomeBpoints = NULL,
					MatchAwayBpoints = NULL,
					MatchHomeWinnerID = '-1',
					MatchHomeLoserID = '-1',
					MatchAwayWinnerID = '-1',
					MatchAwayLoserID = '-1',
					MatchHomeTieID = '-1',
					MatchAwayTieID = '-1',
					MatchHomeSets = NULL,
					MatchHomeHighfinish = NULL,
					MatchAwaySets = NULL,
					MatchAwayHighfinish = NULL,
                    MatchCreated = ".time().",
                        MatchHome180 = '$home_180[$i]',
                        MatchAway180 = '$away_180[$i]'
					WHERE MatchID = '$mid'
					LIMIT 1
					");
            }                
    }
}
elseif($matchdart_delete_submit)
{
    $mid = intval($_POST['mid']);
    $xoopsDB->query("DELETE FROM ".$xoopsDB->prefix("matchdart_matches")." WHERE MatchID = '$mid' LIMIT 1");
}

	?>

        <?php
	include('head.php');
	include('eventhead.php');
	?>

	<table align="center" width="700">
		<tr>
		<td align="left" valign="top">
		<form method="post" action="<?php echo "$PHP_SELF"?>">
		<?php
		if(!isset($matchdart_action))
		{
		?>
		<h3><?php echo _AM_XD_ADDMATCH;?></h3>
		<?php echo _AM_XD_ADDMATCHNOTE;?><br><br>

		<?php echo _AM_XD_DATE;?>
		<select name="day">
		<?php
		//print the days
		for($i = 1 ; $i < 32 ; $i++)
		{
		    if($i<10)
		    {
		        $i = "0".$i;
		    }
		    if($i == "01")
		    echo "<option value=\"$i\" SELECTED>$i</option>\n";
		    else
		    echo "<option value=\"$i\">$i</option>\n";
		}
		?>
		</select>&nbsp;/&nbsp;

		<select name="month">
		<?php
		//print the months
		for($i = 1 ; $i < 13 ; $i++)
		{
		    if($i<10)
		    {
		        $i = "0".$i;
		    }
		    if($i == "01")
		    echo "<option value=\"$i\" SELECTED>$i</option>\n";
		    else
		    echo "<option value=\"$i\">$i</option>\n";
		}
		?>
		</select>&nbsp;/&nbsp;

		<select name="year">
		<?php
		//print the years
		for($i = $matchdart_seasonname ; $i < $matchdart_seasonname+1 ; $i++)
		{
		    if($i<10)
		    {
		        $i = "0".$matchdart_seasonname;
		    } 
		    if($i == $matchdart_seasonname)
		    echo "<option value=\"$i\" SELECTED>$i</option>\n";
		    else
		    echo "<option value=\"$i\">$i</option>\n";
		}
		?>
		</select><br><br>
		<?php echo _AM_XD_ADDMATCHNOTE2;?><br><br>

		<table width="100%" cellspacing="3" cellpadding="3" border="0">
		<tr>
		<td align="left" valign="middle"><b><?php echo _AM_XD_HOMEPLAYER;?></b></td>
		<td align="center" valign="middle"><b><?php echo _AM_XD_180,'&nbsp;',_AM_XD_BONUSPOINTS;?></b></td>
		<td align="center" valign="middle"><b><?php echo _AM_XD_SETS;?></b></td>
		<td align="center" valign="middle"><b><?php echo _AM_XD_HIGHFINISH;?></b></td>
		<td align="left" valign="middle"><b><?php echo _AM_XD_AWAYPLAYER;?></b></td>
		<td align="center" valign="middle"><b><?php echo _AM_XD_180,'&nbsp;',_AM_XD_BONUSPOINTS;?></b></td>
		<td align="center" valign="middle"><b><?php echo _AM_XD_SETS;?></b></td>
		<td align="center" valign="middle"><b><?php echo _AM_XD_HIGHFINISH;?></b></td>
		</tr>

		<?php
		
		//query to get all the teams independent of season and event
		$matchdart_get_players = $xoopsDB->query("SELECT PlayerID AS id,
		PlayerName AS name
		FROM ".$xoopsDB->prefix("matchdart_players")." ORDER BY PlayerName");
		
		//Prints 15 forms
		$i=0;
		
		while($i < 15)
		{
	    //query back to row 0 if not the first time in the loop
		    if($i>0)
		    mysql_data_seek($matchdart_get_players, 0);
		    
		    echo'
		    <tr>
			<td align="left" valign="middle">
			';
		    
		    echo"<select name=\"home[$i]\">";
		    
		    while($matchdart_data = $xoopsDB->fetchArray($matchdart_get_players))
		    {
		        echo"<option value=\"$matchdart_data[id]\">$matchdart_data[name]</option>\n";
		    }
		    
		    echo'
			</select>
			</td>
			';
			
			echo"
			<td align=\"center\" valign=\"middle\"><input type=\"text\" name=\"home_180[$i]\" size=\"2\">&nbsp;<input type=\"text\" name=\"home_bpoints[$i]\" size=\"2\"></td>
			<td align=\"center\" valign=\"middle\"><input type=\"text\" name=\"home_sets[$i]\" size=\"2\"></td>
			<td align=\"center\" valign=\"middle\"><input type=\"text\" name=\"home_highfinish[$i]\" size=\"2\"></td>
			";
			
			echo'
			<td align="left" valign="middle">
			';
		    
		    //Back to line 0 in the query
		    mysql_data_seek($matchdart_get_players, 0);
		    
		    echo"<select name=\"away[$i]\">";
		    
		    while($matchdart_data = $xoopsDB->fetchArray($matchdart_get_players))
		    {
		        echo"<option value=\"$matchdart_data[id]\">$matchdart_data[name]</option>\n";
		    }
		    
		    echo"
			</select>
			</td>
			";
			
			echo"
			<td align=\"center\" valign=\"middle\"><input type=\"text\" name=\"away_180[$i]\" size=\"2\">&nbsp;<input type=\"text\" name=\"away_bpoints[$i]\" size=\"2\"></td>
			<td align=\"center\" valign=\"middle\"><input type=\"text\" name=\"away_sets[$i]\" size=\"2\"></td>
			<td align=\"center\" valign=\"middle\"><input type=\"text\" name=\"away_highfinish[$i]\" size=\"2\"></td>
			</tr>
			";
		    
			
			$i++;
		}
		?>

		</table><br><br>
        <input type="hidden" name="seasonid" value="<?php echo $matchdart_seasonid; ?>">
        <input type="hidden" name="seasonname" value="<?php echo $matchdart_seasonname; ?>">
        <input type="hidden" name="eventid" value="<?php echo $matchdart_eventid; ?>">
        <input type="hidden" name="eventname" value="<?php echo $matchdart_eventname; ?>">
		<input type="submit" name="add_submit" value="<?php echo _AM_XD_ADDMATCHES;?>">
		</form>
		<?php
		}
		elseif($matchdart_action == 'modifyall')
		{
		    $date = $_REQUEST['date'];
		    
		    		$matchdart_get_matches = $xoopsDB->query("SELECT DAYOFMONTH(LM.MatchDate) AS dayofmonth,
		MONTH(LM.MatchDate) AS month,
		YEAR(LM.MatchDate) AS year,
		LM.MatchHomeID AS homeid,
		LM.MatchAwayID AS awayid,
		LM.MatchHomeBpoints AS homebpoints,
		LM.MatchAwayBpoints AS awaybpoints,
		LM.MatchHomeSets AS homesets,
		LM.MatchHomeHighfinish AS homehighfinish,
		LM.MatchAwaySets AS awaysets,
		LM.MatchAwayHighfinish AS awayhighfinish,
		LM.MatchHome180 AS home180,
		LM.MatchAway180 AS away180
		FROM ".$xoopsDB->prefix("matchdart_matches")." LM
		WHERE LM.MatchDate = '$date'
		");
		    
		//query to get date
		$matchdart_get_match = $xoopsDB->query("SELECT DAYOFMONTH(LM.MatchDate) AS dayofmonth,
		MONTH(LM.MatchDate) AS month,
		YEAR(LM.MatchDate) AS year
		FROM ".$xoopsDB->prefix("matchdart_matches")." LM
		WHERE LM.MatchDate = '$date'
		LIMIT 1
		");
		    
		    $datedata = $xoopsDB->fetchArray($matchdart_get_match);
		
		//query to get all the teams independent of season and event
		$matchdart_get_players = $xoopsDB->query("SELECT PlayerID AS id,
		PlayerName AS name
		FROM ".$xoopsDB->prefix("matchdart_players")." ORDER BY PlayerName");
		
		?>

		<form method="post" action="<?php echo "$PHP_SELF" ?>">
		<h3><?php echo _AM_XD_MODMATCHES;?></h3>

		<table width="100%" cellspacing="3" cellpadding="3" border="0">

			<tr>
				<td align="left" valign="top">
				<?php echo _AM_XD_DATETIME;?>
				</td>
				<td align="left" valign="top">

				<select name="day">
				<?php
				//Print the days
				for($i = 1 ; $i < 32 ; $i++)
				{
				    if($i<10)
				    {
				        $i = "0".$i;
				    }
				    if($datedata['dayofmonth'] == $i)
				    echo "<option value=\"$i\" SELECTED>$i</option>\n";
				    else
				    echo "<option value=\"$i\">$i</option>\n";
				}
				?>
				</select>&nbsp;/&nbsp;

				<select name="month">
				<?php
				//Print the months
				for($i = 1 ; $i < 13 ; $i++)
				{
				    if($i<10)
				    {
				        $i = "0".$i;
				    }
				    if($datedata['month'] == $i)
				    echo "<option value=\"$i\" SELECTED>$i</option>\n";
				    else
				    echo "<option value=\"$i\">$i</option>\n";
				}
				?>
				</select>&nbsp;/&nbsp;

				<select name="year">
				<?php
				//Print the years
				for($i = $matchdart_seasonname ; $i < $matchdart_seasonname+1 ; $i++)
				{
				    if($i<10)
				    {
				        $i = "0".$matchdart_seasonname;
				    } 
				    if($datedata['year'] == $matchdart_seasonname)
				    echo "<option value=\"$i\" SELECTED>$i</option>\n";
				    else
				    echo "<option value=\"$i\">$i</option>\n";
				}
				?>
			</select>
			</td>
		</tr>
		</table>

		<table width="100%" cellspacing="3" cellpadding="3" border="0">
		<tr>
		<td align="left" valign="middle"><b><?php echo _AM_XD_HOMEPLAYER;?></b></td>
		<td align="center" valign="middle"><b><?php echo _AM_XD_180,'&nbsp;',_AM_XD_BONUSPOINTS;?></b></td>
		<td align="center" valign="middle"><b><?php echo _AM_XD_SETS;?></b></td>
		<td align="center" valign="middle"><b><?php echo _AM_XD_HIGHFINISH;?></b></td>
		<td align="left" valign="middle"><b><?php echo _AM_XD_AWAYPLAYER;?></b></td>
		<td align="center" valign="middle"><b><?php echo _AM_XD_180,'&nbsp;',_AM_XD_BONUSPOINTS;?></b></td>
		<td align="center" valign="middle"><b><?php echo _AM_XD_SETS;?></b></td>
		<td align="center" valign="middle"><b><?php echo _AM_XD_HIGHFINISH;?></b></td>
		</tr>
		
		<?php
		//Lets get all the matches from selected date to the form
		$i = 0;
		while($matchdata = $xoopsDB->fetchArray($matchdart_get_matches))
		{
		    //Back to line 0 in the query if not the first loop
		    if($i>0)
		    mysql_data_seek($matchdart_get_players, 0);
		    
		    echo'
			<tr>
			<td align="left" valign="middle">
			';
		    
		    echo"<select name=\"home[$i]\">";
		    
		    while($matchdart_data = $xoopsDB->fetchArray($matchdart_get_players))
		    {
		        if($matchdata['homeid'] == $matchdart_data['id'])
		        echo"<option value=\"$matchdart_data[id]\" SELECTED>$matchdart_data[name]</option>\n";
		    }
		    
		    echo'
			</select>
			</td>
			';
			
			echo'
			<td align="center" valign="middle"><input type="text" name="home_180[$i]" size="2" value="$matchdata[home180]">&nbsp;<input type="text" name="home_bpoints[$i]" size="2" value="$matchdata[homebpoints]"></td>
			<td align="center" valign="middle"><input type="text" name="home_sets[$i]" size="2" value="$matchdata[homesets]"></td>
			<td align="center" valign="middle"><input type="text" name="home_highfinish[$i]" size="2" value="$matchdata[homehighfinish]"></td>
			';
			
			echo'
			<td align="left" valign="middle">
			';
		    
		    //Back to line 0 in the query
		    mysql_data_seek($matchdart_get_players, 0);
		    
		    echo"<select name=\"away[$i]\">";
		    
		    while($matchdart_data = $xoopsDB->fetchArray($matchdart_get_players))
		    {
		        if($matchdata['awayid'] == $matchdart_data['id'])
		        echo"<option value=\"$matchdart_data[id]\" SELECTED>$matchdart_data[name]</option>\n";
		    }
		    
		    echo"
			</select>
			</td>
			";
			
			echo'
			<td align="center" valign="middle"><input type="text" name="away_180[$i]" size="2" value="$matchdata[away180]">&nbsp;<input type="text" name="away_bpoints[$i]" size="2" value="$matchdata[awaybpoints]"></td>
			<td align="center" valign="middle"><input type="text" name="away_sets[$i]" size="2" value="$matchdata[awaysets]"></td>
			<td align="center" valign="middle"><input type="text" name="away_highfinish[$i]" size="2" value="$matchdata[awayhighfinish]"></td>
			';
		    
		    $i++;
		}
		?>
		</table>

		<font color="red"><?php echo _AM_XD_MODNOTICE1;?></font><br><br>
		<input type="hidden" name="qty" value="<?= $i ?>">
		<input type="hidden" name="seasonname" value="<?php echo $matchdart_seasonname; ?>">
		<input type="hidden" name="eventname" value="<?php echo $matchdart_eventname; ?>">
		<br><input type="submit" name="modifyall_submit" value="<?php echo _AM_XD_MODINPUT;?>">
		</form>

		<?php
		}
		elseif($matchdart_action == 'modify')
		{
		    $id = intval($_REQUEST['id']);
		    
		    		$matchdart_get_match = $xoopsDB->query("SELECT DAYOFMONTH(LM.MatchDate) AS dayofmonth,
		MONTH(LM.MatchDate) AS month,
		YEAR(LM.MatchDate) AS year,
		LM.MatchHomeID AS homeid,
		LM.MatchAwayID AS awayid,
		LM.MatchHomeBpoints AS homebpoints,
		LM.MatchAwayBpoints AS awaybpoints,
		LM.MatchHomeSets AS homesets,
		LM.MatchHomeHighfinish AS homehighfinish,
		LM.MatchAwaySets AS awaysets,
		LM.MatchAwayHighfinish AS awayhighfinish,
		LM.MatchHome180 AS home180,
		LM.MatchAway180 AS away180
		FROM ".$xoopsDB->prefix("matchdart_matches")." LM
		WHERE LM.MatchID = '$id'
		LIMIT 1
		");
		    
		//query to get all the teams independent of season and event
		$matchdart_get_players = $xoopsDB->query("SELECT PlayerID AS id,
		PlayerName AS name
		FROM ".$xoopsDB->prefix("matchdart_players")." ORDER BY PlayerName");
		
		    $matchdata = $xoopsDB->fetchArray($matchdart_get_match);
		?>
		<form method="post" action="<?php echo "$PHP_SELF"?>">
		<h3><?php echo _AM_XD_MODMATCH;?></h3>

		<table width="100%" cellspacing="3" cellpadding="3" border="0">

			<tr>
				<td align="left" valign="top">
				<?php echo _AM_XD_DATETIME;?>
				</td>
				<td align="left" valign="top">

				<select name="day">
				<?php
				//Print the days
				for($i = 1 ; $i < 32 ; $i++)
				{
				    if($i<10)
				    {
				        $i = "0".$i;
				    }
				    if($matchdata['dayofmonth'] == $i)
				    echo "<option value=\"$i\" SELECTED>$i</option>\n";
				    else
				    echo "<option value=\"$i\">$i</option>\n";
				}
				?>
				</select>&nbsp;/&nbsp;

				<select name="month">
				<?php
				//Print the months
				for($i = 1 ; $i < 13 ; $i++)
				{
				    if($i<10)
				    {
				        $i = "0".$i;
				    }
				    if($matchdata['month'] == $i)
				    echo "<option value=\"$i\" SELECTED>$i</option>\n";
				    else
				    echo "<option value=\"$i\">$i</option>\n";
				}
				?>
				</select>&nbsp;/&nbsp;

				<select name="year">
				<?php
				//Print the years
				for($i = $matchdart_seasonname ; $i < $matchdart_seasonname+1 ; $i++)
				{
				    if($i<10)
				    {
				        $i = "0".$matchdart_seasonname;
				    } 
				    if($matchdata['year'] == $matchdart_seasonname)
				    echo "<option value=\"$i\" SELECTED>$i</option>\n";
				    else
				    echo "<option value=\"$i\">$i</option>\n";
				}
				?>
			</select>
			</td>
		</tr>
		</table>

		<table width="100%" cellspacing="3" cellpadding="3" border="0">
		<tr>
		<td align="left" valign="middle"><b><?php echo _AM_XD_HOMEPLAYER;?></b></td>
		<td align="center" valign="middle"><b><?php echo _AM_XD_180,'&nbsp;',_AM_XD_BONUSPOINTS;?></b></td>
		<td align="center" valign="middle"><b><?php echo _AM_XD_SETS;?></b></td>
		<td align="center" valign="middle"><b><?php echo _AM_XD_HIGHFINISH;?></b></td>
		<td align="left" valign="middle"><b><?php echo _AM_XD_AWAYPLAYER;?></b></td>
		<td align="center" valign="middle"><b><?php echo _AM_XD_180,'&nbsp;',_AM_XD_BONUSPOINTS;?></b></td>
		<td align="center" valign="middle"><b><?php echo _AM_XD_SETS;?></b></td>
		<td align="center" valign="middle"><b><?php echo _AM_XD_HIGHFINISH;?></b></td>
		</tr>

		<tr>
		<td align="left" valign="middle">

		<select name="home">
		<?php

		while($matchdart_data = $xoopsDB->fetchArray($matchdart_get_players))
		{
		    if($matchdata['homeid'] == $matchdart_data['id'])
		    echo"<option value=\"$matchdart_data[id]\" SELECTED>$matchdart_data[name]</option>\n";
		    else
		    echo"<option value=\"$matchdart_data[id]\">$matchdart_data[name]</option>\n";
		}
		
		?>
		</select>
		</td>
		<td align="center" valign="middle"><input type="text" name="home_180" size="2" value="<?= $matchdata['home180']?>">&nbsp;<input type="text" name="home_bpoints" size="2" value="<?= $matchdata['homebpoints']?>"></td>
		<td align="center" valign="middle"><input type="text" name="home_sets" size="2" value="<?= $matchdata['homesets'] ?>"></td>
		<td align="center" valign="middle"><input type="text" name="home_highfinish" size="2" value="<?= $matchdata['homehighfinish'] ?>"></td>

		<td align="left" valign="middle">

		<select name="away">
		<?php
		
		mysql_data_seek($matchdart_get_players, 0);
		
		while($matchdart_data = $xoopsDB->fetchArray($matchdart_get_players))
		{
		    if($matchdata['awayid'] == $matchdart_data['id'])
		    echo"<option value=\"$matchdart_data[id]\" SELECTED>$matchdart_data[name]</option>\n";
		    else
		    echo"<option value=\"$matchdart_data[id]\">$matchdart_data[name]</option>\n";
		}
		
		?>
		</select>
		</td>
		<td align="center" valign="middle"><input type="text" name="away_180" size="2" value="<?= $matchdata['away180']?>">&nbsp;<input type="text" name="away_bpoints" size="2" value="<?= $matchdata['awaybpoints'] ?>"></td>
		<td align="center" valign="middle"><input type="text" name="away_sets" size="2" value="<?= $matchdata['awaysets'] ?>"></td>
		<td align="center" valign="middle"><input type="text" name="away_highfinish" size="2" value="<?= $matchdata['awayhighfinish'] ?>"></td>
		</tr>
		</table>

		<input type="hidden" name="mid" value="<?= $id ?>">
		<input type="hidden" name="homeid" value="<?= $matchdata['awayid'] ?>">
		<input type="hidden" name="awayid" value="<?= $matchdata['homeid'] ?>">
		<br><input type="submit" name="modify_submit" value="<?php echo _AM_XD_MODINPUT2;?>">
		<input type="hidden" name="seasonid" value="<?php echo $matchdart_seasonid; ?>">
		<input type="hidden" name="seasonname" value="<?php echo $matchdart_seasonname; ?>">
		<input type="hidden" name="eventid" value="<?php echo $matchdart_eventid; ?>">
		<input type="hidden" name="eventname" value="<?php echo $matchdart_eventname; ?>">
		<br><br><br><br><br>
		<input type="submit" name="delete_submit" value="<?php echo _AM_XD_DELINPUT;?>">
		</form>

		<?php
		}
		?>
		</td></tr>

<tr>
		<td align="left" valign="top">

		<table width="100%">
		<?php
				$matchdart_get_matches = $xoopsDB->query("SELECT O.PlayerName AS hometeam,
		OP.PlayerName AS awayteam,
		LM.MatchHomeBpoints AS bpoints_home,
		LM.MatchAwayBpoints AS bpoints_away,
		LM.MatchHomeSets AS sets_home,
		LM.MatchHomeHighfinish AS highfinish_home,
		LM.MatchAwaySets AS sets_away,
		LM.MatchAwayHighfinish AS highfinish_away,
		LM.MatchHome180 AS 180_home,
		LM.MatchAway180 AS 180_away,
		LM.MatchID AS id,
		LM.MatchDate AS defaultdate,
		DATE_FORMAT(LM.MatchDate, '%b %D %Y') AS date
		FROM ".$xoopsDB->prefix("matchdart_matches")." LM, ".$xoopsDB->prefix("matchdart_players")." O, ".$xoopsDB->prefix("matchdart_players")." OP
		WHERE O.PlayerID = LM.MatchHomeID AND
		OP.PlayerID = LM.MatchAwayID AND
		MatchSeasonID = '$matchdart_seasonid' AND
		MatchEventID = '$matchdart_eventid'
		ORDER BY LM.MatchDate DESC");
		
		if($xoopsDB->getRowsNum($matchdart_get_matches) < 1)
		{
		    echo "<b>  "._AM_XD_NOMATCHESYET.": <u>$matchdart_seasonname</u> </b><br /><br />";
			echo "<b>  "._AM_XD_NOEVENTMATCHESYET.": <u>$matchdart_eventname</u> </b><br /><br />";
		}
		else
		{
		    echo "<b> "._AM_XD_MATCHESYET.": <u>$matchdart_seasonname</u></b><br /><br />";
			echo "<b> "._AM_XD_EVENTMATCHESYET.": <u>$matchdart_eventname</u></b><br /><br /><br />";
		    
		    $i = 0;
		    $temp = '';

			echo "<table width=\"90%\" align=\"center\" cellspacing=\"3\" cellpadding=\"3\" border=\"1\">";		    
		    while($matchdart_data = $xoopsDB->fetchArray($matchdart_get_matches))
		    {
		        if($i == 0)
		        {
					echo"
					<tr>
					<td align=\"left\" colspan=\"2\">
					<b><a href=\"$PHP_SELF?action=modifyall&amp;date=$matchdart_data[defaultdate]\">$matchdart_data[date]</a></b>
					</td>
					</tr>
					";
				}
		        
		        if($matchdart_data['date'] != "$temp" && $i > 0)
		        {
					echo"
					<tr>
					<td align=\"left\" colspan=\"2\">
					<br><br>
					<b><a href=\"$PHP_SELF?action=modifyall&amp;date=$matchdart_data[defaultdate]\">$matchdart_data[date]</a></b>
					</td>
					</tr>
					";
		        }
		        
				echo "
				<tr>
				<td align=\"left\" valign=\"top\" width=\"500\">
				<a href=\"$PHP_SELF?action=modify&amp;id=$matchdart_data[id]\">$matchdart_data[hometeam] Vs $matchdart_data[awayteam]</a>
				</td>
				<td align=\"center\" valign=\"top\" width=\"250\">";
		        
		        if(!is_null($matchdart_data['sets_home']) || ($matchdart_data['highfinish_home']))
		        echo"$matchdart_data[sets_home] for $matchdart_data[highfinish_home] - $matchdart_data[sets_away] for $matchdart_data[highfinish_away]";
		        else
		        echo'&nbsp;';
		        
		        echo"
				</td>
				</tr>";
				
		        $temp = "$matchdart_data[date]";
		        
		        $i++;
		    }
	      echo "</table>";
		}
		?>
		</table>
		</td>
		</tr>
	</table>
<?php 
xoops_cp_footer();
?>