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
	'ACP_ZEBRA_ENHANCE_SETTINGS' => 'Instellingen van Zebra Enhance',
	'ACP_ZEBRA_ENHANCE_SETTINGS_EXPLAIN' => 'Stel forumbrede limieten voor vriendschapsverzoeken en het zoeken naar berichten van vrienden in, en kies welke uitgebreide vijandfuncties gebruikers in het UCP mogen gebruiken.',
	'ACP_ZEBRA_ENHANCE_REQUESTS' => 'Verzoeklimieten',
	'ACP_ZEBRAENHANCE_MAX_PENDING_REQUESTS' => 'Maximum openstaande verzoeken',
	'ACP_ZEBRAENHANCE_MAX_PENDING_REQUESTS_EXPLAIN' => 'Maximum aantal ontvangen en verzonden verzoeken per gebruiker, afzonderlijk toegepast per richting. Gebruik 0 voor onbeperkt.',
	'ACP_ZEBRAENHANCE_DECLINE_COOLDOWN_DAYS' => 'Wachttijd na afwijzing',
	'ACP_ZEBRAENHANCE_DECLINE_COOLDOWN_DAYS_EXPLAIN' => 'Dagen voordat een afgewezen persoon opnieuw een verzoek kan sturen. Gebruik 0 om uit te schakelen.',
	'ACP_ZEBRA_ENHANCE_FRIENDS' => 'Vrienden',
	'ACP_ZEBRAENHANCE_FRIEND_POSTS' => 'Zoeken naar berichten van vrienden',
	'ACP_ZEBRAENHANCE_FRIEND_POSTS_EXPLAIN' => 'Voegt „Berichten van je vrienden bekijken” toe aan het menu met snelkoppelingen, en naast elke vriend in Gebruikerspaneel > Vrienden een link die de berichten van die vriend zoekt.',
	'ACP_ZEBRAENHANCE_FRIEND_HIGHLIGHT' => 'Inhoud van vrienden markeren',
	'ACP_ZEBRAENHANCE_FRIEND_HIGHLIGHT_EXPLAIN' => 'Laat gebruikers berichten, onderwerpen, zoekresultaten en privéberichten van hun vrienden markeren, met een aparte kleur voor goede vrienden. Elke gebruiker kan dit uitschakelen in Gebruikerspaneel > Vrienden.',
	'ACP_ZEBRAENHANCE_FRIEND_AVATARS' => 'Avatars van vrienden tonen',
	'ACP_ZEBRAENHANCE_FRIEND_AVATARS_EXPLAIN' => 'Toont de avatar van elke vriend in Gebruikerspaneel > Vrienden.',
	'ACP_ZEBRA_ENHANCE_SAVED' => 'Instellingen van Zebra Enhance bijgewerkt.',
	'ACP_ZEBRA_ENHANCE_REPORT' => 'Openstaande vriendschapsverzoeken',
	'ACP_ZEBRA_ENHANCE_REPORT_EXPLAIN' => 'Forum-breed alleen-lezenoverzicht van verzoeken die nog niet zijn geaccepteerd, geweigerd of geannuleerd.',
	'ACP_ZEBRAENHANCE_REQUEST_ID' => 'Verzoek-ID',
	'ACP_ZEBRAENHANCE_REQUESTER' => 'Aanvrager',
	'ACP_ZEBRAENHANCE_RECIPIENT' => 'Ontvanger',
	'ACP_ZEBRAENHANCE_REQUEST_DATE' => 'Aangevraagd',
	'ACP_ZEBRAENHANCE_REQUEST_MESSAGE' => 'Bericht',
	'ACP_ZEBRAENHANCE_NO_PENDING_REQUESTS' => 'Er zijn geen openstaande vriendschapsverzoeken.',
	'ACP_ZEBRAENHANCE_PENDING_TOTAL' => array(1 => '%d openstaand vriendschapsverzoek', 2 => '%d openstaande vriendschapsverzoeken'),
	'LOG_ZEBRA_ENHANCE_SETTINGS' => '<strong>Instellingen van Zebra Enhance gewijzigd</strong><br>» Maximum openstaande verzoeken: %1$d; wachttijd na weigering: %2$d dagen; vijandverbeteringen: %3$s',
	'ACP_ZEBRA_ENHANCE_FOES' => 'Vijandverbeteringen',
	'ACP_ZEBRAENHANCE_FOES_ENHANCEMENT' => 'Vijandverbeteringen inschakelen',
	'ACP_ZEBRAENHANCE_FOES_ENHANCEMENT_EXPLAIN' => 'Stelt het uitgebreide vijandenbeheer en de hieronder geselecteerde beschermingen beschikbaar voor gebruikers. Als dit is uitgeschakeld, blijft het standaardgedrag van phpBB voor vijanden ongewijzigd.',
	'ACP_ZEBRAENHANCE_FOE_PM' => 'Bescherming van privéberichten',
	'ACP_ZEBRAENHANCE_FOE_PM_EXPLAIN' => 'Hiermee kunnen gebruikers kiezen wie hun privéberichten mag sturen: iedereen, iedereen behalve vijanden, alleen vrienden of alleen goede vrienden.',
	'ACP_ZEBRAENHANCE_FOE_CONTENT' => 'Inhoud en citaten verbergen',
	'ACP_ZEBRAENHANCE_FOE_CONTENT_EXPLAIN' => 'Hiermee kunnen gebruikers berichten, onderwerpen, zoekresultaten en herkenbare citaten van hun vijanden verbergen, achter een mededeling of volledig.',
	'ACP_ZEBRAENHANCE_FOE_NOTIFICATIONS' => 'Meldingen dempen',
	'ACP_ZEBRAENHANCE_FOE_NOTIFICATIONS_EXPLAIN' => 'Hiermee kunnen gebruikers ondersteunde meldingen van hun vijanden onderdrukken.',
	'ACP_ZEBRAENHANCE_FOE_TEMPORARY' => 'Tijdelijke vijanden',
	'ACP_ZEBRAENHANCE_FOE_TEMPORARY_EXPLAIN' => 'Hiermee kunnen gebruikers iemand voor 24 uur, 7 dagen of 30 dagen als vijand instellen.',
	'ACP_ZEBRAENHANCE_FOE_NOTES' => 'Privénotities over vijanden',
	'ACP_ZEBRAENHANCE_FOE_NOTES_EXPLAIN' => 'Hiermee kunnen gebruikers voor elke vijand een privénotitie bewaren.',
	'ACP_ZEBRAENHANCE_FOE_EXCEPTIONS' => 'Uitzonderingen per vijand',
	'ACP_ZEBRAENHANCE_FOE_EXCEPTIONS_EXPLAIN' => 'Hiermee kunnen gebruikers per vijand afwijken van hun algemene keuzes voor privéberichten, inhoud en meldingen.',
));
