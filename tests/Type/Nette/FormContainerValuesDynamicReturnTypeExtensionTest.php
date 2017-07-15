<?php declare(strict_types = 1);

namespace PHPStan\Type\Nette;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\ArrayType;
use PHPStan\Type\CommonUnionType;
use PHPStan\Type\FalseBooleanType;
use PHPStan\Type\IterableIterableType;
use PHPStan\Type\MixedType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\TrueBooleanType;

final class FormContainerValuesDynamicReturnTypeExtensionTest extends \PHPUnit\Framework\TestCase
{

	/** @var \PHPStan\Type\Nette\FormContainerValuesDynamicReturnTypeExtension */
	private $extension;

	protected function setUp()
	{
		$this->extension = new FormContainerValuesDynamicReturnTypeExtension();
	}

	public function testParameterAsArray()
	{
		$methodReflection = $this->createMock(MethodReflection::class);
		$methodReflection
			->method('getReturnType')
			->willReturn(new CommonUnionType([new ArrayType(new MixedType()), new IterableIterableType(new ObjectType(\Nette\Utils\ArrayHash::class))]));

		$scope = $this->createMock(Scope::class);
		$scope->method('getType')->willReturn(new TrueBooleanType());

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

		$this->assertInstanceOf(ArrayType::class, $resultType);
	}

	public function testParameterAsArrayHash()
	{
		$methodReflection = $this->createMock(MethodReflection::class);
		$methodReflection
			->method('getReturnType')
			->willReturn(new CommonUnionType([new ArrayType(new MixedType()), new IterableIterableType(new ObjectType(\Nette\Utils\ArrayHash::class))]));

		$scope = $this->createMock(Scope::class);
		$scope->method('getType')->willReturn(new FalseBooleanType());

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

		$this->assertInstanceOf(ObjectType::class, $resultType);
		$this->assertSame(\Nette\Utils\ArrayHash::class, $resultType->getClass());
	}

	public function testDefaultParameterIsArrayHash()
	{
		$methodReflection = $this->createMock(MethodReflection::class);
		$methodReflection
			->method('getReturnType')
			->willReturn(new CommonUnionType([new ArrayType(new MixedType()), new IterableIterableType(new ObjectType(\Nette\Utils\ArrayHash::class))]));

		$scope = $this->createMock(Scope::class);
		$scope->method('getType')->willReturn(new FalseBooleanType());

		/** @var \PhpParser\Node\Expr\MethodCall $methodCall */
		$methodCall = $this->createMock(MethodCall::class);
		$methodCall->args = [];

		$resultType = $this->extension->getTypeFromMethodCall($methodReflection, $methodCall, $scope);

		$this->assertInstanceOf(ObjectType::class, $resultType);
		$this->assertSame(\Nette\Utils\ArrayHash::class, $resultType->getClass());
	}

}
