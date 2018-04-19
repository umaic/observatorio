<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Index Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Controller_Validation extends Controller {

	

	/**
	 * Retrieve a basic information about the API
	 *
	 * GET /api
	 *
	 * @return void
	 */
	public function action_validate()
	{
		$email = $this->request->post('email');
		//$this->response->body('hello, world! ' . $email);;
		$query = DB::select()->from('users')
		->where('users.email', '=', $email);
		$result = $query->execute();
		$this->response->body($result);
		/*if($results)
			$this->response->body(json_encode($query));
		else
			$this->response->body('{user:false}');*/
	}
}
