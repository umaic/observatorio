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
		//General
		$query = DB::select('posts.post_date', 'form.name')->from('posts')
		->join(array('forms', 'form'))
		->on('posts.form_id', '=', 'form.id')
		->where('posts.post_date', '>=', $weekago)
		->order_by('posts.post_date', 'DESC');
		//Categories
		$query2 = DB::select('tag.tag', 'form.name', 'posts.id as post_id', 'tag.id as tag_id')->from('posts')
		->join(array('forms', 'form'))
		->on('posts.form_id', '=', 'form.id')
		->join(array('posts_tags', 'pt'))
		->on('pt.post_id', '=', 'posts.post_id')
		->join(array('tags', 'tag'))
		->on('tag.tag_id', '=', 'pt.tag_id')
		->where('posts.post_date', '>=', $weekago)
		->order_by('posts.post_date', 'DESC');
		$result = $query->execute()->as_array();
		$result2 = $query2->execute()->as_array();
		$last_date = null;
		$last_type = null;
		$count = 0;
		$data = [];
		$totals = [];
		$totals_type = [];
		if(count($result) == 0){
			array_push($totals, [$result[0]['post_date'] => 1]);
		}else{
			foreach($result as $r){
				$date = substr($r['post_date'], 0, 10);
				//Total by date
				if($last_date == null)
					$last_date = $date;
				if($last_date == $date){
					$count++;
				}else{
					array_push($totals, [$last_date => $count]);
					$count = 1;
					$last_date = $date;
				}
			}
			$map = function($result) {return $result['name'];};
			$totals_type = array_count_values(array_map($map, $result));
			//$this->array_sort_by_column($result, 'name');
		}
		$data = [
			'events'=> [
				'total_by_day' => $totals,
				'total_by_type' => $totals_type,
				'total_by_categories' => $result2
			]
		];

		$this->response->headers('Access-Control-Allow-Origin', '*');
		$this->response->headers('Access-Control-Allow-Methods', 'POST, GET, OPTIONS');
		$this->response->headers("Access-Control-Allow-Headers", '*');
		$this->response->headers('Content-Type', 'application/json; charset=utf-8');
		$this->response->body(json_encode($data));
	}

	function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
		$sort_col = array();
		foreach ($arr as $key=> $row) {
			$sort_col[$key] = $row[$col];
		}
	
		array_multisort($sort_col, $dir, $arr);
	}

}
