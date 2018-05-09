<?php

/**
 * Ushahidi Config Console Command
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Console
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Console\Command;

use Ushahidi\Core\Usecase;
use Ushahidi\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\TableHelper;

class ApikeySet extends Command
{

	/**
	 * @var Ushahidi\Core\Usecase\Usecase
	 * @todo  support multiple entity types
	 */
	protected $usecase;

	public function setUsecase(Usecase $usecase)
	{
		$this->usecase = $usecase;
	}

	protected function configure()
	{
		$this
			->setName('apikey:set')
			->setDescription('Set apikey')
			;
	}

	// Execution router takes the action argument and uses it to reroute execution.
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$response = $this->usecase->interact();

		// Format the response and output
		$this->handleResponse($response, $output);
	}

	/**
	 * Override response handler to flatten array
	 */
	protected function handleResponse($response, OutputInterface $output, $format = '')
	{
		$iterator = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($response));
		$result = [];
		foreach ($iterator as $leafValue) {
			$keys = [];
			foreach (range(0, $iterator->getDepth()) as $depth) {
				$keys[] = $iterator->getSubIterator($depth)->key();
			}
			$result[ join('.', $keys) ] = $leafValue;
		}
		return parent::handleResponse($result, $output);
	}
}
