<?php declare(strict_types=1);

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

namespace BulkGate\Extensions\Hook
{
	use BulkGate\Plugin\Event\Variables as PluginVariables;
	use function class_exists, class_alias;

	if (false)
	{
		/**
		 * @deprecated use BulkGate\Plugin\Event\Variables
		 */
		class Variables extends PluginVariables
		{
		}
	}
	else if (!class_exists(Variables::class))
	{
		class_alias(PluginVariables::class, Variables::class);
	}
}
