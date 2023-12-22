<?php declare(strict_types = 1);

namespace PHPStan\Nette;

use Nette\Application\UI\Component;
use Nette\Application\UI\Presenter;
use PHPStan\Analyser\Scope;
use PHPStan\Exceptions\InvalidLinkDestinationException;
use PHPStan\Exceptions\InvalidLinkException;
use PHPStan\Exceptions\InvalidLinkParamsException;
use PHPStan\Exceptions\LinkCheckFailedException;
use PHPStan\Exceptions\PresenterResolvingException;
use PHPStan\Exceptions\PresenterResolvingNotAvailableException;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\ArrayType;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\MixedType;
use PHPStan\Type\Type;
use PHPStan\Type\VerbosityLevel;
use Throwable;
use function array_key_exists;
use function array_merge;
use function array_values;
use function dirname;
use function explode;
use function get_class;
use function is_array;
use function is_callable;
use function is_dir;
use function is_file;
use function ltrim;
use function parse_str;
use function preg_match;
use function sprintf;
use function strpos;
use function strrpos;
use function substr;
use function ucfirst;

class LinkChecker
{

	/** @var PresenterResolver */
	private $presenterResolver;

	/** @var ReflectionProvider */
	private $reflectionProvider;

	public function __construct(PresenterResolver $presenterResolver, ReflectionProvider $reflectionProvider)
	{
		$this->presenterResolver = $presenterResolver;
		$this->reflectionProvider = $reflectionProvider;
	}

	/**
	 * @param array<string|null> $currentClasses
	 * @param array<string> $destinations
	 * @param array<array<Type>|null> $paramsOptions
	 * @return array<int, RuleError>
	 */
	public function checkLinkVariants(Scope $scope, array $currentClasses, string $methodName, array $destinations, array $paramsOptions = [null]): array
	{
		if ($paramsOptions === []) {
			$paramsOptions = [null];
		}

		$errors = [];
		foreach ($destinations as $destination) {
			foreach ($paramsOptions as $params) {
				foreach ($currentClasses as $currentClass) {
					$errors = array_merge($errors, $this->checkLinkError($scope, $currentClass, $methodName, $destination, $params));
				}
			}
		}
		return array_values($errors);
	}

	/**
	 * @param array<int|string, Type>|null $params
	 * @return array<int, RuleError>
	 */
	public function checkLink(Scope $scope, ?string $currentClass, string $methodName, string $destination, ?array $params = null): array
	{
		return array_values($this->checkLinkError($scope, $currentClass, $methodName, $destination, $params));
	}

	/**
	 * @param array<int|string, Type>|null $params
	 * @return array<string, RuleError>
	 */
	public function checkLinkError(Scope $scope, ?string $currentClass, string $methodName, string $destination, ?array $params = null): array
	{
		try {
			$this->validateLink($scope, $currentClass, $destination, $params);
			return [];
		} catch (PresenterResolvingNotAvailableException $e) {
			return []; // link checking not enabled
		} catch (InvalidLinkDestinationException $e) {
			$message = sprintf("Invalid link destination '%s' in %s() call", $e->getDestination(), $methodName) .
				($e->getPrevious() !== null ? ': ' . $e->getPrevious()->getMessage() : '.');
			$identifier = 'nette.invalidLink.destination';
		} catch (InvalidLinkParamsException $e) {
			$message = sprintf('Invalid link params in %s() call: ', $methodName) . $e->getMessage();
			$identifier = 'nette.invalidLink.params';
		} catch (InvalidLinkException $e) {
			$message = sprintf('Invalid link in %s() call: ', $methodName) . $e->getMessage();
			$identifier = 'nette.invalidLink.error';
		} catch (LinkCheckFailedException $e) {
			$message = sprintf('Link check failed: ' . $e->getMessage());
			$identifier = 'nette.invalidLink.checkFailed';
		} catch (PresenterResolvingException $e) {
			$message = sprintf('Link check failed: ' . $e->getMessage());
			$identifier = 'nette.invalidLink.presenterResolvingFailed';
		} catch (Throwable $e) {
			$message = sprintf('Link check failed: %s: %s in %s on line %s', get_class($e), $e->getMessage(), $e->getFile(), $e->getLine());
			$identifier = 'nette.invalidLink.unexpectedError';
		}
		return [$message => RuleErrorBuilder::message($message)->identifier($identifier)->build()];
	}

	/**
	 * @param array<int|string, Type>|null $params
	 */
	private function validateLink(Scope $scope, ?string $currentClass, string $destination, ?array $params): void
	{
		if ($currentClass !== null) {
			$reflection = $this->reflectionProvider->getClass($currentClass);
			$isComponent = $reflection->is(Component::class);
			$isPresenter = $reflection->is(Presenter::class);
			$isLinkGenerator = false;
		} else {
			$isComponent = false;
			$isPresenter = false;
			$isLinkGenerator = true;
		}

		if ($isLinkGenerator && preg_match('~^([\w:]+):(\w*+)(#.*)?()$~D', $destination) !== 1) {
			throw new InvalidLinkDestinationException($destination);
		}

		if (preg_match('~^ (?<absolute>//)?+ (?<path>[^!?#]++) (?<signal>!)?+ (?<query>\?[^#]*)?+ (?<fragment>\#.*)?+ $~x', $destination, $matches) !== 1) {
			throw new InvalidLinkDestinationException($destination);
		}

		$path = $matches['path'] ?? '';
		$signal = $matches['signal'] ?? '';
		$query = $matches['query'] ?? '';

		if ($query !== '') {
			parse_str(substr($query, 1), $queryParams);
			$queryParamsTypes = [];
			foreach ($queryParams as $key => $value) {
				if (is_array($value)) {
					$queryParamsTypes[$key] = new ArrayType(new MixedType(), new MixedType());
				} else {
					$queryParamsTypes[$key] = new ConstantStringType($value);
				}
			}
			$params = $params !== null ? array_merge($queryParamsTypes, $params) : null;
		}

		if (($isComponent && !$isPresenter) || $signal !== '') {
			$pathSepPos = strrpos($path, ':');
			if ($pathSepPos !== false) {
				$signal = substr($path, $pathSepPos + 1);
			} else {
				$signal = $path;
			}
			if ($signal === '' || $signal === false) {
				throw new InvalidLinkDestinationException($destination, 0, new InvalidLinkException('Signal must be non-empty string.'));
			}
			$path = 'this';
		}

		$pathSepPos = strrpos($path, ':');
		if ($pathSepPos !== false && $pathSepPos !== 0) {
			$presenter = substr($path, 0, $pathSepPos);
			$action = substr($path, $pathSepPos + 1);
		} else {
			$presenter = 'this';
			$action = $path;
		}

		if ($isLinkGenerator) {
			if ($presenter[0] === ':') {
				throw new InvalidLinkDestinationException($destination, 0, new InvalidLinkException('Do not use absolute destinations with LinkGenerator.'));
			}
			$presenter = ':' . $presenter;
		}

		if ($currentClass !== null) {
			$currentClassReflection = $this->reflectionProvider->getClass($currentClass);

			if ($currentClassReflection->isAbstract() && $presenter !== 'this' && $presenter[0] !== ':') {
				throw new LinkCheckFailedException(sprintf('Cannot analyse relative destination \'%s\' for abstract class %s.', $destination, $currentClass));
			}
		}

		if ($action !== '' && $action !== '*' && preg_match('/^[a-zA-Z0-9_]+$/', $action) !== 1) {
			throw new InvalidLinkDestinationException($destination);
		}

		if ($signal !== '' && preg_match('/^[a-zA-Z0-9_-]+$/', $signal) !== 1) {
			throw new InvalidLinkDestinationException($destination);
		}

		if ($action === '') {
			$action = 'default';
		}

		if ($isComponent && !$isPresenter) {
			$targetClass = $currentClass;
		} else {
			try {
				$targetClass = $this->presenterResolver->getPresenterClassByName($presenter, $isPresenter ? $currentClass : null);
			} catch (PresenterResolvingException $e) {
				throw new InvalidLinkDestinationException($destination, 0, $e);
			}
		}

		if ($targetClass === null) {
			return;
		}

		if ($signal !== '') {
			try {
				$this->validateSignal($scope, $targetClass, $signal, $params);
			} catch (InvalidLinkDestinationException $e) {
				throw new InvalidLinkDestinationException($destination, 0, $e->getPrevious());
			}
		}

		$targetClassReflection = $this->reflectionProvider->getClass($targetClass);

		if ($params === null || $action === '*' || $action === 'this' || !$targetClassReflection->isSubclassOf(Presenter::class)) {
			return;
		}

		$hasCustomFormatActionMethod = $targetClassReflection->hasMethod('formatActionMethod') &&
			$targetClassReflection->getMethod('formatActionMethod', $scope)->getDeclaringClass()->getName() !== Presenter::class;
		$actionMethod = $hasCustomFormatActionMethod && is_callable([$targetClass, 'formatActionMethod']) ? $targetClass::formatActionMethod($action) : 'action' . ucfirst($action);

		$hasCustomFormatRenderMethod = $targetClassReflection->hasMethod('formatRenderMethod') &&
			$targetClassReflection->getMethod('formatActionMethod', $scope)->getDeclaringClass()->getName() !== Presenter::class;
		$renderMethod = $hasCustomFormatRenderMethod && is_callable([$targetClass, 'formatRenderMethod']) ? $targetClass::formatRenderMethod($action) : 'render' . ucfirst($action);

		if ($targetClassReflection->hasMethod($actionMethod)) {
			$this->validateParams($scope, $targetClass, $actionMethod, $params);
			//detect deprecated?
		} elseif ($targetClassReflection->hasMethod($renderMethod)) {
			$this->validateParams($scope, $targetClass, $renderMethod, $params);
			//detect deprecated?
		} elseif (array_key_exists(0, $params)) {
			throw new InvalidLinkParamsException(sprintf("Unable to pass parameters to action '%s:%s', missing corresponding method in %s.", $presenter, $action, $targetClass));
		} else {
			$targetClassFileName = $targetClassReflection->getFileName();
			if ($targetClassFileName === null) {
				return;
			}
			$targetClassDir = dirname($targetClassFileName);
			$templateDir = is_dir($targetClassDir . '/templates') ? $targetClassDir : dirname($targetClassDir);
			$templates = [
				sprintf('%s/templates/%s/%s.latte', $templateDir, ltrim($presenter, ':'), $action),
				sprintf('%s/templates/%s.%s.latte', $templateDir, ltrim($presenter, ':'), $action),
			];
			foreach ($templates as $template) {
				if (is_file($template)) {
					return;
				}
			}
			throw new InvalidLinkDestinationException(
				$destination,
				0,
				new InvalidLinkException(sprintf("Action '%s:%s' does not exists, missing corresponding template or method in %s.", $presenter, $action, $targetClass))
			);
		}
	}

	/**
	 * @param array<int|string, Type> $params
	 */
	private function validateParams(
		Scope $scope,
		string $class,
		string $method,
		array &$params
	): void
	{
		$i = 0;
		$rm = $this->reflectionProvider->getClass($class)->getMethod($method, $scope);
		$declaringClass = $rm->getDeclaringClass()->getName();

		$selectedVariant = ParametersAcceptorSelector::selectFromTypes($params, $rm->getVariants(), false);

		foreach ($selectedVariant->getParameters() as $param) {
			$expectedType = $param->getType();
			$name = $param->getName();

			if (array_key_exists($i, $params)) {
				$params[$name] = $params[$i];
				unset($params[$i]);
				$i++;
			}

			if (!isset($params[$name])) {
				if (
					$param->getDefaultValue() === null
					&& $expectedType->isNull()->no()
					&& $expectedType->isScalar()->no()
					&& $expectedType->isArray()->no()
					&& $expectedType->isIterable()->no()
				) {
					throw new InvalidLinkParamsException(sprintf('Missing parameter $%s required by %s::%s()', $param->getName(), $declaringClass, $method));
				}
				continue;
			}

			$actualType = $params[$name];
			if ($expectedType->accepts($actualType, false)->no()) {
				throw new InvalidLinkParamsException(sprintf(
					'Argument $%s passed to %s() must be %s, %s given.',
					$name,
					$declaringClass . '::' . $method,
					$expectedType->describe(VerbosityLevel::precise()),
					$actualType->describe(VerbosityLevel::precise())
				));
			}
		}

		if (array_key_exists($i, $params)) {
			throw new InvalidLinkParamsException(sprintf('Passed more parameters than method %s::%s() expects.', $declaringClass, $method));
		}
	}

	/**
	 * @param array<int|string, Type> $params
	 */
	private function validateSignal(
		Scope $scope,
		string $targetClass,
		string $signal,
		?array $params
	): void
	{
		$targetClassReflection = $this->reflectionProvider->getClass($targetClass);
		if ($signal === 'this') { // means "no signal"
			if ($params !== null && array_key_exists(0, $params)) {
				throw new InvalidLinkParamsException("Unable to pass parameters to 'this!' signal.");
			}
		} elseif (strpos($signal, '-') === false) {
			$hasCustomFormatActionMethod = $targetClassReflection->hasMethod('formatSignalMethod') &&
				$targetClassReflection->getMethod('formatSignalMethod', $scope)->getDeclaringClass()->getName() !== Component::class;
			$signalMethod = $hasCustomFormatActionMethod && is_callable([$targetClass, 'formatSignalMethod']) ? $targetClass::formatSignalMethod($signal) : 'handle' . ucfirst($signal);
			if (!$targetClassReflection->hasMethod($signalMethod)) {
				throw new InvalidLinkDestinationException(
					$signal,
					0,
					new InvalidLinkException(sprintf("Unknown signal '%s', missing handler %s::%s()", $signal, $targetClass, $signalMethod))
				);
			}
			$this->validateParams($scope, $targetClass, $signalMethod, $params);
			//detect deprecated?
		} else {
			[$componentName, $componentSignal] = explode('-', $signal, 2);
			$subComponentMethodName = 'createComponent' . ucfirst($componentName);
			if (!$targetClassReflection->hasMethod($subComponentMethodName)) {
				throw new InvalidLinkDestinationException(
					$signal,
					0,
					new InvalidLinkException(sprintf(
						"Sub-component '%s' might not exists. Method %s::%s() not found.",
						$componentName,
						$targetClass,
						$subComponentMethodName
					))
				);
			}

			$subComponentMethod = $targetClassReflection->getMethod($subComponentMethodName, $scope);
			$subComponentType = ParametersAcceptorSelector::selectSingle($subComponentMethod->getVariants())->getReturnType();
			foreach ($subComponentType->getReferencedClasses() as $componentClass) {
				$subComponentClassReflection = $this->reflectionProvider->getClass($componentClass);
				if (!$subComponentClassReflection->isSubclassOf(Component::class)) {
					continue;
				}

				$this->validateLink($scope, $componentClass, $componentSignal, $params);
			}
		}
	}

}
