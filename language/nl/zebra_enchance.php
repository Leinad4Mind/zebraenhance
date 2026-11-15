<?php

/**
*
* Zebraenhance [Dutch] Translated by Dutch Translators (https://github.com/dutch-translators)
*
* @package language
* @version $Id$
* @copyright (c) 2026 Leinad4Mind
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
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
	'UCP_ZEBRA_PENDING_IN'	=>	'Wachten op goedkeuring',
	'UCP_ZEBRA_PENDING_IN_EXP'	=>	'Lijst met vriendschapsverzoeken die op jouw goedkeuring wachten.',

	'UCP_ZEBRA_PENDING_OUT'	=>	'In afwachting van goedkeuring',
	'UCP_ZEBRA_PENDING_OUT_EXP'	=>	'Lijst met jouw vriendschapsverzoeken die nog moeten worden goedgekeurd.',

	'UCP_ZEBRA_PENDING_NONE'	=>	'Geen wachtende vriendschapsverzoeken',

	'UCP_ZEBRA_ENCHANCE_CONFIRM_CANCEL_ASK'	=>	'Weet je zeker dat je dit vriendschapsverzoek wilt annuleren?',
	'UCP_ZEBRA_ENCHANCE_CONFIRM_CANCEL'	=> 'Het vriendschapsverzoek is geannuleerd!',

	'NOTIFICATION_TYPE_ZEBRA_ADD'	=>	'Melding van een nieuw vriendschapsverzoek',
	'NOTIFICATION_ZEBRA_ADD'	=>	'%1$s heeft je een vriendschapsverzoek gestuurd!',

	'NOTIFICATION_TYPE_ZEBRA_CONFIRM'	=>	'Bevestiging van je vriendschapsverzoek',
	'NOTIFICATION_ZEBRA_CONFIRM'	=>	'%1$s heeft je vriendschapsverzoek goedgekeurd!',

	'FRINEDLIST_TITLE'	=>	'Vriendenlijst',

	'NOT_ENEMY'	=>	'Alle behalve vijanden',
	'SPECIAL_FRIENDS'	=>	'Speciale vrienden',

	'ZEBRAENHANCE_FRIENDLIST'	=>	'Vriendenlijst',
	'ZEBRAENHANCE_FRIENDLIST_EXPLAIN'	=>	'Wie kan je vriendenlijst zien?',
	'ZEBRAENHANCE_BIRTHDAY_VISIBILITY'         => 'Wie mag je verjaardag en leeftijd zien?',
	'ZEBRAENHANCE_BIRTHDAY_VISIBILITY_EXPLAIN' => 'Wie geen toestemming heeft, ziet je leeftijd niet op je profiel of naast je berichten, en je naam niet in de verjaardagslijst op de forumindex. Beheerders en moderators kunnen ze altijd zien.',

	'FRIENDLIST_ERROR_ACCESS'	=>	'Je hebt geen toegang tot de vriendenlijst van deze gebruiker.',

	'FRIENDLIST_TITLE'       => 'Vriendenlijst',
	'ZEBRAENHANCE_ACCEPT_REQUEST'      => 'Vriendschapsverzoek accepteren',
	'ZEBRAENHANCE_DECLINE_REQUEST'     => 'Vriendschapsverzoek weigeren',
	'ZEBRAENHANCE_DECLINE_BLOCK_REQUEST' => 'Weigeren en afzender blokkeren',
	'ZEBRAENHANCE_CANCEL_REQUEST'      => 'Vriendschapsverzoek annuleren',
	'ZEBRAENHANCE_REMOVE_FRIEND'       => 'Vriend verwijderen',
	'ZEBRAENHANCE_ADD_CLOSE_FRIEND'    => 'Toevoegen aan goede vrienden',
	'ZEBRAENHANCE_REMOVE_CLOSE_FRIEND' => 'Verwijderen uit goede vrienden',
	'ZEBRAENHANCE_AJAX_NOT_AUTHORIZED' => 'Je hebt geen toestemming om uitgebreide vriendschapsrelaties te beheren.',
	'ZEBRAENHANCE_AJAX_NOT_FRIEND'     => 'Die gebruiker staat niet in je vriendenlijst.',
	'ZEBRAENHANCE_AJAX_REQUEST_NOT_FOUND' => 'Dat vriendschapsverzoek bestaat niet meer of is niet van jou.',
	'ZEBRAENHANCE_REQUESTER_CANNOT_BE_BLOCKED' => 'Beheerders en moderators kunnen niet worden geblokkeerd.',
	'ZEBRAENHANCE_REQUEST_ACCEPTED'       => 'Vriendschapsverzoek geaccepteerd.',
	'ZEBRAENHANCE_FRIEND_REQUEST_CREATED' => 'Vriendschapsverzoek verstuurd.',
	'ZEBRAENHANCE_REQUEST_DECLINED'       => 'Vriendschapsverzoek geweigerd.',
	'ZEBRAENHANCE_REQUEST_DECLINED_BLOCKED' => 'Vriendschapsverzoek geweigerd en afzender geblokkeerd.',
	'ZEBRAENHANCE_REQUEST_CANCELLED'      => 'Vriendschapsverzoek geannuleerd.',
	'ZEBRAENHANCE_CONFIRM_DECLINE_REQUEST' => 'Weet je zeker dat je dit vriendschapsverzoek wilt weigeren?',
	'ZEBRAENHANCE_CONFIRM_DECLINE_BLOCK_REQUEST' => 'Dit vriendschapsverzoek weigeren en de afzender blokkeren?',
	'ZEBRAENHANCE_CONFIRM_CANCEL_REQUEST'  => 'Weet je zeker dat je dit vriendschapsverzoek wilt annuleren?',
	'ZEBRAENHANCE_ERROR_TITLE'              => 'Fout bij vriendschapsverzoek',
	'ZEBRAENHANCE_REQUEST_FAILED'           => 'Het verzoek kon niet worden voltooid.',
	'ZEBRAENHANCE_NO_FRIENDS'               => 'Je hebt momenteel geen vrienden toegevoegd.',
	'ZEBRAENHANCE_VIS_EVERYONE'             => 'Iedereen',
	'ZEBRAENHANCE_VIS_REGISTERED'           => 'Geregistreerde gebruikers',
	'ZEBRAENHANCE_VIS_NON_FOES'             => 'Iedereen behalve vijanden',
	'ZEBRAENHANCE_VIS_FRIENDS'              => 'Vrienden',
	'ZEBRAENHANCE_VIS_CLOSE_FRIENDS'        => 'Goede vrienden',
	'ZEBRAENHANCE_VIS_NOBODY'               => 'Niemand',
	'ZEBRAENHANCE_FRIEND_REQUEST_NOT_AUTHORIZED' => 'Je hebt geen toestemming om vriendschapsverzoeken te sturen.',
	'ZEBRAENHANCE_FRIEND_REQUEST_UNCHANGED'      => 'Er is geen vriendschapsverzoek gewijzigd.',
	'ZEBRAENHANCE_REQUEST_POLICY'                    => 'Wie mag je vriendschapsverzoeken sturen?',
	'ZEBRAENHANCE_REQUEST_POLICY_EXPLAIN'            => 'Dit geldt alleen voor nieuwe verzoeken. Verzoeken die je al hebt gestuurd kunnen nog worden geaccepteerd.',
	'ZEBRAENHANCE_REQUEST_POLICY_EVERYONE'           => 'Iedereen',
	'ZEBRAENHANCE_REQUEST_POLICY_FRIENDS_OF_FRIENDS' => 'Vrienden van vrienden',
	'ZEBRAENHANCE_REQUEST_POLICY_NOBODY'             => 'Niemand',
	'ZEBRAENHANCE_REQUEST_MESSAGE'                   => 'Optioneel bericht',
	'ZEBRAENHANCE_REQUEST_MESSAGE_PLACEHOLDER'       => 'Voeg een kort persoonlijk bericht toe (optioneel)',

	'ZEBRAENHANCE_MUTUAL_FRIENDS'        => 'Gemeenschappelijke vrienden',
	'ZEBRAENHANCE_FRIENDS_SHOWN'          => array(
		1 => '%2$d van %1$d vriend weergegeven',
		2 => '%2$d van %1$d vrienden weergegeven',
	),
	'ZEBRAENHANCE_SHOW_MORE_FRIENDS'      => 'Meer tonen',
	'ZEBRAENHANCE_FRIENDS_LOAD_FAILED'    => 'Meer vrienden konden niet worden geladen.',
	'ZEBRAENHANCE_SEARCH_FRIEND_POSTS'    => 'Berichten van je vrienden bekijken',
	'ZEBRAENHANCE_FRIEND_MARK'            => 'Vriend',
	'ZEBRAENHANCE_CLOSE_FRIEND_MARK'      => 'Goede vriend',
	'ZEBRAENHANCE_HIGHLIGHT_FRIENDS'      => 'Inhoud van vrienden markeren',
	'ZEBRAENHANCE_HIGHLIGHT_FRIENDS_EXPLAIN' => 'Markeert berichten, onderwerpen en privéberichten van je vrienden, met een aparte kleur voor goede vrienden. Alleen jij ziet de markering.',
	'ZEBRAENHANCE_PM_POLICY'                      => 'Wie mag je privéberichten sturen?',
	'ZEBRAENHANCE_PM_POLICY_EXPLAIN'              => 'Beheerders en moderators kunnen je altijd privéberichten sturen. Als iemand die je niet zou accepteren een bericht naar een groep stuurt waar je lid van bent, word je overgeslagen en ontvangt de rest van de groep het gewoon.',
	'ZEBRAENHANCE_HIDE_FOE_CONTENT'                  => 'Inhoud van vijanden',
	'ZEBRAENHANCE_HIDE_FOE_CONTENT_EXPLAIN'          => 'Geldt voor hun berichten, de onderwerpen die ze zijn begonnen en de citaten van hun berichten die herkend kunnen worden. Een mededeling houdt de inhoud ingeklapt, maar je kunt die nog steeds openen; bij berichten is dat de eigen mededeling van phpBB. Oudere of handmatig toegeschreven citaten worden niet altijd herkend.',
	'ZEBRAENHANCE_FOE_CONTENT_SHOW'                  => 'Niet verbergen',
	'ZEBRAENHANCE_FOE_CONTENT_NOTICE'                => 'Verbergen achter een mededeling',
	'ZEBRAENHANCE_FOE_CONTENT_REMOVE'                => 'Volledig verwijderen',
	'ZEBRAENHANCE_FOE_QUOTE_HIDDEN'                  => 'Dit citaat is van %s, die op je vijandenlijst staat.',
	'ZEBRAENHANCE_FOE_QUOTE_HIDDEN_UNKNOWN'          => 'Dit citaat is van een gebruiker op je vijandenlijst.',
	'ZEBRAENHANCE_FOE_QUOTE_SHOW'                    => 'Citaat tonen',
	'ZEBRAENHANCE_FOE_QUOTE_HIDE'                    => 'Citaat verbergen',
	'ZEBRAENHANCE_FOE_TOPIC_HIDDEN'                  => 'Dit onderwerp is gestart door %s, die op je vijandenlijst staat.',
	'ZEBRAENHANCE_FOE_TOPIC_SHOW'                    => 'Onderwerp tonen',
	'ZEBRAENHANCE_FOE_TOPIC_HIDE'                    => 'Onderwerp verbergen',
	'ZEBRAENHANCE_PM_RECIPIENTS_BLOCKED'             => 'Een of meer ontvangers accepteren geen privéberichten van jou.',
	'ZEBRAENHANCE_FRIEND_SUGGESTIONS'         => 'Mensen die je misschien kent',
	'ZEBRAENHANCE_FRIEND_SUGGESTIONS_EXPLAIN' => 'Vrienden van vrienden van wie de privacy- en verzoekinstellingen een suggestie toestaan.',
	'ZEBRAENHANCE_SEND_FRIEND_REQUEST'        => 'Vriendschapsverzoek sturen',
	'ZEBRAENHANCE_MUTUAL_FRIEND_COUNT'        => array(
		1 => '%d gemeenschappelijke vriend',
		2 => '%d gemeenschappelijke vrienden',
	),
	'ZEBRAENHANCE_SEARCH_FRIENDS'             => 'Vrienden zoeken',
	'ZEBRAENHANCE_SELECT_REQUEST'             => 'Selecteer ten minste één vriendschapsverzoek.',
	'ZEBRAENHANCE_SELECT_REQUEST_ITEM'        => 'Vriendschapsverzoek met %s selecteren',
	'ZEBRAENHANCE_SELECT_ALL'                 => 'Alles selecteren',
	'ZEBRAENHANCE_ACCEPT_SELECTED'            => 'Selectie accepteren',
	'ZEBRAENHANCE_DECLINE_SELECTED'           => 'Selectie weigeren',
	'ZEBRAENHANCE_CANCEL_SELECTED'            => 'Selectie annuleren',
	'ZEBRAENHANCE_CONFIRM_DECLINE_SELECTED'   => 'De geselecteerde vriendschapsverzoeken weigeren?',
	'ZEBRAENHANCE_CONFIRM_CANCEL_SELECTED'    => 'De geselecteerde vriendschapsverzoeken annuleren?',
	'ZEBRAENHANCE_BULK_REQUESTS_COMPLETED'    => array(
		1 => '%d vriendschapsverzoek verwerkt.',
		2 => '%d vriendschapsverzoeken verwerkt.',
	),
	'ZEBRAENHANCE_CIRCLES'               => 'Vriendenkringen',
	'ZEBRAENHANCE_CIRCLES_EXPLAIN'       => 'Maak privégroepen en kies welke geaccepteerde vrienden bij elke groep horen.',
	'ZEBRAENHANCE_CIRCLE_NAME'           => 'Naam van de kring',
	'ZEBRAENHANCE_CREATE_CIRCLE'         => 'Kring maken',
	'ZEBRAENHANCE_RENAME_CIRCLE'         => 'Hernoemen',
	'ZEBRAENHANCE_DELETE_CIRCLE'         => 'Verwijderen',
	'ZEBRAENHANCE_SAVE_CIRCLES'          => 'Kringen opslaan',
	'ZEBRAENHANCE_MEMBERS'               => 'leden',
	'ZEBRAENHANCE_CONFIRM_DELETE_CIRCLE' => 'Deze kring verwijderen? Vriendschappen worden niet verwijderd.',
	'ZEBRAENHANCE_CIRCLE_CREATED'        => 'Vriendenkring gemaakt.',
	'ZEBRAENHANCE_CIRCLE_RENAMED'        => 'Vriendenkring hernoemd.',
	'ZEBRAENHANCE_CIRCLE_DELETED'        => 'Vriendenkring verwijderd.',
	'ZEBRAENHANCE_CIRCLES_SAVED'         => 'Vriendenkringen opgeslagen.',
	'ZEBRAENHANCE_CIRCLE_INVALID'        => 'Voer een geldige naam voor de kring in van maximaal 50 tekens.',
	'ZEBRAENHANCE_CIRCLE_DUPLICATE'      => 'Je hebt al een kring met die naam.',
	'ZEBRAENHANCE_CIRCLE_LIMIT'          => 'Je kunt maximaal 20 vriendenkringen maken.',
	'ZEBRAENHANCE_CIRCLE_NOT_FOUND'      => 'Die vriendenkring bestaat niet meer of is niet van jou.',

	'ZEBRAENHANCE_MUTE_FOE_NOTIFICATIONS'         => 'Meldingen van vijanden dempen',
	'ZEBRAENHANCE_MUTE_FOE_NOTIFICATIONS_EXPLAIN' => 'Onderdrukt meldingen over citaten, vermeldingen, antwoorden, onderwerpen en bladwijzers die door gebruikers op je vijandenlijst worden veroorzaakt.',
	'ZEBRAENHANCE_UCP_FOE_MANAGER'                => 'Uitgebreid vijandenbeheer',
	'ZEBRAENHANCE_UCP_FOE_MANAGER_EXPLAIN'        => 'Doorzoek je vijanden, bewaar privénotities, stel tijdelijke blokkeringen in en wijk per gebruiker af van elke algemene bescherming.',
	'ZEBRAENHANCE_SEARCH_FOES'                    => 'Vijanden zoeken',
	'ZEBRAENHANCE_ADD_FOES'                       => 'Vijanden toevoegen',
	'ZEBRAENHANCE_GLOBAL_DEFAULTS'                => 'Algemene standaardinstellingen',
	'ZEBRAENHANCE_PROTECTION_PM'                  => 'Privéberichten',
	'ZEBRAENHANCE_PROTECTION_CONTENT'             => 'Inhoud',
	'ZEBRAENHANCE_PROTECTION_NOTIFICATIONS'       => 'Meldingen',
	'ZEBRAENHANCE_FOE_ADDED'                      => 'Toegevoegd',
	'ZEBRAENHANCE_FOE_EXPIRES'                    => 'Verloopt',
	'ZEBRAENHANCE_FOE_DATE_UNKNOWN'               => 'Onbekend',
	'ZEBRAENHANCE_FOE_PERMANENT'                  => 'Blijvend',
	'ZEBRAENHANCE_FOE_NOTE'                       => 'Privénotitie',
	'ZEBRAENHANCE_FOE_NOTE_PLACEHOLDER'           => 'Optionele notitie die alleen jij kunt zien',
	'ZEBRAENHANCE_FOE_DURATION'                   => 'Duur',
	'ZEBRAENHANCE_FOE_DURATION_KEEP'              => 'Huidige vervaldatum behouden',
	'ZEBRAENHANCE_FOE_24_HOURS'                   => '24 uur',
	'ZEBRAENHANCE_FOE_7_DAYS'                     => '7 dagen',
	'ZEBRAENHANCE_FOE_30_DAYS'                    => '30 dagen',
	'ZEBRAENHANCE_POLICY_INHERIT'                 => 'Algemene standaard gebruiken',
	'ZEBRAENHANCE_POLICY_ALLOW'                   => 'Toestaan',
	'ZEBRAENHANCE_POLICY_BLOCK'                   => 'Blokkeren',
	'ZEBRAENHANCE_SELECT_FOE'                     => 'Selecteer ten minste één vijand.',
	'ZEBRAENHANCE_SELECT_FOE_ITEM'                => 'Vijand %s selecteren',
	'ZEBRAENHANCE_REMOVE_SELECTED_FOES'           => 'Selectie verwijderen',
	'ZEBRAENHANCE_CONFIRM_REMOVE_FOES'            => 'De geselecteerde gebruikers van je vijandenlijst verwijderen?',
	'ZEBRAENHANCE_NO_FOES'                        => 'Je hebt momenteel geen vijanden toegevoegd.',
	'ZEBRAENHANCE_NO_MATCHING_FOES'               => 'Geen vijanden gevonden die aan je zoekopdracht voldoen.',
	'ZEBRAENHANCE_FOE_SAVED'                      => 'Instellingen voor de vijand opgeslagen.',
	'ZEBRAENHANCE_FOE_NOT_FOUND'                  => 'Die gebruiker staat niet meer op je vijandenlijst.',
	'ZEBRAENHANCE_FOE_ERROR_TITLE'                => 'Fout bij vijandenbeheer',
	'ZEBRAENHANCE_FOES_REMOVED'                   => array(
		1 => '%d vijand verwijderd.',
		2 => '%d vijanden verwijderd.',
	),
));
