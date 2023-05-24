<?php declare(strict_types=1);

/**
 * @author Lukáš Piják 2023 TOPefekt s.r.o.
 * @link https://www.bulkgate.com/
 */

use Rector\{Config\RectorConfig, Set\ValueObject\SetList, Set\ValueObject\LevelSetList, CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector, TypeDeclaration\Rector\Property\TypedPropertyFromStrictConstructorRector};

return static function (RectorConfig $rectorConfig): void
{
	$rectorConfig->paths([
		__DIR__ . '/src'
	]);

	$rectorConfig->rule(InlineConstructorDefaultToPropertyRector::class);

	$rectorConfig->rule(TypedPropertyFromStrictConstructorRector::class);

	$rectorConfig->sets([
		LevelSetList::UP_TO_PHP_74,
		SetList::TYPE_DECLARATION,
		SetList::ACTION_INJECTION_TO_CONSTRUCTOR_INJECTION
	]);

	$rectorConfig->phpstanConfig(__DIR__ . '/phpstan.neon');
};
