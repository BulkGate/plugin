<?php declare(strict_types=1);

namespace BulkGate\Plugin\Eshop\Test;

require __DIR__ . '/../bootstrap.php';

use Tester\{Assert, TestCase};
use BulkGate\Plugin\Eshop\ConfigurationDefault;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 * @testCase
 */
class ConfigurationDefaultTest extends TestCase
{
	public function testBase(): void
	{
		$configuration = new ConfigurationDefault('https://eshop.bulkgate.com/', 'ws', '1.0.0', 'shop');

		Assert::same('https://eshop.bulkgate.com/', $configuration->url());
		Assert::same('ws', $configuration->product());
		Assert::same('1.0.0', $configuration->version());
		Assert::same('shop', $configuration->name());
	}
}

(new ConfigurationDefaultTest())->run();
