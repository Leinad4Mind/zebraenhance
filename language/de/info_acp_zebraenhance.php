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
	'LOG_ZEBRA_ENHANCE_SETTINGS' => '<strong>Zebra-Enhance-Einstellungen geändert</strong><br>» Maximale Anfragen: %1$d; Wartezeit: %2$d Tage',
));
