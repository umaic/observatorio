<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Users Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */
class Controller_Api_Users extends Ushahidi_Rest {

	protected $_action_map = array
	(
		Http_Request::GET
	);

	protected function _scope()
	{
		return 'users';
	}

	/**
	 * Get current user
	 *
	 * GET /api/users/me
	 *
	 * @return void
	 */
	public function action_get_me()
	{
		$this->action_get_index();

		if ($id = service('session.user')->getId()) {
			// Replace the "me" id with the actual id
			$this->_usecase->setIdentifiers(['id' => $id]);
		}
	}

	/**
	 * Get options for /users/me
	 *
	 * @return void
	 */
	public function action_options_me()
	{
		$this->response->status(200);
	}

	/**
	 * Update current user
	 *
	 * PUT /api/users/me
	 *
	 * @return void
	 */
	public function action_put_me()
	{
		$this->action_put_index();

		if ($id = service('session.user')->getId()) {
			// Replace the "me" id with the actual id
			$this->_usecase->setIdentifiers(['id' => $id]);
		}
	}

	/**
	 * Validate user by email
	 *
	 * GET /api/users/validate
	 *
	 * @return void
	 */
	public function action_get_validate()
	{
		$email = $this->request->param('email');
		return $query = DB::select()->from('users')
		->where('users.email', '=', '');
	}
}
