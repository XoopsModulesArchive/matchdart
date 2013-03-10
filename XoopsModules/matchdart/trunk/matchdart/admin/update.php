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
include_once 'admin_header.php';
include '../../../include/cp_header.php'; //Include file, which checks for permissions and sets navigation
if (@include_once XOOPS_ROOT_PATH."/Frameworks/art/functions.php")
{
	include_once XOOPS_ROOT_PATH."/Frameworks/art/functions.admin.php";
	$noframework = 0;
} else
{
	$noframework = 1;
}

xoops_cp_header();
//if ($noframework == 0){loadModuleAdminMenu(5);}
//if ($noframework == 1){include 'navbar.php';}
$indexAdmin = new ModuleAdmin();
echo $indexAdmin->addNavigation('update.php');

$matchdart_update_submit = isset($_POST['update_submit']) ? $_POST['update_submit'] : false;
$update_err = false;

if($matchdart_update_submit)
{
	$query = $xoopsDB->query("ALTER TABLE ".$xoopsDB->prefix("matchdart_matches")."
	ADD MatchHomeLegs int(3) default NULL, 
	ADD MatchAwayLegs int(3) default NULL, 
	ADD MatchHomeDarts int(3) default NULL, 
	ADD MatchAwayDarts int(3) default NULL, 
	ADD MatchHomeMatchDarts int(3) default NULL, 
	ADD MatchAwayMatchDarts int(3) default NULL, 
	ADD MatchHomePPT double default NULL, 
	ADD MatchAwayPPT double default NULL, 
	ADD MatchHomePPD double default NULL, 
	ADD MatchAwayPPD double default NULL,
	ADD MatchPlayerStats int(1) not NULL default -1 
	");
	$query1 = $xoopsDB->query("ALTER TABLE ".$xoopsDB->prefix("matchdart_eventnames")."
	ADD EventPlayerStats tinyint(1) default NULL 
	");
	if(!$query OR !$query1) {
		$update_err = true;
		echo "<p style='color:red'>Error: Please visit <a href='http://www.matchdart.de/modules/liaise' target='_blank'>www.matchdart.de</a></p>";
	} else {
		$update_err = false;
		echo "Congrats: You have successfully updated your MatchDart Database Tables. <p style='color:red'>Please check Event Settings for Player Stats!</p>";
	}

}

if (!$update_err){
	$query = $xoopsDB->query("SELECT MatchHomePPT FROM ".$xoopsDB->prefix("matchdart_matches")."
     ");
	if($query) {
		echo "<p style='color:green'>"._AM_XD_MODNOUPDATE."</p><br>";
	} else {
		echo "<form method='post' action='update.php'>";
		echo "<p style='color:red; font-size:medium; font-weight:bold'>"._AM_XD_MODUPDATEWARNING."</p><br>";
		echo "<input type='submit' name='update_submit' value='"._AM_XD_MODUPDATE."'>";
		echo "</form>";
	}
}

xoops_cp_footer();
?>