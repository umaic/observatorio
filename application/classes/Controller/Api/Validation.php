<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Index Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Controller_Api_Validation extends Ushahidi_Rest {

	/**
	 * @var array Map of HTTP methods -> actions
	 */
	protected $_action_map = array
	(
		Http_Request::GET     => 'get',
		Http_Request::OPTIONS => 'options'
	);

	protected function _scope()
	{
		return null;
	}

	protected function _is_auth_required()
	{
		return FALSE;
	}

	/**
	 * Retrieve a basic information about the API
	 *
	 * GET /api
	 *
	 * @return void
	 */
	public function action_validate()
	{
		$email = $this->request->param('email');
		return $query = DB::select()->from('users')
		->where('users.email', '=', '');
	}
}
