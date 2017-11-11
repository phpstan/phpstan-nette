<?php declare(strict_types = 1);

namespace PHPStan\Rule\Nette;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Broker\Broker;
use PHPStan\Reflection\ClassReflection;

class DoNotExtendNetteObjectRuleTest extends \PHPUnit\Framework\TestCase
{

	/** @var \PHPStan\Rule\Nette\DoNotExtendNetteObjectRule */
	private $rule;

	protected function setUp()
	{
		$broker = $this->createMock(Broker::class);
		$broker->method('hasClass')->will($this->returnValue(true));
		$broker->method('getClass')->will($this->returnCallback(function (string $className) {
			$nativeReflection = new \ReflectionClass($className);
			$classReflection = $this->createMock(ClassReflection::class);
			$classReflection->method('getNativeReflection')->will($this->returnValue($nativeReflection));
			return $classReflection;
		}));

		$this->rule = new DoNotExtendNetteObjectRule($broker);
	}

	public function testSmartObjectChild()
	{
		$scope = $this->createMock(Scope::class);
		$node = $this->createMock(Node\Stmt\Class_::class);
		$node->namespacedName = new Node\Name('PHPStan\Tests\SmartObjectChild');

		$result = $this->rule->processNode($node, $scope);

		$this->assertEmpty($result);
	}

	public function testNetteObjectChild()
	{
		if (PHP_VERSION_ID >= 70200) {
			$this->markTestSkipped('PHP 7.2 is incompatible with Nette\Object.');
		}
		$scope = $this->createMock(Scope::class);
		$node = $this->createMock(Node\Stmt\Class_::class);
		$node->namespacedName = new Node\Name('PHPStan\Tests\NetteObjectChild');

		$result = $this->rule->processNode($node, $scope);

		$this->assertSame(['Class PHPStan\Tests\NetteObjectChild extends Nette\Object - it\'s better to use Nette\SmartObject trait.'], $result);
	}

}
