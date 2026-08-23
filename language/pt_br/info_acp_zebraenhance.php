<?php
/**
* Zebra Enhance [Brazilian Portuguese]
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
	'ACP_ZEBRA_ENHANCE_SETTINGS' => 'Configurações de solicitações de amizade',
	'ACP_ZEBRA_ENHANCE_SETTINGS_EXPLAIN' => 'Configure os limites globais para solicitações de amizade. Cada usuário pode aplicar restrições adicionais no UCP.',
	'ACP_ZEBRA_ENHANCE_REQUESTS' => 'Limites de solicitações',
	'ACP_ZE_MAX_PENDING_REQUESTS' => 'Máximo de solicitações pendentes',
	'ACP_ZE_MAX_PENDING_REQUESTS_EXPLAIN' => 'Máximo combinado de solicitações recebidas e enviadas por usuário. Use 0 para não limitar.',
	'ACP_ZE_DECLINE_COOLDOWN_DAYS' => 'Intervalo após uma recusa',
	'ACP_ZE_DECLINE_COOLDOWN_DAYS_EXPLAIN' => 'Dias até que a pessoa recusada possa enviar nova solicitação. Use 0 para desativar.',
	'ACP_ZEBRA_ENHANCE_SAVED' => 'Configurações do Zebra Enhance atualizadas.',
	'LOG_ZEBRA_ENHANCE_SETTINGS' => '<strong>Alterou as configurações do Zebra Enhance</strong><br>» Máximo pendente: %1$d; intervalo após recusa: %2$d dias',
));
