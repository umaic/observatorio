<?php

/**
 * Ushahidi Platform Update Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\Repository\EntityGet;

interface UpdateRepository extends EntityGet
{
	/**
	 * @param  Entity $entity
	 * @return void
	 */
	public function update(Entity $entity);
}
