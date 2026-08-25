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

	'ZE_FRIENDLIST'	=>	'Lista de Amigos',
	'ZE_FRIENDLIST_EXPLAIN'	=>	'Quem pode ver sua lista de amigos?',

	'FRIENDLIST_ERROR_ACCESS'	=>	'Você não tem acesso para ver a lista de amigos do usuário.',

	'FRIENDLIST_TITLE'       => 'Lista de Amigos',
	'ZE_ACCEPT_REQUEST'      => 'Accept friend request',
	'ZE_DECLINE_REQUEST'     => 'Decline friend request',
	'ZE_DECLINE_BLOCK_REQUEST' => 'Recusar e bloquear solicitante',
	'ZE_CANCEL_REQUEST'      => 'Cancel friend request',
	'ZE_REMOVE_FRIEND'       => 'Remove friend',
	'ZE_ADD_CLOSE_FRIEND'    => 'Add to Close Friends',
	'ZE_REMOVE_CLOSE_FRIEND' => 'Remove from Close Friends',
	'ZE_AJAX_NOT_AUTHORIZED' => 'Você não tem autorização para gerenciar relacionamentos de amizade avançados.',
	'ZE_AJAX_NOT_FRIEND'     => 'That user is not in your friend list.',
	'ZE_AJAX_REQUEST_NOT_FOUND' => 'That friend request no longer exists or does not belong to you.',
	'ZE_REQUESTER_CANNOT_BE_BLOCKED' => 'Administradores e moderadores não podem ser bloqueados.',
	'ZE_REQUEST_ACCEPTED'       => 'Friend request accepted.',
	'ZE_FRIEND_REQUEST_CREATED' => 'Friend request sent.',
	'ZE_REQUEST_DECLINED'       => 'Friend request declined.',
	'ZE_REQUEST_DECLINED_BLOCKED' => 'Pedido de amizade recusado e solicitante bloqueado.',
	'ZE_REQUEST_CANCELLED'      => 'Friend request cancelled.',
	'ZE_CONFIRM_DECLINE_REQUEST' => 'Are you sure you want to decline this friend request?',
	'ZE_CONFIRM_DECLINE_BLOCK_REQUEST' => 'Recusar este pedido de amizade e bloquear o solicitante?',
	'ZE_CONFIRM_CANCEL_REQUEST'  => 'Are you sure you want to cancel this friend request?',
	'ZE_ERROR_TITLE'              => 'Friend request error',
	'ZE_REQUEST_FAILED'           => 'The request could not be completed.',
	'ZE_NO_FRIENDS'               => 'No friends currently defined.',
	'ZE_VIS_EVERYONE'             => 'Everyone',
	'ZE_VIS_REGISTERED'           => 'Registered users',
	'ZE_VIS_NON_FOES'             => 'Everyone except foes',
	'ZE_VIS_FRIENDS'              => 'Friends',
	'ZE_VIS_CLOSE_FRIENDS'        => 'Close Friends',
	'ZE_VIS_NOBODY'               => 'Nobody',
	'ZE_FRIEND_REQUEST_NOT_AUTHORIZED' => 'You are not authorized to send friend requests.',
	'ZE_FRIEND_REQUEST_UNCHANGED'      => 'No friend request was changed.',
	'ZE_REQUEST_POLICY'                    => 'Quem pode enviar solicitações de amizade?',
	'ZE_REQUEST_POLICY_EXPLAIN'            => 'Isto aplica-se apenas a novas solicitações. As solicitações que você já enviou ainda podem ser aceitas.',
	'ZE_REQUEST_POLICY_EVERYONE'           => 'Todos',
	'ZE_REQUEST_POLICY_FRIENDS_OF_FRIENDS' => 'Amigos de amigos',
	'ZE_REQUEST_POLICY_NOBODY'             => 'Ninguém',
	'ZE_REQUEST_MESSAGE'                   => 'Mensagem opcional',
	'ZE_REQUEST_MESSAGE_PLACEHOLDER'       => 'Adicione uma mensagem pessoal curta (opcional)',

	'ZE_MUTUAL_FRIENDS'        => 'Amigos em comum',
	'ZE_BLOCK_FOE_PM'                      => 'Block private messages from foes',
	'ZE_BLOCK_FOE_PM_EXPLAIN'              => 'When enabled, users on your foe list cannot send you private messages. A group PM from a foe that includes you is rejected for the whole group.',
	'ZE_HIDE_FOE_CONTENT'                  => 'Completely hide content from foes',
	'ZE_HIDE_FOE_CONTENT_EXPLAIN'          => 'Removes foe-authored posts instead of showing a placeholder and hides quotes that can be identified as coming from foes. Older or manually attributed quotes may not always be identifiable.',
	'ZE_PM_RECIPIENTS_BLOCKED'             => 'One or more recipients do not accept private messages from you.',
	'ZE_FRIEND_SUGGESTIONS'         => 'Pessoas que você talvez conheça',
	'ZE_FRIEND_SUGGESTIONS_EXPLAIN' => 'Amigos de amigos cujas configurações de privacidade e pedidos permitem a sugestão.',
	'ZE_SEND_FRIEND_REQUEST'        => 'Enviar pedido de amizade',
	'ZE_MUTUAL_FRIEND_COUNT'        => array(
		1 => '%d amigo em comum',
		2 => '%d amigos em comum',
	),
	'ZE_SEARCH_FRIENDS'             => 'Pesquisar amigos',
	'ZE_SELECT_REQUEST'             => 'Selecione pelo menos um pedido de amizade.',
	'ZE_SELECT_REQUEST_ITEM'        => 'Selecionar pedido de amizade relacionado com %s',
	'ZE_SELECT_ALL'                 => 'Selecionar todos',
	'ZE_ACCEPT_SELECTED'            => 'Aceitar selecionados',
	'ZE_DECLINE_SELECTED'           => 'Recusar selecionados',
	'ZE_CANCEL_SELECTED'            => 'Cancelar selecionados',
	'ZE_CONFIRM_DECLINE_SELECTED'   => 'Recusar os pedidos de amizade selecionados?',
	'ZE_CONFIRM_CANCEL_SELECTED'    => 'Cancelar os pedidos de amizade selecionados?',
	'ZE_BULK_REQUESTS_COMPLETED'    => array(
		1 => '%d pedido de amizade processado.',
		2 => '%d pedidos de amizade processados.',
	),
	'ZE_CIRCLES'               => 'Círculos de amigos',
	'ZE_CIRCLES_EXPLAIN'       => 'Crie grupos privados e escolha quais amigos aceitos pertencem a cada um.',
	'ZE_CIRCLE_NAME'           => 'Nome do círculo',
	'ZE_CREATE_CIRCLE'         => 'Criar círculo',
	'ZE_RENAME_CIRCLE'         => 'Renomear',
	'ZE_DELETE_CIRCLE'         => 'Excluir',
	'ZE_SAVE_CIRCLES'          => 'Salvar círculos',
	'ZE_MEMBERS'               => 'membros',
	'ZE_CONFIRM_DELETE_CIRCLE' => 'Excluir este círculo? As amizades não serão removidas.',
	'ZE_CIRCLE_CREATED'        => 'Círculo de amigos criado.',
	'ZE_CIRCLE_RENAMED'        => 'Círculo de amigos renomeado.',
	'ZE_CIRCLE_DELETED'        => 'Círculo de amigos excluído.',
	'ZE_CIRCLES_SAVED'         => 'Círculos de amigos salvos.',
	'ZE_CIRCLE_INVALID'        => 'Digite um nome válido para o círculo com até 50 caracteres.',
	'ZE_CIRCLE_DUPLICATE'      => 'Você já tem um círculo com esse nome.',
	'ZE_CIRCLE_LIMIT'          => 'Você pode criar até 20 círculos de amigos.',
	'ZE_CIRCLE_NOT_FOUND'      => 'Esse círculo não existe mais ou não pertence a você.',

	'ZE_MUTE_FOE_NOTIFICATIONS'         => 'Mute notifications from foes',
	'ZE_MUTE_FOE_NOTIFICATIONS_EXPLAIN' => 'Suppresses quote, mention, reply, topic, and bookmark notifications created by users on your foe list.',
	'ZE_UCP_FOE_MANAGER'                => 'Enhanced foe management',
	'ZE_UCP_FOE_MANAGER_EXPLAIN'        => 'Search your foes, keep private notes, set temporary blocks, and override each global protection for individual users.',
	'ZE_SEARCH_FOES'                    => 'Search foes',
	'ZE_ADD_FOES'                       => 'Add foes',
	'ZE_GLOBAL_DEFAULTS'                => 'Global defaults',
	'ZE_PROTECTION_PM'                  => 'Private messages',
	'ZE_PROTECTION_CONTENT'             => 'Content',
	'ZE_PROTECTION_NOTIFICATIONS'       => 'Notifications',
	'ZE_FOE_ADDED'                      => 'Recorded',
	'ZE_FOE_EXPIRES'                    => 'Expires',
	'ZE_FOE_DATE_UNKNOWN'               => 'Unknown',
	'ZE_FOE_PERMANENT'                  => 'Permanent',
	'ZE_FOE_NOTE'                       => 'Private note',
	'ZE_FOE_NOTE_PLACEHOLDER'           => 'Optional note visible only to you',
	'ZE_FOE_DURATION'                   => 'Duration',
	'ZE_FOE_DURATION_KEEP'              => 'Keep current expiry',
	'ZE_FOE_24_HOURS'                   => '24 hours',
	'ZE_FOE_7_DAYS'                     => '7 days',
	'ZE_FOE_30_DAYS'                    => '30 days',
	'ZE_POLICY_INHERIT'                 => 'Use global default',
	'ZE_POLICY_ALLOW'                   => 'Allow',
	'ZE_POLICY_BLOCK'                   => 'Block',
	'ZE_SELECT_FOE'                     => 'Select at least one foe.',
	'ZE_SELECT_FOE_ITEM'                => 'Selecionar inimigo %s',
	'ZE_REMOVE_SELECTED_FOES'           => 'Remove selected',
	'ZE_CONFIRM_REMOVE_FOES'            => 'Remove the selected users from your foe list?',
	'ZE_NO_FOES'                        => 'No foes currently defined.',
	'ZE_NO_MATCHING_FOES'               => 'No foes match your search.',
	'ZE_FOE_SAVED'                      => 'Foe settings saved.',
	'ZE_FOE_NOT_FOUND'                  => 'That user is no longer on your foe list.',
	'ZE_FOE_ERROR_TITLE'                => 'Foe management error',
	'ZE_FOES_REMOVED'                   => array(
		1 => '%d foe removed.',
		2 => '%d foes removed.',
	),
));
