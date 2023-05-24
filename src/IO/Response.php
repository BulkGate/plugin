<?php declare(strict_types=1);

namespace BulkGate\Plugin\IO;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

use BulkGate\Plugin\{AuthenticateException, Helpers, InvalidResponseException, Strict, Utils\JsonArray};
use function is_string;

class Response
{
	use Strict;

	/**
	 * @var array<string, mixed>
	 */
	public array $data = [];


	/**
	 * @throws AuthenticateException
	 * @throws InvalidResponseException
	 */
	public function __construct(string $data, string $content_type = 'application/json')
	{
		if ($content_type === 'application/json')
		{
			$this->setData(JsonArray::decode($data));
		}
		else
		{
			throw new InvalidResponseException('invalid_content_type');
		}
	}


	/**
	 * @param array<array-key, mixed> $decoded
	 * @throws AuthenticateException
	 * @throws InvalidResponseException
	 */
	private function setData(array $decoded): void
	{
		if ($decoded === [])
		{
			throw new InvalidResponseException('empty_response');
		}

		if (isset($decoded['error']))
		{
			throw new InvalidResponseException(is_string($decoded['error']) ? $decoded['error'] : 'unknown_error');
		}

		if (isset($decoded['signal']) && $decoded['signal'] === 'authenticate')
		{
			throw new AuthenticateException('authenticate');
		}

		$this->data = $decoded;
	}


	/**
	 * @return mixed
	 */
	public function get(string $key)
	{
		return Helpers::reduceStructure($this->data['data'] ?? [], $key);
	}
}
