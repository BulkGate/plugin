<?php declare(strict_types=1);

namespace BulkGate\Plugin\Event;

use BulkGate\Plugin\{Strict, Settings\Settings};
use function in_array, uniqid, preg_replace;

/**
 * @author Lukáš Piják 2025 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */
class Dispatcher
{
	use Strict;

	public const
		Direct = 'direct',
		Asset = 'asset',
		Cron = 'cron';

	private Settings $settings;

	private Hook $hook;

	private Loader $loader;

	public static string $default_dispatcher = self::Direct;

	public function __construct(Settings $settings, Hook $hook, Loader $loader)
	{
		$this->settings = $settings;
		$this->hook = $hook;
		$this->loader = $loader;
	}

	/**
	 * @param array<array-key, mixed> $parameters
	 */
	public function dispatch(string $category, string $endpoint, Variables $variables, array $parameters = [], ?callable $success_callback = null): void
	{
		$this->loader->load($variables, $parameters);

		$variables['contact_synchronize'] = $this->settings->load('main:synchronization') ?? 'all';
		$variables['contact_address_preference'] = $this->settings->load('main:address_preference') ?? 'delivery';

		if (in_array($this->settings->load('main:dispatcher') ?? self::$default_dispatcher, [self::Cron, self::Asset], true))
		{
			$id = preg_replace('~[^0-9a-zA-Z]~', '-', uniqid('', true));

			$this->settings->set("asynchronous:$id", ['category' => $category, 'endpoint' => $endpoint, 'variables' => $variables->toArray()], ['type' => 'json']);
		}
		else
		{
			$this->hook->dispatch($category, $endpoint, $variables->toArray());
		}

		$success_callback !== null && $success_callback();
	}
}
