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

if(!session_is_registered('season_name') || !session_is_registered('season_id'))
{
	echo "<form method=\"post\" action=\"eventmatches.php\">";
	echo '<b><?php echo _AM_XD_CHOSEASON;?></b>';
	echo '<select name="season_select">';
	$matchdart_get_seasons = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("matchdart_seasonnames")." ORDER BY SeasonName");

	while($matchdart_sdata = $xoopsDB->fetchArray($matchdart_get_seasons))
	{
		echo "<option value=\"$matchdart_sdata[SeasonID]____$matchdart_sdata[SeasonName]\">$matchdart_sdata[SeasonName]</option>\n";
	}
	echo "</select> <input type=\"submit\" name=\"submit\" value=" ._AM_XD_SEASONGO. "></form>";


	mysql_free_result($matchdart_get_seasons);
}
else
{
	$matchdart_season_name = $_SESSION['season_name'];
	echo "<form method=\"post\" action=\"eventmatches.php\">";
	echo "<b> "._AM_XD_SEASELECT."  $matchdart_season_name</b><br><br>";
	echo _AM_XD_SEASELDROP;
	echo '<select name="season_select">';

	$matchdart_get_seasons = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("matchdart_seasonnames")." ORDER BY SeasonName");

	while($matchdart_sdata = $xoopsDB->fetchArray($matchdart_get_seasons))
	{
		if($matchdart_sdata['SeasonID'] == $matchdart_seasonid)
			echo "<option value=\"$matchdart_sdata[SeasonID]____$matchdart_sdata[SeasonName]\" SELECTED>$matchdart_sdata[SeasonName]</option>\n";
		else
			echo "<option value=\"$matchdart_sdata[SeasonID]____$matchdart_sdata[SeasonName]\">$matchdart_sdata[SeasonName]</option>\n";
	}
	echo "</select> <input type=\"submit\" name=\"submit\" value=" ._AM_XD_SEASONGO. "></form>";

	mysql_free_result($matchdart_get_seasons);
}

?>

<hr width="100%">

</center>