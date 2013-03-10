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

defined("XOOPS_ROOT_PATH") or die("XOOPS root path not defined");

$path = dirname(dirname(dirname(dirname(__FILE__))));
include_once $path . '/mainfile.php';

$dirname         = basename(dirname(dirname(__FILE__)));
$module_handler  = xoops_gethandler('module');
$module          = $module_handler->getByDirname($dirname);
$pathIcon32      = $module->getInfo('icons32');
$pathModuleAdmin = $module->getInfo('dirmoduleadmin');
$pathLanguage    = $path . $pathModuleAdmin;


if (!file_exists($fileinc = $pathLanguage . '/language/' . $GLOBALS['xoopsConfig']['language'] . '/' . 'main.php')) {
    $fileinc = $pathLanguage . '/language/english/main.php';
}

include_once $fileinc;

$adminmenu = array();
$i=0;
$adminmenu[$i]["title"] = _AM_MODULEADMIN_HOME;
$adminmenu[$i]['link'] = "admin/index.php";
$adminmenu[$i]["icon"]  = $pathIcon32 . '/home.png';
//$i++;
//$adminmenu[$i]['title'] = _AM_MODULEADMIN_HOME;
//$adminmenu[$i]['link'] = "admin/main.php";
//$adminmenu[$i]["icon"]  = $pathIcon32 . '/manage.png';
$i++;
$adminmenu[$i]['title'] = _MI_XD_ADMENUSEASONS;
$adminmenu[$i]['link'] = "admin/seasons.php";
$adminmenu[$i]["icon"]  = $pathIcon32 . '/category.png';
$i++;
$adminmenu[$i]['title'] = _MI_XD_ADMENUEVENTS;
$adminmenu[$i]['link'] = "admin/events.php";
$adminmenu[$i]["icon"]  = $pathIcon32 . '/event.png';
$i++;
$adminmenu[$i]['title'] = _MI_XD_ADMENUPLAYERS;
$adminmenu[$i]['link'] = "admin/players.php";
$adminmenu[$i]["icon"]  = $pathIcon32 . '/users.png';
$i++;
$adminmenu[$i]['title'] = _MI_XD_ADMENUPERMISSIONS;
$adminmenu[$i]['link'] = "admin/permissions.php";
$adminmenu[$i]["icon"]  = $pathIcon32 . '/permissions.png';
$i++;
$adminmenu[$i]['title'] = _MI_XD_ADMENUUPDATE;
$adminmenu[$i]['link'] = "admin/update.php";
$adminmenu[$i]["icon"]  = $pathIcon32 . '/update.png';
$i++;
$adminmenu[$i]['title'] = _MI_XD_ADMENUABOUT;
$adminmenu[$i]['link'] = "admin/about.php";
$adminmenu[$i]["icon"]  = $pathIcon32 . '/about.png';

//$i++;
//$adminmenu[$i]['title'] = _AM_MODULEADMIN_ABOUT;
//$adminmenu[$i]["link"]  = "admin/about0.php";
//$adminmenu[$i]["icon"]  = $pathIcon32 . '/about.png';