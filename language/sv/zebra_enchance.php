<?php

/**
*
* Zebra Enhance [Swedish]
*
* @package language
* @version $Id$
* @copyright (c) 2026 Leinad4Mind
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
* Swedish translation by Holger (http://www.maskinisten.net)
*
*/

if (!defined('IN_PHPBB'))
{
		exit;
}
if (empty($lang) || !is_array($lang))
{
		$lang = array();
}

$lang = array_merge($lang, array(
	'UCP_ZEBRA_PENDING_IN'	=>	'Väntar på bekräftelse',
	'UCP_ZEBRA_PENDING_IN_EXP'	=>	'En lista med förfrågningar som väntar på ditt godkännande.',

	'UCP_ZEBRA_PENDING_OUT'	=>	'Dina väntande förfrågningar',
	'UCP_ZEBRA_PENDING_OUT_EXP'	=>	'En lista med dina förfrågningar som väntar på godkännande.',

	'UCP_ZEBRA_PENDING_NONE'	=>	'Inga väntande förfrågningar',

	'UCP_ZEBRA_ENCHANCE_CONFIRM_CANCEL_ASK'	=>	'Är du säker på att du vill avbryta vänförfrågan?',
	'UCP_ZEBRA_ENCHANCE_CONFIRM_CANCEL'	=> 'Vänförfrågan avbröts!',

	'NOTIFICATION_TYPE_ZEBRA_ADD'	=>	'Ny vänförfrågan',
	'NOTIFICATION_ZEBRA_ADD'	=>	'%1$s skickade en vänförfrågan till dig!',

	'NOTIFICATION_TYPE_ZEBRA_CONFIRM'	=>	'Bekräftad vänförfrågan',
	'NOTIFICATION_ZEBRA_CONFIRM'	=>	'%1$s bekräftade din vänförfrågan!',

	'FRINEDLIST_TITLE'	=>	'Vänlista',

	'NOT_ENEMY'	=>	'Alla utom ignorerade',
	'SPECIAL_FRIENDS'	=>	'Speciella vänner',

	'ZEBRAENHANCE_FRIENDLIST'	=>	'Vänlista',
	'ZEBRAENHANCE_FRIENDLIST_EXPLAIN'	=>	'Vem kan se din vänlista?',
	'ZEBRAENHANCE_BIRTHDAY_VISIBILITY'         => 'Vem får se din födelsedag och ålder?',
	'ZEBRAENHANCE_BIRTHDAY_VISIBILITY_EXPLAIN' => 'Den som inte har tillåtelse ser inte din ålder i din profil eller bredvid dina inlägg, och inte ditt namn i födelsedagslistan på forumets index. Administratörer och moderatorer kan alltid se dem.',

	'FRIENDLIST_ERROR_ACCESS'	=>	'Du har inte behörighet att se den här användarens vänlista.',

	'FRIENDLIST_TITLE'       => 'Vänlista',
	'ZEBRAENHANCE_ACCEPT_REQUEST'      => 'Acceptera vänförfrågan',
	'ZEBRAENHANCE_DECLINE_REQUEST'     => 'Avböj vänförfrågan',
	'ZEBRAENHANCE_DECLINE_BLOCK_REQUEST' => 'Avböj och blockera avsändaren',
	'ZEBRAENHANCE_CANCEL_REQUEST'      => 'Avbryt vänförfrågan',
	'ZEBRAENHANCE_REMOVE_FRIEND'       => 'Ta bort vän',
	'ZEBRAENHANCE_ADD_CLOSE_FRIEND'    => 'Lägg till bland nära vänner',
	'ZEBRAENHANCE_REMOVE_CLOSE_FRIEND' => 'Ta bort från nära vänner',
	'ZEBRAENHANCE_AJAX_NOT_AUTHORIZED' => 'Du har inte behörighet att hantera utökade vänrelationer.',
	'ZEBRAENHANCE_AJAX_NOT_FRIEND'     => 'Den användaren finns inte i din vänlista.',
	'ZEBRAENHANCE_AJAX_REQUEST_NOT_FOUND' => 'Den vänförfrågan finns inte längre eller tillhör inte dig.',
	'ZEBRAENHANCE_REQUESTER_CANNOT_BE_BLOCKED' => 'Administratörer och moderatorer kan inte blockeras.',
	'ZEBRAENHANCE_REQUEST_ACCEPTED'       => 'Vänförfrågan accepterad.',
	'ZEBRAENHANCE_FRIEND_REQUEST_CREATED' => 'Vänförfrågan skickad.',
	'ZEBRAENHANCE_REQUEST_DECLINED'       => 'Vänförfrågan avböjd.',
	'ZEBRAENHANCE_REQUEST_DECLINED_BLOCKED' => 'Vänförfrågan avböjd och avsändaren blockerad.',
	'ZEBRAENHANCE_REQUEST_CANCELLED'      => 'Vänförfrågan avbruten.',
	'ZEBRAENHANCE_CONFIRM_DECLINE_REQUEST' => 'Är du säker på att du vill avböja den här vänförfrågan?',
	'ZEBRAENHANCE_CONFIRM_DECLINE_BLOCK_REQUEST' => 'Avböja den här vänförfrågan och blockera avsändaren?',
	'ZEBRAENHANCE_CONFIRM_CANCEL_REQUEST'  => 'Är du säker på att du vill avbryta den här vänförfrågan?',
	'ZEBRAENHANCE_ERROR_TITLE'              => 'Fel med vänförfrågan',
	'ZEBRAENHANCE_REQUEST_FAILED'           => 'Förfrågan kunde inte slutföras.',
	'ZEBRAENHANCE_NO_FRIENDS'               => 'Du har inga vänner tillagda just nu.',
	'ZEBRAENHANCE_VIS_EVERYONE'             => 'Alla',
	'ZEBRAENHANCE_VIS_REGISTERED'           => 'Registrerade användare',
	'ZEBRAENHANCE_VIS_NON_FOES'             => 'Alla utom ignorerade',
	'ZEBRAENHANCE_VIS_FRIENDS'              => 'Vänner',
	'ZEBRAENHANCE_VIS_CLOSE_FRIENDS'        => 'Nära vänner',
	'ZEBRAENHANCE_VIS_NOBODY'               => 'Ingen',
	'ZEBRAENHANCE_FRIEND_REQUEST_NOT_AUTHORIZED' => 'Du har inte behörighet att skicka vänförfrågningar.',
	'ZEBRAENHANCE_FRIEND_REQUEST_UNCHANGED'      => 'Ingen vänförfrågan ändrades.',
	'ZEBRAENHANCE_REQUEST_POLICY'                    => 'Vem får skicka vänförfrågningar till dig?',
	'ZEBRAENHANCE_REQUEST_POLICY_EXPLAIN'            => 'Detta gäller bara nya förfrågningar. Förfrågningar som du redan har skickat kan fortfarande accepteras.',
	'ZEBRAENHANCE_REQUEST_POLICY_EVERYONE'           => 'Alla',
	'ZEBRAENHANCE_REQUEST_POLICY_FRIENDS_OF_FRIENDS' => 'Vänners vänner',
	'ZEBRAENHANCE_REQUEST_POLICY_NOBODY'             => 'Ingen',
	'ZEBRAENHANCE_REQUEST_MESSAGE'                   => 'Valfritt meddelande',
	'ZEBRAENHANCE_REQUEST_MESSAGE_PLACEHOLDER'       => 'Lägg till ett kort personligt meddelande (valfritt)',

	'ZEBRAENHANCE_MUTUAL_FRIENDS'        => 'Gemensamma vänner',
	'ZEBRAENHANCE_FRIENDS_SHOWN'          => array(
		1 => 'Visar %2$d av %1$d vän',
		2 => 'Visar %2$d av %1$d vänner',
	),
	'ZEBRAENHANCE_SHOW_MORE_FRIENDS'      => 'Visa fler',
	'ZEBRAENHANCE_FRIENDS_LOAD_FAILED'    => 'Det gick inte att läsa in fler vänner.',
	'ZEBRAENHANCE_SEARCH_FRIEND_POSTS'    => 'Visa dina vänners inlägg',
	'ZEBRAENHANCE_FRIEND_MARK'            => 'Vän',
	'ZEBRAENHANCE_CLOSE_FRIEND_MARK'      => 'Nära vän',
	'ZEBRAENHANCE_HIGHLIGHT_FRIENDS'      => 'Markera innehåll från vänner',
	'ZEBRAENHANCE_HIGHLIGHT_FRIENDS_EXPLAIN' => 'Markerar inlägg, ämnen och privata meddelanden från dina vänner, med en egen färg för nära vänner. Bara du ser markeringen.',
	'ZEBRAENHANCE_PM_POLICY'                      => 'Vem får skicka privata meddelanden till dig?',
	'ZEBRAENHANCE_PM_POLICY_EXPLAIN'              => 'Administratörer och moderatorer kan alltid skicka privata meddelanden till dig. När någon du inte skulle ta emot meddelanden från skriver till en grupp du tillhör hoppas du över, och resten av gruppen får meddelandet ändå.',
	'ZEBRAENHANCE_HIDE_FOE_CONTENT'                  => 'Innehåll från ignorerade användare',
	'ZEBRAENHANCE_HIDE_FOE_CONTENT_EXPLAIN'          => 'Gäller deras inlägg, de ämnen de har startat och de citat av deras inlägg som kan identifieras. Ett meddelande håller innehållet hopfällt, men du kan fortfarande öppna det; för inlägg är det phpBB:s egen platshållare. Äldre eller manuellt tillskrivna citat kan inte alltid identifieras.',
	'ZEBRAENHANCE_FOE_CONTENT_SHOW'                  => 'Dölj inte',
	'ZEBRAENHANCE_FOE_CONTENT_NOTICE'                => 'Dölj bakom ett meddelande',
	'ZEBRAENHANCE_FOE_CONTENT_REMOVE'                => 'Ta bort helt',
	'ZEBRAENHANCE_FOE_QUOTE_HIDDEN'                  => 'Det här citatet är från %s, som du ignorerar.',
	'ZEBRAENHANCE_FOE_QUOTE_HIDDEN_UNKNOWN'          => 'Det här citatet är från en användare som du ignorerar.',
	'ZEBRAENHANCE_FOE_QUOTE_SHOW'                    => 'Visa citat',
	'ZEBRAENHANCE_FOE_QUOTE_HIDE'                    => 'Dölj citat',
	'ZEBRAENHANCE_FOE_TOPIC_HIDDEN'                  => 'Det här ämnet startades av %s, som du ignorerar.',
	'ZEBRAENHANCE_FOE_TOPIC_SHOW'                    => 'Visa ämne',
	'ZEBRAENHANCE_FOE_TOPIC_HIDE'                    => 'Dölj ämne',
	'ZEBRAENHANCE_PM_RECIPIENTS_BLOCKED'             => 'En eller flera mottagare tar inte emot privata meddelanden från dig.',
	'ZEBRAENHANCE_FRIEND_SUGGESTIONS'         => 'Personer du kanske känner',
	'ZEBRAENHANCE_FRIEND_SUGGESTIONS_EXPLAIN' => 'Vänners vänner vars integritets- och förfrågningsinställningar tillåter ett förslag.',
	'ZEBRAENHANCE_SEND_FRIEND_REQUEST'        => 'Skicka vänförfrågan',
	'ZEBRAENHANCE_MUTUAL_FRIEND_COUNT'        => array(
		1 => '%d gemensam vän',
		2 => '%d gemensamma vänner',
	),
	'ZEBRAENHANCE_SEARCH_FRIENDS'             => 'Sök vänner',
	'ZEBRAENHANCE_SELECT_REQUEST'             => 'Välj minst en vänförfrågan.',
	'ZEBRAENHANCE_SELECT_REQUEST_ITEM'        => 'Välj vänförfrågan med %s',
	'ZEBRAENHANCE_SELECT_ALL'                 => 'Markera alla',
	'ZEBRAENHANCE_ACCEPT_SELECTED'            => 'Acceptera markerade',
	'ZEBRAENHANCE_DECLINE_SELECTED'           => 'Avböj markerade',
	'ZEBRAENHANCE_CANCEL_SELECTED'            => 'Avbryt markerade',
	'ZEBRAENHANCE_CONFIRM_DECLINE_SELECTED'   => 'Avböja de markerade vänförfrågningarna?',
	'ZEBRAENHANCE_CONFIRM_CANCEL_SELECTED'    => 'Avbryta de markerade vänförfrågningarna?',
	'ZEBRAENHANCE_BULK_REQUESTS_COMPLETED'    => array(
		1 => '%d vänförfrågan behandlad.',
		2 => '%d vänförfrågningar behandlade.',
	),
	'ZEBRAENHANCE_CIRCLES'               => 'Vänkretsar',
	'ZEBRAENHANCE_CIRCLES_EXPLAIN'       => 'Skapa privata grupper och välj vilka accepterade vänner som hör till var och en.',
	'ZEBRAENHANCE_CIRCLE_NAME'           => 'Kretsens namn',
	'ZEBRAENHANCE_CREATE_CIRCLE'         => 'Skapa krets',
	'ZEBRAENHANCE_RENAME_CIRCLE'         => 'Byt namn',
	'ZEBRAENHANCE_DELETE_CIRCLE'         => 'Ta bort',
	'ZEBRAENHANCE_SAVE_CIRCLES'          => 'Spara kretsar',
	'ZEBRAENHANCE_MEMBERS'               => 'medlemmar',
	'ZEBRAENHANCE_CONFIRM_DELETE_CIRCLE' => 'Ta bort den här kretsen? Vänskaperna tas inte bort.',
	'ZEBRAENHANCE_CIRCLE_CREATED'        => 'Vänkretsen har skapats.',
	'ZEBRAENHANCE_CIRCLE_RENAMED'        => 'Vänkretsen har bytt namn.',
	'ZEBRAENHANCE_CIRCLE_DELETED'        => 'Vänkretsen har tagits bort.',
	'ZEBRAENHANCE_CIRCLES_SAVED'         => 'Vänkretsarna har sparats.',
	'ZEBRAENHANCE_CIRCLE_INVALID'        => 'Ange ett giltigt namn på kretsen med högst 50 tecken.',
	'ZEBRAENHANCE_CIRCLE_DUPLICATE'      => 'Du har redan en krets med det namnet.',
	'ZEBRAENHANCE_CIRCLE_LIMIT'          => 'Du kan skapa upp till 20 vänkretsar.',
	'ZEBRAENHANCE_CIRCLE_NOT_FOUND'      => 'Den vänkretsen finns inte längre eller tillhör inte dig.',

	'ZEBRAENHANCE_MUTE_FOE_NOTIFICATIONS'         => 'Tysta aviseringar från ignorerade användare',
	'ZEBRAENHANCE_MUTE_FOE_NOTIFICATIONS_EXPLAIN' => 'Stänger av aviseringar om citat, omnämnanden, svar, ämnen och bokmärken som skapas av användare som du ignorerar.',
	'ZEBRAENHANCE_UCP_FOE_MANAGER'                => 'Utökad hantering av ignorerade',
	'ZEBRAENHANCE_UCP_FOE_MANAGER_EXPLAIN'        => 'Sök bland dem du ignorerar, spara privata anteckningar, ställ in tillfälliga blockeringar och åsidosätt varje globalt skydd för enskilda användare.',
	'ZEBRAENHANCE_SEARCH_FOES'                    => 'Sök ignorerade',
	'ZEBRAENHANCE_ADD_FOES'                       => 'Lägg till ignorerade',
	'ZEBRAENHANCE_GLOBAL_DEFAULTS'                => 'Globala standardval',
	'ZEBRAENHANCE_PROTECTION_PM'                  => 'Privata meddelanden',
	'ZEBRAENHANCE_PROTECTION_CONTENT'             => 'Innehåll',
	'ZEBRAENHANCE_PROTECTION_NOTIFICATIONS'       => 'Aviseringar',
	'ZEBRAENHANCE_FOE_ADDED'                      => 'Tillagd',
	'ZEBRAENHANCE_FOE_EXPIRES'                    => 'Upphör',
	'ZEBRAENHANCE_FOE_DATE_UNKNOWN'               => 'Okänt',
	'ZEBRAENHANCE_FOE_PERMANENT'                  => 'Tills vidare',
	'ZEBRAENHANCE_FOE_NOTE'                       => 'Privat anteckning',
	'ZEBRAENHANCE_FOE_NOTE_PLACEHOLDER'           => 'Valfri anteckning som bara du kan se',
	'ZEBRAENHANCE_FOE_DURATION'                   => 'Varaktighet',
	'ZEBRAENHANCE_FOE_DURATION_KEEP'              => 'Behåll nuvarande utgångstid',
	'ZEBRAENHANCE_FOE_24_HOURS'                   => '24 timmar',
	'ZEBRAENHANCE_FOE_7_DAYS'                     => '7 dagar',
	'ZEBRAENHANCE_FOE_30_DAYS'                    => '30 dagar',
	'ZEBRAENHANCE_POLICY_INHERIT'                 => 'Använd globalt standardval',
	'ZEBRAENHANCE_POLICY_ALLOW'                   => 'Tillåt',
	'ZEBRAENHANCE_POLICY_BLOCK'                   => 'Blockera',
	'ZEBRAENHANCE_SELECT_FOE'                     => 'Välj minst en ignorerad användare.',
	'ZEBRAENHANCE_SELECT_FOE_ITEM'                => 'Välj %s',
	'ZEBRAENHANCE_REMOVE_SELECTED_FOES'           => 'Ta bort markerade',
	'ZEBRAENHANCE_CONFIRM_REMOVE_FOES'            => 'Sluta ignorera de markerade användarna?',
	'ZEBRAENHANCE_NO_FOES'                        => 'Du ignorerar inga användare just nu.',
	'ZEBRAENHANCE_NO_MATCHING_FOES'               => 'Inga ignorerade användare matchar din sökning.',
	'ZEBRAENHANCE_FOE_SAVED'                      => 'Inställningarna för den ignorerade användaren har sparats.',
	'ZEBRAENHANCE_FOE_NOT_FOUND'                  => 'Du ignorerar inte längre den användaren.',
	'ZEBRAENHANCE_FOE_ERROR_TITLE'                => 'Fel vid hantering av ignorerade',
	'ZEBRAENHANCE_FOES_REMOVED'                   => array(
		1 => '%d ignorerad användare borttagen.',
		2 => '%d ignorerade användare borttagna.',
	),
));
