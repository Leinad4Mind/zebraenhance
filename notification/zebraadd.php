<?php
/**
*
* @package Zebra Enhance Extension
* @copyright (c) 2014 Lucifer
* @copyright (c) 2026 Leinad4Mind
* @license GNU General Public License, version 2 (GPL-2.0-only)
*
*/

namespace anavaro\zebraenhance\notification;

/**
* Friend request notification.
*
* @package notifications
*/
class zebraadd extends \phpbb\notification\type\base
{
	/**
	* Get notification type name
	*
	* @return string
	*/
	public function get_type()
	{
		return 'anavaro.zebraenhance.notification.zebraadd';
	}

	/**
	 * Notification option data (for outputting to the user)
	 *
	 * @var bool|array False if the service should use it's default data
	 * 					Array of data (including keys 'id', 'lang', and 'group')
	 */
	public static $notification_option = array(
		'lang'	=> 'NOTIFICATION_TYPE_ZEBRA_ADD',
	);

	/** @var \phpbb\user_loader */
	protected $user_loader;

	public function set_user_loader(\phpbb\user_loader $user_loader)
	{
		$this->user_loader = $user_loader;
	}

	/**
	 * Is available
	 */
	public function is_available()
	{
		return $this->auth->acl_get('u_ze_use');
	}

	/**
	 * Get the id of the
	 *
	 * @param array $pm The data from the private message
	 * @return int
	 */
	public static function get_item_id($data)
	{
		return isset($data['request_id']) ? (int) $data['request_id'] : 0;
	}

	/**
	 * Get the id of the parent
	 *
	 * @param array $pm The data from the pm
	 * @return int
	 */
	public static function get_item_parent_id($data)
	{
		return isset($data['requester_id']) ? (int) $data['requester_id'] : 0;
	}

	/**
	 * Find the users who want to receive notifications
	 *
	 * @param array $data Data from submit_pm
	 * @param array $options Options for finding users for notification
	 *
	 * @return array
	 */
	public function find_users_for_notification($data, $options = array())
	{

		$this->user_loader->load_users(array_keys($data['user_id']));

		return $this->check_user_notification_options(array_keys($data['user_id']), $options);
	}

	/**
	 * Get the user's avatar
	 */
	public function get_avatar()
	{
		$requester_id = (int) $this->get_data('requester_id');
		return $requester_id ? $this->user_loader->get_avatar($requester_id, false, true) : '';
	}

	/**
	 * Get the HTML formatted title of this notification
	 *
	 * @return string
	 */
	public function get_title()
	{
		$requester_id = (int) $this->get_data('requester_id');
		$username = $requester_id ? $this->user_loader->get_username($requester_id, 'no_profile') : $this->language->lang('GUEST');

		return $this->language->lang('NOTIFICATION_ZEBRA_ADD', $username);
	}

	/**
	 * Get email template
	 *
	 * @return string|bool
	 */
	public function get_email_template()
	{
		return false;
	}

	/**
	 * Get email template variables
	 *
	 * @return array
	 */
	public function get_email_template_variables()
	{
		return array();
	}

	/**
	 * Get the url to this item
	 *
	 * @return string URL
	 */
	public function get_url()
	{
		return append_sid($this->phpbb_root_path . 'ucp.' . $this->php_ext, 'i=ucp_zebra&mode=friends');
	}

	/**
	 * Users needed to query before this notification can be displayed
	 *
	 * @return array Array of user_ids
	 */
	public function users_to_query()
	{
		$requester_id = (int) $this->get_data('requester_id');
		return $requester_id ? array($requester_id) : array();
	}

	/**
	 * {@inheritdoc}
	 */
	public function create_insert_array($data, $pre_create_data = array())
	{

		$this->set_data('requester_id', isset($data['requester_id']) ? (int) $data['requester_id'] : 0);
		parent::create_insert_array($data, $pre_create_data);
	}
}
