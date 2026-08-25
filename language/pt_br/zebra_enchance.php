<?php

/**
*
* Zebra Enhance [Brazilian Portuguese [pt_br]]
* Brazilian Portuguese translation by eunaumtenhoid (c) 2017 [ver 1.0.4] (https://github.com/phpBBTraducoes)
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
	'UCP_ZEBRA_PENDING_IN'	=>	'Esperando confirmação',
	'UCP_ZEBRA_PENDING_IN_EXP'	=>	'Lista com solicitações aguardando sua aprovação.',

	'UCP_ZEBRA_PENDING_OUT'	=>	'Confirmação pendente',
	'UCP_ZEBRA_PENDING_OUT_EXP'	=>	'Lista com suas solicitações pendentes de aprovação.',

	'UCP_ZEBRA_PENDING_NONE'	=>	'Sem solicitações pendentes',

	'UCP_ZEBRA_ENCHANCE_CONFIRM_CANCEL_ASK'	=>	'Tem certeza de que deseja cancelar a solicitação de amizade?',
	'UCP_ZEBRA_ENCHANCE_CONFIRM_CANCEL'	=> 'A solicitação de amizade foi cancelada!',

	'NOTIFICATION_TYPE_ZEBRA_ADD'	=>	'Nova notificação de solicitação de amizade',
	'NOTIFICATION_ZEBRA_ADD'	=>	'%1$s lhe enviou uma solicitação de amizade!',

	'NOTIFICATION_TYPE_ZEBRA_CONFIRM'	=>	'Confirmação para solicitação de amizade',
	'NOTIFICATION_ZEBRA_CONFIRM'	=>	'%1$s Confirmou sua solicitação de amizade!',

	'FRINEDLIST_TITLE'	=>	'Lista de Amigos',

	'NOT_ENEMY'	=>	'Todos exceto inimigos',
	'SPECIAL_FRIENDS'	=>	'Amigos especiais',

	'ZEBRAENHANCE_FRIENDLIST'	=>	'Lista de Amigos',
	'ZEBRAENHANCE_FRIENDLIST_EXPLAIN'	=>	'Quem pode ver sua lista de amigos?',

	'FRIENDLIST_ERROR_ACCESS'	=>	'Você não tem acesso para ver a lista de amigos do usuário.',

	'FRIENDLIST_TITLE'       => 'Lista de Amigos',
	'ZEBRAENHANCE_ACCEPT_REQUEST'      => 'Accept friend request',
	'ZEBRAENHANCE_DECLINE_REQUEST'     => 'Decline friend request',
	'ZEBRAENHANCE_DECLINE_BLOCK_REQUEST' => 'Recusar e bloquear solicitante',
	'ZEBRAENHANCE_CANCEL_REQUEST'      => 'Cancel friend request',
	'ZEBRAENHANCE_REMOVE_FRIEND'       => 'Remove friend',
	'ZEBRAENHANCE_ADD_CLOSE_FRIEND'    => 'Add to Close Friends',
	'ZEBRAENHANCE_REMOVE_CLOSE_FRIEND' => 'Remove from Close Friends',
	'ZEBRAENHANCE_AJAX_NOT_AUTHORIZED' => 'Você não tem autorização para gerenciar relacionamentos de amizade avançados.',
	'ZEBRAENHANCE_AJAX_NOT_FRIEND'     => 'That user is not in your friend list.',
	'ZEBRAENHANCE_AJAX_REQUEST_NOT_FOUND' => 'That friend request no longer exists or does not belong to you.',
	'ZEBRAENHANCE_REQUESTER_CANNOT_BE_BLOCKED' => 'Administradores e moderadores não podem ser bloqueados.',
	'ZEBRAENHANCE_REQUEST_ACCEPTED'       => 'Friend request accepted.',
	'ZEBRAENHANCE_FRIEND_REQUEST_CREATED' => 'Friend request sent.',
	'ZEBRAENHANCE_REQUEST_DECLINED'       => 'Friend request declined.',
	'ZEBRAENHANCE_REQUEST_DECLINED_BLOCKED' => 'Pedido de amizade recusado e solicitante bloqueado.',
	'ZEBRAENHANCE_REQUEST_CANCELLED'      => 'Friend request cancelled.',
	'ZEBRAENHANCE_CONFIRM_DECLINE_REQUEST' => 'Are you sure you want to decline this friend request?',
	'ZEBRAENHANCE_CONFIRM_DECLINE_BLOCK_REQUEST' => 'Recusar este pedido de amizade e bloquear o solicitante?',
	'ZEBRAENHANCE_CONFIRM_CANCEL_REQUEST'  => 'Are you sure you want to cancel this friend request?',
	'ZEBRAENHANCE_ERROR_TITLE'              => 'Friend request error',
	'ZEBRAENHANCE_REQUEST_FAILED'           => 'The request could not be completed.',
	'ZEBRAENHANCE_NO_FRIENDS'               => 'No friends currently defined.',
	'ZEBRAENHANCE_VIS_EVERYONE'             => 'Everyone',
	'ZEBRAENHANCE_VIS_REGISTERED'           => 'Registered users',
	'ZEBRAENHANCE_VIS_NON_FOES'             => 'Everyone except foes',
	'ZEBRAENHANCE_VIS_FRIENDS'              => 'Friends',
	'ZEBRAENHANCE_VIS_CLOSE_FRIENDS'        => 'Close Friends',
	'ZEBRAENHANCE_VIS_NOBODY'               => 'Nobody',
	'ZEBRAENHANCE_FRIEND_REQUEST_NOT_AUTHORIZED' => 'You are not authorized to send friend requests.',
	'ZEBRAENHANCE_FRIEND_REQUEST_UNCHANGED'      => 'No friend request was changed.',
	'ZEBRAENHANCE_REQUEST_POLICY'                    => 'Quem pode enviar solicitações de amizade?',
	'ZEBRAENHANCE_REQUEST_POLICY_EXPLAIN'            => 'Isto aplica-se apenas a novas solicitações. As solicitações que você já enviou ainda podem ser aceitas.',
	'ZEBRAENHANCE_REQUEST_POLICY_EVERYONE'           => 'Todos',
	'ZEBRAENHANCE_REQUEST_POLICY_FRIENDS_OF_FRIENDS' => 'Amigos de amigos',
	'ZEBRAENHANCE_REQUEST_POLICY_NOBODY'             => 'Ninguém',
	'ZEBRAENHANCE_REQUEST_MESSAGE'                   => 'Mensagem opcional',
	'ZEBRAENHANCE_REQUEST_MESSAGE_PLACEHOLDER'       => 'Adicione uma mensagem pessoal curta (opcional)',

	'ZEBRAENHANCE_MUTUAL_FRIENDS'        => 'Amigos em comum',
	'ZEBRAENHANCE_BLOCK_FOE_PM'                      => 'Block private messages from foes',
	'ZEBRAENHANCE_BLOCK_FOE_PM_EXPLAIN'              => 'When enabled, users on your foe list cannot send you private messages. A group PM from a foe that includes you is rejected for the whole group.',
	'ZEBRAENHANCE_HIDE_FOE_CONTENT'                  => 'Completely hide content from foes',
	'ZEBRAENHANCE_HIDE_FOE_CONTENT_EXPLAIN'          => 'Removes foe-authored posts instead of showing a placeholder and hides quotes that can be identified as coming from foes. Older or manually attributed quotes may not always be identifiable.',
	'ZEBRAENHANCE_PM_RECIPIENTS_BLOCKED'             => 'One or more recipients do not accept private messages from you.',
	'ZEBRAENHANCE_FRIEND_SUGGESTIONS'         => 'Pessoas que você talvez conheça',
	'ZEBRAENHANCE_FRIEND_SUGGESTIONS_EXPLAIN' => 'Amigos de amigos cujas configurações de privacidade e pedidos permitem a sugestão.',
	'ZEBRAENHANCE_SEND_FRIEND_REQUEST'        => 'Enviar pedido de amizade',
	'ZEBRAENHANCE_MUTUAL_FRIEND_COUNT'        => array(
		1 => '%d amigo em comum',
		2 => '%d amigos em comum',
	),
	'ZEBRAENHANCE_SEARCH_FRIENDS'             => 'Pesquisar amigos',
	'ZEBRAENHANCE_SELECT_REQUEST'             => 'Selecione pelo menos um pedido de amizade.',
	'ZEBRAENHANCE_SELECT_REQUEST_ITEM'        => 'Selecionar pedido de amizade relacionado com %s',
	'ZEBRAENHANCE_SELECT_ALL'                 => 'Selecionar todos',
	'ZEBRAENHANCE_ACCEPT_SELECTED'            => 'Aceitar selecionados',
	'ZEBRAENHANCE_DECLINE_SELECTED'           => 'Recusar selecionados',
	'ZEBRAENHANCE_CANCEL_SELECTED'            => 'Cancelar selecionados',
	'ZEBRAENHANCE_CONFIRM_DECLINE_SELECTED'   => 'Recusar os pedidos de amizade selecionados?',
	'ZEBRAENHANCE_CONFIRM_CANCEL_SELECTED'    => 'Cancelar os pedidos de amizade selecionados?',
	'ZEBRAENHANCE_BULK_REQUESTS_COMPLETED'    => array(
		1 => '%d pedido de amizade processado.',
		2 => '%d pedidos de amizade processados.',
	),
	'ZEBRAENHANCE_CIRCLES'               => 'Círculos de amigos',
	'ZEBRAENHANCE_CIRCLES_EXPLAIN'       => 'Crie grupos privados e escolha quais amigos aceitos pertencem a cada um.',
	'ZEBRAENHANCE_CIRCLE_NAME'           => 'Nome do círculo',
	'ZEBRAENHANCE_CREATE_CIRCLE'         => 'Criar círculo',
	'ZEBRAENHANCE_RENAME_CIRCLE'         => 'Renomear',
	'ZEBRAENHANCE_DELETE_CIRCLE'         => 'Excluir',
	'ZEBRAENHANCE_SAVE_CIRCLES'          => 'Salvar círculos',
	'ZEBRAENHANCE_MEMBERS'               => 'membros',
	'ZEBRAENHANCE_CONFIRM_DELETE_CIRCLE' => 'Excluir este círculo? As amizades não serão removidas.',
	'ZEBRAENHANCE_CIRCLE_CREATED'        => 'Círculo de amigos criado.',
	'ZEBRAENHANCE_CIRCLE_RENAMED'        => 'Círculo de amigos renomeado.',
	'ZEBRAENHANCE_CIRCLE_DELETED'        => 'Círculo de amigos excluído.',
	'ZEBRAENHANCE_CIRCLES_SAVED'         => 'Círculos de amigos salvos.',
	'ZEBRAENHANCE_CIRCLE_INVALID'        => 'Digite um nome válido para o círculo com até 50 caracteres.',
	'ZEBRAENHANCE_CIRCLE_DUPLICATE'      => 'Você já tem um círculo com esse nome.',
	'ZEBRAENHANCE_CIRCLE_LIMIT'          => 'Você pode criar até 20 círculos de amigos.',
	'ZEBRAENHANCE_CIRCLE_NOT_FOUND'      => 'Esse círculo não existe mais ou não pertence a você.',

	'ZEBRAENHANCE_MUTE_FOE_NOTIFICATIONS'         => 'Mute notifications from foes',
	'ZEBRAENHANCE_MUTE_FOE_NOTIFICATIONS_EXPLAIN' => 'Suppresses quote, mention, reply, topic, and bookmark notifications created by users on your foe list.',
	'ZEBRAENHANCE_UCP_FOE_MANAGER'                => 'Enhanced foe management',
	'ZEBRAENHANCE_UCP_FOE_MANAGER_EXPLAIN'        => 'Search your foes, keep private notes, set temporary blocks, and override each global protection for individual users.',
	'ZEBRAENHANCE_SEARCH_FOES'                    => 'Search foes',
	'ZEBRAENHANCE_ADD_FOES'                       => 'Add foes',
	'ZEBRAENHANCE_GLOBAL_DEFAULTS'                => 'Global defaults',
	'ZEBRAENHANCE_PROTECTION_PM'                  => 'Private messages',
	'ZEBRAENHANCE_PROTECTION_CONTENT'             => 'Content',
	'ZEBRAENHANCE_PROTECTION_NOTIFICATIONS'       => 'Notifications',
	'ZEBRAENHANCE_FOE_ADDED'                      => 'Recorded',
	'ZEBRAENHANCE_FOE_EXPIRES'                    => 'Expires',
	'ZEBRAENHANCE_FOE_DATE_UNKNOWN'               => 'Unknown',
	'ZEBRAENHANCE_FOE_PERMANENT'                  => 'Permanent',
	'ZEBRAENHANCE_FOE_NOTE'                       => 'Private note',
	'ZEBRAENHANCE_FOE_NOTE_PLACEHOLDER'           => 'Optional note visible only to you',
	'ZEBRAENHANCE_FOE_DURATION'                   => 'Duration',
	'ZEBRAENHANCE_FOE_DURATION_KEEP'              => 'Keep current expiry',
	'ZEBRAENHANCE_FOE_24_HOURS'                   => '24 hours',
	'ZEBRAENHANCE_FOE_7_DAYS'                     => '7 days',
	'ZEBRAENHANCE_FOE_30_DAYS'                    => '30 days',
	'ZEBRAENHANCE_POLICY_INHERIT'                 => 'Use global default',
	'ZEBRAENHANCE_POLICY_ALLOW'                   => 'Allow',
	'ZEBRAENHANCE_POLICY_BLOCK'                   => 'Block',
	'ZEBRAENHANCE_SELECT_FOE'                     => 'Select at least one foe.',
	'ZEBRAENHANCE_SELECT_FOE_ITEM'                => 'Selecionar inimigo %s',
	'ZEBRAENHANCE_REMOVE_SELECTED_FOES'           => 'Remove selected',
	'ZEBRAENHANCE_CONFIRM_REMOVE_FOES'            => 'Remove the selected users from your foe list?',
	'ZEBRAENHANCE_NO_FOES'                        => 'No foes currently defined.',
	'ZEBRAENHANCE_NO_MATCHING_FOES'               => 'No foes match your search.',
	'ZEBRAENHANCE_FOE_SAVED'                      => 'Foe settings saved.',
	'ZEBRAENHANCE_FOE_NOT_FOUND'                  => 'That user is no longer on your foe list.',
	'ZEBRAENHANCE_FOE_ERROR_TITLE'                => 'Foe management error',
	'ZEBRAENHANCE_FOES_REMOVED'                   => array(
		1 => '%d foe removed.',
		2 => '%d foes removed.',
	),
));
