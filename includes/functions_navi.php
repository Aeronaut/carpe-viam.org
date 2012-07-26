<?php
$user->setup();
$navia = array();
if ($_GET['my_lang'])
{
	$user->lang_name = $_GET['my_lang'];
}

$sql = "SELECT `id`,`button`,`name`,`title`,`lang`,`p` FROM DB23680.`HTML` WHERE lang = 'en' OR lang = 'de' OR lang = '".$user->lang_name."' ORDER BY `id` ASC";
$result = $db->sql_query($sql);
while ($row = $db->sql_fetchrow($result)) {
	if ($row['p'] == 1) {
		switch ($row['button']) {
			case "cv_forum":
				if ($row['lang'] == $user->lang_name) {
					$navia[$row['id']] = '<a title="'.$row['title'].'" class="cv_forum" href='.append_sid("{$phpbb_root_path}index.$phpEx",'section=forum&my_lang='.$user->lang_name).'><span>'.$row['name'].'</span></a>';
					$navid[$row['id']] = 3;
				}
				elseif (($row['lang'] == "en") && $navid[$row['id']] < 3) {
					$navia[$row['id']] = '<a title="'.$row['title'].'" class="cv_forum" href='.append_sid("{$phpbb_root_path}index.$phpEx",'section=forum&my_lang='.$user->lang_name).'><span>'.$row['name'].'</span></a>';
					$navid[$row['id']] = 2;
				}
				elseif ($navid[$row['id']] < 2) {
					$navia[$row['id']] = '<a title="'.$row['title'].'" class="cv_forum" href='.append_sid("{$phpbb_root_path}index.$phpEx",'section=forum&my_lang='.$user->lang_name).'><span>'.$row['name'].'</span></a>';
					$navid[$row['id']] = 1;
				}
				break;
			default:
				if ($row['lang'] == $user->lang_name) {
					$navia[$row['id']] = '<a title="'.$row['title'].'" class="'.$row['button'].'" href='.$phpbb_root_path.'index.'.$phpEx.'?section='.$row['id'].'&my_lang='.$user->lang_name.'><span>'.$row['name'].'</span></a>';
					$navid[$row['id']] = 1;
				}
				elseif (($row['lang'] == "en") && $navid[$row['id']] == null) {
					$navia[$row['id']] = '<a title="'.$row['title'].'" class="'.$row['button'].'" href='.$phpbb_root_path.'index.'.$phpEx.'?section='.$row['id'].'&my_lang='.$user->lang_name.'><span>'.$row['name'].'</span></a>';
					$navid[$row['id']] = 1;
				}	
				elseif ($navid[$row['id']] == null) {
					$navia[$row['id']] = '<a title="'.$row['title'].'" class="'.$row['button'].'" href='.$phpbb_root_path.'index.'.$phpEx.'?section='.$row['id'].'&my_lang='.$user->lang_name.'><span>'.$row['name'].'</span></a>';
					$navid[$row['id']] = null;
				}
				break;
		}
	}
}
$navin = '<div id="wrapleft" style="position: fixed;">';
foreach ($navia as $k => $v) {
    $navin .= $v;
}
$u_wsa = $auth->acl_get('m_',"6");

$navin .= '</div>';
	$template->assign_vars(array(
		'U_WSA'					=> $u_wsa,
		'NAVI_MENU'				=> $navin)
	);
?>