<?php declare(strict_types=1);

namespace BulkGate\Plugin\DI;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

use function is_callable;

trait FactoryStatic
{
	private static Container $container;

	/**
	 * @var callable(): array<string, mixed>
	 */
	private static $parameters_callback;

	/**
	 * @var array<string, mixed>
	 */
	private static array $parameters;


	public static function setup(callable $callback): void
	{
		self::$parameters_callback = $callback;
	}


	public static function get(): Container
	{
		if (!isset(self::$container))
		{
			if (!isset(self::$parameters))
			{
				self::$parameters = isset(self::$parameters_callback) && is_callable(self::$parameters_callback) ? (self::$parameters_callback)() : [];
			}

			self::$container = self::createContainer(self::$parameters);
		}

		return self::$container;
	}


	abstract protected static function createContainer(array $parameters = []): Container;
}
