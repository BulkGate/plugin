<?php declare(strict_types=1);

namespace BulkGate\Plugin\IO\Test;

require __DIR__ . '/../bootstrap.php';

use Tester\{Assert, TestCase};
use BulkGate\Plugin\{AuthenticateException, InvalidResponseException, IO\Response};

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 * @testCase
 */
class ResponseTest extends TestCase
{
	public function testSimple(): void
	{
		$request = new Response('{"status":"ok"}', 'application/json');

		Assert::same(['status' => 'ok'], $request->data);
	}


	public function testAdvanced(): void
	{
		$request = new Response('{"data": {"status":"ok","reducer":{"scope":{"name":"John"},"server":{}}}}', 'application/json');

		Assert::same(['data' => ['status' => 'ok', 'reducer' => ['scope' => ['name' => 'John'], 'server' => []]]], $request->data);
	}


	public function testUnsupportedContentType(): void
	{
		Assert::exception(fn () => new Response('{}', 'application/xxx'), InvalidResponseException::class, 'invalid_content_type');
		Assert::exception(fn () => new Response('{}', 'application/json'), InvalidResponseException::class, 'empty_response');
		Assert::exception(fn () => new Response('{"error": "test_error"}', 'application/json'), InvalidResponseException::class, 'test_error');
		Assert::exception(fn () => new Response('{"error": ["test_error1", "test_error2"]}', 'application/json'), InvalidResponseException::class, 'test_error1');

		Assert::exception(fn () => new Response('{"signal": "authenticate"}', 'application/json'), AuthenticateException::class, 'authenticate');
	}
}

(new ResponseTest())->run();
