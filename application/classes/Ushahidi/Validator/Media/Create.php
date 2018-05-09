<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Media Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Entity;
use Ushahidi\Core\Tool\Validator;

class Ushahidi_Validator_Media_Create extends Validator
{
	protected $max_bytes = 0;
	protected $default_error_source = 'media';

	public function setMaxBytes($max_bytes)
	{
		$this->max_bytes = $max_bytes;
	}

	protected function getRules()
	{
		return [
			'user_id' => [
				['digit'],
			],
			'caption' => [
				// alphas, numbers, punctuation, and spaces
				['regex', [':value', '/^[\pL\pN\pP ]++$/uD']],
			],
			'mime' => [
				['not_empty'],
				[[$this, 'validateMime'], [':validation', ':value']],
			],
			'o_filename' => [
				['not_empty']
			],
			'o_size' => [
				['not_empty'],
				[[$this, 'validateSize'], [':validation', ':value']],
			],
			'o_width' => [
				['numeric'],
			],
			'o_height' => [
				['numeric'],
			],
		];
	}

	public function validateSize($validation, $value)
	{
		if ($value <= 0 || $value > $this->max_bytes)
		{
			$size_in_mb = ($this->max_bytes / 1024) / 1024;
			$validation->error('o_size','size_error', [$size_in_mb]);
		}
	}

	public function validateMime($validation, $mime)
	{
		$allowed_mime_types = [
			'image/gif', 'image/jpg', 'image/jpeg', 'image/png'
		];

		if (!$mime) {
			$validation->error('mime', 'mime_not_empty');
		} else if (!in_array($mime, $allowed_mime_types)) {
			$validation->error('mime', 'mime_type_not_allowed');
		}
	}
}
