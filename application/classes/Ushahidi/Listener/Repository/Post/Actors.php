<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Post Relation Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Usecase\Post\UpdatePostActorRepository;

class Ushahidi_Repository_Post_Actors extends Ushahidi_Repository_Post_Value
{
	protected $actor_repo;

	/**
	 * Construct
	 * @param Database              $db
	 * @param ActorRepo               $actor_repo
	 */
	public function __construct(
			Database $db,
			UpdatePostActorRepository $actor_repo
		)
	{
		parent::__construct($db);
		$this->actor_repo = $actor_repo;
	}

	// Ushahidi_Repository
	protected function getTable()
	{
		return 'posts_actors';
	}

	// Override selectQuery to fetch attribute 'key' too
	protected function selectQuery(Array $where = [])
	{
		$query = parent::selectQuery($where);

		// Select 'actor_id' as value too
		$query->select(
				['posts_actors.actor_id', 'value']
			);

		return $query;
	}

	// PostValueRepository
	public function getValueQuery($form_attribute_id, array $matches)
	{
		$query = $this->selectQuery(compact('form_attribute_id'))
			->and_where_open();

		foreach ($matches as $match) {
			$query->or_where('actor_id', 'LIKE', "%$match%");
		}

		$query->and_where_close();

		return $query;
	}

	// UpdatePostValueRepository
	public function createValue($value, $form_attribute_id, $post_id)
	{
		$actor_id = $this->parseActor($value);
		$input = compact('actor_id', 'form_attribute_id', 'post_id');
		$input['created'] = time();

		return $this->executeInsert($input);
	}

	// UpdatePostValueRepository
	public function updateValue($id, $value)
	{
		$actor_id = $this->parseActor($value);
		$update = compact($actor_id);
		if ($id && $update)
		{
			$this->executeUpdate(compact('id'), $update);
		}
	}

	protected function parseActor($actor)
	{
		if (is_array($actor)) {
			$actor = $actor['id'];
		}

		// Find the actor by id or name
		// @todo this should happen before we even get here
		$actor_entity = $this->actor_repo->getByTag($actor);
		if (! $actor_entity->id)
		{
			$actor_entity = $this->actor_repo->get($actor);
		}

		return $actor_entity->id;
	}

}
