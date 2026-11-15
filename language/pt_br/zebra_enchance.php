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
	'NOTIFICATION_ZEBRA_CONFIRM'	=>	'%1$s confirmou sua solicitação de amizade!',

	'FRINEDLIST_TITLE'	=>	'Lista de amigos',

	'NOT_ENEMY'	=>	'Todos exceto inimigos',
	'SPECIAL_FRIENDS'	=>	'Amigos especiais',

	'ZEBRAENHANCE_FRIENDLIST'	=>	'Lista de amigos',
	'ZEBRAENHANCE_FRIENDLIST_EXPLAIN'	=>	'Quem pode ver sua lista de amigos?',
	'ZEBRAENHANCE_BIRTHDAY_VISIBILITY'         => 'Quem pode ver seu aniversário e sua idade?',
	'ZEBRAENHANCE_BIRTHDAY_VISIBILITY_EXPLAIN' => 'Quem não tiver permissão não verá sua idade no seu perfil nem ao lado das suas mensagens, nem seu nome na lista de aniversariantes do índice do fórum. Administradores e moderadores sempre podem vê-los.',

	'FRIENDLIST_ERROR_ACCESS'	=>	'Você não tem acesso para ver a lista de amigos do usuário.',

	'FRIENDLIST_TITLE'       => 'Lista de amigos',
	'ZEBRAENHANCE_ACCEPT_REQUEST'      => 'Aceitar solicitação de amizade',
	'ZEBRAENHANCE_DECLINE_REQUEST'     => 'Recusar solicitação de amizade',
	'ZEBRAENHANCE_DECLINE_BLOCK_REQUEST' => 'Recusar e bloquear solicitante',
	'ZEBRAENHANCE_CANCEL_REQUEST'      => 'Cancelar solicitação de amizade',
	'ZEBRAENHANCE_REMOVE_FRIEND'       => 'Remover amigo',
	'ZEBRAENHANCE_ADD_CLOSE_FRIEND'    => 'Adicionar aos Amigos próximos',
	'ZEBRAENHANCE_REMOVE_CLOSE_FRIEND' => 'Remover dos Amigos próximos',
	'ZEBRAENHANCE_AJAX_NOT_AUTHORIZED' => 'Você não tem permissão para gerenciar relacionamentos de amizade avançados.',
	'ZEBRAENHANCE_AJAX_NOT_FRIEND'     => 'Esse usuário não está na sua lista de amigos.',
	'ZEBRAENHANCE_AJAX_REQUEST_NOT_FOUND' => 'Essa solicitação de amizade não existe mais ou não pertence a você.',
	'ZEBRAENHANCE_REQUESTER_CANNOT_BE_BLOCKED' => 'Administradores e moderadores não podem ser bloqueados.',
	'ZEBRAENHANCE_REQUEST_ACCEPTED'       => 'Solicitação de amizade aceita.',
	'ZEBRAENHANCE_FRIEND_REQUEST_CREATED' => 'Solicitação de amizade enviada.',
	'ZEBRAENHANCE_REQUEST_DECLINED'       => 'Solicitação de amizade recusada.',
	'ZEBRAENHANCE_REQUEST_DECLINED_BLOCKED' => 'Solicitação de amizade recusada e solicitante bloqueado.',
	'ZEBRAENHANCE_REQUEST_CANCELLED'      => 'Solicitação de amizade cancelada.',
	'ZEBRAENHANCE_CONFIRM_DECLINE_REQUEST' => 'Tem certeza de que deseja recusar esta solicitação de amizade?',
	'ZEBRAENHANCE_CONFIRM_DECLINE_BLOCK_REQUEST' => 'Recusar esta solicitação de amizade e bloquear o solicitante?',
	'ZEBRAENHANCE_CONFIRM_CANCEL_REQUEST'  => 'Tem certeza de que deseja cancelar esta solicitação de amizade?',
	'ZEBRAENHANCE_ERROR_TITLE'              => 'Erro na solicitação de amizade',
	'ZEBRAENHANCE_REQUEST_FAILED'           => 'Não foi possível concluir a solicitação.',
	'ZEBRAENHANCE_NO_FRIENDS'               => 'Nenhum amigo adicionado no momento.',
	'ZEBRAENHANCE_VIS_EVERYONE'             => 'Todos',
	'ZEBRAENHANCE_VIS_REGISTERED'           => 'Usuários registrados',
	'ZEBRAENHANCE_VIS_NON_FOES'             => 'Todos exceto inimigos',
	'ZEBRAENHANCE_VIS_FRIENDS'              => 'Amigos',
	'ZEBRAENHANCE_VIS_CLOSE_FRIENDS'        => 'Amigos próximos',
	'ZEBRAENHANCE_VIS_NOBODY'               => 'Ninguém',
	'ZEBRAENHANCE_FRIEND_REQUEST_NOT_AUTHORIZED' => 'Você não tem permissão para enviar solicitações de amizade.',
	'ZEBRAENHANCE_FRIEND_REQUEST_UNCHANGED'      => 'Nenhuma solicitação de amizade foi alterada.',
	'ZEBRAENHANCE_REQUEST_POLICY'                    => 'Quem pode enviar solicitações de amizade para você?',
	'ZEBRAENHANCE_REQUEST_POLICY_EXPLAIN'            => 'Isso vale apenas para novas solicitações. As solicitações que você já enviou ainda podem ser aceitas.',
	'ZEBRAENHANCE_REQUEST_POLICY_EVERYONE'           => 'Todos',
	'ZEBRAENHANCE_REQUEST_POLICY_FRIENDS_OF_FRIENDS' => 'Amigos de amigos',
	'ZEBRAENHANCE_REQUEST_POLICY_NOBODY'             => 'Ninguém',
	'ZEBRAENHANCE_REQUEST_MESSAGE'                   => 'Mensagem opcional',
	'ZEBRAENHANCE_REQUEST_MESSAGE_PLACEHOLDER'       => 'Adicione uma mensagem pessoal curta (opcional)',

	'ZEBRAENHANCE_MUTUAL_FRIENDS'        => 'Amigos em comum',
	'ZEBRAENHANCE_FRIENDS_SHOWN'          => array(
		1 => 'Mostrando %2$d de %1$d amigo',
		2 => 'Mostrando %2$d de %1$d amigos',
	),
	'ZEBRAENHANCE_SHOW_MORE_FRIENDS'      => 'Ver mais',
	'ZEBRAENHANCE_FRIENDS_LOAD_FAILED'    => 'Não foi possível carregar mais amigos.',
	'ZEBRAENHANCE_SEARCH_FRIEND_POSTS'    => 'Ver as mensagens dos seus amigos',
	'ZEBRAENHANCE_PM_POLICY'                      => 'Quem pode enviar mensagens privadas para você?',
	'ZEBRAENHANCE_PM_POLICY_EXPLAIN'              => 'Administradores e moderadores sempre podem enviar mensagens privadas para você. Quando alguém que você não aceitaria envia uma mensagem a um grupo do qual você faz parte, você fica de fora e o resto do grupo a recebe normalmente.',
	'ZEBRAENHANCE_HIDE_FOE_CONTENT'                  => 'Conteúdo de inimigos',
	'ZEBRAENHANCE_HIDE_FOE_CONTENT_EXPLAIN'          => 'Aplica-se às mensagens deles, aos tópicos que eles criaram e às citações das mensagens deles que podem ser identificadas. O aviso mantém o conteúdo recolhido, mas você ainda pode abri-lo; nas mensagens, é o próprio aviso do phpBB. Citações antigas ou atribuídas manualmente nem sempre podem ser identificadas.',
	'ZEBRAENHANCE_FOE_CONTENT_SHOW'                  => 'Não ocultar',
	'ZEBRAENHANCE_FOE_CONTENT_NOTICE'                => 'Ocultar com um aviso',
	'ZEBRAENHANCE_FOE_CONTENT_REMOVE'                => 'Remover completamente',
	'ZEBRAENHANCE_FOE_QUOTE_HIDDEN'                  => 'Esta citação é de %s, que está na sua lista de inimigos.',
	'ZEBRAENHANCE_FOE_QUOTE_HIDDEN_UNKNOWN'          => 'Esta citação é de um usuário da sua lista de inimigos.',
	'ZEBRAENHANCE_FOE_QUOTE_SHOW'                    => 'Mostrar citação',
	'ZEBRAENHANCE_FOE_QUOTE_HIDE'                    => 'Ocultar citação',
	'ZEBRAENHANCE_FOE_TOPIC_HIDDEN'                  => 'Este tópico foi criado por %s, que está na sua lista de inimigos.',
	'ZEBRAENHANCE_FOE_TOPIC_SHOW'                    => 'Mostrar tópico',
	'ZEBRAENHANCE_FOE_TOPIC_HIDE'                    => 'Ocultar tópico',
	'ZEBRAENHANCE_PM_RECIPIENTS_BLOCKED'             => 'Um ou mais destinatários não aceitam mensagens privadas de você.',
	'ZEBRAENHANCE_FRIEND_SUGGESTIONS'         => 'Pessoas que você talvez conheça',
	'ZEBRAENHANCE_FRIEND_SUGGESTIONS_EXPLAIN' => 'Amigos de amigos cujas configurações de privacidade e de solicitações permitem a sugestão.',
	'ZEBRAENHANCE_SEND_FRIEND_REQUEST'        => 'Enviar solicitação de amizade',
	'ZEBRAENHANCE_MUTUAL_FRIEND_COUNT'        => array(
		1 => '%d amigo em comum',
		2 => '%d amigos em comum',
	),
	'ZEBRAENHANCE_SEARCH_FRIENDS'             => 'Pesquisar amigos',
	'ZEBRAENHANCE_SELECT_REQUEST'             => 'Selecione pelo menos uma solicitação de amizade.',
	'ZEBRAENHANCE_SELECT_REQUEST_ITEM'        => 'Selecionar a solicitação de amizade com %s',
	'ZEBRAENHANCE_SELECT_ALL'                 => 'Selecionar todos',
	'ZEBRAENHANCE_ACCEPT_SELECTED'            => 'Aceitar selecionadas',
	'ZEBRAENHANCE_DECLINE_SELECTED'           => 'Recusar selecionadas',
	'ZEBRAENHANCE_CANCEL_SELECTED'            => 'Cancelar selecionadas',
	'ZEBRAENHANCE_CONFIRM_DECLINE_SELECTED'   => 'Recusar as solicitações de amizade selecionadas?',
	'ZEBRAENHANCE_CONFIRM_CANCEL_SELECTED'    => 'Cancelar as solicitações de amizade selecionadas?',
	'ZEBRAENHANCE_BULK_REQUESTS_COMPLETED'    => array(
		1 => '%d solicitação de amizade processada.',
		2 => '%d solicitações de amizade processadas.',
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

	'ZEBRAENHANCE_MUTE_FOE_NOTIFICATIONS'         => 'Silenciar notificações de inimigos',
	'ZEBRAENHANCE_MUTE_FOE_NOTIFICATIONS_EXPLAIN' => 'Suprime notificações de citações, menções, respostas, tópicos e favoritos geradas por usuários da sua lista de inimigos.',
	'ZEBRAENHANCE_UCP_FOE_MANAGER'                => 'Gerenciamento avançado de inimigos',
	'ZEBRAENHANCE_UCP_FOE_MANAGER_EXPLAIN'        => 'Pesquise seus inimigos, salve notas privadas, defina bloqueios temporários e substitua cada proteção global para usuários específicos.',
	'ZEBRAENHANCE_SEARCH_FOES'                    => 'Pesquisar inimigos',
	'ZEBRAENHANCE_ADD_FOES'                       => 'Adicionar inimigos',
	'ZEBRAENHANCE_GLOBAL_DEFAULTS'                => 'Padrões globais',
	'ZEBRAENHANCE_PROTECTION_PM'                  => 'Mensagens privadas',
	'ZEBRAENHANCE_PROTECTION_CONTENT'             => 'Conteúdo',
	'ZEBRAENHANCE_PROTECTION_NOTIFICATIONS'       => 'Notificações',
	'ZEBRAENHANCE_FOE_ADDED'                      => 'Adicionado em',
	'ZEBRAENHANCE_FOE_EXPIRES'                    => 'Expira em',
	'ZEBRAENHANCE_FOE_DATE_UNKNOWN'               => 'Desconhecida',
	'ZEBRAENHANCE_FOE_PERMANENT'                  => 'Permanente',
	'ZEBRAENHANCE_FOE_NOTE'                       => 'Nota privada',
	'ZEBRAENHANCE_FOE_NOTE_PLACEHOLDER'           => 'Nota opcional visível apenas para você',
	'ZEBRAENHANCE_FOE_DURATION'                   => 'Duração',
	'ZEBRAENHANCE_FOE_DURATION_KEEP'              => 'Manter a expiração atual',
	'ZEBRAENHANCE_FOE_24_HOURS'                   => '24 horas',
	'ZEBRAENHANCE_FOE_7_DAYS'                     => '7 dias',
	'ZEBRAENHANCE_FOE_30_DAYS'                    => '30 dias',
	'ZEBRAENHANCE_POLICY_INHERIT'                 => 'Usar o padrão global',
	'ZEBRAENHANCE_POLICY_ALLOW'                   => 'Permitir',
	'ZEBRAENHANCE_POLICY_BLOCK'                   => 'Bloquear',
	'ZEBRAENHANCE_SELECT_FOE'                     => 'Selecione pelo menos um inimigo.',
	'ZEBRAENHANCE_SELECT_FOE_ITEM'                => 'Selecionar inimigo %s',
	'ZEBRAENHANCE_REMOVE_SELECTED_FOES'           => 'Remover selecionados',
	'ZEBRAENHANCE_CONFIRM_REMOVE_FOES'            => 'Remover os usuários selecionados da sua lista de inimigos?',
	'ZEBRAENHANCE_NO_FOES'                        => 'Nenhum inimigo adicionado no momento.',
	'ZEBRAENHANCE_NO_MATCHING_FOES'               => 'Nenhum inimigo corresponde à sua pesquisa.',
	'ZEBRAENHANCE_FOE_SAVED'                      => 'Configurações do inimigo salvas.',
	'ZEBRAENHANCE_FOE_NOT_FOUND'                  => 'Esse usuário não está mais na sua lista de inimigos.',
	'ZEBRAENHANCE_FOE_ERROR_TITLE'                => 'Erro no gerenciamento de inimigos',
	'ZEBRAENHANCE_FOES_REMOVED'                   => array(
		1 => '%d inimigo removido.',
		2 => '%d inimigos removidos.',
	),
));
