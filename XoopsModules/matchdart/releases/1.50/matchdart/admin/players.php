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

include '../../../include/cp_header.php'; //Include file, which checks for permissions and sets navigation
if (@include_once XOOPS_ROOT_PATH."/Frameworks/art/functions.php")
{
	include_once XOOPS_ROOT_PATH."/Frameworks/art/functions.admin.php";
	$noframework = 0;
} else
{
	$noframework = 1;
}

if (isset($_POST['season_select'])) {
    $matchdart_season = explode("____",$_POST['season_select']);
}
elseif (isset($_POST['seasonid'])) {
    $matchdart_season = array (intval($_POST['seasonid']), $_POST['seasonname']);
}
elseif (!isset($_SESSION['season_id'])) {
    $sql = "SELECT SeasonID, SeasonName FROM ".$xoopsDB->prefix("matchdart_seasonnames")." WHERE SeasonDefault=1";
    $matchdart_seasonname = $xoopsDB->query($sql);
    $matchdart_seasonname = $xoopsDB->fetchArray($matchdart_seasonname);
    $matchdart_season = array($matchdart_seasonname['SeasonID'], $matchdart_seasonname['SeasonName']);
}
else {
    $matchdart_season = array(intval($_SESSION['season_id']), $_SESSION['season_name']);
}

if (isset($_POST['event_select'])) {
    $matchdart_event = explode("____",$_POST['event_select']);
}
elseif (isset($_POST['eventid'])) {
    $matchdart_event = array (intval($_POST['eventid']), $_POST['eventname']);
}
elseif (!isset($_SESSION['event_id'])) {
    $sql2 = "SELECT EventID, EventName FROM ".$xoopsDB->prefix("matchdart_eventnames")." WHERE EventDefault=1";
    $matchdart_eventname = $xoopsDB->query($sql2);
    $matchdart_eventname = $xoopsDB->fetchArray($matchdart_eventname);
    $matchdart_event = array($matchdart_eventname['EventID'], $matchdart_eventname['EventName']);
}
else {
    $matchdart_event = array(intval($_SESSION['event_id']), $_SESSION['event_name']);
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

$matchdart_d_points_add = isset($_POST['d_points_add']) ? $_POST['d_points_add'] : null;
$matchdart_d_points_modify = isset($_POST['d_points_modify']) ? $_POST['d_points_modify'] : null;

xoops_cp_header();
if ($noframework == 0){loadModuleAdminMenu(3);}
if ($noframework == 1){include 'navbar.php';}

//Add
if($matchdart_add_submit)
{
    $player = trim($_POST['player']);
    $player = $xoopsDB->quoteString($player);
    $playeruid = ($_POST['playeruid']);
	    if(!isset($playeruid))
    {
        $playeruid = 0;
    }
    
//query to check if there are already a team with submitted name independent of season and event
    $query = $xoopsDB->query("SELECT PlayerName FROM ".$xoopsDB->prefix("matchdart_players")." WHERE PlayerName = $player");
    
    if($xoopsDB->getRowsNum($query) > 0)
    {
        echo "<font color='red'><b>". _AM_XD_PLAYERDUPLICATE."</b></font><br><br>";
        exit();
    }
    
    if($player != '')
    {
        //$xoopsDB->query("INSERT INTO ".$xoopsDB->prefix("matchdart_players")." SET PlayerName = $player, PlayerEventID = $matchdart_eventid, PlayerSeasonID = $matchdart_seasonid");
        $xoopsDB->query("INSERT INTO ".$xoopsDB->prefix("matchdart_players")." SET PlayerName = $player, PlayerUID = $playeruid");
        
        header("Location: $PHP_SELF");
    }
}

//Modify
elseif($matchdart_modify_submit)
{
    $player = $xoopsDB->quoteString(trim($_POST['player']));
    $playerid = intval($_POST['playerid']);
    //$own = $_POST['own'];
    $uid = $_POST['playeruid'];

    //Checked own
    if(!isset($own))
    {
        $own = 0;
    }
    if(!isset($uid))
    {
        $uid = 0;
    }
    
    if($player != '')
    {

        //If own team->delete the own status from the previous one
        if($own == 1)
        {
            $xoopsDB->query("UPDATE ".$xoopsDB->prefix("matchdart_players")." SET
				PlayerOwn = '0'
				WHERE PlayerOwn = '1'
				");
        }
		$xoopsDB->query("UPDATE ".$xoopsDB->prefix("matchdart_players")." SET
			PlayerName = $player,
			PlayerOwn = $own,
			PlayerUID = $uid
			WHERE PlayerID = $playerid
			");
    }
    
    //header("Location: $HTTP_REFERER");
}

//Delete
elseif($matchdart_delete_submit)
{
    $playerid = intval($_POST['playerid']);
    //query to check, if team already exists in the eventtables independent of season and event
    $query = $xoopsDB->query("SELECT MatchID
		FROM ".$xoopsDB->prefix("matchdart_matches")."
		WHERE MatchHomeID = $playerid OR MatchAwayID = $playerid");
        
    if($xoopsDB->getRowsNum($query) == 0)
    {
        $xoopsDB->query("DELETE FROM ".$xoopsDB->prefix("matchdart_players")." WHERE PlayerID = $playerid");
        
        header("Location: $PHP_SELF");
    }
    else
    {
        echo "<font color='red'><b>". _AM_XD_PLAYERISINUSE."</b></font><br><br>";
        exit();
    }
}

//Deducted points
elseif($matchdart_d_points_add)
{
    $matchdart_d_points = intval($_POST['d_points']);
    $teamid = intval($_POST['teamid']);
    
    if(is_numeric($matchdart_d_points) && $matchdart_d_points != '')
    {
		$xoopsDB->query("INSERT INTO ".$xoopsDB->prefix("matchdart_deductedpoints")." SET
			teamid = $teamid,
			points = $matchdart_d_points");
		
    }
    
    header("Location: $HTTP_REFERER");
}

//Modify of deducted points
elseif($matchdart_d_points_modify)
{
    $matchdart_d_points = intval($_POST['d_points']);
    $id = intval($_POST['id']);
    
    if(is_numeric($matchdart_d_points) && $matchdart_d_points != '')
    {
        //Delete deducted points if zero is written
        if($matchdart_d_points == 0)
        {
            $xoopsDB->query("DELETE FROM ".$xoopsDB->prefix("matchdart_deductedpoints")."
				WHERE id = $id");
        }
        //Modify if some other number
        else
        {
            $xoopsDB->query("UPDATE ".$xoopsDB->prefix("matchdart_deductedpoints")." SET
				points = $matchdart_d_points
				WHERE id = $id");
        }
    }
    
    header("Location: $HTTP_REFERER");
}
?>
	
	<?php
	?>
	<table align="center" width="100%" border="3">
		<tr>
		<td align="left" valign="top">
		<?php
		if(!isset($matchdart_action))
		{
		?>
		<form method="post" action="<?php echo "$PHP_SELF"?>">
		<h3><?php echo _AM_XD_ADDNEWPLAYER;?></h3>
		<table width="100%" cellspacing="3" cellpadding="3" border="0">
		<tr>
			<td align="left" valign="top">
			<?php echo _AM_XD_PLAYERNAME;?>
			</td>
			<td align="left" valign="top">
			<input type="text" name="player">
			</td>
		</tr>
		<tr>
			<td align="left" valign="top">
			<?php echo _AM_XD_PLAYERXUID;?>
			</td>
			<td align="left" valign="top">
			<?php
			
			$member_handler = & xoops_gethandler('member');
			$allusers = $member_handler->getUsers();
			$q=1;
			
			echo"<select name=\"playeruid\">";
			echo"<option value=\"0\">",_AM_XD_PLAYERNOXUID,"</option>\n";
			//TODO: Order by Username!
			foreach (array_keys($allusers) as $j)
			{
				$xuser['uid'] = $allusers[$j]->getVar("uid");
				$xuser['uname'] = $allusers[$j]->getVar("uname");
		    	echo"<option value=\"$xuser[uid]\">$xuser[uname]</option>\n";
				
			}	
		    echo"
			</select>
			</td>
			";
			
			?>
			</td>
		</tr>
		</table>
		<input type="submit" name="add_submit" value="<?php echo _AM_XD_ADDPLAYER;?>">
		</form>
		<?php
		}
		elseif($matchdart_action == 'modify')
		{
		    $playerid = intval($_REQUEST['player']);
		    //$matchdart_get_player = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("matchdart_players")." WHERE PlayerID = $playerid AND PlayerEventID = $matchdart_eventid AND PlayerSeasonID = $matchdart_seasonid LIMIT 1");
		    $matchdart_get_player = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("matchdart_players")." WHERE PlayerID = $playerid LIMIT 1");
		    $matchdart_data = $xoopsDB->fetchArray($matchdart_get_player);
		?>

		<form method="post" action="<?php echo "$PHP_SELF"?>">
		<h3><?php echo _AM_XD_PLAYERMODIFYDELETE;?></h3>
		<table width="100%" cellspacing="3" cellpadding="3" border="0">
		<tr>
			<td align="left" valign="top">
			<?php echo _AM_XD_PLAYERNAME;?>
			</td>
			<td align="left" valign="top">
			<input type="text" name="player" value="<?php echo $matchdart_data['PlayerName'] ?>">
			<input type="hidden" name="playerid" value="<?php echo $matchdart_data['PlayerID'] ?>">
			<input type="hidden" name="playerseasonid" value="<?php echo $matchdart_data['PlayerSeasonID'] ?>">
			<input type="hidden" name="playereventid" value="<?php echo $matchdart_data['PlayerEventID'] ?>">
			</td>
		</tr>

		<tr>
			<td align="left" valign="top">
			<?php echo _AM_XD_PLAYERXUID;?>
			</td>
			<td align="left" valign="top">
			<?php
			
			$member_handler = & xoops_gethandler('member');
			$allusers = $member_handler->getUsers();
			$q=1;
			
			echo"<select name=\"playeruid\">";
			echo"<option value=\"0\">",_AM_XD_PLAYERNOXUID,"</option>\n";
			foreach (array_keys($allusers) as $j)
			{
				$xuser['uid'] = $allusers[$j]->getVar("uid");
				$xuser['uname'] = $allusers[$j]->getVar("uname");
			    if($xuser['uid'] == $matchdart_data['PlayerUID'])
			    echo"<option value=\"$xuser[uid]\" SELECTED>$xuser[uname]</option>\n";
			    else
		    	echo"<option value=\"$xuser[uid]\">$xuser[uname]</option>\n";
				
			}	
		    echo"
			</select>
			</td>
			";
						
			?>
			</td>
		</tr>
		
		</table>
		<input type="submit" name="modify_submit" value="<?php echo _AM_XD_PLAYERMODIFY;?>"> <input type="submit" name="delete_submit" value="<?php echo _AM_XD_PLAYERDELETE;?>">
		</form>

		<a href="<?php echo "$PHP_SELF" ?>"><?php echo _AM_XD_ADDNEWPLAYER;?></a>

		
		<?php
		mysql_free_result($matchdart_get_player);
		}
		?>
		</td>

		<td align="left" valign="top">
		<?php
		$matchdart_get_players = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("matchdart_players")." ORDER BY PlayerName");
		
		if($xoopsDB->getRowsNum($matchdart_get_players) < 1)
		{
		    echo "<b>". _AM_XD_NOPLAYERSAVAILABLE."</b><br><br>";
		}
		else
		{
		    echo "<b>". _AM_XD_PLAYERSAVAILABLE."</b><br><br>";
		    
		    while($matchdart_data = $xoopsDB->fetchArray($matchdart_get_players))
		    {
		        echo "<a href=\"$PHP_SELF?action=modify&amp;player=$matchdart_data[PlayerID]\">$matchdart_data[PlayerName]</a>";
		        
		        echo"<br>\n";
		    }
		}
		?>
		<br><br>

		</td>
		</tr>
	</table>
<?php
xoops_cp_footer();
?>