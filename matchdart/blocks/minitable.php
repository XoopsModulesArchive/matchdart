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

function b_minitable_show()
{
	include_once(XOOPS_ROOT_PATH.'/modules/matchdart/include/functions.php');
	Global $xoopsUser, $xoopsModuleConfig;
	$blockfactor = xd_getmoduleoption('correction');
	$blockseason =  xd_getdefaultseasonid();
	$blockevent =  xd_getdefaulteventid();
	// $blockseason =  '%';
	// $blockevent =  '%';
	$block = Array();
	//get table
	$block = xd_gettable('mini', $blockseason, $blockevent, $blockfactor);
	// sort table by points
	usort($block, "xd_cmppoints");
	
	Return $block;
}

?>