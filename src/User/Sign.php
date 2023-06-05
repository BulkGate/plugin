<?php declare(strict_types=1);

namespace BulkGate\Plugin\User;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

use BulkGate\{Plugin\Eshop\Configuration, Plugin\InvalidResponseException, Plugin\IO\Connection, Plugin\IO\Request, Plugin\IO\Url, Plugin\Settings\Settings, Plugin\Strict, Plugin\Utils\Jwt};

class Sign
{
	use Strict;

	private Settings $settings;

	private Connection $connection;

	private Url $url;

    private Configuration $configuration;


	public function __construct(Settings $settings, Connection $connection, Url $url, Configuration $configuration)
	{
		$this->settings = $settings;
		$this->connection = $connection;
		$this->url = $url;
		$this->configuration = $configuration;
	}


	public function authenticate(bool $reload = false): ?string
	{
		$token = $this->settings->load('static:application_token', $reload);

		return Jwt::encode([
			'application_id' => $this->settings->load('static:application_id'),
			'application_url' => $this->configuration->url(),
			'application_product' => $this->configuration->product(),
			'application_language' => $this->settings->load('main:language') ?? 'en',
			'guest' => $token === null,
			'expire' => time() + 300
		], $token ?? '');
	}


	/**
	 * @return array{token: string|null, redirect: string|null}|array{error: list<string>}
	 */
	public function in(string $email, string $password, ?string $success_redirect = null): array
	{
		try
		{
			$response = $this->connection->run(new Request($this->url->get('api/1.0/token/get/permanent'), [
				'email' => $email,
				'password' => $password,
				'name' => $this->configuration->name(),
			], 'application/json', 20));

			if (!isset($response->data['data']['application_id']) || !isset($response->data['data']['application_token']))
			{
				return ['error' => ['unknown_error']];
			}

			$this->settings->install();

			$this->settings->set('static:application_id', $response->data['data']['application_id'], ['type' => 'int']);
			$this->settings->set('static:application_token', $response->data['data']['application_token'], ['type' => 'string']);
			$this->settings->set('static:synchronize', 0, ['type' => 'int']);

			return ['token' => $this->authenticate(true), 'redirect' => $success_redirect];
		}
		catch (InvalidResponseException $e)
		{
			return ['error' => [$e->getMessage()]];
		}
	}


	public function out(): void
	{
		$this->settings->delete('static:application_token');
	}
}
