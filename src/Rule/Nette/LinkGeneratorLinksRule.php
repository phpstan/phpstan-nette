<?php declare(strict_types = 1);

namespace PHPStan\Rule\Nette;

use Nette\Application\LinkGenerator;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ObjectType;
use function array_slice;

/**
 * @extends LinksRule<MethodCall>
 */
class LinkGeneratorLinksRule extends LinksRule
{

	public function getNodeType(): string
	{
		return MethodCall::class;
	}

	public function processNode(Node $node, Scope $scope): array
	{
		if (!$node->name instanceof Node\Identifier) {
			return [];
		}

		$methodName = $node->name->toString();
		$callerType = $scope->getType($node->var);
		$args = $node->getArgs();

		if (!isset($args[0])) {
			return [];
		}

		if ((new ObjectType(LinkGenerator::class))->isSuperTypeOf($callerType)->no()) {
			return [];
		}

		if ($methodName !== 'link') {
			return [];
		}

		$destinationArg = $args[0];
		$paramArgs = array_slice($args, 1);

		$destinations = $this->extractDestintionsFromArg($scope, $destinationArg);
		$paramsVariants = $this->extractParamVariantsFromArrayArg($scope, $paramArgs[0] ?? null);
		return $this->linkChecker->checkLinkVariants($scope, [null], $methodName, $destinations, $paramsVariants);
	}

}
