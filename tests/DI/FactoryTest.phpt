<?php declare(strict_types=1);

namespace BulkGate\Plugin\DI\Test;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

use Tester\{Assert, TestCase};
use BulkGate\Plugin\DI\{Factory, Container};

require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
class FactoryTest extends TestCase
{
	public function testFactory(): void
	{
		$factory = new class () implements Factory
		{
			public static function create(array $parameters = []): Container
			{
				return new Container();
			}
		};

		Assert::type(Container::class, $factory->create(['test' => 'test']));
	}
}

(new FactoryTest())->run();
