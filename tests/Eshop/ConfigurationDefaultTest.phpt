<?php declare(strict_types=1);

namespace BulkGate\Plugin\Eshop\Test;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

use Tester\{Assert, TestCase};
use BulkGate\Plugin\Eshop\ConfigurationDefault;

require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
class ConfigurationDefaultTest extends TestCase
{
	public function testBase(): void
	{
		$configuration = new ConfigurationDefault('https://eshop.bulkgate.com/', 'ws', '1.0.0');

		Assert::same('https://eshop.bulkgate.com/', $configuration->url());
		Assert::same('ws', $configuration->product());
		Assert::same('1.0.0', $configuration->version());

	}
}

(new ConfigurationDefaultTest())->run();
