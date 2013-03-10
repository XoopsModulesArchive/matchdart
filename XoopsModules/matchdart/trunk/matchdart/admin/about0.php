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

xoops_cp_header();
//if ($noframework == 0){loadModuleAdminMenu(6);}
//if ($noframework == 1){include 'navbar.php';}


$versioninfo =& $module_handler->get( $xoopsModule->getVar( 'mid' ) );
echo "
	<style type=\"text/css\">
	label,text {
		display: block;
		float: left;
		margin-bottom: 2px;
	}
	label {
		text-align: right;
		width: 150px;
		padding-right: 20px;
	}
	br {
		clear: left;
	}
	</style>
";

echo "<fieldset><legend style='font-weight: bold; color: #900;'>" . $xoopsModule->getVar("name"). "</legend>";
echo "<div style='padding: 8px;'>";
echo "<img src='" . XOOPS_URL . "/modules/" . $xoopsModule->getVar("dirname") . "/" . $versioninfo->getInfo( 'image' ) . "' alt='' hspace='10' vspace='0' /></a>\n";
echo "<div style='padding: 5px;'><strong>" . $versioninfo->getInfo( 'name' ) . " version " . $versioninfo->getInfo( 'version' ) . "</strong></div>\n";
echo "<label>" ._AM_XD_ABOUT_RELEASEDATE. ":</label><text>" . $versioninfo->getInfo( 'release' ) . "</text><br />";
echo "<label>" ._AM_XD_ABOUT_AUTHOR. ":</label><text>" . $versioninfo->getInfo( 'author' ) . "</text><br />";
echo "<label>" ._AM_XD_ABOUT_CREDITS. ":</label><text>" . $versioninfo->getInfo( 'credits' ) . "</text><br />";
echo "<label>" ._AM_XD_ABOUT_README. ":</label><text><a href=\"".$versioninfo->getInfo( 'release_file' )."\" target=\"_blank\" >" . $versioninfo->getInfo( 'release_info' ) . "</a></text><br />";
echo "<label>" ._AM_XD_ABOUT_MANUAL. ":</label><text><a href=\"".$versioninfo->getInfo( 'manual_file' )."\" target=\"_blank\" >" . $versioninfo->getInfo( 'manual' ) . "</a></text><br />";
echo "<label>" ._AM_XD_ABOUT_LICENSE. ":</label><text><a href=\"".$versioninfo->getInfo( 'license_file' )."\" target=\"_blank\" >" . $versioninfo->getInfo( 'license' ) . "</a></text>\n";
echo "</div>";
echo "</fieldset>";
echo "<br clear=\"all\" />";

echo "<fieldset><legend style='font-weight: bold; color: #900;'>" ._AM_XD_ABOUT_MODULE_INFO. "</legend>";
echo "<div style='padding: 8px;'>";
echo "<label>" ._AM_XD_ABOUT_MODULE_STATUS. ":</label><text>" . $versioninfo->getInfo( 'module_status' ) . "</text><br />";
echo "<label>" ._AM_XD_ABOUT_WEBSITE. ":</label><text>" . "<a href='" . $versioninfo->getInfo( 'module_website_url' ) . "' target='_blank'>" . $versioninfo->getInfo( 'module_website_name' ) . "</a>" . "</text><br />";
echo "</div>";
echo "</fieldset>";
echo "<br clear=\"all\" />";

echo "<fieldset><legend style='font-weight: bold; color: #900;'>" ._AM_XD_ABOUT_AUTHOR_INFO. "</legend>";
echo "<div style='padding: 8px;'>";
echo "<label>" ._AM_XD_ABOUT_AUTHOR_NAME. ":</label><text>" . $versioninfo->getInfo( 'author' ) . "</text><br />";
echo "<label>" ._AM_XD_ABOUT_WEBSITE. ":</label><text>" . "<a href='" . $versioninfo->getInfo( 'author_website_url' ) . "' target='_blank'>" . $versioninfo->getInfo( 'author_website_name' ) . "</a>" . "</text><br />";
echo "</div>";
echo "</fieldset>";
echo "<br clear=\"all\" />";

echo "<fieldset><legend style='font-weight: bold; color: #900;'>" ._AM_XD_ABOUT_DISCLAIMER. "</legend>";
echo "<div style='padding: 8px;'>";
echo "<div>"._AM_XD_ABOUT_DISCLAIMER_TEXT. "</div>";
echo "</div>";
echo "</fieldset>";
echo "<br clear=\"all\" />";

$file = XOOPS_ROOT_PATH. "/modules/matchdart/changelog.txt";
if ( is_readable( $file ) ){
	echo "<fieldset><legend style='font-weight: bold; color: #900;'>" ._AM_XD_ABOUT_CHANGELOG. "</legend>";
	echo "<div style='padding: 8px;'>";
	echo "<div>". implode("<br />", file( $file )) . "</div>";
	echo "</div>";
	echo "</fieldset>";
	echo "<br clear=\"all\" />";
}

xoops_cp_footer();
?>