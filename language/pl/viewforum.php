<?php
/**
*
* viewforum [Polski]
*
* @package language
* @copyright (c) 2006 - 2011 phpBB3.PL Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// INFORMACJA
//
// Wszystkie pliki językowe powinny używać kodowania UTF-8 i nie powinny zawierać znaku BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine

$lang = array_merge($lang, array(
	'ACTIVE_TOPICS'			=> 'Aktywne wątki',
	'ANNOUNCEMENTS'			=> 'Ogłoszenia',

	'FORUM_PERMISSIONS'		=> 'Twoje uprawnienia w tym dziale',

	'ICON_ANNOUNCEMENT'		=> 'Ogłoszenie',
	'ICON_STICKY'			=> 'Przyklejony',

	'LOGIN_NOTIFY_FORUM'	=> 'Zostałeś powiadomiony o tym dziale, zaloguj się, aby go przejrzeć.',

	'MARK_TOPICS_READ'		=> 'Oznacz wątki jako przeczytane',

	'NEW_POSTS_HOT'			=> 'Nowe posty [ Popularne ]', // Not used anymore
	'NEW_POSTS_LOCKED'		=> 'Nowe posty [ Zablokowane ]', // Not used anymore
	'NO_NEW_POSTS_HOT'		=> 'Brak nowych postów [ Popularne ]', // Not used anymore
	'NO_NEW_POSTS_LOCKED'	=> 'Brak nowych postów [ Zablokowane ]', // Not used anymore
	'NO_READ_ACCESS'		=> 'Nie masz uprawnień do przeglądania wątków w tym dziale.',
	'NO_UNREAD_POSTS_HOT'		=> 'Brak nieprzeczytanych postów [ Popularne ]',
	'NO_UNREAD_POSTS_LOCKED'	=> 'Brak nieprzeczytanych postów [ Zablokowane ]',

	'POST_FORUM_LOCKED'		=> 'Ten dział jest zablokowany',

	'TOPICS_MARKED'			=> 'Wątki w tym dziale oznaczono jako przeczytane.',

	'UNREAD_POSTS_HOT'		=> 'Nieprzeczytane posty [ Popularne ]',
	'UNREAD_POSTS_LOCKED'	=> 'Nieprzeczytane posty [ Zablokowane ]',

	'VIEW_FORUM'			=> 'Zobacz dział',
	'VIEW_FORUM_TOPIC'		=> 'Wątki: 1',
	'VIEW_FORUM_TOPICS'		=> 'Wątki: %d',
));

?>