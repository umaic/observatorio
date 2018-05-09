<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Index Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Controller_VictimsData extends Controller {

	

	/**
	 * Retrieve a basic information about the API
	 *
	 * GET /api
	 *
	 * @return void
	 */
	public function action_getData()
	{
        $data = [
            'victim_age' => DB::select('*')->from('victim_age')->execute()->as_array(),
            'victim_age_group' => DB::select('*')->from('victim_age_group')->execute()->as_array(),
            'victim_condition' => DB::select('*')->from('victim_condition')->execute()->as_array(),
            'victim_ethnic_group' => DB::select('*')->from('victim_ethnic_group')->execute()->as_array(),
            'victim_gender' => DB::select('*')->from('victim_gender')->execute()->as_array(),
            'victim_occupation' => DB::select('*')->from('victim_occupation')->execute()->as_array(),
            'victim_status' => DB::select('*')->from('victim_status')->execute()->as_array(),
            'victim_sub_condition' => DB::select('*')->from('victim_sub_condition')->execute()->as_array(),
            'victim_sub_ethnic_group' => DB::select('*')->from('victim_sub_ethnic_group')->execute()->as_array(),
        ];

		$this->response->headers('Access-Control-Allow-Origin', '*');
		$this->response->headers('Access-Control-Allow-Methods', 'POST, GET, OPTIONS');
		$this->response->headers("Access-Control-Allow-Headers", '*');
		$this->response->headers('Content-Type', 'application/json; charset=utf-8');
		$this->response->body(json_encode($data));
	}


}
