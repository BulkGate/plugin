<?php declare(strict_types=1);

namespace BulkGate\Plugin\IO;

use function json_encode;
use const PHP_EOL;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

function stream_context_create(array $options): array
{
	return $options;
}


function fopen(string $url, string $mode, bool $use_include_path = false, array $context = null): ?array
{
	if ($url === 'https://portal.bulkgate.com/')
	{
		return [
			'context' => $context,
			'url' => $url,
			'mode' => $mode,
			'use_include_path' => $use_include_path,
		];
	}
	return null;
}


/**
 * @return false|string
 */
function stream_get_contents(array $connection)
{
	return json_encode($connection);
}


function stream_get_meta_data(): array
{
	return ['wrapper_data' => ['HTTP/1.1 200 OK' . PHP_EOL . 'content-type: application/json']];
}


function fclose(): void
{
}