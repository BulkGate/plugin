<?php declare(strict_types=1);

namespace BulkGate\Plugin\IO\Test;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

use Tester\{Assert, TestCase};
use BulkGate\Plugin\IO\Helpers;

require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
class HelperTest extends TestCase
{
	public function testParseContentType(): void
	{
		Assert::same('application/json', Helpers::parseContentType(<<<EOT
            content-type: application/json; charset=utf-8
            date: Thu, 30 Mar 2023 10:40:14 GMT
            etag: W/"b086cd16a5d1e1190981cda623503729"
            referrer-policy: origin-when-cross-origin, strict-origin-when-cross-origin
            server: GitHub.com
        EOT
		));


		Assert::same('application/json', Helpers::parseContentType(<<<EOT
            date: Thu, 30 Mar 2023 10:40:14 GMT
            etag: W/"b086cd16a5d1e1190981cda623503729"
            content-type: application/json; charset=utf-8
            referrer-policy: origin-when-cross-origin, strict-origin-when-cross-origin
            server: GitHub.com
        EOT
		));

		Assert::same('application/json', Helpers::parseContentType(<<<EOT
            date: Thu, 30 Mar 2023 10:40:14 GMT
            etag: W/"b086cd16a5d1e1190981cda623503729"
            referrer-policy: origin-when-cross-origin, strict-origin-when-cross-origin
            server: GitHub.com
            Content-Type: application/jsoN; charset=utf-8
        EOT
		));

		Assert::same('application/zip', Helpers::parseContentType(<<<EOT
            date: Thu, 30 Mar 2023 10:40:14 GMT
            etag: W/"b086cd16a5d1e1190981cda623503729"
            referrer-policy: origin-when-cross-origin, strict-origin-when-cross-origin
            server: GitHub.com
            Content-Type: application/zip
        EOT
		));

		Assert::null(Helpers::parseContentType(<<<EOT
            date: Thu, 30 Mar 2023 10:40:14 GMT
            etag: W/"b086cd16a5d1e1190981cda623503729"
            referrer-policy: origin-when-cross-origin, strict-origin-when-cross-origin
        EOT
		));
	}
}

(new HelperTest())->run();
