<?php declare(strict_types=1);

namespace BulkGate\Plugin\DI\Test;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

use Tester\{Assert, TestCase};
use BulkGate\Plugin\DI\{Factory, Container, FactoryStatic};

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
			public static array $parameters = [];

			public static function create(array $parameters = []): Container
			{
				return new Container();
			}

			public static function setup(callable $callback): void
			{
				self::$parameters = $callback();
			}

			public static function get(): Container
			{
				return new Container();
			}
		};

		$factory::setup(fn (): array => ['test' => 'test']);

		Assert::same(['test' => 'test'], $factory::$parameters);

		Assert::type(Container::class, $factory->get());
	}


	public function testFactoryStatic(): void
	{
		$factory = new class ($container = new Container()) implements Factory
		{
			use FactoryStatic;

			private static Container $_container;

			public function __construct(Container $container)
			{
				self::$_container = $container;
			}

			protected static function createContainer(array $parameters = []): Container
			{
				Assert::same(['test' => 'test'], $parameters);

				return self::$_container;
			}
		};

		$factory::setup(fn (): array => ['test' => 'test']);

		Assert::same($container, $factory::get());
	}
}

(new FactoryTest())->run();
