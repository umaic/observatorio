<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Source Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity;
use Ushahidi\Core\SearchData;
use Ushahidi\Core\Entity\Source;
use Ushahidi\Core\Entity\SourceRepository;
use Ushahidi\Core\Usecase\Source\UpdateSourceRepository;
use Ushahidi\Core\Usecase\Source\DeleteSourceRepository;
use Ushahidi\Core\Usecase\Post\UpdatePostSourceRepository;

class Ushahidi_Repository_Source extends Ushahidi_Repository implements
	UpdateSourceRepository,
	DeleteSourceRepository,
	UpdatePostSourceRepository,
	SourceRepository
{
	// Use the JSON transcoder to encode properties
	use Ushahidi_JsonTranscodeRepository;
	// Use trait to for updating forms_sources-table
	use Ushahidi_FormsSourcesTrait;
	private $created_id;
	private $created_ts;

	private $deleted_source;

	// Ushahidi_Repository
	protected function getTable()
	{
		return 'sources';
	}

	// CreateRepository
	// ReadRepository
	public function getEntity(Array $data = null)
	{
		if (!empty($data['id'])) {
			// If this is a top level category
			if (empty($data['parent_id'])) {
				// Load children
				$data['children'] = DB::select('id')
					->from('sources')
					->where('parent_id', '=', $data['id'])
					->execute($this->db)
					->as_array(null, 'id');
			}
		}

		return new Source($data);
	}

	// Ushahidi_JsonTranscodeRepository
	protected function getJsonProperties()
	{
		return ['role'];
	}

	// SearchRepository
	public function getSearchFields()
	{
		return ['tag', 'type', 'parent_id', 'q', 'level' /* LIKE tag */];
	}

	// Ushahidi_Repository
	protected function setSearchConditions(SearchData $search)
	{
		$query = $this->search_query;
		foreach (['tag', 'type', 'parent_id'] as $key) {
			if ($search->$key) {
				$query->where($key, '=', $search->$key);
			}
		}

		if ($search->q) {
			// Source text searching
			$query->where('tag', 'LIKE', "%{$search->q}%");
		}

		if ($search->level) {
			//searching for top-level-sources
			if ($search->level === 'parent') {
				$query->where('parent_id', '=', null);
			}
		}
	}

	// SearchRepository
	public function getSearchResults()
	{
		$query = $this->getSearchQuery();
		$results = $query->distinct(TRUE)->execute($this->db);
		return $this->getCollection($results->as_array());
	}

	// CreateRepository
	public function create(Entity $entity)
	{
		$record = $entity->asArray();
		$record['created'] = time();

		$id = $this->executeInsert($this->removeNullValues($record));

		return $id;
	}

	public function update(Entity $entity)
	{
		$source = $entity->getChanged();
		// removing children before saving source
		unset($source['children']);
		$count = $this->executeUpdate(['id' => $entity->id], $source);

		return $count;
	}

	// UpdatePostSourceRepository
	public function getByTag($tag)
	{
		return $this->getEntity($this->selectOne(compact('tag')));
	}

	// UpdatePostSourceRepository
	public function doesSourceExist($tag_or_id)
	{
		$query = $this->selectQuery()
			->resetSelect()
			->select([DB::expr('COUNT(*)'), 'total'])
			->where('id', '=', $tag_or_id)
			->or_where('tag', '=', $tag_or_id)
			->execute($this->db);

		return $query->get('total') > 0;
	}

	// UpdateSourceRepository
	public function isSlugAvailable($slug)
	{
		return $this->selectCount(compact('slug')) === 0;
	}

	public function delete(Entity $entity)
	{
		// Remove source from attribute options
		$this->removeSourceFromAttributeOptions($entity->id);

		return $this->executeDelete([
			'id' => $entity->id
		]);
	}

	// DeleteSourceRepository
	public function deleteSource($id)
	{
		// Remove source from attribute options
		$this->removeSourceFromAttributeOptions($id);
		return $this->delete(compact('id'));
	}

	/**
	 * Checks if the assigned role is valid for this source.
	 * True if there is no role or if it's a parent with no children
	 * @param Validation $validation
	 * @param $fullData
	 * @return bool
	 */
	public function isRoleValid(Validation $validation, $fullData)
	{
		$valid = true;
		$entityFullData = $this->getEntity($fullData);
		$isChild = !!$entityFullData->parent_id;
		$hasRole = !!$entityFullData->role;
		$parent = $isChild ? $this->selectOne(['id' => $entityFullData->parent_id]) : null;
		if ($hasRole && $isChild && $parent) {
			$parent = $this->getEntity($parent);
			$valid = $parent->role == $entityFullData->role;
		}
		if (!$valid) {
			$validation->error('role', 'source.role');
		}
		return $valid;
	}
}
