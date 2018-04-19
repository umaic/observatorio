<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Index Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Controller_Validation extends Ushahidi_Rest {

	public function before()
	{
		if ($this->request->method() == HTTP_Request::GET){
			$this->request->action('validate');
		}
		parent::before();
	}

	public function after()
	{
		$this->add_cors_headers($this->response);
		parent::after();
	}

	protected function _scope()
	{
		return null;
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
		$query = DB::select()->from('users')
		->where('users.email', '=', '');
		$this->response->body(json_encode($query));
	}
}
