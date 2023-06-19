<?php declare(strict_types=1);

namespace BulkGate\Plugin\Settings;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

use BulkGate\Plugin\{Eshop\Configuration, IO\Url, Strict, AuthenticateException, InvalidResponseException};

class Synchronizer
{
	use Strict;

	private Repository\Synchronization $repository;

	private Settings $settings;

	private Url $url;

	private Configuration $configuration;

	public function __construct(Repository\Synchronization $repository, Settings $settings, Url $url, Configuration $configuration)
	{
		$this->repository = $repository;
		$this->settings = $settings;
		$this->url = $url;
		$this->configuration = $configuration;
	}


	public function synchronize(bool $immediately = false): void
	{
		try
		{
			$this->checkUpdate();

			if ($immediately || ($this->settings->load('static:synchronize') ?? 0) < time() && ($this->settings->load('static:application_id') ?? false))
			{
				$plugin_settings = $this->repository->loadPluginSettings();

				$server_settings = $this->repository->loadServerSettings($this->url->get('module/settings/synchronize'), $plugin_settings, 6);

				foreach ($server_settings as $server_setting)
				{
					$key = "$server_setting->scope:$server_setting->key";

					if (!isset($plugin_settings[$key]) || $server_setting->datetime >= $plugin_settings[$key]->datetime)
					{
						$this->settings->set($key, $server_setting->value, [
							'type' => $server_setting->type,
							'datetime' => $server_setting->datetime,
							'order' => $server_setting->order,
							'synchronize_flag' => $server_setting->synchronize_flag,
						]);
					}
				}

				$this->settings->set('static:synchronize', time() + ($this->settings->load('main:synchronize_interval') ?? 3_600), [
					'type' => 'int',
				]);

				$this->settings->cleanup();
			}
		}
		catch (AuthenticateException $e)
		{
			$this->settings->delete('static:application_token');
		}
		catch (InvalidResponseException $e)
		{
		}
	}


	private function checkUpdate(): void
	{
		if ($this->settings->load('static:version') !== $this->configuration->version())
		{
			$this->settings->install(true);
			$this->settings->set('static:version', $this->configuration->version(), [
				'type' => 'string',
			]);
		}
	}
}
