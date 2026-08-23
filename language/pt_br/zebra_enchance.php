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

	'FRIENDLIST_TITLE'       => 'Friend list',
	'ZE_ACCEPT_REQUEST'      => 'Accept friend request',
	'ZE_DECLINE_REQUEST'     => 'Decline friend request',
	'ZE_CANCEL_REQUEST'      => 'Cancel friend request',
	'ZE_REMOVE_FRIEND'       => 'Remove friend',
	'ZE_ADD_CLOSE_FRIEND'    => 'Add to Close Friends',
	'ZE_REMOVE_CLOSE_FRIEND' => 'Remove from Close Friends',
	'ZE_AJAX_NOT_AUTHORIZED' => 'Você não tem autorização para gerenciar relacionamentos de amizade avançados.',
	'ZE_AJAX_NOT_FRIEND'     => 'That user is not in your friend list.',
	'ZE_AJAX_REQUEST_NOT_FOUND' => 'That friend request no longer exists or does not belong to you.',
	'ZE_REQUEST_ACCEPTED'       => 'Friend request accepted.',
	'ZE_FRIEND_REQUEST_CREATED' => 'Friend request sent.',
	'ZE_REQUEST_DECLINED'       => 'Friend request declined.',
	'ZE_REQUEST_CANCELLED'      => 'Friend request cancelled.',
	'ZE_CONFIRM_DECLINE_REQUEST' => 'Are you sure you want to decline this friend request?',
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

));
