<?php declare(strict_types = 1);

namespace PHPStan\Rule\Nette;

use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\TryCatch;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\ObjectType;
use PHPStan\Type\TypeCombinator;
use Throwable;
use function array_map;
use function array_merge;
use function array_unique;
use function count;
use function is_array;
use function is_string;
use function sprintf;
use function strtolower;

/**
 * @implements Rule<TryCatch>
 */
class RethrowExceptionRule implements Rule
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
				if ($typeClass === 'Exception' || $typeClass === Throwable::class) {
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
				$caughtType = TypeCombinator::union(...array_map(static function (Name $class): ObjectType {
					return new ObjectType((string) $class);
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

			$messages[] = RuleErrorBuilder::message(sprintf('Exception %s needs to be rethrown.', $exceptionName))->identifier('nette.rethrowException')->build();
		}

		return $messages;
	}

	/**
	 * @param Node|Node[]|scalar $node
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
