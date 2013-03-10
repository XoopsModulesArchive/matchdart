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

// get module preferences
$xd_print_date = $xoopsModuleConfig['printdate'];
$xd_print_lastupdate = $xoopsModuleConfig['printlastupdate'];
$xd_degradefactor = $xoopsModuleConfig['correction'];
$xd_bg_highlightplayer = $xoopsModuleConfig['bg1']; //highlight my player
$xd_bg_highlightpts = $xoopsModuleConfig['bg2']; //highlight points + matches played
$xd_tballevents = $xoopsModuleConfig['tb_allevents'];
$xd_tballseasons = $xoopsModuleConfig['tb_allseasons'];
$xd_displayplayerstats = $xoopsModuleConfig['displayplayerstats'];
$xd_tb_simwidth = $xoopsModuleConfig['tb_simwidth'];
$xd_tb_trawidth = $xoopsModuleConfig['tb_trawidth'];
$xd_tb_tratext = $xoopsModuleConfig['tb_tratext'];
$xd_stats_width = $xoopsModuleConfig['stats_width'];
$xd_pvp_width = $xoopsModuleConfig['pvp_width'];
$xd_header_width = $xoopsModuleConfig['header_width'];
$xd_matches_width = $xoopsModuleConfig['matches_width'];

// assign preferences
$xoopsTpl->assign('xd_degradefactor',$xd_degradefactor);
$xoopsTpl->assign('xd_highlightplayer',$xd_bg_highlightplayer); //highlight my player
$xoopsTpl->assign('xd_highlightpts',$xd_bg_highlightpts); //highlight points + matches played
$xoopsTpl->assign('xd_tballevents',$xd_tballevents);
$xoopsTpl->assign('xd_tballseasons',$xd_tballseasons);
$xoopsTpl->assign('xd_displayplayerstats',$xd_displayplayerstats);
$xoopsTpl->assign('xd_tb_simwidth',$xd_tb_simwidth);
$xoopsTpl->assign('xd_tb_trawidth',$xd_tb_trawidth);
$xoopsTpl->assign('xd_tb_tratext',$xd_tb_tratext);
$xoopsTpl->assign('xd_stats_width',$xd_stats_width);
$xoopsTpl->assign('xd_pvp_width',$xd_pvp_width);
$xoopsTpl->assign('xd_header_width',$xd_header_width);
$xoopsTpl->assign('xd_matches_width',$xd_matches_width);

// assign config options
$xoopsTpl->assign('xd_language',$xoopsConfig['language']);

?>
