<?php
/**
*
* @package phpBB3
* @version $Id$
* @copyright (c) 2005 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
*/

/**
* @ignore
*/
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('viewforum');

$section = $_GET['section'];
$page = $_GET['page'];
/*if (!class_exists('phpbb_mods_who_was_here'))
{
	include($phpbb_root_path . 'includes/mods/who_was_here.' . $phpEx);
	phpbb_mods_who_was_here::update_session();
}
phpbb_mods_who_was_here::display();
*/	
if ($section == "forum") {
	display_forums('', $config['load_moderators']);

	// Set some stats, get posts count from forums data if we... hum... retrieve all forums data
	$total_posts	= $config['num_posts'];
	$total_topics	= $config['num_topics'];
	$total_users	= $config['num_users'];

	$l_total_user_s = ($total_users == 0) ? 'TOTAL_USERS_ZERO' : 'TOTAL_USERS_OTHER';
	$l_total_post_s = ($total_posts == 0) ? 'TOTAL_POSTS_ZERO' : 'TOTAL_POSTS_OTHER';
	$l_total_topic_s = ($total_topics == 0) ? 'TOTAL_TOPICS_ZERO' : 'TOTAL_TOPICS_OTHER';

	// Grab group details for legend display
	if ($auth->acl_gets('a_group', 'a_groupadd', 'a_groupdel'))
	{
		$sql = 'SELECT group_id, group_name, group_colour, group_type
			FROM ' . GROUPS_TABLE . '
			WHERE group_legend = 1
			ORDER BY group_name ASC';
	}
	else
	{
		$sql = 'SELECT g.group_id, g.group_name, g.group_colour, g.group_type
			FROM ' . GROUPS_TABLE . ' g
			LEFT JOIN ' . USER_GROUP_TABLE . ' ug
				ON (
					g.group_id = ug.group_id
					AND ug.user_id = ' . $user->data['user_id'] . '
					AND ug.user_pending = 0
				)
			WHERE g.group_legend = 1
				AND (g.group_type <> ' . GROUP_HIDDEN . ' OR ug.user_id = ' . $user->data['user_id'] . ')
			ORDER BY g.group_name ASC';
	}
	$result = $db->sql_query($sql);

	$legend = array();
	while ($row = $db->sql_fetchrow($result))
	{
		$colour_text = ($row['group_colour']) ? ' style="color:#' . $row['group_colour'] . '"' : '';
		$group_name = ($row['group_type'] == GROUP_SPECIAL) ? $user->lang['G_' . $row['group_name']] : $row['group_name'];

		if ($row['group_name'] == 'BOTS' || ($user->data['user_id'] != ANONYMOUS && !$auth->acl_get('u_viewprofile')))
		{
			$legend[] = '<span' . $colour_text . '>' . $group_name . '</span>';
		}
		else
		{
			$legend[] = '<a' . $colour_text . ' href="' . append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=group&amp;g=' . $row['group_id']) . '">' . $group_name . '</a>';
		}
	}
	$db->sql_freeresult($result);

	$legend = implode(', ', $legend);

	// Generate birthday list if required ...
	$birthday_list = '';
	if ($config['load_birthdays'] && $config['allow_birthdays'])
	{
		$now = getdate(time() + $user->timezone + $user->dst - date('Z'));
		$sql = 'SELECT u.user_id, u.username, u.user_colour, u.user_birthday
			FROM ' . USERS_TABLE . ' u
			LEFT JOIN ' . BANLIST_TABLE . " b ON (u.user_id = b.ban_userid)
			WHERE (b.ban_id IS NULL
				OR b.ban_exclude = 1)
				AND u.user_birthday LIKE '" . $db->sql_escape(sprintf('%2d-%2d-', $now['mday'], $now['mon'])) . "%'
				AND u.user_type IN (" . USER_NORMAL . ', ' . USER_FOUNDER . ')';
		$result = $db->sql_query($sql);

		while ($row = $db->sql_fetchrow($result))
		{
			$birthday_list .= (($birthday_list != '') ? ', ' : '') . get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']);

			if ($age = (int) substr($row['user_birthday'], -4))
			{
				$birthday_list .= ' (' . ($now['year'] - $age) . ')';
			}
		}
		$db->sql_freeresult($result);
	}

	if (class_exists('phpbb_gallery_integration'))
	{
		phpbb_gallery_integration::index_total_images();
	}

	// Assign index specific vars
	$template->assign_vars(array(
		'TOTAL_POSTS'	=> sprintf($user->lang[$l_total_post_s], $total_posts),
		'TOTAL_TOPICS'	=> sprintf($user->lang[$l_total_topic_s], $total_topics),
		'TOTAL_USERS'	=> sprintf($user->lang[$l_total_user_s], $total_users),
		'NEWEST_USER'	=> sprintf($user->lang['NEWEST_USER'], get_username_string('full', $config['newest_user_id'], $config['newest_username'], $config['newest_user_colour'])),

		'LEGEND'		=> $legend,
		'BIRTHDAY_LIST'	=> $birthday_list,

		'FORUM_IMG'				=> $user->img('forum_read', 'NO_UNREAD_POSTS'),
		'FORUM_UNREAD_IMG'			=> $user->img('forum_unread', 'UNREAD_POSTS'),
		'FORUM_LOCKED_IMG'		=> $user->img('forum_read_locked', 'NO_UNREAD_POSTS_LOCKED'),
		'FORUM_UNREAD_LOCKED_IMG'	=> $user->img('forum_unread_locked', 'UNREAD_POSTS_LOCKED'),

		'S_LOGIN_ACTION'			=> append_sid("{$phpbb_root_path}ucp.$phpEx", 'mode=login'),
		'S_DISPLAY_BIRTHDAY_LIST'	=> ($config['load_birthdays']) ? true : false,

		'U_MARK_FORUMS'		=> ($user->data['is_registered'] || $config['load_anon_lastread']) ? append_sid("{$phpbb_root_path}index.$phpEx", 'hash=' . generate_link_hash('global') . '&amp;mark=forums') : '',
		'U_MCP'				=> ($auth->acl_get('m_') || $auth->acl_getf_global('m_')) ? append_sid("{$phpbb_root_path}mcp.$phpEx", 'i=main&amp;mode=front', true, $user->session_id) : '')
	);

	// Output page
	page_header($user->lang['INDEX']);

	$template->set_filenames(array(
		'body' => 'index_body.html')
	);

	page_footer();
}

elseif ($section == "galery") {
	include("cv_galerie.php");
}

else {
	if ($_GET['my_lang'])
	{
		$user->lang_name = $_GET['my_lang'];
	}
	$multipage_nav = "";
	$multipage = false;
    if (!$section) $section = 1;
	if (!$page) $page = 1;
	$sql = "SELECT name,code,p,p_title FROM DB23680.`HTML` WHERE (lang = '".$user->lang_name."' OR lang = 'all') AND id = '".$section."' ORDER BY `p` DESC, `id` ASC";
	$result = $db->sql_query($sql);
	while ($row = $db->sql_fetchrow($result)) {
		if ($row['p'] > 1) {
			$multipage = true;
		}
		if ($multipage) {
			if ($row['p'] == $page) $multipage_nav = "<span style=\"font-family: 'courier new',courier; font-size: xx-large; color: #930;\"><strong>".$row['p_title']."</strong></span> ".$multipage_nav;
			else $multipage_nav = "<span style=\"text-decoration:underline; font-family: \'courier new\',courier; font-size: large;\"><a href=?section=".$section."&page=".$row['p']."&my_lang=".$user->lang_name.">&lt;".$row['p_title']."&gt;</a></strong></span> ".$multipage_nav;
		}
		if ($page == $row['p']) {
			$content .= $row['code'];
			$name = $row['name'];
		}
	}
	$sql = "SELECT * FROM DB23680.`HTML` WHERE id = '".$section."' AND `p` = '".$page."' ORDER BY 'id'";
	$result = $db->sql_query($sql);
	while ($row = $db->sql_fetchrow($result)) {
		$lang .= "<a href=?my_lang=".$row['lang']."&section=".$section.">".$row['lang']."</a> ";
	}
	$wsa_info .= "Sprache: ".$user->lang_name." Diese Seite ist in folgenden Sprachen verfügbar: ".$lang;
	if (!$content) {
		$sql = "SELECT name,code,p,p_title FROM DB23680.`HTML` WHERE (lang = 'en' OR lang = 'all') AND id = '".$section."' ORDER BY 'id'";
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result)) {
			if ($row['p'] > 1) {
				$multipage = true;
			}
			if ($multipage) {
				if ($row['p'] == $page) $multipage_nav = $row['p_title']." ".$multipage_nav;
				else $multipage_nav = "<a href=?section=".$section."&page=".$row['p']."&my_lang=".$user->lang_name.">".$row['p_title']."</a> ".$multipage_nav;
			}
			if ($page == $row['p']) {
				$content .= $row['code'];
				$name = $row['name'];
			}
		}
	}
	if (strlen($content) > 5000) $content = $multipage_nav.$content.$multipage_nav;
	else $content = $multipage_nav.$content;
	$debug = null;
	function _read($socket)
	{
		$data .= fgets($socket, 1024);
		if ($debug) print "Server <- ".$data."<br>";
		return $data;
	}

	function _write($socket,$data)
	{
		if ($debug) print "Client -> ".$data."<br>";
		fputs($socket, $data."\r\n");
	}

	function _xor($string, $string2)
	{
		$result = '';
		$size   = strlen($string);

		for ($i=0; $i<$size; $i++) {
			$result .= chr(ord($string[$i]) ^ ord($string2[$i]));
		}

		return $result;
	}

	function unreademails() {
		global $eusername, $epassword; // In die Config.php verschoben wegen GIT
		$ipad = '';
		$opad = '';

		if ($debug) echo "Open...<br>";
		if(!$socket = fsockopen("ssl://imap.worldserver.net", "993", $errno, $errstr, 3))
			return false;
		if ($debug) echo "Verbindung hergestellt...<br>";
		
		_read($socket);
		
		_write($socket,"a AUTHENTICATE CRAM-MD5");
		
		// generate reply
		$line = trim(_read($socket));
		if ($line[0] == '+') {
			$challenge = substr($line, 2);
		}
		
		// initialize ipad, opad
		for ($i=0; $i<64; $i++) {
			$ipad .= chr(0x36);
			$opad .= chr(0x5C);
		}

		// pad $epassword so it's 64 bytes
		$padLen = 64 - strlen($epassword);
		for ($i=0; $i<$padLen; $i++) {
			$epassword .= chr(0);
		}

		// generate hash
		$hash  = md5(_xor($epassword, $opad) . pack("H*",md5(_xor($epassword, $ipad) . base64_decode($challenge))));
		$reply = base64_encode($eusername . ' ' . $hash);
		
		_write($socket,$reply);
		
		_read($socket);
			
		/*
		_write($socket,"a GETQUOTAROOT INBOX\r\n");
		$data = trim(_read($socket));
		while ($data[0] != "a") {
			$result .= $data."<br>";
			$data = trim(_read($socket));
		}
		*/
		
		

		
		_write($socket,'a STATUS INBOX (unseen)');
		while ($data[0] != "a") {
			if (preg_match('/(?P<zahl>\d+)/', $data,$unseen));
				$return = $unseen["zahl"];
			$result .= $data."<br>";
			$data = trim(_read($socket));
		}
		
		
		
		
		_write($socket,"a LOGOUT\r\n");
		_read($socket);
		return $return;
	}
	if ($auth->acl_get('f_read', 10))
	{
		$unreademails = unreademails();
		if ($unreademails > 0)
			$wsa_info .= "<a style=\"color:#FF0000\" href=http://carpe-viam.org/roundcubemail target=\"_blank\">Neue E-Mails für INFO@CARPE-VIAM.ORG: ".$unreademails."</a>";
	}
	
	$template->assign_vars(array(
		'WSA_INFO'				=> $wsa_info,
		'CONTENT'				=> $content)
	);
	
	// Output page
	page_header($name);

	$template->set_filenames(array(
		'body' => 'carpe-viam/index_body.html')
	);

	page_footer();
}
?>