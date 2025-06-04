<?php declare(strict_types=1);

namespace BulkGate\Plugin\Localization\Test;

require __DIR__ . '/../bootstrap.php';

use Tester\{Assert, TestCase};
use BulkGate\Plugin\Localization\FormatterIntl;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 * @testCase
 */
class FormatterIntlTest extends TestCase
{
	public function testCzechia(): void
	{
		$formatter = new FormatterIntl('cs', 'cz');

		Assert::match('~11\.\s4\.\s2022\s16:05~u', $formatter->format('datetime', '2022-04-11 16:05:00', 'Europe/Prague'));
		Assert::match('~11\.\s4\.\s2023\s16:06~u', $formatter->format('datetime', 1681222006, 'Europe/Prague'));
		Assert::null($formatter->format('datetime', "1681222006", 'Europe/Prague'));
		Assert::null($formatter->format('datetime', "hahahaha", 'Europe/Prague'));

		Assert::match('~11\.\s4\.\s2022~u', $formatter->format('date', '2022-04-11 16:05:00', 'Europe/Prague'));
		Assert::match('~11\.\s4\.\s2023~u', $formatter->format('date', 1681222006, 'Europe/Prague'));
		Assert::null($formatter->format('date', "161222006", 'Europe/Prague'));
		Assert::null($formatter->format('date', "hahahaha", 'Europe/Prague'));

		Assert::same('16:05', $formatter->format('time', '2022-04-11 16:05:00', 'Europe/Prague'));
		Assert::same('16:06', $formatter->format('time', 1681222006, 'Europe/Prague'));
		Assert::null($formatter->format('time', "1681222006", 'Europe/Prague'));
		Assert::null($formatter->format('time', "hahahaha", 'Europe/Prague'));

		Assert::match('~16,50\sKč~u', $formatter->format('price', 16.5, 'CZK'));
		Assert::match('~16,00\sKč~u', $formatter->format('price', 16, 'CZK'));
		Assert::match('~16,50\s€~u', $formatter->format('price', 16.5, 'EUR'));
		Assert::match('~0,00\s€~u', $formatter->format('price', ''));

		Assert::same('16,5', $formatter->format('number', 16.5));
		Assert::same('16', $formatter->format('number', 16));
		Assert::match('~1\s000~u', $formatter->format('number', 1000));
		Assert::match('~1\s000\s000~u', $formatter->format('number', 1000000));
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

		Assert::match('~11\savr\.\s2022.*16:05~u', $formatter->format('datetime', '2022-04-11 16:05:00', 'Europe/Prague'));
		Assert::match('~11\savr\.\s2023.*16:06~u', $formatter->format('datetime', 1681222006, 'Europe/Prague'));
		Assert::null($formatter->format('datetime', "1681222006", 'Europe/Prague'));
		Assert::null($formatter->format('datetime', "hahahaha", 'Europe/Prague'));

		Assert::match('~11\savr\.\s2022~u', $formatter->format('date', '2022-04-11 16:05:00', 'Europe/Prague'));
		Assert::match('~11\savr\.\s2023~u', $formatter->format('date', 1681222006, 'Europe/Prague'));
		Assert::null($formatter->format('date', "1681222006", 'Europe/Prague'));
		Assert::null($formatter->format('date', "hahahaha", 'Europe/Prague'));

		Assert::same('16:05', $formatter->format('time', '2022-04-11 16:05:00', 'Europe/Prague'));
		Assert::same('16:06', $formatter->format('time', 1681222006, 'Europe/Prague'));
		Assert::null($formatter->format('time', "1681222006", 'Europe/Prague'));
		Assert::null($formatter->format('time', "hahahaha", 'Europe/Prague'));

		Assert::match('~16,50\sCZK~u', $formatter->format('price', 16.5, 'CZK'));
		Assert::match('~16,00\sCZK~u', $formatter->format('price', 16, 'CZK'));
		Assert::match('~16,50\s€~u', $formatter->format('price', 16.5, 'EUR'));
		Assert::match('~0,00\s€~u', $formatter->format('price', ''));

		Assert::same('16,5', $formatter->format('number', 16.5));
		Assert::same('16', $formatter->format('number', 16));
		Assert::match('~1\s000~u', $formatter->format('number', 1000));
		Assert::match('~1\s000\s000~u', $formatter->format('number', 1000000));
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

		Assert::match('~Apr\s11,\s2022,\s4:05\sPM~u', $formatter->format('datetime', '2022-04-11 16:05:00', 'Europe/Prague'));
		Assert::match('~Apr\s11,\s2023,\s4:06\sPM~u', $formatter->format('datetime', 1681222006, 'Europe/Prague'));
		Assert::null($formatter->format('datetime', "1681222006", 'Europe/Prague'));
		Assert::null($formatter->format('datetime', "hahahaha", 'Europe/Prague'));

		Assert::match('~Apr\s11,\s2022~u', $formatter->format('date', '2022-04-11 16:05:00', 'Europe/Prague'));
		Assert::match('~Apr\s11,\s2023~u', $formatter->format('date', 1681222006, 'Europe/Prague'));
		Assert::null($formatter->format('date', "1681222006", 'Europe/Prague'));
		Assert::null($formatter->format('date', "hahahaha", 'Europe/Prague'));

		Assert::match('~4:05\sPM~u', $formatter->format('time', '2022-04-11 16:05:00', 'Europe/Prague'));
		Assert::match('~4:06\sPM~u', $formatter->format('time', 1681222006, 'Europe/Prague'));
		Assert::null($formatter->format('time', "1681222006", 'Europe/Prague'));
		Assert::null($formatter->format('time', "hahahaha", 'Europe/Prague'));

		Assert::match('~CZK\s16\.50~u', $formatter->format('price', 16.5, 'CZK'));
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
