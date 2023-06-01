<?php declare(strict_types=1);

namespace BulkGate\Plugin\User;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

use BulkGate\Plugin\{
    Eshop\Configuration,
    InvalidResponseException,
    IO\Connection,
    IO\Request,
    IO\Url,
    Settings\Settings,
    Strict,
    Utils\Jwt};
use function array_merge;

class Sign
{
	use Strict;

	private Settings $settings;

	private Connection $connection;

	private Url $url;

    private Configuration $eshop_configuration;


	public function __construct(Settings $settings, Connection $connection, Url $url, Configuration $configuration)
	{
		$this->settings = $settings;
		$this->connection = $connection;
		$this->url = $url;
        $this->eshop_configuration = $configuration;
	}


	/**
	 * @return array{token: string}
	 */
	public function authenticate(): array
	{
		return [
			'token' => Jwt::encode([
					'application_id' => $this->settings->load('static:application_id'),
                    'application_url' => $this->eshop_configuration->url(),
					// todo
					'expire' => time() + 300
				],
				$this->settings->load('static:application_token') ?? 'guest'
			) ?? 'guest'
		];
	}


	/**
	 * @return array{token: string, redirect: string|null}|array{error: string}
	 */
	public function in(string $email, string $password, ?string $eshop_name = null, ?string $success_redirect = null): array
	{
		try
		{
			$response = $this->connection->run(new Request($this->url->get('module/sign/in'), [
				'email' => $email,
				'password' => $password,
				'eshop_name' => $eshop_name
			], 'application/json', 20));

			$login = $response->get('::login');

			if (!isset($login['application_id']) || !isset($login['application_token']))
			{
				return ['error' => ['unknown_error']]; //todo: pouzit balicek pro response (react)
			}

			$this->settings->install();

			$this->settings->set('static:application_id', $login['application_id'], ['type' => 'int']);
			$this->settings->set('static:application_token', $login['application_token'], ['type' => 'string']);
			$this->settings->set('static:synchronize', 0, ['type' => 'int']);

			return array_merge($this->authenticate(), ['redirect' => $success_redirect]);
		}
		catch (InvalidResponseException $e)
		{
			return ['error' => [$e->getMessage()]]; //todo: pouzit balicek pro response (react)
		}
	}


	public function out(): void
	{
		$this->settings->delete('static:application_token');
	}
}
