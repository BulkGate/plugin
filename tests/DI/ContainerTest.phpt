<?php declare(strict_types=1);

namespace BulkGate\Plugin\DI\Test;

require __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/Assets/TestClass.php';

use DateTime;
use Tester\{Assert, TestCase};
use Connection, TestClassEntity, ConnectionTest, TestExternalService;
use BulkGate\Plugin\DI\{AutoWiringException, Container, InvalidStateException, MissingParameterException, MissingServiceException};

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 * @testCase
 */
class ContainerTest extends TestCase
{
	public function testAutoWiring(): void
	{
		$container = new Container('rewrite');

		$container['test'] = \ConnectionTest::class;
		$container['production'] = \ConnectionProduction::class;

		Assert::type(\Connection::class, $container->getByClass(\Connection::class));
		Assert::type(\ConnectionProduction::class, $container->getByClass(\Connection::class));
		Assert::type(\ConnectionProduction::class, $container->getByClass(\ConnectionProduction::class));
		Assert::same('production', $container->getByClass(\ConnectionProduction::class)->test());

		Assert::same($container['production'], $container->getByClass(\Connection::class));
		Assert::same($container['test'], $container->getService('test'));
		Assert::same($container['production'], $container->getService('production'));
	}


	public function testDependencyInjection(): void
	{
		$container = new Container('ignore');

		$container['test'] = \ConnectionTest::class;
		$container['production'] = \ConnectionProduction::class;
		$container[] = ['factory' => \Service::class, 'parameters' => ['name' => 'test']];

		$service = $container->getByClass(\Service::class);

		Assert::type(\Service::class, $service);

		Assert::same($container['test'], $service->connection);
		Assert::same($container['production'], $service->production);
		Assert::same('test', $service->name);
	}


	public function testDependencyInjectionAutoWiringOff(): void
	{
		$container = new Container('strict');

		$container['test'] = ['factory' => \ConnectionTest::class, 'wiring' => Connection::class, 'auto_wiring' => false];
		$container['production'] = ['factory' => \ConnectionProduction::class, 'auto_wiring' => false];
		$container[] = ['factory' => \Service::class, 'parameters' => ['name' => 'ok']];

		$service = $container->getByClass(\Service::class);

		Assert::type(\Service::class, $service);

		Assert::same($container['test'], $service->connection);
		Assert::same($container['production'], $service->production);
		Assert::same('ok', $service->name);

		Assert::true(isset($container['test']));
		Assert::false(isset($container['invalid']));
	}


	public function testErrors(): void
	{
		$container = new Container('strict');

		Assert::exception(function () use ($container): void {
			unset($container['test']);
		}, InvalidStateException::class, 'Invalid unset operation');

		Assert::exception(fn() => $container[] = [], InvalidStateException::class, 'Invalid service factory');
		Assert::exception(fn() => $container[] = true, InvalidStateException::class, 'Invalid service factory');
		Assert::exception(fn() => $container[] = ['factory' => 'test'], InvalidStateException::class);
		$container[] = ['factory' => \ConnectionTest::class];
		Assert::exception(fn() => $container[] = ['factory' => \ConnectionTest::class], AutoWiringException::class, 'Auto wiring conflict: \'ConnectionTest\' is already registered');

		Assert::exception(fn() => $container->getService('invalid'), MissingServiceException::class, 'Service \'invalid\' not found');
		Assert::exception(fn() => $container->getByClass('invalid'), MissingServiceException::class, 'Service \'invalid\' not found');

		$container['entity'] = TestClassEntity::class;

		Assert::exception(fn() => $container->getByClass(TestClassEntity::class), MissingParameterException::class, 'Missing \'string\' parameter \'TestClassEntity::$name\'');

		$container['external'] = ['factory' => \TestExternalService::class];
		Assert::exception(fn () => $container->getByClass(\TestExternalService::class), MissingServiceException::class, "Service 'DateTime' not found");
	}


	public function testConfig(): void
	{
		$container = new Container('rewrite');

		$container->setConfig([
			'test' => \ConnectionTest::class,
			'production' => \ConnectionProduction::class
		]);

		Assert::type(\Connection::class, $container->getByClass(\Connection::class));
		Assert::type(\ConnectionProduction::class, $container->getByClass(\Connection::class));

		Assert::same($container['production'], $container->getByClass(\Connection::class));
		Assert::same($container['test'], $container->getService('test'));
		Assert::same($container['production'], $container->getService('production'));
	}


	public function testFactoryMethod(): void
	{
		$container = new Container('rewrite');

		$container['production'] = ['factory' => \ConnectionProduction::class, 'factory_method' => fn () => new \ConnectionProduction()];

		Assert::type(\ConnectionProduction::class, $container->getByClass(\Connection::class));

		Assert::same($container['production'], $container->getByClass(\Connection::class));
		Assert::same($container['production'], $container->getService('production'));
	}


	public function testFactoryMethodInvalid(): void
	{
		$container = new Container('rewrite');
		$container['production'] = ['factory' => \ConnectionTest::class, 'factory_method' => fn () => new \ConnectionProduction()];
		$container['external'] = ['factory' => \TestExternalService::class, 'factory_method' => fn () => new TestExternalService(new DateTime())];

		Assert::exception(fn () => $container->getByClass(\Connection::class), MissingParameterException::class, 'Factory method must return instance of \'ConnectionTest\'');

		Assert::type(TestExternalService::class, $container->getByClass(\TestExternalService::class));
	}


	public function testExistingService(): void
	{
		$container = new Container('rewrite');

		$container['production'] = ['factory' => \TestService::class, 'parameters' => ['service' => $test = new ConnectionTest()]];

		Assert::type(\TestService::class, $service = $container->getByClass(\TestService::class));

		Assert::same($service->service, $test);

		Assert::count(1, $container);
	}
}


(new ContainerTest())->run();
