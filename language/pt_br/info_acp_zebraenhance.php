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
	'ACP_ZEBRA_ENHANCE_REPORT' => 'Solicitações de amizade pendentes',
	'ACP_ZEBRA_ENHANCE_REPORT_EXPLAIN' => 'Relatório global e somente leitura das solicitações que ainda não foram aceitas, recusadas ou canceladas.',
	'ACP_ZE_REQUEST_ID' => 'ID da solicitação',
	'ACP_ZE_REQUESTER' => 'Solicitante',
	'ACP_ZE_RECIPIENT' => 'Destinatário',
	'ACP_ZE_REQUEST_DATE' => 'Solicitada',
	'ACP_ZE_REQUEST_MESSAGE' => 'Mensagem',
	'ACP_ZE_NO_PENDING_REQUESTS' => 'Não há solicitações de amizade pendentes.',
	'ACP_ZE_PENDING_TOTAL' => array(1 => '%d solicitação de amizade pendente', 2 => '%d solicitações de amizade pendentes'),
	'LOG_ZEBRA_ENHANCE_SETTINGS' => '<strong>Alterou as configurações do Zebra Enhance</strong><br>» Máximo pendente: %1$d; intervalo após recusa: %2$d dias',
));
