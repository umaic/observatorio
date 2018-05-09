<?php

/**
 * Ushahidi Platform Admin Read Source Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Source;

interface ReadSourceRepository
{
	/**
	 * @param  int $id
	 * @return Ushahidi\Core\Entity\Source
	 */
	public function get($id);
}
