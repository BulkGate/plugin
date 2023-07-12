<?php declare(strict_types=1);

namespace BulkGate\Plugin\Debug;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

use BulkGate\Plugin\Strict;
use function base64_decode, serialize, unserialize, extension_loaded, json_decode, json_encode, error_get_last;

class Requirements
{
	use Strict;

	public bool $failed = false;

	public int $count = 0;

	public int $passed = 0;


	/**
	 * @param list<array{passed: bool, description: string, color: string, error: string|null}> $output
	 * @return list<array{passed: bool, description: string, color: string, error: string|null}>
	 */
	public function run(array $output = []): array
	{
		$output[] = $this->same(true, PHP_VERSION_ID >= 70400, 'PHP >= 7.4 version');
		$output[] = $this->same(true, extension_loaded('intl'), 'INTL extension', true);
		$output[] = $this->same(true, extension_loaded('curl'), 'cURL extension', true);
		$output[] = $this->same(true, extension_loaded('mbstring'), 'Multibyte string extension', true);
		$output[] = $this->same(true, extension_loaded('zlib'), 'Compression extension', true);
		$output[] = $this->same('BulkGate', unserialize(serialize('BulkGate')), 'Serialize');
		$output[] = $this->same('BulkGate', base64_decode(base64_encode('BulkGate')), 'Base64');
		$output[] = $this->same(['BulkGate' => 'portal'], json_decode((string) json_encode(['BulkGate' => 'portal']), true), 'JSON');

		return $output;
	}


	/**
	 * @param mixed $excepted
	 * @param mixed $actual
	 * @return array{passed: bool, description: string, color: string, error: string|null}
	 */
	public function same($excepted, $actual, string $description, bool $optional = false): array
	{
		$this->count ++;

		if ($excepted === $actual)
		{
			$this->passed ++;

			return [
				'passed' => true,
				'description' => $description,
				'color' => 'limegreen',
				'error' => null,
			];
		}
		else
		{
			$error = error_get_last();
			$error_message = $error !== null ? $error['message'] : null;

			return [
				'passed' => false,
				'description' => $description,
				'color' => $optional ? 'darkorange' : 'red',
				'error' => $error_message,
			];
		}
	}
}
