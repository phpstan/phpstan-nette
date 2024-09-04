<?php declare(strict_types = 1);

namespace PHPStan\Rule\Nette;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use function in_array;
use function sprintf;

/**
 * @implements Rule<InClassNode>
 */
class DoNotExtendNetteObjectRule implements Rule
{

	public function getNodeType(): string
	{
		return InClassNode::class;
	}

	public function processNode(Node $node, Scope $scope): array
	{
		$classReflection = $node->getClassReflection();
		$parentClass = $classReflection->getNativeReflection()->getParentClass();
		if ($parentClass !== false && in_array($parentClass->getName(), [ // @phpstan-ignore-line
			'Nette\Object',
			'Nette\LegacyObject',
		], true)) {
			return [
				RuleErrorBuilder::message(sprintf(
					"Class %s extends %s - it's better to use %s trait.",
					$classReflection->getDisplayName(),
					'Nette\Object',
					'Nette\SmartObject',
				))->identifier('class.extendsNetteObject')->build(),
			];
		}

		return [];
	}

}
