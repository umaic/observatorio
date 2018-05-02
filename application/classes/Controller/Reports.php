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
		->where('posts.status', '=', 'published')
		->order_by('posts.post_date', 'DESC');
		//Categories
		$query2 = DB::select('tag.tag', 'form.name', 'posts.id')->from('posts')
		->join(array('forms', 'form'))
		->on('posts.form_id', '=', 'form.id')
		->join(array('posts_tags', 'pt'))
		->on('pt.post_id', '=', 'posts.id')
		->join(array('tags', 'tag'))
		->on('tag.id', '=', 'pt.tag_id')
		->where('posts.post_date', '>=', $weekago)
		->where('posts.status', '=', 'published')
		->order_by('posts.post_date', 'DESC');
		//Totales
		$query3 = DB::select('v.amount', 'vc.condition', 'veg.ethnic_group', 'vg.gender')
		->from(array('victims', 'v'))
		->join(array('posts', 'p'))
		->on('v.post_id', '=', 'p.id')
		->join(array('victim_condition', 'vc'))
		->on('vc.id', '=', 'v.id_condition')
		->join(array('victim_ethnic_group', 'veg'))
		->on('veg.id', '=', 'v.id_ethnic_group')
		->join(array('victim_gender', 'vg'))
		->on('vg.id', '=', 'v.id_gender')
		->where('p.post_date', '>=', $weekago)
		->where('p.status', '=', 'published');

		$result = $query->execute()->as_array();
		$result2 = $query2->execute()->as_array();
		$victims_count = $query3->execute()->as_array();
		$last_date = null;
		$last_type = null;
		$count = 0;
		$data = [];
		$totals = [];
		$totals_type = [];
		$total_categories = [];
		if(count($result) == 0){
			array_push($totals, [$result[0]['post_date'] => 1]);
		}else{
			$map = function($result) {return $result['name'];};
			$totals_type = array_count_values(array_map($map, $result));
			foreach($result as $r){
				$date = substr($r['post_date'], 0, 10);
				$name = strtolower(str_replace(" ","_",$r['name']));
				if(!isset($totals[$name]))
					$totals[$name] = []; 
				if(!isset($totals[$name][$date]))
					$totals[$name][$date] = 0; 
				$totals[$name][$date] += 1;
			}
			foreach($totals_type as $key=>$value) {
				foreach($result2 as $r2){
					if($r2['name'] == $key){
						$name = strtolower(str_replace(" ","_",$key));
						if(!isset($total_categories[$name]))
							$total_categories[$name] = []; 
						if(!isset($total_categories[$name][$r2['tag']]))
							$total_categories[$name][$r2['tag']] = 0; 
						$total_categories[$name][$r2['tag']] += 1;
					}
				}
			}
			$map = function($result) {return substr($result['post_date'], 0, 10);};
			$dates = array_count_values(array_map($map, $result));
			ksort($dates);
		}
		$civils = 0;
		$afros = 0;
		$menores = 0;
		$hombres = 0;
		$mujeres = 0;
		$indigenas = 0;
		foreach($victims_count as $v) {
			$civils += $v['condition'] == 'civil' ? $v['amount'] : 0;
			$afros += $v['ethnic_group'] == 'afro' ? $v['amount'] : 0;
			$indigenas += $v['ethnic_group'] == 'indigena' ? $v['amount'] : 0;
			$menores += $v['ethnic_group'] == 'menores' ? $v['amount'] : 0;
			$hombres += $v['gender'] == 'masculino' ? $v['amount'] : 0;
			$mujeres += $v['gender'] == 'femenino' ? $v['amount'] : 0;
		}
		$totals_v = [
			'civiles' => $civils,
			'afros' => $afros,
			'indigenas' => $indigenas,
			'menores' => $menores,
			'hombres' => $hombres,
			'mujeres' => $mujeres
		];
		$data = [
			'events'=> [
				'total_by_day' => $totals,
				'total_by_type' => $totals_type,
				'total_by_categories' => $total_categories,
				'dates' => $dates,
				'victims_count' => $totals_v
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
