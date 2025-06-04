<?php declare(strict_types=1);

namespace BulkGate\Plugin\IO\Test;

require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/.mock.php';

use Mockery;
use Tester\{Assert, TestCase};
use BulkGate\Plugin\{InvalidResponseException, IO\ConnectionCurl, IO\Request};
use const CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST, CURLOPT_HTTP_VERSION, CURLOPT_HTTPHEADER, CURLOPT_MAXREDIRS, CURLOPT_POSTFIELDS, CURLOPT_RETURNTRANSFER, CURLOPT_SSL_VERIFYPEER, CURLOPT_TIMEOUT, CURLOPT_URL;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 * @testCase
 */
class ConnectionCurlTest extends TestCase
{
	public function testRun(): void
	{
		global $curl_options;

		$connection = new ConnectionCurl(fn() => 'token');

		$response = $connection->run(new Request('https://portal.bulkgate.com/api', ['ok'], 'application/json', 50));

		Assert::same(['message' => 'BulkGate API'], $response->data);
		Assert::same([
			CURLOPT_URL => 'https://portal.bulkgate.com/api',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 50,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => '["ok"]',
			CURLOPT_HTTPHEADER => [
				'Content-Type: application/json',
				'Authorization: Bearer token'
			]
		], $curl_options);

		Assert::exception(fn () => $connection->run(new Request('invalid', ['ok'], 'application/json', 50)), InvalidResponseException::class, 'Server Unavailable. Try contact your hosting provider.');
	}


	public function tearDown(): void
	{
		Mockery::close();
	}
}

(new ConnectionCurlTest())->run();
