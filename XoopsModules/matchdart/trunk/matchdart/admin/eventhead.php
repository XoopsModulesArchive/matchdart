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

//if(!session_is_registered('event_name') || !session_is_registered('event_id'))
if ( !isset( $_SESSION['event_name'] ) || !isset( $_SESSION['event_id'] ))
{
	echo "<form method=\"post\" action=\"eventmatches.php\">";
	echo '<b><?php echo _AM_XD_CHOEVENT;?></b>';
	echo '<select name="event_select">';
	$matchdart_get_events = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("matchdart_eventnames")." ORDER BY eventName");

	while($matchdart_sdata = $xoopsDB->fetchArray($matchdart_get_events))
	{
		echo "<option value=\"$matchdart_sdata[EventID]____$matchdart_sdata[EventName]\">$matchdart_sdata[EventName]</option>\n";
	}
	echo "</select> <input type=\"submit\" name=\"submit1\" value=" ._AM_XD_EVENTGO. "></form>";


	mysql_free_result($matchdart_get_events);
}
else
{
	$matchdart_event_name = $_SESSION['event_name'];
	echo "<form method=\"post\" action=\"eventmatches.php\">";
	echo "<b> "._AM_XD_EVENTSELECT."  $matchdart_event_name</b><br><br>";
	echo _AM_XD_EVENTSELDROP;
	echo '<select name="event_select">';

	$matchdart_get_events = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("matchdart_eventnames")." ORDER BY EventName");

	while($matchdart_sdata = $xoopsDB->fetchArray($matchdart_get_events))
	{
		if($matchdart_sdata['EventID'] == $matchdart_eventid)
			echo "<option value=\"$matchdart_sdata[EventID]____$matchdart_sdata[EventName]\" SELECTED>$matchdart_sdata[EventName]</option>\n";
		else
			echo "<option value=\"$matchdart_sdata[EventID]____$matchdart_sdata[EventName]\">$matchdart_sdata[EventName]</option>\n";
	}
	echo "</select> <input type=\"submit\" name=\"submit1\" value=" ._AM_XD_EVENTGO. "></form>";

	mysql_free_result($matchdart_get_events);
}
?>

<hr width="100%">

</center>