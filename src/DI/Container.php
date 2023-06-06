<?php declare(strict_types=1);

namespace BulkGate\Plugin\DI;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

use ArrayAccess;
use BulkGate\Plugin\Strict;
use ReflectionClass, ReflectionException;
use function array_key_exists, class_exists, interface_exists, is_array, is_string, is_subclass_of, uniqid;

/**
 * @template T of object
 * @implements ArrayAccess<string, T|class-string<T>|array{name: string, factory?: class-string<T>, auto_wiring?: bool, parameters?: array<string, mixed>, reflection: ReflectionClass<T>, instantiable: bool}>
 */
class Container implements ArrayAccess
{
	use Strict;

	/**
	 * @var array<string, array{name: string, factory?: class-string<T>, auto_wiring?: bool, parameters?: array<string, mixed>, reflection: ReflectionClass<T>, instantiable: bool}>
	 */
	private array $services = [];

	/**
	 * @var array<class-string<T>, array{name: string, factory?: class-string<T>, auto_wiring?: bool, parameters?: array<string, mixed>, reflection: ReflectionClass<T>, instantiable: bool, factory_method?: callable(mixed ...$parameters):T|null}>
	 */
	private array $auto_wiring = [];

	/**
	 * @var string 'strict'|'rewrite'|'ignore'
	 */
	private string $auto_wiring_mode;

	/**
	 * @var array<string, T>
	 */
	private array $instances = [];


	/**
	 * @param string $auto_wiring_mode 'strict'|'rewrite'|'ignore'
	 */
	public function __construct(string $auto_wiring_mode = 'ignore')
	{
		$this->auto_wiring_mode = $auto_wiring_mode;
	}


	/**
	 * @param array<array-key, mixed> $config
	 */
	public function setConfig(array $config): void
	{
		foreach ($config as $name => $service) if (is_string($service) || is_array($service))
		{
			/**
			 * @var class-string<T>|array{factory?: class-string<T>, parameters?: array<string, mixed>, wiring?: class-string<T>, auto_wiring?: bool, factory_method?: callable(mixed ...$parameters):T|null} $service
			 */
			$this[is_string($name) ? $name : uniqid('class-')] = $service;
		}
	}


	/**
	 * @param class-string<T> $factory
	 * @param array<string, mixed> $parameters
	 * @param class-string<T>|null $wiring
	 * @throws AutoWiringException|InvalidStateException
	 */
	public function add(string $factory, array $parameters = [], ?string $name = null, ?string $wiring = null, bool $auto_wiring = true, ?callable $factory_method = null): void
	{
		try
		{
			$name ??= uniqid('class-');

			$this->services[$name] = [
				'name' => $name,
				'factory' => $factory,
				'auto_wiring' => $auto_wiring,
				'parameters' => $parameters,
				'reflection' => $reflection = new ReflectionClass($factory),
				'instantiable' => $reflection->isInstantiable(),
				'factory_method' => $factory_method
			];

			$this->setAutoWiring($factory, $name);

			if ($wiring !== null && interface_exists($wiring) && is_subclass_of($factory, $wiring))
			{
				$this->setAutoWiring($wiring, $name);
			}
			else if ($auto_wiring) foreach ($this->services[$name]['reflection']->getInterfaces() as $interface)
			{
				/**
				 * @var class-string<T> $interface_factory
				 */
				$interface_factory = $interface->getName();

				$this->setAutoWiring($interface_factory, $name);
			}
		}
		catch (ReflectionException $e)
		{
			throw new InvalidStateException($e->getMessage());
		}
	}

	/**
	 * @param class-string<T> $factory
	 * @throws AutoWiringException
	 */
	public function setAutoWiring(string $factory, string $name): void
	{
		if (!array_key_exists($factory, $this->auto_wiring) || $this->auto_wiring_mode === 'rewrite')
		{
			$this->auto_wiring[$factory] = &$this->services[$name];
		}
		else if ($this->auto_wiring_mode === 'strict')
		{
			throw new AutoWiringException("Auto wiring conflict: '$factory' is already registered");
		}
	}


	/**
	 * @param class-string<T> $class
	 * @return T
	 */
	public function getByClass(string $class): object
	{
		if (!isset($this->auto_wiring[$class]))
		{
			throw new MissingServiceException("Service '$class' not found");
		}

		return $this->getService($this->auto_wiring[$class]['name']);
	}


	/**
	 * @return T
	 * @throws MissingServiceException|MissingParameterException
	 */
	public function getService(string $name): object
	{
		if (isset($this->instances[$name]))
		{
			return $this->instances[$name];
		}

		/**
		 * @var array{factory: class-string<T>, parameters: array<string, mixed>, reflection: ReflectionClass<T>, instantiable: bool, factory_method?: callable(mixed ...$parameters):T|null}|null $service
		 */
		$service = $this->services[$name] ?? null;

		if ($service === null)
		{
			throw new MissingServiceException("Service '$name' not found");
		}

		/**
		 * @var class-string<T> $factory
		 */
		$factory = $service['factory'];

		$parameters = [];

		try
		{
			$constructor = $service['reflection']->getMethod('__construct');

			foreach ($constructor->getParameters() as $parameter)
			{
				$type_reflection = $parameter->getType() ?? null;

				/**
				 * @var string|null $parameter_type
				 */
				$parameter_type = $type_reflection instanceof \ReflectionNamedType ? $type_reflection->getName() : null;

				if (isset($service['parameters'][$parameter->getName()]) && $service['parameters'][$parameter->getName()] instanceof $parameter_type)
				{
					$parameters[] = $service['parameters'][$parameter->getName()];
				}
				else if ($parameter_type !== null && (class_exists($parameter_type) || interface_exists($parameter_type)))
				{
					/**
					 * @var class-string<T> $parameter_type
					 */
					$parameters[] = $this->getByClass($parameter_type);
				}
				else
				{
					$static_parameter = $service['parameters'][$parameter->getName()] ?? null;

					if ($static_parameter === null && !isset($service['factory_method']))
					{
						$parameter_type ??= 'unknown';

						throw new MissingParameterException("Missing '$parameter_type' parameter '$factory::\${$parameter->getName()}'");
					}

					$parameters[] = $static_parameter;
				}
			}

			return $this->createInstance($factory, $name, $service['factory_method'] ?? null, ...$parameters);
		}
		catch (ReflectionException $e)
		{
			return $this->createInstance($factory, $name, $service['factory_method'] ?? null);
		}
	}


	/**
	 * @param class-string<T> $factory
	 * @param string $name
	 * @param callable(mixed ...$parameters):T|null $factory_method
	 * @param mixed ...$parameters
	 * @return T
	 * @throws MissingParameterException
	 */
	private function createInstance(string $factory, string $name, ?callable $factory_method, ...$parameters): object
	{
		if ($factory_method !== null)
		{
			$instance = $factory_method(...$parameters);

			if (!$instance instanceof $factory)
			{
				throw new MissingParameterException("Factory method must return instance of '$factory'");
			}

			return $this->instances[$name] = $instance;
		}
		else
		{
			return $this->instances[$name] = new $factory(...$parameters);
		}
	}


	/**
	 * @param array-key $offset
	 * @return bool
	 */
	public function offsetExists($offset): bool
	{
		return isset($this->services[$offset]);
	}


	/**
	 * @param array-key $offset
	 * @throws MissingServiceException|MissingParameterException
	 */
	public function offsetGet($offset): object
	{
		return $this->getService($offset);
	}


	/**
	 * @param array-key $offset
	 * @param class-string<T>|array{factory?: class-string<T>, parameters?: array<string, mixed>, wiring?: class-string<T>, auto_wiring?: bool, factory_method?: callable(mixed ...$parameters):T|null} $value
	 * @throws AutoWiringException|InvalidStateException
	 */
	public function offsetSet($offset, $value): void
	{
		if (is_string($value) && (class_exists($value) || interface_exists($value)))
		{
			$this->add($value, [], $offset);
		}
		else if (is_array($value) && isset($value['factory']))
		{
			$this->add($value['factory'], $value['parameters'] ?? [], $offset, $value['wiring'] ?? null, $value['auto_wiring'] ?? true, $value['factory_method'] ?? null);
		}
		else
		{
			throw new InvalidStateException('Invalid service factory');
		}
	}

	/**
	 * @param array-key $offset
	 * @throws InvalidStateException
	 */
	public function offsetUnset($offset): void
	{
		throw new InvalidStateException('Invalid unset operation');
	}
}
