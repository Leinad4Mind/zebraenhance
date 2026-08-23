<?php
/**
* Zebra Enhance [Dutch]
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
	'ACP_ZEBRA_ENHANCE_SETTINGS' => 'Instellingen voor vriendschapsverzoeken',
	'ACP_ZEBRA_ENHANCE_SETTINGS_EXPLAIN' => 'Stel algemene limieten voor vriendschapsverzoeken in. Gebruikers kunnen in het UCP extra beperkingen instellen.',
	'ACP_ZEBRA_ENHANCE_REQUESTS' => 'Verzoeklimieten',
	'ACP_ZE_MAX_PENDING_REQUESTS' => 'Maximum openstaande verzoeken',
	'ACP_ZE_MAX_PENDING_REQUESTS_EXPLAIN' => 'Maximum ontvangen en verzonden verzoeken samen per gebruiker. Gebruik 0 voor onbeperkt.',
	'ACP_ZE_DECLINE_COOLDOWN_DAYS' => 'Wachttijd na afwijzing',
	'ACP_ZE_DECLINE_COOLDOWN_DAYS_EXPLAIN' => 'Dagen voordat een afgewezen persoon opnieuw een verzoek kan sturen. Gebruik 0 om uit te schakelen.',
	'ACP_ZEBRA_ENHANCE_SAVED' => 'Instellingen van Zebra Enhance bijgewerkt.',
	'LOG_ZEBRA_ENHANCE_SETTINGS' => '<strong>Instellingen van Zebra Enhance gewijzigd</strong><br>» Maximum openstaand: %1$d; wachttijd: %2$d dagen',
));
