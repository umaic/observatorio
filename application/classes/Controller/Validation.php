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
		$db = new Database;
		$email = $this->request->post('email');
		//$this->response->body('hello, world! ' . $email);;
		$query = $db->query('SELECT `username` FROM `users` where `email` = ?', array($email));
		if($query)
			$this->response->body(json_encode($query));
		else
			$this->response->body('{user:false}');
	}
}
