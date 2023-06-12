<?php declare(strict_types=1);

namespace BulkGate\Plugin\Event;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

use BulkGate\Plugin\{Strict, Settings\Settings};
use function array_key_exists, in_array, str_replace, uniqid, preg_replace;

class Dispatcher
{
	use Strict;

	private Settings $settings;

	private Hook $hook;

	private Loader $loader;

	public function __construct(Settings $settings, Hook $hook, Loader $loader)
	{
		$this->settings = $settings;
		$this->hook = $hook;
		$this->loader = $loader;
	}


	/**
	 * @param array<array-key, mixed> $parameters
	 */
	public function dispatch(string $category, string $endpoint, Variables $variables, array $parameters = []): void
	{
		$this->loader->load($variables, $parameters);

		if ($this->check($category, $endpoint, $variables))
		{
			if (in_array($this->settings->load('main:dispatcher') ?? 'direct', ['cron', 'asset'], true))
			{
				$id = preg_replace('~[^0-9a-zA-Z]~', '-', uniqid('', true));

				$this->settings->set("asynchronous:$id", ['category' => $category, 'endpoint' => $endpoint, 'variables' => $variables->toArray()], ['type' => 'json']);
			}
			else
			{
				$this->hook->dispatch($category, $endpoint, $variables->toArray());
			}
		}
	}


	private function check(string $category, string $endpoint, Variables $variables): bool
	{
		if (($this->settings->load('main:synchronize') ?? 'all') === 'all')
		{
			return true;
		}
		else
		{
			$language = $variables['lang_id'] ?? 'en';
			$store_id = $variables['store_id'] ?? 0;
			$category = str_replace('-', '_', $category);

			if (!in_array($this->settings->load('main:language_mutation'), [1, true, '1'], true))
			{
				$language = 'default';
			}

			foreach (["admin_sms-default-$store_id", "customer_sms-$language-$store_id"] as $scope)
			{
				if ($category === 'order' && $endpoint === 'status_change' && isset($variables['order_status_id']))
				{
					$endpoint = "status_change_{$variables['order_status_id']}";
				}

				if ($category === 'return' && $endpoint === 'status_change' && isset($variables['return_status_id']))
				{
					$endpoint = "status_change_{$variables['return_status_id']}";
				}

				$settings = $this->settings->load("$scope:{$category}_$endpoint");

				if (is_array($settings)) foreach ($settings as $value) if (is_array($value) && array_key_exists('active', $value) && $value['active'] === true)
				{
					return true;
				}
			}
			return false;
		}
	}
}
