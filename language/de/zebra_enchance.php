<?php

/**
*
* Zebra Enhance [German]
*
* @package language
* @version $Id$
* @copyright (c) 2026 Leinad4Mind
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
* German translation by franki (http://dieahnen.de/ahnenforum/)
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
	'UCP_ZEBRA_PENDING_IN'					=> 'Warten auf Bestätigung',
	'UCP_ZEBRA_PENDING_IN_EXP'				=> 'Liste der Anfragen, die auf deine Bestätigung warten.',

	'UCP_ZEBRA_PENDING_OUT'					=> 'Ausstehende Bestätigung',
	'UCP_ZEBRA_PENDING_OUT_EXP'				=> 'Liste deiner Anfragen, die noch bestätigt werden müssen.',

	'UCP_ZEBRA_PENDING_NONE'				=> 'Keine anstehenden Anfragen',

	'UCP_ZEBRA_ENCHANCE_CONFIRM_CANCEL_ASK'	=> 'Bist du sicher, dass du die Freundschaftsanfrage zurückziehen möchtest?',
	'UCP_ZEBRA_ENCHANCE_CONFIRM_CANCEL'		=> 'Die Freundschaftsanfrage wurde zurückgezogen!',

	'NOTIFICATION_TYPE_ZEBRA_ADD'			=> 'Benachrichtigung bei neuen Freundschaftsanfragen',
	'NOTIFICATION_ZEBRA_ADD'				=> '%1$s hat dir eine Freundschaftsanfrage geschickt!',

	'NOTIFICATION_TYPE_ZEBRA_CONFIRM'		=> 'Bestätigung für Freundschaftsanfrage',
	'NOTIFICATION_ZEBRA_CONFIRM'			=> '%1$s hat deine Freundschaftsanfrage bestätigt!',

	'FRINEDLIST_TITLE'						=> 'Freundesliste',

	'NOT_ENEMY'								=> 'Alle außer Feinden',
	'SPECIAL_FRIENDS'						=> 'Besondere Freunde',

	'ZEBRAENHANCE_FRIENDLIST'							=> 'Freundesliste',
	'ZEBRAENHANCE_FRIENDLIST_EXPLAIN'					=> 'Wer kann deine Freundesliste sehen?',
	'ZEBRAENHANCE_BIRTHDAY_VISIBILITY'         => 'Wer darf deinen Geburtstag und dein Alter sehen?',
	'ZEBRAENHANCE_BIRTHDAY_VISIBILITY_EXPLAIN' => 'Wer keine Erlaubnis hat, sieht dein Alter weder in deinem Profil noch neben deinen Beiträgen und deinen Namen nicht in der Geburtstagsliste auf der Startseite. Administratoren und Moderatoren können beides immer sehen.',

	'FRIENDLIST_ERROR_ACCESS'				=> 'Du hast keine Berechtigung, die Freundesliste dieses Benutzers zu sehen.',

	'FRIENDLIST_TITLE'       => 'Freundesliste',
	'ZEBRAENHANCE_ACCEPT_REQUEST'      => 'Freundschaftsanfrage annehmen',
	'ZEBRAENHANCE_DECLINE_REQUEST'     => 'Freundschaftsanfrage ablehnen',
	'ZEBRAENHANCE_DECLINE_BLOCK_REQUEST' => 'Ablehnen und Absender blockieren',
	'ZEBRAENHANCE_CANCEL_REQUEST'      => 'Freundschaftsanfrage zurückziehen',
	'ZEBRAENHANCE_REMOVE_FRIEND'       => 'Freund entfernen',
	'ZEBRAENHANCE_ADD_CLOSE_FRIEND'    => 'Zu engen Freunden hinzufügen',
	'ZEBRAENHANCE_REMOVE_CLOSE_FRIEND' => 'Aus engen Freunden entfernen',
	'ZEBRAENHANCE_AJAX_NOT_AUTHORIZED' => 'Du bist nicht berechtigt, erweiterte Freundschaftsbeziehungen zu verwalten.',
	'ZEBRAENHANCE_AJAX_NOT_FRIEND'     => 'Dieser Benutzer ist nicht in deiner Freundesliste.',
	'ZEBRAENHANCE_AJAX_REQUEST_NOT_FOUND' => 'Diese Freundschaftsanfrage existiert nicht mehr oder gehört nicht zu dir.',
	'ZEBRAENHANCE_REQUESTER_CANNOT_BE_BLOCKED' => 'Administratoren und Moderatoren können nicht blockiert werden.',
	'ZEBRAENHANCE_REQUEST_ACCEPTED'       => 'Freundschaftsanfrage angenommen.',
	'ZEBRAENHANCE_FRIEND_REQUEST_CREATED' => 'Freundschaftsanfrage gesendet.',
	'ZEBRAENHANCE_REQUEST_DECLINED'       => 'Freundschaftsanfrage abgelehnt.',
	'ZEBRAENHANCE_REQUEST_DECLINED_BLOCKED' => 'Freundschaftsanfrage abgelehnt und Absender blockiert.',
	'ZEBRAENHANCE_REQUEST_CANCELLED'      => 'Freundschaftsanfrage zurückgezogen.',
	'ZEBRAENHANCE_CONFIRM_DECLINE_REQUEST' => 'Bist du sicher, dass du diese Freundschaftsanfrage ablehnen möchtest?',
	'ZEBRAENHANCE_CONFIRM_DECLINE_BLOCK_REQUEST' => 'Diese Freundschaftsanfrage ablehnen und den Absender blockieren?',
	'ZEBRAENHANCE_CONFIRM_CANCEL_REQUEST'  => 'Bist du sicher, dass du diese Freundschaftsanfrage zurückziehen möchtest?',
	'ZEBRAENHANCE_ERROR_TITLE'              => 'Fehler bei der Freundschaftsanfrage',
	'ZEBRAENHANCE_REQUEST_FAILED'           => 'Die Anfrage konnte nicht abgeschlossen werden.',
	'ZEBRAENHANCE_NO_FRIENDS'               => 'Derzeit sind keine Freunde eingetragen.',
	'ZEBRAENHANCE_VIS_EVERYONE'             => 'Jeder',
	'ZEBRAENHANCE_VIS_REGISTERED'           => 'Registrierte Benutzer',
	'ZEBRAENHANCE_VIS_NON_FOES'             => 'Alle außer Feinden',
	'ZEBRAENHANCE_VIS_FRIENDS'              => 'Freunde',
	'ZEBRAENHANCE_VIS_CLOSE_FRIENDS'        => 'Enge Freunde',
	'ZEBRAENHANCE_VIS_NOBODY'               => 'Niemand',
	'ZEBRAENHANCE_FRIEND_REQUEST_NOT_AUTHORIZED' => 'Du bist nicht berechtigt, Freundschaftsanfragen zu senden.',
	'ZEBRAENHANCE_FRIEND_REQUEST_UNCHANGED'      => 'Es wurde keine Freundschaftsanfrage geändert.',
	'ZEBRAENHANCE_REQUEST_POLICY'                    => 'Wer darf dir Freundschaftsanfragen senden?',
	'ZEBRAENHANCE_REQUEST_POLICY_EXPLAIN'            => 'Dies gilt nur für neue Anfragen. Bereits gesendete Anfragen können weiterhin angenommen werden.',
	'ZEBRAENHANCE_REQUEST_POLICY_EVERYONE'           => 'Jeder',
	'ZEBRAENHANCE_REQUEST_POLICY_FRIENDS_OF_FRIENDS' => 'Freunde von Freunden',
	'ZEBRAENHANCE_REQUEST_POLICY_NOBODY'             => 'Niemand',
	'ZEBRAENHANCE_REQUEST_MESSAGE'                   => 'Optionale Nachricht',
	'ZEBRAENHANCE_REQUEST_MESSAGE_PLACEHOLDER'       => 'Kurze persönliche Nachricht hinzufügen (optional)',

	'ZEBRAENHANCE_MUTUAL_FRIENDS'        => 'Gemeinsame Freunde',
	'ZEBRAENHANCE_FRIENDS_SHOWN'          => array(
		1 => '%2$d von %1$d Freund angezeigt',
		2 => '%2$d von %1$d Freunden angezeigt',
	),
	'ZEBRAENHANCE_SHOW_MORE_FRIENDS'      => 'Mehr anzeigen',
	'ZEBRAENHANCE_FRIENDS_LOAD_FAILED'    => 'Weitere Freunde konnten nicht geladen werden.',
	'ZEBRAENHANCE_SEARCH_FRIEND_POSTS'    => 'Beiträge deiner Freunde anzeigen',
	'ZEBRAENHANCE_PM_POLICY'                      => 'Wer darf dir private Nachrichten senden?',
	'ZEBRAENHANCE_PM_POLICY_EXPLAIN'              => 'Administratoren und Moderatoren können dir immer private Nachrichten senden. Schreibt jemand, den du nicht annehmen würdest, einer Gruppe, zu der du gehörst, wirst du ausgelassen und der Rest der Gruppe erhält die Nachricht trotzdem.',
	'ZEBRAENHANCE_HIDE_FOE_CONTENT'                  => 'Inhalte von Feinden',
	'ZEBRAENHANCE_HIDE_FOE_CONTENT_EXPLAIN'          => 'Gilt für ihre Beiträge, die von ihnen erstellten Themen und die Zitate ihrer Beiträge, die erkannt werden können. Ein Hinweis hält den Inhalt eingeklappt, du kannst ihn aber trotzdem öffnen; bei Beiträgen ist das der Hinweis von phpBB selbst. Ältere oder manuell zugeordnete Zitate werden nicht immer erkannt.',
	'ZEBRAENHANCE_FOE_CONTENT_SHOW'                  => 'Nicht ausblenden',
	'ZEBRAENHANCE_FOE_CONTENT_NOTICE'                => 'Hinter einem Hinweis ausblenden',
	'ZEBRAENHANCE_FOE_CONTENT_REMOVE'                => 'Vollständig entfernen',
	'ZEBRAENHANCE_FOE_QUOTE_HIDDEN'                  => 'Dieses Zitat stammt von %s aus deiner Feindesliste.',
	'ZEBRAENHANCE_FOE_QUOTE_HIDDEN_UNKNOWN'          => 'Dieses Zitat stammt von einem Mitglied aus deiner Feindesliste.',
	'ZEBRAENHANCE_FOE_QUOTE_SHOW'                    => 'Zitat anzeigen',
	'ZEBRAENHANCE_FOE_QUOTE_HIDE'                    => 'Zitat ausblenden',
	'ZEBRAENHANCE_FOE_TOPIC_HIDDEN'                  => 'Dieses Thema wurde von %s aus deiner Feindesliste erstellt.',
	'ZEBRAENHANCE_FOE_TOPIC_SHOW'                    => 'Thema anzeigen',
	'ZEBRAENHANCE_FOE_TOPIC_HIDE'                    => 'Thema ausblenden',
	'ZEBRAENHANCE_PM_RECIPIENTS_BLOCKED'             => 'Mindestens ein Empfänger nimmt keine privaten Nachrichten von dir an.',
	'ZEBRAENHANCE_FRIEND_SUGGESTIONS'         => 'Personen, die du vielleicht kennst',
	'ZEBRAENHANCE_FRIEND_SUGGESTIONS_EXPLAIN' => 'Freunde von Freunden, deren Privatsphäre- und Anfrageeinstellungen einen Vorschlag erlauben.',
	'ZEBRAENHANCE_SEND_FRIEND_REQUEST'        => 'Freundschaftsanfrage senden',
	'ZEBRAENHANCE_MUTUAL_FRIEND_COUNT'        => array(
		1 => '%d gemeinsamer Freund',
		2 => '%d gemeinsame Freunde',
	),
	'ZEBRAENHANCE_SEARCH_FRIENDS'             => 'Freunde suchen',
	'ZEBRAENHANCE_SELECT_REQUEST'             => 'Wähle mindestens eine Freundschaftsanfrage aus.',
	'ZEBRAENHANCE_SELECT_REQUEST_ITEM'        => 'Freundschaftsanfrage mit %s auswählen',
	'ZEBRAENHANCE_SELECT_ALL'                 => 'Alle auswählen',
	'ZEBRAENHANCE_ACCEPT_SELECTED'            => 'Ausgewählte annehmen',
	'ZEBRAENHANCE_DECLINE_SELECTED'           => 'Ausgewählte ablehnen',
	'ZEBRAENHANCE_CANCEL_SELECTED'            => 'Ausgewählte zurückziehen',
	'ZEBRAENHANCE_CONFIRM_DECLINE_SELECTED'   => 'Die ausgewählten Freundschaftsanfragen ablehnen?',
	'ZEBRAENHANCE_CONFIRM_CANCEL_SELECTED'    => 'Die ausgewählten Freundschaftsanfragen zurückziehen?',
	'ZEBRAENHANCE_BULK_REQUESTS_COMPLETED'    => array(
		1 => '%d Freundschaftsanfrage bearbeitet.',
		2 => '%d Freundschaftsanfragen bearbeitet.',
	),
	'ZEBRAENHANCE_CIRCLES'               => 'Freundeskreise',
	'ZEBRAENHANCE_CIRCLES_EXPLAIN'       => 'Erstelle private Gruppen und wähle aus, welche bestätigten Freunde zu welcher Gruppe gehören.',
	'ZEBRAENHANCE_CIRCLE_NAME'           => 'Name des Kreises',
	'ZEBRAENHANCE_CREATE_CIRCLE'         => 'Kreis erstellen',
	'ZEBRAENHANCE_RENAME_CIRCLE'         => 'Umbenennen',
	'ZEBRAENHANCE_DELETE_CIRCLE'         => 'Löschen',
	'ZEBRAENHANCE_SAVE_CIRCLES'          => 'Kreise speichern',
	'ZEBRAENHANCE_MEMBERS'               => 'Mitglieder',
	'ZEBRAENHANCE_CONFIRM_DELETE_CIRCLE' => 'Diesen Kreis löschen? Die Freundschaften bleiben bestehen.',
	'ZEBRAENHANCE_CIRCLE_CREATED'        => 'Freundeskreis erstellt.',
	'ZEBRAENHANCE_CIRCLE_RENAMED'        => 'Freundeskreis umbenannt.',
	'ZEBRAENHANCE_CIRCLE_DELETED'        => 'Freundeskreis gelöscht.',
	'ZEBRAENHANCE_CIRCLES_SAVED'         => 'Freundeskreise gespeichert.',
	'ZEBRAENHANCE_CIRCLE_INVALID'        => 'Gib einen gültigen Namen für den Kreis mit höchstens 50 Zeichen ein.',
	'ZEBRAENHANCE_CIRCLE_DUPLICATE'      => 'Du hast bereits einen Kreis mit diesem Namen.',
	'ZEBRAENHANCE_CIRCLE_LIMIT'          => 'Du kannst bis zu 20 Freundeskreise erstellen.',
	'ZEBRAENHANCE_CIRCLE_NOT_FOUND'      => 'Dieser Freundeskreis existiert nicht mehr oder gehört nicht zu dir.',

	'ZEBRAENHANCE_MUTE_FOE_NOTIFICATIONS'         => 'Benachrichtigungen von Feinden stummschalten',
	'ZEBRAENHANCE_MUTE_FOE_NOTIFICATIONS_EXPLAIN' => 'Unterdrückt Benachrichtigungen über Zitate, Erwähnungen, Antworten, Themen und Lesezeichen, die von Benutzern auf deiner Feindesliste ausgelöst werden.',
	'ZEBRAENHANCE_UCP_FOE_MANAGER'                => 'Erweiterte Feindesverwaltung',
	'ZEBRAENHANCE_UCP_FOE_MANAGER_EXPLAIN'        => 'Durchsuche deine Feinde, speichere private Notizen, lege befristete Sperren fest und überschreibe jeden globalen Schutz für einzelne Benutzer.',
	'ZEBRAENHANCE_SEARCH_FOES'                    => 'Feinde suchen',
	'ZEBRAENHANCE_ADD_FOES'                       => 'Feinde hinzufügen',
	'ZEBRAENHANCE_GLOBAL_DEFAULTS'                => 'Globale Standardwerte',
	'ZEBRAENHANCE_PROTECTION_PM'                  => 'Private Nachrichten',
	'ZEBRAENHANCE_PROTECTION_CONTENT'             => 'Inhalte',
	'ZEBRAENHANCE_PROTECTION_NOTIFICATIONS'       => 'Benachrichtigungen',
	'ZEBRAENHANCE_FOE_ADDED'                      => 'Eingetragen',
	'ZEBRAENHANCE_FOE_EXPIRES'                    => 'Läuft ab',
	'ZEBRAENHANCE_FOE_DATE_UNKNOWN'               => 'Unbekannt',
	'ZEBRAENHANCE_FOE_PERMANENT'                  => 'Dauerhaft',
	'ZEBRAENHANCE_FOE_NOTE'                       => 'Private Notiz',
	'ZEBRAENHANCE_FOE_NOTE_PLACEHOLDER'           => 'Optionale Notiz, die nur du sehen kannst',
	'ZEBRAENHANCE_FOE_DURATION'                   => 'Dauer',
	'ZEBRAENHANCE_FOE_DURATION_KEEP'              => 'Aktuellen Ablauf beibehalten',
	'ZEBRAENHANCE_FOE_24_HOURS'                   => '24 Stunden',
	'ZEBRAENHANCE_FOE_7_DAYS'                     => '7 Tage',
	'ZEBRAENHANCE_FOE_30_DAYS'                    => '30 Tage',
	'ZEBRAENHANCE_POLICY_INHERIT'                 => 'Globalen Standard verwenden',
	'ZEBRAENHANCE_POLICY_ALLOW'                   => 'Erlauben',
	'ZEBRAENHANCE_POLICY_BLOCK'                   => 'Blockieren',
	'ZEBRAENHANCE_SELECT_FOE'                     => 'Wähle mindestens einen Feind aus.',
	'ZEBRAENHANCE_SELECT_FOE_ITEM'                => 'Feind %s auswählen',
	'ZEBRAENHANCE_REMOVE_SELECTED_FOES'           => 'Ausgewählte entfernen',
	'ZEBRAENHANCE_CONFIRM_REMOVE_FOES'            => 'Die ausgewählten Benutzer von deiner Feindesliste entfernen?',
	'ZEBRAENHANCE_NO_FOES'                        => 'Derzeit sind keine Feinde eingetragen.',
	'ZEBRAENHANCE_NO_MATCHING_FOES'               => 'Keine Feinde entsprechen deiner Suche.',
	'ZEBRAENHANCE_FOE_SAVED'                      => 'Feindeinstellungen gespeichert.',
	'ZEBRAENHANCE_FOE_NOT_FOUND'                  => 'Dieser Benutzer ist nicht mehr auf deiner Feindesliste.',
	'ZEBRAENHANCE_FOE_ERROR_TITLE'                => 'Fehler bei der Feindesverwaltung',
	'ZEBRAENHANCE_FOES_REMOVED'                   => array(
		1 => '%d Feind entfernt.',
		2 => '%d Feinde entfernt.',
	),
));
