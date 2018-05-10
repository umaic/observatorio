<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Index Controller
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */
class Controller_Reports extends Controller
{


    /**
     * Retrieve a basic information about the API
     *
     * GET /api
     *
     * @return void
     */
     public function action_getData()
        {
            $ids = [];
            if (count($_POST) > 0)
                $ids = explode(",", $_POST['ids']);
            $weekago = date('Y-m-d', time() - (7 * 24 * 60 * 60));
            //General
            if (count($ids) == 0) {
                $query = DB::select('posts.form_id', 'posts.post_date', 'form.name')->from('posts')
                    ->join(array('forms', 'form'))
                    ->on('posts.form_id', '=', 'form.id')
                    ->where('posts.post_date', '>=', $weekago)
                    ->where('posts.status', '=', 'published')
                    ->order_by('posts.post_date', 'DESC');
                //Categories
                $query2 = DB::select('tag.tag', 'form.name', 'posts.id', 'posts.form_id')->from('posts')
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
                $query3 = DB::select('v.amount', 'vc.condition', 'veg.ethnic_group', 'vg.gender', 'p.form_id')
                    ->from(array('victims', 'v'))
                    ->join(array('posts', 'p'))
                    ->on('v.post_id', '=', 'p.id')
                    ->join(array('victim_condition', 'vc'), 'LEFT')
                    ->on('vc.id', '=', 'v.id_condition')
                    ->join(array('victim_ethnic_group', 'veg'), 'LEFT')
                    ->on('veg.id', '=', 'v.id_ethnic_group')
                    ->join(array('victim_gender', 'vg'), 'LEFT')
                    ->on('vg.id', '=', 'v.id_gender')
                    ->where('p.post_date', '>=', $weekago)
                    ->where('p.status', '=', 'published');
            } else {
                $query = DB::select('posts.form_id', 'posts.post_date', 'form.name')->from('posts')
                    ->join(array('forms', 'form'))
                    ->on('posts.form_id', '=', 'form.id')
                    ->where('posts.id', 'in', $ids)
                    ->order_by('posts.post_date', 'DESC');
                //Categories
                $query2 = DB::select('tag.tag', 'form.name', 'posts.id', 'posts.form_id')->from('posts')
                    ->join(array('forms', 'form'))
                    ->on('posts.form_id', '=', 'form.id')
                    ->join(array('posts_tags', 'pt'))
                    ->on('pt.post_id', '=', 'posts.id')
                    ->join(array('tags', 'tag'))
                    ->on('tag.id', '=', 'pt.tag_id')
                    ->where('posts.id', 'in', $ids)
                    ->order_by('posts.post_date', 'DESC');
                //Totales
                $query3 = DB::select('v.amount', 'vc.condition', 'veg.ethnic_group', 'vg.gender', 'p.form_id')
                    ->from(array('victims', 'v'))
                    ->join(array('posts', 'p'))
                    ->on('v.post_id', '=', 'p.id')
                    ->join(array('victim_condition', 'vc'), 'LEFT')
                    ->on('vc.id', '=', 'v.id_condition')
                    ->join(array('victim_ethnic_group', 'veg'), 'LEFT')
                    ->on('veg.id', '=', 'v.id_ethnic_group')
                    ->join(array('victim_gender', 'vg'), 'LEFT')
                    ->on('vg.id', '=', 'v.id_gender')
                    ->where('p.id', 'in', $ids);
            }
            $queryForms = DB::select('*')->from('forms')->where('forms.disabled', '=', 0);
            $forms = $queryForms->execute()->as_array();
            $result = $query->execute()->as_array();
            $result2 = $query2->execute()->as_array();
            $victims_count = $query3->execute()->as_array();
            $last_date = null;
            $last_type = null;
            $count = 0;
            $data = [];
            $dates = [];
            $totals = [];
            $totals_type = [];
            $total_categories = [];
            foreach ($forms as $key => $value) {
                $queryPosts = DB::select('*')->from('posts')->where('status', '=', 'published')->where('form_id', '=', $value['id']);
                $forms[$key]['categories'] = [];
                $forms[$key]['victims'] = [];
                $forms[$key]['total_posts'] = count($queryPosts->execute()->as_array());
            }
            if (count($result) == 0) {
                $totals_v = [
                    'civiles' => 0,
                    'afros' => 0,
                    'indigenas' => 0,
                    'menores' => 0,
                    'hombres' => 0,
                    'mujeres' => 0,
                    'extranjero' => 0,
                    'no_info' => 0,
                    'otro' => 0,
                    'desconocido' => 0
                ];
                $totals = [
                    "violencia_armada" => ["" => 0],
                    "desastres" => ["" => 0]
                ];
                $totals_type = [
                    "Violencia Armada" => 0,
                    "Desastres" => 0
                ];
            } else {
                $map = function ($result) {
                    return $result['name'];
                };
                $totals_type = array_count_values(array_map($map, $result));
                foreach ($result as $r) {
                    $date = substr($r['post_date'], 0, 10);
                    $name = strtolower(str_replace(" ", "_", $r['name']));
                    if (!isset($totals[$name]))
                        $totals[$name] = [];
                    if (!isset($totals[$name][$date]))
                        $totals[$name][$date] = 0;
                    $totals[$name][$date] += 1;
                }
                foreach ($forms as $key => $value) {
                    foreach ($result2 as $r2) {
                        if ($r2['form_id'] == $value['id']) {
                            if (!isset($forms[$key]['categories'][$r2['tag']]))
                                $forms[$key]['categories'][$r2['tag']] = 0;
                            $forms[$key]['categories'][$r2['tag']] += 1;
                        }
                    }
                    foreach ($victims_count as $vc) {
                        if ($vc['amount'] && $vc['form_id'] == $value['id']) {
                            if (!isset($forms[$key]['victims'][$vc['ethnic_group']]))
                                $forms[$key]['victims'][$vc['ethnic_group']] = 0;
                            $forms[$key]['victims'][$vc['ethnic_group']] += intval($vc['amount']);
                            if (!isset($forms[$key]['victims'][$vc['gender']]))
                                $forms[$key]['victims'][$vc['gender']] = 0;
                            $forms[$key]['victims'][$vc['gender']] += intval($vc['amount']);
                        }
                    }
                    foreach ($result as $r) {
                        if ($r['form_id'] == $value['id']) {
                            $date = substr($r['post_date'], 0, 10);
                            if (!isset($forms[$key]['totals'][$date]))
                                $forms[$key]['totals'][$date] = 0;
                            $forms[$key]['totals'][$date] += 1;
                        }
                    }
                }

                foreach ($totals_type as $key => $value) {
                    foreach ($result2 as $r2) {
                        if ($r2['name'] == $key) {
                            $name = strtolower(str_replace(" ", "_", $key));
                            if (!isset($total_categories[$name]))
                                $total_categories[$name] = [];
                            if (!isset($total_categories[$name][$r2['tag']]))
                                $total_categories[$name][$r2['tag']] = 0;
                            $total_categories[$name][$r2['tag']] += 1;
                        }
                    }
                }
                $map = function ($result) {
                    return substr($result['post_date'], 0, 10);
                };
                $dates = array_count_values(array_map($map, $result));
                ksort($dates);
            }
            $civils = 0;
            $afros = 0;
            $menores = 0;
            $hombres = 0;
            $mujeres = 0;
            $indigenas = 0;
            $extranjero = 0;
            $no_info = 0;
            $otro = 0;
            $desconocido = 0;
            foreach ($victims_count as $v) {
                $civils += $v['condition'] == 'civil' ? $v['amount'] : 0;
                $afros += $v['ethnic_group'] == 'afro' ? $v['amount'] : 0;
                $indigenas += $v['ethnic_group'] == 'indigena' ? $v['amount'] : 0;
                $menores += $v['ethnic_group'] == 'menores' ? $v['amount'] : 0;
                $hombres += $v['gender'] == 'masculino' ? $v['amount'] : 0;
                $mujeres += $v['gender'] == 'femenino' ? $v['amount'] : 0;
                $extranjero += $v['ethnic_group'] == 'extranjero' ? $v['amount'] : 0;
                $no_info += $v['ethnic_group'] == 'no_info' ? $v['amount'] : 0;
                $otro += $v['ethnic_group'] == 'otro' ? $v['amount'] : 0;
                $desconocido += $v['gender'] == 'desconocido' ? $v['amount'] : 0;
            }
            $totals_v = [
                'civiles' => $civils,
                'afros' => $afros,
                'indigenas' => $indigenas,
                'menores' => $menores,
                'hombres' => $hombres,
                'mujeres' => $mujeres,
                'extranjero' => $extranjero,
                'no_info' => $no_info,
                'otro' => $otro,
                'desconocido' => $desconocido
            ];
            $data = [
                'events' => [
                    'total_by_day' => $totals,
                    'total_by_type' => $totals_type,
                    'total_by_categories' => $total_categories,
                    'dates' => $dates,
                    'forms' => $forms,
                    'victims_count' => $totals_v
                ]
            ];

            $this->response->headers('Access-Control-Allow-Origin', '*');
            $this->response->headers('Access-Control-Allow-Methods', 'POST, GET, OPTIONS');
            $this->response->headers("Access-Control-Allow-Headers", '*');
            $this->response->headers('Content-Type', 'application/json; charset=utf-8');
            $this->response->body(json_encode($data));
        }

    function array_sort_by_column(&$arr, $col, $dir = SORT_ASC)
    {
        $sort_col = array();
        foreach ($arr as $key => $row) {
            $sort_col[$key] = $row[$col];
        }

        array_multisort($sort_col, $dir, $arr);
    }

	public function action_getDataTest()
	{
		$query = DB::select(
			'posts.id',
			'form.name', 
			'posts.title',
			'posts.slug',
			'posts.content',
			'posts.status',
			'posts.post_date', 
			'v.amount', 
			'vc.condition', 
			'veg.ethnic_group', 
			'vg.gender',
			'vseg.sub_ethnic_group',
			'vsc.sub_condition',
			'vo.occupation',
			'va.age',
			'vag.age_group',
			'vs.status',
			'tag.tag',
			'ac.tag',
			'psd.event_desc',
			'psd.event_url',
			'psd.event_date'
		)->from('posts')
		->join('forms', 'LEFT')
		->on('posts.form_id', '=', 'forms.id')
		->join('victims', 'LEFT')
		->on('post.id', '=', 'victims.post_id')
		->join('victims', 'LEFT')
		->on('victims.posts_id', '=', 'posts.id')
		->join('victim_condition', 'LEFT')
		->on('victim_condition.id', '=', 'victims.id_condition')
		->join('victim_ethnic_group', 'LEFT')
		->on('victim_ethnic_group.id', '=', 'victims.id_ethnic_group')
		->join('victim_gender', 'LEFT')
		->on('victim_gender.id', '=', 'victims.id_gender')
		->join('victim_sub_ethnic_group', 'LEFT')
		->on('victim_sub_ethnic_group.id', '=', 'victims.id_sub_ethnic_group')
		->join('victim_sub_condition', 'LEFT')
		->on('victim_sub_condition.id', '=', 'victims.id_sub_condition')
		->join('victim_occupation', 'LEFT')
		->on('victim_occupation.id', '=', 'victims.id_occupation')
		->join('victim_age', 'LEFT')
		->on('victim_age.id', '=', 'victims.id_age')
		->join('victim_age_group', 'LEFT')
		->on('victim_age_group.id', '=', 'victims.id_age_group')
		->join('victim_status', 'LEFT')
		->on('victim_status.id', '=', 'victims.id_status')
		->join('tags', 'LEFT')
		->on('tags.id', '=', 'victims.tag_id')
		->join('post_tag_actor', 'LEFT')
		->on('post_tag_actor.post_id', '=', 'posts.id')
		->join('actors', 'LEFT')
		->on('actors.id', '=', 'pta.actor_id')
		->join('post_source_detail', 'LEFT')
		->on('post_source_detail.post.id', '=', 'posts.id');
		$this->response->headers('Access-Control-Allow-Origin', '*');
		$this->response->headers('Access-Control-Allow-Methods', 'POST, GET, OPTIONS');
		$this->response->headers("Access-Control-Allow-Headers", '*');
		$this->response->headers('Content-Type', 'application/json; charset=utf-8');
		$this->response->body(json_encode($query->execute()->as_array()));
	}

}
