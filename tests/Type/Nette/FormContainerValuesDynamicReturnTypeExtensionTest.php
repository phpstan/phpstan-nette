<?php declare(strict_types = 1);

namespace PHPStan\Type\Nette;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionVariant;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\ArrayType;
use PHPStan\Type\Constant\ConstantBooleanType;
use PHPStan\Type\Generic\TemplateTypeMap;
use PHPStan\Type\IterableType;
use PHPStan\Type\MixedType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\UnionType;
use PHPStan\Type\VerbosityLevel;

final class FormContainerValuesDynamicReturnTypeExtensionTest extends \PHPUnit\Framework\TestCase
{

	/** @var \PHPStan\Type\Nette\FormContainerValuesDynamicReturnTypeExtension */
	private $extension;

	protected function setUp(): void
	{
		$this->extension = new FormContainerValuesDynamicReturnTypeExtension();
	}

	public function testParameterAsArray(): void
	{
		$methodReflection = $this->createMock(MethodReflection::class);
		$methodReflection
			->method('getVariants')
			->willReturn([new FunctionVariant(
				TemplateTypeMap::createEmpty(),
				TemplateTypeMap::createEmpty(),
				[],
				true,
				new UnionType([new ArrayType(new MixedType(), new MixedType()), new IterableType(new MixedType(), new ObjectType(\Nette\Utils\ArrayHash::class))])
			)]);

		$scope = $this->createMock(Scope::class);
		$scope->method('getType')->willReturn(new ConstantBooleanType(true));

		/** @var \PhpParser\Node\Expr\MethodCall $methodCall */
		$methodCall = $this->createMock(MethodCall::class);
		/** @var \PhpParser\Node\Arg $arg */
		$arg = $this->createMock(Arg::class);
		/** @var \PhpParser\Node\Expr $value */
		$value = $this->createMock(Expr::class);
		$arg->value = $value;
		$methodCall->args = [
			0 => $arg,
		];

		$resultType = $this->extension->getTypeFromMethodCall($methodReflection, $methodCall, $scope);

		self::assertInstanceOf(ArrayType::class, $resultType);
	}

	public function testParameterAsArrayHash(): void
	{
		$methodReflection = $this->createMock(MethodReflection::class);
		$methodReflection
			->method('getVariants')
			->willReturn([new FunctionVariant(TemplateTypeMap::createEmpty(), TemplateTypeMap::createEmpty(), [], true, new UnionType([new ArrayType(new MixedType(), new MixedType()), new IterableType(new MixedType(), new ObjectType(\Nette\Utils\ArrayHash::class))]))]);

		$scope = $this->createMock(Scope::class);
		$scope->method('getType')->willReturn(new ConstantBooleanType(false));

		/** @var \PhpParser\Node\Expr\MethodCall $methodCall */
		$methodCall = $this->createMock(MethodCall::class);
		/** @var \PhpParser\Node\Arg $arg */
		$arg = $this->createMock(Arg::class);
		/** @var \PhpParser\Node\Expr $value */
		$value = $this->createMock(Expr::class);
		$arg->value = $value;
		$methodCall->args = [
			0 => $arg,
		];

		$resultType = $this->extension->getTypeFromMethodCall($methodReflection, $methodCall, $scope);

		self::assertInstanceOf(ObjectType::class, $resultType);
		self::assertSame(\Nette\Utils\ArrayHash::class, $resultType->describe(VerbosityLevel::value()));
	}

	public function testDefaultParameterIsArrayHash(): void
	{
		$methodReflection = $this->createMock(MethodReflection::class);
		$methodReflection
			->method('getVariants')
			->willReturn([new FunctionVariant(TemplateTypeMap::createEmpty(), TemplateTypeMap::createEmpty(), [], true, new UnionType([new ArrayType(new MixedType(), new MixedType()), new IterableType(new MixedType(), new ObjectType(\Nette\Utils\ArrayHash::class))]))]);

		$scope = $this->createMock(Scope::class);
		$scope->method('getType')->willReturn(new ConstantBooleanType(false));

		/** @var \PhpParser\Node\Expr\MethodCall $methodCall */
		$methodCall = $this->createMock(MethodCall::class);
		$methodCall->args = [];

		$resultType = $this->extension->getTypeFromMethodCall($methodReflection, $methodCall, $scope);

		self::assertInstanceOf(ObjectType::class, $resultType);
		self::assertSame(\Nette\Utils\ArrayHash::class, $resultType->describe(VerbosityLevel::value()));
	}

}
