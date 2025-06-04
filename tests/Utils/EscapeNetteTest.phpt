<?php declare(strict_types=1);

namespace BulkGate\Plugin\Utils\Test;

require __DIR__ . '/../bootstrap.php';

use Tester\{Assert, TestCase};
use BulkGate\Plugin\Utils\EscapeNette;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 * @testCase
 */
class EscapeNetteTest extends TestCase
{
	public function testHtml(): void
	{
		Assert::same('&lt;script type=&quot;application/javascript&quot;&gt;alert(1);&lt;/script&gt;', EscapeNette::html('<script type="application/javascript">alert(1);</script>'));
	}


	public function testJs(): void
	{
		Assert::same('"<script type=\"application/javascript\">alert(1);<\/script>"', EscapeNette::js('<script type="application/javascript">alert(1);</script>'));
	}


	public function testUrl(): void
	{
		Assert::same('', EscapeNette::url(' javascript:'));
	}


	public function testHtmlAttr(): void
	{
		Assert::same('`hello ', EscapeNette::htmlAttr('`hello'));
		Assert::same('`hello&quot;', EscapeNette::htmlAttr('`hello"'));
		Assert::same('&lt; &amp; &apos; &quot; &gt;', EscapeNette::htmlAttr('< & \' " >'));
	}
}

(new EscapeNetteTest())->run();
