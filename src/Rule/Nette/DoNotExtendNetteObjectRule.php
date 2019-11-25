<?php declare(strict_types = 1);

namespace PHPStan\Rule\Nette;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Broker\Broker;

/**
 * @implements \PHPStan\Rules\Rule<Class_>
 */
class DoNotExtendNetteObjectRule implements \PHPStan\Rules\Rule
{

	/** @var \PHPStan\Broker\Broker */
	private $broker;

	public function __construct(Broker $broker)
	{
		$this->broker = $broker;
	}

	public function getNodeType(): string
	{
		return Class_::class;
	}

	public function processNode(Node $node, Scope $scope): array
	{
		if (!isset($node->namespacedName)) {
			// anonymous class - will be possible to inspect
			// with node visitor and special ClassBody node
			// because $scope will contain the anonymous class reflection
			return [];
		}

		$className = (string) $node->namespacedName;
		if (!$this->broker->hasClass($className)) {
			return [];
		}

		$classReflection = $this->broker->getClass($className);
		$parentClass = $classReflection->getNativeReflection()->getParentClass();
		if ($parentClass !== false && in_array($parentClass->getName(), [
			'Nette\Object',
			'Nette\LegacyObject',
		], true)) {
			return [
				sprintf(
					"Class %s extends %s - it's better to use %s trait.",
					$className,
					'Nette\Object',
					'Nette\SmartObject'
				),
			];
		}

		return [];
	}

}
