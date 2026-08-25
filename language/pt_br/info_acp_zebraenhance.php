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
	'ACP_ZEBRA_ENHANCE_SETTINGS' => 'Configurações do Zebra Enhance',
	'ACP_ZEBRA_ENHANCE_SETTINGS_EXPLAIN' => 'Configure os limites globais de solicitações de amizade e escolha quais melhorias para inimigos estarão disponíveis no UCP.',
	'ACP_ZEBRA_ENHANCE_REQUESTS' => 'Limites de solicitações',
	'ACP_ZEBRAENHANCE_MAX_PENDING_REQUESTS' => 'Máximo de solicitações pendentes',
	'ACP_ZEBRAENHANCE_MAX_PENDING_REQUESTS_EXPLAIN' => 'Máximo de solicitações recebidas e enviadas por usuário, aplicado separadamente a cada direção. Use 0 para não limitar.',
	'ACP_ZEBRAENHANCE_DECLINE_COOLDOWN_DAYS' => 'Intervalo após uma recusa',
	'ACP_ZEBRAENHANCE_DECLINE_COOLDOWN_DAYS_EXPLAIN' => 'Dias até que a pessoa recusada possa enviar nova solicitação. Use 0 para desativar.',
	'ACP_ZEBRA_ENHANCE_FOES' => 'Melhorias para inimigos',
	'ACP_ZEBRAENHANCE_FOES_ENHANCEMENT' => 'Ativar melhorias para inimigos',
	'ACP_ZEBRAENHANCE_FOES_ENHANCEMENT_EXPLAIN' => 'Disponibiliza o gestor melhorado de inimigos e as proteções selecionadas abaixo. Quando desativada, permanece apenas o comportamento nativo do phpBB.',
	'ACP_ZEBRAENHANCE_FOE_PM' => 'Proteção de mensagens privadas',
	'ACP_ZEBRAENHANCE_FOE_PM_EXPLAIN' => 'Permite que os usuários rejeitem mensagens privadas enviadas pelos seus inimigos.',
	'ACP_ZEBRAENHANCE_FOE_CONTENT' => 'Ocultação de conteúdo e citações',
	'ACP_ZEBRAENHANCE_FOE_CONTENT_EXPLAIN' => 'Permite ocultar mensagens, resultados de pesquisa e citações identificáveis dos inimigos.',
	'ACP_ZEBRAENHANCE_FOE_NOTIFICATIONS' => 'Silenciar notificações',
	'ACP_ZEBRAENHANCE_FOE_NOTIFICATIONS_EXPLAIN' => 'Permite suprimir notificações compatíveis geradas pelos inimigos.',
	'ACP_ZEBRAENHANCE_FOE_TEMPORARY' => 'Inimigos temporários',
	'ACP_ZEBRAENHANCE_FOE_TEMPORARY_EXPLAIN' => 'Permite que relações de inimigo expirem após 24 horas, 7 dias ou 30 dias.',
	'ACP_ZEBRAENHANCE_FOE_NOTES' => 'Notas privadas sobre inimigos',
	'ACP_ZEBRAENHANCE_FOE_NOTES_EXPLAIN' => 'Permite guardar uma nota privada para cada inimigo.',
	'ACP_ZEBRAENHANCE_FOE_EXCEPTIONS' => 'Exceções por inimigo',
	'ACP_ZEBRAENHANCE_FOE_EXCEPTIONS_EXPLAIN' => 'Permite substituir as escolhas globais de mensagens privadas, conteúdo e notificações para inimigos individuais.',
	'ACP_ZEBRA_ENHANCE_SAVED' => 'Configurações do Zebra Enhance atualizadas.',
	'ACP_ZEBRA_ENHANCE_REPORT' => 'Solicitações de amizade pendentes',
	'ACP_ZEBRA_ENHANCE_REPORT_EXPLAIN' => 'Relatório global e somente leitura das solicitações que ainda não foram aceitas, recusadas ou canceladas.',
	'ACP_ZEBRAENHANCE_REQUEST_ID' => 'ID da solicitação',
	'ACP_ZEBRAENHANCE_REQUESTER' => 'Solicitante',
	'ACP_ZEBRAENHANCE_RECIPIENT' => 'Destinatário',
	'ACP_ZEBRAENHANCE_REQUEST_DATE' => 'Solicitada',
	'ACP_ZEBRAENHANCE_REQUEST_MESSAGE' => 'Mensagem',
	'ACP_ZEBRAENHANCE_NO_PENDING_REQUESTS' => 'Não há solicitações de amizade pendentes.',
	'ACP_ZEBRAENHANCE_PENDING_TOTAL' => array(1 => '%d solicitação de amizade pendente', 2 => '%d solicitações de amizade pendentes'),
	'LOG_ZEBRA_ENHANCE_SETTINGS' => '<strong>Alterou as configurações do Zebra Enhance</strong><br>» Máximo pendente: %1$d; intervalo após recusa: %2$d dias; melhorias para inimigos: %3$s',
));
