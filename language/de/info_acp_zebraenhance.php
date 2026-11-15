<?php
/**
* Zebra Enhance [German]
*
* @copyright (c) 2013-2026 Stanislav Atanasov
* @copyright (c) 2026 Leinad4Mind
* @license GNU General Public License, version 2 (GPL-2.0-only)
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
	'ACP_ZEBRA_ENHANCE_TITLE' => 'Zebra Enhance',
	'ACP_ZEBRA_ENHANCE_SETTINGS' => 'Zebra-Enhance-Einstellungen',
	'ACP_ZEBRA_ENHANCE_SETTINGS_EXPLAIN' => 'Lege forumweite Grenzen für Freundschaftsanfragen fest und wähle aus, welche erweiterten Feindfunktionen Benutzer im UCP nutzen dürfen.',
	'ACP_ZEBRA_ENHANCE_REQUESTS' => 'Anfragegrenzen',
	'ACP_ZEBRAENHANCE_MAX_PENDING_REQUESTS' => 'Maximale ausstehende Anfragen',
	'ACP_ZEBRAENHANCE_MAX_PENDING_REQUESTS_EXPLAIN' => 'Maximale Anzahl eingehender und ausgehender Anfragen pro Benutzer, getrennt je Richtung. 0 bedeutet unbegrenzt.',
	'ACP_ZEBRAENHANCE_DECLINE_COOLDOWN_DAYS' => 'Wartezeit nach Ablehnung',
	'ACP_ZEBRAENHANCE_DECLINE_COOLDOWN_DAYS_EXPLAIN' => 'Tage bis zu einer neuen Anfrage nach einer Ablehnung. Mit 0 deaktivieren.',
	'ACP_ZEBRA_ENHANCE_SAVED' => 'Zebra-Enhance-Einstellungen aktualisiert.',
	'ACP_ZEBRA_ENHANCE_REPORT' => 'Ausstehende Freundschaftsanfragen',
	'ACP_ZEBRA_ENHANCE_REPORT_EXPLAIN' => 'Schreibgeschützter, forumweiter Bericht über noch nicht angenommene, abgelehnte oder zurückgezogene Freundschaftsanfragen.',
	'ACP_ZEBRAENHANCE_REQUEST_ID' => 'Anfrage-ID',
	'ACP_ZEBRAENHANCE_REQUESTER' => 'Anfragender',
	'ACP_ZEBRAENHANCE_RECIPIENT' => 'Empfänger',
	'ACP_ZEBRAENHANCE_REQUEST_DATE' => 'Angefragt',
	'ACP_ZEBRAENHANCE_REQUEST_MESSAGE' => 'Nachricht',
	'ACP_ZEBRAENHANCE_NO_PENDING_REQUESTS' => 'Es gibt keine ausstehenden Freundschaftsanfragen.',
	'ACP_ZEBRAENHANCE_PENDING_TOTAL' => array(1 => '%d ausstehende Freundschaftsanfrage', 2 => '%d ausstehende Freundschaftsanfragen'),
	'LOG_ZEBRA_ENHANCE_SETTINGS' => '<strong>Zebra-Enhance-Einstellungen geändert</strong><br>» Maximale ausstehende Anfragen: %1$d; Wartezeit nach Ablehnung: %2$d Tage; Feind-Erweiterungen: %3$s',
	'ACP_ZEBRA_ENHANCE_FOES' => 'Feind-Erweiterungen',
	'ACP_ZEBRAENHANCE_FOES_ENHANCEMENT' => 'Feind-Erweiterungen aktivieren',
	'ACP_ZEBRAENHANCE_FOES_ENHANCEMENT_EXPLAIN' => 'Stellt Benutzern die erweiterte Feindesverwaltung und die unten ausgewählten Schutzfunktionen zur Verfügung. Wenn deaktiviert, bleibt das native Verhalten von phpBB gegenüber Feinden unverändert.',
	'ACP_ZEBRAENHANCE_FOE_PM' => 'Schutz vor privaten Nachrichten',
	'ACP_ZEBRAENHANCE_FOE_PM_EXPLAIN' => 'Erlaubt Benutzern zu wählen, wer ihnen private Nachrichten senden darf: jeder, alle außer Feinden, nur Freunde oder nur enge Freunde.',
	'ACP_ZEBRAENHANCE_FOE_CONTENT' => 'Ausblenden von Inhalten und Zitaten',
	'ACP_ZEBRAENHANCE_FOE_CONTENT_EXPLAIN' => 'Erlaubt Benutzern, Beiträge, Themen, Suchergebnisse und erkennbare Zitate ihrer Feinde auszublenden, entweder hinter einem Hinweis oder vollständig.',
	'ACP_ZEBRAENHANCE_FOE_NOTIFICATIONS' => 'Stummschalten von Benachrichtigungen',
	'ACP_ZEBRAENHANCE_FOE_NOTIFICATIONS_EXPLAIN' => 'Erlaubt Benutzern, unterstützte Benachrichtigungen ihrer Feinde zu unterdrücken.',
	'ACP_ZEBRAENHANCE_FOE_TEMPORARY' => 'Befristete Feinde',
	'ACP_ZEBRAENHANCE_FOE_TEMPORARY_EXPLAIN' => 'Erlaubt Benutzern, Feindbeziehungen nach 24 Stunden, 7 Tagen oder 30 Tagen ablaufen zu lassen.',
	'ACP_ZEBRAENHANCE_FOE_NOTES' => 'Private Notizen zu Feinden',
	'ACP_ZEBRAENHANCE_FOE_NOTES_EXPLAIN' => 'Erlaubt Benutzern, zu jedem Feind eine private Notiz zu speichern.',
	'ACP_ZEBRAENHANCE_FOE_EXCEPTIONS' => 'Ausnahmen pro Feind',
	'ACP_ZEBRAENHANCE_FOE_EXCEPTIONS_EXPLAIN' => 'Erlaubt Benutzern, ihre globalen Einstellungen für PNs, Inhalte und Benachrichtigungen für einzelne Feinde zu überschreiben.',
));
