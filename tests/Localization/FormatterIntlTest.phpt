<?php declare(strict_types=1);

namespace BulkGate\Plugin\Localization\Test;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

use Tester\{Assert, TestCase};
use BulkGate\Plugin\Localization\FormatterIntl;

require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
class FormatterIntlTest extends TestCase
{
	public function testCzechia(): void
	{
		$formatter = new FormatterIntl('cs', 'cz');

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

		Assert::same('16,50 Kč', $formatter->format('price', 16.5, 'CZK'));
		Assert::same('16,00 Kč', $formatter->format('price', 16, 'CZK'));
		Assert::same('16,50 €', $formatter->format('price', 16.5, 'EUR'));
		Assert::same('0,00 €', $formatter->format('price', ''));

		Assert::same('16,5', $formatter->format('number', 16.5));
		Assert::same('16', $formatter->format('number', 16));
		Assert::same('1 000', $formatter->format('number', 1000));
		Assert::same('1 000 000', $formatter->format('number', 1000000));
		Assert::same('0', $formatter->format('number', null));

		Assert::same('Česko', $formatter->format('country', 'CZ'));
		Assert::same('Francie', $formatter->format('country', 'Fr'));
		Assert::same('Spojené státy', $formatter->format('country', 'us'));
		Assert::null($formatter->format('country', null));
		Assert::same('XX', $formatter->format('country', 'XX'));

		Assert::null($formatter->format('invalid', 'XX'));
	}


	public function testFrance(): void
	{
		$formatter = new FormatterIntl('fr', 'FR');

		Assert::match('~11 avr\. 2022.*16:05~', $formatter->format('datetime', '2022-04-11 16:05:00', 'Europe/Prague'));
		Assert::match('~11 avr\. 2023.*16:06~', $formatter->format('datetime', 1681222006, 'Europe/Prague'));
		Assert::null($formatter->format('datetime', "1681222006", 'Europe/Prague'));
		Assert::null($formatter->format('datetime', "hahahaha", 'Europe/Prague'));

		Assert::same('11 avr. 2022', $formatter->format('date', '2022-04-11 16:05:00', 'Europe/Prague'));
		Assert::same('11 avr. 2023', $formatter->format('date', 1681222006, 'Europe/Prague'));
		Assert::null($formatter->format('date', "1681222006", 'Europe/Prague'));
		Assert::null($formatter->format('date', "hahahaha", 'Europe/Prague'));

		Assert::same('16:05', $formatter->format('time', '2022-04-11 16:05:00', 'Europe/Prague'));
		Assert::same('16:06', $formatter->format('time', 1681222006, 'Europe/Prague'));
		Assert::null($formatter->format('time', "1681222006", 'Europe/Prague'));
		Assert::null($formatter->format('time', "hahahaha", 'Europe/Prague'));

		Assert::same('16,50 CZK', $formatter->format('price', 16.5, 'CZK'));
		Assert::same('16,00 CZK', $formatter->format('price', 16, 'CZK'));
		Assert::same('16,50 €', $formatter->format('price', 16.5, 'EUR'));
		Assert::same('0,00 €', $formatter->format('price', ''));

		Assert::same('16,5', $formatter->format('number', 16.5));
		Assert::same('16', $formatter->format('number', 16));
		Assert::same('1 000', $formatter->format('number', 1000));
		Assert::same('1 000 000', $formatter->format('number', 1000000));
		Assert::same('0', $formatter->format('number', null));

		Assert::same('Tchéquie', $formatter->format('country', 'CZ'));
		Assert::same('France', $formatter->format('country', 'Fr'));
		Assert::same('États-Unis', $formatter->format('country', 'us'));
		Assert::null($formatter->format('country', null));
		Assert::same('XX', $formatter->format('country', 'XX'));

		Assert::null($formatter->format('invalid', 'XX'));
	}


	public function testUSA(): void
	{
		$formatter = new FormatterIntl('en', 'US');

		Assert::same('Apr 11, 2022, 4:05 PM', $formatter->format('datetime', '2022-04-11 16:05:00', 'Europe/Prague'));
		Assert::same('Apr 11, 2023, 4:06 PM', $formatter->format('datetime', 1681222006, 'Europe/Prague'));
		Assert::null($formatter->format('datetime', "1681222006", 'Europe/Prague'));
		Assert::null($formatter->format('datetime', "hahahaha", 'Europe/Prague'));

		Assert::same('Apr 11, 2022', $formatter->format('date', '2022-04-11 16:05:00', 'Europe/Prague'));
		Assert::same('Apr 11, 2023', $formatter->format('date', 1681222006, 'Europe/Prague'));
		Assert::null($formatter->format('date', "1681222006", 'Europe/Prague'));
		Assert::null($formatter->format('date', "hahahaha", 'Europe/Prague'));

		Assert::same('4:05 PM', $formatter->format('time', '2022-04-11 16:05:00', 'Europe/Prague'));
		Assert::same('4:06 PM', $formatter->format('time', 1681222006, 'Europe/Prague'));
		Assert::null($formatter->format('time', "1681222006", 'Europe/Prague'));
		Assert::null($formatter->format('time', "hahahaha", 'Europe/Prague'));

		Assert::same('CZK 16.50', $formatter->format('price', 16.5, 'CZK'));
		Assert::same('$16.00', $formatter->format('price', 16, 'USD'));
		Assert::same('€16.50', $formatter->format('price', 16.5, 'EUR'));
		Assert::same('€0.00', $formatter->format('price', ''));

		Assert::same('16.5', $formatter->format('number', 16.5));
		Assert::same('16', $formatter->format('number', 16));
		Assert::same('1,000', $formatter->format('number', 1000));
		Assert::same('1,000,000', $formatter->format('number', 1000000));
		Assert::same('0', $formatter->format('number', null));

		Assert::same('Czechia', $formatter->format('country', 'CZ'));
		Assert::same('France', $formatter->format('country', 'Fr'));
		Assert::same('United States', $formatter->format('country', 'us'));
		Assert::null($formatter->format('country', null));
		Assert::same('XX', $formatter->format('country', 'XX'));

		Assert::null($formatter->format('invalid', 'XX'));
	}
}

(new FormatterIntlTest())->run();
