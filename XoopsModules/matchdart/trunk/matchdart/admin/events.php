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

$matchdart_eventid = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;
$matchdart_eventname = isset($_GET['event_name']) ? $_GET['event_name'] : "";

$PHP_SELF = $_SERVER['PHP_SELF'];
$HTTP_REFERER = $_SERVER['HTTP_REFERER'];
$matchdart_action = isset($_GET['action']) ? $_GET['action'] : null;
$matchdart_action = isset($_POST['action']) ? $_POST['action'] : $matchdart_action;

$matchdart_add_submit = isset($_POST['add_submit']) ? $_POST['add_submit'] : false;
$matchdart_modify_submit = isset($_POST['modify_submit']) ? $_POST['modify_submit'] : false;
$matchdart_delete_submit = isset($_POST['delete_submit']) ? $_POST['delete_submit'] : false;

xoops_cp_header();
if ($noframework == 0){loadModuleAdminMenu(2);}
if ($noframework == 1){include 'navbar.php';}

if($matchdart_add_submit)
{
    $matchdart_name = $xoopsDB->quoteString(trim($_POST['name']));
    $matchdart_pointswin = trim($_POST['pointswin']);
    $matchdart_pointsdraw = trim($_POST['pointsdraw']);
    $matchdart_pointsloss = trim($_POST['pointsloss']);
    $matchdart_drawline = trim($_POST['drawline']);
    $publish = $_POST['publish'];
    $defevent = intval($_POST['defevent']);
    $playerstats = intval($_POST['playerstats']);
    
    //Query to check if there are already a submitted event name in the database
    $query = $xoopsDB->query("SELECT EventName FROM ".$xoopsDB->prefix("matchdart_eventnames")." WHERE EventName = $matchdart_name");
    
    if($xoopsDB->getRowsNum($query) > 0)
    {
        echo "<font color='red'><b>". _AM_XD_EVENTDUPLICATE."</b></font><br><br>";
        exit();
    }
    
    mysql_free_result($query);
    
    if($matchdart_name != '')
    {
        $xoopsDB->query("INSERT INTO ".$xoopsDB->prefix("matchdart_eventnames")." SET
			EventName = $matchdart_name,
			EventLine = '$matchdart_drawline',
            EventPublish = '$publish',
            EventPointsWin = '$matchdart_pointswin',
            EventPointsDraw = '$matchdart_pointsdraw',
            EventPointsLoss = '$matchdart_pointsloss',
            EventDefault = '$defevent',
            EventPlayerStats = '$playerstats'");
        
        header("Location: $PHP_SELF");
    }
}
elseif($matchdart_modify_submit)
{
    $matchdart_name = $xoopsDB->quoteString(trim($_POST['name']));
    $matchdart_drawline = trim($_POST['drawline']);
    $publish = $_POST['publish'];
    $matchdart_pointswin = trim($_POST['pointswin']);
    $matchdart_pointsdraw = trim($_POST['pointsdraw']);
    $matchdart_pointsloss = trim($_POST['pointsloss']);
    $matchdart_eventid = intval($_POST['eventid']);
    $defevent = intval($_POST['defevent']);
    $playerstats = intval($_POST['playerstats']);
    
    //
    //If published is checked
    //
    if(!isset($publish))
    {
        $publish = 0;
    }
    if(!isset($defevent))
    {
        $defevent = 0;
    }
    
    if($matchdart_name != '')
    {
        //
        //If default event->delete the default status from the previous one
        //
        if($defevent == 1)
        {
            $xoopsDB->query("UPDATE ".$xoopsDB->prefix("matchdart_eventnames")." SET
				EventDefault = '0'");
        }
        $xoopsDB->query("UPDATE ".$xoopsDB->prefix("matchdart_eventnames")." SET
			EventName = $matchdart_name,
			EventLine = '$matchdart_drawline',
			EventPublish = '$publish',
            EventPointsWin = '$matchdart_pointswin',
            EventPointsDraw = '$matchdart_pointsdraw',
            EventPointsLoss = '$matchdart_pointsloss',
            EventDefault = '$defevent',
            EventPlayerStats = '$playerstats'
			WHERE EventID = '$matchdart_eventid'");
    }
    
    header("Location: $HTTP_REFERER");
}
elseif($matchdart_delete_submit)
{
    $matchdart_eventid = intval($_POST['eventid']);
    
    //
    //Query to check if there are already matches in the event->can't delete
    //
    $query = $xoopsDB->query("SELECT M.EventMatchID
		FROM ".$xoopsDB->prefix("matchdart_eventmatches")." M, ".$xoopsDB->prefix("matchdart_eventnames")." L
		WHERE M.EventMatchEventID = '$matchdart_eventid'");
    
    if($xoopsDB->getRowsNum($query) == 0)
    {
        $xoopsDB->query("DELETE FROM ".$xoopsDB->prefix("matchdart_eventnames")." WHERE EventID = '$matchdart_eventid'");
    }
    else
    {
        echo "<font color='red'><b>". _AM_XD_EVENTHASMATCHES."</b></font><br><br>";
        exit();
    }
    
    header("Location: $PHP_SELF");
}


?>
	
	<?php
	include('eventhead.php');
	?>
	<table align="center" width="600">
		<tr>
		<td>
		<?php
		if(!isset($matchdart_action))
		{
		?>
		<form method="post" action="<?php echo "$PHP_SELF" ?>">
		<h3><?php echo _AM_XD_ADDEVENT;?></h3>
		<table width="100%" cellspacing="3" cellpadding="3" border="0">
		<tr>
			<td align="left" valign="top">
			<?php echo _AM_XD_EVENTNAMEYEARS;?>
			</td>
			<td align="left" valign="top">
			<input type="text" name="name">
			</td>
		</tr>
		<tr>
			<td align="left" valign="top">
			<?php echo _AM_XD_EVENTPUBLISHED;?>
			</td>
			<td align="left" valign="top">
			<?php
			//
			//If event is published
			//
			if($matchdart_data['EventPublish'] == 1)
			echo'<input type="checkbox" name="publish" value="1" CHECKED>';
			else
			echo'<input type="checkbox" name="publish" value="1">';
			
			?>
			</td>
		</tr>
		<tr>
			<td align="left" valign="top">
			<?php echo _AM_XD_EVENTPOINTSWIN;?>
			</td>
			<td align="left" valign="top">
			<input type="text" name="pointswin" value="" size="10">
			</td>
		</tr>
		<tr>
			<td align="left" valign="top">
			<?php echo _AM_XD_EVENTPOINTSDRAW;?>
			</td>
			<td align="left" valign="top">
			<input type="text" name="pointsdraw" value="" size="10">
			</td>
		</tr>
		<tr>
			<td align="left" valign="top">
			<?php echo _AM_XD_EVENTPOINTSLOSS;?>
			</td>
			<td align="left" valign="top">
			<input type="text" name="pointsloss" value="" size="10">
			</td>
		</tr>
		<tr>
			<td align="left" valign="top">
			<?php echo _AM_XD_EVENTDRAWLINE;?>
			</td>
			<td align="left" valign="top">
			<input type="text" name="drawline" value="" size="10">
			</td>
		</tr>
		<tr>
		    <td align="left" valign="top">
			<?php echo _AM_XD_DEFAULTEVENT;?>
			</td>
			<td align="left" valign="top">
			<?php
			
			if($matchdart_data['EventDefault'] == 1)
			echo"<input type=\"checkbox\" name=\"defevent\" value=\"1\" CHECKED>\n";
			else
			echo"<input type=\"checkbox\" name=\"defevent\" value=\"1\">\n";
			
			?>
			</td>
		</tr>
		<tr>
		    <td align="left" valign="top">
			<?php echo _AM_XD_EVENTPLAYERSTATS;?>
			</td>
			<td align="left" valign="top">
			<?php
			
			if($matchdart_data['EventPlayerStats'] == 1)
			echo"<input type=\"checkbox\" name=\"playerstats\" value=\"1\" CHECKED>\n";
			else
			echo"<input type=\"checkbox\" name=\"playerstats\" value=\"1\">\n";
			
			?>
			</td>
		</tr>	
		</table>
		<input type="submit" name="add_submit" value="<?php echo _AM_XD_EVENTADD;?>">
		</form>
		<?php
		}
		elseif($matchdart_action == 'modify')
		{
		    $matchdart_eventid = intval($_REQUEST['event']);
		    $matchdart_get_event = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("matchdart_eventnames")." WHERE EventID = '$matchdart_eventid' LIMIT 1");
		    $matchdart_data = $xoopsDB->fetchArray($matchdart_get_event);
		?>

		<form method="post" action="<?php echo "$PHP_SELF"?>">
		<h3><?php echo _AM_XD_EVENTMODIFYDELETE;?></h3>
		<table width="100%" cellspacing="3" cellpadding="3" border="0">
		<tr>
			<td align="left" valign="top">
			<?php echo _AM_XD_EVENTNAMEYEARS;?>
                        </td>
                        <td>
                        <input type="text" name="name" value="<?php echo $matchdart_data['EventName'] ?>">
			<input type="hidden" name="eventid" value="<?php echo $matchdart_data['EventID'] ?>">
			</td>
	   </tr>
	   <tr>
			<td align="left" valign="top">
			<?php echo _AM_XD_EVENTPUBLISHED;?>
			</td>
			<td align="left" valign="top">
			<?php
			//
			//If event is published
			//
			if($matchdart_data['EventPublish'] == 1)
			echo'<input type="checkbox" name="publish" value="1" CHECKED>';
			else
			echo'<input type="checkbox" name="publish" value="1">';
			
			?>
			</td>
		</tr>
		
		<tr>
			<td align="left" valign="top">
			<?php echo _AM_XD_EVENTPOINTSWIN;?>
			</td>
			<td align="left" valign="top">
			<input type="text" name="pointswin" value="<?= $matchdart_data['EventPointsWin'] ?>" size="10">
			</td>
		</tr>
		<tr>
			<td align="left" valign="top">
			<?php echo _AM_XD_EVENTPOINTSDRAW;?>
			</td>
			<td align="left" valign="top">
			<input type="text" name="pointsdraw" value="<?= $matchdart_data['EventPointsDraw'] ?>" size="10">
			</td>
		</tr>
		<tr>
			<td align="left" valign="top">
			<?php echo _AM_XD_EVENTPOINTSLOSS;?>
			</td>
			<td align="left" valign="top">
			<input type="text" name="pointsloss" value="<?= $matchdart_data['EventPointsLoss'] ?>" size="10">
			</td>
		</tr>
		<tr>
			<td align="left" valign="top">
			<?php echo _AM_XD_EVENTDRAWLINE;?>
			</td>
			<td align="left" valign="top">
			<input type="text" name="drawline" value="<?= $matchdart_data['EventLine'] ?>" size="10">
			</td>
		</tr>
		<tr>
		    <td align="left" valign="top">
			<?php echo _AM_XD_DEFAULTEVENT;?>
			</td>
			<td align="left" valign="top">
			<?php
			
			if($matchdart_data['EventDefault'] == 1)
			echo"<input type=\"checkbox\" name=\"defevent\" value=\"1\" CHECKED>\n";
			else
			echo"<input type=\"checkbox\" name=\"defevent\" value=\"1\">\n";
			
			?>
			</td>
		</tr>
		<tr>
		    <td align="left" valign="top">
			<?php echo _AM_XD_EVENTPLAYERSTATS;?>
			</td>
			<td align="left" valign="top">
			<?php
			
			if($matchdart_data['EventPlayerStats'] == 1)
			echo"<input type=\"checkbox\" name=\"playerstats\" value=\"1\" CHECKED>\n";
			else
			echo"<input type=\"checkbox\" name=\"playerstats\" value=\"1\">\n";
			
			?>
			</td>
		</tr>	
	</table>
		<input type="submit" name="modify_submit" value="<?php echo _AM_XD_EVENTMODIFY;?>"> <input type="submit" name="delete_submit" value="<?php echo _AM_XD_EVENTDELETE;?>">
		</form>

		<a href="<?php echo "$PHP_SELF"?>"><?php echo _AM_XD_ADDEVENT;?></a>

		<?php
		mysql_free_result($matchdart_get_event );
		}
		?>
		</td>

		<td align="left" valign="top">
		<?php
		$matchdart_get_events = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("matchdart_eventnames")." ORDER BY EventName");
		
		if($xoopsDB->getRowsNum($matchdart_get_events) < 1)
		{
		    echo "<b>"._AM_XD_NOEVENTS."</b>";
		}
		else
		{
		    echo "<b>". _AM_XD_EVENTSAVAILABLE."</b><br><br>";
		    
		    while($matchdart_data = $xoopsDB->fetchArray($matchdart_get_events))
		    {
		        echo "<a href=\"$PHP_SELF?action=modify&amp;event=$matchdart_data[EventID]\">$matchdart_data[EventName]</a>";
		        
		        //
		        //Event published?
		        //
		        if($matchdart_data['EventPublish'] == 0)
		        echo "&nbsp;" ._AM_XD_EVENTNP."<br>\n";
		        else
		        echo"<br>\n";
		    }
		}
		
		?>
		<br><br>
		<?php echo _AM_XD_EVENTNOTE;?>
		</td>
		</tr>
	</table>
	

<?php
xoops_cp_footer();
?>