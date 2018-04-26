<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Index Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Controller_Reports extends Controller {

	

	/**
	 * Retrieve a basic information about the API
	 *
	 * GET /api
	 *
	 * @return void
	 */
	public function action_getData()
	{
		$weekago = date('Y-m-d', time() - (7 * 24 * 60 * 60));
		$query = DB::select()->from('posts')
		->join(array('forms', 'form'))
		->on('posts.form_id', '=', 'form.id')
		->where('posts.post_date', '>=', $weekago)
		->orderBy('posts.post_date');
		$result = $query->execute()->as_array();
		$totals = [];
		$last_date = null;
		$count = 0;
		$data = [];
		foreach($result as $r){
			array_push($data, 'Hola...');
			//$this->response->body('Hola...');
			/*if($last_date == null)
				$last_date = $r['post_date'];
			if($last_date == $r['post_date']){
				$count++;
			}else{
				array_push($totals, [$last_date => $count]);
				$count = 1;
				$last_date = $r['post_date'];
			}*/
		}
		/*$data = array();
		array_push($data, ['totals' => $totals]);*/
		$this->response->headers('Access-Control-Allow-Origin', '*');
		$this->response->headers('Access-Control-Allow-Methods', 'POST, GET, OPTIONS');
		$this->response->headers("Access-Control-Allow-Headers", '*');
		//$this->response->headers('Content-Type', 'application/json; charset=utf-8');
		$this->response->body('Hola....');
	}
}
