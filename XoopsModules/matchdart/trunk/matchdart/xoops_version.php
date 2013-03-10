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

$modversion['name'] = "MatchDart";
$modversion['version'] = "1.5.0";
$modversion['description'] = 'Dart Ranking for Xoops';
$modversion['author'] = 'mawi27';
$modversion['author_website_url'] = "http://www.matchdart.de";
$modversion['author_website_name'] = "Match Dart";
$modversion['credits'] = "Based on TPLLeagueStats Xoops Port by Mithrandir,ralf57,M0nty";
$modversion['license'] = "GPL see LICENSE";
$modversion["license_file"] = XOOPS_URL."/modules/matchdart/gpl.txt";
$modversion["release_info"] = "README";
$modversion["release_file"] = XOOPS_URL."/modules/matchdart/readme.txt";
$modversion["manual"] = "MANUAL";
$modversion["manual_file"] = XOOPS_URL."/modules/matchdart/manual.txt";
$modversion['official'] = 0;
$modversion['image'] = "images/matchdart_slogo.png";
$modversion['dirname'] = "matchdart";

$modversion['demo_site_url'] = "http://www.matchdart.de/modules/matchdart";
$modversion['demo_site_name'] = "Match Dart";
$modversion["module_website_url"] = "http://www.matchdart.de/modules/matchdart";
$modversion["module_website_name"] = "Match Dart";

$modversion["release"] = "2008-03-04";
$modversion["module_status"] = "Release";

// Sql file
$modversion['sqlfile']['mysql'] = "sql/mysql.sql";

// Tables created by sql file (without prefix!)
$modversion['tables'][0] = "matchdart_matches";
$modversion['tables'][1] = "matchdart_players";
$modversion['tables'][2] = "matchdart_seasonnames";
$modversion['tables'][3] = "matchdart_eventnames";
//$modversion['tables'][4] = "matchdart_eventmatchtypes";
//$modversion['tables'][5] = "matchdart_matchlegs";

// Admin things
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = "admin/index.php";
$modversion['adminmenu'] = "admin/menu.php";

// Templates
$modversion['templates'][1]['file'] = 'xd_index.html';
$modversion['templates'][1]['description'] = '';
$modversion['templates'][2]['file'] = 'xd_stats.html';
$modversion['templates'][2]['description'] = '';
$modversion['templates'][3]['file'] = 'xd_matches_form.html';
$modversion['templates'][3]['description'] = '';
$modversion['templates'][4]['file'] = 'xd_matches_main.html';
$modversion['templates'][4]['description'] = '';
$modversion['templates'][5]['file'] = 'xd_select_header.html';
$modversion['templates'][5]['description'] = '';
$modversion['templates'][6]['file'] = 'xd_pvp.html';
$modversion['templates'][6]['description'] = '';

// Blocks
$modversion['blocks'][1]['file'] = "minitable.php";
$modversion['blocks'][1]['name'] = _MI_XD_MINITABLE;
$modversion['blocks'][1]['description'] = "Shows default season & event";
$modversion['blocks'][1]['show_func'] = "b_minitable_show";
$modversion['blocks'][1]['template'] = 'xd_block_minitable.html';


// Menu
$modversion['hasMain'] = 1;
$modversion['sub'][1]['name'] = _MI_XD_MENU_STATS;
$modversion['sub'][1]['url'] = "stats.php";
$modversion['sub'][2]['name'] = _MI_XD_MENU_PVP;
$modversion['sub'][2]['url'] = "pvp.php";
// Only permitted groups
$match_submit = 0;
$module_handler =& xoops_gethandler('module');
$module =& $module_handler->getByDirname($modversion['dirname']);
if ($module) {
    global $xoopsUser;
    if (is_object($xoopsUser)) {
        $groups = $xoopsUser->getGroups();
    } else {
        $groups = XOOPS_GROUP_ANONYMOUS;
    }
    $gperm_handler =& xoops_gethandler('groupperm');
    if ($gperm_handler->checkRight("match_submit", 0, $groups, $module->getVar('mid'))) {
          $match_submit = 1;
    }
}
if ($match_submit) {
    $modversion['sub'][3]['name'] = _MI_XD_MENU_MATCHES;
    $modversion['sub'][3]['url'] = "matches.php";
}
unset ($match_submit);

// Search
$modversion['hasSearch'] = 0;

// Comments
$modversion['hasComments'] = 0;

// Notification
$modversion['hasNotification'] = 0;

// Config Settings (only for modules that need config settings generated automatically) 
// name of config option for accessing its specified value. i.e. $xoopsModuleConfig['storyhome']
global $xoopsDB;
$query = $xoopsDB->query("SELECT SeasonID, SeasonName FROM ".$xoopsDB->prefix("matchdart_seasonnames")." WHERE SeasonPublish = '1' ORDER BY SeasonName");
while($xd_thisseason = $xoopsDB->fetchArray($query)) {
    $xd_allseasons[$xd_thisseason['SeasonName']] = $xd_thisseason['SeasonID'];
}
$query = $xoopsDB->query("SELECT EventID, EventName FROM ".$xoopsDB->prefix("matchdart_eventnames")." WHERE EventPublish = '1' ORDER BY EventName");
while($xd_thisevent = $xoopsDB->fetchArray($query)) {
    $xd_allevents[$xd_thisevent['EventName']] = $xd_thisevent['EventID'];
}
$i=1;
$modversion['config'][$i]['name'] = 'defaulttable';
$modversion['config'][$i]['title'] = '_MI_XD_DEFAULTTABLE';
$modversion['config'][$i]['description'] = 'description';
$modversion['config'][$i]['formtype'] = 'select';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 1;
$modversion['config'][$i]['options'] = array(_MI_XD_DEFAULTTABLESIM => 1, _MI_XD_DEFAULTTABLETRA => 2);
$i++;
$modversion['config'][$i] = array('name' => 'defaultshow', 'title' => '_MI_XD_DEFAULTSCHEDULE', 'description' => '', 'formtype' => 'select', 'valuetype' => 'int', 'default' => 1);
$modversion['config'][$i]['options'] = array(_MI_XD_DEFAULTSCHEDULEALL => 1, _MI_XD_DEFAULTSCHEDULEOWN => 2, _MI_XD_DEFAULTSCHEDULENONE => 3);
$i++;
$modversion['config'][$i]= array('name' => 'printdate', 'title' => '_MI_XD_DATE', 'description' => '_MI_XD_DATEDESCR', 'formtype' => 'textbox', 'valuetype' => 'text', 'default' => '%d.%m.%Y');

//Format of the date to use for Last Updated
$i++;
$modversion['config'][$i]['name'] = 'printlastupdate';
$modversion['config'][$i]['title'] = '_MI_XD_PRINTLASTUPDATE';
$modversion['config'][$i]['description'] = '_MI_XD_PRINTLASTUPDATEDESCR';
$modversion['config'][$i]['formtype'] = 'textbox';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = "d.m.Y @ H:i";

$i++;
$modversion['config'][$i] = array('name' => 'correction', 'title' => '_MI_XD_CORRECTION', 'description' => '', 'formtype' => 'textbox', 'valuetype' => 'text', 'default' => '0.5');
$i++;
$modversion['config'][$i] = array('name' => 'bg1', 'title' => '_MI_XD_HIGHLIGHTPLAYER', 'description' => '', 'formtype' => 'textbox', 'valuetype' => 'text', 'default' => '#FF0000');
$i++;
$modversion['config'][$i] = array('name' => 'bg2', 'title' => '_MI_XD_HIGHLIGHTPOINTS', 'description' => '', 'formtype' => 'textbox', 'valuetype' => 'text', 'default' => '#FFFFCC');
//Allow Select 'All Events' as table selection
$i++;
$modversion['config'][$i]['name'] = 'tb_allevents';
$modversion['config'][$i]['title'] = '_MI_XD_TBALLEVENTS';
$modversion['config'][$i]['description'] = '_MI_XD_TBALLEVENTSDESCR';
$modversion['config'][$i]['formtype'] = 'yesno';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 1;
//Allow Select 'All Seasons' as table selection
$i++;
$modversion['config'][$i]['name'] = 'tb_allseasons';
$modversion['config'][$i]['title'] = '_MI_XD_TBALLSEASONS';
$modversion['config'][$i]['description'] = '_MI_XD_TBALLSEASONSDESCR';
$modversion['config'][$i]['formtype'] = 'yesno';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 1;
//Use additional Player Stats
$i++;
$modversion['config'][$i]['name'] = 'displayplayerstats';
$modversion['config'][$i]['title'] = '_MI_XD_DISPLAYPLAYERSTATS';
$modversion['config'][$i]['description'] = '_MI_XD_DISPLAYPLAYERSTATSDESCR';
$modversion['config'][$i]['formtype'] = 'yesno';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 0;
//Select Header width
$i++;
$modversion['config'][$i]['name'] = 'header_width';
$modversion['config'][$i]['title'] = '_MI_XD_HEADERWIDTH';
$modversion['config'][$i]['description'] = '_MI_XD_HEADERWIDTHDESCR';
$modversion['config'][$i]['formtype'] = 'textbox';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = "75%";
//Table width (simple table)
$i++;
$modversion['config'][$i]['name'] = 'tb_simwidth';
$modversion['config'][$i]['title'] = '_MI_XD_TBSIMWIDTH';
$modversion['config'][$i]['description'] = '_MI_XD_TBSIMWIDTHDESCR';
$modversion['config'][$i]['formtype'] = 'textbox';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = "75%";
//Table width (traditional table)
$i++;
$modversion['config'][$i]['name'] = 'tb_trawidth';
$modversion['config'][$i]['title'] = '_MI_XD_TBTRAWIDTH';
$modversion['config'][$i]['description'] = '_MI_XD_TBTRAWIDTHDESCR';
$modversion['config'][$i]['formtype'] = 'textbox';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = "99%";
//Text size (traditional table)
$i++;
$modversion['config'][$i]['name'] = 'tb_tratext';
$modversion['config'][$i]['title'] = '_MI_XD_TBTRATEXT';
$modversion['config'][$i]['description'] = '_MI_XD_TBTRATEXTDESCR';
$modversion['config'][$i]['formtype'] = 'textbox';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = "10px";
//Table width (stats)
$i++;
$modversion['config'][$i]['name'] = 'stats_width';
$modversion['config'][$i]['title'] = '_MI_XD_STATSWIDTH';
$modversion['config'][$i]['description'] = '_MI_XD_STATSWIDTHDESCR';
$modversion['config'][$i]['formtype'] = 'textbox';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = "75%";
//Table width (pvp)
$i++;
$modversion['config'][$i]['name'] = 'pvp_width';
$modversion['config'][$i]['title'] = '_MI_XD_PVPWIDTH';
$modversion['config'][$i]['description'] = '_MI_XD_PVPWIDTHDESCR';
$modversion['config'][$i]['formtype'] = 'textbox';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = "75%";
//Table width (matches)
$i++;
$modversion['config'][$i]['name'] = 'matches_width';
$modversion['config'][$i]['title'] = '_MI_XD_MATCHESWIDTH';
$modversion['config'][$i]['description'] = '_MI_XD_MATCHESWIDTHDESCR';
$modversion['config'][$i]['formtype'] = 'textbox';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = "90%";

?>