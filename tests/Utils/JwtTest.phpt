<?php declare(strict_types=1);

namespace BulkGate\Plugin\Settings\Test;

require __DIR__ . '/../bootstrap.php';

use Tester\{Assert, TestCase};
use BulkGate\Plugin\Utils\Jwt;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 * @testCase
 */
class JwtTest extends TestCase
{
	public function testEncode(): void
	{
		Assert::same(
			'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ0ZXN0IjoidGVzdCJ9.k2U9Nt_rLAgeX6ihgUx7jB7h3dgbo6unIh7yjmawq44',
			Jwt::encode(['test' => 'test'], 'secret')
		);

		Assert::null(Jwt::encode(['test' => NAN], 'secret'));
	}


	public function testInstance(): void
	{
		Assert::same(
			'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ0ZXN0IjoidGVzdCJ9.k2U9Nt_rLAgeX6ihgUx7jB7h3dgbo6unIh7yjmawq44',
			(new Jwt('secret'))->create(['test' => 'test'])
		);

		Assert::same(
			'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ0ZXN0IjoidGVzdCJ9.k2U9Nt_rLAgeX6ihgUx7jB7h3dgbo6unIh7yjmawq44',
			(new Jwt('other'))->create(['test' => 'test'], 'secret')
		);

		Assert::same(
			'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ0ZXN0IjoidGVzdCJ9.F1kyjG-c_jQAmXkaZMLYxIDxh6VDw5jrMWyyupQz4X8',
			(new Jwt('other'))->create(['test' => 'test'])
		);
	}
}

(new JwtTest())->run();
