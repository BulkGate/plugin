<?php declare(strict_types=1);

namespace BulkGate\Plugin\Event;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

use BulkGate\Plugin\Strict;

class Loader
{
	use Strict;

	/**
	 * @var list<DataLoader>
	 */
	private array $loaders;


	/**
	 * @param list<DataLoader> $loaders
	 */
	public function __construct(array $loaders)
	{
		$this->loaders = $loaders;
	}


	public function load(Variables $variables): void
	{
		foreach ($this->loaders as $loader)
		{
			$loader->load($variables);
		}
	}
}
