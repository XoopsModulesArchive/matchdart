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
include "../../../include/cp_header.php";
if (@include_once XOOPS_ROOT_PATH."/Frameworks/art/functions.php")
{
	include_once XOOPS_ROOT_PATH."/Frameworks/art/functions.admin.php";
	$noframework = 0;
} else
{
	$noframework = 1;
}


// include the default language file for the admin interface
//if ( !@include_once(XOOPS_ROOT_PATH."/modules/".$xoopsModule->getVar("dirname")."/language/" . $xoopsConfig['language'] . "/admin.php")){
//    include_once(XOOPS_ROOT_PATH."/modules/".$xoopsModule->getVar("dirname")."/language/english/admin.php");
//}


$myts = &MyTextSanitizer::getInstance();
//include_once XOOPS_ROOT_PATH."/modules/".$xoopsModule->getVar("dirname")."/class/xoopsformloader.php";

if(!@include_once XOOPS_ROOT_PATH."/Frameworks/xoops22/class/xoopsformloader.php"){
	include_once XOOPS_ROOT_PATH."/class/xoopsformloader.php";
}
include_once XOOPS_ROOT_PATH . '/class/xoopsform/grouppermform.php';



/**
 * TODO : better or more standardized way to do this?
 */

xoops_cp_header();
//if ($noframework == 0){loadModuleAdminMenu(4);}
//if ($noframework == 1){include 'navbar.php';}
$indexAdmin = new ModuleAdmin();
echo $indexAdmin->addNavigation('permissions.php');

//$action = isset($_REQUEST['action']) ? strtolower($_REQUEST['action']) : "";
$module_id = $xoopsModule->getVar('mid');
$perms = array('match_submit');

$permform = new XoopsSimpleForm(_AM_XD_PERMISSION, 'actionform', 'permissions.php', "GET");
$permform->display();
$member_handler =& xoops_gethandler('member');
$grouplist =& $member_handler->getGroupList();
$gperm_handler =& xoops_gethandler('groupperm');
$permissionset = Array();


// Check for changes

$checkbox = Array('-1');

if (isset($_POST['submit'])) {
	if (isset($_POST['match_submit'])) {
		foreach ( array_keys($_POST['match_submit']) AS $i ) {
		   	$checkbox[] = $i;
		}
	}

	foreach (array_keys($grouplist) as $i) {
		$permissionset[$i]['groupid'] = $i;
		$permissionset[$i]['groupname'] = $grouplist[$i];
		$query = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("group_permission")." WHERE
							gperm_modid = '$module_id' AND gperm_groupid = '$i'");
		$groupperm = $xoopsDB->fetchArray($query);
		if($xoopsDB->getRowsNum($query) < 1)
		{
			$permissionset[$i]['permid'] = -1;
			$permissionset[$i]['permname'] = '';
			$permissionset[$i]['action'] = 'none';
			if (in_array($i, $checkbox)) {
				$permissionset[$i]['permname'] = 'match_submit';
				$permissionset[$i]['action'] = 'add';
			}
		} else
		{
			$permissionset[$i]['permid'] = $groupperm['gperm_id'];
			$permissionset[$i]['permname'] = $groupperm['gperm_name'];
			$permissionset[$i]['action'] = 'none';
			if (!in_array($i, $checkbox)) {
				$permissionset[$i]['permname'] = '';
				$permissionset[$i]['action'] = 'delete';
			}
		}
	}
	//update database
	foreach (array_keys($permissionset) as $k) {
		if ($permissionset[$k]['action'] == 'add') {
			$query = $xoopsDB->query("INSERT INTO ".$xoopsDB->prefix("group_permission")." SET
				gperm_groupid = ".$permissionset[$k]['groupid'].",
				gperm_itemid = '1',
				gperm_modid = '$module_id',
				gperm_name = 'match_submit'");
		} elseif ($permissionset[$k]['action'] == 'delete') {
			$query = $xoopsDB->query("DELETE FROM ".$xoopsDB->prefix("group_permission")." WHERE
				gperm_id = ".$permissionset[$k]['permid']."");
		} 
	}
}
// end check for changes


// Get actual permissions
foreach (array_keys($grouplist) as $i) {
	//echo $i,'&nbsp;';
	$permissionset[$i]['groupid'] = $i;
	$permissionset[$i]['groupname'] = $grouplist[$i];
	$query = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("group_permission")." WHERE
						gperm_modid = '$module_id' AND gperm_groupid = '$i'");
	$groupperm = $xoopsDB->fetchArray($query);
	if($xoopsDB->getRowsNum($query) < 1)
	{
		$permissionset[$i]['permid'] = -1;
		$permissionset[$i]['permname'] = '';
	} else
	{
		$permissionset[$i]['permid'] = $groupperm['gperm_id'];
		$permissionset[$i]['permname'] = $groupperm['gperm_name'];
	}
	
	//print_r($permissionset);
}





//add a permission: $gperm_handler->addRight($gperm_name, $gperm_itemid, $gperm_groupid, $gperm_modid = 1)

//$elements = array();
//$xdperm_handler = & xoops_getmodulehandler('permission', 'matchdart');

        foreach (array_keys($permissionset) as $i) {
        	//echo $i;
            $selected = array();
            $ret_ele  = '<tr align="left" valign="top"><td class="head">'.$permissionset[$i]['groupname'].'</td>';
			$ret_ele .= '<td class="even">';
			$ret_ele .= '<table class="outer">';
//			$ret_ele .= '<table class="outer"><tr><td class="odd"><table><tr>';
//			$ii = 0;
//			$option_ids = array();
//			foreach ($perms as $perm) {
//				$ii++;
//				if($ii % 5 ==0 ){
//					$ret_ele .= '</tr><tr>';
//				}
//				$checked = in_array("forum_".$perm, $selected)?" checked='checked'":"";
//				$option_id = $perm.'_'.$i;
//				$option_ids[] = $option_id;
//				$ret_ele .='<td><input name="perms['.$i.']['."forum_".$perm.']" id="'.$option_id.'" onclick="" value="1" type="checkbox"'.$checked.'>'.CONSTANT("_AM_NEWBB_CAN_".strtoupper($perm)).'<br></td>';
//			}
//			$ret_ele .= '</tr></table></td><td class="even">';
			$ret_ele .= '<td class="even">';
//			if ($gperm_handler->checkRight("match_submit", 0, $grouplist[$i], $module_id)) {
//          		$match_submit = 1;
//			}
			if ($permissionset[$i]['permid'] == -1){
				$ret_ele .= _AM_XD_SUBMITMATCHES.' <input id="match_submit['.$i.']" name="match_submit['.$i.']" type="checkbox" value="1">';
			} else
			{
				$ret_ele .= _AM_XD_SUBMITMATCHES.' <input id="match_submit['.$i.']" name="match_submit['.$i.']" type="checkbox" value="1" checked="checked">';
			}
			$ret_ele .= '</td></tr></table>';
			$ret_ele .= '</td></tr>';
            $elements[] = $ret_ele;
        }
        $tray = new XoopsFormElementTray('');
        //$tray->addElement(new XoopsFormHidden('action', 'template_save'));
        $tray->addElement(new XoopsFormButton('', 'submit', _SUBMIT, 'submit'));
        $tray->addElement(new XoopsFormButton('', 'reset', _CANCEL, 'reset'));
     	//$ret = '<h4>' . _AM_NEWBB_PERM_TEMPLATE . '</h4>' . _AM_NEWBB_PERM_TEMPLATE_DESC . '<br /><br /><br />';
        $ret = "<br><br><form name='template' id='template' method='post'>\n<table width='100%' class='outer' cellspacing='1'>\n";
        $ret .= implode("\n",$elements);
		$ret .= '<tr align="left" valign="top"><td class="head"></td><td class="even">';
        $ret .= $tray->render();
		$ret .= '</td></tr>';
        $ret .= '</table></form>';
        echo $ret;

//switch($action){
//	case "template":
//		$opform = new XoopsSimpleForm(_AM_NEWBB_PERM_ACTION, 'actionform', 'admin_permissions.php', "get");
////		$op_select = new XoopsFormSelect("", 'action');
////		$op_select->setExtra('onchange="document.forms.actionform.submit()"');
////		$op_select->addOptionArray(array(
////			"no"=>_SELECT, 
////			"template"=>_AM_NEWBB_PERM_TEMPLATE, 
////			"apply"=>_AM_NEWBB_PERM_TEMPLATEAPP,
////			"default"=>_AM_NEWBB_PERM_SETBYGROUP
////			));
////		$opform->addElement($op_select);
//		$opform->display();
//		
//        $member_handler =& xoops_gethandler('member');
//        $glist =& $member_handler->getGroupList();
//        $elements = array();
//        $newbbperm_handler = &xoops_getmodulehandler('permission', 'newbb');
//        $perm_template = $newbbperm_handler->getTemplate($groupid = 0);
//        foreach (array_keys($glist) as $i) {
//            $selected = !empty($perm_template[$i]) ? array_keys($perm_template[$i]) : array();
//            $ret_ele  = '<tr align="left" valign="top"><td class="head">'.$glist[$i].'</td>';
//			$ret_ele .= '<td class="even">';
//			$ret_ele .= '<table class="outer"><tr><td class="odd"><table><tr>';
//			$ii = 0;
//			$option_ids = array();
//			foreach ($perms as $perm) {
//				$ii++;
//				if($ii % 5 ==0 ){
//					$ret_ele .= '</tr><tr>';
//				}
//				$checked = in_array("forum_".$perm, $selected)?" checked='checked'":"";
//				$option_id = $perm.'_'.$i;
//				$option_ids[] = $option_id;
//				$ret_ele .='<td><input name="perms['.$i.']['."forum_".$perm.']" id="'.$option_id.'" onclick="" value="1" type="checkbox"'.$checked.'>'.CONSTANT("_AM_NEWBB_CAN_".strtoupper($perm)).'<br></td>';
//			}
//			$ret_ele .= '</tr></table></td><td class="even">';
//			$ret_ele .= _ALL.' <input id="checkall['.$i.']" type="checkbox" value="" onclick="var optionids = new Array('.implode(", ", $option_ids).'); xoopsCheckAllElements(optionids, \'checkall['.$i.']\')" />';
//			$ret_ele .= '</td></tr></table>';
//			$ret_ele .= '</td></tr>';
//            $elements[] = $ret_ele;
//        }
//        $tray = new XoopsFormElementTray('');
//        $tray->addElement(new XoopsFormHidden('action', 'template_save'));
//        $tray->addElement(new XoopsFormButton('', 'submit', _SUBMIT, 'submit'));
//        $tray->addElement(new XoopsFormButton('', 'reset', _CANCEL, 'reset'));
//     	$ret = '<h4>' . _AM_NEWBB_PERM_TEMPLATE . '</h4>' . _AM_NEWBB_PERM_TEMPLATE_DESC . '<br /><br /><br />';
//        $ret .= "<form name='template' id='template' method='post'>\n<table width='100%' class='outer' cellspacing='1'>\n";
//        $ret .= implode("\n",$elements);
//		$ret .= '<tr align="left" valign="top"><td class="head"></td><td class="even">';
//        $ret .= $tray->render();
//		$ret .= '</td></tr>';
//        $ret .= '</table></form>';
//        echo $ret;
//        break;	
//        
//	case "template_save":
//        $newbbperm_handler = &xoops_getmodulehandler('permission', 'newbb');
//        $res = $newbbperm_handler->setTemplate($_POST['perms'], $groupid = 0);
//        if($res){
//	    	redirect_header("admin_permissions.php?action=template", 2, _AM_NEWBB_PERM_TEMPLATE_CREATED);
//        }else{
//	    	redirect_header("admin_permissions.php?action=template", 2, _AM_NEWBB_PERM_TEMPLATE_ERROR);
//        }
//		break;
//		
//	case "apply":
//        $newbbperm_handler = &xoops_getmodulehandler('permission', 'newbb');
//	    $perm_template = $newbbperm_handler->getTemplate();
//		if($perm_template===null){
//	    	redirect_header("admin_permissions.php?action=template", 2, _AM_NEWBB_PERM_TEMPLATE);
//		}
//		
//		$opform = new XoopsSimpleForm(_AM_NEWBB_PERM_ACTION, 'actionform', 'admin_permissions.php', "get");
//		$op_select = new XoopsFormSelect("", 'action');
//		$op_select->setExtra('onchange="document.forms.actionform.submit()"');
//		$op_select->addOptionArray(array("no"=>_SELECT, "template"=>_AM_NEWBB_PERM_TEMPLATE, "apply"=>_AM_NEWBB_PERM_TEMPLATEAPP));
//		$opform->addElement($op_select);
//		$opform->display();
//		
//		$category_handler =& xoops_getmodulehandler('category', 'newbb');
//		$categories = $category_handler->getAllCats("", true);
//		
//		$forum_handler = &xoops_getmodulehandler('forum', 'newbb');
//		$forums = $forum_handler->getForumsByCategory(0, '', false);
//		$fm_options = array();
//		foreach (array_keys($categories) as $c) {
//			$fm_options[-1*$c] = "[".$categories[$c]->getVar('cat_title')."]";
//			foreach(array_keys($forums[$c]) as $f){
//				$fm_options[$f] = $forums[$c][$f]["title"];
//		        if(!isset($forums[$c][$f]["sub"])) continue;
//				foreach(array_keys($forums[$c][$f]["sub"]) as $s){
//					$fm_options[$s] = "-- ".$forums[$c][$f]["sub"][$s]["title"];
//				}
//			}
//		}
//		unset($forums, $categories);		
//		$fmform = new XoopsThemeForm(_AM_NEWBB_PERM_TEMPLATEAPP, 'fmform', 'admin_permissions.php', "post");
//		$fm_select = new XoopsFormSelect(_AM_NEWBB_PERM_FORUMS, 'forums', null, 10, true);
//		$fm_select->addOptionArray($fm_options);
//		$fmform->addElement($fm_select);
//        $tray = new XoopsFormElementTray('');
//        $tray->addElement(new XoopsFormHidden('action', 'apply_save'));
//        $tray->addElement(new XoopsFormButton('', 'submit', _SUBMIT, 'submit'));
//        $tray->addElement(new XoopsFormButton('', 'reset', _CANCEL, 'reset'));
//		$fmform->addElement($tray);
//		$fmform->display();
//		break;
//		
//	case "apply_save":
//		if(empty($_POST["forums"])) break;
//	    $newbbperm_handler =& xoops_getmodulehandler('permission', 'newbb');
//		foreach($_POST["forums"] as $forum){
//			if($forum < 1) continue;
//			$newbbperm_handler->applyTemplate($forum, $module_id);
//		}
//	    redirect_header("admin_permissions.php", 2, _AM_NEWBB_PERM_TEMPLATE_APPLIED);
//		break;
//		
//	default:
//		
//		$opform = new XoopsSimpleForm(_AM_NEWBB_PERM_ACTION, 'actionform', 'admin_permissions.php', "get");
//		$op_select = new XoopsFormSelect("", 'action');
//		$op_select->setExtra('onchange="document.forms.actionform.submit()"');
//		$op_select->addOptionArray(array(
//			"no"=>_SELECT, 
//			"template"=>_AM_NEWBB_PERM_TEMPLATE, 
//			"apply"=>_AM_NEWBB_PERM_TEMPLATEAPP,
//			"default"=>_AM_NEWBB_PERM_SETBYGROUP
//			));
//		$opform->addElement($op_select);
//		$opform->display();
//		
//		$forum_handler =& xoops_getmodulehandler('forum', 'newbb');
//		$forums = $forum_handler->getForumsByCategory(0, '', false);
//		$op_options = array("category"=>_AM_NEWBB_CAT_ACCESS);
//		$fm_options = array("category"=>array("title"=>_AM_NEWBB_CAT_ACCESS, "item"=>"category_access", "desc"=>"", "anonymous"=>true));
//		foreach($perms as $perm){
//			$op_options[$perm] = CONSTANT("_AM_NEWBB_CAN_".strtoupper($perm));
//			$fm_options[$perm] = array("title"=>CONSTANT("_AM_NEWBB_CAN_".strtoupper($perm)), "item"=>"forum_".$perm, "desc"=>"", "anonymous"=>true);
//		}
//		
//		$op_keys = array_keys($op_options);
//		$op = isset($_GET['op']) ? strtolower($_GET['op']) : (isset($_COOKIE['op']) ? strtolower($_COOKIE['op']):"");
//		if(empty($op)){
//			$op = $op_keys[0];
//			setCookie("op", isset($op_keys[1])?$op_keys[1]:"");
//		}else{
//			for($i=0;$i<count($op_keys);$i++){
//				if($op_keys[$i]==$op) break;
//			}
//			setCookie("op", isset($op_keys[$i+1])?$op_keys[$i+1]:"");
//		}
//		
//		$opform = new XoopsSimpleForm('', 'opform', 'admin_permissions.php', "get");
//		$op_select = new XoopsFormSelect("", 'op', $op);
//		$op_select->setExtra('onchange="document.forms.opform.submit()"');
//		$op_select->addOptionArray($op_options);
//		$opform->addElement($op_select);
//		$opform->display();
//		
//		$perm_desc = "";
//		
//		$form = new newbb_XoopsGroupPermForm($fm_options[$op]["title"], $module_id, $fm_options[$op]["item"], $fm_options[$op]["desc"], 'admin/admin_permissions.php', $fm_options[$op]["anonymous"]);
//		
//		$category_handler =& xoops_getmodulehandler('category', 'newbb');
//		$categories = $category_handler->getAllCats("", true);
//		if($op=="category"){
//			foreach (array_keys($categories) as $c) {
//				$form->addItem($c, $categories[$c]->getVar('cat_title'));
//			}
//			unset($categories);
//		}else{
//			foreach (array_keys($categories) as $c) {
//				$key_c = -1 * $c;
//				$form->addItem($key_c, "<strong>[".$categories[$c]->getVar('cat_title')."]</strong>");
//				foreach(array_keys($forums[$c]) as $f){
//			        $form->addItem($f, $forums[$c][$f]["title"], $key_c);
//			        if(!isset($forums[$c][$f]["sub"])) continue;
//					foreach(array_keys($forums[$c][$f]["sub"]) as $s){
//			        	$form->addItem($s, "&rarr;".$forums[$c][$f]["sub"][$s]["title"], $f);
//					}
//				}
//			}
//			unset($forums, $categories);		
//		}
//		$form->display();
//		
//		break;
//}

xoops_cp_footer();
?>