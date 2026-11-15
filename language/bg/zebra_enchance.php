<?php

/**
*
* Zebra Enhance [Bulgarian]
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
	'UCP_ZEBRA_PENDING_IN'	=>	'Очакващи потвърждение',
	'UCP_ZEBRA_PENDING_IN_EXP'	=>	'Списък с покани за приятелство, които очакват вашето потвърждение.',

	'UCP_ZEBRA_PENDING_OUT'	=>	'Изпратени покани',
	'UCP_ZEBRA_PENDING_OUT_EXP'	=>	'Списък с вашите покани за приятелство, които очакват потвърждение.',

	'UCP_ZEBRA_PENDING_NONE'	=>	'Нямате чакащи покани',

	'UCP_ZEBRA_ENCHANCE_CONFIRM_CANCEL_ASK'	=>	'Сигурни ли сте, че искате да оттеглите поканата за приятелство?',
	'UCP_ZEBRA_ENCHANCE_CONFIRM_CANCEL'	=> 'Поканата за приятелство е оттеглена!',

	'NOTIFICATION_TYPE_ZEBRA_ADD'	=>	'Известие за нова покана за приятелство',
	'NOTIFICATION_ZEBRA_ADD'	=>	'%1$s ви изпрати покана за приятелство!',

	'NOTIFICATION_TYPE_ZEBRA_CONFIRM'	=>	'Известие за приета покана за приятелство',
	'NOTIFICATION_ZEBRA_CONFIRM'	=>	'%1$s прие поканата ви за приятелство!',

	'FRINEDLIST_TITLE'	=>	'Приятели',

	'NOT_ENEMY'	=>	'Всички без врагове',
	'SPECIAL_FRIENDS'	=>	'Специални приятели',

	'ZEBRAENHANCE_FRIENDLIST'	=>	'Списък с приятели',
	'ZEBRAENHANCE_FRIENDLIST_EXPLAIN'	=>	'Кой може да вижда приятелите ви в профила ви?',

	'FRIENDLIST_ERROR_ACCESS'	=>	'Нямате права да виждате приятелите на този потребител.',
	'FRIENDLIST_TITLE'       => 'Приятели',
	'ZEBRAENHANCE_ACCEPT_REQUEST'      => 'Приеми поканата за приятелство',
	'ZEBRAENHANCE_DECLINE_REQUEST'     => 'Откажи поканата за приятелство',
	'ZEBRAENHANCE_DECLINE_BLOCK_REQUEST' => 'Откажи и блокирай подателя',
	'ZEBRAENHANCE_CANCEL_REQUEST'      => 'Оттегли поканата за приятелство',
	'ZEBRAENHANCE_REMOVE_FRIEND'       => 'Премахни от приятели',
	'ZEBRAENHANCE_ADD_CLOSE_FRIEND'    => 'Добави към близките приятели',
	'ZEBRAENHANCE_REMOVE_CLOSE_FRIEND' => 'Премахни от близките приятели',
	'ZEBRAENHANCE_AJAX_NOT_AUTHORIZED' => 'Нямате права да управлявате разширените приятелски връзки.',
	'ZEBRAENHANCE_AJAX_NOT_FRIEND'     => 'Този потребител не е в списъка ви с приятели.',
	'ZEBRAENHANCE_AJAX_REQUEST_NOT_FOUND' => 'Тази покана за приятелство вече не съществува или не е ваша.',
	'ZEBRAENHANCE_REQUESTER_CANNOT_BE_BLOCKED' => 'Администраторите и модераторите не могат да бъдат блокирани.',
	'ZEBRAENHANCE_REQUEST_ACCEPTED'       => 'Поканата за приятелство е приета.',
	'ZEBRAENHANCE_FRIEND_REQUEST_CREATED' => 'Поканата за приятелство е изпратена.',
	'ZEBRAENHANCE_REQUEST_DECLINED'       => 'Поканата за приятелство е отказана.',
	'ZEBRAENHANCE_REQUEST_DECLINED_BLOCKED' => 'Поканата за приятелство е отказана, а подателят е блокиран.',
	'ZEBRAENHANCE_REQUEST_CANCELLED'      => 'Поканата за приятелство е оттеглена.',
	'ZEBRAENHANCE_CONFIRM_DECLINE_REQUEST' => 'Сигурни ли сте, че искате да откажете тази покана за приятелство?',
	'ZEBRAENHANCE_CONFIRM_DECLINE_BLOCK_REQUEST' => 'Да откажете тази покана за приятелство и да блокирате подателя?',
	'ZEBRAENHANCE_CONFIRM_CANCEL_REQUEST'  => 'Сигурни ли сте, че искате да оттеглите тази покана за приятелство?',
	'ZEBRAENHANCE_ERROR_TITLE'              => 'Грешка при поканата за приятелство',
	'ZEBRAENHANCE_REQUEST_FAILED'           => 'Заявката не можа да бъде изпълнена.',
	'ZEBRAENHANCE_NO_FRIENDS'               => 'В момента нямате добавени приятели.',
	'ZEBRAENHANCE_VIS_EVERYONE'             => 'Всички',
	'ZEBRAENHANCE_VIS_REGISTERED'           => 'Регистрирани потребители',
	'ZEBRAENHANCE_VIS_NON_FOES'             => 'Всички без враговете',
	'ZEBRAENHANCE_VIS_FRIENDS'              => 'Приятели',
	'ZEBRAENHANCE_VIS_CLOSE_FRIENDS'        => 'Близки приятели',
	'ZEBRAENHANCE_VIS_NOBODY'               => 'Никой',
	'ZEBRAENHANCE_FRIEND_REQUEST_NOT_AUTHORIZED' => 'Нямате права да изпращате покани за приятелство.',
	'ZEBRAENHANCE_FRIEND_REQUEST_UNCHANGED'      => 'Не е променена нито една покана за приятелство.',
	'ZEBRAENHANCE_REQUEST_POLICY'                    => 'Кой може да ви изпраща покани за приятелство?',
	'ZEBRAENHANCE_REQUEST_POLICY_EXPLAIN'            => 'Това важи само за нови покани. Поканите, които вече сте изпратили, все още могат да бъдат приети.',
	'ZEBRAENHANCE_REQUEST_POLICY_EVERYONE'           => 'Всички',
	'ZEBRAENHANCE_REQUEST_POLICY_FRIENDS_OF_FRIENDS' => 'Приятели на приятели',
	'ZEBRAENHANCE_REQUEST_POLICY_NOBODY'             => 'Никой',
	'ZEBRAENHANCE_REQUEST_MESSAGE'                   => 'Незадължително съобщение',
	'ZEBRAENHANCE_REQUEST_MESSAGE_PLACEHOLDER'       => 'Добавете кратко лично съобщение (незадължително)',

	'ZEBRAENHANCE_MUTUAL_FRIENDS'        => 'Общи приятели',
	'ZEBRAENHANCE_FRIENDS_SHOWN'          => array(
		1 => 'Показани %2$d от %1$d приятел',
		2 => 'Показани %2$d от %1$d приятели',
	),
	'ZEBRAENHANCE_SHOW_MORE_FRIENDS'      => 'Покажи още',
	'ZEBRAENHANCE_FRIENDS_LOAD_FAILED'    => 'Не можаха да се заредят още приятели.',
	'ZEBRAENHANCE_BLOCK_FOE_PM'                      => 'Блокиране на лични съобщения от врагове',
	'ZEBRAENHANCE_BLOCK_FOE_PM_EXPLAIN'              => 'Когато е включено, потребителите от списъка ви с врагове не могат да ви изпращат лични съобщения. Групово лично съобщение от враг, което включва и вас, се отхвърля за цялата група.',
	'ZEBRAENHANCE_HIDE_FOE_CONTENT'                  => 'Съдържание от врагове',
	'ZEBRAENHANCE_HIDE_FOE_CONTENT_EXPLAIN'          => 'Важи за техните мнения, темите, които са започнали, и цитатите от техни мнения, които могат да бъдат разпознати. Предупреждението държи съдържанието скрито, но все пак можете да го отворите; при мненията това е собственото предупреждение на phpBB. По-старите или ръчно приписаните цитати не винаги могат да бъдат разпознати.',
	'ZEBRAENHANCE_FOE_CONTENT_SHOW'                  => 'Не скривай',
	'ZEBRAENHANCE_FOE_CONTENT_NOTICE'                => 'Скрий зад предупреждение',
	'ZEBRAENHANCE_FOE_CONTENT_REMOVE'                => 'Премахни напълно',
	'ZEBRAENHANCE_FOE_QUOTE_HIDDEN'                  => 'Този цитат е от %s – потребител от списъка ви с врагове.',
	'ZEBRAENHANCE_FOE_QUOTE_HIDDEN_UNKNOWN'          => 'Този цитат е от потребител от списъка ви с врагове.',
	'ZEBRAENHANCE_FOE_QUOTE_SHOW'                    => 'Покажи цитата',
	'ZEBRAENHANCE_FOE_QUOTE_HIDE'                    => 'Скрий цитата',
	'ZEBRAENHANCE_FOE_TOPIC_HIDDEN'                  => 'Тази тема е започната от %s – потребител от списъка ви с врагове.',
	'ZEBRAENHANCE_FOE_TOPIC_SHOW'                    => 'Покажи темата',
	'ZEBRAENHANCE_FOE_TOPIC_HIDE'                    => 'Скрий темата',
	'ZEBRAENHANCE_PM_RECIPIENTS_BLOCKED'             => 'Един или повече получатели не приемат лични съобщения от вас.',
	'ZEBRAENHANCE_FRIEND_SUGGESTIONS'         => 'Хора, които може би познавате',
	'ZEBRAENHANCE_FRIEND_SUGGESTIONS_EXPLAIN' => 'Приятели на приятели, чиито настройки за поверителност и покани позволяват да бъдат предложени.',
	'ZEBRAENHANCE_SEND_FRIEND_REQUEST'        => 'Изпрати покана за приятелство',
	'ZEBRAENHANCE_MUTUAL_FRIEND_COUNT'        => array(
		1 => '%d общ приятел',
		2 => '%d общи приятели',
	),
	'ZEBRAENHANCE_SEARCH_FRIENDS'             => 'Търсене на приятели',
	'ZEBRAENHANCE_SELECT_REQUEST'             => 'Изберете поне една покана за приятелство.',
	'ZEBRAENHANCE_SELECT_REQUEST_ITEM'        => 'Изберете поканата за приятелство с %s',
	'ZEBRAENHANCE_SELECT_ALL'                 => 'Избери всички',
	'ZEBRAENHANCE_ACCEPT_SELECTED'            => 'Приеми избраните',
	'ZEBRAENHANCE_DECLINE_SELECTED'           => 'Откажи избраните',
	'ZEBRAENHANCE_CANCEL_SELECTED'            => 'Оттегли избраните',
	'ZEBRAENHANCE_CONFIRM_DECLINE_SELECTED'   => 'Да откажете избраните покани за приятелство?',
	'ZEBRAENHANCE_CONFIRM_CANCEL_SELECTED'    => 'Да оттеглите избраните покани за приятелство?',
	'ZEBRAENHANCE_BULK_REQUESTS_COMPLETED'    => array(
		1 => '%d покана за приятелство е обработена.',
		2 => '%d покани за приятелство са обработени.',
	),
	'ZEBRAENHANCE_CIRCLES'               => 'Кръгове от приятели',
	'ZEBRAENHANCE_CIRCLES_EXPLAIN'       => 'Създайте лични групи и изберете кои приети приятели принадлежат към всяка от тях.',
	'ZEBRAENHANCE_CIRCLE_NAME'           => 'Име на кръга',
	'ZEBRAENHANCE_CREATE_CIRCLE'         => 'Създай кръг',
	'ZEBRAENHANCE_RENAME_CIRCLE'         => 'Преименувай',
	'ZEBRAENHANCE_DELETE_CIRCLE'         => 'Изтрий',
	'ZEBRAENHANCE_SAVE_CIRCLES'          => 'Запази кръговете',
	'ZEBRAENHANCE_MEMBERS'               => 'членове',
	'ZEBRAENHANCE_CONFIRM_DELETE_CIRCLE' => 'Да изтриете този кръг? Приятелствата няма да бъдат премахнати.',
	'ZEBRAENHANCE_CIRCLE_CREATED'        => 'Кръгът от приятели е създаден.',
	'ZEBRAENHANCE_CIRCLE_RENAMED'        => 'Кръгът от приятели е преименуван.',
	'ZEBRAENHANCE_CIRCLE_DELETED'        => 'Кръгът от приятели е изтрит.',
	'ZEBRAENHANCE_CIRCLES_SAVED'         => 'Кръговете от приятели са запазени.',
	'ZEBRAENHANCE_CIRCLE_INVALID'        => 'Въведете валидно име на кръг с до 50 знака.',
	'ZEBRAENHANCE_CIRCLE_DUPLICATE'      => 'Вече имате кръг с това име.',
	'ZEBRAENHANCE_CIRCLE_LIMIT'          => 'Можете да създадете до 20 кръга от приятели.',
	'ZEBRAENHANCE_CIRCLE_NOT_FOUND'      => 'Този кръг от приятели вече не съществува или не е ваш.',

	'ZEBRAENHANCE_MUTE_FOE_NOTIFICATIONS'         => 'Заглушаване на известия от врагове',
	'ZEBRAENHANCE_MUTE_FOE_NOTIFICATIONS_EXPLAIN' => 'Спира известията за цитати, споменавания, отговори, теми и отметки, създадени от потребители от списъка ви с врагове.',
	'ZEBRAENHANCE_UCP_FOE_MANAGER'                => 'Разширено управление на враговете',
	'ZEBRAENHANCE_UCP_FOE_MANAGER_EXPLAIN'        => 'Търсете сред враговете си, пазете лични бележки, задавайте временни блокирания и променяйте всяка обща защита за отделни потребители.',
	'ZEBRAENHANCE_SEARCH_FOES'                    => 'Търсене на врагове',
	'ZEBRAENHANCE_ADD_FOES'                       => 'Добавяне на врагове',
	'ZEBRAENHANCE_GLOBAL_DEFAULTS'                => 'Общи настройки по подразбиране',
	'ZEBRAENHANCE_PROTECTION_PM'                  => 'Лични съобщения',
	'ZEBRAENHANCE_PROTECTION_CONTENT'             => 'Съдържание',
	'ZEBRAENHANCE_PROTECTION_NOTIFICATIONS'       => 'Известия',
	'ZEBRAENHANCE_FOE_ADDED'                      => 'Добавен',
	'ZEBRAENHANCE_FOE_EXPIRES'                    => 'Изтича',
	'ZEBRAENHANCE_FOE_DATE_UNKNOWN'               => 'Неизвестно',
	'ZEBRAENHANCE_FOE_PERMANENT'                  => 'Постоянно',
	'ZEBRAENHANCE_FOE_NOTE'                       => 'Лична бележка',
	'ZEBRAENHANCE_FOE_NOTE_PLACEHOLDER'           => 'Незадължителна бележка, видима само за вас',
	'ZEBRAENHANCE_FOE_DURATION'                   => 'Продължителност',
	'ZEBRAENHANCE_FOE_DURATION_KEEP'              => 'Запази текущия срок',
	'ZEBRAENHANCE_FOE_24_HOURS'                   => '24 часа',
	'ZEBRAENHANCE_FOE_7_DAYS'                     => '7 дни',
	'ZEBRAENHANCE_FOE_30_DAYS'                    => '30 дни',
	'ZEBRAENHANCE_POLICY_INHERIT'                 => 'Използвай общата настройка',
	'ZEBRAENHANCE_POLICY_ALLOW'                   => 'Разреши',
	'ZEBRAENHANCE_POLICY_BLOCK'                   => 'Блокирай',
	'ZEBRAENHANCE_SELECT_FOE'                     => 'Изберете поне един враг.',
	'ZEBRAENHANCE_SELECT_FOE_ITEM'                => 'Изберете врага %s',
	'ZEBRAENHANCE_REMOVE_SELECTED_FOES'           => 'Премахни избраните',
	'ZEBRAENHANCE_CONFIRM_REMOVE_FOES'            => 'Да премахнете избраните потребители от списъка си с врагове?',
	'ZEBRAENHANCE_NO_FOES'                        => 'В момента нямате добавени врагове.',
	'ZEBRAENHANCE_NO_MATCHING_FOES'               => 'Няма врагове, които отговарят на търсенето ви.',
	'ZEBRAENHANCE_FOE_SAVED'                      => 'Настройките за врага са запазени.',
	'ZEBRAENHANCE_FOE_NOT_FOUND'                  => 'Този потребител вече не е в списъка ви с врагове.',
	'ZEBRAENHANCE_FOE_ERROR_TITLE'                => 'Грешка при управлението на враговете',
	'ZEBRAENHANCE_FOES_REMOVED'                   => array(
		1 => '%d враг е премахнат.',
		2 => '%d врагове са премахнати.',
	),
));
