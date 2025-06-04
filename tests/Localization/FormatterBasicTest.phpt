<?php declare(strict_types=1);

namespace BulkGate\Plugin\Localization\Test;

require __DIR__ . '/../bootstrap.php';

use Tester\{Assert, TestCase};
use BulkGate\Plugin\Localization\FormatterBasic;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 * @testCase
 */
class FormatterBasicTest extends TestCase
{
	public function testBase(): void
	{
		$formatter = new FormatterBasic();

		Assert::same('11. 4. 2022 16:05', $formatter->format('datetime', '2022-04-11 16:05:00', 'Europe/Prague'));
		Assert::same('11. 4. 2023 16:06', $formatter->format('datetime', 1681222006, 'Europe/Prague'));
		Assert::null($formatter->format('datetime', "1681222006", 'Europe/Prague'));
		Assert::null($formatter->format('datetime', "hahahaha", 'Europe/Prague'));

		Assert::same('11. 4. 2022', $formatter->format('date', '2022-04-11 16:05:00', 'Europe/Prague'));
		Assert::same('11. 4. 2023', $formatter->format('date', 1681222006, 'Europe/Prague'));
		Assert::null($formatter->format('date', "1681222006", 'Europe/Prague'));
		Assert::null($formatter->format('date', "hahahaha", 'Europe/Prague'));

		Assert::same('16:05', $formatter->format('time', '2022-04-11 16:05:00', 'Europe/Prague'));
		Assert::same('16:06', $formatter->format('time', 1681222006, 'Europe/Prague'));
		Assert::null($formatter->format('time', "1681222006", 'Europe/Prague'));
		Assert::null($formatter->format('time', "hahahaha", 'Europe/Prague'));

		Assert::same('16.50 CZK', $formatter->format('price', 16.5, 'CZK'));
		Assert::same('16.00 CZK', $formatter->format('price', 16, 'CZK'));
		Assert::same('16.50 EUR', $formatter->format('price', 16.5, 'EUR'));
		Assert::same('0.00 EUR', $formatter->format('price', ''));

		Assert::same('16.50', $formatter->format('number', 16.5));
		Assert::same('16.00', $formatter->format('number', 16));
		Assert::same('1,000.00', $formatter->format('number', 1000));
		Assert::same('1,000,000.00', $formatter->format('number', 1000000));
		Assert::same('0.00', $formatter->format('number', null));

		Assert::same('CZ', $formatter->format('country', 'CZ'));
		Assert::same('Fr', $formatter->format('country', 'Fr'));
		Assert::same('us', $formatter->format('country', 'us'));
		Assert::null($formatter->format('country', null));
		Assert::same('XX', $formatter->format('country', 'XX'));

		Assert::null($formatter->format('invalid', 'XX'));
	}
}

(new FormatterBasicTest())->run();
