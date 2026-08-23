<?php
/**
* Zebra Enhance [Swedish]
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
	'ACP_ZEBRA_ENHANCE_SETTINGS' => 'Inställningar för vänförfrågningar',
	'ACP_ZEBRA_ENHANCE_SETTINGS_EXPLAIN' => 'Ställ in globala gränser för vänförfrågningar. Användare kan välja ytterligare begränsningar i kontrollpanelen.',
	'ACP_ZEBRA_ENHANCE_REQUESTS' => 'Gränser för förfrågningar',
	'ACP_ZE_MAX_PENDING_REQUESTS' => 'Högsta antal väntande förfrågningar',
	'ACP_ZE_MAX_PENDING_REQUESTS_EXPLAIN' => 'Högsta sammanlagda antal inkommande och utgående förfrågningar per användare. 0 betyder obegränsat.',
	'ACP_ZE_DECLINE_COOLDOWN_DAYS' => 'Väntetid efter avslag',
	'ACP_ZE_DECLINE_COOLDOWN_DAYS_EXPLAIN' => 'Dagar innan en nekad person kan skicka en ny förfrågan. Ange 0 för att inaktivera.',
	'ACP_ZEBRA_ENHANCE_SAVED' => 'Inställningarna för Zebra Enhance har uppdaterats.',
	'LOG_ZEBRA_ENHANCE_SETTINGS' => '<strong>Ändrade inställningarna för Zebra Enhance</strong><br>» Högsta väntande: %1$d; väntetid: %2$d dagar',
));
