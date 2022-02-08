<?php declare(strict_types = 1);

namespace PHPStan\Type\Nette;

use Nette\Utils\ArrayHash;
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
use PHPUnit\Framework\TestCase;

final class FormContainerValuesDynamicReturnTypeExtensionTest extends TestCase
{

	/** @var FormContainerValuesDynamicReturnTypeExtension */
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
				new UnionType([new ArrayType(new MixedType(), new MixedType()), new IterableType(new MixedType(), new ObjectType(ArrayHash::class))])
			)]);

		$scope = $this->createMock(Scope::class);
		$scope->method('getType')->willReturn(new ConstantBooleanType(true));

		$methodCall = $this->createMock(MethodCall::class);
		$arg = $this->createMock(Arg::class);
		$value = $this->createMock(Expr::class);
		$arg->value = $value;
		$methodCall->args = [
			0 => $arg,
		];
		$methodCall->method('getArgs')->willReturn($methodCall->args);

		$resultType = $this->extension->getTypeFromMethodCall($methodReflection, $methodCall, $scope);

		self::assertInstanceOf(ArrayType::class, $resultType);
	}

	public function testParameterAsArrayHash(): void
	{
		$methodReflection = $this->createMock(MethodReflection::class);
		$methodReflection
			->method('getVariants')
			->willReturn([new FunctionVariant(TemplateTypeMap::createEmpty(), TemplateTypeMap::createEmpty(), [], true, new UnionType([new ArrayType(new MixedType(), new MixedType()), new IterableType(new MixedType(), new ObjectType(ArrayHash::class))]))]);

		$scope = $this->createMock(Scope::class);
		$scope->method('getType')->willReturn(new ConstantBooleanType(false));

		$methodCall = $this->createMock(MethodCall::class);
		$arg = $this->createMock(Arg::class);
		$value = $this->createMock(Expr::class);
		$arg->value = $value;
		$methodCall->args = [
			0 => $arg,
		];
		$methodCall->method('getArgs')->willReturn($methodCall->args);

		$resultType = $this->extension->getTypeFromMethodCall($methodReflection, $methodCall, $scope);

		self::assertInstanceOf(ObjectType::class, $resultType);
		self::assertSame(ArrayHash::class, $resultType->describe(VerbosityLevel::value()));
	}

	public function testDefaultParameterIsArrayHash(): void
	{
		$methodReflection = $this->createMock(MethodReflection::class);
		$methodReflection
			->method('getVariants')
			->willReturn([new FunctionVariant(TemplateTypeMap::createEmpty(), TemplateTypeMap::createEmpty(), [], true, new UnionType([new ArrayType(new MixedType(), new MixedType()), new IterableType(new MixedType(), new ObjectType(ArrayHash::class))]))]);

		$scope = $this->createMock(Scope::class);
		$scope->method('getType')->willReturn(new ConstantBooleanType(false));

		$methodCall = $this->createMock(MethodCall::class);
		$methodCall->args = [];
		$methodCall->method('getArgs')->willReturn($methodCall->args);

		$resultType = $this->extension->getTypeFromMethodCall($methodReflection, $methodCall, $scope);

		self::assertInstanceOf(ObjectType::class, $resultType);
		self::assertSame(ArrayHash::class, $resultType->describe(VerbosityLevel::value()));
	}

}
