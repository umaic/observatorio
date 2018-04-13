<?php

/**
 * Ushahidi Platform Update Post Actor Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Post;

interface UpdatePostActorRepository
{
	/**
	 * @param  int $id
	 * @return Ushahidi\Core\Entity\Actor
	 */
	public function get($id);

	/**
	 * @param  string $tag
	 * @return Ushahidi\Core\Entity\Actor
	 */
	public function getByTag($tag);

	/**
	 * @param  string $tag
	 * @return Boolean
	 */
	public function doesActorExist($tag_or_id);
}
