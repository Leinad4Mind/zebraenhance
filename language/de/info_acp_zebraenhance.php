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
	'ACP_ZEBRA_ENHANCE_SETTINGS' => 'Einstellungen für Freundschaftsanfragen',
	'ACP_ZEBRA_ENHANCE_SETTINGS_EXPLAIN' => 'Konfiguriert globale Grenzen für Freundschaftsanfragen. Benutzer können im UCP weitere Einschränkungen festlegen.',
	'ACP_ZEBRA_ENHANCE_REQUESTS' => 'Anfragegrenzen',
	'ACP_ZE_MAX_PENDING_REQUESTS' => 'Maximale ausstehende Anfragen',
	'ACP_ZE_MAX_PENDING_REQUESTS_EXPLAIN' => 'Maximale Anzahl eingehender und ausgehender Anfragen pro Benutzer. 0 bedeutet unbegrenzt.',
	'ACP_ZE_DECLINE_COOLDOWN_DAYS' => 'Wartezeit nach Ablehnung',
	'ACP_ZE_DECLINE_COOLDOWN_DAYS_EXPLAIN' => 'Tage bis zu einer neuen Anfrage nach einer Ablehnung. Mit 0 deaktivieren.',
	'ACP_ZEBRA_ENHANCE_SAVED' => 'Zebra-Enhance-Einstellungen aktualisiert.',
	'ACP_ZEBRA_ENHANCE_REPORT' => 'Ausstehende Freundschaftsanfragen',
	'ACP_ZEBRA_ENHANCE_REPORT_EXPLAIN' => 'Schreibgeschützter, forumweiter Bericht über noch nicht angenommene, abgelehnte oder zurückgezogene Freundschaftsanfragen.',
	'ACP_ZE_REQUEST_ID' => 'Anfrage-ID',
	'ACP_ZE_REQUESTER' => 'Anfragender',
	'ACP_ZE_RECIPIENT' => 'Empfänger',
	'ACP_ZE_REQUEST_DATE' => 'Angefragt',
	'ACP_ZE_REQUEST_MESSAGE' => 'Nachricht',
	'ACP_ZE_NO_PENDING_REQUESTS' => 'Es gibt keine ausstehenden Freundschaftsanfragen.',
	'ACP_ZE_PENDING_TOTAL' => array(1 => '%d ausstehende Freundschaftsanfrage', 2 => '%d ausstehende Freundschaftsanfragen'),
	'LOG_ZEBRA_ENHANCE_SETTINGS' => '<strong>Zebra-Enhance-Einstellungen geändert</strong><br>» Maximale Anfragen: %1$d; Wartezeit: %2$d Tage',
));
