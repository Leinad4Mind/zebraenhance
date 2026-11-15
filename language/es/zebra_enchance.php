<?php

/**
*
* Friend list enchance [Spanish]
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
	'UCP_ZEBRA_PENDING_IN'	=>	'Esperando confirmación',
	'UCP_ZEBRA_PENDING_IN_EXP'	=>	'Lista de solicitudes que esperan tu aprobación.',

	'UCP_ZEBRA_PENDING_OUT'	=>	'Pendiente de confirmación',
	'UCP_ZEBRA_PENDING_OUT_EXP'	=>	'Lista de tus solicitudes pendientes de aprobación.',

	'UCP_ZEBRA_PENDING_NONE'	=>	'No hay solicitudes pendientes',

	'UCP_ZEBRA_ENCHANCE_CONFIRM_CANCEL_ASK'	=>	'¿Seguro que quieres cancelar la solicitud de amistad?',
	'UCP_ZEBRA_ENCHANCE_CONFIRM_CANCEL'	=> '¡La solicitud de amistad ha sido cancelada!',

	'NOTIFICATION_TYPE_ZEBRA_ADD'	=>	'Notificación de nueva solicitud de amistad',
	'NOTIFICATION_ZEBRA_ADD'	=>	'¡%1$s te envió una solicitud de amistad!',

	'NOTIFICATION_TYPE_ZEBRA_CONFIRM'	=>	'Confirmación de solicitud de amistad',
	'NOTIFICATION_ZEBRA_CONFIRM'	=>	'¡%1$s aceptó tu solicitud de amistad!',

	'FRINEDLIST_TITLE'	=>	'Lista de amigos',

	'NOT_ENEMY'	=>	'Todos excepto ignorados',
	'SPECIAL_FRIENDS'	=>	'Amigos especiales',

	'ZEBRAENHANCE_FRIENDLIST'	=>	'Lista de amigos',
	'ZEBRAENHANCE_FRIENDLIST_EXPLAIN'	=>	'¿Quién puede ver tu lista de amigos?',
	'ZEBRAENHANCE_BIRTHDAY_VISIBILITY'         => '¿Quién puede ver tu cumpleaños y tu edad?',
	'ZEBRAENHANCE_BIRTHDAY_VISIBILITY_EXPLAIN' => 'Quien no tenga permiso no verá tu edad en tu perfil ni junto a tus mensajes, ni tu nombre en la lista de cumpleaños del índice del foro. Los administradores y moderadores siempre pueden verlos.',

	'FRIENDLIST_ERROR_ACCESS'	=>	'No tienes permiso para ver la lista de amigos de este usuario.',

	'FRIENDLIST_TITLE'       => 'Lista de amigos',
	'ZEBRAENHANCE_ACCEPT_REQUEST'      => 'Aceptar solicitud de amistad',
	'ZEBRAENHANCE_DECLINE_REQUEST'     => 'Rechazar solicitud de amistad',
	'ZEBRAENHANCE_DECLINE_BLOCK_REQUEST' => 'Rechazar y bloquear al solicitante',
	'ZEBRAENHANCE_CANCEL_REQUEST'      => 'Cancelar solicitud de amistad',
	'ZEBRAENHANCE_REMOVE_FRIEND'       => 'Eliminar amigo',
	'ZEBRAENHANCE_ADD_CLOSE_FRIEND'    => 'Añadir a Amigos cercanos',
	'ZEBRAENHANCE_REMOVE_CLOSE_FRIEND' => 'Quitar de Amigos cercanos',
	'ZEBRAENHANCE_AJAX_NOT_AUTHORIZED' => 'No tienes permiso para gestionar las relaciones de amistad avanzadas.',
	'ZEBRAENHANCE_AJAX_NOT_FRIEND'     => 'Ese usuario no está en tu lista de amigos.',
	'ZEBRAENHANCE_AJAX_REQUEST_NOT_FOUND' => 'Esa solicitud de amistad ya no existe o no te pertenece.',
	'ZEBRAENHANCE_REQUESTER_CANNOT_BE_BLOCKED' => 'No se puede bloquear a administradores ni moderadores.',
	'ZEBRAENHANCE_REQUEST_ACCEPTED'       => 'Solicitud de amistad aceptada.',
	'ZEBRAENHANCE_FRIEND_REQUEST_CREATED' => 'Solicitud de amistad enviada.',
	'ZEBRAENHANCE_REQUEST_DECLINED'       => 'Solicitud de amistad rechazada.',
	'ZEBRAENHANCE_REQUEST_DECLINED_BLOCKED' => 'Solicitud de amistad rechazada y solicitante bloqueado.',
	'ZEBRAENHANCE_REQUEST_CANCELLED'      => 'Solicitud de amistad cancelada.',
	'ZEBRAENHANCE_CONFIRM_DECLINE_REQUEST' => '¿Seguro que quieres rechazar esta solicitud de amistad?',
	'ZEBRAENHANCE_CONFIRM_DECLINE_BLOCK_REQUEST' => '¿Rechazar esta solicitud de amistad y bloquear al solicitante?',
	'ZEBRAENHANCE_CONFIRM_CANCEL_REQUEST'  => '¿Seguro que quieres cancelar esta solicitud de amistad?',
	'ZEBRAENHANCE_ERROR_TITLE'              => 'Error en la solicitud de amistad',
	'ZEBRAENHANCE_REQUEST_FAILED'           => 'No se pudo completar la solicitud.',
	'ZEBRAENHANCE_NO_FRIENDS'               => 'Todavía no tienes amigos añadidos.',
	'ZEBRAENHANCE_VIS_EVERYONE'             => 'Todos',
	'ZEBRAENHANCE_VIS_REGISTERED'           => 'Usuarios registrados',
	'ZEBRAENHANCE_VIS_NON_FOES'             => 'Todos excepto ignorados',
	'ZEBRAENHANCE_VIS_FRIENDS'              => 'Amigos',
	'ZEBRAENHANCE_VIS_CLOSE_FRIENDS'        => 'Amigos cercanos',
	'ZEBRAENHANCE_VIS_NOBODY'               => 'Nadie',
	'ZEBRAENHANCE_FRIEND_REQUEST_NOT_AUTHORIZED' => 'No tienes permiso para enviar solicitudes de amistad.',
	'ZEBRAENHANCE_FRIEND_REQUEST_UNCHANGED'      => 'No se modificó ninguna solicitud de amistad.',
	'ZEBRAENHANCE_REQUEST_POLICY'                    => '¿Quién puede enviarte solicitudes de amistad?',
	'ZEBRAENHANCE_REQUEST_POLICY_EXPLAIN'            => 'Esto solo se aplica a solicitudes nuevas. Las solicitudes que ya enviaste aún pueden aceptarse.',
	'ZEBRAENHANCE_REQUEST_POLICY_EVERYONE'           => 'Todos',
	'ZEBRAENHANCE_REQUEST_POLICY_FRIENDS_OF_FRIENDS' => 'Amigos de amigos',
	'ZEBRAENHANCE_REQUEST_POLICY_NOBODY'             => 'Nadie',
	'ZEBRAENHANCE_REQUEST_MESSAGE'                   => 'Mensaje opcional',
	'ZEBRAENHANCE_REQUEST_MESSAGE_PLACEHOLDER'       => 'Añade un mensaje personal breve (opcional)',

	'ZEBRAENHANCE_MUTUAL_FRIENDS'        => 'Amigos en común',
	'ZEBRAENHANCE_FRIENDS_SHOWN'          => array(
		1 => 'Mostrando %2$d de %1$d amigo',
		2 => 'Mostrando %2$d de %1$d amigos',
	),
	'ZEBRAENHANCE_SHOW_MORE_FRIENDS'      => 'Ver más',
	'ZEBRAENHANCE_FRIENDS_LOAD_FAILED'    => 'No se pudieron cargar más amigos.',
	'ZEBRAENHANCE_PM_POLICY'                      => '¿Quién puede enviarte mensajes privados?',
	'ZEBRAENHANCE_PM_POLICY_EXPLAIN'              => 'Los administradores y moderadores siempre pueden enviarte mensajes privados. Un mensaje de grupo se rechaza para todo el grupo cuando no lo aceptarías del remitente.',
	'ZEBRAENHANCE_HIDE_FOE_CONTENT'                  => 'Contenido de usuarios ignorados',
	'ZEBRAENHANCE_HIDE_FOE_CONTENT_EXPLAIN'          => 'Se aplica a sus mensajes, a los temas que iniciaron y a las citas de sus mensajes que se pueden identificar. Un aviso mantiene el contenido contraído, pero puedes abrirlo igualmente; en los mensajes es el propio aviso de phpBB. Las citas antiguas o atribuidas manualmente no siempre se pueden identificar.',
	'ZEBRAENHANCE_FOE_CONTENT_SHOW'                  => 'No ocultar',
	'ZEBRAENHANCE_FOE_CONTENT_NOTICE'                => 'Ocultar tras un aviso',
	'ZEBRAENHANCE_FOE_CONTENT_REMOVE'                => 'Eliminar por completo',
	'ZEBRAENHANCE_FOE_QUOTE_HIDDEN'                  => 'Esta cita es de %s, que está en tu lista de ignorados.',
	'ZEBRAENHANCE_FOE_QUOTE_HIDDEN_UNKNOWN'          => 'Esta cita es de un usuario de tu lista de ignorados.',
	'ZEBRAENHANCE_FOE_QUOTE_SHOW'                    => 'Mostrar cita',
	'ZEBRAENHANCE_FOE_QUOTE_HIDE'                    => 'Ocultar cita',
	'ZEBRAENHANCE_FOE_TOPIC_HIDDEN'                  => 'Este tema lo inició %s, que está en tu lista de ignorados.',
	'ZEBRAENHANCE_FOE_TOPIC_SHOW'                    => 'Mostrar tema',
	'ZEBRAENHANCE_FOE_TOPIC_HIDE'                    => 'Ocultar tema',
	'ZEBRAENHANCE_PM_RECIPIENTS_BLOCKED'             => 'Uno o más destinatarios no aceptan tus mensajes privados.',
	'ZEBRAENHANCE_FRIEND_SUGGESTIONS'         => 'Personas que quizá conozcas',
	'ZEBRAENHANCE_FRIEND_SUGGESTIONS_EXPLAIN' => 'Amigos de amigos cuya configuración de privacidad y de solicitudes permite sugerirlos.',
	'ZEBRAENHANCE_SEND_FRIEND_REQUEST'        => 'Enviar solicitud de amistad',
	'ZEBRAENHANCE_MUTUAL_FRIEND_COUNT'        => array(
		1 => '%d amigo en común',
		2 => '%d amigos en común',
	),
	'ZEBRAENHANCE_SEARCH_FRIENDS'             => 'Buscar amigos',
	'ZEBRAENHANCE_SELECT_REQUEST'             => 'Selecciona al menos una solicitud de amistad.',
	'ZEBRAENHANCE_SELECT_REQUEST_ITEM'        => 'Seleccionar la solicitud de amistad con %s',
	'ZEBRAENHANCE_SELECT_ALL'                 => 'Seleccionar todo',
	'ZEBRAENHANCE_ACCEPT_SELECTED'            => 'Aceptar seleccionadas',
	'ZEBRAENHANCE_DECLINE_SELECTED'           => 'Rechazar seleccionadas',
	'ZEBRAENHANCE_CANCEL_SELECTED'            => 'Cancelar seleccionadas',
	'ZEBRAENHANCE_CONFIRM_DECLINE_SELECTED'   => '¿Rechazar las solicitudes de amistad seleccionadas?',
	'ZEBRAENHANCE_CONFIRM_CANCEL_SELECTED'    => '¿Cancelar las solicitudes de amistad seleccionadas?',
	'ZEBRAENHANCE_BULK_REQUESTS_COMPLETED'    => array(
		1 => '%d solicitud de amistad procesada.',
		2 => '%d solicitudes de amistad procesadas.',
	),
	'ZEBRAENHANCE_CIRCLES'               => 'Círculos de amigos',
	'ZEBRAENHANCE_CIRCLES_EXPLAIN'       => 'Crea grupos privados y elige qué amigos aceptados pertenecen a cada uno.',
	'ZEBRAENHANCE_CIRCLE_NAME'           => 'Nombre del círculo',
	'ZEBRAENHANCE_CREATE_CIRCLE'         => 'Crear círculo',
	'ZEBRAENHANCE_RENAME_CIRCLE'         => 'Renombrar',
	'ZEBRAENHANCE_DELETE_CIRCLE'         => 'Eliminar',
	'ZEBRAENHANCE_SAVE_CIRCLES'          => 'Guardar círculos',
	'ZEBRAENHANCE_MEMBERS'               => 'miembros',
	'ZEBRAENHANCE_CONFIRM_DELETE_CIRCLE' => '¿Eliminar este círculo? Las amistades no se eliminarán.',
	'ZEBRAENHANCE_CIRCLE_CREATED'        => 'Círculo de amigos creado.',
	'ZEBRAENHANCE_CIRCLE_RENAMED'        => 'Círculo de amigos renombrado.',
	'ZEBRAENHANCE_CIRCLE_DELETED'        => 'Círculo de amigos eliminado.',
	'ZEBRAENHANCE_CIRCLES_SAVED'         => 'Círculos de amigos guardados.',
	'ZEBRAENHANCE_CIRCLE_INVALID'        => 'Introduce un nombre de círculo válido de hasta 50 caracteres.',
	'ZEBRAENHANCE_CIRCLE_DUPLICATE'      => 'Ya tienes un círculo con ese nombre.',
	'ZEBRAENHANCE_CIRCLE_LIMIT'          => 'Puedes crear hasta 20 círculos de amigos.',
	'ZEBRAENHANCE_CIRCLE_NOT_FOUND'      => 'Ese círculo de amigos ya no existe o no te pertenece.',

	'ZEBRAENHANCE_MUTE_FOE_NOTIFICATIONS'         => 'Silenciar notificaciones de usuarios ignorados',
	'ZEBRAENHANCE_MUTE_FOE_NOTIFICATIONS_EXPLAIN' => 'Suprime las notificaciones de citas, menciones, respuestas, temas y marcadores generadas por usuarios de tu lista de ignorados.',
	'ZEBRAENHANCE_UCP_FOE_MANAGER'                => 'Gestión avanzada de ignorados',
	'ZEBRAENHANCE_UCP_FOE_MANAGER_EXPLAIN'        => 'Busca entre tus ignorados, guarda notas privadas, establece bloqueos temporales y cambia cada protección global para usuarios concretos.',
	'ZEBRAENHANCE_SEARCH_FOES'                    => 'Buscar ignorados',
	'ZEBRAENHANCE_ADD_FOES'                       => 'Añadir ignorados',
	'ZEBRAENHANCE_GLOBAL_DEFAULTS'                => 'Valores globales',
	'ZEBRAENHANCE_PROTECTION_PM'                  => 'Mensajes privados',
	'ZEBRAENHANCE_PROTECTION_CONTENT'             => 'Contenido',
	'ZEBRAENHANCE_PROTECTION_NOTIFICATIONS'       => 'Notificaciones',
	'ZEBRAENHANCE_FOE_ADDED'                      => 'Añadido',
	'ZEBRAENHANCE_FOE_EXPIRES'                    => 'Caduca',
	'ZEBRAENHANCE_FOE_DATE_UNKNOWN'               => 'Desconocido',
	'ZEBRAENHANCE_FOE_PERMANENT'                  => 'Permanente',
	'ZEBRAENHANCE_FOE_NOTE'                       => 'Nota privada',
	'ZEBRAENHANCE_FOE_NOTE_PLACEHOLDER'           => 'Nota opcional que solo tú puedes ver',
	'ZEBRAENHANCE_FOE_DURATION'                   => 'Duración',
	'ZEBRAENHANCE_FOE_DURATION_KEEP'              => 'Mantener la caducidad actual',
	'ZEBRAENHANCE_FOE_24_HOURS'                   => '24 horas',
	'ZEBRAENHANCE_FOE_7_DAYS'                     => '7 días',
	'ZEBRAENHANCE_FOE_30_DAYS'                    => '30 días',
	'ZEBRAENHANCE_POLICY_INHERIT'                 => 'Usar el valor global',
	'ZEBRAENHANCE_POLICY_ALLOW'                   => 'Permitir',
	'ZEBRAENHANCE_POLICY_BLOCK'                   => 'Bloquear',
	'ZEBRAENHANCE_SELECT_FOE'                     => 'Selecciona al menos un usuario ignorado.',
	'ZEBRAENHANCE_SELECT_FOE_ITEM'                => 'Seleccionar a %s',
	'ZEBRAENHANCE_REMOVE_SELECTED_FOES'           => 'Quitar seleccionados',
	'ZEBRAENHANCE_CONFIRM_REMOVE_FOES'            => '¿Quitar a los usuarios seleccionados de tu lista de ignorados?',
	'ZEBRAENHANCE_NO_FOES'                        => 'No tienes usuarios ignorados.',
	'ZEBRAENHANCE_NO_MATCHING_FOES'               => 'Ningún usuario ignorado coincide con tu búsqueda.',
	'ZEBRAENHANCE_FOE_SAVED'                      => 'Ajustes del usuario ignorado guardados.',
	'ZEBRAENHANCE_FOE_NOT_FOUND'                  => 'Ese usuario ya no está en tu lista de ignorados.',
	'ZEBRAENHANCE_FOE_ERROR_TITLE'                => 'Error en la gestión de ignorados',
	'ZEBRAENHANCE_FOES_REMOVED'                   => array(
		1 => '%d usuario ignorado quitado.',
		2 => '%d usuarios ignorados quitados.',
	),
));
