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
	'ACP_ZEBRA_ENHANCE_SETTINGS' => 'Ajustes de solicitudes de amistad',
	'ACP_ZEBRA_ENHANCE_SETTINGS_EXPLAIN' => 'Configura los límites globales para las solicitudes. Cada usuario puede aplicar más restricciones en el UCP.',
	'ACP_ZEBRA_ENHANCE_REQUESTS' => 'Límites de solicitudes',
	'ACP_ZE_MAX_PENDING_REQUESTS' => 'Máximo de solicitudes pendientes',
	'ACP_ZE_MAX_PENDING_REQUESTS_EXPLAIN' => 'Máximo combinado de solicitudes recibidas y enviadas por usuario. Usa 0 para no limitar.',
	'ACP_ZE_DECLINE_COOLDOWN_DAYS' => 'Espera tras un rechazo',
	'ACP_ZE_DECLINE_COOLDOWN_DAYS_EXPLAIN' => 'Días hasta que la persona rechazada pueda enviar otra solicitud. Usa 0 para desactivar.',
	'ACP_ZEBRA_ENHANCE_SAVED' => 'Ajustes de Zebra Enhance actualizados.',
	'LOG_ZEBRA_ENHANCE_SETTINGS' => '<strong>Cambió los ajustes de Zebra Enhance</strong><br>» Máximo pendiente: %1$d; espera tras rechazo: %2$d días',
));
