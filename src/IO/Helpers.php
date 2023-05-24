<?php declare(strict_types=1);

namespace BulkGate\Plugin\IO;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

use BulkGate\Plugin\{Strict, Utils\Strings};

class Helpers
{
	use Strict;

	public static function parseContentType(string $header): ?string
	{
		if (preg_match('~content-type:\s([^\n;]+)~', Strings::lower($header), $m))
		{
			[, $content_type] = $m;

			return $content_type;
		}
		return null;
	}
}
