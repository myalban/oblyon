<?php
/**
 * Copyright (C) 2015-2016  Nicolas Rivera      <nrivera.pro@gmail.com>
 * Copyright (C) 2015-2019  Open-DSI            <support@open-dsi.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *	\file       admin/menus.php
 *	\ingroup    oblyon
 *	\brief      Menus Page < Oblyon Theme Configurator >
 */

// Dolibarr environment
$res = @include ("../../main.inc.php"); // From htdocs directory
if (! $res) {
  $res = @include ("../../../main.inc.php"); // From "custom" directory
}

// Libraries
require_once DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php";
require_once(DOL_DOCUMENT_ROOT.'/core/class/html.formadmin.class.php');
require_once(DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php');
require_once '../lib/oblyon.lib.php';

// Translations
$langs->loadLangs(array("admin","oblyon@oblyon"));

$action=GETPOST('action','alpha');

/*
 * Actions
 */

if (preg_match('/^set/',$action)) {
  // This is to force to add a new param after css urls to force new file loading
  // This set must be done before calling llxHeader().
  $_SESSION['dol_resetcache']=dol_print_date(dol_now(),'dayhourlog');
}

if ($action == 'set') {
  $value = GETPOST ( 'value', 'int' );
  $name = GETPOST ( 'name', 'text' );
    
  if ($value == 1) {
    $res = dolibarr_set_const($db, $name, $value, 'yesno', 0, '', $conf->entity);
    if (! $res > 0) $error ++;
  } else {
    $res = dolibarr_set_const($db, $name, $value, 'yesno', 0, '', $conf->entity);
    if (! $res > 0) $error ++;
  }

  if ($error) {
    setEventMessage ( 'Error', 'errors' );
  } else {
    setEventMessage ( $langs->trans ( 'Save' ), 'mesgs' );
  }
}
 
if ($action == 'setvar'){  
  $res = dolibarr_set_const($db, 'OBLYON_EFFECT_LEFTMENU', GETPOST('OBLYON_EFFECT_LEFTMENU'),'chaine',0,'',$conf->entity);
  $res = dolibarr_set_const($db, 'OBLYON_LOGO_PADDING', GETPOST('OBLYON_LOGO_PADDING'),'chaine',0,'',$conf->entity);
  
  if (! $res > 0) $error++;
  
  if ($error) {
    setEventMessage ( 'Error', 'errors' );
  } else {
    setEventMessage ( $langs->trans ( 'Save' ), 'mesgs' );
  }
}

/** 
 * View
 */

$formother= new FormOther($db);

$page_name = "OblyonMenusTitle";

//$morejs=array("/oblyon/js/oblyon.js");
llxHeader ( '', $langs->trans('OblyonMenusTitle'));

// subheader
$linkback = '<a href="' . DOL_URL_ROOT . '/admin/modules.php">' . $langs->trans ( "BackToModuleList" ) . '</a>';
print_fiche_titre ( $langs->trans ( $page_name ), $linkback );

// Configuration header
$head = oblyon_admin_prepare_head();
dol_fiche_head ( $head, 'menus', $langs->trans ( "Module113900Name" ), 0, "oblyon@oblyon" );

// Alert
if (!defined('MAIN_MODULE_OBLYON') && $conf->theme!="oblyon")
{
  print '<div class="bloc_warning">';
  print img_warning().' '.$langs->trans('OblyonErrorMessage');    
  print '</div>';
}
else
{
  print '<div class="bloc_success">';
  print $langs->trans('OblyonSuccessMessage');
  print '</div>';
}

/*
if (!($conf->global->MAIN_MENU_STANDARD == "oblyon_menu.php" && $conf->global->MAIN_MENU_SMARTPHONE == "oblyon_menu.php") || !($conf->global->MAIN_MENUFRONT_STANDARD == "oblyon_menu.php" && $conf->global->MAIN_MENUFRONT_SMARTPHONE == "oblyon_menu.php")) {
    print '<br>';
    print '<div class="bloc_warning">';
    print img_warning().' '.$langs->trans('OblyonMenusErrorMessage');    
    print '</div>';
}
*/

print '<form name="formmenus" method="POST" action="'.$_SERVER["PHP_SELF"].'">';
print '<input type="hidden" name="action" value="setvar">';

$var=true;
print '<table class="noborder" width="100%">';

// Invert menu
$var=!$var;
print '<tr '.$bc[$var].'>';
print '<td>'.$langs->trans('InvertMenus').'</td>';
$name='MAIN_MENU_INVERT';
if(! empty($conf->global->MAIN_MENU_INVERT))
{
    print '<td><a href="' . $_SERVER ['PHP_SELF'] . '?action=set&name='.$name.'&value=0">';
    print img_picto ( $langs->trans ( "Enabled" ), 'switch_on' );
    print "</a></td>\n";
}
else
{
    print '<td><a href="' . $_SERVER ['PHP_SELF'] . '?action=set&name='.$name.'&value=1">';
    print img_picto ( $langs->trans ( "Disabled" ), 'switch_off' );
    print "</a></td>\n";
}
print '</tr>';

// Top menu
print '<tr class="liste_titre"><td colspan="2">'.$langs->trans('TopMenu').'</td></tr>';

// Option only if invert menu is on
if($conf->global->MAIN_MENU_INVERT) {
    if($conf->global->OBLYON_SHOW_COMPNAME || $conf->global->OBLYON_HIDE_LEFTMENU)
	{
		$conf->global->OBLYON_FULLSIZE_TOPBAR = TRUE;
    }

	// Fullsize top bar
    print '<tr '.$bc[$var].'>';
    print '<td>'.$langs->trans('FullsizeTopBar');
    if(empty($conf->global->OBLYON_FULLSIZE_TOPBAR))
	{
		print '<br><span class="warning">'.$langs->trans('FullsizeTopBarWarning').'</span>';
    } 
    print '</td>';
    $name='OBLYON_FULLSIZE_TOPBAR';
    if(! empty($conf->global->OBLYON_FULLSIZE_TOPBAR))
	{
		if($conf->global->OBLYON_SHOW_COMPNAME || $conf->global->OBLYON_HIDE_LEFTMENU)
		{
			print '<td><a href="#" class="tmenudisabled">';
			print img_picto($langs->trans( "Enabled" ), 'switch_on');
			print "</a></td>\n";
		}
		else
		{
			print '<td><a href="' . $_SERVER ['PHP_SELF'] . '?action=set&name='.$name.'&value=0">';
			print img_picto($langs->trans("Enabled"), 'switch_on');
			print "</a></td>\n";
		}
    }
	else
	{
      print '<td><a href="' . $_SERVER ['PHP_SELF'] . '?action=set&name='.$name.'&value=1">';
      print img_picto ( $langs->trans ( "Disabled" ), 'switch_off' );
      print "</a></td>\n";
    }
    print '</tr>';
}

// Show company name
print '<tr '.$bc[$var].'>';
print '<td>'.$langs->trans('ShowCompanyName').'</td>';
$name='OBLYON_SHOW_COMPNAME';
if(! empty($conf->global->OBLYON_SHOW_COMPNAME))
{
    print '<td><a href="' . $_SERVER ['PHP_SELF'] . '?action=set&name='.$name.'&value=0">';
    print img_picto ( $langs->trans ( "Enabled" ), 'switch_on' );
    print "</a></td>\n";
}
else
{
    print '<td><a href="' . $_SERVER ['PHP_SELF'] . '?action=set&name='.$name.'&value=1">';
    print img_picto ( $langs->trans ( "Disabled" ), 'switch_off' );
    print "</a></td>\n";
}
print '</tr>';  

// Sticky bar
print '<tr '.$bc[$var].'>';
print '<td>'.$langs->trans('StickyTopBar');
if($conf->global->OBLYON_STICKY_TOPBAR)
{
    print '<br><span class="warning">'.img_warning().' '.$langs->trans('StickyTopBarWarning').'</span>';
    if($conf->global->MAIN_MENU_INVERT)
	{
		print '<br><span class="warning">'.$langs->trans('StickyTopBarInvertedWarning').'</span>';
    }
}
print '</td>';
$name='OBLYON_STICKY_TOPBAR';
if(! empty($conf->global->OBLYON_STICKY_TOPBAR))
{
    print '<td><a href="' . $_SERVER ['PHP_SELF'] . '?action=set&name='.$name.'&value=0">';
    print img_picto ( $langs->trans ( "Enabled" ), 'switch_on' );
    print "</a></td>\n";
}
else
{
    print '<td><a href="' . $_SERVER ['PHP_SELF'] . '?action=set&name='.$name.'&value=1">';
    print img_picto ( $langs->trans ( "Disabled" ), 'switch_off' );
    print "</a></td>\n";
}
print '</tr>';

// Hide top icons
if($conf->global->OBLYON_ELDY_ICONS && $conf->global->MAIN_MENU_INVERT)
{
	$conf->global->OBLYON_HIDE_TOPICONS = TRUE;
}
  
if($conf->global->OBLYON_ELDY_OLD_ICONS && $conf->global->MAIN_MENU_INVERT)
{
	$conf->global->OBLYON_HIDE_TOPICONS = TRUE;
} 

print '<tr '.$bc[$var].'>';
print '<td>'.$langs->trans('HideTopIcons').'</td>';
$name='OBLYON_HIDE_TOPICONS';
if(! empty($conf->global->OBLYON_HIDE_TOPICONS))
{
    if(($conf->global->OBLYON_ELDY_ICONS || $conf->global->OBLYON_ELDY_OLD_ICONS) && $conf->global->MAIN_MENU_INVERT)
	{
		print '<td><a href="#" class="tmenudisabled">';
		print img_picto ( $langs->trans ( "Enabled" ), 'switch_on' );
		print "</a></td>\n";
    }
	else
	{
		print '<td><a href="' . $_SERVER ['PHP_SELF'] . '?action=set&name='.$name.'&value=0">';
		print img_picto ( $langs->trans ( "Enabled" ), 'switch_on' );
		print "</a></td>\n";
    }
}
else
{
	print '<td><a href="' . $_SERVER ['PHP_SELF'] . '?action=set&name='.$name.'&value=1">';
	print img_picto ( $langs->trans ( "Disabled" ), 'switch_off' );
	print "</a></td>\n";
}
print '</tr>';

// Use Eldy icons
if(empty($conf->global->MAIN_MENU_INVERT))
{
    if($conf->global->OBLYON_ELDY_OLD_ICONS || $conf->global->OBLYON_HIDE_LEFTICONS)
	{
		$conf->global->OBLYON_ELDY_ICONS = FALSE;
    }

    print '<tr '.$bc[$var].'>';
    print '<td>'.$langs->trans('EldyIcons');
    if($conf->global->OBLYON_ELDY_ICONS)
	{
		print '<br><span class="warning">'.$langs->trans('EldyIconsWarning').'</span>';
    } 
    print '</td>';    
    $name='OBLYON_ELDY_ICONS';
    if(! empty($conf->global->OBLYON_ELDY_ICONS))
	{
        print '<td><a href="' . $_SERVER ['PHP_SELF'] .  '?action=set&name='.$name.'&value=0">';
        print img_picto ( $langs->trans ( "Enabled" ), 'switch_on' );
        print "</a></td>\n";
    } 
	else
	{
		if ( $conf->global->OBLYON_ELDY_OLD_ICONS || $conf->global->OBLYON_HIDE_LEFTICONS ) {
			print '<td><a href="#" class="tmenudisabled">';
			print img_picto ( $langs->trans ( "Disabled" ), 'switch_off' );
			print "</a></td>\n";
		}
		else 
		{
			print '<td><a href="' . $_SERVER ['PHP_SELF'] .  '?action=set&name='.$name.'&value=1">';
			print img_picto ( $langs->trans ( "Disabled" ), 'switch_off' );
			print "</a></td>\n";
		}
    }
    print '</tr>';
}

// Use old Eldy icons
if(empty($conf->global->MAIN_MENU_INVERT))
{
    if($conf->global->OBLYON_ELDY_ICONS || $conf->global->OBLYON_HIDE_LEFTICONS)
	{
		$conf->global->OBLYON_ELDY_OLD_ICONS = FALSE;
    }

	print '<tr '.$bc[$var].'>';
    print '<td>'.$langs->trans('EldyOldIcons');
    if ( $conf->global->OBLYON_ELDY_OLD_ICONS )
	{
		print '<br><span class="warning">'.$langs->trans('EldyOldIconsWarning').'</span>';
    } 
    print '</td>';
    $name='OBLYON_ELDY_OLD_ICONS';
    if(! empty($conf->global->OBLYON_ELDY_OLD_ICONS))
	{
		print '<td><a href="' . $_SERVER ['PHP_SELF'] .  '?action=set&name='.$name.'&value=0">';
		print img_picto ( $langs->trans ( "Enabled" ), 'switch_on' );
		print "</a></td>\n";
    }
	else
	{
		if ( $conf->global->OBLYON_ELDY_ICONS || $conf->global->OBLYON_HIDE_LEFTICONS ) {
			print '<td><a href="#" class="tmenudisabled">';
			print img_picto ( $langs->trans ( "Disabled" ), 'switch_off' );
			print "</a></td>\n";
		} 
		else
		{
			print '<td><a href="' . $_SERVER ['PHP_SELF'] .  '?action=set&name='.$name.'&value=1">';
			print img_picto ( $langs->trans ( "Disabled" ), 'switch_off' );
			print "</a></td>\n";
		}
    }
    print '</tr>';
}
  
// Left menu
print '<tr class="liste_titre"><td colspan="2">'.$langs->trans('LeftMenu').'</td></tr>';

// Show company name
if(empty($conf->global->MAIN_MENU_INVERT))
{
	print '<tr '.$bc[$var].'>';
	print '<td>'.$langs->trans('ShowCompanyName').'</td>';
    $name='OBLYON_SHOW_COMPNAME';
    if(! empty($conf->global->OBLYON_SHOW_COMPNAME))
	{
		print '<td><a href="' . $_SERVER ['PHP_SELF'] . '?action=set&name='.$name.'&value=0">';
		print img_picto($langs->trans("Enabled"), 'switch_on');
		print "</a></td>\n";
    } 
	else 
	{
		print '<td><a href="' . $_SERVER ['PHP_SELF'] . '?action=set&name='.$name.'&value=1">';
		print img_picto($langs->trans("Disabled"), 'switch_off');
		print "</a></td>\n";
    }
    print '</tr>';  
}

// Hide leftmenu
print '<tr '.$bc[$var].'>';
print '<td>'.$langs->trans('HideLeftMenu');
print '</td>';
$name='OBLYON_HIDE_LEFTMENU';
if(! empty($conf->global->OBLYON_HIDE_LEFTMENU))
{
	print '<td><a href="' . $_SERVER ['PHP_SELF'] . '?action=set&name='.$name.'&value=0">';
	print img_picto($langs->trans("Enabled"), 'switch_on');
	print "</a></td>\n";
}
else
{
    print '<td><a href="' . $_SERVER ['PHP_SELF'] . '?action=set&name='.$name.'&value=1">';
    print img_picto($langs->trans("Disabled"), 'switch_off');
    print "</a></td>\n";
}
print '</tr>';

// Effect open leftmenu
if($conf->global->OBLYON_HIDE_LEFTMENU)
{
    if(empty($conf->global->OBLYON_EFFECT_LEFTMENU))
	{
		$conf->global->OBLYON_EFFECT_LEFTMENU= 'slide';
    }
    print '<tr '.$bc[$var].'>';
    print '<td>'.$langs->trans('OpenEffect');
    print '</td>';
    $name='OBLYON_EFFECT_LEFTMENU';
    print '<td>';
    print '<input type="radio" value="slide" id="slide" class="flat" name="'.$name.'" '.($conf->global->$name == "slide"?' checked="checked"':'').'"> ';
    print '<label for="slide">'.$langs->trans( 'EffectLeftMenuSlide' ).'</label><br>'; 
    print '<input type="radio" value="push" id="push" class="flat" name="'.$name.'" '.($conf->global->$name == "push"?' checked="checked"':'').'"> ';
    print '<label for="push">'.$langs->trans( 'EffectLeftMenuPush' ).'</label><br>';         
    print '</td>';
    print '</tr>';
}

// Hide left icons
if($conf->global->OBLYON_ELDY_OLD_ICONS && !$conf->global->MAIN_MENU_INVERT)
{
	$conf->global->OBLYON_HIDE_LEFTICONS = TRUE;
} 
   
print '<tr '.$bc[$var].'>';
print '<td>'.$langs->trans('HideLeftIcons').'</td>';
$name='OBLYON_HIDE_LEFTICONS';
if(! empty($conf->global->OBLYON_HIDE_LEFTICONS))
{
    if($conf->global->OBLYON_ELDY_OLD_ICONS && !$conf->global->MAIN_MENU_INVERT)
	{
		print '<td><a href="#" class="tmenudisabled">';
		print img_picto($langs->trans("Enabled"), 'switch_on');
		print "</a></td>\n";
    }
	else
	{
		print '<td><a href="' . $_SERVER ['PHP_SELF'] . '?action=set&name='.$name.'&value=0">';
		print img_picto($langs->trans("Enabled"), 'switch_on');
		print "</a></td>\n";
    }   
}
else
{
    print '<td><a href="' . $_SERVER ['PHP_SELF'] . '?action=set&name='.$name.'&value=1">';
    print img_picto($langs->trans("Disabled"), 'switch_off');
    print "</a></td>\n";
}
print '</tr>';
  
// Use Eldy icons
if(! empty($conf->global->MAIN_MENU_INVERT))
{
    if($conf->global->OBLYON_ELDY_OLD_ICONS || $conf->global->OBLYON_HIDE_LEFTICONS)
	{
		$conf->global->OBLYON_ELDY_ICONS = FALSE;
    }

    print '<tr '.$bc[$var].'>';
    print '<td>'.$langs->trans('EldyIcons');
    if($conf->global->OBLYON_ELDY_ICONS)
	{
		print '<br><span class="warning">'.$langs->trans('EldyIconsWarning').'</span>';
    } 
    print '</td>';
    $name='OBLYON_ELDY_ICONS';
    if(! empty($conf->global->OBLYON_ELDY_ICONS))
	{
		print '<td><a href="' . $_SERVER ['PHP_SELF'] .  '?action=set&name='.$name.'&value=0">';
		print img_picto($langs->trans("Enabled"), 'switch_on');
		print "</a></td>\n";
    } 
	else
	{
		if ( $conf->global->OBLYON_ELDY_OLD_ICONS || $conf->global->OBLYON_HIDE_LEFTICONS ) {
			print '<td><a href="#" class="tmenudisabled">';
			print img_picto($langs->trans("Disabled"), 'switch_off');
			print "</a></td>\n";
		}
		else
		{
			print '<td><a href="' . $_SERVER ['PHP_SELF'] .  '?action=set&name='.$name.'&value=1">';
			print img_picto($langs->trans("Disabled"), 'switch_off');
			print "</a></td>\n";
		}
    }
    print '</tr>';
}

// Use old Eldy icons
if(! empty($conf->global->MAIN_MENU_INVERT))
{
	if($conf->global->OBLYON_ELDY_ICONS || $conf->global->OBLYON_HIDE_LEFTICONS)
	{
		$conf->global->OBLYON_ELDY_OLD_ICONS = FALSE;
	}

	print '<tr '.$bc[$var].'>';
	print '<td>'.$langs->trans('EldyOldIcons');
	if ( $conf->global->OBLYON_ELDY_OLD_ICONS ) {
		print '<br><span class="warning">'.$langs->trans('EldyOldIconsWarning').'</span>';
	} 
	print '</td>';
	$name='OBLYON_ELDY_OLD_ICONS';
	if (! empty ( $conf->global->OBLYON_ELDY_OLD_ICONS ))
	{
		print '<td><a href="' . $_SERVER ['PHP_SELF'] .  '?action=set&name='.$name.'&value=0">';
		print img_picto($langs->trans("Enabled"), 'switch_on');
		print "</a></td>\n";
	}
	else
	{
		if($conf->global->OBLYON_ELDY_ICONS || $conf->global->OBLYON_HIDE_LEFTICONS)
		{
			print '<td><a href="#" class="tmenudisabled">';
			print img_picto($langs->trans("Disabled"), 'switch_off');
			print "</a></td>\n";
		}
		else
		{
			print '<td><a href="' . $_SERVER ['PHP_SELF'] .  '?action=set&name='.$name.'&value=1">';
			print img_picto($langs->trans("Disabled"), 'switch_off');
			print "</a></td>\n";
		}
	}
	print '</tr>';
}

// Show Company Logo
print '<tr '.$bc[$var].'>';
print '<td>'.$langs->trans('EnableShowLogo').'</td>';
$name='MAIN_SHOW_LOGO';
if(! empty($conf->global->MAIN_SHOW_LOGO))
{
	print '<td><a href="' . $_SERVER ['PHP_SELF'] .  '?action=set&name='.$name.'&value=0">';
	print img_picto($langs->trans("Enabled"), 'switch_on');
	print "</a></td>\n";
}
else
{
	print '<td><a href="' . $_SERVER ['PHP_SELF'] . '?action=set&name='.$name.'&value=1">';
	print img_picto($langs->trans("Disabled"), 'switch_off');
	print "</a></td>\n";
}
print '</tr>';

// Show options for logo when it's on
if(! empty($conf->global->MAIN_SHOW_LOGO))
{
	// Logo size
	print '<tr '.$bc[$var].'>';
	print '<td>'.$langs->trans('LogoSize').'</td>';
	$name='OBLYON_LOGO_SIZE';
	if(! empty($conf->global->OBLYON_LOGO_SIZE))
	{
		print '<td><a href="' . $_SERVER ['PHP_SELF'] .  '?action=set&name='.$name.'&value=0">';
		print img_picto($langs->trans("Enabled"), 'switch_on');
		print "</a></td>\n";
	}
	else
	{
		print '<td><a href="' . $_SERVER ['PHP_SELF'] . '?action=set&name='.$name.'&value=1">';
		print img_picto($langs->trans("Disabled"), 'switch_off');
		print "</a></td>\n";
	}
	print '</tr>';

	// Padding logo
	if ( empty ($conf->global->OBLYON_LOGO_PADDING) )
	{
		$conf->global->OBLYON_LOGO_PADDING = "padding";
	} 
	print '<tr '.$bc[$var].'>';
	print '<td>'.$langs->trans('LogoPadding').'</td>';
	$name='OBLYON_LOGO_PADDING';
	print '<td>';
	print '<input type="radio" value="nopadding" id="nopadding" class="flat" name="'.$name.'" '.($conf->global->OBLYON_LOGO_PADDING == "nopadding"?' checked="checked"':'').'"> ';
	print '<label for="nopadding">'.$langs->trans( 'NoPaddingLogo' ).'</label><br>';       
	print '<input type="radio" value="padding" id="padding" class="flat" name="'.$name.'" '.($conf->global->OBLYON_LOGO_PADDING == "padding"?' checked="checked"':'').'"> ';
	print '<label for="padding">'.$langs->trans( 'PaddingLogo' ).'</label><br>';   
	print '</td>';
	print '</tr>';
}
print '</table>';
dol_fiche_end();
  
print '<div class="center">';
print '<input type="submit" name="button" class="button" value="'.$langs->trans("Save").'">';
print '</div>';

print "</form>\n";

print '</table>';

llxFooter();
$db->close();