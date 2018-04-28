<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Post Relation Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Usecase\Post\UpdatePostSourceRepository;

class Ushahidi_Repository_Post_Sources extends Ushahidi_Repository_Post_Value
{
	protected $source_repo;

	/**
	 * Construct
	 * @param Database              $db
	 * @param SourceRepo               $source_repo
	 */
	public function __construct(
			Database $db,
			UpdatePostSourceRepository $source_repo
		)
	{
		parent::__construct($db);
		$this->source_repo = $source_repo;
	}

	// Ushahidi_Repository
	protected function getTable()
	{
		return 'posts_sources';
	}

	// Override selectQuery to fetch attribute 'key' too
	protected function selectQuery(Array $where = [])
	{
		$query = parent::selectQuery($where);

		// Select 'source_id' as value too
		$query->select(
				['posts_sources.source_id', 'value']
			);

		return $query;
	}

	// PostValueRepository
	public function getValueQuery($form_attribute_id, array $matches)
	{
		$query = $this->selectQuery(compact('form_attribute_id'))
			->and_where_open();

		foreach ($matches as $match) {
			$query->or_where('source_id', 'LIKE', "%$match%");
		}

		$query->and_where_close();

		return $query;
	}

	// UpdatePostValueRepository
	public function createValue($value, $form_attribute_id, $post_id)
	{
		$source_id = $this->parseSource($value);
		$input = compact('source_id', 'form_attribute_id', 'post_id');
		$input['created'] = time();

		return $this->executeInsert($input);
	}

	// UpdatePostValueRepository
	public function updateValue($id, $value)
	{
		$source_id = $this->parseSource($value);
		$update = compact($source_id);
		if ($id && $update)
		{
			$this->executeUpdate(compact('id'), $update);
		}
	}

	protected function parseSource($source)
	{
		if (is_array($source)) {
			$source = $source['id'];
		}

		// Find the source by id or name
		// @todo this should happen before we even get here
		$source_entity = $this->source_repo->getByTag($source);
		if (! $source_entity->id)
		{
			$source_entity = $this->source_repo->get($source);
		}

		return $source_entity->id;
	}

}
