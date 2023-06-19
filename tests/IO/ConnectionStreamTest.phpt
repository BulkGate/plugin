<?php declare(strict_types=1);

namespace BulkGate\Plugin\IO\Test;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

use Mockery;
use Tester\{Assert, TestCase};
use BulkGate\Plugin\{InvalidResponseException, IO\ConnectionStream, IO\Request};

require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/.mock.php';

/**
 * @testCase
 */
class ConnectionStreamTest extends TestCase
{
	public function testRun(): void
	{
		$connection = new ConnectionStream(fn () => 'token');

		$response = $connection->run(new Request('https://portal.bulkgate.com/', ['ok'], 'application/json', 50));

		Assert::equal([
			'context' => [
				'http' => [
					'method' => 'POST',
					'header' => ['Content-type: application/json', 'Authorization: Bearer token'],
					'content' => '["ok"]',
					'ignore_errors' => true,
					'timeout' => 50,
				],
			],
			'url' => 'https://portal.bulkgate.com/',
			'mode' => 'r',
			'use_include_path' => false,
		], $response->data);

		Assert::exception(fn () => $connection->run(new Request('invalid', ['ok'], 'application/json', 50)), InvalidResponseException::class, 'Server Unavailable. Try contact your hosting provider.');
	}


	public function tearDown(): void
	{
		Mockery::close();
	}
}

(new ConnectionStreamTest())->run();
