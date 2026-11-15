<?php
/**
* Zebra Enhance [Bulgarian]
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
	'ACP_ZEBRA_ENHANCE_SETTINGS' => 'Настройки на Zebra Enhance',
	'ACP_ZEBRA_ENHANCE_SETTINGS_EXPLAIN' => 'Настройте общите ограничения за покани за приятелство и изберете кои разширени функции за врагове да са достъпни за потребителите в UCP.',
	'ACP_ZEBRA_ENHANCE_REQUESTS' => 'Ограничения за покани',
	'ACP_ZEBRAENHANCE_MAX_PENDING_REQUESTS' => 'Максимален брой чакащи покани',
	'ACP_ZEBRAENHANCE_MAX_PENDING_REQUESTS_EXPLAIN' => 'Максимален брой входящи и изходящи покани за потребител, прилаган отделно за всяка посока. 0 означава без ограничение.',
	'ACP_ZEBRAENHANCE_DECLINE_COOLDOWN_DAYS' => 'Изчакване след отказ',
	'ACP_ZEBRAENHANCE_DECLINE_COOLDOWN_DAYS_EXPLAIN' => 'Дни преди отказан потребител да може да изпрати нова покана. 0 изключва ограничението.',
	'ACP_ZEBRA_ENHANCE_SAVED' => 'Настройките на Zebra Enhance са обновени.',
	'ACP_ZEBRA_ENHANCE_REPORT' => 'Чакащи покани за приятелство',
	'ACP_ZEBRA_ENHANCE_REPORT_EXPLAIN' => 'Отчет само за четене за целия форум с поканите за приятелство, които още не са приети, отказани или оттеглени.',
	'ACP_ZEBRAENHANCE_REQUEST_ID' => 'ID на поканата',
	'ACP_ZEBRAENHANCE_REQUESTER' => 'Подател',
	'ACP_ZEBRAENHANCE_RECIPIENT' => 'Получател',
	'ACP_ZEBRAENHANCE_REQUEST_DATE' => 'Изпратена',
	'ACP_ZEBRAENHANCE_REQUEST_MESSAGE' => 'Съобщение',
	'ACP_ZEBRAENHANCE_NO_PENDING_REQUESTS' => 'Няма чакащи покани за приятелство.',
	'ACP_ZEBRAENHANCE_PENDING_TOTAL' => array(1 => '%d чакаща покана за приятелство', 2 => '%d чакащи покани за приятелство'),
	'LOG_ZEBRA_ENHANCE_SETTINGS' => '<strong>Променени настройки на Zebra Enhance</strong><br>» Максимум чакащи покани: %1$d; изчакване след отказ: %2$d дни; подобрения за врагове: %3$s',
	'ACP_ZEBRA_ENHANCE_FOES' => 'Подобрения за врагове',
	'ACP_ZEBRAENHANCE_FOES_ENHANCEMENT' => 'Включване на подобренията за врагове',
	'ACP_ZEBRAENHANCE_FOES_ENHANCEMENT_EXPLAIN' => 'Прави разширеното управление на враговете и избраните по-долу защити достъпни за потребителите. Когато е изключено, стандартното поведение на phpBB към враговете остава непроменено.',
	'ACP_ZEBRAENHANCE_FOE_PM' => 'Защита на личните съобщения',
	'ACP_ZEBRAENHANCE_FOE_PM_EXPLAIN' => 'Позволява на потребителите да избират кой може да им изпраща лични съобщения: всички, всички без враговете, само приятели или само близки приятели.',
	'ACP_ZEBRAENHANCE_FOE_CONTENT' => 'Скриване на съдържание и цитати',
	'ACP_ZEBRAENHANCE_FOE_CONTENT_EXPLAIN' => 'Позволява на потребителите да скриват мнения, теми, резултати от търсене и разпознаваеми цитати от своите врагове – зад предупреждение или напълно.',
	'ACP_ZEBRAENHANCE_FOE_NOTIFICATIONS' => 'Заглушаване на известия',
	'ACP_ZEBRAENHANCE_FOE_NOTIFICATIONS_EXPLAIN' => 'Позволява на потребителите да спират поддържаните известия, създадени от техните врагове.',
	'ACP_ZEBRAENHANCE_FOE_TEMPORARY' => 'Временни врагове',
	'ACP_ZEBRAENHANCE_FOE_TEMPORARY_EXPLAIN' => 'Позволява на потребителите да добавят някого като враг за 24 часа, 7 дни или 30 дни.',
	'ACP_ZEBRAENHANCE_FOE_NOTES' => 'Лични бележки за врагове',
	'ACP_ZEBRAENHANCE_FOE_NOTES_EXPLAIN' => 'Позволява на потребителите да пазят лична бележка за всеки враг.',
	'ACP_ZEBRAENHANCE_FOE_EXCEPTIONS' => 'Изключения за отделни врагове',
	'ACP_ZEBRAENHANCE_FOE_EXCEPTIONS_EXPLAIN' => 'Позволява на потребителите да променят общите си настройки за лични съобщения, съдържание и известия за отделни врагове.',
));
