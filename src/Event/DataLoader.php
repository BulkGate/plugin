<?php declare(strict_types=1);

namespace BulkGate\Plugin\Event;

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

interface DataLoader
{
	public function load(Variables $variables): void;
}
