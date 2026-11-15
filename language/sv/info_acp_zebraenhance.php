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
	'ACP_ZEBRA_ENHANCE_SETTINGS' => 'Inställningar för Zebra Enhance',
	'ACP_ZEBRA_ENHANCE_SETTINGS_EXPLAIN' => 'Ställ in gränser för vänförfrågningar för hela forumet och välj vilka utökade funktioner för ignorerade användare som användarna får använda i kontrollpanelen.',
	'ACP_ZEBRA_ENHANCE_REQUESTS' => 'Gränser för förfrågningar',
	'ACP_ZEBRAENHANCE_MAX_PENDING_REQUESTS' => 'Högsta antal väntande förfrågningar',
	'ACP_ZEBRAENHANCE_MAX_PENDING_REQUESTS_EXPLAIN' => 'Högsta antal inkommande och utgående förfrågningar per användare, tillämpat separat per riktning. 0 betyder obegränsat.',
	'ACP_ZEBRAENHANCE_DECLINE_COOLDOWN_DAYS' => 'Väntetid efter avslag',
	'ACP_ZEBRAENHANCE_DECLINE_COOLDOWN_DAYS_EXPLAIN' => 'Dagar innan en nekad person kan skicka en ny förfrågan. Ange 0 för att inaktivera.',
	'ACP_ZEBRA_ENHANCE_SAVED' => 'Inställningarna för Zebra Enhance har uppdaterats.',
	'ACP_ZEBRA_ENHANCE_REPORT' => 'Väntande vänförfrågningar',
	'ACP_ZEBRA_ENHANCE_REPORT_EXPLAIN' => 'Skrivskyddad rapport över vänförfrågningar som ännu inte har godkänts, avvisats eller avbrutits.',
	'ACP_ZEBRAENHANCE_REQUEST_ID' => 'Förfrågnings-ID',
	'ACP_ZEBRAENHANCE_REQUESTER' => 'Avsändare',
	'ACP_ZEBRAENHANCE_RECIPIENT' => 'Mottagare',
	'ACP_ZEBRAENHANCE_REQUEST_DATE' => 'Begärd',
	'ACP_ZEBRAENHANCE_REQUEST_MESSAGE' => 'Meddelande',
	'ACP_ZEBRAENHANCE_NO_PENDING_REQUESTS' => 'Det finns inga väntande vänförfrågningar.',
	'ACP_ZEBRAENHANCE_PENDING_TOTAL' => array(1 => '%d väntande vänförfrågan', 2 => '%d väntande vänförfrågningar'),
	'LOG_ZEBRA_ENHANCE_SETTINGS' => '<strong>Ändrade inställningarna för Zebra Enhance</strong><br>» Högsta antal väntande förfrågningar: %1$d; väntetid efter avböjande: %2$d dagar; förbättringar för ignorerade: %3$s',
	'ACP_ZEBRA_ENHANCE_FOES' => 'Förbättringar för ignorerade',
	'ACP_ZEBRAENHANCE_FOES_ENHANCEMENT' => 'Aktivera förbättringar för ignorerade',
	'ACP_ZEBRAENHANCE_FOES_ENHANCEMENT_EXPLAIN' => 'Gör den utökade hanteringen av ignorerade och de skydd som valts nedan tillgängliga för användarna. När det är inaktiverat förblir phpBB:s inbyggda hantering av ignorerade oförändrad.',
	'ACP_ZEBRAENHANCE_FOE_PM' => 'Skydd för privata meddelanden',
	'ACP_ZEBRAENHANCE_FOE_PM_EXPLAIN' => 'Låter användare välja vem som får skicka privata meddelanden till dem: alla, alla utom ignorerade, endast vänner eller endast nära vänner.',
	'ACP_ZEBRAENHANCE_FOE_CONTENT' => 'Dölja innehåll och citat',
	'ACP_ZEBRAENHANCE_FOE_CONTENT_EXPLAIN' => 'Låter användare dölja inlägg, ämnen, sökresultat och identifierbara citat från användare de ignorerar, antingen bakom ett meddelande eller helt.',
	'ACP_ZEBRAENHANCE_FOE_NOTIFICATIONS' => 'Tysta aviseringar',
	'ACP_ZEBRAENHANCE_FOE_NOTIFICATIONS_EXPLAIN' => 'Låter användare stänga av aviseringar som stöds och som skapas av användare de ignorerar.',
	'ACP_ZEBRAENHANCE_FOE_TEMPORARY' => 'Tillfällig ignorering',
	'ACP_ZEBRAENHANCE_FOE_TEMPORARY_EXPLAIN' => 'Låter användare ignorera någon i 24 timmar, 7 dagar eller 30 dagar.',
	'ACP_ZEBRAENHANCE_FOE_NOTES' => 'Privata anteckningar om ignorerade',
	'ACP_ZEBRAENHANCE_FOE_NOTES_EXPLAIN' => 'Låter användare spara en privat anteckning för varje ignorerad användare.',
	'ACP_ZEBRAENHANCE_FOE_EXCEPTIONS' => 'Undantag per ignorerad användare',
	'ACP_ZEBRAENHANCE_FOE_EXCEPTIONS_EXPLAIN' => 'Låter användare åsidosätta sina globala val för privata meddelanden, innehåll och aviseringar för enskilda ignorerade användare.',
));
