<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Users Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */
class Controller_Api_Validation extends Ushahidi_Rest {

	protected $_action_map = array
	(
		Http_Request::GET     => 'get',
	);

	protected function _is_auth_required()
	{
		return FALSE;
	}

	protected function _scope()
	{
		return 'users';
	}

	
	/**
	 * Validate user by email
	 *
	 * GET /api/users/validate
	 *
	 * @return void
	 */
	public function action_index()
	{
		$email = $this->request->param('email');
		return $query = DB::select()->from('users')
		->where('users.email', '=', '');
	}
}
