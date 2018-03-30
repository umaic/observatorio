<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi API Actors Controller
 *
 * @author     Kuery team <saudade@kuery.com.co>
 * @package    Ushahidi\Application\Controllers
 * @copyright  2018 Kuery
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

class Controller_Api_Actors extends Ushahidi_Rest {

	protected function _scope()
	{
		return 'actors';
	}
}
