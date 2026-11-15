<?php
/**
* Zebra Enhance [Spanish]
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
	'ACP_ZEBRA_ENHANCE_SETTINGS' => 'Ajustes de Zebra Enhance',
	'ACP_ZEBRA_ENHANCE_SETTINGS_EXPLAIN' => 'Configura los límites globales de las solicitudes de amistad y la búsqueda de mensajes de amigos, y elige qué funciones avanzadas para usuarios ignorados pueden usar los usuarios en el UCP.',
	'ACP_ZEBRA_ENHANCE_REQUESTS' => 'Límites de solicitudes',
	'ACP_ZEBRAENHANCE_MAX_PENDING_REQUESTS' => 'Máximo de solicitudes pendientes',
	'ACP_ZEBRAENHANCE_MAX_PENDING_REQUESTS_EXPLAIN' => 'Máximo de solicitudes recibidas y enviadas por usuario, aplicado por separado a cada dirección. Usa 0 para no limitar.',
	'ACP_ZEBRAENHANCE_DECLINE_COOLDOWN_DAYS' => 'Espera tras un rechazo',
	'ACP_ZEBRAENHANCE_DECLINE_COOLDOWN_DAYS_EXPLAIN' => 'Días hasta que la persona rechazada pueda enviar otra solicitud. Usa 0 para desactivar.',
	'ACP_ZEBRA_ENHANCE_FRIENDS' => 'Amigos',
	'ACP_ZEBRAENHANCE_FRIEND_POSTS' => 'Búsqueda de mensajes de amigos',
	'ACP_ZEBRAENHANCE_FRIEND_POSTS_EXPLAIN' => 'Añade «Ver los mensajes de tus amigos» al menú de enlaces rápidos y, junto a cada amigo en Panel de Control de Usuario > Amigos, un enlace que busca los mensajes de ese amigo.',
	'ACP_ZEBRAENHANCE_FRIEND_HIGHLIGHT' => 'Resaltar el contenido de los amigos',
	'ACP_ZEBRAENHANCE_FRIEND_HIGHLIGHT_EXPLAIN' => 'Permite a los usuarios resaltar los mensajes, temas, resultados de búsqueda y mensajes privados de sus amigos, con un color propio para los Amigos cercanos. Cada usuario puede desactivarlo en Panel de Control de Usuario > Amigos.',
	'ACP_ZEBRAENHANCE_FRIEND_AVATARS' => 'Mostrar los avatares de los amigos',
	'ACP_ZEBRAENHANCE_FRIEND_AVATARS_EXPLAIN' => 'Muestra el avatar de cada amigo en Panel de Control de Usuario > Amigos.',
	'ACP_ZEBRA_ENHANCE_SAVED' => 'Ajustes de Zebra Enhance actualizados.',
	'ACP_ZEBRA_ENHANCE_REPORT' => 'Solicitudes de amistad pendientes',
	'ACP_ZEBRA_ENHANCE_REPORT_EXPLAIN' => 'Informe global de solo lectura de las solicitudes que aún no se han aceptado, rechazado o cancelado.',
	'ACP_ZEBRAENHANCE_REQUEST_ID' => 'ID de solicitud',
	'ACP_ZEBRAENHANCE_REQUESTER' => 'Solicitante',
	'ACP_ZEBRAENHANCE_RECIPIENT' => 'Destinatario',
	'ACP_ZEBRAENHANCE_REQUEST_DATE' => 'Solicitada',
	'ACP_ZEBRAENHANCE_REQUEST_MESSAGE' => 'Mensaje',
	'ACP_ZEBRAENHANCE_NO_PENDING_REQUESTS' => 'No hay solicitudes de amistad pendientes.',
	'ACP_ZEBRAENHANCE_PENDING_TOTAL' => array(1 => '%d solicitud de amistad pendiente', 2 => '%d solicitudes de amistad pendientes'),
	'LOG_ZEBRA_ENHANCE_SETTINGS' => '<strong>Cambió los ajustes de Zebra Enhance</strong><br>» Máximo de solicitudes pendientes: %1$d; espera tras un rechazo: %2$d días; mejoras para ignorados: %3$s',
	'ACP_ZEBRA_ENHANCE_FOES' => 'Mejoras para usuarios ignorados',
	'ACP_ZEBRAENHANCE_FOES_ENHANCEMENT' => 'Activar mejoras para usuarios ignorados',
	'ACP_ZEBRAENHANCE_FOES_ENHANCEMENT_EXPLAIN' => 'Pone a disposición de los usuarios la gestión avanzada de ignorados y las protecciones seleccionadas abajo. Si se desactiva, el comportamiento nativo de phpBB con los usuarios ignorados no cambia.',
	'ACP_ZEBRAENHANCE_FOE_PM' => 'Protección de mensajes privados',
	'ACP_ZEBRAENHANCE_FOE_PM_EXPLAIN' => 'Permite a los usuarios elegir quién puede enviarles mensajes privados: todos, todos excepto ignorados, solo amigos o solo Amigos cercanos.',
	'ACP_ZEBRAENHANCE_FOE_CONTENT' => 'Ocultación de contenido y citas',
	'ACP_ZEBRAENHANCE_FOE_CONTENT_EXPLAIN' => 'Permite a los usuarios ocultar mensajes, temas, resultados de búsqueda y citas identificables de los usuarios que ignoran, tras un aviso o por completo.',
	'ACP_ZEBRAENHANCE_FOE_NOTIFICATIONS' => 'Silenciado de notificaciones',
	'ACP_ZEBRAENHANCE_FOE_NOTIFICATIONS_EXPLAIN' => 'Permite a los usuarios suprimir las notificaciones compatibles generadas por los usuarios que ignoran.',
	'ACP_ZEBRAENHANCE_FOE_TEMPORARY' => 'Ignorados temporales',
	'ACP_ZEBRAENHANCE_FOE_TEMPORARY_EXPLAIN' => 'Permite a los usuarios ignorar a alguien durante 24 horas, 7 días o 30 días.',
	'ACP_ZEBRAENHANCE_FOE_NOTES' => 'Notas privadas sobre ignorados',
	'ACP_ZEBRAENHANCE_FOE_NOTES_EXPLAIN' => 'Permite a los usuarios guardar una nota privada para cada usuario ignorado.',
	'ACP_ZEBRAENHANCE_FOE_EXCEPTIONS' => 'Excepciones por usuario ignorado',
	'ACP_ZEBRAENHANCE_FOE_EXCEPTIONS_EXPLAIN' => 'Permite a los usuarios cambiar sus opciones globales de MP, contenido y notificaciones para usuarios ignorados concretos.',
));
