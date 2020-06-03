<?php declare(strict_types = 1);

namespace PHPStan\Rule\Nette;

use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\TryCatch;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ObjectType;
use PHPStan\Type\TypeCombinator;

/**
 * @implements \PHPStan\Rules\Rule<TryCatch>
 */
class RethrowExceptionRule implements \PHPStan\Rules\Rule
{

	/** @var array<string, string[]> */
	private $methods;

	/**
	 * @param string[][] $methods
	 */
	public function __construct(array $methods)
	{
		$this->methods = $methods;
	}

	public function getNodeType(): string
	{
		return TryCatch::class;
	}

	public function processNode(Node $node, Scope $scope): array
	{
		$hasGeneralCatch = false;
		foreach ($node->catches as $catch) {
			foreach ($catch->types as $type) {
				$typeClass = (string) $type;
				if ($typeClass === 'Exception' || $typeClass === \Throwable::class) {
					$hasGeneralCatch = true;
					break 2;
				}
			}
		}
		if (!$hasGeneralCatch) {
			return [];
		}

		$exceptions = $this->getExceptionTypes($scope, $node->stmts);
		if (count($exceptions) === 0) {
			return [];
		}

		$messages = [];
		foreach ($exceptions as $exceptionName) {
			$exceptionType = new ObjectType($exceptionName);
			foreach ($node->catches as $catch) {
				$caughtType = TypeCombinator::union(...array_map(function (string $class): ObjectType {
					return new ObjectType($class);
				}, $catch->types));
				if (!$caughtType->isSuperTypeOf($exceptionType)->yes()) {
					continue;
				}
				if (
					count($catch->stmts) === 1
					&& $catch->stmts[0] instanceof Node\Stmt\Throw_
					&& $catch->stmts[0]->expr instanceof Variable
					&& $catch->var !== null
					&& is_string($catch->var->name)
					&& is_string($catch->stmts[0]->expr->name)
					&& $catch->var->name === $catch->stmts[0]->expr->name
				) {
					continue 2;
				}
			}

			$messages[] = sprintf('Exception %s needs to be rethrown.', $exceptionName);
		}

		return $messages;
	}

	/**
	 * @param \PHPStan\Analyser\Scope $scope
	 * @param \PhpParser\Node|\PhpParser\Node[]|scalar $node
	 * @return string[]
	 */
	private function getExceptionTypes(Scope $scope, $node): array
	{
		$exceptions = [];
		if ($node instanceof Node) {
			foreach ($node->getSubNodeNames() as $subNodeName) {
				$subNode = $node->{$subNodeName};
				$exceptions = array_merge($exceptions, $this->getExceptionTypes($scope, $subNode));
			}
			if ($node instanceof Node\Expr\MethodCall) {
				$methodCalledOn = $scope->getType($node->var);
				foreach ($this->methods as $type => $methods) {
					if (!$node->name instanceof Node\Identifier) {
						continue;
					}
					if (!(new ObjectType($type))->isSuperTypeOf($methodCalledOn)->yes()) {
						continue;
					}

					$methodName = strtolower((string) $node->name);
					foreach ($methods as $throwingMethodName => $exception) {
						if (strtolower($throwingMethodName) !== $methodName) {
							continue;
						}
						$exceptions[] = $exception;
					}
				}
			}
		} elseif (is_array($node)) {
			foreach ($node as $subNode) {
				$exceptions = array_merge($exceptions, $this->getExceptionTypes($scope, $subNode));
			}
		}

		return array_unique($exceptions);
	}

}
