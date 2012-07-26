<?php

define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
include($phpbb_root_path . 'includes/bbcode.' . $phpEx);
$user->session_begin();
$auth->acl($user->data);
$user->setup(array('acp/profile','acp/language','ucp'));

$f	= $_GET['f'];
$a	= $_POST['a'];

if (!$auth->acl_get('m_') && !$auth->acl_get('a_')) login_box($_SERVER['REQUEST_URI'], $user->lang['LOGIN_ADMIN_CONFIRM']);
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="./jscripts/tiny_mce3.5.4.1/tiny_mce.js"></script>
<script type="text/javascript">
	function calcSize()
	{
		var iframe = document.getElementById('_send');
		var innerDoc = iframe.contentDocument || iframe.contentWindow.document;
		height = innerDoc.getElementById("g").offsetHeight;
		width = innerDoc.getElementById("g").offsetWidth;
		iframe.height=height;
		iframe.width=width;
	}

	tinyMCE.init({
		// General options
		mode : "textareas",
		theme : "advanced",
		plugins : "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave,visualblocks",

		// Theme options
		theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
		theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
		theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,restoredraft,visualblocks",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,
		forced_root_block : '',
		// Example content CSS (should be your site CSS)
		content_css : "./styles/carpeviam-viridis/theme/stylesheet_hp.css",

		// Drop lists for link/image/media/template dialogs

		// Style formats
		style_formats : [
			{title : 'Bold text', inline : 'b'},
			{title : 'Red text', inline : 'span', styles : {color : '#ff0000'}},
			{title : 'Red header', block : 'h1', styles : {color : '#ff0000'}},
			{title : 'Example 1', inline : 'span', classes : 'example1'},
			{title : 'Example 2', inline : 'span', classes : 'example2'},
			{title : 'Table styles'},
			{title : 'Table row 1', selector : 'tr', classes : 'tablerow1'}
		],

		// Replace values for the template plugin

	});
</script>
<?php
function get_langselect()
{
	global $user, $db, $_GET;
	$l	= $_GET['l'];
	
	$sql = "SELECT `lang` FROM DB23680.`HTML` GROUP BY `lang`";
	$result = $db->sql_query($sql);
	$select = '<form accept-charset="utf-8" method="get"><select onchange="this.form.submit();" name="l" id="l" size="1" style="font-size: 1em;"><option disabled>'.$user->lang['LANGUAGE'].':</option>';
	while ($row = $db->sql_fetchrow($result))
	{	
		if ($row['lang'] == $l)
			$select .= '<option value="'.$row['lang'].'" selected>'.$row['lang'].'&nbsp;</option>';
		else 
			$select .= '<option value="'.$row['lang'].'">'.$row['lang'].'&nbsp;</option>';
	}
	$db->sql_freeresult($result);
	$select .= '</select>';
	foreach ($_GET as $k=>$v)
		if ($k != "l") 
			$select .= "<input type='hidden' name='$k' value='$v'>";  
	$select .= '</form>';
	return $select;
}
$l	= $_GET['l'];
if (!$l) 
{
	echo get_langselect();
	die;
}

function get_fileselect()
{
	global $db,$_GET,$_POST;
	$l	= $_GET['l'];
	$g	= $_GET['g'];

	$sql = "SELECT `guid`, `name`, `p` FROM DB23680.`HTML` WHERE `lang` = '".$l."' ORDER BY `id`,`p`";
	$result = $db->sql_query($sql);
	$select = '<form accept-charset="utf-8" method="post" target="_parent" action="'.$_SERVER['REQUEST_URL'].'?l='.$l.'"><select onchange="this.form.submit();" onclick="this.form.submit();" id="g" name="g" size="15" style="font-size: 1em;">';
	while ($row = $db->sql_fetchrow($result))
	{
		if ($row['name']) $name = $row['name']; 
		if ($row['guid'] == $g)
			$select .= '<option value="'.$row['guid'].'" selected>'.$name.' Seite: '.$row['p'].'&nbsp;</option>';
		else 
			$select .= '<option value="'.$row['guid'].'">'.$name.' Seite: '.$row['p'].'&nbsp;</option>';
	}
	$db->sql_freeresult($result);
	$select .= '</select><input type="hidden" name="a" value="load"></form>';
	return $select;
}

function load_editor()
{
	global $db,$user,$_POST;
	$g = $_POST['g'];
	if ($g) 
	{
		$sql = "SELECT * FROM DB23680.`HTML` WHERE `guid` = '".$g."'";
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
	}
	$site .= '	<table style="none">
					<tr><td>'.get_langselect().'</td><td rowspan="3" valign="top">
						<table>
							<tr><td valign="top"><form accept-charset="utf-8" name="html" method="post" target="_send" action="'.$_SERVER['REQUEST_URI'].'&f=fileselect&g='.$g.'"><textarea id="content" name="c" style="width:100%">'.$row['code'].'</textarea></td></tr>
							<tr><td><label for="id">ID: </label><input type="text" id="id" name="i" size="1" value="'.$row['id'].'" /> <label for="p">Seite: </label><input type="text" id="p" name="p" size="1" value="'.$row['p'].'" /> <label for="lang"> '.$user->lang['LANGUAGE'].': </label><input id="lang" type="text" name="l" size="1" value="'.$row['lang'].'" /><br>';
							
	if ($row['p'] == "1") $site .= '<label for="name"> '.$user->lang['LANGUAGE_PACK_NAME'].' (Button): </label><input id="name" type="text" name="n" size="10" value="'.$row['name'].'" /> <label for="button"> Button: </label><input id="button" type="text" name="b" size="10" value="'.$row['button'].'" /> <label for="titel">'.$user->lang['TOPIC'].': </label><input id="titel" type="text" name="t" size="15" value="'.$row['title'].'" />';
	$site .=			   '<label for="pt">'.$user->lang['LANGUAGE_PACK_NAME'].' (Seite): </label><input id="pt" type="text" name="pt" size="10" value="'.$row['p_title'].'" /><br><br><input type="submit" name="a" value="Save (New)" /> ';
			  if ($g) $site .= '<input type="submit" name="a" value="Save (Overwrite)" /> ';
			 else $site .= '<input type="submit" name="a" value="Save (Overwrite)" disabled/> ';
				  $site .= '<input type="hidden" name="g" value="'.$row['guid'].'"><input type="submit" name="a" value="Delete" /></td></tr>
						</table>
					</td></tr>
					<tr><td valign="top"><iframe onload="calcSize()" src="'.$_SERVER['REQUEST_URI'].'&f=fileselect&g='.$g.'" height="10" width="10"  scrolling="no" marginheight="0" marginwidth="0" frameborder="0" id="_send" name="_send"></iframe>
					</td></tr>
					<tr><td><div style="font-size: 50%; background: url(http://www.carpe-viam.org/styles/carpeviam-viridis/theme/images/Grass.jpg);">
							<a class="cv_carpe-viam" title="cv_carpe-viam" href=# onclick="document.getElementById(\'button\').value=\'cv_carpe-viam\';">1</a>
							<a class="cv_aktuelles" title="cv_aktuelles" onclick="document.getElementById(\'button\').value=\'cv_aktuelles\';" href=#><span>cv_aktuelles</span></a>
							<a class="cv_programm" title="cv_programm" onclick="document.getElementById(\'button\').value=\'cv_programm\';" href=#><span>cv_programm</span></a>
							<a class="cv_konzept" title="cv_konzept" onclick="document.getElementById(\'button\').value=\'cv_konzept\';" href=#><span>cv_konzept</span></a>
							<a class="cv_team" title="cv_team" onclick="document.getElementById(\'button\').value=\'cv_team\';" href=#><span>cv_team</span></a>
							<a class="cv_forum" title="cv_forum" onclick="document.getElementById(\'button\').value=\'cv_forum\';" href=#><span>cv_forum</span></a>
							<a class="cv_galery" title="cv_galery" onclick="document.getElementById(\'button\').value=\'cv_galery\';" href=#><span>cv_galery</span></a>
							<a class="cv_kontakt" title="cv_kontakt" onclick="document.getElementById(\'button\').value=\'cv_kontakt\';" href=#><span>cv_kontakt</span></a>
							<a class="cv_links" title="cv_links" onclick="document.getElementById(\'button\').value=\'cv_links\';" href=#><span>cv_links</span></a>
							<a class="cv_x1" title="cv_x1" onclick="document.getElementById(\'button\').value=\'cv_x1\';" href=#><span>cv_x1</span></a>
					</div></td></tr>
				</table>';
	return $site;
	
}
if ($f == "fileselect")
{
	switch ($a)
	{
		case "Save (Overwrite)":
			$sql = "UPDATE DB23680.`HTML` SET `id`='".$_POST['i']."',`p`='".$_POST['p']."',`p_title`='".$_POST['pt']."',`lang`='".strtolower($_GET['l'])."',`button`='".$_POST['b']."',`name`='".$_POST['n']."',`title`='".$_POST['t']."',`code`='".$_POST['c']."' WHERE `guid` = '".$_POST['g']."'";
			$db->sql_query($sql);
			break;
		case "Save (New)":
			$sql = "INSERT INTO DB23680.`HTML` (`id`, `p`, `lang`, `button`, `name`, `title`,`p_title`, `code`) VALUES ('".$_POST['i']."','".$_POST['p']."','".strtolower($_GET['l'])."','".$_POST['b']."','".$_POST['n']."','".$_POST['t']."','".$_POST['pt']."','".$_POST['c']."')";			
			$db->sql_query($sql);
			break;
		case "Delete":
			$sql = "DELETE FROM DB23680.`HTML` WHERE `guid` = '".$_POST['g']."'";
			$db->sql_query($sql);
			break;
		default:
			$sql = "UPDATE DB23680.`HTML` SET `id`='".$_POST['i']."',`p`='".$_POST['p']."',`p_title`='".$_POST['pt']."',`lang`='".strtolower($_GET['l'])."',`button`='".$_POST['b']."',`name`='".$_POST['n']."',`title`='".$_POST['t']."',`code`='".$_POST['c']."' WHERE `guid` = '".$_POST['g']."'";
			$db->sql_query($sql);
			break;
	}
	echo get_fileselect();
	return;
}
echo load_editor();
?>
<link rel="stylesheet" href="/styles/carpeviam-viridis/theme/stylesheet.css" type="text/css" />
<body style="background: #fff;"/>